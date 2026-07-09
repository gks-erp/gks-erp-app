<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
function gks_eftpos_has_transaction_status_epay($id) {
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

function gks_eftpos_has_transaction_result_epay($id) {
  if ($id===null) return '';
  $id=intval($id);
  switch ($id) {
    case 1: return 'APPROVED';
    case 2: return 'DECLINED';
    case 3: return 'CANCELLED';
    case 4: return 'FAILED';
    case 5: return 'UNKNOWN';
    case 6: return 'BUSY';
    case 7: return 'MAX_TRANSACTIONS';
    case 101: return 'GKS_ERROR_SEND';
  }
  return 'ID '.$id;
}

/*Intent result
The possible values for the intent result are:
1: APPROVED - The transaction has been completed and approved by the authorization system
2: DECLINED - The transaction has been completed and declined by the authorization system
3: CANCELLED - The transaction has been cancelled by the POS user before reaching completion
4: FAILED - The transaciton has failed to complete
5: UNKNOWN - The transaction result is unknown. Only possible if the device hasn't responded with results
6: BUSY - The transaction has failed because the POS is currently unavailable for transactions (either processing another transaction or under maintenance)
7: MAX_TRANSACTIONS - The POS device has reached its transaction limit for the specific batch. Batch closing should be performed on the device before continuing transactions
*/

function gks_eftpos_has_transaction_type_epay($id) {
  if ($id===null) return '';
  $id=intval($id);
  switch ($id) {
    case 0: return 'Sale';
    case 1: return 'Refund';
    case 2: return 'Pre-authorisation';
    case 3: return 'Pre-authorisation completion';
    case 4: return 'Mail order/Telephone order';
    case 5: return 'Cash advance';
    case 6: return 'Card payment';
    case 7: return 'Bill payment';
    case 8: return 'Other payment';
    case 9: return 'Pre-payment';
    case 101: return 'Void';
  }
  return 'ID '.$id;
}
  
function gks_eftpos_has_credentials_epay($row_company) {
  $return=array('success' => false, 'message' => 'generic error');
  if (trim_gks($row_company['epay_username'])=='' or trim_gks($row_company['epay_password'])=='' or trim_gks($row_company['epay_authorization_code'])=='' or trim_gks($row_company['epay_x_api_key'])=='') {
    $return['message']=gks_lang('Δεν έχουν ορισθεί τα Διαπιστευτήρια πρόσβασης για την ePay στην εταιρεία').' '.$row_company['company_title'];
    debug_mail(false,$return['message'],print_r($row_company,true));return $return;
  }
  
  return array('success' => true, 'message' => 'OK', 'data' => array(
    'epay_username' => $row_company['epay_username'],
    'epay_password' => $row_company['epay_password'],
    'epay_authorization_code' => $row_company['epay_authorization_code'],
    'epay_x_api_key' => $row_company['epay_x_api_key'],
  ));  
}

function gks_eftpos_get_token_epay($row_company) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');

  $id_company_eftpos=0;
  $need_only_refresh=false;
  
  $sql="select * from gks_company_eftpos 
  where payment_acquirer_with_id=5
  and company_id=".$row_company['id_company']."
  and pc_token_id<>'' 
  and pc_refresh_token_id<>''";
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    $id_company_eftpos=intval($row['id_company_eftpos']);   
    if (time() < (strtotime($row['pc_token_expiration'])-100) and trim_gks($row['pc_token_id'])!='') {
      return array('success' => true, 'message' => 'OK', 'data' => array(
        'access_token' => $row['pc_token_id'],
      ));
    }
    if (time() < (strtotime($row['pc_refresh_token_expiration'])-100) and trim_gks($row['pc_refresh_token_id'])!='') {
      $need_only_refresh=true;
      $pc_refresh_token_id=trim_gks($row['pc_refresh_token_id']);
    }
  }
    
  if ($id_company_eftpos==0) $need_only_refresh=false; //gia sigoura
  
  
  $headers = array(
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: gks ERP/2024',
  );
  if ($need_only_refresh) {
    $url=GKS_EPAY_COM_API.'/token/refresh/';
    $mypost=array();
    $mypost['refresh']=$pc_refresh_token_id;
  } else { 
    $url=GKS_EPAY_COM_API.'/token/';
    $mypost=array();
    $mypost['username']=$row_company['epay_username'];
    $mypost['password']=$row_company['epay_password'];
  }
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

  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code;
    debug_mail(false,$return['message'],print_r($row_company,true));return $return;}

  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    $return['message']=gks_lang('Σφάλμα δεδομένων').' (3) '.$response;
    debug_mail(false,$return['message'],$response);return $return;}
  
 
  if($need_only_refresh) {
    /*Array
    (
        [access] => eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0b2tlbl90eXBlIjoiYWNjZXNzIiwiZXhwIjoxNzIwMTI1OTg4LCJpYXQiOjE3MTk4NDUwNjUsImp0aSI6IjJkNDBkNDRiZmY4ZDQ5ZTBhYjk1NTA1MGVmYTAwMWYwIiwidXNlcl9pZCI6ODQ5fQ._kVwoNDKRzxclBqeVQq7WKHMnSQ_AdQsejCCCAw0Ghs
    )*/    
    if (!(isset($response_array['access']) and trim_gks($response_array['access'])!='')) {
      $return['message']=gks_lang('Σφάλμα δεδομένων').' (4) '.$response;
      debug_mail(false,$return['message'],$response);return $return;}
    $access_token=trim_gks($response_array['access']);
    $pc_token_expiration=date('Y-m-d H:i:s',time() + 3000); // apo 8 ores, alla to evala 1
     
  } else {

    if (!(isset($response_array['access'])  and trim_gks($response_array['access'] )!='' and
          isset($response_array['refresh']) and trim_gks($response_array['refresh'])!='')) {
      $return['message']=gks_lang('Σφάλμα δεδομένων').' (5) '.$response;
      debug_mail(false,$return['message'],$response);return $return;}
      
    $access_token=trim_gks($response_array['access']);
    $pc_token_expiration=date('Y-m-d H:i:s',time() + 3000); // apo 8 ores, alla to evala 8
    $refresh_token=trim_gks($response_array['refresh']);
    $pc_refresh_token_expiration=date('Y-m-d H:i:s',time() + 3000); //exei parametro ???
  }
  
  //$access_token=trim_gks($response_array['access']);
  //$pc_token_expiration=date('Y-m-d H:i:s',time() + 8*60*60); // apo 8 ores, alla to evala 1
  
  if ($id_company_eftpos==0) {
    $sql="insert into gks_company_eftpos (
    user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
    company_id,
    company_sub_id,
    payment_acquirer_with_id,
    pc_token_id,
    pc_token_expiration,
    pc_refresh_token_id,
    pc_refresh_token_expiration
    ) values (
    ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
    ".$row_company['id_company'].",
    0,
    5,
    '".$db_link->escape_string($access_token)."',
    '".$pc_token_expiration."',
    '".$db_link->escape_string($refresh_token)."',
    '".$pc_refresh_token_expiration."'
    
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      $return['message']='error sql';
      debug_mail(false,$return['message'],$sql);return $return;}

      
  } else {
    $sql="update gks_company_eftpos set
    pc_token_id='".$db_link->escape_string($access_token)."',
    pc_token_expiration='".$pc_token_expiration."',";
    if($need_only_refresh==false) {
      $sql.="
      pc_refresh_token_id='".$db_link->escape_string($refresh_token)."',
      pc_refresh_token_expiration='".$pc_refresh_token_expiration."',";
    }
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

function gks_eftpos_sales_request_epay($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');
  
  $transaction_type=$data['transaction_type'];
  //echo '<pre>dataaaaaaa '."\n";print_r($data);die();
  
  $url=GKS_EPAY_COM_API.'/terminal/'.$data['row_asset']['epay_id'].'/txninit/';
  //echo '<pre>'.$url;die();
  
  
  $mypost=array();
  $mypost['TxnType']=999999;
  if (in_array($transaction_type,['sale','saleerp'])) {
    $mypost['TxnType']=0;
  } else if (in_array($transaction_type,['refund','refunderp'])) {
    $mypost['TxnType']=1;
  } else {
    debug_mail(false,gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type,'');
    return array('success' => false, 
    'message' => gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type, 
    'status' =>  gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος').' '.$transaction_type);    
  }
    

  /*TxnType:
  0: sale
  1: refund
  2: Pre-authorisation
  3: Pre-authorisation completion
  4: Mail order/Telephone order
  5: Cash advance
  6: Card payment
  7: Bill payment
  8: Other payment
  9: Pre-payment*/

  $mypost['Amount']=round(100*$data['amount'],0); // to 16 simenai 0.16;
  $mypost['TipAmount']=round(100*$data['tipAmount'],0);
  $mypost['CashbackAmount']=0; //poso epistrofis The cashback amount in integer form. Ex. 1.00 eur is 100
  $mypost['CurrencyCode']=978;
  $mypost['Instalments']=$data['installments'];  

  //$mypost['IsTaxFree']=false;
  //$mypost['PreloadTransaction']=false;
  //$mypost['PreloadExpiration']=0; //integer The expiration of a transaction that is preloaded to a POS, in minutes. The field is mandatory if PreloadTransaction is true
  $mypost['OnBehalfCollection']=false; 
  $mypost['CustomerReference']=$data['sessionId']; //Maximum length of 50 characters
  
  if (isset($data['row_inv']['user_email']) and empty($data['row_inv']['user_email'])==false) {
    $mypost['CustomerEmail']=$data['row_inv']['user_email'];
  }
  if (isset($data['row_inv']['user_mobile']) and empty($data['row_inv']['user_mobile'])==false) {
    $mypost['CustomerPhone']=$data['row_inv']['user_mobile'];
  }

  //$mypost['InitialTransaction']=''; //Required for pre-auth completion and refund transactions and it should include the TransactionId field of the original transaction  
  //$mypost['ProviderData']=''; //A data object representing the relevant data required by Greek law to accompany a provider signature based transaction request.
  //$mypost['EcrTokenData']=''; //A data object representing the relevant data required by Greek law to accompany an ECR token MAC based transaction request.
  $mypost['Timeout']=0; //If timeout is 0 or not present, the transaction will be initiated asynchronously. If present, the service will wait up to "Timeout" seconds before returning to the caller. Max timeout is 180s   
  
  
  
  if (in_array($transaction_type,['refund','refunderp','preauthcompletion','preauthcompletionerp'])) {
    
    $xxx_transaction_id=intval($data['row_prev_eftpos']['xxx_transaction_id']);
    $sql="select * from gks_epay_transaction 
    where id_epay_transaction>0
    and myStatus=3 and myResult=1
    and id_epay_transaction=".$xxx_transaction_id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    if ($result->num_rows==0) {
      debug_mail(false,gks_lang('Δεν βρέθηκε η σχετική γονική εγγραφή').' (2)',$sql);
      return array('success' => false, 
      'message' => gks_lang('Δεν βρέθηκε η σχετική γονική εγγραφή').' (2)', 
      'status' =>  gks_lang('Δεν βρέθηκε η σχετική γονική εγγραφή').' (2)');}
    $row_prev_epay = $result->fetch_assoc();
    
    $mypost['InitialTransaction']=$row_prev_epay['Id'];
  }
  
  if ($data['seira_need_signature']) { 
    $mypost['ProviderData']=array(
      'Uid' =>$data['aadeSignatureUID'],
      //'Mark' => '',
      'SignatureTimestamp' => $data['aadeSignatureTimestamp'], //The generation timestamp of ProviderSignature in the same format as in the signature itself, namely YYYYMMDDhhmmss in Greece local time
      'NetAmount' => round(100*$data['netAmount'],0), // to 16 simenai 0.16; 
      'VatAmount' => round(100*$data['vatAmount'],0), // to 16 simenai 0.16; 
      'TotalAmount' => round(100*$data['grossAmount'],0), // to 16 simenai 0.16; 
      'ProviderId' => $data['aadeProviderId'],
      'Signature' => $data['aadeProviderSignature'],
    );
  }
  
  
  
  //0: CARD PAYMENT
  //1: IRIS
  if (!empty($data['preferred_payment_method'])) {
    if ($data['preferred_payment_method']=='tap') {
      $mypost['PaymentType']=0;
    } else if ($data['preferred_payment_method']=='iris') {
      $mypost['PaymentType']=1;
    }
  }
  
  $headers = array(
    'Content-Type:application/json',
    'Accept: application/json',
    'User-Agent: gks ERP/2024',
    'Authorization: Bearer '. $data['access_token'],
    'X-Api-Key: '.$data['epay_x_api_key'],
  );
    
  $mypostdata=json_encode($mypost);
  //echo '<pre>postargs '."\n";print_r($headers);print_r($mypost);print_r($data);die();
  
  $sql="insert into gks_epay_transaction (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  intentId,
  myStatus,myResult,
  TID,
  TxnType,Amount,TipAmount,CurrencyCode,Instalments,
  CustomerReference,
  CustomerEmail,CustomerPhone
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App','epay Api',
  ".$data['id_eftpos_transaction'].",
  '',
  0,0,
  '".$db_link->escape_string($data['terminalId'])."',
  ".$mypost['TxnType'].",
  ".$data['amount'].",
  ".$data['tipAmount'].",
  ".$mypost['CurrencyCode'].",
  ".$mypost['Instalments'].",
  '".$db_link->escape_string($mypost['CustomerReference'])."',
  '".$db_link->escape_string((isset($mypost['CustomerEmail']) ? $mypost['CustomerEmail'] : ''))."',
  '".$db_link->escape_string((isset($mypost['CustomerPhone']) ? $mypost['CustomerPhone'] : ''))."'
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  $id_epay_transaction = $db_link->insert_id; 
  


  

  
  $sql="update gks_eftpos_transaction set
  send_array='".$db_link->escape_string($mypostdata)."',
  xxx_transaction_id=".$id_epay_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'data' => array());}  
  
  //echo '<pre>'.$url."\n".$data['access_token']."\n".$data['epay_x_api_key'];die();
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
  

  //debug_mail(false,'response epay',$response);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/epay_sales_request_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/epay_sales_request_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);



  //to $response einai keno otan ginei epitixos
  $response_array=json_decode($response,true);
  
  if (is_array($response_array) and 
      isset($response_array['Id'])==false and
      isset($response_array['id'])) {
    $response_array['Id']=$response_array['id'];        
  }
  
  $has_error=false;
  if ($gks_curl_http_code==200 and 
      is_array($response_array) and 
      isset($response_array['Id']) and 
      intval($response_array['Id'])>0) {
    //egine ok to post
    $myerror='Pending ...';
    
  } else {
    $has_error=true;
    $response_array=array();
    $response_array['Id']=0;
    $response_array['Status']=101;
    $response_array['Result']=101;
    $myerror=$response;
  }
  
  

  
  

  $sql="update gks_eftpos_transaction set 
  remote_id='".$db_link->escape_string($response_array['Id'])."',
  ".($has_error ? "transaction_status='agnosto'," : '')."
  mymessage='".$db_link->escape_string($myerror)."'
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}  


  $sql="update gks_epay_transaction set
  intentId='".$db_link->escape_string($response_array['Id'])."',
  myStatus=".$response_array['Status'].",
  myResult=".$response_array['Result'].",
  myerror='".$db_link->escape_string($myerror)."'
  where id_epay_transaction=".$id_epay_transaction;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
  //echo '<pre>response ';print_r($response_array); die();
  
  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code .'<br>'.$response;
    debug_mail(false,'epay error',$response);return $return;}
  
  
  return array('success' => true, 'message' => 'OK', 'data' => $response_array); 
    
}

/*
Transaction type
The possible values for the transaction type are:
0: sale
1: refund
2: Pre-authorisation
3: Pre-authorisation completion
4: Mail order/Telephone order
5: Cash advance
6: Card payment
7: Bill payment
8: Other payment
9: Pre-payment
Transaction types from 0 to 49 are reserved for future use. Values over 50 are allowed to be used in custom
implementations.



*/

function gks_eftpos_sales_request_get_status_epay($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error' , 'status'=>'agnosto');
  $row_company=$data['row_company'];
  $row_tra=$data['row_tra'];
  $transaction_type=$data['row_tra']['transaction_type'];
  
  if (in_array($transaction_type,['fullvoid','fullvoiderp'])) {
    $sql="SELECT gks_eftpos_transaction_thisisfor.my_for, gks_epay_transaction.TransactionId_org
    FROM (gks_eftpos_transaction_thisisfor 
    LEFT JOIN gks_eftpos_transaction ON gks_eftpos_transaction_thisisfor.my_for = gks_eftpos_transaction.id_eftpos_transaction) 
    LEFT JOIN gks_epay_transaction ON gks_eftpos_transaction.xxx_transaction_id = gks_epay_transaction.id_epay_transaction
    WHERE gks_eftpos_transaction_thisisfor.my_this=".$row_tra['id_eftpos_transaction']."
    AND gks_eftpos_transaction_thisisfor.my_is='".$transaction_type."'
    AND gks_epay_transaction.TransactionId_org<>''";
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
    
    $url=GKS_EPAY_COM_API.'/transactionintent/?TransactionId='.rawurlencode($prev_TransactionId_org);
  } else {
    $url=GKS_EPAY_COM_API.'/transactionintent/?CustomerReference='.$data['sessionId'];
  }
  
//  $url=GKS_EPAY_COM_API.'/transactionintent/'.
//  '?TID='.
//  $data['row_tra']['terminalId'].
//  '&Timestamp_min='.
//  date('Y-m-d\TH:i:s\Z', strtotime($data['row_tra']['mydate_add'])-10*60); //2024-07-01T15:19:48Z


  //echo '<pre>'.$url."\n";print_r($data);die();
  
  $headers = array(
    'Content-Type:application/json',
    'Accept: application/json',
    'User-Agent: gks ERP/2024',
    'Authorization: Bearer '. $data['access_token'],
    'X-Api-Key: '.$row_company['epay_x_api_key'],
  );
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
  $response = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close($ch);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/epay_status_send_s1_'.time().'.json',$url);
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/epay_status_response_s1_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);


  
  //debug_mail(false,'response',$gks_curl_http_code.' '.$response);
  //return array('success' => true, 'message' => 'OK', 'status' => 'wait');
  

  
  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code .'<br>'.$response;
    debug_mail(false,'viva error',$response);return $return;}

  $response_array = json_decode($response, true);
  if (!(is_array($response_array) and 
        isset($response_array['count']) and $response_array['count']>=1 and
        isset($response_array['results']) and count($response_array['results'])>=1)) {
    return array('success' => true, 'message' => 'OK', 'status' => 'wait'); 
  }
  
  
  $remote_id=trim_gks($data['row_tra']['remote_id']);
  
  $myresult=false;
  if ($remote_id!='') {
    foreach ($response_array['results'] as $res_val) {
      if (isset($res_val['Id']) and trim_gks($res_val['Id'])==$remote_id) {
        $myresult=$res_val;
        break;
      }
    } 
  }
  if ($myresult===false) {
    return array('success' => true, 'message' => 'OK111', 'status' => 'wait'); 
    
  }
  

  $r_Status=-1;  if (isset($myresult['Status'])) $r_Status=intval($myresult['Status']);
  $r_Result=-1;  if (isset($myresult['Result'])) $r_Result=intval($myresult['Result']);
  
/*Intent status
The possible values for the intent status are:
1: PENDING - Intent has been registered to the backend and is pending to be sent to the device
2: SENT - Intent has been sent to the device
3: COMPLETED - Intent has been successfully completed by the device and has registered the results
*/
  
  
  if ($r_Status==1 or $r_Status<1 or $r_Status>3) { //PENDING
    $intentId=trim_gks($myresult['Id']);
    if ($intentId!='') {
      $sql="update gks_epay_transaction set 
      myStatus=".$r_Status.",
      myResult=".$r_Result.",
      myerror='Pending'
      where intentId='".$db_link->escape_string($intentId)."'";
      //debug_mail(false,'sql 1',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    }
    return array('success' => true, 'message' => 'OK', 'status' => 'wait'); 
  }
  if ($r_Status==2) { //SENT
    $intentId=trim_gks($myresult['Id']);
    if ($intentId!='') {
      $sql="update gks_epay_transaction set 
      myStatus=".$r_Status.",
      myResult=".$r_Result.",
      myerror='Send'
      where intentId='".$db_link->escape_string($intentId)."'";
      //debug_mail(false,'sql 2',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    }
    return array('success' => true, 'message' => 'OK', 'status' => 'processed'); 
  }
  
  //COMPLETED

  //echo '<pre>';print_r($data);die();
  $id_eftpos_transaction=0;
  $paroxos_signature_id=0;
  $sql="select paroxos_signature_id,acc_inv_payment_id,acc_pay_payment_id 
  from gks_eftpos_transaction 
  where sessionId='".$db_link->escape_string($data['sessionId'])."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc(); 
    $id_eftpos_transaction=intval($row['id_eftpos_transaction']);
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
    
  
  if (in_array($r_Result,[2,3,4,5,6,7])) {//ola ektos APPROVED
    
    
    
    $transaction_status='abort'; $ret_message='general error';
    if ($r_Result==2) {$transaction_status='abort';   $ret_message='The transaction has been completed and declined by the authorization system';}
    if ($r_Result==3) {$transaction_status='canceled';$ret_message='The transaction has been cancelled by the POS user before reaching completion';}
    if ($r_Result==4) {$transaction_status='abort';   $ret_message='The transaciton has failed to complete';}
    if ($r_Result==5) {$transaction_status='abort';   $ret_message='The transaction result is unknown. Only possible if the device hasn\'t responded with results';}
    if ($r_Result==6) {$transaction_status='abort';   $ret_message='The transaction has failed because the POS is currently unavailable for transactions (either processing another transaction or under maintenance)';}
    if ($r_Result==7) {$transaction_status='abort';   $ret_message='The POS device has reached its transaction limit for the specific batch. Batch closing should be performed on the device before continuing transactions';}

  
    $sql="update gks_eftpos_transaction set 
    transaction_status='".$transaction_status."',
    mymessage='".$db_link->escape_string($ret_message)."',
    user_id_edit=".$my_wp_user_id.",
    mydate_edit=now(),
    myip='".$db_link->escape_string($gkIP)."'
    where payment_acquirer_with_id=5 
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
      WHERE gks_eftpos_transaction.payment_acquirer_with_id=5
      AND gks_eftpos_transaction.transaction_status<>'done'
      AND gks_eftpos_transaction.sessionId='".$db_link->escape_string($data['sessionId'])."'";
      //debug_mail(false,'sql 4',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    }
    
    $intentId=trim_gks($myresult['Id']);
    if ($intentId!='') {
      $sql="update gks_epay_transaction set 
      myStatus=".$r_Status.",
      myResult=".$r_Result.",
      myerror='".$db_link->escape_string($ret_message)."'
      where intentId='".$db_link->escape_string($intentId)."'";
      //debug_mail(false,'sql 5',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    }
    
    // apo canreuse to kano canceled
    if ($paroxos_signature_id>0) {
      $sql="update gks_paroxos_signature set 
      signature_status='canceled',mydate_edit=now()
      where id_paroxos_signature=".$paroxos_signature_id."
      and signature_status in ('assign')";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    }
        
    
    
    return array('success' => true, 'message' => $ret_message, 'status' => $transaction_status);
  }
  
  if ($r_Result!=1) { //den einai APPROVED
    
    $intentId=trim_gks($myresult['Id']);
    if ($intentId!='') {
      $sql="update gks_epay_transaction set 
      myStatus=".$r_Status.",
      myResult=".$r_Result.",
      myerror='agnosto'
      where intentId='".$db_link->escape_string($intentId)."'";
      //debug_mail(false,'sql 6',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    }    
    
    return array('success' => false, 'message' => 'sql error', 'status' => 'agnosto');
  }

  $transaction_number=''; if (isset($myresult['Transaction'])) $transaction_number=$myresult['Transaction'];
  $aadeTransactionId='';if (isset($myresult['TransactionId'])) $aadeTransactionId=$myresult['TransactionId'];
  
  //transactionId='".$db_link->escape_string($transactionId)."',
  $sql="update gks_eftpos_transaction set 
  transaction_status='done',
  aadeTransactionId='".$db_link->escape_string($aadeTransactionId)."',
  response_array='".$db_link->escape_string(json_encode($myresult))."',
  mymessage='Read transaction',
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."'
  where payment_acquirer_with_id=5 
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
  
  $TransactionId_org='';if (isset($myresult['TransactionId'])) $TransactionId_org=$myresult['TransactionId'];
  
  
  $url=GKS_EPAY_COM_API.'/transaction/'.
  '?TID='.
  $data['row_tra']['terminalId'].
  '&Timestamp_min='.
  date('Y-m-d\TH:i:s\Z', strtotime($data['row_tra']['mydate_add'])-10*60); //2024-07-01T15:19:48Z
  //echo '<pre>'.$url;die();
  
  
  $headers = array(
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: gks ERP/2024',
    'Authorization: Bearer '. $data['access_token'],
    'X-Api-Key: '.$row_company['epay_x_api_key'],
  );
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
  $response = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close($ch);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/epay_status_send_s2_'.time().'.json',$url);
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/epay_status_response_s2_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);


  $transactionId='';
  if ($gks_curl_http_code==200) {
    $response_array = json_decode($response, true);
    if (is_array($response_array) and 
        isset($response_array['count']) and $response_array['count']>=1 and
        isset($response_array['results']) and count($response_array['results'])>=1) {


      foreach ($response_array['results'] as $index => $resitem) {
        if ($resitem['Id']==$transaction_number) {
          //echo '<pre>';var_dump($resitem);die();
          
          if (isset($resitem['RRN'])) $transactionId=$resitem['RRN'];
          
          $new_fileds=[];
          $sqlF=array();$sqlV=array();  $sqlU=array();     
          $fab=array('Approved','Voided');
          $fai=array('TxnType','STAN','BatchNumber','Instalments','PosEntryMode','CurrencyCode','PaymentType');
          $faf=array('Amount','DccAmount','TipAmount','CashbackAmount','LoyaltyRedemptionAmount');
          $fad=array('Timestamp','VoidTimestamp');
          $fas=array('Id','ExternalId','CardPAN','CardHash','Batch','Acquirer','TID','MID',
          'Cryptogram','HostResponseCode','RRN','AuthCode','OriginalRRN','OriginalAuthCode',
          'DccCurrencyCode','CustomerReference','CardType');
          
          
          foreach ($resitem as $key => $rtr) {
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
                $sqlV[]=intval($rtr)/100;
                $sqlU[]=$key."=".intval($rtr)/100;
              } else if (in_array($key,$fad)) {
                $sqlF[]=$key;
                $sqlV[]="'".date('Y-m-d H:i:s',strtotime($rtr))."'";
                $sqlU[]=$key."='".date('Y-m-d H:i:s',strtotime($rtr))."'";
              } else if (in_array($key,$fas)) {
                $sqlF[]=$key;
                $sqlV[]="'".$db_link->escape_string($rtr)."'";
                $sqlU[]=$key."='".$db_link->escape_string($rtr)."'";
              } else {
                $new_fileds[]=$key;
              }
            }
          }
          
//          $CustomerEmail='';$CustomerPhone='';$r_error='';
//          
//          
//          $sql="select send_array,mymessage from gks_eftpos_transaction
//          where id_eftpos_transaction=".$data['row_tra']['id_eftpos_transaction'];
//          $result = $db_link->query($sql);        
//          if (!$result) {
//            debug_mail(false,'error sql',$sql);
//            //echo 'error:sql error'; die();
//          } else {
//            if ($result->num_rows==1) {
//              $row = $result->fetch_assoc();
//              $r_error=trim_gks($row['mymessage']);
//              $send_array=trim_gks($row['send_array']);
//              if ($send_array!='') {
//                $send_array=json_decode($send_array,true);
//                if (is_array($send_array)) {
//                  if (isset($send_array['CustomerEmail'])) $CustomerEmail=$send_array['CustomerEmail'];
//                  if (isset($send_array['CustomerPhone'])) $CustomerPhone=$send_array['CustomerPhone'];
//                  
//                } 
//              }
//              
//            } 
//            
//          }
          
          $company_id=0;
          $epay_mid=''; if (isset($resitem['MID'])) $epay_mid=trim_gks($resitem['MID']);
          if ($epay_mid!='') {
            $sql="select id_company from gks_company where company_disable=0 and epay_mid='".$db_link->escape_string($epay_mid)."'";
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
          
          $intentId=trim_gks($myresult['Id']);
          if ($intentId!='') {
            $sql="update gks_epay_transaction set 
            myStatus=".$r_Status.",
            myResult=".$r_Result.",
            TransactionId_org='".$db_link->escape_string($TransactionId_org)."',
            ".implode(',',$sqlU).",
            myjson='".$db_link->escape_string(json_encode($resitem))."',
            myerror='Finish',
            user_id_edit=".$my_wp_user_id.",
            mydate_edit=now(),
            myip='".$db_link->escape_string($gkIP)."'
            where intentId='".$db_link->escape_string($intentId)."'";
            //debug_mail(false,'sql 9',$sql);
            $result = $db_link->query($sql);
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
                      
            
          } else {
            
            $sql="insert into gks_epay_transaction (
            user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
            xeiristis_id,add_from_system,myfrom,
            myStatus,myResult,
            TransactionId_org,
            ".implode(',',$sqlF).",
            myjson,
            myerror
            ) values (
            ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
            ".$my_wp_user_id.",'gks ERP App','epay Api',
            ".$r_Status.",".$r_Result.",
            '".$db_link->escape_string($TransactionId_org)."',
            ".implode(',',$sqlV).",
            '".$db_link->escape_string(json_encode($resitem))."',
            'Finish'
            )";
            //debug_mail(false,'sql 10',$sql);
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
            
            
            $id_epay_transaction = $db_link->insert_id;
              
            $sql="update gks_eftpos_transaction set
            xxx_transaction_id=".$id_epay_transaction.",
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
            debug_mail(false,'new fields in epay.php',print_r($new_fileds,true)."\n".print_r($resitem,true));
          }
        
          break;  
        }
        
      } 
    
    
    }
    
    $sql="update gks_eftpos_transaction set 
    transactionId='".$db_link->escape_string($transactionId)."'
    where payment_acquirer_with_id=5 
    AND transaction_status='done'
    AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
    //debug_mail(false,'sql 12',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
    if (in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) {
      
      
      $sql="select * from gks_eftpos_transaction
      where payment_acquirer_with_id=5 
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
              where payment_acquirer_with_id=5 
              AND transaction_status='done'
              AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
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
    
    gks_eftpos_set_payment_via_iris($id_eftpos_transaction);
  }
  
  
  return array('success' => true, 'message' => 'OK', 'status' => 'done');
    
  echo '<pre>'.$url."\n";print_r($response_array);die();
  
    
  

    
}

function gks_eftpos_get_transaction_extra_html_epay($input) {
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
  
  $sql="select * from gks_epay_transaction 
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
    if (!empty($row['Timestamp'])) $return['transaction']['donedate']=$row['Timestamp'];
    
    $amount=floatval($row['Amount']);
    if ($amount!=0) $return['transaction']['amount']=$amount;
        
    //$tipamount=floatval($row['TipAmount']);
    //if ($tipamount!=0) $return['transaction']['tipamount']=$tipamount;
        
    $html=[];
    if (!empty($row['TransactionId_org'])) $html[]='TransactionId-2: '.$row['TransactionId_org'];
    if (!empty($row['TipAmount'])) $html[]='TipAmount: <b>'.myCurrencyFormat($row['TipAmount']).'</b>';
    
    
    
    //if (!empty($row['TotalCommission'])) $html[]=gks_lang('Προμήθεια').': '.myCurrencyFormat(floatval($row['TotalCommission']));
    //if (!empty($row['TotalFee'])) $html[]=gks_lang('Τέλος').': '.myCurrencyFormat(floatval($row['TotalFee']));
    
    if (!empty($row['Approved']) and $row['Approved']==1) {
      $html[]='Approved: <span class="gks_epay_transaction_approved">Approved</span>';
    } else {
      $html[]='Approved: <span class="gks_epay_transaction_not_approved">Not Approved</span>';
    }
    if (!empty($row['Voided']) and $row['Voided']==1) {
      $html[]='Voided: <span class="gks_epay_transaction_voided">Voided</span>';
    } 
    
    
    //if (!empty($row['TxnType'])) {
      $html[]=gks_lang('Τύπος').': <b>'. gks_eftpos_has_transaction_type_epay($row['TxnType']).'</b>';
    //} 

      
    //$html[]=gks_lang('Αναφορά Πληρωμής').': <span title="'.$row['MerchantTrns'].'"><i class="fas fa-exclamation-circle"></i></span>';
    $html[]='MID: '.$row['MID'].'</span>';
    $html[]='TID: '.$row['TID'].'</span>';
    //if (isset($row['Latitude']) and isset($row['Longitude'])) {
    //  $html[]=gks_lang('Στίγμα').': '.
    //  '<a href="https://www.google.com/maps/search/?api=1&query='.$row['Latitude'].','.$row['Longitude'].'" target="_blank"><i class="fas fa-map-marker-alt gks_marker_gps"></i></a>';
    //}

    //if (!empty($row['referenceNumber'])) $html[]=gks_lang('Κωδικός Αναφοράς').': '.$row['referenceNumber'];
    //if (!empty($row['cardType'])) $html[]=gks_lang('Τύπος Κάρτας').': '.$row['cardType'];
    if (!empty($row['applicationName'])) $html[]=gks_lang('Εφαρμογή').': '.$row['applicationName'];
    if (!empty($row['CardPAN'])) $html[]=gks_lang('Κάρτα').': '.$row['CardPAN'];
    
    if ($row['PaymentType']==1) $temp='IRIS';
    else $temp=$row['CardType'];
    $html[]=gks_lang('Τύπος Κάρτας').': <b class="epay_tra_PaymentTypeCardType epay_tra_PaymentType_'.$row['PaymentType'].' epay_tra_CardType_'.str_replace(' ','_',$row['CardType']).'">'.$temp.'</b>';
    
    if (!empty($row['CustomerEmail'])) $html[]=gks_lang('email').': <a href="mailto:'.$row['CustomerEmail'].'">'.$row['CustomerEmail'].'</a>';
    if (!empty($row['CustomerPhone'])) $html[]=gks_lang('Τηλέφωνο').': <a href="tel:'.$row['CustomerPhone'].'">'.$row['CustomerPhone'].'</a>';
    if (!empty($row['Acquirer'])) $html[]=gks_lang('Τράπεζα').': '.$row['Acquirer'];
    if (!empty($row['STAN'])) $html[]='STAN: '.$row['STAN'];
    if (!empty($row['BatchNumber'])) $html[]='BatchNumber: '.$row['BatchNumber'];
    if (!empty($row['Batch'])) $html[]='Batch: '.$row['Batch'];
    if (!empty($row['RRN'])) $html[]='RRN: '.$row['RRN'];
    if (!empty($row['AuthCode'])) $html[]='AuthCode: '.$row['AuthCode'];
    if (!empty($row['OriginalRRN'])) $html[]='OriginalRRN: '.$row['OriginalRRN'];
    if (!empty($row['OriginalAuthCode'])) $html[]='OriginalAuthCode: '.$row['OriginalAuthCode'];
    if (!empty($row['CurrencyCode'])) $html[]='CurrencyCode: '.$row['CurrencyCode'];
    if (!empty($row['DccCurrencyCode'])) $html[]='DccCurrencyCode: '.$row['DccCurrencyCode'];
    if (!empty($row['CustomerReference'])) $html[]='CustomerReference: '.$row['CustomerReference'];
    
    
    

    $html[]='<a href="admin-epay-transaction-raw.php?id='.$input['id_eftpos_transaction'].'" target="_blank" title="Raw Data"><i class="fas fa-database gks_payment_rawdata"></i></a>'.
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

function gks_eftpos_fullvoid_request_epay($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');
  
  $transaction_type=$data['transaction_type'];
  //echo '<pre>dataaaaaaa '."\n";print_r($data);die();
  
  $url=GKS_EPAY_COM_API.'/terminal/'.$data['row_asset']['epay_id'].'/txnvoid/';
  //echo '<pre>'.$url;die();
  
  
  $mypost=array();
  $cur_TxnType=0;
  $cur_CurrencyCode='978';
  
  if (in_array($transaction_type,['fullvoid','fullvoiderp'])) {
    $cur_TxnType=101;
    $mypost['OriginalIdentifier']=trim_gks($data['row_prev_eftpos']['remote_id']);
    
    //The type of identifier contained in the OriginalIdentifier field
    //1: INTENTID - The field contains the field "Id" of the original intent to be voided
    //2: TRANSACTIONID - The field contains the field "TransactionId" of the original intent to be voided
    $mypost['OriginalIdentifierType']=1;
    
    if (isset($data['row_inv']['user_email']) and empty($data['row_inv']['user_email'])==false) {
      $mypost['CustomerEmail']=$data['row_inv']['user_email'];
    }
    if (isset($data['row_inv']['user_mobile']) and empty($data['row_inv']['user_mobile'])==false) {
      $mypost['CustomerPhone']=$data['row_inv']['user_mobile'];
    }
  
    //$mypost['InitialTransaction']=''; //Required for pre-auth completion and refund transactions and it should include the TransactionId field of the original transaction  
    //$mypost['ProviderData']=''; //A data object representing the relevant data required by Greek law to accompany a provider signature based transaction request.
    //$mypost['EcrTokenData']=''; //A data object representing the relevant data required by Greek law to accompany an ECR token MAC based transaction request.
    $mypost['Timeout']=0; //If timeout is 0 or not present, the transaction will be initiated asynchronously. If present, the service will wait up to "Timeout" seconds before returning to the caller. Max timeout is 180s   
  } else {
    
    echo '<pre>1111111 transaction_type '.$transaction_type;die();
    $cur_TxnType=-1;
  }
  
  
  
  //echo '<pre>mymymymymmypost '."\n";print_r($mypost);die();
  
  if (in_array($transaction_type,['fullvoid','fullvoiderp'])==false) {
    if ($data['seira_need_signature']) {
      $mypost['ProviderData']=array(
        'Uid' =>$data['aadeSignatureUID'],
        //'Mark' => '',
        'SignatureTimestamp' => $data['aadeSignatureTimestamp'], //The generation timestamp of ProviderSignature in the same format as in the signature itself, namely YYYYMMDDhhmmss in Greece local time
        'NetAmount' => round(100*$data['netAmount'],0), // to 16 simenai 0.16; 
        'VatAmount' => round(100*$data['vatAmount'],0), // to 16 simenai 0.16; 
        'TotalAmount' => round(100*$data['grossAmount'],0), // to 16 simenai 0.16; 
        'ProviderId' => $data['aadeProviderId'],
        'Signature' => $data['aadeProviderSignature'],
      );
    }
  }
  
  
  
  
  $headers = array(
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: gks ERP/2024',
    'Authorization: Bearer '. $data['access_token'],
    'X-Api-Key: '.$data['epay_x_api_key'],
  );
    
  $mypostdata=json_encode($mypost);
  //echo '<pre>postargs '."\n";print_r($headers);print_r($mypost);print_r($data);die();
  
  $sql="insert into gks_epay_transaction (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  intentId,
  myStatus,myResult,
  TID,
  TxnType,Amount,TipAmount,CurrencyCode,Instalments,
  CustomerReference,
  CustomerEmail,CustomerPhone
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App','epay Api',
  ".$data['id_eftpos_transaction'].",
  '',
  0,0,
  '".$db_link->escape_string($data['terminalId'])."',
  ".$cur_TxnType.",
  ".$data['amount'].",
  ".$data['tipAmount'].",
  ".$cur_CurrencyCode.",
  0,
  '".$db_link->escape_string($data['sessionId'])."',
  '".$db_link->escape_string((isset($mypost['CustomerEmail']) ? $mypost['CustomerEmail'] : ''))."',
  '".$db_link->escape_string((isset($mypost['CustomerPhone']) ? $mypost['CustomerPhone'] : ''))."'
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  $id_epay_transaction = $db_link->insert_id; 
  


  

  
  $sql="update gks_eftpos_transaction set
  send_array='".$db_link->escape_string($mypostdata)."',
  xxx_transaction_id=".$id_epay_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'data' => array());}  
  
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
  

  //debug_mail(false,'response epay',$response);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/epay_sales_request_send_'.time().'.json',$url."\n".json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/epay_sales_request_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);



  //to $response einai keno otan ginei epitixos
  $response_array=json_decode($response,true);
  if (is_array($response_array) and 
      isset($response_array['Id'])==false and
      isset($response_array['id'])) {
    $response_array['Id']=$response_array['id'];        
  }  
  $has_error=false;
  if ($gks_curl_http_code==200 and 
      is_array($response_array) and 
      isset($response_array['Id']) and 
      intval($response_array['Id'])>0) {
    //egine ok to post
    $myerror='Pending ...';
    
  } else {
    $has_error=true;
    $response_array=array();
    $response_array['Id']=0;
    $response_array['Status']=101;
    $response_array['Result']=101;
    $myerror=$response;
  }
  
  

  
  

  $sql="update gks_eftpos_transaction set 
  remote_id='".$db_link->escape_string($response_array['Id'])."',
  ".($has_error ? "transaction_status='agnosto'," : '')."
  mymessage='".$db_link->escape_string($myerror)."'
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  //debug_mail(false,'sql 111111111',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}  


  $sql="update gks_epay_transaction set
  intentId='".$db_link->escape_string($response_array['Id'])."',
  myStatus=".$response_array['Status'].",
  myResult=".$response_array['Result'].",
  myerror='".$db_link->escape_string($myerror)."'
  where id_epay_transaction=".$id_epay_transaction;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    
  //echo '<pre>response ';print_r($response_array); die();
  
  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code .'<br>'.$response;
    debug_mail(false,'epay error',$response);return $return;}
  
  
  return array('success' => true, 'message' => 'OK', 'data' => $response_array); 
    
}