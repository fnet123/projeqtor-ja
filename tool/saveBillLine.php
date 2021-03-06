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

// Get the bill line info
$lineId=null;
if (array_key_exists('billLineId',$_REQUEST)) {
  $lineId=$_REQUEST['billLineId'];
}

if (! array_key_exists('billLineRefType',$_REQUEST)) {
  throwError('billLineRefType parameter not found in REQUEST');
}
$refType=$_REQUEST['billLineRefType'];

if (! array_key_exists('billLineRefId',$_REQUEST)) {
  throwError('billLineRefId parameter not found in REQUEST');
}
$refId=$_REQUEST['billLineRefId'];

if (! array_key_exists('billLineLine',$_REQUEST)) {
	throwError('billLineLine parameter not found in REQUEST');
}
$lineNum=$_REQUEST['billLineLine'];

$quantity=null;
if (array_key_exists('billLineQuantity',$_REQUEST)) {
  $quantity=$_REQUEST['billLineQuantity'];
}

$idTerm="";
if (array_key_exists('billLineIdTerm',$_REQUEST)) {
   $idTerm=$_REQUEST['billLineIdTerm'];
}

$idResource="";
if (array_key_exists('billLineIdResource',$_REQUEST)) {
   $idResource=$_REQUEST['billLineIdResource'];
}

$idActivityPrice="";
if (array_key_exists('billLineIdActivityPrice',$_REQUEST)) {
   $idActivityPrice=$_REQUEST['billLineIdActivityPrice'];
}

$startDate="";
if (array_key_exists('billLineStartDate',$_REQUEST)) {
  $startDate=$_REQUEST['billLineStartDate'];
}

$endDate="";
if (array_key_exists('billLineEndDate',$_REQUEST)) {
  $endDate=$_REQUEST['billLineEndDate'];
}

$description=null;
if (array_key_exists('billLineDescription',$_REQUEST)) {
  $description=$_REQUEST['billLineDescription'];
}

$detail=null;
if (array_key_exists('billLineDetail',$_REQUEST)) {
  $detail=$_REQUEST['billLineDetail'];
}

$price=null;
if (array_key_exists('billLinePrice',$_REQUEST)) {
  $price=$_REQUEST['billLinePrice'];
}

$lineId=trim($lineId);
if ($lineId=='') {
  $lineId=null;
} 
Sql::beginTransaction();
$line=new BillLine($lineId);
$line->refType=$refType;
$line->refId=$refId;
$line->line=$lineNum;
$line->quantity=$quantity;
$line->idTerm=$idTerm;
$line->idResource=$idResource;
$line->idActivityPrice=$idActivityPrice;
$line->startDate=$startDate;
$line->endDate=$endDate;
$line->description=$description;
$line->detail=$detail;
$line->price=$price;
$result=$line->save();

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