<?php 
/** ============================================================================
 * Milestone is a target or entry key point
 */  
require_once('_securityCheck.php');
class Milestone extends MilestoneMain {

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


}
?>