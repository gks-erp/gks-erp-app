<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

  
function select_gks_hotel_reservation() {
$sql ="SELECT gks_hotel_reservation.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
gks_country.country_name, gks_nomoi.nomos_descr, 
gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
gks_country.country_initials,gks_country.country_name,gks_country_en_US.country_name_en_US,
gks_lang.lang_ico, gks_lang.lang_name, gks_lang_en_US.lang_name_en_US,
gks_users.pelati_sxolio, gks_users.order_sxolio,
gks_hotel.hotel_title,
gks_hotel.company_id, gks_hotel.company_sub_id,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,gks_acc_seires.send_mydata,
gks_acc_journal.acc_eidos_parastatikou_id,
gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, antisimvalomenos_label, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
gks_payment_acquirers.payment_acquirer_name,
gks_hotel.hotel_color

FROM ((((((((((((((((((((((((gks_hotel_reservation 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_reservation.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_reservation.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_print ON gks_hotel_reservation.print_user_id = ".GKS_WP_TABLE_PREFIX."users_print.ID) 
LEFT JOIN gks_country ON gks_hotel_reservation.ma_country_id = gks_country.id_country)
LEFT JOIN (
  SELECT country_id, country_name as country_name_en_US FROM gks_country_lang WHERE lang_code='en-US'
) AS gks_country_en_US ON gks_country.id_country = gks_country_en_US.country_id)


LEFT JOIN gks_nomoi ON gks_hotel_reservation.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_eshop_fiscal_position ON gks_hotel_reservation.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position)
LEFT JOIN gks_eshop_pricelist ON gks_hotel_reservation.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_lang ON gks_hotel_reservation.user_lang = gks_lang.id_lang)
LEFT JOIN (
  SELECT lang_idd, lang_name as lang_name_en_US FROM gks_lang_lang WHERE lang_code='en-US'
) AS gks_lang_en_US ON gks_lang.idd_lang = gks_lang_en_US.lang_idd)

LEFT JOIN gks_users on gks_hotel_reservation.user_id = gks_users.user_id)
LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_hotel_reservation.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_hotel_reservation.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_hotel_reservation.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_hotel_reservation.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_company on gks_hotel.company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_hotel.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_hotel_reservation.reservation_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_hotel_reservation.reservation_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_payment_acquirers ON gks_hotel_reservation.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer

";



return $sql;
}


function select_gks_hotel_reservation_room() {
$sql_rooms="SELECT gks_hotel_reservation_room.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
gks_hotel_room.id_hotel_room,
gks_hotel_room.room_descr, gks_hotel_room_en_US.room_descr_en_US, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_lang.idd_lang,gks_lang.lang_name, gks_lang.lang_ico, 
gks_country.country_initials, gks_country.country_name, 
gks_nomoi.nomos_descr,
gks_hotel_room_type.id_hotel_room_type,
gks_hotel_room_type.room_type_descr,gks_hotel_room_type_en_US.room_type_descr_en_US,
gks_hotel_room_type.room_type_visitors,gks_hotel_room_type.room_type_visitors_childs,gks_hotel_room_type.room_type_visitors_max,
gks_hotel_room_type.room_type_child_kounies,gks_hotel_room_type.room_type_extra_beds,
gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,

gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_descr, 
gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr, 
gks_aade_katigoria_telon.aade_katigoria_telon_descr, 
gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr,
gks_hotel_room.hotel_id as hotel_id_from_room,
gks_monades_metrisis.monada_descr, gks_eshop_fpa_base.fpa_base_descr,
gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr

FROM (((((((((((((((((((gks_hotel_reservation_room 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_reservation_room.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_reservation_room.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN gks_hotel_room ON gks_hotel_reservation_room.hotel_room_id = gks_hotel_room.id_hotel_room) 
LEFT JOIN (
  SELECT hotel_room_id, room_descr as room_descr_en_US FROM gks_hotel_room_lang WHERE lang_code='en-US'
) AS gks_hotel_room_en_US ON gks_hotel_room.id_hotel_room = gks_hotel_room_en_US.hotel_room_id)
LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
LEFT JOIN (
  SELECT hotel_room_type_id, room_type_descr as room_type_descr_en_US FROM gks_hotel_room_type_lang WHERE lang_code='en-US'
) AS gks_hotel_room_type_en_US ON gks_hotel_room_type.id_hotel_room_type = gks_hotel_room_type_en_US.hotel_room_type_id)  
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation_room.ruser_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_lang ON gks_hotel_reservation_room.ruser_lang = gks_lang.id_lang) 
LEFT JOIN gks_country ON gks_hotel_reservation_room.ruser_ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_hotel_reservation_room.ruser_ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_eshop_fpa ON gks_hotel_reservation_room.product_fpa_id = gks_eshop_fpa.id_fpa)

LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_hotel_reservation_room.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron) 
LEFT JOIN gks_aade_katigoria_xartosimou ON gks_hotel_reservation_room.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou) 
LEFT JOIN gks_aade_katigoria_telon ON gks_hotel_reservation_room.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon) 
LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_hotel_reservation_room.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron)

LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product)
LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada)
LEFT JOIN gks_eshop_fpa_base ON gks_hotel_reservation_room.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base)
LEFT JOIN gks_eshop_fiscal_position ON gks_hotel_reservation_room.ruser_fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position)
LEFT JOIN gks_eshop_pricelist ON gks_hotel_reservation_room.ruser_pricelist_id = gks_eshop_pricelist.id_pricelist

";   
 //echo '<pre>';echo $sql_rooms;die();
return $sql_rooms;
}



function gks_hotel_reservation_sxolio_log($id,$row_old,$rooms_old,$sxolio_log_start,$gks_custom_row_old) {
  global $db_link;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_CRM_ENABLE;
  
  $sql=select_gks_hotel_reservation()." where id_hotel_reservation=".$id." limit 1"; 

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

  $sql_rooms=select_gks_hotel_reservation_room();  
  $sql_rooms.=" where hotel_reservation_id=".$id."
  order by id_hotel_reservation_room";
  
  $result = $db_link->query($sql_rooms);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_rooms);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $rooms_new=array();
  while ($row = $result->fetch_assoc()) {
    $rooms_new[]=$row;
  }
    
  
  $sxolio_log=$sxolio_log_start;
  
  
  
  if (trim_gks($row_old['hotel_title']) != trim_gks($row_new['hotel_title'])) 
    $sxolio_log.=gks_lang('Ξενοδοχείο').': <b>'.$row_old['hotel_title'].'</b> [[-r]] <b>'.$row_new['hotel_title'].'</b>'.'<br>';
    
    
  if (trim_gks($row_old['company_title']) != trim_gks($row_new['company_title'])) 
    $sxolio_log.=gks_lang('Εταιρεία').': <b>'.$row_old['company_title'].'</b> [[-r]] <b>'.$row_new['company_title'].'</b>'.'<br>';
  
  if (trim_gks($row_old['company_sub_title']) != trim_gks($row_new['company_sub_title'])) 
    $sxolio_log.=gks_lang('Υποκατάστημα').': <b>'.$row_old['company_sub_title'].'</b> [[-r]] <b>'.$row_new['company_sub_title'].'</b>'.'<br>';

  if (trim_gks($row_old['acc_journal_descr']) != trim_gks($row_new['acc_journal_descr'])) 
    $sxolio_log.=gks_lang('Ημερολόγιο').': <b>'.$row_old['acc_journal_descr'].'</b> [[-r]] <b>'.$row_new['acc_journal_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['seira_code'].' - '.$row_old['seira_descr']) != trim_gks($row_new['seira_code'].' - '.$row_new['seira_descr'])) 
    $sxolio_log.=gks_lang('Σειρά').': <b>'.$row_old['seira_code'].' - '.$row_old['seira_descr'].'</b> [[-r]] <b>'.$row_new['seira_code'].' - '.$row_new['seira_descr'].'</b>'.'<br>';

  if (trim_gks($row_old['reservation_number_int']) != trim_gks($row_new['reservation_number_int'])) 
    $sxolio_log.=gks_lang('Αριθμός').': <b>'.$row_old['reservation_number_int'].'</b> [[-r]] <b>'.$row_new['reservation_number_int'].'</b>'.'<br>';

  if (showDate(strtotime($row_old['reservation_date']), 'd/m/Y H:i', 1)!=showDate(strtotime($row_new['reservation_date']), 'd/m/Y H:i', 1)) 
    $sxolio_log.=gks_lang('Ημερομηνία Κράτησης').': <b>'.showDate(strtotime($row_old['reservation_date']), 'd/m/Y H:i', 1).'</b> [[-r]] <b>'.showDate(strtotime($row_new['reservation_date']), 'd/m/Y H:i', 1).'</b>'.'<br>';


  if ($row_old['reservation_status'].'' != $row_new['reservation_status'].'') 
    $sxolio_log.=gks_lang('Κατάσταση').': <span class="reservation_status_'.$row_old['reservation_status'].'">'.getHotelReservationStatusDescr($row_old['reservation_status']).'</span> [[-r]] '.
    '<span class="reservation_status_'.$row_new['reservation_status'].'">'.getHotelReservationStatusDescr($row_new['reservation_status']).'</span>'.'<br>';

  if (myDateTimeFormatw(strtotime($row_old['check_in']))!=myDateTimeFormatw(strtotime($row_new['check_in']))) 
    $sxolio_log.=gks_lang('Άφιξη').': <b>'.myDateTimeFormatw(strtotime($row_old['check_in'])).'</b> [[-r]] <b>'.myDateTimeFormatw(strtotime($row_new['check_in'])).'</b>'.'<br>';

  if (myDateTimeFormatw(strtotime($row_old['check_out']))!=myDateTimeFormatw(strtotime($row_new['check_out']))) 
    $sxolio_log.=gks_lang('Αναχώρηση').': <b>'.myDateTimeFormatw(strtotime($row_old['check_out'])).'</b> [[-r]] <b>'.myDateTimeFormatw(strtotime($row_new['check_out'])).'</b>'.'<br>';

  
  if (trim_gks($row_old['num_days']) != trim_gks($row_new['num_days'])) 
    $sxolio_log.=gks_lang('Διανυκτερεύσεις').': <b>'.$row_old['num_days'].'</b> [[-r]] <b>'.$row_new['num_days'].'</b>'.'<br>';

  if (trim_gks($row_old['num_adults']) != trim_gks($row_new['num_adults'])) 
    $sxolio_log.=gks_lang('Ενήλικες').': <b>'.$row_old['num_adults'].'</b> [[-r]] <b>'.$row_new['num_adults'].'</b>'.'<br>';

  if (trim_gks($row_old['num_childs']) != trim_gks($row_new['num_childs'])) 
    $sxolio_log.=gks_lang('Παιδιά').': <b>'.$row_old['num_childs'].'</b> [[-r]] <b>'.$row_new['num_childs'].'</b>'.'<br>';


  $childs_ages_list_str_old=''; if (trim_gks($row_old['childs_ages_list'])!='') $childs_ages_list_str_old=implode(',',json_decode($row_old['childs_ages_list'],true));
  $childs_ages_list_str_new=''; if (trim_gks($row_new['childs_ages_list'])!='') $childs_ages_list_str_new=implode(',',json_decode($row_new['childs_ages_list'],true));
  if ($childs_ages_list_str_old != $childs_ages_list_str_new) 
    $sxolio_log.=gks_lang('Ηλικίες παιδιών').': <b>'.$childs_ages_list_str_old.'</b> [[-r]] <b>'.$childs_ages_list_str_new.'</b>'.'<br>';


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

  if (trim_gks($row_old['parastatiko']) != trim_gks($row_new['parastatiko'])) 
    $sxolio_log.=gks_lang('Τύπος Παραστατικού').': <b>'.($row_old['parastatiko']==0 ? gks_lang('Απόδειξη') : gks_lang('Τιμολόγιο')).'</b> [[-r]] <b>'.($row_new['parastatiko']==0 ? 'Απόδειξη' : 'Τιμολόγιο').'</b>'.'<br>';


  
  
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

  
  

  
    
  //compare products_old products_new
  foreach ($rooms_old as &$pitem_old) {
    $pitem_old['del']=false;
    $pitem_old['k']=-1;
  }
  unset($pitem_old);
  foreach ($rooms_new as &$pitem_new) {
    $pitem_new['del']=false;
    $pitem_new['k']=-1;
  }
  unset($pitem_new);

  foreach ($rooms_old as $key_old => &$pitem_old) {
    $found=-1;
    foreach ($rooms_new as $key_new =>&$pitem_new) {
      if ($pitem_old['id_hotel_reservation_room'] == $pitem_new['id_hotel_reservation_room']) {
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

  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/compare.txt',print_r($rooms_old,true).' '.print_r($rooms_new,true));

  
  
  
  $sxolio_eidi_log='';
  foreach ($rooms_old as $pitem_old) {
    if ($pitem_old['del']) {
      $sxolio_eidi_log.=gks_lang('Αφαιρέθηκε το δωμάτιο').': <b>'.$pitem_old['room_descr'].'</b><br>';
    }
  }
  
  foreach ($rooms_new as $key_new =>&$pitem_new) {
    if ($pitem_new['del'] == false and $pitem_new['k'] == -1) {
      $sxolio_eidi_log.=gks_lang('Προστέθηκε το δωμάτιο').': <b>'.$pitem_new['room_descr'].'</b> '.
      gks_lang('Αξία').': <b>'.number_format($pitem_new['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND).'</b> '.
      ($pitem_new['product_price_ekptosi_pososto'] == 0 ? '' : ' '.gks_lang('Έκπτωση').': <b>'.myNumberFormatNo0Local($pitem_new['product_price_ekptosi_pososto']).'%</b>').
      '<br>';
    }
  }
  
   // $sxolio_log.=gks_lang('Εσωτερική σημείωση για λογιστήριο').':<br><b>'.(isset($row_old['note_logistirio']) ? $row_old['note_logistirio'] : '').'</b> [[-r]] '.
   // '<b>'.(isset($row_new['note_logistirio']) ? $row_new['note_logistirio'] : '').'</b>'.'<br>';
  
  $pn=$rooms_new;
  foreach ($rooms_old as $p) {
    if ($p['k']>=0) {
      $item_descr_change='';
      if ($p['room_descr'] != $pn[$p['k']]['room_descr']) 
        $item_descr_change.='<b>'.$p['room_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['room_descr'].'</b>'.'<br>';
      
      $item_txt='';
      
      if ($p['rnum_adults'] != $pn[$p['k']]['rnum_adults']) 
        $item_txt.='&nbsp;'.gks_lang('Ενήλικες').': <b>'.$p['rnum_adults'].'</b> [[-r]] <b>'.$pn[$p['k']]['rnum_adults'].'</b>'.'<br>';
      if ($p['rnum_childs'] != $pn[$p['k']]['rnum_childs']) 
        $item_txt.='&nbsp;'.gks_lang('Παιδιά').': <b>'.$p['rnum_childs'].'</b> [[-r]] <b>'.$pn[$p['k']]['rnum_childs'].'</b>'.'<br>';
      
      //echo '<pre>';print_r($p); print_r($pn); $item_txt;die();
      
      
      $childs_ages_list_str_old=''; 
      if (trim_gks($p['rchilds_ages_list'])!='') {
        $temp=json_decode($p['rchilds_ages_list'],true);
        $temp2=array();
        foreach ($temp as $value) {
          $temp2[]=$value['age'];
        }
        $childs_ages_list_str_old=implode(',',$temp2);
      }
      $childs_ages_list_str_new='';
      if (trim_gks($p['rchilds_ages_list'])!='') {
        $temp=json_decode($pn[$p['k']]['rchilds_ages_list'],true);
        $temp2=array();
        foreach ($temp as $value) {
          $temp2[]=$value['age'];
        }
        $childs_ages_list_str_new=implode(',',$temp2);
      }
      if ($childs_ages_list_str_old != $childs_ages_list_str_new) 
        $item_txt.='&nbsp;'.gks_lang('Ηλικίες παιδιών δωματίου').': <b>'.$childs_ages_list_str_old.'</b> [[-r]] <b>'.$childs_ages_list_str_new.'</b>'.'<br>';
      
      //$item_txt.='|'.$childs_ages_list_str_old.'|'.$childs_ages_list_str_new.'|<br>';
      
      if ($p['rnum_child_kounies'] != $pn[$p['k']]['rnum_child_kounies']) 
        $item_txt.='&nbsp;'.gks_lang('Βρεφικά κρεβάτια').': <b>'.$p['rnum_child_kounies'].'</b> [[-r]] <b>'.$pn[$p['k']]['rnum_child_kounies'].'</b>'.'<br>';

      if ($p['rnum_extra_beds'] != $pn[$p['k']]['rnum_extra_beds']) 
        $item_txt.='&nbsp;'.gks_lang('Επιπλέον κρεβάτια').': <b>'.$p['rnum_extra_beds'].'</b> [[-r]] <b>'.$pn[$p['k']]['rnum_extra_beds'].'</b>'.'<br>';
      
      
      
      
      
      
      if ($p['rsxolio'] != $pn[$p['k']]['rsxolio']) 
        $item_txt.='&nbsp;'.gks_lang('Σχόλιο').': <b>'.$p['rsxolio'].'</b> [[-r]] <b>'.$pn[$p['k']]['rsxolio'].'</b>'.'<br>';
        
        
        
      if ($p['ruser_id'] != $pn[$p['k']]['ruser_id']) {
        $item_txt.='&nbsp;'.gks_lang('Πελάτης').': <b>'.
        ($p['ruser_id']==-1 ? gks_lang('Ίδιος πελάτης') : ($p['ruser_id']>0 ? gks_lang('Υπάρχον Πελάτης') : gks_lang('Άλλος πελάτης'))).
        '</b> [[-r]] <b>'.
        ($pn[$p['k']]['ruser_id']==-1 ? gks_lang('Ίδιος πελάτης') : ($pn[$p['k']]['ruser_id']>0 ? gks_lang('Υπάρχον Πελάτης') : gks_lang('Άλλος πελάτης'))).
        '</b>'.'<br>';
      }
      //$item_txt.='|'.$p['ruser_id'].'|'.$pn[$p['k']]['ruser_id'].'|';

      if ((isset($p['gks_nickname']) and isset($p['gks_nickname']) == false) or 
          (isset($p['gks_nickname']) == false and isset($p['gks_nickname'])) or 
          $p['gks_nickname'] != $pn[$p['k']]['gks_nickname']) 
        $item_txt.='&nbsp;'.gks_lang('Πελάτης').': <b>'.(isset($p['gks_nickname']) ? $p['gks_nickname'] : '').'</b> [[-r]] '.
        '<b>'.(isset($pn[$p['k']]['gks_nickname']) ? $pn[$p['k']]['gks_nickname'] : '').'</b>'.'<br>';
      
      if (trim_gks($p['ruser_first_name']) != trim_gks($pn[$p['k']]['ruser_first_name'])) 
        $item_txt.='&nbsp;'.gks_lang('Όνομα').': <b>'.$p['ruser_first_name'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_first_name'].'</b>'.'<br>';
      
      if (trim_gks($p['ruser_last_name']) != trim_gks($pn[$p['k']]['ruser_last_name'])) 
        $item_txt.='&nbsp;'.gks_lang('Επώνυμο').': <b>'.$p['ruser_last_name'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_last_name'].'</b>'.'<br>';
    
      if (trim_gks($p['ruser_email']) != trim_gks($pn[$p['k']]['ruser_email'])) 
        $item_txt.='&nbsp;'.gks_lang('email').': <b>'.$p['ruser_email'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_email'].'</b>'.'<br>';
    
      if (trim_gks($p['ruser_mobile']) != trim_gks($pn[$p['k']]['ruser_mobile'])) 
        $item_txt.='&nbsp;'.gks_lang('Τηλέφωνο').': <b>'.$p['ruser_mobile'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_mobile'].'</b>'.'<br>';
    
      if (trim_gks($p['lang_name']) != trim_gks($pn[$p['k']]['lang_name'])) 
        $item_txt.='&nbsp;'.gks_lang('Γλώσσα').': <b>'.$p['lang_name'].'</b> [[-r]] <b>'.$pn[$p['k']]['lang_name'].'</b>'.'<br>';
    
    
      if (trim_gks($p['ruser_ma_odos']) != trim_gks($pn[$p['k']]['ruser_ma_odos'])) 
        $item_txt.='&nbsp;'.gks_lang('Οδός').': <b>'.$p['ruser_ma_odos'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_ma_odos'].'</b>'.'<br>';
    
      if (trim_gks($p['ruser_ma_arithmos']) != trim_gks($pn[$p['k']]['ruser_ma_arithmos'])) 
        $item_txt.='&nbsp;'.gks_lang('Αριθμός').': <b>'.$p['ruser_ma_arithmos'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_ma_arithmos'].'</b>'.'<br>';
    
    
    
      if (trim_gks($p['ruser_ma_orofos']) != trim_gks($pn[$p['k']]['ruser_ma_orofos'])) 
        $item_txt.='&nbsp;'.gks_lang('Όροφος').': <b>'.$p['ruser_ma_orofos'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_ma_orofos'].'</b>'.'<br>';
    
      if (trim_gks($p['ruser_ma_perioxi']) != trim_gks($pn[$p['k']]['ruser_ma_perioxi'])) 
        $item_txt.='&nbsp;'.gks_lang('Περιοχή').': <b>'.$p['ruser_ma_perioxi'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_ma_perioxi'].'</b>'.'<br>';
    
      if (trim_gks($p['ruser_ma_poli']) != trim_gks($pn[$p['k']]['ruser_ma_poli'])) 
        $item_txt.='&nbsp;'.gks_lang('Πόλη').': <b>'.$p['ruser_ma_poli'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_ma_poli'].'</b>'.'<br>';
    
      if (trim_gks($p['ruser_ma_tk']) != trim_gks($pn[$p['k']]['ruser_ma_tk'])) 
        $item_txt.='&nbsp;'.gks_lang('TK').': <b>'.$p['ruser_ma_tk'].'</b> [[-r]] <b>'.$pn[$p['k']]['ruser_ma_tk'].'</b>'.'<br>';
    
      if (trim_gks($p['country_name']) != trim_gks($pn[$p['k']]['country_name'])) 
        $item_txt.='&nbsp;'.gks_lang('Χώρα').': <b>'.$p['country_name'].'</b> [[-r]] <b>'.$pn[$p['k']]['country_name'].'</b>'.'<br>';
    
      if (trim_gks($p['nomos_descr']) != trim_gks($pn[$p['k']]['nomos_descr'])) 
        $item_txt.='&nbsp;'.gks_lang('Νομός').': <b>'.$p['nomos_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['nomos_descr'].'</b>'.'<br>';
    
      
      if (trim_gks($p['fiscal_position_descr']) != trim_gks($pn[$p['k']]['fiscal_position_descr'])) 
        $item_txt.='&nbsp;'.gks_lang('Φορολογική Θέση').': <b>'.$p['fiscal_position_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['fiscal_position_descr'].'</b>'.'<br>';
    
      if (trim_gks($p['pricelist_descr']) != trim_gks($pn[$p['k']]['pricelist_descr'])) 
        $item_txt.='&nbsp;'.gks_lang('Τιμοκατάλογος').': <b>'.$p['pricelist_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['pricelist_descr'].'</b>'.'<br>';
        
        
        


        
        
      if ($p['product_comments'] != $pn[$p['k']]['product_comments']) 
        $item_txt.='&nbsp;'.gks_lang('Παρατηρήσεις').': <b>'.$p['product_comments'].'</b> [[-r]] <b>'.$pn[$p['k']]['product_comments'].'</b> ';
      
      if ($p['product_price_ekptosi_pososto'] != $pn[$p['k']]['product_price_ekptosi_pososto']) 
        $item_txt.='&nbsp;'.gks_lang('Έκπτωση').': <b>'.myNumberFormatNo0Local($p['product_price_ekptosi_pososto']).'%</b> [[-r]] <b>'.myNumberFormatNo0Local($pn[$p['k']]['product_price_ekptosi_pososto']).'%</b> ';
      if ($p['product_price_final_all_net'] != $pn[$p['k']]['product_price_final_all_net']) 
        $item_txt.='&nbsp;'.gks_lang('Τιμή').': <b>'.myCurrencyFormat($p['product_price_final_all_net'], false).'</b> [[-r]] <b>'.myCurrencyFormat($pn[$p['k']]['product_price_final_all_net'], false).'</b> ';
      
      if ($p['fpa_base_descr']!=$pn[$p['k']]['fpa_base_descr'])
        $item_txt.='&nbsp;'.gks_lang('ΦΠΑ').': <b>'.$p['fpa_base_descr'].'</b> [[-r]] <b>'.$pn[$p['k']]['fpa_base_descr'].'</b> ';
      
        
      if ($item_txt != '') {
        $item_txt=trim_gks($item_txt);
        if ($item_descr_change!='') $item_txt=$item_descr_change.': '.$item_txt;
        else $item_txt=$p['room_descr'].': '.$item_txt;
        
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
  

  if (trim_gks($row_old['kostos_pliromis']) != trim_gks($row_new['kostos_pliromis'])) 
    $sxolio_log.=gks_lang('Κόστος πληρωμής').': <b>'.myCurrencyFormat($row_old['kostos_pliromis']).'</b> [[-r]] <b>'.myCurrencyFormat($row_new['kostos_pliromis']).'</b>'.'<br>';


  if ($row_old['gks_price_total'] != $row_new['gks_price_total']) 
    $sxolio_log.=gks_lang('Σύνολο').': <b>'.myCurrencyFormat($row_old['gks_price_total']).'</b> [[-r]] '.'<b>'.myCurrencyFormat($row_new['gks_price_total']).'</b>'.'<br>';
  

  $pliroteo_old=$row_old['gks_price_total'] + $row_old['kostos_apostolis'] + $row_old['kostos_pliromis'];
  $pliroteo_new=$row_new['gks_price_total'] + $row_new['kostos_apostolis'] + $row_new['kostos_pliromis'];
  if ($pliroteo_old != $pliroteo_new) 
    $sxolio_log.=gks_lang('Πληρωτέο').': <b>'.myCurrencyFormat($pliroteo_old).'</b> [[-r]] '.'<b>'.myCurrencyFormat($pliroteo_new).'</b>'.'<br>';





 
  if (trim_gks($row_old['payment_acquirer_name']) != trim_gks($row_new['payment_acquirer_name'])) 
    $sxolio_log.=gks_lang('Τρόπος πληρωμής').': <b>'.$row_old['payment_acquirer_name'].'</b> [[-r]] <b>'.$row_new['payment_acquirer_name'].'</b>'.'<br>';

  if (trim_gks($row_old['user_notes'])!=trim_gks($row_new['user_notes']))
    $sxolio_log.=gks_lang('Σχόλιο από πελάτη').':<br><b>'.(isset($row_old['user_notes']) ? $row_old['user_notes'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['user_notes']) ? $row_new['user_notes'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['sxolio'])!=trim_gks($row_new['sxolio']))
    $sxolio_log.=gks_lang('Σχόλιο κράτησης').':<br><b>'.(isset($row_old['sxolio']) ? $row_old['sxolio'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['sxolio']) ? $row_new['sxolio'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['note_logistirio'])!=trim_gks($row_new['note_logistirio']))
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
 

  $gks_custom_prepare=gks_custom_table_item_prepare('gks_hotel_reservation',['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;




  
  
  if ($sxolio_log == '') $sxolio_log=gks_lang('Ενημέρωση').'<br>';
  //print '<pre>';
  //print_r($rooms_old);
  //die();  
  
  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into gks_hotel_reservation_log (hotel_reservation_id, add_date,user_id,sxolio) values (
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
