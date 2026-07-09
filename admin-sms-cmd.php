<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$my_page_title=gks_lang('Εκτέλεση Εντολής sms');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sms','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
if ($cmd!='resend') $cmd='';
if ($cmd=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί εντολή')));
  echo json_encode($return); die();}

$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID'));
  echo json_encode($return); die();}

$sql="select * from gks_sms where id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

if ($result->num_rows==0) {
  debug_mail(false,'record not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή')));
  echo json_encode($return); die();}  
  
$row = $result->fetch_assoc();

if ($cmd=='resend') {
  if (gks_sms_can_resend_status($row['status'],$row['model'])==false) {
    $message=gks_lang('Είναι σε κατάσταση <span class="sms_status sms_status_[1]">[2]</span> με μοντέλο <b>[3]</b>');
    $message=str_replace('[1]',$row['status'],$message);
    $message=str_replace('[2]',$row['status_name'],$message);
    $message=str_replace('[3]',$row['model'],$message);
    $message=gks_lang('Αυτό το SMS δεν μπορεί να ξανασταλεί').'<br>'.$message;
    
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  
  $model=$row['model'];
  $model_id=$row['model_id'];
  $sender_sms_provider=$row['sms_provider'];     //gks_erp_app_mobile or smsapi
  $sender_sms_sender= $row['erp_app_mobile_id']; //p.x. 10001 
  $myto=$row['myto'];
  $mymessage=$row['Message'];
  
  //if (intval($row['sms_mobile_db_id'])==0) {
    
  //} else {
    $res=gks_sms_send($model,$model_id,$sender_sms_sender,$myto,$mymessage,$sender_sms_provider,$id);
    
  //}
  
  if (!$res) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αποτυχία αποστολής')));
    echo json_encode($return); die(); }
  
  $return = array('success' => true, 'message' => base64_encode(gks_lang('Επιτυχής έναρξη προσπάθειας αποστολής')));
  echo json_encode($return); die();
}

echo '<pre>error command';die();
