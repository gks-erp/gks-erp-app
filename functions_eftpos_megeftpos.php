<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_eftpos_has_transaction_status_megeftpos($id) {
  if ($id===null) return '';
  
  switch ($id) {
    case 'draft': return gks_lang('Πρόχειρη','part4','gks_eftpos_has_transaction_status_megeftpos');
    case 'send':  return gks_lang('Στάλθηκε','part4','gks_eftpos_has_transaction_status_megeftpos');
    case 'fail':  return gks_lang('Απέτυχε','part4','gks_eftpos_has_transaction_status_megeftpos');
    case 'done':  return gks_lang('Ολοκληρώθηκε','part4','gks_eftpos_has_transaction_status_megeftpos');
  }
  return 'ID '.$id;
}

function gks_eftpos_has_transaction_result_megeftpos($id) {
  if ($id===null) return '';
  
  switch ($id) {
    case 0: return 'APPROVED';
	  case 1: return 'DECLINED';
	  case 2: return 'CANCELLED';
	  case 3: return 'FAILED';
	  case 4: return 'UNKNOWN';
	  case 5: return 'BUSY';
	  case 6: return 'MAX_TRANSACTIONS';
	  case 7: return 'ACTION_REQUIRED';
	  case 8: return 'COMMUNICATION_ERROR';
  }
  return 'ID '.$id;
}

function guid_for_megeftpos_pos_id($not_in = array()) {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $guid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
    $guid = strtolower($guid);
    
    if (!(is_array($not_in) and count($not_in) and in_array($guid, $not_in))) {
      
      $sql = "SELECT megeftpos_pos_id from gks_assets where megeftpos_pos_id='".$db_link->escape_string($guid)."'";
      $result = $db_link->query($sql);
      
      if ($result->num_rows == 0) {
        return $guid; 
      }
    }
  }
}


function gks_eftpos_build_json_for_send_ping_terminal_megeftpos($input) {
  $ret = array('success' => false, 'message' => 'generic error ping_megeftpos');
  $row_asset=$input['row_asset'];
  $ret['success']=true;
  $ret['message']='OK';
  $ret['send_data_array']=array(
    'ip' => $input['row_asset']['megeftpos_static_ip'],
    'port' => intval($input['row_asset']['megeftpos_port']),
  );
  $ret['megeftpos_ecr2eftweb_service_url']=$row_asset['megeftpos_ecr2eftweb_service_url'];



  //echo '<pre>dddddddddddd';print_r($ret);die();
  return $ret;
}

function gks_eftpos_sales_request_megeftpos($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');
  


  //echo '<pre>dddddddddddd d ';print_r($data);die();
  
   
  
  $mypost=array();


  
  $mypost['transactionData']=array(
    'posId' => $data['row_asset']['megeftpos_pos_id'],
    'host' => $data['row_asset']['megeftpos_static_ip'],
    'port' => $data['row_asset']['megeftpos_port'],
    'apiKey' => $data['row_asset']['megeftpos_api_key'],
    'protocol' => intval($data['row_asset']['megeftpos_protocol']),
    'terminal_id' => $data['row_asset']['megeftpos_terminal_id'],
    'uniqueTxnId' => $data['my_uniqueTxnId'],
    'amount'=>number_format($data['amount'],2,'.',''),
    'tipAmount'=>number_format($data['tipAmount'],2,'.',''),
    'cashier' => $data['cashRegisterId'],
    'installments'=>$data['installments'],
    
  );
  

  $api_call='sale';
  if ($data['seira_need_signature']) {
    $api_call='saleerp';
    $mypost['transactionData']['signature']=$data['aadeProviderSignature'];
    $mypost['transactionData']['signatureData']=$data['aadeProviderSignatureData'];
    $mypost['transactionData']['providersId']=$data['aadeProviderId'];
    $mypost['transactionData']['uid']=$data['aadeSignatureUID'];
    $mypost['transactionData']['dateTimeProviderSignature']=$data['aadeSignatureTimestamp'];
    $mypost['transactionData']['amountPayable']=floatval($data['amount']).'';
    $mypost['transactionData']['netValue']=floatval($data['netAmount']).'';
    $mypost['transactionData']['vatValue']=floatval($data['vatAmount']).'';
    $mypost['transactionData']['totalValue']=floatval($data['grossAmount']).'';
  }
   
  if ($data['seira_need_signature'] and strlen($mypost['transactionData']['dateTimeProviderSignature'])!=14) {
    $return['message']=gks_lang('Δεν έχει ορισθεί η χρονική σφραγίδα της υπογραφής');
    debug_mail(false,$return['message'],print_r($mypost,true));return $return;}    
    
  if ($mypost['transactionData']['posId']=='') {
    $return['message']=gks_lang('Δεν έχει ορισθεί POS ID για αυτό το πάγιο');
    debug_mail(false,$return['message'],print_r($mypost,true));return $return;}    
  
  
  $params=array(
    'id' => $data['row_asset']['megeftpos_erp_app_id'],
    'cmd' => 'megeftpos_run_command',
    'asset_id' => $data['row_asset']['id_asset'],
    'api_call' => $api_call,
    'send_data_array' => $mypost,
    'megeftpos_ecr2eftweb_service_url' => $data['row_asset']['megeftpos_ecr2eftweb_service_url'],
    'id_eftpos_transaction' => $data['id_eftpos_transaction'],
  );

  //echo '<pre>dddddddddddd params ';print_r($params);die();
  
  $r_error='wait';
  $sql="insert into gks_megeftpos_transaction (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  trans_status,
  originalAmount,
  paidAmount,
  tipAmount,
  mymessage,
  transactionType,
  trans_type,
  eftTerminalId,
  installments
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App Desktop','megeftpos',
  ".$data['id_eftpos_transaction'].",
  'draft',
  ".$data['amount'].",
  ".$data['amount'].",
  ".$data['tipAmount'].",
  'wait',
  '".$api_call."',
  '".$api_call."',
  '".$db_link->escape_string($data['row_asset']['megeftpos_terminal_id'])."',
  ".$data['installments']."
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  
  $id_megeftpos_transaction=$db_link->insert_id;
  

  
  
  
  $sql="update gks_eftpos_transaction set 
  send_array='".$db_link->escape_string(json_encode($mypost))."',
  xxx_transaction_id=".$id_megeftpos_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];  
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}

    

  //echo '<pre>dddddddddddd send params ';print_r($params);die();
  
  $gks_erp_run_result=gks_erp_app_run_command($params);

  //echo '<pre>dddddddddddd return gks_erp_run_result ';print_r($gks_erp_run_result);die();

  
  if ($gks_erp_run_result['success']==false) {
    $return['message']=$gks_erp_run_result['message'];
    debug_mail(false,$gks_erp_run_result['message'],print_r($gks_erp_run_result,true));return $return;}  
  
//  echo '<pre>dddddddddddd return erp_app ';print_r($gks_erp_run_result);die();
  
  return $gks_erp_run_result;
    

    
}


function gks_eftpos_sales_request_get_status_megeftpos($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error' , 'status'=>'agnosto');
  
  $sessionId=trim_gks($data['sessionId']);
  if ($sessionId!=='') {
    $sql="select * from gks_eftpos_transaction 
    where sessionId='".$db_link->escape_string($sessionId)."'";
    $result = $db_link->query($sql);        
    if (!$result) {
      $return['message']='error sql';
      debug_mail(false,$return['message'],$sql);return $return;}   
    if ($result->num_rows != 1) {
      $return['message']=gks_lang('Δεν βρέθηκε η συναλλαγή με sessionId').' '.$sessionId;
      debug_mail(false,$return['message'],$sql);return $return;}    
    $row= $result->fetch_assoc();
    $id_eftpos_transaction=intval($row['id_eftpos_transaction']);
    $transaction_status=trim_gks($row['transaction_status']);
    $mymessage=trim_gks($row['mymessage']);
    $transactionId=trim_gks($row['transactionId']);
    $transaction_type=trim_gks($row['transaction_type']);

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
    
    if (in_array($transaction_status,['abort','agnosto','canceled'])) {
      if (in_array($transaction_type,['sale','saleerp'])) {
        $sql="UPDATE ".$t_gks_acc_xxx_payment." 
        LEFT JOIN gks_eftpos_transaction ON ".$t_gks_acc_xxx_payment.".".$f_id_acc_xxx_payment." = gks_eftpos_transaction.".$f_acc_xxx_payment_id." 
        SET ".$t_gks_acc_xxx_payment.".transaction_pa_with_id = 0, 
        ".$t_gks_acc_xxx_payment.".transaction_id = 0
        WHERE gks_eftpos_transaction.payment_acquirer_with_id=2
        AND gks_eftpos_transaction.transaction_status<>'done'
        AND gks_eftpos_transaction.sessionId='".$db_link->escape_string($sessionId)."'";
        $result = $db_link->query($sql);        
        if (!$result) {
          $return['message']='error sql';
          debug_mail(false,$return['message'],$sql);return $return;}
      }
    }
    
    
    
    if (in_array($transaction_status,['draft','async'])) {
      return array('success' => true, 'message' => 'OK', 'status' => 'wait');
    }
    if (in_array($transaction_status,['abort','agnosto'])) {
       return array('success' => true, 'message' => $mymessage, 'status' => 'abort');
    }
    if ($transaction_status=='canceled') {
      return array('success' => true, 'message' => $mymessage, 'status' => 'canceled');
    }

    if ($transaction_status=='done') {
      
      
      return array('success' => true, 'message' => $mymessage, 'status' => 'done');
    }

    
    return array('success' => true, 'message' => 'OK1111'.$mymessage, 'status' => 'wait');
    
  }
  
  //'agnosto', 'processed', 'done', 'request', 'sql error', 'canceled', 'abort', 'wait'
  //echo '<pre>'; print_r($data);die();
  
  
  
  return array('success' => true, 'message' => 'OK', 'status' => 'wait');
}


function gks_eftpos_get_transaction_extra_html_megeftpos($input) {
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
    
  $transactionId=''; if (isset($input['transactionId'])) $transactionId=trim($input['transactionId']);
  $sessionId=''; if (isset($input['sessionId'])) $sessionId=trim($input['sessionId']);
  if ($transactionId=='' and $sessionId=='') {
    $return['message']=gks_lang('Δεν έχουν ορισθεί όλα τα δεδομένα');
    debug_mail(false,$return['message'],print_r($input,true));return $return;}
  
  $sql="select * from gks_megeftpos_transaction 
  where eftpos_transaction_id=".$input['id_eftpos_transaction'];
  //if ($transactionId!='') $sql.=" receiptNumber='".$db_link->escape_string($transactionId)."'";
  //else $sql.=" 1=2";
  $result = $db_link->query($sql);  
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc(); 
    $return['transaction']['donedate']=$row['mydate_add'];
    
    $amount=floatval($row['paidAmount']);
    if ($amount!=0) $return['transaction']['amount']=$amount;
        
    $tipamount=floatval($row['tipAmount']);
    if ($tipamount!=0) $return['transaction']['tipamount']=$tipamount;
        
    $html=[];

    
    
    //if ($row['responseCode']===0 or !empty($row['responseCode'])) {
      $html[]='Response: <span class="megeftpos_responseCode_'.$row['responseCode'].'">'.$row['nspResponseCodeDescription'].'</span>';
    //}
    if (!empty($row['transactionType'])) {
      $html[]=gks_lang('Τύπος').': '. $row['transactionType'];
    } 
      
    //$html[]=gks_lang('Αναφορά Πληρωμής').': <span title="'.$row['MerchantTrns'].'"><i class="fas fa-exclamation-circle"></i></span>';
    //$html[]=gks_lang('Εταιρεία').': <span title="'.$row['MerchantId'].'">'.(empty($row['CompanyName']) ? '<i class="fas fa-exclamation-circle"></i>' : $row['CompanyName']).'</span>';

    if (!empty($row['receiptNumber'])) $html[]=gks_lang('Κωδικός Πληρωμής').': '.$row['receiptNumber'];
    if (!empty($row['cardType'])) $html[]=gks_lang('Τύπος Κάρτας').': '.$row['cardType'];
    if (!empty($row['cardNumber'])) $html[]=gks_lang('Κάρτα').': '.$row['cardNumber'];
    if (!empty($row['bankCode'])) $html[]=gks_lang('Τράπεζα').': '.$row['bankCode'];
    if (!empty($row['cardHolder'])) $html[]=gks_lang('Όνομα').': '.$row['cardHolder'];

    if (!empty($row['nspResponseCodeDescription'])) $html[]='ResponseCode: '.$row['nspResponseCodeDescription'];
    
    

   $html[]='<a href="admin-megeftpos-transaction-raw.php?mtid='.$row['id_megeftpos_transaction'].'" target="_blank" title="Raw Data"><i class="fas fa-database gks_payment_rawdata"></i></a>'.
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

function gks_eftpos_fullvoid_request_megeftpos($data) {

  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');
  
  $transaction_type=$data['transaction_type'];
  
  //echo '<pre>dddddddddddd d ';print_r($data);die();
  
  $mypost['transactionData']=array(
    'posId' => $data['row_asset']['megeftpos_pos_id'],
    'host' => $data['row_asset']['megeftpos_static_ip'],
    'port' => $data['row_asset']['megeftpos_port'],
    'apiKey' => $data['row_asset']['megeftpos_api_key'],
    'protocol' => intval($data['row_asset']['megeftpos_protocol']),
    'terminal_id' => $data['row_asset']['megeftpos_terminal_id'],
    'uniqueTxnId' => $data['my_uniqueTxnId'],
    'amount'=>number_format($data['amount'],2,'.',''),
    'tipAmount'=>number_format($data['tipAmount'],2,'.',''),
    'cashier' => $data['cashRegisterId'],
    'installments'=>$data['installments'],
    
  );
  

  $api_call='fullvoid';
  if (in_array($transaction_type,['refund','refunderp'])) $api_call='refund';
  if ($data['seira_need_signature']) {
    $api_call='fullvoiderp';
    if (in_array($transaction_type,['refund','refunderp'])) $api_call='refunderp';

    $mypost['transactionData']['signature']=$data['aadeProviderSignature'];
    $mypost['transactionData']['signatureData']=$data['aadeProviderSignatureData'];
    $mypost['transactionData']['providersId']=$data['aadeProviderId'];
    $mypost['transactionData']['uid']=$data['aadeSignatureUID'];
    $mypost['transactionData']['dateTimeProviderSignature']=$data['aadeSignatureTimestamp'];
    $mypost['transactionData']['amountPayable']=floatval($data['amount']).'';
    $mypost['transactionData']['netValue']=floatval($data['netAmount']).'';
    $mypost['transactionData']['vatValue']=floatval($data['vatAmount']).'';
    $mypost['transactionData']['totalValue']=floatval($data['grossAmount']).'';
  }
  
  $xxx_transaction_id=0; 
  if (isset($data['row_prev_eftpos']['xxx_transaction_id'])) {
    $xxx_transaction_id=intval($data['row_prev_eftpos']['xxx_transaction_id']);
  }
  if ($xxx_transaction_id<=0) {
    $return['message']=gks_lang('Δεν βρέθηκε η σχετική συναλλαγή');
    debug_mail(false,$return['message'],print_r($mypost,true));return $return;}     

  $sql="select * from gks_megeftpos_transaction where id_megeftpos_transaction=".$xxx_transaction_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows == 0) {
    $return['message']=gks_lang('Δεν βρέθηκε η σχετική συναλλαγή με id').' '.$xxx_transaction_id;
    debug_mail(false,$return['message'],print_r($mypost,true));return $return;}     
  
  $row_prev_tra= $result->fetch_assoc();  
  $mypost['transactionData']['bankAuthorizationCode']=trim_gks($row_prev_tra['bankAuthorizationCode']);
  //$mypost['transactionData']['ecrToken']=trim_gks($row_prev_tra['ecrToken']);
  $mypost['transactionData']['nspReferenceNumber']=trim_gks($row_prev_tra['nspReferenceNumber']);
  $mypost['transactionData']['receiptNumber']=trim_gks($row_prev_tra['receiptNumber']);
  
  
  //echo '<pre>dddddddddddd d ';print_r($mypost);die();
  
  
  if ($data['seira_need_signature'] and strlen($mypost['transactionData']['dateTimeProviderSignature'])!=14) {
    $return['message']=gks_lang('Δεν έχει ορισθεί η χρονική σφραγίδα της υπογραφής');
    debug_mail(false,$return['message'],print_r($mypost,true));return $return;}    
    
  if ($mypost['transactionData']['posId']=='') {
    $return['message']=gks_lang('Δεν έχει ορισθεί POS ID για αυτό το πάγιο');
    debug_mail(false,$return['message'],print_r($mypost,true));return $return;}    
    
  $params=array(
    'id' => $data['row_asset']['megeftpos_erp_app_id'],
    'cmd' => 'megeftpos_run_command',
    'asset_id' => $data['row_asset']['id_asset'],
    'api_call' => $api_call,
    'send_data_array' => $mypost,
    'megeftpos_ecr2eftweb_service_url' => $data['row_asset']['megeftpos_ecr2eftweb_service_url'],
    'id_eftpos_transaction' => $data['id_eftpos_transaction'],
  );  

  //echo '<pre>dddddddddddd params ';print_r($params);die();
  $r_error='wait';
  $sql="insert into gks_megeftpos_transaction (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  trans_status,
  originalAmount,
  paidAmount,
  tipAmount,
  mymessage,
  transactionType,
  trans_type,
  eftTerminalId,
  installments
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App Desktop','megeftpos',
  ".$data['id_eftpos_transaction'].",
  'draft',
  ".$data['amount'].",
  ".$data['amount'].",
  ".$data['tipAmount'].",
  'wait',
  '".$api_call."',
  '".$api_call."',
  '".$db_link->escape_string($data['row_asset']['megeftpos_terminal_id'])."',
  ".$data['installments']."
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  
  $id_megeftpos_transaction=$db_link->insert_id;  

  $sql="update gks_eftpos_transaction set 
  send_array='".$db_link->escape_string(json_encode($mypost))."',
  xxx_transaction_id=".$id_megeftpos_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];  
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  
  
  $gks_erp_run_result=gks_erp_app_run_command($params);


  if ($gks_erp_run_result['success']==false) {
    $return['message']=$gks_erp_run_result['message'];
    debug_mail(false,$gks_erp_run_result['message'],print_r($gks_erp_run_result,true));return $return;}  
  
//  echo '<pre>dddddddddddd return erp_app ';print_r($gks_erp_run_result);die();
  
  return $gks_erp_run_result;
        

    
  
}
