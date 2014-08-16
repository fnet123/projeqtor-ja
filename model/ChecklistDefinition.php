<?php 
/* ============================================================================
 * Habilitation defines right to the application for a menu and a profile.
 */  
require_once('_securityCheck.php'); 
class ChecklistDefinition extends SqlElement {

  // extends SqlElement, so has $id
  public $_col_1_2_description;
  public $id;    // redefine $id to specify its visible place 
  public $name;
  public $idChecklistable;
  public $nameChecklistable;
  public $idType;
  //public $lineCount;

  public $idle;
  public $_col_2_2;
  
  public $_col_1_1;
  public $_ChecklistDefinitionLine=array();
  public $_noCopy;
    
    private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%" ># ${id}</th>
    <th field="nameChecklistable" formatter="translateFormatter" width="20%" >${element}</th>
    <th field="nameType" width="20%" >${type}</th>
    <th field="lineCount" formatter="numericFormatter" width="10%" >${lineCount}</th>
    <th field="idle" width="5%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("name"=>"hidden",
                                  "idType"=>"nocombo",
                                  "nameChecklistable"=>"hidden",
  		                            //"lineCount"=>"readonly"
  );  
  
    private static $_colCaptionTransposition = array('idType'=>'type', 'idChecklistable'=>'element');
  
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
  
// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  
  public function save() {
  	$Checklistable=new Checklistable($this->idChecklistable);
  	$this->nameChecklistable=$Checklistable->name;
  	return parent::save();
  }
  
    /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo framework)
   */
  public function getValidationScript($colName) {
  	$colScript = parent::getValidationScript($colName);
  	if ($colName=='idChecklistable') {
  		$colScript .= '<script type="dojo/connect" event="onChange" args="evt">';
  		$colScript .= '  dijit.byId("idType").set("value",null);';
  		$colScript .= '  refreshList("idType","scope", checklistableArray[this.value]);';
  		$colScript .= '</script>';
  	}
    return $colScript;
  }
  
  public function control(){
    $result="";
    if (! trim($this->idChecklistable)) {
    	$result.='<br/>' . i18n('messageMandatory',array(i18n('colElement')));
    }

    $crit=array('idChecklistable'=>trim($this->idChecklistable),
                'idType'=>trim($this->idType));
    $elt=SqlElement::getSingleSqlElementFromCriteria('ChecklistDefinition', $crit);
    if ($elt and $elt->id and $elt->id!=$this->id) {
      $result.='<br/>' . i18n('errorDuplicateChecklistDefinition');
    }
    $defaultControl=parent::control();
    if ($defaultControl!='OK') {
      $result.=$defaultControl;
    }if ($result=="") {
      $result='OK';
    }
    return $result;
  }
  
}
?>