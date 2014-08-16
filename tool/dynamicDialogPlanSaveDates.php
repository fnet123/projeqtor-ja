  <table width="500px">
    <tr>
      <td width="100%">
       <form id='dialogPlanSaveDatesForm' name='dialogPlanSaveDatesForm' onSubmit="return false;">
         <table width="100%" >
           <tr>
             <td class="dialogLabel"  >
               <label for="idProjectPlanSaveDates" ><?php echo i18n("colIdProject") ?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <select dojoType="dijit.form.FilteringSelect" 
                id="idProjectPlanSaveDates" name="idProjectPlanSaveDates" 
                class="input" value="" >
                 <?php 
                    $proj=null; 
                    if (array_key_exists('project',$_SESSION)) {
                        $proj=$_SESSION['project'];
                    }
                    if ($proj=="*" or ! $proj) $proj=null;
                    htmlDrawOptionForReference('idProject', $proj, null, false);
                 ?>
               </select>
             </td>
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
           <tr><td colspan="2" style="width:100%; text-align: center;">
             
             <table width="100%">
               <tr><td colspan="5"><b><?php echo i18n("reportPlannedDates");?><br/></b></td></tr>
               <tr><td colspan="5">&nbsp;</td>
               <tr>
                 <td style="width:35%;text-align: right;"><b><?php echo i18n('updateInitialDates');?></b></td>
                 <td style="width:5%">&nbsp;</td> 
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateInitialDates" id="updateInitialDatesAlways" 
	                    value="ALWAYS" /><?php echo i18n('always');?></td>
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateInitialDates" id="updateInitialDatesIfEmpty" 
	                    checked value="IFEMPTY" /><?php echo i18n('ifEmpty');?></td>
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateInitialDates" id="updateInitialDatesNever"  
	                    value="NEVER" /><?php echo i18n('never');?></td>
               </tr>
               <tr><td colspan="5">&nbsp;</td>	
               <tr>
                 <td style="width:35%;text-align: right;"><b><?php echo i18n('updateValidatedDates');?></b></td>
                 <td style="width:5%">&nbsp;</td> 
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateValidatedDates" id="updateValidatedDatesAlways" 
	                    checked value="ALWAYS" /><?php echo i18n('always');?></td>
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateValidatedDates" id="updateValidatedDatesIfEmpty" 
	                     value="IFEMPTY" /><?php echo i18n('ifEmpty');?></td>
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateValidatedDates" id="updateValidatedDatesNever"  
	                    value="NEVER" /><?php echo i18n('never');?></td>
               </tr> 
             </table>
           </td></tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
         </table>
        </form>
      </td>
    </tr>
    <tr>
      <td align="center">
        <input type="hidden" id="dialogPlanSaveDatesCancel">
        <button dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogPlanSaveDates').hide();">
          <?php echo i18n("buttonCancel");?>
        </button>
        <button dojoType="dijit.form.Button" type="submit" id="dialogPlanSaveDatesSubmit" onclick="planSaveDates();return false;">
          <?php echo i18n("buttonOK");?>
        </button>
      </td>
    </tr>
  </table>