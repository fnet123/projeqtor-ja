<?php
if (! isset ($print)) {
	$print=false;
}
if (! array_key_exists('objectClass',$_REQUEST)) {
	throwError('Parameter objectClass not found in REQUEST');
}
$objectClass=$_REQUEST['objectClass'];

if (! array_key_exists('objectId',$_REQUEST)) {
	throwError('Parameter objectId not found in REQUEST');
}
$objectId=$_REQUEST['objectId'];

$checklistDefinition=null;
$obj=new $objectClass($objectId);
$type='id'.$objectClass.'Type';
$checklist=SqlElement::getSingleSqlElementFromCriteria('Checklist', array('refType'=>$objectClass, 'refId'=>$objectId));
if ($checklist and $checklist->id) {
	$checklistDefinition=new ChecklistDefinition($checklist->idChecklistDefinition);
	if ($checklistDefinition->id and 
      ( ( $checklistDefinition->nameChecklistable!=$objectClass) 
      or( $checklistDefinition->idType and $checklistDefinition->idType!=$obj->$type)
      ) ) {
		$checklist->delete();
		unset($checklist);
	}
}
if (!$checklist or !$checklist->id) {
	$checklist=new Checklist();
}

if (!$checklistDefinition or ! $checklistDefinition->id) {
	if (property_exists($obj,$type)) {
		$crit=array('nameChecklistable'=>$objectClass, 'idType'=>$obj->$type);
  	$checklistDefinition=SqlElement::getSingleSqlElementFromCriteria('ChecklistDefinition', $crit);
	}
	if (!$checklistDefinition or !$checklistDefinition->id) {
		$crit=array('nameChecklistable'=>$objectClass);
		$checklistDefinition=SqlElement::getSingleSqlElementFromCriteria('ChecklistDefinition', $crit);
	}
}
if (!$checklistDefinition or !$checklistDefinition->id) {
	echo '<span class="ERROR" >'.i18n('noChecklistDefined').'</span>';
	exit;
}
$cdl=new ChecklistDefinitionLine();
$defLines=$cdl->getSqlElementsFromCriteria(array('idChecklistDefinition'=>$checklistDefinition->id),false, null, 'sortOrder asc');
//usort($defLines,"ChecklistDefinitionLine::sort");
$cl=new ChecklistLine();
$linesTmp=$cl->getSqlElementsFromCriteria(array('idChecklist'=>$checklist->id));

$linesVal=array();
foreach ($linesTmp as $line) {
	$linesVal[$line->idChecklistDefinitionLine]=$line;
}

$canUpdate=(securityGetAccessRightYesNo('menu' . $objectClass, 'update', $obj)=='YES');
if ($obj->idle) $canUpdate=false;
if ($print) $canUpdate=false;
?>
<?php if (! $print) {?>
<form id="dialogChecklistForm" name="dialogChecklistForm" action="">
<input type="hidden" name="checklistDefinitionId" value="<?php echo $checklistDefinition->id;?>" />
<input type="hidden" name="checklistId" value="<?php echo $checklist->id;?>" />
<input type="hidden" name="checklistObjectClass" value="<?php echo $objectClass;?>" />
<input type="hidden" name="checklistObjectId" value="<?php echo $objectId;?>" />
<?php } else {?>
<table style="width: 100%;"><tr><td class="section"><?php echo i18n("Checklist");?></td></tr></table>	
<?php }?> 
<table style="width: 100%;">
  <tr>
    <td style="width: 100%;">
	    <table width="100%;" >
<?php foreach($defLines as $line) {
	      if (isset($linesVal[$line->id])) {
          $lineVal=$linesVal[$line->id];
        } else {
          $lineVal=new ChecklistLine();
        }?>	 
		    <tr>
<?php   if ($line->check01) {?>
			    <td class="noteData" style="position: relative; border-right:0; text-align:right" title="<?php echo ($print)?'':$line->title;?>"> 
				  <?php echo htmlEncode( $line->name);?> :   
		      </td>
			    <td class="noteData" style="border-left:0;">
			      <table witdh="100%" style="width:100%;">
			        <tr>
				<?php for ($i=1;$i<=5;$i++) {
								$check='check0'.$i;
								$title='title0'.$i;
								$value='value0'.$i;?>
								<td style="min-width:100px;<?php if ($print) echo 'width:15%;'?>white-space:nowrap; vertical-align:top;" title="<?php echo ($print)?'':$line->$title;?>" >
					<?php if ($line->$check) {
								  $checkName="check_".$line->id."_".$i;
								  if ($print) {
		                $checkImg="checkedKO.png";
		                if ($lineVal->$value) {
			               $checkImg= 'checkedOK.png';
		                }
		                echo '<img src="img/' . $checkImg . '" />&nbsp;'.$line->$check.'&nbsp;&nbsp;';
							    } else {?>
								  <div dojoType="dijit.form.CheckBox" type="checkbox"
						        <?php if ($line->exclusive and ! $print) {?>onClick="checkClick(<?php echo $line->id;?>, <?php echo $i;?>)" <?php }?>
						        name="<?php echo $checkName;?>" id="<?php echo $checkName;?>"
						        <?php if (! $canUpdate) echo 'readonly';?>
				            <?php if ($lineVal->$value) { echo 'checked'; }?> ></div>
								  <span style="cursor:pointer;" onClick="dojo.byId('<?php echo $checkName;?>').click();"><?php echo $line->$check;?>&nbsp;&nbsp;</span>
					  <?php } 
		            }?>
		            </td>
				<?php }?>
					
				<td style="text-align:right; width:15px; color: #A0A0A0;" valign="top">				  
				<?php 
				  if ($lineVal->checkTime and !$print) {
            echo '<img src="../view/img/note.png"'; 
            echo 'title="'.SqlList::getNameFromId('User',$lineVal->idUser)."\n";
            echo htmlFormatDateTime($lineVal->checkTime,false).'"';
            echo '/>';
         }?></td>
				<td >&nbsp;</td>
				<td valign="top" style="width: 150px;"> 
				  <?php if (! $print) {?>
				  <textarea dojoType="dijit.form.Textarea" 
            id="checklistLineComment_<?php echo $line->id;?>" name="checklistLineComment_<?php echo $line->id;?>"
            style="width: 150px;min-height: 25px; font-size: 90%"
            maxlength="4000"
            class="input"><?php echo $lineVal->comment;?></textarea>
          <?php } else {
            echo htmlEncode($lineVal->comment); 
                }?>  
				</td>
				  </tr></table></td>
				
<?php } else { ?>
				<td class="reportTableHeader" colspan="2" style="text-align:center" title="<?php echo $line->title;?>">
				  <?php echo $line->name;?>
				  <div style="width: 150px; float:right; font-weight: normal"><?php echo i18n('colComment')?></div>
				</td>
<?php }?>		
	    </tr>
<?php } // end foreach($defLine?>
      <tr>
        <td class="noteDataClosetable">&nbsp;</td>
	      <td class="noteDataClosetable">&nbsp;</td>
	    </tr>
	    <?php if (! $print or $checklist->comment) {?>
	    <tr>
	      <td style="text-align: right;"><?php echo i18n('colComment')?>&nbsp;:&nbsp;</td>
	      <td>
	      <?php if (! $print) {?>
				  <textarea dojoType="dijit.form.Textarea" 
            id="checklistComment" name="checklistComment"
            style="width: 100%;font-size: 90%"
            maxlength="4000"
            class="input"><?php echo $checklist->comment;?></textarea>
          <?php } else {
            echo htmlEncode($checklist->comment); 
                }?>  
	      </td>
	    </tr>
	    <?php }?>
	  </table>
  </td></tr>
 <tr><td style="width: 100%;">&nbsp;</td></tr>
<?php if (! $print) {?>
 <tr>
   <td style="width: 100%;" align="center">
     <button dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogChecklist').hide();">
       <?php echo i18n("buttonCancel");?>
     </button>
     <button id="dialogChecklistSubmit" dojoType="dijit.form.Button" type="submit" 
       onclick="saveChecklist();return false;" >
       <?php echo i18n("buttonOK");?>
     </button>
   </td>
 </tr>      
<?php }?> 
</table>
<?php if (! $print) {?></form><?php }?>
