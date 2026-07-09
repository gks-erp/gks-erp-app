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


$my_page_title=gks_lang('Αποθήκευση Παρτίδας-Serial Number id').': '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_product_lots',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($id>0) {
  $sql ="SELECT * FROM gks_eshop_product_lots where id_lot_product = ".$id;
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

$lotproduct_id=''; if (isset($_POST['lotproduct_id'])) $lotproduct_id=intval($_POST['lotproduct_id']);
$lot_name=''; if (isset($_POST['lot_name'])) $lot_name=trim_gks(base64_decode($_POST['lot_name']));
$lot_descr=''; if (isset($_POST['lot_descr'])) $lot_descr=trim_gks(base64_decode($_POST['lot_descr']));
$lot_sortorder=0; if (isset($_POST['lot_sortorder'])) $lot_sortorder=intval($_POST['lot_sortorder']);
$lot_disabled=0; if (isset($_POST['lot_disabled'])) $lot_disabled=intval($_POST['lot_disabled']);

if ($_POST['lot_date_production'] == '__/__/____') $_POST['lot_date_production']='';
$lot_date_production=trim_gks(base64_decode($_POST['lot_date_production']));
if ($lot_date_production=='__/__/____') $lot_date_production='';
if ($lot_date_production!='') {
  $lot_date_production = mystrtodb($lot_date_production.' 00:00');
}
if ($_POST['lot_date_expire'] == '__/__/____') $_POST['lot_date_expire']='';
$lot_date_expire=trim_gks(base64_decode($_POST['lot_date_expire']));
if ($lot_date_expire=='__/__/____') $lot_date_expire='';
if ($lot_date_expire!='') {
  $lot_date_expire = mystrtodb($lot_date_expire.' 00:00');
}
//echo '<pre>'.$lot_date_production."\n".$lot_date_expire;die();

if ($lot_name=='') {debug_mail(false,'emptyl',                   gks_lang('Η παρτίδα-serial number δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η παρτίδα-serial number δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }

if ($lotproduct_id<=0) {debug_mail(false,'emptyl',               gks_lang('Το είδος δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το είδος δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }


$sql="select * from gks_eshop_products where id_product=".$lotproduct_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows<=0) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το είδος με ID').':'.$lotproduct_id));
  echo json_encode($return); die(); }

$row = $result->fetch_assoc();
$product_lot_serial=trim_gks($row['product_lot_serial']);
$product_parent_id=intval($row['product_parent_id']);

if ($product_lot_serial!='lot' and $product_lot_serial!='serial') {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το είδος δεν έχει ορισθεί για παρακαλούθηση πατρίδας ή serial number')));
  echo json_encode($return); die(); }

$others_ids=array();
if ($product_parent_id>0) {
  $sql="select id_product from gks_eshop_products where product_parent_id=".$product_parent_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $others_ids[]=$product_parent_id;
  while ($row = $result->fetch_assoc()) {
    $others_ids[]=$row['id_product'];
  }
  
}

$sql="select * from gks_eshop_product_lots 
where lot_name like '".$db_link->escape_string($lot_name)."' 
and id_lot_product<>".$id." 
and (lotproduct_id=".$lotproduct_id;
if (count($others_ids)>0) $sql.=" or lotproduct_id in (".implode(',',$others_ids).")";
$sql.=")";
//echo '<pre>'.$sql;die();

$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η παρτίδα - serial number<b>[1]</b> υπάρχει ήδη:<br><br><a href="admin-products-lots-item.php?id=[2]" class="gks_link">[3]</a>');
  $message=str_replace('[1]', $lot_descr, $message);
  $message=str_replace('[2]', $id_lot_product, $message);
  $message=str_replace('[3]', gks_lang('Προβολή'), $message);
  
  
  debug_mail(false,'monada metrisis exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_eshop_product_lots');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_eshop_product_lots (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-products-lots-item.php?id='.$id); 
}

$sql="update gks_eshop_product_lots set 
lotproduct_id=".$lotproduct_id.",
lot_name='".$db_link->escape_string($lot_name)."',
lot_descr='".$db_link->escape_string($lot_descr)."',
lot_date_production=".($lot_date_production=='' ? 'null' : "'".$db_link->escape_string($lot_date_production)."'").",
lot_date_expire=".($lot_date_expire=='' ? 'null' : "'".$db_link->escape_string($lot_date_expire)."'").",
lot_sortorder=".$lot_sortorder.",
lot_disabled=".$lot_disabled.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_lot_product = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

