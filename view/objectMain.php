<?php 
/* ============================================================================
 * Presents an object. 
 */
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/objectMain.php');
  $listHeight='40%';
  if (isset($_REQUEST['objectClass'])) {
  	if ($_REQUEST['objectClass']=='CalendarDefinition') {
  		$listHeight='25%';
  	}
  }
?>
<div id="mainDivContainer" class="container" dojoType="dijit.layout.BorderContainer" liveSplitters="false">
  <div id="listDiv" dojoType="dijit.layout.ContentPane" region="top" splitter="true" style="height:<?php echo $listHeight;?>">
   <?php include 'objectList.php'?>
  </div>
  <div id="detailDiv" dojoType="dijit.layout.ContentPane" region="center" >
   <?php $noselect=true; include 'objectDetail.php'; ?>
  </div>
</div>