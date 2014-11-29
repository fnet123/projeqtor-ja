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

/** ===========================================================================
 * Display the column selector div
 */

require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/planningColumnSelector');

$columns=Parameter::getPlanningColumnOrder();
$columnsAll=Parameter::getPlanningColumnOrder(true);
//asort($columns);
//$pe=new ProjectPlanningElement();
//$pe->setVisibility();
//$workVisibility=$pe->_workVisibility;
//$costVisibility=$pe->_costVisibility;    
foreach ($columnsAll as $order=>$col) {
	if ( (isset($resourcePlanning) and ($col=='ValidatedWork' or $col=='Resource' ) )
	  or (isset($portfolioPlanning) and ($col=='Priority' or $col=='Resource' or $col=='IdPlanningMode') )	) {
	  // noting	
	} else if ( ! SqlElement::isVisibleField($col) ) {
		// noting 
	} else {
		echo '<div class="dojoDndItem" id="columnSelector'.$col.'" dndType="planningColumn">';
		echo '<span class="dojoDndHandle handleCursor"><img style="width:6px" src="css/images/iconDrag.gif" />&nbsp;&nbsp;</span>';
	  echo '<span dojoType="dijit.form.CheckBox" type="checkbox" id="checkColumnSelector'.$col.'" ' 
	    . ((substr($columns[$order],0,6)!='Hidden')?' checked="checked" ':'') 
	    . ' onChange="changePlanningColumn(\'' . $col . '\',this.checked,\'' . $order . '\')" '
	    . '></span><label for="checkColumnSelector'.$col.'" class="checkLabel">';
	  echo '&nbsp;';
	  echo i18n('col' . $col) . "</label>";
	  echo '</div>';
	}
}

?>