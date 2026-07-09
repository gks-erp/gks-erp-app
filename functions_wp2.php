<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_return_bytes($val) {
  $val = trim_gks($val);
  $last = strtolower($val[strlen($val)-1]);
  
  switch($last) {
    case 'g':
      $val = intval(substr($val,0, strlen($val)-1));
      $val *= 1024*1024*1024;
      break;
    case 'm':
      $val = intval(substr($val,0, strlen($val)-1));
      $val *= 1024*1024;
      break;
    case 'k':
      $val = intval(substr($val,0, strlen($val)-1));
      $val *= 1024;
      break;
  }
  return $val;
}
function gks_get_max_upload_file_size($return_bytes=false) {
  $v1=gks_return_bytes(ini_get('post_max_size'));
  $v2=gks_return_bytes(ini_get('upload_max_filesize'));
  $v=$v1;
  if ($v2 < $v) $v=$v2;
  if ($return_bytes) return $v;
  return number_format($v/1024/1024,0,',','.').' MB';
}

function gks_viber_send($model, $model_id, $receiver, $mytext, $mykeyboard=array(), $is_file = false, $file_size = 0, $file_name = '',$id_rec=0) {
	global $GKS_VIBER_TOKEN;
  global $db_link;

  
  if ($GKS_VIBER_TOKEN=='') return array('error'=>'Viber Token is not set');

  if (trim_gks($receiver)=='') return array('error'=>'receiver is empty');
  
  $sql="SELECT ID,viber_subscribed FROM ".GKS_WP_TABLE_PREFIX."users WHERE viber_id='".$db_link->escape_string($receiver)."'";
  $result = $db_link->query($sql);
  $user_id=0;
  if ($result and $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['ID'];
    if ($row['viber_subscribed']==0) return array('error'=>'User not subscribed');
  }
  
	$type='text';
	$myarray = array(
	  'auth_token' => $GKS_VIBER_TOKEN,
	  'receiver' => $receiver,
	  "type" => "text", 
	  "text" => $mytext, 
	);
	
  if ($is_file) {
    $type='file';
    $myarray = array(
      'auth_token' => $GKS_VIBER_TOKEN,
      'receiver' => $receiver,
      "type" => "file", 
      "media" => $mytext, 
      "size" => $file_size,
      "file_name" => $file_name, 
    );  
  }
	
	if (count($mykeyboard) > 0) {
		$myarray['keyboard'] = $mykeyboard;
	}
  
  //debug_mail(false,'viber json found.',print_r($myarray,true));

  //here goes the curl to send data to user
	$url = 'https://chatapi.viber.com/pa/send_message';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($myarray));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$response = curl_exec($ch);
	curl_close($ch);
	
	$viber_response=json_decode($response,true);
	
	$status=-1;if (isset($viber_response['status'])) $status=intval($viber_response['status']);
	$status_message='';if (isset($viber_response['status_message'])) $status_message=trim_gks($viber_response['status_message']);
	$message_token='';if (isset($viber_response['message_token'])) $message_token=trim_gks($viber_response['message_token']);
	
	if ($message_token!='' and $id_rec>0) {
	  $sql="update gks_viber_msgs set message_token='".$db_link->escape_string($message_token)."' where id_viber_msgs=".$id_rec;
  	$result = $db_link->query($sql);
  	if (!$result) {debug_mail(false,'error sql',$sql);die();}
  
  } else {
  	
    $sql="insert into gks_viber_msgs (mydate,receiver_id,user_id,
    message_type,
    message,
    status,status_message,message_token,response,
    model,model_id
    ) values (
    now(),'".$db_link->escape_string($receiver)."',".$user_id.",
    '".$db_link->escape_string($type)."',
    '".$db_link->escape_string($mytext)."',
    ".$status.",
    '".$db_link->escape_string($status_message)."',
    '".$db_link->escape_string($message_token)."',
    '".$db_link->escape_string($response)."',
    '".$db_link->escape_string($model)."',
    ".$model_id."
    )";
  	$result = $db_link->query($sql);
  	if (!$result) {debug_mail(false,'error sql',$sql);die();}
    $id_rec = $db_link->insert_id;
  }
  
	return $viber_response;
	
	//debug_mail(false,'gks_viber_send',print_r($viber_response, true)); 
	
}


function gks_update_user_from_some_move($params) {
  
  if (is_array($params)==false) return;
  $user_id=0;if (isset($params['user_id'])) $user_id=intval($params['user_id']);
  if ($user_id<=0) return;
  $table='';if (isset($params['table'])) $table=trim_gks($params['table']);
  if ($table=='') return;
  $id_table=0;if (isset($params['id_table'])) $id_table=intval($params['id_table']);
  if ($id_table<=0) return;
  
  
  
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, gks_users.id_users, 
  ".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.user_url, 
  ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id, ".GKS_WP_TABLE_PREFIX."users.pricelist_id, 
  ".GKS_WP_TABLE_PREFIX."users.gks_sex, ".GKS_WP_TABLE_PREFIX."users.gks_lang, 
  gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
  gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, gks_users.ma_poli, gks_users.ma_tk, gks_users.ma_country_id, gks_users.ma_nomos_id, 
  gks_users.ma_latitude, gks_users.ma_longitude,
  gks_users.phone_home,gks_users.genisi_date
  FROM ".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id
  where ".GKS_WP_TABLE_PREFIX."users.ID=".$user_id;
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  
  if ($result->num_rows==0) return;
  
  $row_user_exist = $result->fetch_assoc();
  $row_user_exist['first_name']='';
  $row_user_exist['last_name']='';
  $row_user_exist['mobile']='';
  $row_user_exist['communication']=array();

  
  if (isset($row_user_exist['id_users'])==false) {
    $sql="insert into gks_users (user_id,mydate_add,user_id_add,myip) values (".$user_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
  }

  $sql="select meta_key,meta_value from ".GKS_WP_TABLE_PREFIX."usermeta where user_id=".$user_id." and meta_key in ('first_name','last_name','mobile')";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  while ($row = $result->fetch_assoc()) {
    $row_user_exist[$row['meta_key']]=trim_gks($row['meta_value']);
  }
  
  $sql="select comm_type,comm_value,comm_primary from gks_users_communication where user_id=".$user_id;
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  while ($row = $result->fetch_assoc()) {
    $row_user_exist['communication'][]=$row;
  }
    
  //user from table 
  $row_user_table=array();

  
  switch ($table) {
    case 'gks_transfer_reservation':
      $sql="select user_id, user_email,user_first_name,user_last_name,user_mobile,user_lang,
      eponimia,title,afm,doy,epaggelma,
      ma_odos,ma_arithmos,ma_orofos,ma_perioxi,ma_poli,ma_tk,ma_country_id,ma_nomos_id,
      fiscal_position_id,pricelist_id
      from gks_transfer_reservation
      where id_transfer_reservation=".$id_table." and user_id=".$user_id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      if ($result->num_rows==0) return;
      $row = $result->fetch_assoc();

      $row_user_table['user_email'] = trim_gks($row['user_email']);
      $row_user_table['fiscal_position_id'] = intval($row['fiscal_position_id']);
      $row_user_table['pricelist_id'] = intval($row['pricelist_id']);
      //$row_user_table['gks_sex] = trim_gks($row['']);
      $row_user_table['gks_lang'] = trim_gks($row['user_lang']);
      $row_user_table['eponimia'] = trim_gks($row['eponimia']);
      $row_user_table['title'] = trim_gks($row['title']);
      $row_user_table['afm'] = trim_gks($row['afm']);
      $row_user_table['doy'] = trim_gks($row['doy']);
      $row_user_table['epaggelma'] = trim_gks($row['epaggelma']);
      $row_user_table['ma_odos'] = trim_gks($row['ma_odos']);
      $row_user_table['ma_arithmos'] = trim_gks($row['ma_arithmos']);
      $row_user_table['ma_orofos'] = trim_gks($row['ma_orofos']);
      $row_user_table['ma_perioxi'] = trim_gks($row['ma_perioxi']);
      $row_user_table['ma_poli'] = trim_gks($row['ma_poli']);
      $row_user_table['ma_tk'] = trim_gks($row['ma_tk']); 
      $row_user_table['ma_country_id'] = intval($row['ma_country_id']);
      $row_user_table['ma_nomos_id'] = intval($row['ma_nomos_id']);
      //$row_user_table['ma_latitude'] = floatval($row['']);
      //$row_user_table['ma_longitude'] = floatval($row['']);
      $row_user_table['first_name'] = trim_gks($row['user_first_name']);
      $row_user_table['last_name'] = trim_gks($row['user_last_name']);
      $row_user_table['mobile'] = trim_gks($row['user_mobile']);
      //$row_user_table['phone_home'] = trim_gks($row['phone_home']);
      break;
      
    case 'gks_hotel_reservation':
      $sql="select user_id, user_email,user_first_name,user_last_name,user_mobile,user_lang,
      eponimia,title,afm,doy,epaggelma,
      ma_odos,ma_arithmos,ma_orofos,ma_perioxi,ma_poli,ma_tk,ma_country_id,ma_nomos_id,
      fiscal_position_id,pricelist_id
      from gks_hotel_reservation
      where id_hotel_reservation=".$id_table." and user_id=".$user_id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      if ($result->num_rows==0) return;
      $row = $result->fetch_assoc();
      $row_user_table['user_email'] = trim_gks($row['user_email']);
      $row_user_table['fiscal_position_id'] = intval($row['fiscal_position_id']);
      $row_user_table['pricelist_id'] = intval($row['pricelist_id']);
      //$row_user_table['gks_sex] = trim_gks($row['']);
      $row_user_table['gks_lang'] = trim_gks($row['user_lang']);
      $row_user_table['eponimia'] = trim_gks($row['eponimia']);
      $row_user_table['title'] = trim_gks($row['title']);
      $row_user_table['afm'] = trim_gks($row['afm']);
      $row_user_table['doy'] = trim_gks($row['doy']);
      $row_user_table['epaggelma'] = trim_gks($row['epaggelma']);
      $row_user_table['ma_odos'] = trim_gks($row['ma_odos']);
      $row_user_table['ma_arithmos'] = trim_gks($row['ma_arithmos']);
      $row_user_table['ma_orofos'] = trim_gks($row['ma_orofos']);
      $row_user_table['ma_perioxi'] = trim_gks($row['ma_perioxi']);
      $row_user_table['ma_poli'] = trim_gks($row['ma_poli']);
      $row_user_table['ma_tk'] = trim_gks($row['ma_tk']); 
      $row_user_table['ma_country_id'] = intval($row['ma_country_id']);
      $row_user_table['ma_nomos_id'] = intval($row['ma_nomos_id']);
      //$row_user_table['ma_latitude'] = floatval($row['']);
      //$row_user_table['ma_longitude'] = floatval($row['']);
      $row_user_table['first_name'] = trim_gks($row['user_first_name']);
      $row_user_table['last_name'] = trim_gks($row['user_last_name']);
      $row_user_table['mobile'] = trim_gks($row['user_mobile']);
      //$row_user_table['phone_home'] = trim_gks($row['phone_home']);
      break;
      
    case 'gks_acc_inv':
      $sql="select user_id, user_email,user_first_name,user_last_name,user_mobile,user_lang,
      eponimia,title,afm,doy,epaggelma,
      ma_odos,ma_arithmos,ma_orofos,ma_perioxi,ma_poli,ma_tk,ma_country_id,ma_nomos_id,
      fiscal_position_id,pricelist_id
      from gks_acc_inv
      where id_acc_inv=".$id_table." and user_id=".$user_id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      if ($result->num_rows==0) return;
      $row = $result->fetch_assoc();
      $row_user_table['user_email'] = trim_gks($row['user_email']);
      $row_user_table['fiscal_position_id'] = intval($row['fiscal_position_id']);
      $row_user_table['pricelist_id'] = intval($row['pricelist_id']);
      //$row_user_table['gks_sex] = trim_gks($row['']);
      $row_user_table['gks_lang'] = trim_gks($row['user_lang']);
      $row_user_table['eponimia'] = trim_gks($row['eponimia']);
      $row_user_table['title'] = trim_gks($row['title']);
      $row_user_table['afm'] = trim_gks($row['afm']);
      $row_user_table['doy'] = trim_gks($row['doy']);
      $row_user_table['epaggelma'] = trim_gks($row['epaggelma']);
      $row_user_table['ma_odos'] = trim_gks($row['ma_odos']);
      $row_user_table['ma_arithmos'] = trim_gks($row['ma_arithmos']);
      $row_user_table['ma_orofos'] = trim_gks($row['ma_orofos']);
      $row_user_table['ma_perioxi'] = trim_gks($row['ma_perioxi']);
      $row_user_table['ma_poli'] = trim_gks($row['ma_poli']);
      $row_user_table['ma_tk'] = trim_gks($row['ma_tk']); 
      $row_user_table['ma_country_id'] = intval($row['ma_country_id']);
      $row_user_table['ma_nomos_id'] = intval($row['ma_nomos_id']);
      //$row_user_table['ma_latitude'] = floatval($row['']);
      //$row_user_table['ma_longitude'] = floatval($row['']);
      $row_user_table['first_name'] = trim_gks($row['user_first_name']);
      $row_user_table['last_name'] = trim_gks($row['user_last_name']);
      $temp=trim_gks($row['user_mobile']);
      if ($temp!='' and substr($temp,0,1)=='6') $row_user_table['mobile']= $temp;
      if ($temp!='' and substr($temp,0,1)!='6') $row_user_table['phone_home']= $temp;
      break;

    case 'gks_orders':
      $sql="select user_id, user_email,user_first_name,user_last_name,user_mobile,user_lang,
      eponimia,title,afm,doy,epaggelma,
      ma_odos,ma_arithmos,ma_orofos,ma_perioxi,ma_poli,ma_tk,ma_country_id,ma_nomos_id,
      fiscal_position_id,pricelist_id
      from gks_orders
      where id_order=".$id_table." and user_id=".$user_id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      if ($result->num_rows==0) return;
      $row = $result->fetch_assoc();
      $row_user_table['user_email'] = trim_gks($row['user_email']);
      $row_user_table['fiscal_position_id'] = intval($row['fiscal_position_id']);
      $row_user_table['pricelist_id'] = intval($row['pricelist_id']);
      //$row_user_table['gks_sex] = trim_gks($row['']);
      $row_user_table['gks_lang'] = trim_gks($row['user_lang']);
      $row_user_table['eponimia'] = trim_gks($row['eponimia']);
      $row_user_table['title'] = trim_gks($row['title']);
      $row_user_table['afm'] = trim_gks($row['afm']);
      $row_user_table['doy'] = trim_gks($row['doy']);
      $row_user_table['epaggelma'] = trim_gks($row['epaggelma']);
      $row_user_table['ma_odos'] = trim_gks($row['ma_odos']);
      $row_user_table['ma_arithmos'] = trim_gks($row['ma_arithmos']);
      $row_user_table['ma_orofos'] = trim_gks($row['ma_orofos']);
      $row_user_table['ma_perioxi'] = trim_gks($row['ma_perioxi']);
      $row_user_table['ma_poli'] = trim_gks($row['ma_poli']);
      $row_user_table['ma_tk'] = trim_gks($row['ma_tk']); 
      $row_user_table['ma_country_id'] = intval($row['ma_country_id']);
      $row_user_table['ma_nomos_id'] = intval($row['ma_nomos_id']);
      //$row_user_table['ma_latitude'] = floatval($row['']);
      //$row_user_table['ma_longitude'] = floatval($row['']);
      $row_user_table['first_name'] = trim_gks($row['user_first_name']);
      $row_user_table['last_name'] = trim_gks($row['user_last_name']);
      $temp=trim_gks($row['user_mobile']);
      if ($temp!='' and substr($temp,0,1)=='6') $row_user_table['mobile']= $temp;
      if ($temp!='' and substr($temp,0,1)!='6') $row_user_table['phone_home']= $temp;
      break;
      
    case 'gks_whi_mov':
      $sql="select user_id, user_email,user_first_name,user_last_name,user_mobile,user_lang,
      eponimia,title,afm,doy,epaggelma,
      ma_odos,ma_arithmos,ma_orofos,ma_perioxi,ma_poli,ma_tk,ma_country_id,ma_nomos_id,
      fiscal_position_id,pricelist_id
      from gks_whi_mov
      where id_whi_mov=".$id_table." and user_id=".$user_id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      if ($result->num_rows==0) return;
      $row = $result->fetch_assoc();
      $row_user_table['user_email'] = trim_gks($row['user_email']);
      $row_user_table['fiscal_position_id'] = intval($row['fiscal_position_id']);
      $row_user_table['pricelist_id'] = intval($row['pricelist_id']);
      //$row_user_table['gks_sex] = trim_gks($row['']);
      $row_user_table['gks_lang'] = trim_gks($row['user_lang']);
      $row_user_table['eponimia'] = trim_gks($row['eponimia']);
      $row_user_table['title'] = trim_gks($row['title']);
      $row_user_table['afm'] = trim_gks($row['afm']);
      $row_user_table['doy'] = trim_gks($row['doy']);
      $row_user_table['epaggelma'] = trim_gks($row['epaggelma']);
      $row_user_table['ma_odos'] = trim_gks($row['ma_odos']);
      $row_user_table['ma_arithmos'] = trim_gks($row['ma_arithmos']);
      $row_user_table['ma_orofos'] = trim_gks($row['ma_orofos']);
      $row_user_table['ma_perioxi'] = trim_gks($row['ma_perioxi']);
      $row_user_table['ma_poli'] = trim_gks($row['ma_poli']);
      $row_user_table['ma_tk'] = trim_gks($row['ma_tk']); 
      $row_user_table['ma_country_id'] = intval($row['ma_country_id']);
      $row_user_table['ma_nomos_id'] = intval($row['ma_nomos_id']);
      //$row_user_table['ma_latitude'] = floatval($row['']);
      //$row_user_table['ma_longitude'] = floatval($row['']);
      $row_user_table['first_name'] = trim_gks($row['user_first_name']);
      $row_user_table['last_name'] = trim_gks($row['user_last_name']);
      $temp=trim_gks($row['user_mobile']);
      if ($temp!='' and substr($temp,0,1)=='6') $row_user_table['mobile']= $temp;
      if ($temp!='' and substr($temp,0,1)!='6') $row_user_table['phone_home']= $temp;
      break; 
      
    case 'gks_crm_leads':
      $sql="select user_id, email,web,first_name,last_name,mobile,phone,user_lang,
      eponimia,title,afm,doy,epaggelma,
      odos,arithmos,orofos,perioxi,poli,tk,country_id,nomos_id,
      map_latitude,map_longitude,
      fiscal_position_id,pricelist_id,
      address_extra,birthday
      from gks_crm_leads
      where id_crm_lead=".$id_table." and user_id=".$user_id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      if ($result->num_rows==0) return;
      $row = $result->fetch_assoc();
      $row_user_table['user_email'] = trim_gks($row['email']);
      $row_user_table['fiscal_position_id'] = intval($row['fiscal_position_id']);
      $row_user_table['pricelist_id'] = intval($row['pricelist_id']);
      //$row_user_table['gks_sex] = trim_gks($row['']);
      $row_user_table['gks_lang'] = trim_gks($row['user_lang']);
      $row_user_table['eponimia'] = trim_gks($row['eponimia']);
      $row_user_table['title'] = trim_gks($row['title']);
      $row_user_table['afm'] = trim_gks($row['afm']);
      $row_user_table['doy'] = trim_gks($row['doy']);
      $row_user_table['epaggelma'] = trim_gks($row['epaggelma']);
      if ($row['address_extra']==-1) {
        $row_user_table['ma_odos'] = trim_gks($row['odos']);
        $row_user_table['ma_arithmos'] = trim_gks($row['arithmos']);
        $row_user_table['ma_orofos'] = trim_gks($row['orofos']);
        $row_user_table['ma_perioxi'] = trim_gks($row['perioxi']);
        $row_user_table['ma_poli'] = trim_gks($row['poli']);
        $row_user_table['ma_tk'] = trim_gks($row['tk']); 
        $row_user_table['ma_country_id'] = intval($row['country_id']);
        $row_user_table['ma_nomos_id'] = intval($row['nomos_id']);
        $row_user_table['ma_latitude'] = floatval($row['map_latitude']);
        $row_user_table['ma_longitude'] = floatval($row['map_longitude']);
      }
      $row_user_table['genisi_date'] = trim_gks($row['birthday']);
      $row_user_table['first_name'] = trim_gks($row['first_name']);
      $row_user_table['last_name'] = trim_gks($row['last_name']);
      
      $row_user_table['mobile']= trim_gks($row['mobile']);
      $row_user_table['phone_home']= trim_gks($row['phone']);
      $row_user_table['user_url']= trim_gks($row['web']);
      
      break;
    case 'gks_crm_tasks':
      $sql="select user_id, email,web,first_name,last_name,mobile,phone,user_lang,
      eponimia,title,afm,doy,epaggelma,
      odos,arithmos,orofos,perioxi,poli,tk,country_id,nomos_id,
      map_latitude,map_longitude,
      fiscal_position_id,pricelist_id,
      address_extra,birthday
      from gks_crm_tasks
      where id_crm_task=".$id_table." and user_id=".$user_id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      if ($result->num_rows==0) return;
      $row = $result->fetch_assoc();
      $row_user_table['user_email'] = trim_gks($row['email']);
      $row_user_table['fiscal_position_id'] = intval($row['fiscal_position_id']);
      $row_user_table['pricelist_id'] = intval($row['pricelist_id']);
      //$row_user_table['gks_sex] = trim_gks($row['']);
      $row_user_table['gks_lang'] = trim_gks($row['user_lang']);
      $row_user_table['eponimia'] = trim_gks($row['eponimia']);
      $row_user_table['title'] = trim_gks($row['title']);
      $row_user_table['afm'] = trim_gks($row['afm']);
      $row_user_table['doy'] = trim_gks($row['doy']);
      $row_user_table['epaggelma'] = trim_gks($row['epaggelma']);
      if ($row['address_extra']==-1) {
        $row_user_table['ma_odos'] = trim_gks($row['odos']);
        $row_user_table['ma_arithmos'] = trim_gks($row['arithmos']);
        $row_user_table['ma_orofos'] = trim_gks($row['orofos']);
        $row_user_table['ma_perioxi'] = trim_gks($row['perioxi']);
        $row_user_table['ma_poli'] = trim_gks($row['poli']);
        $row_user_table['ma_tk'] = trim_gks($row['tk']); 
        $row_user_table['ma_country_id'] = intval($row['country_id']);
        $row_user_table['ma_nomos_id'] = intval($row['nomos_id']);
        $row_user_table['ma_latitude'] = floatval($row['map_latitude']);
        $row_user_table['ma_longitude'] = floatval($row['map_longitude']);
      }
      $row_user_table['genisi_date'] = trim_gks($row['birthday']);
      $row_user_table['first_name'] = trim_gks($row['first_name']);
      $row_user_table['last_name'] = trim_gks($row['last_name']);
      
      $row_user_table['mobile']= trim_gks($row['mobile']);
      $row_user_table['phone_home']= trim_gks($row['phone']);
      $row_user_table['user_url']= trim_gks($row['web']);
      
      break;       
      
    default:
      return;
  }
  
  
  $need_update=false;$update_wp_users=array();$update_gks_users=array();
  
  if ($row_user_exist['user_email']=='' and isset($row_user_table['user_email']) and $row_user_table['user_email']!='') {
    $sql="select ID from ".GKS_WP_TABLE_PREFIX."users where user_email='".$db_link->escape_string($row_user_table['user_email'])."'";
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    //echo $sql;
    if ($result->num_rows==0) {
      $need_update=true;
    
      $sql="update ".GKS_WP_TABLE_PREFIX."users set user_email='".$db_link->escape_string($row_user_table['user_email'])."' where ID=".$user_id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      
      
      $found=false;$has_primary=false;
      foreach ($row_user_exist['communication'] as $value) {
        if ($value['comm_value']==$row_user_table['user_email']) {
          $found=true;
        }
        if ($value['comm_type']=='email' and intval($value['comm_primary'])!=0) $has_primary=true;
      }
      if ($found==false) {
        $sql="insert into gks_users_communication (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        user_id,comm_type,comm_value,comm_descr,comm_primary
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        ".$user_id.",'email','".$db_link->escape_string($row_user_table['user_email'])."','',".($has_primary ? '0' : '1')."
        )";
        $result = $db_link->query($sql); 
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); } 
        
        $row_user_exist['communication'][]=array(
          'comm_type' => 'email',
          'comm_value' => $row_user_table['user_email'],
          'comm_primary' => ($has_primary ? 0 : 1),
        );
      }
    }
  }

  if ($row_user_exist['user_url']=='' and isset($row_user_table['user_url']) and $row_user_table['user_url']!='') {
    $need_update=true;
    $sql="update ".GKS_WP_TABLE_PREFIX."users set user_url='".$db_link->escape_string($row_user_table['user_url'])."' where ID=".$user_id;
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
    
    
    $found=false;$has_primary=false;
    foreach ($row_user_exist['communication'] as $value) {
      if ($value['comm_value']==$row_user_table['user_url']) {
        $found=true;
      }
      if ($value['comm_type']=='url' and intval($value['comm_primary'])!=0) $has_primary=true;
    }
    if ($found==false) {
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      user_id,comm_type,comm_value,comm_descr,comm_primary
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$user_id.",'url','".$db_link->escape_string($row_user_table['user_url'])."','',".($has_primary ? '0' : '1')."
      )";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      
      $row_user_exist['communication'][]=array(
        'comm_type' => 'url',
        'comm_value' => $row_user_table['user_url'],
        'comm_primary' => ($has_primary ? 0 : 1),
      );
    }
  }
    
  if ($row_user_exist['first_name']=='' and isset($row_user_table['first_name']) and $row_user_table['first_name']!='') {
    $need_update=true;
    update_user_meta( $user_id, 'first_name', $row_user_table['first_name']);
  }
  if ($row_user_exist['last_name']=='' and isset($row_user_table['last_name']) and $row_user_table['last_name']!='') {
    $need_update=true;
    update_user_meta( $user_id, 'last_name', $row_user_table['last_name']);
  }
  if ($row_user_exist['mobile']=='' and isset($row_user_table['mobile']) and $row_user_table['mobile']!='') {
    $need_update=true;
    update_user_meta( $user_id, 'mobile', $row_user_table['mobile']);
    $found=false;$has_primary=false;
    foreach ($row_user_exist['communication'] as $value) {
      if ($value['comm_value']==$row_user_table['mobile']) {
        $found=true;
      }
      if ($value['comm_type']=='phone' and intval($value['comm_primary'])!=0) $has_primary=true;
    }
    if ($found==false) {
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      user_id,comm_type,comm_value,comm_descr,comm_primary
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$user_id.",'phone','".$db_link->escape_string($row_user_table['mobile'])."','',".($has_primary ? '0' : '1')."
      )";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      
      $row_user_exist['communication'][]=array(
        'comm_type' => 'phone',
        'comm_value' => $row_user_table['mobile'],
        'comm_primary' => ($has_primary ? 0 : 1),
      );
    }
  }

  if ($row_user_exist['phone_home']=='' and isset($row_user_table['phone_home']) and $row_user_table['phone_home']!='') {
    $need_update=true;
    $update_gks_users[]="phone_home='".$db_link->escape_string($row_user_table['phone_home'])."'";
    $found=false;$has_primary=false;
    foreach ($row_user_exist['communication'] as $value) {
      if ($value['comm_value']==$row_user_table['phone_home']) {
        $found=true;
      }
      if ($value['comm_type']=='phone' and intval($value['comm_primary'])!=0) $has_primary=true;
    }
    if ($found==false) {
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      user_id,comm_type,comm_value,comm_descr,comm_primary
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$user_id.",'phone','".$db_link->escape_string($row_user_table['phone_home'])."','',".($has_primary ? '0' : '1')."
      )";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      
      $row_user_exist['communication'][]=array(
        'comm_type' => 'phone',
        'comm_value' => $row_user_table['phone_home'],
        'comm_primary' => ($has_primary ? 0 : 1),
      );
    }
  }
  
  if ($row_user_exist['fiscal_position_id']==0 and isset($row_user_table['fiscal_position_id']) and $row_user_table['fiscal_position_id']!=0) {
    $update_wp_users[]="fiscal_position_id=".$row_user_table['fiscal_position_id'];
  }
  if ($row_user_exist['pricelist_id']==0 and isset($row_user_table['pricelist_id']) and $row_user_table['pricelist_id']!=0) {
    $update_wp_users[]="pricelist_id=".$row_user_table['pricelist_id'];
  }
  if ($row_user_exist['gks_sex']==0 and isset($row_user_table['gks_sex']) and $row_user_table['gks_sex']!=0) {
    $update_wp_users[]="gks_sex=".$row_user_table['gks_sex'];
  }
  if ($row_user_exist['gks_lang']=='' and isset($row_user_table['gks_lang']) and $row_user_table['gks_lang']!='') {
    $update_wp_users[]="gks_lang='".$db_link->escape_string($row_user_table['gks_lang'])."'";
  }
  
  if ($row_user_exist['eponimia']=='' and isset($row_user_table['eponimia']) and $row_user_table['eponimia']!='') {
    $update_gks_users[]="eponimia='".$db_link->escape_string($row_user_table['eponimia'])."'";
  }
  if ($row_user_exist['title']=='' and isset($row_user_table['title']) and $row_user_table['title']!='') {
    $update_gks_users[]="title='".$db_link->escape_string($row_user_table['title'])."'";
  }
  if ($row_user_exist['afm']=='' and isset($row_user_table['afm']) and $row_user_table['afm']!='') {
    $update_gks_users[]="afm='".$db_link->escape_string($row_user_table['afm'])."'";
  }
  if ($row_user_exist['doy']=='' and isset($row_user_table['doy']) and $row_user_table['doy']!='') {
    $update_gks_users[]="doy='".$db_link->escape_string($row_user_table['doy'])."'";
  }
  if ($row_user_exist['epaggelma']=='' and isset($row_user_table['epaggelma']) and $row_user_table['epaggelma']!='') {
    $update_gks_users[]="epaggelma='".$db_link->escape_string($row_user_table['epaggelma'])."'";
  }
  if ($row_user_exist['ma_odos']=='' and isset($row_user_table['ma_odos']) and $row_user_table['ma_odos']!='') {
    $update_gks_users[]="ma_odos='".$db_link->escape_string($row_user_table['ma_odos'])."'";
  }
  if ($row_user_exist['ma_arithmos']=='' and isset($row_user_table['ma_arithmos']) and $row_user_table['ma_arithmos']!='') {
    $update_gks_users[]="ma_arithmos='".$db_link->escape_string($row_user_table['ma_arithmos'])."'";
  }
  
  if ($row_user_exist['ma_orofos']=='' and isset($row_user_table['ma_orofos']) and $row_user_table['ma_orofos']!='') {
    $update_gks_users[]="ma_orofos='".$db_link->escape_string($row_user_table['ma_orofos'])."'";
  }
  if ($row_user_exist['ma_perioxi']=='' and isset($row_user_table['ma_perioxi']) and $row_user_table['ma_perioxi']!='') {
    $update_gks_users[]="ma_perioxi='".$db_link->escape_string($row_user_table['ma_perioxi'])."'";
  }
  if ($row_user_exist['ma_poli']=='' and isset($row_user_table['ma_poli']) and $row_user_table['ma_poli']!='') {
    $update_gks_users[]="ma_poli='".$db_link->escape_string($row_user_table['ma_poli'])."'";
  }
  if ($row_user_exist['ma_tk']=='' and isset($row_user_table['ma_tk']) and $row_user_table['ma_tk']!='') {
    $update_gks_users[]="ma_tk='".$db_link->escape_string($row_user_table['ma_tk'])."'";
  }
  if ($row_user_exist['ma_country_id']==0 and isset($row_user_table['ma_country_id']) and $row_user_table['ma_country_id']!=0) {
    $update_gks_users[]="ma_country_id=".$row_user_table['ma_country_id'];
  }
  if ($row_user_exist['ma_nomos_id']==0 and isset($row_user_table['ma_nomos_id']) and $row_user_table['ma_nomos_id']!=0) {
    $update_gks_users[]="ma_nomos_id=".$row_user_table['ma_nomos_id'];
  }
  
  if ($row_user_exist['ma_latitude']==0 and $row_user_exist['ma_longitude']==0 and 
      isset($row_user_table['ma_latitude']) and isset($row_user_table['ma_longitude']) and 
      $row_user_table['ma_latitude']!=0 and $row_user_table['ma_longitude']!=0) {
    $update_gks_users[]="ma_latitude=". number_format($row_user_table['ma_latitude'] ,10,'.','');
    $update_gks_users[]="ma_longitude=".number_format($row_user_table['ma_longitude'],10,'.','');
  }  
  
  if ($row_user_exist['genisi_date']=='' and isset($row_user_table['genisi_date']) and $row_user_table['genisi_date']!='') {
    $update_gks_users[]="genisi_date='".$db_link->escape_string($row_user_table['genisi_date'])."'";
  }
  
  if (count($update_wp_users)>0) {
    $need_update=true;
    $sql="update ".GKS_WP_TABLE_PREFIX."users set ".implode(',',$update_wp_users).",
    update_from_gks=1 where ID=".$user_id;  
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
  }
  if (count($update_gks_users)>0) {
    $need_update=true;
    $sql="update gks_users set ".implode(',',$update_gks_users).",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where user_id=".$user_id;  
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
  }
    
    
  if ($need_update) {
    gks_user_update_comm_search($user_id);
    gks_user_update_dav($user_id,false);
    
    //echo '<pre>';print_r($row_user_exist);print_r($row_user_table);die();
    
  }
  
  
  //echo '<pre>'.$sql;die();
  
  //echo '<pre>ggggg';die();
  
}

function gks_qr_code_generate($codeContents, $mytype='url') {
  //https://phpqrcode.sourceforge.net/examples/index.php?example=020
  
  include_once('vendor_inc/phpqrcode/qrlib.php');

  $fileName = '001_qr_code_'.$mytype.'_'.md5($codeContents).'_gks_v1.png';
  
  $pngAbsoluteFilePath = GKS_QR_SAVE_DIR.$fileName;
  $urlRelativeFilePath = GKS_SITE_URL.'my/uploads/qr_codes/'.$fileName;
  
  // generating
  if (!file_exists($pngAbsoluteFilePath)) {
    //QRcode::png($codeContents, $pngAbsoluteFilePath);
    QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 4, 0);

//    how to configure pixel "zoom" factor
//    QRcode::png($codeContents, $tempDir.'007_1.png', QR_ECLEVEL_L, 1);
//    QRcode::png($codeContents, $tempDir.'007_2.png', QR_ECLEVEL_L, 2);
//    QRcode::png($codeContents, $tempDir.'007_3.png', QR_ECLEVEL_L, 3);
//    QRcode::png($codeContents, $tempDir.'007_4.png', QR_ECLEVEL_L, 4);

//    frame config values below 4 are not recomended !!!
//    QRcode::png($codeContents, $tempDir.'008_4.png', QR_ECLEVEL_L, 3, 4);  
//    QRcode::png($codeContents, $tempDir.'008_6.png', QR_ECLEVEL_L, 3, 6);
//    QRcode::png($codeContents, $tempDir.'008_12.png', QR_ECLEVEL_L, 3, 10);
        
  } 
  
  if (file_exists($pngAbsoluteFilePath)) {
    return $urlRelativeFilePath;
  }
  
  //debug_mail(false,'error gks_qr_code',$codeContents.'||'.$mytype);
  return '';
  
}
function gks_barcode_generate($barcodetype,$codeContents, $mytype='barcode') {
  //https://test.easyfilesselection.com/my/_test/_barcode_test.php
  $ret=array('url'=>'', 'error'=>'Cannot create barcode');
  $barcodetype2=strtolower($barcodetype);
  $barcodetype2=str_replace('+','plus',$barcodetype2);
  $barcodetype2=str_replace(',','_',$barcodetype2);
  
  $fileName = '002_'.$barcodetype2.'_'.$mytype.'_'.md5($codeContents).'_gks_bv1.png';
  $pngAbsoluteFilePath = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/barcodes/';//GKS_QR_SAVE_DIR.$fileName;
  if (file_exists($pngAbsoluteFilePath)==false) {
    $res=@mkdir($pngAbsoluteFilePath,0755);
    if ($res==false) {
      //debug_mail(false,'error gks_barcode_generate create dir',$pngAbsoluteFilePath);
      $ret['error']='Cannot create directory '.$pngAbsoluteFilePath;
      return $ret;
    }
  }
  $pngAbsoluteFilePath.=$fileName;
  
  $urlRelativeFilePath = GKS_SITE_URL.'my/uploads/barcodes/'.$fileName;
  
  // generating
  if (!file_exists($pngAbsoluteFilePath)) {
    require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/vendor_inc/tc-lib-color/resources/autoload.php');
    require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/vendor_inc/tc-lib-barcode/resources/autoload.php');
    
    $barcode = new \Com\Tecnick\Barcode\Barcode();
    //$barcodeException = new \Com\Tecnick\Barcode\Exception;
    
    $linear = [
        'C128A',// => ['0123456789', 'CODE 128 A'],
        'C128B',// => ['0123456789', 'CODE 128 B'],
        'C128C',// => ['0123456789', 'CODE 128 C'],
        'C128',// => ['0123456789', 'CODE 128'],
        'C39E+',// => ['0123456789', 'CODE 39 EXTENDED + CHECKSUM'],
        'C39E',// => ['0123456789', 'CODE 39 EXTENDED'],
        'C39+',// => ['0123456789', 'CODE 39 + CHECKSUM'],
        'C39',// => ['0123456789', 'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9'],
        'C93',// => ['0123456789', 'CODE 93 - USS-93'],
        'CODABAR',// => ['0123456789', 'CODABAR'],
        'CODE11',// => ['0123456789', 'CODE 11'],
        'EAN13',// => ['5201219046154', 'EAN 13'],
        'EAN2',// => ['12', 'EAN 2-Digits UPC-Based Extension'],
        'EAN5',// => ['12345', 'EAN 5-Digits UPC-Based Extension'],
        'EAN8',// => ['1234567', 'EAN 8'],
        'I25+',// => ['0123456789', 'Interleaved 2 of 5 + CHECKSUM'],
        'I25',// => ['0123456789', 'Interleaved 2 of 5'],
        'IMB',// => ['01234567094987654321-01234567891', 'IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200'],
        'IMBPRE',// => ['AADTFFDFTDADTAADAATFDTDDAAADDTDTTDAFADADDDTFFFDDTTTADFAAADFTDAADA', 'IMB pre-processed'],
        'KIX',// => ['0123456789', 'KIX (Klant index - Customer index)'],
        'MSI+',// => ['0123456789', 'MSI + CHECKSUM (modulo 11)'],
        'MSI',// => ['0123456789', 'MSI (Variation of Plessey code)'],
        'PHARMA2T',// => ['0123456789', 'PHARMACODE TWO-TRACKS'],
        'PHARMA',// => ['0123456789', 'PHARMACODE'],
        'PLANET',// => ['0123456789', 'PLANET'],
        'POSTNET',// => ['0123456789', 'POSTNET'],
        'RMS4CC',// => ['0123456789', 'RMS4CC (Royal Mail 4-state Customer Bar Code)'],
        'S25+',// => ['0123456789', 'Standard 2 of 5 + CHECKSUM'],
        'S25',// => ['0123456789', 'Standard 2 of 5'],
        'UPCA',// => ['72527273070', 'UPC-A'],
        'UPCE',// => ['725277', 'UPC-E'],
    ];
    $square = [
        'LRAW',// => ['0101010101', '1D RAW MODE (comma-separated rows of 01 strings)'],
        'SRAW',// => ['0101,1010', '2D RAW MODE (comma-separated rows of 01 strings)'],
        'AZTEC',// => ['ABCDabcd01234', 'AZTEC (ISO/IEC 24778:2008)'],
        'AZTEC,50,A,A',// => ['ABCDabcd01234', 'AZTEC (ISO/IEC 24778:2008)'],
        'PDF417',// => ['0123456789', 'PDF417 (ISO/IEC 15438:2006)'],
        'QRCODE',// => ['0123456789', 'QR-CODE'],
        'QRCODE,H,ST,0,0',// => ['abcdefghijklmnopqrstuvwxy0123456789', 'QR-CODE WITH PARAMETERS'],
        'DATAMATRIX',// => ['0123456789', 'DATAMATRIX (ISO/IEC 16022) SQUARE'],
        'DATAMATRIX,R',// => ['0123456789012345678901234567890123456789', 'DATAMATRIX Rectangular (ISO/IEC 16022) RECTANGULAR'],
        'DATAMATRIX,S,GS1',// => [chr(232) . '01095011010209171719050810ABCD1234' . chr(232) . '2110', 'GS1 DATAMATRIX (ISO/IEC 16022) SQUARE GS1'],
        'DATAMATRIX,R,GS1',// => [chr(232) . '01095011010209171719050810ABCD1234' . chr(232) . '2110', 'GS1 DATAMATRIX (ISO/IEC 16022) RECTANGULAR GS1'],
    ];  
    //example
    //linear
    //$bobj = $barcode->getBarcodeObj($type, $code[0], -3, -30, 'black', [0, 0, 0, 0]);
    
    //square
    //$bobj = $barcode->getBarcodeObj($type, $code[0], -4, -4, 'black', [0, 0, 0, 0]);
    try {
      if (in_array($barcodetype,$linear)) {
        try {
          $bobj = $barcode->getBarcodeObj(
            $barcodetype,                     // barcode type and additional comma-separated parameters
            $codeContents,             // data string to encode
            -3,                        // bar width (use absolute or negative value as multiplication factor)
            -50,                       // bar height (use absolute or negative value as multiplication factor)
            'black',                   // foreground color
            array(0, 0, 0, 0)          // padding (use absolute or negative values as multiplication factors)
          )->setBackgroundColor('white'); // background color
//        } catch (\Com\Tecnick\Barcode\Exception $e) {
//          $ret['error']=$e->getMessage();
//          return $ret;
        } catch (TypeError $e) {
          $ret['error']=$e->getMessage();
          return $ret;
        } catch (Exception $e) {
          $ret['error']=$e->getMessage();
          return $ret;
        }
        
      } else if (in_array($barcodetype,$square)) {
        try {
          $bobj = $barcode->getBarcodeObj(
            $barcodetype,                     // barcode type and additional comma-separated parameters
            $codeContents,             // data string to encode
            -4,                        // bar width (use absolute or negative value as multiplication factor)
            -4,                       // bar height (use absolute or negative value as multiplication factor)
            'black',                   // foreground color
            array(0, 0, 0, 0)          // padding (use absolute or negative values as multiplication factors)
          )->setBackgroundColor('white'); // background color
        } catch (TypeError $e) {
          $ret['error']=$e->getMessage();
          return $ret;
        } catch (Exception $e) {
          $ret['error']=$e->getMessage();
          return $ret;
        }
      
      } else {
        //debug_mail(false,'error gks_barcode type agnosto',$barcodetype.'||'.$codeContents.'||'.$mytype);
        $ret['error']='Barcode type not supported';
        return $ret;       
      }
      
      $res=@file_put_contents($pngAbsoluteFilePath,$bobj->getPngData());
    } catch (Exception $e) {
      $ret['error']=$e->getMessage();
      return $ret;      
    } finally {
      
    }
    
        
  } 
  
  if (file_exists($pngAbsoluteFilePath)) {
    $ret['url']=$urlRelativeFilePath;
    $ret['error']='';
    return $ret;
  }
  
  //debug_mail(false,'error gks_barcode',$barcodetype.'||'.$codeContents.'||'.$mytype);
  return $ret;
  
}
function gks_format_serial_number($s) {
  if (empty($s)) return '';
  if ($s==null) return '';
  if (strlen($s)!=16) return '';
  return substr($s,0,4).' '.substr($s,4,4).' '.substr($s,8,4).' '.substr($s,12,4);
}

function gks_license_get_status() {
  global $db_link;
  global $GKS_ERP_APP_PURCHASE_CODE;
  global $GKS_CACHE_DB_VER;
  global $gks_cache_version;
  
  
  $sql="select * from gks_settings where mykey='GKS_ERP_APP_PURCHASE_DATA'";
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$response);return $return;}
  $send_gks_erp_hashmd5key=false;
  if ($result->num_rows == 0) $send_gks_erp_hashmd5key=true;
    
  
  //echo '<pre>'.$_SERVER['HTTP_HOST']."\n".GKS_SITE_URL;die();  
  $return = array('success' => false, 'message' => 'general error', 'data' => []);

  $headers = array(
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: gks ERP/2024',
  );
  
  $url='https://tools.gks.gr/my/api/';
  $mypost=array();
  $mypost['HTTP_HOST']=$_SERVER['HTTP_HOST'];
  $mypost['GKS_SITE_URL']=GKS_SITE_URL;
  $mypost['admin_email']=get_bloginfo('admin_email');
  $mypost['cmd']='check_status';
  
  
  
  $rand1=rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
  $mypost['rand1']=$rand1;
  $mypost['semd5']=$semd5=md5($rand1.$mypost['GKS_SITE_URL'].$rand1.$mypost['HTTP_HOST'].$rand1. GKS_ERP_HASHMD5KEY04);

  $mypost['appver']=$GKS_CACHE_DB_VER.'.'.$gks_cache_version;
  $mypost['time']=date('Y-m-d H:i:s');
  $mypost['winver']=php_uname();
  $mypost['arc']=php_uname('m');
  $mypost['phpver']=phpversion();
  $mypost['hdwd']=intval(disk_free_space('.')/1024/1024);
  $mypost['pcname']=gethostname();
  $mypost['pcusername']=$_SERVER['USER']; //get_current_user();

  if ($send_gks_erp_hashmd5key) {
    $mypost['GKS_ERP_HASHMD5KEY02']=GKS_ERP_HASHMD5KEY02;
    $mypost['GKS_ERP_HASHMD5KEY04']=GKS_ERP_HASHMD5KEY04;
    $mypost['GKS_ERP_HASHMD5KEY05']=GKS_ERP_HASHMD5KEY05;
  }

  //echo $url;die();
  $mypostdata=json_encode($mypost);
  //echo '<pre>'.$mypostdata;die();
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
  $response = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close($ch);

  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/license_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/license_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);

  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code;
    debug_mail(false,$return['message'],$response);return $return;}

  //return $response;
  
  
  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    $return['message']=gks_lang('Σφάλμα δεδομένων').' (3) HTTP Code:'.$gks_curl_http_code.' Response: '.$response;
    debug_mail(false,$return['message'],$response);return $return;}

  if (is_array($response_array)==false or 
      isset($response_array['success'])==false or 
      $response_array['success']==false) {
  
    $return['success']=false;
    $return['message']=base64_decode($response_array['message']);
    return $return;
  }
  
  $GKS_ERP_APP_PURCHASE_CODE=$response_array['data'];
  
  $temp=json_encode($GKS_ERP_APP_PURCHASE_CODE);
  
  $sql="replace into gks_settings (mykey,myvalue) values ('GKS_ERP_APP_PURCHASE_DATA','".$db_link->escape_string($temp)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,'error sql',$sql);return $return;}
    


//  $return['success']=false;
//  $return['message']='111111111111'.$response;
//  $return['data']=$response_array;
//  return $return;
  
  $return['success']=true;
  $return['message']='OK';
  $return['data']=$response_array;
  
  return $return;
}



function gks_get_directory_size($path){
  
  $bytestotal = -1;
  $io = popen ( '/usr/bin/du -sb ' . $path, 'r' );
  $size = fgets ( $io, 4096);
  //echo '<pre>sss '.$path.'|'.$size;die();
  $size = substr ( $size, 0, strpos ( $size, "\t" ) );
  if (strlen($size)>0 and (intval($size).'')==$size) {
    $bytestotal=$size;
  }
  pclose ( $io );
  if ($bytestotal>4096) return $bytestotal;
  
  
  $bytestotal = 0;  
  $path = realpath($path);
  if($path!==false && $path!='' && file_exists($path)){
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
        $bytestotal += $object->getSize();
    }
  }
  return $bytestotal;
}

