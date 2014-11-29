<?php 
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2014 Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 * 
 * Most of properties are extracted from Dojo Framework.
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

function getGraphImgName($root) {
  global $reportCount;
  //$user=$_SESSION['user'];
  $reportCount+=1;
  $name=Parameter::getGlobalParameter('paramReportTempDirectory');
  $name.="/user" . getCurrentUserId() . "_";
  $name.=$root . "_";
  $name.=date("Ymd_His") . "_";
  $name.=$reportCount;
  $name.=".png";  
  return $name;
}

function testGraphEnabled() {
  global $graphEnabled;
  if ($graphEnabled) {
    return true;
  } else {
    //echo '<table width="95%" align="center"><tr><td align="center">';
    //echo '<img src="../view/img/GDnotEnabled.png" />'; 
    //echo '</td></tr></table>';
    return false;
  }  
}

function checkNoData($result) {
  global $outMode;
  if (count($result)==0) {
    echo '<table width="95%" align="center"><tr height="50px"><td width="100%" align="center">';
    echo i18n('reportNoData');
    echo '</td></tr></table>';
    if ($outMode=='pdf') {
      finalizePrint();
    }
    return true;
  }
  return false;
}
?>
