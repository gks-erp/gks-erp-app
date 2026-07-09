<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

/*
https://developer.viva.com/downloads/iris/5.32.0.apk
https://developer.viva.com/downloads/iris/5.32.1-demo-1.apk
https://developer.viva.com/downloads/apps/Android_App_5.31.4_demo.apk

Since no mobile banking demo app exists, Viva simulates 
two specific amounts to enable end-to-end IRIS transaction testing on POS. 
For successful transactions, you may set the amount 7,34€. 
For failed transactions, you may set the amount 7,35€.

https://developer.viva.com/payment-methods/native-pos-payment-methods/iris-for-pos/
https://developer.viva.com/integration-reference/response-codes/#transactiontypeid-parameter
https://developer.viva.com/apis-for-point-of-sale/card-terminal-apps/android-app/sale/#sale-request
https://developer.viva.com/apis-for-point-of-sale/card-terminals-devices/rest-api/eft-pos-api-documentation/
https://developer.viva.com/apis-for-point-of-sale/card-terminals-devices/rest-api/eft-pos-api-documentation/#tag/Transactions/paths/~1ecr~1v1~1transactions:refund/post


*/



function gks_eftpos_get_base_url_accounts_viva() {
  if (GKS_VIVA_URL_WWW=='https://www.vivapayments.com') {
    return 'https://accounts.vivapayments.com';
  } else {
    return 'https://demo-accounts.vivapayments.com';
  }
}
function gks_eftpos_get_base_url_api_viva() {
  if (GKS_VIVA_URL_WWW=='https://www.vivapayments.com') {
    return 'https://api.vivapayments.com';
  } else {
    return 'https://demo-api.vivapayments.com';
  }
}



function gks_eftpos_has_transaction_type_viva($id) { //TransactionTypeId
  if ($id===null) return '';
  $id=intval($id);
  switch ($id) {
    case 0:  return 'Capture';
    case 1:  return 'Authorization';
    case 4:  return 'Refund Card';
    case 5:  return 'Card Charge';
    case 6:  return 'Card Charge with Installments';
    case 7:  return 'Payment Cancellation';
    case 8:  return 'Original Credit';
    case 9:  return 'VivaWallet Charge';
    case 11:  return 'VivaWallet Refund';
    case 13:  return 'Refund from Chargeback';
    case 15:  return 'Intra Bank Network Payment';
    case 16:  return 'Cash Payment';
    case 18:  return 'Refund Installments';
    case 19:  return 'Pay Out';
    case 20:  return 'Alipay Charge';
    case 21:  return 'Alipay Refund';
    case 22:  return 'Manual Cash Disbursement';
    case 23:  return 'iDeal Charge';
    case 24:  return 'iDeal Refund';
    case 25:  return 'P24 Charge';
    case 26:  return 'P24 Refund';
    case 27:  return 'Blik Charge';
    case 28:  return 'Blik Refund';
    case 29:  return 'PayU Charge';
    case 30:  return 'PayU Refund';
    case 31:  return 'Withdrawal';
    case 32:  return 'Multibanco Charge';
    case 34:  return 'GiroPay Charge';
    case 35:  return 'GiroPay Refund';
    case 36:  return 'SOFORT Banking Charge';
    case 37:  return 'SOFORT Banking Refund';
    case 38:  return 'EPS Charge';
    case 39:  return 'EPS Refund';
    case 40:  return 'WeChat Pay Charge';
    case 41:  return 'WeChat Pay Refund';
    case 42:  return 'BitPay Charge';
    case 44:  return 'DirectDebit Charge';
    case 45:  return 'DirectDebit Refund';
    case 46:  return 'DirectDebit Refund from Dispute';
    case 48:  return 'PayPal Charge';
    case 49:  return 'PayPal Refund';
    case 50:  return 'Trustly Charge';
    case 51:  return 'Trustly Refund';
    case 52:  return 'Klarna Charge';
    case 53:  return 'Klarna Refund';
    case 54:  return 'SibsPagamentos WayId Charge';
    case 55:  return 'SibsPagamentos WayId Refund';
    case 56:  return 'SibsPagamentos Reference Charge';
    case 57:  return 'SibsPagamentos Reference Refund';
    case 58:  return 'Payconiq Charge';
    case 59:  return 'Payconiq Refund';
    case 60:  return 'Iris Charge';
    case 61:  return 'Iris Refund';
    case 62:  return 'Pay by Bank Charge';
    case 63:  return 'Pay by Bank Refund';
    case 64:  return 'BancomatPay Charge';
    case 65:  return 'BancomatPay Refund';
    case 66:  return 'TBI BNPL Charge';
    case 67:  return 'TBI BNPL Refund';
    case 68:  return 'PayOnDelivery Charge';
    case 69:  return 'Card Verification';
    case 70:  return 'Swish Charge';
    case 71:  return 'Swish Refund';
    case 72:  return 'Blik Charge';
    case 73:  return 'Blik Refund';
    case 74:  return 'Bluecode Charge';
    case 75:  return 'Bluecode Refund';
    case 76:  return 'GiroPay Charge';
    case 77:  return 'GiroPay Refund';
    case 78:  return 'SatisPay Charge';
    case 79:  return 'SatisPay Refund';
    case 80:  return 'Klarna Authorization';
    case 81:  return 'Klarna Capture';
    case 84:  return 'IRIS void';

  
  }
  return 'ID '.$id;
}

 

function gks_eftpos_has_credentials_viva($row_company) {
  $return=array('success' => false, 'message' => 'generic error');
  if (trim_gks($row_company['viva_pos_client_id'])=='' or trim_gks($row_company['viva_pos_client_secret'])=='') {
    $return['message']=gks_lang('Δεν έχουν ορισθεί τα Διαπιστευτήρια POS APIs για την Viva στην εταιρεία').' '.$row_company['company_title'];
    debug_mail(false,$return['message'],print_r($row_company,true));return $return;
  }
  
  return array('success' => true, 'message' => 'OK', 'data' => array(
    'viva_pos_client_id' => $row_company['viva_pos_client_id'],
    'viva_pos_client_secret' => $row_company['viva_pos_client_secret'],
  ));  
}

function gks_eftpos_get_token_viva($row_company) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');

  $id_company_eftpos=0;
  
  $sql="select * from gks_company_eftpos 
  where payment_acquirer_with_id=1
  and company_id=".$row_company['id_company'];
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    $id_company_eftpos=intval($row['id_company_eftpos']);   
    if (time() < (strtotime($row['pc_token_expiration'])-100)) {
      return array('success' => true, 'message' => 'OK', 'data' => array(
        'access_token' => $row['pc_token_id'],
      ));       
      
    }
    
  }
    
    
  
  
  $headers = array(
    'Content-Type:application/x-www-form-urlencoded',
    'Authorization: Basic '. base64_encode($row_company['viva_pos_client_id'].':'.$row_company['viva_pos_client_secret'])
  );
  $url = gks_eftpos_get_base_url_accounts_viva().'/connect/token';
  //echo $url;die();
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
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
  

  if (!(isset($response_array['access_token']) and isset($response_array['expires_in']) and
        isset($response_array['token_type'])   and isset($response_array['scope']))) {
    $return['message']=gks_lang('Σφάλμα δεδομένων').' (4) '.$response;
    debug_mail(false,$return['message'],$response);return $return;}

  $access_token=trim_gks($response_array['access_token']);
  $pc_token_expiration=date('Y-m-d H:i:s',time() + intval($response_array['expires_in']));
  
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
    1,
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
    pc_token_expiration='".$pc_token_expiration."',
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



function gks_eftpos_sales_request_viva($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');
  
  $url=gks_eftpos_get_base_url_api_viva().'/ecr/v1/transactions:sale';
  //echo '<pre>'.$url;die();
  //echo '<pre>';print_r($data);die();
  
  
  $amount_viva=round(100*$data['amount'],0); // to 16 simenai 0.16
  $tip_amount_viva=round(100*$data['tipAmount'],0); // to 16 simenai 0.16
  
  $mypost=array();
  $mypost['sessionId']=$data['sessionId'];
  $mypost['terminalId']=$data['terminalId'];
  $mypost['cashRegisterId']=$data['cashRegisterId'];
  $mypost['amount']=$amount_viva;
  $mypost['tipAmount']=$tip_amount_viva;
  $mypost['currencyCode']='978';
  if ($data['merchantReference']!='') $mypost['merchantReference']=$data['merchantReference'];
  if ($data['customerTrns']!='') $mypost['customerTrns']=$data['customerTrns'];
  //$mypost['preauth']=false;
  $mypost['maxInstalments']=$data['installments'];
  $mypost['showTransactionResult']=true;
  $mypost['showReceipt']=true;
  
/*
CardPresent
MOTO
QrDefault
QrPayconic
AliPay
Paypal
Klarna
IRIS
*/
  

  $mypost['paymentMethod']='CardPresent';
  if (isset($data['preferred_payment_method']) and $data['preferred_payment_method']=='iris') {
    $mypost['paymentMethod']='IRIS';
    //echo '<pre>';print_r($data);die();
  }

    
  //$mypost['callback']='gkserpappmobile://vivaresult';
  
  
  if (isset($data['aadeProviderId'])) $mypost['aadeProviderId']=$data['aadeProviderId'];
  if (isset($data['aadeProviderSignatureData'])) $mypost['aadeProviderSignatureData']=$data['aadeProviderSignatureData'];
  if (isset($data['aadeProviderSignature'])) $mypost['aadeProviderSignature']=$data['aadeProviderSignature'];
  
/*
Allowed Values:
Valid URI: A full URI (scheme + path; query/fragment allowed) to which the terminal will return after completing the transaction.
finish :Special keyword signaling immediate backgrounding of the terminal app.
Empty / Missing : No change to current flow.
*/
  

  //Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1" "-"
  //Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/143.0.7499.151 Mobile/15E148 Safari/604.1" "-"
  
  //if (isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], '(iPhone; CPU iPhone OS')) {
  //  $mypost['interappCallback']='finish';
  //}
  //Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36
  //Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36
  //delete me
  //if (isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)')!==false) {
  //if (isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0 (Linux; Android ')!==false) {
  //  $mypost['interappCallback']='finish';
  //}
  //echo '<pre>'.$_SERVER['HTTP_USER_AGENT'];die();
  //$mypost['interappCallback']='gkserpappmobile://vivaresult';
  //echo '<pre>sssssssss ';print_r($mypost);die();
  
  //echo '<pre>sassssssssssss ';print_r($data);die();
  $viva_action_after=0;
  if (isset($data['row_asset']['viva_action_after'])) {
    $viva_action_after=intval($data['row_asset']['viva_action_after']);
    if ($viva_action_after<0 or $viva_action_after>6) $viva_action_after=0;
  }
  switch ($viva_action_after) {   
    case 0: //Automatic
      if (isset($_SERVER['HTTP_REFERER']) and strpos($_SERVER['HTTP_REFERER'], '&from=gks_erp_app_mobile&iderpappmobile=')!==false) {
        
      } else {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
          $useragent=$_SERVER['HTTP_USER_AGENT'];
          //if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
          if (strpos($useragent, '(iPhone; CPU iPhone OS')) {
            $mypost['interappCallback']='finish';  
          }
        }
      }
      break;
    case 1: //Tipota
      break;
    case 2: //Hide Viva
      $mypost['interappCallback']='finish';
      break;
    case 3: //Show gks ERP App Mobile
      $mypost['interappCallback']='gkserpappmobile://pos';
      //$mypost['interappCallback']='intent://URL#Intent;scheme=https;package=com.gks_gr.gks_erp_app_mobile;end';
      /*
      com.gks_gr.gks_erp_app_mobile
      <a href="intent://<URL>#Intent;scheme=http;package=com.android.chrome;end">
      <a href="intent://stackoverflow.com/questions/29250152/what-is-the-intent-to-launch-any-website-link-in-google-chrome#Intent;scheme=http;package=com.android.chrome;end"> 
      <a href="intent://stackoverflow.com/questions/29250152/what-is-the-intent-to-launch-any-website-link-in-google-chrome#Intent;scheme=http;action=android.intent.action.VIEW;end;">
      */
      break;
    case 4: //Show Chrome
      $mypost['interappCallback']='googlechrome://navigate?url=';
      break;
    case 5: //Show Safari
      $mypost['interappCallback']='safari://navigate?url=';
      break;
    case 6: //Show Firefox
      //org.mozilla.firefox", "org.mozilla.firefox.App/ intent
      //firefox://open-url?url=
      $mypost['interappCallback']='firefox://open-url?url=';
      //$mypost['interappCallback']='intent://URL#Intent;scheme=https;package=org.mozilla.firefox;end';
      break;
  }

  //echo '<pre>sassssssssssss ';print_r($mypost);die();

  
  global $GKS_CACHE_DB_VER;
  global $gks_cache_version;
  if (1==2) {
    $mypost['saleToAcquirerData']=base64_encode(json_encode(array(
      'applicationInfo'=> array(
        'externalPlatform' => array(
          'name'=> 'gks ERP',
          'version' => $GKS_CACHE_DB_VER.'.'.$gks_cache_version,
          'integrator' => 'gks Software',
        )
      )
    )));
  }
  
  
  $headers = array(
    'Content-Type:application/json',
    'Authorization: Bearer '.$data['access_token']
  );
  $mypostdata=json_encode($mypost);
  //echo '<pre>postargs ';print_r($data);print_r($headers);print_r($mypost);print $mypostdata; die();
  
  $sql="update gks_eftpos_transaction set send_array='".$db_link->escape_string($mypostdata)."'
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='sql error';
    debug_mail(false,'sql error viva',$sql);return $return;}
  
  
  
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

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/viva_sales_request_send_'.time().'.json',json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/viva_sales_request_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);


  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code .'<br>'.$response;
    debug_mail(false,'viva error',$response);return $return;}

  //to $response einai keno otan ginei epitixos
  //echo '<pre>response ';print_r($response_array); die();
  
  $MerchantTrns=''; if ($data['merchantReference']!='') $MerchantTrns=$data['merchantReference'];
  $CustomerTrns='';if ($data['customerTrns']!='') $CustomerTrns=$data['customerTrns'];
  
   
  

  $sql="insert into gks_viva_transaction (
  user_id_add,user_id_edit,add_date,edit_date,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  Amount,
  tipAmount,
  installments,
  CurrencyCode,
  StatusId,
  terminalId,
  MerchantTrns,
  CustomerTrns
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App','Viva Api',
  ".$data['id_eftpos_transaction'].",
  ".$data['amount'].",
  ".$data['tipAmount'].",
  ".$data['installments'].",
  978,
  'gks_wait',
  '".$db_link->escape_string($data['terminalId'])."',
  '".$db_link->escape_string($MerchantTrns)."',
  '".$db_link->escape_string($CustomerTrns)."'
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  $id_viva_transaction = $db_link->insert_id;
    
  
  $sql="update gks_eftpos_transaction set 
  transaction_status='async',
  xxx_transaction_id=".$id_viva_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='sql error';
    debug_mail(false,'sql error viva',$sql);return $return;}
  
  
  
  return array('success' => true, 'message' => 'OK', 'data' => array(
    //'access_token' => $access_token,
  )); 
    
}


function gks_eftpos_sales_request_get_status_viva($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error' , 'status'=>'agnosto');
  
  $url=gks_eftpos_get_base_url_api_viva().'/ecr/v1/sessions/'.$data['sessionId'];
  //echo '<pre>'.$url;die();
  
  $headers = array(
    'Content-Type:application/json',
    'Authorization: Bearer '.$data['access_token']
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

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/viva_sales_request_status_send_'.time().'.json',$url);
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/viva_sales_request_status_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);

  /*
  200 Successful Response
  202 The session is being processed
  400 Bad Request
  401 You were not authorized to execute this operation
  404 Session id was not found
  503 Unable to handle the request
  5xx Internal Server Error
  
  
  {"detail":"The session is being processed"
  */
  
  //debug_mail(false,'response',$gks_curl_http_code.' | '.$data['sessionId'].' | '.$response);
  
  //return array('success' => true, 'message' => 'OK', 'status' => 'wait');
  $transaction_type=$data['row_tra']['transaction_type'];
  
  
  if ($gks_curl_http_code==202) {
    //egine epitoxos
    $sql="update gks_eftpos_transaction set 
    transaction_status='processed',
    mymessage='processed',
    user_id_edit=".$my_wp_user_id.",
    mydate_edit=now(),
    myip='".$db_link->escape_string($gkIP)."'
    where payment_acquirer_with_id=1 
    AND transaction_status<>'done'
    AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}

    $sql="UPDATE gks_viva_transaction 
    LEFT JOIN gks_eftpos_transaction ON gks_viva_transaction.eftpos_transaction_id = gks_eftpos_transaction.id_eftpos_transaction 
    SET gks_viva_transaction.StatusId = 'gks_processed',
    gks_viva_transaction.mymessage='processed'
    where payment_acquirer_with_id=1 
    AND transaction_status<>'done'
    AND sessionId='".$db_link->escape_string($data['sessionId'])."'
    and (gks_viva_transaction.StatusId like 'gks%' or gks_viva_transaction.StatusId='' or gks_viva_transaction.StatusId is null)";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
          
    //if ($gkIP=='94.68.66.196') {
    //  echo '<pre>bbbbbbbbbbbb ';die();
    //}

    return array('success' => true, 'message' => 'OK', 'status' => 'processed'); 
  }
  
  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code .'<br>'.$response;
    debug_mail(false,'viva error',$response);return $return;}

  $response_array = json_decode($response, true);
  
  //echo $transaction_type.'|'.$gks_curl_http_code.'|';print_r($data);print_r($response_array);die();
  
  $id_eftpos_transaction=0;
  $paroxos_signature_id=0;
  $t_gks_acc_xxx_payment='';
  $f_acc_xxx_id='';
  $f_acc_xxx_payment_id='';
  $f_id_acc_xxx_payment='';
  $value_acc_xxx_payment_id=0;
      
  $sql="select id_eftpos_transaction,paroxos_signature_id,acc_inv_payment_id,acc_pay_payment_id 
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
    

    if (intval($row['acc_inv_payment_id'])>0) {
      $value_acc_xxx_payment_id=intval($row['acc_inv_payment_id']);
      $t_gks_acc_xxx_payment='gks_acc_inv_payment';
      $f_acc_xxx_id='acc_inv_id';
      $f_acc_xxx_payment_id='acc_inv_payment_id';
      $f_id_acc_xxx_payment='id_acc_inv_payment';
    } else if (intval($row['acc_pay_payment_id'])>0) {
      $value_acc_xxx_payment_id=intval($row['acc_pay_payment_id']);
      $t_gks_acc_xxx_payment='gks_acc_pay_payment';
      $f_acc_xxx_id='acc_pay_id';
      $f_acc_xxx_payment_id='acc_pay_payment_id';
      $f_id_acc_xxx_payment='id_acc_pay_payment';
    }
  }
  
  
  if (is_array($response_array) and isset($response_array['sessionId']) and isset($response_array['success'])) {
    if ($response_array['sessionId']!=$data['sessionId']) {
      $return['message']=gks_lang('Σφάλμα απόκρισης').'. '.gks_lang('Λάθος sessionId');
      debug_mail(false,'viva error',$response);return $return;}
    
    if ($response_array['success']==true and $response_array['message']=='Transaction successful') {
      $transactionId=''; if (isset($response_array['transactionId']))  $transactionId=$response_array['transactionId'];
      $aadeTransactionId=''; if (isset($response_array['aadeTransactionId']))  $aadeTransactionId=$response_array['aadeTransactionId'];
      
      $tipAmount=0;
      if (isset($response_array['tipAmount'])) $tipAmount=floatval($response_array['tipAmount'])/100;
      $installments=0;
      if (isset($response_array['installments'])) $installments=intval($response_array['installments']);
      
      $sql="update gks_eftpos_transaction set 
      transaction_status='done',
      mymessage='OK',
      tipAmount=".$tipAmount.",
      installments=".$installments.",
      transactionId='".$db_link->escape_string($transactionId)."',
      aadeTransactionId='".$db_link->escape_string($aadeTransactionId)."',
      response_array='".$db_link->escape_string($response)."',
      user_id_edit=".$my_wp_user_id.",
      mydate_edit=now(),
      myip='".$db_link->escape_string($gkIP)."'
      where payment_acquirer_with_id=1 
      AND transaction_status<>'done'
      AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}

      
      
      $sql="UPDATE gks_viva_transaction 
      LEFT JOIN gks_eftpos_transaction ON gks_viva_transaction.eftpos_transaction_id = gks_eftpos_transaction.id_eftpos_transaction 
      SET gks_viva_transaction.StatusId = 'gks_done',
      gks_viva_transaction.transactionId='".$db_link->escape_string($transactionId)."',
      gks_viva_transaction.mymessage='OK',
      gks_viva_transaction.tipAmount=".$tipAmount.",
      gks_viva_transaction.installments=".$installments."
      where payment_acquirer_with_id=1 
      AND transaction_status='done'
      AND sessionId='".$db_link->escape_string($data['sessionId'])."'
      and (gks_viva_transaction.StatusId like 'gks%' or gks_viva_transaction.StatusId='' or gks_viva_transaction.StatusId is null)";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}

      $xxx_cardType='';if (isset($response_array['cardType'])) $xxx_cardType=trim_gks($response_array['cardType']);
      $xxx_applicationLabel='';if (isset($response_array['applicationLabel'])) $xxx_applicationLabel=trim_gks($response_array['applicationLabel']);
      $xxx_primaryAccountNumberMasked='';if (isset($response_array['primaryAccountNumberMasked'])) $xxx_primaryAccountNumberMasked=trim_gks($response_array['primaryAccountNumberMasked']);
      $xxx_orderCode='';if (isset($response_array['orderCode'])) $xxx_orderCode=trim_gks($response_array['orderCode']);
      $xxx_bankId='';if (isset($response_array['bankId'])) $xxx_bankId=trim_gks($response_array['bankId']);

      //delete me
      //$xxx_bankId='NET_IRIS';
      
      $sql="UPDATE gks_viva_transaction 
      LEFT JOIN gks_eftpos_transaction ON gks_viva_transaction.eftpos_transaction_id = gks_eftpos_transaction.id_eftpos_transaction 
      SET 
      gks_viva_transaction.aadeTransactionId='".$db_link->escape_string($aadeTransactionId)."',
      gks_viva_transaction.CardTypeName='".$db_link->escape_string($xxx_cardType)."',
      gks_viva_transaction.CardNumber='".$db_link->escape_string($xxx_primaryAccountNumberMasked)."',
      gks_viva_transaction.OrderCode='".$db_link->escape_string($xxx_orderCode)."',
      gks_viva_transaction.BankId='".$db_link->escape_string($xxx_bankId)."'
      
      where payment_acquirer_with_id=1 
      AND transaction_status='done'
      AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}



      if ($xxx_bankId=='NET_IRIS') {
        gks_eftpos_set_payment_via_iris($id_eftpos_transaction);
      }
      
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
      
      if (in_array($transaction_type,['fullvoid','fullvoiderp','refund','refunderp'])) {
        
        
        $sql="select * from gks_eftpos_transaction
        where payment_acquirer_with_id=1 
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
                where payment_acquirer_with_id=1 
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
      
      
      //debug_mail(false,'doneeeeeee',$sql);
      
      return array('success' => true, 'message' => 'OK', 'status' => 'done');
    }
    if ($response_array['success']==false and $response_array['message']=='There is already a request in progress') {
      $sql="update gks_eftpos_transaction set 
      transaction_status='request',
      mymessage='".$db_link->escape_string($response_array['message'])."',
      user_id_edit=".$my_wp_user_id.",
      mydate_edit=now(),
      myip='".$db_link->escape_string($gkIP)."'
      where payment_acquirer_with_id=1 
      AND transaction_status<>'done'
      AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
      
      $sql="UPDATE gks_viva_transaction 
      LEFT JOIN gks_eftpos_transaction ON gks_viva_transaction.eftpos_transaction_id = gks_eftpos_transaction.id_eftpos_transaction 
      SET gks_viva_transaction.StatusId = 'gks_request',
      gks_viva_transaction.mymessage='".$db_link->escape_string($response_array['message'])."'
      where payment_acquirer_with_id=1 
      AND transaction_status<>'done'
      AND sessionId='".$db_link->escape_string($data['sessionId'])."'
      and (gks_viva_transaction.StatusId like 'gks%' or gks_viva_transaction.StatusId='' or gks_viva_transaction.StatusId is null)";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}

      
      return array('success' => true, 'message' => 'OK', 'status' => 'request');
    }
    if ($response_array['success']==false and $response_array['message']=='Canceled by user') {
      $sql="update gks_eftpos_transaction set 
      transaction_status='canceled',
      mymessage='".$db_link->escape_string($response_array['message'])."',
      user_id_edit=".$my_wp_user_id.",
      mydate_edit=now(),
      myip='".$db_link->escape_string($gkIP)."'
      where payment_acquirer_with_id=1 
      AND transaction_status<>'done'
      AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
      
      if (in_array($transaction_type,['sale','saleerp'])) {
        $sql="UPDATE ".$t_gks_acc_xxx_payment." 
        LEFT JOIN gks_eftpos_transaction ON ".$t_gks_acc_xxx_payment.".".$f_id_acc_xxx_payment." = gks_eftpos_transaction.".$f_acc_xxx_payment_id." 
        SET ".$t_gks_acc_xxx_payment.".transaction_pa_with_id = 0, 
        ".$t_gks_acc_xxx_payment.".transaction_id = 0
        WHERE gks_eftpos_transaction.payment_acquirer_with_id=1
        AND gks_eftpos_transaction.transaction_status<>'done'
        AND gks_eftpos_transaction.sessionId='".$db_link->escape_string($data['sessionId'])."'";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
      }
      
      $sql="UPDATE gks_viva_transaction 
      LEFT JOIN gks_eftpos_transaction ON gks_viva_transaction.eftpos_transaction_id = gks_eftpos_transaction.id_eftpos_transaction 
      SET gks_viva_transaction.StatusId = 'gks_canceled',
      gks_viva_transaction.mymessage='".$db_link->escape_string($response_array['message'])."'
      where payment_acquirer_with_id=1 
      AND transaction_status<>'done'
      AND sessionId='".$db_link->escape_string($data['sessionId'])."'
      and (gks_viva_transaction.StatusId like 'gks%' or gks_viva_transaction.StatusId='' or gks_viva_transaction.StatusId is null)";
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
            
      
      return array('success' => true, 'message' => 'OK', 'status' => 'canceled');
    }
    
  }
  
  $message='Some message..';if (isset($response_array['message']))  $message=$response_array['message'];
  $transactionId=''; if (isset($response_array['transactionId']))  $transactionId=$response_array['transactionId'];
  $sql="update gks_eftpos_transaction set 
  transaction_status='abort',
  mymessage='".$db_link->escape_string($response_array['message'])."',
  transactionId='".$db_link->escape_string($transactionId)."',
  response_array='".$db_link->escape_string($response)."',
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."'
  where payment_acquirer_with_id=1 
  AND transaction_status<>'done'
  AND sessionId='".$db_link->escape_string($data['sessionId'])."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}  
  
  if (in_array($transaction_type,['sale','saleerp'])) {
    $sql="UPDATE ".$t_gks_acc_xxx_payment." 
    LEFT JOIN gks_eftpos_transaction ON ".$t_gks_acc_xxx_payment.".".$f_id_acc_xxx_payment." = gks_eftpos_transaction.".$f_acc_xxx_payment_id." 
    SET ".$t_gks_acc_xxx_payment.".transaction_pa_with_id = 0, 
    ".$t_gks_acc_xxx_payment.".transaction_id = 0
    WHERE gks_eftpos_transaction.payment_acquirer_with_id=1
    AND gks_eftpos_transaction.transaction_status<>'done'
    AND gks_eftpos_transaction.sessionId='".$db_link->escape_string($data['sessionId'])."'";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  }

  $sql="UPDATE gks_viva_transaction 
  LEFT JOIN gks_eftpos_transaction ON gks_viva_transaction.eftpos_transaction_id = gks_eftpos_transaction.id_eftpos_transaction 
  SET gks_viva_transaction.StatusId = 'gks_abort',
  gks_viva_transaction.mymessage='".$db_link->escape_string($response_array['message'])."'
  where payment_acquirer_with_id=1 
  AND transaction_status<>'done'
  AND sessionId='".$db_link->escape_string($data['sessionId'])."'
  and (gks_viva_transaction.StatusId like 'gks%' or gks_viva_transaction.StatusId='' or gks_viva_transaction.StatusId is null)";
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
    
  //to $response einai keno otan ginei epitixos
  //echo '<pre>response ';print_r($response_array); die();
  
  
  
  
  return array('success' => true, 'message' => $message, 'status' => 'abort'); 
    
}


function gks_eftpos_sales_request_abort_viva($data) {

  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');
  
  $url=gks_eftpos_get_base_url_api_viva().'/ecr/v1/sessions/'.$data['sessionId'].'?cashRegisterId='.$data['cashRegisterId'];
  //echo '<pre>'.$url;die();
  
  $headers = array(
    'Content-Type:application/json',
    'Authorization: Bearer '.$data['access_token']
  );
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
  $response = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close($ch);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/viva_sales_abort_send_'.time().'.json',$url);
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/viva_sales_abort_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);

  /*empty string
  200 Successful Response
  401 You were not authorized to execute this operation
  403 Only cash register that created the transaction can abort it
  404 Session id was not found
  409 Abort process already started
  503 Unable to handle the request
  5xx Internal Server Error
  */
  
  if ($gks_curl_http_code==200) {
    return array('success' => true, 'message' => 'OK');   
  } else {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code .'<br>'.$response;
    debug_mail(false,'viva error',$response);
    return $return;
  }
}


function gks_eftpos_get_transaction_extra_html_viva($input) {
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
  
  $sql="select * from gks_viva_transaction 
  where eftpos_transaction_id=".$input['id_eftpos_transaction'];
  //if ($transactionId!='') $sql.=" TransactionId='".$db_link->escape_string($transactionId)."'";
  //else if ($sessionId!='') $sql.=" MerchantTrns like '%|".$db_link->escape_string($sessionId)."|gks'";  
  //else $sql.=" 1=2";
  $result = $db_link->query($sql);  
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc(); 
    $return['transaction']['donedate']=$row['add_date'];
    
    $return['transaction']['amount']=floatval($row['Amount']);
    $return['transaction']['tipamount']=floatval($row['TipAmount']);
    $return['transaction']['installments']=intval($row['installments']);

    
    $html=[];

    
    if (!empty($row['TotalCommission'])) $html[]=gks_lang('Προμήθεια').': '.myCurrencyFormat(floatval($row['TotalCommission']));
    if (!empty($row['TotalFee'])) $html[]=gks_lang('Τέλος').': '.myCurrencyFormat(floatval($row['TotalFee']));
    
    if (!empty($row['StatusId'])) {
      $html[]='StatusId: <span class="gks_viva_transaction_StatusId_'.$row['StatusId'].'">'.$row['StatusId'].'</span>';
    }
    if (!empty($row['TransactionTypeName'])) {
      $html[]=gks_lang('Τύπος').': '. $row['TransactionTypeName'];
    } else if (intval($row['EventTypeId'])<>0) {
      $html[]=gks_lang('Τύπος').': Event ID '.$row['EventTypeId'];  
    } else {
      //$html[]=gks_lang('Τύπος').': '. '--';
    }
      
    $html[]=gks_lang('Αναφορά Πληρωμής').': <span title="'.$row['MerchantTrns'].'"><i class="fas fa-exclamation-circle"></i></span>';
    $html[]=gks_lang('Εταιρεία').': <span title="'.$row['MerchantId'].'">'.(empty($row['CompanyName']) ? '<i class="fas fa-exclamation-circle"></i>' : $row['CompanyName']).'</span>';
    if (isset($row['Latitude']) and isset($row['Longitude'])) {
      $html[]=gks_lang('Στίγμα').': '.
      '<a href="https://www.google.com/maps/search/?api=1&query='.$row['Latitude'].','.$row['Longitude'].'" target="_blank"><i class="fas fa-map-marker-alt gks_marker_gps"></i></a>';
    }

    if (!empty($row['OrderCode'])) $html[]=gks_lang('Κωδικός Πληρωμής').': '.$row['OrderCode'];
    if (!empty($row['CardTypeName'])) $html[]=gks_lang('Τύπος Κάρτας').': '.$row['CardTypeName'];
    if (!empty($row['CardNumber'])) $html[]=gks_lang('Κάρτα').': '.$row['CardNumber'];
    if (!empty($row['BankId'])) $html[]='BankId: <b class="viva_tra_BankId viva_tra_BankId_'.$row['BankId'].' viva_tra_CardTypeName_'.str_replace(' ','_',$row['CardTypeName']).'">'.$row['BankId'].'</b>';
    if (!empty($row['CardIssuingBank'])) $html[]=gks_lang('Τράπεζα').': '.$row['CardIssuingBank'];
    if (!empty($row['FullName'])) $html[]=gks_lang('Όνομα').': '.$row['FullName'];
    if (!empty($row['Email'])) $html[]=gks_lang('email').': '.$row['Email'];
    if (!empty($row['Phone'])) $html[]=gks_lang('Τηλέφωνο').': '.$row['Phone'];
    if (!empty($row['Description'])) $html[]=gks_lang('Περιγραφή').': '.$row['Description'];
    
    

   $html[]='<a href="admin-viva-transaction-raw.php?mtid='.$row['id_viva_transaction'].'" target="_blank" title="Raw Data"><i class="fas fa-database gks_payment_rawdata"></i></a>'.
           ' <a href="'.GKS_VIVA_URL_WWW.'/web/receipt?tid='.$row['TransactionId'].'" target="_blank" title="'.gks_lang('Αποδεικτικό είσπραξης').'"><i class="fas fa-receipt gks_payment_receipt"></i></a>'.
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


function gks_eftpos_fullvoid_request_viva($data) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $return=array('success' => false, 'message' => 'generic error');
  
  $url=gks_eftpos_get_base_url_api_viva().'/ecr/v1/transactions:refund';
  //echo '<pre>'.$url;die();
  //echo '<pre>';print_r($data);die();
  
  $amount_viva=round(100*$data['amount'],0); // to 16 simenai 0.16
  $tip_amount_viva=round(100*$data['tipAmount'],0); // to 16 simenai 0.16
  
  
  $mypost=array();
  $mypost['sessionId']=$data['sessionId'];
  $mypost['terminalId']=$data['terminalId'];
  
  
  $mypost['cashRegisterId']=$data['row_prev_eftpos']['cashRegisterId'];
  $mypost['parentSessionId']=$data['row_prev_eftpos']['sessionId'];
  
  $mypost['amount']=$amount_viva;
  //$mypost['tipAmount']=$tip_amount_viva;
  $mypost['currencyCode']='978';
  if ($data['merchantReference']!='') $mypost['merchantReference']=$data['merchantReference'];
  if ($data['customerTrns']!='') $mypost['customerTrns']=$data['customerTrns'];
  //$mypost['preauth']=false;
  $mypost['maxInstalments']=0;
  $mypost['showTransactionResult']=true;
  $mypost['showReceipt']=true;
  
  if ($data['seira_need_signature']) {
    if (isset($data['aadeProviderId'])) 
      $mypost['aadeProviderId']=$data['aadeProviderId'];
    else 
      $mypost['aadeProviderId']='xxxxxxxxxxxxxxxxxxxxx';
    if (isset($data['aadeProviderSignatureData'])) 
      $mypost['aadeProviderSignatureData']=$data['aadeProviderSignatureData'];
    else
      $mypost['aadeProviderSignatureData']='xxxxxxxxxxxxxxxxxxxxx';
    if (isset($data['aadeProviderSignature'])) 
      $mypost['aadeProviderSignature']=$data['aadeProviderSignature'];
    else
      $mypost['aadeProviderSignature']='xxxxxxxxxxxxxxxxxxxxx';
  }
  
  
//  if ($data['aade_paroxos_id'] > 0) {
//    
//

//    
//    $mypost['aadeProviderId']='108';//ILYDA = ‘108’, VivaDemo - ‘999’
//    $mypost['aadeProviderSignatureData']='A6983D02CECC8E77C1CC4EA61E31C2994CF4913D;;20240629194534;10000;8065;1935;10000;16007426';
//    $mypost['aadeProviderSignature']='MEYCIQDPDU2u/C7zGeQtR8bb0PY8FOMZVkDUwXMIcBB6thLI/wIhAI39vJ22J8FpKIGtzO2XSsm0PnrrcWzmyE3qiw6GV7M9';
//    //$mypost['aadePreloaded']=true;
//    //$mypost['aadePreloadedDuration']=13;
//
//  }
  
  $headers = array(
    'Content-Type:application/json',
    'Authorization: Bearer '.$data['access_token']
  );
  $mypostdata=json_encode($mypost);
  //echo '<pre>postargs ';print_r($data);print_r($headers);print_r($mypost);print $mypostdata; die();
  
  $sql="update gks_eftpos_transaction set send_array='".$db_link->escape_string($mypostdata)."'
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='sql error';
    debug_mail(false,'sql error viva',$sql);return $return;}
  
  
  
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

  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/viva_refund_request_send_'.time().'.json',json_encode(json_decode($mypostdata,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/viva_refund_request_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$response);


  if ($gks_curl_http_code!=200) {
    $return['message']=gks_lang('Σφάλμα απόκρισης από το').' '.$url.' http_code: '.$gks_curl_http_code .'<br>'.$response;
    debug_mail(false,'viva error',$response);return $return;}

  //to $response einai keno otan ginei epitixos
  //echo '<pre>response ';print_r($response_array); die();
  
  $MerchantTrns=''; if ($data['merchantReference']!='') $MerchantTrns=$data['merchantReference'];
  $CustomerTrns='';if ($data['customerTrns']!='') $CustomerTrns=$data['customerTrns'];
  
   
  

  $sql="insert into gks_viva_transaction (
  user_id_add,user_id_edit,add_date,edit_date,myip,
  xeiristis_id,add_from_system,myfrom,
  eftpos_transaction_id,
  Amount,
  tipAmount,
  CurrencyCode,
  StatusId,
  terminalId,
  MerchantTrns,
  CustomerTrns
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",'gks ERP App','Viva Api',
  ".$data['id_eftpos_transaction'].",
  ".$data['amount'].",
  0,
  978,
  'gks_wait',
  '".$db_link->escape_string($data['terminalId'])."',
  '".$db_link->escape_string($MerchantTrns)."',
  '".$db_link->escape_string($CustomerTrns)."'
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  $id_viva_transaction = $db_link->insert_id;
    
  
  $sql="update gks_eftpos_transaction set 
  transaction_status='async',
  xxx_transaction_id=".$id_viva_transaction."
  where id_eftpos_transaction=".$data['id_eftpos_transaction'];
  $result = $db_link->query($sql);        
  if (!$result) {
    $return['message']='sql error';
    debug_mail(false,'sql error viva',$sql);return $return;}
  
  
  
  return array('success' => true, 'message' => 'OK', 'data' => array(
    //'access_token' => $access_token,
  )); 
    
}
