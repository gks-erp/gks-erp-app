<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$my_page_title=gks_lang('Εκτέλεση Αποστολής sms');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sms','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





  
//$_POST['guid']=$_GET['guid'];


if (!isset($_POST['from'])|| trim_gks($_POST['from'])=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποστολέα').' (1)'));
  echo json_encode($return); die();
}
$myfrom=trim_gks(base64_decode($_POST['from']));

$sender_sms_provider='';if (isset($_POST['sender_sms_provider'])) $sender_sms_provider=trim_gks(base64_decode($_POST['sender_sms_provider']));
if ($sender_sms_provider=='' or ($sender_sms_provider!='gks_erp_app_mobile' and $sender_sms_provider!='smsapi')) {
  debug_mail(false,'sender_sms_provider is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποστολέα').' (2)'));
  echo json_encode($return); die();}

$sender_sms_sender='';if (isset($_POST['sender_sms_sender'])) $sender_sms_sender=trim_gks(base64_decode($_POST['sender_sms_sender']));
if ($sender_sms_sender=='') {
  debug_mail(false,'sender_sms_sender is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποστολέα').' (3)'));
  echo json_encode($return); die();}




if (!isset($_POST['to'])|| trim_gks($_POST['to'])=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε κάποιον αποδέκτη')));
  echo json_encode($return); die();
}
$myto=trim_gks(base64_decode($_POST['to']));

//echo '|||'.$myto.'|||'.$_POST['to'].'|||';
//die();

if (!isset($_POST['message'])|| trim_gks($_POST['message'])=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε κάποιο μήνυμα')));
  echo json_encode($return); die();
}
$mymessage=trim_gks(base64_decode($_POST['message']));

$model='admin';
if (isset($_POST['model']) && trim_gks($_POST['model'])!='') {
  $model=trim_gks(stripslashes(urldecode($_POST['model'])));
}
$model_id=0;
if (isset($_POST['model_id']) && trim_gks($_POST['model_id'])!='') {
  $model_id=intval(trim_gks(stripslashes(urldecode($_POST['model_id']))));
}



$myreport_error='';
$myreport_ok='';
$mytotal=0;
$myok=0;

$mya=explode(',',$myto);
foreach ($mya as $user) {
  $mytotal++;
  
  $res=false;
  $user=trim_gks($user);
  
  //echo '|||'.$user.'|||';
  //die();
  
  if (1==1 or (startwith($user, '69') and strlen($user)==10) or startwith($user,'00') or startwith($user,'+')) {
    
    //mysms($model, $model_id, $from, $to, $szMessageText) {
    //echo '<pre>|'.$user.'|'.$sender_sms_sender.'|'.$sender_sms_provider.'|';die();
    //|6988566726|10001|gks_erp_app_mobile|
    $res=gks_sms_send($model,$model_id,$sender_sms_sender,$user,$mymessage,$sender_sms_provider);
    
  }
  if ($res) {
    $myok++;
    $myreport_ok.=$user.'</br>';
 } else {
    $myreport_error.=$user.'</br>';
  }
} 

if ($mytotal == $myok) {
//  if ($model=='hr_interview' and $model_id>0) {
//    $sxolio=gks_lang('Αποστολή SMS στο').': '.$myto.' με κείμενο:<br>'.$mymessage; 
//    $sql="insert into gks_hr_interview_log (interview_id, add_date,user_id,sxolio) values (
//    ".$model_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
//    $result = $db_link->query($sql);        
//    if (!$result) {
//      debug_mail(false,'error sql',$sql);
//      $return = array('success' => false, 'message' => base64_encode('sql error'));
//      echo json_encode($return); die();
//    }
//  }
  $return = array('success' => true, 'message' => base64_encode(gks_lang('Επιτυχής έναρξη προσπάθειας αποστολής')));
  echo json_encode($return); die(); 
  
} else {
  $myout = gks_lang('Αποτυχία αποστολής').'<br/>';
  if ($myok>0) {
    $myout.=gks_lang('Η αποστολή στους παρακάτω αριθμούς ήταν <b>επιτυχής</b>').':<br>'.$myreport_ok.'<br><br>';
  }
  $myout.=gks_lang('Η αποστολή στους παρακάτω αριθμούς <b>δεν ήταν επιτυχής</b>').':<br>'.$myreport_error;
  
  $return = array('success' => false, 'message' => base64_encode($myout ));
  echo json_encode($return); die();  
}



