<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$template_id=0; if (isset($_POST['template_id'])) $template_id=intval($_POST['template_id']);
if ($template_id<=0) {
  debug_mail(false,'the template_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' template_id'));
  echo json_encode($return); die();}

$obj_index=0; if (isset($_POST['obj_index'])) $obj_index=intval($_POST['obj_index']);
if ($obj_index<=0) {
  debug_mail(false,'the obj_index is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' obj_index'));
  echo json_encode($return); die();}

$object_name='';
if ($obj_index==1) $object_name='gks_acc_inv';
else if ($obj_index==2) $object_name='gks_acc_pay';
else if ($obj_index==3) $object_name='gks_whi_mov';
else if ($obj_index==4) $object_name='gks_orders';

if ($object_name=='') {
  debug_mail(false,'the object_name is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' object_name'));
  echo json_encode($return); die();}

$template_name=''; if (isset($_POST['template_name'])) $template_name=trim_gks(base64_decode($_POST['template_name']));
if ($template_name=='') {
  debug_mail(false,'the template_name is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' template_name'));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Ορισμός εγγράφου ως πρότυπο').': '.$template_id;
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_settings_users','edit',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$sql="select * from gks_users_templates where user_id=".$my_wp_user_id." and object_name='".$db_link->escape_string($object_name)."' and template_id=".$template_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows>=1) {
  $sql="update gks_users_templates set 
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."',
  template_name='".$db_link->escape_string($template_name)."'
  where user_id=".$my_wp_user_id." and object_name='".$db_link->escape_string($object_name)."' and template_id=".$template_id;

  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
} else {
  $sql="insert into gks_users_templates (
    user_id,object_name,template_id,template_name,
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip
  ) values (
    ".$my_wp_user_id.",
    '".$db_link->escape_string($object_name)."',
    ".$template_id.",
    '".$db_link->escape_string($template_name)."',
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
}


gks_cache_update_menu_version();

$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();
