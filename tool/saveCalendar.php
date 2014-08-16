<?php
/* ============================================================================
 * List of parameter specific to a user.
 * Every user may change these parameters (for his own user only !).
 */
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/calendar.php');
  $user=$_SESSION['user'];
  $collapsedList=Collapsed::getCollaspedList();
  $currentYear=strftime("%Y") ;
  $idCalendarDefinition=0;
  if (isset($_REQUEST['year'])) {
    $currentYear=$_REQUEST['year'];
  }
  if (isset($_REQUEST['idCalendarDefinition'])) {
  	$idCalendarDefinition=$_REQUEST['idCalendarDefinition'];
  }
  if (isset($_REQUEST['copyYearFrom'])) {
  	$from=$_REQUEST['copyYearFrom'];
  	copyYear($from,$idCalendarDefinition, $currentYear);
  }
  if (isset($_REQUEST['day'])) {
    switchDay($_REQUEST['day'],$idCalendarDefinition);
    $currentYear=substr($_REQUEST['day'],0,4);
  }
  
  $cal=new Calendar;
  //$currentYear=date('YYYY');
  $cal->setDates($currentYear.'-01-01');
  $cal->idCalendarDefinition=$idCalendarDefinition;
  $result= $cal->drawSpecificItem('calendarView');
  echo $result;

function switchDay ($day,$idCalendarDefinition) {
  global $bankHolidays, $bankWorkdays;
  $cal=SqlElement::getSingleSqlElementFromCriteria('Calendar',array('calendarDate'=>$day, 'idCalendarDefinition'=>$idCalendarDefinition));
  if (!$cal->id) {
    $cal->setDates($day);
    $cal->idCalendarDefinition=$idCalendarDefinition;
    if (isOpenDay($day,$idCalendarDefinition)) {
      $cal->isOffDay=1;
    } else {
      $cal->isOffDay=0;
    }
    $cal->save();
  } else {
    $cal->delete();
  }
  $bankHolidays=array();
  $bankWorkdays=array();
}

function copyYear($from, $to, $currentYear) {
	if ($from==$to) return;
	$cal=new Calendar();
	$calList=$cal->getSqlElementsFromCriteria(array('idCalendarDefinition'=>$from, 'year'=>$currentYear));
	foreach ($calList as $cal) {
		$cp=SqlElement::getSingleSqlElementFromCriteria('Calendar',array('idCalendarDefinition'=>$to, 'day'=>$cal->day));
		$cp->setDates($cal->calendarDate);
		$cp->idCalendarDefinition=$to;
		$cp->name=$cal->name;
		$cp->isOffDay=$cal->isOffDay;
		$cp->idle=$cal->idle;
		$cp->save();
	}
}
?>