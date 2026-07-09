<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

$my_page_title=gks_lang('SMSAPI Callback');
db_open();
stat_record();

//debug_mail(false,'smsapi_callback.php');

$arIds = explode(',',$_GET['MsgId']);  
$arTo = explode(',',$_GET['to']);  
$arDonedate = explode(',',$_GET['donedate']);
$arStatus = explode(',',$_GET['status']);
if (isset($_GET['mcc'])) {
  $arStatus_Name = explode(',',$_GET['status_name']);
} else  {
  $arStatus_Name = array('');
}
$arIdx = explode(',',$_GET['idx']);
$arUsername= explode(',',$_GET['username']);
$arPoints = explode(',',$_GET['points']);
if (isset($_GET['mcc'])) {
  $armcc = explode(',',$_GET['mcc']);
} else {
  $armcc=array(0);  
}
if (isset($_GET['mnc'])) {
  $armnc = explode(',',$_GET['mnc']);
} else {
  $armnc=array(0);
}



if($arIds){
  foreach($arIds as $k => $v){
    
    
    $message_id=$v;
    
    if ($k < count($arTo)) {
      $to=$arTo[$k];
    } else {
      $to=$arTo[0];
    }
    
    
    if ($k < count($arDonedate)) {
      $donedate=$arDonedate[$k];
    } else {
      $donedate=$arDonedate[0];
    }
    $donedate_date="'".date('Y-m-d H:i:s',$donedate)."'";
    
    $status= $arStatus[$k];
    $status_name='--';
    
    switch ($status) {
      case 401: // NOT_FOUND Wrong ID or report has expired
        $status_name=gks_lang('Δεν βρέθηκε');
        break;
      case 402: //EXPIRED Messages expired.
        $status_name=gks_lang('Έληξε');
        break;
      case 403: //SENT Message is sent without final delivery report.
        $status_name=gks_lang('Στάλθηκε');
        break;
      case 404: //DELIVERED Message is delivered to recipient
        $status_name=gks_lang('Παραδόθηκε');
        break;
      case 405: //UNDELIVERED Message is undelivered (invalid number, roaming error etc)
        $status_name=gks_lang('Δεν παραδόθηκε');
        break;
      case 406: // FAILED Sending message failed – please report it to us
        $status_name=gks_lang('Αποτυχία');
        break;
      case 407: // REJECTED Message is undelivered (invalid number, roaming error etc)
        $status_name=gks_lang('Απορίφθηκε');
        break;
      case 408: // UNKNOWN No report (message may be either delivered or not)
        $status_name=gks_lang('Άγνωστο');
        break;
      case 409: // QUEUE Message is waiting to be sent
        $status_name=gks_lang('Σε ουρά');
        break;
      case 410: // ACCEPTED Message is delivered to operator
        $status_name=gks_lang('Αποδέχθηκε');
        break;
      case 412: // STOP Bulk has been stopped by the user.
        $status_name=gks_lang('Διακόπηκε');
        break;
      default:
        $status_name=gks_lang('Άγνωστο');
    }    
    
    
    if ($k < count($arUsername)) { 
      $from = $arUsername[$k];
    } else {
      $from = $arUsername[0];
    }

    
    if ($k < count($arPoints)) { 
      $points = $arPoints[$k];
    } else {
      $points = $arPoints[0];
    }
    if ($k < count($armcc)) {
      $mcc = $armcc[$k]+0;
    } else {
      $mcc = $armcc[0]+0;
    }
    if ($k < count($armnc)) {
      $mnc = $armnc[$k]+0;
    } else {
      $mnc = $armnc[0]+0;
    }
    
    
    $sql="select message_id from gks_sms where sms_provider='smsapi' and message_id='".$db_link->escape_string($message_id)."'";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'warning on smsapi callback error sql 1 ',$sql);
    }
    if ($result->num_rows == 0) {
      $sql="insert into gks_sms (sms_provider,date_add,message_id,myfrom,myto,Message,Message_post,donedate,donedate_date,status,status_name,points,cost,mcc,mnc) values (
      'smsapi',
      NOW(),
      '".$db_link->escape_string($message_id)."',
      '".$db_link->escape_string($from)."',
      '".$db_link->escape_string($to)."',
      '',
      '',
      ".$donedate.",
      ".$donedate_date.",
      ".$status.",
      '".$db_link->escape_string($status_name)."',
      ".$points.",
      ".$points.",
      ".$mcc.",
      ".$mnc.")";
      $myrun = $db_link->query($sql);
      if (!$myrun) {
        debug_mail(false,'warning on smsapi callback error sql 2 ',$sql);
      }      

    } else {
    
    
      $sql="update gks_sms set 
      donedate=".$donedate.",
      donedate_date=".$donedate_date.",
      status=".$status.",
      status_name='".$db_link->escape_string($status_name)."',
      points=".$points.",
      mcc=".$mcc.",
      mnc=".$mnc."
      where message_id='".$db_link->escape_string($message_id)."' 
      and sms_provider='smsapi'
      limit 1";
      
      
      $myrun = $db_link->query($sql);
      if (!$myrun) {
        debug_mail(false,'warning on smsapi callback error sql 2 ',$sql);
      }
      
    }
  }
  
}



echo 'OK';


