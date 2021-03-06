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
 * Chek login/password entered in connection screen
 */
  require_once "../tool/projeqtor.php"; 
scriptLog("changePassword.php");  
  $password="";
  if (array_key_exists('password',$_POST)) {
    $password=$_POST['password'];
  }    
  $userSalt=$_POST['userSalt'];
  if ($password=="") {
    passwordError();
  }
  if ($password==hash('sha256',Parameter::getGlobalParameter('paramDefaultPassword').$userSalt)) {
    passwordError();
  }
  $user=$_SESSION['user'];
  if ( ! $user ) {
   passwordError();
  } 
  if ( ! $user->id) {
    passwordError();
  } 
  if ( $user->idle!=0) {
    passwordError();
  } 
  if ($user->isLdap<>0) {
    passwordError();
  } 
  $passwordLength=$_POST['passwordLength'];
  if ($passwordLength<Parameter::getGlobalParameter('paramPasswordMinLength')) {
    passwordError();
  }
  
  changePassword($user, $password, $userSalt, 'sha256');
  
  /** ========================================================================
   * Display an error message because of invalid login
   * @return void
   */
  function passwordError() {
    echo '<span class="messageERROR">';
    echo i18n('invalidPasswordChange', array(Parameter::getGlobalParameter('paramPasswordMinLength')));
    echo '</span>';
    exit;
  }
  
   /** ========================================================================
   * Valid login
   * @param $user the user object containing login information
   * @return void
   */
  function changePassword ($user, $newPassword, $salt, $crypto) {
  	Sql::beginTransaction();
    //$user->password=md5($newPassword); password is encryted in JS
    $user->password=$newPassword;
    $user->salt=$salt;
    $user->crypto=$crypto;
    $user->passwordChangeDate=date('Y-m-d');
    $result=$user->save();
		if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {
		  Sql::rollbackTransaction();
		  echo '<span class="messageERROR" >' . $result . '</span>';
		} else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
		  Sql::commitTransaction();
		  $_SESSION['user']=$user;
		  echo '<span class="messageOK">';
	    echo i18n('passwordChanged');
	    echo '<div id="validated" name="validated" type="hidden"  dojoType="dijit.form.TextBox">OK';
	    echo '</div>';
	    echo '</span>';
		} else { 
		  Sql::rollbackTransaction();
		  echo '<span class="messageWARNING" >' . $result . '</span>';
		}
  }
  
?>