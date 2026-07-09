<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
$gks_eftpos_worldline_support_dcc=true;
//apo 100000001 ginetai -> 300000007 


function gks_eftpos_has_transaction_status_worldline($id) {
  if ($id===null) return '';
  $id=intval($id);
  switch ($id) {
    case 1: return 'PENDING';
    case 2: return 'SENT';
    case 3: return 'COMPLETED';
    case 101: return 'GKS_ERROR_SEND';
  }
  return 'ID '.$id;
}
//1: PENDING - Intent has been registered to the backend and is pending to be sent to the device
//2: SENT - Intent has been sent to the device
//3: COMPLETED - Intent has been successfully completed by the device and has registered the results

function gks_eftpos_has_transaction_result_worldline($id) {
  if ($id===null) return '';
  $id=intval($id);
  switch ($id) {
    case 2: return 'APPROVED';
    case 5: return 'UNKNOWN';
    case 6: return 'REJECTED';
    case 9: return 'ERROR';
    case 101: return 'GKS_ERROR_SEND';
    case 103: return 'CANCELLED';
  }
  return 'ID '.$id;
}
//////
///////*Intent result
//////The possible values for the intent result are:
//////1: APPROVED - The transaction has been completed and approved by the authorization system
//////2: DECLINED - The transaction has been completed and declined by the authorization system
//////3: CANCELLED - The transaction has been cancelled by the POS user before reaching completion
//////4: FAILED - The transaciton has failed to complete
//////5: UNKNOWN - The transaction result is unknown. Only possible if the device hasn't responded with results
//////6: BUSY - The transaction has failed because the POS is currently unavailable for transactions (either processing another transaction or under maintenance)
//////7: MAX_TRANSACTIONS - The POS device has reached its transaction limit for the specific batch. Batch closing should be performed on the device before continuing transactions
//////*/
//////
function gks_eftpos_has_transaction_type_worldline($id) {
  if ($id===null) return '';
  $id=intval($id);
  switch ($id) {
    case '100000001': return 'Regular purchase';
    case '100000002': return 'P2P';
    case '200000001': return 'Reversal';
    case '200000003': return 'Refund with RRN';
    case '200000004': return 'Refund with RRN and card tap';
    case '200000005': return 'Reversal with card tap';
    case '300000004': return 'Purchase with tip';
    case '300000005': return 'Installment';
    case '300000007': return 'Purchase that support DCC';
    case '300000008': return 'Pre-authorisation';
    case '300000009': return 'Pre-authorisation completion';
  }
  return 'ID '.$id;
}
//////  
function gks_eftpos_has_credentials_worldline($row_company) {
  $return=array('success' => false, 'message' => 'generic error');
  
  if (trim_gks($row_company['worldline_username'])=='') { // or trim_gks($row_company['worldline_password'])=='' or trim_gks($row_company['worldline_authorization_code'])=='' or trim_gks($row_company['worldline_x_api_key'])=='') {
    $return['message']=gks_lang('Δεν έχουν ορισθεί τα Διαπιστευτήρια πρόσβασης για την worldline στην εταιρεία').' '.$row_company['company_title'];
    debug_mail(false,$return['message'],print_r($row_company,true));return $return;
  }
  if (GKS_WORLDLINE_COM_API_PARTNER_ID=='' or GKS_WORLDLINE_COM_API_PARTNER_KEY=='') {
    $return['message']=gks_lang('Δεν έχουν ορισθεί τα Διαπιστευτήρια του συνεργάτη');
    debug_mail(false,$return['message'],print_r($row_company,true));return $return;
  }
  
  //echo '<pre>dddddd';print_r($row_company);die();
  return array('success' => true, 'message' => 'OK', 'data' => array(
    'worldline_username' => $row_company['worldline_username'],
    'worldline_password' => $row_company['worldline_password'],
    'worldline_authorization_code' => $row_company['worldline_authorization_code'],
    'worldline_x_api_key' => $row_company['worldline_x_api_key'],
  ));  
}
//////

function gks_eftpos_get_token_worldline($row_company) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');


  $id_company_eftpos=0;
  
    
  
  $sql="select * from gks_company_eftpos 
  where payment_acquirer_with_id=6
  and company_id=".$row_company['id_company']."
  and pc_token_id<>''";
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    $id_company_eftpos=intval($row['id_company_eftpos']);   
    if (time() < (strtotime($row['pc_token_expiration'])) and trim_gks($row['pc_token_id'])!='') {
      return array('success' => true, 'message' => 'OK', 'data' => array(
        'access_token' => $row['pc_token_id'],
      ));
    }
  }
    

  
  $headers = array(
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: gks ERP/2024',
  );

  $url=GKS_WORLDLINE_COM_API.'/auth';
  $mypost=array();
  $mypost['username']=GKS_WORLDLINE_COM_API_PARTNER_ID;
  $mypost['password']=GKS_WORLDLINE_COM_API_PARTNER_KEY;
  
  //echo $url;die();
  $mypostdata=json_encode($mypost);
  //echo '<pre>'.$mypostdata;die();
  
  $ch = curl_init($url);
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
  
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_auth_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_auth_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);

  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code;
    debug_mail(false,$return['message'],print_r($row_company,true));return $return;}

  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    $return['message']=gks_lang('Σφάλμα δεδομένων').' (3) '.$response;
    debug_mail(false,$return['message'],$response);return $return;}
  

  if (!(isset($response_array['token'])  and trim_gks($response_array['token'] )!='')) {
    $return['message']=gks_lang('Σφάλμα δεδομένων').' (5) '.$response;
    debug_mail(false,$return['message'],$response);return $return;}
    
  $access_token=trim_gks($response_array['token']);
  $pc_token_expiration=date('Y-m-d H:i:s',time() + 1*60*60 - 10*60); // 1 ora -0 lepta
  
  
  if ($id_company_eftpos==0) {
    $sql="insert into gks_company_eftpos (
    user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
    company_id,
    company_sub_id,
    payment_acquirer_with_id,
    pc_token_id,
    pc_token_expiration

    ) values (
    ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
    ".$row_company['id_company'].",
    0,
    6,
    '".$db_link->escape_string($access_token)."',
    '".$pc_token_expiration."'
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      $return['message']='error sql';
      debug_mail(false,$return['message'],$sql);return $return;}

      
  } else {
    $sql="update gks_company_eftpos set
    pc_token_id='".$db_link->escape_string($access_token)."',
    pc_token_expiration='".$pc_token_expiration."',";
    $sql.="
    user_id_edit=".$my_wp_user_id.",
    mydate_edit=now(),
    myip='".$db_link->escape_string($gkIP)."'
    where id_company_eftpos=".$id_company_eftpos;
    $result = $db_link->query($sql);        
    if (!$result) {
      $return['message']='error sql';
      debug_mail(false,$return['message'],$sql);return $return;}

    
  }
  
  //echo '<pre>';print_r($response_array);die();
         
  return array('success' => true, 'message' => 'OK', 'data' => array(
    'access_token' => $access_token,
  ));  
}

function gks_eftpos_sales_request_worldline($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $gks_eftpos_worldline_support_dcc;
  
  $return=array('success' => false, 'message' => 'generic error');

/*trn_type:
Code	    Description	                    Сountry applicability
100000001	Regular purchase	              All
100000002	P2P	                            Ukraine, only 1 bank
200000001	Reversal	                      All
200000003	Refund with RRN	                All
200000004	Refund with RRN and card tap,	  All
200000005	Reversal with card tap	        Azerbaijan
300000004	Purchase with tip	              Greece, Slovakia
300000005	Installment	                    Greece
300000007	Purchase that support DCC	      Greece
300000008	Pre-authorisation	              Greece
300000009	Pre-authorisation completion	  Greece
*/

  
  
  $transaction_type=$data['transaction_type'];
  //echo '<pre>dataaaaaaa '."\n";print_r($data);die();

  $worldline_implementation ='serverapi'; //app2app or serverapi
  if (isset($data['worldline_implementation'])) {
    if (in_array($data['worldline_implementation'],['serverapi','app2app'])) {
      $worldline_implementation=$data['worldline_implementation'];
    }
  }
  //$worldline_implementation ='serverapi';

  if ($worldline_implementation=='serverapi') {
    $url=GKS_WORLDLINE_COM_API.'/v1/payment-intent';
    //echo '<pre>'.$url;die();
  
    $mypost=array();
    $mypost['merchant_bank_id']=GKS_WORLDLINE_COM_API_BANK_ID;
    $mypost['merchant_id_type']='merchant_email';
    $mypost['merchant_id']=$data['row_company']['worldline_username'];
    $mypost['app_language_code']='el';
    $mypost['merchant_terminal_id']=$data['terminalId'];
    $mypost['trn_amount']=number_format($data['amount'],2,'.','');
    $mypost['trn_amount_fix']=true;
    $mypost['trn_currency']='EUR';
  
    $mypost['trn_type']='100000001';
    if ($gks_eftpos_worldline_support_dcc) $mypost['trn_type']='300000007';
    if (in_array($transaction_type,['sale'])) {
      $mypost['trn_type']='100000001';
      if ($gks_eftpos_worldline_support_dcc) $mypost['trn_type']='300000007';
      if ($data['tipAmount']>0) {$mypost['trn_type']='300000004'; }
      else if ($data['installments']>1) $mypost['trn_type']='300000005'; 
    } else if (in_array($transaction_type,['saleerp'])) {
      $mypost['trn_type']='100000001';
      if ($gks_eftpos_worldline_support_dcc) $mypost['trn_type']='300000007';
      if ($data['tipAmount']>0) $mypost['trn_type']='300000004';
      else if ($data['installments']>1) $mypost['trn_type']='300000005'; 
    } else if (in_array($transaction_type,['refund'])) {
      $mypost['trn_type']='200000003';
    } else if (in_array($transaction_type,['refunderp'])) {
      $mypost['trn_type']='200000003';
    } else {
      debug_mail(false,gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type,'');
      return array('success' => false, 
      'message' => gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type, 
      'status' =>  gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type);    
    }

    $mypost['trn_receipt_send']=false;
    $mypost['app_receipt_screen']=true;
    $mypost['trn_reference_label']=$data['sessionId'];
    $mypost['remote_payment_activation_mode']=true;
    $mypost['cancel_last_attempt']=true;
    $mypost['trn_host_rrn']='';
    
  } else if ($worldline_implementation=='app2app') {

/*{
    "client_id": "5411e2ed-063a-11f0-959d-022df560c9d7",
    "client_secret": "ZGjbzko77pyckfBSbcKc1JcaSVvxLLH7DPL1U0UsdFbZPpvIlnoFA25zveGF1oR7VotnNp8ejH9RO87bywOQIsWeV4Wn1veXusZZpiuA6JvWQeYuerPUchmNbuwoq7PI",
    "UID": "1bce4a97-7444-4e41-85bf-0db0c93f6617",
    "login": "info@gks.gr",
    "check_field": "email",
    "data": {
        "language": "el",
        "edit": true,
        "receipt": true,
        "stay": true,
        "operation": "300000004",
        "currency": "EUR",
        "amount": "10.00",
        "rrn": "" 
    },
    "info_data": {
        "tipAmt": "2.34"
    }
}*/

    $url=GKS_WORLDLINE_COM_API_TOKEN.'/token/token.do';
    //echo '<pre>'.$url;die();
    
    $mypost=array();
    $mypost['client_id']=GKS_WORLDLINE_COM_API_PARTNER_ID;
    $mypost['client_secret']=GKS_WORLDLINE_COM_API_PARTNER_KEY;
    $mypost['UID']=GKS_WORLDLINE_COM_API_BANK_ID;
    $mypost['login']=$data['row_company']['worldline_username'];
    $mypost['check_field']='email';
    $mypost['data']=[];
    $mypost['data']['language']='el';
    $mypost['data']['edit']=false;
    $mypost['data']['receipt']=true;
    $mypost['data']['stay']=false;
    $mypost['data']['operation']='100000001';
    if ($gks_eftpos_worldline_support_dcc) $mypost['data']['operation']='300000007';
    
    if (in_array($transaction_type,['sale'])) {
      $mypost['data']['operation']='100000001';
      if ($gks_eftpos_worldline_support_dcc) $mypost['data']['operation']='300000007';
      if ($data['tipAmount']>0) {$mypost['data']['operation']='300000004'; }
      else if ($data['installments']>1) $mypost['data']['operation']='300000005'; 
    } else if (in_array($transaction_type,['saleerp'])) {
      $mypost['data']['operation']='100000001';
      if ($gks_eftpos_worldline_support_dcc) $mypost['data']['operation']='300000007';
      if ($data['tipAmount']>0) $mypost['data']['operation']='300000004';
      else if ($data['installments']>1) $mypost['data']['operation']='300000005'; 
    } else if (in_array($transaction_type,['refund'])) {
      $mypost['data']['operation']='200000003';
    } else if (in_array($transaction_type,['refunderp'])) {
      $mypost['data']['operation']='200000003';
    } else {
      debug_mail(false,gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type,'');
      return array('success' => false, 
      'message' => gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type, 
      'status' =>  gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type);    
    }
    
    $mypost['data']['currency']='EUR';
    $mypost['data']['amount']=number_format($data['amount'],2,'.','');
    $mypost['data']['rrn']='';
    
    $mypost['info_data']=[];
    $mypost['info_data']['custom_ref_label']=$data['sessionId'];
    
    if ($data['tipAmount']>0) $mypost['info_data']['tipAmt']=$data['tipAmount'];
    if ($data['installments']>1) $mypost['info_data']['installmentPeriod']=$data['installments'];

  }
  
  //print '<pre>';print_r($mypost);die();
  //print '<pre>';print_r($data['row_prev_eftpos']);die();
  
  if (in_array($transaction_type,['refund','refunderp','preauthcompletion','preauthcompletionerp'])) {
    
    $xxx_transaction_id=intval($data['row_prev_eftpos']['xxx_transaction_id']);
    $sql="select * from gks_worldline_transaction 
    where id_worldline_transaction>0
    and myStatus=3 and myResult=2
    and id_worldline_transaction=".$xxx_transaction_id;
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    if ($result->num_rows==0) {
      debug_mail(false,gks_lang('Δεν βρέθηκε η σχετική γονική εγγραφή').' (2)',$sql);
      return array('success' => false, 
      'message' => gks_lang('Δεν βρέθηκε η σχετική γονική εγγραφή').' (2)', 
      'status' =>  gks_lang('Δεν βρέθηκε η σχετική γονική εγγραφή').' (2)');}
    $row_prev_worldline = $result->fetch_assoc();
    
    if ($worldline_implementation=='serverapi') {
      $mypost['trn_host_rrn']=$data['row_prev_eftpos']['transactionId'];
    } else if ($worldline_implementation=='app2app') {
      $mypost['data']['rrn']=$data['row_prev_eftpos']['transactionId'];
    }
  }
  
  if ($data['seira_need_signature']) { 
    
    
    if ($worldline_implementation=='serverapi') {
      $mypost['external_data_key']=GKS_WORLDLINE_YLIDA_Key_ID;
      $mypost['externald_data_signature']= $data['aadeProviderSignature'];
      $mypost['external_data_validation']= $data['aadeProviderSignatureData'];

      if ($gks_eftpos_worldline_support_dcc and $mypost['trn_type']=='100000001') {
        $mypost['trn_type']='300000007';
      }
      
    } else if ($worldline_implementation=='app2app') {
      $mypost['info_data']['external_data_key']=GKS_WORLDLINE_YLIDA_Key_ID;
      $mypost['info_data']['externald_data_signature']=$data['aadeProviderSignature'];
      $mypost['info_data']['external_data_validation']=$data['aadeProviderSignatureData'];

      if ($gks_eftpos_worldline_support_dcc and $mypost['data']['operation']=='100000001') {
        $mypost['data']['operation']='300000007';
      }
    }
  }
  
  //echo '<pre>sssssssssssaaa ';print_r($data);die();
  //echo '<pre>sssssssssssaaa ';print_r($mypost);die();
  
  if ($worldline_implementation=='serverapi') {
    $headers = array(
      'Content-Type:application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
      'Authorization: Bearer '. $data['access_token'],
    );
    $mypost_trn_type=$mypost['trn_type'];
    $curl_request='PUT';
  } else if ($worldline_implementation=='app2app') {
    $headers = array(
      'Content-Type:application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
    );
    $mypost_trn_type=$mypost['data']['operation'];
    $curl_request='POST';
  }
    
  $mypostdata=json_encode($mypost);
  //echo '<pre>postargs '."\n";print_r($headers);print_r($mypost);print_r($data);die();
  
  
  
  
  $sql="insert into gks_worldline_transaction (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  intentId,
  myStatus,myResult,
  merchant_terminal_id,trn_type,trn_amount,trn_currency,
  worldline_implementation
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App','worldline Api',
  ".$data['id_eftpos_transaction'].",
  '',
  0,0,
  '".$db_link->escape_string($data['terminalId'])."',
  ".$mypost_trn_type.",
  ".$data['amount'].",
  '".$db_link->escape_string('EUR')."',
  '".$db_link->escape_string($worldline_implementation)."'
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  $id_worldline_transaction = $db_link->insert_id; 
  


  //echo '<pre>sssssssssssaaa ';print_r($mypost);die();

  
  $sql="update gks_eftpos_transaction set
  send_array='".$db_link->escape_string($mypostdata)."',
  xxx_transaction_id=".$id_worldline_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'data' => array());}  
  
  //echo '<pre>'.$url."\n".$data['access_token']."\n".$data['worldline_x_api_key'];die();
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $curl_request);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
  $response = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close($ch);
  

  //debug_mail(false,'response worldline',$response);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_sales_request_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_sales_request_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);

  //echo '<pre>'.$response;die();

  //to $response einai keno otan ginei epitixos
  $response_array=json_decode($response,true);

/*  
se serverapi: ean to app DEN einai anoixto
{"name":"Bad Gateway or Proxy Error",
"message":"Failed to send payment activation request",
"code":0,
"status":502}


se serverapi: arkei to app na einai anoixto
{"token":"Q1MvN8ly2UV2kmqJ5nXEC1En1Lr4RPbH"}   

se appp2app:   
{"status":0,"token":"LWozHqLaeeeByxQ8wLsvm9WsqWEwDm12"}
   
*/  
  
  $has_error=false;
  if ($gks_curl_http_code==200 and 
      is_array($response_array) and 
      isset($response_array['token']) and 
      $response_array['token']!='') {
    //egine ok to post
    $myerror='Pending ...';
    $response_array['Id']=0; 
    $response_array['Status']=1;
    $response_array['Result']=5;    
  } else if ($gks_curl_http_code==502 and 
      is_array($response_array) and
      isset($response_array['code']) and
      $response_array['code']=='0' and
      isset($response_array['status']) and
      $response_array['status']=='502') {
    
    $has_error=true;
    $response=$response_array['message'].'<br>'.gks_lang('Βεβαιωθείτε ότι η εφαρμογή της Worldline είναι ανοιχτή στο τερματικό.');
    $myerror=$response;
    $response_array['token']='';
    $response_array['Id']=0;
    $response_array['Status']=101;
    $response_array['Result']=101;
  } else {
    $has_error=true;
    $response_array=array();
    $response_array['token']='';
    $response_array['Id']=0;
    $response_array['Status']=101;
    $response_array['Result']=101;
    $myerror=$response;
  }
  
  

  
  

  $sql="update gks_eftpos_transaction set 
  transactionId='".$response_array['token']."',
  remote_id='".$db_link->escape_string($response_array['Id'])."',
  ".($has_error ? "transaction_status='agnosto'," : '')."
  mymessage='".$db_link->escape_string($myerror)."'
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}  


  $sql="update gks_worldline_transaction set
  intentId='".$db_link->escape_string($response_array['token'])."',
  myStatus=".$response_array['Status'].",
  myResult=".$response_array['Result'].",
  myerror='".$db_link->escape_string($myerror)."'
  where id_worldline_transaction=".$id_worldline_transaction;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
  //echo '<pre>response ';print_r($response_array); die();
  
  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').'<br>'.$url.'<br>http_code: <b>'.$gks_curl_http_code .'</b><br>'.$response;
    debug_mail(false,'worldline error',$response);return $return;}
  
  if (isset($response_array['token'])==false or trim_gks($response_array['token'])=='') {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').'<br>'.$url.'<br>http_code: <b>'.$gks_curl_http_code .'</b><br>'.$response;
    debug_mail(false,'worldline error',$response);return $return;}
  
  
  return array('success' => true, 'message' => 'OK', 'data' => $response_array); 
    
}

/*
Code	Description	Сountry applicability
100000001	Regular purchase	All
100000002	P2P	Ukraine, only 1 bank
200000001	Reversal	All
200000003	Refund with RRN	All
200000004	Refund with RRN and card tap,	All
200000005	Reversal with card tap	Azerbaijan
300000004	Purchase with tip	Greece, Slovakia
300000005	Installment	Greece
300000007	Purchase that support DCC	Greece
300000008	Pre-authorisation	Greece
300000009	Pre-authorisation completion	Greece
*/


function gks_eftpos_sales_request_get_status_worldline($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error' , 'status'=>'agnosto');
  $row_company=$data['row_company'];
  $row_tra=$data['row_tra'];
  $transaction_type=$data['row_tra']['transaction_type'];
  
  $transaction_token=$data['row_tra']['transactionId'];
  
  $sql="select paroxos_signature_id,acc_inv_payment_id,acc_pay_payment_id 
  from gks_eftpos_transaction 
  where sessionId='".$db_link->escape_string($data['sessionId'])."'
  and transaction_status='canceled'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  if ($result->num_rows==1) {
    //$row = $result->fetch_assoc(); 
    return array('success' => true, 'message' => 'OK', 'status' => 'canceled');
  }  
  
  $sql="select * from gks_worldline_transaction where id_worldline_transaction=".$data['row_tra']['xxx_transaction_id'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  if ($result->num_rows==0) {
    debug_mail(false,gks_lang('Δεν βρέθηκε η σχετική εγγραφή').' (78124g556)',$sql);
    return array('success' => false, 
    'message' => gks_lang('Δεν βρέθηκε η σχετική εγγραφή').' (78124g556)', 
    'status' =>  gks_lang('Δεν βρέθηκε η σχετική εγγραφή').' (78124g556)');}
  $row_wl = $result->fetch_assoc();
  $worldline_implementation=$row_wl['worldline_implementation'];
  
  if ($worldline_implementation=='app2app') {
    if ($row_wl['myStatus']!=1) { //NOT PENDING
      $transaction_status='abort';//abort canceled agnosto processed request
      if ($row_wl['myResult']==5) $transaction_status='agnosto'; 
      else if ($row_wl['myResult']==103) $transaction_status='canceled'; 
      
      $ret_message=trim_gks($row_wl['myerror']);
      if ($ret_message=='') $ret_message='Transaction processing error';
      
      return array('success' => true, 'message' => $ret_message, 'status' => $transaction_status);
    }
    
  }
  
  
  //echo '<pre>sssssss '.$worldline_implementation.' ';print_r($data);die();
  
  if (in_array($transaction_type,['fullvoid','fullvoiderp'])) {
    $sql="SELECT gks_eftpos_transaction_thisisfor.my_for, gks_worldline_transaction.TransactionId_org
    FROM (gks_eftpos_transaction_thisisfor 
    LEFT JOIN gks_eftpos_transaction ON gks_eftpos_transaction_thisisfor.my_for = gks_eftpos_transaction.id_eftpos_transaction) 
    LEFT JOIN gks_worldline_transaction ON gks_eftpos_transaction.xxx_transaction_id = gks_worldline_transaction.id_worldline_transaction
    WHERE gks_eftpos_transaction_thisisfor.my_this=".$row_tra['id_eftpos_transaction']."
    AND gks_eftpos_transaction_thisisfor.my_is='".$transaction_type."'
    AND gks_worldline_transaction.TransactionId_org<>''";
    
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    if ($result->num_rows==0) {
      debug_mail(false,gks_lang('Δεν βρέθηκε η σχετική γονική εγγραφή').' (1)',$sql);
      return array('success' => false, 
      'message' => gks_lang('Δεν βρέθηκε η σχετική γονική εγγραφή').' (1)', 
      'status' =>  gks_lang('Δεν βρέθηκε η σχετική γονική εγγραφή').' (1)');}
    $row = $result->fetch_assoc();
    $prev_id_eftpos_transaction=intval($row['my_for']);
    $prev_TransactionId_org=trim_gks($row['TransactionId_org']);
    
    if ($worldline_implementation=='serverapi') {
      $url=GKS_WORLDLINE_COM_API.'/v1/payment-intent/transaction/result?merchant_bank_id='.GKS_WORLDLINE_COM_API_BANK_ID.'&token='.$transaction_token;
      $curl_request='GET';
    } else if ($worldline_implementation=='app2app') {
      $url=GKS_WORLDLINE_COM_API_TOKEN.'/token/tr_get.do';
      $curl_request='POST';
    }    
    
  } else {
    if ($worldline_implementation=='serverapi') {
      $url=GKS_WORLDLINE_COM_API.'/v1/payment-intent/transaction/result?merchant_bank_id='.GKS_WORLDLINE_COM_API_BANK_ID.'&token='.$transaction_token;
      $curl_request='GET';
    } else if ($worldline_implementation=='app2app') {
      $url=GKS_WORLDLINE_COM_API_TOKEN.'/token/tr_get.do';
      $curl_request='POST';
    }
    
  }
  

  //echo '<pre>'.$url."\n".$curl_request."\n";print_r($data);die();
  
  
  if ($worldline_implementation=='serverapi') {
    $headers = array(
      'Content-Type:application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
      'Authorization: Bearer '. $data['access_token'],
    );
    $mypostdata='';
  } else if ($worldline_implementation=='app2app') {
    $headers = array(
      'Content-Type:application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
    );    
    $mypost=[];
    $mypost['client_id']=GKS_WORLDLINE_COM_API_PARTNER_ID;
    $mypost['client_secret']=GKS_WORLDLINE_COM_API_PARTNER_KEY;
    $mypost['UID']=GKS_WORLDLINE_COM_API_BANK_ID;
    $mypost['token']=$transaction_token;    
    
    $mypostdata=json_encode($mypost);
  }
  
  $ch = curl_init($url);
  if ($worldline_implementation=='app2app') {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);  
  }
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
  $response = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close($ch);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_status_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_status_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);


  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_status_send_mypostdata_'.time().'.json',$mypostdata);
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_status_send_s1_'.time().'.json',$url);
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_status_response_s1_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);


  
  //$response_conv=$response;
  //$response_conv=str_replace("\\r\\n", '', $response_conv);
  //$response_conv=str_replace("\\r"  , '', $response_conv);
  //$response_conv=str_replace("\\n"  , '', $response_conv);
  //$response_conv=str_replace('\\"'  , '"',$response_conv);
  
  $response_array = json_decode($response, true);
  
  if (is_string($response_array)) { 
    //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_edo_'.time().'.json',$response_array);
    $response_array=trim($response_array);
    if (substr($response_array, strlen($response_array)-5)=='",'."\r\n".'}') {
      $response_array=substr($response_array,0,strlen($response_array)-5).'"}';
    }
    //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_edo2_'.time().'.json',$response_array);
    $response_array = json_decode($response_array, true);//diplo gia na figei to escape
    //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_edo3_'.time().'.json',print_r($response_array,true));
  }
  


  //debug_mail(false,'response',$gks_curl_http_code."\r\n".$response."\r\n".print_r($response_array,true));
  //return array('success' => true, 'message' => 'OK', 'status' => 'wait');
  
  if ($worldline_implementation=='serverapi' and
      $gks_curl_http_code==502 and 
      is_array($response_array) and 
      isset($response_array['code']) and 
      $response_array['code']==703 and
      isset($response_array['status']) and 
      $response_array['status']==502) {
    return array('success' => true, 'message' => 'OK', 'status' => 'wait'); 
  }
  if ($worldline_implementation=='serverapi' and
      $gks_curl_http_code==404 and 
      is_array($response_array) and 
      isset($response_array['code']) and 
      $response_array['code']==0 and
      isset($response_array['status']) and 
      $response_array['status']==404) {
    return array('success' => true, 'message' => 'OK', 'status' => 'wait'); 
  }
    
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/0001.txt',print_r($response_array,true));
  
  if ($worldline_implementation=='app2app' and
      $gks_curl_http_code==200 and
      is_array($response_array) and 
      isset($response_array['status']) and 
      $response_array['status']==102) {
    return array('success' => true, 'message' => 'OK', 'status' => 'wait'); 
  }  

  
  
  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code .'<br>'.$response;
    debug_mail(false,'worldline error',$response);return $return;}

  
/*
{"name":"Bad Gateway or Proxy Error","message":"Failed to send payment activation request","code":0,"status":502}

se serverapi otan e;inai OK
{
  "message_type": "Message type. By default =transaction.result",
  "trn_id": "Transaction ID - receipt number for transaction",
  "trn_debit_id": "ID of reversed transaction or transaction to which the Refund relates",
  "trn_date_time": "Transaction date and time. YYYY-MM-DD hh:mm:ss",
  "trn_type": "Transaction type code. https://partner.tapxphone.com/index.php?r=docs%2Fview&page=reference-directories",
  "trn_type_name": "Transaction type name",
  "trn_status_code": "Transaction status code. 2 - successfully completed; 3- reversed(the transaction was canceled); 6 - completed with decline; 9 - transaction processing error",
  "trn_reference_label": "Transaction reference Label - Any value as defined by the merchant or acquirer in order to identify the transaction",
  "trn_token": "Token that was used for Payment Intent",
  "trn_currency": "Transaction currency",
  "trn_amount": "Transaction amount",
  "trn_host_rrn": "Transaction RRN in processing center",
  "trn_host_auth_code": "Transaction authorization result - authorisation code in processing center",
  "trn_host_resp_code": "Transaction authorization result - response code from processing center",
  "trn_host_error_desc": "Transaction authorization result - response description from processing center",
  "trn_batch": "Transaction batch status",
  "trn_stan": "Transaction Stan",
  "trn_info_data": "Transaction additional data received from external application",
  "merchant_bank_id": "Acquiring Service Provider ID",
  "merchant_id": "Merchnat ID in processing center",
  "merchant_name": "Merchant name",
  "merchant_taxpayer_id": "Merchant Taxpayer ID",
  "merchant_terminal_id": "Payment terminal ID in processing center",
  "merchant_terminal_description": "Payment terminal descriprion or Outlet description",
  "merchant_terminal_location": "Payment terminal address or Outlet name",
  "merchant_terminal_city": "Payment terminal city or Outlet city",
  "merchant_terminal_state": "Payment terminal state or Outlet state",
  "merchant_terminal_country": "Payment terminal country code or Outlet country code",
  "merchant_device_id": "Mobile device ID in tapXphone application",
  "card_pan": "Mask of card personal account number",
  "card_aid": "Card Application ID of payment card, tag 9F06 or 84 from EMV configuration",
  "card_lable": "Card Application label of payment card, tag 50 from EMV configuration",
  "card_cvm_pin": "TRUE if card holder verification by PIN",
  "card_cvm_cdcvm": "TRUE if card holder verification on device, tag 9F6C from EMV configuration",
  "card_tvr": "Terminal verification results, tag 95 from EMV configuration",
  "card_kvr": "Kernel verification results, tag DF74 from EMV configuration",
  "card_cda_res": "Tag 9F70 from EMV configuration"
}

se app2app otan einai OK
{
    "hostRs": null, //Payment service provider Response message about unsuccessful payment transaction processing (if any) during authorization. "null" if the value is absent.
    "status_transaction": 1, //Payment transaction status, that can have the following values: 1 - complete (transaction completed successfully); 2 - error (the transaction was declined by acquirer); 3 - reversed (the transaction was canceled)
    "info_data": null, //Additional information about the transaction according to “Payment token” request . "null" if the value is absent.
    "rquid": "2cfb3454-6404-4ff2-b740-bf7b36a78500",
    "receipt": "{\"status\":\"APPROVED\",\"bank_owner\":\"Bank 23\",\"trxid\":\"67909464\",\"pmt_name\":\"Purchase\",\"pmt_dest\":\"Emul Klimovich E 23\",\"unp\":\"12345679\",\"aid\":\"A0000000031010\",\"applbl\":\"Visa Debit Contactless\", \"card_mask\":\"458522******3110\",\"rrn\":\"000000025364\",\"auth_code\":\"764777\",\"pmt_terminal\":\"04EM0004\",\"terminal\":\"e9f32aac576e0ea1_23\",\"amt\":\"4.00\",\"cur_code\":\"EUR\",\"date_time\":\"2024-04-20 00:07:41\",\"tvr\":\"0000000000\",\"trn_reference_label\":\"F00CD90A54313233343B31313235\",\"time_offset\":\"GMT+03:00\",\"host_resp_code\":\"000\",\"stan\":\"760\",\"batch\":\"1\",\"trn_type\":\"100000001\",\"trn_status_code\":\"2\"}", //(Optional) The structure of the receipt is determined by each Acquiring Service Provider separately. For a description of the possible parameters see the “Receipt parameters” section
    "deviceId": "e9f32aac576e0ea1_19", //Mobile device ID in tapXphone application
    "status": 0, //Request processing status. "0" if the request was processed successfully. If the code is different, then the request was processed with an error.
    "token": "NgKdssRi2qTbpnxdMcSO48dftWHjN2bx" //Payment token
}
{
    "hostRs": null,
    "status_transaction": 1,
    "info_data": "{\"custom_ref_label\":\"8b9ae79d-a199-ddef-f136-7985977fc819\"}",
    "rquid": "42106e72-6482-4958-80f4-d81a99018922",
    "receipt": "{\"status\":\"APPROVED\", \"auth_status_name\":\"Approved\",\"bank_owner\":\"Bank 23\",\"trxid\":\"37229165\",\"pmt_name\":\"Purchase\",\"pmt_dest\":\"GKS SOFTWARE\",\"unp\":\"065938168\",\"aid\":\"A0000000031010\",\"applbl\":\"Visa Debit Contactless\", \"card_mask\":\"498877******9251\",\"rrn\":\"000000027876\",\"auth_code\":\"764777\",\"pmt_terminal\":\"23EM0227\",\"terminal\":\"601ba51f3c68ebe6_23\",\"amt\":\"2.00\",\"cur_code\":\"EUR\",\"date_time\":\"2025-06-29 05:06:01\",   \"tvr\":\"0000000000\",    \"trn_reference_label\": \"8b9ae79d-a199-ddef-f136-7985977fc819\", \"time_offset\": \"GMT+03:00\", \"host_resp_code\":\"000\",\"stan\":\"116\",\"batch\":\"1\",\"trn_type\":\"100000001\",\"trn_status_code\":\"2\",\"trn_id_gr_aade\":\"tr1;109;000000027876;764777\"}",
    "deviceId": "601ba51f3c68ebe6_23",
    "status": 0,
    "token": "Dxn3XRoeCLVpFcshHHKTJYr0q6Jlo74U"
}

*/
  if ($worldline_implementation=='serverapi' and isset($response_array['message_type'])==false) {

    return array('success' => true, 'message' => 'OK', 'status' => 'wait');
  }
  

  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/0000.txt','');
  
  $r_Result=-1;  
  if ($worldline_implementation=='serverapi') {
    
    if (isset($response_array['trn_status_code'])) {
      /*"Transaction status code. 
      2 - successfully completed; 
      3-  reversed(the transaction was canceled); 
      6 - completed with decline; 
      9 - transaction processing error",*/
      
      $r_Result=intval($response_array['trn_status_code']);
      
    }
    
  } else if ($worldline_implementation=='app2app') {
    $app2app_receipt=[];
    if (isset($response_array['status_transaction'])) {
      /* Payment transaction status, that can have the following values: 
      1 - complete (transaction completed successfully); 
      2 - error (the transaction was declined by acquirer); 
      3 - reversed (the transaction was canceled)*/
      if ($response_array['status_transaction']==1) $r_Result=2;
      else if ($response_array['status_transaction']==2) $r_Result=6;
      else if ($response_array['status_transaction']==3) $r_Result=3;
      
      if (isset($response_array['receipt'])) {
        $response_array['receipt']=json_decode($response_array['receipt'],true);
      }
      
    }
  }
  
  $r_Status=3; 

  
  //print '<pre>sssssssssssss ';print_r($response_array);die();

  
  $paroxos_signature_id=0;
  $sql="select paroxos_signature_id,acc_inv_payment_id,acc_pay_payment_id 
  from gks_eftpos_transaction 
  where sessionId='".$db_link->escape_string($data['sessionId'])."'";
  //debug_mail(false,'sql2',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc(); 
    $paroxos_signature_id=intval($row['paroxos_signature_id']);

    $t_gks_acc_xxx_payment='';
    $f_acc_xxx_id='';
    $f_acc_xxx_payment_id='';
    $f_id_acc_xxx_payment='';
    if ($row['acc_inv_payment_id']>0) {
      $t_gks_acc_xxx_payment='gks_acc_inv_payment';
      $f_acc_xxx_id='acc_inv_id';
      $f_acc_xxx_payment_id='acc_inv_payment_id';
      $f_id_acc_xxx_payment='id_acc_inv_payment';
    } else if ($row['acc_pay_payment_id']>0) {
      $t_gks_acc_xxx_payment='gks_acc_pay_payment';
      $f_acc_xxx_id='acc_pay_id';
      $f_acc_xxx_payment_id='acc_pay_payment_id';
      $f_id_acc_xxx_payment='id_acc_pay_payment';
    }    
  }
  
  
    
/*
serverapi and app2app
2	Transaction was successfully completed by acquirer
3 reversed(the transaction was canceled); 
6	Transaction was declined (rejected) by acquirer
9	Transaction processing error
*/
    
  if ($r_Result!=2) {//ola ektos APPROVED
    
    
    
    $transaction_status='abort'; $ret_message='general error';
    if ($r_Result==6) {$transaction_status='abort';   $ret_message='Transaction was declined (rejected) by acquirer';}
    if ($r_Result==9) {$transaction_status='abort';   $ret_message='Transaction processing error';}

  
    $sql="update gks_eftpos_transaction set 

    transaction_status='".$transaction_status."',
    mymessage='".$db_link->escape_string($ret_message)."',
    user_id_edit=".$my_wp_user_id.",
    mydate_edit=now(),
    myip='".$db_link->escape_string($gkIP)."'
    where payment_acquirer_with_id=6 
    AND transaction_status<>'done'
    AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
    //debug_mail(false,'sql3',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
    if (in_array($transaction_type,['sale','saleerp'])) {  
      $sql="UPDATE ".$t_gks_acc_xxx_payment." 
      LEFT JOIN gks_eftpos_transaction ON ".$t_gks_acc_xxx_payment.".".$f_id_acc_xxx_payment." = gks_eftpos_transaction.".$f_acc_xxx_payment_id." 
      SET ".$t_gks_acc_xxx_payment.".transaction_pa_with_id = 0, 
      ".$t_gks_acc_xxx_payment.".transaction_id = 0
      WHERE gks_eftpos_transaction.payment_acquirer_with_id=6
      AND gks_eftpos_transaction.transaction_status<>'done'
      AND gks_eftpos_transaction.sessionId='".$db_link->escape_string($data['sessionId'])."'";
      //debug_mail(false,'sql 4',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    }
    
    $sql="update gks_worldline_transaction set 
    Approved=0,
    Voided=0,    
    myStatus=".$r_Status.",
    myResult=".$r_Result.",
    myerror='".$db_link->escape_string($ret_message)."'
    where intentId='".$db_link->escape_string($transaction_token)."'";
    //debug_mail(false,'sql 5',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
    
    if ($paroxos_signature_id>0) {
      $sql="update gks_paroxos_signature set 
      signature_status='canreuse',mydate_edit=now() 
      where id_paroxos_signature=".$paroxos_signature_id."
      and signature_status in ('assign')";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    }
        
    
    
    return array('success' => true, 'message' => $ret_message, 'status' => $transaction_status);
  }
  
  $cna=[];
  $new_tip_amount=0;
  if ($worldline_implementation=='serverapi') {
    $cna=$response_array;
  } else if ($worldline_implementation=='app2app') {
    $rcp=[];if (isset($response_array['receipt'])) $rcp=$response_array['receipt'];
    
    $cna=[];
    $message_type=[];
    if (isset($rcp['status'])) $message_type[]= $rcp['status'];
    if (isset($rcp['auth_status_name'])) $message_type[]= $rcp['auth_status_name'];
    $cna['message_type']=implode(' ',$message_type);

    if (isset($rcp['trxid'])) $cna['trn_id']=$rcp['trxid'];
    if (isset($rcp['date_time'])) $cna['trn_date_time']=$rcp['date_time'];
    if (isset($rcp['trn_type'])) $cna['trn_type']=$rcp['trn_type'];
    if (isset($rcp['pmt_name'])) $cna['trn_type_name']=$rcp['pmt_name'];
    if (isset($rcp['trn_status_code'])) $cna['trn_status_code']=$rcp['trn_status_code'];
    if (isset($response_array['token'])) $cna['trn_token']=$response_array['token'];
    if (isset($rcp['cur_code'])) $cna['trn_currency']=$rcp['cur_code'];
    if (isset($rcp['amt'])) $cna['trn_amount']=$rcp['amt'];
    if (isset($rcp['rrn'])) $cna['trn_host_rrn']=$rcp['rrn'];
    if (isset($rcp['auth_code'])) $cna['trn_host_auth_code']=$rcp['auth_code'];
    if (isset($rcp['host_resp_code'])) $cna['trn_host_resp_code']=$rcp['host_resp_code'];
    if (isset($rcp['host_resp_code'])) $cna['trn_host_resp_code']=$rcp['host_resp_code'];
    if (isset($rcp['bank_owner'])) $cna['merchant_bank_id']=$rcp['bank_owner'];
    //"merchant_id":"0020022236",
    if (isset($rcp['pmt_dest'])) $cna['merchant_name']=$rcp['pmt_dest'];
    if (isset($rcp['unp'])) $cna['merchant_taxpayer_id']=$rcp['unp'];
    if (isset($rcp['pmt_terminal'])) $cna['merchant_terminal_id']=$rcp['pmt_terminal'];
    //"merchant_terminal_description":"GKS SOFTWARE",
    //"merchant_terminal_location":"GKS SOFTWARE",
    //"merchant_terminal_city":"THESSALONIKI",
    //"merchant_terminal_country":"GR",
    if (isset($rcp['terminal'])) $cna['merchant_device_id']=$rcp['terminal'];
    if (isset($rcp['trn_id_gr_aade'])) $cna['trn_id_gr_aade']=$rcp['trn_id_gr_aade'];
    if (isset($rcp['aid'])) $cna['card_aid']=$rcp['aid'];
    if (isset($rcp['applbl'])) $cna['card_label']=$rcp['applbl'];
    if (isset($rcp['tvr'])) $cna['card_tvr']=$rcp['tvr'];
    if (isset($rcp['card_mask'])) $cna['card_pan']=$rcp['card_mask'];
    
    

    if (isset($rcp['stan'])) $cna['trn_stan']=$rcp['stan'];
    if (isset($rcp['batch'])) $cna['trn_batch']=$rcp['batch'];

    if (isset($rcp['tip_amt'])) $new_tip_amount=floatval($rcp['tip_amt']);
    
    /* auta pou den iparxoyn sto serverapi
    
    sto basiko
      info_data
      rquid
      status
    sto receipt
      status
      auth_status_name
      time_offset
    */
    
    
  }
  
  $sql="select amount 
  from gks_eftpos_transaction 
  where payment_acquirer_with_id=6 
  AND transaction_status<>'done'
  AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $orig_amount=floatval($row['amount']);
    $new_amount=floatval($cna['trn_amount']);
    $temp=$new_amount-$orig_amount;
    if ($temp>0) {
      $cna['trn_amount']=$orig_amount;
      $new_tip_amount=$temp;
    }
  }
  
  
  
  //print '<pre>aaaaaaaaaaaa '; print_r($cna);die();
  
  //$r_Status=3;
  
  $transaction_number=''; if (isset($cna['trn_id'])) $transaction_number=$cna['trn_id'];
  $aadeTransactionId='';if (isset($cna['trn_id_gr_aade'])) $aadeTransactionId=$cna['trn_id_gr_aade'];
  
  //transactionId='".$db_link->escape_string($transactionId)."',
  $sql="update gks_eftpos_transaction set 
  transaction_status='done',
  aadeTransactionId='".$db_link->escape_string($aadeTransactionId)."',
  response_array='".$db_link->escape_string(json_encode($response_array))."',
  mymessage='Read transaction',
  tipAmount=".$new_tip_amount.",
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."'
  where payment_acquirer_with_id=6 
  AND transaction_status<>'done'
  AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
  //debug_mail(false,'sql 7',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}

  
  if ($paroxos_signature_id>0) {
    $sql="update gks_paroxos_signature set 
    signature_status='used',mydate_edit=now()
    where id_paroxos_signature=".$paroxos_signature_id."
    and signature_status in ('assign')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  }  
  
  $TransactionId_org='';if (isset($cna['trn_host_rrn'])) $TransactionId_org=$cna['trn_host_rrn'];
  
  $new_fileds=[];
  $sqlF=array();$sqlV=array();  $sqlU=array();     
  $fab=array(); 
  $fai=array('trn_status_code'); 
  $faf=array('trn_amount');
  $fad=array('trn_date_time');
  $fas=array('message_type','trn_id','trn_debit_id','trn_type','trn_type_name','trn_reference_label','trn_token','trn_currency','trn_host_rrn',
  'trn_host_auth_code','trn_host_resp_code','trn_host_error_desc','trn_batch','trn_stan','trn_info_data',
  'merchant_bank_id','merchant_id','merchant_name','merchant_taxpayer_id','merchant_terminal_id','merchant_terminal_description',
  'merchant_terminal_location','merchant_terminal_city','merchant_terminal_state','merchant_terminal_country',
  'merchant_device_id','trn_id_gr_aade',
  'card_pan','card_aid','card_label','card_cvm_pin','card_cvm_cdcvm','card_tvr','card_kvr','card_cda_res');  
  

  $transactionId='';
  
      
          
  if (isset($cna['trn_host_rrn'])) $transactionId=$cna['trn_host_rrn'];
  

  
  $sqlF[]='TipAmount';
  $sqlV[]=$new_tip_amount;
  $sqlU[]='TipAmount='.$new_tip_amount;
  
  foreach ($cna as $key => $rtr) {
    if ($rtr!==null) {
      if (in_array($key,$fab)) {
        $sqlF[]=$key;
        $sqlV[]=(boolval($rtr) ? '1':'0');
        $sqlU[]=$key."=".(boolval($rtr) ? '1':'0');
      } else if (in_array($key,$fai)) {
        $sqlF[]=$key;
        $sqlV[]=intval($rtr);
        $sqlU[]=$key."=".intval($rtr);
      } else if (in_array($key,$faf)) {
        $sqlF[]=$key;
        $sqlV[]=floatval($rtr);
        $sqlU[]=$key."=".floatval($rtr);
      } else if (in_array($key,$fad)) {
        $sqlF[]=$key;
        $sqlV[]="'".date('Y-m-d H:i:s',_time_user(strtotime($rtr),-1))."'";
        $sqlU[]=$key."='".date('Y-m-d H:i:s',_time_user(strtotime($rtr),-1))."'";
      } else if (in_array($key,$fas)) {
        $sqlF[]=$key;
        $sqlV[]="'".$db_link->escape_string($rtr)."'";
        $sqlU[]=$key."='".$db_link->escape_string($rtr)."'";
      } else {
        $new_fileds[]=$key;
      }
    }
  }
          
  
  $company_id=0;
  $worldline_mid=''; if (isset($cna['merchant_id'])) $worldline_mid=trim_gks($cna['merchant_id']);
  if ($worldline_mid!='') {
    $sql="select id_company from gks_company where company_disable=0 and worldline_mid='".$db_link->escape_string($worldline_mid)."'";
    //debug_mail(false,'sql 8',$sql);
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      $company_id=intval($row['id_company']);
    }
  }
          
  $myVoided=0;
  if (in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) {
    $myVoided=1;
  }
          
  if ($transaction_token!='') {
    
    
    $sql="update gks_worldline_transaction set 
    Approved=1,
    Voided=".$myVoided.",    
    myStatus=".$r_Status.",
    myResult=".$r_Result.",
    TransactionId_org='".$db_link->escape_string($TransactionId_org)."',
    ".implode(',',$sqlU).",
    myjson='".$db_link->escape_string(json_encode($response))."',
    myerror='Finish',
    user_id_edit=".$my_wp_user_id.",
    mydate_edit=now(),
    myip='".$db_link->escape_string($gkIP)."'
    where intentId='".$db_link->escape_string($transaction_token)."'";
    //debug_mail(false,'sql 9',$sql);
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
                      
            

  } else {
    $sql="insert into gks_worldline_transaction (
    user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
    xeiristis_id,add_from_system,myfrom,
    myStatus,myResult,
    TransactionId_org,
    ".implode(',',$sqlF).",
    myjson,
    myerror,
    Voided
    ) values (
    ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
    ".$my_wp_user_id.",'gks ERP App','worldline Api',
    ".$r_Status.",".$r_Result.",
    '".$db_link->escape_string($TransactionId_org)."',
    ".implode(',',$sqlV).",
    '".$db_link->escape_string(json_encode($resitem))."',
    'Finish',
    ".$myVoided."
    )";
    //debug_mail(false,'sql 10',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
    
    $id_worldline_transaction = $db_link->insert_id;
      
    $sql="update gks_eftpos_transaction set
    xxx_transaction_id=".$id_worldline_transaction.",
    mymessage='Finish',
    company_id=".$company_id."
    where id_eftpos_transaction=".$data['row_tra']['id_eftpos_transaction'];
    //debug_mail(false,'sql 11',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
  }
          
  if (count($new_fileds)>0)  {
    debug_mail(false,'new fields in worldline.php',print_r($new_fileds,true)."\n".print_r($resitem,true));
  }
        

  
  $sql="update gks_eftpos_transaction set 
  transactionId='".$db_link->escape_string($transactionId)."'
  where payment_acquirer_with_id=6 
  AND transaction_status='done'
  AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
  //debug_mail(false,'sql 12',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  
  if (in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) {
    
    
    $sql="select * from gks_eftpos_transaction
    where payment_acquirer_with_id=6 
    AND transaction_status='done'
    AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
    //debug_mail(false,'sql 2',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    if ($result->num_rows==1) {
      
      $row_eftpos_curr = $result->fetch_assoc(); 
      $my_this=intval($row_eftpos_curr['id_eftpos_transaction']);
      
      $sql="select my_for from gks_eftpos_transaction_thisisfor
      where my_this=".$my_this."
      and my_is='".$db_link->escape_string($transaction_type)."'
      and my_for>0";
      //debug_mail(false,'sql1',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
      if ($result->num_rows==1) {
        $row = $result->fetch_assoc(); 
        $my_for=intval($row['my_for']);
        
        $sql="select * from ".$t_gks_acc_xxx_payment."
        where transaction_id=".$my_for;
        
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
        if ($result->num_rows==1) {
          $row_acc_xxx_payment = $result->fetch_assoc();
          if ($t_gks_acc_xxx_payment=='gks_acc_pay_payment') {
            $acc_pay_method_id=$row_acc_xxx_payment['acc_pay_method_id'];
          }
          //ean iparxei idi
          $sql="select * from ".$t_gks_acc_xxx_payment."
          where transaction_id=".$my_this;
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
        
          
          if ($result->num_rows==0) {
            //den iparxei idi, an mpei
            //echo 'ggggggg11116';die();
            $sql="insert into ".$t_gks_acc_xxx_payment." (
            mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
            ".$f_acc_xxx_id.",
            ".($t_gks_acc_xxx_payment=='gks_acc_pay_payment' ? 'acc_pay_method_id,' : '')."
            pp,payment_acquirer_id,poso,asset_id,
            transaction_pa_with_id,transaction_id
            ) values (
            now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
            ".$row_acc_xxx_payment[$f_acc_xxx_id].",
            ".($t_gks_acc_xxx_payment=='gks_acc_pay_payment' ? $acc_pay_method_id.',' : '')."
            ".$row_acc_xxx_payment['pp'].",
            ".$row_eftpos_curr['payment_acquirer_id'].",
            ".-floatval($row_eftpos_curr['amount']).",
            ".$row_eftpos_curr['asset_id'].",
            ".$row_eftpos_curr['payment_acquirer_with_id'].",
            ".$my_this."
            )";
            //debug_mail(false,'sql 5',$sql);
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
            
            
            $acc_xxx_payment_id = $db_link->insert_id;
            
            $sql="UPDATE gks_eftpos_transaction 
            SET ".$f_acc_xxx_payment_id."=".$acc_xxx_payment_id."
            where payment_acquirer_with_id=6 
            AND transaction_status='done'
            AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
            //debug_mail(false,'sql3',$sql);

            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}

                      
            //echo 'ggggggg11117';die();
              
          }
            
        }
        
        
        //todo gia pay
        
      }
      
      
    }

  }    
    
    
  
  
  
  return array('success' => true, 'message' => 'OK', 'status' => 'done');
    
  echo '<pre>'.$url."\n";print_r($cna);die();
  
    
  

    
}

function gks_eftpos_sales_request_abort_worldline($data) {

  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');

  $sql="select * from gks_eftpos_transaction 
  where payment_acquirer_with_id=6 
  AND transaction_status<>'done'
  AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  
  if ($result->num_rows==0) {
    debug_mail(false,gks_lang('Δεν βρέθηκε η συναλλαγή'),$sql);
    return array('success' => false, 'message' => gks_lang('Δεν βρέθηκε η συναλλαγή'), 'status' => '');}
    
  $row = $result->fetch_assoc();  
  $transaction_type=$row['transaction_type'];
  $acc_inv_payment_id=intval($row['acc_inv_payment_id']);
  $acc_pay_payment_id=intval($row['acc_pay_payment_id']);
  $xxx_transaction_id=intval($row['xxx_transaction_id']);
  $paroxos_signature_id=intval($row['paroxos_signature_id']);
  
  
  $t_gks_acc_xxx_payment='';
  $f_acc_xxx_id='';
  $f_acc_xxx_payment_id='';
  $f_id_acc_xxx_payment='';
  if ($row['acc_inv_payment_id']>0) {
    $t_gks_acc_xxx_payment='gks_acc_inv_payment';
    $f_acc_xxx_id='acc_inv_id';
    $f_acc_xxx_payment_id='acc_inv_payment_id';
    $f_id_acc_xxx_payment='id_acc_inv_payment';
  } else if ($row['acc_pay_payment_id']>0) {
    $t_gks_acc_xxx_payment='gks_acc_pay_payment';
    $f_acc_xxx_id='acc_pay_id';
    $f_acc_xxx_payment_id='acc_pay_payment_id';
    $f_id_acc_xxx_payment='id_acc_pay_payment';
  }   
    
  //echo '<pre>sssssssssss '.$transaction_type.' -- '.$acc_inv_payment_id.' -- '.$acc_pay_payment_id.' --- '.$t_gks_acc_xxx_payment.' -- ';print_r($data);die();  
    
  $sql="update gks_eftpos_transaction set 
  transaction_status='canceled',
  mymessage='".$db_link->escape_string('Canceled by user')."',
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."'
  where payment_acquirer_with_id=6 
  AND transaction_status<>'done'
  AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
  //debug_mail(false,'sql 3',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
  if (in_array($transaction_type,['sale','saleerp'])) {  
    $sql="UPDATE ".$t_gks_acc_xxx_payment." 
    LEFT JOIN gks_eftpos_transaction ON ".$t_gks_acc_xxx_payment.".".$f_id_acc_xxx_payment." = gks_eftpos_transaction.".$f_acc_xxx_payment_id." 
    SET ".$t_gks_acc_xxx_payment.".transaction_pa_with_id = 0, 
    ".$t_gks_acc_xxx_payment.".transaction_id = 0
    WHERE gks_eftpos_transaction.payment_acquirer_with_id=6
    AND gks_eftpos_transaction.transaction_status<>'done'
    AND gks_eftpos_transaction.sessionId='".$db_link->escape_string($data['sessionId'])."'";
    //debug_mail(false,'sql 4',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  }
  
  if ($xxx_transaction_id>0) {
    $sql="update gks_worldline_transaction set 
    Approved=0,
    Voided=0,    
    myStatus=3,
    myResult=103,
    myerror='Canceled by user'
    where id_worldline_transaction=".$xxx_transaction_id;
    //debug_mail(false,'sql 5',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  }
  
  if ($paroxos_signature_id>0) {
    $sql="update gks_paroxos_signature set 
    signature_status='canreuse',mydate_edit=now()
    where id_paroxos_signature=".$paroxos_signature_id."
    and signature_status in ('assign')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  }
  
  return array('success' => true, 'message' => 'OK'); 
  
  echo '<pre>sssssssssss ';print_r($data);die();
  
}


function gks_eftpos_get_transaction_extra_html_worldline($input) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;

  $return=array('success' => false, 'message' => 'generic error',
    'transaction'=>array(
      'html'=> gks_lang('Σφάλμα').'. '.gks_lang('Δεν βρέθηκε η συναλλαγή'),
      'donedate' => '',
      //amount
      //tipamount
    ),
  );
    
  //echo '<pre>';print_r($input);die();
  
  $transactionId=''; if (isset($input['transactionId'])) $transactionId=trim($input['transactionId']);
  $sessionId=''; if (isset($input['sessionId'])) $sessionId=trim($input['sessionId']);
  if ($transactionId=='' and $sessionId=='') {
    $return['message']=gks_lang('Δεν έχουν ορισθεί όλα τα δεδομένα');
    debug_mail(false,$return['message'],print_r($input,true));return $return;}
  
  $sql="select * from gks_worldline_transaction 
  where eftpos_transaction_id=".$input['id_eftpos_transaction'];
  //if ($transactionId!='') $sql.=" `Id`='".$db_link->escape_string($transactionId)."'";
  //else if ($sessionId!='') $sql.=" MerchantTrns like '%|".$db_link->escape_string($sessionId)."|gks'";  
  //else $sql.=" 1=2";
  $result = $db_link->query($sql);  
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc(); 
    if (!empty($row['trn_date_time'])) $return['transaction']['donedate']=$row['trn_date_time'];
    
    $amount=floatval($row['trn_amount']);
    if ($amount!=0) $return['transaction']['amount']=$amount;
        
    //$tipamount=floatval($row['TipAmount']);
    //if ($tipamount!=0) $return['transaction']['tipamount']=$tipamount;
        
    $html=[];
    if (!empty($row['TransactionId_org'])) $html[]='TransactionId-2: '.$row['TransactionId_org'];
//    if (!empty($row['TipAmount'])) $html[]='TipAmount: <b>'.myCurrencyFormat($row['TipAmount']).'</b>';
//    
//    
//    
//    //if (!empty($row['TotalCommission'])) $html[]=gks_lang('Προμήθεια').': '.myCurrencyFormat(floatval($row['TotalCommission']));
//    //if (!empty($row['TotalFee'])) $html[]=gks_lang('Τέλος').': '.myCurrencyFormat(floatval($row['TotalFee']));
//    
    if (!empty($row['Approved']) and $row['Approved']==1) {
      $html[]='Approved: <span class="gks_worldline_transaction_approved">Approved</span>';
    } else {
      $html[]='Approved: <span class="gks_worldline_transaction_not_approved">Not Approved</span>';
    }
    if (!empty($row['Voided']) and $row['Voided']==1) {
      $html[]='Voided: <span class="gks_worldline_transaction_voided">Voided</span>';
    } 
    
   
    //if (!empty($row['trn_type'])) {
      $html[]=gks_lang('Τύπος').': <b>'. gks_eftpos_has_transaction_type_worldline($row['trn_type']).'</b>';
    //} 
//
//      
    $html[]=gks_lang('Αναφορά Πληρωμής').': <span title="'.$row['trn_reference_label'].'"><i class="fas fa-exclamation-circle"></i></span>';
    $html[]='MID: '.$row['merchant_id'].'</span>';
    $html[]='TID: '.$row['merchant_terminal_id'].'</span>';
//    //if (isset($row['Latitude']) and isset($row['Longitude'])) {
//    //  $html[]=gks_lang('Στίγμα').': '.
//    //  '<a href="https://www.google.com/maps/search/?api=1&query='.$row['Latitude'].','.$row['Longitude'].'" target="_blank"><i class="fas fa-map-marker-alt gks_marker_gps"></i></a>';
//    //}
//
//    //if (!empty($row['referenceNumber'])) $html[]=gks_lang('Κωδικός Αναφοράς').': '.$row['referenceNumber'];
//    //if (!empty($row['cardType'])) $html[]=gks_lang('Τύπος Κάρτας').': '.$row['cardType'];
//    if (!empty($row['applicationName'])) $html[]=gks_lang('Εφαρμογή').': '.$row['applicationName'];
    if (!empty($row['card_label'])) $html[]=gks_lang('Τύπος Κάρτας').': <b class="worldline_tra_card_label worldline_tra_card_label_'.str_replace(' ','_',$row['card_label']).'">'.$row['card_label'].'</b>';


    if (!empty($row['card_pan'])) $html[]=gks_lang('Κάρτα').': '.$row['card_pan'];
//    if (!empty($row['CustomerEmail'])) $html[]=gks_lang('email').': <a href="mailto:'.$row['CustomerEmail'].'">'.$row['CustomerEmail'].'</a>';
//    if (!empty($row['CustomerPhone'])) $html[]=gks_lang('Τηλέφωνο').': <a href="tel:'.$row['CustomerPhone'].'">'.$row['CustomerPhone'].'</a>';
    if (!empty($row['merchant_bank_id'])) $html[]=gks_lang('Τράπεζα').': '.$row['merchant_bank_id'];
    if (!empty($row['trn_stan'])) $html[]='STAN: '.$row['trn_stan'];
    if (!empty($row['trn_batch'])) $html[]='BatchNumber: '.$row['trn_batch'];
//    if (!empty($row['Batch'])) $html[]='Batch: '.$row['Batch'];
    if (!empty($row['trn_host_rrn'])) $html[]='RRN: '.$row['trn_host_rrn'];
    if (!empty($row['trn_host_auth_code'])) $html[]='AuthCode: '.$row['trn_host_auth_code'];
//    if (!empty($row['trn_host_rrn'])) $html[]='OriginalRRN: '.$row['trn_host_rrn'];
//    if (!empty($row['OriginalAuthCode'])) $html[]='OriginalAuthCode: '.$row['OriginalAuthCode'];
    if (!empty($row['CurrencyCode'])) $html[]='CurrencyCode: '.$row['trn_currency'];
//    if (!empty($row['DccCurrencyCode'])) $html[]='DccCurrencyCode: '.$row['DccCurrencyCode'];
//    if (!empty($row['CustomerReference'])) $html[]='CustomerReference: '.$row['CustomerReference'];
    
    
    

    $html[]='<a href="admin-worldline-transaction-raw.php?id='.$input['id_eftpos_transaction'].'" target="_blank" title="Raw Data"><i class="fas fa-database gks_payment_rawdata"></i></a>'.
           ' <i class="fas fa-arrow-circle-left gks_payment_next_actions" '.
           'data-id="'.$input['id_eftpos_transaction'].'" '.
           'data-poso="'.$input['amount'].'" '.
           'data-asset_id="'.$input['asset_id'].'" '.
           'data-asset_title="'.base64_encode($input['asset_title']).'" '.
           'data-id_acc_xxx_payment="'.$input['id_acc_xxx_payment'].'" '.
           '></i>';


    $return['transaction']['html']=implode('<br>',$html);
    $return['success']=true;
    $return['message']='OK';
    
  }
  
  return $return;
}

function gks_eftpos_transaction_result_text_worldline_app2app($intent_value) {
  switch ($intent_value) {   
    case 'INTENT-0': return 'Transaction completed successfully';break;
    case 'INTENT-512': return 'Device blocked';break;
    case 'INTENT-900': return 'Transaction failed';break;
    case 'INTENT-903': return 'Intent has an invalid merchant_bank_id';break;
    case 'INTENT-904': return 'Network Error';break;
    case 'INTENT-905': return 'Invalid transaction type';break;
    case 'INTENT-906': return 'Invalid locale';break;
    case 'INTENT-907': return 'Invalid amount';break;
    case 'INTENT-908': return 'Overlay detected';break;
    case 'INTENT-909': return 'The device successfully configured and can be used for payment';break;
    case 'INTENT-910': return 'The device setup required';break;
    case 'INTENT-911': return 'Developer mode detected';break;
    case 'INTENT-912': return 'Split screen detected';break;
    case 'INTENT-913': return 'Error in the formation of a request to the server';break;
    case 'INTENT-914': return 'Encryption error';break;
    case 'INTENT-915': return 'Error receiving a response from the server';break;
    case 'INTENT-916': return 'Response parsing error';break;
    case 'INTENT-917': return 'Invalid currency';break;
    case 'INTENT-918': return 'Invalid payload data for transaction';break;
    case 'INTENT-919': return 'Card reading error';break;
    case 'INTENT-920': return 'Payment transaction declined by server';break;
    case 'INTENT-921': return 'The transaction is canceled after execution within one session';break;
    case 'INTENT-922': return 'Lack of external data from the token';break;
    case 'INTENT-923': return 'Reset application settings required';break;
    case 'INTENT-924': return 'Incorrect device status';break;
    case 'INTENT-925': return 'Service Connection Error';break;
    case 'INTENT-950': return 'No Internet connection';break;
    case 'INTENT-951': return 'All required permissions have not been obtained.';break;
    case 'INTENT-952': return 'Receiving data from the configuration server occur for too long time ago.';break;
    case 'INTENT-953': return 'Hardware TEE is not detected on the device';break;
    case 'INTENT-954': return 'Google services is not detected on the device';break;
    case 'INTENT-955': return 'Random generator self check not passed';break;
    case 'INTENT-956': return 'Device status: error';break;
    case 'INTENT-958': return 'Reinitialization required';break;
    case 'INTENT-959': return 'NFС is not detected on the device';break;
    case 'INTENT-961': return 'Device status: initialization required';break;
    case 'INTENT-962': return 'Device status: blocked';break;
    case 'INTENT-963': return 'Server response error';break;
    case 'INTENT-964': return 'Input parameters are incorrect';break;
    case 'INTENT-965': return 'Required functionality is not supported';break;
    case 'INTENT-966': return 'Not isolated start of SDK is prohibited';break;
    case 'INTENT-967': return 'External app not allowed';break;
    case 'INTENT-968': return 'Cardholder rejected transaction';break;
    case 'INTENT-969': return 'NFC error';break;
    case 'INTENT-970': return 'Play Integrity error';break;
    case 'INTENT-971': return 'Camera usage detected';break;
    case 'INTENT-999': return 'The status of the payment transaction is unknown';break;
  }
  return gks_lang('Η συναλλαγή απέτυχε');
}



function gks_eftpos_fullvoid_request_worldline($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $gks_eftpos_worldline_support_dcc;
  $return=array('success' => false, 'message' => 'generic error');
  
  $transaction_type=$data['transaction_type'];
  //echo '<pre>dataaaaaaa '."\n";print_r($data);die();
  

  $worldline_implementation ='serverapi'; //app2app or serverapi
  if (isset($data['worldline_implementation'])) {
    if (in_array($data['worldline_implementation'],['serverapi','app2app'])) {
      $worldline_implementation=$data['worldline_implementation'];
    }
  }

  $sql="select * from gks_worldline_transaction 
  where id_worldline_transaction=".$data['row_prev_eftpos']['xxx_transaction_id']."
  and trn_id<>''";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'data' => array());}  
  if ($result->num_rows==0) {
    debug_mail(false,'parent record not found',$sql);
    return array('success' => false, 'message' => 'parent record not found', 'data' => array());}  
  
  $row = $result->fetch_assoc();
  $trn_id=$row['trn_id'];
  
  //echo '<pre>dddddddd '.$trn_id;die();
  
  
  if ($worldline_implementation=='serverapi') {
    $url=GKS_WORLDLINE_COM_API.'/v1/payment-intent';
    //echo '<pre>'.$url;die();
  
    $mypost=array();
    $mypost['merchant_bank_id']=GKS_WORLDLINE_COM_API_BANK_ID;
    $mypost['merchant_id_type']='merchant_email';
    $mypost['merchant_id']=$data['row_company']['worldline_username'];
    $mypost['app_language_code']='el';
    $mypost['merchant_terminal_id']=$data['terminalId'];
    $mypost['trn_amount']=number_format($data['amount'],2,'.','');
    $mypost['trn_amount_fix']=true;
    $mypost['trn_currency']='EUR';
  
    $mypost['trn_type']='200000001';
    

    $mypost['trn_receipt_send']=false;
    $mypost['app_receipt_screen']=true;
    $mypost['trn_reference_label']=$data['sessionId'];
    $mypost['remote_payment_activation_mode']=true;
    $mypost['cancel_last_attempt']=true;
    $mypost['trn_host_rrn']=$data['row_prev_eftpos']['transactionId'];;
    $mypost['trn_id']=$trn_id; //$data['row_prev_eftpos']['transactionId'];;
    
  } else if ($worldline_implementation=='app2app') {

/*
{
    "client_id": "fdce1194-cfed-47ca-82f9-77857c6fa7cf",
    "client_secret": "2XuU2LxdrLmW0vdGCUhprxITMinxIJ7oCNjNeyxtSRGuwmz8gXbSH6MxHSx01J00VcWQNdVotN9devmhmN28p2mUR4fpj5jQxtASCAQuuvxRaWWVLsssd8w2w57Efs6s",
    "UID": "93bf6cb5-087d-4b3b-b23d-41a64bab6ad6",
    "login": "merchant_email@example.com",
    "data": {
        "language": "en",
        "edit": false,
        "receipt": true,
        "stay": false,
        "operation": "200000001", //Confirm with your  Acquiring Service Provider that this type of Transaction is supported by them.
        "currency": "EUR", //Input ISO 4217 symbolic currency code here that Merchant support
        "amount": "5.00", //Input Original transaction amount. Format 0.0 is required. Separator character "."
        "rrn": "67867867" //Receipt number (field "trxid" of receipt or field "trn_id" of "transaction.result" Webhook message.
    }
}*/



    $url=GKS_WORLDLINE_COM_API_TOKEN.'/token/token.do';
    //echo '<pre>'.$url;die();
    
    $mypost=array();
    $mypost['client_id']=GKS_WORLDLINE_COM_API_PARTNER_ID;
    $mypost['client_secret']=GKS_WORLDLINE_COM_API_PARTNER_KEY;
    $mypost['UID']=GKS_WORLDLINE_COM_API_BANK_ID;
    $mypost['login']=$data['row_company']['worldline_username'];
    $mypost['check_field']='email';
    $mypost['data']=[];
    $mypost['data']['language']='el';
    $mypost['data']['edit']=false;
    $mypost['data']['receipt']=true;
    $mypost['data']['stay']=false;
    
    $mypost['data']['operation']='200000001';
    
//    $mypost['data']['operation']='100000001';
//    if (in_array($transaction_type,['sale'])) {
//      $mypost['data']['operation']='100000001';
//      if ($data['tipAmount']>0) {$mypost['data']['operation']='300000004'; }
//      else if ($data['installments']>1) $mypost['data']['operation']='300000005'; 
//    } else if (in_array($transaction_type,['saleerp'])) {
//      $mypost['data']['operation']='100000001';
//      if ($data['tipAmount']>0) $mypost['data']['operation']='300000004';
//      else if ($data['installments']>1) $mypost['data']['operation']='300000005'; 
//    } else if (in_array($transaction_type,['refund'])) {
//      $mypost['data']['operation']='200000003';
//    } else if (in_array($transaction_type,['refunderp'])) {
//      $mypost['data']['operation']='200000003';
//    } else {
//      debug_mail(false,gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type,'');
//      return array('success' => false, 
//       'message' => gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type, 
//       'status' =>  gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type);    
//    }
    
    $mypost['data']['currency']='EUR';
    $mypost['data']['amount']=number_format($data['amount'],2,'.','');
    $mypost['data']['rrn']=$data['row_prev_eftpos']['transactionId'];
    $mypost['data']['trn_id']=$trn_id; //$data['row_prev_eftpos']['transactionId'];
    
    $mypost['info_data']=[];
    $mypost['info_data']['custom_ref_label']=$data['sessionId'];
    
    if ($data['tipAmount']>0) $mypost['info_data']['tipAmt']=$data['tipAmount'];
    if ($data['installments']>1) $mypost['info_data']['installmentPeriod']=$data['installments'];

  }

  if ($data['seira_need_signature']) { 
    
    
    if ($worldline_implementation=='serverapi') {
      $mypost['external_data_key']=GKS_WORLDLINE_YLIDA_Key_ID;
      $mypost['externald_data_signature']= $data['aadeProviderSignature'];
      $mypost['external_data_validation']= $data['aadeProviderSignatureData'];

      if ($gks_eftpos_worldline_support_dcc and $mypost['trn_type']=='100000001') {
        $mypost['trn_type']='300000007';
      }
      
    } else if ($worldline_implementation=='app2app') {
      $mypost['info_data']['external_data_key']=GKS_WORLDLINE_YLIDA_Key_ID;
      $mypost['info_data']['externald_data_signature']=$data['aadeProviderSignature'];
      $mypost['info_data']['external_data_validation']=$data['aadeProviderSignatureData'];

      if ($gks_eftpos_worldline_support_dcc and $mypost['data']['operation']=='100000001') {
        $mypost['data']['operation']='300000007';
      }
    }
  }
    
  //print '<pre>';print_r($mypost);die();  
  
  if ($worldline_implementation=='serverapi') {
    $headers = array(
      'Content-Type:application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
      'Authorization: Bearer '. $data['access_token'],
    );
    $mypost_trn_type=$mypost['trn_type'];
    $curl_request='PUT';
  } else if ($worldline_implementation=='app2app') {
    $headers = array(
      'Content-Type:application/json',
      'Accept: application/json',
      'User-Agent: gks ERP/2024',
    );
    $mypost_trn_type=$mypost['data']['operation'];
    $curl_request='POST';
  }
    
  $mypostdata=json_encode($mypost);
  //echo '<pre>postargs '."\n";print_r($headers);print_r($mypost);die();

  $sql="insert into gks_worldline_transaction (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  intentId,
  myStatus,myResult,
  merchant_terminal_id,trn_type,trn_amount,trn_currency,
  worldline_implementation
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App','worldline Api',
  ".$data['id_eftpos_transaction'].",
  '',
  0,0,
  '".$db_link->escape_string($data['terminalId'])."',
  ".$mypost_trn_type.",
  ".$data['amount'].",
  '".$db_link->escape_string('EUR')."',
  '".$db_link->escape_string($worldline_implementation)."'
  )";
  //echo '<pre>'.$sql;die();
  //debug_mail(false,'sql1',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  $id_worldline_transaction = $db_link->insert_id; 
  


  //echo '<pre>sssssssssssaaa ';print_r($mypost);die();
    
  $sql="update gks_eftpos_transaction set
  send_array='".$db_link->escape_string($mypostdata)."',
  xxx_transaction_id=".$id_worldline_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'data' => array());}  
  
  //echo '<pre>'.$url."\n".$data['access_token']."\n".$data['worldline_x_api_key'];die();
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $curl_request);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
  $response = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close($ch);
  

  //debug_mail(false,'response worldline',$response);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_fullvoid_request_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_fullvoid_request_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);

  //echo '<pre>'.$response;die();

  //to $response einai keno otan ginei epitixos
  $response_array=json_decode($response,true);
  
  
  //echo '<pre>sssssssssssaaa ';print_r($response_array);die();

  $has_error=false;
  if ($gks_curl_http_code==200 and 
      is_array($response_array) and 
      isset($response_array['token']) and 
      $response_array['token']!='') {
    //egine ok to post
    $myerror='Pending ...';
    $response_array['Id']=0; 
    $response_array['Status']=1;
    $response_array['Result']=5;    
  } else if ($gks_curl_http_code==502 and 
      is_array($response_array) and
      isset($response_array['code']) and
      $response_array['code']=='0' and
      isset($response_array['status']) and
      $response_array['status']=='502') {
    
    $has_error=true;
    $response=$response_array['message'].'<br>'.gks_lang('Βεβαιωθείτε ότι η εφαρμογή της Worldline είναι ανοιχτή στο τερματικό.');
    $myerror=$response;
    $response_array['token']='';
    $response_array['Id']=0;
    $response_array['Status']=101;
    $response_array['Result']=101;
  } else {
    $has_error=true;
    $response_array=array();
    $response_array['token']='';
    $response_array['Id']=0;
    $response_array['Status']=101;
    $response_array['Result']=101;
    $myerror=$response;
  }
  
  

  
  

  $sql="update gks_eftpos_transaction set 
  transactionId='".$response_array['token']."',
  remote_id='".$db_link->escape_string($response_array['Id'])."',
  ".($has_error ? "transaction_status='agnosto'," : '')."
  mymessage='".$db_link->escape_string($myerror)."'
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}  


  $sql="update gks_worldline_transaction set
  intentId='".$db_link->escape_string($response_array['token'])."',
  myStatus=".$response_array['Status'].",
  myResult=".$response_array['Result'].",
  myerror='".$db_link->escape_string($myerror)."'
  where id_worldline_transaction=".$id_worldline_transaction;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
  //echo '<pre>response ';print_r($response_array); die();
  
  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').'<br>'.$url.'<br>http_code: <b>'.$gks_curl_http_code .'</b><br>'.$response;
    debug_mail(false,'worldline error',$response);return $return;}
  
  if (isset($response_array['token'])==false or trim_gks($response_array['token'])=='') {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').'<br>'.$url.'<br>http_code: <b>'.$gks_curl_http_code .'</b><br>'.$response;
    debug_mail(false,'worldline error',$response);return $return;}
  
  
  return array('success' => true, 'message' => 'OK', 'data' => $response_array); 
  
//    
//  
//  $url=GKS_WORLDLINE_COM_API.'/terminal/'.$data['row_asset']['worldline_id'].'/txnvoid/';
//  //echo '<pre>'.$url;die();
//  
//  
//  $mypost=array();
//  $cur_trn_type=0;
//  $cur_CurrencyCode='978';
//  
//  if (in_array($transaction_type,['fullvoid','fullvoiderp'])) {
//    $cur_trn_type=101;
//    $mypost['OriginalIdentifier']=$data['row_prev_eftpos']['remote_id'];
//    
//    //The type of identifier contained in the OriginalIdentifier field
//    //1: INTENTID - The field contains the field "Id" of the original intent to be voided
//    //2: TRANSACTIONID - The field contains the field "TransactionId" of the original intent to be voided
//    $mypost['OriginalIdentifierType']=1;
//    
//    if (isset($data['row_inv']['user_email']) and empty($data['row_inv']['user_email'])==false) {
//      $mypost['CustomerEmail']=$data['row_inv']['user_email'];
//    }
//    if (isset($data['row_inv']['user_mobile']) and empty($data['row_inv']['user_mobile'])==false) {
//      $mypost['CustomerPhone']=$data['row_inv']['user_mobile'];
//    }
//  
//    //$mypost['InitialTransaction']=''; //Required for pre-auth completion and refund transactions and it should include the TransactionId field of the original transaction  
//    //$mypost['ProviderData']=''; //A data object representing the relevant data required by Greek law to accompany a provider signature based transaction request.
//    //$mypost['EcrTokenData']=''; //A data object representing the relevant data required by Greek law to accompany an ECR token MAC based transaction request.
//    $mypost['Timeout']=0; //If timeout is 0 or not present, the transaction will be initiated asynchronously. If present, the service will wait up to "Timeout" seconds before returning to the caller. Max timeout is 180s   
//  } else {
//    
//    echo '<pre>1111111 transaction_type '.$transaction_type;die();
//    $cur_trn_type=-1;
//  }
//  
//  
//  
//  //echo '<pre>mymymymymmypost '."\n";print_r($mypost);die();
//  
//  if (in_array($transaction_type,['fullvoid','fullvoiderp'])==false) {
//    if ($data['seira_need_signature']) {
//      $mypost['ProviderData']=array(
//        'Uid' =>$data['aadeSignatureUID'],
//        //'Mark' => '',
//        'SignatureTimestamp' => $data['aadeSignatureTimestamp'], //The generation timestamp of ProviderSignature in the same format as in the signature itself, namely YYYYMMDDhhmmss in Greece local time
//        'NetAmount' => round(100*$data['netAmount'],0), // to 16 simenai 0.16; 
//        'VatAmount' => round(100*$data['vatAmount'],0), // to 16 simenai 0.16; 
//        'TotalAmount' => round(100*$data['grossAmount'],0), // to 16 simenai 0.16; 
//        'ProviderId' => $data['aadeProviderId'],
//        'Signature' => $data['aadeProviderSignature'],
//      );
//    }
//  }
//  
//  
//  
//  
//  $headers = array(
//    'Content-Type: application/json',
//    'Accept: application/json',
//    'User-Agent: gks ERP/2024',
//    'Authorization: Bearer '. $data['access_token'],
//    'X-Api-Key: '.$data['worldline_x_api_key'],
//  );
//    
//  $mypostdata=json_encode($mypost);
//  //echo '<pre>postargs '."\n";print_r($headers);print_r($mypost);print_r($data);die();
//  
//  $sql="insert into gks_worldline_transaction (
//  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
//  xeiristis_id,add_from_system,myfrom,
//  eftpos_transaction_id,
//  intentId,
//  myStatus,myResult,
//  TID,
//  trn_type,Amount,TipAmount,CurrencyCode,Instalments,
//  CustomerReference,
//  CustomerEmail,CustomerPhone
//  ) values (
//  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
//  ".$my_wp_user_id.",'gks ERP App','worldline Api',
//  ".$data['id_eftpos_transaction'].",
//  0,
//  0,
//  0,
//  '".$db_link->escape_string($data['terminalId'])."',
//  ".$cur_trn_type.",
//  ".$data['amount'].",
//  ".$data['tipAmount'].",
//  ".$cur_CurrencyCode.",
//  0,
//  '".$db_link->escape_string($data['sessionId'])."',
//  '".$db_link->escape_string((isset($mypost['CustomerEmail']) ? $mypost['CustomerEmail'] : ''))."',
//  '".$db_link->escape_string((isset($mypost['CustomerPhone']) ? $mypost['CustomerPhone'] : ''))."'
//  )";
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
//  $id_worldline_transaction = $db_link->insert_id; 
//  
//
//
//  
//
//  
//  $sql="update gks_eftpos_transaction set
//  send_array='".$db_link->escape_string($mypostdata)."',
//  xxx_transaction_id=".$id_worldline_transaction."
//  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    return array('success' => false, 'message' => 'sql error', 'data' => array());}  
//  
//  $ch = curl_init($url);
//  curl_setopt($ch, CURLOPT_POST, true);
//  curl_setopt($ch, CURLOPT_POSTFIELDS, $mypostdata);
//  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
//  $response = curl_exec($ch);
//  $gks_curl_errno=curl_errno($ch);
//  $gks_curl_info =curl_getinfo($ch);
//  curl_close($ch);
//  
//
//  //debug_mail(false,'response worldline',$response);
//  
//  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
//
//  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_sales_request_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
//  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/worldline_sales_request_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);
//
//
//
//  //to $response einai keno otan ginei epitixos
//  $response_array=json_decode($response,true);
//  if (is_array($response_array) and 
//      isset($response_array['Id'])==false and
//      isset($response_array['id'])) {
//    $response_array['Id']=$response_array['id'];        
//  }  
//  $has_error=false;
//  if ($gks_curl_http_code==200 and 
//      is_array($response_array) and 
//      isset($response_array['Id']) and 
//      intval($response_array['Id'])>0) {
//    //egine ok to post
//    $myerror='Pending ...';
//    
//  } else {
//    $has_error=true;
//    $response_array=array();
//    $response_array['Id']=0;
//    $response_array['Status']=101;
//    $response_array['Result']=101;
//    $myerror=$response;
//  }
//  
//  
//
//  
//  
//
//  $sql="update gks_eftpos_transaction set 
//  remote_id=".$response_array['Id'].",
//  ".($has_error ? "transaction_status='agnosto'," : '')."
//  mymessage='".$db_link->escape_string($myerror)."'
//  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
//  //debug_mail(false,'sql 111111111',$sql);
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}  
//
//
//  $sql="update gks_worldline_transaction set
//  intentId=".$response_array['Id'].",
//  myStatus=".$response_array['Status'].",
//  myResult=".$response_array['Result'].",
//  myerror='".$db_link->escape_string($myerror)."'
//  where id_worldline_transaction=".$id_worldline_transaction;
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
//    
//  //echo '<pre>response ';print_r($response_array); die();
//  
//  if ($gks_curl_http_code!=200) {
//    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code .'<br>'.$response;
//    debug_mail(false,'worldline error',$response);return $return;}
//  
//  
//  return array('success' => true, 'message' => 'OK', 'data' => $response_array); 
    
}
