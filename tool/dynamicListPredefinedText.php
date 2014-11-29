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

/** ============================================================================
 * 
 */

require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/dynamicListPredefinedText.php');
$refType=$_REQUEST['objectClass'];
$refId=$_REQUEST['objectType'];

$refTypeId=SqlList::getIdFromTranslatableName('Textable', $refType);
//echo $refType.'/'.$refId;

$crit="scope='Note' and (idTextable is null or idTextable='" . Sql::fmtId($refTypeId) ."')";
$crit.=" and (idType is null or idType='" . Sql::fmtId($refId) ."') and idle=0";

$txt=new PredefinedNote();
$list=$txt->getSqlElementsFromCriteria(null, false, $crit, 'name asc');
if (count($list)==0) {
	return;
}
?>
<label for="dialogNotePredefinedNote" ><?php echo i18n("colPredefinedNote");?>&nbsp;:&nbsp;</label>
<select id="dialogNotePredefinedNote" name="dialogNotePredefinedNote" 
onchange="noteSelectPredefinedText(this.value);" dojoType="dijit.form.FilteringSelect"  
class="input" style="width:345px">
 <option value=""></option>
 <?php
 foreach ($list as $lstObj) {
   echo '<option value="' . $lstObj->id .'" >'.htmlEncode($lstObj->name).'</option>';
 }
 
 ?>
</select>