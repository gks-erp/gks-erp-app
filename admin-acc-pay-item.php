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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_pay',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_gks_acc_pay_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay','view',0);
$perm_gks_acc_pay_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay','edit',0);
$perm_gks_acc_pay_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay','add',0);
$perm_gks_acc_pay_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay','delete',0);

$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');

$gks_voip_params=gks_voip_user_params();

if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

if ($id==-1) {
  $nav_active_array=array('accounting_pay_new');  
} else {
  $nav_active_array=array('accounting','accounting_pay');
}

$user_companys=gks_get_companys_list();
if (count($user_companys)==0) {
  debug_mail(false,'company not found','');
  echo 'company not found';
  die(); 
}




$def_phone='';
$gks_custom_prepare=gks_custom_table_item_prepare('gks_acc_pay',['from'=>'item']);

$template_id=0; if (isset($_GET['template_id'])) $template_id=intval($_GET['template_id']);
if ($id==-1) {
  
  if ($template_id>0) {
    $sql=select_gks_acc_pay()." where gks_acc_pay.id_acc_pay = ".$template_id;
    if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_pay.company_id in (".implode(',',$perm_id_company_ids).")";
    if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_pay.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
    if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_pay.pay_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
    if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_pay.pay_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
    
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
      
      $row['id_acc_pay']=-1;
      $row['credit_memo_for_acc_pay_id']=0;
      $row['from_aade_import']='';
      $row['pay_guid']='';
      $row['pay_date']=date('Y-m-d H:i:s');
      $row['mydate_add']=null;
      $row['mydate_edit']=null;
      $row['user_id_add']=0;
      $row['user_id_edit']=0;
      $row['gks_nickname_add']='';
      $row['gks_nickname_edit']='';
      $row['myip']='';
      
      $row['pay_state']='010draft';
      $row['pay_acc_number_int']=0;
      $row['pay_acc_number_str']='';

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
      $row['print_pay_state']='';
      
      $row['bank_deposit_9digit']='';
      
      $row['gks_base_template_id']=$template_id;
      $my_page_title=gks_lang('Νέα Πληρωμή από το πρότυπο').' #'.$template_id;
    }
      
            
  }
  //echo '<pre>';print $template_id;die();
  
  if ($template_id==0) {  
  
    $my_page_title=gks_lang('Νέα Πληρωμή');
    $row=array();
  
    $row['id_acc_pay']=-1;
    $row['credit_memo_for_acc_pay_id']=0;
    $row['from_aade_import']='';
    $row['pay_guid']='';
    $row['pay_date']=date('Y-m-d H:i:s');
    $row['mydate_add']=null;
    $row['mydate_edit']=null;
    $row['user_id_add']=0;
    $row['user_id_edit']=0;
    $row['gks_nickname_add']='';
    $row['gks_nickname_edit']='';
    $row['myip']='';
  
    $row['pay_state']='010draft';
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
    $row['ma_odos']='';
    $row['ma_arithmos']='';
    $row['ma_orofos']='';
    $row['ma_perioxi']='';
    $row['ma_poli']='';
    $row['ma_tk']='';
    $row['ma_country_id']=0;
    $row['ma_nomos_id']=0;
    $row['country_name']='';
    $row['nomos_descr']='';
    
    
    $row['note_doc']='';
    $row['note_logistirio']='';
  
    $row['gks_price_total']=0;
    
    $row['pelati_sxolio']='';
    $row['order_sxolio']='';
    
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
    $row['pay_acc_journal_id']=0;
    $row['pay_acc_seira_id']=0;
    $row['pay_acc_seira_code']='';
    $row['pay_acc_number_int']=0;
    $row['pay_acc_number_str']='';
    $row['is_xeirografi']=0;
    
    $row['acc_eidos_parastatikou_id']=0;
    $row['eidos_parastatikou_type_id']=0;
    $row['antisimvalomenos_label']=gks_lang('Πελάτης');
    $row['eidos_parastatikou_need_prev']=0;
    $row['eidos_parastatikou_balance_pros']=0;
    $row['acc_eidos_parastatikou_other_entity']=0;
    $row['journal_has_correlated_invoices']=0;
    $row['journal_has_multiple_connected_marks']=0;
        
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
    $row['print_pay_state']='';
  
    $row['affect_balance']=1;
    $row['affect_balance_all_poso']=1;
    $row['affect_balance_all_poso_type']='price_total';
    $row['affect_balance_poso']=0;
     
    $row['pay_poso_str']='';

    
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
    $row['bank_deposit_9digit']='';
    
  }
  if (isset($gks_user_settings['gks_acc_pay']['def_values'])) {
    $def_values=unserialize($gks_user_settings['gks_acc_pay']['def_values']);  
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


  $sql=select_gks_acc_pay()." where gks_acc_pay.id_acc_pay = ".$id;
  if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_pay.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_pay.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_pay.pay_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_pay.pay_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  
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
  $my_page_title=gks_lang('Πληρωμή').': #'.$id;
  
  if ($row['pay_date']=='') $row['pay_date']=$row['mydate_add'];
 
 
  
  $sql_def_phone="SELECT comm_value
  FROM gks_users_communication
  WHERE user_id=".$row['user_id']." AND comm_primary=1 AND comm_type='phone' and comm_value<>'' order by id_user_communication desc";
  $result_def_phone = $db_link->query($sql_def_phone);        
  if (!$result_def_phone) {
    debug_mail(false,'error sql',$sql_def_phone);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  }
  if ($result_def_phone->num_rows>=1) {
    $row_def_phone = $result_def_phone->fetch_assoc();
    $def_phone=trim_gks($row_def_phone['comm_value']);
  }
  
}
$pay_acc_seira_id=$row['pay_acc_seira_id'];
$paroxos_status=intval($row['paroxos_status']);
$aade_paroxos_id=intval($row['aade_paroxos_id']);
$print_pay_state=trim_gks($row['print_pay_state']);
$paroxos_send_pdf=trim_gks($row['paroxos_send_pdf']);
$print_file_name=trim_gks($row['print_file_name']);
$curr_pay_state=trim_gks($row['pay_state']);

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;

if ($row['pay_state']=='040cancelled' or $row['pay_state']=='080listing' or $row['pay_state']=='090ekdosi') {
  $gks_lock=true;
} else {
  if ($row['pay_acc_number_int'] > 0 and $row['is_xeirografi']==0 and ($row['pay_state']=='010draft')) {
    $gks_number_lock=true;
  }
}



$user_id=$row['user_id'];

$credit_memo_for_acc_pay_id=$row['credit_memo_for_acc_pay_id'];

$antisimvalomenos_label=$row['antisimvalomenos_label'];
$acc_eidos_parastatikou_id=intval($row['acc_eidos_parastatikou_id']);
$eidos_parastatikou_type_id=intval($row['eidos_parastatikou_type_id']);
$eidos_parastatikou_need_prev=intval($row['eidos_parastatikou_need_prev']);
$eidos_parastatikou_balance_pros=intval($row['eidos_parastatikou_balance_pros']);

$antisimvalomenos_label_org=$antisimvalomenos_label;
$acc_eidos_parastatikou_id_org=$acc_eidos_parastatikou_id;
$eidos_parastatikou_type_id_org=$eidos_parastatikou_type_id;

$acc_eidos_parastatikou_other_entity=intval($row['acc_eidos_parastatikou_other_entity']);
$journal_has_correlated_invoices=intval($row['journal_has_correlated_invoices']);
$journal_has_multiple_connected_marks=intval($row['journal_has_multiple_connected_marks']);


$pay_poso=array();
$pay_poso_str=trim_gks($row['pay_poso_str']);
if ($pay_poso_str!='') {
  $pay_poso=unserialize($pay_poso_str);
  if (is_array($pay_poso)==false) $pay_poso=array();
}

//print '<pre>';print_r($pay_poso);die();

$credit_memo_descr_for='';
$credit_memo_descr_by='';
if ($credit_memo_for_acc_pay_id!=0) { //check ean einai akirotiko gia allo
  $org_gks_price_total=0;
  $others_count=0;
  $others_gks_price_total_sum=0;
  $rest_gks_price_total_sum=0;
  
  $sql_credit_memo="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
  gks_acc_pay.pay_acc_number_int, gks_acc_pay.pay_acc_number_str, gks_acc_pay.pay_acc_ekdosi_date, gks_acc_pay.pay_date,gks_acc_pay.pay_state,
  gks_acc_pay.gks_price_total
  FROM (((gks_acc_pay 
  LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
  LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira
  where gks_acc_pay.id_acc_pay=".$credit_memo_for_acc_pay_id;
  $result_credit_memo = $db_link->query($sql_credit_memo);        
  if (!$result_credit_memo) {debug_mail(false,'error sql',$sql_credit_memo);die('sql error');}
  if ($result_credit_memo->num_rows==0) {
    $credit_memo_descr_for=gks_lang('Δεν βρέθηκε το παραστατικό με ID').': '.
    '<a href="admin-acc-pay-item.php?id='.$credit_memo_for_acc_pay_id.'" class="alert-link">'.$credit_memo_for_acc_pay_id.'</a>';
    debug_mail(false,'record parent not found sql',$sql_credit_memo); 
    //die('no record found (2)');
  } else {
    $row_credit_memo = $result_credit_memo->fetch_assoc();
  
    $org_gks_price_total=$row_credit_memo['gks_price_total'];

  
    $antisimvalomenos_label=$row_credit_memo['antisimvalomenos_label'];
    $acc_eidos_parastatikou_id=intval($row_credit_memo['acc_eidos_parastatikou_id']);
    $eidos_parastatikou_type_id=intval($row_credit_memo['eidos_parastatikou_type_id']);
    $eidos_parastatikou_need_prev=intval($row_credit_memo['eidos_parastatikou_need_prev']);
    //$eidos_parastatikou_balance_pros=intval($row_credit_memo['eidos_parastatikou_balance_pros']);
    
    $credit_memo_descr_for=
        '<a href="admin-acc-pay-item.php?id='.$credit_memo_for_acc_pay_id.'" class="alert-link">'.
        '#' .$credit_memo_for_acc_pay_id.', '.
        $row_credit_memo['acc_journal_descr'].', '.
        $row_credit_memo['seira_code'].' - '.$row_credit_memo['seira_descr'].', '.
        showDate(strtotime($row_credit_memo['pay_date']), 'd/m/Y H:i', 1).', '.
        $row_credit_memo['pay_acc_number_int'].', '.
        '<span class="acc_pay_state_'.$row_credit_memo['pay_state'].'">'.getAccPayStateDescr($row_credit_memo['pay_state']).'</span></a>';
  }
  
  $sql_others="SELECT count(*) as others_count, Sum(gks_price_total) AS gks_price_total_sum
  FROM gks_acc_pay
  WHERE credit_memo_for_acc_pay_id=".$credit_memo_for_acc_pay_id." AND id_acc_pay<>".$id;
  //echo $sql_others; die();
  $result_others = $db_link->query($sql_others);        
  if (!$result_others) {debug_mail(false,'error sql',$sql_others);die('sql error');}
  if ($result_others->num_rows>=1) {
    $row_others = $result_others->fetch_assoc();
    if (empty($row_others['gks_price_total_sum'])==false) $others_gks_price_total_sum=floatval($row_others['gks_price_total_sum']);
    if (empty($row_others['others_count'])==false) $others_count=floatval($row_others['others_count']);
  }
  $rest_gks_price_total_sum=$org_gks_price_total-$others_gks_price_total_sum;
  
  
  $gks_number_lock=true;
  $gks_user_lock=true;
  //$gks_lock=true;
}


$credit_memo_descr_by='';
if ($id>=0) {
  //check ean einai credit_memo
  $sql_credit_memo="SELECT gks_acc_pay.id_acc_pay, gks_acc_pay.pay_date, gks_acc_pay.pay_state, 
  gks_acc_pay.pay_acc_number_int, gks_acc_pay.pay_acc_number_str, gks_acc_journal.acc_journal_descr, 
  gks_acc_seires.seira_code, gks_acc_seires.seira_descr
  FROM (gks_acc_pay 
  LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira
  WHERE gks_acc_pay.credit_memo_for_acc_pay_id=".$id;
  $result_credit_memo = $db_link->query($sql_credit_memo);  
  if (!$result_credit_memo) {
    debug_mail(false,'error sql',$sql_credit_memo);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_credit_memo->num_rows>=1) {
    $tmp_array=array();
    while ($row_credit_memo = $result_credit_memo->fetch_assoc()) {
      

      
        $tmp_array[]=
        '<a href="admin-acc-pay-item.php?id='.$row_credit_memo['id_acc_pay'].'" class="alert-link">'.
        '#' .$row_credit_memo['id_acc_pay'].', '.
        $row_credit_memo['acc_journal_descr'].', '.
        $row_credit_memo['seira_code'].' - '.$row_credit_memo['seira_descr'].', '.
        showDate(strtotime($row_credit_memo['pay_date']), 'd/m/Y H:i', 1).', '.
        $row_credit_memo['pay_acc_number_int'].', '.
        '<span class="acc_pay_state_'.$row_credit_memo['pay_state'].'">'.getAccPayStateDescr($row_credit_memo['pay_state']).'</span></a>';
      
    }
    if (count($tmp_array)==1) {
      $credit_memo_descr_by=gks_lang('Το συσχετιζόμενο παραστατικό είναι το').': '.$tmp_array[0];
    } else if (count($tmp_array)>=2) {
      $credit_memo_descr_by=gks_lang('Τα συσχετιζόμενα παραστατικό είναι τα').':<br>'.implode('<br>',$tmp_array);
    }
    
    
    
  }
}





$pelati_sxolio=nl2br_gks($row['pelati_sxolio']);
$order_sxolio=nl2br_gks($row['order_sxolio']);






$pay_state=$row['pay_state'];
$pay_acc_journal_id=$row['pay_acc_journal_id'];


$sql_paymethods="select id_payment_acquirer as id, 
payment_acquirer_name as descr,
aade_tropos_pliromis_id,
payment_acquirer_with_id
from gks_payment_acquirers
where show_acc_pay<>0 order by mysortorder";
$result_paymethods = $db_link->query($sql_paymethods);        
if (!$result_paymethods) {debug_mail(false,'error sql',$sql_paymethods); die('sql error');}

$paymethods_array=array();
while ($paymethod = $result_paymethods->fetch_assoc()) {
  $paymethods_array[]=$paymethod;
}

$sql_eidi="SELECT gks_acc_pay_method.*,
gks_payment_acquirers.payment_acquirer_name,
gks_payment_acquirers.aade_tropos_pliromis_id,
gks_payment_acquirers.payment_acquirer_with_id
FROM gks_acc_pay_method 
LEFT JOIN gks_payment_acquirers ON gks_acc_pay_method.paymethod_id = gks_payment_acquirers.id_payment_acquirer
WHERE gks_acc_pay_method.acc_pay_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1'))."
ORDER BY gks_acc_pay_method.paymethod_aa;";
//gks_acc_pay_products.product_set
$result_eidi = $db_link->query($sql_eidi);        
if (!$result_eidi) {debug_mail(false,'error sql',$sql_eidi); die('sql error');}

$eidos_array = array();
$products_count=0;

$id_acc_pay_method_array=array();

while ($eidos = $result_eidi->fetch_assoc()) {
  //if ($eidos['paymethod_id']==2) $eidos['paymethod_id']=0;
  $eidos['payments']=array();
  $eidos_array[]=$eidos;
  $products_count++;
  $id_acc_pay_method_array[]=$eidos['id_acc_pay_method'];

}
//print '<pre>';print_r($eidos_array);die();

unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='acc_pay';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']= $row['company_id'];
$mybasketarray['company_sub_id']= $row['company_sub_id'];
$mybasketarray['user']['user_id']=$user_id;
$mybasketarray['user']['afm']=$row['afm'];
$mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
$mybasketarray['parastatiko']=1;
gks_CheckAFM_Live($mybasketarray);
$check_vies=$mybasketarray['check_vies'];

//print '<pre>';print_r($mybasketarray['check_vies']);//die();





$payment_type_multi=array();

$transaction_id_ids=[];
if ($id>0 or $template_id>0) {
  $sql_payments="SELECT id_acc_pay_payment,
  acc_pay_method_id,
  payment_acquirer_id, poso,asset_id,
  transaction_id,transaction_pa_with_id,
  gks_payment_acquirers.payment_acquirer_name, 
  aade_tropos_pliromis_id,
  gks_payment_acquirers.payment_acquirer_with_id,
  gks_assets.asset_code, gks_assets.asset_title
  FROM (gks_acc_pay_payment 
  LEFT JOIN gks_assets ON gks_acc_pay_payment.asset_id = gks_assets.id_asset) 
  LEFT JOIN gks_payment_acquirers ON gks_acc_pay_payment.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer
  WHERE gks_acc_pay_payment.acc_pay_id=";
  if ($id>0) $sql_payments.=$id;
  else if ($id==-1 and $template_id>0) $sql_payments.=$template_id." and gks_acc_pay_payment.poso>0";
  //else $sql_payments.="-1";
  $sql_payments.=" ORDER BY gks_acc_pay_payment.pp,id_acc_pay_payment";
  
  $result_payments = $db_link->query($sql_payments);        
  if (!$result_payments) {debug_mail(false,'error sql',$sql_payments);die('sql error2');}
  
  while ($row_payments = $result_payments->fetch_assoc()) {
    $row_payments['acc_pay_method_id']=intval($row_payments['acc_pay_method_id']);
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
      $row_payments['id_acc_pay_payment']=0;
      $row_payments['transaction_id']=0;
      $row_payments['transaction_pa_with_id']=0;
      
    } 
    
    $payment_type_multi[]=$row_payments;
    
  }
  //if (count($payment_type_multi)>=2) $tropos_pliromis_one_multi=1;
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
//print '<pre>payments_lock_level '.$payments_lock_level;die();

foreach ($eidos_array as &$eidos) {
  foreach ($payment_type_multi as $pmvalue) {
    if ($pmvalue['acc_pay_method_id']==$eidos['id_acc_pay_method']) {
      $eidos['payments'][]=$pmvalue;
    }
  }
}
unset($eidos);

foreach ($eidos_array as &$eidos) {
  if (count($eidos['payments'])==0) {
    $eidos['payments'][]=array(
      'id_acc_pay_payment'=>0,
      'acc_pay_method_id'=>$eidos['id_acc_pay_method'],
      'payment_acquirer_id' => $eidos['paymethod_id'],
      'poso' => $eidos['paymethod_total'],
      'asset_id' => 0,
      'transaction_id' => 0,
      'transaction_pa_with_id' => 0,
      'payment_acquirer_name' => $eidos['payment_acquirer_name'],
      'aade_tropos_pliromis_id' => $eidos['aade_tropos_pliromis_id'],
      'payment_acquirer_with_id' => $eidos['payment_acquirer_with_id'],
      'asset_code' => 0,
      'asset_title' => '',
    );
  }
}
unset($eidos);
//echo '<pre>';print_r($eidos_array);die();


$sql_multiple_connected_marks="SELECT gks_acc_pay_multiple_connected_marks.* 
FROM gks_acc_pay_multiple_connected_marks 
where acc_pay_id=".($id > 0 ? $id : ($template_id > 0 ? $template_id : '-1 and 1=2'))."
order by mcm_aa";
$result_multiple_connected_marks = $db_link->query($sql_multiple_connected_marks);        
if (!$result_multiple_connected_marks) {debug_mail(false,'error sql',$sql_multiple_connected_marks); die('sql error');}
$multiple_connected_marks_array=array();
while ($row_multiple_connected_marks = $result_multiple_connected_marks->fetch_assoc()) {
  if ($id<0) $row_multiple_connected_marks['id_acc_pay_multiple_connected_marks']=0;
  $row_multiple_connected_marks['mcm_mark']=trim_gks($row_multiple_connected_marks['mcm_mark']);
  if ($row_multiple_connected_marks['mcm_mark']=='') {
    $row_multiple_connected_marks['mcm_mark']=gks_aade_get_mark_from_id($row_multiple_connected_marks);
  }
  $multiple_connected_marks_array[]=$row_multiple_connected_marks;
}

gks_cache_admin_acc_pay_item();



stat_record();

include_once('_my_header_admin.php');
//print '<pre>';
//print_r($row);
//print '</pre>';


?>

<link href="css/admin-eftpos-transaction-dialog.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link href="css/admin-acc-pay-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Πληρωμή');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Πληρωμή');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>


<?php if ($credit_memo_for_acc_pay_id!=0) {?>
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
            <label for="pay_acc_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                $sql="SELECT eidos_parastatikou_type_id,eidos_parastatikou_balance_pros
                from gks_acc_journal
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                where id_acc_journal=".$row['pay_acc_journal_id'];
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                $eidos_parastatikou_type_id=0;
                $eidos_parastatikou_balance_pros=0;
                if ($result_select->num_rows==1) {
                  $row_select = $result_select->fetch_assoc();
                  $eidos_parastatikou_type_id=intval($row_select['eidos_parastatikou_type_id']);
                  $eidos_parastatikou_balance_pros=intval($row_select['eidos_parastatikou_balance_pros']);
                }
                
                echo '<input id="pay_acc_journal_id" type="hidden" value="'.$row['pay_acc_journal_id'].'" '.
                'data-type_id="'.$eidos_parastatikou_type_id.'" '.
                'data-balance_pros="'.$eidos_parastatikou_balance_pros.'" '.
                '>';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['acc_journal_descr'];
                echo '</div>';
              } else {?>
              <select id="pay_acc_journal_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_journal, acc_journal_descr, acc_eidos_parastatikou_id, 
                eidos_parastatikou_type_id, eidos_parastatikou_need_prev, 
                eidos_parastatikou_balance_pros,
                acc_eidos_parastatikou_other_entity,
                journal_has_correlated_invoices,
                journal_has_multiple_connected_marks
                FROM gks_acc_journal 
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                WHERE eidos_parastatikou_type_id in (11,12) and id_acc_eidos_parastatikou not in (702,703,704)
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
                  'data-balance_pros="'.$row_select['eidos_parastatikou_balance_pros'].'" '.
                  'data-other_entity="'.intval($row_select['acc_eidos_parastatikou_other_entity']).'" '. 
                  'data-correlated_invoices="'.intval($row_select['journal_has_correlated_invoices']).'" '. 
                  'data-multiple_connected_marks="'.intval($row_select['journal_has_multiple_connected_marks']).'" '; 
                  
                  if ($row['pay_acc_journal_id'] == $row_select['id_acc_journal']) echo ' selected ';
                  echo '>'.$row_select['acc_journal_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="pay_acc_seira_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="pay_acc_seira_id" type="hidden" value="'.$row['pay_acc_seira_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['seira_code'].' - '.$row['seira_descr'];
                echo '</div>';
              } else {?>
              <select id="pay_acc_seira_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_seira, seira_code,seira_descr,is_xeirografi 
                FROM gks_acc_seires 
                WHERE is_disable=0 and acc_journal_id=".$row['pay_acc_journal_id'];
                if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_seires.id_acc_seira in (".implode(',',$perm_id_acc_seira_ids).")";
                $sql.=" ORDER BY sortorder,seira_code;";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_seira'].'" '.
                  'data-is_xeirografi="'.$row_select['is_xeirografi'].'" ';
                  if ($row['pay_acc_seira_id'] == $row_select['id_acc_seira']) echo ' selected ';
                  echo '>'.$row_select['seira_code'].' - '.$row_select['seira_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div> 

          <div class="form-group row">
            <label for="pay_acc_number_int" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['pay_acc_number_int'];
                echo '</div>';
              } else {?>
              <input id="pay_acc_number_int" class="form-control form-control-sm myneedsave" type="number" 
              value="<?php if ($row['pay_acc_number_int']>0) echo $row['pay_acc_number_int'];?>" style="max-width:100px;" 
              placeholder="" min="0" step="1"
              <?php if ($gks_number_lock or $row['is_xeirografi']==0) echo 'disabled';?>>
              <?php } ?>
            </div>
          </div> 

                              
          <div class="form-group row">
            <label for="pay_date" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-sm-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if (isset($row['pay_date'])) echo  showDate(strtotime($row['pay_date']), 'd/m/Y H:i', 1);
                echo '</div>';
              } else {?>
              <input id="pay_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['pay_date'])) echo  showDate(strtotime($row['pay_date']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" <?php if (!$perm_gks_acc_pay_edit) echo 'readonly';?>>
              <?php } ?>
            </div>
          </div> 


          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-sm-8">
              <span class="acc_pay_state_<?php echo $row['pay_state'];?>"><?php echo getAccPayStateDescr($row['pay_state']);?></span>
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


      

    </div>
    
    
    <div class="col-md-8">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center" id="antisimvalomenos_label">
          <?php echo $antisimvalomenos_label;?>
        </div>
        <div class="card-body" <?php echo gks_card_body('user');?>>

 
          <div id="div_show_user" style="">
          
            <div class="form-group row">
              <label for="user" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
              <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if ($user_id>0)
                    echo '<a href="admin-users-item.php?id='.$user_id.'" class="email_contact_name">'.$row['gks_nickname'].'</a>';
                  else
                     echo '<span class="email_contact_name">'.$row['gks_nickname'].'</span>';
                  echo '<input type="hidden" id="user_id" value="'.$row['user_id'].'">';
                echo '</div>';                
              } else {?>
                    <input id="user" type="text" class="form-control form-control-sm myneedsave email_contact_name"  <?php if ($gks_user_lock) echo 'disabled';?>
                    value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
                    style="width:calc(98% - 22px);display:inline;" 
                    placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" <?php if (!$perm_gks_acc_pay_edit) echo 'readonly';?>>
                    <input id="user_id" type="hidden" value="<?php echo $user_id;?>" class="myneedsave">
                    <?php if ($perm_gks_acc_pay_edit) {?>
                    <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $user_id;?>" style="<?php if ($user_id==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
                    <?php } ?>
              <?php } ?>
              </div>

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
                <div class="form-control-sm gks_unset_height" id="dr_user_first_name">
                  <?php echo $row['user_first_name'];?>
                </div>
              </div>
              <label for="dr_user_last_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επώνυμο');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_last_name">
                  <?php echo $row['user_last_name'];?>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="dr_user_email_div" class="col-sm-2 col-form-label form-control-sm text-sm-right">email:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_email_div2">
                  <?php if (isset($row['user_email'])) echo '<a href="mailto:'.$row['user_email'].'">'.$row['user_email'].'</a>';?>
                  <input id="dr_user_email" type="hidden" value="<?php echo htmlspecialchars_gks($row['user_email']);?>">
                </div>
              </div>
              <label for="dr_user_mobile" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_mobile">
                  <?php if ($def_phone!='') echo '<a href="tel:'.$def_phone.'" class="'.$gks_voip_params['class_span'].'">'.$def_phone.'</a>';
                  echo $gks_voip_params['html_after_span'];
                  ?>
                  
                </div>                
              </div>
            </div>
                
            <div class="form-group row">
              <label for="dr_user_lang" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γλώσσα');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_lang" data-val="<?php echo $row['user_lang'];?>">
                  <?php echo $row['lang_name'];?>
                </div>
                
              </div>
            </div>
            
            <div class="form-group row">
              <label for="dr_user_eponimia" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επωνυμία');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_eponimia">
                  <?php echo $row['eponimia'];?>
                </div>
              </div>
              <label for="dr_user_title" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τίτλος');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_title">
                  <?php echo $row['title'];?>
                </div>
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
            ?>
            
            
            <div class="form-group row">
              <label for="dr_user_afm" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height">
                	
                  <span id="dr_user_afm_ee_initial_static" style="<?php echo ($ee_initials!='' ? '' : 'display:none;');?>"><?php echo $ee_initials;?></span><span 
                    style="display: inline-block;text-align:left;vertical-align: middle;"
                    id="dr_user_afm" class=" <?php echo ($ee_initials=='' ? '':'dr_user_afm_views');?>"><?php echo htmlspecialchars_gks($row['afm']);?></span><span 
                    id="dr_user_afm_views_run_static" style="height:25px;<?php echo ($check_vies['run'] ? '' : 'display:none;');?>"><?php echo $check_vies['views_run_img'];?></span>
              
                </div>
              </div>
              <label for="dr_user_doy" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_doy">
                  <?php echo $row['doy'];?>
                </div>
              </div>
            </div>


            <div class="form-group row">
              <label for="dr_user_epaggelma" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
              <div class="col-sm-10">
                <div class="form-control-sm gks_unset_height" id="dr_user_epaggelma">
                  <?php echo $row['epaggelma'];?>
                </div>
              </div>
            </div>  


            <div class="form-group row">
              <label for="dr_user_ma_odos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_ma_odos">
                  <?php echo $row['ma_odos'];?>
                </div>
              </div>
              <label for="dr_user_ma_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_ma_arithmos">
                  <?php echo $row['ma_arithmos'];?>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label for="dr_user_ma_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_ma_orofos">
                  <?php echo $row['ma_orofos'];?>
                </div>
              </div>
              <label for="dr_user_ma_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_ma_perioxi">
                  <?php echo $row['ma_perioxi'];?>
                </div>
              </div>
            </div>  
            <div class="form-group row">
              <label for="dr_user_ma_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_ma_poli">
                  <?php echo $row['ma_poli'];?>
                </div>
              </div>
              <label for="dr_user_ma_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_ma_tk">
                  <?php echo $row['ma_tk'];?>
                </div>
              </div>
            </div>
  
            <div class="form-group row">
              <label for="dr_user_ma_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_ma_country_id" data-id="<?php echo $row['ma_country_id'];?>">
                  <?php 
                  echo gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');
                  ?>
                </div>
              </div>
              <label for="dr_user_ma_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
              <div class="col-sm-4">
                <div class="form-control-sm gks_unset_height" id="dr_user_ma_nomos_id">
                  <?php 
                  echo gks_lang_data_trans($row['nomos_descr'],$row['ma_nomos_id'],'gks_nomoi','nomos_descr');
                  ?>
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
          
          <div class="form-group row gks_multiple_connected_marks_item align-items-center" data-mcmaa="<?php echo $mcm_aa;?>" data-recid="<?php echo $ci_item['id_acc_pay_multiple_connected_marks'];?>">

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
                <?php if ($gks_lock==false and $perm_gks_acc_pay_edit) {?>
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
          <?php echo gks_lang('Τρόποι πληρωμής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('eidi');?> id="eidi_table"> 
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
            
            $gkscols1 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col';
            $gkscols2 ='col-12 col-sm-6  col-md-8  col-lg-2 gks_items_col';
            $gkscols3 ='col-12 col-sm-6  col-md-4  col-lg-3 gks_items_col';
            $gkscols5 ='col-12  col-sm-6  col-md-5  col-lg-3 gks_items_col';
            $gkscols7 ='col-6  col-sm-6  col-md-2  col-lg-1 gks_items_col';            
            $gkscols8 ='col-6  col-sm-6  col-md-1  col-lg-1 gks_items_col';            
            //$gkscols6 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';            
            //$gkscols4 ='col-6  col-sm-3  col-md-2  col-lg-1 gks_items_col';
            //$gkscols9 ='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';            
            //$gkscols10='col-6  col-sm-3  col-md-1  col-lg-1 gks_items_col';    
            
//            if ($perm_gks_acc_pay_edit) {
//
//            } else {
//              $gkscols1 ='col-12 col-sm-4  col-md-4  col-lg-2 gks_items_col';
//              $gkscols2 ='col-12 col-sm-8  col-md-8  col-lg-5 gks_items_col';
//              $gkscols3 ='col-12 col-sm-8  col-md-8  col-lg-4 gks_items_col';
//              $gkscols5 ='col-12 col-sm-4  col-md-4  col-lg-1 gks_items_col';            
//            }
          
          
          ?>
          
          
          <div class="form-group row gks_eidos_label">
            <div class="<?php echo $gkscols1;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Τύπος');?></div>
            </div>
            <div class="<?php echo $gkscols2;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Περιγραφή');?></div>
            </div>
            <div class="<?php echo $gkscols3;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Παρατηρήσεις');?></div>
            </div>  
            <div class="<?php echo $gkscols5;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('EFT/POS');?></div>
            </div>
            <div class="<?php echo $gkscols7;?>">
              <div class="table-dark gks_eidos_label"><?php echo gks_lang('Ποσό');?></div>
            </div>
            <div class="<?php echo $gkscols8;?>">
              <div class="table-dark gks_eidos_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
            </div> 
          </div>           
<?php 

  



    $aa=0;
    $pp=0;
    $eidi_sum_price_net=0;
    $fields_change=array();
    foreach ($eidos_array as $eidos) {

      $aa++;$pp++;
      $eidi_sum_price_net+=$eidos['paymethod_total'];
  

      
      
?>        

          <div class="form-group row gks_eidos " 
            data-recid="<?php echo ($template_id==0 ? $eidos['id_acc_pay_method'] : '0');?>" 
            data-aa="<?php echo $aa;?>"
            data-pp="<?php echo $pp;?>">
            <div class="<?php echo $gkscols1;?>">
              <?php 
              $has_transaction_id=false; 
              foreach ($eidos['payments'] as $value) {
                if ($value['transaction_id']>0) {
                  $has_transaction_id=true;
                  break;
                }
              }
              
              if ($payments_lock_level>=4 or $has_transaction_id or $gks_lock) {
                echo '<div class="gks_flock gks_flock_small form-control-sm">'.
                       '<input class="gks_code" data-aa="'.$aa.'" type="hidden" value="'.$eidos['paymethod_id'].'">'.
                       '<span>'.
                         $eidos['payment_acquirer_name'].
                       '</span>'.
                     '</div>';
              } else {?>
              <select class="div_payment_type_multi_item_select gks_code form-control form-control-sm" 
                data-aa="<?php echo $aa;?>" 
                data-pp="<?php echo $pp;?>" 
              <?php if (!$perm_gks_acc_pay_edit) echo 'readonly';?>>
              <?php
              foreach ($paymethods_array as $value) {
                 echo '<option value="'.$value['id'].'" '.
                 ($value['id']==$eidos['paymethod_id'] ? ' selected ': '').
                 ' data-aade_id="'.$value['aade_tropos_pliromis_id'].'"'.
                 ' data-payment_acquirer_with_id="'.$value['payment_acquirer_with_id'].'"'.
                 '>'.$value['descr'].'</option>';
              } 
              ?>
              
              </select>
              <?php } ?>
            </div>
            <div class="<?php echo $gkscols2;?>">
              <div class="text-left"><?php 

              if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm gks_descr">';
                  echo htmlspecialchars_gks($eidos['paymethod_descr']);
                echo '</div>';                
              } else {
                echo '<textarea class="gks_descr form-control form-control-sm" rows="1" data-aa="'.$aa.'"   placeholder="'.gks_lang('Περιγραφή').'">'.htmlspecialchars_gks($eidos['paymethod_descr']).'</textarea>';
              }
              ?>
              
              </div>
            </div>
            <div class="<?php echo $gkscols3;?>">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm gks_comments">';
                  echo nl2br_gks(htmlspecialchars_gks($eidos['paymethod_comments']));
                echo '</div>';
              } else {?>
              <textarea class="gks_comments form-control form-control-sm" rows="1" data-aa="<?php echo $aa;?>" <?php if (!$perm_gks_acc_pay_edit) echo 'readonly';?> placeholder="<?php echo gks_lang('Σχόλιο');?>"><?php echo htmlspecialchars_gks($eidos['paymethod_comments']);?></textarea>
              <?php } ?>
            </div>
            
            <div class="<?php echo $gkscols5;?>">
              <?php
              $iii=0;
              foreach ($eidos['payments'] as $pmvalue) {
                  if ($iii>0) $pp++;
                  $iii++;
                  
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
                  <div data-pp="<?php echo $pp;?>" data-rec_id="<?php echo $pmvalue['id_acc_pay_payment'];?>" class="div_payment_type_multi_item <?php echo $multi_item_extra_class;?>">

                    <div class="div_payment_type_multi_item_row2" <?php echo $pos_style1;?>> 
                      <?php
                      if ($pmvalue['transaction_id']>0) {
                        echo '<div class="div_payment_type_multi_item_row2_text">';
                        //'plirome me: '.$pmvalue['asset_title'].
                        
                        $ret=gks_eftpos_get_transaction_html(['id_eftpos_transaction'=> $pmvalue['transaction_id']]);
                        //echo '<pre>';print_r($ret);echo '</pre>';
                        if (isset($ret['transaction']['html'])) {
                          echo $ret['transaction']['html'];
                        }
                        echo '</div>'.
                        '<span class="div_payment_type_multi_item_pos_terminal" data-asset_id="'.$pmvalue['asset_id'].'" data-aa="'.$aa.'" data-pp="'.$pp.'" style="display:none;"/>';
                      } else {
                      ?>
                      <button data-pp="<?php echo $pp;?>" 
                        class="btn btn-sm btn-primary div_payment_type_multi_item_pos_start"><?php echo gks_lang('Πληρωμή με');?>:</button>
                      <input  
                      data-aa="<?php echo $aa;?>"
                      data-pp="<?php echo $pp;?>" 
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
                  
                <?php 
                
                
              }              
              
            ?>  
            </div>

            


            <div class="<?php echo $gkscols7;?>">
              
              
              <?php if ($payments_lock_level>=4 or $has_transaction_id or $gks_lock) {
                echo '<div class="gks_flock form-control-sm gks_price_lock">';
                  echo '<input class="gks_price div_payment_type_multi_item_input" data-aa="'.$aa.'" data-pp="'.$pp.'" type="hidden" value="'.$eidos['paymethod_total'].'">';
                  echo '<span>';
                  if ($eidos['paymethod_total']!=0) echo myCurrencyFormat($eidos['paymethod_total'],false);
                  echo '</span>';
                echo '</div>';
              } else {?>
              <input type="number" class="gks_price div_payment_type_multi_item_input form-control form-control-sm" 
              data-aa="<?php echo $aa;?>" 
              data-pp="<?php echo $pp;?>"
              value="<?php if ($eidos['paymethod_total']!=0) echo number_format($eidos['paymethod_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" 
              style="text-align:right;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
              placeholder="<?php echo gks_lang('Ποσό');?>"
              >
              <?php } ?>
            </div>

            
            
            <div class="<?php echo $gkscols8;?>">
              <div class="text-center gks_icons">
                <?php if ($gks_lock==false and $perm_gks_acc_pay_edit) {?>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_handle"></i>
                </div>

                <?php if ($payments_lock_level<4) {?>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_add_eidos"  data-aa="<?php echo $aa;?>"></i>
                </div>
                <?php } ?>
                                
                <?php if (!($payments_lock_level>=4 or $has_transaction_id)) {?>
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_delete_eidos" data-aa="<?php echo $aa;?>" style=""></i>
                </div>
                <?php } ?>
                
                <?php } ?>
              </div>
            </div>   
           
          </div> 
          
          
          
          

          

<?php } ?>


          <div class="row" id="eidi_footer1">
            <div class="col-sm-4">
              <div class="form-group row total_row">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Γραμμές');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_products_count"  style="padding-right: 6px;font-size: 0.8rem;font-weight1: bold;padding-top: 8px;"><?php echo $products_count;?></div>
                </div>
              </div> 

              
            </div>
            <div class="col-sm-4">

             
            </div>

            <div class="col-sm-4">



              
              
              

              
              <div class="form-group row total_row" id="tr_gks_total_price_total">
                <div class="col-8  col-sm-6  col-md-4  col-lg-5 table-dark1 gks_eidos_label text-right">
                  <?php echo gks_lang('Σύνολο');?>:
                </div>
                <div class="col-4 col-sm-6  col-md-5  col-lg-4">
                  <div class="text-right" id="gks_total_price_total"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;" data-val="<?php 
                    echo number_format($row['gks_price_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>"><?php echo myCurrencyFormat($row['gks_price_total']);?></div>
                </div>
              </div>              


              
            </div>

          </div>
        
          <?php if ($credit_memo_for_acc_pay_id!=0) { ?>  
          <div class="row" style="margin-top: 10px;padding-top: 10px;border-top: 1px solid lightgray;">
            <div class="col-sm-12 gks_eidos_label">
              <?php echo gks_lang('Το συσχετιζόμενο παραστατικό στο οποίο αναφέρεται το τρέχον παραστατικό έχει αξία');?> <b><?php echo myCurrencyFormat($org_gks_price_total);?></b>
              
            </div>
            <div class="col-sm-12 gks_eidos_label">
              <?php echo gks_lang('Όλα τα συσχετιζόμενα πιστωτικά παραστατικά, εκτός από το τρέχον, είναι');?> <b><?php echo $others_count;?></b> και έχουν άθροισμα αξίας <b><?php echo myCurrencyFormat($others_gks_price_total_sum);?></b>
              
            </div>
            <div class="col-sm-12 gks_eidos_label">
              <?php echo gks_lang('Άρα το τρέχον παραστατικό θα μπορεί να έχει ως μέγιστη αξία');?> 
              <b><span id="rest_gks_price_total_sum" data-val="<?php echo number_format($rest_gks_price_total_sum,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding: 0px 6px;border-radius: 10px;"><?php echo myCurrencyFormat($rest_gks_price_total_sum);?></span></b>
              
            </div>
            
          </div>
          <?php } ?>        
                    

                              
        </div>
      </div>
    </div>
  </div>
</div>










<div class="container-fluid " style="padding-top:0px">
  <div class="row">
    <div class="col-md-12">

      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Η πληρωμή αφορά');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('relinv');?>> 
          <div id="payment_is_for_invs">
            <?php if ($user_id<=0) {
              echo '<div style="text-align:center;" class="alert alert-warning">'.gks_lang('Επιλέξτε κάποια επαφή (πελάτη/προμηθευτή) και εδώ θα εμφανιστούν τα μη εξοφλημένα παραστατικά').'</div>';
            } else {
              
              echo gks_get_user_payment_is_for_invs($user_id,$pay_poso,$id,$row['gks_price_total'],$pay_state,$pay_acc_journal_id);
            }?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>





<div class="container-fluid " style="padding-top:0px">
  <div class="row">
    <div class="col-md-6">

      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σημειώσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('notes');?>> 

          <div class="form-group row">
            <label for="note_doc" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλια εγγράφου');?>:</label>
            <div class="col-md-8">
              <textarea id="note_doc" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_acc_pay_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_doc']);?></textarea>
            </div>
          </div> 


          <div class="form-group row">
            <label for="note_logistirio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερική σημείωση για λογιστήριο');?>:</label>
            <div class="col-md-8">
              <textarea id="note_logistirio" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_acc_pay_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_logistirio']);?></textarea>
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
                  $balance_user_before=gks_balance_calc(['id' => $user_id, 'except_id_acc_pay' => $id]);
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
                  $balance_user_after=gks_balance_calc(['id' => $user_id]);
                  echo myCurrencyFormat($balance_user_after);
                  ?>
                </div>
              </div>
            </div>
            
            <div class="form-group row" id="div_affect_balance">
              <label for="affect_balance" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επηρεάζει το υπόλοιπο της επαφής');?>:</label>
              <div class="col-md-8">
                <input id="affect_balance" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['affect_balance']!=0) echo ' checked ';?> <?php if (!$perm_gks_acc_pay_edit) echo 'disabled';?>>
                <?php if (!($pay_state=='080listing' or $pay_state=='090ekdosi')) {?>
                <small class="form-text text-muted"><?php echo gks_lang('Θα εφαρμοστεί η ρύθμιση όταν η κατάσταση της πληρωμής θα είναι μία από τις παρακάτω');?>:<br>
                  <span style="line-height: 1.8;">
                  <span class="acc_pay_state_080listing"><?php echo getAccPayStateDescr('080listing');?></span>
                  <span class="acc_pay_state_090ekdosi"><?php echo getAccPayStateDescr('090ekdosi');?></span>
                  </span>
                </small>
                <?php } ?>
              </div>
            </div> 
  
            
            
            <div class="form-group row" id="div_affect_balance_all_poso" style="<?php if ($row['affect_balance']==0) echo 'display:none;';?>">
            <label for="affect_balance_all_poso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ολόκληρο το ποσό');?>:</label>
            <div class="col-md-8">
              <input id="affect_balance_all_poso" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['affect_balance_all_poso']!=0) echo ' checked ';?> <?php if (!$perm_gks_acc_pay_edit) echo 'disabled';?>>
              <small class="form-text text-muted" id="small_affect_balance_all_poso" style="<?php if (!($row['affect_balance']==0 or $row['affect_balance_all_poso']!=0)) echo 'display:none;';?>">
                <input type="radio" name="affect_balance_all_poso_type" value="price_total" id="affect_balance_all_poso_type_price_total" <?php
                if ($row['affect_balance_all_poso_type']=='price_total') echo ' checked';?>>
                  <label for="affect_balance_all_poso_type_price_total" style="margin-bottom: 0px;"><?php echo gks_lang('Σύνολο');?> (<span
                    id="bal_gks_total_price_total" data-val="<?php echo number_format($row['gks_price_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                    ?>"><?php echo myCurrencyFormat($row['gks_price_total']);?></span>)</label><br>



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
              <?php if (!$perm_gks_acc_pay_edit) echo 'disabled';?> >            
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
$GKS_ACC_PAY_STATUS_BUTTONS=array(
  '010draft' =>           array('cmdupdate','cmddelete','cmdprint',           '050proinvoice','080listing','090ekdosi',),
  '040cancelled' =>       array('cmdupdate',            'cmdprint','010draft',),
  '080listing' =>         array('cmdupdate',            'cmdprint','010draft','040cancelled',),
  '090ekdosi' =>          array('cmdupdate',            'cmdprint','010draft','040cancelled',),
);

if (isset($GKS_ACC_PAY_STATUS_BUTTONS[$pay_state])) {
  if ($perm_gks_acc_pay_edit) {

    
    if (in_array('cmdupdate',$GKS_ACC_PAY_STATUS_BUTTONS[$pay_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="submit_button_ok_custom">'.gks_lang('Αποθήκευση').'</button> ';
    if (in_array('cmddelete',$GKS_ACC_PAY_STATUS_BUTTONS[$pay_state]) and $id>0) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-danger thisdeleterowbtn" data-id="'.($row['id_acc_pay']>0 ? $row['id_acc_pay'] : '').'" data-model="gks_acc_pay" data-backurl="admin-acc-pay.php">'.gks_lang('Διαγραφή').'</button> ';
    if (in_array('010draft',$GKS_ACC_PAY_STATUS_BUTTONS[$pay_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_pay_state_010draft" id="submit_button_010draft">'.gks_lang('Επαναφορά σε Πρόχειρο').'</button> ';
    if (in_array('040cancelled',$GKS_ACC_PAY_STATUS_BUTTONS[$pay_state])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_pay_state_040cancelled" id="submit_button_040cancelled">'.gks_lang('Ακύρωση').'</button> ';
    if (in_array('080listing',$GKS_ACC_PAY_STATUS_BUTTONS[$pay_state])) 
      echo '<button type="button" style="margin-bottom:10px;'.($row['is_xeirografi']==0 ? 'display:none;' : '').'" class="btn button_acc_pay_state_080listing" id="submit_button_080listing">'.getAccPayStateDescr('080listing').'</button> ';
    if (in_array('090ekdosi',$GKS_ACC_PAY_STATUS_BUTTONS[$pay_state])) 
      echo '<button type="button" style="margin-bottom:10px;'.($row['is_xeirografi']!=0 ? 'display:none;' : '').'" class="btn button_acc_pay_state_090ekdosi" id="submit_button_090ekdosi">'.getAccPayStateDescr('090ekdosi').'</button> ';
    if (in_array($acc_eidos_parastatikou_id_org,[702,703,704])==false) { /*akurotiko */
    if (($pay_state=='090ekdosi') and $row['is_xeirografi']==0 and ($acc_eidos_parastatikou_id_org!=51 and $acc_eidos_parastatikou_id_org!=52)) { 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_pay_state_credit_memo tooltipster" id="submit_button_credit_memo" title="'.gks_lang('Δημιουργία Επιστροφή είσπραξης/πληρωμής σε πελάτες/προμηθευτές').'">'.gks_lang('Επιστροφή').'</button> ';
    }}

    if (($row['from_aade_import']=='' and ($pay_state=='080listing' or $pay_state=='090ekdosi' or $pay_state=='100payment')) and $row['send_mydata']==1) { 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_pay_state_aade_send tooltipster" id="submit_button_aade_send" title="'.gks_lang('Αποστολή myData στην ΑΑΔΕ').'">'.gks_lang('ΑΑΔΕ').'</button> ';
    }
    if (($row['from_aade_import']=='' and ($pay_state=='080listing' or $pay_state=='090ekdosi' or $pay_state=='100payment')) and $row['send_paroxos']==1) { 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_acc_pay_state_paroxos_send tooltipster" id="submit_button_paroxos_send" title="'.gks_lang('Αποστολή στον πάροχο').'">'.gks_lang('Πάροχος').'</button> ';
    }
        
    
  }
  
  if (in_array('cmdprint',$GKS_ACC_PAY_STATUS_BUTTONS[$pay_state])) {
    echo '<button type="button" style="margin-bottom:10px;" class="btn btn-dark" id="submit_button_print">'.gks_lang('Εκτύπωση').' <i class="fas fa-print" style="color: #35dc35;font-size: 120%;"></i></button> ';
  }

  if ($id>0 and $perm_gks_acc_pay_add) {
    echo '<a href="admin-acc-pay-item.php?id=-1&template_id='.$id.'" style="margin-bottom:10px;" '.
      'class="btn btn-primary tooltipster" '.
      'id="submit_button_template" '.
      'title="<div style=\'text-align: center;\'>'.gks_lang('Δημιουργία αντιγράφου').'<br>'.gks_lang('ή').'<br>'.
              '<button class=\'btn btn-primary btn-sm\' style=\'margin-top:6px;\' '.
              'onclick=\'submit_button_template_create(2);\' '.
              'id=\'submit_button_template_create\' data-obj=\'gks_acc_pay\''.
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



      <?php 
      echo getObjectRels('gks_acc_pay',$id);   
      echo getActivityObjectTable('gks_acc_pay',$id);
      ?>
      
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
            $sql_msg="SELECT gks_acc_pay_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_acc_pay_messages LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_acc_pay_messages.acc_pay_id=".$id."
            ORDER BY gks_acc_pay_messages.mydate_add DESC, gks_acc_pay_messages.id_acc_pay_message DESC;";
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg['id_acc_pay_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_msg['acc_pay_message']);
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

          
          
          $query = "SELECT gks_acc_pay_links.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_acc_pay_links LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay_links.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_acc_pay_links.acc_pay_id in (".$id.")
          ORDER BY gks_acc_pay_links.mydate, gks_acc_pay_links.id_acc_pay_links;";
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
            <tr id="tr_links_url_<?php echo $row_list['id_acc_pay_links'];?>">
              <th scope="row" nowrap align="right" class="links_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_list['id_acc_pay_links'];?>" data-deleteafter="gks_fnc_links_delete_after|<?php echo $row_list['id_acc_pay_links'];?>" data-model="gks_acc_pay_links" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>

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
                <div class="progress download-perc" data-id="<?php echo $row_list['id_acc_pay_links'];?>" 
                  style="<?php echo ($row_list['download_status']==1 ? '' : 'display:none;');?>">
                  <div class="download-perc-bar progress-bar progress-bar-striped" 
                    data-id="<?php echo $row_list['id_acc_pay_links'];?>" role="progressbar" 
                    style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>    
                <div class="download-message" 
                  data-id="<?php echo $row_list['id_acc_pay_links'];?>" 
                  style="<?php echo ($row_list['download_status']==3 ? '' : 'display:none;');?>"
                  ><?php echo $row_list['download_message'];?></div>
                
              </td>
              <td nowrap class="download_size_until_now" data-id="<?php echo $row_list['id_acc_pay_links'];?>" style="text-align:right;vertical-align:middle;"><?php if ($row_list['download_size_until_now']>0) echo number_format($row_list['download_size_until_now']/1024/1024,2,$GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB';?></td>  
              <td nowrap class="download_file_td" data-id="<?php echo $row_list['id_acc_pay_links'];?>" style="text-align:center;vertical-align: middle;"><?php
              
              
              // 0 notdownload
              // 1 downloding
              // 2 complete
              // 3 abort
              
              if ($row_list['download_status']==0) { //notdownload
                echo '<i class="fas fa-file-download download_action_start" data-id="'.$row_list['id_acc_pay_links'].'" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==1) { //downloding
                $need_download_timer=1;
                echo '<i class="fas fa-stop-circle download_action_stop" data-id="'.$row_list['id_acc_pay_links'].'" style="font-size:200%;vertical-align:middle;color:red;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==2) { //complete
                echo '<i class="fas fa-check-circle download_action_complete" data-id="'.$row_list['id_acc_pay_links'].'" style="font-size:200%;vertical-align:middle;color:green;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==3) { //abort
                echo '<i class="fas fa-undo download_action_reset" data-id="'.$row_list['id_acc_pay_links'].'" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>';
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
			$obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_acc_pay','id'=>$id));
      echo $obj_fileslist['html'];
      ?>

        


    </div>

    
    
    <div class="col-xl-6">


      <?php 
      
      if (trim_gks($row['print_date'])!='' or 
          trim_gks($row['print_file_name']) != '' or 
          trim_gks($row['print_file_url']) != '' or 
          $row['print_user_id']>0 or 
          trim_gks($row['print_pay_state']) != '') {?>
      
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
            <div class="col-sm-8"><span class="acc_pay_state_<?php echo $row['print_pay_state'];?>"><?php echo getAccPayStateDescr($row['print_pay_state']);?></span></div>
          </div>

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αρχείο');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (trim_gks($row['print_file_name'])!='') {
                $local_file=GKS_FileServerShare.'acc/pay/'.$id.'/print/'.$row['print_file_name'];
                if (file_exists($local_file)) {
                  //print_file_url
                  $url_file='admin-get-file.php?fs=fileservers&file=acc%2Fpay%2F'.$id.'%2Fprint%2F'.urlencode($row['print_file_name']);
                  echo '<a href="'.$url_file.'" target="_blank">'.$row['print_file_name'].'</a> ';
                  echo '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
                }
              }
              ?></span></div>
          </div>

        </div>      
      </div>              
      <?php } ?>
      <?php if ($perm_gks_acc_pay_edit) {?>
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
              if ($aade_sending['aade']==1) echo 'myData';
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
                $local_file=GKS_FileServerShare.'acc/pay/'.$id.'/aade_mydata/'.$row['aade_xml_send'];
                if (file_exists($local_file)) {
                  
                  $url_file='admin-get-file.php?fs=fileservers&file=acc%2Fpay%2F'.$id.'%2Faade_mydata%2F'.urlencode($row['aade_xml_send']);
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
                $local_file=GKS_FileServerShare.'acc/pay/'.$id.'/aade_mydata/'.$row['aade_xml_response'];
                if (file_exists($local_file)) {
                  
                  $url_file='admin-get-file.php?fs=fileservers&file=acc%2Fpay%2F'.$id.'%2Faade_mydata%2F'.urlencode($row['aade_xml_response']);
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
          $sql_log="SELECT gks_acc_pay_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_acc_pay_log LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_acc_pay_log.acc_pay_id=".$id."
          ORDER BY gks_acc_pay_log.id_acc_pay_log DESC;";
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_acc_pay']>0) echo $row['id_acc_pay'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">GUID:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['pay_guid'];?></span></div>
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
  if (isset($gks_user_settings['print']['form_id_pay'])) $user_def_form_id=intval($gks_user_settings['print']['form_id_pay']);
  
  $sql_print_forms="SELECT gks_print_forms.*, gks_lang.lang_name
  FROM ((gks_print_objects 
  LEFT JOIN gks_print_objects_forms ON gks_print_objects.id_print_object = gks_print_objects_forms.print_object_id) 
  LEFT JOIN gks_print_forms ON gks_print_objects_forms.print_form_id = gks_print_forms.id_print_form)
  LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  WHERE gks_print_forms.id_print_form is not null and gks_print_forms.is_disable=0 AND gks_print_objects.object_name='gks_acc_pay'
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
      ($curr_pay_state=='090ekdosi' or $curr_pay_state=='100payment')) {

    $html_paroxos_pdf=
    '<div class="col-sm-6 form-control-sm text-sm-left">
       <input id="gks_paroxos_send_pdf" type="checkbox" class="form-control form-control-sm" value="1" checked>
       <label for="gks_paroxos_send_pdf" style="margin: 0px;position: relative;top: 2px;font-size: 0.8rem;"> '.gks_lang('Αποστολή στον πάροχο').'</label>
       <i class="fas fa-info-circle tooltipster" title="'.gks_lang('Αποδοχή μόνο αρχείων pdf').'" style="font-size: 150%;position: relative;top: 4px;"></i>
     </div>';
  }
  
  $erp_app_id=0;
  if ($pay_acc_seira_id>0) {
    $sql_send_erp_app="SELECT gks_acc_seires.id_acc_seira, gks_acc_seires.erp_app_id, gks_acc_seires.erp_app_dest,  
    gks_acc_seires.erp_app_dest_printer, 
    gks_acc_seires.erp_app_dest_printer_method,
    gks_acc_seires.erp_app_dest_printer_lpr_ip,
    gks_acc_seires.erp_app_dest_printer_copies, 
    gks_acc_seires.erp_app_dest_folder, 
    gks_erp_app.id_erp_app, gks_erp_app.erp_app_name, gks_erp_app.erp_app_last_ping
    FROM gks_acc_seires 
    LEFT JOIN gks_erp_app ON gks_acc_seires.erp_app_id = gks_erp_app.id_erp_app
    where gks_acc_seires.id_acc_seira=".$pay_acc_seira_id;
    
    $result_send_erp_app = $db_link->query($sql_send_erp_app);        
    if (!$result_send_erp_app) {debug_mail(false,'error sql',$sql_send_erp_app);die('sql error');}
    if ($result_send_erp_app->num_rows==1) {
      $row_send_erp_app = $result_send_erp_app->fetch_assoc();
      $erp_app_id=$row_send_erp_app['erp_app_id'];
      

      $send_erp_app_tooltip='';
      $send_erp_app_tooltip.='gks ERP App: '.trim_gks($row_send_erp_app['erp_app_name']).'<br>';
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
   
<?php include_once('admin-eftpos-transaction-dialog.php');

$sql_ppm="SELECT viva_preferred_payment_methods, mellon_preferred_payment_methods, 
cardlink_preferred_payment_methods, epay_preferred_payment_methods, 
worldline_preferred_payment_methods, nexi_preferred_payment_methods
FROM gks_acc_pay LEFT JOIN gks_company ON gks_acc_pay.company_id = gks_company.id_company
WHERE gks_acc_pay.id_acc_pay=".$id." AND gks_company.id_company Is Not Null";
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


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_dialog_object_rel_curr='gks_acc_pay';
var from_php_activity_model='gks_acc_pay';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');



var from_php_id=<?php echo $id;?>;
var from_php_template_id=<?php echo $template_id;?>;
var from_php_gks_lock=<?php echo ($gks_lock ? 'true' : 'false');?>;
var from_php_number_gks_lock=<?php echo ($gks_number_lock ? 'true' : 'false');?>;
var from_php_user_gks_lock=<?php echo ($gks_user_lock ? 'true' : 'false');?>;


var last_aa=<?php echo $aa;?>;
var last_mcmaa=<?php echo $mcm_aa;?>;

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








var fields_change=[];
<?php 
foreach ($fields_change as $field_aa => $field_name) {

  echo "fields_change[".$field_aa."]='".$field_name."';";
} ?>
//console.log(fields_change);


var from_php_pay_state='<?php echo $pay_state;?>';
var from_php_acc_eidos_parastatikou_id=<?php echo $acc_eidos_parastatikou_id;?>;
var from_php_eidos_parastatikou_type_id=<?php echo $eidos_parastatikou_type_id;?>;
var from_php_eidos_parastatikou_need_prev=<?php echo $eidos_parastatikou_need_prev;?>;
var from_php_eidos_parastatikou_balance_pros=<?php echo $eidos_parastatikou_balance_pros;?>;
var from_php_acc_eidos_parastatikou_other_entity=<?php echo $acc_eidos_parastatikou_other_entity;?>;
var from_php_journal_has_correlated_invoices=<?php echo $journal_has_correlated_invoices;?>;
var from_php_journal_has_multiple_connected_marks=<?php echo $journal_has_multiple_connected_marks;?>;


var from_php_print_def_file_type='<?php echo (isset($gks_user_settings['print']['file_type']) ? $gks_user_settings['print']['file_type'] : 'pdf');?>';
var from_php_print_def_grayscale=<?php echo (isset($gks_user_settings['print']['grayscale']) ? $gks_user_settings['print']['grayscale'] : 'false');?>;
var from_php_print_def_landscape=<?php echo (isset($gks_user_settings['print']['landscape']) ? $gks_user_settings['print']['landscape'] : 'false');?>;
var from_php_print_def_zoom=<?php echo (isset($gks_user_settings['print']['zoom']) ? $gks_user_settings['print']['zoom'] : '100');?>;;
var from_php_print_def_form_id=<?php echo (isset($gks_user_settings['print']['form_id_pay']) ? $gks_user_settings['print']['form_id_pay'] : '0');?>;;
var from_php_print_def_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_user_settings['print']['forms_pay']));?>'));

var from_php_is_credit_memo=<?php echo ($credit_memo_for_acc_pay_id ==0 ? 'false' : 'true');?>;


var from_php_enter_order=[];
<?php
if (isset($gks_user_settings['gks_acc_pay']['enter_order']) and is_array($gks_user_settings['gks_acc_pay']['enter_order'])) {
  foreach ($gks_user_settings['gks_acc_pay']['enter_order'] as $value) {
    echo 'from_php_enter_order.push(\''.$value.'\');'."\n";
  } 
}
?>
var from_php_paymethods_array=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($paymethods_array));?>'));

var from_php_dialog_item_message_email_from_array=[];
<?php 
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_pay','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_pay','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_pay','delete',$id);?>;

var from_php_perm_print_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($perm_print_forms));?>'));
var from_php_check_vies_valid_wait=<?php echo ($mybasketarray['check_vies']['valid']==2 ? 'true' : 'false');?>;
var from_php_payments_lock_level=<?php echo $payments_lock_level;?>;
var from_php_preferred_payment_methods = JSON.parse('<?php echo json_encode($preferred_payment_methods);?>');

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});



</script>  

<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>admin-acc-pay-item.js"></script>

<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-acc-pay-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-eftpos-transaction-dialog.js?v=<?php echo $gks_cache_version;?>"></script>



<?php

echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


