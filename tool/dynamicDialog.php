<?php
include_once '../tool/projeqtor.php';
if (! array_key_exists('dialog', $_REQUEST)) {
	throwError('dialog parameter not found in REQUEST');
}
$dialog=$_REQUEST['dialog'];
//echo "<br/>".$dialog."<br/>";
$dialogFile="../tool/dynamic".ucfirst($dialog).'.php';
if (file_exists($dialogFile)) {
	include $dialogFile;
} else {
	echo "ERROR dialog=".$dialog." is not an expected dialog";
}