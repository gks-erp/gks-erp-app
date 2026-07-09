<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$ergasia_id=0;
if (isset($_POST['ergasia_id'])) $ergasia_id=intval($_POST['ergasia_id']);
if ($ergasia_id<=0) {
  debug_mail(false,'the ergasia_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εργασία')));
  echo json_encode($return); die();}

$posto_id=0;
if (isset($_POST['posto_id'])) $posto_id=intval($_POST['posto_id']);
if ($posto_id<=0) {
  debug_mail(false,'the posto_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το πόστο')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Προσθήκη πόστου σε εργασία');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_posta','edit',$posto_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}






$sql="SELECT id_production_posto FROM gks_production_posta where id_production_posto = ".$posto_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'posto_id not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το πόστο')));
  echo json_encode($return); die();}  

$sql="SELECT id_production_ergasia FROM gks_production_ergasies where id_production_ergasia = ".$ergasia_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'ergasia_id not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εργασία')));
  echo json_encode($return); die();}  




$sql="SELECT * FROM gks_production_posta_ergasies where production_ergasia_id = ".$ergasia_id." and production_posto_id=".$posto_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'ergasia_idnot found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εργασία - πόστο υπάρχει ήδη').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}  


$sql="insert into gks_production_posta_ergasies (production_posto_id,production_ergasia_id,
user_id_add,user_id_edit,mydate_add,mydate_edit,myip
) values (
".$posto_id.",
".$ergasia_id.",
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
//
//
//$sql="insert into gks_log_users_groups_users (action_date,action_user_id,action_type,action_myip,group_id,user_id,is_omadarxis) values(
//NOW(),
//".$my_wp_user_id.",
//'add',
//'".$db_link->escape_string($gkIP)."',
//".$id.",
//".$user_id.",
//0)";
//$result = $db_link->query($sql);
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  echo json_encode($return); die();
//}



$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();