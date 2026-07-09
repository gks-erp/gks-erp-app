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
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Φορολογικής Θέσης').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_aade',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_eshop_fiscal_position where id_fiscal_position = ".$id;
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


$fiscal_position_descr=''; if (isset($_POST['fiscal_position_descr'])) $fiscal_position_descr=trim_gks(base64_decode($_POST['fiscal_position_descr']));
$fiscal_position_sortorder=''; if (isset($_POST['fiscal_position_sortorder'])) $fiscal_position_sortorder=intval($_POST['fiscal_position_sortorder']);
$fiscal_position_disable=''; if (isset($_POST['fiscal_position_disable'])) $fiscal_position_disable=intval($_POST['fiscal_position_disable']);


if ($fiscal_position_descr=='') {debug_mail(false,'emptyl',gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }



$redirect='';
if ($id==-1) {
  $sql="insert into gks_eshop_fiscal_position (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-eshop-fiscal-position-item.php?id='.$id); 
}



$sql="update gks_eshop_fiscal_position set 
fiscal_position_descr='".$db_link->escape_string($fiscal_position_descr)."',
fiscal_position_sortorder=".$fiscal_position_sortorder.",
fiscal_position_disable=".$fiscal_position_disable.",
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_fiscal_position = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  


gks_lang_data_obj_save_exec_php('gks_eshop_fiscal_position',$id);
 
gks_cache_update_menu_version(-1);

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

