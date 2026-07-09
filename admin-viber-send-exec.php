<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Εκτέλεση Αποστολής Viber');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_viber_msgs','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$myto=0; if (isset($_POST['to'])) $myto=intval($_POST['to']);
if ($myto<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποιον αποδέκτη')));
  echo json_encode($return); die();}

$sql="SELECT ID, viber_id,gks_nickname FROM ".GKS_WP_TABLE_PREFIX."users WHERE viber_id<>'' AND viber_subscribed<>0 and ID=".$myto;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}  

$send_viber=array();
while ($row = $result->fetch_assoc()) {
  $send_viber[]=array('id'=>$row['ID'], 'viber_id'=> $row['viber_id'], 'gks_nickname' => $row['gks_nickname']);
}

if (count($send_viber)==0) {
  debug_mail(false,'viber user is not found','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν οι αποδέκτες viber')));
  echo json_encode($return); die();}


if (!isset($_POST['message']) || trim_gks($_POST['message'])=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε κάποιο μήνυμα')));
  echo json_encode($return); die();}
$mymessage=trim_gks(base64_decode($_POST['message']));

$message_viber=$mymessage;

$message_viber=str_replace('<br />',"\n",$message_viber);
$message_viber=str_replace('<br/>',"\n",$message_viber);
$message_viber=str_replace('<br>',"\n",$message_viber);

$message_viber=str_replace("\n\n","\n",$message_viber);
$message_viber=str_replace("\n\n","\n",$message_viber);
$message_viber=str_replace("\n\n","\n",$message_viber);
$message_viber=str_replace("\n\n","\n",$message_viber);



$model='admin';
if (isset($_POST['model']) && trim_gks($_POST['model'])!='') {
  $model=trim_gks(stripslashes(urldecode($_POST['model'])));
}
$model_id=0;
if (isset($_POST['model_id']) && trim_gks($_POST['model_id'])!='') {
  $model_id=intval(trim_gks(stripslashes(urldecode($_POST['model_id']))));
}

$errors=array();
foreach ($send_viber as $value) {
  $ret = gks_viber_send($model,$model_id,$value['viber_id'],$message_viber);
  if (isset($ret['error'])) $errors[]=$ret['error'];
  if (isset($ret['status_message']) and trim_gks(strtolower($ret['status_message']!='ok'))) $errors[]=$ret['status_message'];
} 


if (count($errors)==0) {
  $return = array('success' => true, 'message' => base64_encode(gks_lang('Επιτυχής αποστολή')));
  echo json_encode($return); die(); 
} else {
  debug_mail(false,'viber send error',print_r($errors,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την αποστολή').':<br>'.implode('<br>',$errors)));
  echo json_encode($return); die();
}
