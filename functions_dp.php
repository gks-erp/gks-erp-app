<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/




//functions for delivery and payment
function my_alphabank_settings() {
  global $GKS_ALPHABANK_REAL_MID;
  global $GKS_ALPHABANK_REAL_KEY;
  global $GKS_ALPHABANK_REAL_URL;
  global $GKS_ALPHABANK_SAND_MID;
  global $GKS_ALPHABANK_SAND_KEY;
  global $GKS_ALPHABANK_SAND_URL;

  global $_gks_session;
  
  if (isset($_gks_session['gks']['basket']['tropos_pliromis']) and 
      isset($_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_env_test']) and 
      $_gks_session['gks']['basket']['tropoi_pliromis_all'][ $_gks_session['gks']['basket']['tropos_pliromis'] ]['payment_acquirer_env_test'] != 0) {
    $alphabank_sandbox = true;
    $alphabank_mid = $GKS_ALPHABANK_SAND_MID;
    $alphabank_shared_secret_key = $GKS_ALPHABANK_SAND_KEY;
    $alphabank_url = $GKS_ALPHABANK_SAND_URL;
  } else {
    $alphabank_sandbox = false;
    $alphabank_mid = $GKS_ALPHABANK_REAL_MID;
    $alphabank_shared_secret_key = $GKS_ALPHABANK_REAL_KEY;
    $alphabank_url = $GKS_ALPHABANK_REAL_URL;
  }
  
  return array('sandbox' => $alphabank_sandbox,'mid' => $alphabank_mid,'shared_secret_key' => $alphabank_shared_secret_key,'url' => $alphabank_url);  
}

function my_paypal_settings() {

  global $GKS_PAYPAL_REAL_USERNAME;
  global $GKS_PAYPAL_REAL_PASSWORD;
  global $GKS_PAYPAL_REAL_SIGNATURE;
  global $GKS_PAYPAL_SANDBOX;
  global $GKS_PAYPAL_SAND_USERNAME;
  global $GKS_PAYPAL_SAND_PASSWORD;
  global $GKS_PAYPAL_SAND_SIGNATURE;
    
  if ($GKS_PAYPAL_SANDBOX) {
        
		$paypal_sandbox = true;
		$paypal_api_username = $GKS_PAYPAL_SAND_USERNAME;
		$paypal_api_password = $GKS_PAYPAL_SAND_PASSWORD;
		$paypal_api_signature = $GKS_PAYPAL_SAND_SIGNATURE;
	} else {
		$paypal_sandbox = false;
		$paypal_api_username = $GKS_PAYPAL_REAL_USERNAME;
		$paypal_api_password = $GKS_PAYPAL_REAL_PASSWORD;
		$paypal_api_signature = $GKS_PAYPAL_REAL_SIGNATURE;
	}      
	
	return array('APIUsername' => $paypal_api_username, 'APIPassword' => $paypal_api_password, 'APISignature' => $paypal_api_signature, 'Sandbox' => $paypal_sandbox);
}

function gks_calculate_kostos_apostolis(&$mybasketarray, $id) { //an id=0 tote den exei oristhei, an id= -1 tote parta ola
  global $db_link;
  global $my_wp_user_id;
  global $my_is_global_admin;
  global $gkIP;

//  echo '<pre>aaaa';
//  print_r($mybasketarray['destination_data']);
//  die();
  
  //echo 'ggggggggggg';
  //die();
  //if ($id != -2 and count($mybasketarray['products']) <=0) {
  //  return 0; 
  //}
  
  if ($id == 0) return 0;
  if ($id < 0) {
    $mybasketarray['tropoi_apostolis_all'] = array();
  }

  
  $lang_data_sqlfl=gks_lang_data_obj_prepare('gks_delivery_methods','default');
  if ($lang_data_sqlfl['success']==false) die($lang_data_sqlfl['message']);
  gks_lang_data_obj_sql_prepare($lang_data_sqlfl, array('delivery_method_name','delivery_method_html','delivery_method_sxolio','delivery_method_tooltip'));
  
  
  $sql="SELECT id_delivery_method, delivery_method_type,delivery_method_type_pa,
  delivery_method_disabled,delivery_method_env_test,delivery_method_fees_enabled,dm_fees_price,
  dm_fees_free_if_greater_than,dm_fees_free_if_greater_than,
  dm_fees_international_fixed,delivery_method_php_function_isok,delivery_method_php_function_calculate,
  mysortorder,
  ".gks_lang_sql_field('delivery_method_name',$lang_data_sqlfl).",
  ".gks_lang_sql_field('delivery_method_html',$lang_data_sqlfl).",
  ".gks_lang_sql_field('delivery_method_sxolio',$lang_data_sqlfl).",
  ".gks_lang_sql_field('delivery_method_tooltip',$lang_data_sqlfl)."
  
  FROM ".$lang_data_sqlfl['sql']['from1']." gks_delivery_methods
  ".$lang_data_sqlfl['sql']['from2']."
  where delivery_method_disabled=0";
  if ($id > 0) $sql.=" and id_delivery_method=".$id;
  $sql.=" order by mysortorder";
  
  if ($gkIP =='94.68.23.3sss') {
    echo $sql;
    die();
  }
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'calculate_tropos_apostolis error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
  }
  if ($result->num_rows == 0) return 0;
  
  while ($row = $result->fetch_assoc()) {
    $item=array('id_delivery_method' => intval($row['id_delivery_method']),
                'delivery_method_name' => trim_gks($row['delivery_method_name']),
                'delivery_method_type' => trim_gks($row['delivery_method_type']),
                'delivery_method_type_pa' => trim_gks($row['delivery_method_type_pa']),
                'delivery_method_html' => trim_gks($row['delivery_method_html']),
                'delivery_method_sxolio' => trim_gks($row['delivery_method_sxolio']),
                'delivery_method_tooltip' => trim_gks($row['delivery_method_tooltip']),
                'delivery_method_disabled' => intval($row['delivery_method_disabled']),
                'delivery_method_env_test' => intval($row['delivery_method_env_test']),
                'delivery_method_fees_enabled' => intval($row['delivery_method_fees_enabled']),
                'dm_fees_price' => floatval($row['dm_fees_price']),
                'dm_fees_free_if_greater_than' => floatval($row['dm_fees_free_if_greater_than']),
                'dm_fees_international_fixed' => floatval($row['dm_fees_international_fixed']),
                'delivery_method_php_function_isok' => trim_gks($row['delivery_method_php_function_isok']),
                'delivery_method_php_function_calculate' => trim_gks($row['delivery_method_php_function_calculate']),
                'mysortorder' => intval($row['mysortorder']),
                'dm_calc_kostos' => 0,
                );
                
                



                
    if ($mybasketarray['products_need_pliromi'] == false and $mybasketarray['products_need_apostoli']) {
      if ($item['delivery_method_type'] == 'pelatis' or  $item['delivery_method_type'] == 'store') {
        $item['delivery_method_type_pa'] = str_replace('[bank]', '', $item['delivery_method_type_pa']);
        $item['delivery_method_type_pa'] = str_replace('[web]', '', $item['delivery_method_type_pa']);
      }
    }
    
    $myisok=true;
    if ($myisok and $item['delivery_method_disabled'] !=0) $myisok = false;
    if ($myisok and $item['delivery_method_env_test'] !=0 and $my_is_global_admin==false) $myisok = false;
    if ($myisok and $mybasketarray['products_need_apostoli'] == false and $item['id_delivery_method'] != 1) $myisok = false;
    if ($myisok and $mybasketarray['products_need_apostoli'] == true  and $item['id_delivery_method'] <= 1) $myisok = false;


    
    if ($myisok and isset($item['delivery_method_php_function_isok']) and $item['delivery_method_php_function_isok'] != '') {
      $myisok = call_user_func_array($item['delivery_method_php_function_isok'],array(&$mybasketarray));
      if ($myisok == false) {
        if ($mybasketarray['tropos_apostolis'] == $item['id_delivery_method']) {
          $mybasketarray['tropos_apostolis'] = 0;
        }
      }
    }
    
    $item['myisok'] = $myisok ? '1':'0';
    if ($id == -2) {
      $mybasketarray['tropoi_apostolis_all'][  $item['id_delivery_method'] ] =$item;
    } else {
      //if ($myisok) {
        $mybasketarray['tropoi_apostolis_all'][  $item['id_delivery_method'] ] =$item;
      //}
    }
    

  }

  foreach ($mybasketarray['tropoi_apostolis_all'] as &$item) {
    global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
    if ($item['delivery_method_fees_enabled'] == 0) {
      $item['dm_calc_kostos']=0;
    } else {
      if (isset($item['delivery_method_php_function_calculate']) and $item['delivery_method_php_function_calculate'] != '') {
        $item['dm_calc_kostos'] = call_user_func_array($item['delivery_method_php_function_calculate'], array(&$mybasketarray));
        $item['dm_calc_kostos'] = round($item['dm_calc_kostos'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      } else {
        $item['dm_calc_kostos'] = $item['dm_fees_price'];
        if ($mybasketarray['destination_data']['country_id'] != 91) {
          $item['dm_calc_kostos'] = $item['dm_fees_international_fixed'];
        } 
        if ($item['dm_fees_free_if_greater_than'] > 0 and $mybasketarray['products_total'] >= $item['dm_fees_free_if_greater_than']) {
          $item['dm_calc_kostos']=0;
        }
      }
      
    }
  }
  
  
  
  if ($id > 0) {
    if (!isset($mybasketarray['tropoi_apostolis_all'][ $id ]['dm_calc_kostos'])) {
      return 0;
    } else {
      return $mybasketarray['tropoi_apostolis_all'][ $id ]['dm_calc_kostos'];
    }
  } else {
    if ($mybasketarray['tropos_apostolis']>0) {
      if (isset($mybasketarray['tropoi_apostolis_all'][ $mybasketarray['tropos_apostolis'] ]['dm_calc_kostos'])) {
        return $mybasketarray['tropoi_apostolis_all'][ $mybasketarray['tropos_apostolis'] ]['dm_calc_kostos'];
      } else {
        return 0;
      }
    } else {
      return 0;
    }
  }
  return 0;
  
  
}



function gks_calculate_kostos_pliromis(&$mybasketarray, $id) { //an id=0 tote den exei oristhei, an id= -1 tote parta ola
  global $db_link;
  global $my_wp_user_id;
  global $my_is_global_admin;
  
  //if ($id != -2 and count($mybasketarray['products']) <=0) {
   // return 0; 
  //}
    
  if ($id == 0) return 0;
  if ($id < 0) {
    $mybasketarray['tropoi_pliromis_all'] = array();
  }

  $lang_data_sqlfl=gks_lang_data_obj_prepare('gks_payment_acquirers','default');
  if ($lang_data_sqlfl['success']==false) die($lang_data_sqlfl['message']);
  gks_lang_data_obj_sql_prepare($lang_data_sqlfl, array('payment_acquirer_name','payment_acquirer_html','payment_acquirer_button_html','payment_acquirer_sxolio','payment_acquirer_tooltip'));
    
  $sql="SELECT id_payment_acquirer,payment_acquirer_type,payment_acquirer_type_dm,
  payment_acquirer_disabled,payment_acquirer_env_test,
  payment_acquirer_fees_enabled,pa_fees_domestic_fixed,
  pa_fees_domestic_percent,pa_fees_international_fixed,pa_fees_international_percent,
  payment_acquirer_php_function_isok,payment_acquirer_php_function_calculate,
  aade_tropos_pliromis_id,payment_acquirer_with_id,
  mysortorder,
  
  ".gks_lang_sql_field('payment_acquirer_name',$lang_data_sqlfl).",
  ".gks_lang_sql_field('payment_acquirer_html',$lang_data_sqlfl).",
  ".gks_lang_sql_field('payment_acquirer_button_html',$lang_data_sqlfl).",
  ".gks_lang_sql_field('payment_acquirer_sxolio',$lang_data_sqlfl).",
  ".gks_lang_sql_field('payment_acquirer_tooltip',$lang_data_sqlfl)."

   FROM ".$lang_data_sqlfl['sql']['from1']." gks_payment_acquirers
   ".$lang_data_sqlfl['sql']['from2']."
   where payment_acquirer_disabled=0";
  if ($id > 0) $sql.=" and id_payment_acquirer=".$id;
  $sql.=" order by mysortorder";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'calculate_tropos_pliromis error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
  }
  if ($result->num_rows == 0) return 0;
  
  while ($row = $result->fetch_assoc()) {
    $item=array('id_payment_acquirer' => intval($row['id_payment_acquirer']),
                'payment_acquirer_name' => trim_gks($row['payment_acquirer_name']),
                'payment_acquirer_type' => trim_gks($row['payment_acquirer_type']),
                'payment_acquirer_type_dm' => trim_gks($row['payment_acquirer_type_dm']),
                'payment_acquirer_html' => trim_gks($row['payment_acquirer_html']),
                'payment_acquirer_button_html' => trim_gks($row['payment_acquirer_button_html']),
                'payment_acquirer_sxolio' => trim_gks($row['payment_acquirer_sxolio']),
                'payment_acquirer_tooltip' => trim_gks($row['payment_acquirer_tooltip']),
                'payment_acquirer_disabled' => intval($row['payment_acquirer_disabled']),
                'payment_acquirer_env_test' => intval($row['payment_acquirer_env_test']),
//                'payment_acquirer_method' => trim_gks($row['payment_acquirer_method']),
                'payment_acquirer_fees_enabled' => intval($row['payment_acquirer_fees_enabled']),
                'pa_fees_domestic_fixed' => floatval($row['pa_fees_domestic_fixed']),
                'pa_fees_domestic_percent' => floatval($row['pa_fees_domestic_percent']),
                'pa_fees_international_fixed' => floatval($row['pa_fees_international_fixed']),
                'pa_fees_international_percent' => floatval($row['pa_fees_international_percent']),
                'payment_acquirer_php_function_isok' => trim_gks($row['payment_acquirer_php_function_isok']),
                'payment_acquirer_php_function_calculate' => trim_gks($row['payment_acquirer_php_function_calculate']),
                'mysortorder' => intval($row['mysortorder']),
                'pa_calc_kostos' => 0,
                'aade_tropos_pliromis_id' => intval($row['aade_tropos_pliromis_id']),
                'payment_acquirer_with_id'=>intval($row['payment_acquirer_with_id']),
                );
    
    
    if ($mybasketarray['products_need_pliromi'] == false and $mybasketarray['products_need_apostoli']) {
      if ($item['id_payment_acquirer'] != 1) {
        $item['payment_acquirer_type_dm'] = str_replace('[pelatis]', '', $item['payment_acquirer_type_dm']);
        $item['payment_acquirer_type_dm'] = str_replace('[store]', '', $item['payment_acquirer_type_dm']);
      }
    }


    $myisok=true;
    if ($myisok and $item['payment_acquirer_disabled'] !=0) $myisok = false;
    if ($myisok and $item['payment_acquirer_env_test'] !=0 and $my_is_global_admin==false) $myisok = false;
    if ($myisok and $mybasketarray['products_need_apostoli'] == false and $mybasketarray['products_need_pliromi'] == false and $item['id_payment_acquirer'] != 1) $myisok = false;
    if ($myisok and $mybasketarray['products_need_pliromi'] == false and $item['id_payment_acquirer'] < 1) $myisok = false;
    if ($myisok and $mybasketarray['products_need_pliromi'] == true  and $item['id_payment_acquirer'] <= 1) $myisok = false;
//    if ($item['id_payment_acquirer'] ==1) {
//     print '<pre>';
//     print '--'.$myisok.'--';
//     print_r($item);
//     die(); 
//    }

    if ($myisok and $mybasketarray['products_need_apostoli'] == false) {
      if (!(strpos($item['payment_acquirer_type_dm'], '[none]') !== false)) { //den periexei 
        $myisok = false;
      }
    }
  
    if ($myisok and isset($item['payment_acquirer_php_function_isok']) and $item['payment_acquirer_php_function_isok'] != '') {
      $myisok = call_user_func_array($item['payment_acquirer_php_function_isok'],array(&$mybasketarray));
      if ($myisok == false) {
        if ($mybasketarray['tropos_pliromis'] == $item['id_payment_acquirer']) {
          $mybasketarray['tropos_pliromis'] = 0;
        }
      }
    }
    
    
    $item['myisok'] = $myisok ? '1':'0';
    if ($id == -2) {
      //$item['myisok'] = $myisok ? '1':'0';
      $mybasketarray['tropoi_pliromis_all'][  $item['id_payment_acquirer'] ] =$item;
    } else {
      //if ($myisok) {
        $mybasketarray['tropoi_pliromis_all'][  $item['id_payment_acquirer'] ] =$item;
      //}
    }
        
  }

  foreach ($mybasketarray['tropoi_pliromis_all'] as &$item) {
    global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
    if ($item['payment_acquirer_fees_enabled'] == 0) {
      $item['pa_calc_kostos']=0;
    } else {
      if (isset($item['payment_acquirer_php_function_calculate']) and $item['payment_acquirer_php_function_calculate'] != '') {
        $item['pa_calc_kostos'] = call_user_func_array($item['payment_acquirer_php_function_calculate'], array(&$mybasketarray));
        $item['pa_calc_kostos'] = round($item['pa_calc_kostos'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      } else {
              
        $item['pa_calc_kostos']=$item['pa_fees_domestic_fixed'];
        $item['pa_calc_kostos']+= round($mybasketarray['products_total'] *  $item['pa_fees_domestic_percent']/100,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        if ($mybasketarray['destination_data']['country_id'] != 91) {
          $item['pa_calc_kostos']=$item['pa_fees_international_fixed'];
          $item['pa_calc_kostos']+= round(($mybasketarray['products_total'] + $mybasketarray['tropos_apostolis']) *  $item['pa_fees_international_percent']/100,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        }
      }
    }
  }
  
  
  
  if ($id > 0) {
    if (!isset($mybasketarray['tropoi_pliromis_all'][ $id ]['pa_calc_kostos'])) {
      return 0;
    } else {
      return $mybasketarray['tropoi_pliromis_all'][ $id ]['pa_calc_kostos'];
    }
  } else {
    if ($mybasketarray['tropos_pliromis']>0) {
      if (isset($mybasketarray['tropoi_pliromis_all'][ $mybasketarray['tropos_pliromis'] ]['pa_calc_kostos'])) {
        return $mybasketarray['tropoi_pliromis_all'][ $mybasketarray['tropos_pliromis'] ]['pa_calc_kostos'];
      } else {
        return 0;   
      }
    } else {
      return 0;
    }
  }
  return 0;
    
  
}

function gks_get_destination_data(&$mybasketarray) {
  global $db_link;
  global $my_wp_user_id;

  if ($mybasketarray['address_extra'] == -1) { // idia 
    
    $mybasketarray['destination_data']['name'] = trim_gks($mybasketarray['user']['first_name'].' '.$mybasketarray['user']['last_name']);
    $mybasketarray['destination_data']['phone'] = $mybasketarray['user']['mobile'];
    $mybasketarray['destination_data']['odos'] = $mybasketarray['user']['ma_odos'];
    $mybasketarray['destination_data']['arithmos'] = $mybasketarray['user']['ma_arithmos'];
    $mybasketarray['destination_data']['orofos'] = $mybasketarray['user']['ma_orofos'];
    $mybasketarray['destination_data']['perioxi'] = $mybasketarray['user']['ma_perioxi'];
    $mybasketarray['destination_data']['poli'] = $mybasketarray['user']['ma_poli'];
    $mybasketarray['destination_data']['tk'] = $mybasketarray['user']['ma_tk'];
    $mybasketarray['destination_data']['country_id'] = $mybasketarray['user']['ma_country_id'];
    $mybasketarray['destination_data']['nomos_id'] = $mybasketarray['user']['ma_nomos_id'];    
    
  } else if ($mybasketarray['address_extra'] >= 0) { // nea i iparxousa
    //nothing to do
    
      
    
  } 
//  else if ($mybasketarray['address_extra'] > 0 and $my_wp_user_id > 0) {  // sigkekrimeni dieyuynsi
//    $sql="SELECT * FROM gks_users_extra_address where id_users_extra_address = ".$mybasketarray['address_extra']." and user_id=".$my_wp_user_id;
//    $result = $db_link->query($sql);
//    if (!$result) {
//      debug_mail(false,'calculate_tropos_pliromis error sql',$sql);
//      return false;
//    }
//    if ($result->num_rows == 0) return false;
//    $row = $result->fetch_assoc();  
//    $mybasketarray['destination_data']['name'] = isset($row['ea_name']) ? $row['ea_name']: '';
//    $mybasketarray['destination_data']['phone'] = isset($row['ea_phone']) ? $row['ea_phone']: '';
//    $mybasketarray['destination_data']['odos'] = isset($row['ea_odos']) ? $row['ea_odos']: '';
//    $mybasketarray['destination_data']['perioxi'] = isset($row['ea_perioxi']) ? $row['ea_perioxi']: '';
//    $mybasketarray['destination_data']['poli'] = isset($row['ea_poli']) ? $row['ea_poli']: '';
//    $mybasketarray['destination_data']['tk'] = isset($row['ea_tk']) ? $row['ea_tk']: '';
//    $mybasketarray['destination_data']['country_id'] = isset($row['ea_country_id']) ? $row['ea_country_id']: 0;
//    $mybasketarray['destination_data']['nomos_id'] = isset($row['ea_nomos_id']) ? $row['ea_nomos_id']: 0;
//  
//  } 
  

  
  return true;
}



function gks_calculate_isok_delivery_elta_simple(&$mybasketarray) {
  if ($mybasketarray['products_need_apostoli'] == false) return false;
  if ($mybasketarray['products_ogos_max_x'] > 30 or $mybasketarray['products_ogos_max_y'] > 30) return false;
  if ($mybasketarray['products_ogos_max_y'] > 5) return false;
  
  return $mybasketarray['products_varos']<=2000;
}
function gks_calculate_kostos_delivery_elta_simple(&$mybasketarray) {
  if ($mybasketarray['products_varos']<=100) {
    return 1.7;  
  } else if ($mybasketarray['products_varos']<=200) {
    return 2.1;  
  } else if ($mybasketarray['products_varos']<=500) {
    return 3.2;  
  } else if ($mybasketarray['products_varos']<=1000) {
    return 3.6;  
  } else if ($mybasketarray['products_varos']<=2000) {
    return 4.0;  
  } else {
    return 5.0;  
  }
}
function gks_calculate_isok_delivery_elta_sistimeno(&$mybasketarray) {
  if ($mybasketarray['products_need_apostoli'] == false) return false;
  if ($mybasketarray['products_ogos_max_x'] > 30 or $mybasketarray['products_ogos_max_y'] > 30) return false;
  if ($mybasketarray['products_ogos_max_y'] > 5) return false;
  return $mybasketarray['products_varos']<=2000;
}
function gks_calculate_kostos_delivery_elta_sistimeno(&$mybasketarray) {
  return  gks_calculate_kostos_delivery_elta_simple($mybasketarray) + 1.9;
}

function gks_calculate_isok_delivery_elta_dema(&$mybasketarray) {
  if ($mybasketarray['products_need_apostoli'] == false) return false;
  return $mybasketarray['products_varos'] > 1000;
}
function gks_calculate_kostos_delivery_elta_dema(&$mybasketarray) {
  $myval= ceil($mybasketarray['products_varos']/1000) * 0.5;  
  if ($myval<1.5) $myval=1.5;
  return $myval;
}

function gks_calculate_kostos_delivery_elta_doortodoor(&$mybasketarray) {
  $mykila = ceil($mybasketarray['products_varos']/1000);
  $mykila=$mykila-2;
  if ($mykila<=0) $mykila=0;
  return 6.67 + $mykila * 2.08;
}

function gks_calculate_kostos_delivery_taxydromiki(&$mybasketarray) {
  
  $mykila = ceil($mybasketarray['products_varos']/1000);
  $mykila=$mykila-2;
  if ($mykila<=0) $mykila=0;
  $mykila = ceil($mykila);
  
  $myval = 14.01 + $mykila* 5.46;
  
  //debug_mail(false,'debug1',$mybasketarray['destination_data']['poli']);
      
  if (isset($mybasketarray['destination_data']['poli'])) {
    $user_poli = mb_strtolower($mybasketarray['destination_data']['poli']);
    $user_poli = str_replace(" ", "", $user_poli);
    $user_poli = str_replace("ss", "s", $user_poli);
    
    //debug_mail(false,'debug2',$user_poli);
    if ($user_poli=='thesaloniki' or $user_poli=='thes/niki') {
      $myval = 8.06 + $mykila * 2.48;
    }
     
  }
  
  if ( $mybasketarray['tropos_pliromis'] == 3) $myval=0; //antikatavoli
  return $myval;
}

function gks_calculate_kostos_pliromis_antikatavoli(&$mybasketarray) {
//  $mykila = ceil($mybasketarray['products_varos']/1000);
//  $mykila=$mykila-2;
//  if ($mykila<=0) $mykila=0;
//  $mykila = ceil($mykila);
//  
//  $myval = 12.60 + $mykila* 2.48;
//  
//  //$deli_kostos_taxi = calculate_kostos_delivery_taxydromiki();
//  //$myval=$myval-$deli_kostos_taxi;
//  //if ($myval<0) $myval=0;
//  if ( $mybasketarray['tropos_apostolis'] == 17){ //antikatavoli
//    $myval = 12.60 + $mykila* 2.48;
//  }
//  if ($mybasketarray['products_total']==0) $myval=0;
//  return $myval;
    return 3;
}



function gks_calculate_isok_delivery_acs_sameday(&$mybasketarray) {
  if ($mybasketarray['products_need_apostoli'] == false) return false;
  if ($mybasketarray['products_need_pliromi'] == false) return false;
  return true;
}

function gks_calculate_isok_payment_paypal(&$mybasketarray) {
  //if ($mybasketarray['products_need_pliromi'] == false) return false;
  return true;
}
function gks_calculate_isok_payment_antikatavoli(&$mybasketarray) {
  
  if ($mybasketarray['products_need_apostoli'] == false) return false;
  if ($mybasketarray['products_need_pliromi'] == false) return false;
  return true;
  //return $mybasketarray['products_total'] > 0;
}



function gks_calculate_kostos_pliromis_alphabank(&$mybasketarray) {
  $poso= $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'];
  $pososto = 1.5/100;

  //kostos priromis = aplos ypologistmos + ypologismo tou kostous pliromis  p.x. 1000 * 1.5% =15 ara telika 15 + 15 * 1.5% = 15 + 0,225 = 15,225
  //kostos priromis = (poso * pososto) + (poso * pososto) * pososto
  return ($poso * $pososto) + ($poso * $pososto) * $pososto;
}
function gks_calculate_kostos_pliromis_piraeusbank(&$mybasketarray) {
  global $db_link;
  $poso= $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'];
  $pososto = 0.9/100;
  $kostos_priromis=($poso * $pososto) + 0.07;
  
  if ($mybasketarray['user']['ma_country_id']!=91) {
    $sql="select * from gks_country where id_country=". $mybasketarray['user']['ma_country_id']." and (country_ee='' or country_ee is null)";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'calculate_tropos_apostolis error sql',$sql);
      return 0;
    }
    if ($result->num_rows >= 1) {
      $row = $result->fetch_assoc();
      $pososto=3/100;
      $kostos_priromis=($poso * $pososto) + 0.07;
    }
    //3.Κάρτες έκδοσης Τραπεζών εκτός Ευρωπαϊκού Οικονομικού Χώρου (ΕΟΧ)
    //(Όλοι οι τύποι καρτών Visa / Mastercard / Maestro)
	  //3.00% + 	0.07  
  }


  //$kostos_priromis = aplos ypologistmos + ypologismo tou kostous pliromis  p.x. 1000 * 1.5% =15 ara telika 15 + 15 * 1.5% = 15 + 0,225 = 15,225
  //$kostos_priromis = (poso * pososto) + (poso * pososto) * pososto
  //$kostos_priromis = ($poso * $pososto) + ($poso * $pososto) * $pososto;
  
  return $kostos_priromis;
}


function gks_calculate_kostos_pliromis_paypal(&$mybasketarray) {
  $poso= $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'];
  $pososto =3.4/100;
  $fix=0.35;
  
  //kostos priromis = aplos ypologistmos + ypologismo tou kostous pliromis 
  //kostos priromis = (fix + (poso * pososto)) + (poso * pososto) * pososto
  return ($fix + ($poso * $pososto)) + (($fix + ($poso * $pososto))) * $pososto;

  
}


function piraeusbank_filter_text($mytext) {
  if ($mytext == false || $mytext == null || empty($mytext)) return '';
  $mytext=trim_gks($mytext);
  if (empty($mytext)) return '';
  
  $sim=' /:_().,+-';
  $num='1234567890';
  $grl='αάβγδεέζηήθιίϊΐκλμνξοόπρστυύϋΰφχψωώς';
  $gru='ΑΆΒΓΔΈΕΖΗΉΘΙΊΪΙΚΛΜΝΞΟΌΠΡΣΤΥΎΫΦΧΨΩΏ';
  $enl='abcdefgΗijklmnopkrstuvwxyz';
  $enu='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  
  
  $out='';
  for ($i=0; $i < mb_strlen($mytext); $i++) {
    $c=mb_substr($mytext,$i,1);
    $isok=false;
    if (strpos($sim, $c) !== false) $isok=true;
    else if (strpos($num, $c) !== false) $isok=true;
    else if (strpos($grl, $c) !== false) $isok=true;
    else if (strpos($gru, $c) !== false) $isok=true;
    else if (strpos($enl, $c) !== false) $isok=true;
    else if (strpos($enu, $c) !== false) $isok=true;
    
    if ($isok) $out.=$c;
  }
  
  return trim_gks($out);
  
}
