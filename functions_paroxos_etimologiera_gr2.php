<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_paroxos_invoice_xml_send_etimologiera_gr($id,$paroxos_params,$struct_data,$file_data) {
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
  //echo '<pre>';print_r($paroxos_params);die();
  
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
	

  $url_sendInvoice='/sendInvoice';
  if (isset($input['invoice'][0]['paymentMethods']['paymentMethodDetails'])) {
    foreach ($input['invoice'][0]['paymentMethods']['paymentMethodDetails'] as $ggg) {
      if (isset($ggg['providersSignature'])) {
        $url_sendInvoice='/sendSimInvoice';
        break;
      }
    }
  }
  //print '<pre>ssssssssss'.$url_sendInvoice;print_r($input);die();
  
	$input_to_raw=$input;
  $ret_send = gks_paroxos_etimologiera_gr_get_url($url_sendInvoice,'POST',$input);
  
  
  /* if ($ret_send['success']==false and 
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
      where paroxos_id=21
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
        $token = gks_paroxos_etimologiera_gr_sign_tf1_token($header, $payload, $secret);
    
        //echo '<pre>'.$token.'</pre>';
        if ($token<>'') {
          $paroxos_tf1_url=$linkBaseUrl.'/'.$algorithm.'/'.$token;
      		$sql_xxx="update ".$doc_table." set
      	  paroxos_tf1_url='".$db_link->escape_string($paroxos_tf1_url)."',
      	  paroxos_tf1_url_has=21
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
  } */
  
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
  //echo '<pre>bbbbbbbb';print_r($response_array);die();
  
	if (isset($response_array['response']) and 
      isset($response_array['response']['responses']) and
      count($response_array['response']['responses'])==1 and
	    isset($response_array['response']['responses'][0]['invoiceMark']) and
	   strlen($response_array['response']['responses'][0]['invoiceMark'])>=8 and
      isset($response_array['response']['paroxosError'])==false and 
      isset($response_array['response']['errors'])==false) {
	  $paroxos_status=true;      
  }
  
  
	/* if (isset($response_array['invoiceMarking']) and 
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
  } */
    
    
	/* if (isset($response_array['invoiceMarking']) and
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
  } */
  
  //echo '<pre>ggggggggg|'.$paroxos_status.'|'.$transmission_failure.'|';die();
  
  //gia to MQ001 den xreiazetai na kano kati, einai lathos kai apla to emfanizei
  
  if (isset($response_array['response']['paroxosError']) or 
     (isset($response_array['response']['errors']) and is_array($response_array['response']['errors']) and count($response_array['response']['errors'])>0)) {
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
    
    if (isset($response_array['response']['paroxosError'])) {
      $tr_aa++;
      $td_code=$response_array['response']['paroxosError']['code'];
      $td_message=[];
      if (isset($response_array['response']['paroxosError']['description'])) {
        $td_message[]=$response_array['response']['paroxosError']['description'];
      }
      if (isset($response_array['response']['paroxosError']['details'])) {
        $td_message[]=$response_array['response']['paroxosError']['details'];
      }
      
      $html_error.=
      '<tr>'.
        '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
        '<td nowrap style="text-align:center">'.htmlspecialchars($td_code).'</td>'.
        '<td  style="text-align:left">'.implode('<br>',$td_message).'</td>'.
      '</tr>';
    }
    if (isset($response_array['response']['errors'])) {
      foreach ($response_array['response']['errors'] as $value) {
        $tr_aa++;
        $td_code=''; if (isset($value['code'])) $td_code=trim_gks($value['code']);
        $td_message=array(); 
        if (isset($value['message']) and $value['message']<>'') $td_message[]=htmlspecialchars(trim_gks($value['message']));
        
        $html_error.=
        '<tr>'.
          '<th scope="row" nowrap style="text-align:center">'.$tr_aa.'</th>'.
          '<td nowrap style="text-align:center">'.htmlspecialchars($td_code).'</td>'.
          '<td  style="text-align:left">'.implode('<br>',$td_message).'</td>'.
        '</tr>';
      } 
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
  $set_filename_g='invoice_'.$set_filename.'-paroxos-3-apiget';

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
	
	
	

	
	if ($paroxos_status==false) {
		
		

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

	//print '<pre>sssss'.$paroxos_status;print_r($paroxos_params);die();
	

	

		
		

	if ($paroxos_status==false) {	
		$ret['message']=gks_lang('Σφάλμα αποστολής').' (34239322422aa23)<br>'.$ret_send['message'];
	  return $ret;}
	
	
	//echo '<pre>ddddddddd 3333 ddd ggg ';print_r($ret_send);die();



  //echo '<pre>'.$paroxos_status;die();
  //if ($has_error) return $ret; 
	
	if ($paroxos_status) { //pige ok
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
      //if (isset($response_array['invoiceMarking']['myDataQrCode']) and $response_array['invoiceMarking']['myDataQrCode']!='') {
      //  $aade_qrurl=$response_array['invoiceMarking']['myDataQrCode'];
      //  $aade_qrurl=str_replace('https://mydatapi.aade.gr/TimologioQR/QRInfo','https://mydatapi.aade.gr/myDATA/TimologioQR/QRInfo',$aade_qrurl);
      //}
      if (isset($response_array['response']['responses'][0]['invoiceUrl'])) {
        $aade_paroxos_qrurl=$response_array['response']['responses'][0]['invoiceUrl'];
      }
      
      
      if ($aade_paroxos_qrurl!='') {
        $api_paroxos_qrurl=str_replace('etimologiera.gr/invoice/','etimologiera.gr/api/invoice/',$aade_paroxos_qrurl);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_paroxos_qrurl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true ); 
        $headers=[];
        $headers[]='Content-Type: text/html; charset=UTF-8';
        $headers[]='Accept: application/json';        
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers); 
        $result_api=curl_exec($ch);
        $gks_curl_errno=curl_errno($ch);
        $gks_curl_info =curl_getinfo($ch);        
        curl_close ($ch);
        
        $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
        if ($gks_curl_http_code==200 and $result_api!='') {
          $result_api_json=json_decode($result_api,true);
          
          if (isset($result_api_json['aadeUrl'])) {
            $aade_qrurl=$result_api_json['aadeUrl'];
          }
          

          $raw_file='<!DOCTYPE html><html dir="ltr" lang="en-US"><head>
              <link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/vendor_inc/nice_r.css?v='.$gks_cache_version.'"/>
              <script type="text/javascript" src="'.GKS_SITE_URL.'my/vendor_inc/nice_r.js?v='.$gks_cache_version.'"></script>
            </head><body>';
                  $obj_nicer = new Nicer($result_api_json, true, true);
                  $raw_file.=$obj_nicer->render(false);
                  $raw_file.='<div id="raw_print_r_b" onclick="raw_toggle()">RAW json</div>';
                  $raw_file.='<div style="display:none;" id="raw_print_r"><pre>';
                  //$raw_file.=json_encode($result_api_json,JSON_PRETTY_PRINT);
                  $raw_file.=$result_api;
                  $raw_file.='</pre></div>';
          $raw_file.='</body>
          </html>'; 
          file_put_contents($save_dir.$set_filename_g.'.html', $raw_file);
  
          
        }
      }
      
      
  		$sql_xxx="update ".$doc_table." set
  		aade_paroxos_id=".$paroxos_params['aade_paroxos_id'].",  
  	  aade_xml_send='".$db_link->escape_string($set_filename_s.'.html')."',
  	  aade_xml_response='".$db_link->escape_string($set_filename_r.'.html')."',
  		aade_invoiceuid='".$db_link->escape_string($response_array['response']['responses'][0]['invoiceUid'])."',
  		aade_invoicemark='".$db_link->escape_string($response_array['response']['responses'][0]['invoiceMark'])."',
  		aade_qrurl='".$db_link->escape_string($aade_qrurl)."',
  		aade_paroxos_qrurl='".$db_link->escape_string($aade_paroxos_qrurl)."',
  		paroxos_authenticationCode='".$db_link->escape_string($response_array['response']['responses'][0]['authenticationCode'])."',
  		paroxos_processId='',
  		aade_statuscode='Success',
  		aade_errors='".$db_link->escape_string($errorMessage)."',
  		aade_send_date=now(),
  		aade_user_id=".$my_wp_user_id.",
      paroxos_invoice_number='".$db_link->escape_string($invoiceNumber_BT_1)."',
  		paroxos_last_response='".$db_link->escape_string(serialize($response_array))."',
  		paroxos_status=".($paroxos_status ? '1' : '0').",
  	  paroxos_user_send=".$my_wp_user_id.",
  	  paroxos_date_send=now()  		
  		where id_".$ttt."=".$id;
  	  $result_xxx = $db_link->query($sql_xxx); 
  	  if (!$result_xxx) {
  	    debug_mail(false,'error sql',$sql_xxx);
  	    return array('success' => false, 'message' => 'sql error');}
  	  
      gks_aade_update_mark_from_id(['mark'=>$response_array['response']['responses'][0]['invoiceMark'],$ttt.'_id'=>$id]);

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

