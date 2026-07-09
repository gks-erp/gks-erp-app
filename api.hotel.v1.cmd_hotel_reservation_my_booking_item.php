<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_hotel_cmd_hotel_reservation_my_booking_item($id_hotel,$row_hotel,$input_data) {

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
  
  //return '<pre>ssssssss'.print_r($input_data,true).'</pre>';
  
  $gks_erp_cookie_id='';
  if ($from=='website') {
    if(isset($input_data['gks_erp_cookie_id'])) {
      $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
    }
    $hotel_title=$row_hotel['hotel_title'];
    $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
    gks_erp_cookie_start($gks_erp_cookie_id);
    //return '<pre>dddddddddd '.print_r($_gks_session,true).'</pre>';
    //$return=array('success' => false, 'message' => base64_encode($_gks_session['gks']['ui_lang'].' σσσσσσσ '.print_r($_gks_session,true)),'data' => false, 'debug'=>'');
    //return $return;
  }
  
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }

  //$return=array('success' => false, 'message' => base64_encode($_gks_session['gks']['ui_lang'].' σσ'),'data' => false, 'debug'=>'');
  //return $return;
  
  $db_lang='';$db_lang2='';if ($_gks_session['gks']['ui_lang']=='en-US') {$db_lang='_en_US';$db_lang2='_en';}
  
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  $return=array('success' => false, 'message' => base64_encode(gks_lang('Γενικό σφάλμα')),'data' => false, 'debug'=>'');
  $error_html=[];
  //return $return;
  
  if (isset($input_data['guid'])==false or isset($input_data['mycookie_vals'])==false or count($input_data['mycookie_vals'])!=6) {
    $return['message'] = base64_encode(gks_lang('Δεν έχουν ορισθεί όλα τα δεδομένα')); return $return;}
    
  $guid=$input_data['guid'];
  $hash1=$input_data['mycookie_vals'][0];
  $hash2=$input_data['mycookie_vals'][1];
  $hash3=$input_data['mycookie_vals'][2];
  $my_prefix=$input_data['mycookie_vals'][3];
  $my_number=$input_data['mycookie_vals'][4];
  $my_email=$input_data['mycookie_vals'][5];
  
  $hash2_calc=md5($hash1.$hash1.$hash3.$hash1.$hash1);
  if ($hash2_calc!=$hash2 or $hash3!=$guid) {
    $return['message'] = base64_encode(gks_lang('Σφάλμα κατακερματισμού')); return $return;}
  
  $return['input_data']=$input_data;


  $hotel_params=gks_hotel_get_params($id_hotel);

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

  //$return['html']='<pre>dddddddddd '.$sql_templete.'</pre>';$return['success']=true; return $return;
  
  
  $my_recs=array();

  $sql=str_replace('[[hotel_booking_number]]',$db_link->escape_string($hotel_booking_number),$sql_templete);
  
//  $return['html']='<pre>dddddddddd '.$sql.'</pre>';$return['success']=true; return $return;
  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
  while ($row = $result->fetch_assoc()) {  
    $my_recs[]=$row;
  }
  
  if (count($my_recs)==0) {
    $sql=str_replace('[[hotel_booking_number]]',$db_link->escape_string($hotel_booking_number.'-%'),$sql_templete);
    //$return['html']='<pre>dddddddddd '.$sql.'</pre>';$return['success']=true; return $return;

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
//    $sql=str_replace('[[booking_booking_number]]',$db_link->escape_string($booking_booking_number.'-2'),$sql_templete);
//    $result = $db_link->query($sql);
//    if (!$result) {
//      debug_mail(false,'error sql',$sql);
//      $return = array('success' => false, 'message' => gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
//      return $return; }
//    while ($row = $result->fetch_assoc()) {  
//      $my_recs[]=$row;
//    }
//  }
  
  if (count($my_recs)==0) {
    debug_mail(false,'reservation not found',$sql);
    $return['message'] = base64_encode(gks_lang('Δεν βρέθηκε η κράτηση')); return $return;}
    
  $is_hash_ok=false;
  foreach ($my_recs as $value) {
    if ($value['reservation_guid']==$guid) {
      $is_hash_ok=true; break;
    }
  } 
  
  if ($is_hash_ok==false) {
    $return['message'] = base64_encode(gks_lang('Σφάλμα κατακερματισμού')); return $return;}
  
  $id_hotel_reservation_ids=array();
  foreach ($my_recs as $value) {
    $id_hotel_reservation_ids[]=$value['id_hotel_reservation'];
  } 

  $sql=select_gks_hotel_reservation();
  $sql.=" where id_hotel_reservation in (".implode(',',$id_hotel_reservation_ids).")";  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
    
  
  $mytr_array=array();
  while ($row = $result->fetch_assoc()) {
    $mytr_array[]=$row;
  }

  $html='
  <link href="'.GKS_SITE_URL.'my/css/gks_frontend.css?v='.$gks_cache_version.'" rel="stylesheet" type="text/css"/>
  <link href="'.GKS_SITE_URL.'my/css/gks_frontend_fontawesome-all.css" rel="stylesheet">
  <link href="'.GKS_SITE_URL.'my/css/hotel.css?v='.$gks_cache_version.'" rel="stylesheet">';

  $gks_total_gks_price_total=0;
  $gks_total_rooms_plithos=0;
  $gks_total_num_days=0;
  $gks_total_visitors_adults=0;
  $gks_total_visitors_childs=0;
  $gks_total_child_kounies=0;
  $gks_total_extra_beds=0;
      
  $rsrv_aa=0;
  foreach ($mytr_array as $mytr) {
    $rsrv_aa++;
    $gks_total_gks_price_total+=$mytr['gks_price_total'];
    $gks_total_rooms_plithos+=$mytr['rooms_plithos'];
    $gks_total_num_days+=$mytr['num_days'];
    
     
    $hotel_reservation_status_out=$mytr['reservation_status'];
    switch ($mytr['reservation_status']) {   
       case '005prodraft':
        $hotel_reservation_status_out=gks_lang('Σε καλάθι','part4','hotelreservationstatusdescr');
        break;
      case '010draft':
        $hotel_reservation_status_out=gks_lang('Πρόχειρη','part4','hotelreservationstatusdescr');
        break;
      case '040cancelled':
        $hotel_reservation_status_out=gks_lang('Ακυρωμένη','part4','hotelreservationstatusdescr');
        break;
      case '050rejected':
        $hotel_reservation_status_out=gks_lang('Απορρίφθηκε','part4','hotelreservationstatusdescr');
        break;
      case '070wait_payment':
        $hotel_reservation_status_out=gks_lang('Αναμονή Πληρωμής','part4','hotelreservationstatusdescr');
        break;
      case '080confirm':
        $hotel_reservation_status_out=gks_lang('Επιβεβαιωμένη','part4','hotelreservationstatusdescr');
        break;
      case '100completed':
        $hotel_reservation_status_out=gks_lang('Ολοκληρωμένη','part4','hotelreservationstatusdescr');
        break;
      case '110payment':
        $hotel_reservation_status_out=gks_lang('Εξοφλημένη','part4','hotelreservationstatusdescr');
        break;
    }
  
  
    $html.='<div class="gks_box_shadow gks_rsrv_rs" style="width:100%;background-color111:#f8f8f8;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;border:1px solid #dddddd;">';
  
    $html.='<h1 class="gks_hotel_my_booking_view_booking_number"><span class="gks_hotel_my_booking_view_booking_number_title">'.
      gks_lang('Κωδικός κράτησης').
      ': </span><span class="gks_hotel_my_booking_view_booking_number_number">'.$mytr['hotel_booking_number'].'</span></h1>';
    $html.='<h2 class="gks_hotel_my_booking_view_booking_state"><span class="gks_hotel_my_booking_view_booking_state_title">'.
      gks_lang('Κατάσταση κράτησης').
      ': </span><span class="gks_hotel_my_booking_view_booking_state_value">'.
      gks_lang($hotel_reservation_status_out.'','part4','hotelreservationstatusdescr').'</span></h1>';
  
    $html.='</div>';
    
    $html.='<div class="gks_box_shadow gks_rsrv_rs" style="width:100%;background-color111:#f8f8f8;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;border:1px solid #dddddd;">
      <div><h2 style="text-transform:unset;text-align:center;">'.gks_lang('Τα προσωπικά σας στοιχεία').'</h2></div>';
      $html.='<div style="width:100%;padding-bottom:20px">';
        $html.='<div class="gks_rsrv_hfd">'.gks_lang('Όνομα').': <b>'.$mytr['user_first_name'].'</b></div>';
        $html.='<div class="gks_rsrv_hfd">'.gks_lang('Επώνυμο').': <b>'.$mytr['user_last_name'].'</b></div>';
        $html.='<div class="gks_dfn"></div>';  
        $html.='<div class="gks_rsrv_hfd">'.gks_lang('email').': <b>'.$mytr['user_email'].'</b></div>';
        $html.='<div class="gks_rsrv_hfd">'.gks_lang('Κινητό Τηλέφωνο').': <b>'.$mytr['user_mobile'].'</b></div>';
        $html.='<div class="gks_dfn"></div>';  
        $html.='<div class="gks_rsrv_hfd">'.gks_lang('Χώρα').': <b>'.$mytr['country_name'.$db_lang].'</b></div>';
        $html.='<div class="gks_rsrv_hfd">'.gks_lang('Γλώσσα').': <b>'.$mytr['lang_name'.$db_lang].'</b></div>';
        $html.='<div class="gks_dfn"></div>';  
      $html.='</div>';
    $html.='</div>';
    
  
    $sql_rooms=select_gks_hotel_reservation_room();  
    $sql_rooms.=" where hotel_reservation_id=".$mytr['id_hotel_reservation']."
    order by id_hotel_reservation_room";
    $result_rooms = $db_link->query($sql_rooms);
    if (!$result_rooms) {
      debug_mail(false,'error sql',$sql_rooms);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }
    
    
    $myroom_types=array();$rooms_cc=0;
    while ($row_room = $result_rooms->fetch_assoc()) {
      if (isset($myrooms[$row_room['id_hotel_room_type']])==false) {
        $myroom_types[$row_room['id_hotel_room_type']]=array(
          'id_hotel_room_type'=> $row_room['id_hotel_room_type'],
          'room_type_descr'=> $row_room['room_type_descr'.$db_lang],
          'rooms_items'=>array(),
        );
      }
      $myroom_types[$row_room['id_hotel_room_type']]['rooms_items'][]=$row_room;
      $rooms_cc++;
      
  
    }
    $mytr['rooms_plithos']=$rooms_cc;
    
  
    $check_in_round_time=strtotime($mytr['check_in']);
    $check_out_round_time=strtotime($mytr['check_out']);
    
    
    $html.='<div class="gks_box_shadow gks_rsrv_rs gks_rsrv_box1" data_rsrv_aa="'.$rsrv_aa.'" style="width:100%;background-color111:#f8f8f8;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;border:1px solid #dddddd;">
      <div><h2 style="text-transform:unset;text-align:center;">'.gks_lang('Κράτηση').' '.($rsrv_aa).'</h2></div>';
  
    $html.='<div style="width:100%;padding-bottom:20px">';
      $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ημερομηνία άφιξης').': <b>'.
              getWeekDayName(date('w', $check_in_round_time)).' '.
              date('j', $check_in_round_time).' '.
              getMonthName(date('n', $check_in_round_time)).' '.
              date('Y', $check_in_round_time).'</b>'.'</div>';
      $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ώρα άφιξης').': '.gks_lang('μετά τις').' <b>'.$hotel_params['hotel_default_checkin'].'</b></div>';
      $html.='<div class="gks_dfn"></div>';
      $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ημερομηνία αναχώρησης').': <b>'.
              getWeekDayName(date('w', $check_out_round_time + 24*60*60)).' '.
              date('j', $check_out_round_time + 24*60*60).' '.
              getMonthName(date('n', $check_out_round_time + 24*60*60)).' '.
              date('Y', $check_out_round_time + 24*60*60).'</b>'.'</div>';
      $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ώρα αναχώρησης').': '.gks_lang('έως τις').' <b>'.$hotel_params['hotel_default_checkout'].'</b></div>';
      $html.='<div class="gks_dfn"></div>';
      $html.='<div class="gks_rsrv_hfd">'.gks_lang('Διανυκτερεύσεις').': <b>'.$mytr['num_days'].'</b>'.'</div>';
      $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ενήλικες').': <b>'.$mytr['num_adults'].'</b>'.'</div>';
      $html.='<div class="gks_rsrv_hfd">'.gks_lang('Παιδιά').': <b>'.$mytr['num_childs'].'</b>'.'</div>';
      $html.='<div class="gks_rsrv_hfd">'.gks_lang('Δωμάτια').': <b>'.$mytr['rooms_plithos'].'</b>'.'</div>';
      $html.='<div class="gks_dfn"></div>';
    $html.='</div>
    <div class="gks_dfn"></div>';
  
  


      
    $reservation_rnum_adults=0;
    $reservation_rnum_childs=0;
    $reservation_rnum_child_kounies=0;
    $reservation_rnum_extra_beds=0;
    
    $roomtype_aa=0;
    foreach ($myroom_types as $myroomtype) {
      $roomtype_aa++;
      $html.='<div class="gks_rsrv_bd" data_rsrv_aa="'.$rsrv_aa.'" data_roomtype_aa="'.$roomtype_aa.'" style="width:100%;display: table;font-size:180%;text-align:left;color1:blue;padding: 10px 0px 10px 0px;">';
      $html.=$myroomtype['room_type_descr'];  
      $html.='</div>';    
      
      $room_cc=0;
  
      foreach ($myroomtype['rooms_items'] as $room_aa => $myroom) {
        $room_cc++;
      
        if ($myroom['rnum_adults']>0) {
          $reservation_rnum_adults+=$myroom['rnum_adults'];
          $gks_total_visitors_adults+=$myroom['rnum_adults'];
          
        }
        if ($myroom['rnum_childs']>0) {
          $reservation_rnum_childs+=$myroom['rnum_childs'];
          $gks_total_visitors_childs+=$myroom['rnum_childs'];
        }
        if ($myroom['rnum_child_kounies']) {
          $reservation_rnum_child_kounies+=$myroom['rnum_child_kounies'];
          $gks_total_child_kounies+=$myroom['rnum_child_kounies'];
        }
        if ($myroom['rnum_extra_beds']) {
          $reservation_rnum_extra_beds+=$myroom['rnum_extra_beds'];
          $gks_total_extra_beds+=$myroom['rnum_extra_beds'];
        }
              
        
        
        $out='<div class="gks_rsrv_br" data_rsrv_aa="'.$rsrv_aa.'" data_roomtype_aa="'.$roomtype_aa.'" data_room_aa="'.$room_aa.'" style="width:100%;display: table;">
          <div class="gks_rsrv_bc1" style="">
            <table cellspacing=0 cellpadding=0 border="0" style="border:0px; width:100%;border-collapse:collapse;margin:0px">
              <tr>
                <td style="width:40%;border:0px;padding: 8px 8px 8px 0px;">';
                  
                  if ($hotel_params['hotel_reservation_can_select_room']!=0) {
                    $out.= '#'.$room_cc.' '.$myroom['room_descr'.$db_lang];
                  } else {
                    $out.= gks_lang('Δωμάτιο').' #'.$room_cc;
                  }
                  
                $out.='</td>
                <td style="width:35%;border:0px;padding: 8px;text-align:center;">';
  
                
                $out.=$myroom['rnum_adults'];
                $out.='x<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i> ';
                
  
                if ($myroom['rnum_childs']>0) {
                  $out.=$myroom['rnum_childs'];
                  $out.='x<i class="gks_fa gks_fa-child gks_rsrv_childicon" style="font-size:80%;"></i> ';
                }
                
                if ($myroom['rnum_child_kounies']>0) {
                  $out.=$myroom['rnum_child_kounies'];
                  $out.='x<i class="gks_fa gks_fa-box gks_rsrv_boxicon" style="font-size:90%;"></i> ';
                }
                if ($myroom['rnum_extra_beds']>0) {
                  $out.=$myroom['rnum_extra_beds'];
                  $out.='x<i class="gks_fa gks_fa-bed gks_rsrv_bedicon" style="font-size:100%;"></i> ';
                }
  
                
  
                $out.='</td>
                <td style="width:25%;border:0px;padding: 8px;text-align:center;">'.myCurrencyFormat($myroom['product_price_final_all_total'], true,true).'</td>
              </tr>
            </table>';
              
              
              
            
            
          $out.='</div>
          <div class="gks_rsrv_bc2" style="width:calc(30% - 30px);float: left;margin-left: 20px;margin-bottom: 0px;padding: 8px 10px 20px 0px;font-size:100%;text-align:left;color111:black;">
            <table cellspacing=0 cellpadding=0 border="0" style="border:0px; width:100%;border-collapse:collapse;margin:0px">
              <tr>
                <td class="gks_rsrv_basket_visitor">';
                  
                  $is_same_visitor=true;
                  if ($myroom['ruser_id'] >=0) $is_same_visitor=false;
                  $elem=$rsrv_aa.'_'.$roomtype_aa.'_'.$room_aa;
                  
                  $out.=gks_lang('Επισκέπτης').': ';
                  if ($is_same_visitor) {
                    $out.=gks_lang('Εσείς');
                  } else {
                    $out.=gks_lang('Άλλος');
                  }
                         
                  $out.='<br>
                  <span id="visitor_name_'.$elem.'">';
                    if ($is_same_visitor==false) {
                      $tmps='';
                      $tmps=trim_gks($myroom['ruser_first_name'].' '.$myroom['ruser_last_name']);
                      if ($tmps != '') $tmps.=', ';
                      if ($myroom['ruser_email'] != '')   $tmps.=$myroom['ruser_email'].', ';
                      if ($myroom['ruser_mobile'] != '')   $tmps.=$myroom['ruser_mobile'].', ';
                      $tmps = trim_gks($tmps);  
                      if (strlen($tmps)>1) $tmps=substr($tmps, 0, strlen($tmps) -1);
                      $out.= $tmps;
                    }
                    
                    $out.='</span>
                </td>
              </tr>
            </table>                
          </div>
        </div>';
        
        
        $html.=$out;
      }    
    }
  
    $out=
    '<div class="gks_rsrv_sum" data_rsrv_aa="'.$rsrv_aa.'" style="width:100%;display: table;">
      <div class="gks_rsrv_bc1" style="">
        <table cellspacing="0" cellpadding="0" border="0" style="border:0px; width:100%;border-collapse:collapse;margin:0px">
          <tbody><tr>
            <td style="width:40%;font-size: 110%;border:0px;padding: 8px 8px 8px 0px;">'.gks_lang('Σύνολο κράτησης').'</td>
            <td style="width:35%;font-size: 110%;border:0px;padding: 8px;text-align:center;">';
            
            
  
    $out.=    '<span class="gks_rsrv_total_visitors_adults" data_rsrv_aa="'.$rsrv_aa.'">'.$reservation_rnum_adults.'</span>x<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i> ';
    if ($reservation_rnum_childs>0)
      $out.=  '<span class="gks_rsrv_total_visitors_childs" data_rsrv_aa="'.$rsrv_aa.'">'.$reservation_rnum_childs.'</span>x<i class="gks_fa gks_fa-child gks_rsrv_childicon" style="font-size:80%;"></i></i> ';
    if ($reservation_rnum_child_kounies>0)
      $out.=  '<span class="gks_rsrv_total_visitors_child_kounies" data_rsrv_aa="'.$rsrv_aa.'">'.$reservation_rnum_child_kounies.'</span>x<i class="gks_fa gks_fa-box gks_rsrv_boxicon" style="font-size:90%;"></i></i> ';
    if ($reservation_rnum_extra_beds>0)
      $out.=  '<span class="gks_rsrv_total_visitors_extra_beds" data_rsrv_aa="'.$rsrv_aa.'">'.$reservation_rnum_extra_beds.'</span>x<i class="gks_fa gks_fa-bed gks_rsrv_bedicon" style="font-size:100%;"></i></i> ';
    
    $out.=
           '</td>
            <td style="width:25%;font-size: 110%;border:0px;padding: 8px;text-align:center;" id="gks_rsrv_total_price_'.$rsrv_aa.'">'.myCurrencyFormat($mytr['gks_price_total'],true,true).'</td>
          </tr>
        </tbody></table>
      </div>
  
      <div class="gks_rsrv_bc2" style="width:calc(50% - 30px);float: left;margin-left: 20px;margin-bottom: 0px;padding: 8px 10px 20px 0px;font-size:100%;text-align:left;color111:black;">
        <table cellspacing="0" cellpadding="0" border="0" style="border:0px; width:100%;border-collapse:collapse;margin:0px">
          <tbody><tr>
            <td id="gks_rsrv_warning_'.$rsrv_aa.'" class="gks_rsrv_basket_visitor">';
              $text_warn='';
              //if ($reservation['total_domatia'] < $reservation['rooms']) $text_warn.='<p style="margin:0px;"><i class="gks_fas gks_fa-exclamation-triangle" style="font-size:150%;color:orange;"></i> '.gks_lang('Προσοχή: Έχετε επιλέξει λιγότερα δωμάτια από αυτά που θέλετε').'</p>';
              //if ($reservation['total_visitors'] < ($reservation['adults'] + $reservation['childs'])) $text_warn.='<p style="margin:0px;"><i class="gks_fas gks_fa-exclamation-triangle" style="font-size:150%;color:orange;"></i> '.gks_lang('Προσοχή: Τα δωμάτια που έχετε επιλέξει εξυπηρετούν λιγότερους επισκέπτες από αυτούς που θέλετε').'</p>';
              //if ($text_warn != '') {
              //  $out.= '<div style="margin:0px;padding:10px 10px 10px 10px;border:1px solid ;background-color111: #feffbe;">'.$text_warn.'</div>';
              //}
            $out.='</td>
          </tr>
        </tbody></table>                
      </div>
    </div>';
    
    $html.=$out;
  
    
    $html.=
      '
    </div>';  
  }
  
  if (count($mytr_array)>=2) {
  $out=
  '<div class="gks_box_shadow" id="gks_total_div" style="width:100%;background-color111:#f8f8f8;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;border:1px solid #dddddd;">
    <div style="text-transform:unset;text-align:center;margin:0px;">
      <div style="line-height: 2;font-size:120%;">
        '.gks_lang('Σύνολο κρατήσεων').': <span id="gks_total_reservations_span" style="font-weight:bold;">'.count($mytr_array).'</span>
        <br>

        '.gks_lang('Σύνολο δωματίων').': <span id="gks_total_domatia_span" style="font-weight:bold;">'.$gks_total_rooms_plithos.'</span>
        <br>
        '.gks_lang('Σύνολο διανυκτερεύσεων').': <span id="gks_total_dianiktereuseis_span" style="font-weight:bold;">'.$gks_total_num_days.'</span>
        <br>
        '.gks_lang('Σύνολο επισκεπτών').': 
        <span id="gks_total_visitors_adults">'.$gks_total_visitors_adults.'</span>x<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i> ';
       
 if ($gks_total_visitors_childs>0) 
   $out.='<span id="gks_total_visitors_childs">'.$gks_total_visitors_childs.'</span>x<i class="gks_fa gks_fa-child gks_rsrv_childicon" style="font-size:80%;"></i></i> ';
 
 $extra_line='';
 if ($gks_total_child_kounies>0) 
   $extra_line.='<span id="gks_total_child_kounies">'.$gks_total_child_kounies.'</span>x<i class="gks_fa gks_fa-box gks_rsrv_boxicon" style="font-size:90%;"></i></i> ';
 if ($gks_total_extra_beds>0) 
   $extra_line.='<span id="gks_total_child_kounies">'.$gks_total_extra_beds.'</span>x<i class="gks_fa gks_fa-bed gks_rsrv_bedicon" style="font-size:100%;"></i></i> ';

 if ($extra_line!='')       
  $out.='<br>'.
        gks_lang('Επιπλέον').': '.$extra_line;
  
 $out.='<br>'.
        gks_lang('Σύνολο αξίας κρατήσεων').': <span id="gks_total_price_span" style="font-weight:bold;">'.myCurrencyFormat($gks_total_gks_price_total,true,true).'</span>
      </div>

    </div>
  </div>';
      
  $html.=$out;
  }

  
               
 
  

  //$html.=$_gks_session['gks']['ui_lang'];
  //$html.='<pre>'.print_r($mytr,true).'</pre>';
  
  $return['html']='<gks_reservation_list>'.$html.'</gks_reservation_list>';
  
  $return['success']=true; 
 
  return $return;
  

  


  
}


