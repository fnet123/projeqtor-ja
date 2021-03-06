<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2014 Pascal BERNARD - support@projeqtor.org
 * Contributors : -
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

/** ===========================================================================
 * Run planning
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/planSaveDates.php');
if (! array_key_exists('idProjectPlanSaveDates',$_REQUEST)) {
  throwError('idProjectPlanSaveDates parameter not found in REQUEST');
}
$idProjectPlan=$_REQUEST['idProjectPlanSaveDates'];
if (! array_key_exists('updateValidatedDates',$_REQUEST)) {
  throwError('updateValidatedDates parameter not found in REQUEST');
}
$updateValidatedDates=$_REQUEST['updateValidatedDates'];
if (! array_key_exists('updateInitialDates',$_REQUEST)) {
	throwError('updateInitialDates parameter not found in REQUEST');
}
$updateInitialDates=$_REQUEST['updateInitialDates'];

projeqtor_set_time_limit(600);
Sql::beginTransaction();
$result=PlannedWork::planSaveDates($idProjectPlan, $updateInitialDates, $updateValidatedDates);

// Message of correct saving
if (stripos($result,'id="lastPlanStatus" value="ERROR"')>0 ) {
	Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
} else if (stripos($result,'id="lastPlanStatus" value="OK"')>0 ) {
	Sql::commitTransaction();
  echo '<span class="messageOK" >' . $result . '</span>';
} else { 
	Sql::commitTransaction();
  echo '<span class="messageWARNING" >' . $result . '</span>';
}
?>