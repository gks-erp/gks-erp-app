<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$fname='';
if (isset($_POST['fname'])) $fname=base64_decode($_POST['fname']);
if ($fname=='') {
  debug_mail(false,'the fname is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' fname.'));
  echo json_encode($return); die();}

$plid=0;
if (isset($_POST['plid'])) $plid=intval($_POST['plid']);


$pid=0;
if (isset($_POST['pid'])) $pid=intval($_POST['pid']);
if ($pid<=0) {
  debug_mail(false,'the myid is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$myvalue=0;
if (isset($_POST['myvalue'])) $myvalue=floatval($_POST['myvalue']);



$my_page_title=gks_lang('Αποθήκευση τιμής είδους');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','edit',$pid);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

//echo '<pre>'.$fname.'|'.$pid.'|'.$myvalue;die();

if (in_array($fname,['product_kostos','product_price_yperx','product_price_yperx_sale','product_price','product_price_sale','product_price_retail','','product_price_retail_sale'])) {
  $sql="update gks_eshop_products set 
  ".$fname."=".$myvalue."
  where id_product=".$pid;
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
    
  $return = array('success' => true, 'message' => base64_encode('OK'));
  echo json_encode($return); die(); 
} else if (in_array($fname,['product_price_plist','product_price_plist_sale'])) {
  if ($plid<=10000) {
    debug_mail(false,'the plid is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' Plist ID.'));
    echo json_encode($return); die();}  
  
  $sql="select * from gks_eshop_products_prices
  where pricelist_id=".$plid."
  and product_id=".$pid;
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows==0) {
    $sql="insert into gks_eshop_products_prices (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    pricelist_id,product_id,
    product_price_plist,product_price_plist_sale,product_price_plist_include_vat
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$plid.",".$pid.",
    0,0,0
    )";
    $result = $db_link->query($sql);     
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
  }
  
  $sql="update gks_eshop_products_prices set
  ".$fname."=".$myvalue."
  where pricelist_id=".$plid."
  and product_id=".$pid;
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
  $return = array('success' => true, 'message' => base64_encode('OK'));
  echo json_encode($return); die(); 
    
} else if (in_array($fname,['product_price_yperx_include_vat','product_price_include_vat','product_price_retail_include_vat'])) {
  if ($myvalue!=1) $myvalue=0;
  $sql="update gks_eshop_products set 
  ".$fname."=".$myvalue."
  where id_product=".$pid;
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
    
  $return = array('success' => true, 'message' => base64_encode('OK'));
  echo json_encode($return); die(); 

} else if (in_array($fname,['product_price_plist_include_vat'])) {
  if ($plid<=10000) {
    debug_mail(false,'the plid is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' Plist ID.'));
    echo json_encode($return); die();}  
  if ($myvalue!=1) $myvalue=0;
  $sql="select * from gks_eshop_products_prices
  where pricelist_id=".$plid."
  and product_id=".$pid;
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows==0) {
    $sql="insert into gks_eshop_products_prices (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    pricelist_id,product_id,
    product_price_plist,product_price_plist_sale,product_price_plist_include_vat
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$plid.",".$pid.",
    0,0,0
    )";
    $result = $db_link->query($sql);     
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
  }
  
  $sql="update gks_eshop_products_prices set
  ".$fname."=".$myvalue."
  where pricelist_id=".$plid."
  and product_id=".$pid;
  $result = $db_link->query($sql);     
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
  $return = array('success' => true, 'message' => base64_encode('OK'));
  echo json_encode($return); die(); 
   
} else {
  debug_mail(false,'fname is not OK',$fname);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To fname δεν έχει σωστή τιμή')));
  echo json_encode($return); die();  
}

die();

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
