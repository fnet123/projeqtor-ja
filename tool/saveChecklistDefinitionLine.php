<?php
/** ===========================================================================
 * Save a checklistdefinition line : call corresponding method in SqlElement Class
 * The new values are fetched in $_REQUEST
 */
require_once "../tool/projeqtor.php";
// Get the bill line info
$lineId=null;
if (array_key_exists('checklistDefinitionLineId',$_REQUEST)) {
  $lineId=$_REQUEST['checklistDefinitionLineId'];
}
$checklistDefinitionId=null;
if (array_key_exists('checklistDefinitionId',$_REQUEST)) {
	$checklistDefinitionId=$_REQUEST['checklistDefinitionId'];
}
$lineName=null;
if (array_key_exists('dialogChecklistDefinitionLineName',$_REQUEST)) {
	$lineName=$_REQUEST['dialogChecklistDefinitionLineName'];
}
$lineTitle=null;
if (array_key_exists('dialogChecklistDefinitionLineTitle',$_REQUEST)) {
	$lineTitle=$_REQUEST['dialogChecklistDefinitionLineTitle'];
}
$sortOrder=0;
if (array_key_exists('dialogChecklistDefinitionLineSortOrder',$_REQUEST)) {
	$sortOrder=$_REQUEST['dialogChecklistDefinitionLineSortOrder'];
}
$checkNames=array();
$checkTitles=array();
for ($i=1;$i<=5;$i++) {
	if (array_key_exists('dialogChecklistDefinitionLineChoice_'.$i,$_REQUEST)) {
		$checkName=$_REQUEST['dialogChecklistDefinitionLineChoice_'.$i];
		if (trim($checkName)) {
			$checkNames[]=$checkName;
			if (array_key_exists('dialogChecklistDefinitionLineTitle_'.$i,$_REQUEST)) {		
				$checkTitles[]=$_REQUEST['dialogChecklistDefinitionLineTitle_'.$i];;
			} else {
				$checkTitles[]=null;
			}
		}
	}
}
$exclusive=0;
if (array_key_exists('dialogChecklistDefinitionLineExclusive',$_REQUEST)) {
	$exclusive=1;
}

Sql::beginTransaction();
$line=new ChecklistDefinitionLine($lineId);
$line->idChecklistDefinition=$checklistDefinitionId;
$line->name=$lineName;
$line->title=$lineTitle;
$line->sortOrder=$sortOrder;
for ($i=1;$i<=5;$i++) {
	$check='check0'.$i;
	$title='title0'.$i;
  if (isset($checkNames[$i-1])) {
  	$line->$check=$checkNames[$i-1];
  	if (isset($checkTitles[$i-1])) {
  		$line->$title=$checkTitles[$i-1];
  	} else {
  		$line->$title=null;
  	}
  } else {
  	$line->$check=null;
  	$line->$title=null;
  }
}
$line->exclusive=$exclusive;
$result=$line->save();

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