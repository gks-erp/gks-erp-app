<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_hotel_cmd_hotel_reservation_basket($id_hotel,$row_hotel,$input_data) {
  global $db_link;
  global $gks_cache_version;
  global $_gks_session;
  global $_gks_id_session;
  global $gks_user_settings;
  global $gks_user_settings;
  
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW;
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;
  global $GKS_HOTEL_RESERVATIONS_ONLINE;
  global $autocomplete_gks_disable;

  
  $gks_erp_cookie_id='';
  if(isset($input_data['gks_erp_cookie_id'])) {
    $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
  }
  //print '<pre>|'.$gks_erp_cookie_id.'|';
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
    
  $defs = get_def_check($id_hotel);
  $hotel_params=gks_hotel_get_params($id_hotel);

  

  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/_gks_session.txt',print_r($_gks_session,true));

//  unset($_gks_session['gks']['confirm']);
//
//  
//  
//  
//  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
//  $my_wp_user_id=get_current_user_id();
//  $my_wp_user_info=wp_get_current_user();
//  //$my_is_global_admin=is_global_admin();
//  
//  if (defined('ICL_LANGUAGE_CODE')) $_gks_session['gks']['ui_lang']=gks_lang_map_WPML_to_gks(ICL_LANGUAGE_CODE);
//  $gks_load_lang_filename = gks_load_lang('gks_core/inc_gks_basket.php');
//  
//  db_open();

  
  $mydaydif=0;
  $mytimenow=time() + $mydaydif*24*60*60; // + 0*24*60*60;
  $time_vardia=_time_user($mytimenow, 1);
  $time_vardia-= GKS_ERP_START_VARDIA*60*60;
  $today_vardia = date('Y-m-d',$time_vardia);
  $today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
  //$today_vardia = _time_user($today_vardia, -1);
  //$today_vardia = $today_vardia;
  $today_vardia_time = $today_vardia;
  $today_vardia = date('Y-m-d H:i:s', $today_vardia);
  
  $defs = get_def_check($id_hotel);
  $hotel_params=gks_hotel_get_params($id_hotel);

  $db_lang='';$db_lang2='';
  if ($_gks_session['gks']['ui_lang']=='en-US') {$db_lang='_en_US';$db_lang2='_en';}
  
  
  //$myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
  $myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
  $elems=array(); $total_sum=0; $total_visitors=0; $total_dianiktereuseis=0; $total_domatia=0;
  hotel_basket_rsrv_calc($id_hotel,$myreservations, $elems, $total_sum, $total_visitors, $total_dianiktereuseis, $total_domatia, true);
  $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'] = $myreservations;
  gks_erp_cookie_save($gks_erp_cookie_id);
  
  
  $id_hotel_room_type_array=array();
  $id_hotel_room_array = array();
  foreach ($myreservations as $reservation) {
    foreach ($reservation['selrooms'] as $selroom) {
      if (in_array($selroom['roomtype']['id'],$id_hotel_room_type_array)== false) {
        $id_hotel_room_type_array[] = $selroom['roomtype']['id'];
      }
      foreach ($selroom['roomtype']['free_rooms'] as $free_room) {
        if (in_array($free_room, $id_hotel_room_array)== false) {
          $id_hotel_room_array[] = $free_room;
        }      
      } 
    } 
  }
  
  $hotel_room_type_array=array();
  if (count($id_hotel_room_type_array)>0) {
    
    $lang_data_sqlfl=gks_lang_data_obj_prepare('gks_hotel_room_type','default');
    if ($lang_data_sqlfl['success']==false) die($lang_data_sqlfl['message']);
    gks_lang_data_obj_sql_prepare($lang_data_sqlfl, array('room_type_descr'));
    
    $sql="SELECT id_hotel_room_type, room_type_descr, room_type_embado, room_type_visitors, room_type_bedrooms, room_type_living_rooms, room_type_bathrooms,
    ".gks_lang_sql_field('room_type_descr',$lang_data_sqlfl)."
    FROM ".$lang_data_sqlfl['sql']['from1']." gks_hotel_room_type
    ".$lang_data_sqlfl['sql']['from2']."
    WHERE id_hotel_room_type In (".implode(',',$id_hotel_room_type_array).")
    order by room_type_sortorder";
    $result = $db_link->query($sql); 
    if (!$result) {debug_mail(false,'error sql',$sql); die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
    while ($row = $result->fetch_assoc()) {
      $hotel_room_type_array[$row['id_hotel_room_type']] = $row;
    }
  }
  //print '<pre>';
  //print_r($hotel_room_type_array);
  //die();
  
  
  $hotel_room_array=array();
  if (count($id_hotel_room_type_array)>0) {
    $sql="SELECT gks_hotel_room.id_hotel_room, gks_hotel_room.hotel_room_type_id, gks_hotel_room.hotel_floor_id, 
    gks_hotel_room.room_descr,gks_hotel_room_en_US.room_descr_en_US,
    gks_hotel_floor.floor_descr,gks_hotel_floor_en_US.floor_descr_en_US
    FROM ((gks_hotel_room 
    LEFT JOIN gks_hotel_floor ON gks_hotel_room.hotel_floor_id = gks_hotel_floor.id_hotel_floor)
    LEFT JOIN (
      SELECT hotel_floor_id, floor_descr as floor_descr_en_US FROM gks_hotel_floor_lang WHERE lang_code='en-US'
    ) AS gks_hotel_floor_en_US ON gks_hotel_floor.id_hotel_floor = gks_hotel_floor_en_US.hotel_floor_id) 
    LEFT JOIN (
      SELECT hotel_room_id, room_descr as room_descr_en_US FROM gks_hotel_room_lang WHERE lang_code='en-US'
    ) AS gks_hotel_room_en_US ON gks_hotel_room.id_hotel_room = gks_hotel_room_en_US.hotel_room_id
    
    WHERE gks_hotel_room.id_hotel_room In (".implode(',',$id_hotel_room_array).")
    order by room_sortorder";
    $result = $db_link->query($sql); 
    if (!$result) {debug_mail(false,'error sql',$sql); die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
    while ($row = $result->fetch_assoc()) {
      $hotel_room_array[$row['id_hotel_room']] = $row;
    }
  }
  
  //$dev_page_starttime1=microtime(true);
  gks_basket_recalc($_gks_session['gks']['basket'], array(), array());




  
  $out='';
  //$out.='|sssssssss|'.$gks_erp_cookie_id.'|dddddd|';  
  //$out.='|sssssssss|'.$hotel_params['hotel_use_checkout_system'].'|dddddd|'; 
  
  $out.='<script language="javascript" type="text/javascript">
   var originalJQuery=jQuery;
   var originalJQuerySign=$;
</script>

<link href="'.GKS_SITE_URL.'my/css/gks_frontend.css?v='.$gks_cache_version.'" rel="stylesheet" type="text/css"/>
<link href="'.GKS_SITE_URL.'my/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<link href="'.GKS_SITE_URL.'my/css/jquery-ui.min.css" rel="stylesheet">
<link href="'.GKS_SITE_URL.'my/css/jquery-ui.structure.min.css" rel="stylesheet">
<link href="'.GKS_SITE_URL.'my/css/jquery-ui.theme.min.css" rel="stylesheet">
<link href="'.GKS_SITE_URL.'my/css/gks_frontend_fontawesome-all.css" rel="stylesheet">
<link href="'.GKS_SITE_URL.'my/css/hotel.css?v='.$gks_cache_version.'" rel="stylesheet">
<script src="'.GKS_SITE_URL.'my/js/jquery-3.3.1.min.js"></script>
<script src="'.GKS_SITE_URL.'my/js/jquery-ui.min.js"></script>
<script src="'.GKS_SITE_URL.'my/js/jquery.base64.js"></script>
<script src="'.GKS_SITE_URL.'my/js/jquery.datetimepicker.full.min.js" type="text/javascript"></script>
<script src="'.GKS_SITE_URL.'my/js/my.js?v='.$gks_cache_version.'" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/css/tooltipster-noir.css"/>
<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/css/tooltipster.css"/>
<script type="text/javascript" src="'.GKS_SITE_URL.'my/js/tooltipster-3.0/js/jquery.tooltipster.min.js"></script>

<script language="javascript" type="text/javascript">
    var jQuery3=jQuery;
    window.jQuery =originalJQuery;
    window.$ = originalJQuerySign;
</script>











<div class="gks_main_content">
  <div class="gks_body_wrapper">
    <div id="" class="gks_container">';

if ($hotel_params['hotel_use_checkout_system']=='') {
  $out.=      
     '<div class="gks_row">
        <div style="margin: 0px;padding: 0px 28px 0px 28px;; background-color: transparent;"   >
          <div style="float: left;width:40%;min-width:250px; font-size:10pt; padding:20px 10px 20px 0px;border: 0px solid #ddd;"> 
            <p ><span style="font-size:24pt">'.gks_lang('Το καλάθι μου').'<br></span></p>
          </div>
          <div style="float: left;width:60%;min-width:250px;text-align: right !important;padding:20px 0px 0px 0px;">          
            <span style="background-color: #73AD21;" class="gks_basket_button" id="header_basket_show">'.gks_lang('Επισκόπηση Παραγγελίας').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: gray;" class="gks_basket_button" id="header_basket_checkout">'.gks_lang('Αποστολή & Χρέωση').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: gray;" class="gks_basket_button" id="header_basket_pay">'.gks_lang('Πληρωμή').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: gray;" class="gks_basket_button" id="header_basket_confirm">'.gks_lang('Επιβεβαίωση').'</span>
          </div>
          <div style="clear: both;"></div>
        </div>
      </div>';
      
    }
$out.=
     '<div class="gks_row">
        <h1 id="gks_rsrv_h1" class="page-title" style="text-transform:unset;text-align:center;'.(count($myreservations)==0 ? 'display:none;' : '').'">'.gks_lang('Οι νέες κρατήσεις μου').'</h1>
        <!--
        <h1 id="gks_rsrv_h0" class="page-title" style="text-transform:unset;text-align:center;'.(count($myreservations)> 0 ? 'display:none;' : '').'">'.gks_lang('Δεν έχετε προσθέσει κάποια κράτηση').'</h1>
        -->
      </div>
      <div class="gks_row">
';

        $gks_total_visitors_adults=0;
        $gks_total_visitors_childs=0;
        $gks_total_child_kounies=0;
        $gks_total_extra_beds=0;
        foreach ($myreservations as $rsrv_aa => $reservation) {
          $check_in_round_time = strtotime($reservation['check_in']);
          $check_out_round_time = strtotime($reservation['check_out']);
          
           
          $out.='<div class="gks_box_shadow gks_rsrv_rs" data_rsrv_aa="'.$rsrv_aa.'" style="width:100%;background-color111:#f8f8f8;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;border:1px solid #dddddd;">
            <div><h2 style="text-transform:unset;text-align:center;">'.gks_lang('Κράτηση').' '.($rsrv_aa + 1).'</h2></div>';

          $html='<div style="width:100%;padding-bottom:20px">';
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
            $html.='<div class="gks_rsrv_hfd">'.gks_lang('Διανυκτερεύσεις').': <b>'.$reservation['num_days'].'</b>'.'</div>';
            $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ενήλικες').': <b>'.$reservation['adults'].'</b>'.'</div>';
            $html.='<div class="gks_rsrv_hfd">'.gks_lang('Παιδιά').': <b>'.$reservation['childs'].'</b>'.'</div>';
            $html.='<div class="gks_rsrv_hfd">'.gks_lang('Δωμάτια').': <b>'.$reservation['rooms'].'</b>'.'</div>';
            $html.='<div class="gks_dfn"></div>';
          $html.='</div>
          <div class="gks_dfn"></div>';   
          $out.=  $html;

          $reservation_rnum_adults=0;
          $reservation_rnum_childs=0;
          $reservation_rnum_child_kounies=0;
          $reservation_rnum_extra_beds=0;
            
          foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {

            


            $out.='<div class="gks_rsrv_bd" data_rsrv_aa="'.$rsrv_aa.'" data_roomtype_aa="'.$roomtype_aa.'" style="width:100%;display: table;font-size:180%;text-align:left;color1:blue;padding: 10px 0px 10px 0px;">';
              
              if (isset($hotel_room_type_array[$selroom['roomtype']['id']])) {
                $out.= $hotel_room_type_array[$selroom['roomtype']['id']]['room_type_descr'];
              } 
              
            $out.='</div>';

            $room_cc=0;

            
            foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
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
                    
              
              
              $out.='<div class="gks_rsrv_br" data_rsrv_aa="'.$rsrv_aa.'" data_roomtype_aa="'.$roomtype_aa.'" data_room_aa="'.$room_aa.'" style="width:100%;display: table;">
                <div class="gks_rsrv_bc1" style="">
                  <table cellspacing=0 cellpadding=0 border="0" style="border:0px; width:100%;border-collapse:collapse;margin:0px">
                    <tr>
                      <td style="width:40%;border:0px;padding: 8px 8px 8px 0px;">
                        <i class="gks_fas gks_fa-trash-alt gks_rsrv_basket_delete_icon"
                          data_rsrv_aa="'.$rsrv_aa.'" 
                          data_roomtype_aa="'.$roomtype_aa.'"
                          data_room_aa="'.$room_aa.'"></i>';
                        
                        if ($hotel_params['hotel_reservation_can_select_room']!=0) {
                          $out.= '#'.$room_cc.' <select 
                          class="gks_input_select rooms_items" 
                          data_rsrv_aa="'.$rsrv_aa.'" 
                          data_roomtype_aa="'.$roomtype_aa.'"
                          data_room_aa="'.$room_aa.'"
                          style="width:calc(100% - 50px) !important;"><option value="0"></option>';
                          foreach ($selroom['roomtype']['free_rooms'] as $free_room) {
                            
                            if (isset($hotel_room_array[$free_room])) {
                              $out.= '<option value="'.$free_room.'"';
                              if ($myroom['room_item_id'] == $free_room) $out.= ' selected ';
                              $out.= '>'.$hotel_room_array[$free_room]['room_descr'.$db_lang].
                              (empty($hotel_room_array[$free_room]['floor_descr'.$db_lang]) ? '' : ' ('.$hotel_room_array[$free_room]['floor_descr'.$db_lang].')').
                              '</option>';
                            } else {
                              $out.= '<option value="'.$free_room.'">'.$free_room.'</option>';
                            }
                            //room_descr, gks_hotel_room.hotel_room_type_id, gks_hotel_room.hotel_floor_id, gks_hotel_floor.floor_descr
                            //$hotel_room_array
                             
                          } 
                          $out.= '</select>';
                        } else {
                          $out.= gks_lang('Δωμάτιο').' #'.$room_cc;
                        }
                        
                      $out.='</td>
                      <td style="width:35%;border:0px;padding: 8px;text-align:center;">';
                      //$out.='<pre>'.print_r($selroom['roomtype'],true).'</pre>';
                      
//                      $tmps='<select 
//                          class="gks_input_select gks_input_rnum_adults" 
//                          data_rsrv_aa="'.$rsrv_aa.'" 
//                          data_roomtype_aa="'.$roomtype_aa.'"
//                          data_room_aa="'.$room_aa.'"
//                          data_room_max_visitors="'.$selroom['roomtype']['visitors_max'].'"
//                          style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
//                      for($i=1;$i<=$selroom['roomtype']['visitors_adults'];$i++) {
//                        $tmps.='<option value="'.$i.'" '.($i==$myroom['rnum_adults'] ? 'selected' : '').'>'.$i.'</option>';
//                      }
//                      $tmps.='</select>';
                      
                      $out.=$myroom['rnum_adults'];
                      $out.='x<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i> ';
                      
//                      $tmps.='<select 
//                          class="gks_input_select gks_input_rnum_childs" 
//                          data_rsrv_aa="'.$rsrv_aa.'" 
//                          data_roomtype_aa="'.$roomtype_aa.'"
//                          data_room_aa="'.$room_aa.'"
//                          data_room_max_visitors="'.$selroom['roomtype']['visitors_max'].'"
//                          style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
//                      for($i=1;$i<=$selroom['roomtype']['visitors_childs'];$i++) {
//                        $tmps.='<option value="'.$i.'"'.($i==$myroom['rnum_childs'] ? 'selected' : '').'>'.$i.'</option>';
//                      }
//                      $tmps.='</select>';
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

                      //for($i=1;$i<=$selroom['roomtype']['visitors'];$i++) {
                      //  $tmps.='<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i>';
                      //}
                      
                      
                      
                      //if ($tmps!='') $out.= $tmps;
                      $out.='</td>
                      <td style="width:25%;border:0px;padding: 8px;text-align:center;">'.myCurrencyFormat($myroom['room_price'], true,true).'</td>
                    </tr>
                  </table>';
                    
                    
                    
                  
                  
                $out.='</div>
                <div class="gks_rsrv_bc2" style="width:calc(30% - 30px);float: left;margin-left: 20px;margin-bottom: 0px;padding: 8px 10px 20px 0px;font-size:100%;text-align:left;color111:black;">
                  <table cellspacing=0 cellpadding=0 border="0" style="border:0px; width:100%;border-collapse:collapse;margin:0px">
                    <tr>
                      <td class="gks_rsrv_basket_visitor">';
                        
                        $is_same_visitor=true;
                        if ($myroom['is_same'] == 0) $is_same_visitor=false;
                        $elem=$rsrv_aa.'_'.$roomtype_aa.'_'.$room_aa;
                        
                        $out.=gks_lang('Επισκέπτης').':
                        <label class="gks_label" for="selecttype0_'.$elem.'">'.gks_lang('Εσείς').'</label>
                        <input class="gks_input_radio gks_rsrv_basket_visitor_check0" type="radio" name="selecttype_'.$elem.'" id="selecttype0_'.$elem.'" value="0" '.($is_same_visitor ? ' checked ' : '').'
                        data_rsrv_aa="'.$rsrv_aa.'" 
                        data_roomtype_aa="'.$roomtype_aa.'"
                        data_room_aa="'.$room_aa.'">
                        '.gks_lang('ή').'
                        <input class="gks_input_radio gks_rsrv_basket_visitor_check1" type="radio" name="selecttype_'.$elem.'" id="selecttype1_'.$elem.'" value="1" '.(!$is_same_visitor ? ' checked ' : '').'
                        data_rsrv_aa="'.$rsrv_aa.'" 
                        data_roomtype_aa="'.$roomtype_aa.'"
                        data_room_aa="'.$room_aa.'">
                        <label class="gks_label" for="selecttype1_'.$elem.'">'.gks_lang('Άλλος').
                        
                        ' <i class="gks_fas gks_fa-edit" style="font-size:140%"></i></label>        
                        <br>
                        <span id="visitor_name_'.$elem.'">';
                          if ($myroom['is_same']==0) {
                            $tmps='';
                            $tmps=trim_gks($myroom['first_name'].' '.$myroom['last_name']);
                            if ($tmps != '') $tmps.=', ';
                            if ($myroom['email'] != '')   $tmps.=$myroom['email'].', ';
                            if ($myroom['mobile'] != '')   $tmps.=$myroom['mobile'].', ';
                            $tmps = trim_gks($tmps);  
                            if (strlen($tmps)>1) $tmps=substr($tmps, 0, strlen($tmps) -1);
                            $out.= $tmps;
                            //print_r($myroom);
                          }
                          
                          $out.='</span>
                      </td>
                    </tr>
                  </table>                
                </div>
              </div>';
                          
            }
          } 
          


              $out.='<div class="gks_rsrv_sum" data_rsrv_aa="'.$rsrv_aa.'" style="width:100%;display: table;">
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
                      <td style="width:25%;font-size: 110%;border:0px;padding: 8px;text-align:center;" id="gks_rsrv_total_price_'.$rsrv_aa.'">'.myCurrencyFormat($reservation['total_price'],true,true).'</td>
                    </tr>
                  </tbody></table>
                </div>

                <div class="gks_rsrv_bc2" style="width:calc(50% - 30px);float: left;margin-left: 20px;margin-bottom: 0px;padding: 8px 10px 20px 0px;font-size:100%;text-align:left;color111:black;">
                  <table cellspacing="0" cellpadding="0" border="0" style="border:0px; width:100%;border-collapse:collapse;margin:0px">
                    <tbody><tr>
                      <td id="gks_rsrv_warning_'.$rsrv_aa.'" class="gks_rsrv_basket_visitor">';
                        $text_warn='';
                        if ($reservation['total_domatia'] < $reservation['rooms']) $text_warn.='<p style="margin:0px;"><i class="gks_fas gks_fa-exclamation-triangle" style="font-size:150%;color:orange;"></i> '.gks_lang('Προσοχή: Έχετε επιλέξει λιγότερα δωμάτια από αυτά που θέλετε').'</p>';
                        if ($reservation['total_visitors'] < ($reservation['adults'] + $reservation['childs'])) $text_warn.='<p style="margin:0px;"><i class="gks_fas gks_fa-exclamation-triangle" style="font-size:150%;color:orange;"></i> '.gks_lang('Προσοχή: Τα δωμάτια που έχετε επιλέξει εξυπηρετούν λιγότερους επισκέπτες από αυτούς που θέλετε').'</p>';
                        if ($text_warn != '') {
                          $out.= '<div style="margin:0px;padding:10px 10px 10px 10px;border:1px solid ;background-color111: #feffbe;">'.$text_warn.'</div>';
                          
                        }
                      $out.='</td>
                    </tr>
                  </tbody></table>                
                </div>
              </div>
              
              
              
          </div>';
          
         
        }
        
      $out.='</div>';




      $out.='<div class="gks_box_shadow" id="gks_total_div" style="width:100%;background-color111:#f8f8f8;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;border:1px solid #dddddd;'.(count($myreservations)==0 ? 'display:none;' : '').'">
        <div style="text-transform:unset;text-align:center;margin:0px;">
          <div style="line-height: 2;font-size:120%;">
            '.gks_lang('Σύνολο κρατήσεων').': <span id="gks_total_reservations_span" style="font-weight:bold;">'.count($myreservations).'</span>
            <br>
            '.gks_lang('Σύνολο δωματίων').': <span id="gks_total_domatia_span" style="font-weight:bold;">'.$total_domatia.'</span>
            <br>
            '.gks_lang('Σύνολο διανυκτερεύσεων').': <span id="gks_total_dianiktereuseis_span" style="font-weight:bold;">'.$total_dianiktereuseis.'</span>
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
            gks_lang('Σύνολο αξίας κρατήσεων').': <span id="gks_total_price_span" style="font-weight:bold;">'.myCurrencyFormat($total_sum,true,true).'</span>
          </div>

        </div>
      </div>

    </div><!--.gks_container-->
  </div><!--.gks_body_wrapper-->
</div><!--.gks_main_content-->
';


if ($hotel_params['hotel_use_checkout_system']=='') {
$out.='
<div class="gks_main_content">
  <div class="gks_body_wrapper">
    <div id="" class="gks_container">
      <div class="x-container max width offset">
        <div class="x-main full" role="main">
            <div id="x-section-4"  class="x-section"  style="margin: 0px;padding: 0px 28px 0px 28px; background-color: transparent;"   >
                <div class="table-responsive table-2" style="display:block;overflow-x:auto;">
                <table class="table table-striped generic-table" border="0" cellspacing="0" cellpadding="0" id="table-basket" style="font-size:10pt;border: 1px solid #ddd;">
                  <thead>
                  <tr style="background-color111: #eeeeee" id="table-basket-header">
                    <th style="text-align: center !important;" nowrap="nowrap" width="0%">'.gks_lang('A/A').'</th>
                    <th style="text-align: center !important;" nowrap="nowrap" width="0%" > </th>        
                    <th style="text-align: left !important;"   nowrap="nowrap" width="85%">'.gks_lang('Περιγραφή').'</th>        
                    <th style="text-align: right !important;"  nowrap="nowrap" width="5%">'.gks_lang('Τιμή').'</th>        
                    <th style="text-align: center !important;" nowrap="nowrap" width="5%">'.gks_lang('Ποσότητα').'</th>        
                    <th style="text-align: right !important;"  nowrap="nowrap" width="5%">'.gks_lang('Σύνολο').'</th>        
                  </tr>
                  </thead>
      
      ';
      
      $script_lightgallery='';
      
      $i=0;
      foreach ($_gks_session['gks']['basket']['products'] as $index => $product) {
        foreach ($product['objects'] as $object_key => $object) {
          $i++;
          
          $row_id=$index.'_'.$product['product_id']['id_product'].'_'.$object_key;
          
          
          if ($object['type'] == 'normal') {
            $mycopies=$object['copies'];
          } else if ($object['type'] == 'simple') {
            $mycopies=0;
            foreach ($object['files'] as $file) {
              $mycopies+=$file['copies'];  
            }
          } else if ($object['type'] == 'multi') {
            $mycopies=$object['copies'];
          }
      
        
        
          $out.='<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'" id="row_root_'.$row_id.'">
            <td style="text-align: center !important;" nowrap="nowrap" class="row_aa">'.$i.'</td>
            <td style="text-align: center !important;" nowrap="nowrap">';
            if (!(isset($product['is_hotel_room_type']) and $product['is_hotel_room_type'] == 1)) { 
              $out.='<i class="rowdelete gks_fas gks_fa-trash-alt gks_basket_delete_icon" data-index="'.$index.'" data-product_id="'.$product['product_id']['id_product'].'" data-object="'.$object_key.'" ></i>';
            }
            $out.='</td>';      
             
            
            
            $out.='<td style="text-align: left !important;">';
      
               
              $out.= $product['product_id']['product_descr'];
              if ($object['type'] == 'multi') {
                $out.= ' - <i>'.$object['descr'].'</i>';
              }
              if (isset($product['product_id']['product_descr_small'])) { 
                $out.=' <span style="white-space: nowrap111;font-style: italic;font-size: 75%;">'.$product['product_id']['product_descr_small'].'</span>';
              }
      
              
              if (isset($product['product_id']['product_descr_big']) and $product['product_id']['product_descr_big'] !='') {
                $out.=' <i data-help="'.base64_encode($product['product_id']['product_descr_big']).'" aria-hidden="true"  
                  style="cursor: pointer; color111: #000000; font-size: 120%;" class="basket_product_help x-icon x-icon-question-circle"></i>';
              }
                      
              $out.='</td>';
              
              
            $out.='<td style="text-align: right !important;"   nowrap="nowrap" id="td_price_id_product_'.$row_id.'">';
              if (isset($product['product_id']['product_price_coupon_use']) and $product['product_id']['product_price_coupon_use']!='') {
                $coupons_html=' <span class="tooltipster" title="'.$product['product_id']['product_pricelist_item_descr'].'" style="text-align:left">
                <span class="coupons">'.$product['product_id']['product_price_coupon_use'].'</span></span> ';
                $out.= $coupons_html;
              }        
              
              if (abs($product['product_id']['product_pricelist_item_percent']) >= 0.01) {
              $out.='<span style="font-weight: normal;text-decoration: line-through;color:#ff0000;padding-left: 10px;">'.
                myCurrencyFormat($product['product_id']['product_price_start_peritem_total'],true,true).'</span>'; 
              }
              $out.='<span style="font-weight: bold;color111:#000000;padding-left: 10px;">'.
                myCurrencyFormat($product['product_id']['product_price_final_peritem_total'],true,true).'</span>';
            $out.='</td>        
            <td style="text-align: center !important;"   nowrap="nowrap">';
      
            
            
      
      
            if (isset($product['is_hotel_room_type']) and $product['is_hotel_room_type'] == 1) { 
              $out.='<span id="rowposotita_'.$row_id.'" style="display:inline-block;width:50px;height:28px;padding-top:6px;margin-bottom: 0px;text-align: center;vertical-align: bottom;">'. 
               $mycopies.'</span>';
            } else if ($object['type'] == 'normal' or $object['type'] == 'multi') {
              $out.='<i class="button_minus1 gks_fas gks_fa-minus-square" style="display: inline-block;font-size:26px;cursor: pointer;color:#6666a4;"'.
                  ' data-index="'.$index.'"  data-product_id="'.$product['product_id']['id_product'].'" data-object="'.$object_key.'" ></i>';


              $out.='<input type="text" class="input_rowposotita" value="'.$mycopies.'" style="width:50px;height:26px;text-align: center; position: relative;top: -7px;"'.
                ' id="input_'.$row_id.'" data-index="'.$index.'"  data-product_id="'.$product['product_id']['id_product'].'" data-object="'.$object_key.'"/>';
              
              $out.='<i class="button_plus1 gks_fas gks_fa-plus-square" style="display: inline-block;font-size:26px;cursor: pointer;color:#6666a4;"'.
                ' data-index="'.$index.'"  data-product_id="'.$product['product_id']['id_product'].'" data-object="'.$object_key.'" ></i>';
                  
              
            } else if ($object['type'] == 'simple') { 
              $out.='<span id="rowposotita_'.$row_id.'" style="display:inline-block;width:50px;height:28px;padding-top:6px;margin-bottom: 0px;text-align: center;vertical-align: bottom;">'.$mycopies.'</span>';
            } 
            
              
              
            $out.='</td>
            <td style="text-align: right !important;"   nowrap="nowrap"><span id="rowpricesum_'.$row_id.'">'.
                myCurrencyFormat($product['product_id']['product_price_final_all_total'],true,true).
              '</span>  
            </td>        
          </tr>';
          
          if ((isset($object['warnings']) and count($object['warnings']) > 0)
                    or ($object['type'] == 'multi' and $product['product_id']['product_need_multi_files']!=0)
                    or (isset($object['files']) and count($object['files']) > 0) ) {
          $out.='<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'" id="row_extra_'.$row_id.'">
            <td style="text-align: center !important;border: 0px #000000 solid;" colspan="2"><span id="warning_span_'.$row_id.'">';
              foreach ($object['warnings'] as $mywarning) {
                $out.= '<span class="tpwarning" title="'.$mywarning['tp'].'" style="text-align:left">'.$mywarning['html'].'</span>';
              } 
              $out.='</span></td>
            <td style="text-align: left !important;" colspan="4">';
              if ($object['type'] == 'multi' and $product['product_id']['product_need_multi_files']!=0) { 
                $out.='<div style="white-space: nowrap11;">';
                  if ($product['product_id']['product_need_multi_files_min']==$product['product_id']['product_need_multi_files_max']) {
                    $out.=str_replace('[1]',$product['product_id']['product_need_multi_files_min'],gks_lang('Απαιτούνται [1] φωτογραφίες'));
                  } else {
                    $tmpmsg=gks_lang('Απαιτούνται φωτογραφίες, από [1] έως [1]');
                    $tmpmsg=str_replace('[1]',$product['product_id']['product_need_multi_files_min'],$tmpmsg);
                    $tmpmsg=str_replace('[2]',$product['product_id']['product_need_multi_files_max'],$tmpmsg);
                    $out.=$tmpmsg;
                  } 
                  $tmpmsg=gks_lang('Μέχρι τώρα έχετε προσθέσει <span id="subcount_[1]">[2]</span>');
                  $tmpmsg=str_replace('[1]',$row_id,$tmpmsg);
                  $tmpmsg=str_replace('[2]',$object['subcount'],$tmpmsg);                  
                  $out.=$tmpmsg;
                $out.='</div>';
              }         
              
              $out.= '<div id="lightgallery_'.$row_id.'">';
              
              foreach ($object['files'] as $file) {
                $file_id=$row_id.'_'.$file['id'];
                  
                $out.= '<div style="float: left;width:130px;height:80px;border: 1px solid #ddd;padding:2px;margin:2px;text-align: center; overflow:hidden;" id="'.$file_id.'">';
                
                $out.= '<div class="filedelete" style="padding: 1px 0px 0px 0px;cursor: pointer;background-color: #6666a4;width: 20px;height: 20px;display: inline-block;"
                  data-index="'.$index.'" data-product_id="'.$product['product_id']['id_product'].'" data-object="'.$object_key.'" data-file="'.$file['id'].'" 
                  ><i style="color:white;" class="gks_fas gks_fa-trash-alt" aria-hidden="true"></i></div>';
                
                
                $out.= '<div class="button_file_minus1" style="padding: 1px 0px 0px 0px;cursor: pointer;background-color: #6666a4;width: 20px;height: 20px;display: inline-block;"
                  data-index="'.$index.'"  data-product_id="'.$product['product_id']['id_product'].'" data-object="'.$object_key.'" data-file="'.$file['id'].'" 
                  ><i style="color:white;" class="gks_fas gks_fa-minus-square" aria-hidden="true"></i></div>';
                
                $out.= '<input type="text" class="input_file_posotita" value="'.$file['copies'].'" style="width:26px;height:20px;margin-bottom: 0px;text-align: center; margin-top:-3px;font-size:8pt;padding:0px"
                id="input_file_'.$file_id.'" data-index="'.$index.'" data-product_id="'.$product['product_id']['id_product'].'" data-object="'.$object_key.'" data-file="'.$file['id'].'" />';
                
                $out.= '<div class="button_file_plus1" style="padding: 1px 0px 0px 0px;cursor: pointer;background-color: #6666a4;width: 20px;height: 20px;display: inline-block;"
                  data-index="'.$index.'"  data-product_id="'.$product['product_id']['id_product'].'" data-object="'.$object_key.'" data-file="'.$file['id'].'" 
                  ><i style="color:white;" class="gks_fas gks_fa-plus-square" aria-hidden="true"></i></div>
                <span id="warning_span_file_'.$file_id.'">'; 
                  foreach ($file['warnings'] as $mywarning) {
                    $out.= '<span class="tpwarning" title="'.$mywarning['tp'].'" style="text-align:left">'.$mywarning['html'].'</span>';
                  } 
                
                $out.='</span><br>';
          
        				if ($file['can_download']) {
        				    $out.='<a class="lightgalleryitem_'.$row_id.'" href="'.GKS_SITE_URL.'my/f.php?guid='.$file['id'].'&d=pre&v='.$file['version'].'" 
        				      data-download-url="'.GKS_SITE_URL.'my/f.php?download=1&guid='.$file['id'].'&d=org&v='.$file['version'].'">
        				      <img style="position: relative; top: 5px; left: 0px;max-width:60px;max-height:45px;" id="myimg" src="'.GKS_SITE_URL.'my/f.php?guid='.$file['id'].'&d=ort&v='.$file['version'].'"/></a>';
        			  } else {
        				    $out.='<a class="lightgalleryitem_'.$row_id.'" href="'.GKS_SITE_URL.'my/f.php?guid='.$file['id'].'&d=wpr&v='.$file['version'].'" 
        				      data-download-url="false">
        				      <img style="position: relative; top: 0px; left: 0px;max-width:60px;max-height:45px;" id="myimg" src="'.GKS_SITE_URL.'my/f.php?guid='.$file['id'].'&d=wth&v='.$file['version'].'" /></a>';												  
        			  }   
                  
      
                $out.='</div>';
             
              }
              $out.='<div style="clear: both;"></div>';
               
              $script_lightgallery.='  $("#lightgallery_'.$row_id.'").lightGallery({selector: ".lightgalleryitem_'.$row_id.'",thumbnail:true});'."\n\r";        
              
              
              $out.='</div>
            </td>
          </tr>'; 
          } 
        
      
     
        }
      }
      
      
                  
                $out.='</table>
                </div>
      
      
      
              </div>            
       
              <div id="x-section-5"  class="x-section"  style="margin: 0px;padding: 0px 28px 0px 28px;; background-color: transparent;"   >
              
      
                
                <div style="float: right;width:50%;min-width:350px;text-align: right !important;padding:20px 0px 0px 0px;">';
                
                
                $pliroteo = $_gks_session['gks']['basket']['products_total'] + $_gks_session['gks']['basket']['kostos_apostolis'] + $_gks_session['gks']['basket']['kostos_pliromis'];
                 
      
                  $out.='<div class="table-responsive" style="overflow-x:auto;">
                    <table  align="right"             class="table table-striped generic-table cs-ta-right" border="0" cellspacing="0" cellpadding="0" id="table-basket-total" style="font-size:10pt;width:100px;text-align: right !important;border: 1px solid #ddd;">
                      <tr id="tr_basket_products_netvalue" style="'.($pliroteo==$_gks_session['gks']['basket']['products_netvalue'] ? 'display:none;' :'').'">
                        <td style="padding: 10px 10px 10px 10px; text-align: left  !important;border-bottom: 1px solid #ddd;font-size:16pt;" nowrap="nowrap" width="0%">'.gks_lang('Σύνολο').'</td>
                        <td style="padding: 10px 10px 10px 10px; text-align: right !important;border-bottom: 1px solid #ddd;font-size:16pt;" nowrap="nowrap" width="0%" id="basket_products_netvalue">'.myCurrencyFormat($_gks_session['gks']['basket']['products_netvalue'],true,true).'</td>
                      </tr>
                      <tr id="tr_basket_products_fpa" style="'.(0==$_gks_session['gks']['basket']['products_fpa'] ? 'display:none;' :'').'">
                        <td style="padding: 10px 10px 10px 10px ; text-align: left  !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%">'.gks_lang('Φόροι').'</td>
                        <td style="padding: 10px 10px 10px 10px ; text-align: right !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%" id="basket_products_fpa">'.myCurrencyFormat($_gks_session['gks']['basket']['products_fpa'],true,true).'</td>
                      </tr>
                      <tr id="tr_basket_kostos_apostolis" style="'.(0==$_gks_session['gks']['basket']['kostos_apostolis'] ? 'display:none;' :'').'">
                        <td style="padding: 10px 10px 10px 10px ; text-align: left  !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%">'.gks_lang('Κόστος αποστολής').'</td>
                        <td style="padding: 10px 10px 10px 10px ; text-align: right !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%" id="basket_kostos_apostolis">'.myCurrencyFormat($_gks_session['gks']['basket']['kostos_apostolis'],true,true).'</td>
                      </tr>
                      <tr id="tr_basket_kostos_pliromis" style="'.(0==$_gks_session['gks']['basket']['kostos_pliromis'] ? 'display:none;' :'').'">
                        <td style="padding: 10px 10px 10px 10px ; text-align: left  !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%">'.gks_lang('Κόστος πληρωμής').'</td>
                        <td style="padding: 10px 10px 10px 10px ; text-align: right !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%" id="basket_kostos_pliromis">'.myCurrencyFormat($_gks_session['gks']['basket']['kostos_pliromis'],true,true).'</td>
                      </tr>
                      <tr>
                        <td style="padding: 10px 10px 10px 10px; text-align: left  !important;border-bottom: 1px solid #ddd;font-size:16pt;" nowrap="nowrap" width="0%">'.gks_lang('Πληρωτέο').'</td>
                        <td style="padding: 10px 10px 10px 10px; text-align: right !important;border-bottom: 1px solid #ddd;font-size:16pt;" nowrap="nowrap" width="0%" id="basket_products_total">'.myCurrencyFormat($pliroteo,true,true).'</td>
                      </tr>
        
        
                    </table>
        
                
                  </div>
                </div>
      
                <div style="float: right;width:50%;min-width:250px; font-size:10pt; padding:20px 10px 20px 0px;border: 0px solid #ddd;"> 
                  <div>'.gks_lang('Εάν έχετε Κουπόνι Έκπτωσης/Φίλου, πληκτρολογήστε το εδώ για να το προσθέσετε στο καλάθι').':</div>
                  <div>
                  <input type="text" class="input_coupon gks_input_select" value="" style="max-width:180px;text-align:left;display: inline;padding: 10px !important;" id="input_coupon" />
                  <span style="" id="coupon_use" class="gks_button fusion-button button-default button-medium button-3d">'.gks_lang('Προσθήκη Κουπονιού').'</span>
                  </div>
                  <div id="coupons_html">';

                  $coupons_html='';
                  foreach ($_gks_session['gks']['basket']['coupons'] as $key => $coupon) {
                     $coupons_html.='<span class="tooltipster coupons_span" title="'.$coupon.'" style="text-align:left;border: 1px solid gray;border-radius: 4px;padding:8px;margin-right: 6px;">
                     <span class="coupons">'.$key.' 
                     <i class="coupon_delete gks_fas gks_fa-trash-alt gks_basket_delete_icon" data-coupon="'.$key.'" style=""></i>
                     </span></span> ';
                  } 
                  if ($coupons_html!='') {
                    $coupons_html=gks_lang('Τα κουπόνια σας').': '.$coupons_html;
                    $out.= $coupons_html;
                  }
            
                  $out.='</div>
                </div>
                          
                <div style="clear: both;"></div>
      
                      
              </div>

      
            
          </div>
      
      			
      			
        </div> <!--x-main full-->
      </div><!--x-container-->

    </div><!--.gks_container-->
  </div><!--.gks_body_wrapper-->
</div><!--.gks_main_content-->
';

}


$out.='
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid avada-html-layout-boxed" id="gks_rsrv_f" style="padding-bottom111: 14px;">
  <div class="gks_col4 gks_left_center" style="padding:0px 0px 0px 0px;">';
   if ($GKS_HOTEL_RESERVATIONS_ONLINE) {
$out.='
    <button id="gks_search" class="gks_button fusion-button button-default button-medium button-3d">
      <span class=""><i class="gks_fa gks_fa-angle-left"></i></span>
      <span class="">'.gks_lang('Νέα κράτηση').'</span>
    </button>';
    } else {
$out.='
    <button id="gks_goto_homepage" class="gks_button fusion-button button-default button-medium button-3d">
      <span class=""><i class="gks_fa gks_fa-angle-left"></i></span>
      <span class="">'.gks_lang('Αρχική Σελίδα').'</span>
    </button>';      
    }
$out.='
  </div>
  <div class="gks_col4" style="text-align: center !important;padding:0px 0px 0px 0px;">   
    <button id="gks_update" class="gks_button fusion-button button-default button-medium button-3d" style="">
      <span class=""><i class="gks_fas gks_fa-save"></i></span>
      <span class="">'.gks_lang('Ενημέρωση').'</span>
    </button> 
    <img id="gks_loading_roll" src="'.GKS_SITE_URL.'my/img/Rolling-1s-38px.gif" border="0" style="display:none;margin-bottom: 0px;">           
  </div>
  <div class="gks_col4 gks_right_center" style="padding:0px 0px 0px 0px;">   
    <button id="gks_checkout" class="gks_button fusion-button button-default button-medium button-3d" style="">
      <span class="">'.gks_lang('Επόμενο βήμα').'</span>
      <span class=""><i class="gks_fa gks_fa-angle-right"></i></span>
    </button>        
  </div>
  <div style="clear: both;"></div>
</div> 
';

$out.='
<div id="gks_dialog_message" title="'.$hotel_title.'" style="display: none;">
  <table style="width:100%" cellpadding="10">
    <tr>
      <td style="width:1%;vertical-align:top">
        <i id="gks_dialog_message_ok"    class="gks_fa gks_fa-check-circle" style = "color: #00e220;font-size: 500%;"></i>
        <i id="gks_dialog_message_error" class="gks_fa gks_fa-exclamation-triangle" style = "color: #cb0000;font-size: 500%;"></i>
      </td>
      <td style="width:99%;vertical-align:top;padding-top:20px;line-height:1;">
        <span id="gks_dialog_message_message" style="font-size:16px;line-height:1;"></span>
      </td>
    </tr> 
  </table>
</div>
<div id="gks_dialog_big_message" title="'.$hotel_title.'" style="display: none;">
  <span id="gks_dialog_big_message_message"></span>
</div>
<div id="gks_dialog_confirm" title="'.$hotel_title.'" style="display: none;">
  <table style="width:100%" cellpadding="10">
    <tr>
      <td style="width:1%;vertical-align:top">
        <i class="gks_fas gks_fa-question-circle" style = "color: #dca327;font-size: 500%;"></i>
        
      </td>
      <td style="width:99%;vertical-align:top;padding-top:20px;line-height:1;">
        <span id="gks_dialog_confirm_message" style="font-size:16px;line-height:1;"></span>
      </td>
    </tr> 
  </table>  
</div>

<div id="gks_dialog_visitor_details" title="'.$hotel_title.'" style="display: none;min-width:100%;width:100%;border:0px solid red;">
  <h2 align="center" style="padding-top:0px;">'.gks_lang('Επισκέπτης').'</h2>
  <div style="width:100%;display: table;" class="">
    <div class="gks_row">
      <div class="gks_col6"><label class="gks_label" for="dr_user_first_name">'.gks_lang('Όνομα').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_first_name" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>
    <div class="gks_row">
      <div class="gks_col6"><label class="gks_label" for="dr_user_last_name">'.gks_lang('Επώνυμο').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_last_name" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>
    <div class="gks_row">
      <div class="gks_col6"><label class="gks_label" for="dr_user_email">'.gks_lang('email').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_email" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>
    <div class="gks_row">
      <div class="gks_col6"><label class="gks_label" for="dr_user_mobile">'.gks_lang('Κινητό Τηλέφωνο').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_mobile" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>

    <div class="gks_row" id="dr_div_customer_more_show">
      <div class="gks_col6"></div>
      <div class="gks_col6"><span id="dr_customer_more_show" style="color:#007bff;text-decoration: underline;cursor:pointer;">'.gks_lang('Περισσότερα').'...</span></div>
    </div>
    

        
    <div class="gks_row dr_divs_customer_more">
      <div class="gks_col6"><label class="gks_label" for="dr_user_lang">'.gks_lang('Γλώσσα').':</label></div>
      <div class="gks_col6"><select id="dr_user_lang" class="gks_input_select gks_input_select_dialog">
        <option value=""></option>
';
        $json_langs_list=array();
        $sql="select id_lang,lang_name,lang_ico FROM gks_lang order by lang_sortorder,lang_name ";
        $result_select = $db_link->query($sql);        
        if (!$result_select) {
          debug_mail(false,'error sql',$sql);
          die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        }
        while ($row_select = $result_select->fetch_assoc()) {
          $json_langs_list[$row_select['id_lang']]=$row_select;
          $out.= '<option value="'.$row_select['id_lang'].'" ';
          $out.= '>'.$row_select['lang_name'].'</option>';
        }
$out.='
      </select></div>
    </div>
    <div class="gks_row dr_divs_customer_more">
      <div class="gks_col6"><label class="gks_label" for="dr_user_ma_odos">'.gks_lang('Οδός').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_ma_odos" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>
    <div class="gks_row dr_divs_customer_more">
      <div class="gks_col6"><label class="gks_label" for="dr_user_ma_arithmos">'.gks_lang('Αριθμός').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_ma_arithmos" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>
    <div class="gks_row dr_divs_customer_more">
      <div class="gks_col6"><label class="gks_label" for="dr_user_ma_orofos">'.gks_lang('Όροφος').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_ma_orofos" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>
    <div class="gks_row dr_divs_customer_more">
      <div class="gks_col6"><label class="gks_label" for="dr_user_ma_perioxi">'.gks_lang('Περιοχή').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_ma_perioxi" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>
    <div class="gks_row dr_divs_customer_more">
      <div class="gks_col6"><label class="gks_label" for="dr_user_ma_poli">'.gks_lang('Πόλη').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_ma_poli" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>
    <div class="gks_row dr_divs_customer_more">
      <div class="gks_col6"><label class="gks_label" for="dr_user_ma_tk">'.gks_lang('TK').':</label></div>
      <div class="gks_col6"><input class="gks_input_text gks_input_text_dialog gks_input_text_sm" id="dr_user_ma_tk" type="text" value="" autocomplete="'.$autocomplete_gks_disable.'"></div>
    </div>
    <div class="gks_row dr_divs_customer_more">
      <div class="gks_col6"><label class="gks_label" for="dr_user_ma_country_id">'.gks_lang('Χώρα').':</label></div>
      <div class="gks_col6"><select id="dr_user_ma_country_id" class="gks_input_select gks_input_select_dialog">
        <option value="0"></option>
';

        $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
        gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));


        $json_country_list=array();
        $sql="select id_country,".gks_lang_sql_field('country_name',$lang_prepare_gks_country).",country_initials 
        FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
        ".$lang_prepare_gks_country['sql']['from2']."
        where country_ISO_3166_1>0
        ORDER BY ".gks_lang_sql_field('country_name',$lang_prepare_gks_country,'',true);
        $result_select = $db_link->query($sql);        
        if (!$result_select) {
          debug_mail(false,'admin-users-item.php error sql',$sql);
          die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        }
        while ($row_select = $result_select->fetch_assoc()) {
          $json_country_list[$row_select['id_country']]=$row_select;
          $out.= '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" ';
          $out.= '>'.$row_select['country_name'].'</option>';
        }
$out.='
      </select></div>
    </div>
    <div class="gks_row dr_divs_customer_more">
      <div class="gks_col6"><label class="gks_label" for="dr_user_ma_nomos_id">'.gks_lang('Νομός').':</label></div>
      <div class="gks_col6"><select id="dr_user_ma_nomos_id" class="gks_input_select gks_input_select_dialog"><option value="0"></option></select> 
      </div>
    </div>

    <div class="gks_row" id="dr_div_customer_more_hide">
      <div class="gks_col6"></div>
      <div class="gks_col6"><span id="dr_customer_more_hide" style="color:#007bff;text-decoration: underline;cursor:pointer;">'.gks_lang('Λιγότερα').'...</span></div>
    </div>
  </div>
</div>
';


//if (defined('ICL_LANGUAGE_CODE')) {
//  $out.= 'ICL_LANGUAGE_CODE: '.ICL_LANGUAGE_CODE;
//}
//$out.= defined('ICL_LANGUAGE_CODE');
//$out.= apply_filters( 'wpml_current_language', NULL );
//$out.=gks_set_lang_url();


$out.='
<script type="text/javascript">



var json_rooms_list=[];
';

foreach ($myreservations as $key => $value) {
  $out.= 'json_rooms_list['.$key.']='.json_encode($value).';'."\r\n";
}

$out.=' 
var products_total_val='.number_format($_gks_session['gks']['basket']['products_total'] + $_gks_session['gks']['basket']['kostos_apostolis'] + $_gks_session['gks']['basket']['kostos_pliromis'],2,'.','').';
var products_posotita_val='.number_format($_gks_session['gks']['basket']['products_posotita'],2,'.','').';

';



$out.=' 
var from_php_minDate ="'.showDate($today_vardia_time + GKS_ERP_START_VARDIA*60*60, 'Y-m-d', 1).'";
var from_php_max_reservation_date_time ='.$defs['max_reservation_date_time'].';
var from_php_max_reservation_date_time_date1 = "'.date('Y-m-d',$defs['max_reservation_date_time']).'";
var from_php_max_reservation_date_time_date2 = "'.date('Y-m-d',$defs['max_reservation_date_time'] + 24*60*60).'";
var from_php_defs_inh = '.$defs['inh'].';
var from_php_defs_outh = '.$defs['outh'].';
var from_php_gks_set_lang_url="'.gks_set_lang_url().'";



'.from_php_global_vars_echo('jQuery3').'

var from_php_lang_OK="'.gks_lang('OK').'";
var from_php_lang_Cancel="'.gks_lang('Άκυρο').'";
var from_php_lang_ErrorPleasetryagainlater="'.gks_lang('Σφάλμα').': '.gks_lang('Παρακαλώ δοκιμάστε αργότερα').'";
var from_php_lang_Surelyyouwanttodeletetheroom="'.gks_lang('Σίγουρα θέλετε να διαγράψετε το δωμάτιο;').'";
var from_php_lang_Surelyyouwanttoremovetheproductfromyourcart="'.gks_lang('Σίγουρα θέλετε να θέλετε να αφαιρέσετε το προϊόν από το καλάθι σας;').'";
var from_php_lang_Surelyyouwanttoremovethefilefromthatproduct="'.gks_lang('Σίγουρα θέλετε να θέλετε να αφαιρέσετε το αρχείο από το συγκεκριμένο προϊόν;').'";
var from_php_lang_Typeyourcouponfirstinthetextbox="'.gks_lang('Πληκτρολογήστε πρώτα το κουπόνι σας στο πλαίσιο κειμένου').'";
var from_php_lang_Yourcartisempty="'.gks_lang('Το καλάθι σας είναι άδειο').'";
var from_php_lang_Successfullyaddedtocart="'.gks_lang('Επιτυχής προσθήκη στο καλάθι').'";
var from_php_lang_Therearewarnings="'.gks_lang('Υπάρχουν <b>[1]</b> προειδοποιήσεις.<br>Σίγουρα θέλετε να συνεχίσετε;').'";

var from_php_gks_api_hotel_page_reservation_search=\''.$input_data['gks_api_hotel_page_reservation_search'][$url_lang].'\';
var from_php_gks_api_hotel_page_reservation_basket=\''.$input_data['gks_api_hotel_page_reservation_basket'][$url_lang].'\';
var from_php_gks_api_page_checkout=\''.$input_data['gks_api_page_checkout'][$url_lang].'\';
var from_php_gks_api_page_payment=\''.$input_data['gks_api_page_payment'].'\';
var from_php_gks_api_page_confirm=\''.$input_data['gks_api_page_confirm'].'\';

var from_php_url_lang=\''.$url_lang.'\';
var from_php_ui_lang=\''.$_gks_session['gks']['ui_lang'].'\';

var from_php_gks_erp_cookie_id=\''.$gks_erp_cookie_id.'\';



</script>

<script src="'.GKS_SITE_URL.'my/js/basket.js?v='.$gks_cache_version.'"></script>


';



  
//  $out.='ABSPATH: '.ABSPATH.'<br>';
//  $out.='time: '.time().'<br>';
//  $out.='Athens time: '.showDate(time(),'d/m/Y H:i:s',1).'<br>';
//  $out.='path: '.getcwd().'<br>';
//  $out.='$hotel_title: '.$hotel_title.'<br>';
//  $out.='gks_hotel_gkIP: '.$gkIP.'<br>';
//  $out.='gks_hotel_my_wp_user_id: '.$my_wp_user_id.'<br>';
//  $out.='db_test: '.db_test().'<br>';
//  
//
//  $out.='<pre>';
////  $out.='gks_hotel_my_wp_user_info: '.print_r($my_wp_user_info, true)."\r\n";
//  $out.='GKS_ALPHABANK_REAL_URL: '.$GKS_ALPHABANK_REAL_URL."\r\n";
////  $out.='SESSION: '.print_r($_gks_session,true);
//  $out.='defs: '.print_r($defs,true);
//  $out.='reservations: '.print_r($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'],true);
//  $out.='</pre>';
  
  



      
  return '<div id="gks_hotel_container">'.$out.'</div>';
  
}

