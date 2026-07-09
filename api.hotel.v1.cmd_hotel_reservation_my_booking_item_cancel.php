<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_hotel_cmd_hotel_reservation_my_booking_item_cancel($id_hotel,$row_hotel,$input_data) {
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
    $hotel_title=$row_hotel['hotel_title'];
    $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
    gks_erp_cookie_start($gks_erp_cookie_id);
    //return '<pre>'.print_r($_gks_session,true).'</pre>';
    //$return=array('success' => false, 'message' => base64_encode($_gks_session['gks']['ui_lang'].' σσσσσσσ '.print_r($_gks_session,true)),'data' => false, 'debug'=>'');
    //return $return;
  }
  
  

  
  if (isset($input_data['get_data']['my_lang']) and trim_gks($input_data['get_data']['my_lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['get_data']['my_lang']);
  }
  $db_lang='';$db_lang2='';if ($_gks_session['gks']['ui_lang']=='en-US') {$db_lang='_en_US';$db_lang2='_en';}




  //$return=array('success' => false, 'message' => base64_encode($_gks_session['gks']['ui_lang'].' σσ'),'data' => false, 'debug'=>'');
  //return $return;

  
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  $return=array('success' => false, 'message' => base64_encode($_gks_session['gks']['ui_lang'].' '.gks_lang('Γενικό σφάλμα')),'data' => false, 'debug'=>'');
  //return $return;
  
  $error_html=[];

  //$return['message'] = '<pre>'.print_r($input_data,true).'</pre>'; return $return;

  
  if (isset($input_data['get_data'])==false or isset($input_data['get_data']['my_hash1'])==false or isset($input_data['get_data']['my_number'])==false) {
    $return['message'] = base64_encode(gks_lang('Δεν έχουν ορισθεί όλα τα δεδομένα')); return $return;}
   
  $guid=$input_data['get_data']['my_hash3'];
  $hash1=$input_data['get_data']['my_hash1'];
  $hash2=$input_data['get_data']['my_hash2'];
  $hash3=$input_data['get_data']['my_hash3'];
  $my_prefix=$input_data['get_data']['my_prefix'];
  $my_number=$input_data['get_data']['my_number'];
  $my_email=$input_data['get_data']['my_email'];
  
  
  
  $hash2_calc=md5($hash1.$hash1.$hash3.$hash1.$hash1);
  if ($hash2_calc!=$hash2 or $hash3!=$guid) {
    $return['message'] = base64_encode(gks_lang('Σφάλμα κατακερματισμού')); return $return;}
  
  
  $return['input_data']=$input_data;

  $hotel_booking_number=$my_prefix.$my_number;
  $sql_templete="select gks_hotel_reservation.*, ".GKS_WP_TABLE_PREFIX."users.user_email,display_name,gks_nickname
  FROM gks_hotel_reservation 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  where hotel_booking_number like '[[hotel_booking_number]]'
  and reservation_status in ('040cancelled','050rejected','070wait_payment','080confirm')
  and check_in >= date_sub(now(), interval 12 hour)
  and (
    gks_hotel_reservation.user_email like '".$db_link->escape_string($my_email)."' or 
    gks_hotel_reservation.other_email like '".$db_link->escape_string($my_email)."' or
    ".GKS_WP_TABLE_PREFIX."users.user_email like '".$db_link->escape_string($my_email)."'
  )
  order by id_hotel_reservation desc"; // limit 1
  
  $my_recs=array();
  $hotel_booking_number_found=$hotel_booking_number;
  $sql=str_replace('[[hotel_booking_number]]',$db_link->escape_string($hotel_booking_number),$sql_templete);
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
  while ($row = $result->fetch_assoc()) {  
    $my_recs[]=$row;
  }
  
  if (count($my_recs)==0) {
    $hotel_booking_number_found=$hotel_booking_number;
    $sql=str_replace('[[hotel_booking_number]]',$db_link->escape_string($hotel_booking_number.'-%'),$sql_templete);
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }
    while ($row = $result->fetch_assoc()) {  
      $my_recs[]=$row;
    }
  }
//  if (count($my_recs)==0) {
//    $hotel_booking_number_found=$hotel_booking_number;
//    $sql=str_replace('[[hotel_booking_number]]',$db_link->escape_string($hotel_booking_number.'-2'),$sql_templete);
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
    debug_mail(false,gks_lang('Δεν βρέθηκε η κράτηση'),$sql);
    $return['message'] = base64_encode(gks_lang('Δεν βρέθηκε η κράτηση')); return $return;}
    
  $is_hash_ok=false;
  foreach ($my_recs as $value) {
    if ($value['reservation_guid']==$guid) {
      $is_hash_ok=true; break;
    }
  } 
  
  if ($is_hash_ok==false) {
    $return['message'] = base64_encode(gks_lang('Σφάλμα κατακερματισμού')); return $return;}

  $can_cancel=true;
  foreach ($my_recs as $mytr) {
    if (in_array($mytr['reservation_status'],array('070wait_payment','080confirm'))==false) {
      $can_cancel=false; break;
    }
  } 

  if ($can_cancel==false) {
    $return['message'] = base64_encode(gks_lang('Η κράτηση δεν μπορεί να ακυρωθεί. Η κατάσταση δεν είναι Αναμονή Πληρωμής ή Επιβεβαιωμένη')); return $return;}

  //$return=array('success' => false, 'message' => base64_encode('gggggggggg'),'data' => false, 'debug'=>''); return $return;
  //$return=array('success' => false, 'message' => base64_encode('gggggggggg '.print_r($my_recs,true)),'data' => false, 'debug'=>''); return $return;


  
  
    
  
  $cancel_hash=//md5(rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999)).
               md5(rand(1000,9999).rand(1000,9999).$guid);
               //md5(rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999)).
               //md5(rand(1000,9999).$guid).
               //md5(rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999));
  
  foreach ($my_recs as $mytr) {
    $sql="update gks_hotel_reservation set 
    cancel_hash='".$db_link->escape_string($cancel_hash)."',
    cancel_until=date_add(now(), interval 5 minute)
    where id_hotel_reservation=".$mytr['id_hotel_reservation']."
    and reservation_status in ('070wait_payment','080confirm')
    limit 1";
  
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }
    
    $num_rows=$db_link->affected_rows;
    
    if ($num_rows!=1) {
      debug_mail(false,'error cancel num_rows',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }

  }
  
  $user_id=2;
  $sql="select ID from ".GKS_WP_TABLE_PREFIX."users where user_email like '".$db_link->escape_string($my_email)."'";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $user_id=$row['ID'];
  }
  
  $sxolio=gks_lang('Βήμα 1 για <span class="reservation_status_040cancelled">ακύρωση</span> από').' '.$my_email;
  foreach ($my_recs as $mytr) {
    $sql="insert into gks_hotel_reservation_log (
    hotel_reservation_id,add_date,user_id,sxolio
    ) values (
    ".$mytr['id_hotel_reservation'].",
    now(),".$user_id.",'".$db_link->escape_string($sxolio)."'
    )";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }
  }
  
  $return['success']=true; 
  $return['message']='OK';
  $return['guid']=$guid;
  $return['cancel_ref']=$hotel_booking_number_found;
  $return['cancel_email']=$my_email;
  $return['cancel_hash']=$cancel_hash;
  $return['cancel_until']=gks_lang('5 λεπτά');
   
  $return['subject']=gks_lang('Ακύρωση κράτησης #%s');
  $return['subject']=str_replace('%s', $hotel_booking_number_found, $return['subject']);
  
  $return['cancel_text']=gks_lang('Ένα email έχει αποσταλεί στο %s1 με το URL ακύρωσης.<br>Κάντε κλικ σε αυτό για ολοκληρώσετε την ακύρωση.<br>Το URL θα είναι έγκυρο για %s2.<br>Ελέγξτε τα email σας και τον φάκελο ανεπιθύμητης αλληλογραφίας');
  $return['cancel_text']=str_replace('%s1', $my_email, $return['cancel_text']);
  $return['cancel_text']=str_replace('%s2', $return['cancel_until'], $return['cancel_text']);
  
  $return['mail_text']='<div>'. gks_lang('Κάντε κλικ στον παρακάτω σύνδεσμο για να ακυρώσετε την κράτηση').':<br>'.
  '<a href="%s">%s</a><br>'.
  '</div>';
  
  return $return;
    



  
}


