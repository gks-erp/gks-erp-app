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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση παρτίδας-serial number');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_product_lots','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$term_array = array();
$temp=explode(' ',$term);
foreach ($temp as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    if (in_array($value, $term_array)==false) $term_array[] = $value;
    //$value = greekkeybord($value);
    
  }
}

$product_id=0; if (isset($_GET['product_id'])) $product_id=intval($_GET['product_id']);
if ($product_id<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το είδος')),'list'=>[]);
  echo json_encode($return); die();
}

//print '<pre>';
//print_r($term_array);
$sql="select gks_eshop_product_lots.id_lot_product as id,
gks_eshop_product_lots.lot_name as descr
from gks_eshop_product_lots
where gks_eshop_product_lots.lot_disabled=0 
and gks_eshop_product_lots.lotproduct_id=".$product_id;


$sql.=' and (';

  
$mywhere='';
foreach ($term_array as $value) {
  $value_en = greekkeybord($value);
  $mywhere.=" (
  gks_eshop_product_lots.lot_name like '%".$db_link->escape_string($value)."%' or
  gks_eshop_product_lots.lot_descr like '%".$db_link->escape_string($value)."%' or
  
  gks_eshop_product_lots.lot_name like '%".$db_link->escape_string($value_en)."%' or
  gks_eshop_product_lots.lot_descr like '%".$db_link->escape_string($value_en)."%'
  ) and ";
} 

if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
$sql.=$mywhere.")  
order by gks_eshop_product_lots.lot_sortorder, gks_eshop_product_lots.lot_name
limit 1000"; 


//print '<pre>'.$sql;die();


$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$out=array();
$data=array();
while ($row = $result->fetch_assoc()) {
  $data[$row['id']]=$row;
}




foreach ($data as $row) {
  $out[] = array('id' => $row['id'], 'value' => $row['descr']);
}

//print_r($out);
$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



