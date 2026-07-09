<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_cmd_payment_edit($id_hotel,$row_hotel,$input_data) {
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
  
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  
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

  //$return = array('success' => false, 'message' => base64_encode('<pre>'.$_gks_session['gks']['ui_lang']));
  //return $return;
   
  $my_wp_user_id=0;

  $_POST=$input_data['post'];

  
if ($_gks_session['gks']['basket']['tropos_apostolis']<=0 ) {
  debug_mail(false,'checkout-edit.php check','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο αποστολής')));
  return $return; }


if ($_gks_session['gks']['basket']['tropos_pliromis']<=0) {
  debug_mail(false,'checkout-edit.php check','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο πληρωμής')));
  return $return; }


$_gks_session['gks']['basket']['kostos_apostolis'] = gks_calculate_kostos_apostolis($_gks_session['gks']['basket'], $_gks_session['gks']['basket']['tropos_apostolis']);
$_gks_session['gks']['basket']['kostos_pliromis']  = gks_calculate_kostos_pliromis ($_gks_session['gks']['basket'], $_gks_session['gks']['basket']['tropos_pliromis']);

$_gks_session['gks']['basket']['delivery_id_8'] = 0;
if (isset($_POST['delivery_id_8'])) $_gks_session['gks']['basket']['delivery_id_8'] = intval($_POST['delivery_id_8']);
$delivery_id_8 = $_gks_session['gks']['basket']['delivery_id_8'];

//$return = array('success' => false, 'message' => base64_encode('aaaaaa.'.$_gks_session['gks']['basket']['delivery_id_8']));
//return $return; 


$_gks_session['gks']['basket']['user']['user_id']=$my_wp_user_id;

gks_basket_recalc($_gks_session['gks']['basket'], array(), array());

//echo '<pre>';
//echo time();
//die();

//if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/basket.txt',print_r($_gks_session['gks']['basket'],true));

//part1
$user_email = '';
$fiscal_position_id=0;
$pricelist_id=0;
//if ($my_wp_user_id<=0) {
$user_email = $_gks_session['gks']['basket']['user']['email'];



  $fiscal_position_id=1;
  if ($_gks_session['gks']['basket']['user']['ma_country_id']==91) {
    if ($_gks_session['gks']['basket']['parastatiko'] != 0) $fiscal_position_id=4;
  } else {
    $fiscal_position_id=10;
    if ($_gks_session['gks']['basket']['parastatiko'] != 0) $fiscal_position_id=11;
  }
  $pricelist_id=1;  
  
//} else {
//  $sql="SELECT user_email,fiscal_position_id,pricelist_id FROM ".GKS_WP_TABLE_PREFIX."users WHERE ID=".$my_wp_user_id;
//  $result = $db_link->query($sql);
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode((gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'))));
//    v }
//    
//  if ($result->num_rows == 0) {
//    debug_mail(false,'need relogin'.'');
//    $return = array('success' => true, 'message' => base64_encode('redirect'),'url' => base64_encode('/login'));
//    return $return; }
//  
//  
//  $row = $result->fetch_assoc();
//  if (isset($row['user_email'])) $user_email = $row['user_email'];
//  if (isset($row['fiscal_position_id'])) $fiscal_position_id = $row['fiscal_position_id'];
//  if (isset($row['pricelist_id'])) $pricelist_id = $row['pricelist_id'];
//}



//part2

$user_first_name=trim_gks($_gks_session['gks']['basket']['user']['first_name']);
$user_last_name=trim_gks($_gks_session['gks']['basket']['user']['last_name']);
$user_mobile=trim_gks($_gks_session['gks']['basket']['user']['mobile']);  
$user_lang=trim_gks($_gks_session['gks']['basket']['user']['lang']);  
$user_ma_odos=trim_gks($_gks_session['gks']['basket']['user']['ma_odos']);
$user_ma_perioxi=trim_gks($_gks_session['gks']['basket']['user']['ma_perioxi']);
$user_ma_poli=trim_gks($_gks_session['gks']['basket']['user']['ma_poli']);
$user_ma_tk=trim_gks($_gks_session['gks']['basket']['user']['ma_tk']);
$user_ma_country_id=intval($_gks_session['gks']['basket']['user']['ma_country_id']);
$user_ma_nomos_id=intval($_gks_session['gks']['basket']['user']['ma_nomos_id']);
$user_eponimia=trim_gks($_gks_session['gks']['basket']['user']['eponimia']);
$user_title=trim_gks($_gks_session['gks']['basket']['user']['title']);
$user_afm=trim_gks($_gks_session['gks']['basket']['user']['afm']);
$user_doy=trim_gks($_gks_session['gks']['basket']['user']['doy']);
$user_epaggelma=trim_gks($_gks_session['gks']['basket']['user']['epaggelma']);

$other_first_name=trim_gks($_gks_session['gks']['basket']['user_other']['first_name']);
$other_last_name=trim_gks($_gks_session['gks']['basket']['user_other']['last_name']);
$other_email=trim_gks($_gks_session['gks']['basket']['user_other']['email']);
$other_mobile=trim_gks($_gks_session['gks']['basket']['user_other']['mobile']);
$other_lang=trim_gks($_gks_session['gks']['basket']['user_other']['lang']);
$other_ma_odos=trim_gks($_gks_session['gks']['basket']['user_other']['ma_odos']);
$other_ma_perioxi=trim_gks($_gks_session['gks']['basket']['user_other']['ma_perioxi']);
$other_ma_poli=trim_gks($_gks_session['gks']['basket']['user_other']['ma_poli']);
$other_ma_tk=trim_gks($_gks_session['gks']['basket']['user_other']['ma_tk']);
$other_ma_country_id=intval($_gks_session['gks']['basket']['user_other']['ma_country_id']);
$other_ma_nomos_id=intval($_gks_session['gks']['basket']['user_other']['ma_nomos_id']);



$address_extra=intval($_gks_session['gks']['basket']['address_extra']);
if ($address_extra<-1) $address_extra=-1;
$_gks_session['gks']['basket']['address_extra']=$address_extra; //just clean

gks_get_destination_data($_gks_session['gks']['basket']);


$dd_name=trim_gks($_gks_session['gks']['basket']['destination_data']['name']);
$dd_phone=trim_gks($_gks_session['gks']['basket']['destination_data']['phone']);
$dd_odos=trim_gks($_gks_session['gks']['basket']['destination_data']['odos']);
$dd_arithmos=trim_gks($_gks_session['gks']['basket']['destination_data']['arithmos']);
$dd_orofos=trim_gks($_gks_session['gks']['basket']['destination_data']['orofos']);
$dd_perioxi=trim_gks($_gks_session['gks']['basket']['destination_data']['perioxi']);
$dd_poli=trim_gks($_gks_session['gks']['basket']['destination_data']['poli']);
$dd_tk=trim_gks($_gks_session['gks']['basket']['destination_data']['tk']);
$dd_country_id=intval($_gks_session['gks']['basket']['destination_data']['country_id']);
$dd_nomos_id=intval($_gks_session['gks']['basket']['destination_data']['nomos_id']);




$order_guid = guid_for_order();



$bank_deposit_9digit=gks_get_bank_deposit_9digit();

//if ($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_type'] == 'bank') {
//  while (true) {
//    $bank_deposit_9digit=rand(10000000,99999999);
//    $sql = "SELECT bank_deposit_9digit from gks_orders where bank_deposit_9digit='".$db_link->escape_string($bank_deposit_9digit)."'";
//    $result = $db_link->query($sql);
//    if ($result->num_rows == 0) {
//      break;
//    }
//  }
  
//}



//print $bank_deposit_9digit;
//print_r( $_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_type']);
//die();

////////////////////////////////////////////////////////


//$_gks_session['gks']['basket']['id_object']=0;

if (isset($_gks_session['gks']['basket']['id_object']) and $_gks_session['gks']['basket']['id_object']>0) {
  $sql="SELECT id_order_product FROM gks_orders_products WHERE order_id=".$_gks_session['gks']['basket']['id_object'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
  $id_order_product_in = '';
  $id_order_product_object_in = '';
  $id_order_product_object_file_in = '';

  while ($row = $result->fetch_assoc()) {  
    $id_order_product_in.= $row['id_order_product'].',';
  }
  if (strlen($id_order_product_in)>0) {
    $id_order_product_in=substr($id_order_product_in, 0,strlen($id_order_product_in)-1);
    
    $sql="SELECT id_order_product_object FROM gks_orders_products_objects WHERE order_product_id in (".$id_order_product_in.")";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }    
    while ($row = $result->fetch_assoc()) {  
      $id_order_product_object_in.= $row['id_order_product_object'].',';
    }    
    if (strlen($id_order_product_object_in)>0) {
      $id_order_product_object_in=substr($id_order_product_object_in, 0,strlen($id_order_product_object_in)-1);
      
      $sql="SELECT id_order_product_object_file FROM gks_orders_products_objects_files WHERE order_product_object_id in (".$id_order_product_object_in.")";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        return $return; }    
      while ($row = $result->fetch_assoc()) {  
        $id_order_product_object_file_in.= $row['id_order_product_object_file'].',';
      }    
      if (strlen($id_order_product_object_file_in)>0) {
        $id_order_product_object_file_in=substr($id_order_product_object_file_in, 0,strlen($id_order_product_object_file_in)-1);
      }      
    }
  }
  
  $sql="delete from gks_orders where id_order=".$_gks_session['gks']['basket']['id_object'];
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }    
  
  if ($id_order_product_in!='') {
    $sql="delete from gks_orders_products where id_order_product in (".$id_order_product_in.")";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }    
  }
  if ($id_order_product_object_in !='') {
    $sql="delete from gks_orders_products_objects where id_order_product_object in (".$id_order_product_object_in.")";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }    
  }

  if ($id_order_product_object_file_in !='') {
    $sql="delete from gks_orders_products_objects_files where id_order_product_object_file in (".$id_order_product_object_file_in.")";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }    
  }

//  $sql="delete from gks_users_extra_address where order_id=".$_gks_session['gks']['basket']['id_object'];
//  $result = $db_link->query($sql);  
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode((gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'))));
//    return $return; }    


  //$return = array('success' => false, 'message' => base64_encode('|'.$id_order_product_in . '|'.$id_order_product_object_in . '|'.$id_order_product_object_file_in .'|'));
  //return $return;   
}



$sqlF=''; $sqlV='';

if (isset($_gks_session['gks']['basket']['id_object']) and $_gks_session['gks']['basket']['id_object']>0) {
$sqlF.='id_order,';                       $sqlV.=$_gks_session['gks']['basket']['id_object'].",";  
}

$sqlF.='order_date,';                       $sqlV.="now(),";  
$sqlF.='order_guid,';                       $sqlV.="'".$db_link->escape_string($order_guid)."',";  
if ($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_type'] =='web') {
  $sqlF.='order_state,';                      $sqlV.="'".$db_link->escape_string('005prodraft')."',";  
} else if ($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_type'] =='bank') {
  $sqlF.='order_state,';                      $sqlV.="'".$db_link->escape_string('055wait_payment')."',";  
} else {
  $sqlF.='order_state,';                      $sqlV.="'".$db_link->escape_string('010draft')."',";
}
$sqlF.='user_id,';                          $sqlV.=$my_wp_user_id.",";                    
$sqlF.='user_first_name,';                  $sqlV.="'".$db_link->escape_string($user_first_name)."',";            
$sqlF.='user_last_name,';                   $sqlV.="'".$db_link->escape_string($user_last_name)."',";             
$sqlF.='user_email,';                       $sqlV.="'".$db_link->escape_string($user_email)."',";                 
$sqlF.='user_mobile,';                      $sqlV.="'".$db_link->escape_string($user_mobile)."',";                
$sqlF.='user_lang,';                        $sqlV.="'".$db_link->escape_string($user_lang)."',";                
$sqlF.='parastatiko,';                      $sqlV.=$_gks_session['gks']['basket']['parastatiko'].",";  
if ($_gks_session['gks']['basket']['parastatiko'] == 1) {              
$sqlF.='eponimia,';                         $sqlV.="'".$db_link->escape_string($user_eponimia)."',";                   
$sqlF.='title,';                            $sqlV.="'".$db_link->escape_string($user_title)."',";                      
$sqlF.='afm,';                              $sqlV.="'".$db_link->escape_string($user_afm)."',";                        
$sqlF.='doy,';                              $sqlV.="'".$db_link->escape_string($user_doy)."',";                        
$sqlF.='epaggelma,';                        $sqlV.="'".$db_link->escape_string($user_epaggelma)."',";                  
}
$sqlF.='ma_odos,';                          $sqlV.="'".$db_link->escape_string($user_ma_odos)."',";                    
$sqlF.='ma_perioxi,';                       $sqlV.="'".$db_link->escape_string($user_ma_perioxi)."',";                    
$sqlF.='ma_poli,';                          $sqlV.="'".$db_link->escape_string($user_ma_poli)."',";                    
$sqlF.='ma_tk,';                            $sqlV.="'".$db_link->escape_string($user_ma_tk)."',";                      
$sqlF.='ma_country_id,';                    $sqlV.=$user_ma_country_id.",";              
$sqlF.='ma_nomos_id,';                      $sqlV.=$user_ma_nomos_id.",";                
$sqlF.='fiscal_position_id,';               $sqlV.=$fiscal_position_id.",";         
$sqlF.='pricelist_id,';                     $sqlV.=$pricelist_id.",";    


                                            
$sqlF.='is_other,';                         $sqlV.=intval($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservation_other']).",";            
$sqlF.='other_first_name,';                 $sqlV.="'".$db_link->escape_string($other_first_name)."',";            
$sqlF.='other_last_name,';                  $sqlV.="'".$db_link->escape_string($other_last_name)."',";             
$sqlF.='other_email,';                      $sqlV.="'".$db_link->escape_string($other_email)."',";                 
$sqlF.='other_mobile,';                     $sqlV.="'".$db_link->escape_string($other_mobile)."',";                
$sqlF.='other_lang,';                       $sqlV.="'".$db_link->escape_string($other_lang)."',";                
$sqlF.='other_ma_odos,';                    $sqlV.="'".$db_link->escape_string($other_ma_odos)."',";                    
$sqlF.='other_ma_perioxi,';                 $sqlV.="'".$db_link->escape_string($other_ma_perioxi)."',";                    
$sqlF.='other_ma_poli,';                    $sqlV.="'".$db_link->escape_string($other_ma_poli)."',";                    
$sqlF.='other_ma_tk,';                      $sqlV.="'".$db_link->escape_string($other_ma_tk)."',";                      
$sqlF.='other_ma_country_id,';              $sqlV.=intval($other_ma_country_id).",";              
$sqlF.='other_ma_nomos_id,';                $sqlV.=intval($other_ma_nomos_id).",";                



           
$sqlF.='address_extra,';                    $sqlV.=$address_extra.",";              
$sqlF.='destination_data_name,';            $sqlV.="'".$db_link->escape_string($dd_name)."',";      
$sqlF.='destination_data_phone,';           $sqlV.="'".$db_link->escape_string($dd_phone)."',";     
$sqlF.='destination_data_odos,';            $sqlV.="'".$db_link->escape_string($dd_odos)."',";      
$sqlF.='destination_data_perioxi,';         $sqlV.="'".$db_link->escape_string($dd_perioxi)."',";      
$sqlF.='destination_data_poli,';            $sqlV.="'".$db_link->escape_string($dd_poli)."',";      
$sqlF.='destination_data_tk,';              $sqlV.="'".$db_link->escape_string($dd_tk)."',";        
$sqlF.='destination_data_country_id,';      $sqlV.=$dd_country_id.",";
$sqlF.='destination_data_nomos_id,';        $sqlV.=$dd_nomos_id.",";  
          
//$sqlF.='mydate_execute,';                    $sqlV.="'".$db_link->escape_string($_gks_session['gks']['basket']['date_execute'])."',";               
//$sqlF.='mydate_send,';                       $sqlV.="'".$db_link->escape_string($_gks_session['gks']['basket']['date_send'])."',";                  
//$sqlF.='mydate_invoice,';                    $sqlV.="'".$db_link->escape_string($_gks_session['gks']['basket']['date_invoice'])."',";               
//$sqlF.='mydate_payment,';                    $sqlV.="'".$db_link->escape_string($_gks_session['gks']['basket']['date_payment'])."',";               
$sqlF.='products_posotita,';                $sqlV.=number_format($_gks_session['gks']['basket']['products_posotita'],8,'.','').",";          
$sqlF.='products_varos,';                   $sqlV.=number_format($_gks_session['gks']['basket']['products_varos'],8,'.','').",";             
$sqlF.='products_ogos,';                    $sqlV.=number_format($_gks_session['gks']['basket']['products_ogos'],8,'.','').",";               
$sqlF.='products_ogos_max_x,';              $sqlV.=number_format($_gks_session['gks']['basket']['products_ogos_max_x'],8,'.','').",";         
$sqlF.='products_ogos_max_y,';              $sqlV.=number_format($_gks_session['gks']['basket']['products_ogos_max_y'],8,'.','').",";         
$sqlF.='products_ogos_max_z,';              $sqlV.=number_format($_gks_session['gks']['basket']['products_ogos_max_z'],8,'.','').",";         
$sqlF.='gks_price_original_net,';           $sqlV.=number_format($_gks_session['gks']['basket']['products_original_netvalue'],8,'.','').",";           
$sqlF.='gks_price_net,';                    $sqlV.=number_format($_gks_session['gks']['basket']['products_netvalue'],8,'.','').",";           
$sqlF.='gks_price_fpa,';                    $sqlV.=number_format($_gks_session['gks']['basket']['products_fpa'],8,'.','').",";                
$sqlF.='gks_price_total,';                  $sqlV.=number_format($_gks_session['gks']['basket']['products_total'],8,'.','').",";              
$sqlF.='products_need_apostoli,';           $sqlV.=($_gks_session['gks']['basket']['products_need_apostoli'] ? '1': '0').",";      
$sqlF.='products_need_pliromi,';            $sqlV.=($_gks_session['gks']['basket']['products_need_pliromi'] ? '1': '0').",";       
$sqlF.='kostos_apostolis,';                 $sqlV.=number_format($_gks_session['gks']['basket']['kostos_apostolis'],8,'.','').",";            
$sqlF.='tropos_apostolis,';                 $sqlV.=$_gks_session['gks']['basket']['tropos_apostolis'].",";            
$sqlF.='tropos_apostolis_json,';            $sqlV.="'".$db_link->escape_string(json_encode($_gks_session['gks']['basket']['tropoi_apostolis_all'][ $_gks_session['gks']['basket']['tropos_apostolis'] ]))."',";       
$sqlF.='kostos_pliromis,';                  $sqlV.=number_format($_gks_session['gks']['basket']['kostos_pliromis'],8,'.','').",";             
$sqlF.='tropos_pliromis,';                  $sqlV.=$_gks_session['gks']['basket']['tropos_pliromis'].",";             
$sqlF.='kostos_pliromis_json,';             $sqlV.="'".$db_link->escape_string(json_encode($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]))."',";       
$sqlF.='session_id,';                       $sqlV.="'".$db_link->escape_string($_gks_id_session)."',";                 
$sqlF.='session_basket,';                   $sqlV.="'".$db_link->escape_string(json_encode($_gks_session['gks']['basket']))."',";   
$sqlF.='bank_deposit_9digit,';              $sqlV.="'".$db_link->escape_string($bank_deposit_9digit)."',";        
$sqlF.='delivery_id_8,';                    $sqlV.=$delivery_id_8.",";              

$sqlF.='mydate_add,';                       $sqlV.="NOW(),";                   
$sqlF.='mydate_edit,';                      $sqlV.="NOW(),";    // $sqlV.="'".$db_link->escape_string($_gks_session['gks']['basket']['date_edit'])."',";        
$sqlF.='user_id_add,';                      $sqlV.=$my_wp_user_id.","; 
$sqlF.='user_id_edit,';                     $sqlV.=$my_wp_user_id.","; 
$sqlF.='myip,';                             $sqlV.="'".$db_link->escape_string($gkIP)."',";  

  
$mycv='|';
foreach ($_gks_session['gks']['basket']['coupons'] as $kc => $cv) {
   $mycv.=$kc.'|';
}
if (strlen($mycv)==1) $mycv.='|';
$sqlF.='coupons,';                          $sqlV.="'".$db_link->escape_string($mycv)."',";                    

$sqlF=substr($sqlF,0, strlen($sqlF)-1);$sqlV=substr($sqlV,0, strlen($sqlV)-1);
$sql = "insert into gks_orders (".$sqlF.") values (".$sqlV.");";

//file_put_contents('/var/www/php/www.easyfilesselection.com/tmp/sql.txt',$sql);

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  return $return; }

$id_order = $db_link->insert_id;



if ($address_extra>0) { //iparxousa
  $sql="SELECT id_users_extra_address FROM gks_users_extra_address 
  WHERE id_users_extra_address=".$address_extra." and (
  (gks_users_extra_address.user_id=".$my_wp_user_id." and gks_users_extra_address.user_id>0) or 
  (gks_users_extra_address.order_id=".$id_order.") or 
  (gks_users_extra_address.session_id='".$db_link->escape_string($_gks_id_session)."'))";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }  
  if ($result->num_rows == 0) $address_extra=0;
  
}

if ($address_extra==0) { //nea
  $sql="insert into gks_users_extra_address (
  mydate_add,user_id_add,myip,
  user_id,ea_name,ea_phone,ea_odos,ea_perioxi,ea_poli,ea_tk,ea_country_id,ea_nomos_id,order_id,session_id
  ) values (
  now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",
  '".$db_link->escape_string($dd_name)."',
  '".$db_link->escape_string($dd_phone)."',
  '".$db_link->escape_string($dd_odos)."',
  '".$db_link->escape_string($dd_perioxi)."',
  '".$db_link->escape_string($dd_poli)."',
  '".$db_link->escape_string($dd_tk)."',
  ".$dd_country_id.",
  ".$dd_nomos_id.",
  ".$id_order.",
  '".$db_link->escape_string($_gks_id_session)."'
  )";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }
  
  $address_extra = $db_link->insert_id; 
  $_gks_session['gks']['basket']['address_extra'] = $address_extra;
  
  $sql="update gks_orders set address_extra=".$address_extra." where id_order=".$id_order;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; } 
    
  calc_profilepososto($my_wp_user_id,false);
   
} else if ($address_extra>0) {
  
  $sql="update gks_users_extra_address set 
  ea_name='".$db_link->escape_string($dd_name)."',
  ea_phone='".$db_link->escape_string($dd_phone)."',
  ea_odos='".$db_link->escape_string($dd_odos)."',
  ea_perioxi='".$db_link->escape_string($dd_perioxi)."',
  ea_poli='".$db_link->escape_string($dd_poli)."',
  ea_tk='".$db_link->escape_string($dd_tk)."',
  ea_country_id=".$dd_country_id.",
  ea_nomos_id=".$dd_nomos_id.",
  order_id=".$id_order.",
  session_id='".$db_link->escape_string($_gks_id_session)."',
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  where id_users_extra_address=".$address_extra;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }    
  
  calc_profilepososto($my_wp_user_id,false);
}

//$return = array('success' => false, 'message' => base64_encode('|'.$id_order .'|'));
//return $return;   
  
  
$_gks_session['gks']['basket']['id_object'] = $id_order ;





foreach ($_gks_session['gks']['basket']['products'] as $product) {

   
  $product_price_ekptosi_net=$product['product_id']['product_price_start_all_net']-$product['product_id']['product_price_final_all_net'];
  $product_price_ekptosi_pososto=0;
  if ($product['product_id']['product_price_start_all_net']!=0) $product_price_ekptosi_pososto=round($product_price_ekptosi_net*100/$product['product_id']['product_price_start_all_net'],2);
  


  $sqlF=''; $sqlV='';
  $sqlF.='mydate_add,';                              $sqlV.="NOW(),";                   
  $sqlF.='mydate_edit,';                             $sqlV.="NOW(),";                   
  $sqlF.='user_id_add,';                             $sqlV.=$my_wp_user_id.",";                    
  $sqlF.='user_id_edit,';                            $sqlV.=$my_wp_user_id.",";                    
  $sqlF.='myip,';                                    $sqlV.="'".$db_link->escape_string($gkIP)."',";  
  $sqlF.='order_id,';                                $sqlV.=$id_order.",";    
  $sqlF.='product_set,';                             $sqlV.="'".$db_link->escape_string($product['product_id']['product_set'])."',";   
  $sqlF.='product_id,';                              $sqlV.=$product['product_id']['id_product'].",";                         
  $sqlF.='product_descr,';                           $sqlV.="'".$db_link->escape_string($product['product_id']['product_descr'])."',";   
  $sqlF.='product_is_digital,';                      $sqlV.=($product['product_id']['product_is_digital'] ? '1': '0').",";
  $sqlF.='product_is_simple_download,';              $sqlV.=($product['product_id']['product_is_simple_download'] ? '1': '0').",";
  $sqlF.='product_need_apostoli,';                   $sqlV.=($product['product_id']['product_need_apostoli'] ? '1': '0').",";
  $sqlF.='product_fpa_base_id,';                          $sqlV.=$product['product_id']['product_fpa_base_id'].",";                     
  $sqlF.='product_fpa_id,';                          $sqlV.=$product['product_id']['product_fpa_id'].",";                     
  $sqlF.='product_fpa_pososto,';                     $sqlV.=number_format($product['product_id']['product_fpa_id_array']['fpa_pososto'],8,'.','').",";                     
  $sqlF.='product_fpa_id_json,';                     $sqlV.="'".$db_link->escape_string(json_encode($product['product_id']['product_fpa_id_array']))."',";       
  $sqlF.='product_type,';                            $sqlV.="'".$db_link->escape_string($product['product_id']['product_type'])."',";                
  $sqlF.='product_normal,';                          $sqlV.=$product['product_id']['product_normal'].",";                     
  $sqlF.='product_need_multi_files,';                $sqlV.=($product['product_id']['product_need_multi_files'] ? '1': '0').",";
  $sqlF.='product_need_multi_files_min,';            $sqlV.=$product['product_id']['product_need_multi_files_min'].",";       
  $sqlF.='product_need_multi_files_max,';            $sqlV.=$product['product_id']['product_need_multi_files_max'].",";       
  $sqlF.='product_varos,';                           $sqlV.=number_format($product['product_id']['product_varos'],8,'.','').",";
  $sqlF.='product_ogos_x,';                          $sqlV.=number_format($product['product_id']['product_ogos_x'],8,'.','').",";
  $sqlF.='product_ogos_y,';                          $sqlV.=number_format($product['product_id']['product_ogos_y'],8,'.','').",";
  $sqlF.='product_ogos_z,';                          $sqlV.=number_format($product['product_id']['product_ogos_z'],8,'.','').",";
  //$sqlF.='product_category_ids,';                     $sqlV.=$product['product_id']['product_category_id'].",";                
  $sqlF.='product_sheets,';                          $sqlV.=number_format($product['product_id']['product_sheets'],0,'','').",";
  $sqlF.='product_quantity,';                        $sqlV.=number_format($product['product_id']['product_quantity'],8,'.','').",";
  $sqlF.='product_price_include_vat,';               $sqlV.=intval($product['product_id']['product_price_include_vat']).",";
  $sqlF.='product_price_start_peritem_db,';          $sqlV.=number_format($product['product_id']['product_price_start_peritem_db'],8,'.','').",";
  
  
  $sqlF.='product_price_start_peritem_net,';         $sqlV.=number_format($product['product_id']['product_price_start_peritem_net'],8,'.','').",";
  $sqlF.='product_price_start_peritem_fpa,';         $sqlV.=number_format($product['product_id']['product_price_start_peritem_fpa'],8,'.','').",";
  $sqlF.='product_price_start_peritem_total,';       $sqlV.=number_format($product['product_id']['product_price_start_peritem_total'],8,'.','').",";
  $sqlF.='product_price_start_all_net,';             $sqlV.=number_format($product['product_id']['product_price_start_all_net'],8,'.','').",";
  $sqlF.='product_price_start_all_fpa,';             $sqlV.=number_format($product['product_id']['product_price_start_all_fpa'],8,'.','').",";
  $sqlF.='product_price_start_all_total,';           $sqlV.=number_format($product['product_id']['product_price_start_all_total'],8,'.','').",";
  $sqlF.='product_price_final_peritem_db,';          $sqlV.=number_format($product['product_id']['product_price_final_peritem_db'],8,'.','').",";
  
  $sqlF.='product_price_final_peritem_net,';         $sqlV.=number_format($product['product_id']['product_price_final_peritem_net'],8,'.','').",";
  $sqlF.='product_price_final_peritem_fpa,';         $sqlV.=number_format($product['product_id']['product_price_final_peritem_fpa'],8,'.','').",";
  $sqlF.='product_price_final_peritem_total,';       $sqlV.=number_format($product['product_id']['product_price_final_peritem_total'],8,'.','').",";
  $sqlF.='product_price_final_all_net,';             $sqlV.=number_format($product['product_id']['product_price_final_all_net'],8,'.','').",";
  $sqlF.='product_price_final_all_fpa,';             $sqlV.=number_format($product['product_id']['product_price_final_all_fpa'],8,'.','').",";
  $sqlF.='product_price_final_all_total,';           $sqlV.=number_format($product['product_id']['product_price_final_all_total'],8,'.','').",";
  $sqlF.='product_pricelist_item_id,';               $sqlV.=$product['product_id']['product_pricelist_item_id'].",";          
  $sqlF.='product_pricelist_item_descr,';            $sqlV.="'".$db_link->escape_string($product['product_id']['product_pricelist_item_descr'])."',";       
  $sqlF.='product_pricelist_item_percent,';          $sqlV.=number_format($product['product_id']['product_pricelist_item_percent'],8,'.','').",";
  $sqlF.='product_price_coupon_use,';                $sqlV.="'".$db_link->escape_string($product['product_id']['product_price_coupon_use'])."',";           

  $sqlF.='product_price_ekptosi_net,';               $sqlV.=number_format($product_price_ekptosi_net,8,'.','').",";
  $sqlF.='product_price_ekptosi_pososto,';           $sqlV.=number_format($product_price_ekptosi_pososto,8,'.','').",";

  
//  product_quantity                               product_quantity                 3
//  product_price_original_net                     product_price_start_all_net    241.94
//  product_price_ekptosi_net          
//  product_price_ekptosi_pososto
//  product_price_start_peritem_net                product_price_final_all_net    193.55
//  product_price_fpa                              product_price_final_all_fpa     46.45
//  product_price_total                            product_price_final_all_total  240

//  product_price_include_vat] => 1                product_price_include_vat
//  product_posotita_for_price] => 3               product_quantity
//  product_price_db] => 100                       product_price_start_peritem_db     
//  product_price_net] => 80.65                    product_price_start_peritem_net
//  product_price_net_fpa] => 19.35                product_price_start_peritem_fpa
//  product_price_net_plus_fpa] => 100             product_price_start_peritem_total
//  product_price_net_posotita] => 241.94          product_price_start_all_net
//  product_price_net_posotita_fpa] => 58.06       product_price_start_all_fpa
//  product_price_net_posotita_plus_fpa] => 300    product_price_start_all_total
//  product_price_db_new] => 80                    product_price_final_peritem_db  
//  product_price_new] => 64.52                    product_price_final_peritem_net
//  product_price_new_fpa] => 15.48                product_price_final_peritem_fpa
//  product_price_new_plus_fpa] => 80              product_price_final_peritem_total
//  product_price_new_posotita] => 193.55          product_price_final_all_net
//  product_price_new_posotita_fpa] => 46.45       product_price_final_all_fpa
//  product_price_new_posotita_plus_fpa] => 240    product_price_final_all_total 
//  product_pricelist_item_id] => 1003
//  product_pricelist_item_descr] => Meion 20%
//  product_pricelist_item_percent] => -20
//  product_price_coupon_use] => meion20
//  count_free] => 0

  
  $sqlF=substr($sqlF,0, strlen($sqlF)-1);$sqlV=substr($sqlV,0, strlen($sqlV)-1);
  $sql = "insert into gks_orders_products (".$sqlF.") values (".$sqlV.");";

  //file_put_contents('/var/www/php/www.easyfilesselection.com/tmp/sql.txt',$sql);
  
  //echo $sql;
  //die();
  
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }

 
  
  $id_order_product = $db_link->insert_id;
  
  foreach ($product['objects'] as $object) {
    $sqlF=''; $sqlV='';

    $sqlF.='order_product_id,';    $sqlV.=$id_order_product.",";
    $sqlF.='mykey,';               $sqlV.=$object['key'].",";             
    $sqlF.='mytype,';              $sqlV.="'".$db_link->escape_string($object['type'])."',";          
    $sqlF.='descr,';               $sqlV.="'".$db_link->escape_string($object['descr'])."',";           
    $sqlF.='copies,';              $sqlV.=$object['copies'].",";     
    if (isset($object['subcount'])) {
    $sqlF.='subcount,';            $sqlV.=$object['subcount'].",";        
    }
    
    $sqlF=substr($sqlF,0, strlen($sqlF)-1);$sqlV=substr($sqlV,0, strlen($sqlV)-1);
    $sql = "insert into gks_orders_products_objects (".$sqlF.") values (".$sqlV.");";

    //echo $sql;
    //die();
  
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }
        
    $id_order_product_object = $db_link->insert_id;

    foreach ($object['files'] as $myfile) {
      $sqlF=''; $sqlV='';
      
      $sqlF.='order_product_object_id,';   $sqlV.=$id_order_product_object.",";
      $sqlF.='guid,';                      $sqlV.="'".$db_link->escape_string($myfile['id'])."',";                   
      $sqlF.='dbid,';                      $sqlV.=$myfile['dbid'].",";                   
      $sqlF.='can_download,';              $sqlV.=($myfile['can_download'] ? '1': '0').",";
      $sqlF.='filename,';                  $sqlV.="'".$db_link->escape_string($myfile['filename'])."',";               
      $sqlF.='copies,';                    $sqlV.=$myfile['copies'].",";                 
      $sqlF.='version,';                   $sqlV.=$myfile['version'].",";                
      $sqlF.='filepath,';                  $sqlV.="'".$db_link->escape_string($myfile['filepath'])."',";               
      
      $sqlF=substr($sqlF,0, strlen($sqlF)-1);$sqlV=substr($sqlV,0, strlen($sqlV)-1);
      $sql = "insert into gks_orders_products_objects_files (".$sqlF.") values (".$sqlV.");";
  
  
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        return $return; }
          
      $gks_orders_products_objects_files = $db_link->insert_id;  
          
    }

  }
  
}


if (isset($_gks_session['gks']['basket']['id_hotel_reservation']) and count($_gks_session['gks']['basket']['id_hotel_reservation'])>0) {

  
  $sql="delete from gks_hotel_reservation where id_hotel_reservation in (".implode(',', $_gks_session['gks']['basket']['id_hotel_reservation']).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }    
  
  $sql="delete from gks_hotel_reservation_room where hotel_reservation_id in (".implode(',', $_gks_session['gks']['basket']['id_hotel_reservation']).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }    
  
  $sql="delete from gks_hotel_reservation_room_day where hotel_reservation_id in (".implode(',', $_gks_session['gks']['basket']['id_hotel_reservation']).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return; }    
  
}

$_gks_session['gks']['basket']['id_hotel_reservation'] = array();
if (isset($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations']) and count($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'])>0) {
  
  $reservation_status='005prodraft';
  
  if ($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_type'] =='web') {
    $reservation_status='005prodraft';
  } else if ($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_type'] =='bank') {
    $reservation_status='070wait_payment';
  } else {
    $reservation_status='010draft';
  }
  
  $myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
  //$id_hotel=0;if (isset($_POST['id_hotel'])) $id_hotel=intval($_POST['id_hotel']);

  foreach ($myreservations as $rsrv_aa => $reservation) {

    $check_in_round_time = strtotime($reservation['check_in'].' '.$hotel_params['hotel_default_checkin'].':00');
    $check_out_round_time = strtotime($reservation['check_out'].' '.$hotel_params['hotel_default_checkout'].':00') + 24*60*60;

    $check_in=date('Y-m-d H:i:s',$check_in_round_time );
    $check_out=date('Y-m-d H:i:s',$check_out_round_time );
    
    
    $days_round=hotel_round_days($id_hotel, $check_in, $check_out);
  
    $reservation_guid = guid_for_reservation();
    $sqlF=''; $sqlV='';
    if (isset($reservation['id_hotel_reservation']) and $reservation['id_hotel_reservation']>0) {
    $sqlF.='id_hotel_reservation,';                       $sqlV.=$reservation['id_hotel_reservation'].",";  
    }
    
    $sqlF.='reservation_guid,';                        $sqlV.="'".$db_link->escape_string($reservation_guid)."',";                   
    $sqlF.='reservation_date,';                        $sqlV.="now(),";
    $sqlF.='mydate_add,';                              $sqlV.="NOW(),";                   
    $sqlF.='mydate_edit,';                             $sqlV.="NOW(),";                   
    $sqlF.='user_id_add,';                             $sqlV.=$my_wp_user_id.",";                    
    $sqlF.='user_id_edit,';                            $sqlV.=$my_wp_user_id.",";                    
    $sqlF.='myip,';                                    $sqlV.="'".$db_link->escape_string($gkIP)."',";  
    $sqlF.='order_id,';                                $sqlV.=$id_order.",";
    $sqlF.='reservation_status,';                      $sqlV.="'".$reservation_status."',";                   
    
                 
    $sqlF.='check_in,';                                $sqlV.="'".$check_in."',";
    $sqlF.='check_out,';                               $sqlV.="'".$check_out."',";
    $sqlF.='num_days,';                                $sqlV.=$reservation['num_days'].",";
    $sqlF.='num_adults,';                              $sqlV.=$reservation['adults'].",";
    $sqlF.='num_childs,';                              $sqlV.=$reservation['childs'].",";
    //childs_ages_list
    $sqlF.='rooms_plithos,';                           $sqlV.=$reservation['rooms'].",";    
    $sqlF.='user_id,';                                 $sqlV.=$my_wp_user_id.",";
    $sqlF.='user_email,';                              $sqlV.="'".$db_link->escape_string($user_email)."',";                 
    $sqlF.='user_first_name,';                         $sqlV.="'".$db_link->escape_string($user_first_name)."',";            
    $sqlF.='user_last_name,';                          $sqlV.="'".$db_link->escape_string($user_last_name)."',";            
    $sqlF.='user_mobile,';                             $sqlV.="'".$db_link->escape_string($user_mobile)."',";                
    $sqlF.='user_lang,';                               $sqlV.="'".$db_link->escape_string($user_lang)."',"; 
    $sqlF.='parastatiko,';                      $sqlV.=$_gks_session['gks']['basket']['parastatiko'].",";    
    if ($_gks_session['gks']['basket']['parastatiko'] == 1) {              
    $sqlF.='eponimia,';                         $sqlV.="'".$db_link->escape_string($user_eponimia)."',";                   
    $sqlF.='title,';                            $sqlV.="'".$db_link->escape_string($user_title)."',";                      
    $sqlF.='afm,';                              $sqlV.="'".$db_link->escape_string($user_afm)."',";                        
    $sqlF.='doy,';                              $sqlV.="'".$db_link->escape_string($user_doy)."',";                        
    $sqlF.='epaggelma,';                        $sqlV.="'".$db_link->escape_string($user_epaggelma)."',";                  
    }    
    
    $sqlF.='ma_odos,';                            $sqlV.="'".$db_link->escape_string($user_ma_odos)."',";                    
    $sqlF.='ma_perioxi,';                         $sqlV.="'".$db_link->escape_string($user_ma_perioxi)."',";                    
    $sqlF.='ma_poli,';                            $sqlV.="'".$db_link->escape_string($user_ma_poli)."',";                    
    $sqlF.='ma_tk,';                              $sqlV.="'".$db_link->escape_string($user_ma_tk)."',";                      
    $sqlF.='ma_country_id,';                      $sqlV.=$user_ma_country_id.",";              
    $sqlF.='ma_nomos_id,';                        $sqlV.=$user_ma_nomos_id.",";                
    //user_notes

$sqlF.='gks_price_original_net,';           $sqlV.=number_format($_gks_session['gks']['basket']['products_original_netvalue'],8,'.','').",";           
$sqlF.='gks_price_net,';                    $sqlV.=number_format($_gks_session['gks']['basket']['products_netvalue'],8,'.','').",";           
$sqlF.='gks_price_fpa,';                    $sqlV.=number_format($_gks_session['gks']['basket']['products_fpa'],8,'.','').",";                
$sqlF.='gks_price_total,';                  $sqlV.=number_format($_gks_session['gks']['basket']['products_total'],8,'.','').",";              

    
    $sqlF.='fiscal_position_id,';                 $sqlV.=$fiscal_position_id.",";         
    $sqlF.='pricelist_id,';                       $sqlV.=$pricelist_id.",";    
//def_ekptosi

$sqlF.='is_other,';                         $sqlV.=intval($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservation_other']).",";            
$sqlF.='other_first_name,';                 $sqlV.="'".$db_link->escape_string($other_first_name)."',";            
$sqlF.='other_last_name,';                  $sqlV.="'".$db_link->escape_string($other_last_name)."',";             
$sqlF.='other_email,';                      $sqlV.="'".$db_link->escape_string($other_email)."',";                 
$sqlF.='other_mobile,';                     $sqlV.="'".$db_link->escape_string($other_mobile)."',";                
$sqlF.='other_lang,';                       $sqlV.="'".$db_link->escape_string($other_lang)."',";                
$sqlF.='other_ma_odos,';                    $sqlV.="'".$db_link->escape_string($other_ma_odos)."',";                    
$sqlF.='other_ma_perioxi,';                 $sqlV.="'".$db_link->escape_string($other_ma_perioxi)."',";                    
$sqlF.='other_ma_poli,';                    $sqlV.="'".$db_link->escape_string($other_ma_poli)."',";                    
$sqlF.='other_ma_tk,';                      $sqlV.="'".$db_link->escape_string($other_ma_tk)."',";                      
$sqlF.='other_ma_country_id,';              $sqlV.=intval($other_ma_country_id).",";              
$sqlF.='other_ma_nomos_id,';                $sqlV.=intval($other_ma_nomos_id).",";                


    
$sqlF.='products_posotita,';                $sqlV.=number_format($_gks_session['gks']['basket']['products_posotita'],8,'.','').",";          
$sqlF.='products_varos,';                   $sqlV.=number_format($_gks_session['gks']['basket']['products_varos'],8,'.','').",";             
$sqlF.='products_ogos,';                    $sqlV.=number_format($_gks_session['gks']['basket']['products_ogos'],8,'.','').",";               
$sqlF.='products_ogos_max_x,';              $sqlV.=number_format($_gks_session['gks']['basket']['products_ogos_max_x'],8,'.','').",";         
$sqlF.='products_ogos_max_y,';              $sqlV.=number_format($_gks_session['gks']['basket']['products_ogos_max_y'],8,'.','').",";         
$sqlF.='products_ogos_max_z,';              $sqlV.=number_format($_gks_session['gks']['basket']['products_ogos_max_z'],8,'.','').",";         
$sqlF.='products_need_apostoli,';           $sqlV.=($_gks_session['gks']['basket']['products_need_apostoli'] ? '1': '0').",";      
$sqlF.='products_need_pliromi,';            $sqlV.=($_gks_session['gks']['basket']['products_need_pliromi'] ? '1': '0').",";       
$sqlF.='kostos_apostolis,';                 $sqlV.=number_format($_gks_session['gks']['basket']['kostos_apostolis'],8,'.','').",";            
$sqlF.='tropos_apostolis,';                 $sqlV.=$_gks_session['gks']['basket']['tropos_apostolis'].",";            
$sqlF.='tropos_apostolis_json,';            $sqlV.="'".$db_link->escape_string(json_encode($_gks_session['gks']['basket']['tropoi_apostolis_all'][ $_gks_session['gks']['basket']['tropos_apostolis'] ]))."',";       
$sqlF.='kostos_pliromis,';                  $sqlV.=number_format($_gks_session['gks']['basket']['kostos_pliromis'],8,'.','').",";             
$sqlF.='tropos_pliromis,';                  $sqlV.=$_gks_session['gks']['basket']['tropos_pliromis'].",";             
$sqlF.='kostos_pliromis_json,';             $sqlV.="'".$db_link->escape_string(json_encode($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]))."',";       
$sqlF.='session_id,';                              $sqlV.="'".$db_link->escape_string($_gks_id_session)."',";                 
$sqlF.='session_basket,';                          $sqlV.="'".$db_link->escape_string(json_encode($_gks_session['gks']['basket']))."',";   
$sqlF.='bank_deposit_9digit,';                     $sqlV.="'".$db_link->escape_string($bank_deposit_9digit)."',";        
$sqlF.='delivery_id_8,';                    $sqlV.=$delivery_id_8.",";              

    
    
    $sqlF.='coupons,';                                 $sqlV.="'".$db_link->escape_string($mycv)."',";          

    
    
    $sqlF=substr($sqlF,0, strlen($sqlF)-1);$sqlV=substr($sqlV,0, strlen($sqlV)-1);
    $sql = "insert into gks_hotel_reservation (".$sqlF.") values (".$sqlV.");";
  
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      return $return; }
    
    $id_hotel_reservation = $db_link->insert_id;
    $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'][$rsrv_aa]['id_hotel_reservation'] = $id_hotel_reservation;
      
    $_gks_session['gks']['basket']['id_hotel_reservation'][] = $id_hotel_reservation ;
    $roolist_day=array();
    foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
      foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
        $sqlF=''; $sqlV='';
        $sqlF.='mydate_add,';                              $sqlV.="NOW(),";                   
        $sqlF.='mydate_edit,';                             $sqlV.="NOW(),";                   
        $sqlF.='user_id_add,';                             $sqlV.=$my_wp_user_id.",";                    
        $sqlF.='user_id_edit,';                            $sqlV.=$my_wp_user_id.",";                    
        $sqlF.='myip,';                                    $sqlV.="'".$db_link->escape_string($gkIP)."',";  
        $sqlF.='hotel_reservation_id,';                    $sqlV.=$id_hotel_reservation.",";
        $sqlF.='hotel_room_id,';                           $sqlV.=$myroom['room_item_id'].",";
        $sqlF.='rnum_adults,';                             $sqlV.=($myroom['rnum_adults'] < 0 ? 0 : $myroom['rnum_adults']).",";
        $sqlF.='rnum_childs,';                             $sqlV.=($myroom['rnum_childs'] < 0 ? 0 : $myroom['rnum_childs']).",";
        $sqlF.='product_price_final_all_total,';                               $sqlV.=number_format($selroom['roomtype']['price'],8,'.','').",";
        if ($myroom['is_same']!=0) {
          $sqlF.='ruser_id,';                              $sqlV.="-1,";
        } else {
          $sqlF.='ruser_id,';                              $sqlV.="0,";
          $sqlF.='ruser_first_name,';                      $sqlV.="'".$db_link->escape_string($myroom['first_name'])."',"; 
          $sqlF.='ruser_last_name,';                       $sqlV.="'".$db_link->escape_string($myroom['last_name'])."',"; 
          $sqlF.='ruser_email,';                           $sqlV.="'".$db_link->escape_string($myroom['email'])."',"; 
          $sqlF.='ruser_mobile,';                          $sqlV.="'".$db_link->escape_string($myroom['mobile'])."',"; 
          $sqlF.='ruser_lang,';                            $sqlV.="'".$db_link->escape_string($myroom['lang'])."',"; 
          $sqlF.='ruser_ma_odos,';                         $sqlV.="'".$db_link->escape_string($myroom['ma_odos'])."',"; 
          $sqlF.='ruser_ma_perioxi,';                      $sqlV.="'".$db_link->escape_string($myroom['ma_perioxi'])."',"; 
          $sqlF.='ruser_ma_poli,';                         $sqlV.="'".$db_link->escape_string($myroom['ma_poli'])."',"; 
          $sqlF.='ruser_ma_tk,';                           $sqlV.="'".$db_link->escape_string($myroom['ma_tk'])."',"; 
          $sqlF.='ruser_ma_country_id,';                   $sqlV.=intval($myroom['ma_country_id']).","; 
          $sqlF.='ruser_ma_nomos_id,';                     $sqlV.=intval($myroom['ma_nomos_id']).","; 
          $sqlF.='rsxolio,';                               $sqlV.="'',"; 
          $sqlF.='ruser_fiscal_position_id,';              $sqlV.=$fiscal_position_id.","; 
          $sqlF.='ruser_pricelist_id,';                    $sqlV.=$pricelist_id.","; 
          
        }
        
        
    //ruser_lang
        
        
        $sqlF=substr($sqlF,0, strlen($sqlF)-1);$sqlV=substr($sqlV,0, strlen($sqlV)-1);
        $sql = "insert into gks_hotel_reservation_room (".$sqlF.") values (".$sqlV.");";
  
        $result = $db_link->query($sql);
        if (!$result) {
          //debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα').'<br>'.$db_link->error));
          return $return; }
        
        $id_hotel_reservation_room = $db_link->insert_id;
        
        $roolist_day[]=array('delete'=>0, 'hotel_room_id'=> $myroom['room_item_id'], 'recid'=> $id_hotel_reservation_room, 'hotel_type_room_id'=>$selroom['roomtype']['id']);        
      }
    }
  
    
    gks_hotel_reservation_room_day_recs($id_hotel_reservation,$roolist_day,$reservation_status,$days_round['check_in_round_time'],$days_round['check_out_round_time']);

  }
}



//$return = array('success' => false, 'message' => base64_encode('OK1'));
//echo json_encode($return); die();

$mybackurl='';

$pliroteo=$_gks_session['gks']['basket']['products_total'] + $_gks_session['gks']['basket']['kostos_apostolis'] + $_gks_session['gks']['basket']['kostos_pliromis'];


$_gks_session['gks']['confirm']['id_object'] = $id_order;
$_gks_session['gks']['confirm']['payment_acquirer_type']=$_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_type'];
$_gks_session['gks']['confirm']['poso']=$pliroteo;
$_gks_session['gks']['confirm']['bank9digit']=$bank_deposit_9digit;  



$payment_tag = 'OrderID: '.$id_order;
if (count($_gks_session['gks']['basket']['id_hotel_reservation'])>0) {
  $payment_tag='Reservation: '.implode(',', $_gks_session['gks']['basket']['id_hotel_reservation']);
}


//$return = array('success' => false, 'message' => base64_encode('<pre>order_guid: '.$order_guid."\r\n".$bank_deposit_9digit."\r\n".$id_order),'url' => base64_encode('mprrrr'));
//$return = array('success' => false, 'message' => base64_encode('<pre>'.gks_set_lang_url()),'url' => base64_encode('mprrrr'));
//return $return; 


switch ($_gks_session['gks']['confirm']['payment_acquirer_type']) {
  case 'bank': 
    $mybackurl ='/confirm'.gks_set_lang_url();
    break;
  case 'delivery': 
    $mybackurl ='/confirm'.gks_set_lang_url();
    break;
  case 'none': 
    $mybackurl ='/confirm'.gks_set_lang_url();
    break;
  case 'store': 
    $mybackurl ='/confirm'.gks_set_lang_url();
    break;
  case 'web': 
    unset($_gks_session['gks']['confirm']); 
    
    
    if ($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_name'] == 'Paypal' ) {
    	

      $paypal_client_id_secret=$GKS_PAYPAL_REAL_CLIENT_ID.':'.$GKS_PAYPAL_REAL_SECRET;
      if ($GKS_PAYPAL_SANDBOX) $paypal_client_id_secret=$GKS_PAYPAL_SAND_CLIENT_ID.':'.$GKS_PAYPAL_SAND_SECRET;
      
      //Get an access token
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.".($GKS_PAYPAL_SANDBOX ? 'sandbox.' : '')."paypal.com/v1/oauth2/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=client_credentials&",
        CURLOPT_HTTPHEADER => array(
          'accept: application/json',
          'accept-language: en_US',
          'content-type: application/x-www-form-urlencoded',
        ),
        CURLOPT_USERPWD => $paypal_client_id_secret, 
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      ));


      $response_access_token = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      if ($err) {
          debug_mail(false,'paypal 1/5 error access_token',$response_access_token.' '.$err);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν είναι δυνατή η σύνδεση με το Paypal').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
          return $return;
      } else {
        $response_access_token=json_decode($response_access_token,true);
      }  
      $paypal_access_token=$response_access_token['access_token'];
      debug_mail(false,'paypal 1/5 access_token '.$_gks_id_session , print_r($response_access_token,true));


      //Create Order
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.".($GKS_PAYPAL_SANDBOX ? 'sandbox.' : '')."paypal.com/v2/checkout/orders",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => '{
        "intent": "CAPTURE",
        "purchase_units": [
          {
            "reference_id": "PUHF",
            "amount": {
              "currency_code": "EUR",
              "value": "'.number_format($pliroteo, 2, '.', '').'",
              "breakdown" : {
                "item_total" :{
                  "currency_code": "EUR",
                  "value": "'.number_format($pliroteo, 2, '.', '').'"
                },
                "tax_total" :{
                  "currency_code": "EUR",
                  "value": "0"
                }
              }
            },
            "items": [
              {
                "name": "'.$payment_tag.'",
                "unit_amount":{
                  "currency_code": "EUR",
                  "value": "'.number_format($pliroteo, 2, '.', '').'"
                },
                "tax":{
                  "currency_code": "EUR",
                  "value": "0"
                },
                "quantity":"1"
              }     
            
            ]
          }
        ],
        "application_context": {
          "return_url": "'.GKS_SITE_URL.'my/paypal-success.php",
          "cancel_url": "'.GKS_SITE_URL.'my/paypal-cancel.php",
          "brand_name": "'.$GKS_SITE_HUMAN_NAME.'",
          "shipping_preference" : "NO_SHIPPING"
        },
        "email":"'.$user_email.'"
      
          
      }',
        CURLOPT_HTTPHEADER => array(
          'accept: application/json',
          'accept-language: en_US',
          'authorization: Bearer '.$paypal_access_token,
          'content-type: application/json'
        ),
      ));


      $response_create_order = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      
      if ($err) {
          debug_mail(false,'paypal 2/5 error Create_Order',$response_create_order.' '.$err);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν είναι δυνατή η σύνδεση με το Paypal').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
          return $return; 
      } else {
        $response_create_order=json_decode($response_create_order,true);
      }
      
      if (isset($response_create_order['status']) == false || $response_create_order['status'] != 'CREATED') {
              debug_mail(false,'paypal 2/5 Create_Order ',print_r($response_create_order,true));
              $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν είναι δυνατή η σύνδεση με το Paypal').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
              return $return; }  
              
      debug_mail(false,'paypal 2/5 Create_Order '.$_gks_id_session , print_r($response_create_order,true));
        

      
      $mybackurl='';
      $paypal_order_id=$response_create_order['id'];
      foreach ($response_create_order['links'] as $value) {
        if ($value['rel']=='approve') $mybackurl=$value['href'];
      } 
      $_gks_session['gks']['paypal']=array();
      $_gks_session['gks']['paypal']['TOKEN'] = $paypal_order_id;
      $_gks_session['gks']['paypal']['ACK'] = '';
      $_gks_session['gks']['paypal']['CORRELATIONID'] = '';
      

      $tropos_pliromis=4;
      $tropos_pliromis_descr='Paypal';
      $tropos_pliromis_type='web';
      $kostos_pliromis=0;

      $sql="insert into gks_payments_paypal (paypal_add_date,token,request1_json,status,ACK,CORRELATIONID,poso,mytype,payment_fee) values (
      NOW(),
      '".$db_link->escape_string($_gks_session['gks']['paypal']['TOKEN'])."',
      '".$db_link->escape_string(json_encode($_gks_session['gks']['paypal']))."',
      'draft',
      '".$db_link->escape_string($_gks_session['gks']['paypal']['ACK'])."',
      '".$db_link->escape_string($_gks_session['gks']['paypal']['CORRELATIONID'])."',
      ".number_format($pliroteo, 8,'.','').",
      1,
      ".number_format($kostos_pliromis, 8,'.','').")";      
      
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'payment-edit.php error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        return $return; }	 	    
      
      $_gks_session['gks']['basket']['payment']['table_id'] = $db_link->insert_id;
      $_gks_session['gks']['basket']['payment']['table_name'] = 'gks_payments_paypal';
      
      $sql="insert into gks_payments (payment_add_date,payment_status,order_id,poso_payment,tropos_pliromis_id,tropos_pliromis_descr,tropos_pliromis_type,
      kostos_pliromis, payment_fee, table_name,table_id,payment_tag) values (
      NOW(),
      'draft',
      ".$_gks_session['gks']['basket']['id_object'].",
      ".number_format($pliroteo, 8,'.','').",
      ".$tropos_pliromis.",
      '".$db_link->escape_string($tropos_pliromis_descr) ."',
      '".$db_link->escape_string($tropos_pliromis_type) ."',
      ".number_format($kostos_pliromis, 8,'.','').",
      0,
      '".$db_link->escape_string($_gks_session['gks']['basket']['payment']['table_name'])."',
      ".$_gks_session['gks']['basket']['payment']['table_id'].",
      '".$db_link->escape_string($payment_tag)."'
      )";
      
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'payment-edit.php error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        return $return; }	   
      
      $_gks_session['gks']['basket']['payment']['id_payment'] = $db_link->insert_id;
      
      
          
      
      
      debug_mail(false,'paypal 3/5 '.$_gks_id_session , print_r($_gks_session['gks']['paypal'],true));      
      

      
      $return = array('success' => true, 'message' => base64_encode('OK'),'url' => base64_encode($mybackurl));
      return $return;       
      
      $return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($response_create_order,true)));
      return $return; 


      
      //$return = array('success' => false, 'message' => base64_encode('111111111111111'));
      //return $return;     	
      
    } else if ($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_name'] == 'alphabank' ) {
      
      
      $_gks_session['gks']['alphabank']['id_object'] = $id_order;
      $_gks_session['gks']['alphabank']['order_guid'] = $order_guid;
      $_gks_session['gks']['alphabank']['pliroteo']=$pliroteo;
      if ($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_env_test'] != 0) {
        //$_gks_session['gks']['alphabank']['pliroteo']='0.1';        
      }


	    $sql="insert into gks_payments_alphabank (alphabank_add_date,request1_json,status,poso,mytype,payment_fee) values (
	    NOW(),
	    '".$db_link->escape_string(json_encode($_gks_session['gks']['alphabank']))."',
	    'draft',
	    ".number_format($pliroteo, 8,'.','').",
	    1,
	    ".number_format($_gks_session['gks']['basket']['kostos_pliromis'], 8,'.','').")";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        return $return; }	 	    
	    
	    $_gks_session['gks']['basket']['payment']['table_id'] = $db_link->insert_id;
	    $_gks_session['gks']['basket']['payment']['table_name'] = 'gks_payments_alphabank';

    
	    $sql="insert into gks_payments (payment_add_date,payment_status,order_id,poso_payment,tropos_pliromis_id,tropos_pliromis_descr,tropos_pliromis_type,
	    kostos_pliromis, payment_fee, table_name,table_id) values (
	    NOW(),
	    'draft',
	    ".$_gks_session['gks']['basket']['id_object'].",
	    ".number_format($pliroteo, 8,'.','').",
	    ".$_gks_session['gks']['basket']['tropos_pliromis'].",
	    '".$db_link->escape_string($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_name']) ."',
	    '".$db_link->escape_string($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_type']) ."',
	    ".number_format($_gks_session['gks']['basket']['kostos_pliromis'], 8,'.','').",
	    0,
	    '".$db_link->escape_string($_gks_session['gks']['basket']['payment']['table_name'])."',
	    ".$_gks_session['gks']['basket']['payment']['table_id'].")";
	    
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        return $return; }	   
	   
	    $_gks_session['gks']['basket']['payment']['id_payment'] = $db_link->insert_id;
	    
      
      $mybackurl = '/my/alphabank.php';
      
      debug_mail(false,'alphabank start '.$_gks_id_session , '');
      //$return = array('success' => false, 'message' => base64_encode('Aftos o tropos pliromis einai prosorina apenergopoiimenos.epilexte kapoion allo.'));
      //return $return; 
      break;       
      
    } else if ($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_name'] == 'piraeusbank' or 
               $_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_name'] == 'MasterPass wallet' ) {
      $_gks_session['gks']['piraeusbank']['id_object'] = $id_order;
      $_gks_session['gks']['piraeusbank']['order_guid'] = $order_guid;
      $_gks_session['gks']['piraeusbank']['pliroteo']=$pliroteo;


      debug_mail(false,'piraeusbank start', '');
      

      

      
      try {
        $soap = new SoapClient("https://paycenter.piraeusbank.gr/services/tickets/issuer.asmx?WSDL");
        
        $MerchantReference= $id_order.'x'.$order_guid.'_'.rand(1000,9999);
        $doseis=0;
        $ticketRequest = array(
            'AcquirerId' => ($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_AcquirerID : $GKS_PIRAEUSBANK_REAL_AcquirerID),
            'MerchantId' => ($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_MerchantID : $GKS_PIRAEUSBANK_REAL_MerchantID),
            'PosId' => ($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_PosID : $GKS_PIRAEUSBANK_REAL_PosID),
            'Username' => ($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_UserName : $GKS_PIRAEUSBANK_REAL_UserName),
            'Password' => hash('md5', ($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_Password : $GKS_PIRAEUSBANK_REAL_Password)),
            'RequestType' => '02',
            'CurrencyCode' => '978',
            'MerchantReference' => $MerchantReference,
            'Amount' => number_format($pliroteo,2,'.',''),
            'Installments' => $doseis,
            'ExpirePreauth' => 0,
            'Bnpl' => '0',
            'Parameters' => $id_order.'',
//            'MasterPass' => 'yes',
        );
        
        //bill details
        $BillAddrCity=piraeusbank_filter_text($_gks_session['gks']['basket']['user']['ma_poli']);
        if ($BillAddrCity<>'') $ticketRequest['BillAddrCity'] = $BillAddrCity;
        $BillAddrCountry=0;
        if ($_gks_session['gks']['basket']['user']['ma_country_id']>0) {
          $sql="select country_ISO_3166_1 from gks_country where id_country=".$_gks_session['gks']['basket']['user']['ma_country_id']." and country_ISO_3166_1>0";
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
            return $return; }
            
          if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $BillAddrCountry = $row['country_ISO_3166_1'];
          }
        }
        if ($BillAddrCountry>0) $ticketRequest['BillAddrCountry'] = $BillAddrCountry;
        $BillAddrLine1=piraeusbank_filter_text($_gks_session['gks']['basket']['user']['ma_odos']);
        if ($BillAddrLine1<>'') $ticketRequest['BillAddrLine1'] = $BillAddrLine1;
        $BillAddrLine2=piraeusbank_filter_text($_gks_session['gks']['basket']['user']['ma_perioxi']);
        if ($BillAddrLine2<>'') $ticketRequest['BillAddrLine2'] = $BillAddrLine2;
        $BillAddrLine3='';
        $BillAddrState='';
        if ($_gks_session['gks']['basket']['user']['ma_nomos_id']>0) {
          $lang_prepare_gks_nomoi=gks_lang_data_obj_prepare('gks_nomoi','default');
          gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomoi, array('nomos_descr'));  
          
          $sql="select nomos_ISO_3166_2 ,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi)."
          from ".$lang_prepare_gks_nomoi['sql']['from1']." gks_nomoi 
          ".$lang_prepare_gks_nomoi['sql']['from2']."
          where id_nomos=".$_gks_session['gks']['basket']['user']['ma_nomos_id'];
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
            return $return; }
            
          if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $BillAddrLine3 = $row['nomos_descr'];
            $BillAddrState = trim_gks($row['nomos_ISO_3166_2']);
          }
        }
        if ($BillAddrLine3<>'') $ticketRequest['BillAddrLine3'] = $BillAddrLine3;
        $BillAddrPostCode=piraeusbank_filter_text($_gks_session['gks']['basket']['user']['ma_tk']);
        if ($BillAddrPostCode<>'') $ticketRequest['BillAddrPostCode'] = $BillAddrPostCode;
        if ($BillAddrState<>'') $ticketRequest['BillAddrState'] = $BillAddrState;
        
        
        
        

        //ship details
        $ShipAddrCity=piraeusbank_filter_text($_gks_session['gks']['basket']['destination_data']['poli']);
        if ($ShipAddrCity<>'') $ticketRequest['ShipAddrCity'] = $ShipAddrCity;
        $ShipAddrCountry=0;
        if ($_gks_session['gks']['basket']['user']['ma_country_id']>0) {
          $sql="select country_ISO_3166_1 from gks_country where id_country=".$_gks_session['gks']['basket']['destination_data']['country_id']." and country_ISO_3166_1>0";
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
            return $return; }
            
          if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $ShipAddrCountry = $row['country_ISO_3166_1'];
          }
        }
        if ($ShipAddrCountry>0) $ticketRequest['ShipAddrCountry'] = $ShipAddrCountry;
        $ShipAddrLine1=piraeusbank_filter_text($_gks_session['gks']['basket']['destination_data']['odos']);
        if ($ShipAddrLine1<>'') $ticketRequest['ShipAddrLine1'] = $ShipAddrLine1;
        $ShipAddrLine2=piraeusbank_filter_text($_gks_session['gks']['basket']['destination_data']['perioxi']);
        if ($ShipAddrLine2<>'') $ticketRequest['ShipAddrLine2'] = $ShipAddrLine2;
        $ShipAddrLine3='';
        $ShipAddrState='';
        if ($_gks_session['gks']['basket']['destination_data']['nomos_id']>0) {
          $lang_prepare_gks_nomoi=gks_lang_data_obj_prepare('gks_nomoi','default');
          if ($lang_prepare_gks_nomoi['success']==false) die($lang_prepare_gks_nomoi['message']);
          gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomoi, array('nomos_descr'));
          
          
          $sql="select nomos_ISO_3166_2,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomoi).",
          from ".$lang_prepare_gks_nomoi['sql']['from1']." gks_nomoi 
          ".$lang_prepare_gks_nomoi['sql']['from2']."
          where id_nomos=".$_gks_session['gks']['basket']['destination_data']['nomos_id'];
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
            return $return; }
            
          if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $ShipAddrLine3 = $row['nomos_descr'];
            $ShipAddrState=trim_gks($row['nomos_ISO_3166_2']);
          }
        }
        if ($ShipAddrLine3<>'') $ticketRequest['ShipAddrLine3'] = $ShipAddrLine3;
        $ShipAddrPostCode=piraeusbank_filter_text($_gks_session['gks']['basket']['destination_data']['tk']);
        if ($ShipAddrPostCode<>'') $ticketRequest['ShipAddrPostCode'] = $ShipAddrPostCode;
        if ($ShipAddrState<>'') $ticketRequest['ShipAddrState'] = $ShipAddrState;

                
        $piraeusbankEmail = $_gks_session['gks']['basket']['user']['email'];
        if ($piraeusbankEmail<>'') $ticketRequest['Email'] = $piraeusbankEmail;
        
        $piraeusbankMobilePhone = $_gks_session['gks']['basket']['user']['mobile'];
        if ($piraeusbankMobilePhone<>'') $ticketRequest['MobilePhone'] = $piraeusbankMobilePhone;
        
        
        
        $xml = array(
            'Request' => $ticketRequest
        );
        debug_mail(false,'piraeusbank ticket send', '<pre>'.print_r($ticketRequest,true).'</pre>');
        
        $oResult = $soap->IssueNewTicket($xml);
        debug_mail(false,'piraeusbank ticket response', '<pre>'.$oResult->IssueNewTicketResult->ResultCode."\n".$oResult->IssueNewTicketResult->TranTicket.'</pre>');
        
        
        $mytimestamp = strtotime($oResult->IssueNewTicketResult->Timestamp);
        $mytimestamp=date('Y-m-d H:i:s',$mytimestamp );
        
  	    $sql="insert into gks_payments_piraeusbank (piraeusbank_add_date,
  	    order_id,order_guid,status,poso,payment_fee,
  	    ticketRequest_json,sandbox,
  	    ResultCode,ResultDescription,TranTicket,myTimestamp,MinutesToExpiration,
  	    MerchantReference,doseis
  	    ) values (
  	    NOW(),
  	    ".$id_order.",
  	    '".$db_link->escape_string($order_guid)."',
  	    'draft',
  	    ".number_format($pliroteo, 8,'.','').",
  	    ".number_format($_gks_session['gks']['basket']['kostos_pliromis'], 8,'.','').",
  	    '".$db_link->escape_string(json_encode($ticketRequest))."',
  	    ".($GKS_PIRAEUSBANK_SANDBOX ? '1' :'0').",
  	    ".$oResult->IssueNewTicketResult->ResultCode.",
  	    '".$db_link->escape_string($oResult->IssueNewTicketResult->ResultDescription)."',
  	    '".$db_link->escape_string($oResult->IssueNewTicketResult->TranTicket)."',
  	    '".$db_link->escape_string($mytimestamp)."',
  	    ".intval($oResult->IssueNewTicketResult->MinutesToExpiration).",
  	    '".$db_link->escape_string($MerchantReference)."',
  	    ".$doseis.")";
  	    
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
          return $return; }	 	    
  	    
  	    $_gks_session['gks']['basket']['payment']['table_id'] = $db_link->insert_id;
  	    $_gks_session['gks']['basket']['payment']['table_name'] = 'gks_payments_piraeusbank';
  
      
  	    $sql="insert into gks_payments (payment_add_date,payment_status,order_id,poso_payment,tropos_pliromis_id,tropos_pliromis_descr,tropos_pliromis_type,
  	    kostos_pliromis, payment_fee, table_name,table_id) values (
  	    NOW(),
  	    'draft',
  	    ".$_gks_session['gks']['basket']['id_object'].",
  	    ".number_format($pliroteo, 8,'.','').",
  	    ".$_gks_session['gks']['basket']['tropos_pliromis'].",
  	    '".$db_link->escape_string($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_name']) ."',
  	    '".$db_link->escape_string($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_type']) ."',
  	    ".number_format($_gks_session['gks']['basket']['kostos_pliromis'], 8,'.','').",
  	    0,
  	    '".$db_link->escape_string($_gks_session['gks']['basket']['payment']['table_name'])."',
  	    ".$_gks_session['gks']['basket']['payment']['table_id'].")";

        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
          return $return; }	   
  	   
  	    $_gks_session['gks']['basket']['payment']['id_payment'] = $db_link->insert_id;
  	            
        if ($oResult->IssueNewTicketResult->ResultCode != 0) {
          debug_mail(false,'piraeusbank soap error 2 '.$_gks_id_session , $oResult->IssueNewTicketResult->ResultCode.' '.$oResult->IssueNewTicketResult->ResultDescription);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την επικοινωνία με την υπηρεσία της piraeusbank.gr<br>Παρακαλώ δοκιμάστε αργότερα')));
          return $return;           
        }
        $gks_payment_acquirer_piraeusbank_template_form='<form action="https://paycenter.piraeusbank.gr/redirection/pay.aspx" method="POST" accept-charset="UTF-8" id="gks_payment_acquirer_piraeusbank_form" enctype="application/x-www-form-urlencoded">'.
'<input name="AcquirerId" type="hidden" value="'.($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_AcquirerID : $GKS_PIRAEUSBANK_REAL_AcquirerID).'" />'.
'<input name="MerchantId" type="hidden" value="'.($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_MerchantID : $GKS_PIRAEUSBANK_REAL_MerchantID).'" />'.
'<input name="PosId" type="hidden" value="'.($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_PosID : $GKS_PIRAEUSBANK_REAL_PosID).'" />'.
'<input name="User" type="hidden" value="'.($GKS_PIRAEUSBANK_SANDBOX ? $GKS_PIRAEUSBANK_SAND_UserName : $GKS_PIRAEUSBANK_REAL_UserName).'" />'.
'<input name="LanguageCode" type="hidden" value="'.($_gks_session['gks']['ui_lang']=='el-GR' ? 'el-GR' : 'en-US').'" />'.
'<input name="MerchantReference" type="hidden" value="'.$MerchantReference.'" />'.
'<input name="ParamBackLink" type="hidden" value="'.gks_set_lang_url().'" />'.
($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_name'] == 'MasterPass wallet' ? '<input name="MasterPass" type="hidden" value="yes" />' : '' ).
'<input type="submit" value="Check out" id="gks_payment_acquirer_piraeusbank_submit" style="position:absolute;top:-10000px;left:-10000px;"/>'.
'</form>';


        debug_mail(false,'piraeusbank form', htmlentities (str_replace('hidden', 'text', $gks_payment_acquirer_piraeusbank_template_form)));
        
        //$return = array('success' => true, 'message' => base64_encode(('xxxxxxx '.$oResult->IssueNewTicketResult->TranTicket.' '.$oResult->IssueNewTicketResult->Timestamp.' '.$oResult->IssueNewTicketResult->MinutesToExpiration.'')));
        $mybackurl = '/';
        //echo 'ffffffffff';
        
        //file_put_contents('/var/www/php/www.easyfilesselection.com/logs/form.txt',base64_decode($gks_payment_acquirer_piraeusbank_template_form));
        //$return = array('success' => false, 'message' => base64_encode('ok'),'piraeusbank' => true,'piraeusbank_form' => base64_encode($gks_payment_acquirer_piraeusbank_template_form));
        //return $return;   
                  
        $return = array('success' => true, 'message' => base64_encode('OK'),'url' => '#', 'piraeusbank' => true, 'piraeusbank_form'=>base64_encode($gks_payment_acquirer_piraeusbank_template_form));
        return $return; 
              
      } catch (SoapFault $fault) {
        debug_mail(false,'piraeusbank soap error '.$_gks_id_session , $fault);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την επικοινωνία με την υπηρεσία της piraeusbank.gr<br>Παρακαλώ δοκιμάστε αργότερα')));
        return $return; 
      }
                
                

    
          
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την επικοινωνία με την υπηρεσία της piraeusbank.gr<br>Παρακαλώ δοκιμάστε αργότερα')));
      return $return; 


    } else {
      debug_mail(false,'payment error (1)'.$_gks_id_session , '');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα τρόπου πληρωμής.<br>Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
      return $return; 
      break;       
      
    }
    
    break;
  default:
    debug_mail(false,'payment error (2)'.$_gks_id_session , '');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα τρόπου πληρωμής.<br>Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
    return $return; 
    break;   
}


gks_erp_cookie_save($gks_erp_cookie_id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'url' => base64_encode($mybackurl));
return $return; 



  
}

