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

include_once "../tool/projeqtor.php";
scriptLog('   ->/tool/import.php');
header ('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" 
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title><?php echo i18n("applicationTitle");?></title>
  <link rel="shortcut icon" href="../view/img/logo.ico" type="../view/image/x-icon" />
  <link rel="icon" href="../view/img/logo.ico" type="../view/image/x-icon" />
  <link rel="stylesheet" type="text/css" href="../view/css/projeqtor.css" />
  <script type="text/javascript" src="../view/js/projeqtorDialog.js?version=<?php echo $version.'.'.$build;?>" ></script>
</head>

<body class="white" onLoad="top.hideWait();//showInfo('<?php echo i18n('ImportCompleted')?>');" style="overflow: auto; ">
<?php 
$class='';
$dateFormat='dd/mm/yyyy';

if (! array_key_exists('elementType',$_REQUEST)) {
	throwError('elementType parameter not found in REQUEST');
}
$class=SqlList::getNameFromId('Importable',$_REQUEST['elementType'],false);

///
/// Upload file
$error=false;
if (array_key_exists('importFile',$_FILES)) {
  $uploadedFile=$_FILES['importFile'];
} else {
  echo htmlGetErrorMessage(i18n('errorNotFoundFile'));
  errorLog(i18n('errorNotFoundFile'));
  exit;
}
$attachementMaxSize=Parameter::getGlobalParameter('paramAttachementMaxSize');
if ( $uploadedFile['error']!=0 ) {
  switch ($uploadedFile['error']) {
    case 1:
      echo htmlGetErrorMessage(i18n('errorTooBigFile',array(ini_get('upload_max_filesize'),'upload_max_filesize')));
      errorLog(i18n('errorTooBigFile',array(ini_get('upload_max_filesize'),'upload_max_filesize')));
      exit;
      break; 
    case 2:  	
      echo htmlGetErrorMessage(i18n('errorTooBigFile',array($attachementMaxSize,'$paramAttachementMaxSize')));
      errorLog(i18n('errorTooBigFile',array($attachementMaxSize,'$paramAttachementMaxSize')));
      exit;
      break;  
    case 4:
      echo htmlGetWarningMessage(i18n('errorNoFile'));
      errorLog(i18n('errorNoFile'));
      exit;
      break;  
    default:
      echo htmlGetErrorMessage(i18n('errorUploadFile',array($uploadedFile['error'])));
      errorLog(i18n('errorUploadFile',array($uploadedFile['error'])));
      exit;
      break;
  }
  }
if (! $uploadedFile['name']) {
  echo htmlGetWarningMessage(i18n('errorNoFile'));
  errorLog(i18n('errorNoFile'));
  $error=true; 
}
$pathSeparator=Parameter::getGlobalParameter('paramPathSeparator');
$attachementDirectory=Parameter::getGlobalParameter('paramAttachementDirectory');
$uploaddir = $attachementDirectory . $pathSeparator . "import" . $pathSeparator;
if (! file_exists($uploaddir)) {
  mkdir($uploaddir,0777,true);
}
$uploadfile = $uploaddir . basename($uploadedFile['name']);
if ( ! move_uploaded_file($uploadedFile['tmp_name'], $uploadfile)) {
   echo htmlGetErrorMessage(i18n('errorUploadFile','hacking ?'));
   errorLog(i18n('errorUploadFile','hacking ?'));
   exit; 
}

//// V2.6 : extracted the import function to Importable class to use it from Cron
$result=Importable::import($uploadfile, $class);
echo Importable::$importResult;
//echo $result;
?>
</body>
</html>