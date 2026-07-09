<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function curl_postfields_flatten($data, $prefix = '') {
  if (!is_array($data)) {
    return $data; // in case someone sends an url-encoded string by mistake
  }

  $output = array();
  foreach($data as $key => $value) {
    $final_key = $prefix ? "{$prefix}[{$key}]" : $key;
    if (is_array($value)) {
      // @todo: handle name collision here if needed
      $output += curl_postfields_flatten($value, $final_key);
    }
    else {
      $output[$final_key] = $value;
    }
  }
  return $output;
}

function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

    if ( is_object( $arrays ) ) {
        $arrays = get_object_vars( $arrays );
    }

    foreach ( $arrays AS $key => $value ) {
        $k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
        if ( is_array( $value ) OR is_object( $value )  ) {
            http_build_query_for_curl( $value, $new, $k );
        } else {
            $new[$k] = $value;
        }
    }
}

function gks_paroxos_tesae_gr_get_url($sub_url,$request_type,$input) {
 
  
  $p_send=$input;
  $p_response=[];
 
  if ($input['paroxos_live']) {
    $url=GKS_TESAE_GR_MODE_LIVE_API.$sub_url;
  } else {
    $url=GKS_TESAE_GR_MODE_TEST_API.$sub_url;
  }
  
  $paroxos_token='';
  if (isset($input['paroxos_token'])) $paroxos_token=$input['paroxos_token'];
  
  unset($input['paroxos_live']);
  unset($input['Email']);
  unset($input['password']);
  unset($input['acc_inv_id']);
  unset($input['acc_pay_id']);
  unset($input['id_company_paroxos']);
  unset($input['paroxos_mydata_live']);
  unset($input['paroxos_token']);
  unset($input['paroxos_url']);
  
  //echo 'url: '.$url."\n";die();
  
  //echo '<pre>'.print_r($input,true)."\n"; die(); 
  
  if ($request_type=='GET') {
    if (is_array($input)) {
      $myq=http_build_query($input);
      $url.='?'.$myq;
      //echo '<pre>'.$url;die();
    } else if (is_string($input)) {
      $url.='?'.$input;
    }
  } else {
//  	$input=array(
//  		'mode' => $formdata,
//  		'formdata' => $input,
//  		
//    );
    //$input=json_encode($input);
    $input=http_build_query($input);
    //$input2=$input;
    //http_build_query_for_curl( $input2, $post2 );
    //$input=$post2;
    //print '<pre>';print_r($post);die();
    
    //$input=curl_postfields_flatten($input);
    
  }
  //file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/ttt.txt',$input);
  
  //echo '<pre>';var_dump($input); die(); 
  //echo '<pre>'.$input."\n"; die(); 
  //echo '<pre>';print_r($input); die(); 

  //$url='https://test.easyfilesselection.com/my/admin-acc-aade-docs-exec.php';
  
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
  
  curl_setopt($ch, CURLOPT_HEADER, true);
  if ($request_type=='POST') {
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);

    //$headers[]='Content-Type: multipart/form-data';
    $headers[]='Content-Type: application/x-www-form-urlencoded';
    //$headers[]='Accept: application/json';
    //$headers[]='Content-Type: application/json';
    //$headers[]='Content-Length: '.strlen($input);
    
  }
  if ($paroxos_token!='') {
    $headers[]='Authorization: Bearer '.$paroxos_token;
  }    
  curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
  
  //echo '<pre>';print_r($headers);die();
  
  //echo '<pre>'.$url;die();
  //echo '<pre>'.$paroxos_token;die();
  //echo '<pre>'.serialize($input);die();
  
  
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
//    
//  //echo '<pre>'; print_r($gks_curl_info);print $result;die();
  //echo '<pre>'; print $result;die();
  
  
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
    debug_mail(false,'tesae.gr error','Δεν βρέθηκε ο διακομιστής<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'tesae.gr Δεν βρέθηκε ο διακομιστής');
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    debug_mail(false,'tesae.gr error','Δεν βρέθηκε το σημείο<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'tesae.gr Δεν βρέθηκε το σημείο');
  
  } else if ($gks_curl_http_code==400) { 
    $error='tesae.gr Σφάλμα (400) Parameters are invalid.';
    $parts=explode("\r\n\r\n",$result,2);
    if (count($parts)==2) {
    	$response=trim($parts[1]);
		  $response_array = json_decode($response, true);
		  if (!($response_array === null && json_last_error() !== JSON_ERROR_NONE)) {
				$paroxos_error=[];
				if (isset($response_array['code']))  $paroxos_error[]='Κωδικός σφάλματος: '.$response_array['code'];
				if (isset($response_array['error']['title']))  $paroxos_error[]='Τίτλος σφάλματος: '.$response_array['error']['title'];
				if (isset($response_array['error']['message']))  $paroxos_error[]='Μήνυμα σφάλματος: '.$response_array['error']['message'];
    		if (isset($response_array['error']['data']['validation_errors'])) {
    			foreach ($response_array['error']['data']['validation_errors'] as $verror) {
    				$paroxos_error[]=$verror;
    			} 
    		}
    		if (isset($response_array['error']['data']) and is_array($response_array['error']['data'])) {
    			foreach ($response_array['error']['data'] as $mkey => $vdata) {
    				if (is_string($vdata) or is_numeric($vdata)) 
    				$paroxos_error[]=$mkey.': '.$vdata;
    			} 
    		}
    		 
    		if (count($paroxos_error)>0 and isset($response_array['ok'])) {
    			$error.='<br>'.implode('<br>',$paroxos_error);
	    		return array('success' => true, 'message' => $error, 'response_array' => $response_array);
    		}
   		
				//print '<pre>';print_r($response_array);die();
				
			}
			
    }
    
    
    debug_mail(false,'tesae.gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error);
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $error='tesae.gr Δεν επιτρέπεται η πρόσβαση.';
    if ($sub_url=='/api/send') $error.=' Unauthorized request. The jwt is either invalid or expired.';
    debug_mail(false,'tesae.gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error);
  
  } else if ($gks_curl_http_code==403) { 
    $error='tesae.gr Σφάλμα (403).';
    if ($sub_url=='/api/account/loginToSubscription') $error.=' Username, password or secret key is invalid';
    if ($sub_url=='/api/token/refresh') $error.=' Either token or refresh token is invalid';
    debug_mail(false,'tesae.gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result.'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error);
  
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    debug_mail(false,'tesae.gr error','Γενικό σφάλμα (2): HTTP Response Error'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'tesae.gr Γενικό σφάλμα(2): HTTP Response Error: '.$gks_curl_http_code);
  
  }
  
  
  
  $parts=explode("\r\n\r\n",$result,2);
  if (count($parts)!=2) {
    debug_mail(false,'tesae.gr result error',$result);
    return array('success' => false, 'message' => 'tesae.gr '.gks_lang('Σφάλμα δεδομένων').' (1).'.$result);}

  $response=trim($parts[1]);
  if ($response=='') {
    debug_mail(false,'tesae.gr response error',$response);
    return array('success' => false, 'message' => 'tesae.gr '.gks_lang('Σφάλμα δεδομένων').' (2).'.$result);}

	if ($response=='Pegasus Generic Error (1)') {
    debug_mail(false,'tesae.gr response error',$response);
    return array('success' => false, 'message' => 'tesae.gr '.gks_lang('Σφάλμα δεδομένων').' (2.1).<br>'.$response);}
		
	
  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'tesae.gr json_decode error',$response);
    return array('success' => false, 'message' => 'tesae.gr '.gks_lang('Σφάλμα δεδομένων').' (3).'.$result);}

  
  

  //echo '<pre>';print_r($response_array);die();
  
  return array('success' => true, 'message' => 'OK', 'response_array' => $response_array);
}

function gks_paroxos_invoice_xml_build_tesae_gr($id,$paroxos_params,$struct_data) {
	
	$ret = array('success' => false, 'message' => 'generic error');
	
	//echo  '<pre>';print_r($paroxos_params); die();
	//echo '<pre>';print_r($struct_data);die();
	
	$xml=$struct_data['xml'];
	$adata=array();
	
  $adata['transmission_failure']=0;
  
	
	//$adata['id']=$struct_data['row']['id_acc_inv'];
	//if (isset($xml['issuer']['vatNumber'])) $adata['is_vatNumber']=$xml['issuer']['vatNumber']; 
	if (isset($xml['issuer']['vatNumber'])) $adata['is_vat']=$xml['issuer']['vatNumber']; 
	if (isset($xml['issuer']['country'])) $adata['is_country']=$xml['issuer']['country']; 
	if (isset($paroxos_params['paroxos_branch'])) $adata['is_branch']=$paroxos_params['paroxos_branch'];
	if (isset($xml['issuer']['name'])) $adata['is_name']=$xml['issuer']['name']; 

	if (isset($xml['issuer']['address']['street'])) $adata['is_address']=$xml['issuer']['address']['street'];
	$adata['is_address_num']='';
	if (isset($xml['issuer']['address']['streetNumber'])) $adata['is_address_num']=$xml['issuer']['address']['streetNumber'];
	if (isset($xml['issuer']['address']['city'])) $adata['is_city']=$xml['issuer']['address']['city'];
	if (isset($xml['issuer']['address']['postalCode'])) $adata['is_zip']=$xml['issuer']['address']['postalCode'];
	
	//if (isset($xml['counterpart']['vatNumber'])) $adata['cp_vatNumber']=$xml['counterpart']['vatNumber']; 
	if (isset($xml['counterpart']['vatNumber'])) $adata['cp_vat']=$xml['counterpart']['vatNumber']; 
	if (isset($xml['counterpart']['country'])) $adata['cp_country']=$xml['counterpart']['country']; 
  if (isset($xml['counterpart']['branch'])) $adata['cp_branch']=$xml['counterpart']['branch'];
	if (isset($xml['counterpart']['name'])) $adata['cp_name']=$xml['counterpart']['name'];
	
	if (isset($xml['counterpart']['address']['street'])) $adata['cp_address']=$xml['counterpart']['address']['street'];
	$adata['cp_address_num']='';
	if (isset($xml['counterpart']['address']['streetNumber'])) $adata['cp_address_num']=$xml['counterpart']['address']['streetNumber'];
	if (isset($xml['counterpart']['address']['city'])) $adata['cp_city']=$xml['counterpart']['address']['city'];
	if (isset($xml['counterpart']['address']['postalCode'])) $adata['cp_zip']=$xml['counterpart']['address']['postalCode'];
	
  //slr_taxr_name
  //slr_taxr_vat
  
  if (isset($xml['invoiceHeader']['series'])) $adata['series']=$xml['invoiceHeader']['series'];
  if (isset($xml['invoiceHeader']['aa'])) $adata['aa']=$xml['invoiceHeader']['aa'];
	if (isset($xml['invoiceHeader']['issueDate'])) $adata['issuedate']=$xml['invoiceHeader']['issueDate'];
	
	if (isset($xml['invoiceHeader']['issueTime'])) $adata['issuetime']=$xml['invoiceHeader']['issueTime'];
	
	
	if (isset($xml['invoiceHeader']['invoiceType'])) $adata['invtype']=$xml['invoiceHeader']['invoiceType']; //Invoice Code
	//$adata['deleteme1']='ggg';
	
	//echo '<pre>';var_dump($xml['invoiceHeader']['currency']);die();
	
	//"fuelInvoice": "Number(1): Παραστατικό καυσίμων (ένδειξη)",
	//vatpaysusp "vatPaymentSuspension": "Number(1): Αναστολή Καταβολής ΦΠΑ",
	if (isset($xml['invoiceHeader']['currency'])) $adata['currency']=$xml['invoiceHeader']['currency'];
	//$adata['deleteme2']='ggg';
	
  //exchrate "exchangeRate": "Number(13,5): Ισοτιμία",
	$adata['exchrate']=1;
	
	$adata['correlinv']='';
	if (isset($struct_data['xml']['invoiceHeader']['correlatedInvoices'])) {
		$correlatedInvoices=[];
		foreach ($struct_data['xml']['invoiceHeader']['correlatedInvoices'] as $colinv) {
			$correlatedInvoices[]=$colinv['aade_invoicemark'];
		}
		if (count($correlatedInvoices)>0) $adata['correlinv']=implode(', ',$correlatedInvoices);
	}
	//slfpricing "selfPricing": "Number(1): Αυτοτιμολόγηση",
	//$adata['slfpricing']=0;
	$adata['dispdate']='';
	if (isset($xml['invoiceHeader']['dispatchDate'])) $adata['dispdate']=$xml['invoiceHeader']['dispatchDate'];
	$adata['disptime']='';
	if (isset($xml['invoiceHeader']['dispatchTime'])) $adata['disptime']=$xml['invoiceHeader']['dispatchTime'];
  $adata['vehiclenum']='';
	if (isset($xml['invoiceHeader']['vehicleNumber'])) $adata['vehiclenum']=$xml['invoiceHeader']['vehicleNumber'];
	
	$adata['mvpurpose']='';
	if (isset($xml['invoiceHeader']['movePurpose'])) $adata['mvpurpose']=$xml['invoiceHeader']['movePurpose'];
	
	
	if (isset($xml['invoiceSummary']['totalNetValue'])) $adata['tnetvalue']=$xml['invoiceSummary']['totalNetValue'];
	if (isset($xml['invoiceSummary']['totalVatAmount'])) $adata['tvat_am']=$xml['invoiceSummary']['totalVatAmount'];
	if (isset($xml['invoiceSummary']['totalWithheldAmount'])) $adata['twthhld_am']=$xml['invoiceSummary']['totalWithheldAmount'];
	if (isset($xml['invoiceSummary']['totalFeesAmount'])) $adata['tfees_am']=$xml['invoiceSummary']['totalFeesAmount'];
	if (isset($xml['invoiceSummary']['totalStampDutyAmount'])) $adata['tstamp_am']=$xml['invoiceSummary']['totalStampDutyAmount'];
	if (isset($xml['invoiceSummary']['totalOtherTaxesAmount'])) $adata['ttax_am']=$xml['invoiceSummary']['totalOtherTaxesAmount'];
	if (isset($xml['invoiceSummary']['totalDeductionsAmount'])) $adata['tdeduction']=$xml['invoiceSummary']['totalDeductionsAmount'];
	if (isset($xml['invoiceSummary']['totalGrossValue'])) $adata['tgross_val']=$xml['invoiceSummary']['totalGrossValue'];
  
  //$adata['p00']=1;
  if (isset($xml['invoiceHeader']['eidos_parastatikou_type_id']) and 
    ($xml['invoiceHeader']['eidos_parastatikou_type_id']==1 or $xml['invoiceHeader']['eidos_parastatikou_type_id']==2)) {
    $adata['p00']=$xml['invoiceHeader']['eidos_parastatikou_type_id']; 
  }
    
  //"p00": "Number(1): Έσοδο/Έξοδο (1/2)",
  
  
  /*
  "pnr01": "String(100): External UID",
  Η τιμή που θα συμπληρώσετε είναι ένα string όπου κάνετε concat τα παρακάτω στοιχεία
  και ανάμεσά τους υπάρχει το token #:
  is_vat
  issuedate
  is_branch
  invtype
  series
  aa
  για παράδειγμα: 094420307#20230203#0#1.1#ΤΠ-ΔΑ#123#
	*/
	$pnr01=$adata['is_vat'].'#'.
	       $xml['invoiceHeader']['issueDate_compact'].'#'.
	       $adata['is_branch'].'#'.
	       $adata['invtype'].'#'.
	       $adata['series'].'#'.
	       $adata['aa'].'#';
	$adata['pnr01']=$pnr01;
	
	$einv02=[];
	$linenumber_cc=0;
	foreach ($struct_data['prow_array'] as $prow) {
	  $linenumber_cc++;
		$item=array();
		$item['rectype']=0;
		$item['mydt10']=$prow['id_acc_inv_product'];
		
		//if (isset($prow['xml_lineNumber'])) $item['linenumber']=$prow['xml_lineNumber'];
		$item['linenumber']=$linenumber_cc;
	  //"rectype": "Number(1): Τύπος γραμμής",
	  if (isset($prow['xml_quantity'])) {
	    $item['quantity']=$prow['xml_quantity'];
	    $item['p105']=$prow['xml_quantity'];
	  }
	  //"fuelCode": "Number(5): Κωδικός Καυσίμου",
	  if (isset($prow['xml_measurementUnit'])) $item['munit']=$prow['xml_measurementUnit'];
	  //"invoiceDetailType": "String(20): Επισήμανση",
	
		if (isset($prow['product_price_final_all_net'])) $item['netvalue']=$prow['product_price_final_all_net'];
		//if (isset($prow['product_price_final_all_total'])) $item['totalValue']=$prow['product_price_final_all_total'];	
    
    if (isset($prow['xml_vatCategory'])) $item['vatcat']=$prow['xml_vatCategory'];
		if (isset($prow['xml_vatExemptionCategory'])) $item['vatexcat']=$prow['xml_vatExemptionCategory'];
		//"applicationId": "String(50): Αρ.Δήλωσης",
		//"applicationDate": "Date(10): Ημ/νία Δήλωσης (YYYY-mm-dd)",
		//"doy": "String(45): ΔΟΥ Δήλωσης",
		//"shipId": "String(100): Στοιχεία Πλοίου",
		//"discountOption": "Number(1): Δικαίωμα Έκπτωσης",		
		//$item['discountOption']=1;
		
		if (isset($prow['xml_withheldPercentCategory']) and $prow['xml_withheldAmount']!=0) {
		  $item['wthhld_am']=$prow['xml_withheldAmount'];
		  $item['wthhldpcat']=$prow['xml_withheldPercentCategory'];
		}
		if (isset($prow['xml_stampDutyPercentCategory']) and $prow['xml_stampDutyAmount']!=0) {
		  $item['stampvalue']=$prow['xml_stampDutyAmount'];
		  $item['stamppcat']=$prow['xml_stampDutyPercentCategory'];
		}
		if (isset($prow['xml_feesPercentCategory']) and $prow['xml_feesAmount']!=0) {
		  $item['fees_am']=$prow['xml_feesAmount'];
		  $item['feestype']=$prow['xml_feesPercentCategory'];
		}
		if (isset($prow['xml_otherTaxesPercentCategory']) and $prow['xml_otherTaxesAmount']!=0) {
		  $item['otax_am']=$prow['xml_otherTaxesAmount'];
		  $item['otaxpcat']=$prow['xml_otherTaxesPercentCategory'];
		}
		
		if (isset($prow['xml_vatAmount'])) $item['fpavalue']=$prow['xml_vatAmount'];
		if (isset($prow['xml_deductionsAmount'])) $item['deducvalue']=$prow['xml_deductionsAmount'];
	
    //"mydt10_p00": "Number(1): Έσοδο/Έξοδο (1/2)",
    
		$classification_count=0;
		if (isset($prow['xml_incomeClassification'])) {
			foreach ($prow['xml_incomeClassification'] as $cl_item) {
				$classification_count++;
				if (isset($cl_item['category'])) $item['mydt57_p00']=$cl_item['category'];
				if (isset($cl_item['type'])) $item['mydt61_p00']=$cl_item['type'];
				$item['p104']=1;
			} 	
		}
		if (isset($prow['xml_expensesClassification'])) {
			foreach ($prow['xml_expensesClassification'] as $cl_item) {
				$classification_count++;
				if (isset($cl_item['category'])) $item['mydt58_p00']=$cl_item['category'];
				if (isset($cl_item['type'])) $item['mydt62_p00']=$cl_item['type'];
				$item['p104']=2;
			}
		}
		
		if ($classification_count>1) {
			$ret['message']='Ο συγκεκριμένος πάροχος δεν υποστηρίζει πάνω από έναν χαρακτηρισμό ανά είδος.<br>Γραμμή '.$prow['xml_lineNumber'];
			debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
		}
		
		//"vclassificationType": "String(20): Κωδικός Χαρακτηρισμού",
		
		//"class_amount": "Number(13,5): Σύνολο Καθαρής Αξίας",    

    if (isset($prow['xml_product_comments'])) {
      $item['comments']=$prow['xml_product_comments'];
    } else {
      $item['comments']='';
    }
    
    //"taxtype": "String(10): Είδος Φόρου",
		//"taxcat": "String(10): Κατηγορία Φόρου",
		//"taxamount": "Number(13,5): Ποσό Φόρου",
		//"taxunder": "Number(13,5): Υποκείμενη Αξία",
	  
	  //"p106": "String: Περιγραφή Pegasus. Χρησιμοποιείται στην προβολή του παραστατικού."
	  if (isset($prow['xml_product_descr'])) {
	    $item['p106']=$prow['xml_product_descr'];
	  } else {
	    $item['p106']='';
	  }
	  
	  if (isset($item['mydt10'])==false) $item['mydt10']=$prow['id_acc_inv_product']; //"Number(14): Μ.Κ. Παραστατικού
	  if (isset($item['mydt57_p00'])==false) $item['mydt57_p00']=''; //String(20): Χαρακτηρισμός Εσόδου
	  if (isset($item['mydt58_p00'])==false) $item['mydt58_p00']=''; //String(20): Χαρακτηρισμός Εξόδου
	  if (isset($item['mydt61_p00'])==false) $item['mydt61_p00']=''; //String(20): Χαρακτηρισμός Ε3 Εσοδου
	  if (isset($item['mydt62_p00'])==false) $item['mydt62_p00']=''; //String(20): Χαρακτηρισμός Ε3 Εξόδου
	  if (isset($item['mydt62_p01'])==false) $item['mydt62_p01']=''; //String(20): Χαρακτηρισμός Ε3 ΦΠΑ Εξόδου
	  if (isset($item['linenumber'])==false) $item['linenumber']=$linenumber_cc; //"Number(10): ΑΑ Γραμμής
	  if (isset($item['rectype'])==false) $item['rectype']=0; //Number(1): Τύπος γραμμής
	  if (isset($item['p104'])==false) $item['p104']=0; //Number(1): Έσοδο/Έξοδο (1/2)
	  if (isset($item['quantity'])==false) $item['quantity']=0; //Number(10,5): Ποσότητα
	  if (isset($item['fuelcode'])==false) $item['fuelcode']=0; //Number(5): Κωδικός Καυσίμου
	  if (isset($item['munit'])==false) $item['munit']='1'; //String(20): Μονάδα Μέτρησης
	  if (isset($item['invdtype'])==false) $item['invdtype']=''; //String(20): Επισήμανση mydt60
	  if (isset($item['netvalue'])==false) $item['netvalue']=0; //Number(10,5): Καθαρή Αξία
	  if (isset($item['vatcat'])==false) $item['vatcat']=''; //String(20): Κατηγορια ΦΠΑ mydt51
	  if (isset($item['vatexcat'])==false) $item['vatexcat']=''; //String(20): Κατηγορια Εξαίρεσης ΦΠΑ mydt52
	  if (isset($item['fpavalue'])==false) $item['fpavalue']=0; //Number(10,5): Αξία ΦΠΑ
	  if (isset($item['appid'])==false) $item['appid']=''; //String(50): Αρ.Δήλωσης
	  if (isset($item['appdate'])==false) $item['appdate']=''; //Date(10): Ημ/νία Δήλωσης (YYYY-mm-dd)
	  if (isset($item['doy'])==false) $item['doy']=''; //String(45): ΔΟΥ Δήλωσης
	  if (isset($item['shipid'])==false) $item['shipid']=''; //String(100): Στοιχεία Πλοίου
	  if (isset($item['discoption'])==false) $item['discoption']=1; //Number(1): Δικαίωμα Έκπτωσης
	  if (isset($item['wthhldpcat'])==false) $item['wthhldpcat']=''; //String(20): Κατηγορία Παρακρ.Φόρου mydt53
	  if (isset($item['wthhld_am'])==false) $item['wthhld_am']=0; //Number(13,5): Ποσό Παρακράτησης Φόρου
	  if (isset($item['stampvalue'])==false) $item['stampvalue']=0; //Number(13,5): Αξία Χαρτοσήμου
	  if (isset($item['stamppcat'])==false) $item['stamppcat']=''; //String(20): Κατηγορία Συντελεστή Χαρτοσήμου mydt55
	  if (isset($item['feestype'])==false) $item['feestype']=''; //String(20): Κατηγορία Συντελεστή Τελών mydt56
	  if (isset($item['fees_am'])==false) $item['fees_am']=0; //Number(13,5): Ποσό Τελών
	  if (isset($item['otaxpcat'])==false) $item['otaxpcat']=''; //String(20): Κατηγορια Λοιπών Φόρων mydt54
	  if (isset($item['otax_am'])==false) $item['otax_am']=0; //Number(13,5): Ποσό Λοιπών Φόρων
	  if (isset($item['deducvalue'])==false) $item['deducvalue']=0; //Number(13,5): Αξία Κρατήσεων
	  if (isset($item['comments'])==false) $item['comments']=''; //String(350): Σχόλια
	  if (isset($item['taxcat'])==false) $item['taxcat']=''; //String(10): Κατηγορία Φόρου
	  if (isset($item['taxtype'])==false) $item['taxtype']=''; //String(10): Είδος Φόρου
	  if (isset($item['taxamount'])==false) $item['taxamount']=0; //Number(13,2): Ποσό Φόρου
	  if (isset($item['taxunder'])==false) $item['taxunder']=0; //Number(13,2): Υποκείμενη Αξία
	  if (isset($item['p105'])==false) $item['p105']=0; //Number(10,2): Ποσότητα Pegasus. Χρησιμοποιείται στην προβολή του παραστατικού.
	  if (isset($item['p106'])==false) $item['p106']=''; //String: Περιγραφή Pegasus. Χρησιμοποιείται στην προβολή του παραστατικού.
	  
	  
	
	  $einv02[]=$item;
	}
	
	//$adata['einv02']=http_build_query($einv02);
	$adata['einv02']=json_encode($einv02);
	 
  //print '<pre>';print_r($einv02);die();

	//"payType": "String(30): Τρόπος Πληρωμής",

  
	$paymentMethods=[];
	if (isset($xml['paymentMethods']) and count($xml['paymentMethods'])>0) {
		foreach ($xml['paymentMethods'] as $pm) {
      //echo '<pre>';print_r($pm);die();
      //echo '<pre>';var_dump($pm);die();
			$paymentMethods[]=array(
				'type'=> $pm['type'].'',                //String(5): Τύπος Πληρωμής,
				'amount'=> $pm['amount'].'',            //Number(13,2): Ποσό Πληρωμής
				'tid' => '',                            //String(50): Μοναδική Ταυτότητα Πληρωμής
				'amount_t' => '0.00',                      //Number(13,2): Ποσό Φιλοδωρήματος
				//'p_info' => $pm['paymentMethodInfo'], //String(150): Πληροφορίες
				'p_sign' => '',                       //String(150): Υπογραφή Πληρωμής Παρόχου
			);
			
			$adata['paytype']=$pm['type'];
			
		}
		//echo '<pre>';print_r($paymentMethods);die();
	}

  if (count($paymentMethods) > 1) {
		$ret['message']='Ο συγκεκριμένος πάροχος δεν υποστηρίζει πολλαπλούς τρόπους πληρωμής';
		debug_mail(false,$ret['message'],print_r($paymentMethods, true)); return $ret;
	}
		 
	if (count($paymentMethods) > 0) {
		//$adata['payments']=http_build_query($paymentMethods);
	  $adata['payments']=json_encode($paymentMethods);
	}
	
	$adata['einv09']=json_encode(array()); //Συναλλασσόμενοι Παραστατικού
	$adata['einv00']='{"p01":"","p02":"","p010":"","p020":"el"}'; //Στοιχεία Αποστολής Παραστατικού
	$adata['lang']='GR';
	if (isset($xml['invoiceHeader']['note_doc'])) $adata['p_notes']=$xml['invoiceHeader']['note_doc'];
	
	//$adata['p_notes']=''; //Σημειώσεις
	
	//print '<pre>';print_r($adata);die();
	
  //$ret['file_data']=array(
  //  'data' => $adata,
  //);
  $ret['file_data']=$adata;
  
  
  $ret['message']='OK';
  $ret['success']=true;

  return $ret;	
}



function gks_paroxos_invoice_xml_send_tesae_gr($id,$paroxos_params,$struct_data,$file_data) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;	
  global $gks_cache_version;
  
	$ret = array('success' => false, 'message' => 'generic error');

  $doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
  } else {
    $xxx='';
  }
  
  //echo '<pre>';echo $id; die();
  //echo '<pre>';print_r($paroxos_params); die();
  //echo '<pre>';print_r($struct_data); die();
  //echo '<pre>ddddddddddd ';print_r($file_data); die();

	if (isset($paroxos_params['pc_key'])==false or trim_gks($paroxos_params['pc_key'])=='') {
	  $ret['message']='Δεν έχει ορισθεί το Api Key για τον πάροχο';return $ret;
	}
	
	//echo '<pre>ddddddddddd ';print_r($file_data); die();

	/*Array
	(
	    [success] => 1
	    [message] => OK
	    [pc_url1] => https://beta-srv.tesae.gr/
	    [pc_token_id] => eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiQ0M4OUQyRjMxOTNENDMzOTg1QTEiLCJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6IjNmMjEyNmJmLTFhNzQtNDNjZC1jNWI2LTA4ZGMzMTUwY2RmYiIsImN1bHR1cmUiOiJlbC1HUiIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6IkFwaVVzZXIiLCJ0ZW5hbnRJZCI6ImFmNmZmY2YyLTU2YWUtNDA4MC1hY2YwLTJmODc5NTc0N2NlYiIsInN1YnNjcmlwdGlvbktleSI6IjFDNzNBNzNBMUI5RDQ5RTM4RTY0MzQzREFEREQwOTI4IiwiSXRlbUZhbWlseUlkZW50aWZpZXIiOiJBdGxhcyIsIm5iZiI6MTcwODgyNDYyNCwiZXhwIjoxNzA4ODI2NDI0LCJpc3MiOiJodHRwczovL3Rlc3QtbG9naW4ucGFyb2Nob3MuZ3IvIiwiYXVkIjoiQW55b25lIn0.3jh9yB2_WDVsFF3R_0VnEIGKeSHOqgQMzzWChZMDNlY
	)*/
	$input=$file_data;
	$input['acc_'.$xxx.'_id']=$id;
	$input['id_company_paroxos']=$paroxos_params['id_company_paroxos'];
  $input['paroxos_token']=$paroxos_params['pc_key'];
  //$input['paroxos_url']=$ret_token['pc_url1'];
  $input['paroxos_live']=$paroxos_params['paroxos_mydata_live'];
	


	$input_to_raw=$input;
  $ret_send = gks_paroxos_tesae_gr_get_url('/invoice-data','POST',$input);
  
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}

	/*
Array
(
    [ok] => 0
    [code] => mydata_sync_003
    [error] => Array
        (
            [title] => Pegasus Generic Error (1)
            [message] => Η αποστολή στο myDATA απέτυχε (XML Validation)
            [data] => Array
                (
                    [validation_errors] => Array
                        (
                            [0] => Error Level:2 Code: 1835 in /workspace/pegasus/ (Line:2):Element '{http://www.aade.gr/myDATA/invoice/v1.0}exchangeRate': [facet 'minExclusive'] The value '0.00000' must be greater than '0'.
                            [1] => Error Level:2 Code: 1871 in /workspace/pegasus/ (Line:2):Element '{http://www.aade.gr/myDATA/invoice/v1.0}invoiceSummary': This element is not expected. Expected is one of ( {http://www.aade.gr/myDATA/invoice/v1.0}paymentMethods, {http://www.aade.gr/myDATA/invoice/v1.0}invoiceDetails ).
                        )

                    [request_xml] => 
094420307GR0065938168GR0
Ευτηχίας 157013Ωραιόκαστρο
tp12024-02-291.1EUR0.000002024-02-2918:41:00nii9515160.0000014.400002.000008.000006.000004.0000010.0000080.40000

                )

        )

)	
	*/
	
	$response_array=$ret_send['response_array'];
	if (isset($response_array['ok'])==false) {$ret['message']='Σφάλμα αποστολής (34239322421)';return $ret;}

	$paroxos_status=intval($response_array['ok'])==1;

  $save_dir = GKS_FileServerShare.'acc/'.$xxx.'/'.$id.'/aade_mydata/';
  if (file_exists($save_dir) == false) {
    if (@mkdir($save_dir , 0777, true) == false ) {
      $ret['message']='Δεν μπορεί να δημιουργηθεί ο φάκελος: '.substr($save_dir, strlen(GKS_FileServerShare)); debug_mail(false,$ret['message']); return $ret;
    }
  }
  $set_filename=showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
  $set_filename_s='invoice_'.$set_filename.'-paroxos-1-send';
  $set_filename_r='invoice_'.$set_filename.'-paroxos-2-response';

  require_once('vendor_inc/Nicer.php');

  unset($input_to_raw['Email']);
  unset($input_to_raw['password']);
  unset($input_to_raw['acc_'.$xxx.'_id']);
  unset($input_to_raw['id_company_paroxos']);
  unset($input_to_raw['paroxos_mydata_live']);
  unset($input_to_raw['paroxos_token']);
  unset($input_to_raw['paroxos_url']);
  unset($input_to_raw['paroxos_live']);

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
  		$sql_xxx="update gks_acc_".$xxx." set  
  	  aade_xml_send='".$db_link->escape_string($set_filename_s.'.html')."',
  	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
  		aade_statuscode='ValidationError',
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		aade_user_id=".$my_wp_user_id.",
  
  		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  		paroxos_status=".($paroxos_status ? '1' : '0').",
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_acc_".$xxx."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
  	}
  }
  	    

	//echo '<pre>ddddddddd ddd ggg ';print_r($ret_send);die();

	

	

		
		
		
	if ($paroxos_status==0) {	
		$ret['message']='Σφάλμα αποστολής (34239322422)<br>'.$ret_send['message'];
	  return $ret;}
	
	
	//echo '<pre>ddddddddd 3333 ddd ggg ';print_r($ret_send);die();



  //echo '<pre>'.$paroxos_status;die();
  //if ($has_error) return $ret; 
	
	if ($paroxos_status==1) { //pige ok
		$errorMessage=''; if (isset($response_array['errorMessage'])) $errorMessage=trim_gks($response_array['errorMessage']); 
		
		
    if ($paroxos_params['paroxos_mydata_live']) {
      
  
  		if (isset($response_array['data']['qrUrl'])==false) {
  		  if (GKS_TESAE_GR_MODE_LIVE_API=='https://e-invoicing-api-dev.pegcloud.io') {
          $response_array['data']['qrUrl']='https://e-invoicing-dev.pegcloud.io/pegasus/einv02/search_invoice01.php?auth_code='.$response_array['data']['authcode'];
        } else {
          $response_array['data']['qrUrl']='https://e-invoicing.pegcloud.io/pegasus/einv02/search_invoice01.php?auth_code='.$response_array['data']['authcode'];
        }
      }
      
      
  
  		$sql_xxx="update gks_acc_".$xxx." set  
  	  aade_xml_send='".$db_link->escape_string($set_filename_s.'.html')."',
  	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
  		aade_invoiceuid='".$db_link->escape_string($response_array['data']['uid'])."',
  		aade_invoicemark='".$db_link->escape_string($response_array['data']['mark'])."',
  		aade_qrurl='".$db_link->escape_string($response_array['data']['qrUrl'])."',
  		paroxos_authenticationCode='".$db_link->escape_string($response_array['data']['authcode'])."',
  		aade_statuscode='Success',
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		aade_send_date=now(),
  		aade_user_id=".$my_wp_user_id.",
  
  		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  		paroxos_status=".$paroxos_status.",
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_acc_".$xxx."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
  	  
  	  gks_aade_update_mark_from_id(['mark'=>$response_array['data']['mark'],'acc_inv_id'=>$id]);
  	  
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

	}
    
	
  $ret['save_but_message']='Σφάλμα κατά την αποστολή (2)';
  $ret['message']='Σφάλμα κατά την αποστολή (1)';
  $ret['success']=false;

  return $ret;	
}


function gks_paroxos_payment_sign_tesae_gr($id,$paroxos_params,$struct_data) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;	
  global $gks_cache_version;
  
	$ret = array('success' => false, 'message' => 'generic error');

  $doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
  } else {
    $xxx='';
  }
  
  //echo '<pre>';echo $id; die();
  //echo '<pre>';print_r($paroxos_params); die();

	if (isset($paroxos_params['pc_key'])==false or trim_gks($paroxos_params['pc_key'])=='') {
	  $ret['message']='Δεν έχει ορισθεί το Api Key για τον πάροχο';return $ret;
	}
	
	//echo '<pre>ddddddddddd ';print_r($struct_data); die();

	/*Array
	(
	    [success] => 1
	    [message] => OK
	    [pc_url1] => https://beta-srv.tesae.gr/
	    [pc_token_id] => eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiQ0M4OUQyRjMxOTNENDMzOTg1QTEiLCJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6IjNmMjEyNmJmLTFhNzQtNDNjZC1jNWI2LTA4ZGMzMTUwY2RmYiIsImN1bHR1cmUiOiJlbC1HUiIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6IkFwaVVzZXIiLCJ0ZW5hbnRJZCI6ImFmNmZmY2YyLTU2YWUtNDA4MC1hY2YwLTJmODc5NTc0N2NlYiIsInN1YnNjcmlwdGlvbktleSI6IjFDNzNBNzNBMUI5RDQ5RTM4RTY0MzQzREFEREQwOTI4IiwiSXRlbUZhbWlseUlkZW50aWZpZXIiOiJBdGxhcyIsIm5iZiI6MTcwODgyNDYyNCwiZXhwIjoxNzA4ODI2NDI0LCJpc3MiOiJodHRwczovL3Rlc3QtbG9naW4ucGFyb2Nob3MuZ3IvIiwiYXVkIjoiQW55b25lIn0.3jh9yB2_WDVsFF3R_0VnEIGKeSHOqgQMzzWChZMDNlY
	)*/
	$input=[];
	$input['acc_'.$xxx.'_id']=$id;
	$input['id_company_paroxos']=$paroxos_params['id_company_paroxos'];
  $input['paroxos_token']=$paroxos_params['pc_key'];
  //$input['paroxos_url']=$ret_token['pc_url1'];
  $input['paroxos_live']=$paroxos_params['paroxos_mydata_live'];
	
	$input['tidnsp']='TID_NSP_TEST'; //Ταυτότητα Μέσου Πληρωμών
	$input['amount']=$struct_data['xml']['invoiceSummary']['totalGrossValue']; //Ποσό Πληρωμής	
	//$input['mark']=; //Μοναδικός Αριθμός (ΜΑΡΚ)
	$input['issue_date']=$struct_data['xml']['invoiceHeader']['issueDate']; //Ημερομηνία Έκδοσης
	$input['issuer_vat']=$struct_data['xml']['issuer']['vatNumber']; //ΑΦΜ Εκδότη
	$input['issuer_branch']=$struct_data['row']['aade_branch']; //Αρ.Εγκατάστασης Εκδότη
	$input['invtype']=$struct_data['xml']['invoiceHeader']['invoiceType']; //Κατηγορία Παραστατικού
	$input['series']=$struct_data['xml']['invoiceHeader']['series']; //Σειρά Παρ/κού
	$input['aa']=$struct_data['xml']['invoiceHeader']['aa']; //Αρ.Παρ/κού
	$input['invoice_net_value']=$struct_data['xml']['invoiceSummary']['totalNetValue']; //Σύνολο Καθαρής Αξίας
	$input['invoice_vat_value']=$struct_data['xml']['invoiceSummary']['totalVatAmount']; //Σύνολο ΦΠΑ
	$input['invoice_total_value']=$struct_data['xml']['invoiceSummary']['totalGrossValue']; //Συνολική Αξία
	
  
  echo '<pre>ddddddddddd ';print_r($input); die();

	$input_to_raw=$input;
	// signature-create?lang=el
	// create-payment-signature
	
  $ret_send = gks_paroxos_tesae_gr_get_url('/signature-create','POST',$input);
  echo '<pre>ddddddddddd ';print_r($ret_send); die();
  
  
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}


	
	$response_array=$ret_send['response_array'];
	if (isset($response_array['ok'])==false) {$ret['message']='Σφάλμα αποστολής (34239322421)';return $ret;}

	$paroxos_status=intval($response_array['ok'])==1;

  $save_dir = GKS_FileServerShare.'acc/'.$xxx.'/'.$id.'/aade_mydata/';
  if (file_exists($save_dir) == false) {
    if (@mkdir($save_dir , 0777, true) == false ) {
      $ret['message']='Δεν μπορεί να δημιουργηθεί ο φάκελος: '.substr($save_dir, strlen(GKS_FileServerShare)); debug_mail(false,$ret['message']); return $ret;
    }
  }
  $set_filename=showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
  $set_filename_s='invoice_'.$set_filename.'-paroxos-1-send';
  $set_filename_r='invoice_'.$set_filename.'-paroxos-2-response';

  require_once('vendor_inc/Nicer.php');

  unset($input_to_raw['Email']);
  unset($input_to_raw['password']);
  unset($input_to_raw['acc_'.$xxx.'_id']);
  unset($input_to_raw['id_company_paroxos']);
  unset($input_to_raw['paroxos_mydata_live']);
  unset($input_to_raw['paroxos_token']);
  unset($input_to_raw['paroxos_url']);
  unset($input_to_raw['paroxos_live']);

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
  		$sql_xxx="update gks_acc_".$xxx." set  
  	  aade_xml_send='".$db_link->escape_string($set_filename_s.'.html')."',
  	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
  		aade_statuscode='ValidationError',
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		aade_user_id=".$my_wp_user_id.",
  
  		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  		paroxos_status=".($paroxos_status ? '1' : '0').",
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_acc_".$xxx."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
  	}
  }
  	    

	//echo '<pre>ddddddddd ddd ggg ';print_r($ret_send);die();

	

	

		
		
		
	if ($paroxos_status==0) {	
		$ret['message']='Σφάλμα αποστολής (34239322422)<br>'.$ret_send['message'];
	  return $ret;}
	
	
	//echo '<pre>ddddddddd 3333 ddd ggg ';print_r($ret_send);die();



  //echo '<pre>'.$paroxos_status;die();
  //if ($has_error) return $ret; 
	
	if ($paroxos_status==1) { //pige ok
		$errorMessage=''; if (isset($response_array['errorMessage'])) $errorMessage=trim_gks($response_array['errorMessage']); 
		
		
    if ($paroxos_params['paroxos_mydata_live']) {
      
  
  		if (isset($response_array['data']['qrUrl'])==false) {
  		  if (GKS_TESAE_GR_MODE_LIVE_API=='https://e-invoicing-api-dev.pegcloud.io') {
          $response_array['data']['qrUrl']='https://e-invoicing-dev.pegcloud.io/pegasus/einv02/search_invoice01.php?auth_code='.$response_array['data']['authcode'];
        } else {
          $response_array['data']['qrUrl']='https://e-invoicing.pegcloud.io/pegasus/einv02/search_invoice01.php?auth_code='.$response_array['data']['authcode'];
        }
      }
      
      
  
  		$sql_xxx="update gks_acc_".$xxx." set  
  	  aade_xml_send='".$db_link->escape_string($set_filename_s.'.html')."',
  	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
  		aade_invoiceuid='".$db_link->escape_string($response_array['data']['uid'])."',
  		aade_invoicemark='".$db_link->escape_string($response_array['data']['mark'])."',
  		aade_qrurl='".$db_link->escape_string($response_array['data']['qrUrl'])."',
  		paroxos_authenticationCode='".$db_link->escape_string($response_array['data']['authcode'])."',
  		aade_statuscode='Success',
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		aade_send_date=now(),
  		aade_user_id=".$my_wp_user_id.",
  
  		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  		paroxos_status=".$paroxos_status.",
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_acc_".$xxx."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
  	  
  	  gks_aade_update_mark_from_id(['mark'=>$response_array['data']['mark'],'acc_inv_id'=>$id]);
  	  
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

	}
    
	
  $ret['save_but_message']='Σφάλμα κατά την αποστολή (2)';
  $ret['message']='Σφάλμα κατά την αποστολή (1)';
  $ret['success']=false;

  return $ret;	
}