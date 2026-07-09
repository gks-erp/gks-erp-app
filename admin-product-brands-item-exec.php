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

$my_page_title=gks_lang('Αποθήκευση μάρκας προϊόντων').': '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products_brands',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_eshop_products_brands where id_product_brand = ".$id;
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

$product_brand_descr=trim_gks(stripslashes(urldecode($_POST['product_brand_descr'])));
$brand_comments=''; if (isset($_POST['brand_comments'])) $brand_comments=trim_gks(base64_decode($_POST['brand_comments']));
$brand_disable=0; if (isset($_POST['brand_disable'])) $brand_disable=intval($_POST['brand_disable']);
$brand_photo=trim_gks(stripslashes(urldecode($_POST['brand_photo'])));



if ($product_brand_descr=='') {debug_mail(false,'emptyl',       gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }


$product_brand_parent_id=intval($_POST['product_brand_parent_id']);

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_eshop_products_brands');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_eshop_products_brands (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-product-brands-item.php?id='.$id); 
}

$sql="update gks_eshop_products_brands set 
product_brand_descr='".$db_link->escape_string($product_brand_descr)."',
product_brand_parent_id=".$product_brand_parent_id.",
brand_comments=". ($brand_comments =='' ? 'null' : "'".$db_link->escape_string($brand_comments)."'").",
brand_disable=".$brand_disable.",
brand_photo='".$db_link->escape_string($brand_photo)."',
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_product_brand = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

gks_lang_data_obj_save_exec_php('gks_eshop_products_brands',$id);

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

