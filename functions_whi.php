<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/




function getWhiMovStateDescr($mystate,$load_lang='') {
  global $gks_user_settings;
  if ($load_lang=='') {
    $load_lang='el-GR';
    if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
  }
  
  if ($load_lang=='el-GR') {  
    switch ($mystate) {
      case '010draft': return gks_lang('Πρόχειρο','part4','getWhiMovStateDescr'); break; 
      case '040cancelled': return gks_lang('Ακυρωμένο','part4','getWhiMovStateDescr'); break; 
      case '080listing': return gks_lang('Καταχώρηση','part4','getWhiMovStateDescr'); break; 
      case '090ekdosi': return gks_lang('Έκδοση','part4','getWhiMovStateDescr'); break; 
      case '100closed': return gks_lang('Κλεισμένο','part4','getWhiMovStateDescr'); break; 
      //100payment
      default: return $mystate; break; 
    } 
  } else {
    switch ($mystate) {
      case '010draft': return 'Draft'; break; 
      case '040cancelled': return 'Cancelled'; break; 
      case '080listing': return 'Listing'; break; 
      case '090ekdosi': return 'Issue'; break; 
      case '100closed': return 'Closed'; break; 
      //100payment
      default: return $mystate; break; 
    } 
    
    
  }
}

function guid_for_whi_mov() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT mov_guid from gks_whi_mov where mov_guid='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    
    if ($result->num_rows == 0) {
      return $guid; 
    }
  }
}

function get_whi_mov_details_txt($id, &$myarray=array(),&$myarray_line=array()) {
  global $db_link;
  $myarray=array();
  
  
  
    
  $sql="SELECT gks_whi_mov_products.*,
  gks_eshop_products.product_code, 
  gks_eshop_products.product_photo, 
  gks_eshop_products.product_descr as product_descr_from_p, 
  gks_eshop_products.product_descr_small, 
  gks_eshop_products.product_descr_big
  FROM gks_whi_mov_products 
  LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product
  WHERE gks_whi_mov_products.whi_mov_id=".$id."
  ORDER BY gks_whi_mov_products.id_whi_mov_product;";
  
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

function select_gks_whi_mov() {

$sql="SELECT gks_whi_mov.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
".GKS_WP_TABLE_PREFIX."users_aade.gks_nickname AS gks_nickname_aade,
".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.ma_branch as ma_branch_fromuser,
gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,
gks_acc_journal.acc_eidos_parastatikou_id,
gks_acc_journal.acc_eidos_parastatikou_other_entity,
gks_acc_journal.journal_has_correlated_invoices,
gks_acc_journal.journal_has_multiple_connected_marks,
gks_acc_journal.journal_has_packings_declarations,
gks_acc_seires.is_xeirografi,gks_acc_seires.send_mydata,gks_acc_seires.send_paroxos,
gks_acc_seires.seira_isdeliverynote,
gks_acc_seires.seira_is_reverse_delivery_note,
gks_acc_seires.seira_is_self_pricing,
gks_acc_seires.seira_is_vat_payment_suspension,
eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_need_prev, eidos_parastatikou_has_fpa,eidos_parastatikou_has_othertaxes, 
eidos_parastatikou_has_esoda, eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,eidos_parastatikou_balance_pros,eidos_parastatikou_stock_pros,
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

gks_aade_paroxos.paroxos_name

FROM (((((((((((((((((((((((((((((gks_whi_mov

LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_whi_mov.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_whi_mov.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_aade ON gks_whi_mov.aade_user_id = ".GKS_WP_TABLE_PREFIX."users_aade.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_print ON gks_whi_mov.print_user_id = ".GKS_WP_TABLE_PREFIX."users_print.ID) 
LEFT JOIN gks_company on gks_whi_mov.company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_whi_mov.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_delivery_methods ON gks_whi_mov.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_eshop_fiscal_position ON gks_whi_mov.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_whi_mov.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_whi_mov.ma_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_whi_mov.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_lang ON gks_whi_mov.user_lang = gks_lang.id_lang)
LEFT JOIN gks_aade_skopos_diakinisis ON gks_whi_mov.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_whi_mov.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_whi_mov.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_whi_mov.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_whi_mov.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_warehouses AS gks_warehouses_from ON gks_whi_mov.warehouses_id_from = gks_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_warehouses_to ON gks_whi_mov.warehouses_id_to = gks_warehouses_to.id_warehouse)

LEFT JOIN gks_country as gks_country_load ON gks_whi_mov.load_country_id = gks_country_load.id_country)
LEFT JOIN gks_nomoi as gks_nomoi_load ON gks_whi_mov.load_nomos_id = gks_nomoi_load.id_nomos)
LEFT JOIN gks_country as gks_country_deli ON gks_whi_mov.deli_country_id = gks_country_deli.id_country)
LEFT JOIN gks_nomoi as gks_nomoi_deli ON gks_whi_mov.deli_nomos_id = gks_nomoi_deli.id_nomos)
LEFT JOIN gks_aade_paroxos ON gks_whi_mov.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos


";
//echo '222222222';
return $sql;
  
}

//gks_whi_mov_cancel_create

function gks_whi_mov_cancel_create($old_id, $check_only) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  
  $sql="select id_whi_mov from gks_whi_mov where cancel_for_whi_mov_id=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    debug_mail(false,'emptyl',                                     gks_lang('Αυτό το παραστατικό έχει ήδη το αντίστοιχο ακυρωτικό παραστατικό').'<br><a href="admin-acc-inv-item.php?id="'.$row['id_whi_mov'].'" class="gks_link">'.gks_lang('Προβολή').'</a>');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το παραστατικό έχει ήδη το αντίστοιχο ακυρωτικό παραστατικό').'<br><a href="admin-acc-inv-item.php?id="'.$row['id_whi_mov'].'" class="gks_link">'.gks_lang('Προβολή').'</a>'));
    echo json_encode($return); die();}
  
  
  $sql="select * from gks_whi_mov where id_whi_mov=".$old_id;
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
//  if ($old_row['mov_state']!='080listing' and $old_row['mov_state']!='090ekdosi' and $old_row['mov_state']!='100closed') {
//    debug_mail(false,'emptyl',                                     gks_lang('Η κατάσταση του παραστατικού δεν είναι <b>Καταχώρηση</b> ή <b>Έκδοση</b> ή <b>Εξοφλημένο</b> και άρα δεν μπορεί να ακυρωθεί'));
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η κατάσταση του παραστατικού δεν είναι <b>Καταχώρηση</b> ή <b>Έκδοση</b> ή <b>Εξοφλημένο</b> και άρα δεν μπορεί να ακυρωθεί')));
//    echo json_encode($return); die();}

  if (intval($old_row['aade_paroxos_id'])>0 and trim_gks($old_row['aade_invoicemark'])) {
    debug_mail(false,'emptyl',                                     gks_lang('Αυτό το δελτίο έχει αποσταλεί στο πάροχο και δεν μπορεί να ακυρωθεί'),$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το δελτίο έχει αποσταλεί στο πάροχο και δεν μπορεί να ακυρωθεί')));
    echo json_encode($return); die();}

    
  $company_id=$old_row['company_id'];
  $company_sub_id=$old_row['company_sub_id'];
  
  $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira,gks_acc_seires.seira_code
  FROM gks_acc_journal 
  LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id
  WHERE gks_acc_journal.acc_eidos_parastatikou_id=704
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
  $new_mov_whi_journal_id=$row['id_acc_journal'];
  $new_mov_whi_seira_id=$row['id_acc_seira'];
  $new_mov_whi_seira_code=trim_gks($row['seira_code']);
  //echo $new_mov_whi_journal_id.'|'.$new_mov_whi_seira_id.'|'.$new_mov_whi_seira_code."\n";
  
  
  if ($check_only) return true;
  
  $new_mov_guid=guid_for_whi_mov();
  //echo $new_mov_guid."\n";
  
  $sql="INSERT INTO gks_whi_mov (mov_guid, mov_date, mydate_add, mydate_edit, 
  user_id_add, user_id_edit, myip, 
  mov_whi_journal_id, mov_whi_seira_id, 
  mov_whi_seira_code, mov_state, 
  cancel_for_whi_mov_id,
  company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos, ma_arithmos,
  ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
  address_extra, destination_data_name, destination_data_phone, destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
  destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, idiotites,
  fiscal_position_id, pricelist_id, is_other, other_first_name,
  other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
  other_ma_nomos_id, products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
  products_need_apostoli, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
  delivery_id_8,
  warehouses_id_from,warehouses_id_to
  
  )
  SELECT '".$new_mov_guid."' as mov_guid, now() as mov_date, now() as mydate_add, now() as mydate_edit,
  ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
  ".$new_mov_whi_journal_id." as mov_whi_journal_id, ".$new_mov_whi_seira_id." as mov_whi_seira_id, 
  '".$db_link->escape_string($new_mov_whi_seira_code)."' as mov_whi_seira_code, '010draft' as mov_state, 
  ".$old_id." as cancel_for_whi_mov_id,
  company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos, ma_arithmos,
  ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
  address_extra, destination_data_name, destination_data_phone, destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
  destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, idiotites,
  fiscal_position_id, pricelist_id, is_other, other_first_name,
  other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
  other_ma_nomos_id, products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
  products_need_apostoli, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
  delivery_id_8,
  warehouses_id_from,warehouses_id_to
  
  FROM gks_whi_mov
  WHERE id_whi_mov=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $new_id = $db_link->insert_id;  
  //echo $new_id."\n";
  
  $sql="select id_whi_mov_product from gks_whi_mov_products where whi_mov_id=".$old_id." order by id_whi_mov_product";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $old_product_ids=array();
  while ($row = $result->fetch_assoc()) {  
    $old_product_ids[]=$row['id_whi_mov_product'];
  }
  
  $map_products=array();
  foreach ($old_product_ids as $old_product_id) {
    $sql="INSERT INTO gks_whi_mov_products ( 
    mydate_add, mydate_edit, user_id_add, user_id_edit, myip, 
    whi_mov_id, 
    product_aa, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
    product_is_simple_download, product_need_apostoli, 
    product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
    product_ogos_y, product_ogos_z, product_category_ids, product_quantity, 
    product_comments, aade_lineComments,
    p_warehouses_id_from,p_warehouses_id_to
    )
    SELECT now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,
    '".$db_link->escape_string($gkIP)."' as myip,
    ".$new_id." as whi_mov_id,
    product_aa, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
    product_is_simple_download, product_need_apostoli, 
    product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
    product_ogos_y, product_ogos_z, product_category_ids, product_quantity, 
    product_comments, aade_lineComments,
    p_warehouses_id_from,p_warehouses_id_to
    FROM gks_whi_mov_products
    where id_whi_mov_product=".$old_product_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    
    $new_product_id = $db_link->insert_id;  
    $map_products[]=array('old' => $old_product_id, 'new' => $new_product_id);
  }
  //echo print_r($map_products,true)."\n";
  
  

  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_whi_mov_log (whi_mov_id, add_date,user_id,sxolio) values (
  ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  gks_whi_after_balance_for_whi($new_id);
    
  return $new_id;
  
}

function gks_whi_mov_credit_memo_create($old_id, $check_only) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  


  $sql="SELECT gks_whi_mov.*,gks_acc_eidi_parastatikon.credit_acc_eidos_parastatikou_id,
  gks_acc_eidi_parastatikon_creadit.eidos_parastatikou_descr as credit_descr
  FROM ((gks_whi_mov 
  LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon AS gks_acc_eidi_parastatikon_creadit ON gks_acc_eidi_parastatikon.credit_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon_creadit.id_acc_eidos_parastatikou
  where id_whi_mov=".$old_id;

  
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
//  if ($old_row['mov_state']!='080listing' and $old_row['mov_state']!='090ekdosi' and $old_row['mov_state']!='100closed') {
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
  WHERE eidos_parastatikou_type_id in (21,22,23) and id_acc_eidos_parastatikou not in (702,703,704) 
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
    debug_mail(false,'emptyl',                                     str_replace('[1]',$credit_descr,gks_lang('Δεν βρέθηκε συσχετιζόμενο ημερολόγιο με τύπο <b>[1]</b> και μηχανογραφημένη σειρά για αυτήν την εταιρεία/υποκατάστημα')));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$credit_descr,gks_lang('Δεν βρέθηκε συσχετιζόμενο ημερολόγιο με τύπο <b>[1]</b> και μηχανογραφημένη σειρά για αυτήν την εταιρεία/υποκατάστημα'))));
    echo json_encode($return); die();}
  $row=$result->fetch_assoc();
  $new_mov_whi_journal_id=$row['id_acc_journal'];
  $new_mov_whi_seira_id=$row['id_acc_seira'];
  $new_mov_whi_seira_code=trim_gks($row['seira_code']);
  //echo $new_mov_whi_journal_id.'|'.$new_mov_whi_seira_id.'|'.$new_mov_whi_seira_code."\n";
  
  if ($check_only) return true;
  
  $new_mov_guid=guid_for_whi_mov();
  //echo $new_mov_guid."\n"; die();
  
  $sql="INSERT INTO gks_whi_mov (mov_guid, mov_date, mydate_add, mydate_edit, 
  user_id_add, user_id_edit, myip, 
  mov_whi_journal_id, mov_whi_seira_id, 
  mov_whi_seira_code, mov_state, 
  credit_memo_for_whi_mov_id,
  company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos, ma_arithmos,
  ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
  address_extra, destination_data_name, destination_data_phone, destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
  destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, idiotites,
  fiscal_position_id, pricelist_id, is_other, other_first_name,
  other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
  other_ma_nomos_id, products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
  products_need_apostoli, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
  delivery_id_8,
  warehouses_id_from,warehouses_id_to

  )
  SELECT '".$new_mov_guid."' as mov_guid, now() as mov_date, now() as mydate_add, now() as mydate_edit,
  ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
  ".$new_mov_whi_journal_id." as mov_whi_journal_id, ".$new_mov_whi_seira_id." as mov_whi_seira_id, 
  '".$db_link->escape_string($new_mov_whi_seira_code)."' as mov_whi_seira_code, '010draft' as mov_state, 
  ".$old_id." as credit_memo_for_whi_mov_id,
  company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos,ma_arithmos,
  ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
  address_extra, destination_data_name, destination_data_phone, destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
  destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, idiotites,
  fiscal_position_id, pricelist_id, is_other, other_first_name,
  other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
  other_ma_nomos_id, products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
  products_need_apostoli, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
  delivery_id_8,
  warehouses_id_to,warehouses_id_from
  
  FROM gks_whi_mov
  WHERE id_whi_mov=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $new_id = $db_link->insert_id;  
  //echo $new_id."\n";die();
  
  $sql="select id_whi_mov_product from gks_whi_mov_products where whi_mov_id=".$old_id." order by id_whi_mov_product";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $old_product_ids=array();
  while ($row = $result->fetch_assoc()) {  
    $old_product_ids[]=$row['id_whi_mov_product'];
  }
  
  $map_products=array();
  foreach ($old_product_ids as $old_product_id) {
    $sql="INSERT INTO gks_whi_mov_products ( 
    mydate_add, mydate_edit, user_id_add, user_id_edit, myip, 
    whi_mov_id, 
    product_aa, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
    product_is_simple_download, product_need_apostoli, 
    product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
    product_ogos_y, product_ogos_z, product_category_ids, product_quantity, 
    product_comments, aade_lineComments,
    p_warehouses_id_from,p_warehouses_id_to
    )
    SELECT now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,
    '".$db_link->escape_string($gkIP)."' as myip,
    ".$new_id." as whi_mov_id,
    product_aa, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
    product_is_simple_download, product_need_apostoli,
    product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
    product_ogos_y, product_ogos_z, product_category_ids, product_quantity,
    product_comments, aade_lineComments,
    p_warehouses_id_to,p_warehouses_id_from
    FROM gks_whi_mov_products
    where id_whi_mov_product=".$old_product_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    
    $new_product_id = $db_link->insert_id;  
    $map_products[]=array('old' => $old_product_id, 'new' => $new_product_id);
  }
  //echo print_r($map_products,true)."\n";
  
  

  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_whi_mov_log (whi_mov_id, add_date,user_id,sxolio) values (
  ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  gks_whi_after_balance_for_whi($new_id);
  
  return $new_id;
  
}

function gks_whi_mov_all_products_for_balance_extend($all_products_for_balance) {
  global $db_link; 
  
  if (is_array($all_products_for_balance)==false) return array();
  if (count($all_products_for_balance)<=0) return array();

  $sql="select id_product,product_parent_id from gks_eshop_products where id_product in (".implode(',',$all_products_for_balance).") and id_product>0";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $ids=array();
  while ($row = $result->fetch_assoc()) {
    if (in_array($row['id_product'],$ids)==false) 
      $ids[]=$row['id_product'];
    if ($row['product_parent_id']>0) 
      if (in_array($row['product_parent_id'],$ids)==false) 
        $ids[]=$row['product_parent_id'];
        
  }
  if (count($ids)<=0) return array();
  
  $sql="select id_product from gks_eshop_products where product_parent_id in (".implode(',',$ids).") and id_product>0";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  while ($row = $result->fetch_assoc()) {
    if (in_array($row['id_product'],$ids)==false) 
      $ids[]=$row['id_product'];
  }
  

  $sql="SELECT gks_production_bom_product.pbom_product_id
  FROM gks_production_bom 
  LEFT JOIN gks_production_bom_product ON gks_production_bom.id_production_bom = gks_production_bom_product.production_bom_id
  WHERE gks_production_bom.bom_product_id In (".implode(',',$ids).") 
  and gks_production_bom_product.pbom_product_id>0";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  while ($row = $result->fetch_assoc()) {
    if (in_array($row['pbom_product_id'],$ids)==false) 
      $ids[]=$row['pbom_product_id'];
  }
  
  $sql="SELECT gks_production_bom.bom_product_id
  FROM gks_production_bom_product 
  LEFT JOIN gks_production_bom ON gks_production_bom_product.production_bom_id = gks_production_bom.id_production_bom
  WHERE gks_production_bom_product.pbom_product_id In (".implode(',',$ids).") 
  AND gks_production_bom.bom_product_id>0";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  while ($row = $result->fetch_assoc()) {
    if (in_array($row['bom_product_id'],$ids)==false) 
      $ids[]=$row['bom_product_id'];
  }

  return $ids;
}


function gks_whi_mov_balance_calc($all_products_for_balance_input, $until_date=null) {
  global $db_link;
  if (is_array($all_products_for_balance_input)==false) return array();
  if (count($all_products_for_balance_input)<=0) return array();
  //var_dump($until_date);
  
  
  
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/all_products_for_balance'.rand(1000,9999).'.txt',print_r($all_products_for_balance,true));
  
  $all_products_for_balance=gks_whi_mov_all_products_for_balance_extend($all_products_for_balance_input);
  //echo '<pre>dddddddddddd '.$until_date.'||';print_r($all_products_for_balance);print_r($all_products_for_balance_input);die();
  
  $mybal=array();
  foreach ($all_products_for_balance as $id_product) {
    $mybal[$id_product]=array('total' => 0, 'warehouses' => array());
    
  }
  
  

  //efugan-out whi
  $sql="SELECT product_id, p_warehouses_id_from, 
  Sum(product_quantity*monada_convert_epi_rev*if(seira_is_reverse_delivery_note=0,1,-1)) AS sumq
  FROM (((gks_whi_mov_products
  LEFT JOIN gks_whi_mov ON gks_whi_mov_products.whi_mov_id = gks_whi_mov.id_whi_mov)
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_warehouses ON gks_whi_mov_products.p_warehouses_id_from = gks_warehouses.id_warehouse)
  LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product
  WHERE whi_mov_id>0 
  and gks_eshop_products.product_base_type in (0,1)
  AND p_mov_state in ('080listing','090ekdosi','100payment')
  and cancel_for_whi_mov_id=0 
  AND gks_warehouses.is_virtual=0
  AND product_id > 2 
  AND product_id In (".implode(',',$all_products_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_whi_mov.mov_date<='".$until_date."'";
  }
  $sql.=" GROUP BY product_id, p_warehouses_id_from;";
  //echo '<pre>ddddd '.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['product_id']])==false) $mybal[$row['product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_from']])==false) $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_from']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_from']]['out']+=$row['sumq'];
  }
  
  //echo '<pre>dddddddddddd';print_r($mybal);die();

  //irthan-in whi
  $sql="SELECT product_id, p_warehouses_id_to, 
  Sum(product_quantity*monada_convert_epi_rev*if(seira_is_reverse_delivery_note=0,1,-1)) AS sumq
  FROM (((gks_whi_mov_products
  LEFT JOIN gks_whi_mov ON gks_whi_mov_products.whi_mov_id = gks_whi_mov.id_whi_mov)
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_warehouses ON gks_whi_mov_products.p_warehouses_id_to = gks_warehouses.id_warehouse)
  LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product
  WHERE whi_mov_id>0 
  and gks_eshop_products.product_base_type in (0,1)
  AND p_mov_state in ('080listing','090ekdosi','100payment')
  and cancel_for_whi_mov_id=0 
  AND gks_warehouses.is_virtual=0
  AND product_id > 2 
  AND product_id In (".implode(',',$all_products_for_balance).")";
  if ($until_date!==null) {
    $sql.=" and gks_whi_mov.mov_date<='".$until_date."'";
  }
  $sql.=" GROUP BY product_id, p_warehouses_id_to;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['product_id']])==false) $mybal[$row['product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_to']])==false) $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_to']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_to']]['in']+=$row['sumq'];
  }
  
  
  //efugan-out acc_inv
  $sql="SELECT product_id, p_warehouses_id_from, Sum(product_quantity*monada_convert_epi_rev) AS sumq
  FROM ((gks_acc_inv_products
  LEFT JOIN gks_acc_inv ON gks_acc_inv_products.acc_inv_id = gks_acc_inv.id_acc_inv)
  LEFT JOIN gks_warehouses ON gks_acc_inv_products.p_warehouses_id_from = gks_warehouses.id_warehouse)
  LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product
  WHERE acc_inv_id>0 
  and gks_eshop_products.product_base_type in (0,1)
  AND p_inv_state in ('080listing','090ekdosi','100payment')
  and cancel_for_acc_inv_id=0 
  AND gks_warehouses.is_virtual=0
  AND product_id > 2 
  AND product_id In (".implode(',',$all_products_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_acc_inv.inv_date<='".$until_date."'";
  }
  $sql.=" GROUP BY product_id, p_warehouses_id_from;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['product_id']])==false) $mybal[$row['product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_from']])==false) $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_from']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_from']]['out']+=$row['sumq'];
  }
  
  
  //irthan-in acc_inv
  $sql="SELECT product_id, p_warehouses_id_to, Sum(product_quantity*monada_convert_epi_rev) AS sumq
  FROM ((gks_acc_inv_products
  LEFT JOIN gks_acc_inv ON gks_acc_inv_products.acc_inv_id = gks_acc_inv.id_acc_inv)
  LEFT JOIN gks_warehouses ON gks_acc_inv_products.p_warehouses_id_to = gks_warehouses.id_warehouse)
  LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product
  WHERE acc_inv_id>0 
  and gks_eshop_products.product_base_type in (0,1)
  AND p_inv_state in ('080listing','090ekdosi','100payment')
  and cancel_for_acc_inv_id=0 
  AND gks_warehouses.is_virtual=0 
  AND product_id > 2 
  AND product_id In (".implode(',',$all_products_for_balance).")";
  if ($until_date!==null) {
    $sql.=" and gks_acc_inv.inv_date<='".$until_date."'";
  }
  $sql.=" GROUP BY product_id, p_warehouses_id_to;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['product_id']])==false) $mybal[$row['product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_to']])==false) $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_to']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_to']]['in']+=$row['sumq'];
  }
  
    
    
  //efugan-out order
  //and cancel_for_order_id=0 
  $sql="SELECT product_id, p_warehouses_id_from, Sum(product_quantity*monada_convert_epi_rev) AS sumq
  FROM ((gks_orders_products
  LEFT JOIN gks_orders ON gks_orders_products.order_id = gks_orders.id_order)
  LEFT JOIN gks_warehouses ON gks_orders_products.p_warehouses_id_from = gks_warehouses.id_warehouse)
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
  WHERE order_id>0
  and gks_eshop_products.product_base_type in (0,1)
  AND p_order_state in ('060registered','070inproduction','090indelivery','095execute','100completed','110payment')
  AND gks_warehouses.is_virtual=0 
  AND product_id > 2 
  AND gks_orders_products.product_is_optional in (0,2)
  AND product_id In (".implode(',',$all_products_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_orders.order_date<='".$until_date."'";
  }
  $sql.=" GROUP BY product_id, p_warehouses_id_from;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['product_id']])==false) $mybal[$row['product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_from']])==false) $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_from']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_from']]['out']+=$row['sumq'];
  }
  
  
  //irthan-in order
  //and cancel_for_order_id=0 
  $sql="SELECT product_id, p_warehouses_id_to, Sum(product_quantity*monada_convert_epi_rev) AS sumq
  FROM ((gks_orders_products
  LEFT JOIN gks_orders ON gks_orders_products.order_id = gks_orders.id_order)
  LEFT JOIN gks_warehouses ON gks_orders_products.p_warehouses_id_to = gks_warehouses.id_warehouse)
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
  WHERE order_id>0 
  and gks_eshop_products.product_base_type in (0,1)
  AND p_order_state in ('060registered','070inproduction','090indelivery','095execute','100completed','110payment')
  AND gks_warehouses.is_virtual=0
  AND product_id > 2 
  AND gks_orders_products.product_is_optional in (0,2)
  AND product_id In (".implode(',',$all_products_for_balance).")";
  if ($until_date!==null) {
    $sql.=" and gks_orders.order_date<='".$until_date."'";
  }
  $sql.=" GROUP BY product_id, p_warehouses_id_to;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['product_id']])==false) $mybal[$row['product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_to']])==false) $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_to']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['product_id']]['warehouses'][$row['p_warehouses_id_to']]['in']+=$row['sumq'];
  }    
  



  //efugan-out gks_production_sintagi_product
  //and cancel_for_order_id=0 
  //*monada_convert_epi_rev
  $sql="SELECT spbom_product_id, sp_warehouses_id_from, Sum(spbom_quantity*monada_convert_epi_rev) AS sumq
  FROM ((gks_production_sintagi_product
  LEFT JOIN gks_orders ON gks_production_sintagi_product.order_id = gks_orders.id_order)
  LEFT JOIN gks_warehouses ON gks_production_sintagi_product.sp_warehouses_id_from = gks_warehouses.id_warehouse)
  LEFT JOIN gks_eshop_products ON gks_production_sintagi_product.spbom_product_id = gks_eshop_products.id_product
  WHERE order_id>0 
  and gks_eshop_products.product_base_type in (0,1)
  AND sp_order_state in ('070inproduction','090indelivery','095execute','100completed','110payment')
  AND gks_warehouses.is_virtual=0 
  AND spbom_product_id > 2 
  AND spbom_product_id In (".implode(',',$all_products_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_orders.order_date<='".$until_date."'";
  }
  $sql.=" GROUP BY spbom_product_id, sp_warehouses_id_from";
  //echo '<pre>'.$sql;die();  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['spbom_product_id']])==false) $mybal[$row['spbom_product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['spbom_product_id']]['warehouses'][$row['sp_warehouses_id_from']])==false) $mybal[$row['spbom_product_id']]['warehouses'][$row['sp_warehouses_id_from']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['spbom_product_id']]['warehouses'][$row['sp_warehouses_id_from']]['out']+=$row['sumq'];
  }
  //echo '<pre>'; print_r($mybal);die();  
  
  
  
  
  foreach ($mybal as $pkey => &$product) {
    foreach ($product['warehouses'] as $wkey => &$warehouse) {
      $warehouse['bal']=$warehouse['in']-$warehouse['out'];
      $mybal[$pkey]['total']+=$warehouse['bal'];
    }
    unset($warehouse);
  } 
  unset($product);
  
  //echo '<pre>'; print_r($mybal);die();  
  
  if ($until_date!==null) {
    return $mybal;
  }
  //echo 'fffff';die();
  

  $sql="select id_warehouse from gks_warehouses order by id_warehouse";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return['message']='sql error';return $return;}
  
  $all_warehouses=[];
  while ($row = $result->fetch_assoc()) {
    $all_warehouses[$row['id_warehouse']]=false;
  }
  
  $sqls=[];
  foreach ($mybal as $pkey => $product) {
    //$wkey_array=array();
    $total_balance=0;
    $aw=$all_warehouses;
    foreach ($product['warehouses'] as $wkey => $warehouse) {
      if (isset($aw[$wkey])) $aw[$wkey]=true;
      //warehouse_id,product_id,balance
      $sqls[]="(".$wkey.",".$pkey.",".myNumberFormatNo0($warehouse['bal']).")";
      $total_balance+=$warehouse['bal'];
      
 
    }
    $sqls[]="(0,".$pkey.",".myNumberFormatNo0($total_balance).")";
    //print '<pre>';print_r($sqls);die();
    
    foreach ($aw as $wkey => $awv) {
      if ($awv==false) {
        $sqls[]="(".$wkey.",".$pkey.",0)";    
      }
    } 
    
    $sql="replace into gks_warehouse_balance_eidi (
    warehouse_id,product_id,balance
    ) values ".implode(',',$sqls);
    $result = $db_link->query($sql);
    //echo $sql.'<br>';
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
        
    //print '<pre>';print_r($sqls);print '</pre>';//die();

  }







  
  $mybal_lots_serials = gks_whi_mov_lots_serials_balance_calc($all_products_for_balance_input, $until_date);
//  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/wkey_array.txt',print_r($wkey_array,true));
//  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/fields_exist.txt',print_r($fields_exist,true));
//  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/mybal.txt',print_r($mybal,true));
  

  
  return $mybal;
}

function gks_whi_mov_lots_serials_balance_calc($all_products_for_balance_input, $until_date=null) {
  global $db_link; 
  global $GKS_PRODUCT_LOTS_SERIALS;
  
  if ($GKS_PRODUCT_LOTS_SERIALS==false) return array();
  if (is_array($all_products_for_balance_input)==false) return array();
  if (count($all_products_for_balance_input)<=0) return array();

//  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/all_products_for_balance_input'.time().'_'.rand(1000,9999).'.txt',
//    print_r($all_products_for_balance_input,true));

  //print '<pre>';print_r($all_products_for_balance_input);die();
  
  $all_products_for_balance=gks_whi_mov_all_products_for_balance_extend($all_products_for_balance_input);
  //print '<pre>';print_r($all_products_for_balance_input);die();

//  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/all_products_for_balance'.time().'_'.rand(1000,9999).'.txt',
//    print_r($all_products_for_balance,true));
  
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/all_products_for_balance_lots_serials'.rand(1000,9999).'.txt',print_r($all_products_for_balance,true));

  $mybal=array();
  $sql="SELECT id_lot_product FROM gks_eshop_product_lots WHERE lotproduct_id In (".implode(',',$all_products_for_balance).")";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $all_lots_for_balance=array();
  
  while ($row = $result->fetch_assoc()) {
    $all_lots_for_balance[]=$row['id_lot_product'];
    $mybal[$row['id_lot_product']]=array('total' => 0,'warehouses' => array());
  }
  //print '<pre>';print_r($mybal);die();
    
//  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/all_lots_for_balance'.time().'_'.rand(1000,9999).'.txt',
//    print_r($all_lots_for_balance,true));
      
  if (count($all_lots_for_balance)<=0) return array();
  

  //efugan-out whi lots_serials
  $sql="SELECT gks_whi_mov_products_lots.lot_product_id, 
  gks_whi_mov_products.product_id, 
  gks_whi_mov_products.p_warehouses_id_from,
  Sum(lot_product_quantity*monada_convert_epi_rev*if(seira_is_reverse_delivery_note=0,1,-1)) AS sumq
  FROM ((((gks_whi_mov_products_lots
  LEFT JOIN gks_whi_mov_products ON gks_whi_mov_products_lots.whi_mov_product_id = gks_whi_mov_products.id_whi_mov_product)
  LEFT JOIN gks_whi_mov ON gks_whi_mov_products.whi_mov_id = gks_whi_mov.id_whi_mov)
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_warehouses ON gks_whi_mov_products.p_warehouses_id_from = gks_warehouses.id_warehouse
  WHERE gks_whi_mov_products.whi_mov_id>0
  AND gks_eshop_products.product_base_type In (0,1)
  AND gks_whi_mov_products.p_mov_state In ('080listing','090ekdosi','100payment')
  AND gks_whi_mov.cancel_for_whi_mov_id=0
  AND gks_warehouses.is_virtual=0
  AND gks_whi_mov_products.product_id>2
  And gks_whi_mov_products_lots.lot_product_id In (".implode(',',$all_lots_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_whi_mov.mov_date<='".$until_date."'";
  }
  $sql.=" GROUP BY gks_whi_mov_products_lots.lot_product_id, gks_whi_mov_products.product_id, gks_whi_mov_products.p_warehouses_id_from";
  //echo '<pre>'.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['lot_product_id']])==false) $mybal[$row['lot_product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_from']])==false) $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_from']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_from']]['out']+=$row['sumq'];
  }
  
  
  //irthan-in whi lots_serials
  $sql="SELECT gks_whi_mov_products_lots.lot_product_id, 
  gks_whi_mov_products.product_id, 
  gks_whi_mov_products.p_warehouses_id_to,
  Sum(lot_product_quantity*monada_convert_epi_rev*if(seira_is_reverse_delivery_note=0,1,-1)) AS sumq
  FROM ((((gks_whi_mov_products_lots
  LEFT JOIN gks_whi_mov_products ON gks_whi_mov_products_lots.whi_mov_product_id = gks_whi_mov_products.id_whi_mov_product)
  LEFT JOIN gks_whi_mov ON gks_whi_mov_products.whi_mov_id = gks_whi_mov.id_whi_mov)
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_warehouses ON gks_whi_mov_products.p_warehouses_id_to = gks_warehouses.id_warehouse
  WHERE gks_whi_mov_products.whi_mov_id>0
  AND gks_eshop_products.product_base_type In (0,1)
  AND gks_whi_mov_products.p_mov_state In ('080listing','090ekdosi','100payment')
  AND gks_whi_mov.cancel_for_whi_mov_id=0
  AND gks_warehouses.is_virtual=0
  AND gks_whi_mov_products.product_id>2
  And gks_whi_mov_products_lots.lot_product_id In (".implode(',',$all_lots_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_whi_mov.mov_date<='".$until_date."'";
  }
  $sql.=" GROUP BY gks_whi_mov_products_lots.lot_product_id, gks_whi_mov_products.product_id, gks_whi_mov_products.p_warehouses_id_to";
  $result = $db_link->query($sql);    
  //echo '<pre>'.$sql;die();
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['lot_product_id']])==false) $mybal[$row['lot_product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_to']])==false) $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_to']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_to']]['in']+=$row['sumq'];
  }
  
  
  //efugan-out acc_inv lots_serials
  $sql="SELECT gks_acc_inv_products_lots.lot_product_id, gks_acc_inv_products.product_id, gks_acc_inv_products.p_warehouses_id_from,
  Sum(lot_product_quantity*monada_convert_epi_rev) AS sumq
  FROM (((gks_acc_inv_products_lots
  LEFT JOIN gks_acc_inv_products ON gks_acc_inv_products_lots.acc_inv_product_id = gks_acc_inv_products.id_acc_inv_product)
  LEFT JOIN gks_acc_inv ON gks_acc_inv_products.acc_inv_id = gks_acc_inv.id_acc_inv)
  LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_warehouses ON gks_acc_inv_products.p_warehouses_id_from = gks_warehouses.id_warehouse
  WHERE gks_acc_inv_products.acc_inv_id>0
  AND gks_eshop_products.product_base_type In (0,1)
  AND gks_acc_inv_products.p_inv_state In ('080listing','090ekdosi','100payment')
  AND gks_acc_inv.cancel_for_acc_inv_id=0
  AND gks_warehouses.is_virtual=0
  AND gks_acc_inv_products.product_id>2
  And gks_acc_inv_products_lots.lot_product_id In (".implode(',',$all_lots_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_acc_inv.inv_date<='".$until_date."'";
  }
  $sql.=" GROUP BY gks_acc_inv_products_lots.lot_product_id, gks_acc_inv_products.product_id, gks_acc_inv_products.p_warehouses_id_from";
  //echo '<pre>'.$sql;die(); 
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['lot_product_id']])==false) $mybal[$row['lot_product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_from']])==false) $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_from']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_from']]['out']+=$row['sumq'];
  }
  //echo '<pre>';print_r($mybal);die();
   
  
  //irthan-in acc_inv lots_serials
  $sql="SELECT gks_acc_inv_products_lots.lot_product_id, gks_acc_inv_products.product_id, gks_acc_inv_products.p_warehouses_id_to,
  Sum(lot_product_quantity*monada_convert_epi_rev) AS sumq
  FROM (((gks_acc_inv_products_lots
  LEFT JOIN gks_acc_inv_products ON gks_acc_inv_products_lots.acc_inv_product_id = gks_acc_inv_products.id_acc_inv_product)
  LEFT JOIN gks_acc_inv ON gks_acc_inv_products.acc_inv_id = gks_acc_inv.id_acc_inv)
  LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_warehouses ON gks_acc_inv_products.p_warehouses_id_to = gks_warehouses.id_warehouse
  WHERE gks_acc_inv_products.acc_inv_id>0
  AND gks_eshop_products.product_base_type In (0,1)
  AND gks_acc_inv_products.p_inv_state In ('080listing','090ekdosi','100payment')
  AND gks_acc_inv.cancel_for_acc_inv_id=0
  AND gks_warehouses.is_virtual=0
  AND gks_acc_inv_products.product_id>2
  And gks_acc_inv_products_lots.lot_product_id In (".implode(',',$all_lots_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_acc_inv.inv_date<='".$until_date."'";
  }
  $sql.=" GROUP BY gks_acc_inv_products_lots.lot_product_id, gks_acc_inv_products.product_id, gks_acc_inv_products.p_warehouses_id_to";
  //echo '<pre>'.$sql;die(); 
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['lot_product_id']])==false) $mybal[$row['lot_product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_to']])==false) $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_to']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_to']]['in']+=$row['sumq'];
  }
  //echo '<pre>';print_r($mybal);die();
    
    
  //efugan-out order lots_serials
  //and cancel_for_order_id=0 
  $sql="SELECT gks_orders_products_lots.lot_product_id, gks_orders_products.product_id, gks_orders_products.p_warehouses_id_from,
  Sum(lot_product_quantity*monada_convert_epi_rev) AS sumq
  FROM (((gks_orders_products_lots
  LEFT JOIN gks_orders_products ON gks_orders_products_lots.order_product_id = gks_orders_products.id_order_product)
  LEFT JOIN gks_orders ON gks_orders_products.order_id = gks_orders.id_order)
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_warehouses ON gks_orders_products.p_warehouses_id_from = gks_warehouses.id_warehouse
  WHERE gks_orders_products.order_id>0
  AND gks_eshop_products.product_base_type In (0,1)
  AND gks_orders_products.p_order_state In ('060registered','070inproduction','090indelivery','095execute','100completed','110payment')
  AND gks_warehouses.is_virtual=0
  AND gks_orders_products.product_id>2
  AND gks_orders_products.product_is_optional in (0,2)
  AND gks_orders_products_lots.lot_product_id In (".implode(',',$all_lots_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_orders.order_date<='".$until_date."'";
  }
  $sql.=" GROUP BY gks_orders_products_lots.lot_product_id, gks_orders_products.product_id, gks_orders_products.p_warehouses_id_from";
  //echo '<pre>'.$sql;die(); 
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['lot_product_id']])==false) $mybal[$row['lot_product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_from']])==false) $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_from']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_from']]['out']+=$row['sumq'];
  }
  
  
  //irthan-in order lots_serials
  //and cancel_for_order_id=0 
  $sql="SELECT gks_orders_products_lots.lot_product_id, gks_orders_products.product_id, gks_orders_products.p_warehouses_id_to,
  Sum(lot_product_quantity*monada_convert_epi_rev) AS sumq
  FROM (((gks_orders_products_lots
  LEFT JOIN gks_orders_products ON gks_orders_products_lots.order_product_id = gks_orders_products.id_order_product)
  LEFT JOIN gks_orders ON gks_orders_products.order_id = gks_orders.id_order)
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_warehouses ON gks_orders_products.p_warehouses_id_to = gks_warehouses.id_warehouse
  WHERE gks_orders_products.order_id>0
  AND gks_eshop_products.product_base_type In (0,1)
  AND gks_orders_products.p_order_state In ('060registered','070inproduction','090indelivery','095execute','100completed','110payment')
  AND gks_warehouses.is_virtual=0
  AND gks_orders_products.product_id>2
  AND gks_orders_products.product_is_optional in (0,2)
  AND gks_orders_products_lots.lot_product_id In (".implode(',',$all_lots_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_orders.order_date<='".$until_date."'";
  }
  $sql.=" GROUP BY gks_orders_products_lots.lot_product_id, gks_orders_products.product_id, gks_orders_products.p_warehouses_id_to";
  //echo '<pre>'.$sql;die(); 
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['lot_product_id']])==false) $mybal[$row['lot_product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_to']])==false) $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_to']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['lot_product_id']]['warehouses'][$row['p_warehouses_id_to']]['in']+=$row['sumq'];
  }
  


    //paragogi kai sintages. otan stin paragogi xrisimopoiite kapoio eidos me partida kai serial number gia na paraxtuei allo proion
    //tha prepei kata tin diadikasia tis paragogis me kapoio tropo na epileksei o ipallilos poia partida xrhsimopoihse
    
  //efugan-out gks_production_sintagi_product lots_serials
  //and cancel_for_order_id=0 
  //*monada_convert_epi_rev
  $sql="SELECT gks_production_sintagi_product_lots_serials.lot_product_id, gks_production_sintagi_product.spbom_product_id, 
  gks_production_sintagi_product.sp_warehouses_id_from, Sum(spbom_quantity*monada_convert_epi_rev) AS sumq
  FROM (((gks_production_sintagi_product_lots_serials 
  LEFT JOIN gks_production_sintagi_product ON gks_production_sintagi_product_lots_serials.production_sintagi_product_id = gks_production_sintagi_product.id_production_sintagi_product) 
  LEFT JOIN gks_orders ON gks_production_sintagi_product.order_id = gks_orders.id_order) 
  LEFT JOIN gks_eshop_products ON gks_production_sintagi_product.spbom_product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_warehouses ON gks_production_sintagi_product.sp_warehouses_id_from = gks_warehouses.id_warehouse
  WHERE gks_production_sintagi_product.order_id>0
  AND gks_eshop_products.product_base_type In (0,1)
  AND gks_production_sintagi_product.sp_order_state In ('070inproduction','090indelivery','095execute','100completed','110payment')
  AND gks_warehouses.is_virtual=0
  AND gks_production_sintagi_product.spbom_product_id>2
  And gks_production_sintagi_product_lots_serials.lot_product_id In (".implode(',',$all_lots_for_balance).") ";
  if ($until_date!==null) {
    $sql.=" and gks_orders.order_date<='".$until_date."'";
  }
  $sql.=" GROUP BY gks_production_sintagi_product_lots_serials.lot_product_id, 
  gks_production_sintagi_product.spbom_product_id, 
  gks_production_sintagi_product.sp_warehouses_id_from;";
 
  //echo '<pre>'.$sql;die();  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  while ($row = $result->fetch_assoc()) {
    if (isset($mybal[$row['lot_product_id']])==false) $mybal[$row['lot_product_id']]=array(
      'total'=>0,
      'warehouses'=>array(),
    );
    if (isset($mybal[$row['lot_product_id']]['warehouses'][$row['sp_warehouses_id_from']])==false) $mybal[$row['lot_product_id']]['warehouses'][$row['sp_warehouses_id_from']]=array(
      'in'=>0,
      'out'=>0,
      'bal'=>0,
    );
    $mybal[$row['lot_product_id']]['warehouses'][$row['sp_warehouses_id_from']]['out']+=$row['sumq'];
  }
  //echo '<pre>'; print_r($mybal);die();  
  
  
  
  
  foreach ($mybal as $pkey => &$product) {
    foreach ($product['warehouses'] as $wkey => &$warehouse) {
      $warehouse['bal']=$warehouse['in']-$warehouse['out'];
      $mybal[$pkey]['total']+=$warehouse['bal'];
    }
    unset($warehouse);
  } 
  unset($product);
  
  //echo '<pre>'; print_r($mybal);die();  
  
  if ($until_date!==null) {
    
    return $mybal;
  }
  //echo 'fffff';die();
  
    
  
  $sql="select id_warehouse from gks_warehouses order by id_warehouse";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return['message']='sql error';return $return;}
  
  $all_warehouses=[];
  while ($row = $result->fetch_assoc()) {
    $all_warehouses[$row['id_warehouse']]=false;
  }
  
  //print '<pre>asaaaaaa ';print_r($all_warehouses);print_r($mybal);die();

  $sqls=[];
  foreach ($mybal as $pkey => $product) {
    //$wkey_array=array();
    $total_balance=0;
    $aw=$all_warehouses;
    foreach ($product['warehouses'] as $wkey => $warehouse) {
      if (isset($aw[$wkey])) $aw[$wkey]=true;
      //warehouse_id,lot_product_id,balance
      $sqls[]="(".$wkey.",".$pkey.",".myNumberFormatNo0($warehouse['bal']).")";
      $total_balance+=$warehouse['bal'];
      
 
    }
    $sqls[]="(0,".$pkey.",".myNumberFormatNo0($total_balance).")";
    //print '<pre>';print_r($sqls);die();
    
    foreach ($aw as $wkey => $awv) {
      if ($awv==false) {
        $sqls[]="(".$wkey.",".$pkey.",0)";    
      }
    } 
    
    $sql="replace into gks_warehouse_balance_lots_serials (
    warehouse_id,lot_product_id,balance
    ) values ".implode(',',$sqls);
    $result = $db_link->query($sql);
    //echo $sql.'<br>';
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
        
    //print '<pre>';print_r($sqls);print '</pre>';//die();

  }


  
  return $mybal;
}






function gks_whi_mov_get_ekdosi_numbers() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $mov_whi_number_int_old;
  global $mov_whi_number_int_new;
  global $mov_whi_number_str_new;
  global $mov_whi_seira_code_new;
  global $mov_whi_seira_id;
  global $has_ekdosi;
  global $save_but_message;
  global $id;
  global $mov_state;
  
  //die('<pre>mov_whi_number_int_old:'.$mov_whi_number_int_old);
  if ($mov_whi_number_int_old>0) {
    $sql_auto_number="select auto_number from gks_acc_seires_auto_numbers where disabled_date is null and acc_seira_id=".$mov_whi_seira_id." and whi_mov_id=".$id;
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_auto_number->num_rows>=1) {
      $row_auto_number = $result_auto_number->fetch_assoc();    
      $mov_whi_number_int_old=$row_auto_number['auto_number'];
      $mov_whi_number_int_new=$row_auto_number['auto_number'];

      $sql="select * from gks_acc_seires where id_acc_seira=".$mov_whi_seira_id;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      $row_seira = $result->fetch_assoc();
      $mov_whi_seira_code_new=trim_gks($row_seira['seira_code']);
      $seires_prefix=trim_gks($row_seira['prefix']);
      $seires_suffix=trim_gks($row_seira['suffix']);
      $seires_number_size=$row_seira['number_size'];
      $mov_whi_number_str_new=$seires_prefix.str_pad($mov_whi_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
      $has_ekdosi=true;
    }
  }
  
  if ($mov_whi_number_int_old==0) {
    $mov_state='';
    
    
    
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select * from gks_acc_seires where id_acc_seira=".$mov_whi_seira_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $row_seira = $result->fetch_assoc();
    $mov_whi_seira_code_new=trim_gks($row_seira['seira_code']);
    $seires_prefix=trim_gks($row_seira['prefix']);
    $seires_suffix=trim_gks($row_seira['suffix']);
    $seires_number_size=$row_seira['number_size'];
    $mov_whi_number_int_new=$row_seira['next_number'];
    $mov_whi_number_str_new=$seires_prefix.str_pad($mov_whi_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
    //$save_but_message='<pre>'.$mov_whi_number_str_new;
    
    $sql="update gks_acc_seires set next_number=next_number+number_step where id_acc_seira=".$mov_whi_seira_id;
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
  
    $mov_state='090ekdosi';
    $has_ekdosi=true;
    if ($save_but_message!='') {
      $save_but_message=gks_lang('Το δελτίο έχει αποθηκευτεί αλλά δεν έχει εκδοθεί διότι').':<br>'.$save_but_message;
    }
    
    $sql="insert into gks_acc_seires_auto_numbers (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_seira_id,whi_mov_id,auto_number
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$mov_whi_seira_id.",".$id.",".$mov_whi_number_int_new."
    )";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
  
}

function gks_whi_after_balance_for_inv($id) {
  global $db_link;
  
  //if ($p_mov_state=='090ekdosi') {
    //$sql="select mov_date from gks_whi_mov where id_whi_mov=".$id." and mov_state='090ekdosi' and mov_date is not null";
    $sql="select inv_date, warehouses_id_from, warehouses_id_to from gks_acc_inv where id_acc_inv=".$id." and inv_date is not null";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      $inv_date=$row['inv_date'];
      $warehouses_id_from=$row['warehouses_id_from'];
      $warehouses_id_to=$row['warehouses_id_to'];
      
      
      $sql="SELECT product_id FROM gks_acc_inv_products WHERE acc_inv_id=".$id." GROUP BY product_id ORDER BY product_id";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      $all_products_for_balance=array();
      while ($row = $result->fetch_assoc()) {
        $all_products_for_balance[]=$row['product_id'];
      }
      
      
      $mybal = gks_whi_mov_balance_calc($all_products_for_balance,$inv_date);
      //echo '<pre>'; print_r($mybal); echo '</pre>';die();
      
      foreach ($mybal as $id_product => $data) {
        $after_balance_warehouses_id_from=0;
        $after_balance_warehouses_id_to=0;
        if (isset($data['warehouses'][$warehouses_id_from])) $after_balance_warehouses_id_from=$data['warehouses'][$warehouses_id_from]['bal'];
        if (isset($data['warehouses'][$warehouses_id_to]))   $after_balance_warehouses_id_to=  $data['warehouses'][$warehouses_id_to]['bal'];
        
        $sql="update gks_acc_inv_products set 
        after_balance_warehouses_id_from=".number_format($after_balance_warehouses_id_from, 8, '.', '').",
        after_balance_warehouses_id_to=".number_format($after_balance_warehouses_id_to, 8, '.', '')."
        where acc_inv_id=".$id." and product_id=".$id_product;
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}    
      } 
    }
    
  //}  
  
  
}


function gks_whi_after_balance_for_order($id) {
  global $db_link;
  
    $sql="select order_date, warehouses_id_from, warehouses_id_to from gks_orders where id_order=".$id." and order_date is not null";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      $order_date=$row['order_date'];
      $warehouses_id_from=$row['warehouses_id_from'];
      $warehouses_id_to=$row['warehouses_id_to'];
      
      
      $sql="SELECT product_id FROM gks_orders_products WHERE order_id=".$id." GROUP BY product_id ORDER BY product_id";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      $all_products_for_balance=array();
      while ($row = $result->fetch_assoc()) {
        $all_products_for_balance[]=$row['product_id'];
      }
      
      
      $mybal = gks_whi_mov_balance_calc($all_products_for_balance,$order_date);
      //echo '<pre>'; print_r($mybal); echo '</pre>';die();
      
      foreach ($mybal as $id_product => $data) {
        $after_balance_warehouses_id_from=0;
        $after_balance_warehouses_id_to=0;
        if (isset($data['warehouses'][$warehouses_id_from])) $after_balance_warehouses_id_from=$data['warehouses'][$warehouses_id_from]['bal'];
        if (isset($data['warehouses'][$warehouses_id_to]))   $after_balance_warehouses_id_to=  $data['warehouses'][$warehouses_id_to]['bal'];
        
        $sql="update gks_orders_products set 
        after_balance_warehouses_id_from=".number_format($after_balance_warehouses_id_from, 8, '.', '').",
        after_balance_warehouses_id_to=".number_format($after_balance_warehouses_id_to, 8, '.', '')."
        where order_id=".$id." and product_id=".$id_product;
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}    
      } 
    }
    
  //}  
  
  
}

function gks_whi_mov_sxolio_log($id,$row_old,$products_old,$extra_address_old,$sxolio_log_start,$myparams,$gks_custom_row_old) {
  global $db_link;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_CRM_ENABLE;
  
  $ret_aade_errors='';
  if (isset($myparams['ret_aade_errors'])) $ret_aade_errors=trim_gks($myparams['ret_aade_errors']);
  
  $sql=select_gks_whi_mov()." where id_whi_mov=".$id." limit 1"; 

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
  $eidos_parastatikou_type_id=$row_new['eidos_parastatikou_type_id'];
  
  $sql="SELECT gks_whi_mov_products.*, gks_monades_metrisis.monada_descr
  FROM gks_whi_mov_products 
  LEFT JOIN gks_monades_metrisis ON gks_whi_mov_products.product_monada_id = gks_monades_metrisis.id_monada
  WHERE gks_whi_mov_products.whi_mov_id=".$id."
  ORDER BY gks_whi_mov_products.product_aa;";
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

  if (trim_gks($row_old['mov_whi_number_int']) != trim_gks($row_new['mov_whi_number_int'])) 
    $sxolio_log.=gks_lang('Αριθμός').': <b>'.$row_old['mov_whi_number_int'].'</b> [[-r]] <b>'.$row_new['mov_whi_number_int'].'</b>'.'<br>';

  if (trim_gks($row_old['mov_date']) != trim_gks($row_new['mov_date'])) 
    $sxolio_log.=gks_lang('Ημερομηνία').': <b>'.showDate(strtotime($row_old['mov_date']), 'd/m/Y H:i', 1).'</b> [[-r]] <b>'.showDate(strtotime($row_new['mov_date']), 'd/m/Y H:i', 1).'</b>'.'<br>';

  if ($row_old['mov_state'].'' != $row_new['mov_state'].'') 
    $sxolio_log.=gks_lang('Κατάσταση').': <span class="whi_mov_state_'.$row_old['mov_state'].'">'.getWhiMovStateDescr($row_old['mov_state']).'</span> [[-r]] '.
    '<span class="whi_mov_state_'.$row_new['mov_state'].'">'.getWhiMovStateDescr($row_new['mov_state']).'</span>'.'<br>';

  if (trim_gks($row_old['fiscal_position_descr']) != trim_gks($row_new['fiscal_position_descr'])) 
    $sxolio_log.=gks_lang('Φορολογική Θέση').': <b>'.$row_old['fiscal_position_descr'].'</b> [[-r]] <b>'.$row_new['fiscal_position_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['pricelist_descr']) != trim_gks($row_new['pricelist_descr'])) 
    $sxolio_log.=gks_lang('Τιμοκατάλογος').': <b>'.$row_old['pricelist_descr'].'</b> [[-r]] <b>'.$row_new['pricelist_descr'].'</b>'.'<br>';

  



  //echo '<pre>'.$row_new['company_title']; die();
  
  
  
  if ((isset($row_old['gks_nickname']) and isset($row_old['gks_nickname']) == false) or 
      (isset($row_old['gks_nickname']) == false and isset($row_old['gks_nickname'])) or 
      $row_old['gks_nickname'] != $row_new['gks_nickname']) 
    $sxolio_log.=gks_lang('Πελάτης').': <b>'.(isset($row_old['gks_nickname']) ? $row_old['gks_nickname'] : '').'</b> [[-r]] '.
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
      $extra_address_old=array('ea_name'=>'', 'ea_phone'=>'', 'ea_odos'=>'', 'ea_arithmos'=>'', 'ea_orofos'=>'', 'ea_perioxi'=>'', 'ea_poli'=>'', 'ea_tk'=>'','nomos_descr'=>'','country_name'=>'');
    }
    
    $extra_address_new=array('ea_name'=>'', 'ea_phone'=>'','ea_odos'=>'','ea_arithmos'=>'','ea_orofos'=>'', 'ea_perioxi'=>'','ea_poli'=>'','ea_tk'=>'','nomos_descr'=>'','country_name'=>'');
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
  
    if (trim_gks($extra_address_old['ea_orofos']) != trim_gks($extra_address_new['ea_orofos'])) 
      $sxolio_log.=gks_lang('Όροφος Αποστολής').': <b>'.$extra_address_old['ea_orofos'].'</b> [[-r]] <b>'.$extra_address_new['ea_orofos'].'</b>'.'<br>';
  
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
      if ($pitem_old['id_whi_mov_product'] == $pitem_new['id_whi_mov_product']) {
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
      gks_lang('Ποσότητα').': <b>'.($eidos_parastatikou_type_id==24 ? $pitem_new['apografi_posotitaonhand'] : $pitem_new['product_quantity']).'</b> '.
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
      
      
      if ($p['product_comments'] != $pn[$p['k']]['product_comments']) 
        $item_txt.=gks_lang('Παρατηρήσεις').': <b>'.$p['product_comments'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_comments'].'</b> ';
      if ($eidos_parastatikou_type_id==24) { //apografi
        if ($p['apografi_posotitaonhand'] != $pn[$p['k']]['apografi_posotitaonhand']) 
          $item_txt.=gks_lang('Ποσότητα').': <b>'.$p['apografi_posotitaonhand'].'</b> [[-r]] <b>'.$pn[$p['k']]['apografi_posotitaonhand'].'</b> ';
      } else {
        if ($p['product_quantity'] != $pn[$p['k']]['product_quantity']) 
          $item_txt.=gks_lang('Ποσότητα').': <b>'.$p['product_quantity'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_quantity'].'</b> ';
      }
      if ($p['monada_descr'] != $pn[$p['k']]['monada_descr']) 
        $item_txt.=gks_lang('Μονάδα μέτρησης').': <b>'.$p['monada_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['monada_descr'].'</b> ';
      
      

      
        
        
        
      if ($item_txt != '') {
        $item_txt=trim_gks($item_txt);
        if ($item_descr_change!='') $item_txt=$item_descr_change.': '.$item_txt;
        else $item_txt=$p['product_descr'].': '.$item_txt;
        
        $sxolio_eidi_log.=$item_txt.'<br>';
        
      }
    }
  }  
  
  $sxolio_log.=$sxolio_eidi_log;


  if (trim_gks($row_old['kostos_apostolis']) != trim_gks($row_new['kostos_apostolis'])) 
    $sxolio_log.=gks_lang('Κόστος αποστολής').': <b>'.myCurrencyFormat($row_old['kostos_apostolis']).'</b> [[-r]] <b>'.myCurrencyFormat($row_new['kostos_apostolis']).'</b>'.'<br>';




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
  


  if ((isset($row_old['note_doc']) and isset($row_old['note_doc']) == false) or 
      (isset($row_old['note_doc']) == false and isset($row_old['note_doc'])) or 
      $row_old['note_doc'] != $row_new['note_doc']) 
    $sxolio_log.=gks_lang('Σχόλια τιμολογίου').':<br><b>'.(isset($row_old['note_doc']) ? $row_old['note_doc'] : '').'</b> [[-r]] '.
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
 


//  if (trim_gks($row_old['print_date']) != trim_gks($row_new['print_date'])) 
//    $sxolio_log.=gks_lang('Ημερομηνία Εκτύπωσης').': '.
//                 (isset($row_old['print_date']) ? '<b>'.showDate(strtotime($row_old['print_date']), 'd/m/Y H:i', 1).'</b>' : '').
//                 ' [[-r]] '.
//                 (isset($row_new['print_date']) ? '<b>'.showDate(strtotime($row_new['print_date']), 'd/m/Y H:i', 1).'</b>' : '').
//                 '<br>';

//  if (trim_gks($row_old['print_mov_state']) != trim_gks($row_new['print_mov_state'])) 
//    $sxolio_log.=gks_lang('Κατάσταση όταν έγινε η εκτύπωση').': '.
//      (trim_gks($row_old['print_mov_state'])=='' ? '' :
//      '<span class="whi_mov_state_'.$row_old['print_mov_state'].'">'.getWhiMovStateDescr($row_old['print_mov_state']).'</span>') .
//      ' [[-r]] '.
//      (trim_gks($row_new['print_mov_state'])=='' ? '' :
//      '<span class="whi_mov_state_'.$row_new['print_mov_state'].'">'.getWhiMovStateDescr($row_new['print_mov_state']).'</span>') .
//      .'<br>';
//  
//  if (trim_gks($row_old['print_file_name']) != trim_gks($row_new['print_file_name'])) {
//    $temp_new='';
//    if (trim_gks($row_new['print_file_name'])!='') {
//      $local_file=GKS_FileServerShare.'whi/mov/'.$id.'/print/'.$row_new['print_file_name'];
//      if (file_exists($local_file)) {
//        $url_file='admin-get-file.php?fs=fileservers&file=whi%2Fmov%2F'.$id.'%2Fprint%2F'.urlencode($row_new['print_file_name']);
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
      $local_file=GKS_FileServerShare.'whi/mov/'.$id.'/aade_mydata/'.$row_new['aade_xml_send'];
      if (file_exists($local_file)) {
        $url_file='admin-get-file.php?fs=fileservers&file=whi%2Fmov%2F'.$id.'%2Faade_mydata%2F'.urlencode($row_new['aade_xml_send']);
        $temp_new.= '<a href="'.$url_file.'" target="_blank">'.$row_new['aade_xml_send'].'</a> ';
        $temp_new.= '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
      }
    }    
    $sxolio_log.=gks_lang('ΑΑΔΕ - Απεσταλμένο XML').': [[-r]] '.$temp_new.'<br>';    
  }

  if (trim_gks($row_old['aade_xml_response']) != trim_gks($row_new['aade_xml_response'])) {
    $temp_new='';
    if (trim_gks($row_new['aade_xml_response'])!='') {
      $local_file=GKS_FileServerShare.'whi/mov/'.$id.'/aade_mydata/'.$row_new['aade_xml_response'];
      if (file_exists($local_file)) {
        $url_file='admin-get-file.php?fs=fileservers&file=whi%2Fmov%2F'.$id.'%2Faade_mydata%2F'.urlencode($row_new['aade_xml_response']);
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


  $gks_custom_prepare=gks_custom_table_item_prepare('gks_whi_mov',['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;

  
  
  if ($sxolio_log == '') $sxolio_log=gks_lang('Ενημέρωση').'<br>';
  //print '<pre>';
  //print_r($products_old);
  //die();  
  
  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into gks_whi_mov_log (whi_mov_id, add_date,user_id,sxolio) values (
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
