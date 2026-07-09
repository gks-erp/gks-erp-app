<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
$my_page_title=gks_lang('Viva Webhook'); 

//https://developer.vivawallet.com/webhooks-for-payments/


//debug_mail(false,$my_page_title.' getallheaders','<pre>'.print_r(getallheaders(),true).'</pre>');
//debug_mail(false,$my_page_title.' php_input','<pre>'.$php_input.'</pre>');

$php_input = trim_gks(file_get_contents('php://input'));

$from=''; if (isset($_GET['from'])) $from=trim_gks($_GET['from']);
$id_company=0; if (isset($_GET['cid'])) $id_company=intval($_GET['cid']);
if ($from=='' or $id_company<1) {
  debug_mail(false,$my_page_title.' ping error from other','');
  echo 'error'; die();}

$mydata=false;
if ($php_input!='') {
  $mydata = json_decode($php_input,true);
  if (is_array($mydata) && isset($mydata['EventData'])) {
    $EventData=$mydata['EventData'];
    if (isset($EventData['BankId']) and $EventData['BankId']=='NET_IRIS') {
      sleep(10); //seconds
    }
  }
}


db_open();
stat_record();


if ($php_input=='') { //einai apo verification to webhook url
  $sql="select viva_verify_webhook_page_key from gks_company 
  where id_company=".$id_company." 
  and company_disable=0
  and viva_merchant_id<>''
  and viva_api_key<>''
  and viva_verify_webhook_page_key<>''";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    $viva_verify_webhook_page_key = trim_gks($row['viva_verify_webhook_page_key']);
    echo '{"Key":"'.$viva_verify_webhook_page_key.'"}'; 
    debug_mail(false,$my_page_title.' OK verification webhook','');
    die();  
  }
  debug_mail(false,$my_page_title.' error verification webhook','');    
  echo 'error'; die();
  
}



/*

Nea Pliromi                             https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?cid=1&from=tpc      //Transaction Payment Created
Nea kinisi logariasmou                  https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?cid=1&from=atc      //Account Transaction Created
Dimiourgia Entolis Trapezikis Metaforas https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?cid=1&from=cbtc     //Command Bank Transfer Created
Ektelesi Entolis Trapezikis Metaforas   https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?cid=1&from=cbte     //Command Bank Transfer Executed
Dimiourgithike Ypochreosi               https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?cid=1&from=oc       //Obligation Created
I ypochreosi katagrafike                https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?cid=1&from=oca      //Obligation Captured
Ypochreosi akyrothike                   https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?cid=1&from=ocancel  //Obligation Cancelled
I synallagi apetyche                    https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?cid=1&from=tf	     //Transaction Failed
Antilogismos Pliromis                   https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?cid=1&from=trc      //Transaction Reversal Created



//// https://demo.gks.gr/my/admin-viva-webhook-page-tpc.php?from=tpcalc  //Transaction Price Calculated


*/









if (is_array($mydata) && isset($mydata['EventData'])) {
  $EventData=$mydata['EventData'];
  $id_viva_transaction=0;
  
  //$OrderCode='';
  //if (isset($EventData['OrderCode']) and trim_gks($EventData['OrderCode']) != '') $OrderCode=trim_gks($EventData['OrderCode']);
  //if ($OrderCode=='0') $OrderCode='';
  
  $TransactionId='';
  if (isset($EventData['TransactionId']) and trim_gks($EventData['TransactionId']) != '') $TransactionId=trim_gks($EventData['TransactionId']);
  
  $MerchantTrns='';
  if (isset($EventData['MerchantTrns']) and trim_gks($EventData['MerchantTrns']) != '') $MerchantTrns=trim_gks($EventData['MerchantTrns']);

  
  if ($TransactionId!='' or $MerchantTrns!='') {
    $sql="select id_viva_transaction from gks_viva_transaction where ";

    $myw=[];
    if ($TransactionId!='') $myw[]="TransactionId='".$db_link->escape_string($TransactionId)."'";
    if ($MerchantTrns!='') $myw[]="MerchantTrns='".$db_link->escape_string($MerchantTrns)."'";
    $sql.=implode(' or ',$myw);
    
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $id_viva_transaction=$row['id_viva_transaction'];
    }
  }
  
  if ($id_viva_transaction==0) {
    $sql="insert into gks_viva_transaction (
    add_date,edit_date,user_id_add,user_id_edit,myip,add_from_system
    ) values (";
    if (isset($EventData['InsDate']) && trim_gks($EventData['InsDate'])!='') {
      $temp=$EventData['InsDate'];
//      if (substr($temp, strlen($temp)-3,1)==':') { //2021-07-20T16:44:38.07+03:00
      if (substr($temp, strlen($temp)-6,1)=='+') { //2021-07-20T16:44:38.07+03:00
        $temp=strtotime($temp);
        //echo 'kkkkkkkkk';
      } else {
        $temp=_time_user(strtotime($temp),-1);
        //echo 'hhhhhhhhhhh';
      }
      $sql.="'".date('Y-m-d H:i:s',$temp)."',";
    } else if (isset($EventData['Created']) && trim_gks($EventData['Created'])!='') {
      $temp=$EventData['Created'];
      if (substr($temp, strlen($temp)-3,1)==':') { //2021-07-20T16:44:38.07+03:00
        $temp=strtotime($temp);
      } else {
        $temp=_time_user(strtotime($temp),-1);
      }
      $sql.="'".date('Y-m-d H:i:s',$temp)."',";
    } else if (isset($mydata['Created']) && trim_gks($mydata['Created'])!='') {
      $temp=$mydata['Created'];
      if (substr($temp, strlen($temp)-3,1)==':') { //2021-07-20T16:44:38.07+03:00
        $temp=strtotime($temp);
      } else {
        $temp=_time_user(strtotime($temp),-1);
      }
      $sql.="'".date('Y-m-d H:i:s',$temp)."',";
    } else {
      $sql.="now(),";
    }
    $sql.="now(),0,0,
    '".$db_link->escape_string($gkIP)."',
    'webhook'
    )";
    $result = $db_link->query($sql);        
    if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
    $id_viva_transaction = $db_link->insert_id;   
  }
  //debug_mail(false,'viva sql test',$sql);
  
  $sql='';
  $sql.="edit_date=now(),";
  $sql.="user_id_edit=0,";
  $sql.="myip='".$db_link->escape_string($gkIP)."',";  
  $myfrom='';if (isset($_GET['from'])) $myfrom=trim_gks($_GET['from']);
  $sql.="myfrom='".$db_link->escape_string($myfrom)."',";  


  $sql.="myjson='".$db_link->escape_string($php_input)."',";


  if (isset($mydata['EventTypeId']) && intval($mydata['EventTypeId'])!=0) $sql.="EventTypeId=".intval($mydata['EventTypeId']).",";
  if (isset($mydata['Created']) && trim_gks($mydata['Created'])!='') $sql.="Created='".date('Y-m-d H:i:s',strtotime($mydata['Created']))."',";
  if (isset($EventData['Amount'])) $sql.="Amount=".number_format(floatval($EventData['Amount']),8,'.','').",";

  if (isset($EventData['CardNumber'])) $sql.="CardNumber='".$db_link->escape_string(mb_substr(trim_gks($EventData['CardNumber']),0,240))."',";
  if (isset($EventData['CardTypeId'])) $sql.="CardTypeId=".intval($EventData['CardTypeId']).","; //0(Visa), 1(Mastercard), 2(Diners), 3(Amex), 4(Invalid), 5(Unknown), 6(Maestro), 7(Discover), 8(JCB)
  if (isset($EventData['CardTypeId'])) {
    if ($EventData['CardTypeId']==0) $sql.="CardTypeName='VISA',";
    else if ($EventData['CardTypeId']==1) $sql.="CardTypeName='MASTERCARD',";
    else if ($EventData['CardTypeId']==2) $sql.="CardTypeName='DINERS',";
    else if ($EventData['CardTypeId']==3) $sql.="CardTypeName='AMEX',";
    else if ($EventData['CardTypeId']==4) $sql.="CardTypeName='Invalid',";
    else if ($EventData['CardTypeId']==5) $sql.="CardTypeName='Unknown',";
    else if ($EventData['CardTypeId']==6) $sql.="CardTypeName='MAESTRO',";
    else if ($EventData['CardTypeId']==7) $sql.="CardTypeName='DISCOVER',";
    else if ($EventData['CardTypeId']==8) $sql.="CardTypeName='JCB',";
  }
  
  if (isset($EventData['CompanyName'])) $sql.="CompanyName='".$db_link->escape_string(mb_substr(trim_gks($EventData['CompanyName']),0,240))."',";
  if (isset($EventData['CurrencyCode'])) $sql.="CurrencyCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['CurrencyCode']),0,240))."',";
  if (isset($EventData['CurrentInstallment'])) $sql.="CurrentInstallment=".intval($EventData['CurrentInstallment']).","; 
  if (isset($EventData['CustomerTrns'])) $sql.="CustomerTrns='".$db_link->escape_string(mb_substr(trim_gks($EventData['CustomerTrns']),0,240))."',";
  if (isset($EventData['Email'])) $sql.="Email='".$db_link->escape_string(mb_substr(trim_gks($EventData['Email']),0,240))."',";
  if (isset($EventData['FullName'])) $sql.="FullName='".$db_link->escape_string(mb_substr(trim_gks($EventData['FullName']),0,240))."',";
  if (isset($EventData['InsDate']) && trim_gks($EventData['InsDate'])!='') $sql.="InsDate='".date('Y-m-d H:i:s',strtotime($EventData['InsDate']))."',";
  if (isset($EventData['MerchantId'])) $sql.="MerchantId='".$db_link->escape_string(mb_substr(trim_gks($EventData['MerchantId']),0,240))."',";
  if (isset($EventData['MerchantTrns'])) $sql.="MerchantTrns='".$db_link->escape_string(mb_substr(trim_gks($EventData['MerchantTrns']),0,240))."',";
  if (isset($EventData['OrderCode'])) $sql.="OrderCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['OrderCode']),0,240))."',";
  if (isset($EventData['ParentId'])) $sql.="ParentId='".$db_link->escape_string(mb_substr(trim_gks($EventData['ParentId']),0,240))."',";
  if (isset($EventData['ResellerCompanyName'])) $sql.="ResellerCompanyName='".$db_link->escape_string(mb_substr(trim_gks($EventData['ResellerCompanyName']),0,240))."',";
  if (isset($EventData['ResellerId'])) $sql.="ResellerId='".$db_link->escape_string(mb_substr(trim_gks($EventData['ResellerId']),0,240))."',";
  if (isset($EventData['ResellerSourceAddress'])) $sql.="ResellerSourceAddress='".$db_link->escape_string(mb_substr(trim_gks($EventData['ResellerSourceAddress']),0,240))."',";
  if (isset($EventData['ResellerSourceCode'])) $sql.="ResellerSourceCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['ResellerSourceCode']),0,240))."',";
  if (isset($EventData['ResellerSourceName'])) $sql.="ResellerSourceName='".$db_link->escape_string(mb_substr(trim_gks($EventData['ResellerSourceName']),0,240))."',";
  if (isset($EventData['SourceCode'])) $sql.="SourceCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['SourceCode']),0,240))."',";
  if (isset($EventData['StatusId'])) $sql.="StatusId='".$db_link->escape_string(mb_substr(trim_gks($EventData['StatusId']),0,240))."',";
  if (isset($EventData['TargetPersonId'])) $sql.="TargetPersonId='".$db_link->escape_string(mb_substr(trim_gks($EventData['TargetPersonId']),0,240))."',";
  if (isset($EventData['TotalCommission'])) $sql.="TotalCommission=".number_format(floatval($EventData['TotalCommission']),8,'.','').",";
  if (isset($EventData['TotalFee'])) $sql.="TotalFee=".number_format(floatval($EventData['TotalFee']),8,'.','').",";
  if (isset($EventData['TotalInstallments'])) $sql.="TotalInstallments=".intval($EventData['TotalInstallments']).","; 
  if (isset($EventData['TransactionId'])) $sql.="TransactionId='".$db_link->escape_string(mb_substr(trim_gks($EventData['TransactionId']),0,240))."',";
  if (isset($EventData['TransactionTypeId'])) $sql.="TransactionTypeId=".intval($EventData['TransactionTypeId']).","; 
  

  if (isset($EventData['Moto'])) $sql.="Moto='".$db_link->escape_string(mb_substr(trim_gks($EventData['Moto']),0,240))."',";
  if (isset($EventData['Phone'])) $sql.="Phone='".$db_link->escape_string(mb_substr(trim_gks($EventData['Phone']),0,240))."',";
  if (isset($EventData['BankId'])) $sql.="BankId='".$db_link->escape_string(mb_substr(trim_gks($EventData['BankId']),0,240))."',";
  if (isset($EventData['Systemic'])) $sql.="Systemic='".$db_link->escape_string(mb_substr(trim_gks($EventData['Systemic']),0,240))."',";
  if (isset($EventData['Switching'])) $sql.="Switching='".$db_link->escape_string(mb_substr(trim_gks($EventData['Switching']),0,240))."',";
  if (isset($EventData['ChannelId'])) $sql.="ChannelId='".$db_link->escape_string(mb_substr(trim_gks($EventData['ChannelId']),0,240))."',";
  if (isset($EventData['TerminalId'])) $sql.="TerminalId='".$db_link->escape_string(mb_substr(trim_gks($EventData['TerminalId']),0,240))."',";
  if (isset($EventData['ProductId'])) $sql.="ProductId='".$db_link->escape_string(mb_substr(trim_gks($EventData['ProductId']),0,240))."',";
  if (isset($EventData['DualMessage'])) $sql.="DualMessage='".$db_link->escape_string(mb_substr(trim_gks($EventData['DualMessage']),0,240))."',";
  if (isset($EventData['CardToken'])) $sql.="CardToken='".$db_link->escape_string(mb_substr(trim_gks($EventData['CardToken']),0,240))."',";
  if (isset($EventData['TipAmount'])) $sql.="TipAmount=".number_format(floatval($EventData['TipAmount']),8,'.','').",";
  if (isset($EventData['SourceName'])) $sql.="SourceName='".$db_link->escape_string(mb_substr(trim_gks($EventData['SourceName']),0,240))."',";
  if (isset($EventData['Latitude'])) $sql.="Latitude=".number_format(floatval($EventData['Latitude']),8,'.','').",";
  if (isset($EventData['Longitude'])) $sql.="Longitude=".number_format(floatval($EventData['Longitude']),8,'.','').",";
  if (isset($EventData['CompanyTitle'])) $sql.="CompanyTitle='".$db_link->escape_string(mb_substr(trim_gks($EventData['CompanyTitle']),0,240))."',";
  if (isset($EventData['PanEntryMode'])) $sql.="PanEntryMode='".$db_link->escape_string(mb_substr(trim_gks($EventData['PanEntryMode']),0,240))."',";
  if (isset($EventData['ReferenceNumber'])) $sql.="ReferenceNumber='".$db_link->escape_string(mb_substr(trim_gks($EventData['ReferenceNumber']),0,240))."',";
  if (isset($EventData['ResponseCode'])) $sql.="ResponseCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['ResponseCode']),0,240))."',";
  if (isset($EventData['OrderCulture'])) $sql.="OrderCulture='".$db_link->escape_string(mb_substr(trim_gks($EventData['OrderCulture']),0,240))."',";
  if (isset($EventData['IsManualRefund'])) $sql.="IsManualRefund='".$db_link->escape_string(mb_substr(trim_gks($EventData['IsManualRefund']),0,240))."',";
  if (isset($EventData['TargetWalletId'])) $sql.="TargetWalletId='".$db_link->escape_string(mb_substr(trim_gks($EventData['TargetWalletId']),0,240))."',";
  if (isset($EventData['LoyaltyTriggered'])) $sql.="LoyaltyTriggered='".$db_link->escape_string(mb_substr(trim_gks($EventData['LoyaltyTriggered']),0,240))."',";
  if (isset($EventData['CardCountryCode'])) $sql.="CardCountryCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['CardCountryCode']),0,240))."',";
  if (isset($EventData['CardIssuingBank'])) $sql.="CardIssuingBank='".$db_link->escape_string(mb_substr(trim_gks($EventData['CardIssuingBank']),0,240))."',";
  if (isset($EventData['RedeemedAmount'])) $sql.="RedeemedAmount=".number_format(floatval($EventData['RedeemedAmount']),8,'.','').",";
  if (isset($EventData['ClearanceDate']) && trim_gks($EventData['ClearanceDate'])!='') $sql.="ClearanceDate='".date('Y-m-d H:i:s',strtotime($EventData['ClearanceDate']))."',";
  if (isset($EventData['BillId'])) $sql.="BillId='".$db_link->escape_string(mb_substr(trim_gks($EventData['BillId']),0,240))."',";
  if (isset($EventData['CardExpirationDate']) && trim_gks($EventData['CardExpirationDate'])!='') $sql.="CardExpirationDate='".date('Y-m-d H:i:s',strtotime($EventData['CardExpirationDate']))."',";
  if (isset($EventData['RetrievalReferenceNumber'])) $sql.="RetrievalReferenceNumber='".$db_link->escape_string(mb_substr(trim_gks($EventData['RetrievalReferenceNumber']),0,240))."',";
  if (isset($EventData['ResponseEventId'])) $sql.="ResponseEventId='".$db_link->escape_string(mb_substr(trim_gks($EventData['ResponseEventId']),0,240))."',";
  if (isset($EventData['ElectronicCommerceIndicator'])) $sql.="ElectronicCommerceIndicator='".$db_link->escape_string(mb_substr(trim_gks($EventData['ElectronicCommerceIndicator']),0,240))."',";

  if (isset($mydata['CorrelationId'])) $sql.="CorrelationId='".$db_link->escape_string(mb_substr(trim_gks($mydata['CorrelationId']),0,240))."',";
  if (isset($mydata['Delay'])) $sql.="Delay='".$db_link->escape_string(mb_substr(trim_gks($mydata['Delay']),0,240))."',";
  if (isset($mydata['MessageId'])) $sql.="MessageId='".$db_link->escape_string(mb_substr(trim_gks($mydata['MessageId']),0,240))."',";
  if (isset($mydata['RecipientId'])) $sql.="RecipientId='".$db_link->escape_string(mb_substr(trim_gks($mydata['RecipientId']),0,240))."',";
  if (isset($mydata['MessageTypeId'])) $sql.="MessageTypeId='".$db_link->escape_string(mb_substr(trim_gks($mydata['MessageTypeId']),0,240))."',";
  
  if (isset($EventData['PersonId'])) $sql.="PersonId='".$db_link->escape_string(mb_substr(trim_gks($EventData['PersonId']),0,240))."',";
  if (isset($EventData['WalletId'])) $sql.="WalletId='".$db_link->escape_string(mb_substr(trim_gks($EventData['WalletId']),0,240))."',";
  if (isset($EventData['IsInternal'])) $sql.="IsInternal='".$db_link->escape_string(mb_substr(trim_gks($EventData['IsInternal']),0,240))."',";
  if (isset($EventData['Description'])) $sql.="Description='".$db_link->escape_string(mb_substr(trim_gks($EventData['Description']),0,240))."',";
  if (isset($EventData['ValueDate']) && trim_gks($EventData['ValueDate'])!='') $sql.="ValueDate='".date('Y-m-d H:i:s',strtotime($EventData['ValueDate']))."',";
  if (isset($EventData['BankAccountId'])) $sql.="BankAccountId='".$db_link->escape_string(mb_substr(trim_gks($EventData['BankAccountId']),0,240))."',";
  if (isset($EventData['SaleTransactionId'])) $sql.="SaleTransactionId='".$db_link->escape_string(mb_substr(trim_gks($EventData['SaleTransactionId']),0,240))."',";
  if (isset($EventData['WalletTransactionId'])) $sql.="WalletTransactionId='".$db_link->escape_string(mb_substr(trim_gks($EventData['WalletTransactionId']),0,240))."',";
  if (isset($EventData['InternalDescription'])) $sql.="InternalDescription='".$db_link->escape_string(mb_substr(trim_gks($EventData['InternalDescription']),0,240))."',";
  
  if (isset($EventData['TypeId'])) $sql.="TypeId=".intval($EventData['TypeId']).","; 
  if (isset($EventData['SubTypeId'])) $sql.="SubTypeId=".intval($EventData['SubTypeId']).","; 
  
  if ($sql!='') {
    $sql=substr($sql, 0, strlen($sql)-1);
    $sql="update gks_viva_transaction set ".$sql." where id_viva_transaction=".$id_viva_transaction;
    $result = $db_link->query($sql);        
    if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
  }
  
  
  $is_from_webpay=false;
  if (isset($EventData['TerminalId']) and trim_gks($EventData['TerminalId'])!='' and 
      isset($EventData['OrderCode']) and trim_gks($EventData['OrderCode'])!='' and
      isset($EventData['StatusId']) and trim_gks($EventData['StatusId'])=='F') {
    $OrderCode=trim_gks($EventData['OrderCode']);
    $sql="SELECT gks_viva_orders.*
    FROM gks_viva_orders
    WHERE gks_viva_orders.order_code='".$db_link->escape_string($OrderCode)."' 
    order by id_viva_order desc limit 1";
    $result = $db_link->query($sql);  
    if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
    if ($result->num_rows==0) {
      if (trim_gks($EventData['TerminalId'])=='90000000' or trim_gks($EventData['TerminalId'])=='80000000') {
        debug_mail(false,'viva error gks_viva_orders not found',$sql);
      }
    } else {
      $is_from_webpay=true;
      $row = $result->fetch_assoc();
      $xeiristis_id=intval($row['xeiristis_id']);

      
      $amount=floatval($row['amount']);
      $mobile=trim_gks($row['mobile']);
      $gks_nickname=trim_gks($row['gks_nickname']);
      $date_start=trim_gks($row['date_start']);

      
      
      $start_msg=gks_lang('Ποσό').': '.number_format($amount/100, 2, ',', '.').'€';
      
      $tr_Amount= floatval($EventData['Amount']);
      if (abs($tr_Amount-$amount/100)>0.1) {
        $start_msg.="\n\n".gks_lang('ΠΡΟΣΟΧΗ: διαφορετικό ποσό').': '.number_format($tr_Amount, 2, ',', '.').'€'."\n\n";
      } 
      
      
      if ($xeiristis_id>0) {
        $sql="update gks_viva_transaction set xeiristis_id=".$xeiristis_id." where id_viva_transaction=".$id_viva_transaction." and (xeiristis_id=0 or xeiristis_id is null)";
        $result = $db_link->query($sql);  
        if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}
      }  

      
      $sql="update gks_viva_orders set 
      edit_date=now(),
      user_id_edit=".$xeiristis_id.",
      myip='".$db_link->escape_string($gkIP)."',
      order_status='complete'
      where order_status='draft' and order_code='".$db_link->escape_string($OrderCode)."' limit 1";
      $result = $db_link->query($sql);  
      if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
      
      if ($xeiristis_id>0) {
        $sql="select viber_id,viber_subscribed from wp_users where ID=".$xeiristis_id;
        $result = $db_link->query($sql);  
        if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
        if ($result->num_rows>0) {
          $row = $result->fetch_assoc();
          $viber_id=trim_gks($row['viber_id']);
          $viber_subscribed=intval($row['viber_subscribed']);
          if ($viber_id!='' and $viber_subscribed<>0) {
            
            $message=gks_lang('Η πληρωμή έχει γίνει μέσω Viva POS')."\n".$start_msg;
            //gks_my_viber($viber_id,$xeiristis_id,$message);
            //gks_my_viber('sKKkyjZhCQyvvSAm+gZQJQ==',1,$message);
          }
          
        }

      }
      
    }
    
        
  }
  
  
  if ($is_from_webpay == false) {
    $terminal_id='';  if (isset($EventData['TerminalId'])) $terminal_id=trim_gks($EventData['TerminalId']);

  
  
    $add_date='';

    
    if ($terminal_id!='') {
      $id_asset=0;
      $asset_last_user_id=0;
  
      $sql="SELECT id_asset,asset_last_user_id,viva_def_ref_pliromis FROM gks_assets WHERE viva_terminal_id like '".$db_link->escape_string($terminal_id)."'";
      $result = $db_link->query($sql);        
      if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
      if ($result->num_rows==0) {
        debug_mail(false,'viva error asset mobile not found',$sql);
      } else {
        $row = $result->fetch_assoc();
        $id_asset=$row['id_asset'];
        $asset_last_user_id=$row['asset_last_user_id'];
        $viva_def_ref_pliromis=trim_gks($row['viva_def_ref_pliromis']);
        if ($viva_def_ref_pliromis!='') {
          $sql="MerchantTrns='[".$db_link->escape_string(mb_substr(trim_gks($row['viva_def_ref_pliromis']),0,240))."]'";
          $sql="update gks_viva_transaction set ".$sql." where id_viva_transaction=".$id_viva_transaction." and (MerchantTrns='' or MerchantTrns is null)";
          $result = $db_link->query($sql);        
          if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
        }
        
        $sql="SELECT user_id
        FROM gks_assets_moves
        WHERE asset_id=".$id_asset." 
        AND mydate<='".$add_date."'
        AND user_id<>0
        ORDER BY mydate DESC limit 1";
        $result = $db_link->query($sql);        
        if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
        if ($result->num_rows>0) {
          $row = $result->fetch_assoc();
          $asset_last_user_id=$row['user_id'];
          //echo $asset_last_user_id;die();
        }
      }
      if ($asset_last_user_id>0) {
        $sql="update gks_viva_transaction set xeiristis_id=".$asset_last_user_id." where id_viva_transaction=".$id_viva_transaction." and (xeiristis_id=0 or xeiristis_id is null)";
        $result = $db_link->query($sql);  
        if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
      }
    }
    

  }

  //print_r($mydata);
  //die();
}


//debug_mail(false,$my_page_title,'');

echo 'ok';

//print_r($mydata);