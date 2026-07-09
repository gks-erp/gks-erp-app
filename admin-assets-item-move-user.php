<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

//die();


$asset_id=0;
if (isset($_POST['asset_id'])) $asset_id=intval($_POST['asset_id']);
if ($asset_id<=0) {
  debug_mail(false,'the asset_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί πάγιο')));
  echo json_encode($return); die();}

$user_id=0;
if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
if ($user_id<=0) {
  debug_mail(false,'the user_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί συνεργάτης')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Μεταφορά παγίου σε συνεργάτη');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_moves','add',-1);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="SELECT id_asset,asset_last_warehouse_id FROM gks_assets where id_asset = ".$asset_id;
$sql.=" limit 1";

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
$row=$result->fetch_assoc();
$warehouse_id = $row['asset_last_warehouse_id'];

if ($warehouse_id<=0) {
  debug_mail(false,'asset not in warehouse');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το πάγιο δεν ανήκει σε κάποιο αποθήκη')));
  echo json_encode($return); die();  
}

$sql="insert into gks_assets_moves (asset_id,warehouse_id,user_id,mydate,user_id_add,action_myip) values (
".$asset_id.",
".$warehouse_id.",
".$user_id.",
now(),
".$my_wp_user_id.",
'".$gkIP."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}

$sql="update gks_assets set asset_last_user_id=".$user_id." where id_asset=".$asset_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}


$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();




