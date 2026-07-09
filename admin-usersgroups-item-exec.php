<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!=-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση ομάδας επαφών');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_users_groups',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

if ($id>0) {
  $sql="select * from gks_users_groups where id_users_group=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
}


$group_title=''; if (isset($_POST['group_title'])) $group_title=trim_gks(stripslashes(urldecode($_POST['group_title'])));
$group_old_code=''; if (isset($_POST['group_old_code'])) $group_old_code=trim_gks(stripslashes(urldecode($_POST['group_old_code'])));
$group_comments=''; if (isset($_POST['group_comments'])) $group_comments=trim_gks(stripslashes(urldecode($_POST['group_comments'])));
$group_parent_id=0; if (isset($_POST['group_parent_id'])) $group_parent_id=intval($_POST['group_parent_id']);
$group_disable=0; if (isset($_POST['group_disable'])) $group_disable=intval($_POST['group_disable']);





if ($group_title=='') {debug_mail(false,'emptyl',                gks_lang('Το όνομα ΔΕΝ μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα ΔΕΝ μπορεί να είναι κενό')));
  echo json_encode($return); die();}

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_users_groups');

$redirect='';
if ($id==-1) {
  $sql="insert into gks_users_groups (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-usersgroups-item.php?id='.$id); 
}



$sql="update gks_users_groups set 
group_title='".$db_link->escape_string($group_title)."',
group_old_code=". ($group_old_code =='' ? 'null' : "'".$db_link->escape_string($group_old_code)."'").", 
group_comments=". ($group_comments =='' ? 'null' : "'".$db_link->escape_string($group_comments)."'").",
group_parent_id=".$group_parent_id.",
group_disable=".$group_disable.",
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_users_group = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);



$return = array('success' => true, 'message' => base64_encode('OK'),'redirect' => $redirect);
echo json_encode($return); die();







