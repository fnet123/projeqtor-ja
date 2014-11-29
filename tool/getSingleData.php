<?PHP
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
 * Get the list of objects, in Json format, to display the grid list
 */
    require_once "../tool/projeqtor.php"; 
    scriptLog('   ->/tool/getSingleData.php');
    $type=$_REQUEST['dataType'];
    if ($type=='resourceCost') {
      $idRes=$_REQUEST['idResource'];
      if (! $idRes) return;
      $idRol=$_REQUEST['idRole'];
      if (! $idRol) return;
      $r=new Resource($idRes);
      // #303
      //echo htmlDisplayNumeric($r->getActualResourceCost($idRol));
      echo $r->getActualResourceCost($idRol);
    } else if ($type=='resourceRole') {
      $idRes=$_REQUEST['idResource'];
      if (! $idRes) return;
      $r=new Resource($idRes);
      echo $r->idRole;
    } else {    
      echo '';
    } 
?>
