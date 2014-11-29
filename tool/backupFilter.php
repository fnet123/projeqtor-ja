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
 * Save filter from User to Session to be able to restore it
 * Retores it if cancel is set
 * Cleans it if clean is set
 * The new values are fetched in $_REQUEST
 */

require_once "../tool/projeqtor.php";
//traceLog("backupFilter.php");

$user=$_SESSION['user'];

$comboDetail=false;
if (array_key_exists('comboDetail',$_REQUEST)) {
	$comboDetail=true;
}

if (! $comboDetail and ! $user->_arrayFilters) {
  $user->_arrayFilters=array();
} else if ($comboDetail and ! $user->_arrayFiltersDetail) {
  $user->_arrayFiltersDetail=array();
}


// Get the filter info
$cancel=false;
if (array_key_exists('cancel',$_REQUEST)) {
  $cancel=true;
}
$clean=false;
if (array_key_exists('clean',$_REQUEST)) {
  $clean=true;
}
$valid=false;
if (array_key_exists('valid',$_REQUEST)) {
  $valid=true;
}
$default=false;
if (array_key_exists('default',$_REQUEST)) {
  $default=true;
}

if (! array_key_exists('filterObjectClass',$_REQUEST)) {
  throwError('filterObjectClass parameter not found in REQUEST');
}
$filterObjectClass=$_REQUEST['filterObjectClass'];
$name="";
if (array_key_exists('filterName',$_REQUEST)) {
  $name=$_REQUEST['filterName'];
}

$filterName='stockFilter' . $filterObjectClass;
if ($cancel) {
  if (! $comboDetail) {
		if (array_key_exists($filterName,$_SESSION)) {
      $user->_arrayFilters[$filterObjectClass]=$_SESSION[$filterName];
	    $_SESSION['user']=$user;
	  } else {
	    if (array_key_exists($filterObjectClass, $user->_arrayFilters)) {
	      unset($user->_arrayFilters[$filterObjectClass]);
	      $_SESSION['user']=$user;
	    }
	  }
  } else {
    if (array_key_exists($filterName.'_Detail',$_SESSION)) {
      $user->_arrayFiltersDetail[$filterObjectClass]=$_SESSION[$filterName.'_Detail'];
      $_SESSION['user']=$user;
    } else {
      if (array_key_exists($filterObjectClass, $user->_arrayFiltersDetail)) {
        unset($user->_arrayFiltersDetail[$filterObjectClass]);
        $_SESSION['user']=$user;
      }
    }
  }
} 
if ($clean or $cancel or $valid) {
	if ($comboDetail) {
    if (array_key_exists($filterName,$_SESSION)) {
      unset($_SESSION[$filterName]);
    }
	} else {
	  if (array_key_exists($filterName.'_Detail',$_SESSION)) {
      unset($_SESSION[$filterName.'_Detail']);
    }
	}
}
if ( ! $clean and ! $cancel and !$valid) {
	if (! $comboDetail) {
	  if (array_key_exists($filterObjectClass,$user->_arrayFilters)) {
	    $_SESSION[$filterName]=$user->_arrayFilters[$filterObjectClass];
	  } else {
	    $_SESSION[$filterName]=array();
	  }
	} else {
    if (array_key_exists($filterObjectClass,$user->_arrayFiltersDetail)) {
      $_SESSION[$filterName.'_Detail']=$user->_arrayFiltersDetail[$filterObjectClass];
    } else {
      $_SESSION[$filterName.'_Detail']=array();
    }
  }
	
}

if ($valid or $cancel) {
  $user->_arrayFilters[$filterObjectClass . "FilterName"]=$name;
  $_SESSION['user']=$user;
}

?>