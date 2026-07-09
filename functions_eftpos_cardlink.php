<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_eftpos_has_transaction_status_cardlink($id) {
  if ($id===null) return '';
  
  switch ($id) {
    case 'draft': return gks_lang('Πρόχειρη','part4','gks_eftpos_has_transaction_status_cardlink');
    case 'send':  return gks_lang('Στάλθηκε','part4','gks_eftpos_has_transaction_status_cardlink');
    case 'fail':  return gks_lang('Απέτυχε','part4','gks_eftpos_has_transaction_status_cardlink');
    case 'done':  return gks_lang('Ολοκληρώθηκε','part4','gks_eftpos_has_transaction_status_cardlink');
  }
  return 'ID '.$id;
}

/*
<option value="00" selected="">Approved</option>
<option value="51">Declined by host</option>
<option value="DC">Declined by card</option>
<option value="UD">Unsupported card</option>
<option value="UC">User cancelled</option>
<option value="LC">Lost carrier</option>
<option value="TO">Time out</option>
<option value="CE">Communication error</option>
<option value="ND">Not delivered</option>
<option value="NA">Host unavailable</option>
<option value="IM">Invalid MAC</option>
<option value="JF">Journal full</option>
<option value="UN">Wrong transaction</option>
<option value="WP">Wrong parameter</option>
<option value="IS">Invalid sequence number</option>
<option value="ID">Invalid transaction data</option>
<option value="IB">Invalid batch number</option>
<option value="EC">Expired card</option>
<option value="WC">Wrong card</option>
<option value="VT">Transaction already voided</option>
<option value="XC">EMV Gen2 failure</option>
<option value="RC">Card removed</option>
<option value="CL">Connection lost</option>
<option value="Y1">Approved offline</option>
<option value="Y2">Approved referral</option>
<option value="Y3">Approved locally</option>
<option value="Z1">Declined offline</option>
<option value="Z2">Declined referral</option>
<option value="Z3">Declined locally</option>
<option value="EA">Unknown authorization code</option>
<option value="NT">Transaction not found</option>




<select id="saleType" onchange="manipulate(); handleSaleTypeChange()">
  <option value="fullvoid" id="fullvoiderp">Full Void ERP</option>
  <option value="fullvoid" id="fullvoidecrtoken">Full Void Ecr Token</option>
  <option value="fullvoid" id="fullvoid">FullVoid</option>
  <option value="sale" id="erpsale">Sale ERP</option>
  <option value="sale" id="ecrtokensale">Sale Ecr Token</option>
  <option value="sale" id="sale" selected="">Sale</option>
  <option value="resendall/start" id="resendallstart">Resend All Start</option>
  <option value="resendall/next" id="resendallnext">Resend All Next</option>
  <option value="resendall/finish" id="resendallfinish">Resend All Finish</option>
  <option value="regreceipt" id="erpregreceipt">Registration Receipt ERP</option>
  <option value="regreceipt" id="ecrtokenregreceipt">Registration Receipt Ecr Token</option>
  <option value="refund" id="refunderp">Refund ERP</option>
  <option value="refund" id="refundecrtoken">Refund Ecr Token</option>
  <option value="refund" id="refund">Refund</option>
  <option value="reconciliation" id="reconciliation">Reconciliation</option>
  <option value="preauthcompletion" id="preauthcompletionerp">Pre Auth Completion ERP</option>
  <option value="preauthcompletion" id="preauthcompletionecrtoken">Pre Auth Completion Ecr Token</option>
  <option value="preauthcompletion" id="preauthcompletion">Pre Auth Completion</option>
  <option value="normalpreauth" id="normalpreauth">Pre Auth</option>
  <option value="onetappreauthcompletion" id="onetappreauthcompletion">One Tap Preauth Completion</option>
  <option value="onetappreauth" id="onetappreauth">One Tap Preauth</option>
  <option value="sale" id="iriserpsale">FORCE IRIS Sale ERP</option>
  <option value="sale" id="irisecrtokensale">FORCE IRIS Sale Ecr Token</option>
  <option value="sale" id="irissale" selected="">FORCE IRIS Sale</option>
  <option value="refund" id="freerefunderp">Free Refund ERP</option>
  <option value="refund" id="freerefund">Free Refund</option>
  <option value="echo" id="echo">Echo</option>
  <option value="control" id="control">Control</option>
<!--                            <option id="saleinstallments" value="saleinstallments">Sale with installements</option>-->
<!--                            <option id="fullvoid" value="fullvoid">FullVoid</option>-->
<!--                            <option id="refund" value="refund">Refund</option>-->
<!--                            <option id="loyaltyvoid" value="loyaltyvoid">Loyalty Void</option>-->
<!--                            <option id="freerefund" value="freerefund">Free Refund</option>-->
<!--                            <option id="merchantinfo" value="merchantinfo">Merchant Info</option>-->
<!--                            <option id="normalpreauth" value="normalpreauth">Pre Auth</option>-->
<!--                            <option id="preauthcompletion" value="preauthcompletion">Pre Auth Completion</option>-->
<!--                            <option id="reconciliation" value="reconciliation">Reconciliation</option>-->
<!--                            <option id="fullreconciliation" value="fullreconciliation">Full Reconciliation</option>-->
<!--                            <option id="onetappreauth" value="onetappreauth">One Tap Preauth</option>-->
<!--                            <option id="onetappreauthcompletion" value="onetappreauthcompletion">One Tap Preauth Completion</option>-->
</select>

*/        
function gks_eftpos_has_transaction_result_cardlink($id,$def='') {
  switch (trim_gks($id)) {   
    case '':  return '';
    case '00':return 'Approved';
    case '51':return 'Declined by host';
    case 'DC':return 'Declined by card';
    case 'UD':return 'Unsupported card';
    case 'UC':return 'User cancelled';
    case 'LC':return 'Lost carrier';
    case 'TO':return 'Time out';
    case 'CE':return 'Communication error';
    case 'ND':return 'Not delivered';
    case 'NA':return 'Host unavailable';
    case 'IM':return 'Invalid MAC';
    case 'JF':return 'Journal full';
    case 'UN':return 'Wrong transaction';
    case 'WP':return 'Wrong parameter';
    case 'IS':return 'Invalid sequence number';
    case 'ID':return 'Invalid transaction data';
    case 'IB':return 'Invalid batch number';
    case 'EC':return 'Expired card';
    case 'WC':return 'Wrong card';
    case 'VT':return 'Transaction already voided';
    case 'XC':return 'EMV Gen2 failure';
    case 'RC':return 'Card removed';
    case 'CL':return 'Connection lost';
    case 'Y1':return 'Approved offline';
    case 'Y2':return 'Approved referral';
    case 'Y3':return 'Approved locally';
    case 'Z1':return 'Declined offline';
    case 'Z2':return 'Declined referral';
    case 'Z3':return 'Declined locally';
    case 'EA':return 'Unknown authorization code';
    case 'NT':return 'Transaction not found';    
    default:
  }
  //if ($id=='' and $def=='') return '';
  return $def.' ('.$id.')';
}

function gks_eftpos_build_json_for_send_ping_terminal_cardlink($input) {
  $ret = array('success' => false, 'message' => 'generic error ping_cardlink');
  $row_asset=$input['row_asset'];
  $ret['success']=true;
  $ret['message']='OK';
  $ret['send_data_array']=array(
    'ip' => $input['row_asset']['cardlink_static_ip'],
    'port' => intval($input['row_asset']['cardlink_port']),
  );
  $ret['cardlink_ecr2eftweb_service_url']=$row_asset['cardlink_ecr2eftweb_service_url'];
  //echo '<pre>dddddddddddd';print_r($ret);die();
  return $ret;
}

function gks_eftpos_build_json_for_send_ping_service_cardlink($input) {
  $ret = array('success' => false, 'message' => 'generic error ping_cardlink');
  $row_asset=$input['row_asset'];
  $ret['success']=true;
  $ret['message']='OK';
  $ret['send_data_array']=array();
  $ret['cardlink_ecr2eftweb_service_url']=$row_asset['cardlink_ecr2eftweb_service_url'];
  //echo '<pre>dddddddddddd';print_r($ret);die();
  return $ret;
}

function gks_eftpos_build_json_for_send_merchantinfo_cardlink($input) {
  $ret = array('success' => false, 'message' => 'generic error merchantinfo_cardlink');
  $row_asset=$input['row_asset'];
  //echo '<pre>dddddddddddd';print_r($row_asset);die();
  
  $send_data_array=array(
    'serviceData' => array (
      'host' =>  $row_asset['cardlink_static_ip'],
      'port' =>  $row_asset['cardlink_port'],
      'ecrid' => $row_asset['cardlink_terminal_id'],
    ),
    'transactionData' => array(),
  );
  if (GKS_CARDLINK_uniqueIntegratorId!='') {
    $send_data_array['transactionData']['uniqueIntegratorId']=GKS_CARDLINK_uniqueIntegratorId;
  }
  
  //echo '<pre>dddddddddddd';print_r($send_data_array);die();
  $ret['success']=true;
  $ret['message']='OK';
  $ret['send_data_array']=$send_data_array;
  $ret['cardlink_ecr2eftweb_service_url']=$row_asset['cardlink_ecr2eftweb_service_url'];
  return $ret;
}


function gks_eftpos_build_json_for_send_reconciliation_cardlink($input) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;	
  
  $ret = array('success' => false, 'message' => 'generic error reconciliation_cardlink');
  $row_asset=$input['row_asset'];
  $id_payment_acquirer_with=intval($input['id_payment_acquirer_with']);
  $transaction_type=trim_gks($input['transaction_type']);
  $id_asset=intval($input['id_asset']);
  $transaction_status='draft';
  
  $my_uniqueTxnId=guid_for_eftpos_my_uniqueTxnId();
  
  
   
  
  $send_data_array=array(
    'serviceData' => array (
      'host' =>  $row_asset['cardlink_static_ip'],
      'port' =>  $row_asset['cardlink_port'],
      'ecrid' => $row_asset['cardlink_terminal_id'],
    ),
    'transactionData' => array(
      'firstByteOriginalTrans'=> 0,
      'uniqueTxnId' => $my_uniqueTxnId,
      'reconciliationMode' => 0,
      'batchNumber' => '000000',
    ),
  );
  if (GKS_CARDLINK_uniqueIntegratorId!='') {
    $send_data_array['transactionData']['uniqueIntegratorId']=GKS_CARDLINK_uniqueIntegratorId;
  }

  $sql="insert into gks_eftpos_transaction (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  transaction_type,
  acc_inv_payment_id,
  acc_pay_payment_id,
  payment_acquirer_with_id,
  payment_acquirer_id,
  aade_paroxos_id,
  asset_id,
  terminalId,
  transaction_status,
  my_uniqueTxnId,
  amount,
  sessionId,
  cashRegisterId,
  merchantReference,
  customerTrns,
  tipAmount,
  aadeProviderId,
  aadeProviderSignatureData,
  aadeProviderSignature,
  send_array
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  '".$db_link->escape_string($transaction_type)."',
  0,
  0,
  ".$id_payment_acquirer_with.",
  0,
  0,
  ".$id_asset.",
  '".$db_link->escape_string($row_asset['cardlink_terminal_id'])."',
  '".$db_link->escape_string($transaction_status)."',
  '".$db_link->escape_string($my_uniqueTxnId)."',
  0,
  '',
  '',
  '',
  '',
  0,
  '',
  '',
  '',
  '".$db_link->escape_string(json_encode($send_data_array))."'
  )";  

  $result = $db_link->query($sql);        
  if (!$result) {
    $ret['message'] = 'sql error';
    debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $id_eftpos_transaction = $db_link->insert_id;  
  

  $ret['success']=true;
  $ret['message']='OK';
  $ret['send_data_array']=$send_data_array;
  $ret['cardlink_ecr2eftweb_service_url']=$row_asset['cardlink_ecr2eftweb_service_url'];
  $ret['id_eftpos_transaction']=$id_eftpos_transaction;
  return $ret;
}



function gks_eftpos_sales_request_cardlink($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');
  
  //print '<pre>';print_r($data);die();
  
  /*
  {
    "serviceData": {
      "host": "virtualpos.services.novidea.gr",
      "port": "4000",
      "ecrid": "83767662"
    },
    "transactionData": {
      "firstByteOriginalTrans": "0",
      "uniqueTxnId": "8ee366b8998447ab26bb57c7a05f32a4",
      "uniqueIntegratorId": "20eba121ee1f43fc9599bd623e715d0f",
      "amount": "1.00"
    }
  }
  */

  //echo '<pre>dddddddddddd d ';print_r($data);die();
  
   
  
  $mypost=array();
  $mypost['serviceData']=array(
    'host' => $data['row_asset']['cardlink_static_ip'],
    'port' => $data['row_asset']['cardlink_port'],
    'ecrid' => $data['row_asset']['cardlink_terminal_id'],
  );
  
  $mypost['transactionData']=array(
    'firstByteOriginalTrans' => 0,
    'uniqueTxnId' => $data['my_uniqueTxnId'],
    'amount'=>number_format($data['amount'],2,'.',''),
  );
  if ($data['tipAmount']>0) {
    
    //$mypost['transactionData']['tipAmount']=number_format($data['tipAmount'],2,'.','');
    //$mypost['transactionData']['TipAmount']=number_format($data['tipAmount'],2,'.','');
  
  }
  
  if ($data['installments']>=2) {
//    $temp=$data['installments'];
//    if ($data['installments']<=9) $temp='00'.$data['installments'];
//    else if ($data['installments']<=99) $temp='0'.$data['installments'];

    //$temp=$data['installments'];
    //if ($data['installments']<=9) $temp='0'.$data['installments'];
    //else if ($data['installments']<=99) $temp=''.$data['installments'];
    
//    $mypost['transactionData']['tlvData']='TL0007NI03'.$temp;
//    $mypost['transactionData']['tlvData']='TL0022TN'.$temp; //00TT141970010
//    $mypost['transactionData']['numberOfInstallments']=$temp; 
//    $mypost['transactionData']['installements']=$temp; 
//    $mypost['transactionData']['installments']=$temp; 
    
        //'installements' => '002', //$data['installments'],
    //'installments'
  }
  
  // send to /saleinstallments
  //call saleinstallments
  //installements
  //numberOfInstallments numberOfPostdatedInstallments
  
  //
  
  if (GKS_CARDLINK_uniqueIntegratorId!='') {
    $mypost['transactionData']['uniqueIntegratorId']=GKS_CARDLINK_uniqueIntegratorId;
  }
  
  //echo '<pre>';print_r($data);die();
  
  $api_call='sale';
  if ($data['seira_need_signature']) {
    $api_call='saleerp';
    
    $mypost['transactionData']['signature']=$data['aadeProviderSignature'];
    $mypost['transactionData']['providersId']=$data['aadeProviderId'];
    //$mypost['transactionData']['uniqueEntryNumber']=$data['id_eftpos_transaction'].'';
    $mypost['transactionData']['uid']=$data['aadeSignatureUID'];
    $mypost['transactionData']['dateTimeProviderSignature']=$data['aadeSignatureTimestamp'];
    $mypost['transactionData']['amountPayable']=intval($data['amount']*100).'';//number_format($data['amount'],2,'.','');
    $mypost['transactionData']['netValue']=intval($data['netAmount']*100).'';//number_format($data['netAmount'],2,'.','');
    $mypost['transactionData']['vatValue']=intval($data['vatAmount']*100).'';//number_format($data['vatAmount'],2,'.','');
    $mypost['transactionData']['totalValue']=intval($data['grossAmount']*100).'';//number_format($data['grossAmount'],2,'.','');
    $mypost['transactionData']['eftTerminalId']=$data['row_asset']['cardlink_terminal_id'];
    $mypost['transactionData']['posId']=$data['row_asset']['cardlink_terminal_id'];
    $mypost['transactionData']['ecrId']=$data['row_asset']['cardlink_terminal_id'];
    
    
  }
  
  if (isset($data['preferred_payment_method']) and $data['preferred_payment_method']=='iris') {
    $mypost['transactionData']['tlvData']='TL0005IP011';
  }
  
  //echo '<pre>dddddddddddd d ';print_r($data);die();
  //echo '<pre>dddddddddddd d ';print_r($mypost);die();
  
  //cardlink_ecr2eftweb_erp_app_id
  //cardlink_ecr2eftweb_service_url
  
  
  $params=array(
    'id' => $data['row_asset']['cardlink_ecr2eftweb_erp_app_id'],
    'cmd' => 'cardlink_run_command',
    'asset_id' => $data['row_asset']['id_asset'],
    'api_call' => $api_call,
    'send_data_array' => $mypost,
    'cardlink_ecr2eftweb_service_url' => $data['row_asset']['cardlink_ecr2eftweb_service_url'],
    'id_eftpos_transaction' => $data['id_eftpos_transaction'],
    
  );

  //echo '<pre>dddddddddddd params ';print_r($params);die();
  $r_error='wait';
  $sql="insert into gks_cardlink_transaction (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  trans_status,
  amount,
  mymessage,
  transactionType,
  trans_type,
  numberOfInstallments
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App Desktop','ecr2eftweb',
  ".$data['id_eftpos_transaction'].",
  'draft',
  ".$data['amount'].",
  'wait',
  '".$api_call."',
  '".$api_call."',
  ".$data['installments']."
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  
  $id_cardlink_transaction=$db_link->insert_id;
  

  
  
  
  $sql="update gks_eftpos_transaction set 
  send_array='".$db_link->escape_string(json_encode($mypost))."',
  xxx_transaction_id=".$id_cardlink_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];  
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}

    

  
  
  $gks_erp_run_result=gks_erp_app_run_command($params);

  //echo '<pre>dddddddddddd return gks_erp_run_result ';print_r($gks_erp_run_result);die();

  /*
  {
  "transactionData": {
    "msgType": "210",
    "paymentSpecs": "P@1",
    "authorizationCode": "269854",
    "city": "AMAROYSION",
    "msgOptions": "0003",
    "amountPayable": "",
    "mid": "340324822",
    "responseCodeMessage": "Approved",
    "cardExpiryDate": "261130",
    "txnDateTime": "",
    "responseCode": "00",
    "merchantName": "gks ERP",
    "uniquePaymentIdECR": "",
    "referenceNumber": "010002",
    "sn": "1640000000",
    "msgCode": "00",
    "acquirerName": "WORLDLINE/EUROBANK",
    "applicationName": "Debit Mastercard",
    "batchNumber": "010",
    "amount": "1.00",
    "address": "AGIOY KONSTANTINOY 50 MAROYSI",
    "eftTerminalId": "83767662",
    "length": "0295",
    "cardType": "MASTERCARD",
    "sessionId": "001290",
    "numberOfPostdatedInstallments": "0000",
    "accountNumber": "5581 21** **** 2451",
    "tc": "03000000303000",
    "token": "",
    "transactionType": "SALE",
    "bankId": "026",
    "go4moreProducts": "",
    "uniquePaymentId": "",
    "tlvData": "TL0022TN00TT1419700101020000",
    "numberOfInstallments": "",
    "phone": "6971234567",
    "posTerminalVersion": "1.1.3",
    "aid": ""
  },
  "error": "null"
}

  */  
  
  if ($gks_erp_run_result['success']==false) {
    $return['message']=$gks_erp_run_result['message'];
    debug_mail(false,$gks_erp_run_result['message'],print_r($gks_erp_run_result,true));return $return;}  
  
//  echo '<pre>dddddddddddd return erp_app ';print_r($gks_erp_run_result);die();
  
  return $gks_erp_run_result;
    

    
}


function gks_eftpos_sales_request_get_status_cardlink($data) {
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
    $transaction_type=trim_gks($row['transaction_type']);
    $mymessage=trim_gks($row['mymessage']);
    $transactionId=trim_gks($row['transactionId']);
    

    $paroxos_signature_id=0;
    $t_gks_acc_xxx_payment='';
    $f_acc_xxx_id='';
    $f_acc_xxx_payment_id='';
    $f_id_acc_xxx_payment='';
    $value_acc_xxx_payment_id=0;
    
    if ($row['acc_inv_payment_id']>0) {
      $value_acc_xxx_payment_id=intval($row['acc_inv_payment_id']);
      $t_gks_acc_xxx_payment='gks_acc_inv_payment';
      $f_acc_xxx_id='acc_inv_id';
      $f_acc_xxx_payment_id='acc_inv_payment_id';
      $f_id_acc_xxx_payment='id_acc_inv_payment';
    } else if ($row['acc_pay_payment_id']>0) {
      $value_acc_xxx_payment_id=intval($row['acc_pay_payment_id']);
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
        WHERE gks_eftpos_transaction.payment_acquirer_with_id=4
        and gks_eftpos_transaction.transaction_type in ('sale','saleerp')
        AND gks_eftpos_transaction.transaction_status<>'done'
        AND gks_eftpos_transaction.sessionId='".$db_link->escape_string($sessionId)."'";
        
        //debug_mail(false,'gggggggggggg',$sql);
         
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
      
      gks_eftpos_set_payment_via_iris($id_eftpos_transaction);
      return array('success' => true, 'message' => $mymessage, 'status' => 'done');
    }

    
    return array('success' => true, 'message' => 'OK1111'.$mymessage, 'status' => 'wait');
    
  }
  
  //'agnosto', 'processed', 'done', 'request', 'sql error', 'canceled', 'abort', 'wait'
  //echo '<pre>'; print_r($data);die();
  
  
  
  return array('success' => true, 'message' => 'OK', 'status' => 'wait');
}


function gks_eftpos_get_transaction_extra_html_cardlink($input) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;

  $return=array('success' => false, 'message' => 'generic error',
    'transaction'=>array(
      'html'=> gks_lang('Σφάλμα').'. '.gks_lang('Δεν βρέθηκε η συναλλαγή'),
      //'donedate' => '',
      //amount
      //tipamount
      //installments
    ),
  );
    
  $transactionId=''; if (isset($input['transactionId'])) $transactionId=trim($input['transactionId']);
  $sessionId=''; if (isset($input['sessionId'])) $sessionId=trim($input['sessionId']);
  if ($transactionId=='' and $sessionId=='') {
    $return['message']=gks_lang('Δεν έχουν ορισθεί όλα τα δεδομένα');
    debug_mail(false,$return['message'],print_r($input,true));return $return;}
  
  $sql="select * from gks_cardlink_transaction 
  where eftpos_transaction_id=".$input['id_eftpos_transaction'];
  //if ($transactionId!='') $sql.=" referenceNumber='".$db_link->escape_string($transactionId)."'";
  //else if ($sessionId!='') $sql.=" MerchantTrns like '%|".$db_link->escape_string($sessionId)."|gks'";  
  //else $sql.=" 1=2";
  $result = $db_link->query($sql);  
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc(); 
    $return['transaction']['donedate']=$row['mydate_add'];

    $return['transaction']['amount']=floatval($row['amount']);
    $return['transaction']['tipamount']=0; //floatval($row['TipAmount']);
    $return['transaction']['installments']=intval($row['numberOfInstallments']);

    
        
    //$tipamount=floatval($row['TipAmount']);
    //if ($tipamount!=0) $return['transaction']['tipamount']=$tipamount;
        
    $html=[];

    
    //if (!empty($row['TotalCommission'])) $html[]=gks_lang('Προμήθεια').': '.myCurrencyFormat(floatval($row['TotalCommission']));
    //if (!empty($row['TotalFee'])) $html[]=gks_lang('Τέλος').': '.myCurrencyFormat(floatval($row['TotalFee']));
    
    if (!empty($row['responseCodeMessage'])) {
      $html[]=gks_lang('Response').': <span class="gks_cardlink_transaction_responseCode_'.($row['responseCode']=='00' ? '00' : 'xx').'">'.$row['responseCodeMessage'].'</span>';
    }
    if (!empty($row['transactionType'])) {
      $html[]=gks_lang('Τύπος').': '. $row['transactionType'];
    } 

      
    //$html[]=gks_lang('Αναφορά Πληρωμής').': <span title="'.$row['MerchantTrns'].'"><i class="fas fa-exclamation-circle"></i></span>';
    $html[]=gks_lang('Εταιρεία').': <span title="'.$row['merchantName'].'">'.(empty($row['merchantName']) ? '<i class="fas fa-exclamation-circle"></i>' : $row['merchantName']).'</span>';
    //if (isset($row['Latitude']) and isset($row['Longitude'])) {
    //  $html[]=gks_lang('Στίγμα').': '.
    //  '<a href="https://www.google.com/maps/search/?api=1&query='.$row['Latitude'].','.$row['Longitude'].'" target="_blank"><i class="fas fa-map-marker-alt gks_marker_gps"></i></a>';
    //}

    //if (!empty($row['referenceNumber'])) $html[]=gks_lang('Κωδικός Αναφοράς').': '.$row['referenceNumber'];
    if (!empty($row['cardType'])) $html[]=gks_lang('Τύπος Κάρτας').': <b class="cardlink_tra_cardType cardlink_tra_cardType_'.$row['cardType'].'">'.$row['cardType'].'</b>'; 
    if (!empty($row['applicationName'])) $html[]=gks_lang('Εφαρμογή').': '.$row['applicationName'];
    if (!empty($row['accountNumber'])) $html[]=gks_lang('Κάρτα').': '.$row['accountNumber'];
    if (!empty($row['acquirerName'])) $html[]=gks_lang('Τράπεζα').': '.$row['acquirerName'];
    //if (!empty($row['transactionType'])) $html[]=gks_lang('Τύπος συναλλαγής').': '.$row['transactionType'];
    
    //if (!empty($row['FullName'])) $html[]=gks_lang(''Όνομα').': '.$row['FullName'];
    //if (!empty($row['Email'])) $html[]=gks_lang(''email').': '.$row['Email'];
    if (!empty($row['phone'])) $html[]=gks_lang('Τηλέφωνο').': '.$row['phone'];
    if (!empty($row['Description'])) $html[]=gks_lang('Περιγραφή').': '.$row['Description'];
    if (!empty($row['batchNumber'])) $html[]=gks_lang('Batch Number').': '.$row['batchNumber'];
    if (!empty($row['sn'])) $html[]=gks_lang('SN').': '.$row['sn'];
    
    
    

    $html[]='<a href="admin-cardlink-transaction-raw.php?id='.$input['id_eftpos_transaction'].'" target="_blank" title="Raw Data"><i class="fas fa-database gks_payment_rawdata"></i></a>'.
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


function gks_eftpos_fullvoid_request_cardlink($data) {

  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');
  
  $transaction_type=$data['transaction_type'];
  
  //echo '<pre>dddddddddddd d ';print_r($data);die();
  
   
  
  $mypost=array();
  $mypost['serviceData']=array(
    'host' => $data['row_asset']['cardlink_static_ip'],
    'port' => $data['row_asset']['cardlink_port'],
    'ecrid' => $data['row_asset']['cardlink_terminal_id'],
  );
  
  $mypost['transactionData']=array(
    'firstByteOriginalTrans' => 0,
    'uniqueTxnId' => $data['my_uniqueTxnId'],
    'amount'=>number_format($data['amount'],2,'.',''),
  );
  if (in_array($transaction_type,['fullvoid','fullvoiderp'])) {
    $mypost['transactionData']['referenceNumber']=$data['row_prev_eftpos']['transactionId'];
  } else if (in_array($transaction_type,['refund','refunderp'])) {
    $mypost['transactionData']['amount']=number_format($data['refund_val'],2,'.','');
    $mypost['transactionData']['tlvData']='TL0010OR06'.$data['row_prev_eftpos']['transactionId'];
  }
  
  
  //
  
  if (GKS_CARDLINK_uniqueIntegratorId!='') {
    $mypost['transactionData']['uniqueIntegratorId']=GKS_CARDLINK_uniqueIntegratorId;
  }
  
  //echo '<pre>';print_r($data);die();
  
  $api_call='fullvoid';
  if (in_array($transaction_type,['refund','refunderp'])) $api_call='refund';
  if ($data['seira_need_signature']) {
    $api_call='fullvoiderp';
    if (in_array($transaction_type,['refund','refunderp'])) $api_call='refunderp';

    
    
    $mypost['transactionData']['amountPayable']=intval($data['amount']*100).'';//number_format($data['amount'],2,'.','');
    if (in_array($transaction_type,['refund','refunderp'])) {
      $mypost['transactionData']['amountPayable']=intval($data['refund_val']*100).'';
    }
    
    $mypost['transactionData']['signature']=$data['aadeProviderSignature'];
    $mypost['transactionData']['providersId']=$data['aadeProviderId'];
    //$mypost['transactionData']['uniqueEntryNumber']=$data['id_eftpos_transaction'].'';
    $mypost['transactionData']['uid']=$data['aadeSignatureUID'];
    $mypost['transactionData']['dateTimeProviderSignature']=$data['aadeSignatureTimestamp'];
    $mypost['transactionData']['netValue']=intval($data['netAmount']*100).'';//number_format($data['netAmount'],2,'.','');
    $mypost['transactionData']['vatValue']=intval($data['vatAmount']*100).'';//number_format($data['vatAmount'],2,'.','');
    $mypost['transactionData']['totalValue']=intval($data['grossAmount']*100).'';//number_format($data['grossAmount'],2,'.','');
    $mypost['transactionData']['eftTerminalId']=$data['row_asset']['cardlink_terminal_id'];
    $mypost['transactionData']['posId']=$data['row_asset']['cardlink_terminal_id'];
    $mypost['transactionData']['ecrId']=$data['row_asset']['cardlink_terminal_id'];
    
    
  }
   
  
  
  //echo '<pre>dddddddddddd d ';print_r($data);die();
  //echo '<pre>dddddddddddd d ';print_r($mypost);die();
  
  //cardlink_ecr2eftweb_erp_app_id
  //cardlink_ecr2eftweb_service_url
  
  
  $params=array(
    'id' => $data['row_asset']['cardlink_ecr2eftweb_erp_app_id'],
    'cmd' => 'cardlink_run_command',
    'asset_id' => $data['row_asset']['id_asset'],
    'api_call' => $api_call,
    'send_data_array' => $mypost,
    'cardlink_ecr2eftweb_service_url' => $data['row_asset']['cardlink_ecr2eftweb_service_url'],
    'id_eftpos_transaction' => $data['id_eftpos_transaction'],
    
  );

  //echo '<pre>dddddddddddd params ';print_r($params);die();
  $r_error='wait';
  $sql="insert into gks_cardlink_transaction (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  trans_status,
  amount,
  mymessage,
  transactionType,
  trans_type
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App Desktop','ecr2eftweb',
  ".$data['id_eftpos_transaction'].",
  'draft',
  ".$data['amount'].",
  'wait',
  '".$api_call."',
  '".$api_call."'
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  
  $id_cardlink_transaction=$db_link->insert_id;
  

  
  
  
  $sql="update gks_eftpos_transaction set 
  send_array='".$db_link->escape_string(json_encode($mypost))."',
  xxx_transaction_id=".$id_cardlink_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];  
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}


  $gks_erp_run_result=gks_erp_app_run_command($params);

  //echo '<pre>dddddddddddd return gks_erp_run_result ';print_r($gks_erp_run_result);die();

  if ($gks_erp_run_result['success']==false) {
    $return['message']=$gks_erp_run_result['message'];
    debug_mail(false,$gks_erp_run_result['message'],print_r($gks_erp_run_result,true));return $return;}  
  
//  echo '<pre>dddddddddddd return erp_app ';print_r($gks_erp_run_result);die();
  
  return $gks_erp_run_result;
    
  
}

