<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
//define('GKS_VIVA_URL_WWW','https://www.vivapayments.com');
//define('GKS_VIVA_URL_WWW','https://demo.vivapayments.com');

//$gks_viva_url='https://www.vivapayments.com';

//developer
//$gks_viva_url='https://demo.vivapayments.com';
//echo '<pre>';print_r($_SERVER);die();

function gks_viva_api_get_transactions($mydaydif, $company_id, $in_transaction_id) {
  global $db_link;
  global $gkIP;
  

  //echo $mydaydif;die();
  
  if ($in_transaction_id=='') {
    $mydaydif=intval($mydaydif); 
    $company_id=intval($company_id); 
    if ($company_id<=0) {
      if (!$result) {debug_mail(false,'company is not set','');die('company is not set');}
    }
    $sql="select * from gks_company where id_company=".$company_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==0) {
      debug_mail(false,'company not found',$sql);die('company not found');
    } else {
      $row = $result->fetch_assoc();
      $gks_viva_Merchant_ID=trim_gks($row['viva_merchant_id']);
      $gks_viva_API_Key=($row['viva_api_key']);
      if ($gks_viva_Merchant_ID=='') {debug_mail(false,'Merchant ID is not set for company '.$company_id,$sql);die('Merchant ID is not set for company '.$company_id);}
      if ($gks_viva_API_Key=='') {debug_mail(false,'API Key is not set for company '.$company_id,$sql);die('API Key is not set for company '.$company_id);}
    }
  } else {
    $sql="select MerchantId from gks_viva_transaction where TransactionId='".$db_link->escape_string($in_transaction_id)."'";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==0) {
      debug_mail(false,'transaction not found',$sql);die('transaction not found');
    }
    $row = $result->fetch_assoc();
    $MerchantId=trim_gks($row['MerchantId']);
    if ($MerchantId=='') {
      debug_mail(false,'MerchantId is empty',$sql);die('MerchantId is empty');
    }
    $sql="select * from gks_company where viva_merchant_id like '".$db_link->escape_string($MerchantId)."'";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==0) {
      debug_mail(false,'company not found',$sql);die('company not found');
    } else {
      $row = $result->fetch_assoc();
      $gks_viva_Merchant_ID=trim_gks($row['viva_merchant_id']);
      $gks_viva_API_Key=($row['viva_api_key']);
      if ($gks_viva_Merchant_ID=='') {debug_mail(false,'Merchant ID is not set for company '.$company_id,$sql);die('Merchant ID is not set for company '.$company_id);}
      if ($gks_viva_API_Key=='') {debug_mail(false,'API Key is not set for company '.$company_id,$sql);die('API Key is not set for company '.$company_id);}
    }
  }
  
  //echo '<pre>';echo $gks_viva_Merchant_ID."\n".$gks_viva_API_Key."\n"; //die();
  
  $mytimenow=time() + $mydaydif*24*60*60; // + 0*24*60*60;
  $time_vardia=_time_user($mytimenow, 1);
  
  $time_vardia-= GKS_ERP_START_VARDIA*60*60;
  $today_vardia = date('Y-m-d',$time_vardia);
  $today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
  $today_vardia = _time_user($today_vardia, -1);
  $today_vardia_time = $today_vardia;
  $today_vardia = date('Y-m-d H:i:s', $today_vardia);


  $headers = array(
    'Content-Type:application/json',
    'Authorization: Basic '. base64_encode($gks_viva_Merchant_ID.':'.$gks_viva_API_Key)
  );
  
  if ($in_transaction_id!='') 
    $api_url=GKS_VIVA_URL_WWW.'/api/transactions/'.$in_transaction_id;
  else
    $api_url=GKS_VIVA_URL_WWW.'/api/transactions/?date='.showDate($today_vardia_time,'Y-m-d',1);
  
  //echo $api_url;die();
      
  $ch = curl_init($api_url);
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_TIMEOUT, 300);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
  $return = curl_exec($ch);
  curl_close($ch);
  
  //echo 'ret:'.$return."\n";die();
  $mydata=json_decode($return,true);
  
  if (is_array($mydata) and isset($mydata['Transactions']) and is_array($mydata['Transactions'])) {
    
    foreach ($mydata['Transactions'] as $EventData) {
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
        if ($result->num_rows==0) {
          $sql="insert into gks_viva_transaction (
          add_date,edit_date,user_id_add,user_id_edit,myip,add_from_system,TransactionId,myjson
          ) values (";
          
          if (isset($EventData['InsDate']) && trim_gks($EventData['InsDate'])!='') {
            $temp=$EventData['InsDate'];
            if (substr($temp, strlen($temp)-6,1)=='+') { //2021-07-20T16:44:38.07+03:00
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
          'cron',
          '".$db_link->escape_string($TransactionId)."',
          '".$db_link->escape_string(json_encode($EventData))."'
          )";
          $result = $db_link->query($sql);
          if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');}  
          $id_viva_transaction = $db_link->insert_id;   
          
        } else {
          $row = $result->fetch_assoc();
          $id_viva_transaction=$row['id_viva_transaction'];
        }
        
        $sql='';
        $sql.="myjson='".$db_link->escape_string(json_encode($EventData))."',";
        $sql.="edit_date=now(),";
        $sql.="user_id_edit=0,";
        $sql.="myip='".$db_link->escape_string($gkIP)."',";  
        
        if (isset($EventData['Fee'])) $sql.="TotalFee=".number_format(floatval($EventData['Fee']),8,'.','').",";
        if (isset($EventData['Commission'])) $sql.="TotalCommission=".number_format(floatval($EventData['Commission']),8,'.','').",";


        if (isset($EventData['CreatedBy'])) $sql.="CreatedBy='".$db_link->escape_string(mb_substr(trim_gks($EventData['CreatedBy']),0,240))."',";
        if (isset($EventData['AcquirerApproved'])) $sql.="AcquirerApproved='".$db_link->escape_string(mb_substr(trim_gks($EventData['AcquirerApproved']),0,240))."',";
        if (isset($EventData['AuthorizationId'])) $sql.="AuthorizationId='".$db_link->escape_string(mb_substr(trim_gks($EventData['AuthorizationId']),0,240))."',";

        if (isset($EventData['BankId'])) $sql.="BankId='".$db_link->escape_string(mb_substr(trim_gks($EventData['BankId']),0,240))."',";
        if (isset($EventData['ParentId'])) $sql.="ParentId='".$db_link->escape_string(mb_substr(trim_gks($EventData['ParentId']),0,240))."',";
        if (isset($EventData['Switching'])) $sql.="Switching='".$db_link->escape_string(mb_substr(trim_gks($EventData['Switching']),0,240))."',";
        if (isset($EventData['Amount'])) $sql.="Amount=".number_format(floatval($EventData['Amount']),8,'.','').",";
        if (isset($EventData['StatusId'])) $sql.="StatusId='".$db_link->escape_string(mb_substr(trim_gks($EventData['StatusId']),0,240))."',";
        if (isset($EventData['ChannelId'])) $sql.="ChannelId='".$db_link->escape_string(mb_substr(trim_gks($EventData['ChannelId']),0,240))."',";
        if (isset($EventData['MerchantId'])) $sql.="MerchantId='".$db_link->escape_string(mb_substr(trim_gks($EventData['MerchantId']),0,240))."',";
        if (isset($EventData['ResellerId'])) $sql.="ResellerId='".$db_link->escape_string(mb_substr(trim_gks($EventData['ResellerId']),0,240))."',";
        if (isset($EventData['InsDate']) && trim_gks($EventData['InsDate'])!='') $sql.="InsDate='".date('Y-m-d H:i:s',strtotime($EventData['InsDate']))."',";
        if (isset($EventData['TipAmount'])) $sql.="TipAmount=".number_format(floatval($EventData['TipAmount']),8,'.','').",";
        if (isset($EventData['SourceCode'])) $sql.="SourceCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['SourceCode']),0,240))."',";
        if (isset($EventData['PanEntryMode'])) $sql.="PanEntryMode='".$db_link->escape_string(mb_substr(trim_gks($EventData['PanEntryMode']),0,240))."',";
        if (isset($EventData['MerchantTrns'])) $sql.="MerchantTrns='".$db_link->escape_string(mb_substr(trim_gks($EventData['MerchantTrns']),0,240))."',";
        if (isset($EventData['CustomerTrns'])) $sql.="CustomerTrns='".$db_link->escape_string(mb_substr(trim_gks($EventData['CustomerTrns']),0,240))."',";
        if (isset($EventData['IsManualRefund'])) $sql.="IsManualRefund='".$db_link->escape_string(mb_substr(trim_gks($EventData['IsManualRefund']),0,240))."',";
        if (isset($EventData['TargetPersonId'])) $sql.="TargetPersonId='".$db_link->escape_string(mb_substr(trim_gks($EventData['TargetPersonId']),0,240))."',";
        if (isset($EventData['SourceTerminalId'])) $sql.="TerminalId='".$db_link->escape_string(mb_substr(trim_gks($EventData['SourceTerminalId']),0,240))."',";
        if (isset($EventData['RedeemedAmount'])) $sql.="RedeemedAmount=".number_format(floatval($EventData['RedeemedAmount']),8,'.','').",";
        if (isset($EventData['TotalInstallments'])) $sql.="TotalInstallments=".intval($EventData['TotalInstallments']).","; 
        if (isset($EventData['CurrentInstallment'])) $sql.="CurrentInstallment=".intval($EventData['CurrentInstallment']).","; 
        if (isset($EventData['ClearanceDate']) && trim_gks($EventData['ClearanceDate'])!='') $sql.="ClearanceDate='".date('Y-m-d H:i:s',strtotime($EventData['ClearanceDate']))."',";
        if (isset($EventData['ResellerSourceCode'])) $sql.="ResellerSourceCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['ResellerSourceCode']),0,240))."',";
        if (isset($EventData['RetrievalReferenceNumber'])) $sql.="RetrievalReferenceNumber='".$db_link->escape_string(mb_substr(trim_gks($EventData['RetrievalReferenceNumber']),0,240))."',";
      
        if (isset($EventData['Order']) and is_array($EventData['Order'])) {
          if (isset($EventData['Order']['OrderCode'])) $sql.="OrderCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['Order']['OrderCode']),0,240))."',";
          if (isset($EventData['Order']['RequestLang'])) $sql.="OrderCulture='".$db_link->escape_string(mb_substr(trim_gks($EventData['Order']['RequestLang']),0,240))."',";
          if (isset($EventData['Order']['ChannelId'])) $sql.="OrderChannelId='".$db_link->escape_string(mb_substr(trim_gks($EventData['Order']['ChannelId']),0,240))."',";
          if (isset($EventData['Order']['ResellerId'])) $sql.="OrderResellerId='".$db_link->escape_string(mb_substr(trim_gks($EventData['Order']['ResellerId']),0,240))."',";
          if (isset($EventData['Order']['SourceCode'])) $sql.="OrderSourceCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['Order']['SourceCode']),0,240))."',";
          if (isset($EventData['Order']['ResellerSourceCode'])) $sql.="OrderResellerSourceCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['Order']['ResellerSourceCode']),0,240))."',";
        }

        if (isset($EventData['Payment']) and is_array($EventData['Payment'])) {
          if (isset($EventData['Payment']['Email'])) $sql.="Email='".$db_link->escape_string(mb_substr(trim_gks($EventData['Payment']['Email']),0,240))."',";
          if (isset($EventData['Payment']['Phone'])) $sql.="Phone='".$db_link->escape_string(mb_substr(trim_gks($EventData['Payment']['Phone']),0,240))."',";
          if (isset($EventData['Payment']['FullName'])) $sql.="FullName='".$db_link->escape_string(mb_substr(trim_gks($EventData['Payment']['FullName']),0,240))."',";
          if (isset($EventData['Payment']['ChannelId'])) $sql.="PaymentChannelId='".$db_link->escape_string(mb_substr(trim_gks($EventData['Payment']['ChannelId']),0,240))."',";
          if (isset($EventData['Payment']['Installments'])) $sql.="PaymentInstallments=".intval($EventData['Payment']['Installments']).",";
          if (isset($EventData['Payment']['RecurringSupport'])) $sql.="PaymentRecurringSupport='".$db_link->escape_string(mb_substr(trim_gks($EventData['Payment']['RecurringSupport']),0,240))."',";
        }
      
        if (isset($EventData['TransactionType']) and is_array($EventData['TransactionType'])) {
          if (isset($EventData['TransactionType']['TransactionTypeId'])) $sql.="TransactionTypeId=".intval($EventData['TransactionType']['TransactionTypeId']).","; 
          if (isset($EventData['TransactionType']['Name'])) $sql.="TransactionTypeName='".$db_link->escape_string(mb_substr(trim_gks($EventData['TransactionType']['Name']),0,240))."',";
        }

        if (isset($EventData['CreditCard']) and is_array($EventData['CreditCard'])) {
          if (isset($EventData['CreditCard']['Token'])) $sql.="CardToken='".$db_link->escape_string(mb_substr(trim_gks($EventData['CreditCard']['Token']),0,240))."',";
          if (isset($EventData['CreditCard']['Number'])) $sql.="CardNumber='".$db_link->escape_string(mb_substr(trim_gks($EventData['CreditCard']['Number']),0,240))."',";
          if (isset($EventData['CreditCard']['CountryCode'])) $sql.="CardCountryCode='".$db_link->escape_string(mb_substr(trim_gks($EventData['CreditCard']['CountryCode']),0,240))."',";
          if (isset($EventData['CreditCard']['IssuingBank'])) $sql.="CardIssuingBank='".$db_link->escape_string(mb_substr(trim_gks($EventData['CreditCard']['IssuingBank']),0,240))."',";
          if (isset($EventData['CreditCard']['ExpirationDate']) && trim_gks($EventData['CreditCard']['ExpirationDate'])!='') $sql.="CardExpirationDate='".date('Y-m-d H:i:s',strtotime($EventData['CreditCard']['ExpirationDate']))."',";
      
          if (isset($EventData['CreditCard']['CardType']) and is_array($EventData['CreditCard']['CardType'])) {
            if (isset($EventData['CreditCard']['CardType']['CardTypeId'])) $sql.="CardTypeId=".intval($EventData['CreditCard']['CardType']['CardTypeId']).","; //0(Visa), 1(Mastercard), 2(Diners), 3(Amex), 4(Invalid), 5(Unknown), 6(Maestro), 7(Discover), 8(JCB)
            if (isset($EventData['CreditCard']['CardType']['Name'])) $sql.="CardTypeName='".$db_link->escape_string(mb_substr(trim_gks($EventData['CreditCard']['CardType']['Name']),0,240))."',";
            if (isset($EventData['CreditCard']['CardType']['CardHolderName'])) $sql.="CardHolderName='".$db_link->escape_string(mb_substr(trim_gks($EventData['CreditCard']['CardType']['CardHolderName']),0,240))."',";
          }
        }        

      
        if ($sql!='') {
          $sql=substr($sql, 0, strlen($sql)-1);
          $sql="update gks_viva_transaction set ".$sql." where id_viva_transaction=".$id_viva_transaction;
          $result = $db_link->query($sql);        
          if (!$result) { debug_mail(false,'viva error sql',$sql);die('sql error');} 
          //debug_mail(false,'viva sql update',$sql);
          
        }
        
        $terminal_id='';  if (isset($EventData['SourceTerminalId'])) $terminal_id=trim_gks($EventData['SourceTerminalId']);
        
      
      
      
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
      
    } 
    
  }
  
//  echo '<pre>';
//  echo $today_vardia."\n";
//  echo $api_url."\n";
////  //print $return;
  //print_r($mydata);
    
  
  return true;
}
  