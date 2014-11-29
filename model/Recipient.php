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
 * defines recipient for a bill
 */ 
require_once('_securityCheck.php');
class Recipient extends SqlElement {

  // extends SqlElement, so has $id
  public $_col_1_2_description;
  public $id;    // redefine $id to specify its visible place 
  public $name;
  public $companyNumber;
  public $numTax;  
  public $taxFree;
  public $idle;
  public $_sec_IBAN;
  public $bank;
  public $ibanCountry;
  public $ibanKey;
  public $ibanBban;
  public $_col_2_2_Address;
  public $designation;
  public $street;
  public $complement;
  public $zip;
  public $city;
  public $state;
  public $country;  
  //public $_spe_projects;
  //public $_sec_Contacts;
  //public $_spe_contacts;
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%"># ${id}</th>
    <th field="name" width="20%">${name}</th>
    <th field="companyNumber" width="20%">${companyNumber}</th>
    <th field="numTax" width="20%">${numTax}</th>
    <th field="bank" width="10%">${bank}</th>
    <th field="idle" formatter="booleanFormatter" width="5%">${idle}</th>
    ';
  
  private static $_fieldsAttributes=array("name"=>"required");

  
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
  
/** =========================================================================
   * Draw a specific item for the current class.
   * @param $item the item. Correct values are : 
   *    - subprojects => presents sub-projects as a tree
   * @return an html string able to display a specific item
   *  must be redefined in the inherited class
   */
  public function drawSpecificItem($item){
    $result="";
    if ($item=='projects') {
      $prj=new Project();
      $result .="<table><tr><td class='label' valign='top'><label>" . i18n('projects') . "&nbsp;:&nbsp;</label>";
      $result .="</td><td>";
      $result .= $prj->drawProjectsList(array('idRecipient'=>$this->id,'idle'=>'0'));
      $result .="</td></tr></table>";
      return $result;
    } else if ($item=='contacts') {
      $con=new Contact();
      $result .="<table><tr><td class='label' valign='top'><label>" . i18n('contacts') . "&nbsp;:&nbsp;</label>";
      $result .="</td><td>";
      $result .= $con->drawContactsList(array('idRecipient'=>$this->id,'idle'=>'0'));
      $result .="</td></tr></table>";
      return $result;
    }
  }
  
    /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo framework)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);

    if ($colName=="ibanCountry") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  calculateIbanKey();';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=="ibanBban") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  calculateIbanKey(); ';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } 
    return $colScript;
  }
  
}
?>