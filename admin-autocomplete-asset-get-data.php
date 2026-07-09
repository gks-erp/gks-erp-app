<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$asset_id=0;
if (isset($_POST['asset_id'])) $asset_id=intval($_POST['asset_id']);
if ($asset_id<=0) die(json_encode(array('success'=> false, 'descr' => 'empty asset_id')));






$my_page_title=gks_lang('Λήψη δεδομένων παγίου');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="SELECT gks_assets.*, gks_assets_type.asset_type_descr, gks_warehouses.warehouse_name, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_company.company_title
FROM (((gks_assets 
LEFT JOIN gks_assets_type ON gks_assets.asset_type = gks_assets_type.id_asset_type) 
LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets.asset_last_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_company ON gks_assets.asset_last_company_id = gks_company.id_company

WHERE gks_assets.id_asset=".$asset_id;
//debug_mail(false,'asset1 sql',$sql);
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  die();
}
if ($result->num_rows<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το πάγιο')));
  echo json_encode($return); die();
}
$row = $result->fetch_assoc();
//$asset_type= intval($row['asset_type']);
//$asset_serialnumber= $row['asset_serialnumber'].'';
//$asset_type_descr= $row['asset_type_descr'].'';

$data=$row;

$return = array('success' => true, 'message' => base64_encode('OK'), 'data' => $data);
echo json_encode($return); die();
