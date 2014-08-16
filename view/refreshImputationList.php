<?php
/* ============================================================================
 * Presents the list of objects of a given class.
 *
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/view/refrehImputationList.php'); 
$rangeType=$_REQUEST['rangeType'];
$rangeValue=$_REQUEST['rangeValue'];
$userId=$_REQUEST['userId'];
$idle=false;
if (array_key_exists('idle',$_REQUEST)) {
    $idle=$_REQUEST['idle'];
}

$showPlannedWork=0;
if (array_key_exists('showPlannedWork',$_REQUEST)) {
    $showPlannedWork=1;
}
Parameter::storeUserParameter('imputationShowPlannedWork',$showPlannedWork);

$hideDone=0;
if (array_key_exists('hideDone',$_REQUEST)) {
	$hideDone=1;
}
Parameter::storeUserParameter('imputationHideDone',$hideDone);

$hideNotHandled=0;
if (array_key_exists('hideNotHandled',$_REQUEST)) {
	$hideNotHandled=1;
}
Parameter::storeUserParameter('imputationHideNotHandled',$hideNotHandled);

$displayOnlyCurrentWeekMeetings=0;
if (array_key_exists('displayOnlyCurrentWeekMeetings',$_REQUEST)) {
	$displayOnlyCurrentWeekMeetings=1;
}
Parameter::storeUserParameter('imputationDisplayOnlyCurrentWeekMeetings',$displayOnlyCurrentWeekMeetings);
?>
<form dojoType="dijit.form.Form" id="listForm" action="" method="post" >
  <input type="hidden" name="userId" id="userId" value="<?php echo $userId;?>"/>
  <input type="hidden" name="rangeType" id="rangeType" value="<?php echo $rangeType;?>"/>
  <input type="hidden" name="rangeValue" id="rangeValue" value="<?php echo $rangeValue;?>"/>
  <input type="checkbox" name="idle" id="idle" style="display: none;"/>
  <input type="checkbox" name="showPlannedWork" id="showPlannedWork" style="display: none;">
  <input type="checkbox" name="hideDone" id="hideDone" style="display: none;" />
  <input type="checkbox" name="hideNotHandled" id="hideNotHandled" style="display: none;" />
  <input type="checkbox" name="displayOnlyCurrentWeekMeetings" id="displayOnlyCurrentWeekMeetings" style="display: none;" />
  <input type="hidden" id="page" name="page" value="../report/imputation.php"/>
  <input type="hidden" id="outMode" name="outMode" value="" />
<?php 
ImputationLine::drawLines($userId, $rangeType, $rangeValue, $idle, $showPlannedWork, false, $hideDone, $hideNotHandled, $displayOnlyCurrentWeekMeetings);
?>
</form>