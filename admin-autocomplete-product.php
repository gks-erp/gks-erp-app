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

$my_page_title=gks_lang('Αυτόματη συμπλήρωση προϊόντος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','autocomplete',0);
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
//print '<pre>';
//print_r($term_array);

$sql="SELECT 
gks_eshop_products.id_product,
gks_eshop_products.product_code,
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_photo<>'' THEN
        gks_eshop_products.product_photo
      ELSE
        gks_eshop_products_parent.product_photo
    END
  ELSE gks_eshop_products.product_photo

END as product_photo_p,
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_descr<>'' THEN
        gks_eshop_products.product_descr
      ELSE
        CASE
          WHEN gks_eshop_products.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent.product_descr, gks_eshop_products.product_descr_variable)
          ELSE
            gks_eshop_products_parent.product_descr
        END
    END
  ELSE gks_eshop_products.product_descr
END as product_descr_p,
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_descr_small<>'' THEN
        gks_eshop_products.product_descr_small
      ELSE
        CASE
          WHEN gks_eshop_products.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
          ELSE
            gks_eshop_products_parent.product_descr_small
        END
    END
  ELSE gks_eshop_products.product_descr_small
END as product_descr_small_p,
gks_eshop_products.product_monada_id
FROM gks_eshop_products
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product

where gks_eshop_products.product_disable=0 ";
if (isset($_GET['and_variable'])) {
  if ($_GET['and_variable']=='0') {
    $sql.=" and gks_eshop_products.product_class<>'variable_item'";
  } else if ($_GET['and_variable']=='1') {
    $sql.=" ";
  } else if ($_GET['and_variable']=='2') {
    $sql.=" and gks_eshop_products.product_class='variable'";
  }
} else {
  //real products
  $sql.=" and gks_eshop_products.product_class<>'variable'";
}

//if (isset($_GET['and_variable'])==false or $_GET['and_variable']==0) {
//  $sql.=" and gks_eshop_products.product_class<>'variable_item'";
//}

//echo '<pre>';print_r( $_GET['base_types']);die();

if (isset($_GET['base_types']) and is_array($_GET['base_types'])) {
  $sql_btype=array();
  foreach ($_GET['base_types'] as $value) {
    $value=intval($value);
    if ($value==0 or $value==1 or $value==2) {
      if (in_array($value,$sql_btype)==false) $sql_btype[]=$value;
    }
  }
  //echo '<pre>';print_r( $sql_btype);die();

  if (count($sql_btype)>0) {
    $sql.=" and gks_eshop_products.product_base_type in (".implode(',',$sql_btype).")";
  }
}

if (isset($_GET['onlylotserial']) and is_array($_GET['onlylotserial'])) {
  $sql_lotserial=array();
  foreach ($_GET['onlylotserial'] as $value) {
    $value=trim_gks($value);
    if ($value=='lot' or $value=='serial') {
      $value="'".$db_link->escape_string($value)."'";
      if (in_array($value,$sql_lotserial)==false) $sql_lotserial[]=$value;
    }
  }
  if (count($sql_lotserial)>0) {
    $sql.=" and gks_eshop_products.product_lot_serial in (".implode(',',$sql_lotserial).")";
  }
  
}



$sql.=' and (';

//echo $sql; die();

if (isset($_GET['onlycode']) and $_GET['onlycode']==1) {
  $mywhere='';
  foreach ($term_array as $value) {
    $value_en = greekkeybord($value);
    $mywhere.=" (gks_eshop_products.product_code like '%".$db_link->escape_string($value)."%' or gks_eshop_products.product_code like '%".$db_link->escape_string($value_en)."%') and ";
  } 
  if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
  $sql.=$mywhere.")  order by gks_eshop_products.product_code
  limit 1000";  
  
} else {
  
  $mywhere='';
  foreach ($term_array as $value) {
    $value_en = greekkeybord($value);
    $mywhere.=" (
    gks_eshop_products.product_code like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_sku like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_gtin like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_upc like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_ean like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_isbn like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_descr like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products_parent.product_descr like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_descr_variable like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_descr_small like '%".$db_link->escape_string($value)."%' or
    gks_eshop_products.product_object_name like '%".$db_link->escape_string($value)."%' or 
    
    gks_eshop_products.product_code like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_sku like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_gtin like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_upc like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_ean like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_isbn like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_descr like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products_parent.product_descr like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_descr_variable like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_descr_small like '%".$db_link->escape_string($value_en)."%' or
    gks_eshop_products.product_object_name like '%".$db_link->escape_string($value_en)."%'
    ) and ";
  } 
  
  if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
  $sql.=$mywhere.")  order by gks_eshop_products.product_code,gks_eshop_products.product_descr,gks_eshop_products.product_descr_variable
  limit 1000"; 
}

//print '<pre>'.$sql;die();


$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$fount_count=0;
$out=array();
$gks_mode=''; if (isset($_GET['mode'])) $gks_mode=trim_gks($_GET['mode']);

$data=array();
while ($row = $result->fetch_assoc()) {
  $data[$row['id_product']]=$row;
}




foreach ($data as $row) {

  $fount_count++;
  $descr=trim_gks($row['product_descr_p']);
//  if ($descr=='') {
//    $descr=trim_gks(mb_substr($row['product_descr'],0,100));
//  }
  if (mb_strlen($descr)>100) $descr=mb_substr($descr,100).'...';
  //$descr=$row['product_code'].($descr=='' ? '' : ' '.$descr);
  
  $product_code=trim_gks($row['product_code']);
  if ($product_code=='') $product_code='--';
  
  if ($gks_mode=='simple') {
    $out[] = array('id' => $row['id_product'], 'value' => $descr);
  } else if ($gks_mode=='photo') {
    $out[] = array('id' => $row['id_product'], 'value' => $product_code, 'descr' => $descr, 'photo'=> getProductPhoto($row['id_product'],$row['product_photo_p'],32));
  } else {
    $out[] = array('id' => $row['id_product'], 'value' => $product_code, 'descr' => $descr);
  }

}

//print_r($out);
$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();

echo json_encode($out);



