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

$warehouse_id=0;
if (isset($_POST['warehouse_id'])) $warehouse_id=intval($_POST['warehouse_id']);
if ($warehouse_id<=0) {
  debug_mail(false,'the warehouse_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η αποθήκη')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Μεταφορά παγίου σε αποθήκη');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_moves','add',-1);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}







$sql="SELECT id_asset,asset_last_warehouse_id,asset_code,asset_title,asset_serialnumber,asset_type FROM gks_assets where id_asset = ".$asset_id;
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
$asset_code=$row['asset_code'];
$asset_title=$row['asset_title'];
$asset_serialnumber=$row['asset_serialnumber'];
$asset_type=$row['asset_type'];

if ($asset_type == 16) {
  $sql="SELECT id_asset, asset_code, asset_title, asset_serialnumber FROM gks_assets WHERE asset_type=16 AND asset_last_warehouse_id=".$warehouse_id." and id_asset<>".$asset_id;
  $sql="SELECT gks_assets.id_asset, gks_assets.asset_code, gks_assets.asset_title, gks_assets.asset_serialnumber
  FROM gks_assets
  LEFT JOIN gks_ergastiria ON gks_assets.asset_last_warehouse_id = gks_ergastiria.id_ergastirio
  WHERE id_asset<>".$asset_id."
  and gks_assets.asset_type=16
  AND gks_assets.asset_last_warehouse_id=".$warehouse_id."
  AND gks_ergastiria.ergastirio_is_kentriko=0";


  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows>=1) {
    debug_mail(false,'tameiaki exist',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτή η αποθήκη έχει ήδη ταμειακή μηχανή')));
    echo json_encode($return); die(); }
}

$sql="insert into gks_assets_moves (asset_id,warehouse_id,user_id,mydate,user_id_add,action_myip) values (
".$asset_id.",
".$warehouse_id.",
0,
now(),
".$my_wp_user_id.",
'".$gkIP."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}

$sql="update gks_assets set asset_last_warehouse_id=".$warehouse_id.",asset_last_user_id=0 where id_asset=".$asset_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}


$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();




