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

/* ============================================================================
 * Habilitation defines right to the application for a menu and a profile.
 */ 
require_once('_securityCheck.php');
class Dependency extends SqlElement {

  // extends SqlElement, so has $id
  public $id;    // redefine $id to specify its visible place 
  public $predecessorId;
  public $predecessorRefType;
  public $predecessorRefId;
  public $successorId;
  public $successorRefType;
  public $successorRefId;
  public $dependencyType;
  public $dependencyDelay;
  
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL) {
    parent::__construct($id);
  }

  
   /** ==========================================================================
   * Destructor
   * @return void
   */ 
  function __destruct() {
    parent::__destruct();
  }

// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  

 /** =========================================================================
   * control data corresponding to Model constraints
   * @param void
   * @return "OK" if controls are good or an error message 
   *  must be redefined in the inherited class
   */
  public function control(){
  	if ($this->id) return "OK";
    $result="";
    $this->predecessorRefId=intval($this->predecessorRefId);
    $this->successorRefId=intval($this->successorRefId);
    // control duplicate
    $crit=array('successorRefType'=>$this->successorRefType, 'successorRefId'=>$this->successorRefId,
                'predecessorRefType'=>$this->predecessorRefType, 'predecessorRefId'=>$this->predecessorRefId);
    $list=$this->getSqlElementsFromCriteria($crit);
    if (count($list)>0) {
    	$result.='<br/>' . i18n('errorDuplicateDependency');
    }
    if ($this->predecessorId) { // Case PlanningElement Dependency
      $prec=new PlanningElement($this->predecessorId);
      $precList=$prec->getPredecessorItemsArray();
      $precParentList=$prec->getParentItemsArray();
      if (array_key_exists('#' . $this->successorId,$precList)) {
        $result.='<br/>' . i18n('errorDependencyLoop');
      }
      // cannot create dependency into parent hierarchy
	    if (array_key_exists('#' . $this->successorId,$precParentList)) {
	      $result.='<br/>' . i18n('errorDependencyHierarchy');
	    }
    } else {
    	$precList=$this->getPredecessorList();
    	$precParentList=array();
      if (array_key_exists($this->successorRefType . '#' . $this->successorRefId,$precList)) {
        $result.='<br/>' . i18n('errorDependencyLoop');
      }
    }
    if ($this->successorId) { // Case PlanningElement Dependency
      $succ=new PlanningElement($this->successorId);    
      $succList=$succ->getSuccessorItemsArray();
      $succParentList=$succ->getParentItemsArray();
      if (array_key_exists('#' .$this->predecessorId,$succList)) {
        $result.='<br/>' . i18n('errorDependencyLoop');
      }
      // cannot create dependency into parent hierarchy
	    if (array_key_exists('#' .$this->predecessorId,$succParentList)) {
	      $result.='<br/>' . i18n('errorDependencyHierarchy');
	    }
    } else {
    	$succList=array();
    	$succParentList=array();
      if (array_key_exists($this->predecessorRefType . '#' . $this->predecessorRefId,$succList)) {
        $result.='<br/>' . i18n('errorDependencyLoop');
      }
    } 
    if ($this->predecessorRefType==$this->successorRefType and $this->predecessorRefId==$this->successorRefId) {
      $result.='<br/>' . i18n('errorDependencyLoop');
    }
    // Must have write access to successor to create link
    $succClass=$this->successorRefType;
    if ($succClass and class_exists($succClass)) {  	
	    $succ=new $succClass($this->successorRefId);
	    $canUpdateSucc=(securityGetAccessRightYesNo('menu' . $succClass, 'update', $succ)=='YES');
	    if (! $canUpdateSucc) {
	    	$result.='<br/>' . i18n('errorUpdateRights');
	    }
    }
    $defaultControl=parent::control();
    if ($defaultControl!='OK') {
      $result.=$defaultControl;
    }if ($result=="") {
      $result='OK';
    } 
    return $result;
  }
  
  private function getPredecessorList() {
  	$crit=array('successorRefType'=>$this->predecessorRefType, 'successorRefId'=>$this->predecessorRefId);
  	$list=$this->getSqlElementsFromCriteria($crit, false, null, null, true);
  	$result=array();
  	foreach ($list as $obj) {
  		$result[$obj->predecessorRefType.'#'.$obj->predecessorRefId]=$obj;  
      if ($obj->id!=$this->id) {		
  	    $result=array_merge_preserve_keys($result,$obj->getPredecessorList());
      }
  	}
  	return $result;
  }
  
  private function getSuccessorList() {
    $crit=array('predecessorRefType'=>$this->successorRefType, 'predeccessorRefId'=>$this->succecessorRefId);
    $list=$this->getSqlElementsFromCriteria($crit, false, null, null, true);
    $result=array();
    foreach ($list as $obj) {
      $result[$obj->successorRefType.'#'.$obj->successorRefId]=$obj;  
      if ($obj->id!=$this->id) {    
        $result=array_merge_preserve_keys($result,$obj->getSuccessorList());
      }
    }
    return $result;    
  }
  
}
?>