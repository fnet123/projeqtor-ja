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

//echo "imputation.php";
include_once '../tool/projeqtor.php';

$userId=$_REQUEST['userId'];
$rangeType=$_REQUEST['rangeType'];
$rangeValue=$_REQUEST['rangeValue'];
$idle=false;
if (array_key_exists('idle',$_REQUEST)) {
  $idle=true;
}
$showPlannedWork=false; 
if (array_key_exists('showPlannedWork',$_REQUEST)) {
  $showPlannedWork=true;
}
$hideDone=Parameter::getUserParameter('imputationHideDone');
$hideNotHandled=Parameter::getUserParameter('imputationHideNotHandled');
$displayOnlyCurrentWeekMeetings=Parameter::getUserParameter('imputationDisplayOnlyCurrentWeekMeetings');
if (Parameter::getGlobalParameter('displayOnlyHandled')=="YES") {
	$hideNotHandled=true;
} 
//echo '<div style="height:10px">';
ImputationLine::drawLines($userId, $rangeType, $rangeValue, $idle, $showPlannedWork, true, $hideDone, $hideNotHandled, $displayOnlyCurrentWeekMeetings);

//echo '</div>';
?>