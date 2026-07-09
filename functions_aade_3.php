<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_aade_invoice_xml_create($id,$doc_table,$aade_params) {
  global $db_link;
  $ret = array('success' => false, 'message' => 'generic error');
  
  $call_from_paroxos=false;
  if (isset($aade_params['call_from_paroxos'])) $call_from_paroxos=$aade_params['call_from_paroxos'];
  
  if ($id<=0) {$ret['message']=gks_lang('Δεν έχει ορισθεί το').' ID.';debug_mail(false,$ret['message'],''); return $ret;}
  
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
    $ttt='acc_inv';
    $rrr='inv_acc';
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
    $ttt='acc_pay';
    $rrr='pay_acc';
  } else if ($doc_table=='gks_whi_mov') {
    $xxx='mov';
    $ttt='whi_mov'; 
    $rrr='mov_whi';
  } else {
    $xxx='';
  }
  
  $uid_array=[];

  if ($doc_table=='gks_acc_inv') {
    $sql="SELECT gks_acc_inv.*, 
    ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
    ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
    gks_company.company_title, 
    gks_company.company_eponimia,
    gks_company.company_afm,
    gks_company.company_doy,
    gks_company.aade_branch,
    gks_company.company_odos,
    gks_company.company_arithmos,
    gks_company.company_orofos,
    gks_company.company_perioxi,
    gks_company.company_poli,
    gks_company.company_tk,
    company_nomoi.nomos_descr as company_nomos_descr,
    company_country.country_initials as company_country_initials,
    company_country.country_name as company_country_name,
    company_country.country_ee as company_country_ee,
    gks_company.company_email,
    gks_company.company_phone,
    gks_company.company_gemi_number,
        
    gks_company_subs.company_sub_title,
    gks_company_subs.company_sub_odos,
    gks_company_subs.company_sub_arithmos,
    gks_company_subs.company_sub_orofos,
    gks_company_subs.company_sub_perioxi,
    gks_company_subs.company_sub_poli,
    gks_company_subs.company_sub_tk,
    company_sub_nomoi.nomos_descr as company_sub_nomos_descr,
    company_sub_country.country_initials as company_sub_country_initials,
    company_sub_country.country_name as company_sub_country_name,
    company_sub_country.country_ee as company_sub_country_ee,
    gks_company_subs.company_sub_email,
    gks_company_subs.company_sub_phone,
        
    gks_acc_journal.acc_journal_code, 
    gks_acc_journal.acc_journal_descr, 
    gks_acc_journal.acc_eidos_parastatikou_whi_id,
    gks_acc_journal.acc_eidos_parastatikou_other_entity,
    gks_acc_seires.seira_code, 
    gks_acc_seires.seira_descr, 
    gks_acc_seires.seira_isdeliverynote,
    0 as seira_is_reverse_delivery_note,
    gks_acc_seires.seira_is_self_pricing,
    gks_acc_seires.seira_is_vat_payment_suspension,    
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
    gks_acc_eidi_parastatikon.peppol_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_posotita,
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
    gks_aade_skopos_diakinisis.aade_skopos_diakinisis_code,
    gks_users.ma_branch as ma_branch_user,
    gks_users.order_sxolio,
    gks_users.pelati_sxolio,
    gks_users.gemi_number,
    gks_users.is_b2g,
    gks_users.b2g_aaht_code,
    gks_users.b2g_aaht_name,
    gks_users.b2g_aaht_foreas,
    gks_users.b2g_aaht_typos_forea,
    gks_users.b2g_aaht_kodikos_ekatharisis,    
    party_country.country_initials as party_country_initials,
    party_country.country_name as party_country_name,
    party_country.country_ee as party_country_ee,
    party_nomoi.nomos_descr as party_nomos_descr,
    gks_eshop_pricelist.pricelist_descr, 
    gks_eshop_fiscal_position.fiscal_position_descr,
    gks_payment_acquirers.payment_acquirer_name, 
    gks_aade_tropoi_pliromis.aade_tropos_pliromis_code,
    gks_delivery_methods.delivery_method_name
    
    FROM ((((((((((((((((((((gks_acc_inv
    
  
    
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_inv.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_inv.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
    LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_nomoi as company_nomoi ON gks_company.company_nomos_id = company_nomoi.id_nomos)
    LEFT JOIN gks_country as company_country ON gks_company.company_country_id = company_country.id_country)
    LEFT JOIN gks_nomoi as company_sub_nomoi ON gks_company_subs.company_sub_nomos_id = company_sub_nomoi.id_nomos)
    LEFT JOIN gks_country as company_sub_country ON gks_company_subs.company_sub_country_id = company_sub_country.id_country)
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
    LEFT JOIN gks_aade_tropoi_pliromis ON gks_payment_acquirers.aade_tropos_pliromis_id = gks_aade_tropoi_pliromis.id_aade_tropos_pliromis)
    LEFT JOIN gks_delivery_methods ON gks_acc_inv.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
    LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
    LEFT JOIN gks_eshop_fiscal_position ON gks_acc_inv.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
    LEFT JOIN gks_eshop_pricelist ON gks_acc_inv.pricelist_id = gks_eshop_pricelist.id_pricelist)
    LEFT JOIN gks_country as party_country ON gks_acc_inv.ma_country_id = party_country.id_country)
    LEFT JOIN gks_nomoi as party_nomoi ON gks_acc_inv.ma_nomos_id = party_nomoi.id_nomos)
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_aade_skopos_diakinisis ON gks_acc_inv.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis
    
    where gks_acc_inv.id_acc_inv=".$id;

  } else if ($doc_table=='gks_acc_pay') {
    $sql="SELECT gks_acc_pay.*,
    ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
    ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
    gks_company.company_title, 
    gks_company.company_eponimia,
    gks_company.company_afm,
    gks_company.company_doy,
    gks_company.aade_branch,
    gks_company.company_odos,
    gks_company.company_arithmos,
    gks_company.company_orofos,
    gks_company.company_perioxi,
    gks_company.company_poli,
    gks_company.company_tk,
    company_nomoi.nomos_descr as company_nomos_descr,
    company_country.country_initials as company_country_initials,
    company_country.country_name as company_country_name,
    company_country.country_ee as company_country_ee,
    gks_company.company_email,
    gks_company.company_phone,
    gks_company.company_gemi_number,
    
    gks_company_subs.company_sub_title,
    gks_company_subs.company_sub_odos,
    gks_company_subs.company_sub_arithmos,
    gks_company_subs.company_sub_orofos,
    gks_company_subs.company_sub_perioxi,
    gks_company_subs.company_sub_poli,
    gks_company_subs.company_sub_tk,
    company_sub_nomoi.nomos_descr as company_sub_nomos_descr,
    company_sub_country.country_initials as company_sub_country_initials,
    company_sub_country.country_name as company_sub_country_name,
    company_sub_country.country_ee as company_sub_country_ee,
    gks_company_subs.company_sub_email,
    gks_company_subs.company_sub_phone,
    
    gks_acc_journal.acc_journal_code, 
    gks_acc_journal.acc_journal_descr, 
    gks_acc_journal.acc_eidos_parastatikou_whi_id,
    gks_acc_journal.acc_eidos_parastatikou_other_entity,
    gks_acc_seires.seira_code, 
    gks_acc_seires.seira_descr,
    0 as seira_isdeliverynote, 
    0 as seira_is_reverse_delivery_note,
    0 as seira_is_self_pricing,
    0 as seira_is_vat_payment_suspension,    
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
    gks_acc_eidi_parastatikon.peppol_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_posotita,
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
                          0 as aade_skopos_diakinisis_code,
                         '' as aade_skopos_19_descr,
    gks_users.ma_branch as ma_branch_user,
    gks_users.order_sxolio,
    gks_users.pelati_sxolio,
    gks_users.gemi_number,
    gks_users.is_b2g,
    gks_users.b2g_aaht_code,
    gks_users.b2g_aaht_name,
    gks_users.b2g_aaht_foreas,
    gks_users.b2g_aaht_typos_forea,
    gks_users.b2g_aaht_kodikos_ekatharisis,
    gks_users.afm,
    party_country.country_initials as party_country_initials,
    party_country.country_name as party_country_name,
    party_country.country_ee as party_country_ee,
    party_nomoi.nomos_descr as party_nomos_descr,
    gks_eshop_pricelist.pricelist_descr, 
    gks_eshop_fiscal_position.fiscal_position_descr,
                    '' as payment_acquirer_name,
                        0 as aade_tropos_pliromis_code,
                   '' as delivery_method_name
    
    FROM ((((((((((((((((gks_acc_pay
    
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_pay.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_pay.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
    LEFT JOIN gks_company on gks_acc_pay.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_pay.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_nomoi as company_nomoi ON gks_company.company_nomos_id = company_nomoi.id_nomos)
    LEFT JOIN gks_country as company_country ON gks_company.company_country_id = company_country.id_country)
    LEFT JOIN gks_nomoi as company_sub_nomoi ON gks_company_subs.company_sub_nomos_id = company_sub_nomoi.id_nomos)
    LEFT JOIN gks_country as company_sub_country ON gks_company_subs.company_sub_country_id = company_sub_country.id_country)
    LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira)
   
  
    LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
    LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
    LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist)
    LEFT JOIN gks_country as party_country ON gks_users.ma_country_id = party_country.id_country)
  
    LEFT JOIN gks_nomoi as party_nomoi ON gks_users.ma_nomos_id = party_nomoi.id_nomos)
  
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    
    where gks_acc_pay.id_acc_pay=".$id;
    
  } else if ($doc_table=='gks_whi_mov') {
    $sql="SELECT gks_whi_mov.*, 
    ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
    ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
    gks_company.company_title, 
    gks_company.company_eponimia,
    gks_company.company_afm,
    gks_company.company_doy,
    gks_company.aade_branch,
    gks_company.company_odos,
    gks_company.company_arithmos,
    gks_company.company_orofos,
    gks_company.company_perioxi,
    gks_company.company_poli,
    gks_company.company_tk,
    company_nomoi.nomos_descr as company_nomos_descr,
    company_country.country_initials as company_country_initials,
    company_country.country_name as company_country_name,
    company_country.country_ee as company_country_ee,
    gks_company.company_email,
    gks_company.company_phone,
    gks_company.company_gemi_number,
        
    gks_company_subs.company_sub_title,
    gks_company_subs.company_sub_odos,
    gks_company_subs.company_sub_arithmos,
    gks_company_subs.company_sub_orofos,
    gks_company_subs.company_sub_perioxi,
    gks_company_subs.company_sub_poli,
    gks_company_subs.company_sub_tk,
    company_sub_nomoi.nomos_descr as company_sub_nomos_descr,
    company_sub_country.country_initials as company_sub_country_initials,
    company_sub_country.country_name as company_sub_country_name,
    company_sub_country.country_ee as company_sub_country_ee,
    gks_company_subs.company_sub_email,
    gks_company_subs.company_sub_phone,
        
    gks_acc_journal.acc_journal_code,
    gks_acc_journal.acc_journal_descr,
    gks_acc_journal.acc_eidos_parastatikou_whi_id, 
    gks_acc_journal.acc_eidos_parastatikou_other_entity,
    gks_acc_seires.seira_code, 
    gks_acc_seires.seira_descr, 
    gks_acc_seires.seira_isdeliverynote,
    gks_acc_seires.seira_is_reverse_delivery_note,
    0 as seira_is_self_pricing,
    0 as seira_is_vat_payment_suspension,
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
    gks_acc_eidi_parastatikon.peppol_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_posotita,
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
    gks_aade_skopos_diakinisis.aade_skopos_diakinisis_code,
    gks_users.ma_branch as ma_branch_user,
    gks_users.order_sxolio,
    gks_users.pelati_sxolio,
    gks_users.gemi_number,
    gks_users.is_b2g,
    gks_users.b2g_aaht_code,
    gks_users.b2g_aaht_name,
    gks_users.b2g_aaht_foreas,
    gks_users.b2g_aaht_typos_forea,
    gks_users.b2g_aaht_kodikos_ekatharisis,    
    party_country.country_initials as party_country_initials,
    party_country.country_name as party_country_name,
    party_country.country_ee as party_country_ee,
    party_nomoi.nomos_descr as party_nomos_descr,
    gks_eshop_pricelist.pricelist_descr, 
    gks_eshop_fiscal_position.fiscal_position_descr,
                    '' as payment_acquirer_name,
                        0 as aade_tropos_pliromis_code,
    gks_delivery_methods.delivery_method_name
    
    FROM ((((((((((((((((((gks_whi_mov
    
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_whi_mov.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_whi_mov.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
    LEFT JOIN gks_company on gks_whi_mov.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_whi_mov.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_nomoi as company_nomoi ON gks_company.company_nomos_id = company_nomoi.id_nomos)
    LEFT JOIN gks_country as company_country ON gks_company.company_country_id = company_country.id_country)
    LEFT JOIN gks_nomoi as company_sub_nomoi ON gks_company_subs.company_sub_nomos_id = company_sub_nomoi.id_nomos)
    LEFT JOIN gks_country as company_sub_country ON gks_company_subs.company_sub_country_id = company_sub_country.id_country)
    LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_delivery_methods ON gks_whi_mov.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
    LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
    LEFT JOIN gks_eshop_fiscal_position ON gks_whi_mov.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
    LEFT JOIN gks_eshop_pricelist ON gks_whi_mov.pricelist_id = gks_eshop_pricelist.id_pricelist)
    LEFT JOIN gks_country as party_country ON gks_whi_mov.ma_country_id = party_country.id_country)
    LEFT JOIN gks_nomoi as party_nomoi ON gks_whi_mov.ma_nomos_id = party_nomoi.id_nomos)
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_aade_skopos_diakinisis ON gks_whi_mov.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis
    
    where gks_whi_mov.id_whi_mov=".$id;
        
  }
      
  //print '<pre>nnnnnnnn '.$sql;die();
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {
    $ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό');
    debug_mail(false,$ret['message'],$sql); return $ret;}
    
  $row = $result->fetch_assoc();
  $rrr_seira_id=intval($row[$rrr.'_seira_id']);
  $eidos_parastatikou_has_posotita=$row['eidos_parastatikou_has_posotita'];
  $eidos_parastatikou_need_afm=intval($row['eidos_parastatikou_need_afm']);
  $credit_memo_for_ttt_id=intval($row['credit_memo_for_'.$ttt.'_id']);
  if ($doc_table=='gks_acc_inv') $dimotikos_foros_for_ttt_id=intval($row['dimotikos_foros_for_'.$ttt.'_id']);
  $eidos_parastatikou_aade_code=trim_gks($row['eidos_parastatikou_aade_code']);
  $acc_eidos_parastatikou_whi_id=intval($row['acc_eidos_parastatikou_whi_id']);
  $seira_isdeliverynote=intval($row['seira_isdeliverynote']);
  $seira_is_reverse_delivery_note=intval($row['seira_is_reverse_delivery_note']);
  $reverse_delivery_purpose=0; 
  if (isset($row['reverse_delivery_purpose'])) $reverse_delivery_purpose=intval($row['reverse_delivery_purpose']);
  $seira_is_self_pricing=intval($row['seira_is_self_pricing']);
  $seira_is_vat_payment_suspension=intval($row['seira_is_vat_payment_suspension']);
      
  $aade_skopos_diakinisis_code=intval($row['aade_skopos_diakinisis_code']);
  $aade_skopos_19_descr=trim_gks($row['aade_skopos_19_descr']);
  
  $acc_eidos_parastatikou_other_entity=intval($row['acc_eidos_parastatikou_other_entity']);
  
  $afm=trim_gks($row['afm']);
  
  
  //echo '<pre>dddddddddddd '.$acc_eidos_parastatikou_other_entity;die();
    
  $correlatedInvoices='';
  
  if ($eidos_parastatikou_aade_code=='5.1') { //Pistotiko Timologio / Syschetizomeno
    if ($credit_memo_for_ttt_id<=0) {
      debug_mail(false,'error Pistotiko Timologio / Syschetizomeno',
       'eidos_parastatikou_aade_code: '.$eidos_parastatikou_aade_code.'<br>credit_memo_for_ttt_id: '.$credit_memo_for_ttt_id);
      $ret['message']=gks_lang('Δεν έχει ορισθεί το συσχετιζόμενο παραστατικό για αυτό το πιστωτικό παραστατικό'); return $ret;
    }
    //echo '<pre>sssssss'; die();
    
    $sql_credit_memo="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
    gks_acc_inv.aade_invoicemark
    FROM (((gks_acc_inv 
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
    where gks_acc_inv.id_acc_inv=".$credit_memo_for_ttt_id;
    $result_credit_memo = $db_link->query($sql_credit_memo);        
    if (!$result_credit_memo) {debug_mail(false,'error sql',$sql_credit_memo);die('sql error');}
    if ($result_credit_memo->num_rows==0) {
      $credit_memo_descr_for=gks_lang('Δεν βρέθηκε το συσχετιζόμενο παραστατικό με ID').': '.
      '<a href="admin-acc-inv-item.php?id='.$credit_memo_for_ttt_id.'" class="gks_link">'.$credit_memo_for_ttt_id.'</a>';
      debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not found',$credit_memo_descr_for.' '.$sql_credit_memo);
      $ret['message']=$credit_memo_descr_for; return $ret;

      //die('no record found (2)');
    } else {
      $row_credit_memo = $result_credit_memo->fetch_assoc();
    
      //$antisimvalomenos_label=$row_credit_memo['antisimvalomenos_label'];
      //$acc_eidos_parastatikou_id=intval($row_credit_memo['acc_eidos_parastatikou_id']);
      //$eidos_parastatikou_type_id=intval($row_credit_memo['eidos_parastatikou_type_id']);
      //$eidos_parastatikou_need_prev=intval($row_credit_memo['eidos_parastatikou_need_prev']);
      //$eidos_parastatikou_has_fpa=intval($row_credit_memo['eidos_parastatikou_has_fpa']);
      //$eidos_parastatikou_has_othertaxes=trim_gks($row_credit_memo['eidos_parastatikou_has_othertaxes']);
      //$eidos_parastatikou_has_esoda=intval($row_credit_memo['eidos_parastatikou_has_esoda']);
      //$eidos_parastatikou_has_eksoda=intval($row_credit_memo['eidos_parastatikou_has_eksoda']);
      $eidos_parastatikou_need_afm=intval($row_credit_memo['eidos_parastatikou_need_afm']);
      
      $aade_invoicemark=trim_gks($row_credit_memo['aade_invoicemark']);
      if ($aade_invoicemark=='') {
        $credit_memo_descr_for=gks_lang('Το συσχετιζόμενο παραστατικό με ID').': '.
        '<a href="admin-acc-inv-item.php?id='.$credit_memo_for_ttt_id.'" class="gks_link">'.$credit_memo_for_ttt_id.'</a><br>'.
        gks_lang('δεν έχει ΜΑΡΚ<br>Σίγουρα έχει σταλεί στην ΑΑΔΕ ;');
        debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not mark',$credit_memo_descr_for.' '.$sql_credit_memo);
        $ret['message']=$credit_memo_descr_for; return $ret;
      }
      
      
      $correlatedInvoices=trim_gks($aade_invoicemark);
      
      $sql_corri="SELECT gks_".$ttt."_correlated_invoices.coi_mark, gks_".$ttt.".aade_invoicemark
      FROM gks_".$ttt."_correlated_invoices 
      LEFT JOIN gks_".$ttt." ON gks_".$ttt."_correlated_invoices.coi_".$ttt."_id = gks_".$ttt.".id_".$ttt."
      where gks_".$ttt."_correlated_invoices.".$ttt."_id=".$id."
      ORDER BY gks_".$ttt."_correlated_invoices.coi_aa;";
      $result_corri = $db_link->query($sql_corri);        
      if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
      $coi_mark=[];
      while ($row_corri = $result_corri->fetch_assoc()) {
        $vvv=trim_gks($row_corri['coi_mark']);
        if ($vvv=='') $vvv=trim_gks($row_corri['aade_invoicemark']);
        if ($vvv!='') $coi_mark[]=$vvv;
      }
      $coi_mark=implode('|',$coi_mark);
      if ($coi_mark!=$correlatedInvoices) {
        $ret['message']=gks_lang('Δεν βρέθηκε το ΜΑΡΚ <br><b>[1]</b><br>στα <b>Συσχετιζόμενα Παραστατικά</b>.<br>Βρέθηκαν τα: <br><b>[2]</b>');
        $ret['message']=str_replace('[1]',$correlatedInvoices,$ret['message']);
        $ret['message']=str_replace('[2]',$coi_mark,$ret['message']);
        debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not mark',$ret['message']);
        return $ret;}
      //echo '<pre>sssssss'; die();
      
    }    
    
  } else if ($eidos_parastatikou_aade_code=='8.2') { //Eidiko Stoicheio - Apodeixis Eispraxis Forou Diamonis
    if ($dimotikos_foros_for_ttt_id<=0) {
      debug_mail(false,'error Apodeixis Eispraxis Forou Diamonis',
       'eidos_parastatikou_aade_code: '.$eidos_parastatikou_aade_code.'<br>dimotikos_foros_for_ttt_id: '.$dimotikos_foros_for_ttt_id);
      $ret['message']=gks_lang('Δεν έχει ορισθεί το συσχετιζόμενο παραστατικό για αυτό το παραστατικό'); return $ret;
    }
    
    $sql_dimotikos_foros="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
    gks_acc_inv.aade_invoicemark
    FROM (((gks_acc_inv 
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
    where gks_acc_inv.id_acc_inv=".$dimotikos_foros_for_ttt_id;
    $result_dimotikos_foros = $db_link->query($sql_dimotikos_foros);        
    if (!$result_dimotikos_foros) {debug_mail(false,'error sql',$sql_dimotikos_foros);die('sql error');}
    if ($result_dimotikos_foros->num_rows==0) {
      $dimotikos_foros_descr_for=gks_lang('Δεν βρέθηκε το συσχετιζόμενο παραστατικό με ID').': '.
      '<a href="admin-acc-inv-item.php?id='.$dimotikos_foros_for_ttt_id.'" class="gks_link">'.$dimotikos_foros_for_ttt_id.'</a>';
      debug_mail(false,'error Apodeixis Eispraxis Forou Diamonis, record parent not found',$dimotikos_foros_descr_for.' '.$sql_dimotikos_foros);
      $ret['message']=$dimotikos_foros_descr_for; return $ret;

      //die('no record found (2)');
    } else {
      $row_dimotikos_foros = $result_dimotikos_foros->fetch_assoc();
    
      //$antisimvalomenos_label=$row_dimotikos_foros['antisimvalomenos_label'];
      //$acc_eidos_parastatikou_id=intval($row_dimotikos_foros['acc_eidos_parastatikou_id']);
      //$eidos_parastatikou_type_id=intval($row_dimotikos_foros['eidos_parastatikou_type_id']);
      //$eidos_parastatikou_need_prev=intval($row_dimotikos_foros['eidos_parastatikou_need_prev']);
      //$eidos_parastatikou_has_fpa=intval($row_dimotikos_foros['eidos_parastatikou_has_fpa']);
      //$eidos_parastatikou_has_othertaxes=trim_gks($row_dimotikos_foros['eidos_parastatikou_has_othertaxes']);
      //$eidos_parastatikou_has_esoda=intval($row_dimotikos_foros['eidos_parastatikou_has_esoda']);
      //$eidos_parastatikou_has_eksoda=intval($row_dimotikos_foros['eidos_parastatikou_has_eksoda']);
      //$eidos_parastatikou_need_afm=intval($row_dimotikos_foros['eidos_parastatikou_need_afm']);
      
      $aade_invoicemark=trim_gks($row_dimotikos_foros['aade_invoicemark']);
      if ($aade_invoicemark=='') {
        $dimotikos_foros_descr_for=gks_lang('Το συσχετιζόμενο παραστατικό με ID').': '.
        '<a href="admin-acc-inv-item.php?id='.$dimotikos_foros_for_ttt_id.'" class="gks_link">'.$dimotikos_foros_for_ttt_id.'</a><br>'.
        gks_lang('δεν έχει ΜΑΡΚ<br>Σίγουρα έχει σταλεί στην ΑΑΔΕ ;');
        debug_mail(false,'error Apodeixis Eispraxis Forou Diamonis, record parent not mark',$dimotikos_foros_descr_for.' '.$sql_dimotikos_foros);
        $ret['message']=$dimotikos_foros_descr_for; return $ret;
      }
      
      
      $correlatedInvoices=trim_gks($aade_invoicemark);
      
      $sql_corri="SELECT gks_".$ttt."_correlated_invoices.coi_mark, 
      gks_".$ttt.".aade_invoicemark
      FROM gks_".$ttt."_correlated_invoices 
      LEFT JOIN gks_".$ttt." ON gks_".$ttt."_correlated_invoices.coi_".$ttt."_id = gks_".$ttt.".id_".$ttt."
      where gks_".$ttt."_correlated_invoices.".$ttt."_id=".$id."
      ORDER BY gks_".$ttt."_correlated_invoices.coi_aa;";
      $result_corri = $db_link->query($sql_corri);        
      if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
      $coi_mark=[];
      while ($row_corri = $result_corri->fetch_assoc()) {
        $vvv=trim_gks($row_corri['coi_mark']);
        if ($vvv=='') $vvv=trim_gks($row_corri['aade_invoicemark']);
        if ($vvv!='') $coi_mark[]=$vvv;
      }
      $coi_mark=implode('|',$coi_mark);
      if ($coi_mark!=$correlatedInvoices) {
        $ret['message']=gks_lang('Δεν βρέθηκε το ΜΑΡΚ <br><b>[1]</b><br>στα <b>Συσχετιζόμενα Παραστατικά</b>.<br>Βρέθηκαν τα: <br><b>[2]</b>');
        $ret['message']=str_replace('[1]',$correlatedInvoices,$ret['message']);
        $ret['message']=str_replace('[2]',$coi_mark,$ret['message']);
        debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not mark',$ret['message']);
        return $ret;}
      //echo '<pre>sssssss'; die();
            
    }    
    
  } else {
    
    //echo '<pre>ggggggggggggggggggg'; die();
      
    $sql_corri="SELECT gks_".$ttt."_correlated_invoices.coi_mark, gks_".$ttt.".aade_invoicemark
    FROM gks_".$ttt."_correlated_invoices 
    LEFT JOIN gks_".$ttt." ON gks_".$ttt."_correlated_invoices.coi_".$ttt."_id = gks_".$ttt.".id_".$ttt."
    where gks_".$ttt."_correlated_invoices.".$ttt."_id=".$id."
    ORDER BY gks_".$ttt."_correlated_invoices.coi_aa;";
    $result_corri = $db_link->query($sql_corri);        
    if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
    $coi_mark=[];
    while ($row_corri = $result_corri->fetch_assoc()) {
      $vvv=trim_gks($row_corri['coi_mark']);
      if ($vvv=='') $vvv=trim_gks($row_corri['aade_invoicemark']);
      if ($vvv!='') $coi_mark[]=$vvv;
    }
    
    $correlatedInvoices=implode('|',$coi_mark);
  }
  
  

  $sql_corri="SELECT gks_".$ttt."_multiple_connected_marks.mcm_mark, 
  gks_".$ttt.".aade_invoicemark
  FROM gks_".$ttt."_multiple_connected_marks 
  LEFT JOIN gks_".$ttt." ON gks_".$ttt."_multiple_connected_marks.mcm_".$ttt."_id = gks_".$ttt.".id_".$ttt."
  where gks_".$ttt."_multiple_connected_marks.".$ttt."_id=".$id."
  ORDER BY gks_".$ttt."_multiple_connected_marks.mcm_aa;";
  $result_corri = $db_link->query($sql_corri);        
  if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
  $mcm_mark=[];
  while ($row_corri = $result_corri->fetch_assoc()) {
    $vvv=trim_gks($row_corri['mcm_mark']);
    if ($vvv=='') $vvv=trim_gks($row_corri['aade_invoicemark']);
    if ($vvv!='') $mcm_mark[]=$vvv;
  }
  $multipleConnectedMarks=implode('|',$mcm_mark);
  //echo '<pre>'.$multipleConnectedMarks;die();
  
  
  
  $isDeliveryNote=false;
  if (in_array($eidos_parastatikou_aade_code,['9.1','9.2','9.3'])) {
    $isDeliveryNote=true;
  } else if ($acc_eidos_parastatikou_whi_id>0) {
    if ($seira_isdeliverynote!=0) $isDeliveryNote=true;
  }
  $reverseDeliveryNote=false;
  $reverseDeliveryNotePurpose=0;
  if (in_array($eidos_parastatikou_aade_code,['9.3'])) {
    if ($seira_is_reverse_delivery_note!=0) {
      $reverseDeliveryNote=true;
      $reverseDeliveryNotePurpose=$reverse_delivery_purpose;
    }
  }
  
  
  $is_endodiakinisi=false;
  if ($isDeliveryNote and 
        in_array($eidos_parastatikou_aade_code,['9.1','9.2','9.3','10.1','10.2']) and 
        $afm=='' and 
        in_array($aade_skopos_diakinisis_code,[8,18])) { //8->metaxy Enkatastaseon Ontotitas, 18=>diakinisi Pagion (Endodiakinisi)
    $is_endodiakinisi=true;
  }
  
  
  //echo '<pre>ggggg ';print_r($row);die();  
    
  $xml = new gks_aade_SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?>'.
  '<InvoicesDoc xmlns="http://www.aade.gr/myDATA/invoice/v1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
			'xsi:schemaLocation="http://www.aade.gr/myDATA/invoice/v1.0/InvoicesDoc-v1.0.9.xsd"	'.
			'xmlns:icls="https://www.aade.gr/myDATA/incomeClassificaton/v1.0" '.
			'xmlns:ecls="https://www.aade.gr/myDATA/expensesClassificaton/v1.0"/>'
  );
  
  //$xml->formatOutput = true;
  
  $NS = array( 
  //  'xsi'  => 'http://www.w3.org/2001/XMLSchema-instance',
    'icls' => 'https://www.aade.gr/myDATA/incomeClassificaton/v1.0',
    'ecls' => 'https://www.aade.gr/myDATA/expensesClassificaton/v1.0',
  ); 
  //$xml->registerXPathNamespace('icls', $NS['xsi']);
  $xml->registerXPathNamespace('icls', $NS['icls']);
  $xml->registerXPathNamespace('ecls', $NS['ecls']);
  
  $invoice = $xml->addChild('invoice');
  
  //echo '<pre>ddddddddddd'; die();
  

  $issuer = $invoice->addChild('issuer'); //PartyType
  
    $company_afm=trim_gks($row['company_afm']);
    if ($call_from_paroxos==false and isset($aade_params['force_afm']) and $aade_params['force_afm']!='') {
      $company_afm=$aade_params['force_afm'];
    }
    //print '<pre>';
    //print_r($aade_params);
    //die();
    
    
    if ($company_afm=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί το ΑΦΜ της εταιρείας σας'); debug_mail(false,$ret['message'],''); return $ret;}
    $issuer->addChild('vatNumber',$company_afm);
    $uid_array['vatNumber']=$company_afm;
    
    $company_country_initials=trim_gks($row['company_country_initials']);
    if ($company_country_initials=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί ο κωδικός χώρας της εταιρείας σας'); debug_mail(false,$ret['message'],''); return $ret;}
    $issuer->addChild('country',$company_country_initials);
    
    if ($call_from_paroxos==false) {
      if ($aade_params['branch'] < 0) {$ret['message']=gks_lang('Ορίστε τον Αριθμό Εγκατάστασης της εταιρείας σας'); debug_mail(false,$ret['message'],''); return $ret;}
      $issuer->addChild('branch',$aade_params['branch']);
      $uid_array['branch']=$aade_params['branch'];
    } else {
      if (isset($aade_params['paroxos_params']['paroxos_branch'])) {
        $issuer->addChild('branch',$aade_params['paroxos_params']['paroxos_branch']);
        $uid_array['branch']=$aade_params['paroxos_params']['paroxos_branch'];        
      }
      //print '<pre>gggggggggg';print_r($aade_params);die();
      
    }
    
    if ($company_country_initials!='GR' or $isDeliveryNote) { //check
    //if ($company_country_initials!='GR') {
      $company_eponimia=trim_gks($row['company_eponimia']);
      if($company_eponimia != '') $issuer->addChild('name',$company_eponimia);

    
      $address=$issuer->addChild('address'); //AddressType
      if ($row['company_sub_id']==0) { //kentriko
        $company_odos=trim_gks($row['company_odos']);
        if ($company_odos!='') $address->addChild('street',$company_odos);
        $company_arithmos=trim_gks($row['company_arithmos']);
        if ($company_arithmos!='') $address->addChild('number',$company_arithmos);
        $company_tk=trim_gks($row['company_tk']);
        if ($company_tk!='') $address->addChild('postalCode',$company_tk);
        $company_poli=trim_gks($row['company_poli']);
        if ($company_poli!='') $address->addChild('city',$company_poli);
      } else { //ypokatastima
        $company_sub_odos=trim_gks($row['company_sub_odos']);
        if ($company_sub_odos!='') $address->addChild('street',$company_sub_odos);
        $company_sub_arithmos=trim_gks($row['company_sub_arithmos']);
        if ($company_sub_arithmos!='') $address->addChild('number',$company_sub_arithmos);
        $company_sub_tk=trim_gks($row['company_sub_tk']);
        if ($company_sub_tk!='') $address->addChild('postalCode',$company_sub_tk);
        $company_sub_poli=trim_gks($row['company_sub_poli']);
        if ($company_sub_poli!='') $address->addChild('city',$company_sub_poli);
      }
    }
  
  
  //echo '<pre>mmmmmmmmmmmmm '.$eidos_parastatikou_aade_code.'|'.$isDeliveryNote.'|'; die();
  
  //echo '<pre>mmmmmmmmmmmmm ';print_r($row); die();
  
  //if ($eidos_parastatikou_need_afm!=0) {
  if ($eidos_parastatikou_need_afm!=0 or 
      ($eidos_parastatikou_aade_code=='11.1' and $isDeliveryNote)) {
        
    $counterpart = $invoice->addChild('counterpart'); //PartyType
  
    
    $party_country_initials=trim_gks($row['party_country_initials']);
    
    if ($is_endodiakinisi) {
      //echo '<pre>dddd';die();
      $counterpart->addChild('vatNumber','000000000');
      //$counterpart->addChild('vatNumber',$company_afm);
      
      if (isset($row['deli_country_id']) and intval($row['deli_country_id'])>0) {
        $sql_temp="select country_initials from gks_country where id_country=".intval($row['deli_country_id']);
        $result_temp = $db_link->query($sql_temp);        
        if (!$result_temp) {debug_mail(false,'error sql',$sql_temp);$ret['message']='sql error'; return $ret;}
        if ($result_temp->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η χώρα με κωδικό').' '.intval($row['deli_country_id']);debug_mail(false,$ret['message'],$sql_temp); return $ret;}
        $row_temp = $result_temp->fetch_assoc();
        $party_country_initials=$row_temp['country_initials'];
        //echo '<pre>dddd ssss'.$party_country_initials;die();
      }
      
//      if (isset($row['deli_odos'])) and trim_gks($row['deli_odos'])!='') $row['ma_odos']=$row['deli_odos'];
//      if (isset($row['deli_arithmos'])) and trim_gks($row['deli_arithmos'])!='') $row['ma_arithmos']=$row['deli_arithmos'];
//      if (isset($row['deli_tk'])) and trim_gks($row['deli_tk'])!='') $row['ma_tk']=$row['deli_tk'];
//      if (isset($row['deli_poli'])) and trim_gks($row['deli_poli'])!='') $row['ma_poli']=$row['deli_poli'];

      
      
    } else {
      if ($afm=='' and in_array($eidos_parastatikou_aade_code,['8.2','11.1'])==false) {
        $ret['message']=gks_lang('Δεν έχει ορισθεί το ΑΦΜ του πελάτη/προμηθευτή'); 
        debug_mail(false,$ret['message'],''); 
        return $ret;
      }
      if ($eidos_parastatikou_aade_code=='11.1') {
        $counterpart->addChild('vatNumber','000000000');
      } else if ($eidos_parastatikou_aade_code=='8.2' and $afm=='') {
        $counterpart->addChild('vatNumber','000000000');
      } else {
        $counterpart->addChild('vatNumber',$afm);
      }
    }
  

    if ($party_country_initials=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί ο κωδικός χώρας του πελάτη/προμηθευτή'); debug_mail(false,$ret['message'],''); return $ret;}
    $counterpart->addChild('country',$party_country_initials);
  
    $ma_branch=0;
    if (isset($row['ma_branch_user'])) {
      if (trim_gks($row['ma_branch_user'])!='') {
        $ma_branch=intval($row['ma_branch_user']);
        //echo '<pre>'.$ma_branch;die();
      }
    }
    if ($is_endodiakinisi) {
      if (isset($row['deli_branch'])) {
        if (trim_gks($row['deli_branch'])!='') {
          $ma_branch=intval($row['deli_branch']);
        }
      }
    }
    //echo '<pre>|'.$ma_branch.'|';die();
    
    
    $counterpart->addChild('branch',$ma_branch);
    
    if ($is_endodiakinisi) {

      if($company_eponimia != '') $counterpart->addChild('name',$company_eponimia);
      
      
      $address=$counterpart->addChild('address'); //AddressType
      $ma_odos=trim_gks($row['deli_odos']);
      if ($ma_odos!='') $address->addChild('street',$ma_odos);
      $ma_arithmos=trim_gks($row['deli_arithmos']);
      if ($ma_arithmos!='') $address->addChild('number',$ma_arithmos);
      $ma_tk=trim_gks($row['deli_tk']);
      if ($ma_tk!='') $address->addChild('postalCode',$ma_tk);
      $ma_poli=trim_gks($row['deli_poli']);
      if ($ma_poli!='') $address->addChild('city',$ma_poli);
      
    } else {
    
      if ($party_country_initials!='GR' or $isDeliveryNote) { //check
      //if ($party_country_initials!='GR') {
        $eponimia=trim_gks($row['eponimia']);
        
        if($eponimia != '') {
          $counterpart->addChild('name',$eponimia);
        } else if ($eidos_parastatikou_aade_code=='11.1') {
          $pelatis_name=trim_gks(trim_gks($row['user_first_name']).' '.trim_gks($row['user_last_name']));
          $counterpart->addChild('name',$pelatis_name);
        }
        
      }
      
      $address=$counterpart->addChild('address'); //AddressType
      $ma_odos=trim_gks($row['ma_odos']);
      if ($ma_odos!='') $address->addChild('street',$ma_odos);
      $ma_arithmos=trim_gks($row['ma_arithmos']);
      if ($ma_arithmos!='') $address->addChild('number',$ma_arithmos);
      $ma_tk=trim_gks($row['ma_tk']);
      if ($ma_tk!='') $address->addChild('postalCode',$ma_tk);
      $ma_poli=trim_gks($row['ma_poli']);
      if ($ma_poli!='') $address->addChild('city',$ma_poli);
    }
    
  }

  $invoiceHeader = $invoice->addChild('invoiceHeader'); //InvoiceHeaderType


  $rrr_seira_code=trim_gks($row[$rrr.'_seira_code']);
  if ($rrr_seira_code=='') {$ret['message']=gks_lang('Δεν βρέθηκε η σειρά'); debug_mail(false,$ret['message'],''); return $ret;}
  $invoiceHeader->addChild('series',$rrr_seira_code);
  $uid_array['series']=$rrr_seira_code;
  
  $rrr_acc_number_int=intval($row[$rrr.'_number_int']);
  if ($rrr_acc_number_int<=0) {$ret['message']=gks_lang('Δεν βρέθηκε ο αριθμός του παραστατικού'); debug_mail(false,$ret['message'],''); return $ret;}
  $invoiceHeader->addChild('aa',$rrr_acc_number_int);
  $uid_array['aa']=$rrr_acc_number_int;
  
  $xxx_date=trim_gks($row[$xxx.'_date']);
  if ($xxx_date=='') {$ret['message']=gks_lang('Δεν ορίσθηκε η ημερομηνία'); debug_mail(false,$ret['message'],''); return $ret;}
  $xxx_date_str=showDate(strtotime($xxx_date),'Y-m-d',1); // H:i:s
  $invoiceHeader->addChild('issueDate',$xxx_date_str);
  $uid_array['issueDate']=$xxx_date_str;
  
  $eidos_parastatikou_aade_code=trim_gks($row['eidos_parastatikou_aade_code']);
  if ($eidos_parastatikou_aade_code=='') {$ret['message']=gks_lang('Δεν βρέθηκε ο κωδικός ΑΑΔΕ για το παραστατικό'); debug_mail(false,$ret['message'],''); return $ret;}
  $invoiceHeader->addChild('invoiceType',$eidos_parastatikou_aade_code);
  $uid_array['invoiceType']=$eidos_parastatikou_aade_code;
  
  //vatPaymentSuspension

  if ($seira_is_vat_payment_suspension==1) {
    $invoiceHeader->addChild('vatPaymentSuspension',true);
    
  }

  if (in_array($doc_table,['gks_acc_inv','gks_acc_pay'])) {
    $invoiceHeader->addChild('currency','EUR');
  }
  

  
  
  //exchangeRate
  //correlatedInvoices
  
  if ($correlatedInvoices!='') {
    $vvv_parts=explode('|',$correlatedInvoices);
    foreach ($vvv_parts as $vvv_item) {
      $invoiceHeader->addChild('correlatedInvoices', $vvv_item);
    } 
  }  
  
  
  
  //selfPricing
  
  if ($seira_is_self_pricing==1) {
    $invoiceHeader->addChild('selfPricing',true);
  }
  
  
  if (in_array($doc_table,['gks_acc_inv','gks_whi_mov'])) {
    $dispatch_date=trim_gks($row['dispatch_date']);
    if ($dispatch_date!='') {
      $invoiceHeader->addChild('dispatchDate',showDate(strtotime($dispatch_date),'Y-m-d',0));
    }
    $dispatch_time=trim_gks($row['dispatch_time']);
    if ($dispatch_time!='') {
      $invoiceHeader->addChild('dispatchTime',$dispatch_time);
    }
  
    
  
    $vehicle_number=trim_gks($row['vehicle_number']);
    if ($vehicle_number!='') $invoiceHeader->addChild('vehicleNumber',$vehicle_number);
    
    
    //if ($aade_skopos_diakinisis_code>=1 and $aade_skopos_diakinisis_code<=8) 
    if ($aade_skopos_diakinisis_code>=1) {
      $invoiceHeader->addChild('movePurpose', $aade_skopos_diakinisis_code);
    }
  
  }
  
  
  if ($acc_eidos_parastatikou_other_entity==1) {
    $sql_temp="SELECT ".$doc_table."_other_entity.*, 
    gks_aade_entitytype.aade_entitytype_code, gks_aade_entitytype.aade_entitytype_descr, 
    gks_country.country_initials, gks_country.country_name
    FROM (".$doc_table."_other_entity 
    LEFT JOIN gks_aade_entitytype ON ".$doc_table."_other_entity.aade_entitytype_id = gks_aade_entitytype.id_aade_entitytype) 
    LEFT JOIN gks_country ON ".$doc_table."_other_entity.entity_country_id = gks_country.id_country
    WHERE ".$doc_table."_other_entity.".$ttt."_id=".$id."
    ORDER BY entity_aa;";
    //echo '<pre>ssss '.$sql_temp;die();
    $result_temp = $db_link->query($sql_temp);        
    if (!$result_temp) {debug_mail(false,'error sql',$sql_temp);$ret['message']='sql error'; return $ret;}
    
    $other_entity_array=array();
    $other_entity_cc=0;
    while ($row_temp = $result_temp->fetch_assoc()) {
      $other_entity_cc++;

      $aade_entitytype_code=intval($row_temp['aade_entitytype_code']);
      if ($aade_entitytype_code<1 or $aade_entitytype_code>6) {
        $ret['message']=gks_lang('Δεν βρέθηκε ο κωδικός του τύπου στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_afm=trim_gks($row_temp['entity_afm']);
      if ($entity_afm=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει ΑΦΜ');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}

      $country_initials=trim_gks($row_temp['country_initials']);
      if ($country_initials=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Χώρα');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}

      $entity_branch=trim_gks($row_temp['entity_branch']);
      if ($entity_branch=='' or intval($entity_branch)!=$entity_branch) {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Αριθμό Εγκατάστασης');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_name=trim_gks($row_temp['entity_name']);
      if ($entity_name=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Επωνυμία');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_odos=trim_gks($row_temp['entity_odos']);
      if ($entity_odos=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Οδό');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_arithmos=trim_gks($row_temp['entity_arithmos']);
      if ($entity_arithmos=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Αριθμό Οδού');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_tk=trim_gks($row_temp['entity_tk']);
      if ($entity_tk=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει TK');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_poli=trim_gks($row_temp['entity_poli']);
      if ($entity_poli=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Πόλη');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      
        
      $other_entity_array[]=array(
        'type' => $aade_entitytype_code,
        'entityData' => array(
          'vatNumber' => $entity_afm,
          'country' => $country_initials,
          'branch' => $entity_branch,
          'name' => $entity_name,
          'address' => array(
            'street' => $entity_odos,
            'number' => $entity_arithmos,
            'postalCode' => $entity_tk, 
            'city' => $entity_poli,
          ),
        ),
      );
    }
    
    if (count($other_entity_array)>0) {
      
      foreach ($other_entity_array as $oe_item) {
        
        //debug 
        //$oe_item['type']=7;
        //$oe_item['entityData']['vatNumber']='';
        //$oe_item['entityData']['country']='';
        //$oe_item['entityData']['branch']='';
        //$oe_item['entityData']['name']='';
        //$oe_item['entityData']['address']['street']='';
        //$oe_item['entityData']['address']['number']='';
        //$oe_item['entityData']['address']['postalCode']='';
        //$oe_item['entityData']['address']['city']='';

        $oe_item_xml=$invoiceHeader->addChild('otherCorrelatedEntities');
        $oe_item_xml->addChild('type',$oe_item['type']);
        
        $entityData=$oe_item_xml->addChild('entityData');
        $entityData->addChild('vatNumber',$oe_item['entityData']['vatNumber']);
        $entityData->addChild('country',$oe_item['entityData']['country']);
        $entityData->addChild('branch',$oe_item['entityData']['branch']);
        $entityData->addChild('name',$oe_item['entityData']['name']);
        $address_ed=$entityData->addChild('address');
        
        $address_ed->addChild('street',$oe_item['entityData']['address']['street']);
        $address_ed->addChild('number',$oe_item['entityData']['address']['number']);
        $address_ed->addChild('postalCode',$oe_item['entityData']['address']['postalCode']);
        $address_ed->addChild('city',$oe_item['entityData']['address']['city']);
      } 
    }
    //echo '<pre>ddddddddddddd ';var_dump($invoiceHeader);die();
  }
  //echo '<pre>ddddddddddddd ';print_r($other_entity_array);die();
  
  

  

  if ($isDeliveryNote) {
//    otherDeliveryNoteHeader
//    loadingAddress
//    deliveryAddress
//    startShippingBranch
//    completeShippingBranch
    
    
    $otherDeliveryNoteHeader=$invoiceHeader->addChild('otherDeliveryNoteHeader');
    
    $loadingAddress=$otherDeliveryNoteHeader->addChild('loadingAddress');
    
//    if ($row['company_sub_id']==0) { //kentriko
//      $company_odos=trim_gks($row['company_odos']);
//      if ($company_odos!='') $loadingAddress->addChild('street',$company_odos);
//      $company_arithmos=trim_gks($row['company_arithmos']);
//      if ($company_arithmos!='') $loadingAddress->addChild('number',$company_arithmos);
//      $company_tk=trim_gks($row['company_tk']);
//      if ($company_tk!='') $loadingAddress->addChild('postalCode',$company_tk);
//      $company_poli=trim_gks($row['company_poli']);
//      if ($company_poli!='') $loadingAddress->addChild('city',$company_poli);
//    } else { //ypokatastima
//      $company_sub_odos=trim_gks($row['company_sub_odos']);
//      if ($company_sub_odos!='') $loadingAddress->addChild('street',$company_sub_odos);
//      $company_sub_arithmos=trim_gks($row['company_sub_arithmos']);
//      if ($company_sub_arithmos!='') $loadingAddress->addChild('number',$company_sub_arithmos);
//      $company_sub_tk=trim_gks($row['company_sub_tk']);
//      if ($company_sub_tk!='') $loadingAddress->addChild('postalCode',$company_sub_tk);
//      $company_sub_poli=trim_gks($row['company_sub_poli']);
//      if ($company_sub_poli!='') $loadingAddress->addChild('city',$company_sub_poli);
//    }
        
      $load_odos=trim_gks($row['load_odos']);
      if ($load_odos!='') $loadingAddress->addChild('street',$load_odos);
      $load_arithmos=trim_gks($row['load_arithmos']);
      if ($load_arithmos!='') $loadingAddress->addChild('number',$load_arithmos);
      $load_tk=trim_gks($row['load_tk']);
      if ($load_tk!='') $loadingAddress->addChild('postalCode',$load_tk);
      $load_poli=trim_gks($row['load_poli']);
      if ($load_poli!='') $loadingAddress->addChild('city',$load_poli);
    
    
    $deliveryAddress=$otherDeliveryNoteHeader->addChild('deliveryAddress');
    
//    $ma_odos=trim_gks($row['ma_odos']);
//    if ($ma_odos!='') $deliveryAddress->addChild('street',$ma_odos);
//    $ma_arithmos=trim_gks($row['ma_arithmos']);
//    if ($ma_arithmos!='') $deliveryAddress->addChild('number',$ma_arithmos);
//    $ma_tk=trim_gks($row['ma_tk']);
//    if ($ma_tk!='') $deliveryAddress->addChild('postalCode',$ma_tk);
//    $ma_poli=trim_gks($row['ma_poli']);
//    if ($ma_poli!='') $deliveryAddress->addChild('city',$ma_poli);

    $deli_odos=trim_gks($row['deli_odos']);
    if ($deli_odos!='') $deliveryAddress->addChild('street',$deli_odos);
    $deli_arithmos=trim_gks($row['deli_arithmos']);
    if ($deli_arithmos!='') $deliveryAddress->addChild('number',$deli_arithmos);
    $deli_tk=trim_gks($row['deli_tk']);
    if ($deli_tk!='') $deliveryAddress->addChild('postalCode',$deli_tk);
    $deli_poli=trim_gks($row['deli_poli']);
    if ($deli_poli!='') $deliveryAddress->addChild('city',$deli_poli);



    $startShippingBranch=trim_gks($row['load_branch']);
    if ($startShippingBranch!='') $otherDeliveryNoteHeader->addChild('startShippingBranch',$startShippingBranch);

    $completeShippingBranch=trim_gks($row['deli_branch']);
    if ($completeShippingBranch!='') $otherDeliveryNoteHeader->addChild('completeShippingBranch',$completeShippingBranch);

    if (in_array($eidos_parastatikou_aade_code,['9.1','9.2','9.3','10.1','10.2'])==false) {
      $invoiceHeader->addChild('isDeliveryNote',true);
    }
    
  }

  if ($aade_skopos_diakinisis_code==19 and $aade_skopos_19_descr!='') {
    $invoiceHeader->addChild('otherMovePurposeTitle',$aade_skopos_19_descr);
  }  
  
 
  if ($multipleConnectedMarks!='') {
    $vvv_parts=explode('|',$multipleConnectedMarks);
    foreach ($vvv_parts as $vvv_item) {
      $invoiceHeader->addChild('multipleConnectedMarks', $vvv_item);
    }
  }
  
  if ($reverseDeliveryNote) {
    $invoiceHeader->addChild('reverseDeliveryNote', 1);
    $invoiceHeader->addChild('reverseDeliveryNotePurpose', $reverseDeliveryNotePurpose);
  }
  
//  $aade_tropos_pliromis_code=intval($row['aade_tropos_pliromis_code']);
//  if ($aade_tropos_pliromis_code>0 and $row['gks_price_total']>0) {
//    $paymentMethods = $invoice->addChild('paymentMethods'); 
//      $paymentMethodDetails = $paymentMethods->addChild('paymentMethodDetails'); //PaymentMethodDetailType
//      $paymentMethodDetails->addChild('type',$aade_tropos_pliromis_code);
//      $paymentMethodDetails->addChild('amount',number_format($row['gks_price_total'],2,'.',''));
//      $payment_acquirer_name=trim_gks($row['payment_acquirer_name']);
//      if ($payment_acquirer_name!='') $paymentMethodDetails->addChild('paymentMethodInfo',$payment_acquirer_name);
//  }
  
  //echo '<pre>ddddddddddd11'; die();
  
  if (in_array($doc_table,['gks_acc_inv','gks_acc_pay'])) {
  
    $sql_need_signature="select payment_acquirer_id from gks_acc_seires_paymentacquirers 
    where acc_seira_id=".$rrr_seira_id."
    order by payment_acquirer_id";
    $result_need_signature = $db_link->query($sql_need_signature);        
    if (!$result_need_signature) {debug_mail(false,'error sql',$sql_need_signature);$ret['message']='sql error'; return $ret;}
    $seira_need_signature_array=[];
    while ($row_need_signature = $result_need_signature->fetch_assoc()) {
      $seira_need_signature_array[$row_need_signature['payment_acquirer_id']]=true;
    }
  
  

    $paymentMethods = $invoice->addChild('paymentMethods'); 
    
    $sql_payments="SELECT gks_acc_".$xxx."_payment.id_acc_".$xxx."_payment, 
    gks_acc_".$xxx."_payment.poso, 
    gks_acc_".$xxx."_payment.payment_acquirer_id, 
    gks_payment_acquirers.payment_acquirer_name, 
    gks_payment_acquirers.aade_tropos_pliromis_id, 
    gks_aade_tropoi_pliromis.aade_tropos_pliromis_code, 
    gks_acc_".$xxx."_payment.transaction_id
    FROM ((gks_acc_".$xxx."_payment 
    LEFT JOIN gks_payment_acquirers ON gks_acc_".$xxx."_payment.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer) 
    LEFT JOIN gks_aade_tropoi_pliromis ON gks_payment_acquirers.aade_tropos_pliromis_id = gks_aade_tropoi_pliromis.id_aade_tropos_pliromis) 
    LEFT JOIN gks_eftpos_transaction ON gks_acc_".$xxx."_payment.transaction_id = gks_eftpos_transaction.id_eftpos_transaction
    WHERE gks_acc_".$xxx."_payment.poso<>0 
    AND gks_acc_".$xxx."_payment.acc_".$xxx."_id=".$id."
    ORDER BY gks_acc_".$xxx."_payment.pp";
    $result_payments = $db_link->query($sql_payments); 
    if (!$result_payments) {debug_mail(false,'error sql',$sql_payments);$ret['message']='sql error'; return $ret;}
  
    $pa_row_array=[];
    while ($pa_row = $result_payments->fetch_assoc()) {
      $pa_row_array[]=$pa_row;
    }
    $paroxos_signature_id_array=[];


    foreach ($pa_row_array as $pa_row) {

      if (floatval($pa_row['poso'])<=0) {
        $ret['message']=gks_lang('O τρόπος πληρωμής [1] δεν έχει ποσό');
        $ret['message']=str_replace('[1]',$pa_row['payment_acquirer_name'],$ret['message']);
        debug_mail(false,$ret['message'],$sql_payments);return $ret;}
      if (intval($pa_row['aade_tropos_pliromis_code'])==0) {
        $ret['message']=gks_lang('O τρόπος πληρωμής [1] δεν έχει κωδικό για ΑΑΔΕ');
        $ret['message']=str_replace('[1]',$pa_row['payment_acquirer_name'],$ret['message']);
        debug_mail(false,$ret['message'],$sql_payments);return $ret;}
  
      
      $pmd_item_type=$pa_row['aade_tropos_pliromis_code'];
      $pmd_item_ammount=number_format($pa_row['poso'],2,'.','');
      $pmd_item_paymentMethodInfo=trim_gks($pa_row['payment_acquirer_name']);
      $pmd_item_tipAmount=false;
      $pmd_item_tid='';
      $pmd_item_transactionId='';
      $pmd_item_SigningAuthor='';
      $pmd_item_Signature='';
      $pmd_item_EndToEndReferenceID='';
      
      //print '<pre>sdddddddddddddd ';print_r($pa_row);die();
      if ($pa_row['aade_tropos_pliromis_code']==7 and //POS / e-POS
          isset($seira_need_signature_array[$pa_row['payment_acquirer_id']])) {
        
        $transaction_id=intval($pa_row['transaction_id']);
        if ($transaction_id==0) {
          $ret['message']=gks_lang('Δεν βρέθηκε η πληρωμή για αυτό το παραστατικό για τον τρόπο πληρωμής').' <b>'.$pa_row['payment_acquirer_name'].'</b>';
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        $sql_transaction="SELECT gks_eftpos_transaction.*, 
        gks_aade_paroxos.paroxos_name, 
        gks_aade_paroxos.signing_author
        FROM gks_eftpos_transaction 
        LEFT JOIN gks_aade_paroxos ON gks_eftpos_transaction.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos
        where id_eftpos_transaction=".$transaction_id;
        $result_transaction = $db_link->query($sql_transaction); 
        if (!$result_transaction) {debug_mail(false,'error sql',$sql_transaction);$ret['message']='sql error'; return $ret;}
        if ($result_transaction->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η συναλλαγή EFT/POS με id').': '.$transaction_id;debug_mail(false,$ret['message'],$sql); return $ret;}
        $row_transaction=$result_transaction->fetch_assoc();
        if (trim_gks($row_transaction['transaction_status'])!='done') {
          $ret['message']=gks_lang('Η συναλλαγή EFT/POS με id: [1] δεν είναι ολοκληρωμένη');
          $ret['message']=str_replace('[1]',$transaction_id,$ret['message']);
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        
        //print '<pre>sdddddddddddddd ';print_r($row_transaction);die();
        
        
        $terminalId=trim_gks($row_transaction['terminalId']);
        if ($terminalId=='') {
          $ret['message']=gks_lang('Η συναλλαγή EFT/POS με id: [1] δεν έχει τερματικό');
          $ret['message']=str_replace('[1]',$transaction_id,$ret['message']);
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        $pmd_item_tid=$terminalId;
        
        $aadeTransactionId=trim_gks($row_transaction['aadeTransactionId']);
        if ($aadeTransactionId=='') {
          $ret['message']=gks_lang('Η συναλλαγή EFT/POS με id: [1] δεν έχει κωδικό ΑΑΔΕ');
          $ret['message']=str_replace('[1]',$transaction_id,$ret['message']);
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        $pmd_item_transactionId=$aadeTransactionId;
        
        $aadeProviderSignature=trim_gks($row_transaction['aadeProviderSignature']);
        if ($aadeProviderSignature=='') {
          $ret['message']=gks_lang('Η συναλλαγή EFT/POS με id: [1] δεν έχει υπογραφή παρόχου');
          $ret['message']=str_replace('[1]',$transaction_id,$ret['message']);
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        
        
        $pmd_item_Signature=$aadeProviderSignature;
        $pmd_item_SigningAuthor=$row_transaction['signing_author'];
        $pmd_item_EndToEndReferenceID='';
        
        $tipAmount=floatval($row_transaction['tipAmount']);
        if ($tipAmount!=0) $pmd_item_tipAmount=number_format($tipAmount,2,'.','');
        
        
        $paroxos_signature_id_array[]=intval($row_transaction['paroxos_signature_id']);
        
        $payment_acquirer_with_id=intval($row_transaction['payment_acquirer_with_id']);
        $xxx_transaction_id=intval($row_transaction['xxx_transaction_id']);
        //echo '<pre>ssssssssssssss '.$payment_acquirer_with_id.'|'.$xxx_transaction_id;die();
        
        $pmd_item_is_iris=false;
        if ($payment_acquirer_with_id>0 and $xxx_transaction_id>0) {
          switch ($payment_acquirer_with_id) {   
            case 1://viva
              $sql_xxx_tra="SELECT BankId from gks_viva_transaction 
              where id_viva_transaction=".$xxx_transaction_id;
              $result_xxx_tra = $db_link->query($sql_xxx_tra); 
              if (!$result_xxx_tra) {debug_mail(false,'error sql',$sql_xxx_tra);$ret['message']='sql error'; return $ret;}
              if ($result_xxx_tra->num_rows>=1) {
                $row_xxx_tra=$result_xxx_tra->fetch_assoc();  
                if ($row_xxx_tra['BankId']=='NET_IRIS') {
                  $pmd_item_is_iris=true;
                  //echo '<pre>ssssssssssssss '.$row_xxx_tra['BankId'];die();
                }
              }  
              break;  
            case 3://mellon
              $sql_xxx_tra="SELECT PaymentType from gks_mellon_transaction 
              where id_mellon_transaction=".$xxx_transaction_id;
              $result_xxx_tra = $db_link->query($sql_xxx_tra); 
              if (!$result_xxx_tra) {debug_mail(false,'error sql',$sql_xxx_tra);$ret['message']='sql error'; return $ret;}
              if ($result_xxx_tra->num_rows>=1) {
                $row_xxx_tra=$result_xxx_tra->fetch_assoc();  
                if (empty($row_xxx_tra['PaymentType'])==false and intval($row_xxx_tra['PaymentType'])==1) {
                  $pmd_item_is_iris=true;
                  //echo '<pre>ssssssssssssss '.$row_xxx_tra['BankId'];die();
                }
              }  
              break;            
            case 4://Cardlink
              $sql_xxx_tra="SELECT cardType from gks_cardlink_transaction 
              where id_cardlink_transaction=".$xxx_transaction_id;
              $result_xxx_tra = $db_link->query($sql_xxx_tra); 
              if (!$result_xxx_tra) {debug_mail(false,'error sql',$sql_xxx_tra);$ret['message']='sql error'; return $ret;}
              if ($result_xxx_tra->num_rows>=1) {
                $row_xxx_tra=$result_xxx_tra->fetch_assoc();  
                if (empty($row_xxx_tra['cardType'])==false and trim_gks($row_xxx_tra['cardType'])=='IRIS') {
                  $pmd_item_is_iris=true;
                  //echo '<pre>ssssssssssssss '.$row_xxx_tra['BankId'];die();
                }
              }  
              break;            
            case 5://epay
              $sql_xxx_tra="SELECT PaymentType from gks_epay_transaction 
              where id_epay_transaction=".$xxx_transaction_id;
              $result_xxx_tra = $db_link->query($sql_xxx_tra); 
              if (!$result_xxx_tra) {debug_mail(false,'error sql',$sql_xxx_tra);$ret['message']='sql error'; return $ret;}
              if ($result_xxx_tra->num_rows>=1) {
                $row_xxx_tra=$result_xxx_tra->fetch_assoc();  
                if (empty($row_xxx_tra['PaymentType'])==false and intval($row_xxx_tra['PaymentType'])==1) {
                  $pmd_item_is_iris=true;
                  //echo '<pre>ssssssssssssss '.$row_xxx_tra['BankId'];die();
                }
              }  
              break;               
            default:
            
              break;
          }  
          
          
        }
        
        if ($pmd_item_is_iris) {
          $pmd_item_type=8; 
          $pmd_item_paymentMethodInfo='IRIS via '.$pmd_item_paymentMethodInfo;
        }
        
        //echo '<pre>ssssssssssssss ';print_r($pa_row);die();
        
      }
      
      $paymentMethodDetails = $paymentMethods->addChild('paymentMethodDetails'); //PaymentMethodDetailType
      
      $paymentMethodDetails->addChild('type',$pmd_item_type);
      $paymentMethodDetails->addChild('amount',$pmd_item_ammount);
      if ($pmd_item_paymentMethodInfo!='') $paymentMethodDetails->addChild('paymentMethodInfo',$pmd_item_paymentMethodInfo);
      if ($pmd_item_tipAmount!==false) $paymentMethodDetails->addChild('tipAmount',$pmd_item_tipAmount);
      if ($pmd_item_transactionId!='') $paymentMethodDetails->addChild('transactionId',$pmd_item_transactionId);
      if ($pmd_item_tid!='') $paymentMethodDetails->addChild('tid',$pmd_item_tid);
      if ($pmd_item_Signature!='') {
        $ProvidersSignature = $paymentMethodDetails->addChild('ProvidersSignature'); //ProviderSignatureType
        $ProvidersSignature->addChild('SigningAuthor',$pmd_item_SigningAuthor);
        $ProvidersSignature->addChild('Signature',$pmd_item_Signature);
        if ($pmd_item_EndToEndReferenceID!='') $ProvidersSignature->addChild('EndToEndReferenceID',$pmd_item_EndToEndReferenceID);
        
      }
      
    }
    
    
    
    //me ton palio tropo
    if (count($pa_row_array)==0) {
      $aade_tropos_pliromis_code=intval($row['aade_tropos_pliromis_code']);
      debug_mail(false,'use old way paymentMethods','aade_tropos_pliromis_code:'.$aade_tropos_pliromis_code);
      if ($aade_tropos_pliromis_code>0 and $row['gks_price_total']>0) {
        //$paymentMethods = $invoice->addChild('paymentMethods'); 
          $paymentMethodDetails = $paymentMethods->addChild('paymentMethodDetails'); //PaymentMethodDetailType
          $paymentMethodDetails->addChild('type',$aade_tropos_pliromis_code);
          $paymentMethodDetails->addChild('amount',number_format($row['gks_price_total'],2,'.',''));
          $payment_acquirer_name=trim_gks($row['payment_acquirer_name']);
          if ($payment_acquirer_name!='') $paymentMethodDetails->addChild('paymentMethodInfo',$payment_acquirer_name);
      }
      
      
    }
    
    $ret['paroxos_signature_id_array']=$paroxos_signature_id_array;
    //echo '<pre>ddddddddddd ';print_r($ret['paroxos_signature_id_array']);die();
  
  }
  
  if ($doc_table=='gks_whi_mov') {
    $ret['paroxos_signature_id_array']=[];
  }
  
  //echo '<pre>';print_r($paymentMethods);die();
  //print '<pre>';print_r($ret);print_r($xml);die();


  if ($doc_table=='gks_acc_inv') {
    $sql_products="SELECT gks_acc_inv_products.*, 
    gks_aade_eidos_posotitas.aade_eidos_posotitas_code, gks_aade_katigoria_fpa.aade_katigoria_fpa_code,
    gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_code,
    gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_code,
    gks_aade_katigoria_telon.aade_katigoria_telon_code,
    gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_code,
    gks_aade_katigoria_fpa_ejeresi.aade_katigoria_fpa_ejeresi_code,
    gks_eshop_products.product_sku,
    gks_eshop_products.product_taric,gks_eshop_products.product_code
    
    FROM (((((((((gks_acc_inv_products 
    LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada) 
    LEFT JOIN gks_aade_eidos_posotitas ON gks_monades_metrisis.aade_eidos_posotitas_id = gks_aade_eidos_posotitas.id_aade_eidos_posotitas) 
    LEFT JOIN gks_eshop_fpa ON gks_acc_inv_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
    LEFT JOIN gks_aade_katigoria_fpa ON gks_eshop_fpa.aade_katigoria_fpa_id = gks_aade_katigoria_fpa.id_aade_katigoria_fpa)
    LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_acc_inv_products.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron)
    LEFT JOIN gks_aade_katigoria_xartosimou ON gks_acc_inv_products.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou)
    LEFT JOIN gks_aade_katigoria_telon ON gks_acc_inv_products.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon)
    LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_acc_inv_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron)
    LEFT JOIN gks_aade_katigoria_fpa_ejeresi ON gks_acc_inv_products.product_fpa_ejeresi_id = gks_aade_katigoria_fpa_ejeresi.id_aade_katigoria_fpa_ejeresi)
    LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product
    
    where gks_acc_inv_products.acc_inv_id=".$id."
    ORDER BY gks_acc_inv_products.product_aa";
    $result_products = $db_link->query($sql_products); 
    if (!$result_products) {debug_mail(false,'error sql',$sql_products);$ret['message']='sql error'; return $ret;}
    
    $prow_array=array();
    $prow_ids=array();
    while ($prow = $result_products->fetch_assoc()) {
      $prow_array[]=$prow;
      $prow_ids[]=$prow['id_acc_inv_product'];
    }
    
    $pirow_array=array();
    $perow_array=array();
    if (count($prow_ids)>0) {
      $sql_products_income="SELECT gks_acc_inv_products_income.*, 
      gks_aade_typos_xarakt_esodon.aade_typos_xarakt_esodon_code, gks_aade_katigoria_xarakt_esodon.aade_katigoria_xarakt_esodon_code
      FROM (gks_acc_inv_products_income 
      LEFT JOIN gks_aade_typos_xarakt_esodon ON gks_acc_inv_products_income.aade_typos_xarakt_esodon_id = gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon) 
      LEFT JOIN gks_aade_katigoria_xarakt_esodon ON gks_acc_inv_products_income.aade_katigoria_xarakt_esodon_id = gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon
      WHERE gks_acc_inv_products_income.acc_inv_product_id In (".implode(',',$prow_ids).")
      order by id_acc_inv_product_income";
      $result_products_income = $db_link->query($sql_products_income); 
      if (!$result_products_income) {debug_mail(false,'error sql',$sql_products_income);$ret['message']='sql error'; return $ret;}
      while ($pirow = $result_products_income->fetch_assoc()) {
        if (isset($pirow_array[$pirow['acc_inv_product_id']])==false) $pirow_array[$pirow['acc_inv_product_id']]=array();
        $pirow_array[$pirow['acc_inv_product_id']][] = array(
          'classificationType' => (trim_gks($pirow['aade_typos_xarakt_esodon_code']) != '' ? trim_gks($pirow['aade_typos_xarakt_esodon_code']) : ''),
          'classificationCategory' => (trim_gks($pirow['aade_katigoria_xarakt_esodon_code']) != '' ? trim_gks($pirow['aade_katigoria_xarakt_esodon_code']) : ''),
          'amount' => $pirow['acc_inv_product_income_ammount'],
        );
      }
      
      $sql_products_expenses="SELECT gks_acc_inv_products_expenses.*, 
      gks_aade_typos_xarakt_eksodon.aade_typos_xarakt_eksodon_code, gks_aade_katigoria_xarakt_eksodon.aade_katigoria_xarakt_eksodon_code
      FROM (gks_acc_inv_products_expenses 
      LEFT JOIN gks_aade_typos_xarakt_eksodon ON gks_acc_inv_products_expenses.aade_typos_xarakt_eksodon_id = gks_aade_typos_xarakt_eksodon.id_aade_typos_xarakt_eksodon) 
      LEFT JOIN gks_aade_katigoria_xarakt_eksodon ON gks_acc_inv_products_expenses.aade_katigoria_xarakt_eksodon_id = gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon
      WHERE gks_acc_inv_products_expenses.acc_inv_product_id In (".implode(',',$prow_ids).")
      order by id_acc_inv_product_expenses";
      $result_products_expenses = $db_link->query($sql_products_expenses); 
      if (!$result_products_expenses) {debug_mail(false,'error sql',$sql_products_expenses);$ret['message']='sql error'; return $ret;}
      while ($perow = $result_products_expenses->fetch_assoc()) {
        if (isset($perow_array[$perow['acc_inv_product_id']])==false) $perow_array[$perow['acc_inv_product_id']]=array();
        
        $perow_array[$perow['acc_inv_product_id']][] = array(
          'classificationType' => (trim_gks($perow['aade_typos_xarakt_eksodon_code']) != '' ? trim_gks($perow['aade_typos_xarakt_eksodon_code']) : ''), 
          'classificationCategory' => (trim_gks($perow['aade_katigoria_xarakt_eksodon_code']) != '' ? trim_gks($perow['aade_katigoria_xarakt_eksodon_code']) : ''),  
          'amount' => $perow['acc_inv_product_expenses_ammount'],
        );
      }    
    }
    //print '<pre>';print_r($pirow_array);print_r($perow_array);die();
    
    
  
    $income_sum_array=array();
    $expenses_sum_array=array();
  
    $lineNumber=0;
    foreach ($prow_array as $prow) {
      $lineNumber++;
      $invoiceDetails = $invoice->addChild('invoiceDetails'); //InvoiceRowType
    
        $product_descr=trim_gks($prow['product_descr']);
        if ($product_descr!='') $prow['xml_product_descr']=$product_descr;
    
        $invoiceDetails->addChild('lineNumber',$lineNumber);
        
        if ($isDeliveryNote) {
          $product_taric=trim_gks($prow['product_taric']);
          if ($product_taric!='') $invoiceDetails->addChild('TaricNo',$product_taric);
          
          $product_code=trim_gks($prow['product_code']);
          if ($product_code!='') $invoiceDetails->addChild('itemCode',$product_code);
          
          $invoiceDetails->addChild('itemDescr',$prow['xml_product_descr']);
          //echo '<pre>wwwwwwwwww ggg';print_r($prow);die();
        }
        
        if ($eidos_parastatikou_aade_code=='8.2') {
          //xoris quantity
        } else {
          if ($eidos_parastatikou_has_posotita!=0) {
            

                          
            $invoiceDetails->addChild('quantity',myNumberFormatNo0($prow['product_quantity']));
            
            if (isset($prow['aade_eidos_posotitas_code'])) {
            	$invoiceDetails->addChild('measurementUnit',$prow['aade_eidos_posotitas_code']);
            } else {
            	$ret['message']=gks_lang('Δεν έχει ορισθεί κωδικός μονάδας μέτρησης για ΑΑΔΕ στην γραμμή').' '.$lineNumber;debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
            }
          }
        }
        
        
        //invoiceDetailType
        if ($eidos_parastatikou_aade_code=='8.2') {
          $prow['product_price_final_all_net']=0;
        } 
        $invoiceDetails->addChild('netValue',number_format($prow['product_price_final_all_net'],2,'.',''));
        
        
        $aade_katigoria_fpa_code=(isset($prow['aade_katigoria_fpa_code']) ? intval($prow['aade_katigoria_fpa_code']) : 0);
        //if ($aade_katigoria_fpa_code==0) $aade_katigoria_fpa_code=7; //miden
        
        if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='1.2') { //Timologio Polisis / Endokoinotikes Paradoseis
          $aade_katigoria_fpa_code=7;
        }
        if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='1.3') { //Timologio Polisis / Paradoseis Triton Choron
          $aade_katigoria_fpa_code=7;
        }
        if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='3.1') { //Titlos Ktisis (mi ypochreos Ekdotis)
          $aade_katigoria_fpa_code=8;
        }
        if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='8.1') { //Enoikia - Esodo
          $aade_katigoria_fpa_code=8;
        }
        if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='8.2') { //Eidiko Stoicheio - Apodeixis Eispraxis Forou Diamonis
          $aade_katigoria_fpa_code=8;
        }
        
        
        if ($aade_katigoria_fpa_code<=0) {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία ΦΠΑ της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; debug_mail(false,$ret['message'],''); return $ret;}
        //if ($aade_katigoria_fpa_code>0)
        $invoiceDetails->addChild('vatCategory',$aade_katigoria_fpa_code);
        
        //if ($prow['product_price_final_all_fpa']!=0)
        $invoiceDetails->addChild('vatAmount',number_format($prow['product_price_final_all_fpa'],2,'.',''));
        
        if ($prow['aade_katigoria_fpa_ejeresi_code']!=0) {
          $invoiceDetails->addChild('vatExemptionCategory',intval($prow['aade_katigoria_fpa_ejeresi_code']));
        }
  
        
        //vatExemptionCategory
        //dienergia
        $invoiceDetails->addChild('discountOption','true');
        
        
        if ($prow['product_withheldAmount']!=0) {
          $invoiceDetails->addChild('withheldAmount',number_format(floatval($prow['product_withheldAmount']),2,'.',''));
          $invoiceDetails->addChild('withheldPercentCategory',$prow['aade_katigoria_parakratoumemenon_foron_code']);
        }
          
        if ($prow['product_stampDutyAmount']!=0) {
          $invoiceDetails->addChild('stampDutyAmount',number_format(floatval($prow['product_stampDutyAmount']),2,'.',''));
          $invoiceDetails->addChild('stampDutyPercentCategory',$prow['aade_katigoria_xartosimou_code']);
        }
          
        if ($prow['product_feesAmount']!=0) {
          $invoiceDetails->addChild('feesAmount',number_format(floatval($prow['product_feesAmount']),2,'.',''));
          $invoiceDetails->addChild('feesPercentCategory',$prow['aade_katigoria_telon_code']);
        }
          
        if ($prow['product_otherTaxesAmount']!=0) {
          $invoiceDetails->addChild('otherTaxesPercentCategory',$prow['aade_katigoria_loipon_foron_code']);
          $invoiceDetails->addChild('otherTaxesAmount',number_format(floatval($prow['product_otherTaxesAmount']),2,'.',''));
        }
        
        if ($prow['product_deductionsAmount']!=0) {
          $invoiceDetails->addChild('deductionsAmount',number_format(floatval($prow['product_deductionsAmount']),2,'.',''));
        }
  
  
        //lineComments
        $found_cc_income=0;
        if (isset($pirow_array[$prow['id_acc_inv_product']])) { //incomeClassification
          foreach ($pirow_array[$prow['id_acc_inv_product']] as $value) {
            $incomeClassification = $invoiceDetails->addChild('incomeClassification'); //InvoiceIncomeClassificationType
            
            if ($value['classificationType']!='e3_null') {
              $incomeClassification->addChild('icls:classificationType',$value['classificationType'],$NS['icls']);
            }
            if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; debug_mail(false,$ret['message']); return $ret;}
            if ($value['classificationCategory']!='category_vat') {
              $incomeClassification->addChild('icls:classificationCategory',$value['classificationCategory'],$NS['icls']);
            }
            
            if ($eidos_parastatikou_aade_code=='8.2') {
              $value['amount']=0;
            }
            
            
            $incomeClassification->addChild('icls:amount',number_format($value['amount'],2,'.',''),$NS['icls']);
            
            
            $sum_key=$value['classificationType'].'||'.$value['classificationCategory'];
            //echo $sum_key.'||';
            if (isset($income_sum_array[$sum_key])==false) {
              $income_sum_array[$sum_key]=array(
                'classificationType' => $value['classificationType'],
                'classificationCategory' => $value['classificationCategory'],
                'amount'=>0,
              );
            }
            $income_sum_array[$sum_key]['amount']+=$value['amount'];
            $found_cc_income++;
          }
        }
  
        $found_cc_expenses=0;
        if (isset($perow_array[$prow['id_acc_inv_product']])) { //expensesClassification
          foreach ($perow_array[$prow['id_acc_inv_product']] as $value) {
            $expensesClassification = $invoiceDetails->addChild('expensesClassification'); //InvoiceExpensesClassificationType
            
            if ($value['classificationType']!='e3_null') {
              $expensesClassification->addChild('ecls:classificationType',$value['classificationType'],$NS['ecls']);
            }
            if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; debug_mail(false,$ret['message']); return $ret;}
            if ($value['classificationCategory']!='category_vat') {
              $expensesClassification->addChild('ecls:classificationCategory',$value['classificationCategory'],$NS['ecls']);
            }
            $expensesClassification->addChild('ecls:amount',number_format($value['amount'],2,'.',''),$NS['ecls']);
          
            
            $sum_key=$value['classificationType'].'||'.$value['classificationCategory'];
            //echo $sum_key.'||';
            if (isset($expenses_sum_array[$sum_key])==false) {
              $expenses_sum_array[$sum_key]=array(
                'classificationType' => $value['classificationType'],
                'classificationCategory' => $value['classificationCategory'],
                'amount'=>0,
              );
            }
            $expenses_sum_array[$sum_key]['amount']+=$value['amount'];
            
            $found_cc_expenses++;
          }
        }
        
        if ($found_cc_income==0 and $found_cc_expenses==0) {
          $ret['message']=gks_lang('Δεν βρέθηκαν Χαρακτηρισμοί Εσόδων ή Εξόδων στο είδος').' <b>'.$product_descr.'</b>'; 
          debug_mail(false,'income-expenses-Classification',print_r($prow,true));
          return $ret;
        }
        
        
    }
  } else if ($doc_table=='gks_acc_pay') {
    $lineNumber=1;
    $invoiceDetails = $invoice->addChild('invoiceDetails');
    //$pa_row_array[0]['payment_acquirer_name'];
    $invoiceDetails->addChild('lineNumber',$lineNumber);
    //$invoiceDetails->addChild('quantity',1);
    //$invoiceDetails->addChild('measurementUnit',1);
    $invoiceDetails->addChild('netValue',number_format($pa_row_array[0]['poso'],2,'.',''));
    $invoiceDetails->addChild('vatCategory',8);
    $invoiceDetails->addChild('vatAmount',0);
    
    $incomeClassification = $invoiceDetails->addChild('incomeClassification'); 
    $incomeClassification->addChild('icls:classificationCategory','category1_95',$NS['icls']);
    $incomeClassification->addChild('icls:amount',number_format($pa_row_array[0]['poso'],2,'.',''),$NS['icls']);
    
    $income_sum_array=array();
    $income_sum_array[0]=array(
      'classificationType' => 'e3_null',
      'classificationCategory' => 'category1_95',
      'amount'=>$pa_row_array[0]['poso'],
    );
    
    $expenses_sum_array=array();
    
  } else if ($doc_table=='gks_whi_mov') {
    $sql_products="SELECT gks_whi_mov_products.*, 
    gks_aade_eidos_posotitas.aade_eidos_posotitas_code,
    gks_eshop_products.product_sku,
    gks_eshop_products.product_taric,gks_eshop_products.product_code
    
    FROM ((gks_whi_mov_products 
    LEFT JOIN gks_monades_metrisis ON gks_whi_mov_products.product_monada_id = gks_monades_metrisis.id_monada) 
    LEFT JOIN gks_aade_eidos_posotitas ON gks_monades_metrisis.aade_eidos_posotitas_id = gks_aade_eidos_posotitas.id_aade_eidos_posotitas)
    LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product
    where gks_whi_mov_products.whi_mov_id=".$id."
    ORDER BY gks_whi_mov_products.product_aa";
    
    //echo '<pre>ddddddddd '.$sql_products;die();
    $result_products = $db_link->query($sql_products); 
    if (!$result_products) {debug_mail(false,'error sql',$sql_products);$ret['message']='sql error'; return $ret;}
    
    $prow_array=array();
    $prow_ids=array();
    while ($prow = $result_products->fetch_assoc()) {
      $prow_array[]=$prow;
      $prow_ids[]=$prow['id_whi_mov_product'];
    }
    
    //$pirow_array=array();
    //$perow_array=array();

    //print '<pre>';print_r($pirow_array);print_r($perow_array);die();
    
    
  
  
    $lineNumber=0;
    foreach ($prow_array as $prow) {
      $lineNumber++;
      $invoiceDetails = $invoice->addChild('invoiceDetails'); //InvoiceRowType
    
        $product_descr=trim_gks($prow['product_descr']);
        if ($product_descr!='') $prow['xml_product_descr']=$product_descr;
    
        $invoiceDetails->addChild('lineNumber',$lineNumber);
        
//        if ($eidos_parastatikou_aade_code=='10.1' and $prow['product_quantity'] < 0) {
//          $invoiceDetails->addChild('recType',7);
//        }
          
        $product_taric=trim_gks($prow['product_taric']);
        if ($product_taric!='') $invoiceDetails->addChild('TaricNo',$product_taric);
          
        $product_code=trim_gks($prow['product_code']);
        if ($product_code!='') $invoiceDetails->addChild('itemCode',$product_code);
        
        $invoiceDetails->addChild('itemDescr',$prow['xml_product_descr']);


          
        if ($eidos_parastatikou_has_posotita!=0) {
          $invoiceDetails->addChild('quantity',myNumberFormatNo0($prow['product_quantity']));
          
          if (isset($prow['aade_eidos_posotitas_code'])) {
          	$invoiceDetails->addChild('measurementUnit',$prow['aade_eidos_posotitas_code']);
          } else {
          	$ret['message']=gks_lang('Δεν έχει ορισθεί κωδικός μονάδας μέτρησης για ΑΑΔΕ στην γραμμή').' '.$lineNumber;debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
          }
        }
        
        
        
        //print '<pre>';print_r($prow);die();
        //$invoiceDetails->addChild('itemDescr',$prow['xml_product_descr']);
        
        //$invoiceDetails->addChild('discountOption','true');
        $invoiceDetails->addChild('netValue','0.00');
        $invoiceDetails->addChild('vatCategory',8);
        $invoiceDetails->addChild('vatAmount',0);
        
        $incomeClassification = $invoiceDetails->addChild('incomeClassification'); 
        $incomeClassification->addChild('icls:classificationCategory','category3',$NS['icls']);
        $incomeClassification->addChild('icls:amount',0,$NS['icls']);

            
            
    } 

    $income_sum_array=array();
    $income_sum_array[0]=array(
      'classificationType' => 'e3_null',
      'classificationCategory' => 'category3', //
      'amount'=>0,
    );    
    $expenses_sum_array=array();
       
  }
  
  //print '<pre>';print_r($ret);var_dump($xml);die();
  
  if ($lineNumber==0) {debug_mail(false,'error sql',$sql);$ret['message']=gks_lang('Δεν βρέθηκαν γραμμές στο παραστατικό'); return $ret;}
  
  //echo '<pre>';print_r($income_sum_array);die();




    
  
  
  
  

  //$taxesTotals = $invoice->addChild('taxesTotals'); //TaxesType
  
  $invoiceSummary = $invoice->addChild('invoiceSummary'); //InvoiceSummaryType
  
  if ($eidos_parastatikou_aade_code=='8.2') {
    $row['gks_price_net']=0;
    $row['gks_price_total']=$row['totalOtherTaxesAmount'];
  }
    
  if ($doc_table=='gks_acc_inv') {  
    //if ($row['gks_price_net']!=0) 
      $invoiceSummary->addChild('totalNetValue',number_format(floatval($row['gks_price_net']),2,'.',''));
    //if ($row['gks_price_fpa']!=0) 
      $invoiceSummary->addChild('totalVatAmount',number_format(floatval($row['gks_price_fpa']),2,'.',''));
    //if ($row['totalWithheldAmount']!=0) 
      $invoiceSummary->addChild('totalWithheldAmount',number_format(floatval($row['totalWithheldAmount']),2,'.',''));
    //if ($row['totalFeesAmount']!=0) 
      $invoiceSummary->addChild('totalFeesAmount',number_format(floatval($row['totalFeesAmount']),2,'.',''));
    //if ($row['totalStampDutyamount']!=0) 
      $invoiceSummary->addChild('totalStampDutyAmount',number_format(floatval($row['totalStampDutyamount']),2,'.',''));
    //if ($row['totalOtherTaxesAmount']!=0) 
      $invoiceSummary->addChild('totalOtherTaxesAmount',number_format(floatval($row['totalOtherTaxesAmount']),2,'.',''));
    //if ($row['totalDeductionsAmount']!=0) 
      $invoiceSummary->addChild('totalDeductionsAmount',number_format(floatval($row['totalDeductionsAmount']),2,'.',''));
    //if ($row['gks_price_total']!=0) 
      $invoiceSummary->addChild('totalGrossValue',number_format(floatval($row['gks_price_total']),2,'.',''));
  
  } else if ($doc_table=='gks_acc_pay') {
      $invoiceSummary->addChild('totalNetValue',number_format(floatval($pa_row_array[0]['poso']),2,'.',''));         
      $invoiceSummary->addChild('totalVatAmount','0.00');


      $invoiceSummary->addChild('totalWithheldAmount','0.00');
      $invoiceSummary->addChild('totalFeesAmount','0.00');
      $invoiceSummary->addChild('totalStampDutyAmount','0.00');
      $invoiceSummary->addChild('totalOtherTaxesAmount','0.00');
      $invoiceSummary->addChild('totalDeductionsAmount','0.00');
      //$invoiceSummary->addChild('totalGrossValue');
      $invoiceSummary->addChild('totalGrossValue',number_format(floatval($pa_row_array[0]['poso']),2,'.','')); 
  } else if ($doc_table=='gks_whi_mov') {
    
      $invoiceSummary->addChild('totalNetValue', '0.00');         
      $invoiceSummary->addChild('totalVatAmount','0.00');


      $invoiceSummary->addChild('totalWithheldAmount','0.00');
      $invoiceSummary->addChild('totalFeesAmount','0.00');
      $invoiceSummary->addChild('totalStampDutyAmount','0.00');
      $invoiceSummary->addChild('totalOtherTaxesAmount','0.00');
      $invoiceSummary->addChild('totalDeductionsAmount','0.00');
      $invoiceSummary->addChild('totalGrossValue','0.00'); 
    
  }
  
  
  //print '<pre>';print_r($ret);var_dump($xml);die();
  
  if (count($income_sum_array)>0) {
    foreach ($income_sum_array as $value) {
      $incomeClassification=$invoiceSummary->addChild('incomeClassification');
      
      if ($value['classificationType']!='e3_null') {
        $incomeClassification->addChild('icls:classificationType',$value['classificationType'],$NS['icls']);
      }
      if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στην σύνοψη του παραστατικού'); debug_mail(false,$ret['message']); return $ret;}
      $incomeClassification->addChild('icls:classificationCategory',$value['classificationCategory'],$NS['icls']);
      $incomeClassification->addChild('icls:amount',number_format($value['amount'],2,'.',''),$NS['icls']);
    } 
  }

  if (count($expenses_sum_array)>0) {
    foreach ($expenses_sum_array as $value) {
      $expensesClassification=$invoiceSummary->addChild('expensesClassification');
      
      if ($value['classificationType']!='e3_null') {
        $expensesClassification->addChild('ecls:classificationType',$value['classificationType'],$NS['ecls']);
      }
      if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στην σύνοψη του παραστατικού'); debug_mail(false,$ret['message']); return $ret;}
      if ($value['classificationCategory']!='category_vat') {
        $expensesClassification->addChild('ecls:classificationCategory',$value['classificationCategory'],$NS['ecls']);
      }
      $expensesClassification->addChild('ecls:amount',number_format($value['amount'],2,'.',''),$NS['ecls']);
    } 
  }
   
   
  if (in_array($doc_table,['gks_acc_inv','gks_whi_mov'])) {
    $sql_corri="SELECT *
    FROM gks_".$ttt."_packings_declarations
    where ".$ttt."_id=".$id."
    ORDER BY packaging_aa";
    $result_corri = $db_link->query($sql_corri);        
    if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
    $pde_items=[];
    while ($row_corri = $result_corri->fetch_assoc()) {
      $type_id=intval($row_corri['packaging_type_id']);
      $type_6_descr=trim_gks($row_corri['packaging_type_6_descr']);
      if ($type_id!=6) $type_6_descr='';
      $quantity=intval($row_corri['packaging_quantity']);
      if ($type_id>0 and $quantity>0) {
        $pde_items[]=array(
          'type_id'=> $type_id,
          'type_6_descr'=> $type_6_descr,
          'quantity'=> $quantity,
        );
      }
    }
    if (count($pde_items)>0) {
      $packingsDeclarations = $invoice->addChild('packingsDeclarations'); //PackingsDeclaration
      foreach ($pde_items as $pde_item) {
        $mypackage=$packingsDeclarations->addChild('Packages');
        $mypackage->addChild('packagingType',$pde_item['type_id']); 
        $mypackage->addChild('quantity',$pde_item['quantity']); 
        if ($pde_item['type_6_descr']!='') {
          $mypackage->addChild('otherPackagingTypeTitle',$pde_item['type_6_descr']); 
        }
      } 
    }
    //echo '<pre>';print_r($pde_items);die();

  }

  
  $temp=$xml->asXML();
  $temp=str_replace('><', '>'."\n".'<', $temp);
  
  //print '<pre>xaml dddd '.htmlspecialchars( $temp);die();
  
  
  if (isset($uid_array['vatNumber']) and 
      isset($uid_array['issueDate']) and 
      isset($uid_array['branch']) and 
      isset($uid_array['invoiceType']) and 
      isset($uid_array['series']) and 
      isset($uid_array['aa'])) {
        
   $uid_str=$uid_array['vatNumber'].'-'.
            $uid_array['issueDate'].'-'.    
            $uid_array['branch'].'-'.    
            $uid_array['invoiceType'].'-'.    
            $uid_array['series'].'-'.    
            $uid_array['aa'];   
    $iso88597 = iconv('UTF-8', 'ISO-8859-7', $uid_str);
    //$iso88597 = mb_convert_encoding($iso88597, 'ISO-8859-7', 'UTF-8');
    
    $ret['out_invoiceuid']=strtoupper(sha1($iso88597)); 
    $ret['out_invoiceuid_raw']=array(
      'uid_array'=>$uid_array,
      'uid_str'=>$uid_str,
      'iso88597'=>$iso88597,
      'sha1'=>$ret['out_invoiceuid'],
    );          
    //echo '<pre>ret_xml 11111111 ';print_r($ret['out_invoiceuid_raw']);die();
    if (isset($ret['out_invoiceuid'])) {
      $out_invoiceuid=$ret['out_invoiceuid'];
      
      $sql_invoiceuid="select id_acc_inv,aade_invoicemark from gks_acc_inv where aade_invoiceuid='".$db_link->escape_string($out_invoiceuid)."'";
      $result_invoiceuid = $db_link->query($sql_invoiceuid);        
      if (!$result_invoiceuid) {debug_mail(false,'error sql',$sql_invoiceuid);$ret['message']='sql error'; return $ret;}
      if ($result_invoiceuid->num_rows>0) {
        $row_invoiceuid = $result_invoiceuid->fetch_assoc();
        $ret['message']=gks_lang('Το <b>Αναγνωριστικό Παραστατικού</b> που θα δημιουργηθεί είναι το<br><b>[1]</b><br>το οποίο υπάρχει ήδη στο παραστατικό με ΜΑΡΚ<br><b>[2]<br></b>και εάν αποσταλεί θα το ακυρώσει.<br>Μετάβαση σε αυτό το παραστατικό').': ';
        $ret['message']=str_replace('[1]',$out_invoiceuid,$ret['message']);
        $ret['message']=str_replace('[2]',$row_invoiceuid['aade_invoicemark'],$ret['message']);
        $ret['message'].='<a href="admin-acc-inv-item.php?id='.$row_invoiceuid['id_acc_inv'].'" class="gks_link" >#'.$row_invoiceuid['id_acc_inv'].'</a>';
        debug_mail(false,'duplicate aade_invoiceuid',$ret['message']."\r\n".$sql_invoiceuid); 
        return $ret;
      }

      $sql_invoiceuid="select id_acc_pay,aade_invoicemark from gks_acc_pay where aade_invoiceuid='".$db_link->escape_string($out_invoiceuid)."'";
      $result_invoiceuid = $db_link->query($sql_invoiceuid);        
      if (!$result_invoiceuid) {debug_mail(false,'error sql',$sql_invoiceuid);$ret['message']='sql error'; return $ret;}
      if ($result_invoiceuid->num_rows>0) {
        $row_invoiceuid = $result_invoiceuid->fetch_assoc();
        $ret['message']=gks_lang('Το <b>Αναγνωριστικό Παραστατικού</b> που θα δημιουργηθεί είναι το<br><b>[1]</b><br>το οποίο υπάρχει ήδη στην πληρωμή με ΜΑΡΚ<br><b>[2]<br></b>και εάν αποσταλεί θα το ακυρώσει.<br>Μετάβαση σε αυτό το παραστατικό').': ';
        $ret['message']=str_replace('[1]',$out_invoiceuid,$ret['message']);
        $ret['message']=str_replace('[2]',$row_invoiceuid['aade_invoicemark'],$ret['message']);
        $ret['message'].='<a href="admin-acc-pay-item.php?id='.$row_invoiceuid['id_acc_pay'].'" class="gks_link" >#'.$row_invoiceuid['id_acc_pay'].'</a>';
        debug_mail(false,'duplicate aade_invoiceuid',$ret['message']."\r\n".$sql_invoiceuid); 
        return $ret;
      }

      $sql_invoiceuid="select id_whi_mov,aade_invoicemark from gks_whi_mov where aade_invoiceuid='".$db_link->escape_string($out_invoiceuid)."'";
      $result_invoiceuid = $db_link->query($sql_invoiceuid);        
      if (!$result_invoiceuid) {debug_mail(false,'error sql',$sql_invoiceuid);$ret['message']='sql error'; return $ret;}
      if ($result_invoiceuid->num_rows>0) {
        $row_invoiceuid = $result_invoiceuid->fetch_assoc();
        $ret['message']=gks_lang('Το <b>Αναγνωριστικό Παραστατικού</b> που θα δημιουργηθεί είναι το<br><b>[1]</b><br>το οποίο υπάρχει ήδη στο δελτίο με ΜΑΡΚ<br><b>[2]<br></b>και εάν αποσταλεί θα το ακυρώσει.<br>Μετάβαση σε αυτό το παραστατικό').': '.
        $ret['message']=str_replace('[1]',$out_invoiceuid,$ret['message']);
        $ret['message']=str_replace('[2]',$row_invoiceuid['aade_invoicemark'],$ret['message']);
        $ret['message'].='<a href="admin-whi-mov-item.php?id='.$row_invoiceuid['id_whi_mov'].'" class="gks_link" >#'.$row_invoiceuid['id_whi_mov'].'</a>';
        debug_mail(false,'duplicate aade_invoiceuid',$ret['message']."\r\n".$sql_invoiceuid); 
        return $ret;
      }
            
      //echo '<pre>ssssssssssssss';die();
      
      
    }
             
  }
  
  $ret['out_xml']=$temp;
  $ret['message']='OK';
  $ret['success']=true;

  return $ret;

}


