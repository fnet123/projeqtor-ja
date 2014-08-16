<?php
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
  
  public function updateExpense() {
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
  	$this->simpleSave();
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