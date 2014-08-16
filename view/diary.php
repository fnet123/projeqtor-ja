<?php 
/* ============================================================================
 * Presents an object. 
 */
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/diary.php');  
  
  $cpt=0;
  $arrayActivities=array(); // Array of activities to display
  $idRessource=$_SESSION['user']->id;
  if (! isset($period)) {
  	$period=htmlentities($_REQUEST['diaryPeriod']);
    $year=htmlentities($_REQUEST['diaryYear']);
    $month=htmlentities($_REQUEST['diaryMonth']);
    $week=htmlentities($_REQUEST['diaryWeek']);
    $day=htmlentities($_REQUEST['diaryDay']);
    Parameter::storeUserParameter("diaryPeriod",$period);
    $idRessource=$_REQUEST['diaryResource'];
  }
  $ress=new Resource($idRessource);
  $calendar=$ress->idCalendarDefinition;
  $weekDaysCaption=array(
  		1=>i18n("Monday"),
  		2=>i18n("Tuesday"),
  		3=>i18n("Wednesday"),
  		4=>i18n("Thursday"),
  		5=>i18n("Friday"),
  		6=>i18n("Saturday"),
  		7=>i18n("Sunday"),
  );
  $projectColorArray=array();
  $projectNameArray=array();
  if ($period=="month") {
  	$trHeight=20;
  	$week=weekNumber($year.'-'.$month.'-01');
  	$lastWeek=weekNumber($year.'-'.$month.'-'.date('t', mktime(0, 0, 0, $month, 1, $year)));
  	$trHeight=round(100/($lastWeek-$week+1))-1;
  } else {
  	$trHeight=100;
  }

  if ($period=="month") {
  	$currentDay=date('Y-m-d',firstDayofWeek($week,$year));
  	$lastDayOfMonth=date('t',strtotime($year.'-'.$month.'-01'));
  	$weekOfLastDayOfMonth=date('W',strtotime($year.'-'.$month.'-'.$lastDayOfMonth));
  	$firstDayOfLastWeek=date('Y-m-d',firstDayofWeek($weekOfLastDayOfMonth, (($lastWeek>$week)?$year:$year+1) ));	
  	$endDay=addDaysToDate($firstDayOfLastWeek, 6);
  	$inScopeDay=false;
  } else if ($period=="week") {
  	$currentDay=date('Y-m-d',firstDayofWeek($week,$year));
  	$endDay=addDaysToDate($currentDay, 6);
  	$inScopeDay=true;
  } else if ($period=="day") {
  	$currentDay=$day;
  	$endDay=$currentDay;
  	$inScopeDay=true;
  }
  echo '<TABLE style="width:100%; height: 100%;">';
  
  if ($period!='day') {
    echo '<tr height="10px"><td></td>';
    for ($i=1; $i<=7;$i++) {
  	  echo '<td class="section" style="width: 14%;">'.$weekDaysCaption[$i].'</td>';
    }
  } else {
  	echo '<tr height="0px"><td></td>';
  }
  $arrayActivities=getAllActivities($currentDay, $endDay, $idRessource);
  drawDiaryLineHeader($currentDay, $trHeight,$period); 
  while ($currentDay<=$endDay) {
  	if ($period=="month") {
  		if (substr($currentDay,5,2)==$month) {
  			$inScopeDay=true;
  		} else {
  			$inScopeDay=false;
  		}
  	}
  	echo '<td style="width: '.(($period=='day')?'100':'14').'%; border: 1px solid #AAAAAA;background-color:'.(($inScopeDay)?'white':'transparent').'">';
  	drawDay($currentDay,$idRessource,$inScopeDay,$period,$calendar); 
  	$currentDay=addDaysToDate($currentDay, 1);
  	if ($currentDay<=$endDay and date('N', strtotime($currentDay))==1) {
      drawDiaryLineHeader($currentDay, $trHeight,$period);
  	}
  }
  echo '</tr></TABLE>';
  
function drawDay($date,$ress,$inScopeDay,$period,$calendar=1) {
	global $cpt;
	echo '<table style="width:100%; height: 100%;'.(($date==date('Y-m-d'))?'border:0px solid #555555;':'').'">';
	if ($period!='day') {
		echo '<tr style="height:10px">';
		echo '<td class="report'.(($date==date('Y-m-d'))?'Table':'').'Header" style="padding:0;cursor: pointer;'.((!$inScopeDay)?'color:#AAAAAA':'').'"';
		echo ' onClick="diaryDay(\''.$date.'\');" >';
		//echo $date.'/';
		echo substr($date,8,2);
		echo '</td>';
		echo '</tr>';
	}
	echo '<tr >';
	$bgColor="#FFFFFF";
	if ($date==date('Y-m-d')) { 
		$bgColor="#ffffaa"; 
	} else if (isOffDay($date,$calendar)) {
		$bgColor="#dfdfdf";
	}
	
	echo '<td style="vertical-align:top;background-color:'.$bgColor.';">';
	echo '<div style="overflow-y: auto; overflow-y:none; height:100%;">';
	echo '<table style="width:100%">';
	$lst=getActivity($date);
	foreach ($lst as $item) {
		$cpt++;
		$hint=i18n($item['type']).' #'.$item['id']."\n"
				.$item['name']."\n"
				.i18n('colIdProject').": ".$item['project'];
		$hintHtml=i18n($item['type']).' #'.$item['id']."<br/>"
				.'<b>'.$item['name']."</b><br/>"
				.i18n('colIdProject').": <i>".$item['project'].'</i><br/>';
		if ($item['date']) { $hintHtml.=i18n('colDate').": <i>".$item['date']."</i>"; }
		if ($item['work'] and $item['real']) { $hintHtml.=i18n('colRealWork').": ".Work::displayWorkWithUnit($item['work']).""; }
		if ($item['work'] and ! $item['real']) { $hintHtml.=i18n('colPlannedWork').": <i>".Work::displayWorkWithUnit($item['work'])."</i>"; }
		echo '<tr>';
		echo '<td style="padding: 3px 3px 0px 3px; width:100%">';
		echo '<div id="item_'.$cpt.'" title="'.$hint.'" style="border:1px solid: #EEEEEE; box-shadow: 2px 2px 4px #AAAAAA; width: 100%;background-color:'.$item['color'].'">';
		echo '<table><tr><td>';
		$attr=((! $item['real'])?'':' style="opacity:0.5;filter:alpha(opacity=50);"');
		echo '<img src="../view/css/images/icon'.$item['type'].'16.png"'.$attr.'/></td><td style="width:1px">';
		echo '</td><td style="color:#555555">';
		echo '<div style="cursor:pointer;color:'.getForeColor($item['color']).'" onClick="gotoElement(\''.$item['type'].'\', '.$item['id'].', false);" >';
		if ($item['real']) {
		  echo $item['display'];
		} else {
			echo '<i>'.$item['display'].'</i>';
		}
		echo '</div>';
		echo '</td></tr></table>';
		echo '</div>';
		// To display a tooltip in replacement of Hint
		echo '<div dojoType="dijit.Tooltip" connectId="item_'.$cpt.'" position="above">';
		echo $hintHtml;
		echo '</div>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';	
	echo '</div>';
	echo '</td>';		
	echo '</tr>';
	echo '</table>';
}

function getActivity($date) {
	global $arrayActivities;
	if (array_key_exists($date, $arrayActivities)) {
		return $arrayActivities[$date];
	} else {
		return array();
	}
}

function getAllActivities($startDate, $endDate, $ress) {
	global $projectColorArray, $projectNameArray, $allActi;
	$result=array();
	// 
	$arrObj=array(new Action(), new Ticket(), new MilestonePlanningElement());
	foreach ($arrObj as $obj) {
		$critWhere="done=0 and idResource=".Sql::fmtId($ress);
		if (property_exists($obj, 'actualDueDate') and property_exists($obj, 'initialDueDate')) {
		  $critWhere.=" and ( "
		   ." (actualDueDate>='$startDate' and actualDueDate<='$endDate') "
		   ." or ( actualDueDate is null and (initialDueDate>='$startDate' and initialDueDate<='$endDate') )"
		   ." )";
	  } else if (property_exists($obj, 'actualDueDateTime') and property_exists($obj, 'initialDueDateTime')) {
		  $critWhere.=" and ( "
		   ." (actualDueDateTime>='$startDate 00:00:00' and actualDueDateTime<='$endDate 23:59:59') "
		   ." or ( actualDueDateTime is null and (initialDueDateTime>='$startDate 00:00:00' and initialDueDateTime<='$endDate 23:59:59') )"
	     ." )";
		} else if (property_exists($obj, 'validatedEndDate') ) {
			$critWhere=" refType='MileStone' and validatedEndDate>='$startDate' and validatedEndDate<='$endDate'";
			$critWhere.=" and idProject in ".transformListIntoInClause($_SESSION['user']->getVisibleProjects(true));
	  } else {
	  	$critWhere.=" and 1=0";
	  }
		$lst=$obj->getSqlElementsFromCriteria(null,false,$critWhere);
		foreach ($lst as $o) {
			if (array_key_exists($o->idProject,$projectColorArray)) {
				$color=$projectColorArray[$o->idProject];
				$projName=$projectNameArray[$o->idProject];
			} else {
				$pro=new Project($o->idProject);
				$color=$pro->color;
				$projName=$pro->name;
				$projectColorArray[$o->idProject]=$color;
				$projectNameArray[$o->idProject]=$projName;
			}
			$date=null;
			$dateField="";
			$name="";
			$id=$o->id;
			$class=get_class($o);
			if (property_exists($obj, 'actualDueDate') and property_exists($obj, 'initialDueDate')) {
				if ($o->actualDueDate) {
					$date=$o->actualDueDate;
					$dateField=i18n('colActualDueDate');
				} else {
					$date=$o->initialDueDate;
					$dateField=i18n('colInitialDueDate');
				}
				$name=$o->name;
			} else if (property_exists($obj, 'actualDueDateTime') and property_exists($obj, 'initialDueDateTime')) {
				if ($o->actualDueDateTime) {
					$date=substr($o->actualDueDateTime,0,10);
					$dateField=i18n('colActualDueDate');
				} else {
					$date=substr($o->initialDueDateTime,0,10);
					$dateField=i18n('colInitialDueDate');
				}
				$name=$o->name;
			} else if (property_exists($obj, 'validatedEndDate')) {
				$name=$o->refName;
				$id=$o->refId;
				$class=$o->refType;
				$date=$o->validatedEndDate;
				$dateField=i18n('colValidatedEndDate');
			}
			if ($date) {
				if (!array_key_exists($date, $result)) {
					$result[$date]=array();
				}				
				$result[$date][get_class($o).'#'.$o->id]=array(
						'type'=>$class,
						'id'=>$id,
						'work'=>0,
						'name'=>$name,
						'color'=>$color,
						'display'=>$name,
						'date'=>$dateField,
						'project'=>$projName,
						'real'=>false
				);
			}		
		}
	}
	// Planned Activities and real work
	$pw=new PlannedWork();
	$w=new Work();
	$critWhere="idResource=".Sql::fmtId($ress);
	$critWhere.=" and workDate>='$startDate' and workDate<='$endDate'";
	$pwList=$pw->getSqlElementsFromCriteria(null,false,$critWhere);
	$wList=$w->getSqlElementsFromCriteria(null,false,$critWhere);
	$workList=array_merge($pwList,$wList);
	foreach ($workList as $pw) {
		$item=new $pw->refType($pw->refId);
		if ($pw->refType=='Meeting') {
			$display=substr($item->meetingStartTime,0,5).' - '.htmlEncode($item->name);
		} else if (get_class($pw)=='Work') {
				$display='['.Work::displayWorkWithUnit($pw->work).'] '.htmlEncode($item->name);
		} else {
		  $display='<i>('.Work::displayWorkWithUnit($pw->work).')</i> '.htmlEncode($item->name);
		}
		if (array_key_exists($item->idProject,$projectColorArray)) {
			$color=$projectColorArray[$item->idProject];
			$projName=$projectNameArray[$item->idProject];
		} else {
			$pro=new Project($item->idProject);
			$color=$pro->color;
			$projName=$pro->name;
			$projectColorArray[$item->idProject]=$color;
			$projectNameArray[$item->idProject]=$projName;
		}
		$date=$pw->workDate;
		if (!array_key_exists($date, $result)) {
			$result[$date]=array();
		}
		$result[$date][$pw->refType.'#'.$pw->refId]=array(
				'type'=>$pw->refType,
		    'id'=>$pw->refId,
				'work'=>$pw->work,
				'name'=>$item->name,
				'color'=>$color,
				'display'=>$display,
				'project'=>$projName,
				'date'=>"",
				'real'=>((get_class($pw)=='Work')?true:false)
		);
	}
	return $result;
}

function drawDiaryLineHeader($currentDay, $trHeight,$period) {
	echo '</tr>';
	echo '<tr height="'.$trHeight.'%"><td class="buttonDiary" ';
	if ($period=="month") {
	  echo 'onClick="diaryWeek('.weekNumber($currentDay).','.substr($currentDay,0,4).');"';
	} else if ($period=="week") {
		echo 'onClick="diaryMonth('.substr($currentDay,5,2).','.substr($currentDay,0,4).');"';
	} else if ($period=="day") {
		echo 'onClick="diaryWeek('.weekNumber($currentDay).','.substr($currentDay,0,4).');"';
	}	
	echo '>';
	if ($period=='week') {
		$month=substr($currentDay,5,2);
		$monthArr=array(i18n("January"),i18n("February"),i18n("March"),
				i18n("April"), i18n("May"),i18n("June"),
				i18n("July"), i18n("August"), i18n("September"),
				i18n("October"),i18n("November"),i18n("December"));
		$dispMonth=(mb_strlen($monthArr[$month-1],'UTF-8')>4)?mb_substr($monthArr[$month-1],0,4,'UTF-8').'.':$monthArr[$month-1];
		echo '<div style="font-size:80%">'.$dispMonth.'</div>';
	} else {
	  echo '<div >'.weekNumber($currentDay).'</div>';
	}
	if ($period=="month") {
		echo '<img src="../view/css/images/right.png" /></td>';
	} else {
		echo '<img src="../view/css/images/left.png" /></td>';
	}
}
?>

