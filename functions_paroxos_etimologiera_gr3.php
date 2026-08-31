<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
function gks_paroxos_etimologiera_gr_get_url($sub_url,$request_type,$input) {
 
  //echo '<pre>'.$sub_url."\n".$request_type."\n".print_r($input,true)."\n"; die(); 
  
  $p_send=$input;
  $p_response=[];
 
  if ($input['paroxos_live']) {
    $url=GKS_ETIMOLOGIERA_GR_MODE_LIVE_API.$sub_url;
  } else {
    $url='https://einvoicing-dev-api.etimologiera.gr/v4'.$sub_url;
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
  unset($input['whi_mov_id']);
  unset($input['id_company_paroxos']);
  unset($input['paroxos_mydata_live']);
  unset($input['paroxos_token']);
  unset($input['paroxos_url']);
  
  //echo 'url: '.$url."\n";die();
  
 
  //$input=array('ggg'=>1,'hhhh'=>'gggg');
  
  //$ssss=json_encode($input);
  
  
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
    $input=json_encode($input,JSON_PRETTY_PRINT);
    //$input=json_encode($input, JSON_UNESCAPED_UNICODE);
    
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
  
  //echo '<pre>'.$input;die();
  
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
  
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/etimologiera_gr_send_'.time().'.json',$url."\n".(is_array($input) ? print_r($input,true) : $input));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/etimologiera_gr_response_'.time().'.json','gks_curl_http_code:'.$gks_curl_http_code."\n".$result);


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
    debug_mail(false,'etimologiera_gr error',                        gks_lang('Δεν βρέθηκε ο διακομιστής').'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'etimologiera_gr '.gks_lang('Δεν βρέθηκε ο διακομιστής'),'http_code'=>$gks_curl_http_code);
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    debug_mail(false,'etimologiera_gr error',                        gks_lang('Δεν βρέθηκε το σημείο').'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'etimologiera_gr '.gks_lang('Δεν βρέθηκε το σημείο'),'http_code'=>$gks_curl_http_code);
  
  } else if ($gks_curl_http_code==400) { 
    $error='etimologiera_gr '.gks_lang('Σφάλμα').' '.gks_lang('Οι παράμετροι είναι λάθος').' (400)';
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
    
    
    debug_mail(false,'etimologiera_gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error,'http_code'=>$gks_curl_http_code);
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $error='etimologiera_gr '.gks_lang('Δεν επιτρέπεται η πρόσβαση');
    if ($sub_url=='/api/send') $error.=' Unauthorized request. The jwt is either invalid or expired.';
    debug_mail(false,'etimologiera_gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error,'http_code'=>$gks_curl_http_code);
  
  } else if ($gks_curl_http_code==403) { 
    $error='etimologiera_gr '.gks_lang('Σφάλμα').' (403).';
    if ($sub_url=='/api/account/loginToSubscription') $error.=' Username, password or secret key is invalid';
    if ($sub_url=='/api/token/refresh') $error.=' Either token or refresh token is invalid';
    debug_mail(false,'etimologiera_gr error',$error.'<br>gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result.'<br>result:<br>'.$result);
    return array('success' => false, 'message' => $error,'http_code'=>$gks_curl_http_code);
  
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    debug_mail(false,'etimologiera_gr error',                        gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error'.'gks_curl_http_code: '.$gks_curl_http_code.'<br>gks_curl_errno: '.$gks_curl_errno.'<br>gks_curl_info: '.print_r($gks_curl_info, true).'<br>result:<br>'.$result);
    return array('success' => false, 'message' => 'etimologiera_gr '.gks_lang('Γενικό σφάλμα').' (2): HTTP Response Error: '.$gks_curl_http_code,'http_code'=>$gks_curl_http_code);
  
  }
  
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/etimologiera_gr_result_txt_'.time().'.txt',$result);
  
  
  //$parts=explode("\r\n\r\n",$result,2);
  //if (count($parts)!=2) {
  //  debug_mail(false,'etimologiera_gr result error',$result);
  //  return array('success' => false, 'message' => 'etimologiera_gr '.gks_lang('Σφάλμα δεδομένων').' (1).'.$result);}

  $response=trim($result); //trim($parts[1]);
  if ($response=='') {
    debug_mail(false,'etimologiera_gr response error',$response);
    return array('success' => false, 'message' => 'etimologiera_gr '.gks_lang('Σφάλμα δεδομένων').' (2).'.$result);}

	if ($response=='etimologiera_gr Generic Error (1)') {
    debug_mail(false,'etimologiera_gr response error',$response);
    return array('success' => false, 'message' => 'etimologiera_gr '.gks_lang('Σφάλμα δεδομένων').' (2.1).<br>'.$response);}
		
	
  $response_array = json_decode($response, true);
  if ($response_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'etimologiera_gr json_decode error',base64_encode($result) .'|||'.$result.'|||'.base64_encode($response) .'|||'.$response);
    return array('success' => false, 'message' => gks_lang('Σφάλμα δεδομένων').' (3).'.$result);}


  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/etimologiera_gr_send_'.time().'.json',json_encode(json_decode($input,true),JSON_PRETTY_PRINT));
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/etimologiera_gr_response_'.time().'.json',json_encode($response_array,JSON_PRETTY_PRINT));

  
  $response_array['gks_ok']=true;

  //echo '<pre>sssssssss';print_r($response_array);die();
  
  return array('success' => true, 'message' => 'OK', 'response_array' => $response_array);
}

function gks_paroxos_payment_sign_etimologiera_gr($id,$paroxos_params,$struct_data) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;	
  global $gks_cache_version;  
  
  //echo '<pre>sssssssssss';print_r($struct_data); die();
  //echo '<pre>sssssssssss';echo 1/0; die();
  //echo '<pre>sign_etimologiera_gr doc_table '.$struct_data['doc_table'];die();
  
  $doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv') {
    $xxx='inv';
    $ttt='acc_inv';
    $rrr='inv_acc'; 
    $ddd='inv_date';    
  } else if ($doc_table=='gks_acc_pay') {
    $xxx='pay';
    $ttt='acc_pay';
    $rrr='pay_acc'; 
    $ddd='pay_date';    
  } else if ($doc_table=='gks_whi_mov') {
    $xxx='mov';
    $ttt='whi_mov'; 
    $rrr='mov_whi'; 
    $ddd='mov_date';    
  } else {
    echo '<pre>error on doc_table-page'; die();
  }  
  
  $ret = array('success' => false, 'message' => 'generic error');

	if (isset($paroxos_params['pc_username'])==false or trim_gks($paroxos_params['pc_password'])=='') {
	  $ret['message']=gks_lang('Δεν έχει ορισθεί το Όνομα Χρήστη/Κωδικός Πρόσβασης για τον πάροχο');return $ret;
	}

  //echo '<pre>ddddddd ddd ';print_r($struct_data['sign']);die();
  //echo '<pre>ddddddd ddd ';print_r($struct_data);die();
  
  $input=[];
	$input[$ttt.'_id']=$id;
	$input['id_company_paroxos']=$paroxos_params['id_company_paroxos'];
  //$input['paroxos_token']=$paroxos_params['pc_key'];
  //$input['paroxos_url']=$ret_token['pc_url1'];
  $input['paroxos_live']=$paroxos_params['paroxos_mydata_live'];
  $input['paroxos_pc_username']=$paroxos_params['pc_username'];
  $input['paroxos_pc_password']=$paroxos_params['pc_password'];


  $input['externalSystemId']=$id.$xxx.rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
  $input['issuerVatNumber']=$struct_data['row']['company_afm'];
  $ddd_date=$struct_data['row'][$ddd];
  
  $input['invoiceIssueDate']=showDate(strtotime($ddd_date),'Y-m-d',1);
  $input['invoiceIssueTime']=showDate(strtotime($ddd_date),'H:i:s',1);
  $input['companyBranch']=$paroxos_params['paroxos_branch'];
  $input['invoiceType']=$struct_data['row']['eidos_parastatikou_aade_code'];
  $input['invoiceSeries']=$struct_data['row']['seira_code'];
  //$input['invoiceAA'] =$struct_data['row'][$rrr.'_number_int'];
  $input['netValue']=floatval($struct_data['sign']['netAmount']);
  $input['vatAmount']=floatval($struct_data['sign']['vatAmount']);
  $input['totalValue']=floatval($struct_data['sign']['grossAmount']);
  $input['paymentAmount']=floatval($struct_data['sign']['amount']);
  $input['terminalId']=$struct_data['sign']['terminalId'];

  //panta to eidos_parastatikou_aade_code tha einai 8.4 dioti kai otan einai 8.5 exei to parent
  if ($doc_table=='gks_acc_pay' and 
      isset($struct_data['row']['eidos_parastatikou_aade_code']) and
      in_array($struct_data['row']['eidos_parastatikou_aade_code'],['8.4','8.5']) and
      isset($struct_data['sign']['refund_val']) and 
      $struct_data['sign']['refund_val']>0) {
      
    $input['netValue']=$struct_data['sign']['refund_val'];
    $input['totalValue']=$struct_data['sign']['refund_val'];
  }

  //echo '<pre>sssssssa ';print_r($struct_data);die();
  //echo '<pre>sssssssa '.$struct_data['sign']['payment_acquirer_with_id']."\r\n";print_r($input);die();

  
  /////////////////
  
  //$input['amount']=$struct_data['sign']['amount'];
  //$input['grossAmount']=$struct_data['sign']['grossAmount'];
  
  //echo '<pre>sssssss '.$struct_data['sign']['payment_acquirer_with_id'];print_r($input);die();
  if ($struct_data['sign']['payment_acquirer_with_id']==1) {
    //$input['vatRate']=0; //24 gia na ginei meta 2400 gia tin viva
    //if ($input['netAmount']>0) {
    //  $mycal_vat_rate=round((100*$input['vatAmount'])/$input['netAmount'],0);
    //} else {
    //  $mycal_vat_rate=24;
    //}
    //$input['vatRate']=$mycal_vat_rate;
  }

  //$input['mark']=null;

  $input['nspCode']='';
  if ($struct_data['sign']['payment_acquirer_with_id']==1) $input['nspCode']='02'; //'VIVA';
  if ($struct_data['sign']['payment_acquirer_with_id']==3) $input['nspCode']='01'; //'MELLON';
  //if ($struct_data['sign']['payment_acquirer_with_id']==4) $input['nspCode']='CARDLINK';
  if ($struct_data['sign']['payment_acquirer_with_id']==5) $input['nspCode']='03'; //'EPAY';
  //if ($struct_data['sign']['payment_acquirer_with_id']==6) $input['nspCode']='WORLDLINE';
  //if ($struct_data['sign']['payment_acquirer_with_id']==7) $input['nspCode']='NEXI'; //to NEXI espidtrafei hex, tha prepei na ginei base64
  //if ($struct_data['sign']['payment_acquirer_with_id']==xxxxxxx) $input['nspCode']='03'; //'NBG SoftPOS';
  
 
	
	if ($input['nspCode']=='') {
	  $ret['message']=gks_lang('Δεν έχει ορισθεί το nspCode');return $ret;
	}
	
	
	
	$input_to_raw=$input;
	
	//echo '<pre>hhhdd ';print_r($input);die();
	
	$ret_send = gks_paroxos_payment_sign_reuse_etimologiera_gr($input);
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
	//echo '<pre>sssssssssshhhdd ';print_r($ret_send);die();

  if ($ret_send['found']==true) {
    return $ret_send;
  }
	
	//echo '<pre>not found, get new hhhdd ';print_r($input);die();
	
  $ret_send = gks_paroxos_etimologiera_gr_get_url('/createSimSign','POST',$input);
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}

  
  //echo '<pre>hhhdd ';print_r($ret_send);die();

  $response_array=$ret_send['response_array'];
  
	  
  if (isset($response_array['response']['paroxosError'])) {
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
      $value=$response_array['response']['paroxosError'];
      $tr_aa++;
      $td_code=''; if (isset($value['code'])) $td_code=trim_gks($value['code']);
      $td_message=[];
      if (isset($value['description'])) $td_message[]= $value['description'];
      if (isset($value['details'])) $td_message[]= $value['details'];

      $html_error.=
      '<tr>'.
        '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
        '<td nowrap style="text-align:center">'.htmlspecialchars($td_code).'</td>'.
        '<td  style="text-align:left">'.implode('<br>',$td_message).'</td>'.
      '</tr>';

    
    
    $html_error.='</tbody></table>';
    
    debug_mail(false,'get sing error',print_r($ret_send,true));
    $ret['success']=false;
    $ret['message']=$html_error;
    return $ret;
    
  }  

  
  
  //echo '<pre>aaaaaaaaahhhdd ';print_r($ret_send);die();
  
	if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}

  if (isset($ret_send['response_array']['response']) and
      is_array($ret_send['response_array']['response']) and
      isset($ret_send['response_array']['response']['bSignature'])) {
    
    $signature=$ret_send['response_array']['response'];
    
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

    r_series,
    r_uidHash,

    r_signedAt,
    r_invoiceTypeCode,
    r_nspProtocol,
    response
    
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    21,
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
    
    '021',
    ".floatval($input['paymentAmount']).",
    ".((time()+1*24*60*60)*1000).",
    ".floatval($input['netValue']).",
    '".$db_link->escape_string($signature['bSignature'])."',
    ".'0'.",
    ".floatval($input['totalValue']).",
    '".$db_link->escape_string($input['terminalId'])."',
    '".$db_link->escape_string($signature['signatureMessage'])."',
    ".floatval($input['vatAmount']).",
    '".$db_link->escape_string($input['issuerVatNumber'])."',
    '".$db_link->escape_string($signature['tempUid'])."',
    ".intval($input['companyBranch']).",
    '".$db_link->escape_string($input['invoiceSeries'])."',
    '".$db_link->escape_string($signature['hSignature'])."',
    ".(time()*1000).",
    '".$db_link->escape_string($input['invoiceType'])."',
    '".$db_link->escape_string($input['nspCode'])."',   
    '".$db_link->escape_string(serialize($ret_send['response_array']))."'
    )";
    
    
    //r_serial,    
    //'".$db_link->escape_string($signature['serial'])."',
    //r_signaturePublicKey,    
    //'".$db_link->escape_string($signature['signaturePublicKey'])."',
    
	  $result = $db_link->query($sql); 
	  if (!$result) {
	    debug_mail(false,'error sql',$sql);
	    return array('success' => false, 'message' => 'sql error');}   
	  
	  $id_paroxos_signature = $db_link->insert_id;      
    //echo '<pre>hhhdd ';print_r($ret_send);die();
    $ret['id_paroxos_signature']=$id_paroxos_signature;
    $ret['id_aade_paroxos']=21;
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

function gks_paroxos_payment_sign_reuse_etimologiera_gr($input) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;	
  global $gks_cache_version; 
  
  $ret = array('success' => false, 'message' => 'generic error', 'found'=>false);
  
  if (1==2) {
    $ret['success']=true;
    $ret['message']='not found';
    $ret['found']=false;
    return $ret;
  }
    
  
  $sql="update gks_paroxos_signature
  set signature_status='canreuse',mydate_edit=now()
  where aade_paroxos_id=21
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
  where aade_paroxos_id=21
  and signature_status in ('canreuse')
  and r_signatureExpirationDate >= ".((time()+5*60)*1000)."
  and r_amount=".$input['paymentAmount']."
  and r_netAmount=".$input['netValue']."
  and r_vatAmount=".$input['vatAmount']."
  and r_grossAmount=".$input['totalValue']."
  and r_sellerVat='".$db_link->escape_string($input['issuerVatNumber'])."'
  and r_series='".$db_link->escape_string($input['invoiceSeries'])."'
  and r_invoiceTypeCode='".$db_link->escape_string($input['invoiceType'])."'
  and r_sellerBranch=".$input['companyBranch']."
  and r_terminalId='".$db_link->escape_string($input['terminalId'])."'
  and r_nspProtocol='".$db_link->escape_string($input['nspCode'])."'
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
  $mysign['tempUid']=$row['r_uid'];
  $mysign['bSignature']=$row['r_signature'];
  $mysign['hSignature']=$row['r_uidHash'];
  $mysign['signatureMessage']=$row['r_signedContent'];
  $mysign['nspCode']=$row['r_nspProtocol'];
  
  /* $mysign['signingAuthor']=$row['r_signingAuthor'];
  $mysign['paymentAmount']=floatval($row['r_amount']);
  $mysign['signatureExpirationDate']=intval($row['r_signatureExpirationDate']);
  $mysign['netValue']=floatval($row['r_netAmount']);
  $mysign['signature']=$row['r_signature'];
  //$mysign['vatRate']=floatval($row['r_vatRate']);
  $mysign['totalValue']=floatval($row['r_grossAmount']);
  $mysign['terminalId']=$row['r_terminalId'];
  $mysign['signedContent']=$row['r_signedContent'];
  $mysign['vatAmount']=floatval($row['r_vatAmount']);
  $mysign['issuerVatNumber']=$row['r_sellerVat'];
  $mysign['uid']=$row['r_uid'];
  $mysign['companyBranch']=intval($row['r_sellerBranch']);
  //$mysign['serial']=$row['r_serial'];
  $mysign['invoiceSeries']=$row['r_series'];
  $mysign['uidHash']=$row['r_uidHash'];
  $mysign['signaturePublicKey']=$row['r_signaturePublicKey'];
  $mysign['signedAt']=$row['r_signedAt'];
  $mysign['invoiceTypeCode']=$row['r_invoiceTypeCode']; */
  
  
  $ret['response']=array();
  $ret['response']['response']=$mysign;
  $ret['response']['errors']='';
  $ret['response']['gks_ok']=true;

  $ret['id_paroxos_signature']=intval($row['id_paroxos_signature']);
  $ret['id_aade_paroxos']=intval($row['aade_paroxos_id']);
  $ret['success']=true;
  $ret['message']='OK';
  $ret['found']=true;
  
  //echo '<pre>dddddddd111 ';print_r($ret);die();  
  
  
  //prepei na epistrefei oti kai i gks_paroxos_payment_sign_etimologiera_gr
  return $ret;
    
}

function gks_paroxos_get_signature_data_etimologiera_gr($signature_data,$payment_acquirer_with_id) {
  
  $ret = array('success' => false, 'message' => 'generic error get_signature_data_ilyda_com');
  //echo '<pre>dddddddddddd ';print_r($signature_data);die();
 

  $aadeSignatureTimestamp='';
  if (isset($signature_data['signature']['signedContent'])) {
    $parts=explode(';',$signature_data['signature']['signedContent']);
    if (count($parts)>=3) $aadeSignatureTimestamp=$parts[2];
  }
  
  if (isset($signature_data['signature']['bSignature'])==false or 
      isset($signature_data['signature']['signatureMessage'])==false or 
      $signature_data['signature']['bSignature']=='' or 
      $signature_data['signature']['signatureMessage']=='') {
    
    $ret['message'] = 'data not found error get_signature_data_etimologiera_gr';
    return $ret;      
  }
  
  
  //echo '<pre>dddddddddddd2';print_r($signature_data);die();
  //https://developer.viva.com/apis-for-point-of-sale/card-terminal-apps/android-app/sale/multimerchant/
  
  $aadeProviderId='21';
  if ($payment_acquirer_with_id==1) $aadeProviderId='120'; //VIVA
  if ($payment_acquirer_with_id==3) $aadeProviderId='21'; //MELLON
  if ($payment_acquirer_with_id==4) $aadeProviderId='21'; //CARDLINK
  if ($payment_acquirer_with_id==6) $aadeProviderId='21'; //WORLDLINE

  $nspProtocol='';
  if ($payment_acquirer_with_id==1) $nspProtocol='VIVA';
  if ($payment_acquirer_with_id==3) $nspProtocol='MELLON'; 
  if ($payment_acquirer_with_id==4) $nspProtocol='CARDLINK';
  if ($payment_acquirer_with_id==6) $nspProtocol='WORLDLINE'; 

  
  $ret['data']= array(
    'aadeProviderId'=> $aadeProviderId, //etimologiera_gr
    'aadeProviderSignatureData' => $signature_data['signature']['signatureMessage'],
    'aadeProviderSignature' => $signature_data['signature']['bSignature'],
    'aadeSignatureTimestamp' => (time()*1000),
    'aadeSignatureUID'=> $signature_data['signature']['tempUid'],
    'nspProtocol' => $nspProtocol,
  );
  
  //echo '<pre>dddddddddddd2';print_r($ret);die();
  
  $ret['success']=true;
  $ret['message']='OK';
  
  return $ret;
}



function gks_paroxos_invoice_get_status_etimologiera_gr($doc_table,$id,$row_item) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $ret = array('success' => false, 'message' => 'generic error');
  
  //echo '<pre>sssssssssssssssss';print_r($row_item);die();
  $sql="SELECT id_company_paroxos,paroxos_mydata_live, paroxos_branch, pc_username, pc_password
  FROM gks_company_paroxos
  WHERE aade_paroxos_id=21 and pc_username<>'' and pc_password<>''";
  if ($row_item['company_sub_id']>0) {
    $sql.=" and company_id=0 and company_sub_id=".$row_item['company_sub_id'];
  } else {
    $sql.=" and company_id=".$row_item['company_id']." and company_sub_id=0";
  }

  //echo '<pre>sssssssssssssssss'.$sql;die();
  $result = $db_link->query($sql);
  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  if ($result->num_rows==0) {
    $ret['message']=gks_lang('Δεν βρέθηκαν τα στοιχεία σύνδεσης για αυτή την εταιρεία');
    $ret['success']=false;  
    return $ret;}
  $row= $result->fetch_assoc();
    
  $input=[];
  $input['paroxos_live']=$row['paroxos_mydata_live'];
  $input['paroxos_pc_username']=$row['pc_username'];
  $input['paroxos_pc_password']=$row['pc_password'];
  $input['acc_inv_id']=0; if ($doc_table=='gks_acc_inv') $input['acc_inv_id']=$id;
  $input['acc_pay_id']=0; if ($doc_table=='gks_acc_pay') $input['acc_pay_id']=$id;
  $input['whi_mov_id']=0; if ($doc_table=='gks_whi_mov') $input['whi_mov_id']=$id;
  $input['id_company_paroxos']=$row['id_company_paroxos'];
  $input['id_company_paroxos']=$row['id_company_paroxos'];

  
  //echo '<pre>ssssssssssss';print_r($input);die();
  //echo '<pre>'.$sub_url;print_r($row_item);die();
  $html='';
  if ($row_item['is_b2g']!=0) {
    $html.='<div style="font-weight:bold;">'.gks_lang('Κατάσταση ως B2G').'</div>';
    $input['mark']=$row_item['aade_invoicemark'];
    $ret_send=gks_paroxos_etimologiera_gr_get_url('/getB2GInvoiceInfo','POST',$input);
    if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
    

    $response_array=$ret_send['response_array'];
    //echo '<pre>ssssssssssss';print_r($ret_send);die();
    
    //delete me
    /* $response_array=json_decode('{"response": {
"uid": "400001234567890ABCDEF0123456789012345678",
"mark": "400001234567890",
"authCode": "A1B2C3D4E5F600112233445566778899AABBCCDD",
"createdAt": "2026‐01‐02T12:09:34Z",
"updatedAt": "2026‐01‐03T08:00:00Z",
"kedState": 9,
"kedStateDescription": "Αίτημα Διόρθωσης",
"softReject": {
"buyerReferenceBT10": {
"requirement": "Να μπει Αναθέτουσα 1007.901.0002.",
"value": "1007.901.0002"
},
"buyerReferenceBT110": {
"requirement": "Να μπει Αναθέτουσα 1007.901.0002.",
"value": "1007.901.0002"
}
}}}',true); */


    if (isset($response_array['response']['paroxosError'])) {
      $html.='<div style="color:red;">'.gks_lang('Σφάλμα').'</div>';
      $html.='<table class="table table-sm table-responsive1 table-striped table-bordered" style="width: 100%;font-size:0.8rem;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
      '<tr>'.
      '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
      '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κωδικός').'</th>'.
      '<th class="table-dark" scope="col" style="text-align: left !important;" width="100%">'.gks_lang('Περιγραφή').'</th>'.
      '</tr>'.
      '</thead>'.
      '<tbody>';
      $tr_aa=0;
        $value=$response_array['response']['paroxosError'];
        $tr_aa++;
        $td_code=''; if (isset($value['code'])) $td_code=trim_gks($value['code']);
        $td_message=[];
        if (isset($value['description'])) $td_message[]= $value['description'];
        if (isset($value['details'])) $td_message[]= $value['details'];
        $html.=
        '<tr>'.
          '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
          '<td nowrap style="text-align:center">'.htmlspecialchars($td_code).'</td>'.
          '<td  style="text-align:left">'.implode('<br>',$td_message).'</td>'.
        '</tr>';
      $html.='</tbody></table>';      
      //$ret['message']=$html;
      //return $ret;
    } else if (isset($response_array['response']['mark'])) {
      $html.='<div style="color:green;">'.gks_lang('Επιτυχής').'</div>';
      $html.='<table class="table table-sm table-responsive1 table-striped table-bordered" style="width: 100%;font-size:0.8rem;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
      '<tr>'.
      '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
      '<th class="table-dark" scope="col" style="text-align: left !important;" width="0%">'.gks_lang('Πεδίο').'</th>'.
      '<th class="table-dark" scope="col" style="text-align: left !important;" width="100%">'.gks_lang('Τιμή').'</th>'.
      '</tr>'.
      '</thead>'.
      '<tbody>';
      $values=''; $tr_aa=0;
      foreach ($response_array['response'] as $index => $value) {
        if (in_array($index,['uid1','authCode1','createdAt1','updatedAt1'])==false) {
          $tr_aa++;
          if ($index=='softReject') {
            $value_text='';
            if (is_array($value)) {
              //$value_text='<pre>'.print_r($value,true).'</pre>';
              $value_text=json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
              $value_text=str_replace('        "','  "',$value_text);
              $value_text=str_replace('    "',' "',$value_text);
              $value_text=str_replace('": {','":{',$value_text);
              $value_text=str_replace('    }',' }',$value_text);
              $value_text='<pre style="margin:0px;">'.$value_text.'</pre>';
            } else if (is_string($value)) {
              $value_text=$value;
            } else if (is_null($value)) {
              $value_text=gks_lang('κενό');
            } else {
              $value_text='--';
            }
          } else {
            $value_text=$value;
          }
          $html.=
          '<tr>'.
            '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
            '<td nowrap style="text-align:left">'.$index.'</td>'.
            '<td style="text-align:left">'.$value_text.'</td>'.
          '</tr>';
        }
      }
      $html.='</tbody></table>'; 
      //$ret['success']=true;
      //$ret['message']=$html;
      //return $ret;
      
      //echo '<pre>ddddddddddd'."\r\n";print_r($values);die();
    } else {
      $html.='<div style="color:red;">'.gks_lang('Σφάλμα').'</div>';
      $html.='<div>Response</div><pre>'.print_r($response_array,true).'</pre>';
      //$ret['message']='error response<pre>'.print_r($response_array,true).'</pre>';
      //return $ret;
    }
  }
  
  
  //normal invoice
  unset($input['mark']);
  $input['uid']=$row_item['aade_invoiceuid'];
  
  $html.='<div style="font-weight:bold;margin-top:20px;">'.gks_lang('Κατάσταση ως παραστατικό').'</div>';
  $input['mark']=$row_item['aade_invoicemark'];
  $ret_send=gks_paroxos_etimologiera_gr_get_url('/invoiceRequestInfo','POST',$input);
  if ($ret_send['success']==false) {$ret['message']=$ret_send['message'];return $ret;}
  

  $response_array=$ret_send['response_array'];
  
  
  if (isset($response_array['response']['paroxosError'])) {
    $html.='<div style="color:red;">'.gks_lang('Σφάλμα').'</div>';
    $html.='<table class="table table-sm table-responsive1 table-striped table-bordered" style="width: 100%;font-size:0.8rem;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">'.
    '<thead>'.
    '<tr>'.
    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κωδικός').'</th>'.
    '<th class="table-dark" scope="col" style="text-align: left !important;" width="100%">'.gks_lang('Περιγραφή').'</th>'.
    '</tr>'.
    '</thead>'.
    '<tbody>';
    $tr_aa=0;
      $value=$response_array['response']['paroxosError'];
      $tr_aa++;
      $td_code=''; if (isset($value['code'])) $td_code=trim_gks($value['code']);
      $td_message=[];
      if (isset($value['description'])) $td_message[]= $value['description'];
      if (isset($value['details'])) $td_message[]= $value['details'];
      $html.=
      '<tr>'.
        '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
        '<td nowrap style="text-align:center">'.htmlspecialchars($td_code).'</td>'.
        '<td  style="text-align:left">'.implode('<br>',$td_message).'</td>'.
      '</tr>';
    $html.='</tbody></table>';      
    //$ret['message']=$html;
    //return $ret;
  } else if (isset($response_array['response']['records'][0]['invoiceMark'])) {
    $html.='<div style="color:green;">'.gks_lang('Επιτυχής').'</div>';
    $html.='<table class="table table-sm table-responsive1 table-striped table-bordered" style="width: 100%;font-size:0.8rem;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">'.
    '<thead>'.
    '<tr>'.
    '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
    '<th class="table-dark" scope="col" style="text-align: left !important;" width="0%">'.gks_lang('Πεδίο').'</th>'.
    '<th class="table-dark" scope="col" style="text-align: left !important;" width="100%">'.gks_lang('Τιμή').'</th>'.
    '</tr>'.
    '</thead>'.
    '<tbody>';
    $values=''; $tr_aa=0;
    foreach ($response_array['response']['records'][0] as $index => $value) {
      if (in_array($index,['uid','authCode','createdAt','updatedAt'])==false) {
        $tr_aa++;
        $html.=
        '<tr>'.
          '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
          '<td nowrap style="text-align:left">'.$index.'</td>'.
          '<td style="text-align:left">'.$value.'</td>'.
        '</tr>';
      }
    }
    if (isset($response_array['response']['status'])) {
      $code='';if (isset($response_array['response']['status']['code'])) $code= $response_array['response']['status']['code'];
      $description='';if (isset($response_array['response']['status']['description'])) $description= $response_array['response']['status']['description'];
      $tr_aa++;
      $html.=
      '<tr>'.
        '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
        '<td nowrap style="text-align:left">Status</td>'.
        '<td style="text-align:left">'.$description.' ('.$code.')</td>'.
      '</tr>';      
    }
    $html.='</tbody></table>'; 
    //$ret['success']=true;
    //$ret['message']=$html;
    //return $ret;
    
    //echo '<pre>ddddddddddd'."\r\n";print_r($values);die();
  } else {
    $html.='<div style="color:red;">'.gks_lang('Σφάλμα').'</div>';
    $html.='<div>Response</div><pre>'.print_r($response_array,true).'</pre>';
    //$ret['message']='error response<pre>'.print_r($response_array,true).'</pre>';
    //return $ret;
  }  
  
  $ret['success']=true;
  $ret['message']=$html;
  return $ret;  
  

}
