<?php
/** ===========================================================================
 * Save a checklistdefinition line : call corresponding method in SqlElement Class
 * The new values are fetched in $_REQUEST
 */
require_once "../tool/projeqtor.php";
if (! array_key_exists('workflowId',$_REQUEST)) {
	throwError('workflowId parameter not found in REQUEST');
}
$workflowId=trim($_REQUEST['workflowId']);

$statusList=SqlList::getList('Status');
Sql::beginTransaction();
$result="";
foreach($statusList as $idStatus=>$status) {
	$critArray=array('scope'=>'workflow', 'objectClass'=>'workflow#'.$workflowId, 'idUser'=>$idStatus);
	$cs=SqlElement::getSingleSqlElementFromCriteria("ColumnSelector", $critArray);
	if ($cs and $cs->id) {
		// OK
	} else {
		$cs->scope='workflow';
		$cs->objectClass='workflow#'.$workflowId;
		$cs->idUser=$idStatus;
		$cs->field=$status;
		$cs->attribute=$status;
		$cs->name='workflow#'.$workflowId.' status#'.$idStatus;
	}
	if (array_key_exists('dialogWorkflowParameterCheckStatusId_'.$idStatus,$_REQUEST) ) {
		$cs->hidden=0;
	} else {
		$cs->hidden=1;
	}
	$resultLine=$cs->save();
	if (! $result or stripos($resultLine,'id="lastOperationStatus" value="ERROR"')>0) {
	 	$result=$resultLine;
	}
}

if (! stripos($result,'id="lastOperationStatus" value="ERROR"')>0) {
  $result=i18n('Workflow') . ' #'. $workflowId . ' ' . i18n('resultUpdated');
  $result .= '<input type="hidden" id="lastSaveId" value="' . $workflowId . '" />';
  $result .= '<input type="hidden" id="lastOperation" value="update" />';
  $result .= '<input type="hidden" id="lastOperationStatus" value="OK" />';
}
// Message of correct saving
if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {
	Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
} else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
	Sql::commitTransaction();
  echo '<span class="messageOK" >' . $result . '</span>';
} else { 
	Sql::rollbackTransaction();
  echo '<span class="messageWARNING" >' . $result . '</span>';
}
?>