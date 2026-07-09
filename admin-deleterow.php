<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
//gks_acc_inv_products_lots medflow

define('SECURE', 1);
include_once('functions.php');

$id=0;
$id_raw='';
if (isset($_POST['myid'])) {
	$id=intval($_POST['myid']);
	$id_raw=urldecode($_POST['myid']);	
}

if ($id<=0) {
  debug_mail(false,'the myid is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$mymodel='';
if (isset($_POST['mymodel'])) $mymodel=trim_gks($_POST['mymodel']);
if ($mymodel=='') {
  debug_mail(false,'the mymodel is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το model')));
  echo json_encode($return); die();}

if ($my_wp_user_id<=0) {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση').' '.$mymodel));
  echo json_encode($return); die();  
}


//
//$userrole='';
//if (isset($my_wp_user_info->roles)) {
//  if (in_array('ordermanager',$my_wp_user_info->roles))  $userrole='ordermanager';
//  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
//  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
//}
//if ($userrole=='') {
//  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση').' '.$mymodel));
//  echo json_encode($return); die();
//}
//
//

  

$my_page_title=gks_lang('Διαγραφή εγγραφής. Μοντέλο').':'.$mymodel.' id:'.$id;

db_open();
stat_record();

//echo '<pre>';echo time();die();

//if (ur_ad()) {
//  $perm_ret=array('success' => true,'message'=>'OK');
//} else {

if ($mymodel=='gks_object_rel') {
  $sql="select * from gks_object_rel where id_object_rel=".$id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows != 1) {
    debug_mail(false,'delete row','record not found');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }  
  $row = $result->fetch_assoc();
  $object_name1=trim_gks($row['object_name1']);
  $object_id1=trim_gks($row['object_id1']);
  $object_name2=trim_gks($row['object_name2']);
  $object_id2=trim_gks($row['object_id2']);
  
  $perm_ret1=gks_permission_user_can_action($my_wp_user_id, $object_name1,'edit',$object_id1);
  $perm_ret2=gks_permission_user_can_action($my_wp_user_id, $object_name2,'edit',$object_id2);
  
  if ($perm_ret1['success']==false) {
    debug_mail(false,'delete-deny',"\n".'model: '.$mymodel."\n".'id: '.$id."\n".print_r($perm_ret1,true));
    $return = array('success' => false, 'message' => base64_encode($perm_ret1['message']));echo json_encode($return); die();
  }
  if ($perm_ret2['success']==false) {
    debug_mail(false,'delete-deny',"\n".'model: '.$mymodel."\n".'id: '.$id."\n".print_r($perm_ret2,true));
    $return = array('success' => false, 'message' => base64_encode($perm_ret2['message']));echo json_encode($return); die();
  }  
  //echo '<pre>'.$object_name1.'|'.$object_name2;die();
  
  
} else {
    
  switch ($mymodel) {
    case 'gks_eshop_fiscal_position':
    case 'gks_aade_skopos_diakinisis':
    case 'gks_aade_katigoria_fpa_ejeresi':
    case 'gks_aade_katigoria_parakratoumemenon_foron':
    case 'gks_aade_katigoria_loipon_foron':
    case 'gks_aade_katigoria_xartosimou':
    case 'gks_aade_katigoria_telon':
    case 'gks_acc_eidi_parastatikon':
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_aade','edit',$id);
      break;  
    
    
    case 'gks_acc_inv_links': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','edit',$id);
      break;  
    case 'gks_whi_mov_links': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov','edit',$id);
      break;  
    case 'gks_acc_pay_links': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_pay','edit',$id);
      break;  
    case 'gks_crm_leads_links': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_leads','edit',$id);
      break;  
    case 'gks_crm_tasks_employee': 
    case 'gks_crm_tasks_links': 
    case 'gks_crm_tasks_machine': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_tasks','edit',$id);
      break;  
    case 'gks_users_favorites': 
      $perm_ret=array('success' => true,'message'=>'OK');
      break;
    case 'gks_users_groups_users': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','edit',$id);
      break;  

    case 'gks_woo_brands':
    case 'gks_woo_categories':
    case 'gks_woo_coupons':
    case 'gks_woo_product':
    case 'gks_eshop_products_brands_products': 
    case 'gks_eshop_products_categories_products': 
    case 'gks_production_ergasies_eidos': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','edit',$id);
      break;
    case 'gks_orders_links': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders','edit',$id);
      break;
    case 'gks_production_ergasies_mustdone': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_ergasies','edit',$id);
      break;
    case 'gks_production_posta_ergasies': 
    case 'gks_production_posta_users': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_posta','edit',$id);
      break;
    case 'gks_eshop_products_categories_photo': 
    case 'gks_eshop_products_categories_products': 
    case 'gks_production_ergasies_eidoscat': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products_categories','edit',$id);
      break;
  
    
    case 'gks_eshop_pricelist_items_products':
    case 'gks_eshop_pricelist_items_categories':
    case 'gks_eshop_pricelist_items_brands':
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_pricelist_items','edit',$id);
      break;
    case 'gks_hotel_reservation_links': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_reservation','edit',$id);
      break;  
  
    case 'gks_transfer_area2poi':
    case 'gks_transfer_area2transfer':
    case 'gks_transfer_area2externalpartner':
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_transfer_area','edit',$id);
      break;
      
    case 'gks_transfer_oxima2type2transfer': 
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets','edit',$id);
      break;
    
    case 'gks_object_rel':
      
      break;
    case 'gks_users_templates': 
      $perm_ret=array('success' => true,'message'=>'OK');
      break; 
    case 'gks_voip_favorites':
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_voip_calls','edit',$id);
      break;
    default:
      $perm_ret=gks_permission_user_can_action($my_wp_user_id, $mymodel,'delete',$id);
      break;
  }


  //print '<pre>'.$mymodel;print_r($perm_ret);die();
  
  if ($perm_ret['success']==false) {
    debug_mail(false,'delete-deny',"\n".'model: '.$mymodel."\n".'id: '.$id."\n".print_r($perm_ret,true));
    $return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();
  }
}
//echo '<pre>';print_r($perm_ret);die();

//$return = array('success' => false, 'message' => base64_encode('ssssss '.$mymodel));
//echo json_encode($return); die();


//check1
switch ($mymodel) {  
  case 'gks_aade_skopos_diakinisis':
    $sql="select * from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=".$id; break;
  case 'gks_aade_katigoria_fpa_ejeresi':
    $sql="select * from gks_aade_katigoria_fpa_ejeresi where id_aade_katigoria_fpa_ejeresi=".$id; break;
  case 'gks_aade_katigoria_parakratoumemenon_foron':
    $sql="select * from gks_aade_katigoria_parakratoumemenon_foron where id_aade_katigoria_parakratoumemenon_foron=".$id; break;
  case 'gks_aade_katigoria_loipon_foron':
    $sql="select * from gks_aade_katigoria_loipon_foron where id_aade_katigoria_loipon_foron=".$id; break;
  case 'gks_aade_katigoria_xartosimou':
    $sql="select * from gks_aade_katigoria_xartosimou where id_aade_katigoria_xartosimou=".$id; break;
  case 'gks_aade_katigoria_telon':
    $sql="select * from gks_aade_katigoria_telon where id_aade_katigoria_telon=".$id; break;
  case 'gks_acc_eidi_parastatikon':
    $sql="select * from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou=".$id; break;
  case 'gks_acc_inv':
    $sql="select * from gks_acc_inv where id_acc_inv=".$id; break;
  case 'gks_acc_inv_links':
    $sql="select * from gks_acc_inv_links where id_acc_inv_links=".$id; break;
  case 'gks_acc_pay':
    $sql="select * from gks_acc_pay where id_acc_pay=".$id; break;
  case 'gks_acc_pay_links':
    $sql="select * from gks_acc_pay_links where id_acc_pay_links=".$id; break;
  case 'gks_acc_journal':
    $sql="select * from gks_acc_journal where id_acc_journal=".$id; break;
  case 'gks_acc_seires':
    $sql="select * from gks_acc_seires where id_acc_seira=".$id; break;
  case 'gks_ads_campain':
    $sql="select * from gks_ads_campain where id_ads_campain=".$id; break;
  case 'gks_airline':
    $sql="select * from gks_airline where id_airline=".$id; break;
  case 'gks_assets':
    $sql="select * from gks_assets where id_asset=".$id; break;
  case 'gks_assets_service':
    $sql="select * from gks_assets_service where id_assets_service=".$id; break;
  case 'gks_assets_service_reasons':
    $sql="select * from gks_assets_service_reasons where id_assets_service_reasons=".$id; break;
  case 'gks_assets_type':
    $sql="select * from gks_assets_type where id_asset_type=".$id; break;
  case 'gks_assets_whi_mov':
    $sql="select * from gks_assets_whi_mov where id_assets_whi_mov=".$id; break;
  case 'gks_bank_accounts':
    $sql="select * from gks_bank_accounts where id_bank_account=".$id; break;
  case 'gks_banks':
    $sql="select * from gks_banks where id_bank=".$id; break;
  case 'gks_barcodes':
    $sql="select * from gks_barcodes where id_barcode=".$id; break;
  case 'gks_calendar':
    $sql="select * from gks_calendar where id_calendar=".$id; break;
  case 'gks_company':
    $sql="select * from gks_company where id_company=".$id; break;
  case 'gks_company_subs':
    $sql="select * from gks_company_subs where id_company_sub=".$id; break;
  case 'gks_company_users':
    $sql="select * from gks_company_users where id_company_users=".$id; break;
  case 'gks_country':
    $sql="select * from gks_country where id_country=".$id; break;
  case 'gks_crons':
    $sql="select * from gks_crons where id_cron=".$id; break;
  case 'gks_crm_activity':
    $sql="select * from gks_crm_activity where id_crm_activity=".$id; break;
  case 'gks_crm_channel_sale':
    $sql="select * from gks_crm_channel_sale where id_crm_channel_sale=".$id; break;
  case 'gks_crm_leads':
    $sql="select * from gks_crm_leads where id_crm_lead=".$id; break;
  case 'gks_crm_leads_links':
    $sql="select * from gks_crm_leads_links where id_crm_leads_links=".$id; break;
  case 'gks_crm_leads_status':
    $sql="select * from gks_crm_leads_status where id_crm_lead_status=".$id; break;
  case 'gks_crm_machine':
    $sql="select * from gks_crm_machine where id_crm_machine=".$id; break;
  case 'gks_crm_tasks':
    $sql="select * from gks_crm_tasks where id_crm_task=".$id; break;
  case 'gks_crm_tasks_employee':
    $sql="select * from gks_crm_tasks_employee where id_crm_task_employee=".$id; break;
  case 'gks_crm_tasks_links':
    $sql="select id_crm_tasks_links from gks_crm_tasks_links where id_crm_tasks_links=".$id; break;
  case 'gks_crm_tasks_machine':
    $sql="select * from gks_crm_tasks_machine where id_crm_task_machine=".$id; break;
  case 'gks_crm_tasks_status':
    $sql="select * from gks_crm_tasks_status where id_crm_task_status=".$id; break;
    
  case 'gks_delivery_methods':
    $sql="select * from gks_delivery_methods where id_delivery_method=".$id; break;
 
  case 'gks_email_template':
    $sql="select * from gks_email_template where id_email_template=".$id; break;
  case 'gks_erp_app':
    $sql="select * from gks_erp_app where id_erp_app=".$id; break;
  case 'gks_erp_app_mobile':
    $sql="select * from gks_erp_app_mobile where id_erp_app_mobile=".$id; break;
  case 'gks_eshop_fiscal_position':
    $sql="select * from gks_eshop_fiscal_position where id_fiscal_position=".$id; break;
  case 'gks_eshop_pricelist':
    if ($id<=4) {
      debug_mail(false,'delete row',gks_lang('Δεν πρέπει να διαγραφεί ο συγκεκριμένος τιμοκατάλογος'));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν πρέπει να διαγραφεί ο συγκεκριμένος τιμοκατάλος')));
      echo json_encode($return); die();}  
  
    $sql="select * from gks_eshop_pricelist where id_pricelist=".$id; break;
  case 'gks_eshop_pricelist_items':    
    $sql="select * from gks_eshop_pricelist_items where id_pricelist_item=".$id; break;
  case 'gks_eshop_pricelist_items_products':
    $sql="select * from gks_eshop_pricelist_items_products where id_pricelist_item_product=".$id; break;
  case 'gks_eshop_pricelist_items_categories':
    $sql="select * from gks_eshop_pricelist_items_categories where id_pricelist_item_category=".$id; break;
  case 'gks_eshop_pricelist_items_brands':
    $sql="select * from gks_eshop_pricelist_items_brands where id_pricelist_item_brand=".$id; break;
    
  case 'gks_eshop_product_lots':
    $sql="select * from gks_eshop_product_lots where id_lot_product=".$id; break;

  case 'gks_eshop_products':
    if ($id<=2) {
      debug_mail(false,'delete row',gks_lang('Δεν πρέπει να διαγραφεί το συγκεκριμένο είδος'));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν πρέπει να διαγραφεί το συγκεκριμένο είδος')));
      echo json_encode($return); die();}  
    $sql="select id_product from gks_eshop_products where id_product=".$id; break;
  
  
  case 'gks_eshop_products_brands': 
    $sql="select * from gks_eshop_products_brands where id_product_brand=".$id; break;
  case 'gks_eshop_products_categories': 
    $sql="select * from gks_eshop_products_categories where id_product_category=".$id; break;
  case 'gks_eshop_products_brands_products':
    $sql="select * from gks_eshop_products_brands_products where id_eshop_products_brands_products=".$id; break;
  case 'gks_eshop_products_categories_products':
    $sql="select * from gks_eshop_products_categories_products where id_eshop_products_categories_products=".$id; break;
  case 'gks_eshops':
    $sql="select * from gks_eshops where id_eshop=".$id; break;
    
  case 'gks_gsis_check':
    $sql="select * from gks_gsis_check where id_gsis_check=".$id; break;
  case 'gks_vies_check':
    $sql="select * from gks_vies_check where id_vies_check=".$id; break;
  case 'gks_voip_favorites':
    $sql="select * from gks_voip_favorites where id_voip_favorite=".$id." and user_id=".$my_wp_user_id; break;
  case 'gks_hotel':
    $sql="select * from gks_hotel where id_hotel=".$id; break;
  case 'gks_hotel_availability':
    $sql="select * from gks_hotel_availability where id_hotel_availability=".$id; break;
  case 'gks_hotel_floor':
    $sql="select * from gks_hotel_floor where id_hotel_floor=".$id; break;
  case 'gks_hotel_price':
    $sql="select * from gks_hotel_price where id_hotel_price=".$id; break;

  case 'gks_hotel_reservation':
    $sql="select * from gks_hotel_reservation where id_hotel_reservation=".$id; break;
  case 'gks_hotel_reservation_links':
    $sql="select * from gks_hotel_reservation_links where id_hotel_reservation_links=".$id; break;
    
    
  case 'gks_hotel_room':
    $sql="select id_hotel_room, room_status from gks_hotel_room where id_hotel_room=".$id; break;
  case 'gks_hotel_room_type':
    $sql="select id_hotel_room_type, room_type_status from gks_hotel_room_type where id_hotel_room_type=".$id; break;
  
  
  case 'gks_lang':
    $sql="select * from gks_lang where idd_lang=".$id; break;
  case 'gks_mass_messages':
    $sql="select * from gks_mass_messages where id_mass_message=".$id; break;
  
  case 'gks_monades_metrisis':
    $sql="select id_monada from gks_monades_metrisis where id_monada=".$id; break;
  case 'gks_nomoi':
    $sql="select id_nomos from gks_nomoi where id_nomos=".$id; break;

  
  case 'gks_object_rel':
    $sql="select * from gks_object_rel where id_object_rel=".$id; break;
  case 'gks_orders':
    $sql="select * from gks_orders where id_order=".$id; break;
  case 'gks_orders_links':
    $sql="select * from gks_orders_links where id_order_links=".$id; break;
  case 'gks_orders_occasion':
    $sql="select * from gks_orders_occasion where id_order_occasion=".$id; break;
  case 'gks_payment_acquirers':
    $sql="select * from gks_payment_acquirers where id_payment_acquirer=".$id; break;
  case 'gks_poi':
    $sql="select * from gks_poi where id_poi=".$id; break;
  case 'gks_poi_diadromes':
    $sql="select * from gks_poi_diadromes where id_poi_diadromes=".$id; break;
  case 'gks_poi_type':
    $sql="select * from gks_poi_type where id_poi_type=".$id; break;
  case 'gks_pos':
    $sql="select * from gks_pos where id_pos=".$id; break;
  case 'gks_print_forms':
    $sql="select * from gks_print_forms where id_print_form=".$id; break;
  case 'gks_product_idiotites':
    $sql="select * from gks_product_idiotites where id_product_idiotita=".$id; break;
  case 'gks_product_idiotites_terms':
    $sql="select * from gks_product_idiotites_terms where id_product_idiotita_term=".$id; break;
  
  
  
  
  
  case 'gks_production_bom':
    $sql="select * from gks_production_bom where id_production_bom=".$id; break;
  case 'gks_production_ergasies':
    $sql="select * from gks_production_ergasies where id_production_ergasia=".$id; break;
  case 'gks_production_ergasies_eidos':
    $sql="select * from gks_production_ergasies_eidos where id_production_ergasies_eidos=".$id; break;
  case 'gks_production_ergasies_eidoscat':
    $sql="select * from gks_production_ergasies_eidoscat where id_production_ergasies_eidoscat=".$id; break;
	case 'gks_production_ergasies_mustdone':
	  $sql="select * from gks_production_ergasies_mustdone where id_production_ergasia_mustdone=".$id; break;


  case 'gks_production_line':
    $sql="select * from gks_production_line where id_production_line=".$id; break;
  case 'gks_production_posta':
    $sql="select * from gks_production_posta where id_production_posto=".$id; break;
  case 'gks_production_posta_ergasies':
    $sql="select * from gks_production_posta_ergasies where id_production_posta_ergasies=".$id; break;
  case 'gks_production_posta_users':
    $sql="select * from gks_production_posta_users where id_production_posto_user=".$id; break;

  case 'gks_sms_viber_template':
    $sql="select * from gks_sms_viber_template where id_sms_viber_template=".$id; break;
  case 'gks_sociallinks_type':
    $sql="select * from gks_sociallinks_type where id_sociallinks_type=".$id; break;
  
  case 'gks_transfer':
    $sql="select * from gks_transfer where id_transfer=".$id; break;
  case 'gks_transfer_area':
    $sql="select * from gks_transfer_area where id_transfer_area=".$id; break;


  case 'gks_transfer_area2poi':
    $sql="select * from gks_transfer_area2poi where id_transfer_area2poi=".$id; break;
  case 'gks_transfer_area2transfer':
    $sql="select * from gks_transfer_area2transfer where id_transfer_area2transfer=".$id; break;
  case 'gks_transfer_area2externalpartner':
    $sql="select * from gks_transfer_area2externalpartner where id_transfer_area2externalpartner=".$id; break;

  case 'gks_transfer_oxima_type':
    $sql="select * from gks_transfer_oxima_type where id_transfer_oxima_type=".$id; break;
  case 'gks_transfer_oxima2type2transfer':
    $sql="select * from gks_transfer_oxima2type2transfer where id_transfer_oxima2type2transfer=".$id; break;
  
  case 'gks_transfer_pricelist':
    $sql="select * from gks_transfer_pricelist where id_transfer_pricelist=".$id; break;
    
  case 'gks_transfer_reservation': 
    $sql="select * from gks_transfer_reservation where id_transfer_reservation=".$id; break;
	case 'gks_urlshort':
	  $sql="select * from gks_urlshort where id_urlshort=".$id; break;
	case 'gks_urlshort_hit':
	  $sql="select * from gks_urlshort_hit where id_urlshort_hit=".$id; break;

 
    
  case 'gks_users_favorites':
    $sql="select * from gks_users_favorites where user_id=". $my_wp_user_id ." and id_favorites=".$id; break;
  case 'gks_users_groups':
    $sql="select * from gks_users_groups where id_users_group=". $id; break;
  case 'gks_users_groups_users':
    $sql="select * from gks_users_groups_users where id_users_groups_users=".$id; break;
  case 'gks_users_templates':
    $sql="select * from gks_users_templates where id_users_template=".$id; break;

  case 'gks_warehouses':
    $sql="select * from gks_warehouses where id_warehouse=".$id; break;
  case 'gks_whi_mov':
    $sql="select * from gks_whi_mov where id_whi_mov=".$id; break;
  case 'gks_whi_mov_links':
    $sql="select * from gks_whi_mov_links where id_whi_mov_links=".$id; break;
  
	  
	case 'gks_woo_brands':
	  $sql="select * from gks_woo_brands where id_woo_brand=".$id; break;
	case 'gks_woo_categories':
	  $sql="select * from gks_woo_categories where id_woo_category=".$id; break;
	case 'gks_woo_coupons':
	  $sql="select * from gks_woo_coupons where id_woo_coupon=".$id; break;
	case 'gks_woo_product':
	  $sql="select * from gks_woo_product where id_woo_product=".$id; break;

  case 'wp_users':
    $sql="select * from ".GKS_WP_TABLE_PREFIX."users where ID=".$id; break;
 	  
	case 'system_file':
    $sql="select now()"; break;


  default:
    $has_error=true;
    if (startwith($mymodel,'gks_ct_')) { // einai gks_custom_table
      //echo '<pre>'.$mymodel;die(); //gks_ct_10035
      $id_custom_table_str=str_replace('gks_ct_','', $mymodel);
      $id_custom_table=intval($id_custom_table_str);
      if ($id_custom_table_str===($id_custom_table.'')) {
        $sql="select * from gks_customt_gks_ct_".$id_custom_table." where id_gks_customt_gks_ct_".$id_custom_table."=".$id;
        $has_error=false;
        //echo '<pre>'.$sql;die(); 
      }
    } 
    if ($has_error) {
      debug_mail(false,'error on mymodel name (1)','');
      $return = array('success' => false, 'message' => base64_encode('error on mymodel name (1).'));
      echo json_encode($return); die(); 
    } 
    
}
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows != 1) {
  debug_mail(false,'delete row','record not found');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  }

$row = $result->fetch_assoc();

if ($mymodel=='gks_lang') {
  $id_lang=$row['id_lang'];
}



//check2
$error_lines=[];

switch ($mymodel) { 
  case 'gks_aade_skopos_diakinisis':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'aade_skopos_diakinisis_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'aade_skopos_diakinisis_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'def_aade_skopos_diakinisis_id','gks_pos');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο σκοπός διακίνησης διότι'));
    break;}
  case 'gks_aade_katigoria_fpa_ejeresi':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'product_fpa_ejeresi_id','gks_eshop_products');
    //gks_admin_delete_record_has_other($id,'product_fpa_ejeresi_id','gks_orders_products');
    gks_admin_delete_record_has_other($id,'product_fpa_ejeresi_id','gks_acc_inv_products');
    gks_admin_delete_record_has_other($id,'katigoria_fpa_ejeresi_id','gks_eshop_fiscal_position');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η αιτία εξαίρεσης ΦΠΑ διότι'));
    break;}
  case 'gks_aade_katigoria_parakratoumemenon_foron':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'product_withheldPercentCategory','gks_acc_inv_products');
    gks_admin_delete_record_has_other($id,'product_withheldPercentCategory','gks_eshop_products');
    //gks_admin_delete_record_has_other($id,'product_withheldPercentCategory','gks_orders_products');
    //gks_admin_delete_record_has_other($id,'product_withheldPercentCategory','gks_hotel_reservation_room');
    //gks_admin_delete_record_has_other($id,'product_withheldPercentCategory','gks_transfer_reservation_oximata');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο παρακρατούμενος φόρος διότι'));
    break;}
  case 'gks_aade_katigoria_loipon_foron':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'product_otherTaxesPercentCategory','gks_acc_inv_products');
    gks_admin_delete_record_has_other($id,'product_otherTaxesPercentCategory','gks_eshop_products');
    //gks_admin_delete_record_has_other($id,'product_otherTaxesPercentCategory','gks_orders_products');
    //gks_admin_delete_record_has_other($id,'product_otherTaxesPercentCategory','gks_hotel_reservation_room');
    //gks_admin_delete_record_has_other($id,'product_otherTaxesPercentCategory','gks_transfer_reservation_oximata');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο λοιπός φόρος διότι'));
    break;}
  case 'gks_aade_katigoria_xartosimou':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'product_stampDutyPercentCategory','gks_acc_inv_products');
    gks_admin_delete_record_has_other($id,'product_stampDutyPercentCategory','gks_eshop_products');
    //gks_admin_delete_record_has_other($id,'product_stampDutyPercentCategory','gks_orders_products');
    //gks_admin_delete_record_has_other($id,'product_stampDutyPercentCategory','gks_hotel_reservation_room');
    //gks_admin_delete_record_has_other($id,'product_stampDutyPercentCategory','gks_transfer_reservation_oximata');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το Ψηφιακό Τέλος συναλλαγής διότι'));
    break;}
  case 'gks_aade_katigoria_telon':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'product_feesPercentCategory','gks_acc_inv_products');
    gks_admin_delete_record_has_other($id,'product_feesPercentCategory','gks_eshop_products');
    //gks_admin_delete_record_has_other($id,'product_feesPercentCategory','gks_orders_products');
    //gks_admin_delete_record_has_other($id,'product_feesPercentCategory','gks_hotel_reservation_room');
    //gks_admin_delete_record_has_other($id,'product_feesPercentCategory','gks_transfer_reservation_oximata');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το Τέλος διότι'));
    break;}
  case 'gks_acc_eidi_parastatikon':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'acc_eidos_parastatikou_id','gks_acc_journal');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το Τέλος διότι'));
    break;}
  case 'gks_acc_inv':{
    if (trim_gks($row['inv_state'])!='010draft') {
      $error_lines[]=gks_lang('δεν είναι σε κατάσταση').' <span class="acc_inv_state_010draft">'.getAccInvStateDescr('010draft').'</span>';}
      
    $aade_invoicemark=trim_gks($row['aade_invoicemark']);
    $from_aade_import=trim_gks($row['from_aade_import']);
    if ($aade_invoicemark!='' and $from_aade_import=='') {
      $error_lines[]=gks_lang('έχει αποσταλεί στην ΑΑΔΕ ή Πάροχο');}
    
    gks_admin_delete_record_has_other($id,'cancel_for_acc_inv_id','gks_acc_inv',gks_lang('(ως ακυρωτικό)'));
    gks_admin_delete_record_has_other($id,'credit_memo_for_acc_inv_id','gks_acc_inv',gks_lang('(ως πιστωτικό)'));
    gks_admin_delete_record_has_other($id,'dimotikos_foros_for_acc_inv_id','gks_acc_inv',gks_lang('(ως δημοτικός φόρος)'));
    gks_admin_delete_record_has_other($id,'coi_acc_inv_id','gks_acc_inv_correlated_invoices');
    gks_admin_delete_record_has_other($id,'coi_acc_inv_id','gks_acc_pay_correlated_invoices');
    gks_admin_delete_record_has_other($id,'coi_acc_inv_id','gks_whi_mov_correlated_invoices');
    gks_admin_delete_record_has_other($id,'mcm_acc_inv_id','gks_acc_inv_multiple_connected_marks');
    gks_admin_delete_record_has_other($id,'mcm_acc_inv_id','gks_acc_pay_multiple_connected_marks');
    gks_admin_delete_record_has_other($id,'mcm_acc_inv_id','gks_whi_mov_multiple_connected_marks');
    gks_admin_delete_record_has_other($id,'acc_inv_id','gks_acc_pay_poso_acc_inv');
    
    gks_admin_delete_record_has_custom($id,1001); //Parastatiko
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το παραστατικό διότι'));
    break;}
  case 'gks_acc_inv_links':    break;
  case 'gks_acc_pay':{
    if (trim_gks($row['pay_state'])!='010draft') {
      $error_lines[]=gks_lang('δεν είναι σε κατάσταση').' <span class="acc_inv_state_010draft">'.getAccPayStateDescr('010draft').'</span>';}
    gks_admin_delete_record_has_other($id,'cancel_for_acc_pay_id','gks_acc_pay',gks_lang('(ως ακυρωτικό)'));
    gks_admin_delete_record_has_other($id,'credit_memo_for_acc_pay_id','gks_acc_pay',gks_lang('(ως επιστροφή)'));
    gks_admin_delete_record_has_other($id,'coi_acc_pay_id','gks_acc_inv_correlated_invoices');
    gks_admin_delete_record_has_other($id,'coi_acc_pay_id','gks_acc_pay_correlated_invoices');
    gks_admin_delete_record_has_other($id,'coi_acc_pay_id','gks_whi_mov_correlated_invoices');
    gks_admin_delete_record_has_other($id,'mcm_acc_pay_id','gks_acc_inv_multiple_connected_marks');
    gks_admin_delete_record_has_other($id,'mcm_acc_pay_id','gks_acc_pay_multiple_connected_marks');
    gks_admin_delete_record_has_other($id,'mcm_acc_pay_id','gks_whi_mov_multiple_connected_marks');
    
    gks_admin_delete_record_has_custom($id,1023); //Pliromi
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η πληρωμή διότι'));
    break;}
  case 'gks_acc_pay_links':    break;
    
  case 'gks_acc_journal': {
    gks_admin_delete_record_has_other($id,'acc_journal_id','gks_acc_seires');
    gks_admin_delete_record_has_other($id,'inv_acc_journal_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'mov_whi_journal_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'pay_acc_journal_id','gks_acc_pay');
    gks_admin_delete_record_has_other($id,'acc_journal_id','gks_eshops',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'acc_journal_id_tim','gks_eshops',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'reservation_journal_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'order_journal_id','gks_orders');
    gks_admin_delete_record_has_other($id,'poi_parastatiko_apodiji_journal_id','gks_poi',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'poi_parastatiko_timologio_journal_id','gks_poi',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'pos_journal_id','gks_pos');
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_journal_id','gks_transfer',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_journal_id','gks_transfer',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'transfer_reservation_journal_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_journal_id','gks_transfer_sub_company_details',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_journal_id','gks_transfer_sub_company_details',gks_lang('(ως τιμολόγιο)'));

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το ημερολόγιο διότι'));
    break;}
  case 'gks_acc_seires': {

    gks_admin_delete_record_has_other($id,'inv_acc_seira_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'mov_whi_seira_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'pay_acc_seira_id','gks_acc_pay');
    gks_admin_delete_record_has_other($id,'acc_seira_id','gks_eshops',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'acc_seira_id_tim','gks_eshops',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'reservation_seira_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'order_seira_id','gks_orders');
    gks_admin_delete_record_has_other($id,'poi_parastatiko_apodiji_seira_id','gks_poi',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'poi_parastatiko_timologio_seira_id','gks_poi',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'pos_seira_id','gks_pos');
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_seira_id','gks_transfer',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_seira_id','gks_transfer',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'transfer_reservation_seira_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_seira_id','gks_transfer_sub_company_details',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_seira_id','gks_transfer_sub_company_details',gks_lang('(ως τιμολόγιο)'));

    gks_admin_delete_record_has_custom($id,1003); //Seira
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η σειρά διότι'));

    break;}
    
  case 'gks_ads_campain': 
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_orders');
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_acc_pay');
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'def_crm_channel_campain_id','gks_pos');
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_urlshort');
    gks_admin_delete_record_has_other($id,'crm_channel_campain_id','gks_urlshort_hit');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η καμπάνια διότι'));
    break;
  case 'gks_airline': {
    //gks_admin_delete_record_has_other($id,'','');
    //gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η αεροπορικές εταιρεία διότι'));
    break;}
  case 'gks_assets':{
    gks_admin_delete_record_has_other($id,'asset_id','gks_acc_inv_payment');
    gks_admin_delete_record_has_other($id,'asset_id','gks_acc_pay_payment');
    gks_admin_delete_record_has_other($id,'asset_id','gks_assets_moves');
    gks_admin_delete_record_has_other($id,'asset_id','gks_assets_rental_asset');
    gks_admin_delete_record_has_other($id,'asset_id','gks_assets_service');
    gks_admin_delete_record_has_other($id,'asset_id','gks_assets_whi_mov_assets');
    gks_admin_delete_record_has_other($id,'asset_id','gks_eftpos_transaction');
    gks_admin_delete_record_has_other($id,'asset_id','gks_paroxos_signature');
    gks_admin_delete_record_has_other($id,'dromologio_asset_id','gks_transfer_dromologio');
    gks_admin_delete_record_has_other($id,'transfer_oxima_asset_id','gks_transfer_reservation_oximata');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το πάγιο διότι'));
    break;}
 
  case 'gks_assets_service':{
    if ($row['isconfirm']!=0) {$error_lines[]=gks_lang('είναι επιβεβαιωμένο');}
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το service παγίου διότι'));
    break;}  
  case 'gks_assets_service_reasons':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'reason_id','gks_assets_service');
    
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η αιτία service παγίου διότι'));
    break;}  
  case 'gks_assets_type':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'asset_type','gks_assets');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο τύπος παγίου διότι'));

    break;}  
  case 'gks_assets_whi_mov':{
    if ($row['assets_whi_mov_status']!='00draft') {
      $error_lines[]=gks_lang('δεν είναι σε κατάσταση').' <span class="assets_apografi_state_00draft">'.get_assets_whi_mov_descr('00draft').'</span>';}
    
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η απογραφή παγίων διότι'));
    break;}
  case 'gks_bank_accounts': {
    
    
    break;}
  case 'gks_banks': {
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'bank_id','gks_assets');
    gks_admin_delete_record_has_other($id,'bank_id','gks_bank_accounts');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η τράπεζα διότι'));
    break;}
  case 'gks_barcodes': break;
    
  case 'gks_calendar': {
    $uri=trim_gks($row['uri']);
    $calendar_user_id=intval($row['calendar_user_id']);
    $sql="SELECT gks_calendar.id_calendar, gks_calendar.calendar_user_id, gks_calendar_other_users.other_user_id
    FROM gks_calendar LEFT JOIN gks_calendar_other_users ON gks_calendar.calendar_user_id = gks_calendar_other_users.other_user_id
    WHERE gks_calendar.id_calendar=".$id." and (gks_calendar.calendar_user_id=".$my_wp_user_id." OR gks_calendar_other_users.this_user_id=".$my_wp_user_id.")";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
    if ($result->num_rows == 0) {
      $error_lines[]=gks_lang('Δεν έχετε τα κατάλληλα δικαιώματα');} 
    
    gks_admin_delete_record_has_custom($id,1002); //Calendar
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η καταχώρηση ημερολογίου διότι'));
    break;}
    
  case 'gks_company':{
    gks_admin_delete_record_has_other($id,'company_id','gks_company_users');
    gks_admin_delete_record_has_other($id,'company_id','gks_company_subs');
    gks_admin_delete_record_has_other($id,'company_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'company_id','gks_acc_pay');
    gks_admin_delete_record_has_other($id,'company_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'company_id','gks_acc_journal');
    gks_admin_delete_record_has_other($id,'company_id','gks_bank_transactions');
    gks_admin_delete_record_has_other($id,'company_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'company_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'company_id','gks_hotel');
    gks_admin_delete_record_has_other($id,'company_id','gks_warehouses');
    gks_admin_delete_record_has_other($id,'company_id','gks_orders');
    gks_admin_delete_record_has_other($id,'asset_last_company_id','gks_assets');
    gks_admin_delete_record_has_other($id,'viva_company_id','gks_assets',gks_lang('(ως Viva)'));
    gks_admin_delete_record_has_other($id,'cardlink_company_id','gks_assets',gks_lang('(ως Cardlink)'));
    gks_admin_delete_record_has_other($id,'mellon_company_id','gks_assets',gks_lang('(ως Mellon)'));
    gks_admin_delete_record_has_other($id,'megeftpos_company_id','gks_assets',gks_lang('(ως Meg EFT/POS)'));
    gks_admin_delete_record_has_other($id,'epay_company_id','gks_assets',gks_lang('(ως ePay)'));
    gks_admin_delete_record_has_other($id,'worldline_company_id','gks_assets',gks_lang('(ως Worldline)'));
    gks_admin_delete_record_has_other($id,'nexi_company_id','gks_assets',gks_lang('(ως NEXI)'));
    gks_admin_delete_record_has_other($id,'company_id','gks_assets_moves');
    gks_admin_delete_record_has_other($id,'company_id','gks_assets_rental');
    gks_admin_delete_record_has_other($id,'company_id','gks_eftpos_transaction');
    gks_admin_delete_record_has_other($id,'company_id','gks_eshops');
    gks_admin_delete_record_has_other($id,'poi_company_id','gks_poi');
    gks_admin_delete_record_has_other($id,'pos_company_id','gks_pos');
    gks_admin_delete_record_has_other($id,'company_id','gks_production_bom');
    gks_admin_delete_record_has_other($id,'company_id','gks_transfer');
    gks_admin_delete_record_has_other($id,'company_id','gks_transfer_area');
    
    gks_admin_delete_record_has_custom($id,1004); //Campany
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η εταιρεία διότι'));

            
    break;   }
  case 'gks_company_subs':{
    
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_company_users');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_acc_pay');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_acc_journal');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_bank_transactions');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_hotel');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_warehouses');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_orders');
    gks_admin_delete_record_has_other($id,'asset_last_company_id','gks_assets');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_assets_rental');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_eshops');
    gks_admin_delete_record_has_other($id,'poi_company_sub_id','gks_poi');
    gks_admin_delete_record_has_other($id,'pos_company_sub_id','gks_pos');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_production_bom');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_transfer');
    gks_admin_delete_record_has_other($id,'company_sub_id','gks_transfer_area');
    
    gks_admin_delete_record_has_custom($id,1005); //Subcompany
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το υποκατάστημα διότι'));

    break;  }


  case 'gks_company_users':{
    $company_id = $row['company_id'];
    $company_sub_id = $row['company_sub_id'];
    $user_id = $row['user_id'];
    break;  }
  case 'gks_country':{
    gks_admin_delete_record_has_other($id,'country_id','gks_nomoi');
    gks_admin_delete_record_has_other($id,'ma_country_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'destination_data_country_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'other_ma_country_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'load_country_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'deli_country_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'entity_country_id','gks_acc_inv_other_entity');
    gks_admin_delete_record_has_other($id,'ma_country_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'destination_data_country_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'other_ma_country_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'load_country_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'deli_country_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'entity_country_id','gks_whi_mov_other_entity');
    gks_admin_delete_record_has_other($id,'ma_country_id','gks_orders');
    gks_admin_delete_record_has_other($id,'destination_data_country_id','gks_orders');
    gks_admin_delete_record_has_other($id,'other_ma_country_id','gks_orders');
    gks_admin_delete_record_has_other($id,'ma_country_id','gks_users');
    gks_admin_delete_record_has_other($id,'ea_country_id','gks_users_extra_address');
    gks_admin_delete_record_has_other($id,'country_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'country_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'calendar_country_id','gks_calendar');
    gks_admin_delete_record_has_other($id,'company_country_id','gks_company');
    gks_admin_delete_record_has_other($id,'company_sub_country_id','gks_company_subs');
    gks_admin_delete_record_has_other($id,'warehouse_country_id','gks_warehouses');
    gks_admin_delete_record_has_other($id,'ma_country_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'other_ma_country_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'ruser_ma_country_id','gks_hotel_reservation_room');
    gks_admin_delete_record_has_other($id,'hotel_country_id','gks_hotel');
    gks_admin_delete_record_has_other($id,'user_ma_country_id','gks_hotel_folio');
    gks_admin_delete_record_has_other($id,'fuser_ma_country_id','gks_hotel_folio_room');
    gks_admin_delete_record_has_other($id,'doy_country_id','gks_doy');
    gks_admin_delete_record_has_other($id,'ma_country_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'other_ma_country_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'ruser_ma_country_id','gks_transfer_reservation_oximata');
    gks_admin_delete_record_has_other($id,'airline_country_id','gks_airline');
    gks_admin_delete_record_has_other($id,'user_ma_country_id','gks_assets_rental');
    gks_admin_delete_record_has_other($id,'fuser_ma_country_id','gks_assets_rental_asset');
    gks_admin_delete_record_has_other($id,'country_id','gks_perifereies');
    gks_admin_delete_record_has_other($id,'poi_country_id','gks_poi');
    gks_admin_delete_record_has_other($id,'transfer_country_id','gks_transfer');
    gks_admin_delete_record_has_other($id,'transfer_area_country_id','gks_transfer_area');
    
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η χώρα διότι'));
    break;}
    
  case 'gks_crons': {
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο Χρονοπρογραμματισμός Εργασίας διότι'));
    break;}  
  case 'gks_crm_activity':    
    $calendar_id=$row['calendar_id'];    
    break;
  case 'gks_crm_channel_sale': {
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_orders');
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_acc_pay');
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'def_crm_channel_id','gks_pos');
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_urlshort');
    gks_admin_delete_record_has_other($id,'crm_channel_id','gks_urlshort_hit');
    
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το κανάλι πωλήσεων διότι'));    
    
    break;}
  case 'gks_crm_leads': 
    if ($row['lead_status_id']!=1) {
      gks_get_leads_status($leads_status,$leads_status_styles);
      $error_lines[]=gks_lang('δεν είναι σε κατάσταση').' <span class="lead_status_1">'.$leads_status[1]['lead_status_descr'].'</span>';}
    gks_admin_delete_record_has_custom($id,1006); //Lead
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η ευκαιρία διότι'));
    break;
  case 'gks_crm_leads_links':    break;
  case 'gks_crm_leads_status': {
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'lead_status_id','gks_crm_leads');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η κατάσταση ευκαιριών διότι'));
  
    break;}
  case 'gks_crm_machine':{
    gks_admin_delete_record_has_other($id,'crm_task_machine_id','gks_crm_tasks_machine');
    
    gks_admin_delete_record_has_custom($id,1027); //Machine CRM
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η συσκευή διότι'));
    break;}
  case 'gks_crm_tasks':   
    if ($row['task_status_id']!=1) {
      gks_get_tasks_status($tasks_status,$tasks_status_styles);
      $error_lines[]=gks_lang('δεν είναι σε κατάσταση').' <span class="task_status_1">'.$tasks_status[1]['task_status_descr'].'</span>';}
    
    gks_admin_delete_record_has_custom($id,1026); //Task CRM
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η εργασία διότι'));
    //die('sssss'.$row['task_status_id']);    
    break;
  case 'gks_crm_tasks_employee':    break;
  case 'gks_crm_tasks_links':    break;
  case 'gks_crm_tasks_machine':    break;
  case 'gks_crm_tasks_status':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'task_status_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'print_crm_task_status_id','gks_crm_tasks');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η κατάσταση εργασιών διότι'));
      
    break;}
  case 'gks_delivery_methods':{
    gks_admin_delete_record_has_other($id,'tropos_apostolis','gks_orders');
    gks_admin_delete_record_has_other($id,'tropos_apostolis','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'tropos_apostolis','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'tropos_apostolis','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'tropos_apostolis','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'def_tropos_apostolis','gks_pos');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί o τρόπος αποστολής διότι'));
    break;}
  case 'gks_email_template':
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_email_template_id','gks_transfer',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_email_template_id','gks_transfer',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_email_template_id','gks_transfer_sub_company_details',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_email_template_id','gks_transfer_sub_company_details',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'template_id','gks_email');
    gks_admin_delete_record_has_other($id,'email_template_id','gks_mass_messages');
    
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το πρότυπο email διότι'));
    break;
  case 'gks_erp_app':
    gks_admin_delete_record_has_other($id,'erp_app_id','gks_acc_seires');
    gks_admin_delete_record_has_other($id,'cardlink_ecr2eftweb_erp_app_id','gks_assets','(cardlink)');
    gks_admin_delete_record_has_other($id,'megeftpos_erp_app_id','gks_assets','(megeftpos)');
    gks_admin_delete_record_has_other($id,'erp_app_id','gks_pos');
    gks_admin_delete_record_has_other($id,'erp_app_id','gks_acc_seires');
    
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το gks ERP App Desktop διότι'));
    break;
  case 'gks_erp_app_mobile':
    $sql="select count(*) as cc from gks_pos where pos_sms_erp_app_mobile_id_code like 'gks_erp_app_mobile:".$id."'"; 
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
    if ($result->num_rows > 0) {$row = $result->fetch_assoc();$cc=intval($row['cc']); 
      if ($cc>0) $error_lines[]=str_replace('[1]',$cc,gks_lang('έχει χρησιμοποιηθεί σε <b>[1]</b> σημεία εντατικής λιανικής'));}
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το gks ERP App Mobile διότι'));
    break;
    
  case 'gks_eshop_fiscal_position':{
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}
    gks_admin_delete_record_has_other($id,'fiscal_position_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'fiscal_position_id','gks_assets_rental');
    gks_admin_delete_record_has_other($id,'fiscal_position_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'fiscal_position_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'fiscal_position_id','gks_hotel_folio');
    gks_admin_delete_record_has_other($id,'fiscal_position_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'ruser_fiscal_position_id','gks_hotel_reservation_room',);
    gks_admin_delete_record_has_other($id,'fiscal_position_id','gks_orders');
    gks_admin_delete_record_has_other($id,'def_fiscal_position_id','gks_pos');
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_fiscal_position_id','gks_transfer',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_fiscal_position_id','gks_transfer',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'fiscal_position_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'ruser_fiscal_position_id','gks_transfer_reservation_oximata');
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_fiscal_position_id','gks_transfer_sub_company_details',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_fiscal_position_id','gks_transfer_sub_company_details',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'fiscal_position_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'fiscal_position_id',GKS_WP_TABLE_PREFIX.'users');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η φορολογική θέση διότι'));
    break;}
    
  case 'gks_eshop_pricelist':
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_eshop_pricelist_items');
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_assets_rental');
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_hotel_folio');
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'ruser_pricelist_id','gks_hotel_reservation_room',);
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_orders');
    gks_admin_delete_record_has_other($id,'def_pricelist_id','gks_pos');
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_pricelist_id','gks_transfer',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_pricelist_id','gks_transfer',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'ruser_pricelist_id','gks_transfer_reservation_oximata');
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_pricelist_id','gks_transfer_sub_company_details',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_pricelist_id','gks_transfer_sub_company_details',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'pricelist_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'pricelist_id',GKS_WP_TABLE_PREFIX.'users');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί o τιμοκατάλογος διότι'));
    break;
  case 'gks_eshop_pricelist_items':
    gks_admin_delete_record_has_other($id,'product_pricelist_item_id','gks_acc_inv_products');
    gks_admin_delete_record_has_other($id,'product_pricelist_item_id','gks_hotel_reservation_room');
    gks_admin_delete_record_has_other($id,'product_pricelist_item_id','gks_orders_products');
    gks_admin_delete_record_has_other($id,'product_pricelist_item_id','gks_transfer_reservation_oximata');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το Στοιχείο Τιμοκαταλόγου-Κουπόνι διότι'));



    
    break;
  case 'gks_eshop_pricelist_items_products': break;
  case 'gks_eshop_pricelist_items_categories': break;
  case 'gks_eshop_pricelist_items_brands': break;

  case 'gks_eshop_product_lots':{
    gks_admin_delete_record_has_other($id,'lot_product_id','gks_acc_inv_products_lots');
    gks_admin_delete_record_has_other($id,'lot_product_id','gks_whi_mov_products_lots');
    gks_admin_delete_record_has_other($id,'lot_product_id','gks_orders_products_lots');
    gks_admin_delete_record_has_other($id,'lot_product_id','gks_production_sintagi_product_lots_serials');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η πατρίδα - Serial Number διότι'));
    
    break;}
  case 'gks_eshop_products':{
    
    $id_array=array();
    $id_array[]=$id;
    $sql="select id_product from gks_eshop_products where product_parent_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
    while ($row = $result->fetch_assoc()) {
      $id_array[]=$row['id_product'];
    }    
    
    gks_admin_delete_record_has_other($id_array,'product_id','gks_orders_products');
    gks_admin_delete_record_has_other($id_array,'product_id','gks_acc_inv_products');
    gks_admin_delete_record_has_other($id_array,'product_id','gks_whi_mov_products');
    gks_admin_delete_record_has_other($id_array,'eidos_id','gks_production_ergasies_eidos');
    gks_admin_delete_record_has_other($id_array,'bom_product_id','gks_production_bom');
    gks_admin_delete_record_has_other($id_array,'production_bom_id','gks_production_bom_product');
    gks_admin_delete_record_has_other($id_array,'pbom_product_id','gks_production_bom_product');
    gks_admin_delete_record_has_other($id_array,'pbom_variant_product_id','gks_production_bom_product');
    gks_admin_delete_record_has_other($id_array,'production_bom_product_id','gks_production_sintagi_product');
    gks_admin_delete_record_has_other($id_array,'spbom_product_id','gks_production_sintagi_product');
    gks_admin_delete_record_has_other($id_array,'crm_machine_product_id','gks_crm_machine');
    gks_admin_delete_record_has_other($id_array,'product_id','gks_eshop_pricelist_items_products');
    gks_admin_delete_record_has_other($id_array,'product_id','gks_hotel_reservation_room');
    gks_admin_delete_record_has_other($id_array,'product_id','gks_hotel_room_type');
    gks_admin_delete_record_has_other($id_array,'lotproduct_id','gks_eshop_product_lots');
    gks_admin_delete_record_has_other($id_array,'hotel_efd_product_id','gks_hotel');
    gks_admin_delete_record_has_other($id_array,'transfer_product_id_sms_text_message','gks_transfer','(SMS)');
    gks_admin_delete_record_has_other($id_array,'transfer_product_id_cancellation_protection','gks_transfer','(cancellation)');
    gks_admin_delete_record_has_other($id_array,'product_id','gks_transfer_oxima_type');
    gks_admin_delete_record_has_other($id_array,'product_id','gks_transfer_reservation_oximata');
    //gks_admin_delete_record_has_other($id_array,'product_id','gks_barcodes');

    gks_admin_delete_record_has_custom($id_array,1007); //Product
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το είδος διότι'));

     
    //echo '<pre>';print_r($id_array);die();  
    
    break;}
  
  case 'gks_eshop_products_brands': 
    gks_admin_delete_record_has_other($id,'product_brand_parent_id','gks_eshop_products_brands',gks_lang('(γονική)'));
    gks_admin_delete_record_has_other($id,'product_brand_id','gks_eshop_pricelist_items_brands');
    
    gks_admin_delete_record_has_custom($id,1025);//Brand
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η μάρκα διότι'));
    break;
  case 'gks_eshop_products_categories':
    gks_admin_delete_record_has_other($id,'product_category_parent_id','gks_eshop_products_categories',gks_lang('(γονική)'));
    gks_admin_delete_record_has_other($id,'product_category_id','gks_eshop_pricelist_items_categories');
    
    gks_admin_delete_record_has_custom($id,1008); //Product category
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η κατηγορία διότι'));
    break;
  case 'gks_eshop_products_brands_products':    
    break;
  case 'gks_eshop_products_categories_products':    break;
    
  case 'gks_eshops':
    gks_admin_delete_record_has_other($id,'woo_eshop_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'woo_eshop_id','gks_acc_inv_messages');
    gks_admin_delete_record_has_other($id,'woo_eshop_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'woo_eshop_id','gks_hotel_reservation_messages');
    gks_admin_delete_record_has_other($id,'woo_eshop_id','gks_orders');
    gks_admin_delete_record_has_other($id,'woo_eshop_id','gks_orders_messages');
    gks_admin_delete_record_has_other($id,'woo_eshop_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'woo_eshop_id','gks_transfer_reservation_messages');
    
    gks_admin_delete_record_has_custom($id,1024); //eshop
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το eshop διότι'));
    break;
  case 'gks_gsis_check':    break;
  case 'gks_vies_check':    break;
  case 'gks_voip_favorites': break;  
  case 'gks_hotel':{
    if ($row['hotel_disable']==0 ) {
      $error_lines[]=gks_lang('δεν θα πρέπει να είναι ενεργό');}
          
    gks_admin_delete_record_has_other($id,'hotel_id','gks_hotel_floor');
    gks_admin_delete_record_has_other($id,'hotel_id','gks_hotel_folio');
    gks_admin_delete_record_has_other($id,'hotel_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'hotel_id','gks_hotel_room');
    gks_admin_delete_record_has_other($id,'hotel_id','gks_hotel_room_type');
    
    gks_admin_delete_record_has_custom($id,1009); //Hotel
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το ξενοδοχείο διότι'));
    break;}
  case 'gks_hotel_availability':{
    $hotel_room_type_id=$row['hotel_room_type_id'];
    $hotel_room_id=$row['hotel_room_id'];
    $availability_from=date('Y-m-d',strtotime($row['availability_from']));
    $availability_to=''; if (isset($row['availability_to'])) $availability_to = date('Y-m-d',strtotime($row['availability_to']));
    
    gks_admin_delete_record_has_custom($id,1010);//Diathesimotita
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η διαθεσιμότητα διότι'));
    
    break;}
  case 'gks_hotel_floor':{
    gks_admin_delete_record_has_other($id,'hotel_floor_id','gks_hotel_room');
    
    gks_admin_delete_record_has_custom($id,1011); //Orofos
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο όροφος διότι'));
    break;}
  case 'gks_hotel_price':{
    $hotel_room_type_id=$row['hotel_room_type_id'];
    $price_from=date('Y-m-d',strtotime($row['price_from']));
    $price_to=''; if (isset($row['price_to'])) $price_to = date('Y-m-d',strtotime($row['price_to']));
    
    gks_admin_delete_record_has_custom($id,1012); //Timi domatiou
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η τιμή διότι'));    
    
    break;}
  case 'gks_hotel_reservation':{
    if ($row['reservation_status']!='005prodraft' and $row['reservation_status']!='010draft') {
      $error_lines[]=gks_lang('δεν είναι σε κατάσταση').' <span class="reservation_status_010draft">'.getHotelReservationStatusDescr('010draft').'</span>';}
    gks_admin_delete_record_has_other($id,'hotel_reservation_id','gks_acc_pay_poso_hotel_reservation');
    gks_admin_delete_record_has_other($id,'hotel_reservation_id','gks_hotel_folio');
    
    gks_admin_delete_record_has_custom($id,1013); //Kratisi
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η κράτηση ξενοδοχείου διότι'));
    break;  }

  case 'gks_hotel_reservation_links':
    break;      
  case 'gks_hotel_room':{
    $sql="select room_status from gks_hotel_room where room_status='disable' and id_hotel_room=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
    if ($result->num_rows == 0) {
      $error_lines[]=gks_lang('δεν είναι <span class="room_status_disable">Ανενεργό</span>');}  
      
    gks_admin_delete_record_has_other($id,'hotel_room_id','gks_hotel_folio_room');
    gks_admin_delete_record_has_other($id,'hotel_room_id','gks_hotel_reservation_room');
    
    gks_admin_delete_record_has_custom($id,1014); //Domatio
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το δωμάτιο διότι')); 
    break;}
  
    
  case 'gks_hotel_room_type':{
    $sql="select room_type_status from gks_hotel_room_type where room_type_status='disable' and id_hotel_room_type=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
    if ($result->num_rows == 0) {
      $error_lines[]=gks_lang('δεν είναι <span class="room_type_status_disable">Ανενεργό</span>');}  
      
    gks_admin_delete_record_has_other($id,'hotel_room_type_id','gks_hotel_room');
    gks_admin_delete_record_has_other($id,'hotel_room_type_id','gks_hotel_reservation_room_type');
    gks_admin_delete_record_has_other($id,'hotel_room_type_id','gks_hotel_room_type_channel_name');
    
    gks_admin_delete_record_has_custom($id,1015); //Tipos domatiou
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο τύπος δωματίου διότι'));      
    break;}


  case 'gks_lang':
    $id_langs="'".$id_lang."'";
    gks_admin_delete_record_has_other($id_langs,'user_lang','gks_acc_inv');
    gks_admin_delete_record_has_other($id_langs,'fuser_lang','gks_assets_rental_asset');
    gks_admin_delete_record_has_other($id_langs,'user_lang','gks_crm_leads');
    gks_admin_delete_record_has_other($id_langs,'user_lang','gks_crm_tasks');
    gks_admin_delete_record_has_other($id_langs,'order_meta_user_lang','gks_eshops');
    gks_admin_delete_record_has_other($id_langs,'user_lang','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id_langs,'ruser_lang','gks_hotel_reservation_room');
    gks_admin_delete_record_has_other($id_langs,'user_lang','gks_orders');
    gks_admin_delete_record_has_other($id_langs,'def_user_lang','gks_pos');
    gks_admin_delete_record_has_other($id_langs,'user_lang','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id_langs,'ruser_lang','gks_transfer_reservation_oximata');
    gks_admin_delete_record_has_other($id_langs,'user_lang','gks_whi_mov');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η γλώσσα διότι'));
    break;
    
  case 'gks_mass_messages':
    $error_lines[]=gks_lang('έχουν γίνει κάποιες αποστολές');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η Μαζική Αποστολή SMS-Viber-email διότι'));
    break;
  case 'gks_monades_metrisis':
    gks_admin_delete_record_has_other($id,'monada_parent_id','gks_monades_metrisis');
    gks_admin_delete_record_has_other($id,'product_monada_id_org','gks_acc_inv_products',gks_lang('(ως αρχική μονάδα)'));
    gks_admin_delete_record_has_other($id,'product_monada_id','gks_acc_inv_products');
    gks_admin_delete_record_has_other($id,'product_monada_id','gks_eshop_products');
    gks_admin_delete_record_has_other($id,'product_monada_id_org','gks_orders_products');
    gks_admin_delete_record_has_other($id,'product_monada_id','gks_orders_products');
    gks_admin_delete_record_has_other($id,'product_monada_id_org','gks_whi_mov_products');
    gks_admin_delete_record_has_other($id,'product_monada_id','gks_whi_mov_products');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η μονάδα μέτρησης διότι'));
    break;
  case 'gks_nomoi': { 
    
    gks_admin_delete_record_has_other($id,'ma_nomos_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'destination_data_nomos_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'other_ma_nomos_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'load_nomos_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'deli_nomos_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'entity_nomos_id','gks_acc_inv_other_entity');
    gks_admin_delete_record_has_other($id,'ma_nomos_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'destination_data_nomos_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'other_ma_nomos_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'load_nomos_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'deli_nomos_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'entity_nomos_id','gks_whi_mov_other_entity');
    gks_admin_delete_record_has_other($id,'ma_nomos_id','gks_orders');
    gks_admin_delete_record_has_other($id,'destination_data_nomos_id','gks_orders');
    gks_admin_delete_record_has_other($id,'other_ma_nomos_id','gks_orders');
    gks_admin_delete_record_has_other($id,'ma_nomos_id','gks_users');
    gks_admin_delete_record_has_other($id,'ea_nomos_id','gks_users_extra_address');
    gks_admin_delete_record_has_other($id,'nomos_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'nomos_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'calendar_nomos_id','gks_calendar');
    gks_admin_delete_record_has_other($id,'company_nomos_id','gks_company');
    gks_admin_delete_record_has_other($id,'company_sub_nomos_id','gks_company_subs');
    gks_admin_delete_record_has_other($id,'warehouse_nomos_id','gks_warehouses');
    gks_admin_delete_record_has_other($id,'ma_nomos_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'other_ma_nomos_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'ruser_ma_nomos_id','gks_hotel_reservation_room');
    gks_admin_delete_record_has_other($id,'hotel_nomos_id','gks_hotel');
    gks_admin_delete_record_has_other($id,'user_ma_nomos_id','gks_hotel_folio');
    gks_admin_delete_record_has_other($id,'fuser_ma_nomos_id','gks_hotel_folio_room');
    gks_admin_delete_record_has_other($id,'doy_nomos_id','gks_doy');
    gks_admin_delete_record_has_other($id,'ma_nomos_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'other_ma_nomos_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'ruser_ma_nomos_id','gks_transfer_reservation_oximata',);
    gks_admin_delete_record_has_other($id,'user_ma_nomos_id','gks_assets_rental');
    gks_admin_delete_record_has_other($id,'fuser_ma_nomos_id','gks_assets_rental_asset');
    gks_admin_delete_record_has_other($id,'poi_nomos_id','gks_poi');
    gks_admin_delete_record_has_other($id,'transfer_nomos_id','gks_transfer');
    gks_admin_delete_record_has_other($id,'transfer_area_nomos_id','gks_transfer_area');
    gks_admin_delete_record_has_other($id,'nomos_id','gks_tk');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο νομός διότι'));
    break;}
  case 'gks_object_rel':    break;
    
  case 'gks_orders':{
    if ($row['order_state']!='010draft') {
      $error_lines[]=gks_lang('δεν είναι σε κατάσταση').' <span class="order_state_010draft">'.getOrderStateDescr('010draft').'</span>';}
        
    gks_admin_delete_record_has_other($id,'order_id','gks_acc_pay_poso_order');
    gks_admin_delete_record_has_other($id,'order_id','gks_payments');
    gks_admin_delete_record_has_other($id,'order_id','gks_payments_piraeusbank');
    gks_admin_delete_record_has_other($id,'order_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'order_id','gks_transfer_reservation');
    
    gks_admin_delete_record_has_custom($id,1016); //Paraggelia
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η παραγγελία διότι'));  

    $id_product_array=array();
    $sql="select product_id from gks_orders_products where order_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    while ($row = $result->fetch_assoc()) {
      $id_product_array[]=$row['product_id'];
    }

    $production_line_id_array=array();
    $sql="select id_production_line from gks_production_line where order_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    while ($row = $result->fetch_assoc()) {
      $production_line_id_array[]=$row['id_production_line'];
    }
    
    break;}
    
  
  
  case 'gks_orders_links':    break;
  case 'gks_orders_occasion':
    gks_admin_delete_record_has_other($id,'order_occasion_id','gks_orders');
    
    gks_admin_delete_record_has_custom($id,1028);//Peristasi
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η περίσταση διότι'));
    break;
  case 'gks_payment_acquirers':
    gks_admin_delete_record_has_other($id,'tropos_pliromis','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'tropos_pliromis','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'tropos_pliromis','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'tropos_pliromis','gks_orders');
    gks_admin_delete_record_has_other($id,'tropos_pliromis_id','gks_payments');
    gks_admin_delete_record_has_other($id,'def_tropos_pliromis','gks_pos');
    gks_admin_delete_record_has_other($id,'payment_acquirer_id','gks_acc_inv_payment');
    gks_admin_delete_record_has_other($id,'payment_acquirer_id','gks_acc_pay_payment');
    gks_admin_delete_record_has_other($id,'payment_acquirer_id','gks_eftpos_transaction');
    gks_admin_delete_record_has_other($id,'payment_acquirer_id','gks_paroxos_signature');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο τρόπος πληρωμής διότι'));
    
    break;
  case 'gks_poi':
    gks_admin_delete_record_has_other($id,'poi_parent_id','gks_poi',gks_lang('(ως γονικό)'));
    gks_admin_delete_record_has_other($id,'poi_id_from','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'poi_id_to','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'poi_id_from','gks_transfer_pricelist');
    gks_admin_delete_record_has_other($id,'poi_id_to','gks_transfer_pricelist');
    gks_admin_delete_record_has_other($id,'poi_id_from','gks_poi_diadromes');
    gks_admin_delete_record_has_other($id,'poi_id_to','gks_poi_diadromes');
    gks_admin_delete_record_has_other($id,'from_to_poi_id','gks_transfer_oxima_type_per_km');

    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο τρόπος πληρωμής διότι'));
    break;
  case 'gks_poi_diadromes':
    gks_admin_delete_record_has_other($id,'poi_diadromes_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'poi_diadromes_id','gks_transfer_pricelist');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η διαδρομή διότι'));
    break;  
  case 'gks_poi_type':
    if ($id<=10000) {$error_lines[]=gks_lang('είναι του συστήματος');}

    gks_admin_delete_record_has_other($id,'poi_type_id','gks_poi');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο τύπος σημείου ενδιαφέροντος διότι'));
    
    break;  
  case 'gks_pos':
    gks_admin_delete_record_has_other($id,'pos_id','gks_acc_inv');
   
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η εντατική λιανική διότι'));
    
    break;
  case 'gks_print_forms':
    gks_admin_delete_record_has_other($id,'pos_print_form_id','gks_pos');
    gks_admin_delete_record_has_other($id,'pos_thermal_form_id','gks_pos',gks_lang('(σε θερμικό)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_print_form_id','gks_transfer',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_print_form_id','gks_transfer',gks_lang('(ως τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_apodiji_print_form_id','gks_transfer_sub_company_details',gks_lang('(ως απόδειξη)'));
    gks_admin_delete_record_has_other($id,'transfer_parastatiko_timologio_print_form_id','gks_transfer_sub_company_details',gks_lang('(ως τιμολόγιο)'));
   
    gks_admin_delete_record_has_custom($id,1017); //Print Form
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η φόρμα εκτύπωσης διότι'));
    break;
  case 'gks_product_idiotites':
    gks_admin_delete_record_has_other($id,'idiotita_id','gks_product_idiotites_terms');
    gks_admin_delete_record_has_other($id,'product_idiotita_id','gks_eshop_products_idiotites');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η ιδιότητα διότι'));
    break;
  case 'gks_product_idiotites_terms':
    gks_admin_delete_record_has_other($id,'product_idiotita_term_id','gks_eshop_products_idiotites_terms');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο όρος ιδιότητας διότι'));
    break;
  
  case 'gks_production_bom':
    gks_admin_delete_record_has_other($id,'production_bom_id','gks_production_sintagi'); 
    
    gks_admin_delete_record_has_custom($id,1029); //Sintagi 
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η συνταγή διότι'));
    break;
  case 'gks_production_ergasies':
    gks_admin_delete_record_has_other($id,'ergasia_mustdone_id','gks_production_ergasies_mustdone');
    gks_admin_delete_record_has_other($id,'ergasia_id','gks_production_line');
    
    gks_admin_delete_record_has_custom($id,1018); //Ergasia paragogis
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η εργασία διότι'));
    break;
  case 'gks_production_ergasies_eidos':    break;
  case 'gks_production_ergasies_eidoscat':    break;

  case 'gks_production_ergasies_mustdone':    break;
    
  case 'gks_production_line': break;
  case 'gks_production_posta':{
  
    gks_admin_delete_record_has_other($id,'last_posto_id','gks_production_line');
    gks_admin_delete_record_has_other($id,'posto_id','gks_production_line_time');
    
    gks_admin_delete_record_has_custom($id,1019);//Posto ergasias
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το πόστο διότι'));
    break;}
  case 'gks_production_posta_ergasies':    break;
  case 'gks_production_posta_users':break;
  case 'gks_sms_viber_template': 
    
    break;
    
  case 'gks_sociallinks_type':
    $sql="SELECT gks_sociallinks.object_name, gks_crm_activity_objects.crm_activity_object_descr, 
    Count(gks_sociallinks.id_sociallinks) AS cc
    FROM gks_sociallinks LEFT JOIN gks_crm_activity_objects ON gks_sociallinks.object_name = gks_crm_activity_objects.crm_activity_object_code
    WHERE (((gks_sociallinks.sociallinks_type_id)=".$id."))
    GROUP BY gks_sociallinks.object_name, gks_crm_activity_objects.crm_activity_object_descr
    order by Count(gks_sociallinks.id_sociallinks) desc";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $ddd=[];
    while ($row = $result->fetch_assoc()) {
      $kkk=$row['object_name'];
      if (!empty($row['crm_activity_object_descr'])) $kkk=$row['crm_activity_object_descr'];
      $ddd[]=$kkk.' '.str_replace('[1]',$row['cc'],gks_lang('<b>[1]</b> φορές'));
    }
    if (count($ddd)>0) {
      $message=gks_lang('Δεν μπορεί να διαγραφεί ο τύπος συνδέσμων κοινωνικών δικτύων διότι έχει χρησιμοποιηθεί στα παρακάτω αντικείμενα').':<br>';
      $message.=implode('<br>',$ddd);
      debug_mail(false,'delete row',$message);
      $return = array('success' => false, 'message' => base64_encode($message));
      echo json_encode($return); die();
    }
    break;
  case 'gks_transfer':
    gks_admin_delete_record_has_other($id,'transfer_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'transfer_id','gks_transfer_sub_company_details');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το κανάλι Transfer διότι'));
    
    break; 
  case 'gks_transfer_area':
    gks_admin_delete_record_has_other($id,'transfer_area_parent_id','gks_transfer_area',gks_lang('(ως γονική)'));
    gks_admin_delete_record_has_other($id,'transfer_area_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η περιοχή διότι'));
    break;  
  case 'gks_transfer_area2poi':
    break;
  case 'gks_transfer_area2transfer':
    break;
  case 'gks_transfer_area2externalpartner':
    break;
  case 'gks_transfer_oxima_type':
    gks_admin_delete_record_has_other($id,'transfer_oxima_type_id','gks_transfer_pricelist');
    gks_admin_delete_record_has_other($id,'transfer_oxima_type_id','gks_transfer_reservation_oximata');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί ο τύπος οχήματος διότι'));
    break;

  case 'gks_transfer_oxima2type2transfer':
    break;
    
    
    
  case 'gks_transfer_pricelist':
    
    break;
  case 'gks_transfer_reservation':
    if ($row['transfer_reservation_status']!='010draft') {
      $error_lines[]=gks_lang('δεν είναι σε κατάσταση').' <span class="transfer_reservation_status_010draft">'.getTransferReservationStatusDescr('010draft').'</span>';}
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η κράτηση transfer διότι'));
    break;

  case 'gks_urlshort':
    gks_admin_delete_record_has_other($id,'urlshort_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'urlshort_id','gks_urlshort_hit');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το μικρό URL διότι'));
    break;
  case 'gks_urlshort_hit': 
    gks_admin_delete_record_has_other($id,'urlshort_hit_id','gks_crm_leads');
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η καταγραφή Μικρού URL διότι'));
    break;

  case 'gks_users_favorites':    break;
  case 'gks_users_groups':
    gks_admin_delete_record_has_other($id,'group_parent_id','gks_users_groups',gks_lang('(γονική)'));
    gks_admin_delete_record_has_other($id,'group_id','gks_users_groups_users');
    
    gks_admin_delete_record_has_custom($id,1020);//Omada epafon
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η ομάδα διότι'));
    break;
  case 'gks_users_groups_users':{
    $group_id = $row['group_id'];
    $user_id = $row['user_id'];
    $is_omadarxis = $row['is_omadarxis'];
    break;}

  case 'gks_users_templates':    break;

  case 'gks_warehouses':
    gks_admin_delete_record_has_other($id,'asset_last_warehouse_id','gks_assets');
    gks_admin_delete_record_has_other($id,'last_action_warehouse_id','gks_assets',gks_lang('(τελευταία κίνηση)'));
    gks_admin_delete_record_has_other($id,'warehouse_id','gks_assets_moves');
    gks_admin_delete_record_has_other($id,'warehouse_id','gks_assets_service');
    gks_admin_delete_record_has_other($id,'warehouse_id','gks_assets_whi_mov');
    gks_admin_delete_record_has_other($id,'warehouse_id','gks_transfer_area');
    gks_admin_delete_record_has_other($id,'warehouses_id_from','gks_acc_inv',gks_lang('(ως από)'));
    gks_admin_delete_record_has_other($id,'warehouses_id_to','gks_acc_inv',gks_lang('(ως προς)'));
    gks_admin_delete_record_has_other($id,'p_warehouses_id_from','gks_acc_inv_products',gks_lang('(ως από)'));
    gks_admin_delete_record_has_other($id,'p_warehouses_id_to','gks_acc_inv_products',gks_lang('(ως προς)'));
    gks_admin_delete_record_has_other($id,'warehouses_id_from','gks_eshops',gks_lang('(για απόδειξη)'));
    gks_admin_delete_record_has_other($id,'warehouses_id_from_tim','gks_eshops',gks_lang('(για τιμολόγιο)'));
    gks_admin_delete_record_has_other($id,'warehouses_id_from','gks_orders',gks_lang('(ως από)'));
    gks_admin_delete_record_has_other($id,'warehouses_id_to','gks_orders',gks_lang('(ως προς)'));
    gks_admin_delete_record_has_other($id,'prod_warehouses_id_from','gks_orders',gks_lang('(παραγωγή ως από)'));
    gks_admin_delete_record_has_other($id,'prod_warehouses_id_to','gks_orders',gks_lang('(παραγωγή ως προς)'));
    gks_admin_delete_record_has_other($id,'p_warehouses_id_from','gks_orders_products',gks_lang('(ως από)'));
    gks_admin_delete_record_has_other($id,'p_warehouses_id_to','gks_orders_products',gks_lang('(ως προς)'));
    gks_admin_delete_record_has_other($id,'pos_warehouses_id_from','gks_pos',gks_lang('(ως από)'));
    gks_admin_delete_record_has_other($id,'pos_warehouses_id_to','gks_pos',gks_lang('(ως προς)'));
    gks_admin_delete_record_has_other($id,'sp_warehouses_id_from','gks_production_sintagi_product');
    gks_admin_delete_record_has_other($id,'warehouses_id_from','gks_whi_mov',gks_lang('(ως από)'));
    gks_admin_delete_record_has_other($id,'warehouses_id_to','gks_whi_mov',gks_lang('(ως προς)'));
    gks_admin_delete_record_has_other($id,'p_warehouses_id_from','gks_whi_mov_products',gks_lang('(ως από)'));
    gks_admin_delete_record_has_other($id,'p_warehouses_id_to','gks_whi_mov_products',gks_lang('(ως προς)'));
    
    gks_admin_delete_record_has_custom($id,1021);//Apothiki
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η αποθήκη διότι'));
    break;
  case 'gks_whi_mov':{
    $inv_state=trim_gks($row['mov_state']);
    if ($inv_state!='010draft') {
      $error_lines[]=gks_lang('δεν είναι σε κατάσταση').' <span class="whi_mov_state_010draft">'.getWhiMovStateDescr('010draft').'</span>';}
    
    gks_admin_delete_record_has_other($id,'cancel_for_whi_mov_id','gks_whi_mov',gks_lang('(ως ακυρωτικό)'));
    gks_admin_delete_record_has_other($id,'credit_memo_for_whi_mov_id','gks_whi_mov',gks_lang('(ως επιστροφή)'));
    gks_admin_delete_record_has_other($id,'coi_whi_mov_id','gks_acc_inv_correlated_invoices');
    gks_admin_delete_record_has_other($id,'coi_whi_mov_id','gks_acc_pay_correlated_invoices');
    gks_admin_delete_record_has_other($id,'coi_whi_mov_id','gks_whi_mov_correlated_invoices');
    gks_admin_delete_record_has_other($id,'mcm_whi_mov_id','gks_acc_inv_multiple_connected_marks');
    gks_admin_delete_record_has_other($id,'mcm_whi_mov_id','gks_acc_pay_multiple_connected_marks');
    gks_admin_delete_record_has_other($id,'mcm_whi_mov_id','gks_whi_mov_multiple_connected_marks');
    
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί το δελτίο διότι'));
    
    break;}
    
    
  case 'gks_whi_mov_links':    break;
    
  case 'gks_woo_brands':    break;
  case 'gks_woo_categories':    break;
  case 'gks_woo_coupons':    break;
  case 'gks_woo_product':    break;

  case 'wp_users':
    gks_admin_delete_record_has_other($id,'user_id','gks_acc_inv');
    gks_admin_delete_record_has_other($id,'aade_user_id','gks_acc_inv',gks_lang('(ως αποστολέας ΑΑΔΕ)'));
    gks_admin_delete_record_has_other($id,'print_user_id','gks_acc_inv',gks_lang('(ως χρήστης εκτύπωσης)'));
    gks_admin_delete_record_has_other($id,'user_id','gks_acc_inv_links');
    gks_admin_delete_record_has_other($id,'user_id','gks_acc_inv_messages');
    gks_admin_delete_record_has_other($id,'entity_user_id','gks_acc_inv_other_entity');
    gks_admin_delete_record_has_other($id,'user_id','gks_acc_pay');
    gks_admin_delete_record_has_other($id,'aade_user_id','gks_acc_pay',gks_lang('(ως αποστολέας ΑΑΔΕ)'));
    gks_admin_delete_record_has_other($id,'print_user_id','gks_acc_pay',gks_lang('(ως χρήστης εκτύπωσης)'));
    gks_admin_delete_record_has_other($id,'user_id','gks_acc_pay_links');
    gks_admin_delete_record_has_other($id,'user_id','gks_acc_pay_messages');
    gks_admin_delete_record_has_other($id,'asset_last_user_id','gks_assets');
    gks_admin_delete_record_has_other($id,'user_id_add','gks_assets_rental');
    gks_admin_delete_record_has_other($id,'fuser_id','gks_assets_rental_asset');
    gks_admin_delete_record_has_other($id,'user_id','gks_bank_accounts');
    gks_admin_delete_record_has_other($id,'user_id','gks_bank_transactions');
    gks_admin_delete_record_has_other($id,'calendar_user_id','gks_calendar');
    gks_admin_delete_record_has_other($id,'company_related_user_id','gks_company',gks_lang('(ως σχετική επαφή)'));
    gks_admin_delete_record_has_other($id,'user_id','gks_company_users');
    gks_admin_delete_record_has_other($id,'activity_user_id','gks_crm_activity');
    gks_admin_delete_record_has_other($id,'user_id','gks_crm_leads');
    gks_admin_delete_record_has_other($id,'user_id','gks_crm_leads_links');
    gks_admin_delete_record_has_other($id,'user_id','gks_crm_leads_messages');
    gks_admin_delete_record_has_other($id,'crm_machine_user_id','gks_crm_machine');
    gks_admin_delete_record_has_other($id,'user_id','gks_crm_machine_messages');
    gks_admin_delete_record_has_other($id,'user_id','gks_crm_tasks');
    gks_admin_delete_record_has_other($id,'print_user_id','gks_crm_tasks',gks_lang('(ως χρήστης εκτύπωσης)'));
    gks_admin_delete_record_has_other($id,'user_id','gks_crm_tasks_links');
    gks_admin_delete_record_has_other($id,'user_id','gks_crm_tasks_messages');
    gks_admin_delete_record_has_other($id,'user_id','gks_hotel_folio');
    gks_admin_delete_record_has_other($id,'fuser_id','gks_hotel_folio_room');
    gks_admin_delete_record_has_other($id,'user_id','gks_hotel_reservation');
    gks_admin_delete_record_has_other($id,'print_user_id','gks_hotel_reservation',gks_lang('(ως χρήστης εκτύπωσης)'));
    gks_admin_delete_record_has_other($id,'user_id','gks_hotel_reservation_links');
    gks_admin_delete_record_has_other($id,'user_id','gks_hotel_reservation_messages');
    gks_admin_delete_record_has_other($id,'ruser_id','gks_hotel_reservation_room');
    gks_admin_delete_record_has_other($id,'user_id_maker','gks_hr_interview',gks_lang('(ως συνεντευξιάζων)'));
    gks_admin_delete_record_has_other($id,'candidate_id','gks_hr_user');
    gks_admin_delete_record_has_other($id,'hr_user_id','gks_hr_user_cvs');
    gks_admin_delete_record_has_other($id,'xeiristis_id','gks_mellon_transaction');
    gks_admin_delete_record_has_other($id,'user_id','gks_orders');
    gks_admin_delete_record_has_other($id,'print_user_id','gks_orders',gks_lang('(ως χρήστης εκτύπωσης)'));
    gks_admin_delete_record_has_other($id,'user_id','gks_orders_links');
    gks_admin_delete_record_has_other($id,'user_id','gks_orders_messages');
    gks_admin_delete_record_has_other($id,'user_id','gks_orders_occasion');
    gks_admin_delete_record_has_other($id,'other_user_id','gks_payments_bank');
    gks_admin_delete_record_has_other($id,'last_user_id_production','gks_production_line');
    gks_admin_delete_record_has_other($id,'user_id','gks_production_line_time');
    gks_admin_delete_record_has_other($id,'transfer_user_id','gks_transfer_area2externalpartner');
    gks_admin_delete_record_has_other($id,'user_id','gks_transfer_reservation');
    gks_admin_delete_record_has_other($id,'print_user_id','gks_transfer_reservation',gks_lang('(ως χρήστης εκτύπωσης)'));
    gks_admin_delete_record_has_other($id,'user_id','gks_transfer_reservation_links');
    gks_admin_delete_record_has_other($id,'user_id','gks_transfer_reservation_messages');
    gks_admin_delete_record_has_other($id,'ruser_id','gks_transfer_reservation_oximata');
    gks_admin_delete_record_has_other($id,'user_id','gks_users_extra_address');
    gks_admin_delete_record_has_other($id,'user_id','gks_users_vacation');
    gks_admin_delete_record_has_other($id,'user_id','gks_whi_mov');
    gks_admin_delete_record_has_other($id,'aade_user_id','gks_whi_mov',gks_lang('(ως αποστολέας ΑΑΔΕ)'));
    gks_admin_delete_record_has_other($id,'print_user_id','gks_whi_mov',gks_lang('(ως χρήστης εκτύπωσης)'));
    gks_admin_delete_record_has_other($id,'user_id','gks_whi_mov_links');
    gks_admin_delete_record_has_other($id,'user_id','gks_whi_mov_messages');
    gks_admin_delete_record_has_other($id,'entity_user_id','gks_whi_mov_other_entity');
    gks_admin_delete_record_has_other($id,'transfer_oxima_driver_id','gks_transfer_reservation_oximata',gks_lang('(ως οδηγός transfer)'));
    gks_admin_delete_record_has_other($id,'externalpartner_id','gks_transfer_reservation_oximata',gks_lang('(ως εξωτερικός συνεργάτης transfer)'));
    gks_admin_delete_record_has_other($id,'erp_app_mobile_user_id','gks_erp_app_mobile');
    gks_admin_delete_record_has_other($id,'responsible_id','gks_transfer_area');
    gks_admin_delete_record_has_other($id,'user_id','gks_barcodes');

    
    gks_admin_delete_record_has_custom($id,1022);//Epafi
    gks_admin_delete_record_has_custom($id,1222,true);//Epafes
    
    
    
    gks_admin_delete_record_has_other_last(gks_lang('Δεν μπορεί να διαγραφεί η επαφή διότι'));
    break;
	case 'system_file':    break;
  default:
    $has_error=true;
    if (startwith($mymodel,'gks_ct_')) { // einai gks_custom_table
      $has_error=false;
    }
    
    if ($has_error) {
      debug_mail(false,'error on mymodel name (2)','');
      $return = array('success' => false, 'message' => base64_encode('error on mymodel name (2).'));
      echo json_encode($return); die();  
    }
}

//$return = array('success' => false, 'message' => base64_encode('aaaaaaaaaa'));
//echo json_encode($return); die();

$sql="select id_crm_activity from gks_crm_activity where activity_model='".$db_link->escape_string($mymodel)."' and activity_model_id=".$id;
//echo '<pre>'.$sql;die();
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
if ($result->num_rows > 0) {
  $message=str_replace('[1]',$result->num_rows,gks_lang('Δεν μπορεί να διαγραφεί το αντικείμενο διότι έχει <b>[1]</b> δραστηριότητες'));
  //debug_mail(false,'delete row',                                 $message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();  }

  

$sql='';
//delete
switch ($mymodel) {  
  case 'gks_aade_skopos_diakinisis':
    $sql="delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=".$id; break;
  case 'gks_aade_katigoria_fpa_ejeresi':
    $sql="delete from gks_aade_katigoria_fpa_ejeresi where id_aade_katigoria_fpa_ejeresi=".$id; break;
  case 'gks_aade_katigoria_parakratoumemenon_foron':
    $sql="delete from gks_aade_katigoria_parakratoumemenon_foron where id_aade_katigoria_parakratoumemenon_foron=".$id; break;
  case 'gks_aade_katigoria_loipon_foron':
    $sql="delete from gks_aade_katigoria_loipon_foron where id_aade_katigoria_loipon_foron=".$id; break;
  case 'gks_aade_katigoria_xartosimou':
    $sql="delete from gks_aade_katigoria_xartosimou where id_aade_katigoria_xartosimou=".$id; break;
  case 'gks_aade_katigoria_telon':
    $sql="delete from gks_aade_katigoria_telon where id_aade_katigoria_telon=".$id; break;
  case 'gks_acc_eidi_parastatikon':
    $sql="delete from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou=".$id; break;
  case 'gks_acc_inv':
    $sql="delete from gks_acc_inv where id_acc_inv=".$id; break;
  case 'gks_acc_inv_links':
    $sql="delete from gks_acc_inv_links where id_acc_inv_links=".$id; break;
  case 'gks_acc_pay':
    $sql="delete from gks_acc_pay where id_acc_pay=".$id; break;
  case 'gks_acc_pay_links':
    $sql="delete from gks_acc_pay_links where id_acc_pay_links=".$id; break;
  case 'gks_acc_journal':
     $sql="delete from gks_acc_journal where id_acc_journal=".$id; break;
  case 'gks_acc_seires':
     $sql="delete from gks_acc_seires where id_acc_seira=".$id; break;
  case 'gks_ads_campain': 
     $sql="delete from gks_ads_campain where id_ads_campain=".$id; break;
  case 'gks_airline':
     $sql="delete from gks_airline where id_airline=".$id; break;
  case 'gks_assets':
     $sql="delete from gks_assets where id_asset=".$id; break;
  case 'gks_assets_service':
     $sql="delete from gks_assets_service where id_assets_service=".$id; break;
  case 'gks_assets_service_reasons':
     $sql="delete from gks_assets_service_reasons where id_assets_service_reasons=".$id; break;
  case 'gks_assets_type':
     $sql="delete from gks_assets_type where id_asset_type=".$id; break;
  case 'gks_assets_whi_mov':
     $sql="delete from gks_assets_whi_mov where id_assets_whi_mov=".$id; break;
  case 'gks_bank_accounts':
    $sql="delete from gks_bank_accounts where id_bank_account=".$id; break;
  case 'gks_banks':
    $sql="delete from gks_banks where id_bank=".$id; break;
  case 'gks_barcodes':
    $sql="delete from gks_barcodes where id_barcode=".$id; break;
  case 'gks_calendar':
     $sql="delete from gks_calendar where id_calendar=".$id; break;
  case 'gks_company':  
     $sql="delete from gks_company where id_company=".$id; break;
  case 'gks_company_subs':
     $sql="delete from gks_company_subs where id_company_sub=".$id; break;
  case 'gks_company_users':     
    $sql="delete from gks_company_users where id_company_users=".$id; break;
  case 'gks_country':
    $sql="delete from gks_country where id_country=".$id; break;
  case 'gks_crons':    
    $sql="delete from gks_crons where id_cron=".$id; break;
  case 'gks_crm_activity':
    $sql="delete from gks_crm_activity where id_crm_activity=".$id; break;
  case 'gks_crm_channel_sale':
    $sql="delete from gks_crm_channel_sale where id_crm_channel_sale=".$id; break;
  case 'gks_crm_leads':
    $sql="delete from gks_crm_leads where id_crm_lead=".$id; break;
  case 'gks_crm_leads_links':
    $sql="delete from gks_crm_leads_links where id_crm_leads_links=".$id; break;
  case 'gks_crm_leads_status':
    $sql="delete from gks_crm_leads_status where id_crm_lead_status=".$id; break;
  case 'gks_crm_machine':
    $sql="delete from gks_crm_machine where id_crm_machine=".$id; break;
  case 'gks_crm_tasks':
    $sql="delete from gks_crm_tasks where id_crm_task=".$id; break;
  case 'gks_crm_tasks_employee':
    $sql="delete from gks_crm_tasks_employee where id_crm_task_employee=".$id; break;
  case 'gks_crm_tasks_links':
    $sql="delete from gks_crm_tasks_links where id_crm_tasks_links=".$id; break;
  case 'gks_crm_tasks_machine':
    $sql="delete from gks_crm_tasks_machine where id_crm_task_machine=".$id; break;
  case 'gks_crm_tasks_status':
    $sql="delete from gks_crm_tasks_status where id_crm_task_status=".$id; break;
  case 'gks_delivery_methods':
    $sql="delete from gks_delivery_methods where id_delivery_method=".$id; break;
  case 'gks_email_template':
    $sql="delete from gks_email_template where id_email_template=".$id; break;
  case 'gks_erp_app':
    $sql="delete from gks_erp_app where id_erp_app=".$id; break;
  case 'gks_erp_app_mobile':
    $sql="delete from gks_erp_app_mobile where id_erp_app_mobile=".$id; break;
  case 'gks_eshop_fiscal_position':
    $sql="delete from gks_eshop_fiscal_position where id_fiscal_position=".$id; break;
  case 'gks_eshop_pricelist':
    $sql="delete from gks_eshop_pricelist where id_pricelist=".$id; break;
  case 'gks_eshop_pricelist_items':  
    $sql="delete from gks_eshop_pricelist_items where id_pricelist_item=".$id; break;
  case 'gks_eshop_pricelist_items_products':
    $sql="delete from gks_eshop_pricelist_items_products where id_pricelist_item_product=".$id; break;
  case 'gks_eshop_pricelist_items_categories':
    $sql="delete from gks_eshop_pricelist_items_categories where id_pricelist_item_category=".$id; break;
  case 'gks_eshop_pricelist_items_brands':
    $sql="delete from gks_eshop_pricelist_items_brands where id_pricelist_item_brand=".$id; break;

  case 'gks_eshop_product_lots':
    $sql="delete from gks_eshop_product_lots where id_lot_product=".$id; break;
  case 'gks_eshop_products':
    $sql="delete from gks_eshop_products where id_product=".$id." or product_parent_id=".$id; break;
  case 'gks_eshop_products_brands':
    $sql="delete from gks_eshop_products_brands where id_product_brand=".$id; break;
  case 'gks_eshop_products_categories':
    $sql="delete from gks_eshop_products_categories where id_product_category=".$id; break;
  case 'gks_eshop_products_brands_products':
    $sql="delete from gks_eshop_products_brands_products where id_eshop_products_brands_products=".$id; break;
  case 'gks_eshop_products_categories_products':
    $sql="delete from gks_eshop_products_categories_products where id_eshop_products_categories_products=".$id; break;

  case 'gks_eshops':
    $sql="delete from gks_eshops where id_eshop=".$id; break;
  case 'gks_gsis_check':
    $sql="delete from gks_gsis_check where id_gsis_check=".$id; break;
  case 'gks_vies_check':
    $sql="delete from gks_vies_check where id_vies_check=".$id; break;
  case 'gks_voip_favorites':
    $sql="delete from gks_voip_favorites where id_voip_favorite=".$id." and user_id=".$my_wp_user_id; break;
  case 'gks_hotel':
    $sql="delete from gks_hotel where id_hotel=".$id; break;
  case 'gks_hotel_availability':
    $sql="delete from gks_hotel_availability where id_hotel_availability=".$id; break;
  case 'gks_hotel_floor':
    $sql="delete from gks_hotel_floor where id_hotel_floor=".$id; break;
  case 'gks_hotel_price':
    $sql="delete from gks_hotel_price where id_hotel_price=".$id; break;
  case 'gks_hotel_reservation':    
    $sql="delete from gks_hotel_reservation where id_hotel_reservation=".$id; break;
  case 'gks_hotel_reservation_links':    
    $sql="delete from gks_hotel_reservation_links where id_hotel_reservation_links=".$id; break;
    
  case 'gks_hotel_room':
    $sql="delete from gks_hotel_room where id_hotel_room=".$id; break;
  case 'gks_hotel_room_type':
    $sql="delete from gks_hotel_room_type where id_hotel_room_type=".$id; break;

  
  case 'gks_lang':
    $sql="delete from gks_lang where idd_lang=".$id; break;
  case 'gks_mass_messages':
    $sql="delete from gks_mass_messages where id_mass_message=".$id; break;
  case 'gks_monades_metrisis':
    $sql="delete from gks_monades_metrisis where id_monada=".$id; break;
  case 'gks_nomoi':    
    $sql="delete from gks_nomoi where id_nomos=".$id; break;

  case 'gks_object_rel':
    $sql="delete from gks_object_rel where id_object_rel=".$id; break;
 
  
  case 'gks_orders':
    $sql="delete from gks_orders where id_order=".$id; break;
  case 'gks_orders_links':
    $sql="delete from gks_orders_links where id_order_links=".$id; break;
  case 'gks_orders_occasion':
    $sql="delete from gks_orders_occasion where id_order_occasion=".$id; break;
  case 'gks_payment_acquirers':
    $sql="delete from gks_payment_acquirers where id_payment_acquirer=".$id; break;
  case 'gks_poi':
    $sql="delete from gks_poi where id_poi=".$id; break;
  case 'gks_poi_diadromes':    
    $sql="delete from gks_poi_diadromes where id_poi_diadromes=".$id; break;
    
  case 'gks_poi_type':  
    $sql="delete from gks_poi_type where id_poi_type=".$id; break;
  case 'gks_pos':
    $sql="delete from gks_pos where id_pos=".$id; break;
  case 'gks_print_forms':
    $sql="delete from gks_print_forms where id_print_form=".$id; break;
  case 'gks_product_idiotites':
    $sql="delete from gks_product_idiotites where id_product_idiotita=".$id; break;
  case 'gks_product_idiotites_terms':
    $sql="delete from gks_product_idiotites_terms where id_product_idiotita_term=".$id; break;
    
  case 'gks_production_bom':  
    $sql="delete from gks_production_bom where id_production_bom=".$id; break;
  case 'gks_production_ergasies':  
    $sql="delete from gks_production_ergasies where id_production_ergasia=".$id; break;
  case 'gks_production_ergasies_eidos':
    $sql="delete from gks_production_ergasies_eidos where id_production_ergasies_eidos=".$id; break;
  case 'gks_production_ergasies_eidoscat':
    $sql="delete from gks_production_ergasies_eidoscat where id_production_ergasies_eidoscat=".$id; break;
  case 'gks_production_ergasies_mustdone':
    $sql="delete from gks_production_ergasies_mustdone where id_production_ergasia_mustdone=".$id; break;

  case 'gks_production_line':
    $sql="delete from gks_production_line where id_production_line=".$id; break;
  case 'gks_production_posta':
    $sql="delete from gks_production_posta where id_production_posto=".$id; break;
  case 'gks_production_posta_ergasies':
    $sql="delete from gks_production_posta_ergasies where id_production_posta_ergasies=".$id; break;
  case 'gks_production_posta_users':
    $sql="delete from gks_production_posta_users where id_production_posto_user=".$id; break;
  case 'gks_sms_viber_template':
    $sql="delete from gks_sms_viber_template where id_sms_viber_template=".$id; break;
  case 'gks_sociallinks_type':
    $sql="delete from gks_sociallinks_type where id_sociallinks_type=".$id; break;
  case 'gks_transfer':
    $sql="delete from gks_transfer where id_transfer=".$id; break;
  case 'gks_transfer_area':
    $sql="delete from gks_transfer_area where id_transfer_area=".$id; break;
 
  case 'gks_transfer_area2poi':
    $sql="delete from gks_transfer_area2poi where id_transfer_area2poi=".$id; break;
  case 'gks_transfer_area2transfer':
    $sql="delete from gks_transfer_area2transfer where id_transfer_area2transfer=".$id; break;
  case 'gks_transfer_area2externalpartner':
    $sql="delete from gks_transfer_area2externalpartner where id_transfer_area2externalpartner=".$id; break;

  case 'gks_transfer_oxima_type':
    $sql="delete from gks_transfer_oxima_type where id_transfer_oxima_type=".$id; break;
  case 'gks_transfer_oxima2type2transfer':
    $sql="delete from gks_transfer_oxima2type2transfer where id_transfer_oxima2type2transfer=".$id; break;
    
  case 'gks_transfer_pricelist':
    $sql="delete from gks_transfer_pricelist where id_transfer_pricelist=".$id; break;

  case 'gks_transfer_reservation':
    $sql="delete from gks_transfer_reservation where id_transfer_reservation=".$id; break;
  case 'gks_urlshort':
    $sql="delete from gks_urlshort where id_urlshort=".$id; break;
  case 'gks_urlshort_hit':
    $sql="delete from gks_urlshort_hit where id_urlshort_hit=".$id; break;    
  case 'gks_users_favorites':
    $sql="delete from gks_users_favorites where user_id=".$my_wp_user_id." and id_favorites=".$id; break;
  case 'gks_users_groups':
    $sql="delete from gks_users_groups where id_users_group=".$id; break;
  case 'gks_users_groups_users':
    $sql="delete from gks_users_groups_users where id_users_groups_users=".$id; break;
  case 'gks_users_templates':
    $sql="delete from gks_users_templates where id_users_template=".$id." and user_id=".$my_wp_user_id; break;

  case 'gks_warehouses':
    $sql="delete from gks_warehouses where id_warehouse=".$id; break;
  case 'gks_whi_mov':
    $sql="delete from gks_whi_mov where id_whi_mov=".$id; break;
  case 'gks_whi_mov_links':
    $sql="delete from gks_whi_mov_links where id_whi_mov_links=".$id; break;

  case 'gks_woo_brands':
    $sql="delete from gks_woo_brands where id_woo_brand=".$id; break;
  case 'gks_woo_categories':
    $sql="delete from gks_woo_categories where id_woo_category=".$id; break;
  case 'gks_woo_coupons':
    $sql="delete from gks_woo_coupons where id_woo_coupon=".$id; break;
  case 'gks_woo_product':
    $sql="delete from gks_woo_product where id_woo_product=".$id; break;

  case 'wp_users':
    $sql="delete  from ".GKS_WP_TABLE_PREFIX."users where ID=".$id; break;
	case 'system_file':
    $sql='';
    break;

  default:
    $has_error=true;
    if (startwith($mymodel,'gks_ct_')) { // einai gks_custom_table
      $sql="delete from gks_customt_gks_ct_".$id_custom_table." where id_gks_customt_gks_ct_".$id_custom_table."=".$id;
      
      $has_error=false;
    }
    
    if ($has_error) {  
      debug_mail(false,'error on mymodel name (3)','');
      $return = array('success' => false, 'message' => base64_encode('error on mymodel name (3).'));
      echo json_encode($return); die();  
    }
}
if ($sql!='') {
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
}




//after 
switch ($mymodel) { 
  case 'gks_aade_skopos_diakinisis':
    gks_admin_delete_record_after($id,'aade_skopos_diakinisis_id','gks_aade_skopos_diakinisis_lang');
    break;
  case 'gks_aade_katigoria_fpa_ejeresi':
    gks_admin_delete_record_after($id,'aade_katigoria_fpa_ejeresi_id','gks_aade_katigoria_fpa_ejeresi_lang');
    break;
  case 'gks_aade_katigoria_parakratoumemenon_foron':
    gks_admin_delete_record_after($id,'aade_katigoria_parakratoumemenon_foron_id','gks_aade_katigoria_parakratoumemenon_foron_lang');
    break;
  case 'gks_aade_katigoria_loipon_foron':
    gks_admin_delete_record_after($id,'aade_katigoria_loipon_foron_id','gks_aade_katigoria_loipon_foron_lang');
    break;
  case 'gks_aade_katigoria_xartosimou':
    gks_admin_delete_record_after($id,'aade_katigoria_xartosimou_id','gks_aade_katigoria_xartosimou_lang');
    break;
  case 'gks_aade_katigoria_telon':
    gks_admin_delete_record_after($id,'aade_katigoria_telon_id','gks_aade_katigoria_telon_lang');
    break;
  case 'gks_acc_eidi_parastatikon':
    gks_admin_delete_record_after($id,'acc_eidos_parastatikou_id','gks_acc_eidi_parastatikon_lang');
    break;
  case 'gks_acc_inv': {
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_inv_links');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_inv_log');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_inv_photo');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_inv_messages');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_inv_products');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_inv_other_entity');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_inv_payment');
    
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_inv_correlated_invoices');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_inv_multiple_connected_marks');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_acc_seires_auto_numbers');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_company_eftpos_log');
    gks_admin_delete_record_after($id,'acc_inv_id','gks_company_paroxos_log');
      
      
    $sql="delete from gks_acc_inv_products_expenses where acc_inv_product_id in(select id_acc_inv_product from gks_acc_inv_products where acc_inv_id=".$id.")";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    $sql="delete from gks_acc_inv_products_income where acc_inv_product_id in(select id_acc_inv_product from gks_acc_inv_products where acc_inv_id=".$id.")";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    
    
    $sql="update gks_users_extra_address set acc_inv_id=0 where acc_inv_id=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}     
    
    $sql="delete from gks_users_templates where object_name='gks_acc_inv' and user_id=".$my_wp_user_id." and template_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($db_link->affected_rows > 0) {
      gks_cache_update_menu_version();
    }
    
    break;}
  case 'gks_acc_inv_links':    break;
  

  case 'gks_acc_pay': 
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_links');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_log');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_photo');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_messages');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_method');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_correlated_invoices');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_multiple_connected_marks');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_payment');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_poso_acc_inv');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_poso_hotel_reservation');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_pay_poso_order');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_acc_seires_auto_numbers');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_company_eftpos_log');
    gks_admin_delete_record_after($id,'acc_pay_id','gks_company_paroxos_log');
  
   
    
    $sql="delete from gks_users_templates where object_name='gks_acc_pay' and user_id=".$my_wp_user_id." and template_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($db_link->affected_rows > 0) {
      gks_cache_update_menu_version();
    }

    
    break;
  case 'gks_acc_pay_links':    break;

  case 'gks_acc_journal': 
    gks_admin_delete_record_after($id,'acc_journal_id','gks_acc_journal_lang');
    gks_admin_delete_record_after($id,'acc_journal_id','gks_acc_journal_photo');
    break;
  case 'gks_acc_seires':
    gks_admin_delete_record_after($id,'acc_seira_id','gks_acc_seires_lang');
    gks_admin_delete_record_after($id,'acc_seira_id','gks_acc_seires_photo');
    gks_admin_delete_record_after($id,'acc_seira_id','gks_acc_seires_auto_numbers');
    gks_admin_delete_record_after($id,'acc_seira_id','gks_acc_seires_paymentacquirers');
    break;
  case 'gks_ads_campain':
    gks_admin_delete_record_after($id,'ads_campain_id','gks_ads_campain_photo');
    
    
    break;
  case 'gks_airline':
    gks_admin_delete_record_after($id,'airline_id','gks_flights');
    gks_admin_delete_record_after($id,'airline_id','gks_flights_routes');
    break;
  case 'gks_assets':
    gks_admin_delete_record_after($id,'asset_id','gks_assets_lang');
    gks_admin_delete_record_after($id,'asset_id','gks_assets_log');
    gks_admin_delete_record_after($id,'asset_id','gks_assets_oximata_km');
    gks_admin_delete_record_after($id,'asset_id','gks_assets_photo');
    gks_admin_delete_record_after($id,'asset_id','gks_assets_rental_asset_day');
    gks_admin_delete_record_after($id,'asset_id','gks_assets_rental_availability');
    gks_admin_delete_record_after($id,'asset_id','gks_assets_rental_availability_day');
    gks_admin_delete_record_after($id,'asset_id','gks_transfer_oxima2type2transfer');
    gks_admin_delete_record_after($id,'asset_id','gks_transfer_reservation_oximata_day');
    
    break;
  case 'gks_assets_service':
    gks_admin_delete_record_after($id,'assets_service_id','gks_assets_service_photo');
    break;
  case 'gks_assets_service_reasons':
    gks_admin_delete_record_after($id,'assets_service_reasons_id','gks_assets_service_reasons_photo');
    gks_admin_delete_record_after($id,'reasons_id','gks_assets_service_reasons_types');
    
    break;
  case 'gks_assets_type':
    gks_admin_delete_record_after($id,'asset_type_id','gks_assets_rental_availability');
    gks_admin_delete_record_after($id,'asset_type_id','gks_assets_rental_availability_day');
    gks_admin_delete_record_after($id,'asset_type_id','gks_assets_rental_price');
    gks_admin_delete_record_after($id,'asset_type_id','gks_assets_rental_price_day');
    gks_admin_delete_record_after($id,'asset_type_id','gks_assets_type_photo');
    gks_admin_delete_record_after($id,'type_id','gks_assets_service_reasons_types');
  
    break;
  case 'gks_assets_whi_mov':    
    gks_admin_delete_record_after($id,'assets_whi_mov_id','gks_assets_whi_mov_assets');
    gks_admin_delete_record_after($id,'assets_whi_mov_id','gks_assets_whi_mov_photo');
    
    break;
  case 'gks_bank_accounts':
    gks_admin_delete_record_after($id,'bank_account_id','gks_bank_accounts_photo');
    
    break;
  case 'gks_banks':
    gks_admin_delete_record_after($id,'bank_id','gks_banks_lang');
    break;
  case 'gks_barcodes': break;  
  case 'gks_calendar':    
    gks_delete_gks_calendar_after($id,$calendar_user_id,$uri);    
    break;
    
  case 'gks_company':     
    gks_admin_delete_record_after($id,'company_id','gks_company_lang');
    gks_admin_delete_record_after($id,'company_id','gks_log_company_users');
    gks_admin_delete_record_after($id,'company_id','gks_company_eftpos');
    gks_admin_delete_record_after($id,'company_id','gks_company_paroxos');
    gks_admin_delete_record_after($id,'company_id','gks_company_photo');
    gks_admin_delete_record_after($id,'company_id','gks_users_protypdays');
    gks_admin_delete_record_after($id,'company_id','gks_company_basefpa');
    gks_admin_delete_record_after($id,'company_id','gks_company_fpa');
    
    
    break;  
  case 'gks_company_subs':     
    gks_admin_delete_record_after($id,'company_sub_id','gks_company_subs_lang');
    gks_admin_delete_record_after($id,'company_sub_id','gks_log_company_users');
    gks_admin_delete_record_after($id,'company_sub_id','gks_company_eftpos');
    gks_admin_delete_record_after($id,'company_sub_id','gks_company_paroxos');
    gks_admin_delete_record_after($id,'company_sub_id','gks_company_subs_photo');
    gks_admin_delete_record_after($id,'company_sub_id','gks_company_subs_basefpa');
    gks_admin_delete_record_after($id,'company_sub_id','gks_company_subs_fpa');
    
      
  
    break;  
  case 'gks_company_users':    
    $exit_date='null';
    if (isset($_POST['exit_date'])) {
      if ($_POST['exit_date'] == '__/__/____ __:__') $_POST['exit_date']='';
      if ($_POST['exit_date'] == '__/__/____') $_POST['exit_date']='';
      $exit_date=trim_gks(stripslashes(urldecode($_POST['exit_date'])));
      if ($exit_date!='') $exit_date = "'".date('Y-m-d', gks_myFormatDate($exit_date))."'";      
    }
    $sql="insert into gks_log_company_users (action_date,action_user_id,action_type,action_myip,company_id,company_sub_id,user_id,hire_exit_date) values(
    NOW(),
    ".$my_wp_user_id.",
    'delete',
    '".$db_link->escape_string($gkIP)."',
    ".$company_id.",
    ".$company_sub_id.",
    ".$user_id.",
    ".$exit_date.")";
    $result = $db_link->query($sql);
    if (!$result) {
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();      
    }  
    $sql="delete from gks_users_protypdays where company_id=".$company_id." and user_id=".$user_id;
    $result = $db_link->query($sql);
    if (!$result) {
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();      
    }    
    
    break;
  case 'gks_country':     
    gks_admin_delete_record_after($id,'country_id','gks_country_lang');
    break;  
  case 'gks_crons': 
    break; 
  case 'gks_crm_activity':
    if ($calendar_id>0) {
      $sql="select * from gks_calendar where id_calendar=".$calendar_id; 
      $result = $db_link->query($sql);
      if (!$result) {
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
      if ($result->num_rows == 1) { 
        $row = $result->fetch_assoc();
        $uri=trim_gks($row['uri']);
        $calendar_user_id=intval($row['calendar_user_id']);
        
        $sql="delete from gks_calendar where id_calendar=".$calendar_id;
        $result = $db_link->query($sql);
        if (!$result) {
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}    
        
        gks_delete_gks_calendar_after($calendar_id,$calendar_user_id,$uri);
      }   
    }
    break;
  case 'gks_crm_channel_sale':
    gks_admin_delete_record_after($id,'crm_channel_sale_id','gks_crm_channel_sale_photo');
    break;
  case 'gks_crm_leads':
    gks_admin_delete_record_after($id,'crm_lead_id','gks_crm_leads_log');
    gks_admin_delete_record_after($id,'crm_lead_id','gks_crm_leads_links');
    gks_admin_delete_record_after($id,'crm_lead_id','gks_crm_leads_photo');
    
    $sql="update gks_users_extra_address set crm_lead_id=0 where crm_lead_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}    
    break; 
  case 'gks_crm_leads_links':
    break;
  case 'gks_crm_leads_status':
    gks_admin_delete_record_after($id,'crm_lead_status_id','gks_crm_leads_status_photo');
    break;
  case 'gks_crm_machine':
    gks_admin_delete_record_after($id,'crm_machine_id','gks_crm_machine_log');
    gks_admin_delete_record_after($id,'crm_machine_id','gks_crm_machine_messages');
    gks_admin_delete_record_after($id,'crm_machine_id','gks_crm_machine_photo');
    gks_admin_delete_record_after($id,'crm_machine_id','gks_crm_machine_log');
    
    break;
  case 'gks_crm_tasks': 
    gks_admin_delete_record_after($id,'crm_task_id','gks_crm_tasks_log');
    gks_admin_delete_record_after($id,'crm_task_id','gks_crm_tasks_links');
    gks_admin_delete_record_after($id,'crm_task_id','gks_crm_tasks_photo');
    gks_admin_delete_record_after($id,'crm_task_id','gks_crm_tasks_employee');
    gks_admin_delete_record_after($id,'crm_task_id','gks_crm_tasks_machine');

    $sql="update gks_users_extra_address set crm_task_id=0 where crm_task_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}    


    break; 
  case 'gks_crm_tasks_employee':    break;
  case 'gks_crm_tasks_links':    break;
  case 'gks_crm_tasks_machine':    break;
  case 'gks_crm_tasks_status': {
    gks_admin_delete_record_has_other($id,'crm_task_status_id','gks_crm_tasks_status_photo');
    break;}
  case 'gks_delivery_methods':
    gks_admin_delete_record_after($id,'delivery_method_id','gks_delivery_methods_lang');
    break;
    
  case 'gks_email_template':
    gks_admin_delete_record_after($id,'email_template_id','gks_email_template_photo');
    gks_admin_delete_record_after($id,'email_template_id','gks_email_template_object_forms');
    break;  
  case 'gks_erp_app':
    gks_admin_delete_record_after($id,'erp_app_id','gks_erp_app_log');
    gks_admin_delete_record_after($id,'erp_app_id','gks_erp_app_ping');
    break;
  case 'gks_erp_app_mobile':
    gks_admin_delete_record_after($id,'erp_app_mobile_id','gks_qrcode_scan');
    gks_admin_delete_record_after($id,'erp_app_mobile_id','gks_erp_app_mobile_log');
    gks_admin_delete_record_after($id,'erp_app_mobile_id','gks_erp_app_mobile_ping');
    
    break;
  case 'gks_eshop_fiscal_position':
    gks_admin_delete_record_after($id,'fiscal_position_id','gks_company_fpa');
    gks_admin_delete_record_after($id,'fiscal_position_id','gks_company_subs_fpa');
    break;
  case 'gks_eshop_pricelist':
    gks_admin_delete_record_after($id,'pricelist_id','gks_eshop_pricelist_photo');
    break;
  case 'gks_eshop_pricelist_items':
    gks_admin_delete_record_after($id,'pricelist_item_id','gks_eshop_pricelist_items_brands');
    gks_admin_delete_record_after($id,'pricelist_item_id','gks_eshop_pricelist_items_categories');
    gks_admin_delete_record_after($id,'pricelist_item_id','gks_eshop_pricelist_items_products');
    gks_admin_delete_record_after($id,'pricelist_item_id','gks_eshop_pricelist_items_photo');
    gks_admin_delete_record_after($id,'pricelist_item_id','gks_woo_coupons');
    
    
    
    break;
  case 'gks_eshop_pricelist_items_products': break;
  case 'gks_eshop_pricelist_items_categories': break;
  case 'gks_eshop_pricelist_items_brands': break;
  case 'gks_eshop_product_lots':
    gks_admin_delete_record_after($id,'lot_product_id','gks_eshop_product_lots_photo');
    gks_admin_delete_record_after($id,'lot_product_id','gks_warehouse_balance_lots_serials');
    
    
    
    break;
  case 'gks_eshop_products': {
    gks_admin_delete_record_after($id_array,'product_id','gks_eshop_products_brands_products');
    gks_admin_delete_record_after($id_array,'product_id','gks_log_eshop_products_brands_product');
    gks_admin_delete_record_after($id_array,'product_id','gks_eshop_products_categories_products');
    gks_admin_delete_record_after($id_array,'product_id','gks_log_eshop_products_categories_product');
    gks_admin_delete_record_after($id_array,'product_id','gks_eshop_products_income');
    gks_admin_delete_record_after($id_array,'product_id','gks_eshop_products_expenses');
    gks_admin_delete_record_after($id_array,'product_id','gks_eshop_products_idiotites');
    gks_admin_delete_record_after($id_array,'product_id','gks_eshop_products_photo');
    gks_admin_delete_record_after($id_array,'product_id','gks_eshop_products_variables');
    gks_admin_delete_record_after($id_array,'product_id','gks_warehouse_balance_eidi');
    gks_admin_delete_record_after($id_array,'product_id','gks_woo_product');
    gks_admin_delete_record_after($id_array,'product_id','gks_eshop_products_lang');
    gks_admin_delete_record_after($id_array,'product_id','gks_eshop_products_prices');
    gks_admin_delete_record_after($id_array,'product_id','gks_barcodes');
    
    $sql="DELETE gks_eshop_products_idiotites_terms.*
    FROM gks_eshop_products_idiotites_terms
    LEFT JOIN gks_eshop_products_idiotites ON gks_eshop_products_idiotites_terms.eshop_products_idiotites_id = gks_eshop_products_idiotites.id_eshop_products_idiotites
    WHERE (((gks_eshop_products_idiotites.id_eshop_products_idiotites) Is Null))";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}   

    


    break; }
  
  case 'gks_eshop_products_brands':
    gks_admin_delete_record_after($id,'product_brand_id','gks_eshop_products_brands_lang');
    gks_admin_delete_record_after($id,'product_brand_id','gks_eshop_products_brands_photo');
    gks_admin_delete_record_after($id,'product_brand_id','gks_eshop_products_brands_products');
    gks_admin_delete_record_after($id,'product_brand_id','gks_woo_brands');
    gks_admin_delete_record_after($id,'brand_id','gks_log_eshop_products_brands_product');
    break; 
  case 'gks_eshop_products_categories':
    gks_admin_delete_record_after($id,'cateidos_id','gks_production_ergasies_eidoscat');
    gks_admin_delete_record_after($id,'product_category_id','gks_eshop_products_categories_lang');
    gks_admin_delete_record_after($id,'product_category_id','gks_eshop_products_categories_photo');
    gks_admin_delete_record_after($id,'product_category_id','gks_eshop_products_categories_products');
    gks_admin_delete_record_after($id,'product_category_id','gks_woo_categories');
    gks_admin_delete_record_after($id,'category_id','gks_log_eshop_products_categories_product');
    
    break; 
  case 'gks_eshop_products_brands_products':    break;
  case 'gks_eshop_products_categories_products':     break;
  case 'gks_eshops':
    gks_admin_delete_record_after($id,'eshop_id','gks_eshops_photo');
    gks_admin_delete_record_after($id,'eshop_id','gks_woo_brands');
    gks_admin_delete_record_after($id,'eshop_id','gks_woo_categories');
    gks_admin_delete_record_after($id,'eshop_id','gks_woo_coupons');
    gks_admin_delete_record_after($id,'eshop_id','gks_woo_product');
    break;
  case 'gks_gsis_check': break; 
  case 'gks_vies_check': break; 
  case 'gks_voip_favorites': break;
  case 'gks_hotel': 
    gks_admin_delete_record_after($id,'hotel_id','gks_hotel_availability');
    gks_admin_delete_record_after($id,'hotel_id','gks_hotel_availability_day');
    gks_admin_delete_record_after($id,'hotel_id','gks_hotel_lang');
    gks_admin_delete_record_after($id,'hotel_id','gks_hotel_photo');
    gks_admin_delete_record_after($id,'hotel_id','gks_hotel_price');
    gks_admin_delete_record_after($id,'hotel_id','gks_hotel_price_day');
    
    
    break; 
  case 'gks_hotel_availability':
    gks_admin_delete_record_after($id,'hotel_availability_id','gks_hotel_availability_photo');
    calc_availability_day($hotel_room_type_id, $hotel_room_id,$availability_from, $availability_to);    
    break;
  case 'gks_hotel_floor':    
    gks_admin_delete_record_after($id,'hotel_floor_id','gks_hotel_floor_lang');
    gks_admin_delete_record_after($id,'hotel_floor_id','gks_hotel_floor_photo');
    
    break;
  case 'gks_hotel_price':    
    gks_admin_delete_record_after($id,'hotel_price_id','gks_hotel_price_photo');
    calc_price_day($hotel_room_type_id, $price_from, $price_to);    
    break; 

    
  case 'gks_hotel_reservation': {
    gks_admin_delete_record_after($id,'hotel_reservation_id','gks_hotel_reservation_room_type');
    gks_admin_delete_record_after($id,'hotel_reservation_id','gks_hotel_reservation_room');
    gks_admin_delete_record_after($id,'hotel_reservation_id','gks_hotel_reservation_room_day');
    gks_admin_delete_record_after($id,'hotel_reservation_id','gks_hotel_reservation_links');
    gks_admin_delete_record_after($id,'hotel_reservation_id','gks_hotel_reservation_messages');
    gks_admin_delete_record_after($id,'hotel_reservation_id','gks_hotel_reservation_photo');
    gks_admin_delete_record_after($id,'hotel_reservation_id','gks_hotel_reservation_log');
    break;}
  case 'gks_hotel_reservation_links': break;
  
  case 'gks_hotel_room':{
    gks_admin_delete_record_after($id,'hotel_room_id','gks_hotel_availability');
    gks_admin_delete_record_after($id,'hotel_room_id','gks_hotel_availability_day');    
    gks_admin_delete_record_after($id,'hotel_room_id','gks_hotel_folio_room_day');    
    gks_admin_delete_record_after($id,'hotel_room_id','gks_hotel_reservation_room_day');    
    gks_admin_delete_record_after($id,'hotel_room_id','gks_hotel_room_lang');    
    gks_admin_delete_record_after($id,'hotel_room_id','gks_hotel_room_photo');    
    
    break;  }
  case 'gks_hotel_room_type':{
    $sql="DELETE FROM gks_hotel_room_type_subroom_bed where hotel_room_type_subroom_id in (select id_hotel_room_type_subroom from gks_hotel_room_type_subroom where hotel_room_type_id=".$id.")";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }

    gks_admin_delete_record_after($id,'hotel_room_type_id','gks_hotel_room_type_subroom');
    gks_admin_delete_record_after($id,'hotel_room_type_id','gks_hotel_room_type_amenity');
    gks_admin_delete_record_after($id,'hotel_room_type_id','gks_hotel_availability');
    gks_admin_delete_record_after($id,'hotel_room_type_id','gks_hotel_availability_day');
    gks_admin_delete_record_after($id,'hotel_room_type_id','gks_hotel_price');
    gks_admin_delete_record_after($id,'hotel_room_type_id','gks_hotel_price_day');
    gks_admin_delete_record_after($id,'hotel_reservation_room_type_id','gks_hotel_reservation_room_day');
    gks_admin_delete_record_after($id,'hotel_room_type_id','gks_hotel_room_type_lang');
    gks_admin_delete_record_after($id,'hotel_room_type_id','gks_hotel_room_type_photo');
    break;  }
  case 'gks_lang':
    gks_admin_delete_record_after($id,'lang_idd','gks_lang_lang');
    gks_admin_delete_record_after($id,'lang_idd','gks_lang_photo');
    break;
  case 'gks_mass_messages':
    $sql="update gks_async_queue set status='abort' where mytype='mass' and param1='".$id."'"; 
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}     

    $sql="update gks_sms set model='',model_id=0 where model='mass' and model_id=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}     

    $sql="update gks_email set model='',model_id=0 where model='mass' and model_id=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}     
    
    $sql="update gks_viber_msgs set model='',model_id=0 where model='mass' and model_id=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}     
    
    break;
  case 'gks_monades_metrisis':
    gks_admin_delete_record_after($id,'monada_id','gks_monades_metrisis_lang');
    
    break;  
        
  case 'gks_nomoi': 
    gks_admin_delete_record_after($id,'nomos_id','gks_nomoi_lang');
    break;  




  case 'gks_object_rel':    break;
  
  case 'gks_orders': {
    gks_admin_delete_record_after($id,'order_id','gks_file_perm');
    gks_admin_delete_record_after($id,'order_id','gks_orders_aws_download');
    gks_admin_delete_record_after($id,'order_id','gks_orders_links');
    gks_admin_delete_record_after($id,'order_id','gks_orders_log');
    gks_admin_delete_record_after($id,'order_id','gks_orders_messages');
    gks_admin_delete_record_after($id,'order_id','gks_orders_photo');
    gks_admin_delete_record_after($id,'order_id','gks_orders_products');
    gks_admin_delete_record_after($id,'order_id','gks_orders_products_sets');
    gks_admin_delete_record_after($id,'order_id','gks_orders_uploads');
    gks_admin_delete_record_after($id,'order_id','gks_production_sintagi');
    gks_admin_delete_record_after($id,'order_id','gks_production_sintagi_cost');
    gks_admin_delete_record_after($id,'order_id','gks_production_sintagi_product');
    gks_admin_delete_record_after($id,'order_id','gks_production_line');
    gks_admin_delete_record_after($id,'order_id','gks_production_line_pid');
    gks_admin_delete_record_after($id,'order_id','gks_acc_seires_auto_numbers');
    
  
  
    $sql="update gks_users_extra_address set order_id=0 where order_id=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}    

    gks_whi_mov_balance_calc($id_product_array);

    if (count($production_line_id_array)>0) {
      gks_admin_delete_record_after($production_line_id_array,'production_line_id','gks_production_line_pid');
      gks_admin_delete_record_after($production_line_id_array,'production_line_id','gks_production_line_time');
    }
    
    $sql="delete from gks_users_templates where object_name='gks_orders' and user_id=".$my_wp_user_id." and template_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($db_link->affected_rows > 0) {
      gks_cache_update_menu_version();
    }
         
    break; }
  
  case 'gks_orders_links': break;
  case 'gks_payment_acquirers':
    gks_admin_delete_record_after($id,'payment_acquirer_id','gks_acc_seires_paymentacquirers'); 
    gks_admin_delete_record_after($id,'payment_acquirer_id','gks_payment_acquirers_lang'); 
     
    break;  
  case 'gks_orders_occasion':
    
    break;
  case 'gks_poi': 
    gks_admin_delete_record_after($id,'poi_id','gks_poi_lang'); 
    gks_admin_delete_record_after($id,'poi_id','gks_poi_photo'); 
    gks_admin_delete_record_after($id,'poi_id','gks_transfer_area2poi'); 

    $sql="update gks_poi set poi_parent_id=0 where poi_parent_id=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}    
    break;
  case 'gks_poi_diadromes': 
    gks_admin_delete_record_after($id,'poi_diadromes_id','gks_poi_diadromes_photo');
    break;
  case 'gks_poi_type': 
    gks_admin_delete_record_after($id,'poi_type_id','gks_poi_type_lang');
    gks_admin_delete_record_after($id,'poi_type_id','gks_poi_type_photo');
    break;
  case 'gks_pos':
    gks_admin_delete_record_after($id,'pos_id','gks_pos_photo');
  
    break;
  case 'gks_print_forms':
    gks_admin_delete_record_after($id,'print_form_id','gks_print_forms_photo');
    gks_admin_delete_record_after($id,'print_form_id','gks_print_objects_forms');
    break;
  case 'gks_product_idiotites': {
    gks_admin_delete_record_after($id,'product_idiotita_id','gks_product_idiotites_lang');
    
    $GKS_IDIOTITES_CACHE_VER=time();
    $sql="replace into gks_settings (mykey,myvalue) values ('GKS_IDIOTITES_CACHE_VER','".$db_link->escape_string($GKS_IDIOTITES_CACHE_VER)."')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    break; }
  case 'gks_product_idiotites_terms': {
    gks_admin_delete_record_after($id,'product_idiotita_term_id','gks_product_idiotites_terms_lang');
    $GKS_IDIOTITES_CACHE_VER=time();
    $sql="replace into gks_settings (mykey,myvalue) values ('GKS_IDIOTITES_CACHE_VER','".$db_link->escape_string($GKS_IDIOTITES_CACHE_VER)."')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    break; }
   
  case 'gks_production_bom':
    gks_admin_delete_record_after($id,'production_bom_id','gks_production_bom_cost');
    gks_admin_delete_record_after($id,'production_bom_id','gks_production_bom_product');
    gks_admin_delete_record_after($id,'production_bom_id','gks_production_bom_photo');
    
    break;
  
  case 'gks_production_ergasies':
    gks_admin_delete_record_after($id,'production_ergasia_id','gks_production_ergasies_eidos');
    gks_admin_delete_record_after($id,'production_ergasia_id','gks_production_ergasies_eidoscat');
    gks_admin_delete_record_after($id,'ergasia_id','gks_production_ergasies_mustdone');
    gks_admin_delete_record_after($id,'production_ergasia_id','gks_production_ergasies_photo');
    gks_admin_delete_record_after($id,'production_ergasia_id','gks_production_posta_ergasies');
  
    break;
  
  case 'gks_production_ergasies_eidos':    break;
  case 'gks_production_ergasies_eidoscat':    break;
  case 'gks_production_ergasies_mustdone':    break;
  case 'gks_production_line': {
    gks_admin_delete_record_after($id,'production_line_id','gks_production_line_pid');
    gks_admin_delete_record_after($id,'production_line_id','gks_production_line_time');
    break; }
  case 'gks_production_posta':
    gks_admin_delete_record_after($id,'production_posto_id','gks_production_posta_ergasies');
    gks_admin_delete_record_after($id,'production_posto_id','gks_production_posta_users');
    gks_admin_delete_record_after($id,'production_posto_id','gks_production_posta_photo');
    
    break;
  case 'gks_production_posta_ergasies':    break;
  case 'gks_production_posta_users':    break;
  case 'gks_sms_viber_template': 
    
    break;
  case 'gks_sociallinks_type': break;
  case 'gks_transfer':
    gks_admin_delete_record_after($id,'transfer_id','gks_assets_rental_availability');
    gks_admin_delete_record_after($id,'transfer_id','gks_assets_rental_availability_day');
    gks_admin_delete_record_after($id,'transfer_id','gks_assets_rental_price');
    gks_admin_delete_record_after($id,'transfer_id','gks_assets_rental_price_day');
    gks_admin_delete_record_after($id,'transfer_id','gks_transfer_area2transfer');
    gks_admin_delete_record_after($id,'transfer_id','gks_transfer_oxima2type2transfer');
    gks_admin_delete_record_after($id,'transfer_id','gks_transfer_oximatype2transfer');
    gks_admin_delete_record_after($id,'transfer_id','gks_transfer_pricelist2transfer');
    gks_admin_delete_record_after($id,'transfer_id','gks_transfer_photo');
    gks_admin_delete_record_after($id,'transfer_id','gks_transfer_lang');
    
    
    
    break;    
  case 'gks_transfer_area':
    gks_admin_delete_record_after($id,'transfer_area_id','gks_transfer_area_lang');
    gks_admin_delete_record_after($id,'transfer_area_id','gks_transfer_area_photo');
    gks_admin_delete_record_after($id,'transfer_area_id','gks_transfer_area2poi');
    gks_admin_delete_record_after($id,'transfer_area_id','gks_transfer_area2transfer');
    gks_admin_delete_record_after($id,'transfer_area_id','gks_transfer_area2externalpartner');
  
    $sql="update gks_transfer_area set transfer_area_parent_id=0 where transfer_area_parent_id=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}     
    break;

  case 'gks_transfer_area2poi':  break;
  case 'gks_transfer_area2transfer': break;
  case 'gks_transfer_area2externalpartner': break;

  case 'gks_transfer_oxima_type':
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_assets_rental_availability');
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_assets_rental_availability_day');
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_assets_rental_price');
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_assets_rental_price_day');
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_transfer_oxima2type2transfer');
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_transfer_oxima_type_lang');
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_transfer_oxima_type_per_km');
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_transfer_oxima_type_photo');
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_transfer_oximatype2transfer');
    gks_admin_delete_record_after($id,'transfer_oxima_type_id','gks_transfer_reservation_oximata_day');
    
    break;

  case 'gks_transfer_oxima2type2transfer':
    break;
    
  case 'gks_transfer_pricelist':
    gks_admin_delete_record_after($id,'transfer_pricelist_id','gks_transfer_pricelist2transfer');
    gks_admin_delete_record_after($id,'transfer_pricelist_id','gks_transfer_pricelist_photo');
  
    break;
    
  case 'gks_transfer_reservation':
    gks_admin_delete_record_after($id,'transfer_reservation_id','gks_transfer_reservation_links');
    gks_admin_delete_record_after($id,'transfer_reservation_id','gks_transfer_reservation_log');
    gks_admin_delete_record_after($id,'transfer_reservation_id','gks_transfer_reservation_messages');
    gks_admin_delete_record_after($id,'transfer_reservation_id','gks_transfer_reservation_oximata');
    gks_admin_delete_record_after($id,'transfer_reservation_id','gks_transfer_reservation_oximata_day');
    gks_admin_delete_record_after($id,'transfer_reservation_id','gks_transfer_reservation_photo');
    break;
  case 'gks_urlshort': break;  
  case 'gks_urlshort_hit': break; 
  case 'gks_users_favorites':{
    gks_cache_update_menu_version();
  
  
    break;}    
  case 'gks_users_groups':    
    gks_admin_delete_record_after($id,'users_group_id','gks_users_groups_photo');
    
    break;   
  case 'gks_users_groups_users': 
    $sql="insert into gks_log_users_groups_users (action_date,action_user_id,action_type,action_myip,group_id,user_id,is_omadarxis) values(
    NOW(),
    ".$my_wp_user_id.",
    'delete',
    '".$db_link->escape_string($gkIP)."',
    ".$group_id.",
    ".$user_id.",
    ".$is_omadarxis.")";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('error sql'));
      echo json_encode($return); die();
    }  
    break;   
  case 'gks_users_templates': {
    gks_cache_update_menu_version();
    break;}


  case 'gks_warehouses':
    gks_admin_delete_record_after($id,'warehouse_id','gks_warehouse_balance_eidi');
    gks_admin_delete_record_after($id,'warehouse_id','gks_warehouse_balance_lots_serials');
    gks_admin_delete_record_after($id,'warehouse_id','gks_warehouses_lang');
    gks_admin_delete_record_after($id,'warehouse_id','gks_warehouses_photo');
    
    
    
    break;
  case 'gks_whi_mov': 
    gks_admin_delete_record_after($id,'whi_mov_id','gks_whi_mov_links');
    gks_admin_delete_record_after($id,'whi_mov_id','gks_whi_mov_log');
    gks_admin_delete_record_after($id,'whi_mov_id','gks_whi_mov_photo');
    gks_admin_delete_record_after($id,'whi_mov_id','gks_whi_mov_messages');
    gks_admin_delete_record_after($id,'whi_mov_id','gks_whi_mov_products');
    gks_admin_delete_record_after($id,'whi_mov_id','gks_whi_mov_other_entity');
    gks_admin_delete_record_after($id,'whi_mov_id','gks_acc_seires_auto_numbers');
    gks_admin_delete_record_after($id,'whi_mov_id','gks_whi_mov_correlated_invoices');
    gks_admin_delete_record_after($id,'whi_mov_id','gks_whi_mov_multiple_connected_marks');
  
  
  
    $sql="update gks_users_extra_address set whi_mov_id=0 where whi_mov_id=".$id; 
    $result = $db_link->query($sql);
    if (!$result) {$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}     

    $sql="delete from gks_users_templates where object_name='gks_whi_mov' and user_id=".$my_wp_user_id." and template_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($db_link->affected_rows > 0) {
      gks_cache_update_menu_version();
    }

    break; 
  case 'gks_whi_mov_links':    break;


  case 'gks_woo_brands':    break;
  case 'gks_woo_categories':    break;
  case 'gks_woo_coupons':    break;
  case 'gks_woo_product':    break;
 
  
  case 'wp_users':
    gks_admin_delete_record_after($id,'this_user_id','gks_calendar_other_users');
    gks_admin_delete_record_after($id,'other_user_id','gks_calendar_other_users');
    gks_admin_delete_record_after($id,'user_id','gks_newsletter_lists_deny_user');
    gks_admin_delete_record_after($id,'user_id','gks_notification_userperm');
    gks_admin_delete_record_after($id,'user_id','gks_permission_user');
    gks_admin_delete_record_after($id,'production_user_id','gks_production_posta_users');
    gks_admin_delete_record_after($id,'user_id','gks_settings_users');
    gks_admin_delete_record_after($id,'user_id','gks_users');
    gks_admin_delete_record_after($id,'user_id','gks_users_card_expand');
    gks_admin_delete_record_after($id,'user_id','gks_users_cars');
    gks_admin_delete_record_after($id,'user_id','gks_users_communication');
    gks_admin_delete_record_after($id,'user_id','gks_users_favorites');
    gks_admin_delete_record_after($id,'user_id','gks_users_groups_users');
    gks_admin_delete_record_after($id,'user_id','gks_users_photo');
    gks_admin_delete_record_after($id,'user_id','gks_users_protypdays');
    gks_admin_delete_record_after($id,'user_id','gks_users_templates');
 
    $sql="select uid from gks_user_carddav where ID=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();     
      $uri=$row['uid'].'.vcf';
      
      
      
      $sql="select myvalue from gks_settings where mykey='carddav_synctoken'";
    	$result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
        
      $carddav_synctoken=0;
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $carddav_synctoken=intval($row['myvalue']);
      }
      $sql="update gks_settings set myvalue='".trim_gks($carddav_synctoken + 1)."' where  mykey='carddav_synctoken'";
    	$result = $db_link->query($sql); 
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
        
      $operation=3; //($is_new_rec ? 1 : 2);
      $sql="INSERT INTO gks_users_dav_changes (
      uri, synctoken, addressbookid, operation
      ) values (
      '".$db_link->escape_string($uri)."',".$carddav_synctoken.",1,".$operation."
      )";
    	$result = $db_link->query($sql); 
      if (!$result) {debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
    	        
      
      
    }   
    gks_admin_delete_record_after($id,'user_id',GKS_WP_TABLE_PREFIX.'usermeta');
    
    
    gks_admin_delete_record_after($id,'ID','gks_user_carddav');
    
    
    break;  
    
	case 'system_file':
  	if (file_exists(GKS_DATA.$id_raw)) unlink(GKS_DATA.$id_raw);		
    break;
      
  default:
    $has_error=true;
    if (startwith($mymodel,'gks_ct_')) { // einai gks_custom_table
      $has_error=false;
    }
    
    if ($has_error) {
      debug_mail(false,'error on mymodel name (4)','');
      $return = array('success' => false, 'message' => base64_encode('error on mymodel name (4).'));
      echo json_encode($return); die();  
    }
}


$sql="delete from gks_object_rel where (object_name1='".$db_link->escape_string($mymodel)."' and object_id1=".$id.") or (object_name2='".$db_link->escape_string($mymodel)."' and object_id2=".$id.")";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}    
    

//custom
$sql="select field_name_id_current from gks_custom_table where custom_table_name='".$db_link->escape_string($mymodel)."'";
//debug_mail(false,'sql1',$sql);
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}    
if ($result->num_rows>0) {
  $row = $result->fetch_assoc();
  $field_name_id_current=$row['field_name_id_current'];

  $table_name='gks_customt_'.$mymodel;
  $sql="show tables like '".$db_link->escape_string($table_name)."'";
  //debug_mail(false,'sql2',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}    

  if ($result->num_rows==1) { //table exist
    $sql="delete from ".$table_name." where ".$field_name_id_current."=".$id." limit 1";
    //debug_mail(false,'sql3',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    
  }    
  
}




$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();


function gks_delete_gks_calendar_after($id,$calendar_user_id,$uri) {
  global $db_link;
  
    $sql_event="select id_dav_calendar,caldav_synctoken from gks_calendar_dav_calendars where user_id=".$calendar_user_id." and other_myobj='cal'";
  	$result_event = $db_link->query($sql_event);  
  	if (!$result_event) {
  	  debug_mail(false,'error sql',$sql_event);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
      
    $caldav_synctoken=0;
    $id_dav_calendar=0;
    if ($result_event->num_rows>0) {
      $row_event = $result_event->fetch_assoc();
      $caldav_synctoken=$row_event['caldav_synctoken'];
      $id_dav_calendar=$row_event['id_dav_calendar'];
    }
    
    $sql_event="update gks_calendar_dav_calendars set caldav_synctoken=".($caldav_synctoken + 1)." where id_dav_calendar=".$id_dav_calendar;
  	$result_event = $db_link->query($sql_event); 
  	if (!$result_event) {
  	  debug_mail(false,'error sql',$sql_event);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
    
    $operation=3;
    $sql_change="INSERT INTO gks_calendar_dav_changes (
    uri, synctoken, calendarid, operation
    ) values (
    '".$db_link->escape_string($uri)."',".$caldav_synctoken.",".$id_dav_calendar.",".$operation."
    )";
  	$result_change = $db_link->query($sql_change); 
  	if (!$result_change) {
  	  debug_mail(false,'error sql',$sql_change);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
  


    $sql="delete from gks_calendar_notification where calendar_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    
    $sql="delete from gks_calendar_participant where calendar_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    
    $sql="delete from gks_notification where model='calendar' and model_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    
    $sql="update gks_crm_activity set calendar_id=0 where calendar_id=".$id;
    $result = $db_link->query($sql);
    if (!$result) {
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();} 
    
    gks_calendar_event_update_dav_activity($id,false);
       
}


function gks_admin_delete_record_has_other($id,$ofield,$otable,$message_input='') {
  global $error_lines;
  global $db_link;
  if (is_array($id)) {
    $mywww=$ofield." in (".implode(',',$id).")"; 
  } else {
    $mywww=$ofield."=".$id; 
  }
  //echo '<pre>sssss';die();
  $sql="select count(*) as cc from ".$otable." where ".$mywww; 
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cc=intval($row['cc']); 
    if ($cc>0) {
      
      $tblname='';
      switch ($otable) {   
        case 'gks_acc_inv': $tblname=gks_lang('παραστατικά'); break;  
        case 'gks_acc_inv_correlated_invoices': $tblname=gks_lang('συσχετιζόμενα παραστατικά'); break;  
        case 'gks_acc_inv_multiple_connected_marks': $tblname=gks_lang('πολλαπλά συνδεόμενα ΜΑΡΚ'); break;  
        case 'gks_acc_inv_payment': $tblname=gks_lang('τρόπος πληρωμής παραστατικού'); break;  
        case 'gks_acc_inv_products': $tblname=gks_lang('γραμμές παραστατικών'); break;  
        case 'gks_acc_inv_products_lots': $tblname=gks_lang('γραμμές παραστατικών'); break;  
        case 'gks_acc_inv_links': $tblname=gks_lang('συνδέσμους σε παραστατικά'); break;  
        case 'gks_acc_inv_messages': $tblname=gks_lang('καταχωρήσεις στα μηνύματα παραστατικών'); break;  
        case 'gks_acc_inv_other_entity': $tblname=gks_lang('παραστατικά ως <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ.</b>'); break;  
        case 'gks_acc_journal': $tblname=gks_lang('ημερολόγια'); break;  
        case 'gks_acc_pay': $tblname=gks_lang('πληρωμές'); break;  
        case 'gks_acc_pay_correlated_invoices': $tblname=gks_lang('συσχετιζόμενες πληρωμές'); break;  
        case 'gks_acc_pay_multiple_connected_marks': $tblname=gks_lang('πολλαπλά συνδεόμενα ΜΑΡΚ'); break;  
        case 'gks_acc_pay_links': $tblname=gks_lang('συνδέσμους σε πληρωμές'); break;  
        case 'gks_acc_pay_messages': $tblname=gks_lang('καταχωρήσεις στα μηνύματα πληρωμών'); break;  
        case 'gks_acc_pay_payment': $tblname=gks_lang('τρόπος πληρωμής σε πληρωμές'); break;
        case 'gks_acc_pay_poso_acc_inv': $tblname=gks_lang('πληρωμές παραστατικών'); break;
        case 'gks_acc_pay_poso_hotel_reservation': $tblname=gks_lang('πληρωμές κρατήσεων ξενοδοχείου'); break;
        case 'gks_acc_pay_poso_order': $tblname=gks_lang('πληρωμές'); break;
        case 'gks_acc_seires': $tblname=gks_lang('σειρές'); break;  
        case 'gks_airline': $tblname=gks_lang('αεροπορικές εταιρείες'); break;  
        case 'gks_assets': $tblname=gks_lang('πάγια'); break;  
        case 'gks_assets_moves': $tblname=gks_lang('κινήσεις παγίων'); break;  
        case 'gks_assets_rental': $tblname=gks_lang('ενοικιάσεις παγίων'); break;  
        case 'gks_assets_rental_asset': $tblname=gks_lang('ενοικιάσεις οχημάτων ως πάγιο'); break;  
        case 'gks_assets_rental_price': $tblname=gks_lang('τιμοκαταλόγους ενοικίασης'); break;  
        case 'gks_assets_service': $tblname=gks_lang('service παγίων'); break;  
        case 'gks_assets_whi_mov': $tblname=gks_lang('απογραφές παγίων'); break;  
        case 'gks_assets_whi_mov_assets': $tblname=gks_lang('γραμμές απογραφές παγίων'); break;  
        case 'gks_banks': $tblname=gks_lang('τράπεζες'); break;  
        case 'gks_bank_accounts': $tblname=gks_lang('τραπεζικούς λογαριασμούς'); break;  
        case 'gks_bank_transactions': $tblname=gks_lang('τραπεζικές συναλλαγές'); break;  
        case 'gks_barcodes': $tblname=gks_lang('barcodes'); break;  
        case 'gks_calendar': $tblname=gks_lang('καταχωρήσεις στο ημερολόγιο'); break;  
        case 'gks_company': $tblname=gks_lang('εταιρείες'); break;  
        case 'gks_company_subs': $tblname=gks_lang('υποκαταστήματα'); break;  
        case 'gks_company_users': $tblname=gks_lang('υπαλλήλους'); break;  
        case 'gks_crm_activity': $tblname=gks_lang('δραστηριότητες'); break;  
        case 'gks_crm_leads': $tblname=gks_lang('ευκαιρίες'); break;  
        case 'gks_crm_leads_links': $tblname=gks_lang('συνδέσμους σε ευκαιρίες'); break;  
        case 'gks_crm_leads_messages': $tblname=gks_lang('καταχωρήσεις στα μηνύματα ευκαιριών'); break;  
        case 'gks_crm_machine': $tblname=gks_lang('συσκευές'); break;  
        case 'gks_crm_machine_messages': $tblname=gks_lang('καταχωρήσεις στα μηνύματα συσκευών'); break;  
        case 'gks_crm_tasks': $tblname=gks_lang('εργασίες'); break;  
        case 'gks_crm_tasks_links': $tblname=gks_lang('συνδέσμους σε εργασίες'); break;
        case 'gks_crm_tasks_machine':  $tblname=gks_lang('εργασίες συσκευών'); break; 
        case 'gks_crm_tasks_messages': $tblname=gks_lang('καταχωρήσεις στα μηνύματα εργασιών'); break;  
        case 'gks_doy': $tblname=gks_lang('ΔΟΥ'); break;  
        case 'gks_eftpos_transaction': $tblname=gks_lang('συναλλαγές EFT/POS'); break;  
        case 'gks_email': $tblname=gks_lang('emails'); break;  
        case 'gks_erp_app_mobile': $tblname=gks_lang('gks ERP App Mobile'); break;  
        case 'gks_eshop_pricelist_items': $tblname=gks_lang('στοιχεία τιμοκαταλόγου-κουπόνια'); break;  
        case 'gks_eshop_pricelist_items_categories': $tblname=gks_lang('γραμμές τιμοκαταλόγων/κατηγορίες'); break;  
        case 'gks_eshop_pricelist_items_brands': $tblname=gks_lang('γραμμές τιμοκαταλόγων/μάρκες'); break;  
        case 'gks_eshop_pricelist_items_products': $tblname=gks_lang('γραμμές τιμοκαταλόγων/είδη'); break;  
        case 'gks_eshop_product_lots': $tblname=gks_lang('παρτίδες/serial number'); break;  
        case 'gks_eshop_products': $tblname=gks_lang('είδη'); break;  
        case 'gks_eshop_products_categories': $tblname=gks_lang('κατηγορίες'); break;  
        case 'gks_eshop_products_idiotites': $tblname=gks_lang('ιδιότητες'); break;  
        case 'gks_eshop_products_idiotites_terms': $tblname=gks_lang('όροι ιδιοτήτων'); break;  
        case 'gks_eshop_products_brands': $tblname=gks_lang('μάρκες'); break;  
        case 'gks_eshops': $tblname=gks_lang('eshop'); break;  
        
        case 'gks_hotel': $tblname=gks_lang('ξενοδοχεία'); break;  
        case 'gks_hotel_floor': $tblname=gks_lang('ορόφους ξενοδοχείου'); break;  
        case 'gks_hotel_folio': $tblname=gks_lang('καρτέλες διαμένοντος'); break;  
        case 'gks_hotel_folio_room': $tblname=gks_lang('δωμάτια καρτελών διαμένοντος'); break;  
        case 'gks_hotel_reservation': $tblname=gks_lang('κρατήσεις ξενοδοχείου'); break;  
        case 'gks_hotel_reservation_links': $tblname=gks_lang('συνδέσμους σε κράτηση ξενοδοχείου'); break;  
        case 'gks_hotel_reservation_messages': $tblname=gks_lang('καταχωρήσεις στα μηνύματα κρατήσεων ξενοδοχείου'); break;  
        case 'gks_hotel_reservation_room': $tblname=gks_lang('δωμάτια κρατήσεων ξενοδοχείου'); break;  
        case 'gks_hotel_reservation_room_type': $tblname=gks_lang('τύπους δωματίων κρατήσεων ξενοδοχείου'); break;  
        case 'gks_hotel_room': $tblname=gks_lang('δωμάτια ξενοδοχείου'); break;  
        case 'gks_hotel_room_type': $tblname=gks_lang('τύπους δωματίων ξενοδοχείου'); break;  
        case 'gks_hotel_room_type_channel_name': $tblname=gks_lang('channel managers ξενοδοχείου'); break;  
        case 'gks_hr_interview': $tblname=gks_lang('συνεντεύξεις'); break;  
        case 'gks_hr_user': $tblname=gks_lang('υποψήφιος'); break;  
        case 'gks_hr_user_cvs': $tblname=gks_lang('βιογραφικά'); break;  
        

        case 'gks_mass_messages': $tblname=gks_lang('μαζικές αποστολές SMS-Viber-email'); break;  
        case 'gks_mellon_transaction': $tblname=gks_lang('συναλλαγές Mellon'); break;  
        case 'gks_monades_metrisis': $tblname=gks_lang('μονάδες μέτρησης'); break;  
        case 'gks_nomoi': $tblname=gks_lang('νομούς'); break;  
        case 'gks_orders': $tblname=gks_lang('παραγγελίες'); break;  
        case 'gks_orders_links': $tblname=gks_lang('συνδέσμους σε παραγγελίες'); break;  
        case 'gks_orders_messages': $tblname=gks_lang('καταχωρήσεις στα μηνύματα παραγγελιών'); break;  
        case 'gks_orders_occasion': $tblname=gks_lang('καταχωρήσεις για Περίσταση παραγγελίας'); break;  
        case 'gks_orders_products': $tblname=gks_lang('γραμμές παραγγελίας'); break;  
        case 'gks_orders_products_lots': $tblname=gks_lang('γραμμές παραγγελίας'); break;  
        case 'gks_paroxos_signature': $tblname=gks_lang('ψηφιακές υπογραφές από πάροχο'); break;  
        case 'gks_payments': $tblname=gks_lang('πληρωμές').' (xc56)'; break;  
        case 'gks_payments_piraeusbank': $tblname=gks_lang('πληρωμές').' (piraeusbank)'; break;  
        case 'gks_perifereies': $tblname=gks_lang('περιφέρειες'); break;  
        case 'gks_poi': $tblname=gks_lang('σημεία ενδιαφέροντος'); break;  
        case 'gks_pos': $tblname=gks_lang('σημεία εντατικής λιανικής'); break;  
        case 'gks_product_idiotites_terms': $tblname=gks_lang('όρους ιδιοτήτων'); break;  
        
        case 'gks_production_bom': $tblname=gks_lang('συνταγές'); break;  
        case 'gks_production_bom_product': $tblname=gks_lang('συνταγές/είδος'); break;  
        case 'gks_production_ergasies_eidos': $tblname=gks_lang('εργασίες/είδος'); break;  
        case 'gks_production_ergasies_mustdone': $tblname=gks_lang('προαπαιτούμενες εργασίες'); break;  
        case 'gks_production_line': $tblname=gks_lang('καταχωρήσεις σε γραμμή εντολής παραγωγής'); break;  
        case 'gks_production_line_time': $tblname=gks_lang('καταχωρήσεις σε γραμμή χρόνου εντολής παραγωγής'); break;  
        case 'gks_production_sintagi': $tblname=gks_lang('παραγωγές'); break;  
        case 'gks_production_sintagi_product': $tblname=gks_lang('παραγωγές/συνταγές'); break;  
        case 'gks_production_sintagi_product_lots_serials': $tblname=gks_lang('παραγωγές/συνταγές'); break;  
        case 'gks_qrcode_scan': $tblname=gks_lang('σαρώσεις QR Code'); break;  
        case 'gks_tk': $tblname=gks_lang('ΤΚ'); break;  
        case 'gks_transfer': $tblname=gks_lang('κανάλια transfer'); break;  
        case 'gks_transfer_area': $tblname=gks_lang('περιοχές transfer'); break;  
        case 'gks_transfer_area2externalpartner': $tblname=gks_lang('καταχωρήσεις ως εξωτερικός συνεργάτης σε transfer'); break;  
        case 'gks_transfer_oxima_type': $tblname=gks_lang('τύπους οχημάτων transfer'); break; 
        case 'gks_transfer_pricelist': $tblname=gks_lang('τιμοκαταλόγους transfer'); break; 
        case 'gks_transfer_reservation': $tblname=gks_lang('κρατήσεις transfer'); break; 
        case 'gks_transfer_reservation_links': $tblname=gks_lang('καταχωρήσεις σε συνδέσμους κρατήσεων transfer'); break; 
        case 'gks_transfer_reservation_messages': $tblname=gks_lang('καταχωρήσεις στα μηνύματα κρατήσεων transfer'); break; 
        case 'gks_transfer_reservation_oximata': $tblname=gks_lang('κρατήσεις οχήματος transfer'); break; 
        case 'gks_transfer_sub_company_details': $tblname=gks_lang('ρυθμίσεις transfer υποκαταστήματος'); break; 
         
        case 'gks_urlshort': $tblname=gks_lang('μικρό URL'); break;  
        case 'gks_urlshort_hit': $tblname=gks_lang('καταγραφές Μικρού URL'); break;  
        case 'gks_users': $tblname=gks_lang('επαφές'); break;  
        case 'gks_users_extra_address': $tblname=gks_lang('επιπλέον διευθύνσεις'); break;  
        case 'gks_users_groups': $tblname=gks_lang('ομάδες επαφών'); break;  
        case 'gks_users_groups_users': $tblname=gks_lang('επαφές/ομάδα'); break;  
        case 'gks_users_vacation': $tblname=gks_lang('ρεπό'); break;  
        case 'gks_warehouses': $tblname=gks_lang('αποθήκες'); break;  
        case 'gks_whi_mov': $tblname=gks_lang('δελτία'); break;  
        case 'gks_whi_mov_correlated_invoices': $tblname=gks_lang('συσχετιζόμενα δελτία'); break;  
        case 'gks_whi_mov_multiple_connected_marks': $tblname=gks_lang('πολλαπλά συνδεόμενα ΜΑΡΚ'); break;  
        case 'gks_whi_mov_links': $tblname=gks_lang('συνδέσμους σε δελτία'); break;  
        case 'gks_whi_mov_messages': $tblname=gks_lang('καταχωρήσεις στα μηνύματα δελτίων'); break;  
        case 'gks_whi_mov_other_entity': $tblname=gks_lang('δελτία ως <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ.</b>'); break;  
        case 'gks_whi_mov_products': $tblname=gks_lang('γραμμές δελτίων'); break;  
        case 'gks_whi_mov_products_lots': $tblname=gks_lang('γραμμές δελτίων'); break;  
        case GKS_WP_TABLE_PREFIX.'users': $tblname=gks_lang('επαφές'); break;  
        
        default: $tblname=$otable;
                
      }
      $message=gks_lang('έχει χρησιμοποιηθεί σε <b>[[num]]</b>').' '.$tblname;
      if ($message_input!='') {
        $message.=' '.$message_input;
      }
      
      $message=str_replace('[[num]]',$cc,$message);
      $error_lines[]=$message;
      //debug_mail(false,'delete row',$message);
      //$return = array('success' => false, 'message' => base64_encode($message));
      //echo json_encode($return); die();  
    }
  }
}
function gks_admin_delete_record_has_other_last($start_text) {
  global $error_lines;
  if (count($error_lines)==0) return;
  $message=$start_text.'<br>'.implode('<br>',$error_lines);
  //debug_mail(false,'delete row',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();    
}

function gks_admin_delete_record_after($id,$ofield,$otable) {
  global $db_link;
  if (is_array($id)) {
    $mywww=$ofield." in (".implode(',',$id).")"; 
  } else {
    $mywww=$ofield."=".$id; 
  }
  $sql="delete from ".$otable." where ".$mywww;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
}

function gks_admin_delete_record_has_custom($id,$id_custom_field_type,$is_multi=false) {
  global $error_lines;
  global $db_link;
  $mywhere_template='';
  
  if ($is_multi) { //p.x. ]][[20447]][[20401]][[
    if (is_array($id)) {
      $vvv=[];
      foreach ($id as $vv) {
        $vvv[]="[[cfxxxxx]] like '%[[".$id.']]%';
      } 
      $mywhere_template='(' . implode(' or ',$vvv) . ')';
      
    } else {
      $mywhere_template="[[cfxxxxx]] like '%[[".$id."]]%'"; 
    }
  } else {
    if (is_array($id)) {
      $mywhere_template="[[cfxxxxx]] in (".implode(',',$id).")"; 
    } else {
      $mywhere_template="[[cfxxxxx]]=".$id; 
    }
  }
  
  $sql="SELECT gks_custom_field.id_custom_field, 
  gks_custom_table.custom_table_descr, 
  gks_custom_table.custom_table_name
  FROM gks_custom_field 
  LEFT JOIN gks_custom_table ON gks_custom_field.custom_table_id = gks_custom_table.id_custom_table
  WHERE gks_custom_field.field_type_id=".$id_custom_field_type."
  and gks_custom_field.field_disabled=0
  and gks_custom_table.custom_table_disabled=0";
  $ctables=[];
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  while ($row = $result->fetch_assoc()) {
    $ctables[]=$row;
  }
  
  foreach ($ctables as $vtable) {
    
    $mywhere=str_replace('[[cfxxxxx]]', 'cf'.$vtable['id_custom_field'], $mywhere_template);
    
    
    $sql="SELECT count(*) as cc FROM gks_customt_".$vtable['custom_table_name']." where ".$mywhere;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $cc=intval($row['cc']); 
      if ($cc>0) {
        $message=gks_lang('έχει χρησιμοποιηθεί σε <b>[[num]]</b>').' '.$vtable['custom_table_descr'].' ('.gks_lang('από Προσαρμογή').
        ($is_multi ? ' '.gks_lang('επιλογή πολλών') : '').
        ')';
        $message=str_replace('[[num]]',$cc,$message);
        $error_lines[]=$message;        
      }
    }
  } 
  //print '<pre>';print_r($ctables);die();
}

