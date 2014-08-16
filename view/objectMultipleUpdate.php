<?php
/* ============================================================================
 * Presents the action buttons of an object.
 * 
 */ 
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/objectMultipleUpdate.php');

  $displayWidth='98%';
  if (array_key_exists('destinationWidth',$_REQUEST)) {
    $width=$_REQUEST['destinationWidth'];
    $displayWidth=floor($width*0.6);
    $labelWidth=250;
    $fieldWidth=$displayWidth-$labelWidth-15;
  } 
  $objectClass=$_REQUEST['objectClass'];
  $obj=new $objectClass();
?>
<div dojoType="dijit.layout.BorderContainer" class="background">
  <div id="buttonDiv" dojoType="dijit.layout.ContentPane" region="top">
    <div dojoType="dijit.layout.BorderContainer">
      <div id="buttonDivContainer" dojoType="dijit.layout.ContentPane" region="left">
        <table width="100%" class="listTitle" >
          <tr valign="middle" height="32px"> 
            <td width="50px" align="center" >
              <img style="position: absolute; top: 0px; left: 0px" src="css/images/icon<?php echo $objectClass;?>22.png" width="22" height="22" />
              <img style="position: absolute; top: 5px; left: 5px" src="css/images/icon<?php echo $objectClass;?>22.png" width="22" height="22" />
              <img style="position: absolute; top: 10px; left: 10px" src="css/images/icon<?php echo $objectClass;?>22.png" width="22" height="22" />
            </td>
            <td valign="middle"><span class="title"><?php echo i18n('labelMultipleMode');?></span></td>
            <td width="15px">&nbsp;</td>
            <td><nobr>
             <button id="selectAllButton" dojoType="dijit.form.Button" showlabel="false" 
               title="<?php echo i18n('buttonSelectAll');?>"
               iconClass="iconSelectAll" >
                <script type="dojo/connect" event="onClick" args="evt">
                   selectAllRows('objectGrid');
                   updateSelectedCountMultiple();
                </script>
              </button>    
              <button id="unselectAllButton" dojoType="dijit.form.Button" showlabel="false" 
               title="<?php echo i18n('buttonUnselectAll');?>"
               iconClass="iconUnselectAll" >
                <script type="dojo/connect" event="onClick" args="evt">
                   unselectAllRows('objectGrid');
                   updateSelectedCountMultiple();
                </script>
              </button>    
              <button id="saveButtonMultiple" dojoType="dijit.form.Button" showlabel="false"
               title="<?php echo i18n('buttonSaveMultiple');?>"
               iconClass="dijitEditorIcon dijitEditorIconSave" >
                <script type="dojo/connect" event="onClick" args="evt">
                  saveMultipleUpdateMode("<?php echo $objectClass;?>");  
                </script>
              </button>
              <button id="undoButtonMultiple" dojoType="dijit.form.Button" showlabel="false"
               title="<?php echo i18n('buttonQuitMultiple');?>"
               iconClass="dijitEditorIcon dijitEditorIconUndo" >
                <script type="dojo/connect" event="onClick" args="evt">
                  dojo.byId("undoButtonMultiple").blur();
                  endMultipleUpdateMode("<?php echo $objectClass;?>");
                </script>
              </button>    
              <button id="deleteButtonMultiple" dojoType="dijit.form.Button" showlabel="false" 
               title="<?php echo i18n('buttonDeleteMultiple');?>" style="display:none"
               iconClass="dijitEditorIcon dijitEditorIconDelete" >
                <script type="dojo/connect" event="onClick" args="evt">
                   deleteMultipleUpdateMode("<?php echo $objectClass;?>");  
                </script>
              </button>    
            </nobr></td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>
              <?php echo i18n("selectedItemsCount");?> :
              <input dojoType="dijit.form.TextBox" type="text" id="selectedCount" style="width: 40px" value="0" readonly />
            </td>
          </tr>
        </table>
      </div>
      <div dojoType="dijit.layout.ContentPane" region="center" >
        <div id="resultDiv">
        </div>
      </div>
    </div>
  </div>
  <div dojoType="dijit.layout.ContentPane" region="center">
    <div dojoType="dijit.layout.BorderContainer" class="background">
      <div dojoType="dijit.layout.ContentPane" region="center">
        <form dojoType="dijit.form.Form" id="objectFormMultiple" jsId="objectFormMultiple" 
          name="objectFormMultiple" encType="multipart/form-data" action="" method="">
          <script type="dojo/method" event="onSubmit">
            return false;        
          </script>
          <input type="hidden" id="selection" name="selection" value=""/>
          <table>
            <tr><td></td><td>&nbsp;</td></tr>
            <?php
             if (isDisplayable($obj,'idProject')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeProject',array($obj->getColCaption('idProject')));?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                 id="idProject" name="idProject">
                 <?php htmlDrawOptionForReference('idProject', null, null, false);?>
                </select>
                <button id="projectButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idProject",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
             if (isDisplayable($obj, 'description') ) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colAddToDescription');?>&nbsp;:&nbsp;</td>
              <td>
                <textarea dojoType="dijit.form.Textarea" name="description" id="description"
                 rows="2" style="width:<?php echo $fieldWidth;?>px;" maxlength="4000" maxSize="4" class="input" ></textarea>
              </td>
            </tr>
            <?php }
             if (isDisplayable($obj,'idStatus')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeStatus');?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                 id="idStatus" name="idStatus">
                 <?php htmlDrawOptionForReference('idStatus', null, null, false);?>
                </select>
                <button id="statusButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idStatus",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
            if (isDisplayable($obj,'idResource')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeResponsible',array($obj->getColCaption('idResource')));?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                 id="idResource" name="idResource">
                 <?php htmlDrawOptionForReference('idResource', null, null, false);?>
                </select>
                <button id="responsibleButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idResource",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
            if (isDisplayable($obj,'idUser')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeIssuer',array($obj->getColCaption('idUser')));?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                 id="idUser" name="idUser">
                 <?php htmlDrawOptionForReference('idUser', null, null, false);?>
                </select>
                <button id="userButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idUser",0); 
                  </script>
                </button>
              </td>
            </tr>
             <?php }
            if (isDisplayable($obj,'idContact')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeRequestor',array($obj->getColCaption('idContact')));?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                 id="idContact" name="idContact">
                 <?php htmlDrawOptionForReference('idContact', null, null, false);?>
                </select>
                <button id="contactButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idContact",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
             if (isDisplayable($obj,'idTargetVersion')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeTargetVersion');?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                 id="idTargetVersion" name="idTargetVersion">
                 <?php htmlDrawOptionForReference('idTargetVersion', null, null, false);?>
                </select>
                <button id="targetVersionButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idTargetVersion",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
            if (isDisplayable($obj,'result')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colAddToResult');?>&nbsp;:&nbsp;</td>
              <td>
                <textarea dojoType="dijit.form.Textarea" name="result" id="result"
                 rows="2" style="width:<?php echo $fieldWidth;?>px;" maxlength="4000" maxSize="4" class="input" ></textarea>
              </td>
            </tr>
            <?php }
            if (isDisplayable($obj,'_Note')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colAddNote');?>&nbsp;:&nbsp;</td>
              <td>
                <textarea dojoType="dijit.form.Textarea" name="note" id="note"
                 rows="2" style="width:<?php echo $fieldWidth;?>px;" maxlength="4000" maxSize="4" class="input" ></textarea>
              </td>
            </tr>
            <?php }
            if (isDisplayable($obj,'initialDueDate')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeInitialDueDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="initialDueDate" id="initialDueDate"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
            if (isDisplayable($obj,'actualDueDate')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeActualDueDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="actualDueDate" id="actualDueDate"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php } 
						if (isDisplayable($obj,'initialEndDate')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeInitialEndDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="initialEndDate" id="initialEndDate"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
            if (isDisplayable($obj,'actualEndDate')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeActualEndDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="actualEndDate" id="actualEndDate"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php } 
            if (isDisplayable($obj,'initialDueDateTime')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeInitialDueDateTime');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="initialDueDate" id="initialDueDate"
                 style="width:100px;" class="input" value="" ></div>
                <div dojoType="dijit.form.TimeTextBox" name="initialDueTime" id="initialDueTime"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
            if (isDisplayable($obj,'actualDueDateTime')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeActualDueDateTime');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="actualDueDate" id="actualDueDate"
                 style="width:100px;" class="input" value="" ></div>
                <div dojoType="dijit.form.TimeTextBox" name="actualDueTime" id="actualDueTime"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
            $pe=get_class($obj).'PlanningElement';
            if (isDisplayable($obj,'validatedStartDate', true)) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeValidatedStartDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="<?php echo $pe;?>_validatedStartDate" id="<?php echo $pe;?>_validatedStartDate"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
            $pe=get_class($obj).'PlanningElement';
            if (isDisplayable($obj,'validatedEndDate', true)) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeValidatedEndDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="<?php echo $pe;?>_validatedEndDate" id="<?php echo $pe;?>_validatedEndDate"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
            $pe=get_class($obj).'PlanningElement';
            $pm='id'.get_class($obj).'PlanningMode';
            if (isDisplayable($obj,$pm, true)) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changePlanningMode');?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth;?>px;" 
                 id="<?php echo $pe.'_'.$pm;?>" name="<?php echo $pe.'_'.$pm;?>">
                 <?php htmlDrawOptionForReference($pm, null, null, false);?>
                </select>
              </td>
            </tr>
            <?php }?>
          </table>
        </form>
      </div>
      <div dojoType="dijit.layout.ContentPane" id="resultDivMultiple" region="right" class="listTitle" style="width:40%"></div>
    </div>
  </div> 
</div>

<?php 
function isDisplayable($obj, $field, $fromPlanningElement=false) {
  if ( property_exists($obj,$field) 
  and ! $obj->isAttributeSetToField($field,'readonly') 
  and ! $obj->isAttributeSetToField($field,'hidden') ) {
    return true;
  } else {
    $pe=get_class($obj).'PlanningElement';
    if ($fromPlanningElement and property_exists($obj,$pe) and is_object($obj->$pe)) {
      $peObj=$obj->$pe;
      if (! $peObj->isAttributeSetToField($field,'readonly')
      and ! $peObj->isAttributeSetToField($field,'hidden') ) {
        return true;
      } else {
        return false;
      }      
    } else {
      return false;
    }
  }         
}
?>