<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function transfer_price_round_type_func($value, $type) {
  $val_ret=$value;
  $type=intval($type);
  switch ($type) {   
    case 0: //Anenergo
      break;
    case 1: //0.01
      $val_ret=round($value,2);
      break;
    case 2: //0.05
      $val_ret=round($value*2,1)/2;
      break;
    case 3: //0.1
      $val_ret=round($value,1);
      break;
    case 4: //0.5
      $val_ret=round($value*2,0)/2;
      break;
    case 5: //1
      $val_ret=round($value,0);
      break;
    default:
  }
  return $val_ret;
}

function gks_api_transfer_cmd_transfer_reservation_search($id_transfer,$row_transfer,$input_data) {
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
  global $GKS_GOOGLE_MAPS_API_KEY_SERVER;
  global $transfer_akrivia;
  
  global $my_wp_user_id;
  if (isset($my_wp_user_id)==false) $my_wp_user_id=0;
  
  
  
  
  //print '<pre>cccccccccc ';print_r($row_transfer);die();
  //print '<pre>cccccccccc ';print_r($input_data);die();
  //print '<pre>cccccccccc ';print_r($id_transfer);die();

  $from_backend=false;
  if (isset($input_data['backend'])) $from_backend=$input_data['backend'];

  //echo '<pre>sssssssss22 '.$from_backend.'|'.$input_data['get_data']['apostasi'];die();
  
  //print '<pre>cccccccccc '.$from_backend;die();
    
  $from='website';                if (isset($input_data['from']))                        $from                        =$input_data['from'];
  $only_id_asset=0;               if (isset($input_data['only_id_asset']))               $only_id_asset               =$input_data['only_id_asset'];
  $only_id_transfer_oxima_type=0; if (isset($input_data['only_id_transfer_oxima_type'])) $only_id_transfer_oxima_type =$input_data['only_id_transfer_oxima_type'];
  
  $transfer_no_warning=false;     if (isset($input_data['params']['transfer_no_warning'])) $transfer_no_warning =$input_data['params']['transfer_no_warning'];
  //print '<pre>ssssssmmmmmmmmmmm '.$transfer_no_warning;print_r($input_data);die();
  
  //file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/sss.txt',$from."\n".$id_transfer."\n".print_r($row_transfer,true)."\n".print_r($input_data,true));
  
  $gks_erp_cookie_id='';
  if ($from=='website') {
    if(isset($input_data['gks_erp_cookie_id'])) {
      $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
    }
    $transfer_title=$row_transfer['transfer_title'];
    $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
    gks_erp_cookie_start($gks_erp_cookie_id);
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  }
  
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  $db_lang='';$db_lang2='';if ($_gks_session['gks']['ui_lang']=='en-US') {$db_lang='_en_US';$db_lang2='_en';}
  
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  $return=array('success' => false, 'message' => base64_encode('generic gks_api_transfer_cmd_transfer_reservation_search error'),'data' => false, 'debug'=>'');
  $error_html=[];
  
  $val_direction='tori'; if (isset($input_data['get_data']['direction'])) $val_direction=trim_gks($input_data['get_data']['direction']); //tori tole 
  if ($val_direction!='tole') $val_direction='tori';
  
  //$val_from_id=0; if (isset($input_data['get_data']['from'])) $val_from_id=intval($input_data['get_data']['from']);
  
  $val_from_id=0;
  $val_from_place_id='';
  $val_from_place_lat=0;
  $val_from_place_lng=0;
  $val_from_place_formatted_address='';
  $val_from_place_url='';
  
  $val_to_id=0;
  $val_to_place_id='';
  $val_to_place_lat=0;
  $val_to_place_lng=0;
  $val_to_place_formatted_address='';
  $val_to_place_url='';
  

  
  //print '<pre>';print_r($input_data);die();
  
  $user_transfer_online_search_type=0;

  if (isset($input_data['get_data']['from']) and isset($input_data['get_data']['to'])) {
    $user_transfer_online_search_type=0;
  } else if (isset($input_data['get_data']['gmfrom'])==true  and isset($input_data['get_data']['gmto'])==false) {
    $user_transfer_online_search_type=2;
  } else if (isset($input_data['get_data']['gmfrom'])==false and isset($input_data['get_data']['gmto'])==true) {
    $user_transfer_online_search_type=1;
  } else if (isset($input_data['get_data']['gmfrom'])==true  and isset($input_data['get_data']['gmto'])==true) {
    $user_transfer_online_search_type=3;
  }
  
  //print '<pre>'.$user_transfer_online_search_type;die();
  
  if (isset($input_data['get_data']['from'])) {
    $val_from_id=0;   if (isset($input_data['get_data']['from']))   $val_from_id  =intval($input_data['get_data']['from']);
    
  } else if (isset($input_data['get_data']['gmfrom']) and isset($input_data['get_data']['latfrom']) and isset($input_data['get_data']['lngfrom'])) {
    $val_from_place_id=trim($input_data['get_data']['gmfrom']);
    $val_from_place_lat=floatval($input_data['get_data']['latfrom']);
    $val_from_place_lng=floatval($input_data['get_data']['lngfrom']);
    
    if ($val_from_place_id=='' or $val_from_place_lat==0 or $val_from_place_lng==0) {
      debug_mail(false,"gks_transfer_search_poi val_from_place_id val_from_place_lat val_from_place_lng",print_r($input_data['get_data'],true));
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
 
    $sql="select * from gks_cache_googlemaps_place 
    where place_id like '".$db_link->escape_string($val_from_place_id)."'
    and language='en'";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc(); 
      if ($from_backend==false) {
        $val_from_place_lat=floatval($row['lat']);
        $val_from_place_lng=floatval($row['lng']);
      }
      $val_from_place_url=$row['url'];
      $val_from_place_formatted_address=$row['address'];
      
      $val_from_id=2; //iparxei to place_id kai einai ana xiliometro
              
      //$return['message']=base64_encode( 'ggggggggggggg');return $return;
      
    } else {
    
      
//      $geocode_url='https://maps.googleapis.com/maps/api/geocode/json?language=en&place_id='.
//      rawurlencode($val_from_place_id).
//      '&key='.$GKS_GOOGLE_MAPS_API_KEY_SERVER;
      
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
          
        //echo '<pre>wwwwwwwwwwwwwwwwwwww ';print_r($response);die();

        if (is_array($response)) {
          if (isset($response['error_message']) and $response['error_message']!='') {
            debug_mail(false,'geocode_url error',$response_str);
            $return = array('success' => false, 'message' => base64_encode($response['error_message']));
            echo json_encode($return); die();
          } else if (isset($response['result']['geometry']['location']['lat']) and isset($response['result']['geometry']['location']['lng'])) {
            $val_from_place_lat_ff=floatval($response['result']['geometry']['location']['lat']);
            $val_from_place_lng_ff=floatval($response['result']['geometry']['location']['lng']);
            //echo '<pre>'.$val_from_place_lat_ff.'|'.$val_from_place_lng_ff;die();
            
            if ($from_backend==false) {
              $val_from_place_lat=$val_from_place_lat_ff;
              $val_from_place_lng=$val_from_place_lng_ff;
            } 
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
            
            $sql="insert into gks_cache_googlemaps_place (
            mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
            place_id,response,lat,lng,url,address,language
            ) values (
            now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
            '".$db_link->escape_string($val_from_place_id)."',
            '".$db_link->escape_string($response_str)."',
            ".number_format($val_from_place_lat_ff, 12, '.', '').",
            ".number_format($val_from_place_lng_ff, 12, '.', '').",
            '".$db_link->escape_string($val_from_place_url)."',
            '".$db_link->escape_string($val_from_place_formatted_address)."',
            'en'
            )";
            $result = $db_link->query($sql);
            if (!$result) {
              debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
              $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
          
            $val_from_id=2; //iparxei to place_id kai einai ana xiliometro
            
            
          } 
        }
      }
    }
  }

  
  
  
  if (isset($input_data['get_data']['to'])) {
    $val_to_id=0;   if (isset($input_data['get_data']['to']))   $val_to_id  =intval($input_data['get_data']['to']);
    //print '<pre>'.$val_from_id.'|'.$val_to_id;die();
  } else if (isset($input_data['get_data']['gmto']) and isset($input_data['get_data']['latto']) and isset($input_data['get_data']['lngto'])) {
    
    
    $val_to_place_id=trim($input_data['get_data']['gmto']);
    $val_to_place_lat=floatval($input_data['get_data']['latto']);
    $val_to_place_lng=floatval($input_data['get_data']['lngto']);
    
    if ($val_to_place_id=='' or $val_to_place_lat==0 or $val_to_place_lng==0) {
      debug_mail(false,"gks_transfer_search_poi val_to_place_id val_to_place_lat val_to_place_lng",print_r($input_data['get_data'],true));
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
 
    $sql="select * from gks_cache_googlemaps_place 
    where place_id like '".$db_link->escape_string($val_to_place_id)."'
    and language='en'";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc(); 
      if ($from_backend==false) {
        $val_to_place_lat=floatval($row['lat']);
        $val_to_place_lng=floatval($row['lng']);
      }
      $val_to_place_url=$row['url'];
      $val_to_place_formatted_address=$row['address'];
      
      $val_to_id=2; //iparxei to place_id kai einai ana xiliometro
              
      //$return['message']=base64_encode( 'ggggggggggggg');return $return;
      
    } else {
    
      
//      $geocode_url='https://maps.googleapis.com/maps/api/geocode/json?language=en&place_id='.
//      rawurlencode($val_to_place_id).
//      '&key='.$GKS_GOOGLE_MAPS_API_KEY_SERVER;
      
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
          
        //echo '<pre>wwwwwwwwwwwwwwwwwwww ';print_r($response);die();

        if (is_array($response)) {
          if (isset($response['error_message']) and $response['error_message']!='') {
            debug_mail(false,'geocode_url error',$response_str);
            $return = array('success' => false, 'message' => base64_encode($response['error_message']));
            echo json_encode($return); die();
          } else if (isset($response['result']['geometry']['location']['lat']) and isset($response['result']['geometry']['location']['lng'])) {
            $val_to_place_lat_ff=floatval($response['result']['geometry']['location']['lat']);
            $val_to_place_lng_ff=floatval($response['result']['geometry']['location']['lng']);
            //echo '<pre>'.$val_to_place_lat_ff.'|'.$val_to_place_lng_ff;die();
            
            if ($from_backend==false) {
              $val_to_place_lat=$val_to_place_lat_ff;
              $val_to_place_lng=$val_to_place_lng_ff;
            } 
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
            
            $sql="insert into gks_cache_googlemaps_place (
            mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
            place_id,response,lat,lng,url,address,language
            ) values (
            now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
            '".$db_link->escape_string($val_to_place_id)."',
            '".$db_link->escape_string($response_str)."',
            ".number_format($val_to_place_lat_ff, 12, '.', '').",
            ".number_format($val_to_place_lng_ff, 12, '.', '').",
            '".$db_link->escape_string($val_to_place_url)."',
            '".$db_link->escape_string($val_to_place_formatted_address)."',
            'en'
            )";
            $result = $db_link->query($sql);
            if (!$result) {
              debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
              $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
          
            $val_to_id=2; //iparxei to place_id kai einai ana xiliometro
            
            
          } 
        }
      }
    }
  }
  
  //echo '<pre>'.$user_transfer_online_search_type.'|'.$val_from_id.'|'.$val_to_id;die();
  //echo '<pre>ffffffffffffffff '.$user_transfer_online_search_type;die();


  if (in_array($user_transfer_online_search_type,[2,3])) {
    
    $sql="SELECT DISTINCTROW id_poi
    FROM gks_poi
    where poi_disable=0 
    and ".$val_from_place_lat." >=poi_bound_south
    and ".$val_from_place_lat." <=poi_bound_north
    and ".$val_from_place_lng." >=poi_bound_west
    and ".$val_from_place_lng." <=poi_bound_east";
    /*
    and id_poi in (
      select id_poi from (
        SELECT poi_id_from as id_poi
        FROM gks_transfer_pricelist
        where transfer_pricelist_disable=0
        GROUP BY poi_id_from
        union
        SELECT poi_id_to  as id_poi
        FROM gks_transfer_pricelist
        where transfer_pricelist_disable=0
        GROUP BY poi_id_to
      ) as mypp
      group by id_poi    
    
    )";   
    */
    
    //echo '<pre>'.$sql;die();
    
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    $geo_points=[];
    while ($row = $result->fetch_assoc()) {
      $geo_points[]=$row['id_poi'];
    }
    
    
    if (count($geo_points)>0) {
      $sql="select id_poi,poi_areas 
      from gks_poi 
      where id_poi in (".implode(',',$geo_points).")
      order by abs(poi_bound_north-poi_bound_south) * abs(poi_bound_east-poi_bound_west) asc";
      
      //echo '<pre>'.$sql;die();
      //debug_mail(false,"kostas check 1 sql", $sql);
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      $geo_areas=[];
      while ($row = $result->fetch_assoc()) {
        $geo_areas[$row['id_poi']]=unserialize($row['poi_areas']);
      }
      $point_is_inside=false;
      foreach ($geo_areas as $id_poi => $poi_areas) {
        if (isset($poi_areas['polygons']) and is_array($poi_areas['polygons']) and count($poi_areas['polygons'])>0) {
          //$return['message']=base64_encode('<pre>'.print_r($poi_areas['polygons'],true));return $return;
          foreach ($poi_areas['polygons'] as $polygon) {
            //$return['message']=base64_encode('<pre>'.print_r($polygon['points'],true));return $return;
            //print '<pre>'.$val_from_place_lng.'|'.$val_from_place_lat;print_r($polygon['points']); die();
            
            $point_is_inside=gks_transfer_is_in_polygon($val_from_place_lng,$val_from_place_lat,$polygon['points']);
            //$return['message']=base64_encode('<pre>|'.$point_is_inside."|\n".print_r($polygon['points'],true));return $return;
            $val_from_id=$id_poi;
            
            //$return['message']=base64_encode('<pre>|'.$point_is_inside."|\n".$val_from_id);return $return;
            //debug_mail(false,"kostas check 2", $id_poi.'|'.$point_is_inside);
            //echo '<pre>'.$id_poi.'|'.$point_is_inside;die();
            
            if ($point_is_inside) break;
          } 
        }
        if ($point_is_inside) break; 
      }
    }
    
    //echo '<pre>ddddddd '; print_r($poi_areas);die();
    
    //echo '<pre>ddddddd '.$val_from_id.'|'.count($geo_points);die();
    
    //$return['message']=base64_encode('<pre>'.$sql."\n".
    //print_r($geo_points,true)."\n".
    //print_r($geo_areas,true)
    //);return $return;
    
  }  
  
  if (in_array($user_transfer_online_search_type,[1,3])) {
    
    $sql="SELECT DISTINCTROW id_poi
    FROM gks_poi
    where poi_disable=0 
    and ".$val_to_place_lat." >=poi_bound_south
    and ".$val_to_place_lat." <=poi_bound_north
    and ".$val_to_place_lng." >=poi_bound_west
    and ".$val_to_place_lng." <=poi_bound_east";
    /*
    and id_poi in (
      select id_poi from (
        SELECT poi_id_from as id_poi
        FROM gks_transfer_pricelist
        where transfer_pricelist_disable=0
        GROUP BY poi_id_from
        union
        SELECT poi_id_to  as id_poi
        FROM gks_transfer_pricelist
        where transfer_pricelist_disable=0
        GROUP BY poi_id_to
      ) as mypp
      group by id_poi    
    
    )"; 
    */
      
    //echo '<pre>'.$sql;die();
    
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    $geo_points=[];
    while ($row = $result->fetch_assoc()) {
      $geo_points[]=$row['id_poi'];
    }
    
    
    if (count($geo_points)>0) {
      $sql="select id_poi,poi_areas 
      from gks_poi 
      where id_poi in (".implode(',',$geo_points).")
      order by abs(poi_bound_north-poi_bound_south) * abs(poi_bound_east-poi_bound_west) asc";
      
      //echo '<pre>'.$sql;die();
      //debug_mail(false,"kostas check 1 sql", $sql);
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      $geo_areas=[];
      while ($row = $result->fetch_assoc()) {
        $geo_areas[$row['id_poi']]=unserialize($row['poi_areas']);
      }
      $point_is_inside=false;
      foreach ($geo_areas as $id_poi => $poi_areas) {
        if (isset($poi_areas['polygons']) and is_array($poi_areas['polygons']) and count($poi_areas['polygons'])>0) {
          //$return['message']=base64_encode('<pre>'.print_r($poi_areas['polygons'],true));return $return;
          foreach ($poi_areas['polygons'] as $polygon) {
            //$return['message']=base64_encode('<pre>'.print_r($polygon['points'],true));return $return;
            //print '<pre>'.$val_to_place_lng.'|'.$val_to_place_lat;print_r($polygon['points']); die();
            
            $point_is_inside=gks_transfer_is_in_polygon($val_to_place_lng,$val_to_place_lat,$polygon['points']);
            //$return['message']=base64_encode('<pre>|'.$point_is_inside."|\n".print_r($polygon['points'],true));return $return;
            $val_to_id=$id_poi;
            //$return['message']=base64_encode('<pre>|'.$point_is_inside."|\n".$val_to_id);return $return;
            //debug_mail(false,"kostas check 2", $id_poi.'|'.$point_is_inside);
            //echo '<pre>'.$id_poi.'|'.$point_is_inside;die();
            
            if ($point_is_inside) break;
          } 
        }
        if ($point_is_inside) break; 
      }
    }
    //$return['message']=base64_encode('<pre>'.$sql."\n".
    //print_r($geo_points,true)."\n".
    //print_r($geo_areas,true)
    //);return $return;
    
  }
  
//  echo '<pre>globals '.
//  'user_transfer_online_search_type '.$user_transfer_online_search_type."\n".
//  'val_from_id '.$val_from_id."\n".
//  'val_from_place_id '.$val_from_place_id."\n".
//  'val_from_place_lat '.$val_from_place_lat."\n".
//  'val_from_place_lng '.$val_from_place_lng."\n".
//  'val_from_place_formatted_address '.$val_from_place_formatted_address."\n".
//  'val_from_place_url '.$val_from_place_url."\n".
//  "\n".
//  'val_to_id '.$val_to_id."\n".
//  'val_to_place_id '.$val_to_place_id."\n".
//  'val_to_place_lat '.$val_to_place_lat."\n".
//  'val_to_place_lng '.$val_to_place_lng."\n".
//  'val_to_place_formatted_address '.$val_to_place_formatted_address."\n".
//  'val_to_place_url '.$val_to_place_url."\n".
//  '';die();
  
  //echo '<pre>'.$val_to_id;die();
  
//  $return['message']=base64_encode(
//  '<pre>'.
//  'user_transfer_online_search_type '.$user_transfer_online_search_type."\n".
//  'val_to_id '.$val_to_id."\n".
//  'val_to_place_id '.$val_to_place_id."\n".
//  'val_to_place_lat '.$val_to_place_lat."\n".
//  'val_to_place_lng '.$val_to_place_lng."\n".
//  'val_to_place_formatted_address '.$val_to_place_formatted_address."\n".
//  'val_to_place_url '.$val_to_place_url."\n".
//  print_r($input_data['get_data'],true).
//  '</pre>');return $return;

  
  if ($val_direction=='tori') {
    if ($val_from_id<=0) $error_html[]=gks_lang('Ορίστε το αεροδρόμιο άφιξης');
    if ($val_to_id<=0) $error_html[]=gks_lang('Ορίστε την μετάβαση σε').'... (1)';
  } else {
    if ($val_from_id<=0) $error_html[]=gks_lang('Ορίστε το αεροδρόμιο αναχώρησης');
    if ($val_to_id<=0) $error_html[]=gks_lang('Ορίστε την τοποθεσία παραλαβής');
  }
  
  //echo '<pre>'.$user_transfer_online_search_type.'|'.$val_from_id.'|'.$val_to_id;die();
   
  //echo '<pre>';print_r($input_data);die();
  //file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/ggg'.rand(1000,9999).'.txt',print_r($input_data,true));
  //$input_data['get_data']['direction']='toli';
  
  //$return_data['input_data']=$input_data;
  $return['data']['val_from_id']=$val_from_id;
  $return['data']['val_from_descr']='';
  $return['data']['val_from_poi_type_id']=0;
  $return['data']['val_from_place_id']=$val_from_place_id;
  $return['data']['val_from_place_lat']=$val_from_place_lat;
  $return['data']['val_from_place_lng']=$val_from_place_lng;
  $return['data']['val_from_place_formatted_address']=$val_from_place_formatted_address;
  $return['data']['val_from_place_url']=$val_from_place_url;


  $return['data']['val_to_id']=$val_to_id;
  $return['data']['val_to_descr']='';
  $return['data']['val_to_poi_type_id']=0;
  $return['data']['val_to_place_id']=$val_to_place_id;
  $return['data']['val_to_place_lat']=$val_to_place_lat;
  $return['data']['val_to_place_lng']=$val_to_place_lng;
  $return['data']['val_to_place_formatted_address']=$val_to_place_formatted_address;
  $return['data']['val_to_place_url']=$val_to_place_url;


  $return['data']['user_country_from_ip']='';
  $return['data']['user_transfer_online_search_type']=$user_transfer_online_search_type;
  
  //print '<pre>aaaaaaaaa ';print_r($return['data']);die();
  //print '<pre>bbbbbbbbb ';print_r($input_data['get_data']);die();
  

  
  if (isset($input_data['gkip_from_user'])==false) $input_data['gkip_from_user']='';
  $input_data['gkip_from_user']=trim_gks($input_data['gkip_from_user']);
  if ($input_data['gkip_from_user']!='') {
    require_once('functions_ip.php');
    $country_initials=gks_get_country_from_ip($input_data['gkip_from_user']);
    $return['data']['user_country_from_ip_raw']=$input_data['gkip_from_user'].'|'.$country_initials;
    if ($country_initials=='--') $country_initials='';

    if ($country_initials!='') {
      $sql="select country_initials from gks_country where country_initials like '".$db_link->escape_string($country_initials)."'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        
        $return['data']['user_country_from_ip']=$row['country_initials'];  
      }
    }
    
    
  }
  //print '<pre>aaaaaaaaa ';print_r($return['data']);die();
  
  //print '<pre>aaaaaaaaa '.$from;die();
  if ($from=='website' and $row_transfer['transfer_default_availability']==0) {
    debug_mail(false,"gks_transfer is not availability", print_r($row_transfer,true));
    $return['message']=base64_encode(gks_lang('Αυτήν την στιγμή το κανάλι δεν είναι διαθέσιμο').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
  
  

  $val_date1_sql=''; $val_date1=''; if (isset($input_data['get_data']['date1'])) $val_date1=trim_gks($input_data['get_data']['date1']);
  $val_date2_sql=''; $val_date2=''; if (isset($input_data['get_data']['date2'])) $val_date2=trim_gks($input_data['get_data']['date2']);
  
  if ($from=='website' and $val_date1=='') {
    $val_date1=date('d/m/Y', time() + 5*24*60*60).' 14:00';
  }
  
  //echo '<pre>sssssdddd '.$val_date1;die();
  
  $return['debug'].='val_date1: '.$val_date1."\n";
  $return['debug'].='val_date2: '.$val_date2."\n";
  
  $val_date1_time=0;
  $val_date1_sql=gks_parse_date($val_date1, $val_date1_time);
  $val_date2_time=0;
  $val_date2_sql=gks_parse_date($val_date2, $val_date2_time);
  $return['debug'].='val_date1_sql: '.$val_date1_sql."\n".'val_date2_sql: '.$val_date2_sql."\n";  
  
  if ($val_date1_time==0) $error_html[]=gks_lang('Ορίστε την άφιξη πτήσης');
  else if ($from=='website' and $val_date1_time <= (time() + ($row_transfer['transfer_reservation_min_hours_to_book']*60*60)))  $error_html[]=str_replace('%1',$row_transfer['transfer_reservation_min_hours_to_book'], gks_lang('Ρυθμίστε την άφιξη πτήσης μετά από %1 ώρες από τώρα'));
  else if ($from=='website' and $val_date1_time > (time() + ($row_transfer['transfer_reservation_days_future']*24*60*60))) $error_html[]=str_replace('%1', $row_transfer['transfer_reservation_days_future'],gks_lang('Ρυθμίστε την άφιξη πτήσης έως %1 ημέρες από τώρα'));
  
  if ($val_date2_time>0) {
    if (($val_date1_time + (1*60*60))  > $val_date2_time) $error_html[]=gks_lang('Ορίστε την επιστροφή της πτήσης');
    else if ($from=='website' and $val_date2_time <= (time() + ($row_transfer['transfer_reservation_min_hours_to_book']*60*60)))  $error_html[]=str_replace('%1',$row_transfer['transfer_reservation_min_hours_to_book'],gks_lang('Ρυθμίστε την επιστροφή μετά από %1 ώρες από τώρα'));
    else if ($from=='website' and $val_date2_time > (time() + ($row_transfer['transfer_reservation_days_future']*24*60*60))) $error_html[]=str_replace('%1',$row_transfer['transfer_reservation_days_future'],gks_lang('Ρυθμίστε την επιστροφή έως %1 ημέρες από τώρα'));
  }
  
  
  if ($from=='website' and trim_gks($row_transfer['transfer_date_open'])!='' and $val_date1_time < strtotime($row_transfer['transfer_date_open'])) {
    debug_mail(false,"gks_transfer is not availability", print_r($row_transfer,true)."\n".print_r($input_data['get_data'],true));
    $return['message']=base64_encode(gks_lang('Δεν είναι διαθέσιμη η υπηρεσία για αυτήν την ημερομηνία').'<br>'.gks_lang('Δοκιμάστε να κάνετε μια άλλη αναζήτηση'));return $return;}
  if ($from=='website' and trim_gks($row_transfer['transfer_date_close'])!='' and $val_date1_time > strtotime($row_transfer['transfer_date_close'])) {
    debug_mail(false,"gks_transfer is not availability", print_r($row_transfer,true)."\n".print_r($input_data['get_data'],true));
    $return['message']=base64_encode(gks_lang('Δεν είναι διαθέσιμη η υπηρεσία για αυτήν την ημερομηνία').'<br>'.gks_lang('Δοκιμάστε να κάνετε μια άλλη αναζήτηση'));return $return;}
  if ($val_date2_time>0) {
    if ($from=='website' and trim_gks($row_transfer['transfer_date_open'])!='' and $val_date2_time < strtotime($row_transfer['transfer_date_open'])) {
      debug_mail(false,"gks_transfer is not availability", print_r($row_transfer,true)."\n".print_r($input_data['get_data'],true));
      $return['message']=base64_encode(gks_lang('Δεν είναι διαθέσιμη η υπηρεσία για αυτήν την ημερομηνία').'<br>'.gks_lang('Δοκιμάστε να κάνετε μια άλλη αναζήτηση'));return $return;}
    if ($from=='website' and trim_gks($row_transfer['transfer_date_close'])!='' and $val_date2_time > strtotime($row_transfer['transfer_date_close'])) {
      debug_mail(false,"gks_transfer is not availability", print_r($row_transfer,true)."\n".print_r($input_data['get_data'],true));
      $return['message']=base64_encode(gks_lang('Δεν είναι διαθέσιμη η υπηρεσία για αυτήν την ημερομηνία').'<br>'.gks_lang('Δοκιμάστε να κάνετε μια άλλη αναζήτηση'));return $return;}
    
  }
  
  
  
  //echo '<pre>sssssssffffffffff ';print_r($return);die();  
  //return $return;

  

  if ($val_from_id>111) {
    $sql="SELECT gks_poi.id_poi, gks_poi.poi_descr,           gks_poi_en_US.poi_descr_en_US,           poi_descr".$db_lang."      as poi_descr_i18n,
                                 gks_poi_type.poi_type_descr, gks_poi_type_en_US.poi_type_descr_en_US, poi_type_descr".$db_lang." as poi_type_descr_i18n,
    gks_poi.poi_type_id,
    gks_poi.poi_map_latitude,gks_poi.poi_map_longitude
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
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $return['data']['val_from_descr']=(isset($row['poi_descr_i18n']) ? trim_gks($row['poi_descr_i18n']) : trim_gks($row['poi_descr_en_US']));
      $return['data']['val_from_poi_type_id']=intval($row['poi_type_id']);
      $return['data']['val_from_poi_map_latitude']=floatval($row['poi_map_latitude']);
      $return['data']['val_from_poi_map_longitude']=floatval($row['poi_map_longitude']);
      
    } 
    //echo '<pre>gggggddddddd ';print_r($return['data']);die();
  }
  
  
  if ($val_to_id>111) {
    $sql="SELECT gks_poi.id_poi, gks_poi.poi_descr,           gks_poi_en_US.poi_descr_en_US,           poi_descr".$db_lang." as poi_descr_i18n,
                                 gks_poi_type.poi_type_descr, gks_poi_type_en_US.poi_type_descr_en_US, poi_type_descr".$db_lang." as poi_type_descr_i18n,
    gks_poi.poi_type_id,
    gks_poi.poi_map_latitude,gks_poi.poi_map_longitude
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
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $return['data']['val_to_descr']=(isset($row['poi_descr_i18n']) ? trim_gks($row['poi_descr_i18n']) : trim_gks($row['poi_descr_en_US']));
      $return['data']['val_to_poi_type_id']=intval($row['poi_type_id']);
      $return['data']['val_to_poi_map_latitude']=floatval($row['poi_map_latitude']);
      $return['data']['val_to_poi_map_longitude']=floatval($row['poi_map_longitude']);
    }
  }
  
  //echo '<pre>sssssssffffffffff ';print_r($return);die();  
  
  if (in_array($user_transfer_online_search_type,[2,3]) and $val_from_place_id!='') {
    if ($val_from_place_formatted_address=='') {
    
      $sql="select * from gks_cache_googlemaps_place
      where place_id='".$db_link->escape_string($val_from_place_id)."'
      and language='en'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $val_from_place_formatted_address=$row['address'];
        $val_from_place_lat=floatval($row['lat']);
        $val_from_place_lng=floatval($row['lng']);
      }
    }

    $return['data']['val_from_descr']=$val_from_place_formatted_address;
    //$return['data']['val_from_poi_type_id']=0;
    $return['data']['val_from_place_lat']=$val_from_place_lat;
    $return['data']['val_from_place_lng']=$val_from_place_lng;
    //$return['message']=base64_encode('<pre>ssss '.$val_from_id.'|'.$return['data']['val_from_descr']);return $return;
    //if ($val_from_id==2) {
      $return['data']['val_from_poi_map_latitude']=$val_from_place_lat;
      $return['data']['val_from_poi_map_longitude']=$val_from_place_lng;      
    //}
  }
  
  if (in_array($user_transfer_online_search_type,[1,3]) and $val_to_place_id!='') {
    if ($val_to_place_formatted_address=='') {
    
      $sql="select * from gks_cache_googlemaps_place
      where place_id='".$db_link->escape_string($val_to_place_id)."'
      and language='en'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $val_to_place_formatted_address=$row['address'];
        $val_to_place_lat=floatval($row['lat']);
        $val_to_place_lng=floatval($row['lng']);
      }
    }

    $return['data']['val_to_descr']=$val_to_place_formatted_address;
    //$return['data']['val_to_poi_type_id']=0;
    $return['data']['val_to_place_lat']=$val_to_place_lat;
    $return['data']['val_to_place_lng']=$val_to_place_lng;
    //$return['message']=base64_encode('<pre>ssss '.$val_to_id.'|'.$return['data']['val_to_descr']);return $return;
    //if ($val_to_id==2) {
      $return['data']['val_to_poi_map_latitude']=$val_to_place_lat;
      $return['data']['val_to_poi_map_longitude']=$val_to_place_lng;      
    //}
  }
  //echo '<pre>dddddddddddddd '.$user_transfer_online_search_type.'|'.$return['data']['val_to_descr'].
  //print_r($input_data['get_data'],true);die();
  //print '<pre>sssssssssss ';print_r($return['data']);die();
  
  //echo '<pre>sssssssffffffffff2 ';print_r($return);die();  
  
  $directions_from_name='';
  $directions_from_place_id='';
  $directions_from_lat=0;
  $directions_from_lng=0;
  $directions_to_name='';
  $directions_to_place_id='';
  $directions_to_lat=0;
  $directions_to_lng=0;
  
        
  if (in_array($user_transfer_online_search_type,[0,1])) {
    if (isset($return['data']['val_from_poi_map_latitude']))  $directions_from_lat=$return['data']['val_from_poi_map_latitude'];
    if (isset($return['data']['val_from_poi_map_longitude'])) $directions_from_lng=$return['data']['val_from_poi_map_longitude'];
    if (isset($return['data']['val_from_descr'])) $directions_from_name=$return['data']['val_from_descr'];
  } else if (in_array($user_transfer_online_search_type,[2,3])) {
    if (isset($return['data']['val_from_place_lat'])) $directions_from_lat=$return['data']['val_from_place_lat'];
    if (isset($return['data']['val_from_place_lng'])) $directions_from_lng=$return['data']['val_from_place_lng'];
    if (isset($return['data']['val_from_descr'])) $directions_from_name=$return['data']['val_from_descr'];
    if (isset($val_from_place_id)) $directions_from_place_id=$val_from_place_id;
  }
  
  if (in_array($user_transfer_online_search_type,[0,2])) {
    if (isset($return['data']['val_to_poi_map_latitude']))  $directions_to_lat=$return['data']['val_to_poi_map_latitude'];
    if (isset($return['data']['val_to_poi_map_longitude'])) $directions_to_lng=$return['data']['val_to_poi_map_longitude'];
    if (isset($return['data']['val_to_descr'])) $directions_to_name=$return['data']['val_to_descr'];
  } else if (in_array($user_transfer_online_search_type,[1,3])) {
    if (isset($return['data']['val_to_place_lat'])) $directions_to_lat=$return['data']['val_to_place_lat'];
    if (isset($return['data']['val_to_place_lng'])) $directions_to_lng=$return['data']['val_to_place_lng'];
    if (isset($return['data']['val_to_descr'])) $directions_to_name=$return['data']['val_to_descr'];
    if (isset($val_to_place_id)) $directions_to_place_id=$val_to_place_id;
  }
  
  //print '<pre>sssssssssss ';print_r($return['data']);die();
  
  //debug_mail(false,'from',$from.' '.$user_transfer_online_search_type);
  
  if ($from=='website') {
    $transfer_areas=trim_gks($row_transfer['transfer_areas']);
    if ($transfer_areas!='') {
      $transfer_areas=unserialize($transfer_areas);
      if (isset($transfer_areas['polygons']) and is_array($transfer_areas['polygons']) and count($transfer_areas['polygons'])>0) {
        
        $point_from_is_inside=true;
        if ($directions_from_place_id!='' and $directions_from_lat<>0 and $directions_from_lng<>0) {
          $point_from_is_inside=false;
          foreach ($transfer_areas['polygons'] as $polygon) {
            $point_from_is_inside=gks_transfer_is_in_polygon($directions_from_lng,$directions_from_lat,$polygon['points']);
            if ($point_from_is_inside) break;
          }
        } 
        
        $point_to_is_inside=true;
        if ($directions_to_place_id!='' and $directions_to_lat<>0 and $directions_to_lng<>0) {
          $point_to_is_inside=false;
          foreach ($transfer_areas['polygons'] as $polygon) {
            $point_to_is_inside=gks_transfer_is_in_polygon($directions_to_lng,$directions_to_lat,$polygon['points']);
            if ($point_to_is_inside) break;
          }          
        }
        
        if ($point_from_is_inside==false and $point_to_is_inside==false) {
          $error_html[]=gks_lang('Δεν βρέθηκε η διαδρομή');
        }
      }
    }
    
    $transfer_countries_initials=array();
    $transfer_countries=trim_gks($row_transfer['transfer_countries']);
    if ($transfer_countries!='') {
      $transfer_countries=unserialize($transfer_countries);
      if (is_array($transfer_countries) and count($transfer_countries)>0) {
         
        $sql="select country_initials from gks_country where id_country in (".implode(',',$transfer_countries).")";
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
          $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
        while ($row = $result->fetch_assoc()) {
          $transfer_countries_initials[]=$row['country_initials'];
        }  
        
      }
    }
    

    
  
  
    $directions_from_place_country='';
    if (count($transfer_countries_initials)>0 and $directions_from_place_id!='') {
      
      $sql="select response from gks_cache_googlemaps_place
      where place_id='".$db_link->escape_string($directions_from_place_id)."'
      and language='en'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $response=$row['response'];
        $response=json_decode($response, true);
        if (isset($response['result']) and isset($response['result']['address_components']) and is_array($response['result']['address_components'])) {
          foreach ($response['result']['address_components'] as $addcc) {
            if (isset($addcc['types']) and  isset($addcc['types'][0]) and $addcc['types'][0]=='country') {
              if (isset($addcc['short_name'])) {
                $directions_from_place_country=$addcc['short_name'];
              }
            }
          } 
          //echo '<pre>ssfffffffffff ';print_r($response);die();
        }
      }
    }

    $directions_to_place_country='';
    if (count($transfer_countries_initials)>0 and $directions_to_place_id!='') {
      
      $sql="select response from gks_cache_googlemaps_place
      where place_id='".$db_link->escape_string($directions_to_place_id)."'
      and language='en'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $response=$row['response'];
        $response=json_decode($response, true);
        if (isset($response['result']) and isset($response['result']['address_components']) and is_array($response['result']['address_components'])) {
          foreach ($response['result']['address_components'] as $addcc) {
            if (isset($addcc['types']) and  isset($addcc['types'][0]) and $addcc['types'][0]=='country') {
              if (isset($addcc['short_name'])) {
                $directions_to_place_country=$addcc['short_name'];
              }
            }
          }
          //echo '<pre>ssfffffffffff ';print_r($response);die();
        }
      }
    }
      
    //echo '<pre>dddddddddd ';print_r($transfer_countries);print_r($transfer_countries_initials);die();

  
    if (count($transfer_countries_initials)>0) {
      if (($directions_from_place_country!='' and in_array($directions_from_place_country,$transfer_countries_initials)==false)
          or
          ($directions_to_place_country!='' and in_array($directions_to_place_country,$transfer_countries_initials)==false)) {
        $error_html[]=gks_lang('Δεν βρέθηκε η διαδρομή');
      }
    }
  }
  
  
  //echo '<pre>ssfffffffffff '.
  //$directions_from_place_id.'|'.$directions_from_place_country.'|'.
  //$directions_to_place_id.'|'.$directions_to_place_country;
  //die();
  
  
  
  //echo '<pre>sssssssffffffffff2 ';print_r($return);die();  
  
  //debug_mail(false,'distance duration',print_r($input_data,true));
  
  $distance_and_duration_set_from='';
  $distance=0;$duration=0;
  //echo '<pre>sssssssss '.$from_backend.'|'.$input_data['get_data']['apostasi'].'|'.$input_data['get_data']['diarkeia'];die();
  if ($from_backend and $input_data['get_data']['apostasi']>0 and $input_data['get_data']['diarkeia']>0) {
    $distance=$input_data['get_data']['apostasi'];
    $duration=$input_data['get_data']['diarkeia'];
    $distance_and_duration_set_from='backend';
  } else {
    
    //debug_mail(false,'directions from to',$directions_from_lat.' '.$directions_from_lng.' '.$directions_to_lat.' '.$directions_to_lng);
    
    if ($directions_from_lat<>0 and
        $directions_from_lng<>0 and
        $directions_to_lat<>0 and
        $directions_to_lng<>0) {
      //strogilopiisi sta 4 dekadika
      
      $sql="select * from gks_cache_googlemaps_directions
      where 
      from_lat >=".number_format($directions_from_lat-$transfer_akrivia,4,'.','')." and 
      from_lat<=".number_format($directions_from_lat+$transfer_akrivia,4,'.','')." and
      from_lng >=".number_format($directions_from_lng-$transfer_akrivia,4,'.','')." and 
      from_lng<=".number_format($directions_from_lng+$transfer_akrivia,4,'.','')." and
      to_lat >=".number_format($directions_to_lat-$transfer_akrivia,4,'.','')." and 
      to_lat<=".number_format($directions_to_lat+$transfer_akrivia,4,'.','')." and
      to_lng >=".number_format($directions_to_lng-$transfer_akrivia,4,'.','')." and 
      to_lng<=".number_format($directions_to_lng+$transfer_akrivia,4,'.','')."
      
      order by (
      abs(from_lat-".$directions_from_lat.") + 
      abs(from_lng-".$directions_from_lng.") + 
      abs(to_lat-".$directions_to_lat.") + 
      abs(to_lng-".$directions_to_lng.") 
      ) asc
      limit 1";
  
      //debug_mail(false,'gks_cache_googlemaps_directions',$sql);
  
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      if ($result->num_rows>=1) {
        $fpoint=[];
        $row = $result->fetch_assoc();
        $distance=intval($row['distance']);
        $duration=intval($row['duration']);
        $distance_and_duration_set_from='google_map';
        $ferries_count=intval($row['ferries_count']);
        //echo '<pre>hhhh '.$distance;die();
        
        if ($user_transfer_online_search_type>=1) {
          if ($ferries_count>0) {
            $error_html[]=gks_lang('Δεν βρέθηκε η διαδρομή');
          }
        }
          
  
        
        
      } else {
        //https://developers.google.com/maps/documentation/directions/get-directions
        
        $myurl="https://maps.googleapis.com/maps/api/directions/json?origin=".
        number_format($directions_from_lat,14,'.','').",".
        number_format($directions_from_lng,14,'.','').
        "&destination=".
        number_format($directions_to_lat,14,'.','').",".
        number_format($directions_to_lng,14,'.','').
        "&key=".$GKS_GOOGLE_MAPS_API_KEY_SERVER."&mode=driving&avoid=ferries"; //walking driving bicycling transit 
        
        //echo '<pre>'.$myurl;die();
       
        //debug_mail(false,'distance get',$myurl);
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $myurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
        $json=curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($json, true);
  
        if (isset($obj['routes'][0]['legs'][0]['distance']['value'])) {
          $distance = intval($obj['routes'][0]['legs'][0]['distance']['value']);
          $duration = round(intval($obj['routes'][0]['legs'][0]['duration']['value'])/60,0);
          $distance_and_duration_set_from='google_map';
          $ferries_count=0;
          if (isset($obj['routes'][0]['legs'][0]['steps'])) {
            $steps=$obj['routes'][0]['legs'][0]['steps'];
            foreach ($steps as $sone) {
              if (isset($sone['maneuver']) and ($sone['maneuver']=='ferry' or $sone['maneuver']=='ferry-train')) {
                $ferries_count++;
              }
            }
          } 
          
          //echo '<pre>ffffff '.$distance."\n".$myurl;die();
          
          $sql="insert into gks_cache_googlemaps_directions (
          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
          from_name,from_place_id,from_lat,from_lng,
          to_name,to_place_id,to_lat,to_lng,
          distance,duration,
          response,
          ferries_count
          ) values (
          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
          '".$db_link->escape_string($directions_from_name)."',
          '".$db_link->escape_string($directions_from_place_id)."',
          ".$directions_from_lat.",
          ".$directions_from_lng.",
          '".$db_link->escape_string($directions_to_name)."',
          '".$db_link->escape_string($directions_to_place_id)."',
          ".$directions_to_lat.",
          ".$directions_to_lng.",
          ".$distance.",
          ".$duration.",
          '".$db_link->escape_string($json)."',
          ".$ferries_count.")";
          $result = $db_link->query($sql);    
          if (!$result) {
            debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
            $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
          
          
          
          if ($user_transfer_online_search_type>=1) {
            if ($ferries_count>0) {
              $error_html[]=gks_lang('Δεν βρέθηκε η διαδρομή');
            }
          }
          

          
        } else {
          debug_mail(false,'no distance found',$myurl.'<br>'.$json);
          
          if ($user_transfer_online_search_type>=1) {
            $error_html[]=gks_lang('Δεν βρέθηκε η διαδρομή');
          }
          
        }                  
        
      }
        
    }
  }
  
//  if ($distance_and_duration_set_from=='google_map' or $distance<=0 or $duration<=0) {
//    $sql_dd="SELECT poi_diadromes_apostasi_se_metra,poi_diadromes_diarkeia_se_lepta,
//    if (poi_id_from=".$val_from_id." and poi_id_to=".$val_to_id.",1,2) as is_diadromi_real 
//    FROM gks_poi_diadromes 
//    WHERE poi_diadromes_disable=0 
//    and poi_diadromes_apostasi_se_metra>0
//    and poi_diadromes_diarkeia_se_lepta>0
//    and  (
//      (
//        poi_id_from=".$val_from_id." and (
//          poi_id_to=".$val_to_id." or  
//          poi_id_to in (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")
//        )
//      ) or (
//        poi_id_to=".$val_from_id." and (
//          poi_id_from=".$val_to_id." or  
//          poi_id_from in (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")
//        )
//      )
//    )
//    order by is_diadromi_real, id_poi_diadromes desc limit 1";
//
//    $result_dd = $db_link->query($sql_dd);
//    if (!$result_dd) {
//      debug_mail(false,"gks_transfer_search_poi sql error", $sql1."\n".$db_link->errno . '-'.$db_link->error);
//      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
//    if ($result_dd->num_rows>=1) {
//      $row_dd=$result_dd->fetch_assoc(); 
//      $distance=$row_dd['poi_diadromes_apostasi_se_metra'];
//      $duration=$row_dd['poi_diadromes_diarkeia_se_lepta'];
//    }
//  }
  
  $return['data']['val_distance']=$distance;
  $return['data']['val_duration']=$duration;
  //echo '<pre>dddddddddd '.$distance;die();
  //echo '<pre>'; print_r($return);die();
  //$error_html[]='<pre>'.print_r($return['data'],true);
       
  //$return['message']=base64_encode('<pre>'.$distance.'|'.$duration);return $return;
        
  
  //$from_lat
  
  //$return['message']=base64_encode('<pre>'.$val_to_id.'|'.$return['data']['val_to_descr'].'|'.print_r($error_html,true));return $return;
  //echo 9/0;
  
  if ($val_direction=='tori') {
    if ($return['data']['val_from_descr']=='') $error_html[]=gks_lang('Ορίστε το αεροδρόμιο άφιξης');
    if ($return['data']['val_to_descr']=='') $error_html[]=gks_lang('Ορίστε την μετάβαση').' σε... (2)';
  } else {
    if ($return['data']['val_from_descr']=='') $error_html[]=gks_lang('Ορίστε το αεροδρόμιο αναχώρησης');
    if ($return['data']['val_to_descr']=='') $error_html[]=gks_lang('Ορίστε την τοποθεσία παραλαβής');
  }
  //echo '<pre>ddddd ';print_r($return);echo '</pre>';
  
  //$return['message']=base64_encode('<pre>'.$val_to_id.'|'.$return['data']['val_to_descr']);return $return;
  
  if (in_array($user_transfer_online_search_type,[2,3])) {
    $return['data']['val_from_descr']=$val_from_place_formatted_address;
  }
  if (in_array($user_transfer_online_search_type,[1,3])) {
    $return['data']['val_to_descr']=$val_to_place_formatted_address;
  }

  //echo '<pre>sssssssffffffffff2 ';print_r($return);die(); 

  $val_adults=2; if (isset($input_data['get_data']['adults'])) $val_adults=intval($input_data['get_data']['adults']);
  $val_children=0; if (isset($input_data['get_data']['children'])) $val_children=intval($input_data['get_data']['children']);
  $val_infants=0; if (isset($input_data['get_data']['infants'])) $val_infants=intval($input_data['get_data']['infants']);

  if ($val_adults<1) $error_html[]=gks_lang('Ορίστε τους ενήλικες επιβάτες');
  if ($val_children<0) $val_children=0;
  if ($val_infants<0) $val_infants=0;

  $val_passengers=$val_adults+$val_children+$val_infants;
  
  
  $return['data']['val_direction']=$val_direction;
  $return['data']['val_date1_time']=$val_date1_time;
  $return['data']['val_date2_time']=$val_date2_time;
  $return['data']['val_adults']=$val_adults;
  $return['data']['val_children']=$val_children;
  $return['data']['val_infants']=$val_infants;
  $return['data']['val_passengers']=$val_passengers;
  $return['data']['id_transfer']=$id_transfer;
  $return['data']['guid']=guid_for_transfer_reservation();
  $return['data']['temp_session_id']= $gks_erp_cookie_id;


  $return['data']['before_seconds_1']=0;
  $return['data']['before_seconds_2']=0;
  $return['data']['start_seconds_1']=0;
  $return['data']['start_seconds_2']=0;
  $return['data']['pick_up_time_real_1']=0;
  $return['data']['pick_up_time_real_2']=0;
  
  

  $return['transfer_properties']=array(
    'transfer_empty_cart_woo' => intval($row_transfer['transfer_empty_cart_woo']),
    'transfer_price_round_type' => intval($row_transfer['transfer_price_round_type']),
    'transfer_reservation_can_select_oxima' => intval($row_transfer['transfer_reservation_can_select_oxima']),
    'transfer_reservation_days_future' => intval($row_transfer['transfer_reservation_days_future']),
    'transfer_reservation_min_hours_to_book' => intval($row_transfer['transfer_reservation_min_hours_to_book']),
    'transfer_reservation_min_hours_to_book_group_multi' => intval($row_transfer['transfer_reservation_min_hours_to_book_group_multi']),
    'transfer_reservation_group_multi_date_range' => unserialize($row_transfer['transfer_reservation_group_multi_date_range']),
    'transfer_use_checkout_system' => trim_gks($row_transfer['transfer_use_checkout_system']),
    'transfer_sms_text_message_enable' => intval($row_transfer['transfer_sms_text_message_enable']),
    'transfer_sms_text_message_price' => floatval($row_transfer['transfer_sms_text_message_price']),
    'transfer_cancellation_protection_enable' => intval($row_transfer['transfer_cancellation_protection_enable']),
    'transfer_cancellation_protection_price' => floatval($row_transfer['transfer_cancellation_protection_price']),
    'transfer_terms_and_policy_frontend' => intval($row_transfer['transfer_terms_and_policy_frontend']),
    'transfer_multi_cars' => intval($row_transfer['transfer_multi_cars']),
    //'transfer_online_search_type' => intval($row_transfer['transfer_online_search_type']),
    
    'transfer_outward_from_airplane_message' => trim_gks($row_transfer['transfer_outward_from_airplane_message']),
    'transfer_outward_from_train_message' => trim_gks($row_transfer['transfer_outward_from_train_message']),
    'transfer_outward_from_cruise_message' => trim_gks($row_transfer['transfer_outward_from_cruise_message']),
    'transfer_outward_from_location_message' => trim_gks($row_transfer['transfer_outward_from_location_message']),
    
    
    'transfer_outward_from_pick_up_point' => trim_gks($row_transfer['transfer_outward_from_pick_up_point']),
    'transfer_outward_from_pick_up_time' => trim_gks($row_transfer['transfer_outward_from_pick_up_time']),
    'transfer_outward_from_pick_up_time_start_minutes_airplane' => intval($row_transfer['transfer_outward_from_pick_up_time_start_minutes_airplane']),
    'transfer_outward_from_pick_up_time_start_minutes_train' => intval($row_transfer['transfer_outward_from_pick_up_time_start_minutes_train']),
    'transfer_outward_from_pick_up_time_start_minutes_cruise' => intval($row_transfer['transfer_outward_from_pick_up_time_start_minutes_cruise']),
    'transfer_outward_from_pick_up_time_text_airplane' => trim_gks($row_transfer['transfer_outward_from_pick_up_time_text_airplane']),
    'transfer_outward_from_pick_up_time_text_train' => trim_gks($row_transfer['transfer_outward_from_pick_up_time_text_train']),
    'transfer_outward_from_pick_up_time_text_cruise' => trim_gks($row_transfer['transfer_outward_from_pick_up_time_text_cruise']),
    'transfer_outward_from_pick_up_time_text_location' => trim_gks($row_transfer['transfer_outward_from_pick_up_time_text_location']),
    
    
    
    'transfer_outward_from_flight_arrival_time' => trim_gks($row_transfer['transfer_outward_from_flight_arrival_time']),

    'transfer_outward_to_drop_off_point' => trim_gks($row_transfer['transfer_outward_to_drop_off_point']),
    'transfer_outward_to_flight_departure_time' => trim_gks($row_transfer['transfer_outward_to_flight_departure_time']),

    'transfer_return_from_airplane_message' => trim_gks($row_transfer['transfer_return_from_airplane_message']),
    'transfer_return_from_train_message' => trim_gks($row_transfer['transfer_return_from_train_message']),
    'transfer_return_from_cruise_message' => trim_gks($row_transfer['transfer_return_from_cruise_message']),
    'transfer_return_from_location_message' => trim_gks($row_transfer['transfer_return_from_location_message']),
    
    'transfer_return_from_address_different' => trim_gks($row_transfer['transfer_return_from_address_different']),
    'transfer_return_from_pick_up_time' => trim_gks($row_transfer['transfer_return_from_pick_up_time']),
    'transfer_return_from_pick_up_time_start_minutes_airplane' => intval($row_transfer['transfer_return_from_pick_up_time_start_minutes_airplane']),
    'transfer_return_from_pick_up_time_start_minutes_train' => intval($row_transfer['transfer_return_from_pick_up_time_start_minutes_train']),
    'transfer_return_from_pick_up_time_start_minutes_cruise' => intval($row_transfer['transfer_return_from_pick_up_time_start_minutes_cruise']),
    'transfer_return_from_pick_up_time_text_airplane' => trim_gks($row_transfer['transfer_return_from_pick_up_time_text_airplane']),
    'transfer_return_from_pick_up_time_text_train' => trim_gks($row_transfer['transfer_return_from_pick_up_time_text_train']),
    'transfer_return_from_pick_up_time_text_cruise' => trim_gks($row_transfer['transfer_return_from_pick_up_time_text_cruise']),
    'transfer_return_from_pick_up_time_text_location' => trim_gks($row_transfer['transfer_return_from_pick_up_time_text_location']),
    
    'transfer_return_from_flight_arrival_time' => trim_gks($row_transfer['transfer_return_from_flight_arrival_time']),

    'transfer_return_to_flight_departure_time' => trim_gks($row_transfer['transfer_return_to_flight_departure_time']),
    'transfer_return_to_address_different' => trim_gks($row_transfer['transfer_return_to_address_different']),
    
    
    
  );
    
  //print '<pre>ppppppppppp ';print_r($error_html); die();
  if (count($error_html)>0) {
    $return['message']=base64_encode(implode('<br>',$error_html));
    return $return;
  }
  
  //$return['message']=base64_encode('<pre>ssss |'.$val_to_id.'|'.$return['data']['val_to_descr']);return $return;  

  

  $sql="select * from gks_custom_field where custom_table_id=42 and field_disabled=0 and field_label='Service highlights private' and field_type_id=501";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
    $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
  $service_highlights_private_field=''; 
  $service_highlights_private_attr=[];
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $service_highlights_private_field='cf'.$row['id_custom_field'];
    $temp=trim_gks($row['field_attr']);
    //$return['debug'].="field_attr\n".print_r($temp,true)."\n";     
    if ($temp!='') {
      $temp=unserialize($temp);
      if (is_array($temp) and isset($temp['options'])) {
            
        $options=[];
        foreach ($temp['options'] as $myoption) {
          //$return['debug'].="myoption\n".print_r($myoption,true)."\n"; 
          if (isset($myoption['value']) and isset($myoption['text'])) {
            $parts=explode(':',$myoption['text']);
            if (count($parts)>=2) {
              $text=$parts[0];
              $code=$parts[1];
            } else {
              $text=$parts[0];
              $code=$parts[0];
            }
            $value=intval($myoption['value']);
            $service_highlights_private_attr[$value]=array('value' => $value,'text' => $text, 'code' => $code);
          }
        } 
      }
      
    }
  }
  
  $sql="select * from gks_custom_field where custom_table_id=42 and field_disabled=0 and field_label='Service highlights shared' and field_type_id=501";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
    $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
  $service_highlights_shared_field=''; 
  $service_highlights_shared_attr=[];
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $service_highlights_shared_field='cf'.$row['id_custom_field'];
    $temp=trim_gks($row['field_attr']);
    //$return['debug'].="field_attr\n".print_r($temp,true)."\n";     
    if ($temp!='') {
      $temp=unserialize($temp);
      if (is_array($temp) and isset($temp['options'])) {
            
        $options=[];
        foreach ($temp['options'] as $myoption) {
          //$return['debug'].="myoption\n".print_r($myoption,true)."\n"; 
          if (isset($myoption['value']) and isset($myoption['text'])) {
            $parts=explode(':',$myoption['text']);
            if (count($parts)>=2) {
              $text=$parts[0];
              $code=$parts[1];
            } else {
              $text=$parts[0];
              $code=$parts[0];
            }
            $value=intval($myoption['value']);
            $service_highlights_shared_attr[$value]=array('value' => $value,'text' => $text, 'code' => $code);
          }
        } 
      }
      
    }
  }
    
  //$return['debug'].="service_highlights_field\n".$service_highlights_field."\n";     
  //$return['debug'].="service_highlights_attr\n".print_r($service_highlights_attr,true)."\n";     
  
  $service_highlights_private_oxima=[];
  if ($service_highlights_private_field!='' and count($service_highlights_private_attr)>0) {
    //  cf10278 gks_customt_gks_transfer_oxima_type
    $sql="select transfer_oxima_type_id,".$service_highlights_private_field." as myvalue  from gks_customt_gks_transfer_oxima_type";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    
    while ($row = $result->fetch_assoc()) {
      $service_highlights_private_oxima[$row['transfer_oxima_type_id']]=$row['myvalue'];
    }
  }

  $service_highlights_shared_oxima=[];
  if ($service_highlights_shared_field!='' and count($service_highlights_shared_attr)>0) {
    //  cf10278 gks_customt_gks_transfer_oxima_type
    $sql="select transfer_oxima_type_id,".$service_highlights_shared_field." as myvalue  from gks_customt_gks_transfer_oxima_type";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    
    while ($row = $result->fetch_assoc()) {
      $service_highlights_shared_oxima[$row['transfer_oxima_type_id']]=$row['myvalue'];
    }
  }  
  
  //$return['debug'].="service_highlights_oxima\n".print_r($service_highlights_oxima,true)."\n";     

  $sql="SELECT gks_transfer_oxima_type.*,
  gks_transfer_oxima_type_en_US.transfer_oxima_type_descr_en_US,
  gks_transfer_oxima_type_en_US.transfer_oxima_type_site_text_en_US
  FROM gks_transfer_oxima_type
  LEFT JOIN (
    SELECT transfer_oxima_type_id, 
    transfer_oxima_type_descr as transfer_oxima_type_descr_en_US, 
    transfer_oxima_type_site_text as transfer_oxima_type_site_text_en_US 
    FROM gks_transfer_oxima_type_lang WHERE lang_code='en-US'
  ) AS gks_transfer_oxima_type_en_US ON gks_transfer_oxima_type.id_transfer_oxima_type = gks_transfer_oxima_type_en_US.transfer_oxima_type_id
  
  WHERE transfer_oxima_type_disable=0 
  ".($only_id_transfer_oxima_type==0 ? '' : ' and id_transfer_oxima_type='.$only_id_transfer_oxima_type)."
  order by sort_order";
  //echo '<pre>'.$sql;die();
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
    $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
  
  $oxima_type=[];
  while ($row = $result->fetch_assoc()) {
    $row['service_highlights_private']=[];
    if (isset($service_highlights_private_oxima[$row['id_transfer_oxima_type']]) and 
        isset($service_highlights_private_attr[$service_highlights_private_oxima[$row['id_transfer_oxima_type']]])) {
      $row['service_highlights_private']=$service_highlights_private_attr[$service_highlights_private_oxima[$row['id_transfer_oxima_type']]];
    }
    $row['service_highlights_shared']=[];
    if (isset($service_highlights_shared_oxima[$row['id_transfer_oxima_type']]) and 
        isset($service_highlights_shared_attr[$service_highlights_shared_oxima[$row['id_transfer_oxima_type']]])) {
      $row['service_highlights_shared']=$service_highlights_shared_attr[$service_highlights_shared_oxima[$row['id_transfer_oxima_type']]];
    }
    
    
    $oxima_type[$row['id_transfer_oxima_type']]=$row;
  }
  //echo '<pre>';print_r($oxima_type);die();
  
  //$return['debug'].="oxima_type\n".print_r($oxima_type,true)."\n";
  
  $mybooking_time=date('Y-m-d H:i:s');
  
  $diadromes=[];
  $diadromes[1]=array('poi_diadromes_apostasi_se_metra'=>0,'poi_diadromes_diarkeia_se_lepta'=>0, 'pick_up_time_diff_seconds'=>0, 'oximata' =>[]);
  $diadromes[2]=array('poi_diadromes_apostasi_se_metra'=>0,'poi_diadromes_diarkeia_se_lepta'=>0, 'pick_up_time_diff_seconds'=>0, 'oximata' =>[]);
  

  if ($val_to_id>111) {
    if ($val_direction=='tori') { //apo airport
      //kanoniko
      $sql1="SELECT id_poi_diadromes,poi_id_from,poi_id_to,poi_diadromes_apostasi_se_metra,poi_diadromes_diarkeia_se_lepta,
      if (poi_id_from=".$val_from_id." and poi_id_to=".$val_to_id.",1,2) as is_diadromi_real 
      FROM gks_poi_diadromes 
      WHERE poi_diadromes_disable=0 and poi_id_from=".$val_from_id." and (
        poi_id_to=".$val_to_id." or  
        poi_id_to in (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")
      )
      order by is_diadromi_real, id_poi_diadromes desc limit 1";
      //epistrofi
      $sql2="SELECT id_poi_diadromes,poi_id_from,poi_id_to,poi_diadromes_apostasi_se_metra,poi_diadromes_diarkeia_se_lepta,
      if (poi_id_from=".$val_to_id." and poi_id_to=".$val_from_id.",1,2) as is_diadromi_real  
      FROM gks_poi_diadromes 
      WHERE poi_diadromes_disable=0 and poi_id_to=".$val_from_id." and (
        poi_id_from=".$val_to_id." or 
        poi_id_from in (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")
      )
      order by is_diadromi_real,id_poi_diadromes desc limit 1";
      
      //kanoniko
      $sql3="SELECT gks_transfer_pricelist.*,
      if (poi_id_from=".$val_from_id." AND poi_id_to=".$val_to_id.",1,2) as is_real
      FROM gks_transfer_pricelist WHERE transfer_pricelist_disable=0
      ".($only_id_transfer_oxima_type==0 ? '' : ' and transfer_oxima_type_id='.$only_id_transfer_oxima_type)."
      and (
        (poi_id_from=".$val_from_id." AND (poi_id_to=  ".$val_to_id." or poi_id_to   in (select poi_parent_id from gks_poi where id_poi=".$val_to_id."))) or 
        (poi_id_to=  ".$val_from_id." AND (poi_id_from=".$val_to_id." or poi_id_from in (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")) and transfer_and_aller_retour=1) 
      )
      and (
        (transfer_pricelist_mydate_start is null and transfer_pricelist_mydate_end is null) or
        (transfer_pricelist_mydate_start <='".$val_date1_sql."' and transfer_pricelist_mydate_end is null) or
        (transfer_pricelist_mydate_start is null and transfer_pricelist_mydate_end >='".$val_date1_sql."') or
        (transfer_pricelist_mydate_start <='".$val_date1_sql."' and transfer_pricelist_mydate_end >='".$val_date1_sql."')
      )
      and (
        (transfer_pricelist_offerrundate_start is null and transfer_pricelist_offerrundate_end is null) or
        (transfer_pricelist_offerrundate_start <='".$mybooking_time."' and transfer_pricelist_offerrundate_end is null) or
        (transfer_pricelist_offerrundate_start is null and transfer_pricelist_offerrundate_end >='".$mybooking_time."') or
        (transfer_pricelist_offerrundate_start <='".$mybooking_time."' and transfer_pricelist_offerrundate_end >='".$mybooking_time."')
      )
      and gks_transfer_pricelist.id_transfer_pricelist in (
        SELECT transfer_pricelist_id
        FROM gks_transfer_pricelist2transfer
        WHERE transfer_id In (".$id_transfer.")
        union
        SELECT id_transfer_pricelist
        FROM gks_transfer_pricelist
        LEFT JOIN gks_transfer_pricelist2transfer ON gks_transfer_pricelist.id_transfer_pricelist = gks_transfer_pricelist2transfer.transfer_pricelist_id
        WHERE transfer_pricelist_id Is Null
      )
      order by is_real, id_transfer_pricelist desc";
      //epistrofi
      $sql4="SELECT gks_transfer_pricelist.*,
      if (poi_id_from=".$val_to_id." AND poi_id_to=".$val_from_id.",3,4) as is_real
      FROM gks_transfer_pricelist WHERE transfer_pricelist_disable=0
      ".($only_id_transfer_oxima_type==0 ? '' : ' and transfer_oxima_type_id='.$only_id_transfer_oxima_type)."
      and (
        (poi_id_to=  ".$val_from_id." AND (poi_id_from=".$val_to_id." or poi_id_from in (select poi_parent_id from gks_poi where id_poi=".$val_to_id."))) or
        (poi_id_from=".$val_from_id." AND (poi_id_to=  ".$val_to_id." or poi_id_to   in (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")) and transfer_and_aller_retour=1)
      )
      and (
        (transfer_pricelist_mydate_start is null and transfer_pricelist_mydate_end is null) or
        (transfer_pricelist_mydate_start <='".$val_date2_sql."' and transfer_pricelist_mydate_end is null) or
        (transfer_pricelist_mydate_start is null and transfer_pricelist_mydate_end >='".$val_date2_sql."') or
        (transfer_pricelist_mydate_start <='".$val_date2_sql."' and transfer_pricelist_mydate_end >='".$val_date2_sql."')
      )
      and (
        (transfer_pricelist_offerrundate_start is null and transfer_pricelist_offerrundate_end is null) or
        (transfer_pricelist_offerrundate_start <='".$mybooking_time."' and transfer_pricelist_offerrundate_end is null) or
        (transfer_pricelist_offerrundate_start is null and transfer_pricelist_offerrundate_end >='".$mybooking_time."') or
        (transfer_pricelist_offerrundate_start <='".$mybooking_time."' and transfer_pricelist_offerrundate_end >='".$mybooking_time."')
      )
      and gks_transfer_pricelist.id_transfer_pricelist in (
        SELECT transfer_pricelist_id
        FROM gks_transfer_pricelist2transfer
        WHERE transfer_id In (".$id_transfer.")
        union
        SELECT id_transfer_pricelist
        FROM gks_transfer_pricelist
        LEFT JOIN gks_transfer_pricelist2transfer ON gks_transfer_pricelist.id_transfer_pricelist = gks_transfer_pricelist2transfer.transfer_pricelist_id
        WHERE transfer_pricelist_id Is Null
      )
      order by is_real, id_transfer_pricelist desc";
  
    } else { //pros airport
      //kanoniko
      $sql1="SELECT id_poi_diadromes,poi_id_from,poi_id_to,poi_diadromes_apostasi_se_metra,poi_diadromes_diarkeia_se_lepta, 
      if (poi_id_to=".$val_from_id." and poi_id_from=".$val_to_id.",1,2) as is_diadromi_real
      FROM gks_poi_diadromes 
      WHERE poi_diadromes_disable=0 and poi_id_to=".$val_from_id." and (
        poi_id_from=".$val_to_id." or
        poi_id_from in (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")
      )
      order by is_diadromi_real, id_poi_diadromes desc limit 1";
      //epistrofi
      $sql2="SELECT id_poi_diadromes,poi_id_from,poi_id_to,poi_diadromes_apostasi_se_metra,poi_diadromes_diarkeia_se_lepta,
      if (poi_id_to=".$val_to_id." and poi_id_from=".$val_from_id.",1,2) as is_diadromi_real
      FROM gks_poi_diadromes 
      WHERE poi_diadromes_disable=0 and poi_id_from=".$val_from_id." AND (
        poi_id_to=".$val_to_id." or
        poi_id_to in (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")
      )
      order by is_diadromi_real, id_poi_diadromes desc limit 1";
  
      //kanoniko
      $sql3="SELECT gks_transfer_pricelist.*,
      if (poi_id_from=".$val_to_id." AND poi_id_to=".$val_from_id.",5,6) as is_real
      FROM gks_transfer_pricelist WHERE transfer_pricelist_disable=0
      ".($only_id_transfer_oxima_type==0 ? '' : ' and transfer_oxima_type_id='.$only_id_transfer_oxima_type)."
      and (
        (poi_id_to=".$val_from_id."   and (poi_id_from=".$val_to_id." or poi_id_from in (select poi_parent_id from gks_poi where id_poi=".$val_to_id."))) or 
        (poi_id_from=".$val_from_id." and (poi_id_to=  ".$val_to_id." or poi_id_to  in  (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")) and transfer_and_aller_retour=1)
      )
      and (
        (transfer_pricelist_mydate_start is null and transfer_pricelist_mydate_end is null) or
        (transfer_pricelist_mydate_start <='".$val_date1_sql."' and transfer_pricelist_mydate_end is null) or
        (transfer_pricelist_mydate_start is null and transfer_pricelist_mydate_end >='".$val_date1_sql."') or
        (transfer_pricelist_mydate_start <='".$val_date1_sql."' and transfer_pricelist_mydate_end >='".$val_date1_sql."')
      )
      and (
        (transfer_pricelist_offerrundate_start is null and transfer_pricelist_offerrundate_end is null) or
        (transfer_pricelist_offerrundate_start <='".$mybooking_time."' and transfer_pricelist_offerrundate_end is null) or
        (transfer_pricelist_offerrundate_start is null and transfer_pricelist_offerrundate_end >='".$mybooking_time."') or
        (transfer_pricelist_offerrundate_start <='".$mybooking_time."' and transfer_pricelist_offerrundate_end >='".$mybooking_time."')
      )
      and gks_transfer_pricelist.id_transfer_pricelist in (
        SELECT transfer_pricelist_id
        FROM gks_transfer_pricelist2transfer
        WHERE transfer_id In (".$id_transfer.")
        union
        SELECT id_transfer_pricelist
        FROM gks_transfer_pricelist
        LEFT JOIN gks_transfer_pricelist2transfer ON gks_transfer_pricelist.id_transfer_pricelist = gks_transfer_pricelist2transfer.transfer_pricelist_id
        WHERE transfer_pricelist_id Is Null
      )
      order by is_real, id_transfer_pricelist desc";
      //epistrofi
      $sql4="SELECT gks_transfer_pricelist.*,
      if (poi_id_from=".$val_from_id." AND poi_id_to=".$val_to_id.",7,8) as is_real
      FROM gks_transfer_pricelist WHERE transfer_pricelist_disable=0 
      ".($only_id_transfer_oxima_type==0 ? '' : ' and transfer_oxima_type_id='.$only_id_transfer_oxima_type)."
      and (
        (poi_id_from=".$val_from_id." and (poi_id_to=  ".$val_to_id." or poi_id_to in   (select poi_parent_id from gks_poi where id_poi=".$val_to_id."))) or
        (poi_id_to=  ".$val_from_id." and (poi_id_from=".$val_to_id." or poi_id_from in (select poi_parent_id from gks_poi where id_poi=".$val_to_id.")) and transfer_and_aller_retour=1)
      )
      and (
        (transfer_pricelist_mydate_start is null and transfer_pricelist_mydate_end is null) or
        (transfer_pricelist_mydate_start <='".$val_date2_sql."' and transfer_pricelist_mydate_end is null) or
        (transfer_pricelist_mydate_start is null and transfer_pricelist_mydate_end >='".$val_date2_sql."') or
        (transfer_pricelist_mydate_start <='".$val_date2_sql."' and transfer_pricelist_mydate_end >='".$val_date2_sql."')
      )
      and (
        (transfer_pricelist_offerrundate_start is null and transfer_pricelist_offerrundate_end is null) or
        (transfer_pricelist_offerrundate_start <='".$mybooking_time."' and transfer_pricelist_offerrundate_end is null) or
        (transfer_pricelist_offerrundate_start is null and transfer_pricelist_offerrundate_end >='".$mybooking_time."') or
        (transfer_pricelist_offerrundate_start <='".$mybooking_time."' and transfer_pricelist_offerrundate_end >='".$mybooking_time."')
      )
      and gks_transfer_pricelist.id_transfer_pricelist in (
        SELECT transfer_pricelist_id
        FROM gks_transfer_pricelist2transfer
        WHERE transfer_id In (".$id_transfer.")
        union
        SELECT id_transfer_pricelist
        FROM gks_transfer_pricelist
        LEFT JOIN gks_transfer_pricelist2transfer ON gks_transfer_pricelist.id_transfer_pricelist = gks_transfer_pricelist2transfer.transfer_pricelist_id
        WHERE transfer_pricelist_id Is Null
      )
      order by is_real, id_transfer_pricelist desc";
  
    }
    //echo '<pre>sql 1-4'."\n\n".$sql1.";\n\n".$sql2.";\n\n".$sql3.";\n\n".$sql4.';';die();
    $result = $db_link->query($sql1);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql1."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    if ($result->num_rows>=1) {
      $row=$result->fetch_assoc();
      $row['pick_up_time_diff_seconds']=0;
      
      $row['oximata']=[];
      
      if ($user_transfer_online_search_type>=1) {
        if ($distance>0) $row['poi_diadromes_apostasi_se_metra']=$distance;
        if ($duration>0) $row['poi_diadromes_diarkeia_se_lepta']=$duration;
      } 

      //echo 'ddddd';die();
      $diadromes[1]=$row;
    }
    //echo '<pre>diadromes ';print_r($diadromes);die();
    
    $result = $db_link->query($sql3);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql3."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    while ($row = $result->fetch_assoc()) {
      if (isset($diadromes[1]['oximata'][$row['transfer_oxima_type_id']])==false) {
        $diadromes[1]['oximata'][$row['transfer_oxima_type_id']]=array(
          'transfer_oxima_type_id' => $row['transfer_oxima_type_id'],
          'pricelist_id_selected_group_one'=>-1,
          'pricelist_id_selected_group_multi'=>-1,
          'pricelists' => array(),
        );
        
      }
      $diadromes[1]['oximata'][$row['transfer_oxima_type_id']]['pricelists'][$row['id_transfer_pricelist']]=$row;
      


    }
  }
  
  //echo '<pre>diadromes ';print_r($diadromes);die();
  //echo 'sssssssssss';print_r($diadromes[1]); die();
  //if ($val_to_id==2) {
  //n vro oximata pou einai anaxiliometro alla den einai idi stin lista 'oximata'
  
  
    
  if (isset($diadromes[1]['id_poi_diadromes'])==false)  { //den vrethike diadromi, vazo tin custom 2
    $row=[];
    $row['id_poi_diadromes']=2;
    $row['poi_id_from']=0;
    $row['poi_id_to']=0;
    if ($val_direction=='tori') { //apo airport
      //kanoniko
      $row['poi_id_from']=$val_from_id;
      $row['poi_id_to']=$val_to_id;
      
    } else { //pros airport
      //kanoniko
      $row['poi_id_from']=$val_to_id;
      $row['poi_id_to']=$val_from_id;
      
    }
    
    
    $row['poi_diadromes_apostasi_se_metra']=$distance;
    $row['poi_diadromes_diarkeia_se_lepta']=$duration;    
    $row['is_diadromi_real']=1;
    $row['pick_up_time_diff_seconds']=0;
    //$row['oximata']=[];
    $row['oximata']=$diadromes[1]['oximata']; //vazo oti vrike apo prin, an vrike kati
    $diadromes[1]=$row;
  }
  //echo '<pre>diadromes ';print_r($diadromes);die();
  //echo 'sssssssssss';print_r($diadromes[1]); die();
  $oximata_exist=[];
  foreach ($diadromes[1]['oximata'] as $oximakey => $oximavalue) {
    $oximata_exist[]=$oximakey;
  }
  //echo 'sssssssssss oximata_exist ';print_r($oximata_exist); die();

  
  //ta ipoloipa oximata, pou den exoyn timokatalogo, na ipologiso tin timi ana km
  $sql="SELECT id_transfer_oxima_type,
  transfer_oxima_type_price_min_per_person,
  transfer_oxima_type_price_min_per_transfer,
  transfer_oxima_type_def_price_per_km,
  formula_per_km_ot
  FROM gks_transfer_oxima_type
  WHERE transfer_oxima_type_disable=0 
  AND transfer_oxima_type_roure_group_one=1";

  if (count($oximata_exist)>0) {
    $sql.=" and id_transfer_oxima_type not in (".implode(',',$oximata_exist).")";
  }
  $sql.=" ORDER BY gks_transfer_oxima_type.sort_order";
  //echo '<pre>gks_transfer_oxima_type '.$sql; die();
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,"gks_transfer_search_poi sql error", $sql1."\n".$db_link->errno . '-'.$db_link->error);
    $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
  $price_oximata_types=array();
  while ($row = $result->fetch_assoc()) {
    $row['per_km']=array();
    $row['transfer_oxima_type_price_min_per_person']=floatval($row['transfer_oxima_type_price_min_per_person']);
    $row['transfer_oxima_type_price_min_per_transfer']=floatval($row['transfer_oxima_type_price_min_per_transfer']);
    $row['transfer_oxima_type_def_price_per_km']=floatval($row['transfer_oxima_type_def_price_per_km']);
    $row['formula_per_km_ot']=trim_gks($row['formula_per_km_ot']);
    
    $price_oximata_types[$row['id_transfer_oxima_type']]=$row;
  }

  //echo '<pre>';print_r($price_oximata_types);die();
  
  $from_to_poi_id_in=[];
  if ($diadromes[1]['poi_id_from']>111) $from_to_poi_id_in[]=$diadromes[1]['poi_id_from'];
  if ($diadromes[1]['poi_id_to']  >111) $from_to_poi_id_in[]=$diadromes[1]['poi_id_to'];
  
  //echo '<pre>from_to_poi_id_in ';print_r($from_to_poi_id_in);die();
    
  if (count($from_to_poi_id_in)>0) {
  
    $sql="select transfer_oxima_type_id,
    from_to_poi_id,
    formula_per_km_ft
    from gks_transfer_oxima_type_per_km
    where from_to_poi_id in (".implode(',',$from_to_poi_id_in).")";
    
    if (count($oximata_exist)>0) {
      $sql.=" and transfer_oxima_type_id not in (".implode(',',$oximata_exist).")";
    }
    $sql.=" ORDER BY aa";
    //print '<pre>'.$sql;die();
    
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql1."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    while ($row = $result->fetch_assoc()) {
      if (isset($price_oximata_types[$row['transfer_oxima_type_id']])) {
        $price_oximata_types[$row['transfer_oxima_type_id']]['per_km'][]=$row;
      }
    }
  }
  
  //echo '<pre>price_oximata_types ';print_r($price_oximata_types);die();
  
  
  //print '<pre>';print_r($price_oximata_types);die();
  //print '<pre>'.$distance.'|'.intval(round($distance/1000)).'|'.$duration."\n".
  //$diadromes[1]['poi_diadromes_apostasi_se_metra'].'|'.$diadromes[1]['poi_diadromes_diarkeia_se_lepta'];
  //die();
  
  foreach ($price_oximata_types as $row) {
  
    $found_price=false;$found_price_ammount=0; 
    foreach ($row['per_km'] as $value) {
      $formula_per_km_ft=trim_gks($value['formula_per_km_ft']);
      if ($formula_per_km_ft!='') {

        $formula=$formula_per_km_ft;
        $formula=str_replace('[km]', intval(round($distance/1000,0)), $formula);
        $formula=str_replace('[minutes]', $duration, $formula);
        
        $eval_result=false;
        $eval_formula='$eval_calc='.$formula.'; $eval_result=true;';
        //echo 'gggggggg '.$eval_formula;die();
        $errors='';
        
        //ob_start();
        //ob_get_clean();
      
        try {
          $res=eval($eval_formula);
          $out_price = $eval_calc;
        } catch (ParseError  $e) {
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (1):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(ArithmeticError $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (2):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(DivisionByZeroError $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (3):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(TypeError $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (4):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(AssertionError $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (5):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(Exception  $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (6):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch (Throwable $e) {  
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (7):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();      
        }
  
  //      $my_buffer_contents=ob_get_clean();
  //      if ($my_buffer_contents!='') {
  //        $errors.=gks_lang('Σφάλμα').':<br><br>'.$my_buffer_contents;
  //      }
            
        if ($eval_result==false or $errors!='') {
          //debug_mail(false,'transfer-oxima-type formula error ',$formula_per_km_ft.'<br>'.htmlspecialchars($errors));
          //$return = array('success' => false, 'message' => base64_encode($errors));
          //echo json_encode($return); die();        
        } else {
          if ($out_price>0) {
            $found_price_ammount=$out_price;
            $found_price_ammount=transfer_price_round_type_func(
              $found_price_ammount,$return['transfer_properties']['transfer_price_round_type']
            );
            $found_price=true;
          }
        }
        
      }
    } 
    
    if ($found_price==false) {
      $formula_per_km_ot=trim_gks($row['formula_per_km_ot']);
      if ($formula_per_km_ot!='') {
        $formula=$formula_per_km_ot;
        $formula=str_replace('[km]', intval(round($distance/1000,0)), $formula);
        $formula=str_replace('[minutes]', $duration, $formula);
        
        $eval_result=false;
        $eval_formula='$eval_calc='.$formula.'; $eval_result=true;';
        //echo 'gggggggg '.$eval_formula;die();
        $errors='';
        
        //ob_start();
        //ob_get_clean();
      
        try {
          $res=eval($eval_formula);
          $out_price = $eval_calc;
        } catch (ParseError  $e) {
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (1):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(ArithmeticError $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (2):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(DivisionByZeroError $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (3):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(TypeError $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (4):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(AssertionError $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (5):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch(Exception  $e){
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (6):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
        } catch (Throwable $e) {  
          $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (7):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();      
        }
  
  //      $my_buffer_contents=ob_get_clean();
  //      if ($my_buffer_contents!='') {
  //        $errors.=gks_lang('Σφάλμα').':<br><br>'.$my_buffer_contents;
  //      }
            
        if ($eval_result==false or $errors!='') {
          //debug_mail(false,'transfer-oxima-type formula error ',$formula_per_km_ft.'<br>'.htmlspecialchars($errors));
          //$return = array('success' => false, 'message' => base64_encode($errors));
          //echo json_encode($return); die();        
        } else {
          if ($out_price>0) {
            $found_price_ammount=$out_price;
            $found_price_ammount=transfer_price_round_type_func(
              $found_price_ammount,$return['transfer_properties']['transfer_price_round_type']
            );
            //echo '<pre>['.$out_price.']['.$found_price_ammount.']</pre>';
            $found_price=true;
          }
        }              
      
      }
    }
    if ($found_price==false and $row['transfer_oxima_type_def_price_per_km']>0) {
      
      $found_price_ammount=$row['transfer_oxima_type_def_price_per_km']*intval(round($distance/1000,0));
      $found_price_ammount=transfer_price_round_type_func(
        $found_price_ammount,$return['transfer_properties']['transfer_price_round_type']
      );      
      $found_price=true;  
    }
    
    //
    
    if ($found_price and $found_price_ammount>0) {
      if ($found_price_ammount < $row['transfer_oxima_type_price_min_per_transfer']) $found_price_ammount = $row['transfer_oxima_type_price_min_per_transfer'];
      
      $diadromes[1]['oximata'][$row['id_transfer_oxima_type']]=array(
        'transfer_oxima_type_id' => $row['id_transfer_oxima_type'],
        'pricelist_id_selected_group_one'=>2,
        'pricelist_id_selected_group_multi'=>-1,
        'pricelists' => array(),
      );
      
      $diadromes[1]['oximata'][$row['id_transfer_oxima_type']]['pricelists'][2]=array(
        'id_transfer_pricelist'=> 2,
        //'mydate_add': '2024-02-10 16:41:46',
        //'mydate_edit': '2024-02-10 16:41:46',
        //'user_id_add': '1',
        //'user_id_edit': '1',
        //'myip': '192.168.1.23',
        //'odbc': '2024-02-10 16:41:46',
        'poi_id_from'=> $diadromes[1]['poi_id_from'],
        'poi_id_to'=> $diadromes[1]['poi_id_to'],
        'transfer_oxima_type_id'=> $row['id_transfer_oxima_type'],
        'transfer_pricelist_offerrundate_start'=> '',
        'transfer_pricelist_offerrundate_end'=> '',
        'transfer_pricelist_mydate_start'=> '',
        'transfer_pricelist_mydate_end'=> '',
        //'transfer_pricelist_disable'=> 0,
        //'transfer_pricelist_comments'=> null,
        'transfer_pricelist_price_per_transfer'=> $found_price_ammount,      //deleteme
        'transfer_pricelist_price_per_transfer_offer'=> 0,
        'transfer_pricelist_price_per_person'=> 0,
        'transfer_pricelist_price_per_person_offer'=> 0,
        'transfer_and_aller_retour'=> 1,
        'transfer_pricelist_apostasi_se_metra'=> $distance,
        'transfer_pricelist_diarkeia_se_lepta'=> $duration,
        'poi_diadromes_id'=> 2,
        
        'is_real'=> 1,
        'diafora_time'=> false,      
      
      
      );        
    }
    
  }
  
    
  //echo '<pre>sssssssssss';print_r($diadromes); die();


  
  if ($val_date2_time>0) {
    
    
    if ($val_to_id>111) {
      //echo '<pre>';print $sql2;die();
      $result = $db_link->query($sql2);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql2."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      if ($result->num_rows>=1) {
          $row=$result->fetch_assoc();
          $row['pick_up_time_diff_seconds']=0;
          $row['oximata']=[];
          if ($user_transfer_online_search_type>=1) {
            if ($distance>0) $row['poi_diadromes_apostasi_se_metra']=$distance;
            if ($duration>0) $row['poi_diadromes_diarkeia_se_lepta']=$duration;
          }
          
          $diadromes[2]=$row;
          
          //den vrethike i kanoniki diadromi, ara na valo ta noumera apo to epistrtofi
          if ($diadromes[1]['poi_diadromes_apostasi_se_metra']==0) {
            $diadromes[1]['poi_diadromes_apostasi_se_metra']=$diadromes[2]['poi_diadromes_apostasi_se_metra'];
            $diadromes[1]['poi_diadromes_diarkeia_se_lepta']=$diadromes[2]['poi_diadromes_diarkeia_se_lepta'];
          }
          
      } else {
        //den vrethike i kanoniki epistrtofi, ara na vala ta noumera apo to pigene
        $diadromes[2]['poi_diadromes_apostasi_se_metra']=$diadromes[1]['poi_diadromes_apostasi_se_metra'];
        $diadromes[2]['poi_diadromes_diarkeia_se_lepta']=$diadromes[1]['poi_diadromes_diarkeia_se_lepta'];
      }
      
      
      //echo '<pre>'.print_r($diadromes[2],true);die();
      //echo '<pre>';print $sql4;die();
      $result = $db_link->query($sql4);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql4."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      while ($row = $result->fetch_assoc()) {
        if (isset($diadromes[2]['oximata'][$row['transfer_oxima_type_id']])==false) {
          $diadromes[2]['oximata'][$row['transfer_oxima_type_id']]=array(
            'transfer_oxima_type_id' => $row['transfer_oxima_type_id'],
            'pricelist_id_selected_group_one'=>-1,
            'pricelist_id_selected_group_multi'=>-1,
            'pricelists' => array(),
          );
        }
        $diadromes[2]['oximata'][$row['transfer_oxima_type_id']]['pricelists'][$row['id_transfer_pricelist']]=$row;
      }
    }
    
    //print '<pre>';print_r($diadromes[2]);die();
    
    if (isset($diadromes[2]['id_poi_diadromes'])==false)  { //den vrethike diadromi, vazo tin custom 2
    

      $row=[];
      $row['id_poi_diadromes']=2;
      $row['poi_id_from']=0;
      $row['poi_id_to']=0;
      if ($val_direction=='tori') { //apo airport
        //kanoniko
        $row['poi_id_from']=$val_to_id;
        $row['poi_id_to']=$val_from_id;
        
      } else { //pros airport
        //kanoniko
        $row['poi_id_from']=$val_from_id;
        $row['poi_id_to']=$val_to_id;
        
      }
      
      
      $row['poi_diadromes_apostasi_se_metra']=$distance;
      $row['poi_diadromes_diarkeia_se_lepta']=$duration;    
      $row['is_diadromi_real']=1;
      $row['pick_up_time_diff_seconds']=0;
      $row['oximata']=$diadromes[2]['oximata']; //vazo oti vrike apo prin, an vrike kati
      $diadromes[2]=$row;
    }
    
    //echo 'sssssssssss';print_r($diadromes[2]); die();
    $oximata_exist=[];
    foreach ($diadromes[2]['oximata'] as $oximakey => $oximavalue) {
      $oximata_exist[]=$oximakey;
    }    
       
    $sql="SELECT id_transfer_oxima_type,
    transfer_oxima_type_price_min_per_person,
    transfer_oxima_type_price_min_per_transfer,
    transfer_oxima_type_def_price_per_km,
    formula_per_km_ot
    FROM gks_transfer_oxima_type
    WHERE transfer_oxima_type_disable=0 
    AND transfer_oxima_type_roure_group_one=1 ";
    if (count($oximata_exist)>0) {
      $sql.=" and id_transfer_oxima_type not in (".implode(',',$oximata_exist).")";
    }
    $sql.=" ORDER BY gks_transfer_oxima_type.sort_order";
  //echo '<pre>sssssssssss'.$sql; die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql1."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    $price_oximata_types=array();
    while ($row = $result->fetch_assoc()) {
      $row['per_km']=array();
      $row['transfer_oxima_type_price_min_per_transfer']=floatval($row['transfer_oxima_type_price_min_per_transfer']);
      $row['transfer_oxima_type_def_price_per_km']=floatval($row['transfer_oxima_type_def_price_per_km']);
      
      $price_oximata_types[$row['id_transfer_oxima_type']]=$row;
    }    

    $from_to_poi_id_in=[];
    if ($diadromes[2]['poi_id_from']>111) $from_to_poi_id_in[]=$diadromes[2]['poi_id_from'];
    if ($diadromes[2]['poi_id_to']  >111) $from_to_poi_id_in[]=$diadromes[2]['poi_id_to'];
    
      
    if (count($from_to_poi_id_in)>0) {
      $sql="select transfer_oxima_type_id,
      from_to_poi_id,
      formula_per_km_ft
      from gks_transfer_oxima_type_per_km
      where from_to_poi_id in (".implode(',',$from_to_poi_id_in).")";
      if (count($oximata_exist)>0) {
        $sql.=" and transfer_oxima_type_id not in (".implode(',',$oximata_exist).")";
      }
      $sql.=" ORDER BY aa";
      //print '<pre>'.$sql;die();
      
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,"gks_transfer_search_poi sql error", $sql1."\n".$db_link->errno . '-'.$db_link->error);
        $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
      while ($row = $result->fetch_assoc()) {
        if (isset($price_oximata_types[$row['transfer_oxima_type_id']])) {
          $price_oximata_types[$row['transfer_oxima_type_id']]['per_km'][]=$row;
        }
      }
    }
        
    
    foreach ($price_oximata_types as $row) {
    
      $found_price=false;$found_price_ammount=0; 
      foreach ($row['per_km'] as $value) {
        $formula_per_km_ft=trim_gks($value['formula_per_km_ft']);
        if ($formula_per_km_ft!='') {
  
          $formula=$formula_per_km_ft;
          $formula=str_replace('[km]', intval(round($distance/1000,0)), $formula);
          $formula=str_replace('[minutes]', $duration, $formula);
          
          $eval_result=false;
          $eval_formula='$eval_calc='.$formula.'; $eval_result=true;';
          //echo 'gggggggg '.$eval_formula;die();
          $errors='';
          
          //ob_start();
          //ob_get_clean();
        
          try {
            $res=eval($eval_formula);
            $out_price = $eval_calc;
          } catch (ParseError  $e) {
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (1):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(ArithmeticError $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (2):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(DivisionByZeroError $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (3):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(TypeError $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (4):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(AssertionError $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (5):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(Exception  $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (6):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch (Throwable $e) {  
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (7):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();      
          }
    
    //      $my_buffer_contents=ob_get_clean();
    //      if ($my_buffer_contents!='') {
    //        $errors.=gks_lang('Σφάλμα').':<br><br>'.$my_buffer_contents;
    //      }
              
          if ($eval_result==false or $errors!='') {
            //debug_mail(false,'transfer-oxima-type formula error ',$formula_per_km_ft.'<br>'.htmlspecialchars($errors));
            //$return = array('success' => false, 'message' => base64_encode($errors));
            //echo json_encode($return); die();        
          } else {
            if ($out_price>0) {
              $found_price_ammount=$out_price;
              $found_price_ammount=transfer_price_round_type_func(
                $found_price_ammount,$return['transfer_properties']['transfer_price_round_type']
              );              
              $found_price=true;
            }
          }
          
        }
      } 
      
      if ($found_price==false) {
        $formula_per_km_ot=trim_gks($row['formula_per_km_ot']);
        if ($formula_per_km_ot!='') {
          $formula=$formula_per_km_ot;
          $formula=str_replace('[km]', intval(round($distance/1000,0)), $formula);
          $formula=str_replace('[minutes]', $duration, $formula);
          
          $eval_result=false;
          $eval_formula='$eval_calc='.$formula.'; $eval_result=true;';
          //echo 'gggggggg '.$eval_formula;die();
          $errors='';
          
          //ob_start();
          //ob_get_clean();
        
          try {
            $res=eval($eval_formula);
            $out_price = $eval_calc;
          } catch (ParseError  $e) {
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (1):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(ArithmeticError $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (2):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(DivisionByZeroError $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (3):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(TypeError $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (4):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(AssertionError $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (5):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch(Exception  $e){
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (6):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();
          } catch (Throwable $e) {  
            $errors=gks_lang('Σφάλμα στον τύπο υπολογισμού Τιμής / Km').' (7):<br><b>'.$formula_per_km_ft.'</b><br>'.gks_lang('για το σημείο').' '.$poi_descr.'<br>'.$e->getMessage();      
          }
    
    //      $my_buffer_contents=ob_get_clean();
    //      if ($my_buffer_contents!='') {
    //        $errors.=gks_lang('Σφάλμα').':<br><br>'.$my_buffer_contents;
    //      }
              
          if ($eval_result==false or $errors!='') {
            //debug_mail(false,'transfer-oxima-type formula error ',$formula_per_km_ft.'<br>'.htmlspecialchars($errors));
            //$return = array('success' => false, 'message' => base64_encode($errors));
            //echo json_encode($return); die();        
          } else {
            if ($out_price>0) {
              $found_price_ammount=$out_price;
              $found_price_ammount=transfer_price_round_type_func(
                $found_price_ammount,$return['transfer_properties']['transfer_price_round_type']
              );              
              $found_price=true;
            }
          }              
        
        }
      }
      if ($found_price==false and $row['transfer_oxima_type_def_price_per_km']>0) {
        
        $found_price_ammount=$row['transfer_oxima_type_def_price_per_km']*intval(round($distance/1000,0));
        $found_price=true;  
      }
      
      
      
      if ($found_price and $found_price_ammount>0) {
        if ($found_price_ammount < $row['transfer_oxima_type_price_min_per_transfer']) $found_price_ammount = $row['transfer_oxima_type_price_min_per_transfer'];

  
        $diadromes[2]['oximata'][$row['id_transfer_oxima_type']]=array(
          'transfer_oxima_type_id' => $row['id_transfer_oxima_type'],
          'pricelist_id_selected_group_one'=>2,
          'pricelist_id_selected_group_multi'=>-1,
          'pricelists' => array(),
        );
      
        $diadromes[2]['oximata'][$row['id_transfer_oxima_type']]['pricelists'][2]=array(
          'id_transfer_pricelist'=> 2,
          //'mydate_add': '2024-02-10 16:41:46',
          //'mydate_edit': '2024-02-10 16:41:46',
          //'user_id_add': '1',
          //'user_id_edit': '1',
          //'myip': '192.168.1.23',
          //'odbc': '2024-02-10 16:41:46',
          'poi_id_from'=> $diadromes[1]['poi_id_from'],
          'poi_id_to'=> $diadromes[1]['poi_id_to'],
          'transfer_oxima_type_id'=> $row['id_transfer_oxima_type'],
          'transfer_pricelist_offerrundate_start'=> '',
          'transfer_pricelist_offerrundate_end'=> '',
          'transfer_pricelist_mydate_start'=> '',
          'transfer_pricelist_mydate_end'=> '',
          //'transfer_pricelist_disable'=> 0,
          //'transfer_pricelist_comments'=> null,
          'transfer_pricelist_price_per_transfer'=> $found_price_ammount,       //deleteme
          'transfer_pricelist_price_per_transfer_offer'=> 0, 
          'transfer_pricelist_price_per_person'=> 0,
          'transfer_pricelist_price_per_person_offer'=> 0,
          'transfer_and_aller_retour'=> 1,
          'transfer_pricelist_apostasi_se_metra'=> $distance,
          'transfer_pricelist_diarkeia_se_lepta'=> $duration,
          'poi_diadromes_id'=> 2,
          
          'is_real'=> 1,
          'diafora_time'=> false,      
        
        
        );         
      }
    }    
  
    //echo '<pre>sssssssssss';print_r($diadromes[2]); die();
  
    
  }
  
  //echo '<pre>diadromes ';print_r($diadromes); die();
  
  if ($distance>0 and $diadromes[1]['poi_diadromes_apostasi_se_metra']==0) $diadromes[1]['poi_diadromes_apostasi_se_metra']=$distance;
  if ($duration>0 and $diadromes[1]['poi_diadromes_diarkeia_se_lepta']==0) $diadromes[1]['poi_diadromes_diarkeia_se_lepta']=$duration;
  
  if ($distance>0 and $diadromes[2]['poi_diadromes_apostasi_se_metra']==0) $diadromes[2]['poi_diadromes_apostasi_se_metra']=$distance;
  if ($duration>0 and $diadromes[2]['poi_diadromes_diarkeia_se_lepta']==0) $diadromes[2]['poi_diadromes_diarkeia_se_lepta']=$duration;
  
  //calc_diafora_time
  for ($diadromi = 1; $diadromi <= 2; $diadromi++) {
    if (isset($diadromes[$diadromi]['oximata'])) {
      foreach ($diadromes[$diadromi]['oximata'] as &$myoxima) {
        foreach ($myoxima['pricelists'] as &$mypl) {
          $diafora_time_start=false;
          if (trim_gks($mypl['transfer_pricelist_mydate_start'])!='') {
            if (strtotime($mypl['transfer_pricelist_mydate_start']) <= $val_date1_time) {
              $diafora_time_start=$val_date1_time-strtotime($mypl['transfer_pricelist_mydate_start']);
            }
          }
          $diafora_time_end=false;
          if (trim_gks($mypl['transfer_pricelist_mydate_end'])!='') {
            if (strtotime($mypl['transfer_pricelist_mydate_end']) >= $val_date1_time) {
              $diafora_time_end=strtotime($mypl['transfer_pricelist_mydate_end'])-$val_date1_time;
            }
          }
          $diafora_time=false;
          if ($diafora_time_start!==false) $diafora_time=$diafora_time_start;
          if ($diafora_time_end!==false) {
            if ($diafora_time===false) $diafora_time=$diafora_time_end;
            else if ($diafora_time_end < $diafora_time_start) $diafora_time=$diafora_time_end;
          }
          $mypl['diafora_time']=$diafora_time;
        } 
        unset($mypl);
      }
      unset($myoxima);
    }
  }
  
  //echo '<pre>diadromes ';print_r($diadromes); die();
  
  //cacl pricelist_id_selected_group_one
  for ($diadromi = 1; $diadromi <= 2; $diadromi++) {
    if (isset($diadromes[$diadromi]['oximata'])) {
      foreach ($diadromes[$diadromi]['oximata'] as &$myoxima) {
        $min_diafora_time=false; $pricelist_id_selected_group_one=-1;
        if (isset($oxima_type[$myoxima['transfer_oxima_type_id']]) and $oxima_type[$myoxima['transfer_oxima_type_id']]['transfer_oxima_type_roure_group_one']==1) {
          //ean exei xrono, to pio kontino xronika
          foreach ($myoxima['pricelists'] as $mykey => $mypl) {
            if ($mypl['transfer_pricelist_price_per_transfer']>0 and $mypl['diafora_time']!==false) {
              if ($min_diafora_time===false) {
                $min_diafora_time=$mypl['diafora_time'];
                $pricelist_id_selected_group_one=$mykey;
              } else if ($mypl['diafora_time'] < $min_diafora_time) {
                $min_diafora_time=$mypl['diafora_time'];
                $pricelist_id_selected_group_one=$mykey;
              }
            }
          } 
          if ($pricelist_id_selected_group_one>0) $myoxima['pricelist_id_selected_group_one']=$pricelist_id_selected_group_one;
          else {
            //efoson den brike pio pano, to proto xoris xrono
            foreach ($myoxima['pricelists'] as $mykey => $mypl) {
              if ($mypl['transfer_pricelist_price_per_transfer']>0 and $mypl['diafora_time']===false) {
                $myoxima['pricelist_id_selected_group_one']=$mykey;
                break;
              }
            }            
          }
        }
      }
      unset($myoxima);
    }
  }
  
  //echo '<pre>diadromes ';print_r($diadromes); die();
  
  
  ///calc pricelist_id_selected_group_multi
  
  $check_group_multi=true;
  if ($from=='website') {
    $limit_time_group_multi = time() + $return['transfer_properties']['transfer_reservation_min_hours_to_book_group_multi']*60*60;
    //$return['message']=base64_encode($limit_time_group_multi.' | '.showDate($limit_time_group_multi,'d/m/y H:i',1).' | '.$val_date1_time.' | '.showDate($val_date1_time,'d/m/y H:i',1));return $return;
    //echo '<pre>limit_time_group_multi'.$limit_time_group_multi ;die();
    if ($val_date1_time < $limit_time_group_multi) {
      $check_group_multi=false;
      //die('fffff');
    } else {
      //$return['message']=base64_encode('<pre>'.print_r($return['transfer_properties']['transfer_reservation_group_multi_date_range']['data'],true));return $return;
      if (count($return['transfer_properties']['transfer_reservation_group_multi_date_range']['data'])>0) {
        $has_check1_item=false;
        foreach ($return['transfer_properties']['transfer_reservation_group_multi_date_range']['data'] as $gmdri) {
          if ($val_date1_time >= _time_user(strtotime($gmdri['from']),-1) and $val_date1_time <= (_time_user(strtotime($gmdri['to']),-1) + 24*60*60)) {
            $has_check1_item=true;
            break;
          }
        }
        if ($has_check1_item==false) $check_group_multi=false;
        //$return['message']=base64_encode('ddd1 '.$check_group_multi);return $return;
        
        
        if ($val_date2_time>0) {
          $has_check2_item=false;
          foreach ($return['transfer_properties']['transfer_reservation_group_multi_date_range']['data'] as $gmdri) {
            if ($val_date2_time >= _time_user(strtotime($gmdri['from']),-1) and $val_date2_time <= (_time_user(strtotime($gmdri['to']),-1) + 24*60*60)) {
              $has_check2_item=true;
              break;
            }
          }
          if ($has_check2_item==false) $check_group_multi=false;
  
  //        $return['message']=base64_encode('<pre>ddd2|'.$has_check1_item.'|'.$has_check2_item.'|'.$check_group_multi.'|'.date('d/m/Y H:i',$val_date2_time).'|'.
  //        print_r($return['transfer_properties']['transfer_reservation_group_multi_date_range']['data'],true).
  //        '|'.
  //        date('Y-m-d H:i',_time_user(strtotime('2023-11-15'),1) + 24*60*60)
  //        
  //        );return $return;
  
        }
      }
      
    }
  }
  
  //echo '<pre>check_group_multi '.$check_group_multi; die();
  
  if ($check_group_multi) {
  
    for ($diadromi = 1; $diadromi <= 2; $diadromi++) {
      if (isset($diadromes[$diadromi]['oximata'])) {
        foreach ($diadromes[$diadromi]['oximata'] as &$myoxima) {
          $min_diafora_time=false; $pricelist_id_selected_group_multi=-1;
          if (isset($oxima_type[$myoxima['transfer_oxima_type_id']]) and $oxima_type[$myoxima['transfer_oxima_type_id']]['transfer_oxima_type_roure_group_multi']==1) {
            //ean exei xrono, to pio kontino xronika
            foreach ($myoxima['pricelists'] as $mykey => $mypl) {
              if ($mypl['transfer_pricelist_price_per_person']>0 and $mypl['diafora_time']!==false) {
                if ($min_diafora_time===false) {
                  $min_diafora_time=$mypl['diafora_time'];
                  $pricelist_id_selected_group_multi=$mykey;
                } else if ($mypl['diafora_time'] < $min_diafora_time) {
                  $min_diafora_time=$mypl['diafora_time'];
                  $pricelist_id_selected_group_multi=$mykey;
                }
              }
            } 
            if ($pricelist_id_selected_group_multi>0) $myoxima['pricelist_id_selected_group_multi']=$pricelist_id_selected_group_multi;
            else {
              //efoson den brike pio pano, to proto xoris xrono
              foreach ($myoxima['pricelists'] as $mykey => $mypl) {
                if ($mypl['transfer_pricelist_price_per_person']>0 and $mypl['diafora_time']===false) {
                  $myoxima['pricelist_id_selected_group_multi']=$mykey;
                  break;
                }
              }            
            }
          }
        }
        unset($myoxima);
      }
    }
  }
  
  //echo '<pre>diadromes ';print_r($diadromes); die();
  //$return['message']=base64_encode('<pre>'.print_r($diadromes,true));return $return;
  
  $before_seconds_1=0;
  $before_seconds_2=0;
  $start_seconds_1=0;
  $start_seconds_2=0;
  if ($val_direction=='tori') {
    
    //pros aerodromio
    if ($return['data']['val_to_poi_type_id']==2)         $before_seconds_1=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_airplane'])*60;
    else if ($return['data']['val_to_poi_type_id']==3)    $before_seconds_1=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_cruise'])*60;
    else if ($return['data']['val_to_poi_type_id']==4)    $before_seconds_1=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_train'])*60;

    if ($return['data']['val_to_poi_type_id']==2)         $start_seconds_1=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_airplane']+$duration)*60;
    else if ($return['data']['val_to_poi_type_id']==3)    $start_seconds_1=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_cruise']+$duration)*60;
    else if ($return['data']['val_to_poi_type_id']==4)    $start_seconds_1=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_train']+$duration)*60;
    $diadromes[1]['pick_up_time_diff_seconds']=           $start_seconds_1;
    
    //apo aerodromio
    if ($return['data']['val_from_poi_type_id']==2)       $before_seconds_2=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_airplane'])*60;
    else if ($return['data']['val_from_poi_type_id']==3)  $before_seconds_2=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_cruise'])*60;
    else if ($return['data']['val_from_poi_type_id']==4)  $before_seconds_2=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_train'])*60;

    if ($return['data']['val_from_poi_type_id']==2)       $start_seconds_2=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_airplane']+$duration)*60;
    else if ($return['data']['val_from_poi_type_id']==3)  $start_seconds_2=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_cruise']+$duration)*60;
    else if ($return['data']['val_from_poi_type_id']==4)  $start_seconds_2=($return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_train']+$duration)*60;
    $diadromes[2]['pick_up_time_diff_seconds']=           $start_seconds_2;
    //echo '<pre>dddd '.$start_seconds_2.'|'.$return['data']['val_from_poi_type_id'].'|'.$return['transfer_properties']['transfer_return_from_pick_up_time_start_minutes_airplane'].'|'.$duration;die();
    
  } else if ($val_direction=='tole') {
    
    //apo aerodromio
    if ($return['data']['val_from_poi_type_id']==2)       $before_seconds_1=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_airplane'])*60;
    else if ($return['data']['val_from_poi_type_id']==3)  $before_seconds_1=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_cruise'])*60;
    else if ($return['data']['val_from_poi_type_id']==4)  $before_seconds_1=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_train'])*60;

    if ($return['data']['val_from_poi_type_id']==2)       $start_seconds_1=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_airplane']+$duration)*60;
    else if ($return['data']['val_from_poi_type_id']==3)  $start_seconds_1=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_cruise']+$duration)*60;
    else if ($return['data']['val_from_poi_type_id']==4)  $start_seconds_1=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_train']+$duration)*60;
    $diadromes[1]['pick_up_time_diff_seconds']=           $start_seconds_1;
    
    //pros aerodromio
    if ($return['data']['val_to_poi_type_id']==2)         $before_seconds_2=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_airplane'])*60;
    else if ($return['data']['val_to_poi_type_id']==3)    $before_seconds_2=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_cruise'])*60;
    else if ($return['data']['val_to_poi_type_id']==4)    $before_seconds_2=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_train'])*60;

    if ($return['data']['val_to_poi_type_id']==2)         $start_seconds_2=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_airplane']+$duration)*60;
    else if ($return['data']['val_to_poi_type_id']==3)    $start_seconds_2=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_cruise']+$duration)*60;
    else if ($return['data']['val_to_poi_type_id']==4)    $start_seconds_2=($return['transfer_properties']['transfer_outward_from_pick_up_time_start_minutes_train']+$duration)*60;
    $diadromes[2]['pick_up_time_diff_seconds']=           $start_seconds_2;
    
  }
  $return['data']['before_seconds_1']=$before_seconds_1;
  $return['data']['before_seconds_2']=$before_seconds_2;
  $return['data']['start_seconds_1']=$start_seconds_1;
  $return['data']['start_seconds_2']=$start_seconds_2;
  
  //echo '<pre>return array '; print $from.'|'.$duration.'|'.$val_direction.'|'.$return['data']['val_from_poi_type_id'].'|'.$return['data']['val_from_id'].'|'.$start_seconds_1.'|'.$start_seconds_2.'|'.showDate($val_date1_time,'d/m/Y H:i',1).'|'.$val_date2_time.'|'; die();
  
  //foreach ($diadromes as $diadromiindex => $mydiadromi) {
  //} 
  
  //echo '<pre>return array '; print_r($return); die();
  //echo '<pre>diadromes array '; print_r($diadromes); die();
  
  
  $protaseis=[];
  
  if ($val_date2_time==0) { //one way, apli diadrromi
    
    foreach ($diadromes[1]['oximata'] as $oxima_type_id=>$myoxima1) {

      //group_one
      if ($myoxima1['pricelist_id_selected_group_one']>0 and isset($oxima_type[$oxima_type_id])) {
        $pkey=$oxima_type_id.'|'.$myoxima1['pricelist_id_selected_group_one'].'|0|group_one';
        $protasi_item=array(
          'pkey'=>$pkey,
          'oxima_type' => $oxima_type[$oxima_type_id],
          'pricelists' => array(
            'proto' => $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']],
          ),
        );
        
        
        
        
        $extra_start_time1_m=0;
        if ($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_s']!='' and
            $protasi_item['oxima_type']['transfer_oxima_type_private_start_time_e']!='' and
            $protasi_item['oxima_type']['transfer_oxima_type_private_start_time_m']>0) {
          
          $date1_time_pick=$val_date1_time-$diadromes[1]['pick_up_time_diff_seconds'];
          $diadromes[1]['pick_up_time_real']=$date1_time_pick;
          
          $return['data']['pick_up_time_real_1']=$date1_time_pick;
          $return['data']['pick_up_time_real_2']=0;
                   
          $time_t1=0;
          if ($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_t']==1) {
            $time_t1+=intval(showDate($date1_time_pick,'H',1))*60;
            $time_t1+=intval(showDate($date1_time_pick,'i',1))*1;          
          } else if ($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_t']==0) {
            $time_t1+=intval(showDate($val_date1_time,'H',1))*60;
            $time_t1+=intval(showDate($val_date1_time,'i',1))*1;          
          }
          
          //echo '<pre>'.$duration.'|'.showDate($date1_time_pick,'d/m/Y H:i',1);die();
          
          $parts=explode(':',$protasi_item['oxima_type']['transfer_oxima_type_private_start_time_s']);
          $time_s=intval($parts[0])*60 + intval($parts[1]);
          
          $parts=explode(':',$protasi_item['oxima_type']['transfer_oxima_type_private_start_time_e']);
          $time_e=intval($parts[0])*60 + intval($parts[1]);
          
          $in_time1_slot=false;
          if ($time_s < $time_e) { // p.x. 18:00 me 23:00
            if ($time_t1 >= $time_s && $time_t1 <= $time_e) {
              $in_time1_slot=true;
            }  
          } else if ($time_s > $time_e) { // p.x. 23:00 me 06:00
            if ($time_t1 >= $time_s and $time_t1 < 24*60) {  // apo 23:00 eos 24:00
              $in_time1_slot=true;
            } else if ($time_t1 <= $time_e) { // apo 00:00 eos 06:00
              $in_time1_slot=true;
            }
          }
          if ($in_time1_slot) {
            $extra_start_time1_m=floatval($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_m']);
          }
//          print '<pre>ddddffffg '.
//          $time_t1.'|'.
//          $time_s.'|'.
//          $time_e.'|'.
//          showDate($val_date1_time,'d/m/Y H:i',1).'|'.
//          $in_time1_slot.'|';  die();      
        }
        //echo '<pre>extra_start_time1_m11111111 '.$extra_start_time1_m;die();
        
        $protasi_item['extra_start_time1_m']=$extra_start_time1_m;
        
        $protasi_item['pricelists']['proto']['price_oxima_per_item']=
         ($myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer']>0 ?
          $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer'] :
          $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer'])
         + $extra_start_time1_m;
        
        $protasi_item['price_per_item_org']=
         $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer']
         + $extra_start_time1_m;
        
        $protasi_item['price_per_item']=
         ($myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer']>0 ?
          $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer'] :
          $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer'])
         + $extra_start_time1_m;
        
        //echo '<pre>'; echo $protasi_item['price_per_item'];
        
//        $protasi_item['price_per_item']=transfer_price_round_type_func(
//          $protasi_item['price_per_item'],
//          $return['transfer_properties']['transfer_price_round_type']
//        );
        
//        echo '|'.$protasi_item['price_per_item'];
//        echo "\n";
//        echo transfer_price_round_type_func(1.1111,0)."\n";
//        echo transfer_price_round_type_func(1.1111,1)."\n";
//        echo transfer_price_round_type_func(1.1111,2)."\n";
//        echo transfer_price_round_type_func(1.1111,3)."\n";
//        echo transfer_price_round_type_func(1.1111,4)."\n";
//        echo transfer_price_round_type_func(1.1111,5)."\n";
//        
//        echo transfer_price_round_type_func(2.6666,0)."\n";
//        echo transfer_price_round_type_func(2.6666,1)."\n";
//        echo transfer_price_round_type_func(2.6666,2)."\n";
//        echo transfer_price_round_type_func(2.6666,3)."\n";
//        echo transfer_price_round_type_func(2.6666,4)."\n";
//        echo transfer_price_round_type_func(2.6666,5)."\n";
//
//        die();
                         
        $protasi_item['items_need']=1;
        if ($val_passengers > $oxima_type[$oxima_type_id]['transfer_oxima_type_max_epivates']) {
          $protasi_item['items_need']=ceil($val_passengers/$oxima_type[$oxima_type_id]['transfer_oxima_type_max_epivates']);
        }
        
        $protasi_item['price_all_items_org']=$protasi_item['items_need'] * $protasi_item['price_per_item_org'];
        $protasi_item['price_all_items']=$protasi_item['items_need'] * $protasi_item['price_per_item'];
        $protasi_item['group_type']='group_one';
        
        if ($protasi_item['items_need']==1 or $return['transfer_properties']['transfer_multi_cars']==1) {
          $protaseis[]=$protasi_item;
        }
        
      }
      
      //group_multi
      if ($myoxima1['pricelist_id_selected_group_multi']>0 and isset($oxima_type[$oxima_type_id])) {
        $pkey=$oxima_type_id.'|'.$myoxima1['pricelist_id_selected_group_multi'].'|0|group_multi';
        $protasi_item=array(
          'pkey'=>$pkey,
          'oxima_type' => $oxima_type[$oxima_type_id],
          'pricelists' => array(
            'proto' => $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']],
          ),
        );
        
        $protasi_item['price_per_item_org']=$myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person'];
        
        $protasi_item['price_per_item']= //thesi
        ($myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person_offer']>0 ?
         $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person_offer'] :
         $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person']);

//        $protasi_item['price_per_item']=transfer_price_round_type_func(
//          $protasi_item['price_per_item'],
//          $return['transfer_properties']['transfer_price_round_type']
//        );
                                 
        $protasi_item['items_need']=$val_passengers; //thesis
        $protasi_item['price_all_items_org']=$protasi_item['items_need'] * $protasi_item['price_per_item_org'];
        $protasi_item['price_all_items']=$protasi_item['items_need'] * $protasi_item['price_per_item'];
        $protasi_item['group_type']='group_multi';
        
        $protaseis[]=$protasi_item;
        
      }
    }      
      

    
    
  } else { // me epistrofi
    
    foreach ($diadromes[1]['oximata'] as $oxima_type_id=>$myoxima1) {
      if (isset($diadromes[2]['oximata'][$oxima_type_id])) { //iporxei idio oxima gia tin epistrofi
        $myoxima2=$diadromes[2]['oximata'][$oxima_type_id];
        
        //group_one
        if ($myoxima1['pricelist_id_selected_group_one']>0 and $myoxima2['pricelist_id_selected_group_one']>0 and isset($oxima_type[$oxima_type_id])) {
          $pkey=$oxima_type_id.'|'.$myoxima1['pricelist_id_selected_group_one'].'|'.$myoxima2['pricelist_id_selected_group_one'].'|group_one';
          $protasi_item=array(
            'pkey'=>$pkey,
            'oxima_type' => $oxima_type[$oxima_type_id],
            'pricelists' => array(
              'proto' => $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']],
              'deytero' => $myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_one']],
            ),
          );
          
          $extra_start_time1_m=0; $extra_start_time2_m=0;
          if ($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_s']!='' and
              $protasi_item['oxima_type']['transfer_oxima_type_private_start_time_e']!='' and
              $protasi_item['oxima_type']['transfer_oxima_type_private_start_time_m']>0) {
            
            $date1_time_pick=$val_date1_time-$diadromes[1]['pick_up_time_diff_seconds'];
            $diadromes[1]['pick_up_time_real']=$date1_time_pick;
            $return['data']['pick_up_time_real_1']=$date1_time_pick;
            
            $date2_time_pick=$val_date2_time-$diadromes[2]['pick_up_time_diff_seconds'];
            $diadromes[2]['pick_up_time_real']=$date2_time_pick;
            $return['data']['pick_up_time_real_2']=$date2_time_pick;
                     
            $time_t1=0;
            if ($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_t']==1) {
              $time_t1+=intval(showDate($date1_time_pick,'H',1))*60;
              $time_t1+=intval(showDate($date1_time_pick,'i',1))*1;          
            } else if ($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_t']==0) {
              $time_t1+=intval(showDate($val_date1_time,'H',1))*60;
              $time_t1+=intval(showDate($val_date1_time,'i',1))*1;          
            }

            
            $time_t2=0;
            if ($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_t']==1) {
              $time_t2+=intval(showDate($date2_time_pick,'H',1))*60;
              $time_t2+=intval(showDate($date2_time_pick,'i',1))*1;          
            } else if ($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_t']==0) {
              $time_t2+=intval(showDate($val_date2_time,'H',1))*60;
              $time_t2+=intval(showDate($val_date2_time,'i',1))*1;          
            }            
            
            //echo '<pre>'.$duration.'|'.showDate($date1_time_pick,'d/m/Y H:i',1);die();
            
            $parts=explode(':',$protasi_item['oxima_type']['transfer_oxima_type_private_start_time_s']);
            $time_s=intval($parts[0])*60 + intval($parts[1]);
            
            $parts=explode(':',$protasi_item['oxima_type']['transfer_oxima_type_private_start_time_e']);
            $time_e=intval($parts[0])*60 + intval($parts[1]);
            
            $in_time1_slot=false;
            if ($time_s < $time_e) { // p.x. 18:00 me 23:00
              if ($time_t1 >= $time_s && $time_t1 <= $time_e) {
                $in_time1_slot=true;
              }  
            } else if ($time_s > $time_e) { // p.x. 23:00 me 06:00
              if ($time_t1 >= $time_s and $time_t1 < 24*60) {  // apo 23:00 eos 24:00
                $in_time1_slot=true;
              } else if ($time_t1 <= $time_e) { // apo 00:00 eos 06:00
                $in_time1_slot=true;
              }
            }
            if ($in_time1_slot) {
              $extra_start_time1_m=floatval($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_m']);
            }
            
            $in_time2_slot=false;
            if ($time_s < $time_e) { // p.x. 18:00 me 23:00
              if ($time_t2 >= $time_s && $time_t2 <= $time_e) {
                $in_time2_slot=true;
              }  
            } else if ($time_s > $time_e) { // p.x. 23:00 me 06:00
              if ($time_t2 >= $time_s and $time_t2 < 24*60) {  // apo 23:00 eos 24:00
                $in_time2_slot=true;
              } else if ($time_t2 <= $time_e) { // apo 00:00 eos 06:00
                $in_time2_slot=true;
              }
            }
            if ($in_time2_slot) {
              $extra_start_time2_m=floatval($protasi_item['oxima_type']['transfer_oxima_type_private_start_time_m']);
            }            
            
//            print '<pre>ddddffffg '.
//            $duration.'|'.
//            $diadromes[1]['pick_up_time_diff_seconds'].'|'.
//            $diadromes[2]['pick_up_time_diff_seconds'].'|'.
//            'time_t1 '.$time_t1.'|'.
//            'time_t2 '.$time_t2.'|'.
//            $time_s.'|'.
//            $time_e.'|'.
//            showDate($val_date1_time,'d/m/Y H:i',1).'|'.
//            showDate($val_date2_time,'d/m/Y H:i',1).'|'.
//            $in_time1_slot.'|'.      
//            $in_time2_slot.'|';  
//            die();      
          }
          //echo '<pre>extra_start_time1_m11111111 '.$extra_start_time1_m;die();
          
          $protasi_item['extra_start_time1_m']=$extra_start_time1_m;
          $protasi_item['extra_start_time2_m']=$extra_start_time2_m;
        
          $protasi_item['pricelists']['proto']['price_oxima_per_item']=
          ($myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer']>0 ?
           $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer'] :
           $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer'])
           + $extra_start_time1_m;

          $protasi_item['pricelists']['deytero']['price_oxima_per_item']=
          ($myoxima1['pricelists'][$myoxima2['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer']>0 ?
           $myoxima1['pricelists'][$myoxima2['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer'] :
           $myoxima1['pricelists'][$myoxima2['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer'])
           + $extra_start_time2_m;


            
            
            
          $protasi_item['price_per_item_org']=
           $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer'] +
           $myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer']
           + $extra_start_time1_m + $extra_start_time2_m;
          
          $protasi_item['price_per_item']=
          ($myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer']>0 ?
           $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer'] :
           $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer'])
          +
          ($myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer']>0 ?
           $myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer_offer'] :
           $myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_one']]['transfer_pricelist_price_per_transfer'])
          + $extra_start_time1_m + $extra_start_time2_m;
          
//          $protasi_item['price_per_item']=transfer_price_round_type_func(
//            $protasi_item['price_per_item'],
//            $return['transfer_properties']['transfer_price_round_type']
//          );
                           
          $protasi_item['items_need']=1;
          if ($val_passengers > $oxima_type[$oxima_type_id]['transfer_oxima_type_max_epivates']) {
            $protasi_item['items_need']=ceil($val_passengers/$oxima_type[$oxima_type_id]['transfer_oxima_type_max_epivates']);
          }
          $protasi_item['price_all_items_org']=$protasi_item['items_need'] * $protasi_item['price_per_item_org'];
          $protasi_item['price_all_items']=$protasi_item['items_need'] * $protasi_item['price_per_item'];
          $protasi_item['group_type']='group_one';
          
          if ($protasi_item['items_need']==1 or $return['transfer_properties']['transfer_multi_cars']==1) {
            $protaseis[]=$protasi_item;
          }
          
        }
        
        //group_multi
        if ($myoxima1['pricelist_id_selected_group_multi']>0 and $myoxima2['pricelist_id_selected_group_multi']>0 and isset($oxima_type[$oxima_type_id])) {
          $pkey=$oxima_type_id.'|'.$myoxima1['pricelist_id_selected_group_multi'].'|'.$myoxima2['pricelist_id_selected_group_multi'].'|group_multi';
          $protasi_item=array(
            'pkey'=>$pkey,
            'oxima_type' => $oxima_type[$oxima_type_id],
            'pricelists' => array(
              'proto' => $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']],
              'deytero' => $myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_multi']],
            ),
          );
            
            
          $protasi_item['price_per_item_org']=
            $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person'] +
            $myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person'];
          
          $protasi_item['price_per_item']= //thesi
          ($myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person_offer']>0 ?
           $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person_offer'] :
           $myoxima1['pricelists'][$myoxima1['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person'])
          +
          ($myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person_offer']>0 ?
           $myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person_offer'] :
           $myoxima2['pricelists'][$myoxima2['pricelist_id_selected_group_multi']]['transfer_pricelist_price_per_person']);

//          $protasi_item['price_per_item']=transfer_price_round_type_func(
//            $protasi_item['price_per_item'],
//            $return['transfer_properties']['transfer_price_round_type']
//          );
                                     
          $protasi_item['items_need']=$val_passengers; //thesis
          $protasi_item['price_all_items_org']=$protasi_item['items_need'] * $protasi_item['price_per_item_org'];
          $protasi_item['price_all_items']=$protasi_item['items_need'] * $protasi_item['price_per_item'];
          $protasi_item['group_type']='group_multi';
          
          $protaseis[]=$protasi_item;
          
        }
      }
    }
  }
  
  
  usort($protaseis, "gks_transfer_protaseis_sort");
   
  
  //$return['debug'].='protaseis'."\n".print_r($protaseis,true)."\n";
  //$return['debug'].='diadromes'."\n".print_r($diadromes,true)."\n";
   
   
  $route_details=array(
    'apostasi' => 0,
    'diarkeia' =>0,
    
  );
  
  
  //$return['data']['val_to_descr']=$_gks_session['gks']['ui_lang'];
  $return['success']=true; 
  //$return['oxima_type']=$oxima_type;
   
  
  $return['diadromes']=$diadromes; 
  $return['protaseis']=$protaseis; 
  $cache_file='transfer_'.date('Y-m-d_H-i-s').'_'.rand(10000,99999).'.json';
  $return['cache_file']=$cache_file; 
  
  if ($from=='website') {
    file_put_contents(GKS_CACHE.$cache_file,json_encode(array(
    	'id_transfer'=> $id_transfer,
    	'row_transfer'=> $row_transfer,
    	'input_data'=> $input_data,
    	'gks_erp_cookie_id'=> $gks_erp_cookie_id,
    	'return' => $return,
    	
    ),JSON_PRETTY_PRINT));
  }

  //echo '<pre>';print_r($return);die();
  //echo '<pre>';print_r($diadromes);die();
  //echo '<pre>';print_r($protaseis);die();
  
  return $return;
 
  
}

function gks_transfer_protaseis_sort($a, $b) {
  if ($a['oxima_type']['sort_order'] > $b['oxima_type']['sort_order']) return 1;
  if ($a['oxima_type']['sort_order'] < $b['oxima_type']['sort_order']) return -1;

  if ($a['price_all_items'] > $b['price_all_items']) return 1;
  if ($a['price_all_items'] < $b['price_all_items']) return -1;
  
  
  
  return 0;
}


//$vertices_x = array(37.628134, 37.629867, 37.62324, 37.622424);    // x-coordinates of the vertices of the polygon
//$vertices_y = array(-77.458334,-77.449021,-77.445416,-77.457819); // y-coordinates of the vertices of the polygon
//$points_polygon = count($vertices_x) - 1;  // number vertices - zero-based array
//$longitude_x = $_GET["longitude"];  // x-coordinate of the point to test
//$latitude_y = $_GET["latitude"];    // y-coordinate of the point to test
//if (is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)){
//  echo "Is in polygon!";
//}
//else echo "Is not in polygon";
//


