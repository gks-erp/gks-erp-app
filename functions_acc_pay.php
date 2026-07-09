<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function getAccPayStateDescr($mystate,$load_lang='') {
//  global $gks_user_settings;
//  if ($load_lang=='') {
//    $load_lang='el-GR';
//    if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
//  }
//  
//  if ($load_lang=='el-GR') {  
    switch ($mystate) {
      case '010draft': return gks_lang('Πρόχειρο','part4','getAccPayStateDescr'); break; 
      case '040cancelled': return gks_lang('Ακυρωμένο','part4','getAccPayStateDescr'); break; 
      case '080listing': return gks_lang('Καταχώρηση','part4','getAccPayStateDescr'); break; 
      case '090ekdosi': return gks_lang('Έκδοση','part4','getAccPayStateDescr'); break; 
      default: return $mystate; break; 
    } 
//  } else {
//    switch ($mystate) {
//      case '010draft': return 'Draft'; break; 
//      case '040cancelled': return 'Cancelled'; break; 
//      case '080listing': return 'Listing'; break; 
//      case '090ekdosi': return 'Issue'; break; 
//      default: return $mystate; break; 
//    }
//  }
}

function select_gks_acc_pay() {

$sql="SELECT gks_acc_pay.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
".GKS_WP_TABLE_PREFIX."users_aade.gks_nickname AS gks_nickname_aade,
".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,
gks_acc_seires.is_xeirografi,gks_acc_seires.send_mydata,gks_acc_seires.send_paroxos,
gks_acc_journal.acc_eidos_parastatikou_id,
gks_acc_journal.acc_eidos_parastatikou_other_entity,
gks_acc_journal.journal_has_correlated_invoices,
gks_acc_journal.journal_has_multiple_connected_marks,
gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, antisimvalomenos_label, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,

eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_need_prev, eidos_parastatikou_has_fpa,eidos_parastatikou_has_othertaxes, 
eidos_parastatikou_has_esoda, eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,eidos_parastatikou_balance_pros,
gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
gks_users.ma_odos,gks_users.ma_arithmos,gks_users.ma_orofos,gks_users.ma_perioxi,gks_users.ma_poli,gks_users.ma_tk,
gks_users.ma_country_id,gks_users.ma_nomos_id,
gks_country.country_name,gks_nomoi.nomos_descr,
table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name,
".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile as user_mobile,
gks_lang.lang_name, ".GKS_WP_TABLE_PREFIX."users.gks_lang as user_lang,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_aade_paroxos.paroxos_name

FROM (((((((((((((((((((((gks_acc_pay

LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_pay.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_pay.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_aade ON gks_acc_pay.aade_user_id = ".GKS_WP_TABLE_PREFIX."users_aade.ID) 
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
LEFT JOIN gks_ads_campain ON gks_acc_pay.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_aade_paroxos ON gks_acc_pay.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos


";
//echo '222222222';
return $sql;
  
}

function get_acc_pay_details_txt($id, &$myarray=array(),&$myarray_line=array()) {
  global $db_link;
  $myarray=array();
    
  $sql="SELECT gks_acc_pay_method.*, gks_payment_acquirers.payment_acquirer_name
  FROM gks_acc_pay_method 
  LEFT JOIN gks_payment_acquirers ON gks_acc_pay_method.paymethod_id = gks_payment_acquirers.id_payment_acquirer
  WHERE gks_acc_pay_method.acc_pay_id=".$id."
  ORDER BY gks_acc_pay_method.id_acc_pay_method;";

  //gks_orders_products.product_set
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);return 'sql error';}
  while ($eidos = $result->fetch_assoc()) {
    $myarray[] = array($eidos['payment_acquirer_name'], $eidos['paymethod_total']);
  }
  
  $ret='';
  $myarray_line=array();
  foreach ($myarray as $value) {
    $ret.=$value[0].': '.myCurrencyFormat($value[1]).'<br>';
    $myarray_line[]=trim_gks($value[0].': '.myCurrencyFormat($value[1]));
  } 
  
  if ($ret!='') $ret=substr($ret, 0, strlen($ret)-4);

  return $ret;
}
function gks_acc_pay_credit_memo_create($old_id, $check_only) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  

  
  $sql="SELECT gks_acc_pay.*,gks_acc_eidi_parastatikon.credit_acc_eidos_parastatikou_id,
  gks_acc_eidi_parastatikon_creadit.eidos_parastatikou_descr as credit_descr
  FROM ((gks_acc_pay 
  LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon AS gks_acc_eidi_parastatikon_creadit ON gks_acc_eidi_parastatikon.credit_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon_creadit.id_acc_eidos_parastatikou
  where id_acc_pay=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'old_id pay noy found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το παραστατικό που θα αφορά το πιστωτικό παραστατικό')));
    echo json_encode($return); die();}
  
  $old_row = $result->fetch_assoc();

    
  $company_id=intval($old_row['company_id']);
  $company_sub_id=intval($old_row['company_sub_id']);
  $credit_acc_eidos_parastatikou_id=intval($old_row['credit_acc_eidos_parastatikou_id']);
  $credit_descr=trim_gks($old_row['credit_descr']);
  
  if ($credit_acc_eidos_parastatikou_id<=0) {
    debug_mail(false,'credit_acc_eidos_parastatikou_id zero',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να εκτελεστεί αυτή η εντολή')));
    echo json_encode($return); die();}
    
  
  $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira,gks_acc_seires.seira_code
  FROM ((gks_acc_journal 
  LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  WHERE eidos_parastatikou_type_id in (11,12) and id_acc_eidos_parastatikou not in (702,703,704)
  and gks_acc_journal.id_acc_journal>0
  and gks_acc_journal.is_disable=0
  and gks_acc_seires.id_acc_seira>0
  and gks_acc_seires.is_xeirografi=0
  and gks_acc_seires.is_disable=0
  and id_acc_eidos_parastatikou=".$credit_acc_eidos_parastatikou_id."
  AND gks_acc_journal.company_id=".$company_id." 
  AND gks_acc_journal.company_sub_id=".$company_sub_id;
  //echo '<pre>';echo $sql;die();
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'credit_descr not found','',$sql);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$credit_descr,gks_lang('Δεν βρέθηκε πιστωτικό συσχετιζόμενο ημερολόγιο με τύπο παραστατικού <b>[1]</b> και αντίστοιχη μηχανογραφημένη σειρά για αυτήν την εταιρεία/υποκατάστημα'))));
    echo json_encode($return); die();}
  $row=$result->fetch_assoc();
  $new_pay_acc_journal_id=$row['id_acc_journal'];
  $new_pay_acc_seira_id=$row['id_acc_seira'];
  $new_pay_acc_seira_code=trim_gks($row['seira_code']);
  //echo $new_pay_acc_journal_id.'|'.$new_pay_acc_seira_id.'|'.$new_pay_acc_seira_code."\n";
  
  
  if ($check_only) return true;
  
  $new_pay_guid=guid_for_acc_pay();
  //echo $new_pay_guid."\n"; die();
  
  $sql="INSERT INTO gks_acc_pay (pay_guid, pay_date, mydate_add, mydate_edit, 
  user_id_add, user_id_edit, myip, 
  pay_acc_journal_id, pay_acc_seira_id, 
  pay_acc_seira_code, pay_state, 
  credit_memo_for_acc_pay_id,
  company_id, company_sub_id, user_id,
  user_notes, idiotites,
  gks_price_total,
  affect_balance,affect_balance_all_poso,affect_balance_all_poso_type,affect_balance_pros
  )
  SELECT '".$new_pay_guid."' as pay_guid, now() as pay_date, now() as mydate_add, now() as mydate_edit,
  ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
  ".$new_pay_acc_journal_id." as pay_acc_journal_id, ".$new_pay_acc_seira_id." as pay_acc_seira_id, 
  '".$db_link->escape_string($new_pay_acc_seira_code)."' as pay_acc_seira_code, '010draft' as pay_state, 
  ".$old_id." as credit_memo_for_acc_pay_id,
  company_id, company_sub_id, user_id,
  user_notes, idiotites,
  gks_price_total,
  affect_balance,affect_balance_all_poso,affect_balance_all_poso_type,-affect_balance_pros as affect_balance_pros_meion
  FROM gks_acc_pay
  WHERE id_acc_pay=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $new_id = $db_link->insert_id;  
  //echo $new_id."\n";die();
  
  $sql="select id_acc_pay_method 
  from gks_acc_pay_method 
  where acc_pay_id=".$old_id." 
  order by id_acc_pay_method";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $old_product_ids=array();
  while ($row = $result->fetch_assoc()) {  
    $old_product_ids[]=$row['id_acc_pay_method'];
  }
  
  $map_products=array();
  foreach ($old_product_ids as $old_product_id) {
    $sql="INSERT INTO gks_acc_pay_method ( 
    mydate_add, mydate_edit, user_id_add, user_id_edit, myip, 
    acc_pay_id, 
    paymethod_aa,paymethod_id,paymethod_descr,paymethod_total,paymethod_comments
    )
    SELECT now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,
    '".$db_link->escape_string($gkIP)."' as myip,
    ".$new_id." as acc_pay_id,
    paymethod_aa,paymethod_id,paymethod_descr,paymethod_total,paymethod_comments
    FROM gks_acc_pay_method
    where id_acc_pay_method=".$old_product_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    
    $new_product_id = $db_link->insert_id;  
    $map_products[]=array('old' => $old_product_id, 'new' => $new_product_id);
    
    $sql="INSERT INTO gks_acc_pay_payment ( 
    mydate_add, mydate_edit, user_id_add, user_id_edit, myip, 
    acc_pay_id,acc_pay_method_id,pp,
    payment_acquirer_id,
    poso,asset_id    
    )
    SELECT now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,
    '".$db_link->escape_string($gkIP)."' as myip,
    acc_pay_id,id_acc_pay_method,paymethod_aa,
    paymethod_id,paymethod_total,0 as asset_id
    FROM gks_acc_pay_method
    where acc_pay_id=".$new_id."
    and id_acc_pay_method=".$new_product_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    
    $new_id_acc_pay_payment = $db_link->insert_id; 
    
    $sql="update gks_acc_pay_payment 
    set asset_id=(
      select asset_id 
      from gks_acc_pay_payment 
      where acc_pay_method_id=".$old_product_id."
      order by transaction_id desc 
      limit 1
    )
    where id_acc_pay_payment=".$new_id_acc_pay_payment;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    
    
    
    
  }
  //echo print_r($map_products,true)."\n";
  

  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_acc_pay_log (acc_pay_id, add_date,user_id,sxolio) values (
  ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
    
  return $new_id;
  
}

function gks_get_user_payment_is_for_invs($user_id,$pay_poso=array(),$id_acc_pay=0,$gks_price_total=0,$pay_state='010draft',$pay_acc_journal_id=0) {
  global $db_link;
  global $GKS_INPUT_STEP_AJIA;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  
  if (is_array($pay_poso)==false) $pay_poso=array();
  
  if ($pay_state=='040cancelled') {
    return '<div style="text-align:center;" class="alert alert-warning">'.gks_lang('Η πληρωμή έχει ακυρωθεί').'</div>';
  }
  $pay_lock_recs=false;
  if ($pay_state=='090ekdosi') $pay_lock_recs=true;
  
  
  
  if ($pay_lock_recs==false) { 
    $sql_user="SELECT ".GKS_WP_TABLE_PREFIX."users.*
    from ".GKS_WP_TABLE_PREFIX."users
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID=".$user_id." limit 1";
    $result_user = $db_link->query($sql_user);        
    if (!$result_user) {
      debug_mail(false,'error sql',$sql_user);
      return '<div style="text-align:center;" class="alert alert-warning">error sql</div>';
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    if ($result_user->num_rows!=1) {
      debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      return '<div style="text-align:center;" class="alert alert-warning">'.gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα').'</div>';
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();  
    }
    
    $row_user = $result_user->fetch_assoc();
  } else {//einai se ekdosi to pay, ara na parv mono ta sugkekrimena tou
    $pay_poso=array();
    
  }
  //echo 'gggggg';die();
  
  //return $pay_acc_journal_id;
  $acc_eidos_parastatikou_ids=0;
  $eidos_parastatikou_type_id=0;
  if ($pay_acc_journal_id!=0) {
    $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_journal.acc_eidos_parastatikou_id, gks_acc_eidi_parastatikon.eidos_parastatikou_type_id
    FROM gks_acc_journal 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    where gks_acc_journal.id_acc_journal=".$pay_acc_journal_id;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return '<div style="text-align:center;" class="alert alert-warning">error sql</div>';
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $acc_eidos_parastatikou_id=$row['acc_eidos_parastatikou_id'];
      $eidos_parastatikou_type_id=$row['eidos_parastatikou_type_id'];
    }
  }
  
  
  $other_eidos_parastatikou_type_id=0;
  if ($eidos_parastatikou_type_id==11) {         //eisprajeis
    $other_eidos_parastatikou_type_id=1;         //polisi
  } else if ($eidos_parastatikou_type_id==12) {  //pliromi
    $other_eidos_parastatikou_type_id=2;         //agora
  }
  //return $eidos_parastatikou_type_id.'|'.$other_eidos_parastatikou_type_id;
  
  $sql_field_notes_subnotes_inv='';
  $sql_field_notes_subnotes_order='';
  $sql_field_notes_subnotes_reservation='';
  gks_plugins_functions_run('functions_acc_pay_gks_get_user_payment_is_for_invs_field_notes_subnotes',array(
    'sql_field_notes_subnotes_inv' => &$sql_field_notes_subnotes_inv,
    'sql_field_notes_subnotes_order' => &$sql_field_notes_subnotes_order,
    'sql_field_notes_subnotes_reservation' => &$sql_field_notes_subnotes_reservation,
  ));
  
  
  $sql="SELECT 'inv' as myfrom, gks_acc_inv.id_acc_inv, gks_acc_inv.inv_date, gks_acc_inv.inv_state,
  gks_acc_inv.inv_acc_journal_id, gks_acc_journal.acc_journal_code, gks_acc_journal.acc_journal_descr, 
  gks_acc_inv.inv_acc_seira_id, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_inv.inv_acc_number_int,
  gks_acc_inv.gks_price_net, gks_acc_inv.gks_price_total,gks_acc_inv.products_posotita,

  gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
  ".$sql_field_notes_subnotes_inv."
  note_doc,note_logistirio,
  CASE
    WHEN (inv_state='080listing' or inv_state='090ekdosi' or inv_state='100payment') and affect_balance=1
      THEN affect_balance_pros * affect_balance_poso
    ELSE 0
  END as affect_balance_calc,
  exist_recs.sum_poso,
  eidos_parastatikou_type_id
  FROM (((((gks_acc_inv
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_company ON gks_acc_inv.company_id = gks_company.id_company)
  LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN (
    select acc_inv_id,sum(poso) as sum_poso from gks_acc_pay_poso_acc_inv where acc_pay_id=".$id_acc_pay." group by acc_inv_id  
  ) as exist_recs ON gks_acc_inv.id_acc_inv=exist_recs.acc_inv_id
  WHERE ".
  ($pay_lock_recs==false ?
    "gks_acc_inv.user_id=".$user_id." and (gks_acc_inv.inv_state='080listing' or gks_acc_inv.inv_state='090ekdosi') 
      ":
    "gks_acc_inv.id_acc_inv in (
      select acc_inv_id from gks_acc_pay_poso_acc_inv where acc_pay_id=".$id_acc_pay."
    )"
  );
  //and eidos_parastatikou_type_id=".$other_eidos_parastatikou_type_id
  
  if (1==1 or $other_eidos_parastatikou_type_id==1) {// polisi
  
  $sql.="
  union
  
  SELECT 'order' as myfrom, gks_orders.id_order AS id_acc_inv, gks_orders.order_date AS inv_date, gks_orders.order_state AS inv_state,
  '' AS inv_acc_journal_id, '' AS acc_journal_code, '' AS acc_journal_descr, 
  0 AS inv_acc_seira_id, '' AS seira_code, '' AS seira_descr,0 AS inv_acc_number_int,
  gks_orders.gks_price_net, gks_orders.gks_price_total, gks_orders.products_posotita,  
  gks_company.company_afm,  gks_company.company_title, gks_company_subs.company_sub_title,
  ".$sql_field_notes_subnotes_order."
  '' AS note_doc, gks_orders.note_logistirio,
  CASE
    WHEN (order_state='060registered' or order_state='070inproduction' or
         order_state='090indelivery' or order_state='095execute' or order_state='100completed' or order_state='110payment') and affect_balance=1
      THEN affect_balance_poso
    ELSE 0
  END as affect_balance_calc,
  exist_recs.sum_poso,
  1 as eidos_parastatikou_type_id
  FROM ((gks_orders
  LEFT JOIN gks_company ON gks_orders.company_id = gks_company.id_company)
  LEFT JOIN gks_company_subs ON gks_orders.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN (
    select order_id,sum(poso) as sum_poso from gks_acc_pay_poso_order where acc_pay_id=".$id_acc_pay." group by order_id  
  ) as exist_recs ON gks_orders.id_order=exist_recs.order_id  
  WHERE ".
  ($pay_lock_recs==false ?
    "gks_orders.user_id=".$user_id."
     and (order_state='025offer' or order_state='055wait_payment' or order_state='060registered' or order_state='070inproduction' or
         order_state='090indelivery' or order_state='095execute' or order_state='100completed')" :
    "gks_orders.id_order in (
      select order_id from gks_acc_pay_poso_order where acc_pay_id=".$id_acc_pay."
    )"
  );
  
  
  
  }


  $sql.="
  union
  
  SELECT 'reservation' as myfrom, gks_hotel_reservation.id_hotel_reservation AS id_acc_inv, 
  gks_hotel_reservation.reservation_date AS inv_date, gks_hotel_reservation.reservation_status AS inv_state,
  '' AS inv_acc_journal_id, '' AS acc_journal_code, '' AS acc_journal_descr, 
  0 AS inv_acc_seira_id, '' AS seira_code, '' AS seira_descr,0 AS inv_acc_number_int,
  gks_hotel_reservation.gks_price_net, gks_hotel_reservation.gks_price_total, gks_hotel_reservation.products_posotita,  
  gks_company.company_afm,  gks_company.company_title, gks_company_subs.company_sub_title,
  ".$sql_field_notes_subnotes_reservation."  
  sxolio AS note_doc, gks_hotel_reservation.note_logistirio,
  CASE
    WHEN (reservation_status='070wait_payment' or reservation_status='080confirm' or
         reservation_status='100completed' or reservation_status='110payment') and affect_balance=1
      THEN affect_balance_poso
    ELSE 0
  END as affect_balance_calc,
  exist_recs.sum_poso,
  1 as eidos_parastatikou_type_id
  FROM (((gks_hotel_reservation
  LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)
  LEFT JOIN gks_company ON gks_hotel.company_id = gks_company.id_company)
  LEFT JOIN gks_company_subs ON gks_hotel.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN (
    select hotel_reservation_id,sum(poso) as sum_poso from gks_acc_pay_poso_hotel_reservation where acc_pay_id=".$id_acc_pay." group by hotel_reservation_id  
  ) as exist_recs ON gks_hotel_reservation.id_hotel_reservation=exist_recs.hotel_reservation_id  
  WHERE ".
  ($pay_lock_recs==false ?
    "gks_hotel_reservation.user_id=".$user_id."
     and (reservation_status='070wait_payment' or reservation_status='080confirm' or reservation_status='100completed')" :
    "gks_hotel_reservation.id_hotel_reservation in (
      select hotel_reservation_id from gks_acc_pay_poso_hotel_reservation where acc_pay_id=".$id_acc_pay."
    )"
  );
  




  $sql.=" ORDER BY inv_date";
  
  //return '<pre>'.$sql;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return '<div style="text-align:center;" class="alert alert-warning">error sql</div>';
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows==0) {
    return '<div style="text-align:center;" class="alert alert-warning">'.gks_lang('Δεν βρέθηκαν παραστατικά').'</div>';
  } 
  
  $gks_orders_ids=array();
  $gks_acc_inv_ids=array();
  $gks_hotel_reservation_ids=array();
  $rows=array();
  while ($row = $result->fetch_assoc()) {
    $row['ejof_poso']=0;
    $row['rest_poso']=floatval($row['affect_balance_calc']);
    $rows[]=$row;
    if ($row['myfrom']=='inv') {
      $gks_acc_inv_ids[]=$row['id_acc_inv'];
    } else if ($row['myfrom']=='reservation') {
      $gks_hotel_reservation_ids[]=$row['id_acc_inv'];
    } else {
      $gks_orders_ids[]=$row['id_acc_inv'];
    }
  }
  
  
  if (count($gks_orders_ids)>0) {
    $sql="SELECT order_id, Sum(poso) AS ejof_poso
    FROM gks_acc_pay_poso_order
    where order_id in (".implode(',',$gks_orders_ids).") and acc_pay_id<>'".$id_acc_pay."'
    GROUP BY order_id;";
    //echo '<pre>';print $sql;die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return '<div style="text-align:center;" class="alert alert-warning">error sql</div>';
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    while ($row_ejof = $result->fetch_assoc()) {
      foreach ($rows as &$row) {
        if ($row['myfrom']=='order' and $row['id_acc_inv']==$row_ejof['order_id']) {
          $row['ejof_poso']+=floatval($row_ejof['ejof_poso']);
          $row['rest_poso']= floatval($row['affect_balance_calc']) - floatval($row_ejof['ejof_poso']);
          
          break;
        }
      }
    }
    unset($row);
    //echo '<pre>';print_r($rows);die();

  }
  if (count($gks_acc_inv_ids)>0) {
    $sql="SELECT acc_inv_id, Sum(poso) AS ejof_poso
    FROM gks_acc_pay_poso_acc_inv
    where acc_inv_id in (".implode(',',$gks_acc_inv_ids).") and acc_pay_id<>'".$id_acc_pay."'
    GROUP BY acc_inv_id";
    //echo '<pre>';print $sql;die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return '<div style="text-align:center;" class="alert alert-warning">error sql</div>';
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    while ($row_ejof = $result->fetch_assoc()) {
      foreach ($rows as &$row) {
        if ($row['myfrom']=='inv' and $row['id_acc_inv']==$row_ejof['acc_inv_id']) {
          $row['ejof_poso']+=floatval($row_ejof['ejof_poso']);
          $row['rest_poso']= floatval($row['affect_balance_calc']) - floatval($row_ejof['ejof_poso']);
          
          break;
        }
      }
    }
    unset($row);
    
  }

  if (count($gks_hotel_reservation_ids)>0) {
    $sql="SELECT hotel_reservation_id, Sum(poso) AS ejof_poso
    FROM gks_acc_pay_poso_hotel_reservation
    where hotel_reservation_id in (".implode(',',$gks_hotel_reservation_ids).") and acc_pay_id<>'".$id_acc_pay."'
    GROUP BY hotel_reservation_id;";
    //echo '<pre>';print $sql;die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return '<div style="text-align:center;" class="alert alert-warning">error sql</div>';
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    while ($row_ejof = $result->fetch_assoc()) {
      foreach ($rows as &$row) {
        if ($row['myfrom']=='reservation' and $row['id_acc_inv']==$row_ejof['hotel_reservation_id']) {
          $row['ejof_poso']+=floatval($row_ejof['ejof_poso']);
          $row['rest_poso']= floatval($row['affect_balance_calc']) - floatval($row_ejof['ejof_poso']);
          
          break;
        }
      }
    }
    unset($row);
    //echo '<pre>';print_r($rows);die();

  }  
  
    
  $user_companys=gks_get_companys_list();

  $html='<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">'.
  '<thead>'.
  '<tr>'.
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap="nowrap" width="0%">#</th>'.
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('ID').'</th>'. 
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" >'.gks_lang('Τύπος').'</th>'.        
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" >'.gks_lang('Κατάσταση').'</th>'.        
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" >'.gks_lang('Ημερομηνία').'</th>'.
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="20%" >'.gks_lang('Εταιρεία').'</th>'.
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="20%" >'.gks_lang('Ημερολόγιο').'</th>'.        
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" >'.gks_lang('Σειρά').'</th>'.        
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" >'.gks_lang('Αριθμός').'</th>'.        
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Ποσότητα').'</th>'. 
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" >'.gks_lang('Τιμή').'</th>'.        
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" >'.gks_lang('Τιμή για<br>υπόλοιπο').'</th>'.        
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" >'.gks_lang('Ποσό από<br>άλλη πληρωμή').'</th>'.        
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" >'.gks_lang('Ποσό').'</th>'.        
    '<th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="50%" >'.gks_lang('Σχόλια').'</th>'.        
  '</tr>'.
  '</thead>'.
  '<tbody>';

  $sum_gks_price_net=0;
  $sum_affect_balance_calc=0;
  $sum_pay_poso=0;
  $sum_ejof_poso=0;
  $sum_rest_poso=0;
  $i = 0;
  foreach ($rows as $row) {

    $i++;
    
    $prosimo=1;
    if ($eidos_parastatikou_type_id==11) {         //eisparjeis
//      if ($row['eidos_parastatikou_type_id']==1) { //posili
//        $prosimo=1;         
//      } else if ($row['eidos_parastatikou_type_id']==2) { //agora
//        $prosimo=-1;
//      }
    } else if ($eidos_parastatikou_type_id==12) {  //pliromes
//      if ($row['eidos_parastatikou_type_id']==1) { //polisi
//        $prosimo=-1;         
//      } else if ($row['eidos_parastatikou_type_id']==2) { //agora
//        $prosimo=1;
//      }
      $prosimo=-1;       
    }
    
    
    
    
//    $sum_gks_price_net+=floatval($row['gks_price_net']);
//    $sum_affect_balance_calc+=floatval($prosimo*$row['affect_balance_calc']);
//    $sum_pay_poso+=$prosimo*$row['sum_poso'];
//    $sum_ejof_poso+=$prosimo*$row['ejof_poso'];
//    $sum_rest_poso+=$prosimo*$row['rest_poso'];
    
    $html.='<tr>'.
    '<th scope="row" nowrap class="mytdcm aa">'.($i).'</th>'.
    '<td nowrap class="mytdcm">'.
      '<table cellpadding=0 cellspacing=0 class="gks_tb1">'.
        '<tr class="gks_tr1">'.
          '<td class="gks_ttd1">'.$row['id_acc_inv'].'</td>'.
        '</tr>'.
        '<tr class="gks_tr1">'.
          '<td class="gks_ttd1">';
    if ($row['myfrom']=='inv') {
      $html.='<a href="admin-acc-inv-item.php?id='.$row['id_acc_inv'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>';
    } else if ($row['myfrom']=='reservation') {
      $html.='<a href="admin-hotel-reservation-item.php?id='.$row['id_acc_inv'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>';
    } else {
      $html.='<a href="admin-orders-item.php?id='.$row['id_acc_inv'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>';
    }       
    $html.='</td>'.
        '</tr>'.
     '</table>'.
    '</td>'.
    '<td nowrap class="mytdcm">';
    if ($row['myfrom']=='inv') {
      $html.=gks_lang('Παραστατικό');
    } else if ($row['myfrom']=='reservation') {
      $html.=gks_lang('Κράτηση');
    } else {
      $html.=gks_lang('Παραγγελία');
    }
    $html.='</td>'.    
    
    '<td nowrap class="mytdcm">';
    if ($row['myfrom']=='inv') {
      $html.='<span class="acc_inv_state_'.$row['inv_state'].'">'.getAccInvStateDescr($row['inv_state']).'</span>';
    } else if ($row['myfrom']=='reservation') {
      $html.='<span class="reservation_status_'.$row['inv_state'].'">'.getHotelReservationStatusDescr($row['inv_state']).'</span>';
    } else {
      $html.='<span class="order_state_'.$row['inv_state'].'">'.getOrderStateDescr($row['inv_state']).'</span>';
    }
    $html.='</td>'.
    '<td nowrap class="mytdcm">'.showDate(strtotime($row['inv_date']), 'd/m/Y\<\b\r\>H:i:s', 1).'</td>'.
    '<td class="mytdcm">'.$row['company_title'].(isset($row['company_sub_title']) ? '<br>'.$row['company_sub_title'] : '').'</td>'.
    '<td class="mytdcm">'.$row['acc_journal_descr'].'</td>'.
    '<td nowrap class="mytdcm">'.$row['seira_code'].'</td>'.
    '<td nowrap class="mytdcm">'.($row['inv_acc_number_int']<>0 ? $row['inv_acc_number_int'] : '').'</td>'.
    '<td nowrap class="mytdcm">'.($row['products_posotita']!=0 ? $row['products_posotita'] : '').'</td>'.
    '<td nowrap class="mytdcm" >'.
      ($row['gks_price_net']!=0 ? '<b>'.myCurrencyFormat($row['gks_price_net']).'</b>' : '').
    '</td>'.
    '<td nowrap class="mytdcm" >'.
      ($row['affect_balance_calc']!=0 ? myCurrencyFormat($prosimo*$row['affect_balance_calc']) : '').
    '</td>'.
    '<td nowrap class="mytdcm" >'.
      ((isset($row['ejof_poso']) and $row['ejof_poso']!=0) ? myCurrencyFormat($prosimo*$row['ejof_poso']) : '').
      //((isset($row['rest_poso']) and $row['rest_poso']!=0) ? myCurrencyFormat($prosimo*$row['rest_poso']) : '').
    '</td>'.
    
    '<td nowrap class="mytdcm" >';
    

    

    
    
    if ($pay_lock_recs==false) {
      $sum_gks_price_net+=floatval($row['gks_price_net']);
      $sum_affect_balance_calc+=floatval($prosimo*$row['affect_balance_calc']);
      $sum_pay_poso+=$prosimo*$row['sum_poso'];
      $sum_ejof_poso+=$prosimo*$row['ejof_poso'];
      $sum_rest_poso+=$prosimo*$row['rest_poso'];



      $val_poso=0;
      foreach ($pay_poso as $val) {
        if ($val['f']==$row['myfrom'] and $val['i']==$row['id_acc_inv']) {
          $val_poso=$val['v'];
          break; 
        }
      }
      
      $rest_poso=0; if (isset($row['rest_poso'])) $rest_poso = $prosimo*$row['rest_poso'];
      
      
      $html.='<input type="number" value="'.($val_poso!=0 ? myNumberFormatNo0($val_poso) : '').'" '.
      'class="form-control form-control-sm pay_poso_for_invs" '.
      'data-myfrom="'.$row['myfrom'].'" data-recid="'.$row['id_acc_inv'].'" '.
      'step="'.$GKS_INPUT_STEP_AJIA.'" ';

      $pososto_bar=0;
      $color_bar='';
      if ($rest_poso > 0) {
        $html.='placeholder="'.gks_lang('μεγ').':'.myNumberFormatNo0Local($rest_poso).'" ';
        $html.='min=0 max="'.myNumberFormatNo0($rest_poso).'" ';
        $pososto_bar=100*$val_poso/$rest_poso;
        $color_bar='#dc3545'; if ($val_poso==$rest_poso) $color_bar='#47a447';
      } else if ($rest_poso < 0) {
        $html.='placeholder="'.gks_lang('ελα').':'.myNumberFormatNo0Local($rest_poso).'" ';
        $html.='min="'.myNumberFormatNo0($rest_poso).'" max=0 ';
        $pososto_bar=100*$val_poso/$rest_poso;
        $color_bar='#dc3545'; if ($val_poso==$rest_poso) $color_bar='#47a447';
      } else {
        $html.='min=0 max=0 ';
      }
      $html.='>';
      
      $html.='<div style="width:100%;height:5px;overflow: hidden;">'.
        '<div class="pay_poso_for_invs_bar" data-myfrom="'.$row['myfrom'].'" data-recid="'.$row['id_acc_inv'].'" '.
        'style="width:'.$pososto_bar.'%;height:5px;background-color:'.$color_bar.';"></div>'.
      '</div>';
      
      //$html.='|'.$row['eidos_parastatikou_type_id'].'|'.$prosimo;
    } else {

      $sum_gks_price_net+=floatval($row['gks_price_net']);
      $sum_affect_balance_calc+=floatval($prosimo*$row['affect_balance_calc']);
      $sum_pay_poso+=$row['sum_poso'];
      $sum_ejof_poso+=$row['ejof_poso'];
      $sum_rest_poso+=$row['rest_poso'];

      
      $html.='<div>'.myCurrencyFormat($row['sum_poso']).'</div>';
      
      $val_poso=$prosimo*$row['sum_poso'];
      $rest_poso=0; if (isset($row['rest_poso'])) $rest_poso = $row['rest_poso'];
      
      //$rest_poso-=$row['ejof_poso'];
      
      $pososto_bar=0;
      $color_bar='';
      if ($rest_poso > 0) {
        $pososto_bar=100*$val_poso/$rest_poso;
        $color_bar='#dc3545'; if ($val_poso==$rest_poso) $color_bar='#47a447';
      } else if ($rest_poso < 0) {
        $pososto_bar=100*$val_poso/$rest_poso;
        $color_bar='#dc3545'; if ($val_poso==$rest_poso) $color_bar='#47a447';
      } 
      
      $html.='<div style="width:100px;height:5px;overflow: hidden;">'.
        '<div class="pay_poso_for_invs_bar" data-myfrom="'.$row['myfrom'].'" data-recid="'.$row['id_acc_inv'].'" '.
        'style="width:'.$pososto_bar.'%;height:5px;background-color:'.$color_bar.';"></div>'.
      '</div>';
      
      //$html.=$val_poso.'|'.$rest_poso.'|'.$row['ejof_poso'];
      
    }
    //print '<pre>';print_r($row);die();
    
    $html.='</td>'.
    '<td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">';
    
      $temp='';
      if (!empty($row['notes'])) $temp.=gks_lang('Πελάτης').': <b>'.nl2br_gks($row['notes']).'</b><br>';
      if (!empty($row['subnotes'])) $temp.=gks_lang('Πελάτης (Συν)').': <b>'.nl2br_gks($row['subnotes']).'</b><br>';
      if (!empty($row['note_doc'])) $temp.=gks_lang('Έγγραφο').': <b>'.nl2br_gks($row['note_doc']).'</b><br>';
      //if (!empty($row['note_production'])) $temp.=gks_lang('Παραγωγή').': <b>'.nl2br_gks($row['note_production']).'</b><br>';
      if (!empty($row['note_logistirio'])) $temp.=gks_lang('Λογιστήριο').': <b>'.nl2br_gks($row['note_logistirio']).'</b><br>';
      
      if ($temp!='') $temp=substr($temp, 0, strlen($temp)-4);
      $html.= $temp;
    $html.='</div></div></td>';   

    $html.='</tr>';    
  }

  $html.='<tr>'.
    '<th scope="row" nowrap class="mytdcm"></th>'.
    '<td nowrap class="mytdcmr" colspan="9"><b>'.gks_lang('Σύνολα').':</b></td>'.
    '<td nowrap class="mytdcm" >'.
      '<b>'.myCurrencyFormat($sum_gks_price_net).'</b>'.
    '</td>'.
    '<td nowrap class="mytdcm" >'.
      myCurrencyFormat($sum_affect_balance_calc).
    '</td>'.
    '<td nowrap class="mytdcm">'.($sum_ejof_poso!=0 ? myCurrencyFormat($sum_ejof_poso) : '').'</td>'.
    '<td nowrap class="mytdcm"><span id="sum_pay_poso_for_invs">'.myCurrencyFormat($sum_pay_poso).'</span></td>'.
    '<td nowrap class="mytdcm"></td>'.
  '</tr>';

  $html.='<tr>'.
    '<th scope="row" nowrap class="mytdcm"></th>'.
    '<td nowrap class="mytdcmr" colspan="12"><b>'.gks_lang('Τρέχουσα πληρωμή').':</b></td>'.
    '<td nowrap class="mytdcm" id="bal_gks_total_price_total2">'.(($pay_lock_recs) ? myCurrencyFormat($gks_price_total) : '').'</td>'.
    '<td nowrap class="mytdcm"></td>'.
  '</tr>';
  
  $temp='';
  if ($pay_lock_recs) {
    $diafora= $gks_price_total-$sum_pay_poso;
    $temp=myCurrencyFormat($diafora);
    $mymin=1;
    if ($GKS_NUMBER_FORMAT_CURRENCY_DECIMAL>0) $mymin=(1/2)/pow(10, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL); // 0.005
    if (abs($diafora) < $mymin)
        $temp='<span style="font-weight:bold;font-size:150%;color:#47a447;">' . $temp . '</span>';
      else
        $temp='<span style="font-weight:bold;font-size:150%;color:#dc3545;">' . $temp . '</span>';
  }
  
  
  $html.='<tr>'.
    '<th scope="row" nowrap class="mytdcm"></th>'.
    '<td nowrap class="mytdcmr" colspan="12" style="font-weight:bold;font-size:150%;color:#47a447;">'.gks_lang('Διαφορά').':</td>'.
    '<td nowrap class="mytdcm" id="diafora_pay_poso_for_invs">'.$temp.'</td>'.
    '<td nowrap class="mytdcm"></td>'.
  '</tr>';


  $html.='</tbody></table>';
  
  
  
  return $html;
}
function gks_pay_sxolio_log($id,$row_old,$products_old,$extra_address_old,$sxolio_log_start,$myparams,$gks_custom_row_old) {
  global $db_link;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_CRM_ENABLE;
  
  $ret_aade_errors='';
  if (isset($myparams['ret_aade_errors'])) $ret_aade_errors=trim_gks($myparams['ret_aade_errors']);
  
  $sql=select_gks_acc_pay()." where id_acc_pay=".$id." limit 1"; 

  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row_new = $result->fetch_assoc();
  
  $sql="SELECT gks_acc_pay_method.*
  FROM gks_acc_pay_method 
  WHERE gks_acc_pay_method.acc_pay_id=".$id."
  ORDER BY gks_acc_pay_method.paymethod_aa;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $products_new=array();
  while ($row = $result->fetch_assoc()) {
    $products_new[]=$row;
  }
    
  
  $sxolio_log=$sxolio_log_start;
  if (trim_gks($row_old['company_title']) != trim_gks($row_new['company_title'])) 
    $sxolio_log.=gks_lang('Εταιρεία').': <b>'.$row_old['company_title'].'</b> [[-r]] <b>'.$row_new['company_title'].'</b>'.'<br>';
  
  if (trim_gks($row_old['company_sub_title']) != trim_gks($row_new['company_sub_title'])) 
    $sxolio_log.=gks_lang('Υποκατάστημα').': <b>'.$row_old['company_sub_title'].'</b> [[-r]] <b>'.$row_new['company_sub_title'].'</b>'.'<br>';

  if (trim_gks($row_old['acc_journal_descr']) != trim_gks($row_new['acc_journal_descr'])) 
    $sxolio_log.=gks_lang('Ημερολόγιο').': <b>'.$row_old['acc_journal_descr'].'</b> [[-r]] <b>'.$row_new['acc_journal_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['seira_code'].' - '.$row_old['seira_descr']) != trim_gks($row_new['seira_code'].' - '.$row_new['seira_descr'])) 
    $sxolio_log.=gks_lang('Σειρά').': <b>'.$row_old['seira_code'].' - '.$row_old['seira_descr'].'</b> [[-r]] <b>'.$row_new['seira_code'].' - '.$row_new['seira_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['pay_acc_number_int']) != trim_gks($row_new['pay_acc_number_int'])) 
    $sxolio_log.=gks_lang('Αριθμός').': <b>'.$row_old['pay_acc_number_int'].'</b> [[-r]] <b>'.$row_new['pay_acc_number_int'].'</b>'.'<br>';

  if (trim_gks($row_old['pay_date']) != trim_gks($row_new['pay_date'])) 
    $sxolio_log.=gks_lang('Ημερομηνία').': <b>'.showDate(strtotime($row_old['pay_date']), 'd/m/Y H:i', 1).'</b> [[-r]] <b>'.showDate(strtotime($row_new['pay_date']), 'd/m/Y H:i', 1).'</b>'.'<br>';

  if ($row_old['pay_state'].'' != $row_new['pay_state'].'') 
    $sxolio_log.=gks_lang('Κατάσταση').': <span class="acc_pay_state_'.$row_old['pay_state'].'">'.getAccPayStateDescr($row_old['pay_state']).'</span> [[-r]] '.
    '<span class="acc_pay_state_'.$row_new['pay_state'].'">'.getAccPayStateDescr($row_new['pay_state']).'</span>'.'<br>';






  //echo '<pre>'.$row_new['company_title']; die();
  
  
  
  if ((isset($row_old['gks_nickname']) and isset($row_old['gks_nickname']) == false) or 
      (isset($row_old['gks_nickname']) == false and isset($row_old['gks_nickname'])) or 
      $row_old['gks_nickname'] != $row_new['gks_nickname']) 
    $sxolio_log.=gks_lang('Πελάτης').': <b>'.(isset($row_old['gks_nickname']) ? $row_old['gks_nickname'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['gks_nickname']) ? $row_new['gks_nickname'] : '').'</b>'.'<br>';



  $ee_initials_old='';
  $sql="select id_country,country_ee,country_name,country_initials 
  FROM gks_country where id_country=".$row_old['ma_country_id']." ORDER BY country_name";
  $result_select = $db_link->query($sql);        
  if (!$result_select) {
    debug_mail(false,'error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  }
  if ($result_select->num_rows==1) {
    $row_select = $result_select->fetch_assoc();
    $ee_initials_old=trim_gks($row_select['country_ee']);
  }
  $ee_initials_new='';
  $sql="select id_country,country_ee,country_name,country_initials 
  FROM gks_country where id_country=".$row_new['ma_country_id']." ORDER BY country_name";
  $result_select = $db_link->query($sql);        
  if (!$result_select) {
    debug_mail(false,'error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  }
  if ($result_select->num_rows==1) {
    $row_select = $result_select->fetch_assoc();
    $ee_initials_new=trim_gks($row_select['country_ee']);
  }
  




  



  
  
  
    
  //compare products_old products_new
  foreach ($products_old as &$pitem_old) {
    $pitem_old['del']=false;
    $pitem_old['k']=-1;
  }
  unset($pitem_old);
  foreach ($products_new as &$pitem_new) {
    $pitem_new['del']=false;
    $pitem_new['k']=-1;
  }
  unset($pitem_new);

  foreach ($products_old as $key_old => &$pitem_old) {
    $found=-1;
    foreach ($products_new as $key_new =>&$pitem_new) {
      if ($pitem_old['id_acc_pay_method'] == $pitem_new['id_acc_pay_method']) {
        $pitem_old['k'] = $key_new;
        $pitem_new['k'] = $key_old;
        $found = $key_new;
        break;
      }
    }
    unset($pitem_new);
    if ($found==-1) $pitem_old['del']=true;
  }  
  unset($pitem_old);

  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/compare.txt',print_r($products_old,true).' '.print_r($products_new,true));

  
  
  
  $sxolio_eidi_log='';
  foreach ($products_old as $pitem_old) {
    if ($pitem_old['del']) {
      $sxolio_eidi_log.=gks_lang('Αφαιρέθηκε τρόπος πληρωμής').': <b>'.$pitem_old['paymethod_descr'].'</b><br>';
    }
  }
  
  foreach ($products_new as $key_new =>&$pitem_new) {
    if ($pitem_new['del'] == false and $pitem_new['k'] == -1) {
      $sxolio_eidi_log.=gks_lang('Προστέθηκε τρόπος πληρωμής').': <b>'.$pitem_new['paymethod_descr'].'</b> '.
      gks_lang('Αξία').': <b>'.number_format($pitem_new['paymethod_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND).'</b> '.
      '<br>';
    }
  }
  

  $pn=$products_new;
  foreach ($products_old as $p) {
    if ($p['k']>=0) {
      $item_descr_change='';
      if ($p['paymethod_descr'] != $pn[$p['k']]['paymethod_descr']) 
        $item_descr_change.='<b>'.$p['paymethod_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['paymethod_descr'].'</b>';
      
      $item_txt='';
      
      
      if ($p['paymethod_comments'] != $pn[$p['k']]['paymethod_comments']) 
        $item_txt.=gks_lang('Παρατηρήσεις').': <b>'.$p['paymethod_comments'].'</b> [[-r]] <b>'.$pn[$p['k']]['paymethod_comments'].'</b> ';
      
      if ($p['paymethod_total'] != $pn[$p['k']]['paymethod_total']) 
        $item_txt.=gks_lang('Τιμή').': <b>'.myCurrencyFormat($p['paymethod_total'], false).'</b> [[-r]] <b>'.myCurrencyFormat($pn[$p['k']]['paymethod_total'], false).'</b> ';
      

        
        
        
      if ($item_txt != '') {
        $item_txt=trim_gks($item_txt);
        if ($item_descr_change!='') $item_txt=$item_descr_change.': '.$item_txt;
        else $item_txt=$p['paymethod_descr'].': '.$item_txt;
        
        $sxolio_eidi_log.=$item_txt.'<br>';
        
      }
    }
  }  
  
  $sxolio_log.=$sxolio_eidi_log;




  if ($row_old['gks_price_total'] != $row_new['gks_price_total']) 
    $sxolio_log.=gks_lang('Σύνολο').': <b>'.myCurrencyFormat($row_old['gks_price_total']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_total']).'</b>'.'<br>';
  
  if ((isset($row_old['note_doc']) and isset($row_old['note_doc']) == false) or 
      (isset($row_old['note_doc']) == false and isset($row_old['note_doc'])) or 
      $row_old['note_doc'] != $row_new['note_doc']) 
    $sxolio_log.=gks_lang('Σχόλια εγγράφου').':<br><b>'.(isset($row_old['note_doc']) ? $row_old['note_doc'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['note_doc']) ? $row_new['note_doc'] : '').'</b>'.'<br>';

  if ((isset($row_old['note_logistirio']) and isset($row_old['note_logistirio']) == false) or 
      (isset($row_old['note_logistirio']) == false and isset($row_old['note_logistirio'])) or 
      $row_old['note_logistirio'] != $row_new['note_logistirio']) 
    $sxolio_log.=gks_lang('Εσωτερική σημείωση για λογιστήριο').':<br><b>'.(isset($row_old['note_logistirio']) ? $row_old['note_logistirio'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['note_logistirio']) ? $row_new['note_logistirio'] : '').'</b>'.'<br>';


  if (intval($row_old['assigned_id']) != intval($row_new['assigned_id']))
    $sxolio_log.=gks_lang('Ανάθεση σε').': <b>'.trim_gks($row_old['gks_nickname_assigned']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['gks_nickname_assigned']).'</b>'.'<br>';

  if ($GKS_CRM_ENABLE) {
  if (intval($row_old['crm_channel_id']) != intval($row_new['crm_channel_id']))
    $sxolio_log.=gks_lang('Κανάλι πωλήσεων').': <b>'.trim_gks($row_old['crm_channel_sale_descr']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['crm_channel_sale_descr']).'</b>'.'<br>';

  if (intval($row_old['crm_channel_contact_id']) != intval($row_new['crm_channel_contact_id']))
    $sxolio_log.=gks_lang('Επαφή Πωλήσεων').': <b>'.trim_gks($row_old['crm_channel_contact_gks_nickname']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['crm_channel_contact_gks_nickname']).'</b>'.'<br>';

  if (intval($row_old['crm_channel_campain_id']) != intval($row_new['crm_channel_campain_id']))
    $sxolio_log.=gks_lang('Καμπάνια').': <b>'.trim_gks($row_old['ads_campain_name']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['ads_campain_name']).'</b>'.'<br>';

  if (trim_gks($row_old['crm_channel_url']) != trim_gks($row_new['crm_channel_url'])) 
    $sxolio_log.=gks_lang('URL').':<br><b>'.(isset($row_old['crm_channel_url']) ? nl2br_gks($row_old['crm_channel_url']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['crm_channel_url']) ? nl2br_gks($row_new['crm_channel_url']) : '').'</b>'.'<br>';

  if (trim_gks($row_old['crm_channel_code']) != trim_gks($row_new['crm_channel_code'])) 
    $sxolio_log.=gks_lang('Κωδικός CRM').':<br><b>'.(isset($row_old['crm_channel_code']) ? nl2br_gks($row_old['crm_channel_code']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['crm_channel_code']) ? nl2br_gks($row_new['crm_channel_code']) : '').'</b>'.'<br>';

  if (trim_gks($row_old['crm_channel_text']) != trim_gks($row_new['crm_channel_text'])) 
    $sxolio_log.=gks_lang('Σχόλιο').':<br><b>'.(isset($row_old['crm_channel_text']) ? nl2br_gks($row_old['crm_channel_text']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['crm_channel_text']) ? nl2br_gks($row_new['crm_channel_text']) : '').'</b>'.'<br>';
  }
 

  if (trim_gks($row_old['aade_statuscode']) != trim_gks($row_new['aade_statuscode'])) {

    $sxolio_log.=gks_lang('ΑΑΔΕ - Κωδικός Αποτελέσματος').': ';
                  if (trim_gks($row_old['aade_statuscode'])!='') {
                    $sxolio_log.='<span class="aade_xml_response_';
                    if ($row_old['aade_statuscode']=='Success') $sxolio_log.='ok';
                    else if ($row_old['aade_statuscode']=='Processing') $sxolio_log.='processing';
                    else $sxolio_log.='error';
                    
                    $sxolio_log.=' tooltipster" '.
                    'title="'.getAADEstatuscodeDescr($row_old['aade_statuscode']).'">'.$row_old['aade_statuscode'].'</span>';
                  }
                  $sxolio_log.=' [[-r]] ';
                  if (trim_gks($row_new['aade_statuscode'])<>'') {
                    $sxolio_log.='<span class="aade_xml_response_';
                    if ($row_new['aade_statuscode']=='Success') $sxolio_log.='ok';
                    else if ($row_new['aade_statuscode']=='Processing') $sxolio_log.='processing';
                    else $sxolio_log.='error';
                    $sxolio_log.=' tooltipster" '.
                    'title="'.getAADEstatuscodeDescr($row_new['aade_statuscode']).'">'.$row_new['aade_statuscode'].'</span>';
                  }
                  $sxolio_log.='<br>';
  }
  
  if (trim_gks($row_old['aade_invoiceuid']) != trim_gks($row_new['aade_invoiceuid'])) 
    $sxolio_log.=gks_lang('ΑΑΔΕ - Αναγνωριστικό Παραστατικού').': <b>'.$row_old['aade_invoiceuid'].'</b> [[-r]] <b>'.$row_new['aade_invoiceuid'].'</b>'.'<br>';
  
  if (trim_gks($row_old['aade_invoicemark']) != trim_gks($row_new['aade_invoicemark'])) 
    $sxolio_log.=gks_lang('ΑΑΔΕ - ΜΑΡΚ').': <b>'.$row_old['aade_invoicemark'].'</b> [[-r]] <b>'.$row_new['aade_invoicemark'].'</b>'.'<br>';

  if (trim_gks($row_old['aade_send_date']) != trim_gks($row_new['aade_send_date'])) 
    $sxolio_log.=gks_lang('ΑΑΔΕ - Ημερομηνία Αποστολής').': '.
                 (isset($row_old['aade_send_date']) ? '<b>'.showDate(strtotime($row_old['aade_send_date']), 'd/m/Y H:i', 1).'</b>' : '').
                 ' [[-r]] '.
                 (isset($row_new['aade_send_date']) ? '<b>'.showDate(strtotime($row_new['aade_send_date']), 'd/m/Y H:i', 1).'</b>' : '').
                 '<br>';
                        
  if (trim_gks($row_old['aade_xml_send']) != trim_gks($row_new['aade_xml_send'])) {
    $temp_new='';
    if (trim_gks($row_new['aade_xml_send'])!='') {
      $local_file=GKS_FileServerShare.'acc/pay/'.$id.'/aade_mydata/'.$row_new['aade_xml_send'];
      if (file_exists($local_file)) {
        $url_file='admin-get-file.php?fs=fileservers&file=acc%2Fpay%2F'.$id.'%2Faade_mydata%2F'.urlencode($row_new['aade_xml_send']);
        $temp_new.= '<a href="'.$url_file.'" target="_blank">'.$row_new['aade_xml_send'].'</a> ';
        $temp_new.= '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
      }
    }    
    $sxolio_log.=gks_lang('ΑΑΔΕ - Απεσταλμένο XML').': [[-r]] '.$temp_new.'<br>';    
  }

  if (trim_gks($row_old['aade_xml_response']) != trim_gks($row_new['aade_xml_response'])) {
    $temp_new='';
    if (trim_gks($row_new['aade_xml_response'])!='') {
      $local_file=GKS_FileServerShare.'acc/pay/'.$id.'/aade_mydata/'.$row_new['aade_xml_response'];
      if (file_exists($local_file)) {
        $url_file='admin-get-file.php?fs=fileservers&file=acc%2Fpay%2F'.$id.'%2Faade_mydata%2F'.urlencode($row_new['aade_xml_response']);
        $temp_new.= '<a href="'.$url_file.'" target="_blank">'.$row_new['aade_xml_response'].'</a> ';
        $temp_new.= '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
      }
    }    
    $sxolio_log.=gks_lang('ΑΑΔΕ - Απάντηση XML').': [[-r]] '.$temp_new.'<br>';    
  }


  $aade_errors_old=trim_gks($row_old['aade_errors']);
  $aade_errors_new=trim_gks($row_new['aade_errors']);
  if ($aade_errors_new=='') $aade_errors_new=$ret_aade_errors;
  if ($aade_errors_old != $aade_errors_new) $sxolio_log.=gks_lang('ΑΑΔΕ - Σφάλματα').': [[-r]] '.($aade_errors_new=='' ? 'Κανένα' : $aade_errors_new).'<br>';    


  $gks_custom_prepare=gks_custom_table_item_prepare('gks_acc_pay',['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;

  
  
  if ($sxolio_log == '') $sxolio_log=gks_lang('Ενημέρωση').'<br>';
  //print '<pre>';
  //print_r($products_old);
  //die();  
  
  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into gks_acc_pay_log (acc_pay_id, add_date,user_id,sxolio) values (
    ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
    
    //$return = array('success' => false, 'message' => base64_encode($sql));
    //echo json_encode($return); die();  
     
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
  }
}
