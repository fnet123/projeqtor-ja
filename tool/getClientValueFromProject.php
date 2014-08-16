<?php
/** ============================================================================
 * Save some information to session (remotely).
 */

require_once "../tool/projeqtor.php";
$idProject=$_REQUEST['idProject'];

$proj=new Project($idProject);
echo $proj->idClient;
