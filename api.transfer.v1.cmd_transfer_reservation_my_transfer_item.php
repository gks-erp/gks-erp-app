<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_transfer_cmd_transfer_reservation_my_transfer_item($id_transfer,$row_transfer,$input_data) {
  global $db_link;
  global $gkIP;
  global $gks_cache_version;
  global $_gks_session;
  global $_gks_id_session;
  global $gks_user_settings;
  
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW;
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;

  
  

  $from='website';if (isset($input_data['from'])) $from=$input_data['from'];
  
  
  $gks_erp_cookie_id='';
  if ($from=='website') {
    if(isset($input_data['gks_erp_cookie_id'])) {
      $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
    }
    $transfer_title=$row_transfer['transfer_title'];
    $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
    gks_erp_cookie_start($gks_erp_cookie_id);
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  }
  
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  $db_lang='';$db_lang2='';if ($_gks_session['gks']['ui_lang']=='en-US') {$db_lang='_en_US';$db_lang2='_en';}
  
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  $return=array('success' => false, 'message' => base64_encode('generic gks_api_transfer_cmd_transfer_reservation_search error'),'data' => false, 'debug'=>'');
  $error_html=[];

  
  if (isset($input_data['guid'])==false or isset($input_data['mycookie_vals'])==false or count($input_data['mycookie_vals'])!=6) {
    $return['message'] = base64_encode('Not all data send'); return $return;}
    
  $guid=$input_data['guid'];
  $hash1=$input_data['mycookie_vals'][0];
  $hash2=$input_data['mycookie_vals'][1];
  $hash3=$input_data['mycookie_vals'][2];
  $my_prefix=$input_data['mycookie_vals'][3];
  $my_number=$input_data['mycookie_vals'][4];
  $my_email=$input_data['mycookie_vals'][5];
  
  $hash2_calc=md5($hash1.$hash1.$hash3.$hash1.$hash1);
  if ($hash2_calc!=$hash2 or $hash3!=$guid) {
    $return['message'] = base64_encode('hash error'); return $return;}
  
  $return['input_data']=$input_data;

  $transfer_booking_number=$my_prefix.$my_number;
  $sql_templete="select gks_transfer_reservation.*, ".GKS_WP_TABLE_PREFIX."users.user_email,display_name,gks_nickname,
  gks_poi_from.poi_type_id as poi_type_id_from, poi_descr_en_US_from,
  gks_poi_to.poi_type_id as poi_type_id_to, poi_descr_en_US_to,
  gks_country.country_initials
  FROM (((((gks_transfer_reservation 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_transfer_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_poi AS gks_poi_from ON gks_transfer_reservation.poi_id_from = gks_poi_from.id_poi) 
  LEFT JOIN (
    SELECT poi_id, poi_descr as poi_descr_en_US_from FROM gks_poi_lang WHERE lang_code='en-US'
  ) AS gks_poi_en_US_from ON gks_poi_from.id_poi = gks_poi_en_US_from.poi_id)
  LEFT JOIN gks_poi AS gks_poi_to ON gks_transfer_reservation.poi_id_to = gks_poi_to.id_poi)
  LEFT JOIN (
    SELECT poi_id, poi_descr as poi_descr_en_US_to FROM gks_poi_lang WHERE lang_code='en-US'
  ) AS gks_poi_en_US_to ON gks_poi_to.id_poi = gks_poi_en_US_to.poi_id)
  LEFT JOIN gks_country ON gks_transfer_reservation.ma_country_id = gks_country.id_country  
  
  where transfer_booking_number like '[[transfer_booking_number]]'
  and transfer_reservation_status in ('040cancelled','050rejected','070wait_payment','080confirm')
  and transfer_start >= date_sub(now(), interval 12 hour)
  and (
    gks_transfer_reservation.user_email like '".$db_link->escape_string($my_email)."' or 
    gks_transfer_reservation.other_email like '".$db_link->escape_string($my_email)."' or
    ".GKS_WP_TABLE_PREFIX."users.user_email like '".$db_link->escape_string($my_email)."'
  )
  order by id_transfer_reservation desc limit 1";
  
  $my_recs=array();

  $sql=str_replace('[[transfer_booking_number]]',$db_link->escape_string($transfer_booking_number),$sql_templete);
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
    return $return; }
  while ($row = $result->fetch_assoc()) {  
    $my_recs[]=$row;
  }
  
  if (count($my_recs)==0) {
    $sql=str_replace('[[transfer_booking_number]]',$db_link->escape_string($transfer_booking_number.'-1'),$sql_templete);
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
      return $return; }
    while ($row = $result->fetch_assoc()) {  
      $my_recs[]=$row;
    }
  }
//  if (count($my_recs)==0) {
//    $sql=str_replace('[[transfer_booking_number]]',$db_link->escape_string($transfer_booking_number.'-2'),$sql_templete);
//    $result = $db_link->query($sql);
//    if (!$result) {
//      debug_mail(false,'error sql',$sql);
//      $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
//      return $return; }
//    while ($row = $result->fetch_assoc()) {  
//      $my_recs[]=$row;
//    }
//  }
  
  if (count($my_recs)==0) {
    debug_mail(false,'Transfer not found',$sql);
    $return['message'] = base64_encode('Transfer not found'); return $return;}
    
  $mytr=$my_recs[0];
  if ($mytr['transfer_reservation_guid']!=$guid) {
    $return['message'] = base64_encode('error hash'); return $return;}
  
  $return['transfer_properties']=array(
    'transfer_empty_cart_woo' => intval($row_transfer['transfer_empty_cart_woo']),
    'transfer_reservation_can_select_oxima' => intval($row_transfer['transfer_reservation_can_select_oxima']),
    'transfer_reservation_days_future' => intval($row_transfer['transfer_reservation_days_future']),
    'transfer_reservation_min_hours_to_book' => intval($row_transfer['transfer_reservation_min_hours_to_book']),
    'transfer_reservation_min_hours_to_book_group_multi' => intval($row_transfer['transfer_reservation_min_hours_to_book_group_multi']),
    'transfer_reservation_group_multi_date_range' => unserialize($row_transfer['transfer_reservation_group_multi_date_range']),
    'transfer_use_checkout_system' => trim_gks($row_transfer['transfer_use_checkout_system']),
    'transfer_sms_text_message_enable' => intval($row_transfer['transfer_sms_text_message_enable']),
    'transfer_sms_text_message_price' => floatval($row_transfer['transfer_sms_text_message_price']),
    'transfer_cancellation_protection_enable' => intval($row_transfer['transfer_cancellation_protection_enable']),
    'transfer_cancellation_protection_price' => floatval($row_transfer['transfer_cancellation_protection_price']),
    'transfer_terms_and_policy_frontend' => intval($row_transfer['transfer_terms_and_policy_frontend']),
    'transfer_multi_cars' => intval($row_transfer['transfer_multi_cars']),
    
    'transfer_outward_from_airplane_message' => trim_gks($row_transfer['transfer_outward_from_airplane_message']),
    'transfer_outward_from_train_message' => trim_gks($row_transfer['transfer_outward_from_train_message']),
    'transfer_outward_from_cruise_message' => trim_gks($row_transfer['transfer_outward_from_cruise_message']),
    'transfer_outward_from_location_message' => trim_gks($row_transfer['transfer_outward_from_location_message']),
    
    
    'transfer_outward_from_pick_up_point' => trim_gks($row_transfer['transfer_outward_from_pick_up_point']),
    'transfer_outward_from_pick_up_time' => trim_gks($row_transfer['transfer_outward_from_pick_up_time']),
    'transfer_outward_from_pick_up_time_start_minutes_airplane' => intval($row_transfer['transfer_outward_from_pick_up_time_start_minutes_airplane']),
    'transfer_outward_from_pick_up_time_start_minutes_train' => intval($row_transfer['transfer_outward_from_pick_up_time_start_minutes_train']),
    'transfer_outward_from_pick_up_time_start_minutes_cruise' => intval($row_transfer['transfer_outward_from_pick_up_time_start_minutes_cruise']),
    'transfer_outward_from_pick_up_time_text_airplane' => trim_gks($row_transfer['transfer_outward_from_pick_up_time_text_airplane']),
    'transfer_outward_from_pick_up_time_text_train' => trim_gks($row_transfer['transfer_outward_from_pick_up_time_text_train']),
    'transfer_outward_from_pick_up_time_text_cruise' => trim_gks($row_transfer['transfer_outward_from_pick_up_time_text_cruise']),
    'transfer_outward_from_pick_up_time_text_location' => trim_gks($row_transfer['transfer_outward_from_pick_up_time_text_location']),
    
    
    'transfer_outward_from_flight_arrival_time' => trim_gks($row_transfer['transfer_outward_from_flight_arrival_time']),

    'transfer_outward_to_drop_off_point' => trim_gks($row_transfer['transfer_outward_to_drop_off_point']),
    'transfer_outward_to_flight_departure_time' => trim_gks($row_transfer['transfer_outward_to_flight_departure_time']),

    'transfer_return_from_airplane_message' => trim_gks($row_transfer['transfer_return_from_airplane_message']),
    'transfer_return_from_train_message' => trim_gks($row_transfer['transfer_return_from_train_message']),
    'transfer_return_from_cruise_message' => trim_gks($row_transfer['transfer_return_from_cruise_message']),
    'transfer_return_from_location_message' => trim_gks($row_transfer['transfer_return_from_location_message']),
    
    'transfer_return_from_address_different' => trim_gks($row_transfer['transfer_return_from_address_different']),
    'transfer_return_from_pick_up_time' => trim_gks($row_transfer['transfer_return_from_pick_up_time']),
    'transfer_return_from_pick_up_time_start_minutes_airplane' => intval($row_transfer['transfer_return_from_pick_up_time_start_minutes_airplane']),
    'transfer_return_from_pick_up_time_start_minutes_train' => intval($row_transfer['transfer_return_from_pick_up_time_start_minutes_train']),
    'transfer_return_from_pick_up_time_start_minutes_cruise' => intval($row_transfer['transfer_return_from_pick_up_time_start_minutes_cruise']),
    'transfer_return_from_pick_up_time_text_airplane' => trim_gks($row_transfer['transfer_return_from_pick_up_time_text_airplane']),
    'transfer_return_from_pick_up_time_text_train' => trim_gks($row_transfer['transfer_return_from_pick_up_time_text_train']),
    'transfer_return_from_pick_up_time_text_cruise' => trim_gks($row_transfer['transfer_return_from_pick_up_time_text_cruise']),
    'transfer_return_from_pick_up_time_text_location' => trim_gks($row_transfer['transfer_return_from_pick_up_time_text_location']),
    
    'transfer_return_from_flight_arrival_time' => trim_gks($row_transfer['transfer_return_from_flight_arrival_time']),

    'transfer_return_to_flight_departure_time' => trim_gks($row_transfer['transfer_return_to_flight_departure_time']),
    'transfer_return_to_address_different' => trim_gks($row_transfer['transfer_return_to_address_different']),
    
    
    
  );

  $mytr['oximata']=array();
  $sql="select gks_transfer_reservation_oximata.*,
  transfer_oxima_type_max_suitcases,
  transfer_oxima_type_max_booster,
  transfer_oxima_type_max_kareklakia,
  transfer_oxima_type_max_amajidia,
  transfer_oxima_type_max_golfbag,
  transfer_oxima_type_max_skis,
  transfer_oxima_type_max_5minstop,
  transfer_oxima_type_service_door_to_door,
  transfer_oxima_type_service_porter,
  transfer_oxima_type_service_treat_yourself,
  transfer_oxima_type_service_free_wifi,
  transfer_oxima_type_service_bottled_water,
  transfer_oxima_type_descr_en_US,
  transfer_oxima_type_photo,
  transfer_oxima_type_max_epivates,
  transfer_oxima_type_site_text_en_US
    
  from (gks_transfer_reservation_oximata 
  LEFT JOIN gks_transfer_oxima_type ON gks_transfer_reservation_oximata.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type)
  LEFT JOIN (
    SELECT transfer_oxima_type_id, 
    transfer_oxima_type_descr as transfer_oxima_type_descr_en_US, 
    transfer_oxima_type_site_text as transfer_oxima_type_site_text_en_US 
    FROM gks_transfer_oxima_type_lang WHERE lang_code='en-US'
  ) AS gks_transfer_oxima_type_en_US ON gks_transfer_oxima_type.id_transfer_oxima_type = gks_transfer_oxima_type_en_US.transfer_oxima_type_id
  
  where transfer_reservation_id=".$mytr['id_transfer_reservation']." order by oximata_aa";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
    return $return; }
  while ($row = $result->fetch_assoc()) {  
    $mytr['oximata'][]=$row;
  }
  
  
  $return['my_outward_transfer']=$mytr;
  $return['my_return_transfer']=array('id_transfer_reservation' => 0);

      
  $sql="select gks_transfer_reservation.*, ".GKS_WP_TABLE_PREFIX."users.user_email,display_name,gks_nickname,
  gks_poi_from.poi_type_id as poi_type_id_from, poi_descr_en_US_from,
  gks_poi_to.poi_type_id as poi_type_id_to, poi_descr_en_US_to,
  gks_country.country_initials  
  FROM (((((gks_transfer_reservation 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_transfer_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_poi AS gks_poi_from ON gks_transfer_reservation.poi_id_from = gks_poi_from.id_poi) 
  LEFT JOIN (
    SELECT poi_id, poi_descr as poi_descr_en_US_from FROM gks_poi_lang WHERE lang_code='en-US'
  ) AS gks_poi_en_US_from ON gks_poi_from.id_poi = gks_poi_en_US_from.poi_id)
  LEFT JOIN gks_poi AS gks_poi_to ON gks_transfer_reservation.poi_id_to = gks_poi_to.id_poi)
  LEFT JOIN (
    SELECT poi_id, poi_descr as poi_descr_en_US_to FROM gks_poi_lang WHERE lang_code='en-US'
  ) AS gks_poi_en_US_to ON gks_poi_to.id_poi = gks_poi_en_US_to.poi_id)
  LEFT JOIN gks_country ON gks_transfer_reservation.ma_country_id = gks_country.id_country
  
  
  where transfer_reservation_status in ('040cancelled','050rejected','070wait_payment','080confirm')
  and transfer_start >= date_sub(now(), interval 12 hour)
  and is_return_transfer_for_id=".$mytr['id_transfer_reservation']."
  order by id_transfer_reservation desc limit 1";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
    return $return; }
  while ($row = $result->fetch_assoc()) {  
    $return['my_return_transfer']=$row;
    $return['my_return_transfer']['oximata']=array();
  }
  
  if ($return['my_return_transfer']['id_transfer_reservation']>0) {
    $sql="select gks_transfer_reservation_oximata.*,
    transfer_oxima_type_max_suitcases,
    transfer_oxima_type_max_booster,
    transfer_oxima_type_max_kareklakia,
    transfer_oxima_type_max_amajidia,
    transfer_oxima_type_max_golfbag,
    transfer_oxima_type_max_skis,
    transfer_oxima_type_max_5minstop,
    transfer_oxima_type_service_door_to_door,
    transfer_oxima_type_service_porter,
    transfer_oxima_type_service_treat_yourself,
    transfer_oxima_type_service_free_wifi,
    transfer_oxima_type_service_bottled_water,
    transfer_oxima_type_descr_en_US,
    transfer_oxima_type_photo,
    transfer_oxima_type_max_epivates,
    transfer_oxima_type_site_text_en_US
    
    from (gks_transfer_reservation_oximata 
    LEFT JOIN gks_transfer_oxima_type ON gks_transfer_reservation_oximata.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type)
    LEFT JOIN (
      SELECT transfer_oxima_type_id, 
      transfer_oxima_type_descr as transfer_oxima_type_descr_en_US, 
      transfer_oxima_type_site_text as transfer_oxima_type_site_text_en_US 
      FROM gks_transfer_oxima_type_lang WHERE lang_code='en-US'
    ) AS gks_transfer_oxima_type_en_US ON gks_transfer_oxima_type.id_transfer_oxima_type = gks_transfer_oxima_type_en_US.transfer_oxima_type_id
    where transfer_reservation_id=".$return['my_return_transfer']['id_transfer_reservation']." order by oximata_aa";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
      return $return; }
    while ($row = $result->fetch_assoc()) {  
      $return['my_return_transfer']['oximata'][]=$row;
    } 
  }
  

  $return['success']=true; 
 
  return $return;
  
  $return['message']=base64_encode('<pre>'.print_r($mytr,true).'</pre>');return $return;


  
}


