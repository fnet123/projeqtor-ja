<?php
if (! array_key_exists('checkId',$_REQUEST)) {
  throwError('objectClass checkId not found in REQUEST');
}
$checkId=null;
if ( array_key_exists('checkId',$_REQUEST) ) {
  $checkId=$_REQUEST['checkId'];
}
$lineId=0;
if ( array_key_exists('lineId',$_REQUEST)) {
	$lineId=$_REQUEST['lineId'];
}
$line=new ChecklistDefinitionLine($lineId);
if ($line->id) {
	$checkId=$line->idChecklistDefinition;
} else {
	$line->exclusive=1;
}

?>
<form id="dialogChecklistDefinitionLineForm" name="dialogChecklistDefinitionLineForm" action="">
<input type="hidden" name="checklistDefinitionLineId" value="<?php echo $line->id;?>" />
<input type="hidden" name="checklistDefinitionId" value="<?php echo $checkId;?>" />
<table style="width: 100%;">
  <tr>
    <td class="dialogLabel" ><label><?php echo i18n('colName');?> : </label></td>
    <td><input type="text" dojoType="dijit.form.TextBox" 
      id="dialogChecklistDefinitionLineName" 
      name="dialogChecklistDefinitionLineName"
      value="<?php echo $line->name;?>"
      style="width: 300px;" maxlength="100" class="input" />
    </td>
  </tr>
  <tr>
    <td class="dialogLabel" >&nbsp;</td>
    <td><textarea dojoType="dijit.form.Textarea" 
          id="dialogChecklistDefinitionLineTitle" name="dialogChecklistDefinitionLineTitle"
          style="width: 300px;"
          maxlength="1000"
          value="<?php echo $line->title;?>"
          title="<?php echo i18n('helpTitle');?>"
          class="input"></textarea>
    </td>
  </tr>
  <tr>
    <td class="dialogLabel" ><label><?php echo i18n('colSortOrder');?> : </label></td>
    <td><input type="text" dojoType="dijit.form.TextBox" 
      id="dialogChecklistDefinitionLineSortOrder" 
      name="dialogChecklistDefinitionLineSortOrder"
      value="<?php echo $line->sortOrder;?>"
      style="width: 30px;" maxlength="3" class="input" />
    </td>
  </tr>
<?php for ($i=1;$i<=5;$i++) {?>
  <tr>
    <td class="dialogLabel" ><label><?php echo i18n('colChoice') . ' #'.$i;?> : </label></td>
    <td><input type="text" dojoType="dijit.form.TextBox" 
      id="dialogChecklistDefinitionLineChoice_<?php echo $i?>" 
      name="dialogChecklistDefinitionLineChoice_<?php echo $i?>"
      value="<?php $var="check0$i";echo $line->$var;?>"
      style="width: 300px;" maxlength="100" class="input" />
    </td>  
  </tr>
  <tr>
    <td class="dialogLabel" >&nbsp;</td>
    <td><textarea dojoType="dijit.form.Textarea" 
          id="dialogChecklistDefinitionLineTitle_<?php echo $i?>" 
          name="dialogChecklistDefinitionLineTitle_<?php echo $i?>"
          style="width: 300px;"
          maxlength="1000"
          title="<?php echo i18n('helpTitle');?>"
          class="input"><?php $vart="title0$i";echo $line->$vart; ?></textarea>
    </td>
  </tr>
<?php }?>
  <tr>
    <td class="dialogLabel" ><label><?php echo i18n('colExclusive');?> : </label></td>
    <td> 
      <input dojoType="dijit.form.CheckBox" 
       name="dialogChecklistDefinitionLineExclusive" 
       id="dialogChecklistDefinitionLineExclusive"
       <?php echo ($line->exclusive)?' checked="checked" ':'';?>
       value="" style="background-color:white;" />
   </td>
 </tr>
 <tr><td colspan="2">&nbsp;</td></tr>
 <tr>
   <td colspan="2" align="center">
     <button dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogChecklistDefinitionLine').hide();">
       <?php echo i18n("buttonCancel");?>
     </button>
     <button id="dialogChecklistDefinitionLineSubmit" dojoType="dijit.form.Button" type="submit" 
       onclick="saveChecklistDefinitionLine();return false;" >
       <?php echo i18n("buttonOK");?>
     </button>
   </td>
 </tr>      
</table>
</form>
