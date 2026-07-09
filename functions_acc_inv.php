<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function getAccInvStateDescr($mystate,$load_lang='') {
  global $gks_user_settings;
  if ($load_lang=='') {
    $load_lang='el-GR';
    if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
  }
  
  if ($load_lang=='el-GR') {  
    switch ($mystate) {
      case '010draft': return gks_lang('Πρόχειρο','part4','getAccInvStateDescr'); break; 
      case '040cancelled': return gks_lang('Ακυρωμένο','part4','getAccInvStateDescr'); break; 
      case '050proinvoice': return gks_lang('Προτιμολόγιο','part4','getAccInvStateDescr'); break; 
      case '070ypoekdosi': return gks_lang('Υπό Έκδοση','part4','getAccInvStateDescr'); break; 
      case '080listing': return gks_lang('Καταχώρηση','part4','getAccInvStateDescr'); break; 
      case '090ekdosi': return gks_lang('Έκδοση','part4','getAccInvStateDescr'); break; 
      case '100payment': return gks_lang('Εξοφλημένο','part4','getAccInvStateDescr'); break; 
      default: return $mystate; break; 
    }
  } else {
    switch ($mystate) {
      case '010draft': return 'Draft'; break; 
      case '040cancelled': return 'Cancelled'; break; 
      case '050proinvoice': return 'ProInvoice'; break; 
      case '070ypoekdosi': return 'For Issue'; break; 
      case '080listing': return 'Listing'; break; 
      case '090ekdosi': return 'Issue'; break; 
      case '100payment': return 'Paid'; break; 
      default: return $mystate; break; 
    }
    
  }
   
}
 
function select_gks_acc_inv() {

  

$sql="SELECT gks_acc_inv.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
".GKS_WP_TABLE_PREFIX."users_aade.gks_nickname AS gks_nickname_aade,
".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.ma_branch as ma_branch_fromuser,
gks_users.gemi_number,gks_users.is_b2g,gks_users.b2g_aaht_code,gks_users.b2g_aaht_name,
gks_users.b2g_aaht_foreas,gks_users.b2g_aaht_typos_forea,gks_users.b2g_aaht_kodikos_ekatharisis,
gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,
gks_acc_seires.is_xeirografi,gks_acc_seires.send_mydata,gks_acc_seires.send_paroxos,
gks_acc_seires.seira_isdeliverynote,
gks_acc_seires.seira_is_self_pricing,
gks_acc_seires.seira_is_vat_payment_suspension,
gks_acc_journal.acc_eidos_parastatikou_id,
gks_acc_journal.acc_eidos_parastatikou_other_entity,
gks_acc_journal.journal_has_correlated_invoices,
gks_acc_journal.journal_has_multiple_connected_marks,
gks_acc_journal.journal_has_packings_declarations,
gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, antisimvalomenos_label, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code as whi_eidos_parastatikou_aade_code,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
gks_lang.lang_name,gks_country.country_name,gks_nomoi.nomos_descr,
gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_warehouses_from.warehouse_name AS warehouse_name_from, 
gks_warehouses_to.warehouse_name AS warehouse_name_to,

gks_nomoi_load.nomos_descr as nomos_descr_load,
gks_country_load.country_name as country_name_load,
gks_nomoi_deli.nomos_descr as nomos_descr_deli,
gks_country_deli.country_name as country_name_deli,

gks_aade_paroxos.paroxos_name,
gks_pos.pos_name, 
gks_erp_app_mobile.erp_app_mobile_name

FROM (((((((((((((((((((((((((((((((((gks_acc_inv

LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_inv.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_inv.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_aade ON gks_acc_inv.aade_user_id = ".GKS_WP_TABLE_PREFIX."users_aade.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_print ON gks_acc_inv.print_user_id = ".GKS_WP_TABLE_PREFIX."users_print.ID) 
LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
LEFT JOIN gks_delivery_methods ON gks_acc_inv.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_eshop_fiscal_position ON gks_acc_inv.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_acc_inv.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_acc_inv.ma_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_acc_inv.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_lang ON gks_acc_inv.user_lang = gks_lang.id_lang)
LEFT JOIN gks_aade_skopos_diakinisis ON gks_acc_inv.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_acc_inv.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_acc_inv.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_acc_inv.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_acc_inv.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_warehouses AS gks_warehouses_from ON gks_acc_inv.warehouses_id_from = gks_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_warehouses_to ON gks_acc_inv.warehouses_id_to = gks_warehouses_to.id_warehouse)

LEFT JOIN gks_country as gks_country_load ON gks_acc_inv.load_country_id = gks_country_load.id_country)
LEFT JOIN gks_nomoi as gks_nomoi_load ON gks_acc_inv.load_nomos_id = gks_nomoi_load.id_nomos)
LEFT JOIN gks_country as gks_country_deli ON gks_acc_inv.deli_country_id = gks_country_deli.id_country)
LEFT JOIN gks_nomoi as gks_nomoi_deli ON gks_acc_inv.deli_nomos_id = gks_nomoi_deli.id_nomos)
LEFT JOIN gks_aade_paroxos ON gks_acc_inv.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos)
LEFT JOIN gks_pos ON gks_acc_inv.pos_id = gks_pos.id_pos) 
LEFT JOIN gks_erp_app_mobile ON gks_acc_inv.erp_app_mobile_id = gks_erp_app_mobile.id_erp_app_mobile

";


//echo '222222222';
return $sql;
  
}

function get_acc_inv_details_txt($id, &$myarray=array(),&$myarray_line=array()) {
  global $db_link;
  $myarray=array();
  
  
  
    
  $sql="SELECT gks_acc_inv_products.*,
  gks_eshop_products.product_code, gks_eshop_products.product_photo, gks_eshop_products.product_descr as product_descr_from_p, gks_eshop_products.product_descr_small, gks_eshop_products.product_descr_big,
  gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
  gks_eshop_pricelist.pricelist_descr
  FROM ((gks_acc_inv_products 
  LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_eshop_fpa ON gks_acc_inv_products.product_fpa_id = gks_eshop_fpa.id_fpa)
  LEFT JOIN gks_eshop_pricelist ON gks_acc_inv_products.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist
  WHERE gks_acc_inv_products.acc_inv_id=".$id."
  ORDER BY gks_acc_inv_products.id_acc_inv_product;";
  //gks_orders_products.product_set
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);return 'sql error';}
  while ($eidos = $result->fetch_assoc()) {
    $myarray[] = array($eidos['product_descr'], $eidos['product_quantity']);
  }
  
  $ret='';
  $myarray_line=array();
  foreach ($myarray as $value) {
    $ret.=$value[0].': '.$value[1].'<br>';
    $myarray_line[]=trim_gks($value[0].': '.$value[1]);
  } 
  
  if ($ret!='') $ret=substr($ret, 0, strlen($ret)-4);

  return $ret;
}

function gks_acc_inv_cancel_create($old_id, $check_only) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  
  $sql="select id_acc_inv from gks_acc_inv where cancel_for_acc_inv_id=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    debug_mail(false,'emptyl',                                     gks_lang('Αυτό το παραστατικό έχει ήδη το αντίστοιχο ακυρωτικό παραστατικό').'<br><a href="admin-acc-inv-item.php?id="'.$row['id_acc_inv'].'" class="gks_link">'.gks_lang('Προβολή').'</a>');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το παραστατικό έχει ήδη το αντίστοιχο ακυρωτικό παραστατικό').'<br><a href="admin-acc-inv-item.php?id="'.$row['id_acc_inv'].'" class="gks_link">'.gks_lang('Προβολή').'</a>'));
    echo json_encode($return); die();}
  
  
  
  $sql="select * from gks_acc_inv where id_acc_inv=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'emptyl',                                     gks_lang('Δεν βρέθηκε το παραστατικό προς ακύρωση'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το παραστατικό προς ακύρωση')));
    echo json_encode($return); die();}
  
  $old_row = $result->fetch_assoc();
//  if ($old_row['inv_state']!='080listing' and $old_row['inv_state']!='090ekdosi' and $old_row['inv_state']!='100payment') {
//    debug_mail(false,'emptyl',                                     gks_lang('Η κατάσταση του παραστατικού δεν είναι <b>Καταχώρηση</b> ή <b>Έκδοση</b> ή <b>Εξοφλημένο</b> και άρα δεν μπορεί να ακυρωθεί'));
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η κατάσταση του παραστατικού δεν είναι <b>Καταχώρηση</b> ή <b>Έκδοση</b> ή <b>Εξοφλημένο</b> και άρα δεν μπορεί να ακυρωθεί')));
//    echo json_encode($return); die();}
    
  //echo '<pre>'.$old_row['aade_paroxos_id'].'|'.$old_row['aade_invoicemark'];die();
  if (intval($old_row['aade_paroxos_id'])>0 and trim_gks($old_row['aade_invoicemark'])) {
    debug_mail(false,'emptyl',                                     gks_lang('Αυτό το παραστατικό έχει αποσταλεί στο πάροχο και δεν μπορεί να ακυρωθεί<br>Μπορείτε όμως να εκδώσετε πιστωτικό'),$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το παραστατικό έχει αποσταλεί στο πάροχο και δεν μπορεί να ακυρωθεί<br>Μπορείτε όμως να εκδώσετε πιστωτικό')));
    echo json_encode($return); die();}
  
  
  
  $company_id=$old_row['company_id'];
  $company_sub_id=$old_row['company_sub_id'];
  
  $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira,gks_acc_seires.seira_code
  FROM gks_acc_journal 
  LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id
  WHERE gks_acc_journal.acc_eidos_parastatikou_id=702 
  and gks_acc_journal.id_acc_journal>0
  and gks_acc_journal.is_disable=0
  and gks_acc_seires.id_acc_seira>0
  and gks_acc_seires.is_xeirografi=0
  and gks_acc_seires.is_disable=0
  AND gks_acc_journal.company_id=".$company_id." 
  AND gks_acc_journal.company_sub_id=".$company_sub_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'emptyl',                                     gks_lang('Δεν βρέθηκε ακυρωτικό ημερολόγιο και σειρά μηχανογραφημένη για αυτήν την εταιρεία/υποκατάστημα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ακυρωτικό ημερολόγιο και σειρά μηχανογραφημένη για αυτήν την εταιρεία/υποκατάστημα')));
    echo json_encode($return); die();}
  $row=$result->fetch_assoc();
  $new_inv_acc_journal_id=$row['id_acc_journal'];
  $new_inv_acc_seira_id=$row['id_acc_seira'];
  $new_inv_acc_seira_code=trim_gks($row['seira_code']);
  //echo $new_inv_acc_journal_id.'|'.$new_inv_acc_seira_id.'|'.$new_inv_acc_seira_code."\n";
  
  if ($check_only) return true;
  
  $new_inv_guid=guid_for_acc_inv();
  //echo $new_inv_guid."\n";
  
  $sql="INSERT INTO gks_acc_inv (inv_guid, inv_date, mydate_add, mydate_edit, 
  user_id_add, user_id_edit, myip, 
  inv_acc_journal_id, inv_acc_seira_id, 
  inv_acc_seira_code, inv_state, 
  cancel_for_acc_inv_id,
  company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos,ma_arithmos,
  ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
  address_extra, destination_data_name, destination_data_phone, destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
  destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, idiotites,
  gks_price_original_net, gks_price_net, gks_price_fpa, gks_price_netfpa, gks_price_total, fiscal_position_id, pricelist_id, is_other, other_first_name,
  other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
  other_ma_nomos_id, products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
  products_need_apostoli, products_need_pliromi, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
  kostos_pliromis, tropos_pliromis, kostos_pliromis_json, delivery_id_8, coupons, def_ekptosi,
  totalWithheldAmount, totalOtherTaxesAmount, totalStampDutyamount, totalFeesAmount, totalDeductionsAmount
  )
  SELECT '".$new_inv_guid."' as inv_guid, now() as inv_date, now() as mydate_add, now() as mydate_edit,
  ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
  ".$new_inv_acc_journal_id." as inv_acc_journal_id, ".$new_inv_acc_seira_id." as inv_acc_seira_id, 
  '".$db_link->escape_string($new_inv_acc_seira_code)."' as inv_acc_seira_code, '010draft' as inv_state, 
  ".$old_id." as cancel_for_acc_inv_id,
  company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos,ma_arithmos,
  ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
  address_extra, destination_data_name, destination_data_phone, 
  destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
  destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, idiotites,
  gks_price_original_net, gks_price_net, gks_price_fpa, gks_price_netfpa, gks_price_total, fiscal_position_id, pricelist_id, is_other, other_first_name,
  other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
  other_ma_nomos_id, products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
  products_need_apostoli, products_need_pliromi, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
  kostos_pliromis, tropos_pliromis, kostos_pliromis_json, delivery_id_8, coupons, def_ekptosi,
  totalWithheldAmount, totalOtherTaxesAmount, totalStampDutyamount, totalFeesAmount, totalDeductionsAmount
  FROM gks_acc_inv
  WHERE id_acc_inv=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $new_id = $db_link->insert_id;  
  //echo $new_id."\n";
  
  $sql="select id_acc_inv_product from gks_acc_inv_products where acc_inv_id=".$old_id." order by id_acc_inv_product";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $old_product_ids=array();
  while ($row = $result->fetch_assoc()) {  
    $old_product_ids[]=$row['id_acc_inv_product'];
  }
  
  $map_products=array();
  foreach ($old_product_ids as $old_product_id) {
    $sql="INSERT INTO gks_acc_inv_products ( 
    mydate_add, mydate_edit, user_id_add, user_id_edit, myip, 
    acc_inv_id, 
    product_aa, product_set, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
    product_is_simple_download, product_need_apostoli, product_fpa_base_id, product_fpa_id, product_fpa_ejeresi_id, product_fpa_pososto, product_fpa_id_json,
    product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
    product_ogos_y, product_ogos_z, product_category_ids, product_sheets, product_quantity, product_price_include_vat, product_price_start_peritem_db,
    product_price_start_peritem_net, product_price_start_peritem_fpa, product_price_start_peritem_total, product_price_start_all_net, product_price_start_all_fpa,
    product_price_start_all_total, product_price_final_peritem_db, product_price_final_peritem_net, product_price_final_peritem_fpa,
    product_price_final_peritem_total, product_price_final_all_net, product_price_final_all_fpa, product_price_final_all_total, product_price_ekptosi_net,
    product_price_ekptosi_pososto, product_pricelist_item_id, product_pricelist_item_descr, product_pricelist_item_percent, product_price_coupon_use,
    product_price_coupon_use_disabled, product_comments, product_withheldPercentCategory, product_withheldAmount, product_stampDutyPercentCategory,
    product_stampDutyAmount, product_feesPercentCategory, product_feesAmount, product_otherTaxesPercentCategory, product_otherTaxesAmount,
    product_deductionsAmount, aade_lineComments
    )
    SELECT now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,
    '".$db_link->escape_string($gkIP)."' as myip,
    ".$new_id." as acc_inv_id,
    product_aa, product_set, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
    product_is_simple_download, product_need_apostoli, product_fpa_base_id, product_fpa_id, product_fpa_ejeresi_id, product_fpa_pososto, product_fpa_id_json,
    product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
    product_ogos_y, product_ogos_z, product_category_ids, product_sheets, product_quantity, product_price_include_vat, product_price_start_peritem_db,
    product_price_start_peritem_net, product_price_start_peritem_fpa, product_price_start_peritem_total, product_price_start_all_net, product_price_start_all_fpa,
    product_price_start_all_total, product_price_final_peritem_db, product_price_final_peritem_net, product_price_final_peritem_fpa,
    product_price_final_peritem_total, product_price_final_all_net, product_price_final_all_fpa, product_price_final_all_total, product_price_ekptosi_net,
    product_price_ekptosi_pososto, product_pricelist_item_id, product_pricelist_item_descr, product_pricelist_item_percent, product_price_coupon_use,
    product_price_coupon_use_disabled, product_comments, product_withheldPercentCategory, product_withheldAmount, product_stampDutyPercentCategory,
    product_stampDutyAmount, product_feesPercentCategory, product_feesAmount, product_otherTaxesPercentCategory, product_otherTaxesAmount,
    product_deductionsAmount, aade_lineComments
    FROM gks_acc_inv_products
    where id_acc_inv_product=".$old_product_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    
    $new_product_id = $db_link->insert_id;  
    $map_products[]=array('old' => $old_product_id, 'new' => $new_product_id);
  }
  //echo print_r($map_products,true)."\n";
  
  
  foreach ($map_products as $map_product) {
    $sql="INSERT INTO gks_acc_inv_products_income (
    acc_inv_product_id, aade_typos_xarakt_esodon_id, aade_katigoria_xarakt_esodon_id, acc_inv_product_income_ammount 
    )
    SELECT ".$map_product['new']." as acc_inv_product_id, aade_typos_xarakt_esodon_id, aade_katigoria_xarakt_esodon_id, acc_inv_product_income_ammount
    FROM gks_acc_inv_products_income
    WHERE acc_inv_product_id=".$map_product['old']."
    ORDER BY id_acc_inv_product_income";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  

    $sql="INSERT INTO gks_acc_inv_products_expenses (
    acc_inv_product_id, aade_typos_xarakt_eksodon_id, aade_katigoria_xarakt_eksodon_id, acc_inv_product_expenses_ammount
    )
    SELECT ".$map_product['new']." as acc_inv_product_id, aade_typos_xarakt_eksodon_id, aade_katigoria_xarakt_eksodon_id, acc_inv_product_expenses_ammount
    FROM gks_acc_inv_products_expenses
    WHERE acc_inv_product_id=".$map_product['old']."
    ORDER BY id_acc_inv_product_expenses";
    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
  }
  
  

  $sql="insert into gks_acc_inv_correlated_invoices (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    acc_inv_id,
    coi_mark,
    coi_acc_inv_id,
    coi_acc_pay_id,
    coi_whi_mov_id,
    coi_aa
  ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$new_id.",
    '".$db_link->escape_string($old_row['aade_invoicemark'])."',
    ".$old_id.",
    0,
    0,
    0
  )";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
      
  $sql="insert into gks_object_rel (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name1,object_id1,object_name2,object_id2
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  'gks_acc_inv',".$old_id.",'gks_acc_inv',".$new_id."
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
          
  //echo '<pre>dddddddd';die();
  
  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
  ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
    
  return $new_id;
  
}
function gks_acc_inv_credit_memo_create($old_id, $check_only) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  
//  $sql="select id_acc_inv from gks_acc_inv where credit_memo_for_acc_inv_id=".$old_id;
//  $result = $db_link->query($sql);  
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die(); }  
//  if ($result->num_rows>0) {
//    $row = $result->fetch_assoc();
//    debug_mail(false,'emptyl',                                     gks_lang('Αυτό το παραστατικό έχει ήδη το συσχετιζόμενο πιστωτικό παραστατικό').'<br><a href="admin-acc-inv-item.php?id="'.$row['id_acc_inv'].'" class="gks_link">'.gks_lang('Προβολή').'</a>');
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το παραστατικό έχει ήδη το συσχετιζόμενο πιστωτικό παραστατικό').'<br><a href="admin-acc-inv-item.php?id="'.$row['id_acc_inv'].'" class="gks_link">'.gks_lang('Προβολή').'</a>'));
//    echo json_encode($return); die();}
  
  

  $sql="SELECT gks_acc_inv.*,gks_acc_eidi_parastatikon.credit_acc_eidos_parastatikou_id,
  gks_acc_eidi_parastatikon_creadit.eidos_parastatikou_descr as credit_descr
  FROM ((gks_acc_inv 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon AS gks_acc_eidi_parastatikon_creadit ON gks_acc_eidi_parastatikon.credit_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon_creadit.id_acc_eidos_parastatikou
  where id_acc_inv=".$old_id;

  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'emptyl',                                     gks_lang('Δεν βρέθηκε το παραστατικό που θα αφορά το πιστωτικό παραστατικό'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το παραστατικό που θα αφορά το πιστωτικό παραστατικό')));
    echo json_encode($return); die();}
  
  $old_row = $result->fetch_assoc();
//  if ($old_row['inv_state']!='080listing' and $old_row['inv_state']!='090ekdosi' and $old_row['inv_state']!='100payment') {
//    debug_mail(false,'emptyl',                                     gks_lang('Η κατάσταση του παραστατικού δεν είναι <b>Καταχώρηση</b> ή <b>Έκδοση</b> ή <b>Εξοφλημένο</b> και άρα δεν μπορεί να ακυρωθεί'));
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η κατάσταση του παραστατικού δεν είναι <b>Καταχώρηση</b> ή <b>Έκδοση</b> ή <b>Εξοφλημένο</b> και άρα δεν μπορεί να ακυρωθεί')));
//    echo json_encode($return); die();}
    
  $company_id=$old_row['company_id'];
  $company_sub_id=$old_row['company_sub_id'];
  $credit_acc_eidos_parastatikou_id=intval($old_row['credit_acc_eidos_parastatikou_id']);
  $credit_descr=trim_gks($old_row['credit_descr']);

  if ($credit_acc_eidos_parastatikou_id<=0) {
    debug_mail(false,'emptyl',                                     gks_lang('Δεν μπορεί να εκτελεστεί αυτή η εντολή'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να εκτελεστεί αυτή η εντολή')));
    echo json_encode($return); die();}

  
  $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira,gks_acc_seires.seira_code
  FROM ((gks_acc_journal 
  LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  WHERE eidos_parastatikou_type_id in (1,2,5) and id_acc_eidos_parastatikou not in (702,703,704)
  and gks_acc_journal.id_acc_journal>0
  and gks_acc_journal.is_disable=0
  and gks_acc_seires.id_acc_seira>0
  and gks_acc_seires.is_xeirografi=0
  and gks_acc_seires.is_disable=0
  and id_acc_eidos_parastatikou=".$credit_acc_eidos_parastatikou_id."
  AND gks_acc_journal.company_id=".$company_id." 
  AND gks_acc_journal.company_sub_id=".$company_sub_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    $tmpmsg=gks_lang('Δεν βρέθηκε πιστωτικό συσχετιζόμενο ημερολόγιο με τύπο παραστατικού <b>[1]</b> και μηχανογραφημένη σειρά για αυτήν την εταιρεία/υποκατάστημα');
    $tmpmsg=str_replace('[1]',$credit_descr,$tmpmsg);
    debug_mail(false,'emptyl',$tmpmsg);
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die();}
  $row=$result->fetch_assoc();
  $new_inv_acc_journal_id=$row['id_acc_journal'];
  $new_inv_acc_seira_id=$row['id_acc_seira'];
  $new_inv_acc_seira_code=trim_gks($row['seira_code']);
  //echo $new_inv_acc_journal_id.'|'.$new_inv_acc_seira_id.'|'.$new_inv_acc_seira_code."\n";
  
  if ($check_only) return true;
  
  $new_inv_guid=guid_for_acc_inv();
  //echo $new_inv_guid."\n"; die();
  
  $sql="INSERT INTO gks_acc_inv (inv_guid, inv_date, mydate_add, mydate_edit, 
  user_id_add, user_id_edit, myip, 
  inv_acc_journal_id, inv_acc_seira_id, 
  inv_acc_seira_code, inv_state, 
  credit_memo_for_acc_inv_id,
  company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos,ma_arithmos,
  ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
  address_extra, destination_data_name, destination_data_phone, destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
  destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, idiotites,
  gks_price_original_net, gks_price_net, gks_price_fpa, gks_price_netfpa, gks_price_total, fiscal_position_id, pricelist_id, is_other, other_first_name,
  other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
  other_ma_nomos_id, products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
  products_need_apostoli, products_need_pliromi, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
  kostos_pliromis, tropos_pliromis, kostos_pliromis_json, delivery_id_8, coupons, def_ekptosi,
  totalWithheldAmount, totalOtherTaxesAmount, totalStampDutyamount, totalFeesAmount, totalDeductionsAmount,
  affect_balance,affect_balance_all_poso,affect_balance_all_poso_type,affect_balance_pros,
  contract_reference,project_reference
  )
  SELECT '".$new_inv_guid."' as inv_guid, now() as inv_date, now() as mydate_add, now() as mydate_edit,
  ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
  ".$new_inv_acc_journal_id." as inv_acc_journal_id, ".$new_inv_acc_seira_id." as inv_acc_seira_id, 
  '".$db_link->escape_string($new_inv_acc_seira_code)."' as inv_acc_seira_code, '010draft' as inv_state, 
  ".$old_id." as credit_memo_for_acc_inv_id,
  company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos, ma_arithmos,
  ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
  address_extra, destination_data_name, destination_data_phone, destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
  destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, idiotites,
  gks_price_original_net, gks_price_net, gks_price_fpa, gks_price_netfpa, gks_price_total, fiscal_position_id, pricelist_id, is_other, other_first_name,
  other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
  other_ma_nomos_id, products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
  products_need_apostoli, products_need_pliromi, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
  kostos_pliromis, tropos_pliromis, kostos_pliromis_json, delivery_id_8, coupons, def_ekptosi,
  totalWithheldAmount, totalOtherTaxesAmount, totalStampDutyamount, totalFeesAmount, totalDeductionsAmount,
  affect_balance,affect_balance_all_poso,affect_balance_all_poso_type,-affect_balance_pros as affect_balance_pros_meion,
  contract_reference,project_reference
  FROM gks_acc_inv
  WHERE id_acc_inv=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $new_id = $db_link->insert_id;  
  //echo $new_id."\n";die();
  
  $sql="select id_acc_inv_product from gks_acc_inv_products where acc_inv_id=".$old_id." order by id_acc_inv_product";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $old_product_ids=array();
  while ($row = $result->fetch_assoc()) {  
    $old_product_ids[]=$row['id_acc_inv_product'];
  }
  
  $map_products=array();
  foreach ($old_product_ids as $old_product_id) {
    $sql="INSERT INTO gks_acc_inv_products ( 
    mydate_add, mydate_edit, user_id_add, user_id_edit, myip, 
    acc_inv_id, 
    product_aa, product_set, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
    product_is_simple_download, product_need_apostoli, product_fpa_base_id, product_fpa_id, product_fpa_ejeresi_id, product_fpa_pososto, product_fpa_id_json,
    product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
    product_ogos_y, product_ogos_z, product_category_ids, product_sheets, product_quantity, product_price_include_vat, product_price_start_peritem_db,
    product_price_start_peritem_net, product_price_start_peritem_fpa, product_price_start_peritem_total, product_price_start_all_net, product_price_start_all_fpa,
    product_price_start_all_total, product_price_final_peritem_db, product_price_final_peritem_net, product_price_final_peritem_fpa,
    product_price_final_peritem_total, product_price_final_all_net, product_price_final_all_fpa, product_price_final_all_total, product_price_ekptosi_net,
    product_price_ekptosi_pososto, product_pricelist_item_id, product_pricelist_item_descr, product_pricelist_item_percent, product_price_coupon_use,
    product_price_coupon_use_disabled, product_comments, product_withheldPercentCategory, product_withheldAmount, product_stampDutyPercentCategory,
    product_stampDutyAmount, product_feesPercentCategory, product_feesAmount, product_otherTaxesPercentCategory, product_otherTaxesAmount,
    product_deductionsAmount, aade_lineComments
    )
    SELECT now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,
    '".$db_link->escape_string($gkIP)."' as myip,
    ".$new_id." as acc_inv_id,
    product_aa, product_set, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
    product_is_simple_download, product_need_apostoli, product_fpa_base_id, product_fpa_id, product_fpa_ejeresi_id, product_fpa_pososto, product_fpa_id_json,
    product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
    product_ogos_y, product_ogos_z, product_category_ids, product_sheets, product_quantity, product_price_include_vat, product_price_start_peritem_db,
    product_price_start_peritem_net, product_price_start_peritem_fpa, product_price_start_peritem_total, product_price_start_all_net, product_price_start_all_fpa,
    product_price_start_all_total, product_price_final_peritem_db, product_price_final_peritem_net, product_price_final_peritem_fpa,
    product_price_final_peritem_total, product_price_final_all_net, product_price_final_all_fpa, product_price_final_all_total, product_price_ekptosi_net,
    product_price_ekptosi_pososto, product_pricelist_item_id, product_pricelist_item_descr, product_pricelist_item_percent, product_price_coupon_use,
    product_price_coupon_use_disabled, product_comments, product_withheldPercentCategory, product_withheldAmount, product_stampDutyPercentCategory,
    product_stampDutyAmount, product_feesPercentCategory, product_feesAmount, product_otherTaxesPercentCategory, product_otherTaxesAmount,
    product_deductionsAmount, aade_lineComments
    FROM gks_acc_inv_products
    where id_acc_inv_product=".$old_product_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    
    $new_product_id = $db_link->insert_id;  
    $map_products[]=array('old' => $old_product_id, 'new' => $new_product_id);
  }
  //echo print_r($map_products,true)."\n";
  
  
  foreach ($map_products as $map_product) {
    $sql="INSERT INTO gks_acc_inv_products_income (
    acc_inv_product_id, aade_typos_xarakt_esodon_id, aade_katigoria_xarakt_esodon_id, acc_inv_product_income_ammount 
    )
    SELECT ".$map_product['new']." as acc_inv_product_id, aade_typos_xarakt_esodon_id, aade_katigoria_xarakt_esodon_id, acc_inv_product_income_ammount
    FROM gks_acc_inv_products_income
    WHERE acc_inv_product_id=".$map_product['old']."
    ORDER BY id_acc_inv_product_income";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  

    $sql="INSERT INTO gks_acc_inv_products_expenses (
    acc_inv_product_id, aade_typos_xarakt_eksodon_id, aade_katigoria_xarakt_eksodon_id, acc_inv_product_expenses_ammount
    )
    SELECT ".$map_product['new']." as acc_inv_product_id, aade_typos_xarakt_eksodon_id, aade_katigoria_xarakt_eksodon_id, acc_inv_product_expenses_ammount
    FROM gks_acc_inv_products_expenses
    WHERE acc_inv_product_id=".$map_product['old']."
    ORDER BY id_acc_inv_product_expenses";
    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
  }
  
  
  $sql="insert into gks_acc_inv_correlated_invoices (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    acc_inv_id,
    coi_mark,
    coi_acc_inv_id,
    coi_acc_pay_id,
    coi_whi_mov_id,
    coi_aa
  ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$new_id.",
    '".$db_link->escape_string($old_row['aade_invoicemark'])."',
    ".$old_id.",
    0,
    0,
    0
  )";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 

  $sql="insert into gks_object_rel (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name1,object_id1,object_name2,object_id2
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  'gks_acc_inv',".$old_id.",'gks_acc_inv',".$new_id."
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
          
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
  ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
    
  return $new_id;
  
}
function gks_inv_sxolio_log_prepare($id,&$row_old,&$products_old,&$extra_address_old) {
  global $db_link;
  
  //print '<pre>pppppppp ';print_r($row_old);die();

  $sql=select_gks_acc_inv()." where id_acc_inv=".$id; 
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
  $products_old=array();
  $extra_address_old=array();
  
  $sql="SELECT gks_acc_inv_products.*, gks_monades_metrisis.monada_descr, gks_eshop_fpa_base.fpa_base_descr
  FROM (gks_acc_inv_products 
  LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada)
  LEFT JOIN gks_eshop_fpa_base ON gks_acc_inv_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base
  WHERE gks_acc_inv_products.acc_inv_id=".$id."
  ORDER BY gks_acc_inv_products.product_aa;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row = $result->fetch_assoc()) {
    $products_old[]=$row;
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
    
  
}

function gks_inv_sxolio_log($id,$row_old,$products_old,$extra_address_old,$sxolio_log_start,$myparams,$gks_custom_row_old) {
  global $db_link;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_CRM_ENABLE;
  
  //print '<pre>lllllllll ';print_r($row_old);die();


  $ret_aade_errors='';
  if (isset($myparams['ret_aade_errors'])) $ret_aade_errors=trim_gks($myparams['ret_aade_errors']);
  //echo '<pre>ddddddddddd '.$ret_aade_errors;die();
  
  $sql=select_gks_acc_inv()." where id_acc_inv=".$id." limit 1"; 
  
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
  
  $sql="SELECT gks_acc_inv_products.*, gks_monades_metrisis.monada_descr, gks_eshop_fpa_base.fpa_base_descr
  FROM (gks_acc_inv_products 
  LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada)
  LEFT JOIN gks_eshop_fpa_base ON gks_acc_inv_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base
  WHERE gks_acc_inv_products.acc_inv_id=".$id."
  ORDER BY gks_acc_inv_products.product_aa;";
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

  if (trim_gks($row_old['inv_acc_number_int']) != trim_gks($row_new['inv_acc_number_int'])) 
    $sxolio_log.=gks_lang('Αριθμός').': <b>'.$row_old['inv_acc_number_int'].'</b> [[-r]] <b>'.$row_new['inv_acc_number_int'].'</b>'.'<br>';

  if (showDate(strtotime($row_old['inv_date']), 'd/m/Y H:i', 1)!=showDate(strtotime($row_new['inv_date']), 'd/m/Y H:i', 1)) 
    $sxolio_log.=gks_lang('Ημερομηνία').': <b>'.showDate(strtotime($row_old['inv_date']), 'd/m/Y H:i', 1).'</b> [[-r]] <b>'.showDate(strtotime($row_new['inv_date']), 'd/m/Y H:i', 1).'</b>'.'<br>';

  if ($row_old['inv_state'].'' != $row_new['inv_state'].'') 
    $sxolio_log.=gks_lang('Κατάσταση').': <span class="acc_inv_state_'.$row_old['inv_state'].'">'.getAccInvStateDescr($row_old['inv_state']).'</span> [[-r]] '.
    '<span class="acc_inv_state_'.$row_new['inv_state'].'">'.getAccInvStateDescr($row_new['inv_state']).'</span>'.'<br>';

  if (trim_gks($row_old['fiscal_position_descr']) != trim_gks($row_new['fiscal_position_descr'])) 
    $sxolio_log.=gks_lang('Φορολογική Θέση').': <b>'.$row_old['fiscal_position_descr'].'</b> [[-r]] <b>'.$row_new['fiscal_position_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['pricelist_descr']) != trim_gks($row_new['pricelist_descr'])) 
    $sxolio_log.=gks_lang('Τιμοκατάλογος').': <b>'.$row_old['pricelist_descr'].'</b> [[-r]] <b>'.$row_new['pricelist_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['def_ekptosi']) != trim_gks($row_new['def_ekptosi'])) 
    $sxolio_log.=gks_lang('Προεπιλεγμένη έκπτωση').': <b>'.myNumberFormatNo0Local($row_old['def_ekptosi']).'%'.'</b> [[-r]] <b>'.myNumberFormatNo0Local($row_new['def_ekptosi']).'%'.'</b>'.'<br>';
  
  if (trim_gks($row_old['coupons']) != trim_gks($row_new['coupons'])) {
    $coupons_old='';
    $coupons_parts=explode('|',trim_gks($row_old['coupons']));
    foreach ($coupons_parts as $value) {
      $value=trim_gks($value);
      if ($value!='') $coupons_old.='<span class="coupons_span"><span class="coupons text-sm">'.$value.'</span></span> ';
    }
    $coupons_new='';
    $coupons_parts=explode('|',trim_gks($row_new['coupons']));
    foreach ($coupons_parts as $value) {
      $value=trim_gks($value);
      if ($value!='') $coupons_new.='<span class="coupons_span"><span class="coupons text-sm">'.$value.'</span></span> ';
    }
    $sxolio_log.=gks_lang('Κουπόνια').': '.$coupons_old.' [[-r]] '.$coupons_new.'<br>';
  }



  //echo '<pre>'.$row_new['company_title']; die();
  
  
  
  if ((isset($row_old['gks_nickname']) and isset($row_old['gks_nickname']) == false) or 
      (isset($row_old['gks_nickname']) == false and isset($row_old['gks_nickname'])) or 
      $row_old['gks_nickname'] != $row_new['gks_nickname']) 
    $sxolio_log.=gks_lang('Επαφή').': <b>'.(isset($row_old['gks_nickname']) ? $row_old['gks_nickname'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['gks_nickname']) ? $row_new['gks_nickname'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['user_first_name']) != trim_gks($row_new['user_first_name'])) 
    $sxolio_log.=gks_lang('Όνομα').': <b>'.$row_old['user_first_name'].'</b> [[-r]] <b>'.$row_new['user_first_name'].'</b>'.'<br>';
  
  if (trim_gks($row_old['user_last_name']) != trim_gks($row_new['user_last_name'])) 
    $sxolio_log.=gks_lang('Επώνυμο').': <b>'.$row_old['user_last_name'].'</b> [[-r]] <b>'.$row_new['user_last_name'].'</b>'.'<br>';

  if (trim_gks($row_old['user_email']) != trim_gks($row_new['user_email'])) 
    $sxolio_log.=gks_lang('email').': <b>'.$row_old['user_email'].'</b> [[-r]] <b>'.$row_new['user_email'].'</b>'.'<br>';

  if (trim_gks($row_old['user_mobile']) != trim_gks($row_new['user_mobile'])) 
    $sxolio_log.=gks_lang('Τηλέφωνο').': <b>'.$row_old['user_mobile'].'</b> [[-r]] <b>'.$row_new['user_mobile'].'</b>'.'<br>';

  if (trim_gks($row_old['lang_name']) != trim_gks($row_new['lang_name'])) 
    $sxolio_log.=gks_lang('Γλώσσα').': <b>'.$row_old['lang_name'].'</b> [[-r]] <b>'.$row_new['lang_name'].'</b>'.'<br>';

  if (trim_gks($row_old['eponimia']) != trim_gks($row_new['eponimia'])) 
    $sxolio_log.=gks_lang('Επωνυμία').': <b>'.$row_old['eponimia'].'</b> [[-r]] <b>'.$row_new['eponimia'].'</b>'.'<br>';

  if (trim_gks($row_old['title']) != trim_gks($row_new['title'])) 
    $sxolio_log.=gks_lang('Τίτλος').': <b>'.$row_old['title'].'</b> [[-r]] <b>'.$row_new['title'].'</b>'.'<br>';



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
  
  if (trim_gks($ee_initials_old.$row_old['afm']) != trim_gks($ee_initials_new.$row_new['afm'])) {
    $sxolio_log.=gks_lang('ΑΦΜ').': <b>'.$ee_initials_old.' '.$row_old['afm'].'</b> [[-r]] <b>'.$ee_initials_new.' '.$row_new['afm'].'</b>'.'<br>';
  }

  if (trim_gks($row_old['doy']) != trim_gks($row_new['doy'])) 
    $sxolio_log.=gks_lang('ΔΟΥ').': <b>'.$row_old['doy'].'</b> [[-r]] <b>'.$row_new['doy'].'</b>'.'<br>';

  if (trim_gks($row_old['epaggelma']) != trim_gks($row_new['epaggelma'])) 
    $sxolio_log.=gks_lang('Επάγγελμα').': <b>'.$row_old['epaggelma'].'</b> [[-r]] <b>'.$row_new['epaggelma'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_odos']) != trim_gks($row_new['ma_odos'])) 
    $sxolio_log.=gks_lang('Οδός').': <b>'.$row_old['ma_odos'].'</b> [[-r]] <b>'.$row_new['ma_odos'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_arithmos']) != trim_gks($row_new['ma_arithmos'])) 
    $sxolio_log.=gks_lang('Αριθμός').': <b>'.$row_old['ma_arithmos'].'</b> [[-r]] <b>'.$row_new['ma_arithmos'].'</b>'.'<br>';



  if (trim_gks($row_old['ma_orofos']) != trim_gks($row_new['ma_orofos'])) 
    $sxolio_log.=gks_lang('Όροφος').': <b>'.$row_old['ma_orofos'].'</b> [[-r]] <b>'.$row_new['ma_orofos'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_perioxi']) != trim_gks($row_new['ma_perioxi'])) 
    $sxolio_log.=gks_lang('Περιοχή').': <b>'.$row_old['ma_perioxi'].'</b> [[-r]] <b>'.$row_new['ma_perioxi'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_poli']) != trim_gks($row_new['ma_poli'])) 
    $sxolio_log.=gks_lang('Πόλη').': <b>'.$row_old['ma_poli'].'</b> [[-r]] <b>'.$row_new['ma_poli'].'</b>'.'<br>';

  if (trim_gks($row_old['ma_tk']) != trim_gks($row_new['ma_tk'])) 
    $sxolio_log.=gks_lang('TK').': <b>'.$row_old['ma_tk'].'</b> [[-r]] <b>'.$row_new['ma_tk'].'</b>'.'<br>';

  if (trim_gks($row_old['country_name']) != trim_gks($row_new['country_name'])) 
    $sxolio_log.=gks_lang('Χώρα').': <b>'.$row_old['country_name'].'</b> [[-r]] <b>'.$row_new['country_name'].'</b>'.'<br>';

  if (trim_gks($row_old['nomos_descr']) != trim_gks($row_new['nomos_descr'])) 
    $sxolio_log.=gks_lang('Νομός').': <b>'.$row_old['nomos_descr'].'</b> [[-r]] <b>'.$row_new['nomos_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['address_extra']) != trim_gks($row_new['address_extra'])) {
    
    $temp_old='';
    if ($row_old['address_extra']==-1) $temp_old=gks_lang('Αποστολή στην ίδια διεύθυνση');
    else {
      $sql="select ea_name from gks_users_extra_address where id_users_extra_address=".$row_old['address_extra'];
      $result_select = $db_link->query($sql);        
      if (!$result_select) {
        debug_mail(false,'error sql',$sql);
        die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      }
      if ($result_select->num_rows==1) {
        $row_select = $result_select->fetch_assoc();
        $temp_old=$row_select['ea_name'];
      }
    }
    
    $temp_new='';
    if ($row_new['address_extra']==-1) $temp_new=gks_lang('Αποστολή στην ίδια διεύθυνση');
    else {
      $sql="select ea_name from gks_users_extra_address where id_users_extra_address=".$row_new['address_extra'];
      $result_select = $db_link->query($sql);        
      if (!$result_select) {
        debug_mail(false,'error sql',$sql);
        die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      }
      if ($result_select->num_rows==1) {
        $row_select = $result_select->fetch_assoc();
        $temp_new=$row_select['ea_name'];
      }
    }
    $sxolio_log.=gks_lang('Αποστολή').': <b>'.$temp_old.'</b> [[-r]] <b>'.$temp_new.'</b>'.'<br>';
  }
  
  $check_ea=false;
  if ($row_old['address_extra']==-1 and $row_new['address_extra']>0) $check_ea=true;
  else if ($row_old['address_extra']>0 and $row_new['address_extra']>0) $check_ea=true;
  
  
  if ($check_ea) {
    if (isset($extra_address_old['ea_name'])==false) { //den exei oristei
      $extra_address_old=array('ea_name'=>'', 'ea_phone'=>'','ea_odos'=>'','ea_arithmos'=>'','ea_orofos'=>'','ea_perioxi'=>'','ea_poli'=>'','ea_tk'=>'','nomos_descr'=>'','country_name'=>'');
    }
    
    $extra_address_new=array('ea_name'=>'', 'ea_phone'=>'','ea_odos'=>'','ea_arithmos'=>'','ea_orofos'=>'','ea_perioxi'=>'','ea_poli'=>'','ea_tk'=>'','nomos_descr'=>'','country_name'=>'');
    if ($row_new['address_extra']>0) {
      $sql="SELECT gks_users_extra_address.*, gks_nomoi.nomos_descr, gks_country.country_name
      FROM (gks_users_extra_address 
      LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
      LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
      WHERE gks_users_extra_address.id_users_extra_address=".$row_new['address_extra'];
      $result_select = $db_link->query($sql);        
      if (!$result_select) {
        debug_mail(false,'error sql',$sql);
        die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      }
      if ($result_select->num_rows==1) {
        $extra_address_new = $result_select->fetch_assoc();
      }    
    }
  
    if (trim_gks($extra_address_old['ea_name']) != trim_gks($extra_address_new['ea_name'])) 
      $sxolio_log.=gks_lang('Όνομα Αποστολής').': <b>'.$extra_address_old['ea_name'].'</b> [[-r]] <b>'.$extra_address_new['ea_name'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_phone']) != trim_gks($extra_address_new['ea_phone'])) 
      $sxolio_log.=gks_lang('Τηλέφωνο Αποστολής').': <b>'.$extra_address_old['ea_phone'].'</b> [[-r]] <b>'.$extra_address_new['ea_phone'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_odos']) != trim_gks($extra_address_new['ea_odos'])) 
      $sxolio_log.=gks_lang('Οδός Αποστολής').': <b>'.$extra_address_old['ea_odos'].'</b> [[-r]] <b>'.$extra_address_new['ea_odos'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_arithmos']) != trim_gks($extra_address_new['ea_arithmos'])) 
      $sxolio_log.=gks_lang('Αριθμός οδού Αποστολής').': <b>'.$extra_address_old['ea_arithmos'].'</b> [[-r]] <b>'.$extra_address_new['ea_arithmos'].'</b>'.'<br>';
  
  
  
    if (trim_gks($extra_address_old['ea_perioxi']) != trim_gks($extra_address_new['ea_perioxi'])) 
      $sxolio_log.=gks_lang('Περιοχή Αποστολής').': <b>'.$extra_address_old['ea_perioxi'].'</b> [[-r]] <b>'.$extra_address_new['ea_perioxi'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_poli']) != trim_gks($extra_address_new['ea_poli'])) 
      $sxolio_log.=gks_lang('Πόλη Αποστολής').': <b>'.$extra_address_old['ea_poli'].'</b> [[-r]] <b>'.$extra_address_new['ea_poli'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['ea_tk']) != trim_gks($extra_address_new['ea_tk'])) 
      $sxolio_log.=gks_lang('TK Αποστολής').': <b>'.$extra_address_old['ea_tk'].'</b> [[-r]] <b>'.$extra_address_new['ea_tk'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['country_name']) != trim_gks($extra_address_new['country_name'])) 
      $sxolio_log.=gks_lang('Χώρα Αποστολής').': <b>'.$extra_address_old['country_name'].'</b> [[-r]] <b>'.$extra_address_new['country_name'].'</b>'.'<br>';
  
    if (trim_gks($extra_address_old['nomos_descr']) != trim_gks($extra_address_new['nomos_descr'])) 
      $sxolio_log.=gks_lang('Νομός Αποστολής').': <b>'.$extra_address_old['nomos_descr'].'</b> [[-r]] <b>'.$extra_address_new['nomos_descr'].'</b>'.'<br>';
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
      if ($pitem_old['id_acc_inv_product'] == $pitem_new['id_acc_inv_product']) {
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
      $sxolio_eidi_log.=gks_lang('Αφαιρέθηκε το είδος').': <b>'.$pitem_old['product_descr'].'</b><br>';
    }
  }
  
  foreach ($products_new as $key_new =>&$pitem_new) {
    if ($pitem_new['del'] == false and $pitem_new['k'] == -1) {
      $sxolio_eidi_log.=gks_lang('Προστέθηκε το είδος').': <b>'.$pitem_new['product_descr'].'</b> '.
      gks_lang('Ποσότητα').': <b>'.$pitem_new['product_quantity'].'</b> '.
      gks_lang('Αξία').': <b>'.number_format($pitem_new['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND).'</b> '.
      ($pitem_new['product_price_ekptosi_pososto'] == 0 ? '' : ' '.gks_lang('Έκπτωση').': <b>'.myNumberFormatNo0Local($pitem_new['product_price_ekptosi_pososto']).'%</b>').
      '<br>';
    }
  }
  
   // $sxolio_log.=gks_lang('Εσωτερική σημείωση για λογιστήριο').':<br><b>'.(isset($row_old['note_logistirio']) ? $row_old['note_logistirio'] : '').'</b> [[-r]] '.
   // '<b>'.(isset($row_new['note_logistirio']) ? $row_new['note_logistirio'] : '').'</b>'.'<br>';
  
  $pn=$products_new;
  foreach ($products_old as $p) {
    if ($p['k']>=0) {
      $item_descr_change='';
      if ($p['product_descr'] != $pn[$p['k']]['product_descr']) 
        $item_descr_change.='<b>'.$p['product_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_descr'].'</b>';
      
      $item_txt='';
      
      
      if ($p['product_set'] != $pn[$p['k']]['product_set']) 
        $item_txt.=gks_lang('Σετ').': <b>'.$p['product_set'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_set'].'</b> ';
      if ($p['product_comments'] != $pn[$p['k']]['product_comments']) 
        $item_txt.=gks_lang('Παρατηρήσεις').': <b>'.$p['product_comments'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_comments'].'</b> ';
      if ($p['product_sheets'] != $pn[$p['k']]['product_sheets']) 
        $item_txt.=gks_lang('Σελίδες').': <b>'.$p['product_sheets'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_sheets'].'</b> ';
      if ($p['product_quantity'] != $pn[$p['k']]['product_quantity']) 
        $item_txt.=gks_lang('Ποσότητα').': <b>'.$p['product_quantity'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_quantity'].'</b> ';
      if ($p['monada_descr'] != $pn[$p['k']]['monada_descr']) 
        $item_txt.=gks_lang('Μονάδα μέτρησης').': <b>'.$p['monada_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['monada_descr'].'</b> ';
      
      if ($p['product_price_ekptosi_pososto'] != $pn[$p['k']]['product_price_ekptosi_pososto']) 
        $item_txt.=gks_lang('Έκπτωση').': <b>'.myNumberFormatNo0Local($p['product_price_ekptosi_pososto']).'%</b> [[-r]] <b>'.myNumberFormatNo0Local($pn[$p['k']]['product_price_ekptosi_pososto']).'%</b> ';
      if ($p['product_price_final_all_net'] != $pn[$p['k']]['product_price_final_all_net']) 
        $item_txt.=gks_lang('Τιμή').': <b>'.myCurrencyFormat($p['product_price_final_all_net'], false).'</b> [[-r]] <b>'.myCurrencyFormat($pn[$p['k']]['product_price_final_all_net'], false).'</b> ';
      
      if ($p['fpa_base_descr']!=$pn[$p['k']]['fpa_base_descr'])
        $item_txt.=gks_lang('ΦΠΑ').': <b>'.$p['fpa_base_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['fpa_base_descr'].'</b> ';
      
      
        
        
        
      if ($item_txt != '') {
        $item_txt=trim_gks($item_txt);
        if ($item_descr_change!='') $item_txt=$item_descr_change.': '.$item_txt;
        else $item_txt=$p['product_descr'].': '.$item_txt;
        
        $sxolio_eidi_log.=$item_txt.'<br>';
        
      }
    }
  }  
  
  $sxolio_log.=$sxolio_eidi_log;


  if ($row_old['gks_price_net'] != $row_new['gks_price_net']) 
    $sxolio_log.=gks_lang('Υποσύνολο').': <b>'.myCurrencyFormat($row_old['gks_price_net']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_net']).'</b>'.'<br>';
  
  if ($row_old['gks_price_fpa'] != $row_new['gks_price_fpa']) 
    $sxolio_log.=gks_lang('ΦΠΑ').': <b>'.myCurrencyFormat($row_old['gks_price_fpa']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_fpa']).'</b>'.'<br>';
  
  if ($row_old['gks_price_netfpa'] != $row_new['gks_price_netfpa']) 
    $sxolio_log.=gks_lang('Μικτό σύνολο').': <b>'.myCurrencyFormat($row_old['gks_price_netfpa']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_netfpa']).'</b>'.'<br>';
  
  if ($row_old['totalWithheldAmount'] != $row_new['totalWithheldAmount']) 
    $sxolio_log.=gks_lang('Φόροι Παρακρατούμενοι').': <b>'.myCurrencyFormat($row_old['totalWithheldAmount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalWithheldAmount']).'</b>'.'<br>';
  
  if ($row_old['totalOtherTaxesAmount'] != $row_new['totalOtherTaxesAmount']) 
    $sxolio_log.=gks_lang('Λοιποί Φόροι').': <b>'.myCurrencyFormat($row_old['totalOtherTaxesAmount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalOtherTaxesAmount']).'</b>'.'<br>';
  
  if ($row_old['totalStampDutyamount'] != $row_new['totalStampDutyamount']) 
    $sxolio_log.=gks_lang('Ψηφιακό Τέλος συναλλαγής').': <b>'.myCurrencyFormat($row_old['totalStampDutyamount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalStampDutyamount']).'</b>'.'<br>';
  
  if ($row_old['totalFeesAmount'] != $row_new['totalFeesAmount']) 
    $sxolio_log.=gks_lang('Τέλη').': <b>'.myCurrencyFormat($row_old['totalFeesAmount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalFeesAmount']).'</b>'.'<br>';
  
  if ($row_old['totalDeductionsAmount'] != $row_new['totalDeductionsAmount']) 
    $sxolio_log.=gks_lang('Κρατήσεις').': <b>'.myCurrencyFormat($row_old['totalDeductionsAmount']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['totalDeductionsAmount']).'</b>'.'<br>';

  if (trim_gks($row_old['kostos_apostolis']) != trim_gks($row_new['kostos_apostolis'])) 
    $sxolio_log.=gks_lang('Κόστος αποστολής').': <b>'.myCurrencyFormat($row_old['kostos_apostolis']).'</b> [[-r]] <b>'.myCurrencyFormat($row_new['kostos_apostolis']).'</b>'.'<br>';

  if (trim_gks($row_old['kostos_pliromis']) != trim_gks($row_new['kostos_pliromis'])) 
    $sxolio_log.=gks_lang('Κόστος πληρωμής').': <b>'.myCurrencyFormat($row_old['kostos_pliromis']).'</b> [[-r]] <b>'.myCurrencyFormat($row_new['kostos_pliromis']).'</b>'.'<br>';


  if ($row_old['gks_price_total'] != $row_new['gks_price_total']) 
    $sxolio_log.=gks_lang('Σύνολο').': <b>'.myCurrencyFormat($row_old['gks_price_total']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_total']).'</b>'.'<br>';
  

  $pliroteo_old=$row_old['gks_price_total'] + $row_old['kostos_apostolis'] + $row_old['kostos_pliromis'];
  $pliroteo_new=$row_new['gks_price_total'] + $row_new['kostos_apostolis'] + $row_new['kostos_pliromis'];
  if ($pliroteo_old != $pliroteo_new) 
    $sxolio_log.=gks_lang('Πληρωτέο').': <b>'.myCurrencyFormat($pliroteo_old).'</b> [[-r]] '.'<b>'.myCurrencyFormat($pliroteo_new).'</b>'.'<br>';


  if (trim_gks($row_old['delivery_method_name']) != trim_gks($row_new['delivery_method_name'])) 
    $sxolio_log.=gks_lang('Τρόπος αποστολής').': <b>'.$row_old['delivery_method_name'].'</b> [[-r]] <b>'.$row_new['delivery_method_name'].'</b>'.'<br>';

  $delivery_id_8_old_desc='';
  $delivery_id_8_new_desc='';
  
  if ($row_old['delivery_id_8']>0) {
    $sql="SELECT warehouse_name FROM gks_warehouses
    WHERE warehouse_disable=0 and is_virtual = 0 and warehouse_can_pelatis_paralavei<>0 and id_warehouse=".$row_old['delivery_id_8'];
    $result_select = $db_link->query($sql);        
    if (!$result_select) {
      debug_mail(false,'error sql',$sql);
      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    }
    if ($result_select->num_rows==1) {
      $row_select = $result_select->fetch_assoc();
      $delivery_id_8_old_desc=$row_select['warehouse_name'];
    }
  }
  if ($row_new['delivery_id_8']>0) {
    $sql="SELECT warehouse_name FROM gks_warehouses
    WHERE warehouse_disable=0 and is_virtual = 0 and warehouse_can_pelatis_paralavei<>0 and id_warehouse=".$row_new['delivery_id_8'];
    $result_select = $db_link->query($sql);        
    if (!$result_select) {
      debug_mail(false,'error sql',$sql);
      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    }
    if ($result_select->num_rows==1) {
      $row_select = $result_select->fetch_assoc();
      $delivery_id_8_new_desc=$row_select['warehouse_name'];
    }
  }

  if (trim_gks($delivery_id_8_old_desc) != trim_gks($delivery_id_8_new_desc)) 
    $sxolio_log.=gks_lang('Παραλαβή από την επιχείρηση').': <b>'.$delivery_id_8_old_desc.'</b> [[-r]] <b>'.$delivery_id_8_new_desc.'</b>'.'<br>';


  if (trim_gks($row_old['delivery_number']) != trim_gks($row_new['delivery_number'])) 
    $sxolio_log.=gks_lang('Αριθμός Αποστολής').': <b>'.$row_old['delivery_number'].'</b> [[-r]] <b>'.$row_new['delivery_number'].'</b>'.'<br>';

  if (trim_gks($row_old['vehicle_number']) != trim_gks($row_new['vehicle_number'])) 
    $sxolio_log.=gks_lang('Αριθμός Μεταφορικού Μέσου').': <b>'.$row_old['vehicle_number'].'</b> [[-r]] <b>'.$row_new['vehicle_number'].'</b>'.'<br>';

  if (trim_gks($row_old['dispatch_date']) != trim_gks($row_new['dispatch_date'])) 
    $sxolio_log.=gks_lang('Ημέρα Έναρξης Αποστολής').': '.
                 (isset($row_old['dispatch_date']) ? '<b>'.showDate(strtotime($row_old['dispatch_date']), 'd/m/Y', 0).'</b>' : '').
                 ' [[-r]] '.
                 (isset($row_new['dispatch_date']) ? '<b>'.showDate(strtotime($row_new['dispatch_date']), 'd/m/Y', 0).'</b>' : '').
                 '<br>';

  if (trim_gks($row_old['dispatch_time']) != trim_gks($row_new['dispatch_time'])) 
    $sxolio_log.=gks_lang('Ώρα Έναρξης Αποστολής').': '.
                 (isset($row_old['dispatch_time']) ? '<b>'.showDate(strtotime($row_old['dispatch_time']), 'H:i', 0).'</b>' : '').
                 ' [[-r]] '.
                 (isset($row_new['dispatch_time']) ? '<b>'.showDate(strtotime($row_new['dispatch_time']), 'H:i', 0).'</b>' : '').
                 '<br>';


  if (trim_gks($row_old['aade_skopos_diakinisis_descr']) != trim_gks($row_new['aade_skopos_diakinisis_descr'])) 
    $sxolio_log.=gks_lang('Σκοπός διακίνησης').': <b>'.$row_old['aade_skopos_diakinisis_descr'].'</b> [[-r]] <b>'.$row_new['aade_skopos_diakinisis_descr'].'</b>'.'<br>';
  

  if (trim_gks($row_old['payment_acquirer_name']) != trim_gks($row_new['payment_acquirer_name'])) 
    $sxolio_log.=gks_lang('Τρόπος πληρωμής').': <b>'.$row_old['payment_acquirer_name'].'</b> [[-r]] <b>'.$row_new['payment_acquirer_name'].'</b>'.'<br>';

  if (trim_gks($row_old['note_doc'])!=trim_gks($row_old['note_doc']))
    $sxolio_log.=gks_lang('Σχόλια τιμολογίου').':<br><b>'.(isset($row_old['note_doc']) ? $row_old['note_doc'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['note_doc']) ? $row_new['note_doc'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['note_logistirio'])!=trim_gks($row_old['note_logistirio']))
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
 


//  if (trim_gks($row_old['print_date']) != trim_gks($row_new['print_date'])) 
//    $sxolio_log.=gks_lang('Ημερομηνία Εκτύπωσης').': '.
//                 (isset($row_old['print_date']) ? '<b>'.showDate(strtotime($row_old['print_date']), 'd/m/Y H:i', 1).'</b>' : '').
//                 ' [[-r]] '.
//                 (isset($row_new['print_date']) ? '<b>'.showDate(strtotime($row_new['print_date']), 'd/m/Y H:i', 1).'</b>' : '').
//                 '<br>';

//  if (trim_gks($row_old['print_inv_state']) != trim_gks($row_new['print_inv_state'])) 
//    $sxolio_log.=gks_lang('Κατάσταση όταν έγινε η εκτύπωση').': '.
//      (trim_gks($row_old['print_inv_state'])=='' ? '' :
//      '<span class="acc_inv_state_'.$row_old['print_inv_state'].'">'.getAccInvStateDescr($row_old['print_inv_state']).'</span>') .
//      ' [[-r]] '.
//      (trim_gks($row_new['print_inv_state'])=='' ? '' :
//      '<span class="acc_inv_state_'.$row_new['print_inv_state'].'">'.getAccInvStateDescr($row_new['print_inv_state']).'</span>') .
//      .'<br>';
//  
//  if (trim_gks($row_old['print_file_name']) != trim_gks($row_new['print_file_name'])) {
//    $temp_new='';
//    if (trim_gks($row_new['print_file_name'])!='') {
//      $local_file=GKS_FileServerShare.'acc/inv/'.$id.'/print/'.$row_new['print_file_name'];
//      if (file_exists($local_file)) {
//        $url_file='admin-get-file.php?fs=fileservers&file=acc%2Finv%2F'.$id.'%2Fprint%2F'.urlencode($row_new['print_file_name']);
//        $temp_new.= '<a href="'.$url_file.'" target="_blank">'.$row_new['print_file_name'].'</a> ';
//        $temp_new.= '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
//      }
//    }    
//    $sxolio_log.=gks_lang('Αρχείο Εκτύπωσης').': [[-r]] '.$temp_new.'<br>';    
//  }  

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
      $local_file=GKS_FileServerShare.'acc/inv/'.$id.'/aade_mydata/'.$row_new['aade_xml_send'];
      if (file_exists($local_file)) {
        $url_file='admin-get-file.php?fs=fileservers&file=acc%2Finv%2F'.$id.'%2Faade_mydata%2F'.urlencode($row_new['aade_xml_send']);
        $temp_new.= '<a href="'.$url_file.'" target="_blank">'.$row_new['aade_xml_send'].'</a> ';
        $temp_new.= '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
      }
    }    
    $sxolio_log.=gks_lang('ΑΑΔΕ - Απεσταλμένο XML').': [[-r]] '.$temp_new.'<br>';    
  }

  if (trim_gks($row_old['aade_xml_response']) != trim_gks($row_new['aade_xml_response'])) {
    $temp_new='';
    if (trim_gks($row_new['aade_xml_response'])!='') {
      $local_file=GKS_FileServerShare.'acc/inv/'.$id.'/aade_mydata/'.$row_new['aade_xml_response'];
      if (file_exists($local_file)) {
        $url_file='admin-get-file.php?fs=fileservers&file=acc%2Finv%2F'.$id.'%2Faade_mydata%2F'.urlencode($row_new['aade_xml_response']);
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

  $gks_custom_prepare=gks_custom_table_item_prepare('gks_acc_inv',['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;

  
  
  
  if ($sxolio_log == '') $sxolio_log=gks_lang('Ενημέρωση').'<br>';
  //print '<pre>';
  //print_r($products_old);
  //die();  
  
  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
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

function gks_acc_inv_create_acc_pay($old_id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  
  if ($old_id<=0) {
    debug_mail(false,'id is zero',$old_id);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αποθηκεύστε πρώτα το παραστατικό')));
    echo json_encode($return); die(); }   
  
  

  

  $sql="SELECT gks_acc_inv.*, gks_company.company_title, gks_company_subs.company_sub_title, 
  gks_payment_acquirers.payment_acquirer_name, gks_payment_acquirers.show_acc_pay, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_type_id
  FROM ((((gks_acc_inv 
  LEFT JOIN gks_company ON gks_acc_inv.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub) 
  LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
  where id_acc_inv=".$old_id;
  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'gks_acc_inv_create_acc_pay',                 gks_lang('Δεν βρέθηκε το παραστατικό').'<br>'.gks_lang('Ανανεώστε την σελίδα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το παραστατικό').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}
  
  $old_row = $result->fetch_assoc();
  //echo '<pre>';echo $old_id;die();
  
  $inv_state=$old_row['inv_state'];
  if ($inv_state!='070ypoekdosi' and $inv_state!='080listing' and $inv_state!='090ekdosi' and $inv_state!='100payment' ) {
    $message=gks_lang('Η κατάσταση του παραστατικού είναι').':<br>'.
    '<span class="acc_inv_state_'.$inv_state.'">'.getAccInvStateDescr($inv_state).'</span><br>'.
    gks_lang('ενώ θα πρέπει να είναι').':<br>'.
    //'<span class="acc_inv_state_050proinvoice">'.getAccInvStateDescr('050proinvoice').'</span> '.gks_lang('ή').'<br>'.
    '<span class="acc_inv_state_070ypoekdosi">'.getAccInvStateDescr('070ypoekdosi').'</span> '.gks_lang('ή').'<br>'.
    '<span class="acc_inv_state_080listing">'.getAccInvStateDescr('080listing').'</span> '.gks_lang('ή').'<br>'.
    '<span class="acc_inv_state_090ekdosi">'.getAccInvStateDescr('090ekdosi').'</span> '.gks_lang('ή').'<br>'.
    '<span class="acc_inv_state_100payment">'.getAccInvStateDescr('100payment').'</span><br>'.
    gks_lang('για να δημιουργηθεί η σχετική πληρωμή');
    
    debug_mail(false,'gks_orders_create_acc_pay',                  $message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  
  
  //echo '<pre>';echo $old_id;die();
    
  $company_id=$old_row['company_id'];
  $company_title=trim_gks($old_row['company_title']);
  $company_sub_id=$old_row['company_sub_id'];
  $company_sub_title=trim_gks($old_row['company_sub_title']);
  if ($company_sub_title=='') $company_sub_title=gks_lang('Κεντρικό');
  $fiscal_position_id=$old_row['fiscal_position_id'];
  //$parastatiko=$old_row['parastatiko'];
  $tropos_pliromis=intval($old_row['tropos_pliromis']);
  $payment_acquirer_name=trim_gks($old_row['payment_acquirer_name']);
  $gks_price_total=$old_row['gks_price_total'];
  $affect_balance_poso=$old_row['affect_balance_poso'];
  $show_acc_pay=$old_row['show_acc_pay'];
  $eidos_parastatikou_type_id=intval($old_row['eidos_parastatikou_type_id']);
  if ($show_acc_pay==0) {
    $tropos_pliromis=9;
    $payment_acquirer_name=gks_lang('Μετρητά');
    
  }
  //echo '<pre>';echo $payment_acquirer_name;die();
  
  $sql_eidi="SELECT gks_eshop_products.product_base_type, Count(gks_eshop_products.id_product) AS cc
  FROM gks_acc_inv_products 
  LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product
  WHERE gks_acc_inv_products.acc_inv_id=".$old_id."
  GROUP BY gks_eshop_products.product_base_type;";


  $result_eidi = $db_link->query($sql_eidi);  
  if (!$result_eidi) {
    debug_mail(false,'error sql',$sql_eidi);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $pbasetypes=array();
  $pbasetypes[0]=array('type'=>0, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_pay_acc_journal_id'=>0,'new_pay_acc_seira_id'=>0,'new_pay_acc_seira_code'=>'','error'=>''); //emporevma kai proion pane mazi
  //$pbasetypes[2]=array('type'=>2, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_pay_acc_journal_id'=>0,'new_pay_acc_seira_id'=>0,'new_pay_acc_seira_code'=>'','error'=>''); //ypiresia
  $total_eidi=0;
  while ($row_eidi= $result_eidi->fetch_assoc()) { 
    $total_eidi+=$row_eidi['cc'];
    $pbasetypes[0]['cc']+=$row_eidi['cc'];
  }  
  if ($total_eidi==0) {
    debug_mail(false,'total_eidi is zero',$total_eidi);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν είδη')));
    echo json_encode($return); die(); } 
    
  //print '<pre>';print_r($pbasetypes);die();
  
  //print '<pre>';print $parastatiko.'|'.$fiscal_position_id;die();
  
  
  
  $pbasetypes[0]['id_acc_eidos_parastatikou']=802; //Eispraxeis apo pelates
  if ($eidos_parastatikou_type_id==1) {         //Polisi
    $pbasetypes[0]['id_acc_eidos_parastatikou']=802;      //Eispraxeis apo pelates
  } else if ($eidos_parastatikou_type_id==2) {  //Agora
    $pbasetypes[0]['id_acc_eidos_parastatikou']=812;      //Pliromes se promitheftes
  }
  
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
        $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira, gks_acc_seires.seira_code,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda
        FROM (gks_acc_journal 
        LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id) 
        LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
        WHERE gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou=".$pb['id_acc_eidos_parastatikou']."
        and gks_acc_journal.id_acc_journal>0
        and gks_acc_journal.is_disable=0
        and gks_acc_seires.id_acc_seira>0
        and gks_acc_seires.is_xeirografi=0
        and gks_acc_seires.is_disable=0
        AND gks_acc_journal.company_id=".$company_id." 
        AND gks_acc_journal.company_sub_id=".$company_sub_id;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        if ($result->num_rows==0) {
          $sql="SELECT eidos_parastatikou_descr FROM gks_acc_eidi_parastatikon WHERE id_acc_eidos_parastatikou=".$db_link->escape_string($pb['id_acc_eidos_parastatikou']);
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }  
          if ($result->num_rows>0) {
            $row=$result->fetch_assoc();
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>'.gks_lang('με τύπο παραστατικού').':<br><b>'.$row['eidos_parastatikou_descr'].'</b>';
          } else {
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>'.gks_lang('με ID τύπου παραστατικού').':<br><b>'.$pb['id_acc_eidos_parastatikou'].'</b>';
          }
        } else {
          $row=$result->fetch_assoc();
          $pbasetypes[$i]['new_pay_acc_journal_id']=$row['id_acc_journal'];
          $pbasetypes[$i]['new_pay_acc_seira_id']=$row['id_acc_seira'];
          $pbasetypes[$i]['new_pay_acc_seira_code']=$row['seira_code'];
          $pbasetypes[$i]['has_esoda']=$row['eidos_parastatikou_has_esoda'];
          $pbasetypes[$i]['has_eksoda']=$row['eidos_parastatikou_has_eksoda'];
          
          
        }
      } else {
        $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε ποιο ημερολόγιο θα πρέπει να χρησιμοποιηθεί για αυτήν την λειτουργία');
      }
    }
  }
  
  $errors='';
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['error']!='') {
      $errors.=$pb['error'].'<br><br>';
    }
  } 
  if ($errors!='') {
    $errors=substr($errors, 0, strlen($errors)-8);
    debug_mail(false,'errors',                                     $errors);
    $return = array('success' => false, 'message' => base64_encode($errors));
    echo json_encode($return); die();}
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id; die();
  

  
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
            
        $new_pay_guid=guid_for_acc_pay();
        //echo $new_pay_guid."\n"; die();
        
        $pay_poso=array();
        $pay_poso[]=array(
          'i'=>$old_id,
          'f'=>'inv',
          'v'=>$affect_balance_poso,
        );
        $pay_poso_str=serialize($pay_poso);
        
        $sql="INSERT INTO gks_acc_pay (pay_guid, pay_date, mydate_add, mydate_edit, 
        user_id_add, user_id_edit, myip, 
        pay_acc_journal_id, pay_acc_seira_id, 
        pay_acc_seira_code, pay_state, 
        
        company_id, company_sub_id, user_id,user_notes,note_logistirio,
        affect_balance,affect_balance_all_poso,affect_balance_all_poso_type,affect_balance_poso,
        gks_price_total,pay_poso_str
        )
        SELECT '".$new_pay_guid."' as pay_guid, now() as pay_date, now() as mydate_add, now() as mydate_edit,
        ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
        ".$pbasetypes[$i]['new_pay_acc_journal_id']." as pay_acc_journal_id, ".$pbasetypes[$i]['new_pay_acc_seira_id']." as pay_acc_seira_id, 
        '".$db_link->escape_string($pbasetypes[$i]['new_pay_acc_seira_code'])."' as pay_acc_seira_code, '010draft' as pay_state, 
        
        company_id, company_sub_id, user_id,user_notes,note_logistirio,
        1 as affect_balance,1 as affect_balance_all_poso,'price_total' as affect_balance_all_poso_type,affect_balance_poso,
        ".number_format($affect_balance_poso,10, '.','').",
        '".$db_link->escape_string($pay_poso_str)."'
        FROM gks_acc_inv
        WHERE id_acc_inv=".$old_id;
        
         
        
        //echo '<pre>';echo $sql;die();
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        $new_id = $db_link->insert_id;  
        //echo $new_id."\n";die();
        
        

        $sql="insert into gks_acc_pay_method (
          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
          acc_pay_id,paymethod_aa,paymethod_id,
          paymethod_total,paymethod_descr,paymethod_comments
        ) values (
          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
          ".$new_id.",
          1,
          ".$tropos_pliromis.",
          ".number_format($affect_balance_poso,10, '.','').",
          '".$db_link->escape_string($payment_acquirer_name)."',
          ''
        )";
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        $new_product_id = $db_link->insert_id;
        
                    //echo print_r($map_products,true)."\n";die();
        
        
        
        
        
        
        $sxolio=gks_lang('Προσθήκη από backend, δημιουργία από παραστατικό με ID').' #<a href="admin-acc-inv-item.php?id='.$old_id.'">'.$old_id.'</a>'; 
        $sql="insert into gks_acc_pay_log (acc_pay_id, add_date,user_id,sxolio) values (
        ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
     
        
        $pbasetypes[$i]['new_id']=$new_id;
        
        $sql="insert into gks_object_rel (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        object_name1,object_id1,object_name2,object_id2
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        'gks_acc_inv',".$old_id.",'gks_acc_pay',".$new_id."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        
      }
    } 
  }
  
  $ret=array();
  foreach ($pbasetypes as $i => $pb) {
    if (isset($pb['new_id']) and $pb['new_id']>0) $ret[]=$pb['new_id'];
  } 
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  return $ret;
  
}



function gks_inv_get_ekdosi_numbers() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $inv_acc_number_int_old;
  global $inv_acc_number_int_new;
  global $inv_acc_number_str_new;
  global $inv_acc_seira_code_new;
  global $inv_acc_seira_id;
  global $has_ekdosi;
  global $save_but_message;
  global $id;
  global $inv_state;
  
  //die('<pre>inv_acc_number_int_old:'.$inv_acc_number_int_old);
  if ($inv_acc_number_int_old>0) {
    $sql_auto_number="select auto_number from gks_acc_seires_auto_numbers where disabled_date is null and acc_seira_id=".$inv_acc_seira_id." and acc_inv_id=".$id;
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_auto_number->num_rows>=1) {
      $row_auto_number = $result_auto_number->fetch_assoc();    
      $inv_acc_number_int_old=$row_auto_number['auto_number'];
      $inv_acc_number_int_new=$row_auto_number['auto_number'];

      $sql="select * from gks_acc_seires where id_acc_seira=".$inv_acc_seira_id;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      $row_seira = $result->fetch_assoc();
      $inv_acc_seira_code_new=trim_gks($row_seira['seira_code']);
      $seires_prefix=trim_gks($row_seira['prefix']);
      $seires_suffix=trim_gks($row_seira['suffix']);
      $seires_number_size=$row_seira['number_size'];
      $inv_acc_number_str_new=$seires_prefix.str_pad($inv_acc_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
      $has_ekdosi=true;
    }
  }
  
  if ($inv_acc_number_int_old==0) {
    $inv_state='';
    
    
    
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select * from gks_acc_seires where id_acc_seira=".$inv_acc_seira_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $row_seira = $result->fetch_assoc();
    $inv_acc_seira_code_new=trim_gks($row_seira['seira_code']);
    $seires_prefix=trim_gks($row_seira['prefix']);
    $seires_suffix=trim_gks($row_seira['suffix']);
    $seires_number_size=$row_seira['number_size'];
    $inv_acc_number_int_new=$row_seira['next_number'];
    $inv_acc_number_str_new=$seires_prefix.str_pad($inv_acc_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
    //$save_but_message='<pre>'.$inv_acc_number_str_new;
    //die('<pre>inv_acc_number_int_old:'.$inv_acc_seira_id.'|'.$inv_acc_number_str_new);
    
    $sql="update gks_acc_seires set next_number=next_number+number_step where id_acc_seira=".$inv_acc_seira_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $sql="unlock tables;";       
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  
    $inv_state='090ekdosi';
    $has_ekdosi=true;
    if ($save_but_message!='') {
      $save_but_message=gks_lang('Το παραστατικό έχει αποθηκευτεί αλλά δεν έχει εκδοθεί διότι').':<br>'.$save_but_message;
    }
    
    $sql="insert into gks_acc_seires_auto_numbers (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_seira_id,acc_inv_id,auto_number
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$inv_acc_seira_id.",".$id.",".$inv_acc_number_int_new."
    )";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
  
}


