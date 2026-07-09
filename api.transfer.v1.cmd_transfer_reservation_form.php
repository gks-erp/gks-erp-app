<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
function gks_api_transfer_cmd_transfer_reservation_form($id_transfer,$row_transfer,$input_data) {
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
  $transfer_title=$row_transfer['transfer_title'];
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  gks_erp_cookie_start($gks_erp_cookie_id);
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  $db_lang='';$db_lang2='';if ($_gks_session['gks']['ui_lang']=='en-US') {$db_lang='_en_US';$db_lang2='_en';}
  
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  
  $val_from_id=0;  if (isset($input_data['get_data']['from'])) $val_from_id=intval($input_data['get_data']['from']);
  $val_from_place_id='';if (isset($input_data['get_data']['gmfrom'])) $val_from_place_id=trim($input_data['get_data']['gmfrom']);

  $val_to_id=0;    if (isset($input_data['get_data']['to']))   $val_to_id  =intval($input_data['get_data']['to']);
  $val_to_place_id='';if (isset($input_data['get_data']['gmto'])) $val_to_place_id=trim($input_data['get_data']['gmto']);
  
  $return=array('success' => false, 'message' => base64_encode('generic gks_api_transfer_cmd_transfer_reservation_form error'),'data' => false);
  //$return_data['input_data']=$input_data;
  $return['data']['val_from_id']=$val_from_id;
  $return['data']['val_from_descr']='';
  $return['data']['val_from_poi_type_id']=0;
  $return['data']['val_from_place_id']=$val_from_place_id;
  $return['data']['val_from_place_lat']=0;
  $return['data']['val_from_place_lng']=0;
  $return['data']['val_from_place_formatted_address']='';
  $return['data']['val_from_place_url']='';  
  
  $return['data']['val_to_id']=$val_to_id;
  $return['data']['val_to_descr']='';
  $return['data']['val_to_poi_type_id']=0;
  $return['data']['val_to_place_id']=$val_to_place_id;
  $return['data']['val_to_place_lat']=0;
  $return['data']['val_to_place_lng']=0;
  $return['data']['val_to_place_formatted_address']='';
  $return['data']['val_to_place_url']='';  
  
  $return['data']['user_transfer_online_search_type']=0;
  
  //return $return;

  
  if ($val_from_id>0 or $val_to_id>0 or $val_from_place_id!='' or $val_to_place_id!='') {

    if ($val_from_id>0) {
      $sql="SELECT gks_poi.id_poi, gks_poi.poi_descr,           gks_poi_en_US.poi_descr_en_US,           poi_descr".$db_lang." as poi_descr_i18n,
                                   gks_poi_type.poi_type_descr, gks_poi_type_en_US.poi_type_descr_en_US, poi_type_descr".$db_lang." as poi_type_descr_i18n,
      gks_poi.poi_type_id
      FROM ((gks_poi 
      LEFT JOIN (
        SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
      ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id)
      LEFT JOIN gks_poi_type ON gks_poi.poi_type_id = gks_poi_type.id_poi_type)
      LEFT JOIN (
        SELECT poi_type_id, poi_type_descr as poi_type_descr_en_US FROM gks_poi_type_lang WHERE lang_code='en-US'
      ) AS gks_poi_type_en_US ON gks_poi_type.id_poi_type = gks_poi_type_en_US.poi_type_id
      WHERE gks_poi.id_poi=".$val_from_id;
      
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode('Internal system error (sql error). Please retry later');return $return;}
      
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $return['data']['val_from_descr']=(isset($row['poi_descr_i18n']) ? trim_gks($row['poi_descr_i18n']) : trim_gks($row['poi_descr_en_US']));
        $return['data']['val_from_poi_type_id']=intval($row['poi_type_id']);
      } 
    }
    if ($val_to_id>0) {
      $sql="SELECT gks_poi.id_poi, gks_poi.poi_descr,           gks_poi_en_US.poi_descr_en_US,           poi_descr".$db_lang." as poi_descr_i18n,
                                   gks_poi_type.poi_type_descr, gks_poi_type_en_US.poi_type_descr_en_US, poi_type_descr".$db_lang." as poi_type_descr_i18n,
      gks_poi.poi_type_id
      FROM ((gks_poi 
      LEFT JOIN (
        SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
      ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id)
      LEFT JOIN gks_poi_type ON gks_poi.poi_type_id = gks_poi_type.id_poi_type)
      LEFT JOIN (
        SELECT poi_type_id, poi_type_descr as poi_type_descr_en_US FROM gks_poi_type_lang WHERE lang_code='en-US'
      ) AS gks_poi_type_en_US ON gks_poi_type.id_poi_type = gks_poi_type_en_US.poi_type_id
      WHERE gks_poi.id_poi=".$val_to_id;
      
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode('Internal system error (sql error). Please retry later');return $return;}
      
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $return['data']['val_to_descr']=(isset($row['poi_descr_i18n']) ? trim_gks($row['poi_descr_i18n']) : trim_gks($row['poi_descr_en_US']));
        $return['data']['val_to_poi_type_id']=intval($row['poi_type_id']);
      }
    }
    


    if ($val_from_place_id!='') {
      
      $sql="select * from gks_cache_googlemaps_place
      where place_id='".$db_link->escape_string($val_from_place_id)."'
      and language='en'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode('Internal system error (sql error). Please retry later');return $return;}
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $val_from_place_formatted_address=$row['address'];
        $return['data']['val_from_descr']=$val_from_place_formatted_address;
        $return['data']['val_from_poi_type_id']=0;
        
        $return['data']['user_transfer_online_search_type']=1;
        $return['data']['val_from_place_id']=$val_from_place_id;
        $return['data']['val_from_place_lat']=floatval($row['lat']);
        $return['data']['val_from_place_lng']=floatval($row['lng']);
        $return['data']['val_from_place_formatted_address']=$row['address'];
        $return['data']['val_from_place_url']=$row['url'];

        
      } else {

        global $GKS_GOOGLE_MAPS_API_KEY_SERVER;
//        $geocode_url='https://maps.googleapis.com/maps/api/geocode/json?language=en&place_id='.
//        rawurlencode($val_from_place_id).
//        '&key='.$GKS_GOOGLE_MAPS_API_KEY_SERVER;

        $geocode_url='https://maps.googleapis.com/maps/api/place/details/json?language=en&place_id='.
        rawurlencode($val_from_place_id).
        '&key='.$GKS_GOOGLE_MAPS_API_KEY_SERVER;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $geocode_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
        $response_str=curl_exec($ch);
        curl_close($ch);
      
        
        if ($response_str!='') {
          $response=json_decode($response_str, true);
          //$return['message']=base64_encode(print_r($response,true));return $return;
            
          //echo '<pre>';print_r($response);die();
          //echo 5/0;
          //echo '<pre>ffffffffffffffff ';print_r($response);die();
          //$return['message']=base64_encode('<pre>ffffffffffffffff '.print_r($response,true));return $return;
          
          if (is_array($response)) {
            if (isset($response['error_message']) and $response['error_message']!='') {
              debug_mail(false,'geocode_url error',$response_str);
              $return = array('success' => false, 'message' => base64_encode($response['error_message']));
              echo json_encode($return); die();
            } else if (isset($response['result']['geometry']['location']['lat']) and isset($response['result']['geometry']['location']['lng'])) {
              $val_from_place_lat=floatval($response['result']['geometry']['location']['lat']);
              $val_from_place_lng=floatval($response['result']['geometry']['location']['lng']);
              $val_from_place_url='';
              if (isset($response['result']['url'])) {
                $val_from_place_url=$response['result']['url'];}

              $val_from_place_formatted_address='';
              if (isset($response['result']['name'])) {
                $val_from_place_formatted_address=$response['result']['name'];}
              if (isset($response['result']['formatted_address'])) {
                if ($val_from_place_formatted_address!=$response['result']['formatted_address']) {
                  $val_from_place_formatted_address.=', ';
                  $val_from_place_formatted_address.=$response['result']['formatted_address'];
                }
              }

              
              global $my_wp_user_id;
              if (isset($my_wp_user_id)==false) $my_wp_user_id=0;
  
              $sql="insert into gks_cache_googlemaps_place (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              place_id,response,lat,lng,url,address,language
              ) values (
              now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
              '".$db_link->escape_string($val_from_place_id)."',
              '".$db_link->escape_string($response_str)."',
              ".number_format($val_from_place_lat, 12, '.', '').",
              ".number_format($val_from_place_lng, 12, '.', '').",
              '".$db_link->escape_string($val_from_place_url)."',
              '".$db_link->escape_string($val_from_place_formatted_address)."',
              'en'
              )";
              $result = $db_link->query($sql);
              if (!$result) {
                debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
                $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
            

              $return['data']['val_from_descr']=$val_from_place_formatted_address;
              $return['data']['val_from_poi_type_id']=0;
              
              $return['data']['user_transfer_online_search_type']=1;
              $return['data']['val_from_place_id']=$val_from_place_id;
              $return['data']['val_from_place_lat']=$val_from_place_lat;
              $return['data']['val_from_place_lng']=$val_from_place_lng;
              $return['data']['val_from_place_formatted_address']=$val_from_place_formatted_address;
              $return['data']['val_from_place_url']=$val_from_place_url;
                      
              
            } 
          }
        }
        
      //$return['message']=base64_encode('<pre>'.$val_to_id.'|'.$return['data']['val_to_descr']);return $return;
        
                
        
        
      }    
    }
    
    if ($val_to_place_id!='') {
      
      $sql="select * from gks_cache_googlemaps_place
      where place_id='".$db_link->escape_string($val_to_place_id)."'
      and language='en'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode('Internal system error (sql error). Please retry later');return $return;}
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $val_to_place_formatted_address=$row['address'];
        $return['data']['val_to_descr']=$val_to_place_formatted_address;
        $return['data']['val_to_poi_type_id']=0;
        
        $return['data']['user_transfer_online_search_type']=1;
        $return['data']['val_to_place_id']=$val_to_place_id;
        $return['data']['val_to_place_lat']=floatval($row['lat']);
        $return['data']['val_to_place_lng']=floatval($row['lng']);
        $return['data']['val_to_place_formatted_address']=$row['address'];
        $return['data']['val_to_place_url']=$row['url'];

        
      } else {

        global $GKS_GOOGLE_MAPS_API_KEY_SERVER;
//        $geocode_url='https://maps.googleapis.com/maps/api/geocode/json?language=en&place_id='.
//        rawurlencode($val_to_place_id).
//        '&key='.$GKS_GOOGLE_MAPS_API_KEY_SERVER;

        $geocode_url='https://maps.googleapis.com/maps/api/place/details/json?language=en&place_id='.
        rawurlencode($val_to_place_id).
        '&key='.$GKS_GOOGLE_MAPS_API_KEY_SERVER;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $geocode_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
        $response_str=curl_exec($ch);
        curl_close($ch);
              

        if ($response_str!='') {
          $response=json_decode($response_str, true);
          //$return['message']=base64_encode(print_r($response,true));return $return;
            
          //echo '<pre>';print_r($response);die();
          //echo 5/0;
          //echo '<pre>ffffffffffffffff ';print_r($response);die();
          //$return['message']=base64_encode('<pre>ffffffffffffffff '.print_r($response,true));return $return;
          
          if (is_array($response)) {
            if (isset($response['error_message']) and $response['error_message']!='') {
              debug_mail(false,'geocode_url error',$response_str);
              $return = array('success' => false, 'message' => base64_encode($response['error_message']));
              echo json_encode($return); die();
            } else if (isset($response['result']['geometry']['location']['lat']) and isset($response['result']['geometry']['location']['lng'])) {
              $val_to_place_lat=floatval($response['result']['geometry']['location']['lat']);
              $val_to_place_lng=floatval($response['result']['geometry']['location']['lng']);
              $val_to_place_url='';
              if (isset($response['result']['url'])) {
                $val_to_place_url=$response['result']['url'];}

              $val_to_place_formatted_address='';
              if (isset($response['result']['name'])) {
                $val_to_place_formatted_address=$response['result']['name'];}
              if (isset($response['result']['formatted_address'])) {
                if ($val_to_place_formatted_address!=$response['result']['formatted_address']) {
                  $val_to_place_formatted_address.=', ';
                  $val_to_place_formatted_address.=$response['result']['formatted_address'];
                }
              }

              
              global $my_wp_user_id;
              if (isset($my_wp_user_id)==false) $my_wp_user_id=0;
  
              $sql="insert into gks_cache_googlemaps_place (
              mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
              place_id,response,lat,lng,url,address,language
              ) values (
              now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
              '".$db_link->escape_string($val_to_place_id)."',
              '".$db_link->escape_string($response_str)."',
              ".number_format($val_to_place_lat, 12, '.', '').",
              ".number_format($val_to_place_lng, 12, '.', '').",
              '".$db_link->escape_string($val_to_place_url)."',
              '".$db_link->escape_string($val_to_place_formatted_address)."',
              'en'
              )";
              $result = $db_link->query($sql);
              if (!$result) {
                debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
                $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
            

              $return['data']['val_to_descr']=$val_to_place_formatted_address;
              $return['data']['val_to_poi_type_id']=0;
              
              $return['data']['user_transfer_online_search_type']=1;
              $return['data']['val_to_place_id']=$val_to_place_id;
              $return['data']['val_to_place_lat']=$val_to_place_lat;
              $return['data']['val_to_place_lng']=$val_to_place_lng;
              $return['data']['val_to_place_formatted_address']=$val_to_place_formatted_address;
              $return['data']['val_to_place_url']=$val_to_place_url;
                      
              
            } 
          }
        }
        
        
                
        
        
      }
      
      
      //$return['message']=base64_encode('<pre>'.$val_to_id.'|'.$return['data']['val_to_descr']);return $return;
      
    }    
    
    
    
  }
  
  $return['data']['user_transfer_online_search_type']=0;
  if ($val_from_place_id=='' and $val_to_place_id=='') {
    $return['data']['user_transfer_online_search_type']=0;
  } else if ($val_from_place_id!='' and $val_to_place_id=='') {
    $return['data']['user_transfer_online_search_type']=2;
  } else if ($val_from_place_id=='' and $val_to_place_id!='') {
    $return['data']['user_transfer_online_search_type']=1;
  } else if ($val_from_place_id!='' and $val_to_place_id!='') {
    $return['data']['user_transfer_online_search_type']=3;
  }
    
    
  
  //$return['data']['val_to_descr']=$_gks_session['gks']['ui_lang'];
  
  
  $return['success']=true; 
  return $return;
  

}

