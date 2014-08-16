<?php 
/** ============================================================================
 * Action is establised during meeting, to define an action to be followed.
 */ 
require_once('_securityCheck.php');
class QuotationMain extends SqlElement {

  // List of fields that will be exposed in general user interface
  public $_col_1_2_description;
  public $id;    // redefine $id to specify its visible place 
  public $reference;
  public $idProject;
  public $idQuotationType;
  public $name;
  public $idUser;
  public $creationDate;
  public $Origin;
  public $idClient;
  public $idContact;
  public $description;
  public $additionalInfo;
  public $_col_2_2_treatment;
  public $idStatus;
  public $idResource;  
  public $sendDate;
  public $validityEndDate;
  public $handled;
  public $handledDate;
  public $done;
  public $doneDate;
  public $idle;
  public $idleDate;
  public $cancelled;
  public $_lib_cancelled;
  public $initialWork;
  public $initialPricePerDayAmount;
  public $initialAmount; 
  public $initialEndDate;
  public $idActivityType;
  public $comment;
  public $_col_1_1_Link;
  public $_Link=array();
  public $_Attachement=array();
  public $_Note=array();
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="4%" ># ${id}</th>
    <th field="nameProject" width="10%" >${idProject}</th>
    <th field="nameClient" width="7%" >${idClient}</th>
    <th field="nameQuotationType" width="7%" >${idQuotationType}</th>
    <th field="name" width="20%" >${name}</th>
    <th field="colorNameStatus" width="10%" formatter="colorNameFormatter">${idStatus}</th>
    <th field="nameResource" width="8%" >${responsible}</th>
    <th field="validityEndDate" width="8%" formatter="dateFormatter" >${offerValidityEndDate}</th>
  	<th field="initialWork" formatter="workFormatter" width="7%" >${validatedWork}</th>
  	<th field="initialAmount" formatter="costFormatter" width="7%" >${validatedAmount}</th>
  	<th field="handled" width="4%" formatter="booleanFormatter" >${handled}</th>
    <th field="done" width="4%" formatter="booleanFormatter" >${done}</th>
    <th field="idle" width="4%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("id"=>"nobr", 
  		                            "idProject"=>"required",
  		                            "reference"=>"readonly",
                                  "name"=>"required", 
                                  "idOrderType"=>"required",
                                  "idStatus"=>"required",
                                  "handled"=>"nobr",
                                  "done"=>"nobr",
                                  "idle"=>"nobr",
  								                "idleDate"=>"nobr",
                                  "cancelled"=>"nobr"
  );  
  
  private static $_colCaptionTransposition = array('idUser'=>'issuer', 
                                                   'idResource'=> 'responsible',
  		                      'validityEndDate'=>'offerValidityEndDate',
  													'idActivity'=>'linkActivity',
  		                      'initialEndDate'=>'plannedEndDate',
  		                      'initialWork'=>'plannedWork',
  		                      'initialAmount'=>'plannedAmount',
  		                      'initialPricePerDayAmount'=>'pricePerDay');
  
//  private static $_databaseColumnName = array('idResource'=>'idUser');
    
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
// GET STATIC DATA FUNCTIONS
// ============================================================================**********
  
  /** ==========================================================================
   * Return the specific layout
   * @return the layout
   */
  protected function getStaticLayout() {
    return self::$_layout;
  }
  
  /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
  }
  
  /** ============================================================================
   * Return the specific colCaptionTransposition
   * @return the colCaptionTransposition
   */
  protected function getStaticColCaptionTransposition($fld) {
    return self::$_colCaptionTransposition;
  }

  /** ========================================================================
   * Return the specific databaseColumnName
   * @return the databaseTableName
   */
  /**protected function getStaticDatabaseColumnName() {
    return self::$_databaseColumnName;
  }
  */

/** =========================================================================
   * control data corresponding to Model constraints
   * @param void
   * @return "OK" if controls are good or an error message 
   *  must be redefined in the inherited class
   */
  public function control(){
    $result="";
   		
   	$defaultControl=parent::control();
  	
   	if ($defaultControl!='OK') {
   		$result.=$defaultControl;
   	}
   	
   	if ($result=="") $result='OK';
	
    return $result;
  }

  
  /** =========================================================================
   * Overrides SqlElement::deleteControl() function to add specific treatments
   * @see persistence/SqlElement#deleteControl()
   * @return the return message of persistence/SqlElement#deleteControl() method
   */  
  
  /**=========================================================================
   * Overrides SqlElement::save() function to add specific treatments
   * @see persistence/SqlElement#save()
   * @return the return message of persistence/SqlElement#save() method
   */
  public function save() {
  	$result='';
  	
    if (trim($this->id)=='') {
    	// fill the creatin date if it's empty - creationDate is not empty for import ! 
    	if ($this->creationDate=='') $this->creationDate=date('Y-m-d H:i');
	  }

    $this->name=trim($this->name);
    
	  $result = parent::save();
    return $result;
  }
  
    /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo frameword)
   */
  public function getValidationScript($colName) {
    
    $colScript = parent::getValidationScript($colName);
    if ($colName=="initialWork") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      //$colScript .= '  if ( ! testAllowedChange(this.value) ) return;';
      $colScript .= '  var initialWork=this.value;';
      $colScript .= '  if (paramWorkUnit!="days") initialWork=initialWork/paramHoursPerDay;';
      $colScript .= '  var initialPricePerDayAmount=dijit.byId("initialPricePerDayAmount").get("value");';
      $colScript .= '  var initialAmount=dijit.byId("initialAmount").get("value");';
      $colScript .= '  if (initialPricePerDayAmount) {';
      $colScript .= '    initialAmount=Math.round(initialPricePerDayAmount*initialWork*100)/100;';
      $colScript .= '    dijit.byId("initialAmount").set("value",initialAmount)';
      $colScript .= '  } else if (initialWork){';
      $colScript .= '    initialPricePerDayAmount=Math.round(initialAmount/initialWork*100)/100; ';
      $colScript .= '    dijit.byId("initialPricePerDayAmount").set("value",initialPricePerDayAmount)';
      $colScript .= '  }';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=="initialPricePerDayAmount") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      //$colScript .= '  if ( ! testAllowedChange(this.value) ) return;';
      $colScript .= '  var initialWork=dijit.byId("initialWork").get("value");';
      $colScript .= '  if (paramWorkUnit!="days") initialWork=initialWork/paramHoursPerDay;';
      $colScript .= '  var initialPricePerDayAmount=this.value;';
      $colScript .= '  var initialAmount=dijit.byId("initialAmount").get("value");';
      $colScript .= '  if (initialWork) {';
      $colScript .= '    initialAmount=Math.round(initialPricePerDayAmount*initialWork*100)/100;';
      $colScript .= '    dijit.byId("initialAmount").set("value",initialAmount)';
      $colScript .= '  } else if (initialAmount){';
      $colScript .= '    initialWork=initialAmount/initialPricePerDayAmount;';
      $colScript .= '    if (paramWorkUnit!="days") initialWork=initialWork/paramHoursPerDay;';
      $colScript .= '    initialWork=Math.round(initialWork*10)/10; ';
      $colScript .= '    dijit.byId("initialWork").set("value",initialWork)';
      $colScript .= '  }';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';    	
    } else if ($colName=="initialAmount") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      //$colScript .= '  if ( ! testAllowedChange(this.value) ) return;';
      $colScript .= '  var initialWork=dijit.byId("initialWork").get("value");';
      $colScript .= '  if (paramWorkUnit!="days") initialWork=initialWork/paramHoursPerDay;';
      $colScript .= '  var initialPricePerDayAmount=dijit.byId("initialPricePerDayAmount").get("value");';
      $colScript .= '  var initialAmount=this.value;';
      $colScript .= '  if (initialWork) {';
      $colScript .= '    initialPricePerDayAmount=Math.round(initialAmount/initialWork*100)/100;';
      $colScript .= '    dijit.byId("initialPricePerDayAmount").set("value",initialPricePerDayAmount)';
      $colScript .= '  } else if (initialPricePerDayAmount){';
      $colScript .= '    initialWork=initialAmount/initialPricePerDayAmount;';
      $colScript .= '    if (paramWorkUnit!="days") initialWork=initialWork/paramHoursPerDay;';
      $colScript .= '    initialWork=Math.round(initialWork*10)/10; ';
      $colScript .= '    dijit.byId("initialWork").set("value",initialWork)';
      $colScript .= '  }';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=="idProject") {
    	$colScript .= '<script type="dojo/connect" event="onChange" >';
    	$colScript .= '  setClientValueFromProject("idClient",this.value);';
    	$colScript .= '  formChanged();';
    	$colScript .= '</script>';
    } else if ($colName=="idClient") {
    	$colScript .= '<script type="dojo/connect" event="onChange" >';
    	$colScript .= '  refreshList("idContact", "idClient", this.value, null, null, false);';
    	$colScript .= '  formChanged();';
    	$colScript .= '</script>';
    }   
    return $colScript;
  }
    
  private function zeroIfNull($value) {
  	$val = $value;
  	if (!$val || $val=='' || !is_numeric($val)) {
  		$val=0;
  	} else { 
  		$val=$val*1;
  	}
  	
  	return $val;
  	
  }
  
}
?>