<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


//die();


$obj='';
if (isset($_POST['obj'])) $obj=trim_gks(base64_decode($_POST['obj']));
if ($obj=='') {
  debug_mail(false,'the obj is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το αντικείμενο')));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Αποθήκευση Σειράς ταξινόμησης').' obj:'.$obj;
db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();
}

$list_str=''; if (isset($_POST['list_str'])) $list_str=trim_gks(base64_decode($_POST['list_str']));

$list_array = json_decode($list_str, true);
if ($list_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['list_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

if (count($list_array)==0) {
  debug_mail(false,'data not found (1)',print_r($list_array,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν δεδομένα').' (1)'));
  echo json_encode($return); die();}

$list_clean=array();
foreach ($list_array as $value) {
  $value=intval($value);
  if ($value>0) $list_clean[]=$value;
}
if (count($list_clean)==0) {
  debug_mail(false,'data not found (2)',print_r($list_array,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν δεδομένα').' (2)'));
  echo json_encode($return); die();}

 

$table=$obj;
$field_id='';
$field_sortorder='';
$filter='';
switch ($obj) {   
  case 'gks_assets_type':  
    $field_id='id_asset_type'; $field_sortorder='asset_type_sortorder'; break;
  case 'gks_assets_service_reasons':  
    $field_id='id_assets_service_reasons'; $field_sortorder='assets_service_reason_sortorder'; break;
  case 'gks_ads_campain':  
    $field_id='id_ads_campain'; $field_sortorder='ads_campain_sortorder'; break;
  case 'gks_acc_journal':  
    $field_id='id_acc_journal'; $field_sortorder='sortorder'; break;
  case 'gks_acc_seires':  
    $field_id='id_acc_seira'; $field_sortorder='sortorder'; break;
  case 'gks_banks':  
    $field_id='id_bank'; $field_sortorder='bank_sortorder'; break;
  case 'gks_company':  
    $field_id='id_company'; $field_sortorder='company_sortorder'; break;
  case 'gks_company_subs':  
    $field_id='id_company_sub'; $field_sortorder='company_sub_sortorder'; break;
  case 'gks_crm_channel_sale':  
    $field_id='id_crm_channel_sale'; $field_sortorder='crm_channel_sale_sortorder'; break;
  case 'gks_crm_leads_status':  
    $field_id='id_crm_lead_status'; $field_sortorder='lead_status_sortorder'; break;
  case 'gks_crm_tasks_status':  
    $field_id='id_crm_task_status'; $field_sortorder='task_status_sortorder'; break;
  case 'gks_delivery_methods':  
    $field_id='id_delivery_method'; $field_sortorder='mysortorder'; break;
  case 'gks_email_template':  
    $field_id='id_email_template'; $field_sortorder='sortorder'; break;
  case 'gks_erp_app':  
    $field_id='id_erp_app'; $field_sortorder='erp_app_sortorder'; break;
  case 'gks_erp_app_mobile':  
    $field_id='id_erp_app_mobile'; $field_sortorder='erp_app_mobile_sortorder'; break;
  case 'gks_eshop_fiscal_position':
    $field_id='id_fiscal_position'; $field_sortorder='fiscal_position_sortorder'; break;
  case 'gks_eshop_pricelist':
    $field_id='id_pricelist'; $field_sortorder='sortorder'; break;
  case 'gks_eshop_pricelist_items':
    $field_id='id_pricelist_item'; $field_sortorder='pricelist_item_sequence'; break;
  case 'gks_eshop_product_lots':  
    $field_id='id_lot_product'; $field_sortorder='lot_sortorder'; break;
  case 'gks_eshops':  
    $field_id='id_eshop'; $field_sortorder='eshop_sortorder'; break;
  case 'gks_hotel':
    $field_id='id_hotel'; $field_sortorder='hotel_sortorder'; break;
  case 'gks_hotel_floor':
    $field_id='id_hotel_floor'; $field_sortorder='sort_order'; break;
  case 'gks_hotel_room':
    $field_id='id_hotel_room'; $field_sortorder='room_sortorder'; break;
  case 'gks_hotel_room_type':
    $field_id='id_hotel_room_type'; $field_sortorder='room_type_sortorder'; break;
  
  
  case 'gks_monades_metrisis':  
    $field_id='id_monada'; $field_sortorder='monada_sortorder'; break;
  case 'gks_payment_acquirers':  
    $field_id='id_payment_acquirer'; $field_sortorder='mysortorder'; break;
  case 'gks_print_forms':  
    $field_id='id_print_form'; $field_sortorder='sortorder'; break;
  case 'gks_product_idiotites':  
    $field_id='id_product_idiotita'; $field_sortorder='idiotita_sortorder'; break;
  case 'gks_product_idiotites_terms':  
    $field_id='id_product_idiotita_term'; $field_sortorder='idiotita_term_sortorder'; break;
  case 'gks_production_ergasies':  
    $field_id='id_production_ergasia'; $field_sortorder='production_ergasia_sortorder'; break;
  case 'gks_production_posta':  
    $field_id='id_production_posto'; $field_sortorder='production_posto_sortorder'; break;
  case 'gks_urlshort':  
    $field_id='id_urlshort'; $field_sortorder='urlsort_sortorder'; break;
  case 'gks_warehouses':  
    $field_id='id_warehouse'; $field_sortorder='warehouse_sortorder'; break;

  case 'gks_template_html':
    $field_id='id_template_html'; $field_sortorder='sortorder'; break;
  case 'gks_transfer':
    $field_id='id_transfer'; $field_sortorder='transfer_sortorder'; break;
  case 'gks_transfer_area':
    $field_id='id_transfer_area'; $field_sortorder='sort_order'; break;
  case 'gks_transfer_oxima_type':
    $field_id='id_transfer_oxima_type'; $field_sortorder='sort_order'; break;
  
  case 'gks_poi':
    $field_id='id_poi'; $field_sortorder='poi_sortorder'; break;
  case 'gks_poi_type':
    $field_id='id_poi_type'; $field_sortorder='poi_type_sortorder'; break;

  case 'gks_lang':
    $field_id='idd_lang'; $field_sortorder='lang_sortorder'; break;
  case 'gks_sociallinks_type':
    $field_id='id_sociallinks_type'; $field_sortorder='sociallinks_type_sortorder'; break;
  case 'gks_sms_viber_template':
    $field_id='id_sms_viber_template'; $field_sortorder='sms_viber_template_sortorder'; break;

  case 'gks_aade_katigoria_loipon_foron':
    $field_id='id_aade_katigoria_loipon_foron'; $field_sortorder='sortorder'; break;
  case 'gks_aade_katigoria_telon':
    $field_id='id_aade_katigoria_telon'; $field_sortorder='sortorder'; break;
  case 'gks_aade_katigoria_parakratoumemenon_foron':
    $field_id='id_aade_katigoria_parakratoumemenon_foron'; $field_sortorder='sortorder'; break;
  case 'gks_aade_katigoria_xartosimou':
    $field_id='id_aade_katigoria_xartosimou'; $field_sortorder='sortorder'; break;
  case 'gks_aade_katigoria_fpa_ejeresi':
    $field_id='id_aade_katigoria_fpa_ejeresi'; $field_sortorder='sortorder'; break;
  case 'gks_aade_skopos_diakinisis':
    $field_id='id_aade_skopos_diakinisis'; $field_sortorder='sortorder'; break;
  case 'gks_acc_eidi_parastatikon':
    $field_id='id_acc_eidos_parastatikou'; $field_sortorder='sortorder'; break;
  case 'gks_users_favorites':
    $field_id='id_favorites'; $field_sortorder='fav_sortorder'; $filter=' and user_id='.$my_wp_user_id; break;
  case 'gks_voip_favorites':
    $field_id='id_voip_favorite'; $field_sortorder='mysortorder'; $filter=' and user_id='.$my_wp_user_id; break;
    
  default:
    debug_mail(false,'obj not found:'.$obj,'');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το αντικείμενο')));
    echo json_encode($return); die();

}

$debug_email_array=array();

$sql="select min(".$field_sortorder.") as mymin,max(".$field_sortorder.") as mymax
from ".$table."
where ".$field_id." in (".implode(',',$list_clean).")".$filter;
$debug_email_array[]=$sql;
//echo '<pre>';echo $sql; die();
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }
$mymin=-1;$mymax=-1;
if ($result->num_rows==1) {
  $row = $result->fetch_assoc();
  if (isset($row['mymin'])) $mymin=intval($row['mymin']);
  if (isset($row['mymax'])) $mymax=intval($row['mymax']);
}
$debug_email_array[]='mymin:'.$mymin;
$debug_email_array[]='mymax:'.$mymax;

$sql="select min(".$field_sortorder.") as upper_min 
from ".$table." 
where ".$field_id." not in (".implode(',',$list_clean).")
and ".$field_sortorder.">=".$mymin.$filter;
$debug_email_array[]=$sql;
//echo '<pre>';echo $sql; die();
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }
$upper_min=-1;
if ($result->num_rows==1) {
  $row = $result->fetch_assoc();
  if (isset($row['upper_min'])) $upper_min=intval($row['upper_min']);
}
$debug_email_array[]='upper_min:'.$upper_min;



$diafora=count($list_clean);
$block_start=$mymin+1;
$block_end=$block_start+$diafora;

$debug_email_array[]='diafora:'.$diafora;
$debug_email_array[]='block_start:'.$block_start;
$debug_email_array[]='block_end:'.$block_end;


foreach ($list_clean as $index => $value) {
  $sortorder_value=$block_start + $index;
  $sql="update ".$table." set ".$field_sortorder."=".$sortorder_value." where ".$field_id."=".$value;
  $debug_email_array[]=$sql;
  //echo '<pre>';echo $sql; die();
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
  
}
if ($upper_min>0) {
  $upper_block_diafora= $block_start + $diafora - $upper_min;
  if ($upper_block_diafora!=0) {
    $sql="update ".$table." 
    set ".$field_sortorder."=".$field_sortorder.($upper_block_diafora>=0 ? '+' : '').$upper_block_diafora."  
    where ".$field_id." not in (".implode(',',$list_clean).")
    and ".$field_sortorder.">=".$upper_min;
    $debug_email_array[]=$sql;
    //echo '<pre>';echo $sql; die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die(); }
  }
}

$sql="select min(".$field_sortorder.") as all_min 
from ".$table;
if ($filter!='') $sql.=" where 1=1".$filter;
$debug_email_array[]=$sql;
//echo '<pre>';echo $sql; die();
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }
$all_min=0;
if ($result->num_rows==1) {
  $row = $result->fetch_assoc();
  $all_min=intval($row['all_min']);
}
$to_one=1-$all_min;
$debug_email_array[]='to_one:'.$to_one;

if ($to_one!=0) {
  $sql="update ".$table." 
  set ".$field_sortorder."=".$field_sortorder.($to_one>=0 ? '+' : '').$to_one;
  if ($filter!='') $sql.=" where 1=1".$filter;
  $debug_email_array[]=$sql;
  //echo '<pre>';echo $sql; die();
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
}

$sql="select ".$field_id." as id, ".$field_sortorder." as so 
from ".$table." 
where ".$field_id." in (".implode(',',$list_clean).")".$filter;
$debug_email_array[]=$sql;
//echo '<pre>';echo $sql; die();
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }

$ret=array();
while ($row = $result->fetch_assoc()) {
  $ret[]=$row;
}

//debug_mail(false,'debug_email_array',print_r($debug_email_array,true));

if ($obj=='gks_users_favorites') {
  gks_cache_update_menu_version();
}

$return = array('success' => true, 'message' => base64_encode('OK'), 'ret'=>$ret);
echo json_encode($return); die();
  
  
$return = array('success' => false, 'message' => base64_encode('δδδδδδδδ <pre>obj '.$obj."\nmymin ".$mymin."\nmymax ".$mymax."\nupper_min ".$upper_min."\nsql ".$sql."\n".print_r($list_clean,true)));
echo json_encode($return); die();

