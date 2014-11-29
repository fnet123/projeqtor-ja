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
	echo "Rapport Flash version 2.0 [2014-02-20]";
	exit;
}

$idProject="";
if (array_key_exists('idProject',$_REQUEST) and trim($_REQUEST['idProject'])!="") {
  $idProject=trim($_REQUEST['idProject']);
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
include "header.php";
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
$showIndicator=1;
$showActivity=1;
$showMilestone=1;
$showRisk=1;
$showCost=1;
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
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
      width:<?php echo $titleWidth;?>mm; height:<?php echo $lineHeight;?>mm;white-space:nowrap;" class="reportTableLineHeader" >
      <?php displayHeader("CPU:");?></div>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $colLeft;?>mm; white-space:nowrap;">
      <?php displayField(SqlList::getNameFromId('Contact',$proj->idContact));?>
    </div>

    <?php $curHeight+=$lineHeight;?>
    <div style="position:absolute;top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
    width:<?php echo $titleWidth;?>mm;height:<?php echo $lineHeight;?>mm;white-space:nowrap;" class="reportTableLineHeader" >
      <?php displayHeader("CPI:");?>
    </div>
    <div style="position:absolute; top:<?php echo $curHeight;?>mm; left:<?php echo $colLeft;?>mm; white-space:nowrap;">
      <?php displayField(SqlList::getFieldFromId('User',$proj->idUser, 'fullName'));?>
    </div>

    <?php $curHeight+=$lineHeight;?>
    <div style="position:absolute;top:<?php echo $curHeight;?>mm; left:<?php echo $titleLeft;?>mm; 
    width:<?php echo $titleWidth;?>mm;height:<?php echo $lineHeight;?>mm;white-space:nowrap;" class="reportTableLineHeader" >
      <?php displayHeader("Situation à:");?>
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
    <?php displayHeader("Périmètre & objectifs Projet / Process Métier impactés");?>
    </div>
    <div style="overflow: <?php echo ($outMode=='pdf')?'hidden':'auto'?>;position:absolute; left:<?php displayWidth(50);?>;top:<?php echo $lineHeight;?>mm;height:30.5mm;
    width:<?php displayWidth(50);?>;<?php echo $border;?>">
      <?php displayField($proj->description);?>
    </div> 
  <?php }?>   
  </div>
  
  
  <div style="position:relative;top:2mm; width:<?php displayWidth(100);?>;height:28mm;<?php echo $borderMain?>" >
  <?php if ($showDecision) {?>
    <div class="reportTableLineHeader" style="width:<?php displayWidth(48.8);?>; white-space:nowrap;"><?php displayHeader("Décisions attendues");?></div>    
      <?php displayList($arrayDecision,5,49);?>
  <?php }?>  
  <?php if ($showIndicator) {
  	$overallProgress=new OverallProgress($proj->idOverallProgress);
    $health=new Health($proj->idHealth);
    $trend=new Trend($proj->idTrend);
    $quality=new Quality($proj->idQuality);?>
    <div class="reportTableLineHeader" style="position: absolute; top: 0mm; height: 10mm; text-align: center;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(60);?>;">
      <?php displayHeader("Global");?><br/>
      <span style="font-size:150%"><i><?php echo htmlEncode($overallProgress->name);?></i></span></div>
    <div style="position: absolute; top: 0mm; height: 10mm; text-align: center; background-color: #FFFFFF;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(70);?>;<?php echo $border;?>">
       <?php displayIndicator($health);?>
      </div>
    <div class="reportTableLineHeader" style="position: absolute; top: 0mm; height: 10mm; text-align: center;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(80);?>; ">
      <?php displayHeader("Tendance");?><br/>
      <i>"<?php echo htmlEncode($trend->name);?>"</i></div>  
    <div style="position: absolute; top: 0mm; height: 10mm; text-align: center; background-color: #FFFFFF;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(90);?>;<?php echo $border;?>">
      <?php displayIndicator($trend);?></div>  
    <div class="reportTableLineHeader" style="position: absolute; top: 10mm; height: 5mm; text-align: center;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(60);?>; ">
      <?php displayHeader("Coût");?></div>  
    <div class="reportTableLineHeader" style="position: absolute; top: 10mm; height: 5mm; text-align: center;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(70);?>;<?php echo $border;?>">
      <?php displayHeader("Qualité");?></div>
    <div class="reportTableLineHeader" style="position: absolute; top: 10mm; height: 5mm; text-align: center;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(80);?>;<?php echo $border;?>">
      <?php displayHeader("Délai");?></div>
    <div class="reportTableLineHeader" style="position: absolute; top: 10mm; height: 5mm; text-align: center;
    width:<?php displayWidth(9.5);?>; left:<?php displayWidth(90);?>;<?php echo $border;?>">
      <?php displayHeader("Risque");?></div>
    <div style="position: absolute; top: 15mm; height: 10mm; text-align: center;vertical-align: middle; background-color: #FFFFFF;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(60);?>;<?php echo $border;?>">
      <img src="../view/icons/smiley<?php echo ucfirst($costIndicator);?>.png" /></div>  
    <div style="position: absolute; top: 15mm; height: 10mm; text-align: center;vertical-align: middle; background-color: #FFFFFF;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(70);?>;<?php echo $border;?>">
      <?php displayIndicator($quality);?>
      </div>
    <div style="position: absolute; top: 15mm; height: 10mm; text-align: center; vertical-align: middle; background-color: #FFFFFF;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(80);?>;<?php echo $border;?>">
      <img src="../view/icons/smiley<?php echo ucfirst($delayIndicator);?>.png" /></div>
    <div style="position: absolute; top: 15mm; height: 10mm; text-align: center; vertical-align: middle;background-color: #FFFFFF;
    width:<?php displayWidth(10);?>; left:<?php displayWidth(90);?>;<?php echo $border;?>">
     <?php displayIndicator($maxRiskCriticality);?></div>    
  <?php }?>  
  
  </div>  
  
 
  <div style="position:relative; top: 3mm; width:<?php displayWidth(100);?>;height:50mm;<?php echo $borderMain?>" >
  <?php if ($showActivity) {?>
    <div style="width:<?php displayWidth(32);?>; position:absolute; left:0mm; top:0mm; background-color: white;">
      <div class="reportTableLineHeader" style="width:<?php displayWidth(31.8);?>; white-space:nowrap;"><?php displayHeader("Actions réalisées");?></div>
      <?php displayList($arrayActionDone,10,32);?>
    </div>
    <div style="width:<?php displayWidth(32);?>; position:absolute; left:<?php displayWidth(34);?>; top:0mm; background-color: white;">
      <div class="reportTableLineHeader" style="width:<?php displayWidth(31.8);?>; white-space:nowrap;"><?php displayHeader("Actions en cours");?></div>
      <?php displayList($arrayActionOngoing,12,32);?>
    </div>
    <div style="width:<?php displayWidth(32);?>; position:absolute; left:<?php displayWidth(67);?>; top:0mm; background-color: white;">
      <div class="reportTableLineHeader" style="width:<?php displayWidth(31.8);?>; white-space:nowrap;"><?php displayHeader("Actions à venir");?></div>
      <?php displayList($arrayActionTodo,12,32);?>
    </div>
  <?php }?>   
  </div>
  
  
  <div style="position:relative;top: 1mm; width:<?php displayWidth(100);?>;height:23mm;<?php echo $borderMain?>" >
  <?php if ($showMilestone) {
  $max=$maxMile;	
  $mileWidth=90;
  $cptDisp=count($arrayMilestone);
  if ($cptDisp>$max) $cptDisp=$max;
  if (count($arrayMilestone)) {
    $mileWidth=round(90/$cptDisp,1);
  }
  $cptMile=count($arrayMilestone);
  //if ($mileWidth>30) $mileWidth=30;?>
	  <table width="100%">
	    <tr><td class="reportTableLineHeader" style="width:10%">Jalons</td>
	    <?php $nb=0; 
	    foreach($arrayMilestone as $mile){
	      $nb++;
	      if ($nb > $max) break;?>
	      <td style="padding-left:1mm; width:<?php echo $mileWidth;?>%;<?php echo $border?>">
	      <?php displayField($mile['name']);?></td>
	    <?php }?>
	    </tr>
	    <tr><td class="reportTableLineHeader" style="width:10%">Initial</td>
	    <?php $nb=0;
	    foreach($arrayMilestone as $mile){
        $nb++;
        if ($nb > $max) break;?>
	      <td style="padding-left:1mm; width:<?php echo $mileWidth;?>%;<?php echo $border?>"><?php echo htmlFormatDate($mile['initial']);?></td>
	    <?php }?>
	    </tr>
	    <tr><td class="reportTableLineHeader" style="width:10%">Révisé</td>
	    <?php $nb=0;
	    foreach($arrayMilestone as $mile){
        $nb++;
        if ($nb > $max) break;
	      $color=$mile['color'];?>
	      <td style="background-color:<?php echo $color;?>;padding-left:1mm; width:<?php echo $mileWidth;?>%;<?php echo $border?>"><?php echo htmlFormatDate($mile['display']);?></td>
	    <?php }?>
	    </tr>
	  </table>
  <?php 
    if ($cptMile>$max) {
      echo '<div class="reportTableLineHeader"';
      echo ' style="position:absolute;top:0mm;right:'.(($outMode=='pdf')?'0':'0').'mm; width:10mm;">';
      echo '...'.$max.'/'.$cptMile.'&nbsp;';
      echo '</div>';
    }
  }?>
  </div> 
  
  
  <div style="position:relative;top: 2mm; width:<?php displayWidth(100);?>;height:25mm;<?php echo $borderMain?>" >
  <?php if ($showRisk) {?>
    <div style="position:absolute;top:0mm; width:<?php displayWidth(50);?>;height:30mm;<?php echo $borderMain?>" >
	    <table style="width:95%">
	       <tr>
	         <td colspan="4" class="reportTableLineHeader" >
	           <?php displayHeader("Alertes / Risques détectés sur le chantier et plan d'action");?>
	         </td>
	       </tr>
         <tr>
           <td  style="width:55%; font-weight:bold;<?php echo $border;?>" >
             <?php displayHeader("Risque");?>
           </td>
           <td  style="text-align: center;width:15%; font-weight:bold;<?php echo $border;?>" >
             <?php displayHeader("Criticité");?>
           </td>
           <td  style="text-align: center;width:15%; font-weight:bold;<?php echo $border;?>" >
             <?php displayHeader("Sévérité");?>
           </td>
           <td  style="text-align: center;width:15%; font-weight:bold;<?php echo $border;?>" >
             <?php displayHeader("Probabilité");?>
           </td>
         </tr>
         <?php 
          $nb=0;
          $max=6;
          foreach ($arrayRisk as $risk) {
            $nb++;
            if ($nb>$max) break;?>
          <tr>
           <td  style="position:relative; width:55%; <?php echo $border;?>" >
             <?php displayField($risk['name']);?>
           </td>
           <td  style="background-color:<?php echo $risk['criticalityColor'];?>;
               color:<?php echo htmlForeColorForBackgroundColor($risk['criticalityColor'])?>;
               text-align: center;width:15%; <?php echo $border;?>" >
             <?php echo $risk['criticality'];?>
           </td>
           <td  style="background-color:<?php echo $risk['severityColor'];?>;
               color:<?php echo htmlForeColorForBackgroundColor($risk['severityColor'])?>;
               text-align: center;width:15%; <?php echo $border;?>" >
             <?php echo $risk['severity'];?>
           </td>
           <td  style="background-color:<?php echo $risk['likelihoodColor'];?>;
               color:<?php echo htmlForeColorForBackgroundColor($risk['likelihoodColor'])?>;
               text-align: center; position:relative; width:15%; <?php echo $border;?>" >
             <?php echo $risk['likelihood'];
             if ($nb==$max and count($arrayRisk)>$max) {
              echo '<div class="reportTableLineHeader"';
              echo ' style="position:absolute;top:0mm;right:'.(($outMode=='pdf')?'-7':'0').'mm; width:10mm;">';
              echo '...'.$nb.'/'.count($arrayRisk).'&nbsp;';
              echo '</div>';
             }?>
           </td>
          </tr>
         <?php }?>
	     </table>
    </div>
  <?php }?>
  <?php if ($showCost) {?>
    <div style="position:absolute;top:0mm; left:<?php displayWidth(50);?>;height:30mm;width:<?php displayWidth(50);?>;<?php echo $borderMain?>" >
      <table style="width:100%">
	      <tr>
	        <td style="width:15%" class="reportTableLineHeader" >
	          <?php displayHeader("Budget\nProjet");?>
	        </td>
          <td style="text-align: center;width:20%" class="reportTableLineHeader" >
            <?php displayHeader("AE Budgétées / Planifiées");?>
          </td>
          <td style="text-align: center;width:20%" class="reportTableLineHeader" >
            <?php displayHeader("AE Engagé");?>
          </td>
          <td style="text-align: center;width:20%" class="reportTableLineHeader" >
            <?php displayHeader("CP consommé");?>
          </td>
          <td style="text-align: center;width:25%" class="reportTableLineHeader" >
            <?php displayHeader("Charge consommée de l'année (Jours Homme)");?>
          </td>
	      </tr>
        <tr style="height:7mm">
          <td style="width:15%" class="reportTableLineHeader" >
            <?php displayHeader("TTC");?>
          </td>
          <td style="text-align: center;width:20%;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($AEbudgetes, true);?>
          </td>
          <td style="text-align: center;width:20%;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($AEengages, true);?>
          </td>
          <td style="text-align: center;width:20%;<?php echo $border;?>"  >
            <?php echo htmlDisplayCurrency($CPconsommes, true);?>
          </td>
          <td style="text-align: center;width:20%;<?php echo $border;?>" >
            <?php echo Work::displayWorkWithUnit($proj->ProjectPlanningElement->realWork);?>
          </td>
         </tr>
         <tr style="height:7mm">
          <td style="width:15%;border-right:#A0A0A0;">
            
          </td>
          <td style="text-align: center;width:20%;<?php echo $border;?>" >
            <?php echo htmlDisplayCurrency($activitiesCost, true);?>
          </td>
          <td style="text-align: center;width:20%;<?php echo $border;?>" >
            <?php displayProgress($AEengages,$AEbudgetes,27);?>
          </td>
          <td style="text-align: center;width:20%;<?php echo $border;?>"  >
            <?php displayProgress($CPconsommes,$AEengages,27);?>
          </td>
          <td style="text-align: center;width:25%;<?php echo $border;?>" >
            <?php displayProgress($proj->ProjectPlanningElement->realWork,$proj->ProjectPlanningElement->validatedWork,34);?>
          </td>
         </tr>
      </table>
    </div>
  <?php }?>
  </div> 
</div>
<?php
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