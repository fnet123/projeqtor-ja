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
 * Delete the current attachement : call corresponding method in SqlElement Class
 */

require_once "../tool/projeqtor.php";

$attachementId=null;
if (array_key_exists('attachementId',$_REQUEST)) {
  $attachementId=$_REQUEST['attachementId'];
}
$attachementId=trim($attachementId);
if ($attachementId=='') {
  $attachementId=null;
} 
if ($attachementId==null) {
  throwError('attachementId parameter not found in REQUEST');
}
$obj=new Attachement($attachementId);
$subDirectory=str_replace('${attachementDirectory}', Parameter::getGlobalParameter('paramAttachementDirectory'), $obj->subDirectory);
if (file_exists($subDirectory . $obj->fileName)) {
  unlink($subDirectory . $obj->fileName);
  purgeFiles($subDirectory, null);
  rmdir($subDirectory);
}
Sql::beginTransaction();
$result=$obj->delete();

// Message of correct saving
if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {
	Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
} else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
	Sql::commitTransaction();
  echo '<span class="messageOK" >' . $result . '</span>';
} else { 
	Sql::rollbackTransaction();
  echo '<span class="messageWARNING" >' . $result . '</span>';
}
?>