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

/** ============================================================================
 * Project is the main object of the project managmement.
 * Almost all other objects are linked to a given project.
 */ 
require_once('_securityCheck.php');
class Product extends SqlElement {

  // List of fields that will be exposed in general user interface
  public $_col_1_2_Description;
  public $id;    // redefine $id to specify its visible place 
  public $name;
  public $designation;
  public $idClient;
  public $idContact;
  public $idProduct;
  public $creationDate;
  public $idle;
  public $description;
  public $_col_2_2_Versions;
  public $_spe_versions;
  public $_Attachement=array();
  public $_Note=array();

  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="10%" ># ${id}</th>
    <th field="name" width="25%" >${productName}</th>
  	<th field="designation" width="15%" >${designation}</th>
    <th field="nameProduct" width="25%" >${isSubProductOf}</th>
    <th field="nameClient" width="15%" >${clientName}</th>
    <th field="idle" width="10%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("name"=>"required"
  );   

  private static $_colCaptionTransposition = array('idContact'=>'contractor','idProduct'=>'isSubProductOf'
  );
  
  
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
  /** ============================================================================
   * Return the specific colCaptionTransposition
   * @return the colCaptionTransposition
   */
  protected function getStaticColCaptionTransposition($fld) {
    return self::$_colCaptionTransposition;
  }  
  
    /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
  }
// ============================================================================**********
// GET VALIDATION SCRIPT
// ============================================================================**********
  
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo frameword)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);
    return $colScript;
  }
  
// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  

  /** =========================================================================
   * Draw a specific item for the current class.
   * @param $item the item. Correct values are : 
   *    - subprojects => presents sub-projects as a tree
   * @return an html string able to display a specific item
   *  must be redefined in the inherited class
   */
  public function drawSpecificItem($item){
    $result="";
    if ($item=='versions') {
      $result .="<table><tr><td class='label' valign='top'><label>" . i18n('versions') . "&nbsp;:&nbsp;</label>";
      $result .="</td><td>";
      if ($this->id) {
        $vers=new Version();
        $crit=array('idProduct'=>$this->id);
      	$result .= $vers->drawVersionsList($crit);
      }
      $result .="</td></tr></table>";
      return $result;
    } 
  }
  
  /** =========================================================================
   * control data corresponding to Model constraints
   * @param void
   * @return "OK" if controls are good or an error message 
   *  must be redefined in the inherited class
   */
  public function control(){
    $result="";
    if ($this->id and $this->id==$this->idProduct) {
      $result.='<br/>' . i18n('errorHierarchicLoop');
    } else if ($this->idProduct){
    	$parent=new Product($this->idProduct);
    	while ($parent->id) {
    	  if ($parent->id==$this->id) {
          $result.='<br/>' . i18n('errorHierarchicLoop');
          break;
        }
        $parent=new Product($parent->idProduct);
    	}
    }
        
    $defaultControl=parent::control();
    if ($defaultControl!='OK') {
      $result.=$defaultControl;
    }
    if ($result=="") {
      $result='OK';
    }
    return $result;
  }
  
  public function getSubProducts($limitToActiveProducts=false) {
  	// TODO : not tested !!!
    if ($this->id==null or $this->id=='') {
      return array();
    }
    $crit=array();
  	$crit['idProduct']=$this->id;
    if ($limitToActiveProducts) {$crit['idle']='0';}
    $sorted=SqlList::getListWithCrit('Product',$crit,'name');
  	$subProducts=array();
    foreach($sorted as $prodId=>$prodName) {
      $subProducts[$prodId]=new Product($prodId);
    }
    return $subProducts;
  }
  public function getSubProductsList($limitToActiveProducts=false) {
    if ($this->id==null or $this->id=='') {
      return array();
    }
    $crit=array();
    $crit['idProduct']=$this->id;
    if ($limitToActiveProducts) {$crit['idle']='0';}
    $sorted=SqlList::getListWithCrit('Product',$crit,'name');
    return $sorted;
  }
  
  /** ==========================================================================
   * Recusively retrieves all the hierarchic sub-products of the current product
   * @return an array containing id, name, subproducts (recursive array)
   */
  public function getRecursiveSubProducts($limitToActiveProducts=false) {
  	// TODO : not tested !!!
    $crit=array('idProduct'=>$this->id);
    if ($limitToActiveProducts) {
      $crit['idle']='0';
    }
    $obj=new Product();
    $subProducts=$obj->getSqlElementsFromCriteria($crit, false) ;
    $subProductList=null;
    foreach ($subProducts as $subProd) {
      $recursiveList=null;
      $recursiveList=$subProd->getRecursiveSubProducts($limitToActiveProducts);
      $arrayProd=array('id'=>$subProd->id, 'name'=>$subProd->name, 'subItems'=>$recursiveList);
      $subProductList[]=$arrayProd;
    }
    return $subProductList;
  }
  
  /** ==========================================================================
   * Recusively retrieves all the sub-Products of the current Product
   * and presents it as a flat array list of id=>name
   * @return an array containing the list of subProducts as id=>name 
   */
  public function getRecursiveSubProductsFlatList($limitToActiveProducts=false, $includeSelf=false) {
  	$tab=$this->getSubProductsList($limitToActiveProducts);
    $list=array();
    if ($includeSelf) {
      $list[$this->id]=$this->name;
    }
    if ($tab) {
      foreach($tab as $id=>$name) {
        $list[$id]=$name;
        $subobj=new Product();
        $subobj->id=$id;
        $sublist=$subobj->getRecursiveSubProductsFlatList($limitToActiveProducts);
        if ($sublist) {
          $list=array_merge_preserve_keys($list,$sublist);
        }
      }
    }
    return $list;
  }

}
?>