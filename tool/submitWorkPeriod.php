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
 * Save a note : call corresponding method in SqlElement Class
 * The new values are fetched in $_REQUEST
 */

require_once "../tool/projeqtor.php";

if (! array_key_exists('action',$_REQUEST)) {
  throwError('action parameter not found in REQUEST');
}
$action=$_REQUEST['action'];

if (! array_key_exists('rangeType',$_REQUEST)) {
  throwError('rangeType parameter not found in REQUEST');
}
$rangeType=$_REQUEST['rangeType'];

if (! array_key_exists('rangeValue',$_REQUEST)) {
  throwError('rangeValue parameter not found in REQUEST');
}
$rangeValue=$_REQUEST['rangeValue'];

if (! array_key_exists('resource',$_REQUEST)) {
  throwError('resource parameter not found in REQUEST');
}
$resource=$_REQUEST['resource'];

Sql::beginTransaction();
// get the modifications (from request)
$period=new WorkPeriod();
$crit=array('idResource'=>$resource, 'periodRange'=>$rangeType,'periodValue'=>$rangeValue);
$period=SqlElement::getSingleSqlElementFromCriteria('WorkPeriod', $crit);
if ($action=='submit') {
	$period->submitted=1;
	$period->submittedDate=date('Y-m-d H:i:s');
} if ($action=='unsubmit') {
  $period->submitted=0;
  $period->submittedDate=null;
} if ($action=='validate') {
  $period->validated=1;
  $period->validatedDate=date('Y-m-d H:i:s');
  $user=$_SESSION['user'];
  $period->idLocker=$user->id;
} if ($action=='unvalidate') {
	$period->validated=0;		
  $period->validatedDate=null;
}
$result=$period->save();

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