<?php
/** ===========================================================================
 * Move task (from before to)
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/moveTask.php');
if (! array_key_exists('from',$_REQUEST)) {
  throwError('from parameter not found in REQUEST');
}
$from=$_REQUEST['from'];

if (! array_key_exists('to',$_REQUEST)) {
  throwError('to parameter not found in REQUEST');
}
$to=$_REQUEST['to'];

if (! array_key_exists('mode',$_REQUEST)) {
  throwError('mode parameter not found in REQUEST');
}
$mode=$_REQUEST['mode'];
if ($mode!='before' and $mode!='after') {
  $mode='before';
}

$idFrom=substr($from, 6);
$idTo=substr($to, 6);
Sql::beginTransaction();
$task=new PlanningElement($idFrom);
$result=$task->moveTo($idTo,$mode);
//$result.=" " . $idFrom . '->' . $idTo .'(' . $mode . ')';
if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {
	$result=str_replace('<b>'.i18n('messageInvalidControls').'</b><br/><br/>','',$result);
	//$result=str_replace('id="lastPlanStatus" value="OK"','id="lastPlanStatus" value="KO"',$result);
	Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
} else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
	Sql::commitTransaction();
  echo '<span class="messageOK" >' . $result . '</span>';
} else { 	
	//$result=str_replace('id="lastPlanStatus" value="OK"','id="lastPlanStatus" value="KO"',$result);
	Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
}
?>