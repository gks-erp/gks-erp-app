<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/




function gks_erp_app_run_command($params) {
  global $db_link;
  
  $return = array('success' => false, 'message' => 'generic error erp_app_run_command', 'data' => false);
  
  $id='';if (isset($params['id'])) $id=intval($params['id']); 
  $cmd='';if (isset($params['cmd'])) $cmd=trim_gks($params['cmd']); 

  if ($id <= 0) {
    debug_mail(false,'the id_erp_app is not set','');
    $return['message'] = gks_lang('Δεν έχει ορισθεί εφαρμογή gks ERP App Desktop');
    return $return;}

  
  $url_cmd='';
  if ($cmd=='run_command_alive') $url_cmd='/';
  else if ($cmd=='run_command_stats') $url_cmd='/stats2';
  else if ($cmd=='run_command_getdata') $url_cmd='/getdata?dummyfiles=all';
  else if ($cmd=='run_command_settings') $url_cmd='/settings';
  else if ($cmd=='run_command_local_printers') $url_cmd='/local_printers';
  else if ($cmd=='run_command_folder_exist') $url_cmd='/folder_exist';
  else if ($cmd=='run_command_print_file') $url_cmd='/print_file';
  else if ($cmd=='run_command_save_file') $url_cmd='/save_file';
  else if (startwith($cmd,'megeftpos_run_command')) $url_cmd='/megeftpos';
  else if (startwith($cmd,'cardlink_run_command')) $url_cmd='/cardlink_ecr2eftweb';
  else if ($cmd=='run_command_voiplocaldbphonebook') $url_cmd='/voiplocaldbphonebook';
  else if ($cmd=='run_command_voipaimtest') $url_cmd='/voipaimtest';
  else if ($cmd=='run_command_voipaimoriginatecall') $url_cmd='/voipaimoriginatecall';

  //echo '<pre>'.$url_cmd;die();

  

  if ($url_cmd=='') {
    debug_mail(false,'the url_cmd is not set','');
    $return['message'] = gks_lang('Δεν έχει ορισθεί η σωστή εντολή');
    return $return;}
  
  $sql ="SELECT * FROM gks_erp_app where id_erp_app = ".$id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return['message'] = 'sql error';
    return $return; }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εφαρμογή gks ERP App Desktop').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return['message'] = gks_lang('Δεν βρέθηκε η εφαρμογή gks ERP App Desktop').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
    return $return;  }
  $row = $result->fetch_assoc();
  $erp_app_token=trim_gks($row['erp_app_token']);
  $erp_app_secret=trim_gks($row['erp_app_secret']);
  if ($erp_app_secret=='') $erp_app_secret='0987390874029347093';
  

  
  $asset_id=0; if (isset($params['asset_id'])) $asset_id=intval($params['asset_id']);
  $api_call=''; if (isset($params['api_call'])) $api_call=trim_gks($params['api_call']);
  
  $id_eftpos_transaction=0;
  $eftpos_transaction_async=false;
  
  if ($url_cmd=='/megeftpos' and $asset_id>0 and $api_call!='') {
    $send_data_array=array();
    $megeftpos_ecr2eftweb_service_url='';
    switch ($api_call) {
      case 'ping_terminal':
        $input=array(
          'id_payment_acquirer_with' => 2, //megeftpos
          'transaction_type' => $api_call,
          'id_asset'=> $asset_id,
        );
        $ret=gks_eftpos_build_json_for_send($input);
        if ($ret['success']==false) {
          $return['message'] = $ret['message'];
          return $return; }
        
        $send_data_array=$ret['send_data_array'];
        $megeftpos_ecr2eftweb_service_url=$ret['megeftpos_ecr2eftweb_service_url'];
        if (isset($ret['id_eftpos_transaction'])) $id_eftpos_transaction=$ret['id_eftpos_transaction'];
        
        
        //print '<pre>sdfsdf sdf sdf s';print_r($ret);die();
        //print '<pre>';print_r($send_data_array);die();
        $eftpos_transaction_async=false;
        break; 
      case 'sale':
      case 'saleerp':
      case 'fullvoid':
      case 'fullvoiderp':
      case 'refund':
      case 'refunderp':
        $send_data_array=$params['send_data_array'];
        $megeftpos_ecr2eftweb_service_url=$params['megeftpos_ecr2eftweb_service_url'];
        if (isset($params['id_eftpos_transaction'])) $id_eftpos_transaction=$params['id_eftpos_transaction'];
        $eftpos_transaction_async=true;
           
      default:
        
        break;
    }
    
    if (GKS_Meg_EFT_POS_Driver_licenseKey=='' or GKS_Meg_EFT_POS_Driver_vatNumber=='') {
      debug_mail(false,'GKS_Meg_EFT_POS_Driver_licenseKey error1', '');
      $return['message'] = gks_lang('Δεν έχει ορισθεί η άδεια χρήσης για το Meg EFT/POS Driver');
      return $return;}
        
    $send_data='';
    if (count($send_data_array)>0) $send_data=json_encode($send_data_array);
    
    $params['postdata']['megeftpos_ecr2eftweb']['service_url']=$megeftpos_ecr2eftweb_service_url;
    $params['postdata']['megeftpos']['send_data']=$send_data;
    $params['postdata']['megeftpos']['api_call']=$api_call;
    $params['postdata']['id_eftpos_transaction']=$id_eftpos_transaction;
    $params['postdata']['async']=($eftpos_transaction_async ? '1' : '0');
    $params['postdata']['gks_server_url']=GKS_SITE_URL;
    $params['postdata']['license']=array(
      'Key' => GKS_Meg_EFT_POS_Driver_licenseKey,
      'vatNumber' => GKS_Meg_EFT_POS_Driver_vatNumber,
    );
    
    //echo '<pre>params erp_app send ';print_r($params);die();  
    //echo '<pre>';print_r($row_asset);die();  
        
  }
  
  if ($url_cmd=='/cardlink_ecr2eftweb' and $asset_id>0 and $api_call!='') {
    $send_data_array=array();
    $cardlink_ecr2eftweb_service_url='';
    switch ($api_call) {   
      case 'ping_terminal':
      case 'ping_service':
      case 'merchantinfo':
      case 'reconciliation':
        $input=array(
          'id_payment_acquirer_with' => 4, //cardlink
          'transaction_type' => $api_call,
          'id_asset'=> $asset_id,
        );
        $ret=gks_eftpos_build_json_for_send($input);
        if ($ret['success']==false) {
          $return['message'] = $ret['message'];
          return $return; }
        
        $send_data_array=$ret['send_data_array'];
        $cardlink_ecr2eftweb_service_url=$ret['cardlink_ecr2eftweb_service_url'];
        if (isset($ret['id_eftpos_transaction'])) $id_eftpos_transaction=$ret['id_eftpos_transaction'];
        
        
        //print '<pre>sdfsdf sdf sdf s';print_r($ret);die();
        //print '<pre>';print_r($send_data_array);die();
        $eftpos_transaction_async=false;
        break;

      case 'sale':
      case 'saleerp':
      case 'fullvoid':
      case 'fullvoiderp':
      case 'refund':
      case 'refunderp':
        $send_data_array=$params['send_data_array'];
        $cardlink_ecr2eftweb_service_url=$params['cardlink_ecr2eftweb_service_url'];
        if (isset($params['id_eftpos_transaction'])) $id_eftpos_transaction=$params['id_eftpos_transaction'];
        $eftpos_transaction_async=true;
        
        break;
      default:
        
        break;
    }
    
    $send_data='';
    if (count($send_data_array)>0) $send_data=json_encode($send_data_array);
    
    $params['postdata']['cardlink_ecr2eftweb']['service_url']=$cardlink_ecr2eftweb_service_url;
    $params['postdata']['cardlink_ecr2eftweb']['send_data']=$send_data;
    $params['postdata']['cardlink_ecr2eftweb']['api_call']=$api_call;
    $params['postdata']['id_eftpos_transaction']=$id_eftpos_transaction;
    $params['postdata']['async']=($eftpos_transaction_async ? '1' : '0');
    $params['postdata']['gks_server_url']=GKS_SITE_URL;
    
            
    //echo '<pre>params erp_app send ';print_r($params);die();  
    //echo '<pre>';print_r($row_asset);die();  
    
  }
  
  if ($url_cmd=='/voiplocaldbphonebook') {
    //echo '<pre>';print_r($params);die();

    if (isset($params['postdata'])==false) {
      $params['postdata']=array();
    }
    
    $user_ids=0; if (isset($params['user_ids'])) $user_ids=$params['user_ids']; //na einai array i integer
    //$user_ids=[1,2,3,4,5,6,7,8,9,19638,19643,19815,20417,31091];//delete me
    //$user_ids=0;//delete me
    //$user_ids=1;//delete me
    //$user_ids=20417;//delete me
    //print '<pre>';print_r($params);die();
    //print '<pre>';var_dump($user_ids);die();
    
    $params['postdata']['phonebook']='';
    

    //$params['postdata']['send1']
    $sql_comm="SELECT gks_users_communication.id_user_communication, 
    gks_users_communication.mydate_add, 
    gks_users_communication.mydate_edit, 
    gks_users_communication.user_id_add, 
    gks_users_communication.user_id_edit, 
    gks_users_communication.user_id, 
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
    gks_users_communication.comm_value, 
    gks_users_communication.comm_descr, 
    gks_users_communication.phone_fix
    FROM gks_users_communication 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_communication.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE gks_users_communication.comm_type='phone'
    and gks_users_communication.comm_value<>''
    and gks_users_communication.phone_fix<>''
    and ".GKS_WP_TABLE_PREFIX."users.ID>0
    and gks_users_communication.user_id ";
    if (is_array($user_ids) and count($user_ids)>0) {
      $sql_comm.=" in (".implode(',',$user_ids).")";
    } else if (is_numeric($user_ids)) {
      if ($user_ids==0) { 
        $sql_comm.=">0";//ola
      } else {
        $sql_comm.="=".$user_ids; //sigkekrimeno
      }
    } else {
      $sql_comm.='-1'; //tipota
    }
    $sql_comm.=" order by gks_users_communication.user_id, phone_fix";
    
    
    $result_comm = $db_link->query($sql_comm);
    if (!$result_comm) {
      debug_mail(false,'error sql',$sql_comm);
      $return['message'] = 'sql error';
      return $return; }
    $phonebook=[];        
    while ($row_comm = $result_comm->fetch_assoc()) {
      $row_comm['user_id']=intval($row_comm['user_id']);
      if (isset($phonebook[$row_comm['user_id']])==false) {
        $phonebook[$row_comm['user_id']]=array(
          'user_id'=>$row_comm['user_id'],
          //'gks_nickname'=>greeklish(trim_gks($row_comm['gks_nickname'])),
          'gks_nickname'=>trim_gks($row_comm['gks_nickname']),
          'phones'=>[],
        );
      }
      $phonebook[$row_comm['user_id']]['phones'][]=array(
        'comm_value'=>trim_gks($row_comm['comm_value']),
        'comm_descr'=>trim_gks($row_comm['comm_descr']),
        'phone_fix'=>trim_gks($row_comm['phone_fix']),
      );
    }
    
    $phonebook_temp=[];
    foreach ($phonebook as $value) {
      $phonebook_temp[]=$value;
    } 
    unset($phonebook);
    $params['postdata']['phonebook']=json_encode($phonebook_temp);
    $params['postdata']['phonebook_all_users']=0;
    if (is_numeric($user_ids) and $user_ids==0) {
      $params['postdata']['phonebook_all_users']=1;
    }
 
    
    //echo '<pre>';print_r($params['postdata']['phonebook']);die();  


  }
  
  if ($url_cmd=='/voipaimtest') {
    if (isset($params['postdata'])==false) {
      $params['postdata']=array();
    }    
  }
  if ($url_cmd=='/voipaimoriginatecall') {
    if (isset($params['extension'])==false or trim($params['extension'])=='') {
      $return['message'] = gks_lang('Δεν βρέθηκε ο εσωτερικός αριθμός τηλεφώνου');
      return $return; }      
    if (isset($params['phone'])==false or trim($params['phone'])=='') {
      $return['message'] = gks_lang('Δεν βρέθηκε το τηλέφωνο που θα γίνει η κλήση');
      return $return; }
              
    if (isset($params['postdata'])==false) {
      $params['postdata']=array();
    }   
    $params['postdata']['extension']=$params['extension'];
    $params['postdata']['phone']=$params['phone'];
  }  
   
  
  $public_url='';
  if ($row['erp_app_url']=='frp') {
    if (trim_gks($row['erp_app_token'])!='') {
      $public_url='http://'.$row['erp_app_token'].GKS_PROXY['DOMAIN_BASE_NAME'].':'.GKS_PROXY['VPORT'];
    }
  } else {
    if (trim_gks($row['erp_app_url'])!='' and $row['erp_app_port']>0) {
      $public_url='http://'.$row['erp_app_url'].':'.$row['erp_app_port'];
    }
  }
  if ($public_url=='') {
    debug_mail(false,'the public_url is not set','');
    $return['message'] = gks_lang('Δεν έχει ορισθεί το Public URL');
    return $return;}  

  $fileurl=$public_url.$url_cmd;
  
  $opts = array(
    'http'=>array(
      'timeout' => 100,  //10 Seconds  
      'method'=>"GET",
      'header'=>"Accept-language: en\r\n"
    )
  );
  
  $context = stream_context_create($opts);
  
  
  
  //$file = @file_get_contents($fileurl, false, $context);
  
  $postdata='';
  if (isset($params['postdata']) and is_array($params['postdata'])) {
    $rnd1s=rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
    $send1= md5($rnd1s . $rnd1s . $erp_app_token . $rnd1s .$erp_app_secret.  GKS_ERP_HASHMD5KEY13);
    $params['postdata']['rnd1s']=$rnd1s;
    $params['postdata']['send1']=$send1;
    
    $postdata = http_build_query($params['postdata']);
    
  }
  
  //print '<pre>';print_r($params);die();
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$fileurl);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $file = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  
  curl_close ($ch);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  
  //echo '<pre>ffffffffffff '.$gks_curl_http_code."\n".$file;die();
  $extra_error_message='';
  if ($gks_curl_http_code==0) { //HTTP Host not found
    $extra_error_message='HTTP Host not found';
    $file='';
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    $extra_error_message='HTTP 404 REQUEST not found';
    $file='';
  } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
    $extra_error_message='HTTP 400 BAD_REQUEST';
    $file='';
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $extra_error_message='HTTP 401 UNAUTHORIZED';
    $file='';
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    $extra_error_message='Unkown HTTP code: '.$gks_curl_http_code;
    $file='';
  } 
  
  

  
  if ($file == '') {
    //error connection
    if ($id_eftpos_transaction>0) {
      $sql="update gks_cardlink_transaction set trans_status='fail',
      mymessage='".$db_link->escape_string(gks_lang('Σφάλμα κατά την επικοινωνία με την εφαρμογή gks ERP App Desktop'))."' 
      where eftpos_transaction_id=".$id_eftpos_transaction;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);$return['message'] = 'sql error';return $return; }
    }
    debug_mail(false,'gks ERP App Desktop error1', $fileurl.' '.$extra_error_message);
    $return['message'] = gks_lang('Σφάλμα κατά την επικοινωνία με την εφαρμογή gks ERP App Desktop').'<br>'.
    gks_lang('Παρακαλώ δοκιμάστε αργότερα').'<br>'.$extra_error_message;
    return $return;}
  
  if ($url_cmd=='/stats2') {
    $file=str_replace('<pre>', '', $file);
    $file=str_replace('</pre>', '', $file);
    $file=str_replace("\r\n", '<br>', $file);
  } else if ($url_cmd=='/settings') {
    $file=htmlentities($file, ENT_QUOTES);
  } else if ($url_cmd=='/local_printers') {
    $local_printers=base64_decode($file);
    $erp_app_local_printers=json_decode($local_printers,true);
    if ($erp_app_local_printers === null && json_last_error() !== JSON_ERROR_NONE) {
      $erp_app_local_printers=false;
    } else {
      if ($erp_app_local_printers!==false and is_array($erp_app_local_printers)) {
        $sql="update gks_erp_app set 
        erp_app_local_printers ='".$db_link->escape_string(serialize($erp_app_local_printers))."'
        where id_erp_app=".$id;
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return['message'] = 'sql error';
          return $return; }   
        $file='<ol>';
        foreach ($erp_app_local_printers as $value) {
          $file.='<li>'.$value.'</li>';
        } 
        $file.='</ol>';      
      }
    }
  } else if ($url_cmd=='/folder_exist') {
    $response=json_decode($file,true);
    
    if ($response === null && json_last_error() !== JSON_ERROR_NONE) {
      debug_mail(false,'response json_last_error',$file);
      $return['message']=gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (2) '.$file;
      return $return;}
    if (is_array($response)==false or isset($response['success'])==false or isset($response['message'])==false) {
      debug_mail(false,'response array error json_last_error',$file);
      $return['message']=gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (3)';
      return $return;}
    if ($response['success']==false) {
      debug_mail(false,'response array error json_last_error',print_r($response,true));
      $return['message']=base64_decode($response['message']);
      return $return;}      
     
      
    // einai ok edo

  } else if ($url_cmd=='/megeftpos') {
    $response=json_decode($file,true);
    
    if ($response === null && json_last_error() !== JSON_ERROR_NONE) {
      if ($id_eftpos_transaction>0) {
        $sql="update gks_megeftpos_transaction set trans_status='fail',
        mymessage='".$db_link->escape_string(gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (2)')."' 
        where eftpos_transaction_id=".$id_eftpos_transaction;
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);$return['message'] = 'sql error';return $return; }
      }
      debug_mail(false,'response json_last_error',$file);
      $return['message']=gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (2) '.$file;
      return $return;}
    if (is_array($response)==false or isset($response['success'])==false or  isset($response['message'])==false) {
      if ($id_eftpos_transaction>0) {
        $sql="update gks_megeftpos_transaction set trans_status='fail',
        mymessage='".$db_link->escape_string(gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (3)')."' 
        where eftpos_transaction_id=".$id_eftpos_transaction;
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);$return['message'] = 'sql error';return $return; }
      }
      debug_mail(false,'response array error json_last_error',$file);
      $return['message']=gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (3)';
      return $return;}
    
    if ($response['success']==false) {
      if ($id_eftpos_transaction>0) {
        $sql="update gks_megeftpos_transaction set trans_status='fail',
        mymessage='".$db_link->escape_string($response['message'])."' 
        where eftpos_transaction_id=".$id_eftpos_transaction;
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);$return['message'] = 'sql error';return $return; }
      }
      
      debug_mail(false,'response array error json_last_error',print_r($response,true));
      $return['message']=base64_decode($response['message']);
      return $return;}  
    
//    $file='<pre>'.print_r(array(
//      'message' => base64_decode($response['message']),
//      'api_call' => $response['api_call'],
//      'send_data' => $response['send_data'],
//      'response_data' => $response['response_data'],
//      
//    ),true).'</pre>';
    
    $return['message']=base64_decode($response['message']);
    
    $file=$response['response_data'];
    
    if ($id_eftpos_transaction>0) {
      $sql="update gks_eftpos_transaction set 
      response_array='".$db_link->escape_string($file)."',
      transaction_status='".($eftpos_transaction_async ? 'async' : 'done') ."'
      where id_eftpos_transaction=".$id_eftpos_transaction."
      and transaction_status='draft'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return['message'] = 'sql error';
        return $return; }
        
      $sql="update gks_megeftpos_transaction set trans_status='send',mymessage='' where eftpos_transaction_id=".$id_eftpos_transaction;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);$return['message'] = 'sql error';return $return; }
      
      
    }
    
    
    
    
  } else if ($url_cmd=='/cardlink_ecr2eftweb') {
    $response=json_decode($file,true);
    
    if ($response === null && json_last_error() !== JSON_ERROR_NONE) {
      if ($id_eftpos_transaction>0) {
        $sql="update gks_cardlink_transaction set trans_status='fail',
        mymessage='".$db_link->escape_string(gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (2)')."' 
        where eftpos_transaction_id=".$id_eftpos_transaction;
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);$return['message'] = 'sql error';return $return; }
      }
      debug_mail(false,'response json_last_error',$file);
      $return['message']=gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (2) '.$file;
      return $return;}
    if (is_array($response)==false or isset($response['success'])==false or  isset($response['message'])==false) {
      if ($id_eftpos_transaction>0) {
        $sql="update gks_cardlink_transaction set trans_status='fail',
        mymessage='".$db_link->escape_string(gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (3)')."' 
        where eftpos_transaction_id=".$id_eftpos_transaction;
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);$return['message'] = 'sql error';return $return; }
      }
      debug_mail(false,'response array error json_last_error',$file);
      $return['message']=gks_lang('Η εφαρμογή gks ERP App Desktop επέστρεψε λάθος δεδομένα').' (3)';
      return $return;}
    
    if ($response['success']==false) {
      if ($id_eftpos_transaction>0) {
        $sql="update gks_cardlink_transaction set trans_status='fail',mymessage='".$db_link->escape_string($response['message'])."' where eftpos_transaction_id=".$id_eftpos_transaction;
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);$return['message'] = 'sql error';return $return; }
      }
      
      debug_mail(false,'response array error json_last_error',print_r($response,true));
      $return['message']=base64_decode($response['message']);
      return $return;}  
    
//    $file='<pre>'.print_r(array(
//      'message' => base64_decode($response['message']),
//      'api_call' => $response['api_call'],
//      'send_data' => $response['send_data'],
//      'response_data' => $response['response_data'],
//      
//    ),true).'</pre>';
    
    $return['message']=base64_decode($response['message']);
    
    $file=$response['response_data'];
    
    if ($id_eftpos_transaction>0) {
      $sql="update gks_eftpos_transaction set 
      response_array='".$db_link->escape_string($file)."',
      transaction_status='".($eftpos_transaction_async ? 'async' : 'done') ."'
      where id_eftpos_transaction=".$id_eftpos_transaction."
      and transaction_status='draft'";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return['message'] = 'sql error';
        return $return; }
        
      $sql="update gks_cardlink_transaction set trans_status='send',mymessage='' where eftpos_transaction_id=".$id_eftpos_transaction;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);$return['message'] = 'sql error';return $return; }
      
      
    }
  } else if ($url_cmd=='/voiplocaldbphonebook') {
    $response=json_decode($file,true);  
    if (is_array($response) and isset($response['success']) and isset($response['message'])) {
      if ($response['success']==false) {
        debug_mail(false,'response array error json_last_error',print_r($response,true));
        $return['message']=base64_decode($response['message']);
        return $return;}     
      $file=nl2br_gks(base64_decode($response['message']));
    } else {
      debug_mail(false,'response array error json_last_error',$file);
      $return['message']=($file);
      return $return;     
    }
  } else if ($url_cmd=='/voipaimtest') {
    $response=json_decode($file,true);  
    if (is_array($response) and isset($response['success']) and isset($response['message'])) {
      if ($response['success']==false) {
        debug_mail(false,'response array error json_last_error',print_r($response,true));
        $return['message']=base64_decode($response['message']);
        return $return;}     
      $file=nl2br_gks(base64_decode($response['message']));
    } else {
      debug_mail(false,'response array error json_last_error',$file);
      $return['message']=($file);
      return $return;     
    }
  } else if ($url_cmd=='/voipaimoriginatecall') {
    $response=json_decode($file,true);
    if (is_array($response) and isset($response['success']) and isset($response['message'])) {
      if ($response['success']==false) {
        debug_mail(false,'response array error json_last_error',print_r($response,true));
        $return['message']=base64_decode($response['message']);
        return $return;}     
      $file=nl2br_gks(base64_decode($response['message']));
    } else {
      debug_mail(false,'response array error json_last_error',$file);
      $return['message']=($file);
      return $return;     
    }
    //echo '<pre>';var_dump($response);die();
  }

  
  
  
  $return['data']=$file;
  $return['success']=true;
  $return['message']='OK';
  return $return;
  
}
function gks_erp_cron_curl_fnc($url) {
  
  $url = GKS_SITE_URL.'my/'.$url.'&cache='.time().rand(1000,9999).rand(1000,9999).rand(1000,9999);
  //echo $url;die();  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER,
    array(
      'accept: application/json',
      'Content-Type: application/json',
    )
  ); 
  curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100); //HERE MAGIC (We wait only 1ms on connection) Script waiting but (processing of send package to $curl is continue up to successful) so after 1ms we continue scripting and in background php continue already package to destiny. This is like apple on tree, we cut and go, but apple still fallow to destiny but we don't care what happened when fall down :) 
  curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // i'dont know just it works together read manual ;)
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  curl_close ($ch);
}