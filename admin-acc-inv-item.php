<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

//echo date('Y-m-d H:i:s',1760283769);
//echo '<br>';
//echo date('Y-m-d H:i:s',1760295283);
//echo '<br>';
//echo date('Y-m-d H:i:s',(time()+15*60));
//die();

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_gks_acc_inv_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','view',0);
$perm_gks_acc_inv_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','edit',0);
$perm_gks_acc_inv_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','add',0);
$perm_gks_acc_inv_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','delete',0);

$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');

$gks_voip_params=gks_voip_user_params();

//gks_plugins_functions_run('functions_paroxos_invoice_after',array(
//  'id'=>&$id,
//  'doc_table' => 'gks_acc_inv',
//)); 
    
//gks_curl_post_async(GKS_SITE_URL.'my/cron_paroxos.php?get_files=1&id=11515',[]);
//die();


if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

if ($id==-1) {
  $nav_active_array=array('accounting','accounting_inv_new');  
} else {
  $nav_active_array=array('accounting','accounting_inv');
}

$user_companys=gks_get_companys_list();
if (count($user_companys)==0) {
  debug_mail(false,'company not found','');
  echo 'company not found';
  die(); 
}

$lang_prepare_gks_monades_metrisis=gks_lang_data_obj_prepare('gks_monades_metrisis','default');
gks_lang_data_obj_sql_prepare($lang_prepare_gks_monades_metrisis, array('monada_descr','monada_symbol'));
$sql="SELECT gks_monades_metrisis.id_monada,".
gks_lang_sql_field('monada_descr',$lang_prepare_gks_monades_metrisis).",".
gks_lang_sql_field('monada_symbol',$lang_prepare_gks_monades_metrisis)."
FROM ".$lang_prepare_gks_monades_metrisis['sql']['from1']." gks_monades_metrisis
".$lang_prepare_gks_monades_metrisis['sql']['from2']."
order by monada_sortorder,monada_descr";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
$monades=array(); 
while ($row = $result->fetch_assoc()) {
  $monades[]=array(
    'id' => $row['id_monada'],
    'descr' => $row['monada_descr'],
    'symbol' => $row['monada_symbol'],
  );
}


$sql="SELECT id_fpa_base,fpa_base_descr
FROM gks_eshop_fpa_base
WHERE fpa_base_disable=0
ORDER BY fpa_base_sortorder";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
$fpa_list=array(); 
while ($row = $result->fetch_assoc()) {
  $fpa_list[]=array(
    'id' => $row['id_fpa_base'],
    'descr' => $row['fpa_base_descr'],
  );
}

$sql="SELECT id_aade_katigoria_fpa, aade_katigoria_fpa_descr
FROM gks_aade_katigoria_fpa
where id_aade_katigoria_fpa not in (7,8)
ORDER BY sortorder";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
$fpa_aade=array(); 
while ($row = $result->fetch_assoc()) {
  $fpa_aade[]=array(
    'id' => $row['id_aade_katigoria_fpa'],
    'descr' => $row['aade_katigoria_fpa_descr'],
  );
}


//print '<pre>';
//print_r($user_companys);
//die();

//print '<pre>';print_r($gks_user_settings['print']);die();



$gks_custom_prepare=gks_custom_table_item_prepare('gks_acc_inv',['from'=>'item']);

$template_id=0; if (isset($_GET['template_id'])) $template_id=intval($_GET['template_id']);
if ($id==-1) {
  
  if ($template_id>0) {
    //admin-acc-inv-item.php?id=-1&template_id=10561
    //admin-acc-inv-item.php?id=-1&template_id=10571
    
    $sql=select_gks_acc_inv()." where gks_acc_inv.id_acc_inv = ".$template_id;
    
    if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")";
    if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
    if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
    if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
    
    //print '<pre>'.$sql;die();
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);die('sql error');
    }
    
    if ($result->num_rows!=1) {
      $template_id=0;
      debug_mail(false,'record not found sql tempate',$sql); 
      die('no record found (tempate)');
    } else {
      $row = $result->fetch_assoc();
      $row['id_acc_inv']=-1;
      $row['cancel_for_acc_inv_id']=0;
      $row['credit_memo_for_acc_inv_id']=0;
      $row['dimotikos_foros_for_acc_inv_id']=0;
      $row['from_aade_import']='';
      $row['import_inv_acc_seira_code']='';
      $row['import_inv_acc_number_str']='';
      $row['import_eidos_parastatikou_aade_code']='';
      $row['inv_guid']='';
      $row['acc_inv_ref_number']='';
      $row['inv_date']=date('Y-m-d H:i:s');
  
      $row['mydate_add']=null;
      $row['mydate_edit']=null;
      $row['user_id_add']=0;
      $row['user_id_edit']=0;
      $row['myip']='';
      $row['inv_state']='010draft';
      
      $row['merchant_ref_trns']='';
      $row['delivery_number']='';
      $row['dispatch_date']='';
      $row['dispatch_time']='';
      
      $row['aade_statuscode']='';
      $row['aade_invoiceuid']='';
      $row['aade_invoicemark']='';
      $row['aade_qrurl']='';
      $row['aade_paroxos_qrurl']='';
      $row['aade_send_date']='';
      $row['aade_errors']='';
      $row['aade_sending']='';
            
      $row['print_date']='';
      $row['print_file_name']='';
      $row['print_file_url']='';
      $row['print_user_id']='';
      $row['print_inv_state']='';
    
      $row['inv_acc_number_int']=0;
      $row['inv_acc_number_str']='';
      $row['bank_deposit_9digit']='';
      $row['from_aade_import_json']='';
          
      $row['gks_base_template_id']=$template_id;
      $my_page_title=gks_lang('Νέο Παραστατικό από το πρότυπο').' #'.$template_id;
    }
      
            
  }
  //echo '<pre>';print $template_id;die();
  
  if ($template_id==0) {
  
    
    $my_page_title=gks_lang('Νέο Παραστατικό');
    $row=array();
  
    $row['id_acc_inv']=-1;
    $row['cancel_for_acc_inv_id']=0;
    $row['credit_memo_for_acc_inv_id']=0;
    $row['dimotikos_foros_for_acc_inv_id']=0;
    $row['from_aade_import']='';
    $row['import_inv_acc_seira_code']='';
    $row['import_inv_acc_number_str']='';
    $row['import_eidos_parastatikou_aade_code']='';
    $row['inv_guid']='';
    $row['acc_inv_ref_number']='';
    $row['aade_skopos_diakinisis_id']=0;
    $row['aade_skopos_19_descr']='';
    $row['inv_date']=date('Y-m-d H:i:s');
    $row['mydate_add']=null;
    $row['mydate_edit']=null;
    $row['user_id_add']=0;
    $row['user_id_edit']=0;
    $row['gks_nickname_add']='';
    $row['gks_nickname_edit']='';
    $row['myip']='';
  
    $row['inv_state']='010draft';
    $row['user_id']=0;
    $row['gks_nickname']='';
    $row['user_first_name']='';
    $row['user_last_name']='';
    $row['user_email']='';
    $row['user_mobile']='';
    $row['user_lang']='el-GR';
    $row['lang_name']='';
    
    $row['eponimia']='';
    $row['title']='';
    $row['afm']='';
    $row['doy']='';
    $row['epaggelma']='';
    $row['ma_branch_fromuser']='';
    $row['ma_odos']='';
    $row['ma_arithmos']='';
    $row['ma_orofos']='';
    $row['ma_perioxi']='';
    $row['ma_poli']='';
    $row['ma_tk']='';
    $row['ma_country_id']=91;
    $row['ma_nomos_id']=26;
    $row['address_extra']=-1;
    
      
    
    $row['note_doc']='';
    $row['note_logistirio']='';
  
  
    
    $row['gks_price_net']=0;
    $row['gks_price_fpa']=0;
    $row['gks_price_netfpa']=0;
    $row['gks_price_total']=0;
    $row['totalWithheldAmount']=0;
    $row['totalOtherTaxesAmount']=0;
    $row['totalStampDutyamount']=0;
    $row['totalFeesAmount']=0;
    $row['totalDeductionsAmount']=0;
   
    
    
    $row['products_posotita']=0;
    $row['products_varos']=0;
    $row['products_ogos']=0;
    $row['products_ogos_max_x']=0;
    $row['products_ogos_max_y']=0;
    $row['products_ogos_max_z']=0;
    $row['products_need_apostoli']=0;
    $row['products_need_pliromi']=0;
    
    $row['tropos_apostolis']=1;
    $row['tropos_pliromis']=1;
    $row['tropos_pliromis_one_multi']=0;
    $row['kostos_apostolis']=0;
    $row['kostos_pliromis']=0;
    $row['destination_data_name']='';
    $row['destination_data_phone']='';
    $row['destination_data_odos']='';
    $row['destination_data_arithmos']='';
    $row['destination_data_orofos']='';
    $row['destination_data_perioxi']='';
    $row['destination_data_poli']='';
    $row['destination_data_tk']='';
    $row['destination_data_country_id']=0;
    $row['destination_data_nomos_id']=0;
    
    $row['fiscal_position_id']=1;
    $row['pricelist_id']=1;
    
    $row['pelati_sxolio']='';
    $row['order_sxolio']='';
    
    $row['delivery_id_8']=0;
    $row['delivery_number']='';
    $row['vehicle_number']='';
    $row['dispatch_date']='';
    $row['dispatch_time']='';
    $row['coupons']='';
    $row['def_ekptosi']=0;
  
    
  
    $row['company_afm']='';
    $row['company_id']=0;
    $row['company_sub_id']=0;
    if (count($user_companys)>=1) {
      foreach ($user_companys as $value) {
        $row['company_id']=$value['id_company'];
        $row['company_sub_id']=$value['id_company_sub'];
        $row['company_afm']=$value['company_afm'];
        break;
      } 
    }
    $row['inv_acc_journal_id']=0;
    $row['inv_acc_seira_id']=0;
    $row['inv_acc_seira_code']='';
    $row['inv_acc_number_int']=0;
    $row['inv_acc_number_str']='';
    $row['send_mydata']=0;
    $row['send_paroxos']=0;
    $row['is_xeirografi']=0;
    
    $row['acc_eidos_parastatikou_id']=0;
    $row['eidos_parastatikou_type_id']=0;
    $row['antisimvalomenos_label']=gks_lang('Πελάτης');
    $row['eidos_parastatikou_need_prev']=0;
    $row['eidos_parastatikou_has_fpa']=0;
    $row['eidos_parastatikou_has_othertaxes']='';
    $row['eidos_parastatikou_has_esoda']=0;
    $row['eidos_parastatikou_has_eksoda']=0;
    $row['eidos_parastatikou_need_afm']=1;
    $row['eidos_parastatikou_balance_pros']=0;
    $row['whi_eidos_parastatikou_stock_pros']=0;
    $row['whi_eidos_parastatikou_type_id']=0;
    $row['acc_eidos_parastatikou_other_entity']=0;
    $row['journal_has_correlated_invoices']=0;
    $row['journal_has_multiple_connected_marks']=0;
    $row['journal_has_packings_declarations']=0;
    $row['seira_isdeliverynote']=0;
    $row['seira_is_self_pricing']=0;
    $row['seira_is_vat_payment_suspension']=0;
    
    $row['aade_statuscode']='';
    $row['aade_invoiceuid']='';
    $row['aade_invoicemark']='';
    $row['aade_qrurl']='';
    $row['aade_paroxos_qrurl']='';
    $row['aade_send_date']='';
    $row['aade_errors']='';
    $row['aade_sending']='';  
       
    $row['print_date']='';
    $row['print_file_name']='';
    $row['print_file_url']='';
    $row['print_user_id']='';
    $row['print_inv_state']='';
  
    $row['affect_balance']=1;
    $row['affect_balance_all_poso']=1;
    $row['affect_balance_all_poso_type']='pliroteo';
    $row['affect_balance_poso']=0;
     
    $row['assigned_id']=0;
    $row['gks_nickname_assigned']='';
    $row['merchant_ref_trns']='';
    $row['crm_channel_id']=0;
    $row['crm_channel_sale_descr']='';
    $row['crm_channel_contact_id']=0;
    $row['crm_channel_contact_gks_nickname']='';
    $row['crm_channel_campain_id']=0;
    $row['ads_campain_name']='';
    $row['crm_channel_url']='';
    $row['crm_channel_code']='';
    $row['crm_channel_text']=''; 
    
    $row['warehouses_id_from']=0;
    $row['warehouse_name_from']='';
    $row['warehouses_id_to']=0;
    $row['warehouse_name_to']='';
    $row['bank_deposit_9digit']='';
  

    $row['from_aade_import_json']='';
    
    
    $row['gemi_number']='';
    $row['is_b2g']=0;
    $row['b2g_inv_aaht_name']='';
    $row['contract_reference']='';
    $row['project_reference']='';
    $row['b2g_inv_buyer_name']='';
    $row['b2g_inv_aaht_code']='';
    
    
    $row['b2g_aaht_foreas']='';
    $row['b2g_aaht_typos_forea']='';
    $row['b2g_aaht_kodikos_ekatharisis']='';
    
    $row['load_branch']='';
    $row['load_odos']='';
    $row['load_arithmos']='';
    $row['load_orofos']='';
    $row['load_perioxi']='';
    $row['load_poli']='';
    $row['load_tk']='';
    $row['load_country_id']=0;
    $row['load_nomos_id']=0;
    $row['nomos_descr_load']='';
    $row['country_name_load']='';
    
    
    $row['deli_branch']='';
    $row['deli_odos']='';
    $row['deli_arithmos']='';
    $row['deli_orofos']='';
    $row['deli_perioxi']='';
    $row['deli_poli']='';
    $row['deli_tk']='';
    $row['deli_country_id']=0;
    $row['deli_nomos_id']=0;
    $row['nomos_descr_deli']='';
    $row['country_name_deli']='';    
    
  }
  
  if (isset($gks_user_settings['gks_acc_inv']['def_values'])) {
    $def_values=unserialize($gks_user_settings['gks_acc_inv']['def_values']);  
    //print '<pre>';print_r($def_values);
    foreach ($def_values as $dkey => $dvalue) {
      if (isset($row[$dkey])) $row[$dkey]=$dvalue;
    }
  } 
  $row['paroxos_status']=0;
  $row['aade_paroxos_id']=0;
  $row['paroxos_send_pdf']='';
  $row['paroxos_tf1_url']='';
  $row['pos_id']=0;
  $row['pos_name']=0;
  $row['erp_app_mobile_id']=0;
  $row['erp_app_mobile_name']='';
   
} else {


  $sql=select_gks_acc_inv()." where gks_acc_inv.id_acc_inv = ".$id;
  
  if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  
  //print '<pre>'.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Παραστατικό').': #'.$id;
  
  if ($row['inv_date']=='') $row['inv_date']=$row['mydate_add'];
  
  
  
  //print '<pre>'; echo floatval($row['gks_price_netfpa']); die(); print_r($row);   
  
}

$inv_acc_seira_id=$row['inv_acc_seira_id'];

$paroxos_status=intval($row['paroxos_status']);
$aade_paroxos_id=intval($row['aade_paroxos_id']);
$print_inv_state=trim_gks($row['print_inv_state']);
$paroxos_send_pdf=trim_gks($row['paroxos_send_pdf']);
$print_file_name=trim_gks($row['print_file_name']);
$curr_inv_state=trim_gks($row['inv_state']);


$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);
//print '<pre>';print_r($gks_custom_row);die();

$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;

if ($row['inv_state']=='040cancelled' or $row['inv_state']=='070ypoekdosi' or $row['inv_state']=='080listing' or $row['inv_state']=='090ekdosi' or $row['inv_state']=='100payment') {
  $gks_lock=true;
} else {
  if ($row['inv_acc_number_int'] > 0 and $row['is_xeirografi']==0 and ($row['inv_state']=='010draft' or $row['inv_state']=='050proinvoice')) {
    $gks_number_lock=true;
  }
}
$credit_memo_for_acc_inv_id=$row['credit_memo_for_acc_inv_id'];
$cancel_for_acc_inv_id=$row['cancel_for_acc_inv_id'];
$dimotikos_foros_for_acc_inv_id=$row['dimotikos_foros_for_acc_inv_id'];

$antisimvalomenos_label=$row['antisimvalomenos_label'];
$acc_eidos_parastatikou_id=intval($row['acc_eidos_parastatikou_id']);
$eidos_parastatikou_type_id=intval($row['eidos_parastatikou_type_id']);
$eidos_parastatikou_need_prev=intval($row['eidos_parastatikou_need_prev']);
$eidos_parastatikou_has_fpa=intval($row['eidos_parastatikou_has_fpa']);
$eidos_parastatikou_has_othertaxes=trim_gks($row['eidos_parastatikou_has_othertaxes']);
$eidos_parastatikou_has_esoda=intval($row['eidos_parastatikou_has_esoda']);
$eidos_parastatikou_has_eksoda=intval($row['eidos_parastatikou_has_eksoda']);
$eidos_parastatikou_need_afm=intval($row['eidos_parastatikou_need_afm']);
$eidos_parastatikou_balance_pros=intval($row['eidos_parastatikou_balance_pros']);
$whi_eidos_parastatikou_stock_pros=intval($row['whi_eidos_parastatikou_stock_pros']);
$whi_eidos_parastatikou_type_id=intval($row['whi_eidos_parastatikou_type_id']);
$acc_eidos_parastatikou_other_entity=intval($row['acc_eidos_parastatikou_other_entity']);
$journal_has_correlated_invoices=intval($row['journal_has_correlated_invoices']);
$journal_has_multiple_connected_marks=intval($row['journal_has_multiple_connected_marks']);
$journal_has_packings_declarations=intval($row['journal_has_packings_declarations']);
$seira_isdeliverynote=intval($row['seira_isdeliverynote']);
$seira_is_self_pricing=intval($row['seira_is_self_pricing']);
if($seira_is_self_pricing) {
  $eidos_parastatikou_has_esoda=0;
  $eidos_parastatikou_has_eksoda=1;
}
$seira_is_vat_payment_suspension=intval($row['seira_is_vat_payment_suspension']);


$antisimvalomenos_label_org=$antisimvalomenos_label;
$acc_eidos_parastatikou_id_org=$acc_eidos_parastatikou_id;
$acc_eidos_parastatikou_type_id_org=$eidos_parastatikou_type_id;
$whi_eidos_parastatikou_stock_pros_org=$whi_eidos_parastatikou_stock_pros;
$whi_eidos_parastatikou_type_id_org=$whi_eidos_parastatikou_type_id;


$credit_memo_descr_for='';
if ($credit_memo_for_acc_inv_id!=0) { //check ean einai akirotiko gia allo
  
  $org_gks_price_net=0;
  $org_gks_price_fpa=0; 
  $others_count=0;
  $others_gks_price_net_sum=0;
  $others_gks_price_fpa_sum=0; 
  $rest_gks_price_net_sum=0;
  $rest_gks_price_fpa_sum=0;
  
  $sql_credit_memo="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
  gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
  gks_acc_inv.gks_price_net,gks_acc_inv.gks_price_fpa
  FROM ((((gks_acc_inv 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
  where gks_acc_inv.id_acc_inv=".$credit_memo_for_acc_inv_id;
  $result_credit_memo = $db_link->query($sql_credit_memo);        
  if (!$result_credit_memo) {debug_mail(false,'error sql',$sql_credit_memo);die('sql error');}
  if ($result_credit_memo->num_rows==0) {
    $credit_memo_descr_for=gks_lang('Δεν βρέθηκε το παραστατικό με ID').': '.
    '<a href="admin-acc-inv-item.php?id='.$credit_memo_for_acc_inv_id.'" class="alert-link">'.$credit_memo_for_acc_inv_id.'</a>';
    debug_mail(false,'record parent not found sql',$sql_credit_memo); 
    //die('no record found (2)');
  } else {
    $row_credit_memo = $result_credit_memo->fetch_assoc();
  
    $org_gks_price_net=$row_credit_memo['gks_price_net'];
    $org_gks_price_fpa=$row_credit_memo['gks_price_fpa']; 

  
    $antisimvalomenos_label=$row_credit_memo['antisimvalomenos_label'];
    $acc_eidos_parastatikou_id=intval($row_credit_memo['acc_eidos_parastatikou_id']);
    $eidos_parastatikou_type_id=intval($row_credit_memo['eidos_parastatikou_type_id']);
    $eidos_parastatikou_need_prev=intval($row_credit_memo['eidos_parastatikou_need_prev']);
    $eidos_parastatikou_has_fpa=intval($row_credit_memo['eidos_parastatikou_has_fpa']);
    $eidos_parastatikou_has_othertaxes=trim_gks($row_credit_memo['eidos_parastatikou_has_othertaxes']);
    $eidos_parastatikou_has_esoda=intval($row_credit_memo['eidos_parastatikou_has_esoda']);
    $eidos_parastatikou_has_eksoda=intval($row_credit_memo['eidos_parastatikou_has_eksoda']);
    $eidos_parastatikou_need_afm=intval($row_credit_memo['eidos_parastatikou_need_afm']);
    //$eidos_parastatikou_balance_pros=intval($row_credit_memo['eidos_parastatikou_balance_pros']);
    $whi_eidos_parastatikou_stock_pros=intval($row_credit_memo['whi_eidos_parastatikou_stock_pros']);
    $whi_eidos_parastatikou_type_id=intval($row_credit_memo['whi_eidos_parastatikou_type_id']);
    
    $credit_memo_descr_for=
        '<a href="admin-acc-inv-item.php?id='.$credit_memo_for_acc_inv_id.'" class="alert-link">'.
        '#' .$credit_memo_for_acc_inv_id.', '.
        $row_credit_memo['acc_journal_descr'].', '.
        $row_credit_memo['seira_code'].' - '.$row_credit_memo['seira_descr'].', '.
        showDate(strtotime($row_credit_memo['inv_date']), 'd/m/Y H:i', 1).', '.
        $row_credit_memo['inv_acc_number_int'].', '.
        '<span class="acc_inv_state_'.$row_credit_memo['inv_state'].'">'.getAccInvStateDescr($row_credit_memo['inv_state']).'</span></a>';
  }
  
  $sql_others="SELECT count(*) as others_count, Sum(gks_price_net) AS gks_price_net_sum, Sum(gks_price_fpa) AS gks_price_fpa_sum
  FROM gks_acc_inv
  WHERE credit_memo_for_acc_inv_id=".$credit_memo_for_acc_inv_id." AND id_acc_inv<>".$id;
  //echo $sql_others; die();
  $result_others = $db_link->query($sql_others);        
  if (!$result_others) {debug_mail(false,'error sql',$sql_others);die('sql error');}
  if ($result_others->num_rows>=1) {
    $row_others = $result_others->fetch_assoc();
    if (empty($row_others['gks_price_net_sum'])==false) $others_gks_price_net_sum=floatval($row_others['gks_price_net_sum']);
    if (empty($row_others['gks_price_fpa_sum'])==false) $others_gks_price_fpa_sum=floatval($row_others['gks_price_fpa_sum']);
    if (empty($row_others['others_count'])==false) $others_count=floatval($row_others['others_count']);
  }
  $rest_gks_price_net_sum=$org_gks_price_net-$others_gks_price_net_sum;
  $rest_gks_price_fpa_sum=$org_gks_price_fpa-$others_gks_price_fpa_sum; 
  
  
  $gks_number_lock=true;
  $gks_user_lock=true;
  //$gks_lock=true;
}

$canceled_descr_for='';
if ($cancel_for_acc_inv_id!=0) { //check ean einai akirotiko gia allo
  
  $sql_canceled="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
  gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state
  
  FROM ((((gks_acc_inv 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
  where gks_acc_inv.id_acc_inv=".$cancel_for_acc_inv_id;
  $result_canceled = $db_link->query($sql_canceled);        
  if (!$result_canceled) {debug_mail(false,'error sql',$sql_canceled);die('sql error');}
  if ($result_canceled->num_rows==0) {
    $canceled_descr_for=gks_lang('Δεν βρέθηκε το παραστατικό με ID').': '.
    '<a href="admin-acc-inv-item.php?id='.$cancel_for_acc_inv_id.'" class="alert-link">'.$cancel_for_acc_inv_id.'</a>';
    debug_mail(false,'record parent not found sql',$sql_canceled); 
    die('no record found (3)');
  } else {
    $row_canceled = $result_canceled->fetch_assoc();
  
    $antisimvalomenos_label=$row_canceled['antisimvalomenos_label'];
    //$acc_eidos_parastatikou_id=intval($row_canceled['acc_eidos_parastatikou_id']);
    //$eidos_parastatikou_type_id=intval($row_canceled['eidos_parastatikou_type_id']);
    $eidos_parastatikou_need_prev=intval($row_canceled['eidos_parastatikou_need_prev']);
    $eidos_parastatikou_has_fpa=intval($row_canceled['eidos_parastatikou_has_fpa']);
    $eidos_parastatikou_has_othertaxes=trim_gks($row_canceled['eidos_parastatikou_has_othertaxes']);
    $eidos_parastatikou_has_esoda=intval($row_canceled['eidos_parastatikou_has_esoda']);
    $eidos_parastatikou_has_eksoda=intval($row_canceled['eidos_parastatikou_has_eksoda']);
    $eidos_parastatikou_need_afm=intval($row_canceled['eidos_parastatikou_need_afm']);
    $eidos_parastatikou_balance_pros=-intval($row_canceled['eidos_parastatikou_balance_pros']);
    $whi_eidos_parastatikou_stock_pros=intval($row_canceled['whi_eidos_parastatikou_stock_pros']);
    $whi_eidos_parastatikou_type_id=intval($row_canceled['whi_eidos_parastatikou_type_id']);
    
    $canceled_descr_for=
        '<a href="admin-acc-inv-item.php?id='.$cancel_for_acc_inv_id.'" class="alert-link">'.
        '#' .$cancel_for_acc_inv_id.', '.
        $row_canceled['acc_journal_descr'].', '.
        $row_canceled['seira_code'].' - '.$row_canceled['seira_descr'].', '.
        showDate(strtotime($row_canceled['inv_date']), 'd/m/Y H:i', 1).', '.
        $row_canceled['inv_acc_number_int'].', '.
        '<span class="acc_inv_state_'.$row_canceled['inv_state'].'">'.getAccInvStateDescr($row_canceled['inv_state']).'</span></a>';
    $gks_lock=true;
  }
}

$credit_memo_descr_by='';
if ($id>=0) {
  //check ean einai credit_memo
  $sql_credit_memo="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.inv_date, gks_acc_inv.inv_state, 
  gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_journal.acc_journal_descr, 
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr
  FROM (gks_acc_inv 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
  WHERE gks_acc_inv.credit_memo_for_acc_inv_id=".$id;
  $result_credit_memo = $db_link->query($sql_credit_memo);  
  if (!$result_credit_memo) {
    debug_mail(false,'error sql',$sql_credit_memo);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_credit_memo->num_rows>=1) {
    $tmp_array=array();
    while ($row_credit_memo = $result_credit_memo->fetch_assoc()) {
      

      
        $tmp_array[]=
        '<a href="admin-acc-inv-item.php?id='.$row_credit_memo['id_acc_inv'].'" class="alert-link">'.
        '#' .$row_credit_memo['id_acc_inv'].', '.
        $row_credit_memo['acc_journal_descr'].', '.
        $row_credit_memo['seira_code'].' - '.$row_credit_memo['seira_descr'].', '.
        showDate(strtotime($row_credit_memo['inv_date']), 'd/m/Y H:i', 1).', '.
        $row_credit_memo['inv_acc_number_int'].', '.
        '<span class="acc_inv_state_'.$row_credit_memo['inv_state'].'">'.getAccInvStateDescr($row_credit_memo['inv_state']).'</span></a>';
      
    }
    if (count($tmp_array)==1) {
      $credit_memo_descr_by=gks_lang('Το συσχετιζόμενο παραστατικό είναι το').': '.$tmp_array[0];
    } else if (count($tmp_array)>=2) {
      $credit_memo_descr_by=gks_lang('Τα συσχετιζόμενα παραστατικό είναι τα').':<br>'.implode('<br>',$tmp_array);
    }
    
    
    
  }
}

$canceled_descr_by='';
if ($id>=0) {
  //check ean einai akyromeno
  $sql_canceled="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.inv_date, gks_acc_inv.inv_state, 
  gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_journal.acc_journal_descr, 
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr
  FROM (gks_acc_inv 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
  WHERE gks_acc_inv.cancel_for_acc_inv_id=".$id;
  $result_canceled = $db_link->query($sql_canceled);  
  if (!$result_canceled) {
    debug_mail(false,'error sql',$sql_canceled);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_canceled->num_rows>=1) {
    $row_canceled = $result_canceled->fetch_assoc();
    if ($row_canceled['id_acc_inv']!=0) {
      $canceled_descr_by=
      '<a href="admin-acc-inv-item.php?id='.$row_canceled['id_acc_inv'].'" class="alert-link">'.
      '#' .$row_canceled['id_acc_inv'].', '.
      $row_canceled['acc_journal_descr'].', '.
      $row_canceled['seira_code'].' - '.$row_canceled['seira_descr'].', '.
      showDate(strtotime($row_canceled['inv_date']), 'd/m/Y H:i', 1).', '.
      $row_canceled['inv_acc_number_int'].', '.
      '<span class="acc_inv_state_'.$row_canceled['inv_state'].'">'.getAccInvStateDescr($row_canceled['inv_state']).'</span></a>';
    }
  }
}


//dimotikos_foros_for_acc_inv_id
//dimotikos_foros_descr_for
//dimotikos_foros_descr_by
$dimotikos_foros_descr_for='';
if ($dimotikos_foros_for_acc_inv_id!=0) { //check 
  
  $sql_dimotikos_foros="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
  gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
  gks_acc_inv.gks_price_net,gks_acc_inv.gks_price_fpa
  FROM ((((gks_acc_inv 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
  where gks_acc_inv.id_acc_inv=".$dimotikos_foros_for_acc_inv_id;
  $result_dimotikos_foros = $db_link->query($sql_dimotikos_foros);        
  if (!$result_dimotikos_foros) {debug_mail(false,'error sql',$sql_dimotikos_foros);die('sql error');}
  if ($result_dimotikos_foros->num_rows==0) {
    $dimotikos_foros_descr_for=gks_lang('Δεν βρέθηκε το παραστατικό με ID').': '.
    '<a href="admin-acc-inv-item.php?id='.$dimotikos_foros_for_acc_inv_id.'" class="alert-link">'.$dimotikos_foros_for_acc_inv_id.'</a>';
    debug_mail(false,'record parent not found sql',$sql_dimotikos_foros); 
    //die('no record found (2)');
  } else {
    $row_dimotikos_foros = $result_dimotikos_foros->fetch_assoc();
    $dimotikos_foros_descr_for=
        '<a href="admin-acc-inv-item.php?id='.$dimotikos_foros_for_acc_inv_id.'" class="alert-link">'.
        '#' .$dimotikos_foros_for_acc_inv_id.', '.
        $row_dimotikos_foros['acc_journal_descr'].', '.
        $row_dimotikos_foros['seira_code'].' - '.$row_dimotikos_foros['seira_descr'].', '.
        showDate(strtotime($row_dimotikos_foros['inv_date']), 'd/m/Y H:i', 1).', '.
        $row_dimotikos_foros['inv_acc_number_int'].', '.
        '<span class="acc_inv_state_'.$row_dimotikos_foros['inv_state'].'">'.getAccInvStateDescr($row_dimotikos_foros['inv_state']).'</span></a>';
  }
}



$dimotikos_foros_descr_by='';
if ($id>=0) {
  //check ean einai credit_memo
  $sql_dimotikos_foros="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.inv_date, gks_acc_inv.inv_state, 
  gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_journal.acc_journal_descr, 
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr
  FROM (gks_acc_inv 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
  WHERE gks_acc_inv.dimotikos_foros_for_acc_inv_id=".$id;
  $result_dimotikos_foros = $db_link->query($sql_dimotikos_foros);  
  if (!$result_dimotikos_foros) {
    debug_mail(false,'error sql',$sql_dimotikos_foros);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_dimotikos_foros->num_rows>=1) {
    $tmp_array=array();
    while ($row_dimotikos_foros = $result_dimotikos_foros->fetch_assoc()) {
        $tmp_array[]=
        '<a href="admin-acc-inv-item.php?id='.$row_dimotikos_foros['id_acc_inv'].'" class="alert-link">'.
        '#' .$row_dimotikos_foros['id_acc_inv'].', '.
        $row_dimotikos_foros['acc_journal_descr'].', '.
        $row_dimotikos_foros['seira_code'].' - '.$row_dimotikos_foros['seira_descr'].', '.
        showDate(strtotime($row_dimotikos_foros['inv_date']), 'd/m/Y H:i', 1).', '.
        $row_dimotikos_foros['inv_acc_number_int'].', '.
        '<span class="acc_inv_state_'.$row_dimotikos_foros['inv_state'].'">'.getAccInvStateDescr($row_dimotikos_foros['inv_state']).'</span></a>';
    }
    if (count($tmp_array)==1) {
      $dimotikos_foros_descr_by=gks_lang('Το συσχετιζόμενο παραστατικό είναι το').': '.$tmp_array[0];
    } else if (count($tmp_array)>=2) {
      $dimotikos_foros_descr_by=gks_lang('Τα συσχετιζόμενα παραστατικό είναι τα').':<br>'.implode('<br>',$tmp_array);
    }
  }
}


//echo $row['ma_country_id'];
//die();

//if ($id>0) {
//  
//  $mybasketarray['user']['first_name'] = trim_gks($row['user_first_name']);
//  $mybasketarray['user']['last_name'] = trim_gks($row['user_last_name']);
//  $mybasketarray['user']['email'] = trim_gks($row['user_email']);
//  $mybasketarray['user']['mobile'] = trim_gks($row['user_mobile']);
//  $mybasketarray['user']['lang'] = trim_gks($row['user_lang']);
//  $mybasketarray['user']['ma_odos'] = trim_gks($row['ma_odos']);
//  $mybasketarray['user']['ma_perioxi'] = trim_gks($row['ma_perioxi']);
//  $mybasketarray['user']['ma_poli'] = trim_gks($row['ma_poli']);
//  $mybasketarray['user']['ma_tk'] = trim_gks($row['ma_tk']);
//  $mybasketarray['user']['ma_country_id'] = intval($row['ma_country_id']);
//  $mybasketarray['user']['ma_nomos_id'] = intval($row['ma_nomos_id']);
//  
//  $mybasketarray['user']['eponimia'] = trim_gks($row['eponimia']);
//  $mybasketarray['user']['title'] = trim_gks($row['title']);
//  $mybasketarray['user']['afm'] = trim_gks($row['afm']);
//  $mybasketarray['user']['doy'] = trim_gks($row['doy']);
//  $mybasketarray['user']['epaggelma'] = trim_gks($row['epaggelma']);
//  
//
//  $mybasketarray['user_other']['first_name'] = trim_gks($row['other_first_name']);
//  $mybasketarray['user_other']['last_name'] = trim_gks($row['other_last_name']);
//  $mybasketarray['user_other']['email'] = trim_gks($row['other_email']);
//  $mybasketarray['user_other']['mobile'] = trim_gks($row['other_mobile']);
//  $mybasketarray['user_other']['lang'] = trim_gks($row['other_lang']);
//  $mybasketarray['user_other']['ma_odos'] = trim_gks($row['other_ma_odos']);
//  $mybasketarray['user_other']['ma_perioxi'] = trim_gks($row['other_ma_perioxi']);
//  $mybasketarray['user_other']['ma_poli'] = trim_gks($row['other_ma_poli']);
//  $mybasketarray['user_other']['ma_tk'] = trim_gks($row['other_ma_tk']);
//  $mybasketarray['user_other']['ma_country_id'] = intval($row['other_ma_country_id']);
//  $mybasketarray['user_other']['ma_nomos_id'] = intval($row['other_ma_nomos_id']);
//  
//  

//
//}


$pelati_sxolio=nl2br_gks($row['pelati_sxolio']);
$order_sxolio=nl2br_gks($row['order_sxolio']);



$row['ma_country_id']=intval($row['ma_country_id']);
$row['ma_poli']=trim_gks($row['ma_poli']);


$inv_state=$row['inv_state'];
$products_posotita=$row['products_posotita'];
$products_varos=$row['products_varos'];
$products_ogos=$row['products_ogos'];;
$products_ogos_max_x=$row['products_ogos_max_x'];
$products_ogos_max_y=$row['products_ogos_max_y'];
$products_ogos_max_z=$row['products_ogos_max_z'];
$products_need_apostoli=$row['products_need_apostoli']==0 ? false : true;
$products_need_pliromi=$row['products_need_pliromi']==0 ? false : true;

$pliroteo=$row['gks_price_total'] + $row['kostos_apostolis'] + $row['kostos_pliromis'];

unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='acc_inv';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']= $row['company_id'];
$mybasketarray['company_sub_id']= $row['company_sub_id'];
$mybasketarray['inv_acc_journal_id']=intval($row['inv_acc_journal_id']);
$mybasketarray['inv_acc_seira_id']=intval($row['inv_acc_seira_id']);
$mybasketarray['inv_state']=trim_gks($row['inv_state']);
$mybasketarray['inv_date']=trim_gks($row['inv_date']);

$mybasketarray['user']['user_id']=$row['user_id'];
$mybasketarray['user']['afm']=$row['afm'];
$mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
$mybasketarray['parastatiko']= $eidos_parastatikou_need_afm;



$mybasketarray['products_varos']= $products_varos;
$mybasketarray['products_ogos']= $products_ogos;
$mybasketarray['products_ogos_max_x']= $products_ogos_max_x;
$mybasketarray['products_ogos_max_y']= $products_ogos_max_y;
$mybasketarray['products_ogos_max_z']= $products_ogos_max_z;
$mybasketarray['products_need_apostoli']=$products_need_apostoli;
$mybasketarray['products_need_pliromi']=$products_need_pliromi;
$mybasketarray['destination_data']['name'] = trim_gks($row['destination_data_name']);
$mybasketarray['destination_data']['phone'] = trim_gks($row['destination_data_phone']);
$mybasketarray['destination_data']['odos'] = trim_gks($row['destination_data_odos']);
$mybasketarray['destination_data']['arithmos'] = trim_gks($row['destination_data_arithmos']);
$mybasketarray['destination_data']['orofos'] = trim_gks($row['destination_data_orofos']);
$mybasketarray['destination_data']['perioxi'] = trim_gks($row['destination_data_perioxi']);
$mybasketarray['destination_data']['poli'] = trim_gks($row['destination_data_poli']);
$mybasketarray['destination_data']['tk'] = trim_gks($row['destination_data_tk']);
$mybasketarray['destination_data']['country_id'] = intval($row['destination_data_country_id']);
$mybasketarray['destination_data']['nomos_id'] = intval($row['destination_data_nomos_id']);
$mybasketarray['tropos_apostolis'] = $row['tropos_apostolis'];
$mybasketarray['tropos_pliromis'] = $row['tropos_pliromis'];
$mybasketarray['products_total'] = $row['gks_price_total'];

$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray,-1);
$mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray,-1);

//print '<pre>';
//print_r($mybasketarray);
//print_r($mybasketarray['tropoi_pliromis_all']);
//die();


if ($row['tropos_apostolis']>0 and isset($mybasketarray['tropoi_apostolis_all'][$row['tropos_apostolis']])) $mybasketarray['tropoi_apostolis_all'][$row['tropos_apostolis']]['dm_calc_kostos']= $row['kostos_apostolis'];
if ($row['tropos_pliromis']>0 and isset($mybasketarray['tropoi_pliromis_all'][$row['tropos_pliromis']])) $mybasketarray['tropoi_pliromis_all'][$row['tropos_pliromis']]['pa_calc_kostos']= $row['kostos_pliromis'];


$mybasketarray['coupons']=array();
$coupons = trim_gks($row['coupons']);
$coupons_parts=explode('|',$coupons);
foreach ($coupons_parts as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    $mybasketarray['coupons'][$value]=$value;
    $sql_coupon="SELECT pricelist_item_descr
    FROM gks_eshop_pricelist_items
    WHERE pricelist_item_coupon='".$db_link->escape_string($value)."'
    AND pricelist_id=".$row['pricelist_id'];
    $result_coupon = $db_link->query($sql_coupon);        
    if (!$result_coupon) {
      debug_mail(false,'error sql',$sql_coupon);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result_coupon->num_rows==1) {
      $row_coupon = $result_coupon->fetch_assoc();
      $mybasketarray['coupons'][$value]=$row_coupon['pricelist_item_descr'];
    }
    
    
  }
}

//$mybasketarray['coupons']['Meion20']='Meion 20%';
//$mybasketarray['coupons']['Meion21']='Meion 21%';
//$mybasketarray['coupons']['Meion22']='Meion 22%';
//$mybasketarray['coupons']['Meion23']='Meion 23%';
//$mybasketarray['coupons']['Meion24']='Meion 24%';
//$mybasketarray['coupons']['Meion25']='Meion 25%';





//print '<pre>';print_r($correlated_invoices_array);die();



$sql_other_entity="SELECT gks_acc_inv_other_entity.*, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_aade_entitytype.aade_entitytype_descr, 
gks_country.country_initials, gks_country.country_name, 
gks_nomoi.nomos_descr
FROM (((gks_acc_inv_other_entity 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv_other_entity.entity_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_aade_entitytype ON gks_acc_inv_other_entity.aade_entitytype_id = gks_aade_entitytype.id_aade_entitytype) 
LEFT JOIN gks_country ON gks_acc_inv_other_entity.entity_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_acc_inv_other_entity.entity_nomos_id = gks_nomoi.id_nomos
where acc_inv_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
order by entity_aa";
$result_other_entity = $db_link->query($sql_other_entity);        
if (!$result_other_entity) {debug_mail(false,'error sql',$sql_other_entity); die('sql error');}

$other_entity_array=array();
while ($row_other_entity = $result_other_entity->fetch_assoc()) {
  if ($id<0) $row_other_entity['id_acc_inv_other_entity']=0;
  $row_other_entity['user_extra_address']=array();
  $row_other_entity['user_extra_address'][]=array(
    'id'=> -1,
    'descr' => gks_lang('Κεντρικό'),
  );
  $other_entity_array[]=$row_other_entity;
}
foreach ($other_entity_array as &$oe_item) {
  if ($oe_item['entity_user_id']>0) {
    $sql_oeea="select id_users_extra_address,ea_name 
    from gks_users_extra_address
    where user_id=".$oe_item['entity_user_id']."
    order by id_users_extra_address";
    $result_oeea = $db_link->query($sql_oeea);        
    if (!$result_oeea) {debug_mail(false,'error sql',$sql_oeea); die('sql error');}
    while ($row_oeea = $result_oeea->fetch_assoc()) {
      $oe_item['user_extra_address'][]=array(
        'id'=> $row_oeea['id_users_extra_address'],
        'descr' => $row_oeea['ea_name'],
      );
    }
  }
}
unset($oe_item);

//print '<pre>';print_r($other_entity_array);die();

$sql_correlated_invoices="SELECT gks_acc_inv_correlated_invoices.* 
FROM gks_acc_inv_correlated_invoices 
where acc_inv_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
order by coi_aa";
$result_correlated_invoices = $db_link->query($sql_correlated_invoices);        
if (!$result_correlated_invoices) {debug_mail(false,'error sql',$sql_correlated_invoices); die('sql error');}
$correlated_invoices_array=array();
while ($row_correlated_invoices = $result_correlated_invoices->fetch_assoc()) {
  if ($id<0) $row_correlated_invoices['id_acc_inv_correlated_invoices']=0;
  $row_correlated_invoices['coi_mark']=trim_gks($row_correlated_invoices['coi_mark']);
  if ($row_correlated_invoices['coi_mark']=='') {
    $row_correlated_invoices['coi_mark']=gks_aade_get_mark_from_id($row_correlated_invoices);
  }
  
  $correlated_invoices_array[]=$row_correlated_invoices;
}

$sql_multiple_connected_marks="SELECT gks_acc_inv_multiple_connected_marks.* 
FROM gks_acc_inv_multiple_connected_marks 
where acc_inv_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
order by mcm_aa";
$result_multiple_connected_marks = $db_link->query($sql_multiple_connected_marks);        
if (!$result_multiple_connected_marks) {debug_mail(false,'error sql',$sql_multiple_connected_marks); die('sql error');}
$multiple_connected_marks_array=array();
while ($row_multiple_connected_marks = $result_multiple_connected_marks->fetch_assoc()) {
  if ($id<0) $row_multiple_connected_marks['id_acc_inv_multiple_connected_marks']=0;
  $row_multiple_connected_marks['mcm_mark']=trim_gks($row_multiple_connected_marks['mcm_mark']);
  if ($row_multiple_connected_marks['mcm_mark']=='') {
    $row_multiple_connected_marks['mcm_mark']=gks_aade_get_mark_from_id($row_multiple_connected_marks);
  }
  $multiple_connected_marks_array[]=$row_multiple_connected_marks;
}

$sql_packings_declarations="SELECT gks_acc_inv_packings_declarations.* 
FROM gks_acc_inv_packings_declarations 
where acc_inv_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
order by packaging_aa";
$result_packings_declarations = $db_link->query($sql_packings_declarations);        
if (!$result_packings_declarations) {debug_mail(false,'error sql',$sql_packings_declarations); die('sql error');}
$packings_declarations_array=array();
while ($row_packings_declarations = $result_packings_declarations->fetch_assoc()) {
  if ($id<0) $row_packings_declarations['id_acc_inv_packings_declarations']=0;
  $packings_declarations_array[]=$row_packings_declarations;
}
//print '<pre>';print_r($packings_declarations_array);die();

$packagingTypes=[];
for ($i = 1; $i <= $getAADE_PackagingTypeDescr_max; $i++) {
  $packagingTypes[]=array('id' => $i, 'descr' => getAADE_PackagingTypeDescr($i));
}
//print '<pre>';print_r($packagingTypes);die();


$sql_eidi="SELECT gks_acc_inv_products.*, 
gks_eshop_products.product_code, gks_eshop_products.product_descr_big, 
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_photo<>'' THEN
        gks_eshop_products.product_photo
      ELSE
        gks_eshop_products_parent.product_photo
    END
  ELSE gks_eshop_products.product_photo
END as product_photo_p,
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_descr_small<>'' THEN
        gks_eshop_products.product_descr_small
      ELSE
        CASE
          WHEN gks_eshop_products.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
          ELSE
            gks_eshop_products_parent.product_descr_small
        END
    END
  ELSE gks_eshop_products.product_descr_small
END as product_descr_small_p,

gks_monades_metrisis.monada_descr, gks_monades_metrisis.monada_symbol,
gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
gks_eshop_pricelist.pricelist_descr,
gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_type, 
gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_descr,
gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_type,
gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr,
gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_type,
gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr,

gks_aade_katigoria_telon.aade_katigoria_telon_type, 
gks_aade_katigoria_telon.aade_katigoria_telon_descr,
gks_aade_katigoria_fpa_ejeresi.aade_katigoria_fpa_ejeresi_descr,

gks_eshop_products.product_lot_serial

FROM (((((((((gks_acc_inv_products 
LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada) 
LEFT JOIN gks_eshop_fpa ON gks_acc_inv_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
LEFT JOIN gks_eshop_pricelist ON gks_acc_inv_products.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_acc_inv_products.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron) 
LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_acc_inv_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron)
LEFT JOIN gks_aade_katigoria_xartosimou ON gks_acc_inv_products.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou)
LEFT JOIN gks_aade_katigoria_telon ON gks_acc_inv_products.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon) 
LEFT JOIN gks_aade_katigoria_fpa_ejeresi ON gks_acc_inv_products.product_fpa_ejeresi_id = gks_aade_katigoria_fpa_ejeresi.id_aade_katigoria_fpa_ejeresi

WHERE gks_acc_inv_products.acc_inv_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
ORDER BY gks_acc_inv_products.product_aa;";
//gks_acc_inv_products.product_set
$result_eidi = $db_link->query($sql_eidi);        
if (!$result_eidi) {debug_mail(false,'error sql',$sql_eidi); die('sql error');}

$eidos_array = array();
$products_sets=array();
$products_count=0;

$id_acc_inv_product_array=array();

while ($eidos = $result_eidi->fetch_assoc()) {
  if ($eidos['product_id']==2) $eidos['product_id']=0;
  $eidos_array[]=$eidos;
  $products_count++;
  $id_acc_inv_product_array[]=$eidos['id_acc_inv_product'];
  $parts=explode(',',trim_gks($eidos['product_set']));
  foreach ($parts as $myset) {
    $myset=trim_gks($myset);
    if ($myset!='') {
      if (isset($products_sets[$myset])==false) $products_sets[$myset]=array();
      $products_sets[$myset][]= $eidos['id_acc_inv_product'];
    }
  }
}
//print '<pre>';print_r($eidos_array);die();


gks_CheckAFM_Live($mybasketarray);
//echo number_format(microtime(true) - $dev_page_starttime,2,',','.');die();
$check_vies=$mybasketarray['check_vies'];
//print '<pre>';print_r($mybasketarray['check_vies']);die();



gks_cache_acc_inv_item();






$products_income=array();
$products_expenses=array();
$products_lots_serials=array();

if (count($id_acc_inv_product_array) > 0) {
  
  if ($eidos_parastatikou_has_esoda!=0) {
    $sql_income="select acc_inv_product_id as id,
    aade_typos_xarakt_esodon_id as typos_id,
    aade_katigoria_xarakt_esodon_id as cat_id,
    acc_inv_product_income_ammount as ammount,
    aade_katigoria_xarakt_esodon_descr,
    aade_typos_xarakt_esodon_descr
    FROM (gks_acc_inv_products_income 
    LEFT JOIN gks_aade_katigoria_xarakt_esodon ON gks_acc_inv_products_income.aade_katigoria_xarakt_esodon_id = gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon) 
    LEFT JOIN gks_aade_typos_xarakt_esodon ON gks_acc_inv_products_income.aade_typos_xarakt_esodon_id = gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon
    where acc_inv_product_id in (".implode(',',$id_acc_inv_product_array).")
    order by id_acc_inv_product_income";
    $result_income = $db_link->query($sql_income);        
    if (!$result_income) {debug_mail(false,'error sql',$sql_income); die('sql error');}
    while ($row_income = $result_income->fetch_assoc()) {
      $products_income[$row_income['id']][]=$row_income;
    }
  }
  
  if ($eidos_parastatikou_has_eksoda!=0) {
    $sql_expenses="select acc_inv_product_id as id,
    aade_typos_xarakt_eksodon_id as typos_id,
    aade_katigoria_xarakt_eksodon_id as cat_id,
    acc_inv_product_expenses_ammount as ammount,
    aade_katigoria_xarakt_eksodon_descr,
    aade_typos_xarakt_eksodon_descr
    FROM (gks_acc_inv_products_expenses 
    LEFT JOIN gks_aade_katigoria_xarakt_eksodon ON gks_acc_inv_products_expenses.aade_katigoria_xarakt_eksodon_id = gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon) 
    LEFT JOIN gks_aade_typos_xarakt_eksodon ON gks_acc_inv_products_expenses.aade_typos_xarakt_eksodon_id = gks_aade_typos_xarakt_eksodon.id_aade_typos_xarakt_eksodon
    where acc_inv_product_id in (".implode(',',$id_acc_inv_product_array).")
    order by id_acc_inv_product_expenses";
    $result_expenses = $db_link->query($sql_expenses);        
    if (!$result_expenses) {debug_mail(false,'error sql',$sql_expenses); die('sql error');}
    while ($row_expenses = $result_expenses->fetch_assoc()) {
      $products_expenses[$row_expenses['id']][]=$row_expenses;
    }
  }
  
  if ($GKS_PRODUCT_LOTS_SERIALS) {
    $sql_lots_serials="SELECT 
    gks_acc_inv_products_lots.lot_product_id,
    acc_inv_product_id as id, 
    lot_product_quantity,
    gks_eshop_product_lots.lot_name, 
    gks_eshop_product_lots.lot_descr, 
    gks_eshop_product_lots.lot_date_production, 
    gks_eshop_product_lots.lot_date_expire, 
    gks_eshop_product_lots.lot_disabled
    FROM gks_acc_inv_products_lots
    LEFT JOIN gks_eshop_product_lots ON gks_acc_inv_products_lots.lot_product_id = gks_eshop_product_lots.id_lot_product
    WHERE gks_acc_inv_products_lots.acc_inv_product_id In (".implode(',',$id_acc_inv_product_array).")
    ORDER BY gks_acc_inv_products_lots.id_acc_inv_product_lots";
    $result_lots_serials = $db_link->query($sql_lots_serials);        
    if (!$result_lots_serials) {debug_mail(false,'error sql',$sql_lots_serials); die('sql error');}
    while ($row_lots_serials = $result_lots_serials->fetch_assoc()) {
      $products_lots_serials[$row_lots_serials['id']][]=$row_lots_serials;
    }
    
    //echo '<pre>';print_r($products_lots_serials);die();
    
  }
  
}

$tropos_pliromis_one_multi=intval($row['tropos_pliromis_one_multi']);
if ($id==-1) $tropos_pliromis_one_multi=0; 
$payment_type_multi=array();

$transaction_id_ids=[];
if ($id>0 or $template_id>0) {
  $sql_payments="SELECT id_acc_inv_payment,
  payment_acquirer_id, poso,asset_id,
  transaction_id,transaction_pa_with_id,
  gks_payment_acquirers.payment_acquirer_name, 
  aade_tropos_pliromis_id,
  gks_payment_acquirers.payment_acquirer_with_id,
  gks_assets.asset_code, gks_assets.asset_title
  FROM (gks_acc_inv_payment 
  LEFT JOIN gks_assets ON gks_acc_inv_payment.asset_id = gks_assets.id_asset) 
  LEFT JOIN gks_payment_acquirers ON gks_acc_inv_payment.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer
  WHERE gks_acc_inv_payment.acc_inv_id=";
  if ($id>0) $sql_payments.=$id;
  else if ($id==-1 and $template_id>0) $sql_payments.=$template_id." and gks_acc_inv_payment.poso>0";
  //else $sql_payments.="-1";
  $sql_payments.=" ORDER BY gks_acc_inv_payment.pp,id_acc_inv_payment";
  
  $result_payments = $db_link->query($sql_payments);        
  if (!$result_payments) {debug_mail(false,'error sql',$sql_payments);die('sql error2');}
  
  while ($row_payments = $result_payments->fetch_assoc()) {
    $row_payments['payment_acquirer_id']=intval($row_payments['payment_acquirer_id']);
    $row_payments['poso']=floatval($row_payments['poso']);
    $row_payments['asset_id']=intval($row_payments['asset_id']);
    $row_payments['aade_tropos_pliromis_id']=intval($row_payments['aade_tropos_pliromis_id']);
    $row_payments['payment_acquirer_with_id']=intval($row_payments['payment_acquirer_with_id']);
    if ($id>0) {
      $row_payments['transaction_id']=intval($row_payments['transaction_id']);
      $row_payments['transaction_pa_with_id']=intval($row_payments['transaction_pa_with_id']);
      
      if ($row_payments['transaction_id']>0) $transaction_id_ids[]=$row_payments['transaction_id'];
      
    } else {
      $row_payments['id_acc_inv_payment']=0;
      $row_payments['transaction_id']=0;
      $row_payments['transaction_pa_with_id']=0;
      
    } 
    
    $payment_type_multi[]=$row_payments;
    
  }
  if (count($payment_type_multi)>=2) $tropos_pliromis_one_multi=1;
}

$transaction_id_ids_array=[];
if (count($transaction_id_ids)>0) {
  $sql_thisisfor="SELECT id_eftpos_transaction_thisisfor, my_this, my_is, my_for, transaction_status
  FROM gks_eftpos_transaction_thisisfor 
  LEFT JOIN gks_eftpos_transaction ON gks_eftpos_transaction_thisisfor.my_this = gks_eftpos_transaction.id_eftpos_transaction
  WHERE gks_eftpos_transaction.transaction_status='done'
  and (my_this in (".implode(',',$transaction_id_ids).")
      OR my_for in (".implode(',',$transaction_id_ids)."))";
  $result_thisisfor = $db_link->query($sql_thisisfor);        
  if (!$result_thisisfor) {debug_mail(false,'error sql',$sql_thisisfor);die('sql error2');}
  while ($row_thisisfor = $result_thisisfor->fetch_assoc()) {
    $row_thisisfor['my_this']=intval($row_thisisfor['my_this']);
    $row_thisisfor['my_is']=trim($row_thisisfor['my_is']);
    $row_thisisfor['my_for']=intval($row_thisisfor['my_for']);
    
    $transaction_id_ids_array[]=$row_thisisfor;
  }
  //echo '<pre>';print_r($transaction_id_ids_array);die();
}

//echo '<pre>';print_r($payment_type_multi);die();

$payments_lock_level=0; //anoixto
$lock_selector_one_multi=false;
if (!empty($row['aade_send_date'])) {
  $lock_selector_one_multi=true;
  $payments_lock_level=5; //pige aade kai ola ta transaction einai olokliromena
  foreach ($payment_type_multi as $pmvalue) {
    if ($pmvalue['payment_acquirer_with_id']>0 and $pmvalue['transaction_id']==0) {
      $payments_lock_level=4; //pige aade alla iparxei ekremeis POS 
      break;
    }
  }  
} else {
  $pos_total=0;
  $pos_eginan=0;
  foreach ($payment_type_multi as $pmvalue) {
    if ($pmvalue['payment_acquirer_with_id']>0) {
      $pos_total++;    
      if ($pmvalue['transaction_id']>0) {
        $pos_eginan++;
      }
    }
  }
  if ($pos_eginan>0 and count($payment_type_multi)>=1)  $lock_selector_one_multi=true;
  
  if ($pos_total>0 && $pos_total==$pos_eginan) {
    $payments_lock_level=3; //den pige aade alla ola ta pos eginan  
  } else if ($pos_total>0 && $pos_total<>$pos_eginan) {
    $payments_lock_level=2; //den pige aade alla kapoia pos tha prepei na ginoun
  } else {
    $payments_lock_level=1;
  }
  
  
}
//var_dump($payments_lock_level); die();

stat_record();

include_once('_my_header_admin.php');
//print '<pre>';
//print_r($row);
//print '</pre>';

?>

<link href="css/admin-acc-inv-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link href="css/admin-eftpos-transaction-dialog.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3>
          <?php echo gks_lang('Παραστατικό');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span>
          <?php if (!empty($row['acc_inv_ref_number'])) {?>
          <?php echo gks_lang('Ref');?>: <span class="acc_inv_ref_number_head"><?php echo $row['acc_inv_ref_number'];?></span>
          <?php } ?>
        </h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Παραστατικό');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<?php if ($cancel_for_acc_inv_id!=0) {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ακυρωτικό Παραστατικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('cancel1');?>>
  
          <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Ακυρωτικό Παραστατικό');?></h4>
            <p><?php echo gks_lang('Αφορά το ακυρωμένο παραστατικό');?>: <?php echo $canceled_descr_for;?></p>
          </div>

  
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if ($canceled_descr_by!='') {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ακυρωμένο Παραστατικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('cancel2');?>>
  
          <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Ακυρωμένο Παραστατικό');?></h4>
            <p><?php echo gks_lang('Αυτό το παραστατικό έχει ακυρωθεί');?>.<br>
              <?php echo gks_lang('Το ακυρωτικό παραστατικό είναι το');?>: <?php echo $canceled_descr_by;?>
            </p>
          </div>

  
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<?php if ($credit_memo_for_acc_inv_id!=0) {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συσχετιζόμενο Πιστωτικό Παραστατικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('credit1');?>>
  
          <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Συσχετιζόμενο Πιστωτικό Παραστατικό');?></h4>
            <p><?php echo gks_lang('Αφορά το συσχετιζόμενο παραστατικό');?>: <?php echo $credit_memo_descr_for;?></p>
          </div>

  
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if ($credit_memo_descr_by!='') {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συσχετιζόμενο Πιστωτικό Παραστατικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('credit2');?>>
  
          <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Συσχετιζόμενο Πιστωτικό Παραστατικό');?></h4>
            <p><?php echo gks_lang('Αυτό το παραστατικό έχει συσχετιζόμενο πιστωτικό παραστατικό');?><br>
              <?php echo $credit_memo_descr_by;?>
            </p>
          </div>

  
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<?php if ($dimotikos_foros_for_acc_inv_id!=0) {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συσχετιζόμενο Παραστατικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('credit1');?>>
  
          <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Συσχετιζόμενο Παραστατικό Απόδειξης Είσπραξης Φόρου Διαμονής');?></h4>
            <p><?php echo gks_lang('Αφορά το συσχετιζόμενο παραστατικό');?>: <?php echo $dimotikos_foros_descr_for;?></p>
          </div>

  
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if ($dimotikos_foros_descr_by!='') {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συσχετιζόμενο Παραστατικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('credit2');?>>
  
          <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Συσχετιζόμενο Παραστατικό Απόδειξης Είσπραξης Φόρου Διαμονής');?></h4>
            <p><?php echo gks_lang('Αυτό το παραστατικό έχει συσχετιζόμενο παραστατικό');?><br>
              <?php echo $dimotikos_foros_descr_by;?>
            </p>
          </div>

  
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<div id="mypostform">
<div class="container-fluid">
  <div class="row">
    
    <div class="col-md-4">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
       
        <div class="card-body" <?php echo gks_card_body('bas');?>>
          
          <div class="form-group row">
            <label for="company_id_sub_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="company_id_sub_id" type="hidden" value="'.$row['company_id'].'|'.$row['company_sub_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['company_title'];
                  if (isset($row['company_sub_title'])) echo ' \ '.$row['company_sub_title'];
                echo '</div>';
              } else {?>
              <select id="company_id_sub_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0|0"></option>
                <?php
                $company_id_sub_id=$row['company_id'].'|'.$row['company_sub_id'];
                foreach ($user_companys as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ' .
                  ' data-afm="'.$row_select['company_afm'].'" ';
                  if ($row_select['id']==$company_id_sub_id) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>
              <?php } ?>
            </div>
          </div> 
          <div class="form-group row">
            <label for="inv_acc_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="inv_acc_journal_id" type="hidden" value="'.$row['inv_acc_journal_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['acc_journal_descr'];
                echo '</div>';
              } else {?>
              <select id="inv_acc_journal_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_journal, acc_journal_descr, acc_eidos_parastatikou_id, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
                whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
                whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
                acc_eidos_parastatikou_other_entity,
                journal_has_correlated_invoices,
                journal_has_multiple_connected_marks,
                journal_has_packings_declarations
                FROM (gks_acc_journal 
                LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                WHERE gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (1,2,5) and gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou not in (702,703,704)
                and is_disable=0 and company_id=".$row['company_id']." AND company_sub_id=".$row['company_sub_id'];
                if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_journal.id_acc_journal in (".implode(',',$perm_id_acc_journal_ids).")";
                $sql.=" ORDER BY gks_acc_journal.sortorder,gks_acc_journal.acc_journal_descr;";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_journal'].'" '.
                  'data-eidi_id="'.$row_select['acc_eidos_parastatikou_id'].'" '.
                  'data-type_id="'.$row_select['eidos_parastatikou_type_id'].'" '.
                  'data-need_prev="'.$row_select['eidos_parastatikou_need_prev'].'" '.
                  'data-fpa="'.$row_select['eidos_parastatikou_has_fpa'].'" '.
                  'data-othertaxes="'.$row_select['eidos_parastatikou_has_othertaxes'].'" '.
                  'data-esoda="'.$row_select['eidos_parastatikou_has_esoda'].'" '.
                  'data-eksoda="'.$row_select['eidos_parastatikou_has_eksoda'].'" '.
                  'data-need_afm="'.$row_select['eidos_parastatikou_need_afm'].'" '.
                  'data-balance_pros="'.$row_select['eidos_parastatikou_balance_pros'].'" '.
                  'data-whi_stock_pros="'.intval($row_select['whi_eidos_parastatikou_stock_pros']).'" '. // intval kanei to null se 0
                  'data-whi_type_id="'.intval($row_select['whi_eidos_parastatikou_type_id']).'" '.       // intval kanei to null se 0
                  'data-other_entity="'.intval($row_select['acc_eidos_parastatikou_other_entity']).'" '. 
                  'data-correlated_invoices="'.intval($row_select['journal_has_correlated_invoices']).'" '. 
                  'data-multiple_connected_marks="'.intval($row_select['journal_has_multiple_connected_marks']).'" '. 
                  'data-packings_declarations="'.intval($row_select['journal_has_packings_declarations']).'" '; 
                  if ($row['inv_acc_journal_id'] == $row_select['id_acc_journal']) echo ' selected ';
                  echo '>'.$row_select['acc_journal_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="inv_acc_seira_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="inv_acc_seira_id" type="hidden" value="'.$row['inv_acc_seira_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['seira_code'].' - '.$row['seira_descr'];
                echo '</div>';
              } else {?>
              <select id="inv_acc_seira_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_seira, seira_code,seira_descr,
                is_xeirografi,
                seira_isdeliverynote,
                seira_is_self_pricing,
                seira_is_vat_payment_suspension
                FROM gks_acc_seires 
                WHERE is_disable=0 and acc_journal_id=".$row['inv_acc_journal_id'];
                if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_seires.id_acc_seira in (".implode(',',$perm_id_acc_seira_ids).")";
                $sql.=" ORDER BY sortorder,seira_code;";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_seira'].'" '.
                  'data-is_xeirografi="'.$row_select['is_xeirografi'].'" '.
                  'data-is_deliverynote="'.$row_select['seira_isdeliverynote'].'" '.
                  'data-is_self_pricing="'.$row_select['seira_is_self_pricing'].'" '.
                  'data-is_vat_payment_suspension="'.$row_select['seira_is_vat_payment_suspension'].'"';
                  if ($row['inv_acc_seira_id'] == $row_select['id_acc_seira']) echo ' selected ';
                  echo '>'.$row_select['seira_code'].' - '.$row_select['seira_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div> 

          <div class="form-group row" id="self_pricing_div" style="<?php if ($row['seira_is_self_pricing']==0) echo 'display:none;';?>">
            <label for="self_pricing" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αυτοτιμολόγηση');?>:</label>
            <div class="col-md-8"><?php 
                echo '<div id="self_pricing_span" class="gks_flock form-control-sm">';
                  echo gks_lang('Ναι');
                echo '</div>';?>
            </div>
          </div>
          <div class="form-group row" id="vat_payment_suspension_div" style="<?php if ($seira_is_vat_payment_suspension==0) echo 'display:none;';?>">
            <label for="vat_payment_suspension" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αναστολή Καταβολής ΦΠΑ');?>:</label>
            <div class="col-md-8"><?php
                echo '<div id="vat_payment_suspension_span" class="gks_flock form-control-sm">';
                  echo gks_lang('Ναι');
                echo '</div>';?>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="inv_acc_number_int" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['inv_acc_number_int'];
                echo '</div>';
              } else {?>
              <input id="inv_acc_number_int" class="form-control form-control-sm myneedsave" type="number" 
              value="<?php if ($row['inv_acc_number_int']>0) echo $row['inv_acc_number_int'];?>" style="max-width:100px;" 
              placeholder="" min="0" step="1"
              <?php if ($gks_number_lock or $row['is_xeirografi']==0) echo 'disabled';?>>
              <?php } ?>
            </div>
          </div> 

          <div class="form-group row">
            <label for="aade_skopos_diakinisis_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σκοπός Διακίνησης');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo gks_lang_data_trans($row['aade_skopos_diakinisis_descr'],$row['aade_skopos_diakinisis_id'],'gks_aade_skopos_diakinisis','aade_skopos_diakinisis_descr');
                  if ($row['aade_skopos_diakinisis_id']==22) echo '<br>'.htmlspecialchars_gks($row['aade_skopos_19_descr']);
                echo '</div>';
              } else {?>
              <select id="aade_skopos_diakinisis_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_aade_skopos_diakinisis=gks_lang_data_obj_prepare('gks_aade_skopos_diakinisis','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_aade_skopos_diakinisis, array('aade_skopos_diakinisis_descr'));
                $sql="select id_aade_skopos_diakinisis,".gks_lang_sql_field('aade_skopos_diakinisis_descr',$lang_prepare_gks_aade_skopos_diakinisis)." 
                FROM ".$lang_prepare_gks_aade_skopos_diakinisis['sql']['from1']." gks_aade_skopos_diakinisis 
                ".$lang_prepare_gks_aade_skopos_diakinisis['sql']['from2']."
                ORDER BY sortorder";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_aade_skopos_diakinisis'].'" ';
                  if ($row_select['id_aade_skopos_diakinisis']==$row['aade_skopos_diakinisis_id']) echo ' selected ';
                  echo '>'.$row_select['aade_skopos_diakinisis_descr'].'</option>';
                }?>
              </select>
              
              <input id="aade_skopos_19_descr" type="text" 
              class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['aade_skopos_19_descr']);?>" 
              autocomplete="<?php echo $autocomplete_gks_disable;?>" 
              <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>
              style="<?php echo ($row['aade_skopos_diakinisis_id']==22 ? '' : 'display:none');?>"
              placeholder="<?php echo gks_lang('Τίτλος της Λοιπής Αιτίας Διακίνησης');?>">
              <?php } ?>
            </div>
          </div> 
                              
          <div class="form-group row">
            <label for="inv_date" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-sm-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if (isset($row['inv_date'])) echo  showDate(strtotime($row['inv_date']), 'd/m/Y H:i', 1);
                echo '</div>';
              } else {?>
              <input id="inv_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['inv_date'])) echo  showDate(strtotime($row['inv_date']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>>
              <?php } ?>
            </div>
          </div> 


          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-sm-8">
              <span class="acc_inv_state_<?php echo $row['inv_state'];?>"><?php echo getAccInvStateDescr($row['inv_state']);?></span>
            </div>
          </div> 

              

 
         
     
          <div class="form-group row">
            <label for="fiscal_position_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φορολογική Θέση');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo  $row['fiscal_position_descr'];
                echo '</div>';
              } else {?>
              <select id="fiscal_position_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_fiscal_position=gks_lang_data_obj_prepare('gks_eshop_fiscal_position','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_fiscal_position, array('fiscal_position_descr'));
                $sql="select id_fiscal_position,".gks_lang_sql_field('fiscal_position_descr',$lang_prepare_gks_eshop_fiscal_position)." 
                FROM ".$lang_prepare_gks_eshop_fiscal_position['sql']['from1']." gks_eshop_fiscal_position 
                ".$lang_prepare_gks_eshop_fiscal_position['sql']['from2']."
                where fiscal_position_disable=0 
                order by fiscal_position_sortorder,fiscal_position_descr";
                //echo $sql;die();
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_fiscal_position'].'" ';
                  if ($row_select['id_fiscal_position']==$row['fiscal_position_id']) echo ' selected ';
                  echo '>'.$row_select['fiscal_position_descr'].'</option>';
                }?>
              </select>    
              <?php } ?>
            </div>
          </div> 
          <div class="form-group row">
            <label for="pricelist_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμοκατάλογος');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo  $row['pricelist_descr'];
                echo '</div>';
              } else {?>
              <select id="pricelist_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_pricelist=gks_lang_data_obj_prepare('gks_eshop_pricelist','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_pricelist, array('pricelist_descr'));
                $sql="select id_pricelist,".gks_lang_sql_field('pricelist_descr',$lang_prepare_gks_eshop_pricelist)." 
                FROM ".$lang_prepare_gks_eshop_pricelist['sql']['from1']." gks_eshop_pricelist 
                ".$lang_prepare_gks_eshop_pricelist['sql']['from2']."
                where pricelist_disable=0 
                order by sortorder,pricelist_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_pricelist'].'" ';
                  if ($row_select['id_pricelist']==$row['pricelist_id']) echo ' selected ';
                  echo '>'.$row_select['pricelist_descr'].'</option>';
                }?>
              </select>    
              <?php } ?>
            </div>
          </div>  
          <div class="form-group row">
            <label for="def_ekptosi" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προεπιλεγμένη έκπτωση');?>:</label>
            <div class="col-sm-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo myNumberFormatNo0Local($row['def_ekptosi']).'%';
                echo '</div>';
              } else {?>
              <input id="def_ekptosi" type="number" class="form-control form-control-sm myneedsave" value="<?php echo number_format($row['def_ekptosi'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="max-width:100px;display: inline-block;" placeholder="" min="0" step="<?php echo $GKS_INPUT_STEP_POSOSTO;?>"> %
              <button class="btn btn-sm btn-primary" id="def_ekptosi_set"><?php echo gks_lang('Εφαρμογή');?></button>
              <?php } ?>
            </div>
          </div>               
          <div class="form-group row">
            <label for="assigned_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ανάθεση σε');?>:</label>
            <div class="col-sm-8">
              <input id="assigned_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname_assigned']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['assigned_id'];?>">
            </div>
          </div>          
          <div class="form-group row">
            <label for="merchant_ref_trns" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αναφορά');?>:</label>
            <div class="col-sm-8">
              <input id="merchant_ref_trns" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['merchant_ref_trns']);?>" placeholder="<?php echo gks_lang('Ένα μικρό σχόλιο');?>">
            </div>
          </div>


                                  

        </div>
      </div>

      <?php 
      
      
      $from_aade_import_json=trim_gks($row['from_aade_import_json']);
       
      if ($from_aade_import_json!='') $from_aade_import_json=json_decode($from_aade_import_json, true);
      
      if (is_array($from_aade_import_json)) $GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA=false;
      
      if (trim_gks($row['from_aade_import'])!='') {?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Από εισαγωγή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('import');?>>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πηγή');?>:</label>
            <div class="col-sm-8">
              <div class="gks_flock form-control-sm">
                <?php 
                if ($row['from_aade_import']=='diko_mou') echo gks_lang('Από εμένα');
                else if ($row['from_aade_import']=='apo_allon') echo gks_lang('Από άλλον');
                else echo '--';
                ?>
              </div>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Παραστατικό');?>:</label>
            <div class="col-sm-8">
              <div class="gks_flock form-control-sm">
                <?php 
                echo $row['import_eidos_parastatikou_aade_code'];
                if (trim_gks($row['import_eidos_parastatikou_aade_code'])!='') {
                  $sql_import_parast="select eidos_parastatikou_descr from gks_acc_eidi_parastatikon where eidos_parastatikou_aade_code like '".$db_link->escape_string(trim_gks($row['import_eidos_parastatikou_aade_code']))."'";
                  $result_import_parast = $db_link->query($sql_import_parast);  
                  if (!$result_import_parast) {
                    debug_mail(false,'error sql',$sql_import_parast);
                    $return = array('success' => false, 'message' => base64_encode('sql error'));
                    echo json_encode($return); die(); }
                  if ($result_import_parast->num_rows>=1) {
                    $row_import_parast = $result_import_parast->fetch_assoc();
                    echo ' - '.$row_import_parast['eidos_parastatikou_descr'];
                  }
                }
                
                ?>
              </div>
            </div>
          </div>           
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά');?>:</label>
            <div class="col-sm-8">
              <div class="gks_flock form-control-sm">
                <?php echo $row['import_inv_acc_seira_code'];?>
              </div>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-sm-8">
              <div class="gks_flock form-control-sm">
                <?php echo $row['import_inv_acc_number_str'];?>
              </div>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αξίες');?>:</label>
            <div class="col-sm-8">
              <div class="gks_flock form-control-sm">
                <?php
                if (is_array($from_aade_import_json)) {
                  if (isset($from_aade_import_json['gks_price_net']) and floatval($from_aade_import_json['gks_price_net'])!=0) {
                    echo '<div class="row"><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.gks_lang('Υποσύνολο').':</div><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.myCurrencyFormat($from_aade_import_json['gks_price_net']).'<img src="img/warning.gif" class="from_aade_import_json_warning_img1" data-id="gks_price_net"/></div></div>'; 
                  }
                  if (isset($from_aade_import_json['gks_price_fpa']) and floatval($from_aade_import_json['gks_price_fpa'])!=0) {
                    echo '<div class="row"><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.gks_lang('ΦΠΑ').':</div><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.myCurrencyFormat($from_aade_import_json['gks_price_fpa']).'<img src="img/warning.gif" class="from_aade_import_json_warning_img1" data-id="gks_price_fpa"/></div></div>'; 
                  }
                  if (isset($from_aade_import_json['gks_price_netfpa']) and floatval($from_aade_import_json['gks_price_netfpa'])!=0) {
                    echo '<div class="row"><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.gks_lang('Μικτό σύνολο').':</div><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.myCurrencyFormat($from_aade_import_json['gks_price_netfpa']).'<img src="img/warning.gif" class="from_aade_import_json_warning_img1" data-id="gks_price_netfpa"/></div></div>'; 
                  }
                  if (isset($from_aade_import_json['totalWithheldAmount']) and floatval($from_aade_import_json['totalWithheldAmount'])!=0) {
                    echo '<div class="row"><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.gks_lang('Φόροι Παρακρατούμενοι').':</div><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.myCurrencyFormat($from_aade_import_json['totalWithheldAmount']).'<img src="img/warning.gif" class="from_aade_import_json_warning_img1" data-id="totalWithheldAmount"/></div></div>'; 
                  }
                  if (isset($from_aade_import_json['totalOtherTaxesAmount']) and floatval($from_aade_import_json['totalOtherTaxesAmount'])!=0) {
                    echo '<div class="row"><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.gks_lang('Λοιποί Φόροι').':</div><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.myCurrencyFormat($from_aade_import_json['totalOtherTaxesAmount']).'<img src="img/warning.gif" class="from_aade_import_json_warning_img1" data-id="totalOtherTaxesAmount"/></div></div>'; 
                  }
                  if (isset($from_aade_import_json['totalStampDutyamount']) and floatval($from_aade_import_json['totalStampDutyamount'])!=0) {
                    echo '<div class="row"><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.gks_lang('Ψηφιακό Τέλος συναλλαγής').':</div><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.myCurrencyFormat($from_aade_import_json['totalStampDutyamount']).'<img src="img/warning.gif" class="from_aade_import_json_warning_img1" data-id="totalStampDutyamount"/></div></div>'; 
                  }
                  if (isset($from_aade_import_json['totalFeesAmount']) and floatval($from_aade_import_json['totalFeesAmount'])!=0) {
                    echo '<div class="row"><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.gks_lang('Τέλη').':</div><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.myCurrencyFormat($from_aade_import_json['totalFeesAmount']).'<img src="img/warning.gif" class="from_aade_import_json_warning_img1" data-id="totalFeesAmount"/></div></div>'; 
                  }
                  if (isset($from_aade_import_json['totalDeductionsAmount']) and floatval($from_aade_import_json['totalDeductionsAmount'])!=0) {
                    echo '<div class="row"><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.gks_lang('Κρατήσεις','part2').':</div><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.myCurrencyFormat($from_aade_import_json['totalDeductionsAmount']).'<img src="img/warning.gif" class="from_aade_import_json_warning_img1" data-id="totalDeductionsAmount"/></div></div>'; 
                  }
                  if (isset($from_aade_import_json['gks_price_total']) and floatval($from_aade_import_json['gks_price_total'])!=0) {
                    echo '<div class="row"><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.gks_lang('Σύνολο').':</div><div class="col-sm-6 text-right" style="font-size: 0.8rem;">'.myCurrencyFormat($from_aade_import_json['gks_price_total']).'<img src="img/warning.gif" class="from_aade_import_json_warning_img1" data-id="gks_price_total"/></div></div>'; 
                  }
                  //echo '<pre>'.print_r($from_aade_import_json, true).'</pre>';
                } ?>
              </div>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τρόπος Πληρωμής');?>:</label>
            <div class="col-sm-8">
              <div class="gks_flock form-control-sm">
                <?php if (isset($from_aade_import_json['paymentMethods_paymentMethodInfo'])) echo $from_aade_import_json['paymentMethods_type'].' - '.$from_aade_import_json['paymentMethods_paymentMethodInfo'];?>
                <?php if (isset($from_aade_import_json['paymentMethods'])) {
                  $temp_out=[];
                  foreach ($from_aade_import_json['paymentMethods']as $pm) {
                    $temp_out[]=$pm['type'].' - '.$pm['paymentMethodInfo'].' <i class="fas fa-caret-right"></i> '.myCurrencyFormat($pm['amount']);
                  }
                  echo implode('<br>',$temp_out);
                }?>
              </div>
            </div>
          </div> 
          
          
        </div>
      </div>
      <?php } ?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κουπόνια');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kou');?>>
          <?php if ($gks_lock==false) {?>
          <div style="text-align: center;">
            <input type="text" class="form-control form-control-sm myneedsave" value="" style="max-width:180px;text-align:left;display: inline;vertical-align: middle;" id="input_coupon" />
            <span style="" id="coupon_use" class="btn btn-sm btn-primary"><?php echo gks_lang('Προσθήκη Κουπονιού');?></span>
          </div>
          <?php } ?>
          <div id="coupons_html" class="form-control-sm">   
<?php
            $coupons_html='';
            foreach ($mybasketarray['coupons'] as $key => $coupon) {
               $coupons_html.='<span class="tooltipster coupons_span" title="'.$coupon.'">
               <span class="coupons text-sm">'.$key.
               ($gks_lock ? '' : ' <i class="coupon_delete fas fa-trash-alt gks_acc_inv_delete_icon" data-coupon="'.$key.'" style=""></i>').
               '</span></span> ';
            } 
            if ($coupons_html!='') {
              $coupons_html=gks_lang('Κουπόνια').': '.$coupons_html;
              echo $coupons_html;
            }
?>            
          </div>         
        </div>
      </div>
    </div>
    
    
    <div class="col-md-8">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center" id="antisimvalomenos_label">
          <?php echo $antisimvalomenos_label;?>
        </div>
        <div class="card-body" <?php echo gks_card_body('user');?>>

          <div class="form-group row" id="div_form_idio_afm" style="<?php echo ($row['eidos_parastatikou_need_afm']==-1 ? '' : 'display:none;') ?>">  
            <div class="col-md-12 form-control-sm text-sm-center">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if ($row['company_afm'] == $row['afm']) echo gks_lang('Εμάς'); else echo gks_lang('Άλλος');
                echo '</div>';                
              } else {?>
              <span style="white-space: nowrap;"><input type="radio" name="form_idio_afm" value="0" id="form_idio_afm_nai" <?php echo ($row['company_afm'] == $row['afm'] ? ' checked ' : '');?>> <label class="gks_label" for="form_idio_afm_nai" style="display:inline;padding-right:18px" ><?php echo gks_lang('Εμάς');?></label></span> 
              <span style="white-space: nowrap;"><input type="radio" name="form_idio_afm" value="1" id="form_idio_afm_oxi" <?php echo ($row['company_afm'] == $row['afm'] ? '  ' : 'checked');?>> <label class="gks_label" for="form_idio_afm_oxi" style="display:inline"><?php echo gks_lang('Άλλος');?></label></span>
              <?php } ?>
            </div>
          </div> 
          <div id="div_show_user" style="<?php echo (($row['eidos_parastatikou_need_afm']==-1 and $row['afm']==$row['company_afm']) ? 'display:none;' : '') ?>">
          
            <div class="form-group row">
              <label for="user" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
              <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if ($row['user_id']>0)
                    echo '<a href="admin-users-item.php?id='.$row['user_id'].'" class="email_contact_name">'.$row['gks_nickname'].'</a>';
                  else
                     echo '<span class="email_contact_name">'.$row['gks_nickname'].'</span>';
                  echo '<input type="hidden" id="user_id" value="'.$row['user_id'].'">';
                echo '</div>';                
              } else {?>
                    <input id="user" type="text" class="form-control form-control-sm myneedsave email_contact_name"  <?php if ($gks_user_lock) echo 'disabled';?>
                    value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
                    style="width:calc(98% - 22px);display:inline;" 
                    placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>>
                    <input id="user_id" type="hidden" value="<?php echo $row['user_id'];?>" class="myneedsave">
                    <?php if ($perm_gks_acc_inv_edit) {?>
                    <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['user_id'];?>" style="<?php if ($row['user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
                    <i id="user_save" class="fas fa-save" style="<?php if ($row['user_id']>0) echo 'display:none';?>;color: #35dc35;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Δημιουργία επαφής');?>"></i>
                    <?php } ?>
              <?php } ?>
                <div id="user_is_b2g" class="alert alert-warning" role="alert" style="<?php if ($row['is_b2g']==0) echo 'display:none';?>;margin: 4px 27px 4px 0px;padding: 6px 6px 6px 10px;">
                  <i class="fas fa-university"></i>  B2G
                </div>
              </div>
              <?php if ($gks_lock==false) {?>
              <label for="" class="col-sm-2 col-form-label form-control-sm text-sm-right"><a class="tooltipster" title="<?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?>" href="https://www.aade.gr/epiheiriseis/forologikes-ypiresies/mitroo/anazitisi-basikon-stoiheion-mitrooy-epiheiriseon" target="_blank">aade.gr</a>:</label>
              <div class="col-sm-4">
                <button style="" id="btn_gsis_get" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση με το ΑΦΜ');?></button>
              </div>
              <?php } ?>
            </div>
  
  
            <div class="form-group row" style="margin-bottom: 0px;">
              <div class="col-sm-6">
                <div class="form-group1 row" id="div_pelati_sxolio" style="<?php echo (trim_gks($row['pelati_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                  <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_pelati_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['pelati_sxolio']);?></div>
                  <div style="text-align:right;width:100%;margin-bottom: 10px;">
                    <i id="copy_text_pelati_sxolio_to_logistirio" class="far fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση για λογιστήριο</b>');?>"></i>
                  </div>
                </div>
                              
              </div>
              
              <div class="col-sm-6">
                <div class="form-group1 row" id="div_order_sxolio" style="<?php echo (trim_gks($row['order_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                  <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['order_sxolio']);?></div>
                  <div style="text-align:right;width:100%;margin-bottom: 10px;">
                    <i id="copy_text_order_sxolio_to_logistirio" class="far fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση για λογιστήριο</b>');?>"></i>
                  </div>
                </div>               
              </div>
            </div>
  
  
  
  
  
  
  
  
  
            <div class="form-group row">
              <label for="dr_user_first_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['user_first_name'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_first_name" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['user_first_name']);?>">
                <?php } ?>
              </div>
              <label for="dr_user_last_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επώνυμο');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['user_last_name'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_last_name" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['user_last_name']);?>">
                <?php } ?>
              </div>
            </div>
            <div class="form-group row">
              <label for="dr_user_email" class="col-sm-2 col-form-label form-control-sm text-sm-right">email:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    if (isset($row['user_email'])) echo '<a href="mailto:'.$row['user_email'].'">'.$row['user_email'].'</a>';
                  echo '</div>';
                  echo '<input id="dr_user_email" type="hidden" value="'.htmlspecialchars_gks($row['user_email']).'">';
                } else {?>
                <input id="dr_user_email" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['user_email']);?>">
                <?php } ?>
              </div>
              <label for="dr_user_mobile" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm" id="dr_user_mobile">';
                    if (isset($row['user_mobile'])) echo '<a href="tel:'.$row['user_mobile'].'" class="'.$gks_voip_params['class_span'].'">'.$row['user_mobile'].'</a>';
                    echo $gks_voip_params['html_after_span'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_mobile" type="text" class="form-control form-control-sm myneedsave <?php echo $gks_voip_params['class_input'];?>" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['user_mobile']);?>">
                <?php echo $gks_voip_params['html_after_input'];?>
                <?php } ?>
              </div>
            </div>
                
            <div class="form-group row">
              <label for="dr_user_lang" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γλώσσα');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['lang_name'];
                  echo '</div>';  
                  echo '<input type="hidden" id="dr_user_lang" value="'.$row['user_lang'].'">';              
                } else {?>
                <select id="dr_user_lang" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                  <option value=""></option>
                  <?php
                  $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
                  gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
                  $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
                  FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
                  ".$lang_prepare_gks_lang['sql']['from2']."
                  ORDER BY lang_sortorder,lang_name";                  
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<option value="'.$row_select['id_lang'].'" ';
                    if ($row['user_lang'] == $row_select['id_lang']) echo ' selected ';
                    echo '>'.$row_select['lang_name'].'</option>';
                  }
                  ?>
                </select>                  
                <?php } ?>
              </div>
            </div>
            
  
    
   
            <div id="div_parastatiko_timologio" <?php echo ($eidos_parastatikou_need_afm == 0 ? ' style="display: none;" ' : '');?>>
              <div class="form-group row">
                <label for="dr_user_eponimia" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επωνυμία');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['eponimia'];
                    echo '</div>';                
                  } else {?>
                  <input id="dr_user_eponimia" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['eponimia']);?>" >
                  <?php } ?>
                </div>
                <label for="dr_user_title" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τίτλος');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['title'];
                    echo '</div>';                
                  } else {?>
                  <input id="dr_user_title" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['title']);?>" >
                  <?php } ?>
                </div>
              </div>
              <?php
              $ee_initials='';
              $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
              gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
              $sql="select id_country,country_ee,country_initials,".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
              FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
              ".$lang_prepare_gks_country['sql']['from2']."
              where id_country=".$row['ma_country_id'];
              //echo '<pre>'.$sql;die();
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              }
              if ($result_select->num_rows==1) {
                $row_select = $result_select->fetch_assoc();
                $ee_initials=trim_gks($row_select['country_ee']);
              }
     
              ?>
              
              
              <div class="form-group row">
                <label for="dr_user_afm" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      if ($ee_initials!='') echo $ee_initials.' ';
                      echo $row['afm'];
                    	echo '<input type="hidden" id="dr_user_afm" value="'.$row['afm'].'">';
                      echo '<span 
                      id="dr_user_afm_views_run" style="'.($check_vies['run'] ? 'display:inline-block;' : 'display:none;').'padding-left: 10px;position:relative;top:-3px;">'.$check_vies['views_run_img'].'</span>';
                    
                   
                      //if ($check_vies['run']) echo '<span style="padding-left: 10px;position:relative;top:-3px;">'.$check_vies['views_run_img'].'</span>';
                      
                    echo '</div>';                
                  } else {?>
                  <span id="dr_user_afm_ee_initials" style="<?php echo ($ee_initials!='' ? '' : 'display:none;');?>"><?php echo $ee_initials;?></span><input 
                    style="display: inline-block;max-width:100%;text-align:left;vertical-align: middle;<?php echo ($ee_initials=='' ? 'width:100%;' : 'width:calc(100% - 75px);');?>"
                    id="dr_user_afm" type="text" class="form-control form-control-sm myneedsave <?php echo ($ee_initials=='' ? '':'dr_user_afm_views');?>" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['afm']);?>" ><span 
                    id="dr_user_afm_views_run" style="<?php echo ($check_vies['run'] ? '' : 'display:none;');?>"><?php echo $check_vies['views_run_img'];?></span>
                  <?php
                  //$out.='<span id="dr_user_afm_ee_initials" style="'.($ee_initials!='' ? '' : 'display:none;').';">'.$ee_initials.'</span>';
                  //$out.='<span id="dr_user_afm_views_run" style="'.($check_vies['run'] ? '' : 'display:none;').'">'.$views_run_img.'</span>';
                  ?>
                  <?php } ?>
                </div>
                <label for="dr_user_doy" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
                <div class="col-sm-4">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['doy'];
                    echo '</div>';                
                  } else {?>
                  <input id="dr_user_doy" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['doy']);?>" >
                  <?php } ?>
                </div>
              </div>
  
  
              <div class="form-group row">
                <label for="dr_user_epaggelma" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
                <div class="col-sm-10">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['epaggelma'];
                    echo '</div>';                
                  } else {?>
                  <input id="dr_user_epaggelma" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['epaggelma']);?>" >
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row" id="div1_gemi_number" style="<?php if (trim_gks($row['gemi_number'])=='') echo 'display:none';?>">
                <label for="gemi_number" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γ.Ε.ΜΗ.');?>:</label>
                <div class="col-sm-10">
                    <div class="gks_flock form-control-sm" id="div2_gemi_number">
                      <?php echo $row['gemi_number'];?>
                    </div>                
                  </div>
              </div>
              
              
            </div>
            
                        
            <div class="row">  
              <div class="col-md-12">
                <div class="text-sm-center" style="font-weight: bold;"><?php echo gks_lang('Διεύθυνση Τιμολόγησης');?></div>
              </div>
            </div>  
            <div class="form-group row">
              <label for="dr_user_ma_odos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['ma_odos'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_ma_odos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_odos']);?>" >
                <small class="form-text text-muted auto_googlemaps" id="dr_user_ma_odos_auto_googlemaps"></small>
                <?php } ?>
              <input id="dr_user_ma_branch_fromuser" type="hidden" value="<?php echo htmlspecialchars_gks($row['ma_branch_fromuser']);?>">
              </div>
              
              <label for="dr_user_ma_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['ma_arithmos'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_ma_arithmos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_arithmos']);?>" >
                <?php } ?>
              </div>              
            </div>
            <div class="form-group row">
              <label for="dr_user_ma_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['ma_orofos'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_ma_orofos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_orofos']);?>" >
                <?php } ?>
              </div>
              <label for="dr_user_ma_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['ma_perioxi'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_ma_perioxi" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_perioxi']);?>" >
                <?php } ?>
              </div>
            </div>
  
            <div class="form-group row">
              <label for="dr_user_ma_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['ma_poli'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_ma_poli" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_poli']);?>" >
                <?php } ?>
              </div>
              <label for="dr_user_ma_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['ma_tk'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_ma_tk" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['ma_tk']);?>" >
                <?php } ?>
              </div>
            </div>
  
            <div class="form-group row">
              <label for="dr_user_ma_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
              <div class="col-sm-4">
              	
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');
                    echo '<input type="hidden" id="dr_user_ma_country_id_h" value="'.$row['ma_country_id'].'">';
                  echo '</div>';                
                } else {?>
                <select data-dbval="<?php echo $row['ma_country_id'];?>" id="dr_user_ma_country_id" <?php if ($gks_user_lock) echo 'disabled';?> class="form-control form-control-sm myneedsave">
                </select> 
                <?php } ?>
              </div>
              <label for="dr_user_ma_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo gks_lang_data_trans($row['nomos_descr'],$row['ma_nomos_id'],'gks_nomoi','nomos_descr');
                  echo '</div>';
                } else {?>
                <select id="dr_user_ma_nomos_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                  <option value="0"><?php echo gks_lang('Νομός');?>...</option>
                  <?php
                  $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                  gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                  $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                  FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                  ".$lang_prepare_gks_nomos['sql']['from2']."
                  where country_id=".$row['ma_country_id']." ORDER BY nomos_descr";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<option value="'.$row_select['id_nomos'].'" ';
                    if ($row['ma_nomos_id'] == $row_select['id_nomos']) echo ' selected ';
                    echo '>'.$row_select['nomos_descr'].'</option>';
                  }
                  ?>
                </select> 
                <?php } ?>
              </div>
            </div>   
            
            
            
  
  
  
  
  
  
            <div id="div_select_apostoli">
              <div class="row">  
                <div class="col-md-12">
                  <div class="text-sm-center" style="font-weight: bold;"><?php echo gks_lang('Αποστολή');?></div>
                </div>
              </div>   
  
                
              <?php 
              $selected_ea=array();
              $selected_ea['ea_name']='';
              $selected_ea['ea_phone']='';
              $selected_ea['ea_branch']='';
              $selected_ea['ea_odos']='';
              $selected_ea['ea_arithmos']='';
              $selected_ea['ea_orofos']='';
              $selected_ea['ea_perioxi']='';
              $selected_ea['ea_poli']='';
              $selected_ea['ea_tk']='';
              $selected_ea['ea_country_id']=0;
              $selected_ea['country_name']='';
              $selected_ea['ea_nomos_id']=0;
              $selected_ea['nomos_descr']='';
              
              $selected_ea_option='';              
              
              $sql="SELECT gks_users_extra_address.*, country_name,nomos_descr
              FROM (gks_users_extra_address 
              LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
              LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
              WHERE (gks_users_extra_address.user_id=".$row['user_id']." and gks_users_extra_address.user_id>0)
              or (gks_users_extra_address.acc_inv_id=".$id.")
              
              ORDER BY gks_users_extra_address.id_users_extra_address";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              }

              while ($row_select = $result_select->fetch_assoc()) {
                $row_select['country_name']=gks_lang_data_trans($row_select['country_name'],$row_select['ea_country_id'],'gks_country','country_name');
                $row_select['nomos_descr']=gks_lang_data_trans($row_select['nomos_descr'],$row_select['ea_nomos_id'],'gks_nomoi','nomos_descr');

                
                $address_name=$row_select['ea_name'].', '.trim_gks($row_select['ea_odos'].' '.$row_select['ea_arithmos']).', '.$row_select['ea_orofos'].', '.$row_select['ea_perioxi'].', '.$row_select['ea_poli'].', '.$row_select['ea_tk'].', '.$row_select['country_name'].', '.$row_select['nomos_descr'];
              
                $address_name=str_replace(', , ', ', ', $address_name);
                $address_name=str_replace(', , ', ', ', $address_name);
                $address_name=str_replace(', , ', ', ', $address_name);
                $address_name=str_replace(', , ', ', ', $address_name);
                
                if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
                if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
                if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
              
                if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
                if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
                if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
                
                
                $selected_ea_option.= '<option value="'.$row_select['id_users_extra_address'].'" ';
                if ($row['address_extra'] == $row_select['id_users_extra_address']) {
                  $selected_ea_option.= ' selected ';
                  $selected_ea=$row_select;
                }
                $selected_ea_option.= '>'.$address_name.'</option>';
              }
              
                
              if ($gks_lock) {
                if ($row['address_extra']==-1) {
                  echo '<div class="form-group row"><div class="col-sm-12 text-center">';
                  echo '<div class="gks_flock form-control-sm">';
                    echo gks_lang('Αποστολή στην ίδια διεύθυνση');
                    //echo $selected_ea['ea_name'];
                  echo '</div></div></div>';
                }
              } else {?>
              <div class="form-group row">
                <div class="col-sm-8 offset-sm-2">
                  <select id="form_select_apostoli" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                    <option value="-1" <?php echo ($row['address_extra']==-1 ? ' selected ' : '');?>><?php echo gks_lang('Αποστολή στην ίδια διεύθυνση');?></option>
                    <?php
                    echo $selected_ea_option;
                    ?>
                    <option value="0" <?php echo ($row['address_extra']==0 ? ' selected ' : '');?>>-- <?php echo gks_lang('Δημιουργία νέας διεύθυνσης');?> --</option>
                  </select>
                </div>
              </div>
              <?php } ?>
            
            
              <div id="div_extra_address" style="<?php echo ($row['address_extra']==-1 ? 'display: none;' : '');?>">
                <?php if ($gks_lock==false) { ?>
                <div class="row">  
                  <div class="col-md-12">
                    <div class="text-sm-center" style="font-weight111: bold;"><?php echo gks_lang('Πληροφορίες αποστολής');?></div>
                  </div>
                </div> 
                <?php } ?>              
                <div class="form-group row">
                  <label for="form_ea_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo $selected_ea['ea_name'].'ddd';
                      echo '</div>';
                    } else {?>
                    <input id="form_ea_name" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_name']);?>" >
                    <?php } ?>
                  </div>
                  <label for="form_ea_phone" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo '<a href="tel:'.$selected_ea['ea_phone'].'">'.$selected_ea['ea_phone'].'</a>';
                      echo '</div>';
                    } else {?>
                    <input id="form_ea_phone" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_phone']);?>" >
                    <?php } ?>
                  </div>
                </div>              
                <div class="form-group row">
                  <label for="form_ea_odos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo $selected_ea['ea_odos'];
                      echo '</div>';
                    } else {?>
                    <input id="form_ea_odos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_odos']);?>" >
                    <small class="form-text text-muted auto_googlemaps" id="form_ea_odos_auto_googlemaps"></small>
                    <?php } ?>
                  <input id="form_ea_branch" type="hidden" value="<?php echo htmlspecialchars_gks($selected_ea['ea_branch']);?>">
                  </div>
                  <label for="form_ea_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo $selected_ea['ea_arithmos'];
                      echo '</div>';
                    } else {?>
                    <input id="form_ea_arithmos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_arithmos']);?>" >
                    <?php } ?>
                  </div>
                </div>              
                <div class="form-group row">
                  <label for="form_ea_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo $selected_ea['ea_orofos'];
                      echo '</div>';
                    } else {?>
                    <input id="form_ea_orofos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_orofos']);?>" >
                    <?php } ?>
                  </div>
                  <label for="form_ea_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo $selected_ea['ea_perioxi'];
                      echo '</div>';
                    } else {?>
                    <input id="form_ea_perioxi" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_perioxi']);?>" >
                    <?php } ?>
                  </div>
                </div>              
                <div class="form-group row">
                  <label for="form_ea_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo $selected_ea['ea_poli'];
                      echo '</div>';
                    } else {?>
                    <input id="form_ea_poli" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_poli']);?>" >
                    <?php } ?>
                  </div>
                  <label for="form_ea_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo $selected_ea['ea_tk'];
                      echo '</div>';
                    } else {?>
                    <input id="form_ea_tk" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($selected_ea['ea_tk']);?>" >
                    <?php } ?>
                  </div>
                </div>              
  
                <div class="form-group row">
                  <label for="form_ea_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo $selected_ea['country_name'];
                      echo '</div>';
                    } else {?>
                    <select data-dbval="<?php echo $selected_ea['ea_country_id'];?>" id="form_ea_country_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                    </select> 
                    <?php } ?>
                  </div>
                  <label for="form_ea_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
                  <div class="col-sm-4">
                    <?php if ($gks_lock) {
                      echo '<div class="gks_flock form-control-sm">';
                        echo $selected_ea['nomos_descr'];
                      echo '</div>';
                    } else {?>
                    <select id="form_ea_nomos_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                      <option value="0"><?php echo gks_lang('Νομός');?>...</option>
                      <?php
                      $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                      gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                      $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                      FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                      ".$lang_prepare_gks_nomos['sql']['from2']."
                      where country_id=".$selected_ea['ea_country_id']." ORDER BY nomos_descr";
                      $result_select = $db_link->query($sql);        
                      if (!$result_select) {
                        debug_mail(false,'error sql',$sql);
                        die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                      }
                      while ($row_select = $result_select->fetch_assoc()) {
                        echo '<option value="'.$row_select['id_nomos'].'" ';
                        if ($selected_ea['ea_nomos_id'] == $row_select['id_nomos']) echo ' selected ';
                        echo '>'.$row_select['nomos_descr'].'</option>';
                      }
                      ?>
                    </select> 
                    <?php } ?>
                  </div>
                </div>
                
              </div>
            </div>
          </div>
                                 

        </div>
      </div>  
      
      
      <div class="card gks_card_expand" id="div_b2g" style="<?php if ($row['is_b2g']==0) echo 'display:none';?>">
        <div class="card-header" style="text-align:center">
          B2G
        </div>      
        <div class="card-body" <?php echo gks_card_body('b2g');?>>
          <div class="row">          
            <div class="col-sm-6">


              <div class="form-group row">
                <label for="b2g_aaht_foreas" class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Φορέας');?>:</label>
                <div class="col-sm-8">
                  <div class="gks_flock form-control-sm" id="div_b2g_aaht_foreas">
                    <?php echo $row['b2g_aaht_foreas'];?>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label for="b2g_aaht_typos_forea" class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Τύπος Φορέα');?>:</label>
                <div class="col-sm-8">
                  <div class="gks_flock form-control-sm" id="div_b2g_aaht_typos_forea">
                    <?php echo $row['b2g_aaht_typos_forea'];?>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label for="b2g_aaht_kodikos_ekatharisis" class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Κωδικός Υπηρεσίας Εκκαθάρισης');?>:</label>
                <div class="col-sm-8">
                  <div class="gks_flock form-control-sm" id="div_b2g_aaht_kodikos_ekatharisis">
                    <?php echo $row['b2g_aaht_kodikos_ekatharisis'];?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6">

              <div class="form-group row">
                <label for="b2g_inv_aaht_name" class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height">Buyer reference:<br>(BT-10)
                  <i class="fas fa-info-circle" id="b2g_inv_aaht_name_info"></i> 
                </label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['b2g_inv_aaht_name'];
                    echo '</div>';                
                  } else {?>                  
                  <input id="b2g_inv_aaht_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['b2g_inv_aaht_name']);?>">  
                  <?php } ?>
                </div>
                <div id="div_b2g_inv_aaht_name_info" class="col-sm-12" style="display:none;" data-show="0"></div>                
              </div>
                            
              <div class="form-group row">
                <label for="project_reference" class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height">Project Reference:<br>(BT-11)
                  <i class="fas fa-info-circle" id="project_reference_info"></i>  
                </label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['project_reference'];
                    echo '</div>';                
                  } else {?>
                  <input id="project_reference" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['project_reference']);?>">
                  <?php } ?>
                </div>
                <div id="div_project_reference_info" class="col-sm-12" style="display:none;" data-show="0"></div>
              </div>
                            
              <div class="form-group row">
                <label for="contract_reference" class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height">Contract Reference:<br>(BT-12)
                  <i class="fas fa-info-circle" id="contract_reference_info"></i>
                </label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['contract_reference'];
                    echo '</div>';                
                  } else {?>
                  <input id="contract_reference" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['contract_reference']);?>">
                  <?php } ?>
                </div>
                <div id="div_contract_reference_info" class="col-sm-12" style="display:none;" data-show="0"></div>
              </div>

              <div class="form-group row">
                <label for="b2g_inv_buyer_name" class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height">Βuyer Name:<br>(BT-44)
                  <i class="fas fa-info-circle" id="b2g_inv_buyer_name_info"></i> 
                </label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['b2g_inv_buyer_name'];
                    echo '</div>';                
                  } else {?>                   
                  <input id="b2g_inv_buyer_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['b2g_inv_buyer_name']);?>">
                  <?php } ?>
                </div>
                <div id="div_b2g_inv_buyer_name_info" class="col-sm-12" style="display:none;" data-show="0"></div>                
              </div>
                                          
              <div class="form-group row">
                <label for="b2g_inv_aaht_code" class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height">Buyer Identifier:<br>(BT-46)
                  <i class="fas fa-info-circle" id="b2g_inv_aaht_code_info"></i> 
                </label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['b2g_inv_aaht_code'];
                    echo '</div>';                
                  } else {?> 
                  <input id="b2g_inv_aaht_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['b2g_inv_aaht_code']);?>">                  
                  <?php } ?>
                </div>
                <div id="div_b2g_inv_aaht_code_info" class="col-sm-12" style="display:none;" data-show="0"></div>                
              </div>              
            </div>
            
          </div>

        </div>
      </div>      
      
      
      <div class="card gks_card_expand" id="div_warehouses" style="<?php if ($whi_eidos_parastatikou_type_id_org==0) echo 'display:none';?>">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποθήκη');?>
        </div>
        <?php
        if ($row['warehouses_id_from']==1 or $row['warehouses_id_from']==2) {$row['warehouses_id_from']=0;$row['warehouse_name_from']='';}
        if ($row['warehouses_id_to']==1   or $row['warehouses_id_to']==2)   {$row['warehouses_id_to']=0;$row['warehouse_name_to']='';}
        
        $div_show_user_display='';
        $warehouses_id_from_div_display='';
        $warehouses_id_from_elem_display='';
        $warehouses_id_from_elem_div_display='';
        $warehouses_id_from_addr_display='';
        $warehouses_id_to_elem_display='';
        $warehouses_id_to_elem_div_display='';
        $warehouses_id_to_addr_display='';
        if ($whi_eidos_parastatikou_type_id_org==24) { //apografi
          $div_show_user_display='display:none;';
          $warehouses_id_from_div_display='display:none;';
          $warehouses_id_from_elem_display='display:none;';
          $warehouses_id_from_elem_div_display='display:none;';
          $warehouses_id_from_addr_display='display:none;';
          $warehouses_id_to_addr_display='display:none;';          
        } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
          $div_show_user_display='display:none;';
          if ($seira_isdeliverynote==0) {
            $warehouses_id_from_addr_display='display:none;';
            $warehouses_id_to_addr_display='display:none;';
          }
          
        } else {
          if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
            $warehouses_id_from_elem_display='display:none;';
            if ($seira_isdeliverynote==0) {
              $warehouses_id_from_addr_display='display:none;';
              $warehouses_id_to_addr_display='display:none;';
            }
          } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
            $warehouses_id_to_elem_display='display:none;';
            if ($seira_isdeliverynote==0) {
              $warehouses_id_from_addr_display='display:none;';
              $warehouses_id_to_addr_display='display:none;';
            }            
          }
        }
        //echo '|'.$whi_eidos_parastatikou_stock_pros_org.'|'.$warehouses_id_from_elem_display.'|'.$warehouses_id_to_elem_display.'|';  die();
        ?>
        
        <div class="card-body" <?php echo gks_card_body('warehouse');?>>
          
          <div class="form-group1 row">
            <div class="col-sm-6" id="warehouses_id_from_div" style="<?php echo $warehouses_id_from_div_display;?>">
              <div class="form-group row">
                <div class="col-sm-12">
                  <div class="text-sm-center" style="font-weight: bold;"><?php echo gks_lang('Από');?></div>
                </div>
              </div>
              
              <div class="form-group row" id="warehouses_id_from_elem_div" style="<?php echo $warehouses_id_from_elem_div_display;?>">
                <label for="warehouses_id_from" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αποθήκη');?>:</label>
                <div class="col-sm-8" style="min-height:31px;">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                  if ($warehouses_id_from_elem_display=='') {
                    echo '<a href="admin-warehouses-item.php?id='.$row['warehouses_id_from'].'">'.$row['warehouse_name_from'].'</a>';
                  } else {
                    echo gks_lang('Εικονική Αποθήκη Τρίτων');
                  } 
                  echo '</div>';
                } else {?>              
                  <input id="warehouses_id_from" type="text" class="form-control form-control-sm myneedsave warehouses_id_from_elem" 
                  value="<?php echo htmlspecialchars_gks($row['warehouse_name_from']);?>" 
                  placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['warehouses_id_from'];?>"
                  style="<?php echo $warehouses_id_from_elem_display;?>">
                  
                  <div id="warehouses_id_from_triton" class="gks_flock form-control-sm"
                  style="<?php echo ($warehouses_id_from_elem_display=='' ? 'display:none;': '')?>">
                    <span><?php echo gks_lang('Εικονική Αποθήκη Τρίτων');?></span>
                    <i id="copy_warehouse_from_addr" class="far fa-copy tooltipster warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή διεύθυνσης από τον αντισυμβαλλόμενο');?>"></i>
                  
                  </div>
                                    
                <?php } ?>
                </div>
              </div>

              <div class="form-group row warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>">
                <label for="load_branch" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός Εγκατάστασης');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['load_branch'];
                    echo '</div>';                
                  } else {?>
                  <input id="load_branch" type="number" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo $row['load_branch'];?>" min=0>
                  <?php } ?>
                </div>
              </div>
                            
              <div class="form-group row warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>">
                <label for="load_odos" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['load_odos'];
                    echo '</div>';                
                  } else {?>
                  <input id="load_odos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['load_odos']);?>" >
                  <small class="form-text text-muted auto_googlemaps" id="load_odos_auto_googlemaps"></small>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>">
                <label for="load_arithmos" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['load_arithmos'];
                    echo '</div>';                
                  } else {?>
                  <input id="load_arithmos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['load_arithmos']);?>" >
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>">
                <label for="load_orofos" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['load_orofos'];
                    echo '</div>';                
                  } else {?>
                  <input id="load_orofos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['load_orofos']);?>" >
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>">
                <label for="load_perioxi" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['load_perioxi'];
                    echo '</div>';                
                  } else {?>
                  <input id="load_perioxi" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['load_perioxi']);?>" >
                  <?php } ?>
                </div>
              </div>    
              <div class="form-group row warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>">
                <label for="load_poli" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['load_poli'];
                    echo '</div>';                
                  } else {?>
                  <input id="load_poli" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['load_poli']);?>" >
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>">
                <label for="load_tk" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['load_tk'];
                    echo '</div>';                
                  } else {?>
                  <input id="load_tk" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['load_tk']);?>" >
                  <?php } ?>
                </div>
              </div>
    
              <div class="form-group row warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>">
                <label for="load_country_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo gks_lang_data_trans($row['country_name_load'],$row['load_country_id'],'gks_country','country_name');
                      echo '<input type="hidden" id="load_country_id_h" value="'.$row['load_country_id'].'">';
                    echo '</div>';                
                  } else {?>
                  <select data-dbval="<?php echo $row['load_country_id'];?>" id="load_country_id" <?php if ($gks_user_lock) echo 'disabled';?> class="form-control form-control-sm myneedsave">
                  </select> 
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_from_addr" style="<?php echo $warehouses_id_from_addr_display;?>">
                <label for="load_nomos_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo gks_lang_data_trans($row['nomos_descr_load'],$row['load_nomos_id'],'gks_nomoi','nomos_descr');
                    echo '</div>';
                  } else {?>
                  <select id="load_nomos_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                    <option value="0"><?php echo gks_lang('Νομός');?>...</option>
                    <?php
                    $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                    gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                    $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                    FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                    ".$lang_prepare_gks_nomos['sql']['from2']."
                    where country_id=".$row['load_country_id']." ORDER BY nomos_descr";
                    $result_select = $db_link->query($sql);        
                    if (!$result_select) {
                      debug_mail(false,'error sql',$sql);
                      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                    }
                    while ($row_select = $result_select->fetch_assoc()) {
                      echo '<option value="'.$row_select['id_nomos'].'" ';
                      if ($row['load_nomos_id'] == $row_select['id_nomos']) echo ' selected ';
                      echo '>'.$row_select['nomos_descr'].'</option>';
                    }
                    ?>
                  </select> 
                  <?php } ?>
                </div>
              </div>   
              
              
              
              
              
            </div>
            <div class="col-sm-6"  id="warehouses_id_to_div">
              <div class="form-group row">
                <div class="col-sm-12">
                  <div id="warehouses_id_to_label" class="text-sm-center" style="font-weight: bold;"><?php
                    if ($whi_eidos_parastatikou_type_id_org==24) { //apografi
                      echo gks_lang('Αφορά');
                    } else {
                      echo gks_lang('Προς');
                    }
                    ?>
                    </div>
                </div>
              </div>
                            
              <div class="form-group row" id="warehouses_id_to_elem_div" style="<?php echo $warehouses_id_to_elem_div_display;?>">
                <label for="warehouses_id_to" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αποθήκη');?>:</label>
                <div class="col-sm-8" style="min-height:31px;">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                  if ($warehouses_id_to_elem_display=='') {
                    echo '<a href="admin-warehouses-item.php?id='.$row['warehouses_id_to'].'">'.$row['warehouse_name_to'].'</a>';
                  } else {
                    echo  gks_lang('Εικονική Αποθήκη Τρίτων');
                  }
                  echo '</div>';
                } else {?> 
                  <input id="warehouses_id_to" type="text" class="form-control form-control-sm myneedsave warehouses_id_to_elem" 
                  value="<?php echo htmlspecialchars_gks($row['warehouse_name_to']);?>" 
                  placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['warehouses_id_to'];?>"
                  style="<?php echo $warehouses_id_to_elem_display;?>">
                  
                  <div id="warehouses_id_to_triton" class="gks_flock form-control-sm"
                  style="<?php echo ($warehouses_id_to_elem_display=='' ? 'display:none;': '')?>">
                    <span><?php echo gks_lang('Εικονική Αποθήκη Τρίτων');?></span>
                    <i id="copy_warehouse_to_addr" class="far fa-copy tooltipster warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή διεύθυνσης από τον αντισυμβαλλόμενο');?>"></i>
                   
                  </div>
                <?php } ?>
                </div>
                
                
                
              </div>
              <div class="form-group row warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>">
                <label for="deli_branch" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός Εγκατάστασης');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['deli_branch'];
                    echo '</div>';                
                  } else {?>
                  <input id="deli_branch" type="number" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo $row['deli_branch'];?>" min=0>
                  <?php } ?>
                </div>
              </div>
                            
              <div class="form-group row warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>">
                <label for="deli_odos" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['deli_odos'];
                    echo '</div>';                
                  } else {?>
                  <input id="deli_odos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['deli_odos']);?>" >
                  <small class="form-text text-muted auto_googlemaps" id="deli_odos_auto_googlemaps"></small>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>">
                <label for="deli_arithmos" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['deli_arithmos'];
                    echo '</div>';                
                  } else {?>
                  <input id="deli_arithmos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['deli_arithmos']);?>" >
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>">
                <label for="deli_orofos" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['deli_orofos'];
                    echo '</div>';                
                  } else {?>
                  <input id="deli_orofos" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['deli_orofos']);?>" >
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>">
                <label for="deli_perioxi" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['deli_perioxi'];
                    echo '</div>';                
                  } else {?>
                  <input id="deli_perioxi" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['deli_perioxi']);?>" >
                  <?php } ?>
                </div>
              </div>
    
              <div class="form-group row warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>">
                <label for="deli_poli" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['deli_poli'];
                    echo '</div>';                
                  } else {?>
                  <input id="deli_poli" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['deli_poli']);?>" >
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>">
                <label for="deli_tk" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo $row['deli_tk'];
                    echo '</div>';                
                  } else {?>
                  <input id="deli_tk" type="text" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['deli_tk']);?>" >
                  <?php } ?>
                </div>
              </div>
    
              <div class="form-group row warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>">
                <label for="deli_country_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo gks_lang_data_trans($row['country_name_deli'],$row['deli_country_id'],'gks_country','country_name');
                      echo '<input type="hidden" id="deli_country_id_h" value="'.$row['deli_country_id'].'">';
                    echo '</div>';                
                  } else {?>
                  <select data-dbval="<?php echo $row['deli_country_id'];?>" id="deli_country_id" <?php if ($gks_user_lock) echo 'disabled';?> class="form-control form-control-sm myneedsave">
                  </select> 
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row warehouses_id_to_addr" style="<?php echo $warehouses_id_to_addr_display;?>">
                <label for="deli_nomos_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
                <div class="col-sm-8">
                  <?php if ($gks_lock) {
                    echo '<div class="gks_flock form-control-sm">';
                      echo gks_lang_data_trans($row['nomos_descr_deli'],$row['deli_nomos_id'],'gks_nomoi','nomos_descr');
                    echo '</div>';
                  } else {?>
                  <select id="deli_nomos_id" class="form-control form-control-sm myneedsave" <?php if ($gks_user_lock) echo 'disabled';?>>
                    <option value="0"><?php echo gks_lang('Νομός');?>...</option>
                    <?php
                    $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                    gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                    $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                    FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                    ".$lang_prepare_gks_nomos['sql']['from2']."
                    where country_id=".$row['deli_country_id']." ORDER BY nomos_descr";
                    $result_select = $db_link->query($sql);        
                    if (!$result_select) {
                      debug_mail(false,'error sql',$sql);
                      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                    }
                    while ($row_select = $result_select->fetch_assoc()) {
                      echo '<option value="'.$row_select['id_nomos'].'" ';
                      if ($row['deli_nomos_id'] == $row_select['id_nomos']) echo ' selected ';
                      echo '>'.$row_select['nomos_descr'].'</option>';
                    }
                    ?>
                  </select> 
                  <?php } ?>
                </div>
              </div>   
            </div>
          </div>      
        </div>
      </div>
    </div>
  </div>
</div>






<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      
      <div class="card gks_card_expand" id="div_other_entity" style="<?php if ($acc_eidos_parastatikou_other_entity==0) echo 'display:none';?>">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Λοιπoί Συσχετιζόμενοι ΑΦΜ');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('othere');?> id="oeitem_entity_table">
          
          <div class="form-group row gks_other_entity_header">
            <div class="col-12 col-sm-6  col-md-4  col-lg-2 gks_other_entity_col">
              <div class="table-dark gks_other_entity_label"><?php echo gks_lang('Τύπος');?></div>
            </div>
            <div class="col-12 col-sm-6  col-md-4  col-lg-2 gks_other_entity_col">
              <div class="table-dark gks_other_entity_label"><?php echo gks_lang('Συσχετιζόμενος');?></div>
            </div>
            <div class="col-12 col-sm-6  col-md-4  col-lg-2 gks_other_entity_col">
              <div class="table-dark gks_other_entity_label"><?php echo gks_lang('Υποκατάστημα');?></div>
            </div>
            <div class="col-12 col-sm-10  col-md-10  col-lg-5 gks_other_entity_col">
              <div class="table-dark gks_other_entity_label"><?php echo gks_lang('Στοιχεία');?></div>
            </div>            
            <div class="col-12 col-sm-2  col-md-2  col-lg-1 gks_other_entity_col">
              <div class="table-dark gks_other_entity_label">
                <i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i>  
              </div>
            </div>
          </div>
          <?php
          
          
          $entity_aa=0;
          foreach ($other_entity_array as $oe_item) {
          $entity_aa++;  
            
          ?>
          
          <div class="form-group row gks_other_entity_item align-items-center" data-oeaa="<?php echo $entity_aa;?>" data-recid="<?php echo $oe_item['id_acc_inv_other_entity'];?>">
            <div class="col-12 col-sm-6  col-md-4  col-lg-2">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock gks_flock_small form-control-sm">';
                  echo $oe_item['aade_entitytype_descr'];
                echo '</div>';                
              } else { ?>
              <select data-dbval="<?php echo $oe_item['aade_entitytype_id'];?>" class="oeitem_aade_entitytype_id form-control form-control-sm myneedsave" data-oeaa="<?php echo $entity_aa;?>">
              </select>
              <?php } ?>
            </div>
            <div class="col-12 col-sm-6  col-md-4  col-lg-2">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock gks_flock_small form-control-sm">';
                  echo $oe_item['gks_nickname'];
                echo '</div>';                
              } else { ?>
              <input class="oeitem_entity_user_id form-control form-control-sm" 
              data-oeaa="<?php echo $entity_aa;?>"
              value="<?php echo $oe_item['gks_nickname'];?>"
              style="width:calc(100% - 22px);display:inline;"
              data-id="<?php echo $oe_item['entity_user_id'];?>"
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
              >
              <a data-oeaa="<?php echo $entity_aa;?>" class="autocomplete_entity_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $oe_item['entity_user_id'];?>" style="<?php if ($row['user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
              
              <?php } ?>
            </div>
            <div class="col-12 col-sm-6  col-md-4  col-lg-2">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock gks_flock_small form-control-sm">';
                foreach ($oe_item['user_extra_address'] as $oeea_item) {
                  if ($oeea_item['id']==$oe_item['address_extra']) echo $oeea_item['descr'];
                } 
                echo '</div>';                
              } else { ?>
              <select class="oeitem_address_extra form-control form-control-sm myneedsave" data-oeaa="<?php echo $entity_aa;?>">
                <?php
                foreach ($oe_item['user_extra_address'] as $oeea_item) {
                  echo '<option value="'.$oeea_item['id'].'" '.
                  ($oeea_item['id']==$oe_item['address_extra'] ? 'selected' : '').
                  '>'.$oeea_item['descr'].'</option>';
                } 
                ?>
              </select>              
              <?php } ?>
            </div>
            <div class="col-12 col-sm-10  col-md-10  col-lg-5">
              <div class="oeitem_address_text" data-oeaa="<?php echo $entity_aa;?>">
              <?php if ($gks_lock) {
                $oeea_ret=gks_other_entity_get_data('gks_acc_inv',$oe_item['id_acc_inv_other_entity']);
              } else {
                $oeea_ret=gks_other_entity_get_data('gks_acc_inv',-1,$oe_item['entity_user_id'],$oe_item['address_extra']);
              }
              echo $oeea_ret['html'];
              ?>
              </div>
            </div>            
            <div class="col-12 col-sm-2  col-md-2  col-lg-1">
              <div class="text-center gks_icons">
                <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_other_entity_delete" data-oeaa="<?php echo $entity_aa;?>"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_handle"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_other_entity_add" data-oeaa="<?php echo $entity_aa;?>"></i>
                </div>
                <?php } ?>
              </div>
            </div>            
          </div>          
          
          <?php
          }
          ?>
          <div id="div_other_entity_footer"></div>
        </div>
      </div>      
    </div>
  </div>
</div>


<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand" id="div_correlated_invoices" style="<?php if ($journal_has_correlated_invoices==0) echo 'display:none';?>">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συσχετιζόμενα Παραστατικά');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('corri');?> id="coiitem_correlated_invoices_table">
          <div class="form-group row gks_correlated_invoices_header">
            <div class="col-12 col-sm-4  col-md-3  col-lg-2 gks_correlated_invoices_col">
              <div class="table-dark gks_correlated_invoices_label"><?php echo gks_lang('ΜΑΡΚ');?></div>
            </div>
            <div class="col-12 col-sm-6  col-md-7  col-lg-9 gks_correlated_invoices_col">
              <div class="table-dark gks_correlated_invoices_label"><?php echo gks_lang('Στοιχεία');?></div>
            </div>            
            <div class="col-12 col-sm-2  col-md-2  col-lg-1 gks_correlated_invoices_col">
              <div class="table-dark gks_correlated_invoices_label">
                <i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i>  
              </div>
            </div>
          </div>
          <?php
          $coi_aa=0;
          foreach ($correlated_invoices_array as $ci_item) {
          $coi_aa++;  
            
          ?>
          
          <div class="form-group row gks_correlated_invoices_item align-items-center" data-coiaa="<?php echo $coi_aa;?>" data-recid="<?php echo $ci_item['id_acc_inv_correlated_invoices'];?>">

            <div class="col-12 col-sm-4  col-md-3 col-lg-2">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock gks_flock_small form-control-sm">';
                  echo $ci_item['coi_mark'];
                echo '</div>';                
              } else { ?>
              <input class="coiitem_mark form-control form-control-sm" 
              value="<?php 
              $coi_mark=trim_gks($ci_item['coi_mark']);
              if ($coi_mark!='') {
                echo $coi_mark;
              } else if ($ci_item['coi_acc_inv_id']>0) {
                echo 'acc_inv#'.$ci_item['coi_acc_inv_id'];
              } else if ($ci_item['coi_acc_pay_id']>0) {
                echo 'acc_pay#'.$ci_item['coi_acc_pay_id'];
              } else if ($ci_item['coi_whi_mov_id']>0) {
                echo 'whi_mov#'.$ci_item['coi_whi_mov_id'];
              }
              
              ?>"
              placeholder="<?php echo gks_lang('ΜΑΡΚ ή #αριθμό ή @ημερομηνία');?> ..."
              data-coiaa="<?php echo $coi_aa;?>"
              data-coi_mark="<?php echo $ci_item['coi_mark'];?>"
              data-coi_acc_inv_id="<?php echo $ci_item['coi_acc_inv_id'];?>"
              data-coi_acc_pay_id="<?php echo $ci_item['coi_acc_pay_id'];?>"
              data-coi_whi_mov_id="<?php echo $ci_item['coi_whi_mov_id'];?>"
              
              >
              <?php } ?>
            </div>

            <div class="col-12 col-sm-6  col-md-7  col-lg-9">
              <div class="coiitem_text" data-coiaa="<?php echo $coi_aa;?>">
              <?php 
              $coiitem_ret=gks_correlated_invoices_get_data($ci_item['coi_mark'],$ci_item['coi_acc_inv_id'],$ci_item['coi_acc_pay_id'],$ci_item['coi_whi_mov_id']);
              
              echo $coiitem_ret['html'];
              ?>
              </div>
            </div>            
            <div class="col-12 col-sm-2  col-md-2  col-lg-1">
              <div class="text-center gks_icons">
                <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_correlated_invoices_delete" data-coiaa="<?php echo $coi_aa;?>"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_handle"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_correlated_invoices_add" data-coiaa="<?php echo $coi_aa;?>"></i>
                </div>
                <?php } ?>
              </div>
            </div>            
          </div>          
          <?php
          }
          ?>
          <div id="div_correlated_invoices_footer"></div>
        </div>
      </div>      
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand" id="div_multiple_connected_marks" style="<?php if ($journal_has_multiple_connected_marks==0) echo 'display:none';?>">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πολλαπλά Συνδεόμενα ΜΑΡΚ');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('mcm_recs');?> id="mcmitem_multiple_connected_marks_table">
          <div class="form-group row gks_multiple_connected_marks_header">
            <div class="col-12 col-sm-4  col-md-3  col-lg-2 gks_multiple_connected_marks_col">
              <div class="table-dark gks_multiple_connected_marks_label"><?php echo gks_lang('ΜΑΡΚ');?></div>
            </div>
            <div class="col-12 col-sm-6  col-md-7  col-lg-9 gks_multiple_connected_marks_col">
              <div class="table-dark gks_multiple_connected_marks_label"><?php echo gks_lang('Στοιχεία');?></div>
            </div>            
            <div class="col-12 col-sm-2  col-md-2  col-lg-1 gks_multiple_connected_marks_col">
              <div class="table-dark gks_multiple_connected_marks_label">
                <i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i>  
              </div>
            </div>
          </div>
          <?php
          $mcm_aa=0;
          foreach ($multiple_connected_marks_array as $ci_item) {
          $mcm_aa++;  
            
          ?>
          
          <div class="form-group row gks_multiple_connected_marks_item align-items-center" data-mcmaa="<?php echo $mcm_aa;?>" data-recid="<?php echo $ci_item['id_acc_inv_multiple_connected_marks'];?>">

            <div class="col-12 col-sm-4  col-md-3 col-lg-2">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock gks_flock_small form-control-sm">';
                  echo $ci_item['mcm_mark'];
                echo '</div>';                
              } else { ?>
              <input class="mcmitem_mark form-control form-control-sm" 
              value="<?php 
              $mcm_mark=trim_gks($ci_item['mcm_mark']);
              if ($mcm_mark!='') {
                echo $mcm_mark;
              } else if ($ci_item['mcm_acc_inv_id']>0) {
                echo 'acc_inv#'.$ci_item['mcm_acc_inv_id'];
              } else if ($ci_item['mcm_acc_pay_id']>0) {
                echo 'acc_pay#'.$ci_item['mcm_acc_pay_id'];
              } else if ($ci_item['mcm_whi_mov_id']>0) {
                echo 'whi_mov#'.$ci_item['mcm_whi_mov_id'];
              }
              
              ?>"
              placeholder="<?php echo gks_lang('ΜΑΡΚ ή #αριθμό ή @ημερομηνία');?> ..."
              data-mcmaa="<?php echo $mcm_aa;?>"
              data-mcm_mark="<?php echo $ci_item['mcm_mark'];?>"
              data-mcm_acc_inv_id="<?php echo $ci_item['mcm_acc_inv_id'];?>"
              data-mcm_acc_pay_id="<?php echo $ci_item['mcm_acc_pay_id'];?>"
              data-mcm_whi_mov_id="<?php echo $ci_item['mcm_whi_mov_id'];?>"
              
              >
              <?php } ?>
            </div>

            <div class="col-12 col-sm-6  col-md-7  col-lg-9">
              <div class="mcmitem_text" data-mcmaa="<?php echo $mcm_aa;?>">
              <?php 
              $mcmitem_ret=gks_multiple_connected_marks_get_data($ci_item['mcm_mark'],$ci_item['mcm_acc_inv_id'],$ci_item['mcm_acc_pay_id'],$ci_item['mcm_whi_mov_id']);
              
              echo $mcmitem_ret['html'];
              ?>
              </div>
            </div>            
            <div class="col-12 col-sm-2  col-md-2  col-lg-1">
              <div class="text-center gks_icons">
                <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_multiple_connected_marks_delete" data-mcmaa="<?php echo $mcm_aa;?>"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_handle"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_multiple_connected_marks_add" data-mcmaa="<?php echo $mcm_aa;?>"></i>
                </div>
                <?php } ?>
              </div>
            </div>            
          </div>          
          <?php
          }
          ?>
          <div id="div_multiple_connected_marks_footer"></div>
        </div>
      </div>      
    </div>
  </div>
</div>

<div id="test"></div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('eidi');?> id="eidi_table"> 
          <?php
          
          
//            $perm_gks_acc_inv_edit=true;
//            $GKS_ACC_INV_COL_ITEMPRICE=false;
//            $GKS_ACC_INV_COL_FPA=false;
            
            
            $gkscols1='';
            $gkscols2='';
            $gkscols3='';
            $gkscols4='';
            $gkscols5='';
            $gkscols6='';
            $gkscols7='';
            $gkscols8='';
            $gkscols9='';
            $gkscols10='';
            
            
            //if ($perm_gks_acc_inv_edit) {
              if ($GKS_ACC_INV_COL_ITEMPRICE) {
                if ($GKS_ACC_INV_COL_FPA) {
                  $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col';
                  $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-2 gks_items_col';
                  $gkscols3 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col';
                  $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';
                  $gkscols5 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';
                  $gkscols6 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';            
                  $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';            
                  $gkscols8 ='col-6  col-sm-2  col-md-1  col-lg-1 gks_items_col';            
                  $gkscols9 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';            
                  $gkscols10='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';            
                } else {
                  $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col';
                  $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-3 gks_items_col';
                  $gkscols3 ='col-12 col-sm-4  col-md-3  col-lg-2 gks_items_col';
                  $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';
                  $gkscols5 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';
                  $gkscols6 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';            
                  $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';            
                  $gkscols8 ='col-6  col-sm-4  col-md-1  col-lg-1 gks_items_col offset-6 offset-sm-0 ';            
                  //$gkscols9 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';            
                  $gkscols10='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';            
                }
                
                
              } else {
                if ($GKS_ACC_INV_COL_FPA) {
                  $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col';
                  $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-3 gks_items_col';
                  $gkscols3 ='col-12 col-sm-12 col-md-5  col-lg-2 gks_items_col';
                  $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';
                  $gkscols5 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';
                  $gkscols6 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';            
                  $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';            
                  $gkscols8 ='col-3  col-sm-1  col-md-1  col-lg-1 gks_items_col';            
                  $gkscols9 ='col-3  col-sm-1  col-md-1  col-lg-1 gks_items_col';            
                } else {
                  $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col';
                  $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-4 gks_items_col';
                  $gkscols3 ='col-12 col-sm-12 col-md-6  col-lg-2 gks_items_col';
                  $gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';
                  $gkscols5 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';
                  $gkscols6 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';            
                  $gkscols7 ='col-6  col-sm-4  col-md-2  col-lg-1 gks_items_col';            
                  $gkscols8 ='col-6  col-sm-1  col-md-1  col-lg-1 gks_items_col';            
                  //$gkscols9 ='col-6  col-sm-1  col-md-1  col-lg-1 gks_items_col';            
                }
              }
//            } else {
//              $gkscols1 ='col-12 col-sm-4  col-md-4  col-lg-2 gks_items_col';
//              $gkscols2 ='col-12 col-sm-8  col-md-8  col-lg-5 gks_items_col';
//              $gkscols3 ='col-12 col-sm-8  col-md-8  col-lg-4 gks_items_col';
//              $gkscols5 ='col-12 col-sm-4  col-md-4  col-lg-1 gks_items_col';            
//            }
          
          
          ?>
          
          
          <div class="form-group row gks_eidos_label">
            <div class="<?php echo $gkscols1;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Κωδικός');?></div>
            </div>
            <div class="<?php echo $gkscols2;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Περιγραφή');?></div>
            </div>
            <div class="<?php echo $gkscols3;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Παρατηρήσεις');?></div>
            </div>

            <div class="<?php echo $gkscols5;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Ποσότητα');?></div>
            </div>
            


<?php if ($GKS_ACC_INV_COL_ITEMPRICE) {?>
            <div class="<?php echo $gkscols10;?>">
              <div class="table-dark gks_eidos_label"><?php if ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA) echo '<span class="tooltipster" title="'.gks_lang('Περιέχει ΦΠΑ η τιμή μονάδος').'">'.gks_lang('πΦΠΑ').'</span> ';?>Τιμή</div>
            </div>
<?php } ?> 
            <div class="<?php echo $gkscols6;?>">
              <div class="table-dark gks_eidos_label tooltipster" title="<?php echo gks_lang('Έκπτωση %');?>"><?php echo gks_lang('Έκπ%');?></div>
            </div>
            <div class="<?php echo $gkscols7;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Σύνολο');?></div>
            </div>
<?php if ($GKS_ACC_INV_COL_FPA) {?>            
            <div class="<?php echo $gkscols9;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('ΦΠΑ');?></div>
            </div>
<?php } ?> 
            <div class="<?php echo $gkscols8;?>">
              <div class="table-dark gks_eidos_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
            </div> 

          </div>           
<?php 

  



    $aa = 0;
    $eidi_sum_quantity=0;
    $eidi_sum_price_net=0;
    $fields_change=array();
    foreach ($eidos_array as $eidos) {

      $aa++;
      $eidi_sum_quantity+=$eidos['product_quantity'];
      $eidi_sum_price_net+=$eidos['product_price_final_all_net'];
  
      $ekptosi_poso_html='';
      $ekptosi_poso = $eidos['product_price_start_all_net']-$eidos['product_price_final_all_net'];
      if (abs($ekptosi_poso) >= (1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) $ekptosi_poso_html= myCurrencyFormat($ekptosi_poso,false);
      
      $ekptosi_poso_netfpa_html='';
      $ekptosi_poso_netfpa = $eidos['product_price_start_all_net']+$eidos['product_price_start_all_fpa']-$eidos['product_price_final_all_net']-$eidos['product_price_final_all_fpa'];
      if (abs($ekptosi_poso_netfpa) >= (1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) $ekptosi_poso_netfpa_html= myCurrencyFormat($ekptosi_poso_netfpa,false);
      
      
      $fields_change[$aa]='';
      if (abs($ekptosi_poso) >= (1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) {
        if ($eidos['product_price_coupon_use']=='') {
          $fields_change[$aa]='gks_price'; //gks_ekptosi
        } else {
          if ($eidos['product_price_coupon_use_disabled']!=0) {
            $fields_change[$aa]='gks_price'; //gks_ekptosi   
          } else {
            $fields_change[$aa]='';
          }
        }
      } else {
        if (1==2 and $row_room['product_price_coupon_use']=='') {
          
        } else {
          $fields_change[$aa]='gks_price';
        }
      } 
      
      $from_aade_import_lock=false;
      if (isset($eidos['from_aade_import_lock']) and $eidos['from_aade_import_lock']==1) $from_aade_import_lock=true;
      $from_aade_import_user_fpa=false;
      if (isset($eidos['from_aade_import_user_fpa']) and $eidos['from_aade_import_user_fpa']==1) $from_aade_import_user_fpa=true;
      
      if ($from_aade_import_lock) $from_aade_import_user_fpa=true;
      

      
?>
          <div class="gks_eidos_2divs <?php if ($from_aade_import_lock) echo ' from_aade_import_lock';?>" data-aa="<?php echo $aa;?>" >
            <div class="form-group row gks_eidos <?php if ($GKS_ACC_INV_EXTRA_OPEN) echo 'gks_eidos_radup';?>" data-recid="<?php echo ($template_id==0 ? $eidos['id_acc_inv_product'] : '0');?>" data-aa="<?php echo $aa;?>">
              <div class="<?php echo $gkscols1;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock gks_flock_small form-control-sm">';
                    echo $eidos['product_code'];
                  echo '</div>';
                } else {?>
                <input type="text" class="form-control form-control-sm gks_code <?php if ($from_aade_import_lock) echo ' from_aade_import_lock';?>" data-aa="<?php echo $aa;?>" 
                style="width:100%;"
                value="<?php echo $eidos['product_code']?>" 
                data-varos="<?php echo $eidos['product_varos'];?>"
                data-ogos_x="<?php echo intval($eidos['product_ogos_x']);?>"
                data-ogos_y="<?php echo intval($eidos['product_ogos_y']);?>"
                data-ogos_z="<?php echo intval($eidos['product_ogos_z']);?>"
                data-need_apostoli="<?php echo intval($eidos['product_need_apostoli']);?>"
                placeholder="<?php echo gks_lang('Κωδικός');?>"
                <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>>
                <?php } ?>
              </div>
              <div class="<?php echo $gkscols2;?>">
                <div class="text-left"><?php 
                $product_descr_small=trim_gks($eidos['product_descr_small_p']);  
                if ($product_descr_small!='') {
                  $product_descr_small="<table style='max-width:300px' border=0><tr><td>".str_replace('"',"'", $product_descr_small)."</td></tr></table>";
                }
                $myimgurl=trim_gks($eidos['product_photo_p'].'');
                if ($myimgurl == '') {
                  $myimgurl="/my/img/product.png";
                  echo '<a class="gks_photo_link" data-aa="'.$aa.'" tabIndex="-1" href="/my/img/product.png" style="display:none;"><img class="gks_img" data-aa="'.$aa.'" src="/my/img/product.png"></a>';
                } else {
                  $mydir = dirname($myimgurl);
                  if (endwith($mydir,'/thumbnail')) {
                    $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
                  } else {
                    $photo_url=$myimgurl;
                  }
                  echo '<a class="lightgalleryitem_user gks_photo_link" data-aa="'.$aa.'"  tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$eidos['product_code'].'"><img class="gks_img" data-aa="'.$aa.'" src="'.$myimgurl.'"></a>';
                }
                if ($perm_gks_acc_inv_edit) echo '<i class="gks_product_zoom enterrow fas fa-pen" data-id_product="'.$eidos['product_id'].'" data-aa="'.$aa.'" title="'.gks_lang('Προβολή Είδους').'"></i>';
                echo '<i class="fas fa-info-circle gks_info_descr '.($product_descr_small!='' ? 'tooltipster' : '').'" data-aa="'.$aa.'" title="'.$product_descr_small.'" '.($product_descr_small=='' ? 'style="display:none;"' : '').'></i>';
                if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_descr">';
                    echo htmlspecialchars_gks($eidos['product_descr']);
                  echo '</div>';                
                } else {?>
                  
                  <textarea class="gks_descr form-control form-control-sm" rows="1" 
                  data-aa="<?php echo $aa;?>"   
                  placeholder="<?php echo gks_lang('Περιγραφή');?>"
                    <?php if ($from_aade_import_lock) { ?>
                      data-prev-text="<?php echo base64_encode(htmlspecialchars_gks($eidos['product_descr']));?>"
                    <?php } ?>
                  ><?php echo  htmlspecialchars_gks($eidos['product_descr']);?></textarea>
                <?php }?>
                
                </div>
              </div>
              <div class="<?php echo $gkscols3;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_comments">';
                    echo nl2br_gks(htmlspecialchars_gks($eidos['product_comments']));
                  echo '</div>';
                } else {?>
                  <textarea class="gks_comments form-control form-control-sm" rows="1" 
                    data-aa="<?php echo $aa;?>" 
                    <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?> 
                    placeholder="<?php echo gks_lang('Σχόλιο');?>"
                    <?php if ($from_aade_import_lock) { ?>
                      data-prev-text="<?php echo base64_encode(htmlspecialchars_gks($eidos['product_comments']));?>"
                    <?php } ?>                  
                  ><?php echo htmlspecialchars_gks($eidos['product_comments']);?></textarea>
                <?php } ?>
              </div>
              
              <div class="<?php echo $gkscols5;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_quantity_lock">';
                    echo myNumberFormatNo0Local($eidos['product_quantity']);
                  echo '</div>';
                } else {?>
                <input style="text-align:right;" type="number" class="form-control form-control-sm gks_quantity" 
                data-aa="<?php echo $aa;?>" 

                data-prev-value="<?php echo $eidos['product_quantity'];?>" 
                value="<?php if ($eidos['product_quantity']!=0) echo $eidos['product_quantity'];?>" 
                min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>" 
                <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?> 
                placeholder="<?php echo gks_lang('Ποσότητα');?>"
                <?php if ($from_aade_import_lock) echo ' disabled ';?>
                >
                <?php } ?>
                <span class="gks_monada_span<?php echo ($gks_lock ? '_lock' :'');?>" 
                  data-mon-id="<?php echo $eidos['product_monada_id'];?>" 
                  data-aa="<?php echo $aa;?>"
                  <?php if ($from_aade_import_lock) { ?>
                  data-prev-mon-id="<?php echo $eidos['product_monada_id'];?>" 
                  data-prev-monsymbol="<?php echo base64_encode($eidos['monada_symbol']);?>"
                  <?php } ?>
                  ><?php echo $eidos['monada_symbol'];?></span>
              </div>
  
              

  <?php if ($GKS_ACC_INV_COL_ITEMPRICE) {?>
              <div class="<?php echo $gkscols10;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_peritem_net_lock '.($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA ? 'gks_peritem_net_lock_s': '').'">';
                    if ($eidos['product_price_final_peritem_net']!=0) {
                      
                      if ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA) {
                        if ($eidos['product_price_check_fpa']==1) {
                          echo '<i class="far fa-check-square"></i> '.myCurrencyFormat($eidos['product_price_final_peritem_net']+$eidos['product_price_final_peritem_fpa'],false);
                        } else {
                          echo '<i class="far fa-square"></i> '.myCurrencyFormat($eidos['product_price_final_peritem_net'],false);
                        }
                      } else {
                        echo myCurrencyFormat($eidos['product_price_final_peritem_net'],false);
                      }
                      
                    }
                  echo '</div>';
                } else {
                  $final_peritem=0;
                  if ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA) {
                    echo '<input type="checkbox" class="gks_peritem_check_fpa" data-aa="'.$aa.'" '.($eidos['product_price_check_fpa']==1 ? 'checked' : '').'>';
                    if ($eidos['product_price_check_fpa']==1) {
                      $final_peritem=$eidos['product_price_final_peritem_net']+$eidos['product_price_final_peritem_fpa'];
                    } else {
                      $final_peritem=$eidos['product_price_final_peritem_net'];
                    }
                  } else {
                    $final_peritem=$eidos['product_price_final_peritem_net'];
                  }
                  
                  
                  
                ?>
                <input type="number" class="form-control form-control-sm gks_peritem_net <?php if ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA) echo 'gks_peritem_net_s';?>" 
                data-aa="<?php echo $aa;?>" 
                value="<?php 
                $valnotzero='';
                if ($final_peritem!=0) {
                  $valnotzero=myNumberFormatNo0($final_peritem);
                  echo $valnotzero;
                }?>" 
                style="text-align:right;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
                
                data-product_price_final_peritem_net="<?php echo number_format($eidos['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-product_price_final_peritem_fpa="<?php echo number_format($eidos['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                
                placeholder="<?php echo gks_lang('Τιμή');?>"
                <?php if ($from_aade_import_lock) echo ' disabled ';?>
                >
                <?php } ?>
              </div>
  <?php } ?>
  
              <div class="<?php echo $gkscols6;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_ekptosi_pososto_lock">';
                    if ($eidos['product_price_ekptosi_pososto']!=0) echo myNumberFormatNo0Local($eidos['product_price_ekptosi_pososto']);
                  echo '</div>';
                } else {?>
                <input type="number" class="form-control form-control-sm gks_ekptosi_pososto" data-aa="<?php echo $aa;?>" 
                value="<?php 
                $valnotzero='';
                if ($eidos['product_price_ekptosi_pososto']!=0) {
                  $valnotzero=myNumberFormatNo0($eidos['product_price_ekptosi_pososto']);
                  echo $valnotzero;
                }?>" 
                style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_POSOSTO;?>"
                placeholder="<?php echo gks_lang('Έκπτωση');?>"
                <?php if ($from_aade_import_lock) echo ' disabled ';?>
                ><?php } 
                $product_price_coupon_use=$eidos['product_price_coupon_use'];
                ?>
                <div class="gks_coupon" data-aa="<?php echo $aa;?>"><div 
                  class="gks_coupon_item <?php if ($eidos['product_price_coupon_use_disabled']!=0) echo 'gks_coupon_item_disabled'.($gks_lock ? '_lock' :'');?>" data-aa="<?php echo $aa;?>" style="<?php echo ($product_price_coupon_use=='' ? 'display:none;' : '');?>"   ><?php echo $product_price_coupon_use;?></div></div>              
              </div>
              <div class="<?php echo $gkscols7;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_price_lock '.($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA ? 'gks_price_lock_s': '').'">';
                    if ($eidos['product_price_final_all_net']!=0) {
                      if ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA) {
                        if ($eidos['product_price_check_fpa']==1) {
                          echo myCurrencyFormat($eidos['product_price_final_all_net']+ $eidos['product_price_final_all_fpa'],false);
                        } else {
                          echo myCurrencyFormat($eidos['product_price_final_all_net'],false);
                        }
                      } else {
                        echo myCurrencyFormat($eidos['product_price_final_all_net'],false);
                      }
                      
                    }
                  echo '</div>';
                } else {
                  $final_all_net=0;
                  if ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA) {
                    if ($eidos['product_price_check_fpa']==1) {
                      $final_all_net=$eidos['product_price_final_all_net']+$eidos['product_price_final_all_fpa'];
                    } else {
                      $final_all_net=$eidos['product_price_final_all_net'];
                    }
                  } else {
                    $final_all_net=$eidos['product_price_final_all_net'];
                  }                
                  
                  
                ?>
                <input type="number" class="form-control form-control-sm gks_price" data-aa="<?php echo $aa;?>" 
                value="<?php if ($final_all_net!=0) echo number_format($final_all_net,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                style="text-align:right;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
                data-product_price_start_all_net="<?php echo number_format($eidos['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-product_price_final_all_net="<?php echo number_format($eidos['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-product_price_final_all_fpa="<?php echo number_format($eidos['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-product_fpa_id="<?php echo $eidos['product_fpa_id'];?>" 
                data-product_fpa_pososto="<?php echo number_format($eidos['product_fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                data-fpa_descr_print="<?php echo $eidos['fpa_descr_print'];?>"
                placeholder="<?php echo gks_lang('Σύνολο');?>"
                <?php if ($from_aade_import_lock) echo ' disabled ';?>
                
                >
                <?php } ?>
                <div class="gks_ekptosi<?php echo ($gks_lock ? '_lock gks_flock form-control-sm' :'');?>" data-aa="<?php echo $aa;?>"><div 
                  class="gks_ekptosi_poso" data-aa="<?php echo $aa;?>" 
                  style="<?php echo ($ekptosi_poso_html=='' ? 'display:none;' : '');?>" 
                  data-net-poso="<?php echo $ekptosi_poso_html;?>"
                  data-netfpa-poso="<?php echo $ekptosi_poso_netfpa_html;?>"  
                  ><?php 
                  if ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA) {
                    if ($eidos['product_price_check_fpa']==1) {
                      echo $ekptosi_poso_netfpa_html;
                    } else {
                      echo $ekptosi_poso_html;
                    }
                  } else {
                    echo $ekptosi_poso_html;
                  }
                  
                  ?></div></div>
              </div>
  <?php if ($GKS_ACC_INV_COL_FPA) {?>
              <div class="<?php echo $gkscols9;?>" >
               <div style="position:relative;">
                  <?php if (is_array($from_aade_import_json)) {?>
                      <i class="fas fa-pencil-alt gks_product_price_final_all_fpa_import_manual_pencil" data-aa="<?php echo $aa;?>"></i>
                  <?php } ?>
                
                 <div class="gks_fpa_div<?php echo ($gks_lock ? '_lock' :'');?> btn btn-primary btn-sm" 
                  data-aa="<?php echo $aa;?>" 
                  data-fpa_base_id="<?php echo $eidos['product_fpa_base_id'];?>"
                  data-fpa_aade_id="<?php echo $eidos['product_fpa_aade_id'];?>"
                  data-val="<?php echo $eidos['product_price_final_all_fpa'];?>"
                  data-import_user_fpa="<?php echo ($from_aade_import_user_fpa ? '1' : '0');?>"
                  >
                    <?php
                  echo $eidos['fpa_descr_print'].
                         ' '.
                         number_format($eidos['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND);
                  ?></div>
                  <?php if (is_array($from_aade_import_json)) {?>
                      <input class="form-control form-control-sm gks_product_price_final_all_fpa_import_manual_number" data-aa="<?php echo $aa;?>" type="number" value="" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>"/>
                  <?php } ?>                  
                  
                </div>             
              </div>
  <?php } ?>            
              
              
              <div class="<?php echo $gkscols8;?>">
                <div class="text-center gks_icons">
                  
                  <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                  <div style="width:25%;float:left;">
                    <i class="fas fa-angle-double-down gks_eidos_details" data-aa="<?php echo $aa;?>" style="<?php if ($GKS_ACC_INV_EXTRA_OPEN) echo 'transform: rotate(180deg);';?>"></i>
                  </div>
                  <div style="width:25%;float:left;">
                    <i class="fas fa-trash-alt gks_delete_eidos" data-aa="<?php echo $aa;?>" style=""></i>
                  </div>
                  <div style="width:25%;float:left;">
                    <i class="fas fa-arrows-alt-v sortorder_handle"></i>
                  </div>
                  <div style="width:25%;float:left;">
                    <i class="fas fa-plus-circle gks_add_eidos"  data-aa="<?php echo $aa;?>"></i>
                  </div>
                  <?php } ?>
  
  
                  
                </div>
              </div>
            
            </div> 
            
            <?php if ($GKS_PRODUCT_LOTS_SERIALS) { ?>
            <div class="form-group row gks_eidos_lots_serials gks_eidos_lots_serials_col1" data-aa="<?php echo $aa;?>" style="padding-top: 4px;<?php if (trim_gks($eidos['product_lot_serial'])=='') echo 'display:none;';?>" data-val-lot-serial="<?php echo trim_gks($eidos['product_lot_serial']);?>">

              <div class="col-12 col-sm-12  col-md-11 col-lg-11 col-xl-11 offset-md-1 offset-lg-1 offset-xl-1 gks_eidos_lots_serials_list" data-aa="<?php echo $aa;?>">
                
                <div class="div_eidos_lots_serials" data-aa="<?php echo $aa;?>" style="<?php echo (1==2 ? 'display:none;' : '');?>"> 
                  <?php
                  
                  $eidos_lots_serials=array();
                  if (isset($products_lots_serials[$eidos['id_acc_inv_product']])) {
                    $eidos_lots_serials=$products_lots_serials[$eidos['id_acc_inv_product']];
                  }
                  ?>
                  <div class="form-group1 row div_add_eidos_lots_serials" data-aa="<?php echo $aa;?>" style="margin: 0px;">
                    <div class="col-8 col-sm-11 col-md-11 col-lg-11 gks_items_col text-center gks_eidos_lots_serials_label div_eidos_lots_serials_title">
                      <?php echo gks_lang('Λίστα');?> 
                      <span class="gks_eidos_lots_serials_span"><?php
                      if (trim_gks($eidos['product_lot_serial'])=='lot') echo gks_lang('Παρτίδων');
                      else if (trim_gks($eidos['product_lot_serial'])=='serial') echo gks_lang('Serial Numbers');
                    ?></span> 
                    </div>
                    <div class="col-4 col-sm-1 col-md-1 col-lg-1 gks_items_col text-center">
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                      <i class="fas fa-plus-circle gks_add_eidos_lots_serials"  data-aa="<?php echo $aa;?>" style="<?php echo (count($eidos_lots_serials) > 0 ? 'display:none;' : '');?>"></i>
                      <?php } ?>
                    </div>
                  </div>
                  <?php
                  
                  $ls=0;
                  $span_eidos_lots_serials_sum_quantity=0;
                  foreach ($eidos_lots_serials as $lot_serial) { 
                    $span_eidos_lots_serials_sum_quantity+=floatval($lot_serial['lot_product_quantity']);
                  }
                    
                  foreach ($eidos_lots_serials as $lot_serial) { 
                    $ls++;
                    ?>
                    
                  <div class="form-group1 row div_gks_eidos_lots_serials" style="margin: 0px;" data-aa="<?php echo $aa;?>" data-ls="<?php echo $ls;?>">
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm">';
                          echo $lot_serial['lot_name'];
                        echo '</div>';
                      } else {?>
                      <input type="text" class="form-control form-control-sm gks_eidos_lots_serials_name" 
                      data-aa="<?php echo $aa;?>" 
                      data-ls="<?php echo $ls;?>" 
                      data-product-id="<?php echo $eidos['product_id'];?>"
                      value="<?php echo $lot_serial['lot_name'];?>"
                      <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?> 
                      placeholder="<?php echo gks_lang('Παρτίδα/Serial Number');?>"
                      ><a href="admin-products-lots-item.php?id=<?php echo $lot_serial['lot_product_id'];?>" class="gks_eidos_lots_serials_zoom"
                        data-aa="<?php echo $aa;?>" data-ls="<?php echo $ls;?>"
                        ><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή Παρτίδας/Serial Number');?>"></i></a>
                      <?php } ?>
                    </div>
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm text-right">';
                          echo myNumberFormatNo0Local($lot_serial['lot_product_quantity'],false);
                        echo '</div>';
                      } else {?>
                      <input style="text-align:right;" type="number" class="form-control form-control-sm gks_eidos_lots_serials_quantity" 
                      data-aa="<?php echo $aa;?>"
                      data-ls="<?php echo $ls;?>" 
                      value="<?php if ($lot_serial['lot_product_quantity']!=0) echo $lot_serial['lot_product_quantity'];?>" 
                      min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>" 
                      <?php if (!$perm_gks_acc_inv_edit or trim_gks($eidos['product_lot_serial'])=='serial') echo 'readonly';?> 
                      placeholder="<?php echo gks_lang('Ποσότητα');?>"
                      ><i class="fas fa-boxes gks_eidos_lots_serials_balance"></i>
                      <?php } ?>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm">';
                          echo $lot_serial['lot_descr'];
                        echo '</div>';
                      } else {?>
                      <textarea class="form-control form-control-sm gks_eidos_lots_serials_descr" rows="1"
                        data-aa="<?php echo $aa;?>" 
                        data-ls="<?php echo $ls;?>"
                        readonly
                        placeholder="<?php echo gks_lang('Περιγραφή');?>"
                        ><?php echo htmlspecialchars_gks($lot_serial['lot_descr']);?></textarea>
                      
                      <?php } ?>
                    </div>

                    <div class="col-4 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm">';
                          if (isset($lot_serial['lot_date_production'])) echo showDate(strtotime($lot_serial['lot_date_production']), 'd/m/Y', 1);
                        echo '</div>';
                      } else {?>
                      <input type="text" class="form-control form-control-sm gks_eidos_lots_serials_date_production" 
                      data-aa="<?php echo $aa;?>" 
                      data-ls="<?php echo $ls;?>" 
                      value="<?php if (isset($lot_serial['lot_date_production'])) echo showDate(strtotime($lot_serial['lot_date_production']), 'd/m/Y', 1);?>"
                      readonly
                      placeholder="<?php echo gks_lang('Ημερ. Παραγωγής');?>">
                      <?php } ?>
                    </div>
                    <div class="col-4 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm">';
                          if (isset($lot_serial['lot_date_expire'])) echo showDate(strtotime($lot_serial['lot_date_expire']), 'd/m/Y', 1);
                        echo '</div>';
                      } else {?>
                      <input type="text" class="form-control form-control-sm gks_eidos_lots_serials_date_expire" 
                      data-aa="<?php echo $aa;?>" 
                      data-ls="<?php echo $ls;?>" 
                      value="<?php if (isset($lot_serial['lot_date_expire'])) echo showDate(strtotime($lot_serial['lot_date_expire']), 'd/m/Y', 1);?>"
                      readonly
                      placeholder="<?php echo gks_lang('Ημερ. Λήξης');?>">
                      <?php } ?>
                    </div>
                                        
                    <div class="col-4 col-sm-2 col-md-2 col-lg-1 col-xl-1 gks_items_col text-center offset-sm-4 offset-md-4 offset-lg-0 ">
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                      <i class="fas fa-trash-alt gks_delete_eidos_lots_serials" data-aa="<?php echo $aa;?>" data-ls="<?php echo $ls;?>" style=""></i>
                      <i class="fas fa-plus-circle gks_add_eidos_lots_serials"  data-aa="<?php echo $aa;?>" style="<?php echo ($ls < count($eidos_lots_serials) ? 'display:none;' : '');?>"></i>
                      <?php } ?>
                    </div>
                  </div>                      
                    
    <?php         }    ?>                
                  <div class="form-group1 row div_eidos_lots_serials_sum_quantity" data-aa="<?php echo $aa;?>" style="margin: 0px;<?php echo (count($eidos_lots_serials) == 0 ? 'display:none;' : '');?>">
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col text-left gks_eidos_lots_serials_label div_eidos_lots_serials_title" style="margin: 0px;">
                      <div class="gks_flock gks_flock_small form-control-sm">
                        <?php echo gks_lang('Άθροισμα');?>:
                      </div>
                    </div>
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 col-xl-2 gks_items_col text-right gks_eidos_lots_serials_label">
                      <div class="gks_flock gks_flock_small form-control-sm">
                        <img src="img/warning.gif" class="img_eidos_lots_serials_sum_quantity" data-aa="<?php echo $aa;?>" style="<?php if (floatval($eidos['product_quantity'])==$span_eidos_lots_serials_sum_quantity) echo 'display:none';?>"/>
                        <span class="span_eidos_lots_serials_sum_quantity <?php
                          if ($gks_lock==false) echo ' span_eidos_lots_serials_sum_quantity_lock ';
                          ?>" data-aa="<?php echo $aa;?>"><?php echo myNumberFormatNo0Local($span_eidos_lots_serials_sum_quantity,false);?></span>
                      </div>
                    </div>
                  </div>
                  
                </div>
                
              </div>
            </div>

            <?php } ?>
            
            <div class="form-group row gks_eidos_extra <?php if ($GKS_ACC_INV_EXTRA_OPEN) echo 'gks_eidos_raddown';?>" data-aa="<?php echo $aa;?>" style="padding-top: 4px;<?php if ($GKS_ACC_INV_EXTRA_OPEN==false) echo 'display:none;';?>">
  
              <div class="col-12 col-sm-15 col-md-6 col-lg-6 gks_eidos_extra_col1" style="padding:0px;">
                
                <div class="div_ejeresi_fpa" data-aa="<?php echo $aa;?>" style="<?php 
                  if ($eidos_parastatikou_has_fpa!=2 and ($eidos_parastatikou_has_fpa==1 and $eidos['product_fpa_base_id']==1004)==false)               
                    echo 'display:none;';
                  
                  ?>"> 
                  <div class="form-group row div_fpa_ejeresi" data-aa="<?php echo $aa;?>" style="margin: 0px;">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3 gks_items_col gks_eidos_extra_label text-left" >
                      <?php echo gks_lang('Αιτία Εξαίρεσης ΦΠΑ');?>:
                    </div>
                    <div class="col-12 col-sm-9 col-md-9 col-lg-9 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo $eidos['aade_katigoria_fpa_ejeresi_descr'];
                        echo '</div>';
                      } else {?>
                      <select data-dbval="<?php echo $eidos['product_fpa_ejeresi_id'];?>" class="gks_fpa_ejeresi_id form-control form-control-sm" data-aa="<?php echo $aa;?>" style="width:100%;">
                      </select>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <div class="div_other_taxes" data-aa="<?php echo $aa;?>" style="<?php echo ($eidos_parastatikou_has_othertaxes=='' ? 'display:none;' : '');?>"> 
                
                  <?php 
                  
                  $ttt=explode(',',$eidos_parastatikou_has_othertaxes);
                  
                  $vals=array();
                  if (in_array('dd',$ttt) and $eidos['product_deductionsAmount']!=0) $vals[]='dd';
                  if (in_array('fe',$ttt) and $eidos['product_feesPercentCategory']!=0) $vals[]='fe';
                  if (in_array('sd',$ttt) and $eidos['product_stampDutyPercentCategory']!=0) $vals[]='sd';
                  if (in_array('ot',$ttt) and $eidos['product_otherTaxesPercentCategory']!=0) $vals[]='ot';
                  if (in_array('wh',$ttt) and $eidos['product_withheldPercentCategory']!=0) $vals[]='wh';
                  
                  
                  if (count($vals)==0 or count($ttt)==0) $mydisplay=''; else $mydisplay='display:none;';
    
                  $mydisplayv1='display:none;';
                  $mydisplayv2='display:none;';
                  $mydisplayv3='display:none;';
                  $mydisplayv4='display:none;';
                  $mydisplayv5='display:none;';
    
                  if (count($vals)==count($ttt)) {
                    //den exei alla
                  } else if (count($vals)>0) {
                    //if ($vals[0]=='wd')print '<pre>'.print_r($vals,true).print_r($ttt,true).'</pre>';
                    if ($vals[0]=='wh') $mydisplayv1='';
                    else if ($vals[0]=='ot') $mydisplayv2='';
                    else if ($vals[0]=='sd') $mydisplayv3='';
                    else if ($vals[0]=='fe') $mydisplayv4='';
                    else if ($vals[0]=='dd') $mydisplayv5='';
                    
                  }
    
                  ?>          
                  <div class="form-group row div_add_feesother" data-aa="<?php echo $aa;?>" style="margin: 0px;">
                    <div class="col-8 col-sm-11 col-md-10 col-lg-11 gks_items_col text-center gks_eidos_extra_label">
                      <?php echo gks_lang('Λοιποί φόροι, τέλη κτλ.');?>
                    </div>
                    <div class="col-4 col-sm-1 col-md-2 col-lg-1 gks_items_col text-center">
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit and $from_aade_import_lock==false) {?>
                      <i class="fas fa-plus-circle gks_add_feesother"  data-aa="<?php echo $aa;?>" style="<?php echo $mydisplay;?>"></i>
                      <?php } ?>
                    </div>
                  </div>              
                  <div class="form-group row div_withheldAmount" data-aa="<?php echo $aa;?>" style="margin: 0px;<?php 
                    echo (($eidos['product_withheldPercentCategory']==0 or in_array('wh',$ttt)==false) ? 'display:none;' : ''); ?>">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3 gks_items_col gks_eidos_extra_label text-left" >
                      <?php echo gks_lang('Φόροι Παρακρατούμενοι');?>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 col-lg-6 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo $eidos['aade_katigoria_parakratoumemenon_foron_descr'];
                        echo '</div>';
                      } else {?>
                      <select data-dbval="<?php echo $eidos['product_withheldPercentCategory'];?>" 
                        class="gks_withheldPercentCategory form-control form-control-sm" 
                        data-aa="<?php echo $aa;?>" style="width:100%;"
                        <?php if ($from_aade_import_lock) echo ' disabled ';?>
                        >
                      </select>
                      <?php } ?>
                    </div>
                    <div class="col-8 col-sm-2 col-md-2 col-lg-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm text-right" style="padding-top: 7px;">';
                          echo myCurrencyFormat($eidos['product_withheldAmount'],false);
                        echo '</div>';
                      } else {?>
                      <input type="number" class="gks_withheldAmount form-control form-control-sm" data-aa="<?php echo $aa;?>" 
                      value="<?php echo number_format($eidos['product_withheldAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                      style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>" <?php 
                      if ($eidos['product_withheldPercentCategory']==0 or $eidos['aade_katigoria_parakratoumemenon_foron_type']!='free' or $from_aade_import_lock) echo 'disabled';
                      ?>>
                      <?php } ?>
                    </div>  
                    <div class="col-4 col-sm-1 col-md-2 col-lg-1 gks_items_col text-center" >
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit and $from_aade_import_lock==false) {?>
                      <i class="fas fa-trash-alt gks_delete_eidos_extra del_withheldAmount" data-aa="<?php echo $aa;?>" style=""></i>
                      <i class="fas fa-plus-circle gks_add_feesother"  data-aa="<?php echo $aa;?>" style="<?php echo $mydisplayv1;?>"></i>
                      <?php } ?>
                    </div>
                  </div> 
                  
                  <div class="form-group row div_otherTaxesAmount" data-aa="<?php echo $aa;?>" style="margin: 0px;<?php 
                    echo (($eidos['product_otherTaxesPercentCategory']==0 or in_array('ot',$ttt)==false) ? 'display:none;' : ''); ?>">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3 gks_items_col gks_eidos_extra_label text-left" >
                     <?php echo gks_lang('Λοιποί Φόροι');?>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 col-lg-6 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo $eidos['aade_katigoria_loipon_foron_descr'];
                        echo '</div>';
                      } else {?>
                      <select data-dbval="<?php echo $eidos['product_otherTaxesPercentCategory'];?>" 
                        class="gks_otherTaxesPercentCategory form-control form-control-sm" 
                        data-aa="<?php echo $aa;?>" style="width:100%;"
                        <?php if ($from_aade_import_lock) echo ' disabled ';?>
                        >
                      </select>
                      <?php } ?>
                    </div>
                    <div class="col-8 col-sm-2 col-md-2 col-lg-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm text-right" style="padding-top: 7px;">';
                          echo myCurrencyFormat($eidos['product_otherTaxesAmount'],false);
                        echo '</div>';
                      } else {?>
                      <input type="number" class="gks_otherTaxesAmount form-control form-control-sm" data-aa="<?php echo $aa;?>" 
                      value="<?php echo number_format($eidos['product_otherTaxesAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                      style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>" <?php 
                      if ($eidos['product_otherTaxesPercentCategory']==0 or $eidos['aade_katigoria_loipon_foron_type']!='free' or $from_aade_import_lock) echo 'disabled';
                      ?>>
                      <?php } ?>
                    </div>  
                    <div class="col-4 col-sm-1 col-md-2 col-lg-1 gks_items_col text-center" >
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit and $from_aade_import_lock==false) {?>
                      <i class="fas fa-trash-alt gks_delete_eidos_extra del_otherTaxesAmount" data-aa="<?php echo $aa;?>" style=""></i>
                      <i class="fas fa-plus-circle gks_add_feesother"  data-aa="<?php echo $aa;?>" style="<?php echo $mydisplayv2;?>"></i>
                      <?php } ?>
                    </div>
                  </div> 
                  
                  <div class="form-group row div_stampDutyAmount" data-aa="<?php echo $aa;?>" style="margin: 0px;<?php 
                    echo (($eidos['product_stampDutyPercentCategory']==0 or in_array('sd',$ttt)==false) ? 'display:none;' : ''); ?>">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3 gks_items_col gks_eidos_extra_label text-left" >
                     <?php echo gks_lang('Ψηφιακό Τέλος συναλλαγής');?>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 col-lg-6 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo $eidos['aade_katigoria_xartosimou_descr'];
                        echo '</div>';
                      } else {?>
                      <select data-dbval="<?php echo $eidos['product_stampDutyPercentCategory'];?>" 
                        class="gks_stampDutyPercentCategory form-control form-control-sm" 
                        data-aa="<?php echo $aa;?>" style="width:100%;"
                        <?php if ($from_aade_import_lock) echo ' disabled ';?>
                        >
                      </select>
                      <?php } ?>
                    </div>
                    
                    <div class="col-8 col-sm-2 col-md-2 col-lg-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm text-right" style="padding-top: 7px;">';
                          echo myCurrencyFormat($eidos['product_stampDutyAmount'],false);
                        echo '</div>';
                      } else {?>
                      <input type="number" class="gks_stampDutyAmount form-control form-control-sm" data-aa="<?php echo $aa;?>" 
                      value="<?php echo number_format($eidos['product_stampDutyAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                      style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>" <?php
                      if ($eidos['product_stampDutyPercentCategory']==0 or $eidos['aade_katigoria_xartosimou_type']!='free' or $from_aade_import_lock) echo 'disabled';
                      ?>>
                      <?php } ?>
                    </div>  
                    <div class="col-4 col-sm-1 col-md-2 col-lg-1 gks_items_col text-center" >
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit and $from_aade_import_lock==false) {?>
                      <i class="fas fa-trash-alt gks_delete_eidos_extra del_stampDutyAmount" data-aa="<?php echo $aa;?>" style=""></i>
                      <i class="fas fa-plus-circle gks_add_feesother"  data-aa="<?php echo $aa;?>" style="<?php echo $mydisplayv3;?>"></i>
                      <?php } ?>
                    </div>
                  </div> 
                  
                  <div class="form-group row div_feesAmount" data-aa="<?php echo $aa;?>" style="margin: 0px;<?php 
                    echo (($eidos['product_feesPercentCategory']==0 or in_array('fe',$ttt)==false) ? 'display:none;' : ''); ?>">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3 gks_items_col gks_eidos_extra_label text-left" >
                     <?php echo gks_lang('Τέλη');?>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 col-lg-6 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo $eidos['aade_katigoria_telon_descr'];
                        echo '</div>';
                      } else {?>
                      <select data-dbval="<?php echo $eidos['product_feesPercentCategory'];?>" 
                        class="gks_feesPercentCategory form-control form-control-sm" 
                        data-aa="<?php echo $aa;?>" style="width:100%;"
                        <?php if ($from_aade_import_lock) echo ' disabled ';?>
                        >
                      </select>
                      <?php } ?>
                    </div>
                    <div class="col-8 col-sm-2 col-md-2 col-lg-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm text-right" style="padding-top: 7px;">';
                          echo myCurrencyFormat($eidos['product_feesAmount'],false);
                        echo '</div>';
                      } else {?>
                      <input type="number" class="gks_feesAmount form-control form-control-sm" data-aa="<?php echo $aa;?>" 
                      value="<?php echo number_format($eidos['product_feesAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                      style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>" <?php 
                      if ($eidos['product_feesPercentCategory']==0 or $eidos['aade_katigoria_telon_type']!='free' or $from_aade_import_lock) echo 'disabled';
                      ?>>
                      <?php } ?>
                    </div>
                    <div class="col-4 col-sm-1 col-md-2 col-lg-1 gks_items_col text-center" >
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit and $from_aade_import_lock==false) {?>
                      <i class="fas fa-trash-alt gks_delete_eidos_extra del_feesAmount" data-aa="<?php echo $aa;?>" style=""></i>
                      <i class="fas fa-plus-circle gks_add_feesother"  data-aa="<?php echo $aa;?>" style="<?php echo $mydisplayv4;?>"></i>
                      <?php } ?>
                    </div>
                  </div>
    
                  <div class="form-group row div_deductionsAmount" data-aa="<?php echo $aa;?>" style="margin: 0px;<?php 
                    echo (($eidos['product_deductionsAmount']==0 or in_array('dd',$ttt)==false) ? 'display:none;' : ''); ?>">
                    <div class="col-12 col-sm-3 col-md-3 col-lg-3 gks_items_col gks_eidos_extra_label text-left" >
                     <?php echo gks_lang('Κρατήσεις','part2');?>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 col-lg-6 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo implode(', ',explode(']][[',$eidos['product_deductionsSelection']));
                        echo '</div>';                        
                      } else {?>
                      <input type="text" class="gks_deductionsSelection form-control form-control-sm" data-aa="<?php echo $aa;?>" 
                      value="<?php echo $eidos['product_deductionsSelection'];?>" 
                      style="text-align:left;"
                      <?php if ($from_aade_import_lock) echo ' disabled ';?>
                      >                        
                      <?php } ?>    
                    </div>
                    <div class="col-8 col-sm-2 col-md-2 col-lg-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm text-right" style="padding-top: 7px;">';
                          echo myCurrencyFormat($eidos['product_deductionsAmount'],false);
                        echo '</div>';
                      } else {?>
                      <input type="number" class="gks_deductionsAmount form-control form-control-sm" data-aa="<?php echo $aa;?>" 
                      value="<?php echo number_format($eidos['product_deductionsAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                      style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
                      <?php if ($from_aade_import_lock) echo ' disabled ';?>
                      >
                      <?php } ?>
                    </div>  
                    <div class="col-4 col-sm-1 col-md-2 col-lg-1 gks_items_col text-center">
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit and $from_aade_import_lock==false) {?>
                      <i class="fas fa-trash-alt gks_delete_eidos_extra del_deductionsAmount" data-aa="<?php echo $aa;?>" style=""></i>
                      <i class="fas fa-plus-circle gks_add_feesother"  data-aa="<?php echo $aa;?>" style="<?php echo $mydisplayv5;?>"></i>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
                
              <div class="col-12 col-sm-12 col-md-6 col-lg-6" style="padding: 0px;">
  <?php
                $out_xarakt_esoda=array();
                if (isset($products_income[$eidos['id_acc_inv_product']])) {
                  $out_xarakt_esoda=$products_income[$eidos['id_acc_inv_product']];
                } 
                $out_xarakt_eksoda=array();
                if (isset($products_expenses[$eidos['id_acc_inv_product']])) {
                  $out_xarakt_eksoda=$products_expenses[$eidos['id_acc_inv_product']];
                }
  ?>
  
  
                <div class="div_xarakt_esoda" data-aa="<?php echo $aa;?>" style="<?php echo ($eidos_parastatikou_has_esoda==0 ? 'display:none;' : '');?>"> 
                  <div class="form-group row div_add_xarakt_esoda" data-aa="<?php echo $aa;?>" style="margin: 0px;">
                    <div class="col-8 col-sm-11 col-md-10 col-lg-11 gks_items_col text-center gks_eidos_extra_label div_gks_xarakt_esoda_title">
                      <?php echo gks_lang('Χαρακτηρισμός Εσόδων');?>
                    </div>
                    <div class="col-4 col-sm-1 col-md-2 col-lg-1 gks_items_col text-center">
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                      <i class="fas fa-plus-circle gks_add_xarakt_esoda"  data-aa="<?php echo $aa;?>" style="<?php echo (count($out_xarakt_esoda) > 0 ? 'display:none;' : '');?>"></i>
                      <?php } ?>
                    </div>
                  </div>
    <?php
                  $xx=0;
                  $span_sum_xarakt=0;
                  foreach ($out_xarakt_esoda as $xarakt) { 
                    $span_sum_xarakt+=$xarakt['ammount'];
                  }
                  
                  foreach ($out_xarakt_esoda as $xarakt) { 
                    $xx++;
                    $pososto=0;
                    if ($span_sum_xarakt!=0) $pososto=round(100*$xarakt['ammount']/$span_sum_xarakt);
                    ?>
                  <div class="form-group row div_gks_xarakt_esoda" style="margin: 0px;" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>">
                    <div class="col-12 col-sm-3 col-md-5 col-lg-3 col-xl-3 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo $xarakt['aade_katigoria_xarakt_esodon_descr'];
                        echo '</div>';
                      } else {?>
                      <select data-dbval="<?php echo $xarakt['cat_id'];?>" class="gks_xarakt_esoda_cat_id form-control form-control-sm" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>" style="width:100%;">
                      </select>
                      <?php } ?>
                    </div>
                    <div class="col-12 col-sm-4 col-md-7 col-lg-5 col-xl-5 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo $xarakt['aade_typos_xarakt_esodon_descr'];
                        echo '</div>';
                      } else {?>
                      <select data-dbval="<?php echo $xarakt['typos_id'];?>" class="gks_xarakt_esoda_typos_id form-control form-control-sm" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>" style="width:100%;">
                      </select>
                      <?php } ?>
                    </div>
                    <div class="col-8 col-sm-3 col-md-5 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm text-right" style="padding-top: 7px;">';
                          echo myCurrencyFormat($xarakt['ammount'],false);
                        echo '</div>';
                      } else {?>
                      <input type="number" class="gks_xarakt_esoda_ammount form-control form-control-sm" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>"
                      value="<?php echo number_format($xarakt['ammount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                      style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>" pososto="<?php echo number_format($pososto,4,'.','');?>">
                      <?php } ?>
                    </div>
                    <div class="col-4 col-sm-2 col-md-3 col-lg-2 col-xl-2 gks_items_col text-center offset-md-4 offset-lg-0 offset-xl-0">
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                      <i class="fas fa-clone gks_clone_eidos_xarakt_esoda" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>" style=""></i>
                      <i class="fas fa-trash-alt gks_delete_eidos_xarakt_esoda" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>" style=""></i>
                      <i class="fas fa-plus-circle gks_add_xarakt_esoda"  data-aa="<?php echo $aa;?>" style="<?php echo ($xx < count($out_xarakt_esoda) ? 'display:none;' : '');?>"></i>
                      <?php } ?>
                    </div>
                  </div>
    <?php         }    ?>                
                  <div class="form-group row div_sum_xarakt_esoda" data-aa="<?php echo $aa;?>" style="margin: 0px;<?php echo (count($out_xarakt_esoda) == 0 ? 'display:none;' : '');?>">
                    <div class="col-4 col-sm-7 col-md-5 col-lg-8 col-xl-8 gks_items_col text-right gks_eidos_extra_label div_gks_xarakt_esoda_title" style="padding-right: 28px;">
                      <?php echo gks_lang('Χαρακτηρισμένη αξία');?>:
                    </div>
                    <div class="col-4 col-sm-3 col-md-5 col-lg-2 col-xl-2 gks_items_col text-right gks_eidos_extra_label" style="padding-right: 16px;">
                      <span class="span_sum_xarakt_esoda" data-aa="<?php echo $aa;?>"><?php echo myCurrencyFormat($span_sum_xarakt,false);?></span>
                    </div>
                  </div>
                </div>
          
                <div class="div_xarakt_seperator" data-aa="<?php echo $aa;?>" style="padding: 10px;<?php echo (($eidos_parastatikou_has_esoda!=0 and $eidos_parastatikou_has_eksoda!=0) ? '' : 'display:none;');?>"> 
                  <div style="width:90%;border-top:1px solid #aaaaaa;margin:auto;"></div>
                </div>
                
                <div class="div_xarakt_eksoda" data-aa="<?php echo $aa;?>" style="<?php echo ($eidos_parastatikou_has_eksoda==0 ? 'display:none;' : '');?>"> 
                  <div class="form-group row div_add_xarakt_eksoda" data-aa="<?php echo $aa;?>" style="margin: 0px;">
                    <div class="col-8 col-sm-11 col-md-10 col-lg-11 gks_items_col text-center gks_eidos_extra_label div_gks_xarakt_eksoda_title">
                      <?php echo gks_lang('Χαρακτηρισμός Εξόδων');?>
                    </div>
                    <div class="col-4 col-sm-1 col-md-2 col-lg-1 gks_items_col text-center">
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                      <i class="fas fa-plus-circle gks_add_xarakt_eksoda"  data-aa="<?php echo $aa;?>" style="<?php echo (count($out_xarakt_eksoda) > 0 ? 'display:none;' : '');?>"></i>
                      <?php } ?>
                    </div>
                  </div>
    <?php
                  $xx=0;
                  $span_sum_xarakt=0;
                  $span_sum_xarakt200=0;
                  foreach ($out_xarakt_eksoda as $xarakt) { 
                    if ($xarakt['cat_id']==200) {
                      $span_sum_xarakt200+=$xarakt['ammount'];
                    } else {
                      $span_sum_xarakt+=$xarakt['ammount'];
                    }
                  }
                  //print '<pre>';print_r($out_xarakt_eksoda);die();
                  foreach ($out_xarakt_eksoda as $xarakt) { 
                    $xx++;
                    $pososto=0;
                    if ($xarakt['cat_id']==200) {
                      if ($span_sum_xarakt200!=0) $pososto=round(100*$xarakt['ammount']/$span_sum_xarakt200);
                    } else {
                      if ($span_sum_xarakt!=0) $pososto=round(100*$xarakt['ammount']/$span_sum_xarakt);                      
                    }
                    ?>
                  <div class="form-group row div_gks_xarakt_eksoda" style="margin: 0px;" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>">
                    <div class="col-12 col-sm-3 col-md-5 col-lg-3 col-xl-3 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo $xarakt['aade_katigoria_xarakt_eksodon_descr'];
                        echo '</div>';
                      } else {?>
                      <select data-dbval="<?php echo $xarakt['cat_id'];?>" class="gks_xarakt_eksoda_cat_id form-control form-control-sm" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>" style="width:100%;">
                      </select>
                      <?php } ?>
                    </div>
                    <div class="col-12 col-sm-4 col-md-7 col-lg-5 col-xl-5 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm" style="padding-top: 7px;">';
                          echo $xarakt['aade_typos_xarakt_eksodon_descr'];
                        echo '</div>';
                      } else {?>
                      <select data-dbval="<?php echo $xarakt['typos_id'];?>" class="gks_xarakt_eksoda_typos_id form-control form-control-sm" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>" style="width:100%;">
                      </select>
                      <?php } ?>
                    </div>
                    <div class="col-8 col-sm-3 col-md-5 col-lg-2 col-xl-2 gks_items_col" >
                      <?php if ($gks_lock) {
                        echo '<div class="gks_flock gks_flock_small form-control-sm text-right" style="padding-top: 7px;">';
                          echo myCurrencyFormat($xarakt['ammount'],false);
                        echo '</div>';
                      } else {?>
                      <input type="number" class="gks_xarakt_eksoda_ammount form-control form-control-sm" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>"
                      value="<?php echo number_format($xarakt['ammount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
                      style="text-align:right;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>" pososto="<?php echo number_format($pososto,4,'.','');?>">
                      <?php } ?>
                    </div>
                    <div class="col-4 col-sm-2 col-md-3 col-lg-2 col-xl-2 gks_items_col text-center offset-md-4 offset-lg-0 offset-xl-0">
                      <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                      <i class="fas fa-clone gks_clone_eidos_xarakt_eksoda" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>" style=""></i>
                      <i class="fas fa-trash-alt gks_delete_eidos_xarakt_eksoda" data-aa="<?php echo $aa;?>" data-xx="<?php echo $xx;?>" style=""></i>
                      <i class="fas fa-plus-circle gks_add_xarakt_eksoda"  data-aa="<?php echo $aa;?>" style="<?php echo ($xx < count($out_xarakt_eksoda) ? 'display:none;' : '');?>"></i>
                      <?php } ?>
                    </div>
                  </div>
    <?php         }    ?>                           
                  <div class="form-group row div_sum_xarakt_eksoda" data-aa="<?php echo $aa;?>" style="margin: 0px;<?php echo (count($out_xarakt_eksoda) == 0 ? 'display:none;' : '');?>">
                    <div class="col-4 col-sm-7 col-md-5 col-lg-8 col-xl-8 gks_items_col text-right gks_eidos_extra_label div_gks_xarakt_eksoda_title">
                      <?php echo gks_lang('Χαρακτηρισμένη αξία');?>:
                    </div>
                    <div class="col-4 col-sm-3 col-md-5 col-lg-2 col-xl-2 gks_items_col text-right gks_eidos_extra_label">
                      <span class="span_sum_xarakt_eksoda" data-aa="<?php echo $aa;?>"><?php echo myCurrencyFormat($span_sum_xarakt+$span_sum_xarakt200,false);?></span>
                    </div>
                  </div>                       
                </div>
                             
              </div>  
            </div>
            
          </div>
<?php } ?>


          <div class="row" id="eidi_footer1">
            <div class="col-sm-4">
              <div class="form-group row total_row">
                <div class="col-12 table-dark1 gks_eidos_label text-left">
                  <button type="button" class="btn btn-sm btn-primary" id="dialog_add_product_variables_button"><?php echo gks_lang('Προσθήκη παραλλαγών είδους');?></button>
                </div>
              </div>
            </div>
            <div class="col-sm-4">              
              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Γραμμές');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_count"  style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php echo $products_count;?></div>
                </div>
              </div> 
              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Ποσότητα');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_posotita" data-val="<?php echo myNumberFormatNo0($products_posotita);?>" style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php echo myNumberFormatNo0Local($products_posotita);?></div>
                </div>
              </div>
              

              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Όγκος');?> (cm<sup>3</sup>):
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_ogos"  style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php
                    if ($products_ogos_max_x>0 or $products_ogos_max_y>0 or $products_ogos_max_z>0) {
                      echo number_format($products_ogos_max_x, 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                           number_format($products_ogos_max_y, 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                           number_format($products_ogos_max_z, 0, '', $GKS_NUMBER_FORMAT_THOUSAND);
                    }
                    ?></div>
                </div>
              </div>
              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Βάρος');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_varos" style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php if ($products_varos>0 or 1==1) echo number_format($products_varos, 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'gr';?></div>
                </div>
              </div>              
            </div>

            <div class="col-sm-4">
              <div class="form-group row total_row" id="tr_gks_total_price_net">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Υποσύνολο');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="gks_total_price_net" data-val="<?php echo number_format($row['gks_price_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_net']);?></span>
                  <?php if (is_array($from_aade_import_json)) {?>
                    <img src="img/warning.gif" class="from_aade_import_json_warning_img2" data-id="gks_price_net"/>
                  <?php } ?>
                </div>
              </div>
             
              <div class="form-group row total_row" id="tr_gks_total_price_fpa" style="<?php if ($row['gks_price_fpa']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('ΦΠΑ');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="gks_total_price_fpa" data-val="<?php echo number_format($row['gks_price_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_fpa']);?></span>
                  <?php if (is_array($from_aade_import_json)) {?>
                    <img src="img/warning.gif" class="from_aade_import_json_warning_img2" data-id="gks_price_fpa"/>
                  <?php } ?>
                </div>
              </div>


              <div class="form-group row total_row" id="tr_gks_total_price_netfpa" style="<?php if ($row['gks_price_netfpa']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Μικτό σύνολο');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="gks_total_price_netfpa" data-val="<?php echo number_format($row['gks_price_netfpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_netfpa']);?></span>
                  <?php if (is_array($from_aade_import_json)) {?>
                    <img src="img/warning.gif" class="from_aade_import_json_warning_img2" data-id="gks_price_netfpa"/>
                  <?php } ?>
                </div>
              </div>
              
              
              
              <div class="form-group row total_row" id="tr_totalWithheldAmount" style="<?php if ($row['totalWithheldAmount']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Φόροι Παρακρατούμενοι');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="totalWithheldAmount" data-val="<?php echo number_format($row['totalWithheldAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalWithheldAmount']);?></span>
                  <?php if (is_array($from_aade_import_json)) {?>
                    <img src="img/warning.gif" class="from_aade_import_json_warning_img2" data-id="totalWithheldAmount"/>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row total_row" id="tr_totalOtherTaxesAmount" style="<?php if ($row['totalOtherTaxesAmount']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Λοιποί Φόροι');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="totalOtherTaxesAmount" data-val="<?php echo number_format($row['totalOtherTaxesAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalOtherTaxesAmount']);?></span>
                  <?php if (is_array($from_aade_import_json)) {?>
                    <img src="img/warning.gif" class="from_aade_import_json_warning_img2" data-id="totalOtherTaxesAmount"/>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row total_row" id="tr_totalStampDutyamount" style="<?php if ($row['totalStampDutyamount']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Ψηφιακό Τέλος συναλλαγής');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="totalStampDutyamount" data-val="<?php echo number_format($row['totalStampDutyamount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalStampDutyamount']);?></span>
                  <?php if (is_array($from_aade_import_json)) {?>
                    <img src="img/warning.gif" class="from_aade_import_json_warning_img2" data-id="totalStampDutyamount"/>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row total_row" id="tr_totalFeesAmount" style="<?php if ($row['totalFeesAmount']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Τέλη');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="totalFeesAmount" data-val="<?php echo number_format($row['totalFeesAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalFeesAmount']);?></span>
                  <?php if (is_array($from_aade_import_json)) {?>
                    <img src="img/warning.gif" class="from_aade_import_json_warning_img2" data-id="totalFeesAmount"/>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row total_row" id="tr_totalDeductionsAmount" style="<?php if ($row['totalDeductionsAmount']==0) echo 'display:none;'?>">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Κρατήσεις','part2');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="totalDeductionsAmount" data-val="<?php echo number_format($row['totalDeductionsAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalDeductionsAmount']);?></span>
                  <?php if (is_array($from_aade_import_json)) {?>
                    <img src="img/warning.gif" class="from_aade_import_json_warning_img2" data-id="totalDeductionsAmount"/>
                  <?php } ?>
                </div>
              </div>
              
              <div class="form-group row total_row" id="tr_gks_total_price_total">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Σύνολο');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="gks_total_price_total" data-val="<?php echo number_format($row['gks_price_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_total']);?></span>
                  <?php if (is_array($from_aade_import_json)) {?>
                    <img src="img/warning.gif" class="from_aade_import_json_warning_img2" data-id="gks_price_total"/>
                  <?php } ?>
                </div>
              </div>              
              <div class="form-group row total_row" id="tr_kostos_apostolis">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Κόστος αποστολής');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <input type="number" id="kostos_apostolis" class="form-control form-control-sm" value="<?php echo number_format($row['kostos_apostolis'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="text-align:right;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
                </div>
              </div>
              <div class="form-group row total_row" id="tr_kostos_pliromis">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Κόστος πληρωμής');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <input type="number" id="kostos_pliromis" class="form-control form-control-sm" value="<?php echo number_format($row['kostos_pliromis'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="text-align:right;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
                </div>
              </div>
              <div class="form-group row total_row" id="tr_pliroteo">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Πληρωτέο');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4 text-right">
                  <span id="gks_pliroteo"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($pliroteo);?></span>
                </div>
              </div>
              
            </div>
        
          </div>
        
        
                    
          <?php if ($credit_memo_for_acc_inv_id!=0) { ?>  
          <div class="row" style="margin-top: 10px;padding-top: 10px;border-top: 1px solid lightgray;">
            <div class="col-sm-12 gks_eidos_label">
              <?php 
              $message=gks_lang('Το συσχετιζόμενο παραστατικό στο οποίο αναφέρεται το τρέχον παραστατικό έχει καθαρή αξία <b>[1]</b>και ΦΠΑ <b>[2]</b>');
              $message=str_replace('[1]',myCurrencyFormat($org_gks_price_net),$message);
              $message=str_replace('[2]',myCurrencyFormat($org_gks_price_fpa),$message);
              echo $message;?>
            </div>
            <div class="col-sm-12 gks_eidos_label">
              <?php
              $message=gks_lang('Όλα τα συσχετιζόμενα πιστωτικά παραστατικά, εκτός από το τρέχον, είναι <b>[1]</b> και έχουν άθροισμα καθαρής αξίας <b>[2]</b> και ΦΠΑ <b>[3]</b>');
              $message=str_replace('[1]',$others_count,$message);
              $message=str_replace('[2]',myCurrencyFormat($others_gks_price_net_sum),$message);
              $message=str_replace('[3]',myCurrencyFormat($others_gks_price_fpa_sum),$message);
              echo $message;?>              
            </div>
            <div class="col-sm-12 gks_eidos_label">
              <?php echo gks_lang('Άρα το τρέχον παραστατικό θα μπορεί να έχει ως μέγιστη καθαρή αξία');?>
              <b><span id="rest_gks_price_net_sum" data-val="<?php echo number_format($rest_gks_price_net_sum,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding: 0px 6px;border-radius: 10px;"><?php echo myCurrencyFormat($rest_gks_price_net_sum);?></span></b>
              <?php echo gks_lang('και μέγιστο ΦΠΑ');?> 
              <b><span id="rest_gks_price_fpa_sum" data-val="<?php echo number_format($rest_gks_price_fpa_sum,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding: 0px 6px;border-radius: 10px;"><?php echo myCurrencyFormat($rest_gks_price_fpa_sum);?></span></b>
            </div>
          </div>
          <?php } ?>
                              
        </div>
      </div>
    </div>
  </div>
</div>




<div class="container-fluid " style="padding-top:0px" id="mypostform">
  <div class="row">
    <div class="col-md-6">

      <div class="card gks_card_expand" id="div_packings_declarations" style="<?php if ($journal_has_packings_declarations==0) echo 'display:none';?>">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πληροφορίες Συσκευασίας Διακίνησης');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('packaging');?> id="pdeitem_packings_declarations_table"> 
          <div class="form-group row gks_packings_declarations_header">
            <div class="col-4 gks_packings_declarations_col">
              <div class="table-dark gks_packings_declarations_label"><?php echo gks_lang('Είδος Συσκευασίας');?></div>
            </div>
            <div class="col-3 gks_packings_declarations_col">
              <div class="table-dark gks_packings_declarations_label"><?php echo gks_lang('Πλήθος');?></div>
            </div>            
            <div class="col-3 gks_packings_declarations_col">
              <div class="table-dark gks_packings_declarations_label"><?php echo gks_lang('Τίτλος για Λοιπά');?></div>
            </div>            
            <div class="col-2 gks_packings_declarations_col">
              <div class="table-dark gks_packings_declarations_label">
                <i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i>  
              </div>
            </div>
          </div>
          <?php
          $pde_aa=0;
          foreach ($packings_declarations_array as $pde_item) {
          $pde_aa++;  
            
          ?>
          <div class="form-group row gks_packings_declarations_item align-items-center" data-pdeaa="<?php echo $pde_aa;?>" data-recid="<?php echo $pde_item['id_acc_inv_packings_declarations'];?>">

            <div class="col-4">
              <?php if ($gks_lock) {
                echo '<div class="pde_packaging_type_id gks_flock gks_flock_small form-control-sm">';
                  echo getAADE_PackagingTypeDescr($pde_item['packaging_type_id']);
                echo '</div>';                
              } else { ?>
              <select data-pdeaa="<?php echo $pde_aa;?>" 
                class="pde_packaging_type_id form-control form-control-sm myneedsave">
                <option value="0"></option>
              <?php foreach($packagingTypes as $pitem) {
                echo '<option value="'.$pitem['id'].'"'.
                ($pitem['id']==$pde_item['packaging_type_id'] ? 'selected' : '').
                '>'.
                $pitem['descr'].
                '</option>';
              }?>
              </select>
              <?php } ?>
            </div>
            <div class="col-3">
              <?php if ($gks_lock) {
                echo '<div class="pde_packaging_quantity gks_flock gks_flock_small form-control-sm">';
                  echo $pde_item['packaging_quantity'];
                echo '</div>';                
              } else { ?>
                <input data-pdeaa="<?php echo $pde_aa;?>" type="number" min="0" 
                class="pde_packaging_quantity form-control form-control-sm myneedsave" 
                value="<?php echo $pde_item['packaging_quantity'];?>"
                />
              <?php } ?>
            </div>
            <div class="col-3">
              <?php if ($gks_lock) {
                echo '<div class="pde_packaging_type_6_descr gks_flock gks_flock_small form-control-sm">';
                  if ($pde_item['packaging_type_id']==6) echo $pde_item['packaging_type_6_descr'];
                echo '</div>';                
              } else { ?>
                <input data-pdeaa="<?php echo $pde_aa;?>" type="text" 
                class="pde_packaging_type_6_descr form-control form-control-sm myneedsave" 
                value="<?php echo $pde_item['packaging_type_6_descr'];?>"
                style="<?php echo ($pde_item['packaging_type_id']==6 ? '' : 'display:none;');?>"
                />
              <?php } ?>
            </div>
            <div class="col-2">
              <div class="text-center gks_icons">
                <?php if ($gks_lock==false and $perm_gks_acc_inv_edit) {?>
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_packings_declarations_delete" data-pdeaa="<?php echo $pde_aa;?>"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_handle"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_packings_declarations_add" data-pdeaa="<?php echo $pde_aa;?>"></i>
                </div>
                <?php } ?>
              </div>
            </div>            
          </div>          
          <?php
          }
          ?>
          <div id="div_packings_declarations_footer"></div>          
                                  
          
        </div>
      </div>

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποστολή - Πληρωμή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('delivery');?>> 


          <div class="row">
            <div class="col-lg-12 col-xl-6" style="margin-bottom:24px;">
              <div style="font-size1:16pt;"><?php echo gks_lang('Τρόποι αποστολής');?>:</div>
              <?php

                $div_delivery_number_style ='display:none';

                foreach ($mybasketarray['tropoi_apostolis_all'] as $row_apostoli) {
                  if ($row['tropos_apostolis'] == $row_apostoli['id_delivery_method']) {
                    if ($row_apostoli['delivery_method_type']=='delivery' or 
                        $row_apostoli['delivery_method_type']=='pelatis' or 
                        $row_apostoli['delivery_method_type']=='post') $div_delivery_number_style='';
                  }
              ?>
              <div style="white-space: nowrap1;<?php echo (($row_apostoli['myisok'] or $row['tropos_apostolis'] == $row_apostoli['id_delivery_method']) ? '' : 'display:none;');?>">
                <input class="myneedsave" type="radio" name="radio_delivery_way" value="<?php echo $row_apostoli['id_delivery_method'];?>" id="radio_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>"  
                data-type="<?php echo $row_apostoli['delivery_method_type'];?>" data-type-o="<?php echo $row_apostoli['delivery_method_type_pa'];?>"
                data-sxolio="<?php echo base64_encode($row_apostoli['delivery_method_sxolio']);?>"
                <?php if ($row['tropos_apostolis'] == $row_apostoli['id_delivery_method']) echo ' checked ';?>
                <?php if (!$perm_gks_acc_inv_edit) echo 'disabled';?>> 
                <label for="radio_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>" style="cursor: pointer;" class="tooltipsterfast delivery_payment_label" title="<?php echo $row_apostoli['delivery_method_tooltip'];?>"><?php echo $row_apostoli['delivery_method_name'];?>
                  <?php if ($row_apostoli['delivery_method_fees_enabled']!=0) {?>
                    <span class="delivery_payment_price" id="price_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>" ><?php echo myCurrencyFormat($row_apostoli['dm_calc_kostos'],true,true);?></span>
                  <?php } ?>
                </label>
                <?php
                if ($row_apostoli['id_delivery_method'] == 8) { ?>
                  <span id="span_delivery_id_8" style="<?php echo ($row['tropos_apostolis']==8 ? '' : 'display:none;');?>">
                  <br>
                  <select id="delivery_id_8" name="delivery_id_8" style="width11:90%;" class="form-control form-control-sm myneedsave" <?php if (!$perm_gks_acc_inv_edit) echo 'disabled';?>>
                      <option value="0"><?php echo gks_lang('Επιλέξτε κατάστημα');?></option>
                      
                      <?php
                      $sql="SELECT id_warehouse,warehouse_name FROM gks_warehouses
                        WHERE warehouse_disable=0 and is_virtual = 0 and warehouse_can_pelatis_paralavei<>0
                        ORDER BY gks_warehouses.warehouse_sortorder, gks_warehouses.warehouse_name";
                      $result_select = $db_link->query($sql);        
                      if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error2');}
                      while ($row_select = $result_select->fetch_assoc()) {
                        echo '<option value="'.$row_select['id_warehouse'].'" ';
                        if ($row_select['id_warehouse']==$row['delivery_id_8']) echo ' selected ';
                        echo '>'.$row_select['warehouse_name'].'</option>';
                      }
                      ?>
                      
                  </select>
                  </span>

                <?php } ?>
              </div>
              <?php } ?>               
              
              <div id="delivery_method_sxolio" class="form-text text-muted" style="font-size:80%"></div>

                                
            </div>

            
            <div class="col-lg-12 col-xl-6" style="margin-bottom:24px;">
              <div style="font-size1:16pt;"><?php echo gks_lang('Τρόποι πληρωμής');?>:</div>
                <div class="div_radio_payment_type_one_multi">
                  <span style="white-space: nowrap;"><input type="radio" name="radio_payment_type_one_multi" value="0" id="radio_payment_type_one"   <?php 
                    if ($tropos_pliromis_one_multi==0) echo ' checked'; 
                    if ($lock_selector_one_multi) echo ' disabled';
                    ?>> <label class="gks_label" for="radio_payment_type_one"   style="display:inline;padding-right:18px"><?php echo gks_lang('Ένας τρόπος');?></label></span> 
                  
                  <span style="white-space: nowrap;"><input type="radio" name="radio_payment_type_one_multi" value="1" id="radio_payment_type_multi" <?php 
                    if ($tropos_pliromis_one_multi==1) echo ' checked';
                    if ($lock_selector_one_multi) echo ' disabled';
                    ?>> <label class="gks_label" for="radio_payment_type_multi" style="display:inline"><?php echo gks_lang('Περισσότεροι τρόποι');?></label></span>
                </div>
                
                <div id="div_payment_type_one" style="<?php if ($tropos_pliromis_one_multi!=0) echo 'display:none;';?>">
                  
                  <?php
    
                    //print '<pre>'; print_r($mybasketarray['tropoi_pliromis_all']); print '</pre>';
                    
                    foreach ($mybasketarray['tropoi_pliromis_all'] as $row_pliromi) {
                      
                  ?>
                  <div style="white-space: nowrap1;<?php echo (($row_pliromi['myisok'] or $row['tropos_pliromis'] == $row_pliromi['id_payment_acquirer']) ? '' : 'display:none;');?>">
                    <input class="myneedsave" type="radio" name="radio_payment_way" value="<?php echo $row_pliromi['id_payment_acquirer'];?>" id="radio_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" 
                    data-type="<?php echo $row_pliromi['payment_acquirer_type'];?>" data-type-o="<?php echo $row_pliromi['payment_acquirer_type_dm'];?>" 
                    data-sxolio="<?php echo base64_encode($row_pliromi['payment_acquirer_sxolio']);?>"
                    data-button-html="<?php echo base64_encode($row_pliromi['payment_acquirer_button_html']);?>"
                    data-aade_id="<?php echo $row_pliromi['aade_tropos_pliromis_id'];?>"
                    data-payment_acquirer_with_id="<?php echo $row_pliromi['payment_acquirer_with_id'];?>"
                    
                    <?php if ($row['tropos_pliromis'] == $row_pliromi['id_payment_acquirer']) echo ' checked ';?>
                    <?php if ($perm_gks_acc_inv_edit==false or $payments_lock_level>=3) echo 'disabled';
                    
                    ?>> 
                    <label for="radio_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" style="cursor: pointer;" class="tooltipsterfast delivery_payment_label" title="<?php echo $row_pliromi['payment_acquirer_tooltip'];?>">
                      <span class="gks_span_text">
                      <?php echo $row_pliromi['payment_acquirer_name'];?>
                      </span>
                      <?php if ($row_pliromi['payment_acquirer_fees_enabled']!=0 and $row_pliromi['payment_acquirer_type']!='none') {?>
                        <span class="delivery_payment_price" id="price_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" ><?php echo myCurrencyFormat($row_pliromi['pa_calc_kostos'],true,true);?></span>
                      <?php } ?>
                    </label>
                    <?php if ($row_pliromi['payment_acquirer_with_id']>0) { ?>
                    <div class="div_payment_one_terminal" style="<?php if ($row['tropos_pliromis']!=$row_pliromi['id_payment_acquirer']) echo 'display:none;';?>" data-one_pway="<?php echo $row_pliromi['id_payment_acquirer'];?>">
                      <?php
                      //echo '<pre>';print_r($row_pliromi);echo '</pre>';
                      $found_this_transaction_id=-1;
                      foreach ($payment_type_multi as $pmvalue) {
                        if ($pmvalue['payment_acquirer_id']==$row_pliromi['id_payment_acquirer'] and
                            $pmvalue['transaction_id']>0) {
                          $found_this_transaction_id = intval($pmvalue['transaction_id']);  
                          break;
                        }
                      }
                      if ($found_this_transaction_id>0) {
                        echo '<div class="div_payment_type_multi_item_row2_text">';
                        $ret=gks_eftpos_get_transaction_html(['id_eftpos_transaction'=> $found_this_transaction_id]);
                        //echo '<pre>';print_r($ret);echo '</pre>';
                        if (isset($ret['transaction']['html'])) {
                          echo $ret['transaction']['html'];
                        }
                        if (isset($ret['success']) and $ret['success']==false) {
                          echo '<div><a href="admin-acc-inv-item-fix.php?id='.$id.'" target="_blank">Fix Card</a></div>'; 
                        }
                        echo '</div>';
                      } else {
                        ?>

                        <button class="div_payment_one_terminal_start div_payment_one_terminal_start_css btn btn-sm btn-primary"><?php echo gks_lang('Πληρωμή με');?>:</button>
                        <input data-pp="-1000" data-pawid="<?php echo $row_pliromi['payment_acquirer_with_id'];?>" class="div_payment_one_terminal_terminal form-control form-control-sm" type="text" placeholder="<?php echo gks_lang('Τερματικό');?>"
                        data-asset_id="<?php
                        $asset_title='';
                        foreach ($payment_type_multi as $pmvalue) {
                          if ($pmvalue['asset_id']>0) {
                            echo $pmvalue['asset_id']; 
                            $asset_title=$pmvalue['asset_title']; 
                            break;
                          }
                        } 
                        ?>"
                        value="<?php echo $asset_title;?>">
                        
                      <?php } ?>
                      
                    </div>
                    <?php } ?>
                    
                  </div>
                  

                  <?php } ?>                              
                    
                    
                                    
                  <div id="payment_acquirer_sxolio" class="form-text text-muted" style="font-size:80%"></div>
                  <div class="" style="display:none"><span id="button_html"><?php echo gks_lang('Πληρωμή τώρα');?></span></div>
              </div>
              <div id="div_payment_type_multi" style="<?php if ($tropos_pliromis_one_multi!=1) echo 'display:none;';?>">
                <div id="div_payment_type_multi_header">
                  <div class="div_payment_type_multi_header_label1">
                    <?php echo gks_lang('Τρόπος');?>
                  </div>
                  <div class="div_payment_type_multi_header_label2">
                    <?php echo gks_lang('Ποσό');?>
                  </div>
                  <div class="div_payment_type_multi_header_label3">
                    <i class="fas fa-exclamation-circle"></i>
                  </div>
                </div>
                <div id="div_payment_type_multi_items">
                  
                <?php
                //echo '<pre>';print_r($payment_type_multi);echo '</pre>';

                $pp=0;
                foreach ($payment_type_multi as $pmvalue) {
                  $pp++;
                  
                  
                  $multi_item_extra_class='';
                  if ($pmvalue['transaction_id']>0) {
                    foreach ($transaction_id_ids_array as $traidida) {
                      if ($pmvalue['transaction_id']==$traidida['my_this']) {
                        $multi_item_extra_class.='div_payment_type_multi_item_is_'.$traidida['my_is'].' ';
                      }
                      if ($pmvalue['transaction_id']==$traidida['my_for']) {
                        $multi_item_extra_class.='div_payment_type_multi_item_has_'.$traidida['my_is'].' ';
                      }

                    } 
                  }
                  
                  
                  $pos_style1=' style="display:none;"';
                  $pos_style2=' style="display:none;"';
                  if ($pmvalue['payment_acquirer_with_id']>=1) $pos_style1='';
                  if ($pmvalue['transaction_id']>0) $pos_style2='';
                  ?>
                  <div data-pp="<?php echo $pp;?>" data-rec_id="<?php echo $pmvalue['id_acc_inv_payment'];?>" class="div_payment_type_multi_item <?php echo $multi_item_extra_class;?>">
                    <div class="div_payment_type_multi_item_row1">
                      <?php
                      if ($payments_lock_level>=4 or $pmvalue['transaction_id']>0) {
                       echo '<div class="div_payment_type_multi_item_select" data-lock_value="'.$pmvalue['payment_acquirer_id'].'">'.
                            $pmvalue['payment_acquirer_name'].
                            '</div>';
                       //echo '<pre>';print_r($pmvalue);echo '</pre>';
                      } else {
                      ?> 
                      <select data-pp="<?php echo $pp;?>" class="div_payment_type_multi_item_select form-control form-control-sm" >
                        <option value=0></option>
                        <?php
                        foreach ($mybasketarray['tropoi_pliromis_all'] as $row_pliromi) {
                          if ($row_pliromi['id_payment_acquirer']>1) {
                            echo '<option value='.$row_pliromi['id_payment_acquirer'].
                            ' data-aade_id="'.$row_pliromi['aade_tropos_pliromis_id'].'"'.
                            ' data-payment_acquirer_with_id="'.$row_pliromi['payment_acquirer_with_id'].'"';
                            if ($row_pliromi['id_payment_acquirer']==$pmvalue['payment_acquirer_id']) echo ' selected';
                            echo '>'.$row_pliromi['payment_acquirer_name'].'</option>';  
                          }
                        }
                        ?> 
                      </select> 
                      <?php } ?>
                      <?php
                      if ($payments_lock_level>=4 or $pmvalue['transaction_id']>0) {
                       echo '<div class="div_payment_type_multi_item_input" data-pp="'.$pp.'" data-lock_value="'.$pmvalue['poso'].'">'.
                            myCurrencyFormat($pmvalue['poso']).
                            '</div>';
                       //echo '<pre>';print_r($pmvalue);echo '</pre>';
                      } else {
                      ?>
                      <input value="<?php echo $pmvalue['poso'];?>" data-pp="<?php echo $pp;?>" class="div_payment_type_multi_item_input form-control form-control-sm" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
                      <?php } ?>
                      
                      <div class="div_payment_type_multi_item_icons">
                        <?php if (!($payments_lock_level>=4 or $pmvalue['transaction_id']>0)) {?>
                        <i data-pp="<?php echo $pp;?>" class="payment_type_multi_del fas fa-trash-alt"></i>
                        <?php } ?>
                        <?php if ($payments_lock_level<4) {?>
                        <i data-pp="<?php echo $pp;?>" class="payment_type_multi_add fas fa-plus-circle"></i>
                        <?php } ?>
                      </div>
                      
                    </div>
                    <div class="div_payment_type_multi_item_row2" <?php echo $pos_style1;?>> 
                      <?php
                      if ($pmvalue['transaction_id']>0) {
                        echo '<div class="div_payment_type_multi_item_row2_text">';
                        //'pliromi me : '.$pmvalue['asset_title'].
                        
                        $ret=gks_eftpos_get_transaction_html(['id_eftpos_transaction'=> $pmvalue['transaction_id']]);
                        //echo '<pre>';print_r($ret);echo '</pre>';
                        if (isset($ret['transaction']['html'])) {
                          echo $ret['transaction']['html'];
                        } 
                        if (isset($ret['success']) and $ret['success']==false) {
                          echo '<div><a href="admin-acc-inv-item-fix.php?id='.$id.'" target="_blank">Fix Card</a></div>'; 
                        }
                        echo '</div>'.
                        '<span class="div_payment_type_multi_item_pos_terminal" data-asset_id="'.$pmvalue['asset_id'].'" style="display:none;"/>';
                      } else {
                      ?>
                      <button data-pp="<?php echo $pp;?>" 
                        class="btn btn-sm btn-primary div_payment_type_multi_item_pos_start"><?php echo gks_lang('Πληρωμή με');?>:</button>
                      <input  data-pp="<?php echo $pp;?>" 
                      data-pawid="<?php echo $pmvalue['payment_acquirer_with_id'];?>" 
                      class="div_payment_type_multi_item_pos_terminal form-control form-control-sm" 
                      type="text" placeholder="<?php echo gks_lang('Τερματικό');?>"
                      data-asset_id="<?php echo $pmvalue['asset_id'];?>"
                      value="<?php echo $pmvalue['asset_title'];?>"
                      >
                      <div class="div_payment_type_multi_item_pos_rest"></div>
                      <?php } ?>
                    </div>
                  </div>
                  
                <?php } ?>
                  
                </div>
                <div id="div_payment_type_multi_footer">
                  <div id="div_payment_type_multi_footer_text">
                    <?php echo gks_lang('Σύνολο');?>
                  </div>
                  <div id="div_payment_type_multi_footer_sum">
                    
                  </div>                  
                  <div id="div_payment_type_multi_footer_rest">
                    
                  </div>                  
                </div>
              </div>
            </div>
            
          </div>
          
          <div class="form-group row" id="div_bank_deposit_9digit" style="<?php if ($row['tropos_pliromis']!=2) echo 'display:none;';?>">
            <label for="bank_deposit_9digit" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αιτιολογία κατάθεσης');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm" id="bank_deposit_9digit"><?php echo gks_format_bank_deposit_9digit($row['bank_deposit_9digit'])?></div>
            </div>
          </div>
                    
          <div class="form-group row" id="div_delivery_number" style="<?php echo $div_delivery_number_style;?>">
            <label for="delivery_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Αποστολής');?>:</label>
            <div class="col-md-8">
              <input class="myneedsave form-control form-control-sm" id="delivery_number" type="text" value="<?php echo htmlspecialchars_gks($row['delivery_number']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>>
            </div>
          </div>                   
          <div class="form-group row" id="div_vehicle_number" style="<?php echo $div_delivery_number_style;?>">
            <label for="vehicle_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Μεταφορικού Μέσου');?>:</label>
            <div class="col-md-8">
              <input class="myneedsave form-control form-control-sm" id="vehicle_number" type="text" value="<?php echo htmlspecialchars_gks($row['vehicle_number']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>>
            </div>
          </div>
         
          <div class="form-group row" id="div_dispatch_date" style="<?php echo $div_delivery_number_style;?>">
            <label for="dispatch_date" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημέρα Έναρξης Αποστολής');?>:</label>
            <div class="col-sm-8">
              <input id="dispatch_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (!empty($row['dispatch_date'])) echo  date('d/m/Y',strtotime($row['dispatch_date']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>>
            </div>
          </div> 
          <div class="form-group row" id="div_dispatch_time" style="<?php echo $div_delivery_number_style;?>">
            <label for="dispatch_time" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ώρα Έναρξης Αποστολής');?>:</label>
            <div class="col-sm-8">
              <input id="dispatch_time" type="text" class="form-control form-control-sm myneedsave" value="<?php if (!empty($row['dispatch_time'])) echo  date('H:i',strtotime($row['dispatch_time']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>>
            </div>
          </div>                   
         
        </div>
      </div>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σημειώσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('notes');?>> 

          <div class="form-group row">
            <label for="note_doc" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλια τιμολογίου');?>:</label>
            <div class="col-md-8">
              <textarea id="note_doc" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_doc']);?></textarea>
            </div>
          </div> 


          <div class="form-group row">
            <label for="note_logistirio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερική σημείωση για λογιστήριο');?>:</label>
            <div class="col-md-8">
              <textarea id="note_logistirio" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_acc_inv_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_logistirio']);?></textarea>
            </div>
          </div> 
          
          
          
        </div>
      </div> 
                   

<?php if ($GKS_CRM_ENABLE) { ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          CRM
        </div>
        <div class="card-body" <?php echo gks_card_body('crm_channel');?>> 
          <div class="form-group row">
            <label for="crm_channel_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κανάλι πωλήσεων');?>:</label>
            <div class="col-md-8">
              <select id="crm_channel_id" class="form-control form-control-sm myneedsave" >
                <option value="0" data-contact="0" data-contact_filter="" data-campain="0" data-url="0" data-code="0" data-text="0"></option>
                <?php
                $sql_channel_sale="SELECT *
                FROM gks_crm_channel_sale
                WHERE crm_channel_sale_disabled=0
                ORDER BY crm_channel_sale_sortorder";
                $result_channel_sale = $db_link->query($sql_channel_sale);        
                if (!$result_channel_sale) {
                  debug_mail(false,'error sql',$sql_channel_sale);
                  die('sql error');
                }
                $row_channel_sale_selected=array(
                  'crm_channel_has_contact'=>0,
                  'crm_channel_has_contact_filter'=>'',
                  'crm_channel_has_campain'=>0,
                  'crm_channel_has_url'=>0,
                  'crm_channel_has_code'=>0,
                  'crm_channel_has_text'=>0,
                );
                
                while ($row_channel_sale = $result_channel_sale->fetch_assoc()) {
                  echo '<option value="'.$row_channel_sale['id_crm_channel_sale'].'" '.
                  'data-contact="'.intval($row_channel_sale['crm_channel_has_contact']).'" '.
                  'data-contact_filter="'.base64_encode(trim_gks($row_channel_sale['crm_channel_has_contact_filter'])).'" '.
                  'data-campain="'.intval($row_channel_sale['crm_channel_has_campain']).'" '.
                  'data-url="'.intval($row_channel_sale['crm_channel_has_url']).'" '.
                  'data-code="'.intval($row_channel_sale['crm_channel_has_code']).'" '.
                  'data-text="'.intval($row_channel_sale['crm_channel_has_text']).'" ';
                  if ($row_channel_sale['id_crm_channel_sale']==$row['crm_channel_id']) {
                    echo ' selected ';
                    $row_channel_sale_selected=$row_channel_sale;
                  }
                  echo '>'.$row_channel_sale['crm_channel_sale_descr'].'</option>';
                }?>
              </select>
            </div>
          </div>



          <div class="form-group row" id="crm_channel_contact_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_contact']==0) echo 'display:none;';?>">
            <label for="crm_channel_contact_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επαφή Πωλήσεων');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_contact_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['crm_channel_contact_gks_nickname']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['crm_channel_contact_id'];?>">
            </div>
          </div>


          <div class="form-group row" id="crm_channel_campain_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_campain']==0) echo 'display:none;';?>">
            <label for="crm_channel_campain_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Καμπάνια');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_campain_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['ads_campain_name']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['crm_channel_campain_id'];?>">
            </div>
          </div>

          <div class="form-group row" id="crm_channel_url_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_url']==0) echo 'display:none;';?>">
            <label for="crm_channel_url" class="col-md-4 col-form-label form-control-sm text-md-right">URL:</label>
            <div class="col-md-8">
              <input id="crm_channel_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_channel_url']);?>">
            </div>
          </div>
          <div class="form-group row" id="crm_channel_code_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_code']==0) echo 'display:none;';?>">
            <label for="crm_channel_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_channel_code']);?>">
            </div>
          </div>
          
          <div class="form-group row" id="crm_channel_text_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_text']==0) echo 'display:none;';?>">
            <label for="crm_channel_text" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="crm_channel_text" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;"><?php echo htmlspecialchars_gks($row['crm_channel_text']);?></textarea>
            </div>
          </div>   


        </div>
      </div>

<?php } ?>

            


      
    </div>
                      
    <div class="col-md-6">
      
     
      
      <?php if (in_array($acc_eidos_parastatikou_id_org,[702,703,704])==false) { /*akurotiko */ ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υπόλοιπο Επαφής');?>         
        </div>

        <div id="myypoloipoepafis_card" class="<?php
          if ($eidos_parastatikou_balance_pros==0) echo 'myypoloipoepafis_card_notactive';
          ?> card-body" <?php echo gks_card_body('aff_bal');?>>  
          <div id="myypoloipoepafis_not_text" class="form-group row" >
            <div class="col-md-12">
              <div class="alert alert-warning" role="alert">
                <?php echo gks_lang('Αυτό το ημερολόγιο δεν επιτρέπει να επηρεαστεί το υπόλοιπο της επαφής');?>
              </div>
            </div>
          </div>
          <div id="myypoloipoepafis_controls">
            <div class="form-group row">
              <label for="balance_user_before" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προηγούμενο υπόλοιπο');?>:</label>
              <div class="col-md-8">
                <div class="form-control-sm" id="balance_user_before" 
                  <?php
                  $balance_user_before=gks_balance_calc(['id' => $row['user_id'], 'except_id_acc_inv' => $id]);
                  echo ' data-val="'.number_format($balance_user_before,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').'">'.myCurrencyFormat($balance_user_before);
                  ?>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="balance_user_after" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νέο υπόλοιπο');?>:</label>
              <div class="col-md-8">
                <div class="form-control-sm" id="balance_user_after" >
                  <?php
                  $balance_user_after=gks_balance_calc(['id' => $row['user_id']]);
                  echo myCurrencyFormat($balance_user_after);
                  ?>
                </div>
              </div>
            </div>
            
            <div class="form-group row" id="div_affect_balance">
              <label for="affect_balance" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επηρεάζει το υπόλοιπο της επαφής');?>:</label>
              <div class="col-md-8">
                <input id="affect_balance" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['affect_balance']!=0) echo ' checked ';?> <?php if (!$perm_gks_acc_inv_edit) echo 'disabled';?>>
                <?php if (!($inv_state=='080listing' or $inv_state=='090ekdosi' or $inv_state=='100payment')) {?>
                <small class="form-text text-muted"><?php echo gks_lang('Θα εφαρμοστεί η ρύθμιση όταν η κατάσταση του παραστατικού θα είναι μία από τις παρακάτω');?>:<br>
                  <span style="line-height: 1.8;">
                  <span class="acc_inv_state_080listing"><?php echo getAccInvStateDescr('080listing');?></span>
                  <span class="acc_inv_state_090ekdosi"><?php echo getAccInvStateDescr('090ekdosi');?></span>
                  <span class="acc_inv_state_100payment"><?php echo getAccInvStateDescr('100payment');?></span>
                  </span>
                </small>
                <?php } ?>
              </div>
            </div> 
  
            
            
            <div class="form-group row" id="div_affect_balance_all_poso" style="<?php if ($row['affect_balance']==0) echo 'display:none;';?>">
              <label for="affect_balance_all_poso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ολόκληρο το ποσό');?>:</label>
              <div class="col-md-8">
                <input id="affect_balance_all_poso" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['affect_balance_all_poso']!=0) echo ' checked ';?> <?php if (!$perm_gks_acc_inv_edit) echo 'disabled';?>>
                <small class="form-text text-muted" id="small_affect_balance_all_poso" style="<?php if (!($row['affect_balance']==0 or $row['affect_balance_all_poso']!=0)) echo 'display:none;';?>">
                  <input type="radio" name="affect_balance_all_poso_type" value="price_net" id="affect_balance_all_poso_type_price_net" <?php
                  if ($row['affect_balance_all_poso_type']=='price_net') echo ' checked';?>>
                    <label for="affect_balance_all_poso_type_price_net"    style="margin-bottom: 0px;"><?php echo gks_lang('Υποσύνολο');?> (<span
                      id="bal_gks_total_price_net" data-val="<?php echo number_format($eidi_sum_price_net,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                      ?>"><?php echo myCurrencyFormat($eidi_sum_price_net);?></span>)</label><br>
                  <input type="radio" name="affect_balance_all_poso_type" value="price_netfpa" id="affect_balance_all_poso_type_price_netfpa" <?php
                  if ($row['affect_balance_all_poso_type']=='price_netfpa') echo ' checked';?>>
                    <label for="affect_balance_all_poso_type_price_netfpa" style="margin-bottom: 0px;"><?php echo gks_lang('Μικτό σύνολο');?> (<span
                      id="bal_gks_total_price_netfpa" data-val="<?php echo number_format($row['gks_price_netfpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                      ?>"><?php echo myCurrencyFormat($row['gks_price_netfpa']);?></span>)</label><br>
                  <input type="radio" name="affect_balance_all_poso_type" value="price_total" id="affect_balance_all_poso_type_price_total" <?php
                  if ($row['affect_balance_all_poso_type']=='price_total') echo ' checked';?>>
                    <label for="affect_balance_all_poso_type_price_total" style="margin-bottom: 0px;"><?php echo gks_lang('Σύνολο');?> (<span
                      id="bal_gks_total_price_total" data-val="<?php echo number_format($row['gks_price_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                      ?>"><?php echo myCurrencyFormat($row['gks_price_total']);?></span>)</label><br>
                  <input type="radio" name="affect_balance_all_poso_type" value="pliroteo" id="affect_balance_all_poso_type_pliroteo" <?php
                  if ($row['affect_balance_all_poso_type']=='pliroteo') echo ' checked';?>>
                    <label for="affect_balance_all_poso_type_pliroteo" style="margin-bottom: 0px;"><?php echo gks_lang('Πληρωτέο');?> (<span
                      id="bal_gks_pliroteo" data-val="<?php echo number_format($pliroteo,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                      ?>"><?php echo myCurrencyFormat($pliroteo);?></span>)</label>
  
                </small>
              </div>
            </div> 
            <div class="form-group row" id="div_affect_balance_poso"  style="<?php if ($row['affect_balance']==0 or $row['affect_balance_all_poso']!=0) echo 'display:none;';?>">
              <label for="affect_balance_poso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ποσό');?>:</label>
              <div class="col-md-8">
                
                <input id="affect_balance_poso" type="number" class="form-control form-control-sm myneedsave" 
                value="<?php 
                $valnotzero='';
                if ($row['affect_balance_poso']!=0) {
                  $valnotzero=myNumberFormatNo0($row['affect_balance_poso']);
                  echo $valnotzero;
                };?>" 
                style="text-align:right;max-width: 100px;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
                placeholder="<?php echo gks_lang('Ποσό');?>"
                <?php if (!$perm_gks_acc_inv_edit) echo 'disabled';?> >            
              </div>
            </div> 
          </div>
                    
        </div>
      </div>
      <?php } ?>

<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>  

      
    </div>
                  
    
  </div>
</div>

</div>


<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">


     
<?php

$GKS_ACC_INV_STATUS_BUTTONS=array(
  '010draft' =>           array('cmdupdate','cmddelete','cmdprint',           '050proinvoice','080listing','070ypoekdosi','090ekdosi',),
  '040cancelled' =>       array('cmdupdate',            'cmdprint','010draft',),
  '050proinvoice' =>      array('cmdupdate','cmddelete','cmdprint','010draft','080listing',  '070ypoekdosi','090ekdosi',),
  '070ypoekdosi' =>       array('cmdupdate',            'cmdprint','010draft',               '090ekdosi',),
  '080listing' =>         array('cmdupdate',            'cmdprint','010draft','040cancelled',),
  '090ekdosi' =>          array('cmdupdate',            'cmdprint','010draft','040cancelled',),
  '100payment' =>         array('cmdupdate',            'cmdprint','010draft','040cancelled',),
);

if (isset($GKS_ACC_INV_STATUS_BUTTONS[$inv_state])) {
  if ($perm_gks_acc_inv_edit) {

    
    if (in_array('cmdupdate',$GKS_ACC_INV_STATUS_BUTTONS[$inv_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="submit_button_ok_custom">'.gks_lang('Αποθήκευση').'</button> ';
    if (in_array('cmddelete',$GKS_ACC_INV_STATUS_BUTTONS[$inv_state]) and $id>0) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-danger thisdeleterowbtn" data-id="'.($row['id_acc_inv']>0 ? $row['id_acc_inv'] : '').'" data-model="gks_acc_inv" data-backurl="admin-acc-inv.php" '.($perm_gks_acc_inv_delete ? '' : 'disabled').'>'.gks_lang('Διαγραφή').'</button> ';
    if (in_array('010draft',$GKS_ACC_INV_STATUS_BUTTONS[$inv_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_inv_state_010draft" id="submit_button_010draft">'.gks_lang('Επαναφορά σε Πρόχειρο').'</button> ';
    if ($cancel_for_acc_inv_id ==0 and in_array('040cancelled',$GKS_ACC_INV_STATUS_BUTTONS[$inv_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_inv_state_040cancelled" id="submit_button_040cancelled">'.gks_lang('Ακύρωση').'</button> ';
    if ($row['from_aade_import']=='' and $cancel_for_acc_inv_id ==0 and in_array('050proinvoice',$GKS_ACC_INV_STATUS_BUTTONS[$inv_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_inv_state_050proinvoice" id="submit_button_050proinvoice">'.getAccInvStateDescr('050proinvoice').'</button> ';
    if (in_array('070ypoekdosi',$GKS_ACC_INV_STATUS_BUTTONS[$inv_state])) 
      echo '<button type="button" style="margin-bottom:10px;'.($row['is_xeirografi']!=0 ? 'display:none;' : '').'" class="btn button_acc_inv_state_070ypoekdosi" id="submit_button_070ypoekdosi">'.getAccInvStateDescr('070ypoekdosi').'</button> ';
    if (in_array('080listing',$GKS_ACC_INV_STATUS_BUTTONS[$inv_state])) 
      echo '<button type="button" style="margin-bottom:10px;'.($row['is_xeirografi']==0 ? 'display:none;' : '').'" class="btn button_acc_inv_state_080listing" id="submit_button_080listing">'.getAccInvStateDescr('080listing').'</button> ';
    if (in_array('090ekdosi',$GKS_ACC_INV_STATUS_BUTTONS[$inv_state])) 
      echo '<button type="button" style="margin-bottom:10px;'.($row['is_xeirografi']!=0 ? 'display:none;' : '').'" class="btn button_acc_inv_state_090ekdosi" id="submit_button_090ekdosi">'.getAccInvStateDescr('090ekdosi').'</button> ';
    if (in_array($acc_eidos_parastatikou_id_org,[702,703,704])==false) { /*akurotiko */
    if (($inv_state=='090ekdosi' or $inv_state=='100payment') and $row['is_xeirografi']==0 and ($acc_eidos_parastatikou_id_org!=51 and $acc_eidos_parastatikou_id_org!=52)) { 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_inv_state_credit_memo tooltipster" id="submit_button_credit_memo" title="'.gks_lang('Δημιουργία πιστωτικού τιμολογίου').'">'.gks_lang('Πιστωτικό').'</button> ';
    }}
    
      
    if (($row['from_aade_import']=='' and ($inv_state=='080listing' or $inv_state=='090ekdosi' or $inv_state=='100payment')) and $row['send_mydata']==1) { 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_inv_state_aade_send tooltipster" id="submit_button_aade_send" title="'.gks_lang('Αποστολή myData στην ΑΑΔΕ').'">'.gks_lang('ΑΑΔΕ').'</button> ';
    }
    if (($row['from_aade_import']=='' and ($inv_state=='080listing' or $inv_state=='090ekdosi' or $inv_state=='100payment')) and $row['send_paroxos']==1) { 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_inv_state_paroxos_send tooltipster" id="submit_button_paroxos_send" title="'.gks_lang('Αποστολή στον πάροχο').'">'.gks_lang('Πάροχος').'</button> ';
    }
    
    
  }
  if (in_array('cmdprint',$GKS_ACC_INV_STATUS_BUTTONS[$inv_state])) {
    echo '<button type="button" style="margin-bottom:10px;" class="btn btn-dark" id="submit_button_print">'.gks_lang('Εκτύπωση').' <i class="fas fa-print" style="color: #35dc35;font-size: 120%;"></i></button> ';
  }
  
  if ($id>0 and $perm_gks_acc_inv_add) {
    echo '<a href="admin-acc-inv-item.php?id=-1&template_id='.$id.'" style="margin-bottom:10px;" '.
      'class="btn btn-primary tooltipster" '.
      'id="submit_button_template" '.
      'title="<div style=\'text-align: center;\'>'.gks_lang('Δημιουργία αντιγράφου').'<br>'.gks_lang('ή').'<br>'.
              '<button class=\'btn btn-primary btn-sm\' style=\'margin-top:6px;\' '.
              'onclick=\'submit_button_template_create(1);\' '.
              'id=\'submit_button_template_create\' data-obj=\'gks_acc_inv\''.
              '>'.gks_lang('Ορισμός ως πρότυπο').'</button>'.
             '</div>">'.
      '<i class="fas fa-copy" style="font-size: 120%;"></i>'.
    '</a> ';
  }
  
}
 
 
 ?>     
      <div style="display:inline-block;width:38px;height:38px;vertical-align:top;">
        <div style="border:1px solid gray;padding: 7px 0px 5px 0px;;border-radius:4px;background-color:#343a40;display:none;" id="calc_hourglass">
          <i class="fas fa-hourglass-half" style="color:coral;font-size:120%;"></i>
        </div> 
      </div>
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid " style="padding-top:0px">
  <div class="row">
    <div class="col-xl-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Πληρωμές');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="acc_pay_add" style="margin-left: 10px;"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('acc_pay');?>>	
<?php
$sql_acc_pay = "SELECT gks_acc_pay_poso_acc_inv.poso, gks_acc_pay.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
gks_acc_journal.acc_eidos_parastatikou_id,
eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_need_prev, eidos_parastatikou_has_fpa,eidos_parastatikou_has_othertaxes, 
eidos_parastatikou_has_esoda, eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,eidos_parastatikou_balance_pros,
gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
gks_users.ma_odos,gks_users.ma_arithmos,gks_users.ma_orofos,gks_users.ma_perioxi,gks_users.ma_poli,gks_users.ma_tk,
gks_users.ma_country_id,gks_users.ma_nomos_id,
gks_country.country_name,gks_nomoi.nomos_descr,
table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name,
".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile as user_mobile,
gks_lang.lang_name, ".GKS_WP_TABLE_PREFIX."users.gks_lang as user_lang,
CASE
  WHEN (pay_state='080listing' or pay_state='090ekdosi' or pay_state='100payment') and affect_balance=1
    THEN affect_balance_pros * affect_balance_poso
  ELSE 0
END as affect_balance_calc,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name




FROM  ((((((((((((((((((((gks_acc_pay_poso_acc_inv
LEFT JOIN gks_acc_pay on gks_acc_pay_poso_acc_inv.acc_pay_id = gks_acc_pay.id_acc_pay)




LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_pay.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_pay.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_print ON gks_acc_pay.print_user_id = ".GKS_WP_TABLE_PREFIX."users_print.ID) 
LEFT JOIN gks_company on gks_acc_pay.company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_acc_pay.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
)  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
)  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_acc_pay.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_acc_pay.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_acc_pay.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_acc_pay.crm_channel_campain_id = gks_ads_campain.id_ads_campain

WHERE gks_acc_pay_poso_acc_inv.acc_inv_id=".$id."
order by gks_acc_pay.pay_date desc, gks_acc_pay.id_acc_pay desc
"; 


		
		$result_acc_pay = $db_link->query($sql_acc_pay);        
		if (!$result_acc_pay) debug_mail(false,'error sql',$sql_acc_pay);
		if (!$result_acc_pay) die('sql error');

		

		
		
?>

<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr >	
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'>#</th>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Κατάσταση');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Ημερομηνία');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Εταιρεία');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Ημερολόγιο');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Σειρά');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Αριθμός');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Τιμή');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Τιμή για<br>υπόλοιπο');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('Ποσό');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="30%" ><?php echo gks_lang('Ιδιότητες');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="30%" ><?php echo gks_lang('Σχόλια');?></th>        
      
  </tr>
</thead>
<tbody>
<?php


    $j = 0;
    while ($row_acc_pay = $result_acc_pay->fetch_assoc()) {

	$j++;
?>
  <tr class="<?php echo ($j % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($j);?></th>
    <td nowrap class="mytdcm">
      <table cellpadding=0 cellspacing=0 class="gks_tb1">
        <tr class="gks_tr1">
          <td class="gks_ttd1" colspan="2"><?php echo $row_acc_pay['id_acc_pay'];?></td>
        </tr>  
        <tr class="gks_tr1">
          <td class="gks_ttd2"><a href="admin-acc-pay-item.php?id=<?php echo $row_acc_pay['id_acc_pay'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <?php if (false) {?>
          <td class="gks_ttd3" ><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_acc_pay['id_acc_pay'];?>" data-model="gks_acc_pay"></i></td>
          <?php } ?>
        </tr>  
     </table>
    </td>
    

    <td nowrap class="mytdcm"><span class="acc_pay_state_<?php echo $row_acc_pay['pay_state'];?>"><?php echo getAccPayStateDescr($row_acc_pay['pay_state']);?></span></td>
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row_acc_pay['pay_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td        class="mytdcm"><?php echo $row_acc_pay['company_title']; if (isset($row_acc_pay['company_sub_title'])) echo '<br>'.$row_acc_pay['company_sub_title'];?></td> 

    <td        class="mytdcm"><?php echo $row_acc_pay['acc_journal_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row_acc_pay['seira_code'];?></td>
    <td nowrap class="mytdcm"><?php if ($row_acc_pay['pay_acc_number_int']<>0) echo $row_acc_pay['pay_acc_number_int'];?></td>






    <td nowrap class="mytdcm" ><?php 
      if ($row_acc_pay['gks_price_total']!=0) echo '<b>'.myCurrencyFormat($row_acc_pay['gks_price_total']).'</b>';
    ?></td>
    <td nowrap class="mytdcm" ><?php 
      if ($row_acc_pay['affect_balance_calc']!=0) echo myCurrencyFormat($row_acc_pay['affect_balance_calc']);
    ?></td>
    <td nowrap class="mytdcm" ><?php 
      if ($row_acc_pay['poso']!=0) echo myCurrencyFormat($row_acc_pay['poso']);
    ?></td>
    


      

    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $temp = trim_gks($row_acc_pay['idiotites']);
      if ($temp!='') {
        $myarray = json_decode($temp, true);
        $temp='';
        foreach ($myarray as $value) {
          $temp.=$value[0].': <b>'.$value[1].'</b><br>';
        } 
        if ($temp!='') $temp=substr($temp, 0, strlen($temp)-4);
        echo $temp;
      }
    
    ?></div></div></td>  
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
      $temp='';
      if (!empty($row_acc_pay['notes'])) $temp.=gks_lang('Πελάτης').': <b>'.nl2br_gks($row_acc_pay['notes']).'</b><br>';
      if (!empty($row_acc_pay['subnotes'])) $temp.=gks_lang('Πελάτης (Συν)').': <b>'.nl2br_gks($row_acc_pay['subnotes']).'</b><br>';
      if (!empty($row_acc_pay['note_production'])) $temp.=gks_lang('Παραγωγή').': <b>'.nl2br_gks($row_acc_pay['note_production']).'</b><br>';
      if (!empty($row_acc_pay['note_logistirio'])) $temp.=gks_lang('Λογιστήριο').': <b>'.nl2br_gks($row_acc_pay['note_logistirio']).'</b><br>';
      
      if ($temp!='') $temp=substr($temp, 0, strlen($temp)-4);
      echo $temp;
    ?></div></div></td>   
    

    

  </tr>
<?php    
    }
?>

</tbody>
</table>
		
        </div>
      </div>
    </div>
  </div>
</div>
	

<div class="container-fluid " style="padding-top:0px">
  <div class="row">
    <div class="col-xl-6">



      <?php echo getObjectRels('gks_acc_inv',$id); ?>   
      
           
      <?php echo getActivityObjectTable('gks_acc_inv',$id); ?>
      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Μηνύματα');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="message_item_add"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('message');?>>
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
            <thead>
              <tr>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;">#</th>
                <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
                <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>                
                <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Μήνυμα');?></th>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;"><i class="fas fa-envelope" style="color: #35dc35;font-size: 120%;"></i></th>
              </tr>
            </thead>  
            <tbody id="item_messages_body"> 
              
            <?php
            $sql_msg="SELECT gks_acc_inv_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_acc_inv_messages LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_acc_inv_messages.acc_inv_id=".$id."
            ORDER BY gks_acc_inv_messages.mydate_add DESC, gks_acc_inv_messages.id_acc_inv_message DESC;";
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg['id_acc_inv_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php 
                if (!empty($row_msg['woo_author'])) echo $row_msg['woo_author'];
                else echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_msg['acc_inv_message']);
                ?></div></div></td>    
              <td class="mytdcm"><?php 
                if ($row_msg['email_id']!=0) {
                  echo '<i class="fas fa-envelope gks_email_view" data-id="'.$row_msg['email_id'].'"></i>';
                }
                if ($row_msg['sms_id']!=0) {
                  echo '<i class="fas fa-sms gks_sms_view" data-id="'.$row_msg['sms_id'].'"></i>';
                }                
                ?></td>
            </tr>
            <?php } ?>                      
            </tbody>   
          </table>                
        </div>
      </div>
            
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σύνδεσμοι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('links');?>><?php

          
          
          $query = "SELECT gks_acc_inv_links.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_acc_inv_links LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv_links.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_acc_inv_links.acc_inv_id in (".$id.")
          ORDER BY gks_acc_inv_links.mydate, gks_acc_inv_links.id_acc_inv_links;";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="links_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Χρήστης');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Προσθήκη');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Σύνδεσμος');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Μέγεθος');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          $need_download_timer=0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr id="tr_links_url_<?php echo $row_list['id_acc_inv_links'];?>">
              <th scope="row" nowrap align="right" class="links_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_list['id_acc_inv_links'];?>" data-deleteafter="gks_fnc_links_delete_after|<?php echo $row_list['id_acc_inv_links'];?>" data-model="gks_acc_inv_links" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>

              </td>
              <td nowrap><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.$row_list['gks_nickname'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate']), 'd/m/Y H:i', 1);?></td>   
              <td       style="word-break: break-all;">
                <div><?php 
                $temp=trim_gks($row_list['url']);
                if ($temp!='' and startwith($temp,'http')) {
                  $temp='<a href="'.$temp.'" target="_blank">'.(strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp).'</a>';
                  echo $temp;
                } else {
                  echo (strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp);
                }
                ?></div>
                <div class="progress download-perc" data-id="<?php echo $row_list['id_acc_inv_links'];?>" 
                  style="<?php echo ($row_list['download_status']==1 ? '' : 'display:none;');?>">
                  <div class="download-perc-bar progress-bar progress-bar-striped" 
                    data-id="<?php echo $row_list['id_acc_inv_links'];?>" role="progressbar" 
                    style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>    
                <div class="download-message" 
                  data-id="<?php echo $row_list['id_acc_inv_links'];?>" 
                  style="<?php echo ($row_list['download_status']==3 ? '' : 'display:none;');?>"
                  ><?php echo $row_list['download_message'];?></div>
                
              </td>
              <td nowrap class="download_size_until_now" data-id="<?php echo $row_list['id_acc_inv_links'];?>" style="text-align:right;vertical-align:middle;"><?php if ($row_list['download_size_until_now']>0) echo number_format($row_list['download_size_until_now']/1024/1024,2,$GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB';?></td>  
              <td nowrap class="download_file_td" data-id="<?php echo $row_list['id_acc_inv_links'];?>" style="text-align:center;vertical-align: middle;"><?php
              
              
              // 0 notdownload
              // 1 downloding
              // 2 complete
              // 3 abort
              
              if ($row_list['download_status']==0) { //notdownload
                echo '<i class="fas fa-file-download download_action_start" data-id="'.$row_list['id_acc_inv_links'].'" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==1) { //downloding
                $need_download_timer=1;
                echo '<i class="fas fa-stop-circle download_action_stop" data-id="'.$row_list['id_acc_inv_links'].'" style="font-size:200%;vertical-align:middle;color:red;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==2) { //complete
                echo '<i class="fas fa-check-circle download_action_complete" data-id="'.$row_list['id_acc_inv_links'].'" style="font-size:200%;vertical-align:middle;color:green;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==3) { //abort
                echo '<i class="fas fa-undo download_action_reset" data-id="'.$row_list['id_acc_inv_links'].'" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>';
              } 
                
              ?></td>  
            </tr>
          <?php } ?>


            <tr class="" id="tr_new_links_url">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="links_url"    id="links_url"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('π.χ.');?> https://we.tl/...">
              </td>  
            </tr>
            <tr class="" id="tr_new_links_url_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_links_url"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>                       
        </div>
      </div>
              


			<?php
			$obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_acc_inv','id'=>$id));
      echo $obj_fileslist['html'];
      ?>

        
      
      
		</div>


    
    	
    <div class="col-xl-6">


      <?php 
      
      if (trim_gks($row['print_date'])!='' or 
          trim_gks($row['print_file_name']) != '' or 
          trim_gks($row['print_file_url']) != '' or 
          $row['print_user_id']>0 or 
          trim_gks($row['print_inv_state']) != '') {?>
      
      <div class="card gks_card_expand" id="gks_print">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εκτύπωση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('print');?>>       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['print_date'])) echo showDate(strtotime($row['print_date']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εκτύπωση από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['print_user_id'].'">'.$row['gks_nickname_print'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση όταν έγινε η εκτύπωση');?>:</label>
            <div class="col-sm-8"><span class="acc_inv_state_<?php echo $row['print_inv_state'];?>"><?php echo getAccInvStateDescr($row['print_inv_state']);?></span></div>
          </div>

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αρχείο');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (trim_gks($row['print_file_name'])!='') {
                $local_file=GKS_FileServerShare.'acc/inv/'.$id.'/print/'.$row['print_file_name'];
                if (file_exists($local_file)) {
                  //print_file_url
                  $url_file='admin-get-file.php?fs=fileservers&file=acc%2Finv%2F'.$id.'%2Fprint%2F'.urlencode($row['print_file_name']);
                  echo '<a href="'.$url_file.'" target="_blank" id="last_print_file">'.$row['print_file_name'].'</a> ';
                  echo '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
                }
              }
              ?></span></div>
          </div>

        </div>      
      </div>              
      <?php } ?>
      <?php if ($perm_gks_acc_inv_edit) {?>
     
      <?php
      $aade_sending=trim_gks($row['aade_sending']);
      if ($aade_sending!='') {
        $aade_sending=unserialize($aade_sending);
        if (isset($aade_sending['time']) and isset($aade_sending['id']) and $aade_sending['id']==$id) {
          $diafora=time() - intval($aade_sending['time']);
        
        ?>
      <div class="row" id="div_aade_sending">
        <div class="col-sm-12">
          <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Προσπάθεια αποστολής σε');?> <b><?php
              if ($aade_sending['aade']==1) echo gks_lang('myData');
              else if ($aade_sending['paroxos']==1) echo gks_lang('πάροχο');
              ?></b></h4>
            <div><?php
              $tmpmsg=gks_lang('Υπάρχει ήδη σε εξέλιση αυτή η διαδικασία εδώ και <b>[1] δευτερόλεπτα</b><br>Κάντε ανανέωση της σελίδας για να δείτε το αποτέλεσμα.<br>Εάν σε 3 λεπτά δεν έχει τελειώσει τότε μπορείτε να ξαναδοκιμάσετε με την αποστολή.<br>Έναρξη στις: [2]<br>doc id: [3]<br>user id: [4]<br>IP: [5]<br>force_options: [6]<br>');
              $tmpmsg=str_replace('[1]',$diafora,$tmpmsg);
              $tmpmsg=str_replace('[2]',showDate($aade_sending['time'], 'd/m/Y H:i:s', 1),$tmpmsg);
              $tmpmsg=str_replace('[3]',$aade_sending['id'],$tmpmsg);
              $tmpmsg=str_replace('[4]',$aade_sending['user'],$tmpmsg);
              $tmpmsg=str_replace('[5]',$aade_sending['ip'],$tmpmsg);
              $tmpmsg=str_replace('[6]',json_encode($aade_sending['force_options']),$tmpmsg);
              echo $tmpmsg;


                      
              ?></div>
          </div>
        </div>
      </div>
        
        <?php
        }
      }

      $aade_errors=trim_gks($row['aade_errors']);
      $paroxos_tf1_url=trim_gks($row['paroxos_tf1_url']);
      if (trim_gks($row['aade_statuscode'])!='' or 
          trim_gks($row['aade_invoiceuid']) != '' or 
          trim_gks($row['aade_invoicemark']) != '' or 
          trim_gks($row['aade_qrurl']) != '' or 
          trim_gks($row['aade_paroxos_qrurl']) != '' or 
          trim_gks($row['aade_send_date']) !='' or 
          ($row['aade_paroxos_id']>0 and $row['paroxos_status']<>-1) or
          $paroxos_tf1_url != ''
          ) {
          $paroxos_tf1=false;  
          if ($paroxos_tf1_url<>'' and 
              trim_gks($row['aade_qrurl'])=='' and
              trim_gks($row['aade_paroxos_qrurl'])=='' and
              trim_gks($row['aade_send_date'])=='' and 
              $row['aade_paroxos_id']==0) {
            $paroxos_tf1=true;
          }?>
      <div class="card gks_card_expand" id="gks_aade">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('ΑΑΔΕ - myData - Πάροχος');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('aade');?>>
          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Αποστολή σε');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if ($row['aade_paroxos_id']<=0 and $paroxos_tf1==false) echo gks_lang('ΑΑΔΕ - myData');
              else {
                echo gks_lang('Πάροχο').': '.$row['paroxos_name'];
                if ($paroxos_tf1 and trim_gks($row['paroxos_name'])=='' and (startwith($paroxos_tf1_url,'https://test.vs.gr/') or startwith($paroxos_tf1_url,'https://vs.gr/'))) {
                  echo 'Meg myData';
                }
              }
            ?></span></div>
          </div>

          
          
          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Αποστολή σε');?>:</label>
            <div class="col-sm-8 gks_unset_height">
              <span class="form-control-plaintext form-control-sm" style="height: auto;">
                <span class="aade_xml_response_<?php 
                  if (trim_gks($row['aade_statuscode'])=='Success') echo 'ok';
                  else if (trim_gks($row['aade_statuscode'])=='Processing') echo 'processing';
                  else echo 'error';
                  ?> tooltipster"
                  title="<?php
                  if ($paroxos_tf1) echo gks_lang('Σφάλμα μετάδοσης παραστατικών');
                  else echo getAADEstatuscodeDescr($row['aade_statuscode']);
                  ?>"><?php 
                  if ($paroxos_tf1) echo 'Transmission Failure 1 (TF-1)'; else echo $row['aade_statuscode'];
                  ?></span>
                <?php
                if ($row['aade_paroxos_id']>0 and $row['paroxos_status']==0) {
                 echo '<span id="paxoros_check_processing" class="btn1 btn-sm1 btn-primary1 tooltipster" title="'.gks_lang('Έλεγχος κατάστασης από πάροχο').'"><i class="fas fa-sync"></i></span>';  
                }
                ?>
              </span>
            </div>
          </div>
          <?php if ($row['aade_paroxos_id']>0) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height" style="height: unset;"><?php echo gks_lang('Κατάσταση σε πάροχο');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if ($row['paroxos_status']==0) echo gks_lang('Σε επεξεργασία');
              else if ($row['paroxos_status']==1) echo gks_lang('Ολοκληρώθηκε');
              else if ($row['paroxos_status']==2) echo gks_lang('Απέτυχε');
              ?></span></div>
          </div>
          <?php } ?>
                    
          <?php if (empty($row['aade_invoiceuid'])==false) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Αναγνωριστικό Παραστατικού');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php echo $row['aade_invoiceuid'];?></span></div>
          </div>
          <?php } ?>
          <?php if ($row['aade_paroxos_id']>0) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height" style="height: unset;"><?php echo gks_lang('Αναγνωριστικό Παραστατικού Παρόχου');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php echo $row['paroxos_authenticationCode'];?></span></div>
          </div>
          <?php if ($row['paroxos_invoice_number']>0) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height" style="height: unset;"><?php echo gks_lang('Αριθμός τιμολογίου Παρόχου');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php echo $row['paroxos_invoice_number'];?></span></div>
          </div>          
          
          <?php }} ?>
                    
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><span title="<?php echo gks_lang('Μοναδικός Αριθμός Καταχώρησης Παραστατικού');?>" class="tooltipster"><?php echo gks_lang('ΜΑΡΚ');?></span>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (empty($row['aade_invoicemark'])==false) {
                echo $row['aade_invoicemark'];
              } else if ($row['aade_paroxos_id']>0) {?>
                <span id="paroxos_get_docstate" class="btn1 btn-sm1 btn-primary1 tooltipster" title="<?php echo gks_lang('Λήψη κατάστασης από πάροχο');?>"><i class="fas fa-sync"></i></span><?php
              } 
              
              ?></span></div>
          </div>
          <?php if (empty($row['aade_qrurl'])==false or empty($row['aade_paroxos_qrurl'])==false or $paroxos_tf1_url!='') {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('QR Code Url');?>:</label>
            <div class="col-sm-8 gks_aade_qrurl_list">
              <?php if (empty($row['aade_qrurl'])==false) {
                $qr_code_url=gks_qr_code_generate($row['aade_qrurl']);?>
              <a href="<?php echo $row['aade_qrurl'];?>" target="_blank" class="gks_aade_qrurl">
                <img src="<?php echo $qr_code_url;?>">
              </a>
              <?php
              }
              if (empty($row['aade_paroxos_qrurl'])==false) {
                $qr_paroxos_code_url=gks_qr_code_generate($row['aade_paroxos_qrurl']);?>
              <a href="<?php echo $row['aade_paroxos_qrurl'];?>" target="_blank" class="gks_aade_paroxos_qrurl">
                <img src="<?php echo $qr_paroxos_code_url;?>">
              </a>                
              <?php } 
              if ($paroxos_tf1_url!='' and empty($row['aade_qrurl']) and empty($row['aade_paroxos_qrurl'])) {
                $qr_paroxos_tf1_url=gks_qr_code_generate($paroxos_tf1_url);?>
              <a href="<?php echo $paroxos_tf1_url;?>" target="_blank" class="gks_aade_paroxos_tf1_qrurl">
                <img src="<?php echo $qr_paroxos_tf1_url;?>">
              </a>                
              <?php } ?>
            </div>
          </div>
          <?php } ?>
          
          <?php if (empty($row['aade_send_date'])==false) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Ημερομηνία Αποστολής');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php if (isset($row['aade_send_date'])) echo showDate(strtotime($row['aade_send_date']), 'd/m/Y H:i', 1);?></span></div>
          </div>
          <?php } ?>
          <?php if (empty($row['gks_nickname_aade'])==false) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Αποστολή από');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['aade_user_id'].'">'.$row['gks_nickname_aade'].'</a>';?></span></div>
          </div>
          <?php } ?>
          <?php if (empty($row['aade_xml_send'])==false) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Απεσταλμένο XML');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (trim_gks($row['aade_xml_send'])!='') {
                $local_file=GKS_FileServerShare.'acc/inv/'.$id.'/aade_mydata/'.$row['aade_xml_send'];
                if (file_exists($local_file)) {
                  
                  $url_file='admin-get-file.php?fs=fileservers&file=acc%2Finv%2F'.$id.'%2Faade_mydata%2F'.urlencode($row['aade_xml_send']);
                  echo '<a href="'.$url_file.'" target="_blank">'.$row['aade_xml_send'].'</a> ';
                  echo '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
                }
              }
              ?></span></div>
          </div>
          <?php } ?>
          <?php if (empty($row['aade_xml_response'])==false) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Απάντηση XML');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (trim_gks($row['aade_xml_response'])!='') {
                $local_file=GKS_FileServerShare.'acc/inv/'.$id.'/aade_mydata/'.$row['aade_xml_response'];
                if (file_exists($local_file)) {
                  
                  $url_file='admin-get-file.php?fs=fileservers&file=acc%2Finv%2F'.$id.'%2Faade_mydata%2F'.urlencode($row['aade_xml_response']);
                  echo '<a href="'.$url_file.'" target="_blank">'.$row['aade_xml_response'].'</a> ';
                  echo '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
                }
              }
              ?></span></div>
          </div>
          <?php } ?>
          <?php if ($row['aade_paroxos_id']>0) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height" style="height: unset;"><?php echo gks_lang('Λήψη αρχείων από πάροχο');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (isset($row['paroxos_get_files'])) echo showDate(strtotime($row['paroxos_get_files']), 'd/m/Y H:i', 1);  
            ?>
            <span id="paxoros_check_files" class="btn1 btn-sm1 btn-primary1 tooltipster" title="<?php echo gks_lang('Λήψη αρχείων από πάροχο');?>"><i class="fas fa-sync"></i></span>
            </span></div>
          </div> 
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height" style="height: unset;"><?php echo gks_lang('Αποστολή pdf σε πάροχο');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (isset($row['paroxos_send_pdf'])) {
                echo showDate(strtotime($row['paroxos_send_pdf']), 'd/m/Y H:i', 1);  
              } else {?>
                <span id="paxoros_send_pdf" class="btn1 btn-sm1 btn-primary1 tooltipster" title="<?php echo gks_lang('Αποστολή αρχείου pdf σε πάροχο');?>"><i class="fas fa-upload"></i></span><?php
              }?>
              
              </span>
            </div>
          </div> 
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height" style="height: unset;"><?php echo gks_lang('pdf σε πάροχο');?>:</label>
            <div class="col-sm-8 gks_unset_height"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (isset($row['paroxos_send_pdf_name']) and isset($row['paroxos_send_pdf_url'])) {
                echo '<a href="'.$row['paroxos_send_pdf_url'].'" target="_blank">'.$row['paroxos_send_pdf_name'].' <i class="fas fa-file-pdf" style="color:#b30b00"></i></a>';
              }
            ?></span></div>
          </div> 
          
                   
          <?php } ?>

          
          <?php
          
          if ($aade_errors!='') {
          ?>
          <div class="row">
            <div class="col-sm-12 text-center1">
              <?php echo $aade_errors;?>
            </div>
          </div>
          <?php } ?>

        </div>
      </div>
      <?php } ?>
      
      <?php
      if (intval($row['seira_isdeliverynote'])!=0 and 
         (trim_gks($row['aade_invoicemark']) != '' or trim_gks($row['aade_qrurl']) != '')) {?>      
      <div class="card gks_card_expand" id="gks_aade">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('ΑΑΔΕ Ψηφιακό δελτίο αποστολής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('aadedn');?>>
          <?php
          $aade_delivery_note_url='admin-aade-delivery-note.php?cmd=status';
          if ($row['aade_invoicemark']!='')
            $aade_delivery_note_url.='&mark='.rawurlencode($row['aade_invoicemark']);
          if ($row['aade_qrurl']!='')
            $aade_delivery_note_url.='&qrurl='.rawurlencode($row['aade_qrurl']);
                      
          $id_aade_delivery_note=0;$vat_issuer='';$vat_issuer_descr='';$vat_customer='';$vat_customer_descr='';
          $last_state='';$last_date_get_data='';$last_raw_data='';
          $gks_aade_delivery_note_recs=0;
          $sql_adn=[];
          if ($row['aade_invoicemark']!='') $sql_adn[]=" (mark like '".$db_link->escape_string($row['aade_invoicemark'])."') ";
          if ($row['aade_qrurl']!='') $sql_adn[]=" (qrUrl like '".$db_link->escape_string($row['aade_qrurl'])."') ";
          $sql_adn="SELECT * FROM gks_aade_delivery_note WHERE ".implode(' or ',$sql_adn)."
          order by id_aade_delivery_note desc limit 1";
          $result_adn = $db_link->query($sql_adn);        
          if (!$result_adn) debug_mail(false,'error sql',$sql_adn);
          if (!$result_adn) die('sql error');
          if ($result_adn->num_rows==1) {
            $row_adn = $result_adn->fetch_assoc();
            $id_aade_delivery_note=intval($row_adn['id_aade_delivery_note']);
            $vat_issuer=trim_gks($row_adn['vat_issuer']);
            $vat_customer=trim_gks($row_adn['vat_customer']);
            $last_state=trim_gks($row_adn['last_state']);
            $last_date_get_data=trim_gks($row_adn['last_date_get_data']);
            $last_raw_data=trim_gks($row_adn['last_raw_data']);
          }
          if ($id_aade_delivery_note>0) {
            $sql_adn="select count(*) as cc from gks_aade_delivery_note_log where aade_delivery_note_id=".$id_aade_delivery_note;
            $result_adn = $db_link->query($sql_adn);        
            if (!$result_adn) debug_mail(false,'error sql',$sql_adn);
            if (!$result_adn) die('sql error');
            if ($result_adn->num_rows==1) {
              $row_adn = $result_adn->fetch_assoc();
              $gks_aade_delivery_note_recs=intval($row_adn['cc']);
            }        
          }
          
          ?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-sm-8 gks_unset_height">
              <span class="form-control-plaintext form-control-sm gks_unset_height">
                <span class="aade_delivery_status_<?php echo $last_state;?>" id="gsdn_status">
                  <?php echo getAADE_InvoiceDeliveryStatus($last_state);?>
                </span>
              </span>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Ημερομηνία ενημέρωσης');?>:</label>
            <div class="col-sm-8 gks_unset_height">
              <span class="form-control-plaintext form-control-sm gks_unset_height" id="gsdn_date">
                <?php if (!empty($last_date_get_data)) echo showDate(strtotime($last_date_get_data), 'd/m/Y H:i', 1);?>
              </span>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Πληροφορίες κατάστασης');?>:</label>
            <div class="col-sm-8 gks_unset_height">
              <span class="form-control-plaintext form-control-sm gks_unset_height" id="gsdn_html_data">
                <?php 
                if ($last_raw_data!='') {
                  $ret_parse=gks_aade_delivery_note_parse_xml_status($last_raw_data);
                  if ($ret_parse['success']==false) {
                    echo $ret_parse['message'];
                  } else {
                    echo $ret_parse['html'];
                  }
                }
                ?>
              </span>
            </div>
          </div>
          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Εκδότης');?>:</label>
            <div class="col-sm-8 gks_unset_height">
              <span class="form-control-plaintext form-control-sm gks_unset_height" id="gsdn_vat_issuer">
                <?php echo $vat_issuer;
                if ($vat_issuer!='') echo ' '.gks_get_user_from_afm($vat_issuer);
                ?>
              </span>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Πελάτης');?>:</label>
            <div class="col-sm-8 gks_unset_height">
              <span class="form-control-plaintext form-control-sm gks_unset_height" id="gsdn_vat_customer">
                <?php echo $vat_customer;
                if ($vat_customer!='') echo ' '.gks_get_user_from_afm($vat_customer);
                ?>
              </span>
            </div>
          </div>
          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><?php echo gks_lang('Πλήθος ιστορικού');?>:</label>
            <div class="col-sm-8 gks_unset_height">
              <span class="form-control-plaintext form-control-sm gks_unset_height" id="gsdn_records_cc">
                <?php echo $gks_aade_delivery_note_recs;?>
              </span>
              
            </div>
          </div>
                    
          <div class="row">
            <div class="col-sm-12 gks_unset_height text-center">
              <?php
              $gsdn_cid=$row['company_id'].'|'.$row['company_sub_id'];
              $gsdn_issuerVatNumber=''; 
              if ($vat_issuer!='') {
                $gsdn_issuerVatNumber=$vat_issuer;
              } else {
                
              }
              
              ?>
              <button id="gks_get_live_status_delivery_note" 
                data-cid="<?php echo $gsdn_cid;?>" 
                data-mark="<?php echo trim_gks($row['aade_invoicemark']);?>" 
                data-issuerVatNumber="<?php echo $gsdn_issuerVatNumber;?>" 
                class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i> <?php echo gks_lang('Ενημέρωση Κατάστασης');?></button>
              <a href="<?php echo $aade_delivery_note_url;?>" class="btn btn-sm btn-primary"><i class="fas fa-history"></i> <?php echo gks_lang('Ενέργειες');?> / <?php echo gks_lang('Ιστορικό');?></a>
            </div>
          </div>
          
          
        </div>
      </div>            
      <?php } ?>
            
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιστορικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('hist');?>>
        <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th class="table-dark" scope="col" width="0%" nowrap>#</th>
              <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
              <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>
              <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Τι');?></th>
            </tr>
          </thead>  
          <tbody> 
            
          <?php
          $sql_log="SELECT gks_acc_inv_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_acc_inv_log LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_acc_inv_log.acc_inv_id=".$id."
          ORDER BY gks_acc_inv_log.id_acc_inv_log DESC;";
          $result_log = $db_link->query($sql_log);        
          if (!$result_log) debug_mail(false,'error sql',$sql_log);
          if (!$result_log) die('sql error');
          
          $j = 0;
          while ($row_log = $result_log->fetch_assoc()) {
            $j++; ?>
        
          <tr>
            <th scope="row" align="center"><?php echo $j;?></th>
            <td align="left"><?php echo showDate(strtotime($row_log['add_date']), 'd/m/Y H:i:s', 1);?></td>  
            <td align="left"><?php echo $row_log['gks_nickname'];?></td>  
            <td align="left"><?php echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_log['sxolio']);?></td>    
          </tr>
          <?php } ?>                      
          </tbody>   
          </table>
        </div>                                   
      </div>
     
      <?php } ?>
     
      

      
      
      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?> >       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_acc_inv']>0) echo $row['id_acc_inv'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('GUID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['inv_guid'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>';?></span></div>
          </div>
          <?php if ($row['pos_id']>0) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εντατική Λιανική');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-pos-item.php?id='.$row['pos_id'].'">'.$row['pos_name'].'</a>';?></span></div>
          </div>          
          <?php } ?>
          <?php if ($row['erp_app_mobile_id']>0) {?>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">gks ERP App Mobile:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-erp-app-mobile-item.php?id='.$row['erp_app_mobile_id'].'">'.$row['erp_app_mobile_name'].'</a>';?></span></div>
          </div>          
          <?php } ?>

          
          
        </div>      
      </div>              


    
    </div>
  </div>
</div>


<div id="dialog_gsis" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?></div>
    </div>
    
    <div class="form-group row">  
      <label for="dialog_gsis_afm" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
      <div class="col-sm-4">
         <input id="dialog_gsis_afm" type="text" class="form-control form-control-sm" value="" >
      </div>
      <div class="col-sm-4">
         <button style="" id="dialog_gsis_run" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση');?></button>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_gsis_html">
        
      </div>
    </div>
    
  </div>
</div>

<div id="dialog_user_save" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Προσθήκη ή επιλογή επαφής');?></div>
    </div>
    <div class="form-group row">  
      <div style="font-size: 100%;text-align:center;width: 100%;">
        <?php echo gks_lang('Βρέθηκαν οι παρακάτω επαφές στο σύστημα');?>
        <?php echo gks_lang('Η αναζήτηση έγινε με βάση το σχετικό πεδίο που αναφέρεται στην στήλη <b>Αναζήτηση</b>.');?>
        <?php echo gks_lang('Μήπως η επαφή που θέλετε να προσθέσετε είναι μία από τις παρακάτω;');?>
        <?php echo gks_lang('Εάν <b>ναι</b>, τότε επιλέξτε την.');?>
        <?php echo gks_lang('Εάν <b>όχι</b>, τότε μπορείτε να προσθέσετε την νέα επαφή επιλέγοντας την επιλογή <b>Προσθήκη νέας επαφής</b>');?>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" style="text-align: center !important;">
        <input type="radio" name="dialog_user_save_radio" id="dialog_user_save_radio_new" value="-1">  <label class="gks_label" for="dialog_user_save_radio_new"><?php echo gks_lang('Προσθήκη νέας επαφής');?>:</label>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_user_save_html">
        
      </div>
    </div>
  
  </div>
</div>

<?php include_once 'admin-obj-send-message.php'; ?>


<div id="dialog_print" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group1 row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Ρυθμίσεις Εκτύπωσης');?></div>
    </div>
        
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Τύπος');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_pdf"  value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_pdf" style="display:inline;padding-right:18px;cursor: pointer;">pdf
          <i class="fas fa-file-pdf tooltipster" title="pdf" style="color:#fa0f00;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_html" value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_html" style="display:inline;padding-right:18px;cursor: pointer;">html
          <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_jpg" value="3" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_jpg" style="display:inline;padding-right:18px;cursor: pointer;">jpg
          <img src="img/jpg21.png" class="tooltipster" title="jpg" style="height:15px;vertical-align: top;"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Προσανατολισμός');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Κατακόρυφος');?>
          <i class="fas fa-portrait tooltipster" title="Portrait" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Οριζόντιος');?>
          <i class="fas fa-image tooltipster" title="Landscape" style="color:#4e4e4e;font-size:120%"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Χρώμα ή Γκρι');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Με χρώμα');?>
          <img src="img/palette-color.png" border="0" width="15" style="vertical-align: top;">
          </label>
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Γκρι');?>
          <img src="img/palette-gray.png" border="0" width="15" style="vertical-align: top;">
          </label>
      </div>
    </div>    

    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Μεγέθυνση');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left">
        <div id="dialog_print_zoom_slider" style="padding-top: 18px;width: calc(100% - 50px);    margin-left: 25px;">
          <div id="dialog_print_zoom_slider_handle" class="ui-slider-handle"></div>
        </div>
      </div>
    </div>

    
    <div class="row" >
      <div class="gks_print_thump_container">
<?php
  $user_def_form_id=0;
  if (isset($gks_user_settings['print']['form_id_inv'])) $user_def_form_id=intval($gks_user_settings['print']['form_id_inv']);
  
  $sql_print_forms="SELECT gks_print_forms.*, gks_lang.lang_name
  FROM ((gks_print_objects 
  LEFT JOIN gks_print_objects_forms ON gks_print_objects.id_print_object = gks_print_objects_forms.print_object_id) 
  LEFT JOIN gks_print_forms ON gks_print_objects_forms.print_form_id = gks_print_forms.id_print_form)
  LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  WHERE gks_print_forms.id_print_form is not null and gks_print_forms.is_disable=0 AND gks_print_objects.object_name='gks_acc_inv'
  ".(count($perm_id_print_forms)>0 ? " and gks_print_forms.id_print_form in (".implode(',',$perm_id_print_forms).")" : '')."
  order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";

  $perm_print_forms=array();
  
  $result_print_forms = $db_link->query($sql_print_forms);        
  if (!$result_print_forms) {debug_mail(false,'error sql',$sql_print_forms);die('sql error');}
  while ($row_print_forms = $result_print_forms->fetch_assoc()) {
    //print $row_print_forms['id_print_form'].' '.$row_print_forms['file_thump_url'].'<br>';
    
    $print_form_descr=trim_gks($row_print_forms['print_form_descr']);
    $print_lang_name=trim_gks($row_print_forms['lang_name']);
    $file_thump_url=trim_gks($row_print_forms['file_thump_url']);
    if ($file_thump_url=='') $file_thump_url='img/print_form_empty.png';
    
    $perm_company_ids=trim_gks($row_print_forms['perm_company_ids']);
    $perm_acc_journal_ids=trim_gks($row_print_forms['perm_acc_journal_ids']);
    $perm_acc_seires_ids=trim_gks($row_print_forms['perm_acc_seires_ids']);

    $temp=array('id'=>intval($row_print_forms['id_print_form']));
    if ($perm_company_ids!='') $temp['perm_company_ids']=unserialize($perm_company_ids);
    if ($perm_acc_journal_ids!='') $temp['perm_acc_journal_ids']=unserialize($perm_acc_journal_ids);
    if ($perm_acc_seires_ids!='') $temp['perm_acc_seires_ids']=unserialize($perm_acc_seires_ids);
    $perm_print_forms[]=$temp;
    
    $div_form='<div class="gks_print_thump_div '.
      ($user_def_form_id==$row_print_forms['id_print_form'] ? 'gks_print_thump_div_selected' : '').
      '" data-form_id="'.$row_print_forms['id_print_form'].'" '.
      'data-lang="'.$row_print_forms['gks_lang'].'" '.
      'data-file_type="'.$row_print_forms['file_type'].'" '.
      'data-landscape="'.$row_print_forms['is_landscape'].'" '.
      'data-grayscale="'.$row_print_forms['grayscale'].'" '.
      'data-zoom="'.intval($row_print_forms['zoom']*100).'" '.
      '>';
      $div_form.='<div class="gks_print_thump_title">'.$print_form_descr.'</div>';
      $div_form.='<div class="gks_print_thump_lang">'.$print_lang_name.'</div>';
      $div_form.='<img src="'.$file_thump_url.'" class="gks_print_thump_img" border="0"/>';
      
    
    $div_form.='</div>';
    echo $div_form;
  }
  
  $div_form='<div id="gks_print_thump_more_div">';
    $div_form.='<div id="gks_print_thump_more_text"><i class="fas fa-plus-circle" style="font-size:200%;color:#35dc35;"></i><br>'.gks_lang('Εμφάνιση όλων').'</div>';
  $div_form.='</div>';
  echo $div_form;
  

?>      
      </div>
    </div>
<?php
  $html_paroxos_pdf='';
  $html_erp_app_id='';
  if ($paroxos_status==1 and $aade_paroxos_id>0 and $paroxos_send_pdf=='' and
      ($curr_inv_state=='090ekdosi' or $curr_inv_state=='100payment')) {

    $html_paroxos_pdf=
    '<div class="col-sm-6 form-control-sm text-sm-left">
       <input id="gks_paroxos_send_pdf" type="checkbox" class="form-control form-control-sm" value="1" checked>
       <label for="gks_paroxos_send_pdf" style="margin: 0px;position: relative;top: 2px;font-size: 0.8rem;"> '.gks_lang('Αποστολή στον πάροχο').'</label>
       <i class="fas fa-info-circle tooltipster" title="'.gks_lang('Αποδοχή μόνο αρχείων pdf').'" style="font-size: 150%;position: relative;top: 4px;"></i>
     </div>';
  }

  $erp_app_id=0;
  if ($inv_acc_seira_id>0) {
    $sql_send_erp_app="SELECT gks_acc_seires.id_acc_seira, gks_acc_seires.erp_app_id, gks_acc_seires.erp_app_dest,  
    gks_acc_seires.erp_app_dest_printer, 
    gks_acc_seires.erp_app_dest_printer_method,
    gks_acc_seires.erp_app_dest_printer_lpr_ip,
    gks_acc_seires.erp_app_dest_printer_copies, 
    gks_acc_seires.erp_app_dest_folder, 
    gks_erp_app.id_erp_app, gks_erp_app.erp_app_name, gks_erp_app.erp_app_last_ping
    FROM gks_acc_seires 
    LEFT JOIN gks_erp_app ON gks_acc_seires.erp_app_id = gks_erp_app.id_erp_app
    where gks_acc_seires.id_acc_seira=".$inv_acc_seira_id;
    
    $result_send_erp_app = $db_link->query($sql_send_erp_app);        
    if (!$result_send_erp_app) {debug_mail(false,'error sql',$sql_send_erp_app);die('sql error');}
    if ($result_send_erp_app->num_rows==1) {
      $row_send_erp_app = $result_send_erp_app->fetch_assoc();
      $erp_app_id=$row_send_erp_app['erp_app_id'];
      

      $send_erp_app_tooltip='';
      $send_erp_app_tooltip.=gks_lang('gks ERP App Desktop').': '.trim_gks($row_send_erp_app['erp_app_name']).'<br>';
      if ($row_send_erp_app['erp_app_dest']=='printer') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Εκτυπωτής').'<br>';
        $send_erp_app_tooltip.=gks_lang('Μέθοδος').': '.erp_app_dest_printer_method_descr($row_send_erp_app['erp_app_dest_printer_method']).'<br>';
        if (in_array($row_send_erp_app['erp_app_dest_printer_method'],[0,1])) $send_erp_app_tooltip.=gks_lang('Εκτυπωτής').': '.trim_gks($row_send_erp_app['erp_app_dest_printer']).'<br>';
        if (in_array($row_send_erp_app['erp_app_dest_printer_method'],[2]))   $send_erp_app_tooltip.=gks_lang('IP εκτυπωτή').': '.trim_gks($row_send_erp_app['erp_app_dest_printer_lpr_ip']).'<br>';
        $send_erp_app_tooltip.=gks_lang('Αντίτυπα').': '.trim_gks($row_send_erp_app['erp_app_dest_printer_copies']);
      } else if ($row_send_erp_app['erp_app_dest']=='folder') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Φάκελος').'<br>';
        $send_erp_app_tooltip.=gks_lang('Φάκελος').': '.trim_gks($row_send_erp_app['erp_app_dest_folder']);
      }     
      $send_erp_app_checkbox_disable=true;
      if (isset($row_send_erp_app['erp_app_last_ping'])) {
        if (strtotime($row_send_erp_app['erp_app_last_ping']) > time()-15*60) {
          $send_erp_app_tooltip.= '<br>'.gks_lang('Τελευταία σύνδεση εφαρμογής').':<br><span class=gks_erp_app_alive>'.secondsago(strtotime($row_send_erp_app['erp_app_last_ping'])).'</span>';
          $send_erp_app_checkbox_disable=false;
        } else {
          $send_erp_app_tooltip.= '<br>'.gks_lang('Τελευταία σύνδεση εφαρμογής').':<br><span class=gks_erp_app_not_alive>'.secondsago(strtotime($row_send_erp_app['erp_app_last_ping'])).'</span>';
        }
      }
      
    }
  }
  
  if ($erp_app_id>0) {
    $html_erp_app_id=
    '<div class="col-sm-6 form-control-sm text-sm-left">
       <input id="gks_print_send_gks_erp_app" type="checkbox" class="form-control form-control-sm switchery1_sel" value="1" '.($send_erp_app_checkbox_disable ? 'disabled' : 'checked').'>
       <label for="gks_print_send_gks_erp_app" style="margin: 0px;position: relative;top: 2px;font-size: 0.8rem;"> '.gks_lang('Αποστολή στην εφαρμογή gks ERP App Desktop').'</label>
       <i class="fas fa-info-circle tooltipster" title="'.$send_erp_app_tooltip.'" style="font-size: 150%;position: relative;top: 4px;"></i>
     </div>';
  }
  if ($html_paroxos_pdf !='' or $html_erp_app_id!='') {
    echo '<div class="row">';
    if ($html_paroxos_pdf=='') echo '<div class="col-sm-6"></div>'; else echo $html_paroxos_pdf;
    if ($html_erp_app_id=='')  echo '<div class="col-sm-6"></div>'; else echo $html_erp_app_id;
    echo '</div>';
  }
  
?>    
  


  </div>  
</div>


<div id="dialog_aade" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Αποστολή στην ΑΑΔΕ μέσω myData');?></div>
    </div>
        

    <div class="form-group row"  id="div_aade_mydata_live"  style="">
      <label for="aade_mydata_live" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πραγματική αποστολή');?>:
        <small>
        <?php echo gks_lang('Εφόσον είναι ενεργοποιημένη η <b>Πραγματική αποστολή</b> και στην εταιρεία');?>
        </small>
      </label>
      <div class="col-md-8">
        <input type="checkbox" id="aade_mydata_live" value="1" class="switchery1_sel">
        <small class="form-text text-muted" style="">
          <?php 
          echo gks_lang('Όταν <strong style="font-size:150%;">δεν είναι</strong> ενεργοποιημένη η επιλογή <strong>"Πραγματική αποστολή"</strong>, τότε το παραστατικό θα αποσταλεί σε ένα άλλο δοκιμαστικό σύστημα της ΑΑΔΕ απλά και μόνο για έλεγχο της σύνταξης του παραστατικού και το παραστατικό είναι σαν να μην το στείλατε ποτέ.');
          echo '<br>';
          echo gks_lang('Όταν <strong style="font-size:150%;">είναι</strong> ενεργοποιημένη η επιλογή <strong>"Πραγματική αποστολή"</strong> τότε το παραστατικό θα αποσταλεί στην ΑΑΔΕ.');
          ?>
        </small>
      </div>
    </div>

  </div>
</div>    

<div id="dialog_paroxos" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Αποστολή στον πάροχο');?></div>
    </div>
        

    <div class="form-group row"  id="div_paroxos_mydata_live"  style="">
      <label for="paroxos_mydata_live" class="gks_unset_height col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πραγματική αποστολή');?>:
        <small>
        <?php echo gks_lang('Εφόσον είναι ενεργοποιημένη η <b>Πραγματική αποστολή</b> και στην εταιρεία');?>
        </small>
      </label>
      <div class="col-md-8">
        <input type="checkbox" id="paroxos_mydata_live" value="1" class="switchery1_sel">
        <small class="form-text text-muted" style="">
          <?php
          echo gks_lang('Όταν <strong style="font-size:150%;">δεν είναι</strong> ενεργοποιημένη η επιλογή <strong>"Πραγματική αποστολή"</strong>, τότε το παραστατικό θα αποσταλεί σε ένα άλλο δοκιμαστικό σύστημα του παρόχου απλά και μόνο για έλεγχο της σύνταξης του παραστατικού και το παραστατικό είναι σαν να μην το στείλατε ποτέ.');
          echo '<br>';
          echo gks_lang('Όταν <strong style="font-size:150%;">είναι</strong> ενεργοποιημένη η επιλογή <strong>"Πραγματική αποστολή"</strong> τότε το παραστατικό θα αποσταλεί στον πάροχο.');
          ?>

        </small>
      </div>
    </div>

  </div>
</div> 

<div id="dialog_add_product_variables">
  <div id="dialog_add_product_variables_inner">
    <div id="dialog_add_product_variables_header">
      <span><?php echo gks_lang('Προσθήκη μεταβλητού είδους με ποσότητες');?></span>
    </div>
    <i class="fas fa-window-close" id="dialog_add_product_variables_close"></i>    
    <div id="dialog_add_product_variables_data">
      <div class="dialog_add_product_variables_data2 container-fluid">
        <div class="form-group row row_gks_multi_copies_enable">
          <label for="dialog_add_product_variables_product_id" class="col-md-2 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Είδος');?>:</label>
          <div class="col-md-2">
            <input id="dialog_add_product_variables_product_id" type="text" class="form-control form-control-sm" value="" 
            style="width:calc(98% - 22px);display:inline;" 
            placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
            data-id="0">
            <a id="autocomplete_dialog_add_product_variables_product_id" tabindex="-1" href="admin-products-item.php?id=0" style="display:none"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή είδους');?>"></i></a>
          </div>
          
          <label for="dialog_add_product_row" class="col-md-2 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Γραμμή');?>:</label>
          <div class="col-md-2">
            <select id="dialog_add_product_row" class="form-control form-control-sm">
              <option value="0"><?php echo gks_lang('Κενό');?></option>
            </select>
          </div>          

          <label for="dialog_add_product_col" class="col-md-2 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Στήλη');?>:</label>
          <div class="col-md-2">
            <select id="dialog_add_product_col" class="form-control form-control-sm">
              <option value="0"><?php echo gks_lang('Κενό');?></option>
            </select>
          </div>
        </div>
        <div class="form-group row" id="row_gks_multi_copies_enable_others">
          <label class="col-md-2 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Άλλες ιδιότητες');?>:</label>
          <div class="col-md-10" id="dialog_add_product_others">
            other attrs
          </div>          
        </div>
        
        <div class="col-md-12" id="dialog_add_product_variables_table">

         
                  

          
        </div>
      </div>
    
      <div class="dialog_add_product_variables_footer">
        
        <button class="btn btn-primary" id="dialog_add_product_variables_save"><?php echo gks_lang('Εφαρμογή');?></button>
        <button class="btn btn-danger" id="dialog_add_product_variables_cancel"><?php echo gks_lang('Ακύρωση');?></button>
      </div>
    
    </div>    
    
  </div>
</div>

<?php include_once('admin-eftpos-transaction-dialog.php');

$sql_ppm="SELECT viva_preferred_payment_methods, mellon_preferred_payment_methods, 
cardlink_preferred_payment_methods, epay_preferred_payment_methods, 
worldline_preferred_payment_methods,nexi_preferred_payment_methods
FROM gks_acc_inv LEFT JOIN gks_company ON gks_acc_inv.company_id = gks_company.id_company
WHERE gks_acc_inv.id_acc_inv=".$id." AND gks_company.id_company Is Not Null";
$result_ppm = $db_link->query($sql_ppm);        
if (!$result_ppm) {debug_mail(false,'error sql',$sql_ppm);die('sql error');}
$preferred_payment_methods=array(); 
while ($row_ppm = $result_ppm->fetch_assoc()) {
  $temp=trim_gks($row_ppm['viva_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['viva']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['mellon_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['mellon']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['cardlink_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['cardlink']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['epay_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['epay']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['worldline_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['worldline']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['nexi_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['nexi']=json_decode($temp,true);
  
  //print '<pre>';print_r($preferred_payment_methods);die();
  break;
}
?>


<?php



//print '<pre>';
//print_r($mybasketarray);
//print '</pre>';

?>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_acc_inv';
var from_php_activity_model='gks_acc_inv';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;
var from_php_template_id=<?php echo $template_id;?>;
var from_php_gks_lock=<?php echo ($gks_lock ? 'true' : 'false');?>;
var from_php_number_gks_lock=<?php echo ($gks_number_lock ? 'true' : 'false');?>;
var from_php_user_gks_lock=<?php echo ($gks_user_lock ? 'true' : 'false');?>;


var last_aa=<?php echo $aa;?>;
var last_oeaa=<?php echo $entity_aa;?>;
var last_coiaa=<?php echo $coi_aa;?>;
var last_mcmaa=<?php echo $mcm_aa;?>;
var last_pdeaa=<?php echo $pde_aa;?>;
var from_php_packagingTypes=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($packagingTypes));?>'));

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 
var from_php_need_download_timer='<?php echo $need_download_timer;?>';
  


var from_php_GKS_ACC_INV_COL_ITEMPRICE=<?php echo ($GKS_ACC_INV_COL_ITEMPRICE? 'true' : 'false')?>;
var from_php_GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA=<?php echo ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA? 'true' : 'false')?>;
var from_php_GKS_ACC_INV_COL_FPA=<?php echo ($GKS_ACC_INV_COL_FPA? 'true' : 'false')?>;
var from_php_GKS_ACC_INV_EXTRA_OPEN=<?php echo ($GKS_ACC_INV_EXTRA_OPEN? 'true' : 'false')?>;







var from_php_gkscols1='<?php echo $gkscols1;?>';
var from_php_gkscols2='<?php echo $gkscols2;?>';
var from_php_gkscols3='<?php echo $gkscols3;?>';
var from_php_gkscols4='<?php echo $gkscols4;?>';
var from_php_gkscols5='<?php echo $gkscols5;?>';
var from_php_gkscols6='<?php echo $gkscols6;?>';
var from_php_gkscols7='<?php echo $gkscols7;?>';
var from_php_gkscols8='<?php echo $gkscols8;?>';
var from_php_gkscols9='<?php echo $gkscols9;?>';
var from_php_gkscols10='<?php echo $gkscols10;?>';

var from_php_delivery_way_default=<?php echo $gks_user_settings['gks_acc_inv']['tropos_apostolis'];?>;
var from_php_payment_way_default=<?php echo $gks_user_settings['gks_acc_inv']['tropos_pliromis'];?>;

var kostos_apostolis_mode='<?php if ($id>0) echo "manual";?>';
var kostos_pliromis_mode='<?php if ($id>0) echo "manual";?>';

var coupons_array = JSON.parse('<?php echo json_encode($mybasketarray['coupons']);?>');
//console.log(coupons_array);

var from_php_monades = [];
<?php foreach ($monades as $monada) {
  echo 'from_php_monades.push({id: '.$monada['id'].', descr: $.base64.decode("'.base64_encode($monada['descr']).'"), symbol: $.base64.decode("'.base64_encode($monada['symbol']).'")});'."\n";
}?>
//console.log(from_php_monades);

var from_php_fpa_list = [];
<?php foreach ($fpa_list as $fpa_item) {
  echo 'from_php_fpa_list.push({id: '.$fpa_item['id'].', descr: $.base64.decode("'.base64_encode($fpa_item['descr']).'")});'."\n";
}?>
var from_php_fpa_aade = [];
<?php foreach ($fpa_aade as $fpa_item) {
  echo 'from_php_fpa_aade.push({id: '.$fpa_item['id'].', descr: $.base64.decode("'.base64_encode($fpa_item['descr']).'")});'."\n";
}?>



var fields_change=[];
<?php 
foreach ($fields_change as $field_aa => $field_name) {
  echo "fields_change[".$field_aa."]='".$field_name."';";
} ?>
//console.log(fields_change);

var from_php_inv_state='<?php echo $inv_state;?>';
var from_php_acc_eidos_parastatikou_id=<?php echo $acc_eidos_parastatikou_id;?>;
var from_php_eidos_parastatikou_type_id=<?php echo $eidos_parastatikou_type_id;?>;
var from_php_eidos_parastatikou_need_prev=<?php echo $eidos_parastatikou_need_prev;?>;
var from_php_eidos_parastatikou_has_fpa=<?php echo $eidos_parastatikou_has_fpa;?>;
var from_php_eidos_parastatikou_has_othertaxes='<?php echo $eidos_parastatikou_has_othertaxes;?>';
var from_php_eidos_parastatikou_has_esoda=<?php echo $eidos_parastatikou_has_esoda;?>;
var from_php_eidos_parastatikou_has_eksoda=<?php echo $eidos_parastatikou_has_eksoda;?>;
var from_php_eidos_parastatikou_need_afm=<?php echo $eidos_parastatikou_need_afm;?>;
var from_php_eidos_parastatikou_balance_pros=<?php echo $eidos_parastatikou_balance_pros;?>;
var from_php_whi_eidos_parastatikou_stock_pros=<?php echo $whi_eidos_parastatikou_stock_pros_org;?>;
var from_php_whi_eidos_parastatikou_type_id=<?php echo $whi_eidos_parastatikou_type_id_org;?>;
var from_php_acc_eidos_parastatikou_other_entity=<?php echo $acc_eidos_parastatikou_other_entity;?>;
var from_php_journal_has_correlated_invoices=<?php echo $journal_has_correlated_invoices;?>;
var from_php_journal_has_multiple_connected_marks=<?php echo $journal_has_multiple_connected_marks;?>;
var from_php_journal_has_packings_declarations=<?php echo $journal_has_packings_declarations;?>;
var from_php_seira_isdeliverynote=<?php echo $seira_isdeliverynote;?>;



var from_php_print_def_file_type='<?php echo (isset($gks_user_settings['print']['file_type']) ? $gks_user_settings['print']['file_type'] : 'pdf');?>';
var from_php_print_def_grayscale=<?php echo (isset($gks_user_settings['print']['grayscale']) ? $gks_user_settings['print']['grayscale'] : 'false');?>;
var from_php_print_def_landscape=<?php echo (isset($gks_user_settings['print']['landscape']) ? $gks_user_settings['print']['landscape'] : 'false');?>;
var from_php_print_def_zoom=<?php echo (isset($gks_user_settings['print']['zoom']) ? $gks_user_settings['print']['zoom'] : '100');?>;;
var from_php_print_def_form_id=<?php echo (isset($gks_user_settings['print']['form_id_inv']) ? $gks_user_settings['print']['form_id_inv'] : '0');?>;;
var from_php_print_def_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_user_settings['print']['forms_inv']));?>'));

var from_php_is_cancelfor=<?php echo ($cancel_for_acc_inv_id ? 'true' : 'false');?>;
var from_php_is_credit_memo=<?php echo ($credit_memo_for_acc_inv_id ==0 ? 'false' : 'true');?>;



var from_php_enter_order=[];
<?php
if (isset($gks_user_settings['gks_acc_inv']['enter_order']) and is_array($gks_user_settings['gks_acc_inv']['enter_order'])) {
  foreach ($gks_user_settings['gks_acc_inv']['enter_order'] as $value) {
    echo 'from_php_enter_order.push(\''.$value.'\');'."\n";
  } 
}
?>

var from_php_dialog_item_message_email_from_array=[];
<?php 
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_inv','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_inv','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_inv','delete',$id);?>;



<?php if (is_array($from_aade_import_json)) { ?>
var from_php_from_aade_import_json=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($from_aade_import_json));?>'));
<?php } ?>

var from_php_perm_print_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($perm_print_forms));?>'));

var from_php_check_vies_valid_wait=<?php echo ($mybasketarray['check_vies']['valid']==2 ? 'true' : 'false');?>;
var from_php_payments_lock_level=<?php echo $payments_lock_level;?>;
var from_php_preferred_payment_methods = JSON.parse('<?php echo json_encode($preferred_payment_methods);?>');


var from_php_gks_deductionsSelection_tags = ['MARKETING'];
 
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});



</script>  

<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>gks_country.js"></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>admin-acc-inv-item.js"></script>
<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>
<?php echo gks_lang_big_texts('admin-acc-inv-item');?>
<script src="js/admin-acc-inv-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-eftpos-transaction-dialog.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

echo gks_from_googlemaps_scripts();

gks_plugins_functions_run('admin_acc_inv_item_scripts_before_footer',array(
  'id'=>&$id,
));

include_once('_my_footer_admin.php');


