<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2014 Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 * 
 * Most of properties are extracted from Dojo Framework.
 *
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ************************************************/

//echo "availabilitySynthesis.php";

include_once '../tool/projeqtor.php';
$paramPeriodValue='';
if (array_key_exists('periodValue',$_REQUEST)) {
  $paramPeriodValue=$_REQUEST['periodValue'];
};
$paramPeriodScale='';
if (array_key_exists('periodScale',$_REQUEST)) {
  $paramPeriodScale=$_REQUEST['periodScale'];
};
$paramTeam='';
if (array_key_exists('idTeam',$_REQUEST)) {
  $paramTeam=trim($_REQUEST['idTeam']);
}
$user=$_SESSION['user'];

// Header
$headerParameters="";
$headerParameters.= i18n('colPeriod') . ' : ' . $paramPeriodValue . ' ' . i18n($paramPeriodScale) . '<br/>';
if ($paramTeam!="") {
  $headerParameters.= i18n("colIdTeam") . ' : ' . SqlList::getNameFromId('Team', $paramTeam) . '<br/>';
}
include "header.php";

$where=getAccesResctictionClause('Affectation',false);

$resources=array();
$resourceCalendar=array();
$aff=new Affectation();
$affLst=$aff->getSqlElementsFromCriteria(null,false, $where);
foreach($affLst as $aff){
	$ress=new Resource($aff->idResource);
	if ($ress->id and !$ress->idle) {
    $resources[$ress->id]=htmlEncode($ress->name);
    $resourceCalendar[$ress->id]=$ress->idCalendarDefinition;
	}
}

if ($paramPeriodScale=="month") {
	$start=date('Y-m-').'01';
	$startYear=substr($start,0,4);
	$startMonth=substr($start,5,2);
	$startValue=$startYear.$startMonth;
	$end=addMonthsToDate($start, $paramPeriodValue);
	$time=mktime(0, 0, 0, $startMonth, 1, $startYear);
	$end=substr($end,0,8).date("t", $time);
	$endYear=substr($end,0,4);
	$endMonth=substr($end,5,2);
	$endValue=$endYear.$endMonth;
	$where.= " and month>='$startValue' and month<='$endValue'";
} else if ($paramPeriodScale=="week") {
	$start=date('Y-m-d',firstDayofWeek(date('W'), date('Y')));
	$startValue=substr($start,0,4).substr($start,5,2).substr($start,8,2);
	$end=addDaysToDate($start, ($paramPeriodValue*7)-1);
	$endValue=substr($end,0,4).substr($end,5,2).substr($end,8,2);
	$where.= " and day>='$startValue' and day<='$endValue'";
} else {
	echo "(1) ERROR incorrect Period sScale";
	exit;
}
$header=i18n($paramPeriodScale);

$order="";
$work=new Work();
$lstWork=$work->getSqlElementsFromCriteria(null,false, $where, $order);
$result=array();

$capacity=array();
foreach ($resources as $id=>$name) {
	$capacity[$id]=SqlList::getFieldFromId('Resource', $id, 'capacity');
  $result[$id]=array();
}

$real=array();
foreach ($lstWork as $work) {
  if (! array_key_exists($work->idResource,$resources)) {
    $resources[$work->idResource]=SqlList::getNameFromId('Resource', $work->idResource);
    $resourceCalendar[$work->idResource]=SqlList::getFieldFromId('Resource', $work->idResource, 'idCalendarDefinition');
    $capacity[$work->idResource]=SqlList::getFieldFromId('Resource', $work->idResource, 'capacity');
    $result[$work->idResource]=array();
  }
  if (! array_key_exists($work->idResource,$real)) {
  	$real[$work->idResource]=array();
  }
  if (! array_key_exists($work->day,$result[$work->idResource])) {
    $result[$work->idResource][$work->day]=0;
    $real[$work->idResource][$work->day]=true;
  }
  $result[$work->idResource][$work->day]+=$work->work;
}
$planWork=new PlannedWork();
$lstPlanWork=$planWork->getSqlElementsFromCriteria(null,false, $where, $order);
foreach ($lstPlanWork as $work) {
  if (! array_key_exists($work->idResource,$resources)) {
    $resources[$work->idResource]=SqlList::getNameFromId('Resource', $work->idResource);
    $resourceCalendar[$work->idResource]=SqlList::getFieldFromId('Resource', $work->idResource, 'idCalendarDefinition');
    $capacity[$work->idResource]=SqlList::getFieldFromId('Resource', $work->idResource, 'capacity');
    $result[$work->idResource]=array();
  }
  if (! array_key_exists($work->idResource,$real)) {
    $real[$work->idResource]=array();
  }
  if (! array_key_exists($work->day,$result[$work->idResource])) {
    $result[$work->idResource][$work->day]=0;
  }
  //if (! array_key_exists($work->day,$real)) { // Do not add planned if real exists 
    $result[$work->idResource][$work->day]+=$work->work;
  //}
}

$weekendBGColor='#cfcfcf';
$weekendFrontColor='#555555';
$weekendStyle=' style="text-align: center;background-color:' . $weekendBGColor . '; color:' . $weekendFrontColor . '" ';
$plannedBGColor='#FFFFDD';
$plannedFrontColor='#777777';
$plannedStyle=' style="text-align:center;background-color:' . $plannedBGColor . '; color: ' . $plannedFrontColor . ';" ';

// Group data corresponding to periodscale
$resultPeriod=array();
$resultPeriodFmt=array();
for($day=$start;$day<=$end;$day=addDaysToDate($day, 1)) {
	if ($paramPeriodScale=="month") {
		$period=substr($day,0,7);
	} else if ($paramPeriodScale=="week") {
		$period=weekFormat($day);
	} else {
		echo "(2) ERROR incorrect Period sScale";
	  exit; 
	}
	if (! isset($resultPeriod[$period])) {
		$resultPeriod[$period]=array();
		$resultPeriodFmt[$period]=array();
	}
	foreach ($resources as $idR=>$nameR) {
		$capaDay=0;
		if (! isOffDay($day, $resourceCalendar[$idR])) {
			$capaDay=$capacity[$idR];
		}
		if (! isset($resultPeriod[$period][$idR])) {
	    $resultPeriod[$period][$idR]=0;
	    $resultPeriodFmt[$period][$idR]='none';
		}
		$resultPeriod[$period][$idR]+=$capaDay;
		$dayFmt=str_replace('-', '', $day);
		if (isset($result[$idR][$dayFmt])) {
			$resultPeriod[$period][$idR]-=$result[$idR][$dayFmt];
			if (isset($real[$idR][$dayFmt]) and $real[$idR][$dayFmt]==true) {
				$resultPeriodFmt[$period][$idR]='real';
			} else if ($resultPeriodFmt[$period][$idR]=='none') {
				$resultPeriodFmt[$period][$idR]='plan';
			}
		}
	}
}

echo '<table width="95%" align="center">';
echo '<tr><td>';
echo '<table width="100%" align="left">';
echo '<tr>';
echo "<td class='reportTableDataFull' style='width:20px;text-align:center;'>1</td>";
echo "<td width='100px' class='legend'>" . i18n('colRealWork') . "</td>";
echo "<td width='5px'>&nbsp;&nbsp;&nbsp;</td>";
echo '<td class="reportTableDataFull" ' . $plannedStyle . '><i>1</i></td>';
echo "<td width='100px' class='legend'>" . i18n('colPlannedWork') . "</td>";
echo "<td>&nbsp;</td>";
echo "<td class='reportTableDataFull' style='width:20px;text-align:center;color: #00AA00;background-color:#FAFAFA'>1</td>";
echo "<td width='100px' class='legend'>" . i18n('colNoWork') . "</td>";
echo "<td width='5px'>&nbsp;&nbsp;&nbsp;</td>";
echo "<td class='legend'>" . Work::displayWorkUnit() . "</td>";
echo "<td>&nbsp;</td>";
echo "</tr>";
echo "</table>";
echo '</td></tr>';
echo '<tr><td>';
//echo '<br/>';
// title

echo '<table width="100%" align="left"><tr>';
echo '<td class="reportTableHeader" rowspan="2">' . i18n('Resource') . '</td>';
echo '<td class="reportTableHeader" rowspan="2">' . i18n('colCapacity') . '</td>';
echo '<td colspan="' . (count($resultPeriod)+1) . '" class="reportTableHeader">' . $header . '</td>';
echo '</tr><tr>';
foreach($resultPeriod as $idP=>$period) {
  echo '<td class="reportTableColumnHeader">' . $idP . '</td>';
}
echo '<td class="reportTableHeader" style="width:5%">' . i18n('sum') . '</td>';
echo '</tr>';

foreach ($resources as $idR=>$nameR) {
	if ($paramTeam) {
    $res=new Resource($idR);
  }
  if (!$paramTeam or $res->idTeam==$paramTeam) {
		$sum=0;
	  echo '<tr height="20px">';
	  echo '<td class="reportTableLineHeader" style="width:20%">' . $nameR . '</td>';
	  echo '<td class="reportTableLineHeader" style="width:5%;text-align:center;">' . ($capacity[$idR]*1) . '</td>';
	  foreach($resultPeriod as $idP=>$period) {	    
	    $style="";
	    $italic=false;
      $style=' style="text-align:center;';
      $val=$period[$idR];
	    if ($resultPeriodFmt[$idP][$idR]=='plan') {
	      $style.='background-color:' . $plannedBGColor . ';';
	      $italic=true;
	    } else if ($resultPeriodFmt[$idP][$idR]=='real') {
	    	$style.='color: #000000;';
	    } else {
	    	$style.='color: #00AA00;color: #00AA00;background-color:#FAFAFA;';
	    }      	
	    $style.='"';  
	    echo '<td class="reportTableDataFull" ' . $style . ' valign="middle">';    
	    if ($italic) {
	      echo '<i>' . Work::displayWork($val) . '</i>';
	    } else { 
	     	echo Work::displayWork($val);
	    }
	  	echo '</td>';
	  	if ($val>0) {
	  		$sum+=$val;
	  	}
	  }
	  echo '<td class="reportTableColumnHeader" style="width:5%">' . Work::displayWork($sum) . '</td>';
	  echo '</tr>';
  }
}

echo '</table>';

echo '</td></tr></table>';