<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$mymassids=[];
if (isset($_POST['mymassids'])) {
  $temp=trim_gks(base64_decode($_POST['mymassids']));
  $temp=explode(',',$temp);
  foreach ($temp as $value) {
    $value=intval($value);
    if ($value>0) $mymassids[]=$value;
  } 
}
//echo '<pre>';print_r($mymassids);die();

if (count($mymassids)==0) {
  $id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
  if ($id<=0) {
    debug_mail(false,'the myid is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
    echo json_encode($return); die();}
  $mymassids[]=$id;
} else {
  $id=0;  
}



$order_state='';  if (isset($_POST['newstate'])) $order_state=trim_gks(base64_decode($_POST['newstate']));
if ($order_state=='') {
  debug_mail(false,'the newstate is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η νέα κατάσταση')));
  echo json_encode($return); die();}
  
$oldstate='';  if (isset($_POST['oldstate'])) $oldstate=trim_gks(base64_decode($_POST['oldstate']));
if ($oldstate=='') {
  debug_mail(false,'the oldstate is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η προηγούμενη κατάσταση')));
  echo json_encode($return); die();}
  

$my_page_title=gks_lang(gks_lang('Αποθήκευση Αλλαγή κατάσταση παραγγελίας'));
db_open();
stat_record();


foreach ($mymassids as $id) {

  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders','edit',$id);
  if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
  $perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
  $perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
  $perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
  $perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');
  
  $myarray_old=array();
  $myarray_line_old=array();
  $row_old=array();
  $products_old=array();
  $extra_address_old=array();
  $all_products_for_balance=array();
  
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
  $row_old = $result->fetch_assoc();
  $order_state_old=trim_gks($row_old['order_state']);
  
  $sql="SELECT gks_orders_products.*, gks_monades_metrisis.monada_descr, gks_eshop_fpa_base.fpa_base_descr
  FROM (gks_orders_products 
  LEFT JOIN gks_monades_metrisis ON gks_orders_products.product_monada_id = gks_monades_metrisis.id_monada)
  LEFT JOIN gks_eshop_fpa_base ON gks_orders_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base
  WHERE gks_orders_products.order_id=".$id."
  ORDER BY gks_orders_products.product_aa;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row = $result->fetch_assoc()) {
    $products_old[]=$row;
    if ($row['product_id']>0 and in_array($row['product_id'],$all_products_for_balance)==false)
      $all_products_for_balance[]=$row['product_id'];    
  }
  
  if ($row_old['address_extra']>0) {
    $sql="SELECT gks_users_extra_address.*, gks_nomoi.nomos_descr, gks_country.country_name
    FROM (gks_users_extra_address 
    LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
    LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
    WHERE gks_users_extra_address.id_users_extra_address=".$row_old['address_extra'];
    $result_select = $db_link->query($sql);        
    if (!$result_select) {
      debug_mail(false,'error sql',$sql);
      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    }
    if ($result_select->num_rows==1) {
      $extra_address_old = $result_select->fetch_assoc();
    }
  }
  
  $idiotites_old=get_order_details_txt($id, $myarray_old, $myarray_line_old); 
  
  if ($oldstate!=$order_state_old) {
    debug_mail(false,'the oldstate !=order_state_old ',$oldstate.' '.$order_state_old);
    
    $message=gks_lang('Η υπάρχουσα κατάσταση της παραγγελίας είναι').'<br>'.
    '<span class="order_state_'.$order_state_old.'">'.getOrderStateDescr($order_state_old).'</span><br> '.
    gks_lang('ενώ θα έπρεπε να είναι').'<br>'.
    '<span class="order_state_'.$oldstate.'">'.getOrderStateDescr($oldstate).'</span><br>'.
    gks_lang('Ανανεώστε την σελίδα');
    $message=str_replace('[1]',$delivery_method_html,$message);
    $message=str_replace('[2]',$delivery_method_html,$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
    
  //$return = array('success' => false, 'message' => base64_encode('gggggggggg'));echo json_encode($return); die();

  $gks_custom_prepare=gks_custom_table_item_prepare('gks_orders',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
  

    
  $sql="update gks_orders set 
  order_state='".$db_link->escape_string($order_state)."', 
  
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."',
  session_id='".$_gks_id_session."'
  where id_order = ".$id." limit 1";  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  
  $sql="update gks_orders_products set p_order_state='".$db_link->escape_string($order_state!='' ? $order_state : $order_state_old)."' where order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}    
  
  
  
  
  gks_whi_mov_balance_calc($all_products_for_balance);
  gks_whi_after_balance_for_order($id);
  gks_order_sxolio_log($id,$row_old,$products_old,$extra_address_old,'',$gks_custom_row_old);
  $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
  //echo '<pre>'; echo $row_old['user_id'];die();
  
  if ($GKS_ORDERS_PRODUCTION) {
    //echo '<pre>';echo time();die();
    
    gks_order_production_warehouses_set($id);
    gks_production_order_sintagi($id);
    gks_whi_mov_balance_calc_for_production_sintagi($id);
    
    
    gks_production_order_ergasies($id);
    gks_production_order_calc_ergasies_setready($id);
    gks_production_order_calc_ergasies_tree($id);
    
    
  }
  
  gks_plugins_functions_run('admin_production_posto_change_order_state_exec_after',array(
    'id'=>&$id,
  ));

  
}




$return = array('success' => true, 'message' => base64_encode('OK'), 'myid' => $id);
echo json_encode($return); die();
