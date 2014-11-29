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

/* ============================================================================
 * Presents the list of objects of a given class.
 *
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/view/reportsList.php');

//$objectClass='Task';
//$obj=new $objectClass;
?>

  
<div dojoType="dijit.layout.BorderContainer">
<div dojoType="dijit.layout.ContentPane" region="top" id="listHeaderDiv" height="27px">
<table width="100%" height="27px" class="listTitle" >
  <tr height="27px">
    <td width="50px" align="center">
      <span style="position:absolute; left:10px; top:2px"><img src="css/images/iconReports22.png" width="22" height="22" /></span>
    </td>
    <td><span class="title"><?php echo i18n('menuReports');?></span></td>
    <td>   
      <form dojoType="dijit.form.Form" id="listForm" action="" method="" >
        <table style="width: 100%;">
          <tr>
            <td>
              <input type="hidden" id="objectClass" name="objectClass" value="" /> 
              <input type="hidden" id="objectId" name="objectId" value="" />
              &nbsp;&nbsp;&nbsp;
            </td>
            <td>
            </td>
            <td><div id="planResultDiv" style=" width: 400px;height: 10px;" dojoType="dijit.layout.ContentPane" region="center" ></div></td>
            <td style="text-align: right; align: right;">
            </td>
          </tr>
        </table>    
      </form>
    </td>
  </tr>
</table>
</div>
<div dojoType="dijit.layout.ContentPane" region="center" id="gridContainerDiv">
  <table>
    <tr>
      <td class="tabLabel">
        <?php echo i18n('colCategory');?>        
      </td>
      <td width="5px">&nbsp;</td>
      <td class="tabLabel" >
        <?php echo i18n('colReport');?>
      </td>
      <td width="20px">&nbsp;</td>
      <td class="tabLabel" >
        <?php echo i18n('colParameters');?>
      </td>
    </tr>
    <tr>
      <td>
        <select id="reportsCategory" name="reportsCategory" value=""  
                dojoType="dijit.form.MultiSelect" multiple="false"
                 style="width:200px" size="10" class="input" >
           <?php htmlDrawOptionForReference('idReportCategory',null,null, true); ?>
          <script type="dojo/connect" event="onChange" args="value">
             reportSelectCategory(value);       
          </script>
        </select>
      </td>
      <td ></td>
      <td>
        <div dojoType="dojo.data.ItemFileReadStore" jsId="reportStore" url="../tool/jsonList.php?listType=empty" searchAttr="name" >
        </div>
        <select id="reportsList" name="reportsList" value=""  
                dojoType="dijit.form.MultiSelect"  multiple="false"
                style="width:300px" size="10" class="input" store="reportStore">
        <script type="dojo/connect" event="onChange" args="value">
             reportSelectReport(value);       
          </script>
        </select>
      </td>
      <td ></td>
      <td valign="top">
        <div id="reportParametersDiv" dojoType="dijit.layout.ContentPane" region="right" ></div>
      </td>   
    </tr>
    
  </table>
</div>
</div>
