<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_hotel_cmd_hotel_reservation_empty_basket($input_data) {
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

  $gks_erp_cookie_id='';
  if(isset($input_data['gks_erp_cookie_id'])) {
    $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
  }
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  gks_erp_cookie_start($gks_erp_cookie_id);
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  
  
  unset($_gks_session['gks']['basket']);
  unset($_gks_session['gks']['basket']['hotel']['reservation']);
  unset($_gks_session['gks']['confirm']);
  unset($_gks_session['gks']['alphabank']);  
  unset($_gks_session['gks']['paypal']);  
  unset($_gks_session['gks']['payment_error']);
  
  gks_erp_cookie_defaults();
  
  gks_erp_cookie_save($gks_erp_cookie_id);
  
  

      
  return 'OK';
  
}

