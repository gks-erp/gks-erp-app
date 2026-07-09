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

$my_page_title=gks_lang('Αποθήκευση στοιχείου τιμοκαταλόγου').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_pricelist_items',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_eshop_pricelist_items where id_pricelist_item = ".$id;
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

$pricelist_id=0;if (isset($_POST['pricelist_id'])) $pricelist_id=intval($_POST['pricelist_id']);
$pricelist_item_descr='';if (isset($_POST['pricelist_item_descr'])) $pricelist_item_descr=trim_gks(base64_decode($_POST['pricelist_item_descr']));
$pricelist_item_sequence=0; if (isset($_POST['pricelist_item_sequence'])) $pricelist_item_sequence=intval($_POST['pricelist_item_sequence']);
$pricelist_item_disable=0;if (isset($_POST['pricelist_item_disable'])) $pricelist_item_disable=intval($_POST['pricelist_item_disable']);
$pricelist_item_coupon='';if (isset($_POST['pricelist_item_coupon'])) $pricelist_item_coupon=trim_gks(base64_decode($_POST['pricelist_item_coupon']));
if ($_POST['pricelist_item_date_from'] == '__/__/____ __:__') $_POST['pricelist_item_date_from']='';
$pricelist_item_date_from=trim_gks(stripslashes(urldecode($_POST['pricelist_item_date_from'])));
if ($pricelist_item_date_from!='') {
  $pricelist_item_date_from = mystrtodb($pricelist_item_date_from);
}
if ($_POST['pricelist_item_date_to'] == '__/__/____ __:__') $_POST['pricelist_item_date_to']='';
$pricelist_item_date_to=trim_gks(stripslashes(urldecode($_POST['pricelist_item_date_to'])));
if ($pricelist_item_date_to!='') {
  $pricelist_item_date_to = mystrtodb($pricelist_item_date_to);
}
$pricelist_item_min_posotita=0;if (isset($_POST['pricelist_item_min_posotita'])) $pricelist_item_min_posotita=floatval(str_replace(',','.', $_POST['pricelist_item_min_posotita']));
$pricelist_item_price_epi=0;if (isset($_POST['pricelist_item_price_epi'])) $pricelist_item_price_epi=floatval(str_replace(',','.', $_POST['pricelist_item_price_epi']));
$pricelist_item_price_plus=0;if (isset($_POST['pricelist_item_price_plus'])) $pricelist_item_price_plus=floatval(str_replace(',','.', $_POST['pricelist_item_price_plus']));
$pricelist_item_price_eval='';if (isset($_POST['pricelist_item_price_eval'])) $pricelist_item_price_eval=trim_gks(base64_decode($_POST['pricelist_item_price_eval']));

if ($pricelist_item_price_eval != '' and substr($pricelist_item_price_eval, 0, 1) != '=')  {
  debug_mail(false,gks_lang('Η έκφραση θα πρέπει να ξεκινά από το σύμβολο ='),$pricelist_item_price_eval);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η έκφραση θα πρέπει να ξεκινά από το σύμβολο =')));
  echo json_encode($return); die(); }

if ($pricelist_id<=0) {debug_mail(false,'emptyl',                gks_lang('Ο τιμοκατάλογος δεν μπορεί να είναι κενός'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο τιμοκατάλογος δεν μπορεί να είναι κενός')));
  echo json_encode($return); die(); }

if ($pricelist_item_descr=='') {debug_mail(false,'emptyl',       gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }

if ($pricelist_item_sequence<=0) {debug_mail(false,'emptyl',     gks_lang('Η Σειρά δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Σειρά δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }


$pricelist_item_min_price=0;if (isset($_POST['pricelist_item_min_price'])) $pricelist_item_min_price=floatval(str_replace(',','.', $_POST['pricelist_item_min_price']));
$pricelist_item_max_price=0;if (isset($_POST['pricelist_item_max_price'])) $pricelist_item_max_price=floatval(str_replace(',','.', $_POST['pricelist_item_max_price']));
$pricelist_item_individual_use=0;if (isset($_POST['pricelist_item_individual_use'])) $pricelist_item_individual_use=intval($_POST['pricelist_item_individual_use']);
$pricelist_item_exclude_sale_items=0;if (isset($_POST['pricelist_item_exclude_sale_items'])) $pricelist_item_exclude_sale_items=intval($_POST['pricelist_item_exclude_sale_items']);
$pricelist_item_users_emails='';if (isset($_POST['pricelist_item_users_emails'])) $pricelist_item_users_emails=trim_gks(base64_decode($_POST['pricelist_item_users_emails']));
$pricelist_item_usage_limit=0;if (isset($_POST['pricelist_item_usage_limit'])) $pricelist_item_usage_limit=intval($_POST['pricelist_item_usage_limit']);
$pricelist_item_limit_usage_to_x_items=0;if (isset($_POST['pricelist_item_limit_usage_to_x_items'])) $pricelist_item_limit_usage_to_x_items=intval($_POST['pricelist_item_limit_usage_to_x_items']);
$pricelist_item_usage_limit_per_user=0;if (isset($_POST['pricelist_item_usage_limit_per_user'])) $pricelist_item_usage_limit_per_user=intval($_POST['pricelist_item_usage_limit_per_user']);


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_eshop_pricelist_items');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_eshop_pricelist_items (mydate_add,mydate_edit,user_id_add,user_id_edit,myip) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-pricelists-items-item.php?id='.$id); 
}



$sql="update gks_eshop_pricelist_items set 
pricelist_id=".$pricelist_id.",
pricelist_item_descr='".$db_link->escape_string($pricelist_item_descr)."',
pricelist_item_sequence=".$pricelist_item_sequence.",
pricelist_item_coupon = ". ($pricelist_item_coupon=='' ? 'null' : "'".$db_link->escape_string($pricelist_item_coupon)."'").",
pricelist_item_date_from = ". ($pricelist_item_date_from=='' ? 'null' : "'".$pricelist_item_date_from."'").",
pricelist_item_date_to = ".   ($pricelist_item_date_to=='' ?   'null' : "'".$pricelist_item_date_to."'").",
pricelist_item_min_posotita=".$pricelist_item_min_posotita.",
pricelist_item_price_epi=".number_format($pricelist_item_price_epi,8,'.','').",
pricelist_item_price_plus=".number_format($pricelist_item_price_plus,8,'.','').",
pricelist_item_price_eval = ". ($pricelist_item_price_eval=='' ? 'null' : "'".$db_link->escape_string($pricelist_item_price_eval)."'").",

pricelist_item_min_price=".$pricelist_item_min_price.",
pricelist_item_max_price=".$pricelist_item_max_price.",
pricelist_item_individual_use=".$pricelist_item_individual_use.",
pricelist_item_exclude_sale_items=".$pricelist_item_exclude_sale_items.",
pricelist_item_users_emails='".$db_link->escape_string($pricelist_item_users_emails)."',
pricelist_item_usage_limit=".$pricelist_item_usage_limit.",
pricelist_item_limit_usage_to_x_items=".$pricelist_item_limit_usage_to_x_items.",
pricelist_item_usage_limit_per_user=".$pricelist_item_usage_limit_per_user.",

pricelist_item_disable=".$pricelist_item_disable.",
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_pricelist_item = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

