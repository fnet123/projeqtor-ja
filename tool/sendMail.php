<?php
/** =========================================================================== 
 * Chek login/password entered in connection screen
 */
  require_once "../tool/projeqtor.php"; 
  scriptLog('   ->/tool/sendMail.php');  
  $title="";
  $msg="";
  $dest="";
  $typeSendMail="";
  
  if (array_key_exists('className',$_REQUEST)) {
    $typeSendMail=$_REQUEST['className'];
  }

  $result="";
  if ($typeSendMail=="User") {
    $login=$_REQUEST['name'];
    $dest=$_REQUEST['email'];
    $userMail=SqlElement::getSingleSqlElementFromCriteria('User', array('name'=>$login));
    $title=$userMail->parseMailMessage(Parameter::getGlobalParameter('paramMailTitleUser'));  
    $msg=$userMail->parseMailMessage(Parameter::getGlobalParameter('paramMailBodyUser'));
    // Format title and message
    $result=(sendMail($dest,$title,$msg))?'OK':'';
  } else if ($typeSendMail=="Meeting") {
    if (array_key_exists('id',$_REQUEST)) {
      $id=$_REQUEST['id'];
      $meeting=new Meeting($id);
      $dest=$meeting->sendMail();
      $result=($dest!='')?'OK':'';
    }
  } else if ($typeSendMail=="Document") {
  	$id=$_REQUEST['id'];
  	$doc=new Document($id);
  	$dest=$doc->sendMailToApprovers(true);
  	$result=($dest!='' and $dest!='0')?'OK':'';
  } else if ($typeSendMail=="Mailable") {
  	$class=$_REQUEST['mailRefType'];
  	if ($class=='TicketSimple') {$class='Ticket';}
  	$id=$_REQUEST['mailRefId'];
  	$mailToContact=(array_key_exists('dialogMailToContact', $_REQUEST))?true:false;
    $mailToUser=(array_key_exists('dialogMailToUser', $_REQUEST))?true:false;
    $mailToResource=(array_key_exists('dialogMailToResource', $_REQUEST))?true:false;
    $mailToSponsor=(array_key_exists('dialogMailToSponsor', $_REQUEST))?true:false;
    $mailToProject=(array_key_exists('dialogMailToProject', $_REQUEST))?true:false;
    $mailToLeader=(array_key_exists('dialogMailToLeader', $_REQUEST))?true:false;
    $mailToManager=(array_key_exists('dialogMailToManager', $_REQUEST))?true:false;
    $mailToAssigned=(array_key_exists('dialogMailToAssigned', $_REQUEST))?true:false;
    $mailToOther=(array_key_exists('dialogMailToOther', $_REQUEST))?true:false;
    $otherMail=(array_key_exists('dialogOtherMail', $_REQUEST))?$_REQUEST['dialogOtherMail']:'';
    $otherMail=str_replace('"','',$otherMail);
    $message=(array_key_exists('dialogMailMessage', $_REQUEST))?$_REQUEST['dialogMailMessage']:'';  
    $obj=new $class($id);
    $directStatusMail=new StatusMail();
    $directStatusMail->mailToContact=$mailToContact;
    $directStatusMail->mailToUser=$mailToUser;
    $directStatusMail->mailToResource=$mailToResource;
    $directStatusMail->mailToSponsor=$mailToSponsor;
    $directStatusMail->mailToProject=$mailToProject;
    $directStatusMail->mailToLeader=$mailToLeader;
    $directStatusMail->mailToManager=$mailToManager;
    $directStatusMail->mailToOther=$mailToOther;
    $directStatusMail->mailToAssigned=$mailToAssigned;
    $directStatusMail->otherMail=$otherMail;
    $directStatusMail->message=htmlEncode($message,'html'); // Attention, do not save this status mail
    $resultMail=$obj->sendMailIfMailable(false,false,$directStatusMail,false,false,false,false,false,false,false,false,false);
    if (! $resultMail or ! is_array($resultMail)) {
    	$result="";
    	$dest="";
    } else {
    	$result=$resultMail['result'];
      $dest=$resultMail['dest'];
    }
  }
  
  
  
  if ($result!="OK") {
    echo '<span class="messageERROR" >' . i18n('noMailSent',array($dest, $result)) . '</span>';
    echo '<input type="hidden" id="lastOperation" value="mail" />';
    echo '<input type="hidden" id="lastOperationStatus" value="ERROR" />';
  } else {
    echo '<span class="messageOK" >' . i18n('mailSentTo',array($dest)) . '</span>';
    echo '<input type="hidden" id="lastOperation" value="mail" />';
    echo '<input type="hidden" id="lastOperationStatus" value="OK" />';
  } 
?>