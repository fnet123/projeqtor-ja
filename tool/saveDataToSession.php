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

/** ============================================================================
 * Save some information to session (remotely).
 */
require_once "../tool/projeqtor.php";

$id=$_REQUEST['id'];
if ($id=='disconnect') {
  //$user=$_SESSION['user'];
  //$user->disconnect();
  //session_destroy();
	if (isset($_REQUEST['cleanCookieHash']) and $_REQUEST['cleanCookieHash']=='true' and isset($_SESSION['user']) ) {
		 $user=new User($_SESSION['user']->id);
		 $user->cleanCookieHash();
	}
	Audit::finishSession();

  exit;
}

$value=$_REQUEST['value'];

$_SESSION[$id]=$value;
if ($id=='browserLocaleDateFormat') {
	$_SESSION['browserLocaleDateFormatJs']=str_replace(array('D','Y'), array('d','y'), $value);
}

if (array_key_exists('userParamatersArray',$_SESSION)) {
	if (array_key_exists($id,$_SESSION['userParamatersArray'])) {
		$_SESSION['userParamatersArray'][$id]=$value;
	}
}

if (isset($_REQUEST['saveUserParam']) && $_REQUEST['saveUserParam']=='true') {
	Parameter::storeUserParameter($id, $value);
}
?>