<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) < 3 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση αποθήκης');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_warehouses','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_warehouse_ids=gks_permission_user_condition($my_wp_user_id,'gks_warehouses','01');


$sql="SELECT id_warehouse,warehouse_name,
warehouse_branch,warehouse_odos,warehouse_arithmos,
warehouse_orofos,warehouse_perioxi,warehouse_poli,warehouse_tk,
warehouse_nomos_id,warehouse_country_id,
warehouse_map_latitude,warehouse_map_longitude

FROM gks_warehouses
where warehouse_disable=0 and is_virtual=0 and (
warehouse_name like '%".$db_link->escape_string($term)."%' or
warehouse_phone like '%".$db_link->escape_string($term)."%' or
warehouse_email like '%".$db_link->escape_string($term)."%' or
warehouse_odos like '%".$db_link->escape_string($term)."%' or
warehouse_perioxi like '%".$db_link->escape_string($term)."%' or
warehouse_poli like '%".$db_link->escape_string($term)."%'
)";
if (isset($_GET['company_id'])) {
  $company_id=intval($_GET['company_id']);
  if ($company_id>0) {
    $sql.=" and company_id=".$company_id;
    $company_sub_id=0; if (isset($_GET['company_sub_id'])) $company_sub_id=intval($_GET['company_sub_id']);
    if ($company_sub_id>=0) {
      $sql.=" and company_sub_id=".$company_sub_id;
    }
  }
}
if (count($perm_id_warehouse_ids)>0) $sql.=" and gks_warehouses.id_warehouse in (".implode(',',$perm_id_warehouse_ids).")";

$sql.=" order by gks_warehouses.warehouse_sortorder, gks_warehouses.warehouse_name
limit 1000";
//echo $sql;die();
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  $out[] = array(
    'id' => intval($row['id_warehouse']), 
    'value' => trim_gks($row['warehouse_name']),
    'warehouse_branch' => trim_gks($row['warehouse_branch']),
    'warehouse_odos' => trim_gks($row['warehouse_odos']),
    'warehouse_arithmos' => trim_gks($row['warehouse_arithmos']),
    'warehouse_orofos' => trim_gks($row['warehouse_orofos']),
    'warehouse_perioxi' => trim_gks($row['warehouse_perioxi']),
    'warehouse_poli' => trim_gks($row['warehouse_poli']),
    'warehouse_tk' => trim_gks($row['warehouse_tk']),
    'warehouse_nomos_id' => intval($row['warehouse_nomos_id']),
    'warehouse_country_id' => intval($row['warehouse_country_id']),
    'warehouse_map_latitude' => floatval($row['warehouse_map_latitude']),
    'warehouse_map_longitude' => floatval($row['warehouse_map_longitude']),
  );
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



