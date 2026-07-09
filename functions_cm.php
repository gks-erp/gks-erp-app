<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_zodomus_get_url($sub_url,$request_type,$input) {
  
  if (GKS_ZODOMUS_MODE_LIVE==true) {
    $gks_zodomus_setting=array(
      'url'=>'',
      'user'=>'',
      'password'=>'',
      'password_cc'=>'',
    );
  } else {
    $gks_zodomus_setting=array(
      'url'=>'',
      'user'=>'',
      'password'=>'',
      'password_cc'=>'',
    );
  }

  
  $url=$gks_zodomus_setting['url'].$sub_url;
  //print '<pre>';print_r($url);die();
  
//  $data_string='';
//  if (is_array($input)) 
//    $data_string = json_encode($input);
//  else 
//    $data_string = '';
  if ($request_type=='GET') {
    if (is_array($input)) {
      $myq=http_build_query($input);
      $url.='?'.$myq;
      //echo '<pre>'.$url;die();
    } else if (is_string($input)) {
      $url.='?'.$input;
    }
  }
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
  if ($request_type=='POST') {
    //print '<pre>';print_r($gks_zodomus_setting);die();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
  } else if ($request_type=='GET') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  }
  curl_setopt($ch, CURLOPT_HEADER, true);
  $headers=array(
      
      'Content-type: application/json; charset=UTF8',
  );
  curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
  curl_setopt($ch, CURLOPT_USERPWD, $gks_zodomus_setting['user'].':' . ($sub_url=='/reservations-cc' ? $gks_zodomus_setting['password_cc'] : $gks_zodomus_setting['password']));
  
  
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  curl_close ($ch); 
  
  //echo '<pre>';echo $result."\n";die();
  //echo '<pre>';echo $result."\n";echo 'error number:';var_dump($gks_curl_errno);var_dump($gks_curl_info);
  
  //echo '<pre>';echo $result;die();
  //echo '<pre>';var_dump($gks_curl_errno);die();
  //echo '<pre>';var_dump($gks_curl_info);die();
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  //echo '<pre>';var_dump($gks_curl_http_code);die();
  if ($gks_curl_http_code==0) { //HTTP Host not found
    //debug_mail(false,'gks_zodomus_get_url error','Δεν βρέθηκε ο διακομιστής gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode('Δεν βρέθηκε ο διακομιστής του eshop'));
    
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    //debug_mail(false,'gks_zodomus_get_url error','Δεν βρέθηκε η σελίδα gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode('Δεν βρέθηκε το πρόσθετο στο Wordpress'));
  
  } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
    //debug_mail(false,'gks_zodomus_get_url error','Γενικό σφάλμα (1)'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode('Γενικό σφάλμα (1)'));
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    //debug_mail(false,'gks_zodomus_get_url error','Δεν επιτρέπεται η πρόσβαση'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    //debug_mail(false,'gks_zodomus_get_url error','Γενικό σφάλμα (2): HTTP Response Error'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true));
    return array('success' => false, 'message' => base64_encode('Γενικό σφάλμα (2): HTTP Response Error: '.$gks_curl_http_code));
  
  }

  //echo $ret['message']; die();
  $parts=explode("\r\n\r\n",$result,2);
  if (count($parts)!=2) {
    //debug_mail(false,'gks_zodomus_get_url result error',$result);
    return array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (1).'.$result));}
  
  $response=trim($parts[1]);
  if ($response=='') {
    //debug_mail(false,'gks_zodomus_get_url response error',$response);
    return array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (2).'.$result));}
    
  
  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    //debug_mail(false,'gks_zodomus_get_url json_decode error',$response);
    return array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα δεδομένων').' (3).'.$result));}
  

  $log_path=GKS_SITE_PATH.'logs_cm/';  
  
  if (file_exists($log_path)==false) {
    if (@mkdir($log_path , 0755, true) == false ) {
      debug_mail(false,'can not create dir: ',$log_path);
      //die('error');
    }
  }  
  $log_file=$log_path.showDate(time(),'Y-m-d_H-i-s',1).'_'.time().'_'.rand(10000,99999).'.json';
  
  file_put_contents($log_file, $response);
  
  return array('success' => true, 'message' => base64_encode('OK'), 'response_array' => $response_array);
    
}

function gks_hotel_cm_reservation_parse($response,$params) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $gks_cache_version;

  require_once('vendor_inc/Nicer.php');
  
  $return = array('success' => false, 'message' => 'generic error gks_hotel_cm_reservation_parse');
   
  $channelId=intval($params['channelId']);
  $propertyId=trim_gks($params['propertyId']);
   
  if ($channelId!=1) {
    debug_mail(false,'gks_hotel_cm_reservation_parse channelId error',print_r($params,true));
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (1)');}
  
  if ($propertyId=='') {
    debug_mail(false,'gks_hotel_cm_reservation_parse propertyId error',print_r($params,true));
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (2)');}
  
  $sql="select id_hotel,company_id,company_sub_id from gks_hotel where ";
  if ($channelId==1)      $sql.="hotel_id_booking"; //Booking.com
  else if ($channelId==2) $sql.="hotel_id_expedia"; //Expedia
  else if ($channelId==3) $sql.="hotel_id_airbnb"; //Airbnb
  else if ($channelId==4) $sql.="hotel_id_agoda"; //Agoda

  $sql.="='".$db_link->escape_string($propertyId)."'";
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
  
  if ($result->num_rows==0) {
    debug_mail(false,'hotel not found',$sql);
    return array('success' => false, 'message' => 'Δεν βρέθηκε το ξενοδοχείο');}
  $row = $result->fetch_assoc();
  $id_hotel=intval($row['id_hotel']);
  $id_company=intval($row['company_id']);
  $id_company_sub=intval($row['company_sub_id']);
  $hotel_params=gks_hotel_get_params($id_hotel);
  //echo $id_hotel;die();
  
  
  
  $data = json_decode($response, true);
  if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'gks_hotel_cm_reservation_parse json_decode error',$response);
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (3)');}

  //echo '<pre>';print_r($data);die();

  if (isset($data['status'])==false or isset($data['status']['returnCode'])==false or $data['status']['returnCode']!=200) {
    debug_mail(false,'gks_hotel_cm_reservation_parse json_decode error',$response);
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (4)');}
  

  //(Integer 1=new, 2=modified, 3=cancelled)
  $cm_status=intval($data['reservations']['reservation']['status']);
  if ($cm_status==1 or $cm_status==2) {
    $reservation_status='080confirm';
  } else if ($cm_status==3) {
    $reservation_status='040cancelled';
  } else {
    $reservation_status='010draft';  
  }
    

  
  if ($reservation_status!= '040cancelled' and (isset($data['reservations'])==false or 
      isset($data['reservations']['reservation'])==false or 
      isset($data['reservations']['customer'])==false or 
      isset($data['reservations']['rooms'])==false)) { 
    debug_mail(false,'gks_hotel_cm_reservation_parse json_decode error',$response);
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (5)');}

  
  $cm_reservation_id=trim_gks($data['reservations']['reservation']['id']);
  
  $raw_file='<!DOCTYPE html><html dir="ltr" lang="en-US"><head>
  		<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/vendor_inc/nice_r.css?v='.$gks_cache_version.'"/>
  		<script type="text/javascript" src="'.GKS_SITE_URL.'my/vendor_inc/nice_r.js?v='.$gks_cache_version.'"></script>
  	</head><body>';
          $obj_nicer = new Nicer($data, true, true);
          $raw_file.=$obj_nicer->render(false);
          $raw_file.='<div id="raw_print_r_b" onclick="raw_toggle()">RAW Print_r</div>';
          $raw_file.='<div style="display:none;" id="raw_print_r"><pre>';
          $raw_file.=print_r($data,true);
          $raw_file.='</pre></div>';
  $raw_file.='</body>
  </html>';  

  if ($reservation_status=='040cancelled') {
    $sql="select id_hotel_reservation from gks_hotel_reservation where hotel_id=".$id_hotel." and booking_reservation_id like '".$db_link->escape_string($cm_reservation_id)."'";
    //echo $sql;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    $cancel_ids=array();
    while ($row = $result->fetch_assoc()) {
      $cancel_ids[]=$row['id_hotel_reservation'];
    }
    
    $sql="update gks_hotel_reservation set reservation_status='040cancelled' where id_hotel_reservation in (".implode(',',$cancel_ids).")";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    
    $sql="update gks_hotel_reservation_room_day set dreservation_status='040cancelled' where hotel_reservation_id in (".implode(',',$cancel_ids).")";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    
    foreach ($cancel_ids as $cancel_id) {
  
      $sxolio_log="Ακύρωση από CM";
      $message='Ακύρωση κράτησης';
  
      $sql="insert into gks_hotel_reservation_log (hotel_reservation_id, add_date,user_id,sxolio) values (
      ".$cancel_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
  
      $message.=' με αριθμό <a href="/my/admin-hotel-reservation-item.php?id='.$cancel_id.'">'.
      '#'.$cancel_id.'</a>';
      
  
      $sql="insert into gks_notification (
      message,for_user_id,`date_add`,for_date,has_ok,model,model_id
      )
      select
      '".$db_link->escape_string($message)."' as message,
      user_id as for_user_id,
      now() as `date_add`,
      now() as `for_date`,
      0 as has_ok,'reservation' as model,
      ".$cancel_id." as model_id
      from gks_notification_userperm where notification_type_id=1010
      and from_admin=1 and from_user=1".gks_notification_userperm_internal_users();
      //from ".GKS_WP_TABLE_PREFIX."users where gks_wp_capabilities like '%ordermanager%' or gks_wp_capabilities like '%adminmy%';";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
      
  
      $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.user_email
      FROM gks_notification_userperm 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE ".GKS_WP_TABLE_PREFIX."users.user_email<>''
      AND gks_notification_userperm.notification_type_id=1010
      AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_email=1".gks_notification_userperm_internal_users();
      //debug_mail(false,'sql',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
      } else {
        $mysubject='';$model_name='';
        $mysubject='Ακύρωση κράτησης με αριθμό '.$cancel_id;
        
        $model_name='hotel-reservation';
      
        $replaces=array();
        $replaces[] = array('[[message]]', $message);
        
        $send_viber=array();
        while ($row = $result->fetch_assoc()) {
          $params=array(
            'model'=>$model_name,
            'model_id'=>$cancel_id,
            'to'=>$row['user_email'],
            'subject'=>$mysubject,
            'template'=>3, //'empty.html',
            'replaces'=>$replaces,
          );
              
          $send_email_res = gks_mymail_template($params);
          
        }
      }
      

      $message='Ακύρωση κράτησης';
      
      $message.=' με αριθμό '.$cancel_id.' '.GKS_SITE_URL.'my/admin-hotel-reservation-item.php?id='.$cancel_id;
      
      $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
      FROM gks_notification_userperm 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
      AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
      AND gks_notification_userperm.notification_type_id=1010
      AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
      //debug_mail(false,'sql',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
      } else { 
        $send_viber=array();
        while ($row = $result->fetch_assoc()) {
          $send_viber[]=$row['viber_id'];
        }
        foreach ($send_viber as $value) {
          gks_viber_send(substr('gks_hotel_reservation', 4) ,$cancel_id ,$value,$message);
        } 
      }    
    
    
    }
    
    
    //echo '<pre>';print_r($cancel_ids);die();
    $return = array('success' => true, 'message' => 'OK');
    return $return;
  }
  
  $cm_room_type_ids=array();
  foreach ($data['reservations']['rooms'] as $cm_room) {
    if (isset($cm_room_type_ids[$cm_room['id']])==false) {
      $cm_room_type_ids[$cm_room['id']]=array(
        'id_hotel_room_type'=>0,
        'product_id' => 0,
        'product_descr'=> '',
        'product_fpa_base_id'=>0,
        'plithos'=>0,
        'id_hotel_room_ids'=>array(),
      );
    }
    $cm_room_type_ids[$cm_room['id']]['plithos']++;
  }
  if (count($cm_room_type_ids)>0) {
    $channel_field='';
    if ($channelId==1)      $channel_field="booking_room_type_id"; //Booking.com
    else if ($channelId==2) $channel_field="expedia_room_type_id"; //Expedia
    else if ($channelId==3) $channel_field="airbnb_room_type_id"; //Airbnb
    else if ($channelId==4) $channel_field="agoda_room_type_id"; //Agoda
    
    
    $sql="select id_hotel_room_type, ".$channel_field." as channel_type, 
    gks_hotel_room_type.product_id,gks_eshop_products.product_fpa_base_id,gks_eshop_products.product_descr
    from gks_hotel_room_type 
    LEFT JOIN gks_eshop_products ON gks_hotel_room_type.product_id = gks_eshop_products.id_product
    where ".$channel_field." in (";
    foreach ($cm_room_type_ids as $key => $value) {
      $sql.="'".$db_link->escape_string($key)."',";
    }
    $sql=substr($sql, 0, strlen($sql)-1);
    $sql.=")";
    //echo '<pre>';echo $sql;die();
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    while ($row = $result->fetch_assoc()) {
      $cm_room_type_ids[$row['channel_type']]['id_hotel_room_type']=$row['id_hotel_room_type'];
      $cm_room_type_ids[$row['channel_type']]['product_id']=$row['product_id'];
      $cm_room_type_ids[$row['channel_type']]['product_fpa_base_id']=$row['product_fpa_base_id'];
      $cm_room_type_ids[$row['channel_type']]['product_descr']=$row['product_descr'];
    }
  }
  $is_all_ok=true;
  foreach ($cm_room_type_ids as $key => $value) {
    if ($value['id_hotel_room_type']==0 or $value['product_id']==0 or $value['product_fpa_base_id']==0) {$is_all_ok=false; break;}
  }
  if ($is_all_ok==false) {
    debug_mail(false,'gks_hotel_cm_reservation_parse not all room types found error',print_r($cm_room_type_ids,true));
    return array('success' => false, 'message' => 'Δεν βρέθηκαν όλοι οι τύποι των δωματίων (1) ή τα προϊόντα τιμολόγησης ή ο φόρος του προϊόντος');}
    
    
  
  //print '<pre>';print_r($cm_room_type_ids);die();
  $min_arrivalDate=0;
  $max_departureDate=0;
  
  $cm_rooms_array=array();$aa_room=0;
  foreach ($data['reservations']['rooms'] as $cm_room) {
    $key=$cm_room['arrivalDate'].'_'.$cm_room['departureDate'];
    if (isset($cm_rooms_array[$key])==false) {
      $cm_rooms_array[$key]=array(
         'arrivalDate'=>$cm_room['arrivalDate'],
         'departureDate' => $cm_room['departureDate'],
         'roomReservationIds'=>array(),
         'id_hotel_reservation'=>0,
         'db_found' => false,
      );
    }
    $aa_room++;
    $cm_rooms_array[$key]['roomReservationIds'][]=array(
      'roomReservationId_org'=>$cm_room['roomReservationId'], 
      'roomReservationId'=>$channelId.'_'.$data['reservations']['reservation']['id'].'_'.$aa_room, 
      'id_hotel_reservation_room'=>0,
      'exist_id_hotel_room' => 0,
      'exist_id_hotel_room_type' => 0,
      'exist_product_id' => 0,
      'exist_product_fpa_base_id' => 0,
      'exist_product_descr' => '',
      
      'db_r_found' => false,
      'cm_r_data'=> $cm_room,
    );
    
    $temp=strtotime($cm_room['arrivalDate']);
    if ($min_arrivalDate==0 or $temp < $min_arrivalDate) $min_arrivalDate=$temp;
    
    $temp=strtotime($cm_room['departureDate']);
    if ($max_departureDate==0 or $temp > $max_departureDate) $max_departureDate=$temp;
    
    
  }
  //print '<pre>';print_r($cm_rooms_array);die();
  
  $sql="select id_hotel_reservation from gks_hotel_reservation where hotel_id=".$id_hotel." and booking_reservation_id like '".$db_link->escape_string($cm_reservation_id)."'";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}

  $id_hotel_reservation_ids=array();$id_hotel_reservation_data=array();
  $is_new_rec= false;
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $id_hotel_reservation_ids[]=$row['id_hotel_reservation'];
      $id_hotel_reservation_data[$row['id_hotel_reservation']]=array(
        'id_hotel_reservation'=>intval($row['id_hotel_reservation']),
        'cm_found'=>false,
        'rooms'=>array(),
      );
    }
  }
  if (count($id_hotel_reservation_ids)>0) {
    $sql="select id_hotel_reservation_room, hotel_reservation_id, cm_roomReservationId,
    gks_hotel_reservation_room.hotel_room_id, gks_hotel_room.hotel_room_type_id,
    gks_hotel_room_type.product_id, gks_eshop_products.product_descr, gks_eshop_products.product_fpa_base_id
    
    FROM ((gks_hotel_reservation_room 
    LEFT JOIN gks_hotel_room ON gks_hotel_reservation_room.hotel_room_id = gks_hotel_room.id_hotel_room) 
    LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
    LEFT JOIN gks_eshop_products ON gks_hotel_room_type.product_id = gks_eshop_products.id_product

    
    where hotel_reservation_id in (".implode(',',$id_hotel_reservation_ids).")";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    while ($row = $result->fetch_assoc()) {
      $id_hotel_reservation_data[$row['hotel_reservation_id']]['rooms'][]=array(
        'id_hotel_reservation_room' => intval($row['id_hotel_reservation_room']),
        'hotel_reservation_id' => intval($row['hotel_reservation_id']),
        'exist_id_hotel_room' => intval($row['hotel_room_id']),
        'exist_id_hotel_room_type' => intval($row['hotel_room_type_id']),
        'exist_product_id' => intval($row['product_id']),
        'exist_product_fpa_base_id' => intval($row['product_fpa_base_id']),
        'exist_product_descr' => trim_gks($row['product_descr']),
        'cm_roomReservationId'=> trim_gks($row['cm_roomReservationId']),
        'cm_r_found'=>false,
      );
    }
  }
  //echo '<pre>';print_r($id_hotel_reservation_data);die();
  
  $has_set=array();
  foreach ($cm_rooms_array as &$cm_rsrv) {
    foreach ($cm_rsrv['roomReservationIds'] as &$cm_room) {
      
    
      foreach ($id_hotel_reservation_data as &$db_rsrv) {
        foreach ($db_rsrv['rooms'] as &$db_room) {
        
          if ($cm_room['roomReservationId']==$db_room['cm_roomReservationId']) {
            if ($cm_rsrv['id_hotel_reservation']==0 and in_array($db_rsrv['id_hotel_reservation'],$has_set)==false) {
              $cm_rsrv['id_hotel_reservation']=$db_rsrv['id_hotel_reservation'];
              $cm_rsrv['db_found']=true;
              $db_rsrv['cm_found']=true;
              
            }
            
            $cm_room['id_hotel_reservation_room']=$db_room['id_hotel_reservation_room'];
            $cm_room['exist_id_hotel_room']=$db_room['exist_id_hotel_room'];
            $cm_room['exist_id_hotel_room_type']=$db_room['exist_id_hotel_room_type'];
            $cm_room['exist_product_id']=$db_room['exist_product_id'];
            $cm_room['exist_product_fpa_base_id']=$db_room['exist_product_fpa_base_id'];
            $cm_room['exist_product_descr']=$db_room['exist_product_descr'];
            

            
            $cm_room['db_r_found']=true;
            $db_room['cm_r_found']=true;
            
            $has_set[]=$db_rsrv['id_hotel_reservation'];
          }
        }
        unset($db_room);
        
      }
      unset($db_rsrv);
    }
    unset($cm_room);
    
  } 
  unset($cm_rsrv);
  //echo '<pre>';print_r($cm_rooms_array);print_r($id_hotel_reservation_ids);print_r($id_hotel_reservation_data);die();
  
  foreach ($cm_rooms_array as &$cm_rsrv) {
    
    if ($cm_rsrv['id_hotel_reservation']==0) {
     
      $reservation_guid=guid_for_reservation();
      $bank_deposit_9digit=gks_get_bank_deposit_9digit();
      $sql="insert into gks_hotel_reservation (
      reservation_guid,reservation_status,bank_deposit_9digit,
      user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
      hotel_id,booking_reservation_id,hotel_booking_number
      ) values (
      '".$db_link->escape_string($reservation_guid)."','010draft','".$db_link->escape_string($bank_deposit_9digit)."',
      ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
      ".$id_hotel.",'".$db_link->escape_string($cm_reservation_id)."','".$db_link->escape_string($cm_reservation_id.'-0')."')";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
  
      $cm_rsrv['id_hotel_reservation']=$db_link->insert_id;
      $cm_rsrv['is_new']=true;
    } 
  } 
  unset($cm_rsrv);
  //echo '<pre>';print_r($cm_rooms_array);print_r($id_hotel_reservation_ids);print_r($id_hotel_reservation_data);die();


  $gks_price_total = floatval($data['reservations']['reservation']['totalPrice']);
  $tropos_apostolis=1;
  $tropos_pliromis=10;
  
  $bookedAt=date('Y-m-d H:i:s');
  //if (isset($data['reservations']['reservation']['bookedAt']) && $data['reservations']['reservation']['bookedAt']!='0000-00-00 00:00:00') $bookedAt=$data['reservations']['reservation']['bookedAt'];
  //if (abs(time()-strtotime($bookedAt)) < 24*60*60) $bookedAt=date('Y-m-d H:i:s');
  
  //$modifiedAt=date('Y-m-d H:i:s');
  //if (isset($data['reservations']['reservation']['modifiedAt']) && $data['reservations']['reservation']['modifiedAt']!='0000-00-00 00:00:00') $modifiedAt=$data['reservations']['reservation']['modifiedAt'];
  
  $sql="SELECT id_acc_seira, acc_journal_id,seira_code
  FROM gks_acc_seires
  WHERE is_disable=0
  and acc_journal_id in (
    SELECT gks_acc_journal.id_acc_journal
    FROM gks_hotel 
    LEFT JOIN gks_acc_journal ON (gks_hotel.company_sub_id = gks_acc_journal.company_sub_id) AND (gks_hotel.company_id = gks_acc_journal.company_id)
    WHERE gks_acc_journal.acc_eidos_parastatikou_id=1200
    AND gks_hotel.id_hotel=".$id_hotel."
    AND gks_acc_journal.is_disable=0
  )
  ORDER BY sortorder";
  //echo '<pre>'.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
  if ($result->num_rows==0) {
    debug_mail(false,'journal and seira not found',$sql);
    return array('success' => false, 'message' => 'Δεν βρέθηκε το ημερολόγιο και η σειρά');}
  $row = $result->fetch_assoc();
  $reservation_journal_id=$row['acc_journal_id'];
  $reservation_seira_id=$row['id_acc_seira'];
  $reservation_seira_code=$row['seira_code'];
  
  $fiscal_position_id=1; $ma_country_id=0;
  if (isset($data['reservations']['customer']['countryCode']) and trim_gks($data['reservations']['customer']['countryCode'])!='') {
    $sql="select * from gks_country where country_initials='".$db_link->escape_string(trim_gks($data['reservations']['customer']['countryCode']))."'";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $ma_country_id=$row['id_country'];
      if ($ma_country_id==91) { //Ελλάδα
        $fiscal_position_id=1;
      } else {
        if (isset($row['country_ee']) and trim_gks($row['country_ee'])!='') {
          $fiscal_position_id=2; //Λιανικής Ενδοκοινοτικές
        } else {
          $fiscal_position_id=3; //Λιανικής Τρίτες Χώρες
        }
      }
    }
  }
  
  if ($ma_country_id==91) {
    $user_lang='el-GR';
  } else {
    $user_lang='en-US';
    if (isset($data['reservations']['customer']['countryCode']) and trim_gks($data['reservations']['customer']['countryCode'])!='') {
      $sql="select id_lang from gks_lang where lang_ico like '".$db_link->escape_string(trim_gks($data['reservations']['customer']['countryCode']))."'";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
      if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
        $user_lang=$row['id_lang'];
      }
    }
  }
  
  $pricelist_id=1;
  $user_first_name=''; if (isset($data['reservations']['customer']['firstName'])) $user_first_name=trim_gks($data['reservations']['customer']['firstName']);
  $middleName=''; if (isset($data['reservations']['customer']['middleName'])) $middleName=trim_gks($data['reservations']['customer']['middleName']);
  $user_last_name=''; if (isset($data['reservations']['customer']['lastName'])) $user_last_name=trim_gks($data['reservations']['customer']['lastName']);
  $ma_odos=''; if (isset($data['reservations']['customer']['address'])) $ma_odos=trim_gks($data['reservations']['customer']['address']);
  $ma_poli=''; if (isset($data['reservations']['customer']['city'])) $ma_poli=trim_gks($data['reservations']['customer']['city']);
  $ma_tk=''; if (isset($data['reservations']['customer']['zipCode'])) $ma_tk=trim_gks($data['reservations']['customer']['zipCode']);
  $user_email=''; if (isset($data['reservations']['customer']['email'])) $user_email=trim_gks($data['reservations']['customer']['email']);
  $user_mobile=''; if (isset($data['reservations']['customer']['phone'])) $user_mobile=trim_gks($data['reservations']['customer']['phone']);
  $user_mobile_org=$user_mobile;
  $phoneCountryCode=''; if (isset($data['reservations']['customer']['phoneCountryCode'])) $phoneCountryCode=trim_gks($data['reservations']['customer']['phoneCountryCode']);
  $phoneCityArea=''; if (isset($data['reservations']['customer']['phoneCityArea'])) $phoneCityArea=trim_gks($data['reservations']['customer']['phoneCityArea']);
  
  if ($phoneCountryCode!='' and $phoneCityArea!='') {
    $user_mobile=$phoneCountryCode.' '.$phoneCityArea.' '.$user_mobile;
  } else if ($phoneCountryCode!='' and $phoneCityArea=='') {
    $user_mobile=$phoneCountryCode.' '.$user_mobile;
  } else if ($phoneCountryCode=='' and $phoneCityArea!='') {
    $user_mobile=$phoneCityArea.' '.$user_mobile;
  }
  
  $user_id=0;
  if ($user_email!='') {
    $sql="select ID from ".GKS_WP_TABLE_PREFIX."users where user_email like '".$db_link->escape_string($user_email)."' order by ID";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();  
      $user_id=$row['ID'];
    }  
  }
  if ($user_id==0 and $user_email!='') {
    $sql="select user_id from gks_users_communication where comm_value like '".$db_link->escape_string($user_email)."' order by user_id";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();  
      $user_id=$row['user_id'];
    }  
  }
  if ($user_id==0 and $user_mobile_org!='') {
    $sql="select user_id from gks_users_communication where comm_value like '%".$db_link->escape_string($user_mobile_org)."' order by user_id";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();  
      $user_id=$row['user_id'];
    }  
  }
  

  //calc_profilepososto($user_id,$is_new_user);
  //ttha prepei prota na mpei o neos xristis
  
  $user_notes=''; if (isset($data['reservations']['reservation']['remarks'])) $user_notes=trim_gks($data['reservations']['reservation']['remarks']);
  $sxolio=''; if (isset($data['reservations']['customer']['remarks'])) $sxolio=trim_gks($data['reservations']['customer']['remarks']);
  
  $crm_channel_id=0;
  if ($channelId==1)      $crm_channel_id=21; //Booking.com
  else if ($channelId==2) $crm_channel_id=23; //Expedia
  else if ($channelId==3) $crm_channel_id=22; //Airbnb
  else if ($channelId==4) $crm_channel_id=24; //Agoda

  
  $crm_channel_code='';if (isset($data['reservations']['reservation']['id'])) $crm_channel_code=trim_gks($data['reservations']['reservation']['id']);
  

  /*
  childs_ages_list json_encode 
    Array
    (
        [0] => 3
        [1] => 6
        [2] => 11
    )

  rchilds_ages_list  json_encode 
    Array
    (
        [0] => Array
            (
                [index] => 1
                [age] => 3
            )
    
        [1] => Array
            (
                [index] => 2
                [age] => 6
            )
    
        [2] => Array
            (
                [index] => 3
                [age] => 11
            )
    
    )
  */
  
  foreach ($cm_rooms_array as &$cm_rsrv) {
    $num_adults=0;$num_childs=0;$childs_ages_list=array();
    $cacc=-1;
    foreach ($cm_rsrv['roomReservationIds'] as &$cm_room) {
      $rnum_adults=0;$rnum_childs=0;$rchilds_ages_list=array();
      if (isset($cm_room['cm_r_data']['guestCount'])) {
        foreach ($cm_room['cm_r_data']['guestCount'] as $value) {
          if (isset($value['adult']) and $value['adult']==1) {
            $rnum_adults+=$value['count'];
          } else if (isset($value['adult']) and $value['adult']==0) {
            $rnum_childs+=$value['count'];
            for($cc=1; $cc<=$value['count']; $cc++) {
              $cacc++;
              $childs_ages_list[$cacc]=$value['age'];
              $rchilds_ages_list[]=array(
                'index' => ($cacc + 1),
                'age' => $value['age'],
              );
              
            }
          }
        } 
        
      }
      
      $cm_room['rnum_adults']=$rnum_adults;
      $cm_room['rnum_childs']=$rnum_childs;
      $cm_room['rchilds_ages_list']=$rchilds_ages_list;
      
      
      $num_adults+=$rnum_adults;
      $num_childs+=$rnum_childs;
      
    }
    unset($cm_room);  
    
    
    $cm_rsrv['num_adults']=$num_adults;
    $cm_rsrv['num_childs']=$num_childs;
    $cm_rsrv['childs_ages_list']=$childs_ages_list;
  }
  unset($cm_rsrv); 
  
  //echo '<pre>';print_r($cm_rooms_array);die();
  
  $kat_id=0;
  foreach ($cm_rooms_array as &$cm_rsrv) {
    $check_in=date('Y-m-d',strtotime($cm_rsrv['arrivalDate'])).' '.$hotel_params['hotel_default_checkin'];
    $check_out=date('Y-m-d',strtotime($cm_rsrv['departureDate'])+(24*60*60)).' '.$hotel_params['hotel_default_checkout'];
    $num_days=intval((strtotime($cm_rsrv['departureDate'])-strtotime($cm_rsrv['arrivalDate']))/(24*60*60)) + 1;
    
    $rooms_plithos=count($cm_rsrv['roomReservationIds']);
    
    $cm_rsrv['check_in']=$check_in;
    $cm_rsrv['check_out']=$check_out;
    $cm_rsrv['num_days']=$num_days;
    $cm_rsrv['rooms_plithos']=$rooms_plithos ;
    
//    $num_adults=0;$num_childs=0;
//    foreach ($cm_rsrv['roomReservationIds'] as $cm_room) {
//      foreach ($data['reservations']['rooms'] as $data_room) {
//        if ($cm_room['roomReservationId']==$data_room['roomReservationId']) {
//          $numberOfGuests=intval($data_room['numberOfGuests']);
//          $numberOfAdults=intval($data_room['numberOfAdults']);
//          $numberOChildren=intval($data_room['numberOChildren']);
//          if ($numberOfGuests>0 and $numberOfAdults==0 and $numberOChildren==0) {
//            $num_adults+=$numberOfGuests;
//          } else {
//            $num_adults+=$numberOfAdults;
//            $num_childs+=$numberOChildren;
//          }
//          $rooms_plithos++;
//        }
//      }
//    } 
    
    
    $kat_id++;  
    $sql="update gks_hotel_reservation set ";
    if (isset($cm_rsrv['is_new']) and $cm_rsrv['is_new']) $sql.="reservation_date='".$bookedAt."',";
    $sql.="
    hotel_booking_number='".$db_link->escape_string($crm_channel_code.'-'.$kat_id)."',
    reservation_journal_id=".$reservation_journal_id.",
    reservation_seira_id=".$reservation_seira_id.",
    reservation_seira_code='".$db_link->escape_string($reservation_seira_code)."',
    reservation_status='".$db_link->escape_string($reservation_status)."',
    check_in='".$check_in."',
    check_out='".$check_out."',
    num_days=".$num_days.",
    num_adults=".$cm_rsrv['num_adults'].",
    num_childs=".$cm_rsrv['num_childs'].",
    childs_ages_list='".$db_link->escape_string(json_encode($cm_rsrv['childs_ages_list']))."',
    rooms_plithos=".$rooms_plithos.",
    
    user_id=".$user_id.",
    user_email='".$db_link->escape_string($user_email)."',
    user_first_name='".$db_link->escape_string($user_first_name)."',
    user_last_name='".$db_link->escape_string($user_last_name)."',
    user_mobile='".$db_link->escape_string($user_mobile)."',
    user_lang='".$db_link->escape_string($user_lang)."',
    
    ma_odos='".$db_link->escape_string($ma_odos)."',
    ma_poli='".$db_link->escape_string($ma_poli)."',
    ma_tk='".$db_link->escape_string($ma_tk)."',
    ma_country_id=".$ma_country_id.",
    sxolio='".$db_link->escape_string($sxolio)."',
    user_notes='".$db_link->escape_string($user_notes)."',
    
    gks_price_netfpa=".$gks_price_total.",
    gks_price_total=".$gks_price_total.",
    fiscal_position_id=".$fiscal_position_id.",
    pricelist_id=".$pricelist_id.",
    tropos_pliromis=".$tropos_pliromis.",
    
    crm_channel_id=".$crm_channel_id.",
    crm_channel_code='".$db_link->escape_string($crm_channel_code)."',
    
    mydate_edit=now(),
    user_id_edit=2,
    myip='".$db_link->escape_string($gkIP)."'
    where id_hotel_reservation=".$cm_rsrv['id_hotel_reservation'];
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    
    //echo '<pre>';print $sql;
  }
  unset($cm_rsrv);

  //echo '<pre>';print_r($cm_rooms_array);print_r($id_hotel_reservation_ids);print_r($id_hotel_reservation_data);die();

  foreach ($cm_rooms_array as &$cm_rsrv) {
    foreach ($cm_rsrv['roomReservationIds'] as &$cm_room) {
      if ($cm_room['id_hotel_reservation_room']==0) {
        $sql="insert into gks_hotel_reservation_room (
          user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
          hotel_reservation_id,cm_roomReservationId
          
        ) values (
          ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
          ".$cm_rsrv['id_hotel_reservation'].",'".$db_link->escape_string($cm_room['roomReservationId'])."'
        )";
        
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    
        $cm_room['id_hotel_reservation_room']=$db_link->insert_id;
      } else {
        $sql="update gks_hotel_reservation_room set 
        hotel_reservation_id=".$cm_rsrv['id_hotel_reservation']."
        where id_hotel_reservation_room=".$cm_room['id_hotel_reservation_room'];
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
        
        
      }
      
    }
    unset($cm_room);
  }
  unset($cm_rsrv);
  //echo '<pre>';print_r($cm_rooms_array);die();
  
  
  

  //print '<pre>';print_r($cm_room_type_ids);die();
  

  
  $hotel_room_type_ids=array();
  foreach ($cm_room_type_ids as $value) {
    $hotel_room_type_ids[]=$value['id_hotel_room_type'];
  } 
  $sql="select * from gks_hotel_room where hotel_room_type_id in (".implode(',',$hotel_room_type_ids).")";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
  while ($row = $result->fetch_assoc()) {
    foreach ($cm_room_type_ids as &$value) {
      //if ($row['hotel_room_type_id']==$value['id_hotel_room_type'] and count($value['id_hotel_room_ids']) < $value['plithos']) {
      if ($row['hotel_room_type_id']==$value['id_hotel_room_type']) {
        $value['id_hotel_room_ids'][]=array(
          'id_hotel_room'=> $row['id_hotel_room'],
          'has_assign'=> false,
          //'is_free' => false,
        );
      }
    }
    unset($value);
  }
  
  $is_all_ok=true;
  foreach ($cm_room_type_ids as $value) {
    if ($value['plithos']>count($value['id_hotel_room_ids'])) {$is_all_ok=false;break;}
  }
  if ($is_all_ok==false) {
    debug_mail(false,'gks_hotel_cm_reservation_parse not all room found error',print_r($cm_room_type_ids,true));
    return array('success' => false, 'message' => 'Δεν βρέθηκαν όλα τα δωμάτια (2)');}
  
  //print '<pre>';print_r($cm_room_type_ids);print_r($cm_rooms_array);die();

  //na mpoun prota ta exist kai na oristoun os has_assign

  $rooms_array=array();
  
  foreach ($cm_rooms_array as &$cm_rsrv) {
    
    //find empty rooms

    $get_availability_rooms_imput=array(
      'id_hotel' => $id_hotel,
      'date_from' => (showDate(strtotime($cm_rsrv['check_in']),'Y-m-d',0)),
      'date_to' => (showDate(strtotime($cm_rsrv['check_out'])-24*60*60,'Y-m-d',0)),
      'alldata' => false,
      'id_hotel_room' => 0,
      'id_hotel_room_type' => 0,
      'not_id_hotel_reservation' => $cm_rsrv['id_hotel_reservation'],
      'not_id_hotel_folio' => 0,
      'not_id_hotel_room' => array(),
      'rnum_adults' => 0,
      'rnum_childs' => 0,
      'rchilds_ages_list' => array(),
      'rnum_child_kounies' =>0,
      'rnum_extra_beds' =>0,
    );
    //print '<pre>';print_r($get_availability_rooms_imput);die();
    $rooms_array[$cm_rsrv['id_hotel_reservation']] = get_availability_rooms($get_availability_rooms_imput);    
    //print '<pre>';print_r($rooms_array);die();

    
    
    foreach ($cm_rsrv['roomReservationIds'] as &$cm_room) {
      if ($cm_room['exist_id_hotel_room']>0 and $cm_room['exist_id_hotel_room_type']>0) {
        $cm_rtid=$cm_room['cm_r_data']['id'];
        if (isset($cm_room_type_ids[$cm_rtid]) and $cm_room_type_ids[$cm_rtid]['id_hotel_room_type']==$cm_room['exist_id_hotel_room_type']) {
          
          
          //check for free
          $found_free=false;
          foreach ($cm_room_type_ids[$cm_rtid]['id_hotel_room_ids'] as $room_id => &$room) {
            if ($room['id_hotel_room']==$cm_room['exist_id_hotel_room']) {
              
              if (isset($rooms_array[$cm_rsrv['id_hotel_reservation']]['rooms'][$cm_room['exist_id_hotel_room']]['is_avl_state_folio']) and $rooms_array[$cm_rsrv['id_hotel_reservation']]['rooms'][$cm_room['exist_id_hotel_room']]['is_avl_state_folio'] == true) {
                
                $cm_room['id_hotel_room']=      $cm_room['exist_id_hotel_room'];
                $cm_room['id_hotel_room_type']= $cm_room['exist_id_hotel_room_type'];
                $cm_room['product_id']=         $cm_room['exist_product_id'];
                $cm_room['product_fpa_base_id']=$cm_room['exist_product_fpa_base_id'];
                $cm_room['product_descr']=      $cm_room['exist_product_descr'];    
                $cm_room['is_free']=true;
                $room['has_assign']=true;
              }
              
            }
          } 
          unset($room);          
        }
      }
    }
    unset($cm_room);
  }
  unset($cm_rsrv);

  //print '<pre>';print_r($cm_room_type_ids);print_r($cm_rooms_array);die();
  
  //check for free rooms
  foreach ($cm_room_type_ids as $cm_type => &$value) {
    foreach ($value['id_hotel_room_ids'] as $room_id => &$room) {
        
      
      if ($room['has_assign']==false) {
        foreach ($cm_rooms_array as &$cm_rsrv) {
          //is free
          if (isset($rooms_array[$cm_rsrv['id_hotel_reservation']]['rooms'][$room['id_hotel_room']]['is_avl_state_folio']) and $rooms_array[$cm_rsrv['id_hotel_reservation']]['rooms'][$room['id_hotel_room']]['is_avl_state_folio'] == true) {
          
          
            foreach ($cm_rsrv['roomReservationIds'] as &$cm_room) {
              if ($cm_type==$cm_room['cm_r_data']['id'] and isset($cm_room['id_hotel_room'])==false) {
                $cm_room['id_hotel_room']=$room['id_hotel_room'];
                $cm_room['id_hotel_room_type']=$value['id_hotel_room_type'];
                $cm_room['product_id']=$value['product_id'];
                $cm_room['product_fpa_base_id']=$value['product_fpa_base_id'];
                $cm_room['product_descr']=$value['product_descr'];
                $cm_room['is_free']=true;
                $room['has_assign']=true;
                unset($cm_room);
                break 2;
              }
            } 
            unset($cm_room);
          }
        } 
        unset($cm_rsrv);
      }
      
    } 
    unset($room);
  }
  unset($value);
  
  
  //check for not free rooms
  foreach ($cm_room_type_ids as $cm_type => &$value) {
    foreach ($value['id_hotel_room_ids'] as $room_id => &$room) {
      if ($room['has_assign']==false) {
        foreach ($cm_rooms_array as &$cm_rsrv) {
          foreach ($cm_rsrv['roomReservationIds'] as &$cm_room) {
            if ($cm_type==$cm_room['cm_r_data']['id'] and isset($cm_room['id_hotel_room'])==false) {
              $cm_room['id_hotel_room']=$room['id_hotel_room'];
              $cm_room['id_hotel_room_type']=$value['id_hotel_room_type'];
              $cm_room['product_id']=$value['product_id'];
              $cm_room['product_fpa_base_id']=$value['product_fpa_base_id'];
              $cm_room['product_descr']=$value['product_descr'];
              $cm_room['is_free']=false;
              $room['has_assign']=true;
              unset($cm_room);
              break 2;
            }
          } 
          unset($cm_room);
        } 
        unset($cm_rsrv);
      }
    } 
    unset($room);
  }
  unset($value);  
  
  
  
  
  //is map rooms OK ?
  $is_all_ok=true;
  foreach ($cm_rooms_array as $cm_rsrv) {
    foreach ($cm_rsrv['roomReservationIds'] as $cm_room) {
      if (isset($cm_room['id_hotel_room'])==false or isset($cm_room['id_hotel_room_type'])==false) {$is_all_ok=false; break;}
    }
  }
  if ($is_all_ok==false) {
    debug_mail(false,'gks_hotel_cm_reservation_parse not all room found error',print_r($cm_room_type_ids,true));
    return array('success' => false, 'message' => 'Δεν βρέθηκαν όλα τα δωμάτια (3)');}

  
  
  //print '<pre>';print_r($cm_room_type_ids);print_r($cm_rooms_array);die();

  
  
  foreach ($cm_rooms_array as &$cm_rsrv) {
    foreach ($cm_rsrv['roomReservationIds'] as &$cm_room) {
      
      $ruser_id=-1; //idios pelatis
      $room_pelatis_name=trim_gks($cm_room['cm_r_data']['guestName']);
      if ($room_pelatis_name!='') {
        $namev1=trim_gks($data['reservations']['customer']['firstName']).' '.trim_gks($data['reservations']['customer']['lastName']);
        $namev2=trim_gks($data['reservations']['customer']['lastName']).' '.trim_gks($data['reservations']['customer']['firstName']);
        if ($room_pelatis_name!=$namev1 and $room_pelatis_name!=$namev2) { //allos pelatis
          $ruser_id=0;
        }
      }
      
      $ruser_first_name='';$ruser_last_name='';
      if ($ruser_id==0 and $room_pelatis_name!='') {
        $parts=explode(' ',$room_pelatis_name,2);
        if (count($parts)==2 and trim_gks($parts[0])!='' and trim_gks($parts[1])!='') {
          $ruser_first_name=trim_gks($parts[0]);
          $ruser_last_name=trim_gks($parts[1]);
        } else {
          $ruser_first_name=$room_pelatis_name;
        }
        
      }
      
      
      $rsxolio='';if (isset($cm_room['cm_r_data']['remarks'])) $rsxolio=trim_gks($cm_room['cm_r_data']['remarks']); 
      
      $product_price_final_all_total=floatval($cm_room['cm_r_data']['totalPrice']);
      
      $sql="update gks_hotel_reservation_room set
      hotel_reservation_id=".$cm_rsrv['id_hotel_reservation'].",
      hotel_room_id=".$cm_room['id_hotel_room'].",
      rnum_adults=".$cm_room['rnum_adults'].",
      rnum_childs=".$cm_room['rnum_childs'].",
      rchilds_ages_list='".$db_link->escape_string(json_encode($cm_room['rchilds_ages_list']))."',
      ruser_id=".$ruser_id.",
      ruser_first_name='".$db_link->escape_string($ruser_first_name)."',
      ruser_last_name='".$db_link->escape_string($ruser_last_name)."',
      
      
      ruser_lang='".$db_link->escape_string($user_lang)."',
      rsxolio='".$db_link->escape_string($rsxolio)."',
      ruser_fiscal_position_id=".$fiscal_position_id.",
      ruser_pricelist_id=".$pricelist_id.",
      product_quantity=1,
      product_price_final_all_total=".$product_price_final_all_total.",
      
      
      
      
      user_id_edit=".$my_wp_user_id.",
      mydate_edit=now(),
      myip='".$db_link->escape_string($gkIP)."'
      where id_hotel_reservation_room=".$cm_room['id_hotel_reservation_room'];
      //echo '<pre>'.$sql.'</pre>';
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
      
  
       
    } 
    unset($cm_room);
  } 
  unset($cm_rsrv);
  
  //echo '<pre>';print_r($cm_room_type_ids);print_r($cm_rooms_array);die();
  //echo '<pre>';print_r($cm_rooms_array);die();


  foreach ($cm_rooms_array as &$cm_rsrv) {
    $product_aa=0;
    $cm_rsrv['eidi_array']=array();
    $fields_change=array();
    foreach ($cm_rsrv['roomReservationIds'] as &$cm_room) {
  

      $product_id=$cm_room['product_id'];
      $product_fpa_base_id=$cm_room['product_fpa_base_id'];
      $product_quantity=$cm_rsrv['num_days'];
      
      
      $product_monada_id=100;
      $product_price_check_fpa=0;
      $product_price_start_all_net=0; //floatval($item['total_org']);
      $product_price_final_all_net=floatval($cm_room['cm_r_data']['totalPrice']);
      $product_price_final_all_fpa=0; //floatval($item['total_tax']);
      
      $product_price_ekptosi_pososto=0;
      if ($product_price_start_all_net!=0) $product_price_ekptosi_pososto=100*($product_price_start_all_net-$product_price_final_all_net)/$product_price_start_all_net;
  
      //echo '<pre>';print_r($cm_room);die();
      
      $product_aa++;
      $fields_change[$product_aa]='gks_price';
      $hh_item=array(
        'aa' => $product_aa,
        'id_order_product' => 0, //$item['id_order_product'],
        'product_id' => $product_id,
        'product_fpa_base_id' => $product_fpa_base_id,
        'product_fpa_id' => 0,
        'product_fpa_pososto' => 0,
        'product_sheets' => 0,
        'product_quantity' => $product_quantity,
        'product_monada_id' => $product_monada_id,
        'product_price_check_fpa' => $product_price_check_fpa, 
        'product_price_start_all_net' => $product_price_start_all_net,
        'product_price_ekptosi_pososto' => $product_price_ekptosi_pososto,
        'product_price_final_all_net' => $product_price_final_all_net,
        'product_price_final_all_fpa' => $product_price_final_all_fpa,
        'product_descr' => $cm_room['product_descr'],
        'product_comments' => '',
        'product_set' => '',
        'cm_room_item_id'=>$cm_room['id_hotel_room'],
        'cm_room_type_item_id'=>$cm_room['id_hotel_room_type'],
        'cm_rnum_adults'=>$cm_room['rnum_adults'],
        'cm_rnum_childs'=>$cm_room['rnum_childs'],
        'cm_rchilds_ages_list'=>$cm_room['rchilds_ages_list'],
        'cm_rnum_child_kounies'=>0,
        'cm_rnum_extra_beds'=>0,
        'input_id_hotel_reservation_room' => $cm_room['id_hotel_reservation_room'],
      );
      
      $cm_rsrv['eidi_array'][]=$hh_item;
    }
    unset($cm_room);
    $cm_rsrv['fields_change']=$fields_change;
    
  }
  unset($cm_rsrv);
  
  //echo '<pre>';print_r($cm_rooms_array);die();
  
  
  foreach ($cm_rooms_array as &$cm_rsrv) {
    $basket_products_temp =array();
    $cm_rsrv['roolist_day']=array();
    
    foreach ($cm_rsrv['eidi_array'] as &$value) {
      
    //    $user_field_change='';
    //    if ($value['aa'] == $fields_change_curr_aa) $user_field_change=$fields_change_curr_name;
    //    $user_change_ekptosi_or_final_net='';
    //    if (isset($fields_change[$value['aa']])) $user_change_ekptosi_or_final_net=$fields_change[$value['aa']];
        
        $user_field_change='gks_price_final';
        $user_change_ekptosi_or_final_net='gks_price_final';
      
        $user_ekptosi = 0; //floatval($value['product_price_ekptosi_pososto']);  
      
        $value['product_withheldPercentCategory']=0;
        $value['product_withheldAmount']=0;
        $value['product_otherTaxesPercentCategory']=0;  
        $value['product_otherTaxesAmount']=0; 
        $value['product_stampDutyPercentCategory']=0;  
        $value['product_stampDutyAmount']=0;
        $value['product_feesPercentCategory']=0;  
        $value['product_feesAmount']=0;  
        $value['product_deductionsAmount']=0;  
        
        
        $sql="select * from gks_eshop_products where id_product=".$value['product_id'];  
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
        
        if ($result->num_rows == 1) {
          $row = $result->fetch_assoc();
          $value['product_withheldPercentCategory']=$row['product_withheldPercentCategory'];
          $value['product_otherTaxesPercentCategory']=$row['product_otherTaxesPercentCategory'];
          $value['product_stampDutyPercentCategory']=$row['product_stampDutyPercentCategory'];
          $value['product_feesPercentCategory']=$row['product_feesPercentCategory'];
        }  
      
        $objects=array();
        $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $value['product_quantity'],  'files' => array(), 'warnings'=>array());
        $basket_products_temp[$value['aa']]=array(
          'is_hotel_room_type'=>1,
          'product_id'=>array(
            'id_product'=>$value['product_id'], 
            'product_monada_id' => $value['product_monada_id'], 
            'product_fpa_base_id' => $value['product_fpa_base_id'], 
            'product_sheets'=>$value['product_sheets'], 
            'product_set' => $value['product_set']
           ), 
          'objects'=>$objects,
          'user_ekptosi' => $user_ekptosi,
          'user_final_net' => 0, //floatval($value['product_price_final_all_net']),
          'user_final_total'=> floatval($value['product_price_final_all_net']),
          'user_change_ekptosi_or_final_net' => $user_change_ekptosi_or_final_net,
          'user_field_change' => $user_field_change,
          
          'other_taxes' => array(
            'withheldPercentCategory' => intval($value['product_withheldPercentCategory']),  
            'withheldAmount' => floatval($value['product_withheldAmount']),  
            'otherTaxesPercentCategory' => intval($value['product_otherTaxesPercentCategory']),  
            'otherTaxesAmount' => floatval($value['product_otherTaxesAmount']),  
            'stampDutyPercentCategory' => intval($value['product_stampDutyPercentCategory']),  
            'stampDutyAmount' => floatval($value['product_stampDutyAmount']), 
            'feesPercentCategory' => intval($value['product_feesPercentCategory']),  
            'feesAmount' => floatval($value['product_feesAmount']),  
            'deductionsAmount' => floatval($value['product_deductionsAmount']),  
          ),
          'input_id_hotel_reservation_room'=>$value['input_id_hotel_reservation_room'],
          
        );
        

      
  
      
        $basket_products_temp[$value['aa']]['id_hotel'] = $id_hotel;
  
        //echo '<pre>';print_r($value);die();
        
        $basket_products_temp[$value['aa']]['user_check_in']= date('Y-m-d',strtotime($cm_rsrv['check_in']));
        $basket_products_temp[$value['aa']]['user_check_out']= date('Y-m-d',strtotime($cm_rsrv['check_out'])-24*60*60);
        $basket_products_temp[$value['aa']]['user_room_id'] = $value['cm_room_item_id'];
        $basket_products_temp[$value['aa']]['user_room_type_item_id'] = $value['cm_room_type_item_id'];
        $basket_products_temp[$value['aa']]['user_rnum_adults'] = $value['cm_rnum_adults'];
        $basket_products_temp[$value['aa']]['user_rnum_childs'] = $value['cm_rnum_childs'];
        $basket_products_temp[$value['aa']]['user_rchilds_ages_list'] = json_encode($value['cm_rchilds_ages_list']);
        $basket_products_temp[$value['aa']]['user_rnum_child_kounies'] = $value['cm_rnum_child_kounies'];
        $basket_products_temp[$value['aa']]['user_rnum_extra_beds'] = $value['cm_rnum_extra_beds'];
        //print '<pre>';print_r($basket_products_temp[$value['aa']]);print_r($value);die();
          
        
  
  
    }
    unset($cm_room);
    //echo '<pre>';print_r($basket_products_temp);die(); 
    

    unset($mybasketarray);
    gks_mybasketarray_create($mybasketarray);
    
    $mybasketarray['from']='reservation';
    $mybasketarray['id_object'] = 0;
    $mybasketarray['company_id']=$id_company;
    $mybasketarray['company_sub_id']=$id_company_sub;
    

    
    
    $mybasketarray['user']['user_id']=$user_id;
    $mybasketarray['user']['first_name']=$user_first_name;
    $mybasketarray['user']['last_name']=$user_last_name;
    $mybasketarray['user']['email']=$user_email;
    $mybasketarray['user']['mobile']=$user_mobile;
    $mybasketarray['user']['lang']=$user_lang;
    
    $mybasketarray['user']['ma_odos']=$ma_odos;
    $mybasketarray['user']['ma_orofos']='';
    $mybasketarray['user']['ma_perioxi']='';
    $mybasketarray['user']['ma_poli']=$ma_poli;
    $mybasketarray['user']['ma_tk']=$ma_tk;
    $mybasketarray['user']['ma_country_id']=$ma_country_id;
    $mybasketarray['user']['ma_nomos_id']=0;
    $mybasketarray['user']['eponimia']='';
    $mybasketarray['user']['title']='';
    $mybasketarray['user']['afm']='';
    $mybasketarray['user']['doy']='';
    $mybasketarray['user']['epaggelma']='';
    $mybasketarray['address_extra']=-1;
    
    
    $mybasketarray['destination_data']['name'] = '';
    $mybasketarray['destination_data']['phone'] = '';
    $mybasketarray['destination_data']['odos'] = '';
    $mybasketarray['destination_data']['orofos'] = '';
    $mybasketarray['destination_data']['perioxi'] = '';
    $mybasketarray['destination_data']['poli'] =  '';
    $mybasketarray['destination_data']['tk'] = '';
    $mybasketarray['destination_data']['country_id'] = 0;
    $mybasketarray['destination_data']['nomos_id'] = 0;
    
    
    $mybasketarray['fiscal_position']=$fiscal_position_id;
    if ($mybasketarray['fiscal_position']<1) $mybasketarray['fiscal_position']=1;
    
    $mybasketarray['pricelist_id']=$pricelist_id;
    if ($mybasketarray['pricelist_id']<1) $mybasketarray['pricelist_id']=1;
    $mybasketarray['coupons']=array();
    //if (isset($mydata['coupons_array'])) {
    //  $mybasketarray['coupons']=$mydata['coupons_array'];
    //}
    
    $mybasketarray['parastatiko']=0;
   
  
    //if ($cmd_is_for_coupon) { //cmd is for coupon
      
    //}
  
    $mybasketarray['products_need_apostoli'] = false; //intval($mydata['gks_products_need_apostoli'])!=0;
    $mybasketarray['products_varos']= 0; //intval($mydata['gks_products_varos']);
    $mybasketarray['products_ogos']= 0; //intval($mydata['gks_products_ogos']);
    $mybasketarray['products_ogos_max_x']= 0; //intval($mydata['gks_products_ogos_x']);
    $mybasketarray['products_ogos_max_y']= 0; //intval($mydata['gks_products_ogos_y']);
    $mybasketarray['products_ogos_max_z']= 0; //intval($mydata['gks_products_ogos_z']);
    $mybasketarray['products_need_pliromi']=false;
    //if (floatval($mydata['gks_total_price_total'])>0) $mybasketarray['products_need_pliromi']=true;;
    
    $mybasketarray['tropos_apostolis'] = $tropos_apostolis; //intval($mydata['tropos_apostolis']);
    $mybasketarray['tropos_pliromis'] = $tropos_pliromis; //intval($mydata['tropos_pliromis']);
    
    $mybasketarray['products'] = $basket_products_temp;

    //echo '<pre>'; print_r($mybasketarray);die();
    $fields_change=$cm_rsrv['fields_change'];
    //echo '<pre>'; print_r($fields_change);die();
    $myproducts = gks_basket_recalc($mybasketarray, $fields_change, array());  

    //echo '<pre>'; print_r($myproducts);die();
    
    $gks_price_net=0;
    $gks_price_fpa=0;

    $products_posotita=0;
    $gks_price_original_net=0;
    
    
    
    
    
    foreach ($mybasketarray['products'] as $aa => $product) {
      $gks_price_net+=$product['product_id']['product_price_final_all_net'];
      $gks_price_fpa+=$product['product_id']['product_price_final_all_fpa'];
      
      $products_posotita+=$product['product_id']['product_quantity'];
      $gks_price_original_net+=$product['product_id']['product_price_start_all_net'];

      //echo '<pre>';print_r($product);die();
      $sql='';

      $sql.="room_ajia_table_math='".$db_link->escape_string($product['product_id']['room_ajia_table']['msg_price'])."',";     
      $sql.="room_ajia_table_html='".$db_link->escape_string($product['product_id']['room_ajia_table']['roomaf_html'])."',";     
      $sql.="room_ajia_table_array='".$db_link->escape_string(base64_decode($product['product_id']['room_ajia_table']['roomaf_array']))."',";     

      //echo '<pre>';print_r($product);die();
      
      $cm_rsrv['roolist_day'][]=array(
        'delete'=>0, 
        'hotel_room_id'=> $product['user_room_id'], 
        'recid'=> $product['input_id_hotel_reservation_room'], 
        'hotel_type_room_id'=>$product['user_room_type_item_id'],
      );
      
      
      $sql.="product_id=".$product['product_id']['id_product'].",";
      $sql.="product_descr='".$db_link->escape_string($product['product_id']['product_descr'])."',";
      $sql.="product_fpa_base_id=".$product['product_id']['product_fpa_base_id'].",";
      $sql.="product_fpa_id=".$product['product_id']['product_fpa_id_array']['id_fpa_to'].",";
      $sql.="product_fpa_pososto=".$product['product_id']['product_fpa_id_array']['fpa_pososto'].",";
      $sql.="product_quantity=".$product['product_id']['product_quantity'].",";
  //  apografi_posotitaonhand` double DEFAULT NULL,
      $sql.="product_price_start_all_net=".$product['product_id']['product_price_start_all_net'].",";

      $sql.="product_price_final_all_net=".$product['product_id']['product_price_final_all_net'].",";
      $sql.="product_price_final_all_fpa=".$product['product_id']['product_price_final_all_fpa'].",";
      $sql.="product_price_final_all_total=".$product['product_id']['product_price_final_all_total'].",";

      $product_price_ekptosi_net=round($product['product_id']['product_price_start_all_net']-$product['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $product_price_ekptosi_netfpa=round($product['product_id']['product_price_start_all_net']+$product['product_id']['product_price_start_all_fpa']-$product['product_id']['product_price_final_all_net']-$product['product_id']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $product_price_ekptosi_total=round($product['product_id']['product_price_start_all_total']-$product['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $product_price_ekptosi_pososto=0;
      if ($product['product_id']['product_price_start_all_net']!=0 and $product['product_id']['product_price_include_vat']==0) {
        $product_price_ekptosi_pososto=round($product_price_ekptosi_net*100/$product['product_id']['product_price_start_all_net'],2);
      } else if ($product['product_id']['product_price_start_all_total']!=0 and $product['product_id']['product_price_include_vat']!=0) {
        $product_price_ekptosi_pososto=round($product_price_ekptosi_total*100/$product['product_id']['product_price_start_all_total'],2);
      }
      $sql.="product_price_ekptosi_pososto=".$product_price_ekptosi_pososto.",";
      
      $sql.="product_price_coupon_use='',";
      $sql.="product_price_coupon_use_disabled=0,";
      $sql.="product_comments='',";
  //  production_product_pososto` double NOT NULL DEFAULT '0',
  //  product_sum_time` int(11) NOT NULL DEFAULT '0',
  
      $sql.="product_withheldPercentCategory=".$product['other_taxes']['withheldPercentCategory'].",";
      $sql.="product_withheldAmount=".$product['other_taxes']['withheldAmount'].",";
      $sql.="product_stampDutyPercentCategory=".$product['other_taxes']['stampDutyPercentCategory'].",";
      $sql.="product_stampDutyAmount=".$product['other_taxes']['stampDutyAmount'].",";
      $sql.="product_feesPercentCategory=".$product['other_taxes']['feesPercentCategory'].",";
      $sql.="product_feesAmount=".$product['other_taxes']['feesAmount'].",";
      $sql.="product_otherTaxesPercentCategory=".$product['other_taxes']['otherTaxesPercentCategory'].",";
      $sql.="product_otherTaxesAmount=".$product['other_taxes']['otherTaxesAmount'].",";


      if (isset($product['product_id']['product_fpa_id_array'])) $sql.="product_fpa_id_json='".$db_link->escape_string(json_encode($product['product_id']['product_fpa_id_array']))."',";
      if (isset($product['product_id']['product_price_include_vat'])) $sql.="product_price_include_vat=".intval($product['product_id']['product_price_include_vat']).",";
      if (isset($product['product_id']['product_price_start_peritem_db'])) $sql.="product_price_start_peritem_db=".number_format($product['product_id']['product_price_start_peritem_db'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_peritem_net'])) $sql.="product_price_start_peritem_net=".number_format($product['product_id']['product_price_start_peritem_net'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_peritem_fpa'])) $sql.="product_price_start_peritem_fpa=".number_format($product['product_id']['product_price_start_peritem_fpa'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_peritem_total'])) $sql.="product_price_start_peritem_total=".number_format($product['product_id']['product_price_start_peritem_total'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_all_fpa'])) $sql.="product_price_start_all_fpa=".number_format($product['product_id']['product_price_start_all_fpa'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_all_total'])) $sql.="product_price_start_all_total=".number_format($product['product_id']['product_price_start_all_total'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_db'])) $sql.="product_price_final_peritem_db=".number_format($product['product_id']['product_price_final_peritem_db'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_net'])) $sql.="product_price_final_peritem_net=".number_format($product['product_id']['product_price_final_peritem_net'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_fpa'])) $sql.="product_price_final_peritem_fpa=".number_format($product['product_id']['product_price_final_peritem_fpa'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_total'])) $sql.="product_price_final_peritem_total=".number_format($product['product_id']['product_price_final_peritem_total'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_all_net']) and 
          isset($product['product_id']['product_price_final_all_net'])) {
        $product_price_ekptosi_net=$product['product_id']['product_price_start_all_net']-$product['product_id']['product_price_final_all_net'];
        $sql.="product_price_ekptosi_net=".number_format($product_price_ekptosi_net,8,'.','').",";
      }
      if (isset($product['product_id']['product_pricelist_item_id'])) $sql.="product_pricelist_item_id=".intval($product['product_id']['product_pricelist_item_id']).",";
      if (isset($product['product_id']['product_pricelist_item_descr'])) $sql.="product_pricelist_item_descr='".$db_link->escape_string($product['product_id']['product_pricelist_item_descr'])."',";
      if (isset($product['product_id']['product_pricelist_item_percent'])) $sql.="product_pricelist_item_percent=".number_format($product['product_id']['product_pricelist_item_percent'],8,'.','').",";
  
  
      $sql=substr($sql, 0, strlen($sql)-1);
      $sql="update gks_hotel_reservation_room set ".$sql." where hotel_reservation_id=".$cm_rsrv['id_hotel_reservation']." and id_hotel_reservation_room=".$product['input_id_hotel_reservation_room']." limit 1";
      //echo '<pre>';echo $sql; die();
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}

    }


    $sql='';
    $gks_price_netfpa=$gks_price_net+$gks_price_fpa;
    $gks_price_total=$gks_price_netfpa;
    

    $sql.="gks_price_net=".number_format($gks_price_net,2,'.','').",";  
    $sql.="gks_price_fpa=".number_format($gks_price_fpa,2,'.','').",";  
    $sql.="gks_price_netfpa=".number_format($gks_price_netfpa,2,'.','').",";  
    $sql.="gks_price_total=".number_format($gks_price_total,2,'.','').",";  
        
    $sql.="products_need_apostoli=0,";
    $sql.="products_need_pliromi=1,";
    $sql.="products_posotita=".($cm_rsrv['num_days']*count($cm_rsrv['eidi_array'])).",";
    $sql.="gks_price_original_net=".number_format($gks_price_original_net,8,'.','').",";
    
    //if (isset($mybasketarray['products_varos'])) $sql.="products_varos=".number_format($mybasketarray['products_varos'],8,'.','').",";
    //if (isset($mybasketarray['products_ogos'])) $sql.="products_ogos=".number_format($mybasketarray['products_ogos'],8,'.','').",";
    //if (isset($mybasketarray['products_ogos_max_x'])) $sql.="products_ogos_max_x=".number_format($mybasketarray['products_ogos_max_x'],8,'.','').",";
    //if (isset($mybasketarray['products_ogos_max_y'])) $sql.="products_ogos_max_y=".number_format($mybasketarray['products_ogos_max_y'],8,'.','').",";
    //if (isset($mybasketarray['products_ogos_max_z'])) $sql.="products_ogos_max_z=".number_format($mybasketarray['products_ogos_max_z'],8,'.','').",";
    
    $sql.="products_varos=0,";
    $sql.="products_ogos=0,";
    $sql.="products_ogos_max_x=0,";
    $sql.="products_ogos_max_y=0,";
    $sql.="products_ogos_max_z=0,";
    
    
    if (isset($mybasketarray['tropoi_apostolis_all']) and 
        isset($mybasketarray['tropos_apostolis']) and
        isset($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]) ) {
          $sql.="tropos_apostolis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]))."',";
    }
    if (isset($mybasketarray['tropoi_pliromis_all']) and 
        isset($mybasketarray['tropos_pliromis']) and
        isset($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]) ) {
          $sql.="kostos_pliromis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]))."',";
    }
    
    
    $sql.="affect_balance=0,";
    $sql.="affect_balance_all_poso=1,";
    $sql.="affect_balance_all_poso_type='pliroteo',";
    $sql.="affect_balance_poso=".number_format($gks_price_total,2,'.','').",";  
    $sql.="affect_balance_pros=1,";
    
 
    
    //$sql.="session_id='".$db_link->escape_string($kataxorisi['temp_session_id'])."',";
    
    $sql=substr($sql, 0, strlen($sql)-1);
    $sql="update gks_hotel_reservation set ".$sql." where id_hotel_reservation=".$cm_rsrv['id_hotel_reservation'];
    
    
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
      
    
    $file_html=GKS_FileServerShare.'hotel/reservation/'.$cm_rsrv['id_hotel_reservation'].'/';
    
    if (file_exists($file_html)==false) {
      if (@mkdir($file_html , 0755, true) == false ) {
        debug_mail(false,'can not create dir: ',$file_html);
        //die('error');
      }
    }
    if (file_exists($file_html)) {
      $file_html.='raw_data_'.showDate(time(),'Y_m_d_H_i_s',1).'_'.rand(1000,9999).rand(1000,9999).'.html';
      file_put_contents($file_html,$raw_file);
    }
    
    
    if (isset($cm_rsrv['is_new']) and $cm_rsrv['is_new']) {
      $sxolio_log="Προσθήκη από CM"; 
      $message='Νέα κράτηση';
    } else {
      $sxolio_log="Ενημέρωση από CM";
      $message='Αλλαγή κράτησης';
    }
    $sql="insert into gks_hotel_reservation_log (hotel_reservation_id, add_date,user_id,sxolio) values (
    ".$cm_rsrv['id_hotel_reservation'].",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}


    
    $message.=' με αριθμό <a href="/my/admin-hotel-reservation-item.php?id='.$cm_rsrv['id_hotel_reservation'].'">'.
    '#'.$cm_rsrv['id_hotel_reservation'].'</a>'.
    ' από το CM του πελάτη '.$user_first_name.' '.$middleName.' '.$user_last_name;

    $sql="insert into gks_notification (
    message,for_user_id,`date_add`,for_date,has_ok,model,model_id
    )
    select
    '".$db_link->escape_string($message)."' as message,
    user_id as for_user_id,
    now() as `date_add`,
    now() as `for_date`,
    0 as has_ok,'reservation' as model,
    ".$cm_rsrv['id_hotel_reservation']." as model_id
    from gks_notification_userperm where notification_type_id=1010
    and from_admin=1 and from_user=1".gks_notification_userperm_internal_users();
    //from ".GKS_WP_TABLE_PREFIX."users where gks_wp_capabilities like '%ordermanager%' or gks_wp_capabilities like '%adminmy%';";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    

    $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.user_email
    FROM gks_notification_userperm 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.user_email<>''
    AND gks_notification_userperm.notification_type_id=1010
    AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_email=1".gks_notification_userperm_internal_users();
    //debug_mail(false,'sql',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
    } else {
      $mysubject='';$model_name='';

      if (isset($cm_rsrv['is_new']) and $cm_rsrv['is_new']) {
        $mysubject='Νέα κράτηση με αριθμό '.$cm_rsrv['id_hotel_reservation'];
      } else {
        $mysubject='Αλλαγή κράτησης με αριθμό '.$cm_rsrv['id_hotel_reservation'];
      }
      $model_name='hotel-reservation';
    
      $replaces=array();
      $replaces[] = array('[[message]]', $message);
      
      $send_viber=array();
      while ($row = $result->fetch_assoc()) {
        $params=array(
          'model'=>$model_name,
          'model_id'=>$cm_rsrv['id_hotel_reservation'],
          'to'=>$row['user_email'],
          'subject'=>$mysubject,
          'template'=>3, //'empty.html',
          'replaces'=>$replaces,
        );
            
        $send_email_res = gks_mymail_template($params);
        
      }
    }
    
    if (isset($cm_rsrv['is_new']) and $cm_rsrv['is_new']) {
      $message='Νέα κράτηση';
    } else {
      $message='Αλλαγή κράτησης';
    }
    $message.=' με αριθμό '.$cm_rsrv['id_hotel_reservation'].' '.GKS_SITE_URL.'my/admin-hotel-reservation-item.php?id='.$cm_rsrv['id_hotel_reservation'].
    ' από το CM του πελάτη '.$user_first_name.' '.$middleName.' '.$user_last_name;
    
    $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
    FROM gks_notification_userperm 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
    AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
    AND gks_notification_userperm.notification_type_id=1010
    AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
    //debug_mail(false,'sql',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
    } else { 
      $send_viber=array();
      while ($row = $result->fetch_assoc()) {
        $send_viber[]=$row['viber_id'];
      }
      foreach ($send_viber as $value) {
        gks_viber_send(substr('gks_hotel_reservation', 4) ,$cm_rsrv['id_hotel_reservation'] ,$value,$message);
      } 
    }
    
  }
  unset($cm_rsrv);
  

  //echo '<pre>';print_r($id_hotel_reservation_data);die();
  //echo '<pre>';print_r($cm_rooms_array);die();
  $delete_id_hotel_reservation=array();
  
  foreach ($cm_rooms_array as &$cm_rsrv) {  
    
    $id_hotel_reservation_room_ids=array();
    foreach ($cm_rsrv['roomReservationIds'] as $cm_room) {
      $id_hotel_reservation_room_ids[]=$cm_room['id_hotel_reservation_room'];
    }
    $sql="SELECT gks_hotel_reservation_room.id_hotel_reservation_room, gks_hotel_reservation_room.hotel_room_id, gks_hotel_room.hotel_room_type_id
    FROM gks_hotel_reservation_room 
    LEFT JOIN gks_hotel_room ON gks_hotel_reservation_room.hotel_room_id = gks_hotel_room.id_hotel_room
    where hotel_reservation_id=".$cm_rsrv['id_hotel_reservation'];
    if (count($id_hotel_reservation_room_ids)>0) {
      $sql.=" and id_hotel_reservation_room not in (".implode(',',$id_hotel_reservation_room_ids).")";
    }
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    while ($row = $result->fetch_assoc()) {
      $cm_rsrv['roolist_day'][]=array(
        'delete'=>1, 
        'hotel_room_id'=> $row['hotel_room_id'], 
        'recid'=> $row['id_hotel_reservation_room'], 
        'hotel_type_room_id'=>$row['hotel_room_type_id'],
      );
    }
    //echo '<pre>';print_r($cm_rsrv['roolist_day']);die();
    
    //$exist_records=
    
    $check_in_round_time=strtotime(showDate(strtotime($cm_rsrv['check_in']),'Y-m-d',0));
    $check_out_round_time=strtotime(showDate(strtotime($cm_rsrv['check_out'])-24*60*60,'Y-m-d',0));
    //echo $check_in_round_time.' '.$check_out_round_time;die();
    //echo '<pre>';print_r($cm_rsrv['roolist_day']);die();
    
    gks_hotel_reservation_room_day_recs($cm_rsrv['id_hotel_reservation'],$cm_rsrv['roolist_day'],
      $reservation_status,
      $check_in_round_time,$check_out_round_time
    );
    
    $sql="delete from gks_hotel_reservation_room 
    where hotel_reservation_id=".$cm_rsrv['id_hotel_reservation'];
    if (count($id_hotel_reservation_room_ids)>0) {
      $sql.=" and id_hotel_reservation_room not in (".implode(',',$id_hotel_reservation_room_ids).")";
    }
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    
    
    if (count($cm_rsrv['roomReservationIds'])==0) $delete_id_hotel_reservation[]=$cm_rsrv['id_hotel_reservation'];

    
  }
  unset($cm_rsrv);

  //echo '<pre>';print_r($id_hotel_reservation_data);die();
  foreach ($id_hotel_reservation_data as $db_rsrv) {
    $found=false;
    foreach ($cm_rooms_array as $cm_rsrv) {
      if ($cm_rsrv['id_hotel_reservation']==$db_rsrv['id_hotel_reservation']) {
        $found=true;
        break;
      }
    } 
    if ($found==false) $delete_id_hotel_reservation[]=$db_rsrv['id_hotel_reservation'];
  } 
  if (count($delete_id_hotel_reservation)>0) {
    $sql="delete from gks_hotel_reservation
    where id_hotel_reservation in (".implode(',', $delete_id_hotel_reservation).')';
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    
    $sql="delete from gks_hotel_reservation_room_day
    where hotel_reservation_id in (".implode(',', $delete_id_hotel_reservation).')';
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'sql error',$sql);return array('success' => false, 'message' => 'sql error');}
    
    
  }
  

  //echo '<pre>';print_r($cm_rooms_array);die();

  $return = array('success' => true, 'message' => 'OK');
  return $return;
}
