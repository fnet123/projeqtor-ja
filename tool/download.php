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
 * Download a file descripbde in the correcponding object 
 * @param class=class of object containing file description
 * @param id = id of object
 * @param display = bolean (existence is enough) to enable display, either download is forced
 */

require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/download.php');
$class=$_REQUEST['class'];
$id=$_REQUEST['id'];
$paramFilenameCharset=Parameter::getGlobalParameter('filenameCharset');

$obj=new $class($id);

$preserveFileName=Parameter::getGlobalParameter('preserveUploadedFileName');
if (!$preserveFileName) $preserveFileName="NO";

if ($class=='Attachement') {
  $path = str_replace('${attachementDirectory}', Parameter::getGlobalParameter('paramAttachementDirectory'), $obj->subDirectory);
  $name = $obj->fileName;
  if ($paramFilenameCharset) {
  	$name = iconv("UTF-8",$paramFilenameCharset.'//TRANSLIT//IGNORE',$name);
  }
  $size = $obj->fileSize;
  $type = $obj->mimeType;
  $file = $path . $name;
  if (! is_file($file)) {
    $file=addslashes($file);
  }
} else if ($class=='DocumentVersion') {
  $name = ($preserveFileName!="YES" and $obj->fullName and  pathinfo($obj->fullName, PATHINFO_EXTENSION)==pathinfo($obj->fileName, PATHINFO_EXTENSION))?$obj->fullName:$obj->fileName;
  $size = $obj->fileSize;
  $type = $obj->mimeType;
  $file = $obj->getUploadFileName();
} else if ($class=='Document') {
	if (!$obj->idDocumentVersion) return;
	$obj=new DocumentVersion($obj->idDocumentVersion);
	$name = ($preserveFileName!="YES" and $obj->fullName and  pathinfo($obj->fullName, PATHINFO_EXTENSION)==pathinfo($obj->fileName, PATHINFO_EXTENSION))?$obj->fullName:$obj->fileName;
  $size = $obj->fileSize;
  $type = $obj->mimeType;
  $file = $obj->getUploadFileName();
}
$contentType="application/force-download";
if ($type) {$contentType=$type;}
//if (array_key_exists('display',$_REQUEST)) {
//  $contentType=$type;
//}
if (substr($name, -10)=='.projeqtor') {
	$name=substr($name,0,strlen($name)-10);
} 

if (($file != "") && (file_exists($file))) { 
	header("Pragma: public"); 
  header("Content-Type: " . $contentType . "; name=\"" . $name . "\""); 
  header("Content-Transfer-Encoding: binary"); 
  header("Content-Length: $size"); 
  if (!array_key_exists('showHtml', $_REQUEST)) {
    header("Content-Disposition: attachment; filename=\"" .$name . "\"");
  }
  header("Expires: 0"); 
  header("Cache-Control: no-cache, must-revalidate");
  header("Cache-Control: private",false);
  header("Pragma: no-cache");
  if (ob_get_length()){   
    ob_clean();
  }
  flush();
  
  readfile($file);  
} else {
	errorLog("download.php : ".$file . ' not found');
}

?>