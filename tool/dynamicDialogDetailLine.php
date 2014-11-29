<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2014 Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 *
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ************************************************/

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

if (! array_key_exists('detailType',$_REQUEST)) {
	throwError('Parameter detailType not found in REQUEST');
}
$detailType=$_REQUEST['detailType'];

if (! $print) {?>
<form id="dialogDetailLineForm" name="dialogDetailLineForm" action="">
<input type="hidden" name="detailLineDetailType" value="<?php echo $detailLineDetailType;?>" />
<input type="hidden" name="detailLineObjectClass" value="<?php echo $detailLineObjectClass;?>" />
<input type="hidden" name="detailLineObjectId" value="<?php echo $detailLineObjectId;?>" />
<?php }?>
<table style="width: 100%;">
  <tr><td style="width: 100%;">

  </td></tr>
 <tr><td style="width: 100%;">&nbsp;</td></tr>
<?php if (! $print) {?>
 <tr>
   <td style="width: 100%;" align="center">
     <button dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogDetailLine').hide();">
       <?php echo i18n("buttonCancel");?>
     </button>
     <button id="dialogChecklistSubmit" dojoType="dijit.form.Button" type="submit" 
       onclick="saveDetailLine();return false;" >
       <?php echo i18n("buttonOK");?>
     </button>
   </td>
 </tr>      
<?php }?> 
</table>
<?php if (! $print) {?></form><?php }?>
