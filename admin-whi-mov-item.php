<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_gks_whi_mov_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov','view',0);
$perm_gks_whi_mov_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov','edit',0);
$perm_gks_whi_mov_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov','add',0);
$perm_gks_whi_mov_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov','delete',0);

$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');

$gks_voip_params=gks_voip_user_params();

//gks_monada_convert(53,56,$out,array());
//print '<pre>';print_r($out);die();




//print '<pre>';
//$out=array();
//gks_monada_convert(6, 8, $out,array());
//print_r($out);
//die();











if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

if ($id==-1) {
  $nav_active_array=array('warehouse','warehouse_mov_new');  
} else {
  $nav_active_array=array('warehouse','warehouse_mov');
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





//print '<pre>';
//print_r($user_companys);
//die();


$gks_custom_prepare = gks_custom_table_item_prepare('gks_whi_mov',['from'=>'item']);

$template_id=0; if (isset($_GET['template_id'])) $template_id=intval($_GET['template_id']);
if ($id==-1) {

  if ($template_id>0) {
    $sql=select_gks_whi_mov()." where gks_whi_mov.id_whi_mov = ".$template_id;
    if (count($perm_id_company_ids)>0) $sql.=" and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")";
    if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
    if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
    if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";

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

  
      $row['id_whi_mov']=-1;
      $row['cancel_for_whi_mov_id']=0;
      $row['credit_memo_for_whi_mov_id']=0;
      $row['from_aade_import']='';
      $row['import_mov_whi_seira_code']='';
      $row['import_mov_whi_number_str']='';
      $row['mov_guid']='';

      $row['mov_date']=date('Y-m-d H:i:s');
      $row['mydate_add']=null;
      $row['mydate_edit']=null;
      $row['user_id_add']=0;
      $row['user_id_edit']=0;
      $row['gks_nickname_add']='';
      $row['gks_nickname_edit']='';
      $row['myip']='';
      
      $row['mov_state']='010draft';
    
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
      $row['print_mov_state']='';

      $row['mov_whi_number_int']=0;
      $row['mov_whi_number_str']='';
          
      $row['bank_deposit_9digit']='';
      $row['from_aade_import_json']='';
          
      $row['gks_base_template_id']=$template_id;
      $my_page_title=gks_lang('Νέο Δελτίο Αποστολής από το πρότυπο').' #'.$template_id;
    }
      
            
  }
  //echo '<pre>';print $template_id;die();
  
  if ($template_id==0) {
  
    
    
    $my_page_title=gks_lang('Νέο Δελτίο Αποστολής');
    $row=array();
  
    $row['id_whi_mov']=-1;
    $row['cancel_for_whi_mov_id']=0;
    $row['credit_memo_for_whi_mov_id']=0;
    $row['from_aade_import']='';
    $row['import_mov_whi_seira_code']='';
    $row['import_mov_whi_number_str']='';
    $row['mov_guid']='';
    $row['aade_skopos_diakinisis_id']=0;
    $row['aade_skopos_19_descr']='';
    $row['mov_date']=date('Y-m-d H:i:s');
    $row['mydate_add']=null;
    $row['mydate_edit']=null;
    $row['user_id_add']=0;
    $row['user_id_edit']=0;
    $row['gks_nickname_add']='';
    $row['gks_nickname_edit']='';
    $row['myip']='';
  
    $row['mov_state']='010draft';
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
  
  
    
  
   
    
    
    $row['products_posotita']=0;
    $row['products_varos']=0;
    $row['products_ogos']=0;
    $row['products_ogos_max_x']=0;
    $row['products_ogos_max_y']=0;
    $row['products_ogos_max_z']=0;
    $row['products_need_apostoli']=0;
  
    
    $row['tropos_apostolis']=1;
    $row['tropos_pliromis']=1;
    $row['kostos_apostolis']=0;
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
    $row['mov_whi_journal_id']=0;
    $row['mov_whi_seira_id']=0;
    $row['mov_whi_seira_code']='';
    $row['mov_whi_number_int']=0;
    $row['mov_whi_number_str']='';
    $row['reverse_delivery_purpose']=0;
    $row['send_mydata']=0;
    $row['send_paroxos']=0;    
    $row['is_xeirografi']=0;
    
    $row['acc_eidos_parastatikou_id']=0;
    $row['eidos_parastatikou_type_id']=0;
    $row['antisimvalomenos_label']=gks_lang('Πελάτης');
    $row['eidos_parastatikou_need_prev']=0;
    $row['eidos_parastatikou_need_afm']=1;
    $row['eidos_parastatikou_stock_pros']=-1;
    $row['acc_eidos_parastatikou_other_entity']=0;
    $row['journal_has_correlated_invoices']=0;
    $row['journal_has_multiple_connected_marks']=0;
    $row['journal_has_packings_declarations']=0;
    $row['seira_isdeliverynote']=0;
    $row['seira_is_reverse_delivery_note']=0;
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
    $row['print_mov_state']='';
  
     
    $row['assigned_id']=0;
    $row['gks_nickname_assigned']='';
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
  
  if (isset($gks_user_settings['gks_whi_mov']['def_values'])) {
    $def_values=unserialize($gks_user_settings['gks_whi_mov']['def_values']);  
    //print '<pre>';print_r($def_values);
    foreach ($def_values as $dkey => $dvalue) {
      if (isset($row[$dkey])) $row[$dkey]=$dvalue;
    }
  }

  $row['paroxos_status']=0;
  $row['aade_paroxos_id']=0;
  $row['paroxos_send_pdf']='';
  $row['paroxos_tf1_url']='';
    
} else {


  $sql=select_gks_whi_mov()." where gks_whi_mov.id_whi_mov = ".$id;
  if (count($perm_id_company_ids)>0) $sql.=" and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  
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
  $my_page_title=gks_lang('Δελτίο Αποστολής').': #'.$id;
  
  if ($row['mov_date']=='') $row['mov_date']=$row['mydate_add'];
  
}


gks_plugins_functions_run('admin_whi_mov_item_start',array(
  'row'=>&$row,

));

$mov_whi_seira_id=$row['mov_whi_seira_id'];

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;

if ($row['mov_state']=='040cancelled' or $row['mov_state']=='080listing' or $row['mov_state']=='090ekdosi' or $row['mov_state']=='100closed') {
  $gks_lock=true;
} else {
  if ($row['mov_whi_number_int'] > 0 and $row['is_xeirografi']==0 and ($row['mov_state']=='010draft')) {
    $gks_number_lock=true;
  }
}
$credit_memo_for_whi_mov_id=$row['credit_memo_for_whi_mov_id'];
$cancel_for_whi_mov_id=$row['cancel_for_whi_mov_id'];

$antisimvalomenos_label=$row['antisimvalomenos_label'];
$acc_eidos_parastatikou_id=intval($row['acc_eidos_parastatikou_id']);
$eidos_parastatikou_type_id=intval($row['eidos_parastatikou_type_id']);
$eidos_parastatikou_need_prev=intval($row['eidos_parastatikou_need_prev']);
$eidos_parastatikou_need_afm=intval($row['eidos_parastatikou_need_afm']);
$eidos_parastatikou_stock_pros=intval($row['eidos_parastatikou_stock_pros']);
$acc_eidos_parastatikou_other_entity=intval($row['acc_eidos_parastatikou_other_entity']);
$journal_has_correlated_invoices=intval($row['journal_has_correlated_invoices']);
$journal_has_multiple_connected_marks=intval($row['journal_has_multiple_connected_marks']);
$journal_has_packings_declarations=intval($row['journal_has_packings_declarations']);
$seira_isdeliverynote=intval($row['seira_isdeliverynote']);
$seira_is_reverse_delivery_note=intval($row['seira_is_reverse_delivery_note']);

$antisimvalomenos_label_org=$antisimvalomenos_label;
$acc_eidos_parastatikou_id_org=$acc_eidos_parastatikou_id;
$whi_eidos_parastatikou_type_id_org=$eidos_parastatikou_type_id;
$whi_eidos_parastatikou_stock_pros_org=$eidos_parastatikou_stock_pros;

$credit_memo_descr_for='';
$credit_memo_descr_by='';
if ($credit_memo_for_whi_mov_id!=0) { //check ean einai akirotiko gia allo
  $org_products_posotita=0;
  $others_count=0;
  $others_products_posotita_sum=0;
  $rest_products_posotita_sum=0;
  
  $sql_credit_memo="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
  gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros,
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
  gks_whi_mov.mov_whi_number_int, gks_whi_mov.mov_whi_number_str, gks_whi_mov.mov_whi_ekdosi_date, gks_whi_mov.mov_date,gks_whi_mov.mov_state,
  gks_whi_mov.products_posotita
  FROM (((gks_whi_mov 
  LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira
  where gks_whi_mov.id_whi_mov=".$credit_memo_for_whi_mov_id;
  $result_credit_memo = $db_link->query($sql_credit_memo);        
  if (!$result_credit_memo) {debug_mail(false,'error sql',$sql_credit_memo);die('sql error');}
  if ($result_credit_memo->num_rows==0) {
    $credit_memo_descr_for=gks_lang('Δεν βρέθηκε το δελτίο αποστολής με ID').': '.
    '<a href="admin-whi-mov-item.php?id='.$credit_memo_for_whi_mov_id.'" class="alert-link">'.$credit_memo_for_whi_mov_id.'</a>';
    debug_mail(false,'record parent not found sql',$sql_credit_memo); 
    //die('no record found (2)');
  } else {
    $row_credit_memo = $result_credit_memo->fetch_assoc();
    $org_products_posotita=$row_credit_memo['products_posotita'];
    
    $antisimvalomenos_label=$row_credit_memo['antisimvalomenos_label'];
    $acc_eidos_parastatikou_id=intval($row_credit_memo['acc_eidos_parastatikou_id']);
    $eidos_parastatikou_type_id=intval($row_credit_memo['eidos_parastatikou_type_id']);
    $eidos_parastatikou_need_prev=intval($row_credit_memo['eidos_parastatikou_need_prev']);
    $eidos_parastatikou_need_afm=intval($row_credit_memo['eidos_parastatikou_need_afm']);
    $eidos_parastatikou_stock_pros=intval($row_credit_memo['eidos_parastatikou_stock_pros']);
    
    $credit_memo_descr_for=
        '<a href="admin-whi-mov-item.php?id='.$credit_memo_for_whi_mov_id.'" class="alert-link">'.
        '#' .$credit_memo_for_whi_mov_id.', '.
        $row_credit_memo['acc_journal_descr'].', '.
        $row_credit_memo['seira_code'].' - '.$row_credit_memo['seira_descr'].', '.
        showDate(strtotime($row_credit_memo['mov_date']), 'd/m/Y H:i', 1).', '.
        $row_credit_memo['mov_whi_number_int'].', '.
        '<span class="whi_mov_state_'.$row_credit_memo['mov_state'].'">'.getWhiMovStateDescr($row_credit_memo['mov_state']).'</span></a>';
  }
  
  $sql_others="SELECT count(*) as others_count,Sum(products_posotita) AS products_posotita_sum
  FROM gks_whi_mov
  WHERE credit_memo_for_whi_mov_id=".$credit_memo_for_whi_mov_id." AND id_whi_mov<>".$id;
  //echo $sql_others; die();
  $result_others = $db_link->query($sql_others);        
  if (!$result_others) {debug_mail(false,'error sql',$sql_others);die('sql error');}
  if ($result_others->num_rows>=1) {
    $row_others = $result_others->fetch_assoc();
    if (empty($row_others['products_posotita_sum'])==false) $others_products_posotita_sum=floatval($row_others['products_posotita_sum']);
    if (empty($row_others['others_count'])==false) $others_count=floatval($row_others['others_count']);
  }
  $rest_products_posotita_sum=$org_products_posotita-$others_products_posotita_sum;
  
  
  $gks_number_lock=true;
  $gks_user_lock=true;
  //$gks_lock=true;
}

$canceled_descr_for='';
if ($cancel_for_whi_mov_id!=0) { //check ean einai akirotiko gia allo
  $sql_canceled="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
  gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros,
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
  gks_whi_mov.mov_whi_number_int, gks_whi_mov.mov_whi_number_str, gks_whi_mov.mov_whi_ekdosi_date, gks_whi_mov.mov_date,gks_whi_mov.mov_state
  
  FROM (((gks_whi_mov 
  LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira
  where gks_whi_mov.id_whi_mov=".$cancel_for_whi_mov_id;
  $result_canceled = $db_link->query($sql_canceled);        
  if (!$result_canceled) {debug_mail(false,'error sql',$sql_canceled);die('sql error');}
  if ($result_canceled->num_rows==0) {
    $canceled_descr_for=gks_lang('Δεν βρέθηκε το δελτίο αποστολής με ID').': '.
    '<a href="admin-whi-mov-item.php?id='.$cancel_for_whi_mov_id.'" class="alert-link">'.$cancel_for_whi_mov_id.'</a>';
    debug_mail(false,'record parent not found sql',$sql_canceled); 
    die('no record found (3)');
  } else {
    $row_canceled = $result_canceled->fetch_assoc();
  
    $antisimvalomenos_label=$row_canceled['antisimvalomenos_label'];
    //$acc_eidos_parastatikou_id=intval($row_canceled['acc_eidos_parastatikou_id']);
    //$eidos_parastatikou_type_id=intval($row_canceled['eidos_parastatikou_type_id']);
    $eidos_parastatikou_need_prev=intval($row_canceled['eidos_parastatikou_need_prev']);
    $eidos_parastatikou_need_afm=intval($row_canceled['eidos_parastatikou_need_afm']);
    $eidos_parastatikou_stock_pros=intval($row_canceled['eidos_parastatikou_stock_pros']);
    
    $canceled_descr_for=
        '<a href="admin-whi-mov-item.php?id='.$cancel_for_whi_mov_id.'" class="alert-link">'.
        '#' .$cancel_for_whi_mov_id.', '.
        $row_canceled['acc_journal_descr'].', '.
        $row_canceled['seira_code'].' - '.$row_canceled['seira_descr'].', '.
        showDate(strtotime($row_canceled['mov_date']), 'd/m/Y H:i', 1).', '.
        $row_canceled['mov_whi_number_int'].', '.
        '<span class="whi_mov_state_'.$row_canceled['mov_state'].'">'.getWhiMovStateDescr($row_canceled['mov_state']).'</span></a>';
    $gks_lock=true;
  }
}

$credit_memo_descr_by='';
if ($id>=0) {
  //check ean einai credit_memo
  $sql_credit_memo="SELECT gks_whi_mov.id_whi_mov, gks_whi_mov.mov_date, gks_whi_mov.mov_state, 
  gks_whi_mov.mov_whi_number_int, gks_whi_mov.mov_whi_number_str, gks_acc_journal.acc_journal_descr, 
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr
  FROM (gks_whi_mov 
  LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira
  WHERE gks_whi_mov.credit_memo_for_whi_mov_id=".$id;
  $result_credit_memo = $db_link->query($sql_credit_memo);  
  if (!$result_credit_memo) {
    debug_mail(false,'error sql',$sql_credit_memo);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_credit_memo->num_rows>=1) {
    $tmp_array=array();
    while ($row_credit_memo = $result_credit_memo->fetch_assoc()) {
      

      
        $tmp_array[]=
        '<a href="admin-whi-mov-item.php?id='.$row_credit_memo['id_whi_mov'].'" class="alert-link">'.
        '#' .$row_credit_memo['id_whi_mov'].', '.
        $row_credit_memo['acc_journal_descr'].', '.
        $row_credit_memo['seira_code'].' - '.$row_credit_memo['seira_descr'].', '.
        showDate(strtotime($row_credit_memo['mov_date']), 'd/m/Y H:i', 1).', '.
        $row_credit_memo['mov_whi_number_int'].', '.
        '<span class="whi_mov_state_'.$row_credit_memo['mov_state'].'">'.getWhiMovStateDescr($row_credit_memo['mov_state']).'</span></a>';
      
    }
    if (count($tmp_array)==1) {
      $credit_memo_descr_by=gks_lang('Το συσχετιζόμενο δελτίο αποστολής είναι το').': '.$tmp_array[0];
    } else if (count($tmp_array)>=2) {
      $credit_memo_descr_by=gks_lang('Τα συσχετιζόμενα δελτία αποστολής είναι τα').':<br>'.implode('<br>',$tmp_array);
    }
    
    
    
  }
}

$canceled_descr_by='';
if ($id>=0) {
  //check ean einai akyromeno
  $sql_canceled="SELECT gks_whi_mov.id_whi_mov, gks_whi_mov.mov_date, gks_whi_mov.mov_state, 
  gks_whi_mov.mov_whi_number_int, gks_whi_mov.mov_whi_number_str, gks_acc_journal.acc_journal_descr, 
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr
  FROM (gks_whi_mov 
  LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira
  WHERE gks_whi_mov.cancel_for_whi_mov_id=".$id;
  $result_canceled = $db_link->query($sql_canceled);  
  if (!$result_canceled) {
    debug_mail(false,'error sql',$sql_canceled);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_canceled->num_rows>=1) {
    $row_canceled = $result_canceled->fetch_assoc();
    if ($row_canceled['id_whi_mov']!=0) {
      $canceled_descr_by=
      '<a href="admin-whi-mov-item.php?id='.$row_canceled['id_whi_mov'].'" class="alert-link">'.
      '#' .$row_canceled['id_whi_mov'].', '.
      $row_canceled['acc_journal_descr'].', '.
      $row_canceled['seira_code'].' - '.$row_canceled['seira_descr'].', '.
      showDate(strtotime($row_canceled['mov_date']), 'd/m/Y H:i', 1).', '.
      $row_canceled['mov_whi_number_int'].', '.
      '<span class="whi_mov_state_'.$row_canceled['mov_state'].'">'.getWhiMovStateDescr($row_canceled['mov_state']).'</span></a>';
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

//$addressL1=trim_gks((empty($row['ma_odos']) ? '' : $row['ma_odos'].', ').$row['ma_perioxi']);
//if (endwith($addressL1,',')) $addressL1=substr($addressL1, 0, strlen($addressL1)-1);
//$addressL2=trim_gks((empty($row['ma_poli']) ? '' : $row['ma_poli'].', ').
//                (empty($row['nomos_descr']) ? '' : $row['nomos_descr'].', ').
//                $row['ma_tk']);
//if (endwith($addressL2,',')) $addressL1=substr($addressL2, 0, strlen($addressL2)-1);
//$addressL3=trim_gks($row['country_name']);
//
//$address='';
//if ($addressL1!='') $address.=$addressL1.'<br>';
//if ($addressL2!='') $address.=$addressL2.'<br>';
//$address.=$addressL3;
//if (endwith($address,'<br>')) $address=substr($address, 0, strlen($address)-4);  


$row['ma_country_id']=intval($row['ma_country_id']);
$row['ma_poli']=trim_gks($row['ma_poli']);


$mov_state=$row['mov_state'];
$products_posotita=$row['products_posotita'];
$products_varos=$row['products_varos'];
$products_ogos=$row['products_ogos'];;
$products_ogos_max_x=$row['products_ogos_max_x'];
$products_ogos_max_y=$row['products_ogos_max_y'];
$products_ogos_max_z=$row['products_ogos_max_z'];
$products_need_apostoli=$row['products_need_apostoli']==0 ? false : true;
if ($whi_eidos_parastatikou_type_id_org==24) {//apografi
  $products_need_apostoli=false;
}



unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='whi_mov';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']= $row['company_id'];
$mybasketarray['company_sub_id']= $row['company_sub_id'];
$mybasketarray['mov_whi_journal_id']=intval($row['mov_whi_journal_id']);
$mybasketarray['mov_whi_seira_id']=intval($row['mov_whi_seira_id']);
$mybasketarray['mov_state']=trim_gks($row['mov_state']);
$mybasketarray['mov_date']=trim_gks($row['mov_date']);

$mybasketarray['user']['user_id']=$row['user_id'];
$mybasketarray['user']['afm']=$row['afm'];
$mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
$mybasketarray['parastatiko']= $eidos_parastatikou_need_afm;



$mybasketarray['products_varos']= $products_varos;
$mybasketarray['products_ogos']= $products_ogos;
$mybasketarray['products_ogos_max_x']= $products_ogos_max_x;
$mybasketarray['products_ogos_max_y']= $products_ogos_max_y;
$mybasketarray['products_need_apostoli']=$products_need_apostoli;
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
$mybasketarray['tropos_pliromis'] = 0;
//echo '<pre>';echo $mybasketarray['tropos_apostolis'].' | '.$mybasketarray['products_need_apostoli'];die();


$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray,-1);
//echo '<pre>';print_r($mybasketarray['tropos_apostolis']);print_r($mybasketarray['tropoi_apostolis_all']);die();


//print '<pre>';
//print_r($mybasketarray);
//print_r($mybasketarray['tropoi_pliromis_all']);
//die();


if ($row['tropos_apostolis']>0 and isset($mybasketarray['tropoi_apostolis_all'][$row['tropos_apostolis']])) $mybasketarray['tropoi_apostolis_all'][$row['tropos_apostolis']]['dm_calc_kostos']= $row['kostos_apostolis'];


$mybasketarray['coupons']=array();


$sql_other_entity="SELECT gks_whi_mov_other_entity.*, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_aade_entitytype.aade_entitytype_descr, 
gks_country.country_initials, gks_country.country_name, 
gks_nomoi.nomos_descr
FROM (((gks_whi_mov_other_entity 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov_other_entity.entity_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_aade_entitytype ON gks_whi_mov_other_entity.aade_entitytype_id = gks_aade_entitytype.id_aade_entitytype) 
LEFT JOIN gks_country ON gks_whi_mov_other_entity.entity_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_whi_mov_other_entity.entity_nomos_id = gks_nomoi.id_nomos
where whi_mov_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
order by entity_aa";
$result_other_entity = $db_link->query($sql_other_entity);        
if (!$result_other_entity) {debug_mail(false,'error sql',$sql_other_entity); die('sql error');}

$other_entity_array=array();
while ($row_other_entity = $result_other_entity->fetch_assoc()) {
  if ($id<0) $row_other_entity['id_whi_mov_other_entity']=0;
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


$sql_correlated_invoices="SELECT gks_whi_mov_correlated_invoices.* 
FROM gks_whi_mov_correlated_invoices 
where whi_mov_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
order by coi_aa";
$result_correlated_invoices = $db_link->query($sql_correlated_invoices);        
if (!$result_correlated_invoices) {debug_mail(false,'error sql',$sql_correlated_invoices); die('sql error');}

$correlated_invoices_array=array();
while ($row_correlated_invoices = $result_correlated_invoices->fetch_assoc()) {
  if ($id<0) $row_correlated_invoices['id_whi_mov_correlated_invoices']=0;
  $row_correlated_invoices['coi_mark']=trim_gks($row_correlated_invoices['coi_mark']);
  if ($row_correlated_invoices['coi_mark']=='') {
    $row_correlated_invoices['coi_mark']=gks_aade_get_mark_from_id($row_correlated_invoices);
  }
  $correlated_invoices_array[]=$row_correlated_invoices;
}

//print '<pre>';print_r($correlated_invoices_array);die();

$sql_multiple_connected_marks="SELECT gks_whi_mov_multiple_connected_marks.* 
FROM gks_whi_mov_multiple_connected_marks 
where whi_mov_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
order by mcm_aa";
$result_multiple_connected_marks = $db_link->query($sql_multiple_connected_marks);        
if (!$result_multiple_connected_marks) {debug_mail(false,'error sql',$sql_multiple_connected_marks); die('sql error');}
$multiple_connected_marks_array=array();
while ($row_multiple_connected_marks = $result_multiple_connected_marks->fetch_assoc()) {
  if ($id<0) $row_multiple_connected_marks['id_whi_mov_multiple_connected_marks']=0;
  $row_multiple_connected_marks['mcm_mark']=trim_gks($row_multiple_connected_marks['mcm_mark']);
  if ($row_multiple_connected_marks['mcm_mark']=='') {
    $row_multiple_connected_marks['mcm_mark']=gks_aade_get_mark_from_id($row_multiple_connected_marks);
  }
  $multiple_connected_marks_array[]=$row_multiple_connected_marks;
}

$sql_packings_declarations="SELECT gks_whi_mov_packings_declarations.* 
FROM gks_whi_mov_packings_declarations 
where whi_mov_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
order by packaging_aa";
$result_packings_declarations = $db_link->query($sql_packings_declarations);        
if (!$result_packings_declarations) {debug_mail(false,'error sql',$sql_packings_declarations); die('sql error');}
$packings_declarations_array=array();
while ($row_packings_declarations = $result_packings_declarations->fetch_assoc()) {
  if ($id<0) $row_packings_declarations['id_whi_mov_packings_declarations']=0;
  $packings_declarations_array[]=$row_packings_declarations;
}
//print '<pre>';print_r($packings_declarations_array);die();

$packagingTypes=[];
for ($i = 1; $i <= $getAADE_PackagingTypeDescr_max; $i++) {
  $packagingTypes[]=array('id' => $i, 'descr' => getAADE_PackagingTypeDescr($i));
}
//print '<pre>';print_r($packagingTypes);die();
$sql_eidi="SELECT gks_whi_mov_products.*, 
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
gks_eshop_products.product_lot_serial
FROM ((gks_whi_mov_products 
LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN gks_monades_metrisis ON gks_whi_mov_products.product_monada_id = gks_monades_metrisis.id_monada
WHERE gks_whi_mov_products.whi_mov_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1'))."
ORDER BY gks_whi_mov_products.product_aa";
$result_eidi = $db_link->query($sql_eidi);        
if (!$result_eidi) {debug_mail(false,'error sql',$sql_eidi); die('sql error');}

$eidos_array = array();
$products_sets=array();
$products_count=0;

$id_whi_mov_product_array=array();

while ($eidos = $result_eidi->fetch_assoc()) {
  if ($eidos['product_id']==2) $eidos['product_id']=0;
  $eidos_array[]=$eidos;
  $products_count++;
  $id_whi_mov_product_array[]=$eidos['id_whi_mov_product'];
}


gks_CheckAFM_Live($mybasketarray);
$check_vies=$mybasketarray['check_vies'];
//print '<pre>';print_r($mybasketarray['check_vies']);//die();



gks_cache_admin_whi_mov_item();



$products_lots_serials=array();

if (count($id_whi_mov_product_array) > 0) {

  if ($GKS_PRODUCT_LOTS_SERIALS) {
    $sql_lots_serials="SELECT 
    gks_whi_mov_products_lots.lot_product_id,
    whi_mov_product_id as id, 
    lot_product_quantity,
    apografi_lot_posotitaonhand,
    gks_eshop_product_lots.lot_name, 
    gks_eshop_product_lots.lot_descr, 
    gks_eshop_product_lots.lot_date_production, 
    gks_eshop_product_lots.lot_date_expire, 
    gks_eshop_product_lots.lot_disabled
    FROM gks_whi_mov_products_lots
    LEFT JOIN gks_eshop_product_lots ON gks_whi_mov_products_lots.lot_product_id = gks_eshop_product_lots.id_lot_product
    WHERE gks_whi_mov_products_lots.whi_mov_product_id In (".implode(',',$id_whi_mov_product_array).")
    ORDER BY gks_whi_mov_products_lots.id_whi_mov_product_lots";
    $result_lots_serials = $db_link->query($sql_lots_serials);        
    if (!$result_lots_serials) {debug_mail(false,'error sql',$sql_lots_serials); die('sql error');}
    while ($row_lots_serials = $result_lots_serials->fetch_assoc()) {
      if ($whi_eidos_parastatikou_type_id_org==24) {
        $row_lots_serials['lot_product_quantity']=$row_lots_serials['apografi_lot_posotitaonhand'];
      }      
      $products_lots_serials[$row_lots_serials['id']][]=$row_lots_serials;
    }
    
    //echo '<pre>';print_r($products_lots_serials);die();
    
  }  
  
}






stat_record();

include_once('_my_header_admin.php');
//print '<pre>';
//print_r($row);
//print '</pre>';


?>

<link href="css/admin-whi-mov-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Δελτίο Αποστολής');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Δελτίο Αποστολής');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<?php if ($cancel_for_whi_mov_id!=0) {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ακυρωτικό Δελτίο Αποστολής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('cancel1');?>>
  
          <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Ακυρωτικό Δελτίο Αποστολής');?></h4>
            <p><?php echo gks_lang('Αφορά το ακυρωμένο δελτίο αποστολής');?>: <?php echo $canceled_descr_for;?></p>
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
          <?php echo gks_lang('Ακυρωμένο Δελτίο Αποστολής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('cancel2');?>>
  
          <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Ακυρωμένο Δελτίο Αποστολής');?></h4>
            <p><?php echo gks_lang('Αυτό το δελτίο αποστολής έχει ακυρωθεί');?>.<br>
              <?php echo gks_lang('Το ακυρωτικό δελτίο αποστολής είναι το');?>: <?php echo $canceled_descr_by;?>
            </p>
          </div>

  
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<?php if ($credit_memo_for_whi_mov_id!=0) {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συσχετιζόμενο Δελτίο Επιστροφής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('credit1');?>>
  
          <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Συσχετιζόμενο Δελτίο Επιστροφής');?></h4>
            <p><?php echo gks_lang('Αφορά το συσχετιζόμενο δελτίο αποστολής');?>: <?php echo $credit_memo_descr_for;?></p>
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
          <?php echo gks_lang('Συσχετιζόμενο Δελτίο Επιστροφής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('credit2');?>>
  
          <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading"><?php echo gks_lang('Συσχετιζόμενο Δελτίο Επιστροφής');?></h4>
            <p><?php echo gks_lang('Αυτό το δελτίο αποστολής έχει συσχετιζόμενο δελτίο επιστροφής');?><br>
              <?php echo $credit_memo_descr_by;?>
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
            <label for="mov_whi_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="mov_whi_journal_id" type="hidden" value="'.$row['mov_whi_journal_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['acc_journal_descr'];
                echo '</div>';
              } else {?>
              <select id="mov_whi_journal_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_journal, acc_journal_descr, acc_eidos_parastatikou_id, 
                eidos_parastatikou_type_id, eidos_parastatikou_need_prev,eidos_parastatikou_need_afm,eidos_parastatikou_stock_pros,
                acc_eidos_parastatikou_other_entity,
                journal_has_correlated_invoices,
                journal_has_multiple_connected_marks,
                journal_has_packings_declarations
                FROM gks_acc_journal 
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                WHERE eidos_parastatikou_type_id in (21,22,23,24) and id_acc_eidos_parastatikou not in (702,703,704) 
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
                  'data-need_afm="'.$row_select['eidos_parastatikou_need_afm'].'" '.
                  'data-stock_pros="'.$row_select['eidos_parastatikou_stock_pros'].'" '.
                  'data-other_entity="'.intval($row_select['acc_eidos_parastatikou_other_entity']).'" '.
                  'data-correlated_invoices="'.intval($row_select['journal_has_correlated_invoices']).'" '.
                  'data-multiple_connected_marks="'.intval($row_select['journal_has_multiple_connected_marks']).'" '. 
                  'data-packings_declarations="'.intval($row_select['journal_has_packings_declarations']).'" '; 
                  if ($row['mov_whi_journal_id'] == $row_select['id_acc_journal']) echo ' selected ';
                  echo '>'.$row_select['acc_journal_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="mov_whi_seira_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="mov_whi_seira_id" type="hidden" value="'.$row['mov_whi_seira_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['seira_code'].' - '.$row['seira_descr'];
                echo '</div>';
              } else {?>
              <select id="mov_whi_seira_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_seira, seira_code,seira_descr,
                is_xeirografi,
                seira_isdeliverynote,
                seira_is_reverse_delivery_note
                FROM gks_acc_seires 
                WHERE is_disable=0 and acc_journal_id=".$row['mov_whi_journal_id'];
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
                  'data-is_reverse_delivery_note="'.$row_select['seira_is_reverse_delivery_note'].'" ';
                  if ($row['mov_whi_seira_id'] == $row_select['id_acc_seira']) echo ' selected ';
                  echo '>'.$row_select['seira_code'].' - '.$row_select['seira_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div> 
          <div class="form-group row" id="reverse_delivery_purpose_div" style="<?php if ($row['seira_is_reverse_delivery_note']==0) echo 'display:none;';?>">
            <label for="reverse_delivery_purpose" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αιτία Αντίστροφης Διακίνησης');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="reverse_delivery_purpose" type="hidden" value="'.$row['reverse_delivery_purpose'].'">';
                echo '<div id="reverse_delivery_purpose_span" class="gks_flock form-control-sm">';
                  echo getAADE_ReverseDeliveryNotePurposeDescr($row['reverse_delivery_purpose']);
                echo '</div>';
              } else {?>
              <select id="reverse_delivery_purpose" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                for ($rvrsdn=1; $rvrsdn<=$getAADE_ReverseDeliveryNotePurposeDescr_max; $rvrsdn++) {
                  echo '<option value="'.$rvrsdn.'" '.
                  ($row['reverse_delivery_purpose']==$rvrsdn ? 'selected' : '').
                  '>'.getAADE_ReverseDeliveryNotePurposeDescr($rvrsdn).'</value>';
                }
                ?>
              </select>
              <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="mov_whi_number_int" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['mov_whi_number_int'];
                echo '</div>';
              } else {?>
              <input id="mov_whi_number_int" class="form-control form-control-sm myneedsave" type="number" 
              value="<?php if ($row['mov_whi_number_int']>0) echo $row['mov_whi_number_int'];?>" style="max-width:100px;" 
              placeholder="" min="0" step="1"
              <?php if ($gks_number_lock or $row['is_xeirografi']==0) echo 'disabled';?>>
              <?php } ?>
            </div>
          </div> 

          <div class="form-group row" id="div_aade_skopos_diakinisis_id" style="<?php if ($whi_eidos_parastatikou_type_id_org==24) echo 'display:none;';?>">
            <label for="aade_skopos_diakinisis_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σκοπός Διακίνησης');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo  $row['aade_skopos_diakinisis_descr'];
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
              <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>
              style="<?php echo ($row['aade_skopos_diakinisis_id']==22 ? '' : 'display:none');?>"
              placeholder="<?php echo gks_lang('Τίτλος της Λοιπής Αιτίας Διακίνησης');?>">    
              <?php } ?>
            </div>
          </div> 
                              
          <div class="form-group row">
            <label for="mov_date" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-sm-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if (isset($row['mov_date'])) echo  showDate(strtotime($row['mov_date']), 'd/m/Y H:i', 1);
                echo '</div>';
              } else {?>
              <input id="mov_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['mov_date'])) echo  showDate(strtotime($row['mov_date']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>>
              <?php } ?>
            </div>
          </div> 


          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-sm-8">
              <span class="whi_mov_state_<?php echo $row['mov_state'];?>"><?php echo getWhiMovStateDescr($row['mov_state']);?></span>
            </div>
          </div> 

              

 
         
     
          <div class="form-group row" id="div_fiscal_position_id" style="<?php if ($whi_eidos_parastatikou_type_id_org==24) echo 'display:none;';?>">
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
          <div class="form-group row" id="div_pricelist_id" style="<?php if ($whi_eidos_parastatikou_type_id_org==24) echo 'display:none;';?>">
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
            <label for="assigned_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ανάθεση σε');?>:</label>
            <div class="col-sm-8">
              <input id="assigned_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname_assigned']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['assigned_id'];?>">
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
                <?php echo $row['import_mov_whi_seira_code'];?>
              </div>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-sm-8">
              <div class="gks_flock form-control-sm">
                <?php echo $row['import_mov_whi_number_str'];?>
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

      
     
    </div>
    
    
    <div class="col-md-8">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand" id="div_show_user" style="<?php echo (($row['eidos_parastatikou_need_afm']==-1) ? 'display:none;' : '') ?>">
        <div class="card-header" style="text-align:center" id="antisimvalomenos_label">
          <?php echo $antisimvalomenos_label;?>
        </div>
        <div class="card-body" <?php echo gks_card_body('user');?>>

 

          
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
                  placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>>
                  <input id="user_id" type="hidden" value="<?php echo $row['user_id'];?>" class="myneedsave">
                  <?php if ($perm_gks_whi_mov_edit) {?>
                  <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['user_id'];?>" style="<?php if ($row['user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
                  <i id="user_save" class="fas fa-save" style="<?php if ($row['user_id']>0) echo 'display:none';?>;color: #35dc35;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Δημιουργία επαφής');?>"></i>
                  <?php } ?>
            <?php } ?>
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
            <?php if ($gks_lock==false) {?>
                  <i id="copy_text_pelati_sxolio_to_logistirio" class="far fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση για λογιστήριο</b>');?>"></i>
            <?php }?>
                </div>
              </div>
                            
            </div>
            
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_order_sxolio" style="<?php echo (trim_gks($row['order_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['order_sxolio']);?></div>
                <div style="text-align:right;width:100%;margin-bottom: 10px;">
            <?php if ($gks_lock==false) {?>
                  <i id="copy_text_order_sxolio_to_logistirio" class="far fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση για λογιστήριο</b>');?>"></i>
            <?php }?>
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
            $result_select = $db_link->query($sql);        
            if (!$result_select) {
              debug_mail(false,'error sql',$sql);
              die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
            }
            if ($result_select->num_rows==1) {
              $row_select = $result_select->fetch_assoc();
              $ee_initials=trim_gks($row_select['country_ee']);
            }
            $this_select='';
            //$this_select.= '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" data-ee="'.trim_gks($row_select['country_ee']).'"';
            //if ($row['ma_country_id'] == $row_select['id_country']) {$this_select.= ' selected '; $ee_initials=trim_gks($row_select['country_ee']); }
            //$this_select.= '>'.$row_select['country_name'].'</option>';
                        
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
                    
                    
                    //if ($check_vies['run']) echo '<span style="display:inline-block;padding-left: 10px;position:relative;top:-3px;">'.$check_vies['views_run_img'].'</span>';
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
            or (gks_users_extra_address.whi_mov_id=".$id.")
            
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
            ?>
              
            <?php if ($gks_lock) {
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
                      echo $selected_ea['ea_phone'];
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

      <div class="card gks_card_expand">
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
          
          <div class="form-group row gks_other_entity_item align-items-center" data-oeaa="<?php echo $entity_aa;?>" data-recid="<?php echo $oe_item['id_whi_mov_other_entity'];?>">
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
                $oeea_ret=gks_other_entity_get_data('gks_whi_mov',$oe_item['id_whi_mov_other_entity']);
              } else {
                $oeea_ret=gks_other_entity_get_data('gks_whi_mov',-1,$oe_item['entity_user_id'],$oe_item['address_extra']);
              }
              echo $oeea_ret['html'];
              ?>
              </div>
            </div>            
            <div class="col-12 col-sm-2  col-md-2  col-lg-1">
              <div class="text-center gks_icons">
                <?php if ($gks_lock==false and $perm_gks_whi_mov_edit) {?>
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
          
          <div class="form-group row gks_correlated_invoices_item align-items-center" data-coiaa="<?php echo $coi_aa;?>" data-recid="<?php echo $ci_item['id_whi_mov_correlated_invoices'];?>">

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
                <?php if ($gks_lock==false and $perm_gks_whi_mov_edit) {?>
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
          
          <div class="form-group row gks_multiple_connected_marks_item align-items-center" data-mcmaa="<?php echo $mcm_aa;?>" data-recid="<?php echo $ci_item['id_whi_mov_multiple_connected_marks'];?>">

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
                <?php if ($gks_lock==false and $perm_gks_whi_mov_edit) {?>
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
          
          <div class="row" id="div_apografi_label" style="<?php if ($whi_eidos_parastatikou_type_id_org!=24) echo 'display:none;';?>">
            <div class="col-12" style="padding: 10px 4px;">
              <div style="text-align: center;font-size: 0.8rem;background-color: lightyellow;padding:10px;border: 1px solid gray;border-radius: 10px;">
                <?php echo gks_lang('Εφόσον το ημερολόγιο είναι απογραφής, εισάγεται ως ποσότητα, την ποσότητα που έχετε αυτήν την στιγμή στα χέρια σας');?>  
              </div>
            </div>
          </div>
          <?php
          
          

            
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
            
            
            if ($perm_gks_whi_mov_edit) {
               
                
              $gkscols1 ='col-12 col-sm-6  col-md-5  col-lg-2 gks_items_col';
              $gkscols2 ='col-12 col-sm-6  col-md-7  col-lg-4 gks_items_col';
              $gkscols3 ='col-12 col-sm-6 col-md-7  col-lg-3 gks_items_col';
              $gkscols5 ='col-6  col-sm-4  col-md-3  col-lg-2 gks_items_col';
              $gkscols8 ='col-6  col-sm-2  col-md-2  col-lg-1 gks_items_col';            
                
              
            } else {
              $gkscols1 ='col-12 col-sm-4  col-md-4  col-lg-2 gks_items_col';
              $gkscols2 ='col-12 col-sm-8  col-md-8  col-lg-5 gks_items_col';
              $gkscols3 ='col-12 col-sm-8  col-md-8  col-lg-4 gks_items_col';
              $gkscols5 ='col-12 col-sm-4  col-md-4  col-lg-1 gks_items_col';            
            }
          
          
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
            
<?php if ($perm_gks_whi_mov_edit) {?>
 
            <div class="<?php echo $gkscols8;?>">
              <div class="table-dark gks_eidos_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
            </div> 
<?php } ?>
          </div>           
<?php 

  



    $aa = 0;
    $eidi_sum_quantity=0;
    foreach ($eidos_array as $eidos) {

      $aa++;
      
      if ($whi_eidos_parastatikou_type_id_org==24) {
        $eidos['product_quantity']=$eidos['apografi_posotitaonhand'];
      }
      
      $eidi_sum_quantity+=$eidos['product_quantity'];
      
?>
          <div class="gks_eidos_2divs" data-aa="<?php echo $aa;?>" >
            <div class="form-group row gks_eidos <?php if (trim_gks($eidos['product_lot_serial'])!='') echo 'gks_eidos_radup';?>" data-recid="<?php echo ($template_id==0 ? $eidos['id_whi_mov_product'] : '0');?>" data-aa="<?php echo $aa;?>">
              <div class="<?php echo $gkscols1;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock gks_flock_small form-control-sm">';
                    echo $eidos['product_code'];
                  echo '</div>';
                } else {?>
                <input type="text" class="form-control form-control-sm gks_code" data-aa="<?php echo $aa;?>" 
                style="width:100%;"
                value="<?php echo $eidos['product_code']?>" 
                data-varos="<?php echo $eidos['product_varos'];?>"
                data-ogos_x="<?php echo intval($eidos['product_ogos_x']);?>"
                data-ogos_y="<?php echo intval($eidos['product_ogos_y']);?>"
                data-ogos_z="<?php echo intval($eidos['product_ogos_z']);?>"
                data-need_apostoli="<?php echo intval($eidos['product_need_apostoli']);?>"
                placeholder="<?php echo gks_lang('Κωδικός');?>"
                <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>>
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
                if ($perm_gks_whi_mov_edit) echo '<i class="gks_product_zoom enterrow fas fa-pen" data-id_product="'.$eidos['product_id'].'" data-aa="'.$aa.'" title="'.gks_lang('Προβολή Είδους').'"></i>';
                echo '<i class="fas fa-info-circle gks_info_descr '.($product_descr_small!='' ? 'tooltipster' : '').'" data-aa="'.$aa.'" title="'.$product_descr_small.'" '.($product_descr_small=='' ? 'style="display:none;"' : '').'></i>';
                if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_descr">';
                    echo htmlspecialchars_gks($eidos['product_descr']);
                  echo '</div>';                
                } else {
                  echo '<textarea class="gks_descr form-control form-control-sm" rows="1" data-aa="'.$aa.'"   placeholder="'.gks_lang('Περιγραφή').'">'.htmlspecialchars_gks($eidos['product_descr']).'</textarea>';
                }
                ?>
                
                </div>
              </div>
              <div class="<?php echo $gkscols3;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_comments">';
                    echo nl2br_gks(htmlspecialchars_gks($eidos['product_comments']));
                  echo '</div>';
                } else {?>
                <textarea class="gks_comments form-control form-control-sm" rows="1" data-aa="<?php echo $aa;?>" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?> placeholder="<?php echo gks_lang('Σχόλιο');?>"><?php echo htmlspecialchars_gks($eidos['product_comments']);?></textarea>
                <?php } ?>
              </div>
              
              <div class="<?php echo $gkscols5;?>">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm gks_quantity_lock">';
                    echo myNumberFormatNo0Local($eidos['product_quantity']);
                  echo '</div>';
                } else {?>
                <input style="text-align:right;" type="number" class="form-control form-control-sm gks_quantity <?php if ($whi_eidos_parastatikou_type_id_org==24) echo 'gks_quantity_apografi';?>" data-aa="<?php echo $aa;?>" data-prev-value="<?php echo $eidos['product_quantity'];?>" value="<?php if ($eidos['product_quantity']!=0) echo $eidos['product_quantity'];?>" min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?> placeholder="<?php echo gks_lang('Ποσότητα');?>">
                <?php } ?>
                <span class="gks_monada_span<?php echo ($gks_lock ? '_lock' :'');?>" data-mon-id="<?php echo $eidos['product_monada_id'];?>" data-aa="<?php echo $aa;?>"><?php echo $eidos['monada_symbol'];?></span>
              </div>
  
              
  <?php if ($perm_gks_whi_mov_edit) {?>
              <div class="<?php echo $gkscols8;?>">
                <div class="text-center gks_icons">
                  <?php if ($gks_lock==false and $perm_gks_whi_mov_edit) {?>
                  <div style="width:33%;float:left;">
                    <i class="fas fa-trash-alt gks_delete_eidos" data-aa="<?php echo $aa;?>" style=""></i>
                  </div>
                  <div style="width:33%;float:left;">
                    <i class="fas fa-arrows-alt-v sortorder_handle"></i>
                  </div>
                  <div style="width:33%;float:left;">
                    <i class="fas fa-plus-circle gks_add_eidos"  data-aa="<?php echo $aa;?>"></i>
                  </div>
                  <?php } ?>
                </div>
              </div>
  <?php } ?>
  
  
  
            </div> 
            
            <?php if ($GKS_PRODUCT_LOTS_SERIALS) { ?>
            <div class="form-group row gks_eidos_lots_serials gks_eidos_raddown gks_eidos_lots_serials_col1" data-aa="<?php echo $aa;?>" style="padding-top: 4px;<?php if (trim_gks($eidos['product_lot_serial'])=='') echo 'display:none;';?>" data-val-lot-serial="<?php echo trim_gks($eidos['product_lot_serial']);?>">
  
              <div class="col-12 col-sm-12  col-md-11 col-lg-11 col-xl-11 offset-md-1 offset-lg-1 offset-xl-1 gks_eidos_lots_serials_list" data-aa="<?php echo $aa;?>">
                
                <div class="div_eidos_lots_serials" data-aa="<?php echo $aa;?>" style="<?php echo (1==2 ? 'display:none;' : '');?>"> 
                  <?php
                  
                  $eidos_lots_serials=array();
                  if (isset($products_lots_serials[$eidos['id_whi_mov_product']])) {
                    $eidos_lots_serials=$products_lots_serials[$eidos['id_whi_mov_product']];
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
                      <?php if ($gks_lock==false and $perm_gks_whi_mov_edit) {?>
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
                      <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?> 
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
                      <?php if (!$perm_gks_whi_mov_edit or trim_gks($eidos['product_lot_serial'])=='serial') echo 'readonly';?> 
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
                      <?php if ($gks_lock==false and $perm_gks_whi_mov_edit) {?>
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
            <?php if ($perm_gks_whi_mov_edit) {?>
            <div class="col-sm-4">

             


           
              <div class="form-group row total_row" id="tr_kostos_apostolis">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Κόστος αποστολής');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <input type="number" id="kostos_apostolis" class="form-control form-control-sm" value="<?php echo number_format($row['kostos_apostolis'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="text-align:right;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
                </div>
              </div>

              
            </div>
            <?php } ?>
          </div>
        
        
                    
          <?php if ($credit_memo_for_whi_mov_id!=0) { ?>  
          <div class="row" style="margin-top: 10px;padding-top: 10px;border-top: 1px solid lightgray;">
            <div class="col-sm-12 gks_eidos_label">
              <?php echo gks_lang('Το συσχετιζόμενο δελτίο αποστολής στο οποίο αναφέρεται το τρέχον δελτίο αποστολής έχει ποσότητα');?> <b><?php echo myNumberFormatNo0Local($org_products_posotita);?></b>
            </div>
            <div class="col-sm-12 gks_eidos_label">
              <?php 
              $message=gks_lang('Όλα τα συσχετιζόμενα πιστωτικά δελτία αποστολής, εκτός από το τρέχον, είναι <b>[1]</b> και έχουν άθροισμα ποσότητας <b>[2]</b>');
              $message=str_replace('[1]',$others_count,$message);
              $message=str_replace('[2]',myNumberFormatNo0Local($others_products_posotita_sum),$message);
              echo $message;
              ?>
            </div>
            <div class="col-sm-12 gks_eidos_label">
              <?php echo gks_lang('Άρα το τρέχον δελτίο αποστολής θα μπορεί να έχει ως μέγιστη ποσότητα');?> 
              <b><span id="rest_products_posotita_sum" data-val="<?php echo myNumberFormatNo0($rest_products_posotita_sum);?>" style="padding: 0px 6px;border-radius: 10px;"><?php echo myNumberFormatNo0Local($rest_products_posotita_sum);?></span></b>
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
          <div class="form-group row gks_packings_declarations_item align-items-center" data-pdeaa="<?php echo $pde_aa;?>" data-recid="<?php echo $pde_item['id_whi_mov_packings_declarations'];?>">

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
                <?php if ($gks_lock==false and $perm_gks_whi_mov_edit) {?>
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
          <?php echo gks_lang('Αποστολή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('delivery');?>> 


          <div class="row">
            <div class="col-lg-12 col-xl-12" style="margin-bottom:24px;">
              <div style="font-size1:16pt;"><?php echo gks_lang('Τρόποι αποστολής');?>:</div>
              <?php
                
                $div_delivery_number_style ='display:none';
                foreach ($mybasketarray['tropoi_apostolis_all'] as $row_apostoli) {
                  //echo '<pre>';print $row['tropos_apostolis']; print_r($row_apostoli);die();
                  //print $row['tropos_apostolis'];
                  //echo '<pre>';print_r($row_apostoli);echo '</pre>';
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
                <?php if (!$perm_gks_whi_mov_edit) echo 'disabled';?>> 
                <label for="radio_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>" style="cursor: pointer;" class="tooltipsterfast delivery_payment_label" title="<?php echo $row_apostoli['delivery_method_tooltip'];?>"><?php echo $row_apostoli['delivery_method_name'];?>
                  <?php if ($row_apostoli['delivery_method_fees_enabled']!=0) {?>
                    <span class="delivery_payment_price" id="price_delivery_way_<?php echo $row_apostoli['id_delivery_method'];?>" ><?php echo myCurrencyFormat($row_apostoli['dm_calc_kostos'],true,true);?></span>
                  <?php } ?>
                </label>
                <?php
                if ($row_apostoli['id_delivery_method'] == 8) { ?>
                  <span id="span_delivery_id_8" style="<?php echo ($row['tropos_apostolis']==8 ? '' : 'display:none;');?>">
                  <br>
                  <select id="delivery_id_8" name="delivery_id_8" style="width11:90%;" class="form-control form-control-sm myneedsave" <?php if (!$perm_gks_whi_mov_edit) echo 'disabled';?>>
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
            
          </div>
          
          
          <div class="form-group row" id="div_delivery_number" style="<?php echo $div_delivery_number_style;?>">
            <label for="delivery_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Αποστολής');?>:</label>
            <div class="col-md-8">
              <input class="myneedsave form-control form-control-sm" id="delivery_number" type="text" value="<?php echo htmlspecialchars_gks($row['delivery_number']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>>
            </div>
          </div>                   
          <div class="form-group row" id="div_vehicle_number" style="<?php echo $div_delivery_number_style;?>">
            <label for="vehicle_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Μεταφορικού Μέσου');?>:</label>
            <div class="col-md-8">
              <input class="myneedsave form-control form-control-sm" id="vehicle_number" type="text" value="<?php echo htmlspecialchars_gks($row['vehicle_number']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>>
            </div>
          </div>
         
          <div class="form-group row" id="div_dispatch_date" style="<?php echo $div_delivery_number_style;?>">
            <label for="dispatch_date" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημέρα Έναρξης Αποστολής');?>:</label>
            <div class="col-sm-8">
              <input id="dispatch_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (!empty($row['dispatch_date'])) echo  date('d/m/Y',strtotime($row['dispatch_date']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>>
            </div>
          </div> 
          <div class="form-group row" id="div_dispatch_time" style="<?php echo $div_delivery_number_style;?>">
            <label for="dispatch_time" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ώρα Έναρξης Αποστολής');?>:</label>
            <div class="col-sm-8">
              <input id="dispatch_time" type="text" class="form-control form-control-sm myneedsave" value="<?php if (!empty($row['dispatch_time'])) echo  date('H:i',strtotime($row['dispatch_time']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>>
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
            <label for="note_doc" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλια δελτίου');?>:</label>
            <div class="col-md-8">
              <textarea id="note_doc" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_doc']);?></textarea>
            </div>
          </div> 


          <div class="form-group row">
            <label for="note_logistirio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερική σημείωση για λογιστήριο');?>:</label>
            <div class="col-md-8">
              <textarea id="note_logistirio" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_whi_mov_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_logistirio']);?></textarea>
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
$GKS_WHI_MOV_STATUS_BUTTONS=array(
  '010draft' =>           array('cmdupdate','cmddelete','cmdprint',           '080listing','090ekdosi',),
  '040cancelled' =>       array('cmdupdate',            'cmdprint','010draft',),
  '080listing' =>         array('cmdupdate',            'cmdprint','010draft','040cancelled',),
  '090ekdosi' =>          array('cmdupdate',            'cmdprint','010draft','040cancelled',),
  '100payment' =>         array('cmdupdate',            'cmdprint','010draft','040cancelled',),
);

if (isset($GKS_WHI_MOV_STATUS_BUTTONS[$mov_state])) {
  if ($perm_gks_whi_mov_edit) {

    
    if (in_array('cmdupdate',$GKS_WHI_MOV_STATUS_BUTTONS[$mov_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="submit_button_ok_custom" '.($perm_gks_whi_mov_edit ? '' : 'disabled').'>'.gks_lang('Αποθήκευση').'</button> ';
    if (in_array('cmddelete',$GKS_WHI_MOV_STATUS_BUTTONS[$mov_state]) and $id>0) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-danger thisdeleterowbtn" data-id="'.($row['id_whi_mov']>0 ? $row['id_whi_mov'] : '').'" data-model="gks_whi_mov" data-backurl="admin-whi-mov.php" '.($perm_gks_whi_mov_delete ? '' : 'disabled').'>'.gks_lang('Διαγραφή').'</button> ';
    if (in_array('010draft',$GKS_WHI_MOV_STATUS_BUTTONS[$mov_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_whi_mov_state_010draft" id="submit_button_010draft" '.($perm_gks_whi_mov_edit ? '' : 'disabled').'>'.gks_lang('Επαναφορά σε Πρόχειρο').'</button> ';
    if ($whi_eidos_parastatikou_type_id_org!=24 and $cancel_for_whi_mov_id ==0 and in_array('040cancelled',$GKS_WHI_MOV_STATUS_BUTTONS[$mov_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_whi_mov_state_040cancelled" id="submit_button_040cancelled" '.($perm_gks_whi_mov_edit ? '' : 'disabled').'>'.gks_lang('Ακύρωση').'</button> ';
    if (in_array('080listing',$GKS_WHI_MOV_STATUS_BUTTONS[$mov_state])) 
      echo '<button type="button" style="margin-bottom:10px;'.($row['is_xeirografi']==0 ? 'display:none;' : '').'" class="btn button_whi_mov_state_080listing" id="submit_button_080listing" '.($perm_gks_whi_mov_edit ? '' : 'disabled').'>'.getWhiMovStateDescr('080listing').'</button> ';
    if (in_array('090ekdosi',$GKS_WHI_MOV_STATUS_BUTTONS[$mov_state])) 
      echo '<button type="button" style="margin-bottom:10px;'.($row['is_xeirografi']!=0 ? 'display:none;' : '').'" class="btn button_whi_mov_state_090ekdosi" id="submit_button_090ekdosi" '.($perm_gks_whi_mov_edit ? '' : 'disabled').'>'.getWhiMovStateDescr('090ekdosi').'</button> ';
    if (in_array($acc_eidos_parastatikou_id_org,[702,703,704])==false) { /*akurotiko deltio*/
    if ($whi_eidos_parastatikou_type_id_org!=24 and ($mov_state=='090ekdosi' or $mov_state=='100closed') and $row['is_xeirografi']==0 and ($acc_eidos_parastatikou_id_org!=51 and $acc_eidos_parastatikou_id_org!=52)) { 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_whi_mov_state_credit_memo tooltipster" id="submit_button_credit_memo" title="Δημιουργία δελτίου επιστροφής" '.($perm_gks_whi_mov_add ? '' : 'disabled').'>'.gks_lang('Επιστροφή').'</button> ';
    }}
    
    if (($row['from_aade_import']=='' and ($mov_state=='080listing' or $mov_state=='090ekdosi' or $mov_state=='100payment')) and $row['send_mydata']==1) { 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_whi_mov_state_aade_send tooltipster" id="submit_button_aade_send" title="'.gks_lang('Αποστολή myData στην ΑΑΔΕ').'">'.gks_lang('ΑΑΔΕ').'</button> ';
    }
    if (($row['from_aade_import']=='' and ($mov_state=='080listing' or $mov_state=='090ekdosi' or $mov_state=='100payment')) and $row['send_paroxos']==1) { 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_whi_mov_state_paroxos_send tooltipster" id="submit_button_paroxos_send" title="Αποστολή στον πάροχο">'.gks_lang('Πάροχος').'</button> ';
    }    
  }
  
  if (in_array('cmdprint',$GKS_WHI_MOV_STATUS_BUTTONS[$mov_state])) {
    echo '<button type="button" style="margin-bottom:10px;" class="btn btn-dark" id="submit_button_print">'.gks_lang('Εκτύπωση').' <i class="fas fa-print" style="color: #35dc35;font-size: 120%;"></i></button> ';
  }

  if ($id>0 and $perm_gks_whi_mov_add) {
    echo '<a href="admin-whi-mov-item.php?id=-1&template_id='.$id.'" style="margin-bottom:10px;" '.
      'class="btn btn-primary tooltipster" '.
      'id="submit_button_template" '.
      'title="<div style=\'text-align: center;\'>'.gks_lang('Δημιουργία αντιγράφου').'<br>ή<br>'.
              '<button class=\'btn btn-primary btn-sm\' style=\'margin-top:6px;\' '.
              'onclick=\'submit_button_template_create(3);\' '.
              'id=\'submit_button_template_create\' data-obj=\'gks_whi_mov\''.
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
    <div class="col-xl-6">



      <?php echo getObjectRels('gks_whi_mov',$id); ?>   
      <?php echo getActivityObjectTable('gks_whi_mov',$id); ?>
      
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
            $sql_msg="SELECT gks_whi_mov_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_whi_mov_messages LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_whi_mov_messages.whi_mov_id=".$id."
            ORDER BY gks_whi_mov_messages.mydate_add DESC, gks_whi_mov_messages.id_whi_mov_message DESC;";
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg['id_whi_mov_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_msg['whi_mov_message']);
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

          
          
          $query = "SELECT gks_whi_mov_links.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_whi_mov_links LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov_links.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_whi_mov_links.whi_mov_id in (".$id.")
          ORDER BY gks_whi_mov_links.mydate, gks_whi_mov_links.id_whi_mov_links;";
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
            <tr id="tr_links_url_<?php echo $row_list['id_whi_mov_links'];?>">
              <th scope="row" nowrap align="right" class="links_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_list['id_whi_mov_links'];?>" data-deleteafter="gks_fnc_links_delete_after|<?php echo $row_list['id_whi_mov_links'];?>" data-model="gks_whi_mov_links" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>

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
                <div class="progress download-perc" data-id="<?php echo $row_list['id_whi_mov_links'];?>" 
                  style="<?php echo ($row_list['download_status']==1 ? '' : 'display:none;');?>">
                  <div class="download-perc-bar progress-bar progress-bar-striped" 
                    data-id="<?php echo $row_list['id_whi_mov_links'];?>" role="progressbar" 
                    style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>    
                <div class="download-message" 
                  data-id="<?php echo $row_list['id_whi_mov_links'];?>" 
                  style="<?php echo ($row_list['download_status']==3 ? '' : 'display:none;');?>"
                  ><?php echo $row_list['download_message'];?></div>
                
              </td>
              <td nowrap class="download_size_until_now" data-id="<?php echo $row_list['id_whi_mov_links'];?>" style="text-align:right;vertical-align:middle;"><?php if ($row_list['download_size_until_now']>0) echo number_format($row_list['download_size_until_now']/1024/1024,2,$GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB';?></td>  
              <td nowrap class="download_file_td" data-id="<?php echo $row_list['id_whi_mov_links'];?>" style="text-align:center;vertical-align: middle;"><?php
              
              
              // 0 notdownload
              // 1 downloding
              // 2 complete
              // 3 abort
              
              if ($row_list['download_status']==0) { //notdownload
                echo '<i class="fas fa-file-download download_action_start" data-id="'.$row_list['id_whi_mov_links'].'" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==1) { //downloding
                $need_download_timer=1;
                echo '<i class="fas fa-stop-circle download_action_stop" data-id="'.$row_list['id_whi_mov_links'].'" style="font-size:200%;vertical-align:middle;color:red;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==2) { //complete
                echo '<i class="fas fa-check-circle download_action_complete" data-id="'.$row_list['id_whi_mov_links'].'" style="font-size:200%;vertical-align:middle;color:green;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==3) { //abort
                echo '<i class="fas fa-undo download_action_reset" data-id="'.$row_list['id_whi_mov_links'].'" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>';
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
			$obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_whi_mov','id'=>$id));
      echo $obj_fileslist['html'];
      ?>

        
      


      

    </div>

    
    
    <div class="col-xl-6">


      <?php 
      
      if (trim_gks($row['print_date'])!='' or 
          trim_gks($row['print_file_name']) != '' or 
          trim_gks($row['print_file_url']) != '' or 
          $row['print_user_id']>0 or 
          trim_gks($row['print_mov_state']) != '') {?>
      
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
            <div class="col-sm-8"><span class="whi_mov_state_<?php echo $row['print_mov_state'];?>"><?php echo getWhiMovStateDescr($row['print_mov_state']);?></span></div>
          </div>

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αρχείο');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (trim_gks($row['print_file_name'])!='') {
                $local_file=GKS_FileServerShare.'whi/mov/'.$id.'/print/'.$row['print_file_name'];
                if (file_exists($local_file)) {
                  //print_file_url
                  $url_file='admin-get-file.php?fs=fileservers&file=whi%2Fmov%2F'.$id.'%2Fprint%2F'.urlencode($row['print_file_name']);
                  echo '<a href="'.$url_file.'" target="_blank">'.$row['print_file_name'].'</a> ';
                  echo '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
                }
              }
              ?></span></div>
          </div>

        </div>      
      </div>              
      <?php } ?>
      <?php if ($perm_gks_whi_mov_edit) {?>
     
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right gks_unset_height"><span title="<?php echo gks_lang('Μοναδικός Αριθμός Καταχώρησης Παραστατικού');?>" class="tooltipster">ΜΑΡΚ</span>:</label>
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">QR Code Url:</label>
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
                $local_file=GKS_FileServerShare.'whi/mov/'.$id.'/aade_mydata/'.$row['aade_xml_send'];
                if (file_exists($local_file)) {
                  
                  $url_file='admin-get-file.php?fs=fileservers&file=whi%2Fmov%2F'.$id.'%2Faade_mydata%2F'.urlencode($row['aade_xml_send']);
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
                $local_file=GKS_FileServerShare.'whi/mov/'.$id.'/aade_mydata/'.$row['aade_xml_response'];
                if (file_exists($local_file)) {
                  
                  $url_file='admin-get-file.php?fs=fileservers&file=whi%2Fmov%2F'.$id.'%2Faade_mydata%2F'.urlencode($row['aade_xml_response']);
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
          $sql_log="SELECT gks_whi_mov_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_whi_mov_log LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_whi_mov_log.whi_mov_id=".$id."
          ORDER BY gks_whi_mov_log.id_whi_mov_log DESC;";
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_whi_mov']>0) echo $row['id_whi_mov'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">GUID:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['mov_guid'];?></span></div>
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
  if (isset($gks_user_settings['print']['form_id_whi'])) $user_def_form_id=intval($gks_user_settings['print']['form_id_whi']);
  
  $sql_print_forms="SELECT gks_print_forms.*, gks_lang.lang_name
  FROM ((gks_print_objects 
  LEFT JOIN gks_print_objects_forms ON gks_print_objects.id_print_object = gks_print_objects_forms.print_object_id) 
  LEFT JOIN gks_print_forms ON gks_print_objects_forms.print_form_id = gks_print_forms.id_print_form)
  LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  WHERE gks_print_forms.id_print_form is not null and gks_print_forms.is_disable=0 AND gks_print_objects.object_name='gks_whi_mov'
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
      '>'."\n";
      $div_form.='<div class="gks_print_thump_title">'.$print_form_descr.'</div>'."\n";
      $div_form.='<div class="gks_print_thump_lang">'.$print_lang_name.'</div>'."\n";
      $div_form.='<img src="'.$file_thump_url.'" class="gks_print_thump_img" border="0"/>'."\n";
      
    
    $div_form.='</div>'."\n";
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
  $erp_app_id=0;
  if ($mov_whi_seira_id>0) {
    $sql_send_erp_app="SELECT gks_acc_seires.id_acc_seira, gks_acc_seires.erp_app_id, gks_acc_seires.erp_app_dest,  
    gks_acc_seires.erp_app_dest_printer, 
    gks_acc_seires.erp_app_dest_printer_method,
    gks_acc_seires.erp_app_dest_printer_lpr_ip,
    gks_acc_seires.erp_app_dest_printer_copies, 
    gks_acc_seires.erp_app_dest_folder, 
    gks_erp_app.id_erp_app, gks_erp_app.erp_app_name, gks_erp_app.erp_app_last_ping
    FROM gks_acc_seires 
    LEFT JOIN gks_erp_app ON gks_acc_seires.erp_app_id = gks_erp_app.id_erp_app
    where gks_acc_seires.id_acc_seira=".$mov_whi_seira_id;
    
    $result_send_erp_app = $db_link->query($sql_send_erp_app);        
    if (!$result_send_erp_app) {debug_mail(false,'error sql',$sql_send_erp_app);die('sql error');}
    if ($result_send_erp_app->num_rows==1) {
      $row_send_erp_app = $result_send_erp_app->fetch_assoc();
      $erp_app_id=$row_send_erp_app['erp_app_id'];
      

      $send_erp_app_tooltip='';
      $send_erp_app_tooltip.='gks ERP App Desktop: '.trim_gks($row_send_erp_app['erp_app_name']).'<br>';
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
?>    
    <div class="row">  
      <div class="col-sm-12 form-control-sm text-sm-left">
        <input id="gks_print_send_gks_erp_app" type="checkbox" class="form-control form-control-sm switchery1_sel" value="1" <?php if ($send_erp_app_checkbox_disable) echo 'disabled'; else echo 'checked';?>>
        <label for="gks_print_send_gks_erp_app" style="margin: 0px;position: relative;top: 2px;font-size: 0.8rem;"> <?php echo gks_lang('Αποστολή στην εφαρμογή gks ERP App Desktop');?></label>
        <i class="fas fa-info-circle tooltipster" title="<?php echo $send_erp_app_tooltip;?>" style="font-size: 150%;position: relative;top: 4px;"></i>
      </div>
    </div>    
<?php } ?>     
    

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
    
<?php
//print '<pre>';
//print_r($mybasketarray);
//print '</pre>';

?>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_whi_mov';
var from_php_activity_model='gks_whi_mov';
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

var from_php_delivery_way_default=<?php echo $gks_user_settings['gks_whi_mov']['tropos_apostolis'];?>;


var kostos_apostolis_mode='<?php if ($id>0) echo "manual";?>';



var from_php_monades = [];
<?php foreach ($monades as $monada) {
  echo 'from_php_monades.push({id: '.$monada['id'].', descr: $.base64.decode("'.base64_encode($monada['descr']).'"), symbol: $.base64.decode("'.base64_encode($monada['symbol']).'")});'."\n";
}?>
//console.log(from_php_monades);




var from_php_mov_state='<?php echo $mov_state;?>';
var from_php_acc_eidos_parastatikou_id=<?php echo $acc_eidos_parastatikou_id;?>;
var from_php_eidos_parastatikou_type_id=<?php echo $whi_eidos_parastatikou_type_id_org;?>;
var from_php_eidos_parastatikou_need_prev=<?php echo $eidos_parastatikou_need_prev;?>;
var from_php_eidos_parastatikou_need_afm=<?php echo $eidos_parastatikou_need_afm;?>;
var from_php_eidos_parastatikou_stock_pros=<?php echo $whi_eidos_parastatikou_stock_pros_org;?>;
var from_php_acc_eidos_parastatikou_other_entity=<?php echo $acc_eidos_parastatikou_other_entity;?>;
var from_php_journal_has_correlated_invoices=<?php echo $journal_has_correlated_invoices;?>;
var from_php_journal_has_multiple_connected_marks=<?php echo $journal_has_multiple_connected_marks;?>;
var from_php_journal_has_packings_declarations=<?php echo $journal_has_packings_declarations;?>;
var from_php_seira_isdeliverynote=<?php echo $seira_isdeliverynote;?>;

var from_php_print_def_file_type='<?php echo (isset($gks_user_settings['print']['file_type']) ? $gks_user_settings['print']['file_type'] : 'pdf');?>';
var from_php_print_def_grayscale=<?php echo (isset($gks_user_settings['print']['grayscale']) ? $gks_user_settings['print']['grayscale'] : 'false');?>;
var from_php_print_def_landscape=<?php echo (isset($gks_user_settings['print']['landscape']) ? $gks_user_settings['print']['landscape'] : 'false');?>;
var from_php_print_def_zoom=<?php echo (isset($gks_user_settings['print']['zoom']) ? $gks_user_settings['print']['zoom'] : '100');?>;;
var from_php_print_def_form_id=<?php echo (isset($gks_user_settings['print']['form_id_whi']) ? $gks_user_settings['print']['form_id_whi'] : '0');?>;;
var from_php_print_def_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_user_settings['print']['forms_whi']));?>'));

var from_php_is_cancelfor=<?php echo ($cancel_for_whi_mov_id ? 'true' : 'false');?>;
var from_php_is_credit_memo=<?php echo ($credit_memo_for_whi_mov_id ==0 ? 'false' : 'true');?>;



var from_php_enter_order=[];
<?php
if (isset($gks_user_settings['gks_whi_mov']['enter_order']) and is_array($gks_user_settings['gks_whi_mov']['enter_order'])) {
  foreach ($gks_user_settings['gks_whi_mov']['enter_order'] as $value) {
    echo 'from_php_enter_order.push(\''.$value.'\');'."\n";
  } 
}
?>


var from_php_dialog_item_message_email_from_array=[];
<?php 
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_whi_mov','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_whi_mov','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_whi_mov','delete',$id);?>;



var from_php_perm_print_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($perm_print_forms));?>'));



var from_php_check_vies_valid_wait=<?php echo ($mybasketarray['check_vies']['valid']==2 ? 'true' : 'false');?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});



</script>  

<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>gks_country.js"></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>admin-whi-mov-item.js"></script>
<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-whi-mov-item.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

echo gks_from_googlemaps_scripts();


include_once('_my_footer_admin.php');


