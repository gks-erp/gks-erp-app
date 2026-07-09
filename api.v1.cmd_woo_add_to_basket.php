<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_cmd_woo_add_to_basket($id_hotel,$row_hotel,$input_data) {
  global $db_link;
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

  $gks_erp_cookie_id='';
  if(isset($input_data['gks_erp_cookie_id'])) {
    $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
  }
  if (isset($input_data['ajax_gks_erp_cookie_id']) and trim_gks($input_data['ajax_gks_erp_cookie_id'])!='') {
    $gks_erp_cookie_id = trim_gks($input_data['ajax_gks_erp_cookie_id']);
  }
  
  $hotel_title=$row_hotel['hotel_title'];
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  gks_erp_cookie_start($gks_erp_cookie_id);
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  if (isset($input_data['post']['ui_lang']) and trim_gks($input_data['post']['ui_lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['post']['ui_lang']);
  }
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  
  gks_load_lang();

  $url_lang='';
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    if (isset($input_data['gks_api_hotel_page_reservation_search'][$input_data['shortcode_attributes']['lang']])) {
      $url_lang=$input_data['shortcode_attributes']['lang'];
    }
  }
  if ($url_lang=='') $url_lang=array_key_first($input_data['gks_api_hotel_page_reservation_search']);
  
  //print '<pre>|'.$gks_erp_cookie_id.'|'.print_r($_gks_session['gks'],true)."\n\n".print_r($input_data['shortcode_attributes'],true);
  //print '<pre>'.$gks_erp_cookie_id.'|'.$_gks_session['gks']['ui_lang']."\n\n|| ".print_r($input_data['shortcode_attributes'],true)."\n\n || ".print_r($input_data['post'],true);
  //die();
  
  $defs = get_def_check($id_hotel);
  $hotel_params=gks_hotel_get_params($id_hotel);
  
  
  
  if ($gks_erp_cookie_id=='') {
    debug_mail(false,'gks_erp_cookie_id is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' Cookie ID.<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
    return $return;
  }
  if (isset($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'])==false) {
    debug_mail(false,'reservations is not set',print_r($_gks_session,true));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Εσωτερικό σφάλμα').' 5684322412677<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
    return $return;    
  }
  
  
  $myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
  hotel_basket_rsrv_calc($id_hotel,$myreservations, $elems, $total_sum, $total_visitors, $total_dianiktereuseis, $total_domatia, false);

//  $myrand=rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);

//  foreach ($myreservations as $rsrv_aa => &$reservation) {            
//    foreach ($reservation['selrooms'] as $roomtype_aa => &$selroom) {
//      foreach ($selroom['rooms_items'] as $room_aa => &$myroom) {
//        if (isset($myroom['myrand'])==false) $myroom['myrand']=$myrand;
//        
//      }
//      unset($myroom);
//    }
//    unset($selroom);
//  }
//  unset($reservation);
  
      
  $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'] = $myreservations;
  gks_basket_recalc($_gks_session['gks']['basket'], array(), array());
  gks_erp_cookie_save($gks_erp_cookie_id);

  $text_room_template=$hotel_params['hotel_template_woo_descr_en_US'];
  if ($text_room_template=='' or $_gks_session['gks']['ui_lang']=='el-GR') $text_room_template=$hotel_params['hotel_template_woo_descr'];
  
  $mc=gks_print_form_mc($text_room_template);

  $cart_items=array();
  
  //$return = array('success' => false, 'message' => base64_encode('<pre>myreservations: '.print_r($myreservations,true)),'cart_items' => $cart_items);
  //return $return;

  $db_lang='en_US';
  if ($_gks_session['gks']['ui_lang']=='el-GR') {
    $db_lang='';
  } else {
    $db_lang='_en_US';
  } 

  if ($db_lang=='') {
    $sql="select id_hotel_room_type as id, room_type_descr as descr FROM gks_hotel_room_type";
  } else {
    $sql="select id_hotel_room_type as id, ifnull(room_type_descr_lang,room_type_descr) as descr
    FROM gks_hotel_room_type
    LEFT JOIN (
      SELECT hotel_room_type_id, room_type_descr as room_type_descr_lang FROM gks_hotel_room_type_lang WHERE lang_code='".$db_lang."'
    ) AS gks_hotel_room_type_en_US ON gks_hotel_room_type.id_hotel_room_type = gks_hotel_room_type_en_US.hotel_room_type_id;";
    
  }
  
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return;
  }
  $room_type_descr_array=array();
  while ($row = $result->fetch_assoc()) {
    $room_type_descr_array[$row['id']]=$row['descr'];
  }
  
  if ($db_lang=='') {        
    $sql="select id_hotel_room as id, room_descr".$db_lang." as descr FROM gks_hotel_room";
  } else {
    $sql="select id_hotel_room as id, ifnull(room_descr_lang,room_descr) as descr
    FROM gks_hotel_room
    LEFT JOIN (
      SELECT hotel_room_id, room_descr as room_descr_lang FROM gks_hotel_room_lang WHERE lang_code='".$db_lang."'
    ) AS gks_hotel_room_en_US ON gks_hotel_room.id_hotel_room = gks_hotel_room_en_US.hotel_room_id;";
    
  }
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return;
  }
  $room_descr_array=array();
  while ($row = $result->fetch_assoc()) {
    $room_descr_array[$row['id']]=$row['descr'];
  }
          
  
  foreach ($myreservations as $rsrv_aa => $reservation) {            
    foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
      foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
      
        //echo '<pre>';echo 'sssssssss';die();
        //if ($myroom['myrand']==$myrand) {
          $cart_item=array(
            'temp_session_id' => $gks_erp_cookie_id,
            'id_hotel' => $id_hotel,
            'rsrv_aa' => $rsrv_aa,
            'guid' => $reservation['guid'],
            'check_in' => $reservation['check_in'],
            'check_out' => $reservation['check_out'],
            'adults' => $reservation['adults'],
            'childs' => $reservation['childs'],
            'calc_persons' => $reservation['calc_persons'],
            'rooms' => $reservation['rooms'],
            'calc_rooms' => $reservation['calc_rooms'],
            'num_days' => $reservation['num_days'],
            'room_type' => array(
              'room_type_id'=>$selroom['room_type_id'],
              'num'=>$selroom['num'],
              'roomtype' => $selroom['roomtype'],
            ),
            'room' => $myroom,
            'woo_text' => '',
          );
        
          $check_in=$reservation['check_in'].' '.$hotel_params['hotel_default_checkin'].':00';
          $check_in=strtotime($check_in);
          
          $check_out=$reservation['check_out'].' '.$hotel_params['hotel_default_checkout'].':00';
          $check_out=strtotime($check_out) + 24*60*60;
          
          $in_array=array();
          $room_descr=gks_lang('[δωμάτιο]');
          if (isset($room_descr_array[$myroom['room_item_id']])) $room_descr=$room_descr_array[$myroom['room_item_id']]; 
          
          $room_type_descr=gks_lang('[τύπος δωματίου]');
          if (isset($room_type_descr_array[$selroom['room_type_id']])) $room_type_descr=$room_type_descr_array[$selroom['room_type_id']]; 
          
          $in_array['room_name']=array('value' => gks_print_isset_s($room_descr),'type' => 's');
          $in_array['room_name_en']=array('value' => gks_print_isset_s($room_descr),'type' => 's');
          $in_array['room_type']=array('value' => gks_print_isset_s($room_type_descr),'type' => 's');
          $in_array['room_type_en']=array('value' => gks_print_isset_s($room_type_descr),'type' => 's');
      
          $in_array['check_in']=    array('value' => myDateTimeFormat(($check_in)),'type' => 's');
          $in_array['check_in_d']=  array('value' => myDateFormat(($check_in)),'type' => 's');
          $in_array['check_in_dw']= array('value' => myDateFormatw(($check_in)),'type' => 's');
          $in_array['check_in_dt']= array('value' => myDateTimeFormat(($check_in)),'type' => 's');
          $in_array['check_in_dtw']=array('value' => myDateTimeFormatw(($check_in)),'type' => 's');
          
          $in_array['check_out']=    array('value' => myDateTimeFormat(($check_out)),'type' => 's');
          $in_array['check_out_d']=  array('value' => myDateFormat(($check_out)),'type' => 's');
          $in_array['check_out_dw']= array('value' => myDateFormatw(($check_out)),'type' => 's');
          $in_array['check_out_dt']= array('value' => myDateTimeFormat(($check_out)),'type' => 's');
          $in_array['check_out_dtw']=array('value' => myDateTimeFormatw(($check_out)),'type' => 's');
          
          $in_array['days']=array('value' => $reservation['num_days'],'type' => 'n');
          $in_array['adults']=array('value' => gks_print_isset_n($myroom['rnum_adults']),'type' => 'n');
          $in_array['childs']=array('value' => gks_print_isset_n($myroom['rnum_childs']),'type' => 'n');
          $in_array['visitors']=array('value' => ($myroom['rnum_adults'] + $myroom['rnum_childs']),'type' => 'n');
          $in_array['child_kounies']=array('value' => gks_print_isset_n($myroom['rnum_child_kounies']),'type' => 'n');
          $in_array['extra_beds']=array('value' => gks_print_isset_n($myroom['rnum_extra_beds']),'type' => 'n');
          
          
          
          $tr_m= array('html' => $text_room_template, 'mc' => $mc, 'tr_hide'=> false);
          $text_room=gks_print_form_replace_field($tr_m,$in_array);
      
          
          $text_room=str_replace('[[data]]', '', $text_room);
          $text_room=str_replace('{hide}',   '', $text_room);
         
          $text_room=str_replace("\r\n\r\n","\r\n", $text_room);
          $text_room=str_replace("\r\n\r\n","\r\n", $text_room);
          $text_room=str_replace("\r\n\r\n","\r\n", $text_room);
          $text_room=str_replace("\r\n\r\n","\r\n", $text_room);
          if (endwith($text_room,"\r\n")) $text_room=substr($text_room,0, strlen($text_room)-2);
              
          $cart_item['woo_text']=$text_room;
          
          $cart_items[]=$cart_item;
        //}
      }
    }
  }
        
  //$cart_items=array();
  if (count($cart_items)<=0) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν κρατήσεις')));
    return $return;}
    
    
            
  //$return = array('success' => false, 'message' => base64_encode('<pre>cart_items: '.print_r($cart_items,true)),'cart_items' => $cart_items);
  //return $return;

  
  //$_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations']

  $return = array('success' => true, 'message' => base64_encode('ok'),'cart_items' => $cart_items);
  return $return;
  

  $return = array('success' => false, 'message' => base64_encode('ssssss'.print_r($input_data,true)));
  return $return;
    
}

