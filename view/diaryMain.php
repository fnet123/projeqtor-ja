<?php 
/* ============================================================================
 * Presents an object. 
 */
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/diaryMain.php');  
  $user=$_SESSION['user'];
?>
<input type="hidden" name="objectClassManual" id="objectClassManual" value="Diary" />
<div class="container" dojoType="dijit.layout.BorderContainer">
  <div id="listDiv" dojoType="dijit.layout.ContentPane" region="top" class="listTitle" splitter="false" style="height:58px;">
  <table width="100%" height="27px" class="listTitle" >
    <tr height="17px">
      <td width="50px" align="center">
        <img src="css/images/iconDiary32.png" width="32" height="32" />
      </td>
      <td width="200px" ><span class="title"><?php echo i18n('menuDiary');?></span></td>
      <td style="text-align: center"> 
		   <?php 
		   $period=Parameter::getUserParameter("diaryPeriod");
		   if (!$period) {$period="month";}
		   $year=date('Y');
		   $month=date('m');
		   $week=date('W');
		   $day=date('Y-m-d');
		   echo '<div style="font-size:20px" id="diaryCaption">';
		   if ($period=='month') {
		     echo i18n(date("F",mktime(0,0,0,$month,1,$year))).' '.$year;
		   } else if ($period=='week') {
         $firstday=date('Y-m-d',firstDayofWeek($week, $year));
         $lastday=addDaysToDate($firstday, 6);
         echo $year.' #'.$week." (".htmlFormatDate($firstday)." - ".htmlFormatDate($lastday).")";
       } else if ($period=='day') {
         $vDayArr = array('', i18n("Monday"),i18n("Tuesday"),i18n("Wednesday"),
		                i18n("Thursday"), i18n("Friday"),i18n("Saturday"),i18n("Sunday"));
         echo $vDayArr[date("N",mktime(0,0,0,$month,date('d'),$year))]." ".htmlFormatDate($day);
       }
       echo "</div>";
		   ?>
		   </td>
		   <td nowrap="nowrap" width="250px" ><form id="diaryForm" name="diaryForm">
		   <input type="hidden" name="diaryPeriod" id="diaryPeriod" value="<?php echo $period;?>" />
		   <input type="hidden" name="diaryYear" id="diaryYear" value="<?php echo $year;?>" />
		   <input type="hidden" name="diaryMonth" id="diaryMonth" value="<?php echo $month;?>" />
		   <input type="hidden" name="diaryWeek" id="diaryWeek" value="<?php echo $week;?>" />
		   <input type="hidden" name="diaryDay" id="diaryDay" value="<?php echo $day;?>" />
		   <?php echo i18n("colIdResource");?> 
		   <select dojoType="dijit.form.FilteringSelect" class="input" style="width: 150px;"
        name="diaryResource" id="diaryResource"
        value="<?php echo ($user->isResource)?$user->id:'0';?>" >
         <script type="dojo/method" event="onChange" >
           loadContent("../view/diary.php","detailDiv","diaryForm");
         </script>
         <?php 
           $crit=array('scope'=>'diary', 'idProfile'=>$user->idProfile);
           $habilitation=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', $crit);
           $scope=new AccessScope($habilitation->rightAccess);
           $table=array();
           if (! $user->isResource) {
             $table[0]=' ';
           }
           if ($scope->accessCode=='NO') {
             $table[$user->id]=' ';
           } else if ($scope->accessCode=='ALL') {
             $table=SqlList::getList('Resource');
           } else if ($scope->accessCode=='OWN' and $user->isResource ) {
             $table=array($user->id=>SqlList::getNameFromId('Resource', $user->id));
           } else if ($scope->accessCode=='PRO') {
             $crit='idProject in ' . transformListIntoInClause($user->getVisibleProjects());
             $aff=new Affectation();
             $lstAff=$aff->getSqlElementsFromCriteria(null, false, $crit, null, true);
             $fullTable=SqlList::getList('Resource');
             foreach ($lstAff as $id=>$aff) {
               if (array_key_exists($aff->idResource,$fullTable)) {
                 $table[$aff->idResource]=$fullTable[$aff->idResource];
               }
             }
           }
           if (count($table)==0) {
             $table[$user->id]=' ';
           }
           foreach($table as $key => $val) {
             echo '<OPTION value="' . $key . '"';
             if ( $key==$user->id ) { echo ' SELECTED '; } 
             echo '>' . $val . '</OPTION>';
           }?>  
       </select>
		   </form> </td>
   	</tr>
   	<tr height="18px" vertical-align="middle">
   	  <td colspan="5">
   	    <table width="100%"><tr><td width="50%;">
   	    <div class="buttonDiary" onClick="diaryPrevious();"><img src="../view/css/images/left.png" /></div>
   	    </td><td style="width:1px"></td><td width="50%">
   	    <div class="buttonDiary" onClick="diaryNext();"><img src="../view/css/images/right.png" /></div>
   	    </td></tr>
   	    </table>
   	  </td>
   	</tr>
   </table>
  </div>
  <div id="detailDiv" dojoType="dijit.layout.ContentPane" region="center">
   <?php include 'diary.php'; ?>
  </div>
</div>