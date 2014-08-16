<?php
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