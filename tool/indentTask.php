<?php
/** ===========================================================================
 * Move task (from before to)
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/indentTask.php');
if (! array_key_exists('objectClass',$_REQUEST)) {
  throwError('objectClass parameter not found in REQUEST');
}
$objectClass=$_REQUEST['objectClass'];

if (! array_key_exists('objectId',$_REQUEST)) {
  throwError('objectId parameter not found in REQUEST');
}
$objectId=$_REQUEST['objectId'];

if (! array_key_exists('way',$_REQUEST)) {
  throwError('way parameter not found in REQUEST');
}
$way=$_REQUEST['way'];
if ($way!='increase' and $way!='decrease') {
  $way='increase';
}

$result="";
Sql::beginTransaction();
$pe=SqlElement::getSingleSqlElementFromCriteria('PlanningElement', array('refType'=>$objectClass,'refId'=>$objectId));
if ($pe and $pe->id) {
	$result=$pe->indent($way);
} else {
	$result=i18n('moveCancelled');
	$result .= '<input type="hidden" id="lastOperation" value="move" />';
	$result .= '<input type="hidden" id="lastOperationStatus" value="ERROR" />';
	$result .= '<input type="hidden" id="lastPlanStatus" value="KO" />';
}

if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {
	Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
} else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
	Sql::commitTransaction();
  echo '<span class="messageOK" >' . $result . '</span>';
} else { 
	Sql::commitTransaction();
  echo '<span class="messageWARNING" >' . $result . '</span>';
}
?>