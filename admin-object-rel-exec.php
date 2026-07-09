<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$name1=''; if (isset($_POST['name1'])) $name1=trim_gks($_POST['name1']);
$id1=0; if (isset($_POST['id1'])) $id1=intval($_POST['id1']);
$name2=''; if (isset($_POST['name2'])) $name2=trim_gks($_POST['name2']);
$id2=0; if (isset($_POST['id2'])) $id2=intval($_POST['id2']);

if ($name1!='gks_assets' and 
    $name1!='gks_assets_type' and 
    $name1!='gks_assets_service' and 
    $name1!='gks_assets_service_reasons' and 
    $name1!='gks_assets_whi_mov' and 
    $name1!='gks_orders' and 
    $name1!='gks_acc_inv' and 
    $name1!='gks_acc_pay' and 
    $name1!='gks_bank_accounts' and 
    $name1!='wp_users' and 
    $name1!='gks_crm_leads' and 
    $name1!='gks_crm_tasks' and 
    $name1!='gks_crm_machine' and 
    $name1!='gks_calendar' and
    $name1!='gks_crm_channel_sale' and
    $name1!='gks_crm_leads_status' and
    $name1!='gks_crm_tasks_status' and
    $name1!='gks_acc_seires' and
    $name1!='gks_acc_journal' and 
    $name1!='gks_company' and
    $name1!='gks_company_subs' and 
    $name1!='gks_eshop_products' and
    $name1!='gks_eshop_product_lots' and
    $name1!='gks_eshop_products_categories' and
    $name1!='gks_hotel' and
    $name1!='gks_hotel_reservation' and 
    $name1!='gks_hotel_availability' and
    $name1!='gks_hotel_floor' and
    $name1!='gks_hotel_price' and
    $name1!='gks_hotel_room' and
    $name1!='gks_hotel_room_type' and

    $name1!='gks_template_html' and

    $name1!='gks_transfer' and
    $name1!='gks_transfer_reservation' and
    $name1!='gks_transfer_area' and
    $name1!='gks_transfer_oxima_type' and
    $name1!='gks_transfer_pricelist' and
    
    $name1!='gks_poi' and
    $name1!='gks_poi_type' and
    $name1!='gks_poi_diadromes' and
    
    $name1!='gks_print_forms' and
    $name1!='gks_production_ergasies' and
    $name1!='gks_production_posta' and
    $name1!='gks_production_bom' and
    $name1!='gks_users_groups' and
    $name1!='gks_warehouses' and
    $name1!='gks_eshops' and
    $name1!='gks_eshop_products_brands' and
    $name1!='gks_orders_occasion' and
    $name1!='gks_custom_table' and
    $name1!='gks_lang' and
    $name1!='gks_sociallinks_type' and
    $name1!='gks_eshop_pricelist' and
    $name1!='gks_eshop_pricelist_items' and
    startwith($name1,'gks_ct_')==false
    
    ) $name1='';
if ($name2!='gks_assets' and 
    $name2!='gks_assets_type' and 
    $name2!='gks_assets_service' and 
    $name2!='gks_assets_service_reasons' and 
    $name2!='gks_assets_whi_mov' and 
    $name2!='gks_orders' and 
    $name2!='gks_acc_inv' and 
    $name2!='gks_acc_pay' and 
    $name2!='gks_bank_accounts' and 
    $name2!='wp_users' and 
    $name2!='gks_crm_leads' and 
    $name2!='gks_crm_tasks' and 
    $name2!='gks_crm_machine' and 
    $name2!='gks_calendar' and
    $name2!='gks_crm_channel_sale' and
    $name2!='gks_crm_leads_status' and
    $name2!='gks_crm_tasks_status' and
    $name2!='gks_acc_seires' and
    $name2!='gks_acc_journal' and 
    $name2!='gks_company' and
    $name2!='gks_company_subs' and 
    $name2!='gks_eshop_product_lots' and
    $name2!='gks_eshop_products' and
    $name2!='gks_eshop_products_categories' and
    $name2!='gks_hotel' and
    $name2!='gks_hotel_reservation' and 
    $name2!='gks_hotel_availability' and
    $name2!='gks_hotel_floor' and
    $name2!='gks_hotel_price' and
    $name2!='gks_hotel_room' and
    $name2!='gks_hotel_room_type' and
    
    $name2!='gks_template_html' and

    $name2!='gks_transfer' and
    $name2!='gks_transfer_reservation' and
    $name2!='gks_transfer_area' and
    $name2!='gks_transfer_oxima_type' and
    $name2!='gks_transfer_pricelist' and

    $name2!='gks_poi' and
    $name2!='gks_poi_type' and
    $name2!='gks_poi_diadromes' and
    
    $name2!='gks_print_forms' and
    $name2!='gks_production_ergasies' and
    $name2!='gks_production_posta' and
    $name2!='gks_production_bom' and
    $name2!='gks_users_groups' and
    $name2!='gks_warehouses' and
    $name2!='gks_eshops' and
    $name2!='gks_eshop_products_brands' and
    $name2!='gks_orders_occasion' and
    $name2!='gks_custom_table' and
    $name2!='gks_lang' and
    $name2!='gks_sociallinks_type' and
    $name2!='gks_eshop_pricelist' and
    $name2!='gks_eshop_pricelist_items' and
    startwith($name2,'gks_ct_')==false
    
    ) $name2='';

if ($id1<=0 or $id2<=0 or $name1=='' or $name2=='') {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (1)<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}
  
$my_page_title=gks_lang('Σύνδεση αντικειμένων').': '.$name1.'|'.$id1.'|'.$name2.'|'.$id2;
db_open();
stat_record();

$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('logistis',$my_wp_user_info->roles))  $userrole='logistis';
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();}

if ($name1==$name2 and $id1==$id2) {
  debug_mail(false,'self obj rel','self link');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί ένα αντικείμενο να συνδεθεί με τον εαυτό του')));
  echo json_encode($return); die();}

$sql="select * from gks_object_rel
where (object_name1='".$db_link->escape_string($name1)."' and object_id1=".$id1." and 
       object_name2='".$db_link->escape_string($name2)."' and object_id2=".$id2.") or 
      (object_name1='".$db_link->escape_string($name2)."' and object_id1=".$id2." and 
       object_name2='".$db_link->escape_string($name1)."' and object_id2=".$id1.")";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  debug_mail(false,'link objects exist');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτή η σύνδεση υπάρχει ήδη').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}

$sql="SELECT 1 as cc FROM gks_settings where mykey='keno keno'";

if      ($name1=='gks_assets')  $sql="select * from gks_assets  where id_asset =".$id1;
else if ($name1=='gks_assets_type')  $sql="select * from gks_assets_type where id_asset_type =".$id1;
else if ($name1=='gks_assets_service')  $sql="select * from gks_assets_service where id_assets_service =".$id1;
else if ($name1=='gks_assets_service_reasons')  $sql="select * from gks_assets_service_reasons where id_assets_service_reasons =".$id1;
else if ($name1=='gks_assets_whi_mov')  $sql="select * from gks_assets_whi_mov where id_assets_whi_mov =".$id1;
else if ($name1=='gks_orders')  $sql="select * from gks_orders  where id_order =".$id1;
else if ($name1=='gks_acc_inv') $sql="select * from gks_acc_inv where id_acc_inv=".$id1;
else if ($name1=='gks_acc_pay') $sql="select * from gks_acc_pay where id_acc_pay=".$id1;
else if ($name1=='gks_bank_accounts') $sql="select * from gks_bank_accounts where id_bank_account=".$id1;
else if ($name1=='wp_users') $sql="select * from ".GKS_WP_TABLE_PREFIX."users where ID=".$id1;
else if ($name1=='gks_crm_leads') $sql="select * from gks_crm_leads where id_crm_lead=".$id1;
else if ($name1=='gks_crm_tasks') $sql="select * from gks_crm_tasks where id_crm_task=".$id1;
else if ($name1=='gks_crm_machine') $sql="select * from gks_crm_machine where id_crm_machine=".$id1;
else if ($name1=='gks_calendar') $sql="select * from gks_calendar where id_calendar=".$id1;
else if ($name1=='gks_crm_channel_sale') $sql="select * from gks_crm_channel_sale where id_crm_channel_sale=".$id1;
else if ($name1=='gks_crm_leads_status') $sql="select * from gks_crm_leads_status where id_crm_lead_status=".$id1;
else if ($name1=='gks_crm_tasks_status') $sql="select * from gks_crm_tasks_status where id_crm_task_status=".$id1;
else if ($name1=='gks_acc_seires') $sql="select * from gks_acc_seires where id_acc_seira=".$id1;
else if ($name1=='gks_acc_journal') $sql="SELECT * from gks_acc_journal WHERE id_acc_journal=".$id1;
else if ($name1=='gks_company') $sql="SELECT * from gks_company WHERE id_company=".$id1;
else if ($name1=='gks_company_subs') $sql="SELECT * from gks_company_subs WHERE id_company_sub=".$id1;
else if ($name1=='gks_eshop_product_lots') $sql="SELECT * from gks_eshop_product_lots WHERE id_lot_product=".$id1;
else if ($name1=='gks_eshop_products') $sql="SELECT * from gks_eshop_products WHERE id_product=".$id1;
else if ($name1=='gks_eshop_products_categories') $sql="SELECT * from gks_eshop_products_categories WHERE id_product_category=".$id1;
else if ($name1=='gks_hotel') $sql="SELECT * from gks_hotel WHERE id_hotel=".$id1;
else if ($name1=='gks_hotel_reservation') $sql="select * from gks_hotel_reservation where id_hotel_reservation=".$id1;
else if ($name1=='gks_hotel_availability') $sql="SELECT * from gks_hotel_availability WHERE id_hotel_availability=".$id1;
else if ($name1=='gks_hotel_floor') $sql="SELECT * from gks_hotel_floor WHERE id_hotel_floor=".$id1;
else if ($name1=='gks_hotel_price') $sql="SELECT * from gks_hotel_price WHERE id_hotel_price=".$id1;
else if ($name1=='gks_hotel_room') $sql="SELECT * from gks_hotel_room WHERE id_hotel_room=".$id1;
else if ($name1=='gks_hotel_room_type') $sql="SELECT * from gks_hotel_room_type WHERE id_hotel_room_type=".$id1;

else if ($name1=='gks_template_html') $sql="SELECT * from gks_template_html WHERE id_template_html=".$id1;

else if ($name1=='gks_transfer') $sql="SELECT * from gks_transfer WHERE id_transfer=".$id1;
else if ($name1=='gks_transfer_reservation') $sql="select * from gks_transfer_reservation where id_transfer_reservation=".$id1;
else if ($name1=='gks_transfer_area') $sql="SELECT * from gks_transfer_area WHERE id_transfer_area=".$id1;
else if ($name1=='gks_transfer_oxima_type') $sql="SELECT * from gks_transfer_oxima_type WHERE id_transfer_oxima_type=".$id1;
else if ($name1=='gks_transfer_pricelist') $sql="SELECT * from gks_transfer_pricelist WHERE id_transfer_pricelist=".$id1;

else if ($name1=='gks_poi') $sql="SELECT * from gks_poi WHERE id_poi=".$id1;
else if ($name1=='gks_poi_type') $sql="SELECT * from gks_poi_type WHERE id_poi_type=".$id1;
else if ($name1=='gks_poi_diadromes') $sql="SELECT * from gks_poi_diadromes WHERE id_poi_diadromes=".$id1;


else if ($name1=='gks_print_forms') $sql="SELECT * from gks_print_forms WHERE id_print_form=".$id1;
else if ($name1=='gks_production_ergasies') $sql="SELECT * from gks_production_ergasies WHERE id_production_ergasia=".$id1;
else if ($name1=='gks_production_posta') $sql="SELECT * from gks_production_posta WHERE id_production_posto=".$id1;
else if ($name1=='gks_production_bom') $sql="SELECT * from gks_production_bom WHERE id_production_bom=".$id1;
else if ($name1=='gks_users_groups') $sql="SELECT * from gks_users_groups WHERE id_users_group=".$id1;
else if ($name1=='gks_warehouses') $sql="SELECT * from gks_warehouses WHERE id_warehouse=".$id1;
else if ($name1=='gks_eshops') $sql="SELECT * from gks_eshops WHERE id_eshop=".$id1;
else if ($name1=='gks_eshop_products_brands') $sql="SELECT * from gks_eshop_products_brands WHERE id_product_brand=".$id1;
else if ($name1=='gks_orders_occasion') $sql="SELECT * FROM gks_orders_occasion WHERE id_order_occasion=".$id1;
else if ($name1=='gks_custom_table') $sql="SELECT * FROM gks_custom_table WHERE id_custom_table=".$id1;
else if ($name1=='gks_lang') $sql="SELECT * FROM gks_lang WHERE idd_lang=".$id1;
else if ($name1=='gks_sociallinks_type') $sql="SELECT * FROM gks_sociallinks_type WHERE id_sociallinks_type=".$id1;
else if ($name1=='gks_eshop_pricelist') $sql="SELECT * FROM gks_eshop_pricelist WHERE id_pricelist=".$id1;
else if ($name1=='gks_eshop_pricelist_items') $sql="SELECT * FROM gks_eshop_pricelist_items WHERE id_pricelist_item=".$id1;
else if (startwith($name1,'gks_ct_')) {
  $ctid=trim_gks(str_replace('gks_ct_','',$name1)); //echo '<pre>|'.$ctid.'|';die();
  $ctid=intval($ctid);
  if ($ctid<=10000) {
    $message=gks_lang('Δεν έχει ορισθεί το').' ctid. ('.$ctid.')';
    debug_mail(false,$message,'');
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  $sql="SELECT * FROM gks_customt_gks_ct_".$ctid." WHERE id_gks_customt_gks_ct_".$ctid."=".$id1;
}

//echo '<pre>'.$sql;die();

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows==0) {
  debug_mail(false,'object not found');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το τρέχον αντικείμενο').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}
  
$objv=array();
$objv['objname']='';
$objv['link']='';
$objv['oname']='';
$objv['state']='';
$objv['price']='';
$objv['date']='';
$objv['balance']='';

if ($name2=='gks_assets') {
  $sql_rel="SELECT *       
  FROM gks_assets
  WHERE gks_assets.id_asset=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Πάγιο'),
    'link' => '<a href="admin-assets-item.php?id='.$row_rel['id_asset'].'">#'.$row_rel['id_asset'].'</a>',
    'oname' => trim_gks($row_rel['asset_title']),
    'state' => '<img src="img/'.($row_rel['asset_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}
else if ($name2=='gks_assets_type') {
  $sql_rel="SELECT *
  FROM gks_assets_type
  WHERE id_asset_type=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Τύπος Παγίου'),
    'link' => '<a href="admin-assets-type-item.php?id='.$row_rel['id_asset_type'].'">#'.$row_rel['id_asset_type'].'</a>',
    'oname' => trim_gks($row_rel['asset_type_descr']),
    'state' => '<img src="img/'.($row_rel['asset_type_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}
else if ($name2=='gks_assets_service') {
  $sql_rel="SELECT gks_assets_service.*, asset_title 
  FROM gks_assets_service
  LEFT JOIN gks_assets ON gks_assets_service.asset_id = gks_assets.id_asset
  WHERE id_assets_service=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Service Παγίου'),
    'link' => '<a href="admin-assets-service-item.php?id='.$row_rel['id_assets_service'].'">#'.$row_rel['id_assets_service'].'</a>',
    'oname' => trim_gks($row_rel['asset_title']),
    'state' => '<img src="img/'.($row_rel['isconfirm']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => ($row_rel['ajia']==0 ? '' : myCurrencyFormat($row_rel['ajia'])),
    'date' => showDate(strtotime($row_rel['mydate_send']), 'd/m/Y\<\b\r\>H:i:s', 1),
    'balance' => '',
  );
}
else if ($name2=='gks_assets_service_reasons') {
  $sql_rel="SELECT *
  FROM gks_assets_service_reasons
  WHERE id_assets_service_reasons=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Αιτία Service Παγίου'),
    'link' => '<a href="admin-assets-service-reasons-item.php?id='.$row_rel['id_assets_service_reasons'].'">#'.$row_rel['id_assets_service_reasons'].'</a>',
    'oname' => trim_gks($row_rel['reasons_descr']),
    'state' => '<img src="img/'.($row_rel['assets_service_reason_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}
else if ($name2=='gks_assets_whi_mov') {
  $sql_rel="SELECT *
  FROM gks_assets_whi_mov
  WHERE id_assets_whi_mov=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Απογραφή Παγίων'),
    'link' => '<a href="admin-assets-whi-mov-item.php?id='.$row_rel['id_assets_whi_mov'].'">#'.$row_rel['id_assets_whi_mov'].'</a>',
    'oname' => '',
    'state' => get_assets_whi_mov_descr($row_rel['assets_whi_mov_status']),
    'price' => '',
    'date' => showDate(strtotime($row_rel['mydate']), 'd/m/Y\<\b\r\>H:i:s', 1),
    'balance' => '',
  );
}
else if ($name2=='gks_orders') {
  $sql_rel="SELECT gks_orders.id_order, gks_orders.order_state, gks_orders.gks_price_net,
  gks_orders.order_date,
  CASE
    WHEN (order_state='060registered' or order_state='070inproduction' or 
         order_state='090indelivery' or order_state='095execute' or order_state='100completed' or order_state='110payment') and affect_balance=1
      THEN affect_balance_poso
    ELSE 0
  END as affect_balance_calc         
  FROM gks_orders
  WHERE gks_orders.id_order=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Παραγγελία'),
    'link' => '<a href="admin-orders-item.php?id='.$row_rel['id_order'].'">#'.$row_rel['id_order'].'</a>',
    'oname' => '',
    'state' => '<span class="order_state_'.$row_rel['order_state'].'">'.getOrderStateDescr($row_rel['order_state']).'</span>',
    'price' => ($row_rel['gks_price_net']==0 ? '' : myCurrencyFormat($row_rel['gks_price_net'])),
    'date' => showDate(strtotime($row_rel['order_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
    'balance' => ($row_rel['affect_balance_calc']==0 ? '' : myCurrencyFormat($row_rel['affect_balance_calc'])),
  );
}

else if ($name2=='gks_acc_inv') {
  $sql_rel="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.inv_state, gks_acc_inv.gks_price_net,
  gks_acc_inv.inv_date,
  gks_acc_journal.acc_journal_code, gks_acc_journal.acc_journal_descr, 
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr, gks_acc_inv.inv_acc_number_int,
  CASE
    WHEN (inv_state='080listing' or inv_state='090ekdosi' or inv_state='100payment') and affect_balance=1
      THEN affect_balance_pros * affect_balance_poso
    ELSE 0
  END as affect_balance_calc          
  FROM (gks_acc_inv 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
  WHERE gks_acc_inv.id_acc_inv=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Παραστατικό'),
    'link' => '<a href="admin-acc-inv-item.php?id='.$row_rel['id_acc_inv'].'">#'.$row_rel['id_acc_inv'].'</a>',
    'oname' => $row_rel['acc_journal_code'].'/'.$row_rel['seira_code'].'/'.($row_rel['inv_acc_number_int']!=0 ? $row_rel['inv_acc_number_int'] : ''),
    'state' => '<span class="acc_inv_state_'.$row_rel['inv_state'].'">'.getAccInvStateDescr($row_rel['inv_state']).'</span>',
    'price' => ($row_rel['gks_price_net']==0 ? '' : myCurrencyFormat($row_rel['gks_price_net'])),
    'date' => showDate(strtotime($row_rel['inv_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
    'balance' => ($row_rel['affect_balance_calc']==0 ? '' : myCurrencyFormat($row_rel['affect_balance_calc'])),
  );
}
else if ($name2=='gks_acc_pay') {
  $sql_rel="SELECT gks_acc_pay.id_acc_pay, gks_acc_pay.pay_state, gks_acc_pay.gks_price_total,
  gks_acc_pay.pay_date,
  gks_acc_journal.acc_journal_code, gks_acc_journal.acc_journal_descr, 
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr, gks_acc_pay.pay_acc_number_int,
  CASE
    WHEN (pay_state='080listing' or pay_state='090ekdosi' or pay_state='100payment') and affect_balance=1
      THEN affect_balance_pros * affect_balance_poso
    ELSE 0
  END as affect_balance_calc          
  FROM (gks_acc_pay 
  LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira
  WHERE gks_acc_pay.id_acc_pay=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Πληρωμή'),
    'link' => '<a href="admin-acc-pay-item.php?id='.$row_rel['id_acc_pay'].'">#'.$row_rel['id_acc_pay'].'</a>',
    'oname' => $row_rel['acc_journal_code'].'/'.$row_rel['seira_code'].'/'.($row_rel['pay_acc_number_int']!=0 ? $row_rel['pay_acc_number_int'] : ''),
    'state' => '<span class="acc_pay_state_'.$row_rel['pay_state'].'">'.getAccPayStateDescr($row_rel['pay_state']).'</span>',
    'price' => ($row_rel['gks_price_total']==0 ? '' : myCurrencyFormat($row_rel['gks_price_total'])),
    'date' => showDate(strtotime($row_rel['pay_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
    'balance' => ($row_rel['affect_balance_calc']==0 ? '' : myCurrencyFormat($row_rel['affect_balance_calc'])),
  );
}

else if ($name2=='gks_bank_accounts') {
  $sql_rel="SELECT *         
  FROM gks_bank_accounts 
  WHERE id_bank_account=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Τραπεζικός λογαριασμός'),
    'link' => '<a href="admin-bank_accounts-item.php?id='.$row_rel['id_bank_account'].'">#'.$row_rel['id_bank_account'].'</a>',
    'oname' => trim_gks($row_rel['account_descr']),
    'state' => '<img src="img/'.($row_rel['bank_account_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='wp_users') {
  $sql_rel="SELECT ID,gks_nickname,gks_balance
  FROM ".GKS_WP_TABLE_PREFIX."users
  WHERE ".GKS_WP_TABLE_PREFIX."users.ID=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Επαφή'),
    'link' => '<a href="admin-users-item.php?id='.$row_rel['ID'].'">#'.$row_rel['ID'].'</a>',
    'oname' => $row_rel['gks_nickname'],
    'state' => '',
    'price' => '',
    'date' => '',
    'balance' => ($row_rel['gks_balance']==0 ? '' : myCurrencyFormat($row_rel['gks_balance'])),
  );
}
else if ($name2=='gks_crm_leads') {
  gks_get_leads_status($leads_status,$leads_status_styles);

  
  $sql_rel="SELECT gks_crm_leads.*
  FROM gks_crm_leads
  WHERE gks_crm_leads.id_crm_lead=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Ευκαιρία'),
    'link' => '<a href="admin-crm-lead-item.php?id='.$row_rel['id_crm_lead'].'">#'.$row_rel['id_crm_lead'].'</a>',
    'oname' => trim_gks($row_rel['subject']),
    'oname_bg'=> trim_gks($row_rel['lead_color']),
    'state' => '<span class="lead_status_'.$row_rel['lead_status_id'].'">'.
               (isset($leads_status[$row_rel['lead_status_id']]) ? $leads_status[$row_rel['lead_status_id']]['lead_status_descr'] : '').'</span>',
    'price' => ($row_rel['esoda']==0 ? '' : myCurrencyFormat($row_rel['esoda'])),
    'date' => showDate(strtotime($row_rel['lead_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
    'balance' => '',
  );
}
else if ($name2=='gks_crm_tasks') {
  gks_get_tasks_status($tasks_status,$tasks_status_styles);

  
  $sql_rel="SELECT gks_crm_tasks.*
  FROM gks_crm_tasks
  WHERE gks_crm_tasks.id_crm_task=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Ευκαιρία'),
    'link' => '<a href="admin-crm-task-item.php?id='.$row_rel['id_crm_task'].'">#'.$row_rel['id_crm_task'].'</a>',
    'oname' => trim_gks($row_rel['subject']),
    'oname_bg'=> trim_gks($row_rel['task_color']),
    'state' => '<span class="task_status_'.$row_rel['task_status_id'].'">'.
               (isset($tasks_status[$row_rel['task_status_id']]) ? $tasks_status[$row_rel['task_status_id']]['task_status_descr'] : '').'</span>',
    'price' => ($row_rel['esoda']==0 ? '' : myCurrencyFormat($row_rel['esoda'])),
    'date' => showDate(strtotime($row_rel['task_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
    'balance' => '',
  );
}

else if ($name2=='gks_crm_machine') {
  
  $sql_rel="SELECT gks_crm_machine.*
  FROM gks_crm_machine
  WHERE gks_crm_machine.id_crm_machine=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Συσκευή'),
    'link' => '<a href="admin-crm-machine-item.php?id='.$row_rel['id_crm_machine'].'">#'.$row_rel['id_crm_machine'].'</a>',
    'oname' => trim_gks($row_rel['crm_machine_name']),
    'state' => '',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}




else if ($name2=='gks_calendar') {
  $sql_rel="SELECT gks_calendar.*
  FROM gks_calendar
  WHERE gks_calendar.id_calendar=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Ημερολόγιο'), 
    'link' => '<a href="admin-crm-calendar.php?id='.$row_rel['id_calendar'].'">#'.$row_rel['id_calendar'].'</a>',
    'oname' => trim_gks($row_rel['calendar_title']),
    'oname_bg'=> trim_gks($row_rel['calendar_color']),
    'state' => '',
    'price' => '',
    'date' => showDate(strtotime($row_rel['calendar_start']), 'd/m/Y\<\b\r\>H:i:s', 1),
    'balance' => '',
  );
}

else if ($name2=='gks_crm_channel_sale') {
  $sql_rel="SELECT id_crm_channel_sale,crm_channel_sale_descr,crm_channel_sale_disabled FROM gks_crm_channel_sale WHERE id_crm_channel_sale=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Κανάλι πωλήσεων'),
    'link' => '<a href="admin-crm-channel-sale-item.php?id='.$row_rel['id_crm_channel_sale'].'">#'.$row_rel['id_crm_channel_sale'].'</a>',
    'oname' => $row_rel['crm_channel_sale_descr'],
    'state' => '<img src="img/'.($row_rel['crm_channel_sale_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_crm_leads_status') {
  $sql_rel="SELECT id_crm_lead_status,lead_status_descr,lead_status_color,lead_status_disabled FROM gks_crm_leads_status WHERE id_crm_lead_status=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Κατάσταση Ευκαιριών'),
    'link' => '<a href="admin-crm-leads-status-item.php?id='.$row_rel['id_crm_lead_status'].'">#'.$row_rel['id_crm_lead_status'].'</a>',
    'oname' => $row_rel['lead_status_descr'],
    'oname_bg'=> trim_gks($row_rel['lead_status_color']),
    'state' => '<img src="img/'.($row_rel['lead_status_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_crm_tasks_status') {
  $sql_rel="SELECT id_crm_task_status,task_status_descr,task_status_color,task_status_disabled FROM gks_crm_tasks_status WHERE id_crm_task_status=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Κατάσταση Εργασιών'),
    'link' => '<a href="admin-crm-tasks-status-item.php?id='.$row_rel['id_crm_task_status'].'">#'.$row_rel['id_crm_task_status'].'</a>',
    'oname' => $row_rel['task_status_descr'],
    'oname_bg'=> trim_gks($row_rel['task_status_color']),
    'state' => '<img src="img/'.($row_rel['task_status_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_acc_journal') {
  $sql_rel="SELECT id_acc_journal,acc_journal_descr,is_disable from gks_acc_journal WHERE id_acc_journal=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Ημερολόγιο'),
    'link' => '<a href="admin-acc_journal-item.php?id='.$row_rel['id_acc_journal'].'">#'.$row_rel['id_acc_journal'].'</a>',
    'oname' => $row_rel['acc_journal_descr'],
    'state' => '<img src="img/'.($row_rel['is_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_acc_seires') {
  $sql_rel="SELECT id_acc_seira,seira_descr,is_disable FROM gks_acc_seires WHERE id_acc_seira=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Σειρά'),
    'link' => '<a href="admin-acc_seires-item.php?id='.$row_rel['id_acc_seira'].'">#'.$row_rel['id_acc_seira'].'</a>',
    'oname' => $row_rel['seira_descr'],
    'state' => '<img src="img/'.($row_rel['is_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_company') {
  $sql_rel="SELECT id_company,company_title,company_color,company_disable FROM gks_company WHERE id_company=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Εταιρεία'),
    'link' => '<a href="admin-company-item.php?id='.$row_rel['id_company'].'">#'.$row_rel['id_company'].'</a>',
    'oname' => $row_rel['company_title'],
    'oname_bg'=> trim_gks($row_rel['company_color']),
    'state' => '<img src="img/'.($row_rel['company_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}



else if ($name2=='gks_company_subs') {
  $sql_rel="SELECT id_company_sub,company_sub_title,company_sub_color,company_sub_disable from gks_company_subs WHERE id_company_sub=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Υποκατάστημα'),
    'link' => '<a href="admin-company-sub-item.php?id='.$row_rel['id_company_sub'].'">#'.$row_rel['id_company_sub'].'</a>',
    'oname' => $row_rel['company_sub_title'],
    'oname_bg'=> trim_gks($row_rel['company_sub_color']),
    'state' => '<img src="img/'.($row_rel['company_sub_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_eshop_product_lots') {
  $sql_rel="SELECT gks_eshop_product_lots.*, gks_eshop_products.product_descr 
    from gks_eshop_product_lots 
    LEFT JOIN gks_eshop_products ON gks_eshop_product_lots.lotproduct_id = gks_eshop_products.id_product
    WHERE id_lot_product=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Παρτίδα-Serial Number'),
    'link' => '<a href="admin-products-lots-item.php?id='.$row_rel['id_lot_product'].'">#'.$row_rel['id_lot_product'].'</a>',
    'oname' => trim_gks($row_rel['lot_name']).'/'.trim_gks($row_rel['product_descr']),
    'state' => '<img src="img/'.($row_rel['lot_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => (empty($row_rel['lot_date_expire']) ? '': showDate(strtotime($row_rel['lot_date_expire']), 'd/m/Y', 0)),
    'balance' => '',
  );
}

else if ($name2=='gks_eshop_products') {
  $sql_rel="SELECT gks_eshop_products.id_product,gks_eshop_products.product_disable,
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
    END as product_descr_p
    from gks_eshop_products 
    LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
    WHERE gks_eshop_products.id_product=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Είδος'),
    'link' => '<a href="admin-products-item.php?id='.$row_rel['id_product'].'">#'.$row_rel['id_product'].'</a>',
    'oname' => $row_rel['product_descr_p'],
    'state' => '<img src="img/'.($row_rel['product_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_eshop_products_categories') {
  $sql_rel="SELECT gks_eshop_products_categories.id_product_category,gks_eshop_products_categories.category_disable,
  CONCAT_WS('\\\\',
                  ug10.product_category_descr,
                  ug9.product_category_descr,
                  ug8.product_category_descr,
                  ug7.product_category_descr,
                  ug6.product_category_descr,
                  ug5.product_category_descr,
                  ug4.product_category_descr,
                  ug3.product_category_descr,
                  ug2.product_category_descr,
                  gks_eshop_products_categories.product_category_descr) as fullpath
  
  FROM ((((((((gks_eshop_products_categories
  LEFT JOIN gks_eshop_products_categories AS ug2  ON gks_eshop_products_categories.product_category_parent_id = ug2.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.product_category_parent_id = ug3.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.product_category_parent_id = ug4.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.product_category_parent_id = ug5.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.product_category_parent_id = ug6.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.product_category_parent_id = ug7.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.product_category_parent_id = ug8.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.product_category_parent_id = ug9.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
  WHERE gks_eshop_products_categories.id_product_category=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Κατηγορία Είδους'),
    'link' => '<a href="admin-product-categorys-item.php?id='.$row_rel['id_product_category'].'">#'.$row_rel['id_product_category'].'</a>',
    'oname' => $row_rel['fullpath'],
    'state' => '<img src="img/'.($row_rel['category_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_hotel') {
  $sql_rel="SELECT id_hotel,hotel_title,hotel_color,hotel_disable from gks_hotel WHERE id_hotel=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Ξενοδοχείο'),
    'link' => '<a href="admin-hotel-item.php?id='.$row_rel['id_hotel'].'">#'.$row_rel['id_hotel'].'</a>',
    'oname' => $row_rel['hotel_title'],
    'oname_bg'=> trim_gks($row_rel['hotel_color']),
    'state' => '<img src="img/'.($row_rel['hotel_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_hotel_reservation') {
  $sql_rel="SELECT gks_hotel_reservation.*,".GKS_WP_TABLE_PREFIX."users.gks_nickname
  FROM gks_hotel_reservation
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_hotel_reservation.id_hotel_reservation=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $oname='';
  if ($row_rel['user_id']>0) {
    if (!empty($row_rel['gks_nickname'])) $oname=$row_rel['gks_nickname'];
  } else {
    if (!empty($row_rel['user_last_name']) or !empty($row_rel['user_first_name'])) $oname=$row_rel['user_last_name'].' '.$row_rel['user_first_name'];
  }
  $objv=array(
    'objname' => gks_lang('Κράτηση'),
    'link' => '<a href="admin-hotel-reservation-item.php?id='.$row_rel['id_hotel_reservation'].'">#'.$row_rel['id_hotel_reservation'].'</a>',
    'oname' => trim_gks($oname),
    'state' => '<span class="reservation_status_'.$row_rel['reservation_status'].'">'.getHotelReservationStatusDescr($row_rel['reservation_status']).'</span>',
    'price' => ($row_rel['gks_price_total']==0 ? '' : myCurrencyFormat($row_rel['gks_price_total'])),
    'date' => showDate(strtotime($row_rel['check_in']), 'd/m/Y\<\b\r\>H:i:s', 0),
    'balance' => '',


  );
}

else if ($name2=='gks_hotel_availability') {
  $sql_rel="SELECT id_hotel_availability,availability_descr,availability_status from gks_hotel_availability WHERE id_hotel_availability=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Διαθεσιμότητα'),
    'link' => '<a href="admin-hotel-availability-item.php?id='.$row_rel['id_hotel_availability'].'">#'.$row_rel['id_hotel_availability'].'</a>',
    'oname' => $row_rel['availability_descr'],
    'state' => '<span class="hotel_availability_'.$row_rel['availability_status'].'">'.getHotelAvailabilityDescr($row_rel['availability_status']).'</span>',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
} 


else if ($name2=='gks_hotel_floor') {
  $sql_rel="SELECT id_hotel_floor,floor_descr from gks_hotel_floor WHERE id_hotel_floor=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Όροφος'),
    'link' => '<a href="admin-hotel-floor-item.php?id='.$row_rel['id_hotel_floor'].'">#'.$row_rel['id_hotel_floor'].'</a>',
    'oname' => $row_rel['floor_descr'],
    'state' => '',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}



else if ($name2=='gks_hotel_price') {
  $sql_rel="SELECT id_hotel_price,price_descr,price from gks_hotel_price WHERE id_hotel_price=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Τιμή δωματίου'),
    'link' => '<a href="admin-hotel-price-item.php?id='.$row_rel['id_hotel_price'].'">#'.$row_rel['id_hotel_price'].'</a>',
    'oname' => $row_rel['price_descr'],
    'state' => '',
    'price' => myCurrencyFormat($row_rel['price']),
    'date' => '',
    'balance' => '',
  );
}


else if ($name2=='gks_hotel_room') {
  $sql_rel="SELECT id_hotel_room,room_descr,room_status from gks_hotel_room WHERE id_hotel_room=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Δωμάτιο'),
    'link' => '<a href="admin-hotel-room-item.php?id='.$row_rel['id_hotel_room'].'">#'.$row_rel['id_hotel_room'].'</a>',
    'oname' => $row_rel['room_descr'],
    'state' => '<span class="room_status_'.$row_rel['room_status'].'">'.getHotelRoomTypeStatusDescr($row_rel['room_status']).'</span>',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_hotel_room_type') {
  $sql_rel="SELECT id_hotel_room_type,room_type_descr,room_type_status,room_type_price from gks_hotel_room_type WHERE id_hotel_room_type=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Τύπος δωματίου'),
    'link' => '<a href="admin-hotel-room-type-item.php?id='.$row_rel['id_hotel_room_type'].'">#'.$row_rel['id_hotel_room_type'].'</a>',
    'oname' => $row_rel['room_type_descr'],
    'state' => '<span class="room_type_status_'.$row_rel['room_type_status'].'">'.getHotelRoomTypeStatusDescr($row_rel['room_type_status']).'</span>',
    'price' => myCurrencyFormat($row_rel['room_type_price']),
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_template_html') {
  $sql_rel="SELECT id_template_html,template_html_descr,is_disable from gks_template_html WHERE id_template_html=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Πρότυπο HTML'),
    'link' => '<a href="admin-template_html-item.php?id=?id='.$row_rel['id_template_html'].'">#'.$row_rel['id_template_html'].'</a>',
    'oname' => $row_rel['template_html_descr'],
    'oname_bg' => '',
    'state' => '<img src="img/'.($row_rel['is_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_transfer') {
  $sql_rel="SELECT id_transfer,transfer_title,transfer_color,transfer_disable from gks_transfer WHERE id_transfer=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Κανάλι Transfer'),
    'link' => '<a href="admin-transfer-item.php?id='.$row_rel['id_transfer'].'">#'.$row_rel['id_transfer'].'</a>',
    'oname' => $row_rel['transfer_title'],
    'oname_bg' => trim_gks($row_rel['transfer_color']),
    'state' => '<img src="img/'.($row_rel['transfer_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_transfer_reservation') {
  $sql_rel="SELECT gks_transfer_reservation.*,".GKS_WP_TABLE_PREFIX."users.gks_nickname
  FROM gks_transfer_reservation
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_transfer_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_transfer_reservation.id_transfer_reservation=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $oname='';
  if ($row_rel['user_id']>0) {
    if (!empty($row_rel['gks_nickname'])) $oname=$row_rel['gks_nickname'];
  } else {
    if (!empty($row_rel['user_last_name']) or !empty($row_rel['user_first_name'])) $oname=$row_rel['user_last_name'].' '.$row_rel['user_first_name'];
  }
  $objv=array(
    'objname' => gks_lang('Κράτηση'),
    'link' => '<a href="admin-transfer-reservation-item.php?id='.$row_rel['id_transfer_reservation'].'">#'.$row_rel['id_transfer_reservation'].'</a>',
    'oname' => trim_gks($oname),
    'state' => '<span class="transfer_reservation_status_'.$row_rel['transfer_reservation_status'].'">'.getTransferReservationStatusDescr($row_rel['transfer_reservation_status']).'</span>',
    'price' => ($row_rel['gks_price_total']==0 ? '' : myCurrencyFormat($row_rel['gks_price_total'])),
    'date' => showDate(strtotime($row_rel['transfer_start']), 'd/m/Y\<\b\r\>H:i:s', 0),
    'balance' => '',


  );
  //print '<pre>';print_r($objv);die();
  
}

else if ($name2=='gks_transfer_area') {
  $sql_rel="SELECT id_transfer_area,transfer_area_descr,transfer_area_disable from gks_transfer_area WHERE id_transfer_area=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Περιοχές'),
    'link' => '<a href="admin-transfer-area-item.php?id='.$row_rel['id_transfer_area'].'">#'.$row_rel['id_transfer_area'].'</a>',
    'oname' => $row_rel['transfer_area_descr'],
    'state' => '<img src="img/'.($row_rel['transfer_area_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_transfer_oxima_type') {
  $sql_rel="SELECT id_transfer_oxima_type,transfer_oxima_type_descr,transfer_oxima_type_disable from gks_transfer_oxima_type WHERE id_transfer_oxima_type=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Τύπος Οχήματος'),
    'link' => '<a href="admin-transfer-oxima-type-item.php?id='.$row_rel['id_transfer_oxima_type'].'">#'.$row_rel['id_transfer_oxima_type'].'</a>',
    'oname' => $row_rel['transfer_oxima_type_descr'],
    'state' => '<img src="img/'.($row_rel['transfer_oxima_type_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_transfer_pricelist') {
  $sql_rel="SELECT id_transfer_pricelist,transfer_pricelist_disable,
  gks_poi_from.poi_descr AS poi_descr_from, gks_poi_to.poi_descr AS poi_descr_to,
  gks_transfer_oxima_type.transfer_oxima_type_photo,gks_transfer_oxima_type.transfer_oxima_type_descr  
  from ((gks_transfer_pricelist 
  LEFT JOIN gks_poi AS gks_poi_from ON gks_transfer_pricelist.poi_id_from = gks_poi_from.id_poi) 
  LEFT JOIN gks_poi AS gks_poi_to ON gks_transfer_pricelist.poi_id_to = gks_poi_to.id_poi)
  LEFT JOIN gks_transfer_oxima_type ON gks_transfer_pricelist.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type
  WHERE id_transfer_pricelist=".$id2;
  //echo $sql;die();
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Καταχώρηση Τιμοκαταλόγου'),
    'link' => '<a href="admin-transfer-pricelist-item.php?id='.$row_rel['id_transfer_pricelist'].'">#'.$row_rel['id_transfer_pricelist'].'</a>',
    'oname' => gks_lang('Προς').' '.$row_rel['poi_descr_to'],
    'state' => '<img src="img/'.($row_rel['transfer_pricelist_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_poi') {
  $sql_rel="SELECT id_poi,poi_descr,poi_color,poi_disable from gks_poi WHERE id_poi=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Σημείο Ενδιαφέροντος'),
    'link' => '<a href="admin-poi-item.php?id='.$row_rel['id_poi'].'">#'.$row_rel['id_poi'].'</a>',
    'oname' => $row_rel['poi_descr'],
    'oname_bg' => trim_gks($row_rel['poi_color']),
    'state' => '<img src="img/'.($row_rel['poi_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_poi_type') {
  $sql_rel="SELECT id_poi_type,poi_type_descr,poi_type_disable from gks_poi_type WHERE id_poi_type=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Τύπος Σημείων Ενδιαφέροντος'),
    'link' => '<a href="admin-poi-type-item.php?id='.$row_rel['id_poi_type'].'">#'.$row_rel['id_poi_type'].'</a>',
    'oname' => $row_rel['poi_type_descr'],
    'state' => '<img src="img/'.($row_rel['poi_type_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_poi_diadromes') {
  $sql_rel="SELECT id_poi_diadromes,
  gks_poi_from.poi_descr AS poi_descr_from, gks_poi_to.poi_descr AS poi_descr_to,
  poi_diadromes_disable 
  from (gks_poi_diadromes 
  LEFT JOIN gks_poi AS gks_poi_from ON gks_poi_diadromes.poi_id_from = gks_poi_from.id_poi) 
  LEFT JOIN gks_poi AS gks_poi_to ON gks_poi_diadromes.poi_id_to = gks_poi_to.id_poi
  WHERE id_poi_diadromes=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Διαδρομή'),
    'link' => '<a href="admin-poi-diadromes-item.php?id='.$row_rel['id_poi_diadromes'].'">#'.$row_rel['id_poi_diadromes'].'</a>',
    'oname' => $row_rel['poi_descr_from'].' <i class="fas fa-chevron-circle-right"></i> '.$row_rel['poi_descr_to'],
    'state' => '<img src="img/'.($row_rel['poi_diadromes_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_print_forms') {
  $sql_rel="SELECT id_print_form,print_form_descr,is_disable from gks_print_forms WHERE id_print_form=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Φόρμα Εκτύπωσης'),
    'link' => '<a href="admin-print_forms-item.php?id='.$row_rel['id_print_form'].'">#'.$row_rel['id_print_form'].'</a>',
    'oname' => $row_rel['print_form_descr'],
    'state' => '<img src="img/'.($row_rel['is_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}



else if ($name2=='gks_production_ergasies') {
  $sql_rel="SELECT id_production_ergasia,production_ergasia_descr from gks_production_ergasies WHERE id_production_ergasia=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Εργασία παραγωγής'),
    'link' => '<a href="admin-production-ergasies-item.php?id='.$row_rel['id_production_ergasia'].'">#'.$row_rel['id_production_ergasia'].'</a>',
    'oname' => $row_rel['production_ergasia_descr'],
    'state' => '',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}



else if ($name2=='gks_production_posta') {
  $sql_rel="SELECT id_production_posto,production_posto_descr from gks_production_posta WHERE id_production_posto=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Πόστο'),
    'link' => '<a href="admin-production-posta-item.php?id='.$row_rel['id_production_posto'].'">#'.$row_rel['id_production_posto'].'</a>',
    'oname' => $row_rel['production_posto_descr'],
    'state' => '',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_production_bom') {
  //echo '<pre>';echo time();die();
  $sql_rel="SELECT id_production_bom,bom_descr,bom_disable from gks_production_bom WHERE id_production_bom=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Συνταγή'),
    'link' => '<a href="admin-production-bom-item.php?id='.$row_rel['id_production_bom'].'">#'.$row_rel['id_production_bom'].'</a>',
    'oname' => $row_rel['bom_descr'],
    'state' => '<img src="img/'.($row_rel['bom_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}


else if ($name2=='gks_users_groups') {
  $sql_rel="select gks_users_groups.id_users_group,gks_users_groups.group_disable,
    CONCAT_WS('\\\\',
                    ug10.group_title,
                    ug9.group_title,
                    ug8.group_title,
                    ug7.group_title,
                    ug6.group_title,
                    ug5.group_title,
                    ug4.group_title,
                    ug3.group_title,
                    ug2.group_title,
                    gks_users_groups.group_title) as descr
    FROM ((((((((gks_users_groups
    LEFT JOIN gks_users_groups AS ug2 ON gks_users_groups.group_parent_id = ug2.id_users_group)
    LEFT JOIN gks_users_groups AS ug3 ON ug2.group_parent_id = ug3.id_users_group)
    LEFT JOIN gks_users_groups AS ug4 ON ug3.group_parent_id = ug4.id_users_group)
    LEFT JOIN gks_users_groups AS ug5 ON ug4.group_parent_id = ug5.id_users_group)
    LEFT JOIN gks_users_groups AS ug6 ON ug5.group_parent_id = ug6.id_users_group)
    LEFT JOIN gks_users_groups AS ug7 ON ug6.group_parent_id = ug7.id_users_group)
    LEFT JOIN gks_users_groups AS ug8 ON ug7.group_parent_id = ug8.id_users_group)
    LEFT JOIN gks_users_groups AS ug9 ON ug8.group_parent_id = ug9.id_users_group)
    LEFT JOIN gks_users_groups AS ug10 ON ug9.group_parent_id = ug10.id_users_group
    WHERE gks_users_groups.id_users_group=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Ομάδα Επαφών'),
    'link' => '<a href="admin-usersgroups-item.php?id='.$row_rel['id_users_group'].'">#'.$row_rel['id_users_group'].'</a>',
    'oname' => $row_rel['descr'],
    'state' => '<img src="img/'.($row_rel['group_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_warehouses') {
  $sql_rel="SELECT id_warehouse,warehouse_name,warehouse_color,warehouse_disable from gks_warehouses WHERE id_warehouse=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Αποθήκη'),
    'link' => '<a href="admin-warehouses-item.php?id='.$row_rel['id_warehouse'].'">#'.$row_rel['id_warehouse'].'</a>',
    'oname' => trim_gks($row_rel['warehouse_name']),
    'oname_bg' => trim_gks($row_rel['warehouse_color']),
    'state' => '<img src="img/'.($row_rel['warehouse_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}



else if ($name2=='gks_eshops') {
  $sql_rel="SELECT id_eshop,eshop_name,eshop_disable from gks_eshops WHERE id_eshop=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('eshop'),
    'link' => '<a href="admin-eshop-item.php?id='.$row_rel['id_eshop'].'">#'.$row_rel['id_eshop'].'</a>',
    'oname' => trim_gks($row_rel['eshop_name']),
    'oname_bg' => '',
    'state' => '<img src="img/'.($row_rel['eshop_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_eshop_products_brands') {
  $sql_rel="SELECT gks_eshop_products_brands.id_product_brand,gks_eshop_products_brands.brand_disable,
  CONCAT_WS('\\\\',
                  ug10.product_brand_descr,
                  ug9.product_brand_descr,
                  ug8.product_brand_descr,
                  ug7.product_brand_descr,
                  ug6.product_brand_descr,
                  ug5.product_brand_descr,
                  ug4.product_brand_descr,
                  ug3.product_brand_descr,
                  ug2.product_brand_descr,
                  gks_eshop_products_brands.product_brand_descr) as fullpath
  
  FROM ((((((((gks_eshop_products_brands
  LEFT JOIN gks_eshop_products_brands AS ug2  ON gks_eshop_products_brands.product_brand_parent_id = ug2.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.product_brand_parent_id = ug3.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.product_brand_parent_id = ug4.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.product_brand_parent_id = ug5.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.product_brand_parent_id = ug6.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.product_brand_parent_id = ug7.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.product_brand_parent_id = ug8.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.product_brand_parent_id = ug9.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.product_brand_parent_id = ug10.id_product_brand
  WHERE gks_eshop_products_brands.id_product_brand=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Μάρκα'),
    'link' => '<a href="admin-product-brands-item.php?id='.$row_rel['id_product_brand'].'">#'.$row_rel['id_product_brand'].'</a>',
    'oname' => trim_gks($row_rel['fullpath']),
    'oname_bg' => '',
    'state' => '<img src="img/'.($row_rel['brand_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_orders_occasion') {
  $sql_rel="SELECT gks_orders_occasion.id_order_occasion, gks_orders_occasion.title, gks_occasion_types.occasion_type_descr
  FROM gks_orders_occasion 
  LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type
  WHERE gks_orders_occasion.id_order_occasion=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Περίσταση'),
    'link' => '<a href="admin-orders-occasion-item.php?id='.$row_rel['id_order_occasion'].'">#'.$row_rel['id_order_occasion'].'</a>',
    'oname' => trim_gks($row_rel['title']).' - '.trim_gks($row_rel['occasion_type_descr']),
    'oname_bg' => '',
    'state' => '',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}




else if ($name2=='gks_custom_table') {
  $sql_rel="SELECT id_custom_table,custom_table_descr,custom_table_disabled FROM gks_custom_table WHERE id_custom_table=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Προσαρμογή'),
    'link' => '<a href="admin-custom-item.php?id='.$row_rel['id_custom_table'].'">#'.$row_rel['id_custom_table'].'</a>',
    'oname' => trim_gks($row_rel['custom_table_descr']),
    'oname_bg' => '',
    'state' => '<img src="img/'.($row_rel['custom_table_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}
else if ($name2=='gks_lang') {
  $sql_rel="SELECT * FROM gks_lang WHERE idd_lang=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Γλώσσα'),
    'link' => '<a href="admin-lang-item.php?id='.$row_rel['idd_lang'].'">#'.$row_rel['idd_lang'].'</a>',
    'oname' => trim_gks($row_rel['lang_name']),
    'oname_bg' => '',
    'state' => '<img src="img/'.($row_rel['lang_on_backend']!=0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_sociallinks_type') {
  $sql_rel="SELECT * FROM gks_sociallinks_type WHERE id_sociallinks_type=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Τύπος Συνδέσμων Κοινωνικών Δικτύων'),
    'link' => '<a href="admin-sociallinks-type-item.php?id='.$row_rel['id_sociallinks_type'].'">#'.$row_rel['id_sociallinks_type'].'</a>',
    'oname' => trim_gks($row_rel['sociallinks_type_descr']),
    'oname_bg' => '',
    'state' => '<img src="img/'.($row_rel['sociallinks_type_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_eshop_pricelist') {
  $sql_rel="SELECT * FROM gks_eshop_pricelist WHERE id_pricelist=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Τιμοκατάλογος'),
    'link' => '<a href="admin-pricelists-item.php?id='.$row_rel['id_pricelist'].'">#'.$row_rel['id_pricelist'].'</a>',
    'oname' => trim_gks($row_rel['pricelist_descr']),
    'oname_bg' => '',
    'state' => '<img src="img/'.($row_rel['pricelist_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}

else if ($name2=='gks_eshop_pricelist_items') {
  $sql_rel="SELECT * FROM gks_eshop_pricelist_items WHERE id_pricelist_item=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => gks_lang('Στοιχείο Τιμοκαταλόγου-Κουπόνι'),
    'link' => '<a href="admin-pricelists-items-item.php?id='.$row_rel['id_pricelist_item'].'">#'.$row_rel['id_pricelist_item'].'</a>',
    'oname' => trim_gks($row_rel['pricelist_item_descr']),
    'oname_bg' => '',
    'state' => '<img src="img/'.($row_rel['pricelist_item_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
    'price' => '',
    'date' => '',
    'balance' => '',
  );
}


else if (startwith($name2,'gks_ct_')) {
  $ctid=trim_gks(str_replace('gks_ct_','',$name2)); //echo '<pre>|'.$ctid.'|';die();
  $ctid=intval($ctid);
  if ($ctid<=10000) {
    $message=gks_lang('Δεν έχει ορισθεί το').' ctid. ('.$ctid.')';
    debug_mail(false,$message,'');
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}

  $field_id='id_gks_customt_gks_ct_'.$ctid;
  $custom_table_descr=gks_lang('Αντικείμενο');
  $sql_rel="select custom_table_descr from gks_custom_table where id_custom_table=".$ctid;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
    
  if ($result_rel->num_rows>=1) {
    $row_rel = $result_rel->fetch_assoc();
    $custom_table_descr=$row_rel['custom_table_descr'];
  }

  $sql_rel="SELECT * FROM gks_customt_gks_ct_".$ctid." WHERE id_gks_customt_gks_ct_".$ctid."=".$id2;
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {
    debug_mail(false,'error sql',$sql_rel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result_rel->num_rows==0) {
    debug_mail(false,'relative object not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σχετικό αντικείμενο')));
    echo json_encode($return); die();}
  $row_rel = $result_rel->fetch_assoc();
  $objv=array(
    'objname' => $custom_table_descr,
    'link' => '<a href="admin-ct-item.php?ctid='.$ctid.'&id='.$row_rel[$field_id].'">#'.$row_rel[$field_id].'</a>',
    'oname' => '#'.$row_rel[$field_id],
    'oname_bg' => '',
    'state' => '',
    'price' => '',
    'date' => showDate(strtotime($row_rel['cf_mydate_add']), 'd/m/Y\<\b\r\>H:i:s', 1),
    'balance' => '',
  );
  
  
  //echo '<pre>dddd '.$name2.' '.$custom_table_descr;die();
  
}









else if ($objv['objname']=='') {
  debug_mail(false,'objname is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (2)<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}
  




$sql="insert into gks_object_rel (
mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
object_name1,object_id1,object_name2,object_id2
) values (
now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
'".$db_link->escape_string($name1)."',".$id1.",'".$db_link->escape_string($name2)."',".$id2."
)";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  
$id_object_rel = $db_link->insert_id; 



$row_html=
'<tr class="object_rel_tr gks_object_rel_tr_new" data-id="'.$id_object_rel.'">'.
  '<th scope="row" nowrap class="mytdcm gks_object_rel_aa"></td>'.      
  '<td nowrap class="mytdcm">'.
    '<i class="fas fa-unlink unlink_object_rel" data-deleteafter="gks_fnc_object_rel_delete_after|'.$id_object_rel.'" data-id="'.$id_object_rel.'" data-model="gks_object_rel" title="'.gks_lang('Αποσύνδεση','part2').'"></i>'.
  '</td>'.
  '<td class="mytdcml">'.$objv['objname'].'</td>'.
  '<td class="mytdcm">'.$objv['link'].'</td>'.  
  '<td class="mytdcml" '.
                (isset($objv['oname_bg']) ? ' style="background-color:'.$objv['oname_bg'].'"' : '').
                '.>'.$objv['oname'].'</td>'.
  '<td nowrap class="mytdcm">'.$objv['state'].'</td>'.
  '<td nowrap class="mytdcm">'.$objv['price'].'</td>'.
  '<td nowrap class="mytdcm">'.$objv['date'].'</td>'.
  '<td nowrap class="mytdcm">'.$objv['balance'].'</td>'.
'</tr>';

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> '', 'row_html' => base64_encode($row_html));
echo json_encode($return); die();
  
$return = array('success' => false, 'message' => base64_encode('Δρρρρρρρρ'));
echo json_encode($return); die();
