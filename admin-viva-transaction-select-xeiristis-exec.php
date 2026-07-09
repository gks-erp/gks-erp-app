<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$myid=0;
if (isset($_POST['myid'])) $myid=intval($_POST['myid']);
if ($myid<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$fid=0;
if (isset($_POST['fid'])) $fid=intval($_POST['fid']);
if ($fid<0) {
  debug_mail(false,'the fid is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' x ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Συναλλαγές Viva Αποθήκευση Χειριστή').': '.$myid;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_viva_transaction','edit',0);
if ($perm_ret['success']==false) {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();}

$sql="SELECT add_date,xeiristis_id FROM gks_viva_transaction WHERE id_viva_transaction=".$myid." limit 1";
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

$sql="update gks_viva_transaction set xeiristis_id=".$fid." where id_viva_transaction=".$myid." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}


$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die(); 
