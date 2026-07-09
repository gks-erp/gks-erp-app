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
if (isset($_POST['myid'])) $id=intval($_POST['myid']);
if ($id<=0) {
  debug_mail(false,'the myid is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}

$my_page_title=gks_lang('Αποθήκευση σχολίου παραγωγής από πόστο');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_posta_run_time',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$mytext=''; if (isset($_POST['mytext'])) $mytext=trim_gks(base64_decode($_POST['mytext']));


$sql="update gks_production_line set 
prod_comments='".$db_link->escape_string($mytext)."',
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_production_line=".$id." limit 1";
$result = $db_link->query($sql);     
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}





$return = array('success' => true, 'message' => base64_encode('OK'), 'myid' => $id);
echo json_encode($return); die();
