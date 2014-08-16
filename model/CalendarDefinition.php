<?php 
/* ============================================================================
 * Stauts defines list stauts an activity or action can get in (lifecylce).
 */  
require_once('_securityCheck.php'); 
class CalendarDefinition extends SqlElement {

  // extends SqlElement, so has $id
  public $_col_1_2_Description;
  public $id;    // redefine $id to specify its visible place 
  public $name;
  //public $sortOrder=0;
  public $idle;
  public $_col_2_2_Year;
  public $_spe_year;
  public $_spe_copyFromDefault;
  public $_col_1_1_Calendar;
  public $_spe_calendar;
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="10%"># ${id}</th>
    <th field="name" width="60%">${name}</th>
    <th field="idle" width="5%" formatter="booleanFormatter">${idle}</th>
    ';
  private static $_fieldsAttributes=array("sortOrder"=>"hidden",
  		"idle"=>"hidden");
  
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
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
  	return self::$_fieldsAttributes;
  }
  /** ==========================================================================
   * Return the specific layout
   * @return the layout
   */
  protected function getStaticLayout() {
    return self::$_layout;
  }
  
  public function drawSpecificItem($item){
  	//scriptLog("Project($this->id)->drawSpecificItem($item)");
  	$result="";
  	$cal=new Calendar;
  	$currentYear=date('Y');
  	if ($item=='calendar') {
  		//$result.='<div id="viewCalendarDiv" dojoType="dijit.layout.ContentPane" region="top">';  		
      $cal->setDates($currentYear.'-01-01');
      $cal->idCalendarDefinition=$this->id;
      $result= $cal->drawSpecificItem('calendarView');
      //$result.='</div>';
  		return $result;
  	} else if ($item=='year') {
  		$result.='<div style="width:70px; text-align: center; color: #000000;" dojoType="dijit.form.NumberSpinner"'
  		 . ' constraints="{min:2000,max:2100,places:0,pattern:\'###0\'}" intermediateChanges="true" maxlength="4" '
       . ' value="'. $currentYear.'" smallDelta="1" id="calendartYearSpinner" name="calendarYearSpinner" >'
  		 . ' <script type="dojo/method" event="onChange" >'
  		 . ' 	loadContent("../tool/saveCalendar.php?idCalendarDefinition='.$this->id.'&year="+this.value,"CalendarDefinition_Calendar");'
  		 . ' </script>'
  		 . '</div>';
  		 return $result;
  	} else if ($item=='copyFromDefault') {
  		if ($this->id!=1) {
  		  $result.='<div type="button" dojoType="dijit.form.Button" showlabel="true">'
  			. i18n('copyFromCalendar')	
  		  . ' <script type="dojo/method" event="onClick" >'
  			. ' 	loadContent("../tool/saveCalendar.php?copyYearFrom="+dijit.byId("calendarCopyFrom").get("value")+"&idCalendarDefinition='.$this->id.'&year="+dijit.byId("calendartYearSpinner").get("value"),"CalendarDefinition_Calendar");'
  			. ' </script>'
  			. '</div>';
  		  $result.='<select dojoType="dijit.form.FilteringSelect" class="input" xlabelType="html" '
				. '  style="width:150px;" name="calendarCopyFrom" id="calendarCopyFrom" >';
  		  ob_start();
				htmlDrawOptionForReference('idCalendarDefinition', 1, null, true);
				$result.=ob_get_clean();
				$result.= '</select>';
  		}		
  	}
  	
  	return $result;
  }
  
  public function deleteControl() {
  	$result="";
  	if ($this->id==1)	{
  		$result .= "<br/>" . i18n("errorDeleteDefaultCalendar");
  	}
  	if (! $result) {
  		$result=parent::deleteControl();
  	}
  	return $result;
  }
}
?>