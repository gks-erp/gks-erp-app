<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_cmd_payment($id_hotel,$row_hotel,$input_data) {
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
  
  //return $input_data['shortcode_attributes']['lang'].'|'.$_gks_session['gks']['ui_lang'];


  if ($_gks_session['gks']['basket']['user']['lang']=='') $_gks_session['gks']['basket']['user']['lang']=$_gks_session['gks']['ui_lang'];
  if ($_gks_session['gks']['basket']['user_other']['lang']=='') $_gks_session['gks']['basket']['user_other']['lang']=$_gks_session['gks']['ui_lang'];

  
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  $my_wp_user_id=0; //get_current_user_id();
  //$my_wp_user_info=wp_get_current_user();
  
  //if (defined('ICL_LANGUAGE_CODE')) $_gks_session['gks']['ui_lang']=gks_lang_map_WPML_to_gks(ICL_LANGUAGE_CODE);
  //$gks_load_lang_filename = gks_load_lang('gks_core/inc_gks_checkout.php');
  //return $gks_load_lang_filename.'<pre>'.print_r($gks_lang_array,true).'</pre>';

  
   
  
  //debug_mail(false,'test debug mail','');

//  $myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
//  $elems=array(); $total_sum=0; $total_visitors=0; $total_dianiktereuseis=0; $total_domatia=0;
//  hotel_basket_rsrv_calc($myreservations, $elems, $total_sum, $total_visitors, $total_dianiktereuseis, $total_domatia, true);
//  $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'] = $myreservations;
//  gks_erp_cookie_save($gks_erp_cookie_id);

  



  if (count($_gks_session['gks']['basket']['products'])<=0 and count($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'])) {
    //header('Location: /my/'); die();
    
  }
  
  

  
  //debug_mail(false,'test debug mail','');

  unset($_gks_session['gks']['confirm']);
  
  //echo '<pre>';echo 'ggggggggggggg'.GKS_WP_TABLE_PREFIX;die();
  
  $myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
  $elems=array(); $total_sum=0; $total_visitors=0; $total_dianiktereuseis=0; $total_domatia=0;
  hotel_basket_rsrv_calc($id_hotel,$myreservations, $elems, $total_sum, $total_visitors, $total_dianiktereuseis, $total_domatia, true);
  $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'] = $myreservations;
  
  
  $_gks_session['gks']['basket']['kostos_apostolis'] = gks_calculate_kostos_apostolis($_gks_session['gks']['basket'],-1);
  $_gks_session['gks']['basket']['kostos_pliromis']  = gks_calculate_kostos_pliromis ($_gks_session['gks']['basket'],-1);
    
  //print '<pre>';
  //print_r($_gks_session['gks']['basket']['tropoi_apostolis_all']);
  //die();
  
  gks_basket_recalc($_gks_session['gks']['basket'], array(), array());
  


  $out='';
  $out.='
<script language="javascript" type="text/javascript">
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
    <div class="gks_container">
      <div class="gks_row">
        <div style="margin: 0px;padding: 0px 28px 0px 28px;; background-color: transparent;"   >
          <div style="float: left;width:40%;min-width:250px; font-size:13px; padding:20px 10px 20px 0px;border: 0px solid #ddd;"> 
            <p ><span style="font-size:24pt">'.gks_lang('Επικύρωση Παραγγελίας').'<br></span></p>
          </div>
          <div style="float: left;width:60%;min-width:250px;text-align: right !important;padding:20px 0px 0px 0px;">          
            <span style="background-color: #476b14;cursor:pointer;" class="gks_basket_button" id="header_basket_show">'.gks_lang('Επισκόπηση Παραγγελίας').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: #476b14;cursor:pointer;" class="gks_basket_button" id="header_basket_checkout">'.gks_lang('Αποστολή & Χρέωση').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: #73AD21;" class="gks_basket_button" id="header_basket_pay">'.gks_lang('Πληρωμή').'</span>
            <img src="'.GKS_SITE_URL.'my/img/gotoright.png" style="position:relative;top:-2px;">
            <span style="background-color: gray;" class="gks_basket_button" id="header_basket_confirm">'.gks_lang('Επιβεβαίωση').'</span>
          </div>
          <div style="clear: both;"></div>
        </div>
      </div>
';   

//$_gks_session['gks']['payment_error']='ggggggggg';
if (isset($_gks_session['gks']['payment_error'])) {
  debug_mail(false,'payment.php message',$_gks_session['gks']['payment_error']);

    $out.= '<div class="fusion-alert alert error alert-danger fusion-alert-center alert-dismissable fusion-animated" style="background-color:#f2dede;color:rgba(166,66,66,1);border-color:rgba(166,66,66,1);border-width:1px;" '.
    'data-animationtype="bounce" data-animationduration="0.5" data-animationoffset="100%" id="gks_alert">'.
    '<button type="button" class="close toggle-alert" data-dismiss="alert" aria-hidden="true">&times;</button>'.
    '<div class="fusion-alert-content-wrapper">'.
    '<span class="alert-icon">'.
    '<i class="fa-lg fa fa-exclamation-triangle"></i></span>'.
    '<span class="fusion-alert-content">'.$_gks_session['gks']['payment_error'].'</span>'.
    '</div>'.
    '</div>';

}
unset($_gks_session['gks']['payment_error']);


$out.='<div id="gks_rsrv_s" class="gks_box_shadow" style="width:70%;float: left;border:1px solid;background-color111:#d2eeff;border-radius: 20px;padding: 24px;margin-left: 10px;margin-bottom: 24px;">
          <h2 style="text-transform:unset;text-align:center;">'.gks_lang('Υπηρεσίες & Προϊόντα').'</h2>
                <div style="padding:10px;">
                <div class="table-responsive table-2" style="display:block;overflow-x:auto;">
                <table class="table table-striped generic-table" border="0" cellspacing="0" cellpadding="0" id="table-basket" style="font-size:13px;border: 1px solid #ddd;">
                  <thead>
                  <tr style="background-color111: #eeeeee" id="table-basket-header">
                    <th style="text-align: center !important;" nowrap="nowrap" width="0%">'.gks_lang('A/A').'</th>
                    <th style="text-align: left !important;"   nowrap="nowrap" width="85%">'.gks_lang('Περιγραφή').'</th>        
                    <th style="text-align: right !important;"  nowrap="nowrap" width="5%">'.gks_lang('Τιμή').'</th>        
                    <th style="text-align: center !important;" nowrap="nowrap" width="5%">'.gks_lang('Ποσότητα').'</th>        
                    <th style="text-align: right !important;"  nowrap="nowrap" width="5%">'.gks_lang('Σύνολο').'</th>        
                  </tr>
                  </thead>
';

      $script_lightgallery='';
      
      $i=0;
      foreach ($_gks_session['gks']['basket']['products'] as $product) {
        foreach ($product['objects'] as $object_key => $object) {
          $i++;
          
          $row_id=$product['product_id']['id_product'].'_'.$object_key;
          
          
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
            <td class="gks_tdblock row_aa" style="text-align: center !important;" nowrap="nowrap">'.$i.'</td>
             <td class="gks_tdblock" style="text-align: left !important;">';
             
              $out.= $product['product_id']['product_descr'];
              if ($object['type'] == 'multi') {
                $out.= ' - <i>'.$object['descr'].'</i>';
              }
              if (isset($product['product_id']['product_descr_small'])) { 
                $out.=' <span style="white-space: nowrap11;font-style: italic;font-size: 75%;">'.$product['product_id']['product_descr_small'].'</span>';
              
              }
      
              
              if (isset($product['product_id']['product_descr_big']) and $product['product_id']['product_descr_big'] !='') {
                $out.=' <i data-help="'.base64_encode($product['product_id']['product_descr_big']).'" aria-hidden="true" data-x-icon="&#xf059;" 
                  style="cursor: pointer; color: #000000; font-size: 120%;" class="basket_product_help x-icon x-icon-question-circle"></i>';
              }
                      
              $out.='</td>';
              
              
            $out.='<td class="gks_tdblock" style="text-align: right !important;"   nowrap11="nowrap11" id="td_price_id_product_'.$row_id.'">';
              if (isset($product['product_id']['product_price_coupon_use']) and $product['product_id']['product_price_coupon_use']!='') {
                $coupons_html=' <span class="tooltipster" title="'.$product['product_id']['product_pricelist_item_descr'].'" style="text-align:left">
                <span class="coupons">'.$product['product_id']['product_price_coupon_use'].'</span></span> ';
                $out.= $coupons_html;
              }        
              
              if (abs($product['product_id']['product_pricelist_item_percent']) >= 0.01) {
              $out.='<span style="font-weight: normal;text-decoration: line-through;color:#ff0000;padding-left: 10px;">'.
                myCurrencyFormat($product['product_id']['product_price_start_peritem_total'],true,true).'</span>';
              }
              $out.='<span style="font-weight: bold;color11:#000000;padding-left: 10px;">'.
                myCurrencyFormat($product['product_id']['product_price_final_peritem_total'],true,true).'</span>  
            </td>        
            <td class="gks_tdblock" style="text-align: center !important;"   nowrap="nowrap">
              <span id="rowposotita_'.$row_id.'" style="display:inline-block;width:50px;height:28px;padding-top:0px;margin-bottom: 0px;text-align: center;vertical-align: bottom;">'.$mycopies.'</span>
            </td>
            <td class="gks_tdblock" style="text-align: right !important;"   nowrap="nowrap"><span id="rowpricesum_'.$row_id.'">'. 
              myCurrencyFormat($product['product_id']['product_price_final_all_total'],true,true).'</span>  
            </td>        
          </tr>';
      
        }
      }
      
      
      $out.='</table>
        </div>
      </div>';  

      $table_products_varos_ogos_visible = (0==$_gks_session['gks']['basket']['products_varos'] && 0==$_gks_session['gks']['basket']['products_ogos']) == false;
      
      $out.='<div class="gks_checkout_col1" id="table_products_varos_ogos" style="'.($table_products_varos_ogos_visible ? '' : 'display:none;').'">
              <div>
                 <table    class="table1 table-striped1 generic-table11 cs-ta-right11" border="0" cellspacing="0" cellpadding="0" id="table-basket-total" style="font-size:13px;width:1%;text-align: right !important;border: 1px solid #ddd;">
                  <tr>
                    <td style="padding: 10px 10px 10px 10px; text-align: left  !important;border-bottom: 1px solid #ddd;font-size:16pt;"  width="100%" colspan="2" nowrap>'.gks_lang('Στοιχεία Αποστολής').'</td>
                  </tr>
                  <tr>
                    <td style="padding: 10px 10px 10px 10px; text-align: left  !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%">'.gks_lang('Τεμάχια').'</td>
                    <td style="padding: 10px 10px 10px 10px; text-align: right !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="100%">'.
                      number_format($_gks_session['gks']['basket']['products_posotita'],0,',','.').'</td>
                  </tr>
                  <tr>
                    <td style="padding: 10px 10px 10px 10px ; text-align: left  !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%">'.gks_lang('Βάρος').'</td>
                    <td style="padding: 10px 10px 10px 10px ; text-align: right !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="100%">'.
                      number_format($_gks_session['gks']['basket']['products_varos']/1000,2,',','.').' <span title="'.gks_lang('Κιλά').'">'.gks_lang('Kgr').'</span></td>
                  </tr>
                  <tr>
                    <td style="padding: 10px 10px 10px 10px ; text-align: left  !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%">'.gks_lang('Όγκος').'</td>
                    <td style="padding: 10px 10px 10px 10px ; text-align: right !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="100%">'.
                      number_format($_gks_session['gks']['basket']['products_ogos']/1000,2,',','.').' <span title="'.gks_lang('Λίτρα').'">'.gks_lang('L').'</span></td>
                  </tr>
                </table>
              </div>
          </div>
          <div class="gks_checkout_col2">';
          
          $pliroteo = $_gks_session['gks']['basket']['products_total'] + $_gks_session['gks']['basket']['kostos_apostolis'] + $_gks_session['gks']['basket']['kostos_pliromis'];

          $out.='<table  align="left"  class="table1 table-striped1 generic-table1 cs-ta-right1" border="0" cellspacing="0" cellpadding="0" id="table-basket-total" style="font-size:13px;width:100px;text-align: right !important;border: 1px solid #ddd;">
                  <tr id="tr_basket_products_netvalue" style="'.($pliroteo==$_gks_session['gks']['basket']['products_netvalue'] ? 'display:none;' :'').'">
                    <td style="padding: 10px 10px 10px 10px; text-align: left  !important;border-bottom: 1px solid #ddd;font-size:16pt;" nowrap="nowrap" width="0%">'.gks_lang('Σύνολο').'</td>
                    <td style="padding: 10px 10px 10px 10px; text-align: right !important;border-bottom: 1px solid #ddd;font-size:16pt;" nowrap="nowrap" width="0%" id="basket_products_netvalue">'.
                      myCurrencyFormat($_gks_session['gks']['basket']['products_netvalue'],true,true).'</td>
                  </tr>
                  <tr id="tr_basket_products_fpa" style="'.(0==$_gks_session['gks']['basket']['products_fpa'] ? 'display:none;' :'').'">
                    <td style="padding: 10px 10px 10px 10px ; text-align: left  !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%">'.gks_lang('Φόροι').'</td>
                    <td style="padding: 10px 10px 10px 10px ; text-align: right !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%" id="basket_products_fpa">'.
                      myCurrencyFormat($_gks_session['gks']['basket']['products_fpa'],true,true).'</td>
                  </tr>
                  <tr id="tr_basket_kostos_apostolis" style="'.(0==$_gks_session['gks']['basket']['kostos_apostolis'] ? 'display:none;' :'').'">
                    <td style="padding: 10px 10px 10px 10px ; text-align: left  !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%">'.gks_lang('Κόστος αποστολής').'</td>
                    <td style="padding: 10px 10px 10px 10px ; text-align: right !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%" id="basket_kostos_apostolis">'.
                      myCurrencyFormat($_gks_session['gks']['basket']['kostos_apostolis'],true,true).'</td>
                  </tr>
                  <tr id="tr_basket_kostos_pliromis" style="'.(0==$_gks_session['gks']['basket']['kostos_pliromis'] ? 'display:none;' :'').'">
                    <td style="padding: 10px 10px 10px 10px ; text-align: left  !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%">'.gks_lang('Κόστος πληρωμής').'</td>
                    <td style="padding: 10px 10px 10px 10px ; text-align: right !important;border-bottom: 1px solid #ddd;" nowrap="nowrap" width="0%" id="basket_kostos_pliromis">'.
                      myCurrencyFormat($_gks_session['gks']['basket']['kostos_pliromis'],true,true).'</td>
                  </tr>
                  <tr>
                    <td style="padding: 10px 10px 10px 10px; text-align: left  !important;border-bottom: 1px solid #ddd;font-size:16pt;" nowrap="nowrap" width="0%">'.gks_lang('Πληρωτέο').'</td>
                    <td style="padding: 10px 10px 10px 10px; text-align: right !important;border-bottom: 1px solid #ddd;font-size:16pt;" nowrap="nowrap" width="0%" id="basket_products_total">'.
                      myCurrencyFormat($pliroteo,true,true).'</td>
                  </tr>
    
    
                </table>
            
          </div>';
          
            $temp_html_apostoli='';
            $will_show_apostoli=false;
  foreach ($_gks_session['gks']['basket']['tropoi_apostolis_all'] as $row_apostoli) {  
              if ($row_apostoli['myisok'] and $row_apostoli['id_delivery_method'] !=1) $will_show_apostoli=true;
              
              $temp_html_apostoli.='<div style="'.($row_apostoli['myisok'] ? '' : 'display:none;').'">
                <input type="radio" name="radio_delivery_way" value="'.$row_apostoli['id_delivery_method'].'" id="radio_delivery_way_'.$row_apostoli['id_delivery_method'].'"  
                data-type="'.$row_apostoli['delivery_method_type'].'" data-type-o="'.$row_apostoli['delivery_method_type_pa'].'"
                data-sxolio="'.base64_encode($row_apostoli['delivery_method_sxolio']).'"'.
                ($_gks_session['gks']['basket']['tropos_apostolis'] == $row_apostoli['id_delivery_method'] ? ' checked ' : '').'
                > 
                <label for="radio_delivery_way_'.$row_apostoli['id_delivery_method'].'" style="cursor: pointer;" class="delivery_payment_label tooltipster" title="'.$row_apostoli['delivery_method_tooltip'].'">'.$row_apostoli['delivery_method_name'].
                  ($row_apostoli['delivery_method_fees_enabled']!=0 ?
                  '<span class="delivery_payment_price" id="price_delivery_way_'.$row_apostoli['id_delivery_method'].'" >'.myCurrencyFormat($row_apostoli['dm_calc_kostos'],true,true).'</span>' 
                  : '').
                '</label>';
                
                if ($row_apostoli['id_delivery_method'] == 8) { 
                  $temp_html_apostoli.='<span id="span_delivery_id_8" style="display:none;">
                  <br>
                  <select id="delivery_id_8" name="delivery_id_8" style="width:90%;" class="gks_input_select">
                      <option value="0">'.gks_lang('Επιλέξτε κατάστημα').'</option>

                  </select>
                  </span>';

               }
               $temp_html_apostoli.='</div>';
 }                   
 
             $temp_html_pliromi='';
             $will_show_pliromi=false;
  foreach ($_gks_session['gks']['basket']['tropoi_pliromis_all'] as $row_pliromi) {
              if ($row_pliromi['myisok'] and $row_pliromi['id_payment_acquirer'] !=1) $will_show_pliromi=true;
              
              $temp_html_pliromi.='<div style="'.($row_pliromi['myisok'] ? '' : 'display:none;').'">
                <input class="myneedsave" type="radio" name="radio_payment_way" value="'.$row_pliromi['id_payment_acquirer'].'" id="radio_payment_way_'.$row_pliromi['id_payment_acquirer'].'" 
                data-type="'.$row_pliromi['payment_acquirer_type'].'" data-type-o="'.$row_pliromi['payment_acquirer_type_dm'].'" 
                data-sxolio="'.base64_encode($row_pliromi['payment_acquirer_sxolio']).'"
                data-button-html="'.base64_encode($row_pliromi['payment_acquirer_button_html']).'" ';
                
                if ($_gks_session['gks']['basket']['tropos_pliromis'] == $row_pliromi['id_payment_acquirer']) $temp_html_pliromi.= ' checked ';
                $temp_html_pliromi.='> 
                <label for="radio_payment_way_'.$row_pliromi['id_payment_acquirer'].'" style="cursor: pointer;" class="delivery_payment_label tooltipster" title="'.$row_pliromi['payment_acquirer_tooltip'].'">'.$row_pliromi['payment_acquirer_html'].
                  (($row_pliromi['payment_acquirer_fees_enabled']!=0 and $row_pliromi['payment_acquirer_type']!='none') ?
                  '<span class="delivery_payment_price" id="price_payment_way_'.$row_pliromi['id_payment_acquirer'].'" >'.myCurrencyFormat($row_pliromi['pa_calc_kostos'],true,true).'</span>'
                  : '').
                '</label>
               </div>';
}   
         
          if ($table_products_varos_ogos_visible  || $will_show_apostoli) {
            $out.='<div class="gks_dfn"></div>';
          }
          //
          //if ( '';
          
          
  
          
            $out.='
            <div class="gks_checkout_col1" id="div_delivery_way" style="'.($will_show_apostoli ? '' : 'display:none;').'">
            <div>
              <div style="font-size:16pt;">'.gks_lang('Τρόποι αποστολής').':</div>';
              $out.= $temp_html_apostoli;
               $out.='<div id="delivery_method_sxolio" style="font-size:13px"></div>
            </div>
          </div>';
          
          
       
          
            $out.='<div class="gks_checkout_col2" id="div_payment_way" style="'.($will_show_pliromi ? '' : 'display:none;').'">
            <div>
              <div style="font-size:16pt;">'.gks_lang('Τρόποι πληρωμής').':</div>';
              $out.= $temp_html_pliromi;
              $out.='<div id="payment_acquirer_sxolio" style="font-size:13px"></div>
            </div>
          </div>
          <div class="gks_dfn"></div>


        </div>
        
        <div id="gks_rsrv_r" style="width:calc(30% - 30px);float: left;margin-left: 10px;">
          
          <div class="gks_box_shadow" id="gks_rsrv_rc_rsrv" style="border:1px solid;background-color111:#d1ffd1;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;';
            if ($total_sum==0) $out.= 'display:none;';
            $out.='">
            <h2 style="text-transform:unset;text-align:center;">'.gks_lang('Σύνοψη Κρατήσεων').'</h2>
            
            <div style="line-height: 2;font-size:130%;text-align: center;">
              '.gks_lang('Κρατήσεις').': <span id="gks_total_reservations_span" style="font-weight:bold;">'.count($myreservations).'</span>
              <br>
              '.gks_lang('Δωμάτια').': <span id="gks_total_domatia_span" style="font-weight:bold;">'.$total_domatia.'</span>
              <br>
              '.gks_lang('Διανυκτερεύσεις').': <span id="gks_total_dianiktereuseis_span" style="font-weight:bold;">'.$total_dianiktereuseis.'</span>
              <br>
              '.gks_lang('Επισκέπτες').': <span id="gks_total_visitors_span" style="font-weight:bold;">'.$total_visitors.
              '</span><span style="font-weight:bold;"> x <i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i></span>
              <br>
              '.gks_lang('Ποσό').': <span id="gks_total_price_span" style="font-weight:bold;">'.myCurrencyFormat($total_sum,true, true).'</span>
            </div>                
          </div>
          
                    
          <div class="gks_box_shadow" id="gks_rsrv_rc_parastatiko" style="border:1px solid;background-color111:#d1ffd1;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;">
            <h2 style="text-transform:unset;text-align:center;">'.gks_lang('Τύπος Παραστατικού').'</h2>
            <table  class="table1 table-striped1 generic-table1 cs-ta-right1" border="0" cellspacing="0" cellpadding="0" id="paytype" style="color:gray;font-size:13px;width:100%;text-align: right !important;border: 0px solid #ddd;">
              <tr>
                <td style="padding: 3px 3px 3px 3px; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><i class="gks_fas gks_fa-file-alt" style="font-size: 20px;"></i></td>
                <td style="padding: 3px 0px 3px 10px; text-align: left; border-bottom: 1px solid #ddd;" width="100%">';
                  if ($_gks_session['gks']['basket']['parastatiko'] == 1) {
                    $out.= gks_lang('Τιμολόγιο');
                  } else { 
                    $out.= gks_lang('Απόδειξη'); 
                  }
                  $out.='</td>
              </tr>
              ';
              
if ($_gks_session['gks']['basket']['parastatiko'] == 1) {
              $out.='<tr>
                <td style="padding: 3px 3px 3px 3px ; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><i class="gks_fas gks_fa-building" style="font-size: 20px;"></i></td>
                <td style="padding: 3px 0px 3px 10px; text-align: left;border-bottom: 1px solid #ddd;" width="100%" >';
                  $out.= isset($_gks_session['gks']['basket']['user']['eponimia']) ?  $_gks_session['gks']['basket']['user']['eponimia'].'<br>' : '';
                  $out.= isset($_gks_session['gks']['basket']['user']['title']) ?  $_gks_session['gks']['basket']['user']['title'].'<br>' : '';
                  $out.= isset($_gks_session['gks']['basket']['user']['afm']) ?  $_gks_session['gks']['basket']['user']['afm'].' ' : '';
                  $out.= isset($_gks_session['gks']['basket']['user']['doy']) ?  $_gks_session['gks']['basket']['user']['doy'].'<br>' : '';
                  $out.= isset($_gks_session['gks']['basket']['user']['epaggelma']) ?  $_gks_session['gks']['basket']['user']['epaggelma'] : '';
                  $out.='</td>
              </tr>';
 }
            $out.='</table>
          </div>
          
          <div class="gks_box_shadow" id="gks_rsrv_rc_pay_from" style="border:1px solid;background-color111:#d1ffd1;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;">
            <h2 style="text-transform:unset;text-align:center;">'.gks_lang('Πληρωμή').'</h2>
            <table  class="table1 table-striped1 generic-table1 cs-ta-right1" border="0" cellspacing="0" cellpadding="0" id="address_pay" style="color:gray;font-size:13px;width:100%;text-align: right !important;border: 0px solid #ddd;">
              <tr>
                <td style="padding: 3px 3px 3px 3px; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><i class="gks_fas gks_fa-user" style="font-size: 20px;"></i></td>
                <td style="padding: 3px 0px 3px 10px; text-align: left;border-bottom: 1px solid #ddd;" width="100%">'.$_gks_session['gks']['basket']['user']['first_name'].' '.$_gks_session['gks']['basket']['user']['last_name'].'</td>
              </tr>
              <tr>
                <td style="padding: 3px 3px 3px 3px;text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><a target="_blank" href="';
                  $url_map='';
                  $url_map.= 'https://www.google.com/maps/place/';
                  $url_map.= $_gks_session['gks']['basket']['user']['ma_odos']!='' ?  urlencode($_gks_session['gks']['basket']['user']['ma_odos'].',') : '';
                  $url_map.= $_gks_session['gks']['basket']['user']['ma_perioxi']!='' ?  urlencode($_gks_session['gks']['basket']['user']['ma_perioxi'].',') : '';
                  $url_map.= $_gks_session['gks']['basket']['user']['ma_poli']!='' ?  urlencode($_gks_session['gks']['basket']['user']['ma_poli'].',') : '';
                  $url_map.= $_gks_session['gks']['basket']['user']['ma_tk']!='' ?  urlencode($_gks_session['gks']['basket']['user']['ma_tk'].',') : '';
                  $nomos_name='';
                  if ($_gks_session['gks']['basket']['user']['ma_nomos_id']>0) {
                    $lang_prepare_gks_nomoi=gks_lang_data_obj_prepare('gks_nomoi','default');
                    if ($lang_prepare_gks_nomoi['success']==false) die($lang_prepare_gks_nomoi['message']);
                    gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomoi, array('nomos_descr'));
                                        
                    $sql_nomos_name="SELECT ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi)." 
                    FROM ".$lang_prepare_gks_nomoi['sql']['from1']." gks_nomoi 
                    ".$lang_prepare_gks_nomoi['sql']['from2']."
                    WHERE id_nomos=".$_gks_session['gks']['basket']['user']['ma_nomos_id'];
                    $result_nomos_name = $db_link->query($sql_nomos_name);
                    if (!$result_nomos_name) {debug_mail(false,'error sql',$sql_nomos_name);die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
                    if ($result_nomos_name->num_rows == 1) {
                      $row_nomos_name = $result_nomos_name->fetch_assoc();
                      $nomos_name=$row_nomos_name['nomos_descr'];
                      $url_map.= $nomos_name!='' ?  urlencode($nomos_name.',') : '';  
                    }                    
                  }
                  $country_name='';
                  if ($_gks_session['gks']['basket']['user']['ma_country_id']>0) {
                    $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
                    gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
                    
                    $sql_country_name="SELECT ".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
                    FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
                    ".$lang_prepare_gks_country['sql']['from2']."
                    WHERE id_country=".$_gks_session['gks']['basket']['user']['ma_country_id'];
                    $result_country_name = $db_link->query($sql_country_name);
                    if (!$result_country_name) {debug_mail(false,'error sql',$sql_country_name);die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
                    if ($result_country_name->num_rows == 1) {
                      $row_country_name = $result_country_name->fetch_assoc();
                      $country_name=$row_country_name['country_name'];
                      $url_map.= $country_name!='' ?  urlencode($country_name) : '';  
                    }
                  }
                  $out.= $url_map;
                  $out.='"><i class="gks_fas gks_fa-map-marker-alt" style="font-size: 20px;"></i></a></td>
                  <td style="padding: 3px 0px 3px 10px;text-align: left;border-bottom: 1px solid #ddd;" width="100%" >';
                  $out.= $_gks_session['gks']['basket']['user']['ma_odos']!='' ?  $_gks_session['gks']['basket']['user']['ma_odos'].'<br>' : '';
                  $out.= $_gks_session['gks']['basket']['user']['ma_perioxi']!='' ?  $_gks_session['gks']['basket']['user']['ma_perioxi'].'<br>' : '';
                  $out.= $_gks_session['gks']['basket']['user']['ma_poli']!='' ?  $_gks_session['gks']['basket']['user']['ma_poli'].' ' : '';
                  $out.= $_gks_session['gks']['basket']['user']['ma_tk']!='' ?  $_gks_session['gks']['basket']['user']['ma_tk'].'<br>' : '';
                  $out.= $nomos_name!='' ?  $nomos_name.'<br>' : '';
                  $out.= $country_name!='' ?  $country_name : '';
                  
                  $out.='</td>
              </tr>
              <tr style="'.($_gks_session['gks']['basket']['user']['mobile']!='' ? '' : 'display:none').'">
                <td style="padding: 3px 3px 3px 3px ; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><i class="gks_fas gks_fa-phone" style="font-size: 20px;"></i></td>
                <td style="padding: 3px 0px 3px 10px; text-align: left; border-bottom: 1px solid #ddd;" width="100%" >'.$_gks_session['gks']['basket']['user']['mobile'].'</td>
              </tr>
              <tr style="'.($_gks_session['gks']['basket']['user']['email']!='' ? '' : 'display:none').'">
                <td style="padding: 3px 3px 3px 3px; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><i class="gks_fas gks_fa-envelope" style="font-size: 20px;"></i></td>
                <td style="padding: 3px 0px 3px 10px; text-align: left; border-bottom: 1px solid #ddd;" width="100%">'.$_gks_session['gks']['basket']['user']['email'].' </td>
              </tr>
              <tr>
                <td style="padding: 3px 3px 3px 3px; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><a href="/checkout"><i class="gks_fas gks_fa-arrow-left" style="font-size: 20px;"></i></a></td>
                <td style="padding: 3px 0px 3px 10px; text-align: left; border-bottom: 1px solid #ddd;" width="100%"><a href="/checkout">'.gks_lang('Αλλαγή διεύθυνσης').'</a></td>
              </tr>

            </table>          
          </div>';
          if ($_gks_session['gks']['basket']['products_need_apostoli']==false) {
            $gks_rsrv_rc_send_from_hide= true; 
          } else {
            if ($_gks_session['gks']['basket']['tropos_apostolis']>0 and 
             isset($_gks_session['gks']['basket']['tropoi_apostolis_all']) and 
             isset($_gks_session['gks']['basket']['tropoi_apostolis_all'][ $_gks_session['gks']['basket']['tropos_apostolis'] ]) and
             $_gks_session['gks']['basket']['tropoi_apostolis_all'][ $_gks_session['gks']['basket']['tropos_apostolis'] ]['delivery_method_type']=='store') {
              $gks_rsrv_rc_send_from_hide= true; 
            } else {
              $gks_rsrv_rc_send_from_hide= false; 
            }
          } 
          
          
          $out.='<div class="gks_box_shadow" id="gks_rsrv_rc_send_from" style="'.($gks_rsrv_rc_send_from_hide ? 'display:none;' : '').'border:1px solid;background-color111:#d1ffd1;color111:#000000;border-radius: 20px;padding: 24px;margin-bottom: 24px;">
            <h2 style="text-transform:unset;text-align:center;">'.gks_lang('Αποστολή σε').'</h2>
          

            <table class="table1 table-striped1 generic-table1 cs-ta-right1" border="0" cellspacing="0" cellpadding="0" id="address_delivery" style="color:gray;font-size:13px;width:100%;text-align: right !important;border: 0px solid #ddd;">';
            if ($_gks_session['gks']['basket']['address_extra']==-1) { 
              $out.='<tr>
                <td style="padding: 3px 3px 3px 3px; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><i class="gks_fas gks_fa-building" style="font-size: 20px;"></i></td>
                <td style="padding: 3px 0px 3px 10px; text-align: left; border-bottom: 1px solid #ddd;" width="100%">'.gks_lang('Αποστολή στην ίδια διεύθυνση').'</td>
              </tr>';
                

            } else { 

  
  

       $out.='<tr style="'.($_gks_session['gks']['basket']['destination_data']['name']!='' ? '' : 'display:none').'">
                <td style="padding: 3px 3px 3px 3px; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><i class="gks_fas gks_fa-user" style="font-size: 20px;"></i></td>
                <td style="padding: 3px 0px 3px 10px; text-align: left;border-bottom: 1px solid #ddd;" width="100%">'.$_gks_session['gks']['basket']['destination_data']['name'].'</td>
              </tr>
              <tr>
                <td style="padding: 3px 3px 3px 3px ; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><a target="_blank" href="';
                  $url_map='';
                  $url_map.= 'https://www.google.com/maps/place/';
                  $url_map.= $_gks_session['gks']['basket']['destination_data']['phone']!='' ?  urlencode($_gks_session['gks']['basket']['destination_data']['phone'].',') : '';
                  $url_map.= $_gks_session['gks']['basket']['destination_data']['perioxi']!='' ?  urlencode($_gks_session['gks']['basket']['destination_data']['perioxi'].',') : '';
                  $url_map.= $_gks_session['gks']['basket']['destination_data']['poli']!='' ?  urlencode($_gks_session['gks']['basket']['destination_data']['poli'].',') : '';
                  $url_map.= $_gks_session['gks']['basket']['destination_data']['tk']!='' ?  urlencode($_gks_session['gks']['basket']['destination_data']['tk'].',') : '';
                  $nomos_name='';
                  if ($_gks_session['gks']['basket']['destination_data']['nomos_id']>0) {
                    
                    $lang_prepare_gks_nomoi=gks_lang_data_obj_prepare('gks_nomoi','default');
                    if ($lang_prepare_gks_nomoi['success']==false) die($lang_prepare_gks_nomoi['message']);
                    gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomoi, array('nomos_descr'));
                    
                    $sql_nomos_name="SELECT ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi)." 
                    FROM ".$lang_prepare_gks_nomoi['sql']['from1']." gks_nomoi 
                    ".$lang_prepare_gks_nomoi['sql']['from2']."
                    WHERE id_nomos=".$_gks_session['gks']['basket']['destination_data']['nomos_id'];
                    $result_nomos_name = $db_link->query($sql_nomos_name);
                    if (!$result_nomos_name) {debug_mail(false,'error sql',$sql_nomos_name);die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
                    if ($result_nomos_name->num_rows == 1) {
                      $row_nomos_name = $result_nomos_name->fetch_assoc();
                      $nomos_name=$row_nomos_name['nomos_descr'];
                      $url_map.= $nomos_name!='' ?  urlencode($nomos_name.',') : '';  
                    }                    
                  }
                  $country_name='';
                  if ($_gks_session['gks']['basket']['destination_data']['country_id']>0) {
                    $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
                    gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
                    
                    $sql_country_name="SELECT ".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
                    FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
                    ".$lang_prepare_gks_country['sql']['from2']."
                    WHERE id_country=".$_gks_session['gks']['basket']['destination_data']['country_id'];
                    $result_country_name = $db_link->query($sql_country_name);
                    if (!$result_country_name) {debug_mail(false,'error sql',$sql_country_name);die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
                    if ($result_country_name->num_rows == 1) {
                      $row_country_name = $result_country_name->fetch_assoc();
                      $country_name=$row_country_name['country_name'];
                      $url_map.= $country_name!='' ?  urlencode($country_name) : '';  
                    }
                  }
                  $out.= $url_map;
                  $out.='"><i class="gks_fas gks_fa-map-marker-alt" style="font-size: 20px;"></i></a></td>
                  <td style="padding: 3px 0px 3px 10px ; text-align: left;border-bottom: 1px solid #ddd;" width="100%" >';
                  $out.= $_gks_session['gks']['basket']['destination_data']['phone']!='' ?  $_gks_session['gks']['basket']['destination_data']['phone'].'<br>' : '';
                  $out.= $_gks_session['gks']['basket']['destination_data']['perioxi']!='' ?  $_gks_session['gks']['basket']['destination_data']['perioxi'].'<br>' : '';
                  $out.= $_gks_session['gks']['basket']['destination_data']['poli']!='' ?  $_gks_session['gks']['basket']['destination_data']['poli'].' ' : '';
                  $out.= $_gks_session['gks']['basket']['destination_data']['tk']!='' ?  $_gks_session['gks']['basket']['destination_data']['tk'].'<br>' : '';
                  $out.= $nomos_name!='' ?  $nomos_name.'<br>' : '';
                  $out.= $country_name!='' ?  $country_name : '';
                  
                  $out.='</td>
              </tr>
              <tr style="'.($_gks_session['gks']['basket']['destination_data']['phone']!='' ? '' : 'display:none').'">
                <td style="padding: 3px 3px 3px 3px ; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><i class="gks_fas gks_fa-phone" style="font-size: 20px;"></i></td>
                <td style="padding: 3px 0px 3px 10px ; text-align: left;border-bottom: 1px solid #ddd;" width="100%" >'.$_gks_session['gks']['basket']['destination_data']['phone'].'</td>
              </tr>';
            } 
            $out.='<tr>
                <td style="padding: 3px 3px 3px 3px; text-align: left  !important;border-bottom: 1px solid #ddd;" width="0%"><a href="/checkout"><i class="gks_fas gks_fa-arrow-left" style="font-size: 20px;"></i></a></td>
                <td style="padding: 3px 0px 3px 10px; text-align: left; border-bottom: 1px solid #ddd;" width="100%"><a href="/checkout">'.gks_lang('Αλλαγή διεύθυνσης').'</a></td>
              </tr>
              

            </table>
           
          </div>
          
        </div>
      </div><!-- .gks_row --> 
        

    </div><!--.gks_container-->
  </div><!--.gks_body_wrapper-->
</div><!--.gks_main_content-->
';


$out.='<div class="gks_main_content">
  <div class="gks_body_wrapper">
    <div class="gks_container">      
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
      <button id="back_to_checkout" class="gks_button fusion-button button-default button-medium button-3d">
        <span class=""><i class="gks_fa gks_fa-angle-left"></i></span>
        <span class="">'.gks_lang('Επιστροφή').'</span>
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
      <button id="pay_now" class="gks_button fusion-button button-default button-medium button-3d">
        <span class=""><span id="button_html">'.gks_lang('Πληρωμή τώρα').'</span></span>
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






$out.='<div id="gks_payment_acquirer_piraeusbank"></div>';

$out.='
<script type="text/javascript">


var from_php_hotel_id='.$id_hotel.';
'.from_php_global_vars_echo().'

var from_php_gks_set_lang_url="'.gks_set_lang_url().'";

var from_php_lang_OK="'.gks_lang('OK').'";
var from_php_lang_Cancel="'.gks_lang('Άκυρο').'";
var from_php_lang_ErrorPleasetryagainlater="'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'";
var from_php_lang_Pleaseselectashippingmethod="'.gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο αποστολής').'";
var from_php_lang_Pleaseselectapaymentmethod="'.gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο πληρωμής').'";
var from_php_lang_Paynow="'.gks_lang('Πληρωμή τώρα').'";
var from_php_lang_ShippingComment="'.gks_lang('Σχόλιο τρόπου αποστολής').'";
var from_php_lang_PaymentComment="'.gks_lang('Σχόλιο τρόπου πληρωμής').'";
var from_php_lang_Pleaseselectthestoreyouwanttopickupyourproducts="'.gks_lang('Παρακαλώ επιλέξτε το κατάστημα που θέλετε να παραλάβετε τα προϊόντα σας').'";



var from_php_gks_api_hotel_page_reservation_search=\''.$input_data['gks_api_hotel_page_reservation_search'].'\';
var from_php_gks_api_hotel_page_reservation_basket=\''.$input_data['gks_api_hotel_page_reservation_basket'].'\';
var from_php_gks_api_page_checkout=\''.$input_data['gks_api_page_checkout'].'\';
var from_php_gks_api_page_payment=\''.$input_data['gks_api_page_payment'].'\';
var from_php_gks_api_page_confirm=\''.$input_data['gks_api_page_confirm'].'\';


</script>

<script src="'.GKS_SITE_URL.'my/js/payment.js?v='.$gks_cache_version.'"></script>

';

  gks_erp_cookie_save($gks_erp_cookie_id);

      
  return '<div id="gks_hotel_container">'.$out.'</div>';
  
}

