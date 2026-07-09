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
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Αποθηκών από Παραγωγή Παραγγελίας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_item',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');





$sql=select_gks_orders($id)." where id_order=".$id;
if (count($perm_id_company_ids)>0) $sql.=" and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";

$sql.=" limit 1"; 
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}
$row = $result->fetch_assoc();
$order_state=trim_gks($row['order_state']);

if ($order_state!='060registered' and $order_state!='070inproduction' and $order_state!='090indelivery') {
  debug_mail(false,'error sql','order_state not good '.getOrderStateDescr('070inproduction'));
  $return = array('success' => false, 'message' => base64_encode(
   gks_lang('Η παραγγελία δεν είναι σε κατάσταση').'<br><span class="order_state_060registered">'.getOrderStateDescr('060registered').'</span><br>'.gks_lang('ή σε').'<br><span class="order_state_070inproduction">'.getOrderStateDescr('070inproduction').'</span><br>'.gks_lang('ή σε').'<br><span class="order_state_090indelivery">'.getOrderStateDescr('090indelivery').'</span>'));
  echo json_encode($return); die();}  


$prod_warehouses_id_from=0; if (isset($_POST['prod_warehouses_id_from'])) $prod_warehouses_id_from=intval($_POST['prod_warehouses_id_from']);
$prod_warehouses_id_to=0;   if (isset($_POST['prod_warehouses_id_to']))   $prod_warehouses_id_to=intval($_POST['prod_warehouses_id_to']);

if ($prod_warehouses_id_from<=0) {
  debug_mail(false,'warning prod_warehouses_id_from');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την αποθήκη <b>Υλικά Από:</b>')));
  echo json_encode($return); die();}
if ($prod_warehouses_id_to<=0) {
  debug_mail(false,'warning prod_warehouses_id_to');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την αποθήκη <b>Παραγόμενα Σε:</b>')));
  echo json_encode($return); die();}

$sql="update gks_orders set
prod_warehouses_id_from=".$prod_warehouses_id_from.",
prod_warehouses_id_to=".$prod_warehouses_id_to.",
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_order=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '');
echo json_encode($return); die();
