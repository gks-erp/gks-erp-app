<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_hotel_cmd_hotel_reservation($id_hotel,$row_hotel,$input_data) {
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
  
  global $autocomplete_gks_disable;
  
  $gks_erp_cookie_id='';
  if(isset($input_data['gks_erp_cookie_id'])) {
    $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
  }
  $hotel_title=$row_hotel['hotel_title'];
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  gks_erp_cookie_start($gks_erp_cookie_id);
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
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

  $myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
  $elems=array(); $total_sum=0; $total_visitors=0; $total_dianiktereuseis=0; $total_domatia=0;
  hotel_basket_rsrv_calc($id_hotel,$myreservations, $elems, $total_sum, $total_visitors, $total_dianiktereuseis, $total_domatia, true);
  $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'] = $myreservations;
  gks_erp_cookie_save($gks_erp_cookie_id);
  
  
  
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
  

  $child_age_price_ap_array=array();
  for($ia=0; $ia<=$hotel_params['hotel_child_accept_max_age']; $ia++) {
    if ($ia < $hotel_params['hotel_child_accept_above_age']) {
      $child_age_price_ap_array[$ia]='';
    } else {
      $foundprice=gks_lang('ως ενήλικας');
      foreach ($hotel_params['hotel_child_age_price'] as $valia) {
        if ($ia >= $valia['from'] and $ia <= $valia['to']) {
          if ($valia['price']==0) $foundprice=gks_lang('Δωρεάν');
          else {
            $foundprice=myCurrencyFormat($valia['price']);
            if ($valia['type']=='night') $foundprice.= ' / '.gks_lang('Βράδυ');
            else if ($valia['type']=='stay') $foundprice.= ' / '.gks_lang('Κράτηση');
          }
          break;
        }
      } 
      $child_age_price_ap_array[$ia] = $ia.' '.gks_lang('ετών'); // ('.$foundprice.')';
    }
  }
  

  $out='';
  //$out.='|sssssssss|'.$gks_erp_cookie_id.'|dddddd|';

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


<link href="'.GKS_SITE_URL.'my/js/light/gallery/css/lightgallery.min.css" rel="stylesheet">
<script src="'.GKS_SITE_URL.'my/js/light/gallery/lib/picturefill.min.js"></script>
<script src="'.GKS_SITE_URL.'my/js/light/gallery/js/lightgallery-all.min.js"></script>
<script src="'.GKS_SITE_URL.'my/js/light/gallery/lib/jquery.mousewheel.min.js"></script>


<script language="javascript" type="text/javascript">
    var jQuery3=jQuery;
    window.jQuery =originalJQuery;
    window.$ = originalJQuerySign;
</script>

<div class="gks_main_content">
<div class="gks_body_wrapper">

  <div>
    <div id="gks_container" class="">
      <div class="gks_row">
        <h1 class="page-title" style="text-transform:unset;text-align:center;">'.gks_lang('Νέα Κράτηση').'</h1>
      </div>
      <div class="gks_row" id="gks_content">
        <article id="post-reservation" class="post-reservation page type-page status-publish hentry">
          <div class="entry-content">
            <div class="gks_box_shadow" id="gks_rsrv_s" style="width:30%;float: left;border:1px solid;background-color111:#d2eeff;border-radius: 20px;padding: 24px;margin-left: 10px;margin-bottom: 24px;">
              <h2 style="text-transform:unset;text-align:center;">'.gks_lang('Αναζήτηση').'</h2>
              <div class="gks_label_search">'.gks_lang('Ημερομηνία άφιξης').':</div>
              <div style="text-align:center;"><input class="gks_input_text" type="text" style="width:100%;max-width:320px;text-align:center;" id="gks_check_in" name="gks_check_in" value="'.showDate($today_vardia_time, 'd/m/Y', 1).'" autocomplete="'.$autocomplete_gks_disable.'"></div>
              <div class="gks_label_search">'.gks_lang('Ημερομηνία αναχώρησης').':</div>
              <div style="text-align:center;"><input class="gks_input_text" type="text" style="width:100%;max-width:320px;text-align:center;" id="gks_check_out" name="gks_check_out" value="'.showDate($today_vardia_time + 24*60*60, 'd/m/Y', 1).'" autocomplete="'.$autocomplete_gks_disable.'"> </div>
              <div style="text-align:center;">'.gks_lang('Διανυκτερεύσεις').': <span id="gks_num_days">1</span></div>
              <div class="gks_label_search">'.gks_lang('Ενήλικες').':</div>
              <div style="text-align:center;"><select class="gks_input_select" id="gks_adults_count" style="width:100%;max-width:320px;" class="wpcf7-form-control1111 wpcf7-select1111">';
  
              $sql="SELECT Sum(gks_hotel_room_type.room_type_visitors) AS cc
              FROM gks_hotel_room LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type
              WHERE gks_hotel_room.room_status='available' AND gks_hotel_room_type.room_type_status='available'";
              $result = $db_link->query($sql);
              if (!$result) {debug_mail(false,'error sql',$sql);$out.= 'sql error'; die();}    
              $row = $result->fetch_assoc();
              for ($i=1; $i<=$row['cc']; $i++) {
                $out.= '<option value="'.$i.'" '.($i==2 ? ' selected ' : '').'>'.$i .' '.($i==1 ? gks_lang('Ενήλικας'):  gks_lang('Ενήλικες')).'</option>';  
              }
      $out.='</select></div>';
      
      if ($hotel_params['hotel_child_accept']) {
        $out.='<div class="gks_label_search">'.gks_lang('Παιδιά').':</div>
                <div style="text-align:center;"><select class="gks_input_select" id="gks_childs_count" style="width:100%;max-width:320px;" class="wpcf7-form-control1111 wpcf7-select1111">
                  <option value="0">'.gks_lang('Κανένα παιδί').'</option>';
        for ($i=1; $i<=intval($row['cc']); $i++) {
          $out.= '<option value="'.$i.'">'.$i .' '.($i==1 ? gks_lang('Παιδί'): gks_lang('Παιδιά')).'</option>';  
        }
        $out.= '</select></div>';
        $out.= '<div id="childs_ages_list_main_div"></div>';
        if ($hotel_params['hotel_child_accept_max_age']<17) {
          $out.= '<div id="elem_GKS_HOTEL_CHILD_ACCEPT_MAX_AGE">'.
          str_replace(
          '[1]',($hotel_params['hotel_child_accept_max_age'] + 1),
          gks_lang('Τα παιδιά [1] ετών ή μεγαλύτερα υπολογίστε τα ως ενήλικες')).
          '</div>';
        }
      } else {
        $out.='<div class="gks_label_search" style="font-size:80%">'.gks_lang('Δεν επιτρέπονται τα παιδιά').'</div>';
      }
    $out.= '<div class="gks_label_search">'.gks_lang('Δωμάτια').':</div>
              <div style="text-align:center;"><select class="gks_input_select" id="gks_rooms_count" style="width:100%;max-width:320px;" class="wpcf7-form-control1111 wpcf7-select1111">';
              $sql="SELECT Count(id_hotel_room) AS cc FROM gks_hotel_room WHERE room_status='available'";
              $result = $db_link->query($sql);
              if (!$result) {debug_mail(false,'error sql',$sql);echo 'sql error'; die();}    
              $row = $result->fetch_assoc();
              for ($i=1; $i<=$row['cc']; $i++) {
                $out.= '<option value="'.$i.'">'.$i .' '.($i==1 ? gks_lang('Δωμάτιο'): gks_lang('Δωμάτια')).'</option>';  
              }
      $out.= '</select></div>';
              
      $out.= '<div style="text-align:center;padding-top:24px;">
                <button id="gks_mysearch" class="gks_button fusion-button button-default button-medium button-3d">
                  <span class="">'.gks_lang('Αναζήτηση').'</span>
                  <span class=""><i class="gks_fa gks_fa-angle-right"></i></span>
                </button>  
              </div>
            </div>
            <div id="gks_rsrv_r" style="width:calc(70% - 30px);float: left;margin-left: 10px;">
              <div class="gks_box_shadow" id="gks_rsrv_rc" style="border:1px solid;background-color111:#d1ffd1;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;">
                <h2 style="text-transform:unset;text-align:center;">'.gks_lang('Αποτελέσματα αναζήτησης').'</h2>
                <div id="gks_search_results"></div>
              </div>

            </div>
          
            <div class="gks_dfn"></div>
          </div><!-- .entry-content -->
        </article><!-- #post-## -->
      </div><!-- .row -->
    </div><!--.content-wrapper-->
  </div>
  
  
              <div id="gks_rsrv_f_pos"></div>
              <div style="padding:10px">
                <div class="container-fluid avada-html-layout-boxed" id="gks_rsrv_f" style="padding: 10px;margin-bottom: 10px;">
                  <div style="text-transform:unset;text-align:center;">'.gks_lang('Σύνοψη των επιλογών μου').'</div>
                  <div id="gks_selections">
                    <div class="gks_selections_col" style="float:left;">
                    
                      <div style="float:left;width:66%;padding-bottom: 6px;font-size:100%;text-align:center;">'.gks_lang('Δωμάτια').': <span id="gks_total_rooms">--</span></div>
                      <div style="float:left;width:34%;padding-bottom: 6px;"><div id="gks_pbar_rooms" style="height: 30px;"></div></div>
                      <div class="gks_dfn"></div>
                    </div>
                    <div class="gks_selections_col" style="float:left;">
                    
                    
                      <div style="float:left;width:66%;padding-bottom: 6px;font-size:100%;text-align:center;">'.gks_lang('Επισκέπτες').': <span id="gks_total_persons">--</span></div>
                      <div style="float:left;width:34%;padding-bottom: 6px;"><div id="gks_pbar_persons" style="height: 30px;"></div></div>
                       <div class="gks_dfn"></div>
                    </div>
                    <div class="gks_selections_col" style="float:left;">
                      
                      
                      <div style="float:left;width:100%;padding-bottom: 6px;font-size:100%;text-align:center;">'.gks_lang('Σύνολο').': <span id="gks_total_price" style="font-size: 100%;font-weight: bold;">--</span></div>

                      <div class="gks_dfn"></div>

                    </div>
                    <div class="gks_selections_col" style="float:left;">

                      
                      <div style="float:left;width:100%;text-align: right;">
                        <button id="gks_book" class="gks_button fusion-button button-default button-medium button-3d" style="background-color111:#888888;color111:#999999;cursor111:default;">
                          <span class="">'.gks_lang('Επόμενο').'</span>
                          <span class=""><i class="gks_fa gks_fa-angle-right"></i></span>
                        </button>
                      </div>
                      <div class="gks_dfn"></div>
                    
                    </div>
                  </div>
                  <div class="gks_dfn"></div>
                
                </div>
              </div>
              <div class="gks_dfn"></div>
              

  
  
</div><!--.body-wrapper-->
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
</div>';


$out.='
<script type="text/javascript">


  
var from_php_minDate ="'.showDate($today_vardia_time + GKS_ERP_START_VARDIA*60*60, 'Y-m-d', 1).'";
var from_php_max_reservation_date_time ='.$defs['max_reservation_date_time'].';
var from_php_max_reservation_date_time_date1 = "'.date('Y-m-d',$defs['max_reservation_date_time']).'";
var from_php_max_reservation_date_time_date2 = "'.date('Y-m-d',$defs['max_reservation_date_time'] + 24*60*60).'";
var from_php_defs_inh = '.$defs['inh'].';
var from_php_defs_outh = '.$defs['outh'].';

'.from_php_global_vars_echo('jQuery3').'


var child_age_price_ap_array = JSON.parse(\''.json_encode($child_age_price_ap_array).'\');
//console.log(child_age_price_ap_array);
var from_php_hotel_child_accept_max_age='.$hotel_params['hotel_child_accept_max_age'].';
var from_php_hotel_child_kounies_array = JSON.parse(\''.json_encode($hotel_params['hotel_child_kounies']).'\');
var from_php_hotel_extra_beds_array = JSON.parse(\''.json_encode($hotel_params['hotel_extra_beds']).'\');




var from_php_gks_api_hotel_page_reservation_search=\''.$input_data['gks_api_hotel_page_reservation_search'][$url_lang].'\';
var from_php_gks_api_hotel_page_reservation_basket=\''.$input_data['gks_api_hotel_page_reservation_basket'][$url_lang].'\';
var from_php_gks_api_page_checkout=\''.$input_data['gks_api_page_checkout'][$url_lang].'\';
var from_php_gks_api_page_payment=\''.$input_data['gks_api_page_payment'].'\';
var from_php_gks_api_page_confirm=\''.$input_data['gks_api_page_confirm'].'\';

var from_php_datetimepicker_locale=\''.gks_datetimepicker_locale($url_lang).'\';
var from_php_ui_lang=\''.$_gks_session['gks']['ui_lang'].'\';

var from_php_textcancel=jQuery3.base64.decode(\''.base64_encode(gks_lang('Άκυρο')).'\');
var from_php_text1=jQuery3.base64.decode(\''.base64_encode(gks_lang('Επιλέξτε την ηλικία του [1]ου παιδιού')).'\');
var from_php_text2=jQuery3.base64.decode(\''.base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Παρακαλώ δοκιμάστε αργότερα')).'\');
var from_php_text3=jQuery3.base64.decode(\''.base64_encode(gks_lang('Δεν βρέθηκαν δωμάτια.<br>Κάντε μια νέα αναζήτηση')).'\');
var from_php_text4=jQuery3.base64.decode(\''.base64_encode(gks_lang('Επιλέξτε κάποιο δωμάτιο')).'\');
var from_php_text5=jQuery3.base64.decode(\''.base64_encode(gks_lang('Έχετε επιλέξει διαφορετικό πλήθος δωματίων από το επιθυμητό:<br><b>Θέλετε: [1]<br>Επιλέξατε: [2]</b><br>Θέλετε να συνεχίσετε;')).'\');
var from_php_text6=jQuery3.base64.decode(\''.base64_encode(gks_lang('Τα δωμάτια που έχετε επιλέξει καλύπτουν διαφορετικό πλήθος επισκεπτών από το επιθυμητό:<br><b>Θέλετε: [1]<br>Επιλέξατε: [2]</b><br>Θέλετε να συνεχίσετε;')).'\');
var from_php_text7=jQuery3.base64.decode(\''.base64_encode(gks_lang('Στον τύπο δωματίου <b>[1]</b>, στο <b>[2]ο</b> δωμάτιο, επιλέξτε την ηλικία του <b>[3]ου</b> παιδιού')).'\');
var from_php_text8=jQuery3.base64.decode(\''.base64_encode(gks_lang('Παιδιά')).'\');
var from_php_text9=jQuery3.base64.decode(\''.base64_encode(gks_lang('Βρεφικά κρεβάτια')).'\');
var from_php_text10=jQuery3.base64.decode(\''.base64_encode(gks_lang('Επιπλέον κρεβάτια')).'\');
var from_php_text11=jQuery3.base64.decode(\''.base64_encode(gks_lang('Τιμή')).'\');
var from_php_text12=jQuery3.base64.decode(\''.base64_encode(gks_lang('Ηλικία [1]ου παιδιού')).'\');
var from_php_text13=jQuery3.base64.decode(\''.base64_encode(gks_lang('ετών')).'\');
var from_php_text14=jQuery3.base64.decode(\''.base64_encode(gks_lang('[1]ο παιδί')).'\');





</script>

<script src="'.GKS_SITE_URL.'my/js/reservation.js?v='.$gks_cache_version.'"></script>

';  

      
  return '<div id="gks_hotel_container">'.$out.'</div>';
  
}

