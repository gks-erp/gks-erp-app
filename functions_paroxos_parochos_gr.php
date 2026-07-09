<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_paroxos_parochos_gr_get_url($sub_url,$request_type,$input) {

  
  $p_send=$input;
  $p_response=[];
 
  if (isset($input['paroxos_url'])) {
    $url=$input['paroxos_url'];
    if (substr($url, strlen($url)-1,1)=='/') $url=substr($url, 0, strlen($url)-1);
    $url.=$sub_url;
  } else {
    
    if ($input['paroxos_mydata_live']) {
      $url=GKS_PAROCHOS_GR_MODE_LIVE_API.$sub_url;
      if ($sub_url=='/api/account/loginToSubscription' or $sub_url=='/api/token/refresh') {
        $url=GKS_PAROCHOS_GR_MODE_LIVE_ACCOUNT.$sub_url;
      }
    } else {
      $url=GKS_PAROCHOS_GR_MODE_TEST_API.$sub_url;
      if ($sub_url=='/api/account/loginToSubscription' or $sub_url=='/api/token/refresh') {
        $url=GKS_PAROCHOS_GR_MODE_TEST_ACCOUNT.$sub_url;
      }
    }
  }
  
  
  $paroxos_token='';
  if (isset($input['paroxos_token'])) $paroxos_token=$input['paroxos_token'];
  
  unset($input['acc_inv_id']);
  unset($input['acc_pay_id']);
  unset($input['id_company_paroxos']);
  unset($input['paroxos_mydata_live']);
  unset($input['paroxos_token']);
  unset($input['paroxos_url']);
  
  //echo 'url: '.$url."\n";die();
  
  
  
  if ($request_type=='GET') {
    if (is_array($input)) {
      $myq=http_build_query($input);
      $url.='?'.$myq;
      //echo '<pre>'.$url;die();
    } else if (is_string($input)) {
      $url.='?'.$input;
    }
  } else {
    $input=json_encode($input);
  }
  //echo 'input: '.$input."\n";  

  
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );  
  //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POST,true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
  curl_setopt($ch, CURLOPT_HEADER, true);

  $headers=array(
      'accept: application/json',
      'Content-type: application/json',
      //'Content-type: application/json; charset=UTF8',
  );
  if ($paroxos_token!='') {
    $headers[]='Authorization: Bearer '.$paroxos_token;
  }    
  curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
  
  
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close ($ch);

//  echo 'gks_curl_info:'."\n";
//  print_r($gks_curl_info);
//  echo "\n\n";
//  echo 'gks_curl_errno:'."\n";
//  var_dump($gks_curl_errno);
//  echo "\n\n";
//  echo 'result:'."\n";
//  var_dump($result);
//  echo "\n\n";
    
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  
  $p_response['gks_curl_http_code']=$gks_curl_http_code;
  $p_response['gks_curl_info']=$gks_curl_info;
  $p_response['gks_curl_errno']=$gks_curl_errno;
  $p_response['result']=$result;

  if (isset($p_send['acc_inv_id'])==false) $p_send['acc_inv_id']=0;
  if (isset($p_send['acc_pay_id'])==false) $p_send['acc_pay_id']=0;
  
  gks_paroxos_log(array($p_send['acc_inv_id'], $p_send['acc_pay_id'], $p_send['id_company_paroxos'], $p_send, $p_response));
  
  
//  echo 'gks_curl_http_code:'."\n";
//  var_dump($gks_curl_http_code);
//  echo "\n\n";

  if ($gks_curl_http_code==0) { //HTTP Host not found
    debug_mail(false,'parochos.gr error','Δεν βρέθηκε ο διακομιστής<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'parochos.gr Δεν βρέθηκε ο διακομιστής');
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    debug_mail(false,'parochos.gr error','Δεν βρέθηκε το σημείο<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'parochos.gr Δεν βρέθηκε το σημείο');
  
  } else if ($gks_curl_http_code==400) { 
    $error='parochos.gr Σφάλμα (400) Parameters are invalid.';
    debug_mail(false,'parochos.gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error);
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $error='parochos.gr Δεν επιτρέπεται η πρόσβαση.';
    if ($sub_url=='/api/send') $error.=' Unauthorized request. The jwt is either invalid or expired.';
    debug_mail(false,'parochos.gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error);
  
  } else if ($gks_curl_http_code==403) { 
    $error='parochos.gr Σφάλμα (403).';
    if ($sub_url=='/api/account/loginToSubscription') $error.=' Username, password or secret key is invalid';
    if ($sub_url=='/api/token/refresh') $error.=' Either token or refresh token is invalid';
    debug_mail(false,'parochos.gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result.'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error);
  
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    debug_mail(false,'parochos.gr error','Γενικό σφάλμα (2): HTTP Response Error'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'parochos.gr Γενικό σφάλμα(2): HTTP Response Error: '.$gks_curl_http_code);
  
  }
  
  
  
  $parts=explode("\r\n\r\n",$result,2);
  if (count($parts)!=2) {
    debug_mail(false,'parochos.gr result error',$result);
    return array('success' => false, 'message' => 'parochos.gr '.gks_lang('Σφάλμα δεδομένων').' (1).'.$result);}

  $response=trim($parts[1]);
  if ($response=='') {
    debug_mail(false,'parochos.gr response error',$response);
    return array('success' => false, 'message' => 'parochos.gr '.gks_lang('Σφάλμα δεδομένων').' (2).'.$result);}

  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'parochos.gr json_decode error',$response);
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (3).'.$result);}

  
  

  //print_r($response_array);
  
  return array('success' => true, 'message' => 'OK', 'response_array' => $response_array);
}

function gks_paroxos_loginToSubscription_parochos_gr($params) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  $ret = array('success' => false, 'message' => 'generic error');
  
  if (isset($params['pc_username'])==false or isset($params['pc_password'])==false or isset($params['pc_key'])==false or 
      $params['pc_username']=='' or $params['pc_password']=='' or $params['pc_key']=='' or
      isset($params['id_company_paroxos'])==false or isset($params['paroxos_mydata_live'])==false) {
    return array('success' => false, 'message' => 'Δεν έχουν ορθισθεί όλα τα δεδομένα'); }   
  
  $input=array(
    'id_company_paroxos' => $params['id_company_paroxos'],
    'paroxos_mydata_live' => $params['paroxos_mydata_live'],
    'Email' => $params['pc_username'],
    'password' => $params['pc_password'],
    'subscriptionKey' => $params['pc_key'],
  );
  //print '<pre>';print_r($input);die();
  $ret_post = gks_paroxos_parochos_gr_get_url('/api/account/loginToSubscription','POST',$input);
  
  if ($ret_post['success']==false or isset($ret_post['response_array'])==false) return $ret_post;
  
  
/*
Array
(
    [success] => 1
    [message] => OK
    [response_array] => Array
        (
            [jwt] => eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiQ0M4OUQyRjMxOTNENDMzOTg1QTEiLCJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6IjNmMjEyNmJmLTFhNzQtNDNjZC1jNWI2LTA4ZGMzMTUwY2RmYiIsImN1bHR1cmUiOiJlbC1HUiIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6IkFwaVVzZXIiLCJ0ZW5hbnRJZCI6ImFmNmZmY2YyLTU2YWUtNDA4MC1hY2YwLTJmODc5NTc0N2NlYiIsInN1YnNjcmlwdGlvbktleSI6IjFDNzNBNzNBMUI5RDQ5RTM4RTY0MzQzREFEREQwOTI4IiwiSXRlbUZhbWlseUlkZW50aWZpZXIiOiJBdGxhcyIsIm5iZiI6MTcwODM1OTg1MywiZXhwIjoxNzA4MzYxNjUzLCJpc3MiOiJodHRwczovL3Rlc3QtbG9naW4ucGFyb2Nob3MuZ3IvIiwiYXVkIjoiQW55b25lIn0.bOLZrf2YkO6bmkpaNfEA0k1m4Jt9m6cpmXX4etVwV5c
            [jwtExpiration] => 2024-02-19T16:54:13Z
            [jwtRefreshToken] => RqNkGlKMETzYt/pRp28lV6sYJZpkGJRMoaLSYkojaPU=
            [jwtRefreshTokenExpiration] => 2024-03-30T16:24:13.6643672Z
            [itemIdentifier] => Atlas.Generic
            [itemFamilyIdentifier] => Atlas
            [appIdentifier] => af6ffcf2-56ae-4080-acf0-2f8795747ceb
            [url1] => https://beta-srv.parochos.gr/
            [url2] => 
        )

)
*/  

  $response_array=$ret_post['response_array'];
  $pc_token_id='';if (isset($response_array['jwt'])) $pc_token_id=$response_array['jwt'];
  $pc_token_expiration=0;if (isset($response_array['jwtExpiration'])) $pc_token_expiration=strtotime($response_array['jwtExpiration']);
  $pc_refresh_token_id='';if (isset($response_array['jwtRefreshToken'])) $pc_refresh_token_id=$response_array['jwtRefreshToken'];
  $pc_refresh_token_expiration=0;if (isset($response_array['jwtRefreshTokenExpiration'])) $pc_refresh_token_expiration=strtotime($response_array['jwtRefreshTokenExpiration']);
  $pc_item_identifier='';if (isset($response_array['itemIdentifier'])) $pc_item_identifier=$response_array['itemIdentifier'];
  $pc_item_family_identifier='';if (isset($response_array['itemFamilyIdentifier'])) $pc_item_family_identifier=$response_array['itemFamilyIdentifier'];
  $pc_app_identifier='';if (isset($response_array['appIdentifier'])) $pc_app_identifier=$response_array['appIdentifier'];
  $pc_url1='';if (isset($response_array['url1'])) $pc_url1=$response_array['url1'];
  $pc_url2='';if (isset($response_array['url2'])) $pc_url2=$response_array['url2'];

  
  $sandbox=''; if ($params['paroxos_mydata_live']==false) $sandbox='sandbox_';
  //echo 'ggggggggggggggggg '.$sandbox.'<pre>'; print_r($params);var_dump($params['paroxos_mydata_live']); die();
  
  $sql_paroxos="update gks_company_paroxos set 
  ".$sandbox."pc_token_id='".$db_link->escape_string($pc_token_id)."',
  ".$sandbox."pc_token_expiration='".($pc_token_expiration==0 ? 'null' : date('Y-m-d H:i:s', $pc_token_expiration))."',
  ".$sandbox."pc_refresh_token_id='".$db_link->escape_string($pc_refresh_token_id)."',
  ".$sandbox."pc_refresh_token_expiration='".($pc_refresh_token_expiration==0 ? 'null' : date('Y-m-d H:i:s', $pc_refresh_token_expiration))."',
  ".$sandbox."pc_item_identifier='".$db_link->escape_string($pc_item_identifier)."',
  ".$sandbox."pc_item_family_identifier='".$db_link->escape_string($pc_item_family_identifier)."',
  ".$sandbox."pc_app_identifier='".$db_link->escape_string($pc_app_identifier)."',
  ".$sandbox."pc_url1='".$db_link->escape_string($pc_url1)."',
  ".$sandbox."pc_url2='".$db_link->escape_string($pc_url2)."',
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  where id_company_paroxos=".$params['id_company_paroxos'];
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {
    debug_mail(false,'error sql',$sql_paroxos);
    return array('success' => false, 'message' => 'sql error');}
  
  //return array('success' => true, 'message' => 'OK');
  //echo 'ggggggggggggggggg';die();
  
  $ret['response_array']=array(
    'pc_token_id'=>$pc_token_id,
	  'pc_token_expiration'=>$pc_token_expiration,
	  'pc_refresh_token_id'=>$pc_refresh_token_id,
	  'pc_refresh_token_expiration'=>$pc_refresh_token_expiration,
	  'pc_item_identifier'=>$pc_item_identifier,
	  'pc_item_family_identifier'=>$pc_item_family_identifier,
	  'pc_app_identifier'=>$pc_app_identifier,
	  'pc_url1'=>$pc_url1,
	  'pc_url2'=>$pc_url2,
	);

  $ret['message']='OK';
  $ret['success']=true;

  return $ret;	
  	
}


function gks_paroxos_invoice_xml_build_parochos_gr($id,$paroxos_params,$struct_data) {
	
	$ret = array('success' => false, 'message' => 'generic error');
	
	//echo  '<pre>';print_r($paroxos_params); die();
	//echo '<pre>';print_r($struct_data);die();
	
	$doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
  } else {
    $xxx='';
  }
	
	
	$adata=array();
	
	if ($paroxos_params['paroxos_mydata_live']==false) {
	  $myrandom=rand(1000,9999).rand(1000,9999);
		$adata['externalSystemId']=$struct_data['row']['id_acc_'.$xxx].''; //'_'.$myrandom;
	} else {
		$adata['externalSystemId']=$struct_data['row']['id_acc_'.$xxx].'';
	}
	
	
	$adata['source']=array();
	$adata['identifier']='eInvoicing';
	$adata['transmissionType']=0;
	
	$source=array();
		$invoice=array();
			$issuer=array();
			$counterpart=array();
			$representative=array();
			$deliveryAddress=array();
			$invoiceHeader=array();
			$invoiceDetails=array();
			$paymentMethods=array();
			$taxesTotals=array();
			$correlatedInvoices=array();
			$invoiceSummary=array();
			//$publishType=array();
			//$publishDetails=array();
			$messages=array();
			//$delayedProcessCode='';
			
	$xml=$struct_data['xml'];
	
	if ($paroxos_params['paroxos_mydata_live']==false) {
		$issuer['vatNumber']='000000000';
	} else {
	  if (GKS_PAROCHOS_GR_MODE_LIVE_API=='https://beta-srv.parochos.gr') {
	    $issuer['vatNumber']='000000000';
	  } else {
		  if (isset($xml['issuer']['vatNumber'])) $issuer['vatNumber']=$xml['issuer']['vatNumber'];
		}
	}
	if (isset($paroxos_params['paroxos_branch'])) $issuer['branch']=$paroxos_params['paroxos_branch'];
	if (isset($xml['issuer']['country'])) $issuer['country']=$xml['issuer']['country'];
	if (isset($xml['issuer']['address']['city'])) $issuer['city']=$xml['issuer']['address']['city'];
	if (isset($xml['issuer']['address']['street'])) $issuer['street']=$xml['issuer']['address']['street'];
	if (isset($xml['issuer']['address']['streetNumber'])) $issuer['streetNumber']=$xml['issuer']['address']['streetNumber'];
	if (isset($xml['issuer']['address']['postalCode'])) $issuer['postalCode']=$xml['issuer']['address']['postalCode'];


	if (isset($xml['counterpart']['vatNumber'])) $counterpart['vatNumber']=$xml['counterpart']['vatNumber'];
	if (isset($xml['counterpart']['branch'])) $counterpart['branch']=$xml['counterpart']['branch'];
	if (isset($xml['counterpart']['name'])) $counterpart['name']=$xml['counterpart']['name'];
	if (isset($xml['counterpart']['country'])) $counterpart['country']=$xml['counterpart']['country'];

	if (isset($xml['counterpart']['address']['city'])) $counterpart['city']=$xml['counterpart']['address']['city'];
	if (isset($xml['counterpart']['address']['street'])) $counterpart['street']=$xml['counterpart']['address']['street'];
	if (isset($xml['counterpart']['address']['streetNumber'])) $counterpart['streetNumber']=$xml['counterpart']['address']['streetNumber'];
	if (isset($xml['counterpart']['address']['postalCode'])) $counterpart['postalCode']=$xml['counterpart']['address']['postalCode'];
	if (isset($xml['counterpart']['email'])) $counterpart['email']=$xml['counterpart']['email'];

	
	if (isset($xml['counterpart']['deliveryAddress']['country'])) $deliveryAddress['country']=$xml['counterpart']['deliveryAddress']['country'];
	if (isset($xml['counterpart']['deliveryAddress']['city'])) $deliveryAddress['city']=$xml['counterpart']['deliveryAddress']['city'];
	if (isset($xml['counterpart']['deliveryAddress']['street'])) $deliveryAddress['street']=$xml['counterpart']['deliveryAddress']['street'];
	if (isset($xml['counterpart']['deliveryAddress']['streetNumber'])) $deliveryAddress['streetNumber']=$xml['counterpart']['deliveryAddress']['streetNumber'];
	if (isset($xml['counterpart']['deliveryAddress']['postalCode'])) $deliveryAddress['postalCode']=$xml['counterpart']['deliveryAddress']['postalCode'];
	if (isset($xml['counterpart']['deliveryAddress']['name'])) $deliveryAddress['partyName']=$xml['counterpart']['deliveryAddress']['name'];
	
	
	if (isset($xml['invoiceHeader']['series'])) $invoiceHeader['series']=$xml['invoiceHeader']['series'];
	
	if (isset($xml['invoiceHeader']['issueDate_iso_8601'])) $invoiceHeader['issueDate']=$xml['invoiceHeader']['issueDate_iso_8601'];
	

  if (isset($xml['invoiceHeader']['aa'])) $invoiceHeader['aa']=$xml['invoiceHeader']['aa'];



	//dueDate
	//paymentTerms
	
	if (isset($xml['invoiceHeader']['dispatchDate_iso_8601'])) $invoiceHeader['dispatchDate']=$xml['invoiceHeader']['dispatchDate_iso_8601'];
	if (isset($xml['invoiceHeader']['vehicleNumber'])) $invoiceHeader['vehicleNumber']=$xml['invoiceHeader']['vehicleNumber'];
	if (isset($xml['invoiceHeader']['note_doc'])) $invoiceHeader['invoiceNote']=$xml['invoiceHeader']['note_doc'];
	if (isset($xml['invoiceHeader']['invoiceType'])) $invoiceHeader['invoiceType']=$xml['invoiceHeader']['invoiceType']; //Invoice Code
	if (isset($xml['invoiceHeader']['invoiceCode'])) $invoiceHeader['invoiceCode']=$xml['invoiceHeader']['invoiceCode']; //journal code
	
	//invoiceTypeUbl
	

	if (isset($xml['invoiceHeader']['currency'])) $invoiceHeader['currency']=$xml['invoiceHeader']['currency'];
	
	//selfPricing
	
	if (isset($xml['invoiceHeader']['movePurpose'])) $invoiceHeader['movePurpose']=$xml['invoiceHeader']['movePurpose'];
	
	/*		
	taxType TaxTotalsType
	Λίστα τιμών:
	1 = Παρακρατούμενος Φόρος
	2 = Τέλη
	3 = Λοιποί Φόροι
	4 = Ψηφιακό Τέλος συναλλαγής
	5 = Κρατήσεις		
	*/			 
	$taxesTotals_i=array();
	$taxesTotals_i[1]=array('cc'=>0,'cats'=>[],'taxType'=>1,'taxCategory' =>0,'taxCategoryUbl'=>'','underlyingValue'=>0,'taxAmount'=>0,'taxPercent'=>0);
	$taxesTotals_i[2]=array('cc'=>0,'cats'=>[],'taxType'=>2,'taxCategory' =>0,'taxCategoryUbl'=>'','underlyingValue'=>0,'taxAmount'=>0,'taxPercent'=>0);
	$taxesTotals_i[3]=array('cc'=>0,'cats'=>[],'taxType'=>3,'taxCategory' =>0,'taxCategoryUbl'=>'','underlyingValue'=>0,'taxAmount'=>0,'taxPercent'=>0);
	$taxesTotals_i[4]=array('cc'=>0,'cats'=>[],'taxType'=>4,'taxCategory' =>0,'taxCategoryUbl'=>'','underlyingValue'=>0,'taxAmount'=>0,'taxPercent'=>0);
	$taxesTotals_i[5]=array('cc'=>0,'cats'=>[],'taxType'=>5,'taxCategory' =>0,'taxCategoryUbl'=>'','underlyingValue'=>0,'taxAmount'=>0,'taxPercent'=>0);
		
	
	
	foreach ($struct_data['prow_array'] as $prow) {
		$item=array();
		
		if (isset($prow['xml_lineNumber'])) $item['lineNumber']=$prow['xml_lineNumber'];
		//recType des parakato gia to idio
		if (isset($prow['xml_quantity'])) $item['quantity']=$prow['xml_quantity'];
		if (isset($prow['xml_product_descr'])) $item['entityName']=$prow['xml_product_descr'];
		//invoiceDetailType
		if (isset($prow['xml_product_descr'])) $item['entityName']=$prow['xml_product_descr'];
		
		if (isset($prow['product_price_final_all_net'])) $item['netValue']=$prow['product_price_final_all_net'];
		if (isset($prow['product_price_final_all_total'])) $item['totalValue']=$prow['product_price_final_all_total'];
		if (isset($prow['xml_vatCategory'])) $item['vatCategory']=$prow['xml_vatCategory'];
		//vatCategoryUbl
		if (isset($prow['xml_vatExemptionCategory'])) $item['vatExemption']=$prow['xml_vatExemptionCategory'];
		//vatExemptionUbl
		if (isset($prow['xml_vatAmount'])) $item['vatAmount']=$prow['xml_vatAmount'];
		if (isset($prow['product_fpa_pososto'])) $item['vatPercent']=$prow['product_fpa_pososto']*100;
		if (isset($prow['xml_measurementUnit'])) $item['measurementUnit']=$prow['xml_measurementUnit'];
		//measurementUnitUbl
		if (isset($prow['xml_product_comments'])) $item['lineComments']=$prow['xml_product_comments'];
		
		$classification_count=0;
		if (isset($prow['xml_incomeClassification'])) {
			foreach ($prow['xml_incomeClassification'] as $cl_item) {
				$classification_count++;
				if (isset($cl_item['category'])) $item['classificationCategory']=$cl_item['category'];
				if (isset($cl_item['type'])) $item['classificationType']=$cl_item['type'];
			} 	
		}
		if (isset($prow['xml_expensesClassification'])) {
			foreach ($prow['xml_expensesClassification'] as $cl_item) {
				$classification_count++;
				if (isset($cl_item['category'])) $item['classificationCategory']=$cl_item['category'];
				if (isset($cl_item['type'])) $item['classificationType']=$cl_item['type'];
			}
		}
		
		if ($classification_count>1) {
			$ret['message']='Ο συγκεκριμένος πάροχος δεν υποστηρίζει πάνω από έναν χαρακτηρισμό ανά είδος.<br>Γραμμή '.$prow['xml_lineNumber'];
			debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
		}
		
		//classificationVatType
		//cpvCode
		

		
		
		$tax_count=0;
		$taxInfo=array();
		if (isset($prow['xml_withheldPercentCategory']) and $prow['xml_withheldAmount']!=0) {
			$tax_count++;
			$rt=1; $cl_item['recType']=$rt;
			$taxInfo['taxCategory']=$prow['xml_withheldPercentCategory'];
			//taxCategoryUbl
			$taxInfo['underlyingValue']=$prow['xml_withheldAmount'];
			//$taxInfo['taxPercent']=100;

			$taxesTotals_i[$rt]['taxAmount']+=$prow['xml_withheldAmount'];
			$taxesTotals_i[$rt]['cc']++;
			if (in_array($taxInfo['taxCategory'],$taxesTotals_i[$rt]['cats'])==false) $taxesTotals_i[$rt]['cats'][]=$taxInfo['taxCategory'];

		} 
		
		if (isset($prow['xml_feesPercentCategory']) and $prow['xml_feesAmount']!=0) {
			$tax_count++;
			$rt=2; $cl_item['recType']=$rt;
			$taxInfo['taxCategory']=$prow['xml_feesPercentCategory'];
			//taxCategoryUbl
			$taxInfo['underlyingValue']=$prow['xml_feesAmount'];
			//$taxInfo['taxPercent']=100;

			$taxesTotals_i[$rt]['taxAmount']+=$prow['xml_feesAmount'];
			$taxesTotals_i[$rt]['cc']++;
			if (in_array($taxInfo['taxCategory'],$taxesTotals_i[$rt]['cats'])==false) $taxesTotals_i[$rt]['cats'][]=$taxInfo['taxCategory'];
		}	
		if (isset($prow['xml_otherTaxesPercentCategory']) and $prow['xml_otherTaxesAmount']!=0) {
			
			$tax_count++;
			$rt=3; $cl_item['recType']=$rt;
			$taxInfo['taxCategory']=$prow['xml_otherTaxesPercentCategory'];
			//taxCategoryUbl
			$taxInfo['underlyingValue']=$prow['xml_otherTaxesAmount'];
			//$taxInfo['taxPercent']=100;

			$taxesTotals_i[$rt]['taxAmount']+=$prow['xml_otherTaxesAmount'];
			$taxesTotals_i[$rt]['cc']++;
			if (in_array($taxInfo['taxCategory'],$taxesTotals_i[$rt]['cats'])==false) $taxesTotals_i[$rt]['cats'][]=$taxInfo['taxCategory'];
			//print '<pre>';print_r($taxesTotals_i[$rt]);die();
		} 		
			
		if (isset($prow['xml_stampDutyPercentCategory']) and $prow['xml_stampDutyAmount']!=0) {
			$tax_count++;
			$rt=4; $cl_item['recType']=$rt;
			$taxInfo['taxCategory']=$prow['xml_stampDutyPercentCategory'];
			//taxCategoryUbl
			$taxInfo['underlyingValue']=$prow['xml_stampDutyAmount'];
			//$taxInfo['taxPercent']=100;

			$taxesTotals_i[$rt]['taxAmount']+=$prow['xml_stampDutyAmount'];
			$taxesTotals_i[$rt]['cc']++;
			if (in_array($taxInfo['taxCategory'],$taxesTotals_i[$rt]['cats'])==false) $taxesTotals_i[$rt]['cats'][]=$taxInfo['taxCategory'];
		} 
 

		if (isset($prow['xml_deductionsAmount']) and $prow['xml_deductionsAmount']!=0) {
			$tax_count++;
			$rt=5; $cl_item['recType']=$rt;
			$taxInfo['taxCategory']=1000; //fixme
			//taxCategoryUbl
			$taxInfo['underlyingValue']=$prow['xml_deductionsAmount'];
			//$taxInfo['taxPercent']=100;
			
			$taxesTotals_i[$rt]['taxAmount']+=$prow['xml_deductionsAmount'];
			$taxesTotals_i[$rt]['cc']++;

			//fixme
			$ret['message']='Ο συγκεκριμένος πάροχος δεν υποστηρίζει Κρατήσεις ανά είδος.<br>Γραμμή '.$prow['xml_lineNumber'];
			debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
		}
		
		if ($tax_count>1) {
			$ret['message']='Ο συγκεκριμένος πάροχος δεν υποστηρίζει πάνω από έναν φόρο (Φόροι Παρακρατούμενοι, Λοιποί Φόροι, Ψηφιακό Τέλος συναλλαγής, Τέλη, Κρατήσεις) ανά είδος.<br>Γραμμή '.$prow['xml_lineNumber'];
			debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
		}
		
	

		
		$invoiceDetails[]=$item;
	} 
	
	
	//echo '<pre>';print_r($taxesTotals_i);die();
	$taxesTotals=array();
	foreach ($taxesTotals_i as $tt_val) {
		if ($tt_val['cc']>=1 and $tt_val['taxAmount']!=0) {
			if (count($tt_val['cats'])>=2) {
				$ret['message']='Ο συγκεκριμένος πάροχος δεν υποστηρίζει πάνω από μία διαφορετική κατηγορία φόρου ανά τύπο (Φόροι Παρακρατούμενοι, Λοιποί Φόροι, Ψηφιακό Τέλος συναλλαγής, Τέλη, Κρατήσεις) σε επίπεδο παραστατικού.';
				debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
			}
			$tt_val['taxCategory']=$tt_val['cats'][0];
			unset($tt_val['cc']);
			unset($tt_val['cats']);
			unset($tt_val['taxCategoryUbl']);
			unset($tt_val['underlyingValue']);
			unset($tt_val['taxPercent']);
			$taxesTotals[]=$tt_val;
			
		} 
	} 
	//echo '<pre>';print_r($taxesTotals);die();
	//echo '<pre>';echo json_encode($taxesTotals);die();


		
	if (isset($xml['paymentMethods']) and count($xml['paymentMethods'])>0) {
		foreach ($xml['paymentMethods'] as $pm) {
 
			$paymentMethods[]=array(
				'type'=> $pm['type'],
				'amount'=> $pm['amount'],
			);
		}
		//echo '<pre>';print_r($paymentMethods);die();
	}
	
	if (isset($struct_data['xml']['invoiceHeader']['correlatedInvoices'])) {
		foreach ($struct_data['xml']['invoiceHeader']['correlatedInvoices'] as $colinv) {
			$correlatedInvoices[]=array(
				'extSystemId'=> $colinv['acc_'.$xxx.'_id'],
				'mark' => $colinv['aade_invoicemark'],
			);
		} 
	}
		
  

	if (isset($xml['invoiceSummary']['totalNetValue'])) $invoiceSummary['totalNetValue']=$xml['invoiceSummary']['totalNetValue'];
	if (isset($xml['invoiceSummary']['totalVatAmount'])) $invoiceSummary['totalVatAmount']=$xml['invoiceSummary']['totalVatAmount'];
	if (isset($xml['invoiceSummary']['totalGrossValue'])) $invoiceSummary['totalValue']=$xml['invoiceSummary']['totalGrossValue'];


	//publishType
	//publishDetails
	
	$messages[]=array(
		'type' => 0, //Type of message to be sent (Email = 0, SMS = 1, Viber = 2)
		'recipients' => 'kostas@gks.gr', //Email or mobile number of the recipients separated by “;”
		//'cc' => 'kostas@gks.gr', //Cc emails separated by “;”
		//'templateIdentifier' => '000001', //Identifier of message template, when not included the default message template is applied.
	);
	$messages[]=array(
		'type' => 1, //Type of message to be sent (Email = 0, SMS = 1, Viber = 2)
		'recipients' => '6971881406', //Email or mobile number of the recipients separated by “;”
		//'templateIdentifier' => '000002', //Identifier of message template, when not included the default message template is applied.
	);
	$messages[]=array(
		'type' => 2, //Type of message to be sent (Email = 0, SMS = 1, Viber = 2)
		'recipients' => '6971881406', //Email or mobile number of the recipients separated by “;”
		//'templateIdentifier' => '000003', //Identifier of message template, when not included the default message template is applied.
	);
	
	//delayedProcessCode

	$invoice['issuer']=$issuer;
	$invoice['counterpart']=$counterpart; //fixme
	$invoice['representative']=$representative;
	$invoice['deliveryAddress']=$deliveryAddress;
	$invoice['invoiceHeader']=$invoiceHeader;
	$invoice['invoiceDetails']=$invoiceDetails;
	$invoice['paymentMethods']=$paymentMethods;
	$invoice['taxesTotals']=$taxesTotals;
	$invoice['correlatedInvoices']=$correlatedInvoices;
	$invoice['invoiceSummary']=$invoiceSummary;
	//$invoice['publishType']=$publishType;
	//$invoice['publishDetails']=$publishDetails;
	$invoice['messages']=$messages;
	//$invoice['delayedProcessCode']=$delayedProcessCode;

	
	
	
	
	
	$source['invoice']=$invoice;

	
	$adata['source']=json_encode($source,JSON_PRETTY_PRINT);
	//echo '<pre>';print_r($adata);die();

	
  $ret['file_data']=$adata;
  $ret['message']='OK';
  $ret['success']=true;

  return $ret;	
}

function gks_paroxos_check_token_parochos_gr($paroxos_params) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
	
	$ret = array('success' => false, 'message' => 'generic error');

	//echo '<pre>gks_paroxos_check_token_parochos_gr ';print_r($paroxos_params); die();
	
  $sql_paroxos="select * from gks_company_paroxos where paroxos_send=1 and id_company_paroxos=".$paroxos_params['id_company_paroxos'];
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {debug_mail(false,'error sql',$sql_paroxos);$ret['message']='sql error'; return $ret;}
  if ($result_paroxos->num_rows==0) {$ret['message']='Αυτή η εταιρεία/υποκατάστημα δεν έχει ορισθεί για αποστολή σε πάροχο';debug_mail(false,$ret['message'],$sql); return $ret;}

  $sandbox=''; if ($paroxos_params['paroxos_mydata_live']==false) $sandbox='sandbox_';
  
  $row_paroxos = $result_paroxos->fetch_assoc();
	$pc_token_id=trim_gks($row_paroxos[$sandbox.'pc_token_id']);
	$pc_token_expiration=trim_gks($row_paroxos[$sandbox.'pc_token_expiration']);
	$pc_refresh_token_id=trim_gks($row_paroxos[$sandbox.'pc_refresh_token_id']);
	$pc_refresh_token_expiration=trim_gks($row_paroxos[$sandbox.'pc_refresh_token_expiration']);
	$pc_url1=trim_gks($row_paroxos[$sandbox.'pc_url1']);

	if ($pc_token_id=='' or $pc_url1=='') {
		$pc_token_expiration='';
		$pc_refresh_token_expiration='';
	}

	if (trim_gks($pc_token_expiration)=='') $pc_token_expiration='2020-01-01';
	if (trim_gks($pc_refresh_token_expiration)=='') $pc_refresh_token_expiration='2020-01-01';
	
	//echo '<pre>dddddddddddd '.$pc_token_id; die();
	if ($pc_token_id!='' and $pc_url1!='' and (time() + 5*60) < strtotime($pc_token_expiration)) { //tora +  lepta gia asfaleia
		//einai OK
	 //echo '<pre>dddddddddddd ok '.$pc_token_id; die();
	} else {
		//den einai OK
		//echo '<pre>gggggggggggg 111'; die();
		
		if ($pc_token_id!='' and $pc_refresh_token_id!='' and $pc_url1!='' and (time() + 5*60) < strtotime($pc_refresh_token_expiration)) { // to refresh_token den exei lijei
			//echo '<pre>gggggggggggg 222'; die();

		  $input=array(
		  	'id_company_paroxos' => $paroxos_params['id_company_paroxos'],
		    'paroxos_mydata_live' => $paroxos_params['paroxos_mydata_live'],
		    //'paroxos_url' => $pc_url1, na min stalei to url1 giati den tha paei sto account server
		    'token' => $pc_token_id,
		    'refreshToken' => $pc_refresh_token_id,
		  );
		  //echo '<pre>gggggggggggg 222 aaa '; print_r($input);die();
		  $ret_post = gks_paroxos_parochos_gr_get_url('/api/token/refresh','POST',$input);
		  //echo '<pre>gggggggggggg 222 aaa '; print_r($ret_post);die();
		  if ($ret_post['success']==false or isset($ret_post['response_array']['jwt'])==false) {
			  $sql_paroxos="update gks_company_paroxos set 
			  ".$sandbox."pc_token_id='',
			  ".$sandbox."pc_token_expiration=null,
			  ".$sandbox."pc_refresh_token_id='',
			  ".$sandbox."pc_refresh_token_expiration=null,
			  ".$sandbox."pc_item_identifier='',
			  ".$sandbox."pc_item_family_identifier='',
			  ".$sandbox."pc_app_identifier='',
			  ".$sandbox."pc_url1='',
			  ".$sandbox."pc_url2='',
			  mydate_edit=now(),
			  user_id_edit=".$my_wp_user_id.",
			  myip='".$db_link->escape_string($gkIP)."'
			  where id_company_paroxos=".$paroxos_params['id_company_paroxos'];
			  $result_paroxos = $db_link->query($sql_paroxos); 
			  if (!$result_paroxos) {
			    debug_mail(false,'error sql',$sql_paroxos);
			    return array('success' => false, 'message' => 'sql error');}
    
				$ret['message']=$ret_post['message'];
				
				return $ret;
			}
			/*Array
			(
			    [success] => 1
			    [message] => OK
			    [response_array] => Array
			        (
			            [jwt] => eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiQ0M4OUQyRjMxOTNENDMzOTg1QTEiLCJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6IjNmMjEyNmJmLTFhNzQtNDNjZC1jNWI2LTA4ZGMzMTUwY2RmYiIsImN1bHR1cmUiOiJlbC1HUiIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6IkFwaVVzZXIiLCJ0ZW5hbnRJZCI6ImFmNmZmY2YyLTU2YWUtNDA4MC1hY2YwLTJmODc5NTc0N2NlYiIsInN1YnNjcmlwdGlvbktleSI6IjFDNzNBNzNBMUI5RDQ5RTM4RTY0MzQzREFEREQwOTI4IiwiSXRlbUZhbWlseUlkZW50aWZpZXIiOiJBdGxhcyIsIm5iZiI6MTcwODgyMzczMywiZXhwIjoxNzA4ODI1NTMzLCJpc3MiOiJodHRwczovL3Rlc3QtbG9naW4ucGFyb2Nob3MuZ3IvIiwiYXVkIjoiQW55b25lIn0.perySZsk29zNbT43GdeWlpIdrpSSzrAvjIGbIyUGyRo
			            [jwtExpiration] => 2024-02-25T01:45:33Z
			            [jwtRefreshToken] => XSW+P8pzH8EoPRslFYmXw8iBQhiONNlRqdKT6rl6F7M=
			            [jwtRefreshTokenExpiration] => 2024-04-05T01:15:33.8562105Z
			        )
			
			)*/
			
			$response_array=$ret_post['response_array'];
		  $pc_token_id='';if (isset($response_array['jwt'])) $pc_token_id=$response_array['jwt'];
		  $pc_token_expiration=0;if (isset($response_array['jwtExpiration'])) $pc_token_expiration=strtotime($response_array['jwtExpiration']);
		  $pc_refresh_token_id='';if (isset($response_array['jwtRefreshToken'])) $pc_refresh_token_id=$response_array['jwtRefreshToken'];
		  $pc_refresh_token_expiration=0;if (isset($response_array['jwtRefreshTokenExpiration'])) $pc_refresh_token_expiration=strtotime($response_array['jwtRefreshTokenExpiration']);
		  $sql_paroxos="update gks_company_paroxos set 
		  ".$sandbox."pc_token_id='".$db_link->escape_string($pc_token_id)."',
		  ".$sandbox."pc_token_expiration='".($pc_token_expiration==0 ? 'null' : date('Y-m-d H:i:s', $pc_token_expiration))."',
		  ".$sandbox."pc_refresh_token_id='".$db_link->escape_string($pc_refresh_token_id)."',
		  ".$sandbox."pc_refresh_token_expiration='".($pc_refresh_token_expiration==0 ? 'null' : date('Y-m-d H:i:s', $pc_refresh_token_expiration))."',
		  mydate_edit=now(),
		  user_id_edit=".$my_wp_user_id.",
		  myip='".$db_link->escape_string($gkIP)."'
		  where id_company_paroxos=".$paroxos_params['id_company_paroxos'];
		  $result_paroxos = $db_link->query($sql_paroxos); 
		  if (!$result_paroxos) {
		    debug_mail(false,'error sql',$sql_paroxos);
		    return array('success' => false, 'message' => 'sql error');}


		  	
		  //echo '<pre>gggggggggggg 222 aaa '; print_r($ret_post);die();			
			
		} else {
			//echo '<pre>gggggggggggg 333'; die();
			
			$params=array(
			  'id_company_paroxos' => $paroxos_params['id_company_paroxos'],
			  'aade_paroxos_id' => $paroxos_params['aade_paroxos_id'],
			  'paroxos_mydata_live' => $paroxos_params['paroxos_mydata_live'],
			  'pc_username' => $paroxos_params['pc_username'],
			  'pc_password' => $paroxos_params['pc_password'],
			  'pc_key' => $paroxos_params['pc_key'],
			);
			//echo '<pre>gggggggggggg 333 aaa '; print_r($params);die();	
			 
			$ret_post=gks_paroxos_loginToSubscription_parochos_gr($params);
			//echo '<pre>gggggggggggg 333 bbb '; print_r($ret_post);die();	
			
			if ($ret_post['success']==false) {
				$ret['message']=$ret_post['message'];
				return $ret;				
			}
			$pc_token_id=$ret_post['response_array']['pc_token_id'];
			$pc_url1=$ret_post['response_array']['pc_url1'];
		
			//echo '<pre>gggggggggggg 333 ccc ';print $pc_url1."\n\n".$pc_token_id."\n\n"; print_r($ret_post);die();	
			
		}
		
		
	}

	
	//echo '<pre>gks_paroxos_check_token_parochos_gr ';print_r($row_paroxos); die();
	
	
  $ret['pc_url1']=$pc_url1;
  $ret['pc_token_id']=$pc_token_id;
  $ret['message']='OK';
  $ret['success']=true;

	//echo '<pre>gks_paroxos_check_token_parochos_gr ';print_r($ret); die();


  return $ret;		
}


function gks_paroxos_invoice_xml_send_parochos_gr($id,$paroxos_params,$struct_data,$file_data) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;	
  global $gks_cache_version;
  
	$ret = array('success' => false, 'message' => 'generic error');


  //echo '<pre>';echo $id; die();
  //echo '<pre>';print_r($paroxos_params); die();
  //echo '<pre>';print_r($struct_data); die();
  //echo '<pre>ddddddddddd';print_r($file_data); die();

  $doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
  } else {
    $xxx='';
  }
  
	$ret_token=gks_paroxos_check_token_parochos_gr($paroxos_params);
	
	if ($ret_token['success']==false) {$ret['message']=$ret_token['message'];return $ret;}

	/*Array
	(
	    [success] => 1
	    [message] => OK
	    [pc_url1] => https://beta-srv.parochos.gr/
	    [pc_token_id] => eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiQ0M4OUQyRjMxOTNENDMzOTg1QTEiLCJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6IjNmMjEyNmJmLTFhNzQtNDNjZC1jNWI2LTA4ZGMzMTUwY2RmYiIsImN1bHR1cmUiOiJlbC1HUiIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6IkFwaVVzZXIiLCJ0ZW5hbnRJZCI6ImFmNmZmY2YyLTU2YWUtNDA4MC1hY2YwLTJmODc5NTc0N2NlYiIsInN1YnNjcmlwdGlvbktleSI6IjFDNzNBNzNBMUI5RDQ5RTM4RTY0MzQzREFEREQwOTI4IiwiSXRlbUZhbWlseUlkZW50aWZpZXIiOiJBdGxhcyIsIm5iZiI6MTcwODgyNDYyNCwiZXhwIjoxNzA4ODI2NDI0LCJpc3MiOiJodHRwczovL3Rlc3QtbG9naW4ucGFyb2Nob3MuZ3IvIiwiYXVkIjoiQW55b25lIn0.3jh9yB2_WDVsFF3R_0VnEIGKeSHOqgQMzzWChZMDNlY
	)*/
	$input=$file_data;
	$input['acc_'.$xxx.'_id']=$id;
	$input['id_company_paroxos']=$paroxos_params['id_company_paroxos'];
  $input['paroxos_token']=$ret_token['pc_token_id'];
  $input['paroxos_url']=$ret_token['pc_url1'];
	
	//echo '<pre>dddddddddddggggggggg';print_r($file_data); die();
    
    //'externalSystemId'=>$id,
    //'source' => $file_data,
    //'identifier' => 'eInvoicing', 
    //https://beta-srv.parochos.gr/settings/tenanttemplates/edit/18dc6654-2fe4-4f7b-1a16-08dc3150dfc9
    //The identifier of the template used to map the incoming invoice data to the model required by AADE Provider. 
    //This value is specified through UI configuration.
    //'transmissionType' => 0,
    //Resolves the required type of transmission.
    //0 transmits invoice to Tax Authorities Platform.
    //1 transmits invoice to Tax Authorities Platform and generates
    //invoice pdf file. (Default Value = 0)
    
    //'attachments' => array(),


  $ret_send = gks_paroxos_parochos_gr_get_url('/api/send','POST',$input);
  
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
	/*Array
	(
	    [success] => 1
	    [message] => OK
	    [response_array] => Array
	        (
	            [processId] => 9af00a4d-7a60-46dd-26e4-08dc31364af3
	            [externalSystemId] => 10001
	            [errorSeverity] => 
	            [timeStamp] => 2024-02-19T16:42:05.1857205Z
	            [signing] => Array
	                (
	                    [uid] => 
	                    [mark] => 
	                    [authenticationCode] => 
	                    [qrCode] => https://beta-srv.parochos.gr/FileDocument/Get/d52c0533-f5bc-46fc-26df-08dc31364af3
	                    [pdfUploaded] => 
	                    [pdfFileUrl] => 
	                    [publishStatus] => 
	                    [paymentTokens] => Array
	                        (
	                        )
	
	                )
	
	            [attachments] => 
	            [status] => 0
	            [errorCategory] => 
	            [errorCode] => 
	            [errorMessage] => 
	        )
	
	)*/
	
	$response_array=$ret_send['response_array'];
	if (isset($response_array['status'])==false) {$ret['message']='Σφάλμα αποστολής (34239322421)';return $ret;}



	//status The progress status of the procedure. InProgress =0, Completed = 1, Failed = 2.
	$paroxos_status=intval($response_array['status']);
	if ($paroxos_status==2) {$ret['message']='Σφάλμα αποστολής (34239322422)<br>'.
		(isset($response_array['errorCategory']) ? 'errorCategory: '.$response_array['errorCategory'].'<br>' : '').
		(isset($response_array['errorCode']) ? 'errorCode: '.$response_array['errorCode'].'<br>' : '').
		(isset($response_array['errorMessage']) ? 'errorMessage: '.$response_array['errorMessage'] : '');
	  return $ret;}
	
	$paroxos_processId='';if (isset($response_array['processId'])) $paroxos_processId=trim_gks($response_array['processId']);
	if ($paroxos_processId=='') {$ret['message']='Σφάλμα αποστολής (34239322423)';return $ret;}
	

  $save_dir = GKS_FileServerShare.'acc/'.$xxx.'/'.$id.'/aade_mydata/';
  if (file_exists($save_dir) == false) {
    if (@mkdir($save_dir , 0777, true) == false ) {
      $ret['message']='Δεν μπορεί να δημιουργηθεί ο φάκελος: '.substr($save_dir, strlen(GKS_FileServerShare)); debug_mail(false,$ret['message']); return $ret;
    }
  }
  $set_filename=showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
  $set_filename_s='invoice_'.$set_filename.'-1-send.json';
  $set_filename_r='invoice_'.$set_filename.'-2-response.json';
  $file_data_fix=$file_data;
  $file_data_fix['source']=json_decode($file_data_fix['source'], true);
  //file_put_contents($save_dir.$set_filename_s, json_encode($file_data_fix,JSON_PRETTY_PRINT));  
  
  
  require_once('vendor_inc/Nicer.php');
  
  $raw_file='<!DOCTYPE html><html dir="ltr" lang="en-US"><head>
  		<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/vendor_inc/nice_r.css?v='.$gks_cache_version.'"/>
  		<script type="text/javascript" src="'.GKS_SITE_URL.'my/vendor_inc/nice_r.js?v='.$gks_cache_version.'"></script>
  	</head><body>';
          $obj_nicer = new Nicer($file_data_fix, true, true);
          $raw_file.=$obj_nicer->render(false);
          $raw_file.='<div id="raw_print_r_b" onclick="raw_toggle()">RAW json</div>';
          $raw_file.='<div style="display:none;" id="raw_print_r"><pre>';
          $raw_file.=json_encode($file_data_fix,JSON_PRETTY_PRINT);
          $raw_file.='</pre></div>';
  $raw_file.='</body>
  </html>'; 
  file_put_contents($save_dir.$set_filename_s.'.html', $raw_file);  
  
  if ($paroxos_params['paroxos_mydata_live']) {	
  	$sql_xxx="update gks_acc_".$xxx." set 
  	aade_xml_send='".$db_link->escape_string($set_filename_s.'.html')."',
  	aade_paroxos_id=".$paroxos_params['aade_paroxos_id'].",
  	paroxos_processId='".$db_link->escape_string($paroxos_processId)."',
  	paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  	paroxos_status=".$paroxos_status.",
  	paroxos_user_send=".$my_wp_user_id.",
  	paroxos_date_send=now()
  	
  	where id_acc_".$xxx."=".$id;
    $result_xxx = $db_link->query($sql_xxx); 
    if (!$result_xxx) {
      debug_mail(false,'error sql',$sql_xxx);
      return array('success' => false, 'message' => 'sql error');}
  }

  //die(); //hackme
  if (1==1) {
    $input_loop=array();	
    $input_loop['acc_'.$xxx.'_id']=$id;
  	$input_loop['id_company_paroxos']=$paroxos_params['id_company_paroxos'];
    $input_loop['paroxos_token']=$ret_token['pc_token_id'];
    $input_loop['paroxos_url']=$ret_token['pc_url1'];
    $input_loop['processId']=$paroxos_processId;
    $input_loop['externalSystemId']=$id.'';
  
    $has_error=false;
  	for($myloop = 1;$myloop <= 10; $myloop++) {
  		usleep(300); 
  
  	  $ret_send = gks_paroxos_parochos_gr_get_url('/api/get','POST',$input_loop);
  	  //debug_mail(false,'api get',print_r($input_loop,true).'<br>'.print_r($ret_send,true));
  		if ($ret_send['success']==false) {$ret['message']='loop '.$myloop.' '.$ret_send['message'];return $ret;}
  		$response_array=$ret_send['response_array'];
  		if (isset($response_array['status'])==false) {$ret['message']='Σφάλμα αποστολής (34239322424)';return $ret;}
  
  
  
  		$paroxos_status=intval($response_array['status']);
  		if ($paroxos_status==2) {
    	  //$ret['message']=
  		  $errorMessage='Σφάλμα αποστολής (34239322425)<br>'.
  			(isset($response_array['errorCategory']) ? 'errorCategory: '.$response_array['errorCategory'].'<br>' : '').
  			(isset($response_array['errorCode']) ? 'errorCode: '.$response_array['errorCode'].'<br>' : '').
  			(isset($response_array['errorMessage']) ? 'errorMessage: '.$response_array['errorMessage'] : '');
  		  $has_error=true;
  		  //break;  
  		}
  		
  		$paroxos_processId='';if (isset($response_array['processId'])) $paroxos_processId=trim_gks($response_array['processId']);
  		//if ($paroxos_processId=='') {$ret['message']='Σφάλμα αποστολής (34239322426)';return $ret;}
  		
  //		$sql_inv="update gks_acc_inv set  
  //		paroxos_processId='".$db_link->escape_string($paroxos_processId)."',
  //		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  //		paroxos_status=".$paroxos_status."
  //		where id_acc_inv=".$id;
  //	  $result_inv = $db_link->query($sql_inv); 
  //	  if (!$result_inv) {
  //	    debug_mail(false,'error sql',$sql_inv);
  //	    return array('success' => false, 'message' => 'sql error');}
  	  
  	  if ($paroxos_status==1 or $paroxos_status==2 or $has_error) break; 
  	}
	}
	
/*Array
(
    [success] => 1
    [message] => OK
    [response_array] => Array
        (
            [processId] => 857c17db-01c8-459d-5a97-08dc34006775
            [externalSystemId] => 11486
            [errorSeverity] => 
            [timeStamp] => 2024-02-25T03:34:46.1166594
            [signing] => Array
                (
                    [uid] => F9FCDF90E93BB063FFB7C30F484EC7ADB4160E88
                    [mark] => 400001924650560
                    [authenticationCode] => 5BE9A8E9F0893A47B8F69492B81906C77C97602C
                    [qrCode] => https://beta-srv.parochos.gr/FileDocument/Get/857c17db-01c8-459d-5a97-08dc34006775
                    [pdfUploaded] => 
                    [pdfFileUrl] => 
                    [publishStatus] => 0
                    [paymentTokens] => Array
                        (
                        )

                )

            [attachments] => 
            [status] => 1
            [errorCategory] => 
            [errorCode] => 
            [errorMessage] => 
        )

)
Array
(
    [success] => 1
    [message] => OK
    [response_array] => Array
        (
            [processId] => d5bf2b9a-2a2a-4893-5aa7-08dc34006775
            [externalSystemId] => 11488
            [errorSeverity] => 
            [timeStamp] => 2024-02-25T10:15:13.4482098
            [signing] => Array
                (
                    [uid] => 8331A651E98360360D831D148F587519707C32AE
                    [mark] => 400001924650724
                    [authenticationCode] => C72E24F8DF0BD57A3DE811C248731E23989E4AB7
                    [qrCode] => https://beta-srv.parochos.gr/FileDocument/Get/d5bf2b9a-2a2a-4893-5aa7-08dc34006775
                    [pdfUploaded] => 
                    [pdfFileUrl] => 
                    [publishStatus] => 0
                    [paymentTokens] => Array
                        (
                        )

                )

            [attachments] => 
            [status] => 1
            [errorCategory] => 
            [errorCode] => 
            [errorMessage] => 
        )

)

*/

	//status The progress status of the procedure. InProgress =0, Completed = 1, Failed = 2.
	//file_put_contents($save_dir.$set_filename_r, json_encode($response_array,JSON_PRETTY_PRINT));  
  $raw_file='<!DOCTYPE html><html dir="ltr" lang="en-US"><head>
  		<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/vendor_inc/nice_r.css?v='.$gks_cache_version.'"/>
  		<script type="text/javascript" src="'.GKS_SITE_URL.'my/vendor_inc/nice_r.js?v='.$gks_cache_version.'"></script>
  	</head><body>';
          $obj_nicer = new Nicer($response_array, true, true);
          $raw_file.=$obj_nicer->render(false);
          $raw_file.='<div id="raw_print_r_b" onclick="raw_toggle()">RAW json</div>';
          $raw_file.='<div style="display:none;" id="raw_print_r"><pre>';
          $raw_file.=json_encode($response_array,JSON_PRETTY_PRINT);
          $raw_file.='</pre></div>';
  $raw_file.='</body>
  </html>'; 
  file_put_contents($save_dir.$set_filename_r.'.html', $raw_file);  

  //echo '<pre>'.$paroxos_status;die();
  //if ($has_error) return $ret; 
	
	if ($paroxos_status==1) { //pige ok
		$errorMessage=''; if (isset($response_array['errorMessage'])) $errorMessage=trim_gks($response_array['errorMessage']); 
		
		
    if ($paroxos_params['paroxos_mydata_live']) {
  		$sql_xxx="update gks_acc_".$xxx." set  
  	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
  		aade_invoiceuid='".$db_link->escape_string($response_array['signing']['uid'])."',
  		aade_invoicemark='".$db_link->escape_string($response_array['signing']['mark'])."',
  		aade_qrurl='".$db_link->escape_string($response_array['signing']['qrCode'])."',
  		paroxos_authenticationCode='".$db_link->escape_string($response_array['signing']['authenticationCode'])."',
  		aade_statuscode='Success',
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		aade_send_date=now(),
  		aade_user_id=".$my_wp_user_id.",
  
  		paroxos_processId='".$db_link->escape_string($paroxos_processId)."',
  		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  		paroxos_status=".$paroxos_status.",
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_acc_".$xxx."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
  	  
  	  gks_aade_update_mark_from_id(['mark'=>$response_array['signing']['mark'],'acc_'.$xxx.'_id'=>$id]);
  	  
  	  //echo '<pre>'; print   GKS_SITE_URL.'my/cron_paroxos.php?get_files=1&id='.$id;die();
  	  //https://test.easyfilesselection.com/my/cron_paroxos.php?get_files=1&id=11514
  	  gks_curl_post_async(GKS_SITE_URL.'my/cron_paroxos.php?doc_table='.$doc_table.'&get_files=1&id='.$id,[]);
  	  
    } else {
  		$sql_xxx="update gks_acc_".$xxx." set  
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()
  		where id_acc_".$xxx."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}

    }
    
  	
  	$ret['save_but_message']='Επιτυχής αποστολή δεδομένων σε πάροχο.';
  	if ($errorMessage!='') $ret['save_but_message'].='<br>'.$errorMessage;
    $ret['message']='ok';
    $ret['success']=true;
  
    return $ret;		

	} else if ($paroxos_status==2) { //sfalma
		$errorMessage=''; if (isset($response_array['errorMessage'])) $errorMessage=trim_gks($response_array['errorMessage']); 
    $errorMessage='Σφάλμα κατά την αποστολή στον πάροχο.<br>'. $errorMessage;
    
    if ($paroxos_params['paroxos_mydata_live']) {
  		$sql_xxx="update gks_acc_".$xxx." set  
  	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
  		aade_statuscode='ValidationError',
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		aade_user_id=".$my_wp_user_id.",
  
  		paroxos_processId='".$db_link->escape_string($paroxos_processId)."',
  		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  		paroxos_status=".$paroxos_status.",
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_acc_".$xxx."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
  	    
      
      
    } else {
  		$sql_xxx="update gks_acc_".$xxx." set  
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_acc_".$xxx."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
    }
    

    $ret['message']=$errorMessage;
    $ret['success']=false;
  
    return $ret;
	  
	} else if ($paroxos_status==0) { //den exei epejergastei akoma
		$errorMessage=''; if (isset($response_array['errorMessage'])) $errorMessage=trim_gks($response_array['errorMessage']); 
	  
		$sql_xxx="update gks_acc_".$xxx." set  
		aade_statuscode='Processing',
		aade_errors='".$db_link->escape_string($errorMessage)."',
		aade_send_date=now(),
 		aade_user_id=".$my_wp_user_id."
		where id_acc_".$xxx."=".$id;
	  $result_xxx = $db_link->query($sql_xxx); 
	  if (!$result_xxx) {
	    debug_mail(false,'error sql',$sql_xxx);
	    return array('success' => false, 'message' => 'sql error');}
	    	  
    $ret['message']='ok';
    $ret['save_but_message']='Έχει γίνει η αποστολή στον πάροχο αλλά το παραστατικό είναι σε κατάσταση επεξεργασίας.<br>'.
    'Σε 1-2 λεπτά ανανεώστε την σελίδα για να δείτε εάν το παραστικό έχει καταχωρησθεί επιτυχώς ή όχι στον πάροχο';
    $ret['success']=true;	
    //print '<pre>'; print_r($ret);die();
      
    return $ret;
	}
	
  //echo '<pre>ret_send ';print_r($ret_send);die();
  
  
  //echo 'ret:'."\n";
  //print_r($ret);
  //die(); 
  

	//echo '<pre>send fffffffffffff ';print_r($ret_token); die();

	
  $ret['save_but_message']='Σφάλμα κατά την αποστολή (2)';
  $ret['message']='Σφάλμα κατά την αποστολή (1)';
  $ret['success']=false;

  return $ret;	
}


function gks_paroxos_invoice_xml_get_in_progress_item_parochos_gr($doc_table,$xxx_item) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $gks_cache_version;
  
  //echo '<pre>ssssssssss ';print_r($inv_item);die();
  
  $ret = array('success' => false, 'message' => 'generic error');
  /*Array
  (
      [id_acc_inv] => 11508
      [company_id] => 1
      [company_sub_id] => 0
      [aade_paroxos_id] => 20
      [paroxos_processId] => 64b9bd71-48e1-419f-5b0b-08dc34006775
      [paroxos_status] => 0
  )*/
  
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
  } else {
    $xxx='';
  }
  
  $sql_paroxos="select * from gks_company_paroxos where ";
  if ($xxx_item['company_id']>0) $sql_paroxos.="company_id=".$xxx_item['company_id'];
  else if ($xxx_item['company_sub_id']>0) $sql_paroxos.="company_sub_id=".$xxx_item['company_sub_id'];
  else $sql_paroxos.="1=2";
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql_paroxos.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  if ($result_paroxos->num_rows==0) {$ret['message']='Αυτή η εταιρεία/υποκατάστημα δεν έχει ορισθεί για αποστολή σε πάροχο';debug_mail(false,$ret['message'],$sql); return $ret;}
  $row_paroxos = $result_paroxos->fetch_assoc();
  
  $force_options=[];$force_options['paroxos_mydata_live']=true;
  $ret_params=gks_paroxos_load_params($xxx_item['company_id'],$xxx_item['company_sub_id'],$force_options);
  if ($ret_params['success']==false) {$ret['message']=$ret_params['message']; debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $paroxos_params=$ret_params['paroxos_params'];

	$ret_token=gks_paroxos_check_token_parochos_gr($paroxos_params);
	if ($ret_token['success']==false) {$ret['message']=$ret_token['message'];return $ret;}

  
  //echo '<pre>gggggggggggs';print_r($paroxos_params);die();

  $input=array();	
  $input['acc_'.$xxx.'_id']=$xxx_item['id_acc_'.$xxx];
	$input['id_company_paroxos']=$row_paroxos['id_company_paroxos'];
  $input['paroxos_token']=$ret_token['pc_token_id'];
  $input['paroxos_url']=$ret_token['pc_url1'];
  $input['processId']=$xxx_item['paroxos_processId'];
  $input['externalSystemId']=$xxx_item['id_acc_'.$xxx].'';
  

  
  //echo '<pre>sssssssssss ';print_r($input);die(); 
  
  $ret_send = gks_paroxos_parochos_gr_get_url('/api/get','POST',$input);
  //debug_mail(false,'api get',print_r($input,true).'<br>'.print_r($ret_send,true));
	if ($ret_send['success']==false) {$ret['message']='start send '.$ret_send['message'];return $ret;}
	$response_array=$ret_send['response_array'];
	if (isset($response_array['status'])==false) {$ret['message']='Σφάλμα αποστολής (44239322424)';return $ret;}


  //print '<pre>dddakskdfkfbb ';print_r($response_array);die();
  
  
  $paroxos_status=intval($response_array['status']);
  if ($paroxos_status==1 or $paroxos_status==2) {

    $save_dir = GKS_FileServerShare.'acc/'.$xxx.'/'.$xxx_item['id_acc_'.$xxx].'/aade_mydata/';
    if (file_exists($save_dir) == false) {
      if (@mkdir($save_dir , 0777, true) == false ) {
        $ret['message']='Δεν μπορεί να δημιουργηθεί ο φάκελος: '.substr($save_dir, strlen(GKS_FileServerShare)); debug_mail(false,$ret['message']); return $ret;
      }
    }
        
    $set_filename=showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
    $set_filename_r='invoice_'.$set_filename.'-2-response.json';
    require_once('vendor_inc/Nicer.php');
    
    
    $raw_file='<!DOCTYPE html><html dir="ltr" lang="en-US"><head>
    		<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/vendor_inc/nice_r.css?v='.$gks_cache_version.'"/>
    		<script type="text/javascript" src="'.GKS_SITE_URL.'my/vendor_inc/nice_r.js?v='.$gks_cache_version.'"></script>
    	</head><body>';
            $obj_nicer = new Nicer($response_array, true, true);
            $raw_file.=$obj_nicer->render(false);
            $raw_file.='<div id="raw_print_r_b" onclick="raw_toggle()">RAW json</div>';
            $raw_file.='<div style="display:none;" id="raw_print_r"><pre>';
            $raw_file.=json_encode($response_array,JSON_PRETTY_PRINT);
            $raw_file.='</pre></div>';
    $raw_file.='</body>
    </html>'; 
    file_put_contents($save_dir.$set_filename_r.'.html', $raw_file); 
    //echo $set_filename_r;die();
		
		if ($paroxos_status==2) {
  	  //$ret['message']=
		  $errorMessage='Σφάλμα αποστολής (34239322425)<br>'.
			(isset($response_array['errorCategory']) ? 'errorCategory: '.$response_array['errorCategory'].'<br>' : '').
			(isset($response_array['errorCode']) ? 'errorCode: '.$response_array['errorCode'].'<br>' : '').
			(isset($response_array['errorMessage']) ? 'errorMessage: '.$response_array['errorMessage'] : '');
		  $has_error=true;
		  //break;  
		}
		    

  
  	if ($paroxos_status==1) { //pige ok
  		$errorMessage=''; if (isset($response_array['errorMessage'])) $errorMessage=trim_gks($response_array['errorMessage']); 
  		
  		
      if ($paroxos_params['paroxos_mydata_live']) {
    		$sql_xxx="update gks_acc_".$xxx." set  
    	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
    		aade_invoiceuid='".$db_link->escape_string($response_array['signing']['uid'])."',
    		aade_invoicemark='".$db_link->escape_string($response_array['signing']['mark'])."',
    		aade_qrurl='".$db_link->escape_string($response_array['signing']['qrCode'])."',
    		paroxos_authenticationCode='".$db_link->escape_string($response_array['signing']['authenticationCode'])."',
    		aade_statuscode='Success',
    		aade_errors='".$db_link->escape_string($errorMessage)."',
    		aade_send_date=now(),
    		aade_user_id=".$my_wp_user_id.",
    
    		
    		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
    		paroxos_status=".$paroxos_status.",
    	  paroxos_user_send=".$my_wp_user_id.",
    	  paroxos_date_send=now()  		
    		where id_acc_".$xxx."=".$xxx_item['id_acc_'.$xxx];
    	  $result_xxx = $db_link->query($sql_xxx); 
    	  if (!$result_xxx) {
    	    debug_mail(false,'error sql',$sql_xxx);
    	    return array('success' => false, 'message' => 'sql error');}
        
        gks_aade_update_mark_from_id(['mark'=>$response_array['signing']['mark'],'acc_'.$xxx.'_id'=>$xxx_item['id_acc_'.$xxx]]);
    	  
    	  gks_curl_post_async(GKS_SITE_URL.'my/cron_paroxos.php?doc_table='.$doc_table.'&get_files=1&id='.$xxx_item['id_acc_'.$xxx],[]);
    	  
      } else {
    		$sql_xxx="update gks_acc_".$xxx." set  
    		aade_errors='".$db_link->escape_string($errorMessage)."',
    		paroxos_user_send=".$my_wp_user_id.",
    	  paroxos_date_send=now()
    		where id_acc_".$xxx."=".$xxx_item['id_acc_'.$xxx];
    	  $result_xxx = $db_link->query($sql_xxx); 
    	  if (!$result_xxx) {
    	    debug_mail(false,'error sql',$sql_xxx);
    	    return array('success' => false, 'message' => 'sql error');}
  
      }
      

    	
      $ret['message']='ok';
      $ret['save_but_message']='Επιτυχής αποστολή δεδομένων σε πάροχο.';
  	  if ($errorMessage!='') $ret['save_but_message'].='<br>'.$errorMessage;
      $ret['success']=true;
    
      return $ret;		
  
  	} else if ($paroxos_status==2) { //sfalma
  		if ($errorMessage=='' and isset($response_array['errorMessage'])) $errorMessage=trim_gks($response_array['errorMessage']); 
  	  
      
      if ($paroxos_params['paroxos_mydata_live']) {
    		$sql_xxx="update gks_acc_".$xxx." set  
    	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
    		aade_statuscode='ValidationError',
    		aade_errors='".$db_link->escape_string($errorMessage)."',
    		aade_user_id=".$my_wp_user_id.",
    
    		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
    		paroxos_status=".$paroxos_status.",
    	  paroxos_user_send=".$my_wp_user_id.",
    	  paroxos_date_send=now()  		
    		where id_acc_".$xxx."=".$xxx_item['id_acc_'.$xxx];
    	  $result_xxx = $db_link->query($sql_xxx); 
    	  if (!$result_xxx) {
    	    debug_mail(false,'error sql',$sql_xxx);
    	    return array('success' => false, 'message' => 'sql error');}
      } else {
    		$sql_xxx="update gks_acc_".$xxx." set  
    		aade_errors='".$db_link->escape_string($errorMessage)."',
    	  paroxos_user_send=".$my_wp_user_id.",
    	  paroxos_date_send=now()  		
    		where id_acc_".$xxx."=".$xxx_item['id_acc_'.$xxx];
    	  $result_xxx = $db_link->query($sql_xxx); 
    	  if (!$result_xxx) {
    	    debug_mail(false,'error sql',$sql_xxx);
    	    return array('success' => false, 'message' => 'sql error');}
      }
      

    	
      $ret['message']=$errorMessage;
      $ret['success']=false;
    
      return $ret;
  	  
  	}
  }
  
    
  echo '<pre>ggggggggg ';print_r($response_array);die();
  
  
  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;  
  
}


function gks_paroxos_invoice_xml_get_files_item_parochos_gr($doc_table,$xxx_item) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $gks_cache_version;
  
  //echo '<pre>ssssssssss ';print_r($xxx_item);die();
  
  $ret = array('success' => false, 'message' => 'generic error');
  /*Array
  (
      [id_acc_xxx] => 11483
      [company_id] => 1
      [company_sub_id] => 0
      [aade_paroxos_id] => 20
  )*/

  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
  } else {
    echo '<pre>error on doc_table-page'; die();
  }
    
  $sql_paroxos="select * from gks_company_paroxos where ";
  if ($xxx_item['company_id']>0) $sql_paroxos.="company_id=".$xxx_item['company_id'];
  else if ($xxx_item['company_sub_id']>0) $sql_paroxos.="company_sub_id=".$xxx_item['company_sub_id'];
  else $sql_paroxos.="1=2";
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql_paroxos.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  if ($result_paroxos->num_rows==0) {$ret['message']='Αυτή η εταιρεία/υποκατάστημα δεν έχει ορισθεί για αποστολή σε πάροχο';debug_mail(false,$ret['message'],$sql); return $ret;}
  $row_paroxos = $result_paroxos->fetch_assoc();
  
  $force_options=[];$force_options['paroxos_mydata_live']=true;
  $ret_params=gks_paroxos_load_params($xxx_item['company_id'],$xxx_item['company_sub_id'],$force_options);
  if ($ret_params['success']==false) {$ret['message']=$ret_params['message']; debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $paroxos_params=$ret_params['paroxos_params'];

	$ret_token=gks_paroxos_check_token_parochos_gr($paroxos_params);
	if ($ret_token['success']==false) {$ret['message']=$ret_token['message'];return $ret;}

  
  //echo '<pre>gggggggggggs';print_r($paroxos_params);die();

  $input=array();	
  $input['acc_'.$xxx.'_id']=$xxx_item['id_acc_'.$xxx];
	$input['id_company_paroxos']=$row_paroxos['id_company_paroxos'];
  $input['paroxos_token']=$ret_token['pc_token_id'];
  $input['paroxos_url']=$ret_token['pc_url1'];
  $input['processId']=$xxx_item['paroxos_processId'];
  $input['externalSystemId']=$xxx_item['id_acc_'.$xxx].'';
  //$input['fileIdentifiers']=array('pdf','mydataxml','einvoicexml');
  /*List of file identifiers. If empty, all files are returned.
  Accepted values:
  • pdf for invoice pdf,
  • mydataxml for Tax Authorities xml file,
  • einvoicexml for PEPPOL xml file*/


  
  //echo '<pre>sssssssssss ';print_r($input);die(); 
  
  $ret_send = gks_paroxos_parochos_gr_get_url('/api/getFiles','POST',$input);
  //debug_mail(false,'api get',print_r($input,true).'<br>'.print_r($ret_send,true));
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
	$response_array=$ret_send['response_array'];
	if (isset($response_array['status'])==false) {$ret['message']='Σφάλμα αποστολής (54239322421)';return $ret;}

/*Array
(
    [files] => Array
        (
            [0] => Array
                (
                    [downloadUrl] => https://edelmibeta.blob.core.windows.net/af6ffcf2-56ae-4080-acf0-2f8795747ceb/239762e4-e4bd-4686-5a94-08dc34006775/bedcfe20-232b-46f1-dfeb-08dc340067d2
                    [fileName] => myDataXML.xml
                    [fileIdentifier] => myDataXML
                    [instanceNumber] => 0
                )

            [1] => Array
                (
                    [downloadUrl] => https://edelmibeta.blob.core.windows.net/af6ffcf2-56ae-4080-acf0-2f8795747ceb/239762e4-e4bd-4686-5a94-08dc34006775/a820ead3-b172-4a93-dfec-08dc340067d2
                    [fileName] => eInvoiceXML.xml
                    [fileIdentifier] => eInvoiceXML
                    [instanceNumber] => 0
                )

        )

    [status] => 1
    [errorCategory] => 
    [errorCode] => 
    [errorMessage] => 
)*/


  if (!(isset($response_array['files']) and is_array($response_array['files']))) {
    $ret['message']='Σφάλμα αποστολής (54239322422)'; return $ret;
  }
  //echo '<pre>dddakskdfkfbb ';print_r($response_array);die();
  if (count($response_array['files'])>0) {
    $save_dir = GKS_FileServerShare.'acc/'.$xxx.'/'.$xxx_item['id_acc_'.$xxx].'/paroxos/';
    if (file_exists($save_dir) == false) {
      if (@mkdir($save_dir , 0777, true) == false ) {
        $ret['message']='Δεν μπορεί να δημιουργηθεί ο φάκελος: '.substr($save_dir, strlen(GKS_FileServerShare)); debug_mail(false,$ret['message']); return $ret;
      }
    }

    foreach ($response_array['files'] as $myfile) {
      $save_file=$save_dir.$myfile['fileName'];
      
      $file_dxml=@file_get_contents($myfile['downloadUrl']);
      if (is_string($file_dxml) and strlen($file_dxml)>100) {
        $file_dxml='<?xml version="1.0" encoding="utf-8"?>'."\n".$file_dxml;
        @file_put_contents($save_file,$file_dxml);
      }
      
//      $file = fopen($save_file, 'w');
//      // cURL
//      $ch = curl_init();
//      curl_setopt($ch, CURLOPT_URL,$myfile['downloadUrl']);
//      // set cURL options
//      curl_setopt($ch, CURLOPT_FAILONERROR, true);
//      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//      // set file handler option
//      curl_setopt($ch, CURLOPT_FILE, $file);
//      // execute cURL
//      curl_exec($ch);
//      // close cURL
//      curl_close($ch);
//      // close file
//      fclose($file);

    }
    $sql_xxx="update gks_acc_".$xxx." set
    paroxos_get_files=now()
		where id_acc_".$xxx."=".$xxx_item['id_acc_'.$xxx];
	  $result_xxx = $db_link->query($sql_xxx); 
	  if (!$result_xxx) {
	    debug_mail(false,'error sql',$sql_xxx);
	    return array('success' => false, 'message' => 'sql error');}      
    
  }
    
  //echo '<pre>ggggggggg ';print_r($response_array);die();
  
  
  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;  
  
}

function gks_paroxos_invoice_xml_send_pdf_item_parochos_gr($doc_table,$xxx_item) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $gks_cache_version;
  
  //echo '<pre>ssssssssss ';print_r($xxx_item);die();
  
  $ret = array('success' => false, 'message' => 'generic error');
  /*Array
  (
      [id_acc_xxx] => 11518
      [company_id] => 1
      [company_sub_id] => 0
      [aade_paroxos_id] => 20
      [paroxos_processId] => a06e3c69-201f-43af-5b29-08dc34006775
      [print_date] => 2024-02-25 22:33:25
      [print_file_name] => INV_11518_ekdosi_2024-02-26_00.33.20.814.pdf
      [print_file_url] => /my/admin-get-file.php?fs=fileservers&file=acc%2Finv%2F11518%2Fprint%2FINV_11518_ekdosi_2024-02-26_00.33.20.814.pdf
      [print_user_id] => 1
      [print_xxx_state] => 090ekdosi
  )*/
  
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
  } else {
    echo '<pre>error on doc_table-page'; die();
  }
  
  $sql_paroxos="select * from gks_company_paroxos where ";
  if ($xxx_item['company_id']>0) $sql_paroxos.="company_id=".$xxx_item['company_id'];
  else if ($xxx_item['company_sub_id']>0) $sql_paroxos.="company_sub_id=".$xxx_item['company_sub_id'];
  else $sql_paroxos.="1=2";
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql_paroxos.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  if ($result_paroxos->num_rows==0) {$ret['message']='Αυτή η εταιρεία/υποκατάστημα δεν έχει ορισθεί για αποστολή σε πάροχο';debug_mail(false,$ret['message'],$sql); return $ret;}
  $row_paroxos = $result_paroxos->fetch_assoc();
  
  $force_options=[];$force_options['paroxos_mydata_live']=true;
  $ret_params=gks_paroxos_load_params($xxx_item['company_id'],$xxx_item['company_sub_id'],$force_options);
  if ($ret_params['success']==false) {$ret['message']=$ret_params['message']; debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $paroxos_params=$ret_params['paroxos_params'];

	$ret_token=gks_paroxos_check_token_parochos_gr($paroxos_params);
	if ($ret_token['success']==false) {$ret['message']=$ret_token['message'];return $ret;}

  $read_file = GKS_FileServerShare.'acc/'.$xxx.'/'.$xxx_item['id_acc_'.$xxx].'/print/'.$xxx_item['print_file_name'];
  if (file_exists($read_file)==false) {$ret['message']=gks_lang('Δεν βρέθηκε το αρχείο').' '.$xxx_item['print_file_name'] ; debug_mail(false,$ret['message'],$sql); return $ret;}
  
  //echo '<pre>gggggggggggs';print_r($paroxos_params);die();
  $read_file_size=filesize($read_file);
  $input=array();	
  $input['acc_'.$xxx.'_id']=$xxx_item['id_acc_'.$xxx];
	$input['id_company_paroxos']=$row_paroxos['id_company_paroxos'];
  $input['paroxos_token']=$ret_token['pc_token_id'];
  $input['paroxos_url']=$ret_token['pc_url1'];
  $input['processId']=$xxx_item['paroxos_processId'];
  $input['externalSystemId']=$xxx_item['id_acc_'.$xxx].'';
  $input['fileName']=$xxx_item['print_file_name'];
  $input['fileSize']=$read_file_size;
  


  
    
  //echo '<pre>sssssssssss ';print_r($input);//die(); 
  
  $ret_send = gks_paroxos_parochos_gr_get_url('/api/uploadFileRequest','POST',$input);
  //debug_mail(false,'api get',print_r($input,true).'<br>'.print_r($ret_send,true));
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
	$response_array=$ret_send['response_array'];
	if (isset($response_array['status'])==false or intval($response_array['status'])!=1) {$ret['message']='Σφάλμα αποστολής (54239322421)';return $ret;}
	if (isset($response_array['uploadUrl'])==false or trim_gks($response_array['uploadUrl'])=='') {$ret['message']='Σφάλμα αποστολής (54239322422)';return $ret;}
  //echo '<pre>';  print_r($response_array);
  
  
  
  
/*Array
(
    [uploadUrl] => https://edelmibeta.blob.core.windows.net/af6ffcf2-56ae-4080-acf0-2f8795747ceb/a06e3c69-201f-43af-5b29-08dc34006775/4654d4a4-0067-4102-e17c-08dc340067d2?sv=2023-11-03&se=2024-02-25T23%3A20%3A50Z&sr=b&sp=w&sig=QZBjhY6e0bo03arJndsLs9owBKlaC5wqZ7vUXR1RwrQ%3D
    [status] => 1
    [errorCategory] => 
    [errorCode] => 
    [errorMessage] => 
)


Use the Azure storage url to send a new PUT httpRequest (See here).
Request Syntax: PUT <uploadurl> HTTP/1.1

Request Headers: Content-Type: application/pdf
                 x-ms-blob-type: BlockBlob

Request Body: Content of pdf file as binary
*/

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $response_array['uploadUrl']);
  curl_setopt($ch, CURLOPT_PUT, true); //PUT REQUEST
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      //'Authorization: Bearer '.$access_token, // authorization for bluemix iam
      'x-ms-blob-type: BlockBlob',
      'Content-Type: application/pdf',
      'Content-Length: '.$read_file_size,
  ));

  $image_or_file = fopen($read_file, "rb");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
  curl_setopt($ch, CURLOPT_INFILE, $image_or_file);
  curl_setopt($ch, CURLOPT_INFILESIZE, $read_file_size);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,300);
  curl_setopt($ch, CURLOPT_TIMEOUT, 300);
  curl_setopt($ch, CURLOPT_UPLOAD, true);
  curl_setopt($ch, CURLOPT_HEADER, true);

  $result = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);  
  $gks_curl_info =curl_getinfo($ch); // request headers from response (check if something wrong)
  curl_close ($ch);
  fclose($image_or_file);

//  echo '<pre>';
//  echo $read_file_size."\n";
//  echo $gks_curl_info['size_upload']."\n";
//  var_dump($result);
//  echo "\n";
//  var_dump($gks_curl_errno);
//  echo "\n";
//  print_r($gks_curl_info);
  
  if (!($gks_curl_errno==0 and 
      isset($gks_curl_info['size_upload']) and 
      intval($gks_curl_info['size_upload'])==$read_file_size)) {
    $ret['message']='Σφάλμα αποστολής (54239322423)';return $ret;
  }
  $sql_xxx="update gks_acc_".$xxx." set
  paroxos_send_pdf=now(),
  paroxos_send_pdf_name='".$db_link->escape_string($xxx_item['print_file_name'])."'
	where id_acc_".$xxx."=".$xxx_item['id_acc_'.$xxx];
  $result_xxx = $db_link->query($sql_xxx); 
  if (!$result_xxx) {
    debug_mail(false,'error sql',$sql_xxx);
    return array('success' => false, 'message' => 'sql error');}      

  
  
  //die();
  

  $input=array();	
  $input['acc_'.$xxx.'_id']=$xxx_item['id_acc_'.$xxx];
	$input['id_company_paroxos']=$row_paroxos['id_company_paroxos'];
  $input['paroxos_token']=$ret_token['pc_token_id'];
  $input['paroxos_url']=$ret_token['pc_url1'];
  $input['processId']=$xxx_item['paroxos_processId'];
  $input['externalSystemId']=$xxx_item['id_acc_'.$xxx].'';


  
  $ret_send = gks_paroxos_parochos_gr_get_url('/api/Finalize','POST',$input);
  //debug_mail(false,'api get',print_r($input,true).'<br>'.print_r($ret_send,true));
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
	$response_array=$ret_send['response_array'];
	if (isset($response_array['status'])==false or intval($response_array['status'])!=1) {$ret['message']='Σφάλμα αποστολής (54239322421)';return $ret;}

  /*Array
  (
      [processId] => 21f2483b-745c-44e4-5b2a-08dc34006775
      [externalSystemId] => 11519
      [errorSeverity] => 
      [timeStamp] => 2024-02-26T07:54:50.5715205
      [signing] => Array
          (
              [uid] => BE1CDC8CEDF777269D5E5EBB86CE16742DAABFF4
              [mark] => 400001924652465
              [authenticationCode] => 8298F5E9A3648D91D5DF3EF499C6734344243A44
              [qrCode] => https://beta-srv.parochos.gr/FileDocument/Get/21f2483b-745c-44e4-5b2a-08dc34006775
              [pdfUploaded] => 1
              [pdfFileUrl] => https://edelmibeta.blob.core.windows.net/af6ffcf2-56ae-4080-acf0-2f8795747ceb/21f2483b-745c-44e4-5b2a-08dc34006775/a265c2ab-2559-41e4-e180-08dc340067d2
              [publishStatus] => 
              [paymentTokens] => Array
                  (
                  )
  
          )
  
      [attachments] => 
      [status] => 1
      [errorCategory] => 
      [errorCode] => 
      [errorMessage] => 
  ) */
  $pdfFileUrl='';if (isset($response_array['signing']['pdfFileUrl'])) $pdfFileUrl=trim_gks($response_array['signing']['pdfFileUrl']); 
  
  if ($pdfFileUrl!='') {
    $sql_xxx="update gks_acc_".$xxx." set
    paroxos_send_pdf_url='".$db_link->escape_string($pdfFileUrl)."'
  	where id_acc_".$xxx."=".$xxx_item['id_acc_'.$xxx];
    $result_xxx = $db_link->query($sql_xxx); 
    if (!$result_xxx) {
      debug_mail(false,'error sql',$sql_xxx);
      return array('success' => false, 'message' => 'sql error');}      
  }
  
  //echo '<pre>dddakskdfkfbb ';print_r($response_array);die();  
  
  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;  
  
}
