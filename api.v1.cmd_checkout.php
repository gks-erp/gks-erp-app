<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_cmd_checkout($id_hotel,$row_hotel,$input_data) {
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
  
  $defs = get_def_check($id_hotel);
  $hotel_params=gks_hotel_get_params($id_hotel);
  //global $gks_lang_array;
  
  //return $input_data['shortcode_attributes']['lang'].'|'.$_gks_session['gks']['ui_lang'].print_r($_gks_session);


  if ($_gks_session['gks']['basket']['user']['lang']=='') $_gks_session['gks']['basket']['user']['lang']=$_gks_session['gks']['ui_lang'];
  if ($_gks_session['gks']['basket']['user_other']['lang']=='') $_gks_session['gks']['basket']['user_other']['lang']=$_gks_session['gks']['ui_lang'];

  
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  $my_wp_user_id=0; //get_current_user_id();
  //$my_wp_user_info=wp_get_current_user();
  
  //if (defined('ICL_LANGUAGE_CODE')) $_gks_session['gks']['ui_lang']=gks_lang_map_WPML_to_gks(ICL_LANGUAGE_CODE);
  //$gks_load_lang_filename = gks_load_lang('gks_core/inc_gks_checkout.php');
  //return $gks_load_lang_filename.'<pre>'.print_r($gks_lang_array,true).'</pre>';

  

  
  //debug_mail(false,'test debug mail','');

  $myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
  $elems=array(); $total_sum=0; $total_visitors=0; $total_dianiktereuseis=0; $total_domatia=0;
  hotel_basket_rsrv_calc($id_hotel,$myreservations, $elems, $total_sum, $total_visitors, $total_dianiktereuseis, $total_domatia, true);
  $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'] = $myreservations;
  gks_erp_cookie_save($gks_erp_cookie_id);

  
  $hrb_user = $_gks_session['gks']['basket']['user'];
  $hrb_user_other = $_gks_session['gks']['basket']['user_other'];  
  $check_vies = $_gks_session['gks']['basket']['check_vies'];

$lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));

$lang_prepare_gks_nomoi=gks_lang_data_obj_prepare('gks_nomoi','default');
gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomoi, array('nomos_descr')); 

//part4
$ea_j='  var extra_address = [];'."\n\r"; //extra_address_javascript
$ea_a=array(); //extra_address_array
$sql="SELECT gks_users_extra_address.*, 
".gks_lang_sql_field('country_name',$lang_prepare_gks_country).", 
".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi)."
FROM 
".$lang_prepare_gks_country['sql']['from1']."
".$lang_prepare_gks_nomoi['sql']['from1']."
(gks_users_extra_address
".$lang_prepare_gks_country['sql']['from2']."
".$lang_prepare_gks_nomoi['sql']['from2']."
 
LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
WHERE (gks_users_extra_address.user_id=".$my_wp_user_id." and gks_users_extra_address.user_id>0) ";
if (isset($_gks_session['gks']['basket']['id_object']) and $_gks_session['gks']['basket']['id_object']>0) {
  $sql.=" or (gks_users_extra_address.order_id=".$_gks_session['gks']['basket']['id_object'].")";
}
if (null !== $_gks_id_session and $_gks_id_session!='') {
  $sql.=" or (gks_users_extra_address.session_id='".$db_link->escape_string($_gks_id_session)."')";
}

$sql.=" ORDER BY gks_users_extra_address.id_users_extra_address";
//echo $sql;
//die();
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'sql error',$sql);
  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
}
while ($row = $result->fetch_assoc()) {
  $address_name=$row['ea_name'].', '.$row['ea_odos'].', '.$row['ea_perioxi'].', '.$row['ea_poli'].', '.$row['ea_tk'].', '.$row['country_name'].', '.$row['nomos_descr'];

  $address_name=str_replace(', , ', ', ', $address_name);
  $address_name=str_replace(', , ', ', ', $address_name);
  $address_name=str_replace(', , ', ', ', $address_name);
  $address_name=str_replace(', , ', ', ', $address_name);
  
  if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
  if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
  if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);

  if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
  if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
  if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);


  $item = array('id' => $row['id_users_extra_address'],
                'name' => $address_name,
                'ea_name' => isset($row['ea_name']) ? $row['ea_name']: '' ,
                'ea_phone' => isset($row['ea_phone']) ? $row['ea_phone']: '' ,
                'ea_odos' => isset($row['ea_odos']) ? $row['ea_odos']: '' ,
                'ea_orofos' => isset($row['ea_orofos']) ? $row['ea_orofos']: '' ,
                'ea_perioxi' => isset($row['ea_perioxi']) ? $row['ea_perioxi']: '' ,
                'ea_poli' => isset($row['ea_poli']) ? $row['ea_poli']: '' ,
                'ea_tk' => isset($row['ea_tk']) ? $row['ea_tk']: '' ,
                'ea_country_id' => isset($row['ea_country_id']) ? $row['ea_country_id']: 0 ,
                'country_name' => isset($row['country_name']) ? $row['country_name']: '' ,
                'ea_nomos_id' => isset($row['ea_nomos_id']) ? $row['ea_nomos_id']: 0 ,
                'nomos_descr' => isset($row['nomos_descr']) ? $row['nomos_descr']: '' ,
                );
  $ea_a[$item['id']] = $item;
  
  $ea_j.="  item = {id: ".$item['id'].",";
  $ea_j.=         "name: jQuery3.base64.decode('".base64_encode($address_name)."'),";
  $ea_j.=         "ea_name: jQuery3.base64.decode('".base64_encode($item['ea_name'])."'),";
  $ea_j.=         "ea_phone: jQuery3.base64.decode('".base64_encode($item['ea_phone'])."'),";
  $ea_j.=         "ea_odos: jQuery3.base64.decode('".base64_encode($item['ea_odos'])."'),";
  $ea_j.=         "ea_orofos: jQuery3.base64.decode('".base64_encode($item['ea_orofos'])."'),";
  $ea_j.=         "ea_perioxi: jQuery3.base64.decode('".base64_encode($item['ea_perioxi'])."'),";
  $ea_j.=         "ea_poli: jQuery3.base64.decode('".base64_encode($item['ea_poli'])."'),";
  $ea_j.=         "ea_tk: jQuery3.base64.decode('".base64_encode($item['ea_tk'])."'),";
  $ea_j.=         "ea_country_id: ".$item['ea_country_id'].",";
  $ea_j.=         "ea_nomos_id: ".$item['ea_nomos_id'].",";
  $ea_j.=         "};"."\n\r";

  $ea_j.="  extra_address[".$item['id']."]=item;"."\n\r";
  
}

  $out='';
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


    <div id="gks_container" class="">
      
      <div class="gks_row">
        <div style="margin: 0px;padding: 0px 28px 0px 28px;; background-color: transparent;"   >
          <div style="float: left;width:40%;min-width:250px; font-size:10pt; padding:20px 10px 20px 0px;border: 0px solid #ddd;"> 
            <p ><span style="font-size:24pt">'.gks_lang('Πληροφορίες χρέωσης').'<br></span></p>
          </div>
          <div style="float: left;width:60%;min-width:250px;text-align: right !important;padding:20px 0px 0px 0px;">          
            <span style="background-color: #476b14;cursor:pointer;" class="gks_basket_button" id="header_basket_show">'.gks_lang('Επισκόπηση Παραγγελίας').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: #73AD21;" class="gks_basket_button" id="header_basket_checkout">'.gks_lang('Αποστολή & Χρέωση').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: gray;" class="gks_basket_button" id="header_basket_pay">'.gks_lang('Πληρωμή').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: gray;" class="gks_basket_button" id="header_basket_confirm">'.gks_lang('Επιβεβαίωση').'</span>
          </div>
          <div style="clear: both;"></div>
        </div>
      </div><!-- .gks_row -->      
      

      <div class="gks_row">
        
          
        <div id="gks_rsrv_s" class="gks_box_shadow" style="width:70%;float: left;border:1px solid; background-color111:#d2eeff;border-radius: 20px;padding: 24px;margin-left: 10px;margin-bottom: 24px;">
          <h2 style="text-transform:unset;text-align:center;">'.gks_lang('Τα στοιχεία μου').'</h2>
          
          <div class="gks_checkout_col1">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_first_name">'.gks_lang('Όνομα').':</label></div>
            <input class="gks_input_text" id="dr_user_first_name" name="dr_user_first_name" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.    
              htmlspecialchars_gks($hrb_user['first_name']).'" autocomplete="'.$autocomplete_gks_disable.'">
          </div>
          <div class="gks_checkout_col2">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_last_name">'.gks_lang('Επώνυμο').':</label></div>
            <input class="gks_input_text" id="dr_user_last_name" name="dr_user_last_name" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
              htmlspecialchars_gks($hrb_user['last_name']).'" autocomplete="'.$autocomplete_gks_disable.'">
          </div>
          <div class="gks_dfn"></div>
          
          <div class="gks_checkout_col1">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_email">'.gks_lang('email').':</label></div>
            <input class="gks_input_text" id="dr_user_email" name="dr_user_email" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
              htmlspecialchars_gks($hrb_user['email']).'" autocomplete="'.$autocomplete_gks_disable.'">
          </div>
          <div class="gks_checkout_col2">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_mobile">'.gks_lang('Κινητό Τηλέφωνο').':</label></div>
            <input class="gks_input_text" id="dr_user_mobile" name="dr_user_mobile" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
              htmlspecialchars_gks($hrb_user['mobile']).'" autocomplete="'.$autocomplete_gks_disable.'">
          </div>
          <div class="gks_dfn"></div>';
          
  
          
          $out.='<div class="gks_checkout_col1">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_lang">'.gks_lang('Γλώσσα').':</label></div>
            <select id="dr_user_lang" name="dr_user_lang" class="gks_input_select" style="width:100%;max-width:100%;text-align:left;">
              <option value=""></option>';
              
              $json_langs_list=array();
              $sql="select id_lang,".gks_sqlfl('lang_name').",lang_ico FROM gks_lang order by lang_sortorder,lang_name ";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              }
              while ($row_select = $result_select->fetch_assoc()) {
                $json_langs_list[$row_select['id_lang']]=$row_select;
                $out.= '<option value="'.$row_select['id_lang'].'" ';
                if ($hrb_user['lang'] == $row_select['id_lang']) $out.= ' selected ';
                $out.= '>'.$row_select['lang_name'].'</option>';
              }
              
            $out.='</select>
          </div>
          <div class="gks_dfn"></div>
          
          <div class="gks_checkout_col1">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_ma_odos">'.gks_lang('Διεύθυνση').':</label></div>
            <input class="gks_input_text" id="dr_user_ma_odos" name="dr_user_ma_odos" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
              htmlspecialchars_gks($hrb_user['ma_odos']).'" autocomplete="'.$autocomplete_gks_disable.'">
          </div>
          <div class="gks_checkout_col2">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_ma_perioxi">'.gks_lang('Περιοχή').':</label></div>
            <input class="gks_input_text" id="dr_user_ma_perioxi" name="dr_user_ma_perioxi" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.   
              htmlspecialchars_gks($hrb_user['ma_perioxi']).'" autocomplete="'.$autocomplete_gks_disable.'">
          </div>
          <div class="gks_dfn"></div>              
          
          <div class="gks_checkout_col1">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_ma_poli">'.gks_lang('Πόλη').':</label></div>
            <input class="gks_input_text" id="dr_user_ma_poli" name="dr_user_ma_poli" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
              htmlspecialchars_gks($hrb_user['ma_poli']).'" autocomplete="'.$autocomplete_gks_disable.'">
          </div>
          <div class="gks_checkout_col2">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_ma_tk">'.gks_lang('TK').':</label></div>
            <input class="gks_input_text" id="dr_user_ma_tk" name="dr_user_ma_tk" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
              htmlspecialchars_gks($hrb_user['ma_tk']).'" autocomplete="'.$autocomplete_gks_disable.'">
          </div>
          <div class="gks_dfn"></div>              
          
          <div class="gks_checkout_col1">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_ma_country_id">'.gks_lang('Χώρα').':</label></div>';
            
            $ee_initials='';
            $this_select='
            <select id="dr_user_ma_country_id" name="dr_user_ma_country_id" class="gks_input_select" style="width:100%;max-width:100%;text-align:left;">
              <option value="0" data-ee="">'.gks_lang('Χώρα').'...</option>';
              
              $countrys_ea_html='';
              $json_country_list=array();
              
              $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
              gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
              
              $sql="select id_country,country_ee,".gks_lang_sql_field('country_name',$lang_prepare_gks_country).",country_initials 
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
                $this_select.= '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" data-ee="'.trim_gks($row_select['country_ee']).'"';
                if ($hrb_user['ma_country_id'] == $row_select['id_country']) {$this_select.= ' selected '; $ee_initials=trim_gks($row_select['country_ee']); }
                $this_select.= '>'.$row_select['country_name'].'</option>';
                $countrys_ea_html.= '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'">'.$row_select['country_name'].'</option>';
              }
            $this_select.='</select>';
            
            $out.=$this_select;
            
          $out.='</div>
          <div class="gks_checkout_col2">
            <div class="gks_label_search"><label class="gks_label" for="dr_user_ma_nomos_id">'.gks_lang('Νομός').':</label></div>
            <select id="dr_user_ma_nomos_id" name="dr_user_ma_nomos_id" class="gks_input_select" style="width:100%;max-width:100%;text-align:left;">
              <option value="0">'.gks_lang('Νομός').'...</option>';

              $lang_prepare_gks_nomoi=gks_lang_data_obj_prepare('gks_nomoi','default');
              gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomoi, array('nomos_descr')); 
                              
              $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi)." 
              FROM ".$lang_prepare_gks_nomoi['sql']['from1']." gks_nomoi 
              ".$lang_prepare_gks_nomoi['sql']['from2']."
              where country_id=".$hrb_user['ma_country_id']." 
              ORDER BY ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi,'',true);
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'admin-users-item.php error sql',$sql);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              }
              while ($row_select = $result_select->fetch_assoc()) {
                $out.= '<option value="'.$row_select['id_nomos'].'" ';
                if ($hrb_user['ma_nomos_id'] == $row_select['id_nomos']) $out.= ' selected ';
                $out.= '>'.$row_select['nomos_descr'].'</option>';
              }                 
            $out.='</select>
          </div>              
          <div class="gks_dfn"></div>              
          
          <h2 style="text-transform:unset;text-align:center;padding: 10px 0px 0px 10px;">'.gks_lang('Τύπος Παραστατικού').'</h2>
          <div class="" style="padding: 0px 10px 10px 10px;text-align:center;">
            <span style="white-space: nowrap;"><input type="radio" name="form_parastatiko" value="0" id="form_parastatiko_apodiji" '.($_gks_session['gks']['basket']['parastatiko'] == 0 ? ' checked ' : '').'>   <label class="gks_label" for="form_parastatiko_apodiji" style="display:inline;padding-right:18px" >'.gks_lang('Απόδειξη').'</label></span> 
            <span style="white-space: nowrap;"><input type="radio" name="form_parastatiko" value="1" id="form_parastatiko_timologio" '.($_gks_session['gks']['basket']['parastatiko'] == 1 ? ' checked ' : '').'> <label class="gks_label" for="form_parastatiko_timologio" style="display:inline">'.gks_lang('Τιμολόγιο').'</label></span>
          </div>
          <div id="div_parastatiko_timologio" '.($_gks_session['gks']['basket']['parastatiko'] == 0 ? ' style="display: none;" ' : '').'>
            <div class="gks_dfn"></div>
            <div class="gks_checkout_col1">
              <div class="gks_label_search"><label class="gks_label" for="dr_user_eponimia">'.gks_lang('Επωνυμία').':</label></div>
              <input class="gks_input_text" id="dr_user_eponimia" name="dr_user_eponimia" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                htmlspecialchars_gks($hrb_user['eponimia']).'" autocomplete="'.$autocomplete_gks_disable.'">
            </div>
            <div class="gks_checkout_col2">
              <div class="gks_label_search"><label class="gks_label" for="dr_user_title">'.gks_lang('Τίτλος').':</label></div>
              <input class="gks_input_text" id="dr_user_title" name="dr_user_title" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                htmlspecialchars_gks($hrb_user['title']).'" autocomplete="'.$autocomplete_gks_disable.'">
            </div>
            <div class="gks_dfn"></div>              
            
            <div class="gks_checkout_col1">
              <div class="gks_label_search"><label class="gks_label" for="dr_user_afm">'.gks_lang('ΑΦΜ').':</label></div>';
              
            $out.='<span id="dr_user_afm_ee_initials" style="'.($ee_initials!='' ? '' : 'display:none;').';">'.$ee_initials.'</span>';
            
            
              
            $out.='<input class="gks_input_text '.($ee_initials=='' ? '':'dr_user_afm_views').'" id="dr_user_afm" name="dr_user_afm" type="text" style="'.($ee_initials=='' ? 'width:100%;' : 'width:calc(100% - 75px);').';max-width:100%;text-align:left;" value="'.
                htmlspecialchars_gks($hrb_user['afm']).'" autocomplete="'.$autocomplete_gks_disable.'">';
            
            $views_run_img='';
            if ($_gks_session['gks']['basket']['check_vies']['run']) {
              if ($_gks_session['gks']['basket']['check_vies']['valid']) $views_run_img='<img src="'.GKS_SITE_URL.'my/img/1.png" style="width:24px;" title="'.
              ($_gks_session['gks']['basket']['check_vies']['function']=='CheckAFM_VIES' ? gks_lang('EU VIES Check: VAT number is valid') : gks_lang('Έλεγχος ΑΦΜ μέσω του gsis.gr: Είναι έκγυρο')).
              '" class="tooltipster">';
              else {
                if ($_gks_session['gks']['basket']['check_vies']['error']=='') $views_run_img='<img src="'.GKS_SITE_URL.'my/img/0.png" style="width:24px;" title="'.
                  ($_gks_session['gks']['basket']['check_vies']['function']=='CheckAFM_VIES' ? gks_lang('EU VIES Check: VAT number is not valid') : gks_lang('Έλεγχος ΑΦΜ μέσω του gsis.gr: Δεν είναι έγκυρο')).
                  '" class="tooltipster">';
                else $views_run_img='<img src="'.GKS_SITE_URL.'my/img/warning.gif" style="width:24px;" title="'.
                  ($_gks_session['gks']['basket']['check_vies']['function']=='CheckAFM_VIES' ? gks_lang('EU VIES Check error').': ' . $_gks_session['gks']['basket']['check_vies']['error'] : gks_lang('Σφάλμα κατά τον έλεγχο του ΑΦΜ μέσω του gsis.gr').': '.$_gks_session['gks']['basket']['check_vies']['error']).
                  '" class="tooltipster">';
              }
            }            
            $out.='<span id="dr_user_afm_views_run" style="'.($check_vies['run'] ? '' : 'display:none;').'">'.$views_run_img.'</span>';
                
            $out.='</div>
            <div class="gks_checkout_col2">
              <div class="gks_label_search"><label class="gks_label" for="dr_user_doy">'.gks_lang('ΔΟΥ').':</label></div>
              <input class="gks_input_text" id="dr_user_doy" name="dr_user_doy" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                htmlspecialchars_gks($hrb_user['doy']).'" autocomplete="'.$autocomplete_gks_disable.'">
            </div>
            <div class="gks_dfn"></div>              
            
            <div class="" style="padding: 0px 10px 10px 10px;text-align:left;">
              <div class="gks_label_search"><label class="gks_label" for="dr_user_epaggelma">'.gks_lang('Επιχειρηματική Δραστηριότητα').':</label></div>
              <input class="gks_input_text" id="dr_user_epaggelma" name="dr_user_epaggelma" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                htmlspecialchars_gks($hrb_user['epaggelma']).'" autocomplete="'.$autocomplete_gks_disable.'">
            </div>
            <div class="gks_dfn"></div>   
          </div>
          
          <div style="'.($total_sum==0 ? 'display:none;' : '').'">
            <h2 style="text-transform:unset;text-align:center;padding: 10px 0px 0px 10px;">'.gks_lang('Η κράτηση είναι για').'</h2>
            <div class="" style="padding: 0px 10px 10px 10px;text-align:center;">
              <span style="white-space: nowrap;"><input type="radio" name="form_reservation_other" value="0" id="form_reservation_other_0" '.
               ($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservation_other'] == 0 ? ' checked ' : '').'> <label class="gks_label" for="form_reservation_other_0" style="display:inline;padding-right:18px" >'.gks_lang('Εμένα').'</label></span> 
              <span style="white-space: nowrap;"><input type="radio" name="form_reservation_other" value="1" id="form_reservation_other_1" '.
              ($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservation_other'] == 1 ? ' checked ' : '').'> <label class="gks_label" for="form_reservation_other_1" style="display:inline">'.gks_lang('Άλλον').'</label></span>
            </div>
            <div id="div_reservation_other" '.($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservation_other'] == 0 ? ' style="display: none;" ' : '').'>
  
              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_first_name">'.gks_lang('Όνομα').':</label></div>
                <input class="gks_input_text" id="other_dr_user_first_name" name="other_dr_user_first_name" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($hrb_user_other['first_name']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_checkout_col2">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_last_name">'.gks_lang('Επώνυμο').':</label></div>
                <input class="gks_input_text" id="other_dr_user_last_name" name="other_dr_user_last_name" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($hrb_user_other['last_name']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_dfn"></div>
              
              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_email">'.gks_lang('email').':</label></div>
                <input class="gks_input_text" id="other_dr_user_email" name="other_dr_user_email" type="text" style="width:100%;max-width:100%;text-align:left;" value="'. 
                  htmlspecialchars_gks($hrb_user_other['email']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_checkout_col2">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_mobile">'.gks_lang('Κινητό Τηλέφωνο').':</label></div>
                <input class="gks_input_text" id="other_dr_user_mobile" name="other_dr_user_mobile" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($hrb_user_other['mobile']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_dfn"></div>
              
  
              
              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_lang">'.gks_lang('Γλώσσα').':</label></div>
                <select id="other_dr_user_lang" name="other_dr_user_lang" class="gks_input_select" style="width:100%;max-width:100%;text-align:left;">
                  <option value=""></option>';
                  
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
                    if ($hrb_user_other['lang'] == $row_select['id_lang']) $out.= ' selected ';
                    $out.= '>'.$row_select['lang_name'].'</option>';
                  }
                $out.='</select>
              </div>
              <div class="gks_dfn"></div>
              
              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_ma_odos">'.gks_lang('Διεύθυνση').':</label></div>
                <input class="gks_input_text" id="other_dr_user_ma_odos" name="other_dr_user_ma_odos" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($hrb_user_other['ma_odos']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_checkout_col2">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_ma_perioxi">'.gks_lang('Περιοχή').':</label></div>
                <input class="gks_input_text" id="other_dr_user_ma_perioxi" name="other_dr_user_ma_perioxi" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($hrb_user_other['ma_perioxi']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_dfn"></div>              
              
              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_ma_poli">'.gks_lang('Πόλη').':</label></div>
                <input class="gks_input_text" id="other_dr_user_ma_poli" name="other_dr_user_ma_poli" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($hrb_user_other['ma_poli']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_checkout_col2">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_ma_tk">'.gks_lang('TK').':</label></div>
                <input class="gks_input_text" id="other_dr_user_ma_tk" name="other_dr_user_ma_tk" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($hrb_user_other['ma_tk']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_dfn"></div>              
              
              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_ma_country_id">'.gks_lang('Χώρα').':</label></div>
                <select id="other_dr_user_ma_country_id" name="other_dr_user_ma_country_id" class="gks_input_select" style="width:100%;max-width:100%;text-align:left;">
                  <option value="0">'.gks_lang('Χώρα').'...</option>';
                  
                  $json_country_list=array();
                  
                  $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
                  gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
                  
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
                    if ($hrb_user_other['ma_country_id'] == $row_select['id_country']) $out.= ' selected ';
                    $out.= '>'.$row_select['country_name'].'</option>';
                  }
                  
                $out.='</select>
              </div>
              <div class="gks_checkout_col2">
                <div class="gks_label_search"><label class="gks_label" for="other_dr_user_ma_nomos_id">'.gks_lang('Νομός').':</label></div>
                <select id="other_dr_user_ma_nomos_id" name="other_dr_user_ma_nomos_id" class="gks_input_select" style="width:100%;max-width:100%;text-align:left;">
                  <option value="0">'.gks_lang('Νομός').'...</option>';

                $lang_prepare_gks_nomoi=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomoi, array('nomos_descr')); 

                  
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi)." 
                FROM ".$lang_prepare_gks_nomoi['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomoi['sql']['from2']."
                where country_id=".$hrb_user_other['ma_country_id']." 
                ORDER BY ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi,'',true);
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  $out.= '<option value="'.$row_select['id_nomos'].'" ';
                  if ($hrb_user_other['ma_nomos_id'] == $row_select['id_nomos']) $out.= ' selected ';
                  $out.= '>'.$row_select['nomos_descr'].'</option>';
                }
                $out.='</select>
              </div>              
              <div class="gks_dfn"></div> 
  
  
            </div>
          </div>
          
          <div id="div_select_apostoli" style="'.($_gks_session['gks']['basket']['products_need_apostoli']==false ? 'display:none' : '').'">
            <h2 style="text-transform:unset;text-align:center;padding: 10px 0px 0px 10px;">'.gks_lang('Αποστολή').'</h2>
            <div class="" style="padding: 0px 10px 10px 10px;">

                           
            
              <select name="form_select_apostoli" id="form_select_apostoli" class="gks_input_select" style="width:100%;max-width:100%;text-align:left;">
                <option value="-1" '.($_gks_session['gks']['basket']['address_extra']==-1 ? ' selected ' : '').'>'.gks_lang('Αποστολή στην ίδια διεύθυνση').'</option>';
                
                
                foreach ($ea_a as $ea_item) {
                   $out.= '<option value="'.$ea_item['id'].'"';
                   if ($_gks_session['gks']['basket']['address_extra'] == $ea_item['id']) $out.= ' selected ';
                   $out.= '>'.$ea_item['name'].'</option>';
                } 
                              
                $out.='<option value="0" '.($_gks_session['gks']['basket']['address_extra']==0 ? ' selected ' : '').'>'.gks_lang('-- Δημιουργία νέας διεύθυνσης --').'</option>
              </select>                
            </div> 
            
            <div id="div_extra_address" style="'.($_gks_session['gks']['basket']['address_extra']==-1 ? 'display: none;' : '').'">
              <h3 style="text-transform:unset;text-align:center;padding: 10px 0px 0px 10px;">'.gks_lang('Πληροφορίες αποστολής').'</h3>
              
              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="form_ea_name">'.gks_lang('Όνομα Παραλήπτη').':</label></div>
                <input class="gks_input_text" id="form_ea_name" name="form_ea_name" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($_gks_session['gks']['basket']['destination_data']['name']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_checkout_col2">
                <div class="gks_label_search"><label class="gks_label" for="form_ea_phone">'.gks_lang('Κινητό Τηλέφωνο').':</label></div>
                <input class="gks_input_text" id="form_ea_phone" name="form_ea_phone" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($_gks_session['gks']['basket']['destination_data']['phone']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_dfn"></div>
            
              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="form_ea_odos">'.gks_lang('Διεύθυνση').':</label></div>
                <input class="gks_input_text" id="form_ea_odos" name="form_ea_odos" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($_gks_session['gks']['basket']['destination_data']['odos']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_checkout_col2">
                <div class="gks_label_search"><label class="gks_label" for="form_ea_perioxi">'.gks_lang('Περιοχή').':</label></div>
                <input class="gks_input_text" id="form_ea_perioxi" name="form_ea_perioxi" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($_gks_session['gks']['basket']['destination_data']['perioxi']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_dfn"></div>
            
              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="form_ea_poli">'.gks_lang('Πόλη').':</label></div>
                <input class="gks_input_text" id="form_ea_poli" name="form_ea_poli" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($_gks_session['gks']['basket']['destination_data']['poli']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_checkout_col2">
                <div class="gks_label_search"><label class="gks_label" for="form_ea_tk">'.gks_lang('TK').':</label></div>
                <input class="gks_input_text" id="form_ea_tk" name="form_ea_tk" type="text" style="width:100%;max-width:100%;text-align:left;" value="'.
                  htmlspecialchars_gks($_gks_session['gks']['basket']['destination_data']['tk']).'" autocomplete="'.$autocomplete_gks_disable.'">
              </div>
              <div class="gks_dfn"></div>                                                  


              <div class="gks_checkout_col1">
                <div class="gks_label_search"><label class="gks_label" for="form_ea_country_id">'.gks_lang('Χώρα').':</label></div>
                <select name="form_ea_country_id" id="form_ea_country_id" class="gks_input_select" style="width:100%;max-width:100%;text-align:left;">
                  <option value="0">'.gks_lang('Χώρα').'...</option>
                  '.$countrys_ea_html.'                      
                </select>                    
              </div>
              <div class="gks_checkout_col2">
                <div class="gks_label_search"><label class="gks_label" for="form_ea_nomos_id">'.gks_lang('Νομός').':</label></div>
                <select name="form_ea_nomos_id" id="form_ea_nomos_id" class="gks_input_select" style="width:100%;max-width:100%;text-align:left;">
                  <option value="0">'.gks_lang('Νομός').'...</option>
                </select> 
              </div>
              <div class="gks_dfn"></div> 

            </div>
            
          </div>
          



          
          
          
          
          
          
          


          <div class="gks_dfn"></div>
        </div>
        <div id="gks_rsrv_r" style="width:calc(30% - 30px);float: left;margin-left: 10px;">
          <div class="gks_box_shadow" id="gks_rsrv_rc_rsrv" style="border:1px solid;background-color111:#d1ffd1;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;'.
            ($total_sum==0 ? 'display:none;' : '').
            '">
            <h2 style="text-transform:unset;text-align:center;">'.gks_lang('Σύνοψη Κρατήσεων').'</h2>
            
            <div style="line-height: 2;font-size:130%;text-align: center;">
              '.gks_lang('Κρατήσεις').': <span id="gks_total_reservations_span" style="font-weight:bold;">'.count($myreservations).'</span>
              <br>
              '.gks_lang('Δωμάτια').': <span id="gks_total_domatia_span" style="font-weight:bold;">'.$total_domatia.'</span>
              <br>
              '.gks_lang('Διανυκτερεύσεις').': <span id="gks_total_dianiktereuseis_span" style="font-weight:bold;">'.$total_dianiktereuseis.'</span>
              <br>
              '.gks_lang('Επισκέπτες').': <span id="gks_total_visitors_span" style="font-weight:bold;">'.$total_visitors.'</span><span style="font-weight:bold;"> x <i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i></span>
              <br>
              '.gks_lang('Ποσό').': <span id="gks_total_price_span" style="font-weight:bold;">'.myCurrencyFormat($total_sum,true, true).'</span>
            </div>                
          </div>
          <div class="gks_dfn"></div>
          <div class="gks_box_shadow" id="gks_rsrv_rc_basket" style="border:1px solid;background-color111:#d1ffd1;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;">';
          
          $pliroteo=$_gks_session['gks']['basket']['products_total'] + $_gks_session['gks']['basket']['kostos_apostolis'] + $_gks_session['gks']['basket']['kostos_pliromis'];
          
          $out.='
            <div class="table-responsive" style="overflow-x:auto;border: 0px !important;">
              <table  align="left"  class="table table-striped1 generic-table cs-ta-right" border="0" cellspacing="0" cellpadding="0" id="table-basket-total" style="font-size:10pt;width1:100px;text-align: right !important;border:0px !important;">
                <tr id="tr_basket_products_netvalue" style="border:0px !important;'.($pliroteo==$_gks_session['gks']['basket']['products_netvalue'] ? 'display:none;' :'').'">
                  <td style="padding: 10px 10px 10px 0px; text-align: left  !important;border: 0px !important;font-size:16pt;" nowrap="nowrap" width="0%">'.gks_lang('Σύνολο').'</td>
                  <td style="padding: 10px 0px 10px 0px; text-align: right !important;border: 0px !important;font-size:16pt;" nowrap="nowrap" width="0%" id="basket_products_netvalue">'.
                    myCurrencyFormat($_gks_session['gks']['basket']['products_netvalue'],true,true).'</td>
                </tr>
                <tr id="tr_basket_products_fpa" style="border:0px !important;'.(0==$_gks_session['gks']['basket']['products_fpa'] ? 'display:none;' :'').'">
                  <td style="padding: 10px 10px 10px 0px ; text-align: left  !important;border: 0px !important;" nowrap="nowrap" width="0%">'.gks_lang('Φόροι').'</td>
                  <td style="padding: 10px 0px 10px 0px ; text-align: right !important;border: 0px !important;" nowrap="nowrap" width="0%" id="basket_products_fpa">'.   
                    myCurrencyFormat($_gks_session['gks']['basket']['products_fpa'],true,true).'</td>
                </tr>
                <tr id="tr_basket_kostos_apostolis" style="border:0px !important;'.(0==$_gks_session['gks']['basket']['kostos_apostolis'] ? 'display:none;' :'').'">
                  <td style="padding: 10px 10px 10px 0px ; text-align: left  !important;border: 0px !important;" nowrap="nowrap" width="0%">'.gks_lang('Κόστος αποστολής').'</td>
                  <td style="padding: 10px 0px 10px 0px ; text-align: right !important;border: 0px !important;" nowrap="nowrap" width="0%" id="basket_kostos_apostolis">'.
                    myCurrencyFormat($_gks_session['gks']['basket']['kostos_apostolis'],true,true).'</td>
                </tr>
                <tr id="tr_basket_kostos_pliromis" style="border:0px !important;'.(0==$_gks_session['gks']['basket']['kostos_pliromis'] ? 'display:none;' :'').'">
                  <td style="padding: 10px 10px 10px 0px ; text-align: left  !important;border: 0px !important;" nowrap="nowrap" width="0%">'.gks_lang('Κόστος πληρωμής').'</td>
                  <td style="padding: 10px 0px 10px 0px ; text-align: right !important;border: 0px !important;" nowrap="nowrap" width="0%" id="basket_kostos_pliromis">'.
                    myCurrencyFormat($_gks_session['gks']['basket']['kostos_pliromis'],true,true).'</td>
                </tr>
                <tr style="border:0px !important">
                  <td style="padding: 10px 10px 10px 0px; text-align: left  !important;border: 0px !important;font-size:16pt;" nowrap="nowrap" width="0%">'.gks_lang('Πληρωτέο').'</td>
                  <td style="padding: 10px 0px 10px 0px; text-align: right !important;border: 0px !important;font-size:16pt;" nowrap="nowrap" width="0%" id="basket_products_total">'.
                    myCurrencyFormat($pliroteo,true,true).'</td>
                </tr>
  
  
              </table>

      
            </div>              
          </div>              
          
        </div>
        <div class="gks_dfn"></div>
        
      </div><!-- .gks_row -->
    </div><!--.gks_container-->

  </div><!--.gks_body_wrapper-->
</div><!--.gks_main_content-->


<div class="gks_main_content">
  <div class="gks_body_wrapper">
    <div id="" class="gks_container">      
      <div class="gks_row" style="text-align:center;padding: 0px 28px 0px 28px;">
      </div>      
      


    </div><!--.gks_container-->
  </div><!--.gks_body_wrapper-->
</div><!--.gks_main_content-->
';

$out.='
<div id="gks_rsrv_f_pos"></div>
<div style="padding:0px 10px;">
  <div class="container-fluid avada-html-layout-boxed" id="gks_rsrv_f" style="padding-bottom: 14px;">
    <div class="gks_col4 gks_left_center" style="padding:0px 0px 0px 0px;">   
      <button id="gks_basket" class="gks_button fusion-button button-default button-medium button-3d">
        <span class=""><i class="gks_fa gks_fa-angle-left"></i></span>
        <span class="">'.gks_lang('Καλάθι').'</span>
      </button>       
    </div>
    <div class="gks_col4" style="text-align: center !important;padding:0px 0px 0px 0px;">   
      <button id="gks_update" class="gks_button fusion-button button-default button-medium button-3d" style="">
        <span class=""><i class="gks_fas gks_fa-save"></i></span>
        <span class="">'.gks_lang('Ενημέρωση').'</span>
      </button> 
      <img id="gks_loading_roll" src="'.GKS_SITE_URL.'my/img/Rolling-1s-38px.gif" border="0" style="display:none;margin-bottom: 0px;">           
    </div>
    <div class="gks_col4 gks_right_center" style="padding:0px 0px 0px 0px;">   
      <button id="gks_payment" class="gks_button fusion-button button-default button-medium button-3d">
        <span class="">'.gks_lang('Επόμενο βήμα').'</span>
        <span class=""><i class="gks_fa gks_fa-angle-right"></i></span>
      </button>
    </div>
    <div style="clear: both;"></div>
  </div>
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

'.$ea_j.'

var from_php_SESSION_gks_basket_address_extra='.$_gks_session['gks']['basket']['address_extra'].';
var from_php_SESSION_gks_basket_destination_data_country_id='.intval($_gks_session['gks']['basket']['destination_data']['country_id']).';
var from_php_SESSION_gks_basket_destination_data_nomos_id='.intval($_gks_session['gks']['basket']['destination_data']['nomos_id']).';
var from_php_gks_set_lang_url="'.gks_set_lang_url().'";

var from_php_hotel_id='.$id_hotel.';
'.from_php_global_vars_echo('jQuery3').'


var from_php_lang_OK="'.gks_lang('OK').'";
var from_php_lang_Cancel="'.gks_lang('Άκυρο').'";
var from_php_lang_ErrorPleasetryagainlater="'.gks_lang('Σφάλμα').': '.gks_lang('Παρακαλώ δοκιμάστε αργότερα').'";

var from_php_gks_api_hotel_page_reservation_search=\''.$input_data['gks_api_hotel_page_reservation_search'].'\';
var from_php_gks_api_hotel_page_reservation_basket=\''.$input_data['gks_api_hotel_page_reservation_basket'].'\';
var from_php_gks_api_page_checkout=\''.$input_data['gks_api_page_checkout'].'\';
var from_php_gks_api_page_payment=\''.$input_data['gks_api_page_payment'].'\';
var from_php_gks_api_page_confirm=\''.$input_data['gks_api_page_confirm'].'\';


</script>

<script src="'.GKS_SITE_URL.'my/js/checkout.js?v='.$gks_cache_version.'"></script>

';

      
  return '<div id="gks_hotel_container">'.$out.'</div>';
  
}

