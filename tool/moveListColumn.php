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

/** ===========================================================================
 * Move task (from before to)
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/moveListColumn.php');
if (! array_key_exists('orderedList',$_REQUEST)) {
  throwError('orderedList parameter not found in REQUEST');
}
$list=$_REQUEST['orderedList'];
$arrayList=explode("|", $list);
$user=$_SESSION['user'];

Sql::beginTransaction();
$cpt=0;
foreach ($arrayList as $id) {
	if (trim($id)) {
		$cpt++;
	  $cs=new ColumnSelector($id);
	  $cs->sortOrder=$cpt;
		$result=$cs->save();
	}
}
//$result="ERROR";
//$result.=" " . $idFrom . '->' . $idTo .'(' . $mode . ')';
if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {
	Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
} else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
	Sql::commitTransaction();
  echo '<span class="messageOK" >' . '</span>';
} else { 
	Sql::commitTransaction();
  echo '<span class="messageWARNING" >' . '</span>';
}
?>