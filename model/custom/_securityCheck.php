<?php
if (! class_exists('SqlElement') ){
	if (file_exists('../tool/projeqtor.php')) {
	  include_once('../tool/projeqtor.php');
	} else if (file_exists('../../tool/projeqtor.php')) {
		include_once('../../tool/projeqtor.php');
	} else {
		exit;
	}
	// FIX FOR IIS
	if (!isset($_SERVER['REQUEST_URI'])) {
		$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
		if (isset($_SERVER['QUERY_STRING'])) { $_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING']; }
	}
	traceHack('Direct acces to class file '.$_SERVER['REQUEST_URI']);
	exit;
}?>