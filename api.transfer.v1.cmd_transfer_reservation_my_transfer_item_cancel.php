<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_transfer_cmd_transfer_reservation_my_transfer_item_cancel($id_transfer,$row_transfer,$input_data) {
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

  //$return['message'] = '<pre>'.print_r($input_data,true).'</pre>'; return $return;

  
  if (isset($input_data['get_data'])==false or isset($input_data['get_data']['my_hash1'])==false or isset($input_data['get_data']['my_number'])==false) {
    $return['message'] = 'Not all data send'; return $return;}
   
  $guid=$input_data['get_data']['my_hash3'];
  $hash1=$input_data['get_data']['my_hash1'];
  $hash2=$input_data['get_data']['my_hash2'];
  $hash3=$input_data['get_data']['my_hash3'];
  $my_prefix=$input_data['get_data']['my_prefix'];
  $my_number=$input_data['get_data']['my_number'];
  $my_email=$input_data['get_data']['my_email'];
  
  
  
  $hash2_calc=md5($hash1.$hash1.$hash3.$hash1.$hash1);
  if ($hash2_calc!=$hash2 or $hash3!=$guid) {
    $return['message'] = 'hash error'; return $return;}
  
  
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
  and transfer_reservation_guid='".$db_link->escape_string($guid)."'
  order by id_transfer_reservation desc limit 1";
  
  $my_recs=array();
  $transfer_booking_number_found=$transfer_booking_number;
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
    $transfer_booking_number_found=$transfer_booking_number;
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
//    $transfer_booking_number_found=$transfer_booking_number;
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
    $return['message'] = 'Transfer not found'; return $return;}
    
  $mytr=$my_recs[0];
  if ($mytr['transfer_reservation_guid']!=$guid) {
    $return['message'] = 'error hash'; return $return;}

  
  if (in_array($mytr['transfer_reservation_status'],array('070wait_payment','080confirm'))==false) {
    $return['message'] = 'Transfer can not canceled. The state is not Wait Payment or Confirm'; return $return;}
    
  
  $cancel_hash=//md5(rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999)).
               md5(rand(1000,9999).rand(1000,9999).$guid);
               //md5(rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999)).
               //md5(rand(1000,9999).$guid).
               //md5(rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999));
  
  $sql="update gks_transfer_reservation set 
  cancel_hash='".$db_link->escape_string($cancel_hash)."',
  cancel_until=date_add(now(), interval 5 minute)
  where id_transfer_reservation=".$mytr['id_transfer_reservation']."
  and transfer_reservation_status in ('070wait_payment','080confirm')
  limit 1";

  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
    return $return; }
  
  $num_rows=$db_link->affected_rows;
  
  if ($num_rows!=1) {
    debug_mail(false,'error cancel num_rows',$sql);
    $return = array('success' => false, 'message' => 'Error<br>Please retry later.');
    return $return; }

  $user_id=2;
  $sql="select ID from ".GKS_WP_TABLE_PREFIX."users where user_email like '".$db_link->escape_string($my_email)."'";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
    return $return; }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $user_id=$row['ID'];
  }
  
  $sxolio=gks_lang('Βήμα 1 για <span class="transfer_reservation_status_040cancelled">ακύρωση</span> από').' '.$my_email;
  $sql="insert into gks_transfer_reservation_log (
  transfer_reservation_id,add_date,user_id,sxolio
  ) values (
  ".$mytr['id_transfer_reservation'].",
  now(),".$user_id.",'".$db_link->escape_string($sxolio)."'
  )";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'SQL Error<br>Please retry later.');
    return $return; }

  
  $return['success']=true; 
  $return['message']='OK';
  $return['guid']=$guid;
  $return['cancel_ref']=$transfer_booking_number_found;
  $return['cancel_email']=$my_email;
  $return['cancel_hash']=$cancel_hash;
  $return['cancel_until']='5 minutes';
   
 
  return $return;
    



  
}


