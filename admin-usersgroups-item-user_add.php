<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');



$id=0;
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η ομάδα')));
  echo json_encode($return); die();}

$user_id=0;
if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
if ($user_id<=0) {
  debug_mail(false,'the user_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί επαφή')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Προσθήκη επαφής σε ομάδα επαφών');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$sql="SELECT id_users_group FROM gks_users_groups where id_users_group = ".$id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty',                                      gks_lang('Δεν βρέθηκε η ομάδα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η ομάδα')));
  echo json_encode($return); die();}  


//$sql="SELECT ID FROM ".GKS_WP_TABLE_PREFIX."users where ID = ".$user_id." and gks_wp_capabilities not like '".$db_link->escape_string('a:1:{s:10:"subscriber";b:1;}')."'";
//$result = $db_link->query($sql);
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  echo json_encode($return); die();
//}
//if ($result->num_rows == 0) {
//  debug_mail(false,'empty',gks_lang('Δεν βρέθηκε ο χρήστης'));
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο χρήστης').'<br>'.gks_lang('Δεν θα πρέπει να είναι συνδρομητής')));
//  echo json_encode($return); die();}  


$sql="SELECT * FROM gks_users_groups_users where user_id = ".$user_id." and group_id=".$id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'empty',gks_lang('Η επαφή - ομάδα υπάρχει ήδη'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η επαφή - ομάδα υπάρχει ήδη')));
  echo json_encode($return); die();}  


$sql="insert into gks_users_groups_users (group_id,user_id,is_omadarxis,
user_id_add,user_id_edit,mydate_add,mydate_edit,myip
) values (
".$id.",
".$user_id.",
0,
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}


$sql="insert into gks_log_users_groups_users (action_date,action_user_id,action_type,action_myip,group_id,user_id,is_omadarxis) values(
NOW(),
".$my_wp_user_id.",
'add',
'".$db_link->escape_string($gkIP)."',
".$id.",
".$user_id.",
0)";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}



$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();