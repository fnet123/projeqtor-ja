<?php 
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2014 Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 * 
 * Most of properties are extracted from Dojo Framework.
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

include_once '../tool/projeqtor.php';
include_once '../report/headerFunctions.php';
//echo "flashReport";
$showWbs=false;

SqlElement::$_cachedQuery['Status']=array();
SqlElement::$_cachedQuery['Severity']=array();
SqlElement::$_cachedQuery['Criticality']=array();
SqlElement::$_cachedQuery['Likelihood']=array();

if (! isset($outMode)) {
	$outMode='screen';
}

if (array_key_exists('version',$_REQUEST)) {
	echo "Projet V0.1 [2014-10-10]";
	exit;
}

$idProject="";
if (array_key_exists('idProject',$_REQUEST) and trim($_REQUEST['idProject'])!="") {
  $idProject=trim($_REQUEST['idProject']);
}
if (array_key_exists('objectId', $_REQUEST)){
	$idProject=trim($_REQUEST['objectId']);
}

$result=array();
if ($idProject) { $result[]=$idProject;}
if (checkNoData($result)) exit;

$headerParameters="";
if ($idProject) {
  $headerParameters.= i18n("colId") . ' : ' . htmlEncode($idProject) . '<br/>';
	$headerParameters.= i18n("colIdProject") . ' : ' . htmlEncode(SqlList::getNameFromId('Project',$idProject)) . '<br/>';
} 

ob_start();
include "../report/header.php";
ob_end_clean();

$proj=new Project($idProject);
$arrayProjects=array($idProject=>$proj);

$user=$_SESSION['user'];

$subProjects=$proj->getSqlElementsFromCriteria(array('idProject'=>$idProject));
foreach ($subProjects as $p) {
	$arrayProjects[$p->id]=$p;
}

// MAIN LOOP OVER SELECTED PROJECT AND ITS SUB6PROJECTS
$currentProject=0;
foreach ($arrayProjects as $idProject=>$proj) {
	$proj=new Project($idProject); // To retrieve PlanningElement
  $projList=getVisibleProjectsList(true,$idProject);
	$currentProject+=1;
// DECISIONS
$arrayDecision=array();
$dec=new Decision();
$decList=$dec->getSqlElementsFromCriteria(null, false, "idProject=$idProject", 'id asc');
foreach($decList as $dec) {
	$status=new Status($dec->idStatus);
	if ($status->setHandledStatus and ! $status->setDoneStatus) {
	  $arrayDecision[]=$dec->name;
	}
}

// ACTIONS
$arrayActionDone=array();
$arrayActionOngoing=array();
$arrayActionTodo=array();
$act=new Action();
$actList=$act->getSqlElementsFromCriteria(null, false, "idProject=$idProject", "actualDueDate asc, initialDueDate asc, creationDate asc");
foreach ($actList as $act) {
	$status=new Status($act->idStatus);
	$name=$act->name;
	if (strlen($name)>60) {
		$name=substr($name, 0,55).'[...]';
	}
	if ($status->setHandledStatus and $status->setDoneStatus) {
		$arrayActionDone[]=$name;
	} else if ($status->setHandledStatus and ! $status->setDoneStatus) {
		$arrayActionOngoing[]=$name;
	} else {
		$arrayActionTodo[]=$name;
	}
}
// ACTIVITIES
$notStartedCost=0;
$activitiesCost=0;
$pe=new ActivityPlanningElement();
// Ticket #1349 - Start
//$peList=$pe->getSqlElementsFromCriteria(null, false, "refType='Activity' and idProject=$idProject", "wbsSortable asc");
$peList=$pe->getSqlElementsFromCriteria(null, false, "refType='Activity' and idProject in $projList", "wbsSortable asc");
// Ticket #1349 - End
foreach ($peList as $pe) {
	$act=new Activity($pe->refId);
	$status=new Status($act->idStatus);
	if (! $status->setHandledStatus and ! $status->setDoneStatus) {
		$notStartedCost+=$pe->validatedCost;
	}
	$activitiesCost+=$pe->validatedCost;
}

// MILESTONES
$realDate_JalonJ="";
$realDate_JalonPrec="";
$prevDate_JalonJ="";
$prevDate_JalonPrec="";

$arrayMilestone=array();
$pe=new MilestonePlanningElement();
$peList=$pe->getSqlElementsFromCriteria(null, false, "refType='Milestone' and idProject=$idProject", "validatedEndDate asc");
$cptMile=count($peList);
$maxMile=12;
if ($cptMile>$maxMile) $cptMile=$maxMile;
$maxCar=100-($cptMile-6)*(15-$cptMile+6);
$currentCptMile=0;
foreach ($peList as $pe) {
	$name=$pe->refName;
	$mile=new Milestone($pe->refId);
	$type=new MilestoneType($mile->idMilestoneType);
	if (strlen($name)>$maxCar) {
    $name=substr($name, 0,$maxCar-5).'[...]';
  }
  $colorMile="#FFFFFF";
  if ($pe->done) {
  	$prevDate_JalonPrec=$prevDate_JalonJ;
  	$realDate_JalonPrec=$realDate_JalonJ;
  	$realDate_JalonJ=$pe->realEndDate;
  	$prevDate_JalonJ=$pe->validatedEndDate;
  	if ($pe->realEndDate<=$pe->validatedEndDate) {
  		$colorMile="#32cd32";
  	} else {
  		if ($currentCptMile>=1) {
  			if ($arrayMilestone[$currentCptMile]['real']<=$arrayMilestone[$currentCptMile]['validated']) {
  			  $colorMile="#ffd700";
  			} else {
  				$colorMile="#ff0000";
  			}
  		} else {
  			$colorMile="#ffd700";
  		}
  	}
  }
  if ($type->showInFlash) {
  	$currentCptMile+=1;
	  $arrayMilestone[$currentCptMile]=array('name'=>$name, 
	    'initial'=>$pe->initialEndDate, 
	    'validated'=>$pe->validatedEndDate,
	    'real'=>$pe->realEndDate,
	  	'display'=>($pe->done)?$pe->realEndDate:$pe->validatedEndDate,
	  	'color'=>$colorMile,
	    'done'=>$pe->done);
  }
}
if (! count($arrayMilestone)) {
	$arrayMilestone[]=array('name'=>"Aucun jalon n'est défini", 
     'initial'=>'', 
     'validated'=>'',
		 'real'=>'',
		 'display'=>'',
     'done'=>false,
	   'color'=>'#FFFFFF'
	   );
}

/*$arrayListValues=array("Severity", "Likelihood", "Criticality");
foreach ($arrayListValues as $listValue) {
  $max='max'.$listValue;
  $min='min'.$listValue;
  $$max=null;
  $$min=null;
	$val=new $listValue();
	$valList=$val->getSqlElementsFromCriteria(null);
	foreach ($valList as $val) {
	  if ($$max===null or $val->value>$$max) {
	  	$$max=$val->value;
	  }
	  if ($$min===null or $val->value<$$min) {
      $$min=$val->value;
    }
	}
}*/
//$noteMax=$maxLikelihood*$maxCriticality*$maxSeverity;
//$noteMin=$minLikelihood*$minCriticality*$minSeverity;
//echo 'noteMax=' . $noteMax . '<br/>';
//echo 'noteMin=' . $noteMin . '<br/>';
$noteRisque=0;
$maxRiskCriticality=new Criticality();
// RISKS
$arrayRisk=array();
$risk=new Risk();
$riskList=$risk->getSqlElementsFromCriteria(null, false, "idProject=$idProject", 'id asc');
foreach ($riskList as $risk) {
	$status=new Status($risk->idStatus);
	if (! $risk->idle) {
		$severity=new Severity($risk->idSeverity);
		$criticality=new Criticality($risk->idCriticality);
		$likelihood=new Likelihood($risk->idLikelihood);
		$order=$severity->value*$criticality->value*$likelihood->value;
		if ($criticality->value>$noteRisque) {
			$noteRisque=$criticality->value;
			$maxRiskCriticality=$criticality;
		}
		$order=htmlFixLengthNumeric($order,6).'-'.$risk->id;
		$name=$risk->name;
	  if (strlen($name)>90) {
	    $name=substr($name, 0,85).'[...]';
	  }
		$arrayRisk[$order]=array('name'=>$name, 
		  'criticality'=>$criticality->name,
		  'criticalityColor'=>($criticality->color)?$criticality->color:"#FFFFFF", 
		  'severity'=>$severity->name,
		  'severityColor'=>($severity->color)?$severity->color:"#FFFFFF", 
		  'likelihood'=>$likelihood->name,
		  'likelihoodColor'=>($likelihood->color)?$likelihood->color:"#FFFFFF");
	}
}
krsort($arrayRisk);

// INDICATORS
$AEbudgetes=0;
$pe=new ProjectPlanningElement();
$peList=$pe->getSqlElementsFromCriteria(null, false, "refType='Project' and refId in $projList");
foreach ($peList as $pe) {
	$AEbudgetes+=$pe->validatedCost;
}

$AEengages=0;
$CPconsommes=0;
$exp=new Expense();
$expList=$exp->getSqlElementsFromCriteria(null,false,"idProject in $projList");
foreach ($expList as $exp) {
	$AEengages+=$exp->plannedAmount;
	$status=new Status($exp->idStatus);
	if ($status->setDoneStatus) {
		$CPconsommes+=$exp->realAmount;
	}
}

$CHARGE=$AEengages+$notStartedCost;
$RESSOUCE=$AEbudgetes;
if ($CHARGE>$RESSOUCE) {
  $costIndicator="red";
} else if ($CHARGE<($RESSOUCE*80/100)) {
	$costIndicator="yellow";
} else {
	$costIndicator="green";
}

if ($realDate_JalonJ<=$prevDate_JalonJ) {
	$delayIndicator="green";
} else {
	if ($realDate_JalonPrec>$prevDate_JalonPrec) {
		$delayIndicator="red";
	} else {
		$delayIndicator="yellow";
	}
}

//echo 'noteRisque=' . $noteRisque . '<br/>';
/*$etendue=$noteMax-$noteMin;
if ($noteRisque<=$noteMin+$etendue/3) {
	$riskIndicator="green";
} else if ($noteRisque<=$noteMin+2*$etendue/3) {
	$riskIndicator="yellow";
} else {
	$riskIndicator="red";
}*/

//$qualityIndicator="green";

// FORMATING VALUES
$height=185;
$width=277;
$borderMain="border: 1px solid red;";
$borderMain="";
$border="border: 1px solid #A0A0A0;";

$showHeader=1;
$showDecision=1;
$showIndicator=0;
$showActivity=1;
$showMilestone=0;
$showRisk=0;
$showCost=0;
if ($outMode!='pdf') {
	echo '<div style="height:1mm;">&nbsp;</div>';
}
?>
<div style="font-family: arial;font-size:<?php echo (($outMode=='pdf')?'3':'3');?>mm; width:<?php displayWidth(100);?>; height:<?php displayheight(100);?>;background-color: white; <?php echo $borderMain?>" >

  <div style="position:relative;width:<?php displayWidth(100);?>;height:37mm;<?php echo $borderMain?>">
  <?php if ($showHeader) {
    $titleLeft=0;
  	$titleWidth=18;
    $colLeft=$titleLeft+$titleWidth+3;
    $lineHeight=4;
    $curHeight=0;
    ?>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
      width:<?php echo $titleWidth;?>mm;font-weight: bold">Situation du <br/>projet</div>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $colLeft;?>mm;
      font-size:150%;font-weight:bold;<?php echo $borderMain?>">
      <?php displayField($proj->name);?></div>
    
    <?php $curHeight=10;?>
    
    <div style="position:absolute;top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
    width:<?php echo $titleWidth;?>mm;height:<?php echo $lineHeight;?>mm;white-space:nowrap;" class="reportTableLineHeader" >
      <?php displayHeader("manager :");?>
    </div>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $colLeft;?>mm; white-space:nowrap;">
      <?php displayField(SqlList::getFieldFromId('User',$proj->idUser, 'fullName'));?>
    </div>

    <?php $curHeight+=$lineHeight;?>
    <div style="position:absolute;top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
    width:<?php echo $titleWidth;?>mm;height:<?php echo $lineHeight;?>mm;white-space:nowrap;" class="reportTableLineHeader" >
      <?php displayHeader("Situation au :");?>
    </div>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $colLeft;?>mm; white-space:nowrap;">
      <?php displayField(htmlFormatDate(date("Y-m-d")));?>
    </div>
    
    <?php $titleLeft=round($width*25/100,0);
    $titleWidth=18;
    $colLeft=$titleLeft+$titleWidth+3;
    $lineHeight=4;
    $curHeight=6;?>
    <div style="position:absolute;top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
    width:<?php echo $titleWidth;?>mm; height:<?php echo $lineHeight;?>mm;white-space:nowrap;" class="reportTableLineHeader" >
      <?php displayHeader("Priorité:");?>
    </div>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $colLeft;?>mm; white-space:nowrap;">
      <?php echo $proj->ProjectPlanningElement->priority;?>
    </div>
    
    <?php $curHeight+=$lineHeight;?>
    <div style="position:absolute;top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
    width:<?php echo $titleWidth;?>mm; height:<?php echo $lineHeight;?>mm;white-space:nowrap;" class="reportTableLineHeader" >
      <?php displayHeader("Sponsor:");?>
    </div>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $colLeft;?>mm; white-space:nowrap;">
      <?php echo SqlList::getNameFromId('Sponsor',$proj->idSponsor);?>
    </div>
 
    <?php $curHeight+=$lineHeight;?>
    <div style="position:absolute;top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
    width:<?php echo $titleWidth;?>mm; height:<?php echo $lineHeight;?>mm;white-space:nowrap;" class="reportTableLineHeader" >
      <?php displayHeader("Direction:");?>
    </div>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $colLeft;?>mm; white-space:nowrap;">
      <?php $cli=new Client($proj->idClient);
      if ($cli->clientCode) {
        $name=$cli->clientCode;
      } else {
        $name=$cli->name;
      }
      if (strlen($name)>37) {
      	$name=substr($name, 0,32).'[...]';
      }
      echo $name;?>
    </div>
    
    <?php $curHeight+=$lineHeight;?>
    <div style="position:absolute;top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
    width:<?php echo $titleWidth;?>mm; height:<?php echo $lineHeight;?>mm;white-space:nowrap;" class="reportTableLineHeader" >
      <?php displayHeader("Etat:");?>
    </div>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $colLeft;?>mm; white-space:nowrap;">
      <?php echo SqlList::getNameFromId('Status',$proj->idStatus);?>
    </div>

    <div style="position:absolute; left:<?php displayWidth(50);?>;top:0mm;height:<?php echo $lineHeight;?>mm;
    width:<?php displayWidth(49.5);?>;white-space:nowrap;" class="reportTableLineHeader">
    <?php displayHeader("Description du projet");?>
    </div>
    <div style="overflow: <?php echo ($outMode=='pdf')?'hidden':'auto'?>;position:absolute; left:<?php displayWidth(50);?>;top:<?php echo $lineHeight;?>mm;height:30.5mm;
    width:<?php displayWidth(50);?>;<?php echo $border;?>">
      <?php displayField($proj->description);?>
    </div> 
  <?php }?>   
  </div>

  
  
  <div style="position:relative;top: 2mm; width:<?php displayWidth(100);?>;height:50mm;<?php echo $borderMain?>" >

  <?php $fraisGeneraux=0.35;$coefFG=1+$fraisGeneraux?>
    <div style="position:absolute;top:0mm; left:<?php displayWidth(0);?>;height:60mm;width:<?php displayWidth(100);?>;<?php echo $borderMain?>" >
      <table style="width:100%">
	      <tr>
	        <td style="width:10%" class="reportTableLineHeader" >
	          <?php displayHeader("Budget Projet");?>
	        </td>
	      <td style="text-align: center;width:10%" class="reportTableLineHeader" >
            <?php displayHeader("Vendu");?>
          </td>
          <td style="text-align: center;width:10%" class="reportTableLineHeader" >
            <?php displayHeader("Prévu\n(initial)");?>
          </td>
          <td style="text-align: center;width:10%" class="reportTableLineHeader" >
            <?php displayHeader("Réalisé\n(consommé)");?>
          </td>
          <td style="text-align: center;width:10%" class="reportTableLineHeader" >
            <?php displayHeader("Reste thérorique\n(prévu-réalisé)");?>
          </td>
          <td style="text-align: center;width:10%" class="reportTableLineHeader" >
            <?php displayHeader("Reste réel\n(réévalué)");?>
          </td>
           <td style="text-align: center;width:10%" class="reportTableLineHeader" >
            <?php displayHeader("Marge initiale");?>
          </td>
          <td style="text-align: center;width:10%" class="reportTableLineHeader" >
            <?php displayHeader("Marge réelle");?>
          </td>
          <td style="text-align: center;width:10%" class="reportTableLineHeader" >
            <?php displayHeader("Avancement Théorique");?>
          </td>
          <td style="text-align: center;width:10%" class="reportTableLineHeader" >
            <?php displayHeader("Avancement Réel");?>
          </td>
	      </tr>
        <tr style="height:7mm">
          <td class="reportTableLineHeader" >
            <?php displayHeader("Charges");?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo Work::displayWorkWithUnit($proj->ProjectPlanningElement->validatedWork, true);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo Work::displayWorkWithUnit($proj->ProjectPlanningElement->assignedWork, true);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo Work::displayWorkWithUnit($proj->ProjectPlanningElement->realWork, true);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo Work::displayWorkWithUnit($proj->ProjectPlanningElement->assignedWork-$proj->ProjectPlanningElement->realWork, true);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo Work::displayWorkWithUnit($proj->ProjectPlanningElement->leftWork, true);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo Work::displayWorkWithUnit($proj->ProjectPlanningElement->validatedWork-$proj->ProjectPlanningElement->assignedWork, true);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo Work::displayWorkWithUnit($proj->ProjectPlanningElement->validatedWork-$proj->ProjectPlanningElement->plannedWork, true);?>
          </td>
           <td style="text-align: center;<?php echo $border;?>" >
            <?php displayProgress($proj->ProjectPlanningElement->realWork,$proj->ProjectPlanningElement->assignedWork,34);?>
          </td>
           <td style="text-align: center;<?php echo $border;?>" >
            <?php displayProgress($proj->ProjectPlanningElement->realWork,$proj->ProjectPlanningElement->plannedWork,34);?>
          </td>
         </tr>
         <tr style="height:7mm">
          <td class="reportTableLineHeader" >
            <?php displayHeader("Coûts des ressources");?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->validatedCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->assignedCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->realCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->assignedCost-$proj->ProjectPlanningElement->realCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->leftCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->validatedCost-$proj->ProjectPlanningElement->assignedCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->validatedCost-$proj->ProjectPlanningElement->plannedCost);?>
          </td>
          <td></td>
          <td></td>
        </tr>
         <tr style="height:7mm">
          <td class="reportTableLineHeader" >
            <?php displayHeader("Dépenses Matériel");?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->expenseValidatedAmount);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->expenseAssignedAmount);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->expenseRealAmount);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->expenseAssignedAmount-$proj->ProjectPlanningElement->expenseRealAmount);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->expenseLeftAmount);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->expenseValidatedAmount-$proj->ProjectPlanningElement->expenseAssignedAmount);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->expenseValidatedAmount-$proj->ProjectPlanningElement->expensePlannedAmount);?>
          </td>
          <td></td>
          <td></td>
         </tr>
         </tr>
         <tr style="height:7mm">
          <td class="reportTableLineHeader" >
            <?php displayHeader("Déboursé sec\n(total)");?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalValidatedCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalAssignedCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalRealCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalAssignedCost-$proj->ProjectPlanningElement->totalRealCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalLeftCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalValidatedCost-$proj->ProjectPlanningElement->totalAssignedCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalValidatedCost-$proj->ProjectPlanningElement->totalPlannedCost);?>
          </td>
          <td></td>
          <td></td>
         </tr>
          <tr style="height:7mm">
          <td class="reportTableLineHeader" >
            <?php displayHeader("Déboursé/Résultat\n(Frais géné ".($fraisGeneraux*100)."%)");?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalValidatedCost);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalAssignedCost*$coefFG);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalRealCost*$coefFG);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency(($proj->ProjectPlanningElement->totalAssignedCost*$coefFG)-($proj->ProjectPlanningElement->totalRealCost*$coefFG));?>
          </td>
          <td style="text-align: center;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalLeftCost*$coefFG);?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalValidatedCost-($proj->ProjectPlanningElement->totalAssignedCost*$coefFG));?>
          </td>
          <td style="text-align: center;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($proj->ProjectPlanningElement->totalValidatedCost-($proj->ProjectPlanningElement->totalPlannedCost*$coefFG));?>
          </td>
          <td></td>
          <td></td>
         </tr>
      </table>
    </div>
  </div> 
</div>
<?php
  return; // Avoid display of sub-projects
  if ($currentProject<count($arrayProjects)) {
    if ($outMode=='pdf') {
    	echo "</PAGE><PAGE>";
    } else {
    	echo '<div style="height:0.1mm; page-break-after:always;">&nbsp;</div>';
    	//echo '<div style="height:1mm;">&nbsp;</div>';
    }
  }
}
// END OF MAIN LOOP OVER SELECTED PROJECT AND ITS SUB-PROJECTS

function displayField($value,$height=null) {
  $res="";
  if ($height) {
	  $res.='<div style="top:0px;width:100%;';
    $res.='height:'.$height.'mm;';
    $res.='white-space:nowrap; text-overflow:ellipsis;">';
  }
  $res.=htmlEncode($value,'print');
  if ($height) {
    $res.='</div>';
  }
	echo $res;
	
}
function displayHeader($value,$height=null) {
  $res='';
  if ($height) {
	  $res.='<div style="top:0px;';
	  $res.='height:'.$height.'px;';
	  $res.='white-space:nowrap;width:100%;">';
  }
  $res.=htmlEncode($value,'print');
  if ($height) {
    $res.='</div>';
  }
  echo $res;
  
}

function displayList($list, $max, $width) {
  $nb=0;
	foreach ($list as $item) {
    $nb++;
    if ($nb>$max) break;
    echo '<div style="position:relative;vertical-align:top; width:';
    displayWidth($width);
    echo ';padding-left:1mm;border: 1px solid #A0A0A0;">';
    displayField($item);
	  if ($nb==$max and count($list)>$max) {
      echo '<div class="reportTableLineHeader" style="position:absolute;top:0mm;right:0mm; width:10mm;">';
      echo '...'.$nb.'/'.count($list).'&nbsp;';
      echo '</div>';
    }
    echo '</div>';
    
  }
}

function displayWidth($widthPct) {
  global $width;
  echo (round($width*$widthPct/100,1)).'mm';
}
function displayheight($heightPct) {
  global $height;
  echo (round($height*$heightPct/100,1)).'mm';
}
  
function displayProgress($value,$max,$width) {
    if ($value==='') { return; }
    if (! $max or $max==0) { return; }
    $green=($max!=0 and $max)?round( $width*$value/$max,1):$width;
    $red=$width-$green;
    $result="";
    $result.=htmlDisplayPct(round(100*$value/$max,0)) . '<br/>';
    $result.='<table style="text-align:center;width:'.$width.'mm;height:5mm;"><tr style="height:5mm;">';
    $result.='<td style="background: #AAFFAA;width:'.$green.'mm;height:5mm;"></td>';
    $result.='<td style="background: #FFAAAA;width:'.$red.'mm;height:5mm;"></td>';
    $result.='</tr></table>';
    //$result='<div style="position:relative; left:1mm; width:' . $width . 'mm" >';
    //$result.='<div style="position:absolute; left:0mm; width:' . $green . 'mm;background: #AAFFAA;">&nbsp;</div>';
    //$result.='<div style="position:absolute; width:' . $red . 'mm;left:' . $green . 'mm;background: #FFAAAA;">&nbsp;</div>';
    
    //$result.='</div>';
    echo $result;
  }
  
function displayIndicator($indObj) {
	$result='';
	if (property_exists($indObj, 'icon') and isset($indObj->icon) and $indObj->icon) {
		$result.='<img src="../view/icons/'.$indObj->icon.'" />';
	} else {
    $result.='<div style="height:7mm; width: 7mm; position: absolute; left:10mm; top:1mm; ';
    $result.='border: 1px solid black; border-radius: 4mm;';
    $result.='background-color:'.$indObj->color.'">&nbsp;</div>';
	}
  echo $result;
}  
?>