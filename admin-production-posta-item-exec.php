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
if ($id<=0 and $id<>-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Πόστου').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_posta',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  $sql ="SELECT * FROM gks_production_posta where id_production_posto = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();
}

$production_posto_descr=''; if (isset($_POST['production_posto_descr'])) $production_posto_descr=trim_gks(stripslashes(urldecode($_POST['production_posto_descr'])));
$bypass_time=''; if (isset($_POST['bypass_time'])) $bypass_time=intval($_POST['bypass_time']);
$all_users=''; if (isset($_POST['all_users'])) $all_users=intval($_POST['all_users']);
$production_posto_sortorder=0; if (isset($_POST['production_posto_sortorder'])) $production_posto_sortorder=intval(stripslashes(urldecode($_POST['production_posto_sortorder'])));




if ($production_posto_descr=='') {debug_mail(false,'emptyl',               gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_production_posta');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_production_posta (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }  
  $id = $db_link->insert_id;
  $redirect=base64_encode('admin-production-posta-item.php?id='.$id);  
}

$sql="update gks_production_posta set 
production_posto_descr='".$db_link->escape_string($production_posto_descr)."',
bypass_time=".$bypass_time.",
all_users=".$all_users.",
production_posto_sortorder=".$production_posto_sortorder.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_production_posto = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


$return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> $redirect);
echo json_encode($return); die();

