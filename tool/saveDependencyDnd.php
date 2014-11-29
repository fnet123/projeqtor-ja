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

// Get the link info
if (! array_key_exists('ref1Type',$_REQUEST)) {
  throwError('ref1Type parameter not found in REQUEST');
}
$ref1Type=$_REQUEST['ref1Type'];

if (! array_key_exists('ref1Id',$_REQUEST)) {
  throwError('ref1Id parameter not found in REQUEST');
}
$ref1Id=$_REQUEST['ref1Id'];

if (! array_key_exists('ref2Type',$_REQUEST)) {
  throwError('ref2Type parameter not found in REQUEST');
}
$ref2Type=$_REQUEST['ref2Type'];

if (! array_key_exists('ref2Id',$_REQUEST)) {
  throwError('ref2Id parameter not found in REQUEST');
}
$ref2Id=$_REQUEST['ref2Id'];

$dependencyDelay=0;
if (array_key_exists('dependencyDelay',$_REQUEST)) {
  $dependencyDelay=$_REQUEST['dependencyDelay'];
}
Sql::beginTransaction();
$result="";
$critPredecessor=array("refType"=>$ref1Type,"refId"=>$ref1Id);
$critSuccessor=array("refType"=>$ref2Type,"refId"=>$ref2Id);

$successor=SqlElement::getSingleSqlElementFromCriteria('PlanningElement',$critSuccessor);
$predecessor=SqlElement::getSingleSqlElementFromCriteria('PlanningElement',$critPredecessor);;
		
$dep=new Dependency();
$dep->successorId=$successor->id;
$dep->successorRefType=$successor->refType;
$dep->successorRefId=$successor->refId;
$dep->predecessorId=$predecessor->id;
$dep->predecessorRefType=$predecessor->refType;
$dep->predecessorRefId=$predecessor->refId;
$dep->dependencyType='E-S';
$dep->dependencyDelay=$dependencyDelay;
$result=$dep->save();

// Message of correct saving
if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {	
	$result.='<input type="hidden" id="lastPlanStatus" value="OK" />';
  Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
} else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
  Sql::commitTransaction();
  echo '<span class="messageOK" >' . $result . '</span>';
} else { 
	$result=str_replace('<b>'.i18n('messageInvalidControls').'</b><br/><br/>','',$result);
	$result=str_replace('id="lastOperationStatus" value="INVALID"','id="lastOperationStatus" value="KO"',$result);
	$result.='<input type="hidden" id="lastPlanStatus" value="OK" />';
  Sql::rollbackTransaction();
  echo '<span class="messageERROR" >' . $result . '</span>';
}
?>