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
 * Planning element is an object included in all objects that can be planned.
 */ 
require_once('_securityCheck.php');
class ProjectPlanningElement extends PlanningElement {

  public $id;
  public $idProject;
  public $refType;
  public $refId;
  public $refName;
  public $_tab_10_7 = array('requested', 'validated', 'assigned', 'planned', 'real', 'left', '', '', '', '', 'startDate', 'endDate', 'duration', 'work', 'resourceCost', 'expense', 'totalCost');
  public $initialStartDate;
  public $validatedStartDate;
  public $_void_13;
  public $plannedStartDate;
  public $realStartDate;
  public $_void_16;
  public $_label_priority;
  public $priority;
  public $_void_19;
  public $_void_10;
  public $initialEndDate;
  public $validatedEndDate;
  public $_void_23;
  public $plannedEndDate;
  public $realEndDate;
  public $_void_26;
  public $_void_27;
  public $_void_28;
  public $_void_29;
  public $_void_20;
  public $initialDuration;
  public $validatedDuration;
  public $_void_33;
  public $plannedDuration;
  public $realDuration;
  public $_void_36;
  public $_label_wbs;
  public $wbs;
  public $_void_39;
  public $_void_30;
  public $_void_41;
  public $validatedWork;
  public $assignedWork;
  public $plannedWork;
  public $realWork;
  public $leftWork;
  public $_label_progress;
  public $progress;
  public $_label_expected;
  public $expectedProgress;
  public $_void_51;
  public $validatedCost;
  public $assignedCost;
  public $plannedCost;
  public $realCost;
  public $leftCost;
  public $_void_57;
  public $_void_58;
  public $_void_59;
  public $_void_50;
  public $_void_61;
  public $expenseValidatedAmount;
  public $expenseAssignedAmount;
  public $expensePlannedAmount;
  public $expenseRealAmount;
  public $expenseLeftAmount;
  public $_void_67;
  public $_void_68;
  public $_void_69;
  public $_void_60;
  public $_void_71;
  public $totalValidatedCost;
  public $totalAssignedCost;
  public $totalPlannedCost;
  public $totalRealCost;
  public $totalLeftCost;
  public $_void_77;
  public $_void_78;
  public $_void_79;
  public $_void_70;
  
  public $wbsSortable;
  public $topId;
  public $topRefType;
  public $topRefId;
  public $idle;
  private static $_fieldsAttributes=array(
    "plannedStartDate"=>"readonly,noImport",
    "realStartDate"=>"readonly,noImport",
    "plannedEndDate"=>"readonly,noImport",
    "realEndDate"=>"readonly,noImport",
    "plannedDuration"=>"readonly,noImport",
    "realDuration"=>"readonly,noImport",
    "initialWork"=>"hidden,noImport",
    "plannedWork"=>"readonly,noImport",
  	"notPlannedWork"=>"hidden",
    "realWork"=>"readonly,noImport",
    "leftWork"=>"readonly,noImport",
    "assignedWork"=>"readonly,noImport",
    "idPlanningMode"=>"hidden,noImport",
  	"expenseAssignedAmount"=>"readonly,noImport",
  	"expensePlannedAmount"=>"readonly,noImport",
  	"expenseRealAmount"=>"readonly,noImport",
  	"expenseLeftAmount"=>"readonly,noImport",
  	"totalAssignedCost"=>"readonly,noImport",
  	"totalPlannedCost"=>"readonly,noImport",
  	"totalRealCost"=>"readonly,noImport",
  	"totalLeftCost"=>"readonly,noImport",
  	"totalValidatedCost"=>"readonly,noImport",
  );   
  
  private static $_databaseTableName = 'planningelement';
  
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

    /** ========================================================================
   * Return the specific databaseTableName
   * @return the databaseTableName
   */
  protected function getStaticDatabaseTableName() {
    $paramDbPrefix=Parameter::getGlobalParameter('paramDbPrefix');
    return $paramDbPrefix . self::$_databaseTableName;
  }
    
  public function save() {
  	$this->updateTotal();
  	return parent::save();
  }
  
  public function updateTotal() {
  	$this->totalAssignedCost=$this->assignedCost+$this->expenseAssignedAmount;
  	$this->totalLeftCost=$this->leftCost+$this->expenseLeftAmount;
  	$this->totalPlannedCost=$this->plannedCost+$this->expensePlannedAmount;
  	$this->totalRealCost=$this->realCost+$this->expenseRealAmount;
  	$this->totalValidatedCost=$this->validatedCost+$this->expenseValidatedAmount;
  }
  
  protected function updateSynthesisObj ($doNotSave=false) {
  	$this->updateSynthesisProject($doNotSave);
  }
  protected function updateSynthesisProject ($doNotSave=false) {
  	parent::updateSynthesisObj(true); // Will update work and resource cost, but not save yet ;)
  	$this->updateExpense(true); // Will retrieve expense directly on the project
  	$consolidateValidated=Parameter::getGlobalParameter('consolidateValidated');
  	$this->_noHistory=true;
  	// Add expense data from other planningElements
  	$validatedExpense=0;
  	$assignedExpense=0;
  	$plannedExpense=0;
  	$realExpense=0;
  	$leftExpense=0;
  	if (! $this->elementary) {
  		$critPla=array("topId"=>$this->id);
  		$planningElement=new ProjectPlanningElement();
  		$plaList=$planningElement->getSqlElementsFromCriteria($critPla, false);
  		// Add data from other planningElements dependant from this one
  		foreach ($plaList as $pla) {  			
  			if (!$pla->cancelled and $pla->expenseValidatedAmount) $validatedExpense+=$pla->expenseValidatedAmount;
  			if (!$pla->cancelled and $pla->expenseAssignedAmount) $assignedExpense+=$pla->expenseAssignedAmount;
  			if (!$pla->cancelled and $pla->expensePlannedAmount) $plannedExpense+=$pla->expensePlannedAmount;
  		  $realExpense+=$pla->expenseRealAmount;
  			if (!$pla->cancelled and $pla->expenseLeftAmount) $leftExpense+=$pla->expenseLeftAmount;
  		}
  	}
  	// save cumulated data
  	$this->expenseAssignedAmount+=$assignedExpense;
  	$this->expensePlannedAmount+=$plannedExpense;
  	$this->expenseRealAmount+=$realExpense;
  	$this->expenseLeftAmount+=$leftExpense;
  	if ($consolidateValidated=="ALWAYS") {
  		$this->expenseValidatedAmount=$validatedExpense;
  	} else if ($consolidateValidated=="IFSET") {
  		if ($validatedExpense) {
  			$this->expenseValidatedAmount=$validatedExpense;
  		}
  	}
  	$this->save();
  	// Dispath to top element
  	if ($this->topId) {
  		self::updateSynthesis($this->topRefType, $this->topRefId);
  	}
  }
  
  public function updateExpense($doNotSave=false) {
  	$exp=new Expense();
  	$lstExp=$exp->getSqlElementsFromCriteria(array('idProject'=>$this->refId));
  	$assigned=0;
  	$real=0;
  	$planned=0;
  	$left=0;
  	foreach ($lstExp as $exp) {
  		if ($exp->plannedAmount) {
  			$assigned+=$exp->plannedAmount;
  		}
  		if ($exp->realAmount) {
  			$real+=$exp->realAmount;
  		} else {
  			if ($exp->plannedAmount) {
  				$left+=$exp->plannedAmount;
  			}
  		}
  	}
  	$planned=$real+$left;
  	$this->expenseAssignedAmount=$assigned;
  	$this->expenseLeftAmount=$left;
  	$this->expensePlannedAmount=$planned;
  	$this->expenseRealAmount=$real;
  	$this->updateTotal();
  	if (! $doNotSave) {
  		$this->simpleSave();
  		if ($this->topId) {
  			self::updateSynthesis($this->topRefType, $this->topRefId);
  		}
  	}
  }
  /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return array_merge(parent::getStaticFieldsAttributes(),self::$_fieldsAttributes);
  }
  
  public function getValidationScript($colName) {
  	$colScript = parent::getValidationScript($colName);
  	if ($colName=='validatedCost' or $colName=='expenseValidatedAmount') {
	  	$colScript .= '<script type="dojo/connect" event="onChange" >';
	  	$colScript .= '  if (dijit.byId("' . get_class($this) . '_totalValidatedCost")) {';
	  	$colScript .= '    var cost=dijit.byId("' . get_class($this) . '_validatedCost").get("value");';
	  	$colScript .= '    var expense=dijit.byId("' . get_class($this) . '_expenseValidatedAmount").get("value");';
	  	$colScript .= '    if (!cost) cost=0;';
	  	$colScript .= '    if (!expense) expense=0;';
	  	$colScript .= '    var total = cost+expense;';
	  	$colScript .= '    dijit.byId("' . get_class($this) . '_totalValidatedCost").set("value",total);';
	  	$colScript .= '    formChanged();';
	  	$colScript .= '  }';
	  	$colScript .= '</script>';
  	}
  	return $colScript;
  }
  
}
?>