<?php
/** ============================================================================
 * 
 */
require_once "../tool/projeqtor.php";
require_once "../external/phpAES/aes.class.php";
require_once "../external/phpAES/aesctr.class.php";
$username="";
if (isset($_REQUEST['username'])) {
	$username=$_REQUEST['username'];
	$username=AesCtr::decrypt($username, md5(session_id()), 256);	
}
$crit=array('name'=>$username);
$user=SqlElement::getSingleSqlElementFromCriteria('User', $crit);
$sessionSalt=md5("projeqtor".date('YmdHis'));
$_SESSION['sessionSalt']=$sessionSalt;
if (isset($user->crypto) and ! $user->isLdap) {
  echo $user->crypto.";".$user->salt.";".$sessionSalt;
} else {
	echo ";;".$sessionSalt;
}