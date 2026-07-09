<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');


//debug_mail(false,'api-gkserpapp-eftpos.php','');

//$gks_debug=false;
//$mytime = time();
//if ($gks_debug) $mytime -= 2*24*60*60; //remove me

$my_wp_user_id=2;

$rnd1s='';
if (isset($_GET['rnd1s'])) $rnd1s=trim($_GET['rnd1s']);

$send1='';
if (isset($_GET['send1'])) $send1=trim($_GET['send1']);

$id_erp_app=0;
if (isset($_GET['id'])) $id_erp_app = intval($_GET['id']);

$data_read = file_get_contents( 'php://input' );
//if ($ergastirio_id == 84) {
//  debug_mail(false,'api-ergastirio-printerinfo2',$data_read);
//}
$data = json_decode($data_read, true,512,JSON_INVALID_UTF8_IGNORE);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'empty RAW',$data_read);
  die();
}


$my_page_title=gks_lang('gks ERP App - gks_eftpos_transaction');     
db_open();
stat_record();

if ($rnd1s=='' or $send1=='' or $id_erp_app<=0 or $data==='') {
  debug_mail(false,'empty data','');
  die();  
}
$sql="SELECT * from gks_erp_app
where erp_app_disabled=0 and erp_app_token<> '' and id_erp_app=".$id_erp_app;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  echo 'error:sql error';die();}
if ($result->num_rows != 1) {
  debug_mail(false,'error gks ERP App','');
  echo 'error:'.gks_lang('Το κλειδί είναι λάθος'); die();}

$row_erp_app=$result->fetch_assoc();
$erp_app_token=$row_erp_app['erp_app_token'];
$erp_app_secret=$row_erp_app['erp_app_secret'];

$send1_calc= md5($rnd1s . $rnd1s . $id_erp_app . $erp_app_token . $rnd1s .$erp_app_secret.  GKS_ERP_HASHMD5KEY13);

if ($send1 != $send1_calc) {
  debug_mail(false,'security error','');
  echo 'error';
  die(); 
}

$responseok = md5($rnd1s . $id_erp_app . $erp_app_token . $erp_app_secret .  GKS_ERP_HASHMD5KEY15);



$success=false; if (isset($data['success'])) $success=$data['success'];
$message='generic gks_App error'; if (isset($data['message'])) $message=base64_decode($data['message']);
$service_url='';if (isset($data['service_url'])) $service_url=trim_gks($data['service_url']);
$api_call='';if (isset($data['api_call'])) $api_call=trim_gks($data['api_call']);
$id_eftpos_transaction=0;if (isset($data['id_eftpos_transaction'])) $id_eftpos_transaction=intval($data['id_eftpos_transaction']);
$async='';if (isset($data['async'])) $async=trim_gks($data['async']);
$send_data='';if (isset($data['send_data'])) $send_data=trim_gks($data['send_data']);
$response_data='';if (isset($data['response_data'])) $response_data=trim_gks($data['response_data']);
$gks_server_url='';if (isset($data['gks_server_url'])) $gks_server_url=trim_gks($data['gks_server_url']);
$tIndex=0;if (isset($data['tIndex'])) $tIndex=intval($data['tIndex']);
$eftpos_system=''; if (isset($data['eftpos_system'])) $eftpos_system=trim_gks($data['eftpos_system']);

if ($eftpos_system!='cardlink_ecr2eftweb' and $eftpos_system!='megeftpos') {
  debug_mail(false,'eftpos_system error','');
  echo 'error: eftpos_system';
  die();
}

$payment_acquirer_with_id=0;
if ($eftpos_system=='megeftpos') $payment_acquirer_with_id=2;
if ($eftpos_system=='cardlink_ecr2eftweb')  $payment_acquirer_with_id=4;

//echo $eftpos_system;die();

//debug_mail(false,'response data',print_r($data,true));

if ($response_data=='empty') $response_data='';

 
$r_data=array();
if ($response_data!='') {
  $r_data=json_decode($response_data, true,512,JSON_INVALID_UTF8_IGNORE);
  if ($r_data === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'empty response_data',$response_data);
    //die();
    $message.="\n".$response_data;
  } else {
     //
  }
}
if (is_array($r_data)==false) $r_data=array();
$transactionId=''; 
$aadeTransactionId='';

switch ($eftpos_system) {
  case 'cardlink_ecr2eftweb': 
    $t_data=array(); if (isset($r_data['transactionData'])) $t_data=$r_data['transactionData'];
    $r_error=''; if (isset($r_data['error'])) $r_error=$r_data['error'];
    if ($r_error!='null') $message.="\n".$r_error;

    if (isset($t_data['referenceNumber'])) $transactionId=$t_data['referenceNumber'];
    if (isset($t_data['uniquePaymentIdECR'])) $aadeTransactionId=$t_data['uniquePaymentIdECR'];
    
    
    $responseCode=''; if (isset($t_data['responseCode'])) $responseCode=trim_gks($t_data['responseCode']);
    $responseCodeMessage=''; if (isset($t_data['responseCodeMessage'])) $responseCodeMessage=trim_gks($t_data['responseCodeMessage']);
    
    //'agnosto', 'processed', 'done', 'request', 'canceled', 'abort', 'wait' , 'sql error'
    $transaction_status='agnosto';
    if ($r_error=='null'){
      if ($responseCodeMessage=='Approved' and $responseCode=='00') {
      $transaction_status='done';
      } else {
        $transaction_status='abort';
      }
    }
    
    if ($responseCodeMessage!='Approved') {
      if ($message=='OK')  {
        $message=$responseCodeMessage; 
      } else {
        $message.="\n".$responseCodeMessage;
      }
    }
    break;
  case 'megeftpos': 
    
    
    $r_error='';if (isset($r_data['errorMessage'])) $r_error=$r_data['errorMessage'];
    //if ($r_error!='null') $message.="\n".$r_error;
    
    $t_data=array(); 
    if (isset($r_data['myjson'])) {
      $t_data=json_decode($r_data['myjson'], true);
    }
    
    $r_responseCode=-1; 
    if (isset($r_data['responseCode']) and $r_data['responseCode']==='0') {
      $r_responseCode=0; //APPROVED
    } else {
      if (isset($r_data['responseCode'])) {
        if ($r_data['responseCode']==='empty') {
          $r_responseCode=-1;
        } else {
          $r_responseCode=intval($r_data['responseCode']);
        }
      }
    }

    //print $r_error.'|'.$r_responseCode.'|';
    //print_r($t_data);die();

    $transactionId='';if (isset($t_data['receiptNumber'])) $transactionId=trim_gks($t_data['receiptNumber']);
    $aadeTransactionId='';if (isset($t_data['nspReferenceNumber'])) $aadeTransactionId=trim_gks($t_data['nspReferenceNumber']);

    //$message.='|agnosto|'.$r_error.'|'.$r_responseCode.'|';
    
    $transaction_status='abort';
    if ($r_responseCode==0) { //APPROVED
      $transaction_status='done';
    } else if ($r_responseCode==1) { //DECLINED
      $transaction_status='abort';
    } else if ($r_responseCode==2) { //CANCELLED
      $transaction_status='canceled';
    } else if ($r_responseCode==3) { //FAILED
      $transaction_status='abort';
    } else if ($r_responseCode==4) { //UNKNOWN
      $transaction_status='agnosto';
    } else if ($r_responseCode==5) { //BUSY
      $transaction_status='abort';
    } else if ($r_responseCode==6) { //MAX_TRANSACTIONS
      $transaction_status='abort';
    } else if ($r_responseCode==7) { //ACTION_REQUIRED
      $transaction_status='abort';
    } else if ($r_responseCode==8) { //COMMUNICATION_ERROR
      $transaction_status='abort';
    }
    
    if (isset($r_data['nspResponseCodeDescription']) and trim_gks($r_data['nspResponseCodeDescription'])!='') {
      $message.="\n".$r_data['nspResponseCodeDescription'];
    }

    
    
    break;
    
}

//die($eftpos_system.' '.$id_eftpos_transaction.' '.$transaction_status);
 

if ($id_eftpos_transaction>0) {
  
  $transaction_type='';
  $paroxos_signature_id=0;
  
  $sql="select * from gks_eftpos_transaction where id_eftpos_transaction=".$id_eftpos_transaction;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    echo 'error:sql error'; die();}  
  if ($result->num_rows==1) {
    $row_eftpos = $result->fetch_assoc();
    //$company_id=intval($row_eftpos['id_company']);
    $transaction_type=trim_gks($row_eftpos['transaction_type']);
    $paroxos_signature_id=intval($row_eftpos['paroxos_signature_id']);
  }
  //debug_mail(false,'sql 1',$sql.' '.$transaction_type);
  
     
  $company_id=-1;
  switch ($eftpos_system) {
    case 'cardlink_ecr2eftweb': 
      if (isset($t_data['mid']) and trim_gks($t_data['mid'])!='') {
        $company_id=0;
        $cardlink_mid=''; if (isset($t_data['mid'])) $cardlink_mid=trim_gks($t_data['mid']);
        if ($cardlink_mid!='') {
          $sql="select id_company from gks_company where company_disable=0 and cardlink_mid='".$db_link->escape_string($cardlink_mid)."'";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            echo 'error:sql error'; die();}
          if ($result->num_rows==1) {
            $row = $result->fetch_assoc();
            $company_id=intval($row['id_company']);
          }
        }
      }
      break;
    case 'megeftpos':
      $company_id=-1;
      break;  
  }

  $installments=0;
  $tipAmount=0;
  switch ($eftpos_system) {
    case 'cardlink_ecr2eftweb':   
      if (isset($t_data['numberOfInstallments'])) $installments=intval($t_data['numberOfInstallments']);
      break;
    case 'megeftpos':
      if (isset($t_data['tipAmount'])) $tipAmount=floatval($t_data['tipAmount']);
      break;
  }

  
  $sql="update gks_eftpos_transaction set 
  mymessage='".$db_link->escape_string($message)."',
  transaction_status='".$transaction_status."',
  transactionId='".$db_link->escape_string($transactionId)."',
  aadeTransactionId='".$db_link->escape_string($aadeTransactionId)."',
  response_array='".$db_link->escape_string($response_data)."',";
  if ($company_id>=0) {
    $sql.="company_id=".$company_id.",";
  }
  $sql.="
  installments=".$installments.",
  tipAmount=".$tipAmount.",
  
  mydate_edit=now(),
  user_id_edit=2,
  myip='".$db_link->escape_string($gkIP)."'
  where id_eftpos_transaction=".$id_eftpos_transaction."
  and (transaction_status='draft' or transaction_status='async')
  and (transactionId='' or transactionId is null)
  and payment_acquirer_with_id=".$payment_acquirer_with_id;
  //die($sql);
  //debug_mail(false,'sql 1',$sql);
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    echo 'error:sql error'; die();}

  if ($paroxos_signature_id>0) {
    if ($transaction_status=='done')  {
      $sql="update gks_paroxos_signature set 
      signature_status='used' 
      where id_paroxos_signature=".$paroxos_signature_id."
      and signature_status in ('assign')";
    } else {
      $sql="update gks_paroxos_signature set 
      signature_status='canreuse' 
      where id_paroxos_signature=".$paroxos_signature_id."
      and signature_status in ('assign')";
    }
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      echo 'error:sql error'; die();}
    
  }

  switch ($eftpos_system) {
    case 'cardlink_ecr2eftweb':   
  
      
      
      $fan=array('amountPayable'=>1,'amount'=>1);
    
      $fas=array('msgType'=>1,'paymentSpecs'=>1,'authorizationCode'=>1,'city'=>1,'msgOptions'=>1,'mid'=>1,
      'responseCodeMessage'=>1,'cardExpiryDate'=>1,'txnDateTime'=>1,'responseCode'=>1,'merchantName'=>1,
      'uniquePaymentIdECR'=>1,'referenceNumber'=>1,'sn'=>1,'msgCode'=>1,'acquirerName'=>1,'applicationName'=>1,
      'batchNumber'=>1,'address'=>1,'eftTerminalId'=>1,'length'=>1,'cardType'=>1,'sessionId'=>1,
      'numberOfPostdatedInstallments'=>1,'accountNumber'=>1,'tc'=>1,'token'=>1,'transactionType'=>1,'bankId'=>1,
      'go4moreProducts'=>1,'uniquePaymentId'=>1,'tlvData'=>1,'numberOfInstallments'=>1,'phone'=>1,
      'posTerminalVersion'=>1,'aid'=>1,
      'signature'=>1,
      'cashierNumber'=>1,
      'invoiceNumber'=>1,
      'tillNumber'=>1,
      'agreementDate'=>1,
      'agreementNumber'=>1,
      'checkinDate'=>1,
      'checkoutDate'=>1,
      'roomNumber'=>1,
      'ecrToken'=>1,
    
      );
      break;
    case 'megeftpos':
    
      $fan=array('invoiceAmount'=>1,'originalAmount'=>1,'paidAmount'=>1,'loyaltyAmount'=>1,'tipAmount'=>1);
      $fas=array(
        'nspResponseCode'=>1,
        'nspResponseCodeDescription'=>1,
        'ecrReferenceNumber'=>1,
        'nspReferenceNumber'=>1,
        'receiptNumber'=>1,
        'transactionTimestamp'=>1,
        'bankAuthorizationCode'=>1,
        'bankCode'=>1,
        'cardNumber'=>1,
        'cardType'=>1,
        'cardHolder'=>1,
        //'responseCode'=>1,
      );
      break;
  }
  //print_r($t_data);die();
  $new_fileds=[];
  $sqlF=array();$sqlV=array();  $sqlU=array();
  foreach ($t_data as $key => $value) {
    if (isset($fas[$key])) {
      $sqlF[]=$key;
      $sqlV[]="'".$db_link->escape_string($value)."'";
      $sqlU[]=$key."='".$db_link->escape_string($value)."'";
      $fas[$key]=0;
    } else if (isset($fan[$key])) {
      $sqlF[]=$key;
      if ($value=='') {
        $sqlV[]='null';
        $sqlU[]=$key."=null";
      } else if ($key=='amountPayable') {
        $value=floatval($value);
        $sqlV[]=(intval($value)/100).'';
        $sqlU[]=$key."=".(intval($value)/100).'';
      } else if ($key=='amount') {
        $value=floatval($value);
        $sqlV[]=number_format($value, 2, '.', '');
        $sqlU[]=$key."=".number_format($value, 2, '.', '');
      } else {
        $value=floatval($value);
        $sqlV[]=number_format($value, 2, '.', '');
        $sqlU[]=$key."=".number_format($value, 2, '.', '');
      }
      $fan[$key]=0;
    } else {
      if ($key!='responseCode') {
        $new_fileds[]=$key;
      }
    }
    
  } 
  if (count($new_fileds)>0)  {
    debug_mail(false,'new fields in api-gkserpapp-eftpos.php',print_r($new_fileds,true)."\n".print_r($t_data,true));
  }


  switch ($eftpos_system) {
  case 'cardlink_ecr2eftweb':    
    $sql="select id_cardlink_transaction as id_othertable_transaction from gks_cardlink_transaction
    where eftpos_transaction_id=".$id_eftpos_transaction;
    break;
  case 'megeftpos':
    $sql="select id_megeftpos_transaction as id_othertable_transaction from gks_megeftpos_transaction
    where eftpos_transaction_id=".$id_eftpos_transaction;
    break;
  }
    
  //debug_mail(false,'sql 2',$sql);
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    echo 'error:sql error'; die();}
  if ($result->num_rows==0) {  
    switch ($eftpos_system) {
    case 'cardlink_ecr2eftweb':    
      $sql="insert into gks_cardlink_transaction (";  break;
    case 'megeftpos':
      $sql="insert into gks_megeftpos_transaction ("; break;
    }
    $sql.="
    user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
    xeiristis_id,add_from_system,myfrom,
    ".implode(',',$sqlF).",
    myerror
    ) values (
    ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
    0,'gks ERP App Desktop','ecr2eftweb',
    ".implode(',',$sqlV).",
    '".$db_link->escape_string($r_error)."'
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      echo 'error:sql error'; die();}  
    
    $id_othertable_transaction = $db_link->insert_id;
    

        
    $sql="update gks_eftpos_transaction set
    xxx_transaction_id=".$id_othertable_transaction."
    where id_eftpos_transaction=".$id_eftpos_transaction;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      echo 'error:sql error'; die();}
    
    
  } else {
    $row=$result->fetch_assoc();
    $id_othertable_transaction=$row['id_othertable_transaction'];

    
    switch ($eftpos_system) {
    case 'cardlink_ecr2eftweb':    
      $sql="update gks_cardlink_transaction set ";  
      $sql_where="id_cardlink_transaction=".$id_othertable_transaction;
      break;
    case 'megeftpos':
      $sql="update gks_megeftpos_transaction set 
      responseCode=".$r_responseCode.",
      ";; 
      $sql_where="id_megeftpos_transaction=".$id_othertable_transaction;
      break;
    }
    
    if (count($sqlU)>0) $sql.=implode(',',$sqlU).",";
    
    $sql.="
    trans_status='done',
    myerror='".$db_link->escape_string($r_error)."',
    mymessage='".$db_link->escape_string($message)."',
    mydate_edit=now(),
    user_id_edit=2,
    myip='".$db_link->escape_string($gkIP)."'
    where ".$sql_where;
    //debug_mail(false,'run sql',$sql);
    //debug_mail(false,'sql 3',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      echo 'error:sql error'; die();}  
    
    
  }
  
  
  if (in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) {
    //debug_mail(false,'check 2',$transaction_type);
    
    $sql="select * from gks_eftpos_transaction
    where id_eftpos_transaction=".$id_eftpos_transaction."
    AND transaction_status='done'";
    //debug_mail(false,'sql 2',$sql.' '.$transaction_type);
    //debug_mail(false,'sql 2',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      echo 'error:sql error'; die();}
    if ($result->num_rows==1) {
      
      $row_eftpos_curr = $result->fetch_assoc(); 
      
      $t_gks_acc_xxx_payment='';
      $f_acc_xxx_id='';
      $f_acc_xxx_payment_id='';
      $f_id_acc_xxx_payment='';
      if ($row_eftpos_curr['acc_inv_payment_id']>0) {
        $t_gks_acc_xxx_payment='gks_acc_inv_payment';
        $f_acc_xxx_id='acc_inv_id';
        $f_acc_xxx_payment_id='acc_inv_payment_id';
        $f_id_acc_xxx_payment='id_acc_inv_payment';
      } else if ($row_eftpos_curr['acc_pay_payment_id']>0) {
        $t_gks_acc_xxx_payment='gks_acc_pay_payment';
        $f_acc_xxx_id='acc_pay_id';
        $f_acc_xxx_payment_id='acc_pay_payment_id';
        $f_id_acc_xxx_payment='id_acc_pay_payment';
      }      
      
      
      $my_this=intval($row_eftpos_curr['id_eftpos_transaction']);
      
      $sql="select my_for from gks_eftpos_transaction_thisisfor
      where my_this=".$my_this."
      and my_is='".$db_link->escape_string($transaction_type)."'
      and my_for>0";
      //debug_mail(false,'sql 3',$sql.' '.$transaction_type);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        echo 'error:sql error'; die();}
      if ($result->num_rows==1) {
        $row = $result->fetch_assoc(); 
        $my_for=intval($row['my_for']);
        
        $sql="select * from ".$t_gks_acc_xxx_payment."
        where transaction_id=".$my_for;
        //debug_mail(false,'sql 4',$sql.' '.$transaction_type);
        
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          echo 'error:sql error'; die();}
        if ($result->num_rows==1) {
          $row_acc_xxx_payment = $result->fetch_assoc();
          if ($t_gks_acc_xxx_payment=='gks_acc_pay_payment') {
            $acc_pay_method_id=$row_acc_xxx_payment['acc_pay_method_id'];
          }
              
          //ean iparxei idi
          $sql="select * from ".$t_gks_acc_xxx_payment."
          where transaction_id=".$my_this;
          //debug_mail(false,'sql 5',$sql.' '.$transaction_type);
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            echo 'error:sql error'; die();}
        
          
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
            //debug_mail(false,'sql 6',$sql.' '.$transaction_type);
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              echo 'error:sql error'; die();}
            
            
            $acc_xxx_payment_id = $db_link->insert_id;
            
            $sql="UPDATE gks_eftpos_transaction 
            SET ".$f_acc_xxx_payment_id."=".$acc_xxx_payment_id."
            where id_eftpos_transaction=".$id_eftpos_transaction."
            AND transaction_status='done'";
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              echo 'error:sql error'; die();}
  
            //debug_mail(false,'sql 7',$sql.' '.$transaction_type);          
            //echo 'ggggggg11117';die();
              
          }
            
        }
        
        
        //todo gia pay
        
      }
      
      
    }
      
    
    
  }
  
  
}




echo $responseok;
die();
//print_r($data);
//die();
