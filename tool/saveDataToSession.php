<?php
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

if (array_key_exists('userParamatersArray',$_SESSION)) {
	if (array_key_exists($id,$_SESSION['userParamatersArray'])) {
		$_SESSION['userParamatersArray'][$id]=$value;
	}
}

?>