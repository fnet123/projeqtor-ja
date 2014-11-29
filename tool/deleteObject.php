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
 * Delete the current object : call corresponding method in SqlElement Class
 */

require_once "../tool/projeqtor.php";

// Get the object from session(last status before change)
if (isset($_REQUEST['directAccessIndex'])) {
  if (! isset($_SESSION['directAccessIndex'][$_REQUEST['directAccessIndex']])) {
    throwError('currentObject parameter not found in SESSION');
  }
  $obj=$_SESSION['directAccessIndex'][$_REQUEST['directAccessIndex']];
} else {
  if (! array_key_exists('currentObject',$_SESSION)) {
    throwError('currentObject parameter not found in SESSION');
  }
  $obj=$_SESSION['currentObject'];
}
if (! is_object($obj)) {
  throwError('last saved object is not a real object');
}

// Get the object class from request
if (! array_key_exists('className',$_REQUEST)) {
  throwError('className parameter not found in REQUEST');
}
$className=$_REQUEST['className'];

// compare expected class with object class
if ($className!=get_class($obj)) {
  throwError('last save object (' . get_class($obj) . ') is not of the expected class (' . $className . ').'); 
}
if (array_key_exists('confirmed',$_REQUEST) ) {
  if ($_REQUEST['confirmed']=='true') {
  	SqlElement::setDeleteConfirmed();
  }
}
Sql::beginTransaction();
$obj=new $className($obj->id); // Get the last saved version, to fetch last version for array of objects
// delete from database
$result=$obj->delete();

// Message of correct saving
if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {
	Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
} else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
	Sql::commitTransaction();
  echo '<span class="messageOK" >' . $result . '</span>';
  if (isset($_REQUEST['directAccessIndex'])) {
  	unset($_SESSION['directAccessIndex'][$_REQUEST['directAccessIndex']]);
  } else {
    unset($_SESSION['currentObject']);
  }
} else { 
	Sql::commitTransaction();
  echo '<span class="messageWARNING" >' . $result . '</span>';
}
echo '<input type="hidden" id="buttonCheckListVisibleObject" value="hidden" />';
?>