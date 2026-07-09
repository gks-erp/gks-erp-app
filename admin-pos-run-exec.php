<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$id_pos=0;
if (isset($_POST['id_pos'])) $id_pos=intval($_POST['id_pos']);
if ($id_pos<=0) {
  debug_mail(false,'the id_pos is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το POS ID')));
  echo json_encode($return); die();
}
$id=0;
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0 and $id<>-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}

$save_but_message_all='';


$my_page_title=gks_lang('Αποθήκευση Παραστατικού από id_pos').': '.$id_pos.' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_pos_run','add',$id_pos);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_pos_run_ids=gks_permission_user_condition($my_wp_user_id,'gks_pos_run','01');

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');


if (isset($_POST['acc_inv_pos_run8_sms_run'])) {
  $acc_inv_pos_run8_sms=trim_gks(base64_decode($_POST['acc_inv_pos_run8_sms']));
  $pos_sms_erp_app_mobile_id_code=trim_gks($_POST['pos_sms_erp_app_mobile_id_code']);
  $data_url=trim_gks(base64_decode($_POST['data_url']));
  $data_objid=intval($_POST['data_objid']);
  if ($pos_sms_erp_app_mobile_id_code=='' or $acc_inv_pos_run8_sms=='' or $data_url=='' or $data_objid<=0) {
    debug_mail(false,'the pos error sms data','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος δεδομένων').' (internel error 784510742)'));
    echo json_encode($return); die();}

  $sql="select * from gks_pos 
  where id_pos=".$id_pos." 
  and pos_sms_erp_app_mobile_id_code='".$db_link->escape_string($pos_sms_erp_app_mobile_id_code)."'
  and pos_disable=0";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  
  if ($result->num_rows<=0) {
    debug_mail(false,'pos not found sql',$sql); 
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το σημείο εντατικής λιανικής')));
    echo json_encode($return); die(); } 

  $row=$result->fetch_assoc();
  $pos_sms_text=trim_gks($row['pos_sms_template_text']);
  if ($pos_sms_text=='') $pos_sms_text='[url]';
  $pos_sms_text=str_replace('[url]', $data_url, $pos_sms_text);
  
  $sender_parts=explode(':',$pos_sms_erp_app_mobile_id_code);
  if (!(count($sender_parts)==2 and 
     (($sender_parts[0]=='smsapi' and $sender_parts[1]!='') or
      ($sender_parts[0]=='gks_erp_app_mobile' and intval($sender_parts[1])>0)))) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος δεδομένα για αποστολή SMS')));
    echo json_encode($return); die(); } 
  
  
                        //$model,   $model_id,   $from,                     $to,                   $szMessageText,$sender_sms_provider
  $ret_sms=gks_sms_send('gks_acc_inv',$data_objid,$sender_parts[1],$acc_inv_pos_run8_sms,$pos_sms_text,$sender_parts[0]);
  
  if ($ret_sms) {
    $return = array('success' => true, 'message' => base64_encode(gks_lang('Επιτυχής αποστολή')));
  } else {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής')));
  }
  echo json_encode($return); die();  

  $return = array('success' => false, 'message' => base64_encode('debug <pre>'.$acc_inv_pos_run8_sms."\n".$pos_sms_text."\n".$data_url."\n".$data_objid."\n".print_r($_POST,true).'</pre>'));
  echo json_encode($return); die();  
}


$gks_run_until='';if (isset($_POST['gks_run_until'])) $gks_run_until=trim_gks($_POST['gks_run_until']);
$pos_step=''; if (isset($_POST['pos_step'])) $pos_step=trim_gks($_POST['pos_step']);

//echo '<pre>ssssssssssssssss '.$gks_run_until;die();
 
if ($id>0 or $pos_step!='') {
  if ($pos_step=='') {
    debug_mail(false,'the pos_step is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' pos_step.'));
    echo json_encode($return); die();}
  if ($id<=0) {
    debug_mail(false,'the id is not set (2)','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID (2)'));
    echo json_encode($return); die();}
  $sql="select pos_step from gks_acc_inv where id_acc_inv=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  
  if ($result->num_rows<=0) {
    debug_mail(false,'prev record not found sql',$sql); 
    die('no record found (tempate)');  }

  $row=$result->fetch_assoc(); 
  $pos_step_db=$row['pos_step'];
  
  if ($pos_step_db!=$pos_step) {
    debug_mail(false,'pos_step_db pos_step',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατάστασης').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die(); } 
  
  //echo '<pre>'.$id.'|'.$pos_step_db;die();
  
  if ($pos_step=='01draft_start') $pos_step='';//ksana apo tin arxi
}



$sql="SELECT gks_pos.*, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, 
gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_country_id,gks_users.ma_nomos_id,
".GKS_WP_TABLE_PREFIX."users.generic_ekprosi,
".GKS_WP_TABLE_PREFIX."users.user_email,
".GKS_WP_TABLE_PREFIX."users.gks_mobile,

gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
gks_acc_seires.send_mydata,gks_acc_seires.send_paroxos,
gks_acc_journal.acc_eidos_parastatikou_id,
gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, antisimvalomenos_label, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
gks_lang.lang_name,gks_country.country_name,gks_nomoi.nomos_descr,
myfirst_name,mylast_name,
gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_warehouses_from.warehouse_name AS warehouse_name_from, gks_warehouses_to.warehouse_name AS warehouse_name_to

FROM ((((((((((((((((((((((((gks_pos
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_pos.def_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_company on gks_pos.pos_company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_pos.pos_company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_pos.pos_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_pos.pos_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_payment_acquirers ON gks_pos.def_tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
LEFT JOIN gks_delivery_methods ON gks_pos.def_tropos_apostolis = gks_delivery_methods.id_delivery_method) 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_eshop_fiscal_position ON gks_pos.def_fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_pos.def_pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_lang ON gks_pos.def_user_lang = gks_lang.id_lang)
LEFT JOIN gks_aade_skopos_diakinisis ON gks_pos.def_aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_pos.def_assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_pos.def_crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_pos.def_crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_pos.def_crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_warehouses AS gks_warehouses_from ON gks_pos.pos_warehouses_id_from = gks_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_warehouses_to ON gks_pos.pos_warehouses_id_to = gks_warehouses_to.id_warehouse)
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
)  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
)  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id


where id_pos=".$id_pos;
if (count($perm_id_pos_run_ids)>0) $sql.=" and gks_pos.id_pos in (".implode(',',$perm_id_pos_run_ids).")";

if (count($perm_id_company_ids)>0) $sql.=" and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
}
  
if ($result->num_rows!=1) {
  debug_mail(false,'record not found sql tempate',$sql); 
  die('no record found (tempate)');
}
$row_pos=$result->fetch_assoc();

$inv_date=date('Y-m-d H:i:s');
$company_id=$row_pos['pos_company_id'];
$company_sub_id=$row_pos['pos_company_sub_id'];
$inv_acc_seira_id=$row_pos['pos_seira_id'];
$inv_acc_journal_id=$row_pos['pos_journal_id'];
$aade_skopos_diakinisis_id=$row_pos['def_aade_skopos_diakinisis_id'];

$user_id=$row_pos['def_user_id'];
$fiscal_position_id=$row_pos['def_fiscal_position_id'];
$pricelist_id=$row_pos['def_pricelist_id'];
$dr_user_first_name=trim_gks($row_pos['myfirst_name']);
$dr_user_last_name=trim_gks($row_pos['mylast_name']);
$dr_user_email=trim_gks($row_pos['user_email']);
$dr_user_mobile=trim_gks($row_pos['gks_mobile']);
$dr_user_lang=trim_gks($row_pos['def_user_lang']); //trim_gks($row_pos['lang_name']);
$dr_user_ma_odos=trim_gks($row_pos['ma_odos']);
$dr_user_ma_arithmos=trim_gks($row_pos['ma_arithmos']);
$dr_user_ma_orofos=trim_gks($row_pos['ma_orofos']);
$dr_user_ma_perioxi=trim_gks($row_pos['ma_perioxi']);
$dr_user_ma_poli=trim_gks($row_pos['ma_poli']);
$dr_user_ma_tk=trim_gks($row_pos['ma_tk']);
$dr_user_ma_country_id=$row_pos['ma_country_id'];
$dr_user_ma_nomos_id=$row_pos['ma_nomos_id'];



$dr_user_eponimia=trim_gks($row_pos['eponimia']);
$dr_user_title=trim_gks($row_pos['title']);
$dr_user_afm=trim_gks($row_pos['afm']);
$dr_user_doy=trim_gks($row_pos['doy']);
$dr_user_epaggelma=trim_gks($row_pos['epaggelma']);
$form_select_apostoli=-1;

$destination_data_name='';
$destination_data_phone='';
$destination_data_odos='';
$destination_data_arithmos='';
$destination_data_orofos='';
$destination_data_perioxi='';
$destination_data_poli='';
$destination_data_tk='';
$destination_data_country_id=0;
$destination_data_nomos_id=0;
$note_doc='';
$note_logistirio='';
$tropos_apostolis=$row_pos['def_tropos_apostolis'];
if (isset($_POST['pway'])) $row_pos['def_tropos_pliromis']=intval($_POST['pway']);
$tropos_pliromis=$row_pos['def_tropos_pliromis'];

$delivery_id_8=$row_pos['def_delivery_id_8'];
$delivery_number='';
$vehicle_number='';
$dispatch_date='';

$def_ekptosi=$row_pos['generic_ekprosi'];
$coupons_str='';
$affect_balance=$row_pos['def_affect_balance'];
$affect_balance_all_poso=$row_pos['def_affect_balance_all_poso'];
$affect_balance_all_poso_type=$row_pos['def_affect_balance_all_poso_type'];


$assigned_id=$row_pos['def_assigned_id'];
$crm_channel_id=$row_pos['def_crm_channel_id'];
$crm_channel_contact_id=$row_pos['def_crm_channel_contact_id'];
$crm_channel_campain_id=$row_pos['def_crm_channel_campain_id'];
$crm_channel_url=$row_pos['def_crm_channel_url'];
$crm_channel_code=$row_pos['def_crm_channel_code'];
$crm_channel_text=$row_pos['def_crm_channel_text'];

$pos_user_can_change_prices=(intval($row_pos['pos_user_can_change_prices'])==1 ? true : false);
$js_pos_user_can_change_prices=false;if (isset($_POST['js_pos_user_can_change_prices'])) $js_pos_user_can_change_prices=intval($_POST['js_pos_user_can_change_prices'])==1;
if ($pos_user_can_change_prices!=$js_pos_user_can_change_prices) {
  debug_mail(false,'pos_user_can_change_prices conflict js-php',$sql);
}
$js_pos_user_can_change_prices=$pos_user_can_change_prices;

//echo '<pre>'.$pos_user_can_change_prices; die();
//////////////////
$customer_has_open=0; if (isset($_POST['customer_has_open'])) $customer_has_open=intval($_POST['customer_has_open']);
if ($customer_has_open==1) {
  $user_id=intval($_POST['user_id']);
  $dr_user_first_name=trim_gks(base64_decode($_POST['dr_user_first_name']));
  $dr_user_last_name=trim_gks(base64_decode($_POST['dr_user_last_name']));
  $dr_user_email=trim_gks(base64_decode($_POST['dr_user_email']));
  $dr_user_mobile=trim_gks(base64_decode($_POST['dr_user_mobile']));
  $dr_user_lang=trim_gks(base64_decode($_POST['dr_user_lang'])); 
  $dr_user_ma_odos=trim_gks(base64_decode($_POST['dr_user_ma_odos']));
  $dr_user_ma_arithmos=trim_gks(base64_decode($_POST['dr_user_ma_arithmos']));
  $dr_user_ma_orofos=trim_gks(base64_decode($_POST['dr_user_ma_orofos']));
  $dr_user_ma_perioxi=trim_gks(base64_decode($_POST['dr_user_ma_perioxi']));
  $dr_user_ma_poli=trim_gks(base64_decode($_POST['dr_user_ma_poli']));
  $dr_user_ma_tk=trim_gks(base64_decode($_POST['dr_user_ma_tk']));
  $dr_user_ma_country_id=intval($_POST['dr_user_ma_country_id']);
  $dr_user_ma_nomos_id=intval($_POST['dr_user_ma_nomos_id']);
  
} 
//echo '<pre>|'.$customer_has_open.'|'.$user_id.'|'.$dr_user_ma_nomos_id.'|'.$dr_user_ma_country_id;die();

if ($user_id<1) {
  debug_mail(false,'user_id zero',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε πελάτη')));
  echo json_encode($return); die();}  
  


$sql="SELECT gks_acc_seires.id_acc_seira,gks_acc_seires.is_xeirografi
FROM (((gks_acc_seires 
LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE eidos_parastatikou_type_id in (1,2,5) AND id_acc_eidos_parastatikou not in (702,703,704)
AND gks_acc_seires.is_disable=0 AND gks_acc_journal.is_disable=0 
AND gks_company.company_disable=0 
AND gks_acc_seires.id_acc_seira=".$inv_acc_seira_id." 
AND gks_acc_journal.id_acc_journal=".$inv_acc_journal_id." 
AND gks_company.id_company=".$company_id;
//$save_but_message='<pre>'.$sql;

if (count($perm_id_company_ids)>0) $sql.=" and gks_company.id_company in (".implode(',',$perm_id_company_ids).")";
if ($company_sub_id>0) {
  $sql.=" AND gks_company_subs.company_sub_disable=0 AND gks_company_subs.id_company_sub=".$company_sub_id;
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_company_subs.id_company_sub in (".implode(',',$perm_id_company_sub_ids).")";
} else {
  $sql.=" AND gks_acc_journal.company_sub_id=0";
  if (count($perm_id_company_sub_ids)>0 and in_array(0,$perm_id_company_sub_ids)==false) $sql.=" and 1=2";
}
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_journal.id_acc_journal in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_seires.id_acc_seira in (".implode(',',$perm_id_acc_seira_ids).")";


$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows!=1) {
  debug_mail(false,'company journa seira',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο συνδυασμός εταιρείας/υποκαταστήματος, ημερολογίου και σειράς')));
  echo json_encode($return); die();}
$row_seira = $result->fetch_assoc();
$is_xeirografi=$row_seira['is_xeirografi'];


$eidos_parastatikou_type_id=0;
$eidos_parastatikou_need_afm=0;
$eidos_parastatikou_has_fpa=1;
$affect_balance_pros=0;
$whi_eidos_parastatikou_stock_pros=0;
$whi_eidos_parastatikou_stock_pros_org=0;
$whi_eidos_parastatikou_type_id=0;
$whi_eidos_parastatikou_type_id_org=0;

//die('<pre>|'.$gks_lock.'|'.$gks_number_lock.'|'.$gks_user_lock.'|');
if ($inv_acc_journal_id>0) {
  $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,
  gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
  gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id
  FROM (gks_acc_journal 
  LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
  WHERE gks_acc_journal.id_acc_journal=".$inv_acc_journal_id." and gks_acc_eidi_parastatikon.eidos_parastatikou_type_id>0";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $eidos_parastatikou_type_id=$row['eidos_parastatikou_type_id'];
    $eidos_parastatikou_need_afm=$row['eidos_parastatikou_need_afm'];
    $eidos_parastatikou_has_fpa=$row['eidos_parastatikou_has_fpa'];
    $eidos_parastatikou_aade_code=$row['eidos_parastatikou_aade_code'];
    $affect_balance_pros=$row['eidos_parastatikou_balance_pros'];
    $whi_eidos_parastatikou_stock_pros=$row['whi_eidos_parastatikou_stock_pros'];
    $whi_eidos_parastatikou_stock_pros_org=$whi_eidos_parastatikou_stock_pros;
    $whi_eidos_parastatikou_type_id=$row['whi_eidos_parastatikou_type_id'];
    $whi_eidos_parastatikou_type_id_org=$whi_eidos_parastatikou_type_id;
    if ($eidos_parastatikou_aade_code=='5.1' and $credit_memo_for_acc_inv_id<=0) {
      $message=gks_lang('Παραστατικά με ημερολόγιο το οποίο έχει ως τύπο παραστατικού το <b>Πιστωτικό Τιμολόγιο / Συσχετιζόμενο</b> δεν μπορούν να δημιουργηθούν άμεσα').
      '<br>'.
      gks_lang('Θα πρέπει να δημιουργηθούν μέσα από το συσχετιζόμενο παραστατικό');
      
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' => base64_encode($message));
      echo json_encode($return); die(); 
    }
  }
  //echo '<pre>';print_r($row);die();
  
}


if ($eidos_parastatikou_type_id<=0) {
  debug_mail(false,'eidos_parastatikou_type_id empty',$inv_acc_journal_id.' '.$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο γενικός τύπου του παραστατικού')));
  echo json_encode($return); die();}

$id_acc_eidos_parastatikou=0;
if ($inv_acc_journal_id>0) {
  $sql="select acc_eidos_parastatikou_id from gks_acc_journal where id_acc_journal=".$inv_acc_journal_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();  
    $id_acc_eidos_parastatikou=$row['acc_eidos_parastatikou_id'];
  }
}


$inv_state='010draft';

$warehouses_id_from=0; if (isset($row_pos['pos_warehouses_id_from'])) $warehouses_id_from=intval($row_pos['pos_warehouses_id_from']);
$warehouses_id_to=0;   if (isset($row_pos['pos_warehouses_id_to']))   $warehouses_id_to=intval($row_pos['pos_warehouses_id_to']);

$warehouses_id_from_is_virtual=false;
$warehouses_id_to_is_virtual=false;
if ($whi_eidos_parastatikou_type_id_org==null) $whi_eidos_parastatikou_type_id_org=0;

if ($whi_eidos_parastatikou_type_id_org==0) {
  $warehouses_id_from=0;
  $warehouses_id_to=0;
  //echo 'hhh ';var_dump($whi_eidos_parastatikou_type_id_org);die();
} else {
  if ($whi_eidos_parastatikou_type_id_org==24) { //apografi
    $warehouses_id_from=0;  
//    $aade_skopos_diakinisis_id=0;
//    $pricelist_id=0;
//    $fiscal_position_id=0;
//    $tropos_apostolis=1; //den apiteitai apostoli
    
  } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
    
  } else {
    if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
        $warehouses_id_from=1; //virtual warehouse pelates
        $warehouses_id_from_is_virtual=true;
      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutes
        $warehouses_id_from=2; //virtual warehouse promitheutes
        $warehouses_id_from_is_virtual=true;
      }
    } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
        $warehouses_id_to=1; //virtual warehouse pelates
        $warehouses_id_to_is_virtual=true;
      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutes
        $warehouses_id_to=2; //virtual warehouse promitheutes
        $warehouses_id_to_is_virtual=true;
      }
    }
  }
  
  //echo '<pre>'.$whi_eidos_parastatikou_type_id_org;die();
  
  
  if ($warehouses_id_from>0 and $warehouses_id_from_is_virtual==false) { //ektos virtual
    $sql="select * from gks_warehouses where is_virtual=0 and id_warehouse=".$warehouses_id_from;
    if ($whi_eidos_parastatikou_type_id!=23) { //not endodiakinisi
      if ($company_id>0) $sql.=" and company_id=".$company_id;
      if ($company_sub_id==0) $sql.=" and company_sub_id=0";
      else if ($company_sub_id>0) $sql.=" and company_sub_id=".$company_sub_id;
    }
    
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      debug_mail(false,'warehouses_id_from not found',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η αποθήκη <b>Από</b>')));
      echo json_encode($return); die();}  
  }
  if ($warehouses_id_to>0 and $warehouses_id_to_is_virtual==false) { //ektos virtual
    $sql="select * from gks_warehouses where is_virtual=0 and id_warehouse=".$warehouses_id_to;
    if ($whi_eidos_parastatikou_type_id!=23) { //not endodiakinisi
      if ($company_id>0) $sql.=" and company_id=".$company_id;
      if ($company_sub_id==0) $sql.=" and company_sub_id=0";
      else if ($company_sub_id>0) $sql.=" and company_sub_id=".$company_sub_id;
    }
    
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      debug_mail(false,'warehouses_id_from not found',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η αποθήκη <b>Προς</b>')));
      echo json_encode($return); die();}  
  }


  if ($whi_eidos_parastatikou_type_id==23 or $whi_eidos_parastatikou_type_id==24) { //endodiakinisi, apografi
//    $user_id=0;
//    $dr_user_first_name=''; 
//    $dr_user_last_name='';
//    $dr_user_email='';
//    $dr_user_mobile='';
//    $dr_user_lang='';
//    $dr_user_ma_odos='';
//    $dr_user_ma_perioxi='';
//    $dr_user_ma_poli='';
//    $dr_user_ma_tk='';
//    $dr_user_ma_country_id=0;
//    $dr_user_ma_nomos_id=0;
//    $dr_user_eponimia='';
//    $dr_user_title='';
//    $dr_user_afm='';
//    $dr_user_doy='';
//    $dr_user_epaggelma='';
//    
//    $form_select_apostoli=-1;
//    $form_ea_name='';
//    $form_ea_phone='';
//    $form_ea_odos='';
//    $form_ea_perioxi='';
//    $form_ea_poli='';
//    $form_ea_tk='';
//    $form_ea_country_id=0; 
//    $form_ea_nomos_id=0;
//    
//    $destination_data_name='';
//    $destination_data_phone='';
//    $destination_data_odos='';
//    $destination_data_perioxi='';
//    $destination_data_poli='';
//    $destination_data_tk='';
//    $destination_data_country_id=0;
//    $destination_data_nomos_id=0;  
  }
  
  if ($whi_eidos_parastatikou_type_id==21 or $whi_eidos_parastatikou_type_id==22) { //deltio apostolh/paralavis
    if ($user_id<=0) {
      debug_mail(false,'user_id zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή (πελάτη/προμηθευτή)')));
      echo json_encode($return); die();}
    
    if ($warehouses_id_from<=0) {
      debug_mail(false,'user_id zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
      echo json_encode($return); die();}
    
    if ($warehouses_id_to<=0) {
      debug_mail(false,'warehouses_id_to zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
      echo json_encode($return); die();}
  }
    
  if ($whi_eidos_parastatikou_type_id==23) { //endodiakinisi
    if ($warehouses_id_from<=0) {
      debug_mail(false,'warehouses_id_from zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
      echo json_encode($return); die();}
  
    if ($warehouses_id_to<=0) {
      debug_mail(false,'warehouses_id_to zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
      echo json_encode($return); die();}
  }
    
  
  if ($whi_eidos_parastatikou_type_id==24) { //apografi
    if ($warehouses_id_to<=0) {
      debug_mail(false,'apografi warehouses_id_to zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη που <b>Αφορά</b> η απογραφή')));
      echo json_encode($return); die();}
  }
  
  
  
  if ($warehouses_id_from==$warehouses_id_to) {
    debug_mail(false,'warehouses_id_from warehouses_id_to ','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η αποθήκη <b>Από</b> πρέπει να είναι διαφορετική από την αποθήκη <b>Προς</b>')));
    echo json_encode($return); die();}    




//
////////////    
//    
//    if ($whi_eidos_parastatikou_type_id==23 and $warehouses_id_from<=0) {
//      debug_mail(false,'whi_eidos_parastatikou_type_id 23 warehouses_id_from','');
//      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
//      echo json_encode($return); die();}
//  
//    if ($whi_eidos_parastatikou_type_id==24) {
//      if ($warehouses_id_to<=0) {
//        debug_mail(false,'whi_eidos_parastatikou_type_id 23 warehouses_id_to','');
//        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη που <b>Αφορά</b> η απογραφή')));
//        echo json_encode($return); die();}
//    } else {
//      if ($warehouses_id_to<=0) {
//        debug_mail(false,'warehouses_id_to zero','');
//        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
//        echo json_encode($return); die();}
//    }
//    
//    if ($warehouses_id_from==$warehouses_id_to) {
//      debug_mail(false,'warehouses_id_from warehouses_id_to','');
//      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η αποθήκη <b>Από</b> πρέπει να είναι διαφορετική από την αποθήκη <b>Προς</b>')));
//      echo json_encode($return); die();}    
//       
//  } else {
////    if ($user_id<=0) {
////      debug_mail(false,'user_id zero','');
////      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή (πελάτη/προμηθευτή)')));
////      echo json_encode($return); die();}
//  }

}




//echo '<pre>'.$js_pos_user_can_change_prices;die();

$merchant_ref_trns=''; if (isset($_POST['merchant_ref_trns'])) $merchant_ref_trns=trim_gks(base64_decode($_POST['merchant_ref_trns']));
$screen_width=0; if (isset($_POST['screen_width'])) $screen_width=intval($_POST['screen_width']);
$screen_height=0; if (isset($_POST['screen_height'])) $screen_height=intval($_POST['screen_height']);
$device_type=''; if (isset($_POST['device_type'])) $device_type=trim_gks(base64_decode($_POST['device_type']));
$my_gps_location_lat=0; if (isset($_POST['my_gps_location_lat'])) $my_gps_location_lat=floatval(base64_decode($_POST['my_gps_location_lat']));
$my_gps_location_lng=0; if (isset($_POST['my_gps_location_lng'])) $my_gps_location_lng=floatval(base64_decode($_POST['my_gps_location_lng']));

//echo '<pre>'.$screen_width.' '.$screen_height.' '.$device_type;die();

$eidi_array_str = trim_gks(base64_decode($_POST['eidi_array_str']));
$eidi_array = json_decode($eidi_array_str, true);
if ($eidi_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['eidi_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

if (count($eidi_array)==0) {
  debug_mail(false,'eidi_array empty',print_r($eidi_array,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχετε προσθέσει είδη')));
  echo json_encode($return); die();}

foreach ($eidi_array as $vkey => $value) {
  if ($value['product_totalprice'] < 0.01) {
    debug_mail(false,'product_totalprice zero',print_r($eidi_array,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',($vkey+1),gks_lang('Στην γραμμή [1] το είδος δεν έχει αξία'))));
    echo json_encode($return); die();
  }
} 
//print '<pre>';print_r($eidi_array);die();

$id_product_ids=array();
foreach ($eidi_array as $value) {
  $id_product_ids[]=$value['product_id'];
} 
//print '<pre>';print_r($id_product_ids);die();

$sql="select id_product,product_monada_id,product_fpa_base_id,
product_withheldPercentCategory,product_otherTaxesPercentCategory,product_stampDutyPercentCategory,product_feesPercentCategory,
product_fpa_ejeresi_id
from gks_eshop_products where id_product in (".implode(',',$id_product_ids).")";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

while ($row = $result->fetch_assoc()) {
  foreach ($eidi_array as &$value) {
    if ($value['product_id']==$row['id_product']) {
      $value['product_monada_id']=$row['product_monada_id'];
      $value['product_fpa_base_id']=$row['product_fpa_base_id'];
      $value['product_withheldPercentCategory']=$row['product_withheldPercentCategory'];
      $value['product_withheldAmount']=0;
      $value['product_otherTaxesPercentCategory']=$row['product_otherTaxesPercentCategory'];
      $value['product_otherTaxesAmount']=0;
      $value['product_stampDutyPercentCategory']=$row['product_stampDutyPercentCategory'];
      $value['product_stampDutyAmount']=0;
      $value['product_feesPercentCategory']=$row['product_feesPercentCategory'];
      $value['product_feesAmount']=0;
      $value['product_deductionsAmount']=0;
      
      $value['product_fpa_ejeresi_id']=$row['product_fpa_ejeresi_id'];
      
      //$value['product_fpa_ejeresi_id']=$value[''];
      
      //break;  
    }
    
  }
  unset($value);
  
  
}



//echo '<pre>|'.$customer_has_open.'|'.$user_id.'|'.$dr_user_ma_nomos_id.'|'.$dr_user_ma_country_id;die();

//print '<pre>';print_r($eidi_array);die();
  

unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);

$mybasketarray['from']='acc_inv';
$mybasketarray['id_object'] = -1;
$mybasketarray['company_id']=intval($row_pos['pos_company_id']);
$mybasketarray['company_sub_id']=intval($row_pos['pos_company_sub_id']);
$mybasketarray['inv_acc_journal_id']=intval($row_pos['pos_journal_id']);
$mybasketarray['inv_acc_seira_id']=intval($row_pos['pos_seira_id']);
$mybasketarray['inv_state']=$inv_state;
$mybasketarray['inv_date']=date('Y-m-d H:i:s'); //2022-05-19 22:32:00
//echo '<pre>'; echo $mybasketarray['inv_date']; die();

$mybasketarray['user']['user_id']=$user_id; //$row_pos['def_user_id'];
$mybasketarray['user']['first_name']=$dr_user_first_name;//trim_gks($row_pos['myfirst_name']);
$mybasketarray['user']['last_name']=$dr_user_last_name;//trim_gks($row_pos['mylast_name']);
$mybasketarray['user']['email']=$dr_user_email;//trim_gks($row_pos['user_email']);
$mybasketarray['user']['mobile']=$dr_user_mobile;//trim_gks($row_pos['gks_mobile']);
$mybasketarray['user']['lang']=$dr_user_lang;//trim_gks($row_pos['def_user_lang']);
$mybasketarray['user']['ma_odos']=$dr_user_ma_odos;//trim_gks($row_pos['ma_odos']);
$mybasketarray['user']['ma_arithmos']=$dr_user_ma_arithmos;//trim_gks($row_pos['ma_arithmos']);
$mybasketarray['user']['ma_orofos']=$dr_user_ma_orofos;//trim_gks($row_pos['ma_orofos']);
$mybasketarray['user']['ma_perioxi']=$dr_user_ma_perioxi;//trim_gks($row_pos['ma_perioxi']);
$mybasketarray['user']['ma_poli']=$dr_user_ma_poli;//trim_gks($row_pos['ma_poli']);
$mybasketarray['user']['ma_tk']=$dr_user_ma_tk;//trim_gks($row_pos['ma_tk']);
$mybasketarray['user']['ma_country_id']=$dr_user_ma_country_id;//$row_pos['ma_country_id'];
$mybasketarray['user']['ma_nomos_id']=$dr_user_ma_nomos_id;//$row_pos['ma_nomos_id'];
$mybasketarray['user']['eponimia']=trim_gks($row_pos['eponimia']);
$mybasketarray['user']['title']=trim_gks($row_pos['title']);
$mybasketarray['user']['afm']=trim_gks($row_pos['afm']);
$mybasketarray['user']['doy']=trim_gks($row_pos['doy']);
$mybasketarray['user']['epaggelma']=trim_gks($row_pos['epaggelma']);
$mybasketarray['address_extra']=-1;
$mybasketarray['destination_data']['name'] = '';
$mybasketarray['destination_data']['phone'] = '';
$mybasketarray['destination_data']['odos'] = '';
$mybasketarray['destination_data']['arithmos'] = '';
$mybasketarray['destination_data']['orofos'] = '';
$mybasketarray['destination_data']['perioxi'] = '';
$mybasketarray['destination_data']['poli'] =  '';
$mybasketarray['destination_data']['tk'] = '';
$mybasketarray['destination_data']['country_id'] = 0;
$mybasketarray['destination_data']['nomos_id'] = 0;
if ($mybasketarray['destination_data']['country_id']==0) $mybasketarray['destination_data']['country_id']=91;



$mybasketarray['fiscal_position']=intval($row_pos['def_fiscal_position_id']);
if ($mybasketarray['fiscal_position']<1) $mybasketarray['fiscal_position']=1;

$mybasketarray['pricelist_id']=intval($row_pos['def_pricelist_id']);
if ($mybasketarray['pricelist_id']<1) $mybasketarray['pricelist_id']=1;
$mybasketarray['coupons']=array();


$mybasketarray['parastatiko']=intval($row_pos['eidos_parastatikou_need_afm']);


$mybasketarray['products_need_apostoli'] = 1; //intval($mydata['gks_products_need_apostoli'])!=0;
$mybasketarray['products_varos']= 0; //intval($mydata['gks_products_varos']);
$mybasketarray['products_ogos']= 0; //intval($mydata['gks_products_ogos']);
$mybasketarray['products_ogos_max_x']= 0; //intval($mydata['gks_products_ogos_x']);
$mybasketarray['products_ogos_max_y']= 0; //intval($mydata['gks_products_ogos_y']);
$mybasketarray['products_ogos_max_z']= 0; //intval($mydata['gks_products_ogos_z']);
$mybasketarray['products_need_pliromi']=false;

$mybasketarray['tropos_apostolis'] = intval($row_pos['def_tropos_apostolis']);
$mybasketarray['tropos_pliromis'] = intval($row_pos['def_tropos_pliromis']);
$mybasketarray['products_total'] = 0; //floatval($mydata['gks_total_price_total']);

$fields_change=array();//$mydata['fields_change'];
$fields_change[0]='';
$fields_change[1]='gks_price';

$fields_change_curr_name='code'; //'gks_quantity'; //trim_gks($mydata['fields_change_curr_name']);
$fields_change_curr_aa=1; //intval($mydata['fields_change_curr_aa']);

$basket_products_temp =array();
foreach ($eidi_array as &$value) {
  $user_field_change='';
  if ($value['aa'] == $fields_change_curr_aa) $user_field_change=$fields_change_curr_name;
  $user_change_ekptosi_or_final_net='';
  if (isset($fields_change[$value['aa']])) $user_change_ekptosi_or_final_net=$fields_change[$value['aa']];
  
  $user_ekptosi = 0; //floatval($value['product_price_ekptosi_pososto']);  
  
  


  $hotel_check_out_round  = showDate(time(),'Y-d-m',1);
  $hotel_check_in_round = showDate(time()-$value['product_quantity']*24*60*60,'Y-d-m',1);
  if ($mybasketarray['inv_date']!='') {
    $hotel_check_out_round  = date('Y-d-m',strtotime($mybasketarray['inv_date']));
    $hotel_check_in_round = date('Y-d-m',strtotime($mybasketarray['inv_date'])-$value['product_quantity']*24*60*60);
  }

 
  //print '<pre>';print_r($value);die();
  
  $objects=array();
  $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $value['product_quantity'],  'files' => array(), 'warnings'=>array());
  $basket_products_temp[$value['aa']]=array(
    'product_id'=>array(
      'id_product'=>$value['product_id'], 
      'product_monada_id' => $value['product_monada_id'], 
      'product_fpa_base_id' => $value['product_fpa_base_id'], 
      'product_sheets'=>0, 
      'product_set' => '',
     ), 
    'objects'=>$objects,


//    'user_ekptosi' => 0, //$user_ekptosi,
//    'user_final_net' => 0, //floatval($value['product_price_final_all_net']),
//    'user_change_ekptosi_or_final_net' => 'gks_ekptosi', //$user_change_ekptosi_or_final_net,
//    'user_field_change' => 'gks_ekptosi', //$user_field_change,


//    'user_final_net' => 0 ,
//    'user_final_total' => 10 ,
//    'user_change_ekptosi_or_final_net' => 'gks_price_final',
//    'user_field_change' => 'gks_price_final',

    'from_aade_import_user_fpa' => 0, //$value['from_aade_import_user_fpa'],
    'from_aade_import_user_fpa_value' => 0, //$value['from_aade_import_user_fpa_value'],
    'product_fpa_ejeresi_id' => $value['product_fpa_ejeresi_id'],
    
    
    
//    'user_check_in'=> $hotel_check_in_round,
//    'user_check_out'=> $hotel_check_out_round,
//    'user_room_id' => 10062,
//    'user_rnum_adults' => 1,
//    'user_rnum_childs' => 0,
//    'user_rchilds_ages_list' => '',

    
    'other_taxes' => array(
      'withheldPercentCategory' => intval($value['product_withheldPercentCategory']),  
      'withheldAmount' => floatval($value['product_withheldAmount']),  
      'otherTaxesPercentCategory' => intval($value['product_otherTaxesPercentCategory']),  
      'otherTaxesAmount' => floatval($value['product_otherTaxesAmount']),  
      'stampDutyPercentCategory' => intval($value['product_stampDutyPercentCategory']),  
      'stampDutyAmount' => floatval($value['product_stampDutyAmount']), 
      'feesPercentCategory' => intval($value['product_feesPercentCategory']),  
      'feesAmount' => floatval($value['product_feesAmount']),  
      'deductionsAmount' => floatval($value['product_deductionsAmount']),  
    
    ),
    
  );

//     => 0, //$user_ekptosi,
//     => 0, //floatval($value['product_price_final_all_net']),
//     => 
//     => 
  if ($js_pos_user_can_change_prices==false) {
    $basket_products_temp[$value['aa']]['user_ekptosi']=0;
    $basket_products_temp[$value['aa']]['user_final_net']=0;
    $basket_products_temp[$value['aa']]['user_change_ekptosi_or_final_net']='gks_ekptosi'; //$user_change_ekptosi_or_final_net,
    $basket_products_temp[$value['aa']]['user_field_change']='gks_ekptosi'; //$user_field_change,
  } else {
    $basket_products_temp[$value['aa']]['user_final_net'] = 0;
    $basket_products_temp[$value['aa']]['user_final_total'] = floatval($value['product_totalprice']);
    $basket_products_temp[$value['aa']]['user_change_ekptosi_or_final_net'] = 'gks_price_final';
    $basket_products_temp[$value['aa']]['user_field_change'] = 'gks_price_final';
  }
  
  
}
unset($value);
//print '<pre>';print_r($basket_products_temp);die();  


$mybasketarray['products'] = $basket_products_temp;
$myproducts = gks_basket_recalc($mybasketarray, $fields_change, array());

//print '<pre>';print_r($mybasketarray);die();  

$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);
$mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);

$kostos_apostolis_mode=''; if (isset($mydata['kostos_apostolis_mode'])) $kostos_apostolis_mode=trim_gks($mydata['kostos_apostolis_mode']);
$kostos_pliromis_mode='';  if (isset($mydata['kostos_pliromis_mode']))  $kostos_pliromis_mode= trim_gks($mydata['kostos_pliromis_mode']);

if ($kostos_apostolis_mode=='manual') $mybasketarray['kostos_apostolis']=$mydata['kostos_apostolis'];
if ($kostos_pliromis_mode=='manual')  $mybasketarray['kostos_pliromis']= $mydata['kostos_pliromis'];


$pliroteo = $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'] + $mybasketarray['kostos_pliromis'];

$kostos_apostolis=$mybasketarray['kostos_apostolis'];
$kostos_pliromis=$mybasketarray['kostos_pliromis'];


//$products_ogos='';
//if ($mybasketarray['products_ogos_max_x']>0 or $mybasketarray['products_ogos_max_y']>0 or $mybasketarray['products_ogos_max_z']>0) {
//  $products_ogos = number_format($mybasketarray['products_ogos_max_x'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
//                   number_format($mybasketarray['products_ogos_max_y'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
//                   number_format($mybasketarray['products_ogos_max_z'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND);
//  
//}
//$products_varos='';
//if ($mybasketarray['products_varos']>0) $products_varos=number_format($mybasketarray['products_varos'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).' gr';

if ($row_pos['pos_max_ammount']>0 and $pliroteo>$row_pos['pos_max_ammount']) {
  debug_mail(false,'pos_max_ammount',$row_pos['pos_max_ammount'].' '.$pliroteo);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η αξία είναι μεγαλύτερη του επιτρεπτού για αυτό το POS')));
  echo json_encode($return); die(); }
  
  


$is_force=0; if (isset($_POST['is_force'])) $is_force=intval($_POST['is_force']);
$mytotal=floatval($_POST['mytotal']);
if ($is_force==0 and abs($mytotal - $pliroteo)>0.01) {
  debug_mail(false,'mytotal pliroteo',$mytotal.' '.$pliroteo);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το σύνολο έχει ξαναυπολογιστεί και βρέθηκε').'<br><b>'.
  number_format($pliroteo,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND ).
  '</b><br>'.gks_lang('Θέλετε να συνεχίσετε ;')), 'for_force'=>true);
  echo json_encode($return); die(); }


//echo '<pre>sssssssss '.$pos_step;die();
//// jump to 

$erp_app_mobile_id=0;if (isset($_POST['iderpappmobile'])) $erp_app_mobile_id=intval($_POST['iderpappmobile']);


if ($pos_step=='10draft_end') goto step_10draft_end;
if ($pos_step=='20ypoekdosi_end') goto step_20ypoekdosi_end;
if ($pos_step=='30aade_end') goto step_30aade_end;
if ($pos_step=='40print_end') goto step_40print_end;

  
//////////////////////////////////////////////////         step draft start         ////////////////////////////////////////////////// 
$pos_step='01draft_start';



$redirect='';
$inv_guid=guid_for_acc_inv();
$bank_deposit_9digit=gks_get_bank_deposit_9digit();


$sql="insert into gks_acc_inv (
user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
inv_guid,bank_deposit_9digit,pos_step,pos_id,erp_app_mobile_id,
merchant_ref_trns
) values (
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
'".$db_link->escape_string($inv_guid)."',
'".$db_link->escape_string($bank_deposit_9digit)."',
'".$pos_step."',
".$id_pos.",
".$erp_app_mobile_id.",
'".$db_link->escape_string($merchant_ref_trns)."'
)";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  die('sql error');
}
$id = $db_link->insert_id;
$redirect=base64_encode('admin-acc-inv-item.php?id='.$id);  

if ($erp_app_mobile_id>0) {
  $sxolio=gks_lang('Προσθήκη από gks ERP App Mobile ID').': '.$erp_app_mobile_id.'<br>POS id: '.$id_pos; 
} else {
  $sxolio=gks_lang('Προσθήκη από backend').'<br>POS id: '.$id_pos; 
}

$sxolio.='<br>'.gks_lang('Οθόνη').': '.$screen_width.'x'.$screen_height.' '.$device_type;
if ($my_gps_location_lat!=0 and $my_gps_location_lng!=0) {
  $geoUri = 'http://maps.google.com/maps?q=loc:' . $my_gps_location_lat . ',' . $my_gps_location_lng . ' (point)';
  $sxolio.='<br>'.gks_lang('Θέση').': <a href="'.$geoUri.'" target="_blank">'.$my_gps_location_lat.' , '.$my_gps_location_lng.' <i class="fas fa-map-marker-alt gks_pointgpslog"></i></a>';
}

$sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 

  
//$return = array('success' => false, 
//  'insertid' => $id,
//  'pos_step' => $pos_step,
//  'message' => base64_encode('sql error'), 
//);
//echo json_encode($return); die();


$gks_price_original_net=0;
$gks_price_net=0;
$gks_price_fpa=0;
$gks_price_netfpa=0;
$gks_price_total=0;

$totalWithheldAmount=0;
$totalOtherTaxesAmount=0;
$totalStampDutyamount=0;
$totalFeesAmount=0;
$totalDeductionsAmount=0;

$all_products_for_balance=array();
$product_aa=0;
$products_posotita=0;
$products_need_apostoli=0;
foreach ($mybasketarray['products'] as $product) {
  $product_aa++;
  
  $products_posotita+=$product['product_id']['product_quantity'];
  if ($product['product_id']['product_need_apostoli']!=0) $products_need_apostoli=1;
  
  $product_id=$product['product_id']['id_product'];
  

  $product_price_start_all_net=$product['product_id']['product_price_start_all_net'];
  $product_price_final_all_net=$product['product_id']['product_price_final_all_net'];
  $product_price_final_all_fpa=$product['product_id']['product_price_final_all_fpa'];
  $product_price_final_all_total= $product_price_final_all_net + $product_price_final_all_fpa;//$product['product_id']['product_price_final_all_total'];
  $product_withheldAmount=$product['other_taxes']['withheldAmount'];
  $product_otherTaxesAmount=$product['other_taxes']['otherTaxesAmount'];
  $product_stampDutyAmount=$product['other_taxes']['stampDutyAmount'];
  $product_feesAmount=$product['other_taxes']['feesAmount'];
  $product_deductionsAmount=$product['other_taxes']['deductionsAmount'];

  $myrec=$product['product_id'];
  $product_fpa_base_id=$myrec['product_fpa_base_id'];
  
  $product_fpa_ejeresi_id=intval($product['product_fpa_ejeresi_id']);
  if ($eidos_parastatikou_has_fpa==0 or ($eidos_parastatikou_has_fpa==1 and $product_fpa_base_id!=1004)) $product_fpa_ejeresi_id=0;

  $product_price_check_fpa=1;
  
  $product_price_ekptosi_net=round($product['product_id']['product_price_start_all_net']-$product['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  $product_price_ekptosi_netfpa=round($product['product_id']['product_price_start_all_net']+$product['product_id']['product_price_start_all_fpa']-$product['product_id']['product_price_final_all_net']-$product['product_id']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  $product_price_ekptosi_total=round($product['product_id']['product_price_start_all_total']-$product['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  
  $product_price_ekptosi_pososto=0;
  if ($product['product_id']['product_price_start_all_net']!=0 and $product['product_id']['product_price_include_vat']==0) {
    $product_price_ekptosi_pososto=round($product_price_ekptosi_net*100/$product['product_id']['product_price_start_all_net'],$GKS_BASKET_CALC_EKPTOSI_DECIMAL);
  } else if ($product['product_id']['product_price_start_all_total']!=0 and $product['product_id']['product_price_include_vat']!=0) {
    $product_price_ekptosi_pososto=round($product_price_ekptosi_total*100/$product['product_id']['product_price_start_all_total'],$GKS_BASKET_CALC_EKPTOSI_DECIMAL);
  }
  $product_comments='';
  $myrec['product_price_coupon_use_disabled']=0;
  
  
  if (isset($myrec['monada_convert']) and 
      isset($myrec['monada_convert']['ok']) and 
      isset($myrec['monada_convert']['epi']) and 
      $myrec['monada_convert']['ok'] and 
      $myrec['monada_convert']['epi'] !=0 and
      $myrec['monada_convert']['epi'] != 1) {
    $monada_convert_epi=$myrec['monada_convert']['epi'];
    $monada_convert_epi_rev=$myrec['monada_convert']['epi_rev'];
  } else {
    $monada_convert_epi=1;
    $monada_convert_epi_rev=1;
  }
  $product_price_ekptosi_net=$myrec['product_price_start_all_net']-$myrec['product_price_final_all_net'];

  $sqlF=array();$sqlV=array();
  $sqlF[]='mydate_add'; $sqlV[]="now()";
  $sqlF[]='mydate_edit'; $sqlV[]="now()";
  $sqlF[]='user_id_add'; $sqlV[]=$my_wp_user_id;
  $sqlF[]='user_id_edit'; $sqlV[]=$my_wp_user_id;
  $sqlF[]='myip'; $sqlV[]="'".$db_link->escape_string($gkIP)."'";
  $sqlF[]='acc_inv_id'; $sqlV[]=$id;
  $sqlF[]='product_aa'; $sqlV[]=$product_aa;
  $sqlF[]='product_set'; $sqlV[]="'".$db_link->escape_string($myrec['product_set'])."'";
  $sqlF[]='product_id'; $sqlV[]=$product_id;
  $sqlF[]='product_descr'; $sqlV[]="'".$db_link->escape_string($myrec['product_descr'])."'";
  $sqlF[]='product_monada_id_org'; $sqlV[]=$myrec['product_monada_id_org'];
  $sqlF[]='product_monada_id'; $sqlV[]=$myrec['product_monada_id'];
  $sqlF[]='monada_convert_json'; $sqlV[]="'".$db_link->escape_string(json_encode($myrec['monada_convert']))."'";
  $sqlF[]='monada_convert_epi'; $sqlV[]=$monada_convert_epi;
  $sqlF[]='monada_convert_epi_rev'; $sqlV[]=$monada_convert_epi_rev;
  $sqlF[]='product_is_digital'; $sqlV[]=$myrec['product_is_digital'];
  $sqlF[]='product_is_simple_download'; $sqlV[]=$myrec['product_is_simple_download'];
  $sqlF[]='product_need_apostoli'; $sqlV[]=$myrec['product_need_apostoli'];
  $sqlF[]='product_fpa_base_id'; $sqlV[]=$myrec['product_fpa_base_id'];
  $sqlF[]='product_fpa_id'; $sqlV[]=$myrec['product_fpa_id_array']['id_fpa_to'];
  $sqlF[]='product_fpa_ejeresi_id'; $sqlV[]=$product_fpa_ejeresi_id;
  $sqlF[]='product_fpa_pososto'; $sqlV[]=number_format($myrec['product_fpa_id_array']['fpa_pososto'],10, '.','');
  $sqlF[]='product_fpa_id_json'; $sqlV[]="'".$db_link->escape_string(json_encode($myrec['product_fpa_id_array']))."'";
  $sqlF[]='product_normal'; $sqlV[]=$myrec['product_normal'];
  $sqlF[]='product_type'; $sqlV[]="'".$myrec['product_type']."'";
  $sqlF[]='product_need_multi_files'; $sqlV[]=$myrec['product_need_multi_files'];
  $sqlF[]='product_need_multi_files_min'; $sqlV[]=$myrec['product_need_multi_files_min'];
  $sqlF[]='product_need_multi_files_max'; $sqlV[]=$myrec['product_need_multi_files_max'];
  $sqlF[]='product_varos'; $sqlV[]=$myrec['product_varos'];
  $sqlF[]='product_ogos_x'; $sqlV[]=$myrec['product_ogos_x'];
  $sqlF[]='product_ogos_y'; $sqlV[]=$myrec['product_ogos_y'];
  $sqlF[]='product_ogos_z'; $sqlV[]=$myrec['product_ogos_z'];
  $sqlF[]='product_category_ids'; $sqlV[]="''";
  $sqlF[]='product_sheets'; $sqlV[]=$myrec['product_sheets'];
  $sqlF[]='product_quantity'; $sqlV[]=$myrec['product_quantity'];
  $sqlF[]='apografi_posotitaonhand'; $sqlV[]='null';
  $sqlF[]='product_price_check_fpa'; $sqlV[]=$product_price_check_fpa;
  $sqlF[]='product_price_include_vat'; $sqlV[]=$myrec['product_price_include_vat'];
  $sqlF[]='product_price_start_peritem_db'; $sqlV[]=number_format($myrec['product_price_start_peritem_db'],8,'.','');
  $sqlF[]='product_price_start_peritem_net'; $sqlV[]=number_format($myrec['product_price_start_peritem_net'],8,'.','');
  $sqlF[]='product_price_start_peritem_fpa'; $sqlV[]=number_format($myrec['product_price_start_peritem_fpa'],8,'.','');
  $sqlF[]='product_price_start_peritem_total'; $sqlV[]=number_format($myrec['product_price_start_peritem_total'],8,'.','');
  $sqlF[]='product_price_start_all_net'; $sqlV[]=number_format($myrec['product_price_start_all_net'],8,'.','');
  $sqlF[]='product_price_start_all_fpa'; $sqlV[]=number_format($myrec['product_price_start_all_fpa'],8,'.','');
  $sqlF[]='product_price_start_all_total'; $sqlV[]=number_format($myrec['product_price_start_all_total'],8,'.','');
  $sqlF[]='product_price_final_peritem_db'; $sqlV[]=number_format($myrec['product_price_final_peritem_db'],8,'.','');
  $sqlF[]='product_price_final_peritem_net'; $sqlV[]=number_format($myrec['product_price_final_peritem_net'],8,'.','');
  $sqlF[]='product_price_final_peritem_fpa'; $sqlV[]=number_format($myrec['product_price_final_peritem_fpa'],8,'.','');
  $sqlF[]='product_price_final_peritem_total'; $sqlV[]=number_format($myrec['product_price_final_peritem_total'],8,'.','');
  $sqlF[]='product_price_final_all_net'; $sqlV[]=number_format($myrec['product_price_final_all_net'],8,'.','');
  $sqlF[]='product_price_final_all_fpa'; $sqlV[]=number_format($myrec['product_price_final_all_fpa'],8,'.','');
  $sqlF[]='product_price_final_all_total'; $sqlV[]=number_format($product_price_final_all_total,8,'.','');
  $sqlF[]='product_price_ekptosi_net'; $sqlV[]=number_format($product_price_ekptosi_net,8,'.','');
  $sqlF[]='product_price_ekptosi_pososto'; $sqlV[]=number_format($product_price_ekptosi_pososto,8,'.','');
  $sqlF[]='product_pricelist_item_id'; $sqlV[]=$myrec['product_pricelist_item_id'];
  $sqlF[]='product_pricelist_item_descr'; $sqlV[]="'".$db_link->escape_string($myrec['product_pricelist_item_descr'])."'";
  $sqlF[]='product_pricelist_item_percent'; $sqlV[]=$myrec['product_pricelist_item_percent'];
  $sqlF[]='product_price_coupon_use'; $sqlV[]="'".$db_link->escape_string($myrec['product_price_coupon_use'])."'";
  $sqlF[]='product_price_coupon_use_disabled'; $sqlV[]=intval($myrec['product_price_coupon_use_disabled']);
  $sqlF[]='product_comments'; $sqlV[]="'".$db_link->escape_string($product_comments)."'";
  $sqlF[]='product_withheldPercentCategory'; $sqlV[]=$product['other_taxes']['withheldPercentCategory'];
  $sqlF[]='product_withheldAmount'; $sqlV[]=number_format($product['other_taxes']['withheldAmount'],10, '.','');
  $sqlF[]='product_stampDutyPercentCategory'; $sqlV[]=$product['other_taxes']['stampDutyPercentCategory'];
  $sqlF[]='product_stampDutyAmount'; $sqlV[]=number_format($product['other_taxes']['stampDutyAmount'],10, '.','');
  $sqlF[]='product_feesPercentCategory'; $sqlV[]=$product['other_taxes']['feesPercentCategory'];
  $sqlF[]='product_feesAmount'; $sqlV[]=number_format($product['other_taxes']['feesAmount'],10, '.','');
  $sqlF[]='product_otherTaxesPercentCategory'; $sqlV[]=$product['other_taxes']['otherTaxesPercentCategory'];
  $sqlF[]='product_otherTaxesAmount'; $sqlV[]=number_format($product['other_taxes']['otherTaxesAmount'],10, '.','');
  $sqlF[]='product_deductionsAmount'; $sqlV[]=number_format($product['other_taxes']['deductionsAmount'],10, '.','');
  $sqlF[]='aade_lineComments'; $sqlV[]="''";
  $sqlF[]='p_warehouses_id_from'; $sqlV[]=$warehouses_id_from;
  $sqlF[]='p_warehouses_id_to'; $sqlV[]=$warehouses_id_to;
  $sqlF[]='p_inv_state'; $sqlV[]="'".$inv_state."'";
  $sqlF[]='after_balance_warehouses_id_from'; $sqlV[]=0;
  $sqlF[]='after_balance_warehouses_id_to'; $sqlV[]=0;
  $sqlF[]='woo_item_id'; $sqlV[]=0;
  $sqlF[]='from_aade_import_lock'; $sqlV[]=0;
  $sqlF[]='from_aade_import_user_fpa'; $sqlV[]=$product['from_aade_import_user_fpa'];
  
  $sql="insert into gks_acc_inv_products (" .
       implode(',',$sqlF).
       ') values ('.
       implode(',',$sqlV).
       ')';
       
  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }   
    
  $gks_id_acc_inv_product=$db_link->insert_id;

  
  $xarakt_product_id=$product_id;
  $sql="select id_product, product_parent_id from gks_eshop_products where id_product=".$product_id." and product_class='variable_item' and product_parent_id>0";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }   
  
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $xarakt_product_id=$row['product_parent_id'];
  }
  
  
  //echo '<pre>';print_r($myrec);die();
  
  $out_xarakt_esoda=array();
  $out_xarakt_eksoda=array();
  
  $sql_income="select 
  acc_eidos_parastatikou_id as ep_id,
  aade_typos_xarakt_esodon_id as typos_id,
  aade_katigoria_xarakt_esodon_id as cat_id,
  acc_inv_product_income_pososto as pososto
  from gks_eshop_products_income
  where product_id in (".$xarakt_product_id.")
  and (acc_eidos_parastatikou_id=0 ".($id_acc_eidos_parastatikou > 0 ? ' or acc_eidos_parastatikou_id='.$id_acc_eidos_parastatikou : '').")
  order by id_product_income";
  $result_income = $db_link->query($sql_income);        
  if (!$result_income) {debug_mail(false,'error sql',$sql_income); die('sql error');}
  $has_xarakt_esoda_this=false;
  while ($row_income = $result_income->fetch_assoc()) {
    $out_xarakt_esoda[]=array(
      'ep_id'=> intval($row_income['ep_id']),
      'typos_id'=> intval($row_income['typos_id']),
      'cat_id'=> intval($row_income['cat_id']),
      'pososto'=> floatval($row_income['pososto']),
      'amount' => round(($myrec['product_price_final_all_net']* floatval($row_income['pososto']) / 100),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
    );
    if (intval($row_income['ep_id']) == $id_acc_eidos_parastatikou) $has_xarakt_esoda_this=true;
  }
  if ($has_xarakt_esoda_this) {
    foreach ($out_xarakt_esoda as $key => $value) {
      if ($value['ep_id']==0) unset($out_xarakt_esoda[$key]);
    }
  } 
  //print '<pre>';print_r($out_xarakt_esoda);die();
  
  foreach ($out_xarakt_esoda as $value) {
    $sql_income="insert into gks_acc_inv_products_income (
    acc_inv_product_id,aade_typos_xarakt_esodon_id,aade_katigoria_xarakt_esodon_id,acc_inv_product_income_ammount
    ) values (
    ".$gks_id_acc_inv_product.",
    ".$value['typos_id'].",
    ".$value['cat_id'].",
    ".$value['amount']."
    )";
    $result_income = $db_link->query($sql_income);        
    if (!$result_income) {debug_mail(false,'error sql',$sql_income); die('sql error');}
    
  } 
            
  //$sql_expenses="select 
  //acc_eidos_parastatikou_id as ep_id,
  //aade_typos_xarakt_eksodon_id as typos_id,
  //aade_katigoria_xarakt_eksodon_id as cat_id,
  //acc_inv_product_expenses_pososto as pososto
  //from gks_eshop_products_expenses
  //where product_id in (".$xarakt_product_id.")
  //and (acc_eidos_parastatikou_id=0 ".($id_acc_eidos_parastatikou > 0 ? ' or acc_eidos_parastatikou_id='.$id_acc_eidos_parastatikou : '').")
  //order by id_product_expenses";
  //$result_expenses = $db_link->query($sql_expenses);        
  //if (!$result_expenses) {debug_mail(false,'error sql',$sql_expenses); die('sql error');}
  //while ($row_expenses = $result_expenses->fetch_assoc()) {
  //  $out_xarakt_eksoda[]=array(
  //    'ep_id'=> intval($row_expenses['ep_id']),
  //    'typos_id'=> intval($row_expenses['typos_id']),
  //    'cat_id'=> intval($row_expenses['cat_id']),
  //    'pososto'=> floatval($row_expenses['pososto']),
  //  );
  //}
  
  






  $gks_price_original_net+=$product_price_start_all_net;
  $gks_price_net+=$product_price_final_all_net;
  $gks_price_fpa+=$product_price_final_all_fpa;
  $gks_price_netfpa+=$product_price_final_all_net+$product_price_final_all_fpa;
  $gks_price_total+=$product_price_final_all_total;

  $totalWithheldAmount+=$product_withheldAmount;
  $totalOtherTaxesAmount+=$product_otherTaxesAmount;
  $totalStampDutyamount+=$product_stampDutyAmount;
  $totalFeesAmount+=$product_feesAmount;
  $totalDeductionsAmount+=$product_deductionsAmount;

  if ($product_id>0 and in_array($product_id,$all_products_for_balance)==false)
    $all_products_for_balance[]=$product_id;


}

$gks_price_total=
   $gks_price_net 
    + $gks_price_fpa
    - $totalWithheldAmount
    + $totalOtherTaxesAmount
    + $totalStampDutyamount
    + $totalFeesAmount
    - $totalDeductionsAmount;
    
$products_need_pliromi=0;
if ($gks_price_net>0) $products_need_pliromi=1;

$sql="update gks_acc_inv set 
inv_state='".$inv_state."', 
company_id=".$company_id.",
inv_acc_journal_id=".$inv_acc_journal_id.",
inv_acc_seira_id=".$inv_acc_seira_id.",
company_sub_id=".$company_sub_id.",
inv_date='".$db_link->escape_string($inv_date)."', 
user_id=".$user_id.",

user_first_name='".$db_link->escape_string($dr_user_first_name)."',
user_last_name='".$db_link->escape_string($dr_user_last_name)."',
user_email='".$db_link->escape_string($dr_user_email)."',
user_mobile='".$db_link->escape_string($dr_user_mobile)."',
user_lang='".$db_link->escape_string($dr_user_lang)."',
ma_odos='".$db_link->escape_string($dr_user_ma_odos)."',
ma_arithmos='".$db_link->escape_string($dr_user_ma_arithmos)."',
ma_orofos='".$db_link->escape_string($dr_user_ma_orofos)."',
ma_perioxi='".$db_link->escape_string($dr_user_ma_perioxi)."',
ma_poli='".$db_link->escape_string($dr_user_ma_poli)."',
ma_tk='".$db_link->escape_string($dr_user_ma_tk)."',
ma_country_id=".$dr_user_ma_country_id.",
ma_nomos_id=".$dr_user_ma_nomos_id.",

eponimia='".$db_link->escape_string($dr_user_eponimia)."',
title='".$db_link->escape_string($dr_user_title)."',
afm='".$db_link->escape_string($dr_user_afm)."',
doy='".$db_link->escape_string($dr_user_doy)."',
epaggelma='".$db_link->escape_string($dr_user_epaggelma)."',
address_extra=".$form_select_apostoli.",

destination_data_name='".$db_link->escape_string($destination_data_name)."',
destination_data_phone='".$db_link->escape_string($destination_data_phone)."',
destination_data_odos='".$db_link->escape_string($destination_data_odos)."',
destination_data_arithmos='".$db_link->escape_string($destination_data_arithmos)."',
destination_data_orofos='".$db_link->escape_string($destination_data_orofos)."',
destination_data_perioxi='".$db_link->escape_string($destination_data_perioxi)."',
destination_data_poli='".$db_link->escape_string($destination_data_poli)."',
destination_data_tk='".$db_link->escape_string($destination_data_tk)."',
destination_data_country_id=".$destination_data_country_id.",
destination_data_nomos_id=".$destination_data_nomos_id.",



note_doc='".$db_link->escape_string($note_doc)."',
note_logistirio='".$db_link->escape_string($note_logistirio)."',

tropos_apostolis=".$tropos_apostolis.",
tropos_pliromis=".$tropos_pliromis.",
delivery_id_8=".$delivery_id_8.",
delivery_number='".$db_link->escape_string($delivery_number)."',
vehicle_number='".$db_link->escape_string($vehicle_number)."',
dispatch_date=".($dispatch_date == '' ? 'null' : "'".$db_link->escape_string($dispatch_date)."'") .", 
kostos_apostolis=".number_format($kostos_apostolis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').", 
kostos_pliromis=".number_format($kostos_pliromis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
def_ekptosi=".number_format($def_ekptosi, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
coupons='".$db_link->escape_string($coupons_str)."',

affect_balance=".$affect_balance.",
affect_balance_all_poso=".$affect_balance_all_poso.",
affect_balance_all_poso_type='".$db_link->escape_string($affect_balance_all_poso_type)."',";

if ($affect_balance == 0) {
  $affect_balance_poso=0;
} else {
  if ($affect_balance_all_poso==1) {
    switch ($affect_balance_all_poso_type) {
      case 'price_net':
        $affect_balance_poso=$gks_price_net;
        break;  
      case 'price_netfpa':
        $affect_balance_poso=$gks_price_netfpa;
        break;  
      case 'price_total':
        $affect_balance_poso=$gks_price_total;
        break;  
      case 'pliroteo':
        $affect_balance_poso=$gks_price_total + $kostos_apostolis + $kostos_pliromis;
        break;  
      default:     
      
    }
  } else {
    //$affect_balance_poso=$affect_balance_poso;
  }
}  
$sql.="affect_balance_poso=".number_format($affect_balance_poso, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",";

if ($affect_balance_pros!=-1 and $affect_balance_pros!=1) {
  $affect_balance_pros=0;
}
$sql.="affect_balance_pros=".$affect_balance_pros.",";

$sql.="assigned_id=".$assigned_id.",";
if ($GKS_CRM_ENABLE) {
$sql.=
"crm_channel_id=".$crm_channel_id.",
crm_channel_contact_id=".$crm_channel_contact_id.",
crm_channel_campain_id=".$crm_channel_campain_id.",
crm_channel_url=". ($crm_channel_url =='' ? 'null' : "'".$db_link->escape_string($crm_channel_url)."'").",
crm_channel_code=". ($crm_channel_code =='' ? 'null' : "'".$db_link->escape_string($crm_channel_code)."'").",
crm_channel_text=". ($crm_channel_text =='' ? 'null' : "'".$db_link->escape_string($crm_channel_text)."'").",";
}


$sql.="
gks_price_original_net=".number_format($gks_price_original_net, 10, '.', '').", 
gks_price_net=".number_format($gks_price_net, 10, '.', '').", 
gks_price_fpa=".number_format($gks_price_fpa, 10, '.', '').", 
gks_price_netfpa=".number_format($gks_price_netfpa, 10, '.', '').", 
gks_price_total=".number_format($gks_price_total, 10, '.', '').", 

totalWithheldAmount=".number_format($totalWithheldAmount, 10, '.', '').", 
totalOtherTaxesAmount=".number_format($totalOtherTaxesAmount, 10, '.', '').", 
totalStampDutyamount=".number_format($totalStampDutyamount, 10, '.', '').", 
totalFeesAmount=".number_format($totalFeesAmount, 10, '.', '').", 
totalDeductionsAmount=".number_format($totalDeductionsAmount, 10, '.', '').", 

aade_skopos_diakinisis_id=".$aade_skopos_diakinisis_id.",
fiscal_position_id=".$fiscal_position_id.",
pricelist_id=".$pricelist_id.",

warehouses_id_from=".$warehouses_id_from.",
warehouses_id_to=".$warehouses_id_to.",

products_posotita=".number_format($products_posotita,8,'.','').",
products_need_apostoli=".$products_need_apostoli.",
products_need_pliromi=".$products_need_pliromi.",
session_id='".$_gks_id_session."',


products_varos=".number_format($mybasketarray['products_varos'],8,'.','').",
products_ogos=".number_format($mybasketarray['products_ogos'],8,'.','').",
products_ogos_max_x=".number_format($mybasketarray['products_ogos_max_x'],8,'.','').",
products_ogos_max_y=".number_format($mybasketarray['products_ogos_max_y'],8,'.','').",
products_ogos_max_z=".number_format($mybasketarray['products_ogos_max_z'],8,'.','').",
";

if (isset($mybasketarray['tropoi_apostolis_all']) and 
    isset($mybasketarray['tropos_apostolis']) and
    isset($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]) ) {
      $sql.="tropos_apostolis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]))."',";
}
if (isset($mybasketarray['tropoi_pliromis_all']) and 
    isset($mybasketarray['tropos_pliromis']) and
    isset($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]) ) {
      $sql.="kostos_pliromis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]))."',";
}


$sql.="
update_from_gks=1,
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'

where id_acc_inv = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }


$myarray_new=array();
$myarray_line_new=array();
$idiotites_new=get_acc_inv_details_txt($id, $myarray_new, $myarray_line_new); 

$sql="update gks_acc_inv set idiotites='".$db_link->escape_string(json_encode($myarray_new))."' where id_acc_inv = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 


$sql="insert into gks_acc_inv_payment (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  acc_inv_id,pp,payment_acquirer_id,poso,asset_id
) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$id.",
  1,
  ".$tropos_pliromis.",
  ".number_format($gks_price_total, 10, '.', '').",
  0
)";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 





//////////////////////////////////////////////////         step draft end        ////////////////////////////////////////////////// 
$pos_step='10draft_end';
$sql="update gks_acc_inv set pos_step='".$pos_step."' where id_acc_inv=".$id." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 





step_10draft_end:






/*    
$has_ekdosi=false;
$save_but_message='';
$inv_acc_number_int_old=0;
gks_inv_get_ekdosi_numbers();
$save_but_message_all.=$save_but_message;

if ($has_ekdosi==false) {
  debug_mail(false,'has_ekdosi false',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν είναι δυνατή η έκδοση του παραστατικού')),'insertid' => $id,'pos_step' => $pos_step);
  echo json_encode($return); die();
}  
*/

$inv_state='070ypoekdosi';
$sql="update gks_acc_inv set
inv_state='070ypoekdosi'
where id_acc_inv = ".$id." limit 1";

/*
,inv_acc_number_int=".$inv_acc_number_int_new.",
inv_acc_number_str='".$db_link->escape_string($inv_acc_number_str_new)."',
inv_acc_ekdosi_date=now(),
inv_acc_seira_code='".$db_link->escape_string($inv_acc_seira_code_new)."'

*/


$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'),'insertid' => $id,'pos_step' => $pos_step);
  echo json_encode($return); die(); } 

$sql="update gks_acc_inv_products set p_inv_state='070ypoekdosi' where acc_inv_id=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'),'insertid' => $id,'pos_step' => $pos_step);
  echo json_encode($return); die(); } 



//$return = array('success' => false, 'message' => base64_encode('check 111111111'),'insertid' => $id,'pos_step' => $pos_step);
//echo json_encode($return); die();


//////////////////////////////////////////////////         step ypoekdosi end        ////////////////////////////////////////////////// 
$pos_step='20ypoekdosi_end';
$sql="update gks_acc_inv set pos_step='".$pos_step."' where id_acc_inv=".$id." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'),'insertid' => $id,'pos_step' => $pos_step);
  echo json_encode($return); die(); }

step_20ypoekdosi_end:




if (isset($_POST['pway'])) {
  
  $tropos_pliromis=intval($_POST['pway']);

  $sql="select id_acc_inv_payment,payment_acquirer_id,transaction_id from gks_acc_inv_payment where acc_inv_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή πληρωμής για αυτό το παραστατικό')));
    echo json_encode($return); die();}
  $row_temp = $result->fetch_assoc();
  $id_acc_inv_payment=intval($row_temp['id_acc_inv_payment']);
  $payment_acquirer_id=intval($row_temp['payment_acquirer_id']);
    

  if (intval($row_temp['transaction_id'])==0 and
      isset($_POST['pway']) and 
      intval($_POST['pway'])>0) {
    
    
    $sql="update gks_acc_inv_payment set 
    payment_acquirer_id=".intval($_POST['pway'])."
    where id_acc_inv_payment=".$id_acc_inv_payment."
    limit 1";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    
    $sql="update gks_acc_inv set 
    tropos_pliromis=".intval($_POST['pway'])."
    where id_acc_inv=".$id."
    and pos_step='20ypoekdosi_end'";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
   
    
    
  }  
  
}



//echo '<pre>dddddddddd '."\n";print_r($_POST);die();
//$_POST['pway']

$row_old=array();
$products_old=array();
$extra_address_old=array();

$sql=select_gks_acc_inv()." where id_acc_inv=".$id;
$sql.=" limit 1";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'),'insertid' => $id,'pos_step' => $pos_step);
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'record not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')),'insertid' => $id,'pos_step' => $pos_step);
  echo json_encode($return); die();}
$row_old = $result->fetch_assoc();
$inv_state=$row_old['inv_state'];
$gks_custom_prepare=gks_custom_table_item_prepare('gks_acc_inv',['from'=>'item']);
$gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 

$sql="SELECT gks_acc_inv_products.*, gks_monades_metrisis.monada_descr, gks_eshop_fpa_base.fpa_base_descr
FROM (gks_acc_inv_products 
LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada)
LEFT JOIN gks_eshop_fpa_base ON gks_acc_inv_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base
WHERE gks_acc_inv_products.acc_inv_id=".$id."
ORDER BY gks_acc_inv_products.product_aa;";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'),'insertid' => $id,'pos_step' => $pos_step);
  echo json_encode($return); die();}
if (isset($all_products_for_balance)==false) $all_products_for_balance=array();
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
    $return = array('success' => false, 'message' => base64_encode('sql error'),'insertid' => $id,'pos_step' => $pos_step);
    echo json_encode($return); die();}

  if ($result_select->num_rows==1) {
    $extra_address_old = $result_select->fetch_assoc();
  }    
}


$balance_user=gks_balance_calc(['id' => $user_id]);

$mybal = gks_whi_mov_balance_calc($all_products_for_balance);

gks_whi_after_balance_for_inv($id);



//echo '<pre>ddddddd ddddd';die();

if ($gks_run_until=='070ypoekdosi') {

  $save_but_message_all.='<div class="alert alert-secondary" role="alert">'.gks_lang('Μετάβαση στο παραστατικό').' <a href="admin-acc-inv-item.php?id='.$id.'&scrollto=gks_print" class="gks_link">#'.$id.'</a></div>';
  
  $return = array('success' => true, 
    'insertid' => $id,
    'pos_step' => $pos_step,
    'message' => base64_encode('ok'), 
    'save_but_message' => base64_encode($save_but_message_all),
    'doc_item_url'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_print'),
  );
  echo json_encode($return); die();  
  
}


//$return = array('success' => false, 'message' => base64_encode('check 111111111'),'insertid' => $id,'pos_step' => $pos_step);
//echo json_encode($return); die();

$has_already_ekdosi=false;
$sql_has_already_ekdosi="select inv_acc_number_int from gks_acc_inv where id_acc_inv = ".$id." and inv_acc_number_int>0 limit 1";
$result_has_already_ekdosi = $db_link->query($sql_has_already_ekdosi);  
if (!$result_has_already_ekdosi) {
  debug_mail(false,'error sql',$sql_has_already_ekdosi);
  $return = array('success' => false, 'message' => base64_encode('sql error'),'insertid' => $id,'pos_step' => $pos_step);
  echo json_encode($return); die(); } 
if ($result_has_already_ekdosi->num_rows==1) {
  //$row_has_already_ekdosi = $result_has_already_ekdosi->fetch_assoc();
  $has_already_ekdosi=true;
}

if ($has_already_ekdosi==false) {
  
  $has_ekdosi=false;
  $save_but_message='';
  $inv_acc_number_int_old=0;
  gks_inv_get_ekdosi_numbers();
  $save_but_message_all.=$save_but_message;
  
  if ($has_ekdosi==false) {
    debug_mail(false,'has_ekdosi false',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν είναι δυνατή η έκδοση του παραστατικού')),'insertid' => $id,'pos_step' => $pos_step);
    echo json_encode($return); die();
  }  
  
  $inv_state='090ekdosi';
  $sql="update gks_acc_inv set
  inv_date=now(),
  inv_state='090ekdosi',
  inv_acc_number_int=".$inv_acc_number_int_new.",
  inv_acc_number_str='".$db_link->escape_string($inv_acc_number_str_new)."',
  inv_acc_ekdosi_date=now(),
  inv_acc_seira_code='".$db_link->escape_string($inv_acc_seira_code_new)."'
  
  where id_acc_inv = ".$id." limit 1";
  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'),'insertid' => $id,'pos_step' => $pos_step);
    echo json_encode($return); die(); } 
  
  $sql="update gks_acc_inv_products set p_inv_state='090ekdosi' where acc_inv_id=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'),'insertid' => $id,'pos_step' => $pos_step);
    echo json_encode($return); die(); } 
}


$aade_mydata_live=intval($row_pos['pos_aade_mydata_live']); 
$seira_send_mydata =intval($row_pos['send_mydata'])==1; 
$seira_send_paroxos=intval($row_pos['send_paroxos'])==1; 

if ($seira_send_mydata) {
  $force_options=array();
  if ($aade_mydata_live==0) {
    $force_options=array(
      'aade_mydata_sender_afm' => $GKS_AADE_MYDATA_SANDBOX_AFM,
      'aade_mydata_live' => $aade_mydata_live,
      'aade_branch' => $GKS_AADE_MYDATA_SANDBOX_BRANCE,
      'aade_mydata_user_id' => $GKS_AADE_MYDATA_SANDBOX_USER_ID,
      'aade_mydata_subscription_key' => $GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY,
    );
  }
  
  //print '<pre>';print_r($force_options);die();
  $force_options['doc_table']='gks_acc_inv';
  $ret=gks_aade_invoice($id,$force_options);
  
  $row_old['aade_errors']='';
  $myparams=[];
  if ($ret['success']==false and $ret['message']!='') $myparams['ret_aade_errors']=$ret['message'];

  gks_inv_sxolio_log($id,$row_old,$products_old,$extra_address_old,($aade_mydata_live==0 ? gks_lang('Δοκιμαστική αποστολή σε myData').'<br>' : gks_lang('Πραγματική αποστολή σε myData').'<br>'),$myparams,$gks_custom_row_old);


  if ($ret['success']==false) {
  
    $save_but_message_all.='<b>'.gks_lang('Αποστολή στην ΑΑΔΕ').'</b><br>'.$ret['message'];
    '<div class="alert alert-secondary" role="alert">'.gks_lang('Μετάβαση στο παραστατικό').' <a href="admin-acc-inv-item.php?id='.$id.'&scrollto=gks_print" class="gks_link" target="_blank">#'.$id.'</a></div>';
    
    $return = array('success' => false, 
      'insertid' => $id,
      'pos_step' => $pos_step,
      'for_retry' => true,
      'message' => base64_encode('error'), 
      'save_but_message' => base64_encode($save_but_message_all),
      'doc_item_url'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_aade'),
    );
    
    //$return = array('success' => true, 'message' => base64_encode('error'),
    // 'redirect'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_aade'),
    // 'save_but_message' => base64_encode($ret['message']));
    echo json_encode($return); die();
    
  }  
    
} else if ($seira_send_paroxos) {
  $force_options=array();
    
  if ($aade_mydata_live==0) {
    $force_options=array(
      'paroxos_mydata_live' => $aade_mydata_live,
    );
  }
  $force_options['doc_table']='gks_acc_inv';
  $ret=gks_paroxos_invoice($id,$force_options);
  
  $row_old['aade_errors']='';
  $myparams=[];
  if ($ret['success']==false and $ret['message']!='') $myparams['ret_aade_errors']=$ret['message'];
  gks_inv_sxolio_log($id,$row_old,$products_old,$extra_address_old,($aade_mydata_live==0 ? gks_lang('Δοκιμαστική αποστολή σε πάροχο').'<br>' : gks_lang('Πραγματική αποστολή σε πάροχο').'<br>'),$myparams,$gks_custom_row_old);

  if (isset($ret['paroxos_tf1_active']) and $ret['paroxos_tf1_active']==true) {
    //apla sinexise san na min simbainei kati
    
  } else if ($ret['success']==false) {
  
    $save_but_message_all.='<b>'.gks_lang('Αποστολή σε πάροχο').'</b><br>'.$ret['message'].
    '<div class="alert alert-secondary" role="alert">'.gks_lang('Μετάβαση στο παραστατικό').' <a href="admin-acc-inv-item.php?id='.$id.'&scrollto=gks_print" class="gks_link" target="_blank">#'.$id.'</a></div>';
    
    $return = array('success' => false, 
      'insertid' => $id,
      'pos_step' => $pos_step,
      'for_retry' => true,
      'message' => base64_encode('error'), 
      'save_but_message' => base64_encode($save_but_message_all),
      'doc_item_url'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_aade'),
    );
    if (isset($ret['paroxos_tf1_active'])) {
      $return['paroxos_tf1_active']=$ret['paroxos_tf1_active'];
    }
    //$return = array('success' => true, 'message' => base64_encode('error'),
    // 'redirect'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_aade'),
    // 'save_but_message' => base64_encode($ret['message']));
    echo json_encode($return); die();
    
  }  
    
}







//////////////////////////////////////////////////         step aade end        ////////////////////////////////////////////////// 
$pos_step='30aade_end';
$sql="update gks_acc_inv set pos_step='".$pos_step."' where id_acc_inv=".$id." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

step_30aade_end:


if (intval($row_pos['pos_print_enable'])==1) {
  
  
  
  $row_pos['pos_print_file_type']=trim_gks($row_pos['pos_print_file_type']);
  $row_pos['pos_print_grayscale']=intval($row_pos['pos_print_grayscale']);
  $row_pos['pos_print_landscape']=intval($row_pos['pos_print_landscape']);
  $row_pos['pos_print_zoom']=intval($row_pos['pos_print_zoom']);
  $row_pos['pos_print_form_id']=intval($row_pos['pos_print_form_id']);
  $row_pos['pos_thermal_form_id']=intval($row_pos['pos_thermal_form_id']);
  
  
  
  $save_folder='acc/inv/'.$id.'/print/';
  $save_basename='INV_'.$id.'_'.greeklish(getAccInvStateDescr($inv_state)).'_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
  $print_params=array(
    'table' => 'gks_acc_inv',
    'id' => $id,
    'fileserver' => GKS_FileServerShare,
    'folder'=> $save_folder,
    'filename' => $save_basename,
    'override' => array(
      'gks_lang' => '',     //  '' is default, 'el-US', 'en-US' 
      'file_type' => '',    //  '' is default, 'pdf','html',
      'grayscale' => -1,    // '-1 is default', 0, 1
      'zoom' => -1,         // '-1 is default', 1, 1.5, 0.8
      'is_landscape' => -1, // '-1 is default', 0, 1
      'is_preview' => 0,
      'createthump' => 0,
    ),
  );
  
  if (isset($row_pos['pos_print_file_type'])) {
    if ($row_pos['pos_print_file_type']=='pdf') $print_params['override']['file_type']='pdf';
    else if ($row_pos['pos_print_file_type']=='html') $print_params['override']['file_type']='html';
    else if ($row_pos['pos_print_file_type']=='jpg') $print_params['override']['file_type']='jpg';
  }
  if (isset($row_pos['pos_print_grayscale'])) {
    if (intval($row_pos['pos_print_grayscale'])==0) $print_params['override']['grayscale']=0;
    else $print_params['override']['grayscale']=1;
  }
  if (isset($row_pos['pos_print_landscape'])) {
    if (intval($row_pos['pos_print_landscape'])==0) $print_params['override']['is_landscape']=0;
    else $print_params['override']['is_landscape']=1;
  }
  if (isset($row_pos['pos_print_zoom'])) {
    $zoom=intval($row_pos['pos_print_zoom']);
    if ($zoom < 10 or $zoom > 200) $zoom=100;
    $print_params['override']['zoom']=$zoom/100;
  }
  
  $gks_erp_app_mobile=0;if (isset($_POST['gks_erp_app_mobile'])) $gks_erp_app_mobile=intval($_POST['gks_erp_app_mobile']);
  $gks_erp_app_mobile_local_printers_length=0;if (isset($_POST['gks_erp_app_mobile_local_printers_length'])) $gks_erp_app_mobile_local_printers_length=intval($_POST['gks_erp_app_mobile_local_printers_length']);
  

  
  $this_device_type='webpage_computer';
  if ($gks_erp_app_mobile==1) {
    if ($gks_erp_app_mobile_local_printers_length==0) $this_device_type='app_no_thermal';  
    else $this_device_type='app_with_thermal';  
  } else {
    if ($device_type=='desktop') $this_device_type='webpage_computer'; 
    else if ($device_type=='tablet') $this_device_type='webpage_tablet'; 
    else if ($device_type=='mobile') $this_device_type='webpage_mobile'; 
  }
  
  //print '<pre>';print_r($row_pos);die();
  $has_set_gks_erp_app=false;

  if ($row_pos['erp_app_id']>0) {
    $has_set_gks_erp_app=true;
    $erp_app_filter=[];
    if (trim_gks($row_pos['erp_app_filter'])!='') $erp_app_filter=json_decode(trim_gks($row_pos['erp_app_filter']), true);
    
    if (in_array($this_device_type,$erp_app_filter)) {
      $print_params['gks_erp_app']=array(
        'id_erp_app'=>intval($row_pos['erp_app_id']),
        'erp_app_dest'=>trim_gks($row_pos['erp_app_dest']),
        'erp_app_dest_printer'=>trim_gks($row_pos['erp_app_dest_printer']),
        'erp_app_dest_printer_method'=>intval($row_pos['erp_app_dest_printer_method']),
        'erp_app_dest_printer_lpr_ip'=>trim_gks($row_pos['erp_app_dest_printer_lpr_ip']),
        'erp_app_dest_printer_copies'=>intval($row_pos['erp_app_dest_printer_copies']),
        'erp_app_dest_folder'=>trim_gks($row_pos['erp_app_dest_folder']),
      );      
    }
  }
  
  //print '<pre>';print_r($print_params);die();
  
  if ($has_set_gks_erp_app==false) {
    $sql_send_erp_app="SELECT gks_acc_inv.id_acc_inv, gks_acc_seires.erp_app_id, gks_acc_seires.erp_app_dest, 
    gks_acc_seires.erp_app_dest_printer, 
    gks_acc_seires.erp_app_dest_printer_method,
    gks_acc_seires.erp_app_dest_printer_lpr_ip,
    gks_acc_seires.erp_app_dest_printer_copies, 
    gks_acc_seires.erp_app_dest_folder,
    gks_acc_seires.erp_app_filter
    FROM (gks_acc_inv 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira) 
    LEFT JOIN gks_erp_app ON gks_acc_seires.erp_app_id = gks_erp_app.id_erp_app
    WHERE gks_acc_inv.id_acc_inv=".$id." 
    AND gks_acc_inv.inv_acc_seira_id>0
    AND gks_acc_seires.erp_app_id>0
    AND gks_erp_app.id_erp_app>0";
    $result_send_erp_app = $db_link->query($sql_send_erp_app);        
    if (!$result_send_erp_app) {
      debug_mail(false,'error sql',$sql_send_erp_app);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
      
    if ($result_send_erp_app->num_rows==1) {
      $row_send_erp_app = $result_send_erp_app->fetch_assoc();

      $erp_app_filter=[];
      if (trim_gks($row_pos['erp_app_filter'])!='') $erp_app_filter=json_decode(trim_gks($row_pos['erp_app_filter']), true);
      
      if (in_array($this_device_type,$erp_app_filter)) {
        $print_params['gks_erp_app']=array(
          'id_erp_app'=>intval($row_send_erp_app['erp_app_id']),
          'erp_app_dest'=>trim_gks($row_send_erp_app['erp_app_dest']),
          'erp_app_dest_printer'=>trim_gks($row_send_erp_app['erp_app_dest_printer']),
          'erp_app_dest_printer_method'=>intval($row_send_erp_app['erp_app_dest_printer_method']),
          'erp_app_dest_printer_lpr_ip'=>trim_gks($row_send_erp_app['erp_app_dest_printer_lpr_ip']),
          'erp_app_dest_printer_copies'=>intval($row_send_erp_app['erp_app_dest_printer_copies']),
          'erp_app_dest_folder'=>trim_gks($row_send_erp_app['erp_app_dest_folder']),
        );
      }
    }
  }
  
  if (isset($_POST['gks_pos_client_send_fileto_url']) and $_POST['gks_pos_client_send_fileto_url']!='') {
    $print_params['gks_pos_client_send_fileto_url']=base64_decode($_POST['gks_pos_client_send_fileto_url']);
    //print '<pre>';print $print_params['gks_pos_client_send_fileto_url'];die();
    
  }
  
  
  
  $form_id=0;if (isset($row_pos['pos_print_form_id'])) $form_id=intval($row_pos['pos_print_form_id']);
  $thermal_form_id=0;if (isset($row_pos['pos_thermal_form_id'])) $thermal_form_id=intval($row_pos['pos_thermal_form_id']);
  
  if ($seira_send_paroxos) {
    $print_params['paroxos_send_pdf']=intval($row_pos['pos_paroxos_send_pdf'])==1;
    //echo '<pre>'.$print_params['paroxos_send_pdf'].'|'.$row_pos['pos_paroxos_send_pdf'];die();
  }
  
  //echo '<pre>'; print_r($print_params); die();
  
  $ret_print = gks_print_form('gks_acc_inv',$id,$form_id,$print_params);


  
  $ret_thermal_print=[];
  if ($erp_app_mobile_id>0 and $thermal_form_id>0 and $gks_erp_app_mobile_local_printers_length>0) {
    $thermal_print_params=$print_params;
    $thermal_print_params['override']['file_type']='raw';
    $ret_thermal_print = gks_print_form('gks_acc_inv',$id,$thermal_form_id,$thermal_print_params);
  }
  //echo '<pre>'; print_r($ret_print); print_r($ret_thermal_print); die();
  
  //echo  '<pre>'.$erp_app_mobile_id.'|'.$thermal_form_id.'|'.$gks_erp_app_mobile_local_printers_length;die();
  
  //debug_mail(false,'ppppppppp','|'.$erp_app_mobile_id.'|'.$thermal_form_id.'|'.$gks_erp_app_mobile_local_printers_length.'|');
  
  if ($ret_print['success']==false) {
    debug_mail(false,'print error',$ret_print['message']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την δημιουργία της εκτύπωσης').'<br>'.$ret_print['message']));
    echo json_encode($return); die();}
  
  //debug_mail(false,'ppppppppp',print_r($ret_print,true));
    
  
  $save_but_message='';
  
  
  $sql="update gks_acc_inv set 
    print_date=now(),
    print_file_name='".$db_link->escape_string($ret_print['save_basename'])."',
    print_file_url='".$db_link->escape_string($ret_print['url_file'])."',
    print_user_id=".$my_wp_user_id.",
    print_inv_state='".$db_link->escape_string($inv_state)."'
    where id_acc_inv=".$id;

  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $erp_app_mobile_id=0;
  $sql_erp_app_mobile="select erp_app_mobile_id from gks_acc_inv where id_acc_inv=".$id;
  $result_erp_app_mobile = $db_link->query($sql_erp_app_mobile);  
  if (!$result_erp_app_mobile) {
    debug_mail(false,'error sql',$sql_erp_app_mobile);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result_erp_app_mobile->num_rows==1) {
    $row_erp_app_mobile = $result_erp_app_mobile->fetch_assoc();
    $erp_app_mobile_id=intval($row_erp_app_mobile['erp_app_mobile_id']);  
  }
      
  
  $ret_shortcode_full='';
  //if ($erp_app_mobile_id>0 or $device_type=='tablet' or $device_type=='mobile') {
  if (true) {

    
    $public_shortcode='';
    
    $mysize=filesize($ret_print['path_file']);
    
    $sql="insert into gks_acc_inv_photo (
    acc_inv_id,photo_url,
    mydate,mysize,
    ip,user_add_id,show_print,filesobjectlist,
    public_expire_date,public_shortcode,public_myopencount,descr
    ) values (
    ".$id.",
    '".$db_link->escape_string($ret_print['path_relative'])."',
    now(),
    ".$mysize.",
    '".$db_link->escape_string($gkIP)."',
     ".$my_wp_user_id.",
    0,
    1,
    '".date('Y-m-d H:i:s', time() + 30*24*60*60)."',
    '".$db_link->escape_string($public_shortcode)."',
    0,
    '".$db_link->escape_string($GKS_SITE_HUMAN_NAME.' '.gks_lang('Απόδειξη').' '.showDate(time(),'d-m-Y H-i',1))."'
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 

    
    $ret_shortcode=gks_fileserver_item_create_public_shortcode('gks_acc_inv',$ret_print['path_relative']);
    /*Array
    (
        [action] => create
        [code] => wqnbb
        [full] => https://test.easyfilesselection.com/s/565wqnbb
    )*/
    if (isset($ret_shortcode['full'])) {
      $ret_shortcode_full=$ret_shortcode['full'];
    }
    
    
    
    if (isset($ret_thermal_print['url_file'])) {
      
      $thermal_public_shortcode='';
      
      $mysize=filesize($ret_thermal_print['path_file']);
      
      $sql="insert into gks_acc_inv_photo (
      acc_inv_id,photo_url,
      mydate,mysize,
      ip,user_add_id,show_print,filesobjectlist,
      public_expire_date,public_shortcode,public_myopencount,descr
      ) values (
      ".$id.",
      '".$db_link->escape_string($ret_thermal_print['path_relative'])."',
      now(),
      ".$mysize.",
      '".$db_link->escape_string($gkIP)."',
       ".$my_wp_user_id.",
      0,
      1,
      '".date('Y-m-d H:i:s', time() + 24*60*60)."',
      '".$db_link->escape_string($public_shortcode)."',
      0,
      '".$db_link->escape_string('gks_erp_raw_print_'.showDate(time(),'d_m_Y_H_i_s',1))."'
      )";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
  
      
      $thermal_ret_shortcode=gks_fileserver_item_create_public_shortcode('gks_acc_inv',$ret_thermal_print['path_relative']);
      /*Array
      (
          [action] => create
          [code] => wqnbb
          [full] => https://test.easyfilesselection.com/s/565wqnbb
      )*/
      if (isset($thermal_ret_shortcode['full'])) {
        $thermal_ret_shortcode_full=$thermal_ret_shortcode['full'];
      }      
      
    }
    
    
    
    
    
    
  }
  

  $sxolio_log = gks_lang('Εκτύπωση').'<br>'.
  gks_lang('Κατάσταση').': <span class="acc_inv_state_'.$inv_state.'">'.getAccInvStateDescr($inv_state).'</span><br>'.
  gks_lang('Αρχείο').': <a href="'.$ret_print['url_file'].'" target="_blank">'.$ret_print['save_basename'].'</a> '.
  '<a href="'.$ret_print['url_file'].'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
  if ($ret_shortcode_full!='') {
    $sxolio_log.='<br>'.gks_lang('Το αρχείο είναι και <b>δημόσιο</b>').':<br><a href="'.$ret_shortcode_full.'">'.$ret_shortcode_full.'</a>';
  }
  

  $sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $save_but_message='<div class="alert alert-success" role="alert"><div class="acc_inv_pos_run1">'.gks_lang('Επιτυχής δημιουργία του αρχείου εκτύπωσης').'</div><div class="acc_inv_pos_run2">'.gks_lang('Τι θέλετε να κάνετε ;').'</div>'.
  '<div class="acc_inv_pos_run3"><a href="'.$ret_print['url_file'].'"            class="gks_link" target="_blank">'.gks_lang('Άνοιγμα σε νέα καρτέλα').'</a></div>'.
  '<div class="acc_inv_pos_run4"><a href="'.$ret_print['url_file'].'&download=1" class="gks_link" target="_blank">'.gks_lang('Λήψη').'</a></div>';
  $save_but_message.='</div>';
  
  if (isset($ret_shortcode_full) and $ret_shortcode_full!='') {
    $qrhtml='';
    
    if (1==1) {
      include_once('vendor_inc/phpqrcode/qrlib.php');
  		$fileName = 'qr_code_temp_'.md5($ret_shortcode_full).'.png';
  	  $pngAbsoluteFilePath = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$fileName;
  	  $urlRelativeFilePath = GKS_SITE_URL.'my/temp/'.$fileName;
  	  // generating
  	  if (!file_exists($pngAbsoluteFilePath)) {
  	    QRcode::png($ret_shortcode_full, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 0);
  	  } 
  	  if (file_exists($pngAbsoluteFilePath)) {
  	    $qrhtml='<img src="'.$urlRelativeFilePath.'" class="accinvposimgqrcode"/>';
  	  }
  	}
	  
    
    $save_but_message.='<div class="alert alert-primary" role="alert">'.
    '<div class="acc_inv_pos_run5">'.gks_lang('Απόδειξη πελάτη').'</div>';
    
    
    
    $pos_sms_erp_app_mobile_id_code=trim_gks($row_pos['pos_sms_erp_app_mobile_id_code']);
    //$save_but_message.='|'.$pos_sms_erp_app_mobile_id_code.'|';
    if ($pos_sms_erp_app_mobile_id_code!='') {
      $save_but_message.=
      '<div class="acc_inv_pos_run8">'.
        '<span>'.gks_lang('SMS').':</span>'.
        '<input id="acc_inv_pos_run8_sms" type="tel" class="form-control form-control-sm" value="" placeholder="'.gks_lang('π.χ.').' 6912345678">'.
        '<button id="acc_inv_pos_run8_sms_run" class="btn btn-primary btn-sm" data-pos_sms_erp_app_mobile_id_code="'.$pos_sms_erp_app_mobile_id_code.'" data-url="'.base64_encode($ret_shortcode_full).'" data-objid="'.$id.'"><i class="fas fa-paper-plane"></i></button>'.
      '</div>'.
      '<div class="acc_inv_pos_run9"><span id="acc_inv_pos_run9_result"></span></div>';
    }
    
    $save_but_message.=
    '<div class="acc_inv_pos_run6"><a href="'.$ret_shortcode_full.'" class="gks_link" target="_blank">'.$ret_shortcode_full.'</a></div>'.
    ($qrhtml=='' ? '' : '<div class="acc_inv_pos_run7">'.$qrhtml.'</div>');
    
    $save_but_message.='</div>';
    
  }
  
  if (isset($ret_print['gks_erp_message'])) {
    $save_but_message.=$ret_print['gks_erp_message'];
  }
  if (isset($ret_print['gks_pos_client_send_fileto_url_message'])) {
    $save_but_message.=$ret_print['gks_pos_client_send_fileto_url_message'];
  }
  
  

  $save_but_message_all.=$save_but_message;
}


//////////////////////////////////////////////////         step print end        ////////////////////////////////////////////////// 
$pos_step='40print_end';
$sql="update gks_acc_inv set pos_step='".$pos_step."' where id_acc_inv=".$id." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

step_40print_end:

$save_but_message_all.='<div class="alert alert-secondary" role="alert">'.gks_lang('Μετάβαση στο παραστατικό').' <a href="admin-acc-inv-item.php?id='.$id.'&scrollto=gks_print" class="gks_link">#'.$id.'</a></div>';
  
$return = array('success' => true, 
  'insertid' => $id,
  'pos_step' => $pos_step,
  'message' => base64_encode('ok'), 
  'save_but_message' => base64_encode($save_but_message_all),
  'doc_item_url'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_print'),
  'thermal_url_file' => '',
);

if (isset($thermal_ret_shortcode_full)) {
  $return['thermal_url_file']=$thermal_ret_shortcode_full;
}

echo json_encode($return); die();















