<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


/*
{
    "invoiceSignatures": null,
    "invoiceMarking": {
        "verificationHash": "DD5043F94D3428AE8F1B2C538DE3530B8A401962",
        "cancellationMark": null,
        "qrCode": "https:\/\/test.vs.gr\/iv\/invoice\/10cc0a7d97a733c60197b23d0846280c",
        "aadePreviouslySubmittedError228": false,
        "invoiceId": "10cc0a7d97a733c60197b23d0846280c",
        "invoiceIdentifier": "418437FB1F6578F4FFBD067D110D61BBEC5326FA",
        "myDataQrCode": "https:\/\/mydataapidev.aade.gr\/TimologioQR\/QRInfo?q=ijepO4QcpfosocBBiHVJnw5QqMko827TnowZ4jyKv7msC8jTpzXgiIt0OhRlSOBxg1BYVGSRE4DdL37vmKmjGECUCRfMNqMRaidl02drN2s%3d",
        "providerUrl": "https:\/\/test.vs.gr",
        "mark": "400001952309304"
    },
    "errors": []
}
*/



function gks_paroxos_ilyda_com_get_url($sub_url,$request_type,$input) {
 
  //echo '<pre>'.$sub_url."\n".$request_type."\n".print_r($input,true)."\n"; die(); 
  
  $p_send=$input;
  $p_response=[];
 
  if ($input['paroxos_live']) {
    $url=GKS_ILYDA_COM_MODE_LIVE_API.$sub_url;
  } else {
    $url=GKS_ILYDA_COM_MODE_TEST_API.$sub_url;
  }
  
  //$paroxos_token='';    if (isset($input['paroxos_token'])) $paroxos_token    =$input['paroxos_token'];
  $paroxos_pc_username=''; if (isset($input['paroxos_pc_username'])) $paroxos_pc_username=$input['paroxos_pc_username'];
  $paroxos_pc_password=''; if (isset($input['paroxos_pc_password'])) $paroxos_pc_password=$input['paroxos_pc_password'];
  
  unset($input['paroxos_live']);
  unset($input['paroxos_pc_username']);
  unset($input['paroxos_pc_password']);
  unset($input['Email']);
  unset($input['password']);
  unset($input['acc_inv_id']);
  unset($input['acc_pay_id']);
  unset($input['id_company_paroxos']);
  unset($input['paroxos_mydata_live']);
  unset($input['paroxos_token']);
  unset($input['paroxos_url']);
  
  //echo 'url: '.$url."\n";die();
  
 
  //$input=array('ggg'=>1,'hhhh'=>'gggg');
  
  $ssss=json_encode($input);
  
  //echo '<pre>sssssss';var_dump($ssss);echo json_last_error();die();
  //echo '<pre>ssssss '.$request_type;print_r($input);print "\n"; die(); 
  //echo '<pre>ssssss '.$request_type.'|';print json_encode($input);print '|';var_dump($input);print "\n"; die(); 
  
  if ($request_type=='GET') {
    if (is_array($input)) {
      if (count($input)>0) {
        $myq=http_build_query($input);
        $url.='?'.$myq;
      }
      //echo '<pre>'.$url;die();
    } else if (is_string($input)) {
      if ($input!='') {
        $url.='?'.$input;
      }
    }
  } else {
//  	$input=array(
//  		'mode' => $formdata,
//  		'formdata' => $input,
//  		
//    );
    //$input=json_encode($input);
    $input=json_encode($input);
    //$input2=$input;
    //http_build_query_for_curl( $input2, $post2 );
    //$input=$post2;
    //print '<pre>inputinputinput '.$input;die();
    
    //$input=curl_postfields_flatten($input);
    
  }
  //file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/ttt.txt',$input);
  
  //echo '<pre>';var_dump($input); die(); 
  //echo '<pre>'.$input."\n"; die(); 
  //echo '<pre>';print_r($input); die(); 
  //echo '<pre>';print_r($url); die(); 

  //$url='https://test.easyfilesselection.com/my/admin-acc-aade-docs-exec.php';
  
  //echo 'url: '.$request_type.' '.$url."\n";die();
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );  
  //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  $headers=[];
  
  //curl_setopt($ch, CURLOPT_HEADER, true);
  if ($request_type=='POST') {
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
    //echo '<pre>input ';print_r($input);die();
    //$headers[]='Content-Type: multipart/form-data';
    //$headers[]='Content-Type: application/x-www-form-urlencoded';
    //$headers[]='Accept: application/json';
    $headers[]='Content-Type: application/json';
    //$headers[]='Content-Length: '.strlen($input);
    
  } else {
    $headers[]='Content-Type: text/html; charset=UTF-8';
    $headers[]='Accept: application/json';
  }
  //if ($paroxos_token!='') {
  //  $headers[]='Authorization: Bearer '.$paroxos_token;
  //} 
  $headers[]='Authorization: Basic '.base64_encode($paroxos_pc_username.':'.$paroxos_pc_password);  
   
  curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
  
  //echo '<pre>headers ';print_r($headers);die();
  
  //echo '<pre>'.$url;die();
  //echo '<pre>'.$paroxos_token;die();
  //echo '<pre>';print_r($input);die();
  
  
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info =curl_getinfo($ch);
  curl_close ($ch);

//	echo '<pre>';
//  echo 'gks_curl_info:'."\n";
//  print_r($gks_curl_info);
//  echo "\n\n";
//  echo 'gks_curl_errno:'."\n";
//  var_dump($gks_curl_errno);
//  echo "\n\n";
//  echo 'result:'."\n";
//  var_dump($result);
//  echo "\n\n";
//  die();
  
//  //echo '<pre>'; print_r($gks_curl_info);print $result;die();
  //echo '<pre>'; print $result;die();
  

  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/ylida_send_'.time().'.json',$url."\n".(is_array($input) ? print_r($input,true) : $input));
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/ylida_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$result);


  //echo '<pre>sssssssssss '.$gks_curl_http_code.' '; print $result;die();
  
  
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
    debug_mail(false,'ilyda vs.gr error',                        gks_lang('Δεν βρέθηκε ο διακομιστής').'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Δεν βρέθηκε ο διακομιστής'),'http_code'=>$gks_curl_http_code);
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    debug_mail(false,'ilyda vs.gr error',                        gks_lang('Δεν βρέθηκε το σημείο').'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Δεν βρέθηκε το σημείο'),'http_code'=>$gks_curl_http_code);
  
  } else if ($gks_curl_http_code==400) { 
    $error='ilyda vs.gr '.gks_lang('Σφάλμα').' '.gks_lang('Οι παράμετροι είναι λάθος').' (400)';
    //$parts=explode("\r\n\r\n",$result,2);
    //if (count($parts)==2) {
    	$response=$result; //trim($parts[1]);
		  $response_array = json_decode($response, true);
		  if (!($response_array === null && json_last_error() !== JSON_ERROR_NONE) and is_array($response_array)) {
		    //print '<pre>';print_r($response_array);die();
		    
				$paroxos_error=[];
				if (isset($response_array['code']))  $paroxos_error[]=gks_lang('Κωδικός σφάλματος').': '.$response_array['code'];
				//if (isset($response_array['error']['title']))  $paroxos_error[]=gks_lang('Τίτλος σφάλματος').': '.$response_array['error']['title'];
				if (isset($response_array['defaultMessage']))  $paroxos_error[]=gks_lang('Μήνυμα σφάλματος').': '.htmlspecialchars($response_array['defaultMessage']);
				if (isset($response_array['fatal']))  $paroxos_error[]='Fatal: '.$response_array['fatal'];
    		
    		if (isset($response_array['errorFields'])) {
    		  $pp_cc=0;
    			foreach ($response_array['errorFields'] as $verror) {
    				$pp_cc++;
    				$paroxos_error[]=gks_lang('Πεδίο').' '.$pp_cc.': '.htmlspecialchars(print_r($verror,true));
    			} 
    		}
    		//print '<pre>';print_r($response_array);print_r($paroxos_error);die();
//    		if (isset($response_array['error']['data']) and is_array($response_array['error']['data'])) {
//    			foreach ($response_array['error']['data'] as $mkey => $vdata) {
//    				if (is_string($vdata) or is_numeric($vdata)) 
//    				$paroxos_error[]=$mkey.': '.$vdata;
//    			} 
//    		}
        $response_array['gks_ok']=0;
    		
    		if (count($paroxos_error)>0) {
    			$error.='<br>'.implode('<br>',$paroxos_error);
	    		return array('success' => true, 'message' => $error, 'response_array' => $response_array);
    		}
   		
				//print '<pre>';print_r($response_array);die();
				
			} 
			
    //}
    
    
    debug_mail(false,'ilyda vs.gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error,'http_code'=>$gks_curl_http_code);
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $error='ilyda vs.gr '.gks_lang('Δεν επιτρέπεται η πρόσβαση');
    if ($sub_url=='/api/send') $error.=' Unauthorized request. The jwt is either invalid or expired.';
    debug_mail(false,'ilyda vs.gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error,'http_code'=>$gks_curl_http_code);
  
  } else if ($gks_curl_http_code==403) { 
    $error='ilyda vs.gr '.gks_lang('Σφάλμα').' (403).';
    if ($sub_url=='/api/account/loginToSubscription') $error.=' Username, password or secret key is invalid';
    if ($sub_url=='/api/token/refresh') $error.=' Either token or refresh token is invalid';
    debug_mail(false,'ilyda vs.gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result.'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error,'http_code'=>$gks_curl_http_code);
  
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    debug_mail(false,'ilyda vs.gr error',                        gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error: '.$gks_curl_http_code,'http_code'=>$gks_curl_http_code);
  
  }
  
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/ylida_result_txt_'.time().'.txt',$result);
  
  
  //$parts=explode("\r\n\r\n",$result,2);
  //if (count($parts)!=2) {
  //  debug_mail(false,'ilyda vs.gr result error',$result);
  //  return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Σφάλμα δεδομένων').' (1).'.$result);}

  $response=trim($result); //trim($parts[1]);
  if ($response=='') {
    debug_mail(false,'ilyda vs.gr response error',$response);
    return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Σφάλμα δεδομένων').' (2).'.$result);}

	if ($response=='Ilyda Generic Error (1)') {
    debug_mail(false,'ilyda vs.gr response error',$response);
    return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Σφάλμα δεδομένων').' (2.1).<br>'.$response);}
		
	
  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'ilyda vs.gr json_decode error',base64_encode($result) .'|||'.$result.'|||'.base64_encode($response) .'|||'.$response);
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (3).'.$result);}


  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/ylida_send_'.time().'.json',json_encode(json_decode($input,true),JSON_PRETTY_PRINT));
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/ylida_response_'.time().'.json',json_encode($response_array,JSON_PRETTY_PRINT));

  
  $response_array['gks_ok']=true;

  //echo '<pre>';print_r($response_array);die();
  
  return array('success' => true, 'message' => 'OK', 'response_array' => $response_array);
}

function gks_paroxos_invoice_xml_build_ilyda_com($id,$paroxos_params,$struct_data) {
	
	$ret = array('success' => false, 'message' => 'generic error');
	
	//echo  '<pre>';print_r($paroxos_params); die();
	//echo '<pre>';print_r($struct_data);die();
	
	// i sira einai symfona me to 
	//C:/owncloud/owncloud/banks-courrier/ilida/2024_05_14/odogies%20ilopoiisis%20eInvoicing%20v1.pdf
	//Page 20 of 133 
	$doc_table=$struct_data['doc_table'];
	
	$b2x='';
	if (in_array($struct_data['eidos_parastatikou_aade_code'], 
	    array('11.1','11.2','11.3','11.4','11.5','8.4','8.5'))) {
    $b2x='b2c';
	} else if (in_array($struct_data['eidos_parastatikou_aade_code'], 
	    array('1.1','1.2','1.3','1.4','1.5','1.6',
	          '2.1','2.2','2.3','2.4',
	          '3.1','3.2',
	          '5.1','5.2',
	          '6.1','6.2',
	          '7.1',
	          '8.1','8.2','9.3'))) {
    $b2x='b2b';
    if ($struct_data['row']['is_b2g']==1) {
      $b2x='b2g';
    }
    
	}
	
	if ($b2x=='') {
	  $ret['message']=gks_lang('Δεν έχει υπολοιπηθεί ακόμη η αποστολή τύπου τιμολογίου [1] σε πάροχο');
	  $ret['message']=str_replace('[1]',$struct_data['eidos_parastatikou_aade_code'],$ret['message']);
		debug_mail(false,$ret['message'],''); return $ret;    
	}
	//echo '<pre>';echo $b2x;die();
	
	$xml=$struct_data['xml'];
	$adata=array();
	
	
	$myData_XML=true; //$myData_XML=$paroxos_params['paroxos_myData_XML'];
	
	/*
	Σε περίπτωση που μας στείλετε το MyData XML μπορείτε να μην συμπληρώσετε τα παρακάτω πεδία:
  •	invoice.serialNumber
  •	invoice.seriesNumber
	•	invoice.aadeData
  •	invoice.paymentMethods
  •	invoice.docTotals.aadeDocTotals
  •	invoice.vatBreakdowns[].aadeVatData
  •	invoice.invoiceLines[].lineVatInfo.aadeVatData
  •	invoice.docLevelAllowances[].aadeTaxData
  •	invoice.docLevelCharges[].aadeTaxData
  */


	
	if ($myData_XML==false) {
    if (isset($xml['invoiceHeader']['aa'])) $adata['serialNumber']=$xml['invoiceHeader']['aa'];
    if (isset($xml['invoiceHeader']['series'])) $adata['seriesNumber']=$xml['invoiceHeader']['series'];
	}
	
	/*
	selfPricing (ET-1) 
	https://www.taxheaven.gr/law/4308/2014
	Άρθρο 9.   Περιεχόμενο τιμολογίου
	Τον όρο «Αυτο-τιμολόγηση», όταν το τιμολόγιο εκδίδεται από τον λήπτη των αγαθών ή των υπηρεσιών. 
	Το πεδίο selfPricing ορίζει αν πρόκειται για Τιμολόγιο Αυτοτιμολόγησης 
	*/
	//$adata['selfPricing']=false;
	
	if ($b2x=='b2b' or $b2x=='b2g') {
  	$adata['sellerIdentifiers']=array(); //(BT-29) 
  	if (isset($xml['issuer']['vatNumber'])) {
    	$adata['sellerIdentifiers'][]=array(
    	  //sellerSchemeIdentifier Example: "VAT" The identification scheme identifier of the Seller identifier
    	  //'sellerSchemeIdentifier' => 'VAT', 
        'sellerIdentifier'=> 
          ((isset($struct_data['row']['company_country_ee']) and $b2x!='b2c') ? $struct_data['row']['company_country_ee'] : '').
          $xml['issuer']['vatNumber'],
    	);
    }
  }
  if (isset($xml['invoiceHeader']['peppol_code'])) $adata['invoiceTypeCode']=$xml['invoiceHeader']['peppol_code'];
  //https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL1001-inv/
  //https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL1001-cn/
  
	
	$adata['seller']=array();
	if (isset($xml['issuer']['vatNumber'])) $adata['seller']['sellerVatIdentifier']=
  	((isset($struct_data['row']['company_country_ee']) and $b2x!='b2c') ? $struct_data['row']['company_country_ee'] : '').
  	$xml['issuer']['vatNumber'];
	if (isset($xml['issuer']['name'])) $adata['seller']['sellerName']=$xml['issuer']['name'];

//  "seller"
//    "sellerContact": {
//      "sellerContactEmail": "sko@keysoft.gr",
//      "sellerContactPoint": "ΧΡΗΣΤΗΣ ΑΝΑΠΤΥΞΗΣ",
//      "sellerContactPhoneNumber": "210-7484381"
//    },
  $adata['seller']['sellerContact']=array();

	global $my_wp_user_info;
	if (isset($my_wp_user_info->data->display_name)) {
	  $uumyusername= trim_gks($my_wp_user_info->data->display_name);
	  $adata['seller']['sellerContact']['sellerContactPoint']=$uumyusername;
	}
	//echo $uumyusername;die();


	if (isset($xml['issuer']['email'])) $adata['seller']['sellerContact']['sellerContactEmail']=$xml['issuer']['email'];
	if (isset($xml['issuer']['phone'])) $adata['seller']['sellerContact']['sellerContactPhoneNumber']=$xml['issuer']['phone'];

  if (isset($xml['issuer']['company_gemi_number'])) {
    $adata['sellerLegalRegistrationIdentifier']=array( 
       //ΑΡ ΓΕΜΗ 
      'sellerLegalRegistrationIdentifier' => $xml['issuer']['company_gemi_number'],
    );
    
  }
  

	
	//print '<pre>';print_r($adata);die();
	//print '<pre>';print_r($xml);die();
	
	$adata['seller']['sellerPostalAddress']=array();
	
	if ($struct_data['row']['company_sub_id']==0) {//kentriko
	  if (trim_gks($struct_data['row']['company_odos'])!='') $adata['seller']['sellerPostalAddress']['sellerAddressLine1']=trim_gks(trim_gks($struct_data['row']['company_odos']).' '.trim_gks($struct_data['row']['company_arithmos']));
	  //if (trim_gks($struct_data['row']['company_orofos'])!='') $adata['seller']['sellerPostalAddress']['sellerAddressLine2']=trim_gks($struct_data['row']['company_orofos']);
	  if (trim_gks($struct_data['row']['company_perioxi'])!='') $adata['seller']['sellerPostalAddress']['sellerAddressLine2']=trim_gks($struct_data['row']['company_perioxi']);
	  if (trim_gks($struct_data['row']['company_poli'])!='') $adata['seller']['sellerPostalAddress']['sellerCity']=trim_gks($struct_data['row']['company_poli']);
	  if (trim_gks($struct_data['row']['company_tk'])!='') $adata['seller']['sellerPostalAddress']['sellerPostCode']=trim_gks($struct_data['row']['company_tk']);
	  if (trim_gks($struct_data['row']['company_nomos_descr'])!='') $adata['seller']['sellerPostalAddress']['sellerCountrySubdivision']=trim_gks($struct_data['row']['company_nomos_descr']);
	  if (trim_gks($struct_data['row']['company_country_initials'])!='') $adata['seller']['sellerPostalAddress']['sellerCountryCode']=trim_gks($struct_data['row']['company_country_initials']);
	  
  } else { //ypokatastima
	  if (trim_gks($struct_data['row']['company_sub_odos'])!='') $adata['seller']['sellerPostalAddress']['sellerAddressLine1']=trim_gks(trim_gks($struct_data['row']['company_sub_odos']).' '.trim_gks($struct_data['row']['company_sub_arithmos']));
	  //if (trim_gks($struct_data['row']['company_sub_orofos'])!='') $adata['seller']['sellerPostalAddress']['sellerAddressLine2']=trim_gks($struct_data['row']['company_sub_orofos']);
	  if (trim_gks($struct_data['row']['company_sub_perioxi'])!='') $adata['seller']['sellerPostalAddress']['sellerAddressLine2']=trim_gks($struct_data['row']['company_sub_perioxi']);
	  if (trim_gks($struct_data['row']['company_sub_poli'])!='') $adata['seller']['sellerPostalAddress']['sellerCity']=trim_gks($struct_data['row']['company_sub_poli']);
	  if (trim_gks($struct_data['row']['company_sub_tk'])!='') $adata['seller']['sellerPostalAddress']['sellerPostCode']=trim_gks($struct_data['row']['company_sub_tk']);
	  if (trim_gks($struct_data['row']['company_sub_nomos_descr'])!='') $adata['seller']['sellerPostalAddress']['sellerCountrySubdivision']=trim_gks($struct_data['row']['company_sub_nomos_descr']);
	  if (trim_gks($struct_data['row']['company_sub_country_initials'])!='') $adata['seller']['sellerPostalAddress']['sellerCountryCode']=trim_gks($struct_data['row']['company_sub_country_initials']);
  }
  
  //$adata['seller']['sellerTradingName']=null;
  
  //$adata['seller']['sellerTaxRegistrationIdentifier']=null;
  //$adata['seller']['sellerAdditionalLegalInfo']
  
  if (isset($paroxos_params['paroxos_branch'])) $adata['seller']['branch']=$paroxos_params['paroxos_branch'];
  
  
  /*projectReference (BT-11)
  Cardinality: 0..1
  Type: String
  Example: "1|123412312"
  The identification of the project the invoice refers to.
  B2G - GREECE
  Το πεδίο BT-11 συμπληρώνεται ως εξής:
  • «1| ΑΔΑ Ανάληψης» : (Αριθμός Διαδικτυακής Ανάρτησης) όταν η αναθέτουσα αρχή είναι φορέας της
  Κεντρικής Διοίκησης σύμφωνα με το άρθρο 14 του νόμου 4270/2014 (Α.143) και οι δαπάνες βαρύνουν τον
  τακτικό προϋπολογισμό.
  Δηλαδή καταχωρούνται: η ένδειξη «1», το διαχωριστικό «|» και ο Αριθμός Διαδικτυακής Ανάρτησης (ΑΔΑ)
  της σχετικής απόφασης δέσμευσης πίστωσης.
  • «2|ο κωδικοποιημένος Ενάριθμος» όταν οι δαπάνες βαρύνουν τον Προϋπολογισμό Δημοσίων Επενδύσεων
  (ΠΔΕ), δηλαδή καταχωρούνται: η ένδειξη «2», το διαχωριστικό «|» και ο Ενάριθμος του έργου όπως
  αναφέρεται στη σχετική ΣΑ και μνημονεύεται υποχρεωτικά στο κείμενο της σύμβασης.
  • «3|ΑΔΑ Ανάληψης» όταν οι δαπάνες δε βαρύνουν τους ανωτέρω προϋπολογισμούς, δηλαδή
  καταχωρούνται:
  Η ένδειξη «3», το διαχωριστικό «|» και ο Αριθμός Διαδικτυακής Ανάρτησης της σχετικής απόφασης
  δέσμευσης πίστωσης
  Στην περίπτωση Πιστωτικού Τιμολογίου το πεδίο ΒΤ-11 είναι κενό. Μόνο ένα Στοιχείο Αναφοράς
  Αγαθού/Υπηρεσίας/Μελέτης/Έργου επιτρέπεται σε επίπεδο παραστατικού. Ο ΑΔΑ είναι ο μοναδικός
  κωδικός αριθμός που λαμβάνει το έγγραφο της Απόφαση Ανάληψης Υποχρέωσης (ΑΑΥ) όταν αναρτηθεί
  στο σύστημα του ΔΙΑΥΓΕΙΑ. Ο ΑΔΑ αναφέρεται στο έγγραφο της Σύμβασης. Η ΑΑΥ είναι η Απόφαση που
  εκδίδεται από το Διατάκτη της Αναθέτουσας, βάσει της οποίας πραγματοποιείται η δέσμευση πίστωσης και
  γίνεται η σχετική ανάληψη υποχρέωσης σε συγκεκριμένη κατηγορία δαπάνης του εγκεκριμένου
  προϋπολογισμού του έργου πριν την υλοποίηση της δαπάνης.
  Για πολυετείς δαπάνες υπάρχει παραπάνω από μια ΑΑΥ. Σε αυτή την περίπτωση αν δεν αναφέρεται στο
  έγγραφο της σύμβασης χρειάζεται να επικοινωνήσετε με την υπηρεσία της Αναθέτουσας Αρχής που εκτελεί
  τη σύμβαση. Ενάριθμος είναι δεκατετραψήφιος κωδικός μοναδικής αναγνώρισης των έργων του ΠΔΕ, ο
  οποίος παράγεται από το Ολοκληρωμένο Πληροφοριακό Σύστημα της Διεύθυνσης Δημοσίων Επενδύσεων
  του Υπουργείου.Ανάπτυξης και Επενδύσεων (e-ΠΔΕ). Τα πρώτα τέσσερα ψηφία αποτελούν το έτος πρώτης
  ένταξης του έργου στο ΠΔΕ, τα επόμενα τον κωδικό της Συλλογικής Απόφασης (ΣΑ), στην οποία είναι
  ενταγμένο και τα υπόλοιπα τον αύξοντα αριθμό του έργου. 	
  Ο Ενάριθμος χαρακτηρίζει μοναδικά ένα έργο ή μελέτη. Στη Σύμβαση με το Ελληνικό Δημόσιο αναφέρεται
  πάντα η πηγή χρηματοδότησης (ΠΔΕ ή/και ενάριθμος). Σε περίπτωση που δεν καταστεί εφικτή η ανεύρεση
  του ενάριθμου του έργου, θα πρέπει ο δικαιούχος να απευθύνεται στην αρμόδια Αναθέτουσα Αρχή (ΑΑ).  
  */ 
  //b2g "1|123412312" "3|1234567" The identification of the project the invoice refers to.
  
  if ($b2x=='b2b' or $b2x=='b2g') {
    if (isset($struct_data['row']['project_reference']) and trim_gks($struct_data['row']['project_reference']!='')) { 
      $adata['projectReference']=trim_gks($struct_data['row']['project_reference']); 
    }
  }
  
  /*dispatchAdviceReference (BT-16)
  Cardinality: 0..1
  Type: String
  Example: "A/1333/2020-10-10"
  An identifier of a referenced despatch advice.
  B2G - GREECE
  Στο πεδίο συμπληρώνεται κατά περίπτωση, ο αριθμός του Δελτίου Αποστολής (ΔΑ) του αγαθού στο οποίο
  αναφέρεται το Η.Τ. Κατά PEPPOL η αντιστοιχία ΗΤ με ΔΑ είναι 1 προς 1.
  Εάν το Η.Τ είναι συγκεντρωτικό και στην περίπτωση που αντιστοιχεί σε περισσότερα του ενός Δελτίων
  Αποστολής, στην τρέχουσα έκδοση του Ελληνικού CIUS και για μόνο τους Προμηθευτές του Ελληνικού
  Δημοσίου με έδρα στην Ελλάδα, στο πεδίο BT-16 θα καταχωρείται το αναγνωριστικό του πρώτου ΔΑ και
  στην ομάδα BG-24 τα υπόλοιπα ΔΑ.
  Notes:
  o στο πεδίο BT-16 θα καταχωρείται το αναγνωριστικό του πρώτου ΔΑ και στην ομάδα BG-24 τα
  υπόλοιπα ΔΑ.	*/
	//dispatchAdviceReference  "A/1333/2020-10-10",
  if ($b2x=='b2b' or $b2x=='b2g') {
    if (isset($struct_data['row']['dispatchAdviceReference'])) $adata['dispatchAdviceReference']=$struct_data['row']['dispatchAdviceReference'];
  }
	
	
	
	/*buyerAccountingReference (BT-19)
  Cardinality: 0..1
  Type: String
  Example: "KAE 123"
  A textual value that specifies where to book the relevant data into the Buyer's financial accounts.
  B2G - GREECE
  Κωδικός λογιστικής εγγραφής στις χρηματοοικονομικές καταστάσεις (ΑΛΕ/ΚΑΕ, ισολογισμός,
  απολογισμός). */
	//buyerAccountingReference "KAE 123"
  if ($b2x=='b2b' or $b2x=='b2g') {
    if (isset($struct_data['row']['buyerAccountingReference'])) $adata['buyerAccountingReference']=$struct_data['row']['buyerAccountingReference'];
	}
	
	/*delivery (BG-13)
  Cardinality: 0..1
  Type: Delivery
  Object reference of type Delivery .
  A group of business terms providing information about where and when the goods and services invoiced are
  delivered.
  B2G - GREECE
  Καταχωρούνται πληροφορίες για την εσωτερική υπηρεσία του Αγοραστή/Α.Α, που φυσικά παραλαμβάνει τα
  αγαθά/υπηρεσίες που τιμολογουνται στο αντίστοιχο Η.Τ, όπως Ονομασία, Ημερομηνία Παράδοσης ,
  διεύθυνση κ.λ.π. Η συμπλήρωση της ομάδα πεδίων BG-13 είναι υποχρεωτική. */
  if ($b2x=='b2b' or $b2x=='b2g') {
    
  	$adata['delivery']=array(); 
  	if (isset($xml['counterpart']['name'])) $adata['delivery']['partyName']=$xml['counterpart']['name'];  //(BT-70) 
  	$adata['delivery']['deliveryInvoicingPeriod']=null; // (BG-14) : DeliveryInvoicingPeriod
  	// deliveryInvoicingPeriod {
  	//  startDate (BT-73) "2020-10-05T00:00:00.000+0200"
  	//  endDate (BT-74)    "2020-10-05T00:00:00.000+0200"
   
  	//}
  	  
  	$adata['delivery']['deliveryLocationIdentifier']=null;// (BT-71) : DeliveryLocationIdentifier
  //	deliveryLocationIdentifier {
  //	  deliveryLocationIdentifier (BT-71)  "Buyer Offshore Inc. Singapore offices"
  //    deliveryLocationSchemeIdentifier (BT-71) "0001"
  //	}
  	
  	$adata['delivery']['actualDeliveryDate']=null;// (BT-71)  "2020-10-05T00:00:00.000+0200"
  	$adata['delivery']['deliveryAddress']=array(); // (BG-15) : DeliveryAddress
  
    if ($struct_data['row']['address_extra']<=0) {
      //deliveryPostCode (BT-78) 
      if (isset($xml['counterpart']['address']['postalCode'])) $adata['delivery']['deliveryAddress']['deliveryPostCode']=$xml['counterpart']['address']['postalCode'];
      //deliveryAddressLine1 (BT-75) 
    	if (isset($xml['counterpart']['address']['street'])) $adata['delivery']['deliveryAddress']['deliveryAddressLine1']=$xml['counterpart']['address']['street'];
      //deliveryAddressLine2 (BT-76) 
      //deliveryAddressLine3 (BT-165) 
      //deliveryCountryCode (BT-80) "GR"
      if (isset($xml['counterpart']['country'])) $adata['delivery']['deliveryAddress']['deliveryCountryCode']=$xml['counterpart']['country']; 
      //deliveryCountrySubdivision (BT-79) //"Ελλάδα"
      if (isset($struct_data['row']['party_nomos_descr'])) $adata['delivery']['deliveryAddress']['deliveryCountrySubdivision']=$struct_data['row']['party_nomos_descr'];
      //deliveryCity (BT-77) 
      //na mpei o nomos 
      if (isset($xml['counterpart']['address']['city'])) $adata['delivery']['deliveryAddress']['deliveryCity']=$xml['counterpart']['address']['city'];
    } else {
      if (isset($struct_data['row']['destination_data_tk'])) $adata['delivery']['deliveryAddress']['deliveryPostCode']=$struct_data['row']['destination_data_tk'];
      if (isset($struct_data['row']['destination_data_odos'])) $adata['delivery']['deliveryAddress']['deliveryAddressLine1']=trim_gks(trim_gks($struct_data['row']['destination_data_odos']).' '.trim_gks($struct_data['row']['destination_data_arithmos']));
      if (isset($struct_data['row']['party_delivery_country_initials'])) $adata['delivery']['deliveryAddress']['deliveryCountryCode']=$struct_data['row']['party_delivery_country_initials']; 
      if (isset($struct_data['row']['party_delivery_nomos_descr'])) $adata['delivery']['deliveryAddress']['deliveryCountrySubdivision']=$struct_data['row']['party_nomos_descr'];
      if (isset($struct_data['row']['destination_data_poli'])) $adata['delivery']['deliveryAddress']['deliveryCity']=$struct_data['row']['destination_data_poli'];
    }
  }
  
  /*vatBreakdowns (BG-23)
  Cardinality: 0..n
  Type: Collection<VatBreakdown>
  Vat Breakdown of this Invoice.
  Contains a set of VatBreakdown objects.
  "vatBreakdowns": [
    {
      "categoryTaxAmount": 48.0, (BT-117)
      "exemptionReasonCode": null, (BT-121)  https://docs.peppol.eu/poacc/billing/3.0/codelist/vatex/
      "categoryTaxableAmount": 200.0, (BT-116) 
      "categoryCode": "S", (BT-118) https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL5305/
      "categoryRate": 24.0,  (BT-119) 
      
      "aadeVatData": { (EG-3) https://www.aade.gr/sites/default/files/2020-07/myDATA%20API%20Documentation%20v1.0_official_upd.pdf
        "aadeVatExemptionCategory": null, y (ET-26)  
        "aadeVatCategory": 1 (ET-29)
      },
      "exemptionReasonText": null, (BT-120) 
      "id": null
      "invoice": null
    }
  ],  
  */
  
  
  
  $adata['vatBreakdowns']=array();
  //if ($b2x=='b2b' or $b2x=='b2g') {
  
  if ($doc_table=='gks_acc_pay') {
    foreach ($struct_data['prow_array'] as &$pitem) {
      $pitem['product_fpa_pososto']=0;
      $pitem['product_price_final_all_fpa']=0;
      $pitem['product_price_final_all_net']=$pitem['xml_netValue'];
      $pitem['product_quantity']=1;
      $pitem['product_price_final_peritem_net']=$pitem['xml_netValue'];
      $pitem['product_aa']=1;
      $pitem['product_withheldPercentCategory']=0;
      //$pitem['totalStampDutyAmount']=0;
      //$pitem['totalOtherTaxesAmount']=0;
      //$pitem['totalFeesAmount']=0;
      //$pitem['totalStampDutyAmount']=0;

      //print '<pre>prow_array item ';print_r($pitem);die();
      
    }
    unset($pitem);
    
  }

  if ($doc_table=='gks_whi_mov') {
    foreach ($struct_data['prow_array'] as &$pitem) {
      $pitem['product_fpa_pososto']=0;
      $pitem['product_price_final_all_fpa']=0;
      $pitem['product_price_final_all_net']=0;
      //$pitem['product_quantity']=1;
      $pitem['product_price_final_peritem_net']=0;
      $pitem['product_withheldPercentCategory']=0;
      $pitem['product_stampDutyAmount']=0;
      $pitem['product_otherTaxesAmount']=0;
      $pitem['product_feesAmount']=0;



      //print '<pre>prow_array item ';print_r($pitem);die();
      
    }
    unset($pitem);    
  }  
  
  //print '<pre>prow_array '.$doc_table;print_r($struct_data['prow_array']);die();
    
    $myvatarray=array();
    foreach ($struct_data['prow_array'] as $pitem) {
      //if (isset($myvatarray[$pitem['product_fpa_pososto']])==false) {
        
        //https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL5305/
        //S - Standard rate Code specifying the standard rate.
        //E - Exempt from tax
        //Z - Zero rated goods Code specifying that the goods are at a zero rate. 
        //O - Services outside scope of tax | Code specifying that taxes are not applicable to the services.
           //8 - Εγγραφές χωρίς ΦΠΑ (πχ Μισθοδοσία, Αποσβέσεις)
        if ($struct_data['row']['eidos_parastatikou_aade_code']=='3.1') { 
          $categoryCode='O';
        } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='8.1') { 
          $categoryCode='O';
        } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='8.2') { 
          $categoryCode='O';
        } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='8.4') { 
          $categoryCode='O';
        } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='8.5') { 
          $categoryCode='O';
        } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='9.3') { 
          $categoryCode='Z';
        } else {
          $categoryCode='S';
          if ($pitem['product_fpa_pososto']==0) $categoryCode='E'; //E - Exempt from tax
          
        }
          
        
        $aade_katigoria_fpa_ejeresi_code='';$exemptionReasonCode=null;$exemptionReasonText=null;
        
        if (isset($pitem['aade_katigoria_fpa_ejeresi_code']) and trim_gks($pitem['aade_katigoria_fpa_ejeresi_code'])!='') {
          $aade_katigoria_fpa_ejeresi_code=$pitem['aade_katigoria_fpa_ejeresi_code'];
          $exemptionReasonCode=$pitem['fpa_ejeresi_peppol_code'];
          $exemptionReasonText=$pitem['aade_katigoria_fpa_ejeresi_descr'];
        }
        
        $pikey='k'.$pitem['product_fpa_pososto'].'|'.$categoryCode.'|'.$aade_katigoria_fpa_ejeresi_code;
        
        //echo '<pre>'.$pikey.'|'.$pitem['product_price_final_all_net'].'</pre>';
        if (isset($myvatarray[$pikey])==false) {
          $myvatarray[$pikey]=array(
            'categoryCode'=> $categoryCode,
            'categoryRate'=> $pitem['product_fpa_pososto']*100,
            'categoryTaxAmount'=>0,
            'categoryTaxableAmount'=>0,
            'exemptionReasonCode' => $exemptionReasonCode, 
            'exemptionReasonText' => $exemptionReasonText, 
            'invoice'=>null,
          );
        }
      //}
      
      $myvatarray[$pikey]['categoryTaxAmount']+=$pitem['product_price_final_all_fpa'];
      
      
      $myvatarray[$pikey]['categoryTaxableAmount']+=floatval($pitem['product_price_final_all_net']);
                                                   //+floatval($pitem['product_stampDutyAmount'])
                                                   //+floatval($pitem['product_otherTaxesAmount']);
        
      
      
    } 
    foreach ($myvatarray as $value) {
      $adata['vatBreakdowns'][]=$value;
    } 
  //}
  
  //print '<pre>';print_r($myvatarray);die();
  
  
  /*For each different value of VAT category rate (BT-119) 
  where the VAT category code (BT-118)  is Standard rated, 
  the VAT category taxable amount (BT-116) 
  in a VAT breakdown (BG-23) 
  shall equal the sum of Invoice line net amounts (BT-131) 
  plus the sum of document level charge amounts (BT-99) 
  minus the sum of document level allowance amounts (BT-92) 
  where the VAT category code (BT-151, BT-102, BT-95) 
  is “Standard rated” 
  and the VAT rate (BT-152, BT-103, BT-96) 
  equals the VAT category rate (BT-119).*/
  
  //echo '<pre>sssssssss ';print_r($myvatarray);die();
  
  
  if ($myData_XML==false) {
    $ret['message']='vatBreakdowns not set';
		debug_mail(false,$ret['message'],''); return $ret;
  //} else {
  //  $adata['vatBreakdowns']['aadeVatData']=null;
  }
  
  //echo '<pre>11111111111 ';print_r($adata['vatBreakdowns']);die();
  
  
  /*vatPointDateCode (BT-8)
  Cardinality: 0..1
  Type: String
  Example: "432"
  The code of the date when the VAT becomes accountable for the Seller and for the Buyer.
  The code shall distinguish between the following entries of UNTDID 2005 [6]: - Invoice document issue date
  - Delivery date, actual - Paid to dateThe Value added tax point date code is used if the Value added tax point
  date is not known when the invoice is issued.
  Notes:
  The use of BT-8 and BT-7 is mutually exclusive
  https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL2005/
  "vatPointDateCode": "432",
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['vatPointDateCode']=null;
  }
  
  /*
  vatPaidByBuyer (ET-2)
  Cardinality: 1..1
  Type: Boolean
  Example: false
  VAT Reverse Charge.
  VAT will be paid by the Buyer Required in Greece based invoicing by article 9, law 4308/2014.
  

  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['vatPaidByBuyer']=false;
  }
  
  /*
  precedingInvoices (BG-3)
  Cardinality: 0..n
  Type: Collection<PrecedingInvoice>
  Invoice References preceding this Invoice.
  Contains a set of PrecedingInvoice objects B2G - GREECE
  Σε επίπτωση περισσοτέρων του ενός προηγούμενων χρεωστικών τιμολογίων, όλα αυτά τα χρεωστικά
  τιμολόγια πρέπει να έχουν την ίδια Σύμβαση (ΑΔΑΜ, ΒΤ-12) και τον ίδιο προϋπολογισμό (BT-11). Στο
  πιστωτικό τιμολόγιο τα BT-11 και BT-12 είναι κενά, και ως εκ τούτου το λογισμικό του ΚΕ.Δ της ΓΓΠΣ ΔΔ
  ανατρέχει στο πρώτο συσχετιζόμενο χρεωστικό τιμολόγιο, προς ανεύρεσή τους. Συστήνεται, για λόγους
  εύρυθμης διαχείρισης του Η.Τ, ένα πιστωτικό Η.Τ να συσχετίζεται μόνο με ένα προηγούμενο, (ισόποσο)
  χρεωστικό Η.Τ .
  "precedingInvoices": [
     {
       "precedingInvoiceReference": "126573810|03/01/2023|0|1.1|0|13333",
       "precedingInvoiceIssueDate": "2020-10-05T00:00:00.000+0200"
     }
   ],  
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['precedingInvoices']=array();
    
    if ($struct_data['row']['eidos_parastatikou_aade_code']=='5.1') { //Pistotiko Timologio / Syschetizomeno  
      if (isset($struct_data['correlatedInvoices'])) {
        foreach ($struct_data['correlatedInvoices'] as $value) {
          if (isset($value['paroxos_invoice_number']) and isset($value['paroxos_date_send'])) {
            $IssueDate=showDate(strtotime($value['paroxos_date_send']),'Y-m-d',1);
            $IssueDate=date('c', strtotime($IssueDate));
            $adata['precedingInvoices'][]=array(
              'precedingInvoiceReference' => $value['paroxos_invoice_number'],
              'precedingInvoiceIssueDate' => $IssueDate,
            
            );
            
          }
          
        }
        
      }
      //echo '<pre>';print_r($adata['precedingInvoices']);die();
    }
  }
  
  /*creditTransfers (BG-17) 
  Cardinality: 0..n
  Type: Collection<CreditTransfer>
  Credit transfer payments relevant to this Invoice.
  Contains a set of CreditTransfer objects .
  "creditTransfers": [
     {
       "accountName": "George Martakis",
       "accountIdentifier": "BE01234134313"
     }
  ]
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['creditTransfers']=null;
  }

  /*paymentDueDate (BT-9)
  Cardinality: 0..1
  Type: Date
  Example: "2020-10-05T00:00:00.000+0200"
  The payment due date reflects the due date of the net payment.
  For partial payments it states the first net due date. The corresponding description of more complex payment
  terms can be stated in BT-20 Payment terms.
  Notes:
  o The text representation when sending values should be formatted according to ISO 8601
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['paymentDueDate']=null;
  }

  /*invoiceNumber (BT-1)
  Cardinality: 1..1
  Type: String
  Example: "126573810|03/01/2023|0|1.1|0|13333"
  An alphanumeric identifier of this invoice.
  EN 16931-1: The sequential number required in Article 226(2) of the directive 2006/112/EC [2], to uniquely
  identify the Invoice within the business context, time-frame, operating systems and records of the Seller. It
  may be based on one or more series of numbers, which may include alphanumeric characters. No identification
  scheme is to be used.
  NOTE THAT: This value is filled automatically by the system on invoice submission. If it is sent, it will be
  overridden by the system.

  */
  //if ($b2x=='b2b' or $b2x=='b2g') {
  //$adata['invoiceNumber']=$struct_data['row']['inv_guid'];
  //}
  
  
  if ($myData_XML==false) { //to exei sto b2c
    /*paymentMethods (EG-5)
    Cardinality: 0..n
    Type: Collection<PaymentMethod>
    Payment Methods of this Invoice.
    Contains a set of PaymentMethod objects

      type (ET-63)
      Cardinality: 0..1
      Type: Integer
      Example: 2
      AADE payment type code
      Notes:
      o integer values from 1 to 5
      • paymentMethodInfo (ET-66)
      Cardinality: 0..1
      Type: String
      Example: "Direct Debit transfer to an account."
      Payment method info text
      • amount (ET-65)
      Cardinality: 0..1
      Type: BigDecimal
      Example: 33.00
      Payment amount for this payment method
      Notes:
      o decimal with 2 decimal places accuracy    
     "paymentMethods": [
       {
         "type": 2,
         "paymentMethodInfo": "Direct Debit transfer to an account.",
         "amount": 33.00
       }
     ],      
    */
  
    $ret['message']='paymentMethods not set';
		debug_mail(false,$ret['message'],''); return $ret;
  }

  //echo '<pre>ssssssssss ';print_r($xml['paymentMethods']);die();
  
  
  if (1==2) {
    $adata['paymentMethods']=array();
  	//if ($b2x=='b2c' ) {
    	if (isset($xml['paymentMethods']) and count($xml['paymentMethods'])>0) {
    		foreach ($xml['paymentMethods'] as $pm) {
    		  //if ($pm['type']==7) { //6 - Web Banking, 7 - POS / e-POS
      		  $adata['paymentMethods'][]=array(
      		    'type' => $pm['type'],
      		    'paymentMethodInfo' => $pm['paymentMethodInfo'],
      		    'amount' => $pm['amount'],
      		  );
    		  //}
    		  
    		}
    		//echo '<pre>';print_r($paymentMethods);die();
    	}
  
    //}
    if (count($adata['paymentMethods'])==0) unset($adata['paymentMethods']);
  }
  /*invoiceLines (BG-25)
  Cardinality: 1..n
  Type: Collection<InvoiceLine>
  Invoice Lines of this Invoice
  Contains a set of InvoiceLine objects
  
  P A N I K O S
  
  */
  $adata['invoiceLines']=array();
  
  //$tr_aa=0;
  foreach ($struct_data['prow_array'] as $pitem) {
    //$tr_aa++;
    $line=array();

    
    /*• note (BT-127)
    Cardinality: 0..1
    Type: String
    Example: "New Old Stock"*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      if (isset($pitem['product_comments'])) $line['note']=$pitem['product_comments'];
    }
    
    /*• invoicedQuantity (BT-129)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 1
    The quantity of items (goods or services) that is charged in the Invoice line
    Notes:
    o It may be a decimal with arbitrary precision*/
    //if ($b2x=='b2b' or $b2x=='b2g') {
      $line['invoicedQuantity']=$pitem['product_quantity'];
    //}
    
    /*discountAmount (ET-35)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 0
    The discount flat amount of this invoice line
    Notes:
    o May be a decimal with 2 decimal places*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['discountAmount']=0;
    }
    
    /*• invoiceLineCharges (BG-28)
    Cardinality: 0..n
    Type: Collection<InvoiceLineCharge>
    Charges of this invoice line
    Contains a set of InvoiceLineCharge objects
    Οι φόροι και τα τέλη χαρτοσήμου (εκτός ΦΠΑ) που υπολογίζονται σε επιπεδο γραμμής H.T. 

      • baseAmount (BT-142)
      Cardinality: 0..1
      Type: BigDecimal
      Example: 0
      The base amount that may be used, in conjunction with the Invoice line charge percentage, to calculate the
      Invoice line charge amount
      Notes:
      o Decimal with 2 decimal places accuracy
      • amount (BT-141)
      Cardinality: 0..1
      Type: BigDecimal
      Example: 0
      The amount of a charge, without VAT.
      Notes:
      o Decimal with 2 decimal places accuracy
      • reason (BT-144)
      Cardinality: 0..1
      Type: String
      Example: null
      The reason for the Invoice line charge, expressed as text.
      • percentage (BT-143)
      Cardinality: 0..1
      Type: Double
      Example: 0
      The percentage that may be used, in conjunction with the Invoice line charge base amount, to calculate the
      Invoice line charge amount
      Notes:
      o Decimal with 2 decimal places accuracy, min value 0 max value 100
      • reasonCode (BT-145)
      Cardinality: 0..1
      Type: String
      Example: null
      The reason for the Invoice line charge, expressed as a code.
      Use entries of the UNTDID 7161 code list [6]
      https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL7161/
      Notes:
      o The Invoice line   
        
      "invoiceLineCharges": [
        {
          "id": "",
          "baseAmount": 0,
          "amount": 0,
          "percentage": 0
        }
      ],
    */
    
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['invoiceLineCharges']=null;
      
//        if (floatval($pitem['product_stampDutyAmount'])>0) {
//          $line['invoiceLineCharges'][]=array(
//            'amount' => floatval($pitem['product_stampDutyAmount']),
//            'reason' => $pitem['aade_katigoria_xartosimou_descr'],
//            'reasonCode' => $pitem['xartosimou_peppol_code'],
//          );
//        }
//        if (floatval($pitem['product_otherTaxesAmount'])>0) {
//          $line['invoiceLineCharges'][]=array(
//            'amount' => floatval($pitem['product_otherTaxesAmount']),
//            'reason' => $pitem['aade_katigoria_loipon_foron_descr'],
//            'reasonCode' => $pitem['loipon_foron_peppol_code'],
//          );
//        }
      

//      
//      product_feesPercentCategory
//      product_feesAmount      
//      
//      product_otherTaxesPercentCategory
//      product_otherTaxesAmount
      
    }
    /*• lineVatInfo (BG-30)
    Cardinality: 0..1
    Type: LineVatInfo
    Object reference of type LineVatInfo
    A group of business terms providing information about the VAT applicable for the goods and services
    invoiced on the Invoice line 
    
      • vatRate (BT-152)
      Cardinality: 0..1
      Type: Double
      Example: 33.00
      The VAT rate, represented as percentage that applies to the invoiced item.
      Notes:
      o Decimal with 2 decimal places accuracy, min 0 max 100
      • vatAmount (ET-36)
      Cardinality: 0..1
      Type: BigDecimal
      Example: 33.00
      The VAT amount, represented as a value that applies to the invoiced item.
      Notes:
      o Decimal with 2 decimal places accuracy
      • vatCategoryCode (BT-151)
      Cardinality: 0..1
      Type: String
      Example: "S"
      The VAT category code for the invoiced item.
      The following entries of UNTDID 5305 [6] are used (further clarification between brackets): - Standard rate
      (Liable for VAT in a standard way) - Zero rated goods (Liable for VAT with a percentage rate of zero) -
      Exempt from tax (VAT/IGIC/IPSI)- VAT Reverse Charge (Reverse charge VAT/IGIC/IPSI rules apply) -
      VAT exempt for intra community supply of goods (VAT/IGIC/IPSI not levied due to Intra-community
      supply rules) - Free export item, tax not charged (VAT/IGIC/IPSI not levied due to export outside of the
      EU) - Services outside scope of tax (Sale is not subject to VAT/IGIC/IPSI) - Canary Islands General
      Indirect Tax (Liable for IGIC tax) - Liable for IPSI (Ceuta/Melilla tax)
      Το πεδίο είναι υποχρεωτικό, βάσει της 1017/2020 - ΦΕΚ 457/Α/14-2-2020 Μορφότυπος ΑΑΔΕ.
      • aadeVatData (EG-3)
      Cardinality: 0..1
      Type: AadeVatData
      Embeddable entity to hold AADE vat specific fields 
    
     
      "lineVatInfo": {
        "vatCategoryCode": "S",
        "vatAmount": 38.4,
        "vatRate": 24.0,
        "aadeVatData": {
          "aadeVatExemptionCategory": "",
          "aadeVatCategory": 1
        }
      },  
  
    */

    //https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL5305/
    //S - Standard rate Code specifying the standard rate.
    //E - Exempt from tax
    //Z - Zero rated goods Code specifying that the goods are at a zero rate. 
    //O - Services outside scope of tax | Code specifying that taxes are not applicable to the services.
    
       //8 - Εγγραφές χωρίς ΦΠΑ (πχ Μισθοδοσία, Αποσβέσεις)
    if ($struct_data['row']['eidos_parastatikou_aade_code']=='3.1') {
      $vatCategoryCode='O';
      $line['lineVatInfo']=array(
        'vatCategoryCode'=>'O',
      );      
    } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='8.1') {
      $vatCategoryCode='O';
      $line['lineVatInfo']=array(
        'vatCategoryCode'=>'O',
      );      
    } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='8.2') {
      $vatCategoryCode='O';
      $line['lineVatInfo']=array(
        'vatCategoryCode'=>'O',
      );      
    } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='8.4') {
      $vatCategoryCode='O';
      $line['lineVatInfo']=array(
        'vatCategoryCode'=>'O',
      );      
    } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='8.5') {
      $vatCategoryCode='O';
      $line['lineVatInfo']=array(
        'vatCategoryCode'=>'O',
      );  
    } else if ($struct_data['row']['eidos_parastatikou_aade_code']=='9.3') {
      $vatCategoryCode='Z';
      $line['lineVatInfo']=array(
        'vatCategoryCode'=>'O',
      );      
    } else {
      $vatCategoryCode='S';
      if ($pitem['product_fpa_pososto']==0) $vatCategoryCode='E'; //E - Exempt from tax
      
    
//    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['lineVatInfo']=array(
        'vatCategoryCode'=>$vatCategoryCode,
        'vatAmount' => $pitem['product_price_final_all_fpa'],
        'vatRate' => $pitem['product_fpa_pososto']*100,
      );
//    } else if ($b2x=='b2c') {
//      $line['lineVatInfo']=array(
//        //'vatCategoryCode'=>$vatCategoryCode,
//        //'aadeVatData'=> array (
//        //  'aadeVatCategory' => $pitem['xml_vatCategory'].''
//        //),
//        'vatAmount' => floatval($pitem['product_price_final_all_fpa']),
//        
//        
//      );
//      
//    }
    }
    

    /*• itemClassificationIdentifiers (BT-158)
    Cardinality: 0..n
    Type: Collection<ItemClassificationIdentifier>
    A set of types for classifying the item of this invoice line by its type or nature.
    Contains a set of ItemClassificationIdentifier objects
    Κωδικός CPV αγαθού/ υπηρεσίας/μελέτης/ έργου. Ο κανονισμός (EΚ) αριθ. 213/2008 με την ταξινόμηση
    των αγαθών, υπηρεσιών και έργων βάσει του Κοινού Λεξιλογίου για τις δημόσιες συμβάσεις (CPV), είναι
    ανηρτημένος στον ιστότοπο ""simap.ted.europa.eu"" σε όλες τις γλώσσες της Ευρωπαϊκής Ένωσης. 

    BT-158
    A type for classifying the item by its type or nature.
    Classification codes are used to allow grouping of similar items for a various purposes e.g. private
    procurement (CPV), e-Commerce (UNSPSC) etc. 
    
    B2G - Greece
    Κωδικός CPV αγαθού/ υπηρεσίας/μελέτης/ έργου. Ο
    κανονισμός (EΚ) αριθ. 213/2008 με την ταξινόμηση των
    αγαθών, υπηρεσιών και έργων βάσει του Κοινού
    Λεξιλογίου για τις δημόσιες συμβάσεις (CPV), είναι
    ανηρτημένος στον ιστότοπο simap.ted.europa.eu σε όλες
    τις γλώσσες της Ευρωπαϊκής Ένωσης.
    H ανάλυση ανά κωδικό του συστήματος ταξινόμησης
    αγαθών, υπηρεσιών και έργων κατά CPV έχει αναρτηθεί
    και στον ιστοτοπο promitheus.gov.gr σε μορφή αρχείου
    excel στην ενότητα "Γενικές Πληροφορίες" σύνδεσμος
    «Αρχείο Ειδών - Κωδικολόγιο CPV", το πεδίο «CODE».
    
      • classificationIdentifierScheme (BT-158)
      Cardinality: 0..1
      Type: String
      Example: "AM"
      The identification scheme identifier of the Item classification identifier.
      The identification scheme shall be chosen from the entries in UNTDID 7143 [6]
      • classificationIdentifierSchemeVersion (BT-158)
      Cardinality: 0..1
      Type: String
      Example: "D.19A"
      The version of the identification scheme.
      • classificationIdentifier (BT-158)
      Cardinality: 0..1
      Type: String
      Example: "1123"
      A code for classifying the item by its type or nature.
      Classification codes are used to allow grouping of similar items for a various purposes e.g. private
      procurement (CPV), e-Commerce (UNSPSC) etc.
      B2G - Greece
      Κωδικός CPV αγαθού/ υπηρεσίας/μελέτης/ έργου. Ο κανονισμός (EΚ) αριθ. 213/2008 με την ταξινόμηση
      των αγαθών, υπηρεσιών και έργων βάσει του Κοινού Λεξιλογίου για τις δημόσιες συμβάσεις (CPV), είναι
      ανηρτημένος στον ιστότοπο simap.ted.europa.eu σε όλες τις γλώσσες της Ευρωπαϊκής Ένωσης.
      H ανάλυση ανά κωδικό του συστήματος ταξινόμησης αγαθών, υπηρεσιών και έργων κατά CPV έχει
      αναρτηθεί και στον ιστοτοπο promitheus.gov.gr σε μορφή αρχείου excel στην ενότητα "Γενικές
      Πληροφορίες" σύνδεσμος «Αρχείο Ειδών - Κωδικολόγιο CPV", το πεδίο «CODE».    

    "itemClassificationIdentifiers": [
      {
        "id": "",
        "classificationIdentifierScheme": "AM",
        "classificationIdentifierSchemeVersion": "D.19A",
        "classificationIdentifier": "1123"
      }
    ],    

    */
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['itemClassificationIdentifiers']=null;
    }

    /*• objectIdentifier (BT-128)
    Cardinality: 0..1
    Type: String
    Example: "ICPU18523 - Phone replacement main board"
    An identifier for an object on which the invoice line is based, given by the Seller.
    It may be a subscription number, telephone number,meter point etc., as applicable. 

    */
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['objectIdentifier']=null;
    }
    /*• buyerAccountingReference (BT-133)
    Cardinality: 0..1
    Type: String
    Example: "A1.3"
    A textual value that specifies where to book the relevant data into the Buyer's financial accounts.
    If required, this reference shall be provided by the Buyer to the Seller prior to the issuing of the Invoice.
    Notes:
    o For Greek Invoicing: Κωδικός λογιστικής εγγραφής στις χρηματοοικονομικές καταστάσεις
    (ΑΛΕ/ΚΑΕ, ισολογισμός, απολογισμός).*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['buyerAccountingReference']=null;
    }
    
    /*• discountTotalAmount (ET-27)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 0
    The total discount amount of this invoice line
    Notes:
    o May be a decimal with 2 decimal places*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['discountTotalAmount']=0;
    }


    /*• itemAttributes (BG-32)
    Cardinality: 0..n
    Type: Collection<ItemAttribute>
    A set of attribute-value pairs to contain additional attributes of thisinvoice line
    Contains a set of ItemAttribute objects
    Σε κάθε μία γραμμή τιμολογίου δύναται να υπάρξουν περισσότερα του ενός τιμολογηθέντα στοιχεία
    (αγαθά, υπηρεσίες, έργα). Τα χαρακτηριστικά του στοιχείου αφορούν το κάθε στοιχείο της γραμμής
    ξεχωριστά. 
    
      • value (BT-161)
      Cardinality: 0..1
      Type: String
      Example: "10 grams"
      The value of the attribute or property of the item.Such as “Red”.
      • name (BT-160)
      Cardinality: 0..1
      Type: String
      Example: "Weight"
      The name of the attribute or property of the item.Such as “Colour”.
    
    
    "itemAttributes": [
      {
        "id": "",
        "value": "10 grams",
        "name": "Weight"
      }
    ],
    */


    /*• purchaseOrderLineReference (BT-132)
    Cardinality: 0..1
    Type: String
    Example: "PO-123/2020"
    An identifier for a referenced line within a purchase order, issued by the Buyer.
    The purchase order identifier is referenced on document level. */    
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['purchaseOrderLineReference']='';
    }
    
    /*• objectIdentifierScheme (BT-128)
    Cardinality: 0..1
    Type: String
    Example: "ABM"
    The identification scheme identifier of the Invoice line object identifier.
    If it may be not clear for the receiver what scheme is used for the identifier, a conditional scheme identifier
    should be used that shall be chosen from the UNTDID 1153 code list [6]
    https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL1153/
    */
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['objectIdentifierScheme']=null;
    }
    
    /*• priceDetails (BG-29)
    Cardinality: 0..1
    Type: PriceDetails
    Object reference of type PriceDetails
    A group of business terms providing information about the price applied for the goods and services invoiced
    on the Invoice line
    Tα πεδία BT-146, BT-149 και ΒΤ-129 θα πρέπει να ευρίσκονται σε αντιστοιχία. Ακολουθούν ενδεικτικά
    παραδείγματα χρήσης των πεδίων αυτών :
    Παράδειγμα I:
    Τιμολογούνται 10 000 καρφιά. Λόγω της χαμηλής τιμής μονάδας τιμολογούνται ανά 1000 κομμάτια το
    πακέτο. Η τιμή για ένα πακέτο καρφιών είναι 4,50 ευρώ, χωρίς ΦΠΑ.
    Σε αυτή τη περίπτωση 
    BT-129=10.000, BT- 147=0, ΒΤ-148 = 4,50, BT-146 = 4,50,
     BT-149=1.000, BT141=0, BT-136=0, 
     BT-131= ( 4,50 / 1.000 ) x 10.000 + 0 – 0 = 0,0045 x 10.000 = 45 ευρώ.
    Παράδειγμα II: Τιμολογούνται 10 000 βίδες ως 10 πακέτα των 1000 βιδών έκαστο. Λόγω της χαμηλής
    τιμής μονάδας τιμολογούνται ανά 1000 βίδες το πακέτο. Η τιμή για ένα πακέτο βιδών είναι 6,50 ευρώ,
    χωρίς ΦΠΑ.
    Σε αυτή τη περίπτωση BT-129=10 πακέτα, BT-147=0, ΒΤ-148 = 6,50, BT-146 = 6,50, BT-149=1, BT141=0, BT-136=0, BT- 131= ( 6,50 / 1 ) x 10 + 0 – 0 = 6,50 x 10 = 65 ευρώ.
    Παράδειγμα III: Τιμολογούνται 14.800 λίτρα πετρελαίου κίνησης, προς 0,97097 ευρώ το λίτρο. Σε αυτή τη
    περίπτωση BT-129=14.800 λίτρα, BT-147=0, BT-148=0,97097 ευρώ, BT-146=0,97097 ευρώ το λίτρο, 
    BT149=1, BT- 141=0, BT-136=0, 
    BT-131= (0,97097 / 1) x 14.800 = 14370, 356 → PEPPOLRound(14370,356 ) = 14.370,36 ευρώ. 
    
    
      • itemPriceBaseQuantity (BT-149)
      Cardinality: 0..1
      Type: BigDecimal
      Example: 1
      The number of item units to which the price applies.
      • itemGrossPrice (BT-148)
      Cardinality: 0..1
      Type: BigDecimal
      Example: 133.00
      The unit price, exclusive of VAT, before subtracting Item price discount
      Notes:
      o Decimal with 2 decimal places accuracy
      • itemNetPrice (BT-146)
      Cardinality: 0..1
      Type: BigDecimal
      Example: 100.00
      The price of an item, exclusive of VAT, after subtracting item price discount.
      The Item net price has to be equal with the Item gross price less the Item price discount
      Notes:
      o Decimal with 2 decimal places accuracy
      • itemPriceBaseQuantityUnitsCode (BT-150)
      Cardinality: 0..1
      Type: String
      Example: "H87"
      The unit of measure that applies to the Item price base quantity.
      • itemPriceDiscount (BT-147)
      Cardinality: 0..1
      Type: BigDecimal
      Example: 0
      The total discount subtracted from the Item gross price to calculate the Item net price.
      Only applies if the discount is provided per unit and if it is not included in the Item gross price.
      Notes:
      o Decimal with 2 decimal places accuracy
    
    
    "priceDetails": {
      "itemPriceBaseQuantity": 1,
      "itemGrossPrice": 133.00,
      "itemNetPrice": 100.00,
      "itemPriceBaseQuantityUnitsCode": "H87",
      "itemPriceDiscount": 0
    },
    */
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['priceDetails']=array();
      $line['priceDetails']['itemNetPrice']=$pitem['product_price_final_peritem_net'];
      $line['priceDetails']['itemGrossPrice']=null; //$pitem['product_price_final_peritem_total'];
      $line['priceDetails']['itemPriceDiscount']=0;
      $line['priceDetails']['itemPriceBaseQuantity']=1;//panta 1. poia einai i timi tou enos temaxiou //$pitem['product_quantity'];
      $line['priceDetails']['itemPriceBaseQuantityUnitsCode']=$pitem['monada_peppol_code'];
    } else if ($b2x=='b2c') {
      $line['priceDetails']=array();
      $line['priceDetails']['itemNetPrice']=$pitem['product_price_final_peritem_net'];
      
    }
    

    /*• discountPercentage1 (ET-32)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 0
    The first discount percentage of this invoice line
    Notes:
    o May be a decimal with 2 decimal places*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['discountPercentage1']=0;    
    }

    /*• discountPercentage2 (ET-33)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 0
    The second discount percentage of this invoice line
    Notes:
    o May be a decimal with 2 decimal places*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['discountPercentage2']=0;    
    }
    
    /*• discountPercentage3 (ET-34)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 0
    The third discount percentage of this invoice line
    Notes:
    o May be a decimal with 2 decimal places*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['discountPercentage3']=0;    
    }
    
    /*• invoiceLinePeriod (BG-26)
    Cardinality: 0..1
    Type: InvoiceLinePeriod
    Object reference of type InvoiceLinePeriod A group of business terms providing information about the
    period relevant for the Invoice line.
    Is also called Invoice line delivery period. 
    
    "invoiceLinePeriod": {
      "endDate": "2020-10-05T00:00:00.000+0200",
      "startDate": "2020-10-05T00:00:00.000+0200"
    },
    */
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['invoiceLinePeriod']=null;   
    }
    
    /*• netAmount (BT-131)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 100.00
    The total amount of the Invoice line.
    The amount is “net” without VAT, i.e. inclusive of line level allowances and charges as well as other
    relevant taxes. */
    $line['netAmount']=floatval($pitem['product_price_final_all_net']);
                      //+floatval($pitem['product_stampDutyAmount'])
                      //+floatval($pitem['product_otherTaxesAmount']);   
    
    /*• invoicedQuantityUnits (BT-130)
    Cardinality: 0..1
    Type: String
    Example: "NPT"
    The unit of measure that applies to the invoiced quantity.
    The unit of measure shall be chosen from the lists in UN/ECE Recommendation N°. 20 Codes for Units of
    Measure Used in International Trade [7] and UN/ECE Recommendation N° 21 Codes for Passengers, Types
    of Cargo, Packages and Packaging Materials (with Complementary Codes for Package Names) [19],
    applying the method described in UN/ECE Rec N° 20 Intro 2.a. Note that in most cases it is not needed for
    Buyers and Sellers to implement these lists fully in their software. Sellers need only to support the units
    needed for their goods and services; Buyers only need to verify that the units used in the Invoice are equal to
    the units used in other documents (such as Contract, Catalogue, Order and Despatch advice). 
    https://docs.peppol.eu/poacc/billing/3.0/codelist/UNECERec20/
    https://docs.peppol.eu/poacc/billing/3.0/codelist/UNECERec21/
    */
    

    if ($b2x=='b2b' or $b2x=='b2g') {
      if (isset($pitem['monada_peppol_code']) and trim_gks($pitem['monada_peppol_code'])!='') {
        $line['invoicedQuantityUnits']=trim_gks($pitem['monada_peppol_code']);
      } else {
        $ret['message']=gks_lang('Η μονάδα μέτρησης <b>[1]</b> δεν έχει κωδικό Peppol');
        $ret['message']=str_replace('[1]',$pitem['monada_descr'],$ret['message']);
  		  debug_mail(false,$ret['message'],''); return $ret;
      }
    }
    
    
    /*• lineNumber (BT-126, ET-37)
    Cardinality: 0..1
    Type: Integer
    Example: 1
    The line number of this line item
    Notes:
    o should be equal or greater than 1*/

    $line['lineNumber']=intval($pitem['product_aa']);
    
    /*• itemInfo (BG-31)
    Cardinality: 0..1
    Type: ItemInfo
    Object reference of type ItemInfo
    A group of business terms providing information about the goods and services invoiced. 

      • itemInfoName (BT-153)
      Cardinality: 0..1
      Type: String
      Example: "Repair part"
      A name for an item
      • standardIdentifier (BT-157)
      Cardinality: 0..1
      Type: String
      Example: null
      An item identifier based on a registered scheme.
      • itemInfoDescription (BT-154)
      Cardinality: 0..1
      Type: String
      Example: "Main board replacement"
      A description for an item.
      The Item description allows for describing the item and its features in more detail than the Item name
      • buyerIdentifier (BT-156)
      Cardinality: 0..1
      Type: String
      Example: null
      An identifier, assigned by the Buyer, for the item
      • standardIdentifierScheme (BT-157)
      Cardinality: 0..1
      Type: String
      Example: null
      The identification scheme identifier of the Item standard identifier.
      The identification scheme shall be identified from the entries of the list published by the ISO/IEC6523
      maintenance agency
      • countryOfOrigin (BT-159)
      Cardinality: 0..1
      Type: String
      Example: "CN"
      The code identifying the country from which the item originates.
      The lists of valid countries are registered with the EN-ISO3166-1 Maintenance agency, Codes for the
      representation of names of countries and their subdivisions.
      • sellerIdentifier (BT-155)
      Cardinality: 0..1
      Type: String
      Example: "FPC-0912"
      An identifier, assigned by the Seller, for the item

    "itemInfo": {
      "itemInfoName": "Repair part",
      "itemInfoDescription": "Main board replacement",
      "buyerIdentifier" : null,
      "standardIdentifier" : null,
      "standardIdentifierScheme" : null,
      "countryOfOrigin": "CN",
      "sellerIdentifier": "FPC-0912"
    },
    */
    if ($b2x=='b2b' or $b2x=='b2g') {
      $line['itemInfo']=array();
      $line['itemInfo']['itemInfoName']=$pitem['product_descr'];
      $line['itemInfo']['itemInfoDescription']=$pitem['product_descr']; //$pitem['product_comments'];
      $line['itemInfo']['buyerIdentifier']=null;
      $line['itemInfo']['standardIdentifier']=null;
      $line['itemInfo']['standardIdentifierScheme']=null;
      $line['itemInfo']['countryOfOrigin']=null;
      $line['itemInfo']['sellerIdentifier']=$pitem['product_code'];
    }
  
        
    /*• invoiceLineAllowances (BG-27)
    Cardinality: 0..n
    Type: Collection<InvoiceLineAllowance>
    Allowances of this invoice line
    Contains a set of InvoiceLineAllowance objects
    Για Παρακρατήσεις και Κρατήσεις Υπερ Τρίτων σε επίπεδο γραμμής Η.Τ, δεν συμπληρώνονται τα πεδία
    BG-27, αλλά οι συνολικές ανά κατηγορία κρατήσεις/παρακρατήσεις σε επίπεδο παραστατικού
    συμπληρώνονται στα πεδία στα πεδία στο BG-24.
    */
    
    
    
    $adata['invoiceLines'][]=$line;
  }
  
  //echo '<pre>sssssssss ssssss ';print_r($adata['invoiceLines']);die();
  
  
  if ($myData_XML==false) {
    $ret['message']='invoiceLines not set';
		debug_mail(false,$ret['message'],''); return $ret;
  }

  /*sellerTaxRepresentative (BG-11)
  Cardinality: 0..1
  Type: SellerTaxRepresentative
  Object reference of type SellerTaxRepresentative
  A group of business terms providing information about the Seller's tax representative
  B2G - GREECE
  Σε περίπτωση που ο Πωλητής – Προμηθευτής του Ελληνικού Δημοσίου με έδρα εκτός Ελλάδας έχει
  υποχρέωση ορισμού Φορολογικού Αντιπροσώπου, βάσει των ισχυουσών φορολογικών διατάξεων, 
  συμπληρώνεται η ομάδα πεδίων BG-11 (TAX REPRESENTATIVE) με τα στοιχεία του Φορολογικού
  Αντιπροσώπου, όπως όνομα, ΑΦΜ 
  
  "sellerTaxRepresentative": {
    "sellerTaxRepresentativeName": "Akis Chourdakis",
    "sellerTaxRepresentativePostalAddress": {
      "sellerTaxRepresentativeCountrySubdivision": "Attiki",
      "sellerTaxRepresentativeCity": "Lagonisi",
      "sellerTaxRepresentativePostCode": "19010",
      "sellerTaxRepresentativeCountryCode": "GR",
      "sellerTaxRepresentativeAddressLine1": "karaiskaki 11",
      "sellerTaxRepresentativeAddressLine2": "",
      "sellerTaxRepresentativeAddressLine3": ""
    },
    "sellerTaxRepresentativeVatIdentifier": "EL126472807"
  },  
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['sellerTaxRepresentative']=null;
  }

  /*purchaseOrderReference (BT-13)
  Cardinality: 0..1
  Type: String
  Example: "A/1333/2020-10-10"
  An identifier of a referenced purchase order, issued by the Buyer
  B2G - GREECE
  Αναγνωριστικό της αντίστοιχης Εντολής Αγοράς, σε επίπεδο παραστατικού, εάν υπάρχει ΑΔΑΜ. 

  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['purchaseOrderReference']='';
  }
  
  /*schemeIdentifier (BT-18)
  Cardinality: 0..1
  Type: String
  Example: "AWV" or VAT 
  The identification scheme identifier of the Invoiced object identifier.
  If it may be not clear for the receiver what scheme is used for the identifier, a conditional scheme identifier
  should be used that shall be chosen from the UNTDID 1153 code list [6] entries. 
  https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL1153/
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['schemeIdentifier']='';
  }

  /*
  buyerReference (BT-10)
  Cardinality: 0..1
  Type: String
  Example: "Νοσοκ ΕΥΑΓΓΕΛΙΣΜΟΣ|XYZ123ABC"
  An identifier assigned by the Buyer used for internal routing purposes
  The identifier is defined by the Buyer (e.g. contact ID, department, office id, project code), but provided by
  the Seller in the Invoice.
  B2G - GREECE
  Το αναγνωριστικό αυτό ορίζεται από τον Αγοραστή (BT-44) ως μια περιγραφή-όνομα της Αναθέτουσας
  Αρχής (σύμφωνα με την υφιστάμενη ΚΥ ΜΟΡΦΟΤΥΠΟΥ) , αλλά παρέχεται στο Η.Τ από τον Πωλητή και
  αφορά στη περιγραφή οργανικής μονάδας στη διοικητική δομή του Αγοραστή (π.χ Νοσοκομείο
  ΕΥΑΓΓΕΛΙΣΜΟΣ, VII ΜΟΝΑΔΑ ΣΤΡΑΤΟΥ, ΔΟΥ Ιωαννίνων, Πανεπιστήμιο Αιγαίου) , η οποία έχει
  οριστεί ως Αναθέτουσα αρχή και είναι ο λογιστικά υπεύθυνος για τη διαχείριση του Η.Τ. Στην παραπάνω
  περιγραφή, μη υποχρεωτικά και συμπληρωματικά παρέχεται (εάν υφίσταται) και μια περιγραφή εσωτερικής,
  διοικητικής μονάδας της Αναθέτουσας Αρχής, στην οποία καταλήγει το Η.Τ, (π.χ ΦΑΡΜΑΚΕΙΟ,
  ΔΙΑΧΕΙΡΙΣΗ ΥΓΕΙΟΝΟΜΙΚΟΥ ΥΛΙΚΟΥ, ΤΕΧΝΙΚΗ ΥΠΗΡΕΣΙΑ, Στρατιωτικό Πρατήριο
  Αλεξανδρούπολης, κ.λ.π) , και η οποία δεν αποτελεί από μόνη της Α.Α, ή εναλλακτικά ενός αλφαριθμητικου
  ΚΩΔΙΚΟΥ. Στην περίπτωση που o Αγοραστής/ΦΟΡΕΑΣ στο BT-44 (υποχρεωτικό πεδίο) και η Α.Α στο BT10 (προαιρετικό πεδίο) 
  είναι διαφορετικές οντότητες, το BT-10 δύναται να πάρει μια από τις εξής
  εναλλακτικές μορφές:
  • BT-10 :: Περιγραφή Α.Α
  -ή-
  • BT-10 :: Περιγραφή Α.Α“|”Περιγραφή Εσωτερ. Μονάδας της Α.Α Παραλαβής του Η.Τ
  -ή-
  • BT-10 :: Περιγραφή Α.Α“|”ΚΩΔΙΚΟΣ Α.Α ή ΚΩΔΙΚΟΣ Εσωτερ. Μονάδας της Α.Α Παραλαβής Η.Τ
  • BT-44 :: Περιγραφή Αγοραστή / Φορέα 
  Στην περίπτωση που o Αγοραστής/ΦΟΡΕΑΣ στο BT-44 (υποχρεωτικό πεδίο) και η Α.Α στο BT-10
  (προαιρετικό πεδίο) είναι ταυτόσημες οντότητες, το BT-10 δύναται να πάρει μια από τις εξής εναλλακτικές
  μορφές:
  • BT-10 :: μπορεί να μην υφίσταται στο XML του Η.Τ, ως μη υποχρεωτικό
  -ή-
  • BT-10 :: Περιγραφή Εσωτερ Μονάδας της Α.Α Παραλαβής του Η.Τ
  -η-
  • BT-10 :: Περιγραφή Εσωτερ Μονάδας της Α.Α Παραλαβής του Η.Τ “|” ΚΩΔΙΚΟΣ Α.Α ή ΚΩΔΙΚΟΣ
  Εσωτερ. Μονάδας της Α.Α Παραλαβής Η.Τ
  • BT-44 :: Περιγραφή Α.Α, ως Φορέας/Αγοραστής
  Η ανωτέρω κωδικοποίηση της εσωτερικής, οργανικής μονάδας του Αγοραστή/Φορέας (ο οποίος Αγοραστής
  ως Νομικό Πρόσωπο έχει δικό του ΑΦΜ και περιγράφεται στα πεδία ΒΤ-44 και ΒΤ-48, π.χ Α.Α.Δ.Ε,
  Νοσοκομείο ΕΥΑΓΓΕΛΙΣΜΟΣ, Υπουργείο Μετανάστευσης, ΥΕΘΑ, Πανεπιστήμιο Αιγαίου), θα επιλέγεται
  από τον εκδότη του Η.Τ, κατόπιν συνεννοήσεως με τον Αγοραστή. 
  
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['buyerReference']='';
    if ($b2x=='b2g') {

      if (isset($struct_data['row']['b2g_inv_aaht_name']) and trim_gks($struct_data['row']['b2g_inv_aaht_name']!='')) { 
        $adata['buyerReference']=trim_gks($struct_data['row']['b2g_inv_aaht_name']); 
      }
          
      //if (isset($struct_data['row']['b2g_aaht_name']) and trim_gks($struct_data['row']['b2g_aaht_name']!='')) { 
      //  $adata['buyerReference']=trim_gks($struct_data['row']['b2g_aaht_name']);
      //}
    }
  }

  /*invoiceIssueDate (BT-2)
  Cardinality: 1..1
  Type: Date
  Example: "2020-10-05T00:00:00.000+0200"
  The date when the Invoice was issued
  Notes:
  o if BT-1 is null, it will be constructed using this number
  o The text representation when sending values should be formatted according to ISO 8601
  
  */
  $adata['invoiceIssueDate']=$xml['invoiceHeader']['issueDate_iso_8601'];

  //delete me
  $adata['invoiceIssueDate']=$xml['invoiceHeader']['issueDate_iso_8601_r'];
  
  /*paymentTerms (BT-20)
  Cardinality: 0..1
  Type: String
  Example: "Paid in full in advance."
  A textual description of the payment terms that apply to the amount due for payment(Including description of
  possible penalties).
  This element may contain multiple lines and multiple terms. 
  
  */
	if ($b2x=='b2b' or $b2x=='b2g') {
  	$paymentTerms=[];
  	if (isset($xml['paymentMethods']) and count($xml['paymentMethods'])>0) {
  		foreach ($xml['paymentMethods'] as $pm) {
  		  $paymentTerms[]=$pm['paymentMethodInfo'];
  		}
  		//echo '<pre>';print_r($paymentMethods);die();
  	}
    $adata['paymentTerms']=implode(', ',$paymentTerms);
  }
  
  /*processControl (BG-2)
  Cardinality: 0..1
  Type: ProcessControl
  Object reference of type ProcessControl
  A group of business terms providing information on the business process and rules applicable to the Invoice
  document 

  "processControl": {
    "specificationIdentifier":"urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0",
   "businessProcessType": "urn:fdc:peppol.eu:2017:poacc:billing:01:1.0 "
  },
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['processControl']=null;
  }
  
  /*docLevelCharges (BG-21)
  Cardinality: 0..n
  Type: Collection<DocLevelCharge>
  Document Level Charges of this Invoice
  Contains a set of DocLevelCharge objects 
  B2G - GREECE
  Οι λοιποί φόροι, τα τέλη και τα τέλη χαρτοσήμου (εκτός ΦΠΑ) που προσαυξάνουν την αξία του
  Ηλεκτρονικού Τιμολογίου και υπολογίζονται σε επίπεδο παραστατικού (έγγραφου) συμπληρώνονται στην
  ομάδα πεδίων BG-21 .
  Οι Μειώσεις Τιμής (allowances) είναι συνήθως κάποια μορφή έκπτωσης (discount), ενώ οι Επιβαρύνσεις
  (Charges) θα ήταν συνήθως μια μορφή υπηρεσίας η οποία παρέχεται από τον Πωλητή. Βασικά, οι Μειώσεις
  Τιμής λειτουργούν αφαιρετικά από το σύνολο του τιμολογίου και oι επιβαρύνσεις είναι προσθήκες στο
  Σύνολο Τιμολογίου. Μειώσεις Τιμής και Επιβαρύνσεις μπορούν να προκύψουν για το Παραστατικό ως
  σύνολο ή να ισχύουν για μεμονωμένα στοιχεία γραμμής ή και τα δύο 

    • vatCategoryCode (BT-102)
    Cardinality: 0..1
    Type: String
    Example: "S"
    A coded identification of what VAT category applies to the document level charge.
    The following entries of UNTDID 5305 [6] are used (further clarification between brackets): 
    - Standard rate  (Liable for VAT in a standard way) 
    - Zero rated goods (Liable for VAT with a percentage rate of zero) 
    - Exempt from tax (VAT/IGIC/IPSI)- VAT Reverse Charge (Reverse charge VAT/IGIC/IPSI rules apply) 
    - VAT exempt for intra community supply of goods (VAT/IGIC/IPSI not levied due to Intra-community supply rules) 
    - Free export item, tax not charged (VAT/IGIC/IPSI not levied due to export outside of the EU) 
    - Services outside scope of tax (Sale is not subject to VAT/IGIC/IPSI) 
    - Canary Islands General Indirect Tax (Liable for IGIC tax) 
    - Liable for IPSI (Ceuta/Melilla tax) 
    https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL5305/
    
    • chargeAmount (BT-99)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 10.00
    The amount of a charge, without VAT.
    Notes:
    o Decimal with 2 decimal places accuracy
    • chargeReason (BT-104)
    Cardinality: 0..1
    Type: String
    Example: "Copyright fee collection"
    The reason for the document level charge, expressed as text.
    • chargeBaseAmount (BT-100)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 100.00
    The base amount that may be used, in conjunction with the document level charge percentage,to calculate
    the document level charge amount
    Notes:
    o Decimal with 2 decimal places accuracy
    • aadeTaxData (EG-2)
    Cardinality: 0..1
    Type: AadeTaxData
    Embeddable entity to hold AADE tax type specific fields
    • vatRate (BT-103)
    Cardinality: 0..1
    Type: Double
    Example: 0
    The VAT rate, represented as percentage that applies to the document level charge.
    Notes:
    o Decimal with 2 decimal places accuracy. Min 0 max 100
    • chargeReasonCode (BT-105)
    Cardinality: 0..1
    Type: String
    Example: "AEP"
    The reason for the document level charge, expressed as a code.
    Use entries of the UNTDID 7161 code list [6]
    Notes:
    o The Document level charge reason code and the Document level charge reason shall indicate the
    same charge reason.
    • chargePercentage (BT-101)
    Cardinality: 0..1
    Type: Double
    Example: 10.00
    The percentage that may be used, in conjunction with the document level charge base amount,to calculate
    the document level charge amount.
    Notes:
    o Decimal with 2 decimal places accuracy. Min 0 max 100  
    
  "docLevelCharges": [
    {
    "vatCategoryCode": "S",
    "chargeAmount": 10.00,
    "chargeReason": "Copyright fee collection",
    "chargeBaseAmount": 100.00,
    "aadeTaxData": {
      "aadeTaxType": 3,
      "aadeTaxCategory": 3
    },
    "vatRate": 0,
    "chargeReasonCode": "AEP",
    "chargePercentage": 10.00
    }
  ],
   
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['docLevelCharges']=array();

    $docLevelCharges=array();
    foreach ($struct_data['prow_array'] as $pitem) {
      
      if (floatval($pitem['product_stampDutyAmount'])>0) {
        $pikey='stamp|'.$pitem['aade_katigoria_xartosimou_code'];
        if (isset($docLevelCharges[$pikey])==false) {
          $docLevelCharges[$pikey]=array(
            'vatCategoryCode' => 'S',
            'chargeAmount' => 0,
            'chargeReason' => $pitem['aade_katigoria_xartosimou_descr'],
            'chargeBaseAmount' => 0,
            'chargeReasonCode' => $pitem['xartosimou_peppol_code'],
          );
          
        }
        $docLevelCharges[$pikey]['chargeAmount']+=$pitem['product_stampDutyAmount'];
        $docLevelCharges[$pikey]['chargeBaseAmount']+=$pitem['product_price_final_all_net'];
      }

      if (floatval($pitem['product_otherTaxesAmount'])>0) {
        $pikey='other|'.$pitem['aade_katigoria_loipon_foron_code'];
        if (isset($docLevelCharges[$pikey])==false) {
          $docLevelCharges[$pikey]=array(
            'vatCategoryCode'=>'S',
            'chargeAmount' => 0,
            'chargeReason' => $pitem['aade_katigoria_loipon_foron_descr'],
            'chargeBaseAmount' => 0,
            'chargeReasonCode' => $pitem['loipon_foron_peppol_code'],
            
          );
          
        }
        $docLevelCharges[$pikey]['chargeAmount']+=$pitem['product_otherTaxesAmount'];
        $docLevelCharges[$pikey]['chargeBaseAmount']+=$pitem['product_price_final_all_net'];
      }
      if (floatval($pitem['product_feesAmount'])>0) {
        $pikey='telon|'.$pitem['aade_katigoria_telon_code'];
        if (isset($docLevelCharges[$pikey])==false) {
          $docLevelCharges[$pikey]=array(
            'vatCategoryCode'=>'S',
            'chargeAmount' => 0,
            'chargeReason' => $pitem['aade_katigoria_telon_descr'],
            'chargeBaseAmount' => 0,
            'chargeReasonCode' => $pitem['telon_peppol_code'],
            
          );
          
        }
        $docLevelCharges[$pikey]['chargeAmount']+=$pitem['product_feesAmount'];
        $docLevelCharges[$pikey]['chargeBaseAmount']+=$pitem['product_price_final_all_net'];
      }      
      
      
      
    }
    
    //echo '<pre>';print_r($docLevelCharges) ; die();
    foreach ($docLevelCharges as $value) {
      $value['vatRate']=($value['chargeAmount']*100)/$value['chargeBaseAmount'];
      $value['chargePercentage']=($value['chargeAmount']*100)/$value['chargeBaseAmount'];
      $adata['docLevelCharges'][]=$value;
    } 
    
    
    //echo '<pre>';print_r($adata['docLevelCharges']) ; die();
    
  }
  
  
  
  if ($myData_XML==false) {
    
    $ret['message']='docLevelCharges not set';
		debug_mail(false,$ret['message'],''); return $ret;

  
  }
  
  
  /*invoiceCurrencyCode (BT-5, ET-4)
  Cardinality: 1..1
  Type: String
  Example: "EUR"
  The currency in which all Invoice amounts are given, except for the Total VAT amount in accounting
  currency.
  Only one currency shall be used in the Invoice, except for the Invoice total VAT amount in accounting
  currency (BT-111) in accordance with article 230 of Directive 2006/112/EC on VAT [2]. The lists of valid
  currencies are registered with the ISO 4217 Maintenance Agency Codes for the representation of currencies
  and funds.
  Μόνο ένα νόμισμα θα χρησιμοποιείται στο Η.Τ (BT-110), εκτός από αυτό για το Ποσόν Συνολικού ΦΠΑ
  Τιμολογίου (BT-111) που θα πρέπει να είναι στο νόμισμα λογιστικής) του κράτους μέλους της EC , σύμφωνα
  με την Οδηγία 2006/112/EC Αρ. 230. Οι λίστες των έγκυρων νομισμάτων είναι καταγεγραμμένες σύμφωνα
  με το πρότυπο ISO 4217 
  https://docs.peppol.eu/poacc/billing/3.0/codelist/ISO4217/
  */
  $adata['invoiceCurrencyCode']='EUR';
  
  /*paymentInstruction (BG-16)
  Cardinality: 0..1
  Type: PaymentInstruction
  Object reference of type PaymentInstruction
  A group of business terms providing information about the payment
  
  "paymentInstruction": {
    "paymentCardInfo": {
      "primaryAccountNumber": "333333***********1111",
      "holderName": "George Martakis"
    },
    "directDebit": {
      "mandateReferenceIdentifier": "333A/333B",
      "bankAssignedCreditorIdentifier": "GMARTAKIS-12341132",
      "debitedAccountIdentifier": "BIC1124231421"
    },
    "paymentMeansTypeCode": "",
    "remittanceInfo": "George Martakis JPhone repair payment",
    "paymentMeansText": "direct debit"
  },

  
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['paymentInstruction']=null;
  }
  
  
  /*mark (ET-6)
  Cardinality: 1..1
  Type: String
  Example: "33333333"
  The MARK number
  The MARK returned from AADE myData API
  Notes:
  o READ ONLY field
  
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['mark']=null;
  }
  
  /*invoicedObjectIdentifier (BT-18)
  Cardinality: 0..1
  Type: String
  Example: "130"
  See also: 
  https://docs.peppol.eu/poacc/billing/3.0/syntax/ubl-invoice/cac-AdditionalDocumentReference/cbc-DocumentTypeCode/
  An identifier for an object on which the invoice is based, given by the Seller
  It may be a subscription number, telephone number, meter point, vehicle, person etc., as applicable.
  PEPPOL: Code "130" MUST be used to indicate an invoice object reference. Not used for other additional
  documents Code "ATS" MUST be used to indicate a credit note object reference. Not used for other additional
  documents
  B2G - GREECE
  Στη περίπτωση Μη Συσχετιζόμενου Πιστωτικου Τιμολογίου με ΕΙΔΟΣ ΠΑΡΑΣΤΑΤΙΚΟΥ στο BT-1 την
  τιμή 5.2, το πεδίο BT-18 λαμβάνει υποχρεωτικά τη τιμή 50
  Σε αυτή τη περίπτωση υφίσταται ένα επιπλέον Υποστηρικτικό Έγγραφο BG-24, τύπου
  «PROJECT|REFERENCE”. Σε όλους τους άλλους τύπους παραστατικών Η.Τ, το πεδίο BT-18 δεν υφίσταται. 
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['invoicedObjectIdentifier']=null;
  }
  
  /*buyerIdentifiers (BT-46)
  Cardinality: 0..n
  Type: Collection<BuyerIdentifier>
  Buyer identifiers of this Invoice
  B2G - GREECE
  Ο Κωδικός (Label) Αναθέτουσας Αρχής / Αναθέτοντος Φορέα για το Ελληνικό Δημόσιο, είναι η από το νόμο
  ορισθείσα Αναθέτουσα Αρχή (Α.Α) που λαμβάνει και διαχειρίζεται το Η.Τ,όπως αυτός προσδιορίζεται στο
  «ΜΗΤΡΩΟ ΑΝΑΘΕΤΟΥΣΩΝ ΑΡΧΩΝ» για την ηλεκτρονική τιμολόγηση της ΓΓΠΣ ΔΔ, με το πεδίο
  «ΚΩΔΙΚΟΣ ΑΝΑΘΕΤΟΥΣΑΣ» (π.χ 2048.8010430600.00061) στην ιστοσελίδα του επίσημου ιστότοπου της
  ΓΓΠΣ ΔΔ www.gsis.gr/e-Invoice . Αφορά μια αυτοτελή διοικητική δομή/μονάδα, ( όπως Υπουργείο Ψηφ.
  Διακυβέρνησης, ΔΟΥ Ιωαννίνων, Πανεπιστήμιο Αιγαίου) του Αγοραστή (όπως αυτός περιγράφεται στο
  πεδίο BT-44), η οποία όμως υπάγεται στον Αγοραστή (ΒΤ-44) ή μπορεί να είναι ή ίδια Αγοραστής.
  
      
    • buyerIdentifier (BT-46)
    Cardinality: 0..1
    Type: String
    Example: "2048.8010430600.00061"
    An identifier of the Buyer.If no scheme is specified,it should be known by Buyer and Seller, e.g. a
    previouslyexchanged Seller assigned identifier of the Buyer
    B2G - GREECE
    Το πεδίο BT-46 αντιστοιχεί στο πεδίο BT-10 και αναφέρονται στην ίδια αυτοτελή διοικητική οντότητα. Στο
    πεδίο ΒΤ-46 παρέχεται ο κωδικός (label) της Α.Α, ενώ το ΒΤ-44 (Περιγραφή Αγοραστή) αντιστοιχεί στο
    πεδίο ΒΤ-48 το οποίο περιέχει τον ΑΦΜ του Αγοραστή. Στην ελληνική Διοίκηση υπάρχουν δομές που είναι
    και Αγοραστές και Α.Α (βλέπε Νοσοκομείο ΕΥΑΓΓΕΛΙΣΜΟΣ, Παν/μιο Αιγαίου, κλπ), και υπάρχουν
    δομές όπου ο Αγοραστής είναι σε διαφορετικό (υπερκείμενο) επίπεδο από αυτό της Α.Α, στον οποίο
    Αγοραστή αυτή υπάγεται (Βλέπε ΑΑΔΕ, ΔΟΥ ...).
    • buyerSchemeIdentifier (BT-46)
    Cardinality: 0..1
    Type: String
    Example: null
    The identification scheme identifier of the Seller identifier.
    If used, the identification scheme identifier shall be chosen from the entries of the list published by the
    ISO/IEC6523 agency   
    https://docs.peppol.eu/poacc/billing/3.0/codelist/ICD/
  
   
  "buyerIdentifiers": [
    {
      "buyerIdentifier": "2048.8010430600.00061",
      "buyerSchemeIdentifier": 
    }
  ],  
  */
  
  if ($b2x=='b2b' or $b2x=='b2g') {
     $adata['buyerIdentifiers']=array();
   if ($b2x=='b2g') {
    
    
      //if (isset($struct_data['row']['b2g_aaht_code']) and trim_gks($struct_data['row']['b2g_aaht_code']!='')) { 
      if (isset($struct_data['row']['b2g_inv_aaht_code']) and trim_gks($struct_data['row']['b2g_inv_aaht_code']!='')) { 
        $adata['buyerIdentifiers'][]=array(
          'buyerIdentifier' => trim_gks($struct_data['row']['b2g_inv_aaht_code']),
          //'buyerSchemeIdentifier' => null, //'0084', //'9933:997001671', //'0084', //'9933:997001671',
        );
      } else {
        if (isset($struct_data['row']['b2g_aaht_code']) and trim_gks($struct_data['row']['b2g_aaht_code']!='')) { 
          $adata['buyerIdentifiers'][]=array(
            'buyerIdentifier' => trim_gks($struct_data['row']['b2g_aaht_code']),
            //'buyerSchemeIdentifier' => null, //'0084', //'9933:997001671', //'0084', //'9933:997001671',
          );
        }   
      }
    }
  }
  
  

  
  /*tenderOrLotReference (BT-17)
  Cardinality: 0..1
  Type: String
  Example: "LOT A1"
  The identification of the call for tender or lot the invoice relates to.
  In some countries a reference to the call for tender that has led to the contract shall be provided.
  B2G - GREECE
  Αναφορά σε Διακήρυξη Διαγωνισμου (RFP) η οποία κατέληξε σε σύμβαση ή σε παρτίδα τμηματικής
  παράδοσης. 
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['tenderOrLotReference']=null;
  }
  
  if ($myData_XML==false) {
    /*aadeData (BG-1)
    Cardinality: 0..1
    Type: AadeData
    Embeddable entity to hold AADE specific fields, such as the Unique Identifierand other required fields, as
    specified in AADE myDataProvider API
    https://www.aade.gr/tehnikes-prodiagrafes-parohoi
    
    */
    $ret['message']='aadeData not set';
		debug_mail(false,$ret['message'],''); return $ret;

  
  }
  
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['aadeData']=null;
  }
  
  
  /*b2g (IT-2)
  Cardinality: 0..1
  Type: Boolean
  Example: false
  Indicate a B2G invoice
  Used internally to identify whether this invoice is meant for a government recipient.
  */
  
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['b2g']=$b2x=='b2g';
  }
  
  /*buyer (BG-9)
  Cardinality: 0..1
  Type: Buyer
  Object reference of type Buyer
  A group of business terms providing information about the Buyer. 



  "buyer": {
    "buyerPostalAddress": {
      "buyerCountryCode": "GR",
      "buyerAddressLine1": "ΧΡ ΛΑΔΑ 66",
      "buyerPostCode": "11525",
      "buyerAddressLine2": "ΑΤΤΙΚΗΣ",
      "buyerCity": "ΑΘΗΝΑ",
      "buyerCountrySubdivision": "Ελλάδα",
      "buyerAddressLine3": ""
    },
    "buyerVatIdentifier": "EL997001671",
    "buyerElectronicAddress": {
      "buyerElectronicAddress": "997001671",
      "buyerElectronicAddressSchemeIdentifier": "9933"
    },
    "buyerTradingName": "ΚΑΠΟΔΙΣΤΡΙΑΚΟ",
    "buyerName": "ΤΕΣΤ ΓΓΠΣ",
    "buyerLegalRegistrationIdentifier": {
      "buyerLegalRegistrationIdentifier": ""
    },
    "buyerBranch": 0,
    "buyerContact": {
      "buyerContactPoint": "",
      "buyerContactPhoneNumber": "21099999999",
      "buyerContactEmail": "testb2g@gsis.com"
    }
  },

  
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['buyer']=array();
    /*    buyerPostalAddress (BG-8)
      Cardinality: 0..1
      Type: BuyerPostalAddress
      Object type reference to BuyerPostalAddress
      A group of business terms providing information about the address of the Buyer.
      Notes:
      o Sufficient components of the address are to be filled to comply with legal requirements
  
        buyerCountryCode (BT-55)
        Cardinality: 0..1
        Type: String
        Example: "GR"
        A code that identifies the country.
        If no tax representative is specified, this is the country where VAT is liable. The lists of valid countries are
        registered with the EN ISO 3166-1 Maintenance agency, Codes for the representation of names of countries
        and their subdivisions.
        • buyerAddressLine1 (BT-50)
        Cardinality: 0..1
        Type: String
        Example: "N. Xylouri 33"
        The main address line in an address.
        Usually the street name and number or post office box.
        • buyerPostCode (BT-53)
        Cardinality: 0..1
        Type: String
        Example: "11333"
        The identifier for an addressable group of properties according to the relevant postal service.Such as a ZIP
        code or a post code
        • buyerAddressLine2 (BT-51)
        Cardinality: 0..1
        Type: String
        Example: "Goudi"
        An additional address line in an address that can be used to givefurther details supplementing the main line.
        • buyerCity (BT-52)
        Cardinality: 0..1
        Type: String
        Example: "Athens"
        The common name of the city, town or village, where the Buyer address is located.
        • buyerCountrySubdivision (BT-54)
        Cardinality: 0..1
        Type: String
        Example: "Attiki"
        The subdivision of a country.Such as a region, a county, a state, a province, etc.
        • buyerAddressLine3 (BT-163)
        Cardinality: 0..1
        Type: String
        Example: "Plateia Pedon"
        An additional address line in an address that can be used to give furtherdetails supplementing the main line.
        */
    
    $adata['buyer']['buyerPostalAddress']=array();
    if (isset($xml['counterpart']['country'])) $adata['buyer']['buyerPostalAddress']['buyerCountryCode']=$xml['counterpart']['country'];
    if (isset($xml['counterpart']['address']['street'])) $adata['buyer']['buyerPostalAddress']['buyerAddressLine1']=$xml['counterpart']['address']['street'];
  	if (isset($xml['counterpart']['address']['postalCode'])) $adata['buyer']['buyerPostalAddress']['buyerPostCode']=$xml['counterpart']['address']['postalCode'];
    
    if (isset($struct_data['row']['ma_perioxi'])) $adata['buyer']['buyerPostalAddress']['buyerAddressLine2'] = $struct_data['row']['ma_perioxi'];
  	if (isset($xml['counterpart']['address']['city'])) $adata['buyer']['buyerPostalAddress']['buyerCity']=$xml['counterpart']['address']['city'];
    if (isset($struct_data['row']['party_nomos_descr'])) $adata['buyer']['buyerPostalAddress']['buyerCountrySubdivision'] = $struct_data['row']['party_nomos_descr'];
    //$adata['buyer']['buyerPostalAddress']['buyerAddressLine3']='';
  
  
    /*buyerVatIdentifier (BT-48)
      Cardinality: 0..1
      Type: String
      Example: "EL1265833"
      The Buyer's VAT identifier (also known as Buyer VAT identification number).
      VAT number prefixed by a country code based on EN ISO 3166-1 Codes for the representation of names of
      countries and their subdivisions
      Το ΑΦΜ Αγοραστή, όπως αυτός περιγράφεται στο πεδίο BT-44, με το πρόθεμα της χώρας, σύμφωνα με το
      EN ISO 3166-1, στην οποία έχει έδρα. Για Αγοραστή με έδρα την Ελλάδα το πρόθεμα χώρας είναι οι
      λατινικοί χαρακτήρες EL, π.χ EL997001671 για το Υ.ΨΗ.Δ. Αν η Α.Α δεν έχει δικό της ΑΦΜ, κληρονομεί
      το ΑΦΜ του υπερκείμενου Διοικητικού Φορέα (π.χ ΑΑΔΕ, Υπουργείο Ψηφιακής Διακυβέρνησης) στον
      οποίο υπάγεται.
      Notes:
      o EL MUST be used for Greek Buyers*/
      
    if (isset($xml['counterpart']['vatNumber'])) {
      $adata['buyer']['buyerVatIdentifier']=
        (isset($struct_data['row']['party_country_ee']) ? $struct_data['row']['party_country_ee'] : '').
        $xml['counterpart']['vatNumber'];
    }
  
    
    /*buyerElectronicAddress (BT-49)
      Cardinality: 0..1
      Type: BuyerElectronicAddress
      Object type reference to BuyerElectronicAddress
      Identifies the Buyer's electronic address to which the invoice is delivered 
  
        buyerElectronicAddress (BT-49)
        Cardinality: 0..1
        Type: String
        Example: "9933:997001671"
        Identifies the Buyer's electronic address to which the invoice is delivered
        In the case of Greek e-Invoicing this could be the electronic address corresponding to the
        recipient's Provider URL.
        B2G - GREECE
        Για την Ελλάδα το schemeID είναι ο κωδικός 9933 που σημαίνει ΑΦΜ, ενώ ο Identifier είναι ένα
        ελληνικό ΑΦΜ χωρίς το πρόθεμα της χώρας. Το αναγνωριστικό ηλεκτρονικής διεύθυνσης
        Αγοραστή για το PEPPOL αφορά τη ΓΓΠΣ ΔΔ (ως το μοναδικό σημείο εισόδου του Η.Τ στο
        Δημόσιο) και είναι το 9933:997001671, το 997001671 είναι το ΑΦΜ του Υπουργείου Ψηφ.
        Διακυβέρνησης στο οποίο υπάγεται η ΓΓΠΣ ΔΔ. Η Ηλεκτρονική Διεύθυνση Αγοραστή
        αναφέρεται επίσης και στο Standard Business Document Header (SBDH) του XML εγγράφου του
        Η.Τ.
        Εσωτερικά στο δίκτυο eDelivery του PEPPOL, το αναγνωριστικό αυτό αντιστοιχεί με τη φυσική
        IP διεύθυνση των endpoint συστημάτων των συμμετεχόντων, ώστε να πραγματοποιείται η φυσική
        δρομολόγηση των ηλεκτρονικών παραστατικών μεταξύ των. Το αναγνωριστικό ηλ. διεύθυνσης
        PEPPOL για τη ΓΓΠΣ ΔΔ είναι το 9933:997001671, το 997001671 είναι το ΑΦΜ του Υπουργείου
        Ψηφ. Διακυβέρνησης στο οποίο υπάγεται η ΓΓΠΣ ΔΔ. Οι κωδικοί αυτοί είναι κατά κανόνα
        δημοσιευμένοι στο PEPPOL Directory στη δημόσια διεύθυνση https://directory.peppol.eu/public.
        Στη τρέχουσα έκδοση του, το PEPPOL Directory περιέχει αναγνωριστικά μόνο για Αγοραστές,
        ενώ αυτά μπορούν να καταχωρηθούν στο SMP της ΓΓΠΣ ΔΔ.
        • buyerElectronicAddressSchemeIdentifier (BT-49)
        Cardinality: 0..1
        Type: String
        Example: "9933"
        The identification scheme identifier of the Buyer electronic address.
        The scheme identifier shall be chosen from a list to be maintained by the Connecting Europe
        Facility.     
        Για την Ελλάδα το schemeID είναι ο κωδικός 9933 που σημαίνει ΑΦΜ, ενώ ο Identifier είναι ένα
        ελληνικό ΑΦΜ χωρίς το πρόθεμα της χώρας.     */
  
    
    if (isset($xml['counterpart']['vatNumber'])) {
      $adata['buyer']['buyerElectronicAddress']=array();
      //$adata['buyer']['buyerElectronicAddress']['buyerElectronicAddress']=$xml['counterpart']['vatNumber']; 
      $adata['buyer']['buyerElectronicAddress']['buyerElectronicAddress']='997001671';
      $adata['buyer']['buyerElectronicAddress']['buyerElectronicAddressSchemeIdentifier']='9933';     
      /*Για την Ελλάδα το schemeID είναι ο κωδικός 9933 που σημαίνει ΑΦΜ, ενώ ο Identifier είναι ένα
      ελληνικό ΑΦΜ χωρίς το πρόθεμα της χώρας. Το αναγνωριστικό ηλεκτρονικής διεύθυνσης
      Αγοραστή για το PEPPOL αφορά τη ΓΓΠΣ ΔΔ (ως το μοναδικό σημείο εισόδου του Η.Τ στο
      Δημόσιο) και είναι το 9933:997001671, το 997001671 είναι το ΑΦΜ του Υπουργείου Ψηφ.
      Διακυβέρνησης στο οποίο υπάγεται η ΓΓΠΣ ΔΔ. Η Ηλεκτρονική Διεύθυνση Αγοραστή
      αναφέρεται επίσης και στο Standard Business Document Header (SBDH) του XML εγγράφου του
      Η.Τ.*/

          
    }
    
    /*buyerTradingName (BT-45)
      Cardinality: 0..1
      Type: String
      Example: "Martakis S.A."
      A name by which the Buyer is known, other than Buyer name (also known as Business name).
      This may be used if different from the Buyer name. */
      
    if (isset($struct_data['row']['title']))     $adata['buyer']['buyerTradingName']=$struct_data['row']['title'];
    else if (isset($xml['counterpart']['name'])) $adata['buyer']['buyerTradingName']=$xml['counterpart']['name'];
    
    /*buyerName (BT-44)
      Cardinality: 0..1
      Type: String
      Example: "Martakis S.A."
      The full name of the Buyer.
      B2G - GREECE
      Για το Ελληνικό Δημόσιο, η υπερκείμενη διοικητική δομή ΑΓΟΡΑΣΤΗΣ (π.χ ΑΝΕΞΑΡΤΗΤΗ ΑΡΧΗ
      ΔΗΜΟΣΙΩΝ ΕΣΟΔΩΝ, Υπουργείο Ψηφ. Διακυβέρνησης, Νοσοκομείο ΕΥΑΓΓΕΛΙΣΜΟΣ, ΥΟΥΡΓΕΙΟ
      ΕΘΝΙΚΗΣ ΑΜΥΝΗΣ, κ.λ.π) η γενικότερα Φορέας, του άρθρου 14 του ν.4270/2014 ή οποιαδήποτε νομική
      οντότητα που εν προκειμένω εκτελεί δημόσια σύμβαση ) που πραγματοποιεί τη δαπάνη. Είναι οπωσδήποτε
      κάτοχος ΑΦΜ.
      Αγοραστής δύναται να είναι μια υπερκείμενη κρατική δομή με ΑΦΜ η οποία δύναται να έχει μια έως
      πολλές Α.Α υπαγόμενες σε αυτή, οι οποίες πραγματοποιούν δαπάνες. Σε αυτή τη περίπτωση η Α.Α στα ΒΤ10, ΒΤ-46 που πραγματοποιεί την δαπάνη με κληρονομούμενο το ΑΦΜ Αγοραστή, υπάγεται στον
      Αγοραστή του πεδίου BT-44.
      Αγοραστής δύναται επίσης να είναι και μια Α.Α (π.χ Παν/μιο Αιγαίου, Νοσοκομείο ΕΥΑΓΓΕΛΙΣΜΟΣ) στο
      ρόλο του Αγοραστή/Φορέα, εφόσον η Α.Α είναι κάτοχος ΑΦΜ. Σε αυτή τη περίπτωση οι τιμές στα πεδία
      BT-10 και BT-44 δύναται να είναι ταυτόσημες, και ως εκ τούτου το πεδίο BT-10 ως μη υποχρεωτικό
      δύναται να παραληφθεί ως περιττό από το XML του Η.Τ (εκτός κι αν το BT-10 φέρει συμπληρωματική
      πληροφορία, βλέπε περιγραφή πεδίου ΒΤ-10).*/
    
    //if (isset($xml['counterpart']['name'])) $adata['buyer']['buyerName']=$xml['counterpart']['name'];
    
    if (isset($struct_data['row']['b2g_inv_buyer_name']) and trim_gks($struct_data['row']['b2g_inv_buyer_name']!='')) { 
      $adata['buyer']['buyerName']=trim_gks($struct_data['row']['b2g_inv_buyer_name']); 
    } else {
      if (isset($xml['counterpart']['name'])) $adata['buyer']['buyerName']=$xml['counterpart']['name'];
    }
        
    
    /*buyerLegalRegistrationIdentifier (BT-47)
      Cardinality: 0..1
      Type: BuyerLegalRegistrationIdentifier
      Object type reference to BuyerLegalRegistrationIdentifier
      Represents an identifier of the Buyer. If no scheme is specified, it should be known by Buyer and Seller, e.g.
      a previously exchanged Seller assigned identifier of the Buyer
      Όταν δεν χρησιμοποιείται το scheme identification, τότε δηλώνει τον Αριθμό Γ.Ε.ΜΗ. (Εφόσον υπάρχει). 
        
        buyerLegalRegistrationSchemeIdentifier (BT-47)
        Cardinality: 0..1
        Type: String
        Example: null
        The identification scheme identifier of the Buyer legal registration identifier.
        If used, the identification scheme shall be chosen from the entries of the list published by the ISO/IEC6523
        agency
        • buyerLegalRegistrationIdentifier (BT-47)
        Cardinality: 0..1
        Type: String
        Example: "002054101033"
        An identifier issued by an official registrar that identifies the Buyer as a legal entity or person.
        If no identification scheme is specified, it should be known by Buyer and Seller, e.g. the identifier that is
        exclusively used in the applicable legal environment.
        B2G
        Όταν δεν χρησιμοποιείται το scheme identification, τότε δηλώνει τον Αριθμό Γ.Ε.ΜΗ. (Εφόσον υπάρχει). 
  
      "buyerLegalRegistrationIdentifier": {
        "buyerLegalRegistrationSchemeIdentifier" : //https://docs.peppol.eu/poacc/billing/3.0/codelist/ICD/
        "buyerLegalRegistrationIdentifier": "002054101033" Γ.Ε.ΜΗ. 
      }*/
      
    $adata['buyer']['buyerLegalRegistrationIdentifier']=array();
    $adata['buyer']['buyerLegalRegistrationIdentifier']['buyerLegalRegistrationIdentifier']='';
  
    if (isset($struct_data['row']['gemi_number'])) { //G.E.MH.
      $adata['buyer']['buyerLegalRegistrationIdentifier']['buyerLegalRegistrationIdentifier']=trim_gks($struct_data['row']['gemi_number']);
    }
    
    //echo '<pre>';print_r($struct_data);die();
    
    /*
      buyerBranch (ET-48)
      Cardinality: 1..1
      Type: Integer
      Example: 0
      Buyer's installation branch number
      Notes:
      o should be 0 when no branch is specified*/
    $adata['buyer']['buyerBranch']=0; //todo
  
    /*buyerContact (BG-9)
      Cardinality: 0..1
      Type: BuyerContact
      Object type reference to BuyerContact
      A group of business terms providing contact information about the Buyer
      Επαρκή στοιχεία επικοινωνίας του Αγοραστή (BT-44, BT-48) πρέπει να συμπληρωθούν . Στις περιπτώσεις
      που ΑΓΟΡΑΣΤΗΣ και η υπαγόμενη Α.Α που πραγματοποιεί την δαπάνη του Η.Τ διαφέρουν, κατ’
      οικονομία στα πεδία BG-9 καταχωρουνται τα στοιχεία της εν λόγω Α.Α. 
      
        • buyerContactPoint (BT-56)
        Cardinality: 0..1
        Type: String
        Example: "Dpt. of Science"
        A contact point for a legal entity or person.
        Such as person name, contact identification, department or office identification.
        Όπως όνομα φυσικού προσώπου, ταυτότητα επαφής, Όνομα τμήματος ή γραφείου.
        • buyerContactPhoneNumber (BT-56)
        Cardinality: 0..1
        Type: String
        Example: "210-7773333"
        A phone number for the contact point
        • buyerContactEmail (BT-57)
        Cardinality: 0..1
        Type: String
        Example: "client@buyer.com"
        An e-mail address for the contact point
    */
    
    $adata['buyer']['buyerContact']=array();
    $buyerContactPoint='';
    if (isset($struct_data['row']['user_first_name'])) $buyerContactPoint.= $struct_data['row']['user_first_name'];
    if (isset($struct_data['row']['user_last_name'])) $buyerContactPoint.= ' '.$struct_data['row']['user_last_name'];
    $buyerContactPoint=trim($buyerContactPoint);
    if ($buyerContactPoint!='') $adata['buyer']['buyerContact']['buyerContactPoint']=$buyerContactPoint;
    
    if (isset($struct_data['row']['user_mobile'])) $adata['buyer']['buyerContact']['buyerContactPhoneNumber']=$struct_data['row']['user_mobile'];
    if (isset($struct_data['row']['user_email'])) $adata['buyer']['buyerContact']['buyerContactEmail']=$struct_data['row']['user_email'];
  }
  
  /*vatAccountingCurrencyCode (BT-6)
  Cardinality: 0..1
  Type: String
  Example: "EUR"
  The currency used for VAT accounting and reporting purposes as accepted or required in the country of the
  Seller.
  Shall be used in combination with the Invoice total VAT amount in accounting currency (BT-111), when the
  VAT accounting currency code differs from the Invoice currency code. The lists of valid currencies are
  registered with the ISO 4217 Maintenance Agency Codes for the representation of currencies and funds
  Θα χρησιμοποιείται σε συνδυασμό με το Συνολικό ποσό ΦΠΑ τιμολογίου στό νόμισμα λογιστικής (BT-111)
  του κράτους μέλους της EC, όταν κωδικός νομίσματος ΦΠΑ διαφέρει από τον Κωδικός νομίσματος
  τιμολογίου. Οι λίστες των έγκυρων νομισμάτων είναι καταγεγραμμένες σύμφωνα με το πρότυπο ISO 4217.
  Notes:
  o Please refer to Article 230 of the Council Directive 2006/112/EC [2] for more information.
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['vatAccountingCurrencyCode']=null;
  }
  
  /*vatPointDate (BT-7)
  Cardinality: 0..1
  Type: Date
  Example: "2020-10-05T00:00:00.000+0200"
  The date when the VAT becomes accountable for the Seller and for the Buyer in so far as that date can be
  determined and differs from the date of issue of the invoice, according to the VAT directive.
  The tax point is usually the date goods were supplied or services completed (the 'basic tax point'). There are
  some variations. Please refer to Article 226 (7) of the Council Directive 2006/112/EC [2] for more
  information. This element is required if the Value added tax point date is different from the Invoice issue date.
  Both Buyer and Seller should use the Tax Point Date when provided by the Seller.
  Notes:
  o The use of BT-7 and BT-8 is mutually exclusive.
  o The text representation when sending values should be formatted according to ISO 8601
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['vatPointDate']=null;
  }
  
  /*additionalSupportDocs (BG-24)
  Cardinality: 0..n
  Type: Collection<AdditionalSupportDoc>
  Document representation of files relevant to this Invoice
  
  B2G - Greece
  B2G - Greece
  Τα πρόσθετα δικαιολογητικά μπορούν να
  χρησιμοποιηθούν για παραπομπές :
  (1) στον αριθμό Μ.ΑΡ.Κ που επιστρέφεται από την
  εφαρμογή Ηλεκτρονικά Βιβλία “myData” της
  Α.Α.Δ.Ε. που πιστοποιεί την εγκυρότητα του
  ηλεκτρονικού τιμολογίου μέσω διαδικτυακής
  υπηρεσίας που επικαλείται ο πιστοποιημένος
  Πάροχος, με input τη σύνοψη του τιμολογίου
  Προμηθευτή του Ελληνικού Δημοσίου, με έδρα
  στην Ελλάδα ο δεύτερος.
  (2) σε μια Συμβολοσειρά Αυθεντικοποίησης (Authentication code) Παραστατικού της ΑΑΔΕ που αναμένεται να
  είναι γνωστή από τον αποδέκτη (αγοραστή),
  (3) στην Ηλεκτρονική διεύθυνση της υπηρεσίας του Προμηθευτή για τον εντοπισμό του HTML εγγράφου του
  Ηλεκτρονικού Τιμολογίου (URL).
  (4) στην Ηλεκτρονική διεύθυνση (URL) του επισημου ιστότοπου του Παρόχου ΥΠΑΗΕΣ του Προμηθευτή/Εκδοτη
  Η.Τ
  (5) σε ένα εξωτερικό Έγγραφο (αναφέρεται μέσω URL). Η επιλογή σύνδεσης σε εξωτερικό έγγραφο θα χρειαστεί,
  για παράδειγμα στην περίπτωση μεγάλου όγκου επισυναπτόμενων αντικειμένων ή και όταν περιέχει ευαίσθητες
  πληροφορίες, όπως π.χ. υπηρεσίες που σχετίζονται με προσωπικά δεδομένα, τα οποία θα πρέπει να διαχωρίζονται
  από το ίδιο το τιμολόγιο.
  (6) σε επί πλέον του ενός Δελτία Αποστολής (ΔΑ), με το πρώτο ΔΑ να καταχωρείται στο πεδίο BT-16
  (7) Σε Παρακρατήσεις και Κρατήσεις Υπερ Τρίτων Φορέων του Ελλην. Δημοσίου, ως πληροφοριακά δεδομένα.
  (8) σε ένα ενσωματωμένο (embedded) έγγραφο που συνοδεύει το Η.Τ.
  (9) σε στοιχεία δρομολόγησης, στη περίπτωση Η.Τ πιστωτικού και μη συσχετισμένου, με τύπο παραστατικού 5.2
  Όταν το πεδίο BT-123 περιέχει την τιμή ##M.AR.K##, τότε το πεδίο BT-122 περιέχει τον κωδικό Μ.ΑΡ.Κ και τα
  BT- 124 και BT-125 δεν χρησιμοποιούνται.
  • Όταν το πεδίο BT-123 περιέχει την τιμή ##AUTHCODE## τότε το πεδίο BT-122 περιέχει τη Συμβολοσειρά
  Αυθεντικοποίησης (ΑΑΔΕ) και τα πεδία BT-124 και ΒΤ-125 είναι κενά (δεν υφίστανται). Η παρουσία
  (εμφάνιση) του string ##AUTHCODE## και του κωδικού αυθεντικοποίησης σε ένα εκτυπωμένο τιμολόγιο
  είναι τα στοιχεία που διαφοροποιούν αν το εκτυπωμένο τιμολόγιο προήλθε από την B2G-PEPPOL
  Ηλεκτρονική Τιμολόγηση ή από ένα παραδοσιακό, έγχαρτο τιμολόγιο, όπου στο δεύτερο δεν υπάρχουν.
  • Όταν το πεδίο BT-123 περιέχει την τιμή ##INVOICE|URL##, τότε το πεδίο BT-124 περιέχει την
  ηλεκτρονική διεύθυνση (URL) του Η.Τ ως HTML έγγράφου, προς οπτικοποίηση και εμφάνιση. Το πεδίο
  αυτό χρησιμοποιείται για την δημιουργία και παρουσίαση του QR Code του Η.Τ, κατά την εμφάνισή του.
  Σε αυτή τη περίπτωση, το BT-122 δεν χρησιμοποιείται, ενώ το BT-125 δύναται να περιέχει ενσωματωμένο
  κάποιο συνημμένο έγγραφο. 
  
  • Όταν το πεδίο BT-123 περιέχει την τιμή ##INVOICE|PROVIDER|URL##, τότε το πεδίο BT-124 περιέχει
  την ηλεκτρονική διεύθυνση (URL) του επίσημου ιστοτόπου του Παρόχου ΥΠΑΗΕΣ του εκδότη του Η.Τ. Το
  Σε αυτή τη περίπτωση, το BT-122 δεν χρησιμοποιείται, ενώ το BT-125 δύναται να περιέχει κάποιο
  συνημμένο έγγραφο (π.χ Εγγραφο Ασφαλιστικής Ενημερότητας, Έγγραφο Φορολογικής Ενημερότητας ).
  [77] Ο αριθμός Μ.ΑΡ.Κ επιστρέφεται από την ηλεκτρονική εφαρμογή myData της ΑΑΔΕ, κατά την
  διαδικασία φορολογικής επαλήθευσης ενός νέου Η.Τ, όταν ο πιστοποιημένος Πάροχος αποστέλλει μέσω
  web service στο myData τη «σύνοψη» του Η.Τ (ένα υποσύνολο πεδίων δεδομένων) για να λάβει πίσω το
  Μ.ΑΡ.K και τη συμβολοσειρά αυθεντικοποίησης, στη περίπτωση έγκυρου Η.Τ. Η συμπερίληψη του αριθμού
  Μ.ΑΡ.Κ στη περίπτωση Ελλήνων προμηθευτών είναι υποχρεωτική και ελέγχεται αυτό από κανόνα του
  Ελληνικού CIUS. Με κάθε επιτυχημένη διαβίβαση της Σύνοψης Ηλεκτρονικού Τιμολογίου στην πλατφόρμα
  myDATA της ΑΑΔΕ, η πλατφόρμα χορηγεί τον αριθμό Μ.ΑΡ.Κ. Η Συμβολοσειρά Αυθεντικοποίησης κάθε
  παραστατικού καθορίζεται από την ΑΑΔΕ, μόνο για την περίπτωση αποστολής Τιμολογίου μέσω
  εγκεκριμένου – πιστοποιημένου από ΑΑΔΕ Παρόχου Ηλεκτρονικής Τιμολόγησης ΥΠΑΗΕΣ. Υπολογίζεται
  από το SHA-1 hash 8 πεδίων του παραστατικού τα οποία είναι : ΑΦΜ Eκδότη, Ημερομηνία Έκδοσης,
  Αριθμός Εγκατάστασης στο Μητρώο του Taxis, Τύπος Παραστατικού, Σειρά, ΑΑ, Μ.ΑΡ.Κ Παραστατικού,
  Συνολική Αξία Παραστατικού, Σύνολο Αξίας Φ.Π.Α. Παραστατικού, ΑΦΜ Λήπτη. Η συμπερίληψη της
  Συμβολοσειράς Αυθεντικοποίησης στο Η.Τ για τους Έλληνες προμηθευτές είναι υποχρεωτική και ελέγχεται
  αυτό από κανόνα του Ελληνικού CIUS, καθώς και στο ΚΕ.Δ. Στη περίπτωση Πωλητή-Προμηθευτή του
  Ελληνικού Δημοσίου με έδρα εκτός Ελλάδος, με ή χωρίς Φορολογικό Αντιπρόσωπο στην Ελλάδα (πεδίο
  BT-62), το πεδίο BT-122 είναι κενό . Δηλαδή, δεν υφίσταται η υποχρέωση υποβολής της Συμβολοσειράς
  Αυθεντικοποίησης στο myDATA, για εξωχώριους προμηθευτές του Ελληνικού Δημοσίου .
  • Όταν το πεδίο BT-123 περιέχει την τιμή ##DELTIO|APOSTOL##, τότε το αντίστοιχο ΒΤ-122 περιέχει το
  ID Δελτίου Αποστολής (ΔΑ). Αυτό είναι δυνατόν να συμβαίνει στη περίπτωση εκείνη που το τρέχον Η.Τ
  είναι συγκεντρωτικό και εμπεριέχει πολλά ΔΑ, όπου το αναγνωριστικό του πρώτου ΔΑ θα καταχωρείται στο
  πεδίο ΒΤ-16 του Η.Τ, ενώ τα υπόλοιπα αναγνωριστικά ΔΑ σε ισάριθμα <ΒΤ-122, ΒΤ-123> XML στοιχεία,
  με την ίδια ένδειξη ##DELTIO.APOSTOL## ως τιμή στο στοιχείο ΒΤ-123 και με τα αναγνωριστικά (IDs)
  ΔΑ στα αντίστοιχα στοιχεία ΒΤ- 122 του ίδιου παραστατικού Η.Τ.
  • Όταν στο BT-123 υπάρχει η τιμή ##PARAKRAT|FOR|EISOD|x##, όπου x είναι ο κωδικός παρακράτησης
  Φόρου Εισοδήματος και δύναται να παίρνει τις τιμές 1, 2, 3, …,18, τότε στο πεδίο BT-122 μπαίνει ως TEXT
  το συνολικό ποσό (άθροισμα) των αντίστοιχων παρακρατήσεων Φόρου Εισοδήματος αυτού του τύπου (με
  το ίδιο κωδικό x), σύμφωνα με τις κείμενες διατάξεις της ΑΑΔΕ ( βλέπε παράγραφο 8.4 «Κατηγορία
  Παρακρατούμενων Φόρων» του τεχνικού εγγράφου “Τεχνική περιγραφή διεπαφών REST API για
  διαβίβαση & λήψη δεδομένων για χρήστες ERP” Έκδοση 1.0.6 – Σεπτέμβριος 2022, της ΑΑΔΕ, στο
  σύνδεσμο 
  https://www.aade.gr/sites/default/files/2022-09/myDATA%20API%20Documentation%20v1.0.6_official_erp.pdf
  ). Σε ένα Η.Τ είναι δυνατόν να
  υφίστανται περισσότερες της μιας παρακρατήσεις Φόρου Εισοδήματος και για κάθε μία από αυτές θα
  αντιστοιχεί ένα ζεύγος δεδομένων . Στη περίπτωση ξένου Πωλητή-Προμηθευτή του Ελληνικού Δημοσίου
  με έδρα εκτός Ελλάδος, με ή χωρίς Φορολογικό Αντιπρόσωπο στην Ελλάδα (πεδίο BT-62), 
  το πεδίο BT122 είναι κενό . 
  Δηλαδή, δεν υφίσταται η υποχρέωση υποβολής Παρακρατήσεων Φόρου Εισοδήματος, για
  εξωχώριους προμηθευτές του Ελληνικού Δημοσίου .
  • Όταν στο BT-123 υπάρχει η τιμή ##PARAKRAT|YPER3## στο πεδίο BT-122 μπαίνει ως TEXT συνολικά
  το άθροισμα των κρατήσεων αυτού του τύπου, δηλαδή Υπέρ Τρίτων φορέων του Ελλην. Δημοσίου (π.χ,
  ΕΑΑΔΗΣΥ, ΑΕΠΠ, ΟΓΑ χαρτόσημο, Υπερ Ψυχικής Υγείας, κ.λ.π). Για Παρακρατήσεις Υπέρ Τρίτων
  υφίσταται στο τρέχον Η.Τ μόνο ένα ζεύγος δεδομένων , το οποίο περιλαμβάνει το άθροισμα όλων των
  φόρων Υπέρ Τρίτων ως text, για το συγκεκριμένο Η.Τ. Στη περίπτωση Πωλητή-Προμηθευτή του Ελληνικού
  Δημοσίου με έδρα εκτός Ελλάδος, με ή χωρίς Φορολογικό Αντιπρόσωπο στην Ελλάδα (πεδίο BT-62), το
  πεδίο BT-122 είναι κενό . Δηλαδή, δεν υφίσταται η υποχρέωση υποβολής Παρακρατήσεων Υπερ Τρίτων
  Φορέων του Ελλην. Δημοσίου, για εξωχώριους προμηθευτές του Ελληνικού Δημοσίου .   
  • Τα πεδία ΜΑΡΚ, Συμβολοσειρά Αυθεντικοποίησης, Ηλ. Διεύθυνση συμπληρώνονται αυτόματα
  από το σύστημα  
  

• reference (BT-122)
Cardinality: 1..1
Type: String
Example: "Contract F203.2/2020-01-01"
An identifier of the supporting document.
B2G
H τιμή του Μοναδικού ΑΡιθμού Καταχώρισης (Μ.ΑΡ.Κ) που αποδίδεται από την εφαρμογή Ηλεκτρονικά
Βιβλία “myData” της Α.Α.Δ.Ε, , στην περίπτωση που ο προμηθευτής είναι εταιρεία με έδρα την Ελλάδα. -
provider specs - mydata specs
Είναι υποχρεωτικό πεδίο για Έλληνες προμηθευτές. Εναλλακτικά, η συμβολοσειρά Αυθεντικοποίησης του
Εγγράφου «Authentication Code», παραγόμενη από την εφαρμογή Ηλεκτρονικά Βιβλία “myData” της
Α.Α.Δ.Ε, , στην περίπτωση που ο προμηθευτής είναι Έλληνας. Είναι υποχρεωτικό πεδίο για Έλληνες
προμηθευτές.
Εναλλακτικά , το αναγνωριστικό ενός Δελτίου Αποστολής (ΔΑ), γι αυτό το Η.Τ.
Εναλλακτικά, το συνολικό ποσό Παρακρατήσεων Φόρου Εισοδήματος του Ελλην. Δημοσίου ανά
κατηγορία/τύπο παρακράτησης/κράτησης, υπό τη μορφή κειμένου (text).
Εναλλακτικά, το συνολικό ποσό Κρατήσεων υπερ Τρίτων Φορέων του Ελλην. Δημοσίου , υπο τη μορφή
κειμένου (text).
Εναλλακτικά, για μη συσχετιζόμενα πιστωτικά Η.Τ τύπου 5.2 περιλαμβάνει τα στοιχεία δρομολόγησης :
«1», στη περίπτωση Τακτικού Προϋπολογισμού, ή «2», στη περίπτωση ΠΔΕ, ή «3», για λοιπούς
προϋπολογισμούς.
• attachedDocument (BG-11)
Cardinality: 0..1
Type: AttachedDocument
Object reference of type AttachedDocument
Attached document is used when documentation shall be stored with the Invoice for future reference or
audit purposes.
• description (BT-123)
Cardinality: 0..1
Type: String
Example: "A contract"
A description of the supporting document.
Such as: timesheet, usage report etc.
B2G
Η αναγραφόμενη περιγραφή ##M.AR.K## στο πεδίο BT-123 προσδιορίζει το πεδίο ΒΤ-122 ως Μ.ΑΡ.Κ
που αντιστοιχεί στο τιμολόγιο, στην περίπτωση που ο προμηθευτής είναι Έλληνας, τα δε πεδία BT-124 και
ΒΤ-125 δεν χρησιμοποιούνται
Εναλλακτικά η αναγραφόμενη περιγραφή ##AUTHCODE## στο πεδίο BT-123 προσδιορίζει το πεδίο BT122 ως Συμβολοσειρά Αυθεντικοποίησης του παραστατικού (στην περίπτωση που ο προμηθευτής είναι
Έλληνας), τα δε πεδία BT-124 και ΒΤ-125 δεν χρησιμοποιούνται.
Εναλλακτικά, η περιγραφή ##INVOICE|URL## προσδιορίζει ότι το μεν πεδίο ΒΤ-124 περιέχει την
ηλεκτρονική διεύθυνση της υπηρεσίας του παρόχου Ηλεκτρονικής Έκδοση Στοιχείων, για τον εντοπισμό
του Ηλεκτρονικού Τιμολογίου σε HTML μορφή, το δε πεδίο BT-125 είναι δυνατόν να περιέχει
ενσωματωμένο (embedded) ένα συνημμένο έγγραφο. Σε αυτή τη περίπτωση το πεδίο BT- 122 δεν
χρησιμοποιείται.
Εναλλακτικά, η περιγραφή ##INVOICE|PROVIDER|URL## προσδιορίζει ότι το μεν πεδίο ΒΤ-124 περιέχει
την ηλεκτρονική διεύθυνση του ιστοτόπου του παρόχου του προμηθευτή/εκδότη Η.Τ, το δε πεδίο BT-125
είναι δυνατόν να περιέχει ενσωματωμένο ένα συνημμένο έγγραφο. Σε αυτή τη περίπτωση το πεδίο BT-122
δεν χρησιμοποιείται.
Εναλλακτικά, η περιγραφή ##PARAKRAT|FOR|EISOD|x## προσδιορίζει ότι το πεδίο ΒΤ-122 περιέχει ως
κείμενο (text) το άθροισμα όλων των παρακρατήσεων ιδίου τύπου “x” Φορολογίας Εισοδήματος. Στη
περίπτωση αυτή τα πεδία ΒΤ-124, BT-125 δεν χρησιμοποιούνται.
Εναλλακτικά, η περιγραφή ##PARAKRAT|YPER3## προσδιορίζει ότι το πεδίο ΒΤ-122 περιέχει ως
κείμενο (text) το άθροισμα όλων των κρατήσεων ιδίου τύπου Υπερ Τρίτων Φορέων του Ελλην. Δημοσίου.
Στη περίπτωση αυτή τα πεδία ΒΤ-124, BT-125 δεν χρησιμοποιούνται.
Εναλλακτικά, η περιγραφή ##EXTERNAL|DOC## προσδιορίζει ότι το πεδίο ΒΤ-124 περιέχει την
ηλεκτρονική διεύθυνση (URL) εξωτερικού εγγράφου, που υποστηρίζει με πρόσθετες πληροφορίες το
τρέχον Η.Τ.
Εναλλακτικά, η περιγραφή ##DELTIO|APOSTOL## προσδιορίζει ότι το αντίστοιχο πεδίο ΒΤ-122 περιέχει
τον αριθμό ενός ΔΑ, στη περίπτωση πολλαπλών ΔΑ γι αυτό το Η.Τ
Εναλλακτικά, η περιγραφή ##PROJECT|REFERENCE## για μη συσχετιζόμενο πιστωτικό Η.Τ τύπου 5.2,
προσδιορίζει ότι το πεδίο BT-122 περιέχει ως κείμενο (text) τις εναλλακτικές τιμές : 1, ή 2|Ενάριθμος, ή 3
• location (BT-124)
Cardinality: 0..1
Type: URL
Example: "https://fakeurl.dot/contract"
The URL (Uniform Resource Locator) that identifies where the external document is located.
A means of locating the resource including its primary access mechanism, e.g. http:// or ftp://. External
document location shall be used if the Buyer requires additional information to support the Invoice. External
documents do not form part of the invoice. Risks can be involved when accessing external documents.
Notes:
o should be a valid, parsable URL

  "additionalSupportDocs": [
    {
      "reference": "PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjxJbnZvaWNlc0RvYyB4bWxucz0iaHR0cDovL3d3dy5hYWRlLmdyL215REFUQS9pbnZvaWNlL3YxLjAiIHhzaTpzY2hlbWFMb2NhdGlvbj0iaHR0cDovL3d3dy5hYWRlLmdyL215REFUQS9pbnZvaWNlL3YxLjAgc2NoZW1hLnhzZCIgeG1sbnM6aWNscz0iaHR0cHM6Ly93d3cuYWFkZS5nci9teURBVEEvaW5jb21lQ2xhc3NpZmljYXRvbi92MS4wIiB4bWxuczplY2xzPSJodHRwczovL3d3dy5hYWRlLmdyL215REFUQS9leHBlbnNlc0NsYXNzaWZpY2F0b24vdjEuMCIgeG1sbnM6eHNpPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxL1hNTFNjaGVtYS1pbnN0YW5jZSI+DQogICAgPGludm9pY2U+DQogICAgPHVpZD48L3VpZD4NCiAgICA8bWFyaz4wPC9tYXJrPg0KICAgIDxpc3N1ZXI+DQogICAgPHZhdE51bWJlcj4xNzc0NzI0Mzg8L3ZhdE51bWJlcj4NCiAgICA8Y291bnRyeT5HUjwvY291bnRyeT4NCiAgICA8YnJhbmNoPjA8L2JyYW5jaD4NCiAgICA8L2lzc3Vlcj4NCiAgICA8Y291bnRlcnBhcnQ+DQogICAgPHZhdE51bWJlcj45OTcwMDE2NzE8L3ZhdE51bWJlcj4NCiAgICA8Y291bnRyeT5HUjwvY291bnRyeT4NCiAgICA8YnJhbmNoPjA8L2JyYW5jaD4NCiAgICA8YWRkcmVzcz4NCiAgICAgICAgPHN0cmVldD7Op86hIM6bzpHOlM6RIDY2PC9zdHJlZXQ+DQogICAgICAgIDxudW1iZXI+PC9udW1iZXI+DQogICAgICAgIDxwb3N0YWxDb2RlPjExNTI1PC9wb3N0YWxDb2RlPg0KICAgICAgICA8Y2l0eT7Okc6YzpfOnc6RPC9jaXR5Pg0KICAgIDwvYWRkcmVzcz4NCiAgICA8L2NvdW50ZXJwYXJ0Pg0KICAgIDxpbnZvaWNlSGVhZGVyPg0KICAgIDxzZXJpZXM+zpE8L3Nlcmllcz4NCiAgICA8YWE+MTMzMzMzNTwvYWE+DQogICAgPGlzc3VlRGF0ZT4yMDIzLTEyLTIxPC9pc3N1ZURhdGU+DQogICAgPGludm9pY2VUeXBlPjEuMTwvaW52b2ljZVR5cGU+DQogICAgPHZhdFBheW1lbnRTdXNwZW5zaW9uPmZhbHNlPC92YXRQYXltZW50U3VzcGVuc2lvbj4NCiAgICA8Y3VycmVuY3k+RVVSPC9jdXJyZW5jeT4NCiAgICA8ZGlzcGF0Y2hEYXRlPjIwMjMtMTItMjE8L2Rpc3BhdGNoRGF0ZT4NCiAgICA8ZGlzcGF0Y2hUaW1lPjIwOjU1OjAwPC9kaXNwYXRjaFRpbWU+DQogICAgPHZlaGljbGVOdW1iZXI+zqXOkc6SMzg1NzwvdmVoaWNsZU51bWJlcj4NCiAgICA8bW92ZVB1cnBvc2U+MTwvbW92ZVB1cnBvc2U+DQogICAgPC9pbnZvaWNlSGVhZGVyPg0KICAgIDxwYXltZW50TWV0aG9kcz4NCiAgICA8cGF5bWVudE1ldGhvZERldGFpbHM+DQogICAgICAgIDx0eXBlPjU8L3R5cGU+DQogICAgICAgIDxhbW91bnQ+MjQ4LjAwPC9hbW91bnQ+DQogICAgPC9wYXltZW50TWV0aG9kRGV0YWlscz4NCiAgICA8L3BheW1lbnRNZXRob2RzPg0KICAgIDxpbnZvaWNlRGV0YWlscz4NCiAgICA8bGluZU51bWJlcj4xPC9saW5lTnVtYmVyPg0KICAgICAgICA8cXVhbnRpdHk+Mi4wMDwvcXVhbnRpdHk+DQogICAgICAgIDxtZWFzdXJlbWVudFVuaXQ+MTwvbWVhc3VyZW1lbnRVbml0Pg0KICAgICAgICA8bmV0VmFsdWU+MjAwLjAwPC9uZXRWYWx1ZT4NCiAgICAgICAgPHZhdENhdGVnb3J5PjE8L3ZhdENhdGVnb3J5Pg0KICAgICAgICA8dmF0QW1vdW50PjQ4LjAwPC92YXRBbW91bnQ+DQogICAgICAgIDxsaW5lQ29tbWVudHM+PC9saW5lQ29tbWVudHM+DQogICAgICA8aW5jb21lQ2xhc3NpZmljYXRpb24+DQogICAgICAgICAgPGljbHM6Y2xhc3NpZmljYXRpb25UeXBlPkUzXzU2MV8wMDc8L2ljbHM6Y2xhc3NpZmljYXRpb25UeXBlPg0KICAgICAgICAgIDxpY2xzOmNsYXNzaWZpY2F0aW9uQ2F0ZWdvcnk+Y2F0ZWdvcnkxXzE8L2ljbHM6Y2xhc3NpZmljYXRpb25DYXRlZ29yeT4NCiAgICAgICAgICA8aWNsczphbW91bnQ+MjAwLjAwPC9pY2xzOmFtb3VudD4NCiAgICAgIDwvaW5jb21lQ2xhc3NpZmljYXRpb24+DQogICAgPC9pbnZvaWNlRGV0YWlscz4NCiAgICA8aW52b2ljZVN1bW1hcnk+DQogICAgPHRvdGFsTmV0VmFsdWU+MjAwLjAwPC90b3RhbE5ldFZhbHVlPg0KICAgIDx0b3RhbFZhdEFtb3VudD40OC4wMDwvdG90YWxWYXRBbW91bnQ+DQogICAgPHRvdGFsV2l0aGhlbGRBbW91bnQ+MC4wMDwvdG90YWxXaXRoaGVsZEFtb3VudD4NCiAgICA8dG90YWxGZWVzQW1vdW50PjAuMDA8L3RvdGFsRmVlc0Ftb3VudD4NCiAgICA8dG90YWxTdGFtcER1dHlBbW91bnQ+MC4wMDwvdG90YWxTdGFtcER1dHlBbW91bnQ+DQogICAgPHRvdGFsT3RoZXJUYXhlc0Ftb3VudD4wLjAwPC90b3RhbE90aGVyVGF4ZXNBbW91bnQ+DQogICAgPHRvdGFsRGVkdWN0aW9uc0Ftb3VudD4wLjAwPC90b3RhbERlZHVjdGlvbnNBbW91bnQ+DQogICAgPHRvdGFsR3Jvc3NWYWx1ZT4yNDguMDA8L3RvdGFsR3Jvc3NWYWx1ZT4NCiAgICAgIDxpbmNvbWVDbGFzc2lmaWNhdGlvbj4NCiAgICAgICAgICA8aWNsczpjbGFzc2lmaWNhdGlvblR5cGU+RTNfNTYxXzAwNzwvaWNsczpjbGFzc2lmaWNhdGlvblR5cGU+DQogICAgICAgICAgPGljbHM6Y2xhc3NpZmljYXRpb25DYXRlZ29yeT5jYXRlZ29yeTFfMTwvaWNsczpjbGFzc2lmaWNhdGlvbkNhdGVnb3J5Pg0KICAgICAgICAgIDxpY2xzOmFtb3VudD4yMDAuMDA8L2ljbHM6YW1vdW50Pg0KICAgICAgICAgIDxpY2xzOmlkPjE8L2ljbHM6aWQ+DQogICAgICA8L2luY29tZUNsYXNzaWZpY2F0aW9uPg0KICAgIDwvaW52b2ljZVN1bW1hcnk+DQogICAgPC9pbnZvaWNlPg0KPC9JbnZvaWNlc0RvYz4NCg==",
      "attachedDocument": null,
      "description": "##InvoicesDoc##",
      "location": null
    },
    {
      "reference": "3.00",
      "description": "##PARAKRAT|FOR|EISOD|2##"
    },
    {
      "reference": "4.00",
      "description": "##PARAKRAT|YPER3##"
    }
  ],
    
  */
  
  $adata['additionalSupportDocs']=array();
  
  $aade_params=array();
  
  $aade_params['call_from_paroxos']=true;
  $aade_params['paroxos_params']=$paroxos_params;
  
  //print '<pre>ggggggggggqqqqqq ';print_r($aade_params);die();
  
  $ret_xml = gks_aade_invoice_xml_create($id,$doc_table,$aade_params);
  if ($ret_xml['success']==false) {
			$ret['message']=$ret_xml['message'];
			debug_mail(false,$ret['message'],print_r($ret_xml , true)); return $ret;
  } else {
    $adata['additionalSupportDocs'][]=array(
      'reference' => base64_encode($ret_xml['out_xml']),
      'attachedDocument' => null,
      'description' => '##InvoicesDoc##',
      'location' => null,
    );
  }
  
  $paroxos_signature_id_array=$ret_xml['paroxos_signature_id_array'];
  //print '<pre>paroxos_signature_id_array ';print_r($paroxos_signature_id_array);die();
  
  
  $PARAKRAT_FOR_EISOD=array();
  $PARAKRAT_YPER3=0;
  foreach ($struct_data['prow_array'] as $pitem) {
    
    if (intval($pitem['product_withheldPercentCategory'])>0) {
      $pikey='##PARAKRAT|FOR|EISOD|'.$pitem['product_withheldPercentCategory'].'##';
      if (isset($PARAKRAT_FOR_EISOD[$pikey])==false) {
        $PARAKRAT_FOR_EISOD[$pikey]=array(
          'description' => $pikey,
          'amount'=> 0,
        );
      }
      $PARAKRAT_FOR_EISOD[$pikey]['amount']+=floatval($pitem['product_withheldAmount']);
      $PARAKRAT_YPER3+=floatval($pitem['product_deductionsAmount']);
    }
  }
  //echo '<pre>';print_r($PARAKRAT_FOR_EISOD);die();
  foreach ($PARAKRAT_FOR_EISOD as $value) {
    $adata['additionalSupportDocs'][]=array(
      'reference' => $value['amount'],
      'description' => $value['description'],
    );    
  } 
  if ($PARAKRAT_YPER3>0) {
    $adata['additionalSupportDocs'][]=array(
      'reference' => $PARAKRAT_YPER3,
      'description' => '##PARAKRAT|YPER3##',
    );    
    
  }
  //echo '<pre>';print_r($adata['additionalSupportDocs']);die();
  
  
  
  /*payee (BG-10)
  Cardinality: 0..1
  Type: Payee
  Object reference of type Payee
  A group of business terms providing information about the Payee, i.e. the role that receives the payment. The
  role of Payee may be fulfilled by another party than the Seller, e.g. a factoring service
  B2G - GREECE
  Σύμφωνα με το Πρότυπο Ηλ. Τιμολογίου για να εκχωρηθεί η πληρωμή του τιμολογίου στον
  "PAYEE"/"ΔΙΚΑΙΟΥΧΟ ΠΛΗΡΩΜΗΣ" χρειάζεται:
  α. Να έχει συμπληρωθεί η αποποίηση ευθύνης (ειδοποίηση στο τιμολόγιο) ότι το τιμολόγιο έχει εκχωρηθεί
  σε έναν άλλο δικαιούχο πληρωμής. Η αποποίηση ευθυνών πρέπει να δοθεί χρησιμοποιώντας το πεδίο
  Σημείωση τιμολογίου (BT-22) σε επίπεδο παραστατικού.
  β. Να έχει προσδιοριστεί ο δικαιούχος πληρωμής, συμπληρώνοντας τα πεδία της ομάδας BG-10 πχ όνομα
  δικαιούχου πληρωμής κλπ. γ. Να αλλάξει ο τραπεζικός λογαριασμός υπέρ του Δικαιούχου 
  
    • payeeName (BT-59)
    Cardinality: 0..1
    Type: String
    Example: "Buyer Offshore Inc."
    The name of the Payee.
    Shall be used when the Payee is different from the Seller. The Payee name may however be the same as the
    Seller name.
    B2G - GREECE
    Σύμφωνα με το Πρότυπο Ηλ. Τιμολογίου για να εκχωρηθεί η πληρωμή του τιμολογίου στον
    "PAYEE"/"ΔΙΚΑΙΟΥΧΟ ΠΛΗΡΩΜΗΣ" χρειάζεται: α. Να έχει συμπληρωθεί η αποποίηση ευθύνης
    (ειδοποίηση στο τιμολόγιο) ότι το τιμολόγιο έχει εκχωρηθεί σε έναν άλλο δικαιούχο πληρωμής. Η
    αποποίηση ευθυνών πρέπει να δοθεί χρησιμοποιώντας το πεδίο Σημείωση τιμολογίου (BT-22) σε επίπεδο
    παραστατικού. β. Να έχει προσδιοριστεί ο δικαιούχος πληρωμής, συμπληρώνοντας τα πεδία της ομάδας
    BG-10 πχ όνομα δικαιούχου πληρωμής κλπ. γ. Να αλλάξει ο τραπεζικός λογαριασμός υπέρ του Δικαιούχου
    • payeeLegalRegistrationIdentifier (BT-61)
    Cardinality: 0..1
    Type: PayeeLegalRegistrationIdentifier
    Object reference of type PayeeLegalRegistrationIdentifier
    An identifier issued by an official registrar that identifies the Payee as a legal entity or person. If no scheme
    is specified, it should be known by Buyer and Seller, e.g. the identifier that is exclusively used in the
    applicable legal environment.
    B2G – GREECE
    Ο Αριθμός ΓΕ.ΜΗ. Δικαιούχου Πληρωμής (εφόσον υπάρχει), διαφορετικά κενό πεδίο. Εάν δεν υπάρχει
    καθορισμένο identification scheme το αναγνωριστικό Πωλητή πρέπει εκ των προτέρων να είναι γνωστό σε
    Πωλητή και Αγοραστή.
    • payeeIdentifier (BT-60) 
    Cardinality: 0..1
    Type: PayeeIdentifier
    Object reference of type PayeeIdentifier
    An identifier for the Payee. If no scheme is specified, it should be known by Buyer and Seller, e.g. a
    previously exchanged Buyer or Seller assigned identifier.
    B2G - GREECE
    Εάν δεν υπάρχει καθορισμένο identification scheme το αναγνωριστικό Πωλητή πρέπει εκ των προτέρων να
    είναι γνωστό σε Πωλητή και Αγοραστή. Αυτό το στοιχείο χρησιμοποιείται τόσο για την ταυτοποίηση του
    Δικαιούχου πληρωμής, είτε για το μοναδικό αναγνωριστικό τραπεζικής αναφοράς του Δικαιούχου
    (εκχωρείται από την τράπεζα του Δικαιούχου.) 
      
    "payee": {
      "payeeName": "Buyer Offshore Inc.",
      "payeeLegalRegistrationIdentifier": {
        "payeeLegalRegistrationIdentifier": "12647333"
      },
      "payeeIdentifier": {
        "payeeIdentifier": "21341231",
        "payeeSchemeIdentifier": "0001"
      }
    },

  
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['payee']=null;
  }
  
  /*invoiceNotes (BG-1)
  Cardinality: 0..n
  Type: Collection<InvoiceNote>
  Invoice Notes relevant to this Invoice
  Contains a set of InvoiceNote objects 

  invoiceNoteSubjectCode (BT-21)
  Cardinality: 0..1
  Type: String
  Example: "AAA"
  The subject of the textual note in BT-22.
  To be chosen from the entries in UNTDID 4451 [6]
       http://www.unece.org/trade/untdid/d11a/tred/tred4451.htm
  https://service.unece.org/trade/untdid/d21a/tred/tred4451.htm
  • invoiceNote (BT-22)
  Cardinality: 1..1
  Type: String
  Example: "A part used in repair of JPhones"
  A textual note that gives unstructured information that is relevant to the Invoice as a whole.
  Such as the reason for any correction or assignment note in case the invoice has been factored 

  "invoiceNotes": [
    {
      "invoiceNoteSubjectCode": "AAA",
      "invoiceNote": "A part used in repair of JPhones"
    }
  ],
  */
  
  
  if (isset($xml['invoiceHeader']['note_doc'])) {
    $adata['invoiceNotes']=array();
    $adata['invoiceNotes'][]=array(
      'invoiceNoteSubjectCode' => 'AAI', //AAI   General information
      'invoiceNote' => $xml['invoiceHeader']['note_doc'],
    );
    
    
  } else {
    $adata['invoiceNotes']=null;
  }
  
  
  /*receivingAdviceReference (BT-15)
  Cardinality: 0..1
  Type: String
  Example: "RA/1333/2020-10-10"
  An identifier of a referenced receiving advice
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['receivingAdviceReference']=null;
  }
  //delete me
  //$adata['receivingAdviceReference']='1cadb85a-88e7-4853-b5d0-75143b38b76e';
  
  
  /*docLevelAllowances (BG-20)
  Cardinality: 0..n
  Type: Collection<DocLevelAllowance>
  Document Level Allowances of this Invoice
  Contains a set of DocLevelAllowance objects
  B2G - GREECE
  Ειδικά, για παρακρατήσεις τύπου Φορολογίας Εισοδήματος και κρατήσεις Υπέρ Τρίτων Φορέων του Ελλην.
  Δημοσίου, δεν συμπληρώνονται από τον Πωλητή/Προμηθευτή τα αντίστοιχα αριθμητικά πεδία στο BG-20
  του Η.Τ τα οποία επηρεάζουν το τελικό συνολικό ποσό του Η.Τ. Οι συνολικές ανά κατηγορία παρακρατήσεις
  /κρατήσεις σε επίπεδο παραστατικού συμπληρώνονται από τους Πωλητές/Προμηθευτές μόνο ως κείμενο
  πληροφορίας (text) στα πεδία της ομάδας BG-24. Στις λοιπές περιπτώσεις Μείωσης Τιμής
  (allowance/discount) , η [52] default λειτουργικότητα των πεδίων στο BG-24 παραμένει ως έχει, σύμφωνα με
  τον Ευρωπαϊκό Μορφότυπο.
  Οι Μειώσεις Τιμής (allowances) είναι συνήθως κάποια μορφή έκπτωσης (discount), ενώ οι Επιβαρύνσεις
  (Charges) θα ήταν συνήθως μια μορφή υπηρεσίας η οποία παρέχεται από τον Πωλητή. Βασικά, οι Μειώσεις
  Τιμής λειτουργούν αφαιρετικά από το σύνολο του τιμολογίου και oι επιβαρύνσεις είναι προσθήκες στο
  Σύνολο Τιμολογίου. Μειώσεις Τιμής και Επιβαρύνσεις μπορούν να προκύψουν για το Παραστατικό ως
  σύνολο ή να ισχύουν για μεμονωμένα στοιχεία γραμμής ή και τα δύο.BG-20
  A group of business terms providing information about allowancesapplicable to the Invoice as a whole.
  Deductions, such as withheld tax may also be specified in this group   */
  

  
  /*• reasonCode (BT-98)
  Cardinality: 0..1
  Type: String
  Example: "62"
  The reason for the document level allowance, expressed as a code.
  Use entries of the UNTDID 5189 code list [6].
  Notes:
  o The Document level allowance reason code and the Document level allowance reason shall indicate
  the same allowance reason.
  • percentage (BT-94)
  Cardinality: 0..1
  Type: Double
  Example: 10.00
  The percentage that may be used, in conjunction with the document level allowance base amount,to
  calculate the document level allowance amount.
  Notes:
  o Decimal with 2 decimal places accuracy. Min 0 max 100
  • vatRate (BT-96)
  Cardinality: 0..1
  Type: Double
  Example: 0
  The VAT rate, represented as percentage that applies to the document level allowance
  Notes:
  o Decimal with 2 decimal places accuracy. Min 0 max 100
  • amount (BT-92)
  Cardinality: 0..1
  Type: BigDecimal
  Example: 10.00
  The amount of an allowance, without VAT.
  Notes:
  o Decimal with 2 decimal places accuracy
  o Calculated as: Round((( (BT-93) x (BT-94) ) / 100 ), 2)
  • vatCategoryCode (BT-95)
  Cardinality: 0..1
  Type: String
  Example: "S"
  A coded identification of what VAT category applies to the document level allowance.
  The following entries of UNTDID 5305 [6] are used (further clarification between brackets): - Standard rate
  (Liable for VAT in a standard way) - Zero rated goods (Liable for VAT with a percentage rate of zero) -
  
  Exempt from tax (VAT/IGIC/IPSI) - VAT Reverse Charge (Reverse charge VAT/IGIC/IPSI rules apply) -
  VAT exempt for intra community supply of goods (VAT/IGIC/IPSI not levied due to Intra-community
  supply rules) - Free export item, tax not charged (VAT/IGIC/IPSI not levied due to export outside of the
  EU) - Services outside scope of tax (Sale is not subject to VAT/IGIC/IPSI) - Canary Islands General
  Indirect Tax (Liable for IGIC tax) - Liable for IPSI (Ceuta/Melilla tax)
  • reason (BT-97)
  Cardinality: 0..1
  Type: String
  Example: "10% for Military Status"
  The reason for the document level allowance, expressed as text.
  • aadeTaxData (EG-2)
  Cardinality: 0..1
  Type: AadeTaxData
  Embeddable entity to hold AADE tax specific fields
  • baseAmount (BT-93)
  Cardinality: 0..1
  Type: BigDecimal
  Example: 100.00
  The base amount that may be used, in conjunction with the document levelallowance percentage, to
  calculate the document level allowance amount.
  Notes:
  o Decimal with 2 decimal places accuracy

  "docLevelAllowances": [
    {
      "reasonCode": "62",
      "percentage": 10.00,
      "vatRate": 0,
      "amount": 10.00,
      "vatCategoryCode": "S",
      "reason": "10% for Military Status",
      "aadeTaxData": {
        "aadeTaxType": 3,
        "aadeTaxCategory": 3
      },
      "baseAmount": 100.00
    }
  ],
    
  */

  if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['docLevelAllowances']=array();

//    if ($b2x=='b2g') {
//      if (isset($struct_data['xml']['invoiceSummary']['totalWithheldAmount']) and
//        $struct_data['xml']['invoiceSummary']['totalWithheldAmount']!=0) {
//        $adata['docLevelAllowances'][]=array(
//          'reasonCode' => '62',
//          'percentage' => 8,
//          'vatRate' => 0,
//          'amount' => $struct_data['xml']['invoiceSummary']['totalWithheldAmount'],
//          'vatCategoryCode' => 'E',
//          'reason' => 'dddd',
//          'baseAmount' => 200,
//        
//        );
//      }
//    }
    
  }
  
    
  if ($myData_XML==false) {

    $ret['message']='docLevelAllowances not set';
		debug_mail(false,$ret['message'],''); return $ret;

    
    
  }
  
  /*• contractReference (BT-12)
  Cardinality: 0..1
  Type: String
  Example: "22PROC01918132005"
  The contract identifier should be unique in the context of the specifictrading relationship and for a defined
  time period
  B2G - GREECE
  Το πεδίο BT-12 συμπληρώνεται με τον Αριθμό Διαδικτυακής Ανάρτησης Σύμβασης (ΑΔΑΜ) του Κεντρικού
  Ηλεκτρονικού Μητρώου Δημοσίων Συμβάσεων (ΚΗΜΔΗΣ), σύμφωνα με το άρθρο 38 του Ν. 4412/2016
  (Α.147) και όταν δεν υπάρχει ΑΔΑΜ συμπληρώνεται με τιμή 0. Στην περίπτωση Πιστωτικού Τιμολογίου το
  πεδίο ΒΤ-12 ειναι κενό. Ο ΑΔΑΜ είναι ο μοναδικός αριθμός που λαμβάνει το έγγραφο της σύμβασης όταν
  αναρτηθεί στο σύστημα του Κεντρικού Ηλεκτρονικού Μητρώου Δημοσίων Συμβάσεων (ΚΗΜΔΗΣ) και
  είναι ο αριθμός που χαρακτηρίζει τη Σύμβαση. Εκτός κάποιων εξαιρέσεων, όλες οι συμβάσεις στις οποίες
  συμβαλλόμενος είναι φορέας του Δημοσίου αναρτώνται στο ΚΗΜΔΗΣ Δείτε περισσότερα:ν.4412/2016
  αρθ.38 και ΥΑ 57654/2017 ΦΕΚ Β' 1781 ή http://www.eprocurement.gov.gr Αναζήτηση στοιχείων
  συμβάσεων ΚΗΜΔΗΣ: 
  http://www.eprocurement.gov.gr/kimds2/unprotected/searchRequests.htm
  */
  if ($b2x=='b2b' or $b2x=='b2g') {
    if (isset($struct_data['row']['contract_reference']) and trim_gks($struct_data['row']['contract_reference']!='')) { 
      $adata['contractReference']=trim_gks($struct_data['row']['contract_reference']);
    }
  }
  //echo '<pre>'.$adata['contractReference'];die();
  
  /*docTotal (BG-22) 
  Cardinality: 0..1
  Type: DocTotal
  Object reference of type DocTotal
  A group of business terms providing the monetary totals for the Invoice 
  
  "docTotal": {
    "documentLevelChargesSum": 10.00,
    "invoiceLinesNetAmountSum": 100.00,
    "invoiceTotalVatAmount": 33.00,
    "invoiceTotalAmountWithVat": 133.00,
    "amountDueForPayment": 33.00,
    "paidAmount": 100.00,
    "aadeDocTotals": {
      "aadeTotalDeductionsAmount": 0,
      "aadeTotalStampDutyAmount": 0,
      "aadeTotalOtherTaxesAmount": "",
      "aadeTotalFeesAmount": 0,
      "aadeTotalNetValue": 0,
      "aadeTotalGrossValue": 0,
      "aadeTotalVatAmount": 0,
      "aadeTotalWitheldAmount": 0
    },
    "roundingAmount": 0.00,
    "invoiceTotalWithoutVat": 100.00,
    "invoiceTotalVatAmountInAccountingCurrency": 33.00,
    "documentLevelAllowancesSum": 10.00,
    "exchangeRate": 1.00
  },  
  */
  $adata['docTotal']=array();
  
  

    /*• documentLevelChargesSum (BT-108)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 10.00
    Sum of all charges on document level in the Invoice.
    Charges on line level are included in the Invoice line net amount, which is summed up into the Sum of
    Invoice line net amount.
    Notes:
    o Decimal with 2 decimal places accuracy*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $adata['docTotal']['documentLevelChargesSum']=0;
      if (isset($struct_data['xml']['invoiceSummary']['totalOtherTaxesAmount']) and
          $struct_data['xml']['invoiceSummary']['totalOtherTaxesAmount']!=0) {
        $adata['docTotal']['documentLevelChargesSum']+=$struct_data['xml']['invoiceSummary']['totalOtherTaxesAmount'];
      }
      if (isset($struct_data['xml']['invoiceSummary']['totalStampDutyAmount']) and
          $struct_data['xml']['invoiceSummary']['totalStampDutyAmount']!=0) {
        $adata['docTotal']['documentLevelChargesSum']+=$struct_data['xml']['invoiceSummary']['totalStampDutyAmount'];
      }
      if (isset($struct_data['xml']['invoiceSummary']['totalFeesAmount']) and
          $struct_data['xml']['invoiceSummary']['totalFeesAmount']!=0) {
        $adata['docTotal']['documentLevelChargesSum']+=$struct_data['xml']['invoiceSummary']['totalFeesAmount'];
      }



    }
    
    //echo '<pre>ggggggggg ';print_r($xml);die();
    
    /*• invoiceLinesNetAmountSum (BT-106)
    Cardinality: 1..1
    Type: BigDecimal
    Example: 100.00
    Sum of all Invoice line net amounts in the Invoice.
    Notes:
    o Decimal with 2 decimal places accuracy*/
    ///if ($b2x=='b2b' or $b2x=='b2g') {
    $adata['docTotal']['invoiceLinesNetAmountSum']=$xml['invoiceSummary']['totalNetValue'];
                                                  //+$xml['invoiceSummary']['totalStampDutyAmount']
                                                  //+$xml['invoiceSummary']['totalOtherTaxesAmount'];
    //}
    /*• invoiceTotalVatAmount (BT-110)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 33.00
    The total VAT amount for the Invoice.
    The Invoice total VAT amount is the sum of all VAT category tax amounts
    Notes:
    o Decimal with 2 decimal places accuracy*/
    
    $adata['docTotal']['invoiceTotalVatAmount']=$xml['invoiceSummary']['totalVatAmount'];
    
    /*• invoiceTotalAmountWithVat (BT-112)
    Cardinality: 1..1
    Type: BigDecimal
    Example: 133.00
    The total amount of the Invoice with VAT.
    The Invoice total amount with VAT is the Invoice total amount without VAT plus the Invoice total VAT amount
    Notes:
    o Decimal with 2 decimal places accuracy*/
    
    //$adata['docTotal']['invoiceTotalAmountWithVat']=0;
    //if (isset($xml['invoiceSummary']['totalNetValue'])) 
    //  $adata['docTotal']['invoiceTotalAmountWithVat']+=$xml['invoiceSummary']['totalNetValue'];
    //if (isset($xml['invoiceSummary']['totalVatAmount'])) 
    //  $adata['docTotal']['invoiceTotalAmountWithVat']+=$xml['invoiceSummary']['totalVatAmount'];
    //ok
    //if (isset($xml['invoiceSummary']['totalGrossValue'])) 
    //  $adata['docTotal']['invoiceTotalAmountWithVat']=$xml['invoiceSummary']['totalGrossValue'];    
    
    if ($doc_table=='gks_acc_inv') {
      $adata['docTotal']['invoiceTotalAmountWithVat']=round($xml['invoiceSummary']['totalVatAmount_NetValue']
                                                           +$xml['invoiceSummary']['totalStampDutyAmount']
                                                           +$xml['invoiceSummary']['totalOtherTaxesAmount']
                                                           +$xml['invoiceSummary']['totalFeesAmount'],4);
    } else if ($doc_table=='gks_acc_pay') {
      $adata['docTotal']['invoiceTotalAmountWithVat']=round($xml['invoiceSummary']['totalVatAmount_NetValue'],4);
    } else if ($doc_table=='gks_whi_mov') {
      $adata['docTotal']['invoiceTotalAmountWithVat']=0;
    }
    
    /*• amountDueForPayment (BT-115)
    Cardinality: 1..1
    Type: BigDecimal
    Example: 33.00
    The outstanding amount that is requested to be paid.
    This amount is the Invoice total amount with VAT minus the paid amount that has been paid in advance.
    The amount is zero in case of a fully paid Invoice.
    Notes:
    o The amount may be negative; in that case the Seller owes the amount to the Buyer*/
    /* 
    Amount due for payment (BT-115) = 
      Invoice total amount with VAT (BT-112) 
      - Paid amount (BT-113) 
      + Rounding amount (BT-114) 
      - Document totals - BT-112
    */
    //$amountDueForPayment=$xml['invoiceSummary']['totalGrossValue'];
    //if ($b2x=='b2b' or $b2x=='b2g') {
    //  if (isset($xml['invoiceSummary']['totalGrossValue'])) 
    //    $adata['docTotal']['amountDueForPayment']=$amountDueForPayment;
    //}
    //$adata['docTotal']['amountDueForPayment']=$xml['invoiceSummary']['totalGrossValue'];
    if ($doc_table=='gks_acc_inv') {
      $adata['docTotal']['amountDueForPayment']=round($xml['invoiceSummary']['totalVatAmount_NetValue']
                                                     +$xml['invoiceSummary']['totalStampDutyAmount']
                                                     +$xml['invoiceSummary']['totalOtherTaxesAmount']
                                                     +$xml['invoiceSummary']['totalFeesAmount'],4);
    } else if ($doc_table=='gks_acc_pay') {
      $adata['docTotal']['amountDueForPayment']=round($xml['invoiceSummary']['totalVatAmount_NetValue'],4);
    } else if ($doc_table=='gks_whi_mov') {
      $adata['docTotal']['amountDueForPayment']=0;
    }
    
    //$adata['docTotal']['amountDueForPayment']=0;
    //if (isset($xml['invoiceSummary']['totalNetValue'])) 
    //  $adata['docTotal']['amountDueForPayment']+=$xml['invoiceSummary']['totalNetValue'];
    //if (isset($xml['invoiceSummary']['totalVatAmount'])) 
    //  $adata['docTotal']['amountDueForPayment']+=$xml['invoiceSummary']['totalVatAmount'];
    
    /*• paidAmount (BT-113)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 100.00
    The sum of amounts which have been paid in advance.
    This amount is subtracted from the invoice total amount with VAT to calculate the amount due for payment.
    Notes:
    o Decimal with 2 decimal places accuracy*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $adata['docTotal']['paidAmount']=0;
    }

    /*• aadeDocTotals (EG-4)
    Cardinality: 0..1
    Type: AadeDocTotals
    Embeddable entity to hold AADE document totals
    
      • aadeTotalDeductionsAmount (ET-24)
      Cardinality: 1..1
      Type: BigDecimal
      Example: 0
      Total Deductions Amount
      Notes:
      o minimum value 0, 2 decimal places accuracy
      • aadeTotalStampDutyAmount (ET-20)
      Cardinality: 1..1
      Type: BigDecimal
      Example: 0
      Stamp Duty Total Amount
      Notes:
      o minimum value 0, 2 decimal places accuracy
      • aadeTotalOtherTaxesAmount
      Cardinality: 1..1
      Type: BigDecimal
      Total Other Taxes Amount
      • aadeTotalFeesAmount (ET-22)
      Cardinality: 1..1
      Type: BigDecimal
      Example: 0
      Total Fees Amount
      Notes:
      o minimum value 0, 2 decimal places accuracy
      • aadeTotalNetValue (ET-46)
      Cardinality: 1..1
      Type: BigDecimal
      Example: 0
      Total Net Value
      Notes:
      o minimum value 0, 2 decimal places accuracy
      • aadeTotalGrossValue (ET-25)
      Cardinality: 1..1
      Type: BigDecimal
      Example: 0
      Total Gross Value
      Notes:
      o minimum value 0, 2 decimal places accuracy
      • aadeTotalVatAmount (ET-47)
      Cardinality: 1..1
      Type: BigDecimal
      Example: 0
      Total VAT Amount
      Notes:
      o minimum value 0, 2 decimal places accuracy
      • aadeTotalWitheldAmount (ET-21)
      Cardinality: 1..1
      Type: BigDecimal
      Example: 0
      Total Withheld Amount
      Notes:
      o minimum value 0, 2 decimal places accuracy*/


  if ($myData_XML) {
    //$adata['docTotal']['aadeDocTotals']=null;
  } else {
    $ret['message']='docTotal aadeDocTotals not set';
		debug_mail(false,$ret['message'],''); return $ret;
  }
  
    /*• roundingAmount (BT-114)
    Cardinality: 0..1
    Embeddable entity to hold AADE Document TotalsShould be filled according to AADE Specifications 
    Type: BigDecimal
    Example: 0.00
    The amount to be added to the invoice total to round the amount to be paid.
    Notes:
    o Decimal with 2 decimal places accuracy*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $adata['docTotal']['roundingAmount']=0;
    }
    /*• invoiceTotalWithoutVat (BT-109)
    Cardinality: 1..1
    Type: BigDecimal
    Example: 100.00
    The total amount of the Invoice without VAT.
    The Invoice total amount without VAT is the Sum of Invoice line net amount 
    minus Sum of allowances on document level 
    plus Sum of charges on document level.
    Notes:
    o Decimal with 2 decimal places accuracy*/
    //if (isset($xml['invoiceSummary']['totalNetValue'])) 
    
    if ($doc_table=='gks_acc_inv') {
      $adata['docTotal']['invoiceTotalWithoutVat']=round($xml['invoiceSummary']['totalNetValue']
                                                        +$xml['invoiceSummary']['totalStampDutyAmount']
                                                        +$xml['invoiceSummary']['totalOtherTaxesAmount']
                                                        +$xml['invoiceSummary']['totalFeesAmount'],4);
    } else if ($doc_table=='gks_acc_pay') {
      $adata['docTotal']['invoiceTotalWithoutVat']=round($xml['invoiceSummary']['totalNetValue'],4);
    } else if ($doc_table=='gks_whi_mov') {
      $adata['docTotal']['invoiceTotalWithoutVat']=0;
    }      
    
    
    //$adata['docTotal']['invoiceTotalWithoutVat']= $amountDueForPayment - $adata['docTotal']['invoiceTotalVatAmount'];
    
    
    /*• invoiceTotalVatAmountInAccountingCurrency (BT-111)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 33.00
    The VAT total amount expressed in the accounting currency accepted or required in the country of the
    Seller.
    To be used when the VAT accounting currency (BT-6) differs from the Invoice currency code (BT-5) in
    accordance with article 230 of Directive 2006/112 / EC on VAT.
    Notes:
    o The VAT amount in accounting currency is not used in the calculation of the Invoice totals*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $adata['docTotal']['invoiceTotalVatAmountInAccountingCurrency']=null;
    }
    
    /*• documentLevelAllowancesSum (BT-107)
    Cardinality: 0..1
    Type: BigDecimal
    Example: 10.00
    Sum of all allowances on document level in the Invoice.
    Allowances on line level are included in the Invoice line net amount, which is summed up into the Sum of
    Invoice line net amount
    Notes:
    o Decimal with 2 decimal places accuracy*/
    if ($b2x=='b2b' or $b2x=='b2g') {
      $adata['docTotal']['documentLevelAllowancesSum']=0;
      //if (isset($struct_data['xml']['invoiceSummary']['totalWithheldAmount']) and
      //   $struct_data['xml']['invoiceSummary']['totalWithheldAmount']!=0) {
      //  $adata['docTotal']['documentLevelAllowancesSum']+=$struct_data['xml']['invoiceSummary']['totalWithheldAmount'];
      //}
      //if (isset($struct_data['xml']['invoiceSummary']['totalDeductionsAmount']) and
      //   $struct_data['xml']['invoiceSummary']['totalDeductionsAmount']!=0) {
      //  $adata['docTotal']['documentLevelAllowancesSum']+=$struct_data['xml']['invoiceSummary']['totalDeductionsAmount'];
      //}
                  


    }





  //todo
  //$struct_data['xml']['invoiceHeader']['correlatedInvoices']
  
  //todo auta einai mesa sto aadeData alla to aadeData den to stelno
  //$struct_data['xml']['invoiceHeader']['dispatchDate']
  //$struct_data['xml']['invoiceHeader']['dispatchTime']
  //$struct_data['xml']['invoiceHeader']['vehicleNumber']
  //$struct_data['xml']['invoiceHeader']['movePurpose']
  
 
  
  
	//print '<pre>adata ';print_r($adata);die();
	
	//print '<pre>';print_r($struct_data);die();
	//print '<pre>';print_r($xml);die();


	

/*
	$adata='{

  "b2g": true,
  "serialNumber": "1333335",
  "sellerIdentifiers": [
    {
      "sellerIdentifier": "EL177472438"
    }
  ],
  "seller": {
    "sellerVatIdentifier": "EL177472438",
    "sellerName": "TEST IKE",
    "sellerContact": {
      "sellerContactEmail": "testb2g@ilyda.com",
      "sellerContactPoint": "ΧΡΗΣΤΗΣ ΑΝΑΠΤΥΞΗΣ",
      "sellerContactPhoneNumber": "210-6705000"
    },
    "sellerElectronicAddress": null,
    "sellerLegalRegistrationIdentifier": {
      "sellerLegalRegistrationIdentifier": "ΑΡ ΓΕΜΗ"
    },
    "sellerPostalAddress": {
      "sellerCountrySubdivision": "Αττική",
      "sellerCountryCode": "GR",
      "sellerAddressLine1": "Adrianeiou 29",
      "sellerAddressLine3": "",
      "sellerAddressLine2": "Psychiko",
      "sellerPostCode": "11525",
      "sellerCity": "ΑΘΗΝΑ"
    },
    "sellerTaxRegistrationIdentifier": null,
    "branch": 0
  },
  "projectReference": "3|1234567",
  "dispatchAdviceReference": "",
  "buyerAccountingReference": null,
  "selfPricing": false,
  "invoiceTypeCode": "380",
  "delivery": {
    "partyName": "ΤΕΣΤ ΓΓΠΣ",
    "actualDeliveryDate": null,
    "deliveryInvoicingPeriod": null,
    "deliveryAddress": {
      "deliveryAddressLine1": "ΧΡ ΛΑΔΑ 66",
      "deliveryAddressLine2": "ΑΤΤΙΚΗΣ",
      "deliveryAddressLine3": "",
      "deliveryCity": "ΑΘΗΝΑ",
      "deliveryPostCode": "11525",
      "deliveryCountrySubdivision": "Ελλάδα",
      "deliveryCountryCode": "GR"
    },
    "deliveryLocationIdentifier": null
  },
  "vatBreakdowns": [
    {
      "aadeVatData": {
        "aadeVatExemptionCategory": null,
        "aadeVatCategory": 1
      },
      "categoryCode": "S",
      "categoryRate": 24.0,
      "categoryTaxAmount": 48.0,
      "categoryTaxableAmount": 200.0,
      "exemptionReasonCode": null,
      "exemptionReasonText": null,
      "invoice": null
    }
  ],
  "seriesNumber": "Α",
  "vatPointDateCode": null,
  "vatPaidByBuyer": false,
  "precedingInvoices": null,
  "creditTransfers": null,
  "paymentDueDate": null,
  "creationDate": "2023-12-20T20:55:08",
  "invoiceNumber": null,
  "paymentMethods": null,
  "invoiceLines": [
    {
      "lineNumber": 1,
      "note": "",
      "objectIdentifier": null,
      "objectIdentifierScheme": null,
      "invoicedQuantity": 2.0,
      "invoicedQuantityUnits": "10",
      "netAmount": 200.0,
      "purchaseOrderLineReference": "",
      "buyerAccountingReference": null,
      "discountPercentage1": 0.0,
      "discountPercentage2": 0.0,
      "discountPercentage3": 0.0,
      "discountAmount": 0.0,
      "discountTotalAmount": 0.0,
      "isAadeSynopsis": null,
      "itemInfo": {
        "itemInfoName": "Test item 1",
        "itemInfoDescription": "Test item 1",
        "sellerIdentifier": "101",
        "buyerIdentifier": null,
        "standardIdentifier": null,
        "standardIdentifierScheme": null,
        "countryOfOrigin": "AU"
      },
      "lineVatInfo": {
        "vatCategoryCode": "S",
        "vatAmount": 48.0,
        "vatRate": 24.0,
        "aadeVatData": {
          "aadeVatExemptionCategory": "",
          "aadeVatCategory": 1
        }
      },
      "invoiceLinePeriod": null,
      "priceDetails": {
        "itemNetPrice": 100.0,
        "itemPriceDiscount": 0.0,
        "itemGrossPrice": null,
        "itemPriceBaseQuantity": 1.0,
        "itemPriceBaseQuantityUnitsCode": null
      },
      "invoiceLineAllowances": [],
      "invoiceLineCharges": null,
      "itemClassificationIdentifiers": [
        {
          "classificationIdentifier": "03100000-2",
          "classificationIdentifierScheme": "STI",
          "classificationIdentifierSchemeVersion": null
        },
        {
          "classificationIdentifier": "46477",
          "classificationIdentifierScheme": "STT",
          "classificationIdentifierSchemeVersion": null
        },
        {
          "classificationIdentifier": "25.55",
          "classificationIdentifierScheme": "ZZZ",
          "classificationIdentifierSchemeVersion": null
        },
        {
          "classificationIdentifier": "2820007785728",
          "classificationIdentifierScheme": "ZZZ",
          "classificationIdentifierSchemeVersion": null
        }
      ]
    }
  ],
  "sellerTaxRepresentative": {
    "sellerTaxRepresentativeName": "Akis Chourdakis",
    "sellerTaxRepresentativePostalAddress": {
      "sellerTaxRepresentativeCountrySubdivision": "Attiki",
      "sellerTaxRepresentativeCity": "Lagonisi",
      "sellerTaxRepresentativePostCode": "19010",
      "sellerTaxRepresentativeCountryCode": "GR",
      "sellerTaxRepresentativeAddressLine1": "karaiskaki 11",
      "sellerTaxRepresentativeAddressLine2": "",
      "sellerTaxRepresentativeAddressLine3": ""
    },
    "sellerTaxRepresentativeVatIdentifier": "EL126472807"
  },
  "purchaseOrderReference": "",
  "schemeIdentifier": null,
  "buyerReference": "Νοσοκ ΕΥΑΓΓΕΛΙΣΜΟΣ|XYZ123ABC",
  "invoiceIssueDate": "2023-12-21T00:00:00",
  "paymentTerms": "ΕΠΙ ΠΙΣΤΩΣΕΙ",
  "processControl": null,
  "docLevelCharges": [],
  "invoiceCurrencyCode": "EUR",
  "paymentInstruction": null,
  "mark": null,
  "invoicedObjectIdentifier": null,
  "buyerIdentifiers": [
    {
      "buyerIdentifier": ""
    }
  ],
  "tenderOrLotReference": null,
  "aadeData": null,
  "buyer": {
    "buyerPostalAddress": {
      "buyerCountryCode": "GR",
      "buyerAddressLine1": "ΧΡ ΛΑΔΑ 66",
      "buyerPostCode": "11525",
      "buyerAddressLine2": "ΑΤΤΙΚΗΣ",
      "buyerCity": "ΑΘΗΝΑ",
      "buyerCountrySubdivision": "Ελλάδα",
      "buyerAddressLine3": ""
    },
    "buyerVatIdentifier": "EL997001671",
    "buyerElectronicAddress": {
      "buyerElectronicAddress": "997001671",
      "buyerElectronicAddressSchemeIdentifier": "9933"
    },
    "buyerTradingName": "ΚΑΠΟΔΙΣΤΡΙΑΚΟ",
    "buyerName": "ΤΕΣΤ ΓΓΠΣ",
    "buyerLegalRegistrationIdentifier": {
      "buyerLegalRegistrationIdentifier": ""
    },
    "buyerBranch": 0,
    "buyerContact": {
      "buyerContactPoint": "",
      "buyerContactPhoneNumber": "21099999999",
      "buyerContactEmail": "testb2g@gsis.com"
    }
  },
  "vatAccountingCurrencyCode": null,
  "vatPointDate": null,
  "additionalSupportDocs": [
    {
      "reference": "PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjxJbnZvaWNlc0RvYyB4bWxucz0iaHR0cDovL3d3dy5hYWRlLmdyL215REFUQS9pbnZvaWNlL3YxLjAiIHhzaTpzY2hlbWFMb2NhdGlvbj0iaHR0cDovL3d3dy5hYWRlLmdyL215REFUQS9pbnZvaWNlL3YxLjAgc2NoZW1hLnhzZCIgeG1sbnM6aWNscz0iaHR0cHM6Ly93d3cuYWFkZS5nci9teURBVEEvaW5jb21lQ2xhc3NpZmljYXRvbi92MS4wIiB4bWxuczplY2xzPSJodHRwczovL3d3dy5hYWRlLmdyL215REFUQS9leHBlbnNlc0NsYXNzaWZpY2F0b24vdjEuMCIgeG1sbnM6eHNpPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxL1hNTFNjaGVtYS1pbnN0YW5jZSI+DQogICAgPGludm9pY2U+DQogICAgPHVpZD48L3VpZD4NCiAgICA8bWFyaz4wPC9tYXJrPg0KICAgIDxpc3N1ZXI+DQogICAgPHZhdE51bWJlcj4xNzc0NzI0Mzg8L3ZhdE51bWJlcj4NCiAgICA8Y291bnRyeT5HUjwvY291bnRyeT4NCiAgICA8YnJhbmNoPjA8L2JyYW5jaD4NCiAgICA8L2lzc3Vlcj4NCiAgICA8Y291bnRlcnBhcnQ+DQogICAgPHZhdE51bWJlcj45OTcwMDE2NzE8L3ZhdE51bWJlcj4NCiAgICA8Y291bnRyeT5HUjwvY291bnRyeT4NCiAgICA8YnJhbmNoPjA8L2JyYW5jaD4NCiAgICA8YWRkcmVzcz4NCiAgICAgICAgPHN0cmVldD7Op86hIM6bzpHOlM6RIDY2PC9zdHJlZXQ+DQogICAgICAgIDxudW1iZXI+PC9udW1iZXI+DQogICAgICAgIDxwb3N0YWxDb2RlPjExNTI1PC9wb3N0YWxDb2RlPg0KICAgICAgICA8Y2l0eT7Okc6YzpfOnc6RPC9jaXR5Pg0KICAgIDwvYWRkcmVzcz4NCiAgICA8L2NvdW50ZXJwYXJ0Pg0KICAgIDxpbnZvaWNlSGVhZGVyPg0KICAgIDxzZXJpZXM+zpE8L3Nlcmllcz4NCiAgICA8YWE+MTMzMzMzNTwvYWE+DQogICAgPGlzc3VlRGF0ZT4yMDIzLTEyLTIxPC9pc3N1ZURhdGU+DQogICAgPGludm9pY2VUeXBlPjEuMTwvaW52b2ljZVR5cGU+DQogICAgPHZhdFBheW1lbnRTdXNwZW5zaW9uPmZhbHNlPC92YXRQYXltZW50U3VzcGVuc2lvbj4NCiAgICA8Y3VycmVuY3k+RVVSPC9jdXJyZW5jeT4NCiAgICA8ZGlzcGF0Y2hEYXRlPjIwMjMtMTItMjE8L2Rpc3BhdGNoRGF0ZT4NCiAgICA8ZGlzcGF0Y2hUaW1lPjIwOjU1OjAwPC9kaXNwYXRjaFRpbWU+DQogICAgPHZlaGljbGVOdW1iZXI+zqXOkc6SMzg1NzwvdmVoaWNsZU51bWJlcj4NCiAgICA8bW92ZVB1cnBvc2U+MTwvbW92ZVB1cnBvc2U+DQogICAgPC9pbnZvaWNlSGVhZGVyPg0KICAgIDxwYXltZW50TWV0aG9kcz4NCiAgICA8cGF5bWVudE1ldGhvZERldGFpbHM+DQogICAgICAgIDx0eXBlPjU8L3R5cGU+DQogICAgICAgIDxhbW91bnQ+MjQ4LjAwPC9hbW91bnQ+DQogICAgPC9wYXltZW50TWV0aG9kRGV0YWlscz4NCiAgICA8L3BheW1lbnRNZXRob2RzPg0KICAgIDxpbnZvaWNlRGV0YWlscz4NCiAgICA8bGluZU51bWJlcj4xPC9saW5lTnVtYmVyPg0KICAgICAgICA8cXVhbnRpdHk+Mi4wMDwvcXVhbnRpdHk+DQogICAgICAgIDxtZWFzdXJlbWVudFVuaXQ+MTwvbWVhc3VyZW1lbnRVbml0Pg0KICAgICAgICA8bmV0VmFsdWU+MjAwLjAwPC9uZXRWYWx1ZT4NCiAgICAgICAgPHZhdENhdGVnb3J5PjE8L3ZhdENhdGVnb3J5Pg0KICAgICAgICA8dmF0QW1vdW50PjQ4LjAwPC92YXRBbW91bnQ+DQogICAgICAgIDxsaW5lQ29tbWVudHM+PC9saW5lQ29tbWVudHM+DQogICAgICA8aW5jb21lQ2xhc3NpZmljYXRpb24+DQogICAgICAgICAgPGljbHM6Y2xhc3NpZmljYXRpb25UeXBlPkUzXzU2MV8wMDc8L2ljbHM6Y2xhc3NpZmljYXRpb25UeXBlPg0KICAgICAgICAgIDxpY2xzOmNsYXNzaWZpY2F0aW9uQ2F0ZWdvcnk+Y2F0ZWdvcnkxXzE8L2ljbHM6Y2xhc3NpZmljYXRpb25DYXRlZ29yeT4NCiAgICAgICAgICA8aWNsczphbW91bnQ+MjAwLjAwPC9pY2xzOmFtb3VudD4NCiAgICAgIDwvaW5jb21lQ2xhc3NpZmljYXRpb24+DQogICAgPC9pbnZvaWNlRGV0YWlscz4NCiAgICA8aW52b2ljZVN1bW1hcnk+DQogICAgPHRvdGFsTmV0VmFsdWU+MjAwLjAwPC90b3RhbE5ldFZhbHVlPg0KICAgIDx0b3RhbFZhdEFtb3VudD40OC4wMDwvdG90YWxWYXRBbW91bnQ+DQogICAgPHRvdGFsV2l0aGhlbGRBbW91bnQ+MC4wMDwvdG90YWxXaXRoaGVsZEFtb3VudD4NCiAgICA8dG90YWxGZWVzQW1vdW50PjAuMDA8L3RvdGFsRmVlc0Ftb3VudD4NCiAgICA8dG90YWxTdGFtcER1dHlBbW91bnQ+MC4wMDwvdG90YWxTdGFtcER1dHlBbW91bnQ+DQogICAgPHRvdGFsT3RoZXJUYXhlc0Ftb3VudD4wLjAwPC90b3RhbE90aGVyVGF4ZXNBbW91bnQ+DQogICAgPHRvdGFsRGVkdWN0aW9uc0Ftb3VudD4wLjAwPC90b3RhbERlZHVjdGlvbnNBbW91bnQ+DQogICAgPHRvdGFsR3Jvc3NWYWx1ZT4yNDguMDA8L3RvdGFsR3Jvc3NWYWx1ZT4NCiAgICAgIDxpbmNvbWVDbGFzc2lmaWNhdGlvbj4NCiAgICAgICAgICA8aWNsczpjbGFzc2lmaWNhdGlvblR5cGU+RTNfNTYxXzAwNzwvaWNsczpjbGFzc2lmaWNhdGlvblR5cGU+DQogICAgICAgICAgPGljbHM6Y2xhc3NpZmljYXRpb25DYXRlZ29yeT5jYXRlZ29yeTFfMTwvaWNsczpjbGFzc2lmaWNhdGlvbkNhdGVnb3J5Pg0KICAgICAgICAgIDxpY2xzOmFtb3VudD4yMDAuMDA8L2ljbHM6YW1vdW50Pg0KICAgICAgICAgIDxpY2xzOmlkPjE8L2ljbHM6aWQ+DQogICAgICA8L2luY29tZUNsYXNzaWZpY2F0aW9uPg0KICAgIDwvaW52b2ljZVN1bW1hcnk+DQogICAgPC9pbnZvaWNlPg0KPC9JbnZvaWNlc0RvYz4NCg==",
      "attachedDocument": null,
      "description": "##InvoicesDoc##",
      "location": null
    },
    {
      "reference": "3.00",
      "description": "##PARAKRAT|FOR|EISOD|2##"
    },
    {
      "reference": "4.00",
      "description": "##PARAKRAT|YPER3##"
    }
  ],
  "payee": null,
  "invoiceNotes": null,
  "receivingAdviceReference": null,
  "docLevelAllowances": [],
  "contractReference": "20SYMV006467658",
  "docTotal": {
    "aadeDocTotals": null,
    "amountDueForPayment": 248.0,
    "documentLevelAllowancesSum": 0.0,
    "documentLevelChargesSum": 0.0,
    "exchangeRate": 0.0,
    "invoiceLinesNetAmountSum": 200.0,
    "invoiceTotalAmountWithVat": 248.0,
    "invoiceTotalVatAmount": 48.0,
    "invoiceTotalVatAmountInAccountingCurrency": null,
    "invoiceTotalWithoutVat": 200.0,
    "paidAmount": 0.0,
    "roundingAmount": 0.0
  },
  "salesOrderReference": null
}';
$adata=json_decode($adata,true);
*/	
  
	//$adata['p_notes']=''; //Seimeioseis
	
  //print '<pre>';print_r($adata);die();
  //print '<pre>';echo json_encode($adata,JSON_PRETTY_PRINT);die();
	
  //$ret['file_data']=array(
  //  'data' => $adata,
  //);
  $ret['file_data']=$adata;
  
  
  
  
  $ret['paroxos_signature_id_array']=$paroxos_signature_id_array;
  $ret['message']='OK';
  $ret['success']=true;

  return $ret;	
}



function gks_paroxos_invoice_xml_send_ilyda_com($id,$paroxos_params,$struct_data,$file_data) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;	
  global $gks_cache_version;
  
	$ret = array('success' => false, 'message' => 'generic error');

  
  //echo '<pre>';echo $id; die();
  //echo '<pre>';print_r($paroxos_params); die();
  //echo '<pre>';print_r($struct_data); die();
  //echo '<pre>ddddddddddd ';print_r($file_data); die();

	if (isset($paroxos_params['pc_username'])==false or trim_gks($paroxos_params['pc_password'])=='') {
	  $ret['message']=gks_lang('Δεν έχει ορισθεί το Όνομα Χρήστη/Κωδικός Πρόσβασης για τον πάροχο');return $ret;
	}

  $doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
    $ttt='acc_inv';
    $rrr='inv_acc';      
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
    $ttt='acc_pay';
    $rrr='pay_acc'; 
  } else if ($doc_table=='gks_whi_mov') {
    $xxx='mov';
    $ttt='whi_mov'; 
    $rrr='mov_whi'; 
  } else {
    $xxx='';
  }
  
	
	//echo '<pre>ddddddddddd ';print_r($file_data); die();

	$input=$file_data;
	$input[$ttt.'_id']=$id;
	$input['id_company_paroxos']=$paroxos_params['id_company_paroxos'];
  //$input['paroxos_token']=$paroxos_params['pc_key'];
  //$input['paroxos_url']=$ret_token['pc_url1'];
  $input['paroxos_live']=$paroxos_params['paroxos_mydata_live'];
  $input['paroxos_pc_username']=$paroxos_params['pc_username'];
  $input['paroxos_pc_password']=$paroxos_params['pc_password'];
	


	$input_to_raw=$input;
  $ret_send = gks_paroxos_ilyda_com_get_url('/api/invoice','POST',$input);
  
  
  if ($ret_send['success']==false and 
      isset($ret_send['http_code']) and 
      in_array($ret_send['http_code'],[0,404])) {
//    echo '<pre>skalose !!!!!</pre>';
//    echo '<pre>11111111 ';print_r($input); echo '</pre>'; 
//    echo '<pre>22222222 ';print_r($paroxos_params); echo '</pre>'; 
//    echo '<pre>33333333 ';print_r($struct_data); echo '</pre>'; 
    
    
    
    $totalGrossValue=false;
    if ($doc_table=='gks_acc_inv' and isset($struct_data['xml']['invoiceSummary']['totalGrossValue'])) {
      $totalGrossValue=$struct_data['xml']['invoiceSummary']['totalGrossValue'];  
    } else if ($doc_table=='gks_acc_pay') {
      $totalGrossValue=$struct_data['xml']['invoiceSummary']['totalNetValue'];  
    } else if ($doc_table=='gks_whi_mov') {
      $totalGrossValue=$struct_data['xml']['invoiceSummary']['totalNetValue'];  
    }
    
    if (isset($struct_data['xml']['issuer']['vatNumber']) and $struct_data['xml']['issuer']['vatNumber']<>'' and
        isset($struct_data['xml']['issuer']['country']) and $struct_data['xml']['issuer']['country']<>'' and
        isset($struct_data['xml']['invoiceHeader']['issueDate_iso_8601']) and $struct_data['xml']['invoiceHeader']['issueDate_iso_8601']<>'' and 
        isset($struct_data['xml']['invoiceHeader']['series']) and $struct_data['xml']['invoiceHeader']['series']<>'' and
        isset($struct_data['xml']['invoiceHeader']['aa']) and intval($struct_data['xml']['invoiceHeader']['aa'])>0 and
        isset($struct_data['xml']['invoiceHeader']['invoiceType']) and $struct_data['xml']['invoiceHeader']['invoiceType']<>'' and 
        isset($struct_data['xml']['invoiceSummary']['totalNetValue']) and floatval ($struct_data['xml']['invoiceSummary']['totalNetValue'])>=0 and 
        isset($struct_data['xml']['invoiceSummary']['totalVatAmount']) and floatval($struct_data['xml']['invoiceSummary']['totalVatAmount'])>=0 and
        $totalGrossValue!==false and floatval($totalGrossValue)>=0) {
        
      $mynow=date('Y-m-d H:i:s');
      $sql_tf1="select keyIdentifier,secret,algorithm,linkBaseUrl from gks_paroxos_tf1_keys 
      where paroxos_id=8
      and afm='".$db_link->escape_string($struct_data['xml']['issuer']['vatNumber'])."'
      and local_status='ACTIVE'
      and status='VERIFIED'
      and validFrom<'".$mynow."'
      and validTo>'".$mynow."'
      and revokedAt is null
      order by installationVerifiedAt desc limit 1";
      //echo '<pre>sqlsqlsqlsql '.$sql_tf1.'</pre>';
      $result_tf1 = $db_link->query($sql_tf1); 
  	  if (!$result_tf1) {
  	    debug_mail(false,'error sql',$sql_tf1);
  	    return array('success' => false, 'message' => 'sql error');}
      if ($result_tf1->num_rows==1) {
        $row_tf1 = $result_tf1->fetch_assoc();
      
        $kid=$row_tf1['keyIdentifier'];
        $secret=$row_tf1['secret'];
        $algorithm=$row_tf1['algorithm'];
        $linkBaseUrl=$row_tf1['linkBaseUrl'];
        
        $payload=array(
          'sellerVat'=> ($struct_data['xml']['issuer']['country']=='GR' ? 'EL' : '').$struct_data['xml']['issuer']['vatNumber'],
          'sellerBranch'=> $paroxos_params['paroxos_branch'],
          'invoiceIssueDate'=> $struct_data['xml']['invoiceHeader']['issueDate_iso_8601_r'],
          'seriesNumber'=> $struct_data['xml']['invoiceHeader']['series'],
          'serialNumber'=> $struct_data['xml']['invoiceHeader']['aa'],
          'aadeInvoiceTypeCode'=> $struct_data['xml']['invoiceHeader']['invoiceType'],
          'netAmount'=> $struct_data['xml']['invoiceSummary']['totalNetValue'],
          'vatAmount'=> $struct_data['xml']['invoiceSummary']['totalVatAmount'],
          'grossAmount'=> $totalGrossValue,
        );
        
        if (isset($struct_data['xml']['counterpart']['vatNumber']) and $struct_data['xml']['counterpart']['vatNumber']<>'' and
            isset($struct_data['xml']['counterpart']['country']) and $struct_data['xml']['counterpart']['country']<>'') {
          $payload['buyerVatNumber']=($struct_data['xml']['counterpart']['country']=='GR' ? 'EL' : '').$struct_data['xml']['counterpart']['vatNumber'];
        }
        
        //echo '<pre>33333333 ';print_r($payload); echo '</pre>'; 
        
        $header = ['alg' => 'HS256', 'typ' => 'OFFLINE_QR_JWS', 'kid' => $kid];
        $token = gks_paroxos_ilyda_com_sign_tf1_token($header, $payload, $secret);
    
        //echo '<pre>'.$token.'</pre>';
        if ($token<>'') {
          $paroxos_tf1_url=$linkBaseUrl.'/'.$algorithm.'/'.$token;
      		$sql_xxx="update ".$doc_table." set
      	  paroxos_tf1_url='".$db_link->escape_string($paroxos_tf1_url)."',
      	  paroxos_tf1_url_has=1
      		where id_".$ttt."=".$id;
      	  $result_xxx = $db_link->query($sql_xxx); 
    	    if (!$result_xxx) {
      	    debug_mail(false,'error sql',$sql_xxx);
      	    return array('success' => false, 'message' => 'sql error');}
      	    
      	    
      	    
      	    
      	  $ret['message']=$ret_send['message'];
      	  $ret['message'].='<br>'.
      	  gks_lang('<b>Ωστόσο</b> έχει δημιουργηθεί ένα QR Code με σύνδεσμο στον πάροχο και μπορεί να χρησιμοποιηθεί στην εκτύπωση που θα <b>πρέπει</b> να δώσετε στον λήπτη. O λήπτης θα μπορεί να δει τα βασικά στοιχεία αυτού του παραστατικού. Εσείς από την πλευρά σας θα πρέπει να στείλετε το παραστατικό στον πάροχο εντός 24 ωρών, όταν αποκατασταθεί η επικοινωνία του gks ERP με τον πάροχο. Κάντε κλικ επάνω στο QR Code για να μεταβείτε στον σύνδεσμο.');
      	  $qr_paroxos_tf1_url=gks_qr_code_generate($paroxos_tf1_url);
          $ret['message'].='<br><a href="'.$paroxos_tf1_url.'" target="_blank" class="gks_aade_paroxos_tf1_qrurl">'.
                '<img src="'.$qr_paroxos_tf1_url.'">'.
              '</a>';
          
          $ret['paroxos_tf1_active']=true;    
      	  return $ret;
      	    
    	  }
      }
    }
  }
  
  //echo '<pre>ssssssssssss '.$id.' ';print_r($ret_send); die();
  
  
  
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}


	
	$response_array=$ret_send['response_array'];
	if (isset($response_array['gks_ok'])==false) {$ret['message']=gks_lang('Σφάλμα αποστολής').' (34239322421a)';return $ret;}

//  echo '<pre>';
//  var_dump($response_array['errors']);
//  echo "\r\n";
//  var_dump($response_array['errors']===null);
//  echo "\r\n";
//  var_dump($response_array['errors']==='');
//  echo "\r\n";
//  var_dump($response_array['errors']==='null');
//  echo "\r\n";
//  die();
  
	$paroxos_status=false;
	$transmission_failure='';
	if (isset($response_array['invoiceMarking']) and 
	    isset($response_array['invoiceMarking']['mark']) and
	    strlen($response_array['invoiceMarking']['mark'])>=8 and
	    array_key_exists('errors',$response_array) and 
	    (
	      (is_array($response_array['errors']) and count($response_array['errors'])==0) or 
	      $response_array['errors']===null or 
	      $response_array['errors']==='' or 
	      $response_array['errors']==='null'
	    )
	  ) {
	  $paroxos_status=true;      
  }
  
  
	if (isset($response_array['invoiceMarking']) and 
	    isset($response_array['invoiceMarking']['mark']) and
	    strlen($response_array['invoiceMarking']['mark'])>=8 and
	    array_key_exists('errors',$response_array) and 
	    (
	      (is_array($response_array['errors']) and 
	      count($response_array['errors'])==1) and 
	      $response_array['errors'][0]['code']==='I0008'
	    )
	  ) {
	  $paroxos_status=true;      
	  $transmission_failure='I0008';
  }
    
    
	if (isset($response_array['invoiceMarking']) and
	    array_key_exists('mark',$response_array['invoiceMarking']) and
	    empty($response_array['invoiceMarking']['mark']) and
	    array_key_exists('errors',$response_array) and 
      is_array($response_array['errors']) and 
      count($response_array['errors'])==3 and (
       ($response_array['errors'][0]['code']==='MQ002' or
        $response_array['errors'][1]['code']==='MQ002' or
        $response_array['errors'][2]['code']==='MQ002') and
        
       ($response_array['errors'][0]['code']==='I9999' or
        $response_array['errors'][1]['code']==='I9999' or
        $response_array['errors'][2]['code']==='I9999') and
        
       ($response_array['errors'][0]['code']==='I0004' or
        $response_array['errors'][1]['code']==='I0004' or
        $response_array['errors'][2]['code']==='I0004')
	    )
	  ) {
	  $paroxos_status=true;      
	  $transmission_failure='I0004';
  }
  
  //echo '<pre>ggggggggg|'.$paroxos_status.'|'.$transmission_failure.'|';die();
  
  //gia to MQ001 den xreiazetai na kano kati, einai lathos kai apla to emfanizei
  
  if (isset($response_array['errors']) and is_array($response_array['errors']) and count($response_array['errors'])>0) {
    $html_error='<table class="table table-sm table-responsive1 table-striped table-bordered" style="width: 100%;font-size:0.8rem;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">'.
    '<thead>'.
    '<tr>'.
    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κωδικός').'</th>'.
    '<th class="table-dark" scope="col" style="text-align: left !important;" width="100%">'.gks_lang('Περιγραφή').'</th>'.
    '</tr>'.
    '</thead>'.
    '<tbody>';
    $tr_aa=0;
    foreach ($response_array['errors'] as $value) {
      $tr_aa++;
      $td_code=''; if (isset($value['code'])) $td_code=trim_gks($value['code']);
      $td_message=array(); 
      if (isset($value['defaultMessage']) and $value['defaultMessage']<>'') $td_message[]=htmlspecialchars(trim_gks($value['defaultMessage']));
      if (isset($value['aadeMessage']) and $value['aadeMessage']<>'') $td_message[]=htmlspecialchars(trim_gks($value['aadeMessage']));
      
      if (isset($value['errorFields']) and is_array($value['errorFields']) and count($value['errorFields'])>0) {
        foreach ($value['errorFields'] as $valueef) {
          $temp='';
          if (isset($valueef['field'])) $temp.=gks_lang('Πεδίο').': '.$valueef['field'].' ';
          if (isset($valueef['value'])) $temp.=gks_lang('Τιμή').': '.$valueef['value'];
          $td_message[]=htmlspecialchars(trim_gks($temp));
        }   
      }
      
      $html_error.=
      '<tr>'.
        '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
        '<td nowrap style="text-align:center">'.htmlspecialchars($td_code).'</td>'.
        '<td  style="text-align:left">'.implode('<br>',$td_message).'</td>'.
      '</tr>';
      
      
      
    } 
    
    $html_error.='</tbody></table>';
    
    if ($ret_send['message']=='OK') {
      $ret_send['message']=$html_error;
    } else {
      $ret_send['message'].=$html_error;
    } 
    
  }

  //echo '<pre>ggggggggg|'.$paroxos_status.'|'.$transmission_failure.'|'.$ret_send['message'];die();


  //echo '<pre>'; print_r($ret_send['message']);die();
  //echo '<pre>';print_r($response_array);die();

  if ($doc_table=='gks_acc_inv') $sub_dir='acc/inv/';
  else if ($doc_table=='gks_acc_pay') $sub_dir='acc/pay/';
  else if ($doc_table=='gks_whi_mov') $sub_dir='whi/mov/';


  $save_dir = GKS_FileServerShare.$sub_dir.$id.'/aade_mydata/';
  if (file_exists($save_dir) == false) {
    if (@mkdir($save_dir , 0777, true) == false ) {
      $ret['message']=gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').': '.substr($save_dir, strlen(GKS_FileServerShare)); debug_mail(false,$ret['message']); return $ret;
    }
  }
  $set_filename=showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
  $set_filename_s='invoice_'.$set_filename.'-paroxos-1-send';
  $set_filename_a='invoice_'.$set_filename.'-paroxos-1-send-mydata';
  $set_filename_r='invoice_'.$set_filename.'-paroxos-2-response';

  require_once('vendor_inc/Nicer.php');

  unset($input_to_raw['Email']);
  unset($input_to_raw['password']);
  unset($input_to_raw[$ttt.'_id']);
  unset($input_to_raw['id_company_paroxos']);
  unset($input_to_raw['paroxos_mydata_live']);
  unset($input_to_raw['paroxos_token']);
  unset($input_to_raw['paroxos_url']);
  unset($input_to_raw['paroxos_live']);
  unset($input_to_raw['paroxos_pc_username']);
  unset($input_to_raw['paroxos_pc_password']);


  if (isset($input_to_raw['additionalSupportDocs'][0]['description']) and
     $input_to_raw['additionalSupportDocs'][0]['description']=='##InvoicesDoc##' and
     isset($input_to_raw['additionalSupportDocs'][0]['reference']) and
     strlen($input_to_raw['additionalSupportDocs'][0]['reference'])>100) {
        
     file_put_contents($save_dir.$set_filename_a.'.xml', base64_decode($input_to_raw['additionalSupportDocs'][0]['reference']));  
     
     //$input_to_raw['additionalSupportDocs'][0]['gks_decode_reference']=htmlspecialchars(base64_decode($input_to_raw['additionalSupportDocs'][0]['reference'])); 
  }

  $raw_file='<!DOCTYPE html><html dir="ltr" lang="en-US"><head>
  		<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/vendor_inc/nice_r.css?v='.$gks_cache_version.'"/>
  		<script type="text/javascript" src="'.GKS_SITE_URL.'my/vendor_inc/nice_r.js?v='.$gks_cache_version.'"></script>
  	</head><body>';
          $obj_nicer = new Nicer($input_to_raw, true, true);
          $raw_file.=$obj_nicer->render(false);
          $raw_file.='<div id="raw_print_r_b" onclick="raw_toggle()">RAW json</div>';
          $raw_file.='<div style="display:none;" id="raw_print_r"><pre>';
          $raw_file.=json_encode($input_to_raw,JSON_PRETTY_PRINT);
          $raw_file.='</pre></div>';
  $raw_file.='</body>
  </html>'; 
  file_put_contents($save_dir.$set_filename_s.'.html', $raw_file);   


  unset($response_array['gks_ok']);
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


  

	if (isset($response_array['error']['data']['request_xml'])) {
		$request_xml=trim_gks($response_array['error']['data']['request_xml']);
		if ($request_xml!='') {
			file_put_contents($save_dir.'invoice_'.$set_filename.'-mydata-2-error.xml', $request_xml);
		}
		
	}
	if (isset($response_array['data']['request_xml'])) {
		$request_xml=trim_gks($response_array['data']['request_xml']);
		if ($request_xml!='') {
			file_put_contents($save_dir.'invoice_'.$set_filename.'-mydata-1-send.xml', $request_xml);
		}
	}
	if (isset($response_array['data']['response_xml'])) {
		$request_xml=trim_gks($response_array['data']['response_xml']);
		if ($request_xml!='') {
			file_put_contents($save_dir.'invoice_'.$set_filename.'-mydata-2-response.xml', $request_xml);
		}
	}
	
	
	

	
	
	if ($paroxos_status==0) {
		
		

    if ($paroxos_params['paroxos_mydata_live']) {
    	$errorMessage=$ret_send['message'];
  		$sql_xxx="update ".$doc_table." set  
  	  aade_xml_send='".$db_link->escape_string($set_filename_s.'.html')."',
  	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
  	  
  		aade_statuscode='ValidationError',
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		aade_user_id=".$my_wp_user_id.",
  
  		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  		paroxos_status=".($paroxos_status ? '1' : '0').",
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_".$ttt."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
  	}
  }
  	    

	//echo '<pre>ddddddddd ddd ggg ';print_r($ret_send);die();

	

	

		
		

	if ($paroxos_status==0) {	
		$ret['message']=gks_lang('Σφάλμα αποστολής').' (34239322422aa23)<br>'.$ret_send['message'];
	  return $ret;}
	
	
	//echo '<pre>ddddddddd 3333 ddd ggg ';print_r($ret_send);die();



  //echo '<pre>'.$paroxos_status;die();
  //if ($has_error) return $ret; 
	
	if ($paroxos_status==1) { //pige ok
		$errorMessage=''; if (isset($response_array['errorMessage'])) $errorMessage=trim_gks($response_array['errorMessage']); 
		$errorMessage=''; if (isset($ret_send['message'])) $errorMessage=trim_gks($ret_send['message']); 
		if ($errorMessage=='OK') $errorMessage='';
		
    if ($paroxos_params['paroxos_mydata_live']) {
      
  
  		
      $invoiceNumber_BT_1=$struct_data['xml']['issuer']['vatNumber'].'|';
      $invoiceNumber_BT_1.=showDate(strtotime($struct_data['row'][$xxx.'_date']), 'd/m/Y',1).'|';
      $invoiceNumber_BT_1.=$paroxos_params['paroxos_branch'].'|'; //'0|';
      
      $invoiceNumber_BT_1.=$struct_data['row']['eidos_parastatikou_aade_code'].'|';
      $invoiceNumber_BT_1.=$struct_data['row'][$rrr.'_seira_code'].'|';
      $invoiceNumber_BT_1.=$struct_data['row'][$rrr.'_number_int'];
      
      
      
      
      $aade_qrurl='';
      $aade_paroxos_qrurl='';
      if (isset($response_array['invoiceMarking']['myDataQrCode']) and $response_array['invoiceMarking']['myDataQrCode']!='') {
        $aade_qrurl=$response_array['invoiceMarking']['myDataQrCode'];
        $aade_qrurl=str_replace('https://mydatapi.aade.gr/TimologioQR/QRInfo','https://mydatapi.aade.gr/myDATA/TimologioQR/QRInfo',$aade_qrurl);
      }
      if (isset($response_array['invoiceMarking']['qrCode']) and $response_array['invoiceMarking']['qrCode']!='') {
        $aade_paroxos_qrurl=$response_array['invoiceMarking']['qrCode'];
      }
      
      
      
  		$sql_xxx="update ".$doc_table." set
  		aade_paroxos_id=".$paroxos_params['aade_paroxos_id'].",  
  	  aade_xml_send='".$db_link->escape_string($set_filename_s.'.html')."',
  	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
  		aade_invoiceuid='".$db_link->escape_string($response_array['invoiceMarking']['invoiceIdentifier'])."',
  		aade_invoicemark='".$db_link->escape_string($response_array['invoiceMarking']['mark'])."',
  		aade_qrurl='".$db_link->escape_string($aade_qrurl)."',
  		aade_paroxos_qrurl='".$db_link->escape_string($aade_paroxos_qrurl)."',
  		paroxos_authenticationCode='".$db_link->escape_string($response_array['invoiceMarking']['verificationHash'])."',
  		paroxos_processId='".$db_link->escape_string($response_array['invoiceMarking']['invoiceId'])."',
  		aade_statuscode='Success',
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		aade_send_date=now(),
  		aade_user_id=".$my_wp_user_id.",
      paroxos_invoice_number='".$db_link->escape_string($invoiceNumber_BT_1)."',
  		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  		paroxos_status=".$paroxos_status.",
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_".$ttt."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
  	  
      gks_aade_update_mark_from_id(['mark'=>$response_array['invoiceMarking']['mark'],$ttt.'_id'=>$id]);

  	  //echo '<pre>'; print   GKS_SITE_URL.'my/cron_paroxos.php?get_files=1&id='.$id;die();
  	  //https://test.easyfilesselection.com/my/cron_paroxos.php?get_files=1&id=11514
  	  //gks_curl_post_async(GKS_SITE_URL.'my/cron_paroxos.php?get_files=1&id='.$id,[]);
  	  
    } else {
  		$sql_xxx="update ".$doc_table." set  
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()
  		where id_".$ttt."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}

    }
    
  	
  	$ret['save_but_message']=gks_lang('Επιτυχής αποστολή δεδομένων σε πάροχο');
  	if ($errorMessage!='') $ret['save_but_message'].='<br>'.$errorMessage;
    $ret['message']='ok';
    $ret['success']=true;
  
    return $ret;		

	}
    
	
  $ret['save_but_message']=gks_lang('Σφάλμα κατά την αποστολή').' (2)';
  $ret['message']=gks_lang('Σφάλμα κατά την αποστολή').' (1)';
  $ret['success']=false;

  return $ret;	
}




function gks_paroxos_invoice_xml_send_pdf_item_ilyda_com($doc_table,$xxx_item) {
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
      [print_file_name] => XXX_11518_ekdosi_2024-02-26_00.33.20.814.pdf
      [print_file_url] => /my/admin-get-file.php?fs=fileservers&file=acc%2Fxxx%2F11518%2Fprint%2FINV_11518_ekdosi_2024-02-26_00.33.20.814.pdf
      [print_user_id] => 1
      [print_xxx_state] => 090ekdosi
  )*/

  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
    $ttt='acc_inv';
    $rrr='inv_acc';   
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
    $ttt='acc_pay';
    $rrr='pay_acc'; 
  } else if ($doc_table=='gks_whi_mov') {
    $xxx='mov';
    $ttt='whi_mov'; 
    $rrr='mov_whi'; 
  } else {
    echo '<pre>error on doc_table-page'; die();
  }
    
  $sql_paroxos="select * from gks_company_paroxos where ";
  if ($xxx_item['company_id']>0) $sql_paroxos.="company_id=".$xxx_item['company_id'];
  else if ($xxx_item['company_sub_id']>0) $sql_paroxos.="company_sub_id=".$xxx_item['company_sub_id'];
  else $sql_paroxos.="1=2";
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql_paroxos.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  if ($result_paroxos->num_rows==0) {$ret['message']=gks_lang('Αυτή η εταιρεία/υποκατάστημα δεν έχει ορισθεί για αποστολή σε πάροχο');debug_mail(false,$ret['message'],$sql); return $ret;}
  $row_paroxos = $result_paroxos->fetch_assoc();
  
  $force_options=[];$force_options['paroxos_mydata_live']=true;
  $ret_params=gks_paroxos_load_params($xxx_item['company_id'],$xxx_item['company_sub_id'],$force_options);
  if ($ret_params['success']==false) {$ret['message']=$ret_params['message']; debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $paroxos_params=$ret_params['paroxos_params'];

  //echo '<pre>ssssssssss11 ';print_r($xxx_item);die();

  if ($doc_table=='gks_acc_inv') $sub_dir='acc/inv/';
  else if ($doc_table=='gks_acc_pay') $sub_dir='acc/pay/';
  else if ($doc_table=='gks_whi_mov') $sub_dir='whi/mov/';

  $read_file = GKS_FileServerShare.$sub_dir.$xxx_item['id_'.$ttt].'/print/'.$xxx_item['print_file_name'];
  if (file_exists($read_file)==false) {$ret['message']=gks_lang('Δεν βρέθηκε το αρχείο').' '.$xxx_item['print_file_name'] ; debug_mail(false,$ret['message'],$sql); return $ret;}
  
  //echo '<pre>gggggggggggs';print_r($paroxos_params);die();
  $read_file_size=filesize($read_file);
//  $input=array();	
//  $input['acc_xxx_id']=$xxx_item['id_acc_'.$xxx];
//	$input['id_company_paroxos']=$row_paroxos['id_company_paroxos'];
//  //$input['paroxos_token']=$ret_token['pc_token_id'];
//  //$input['paroxos_url']=$ret_token['pc_url1'];
//  $input['paroxos_live']=$paroxos_params['paroxos_mydata_live'];
//  $input['paroxos_pc_username']=$paroxos_params['pc_username'];
//  $input['paroxos_pc_password']=$paroxos_params['pc_password'];
//  //$input['processId']=$inv_item['paroxos_processId'];
//  //$input['externalSystemId']=$inv_item['id_acc_inv'].'';
//  $input['fileName']=$inv_item['print_file_name'];
//  $input['fileSize']=$read_file_size;
  


  
    
  //echo '<pre>sssssssssss33 3 ';print_r($input);die(); 
  
  if ($paroxos_params['paroxos_mydata_live']) {
    $url=GKS_ILYDA_COM_MODE_LIVE_API;
  } else {
    $url=GKS_ILYDA_COM_MODE_TEST_API;
  }
  $url.='/api/invoice/upload/'.$xxx_item['paroxos_processId'];
  //echo $url;die();
  

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );  
  //curl_setopt($ch, CURLOPT_HEADER, true);
  $headers=array(
      //'Authorization: Bearer '.$access_token, // authorization for bluemix iam
      //'x-ms-blob-type: BlockBlob',
      //'Content-Type: application/pdf',
      'Content-Type: multipart/form-data',
      //'Content-Length: '.filesize($read_file),
      'Authorization: Basic '.base64_encode($paroxos_params['pc_username'].':'.$paroxos_params['pc_password'])
  );
  //echo '<pre>';print_r($headers);die();
  curl_setopt($ch, CURLOPT_HTTPHEADER,$headers );
  curl_setopt($ch, CURLOPT_POST,true);
  $fields = array(
    'FileUpload' => new \CurlFile($read_file, 'application/pdf', 'invoice.pdf'),
    'invoiceId' => $xxx_item['paroxos_processId'],
  );
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
  $result = curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);  
  $gks_curl_info =curl_getinfo($ch); // request headers from response (check if something wrong)
  curl_close ($ch);

  

//  echo '<pre>';
//  echo $read_file_size."\n";
//  echo $gks_curl_info['size_upload']."\n";
//  var_dump($result);
//  echo "\n";
//  var_dump($gks_curl_errno);
//  echo "\n";
//  print_r($gks_curl_info);
//  die();
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
 
  if ($gks_curl_http_code!=200) {
    debug_mail(false,'ilyda vs.gr error',                        gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error: '.$gks_curl_http_code);
  }
  

  //$parts=explode("\r\n\r\n",$result,2);
  //if (count($parts)!=2) {
  //  debug_mail(false,'ilyda vs.gr result error',$result);
  //  return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Σφάλμα δεδομένων').' (1).'.$result);}

  $response=trim($result); //trim($parts[1]);
  if ($response=='') {
    debug_mail(false,'ilyda vs.gr response error',$response);
    return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Σφάλμα δεδομένων').' (2).'.$result);}

	if ($response=='Ilyda Generic Error (1)') {
    debug_mail(false,'ilyda vs.gr response error',$response);
    return array('success' => false, 'message' => 'ilyda vs.gr '.gks_lang('Σφάλμα δεδομένων').' (2.1).<br>'.$response);}
		
	
  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'ilyda vs.gr json_decode error',$response);
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (3).'.$result);}
    
  
/*
Array
(
    [signedPaymentMethods] => 
    [invoiceMarking] => Array
        (
            [verificationHash] => 
            [qrCode] => 
            [aadePreviouslySubmittedError228] => 
            [invoiceId] => 10cc0a7d9020e9390190228bc018026a
            [invoiceIdentifier] => 
            [providerUrl] => 
            [mark] => 
        )

    [errors] => Array
        (
        )

)
*/

  //print '<pre>';print_r($response_array);die();
  


  if (!(is_array($response_array) and 
      //isset($response_array['signedPaymentMethods']) and
      isset($response_array['invoiceMarking']) and
      isset($response_array['invoiceMarking']['invoiceId']) and
      $response_array['invoiceMarking']['invoiceId']==$xxx_item['paroxos_processId'] and
      isset($response_array['errors']) and
      is_array($response_array['errors']) and
      count($response_array['errors'])==0)) {
    debug_mail(false,'ilyda vs.gr file pdf upload error',print_r($response,true));
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (4).<br>'.print_r($response,true));
  }
  


  
  

  $sql_xxx="update ".$doc_table." set
  paroxos_send_pdf=now(),
  paroxos_send_pdf_name='".$db_link->escape_string($xxx_item['print_file_name'])."'
	where id_".$ttt."=".$xxx_item['id_'.$ttt];
  $result_xxx = $db_link->query($sql_xxx); 
  if (!$result_xxx) {
    debug_mail(false,'error sql',$sql_xxx);
    return array('success' => false, 'message' => 'sql error');}      

  if ($paroxos_params['paroxos_mydata_live']) {
    $pdfFileUrl=GKS_ILYDA_COM_MODE_LIVE_API;
  } else {
    $pdfFileUrl=GKS_ILYDA_COM_MODE_TEST_API;
  }
  $pdfFileUrl.='/iv/invoice/file/'.$xxx_item['paroxos_processId'];

  

  $sql_xxx="update ".$doc_table." set
  paroxos_send_pdf_url='".$db_link->escape_string($pdfFileUrl)."'
	where id_".$ttt."=".$xxx_item['id_'.$ttt];
  $result_xxx = $db_link->query($sql_xxx); 
  if (!$result_xxx) {
    debug_mail(false,'error sql',$sql_xxx);
    return array('success' => false, 'message' => 'sql error');}      
  
  //echo '<pre>dddakskdfkfbb ';print_r($response_array);die();  
  
  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;  
  
}


function gks_paroxos_invoice_xml_get_files_item_ilyda_com($doc_table,$xxx_item) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $gks_cache_version;
  
  //echo '<pre>ssssssssss ';print_r($inv_item);die();
  
  $ret = array('success' => false, 'message' => 'generic error');
  /*Array
(
    [id_acc_inv] => 11957
    [company_id] => 10014
    [company_sub_id] => 0
    [aade_paroxos_id] => 8
    [paroxos_processId] => 10cc0a7d9030ce12019031abdd2a00fa
)*/
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
    $ttt='acc_inv';
    $rrr='inv_acc';   
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
    $ttt='acc_pay';
    $rrr='pay_acc'; 
  } else if ($doc_table=='gks_whi_mov') {
    $xxx='mov';
    $ttt='whi_mov'; 
    $rrr='mov_whi'; 
  } else {
    echo '<pre>error on doc_table-page'; die();
  }
    
  $sql_paroxos="select * from gks_company_paroxos where ";
  if ($xxx_item['company_id']>0) $sql_paroxos.="company_id=".$xxx_item['company_id'];
  else if ($xxx_item['company_sub_id']>0) $sql_paroxos.="company_sub_id=".$xxx_item['company_sub_id'];
  else $sql_paroxos.="1=2";
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql_paroxos.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  if ($result_paroxos->num_rows==0) {
    $ret['message']=gks_lang('Αυτή η εταιρεία/υποκατάστημα δεν έχει ορισθεί για αποστολή σε πάροχο');
    debug_mail(false,$ret['message'],$sql); return $ret;}
  $row_paroxos = $result_paroxos->fetch_assoc();
  
  $force_options=[];$force_options['paroxos_mydata_live']=true;
  $ret_params=gks_paroxos_load_params($xxx_item['company_id'],$xxx_item['company_sub_id'],$force_options);
  if ($ret_params['success']==false) {$ret['message']=$ret_params['message']; debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $paroxos_params=$ret_params['paroxos_params'];


  $url='/api/invoice/status/'.$xxx_item['paroxos_processId'];
  //$url='/api/invoice/status/'.$xxx_item['aade_invoiceuid'];
  
  //echo '<pre>gggggggggggs ';print_r($xxx_item);die();
  //echo '<pre>gggggggggggs '.$url;die();

	$input=array();
	$input[$ttt.'_id']=$xxx_item['id_'.$ttt];
	$input['id_company_paroxos']=$paroxos_params['id_company_paroxos'];
  //$input['paroxos_token']=$paroxos_params['pc_key'];
  //$input['paroxos_url']=$ret_token['pc_url1'];
  $input['paroxos_live']=$paroxos_params['paroxos_mydata_live'];
  $input['paroxos_pc_username']=$paroxos_params['pc_username'];
  $input['paroxos_pc_password']=$paroxos_params['pc_password'];

  $input_to_raw=$input;

  
  //echo '<pre>sssssssssss ';print_r($input);die(); 
  
  $ret_send = gks_paroxos_ilyda_com_get_url($url,'GET',$input_to_raw);
  //echo '<pre>sssssssssss ';print_r($ret_send);die();
  
  //debug_mail(false,'api get',print_r($input,true).'<br>'.print_r($ret_send,true));
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
	$response_array=$ret_send['response_array'];
	if (isset($response_array['status'])==false) {
	  $ret['message']=gks_lang('Σφάλμα αποστολής').' (54239322421)';return $ret;}

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
    $ret['message']=gks_lang('Σφάλμα αποστολής').' (54239322422)'; return $ret;
  }
  
  if ($doc_table=='gks_acc_inv') $sub_dir='acc/inv/';
  else if ($doc_table=='gks_acc_pay') $sub_dir='acc/pay/';
  else if ($doc_table=='gks_whi_mov') $sub_dir='whi/mov/';
  
  //echo '<pre>dddakskdfkfbb ';print_r($response_array);die();
  if (count($response_array['files'])>0) {
    $save_dir = GKS_FileServerShare.$sub_dir.$xxx_item['id_'.$ttt].'/paroxos/';
    if (file_exists($save_dir) == false) {
      if (@mkdir($save_dir , 0777, true) == false ) {
        $ret['message']=gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').': '.substr($save_dir, strlen(GKS_FileServerShare)); debug_mail(false,$ret['message']); return $ret;
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

    $sql_xxx="update ".$doc_table." set
    paroxos_get_files=now()
		where id_".$ttt."=".$xxx_item['id_'.$ttt];
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


function gks_paroxos_payment_sign_ilyda_com($id,$paroxos_params,$struct_data) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;	
  global $gks_cache_version;  
  
  
  //echo '<pre>sign_ilyda doc_table '.$struct_data['doc_table'];die();
  
  $doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
    $ttt='acc_inv';
    $rrr='inv_acc';   
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
    $ttt='acc_pay';
    $rrr='pay_acc'; 
  } else if ($doc_table=='gks_whi_mov') {
    $xxx='mov';
    $ttt='whi_mov'; 
    $rrr='mov_whi'; 
  } else {
    echo '<pre>error on doc_table-page'; die();
  }  
  
  $ret = array('success' => false, 'message' => 'generic error');

	if (isset($paroxos_params['pc_username'])==false or trim_gks($paroxos_params['pc_password'])=='') {
	  $ret['message']=gks_lang('Δεν έχει ορισθεί το Όνομα Χρήστη/Κωδικός Πρόσβασης για τον πάροχο');return $ret;
	}

  //echo '<pre>ddddddd ddd ';print_r($struct_data['sign']);die();
  
  $input=[];
	$input['acc_inv_id']=$id;
	$input['id_company_paroxos']=$paroxos_params['id_company_paroxos'];
  //$input['paroxos_token']=$paroxos_params['pc_key'];
  //$input['paroxos_url']=$ret_token['pc_url1'];
  $input['paroxos_live']=$paroxos_params['paroxos_mydata_live'];
  $input['paroxos_pc_username']=$paroxos_params['pc_username'];
  $input['paroxos_pc_password']=$paroxos_params['pc_password'];

  $input['amount']=$struct_data['sign']['amount'];
  $input['netAmount']=$struct_data['sign']['netAmount'];
  $input['vatAmount']=$struct_data['sign']['vatAmount'];
  $input['grossAmount']=$struct_data['sign']['grossAmount'];
  
  //echo '<pre>sssssss '.$struct_data['sign']['payment_acquirer_with_id'];print_r($input);die();
  if ($struct_data['sign']['payment_acquirer_with_id']==1) {
    $input['vatRate']=0; //24 gia na ginei meta 2400 gia tin viva
    if ($input['netAmount']>0) {
      $mycal_vat_rate=round((100*$input['vatAmount'])/$input['netAmount'],0);
    } else {
      $mycal_vat_rate=24;
    }
    $input['vatRate']=$mycal_vat_rate;
  }
  
  $input['sellerVat']=$struct_data['row']['company_afm'];
  $input['series']=$struct_data['row']['seira_code'];
  $input['invoiceTypeCode']=$struct_data['row']['eidos_parastatikou_aade_code'];
  $input['sellerBranch']=$paroxos_params['paroxos_branch'];
  $input['mark']=null;
  $input['terminalId']=$struct_data['sign']['terminalId'];
  
  $input['nspProtocol']='';
//  $input['nspProtocol']='NEOSOFT';
//  $input['nspProtocol']='MELLON';
//  $input['nspProtocol']='EPAY';
//  $input['nspProtocol']='NEXI';
//  $input['nspProtocol']='CARDLINK';
//  $input['nspProtocol']='VIVA';
//  $input['nspProtocol']='WORLDLINE';

  
  if ($struct_data['sign']['payment_acquirer_with_id']==1) $input['nspProtocol']='VIVA';
  if ($struct_data['sign']['payment_acquirer_with_id']==3) $input['nspProtocol']='MELLON';
  if ($struct_data['sign']['payment_acquirer_with_id']==4) $input['nspProtocol']='CARDLINK';
  if ($struct_data['sign']['payment_acquirer_with_id']==5) $input['nspProtocol']='EPAY';
  if ($struct_data['sign']['payment_acquirer_with_id']==6) $input['nspProtocol']='WORLDLINE';
  if ($struct_data['sign']['payment_acquirer_with_id']==7) $input['nspProtocol']='NEXI'; //to NEXI espidtrafei hex, tha prepei na ginei base64
  
  
  if ($struct_data['sign']['payment_acquirer_with_id']==2) { //Meg EFT/POS Driver
    switch ($struct_data['sign']['megeftpos_protocol']) {
      
      case 0: //NOT_SET
         $input['nspProtocol']='DEFAULT';
         break;
      case 1: //EDPS_JSON
         $input['nspProtocol']='DEFAULT';
         break;  
      case 2: //CARDLINK_DLL
         $input['nspProtocol']='CARDLINK';
         break;  
      case 3: //MELLON_WEB_ECR
         $input['nspProtocol']='MELLON';
         break;  
      case 4: //EPAY_WEB_ECR
         $input['nspProtocol']='EPAY';
         break;  
      case 8: //WORLDLINE_WEB_ECR
         $input['nspProtocol']='WORLDLINE';
         break;  
       default:
        $ret['message']=gks_lang('Δεν έχει ορισθεί το nspProtocol για την λήψη της υπογραφής');
        debug_mail(false,$ret['message'],print_r($struct_data,true));
        return $ret;
       
    }
    
    //echo '<pre>ssssssssssssssss';print_r($struct_data);die();
    //$paroxos_params,$struct_data
  }

	
	if ($input['nspProtocol']=='') {
	  $ret['message']=gks_lang('Δεν έχει ορισθεί το nspProtocol');return $ret;
	}
	
	
	
	$input_to_raw=$input;
	
	//echo '<pre>hhhdd ';print_r($input);die();
	
	$ret_send = gks_paroxos_payment_sign_reuse_ilyda_com($input);
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
	//echo '<pre>sssssssssshhhdd ';print_r($ret_send);die();

  if ($ret_send['found']==true) {
    return $ret_send;
  }
	
	//echo '<pre>not found, get new hhhdd ';print_r($input);die();
	
  $ret_send = gks_paroxos_ilyda_com_get_url('/api/invoice/sign','POST',$input);
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}

  
  //echo '<pre>hhhdd ';print_r($ret_send);die();

  $response_array=$ret_send['response_array'];
  
	  
  if (isset($response_array['errors']) and is_array($response_array['errors']) and count($response_array['errors'])>0) {
    $html_error='<div>'.gks_lang('Σφάλμα κατά την λήψη της υπογραφής').'</div>';
    $html_error.='<table class="table table-sm table-responsive1 table-striped table-bordered" style="width: 100%;font-size:0.8rem;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">'.
    '<thead>'.
    '<tr>'.
    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κωδικός').'</th>'.
    '<th class="table-dark" scope="col" style="text-align: left !important;" width="100%">'.gks_lang('Περιγραφή').'</th>'.
    '</tr>'.
    '</thead>'.
    '<tbody>';
    $tr_aa=0;
    foreach ($response_array['errors'] as $value) {
      $tr_aa++;
      $td_code=''; if (isset($value['code'])) $td_code=trim_gks($value['code']);
      $td_message=array(); 
      if (isset($value['defaultMessage']) and $value['defaultMessage']<>'') $td_message[]=htmlspecialchars(trim_gks($value['defaultMessage']));
      if (isset($value['aadeMessage']) and $value['aadeMessage']<>'') $td_message[]=htmlspecialchars(trim_gks($value['aadeMessage']));
      
      if (isset($value['errorFields']) and is_array($value['errorFields']) and count($value['errorFields'])>0) {
        foreach ($value['errorFields'] as $valueef) {
          $temp='';
          if (isset($valueef['field'])) $temp.=gks_lang('Πεδίο').': '.$valueef['field'].' ';
          if (isset($valueef['value'])) $temp.=gks_lang('Τιμή').': '.$valueef['value'];
          $td_message[]=htmlspecialchars(trim_gks($temp));
        }   
      }
      
      $html_error.=
      '<tr>'.
        '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
        '<td nowrap style="text-align:center">'.htmlspecialchars($td_code).'</td>'.
        '<td  style="text-align:left">'.implode('<br>',$td_message).'</td>'.
      '</tr>';
      
      
      
    } 
    
    $html_error.='</tbody></table>';
    
    debug_mail(false,'get sing error',print_r($ret_send,true));
    $ret['success']=false;
    $ret['message']=$html_error;
    return $ret;
    
  }  

  
  
  //echo '<pre>aaaaaaaaahhhdd ';print_r($ret_send);die();
  
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}

  if (isset($ret_send['response_array']['invoiceSignatures']) and
      is_array($ret_send['response_array']['invoiceSignatures']) and
      count($ret_send['response_array']['invoiceSignatures'])==1) {
    
    $signature=$ret_send['response_array']['invoiceSignatures'][0];
    
    if ($struct_data['doc_table']=='gks_acc_inv') {
      $val_acc_inv_id=$struct_data['row']['id_acc_inv'];
      $val_acc_pay_id=0;
      $val_acc_inv_payment_id=$struct_data['sign']['acc_xxx_payment_id'];
      $val_acc_pay_payment_id=0;
    } else if ($struct_data['doc_table']=='gks_acc_pay') {
      $val_acc_inv_id=0;
      $val_acc_pay_id=$struct_data['row']['id_acc_pay'];
      $val_acc_inv_payment_id=0;
      $val_acc_pay_payment_id=$struct_data['sign']['acc_xxx_payment_id'];
      
    }
    
    $sql="insert into gks_paroxos_signature (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    aade_paroxos_id,
    acc_inv_id,
    acc_pay_id,
    acc_inv_payment_id,
    acc_pay_payment_id,
    payment_acquirer_with_id,
    payment_acquirer_id,
    signature_status,
    asset_id,
    s_terminalId,
    s_amount,
    s_netAmount,
    s_vatAmount,
    s_grossAmount,

    r_signingAuthor,
    r_amount,
    r_signatureExpirationDate,
    r_netAmount,
    r_signature,
    r_vatRate,
    r_grossAmount,
    r_terminalId,
    r_signedContent,
    r_vatAmount,
    r_sellerVat,
    r_uid,
    r_sellerBranch,
    r_serial,
    r_series,
    r_uidHash,
    r_signaturePublicKey,
    r_signedAt,
    r_invoiceTypeCode,
    r_nspProtocol,
    response
    
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    8,
    ".$val_acc_inv_id.",
    ".$val_acc_pay_id.",
    ".$val_acc_inv_payment_id.",
    ".$val_acc_pay_payment_id.",
    ".$struct_data['sign']['payment_acquirer_with_id'].",
    ".$struct_data['sign']['payment_acquirer_id'].",
    'draft',
    ".$struct_data['sign']['asset_id'].",
    '".$db_link->escape_string($struct_data['sign']['terminalId'])."',
    ".$struct_data['sign']['amount'].",
    ".$struct_data['sign']['netAmount'].",
    ".$struct_data['sign']['vatAmount'].",
    ".$struct_data['sign']['grossAmount'].",
    
    '".$db_link->escape_string($signature['signingAuthor'])."',
    ".floatval($signature['amount']).",
    ".intval($signature['signatureExpirationDate']).",
    ".floatval($signature['netAmount']).",
    '".$db_link->escape_string($signature['signature'])."',
    ".floatval($signature['vatRate']).",
    ".floatval($signature['grossAmount']).",
    '".$db_link->escape_string($signature['terminalId'])."',
    '".$db_link->escape_string($signature['signedContent'])."',
    ".floatval($signature['vatAmount']).",
    '".$db_link->escape_string($signature['sellerVat'])."',
    '".$db_link->escape_string($signature['uid'])."',
    ".intval($signature['sellerBranch']).",
    '".$db_link->escape_string($signature['serial'])."',
    '".$db_link->escape_string($signature['series'])."',
    '".$db_link->escape_string($signature['uidHash'])."',
    '".$db_link->escape_string($signature['signaturePublicKey'])."',
    ".intval($signature['signedAt']).",
    '".$db_link->escape_string($signature['invoiceTypeCode'])."',
    '".$db_link->escape_string($signature['nspProtocol'])."',   
    '".$db_link->escape_string(serialize($ret_send['response_array']))."'
    )";
	  $result = $db_link->query($sql); 
	  if (!$result) {
	    debug_mail(false,'error sql',$sql);
	    return array('success' => false, 'message' => 'sql error');}   
	  
	  $id_paroxos_signature = $db_link->insert_id;      
    //echo '<pre>hhhdd ';print_r($ret_send);die();
    $ret['id_paroxos_signature']=$id_paroxos_signature;
    $ret['id_aade_paroxos']=8;
  } else {
    $ret['message']=gks_lang('Δεν βρέθηκε υπογραφή');return $ret;
  }
	$response_array=$ret_send['response_array'];
	if (isset($response_array['gks_ok'])==false) {$ret['message']=gks_lang('Σφάλμα αποστολής').' (34239322421a)';return $ret;}

  $ret['response']=$response_array;
  $ret['message']='ok';
  $ret['success']=true;

  return $ret; 
    
}

function gks_paroxos_get_signature_data_ilyda_com($signature_data) {
  
  $ret = array('success' => false, 'message' => 'generic error get_signature_data_ilyda_com');
  //echo '<pre>dddddddddddd ';print_r($signature_data);die();
 
  ;
  $aadeSignatureTimestamp='';
  if (isset($signature_data['signature']['signedContent'])) {
    $parts=explode(';',$signature_data['signature']['signedContent']);
    if (count($parts)>=3) $aadeSignatureTimestamp=$parts[2];
  }
  
  if ($aadeSignatureTimestamp=='' or 
     isset($signature_data['signature'])==false or 
     isset($signature_data['signature']['signedContent'])==false or 
     isset($signature_data['signature']['signature'])==false or 
     $signature_data['signature']['signedContent']=='' or 
     $signature_data['signature']['signature']=='') {
    
    $ret['message'] = 'data not found error get_signature_data_ilyda_com';
    return $ret;      
  }
  
  
  //echo '<pre>';print_r($signature_data);die();
  
  $aadeProviderId='8';
  if ($signature_data['signature']['nspProtocol']=='VIVA') $aadeProviderId='108';
  if ($signature_data['signature']['nspProtocol']=='MELLON') $aadeProviderId='8';
  if ($signature_data['signature']['nspProtocol']=='CARDLINK') $aadeProviderId='8';
  if ($signature_data['signature']['nspProtocol']=='WORLDLINE') $aadeProviderId='8';
  
  $ret['data']= array(
    'aadeProviderId'=> $aadeProviderId, //ylida
    'aadeProviderSignatureData' => $signature_data['signature']['signedContent'],
    'aadeProviderSignature' => $signature_data['signature']['signature'],
    'aadeSignatureTimestamp' => $aadeSignatureTimestamp,
    'aadeSignatureUID'=> $signature_data['signature']['uidHash'],
    'nspProtocol' => $signature_data['signature']['nspProtocol'],
  );
  
  $ret['success']=true;
  $ret['message']='OK';
  
  return $ret;
}

function gks_paroxos_payment_sign_reuse_ilyda_com($input) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;	
  global $gks_cache_version; 
  
  $ret = array('success' => false, 'message' => 'generic error', 'found'=>false);
  
  if (1==1) {
    $ret['success']=true;
    $ret['message']='not found';
    $ret['found']=false;
    return $ret;
  }
    
  
  $sql="update gks_paroxos_signature
  set signature_status='canreuse',mydate_edit=now()
  where aade_paroxos_id=8
  and r_terminalId='".$db_link->escape_string($input['terminalId'])."'
  and signature_status in ('assign')
  and mydate_add < date_sub(now(), interval 120 minute)";
  $result = $db_link->query($sql); 
  if (!$result) {
    $ret['message']='sql error';
    debug_mail(false,$ret['message'],$sql);
    return $ret;}  
  
  
  //signatureExpirationDate einai +2 ores 
  //ego thelo +5 lepta gia na ginei i diadikasia
  $sql="select * from gks_paroxos_signature
  where aade_paroxos_id=8
  and signature_status in ('canreuse')
  and r_signatureExpirationDate >= ".((time()+5*60)*1000)."
  and r_amount=".$input['amount']."
  and r_netAmount=".$input['netAmount']."
  and r_vatAmount=".$input['vatAmount']."
  and r_grossAmount=".$input['grossAmount']."
  and r_sellerVat='".$db_link->escape_string($input['sellerVat'])."'
  and r_series='".$db_link->escape_string($input['series'])."'
  and r_invoiceTypeCode='".$db_link->escape_string($input['invoiceTypeCode'])."'
  and r_sellerBranch=".$input['sellerBranch']."
  and r_terminalId='".$db_link->escape_string($input['terminalId'])."'
  and r_nspProtocol='".$db_link->escape_string($input['nspProtocol'])."'
  order by id_paroxos_signature";
  
  //and r_mark=".$input['mark']."
  //echo '<pre>dddddddd '.$sql;die();  
  
  $result = $db_link->query($sql); 
  if (!$result) {
    $ret['message']='sql error';
    debug_mail(false,$ret['message'],$sql);
    return $ret;}
  
  if ($result->num_rows==0) {
    $ret['success']=true;
    $ret['message']='not found';
    $ret['found']=false;
    return $ret;}
  
  $row=$result->fetch_assoc();
  
  $mysign=[];
  $mysign['signingAuthor']=$row['r_signingAuthor'];
  $mysign['amount']=floatval($row['r_amount']);
  $mysign['signatureExpirationDate']=intval($row['r_signatureExpirationDate']);
  $mysign['netAmount']=floatval($row['r_netAmount']);
  $mysign['signature']=$row['r_signature'];
  $mysign['vatRate']=floatval($row['r_vatRate']);
  $mysign['grossAmount']=floatval($row['r_grossAmount']);
  $mysign['terminalId']=$row['r_terminalId'];
  $mysign['signedContent']=$row['r_signedContent'];
  $mysign['vatAmount']=floatval($row['r_vatAmount']);
  $mysign['sellerVat']=$row['r_sellerVat'];
  $mysign['uid']=$row['r_uid'];
  $mysign['sellerBranch']=intval($row['r_sellerBranch']);
  $mysign['serial']=$row['r_serial'];
  $mysign['series']=$row['r_series'];
  $mysign['uidHash']=$row['r_uidHash'];
  $mysign['signaturePublicKey']=$row['r_signaturePublicKey'];
  $mysign['signedAt']=$row['r_signedAt'];
  $mysign['invoiceTypeCode']=$row['r_invoiceTypeCode'];
  $mysign['nspProtocol']=$row['r_nspProtocol'];
  
  $ret['response']=array();
  $ret['response']['invoiceSignatures']=array();
  $ret['response']['invoiceSignatures'][]=$mysign;
  $ret['response']['invoiceMarking']='';
  $ret['response']['errors']='';
  $ret['response']['gks_ok']=true;

  $ret['id_paroxos_signature']=intval($row['id_paroxos_signature']);
  $ret['id_aade_paroxos']=intval($row['aade_paroxos_id']);
  $ret['success']=true;
  $ret['message']='OK';
  $ret['found']=true;
  
  //echo '<pre>dddddddd111 ';print_r($ret);die();  
  
  
  //prepei na epistrefei oti kai i gks_paroxos_payment_sign_ilyda_com
  return $ret;
    
}


function gks_paroxos_invoice_get_docstate_ilyda_com($doc_table,$xxx_item) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $gks_cache_version;
  
  //echo '<pre>ssssssssss '.$doc_table;print_r($xxx_item);die();
  
  $ret = array('success' => false, 'message' => 'generic error','save_but_message'=>'');
  /* xxx_item Array
(
    [id_acc_inv] => 14154
    [company_id] => 10014
    [company_sub_id] => 0
    [aade_paroxos_id] => 8
    [paroxos_processId] => 
    [aade_invoiceuid] => 46aa21c49f5ec2cde4826500a05277398ab04e08
    [aade_invoicemark] => 
    [aade_qrurl] => 
    [aade_paroxos_qrurl] => https://test.vs.gr/iv/invoice/download/uhhs_1/uhhs_1~k1~46aa21c49f5ec2cde4826500a05277398ab04e08~sCLpMSADayOVRLiyiWLAnAval2Yn927aj3OQuoGxUJs
    [aade_statuscode] => Success
    [aade_errors] => 
    [aade_send_date] => 2025-12-12 17:56:49
    [aade_sending] => 
)*/
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
    $ttt='acc_inv';
    $rrr='inv_acc';   
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
    $ttt='acc_pay';
    $rrr='pay_acc'; 
  } else if ($doc_table=='gks_whi_mov') {
    $xxx='mov';
    $ttt='whi_mov'; 
    $rrr='mov_whi'; 
  } else {
    echo '<pre>error on doc_table-page'; die();
  }
    
  $sql_paroxos="select * from gks_company_paroxos where ";
  if ($xxx_item['company_id']>0) $sql_paroxos.="company_id=".$xxx_item['company_id'];
  else if ($xxx_item['company_sub_id']>0) $sql_paroxos.="company_sub_id=".$xxx_item['company_sub_id'];
  else $sql_paroxos.="1=2";
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql_paroxos.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  if ($result_paroxos->num_rows==0) {
    $ret['message']=gks_lang('Αυτή η εταιρεία/υποκατάστημα δεν έχει ορισθεί για αποστολή σε πάροχο');
    debug_mail(false,$ret['message'],$sql); return $ret;}
  $row_paroxos = $result_paroxos->fetch_assoc();
  
  $force_options=[];$force_options['paroxos_mydata_live']=true;
  $ret_params=gks_paroxos_load_params($xxx_item['company_id'],$xxx_item['company_sub_id'],$force_options);
  if ($ret_params['success']==false) {$ret['message']=$ret_params['message']; debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $paroxos_params=$ret_params['paroxos_params'];

  $url='/api/invoice/pending/by-uid/'.$xxx_item['aade_invoiceuid'];
  
  //echo '<pre>gggggggggggs '.$url;die();

	$input=array();
	$input[$ttt.'_id']=$xxx_item['id_'.$ttt];
	$input['id_company_paroxos']=$paroxos_params['id_company_paroxos'];
  //$input['paroxos_token']=$paroxos_params['pc_key'];
  //$input['paroxos_url']=$ret_token['pc_url1'];
  $input['paroxos_live']=$paroxos_params['paroxos_mydata_live'];
  $input['paroxos_pc_username']=$paroxos_params['pc_username'];
  $input['paroxos_pc_password']=$paroxos_params['pc_password'];

  $input_to_raw=$input;

  
  //echo '<pre>sssssssssss ';print_r($input);die(); 
  
  $ret_send = gks_paroxos_ilyda_com_get_url($url,'GET',$input_to_raw);
  //echo '<pre>kkkkkkkk ';print_r($ret_send);die();
  
  //debug_mail(false,'api get',print_r($input,true).'<br>'.print_r($ret_send,true));
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
	$response_array=$ret_send['response_array'];
	if (isset($response_array['uid'])==false) {$ret['message']=gks_lang('Σφάλμα αποστολής').' (54239422421)';return $ret;}

/*Array
(
    [success] => 1
    [message] => OK
    [response_array] => Array
        (
            [invoiceNumber] => 177472438|12/12/2025|0|11.1|alpmesign|1253
            [uid] => 46aa21c49f5ec2cde4826500a05277398ab04e08
            [sellerVatIdentifier] => 177472438
            [seriesNumber] => alpmesign
            [serialNumber] => 1253
            [invoiceId] => 10cc0a7d9b03f663019b13bdc86e1c32
            [mark] => 400001958244859
            [verificationHash] => AB23D57DBA1C6DE06AD6FCCDBFCEF787EB334A29
            [invoiceIssueDate] => 1765562160000
            [invoiceState] => SUBMITTED
            [errorsJson] => 
            [gks_ok] => 1
        )

)*/


  if (isset($response_array['invoiceState'])==false or trim_gks($response_array['invoiceState'])=='') {
    $ret['message']=gks_lang('Σφάλμα αποστολής').' (54239422422)'; return $ret;
  }
  $invoiceState=$response_array['invoiceState'];
  //SUBMITTED 
  switch ($invoiceState) {   
    case 'RESUBMIT_PENDING': 
      $ret['message']=gks_lang('Κατάσταση <b>RESUBMIT_PENDING</b>.<br>Αυτό το παραστατικό πρέπει να υποβληθεί εκ νέου στο myData από τον πάροχο. Θα γίνει αυτόματα. Αυτό συμβαίνει συνήθως μετά από βλάβες δικτύου ή διακομιστή myData.<br>Ξαναδοκιμάστε αργότερα για να δείτε εάν έχει αποσταλεί στο myData.'); return $ret;
      break;
    case 'MAX_RETRIES_REACHED': 
      $ret['message']=gks_lang('Κατάσταση <b>MAX_RETRIES_REACHED</b>.<br>Έχει επιτευχθεί ο μέγιστος επιτρεπόμενος αριθμός προσπαθειών για τη μετάδοση αυτού του τιμολογίου από τον πάροχο προς ΑΑΔΕ.'); return $ret;
      break;
    case 'SUBMISSION_ERRORS':
      $errorsJson= json_decode($response_array['errorsJson'],true);
      $ret['message']=gks_lang('Κατάσταση <b>SUBMISSION_ERRORS</b>.<br>Αυτό το τιμολόγιο έχει διαβιβαστεί στο myData, αλλά παρουσίασε ανεπανόρθωτα σφάλματα. ΔΕΝ θα πραγματοποιηθούν εκ νέου υποβολές.').'<br><pre>'.print_r($errorsJson,true).'</pre>'; return $ret;
      break;
    case 'SUBMITTED':
      // tha to kano pio kato...
      break;  
    default:      
      $ret['message']=gks_lang('Άγνωστη Κατάσταση').' '.$invoiceState; return $ret;
      break;
  }
  
  if ($doc_table=='gks_acc_inv') $sub_dir='acc/inv/';
  else if ($doc_table=='gks_acc_pay') $sub_dir='acc/pay/';
  else if ($doc_table=='gks_whi_mov') $sub_dir='whi/mov/';

  $save_dir = GKS_FileServerShare.$sub_dir.$xxx_item['id'].'/aade_mydata/';
  if (file_exists($save_dir) == false) {
    if (@mkdir($save_dir , 0777, true) == false ) {
      $ret['message']=gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').': '.substr($save_dir, strlen(GKS_FileServerShare)); debug_mail(false,$ret['message']); return $ret;
    }
  }
  $set_filename=showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
  $set_filename_r='invoice_'.$set_filename.'-paroxos-2-response';
    
  require_once('vendor_inc/Nicer.php');
  
  unset($response_array['gks_ok']);
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
  
  $aade_invoicemark=trim_gks($response_array['mark']);
  $paroxos_authenticationCode=trim_gks($response_array['verificationHash']);
  $paroxos_invoice_number=trim_gks($response_array['invoiceNumber']);
  //aade_errors

  $aade_invoiceuid=trim_gks($response_array['uid']);
  $sellerVatIdentifier=trim_gks($response_array['sellerVatIdentifier']);
  $seriesNumber=trim_gks($response_array['seriesNumber']);
  $serialNumber=trim_gks($response_array['serialNumber']);
  $invoiceId=trim_gks($response_array['invoiceId']);
  $invoiceIssueDate=trim_gks($response_array['invoiceIssueDate']);
  $errorsJson=trim_gks($response_array['errorsJson']);

  if ($aade_invoicemark=='' or $paroxos_authenticationCode=='' or $paroxos_invoice_number=='') {
    $ret['message']=gks_lang('Σφάλμα αποστολής').' (54239432422) '.gks_lang('Τα πεδία mark,verificationHash,invoiceNumber είναι κενά').'<br>'.print_r(response_array,true); return $ret;
  }
  //delete me
//  $errorsJson='[{"code":"311","defaultMessage":"","fatal":true,"statusCode":"TechnicalError",
//"element":"Application","aadeMessage":"Classification with type category1_1 and category E3_561_003 not found in invoice summary"},{"code":"I0003","defaultMessage":"Could not extract Invoice Marking from AADE mydata response. It probably returned errors.","fatal":true,
//"errorFields":[]}]';

  $is_I0003_eror=false;
  $html_error=$errorsJson;
  if ($errorsJson!='') {
    $errorsarray=json_decode($errorsJson,true);
    $html_error='<table class="table table-sm table-responsive1 table-striped table-bordered" style="width: 100%;font-size:0.8rem;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">'.
    '<thead>'.
    '<tr>'.
    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κωδικός').'</th>'.
    '<th class="table-dark" scope="col" style="text-align: left !important;" width="100%">'.gks_lang('Περιγραφή').'</th>'.
    '</tr>'.
    '</thead>'.
    '<tbody>';
    $tr_aa=0;
    foreach ($errorsarray as $value) {
      $tr_aa++;
      $td_code=''; if (isset($value['code'])) $td_code=trim_gks($value['code']);
      if ($td_code=='I0003') {
        $is_I0003_eror=true;
      }
      $td_message=array(); 
      if (isset($value['defaultMessage']) and $value['defaultMessage']<>'') $td_message[]=htmlspecialchars(trim_gks($value['defaultMessage']));
      if (isset($value['aadeMessage']) and $value['aadeMessage']<>'') $td_message[]=htmlspecialchars(trim_gks($value['aadeMessage']));
      
      if (isset($value['errorFields']) and is_array($value['errorFields']) and count($value['errorFields'])>0) {
        foreach ($value['errorFields'] as $valueef) {
          $temp='';
          if (isset($valueef['field'])) $temp.=gks_lang('Πεδίο').': '.$valueef['field'].' ';
          if (isset($valueef['value'])) $temp.=gks_lang('Τιμή').': '.$valueef['value'];
          $td_message[]=htmlspecialchars(trim_gks($temp));
        }   
      }
      
      $html_error.=
      '<tr>'.
        '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
        '<td nowrap style="text-align:center">'.htmlspecialchars($td_code).'</td>'.
        '<td  style="text-align:left">'.implode('<br>',$td_message).'</td>'.
      '</tr>';
      
      
      
    } 
    
    $html_error.='</tbody></table>';
    //echo '<pre>'.$html_error;die();
  }
  
  if ($is_I0003_eror) {
    $sql_paroxos="update ".$doc_table." set 
    aade_skopos_diakinisis_id=0,
    aade_skopos_19_descr=null,
    aade_invoiceuid=null,
    aade_invoicemark=null,
    aade_qrurl=null,
    aade_paroxos_qrurl=null,
    aade_statuscode=null,
    aade_errors=null,
    aade_send_date=null,
    aade_user_id=0,
    aade_xml_send=null,
    aade_xml_response=null,
    aade_paroxos_id=0,
    paroxos_processId=null,
    paroxos_last_response=null,
    paroxos_status=-1,
    paroxos_authenticationCode=null,
    paroxos_user_send=0,
    paroxos_date_send=null,
    paroxos_get_files=null,
    paroxos_send_pdf=null,
    paroxos_send_pdf_name=null,
    paroxos_send_pdf_url=null,
    paroxos_invoice_number=null
    where paroxos_status=1
    and aade_paroxos_id>0
    and aade_invoiceuid='".$db_link->escape_string($aade_invoiceuid)."'
    and (aade_invoicemark is null or aade_invoicemark='')
    and aade_send_date is not null
    and id_".$ttt."=".$xxx_item['id'];   
    
 
  } else { //OK
    $sql_paroxos="update ".$doc_table." set 
    aade_invoicemark='".$db_link->escape_string($aade_invoicemark)."',
    paroxos_authenticationCode='".$db_link->escape_string($paroxos_authenticationCode)."',
    paroxos_invoice_number='".$db_link->escape_string($paroxos_invoice_number)."'
    where paroxos_status=1
    and aade_paroxos_id>0
    and aade_invoiceuid='".$db_link->escape_string($aade_invoiceuid)."'
    and (aade_invoicemark is null or aade_invoicemark='')
    and aade_send_date is not null
    and id_".$ttt."=".$xxx_item['id'];    
    
    gks_aade_update_mark_from_id(['mark'=>$aade_invoicemark,$ttt.'_id'=>$xxx_item['id']]);


  }

  

  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql_paroxos.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  
  if ($is_I0003_eror) {
    $sxolio=gks_lang('Η αποστολή προς myData δεν ήταν επιτυχής από τον πάροχο').'<br>'.$html_error;   
  } else {
    $sxolio=gks_lang('Επιτυχής αποστολή προς myData από τον πάροχο').'<br>'.gks_lang('ΜΑΡΚ').': '.$aade_invoicemark; 
  }
  $sql="insert into ".$doc_table."_log (".$ttt."_id, add_date,user_id,sxolio) values (
  ".$xxx_item['id'].",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
    
  //echo '<pre>ssssssssssss '.$sql."\r\n";print_r($ret_send);die();

  $ret['message']='OK';
  $ret['save_but_message']='';
  if ($html_error!='') {
    $ret['message']='WARNING';
    $ret['save_but_message']=base64_encode(gks_lang('Το παραστατικό έχει σταλεί το myData από τον πάροχο αλλά το myData το απέρριψε διότι βρέθηκαν κάποια προβλήμα.<br>Κάντε πρόχειρο το παραστατικό, διορθώστε τα προβλήματα και ξαναστείλτε το.').'<br><br>'.$html_error);
  }
  $ret['success']=true;  
  //echo '<pre>';print_r($ret);die();
  return $ret;  
  
}



function gks_paroxos_ilyda_com_base64url_encode(string $data): string {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function gks_paroxos_ilyda_com_base64url_decode(string $data): string {
  $padding = 4 - (strlen($data) % 4);
  if ($padding < 4) {
  $data .= str_repeat('=', $padding);
  }
  return base64_decode(strtr($data, '-_', '+/'));
}
function gks_paroxos_ilyda_com_sign_tf1_token(array $header, array $payload, string $secretB64): string {
  $segments = [
    gks_paroxos_ilyda_com_base64url_encode(json_encode($header, JSON_UNESCAPED_SLASHES)),
    gks_paroxos_ilyda_com_base64url_encode(json_encode($payload, JSON_UNESCAPED_SLASHES))
  ];
  $signingInput = implode('.', $segments);
  $secretBytes = gks_paroxos_ilyda_com_base64url_decode($secretB64);
  $signature = hash_hmac('sha256', $signingInput, $secretBytes, true);
  $segments[] = gks_paroxos_ilyda_com_base64url_encode($signature);
  return implode('.', $segments);
}


function gks_paroxos_get_offline_qrcode_key_ilyda_com() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $paroxos_id=8;
  $cret=array('success' => false, 'message' => 'generic error', 'html'=>'');
  
  $afms=gks_paroxos_overview_get_afms($paroxos_id);
  if (count($afms)==0) {
    $cret['message']=gks_lang('Δεν βρέθηκαν σχετικές εταιρείες');
    debug_mail(false,$cret['message'],'');
    return $cret;}      
  
  //echo '<pre>';print_r($afms);die();
  $html=[];
  foreach ($afms as $afm) {
    if ($afm['paroxos_mydata_live']) {
      $url=GKS_ILYDA_COM_MODE_LIVE_API; 
    } else {
      $url=GKS_ILYDA_COM_MODE_TEST_API; 
    }
    $url.='/api/offline-qr/'.$afm['afm'].'/keys';
    
    
    $input=array('purpose' => 'Offline QR');
    $input=json_encode($input);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
              
    $headers=[];
    $headers[]='Content-Type: application/json; charset=UTF-8';
    $headers[]='Accept: application/json';
    $headers[]='Authorization: Basic '.base64_encode($afm['username'].':'.$afm['password']);  
     
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 

    $result=curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info =curl_getinfo($ch);
    curl_close ($ch);

    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    if ($gks_curl_http_code!=201) {
      $cret['message']=gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (1)<br>HTTP Code: '.$gks_curl_http_code.'<br>'.$result;
      debug_mail(false,'error ylida response',$cret['message']);
      return $cret;} 
    
    $response_array = json_decode($result, true);
    if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
      $cret['message']=gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (2)<br>HTTP Code: '.$gks_curl_http_code.'<br>'.$result;
      debug_mail(false,'ilyda vs.gr json_decode error',$cret['message']);
      return $cret;}

    
    $mykey=$response_array;
    if (!(is_array($mykey) and 
          isset($mykey['keyIdentifier']) and
          isset($mykey['secret']) and
          isset($mykey['algorithm']) and
          isset($mykey['companyVat']) and
          isset($mykey['validFrom']) and
          isset($mykey['validTo']))) {
      $cret['message']=gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (3)<br>HTTP Code: '.$gks_curl_http_code.'<br>'.$result;
      debug_mail(false,'ilyda vs.gr json_decode error',$cret['message']);
      return $cret;}
    
/* {
"keyIdentifier":"09a0ab96-81f4-40d8-a94f-67be403c196e",
"keyVersion":59,
"secret":"hLfmcKjoatCyGFI41FaIC3hVgQ116cr3bkTrO2F_xTI",
"algorithm":"OFFLINE_QR_JWS",
"purpose":"Offline QR",
"companyVat":"177472438",
"issuedAt":1767180061.460170702,
"validFrom":1767180061.460170702,
"validTo":1768044061.460170702,
"installationVerifiedAt":null,
"linkBaseUrl":"https://test.vs.gr/iv/invoice/offline"
} 

Array
(
  [keyIdentifier] => f040a64a-e70c-486f-92fc-68b91901f69a
  [keyVersion] => 60
  [secret] => rd0ItWwZu0GLlYtHv1z3Ff4dl5DvlDrvLO6k5zYpcH4
  [algorithm] => OFFLINE_QR_JWS
  [purpose] => Offline QR
  [companyVat] => 177472438
  [issuedAt] => 1767212804.1544
  [validFrom] => 1767212804.1544
  [validTo] => 1768076804.1544
  [installationVerifiedAt] => 
  [linkBaseUrl] => https://test.vs.gr/iv/invoice/offline
  [revokeReason] => 
)
*/          
    if (isset($mykey['status'])==false) $mykey['status']='ISSUED';
    if (isset($mykey['revokedAt'])==false) $mykey['revokedAt']='';
    if (isset($mykey['revokeReason'])==false) $mykey['revokeReason']='';
    
    $issuedAt='null'; if (isset($mykey['issuedAt']) and intval($mykey['issuedAt'])>0) $issuedAt="'".date('Y-m-d H:i:s', intval($mykey['issuedAt']))."'";
    $revokedAt='null'; if (isset($mykey['revokedAt']) and intval($mykey['revokedAt'])>0) $revokedAt="'".date('Y-m-d H:i:s', intval($mykey['revokedAt']))."'";
    $validFrom='null'; if (isset($mykey['validFrom']) and intval($mykey['validFrom'])>0) $validFrom="'".date('Y-m-d H:i:s', intval($mykey['validFrom']))."'";
    $validTo='null'; if (isset($mykey['validTo']) and intval($mykey['validTo'])>0) $validTo="'".date('Y-m-d H:i:s', intval($mykey['validTo']))."'";
    $installationVerifiedAt='null'; if (isset($mykey['installationVerifiedAt']) and intval($mykey['installationVerifiedAt'])>0) $installationVerifiedAt="'".date('Y-m-d H:i:s', intval($mykey['installationVerifiedAt']))."'";

    $local_status='NOT_VALID';
    if (intval($mykey['revokedAt'])==0 and 
        intval($mykey['validFrom'])>0 and time()>=intval($mykey['validFrom']) and 
        intval($mykey['validTo'])>0   and time()<=intval($mykey['validTo']) and 
        $mykey['status']=='VERIFIED') {
      $local_status='VALID';      
    }
    
    $sql="insert into gks_paroxos_tf1_keys (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    local_status,paroxos_id,afm,
    secret,
    keyIdentifier,keyVersion,algorithm,purpose,
    status,issuedAt,revokedAt,validFrom,validTo,
    installationVerifiedAt,revokeReason,linkBaseUrl
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    '".$local_status."',".$paroxos_id.",'".$db_link->escape_string($afm['afm'])."',
    '".$db_link->escape_string($mykey['secret'])."',
    '".$db_link->escape_string($mykey['keyIdentifier'])."',
    ".intval($mykey['keyVersion']).",
    '".$db_link->escape_string($mykey['algorithm'])."',
    '".$db_link->escape_string($mykey['purpose'])."',
    '".$db_link->escape_string($mykey['status'])."',
    ".$issuedAt.",
    ".$revokedAt.",
    ".$validFrom.",
    ".$validTo.",
    ".$installationVerifiedAt.",
    '".$db_link->escape_string($mykey['revokeReason'])."',
    '".$db_link->escape_string($mykey['linkBaseUrl'])."'
    )";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$cret['message']='sql error';return $cret;}
    
    $id_paroxos_tf1_keys = $db_link->insert_id;     
    
    $tmpmsg=gks_lang('Για το ΑΦΜ [1] δημιουργήθηκε ένα νέο κλειδί με αναγνωριστικό: [2]');
    $tmpmsg=str_replace('[1]',$afm['afm'],$tmpmsg);
    $tmpmsg=str_replace('[2]',$mykey['keyIdentifier'],$tmpmsg);
    $html[]=$tmpmsg;
    
    //echo '<pre>';print_r($mykey);die();
    
    
    
    if ($afm['paroxos_mydata_live']) {
      $url=GKS_ILYDA_COM_MODE_LIVE_API; 
    } else {
      $url=GKS_ILYDA_COM_MODE_TEST_API; 
    }
    $url.='/api/offline-qr/'.$afm['afm'].'/keys/'.$mykey['keyIdentifier'].'/verify';
    
    
    $input=array();
    $input=json_encode($input);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
              
    $headers=[];
    $headers[]='Content-Type: application/json; charset=UTF-8';
    $headers[]='Accept: application/json';
    $headers[]='Authorization: Basic '.base64_encode($afm['username'].':'.$afm['password']);  
     
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 

    $result=curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info =curl_getinfo($ch);
    curl_close ($ch);

    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    if ($gks_curl_http_code!=200) {
      $cret['message']=gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (1)<br>HTTP Code: '.$gks_curl_http_code.'<br>'.$result;
      debug_mail(false,'error ylida response error',$cret['message']);
      return $cret;}

    
    $response_array = json_decode($result, true);
    if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
      $cret['message']=gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (2)<br>HTTP Code: '.$gks_curl_http_code.'<br>'.$result;
      debug_mail(false,'ilyda vs.gr json_decode error',$cret['message']);
      return $cret;}      
      
/*
Array
(
  [keyIdentifier] => 368a002d-d409-489c-9ac5-8233ebc38c78
  [keyVersion] => 66
  [algorithm] => OFFLINE_QR_JWS
  [purpose] => Offline QR
  [status] => VERIFIED
  [issuedAt] => 1767216278.9183
  [revokedAt] => 
  [validFrom] => 1767216278.9183
  [validTo] => 1768080278.9183
  [installationVerifiedAt] => 1767216279.2188
  [revokeReason] => 
  [linkBaseUrl] => https://test.vs.gr/iv/invoice/offline
)
*/  
    $mykey=$response_array;
    if (!(is_array($mykey) and 
          isset($mykey['keyIdentifier']) and
          isset($mykey['algorithm']) and
          isset($mykey['validFrom']) and
          isset($mykey['validTo']) and 
          isset($mykey['installationVerifiedAt']) and 
          isset($mykey['status']) and 
          $mykey['status']=='VERIFIED')) {
      debug_mail(false,'ilyda vs.gr json_decode error',base64_encode($result) .'|||'.$result.'|||'.base64_encode($response) .'|||'.$response);
      $cret['message']=gks_lang('Σφάλμα απόκρισης από τον ΥΛΙΔΑ server').' (3)<br>HTTP Code: '.$gks_curl_http_code.'<br>'.$result;
      return $cret;}

    $installationVerifiedAt='null'; if (isset($mykey['installationVerifiedAt']) and intval($mykey['installationVerifiedAt'])>0) $installationVerifiedAt="'".date('Y-m-d H:i:s', intval($mykey['installationVerifiedAt']))."'";
    
     
    $sql="update gks_paroxos_tf1_keys set
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    local_status='ACTIVE',
    status='".$mykey['status']."',
    installationVerifiedAt=".$installationVerifiedAt."
    where id_paroxos_tf1_keys=".$id_paroxos_tf1_keys;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$cret['message']='sql error';return $cret;}        
    
    $html[]=gks_lang('To κλειδί έχει επαληθευτεί επιτυχώς');

    
    $sql="update gks_paroxos_tf1_keys set 
    local_status='ARCHIVE' 
    where local_status='ACTIVE' 
    and afm='".$db_link->escape_string($afm['afm'])."' 
    and id_paroxos_tf1_keys<>".$id_paroxos_tf1_keys;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);$cret['message']='sql error';return $cret;}        
    
    
    //echo '<pre>';print_r($response_array);die();  
            
  }
  
  $cret['html']=implode('<br>',$html);
  $cret['success']=true;
  $cret['message']='OK';
  
  return $cret; 
}

function gks_paroxos_get_keys_ilyda_com() {
  global $db_link;
  $paroxos_id=8;
  $ret = array('success' => false, 'message' => 'generic error gks_paroxos_get_keys_ilyda_com');
  
  $afms=gks_paroxos_overview_get_afms($paroxos_id);
  if (count($afms)==0) {$ret['success']=true;$ret['message']=gks_lang('Δεν βρέθηκαν ΑΦΜ'); return $ret;}
  
  //echo '<pre>';print_r($afms);die();
  $need_new_keys=false;

  $mynow=date('Y-m-d H:i:s');
  foreach ($afms as $afm) {
    $sql_tf1="select validTo
    from gks_paroxos_tf1_keys 
    where paroxos_id=".$paroxos_id."
    and afm='".$db_link->escape_string($afm['afm'])."'
    and local_status='ACTIVE'
    and status='VERIFIED'
    and validFrom<'".$mynow."'
    and validTo>'".$mynow."'
    and revokedAt is null
    order by installationVerifiedAt desc limit 1";
    $result_tf1 = $db_link->query($sql_tf1); 
	  if (!$result_tf1) {debug_mail(false,'error sql',$sql_tf1);$ret['message']='sql error';return $ret;}
    if ($result_tf1->num_rows==1) {    
      $row_tf1 = $result_tf1->fetch_assoc();
      $validTo=strtotime($row_tf1['validTo']);
      if (time() > ($validTo-(72*60*60))) { //72 vres prin
        $need_new_keys=true;
      }
    } else {
      $need_new_keys=true;
    }
  }
  if ($need_new_keys) {
    $cret=gks_paroxos_get_offline_qrcode_key_ilyda_com();
    //$cret=array('success' => false, 'message' => 'generic error asd asd asd sdd', 'html'=>'');
    if ($cret['success']==false) {
      $message_html =gks_lang('Σφάλμα κατά την λήψη κλειδιών από ΥΛΙΔΑ για το <b>Σφάλμα μετάδοσης παραστατικών</b> <span class="aade_xml_response_error">Transmission Failure 1</span>').'<br>'.$cret['message'];
      $message_viber=gks_lang('Σφάλμα κατά την λήψη κλειδιών από ΥΛΙΔΑ για το *Σφάλμα μετάδοσης παραστατικών* *Transmission Failure 1*')."\r\n".str_replace('<br>',"\r\n",$cret['message']);
      
      $sql="select gks_notification_userperm.user_id,
      gks_notification_userperm.from_user,
      gks_notification_userperm.to_email,
      gks_notification_userperm.to_viber,
      ".GKS_WP_TABLE_PREFIX."users.user_email,
      ".GKS_WP_TABLE_PREFIX."users.viber_id,
      ".GKS_WP_TABLE_PREFIX."users.viber_subscribed
      from gks_notification_userperm 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      where gks_notification_userperm.notification_type_id=600
      and gks_notification_userperm.from_admin=1 ".gks_notification_userperm_internal_users();
      //echo '<pre>aaaaaaaaa '.$sql;die();
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}      
      $users_notif=[];
      while ($row = $result->fetch_assoc()) {
        $users_notif[]=$row;
      }
      //echo '<pre>aaaaaaaaa ';print_r($users_notif);die();
      
      //notification
      foreach ($users_notif as $myuser) { 
        if ($myuser['from_user']=='1') {
          $sql="select * from gks_notification 
          where for_user_id=".$myuser['user_id']."
          and model='ylida_keys'
          and model_id=0";
          $result = $db_link->query($sql);
          if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}      
          if ($result->num_rows>=1) {
            $row = $result->fetch_assoc();
            $has_ok=intval($row['has_ok']);
            if ($has_ok==1) {
              $id_notification=intval($row['id_notification']);
              $sql="update gks_notification set
              message='".$db_link->escape_string($message_html)."',
              date_add=now(),for_date=now(),
              has_ok=0,ok_date=null,playsound=0
              where id_notification=".$id_notification;
              $result = $db_link->query($sql);
              if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}      
            }
          } else {
            $sql="insert into gks_notification (
            message,sender_id,for_user_id,date_add,for_date,has_ok,ok_date,model,model_id,playsound
            ) values (
            '".$db_link->escape_string($message_html)."',
            2,".$myuser['user_id'].",now(),now(),0,null,'ylida_keys',0,0
            )";
            $result = $db_link->query($sql);
            if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}      
          }
        }
      }
      
      //email
      $replaces=array();
      $replaces[] = array('[[message]]', $message_html);      
      $mysubject=gks_lang('Σφάλμα κατά την λήψη κλειδιών από ΥΛΙΔΑ');
      foreach ($users_notif as $myuser) { 
        //echo $myuser['user_email'];die();
        if ($myuser['to_email']=='1' and trim_gks($myuser['user_email'])!='') {
          $params=array(
            'model'=>'ylida_keys',
            'model_id'=>0,
            'to'=>trim_gks($myuser['user_email']),
            'subject'=>$mysubject,
            'template'=>3, //'empty.html',
            'replaces'=>$replaces,
          );
          $send_email_res = gks_mymail_template($params);
        }
      }
      
      //viber 
      foreach ($users_notif as $myuser) { 
        if ($myuser['to_viber']=='1' and intval($myuser['viber_subscribed'])==1 and trim_gks($myuser['viber_id'])!='') {
          gks_viber_send('ylida_keys',0,$myuser['viber_id'],$message_viber);
        }
      }
              
      
      $ret['message']=$cret['message'];
      return $ret;
    }
  }
  $ret['success']=true;
  $ret['message']='OK';
  return $ret;
}

  