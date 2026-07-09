<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function getAADEstatuscodeDescr($mystate) {
  switch ($mystate) {
    case 'Success': return gks_lang('Επιτυχία','part4','getAADEstatuscodeDescr'); break; 
    case 'ValidationError': return gks_lang('Αποτυχία επιχειρησιακών ελέγχων','part4','getAADEstatuscodeDescr'); break; 
    case 'TechnicalError': return gks_lang('Τεχνικό σφάλμα','part4','getAADEstatuscodeDescr'); break; 
    case 'XMLSyntaxError': return gks_lang('Σφάλμα επικύρωσης σύνταξης XML','part4','getAADEstatuscodeDescr'); break; 

    default: return $mystate; break; 
  } 
}







class gks_aade_SimpleXMLExtended extends SimpleXMLElement {
  public function addCData($cdata_text) {
    $node = dom_import_simplexml($this); 
    $no   = $node->ownerDocument; 
    $node->appendChild($no->createCDATASection($cdata_text)); 
  }
}

function gks_aade_request_transmitted_docs($id_company,$id_company_sub,$mark,$maxMark,$dateFrom,$dateTo,$entityVatNumber,$receiverVatNumber,$invType) {
  global $db_link;
  $ret = array('success' => false, 'message' => 'generic error');

  if ($id_company_sub==0) { //kentriko
    $sql="select * from gks_company where company_disable=0 and id_company=".$id_company;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε η εταιρεία με ID').' <b>'.$id_company.'</b>';
      debug_mail(false,$ret['message'],$sql); return $ret;}
    $row = $result->fetch_assoc();
    $aade_mydata_user_id=trim_gks($row['aade_mydata_user_id']);
    $aade_mydata_subscription_key=trim_gks($row['aade_mydata_subscription_key']);
    $aade_mydata_live=intval($row['aade_mydata_live']);
    if ($aade_mydata_user_id=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']); 
      debug_mail(false,$ret['message'],$sql); return $ret;}
    if ($aade_mydata_subscription_key=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
  

  } else {
    $sql="select gks_company_subs.*,company_title 
    from gks_company_subs 
    LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
    where company_sub_disable=0 and id_company_sub=".$id_company_sub;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε το υποκατάστημα με ID').' <b>'.$id_company_sub.'</b>';
      debug_mail(false,$ret['message'],$sql); return $ret;}
    $row = $result->fetch_assoc();
    $aade_mydata_user_id=trim_gks($row['aade_mydata_user_id_sub']);
    $aade_mydata_subscription_key=trim_gks($row['aade_mydata_subscription_key_sub']);
    $aade_mydata_live=intval($row['aade_mydata_live_sub']);
    
    if ($aade_mydata_user_id=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
      $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
    if ($aade_mydata_subscription_key=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
      $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
  }
  
  $aade_url=($aade_mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).'RequestTransmittedDocs?mark='.$mark;
  if ($maxMark>0) $aade_url.='&maxMark='.$maxMark;
  if ($dateFrom!='') $aade_url.='&dateFrom='.$dateFrom;
  if ($dateTo!='') $aade_url.='&dateTo='.$dateTo;
  if ($entityVatNumber!='') $aade_url.='&entityVatNumber='.$entityVatNumber;
  if ($receiverVatNumber!='') $aade_url.='&receiverVatNumber='.$receiverVatNumber;
  if ($invType!='') $aade_url.='&invType='.$invType;
  
  //echo $aade_url;die();
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$aade_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,array(
    'Content-Type: text/xml',
    'aade-user-id: '.$aade_mydata_user_id,
    'Ocp-Apim-Subscription-Key: '.$aade_mydata_subscription_key,
  )); 
  
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  curl_close ($ch);
  //var_dump($result);var_dump($gks_curl_errno);var_dump($gks_curl_info);die();
	//echo '<pre>';var_dump($result);die();
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  if ($gks_curl_http_code==0) { //HTTP Host not found
    $ret['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');debug_mail(false,$ret['message'],$sql); return $ret;
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    $ret['message']=gks_lang('Δεν βρέθηκε η υπηρεσία RequestTransmittedDocs της ΑΑΔΕ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
    $ret['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει ο κωδικός ΜΑΡΚ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $ret['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    $ret['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;debug_mail(false,$ret['message'],$result); return $ret;
  } 
  

  
  $ret['out_xml']=$result;
  $ret['message']='OK';
  $ret['success']=true;
  
  return $ret;
}

function gks_aade_request_docs($id_company,$id_company_sub,$mark,$maxMark,$dateFrom,$dateTo,$entityVatNumber,$receiverVatNumber,$invType) {
  global $db_link;
  $ret = array('success' => false, 'message' => 'generic error');
  //die('ssssssssssss');
  if ($id_company_sub==0) { //kentriko
    $sql="select * from gks_company where company_disable=0 and id_company=".$id_company;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε η εταιρεία με ID').' <b>'.$id_company.'</b>';
      debug_mail(false,$ret['message'],$sql); return $ret;}
    $row = $result->fetch_assoc();
    $aade_mydata_user_id=trim_gks($row['aade_mydata_user_id']);
    $aade_mydata_subscription_key=trim_gks($row['aade_mydata_subscription_key']);
    $aade_mydata_live=intval($row['aade_mydata_live']);
    if ($aade_mydata_user_id=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
    if ($aade_mydata_subscription_key=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
  

  } else {
    $sql="select gks_company_subs.*,company_title 
    from gks_company_subs 
    LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
    where company_sub_disable=0 and id_company_sub=".$id_company_sub;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε το υποκατάστημα με ID').' <b>'.$id_company_sub.'</b>';
      debug_mail(false,$ret['message'],$sql); return $ret;}
    $row = $result->fetch_assoc();
    $aade_mydata_user_id=trim_gks($row['aade_mydata_user_id_sub']);
    $aade_mydata_subscription_key=trim_gks($row['aade_mydata_subscription_key_sub']);
    $aade_mydata_live=intval($row['aade_mydata_live_sub']);
    
    if ($aade_mydata_user_id=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
      $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
    if ($aade_mydata_subscription_key=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
      $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
  }
  
    
  $aade_url=($aade_mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).'RequestDocs?mark='.$mark;
  if ($maxMark>0) $aade_url.='&maxMark='.$maxMark;
  if ($dateFrom!='') $aade_url.='&dateFrom='.$dateFrom;
  if ($dateTo!='') $aade_url.='&dateTo='.$dateTo;
  if ($entityVatNumber!='') $aade_url.='&entityVatNumber='.$entityVatNumber;
  if ($receiverVatNumber!='') $aade_url.='&receiverVatNumber='.$receiverVatNumber;
  if ($invType!='') $aade_url.='&invType='.$invType;
  
  //echo '<pre>'.$aade_url;die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$aade_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,array(
    'Content-Type: text/xml',
    'aade-user-id: '.$aade_mydata_user_id,
    'Ocp-Apim-Subscription-Key: '.$aade_mydata_subscription_key,
  )); 
  
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  curl_close ($ch);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  if ($gks_curl_http_code==0) { //HTTP Host not found
    $ret['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');debug_mail(false,$ret['message'],$sql); return $ret;
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    $ret['message']=gks_lang('Δεν βρέθηκε η υπηρεσία RequestTransmittedDocs της ΑΑΔΕ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
    $ret['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει ο κωδικός ΜΑΡΚ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $ret['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    $ret['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;debug_mail(false,$ret['message'],$result); return $ret;
  } 
  
  $ret['out_xml']=$result;
  $ret['message']='OK';
  $ret['success']=true;
  return $ret;
}


function gks_aade_invoice_xml_parse_response($xml_string) {
  $xml_response=array('index'=>0, 'invoiceUid'=>'', 'invoiceMark'=>'', 'qrurl'=> '', 'statusCode' => '','errors' => array(),'errors_human'=>'');
  
  //$xml_string='gggggg';
  //libxml_use_internal_errors(true);
  try {
    
    $xml = new SimpleXMLElement($xml_string, LIBXML_NOERROR);
    
    $nodes = $xml->xpath('/ResponseDoc/response/index');
    if (count($nodes)==1) $xml_response['index'] = intval((string)$nodes[0]);
    
    $nodes = $xml->xpath('/ResponseDoc/response/invoiceUid');
    if (count($nodes)==1) $xml_response['invoiceUid'] = ((string)$nodes[0]);
    
    $nodes = $xml->xpath('/ResponseDoc/response/invoiceMark');
    if (count($nodes)==1) $xml_response['invoiceMark'] = ((string)$nodes[0]);
    
    $nodes = $xml->xpath('/ResponseDoc/response/qrUrl');
    if (count($nodes)==1) $xml_response['qrurl'] = ((string)$nodes[0]);
    
    
    $nodes = $xml->xpath('/ResponseDoc/response/statusCode');
    if (count($nodes)==1) $xml_response['statusCode'] = (string)$nodes[0];
    
    $nodes = $xml->xpath('/ResponseDoc/response/errors/error');
    foreach ($nodes as $item) {
      $message=(string)$item->message;
      $code=(string)$item->code;
      $xml_response['errors'][]=array('message'=>$message, 'code'=>$code);
    } 
  
    
  } catch (Exception $e) { 
    $xml_response['statusCode']='gks_parse_error';
    $xml_response['errors'][]=gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ').' (xml parse error)' ;//: '.$e; 
  }
  
  $temp='';
  $i=0;
  foreach ($xml_response['errors'] as $value) {
    $i++;
    $temp.='<tr>'.
    '<th scope="row" nowrap style="text-align:center">'.$i.'</th>';
    
    if (is_array($value) and isset($value['code'])) {
      $temp.='<td nowrap style="text-align:center">'.htmlspecialchars_gks($value['code']).'</td>';
      $temp.='<td  style="text-align:left">'.htmlspecialchars_gks($value['message']).'</td>';
    } else if (is_string($value)) {
      $temp.='<td nowrap>-</td>';
      $temp.='<td style="text-align:left">'.htmlspecialchars_gks($value).'</td>';
    } else {
      echo '<pre>';
      var_dump($value);
      die();
    }
    $temp.='</tr>';
  } 
  if ($temp!='') {
    $temp=
    '<p>'.gks_lang('Παρουσιάστηκαν τα παρακάτω σφάλματα κατά την διαδικασία').'</p>'.
    '<table class="table table-sm table-responsive1 table-striped table-bordered" style="width: 100%;font-size:0.8rem;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
        '<tr>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κωδικός').'</th>'.
          '<th class="table-dark" scope="col" style="text-align: left !important;" width="100%">'.gks_lang('Περιγραφή').'</th>'.
        '</tr>'.
      '</thead>'.
      '<tbody>'.
        $temp.
      '</tbody>'.
    '</table>';
    
  }
  $xml_response['errors_human']=$temp;
  
  return $xml_response;
}



function gks_aade_invoice_cancel_xml_parse_response($xml_string) {
  $xml_response=array('index'=>0, 'invoiceUid'=>'', 'invoiceMark'=>'', 'statusCode' => '','errors' => array(),'errors_human'=>'');
  
  //$xml_string='gggggg';
  //libxml_use_internal_errors(true);
  try {
    
    $xml = new SimpleXMLElement($xml_string, LIBXML_NOERROR);
    
    $nodes = $xml->xpath('/ResponseDoc/response/cancellationMark');
    if (count($nodes)==1) $xml_response['invoiceMark'] = ((string)$nodes[0]);
    
    
    $nodes = $xml->xpath('/ResponseDoc/response/statusCode');
    if (count($nodes)==1) $xml_response['statusCode'] = (string)$nodes[0];
    
    $nodes = $xml->xpath('/ResponseDoc/response/errors/error');
    foreach ($nodes as $item) {
      $message=(string)$item->message;
      $code=(string)$item->code;
      $xml_response['errors'][]=array('message'=>$message, 'code'=>$code);
    } 
  
    
  } catch (Exception $e) { 
    $xml_response['statusCode']='gks_parse_error';
    $xml_response['errors'][]=gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ').' (xml parse error)' ;//: '.$e; 
  }
  
  $temp='';
  $i=0;
  foreach ($xml_response['errors'] as $value) {
    $i++;
    $temp.='<tr>'.
    '<th scope="row" nowrap style="text-align:center">'.$i.'</th>';
    
    if (is_array($value) and isset($value['code'])) {
      $temp.='<td nowrap style="text-align:center">'.htmlspecialchars_gks($value['code']).'</td>';
      $temp.='<td  style="text-align:left">'.htmlspecialchars_gks($value['message']).'</td>';
    } else if (is_string($value)) {
      $temp.='<td nowrap>-</td>';
      $temp.='<td style="text-align:left">'.htmlspecialchars_gks($value).'</td>';
    } else {
      echo '<pre>';
      var_dump($value);
      die();
    }
    $temp.='</tr>';
  } 
  if ($temp!='') {
    $temp=
    '<p>'.gks_lang('Παρουσιάστηκαν τα παρακάτω σφάλματα κατά την διαδικασία').'</p>'.
    '<table class="table table-sm table-responsive1 table-striped table-bordered" style="width: 100%;font-size:0.8rem;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">'.
      '<thead>'.
        '<tr>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>'.
          '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κωδικός').'</th>'.
          '<th class="table-dark" scope="col" style="text-align: left !important;" width="100%">'.gks_lang('Περιγραφή').'</th>'.
        '</tr>'.
      '</thead>'.
      '<tbody>'.
        $temp.
      '</tbody>'.
    '</table>';
    
  }
  $xml_response['errors_human']=$temp;
  
  return $xml_response;
}
 

function gks_aade_invoice_xml_save($id,$xml_string,$filename,$doc_table) {
  
  if ($doc_table=='gks_acc_inv') $sub_dir='acc/inv/';
  else if ($doc_table=='gks_acc_pay') $sub_dir='acc/pay/';
  else if ($doc_table=='gks_whi_mov') $sub_dir='whi/mov/';
  
  
  $ret = array('success' => false, 'message' => 'generic error');
  if ($id<=0) {$ret['message']=gks_lang('Δεν έχει ορισθεί το').' ID';debug_mail(false,$ret['message'],''); return $ret;}
  
  $save_dir = GKS_FileServerShare.$sub_dir.$id.'/aade_mydata/';
  if (file_exists($save_dir) == false) {
    if (@mkdir($save_dir , 0777, true) == false ) {
      $ret['message']=gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').': '.substr($save_dir, strlen(GKS_FileServerShare)); debug_mail(false,$ret['message']); return $ret;
    }
  }
  if ($filename=='') {
    $set_filename='invoice_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999).'-1-send.xml';
  } else {
    $set_filename=$filename;
  }
  $full_path=$save_dir.$set_filename;
  file_put_contents($full_path, $xml_string);  
  
  
  $ret['full_path']=$full_path;
  $ret['filename']=$set_filename;
  $ret['message']='OK';
  $ret['success']=true;

  return $ret;  
}

function gks_aade_invoice_xml_send($id, $xml_string,$aade_params) {
  global $db_link;
  $ret = array('success' => false, 'message' => 'generic error');



    
  $aade_url=($aade_params['mydata_live']==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).'SendInvoices';
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$aade_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$xml_string);
  curl_setopt($ch, CURLOPT_HTTPHEADER,array(
    'Content-Type: text/xml',
    'aade-user-id: '.$aade_params['mydata_user_id'],
    'Ocp-Apim-Subscription-Key: '.$aade_params['mydata_subscription_key'],
  )); 
  
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  curl_close ($ch);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  if ($gks_curl_http_code==0) { //HTTP Host not found
    $ret['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    $ret['message']=gks_lang('Δεν βρέθηκε η υπηρεσία RequestTransmittedDocs της ΑΑΔΕ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
    $ret['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει ο κωδικός ΜΑΡΚ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $ret['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    $ret['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;debug_mail(false,$ret['message'],$result); return $ret;
  } 
  $ret['out_xml']=$result;
  $ret['message']='OK';
  $ret['success']=true;
  
  return $ret;
}

function gks_aade_invoice_cancel_send($id, $aade_invoicemark,$aade_params) {
  global $db_link;
  $ret = array('success' => false, 'message' => 'generic error');

  

    
  $aade_url=($aade_params['mydata_live']==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).'CancelInvoice?mark='.$aade_invoicemark;
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$aade_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,'');
  curl_setopt($ch, CURLOPT_HTTPHEADER,array(
    'Content-Type: text/xml',
    'aade-user-id: '.$aade_params['mydata_user_id'],
    'Ocp-Apim-Subscription-Key: '.$aade_params['mydata_subscription_key'],
  )); 
  
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  curl_close ($ch);
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  if ($gks_curl_http_code==0) { //HTTP Host not found
    $ret['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
    $ret['message']=gks_lang('Δεν βρέθηκε η υπηρεσία RequestTransmittedDocs της ΑΑΔΕ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
    $ret['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει ο κωδικός ΜΑΡΚ');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
    $ret['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');debug_mail(false,$ret['message'],$result); return $ret;
  } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
    $ret['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;debug_mail(false,$ret['message'],$result); return $ret;
  } 
  $ret['out_xml']=$result;
  $ret['message']='OK';
  $ret['success']=true;
  
  return $ret;
}


function gks_aade_requestvatinfo($id_company,$id_company_sub,$dateFrom,$dateTo,$entityVatNumber,$GroupedPerDay) {
  global $db_link;
  $ret = array('success' => false, 'message' => 'generic error');

  if ($id_company_sub==0) { //kentriko
    $sql="select * from gks_company where company_disable=0 and id_company=".$id_company;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε η εταιρεία με ID').' <b>'.$id_company.'</b>';
      debug_mail(false,$ret['message'],$sql); return $ret;}
    $row = $result->fetch_assoc();
    $aade_mydata_user_id=trim_gks($row['aade_mydata_user_id']);
    $aade_mydata_subscription_key=trim_gks($row['aade_mydata_subscription_key']);
    $aade_mydata_live=intval($row['aade_mydata_live']);
    if ($aade_mydata_user_id=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
    if ($aade_mydata_subscription_key=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
  

  } else {
    $sql="select gks_company_subs.*,company_title 
    from gks_company_subs 
    LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
    where company_sub_disable=0 and id_company_sub=".$id_company_sub;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε το υποκατάστημα με ID').' <b>'.$id_company_sub.'</b>';
      debug_mail(false,$ret['message'],$sql); return $ret;}
    $row = $result->fetch_assoc();
    $aade_mydata_user_id=trim_gks($row['aade_mydata_user_id_sub']);
    $aade_mydata_subscription_key=trim_gks($row['aade_mydata_subscription_key_sub']);
    $aade_mydata_live=intval($row['aade_mydata_live_sub']);
    
    if ($aade_mydata_user_id=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
      $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
    if ($aade_mydata_subscription_key=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
      $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
  }
  
/*
https://mydatapi.aade.gr/myDATA/RequestVatInfo?[entityVatNumber]&[dateFrom]&[dateT
o]&[GroupedPerDay]&[nextPartitionKey]&[nextRowKey]
*/
    
  $aade_url_template=($aade_mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).'RequestVatInfo?';
  if ($entityVatNumber!='') $aade_url_template.='&entityVatNumber='.$entityVatNumber;
  if ($dateFrom!='') $aade_url_template.='&dateFrom='.$dateFrom;
  if ($dateTo!='') $aade_url_template.='&dateTo='.$dateTo;
  
  if ($GroupedPerDay!='') $aade_url_template.='&GroupedPerDay='.$GroupedPerDay; //"true" or "false"

  $aade_url=$aade_url_template;
  
  $xml_final=
  '<?xml version="1.0" encoding="utf-8"?>'.
  '<RequestedVatInfo xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.aade.gr/myDATA/invoice/v1.0">';
  
  
  //echo '<pre>';print $aade_url;die();
  do {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$aade_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array(
      'Content-Type: text/xml',
      'aade-user-id: '.$aade_mydata_user_id,
      'Ocp-Apim-Subscription-Key: '.$aade_mydata_subscription_key,
    )); 
    
    $result=curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info = curl_getinfo($ch);
    curl_close ($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    if ($gks_curl_http_code==0) { //HTTP Host not found
      $ret['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');debug_mail(false,$ret['message'],$sql); return $ret;
    } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
      $ret['message']=gks_lang('Δεν βρέθηκε η υπηρεσία RequestTransmittedDocs της ΑΑΔΕ');debug_mail(false,$ret['message'],$result); return $ret;
    } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
      $ret['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει ο κωδικός ΜΑΡΚ');debug_mail(false,$ret['message'],$result); return $ret;
    } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
      $ret['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');debug_mail(false,$ret['message'],$result); return $ret;
    } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
      $ret['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;debug_mail(false,$ret['message'],$result); return $ret;
    } 
  
    
    $xml = new SimpleXMLElement($result, LIBXML_NOERROR);
    $xmlname=$xml->getName();
    
    if ($xmlname!='RequestedVatInfo') break;
/*
  <continuationToken>
    <nextPartitionKey>065938168</nextPartitionKey>
    <nextRowKey>400001946495438</nextRowKey>
  </continuationToken>
*/
  
    $nextPartitionKey='';
    $nextRowKey='';
    foreach ($xml->children() as $vatitem) {
      $iname=$vatitem->getName();
      if ($iname=='continuationToken') {
        $nextPartitionKey=(string)$vatitem->nextPartitionKey;
        $nextRowKey=(string)$vatitem->nextRowKey;
      } else {
        $child=$vatitem->asXML(); //ti kanei kai peza
        //echo '<pre>'.$child;die();
        $xml_final.=$child;
      }
    }
    
    ///echo '<pre>'.$nextPartitionKey.' '.$nextRowKey;  die();
    if ($nextPartitionKey=='' or $nextRowKey=='') break;
    
    $aade_url=$aade_url_template;
    $aade_url.='&nextPartitionKey='.$nextPartitionKey;
    $aade_url.='&nextRowKey='.$nextRowKey;
    
  } while (true);

  
  $xml_final.='</RequestedVatInfo>';
  //echo '<pre>'.$xml_final;die();
  
  $ret['out_xml']=$xml_final;
  $ret['message']='OK';
  $ret['success']=true;
  return $ret;
}

function gks_aade_requeste3info($id_company,$id_company_sub,$dateFrom,$dateTo,$entityVatNumber,$GroupedPerDay) {
  global $db_link;
  $ret = array('success' => false, 'message' => 'generic error');

  if ($id_company_sub==0) { //kentriko
    $sql="select * from gks_company where company_disable=0 and id_company=".$id_company;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε η εταιρεία με ID').' <b>'.$id_company.'</b>';
      debug_mail(false,$ret['message'],$sql); return $ret;}
    $row = $result->fetch_assoc();
    $aade_mydata_user_id=trim_gks($row['aade_mydata_user_id']);
    $aade_mydata_subscription_key=trim_gks($row['aade_mydata_subscription_key']);
    $aade_mydata_live=intval($row['aade_mydata_live']);
    if ($aade_mydata_user_id=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
    if ($aade_mydata_subscription_key=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
  

  } else {
    $sql="select gks_company_subs.*,company_title 
    from gks_company_subs 
    LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
    where company_sub_disable=0 and id_company_sub=".$id_company_sub;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε το υποκατάστημα με ID').' <b>'.$id_company_sub.'</b>';
      debug_mail(false,$ret['message'],$sql); return $ret;}
    $row = $result->fetch_assoc();
    $aade_mydata_user_id=trim_gks($row['aade_mydata_user_id_sub']);
    $aade_mydata_subscription_key=trim_gks($row['aade_mydata_subscription_key_sub']);
    $aade_mydata_live=intval($row['aade_mydata_live_sub']);
    
    if ($aade_mydata_user_id=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
      $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
    if ($aade_mydata_subscription_key=='') {
      $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
      $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
      $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
      debug_mail(false,$ret['message'],$sql); return $ret;}
  }
  
/*

https://mydataapidev.aade.gr/RequestE3Info?[entityVatNumber]&[dateFrom]&[dateTo]&[G
roupedPerDay]&[nextPartitionKey]&[nextRowKey]

https://mydatapi.aade.gr/myDATA/RequestE3Info_?[entityVatNumber]&[dateFrom]&[dateT
o]&[GroupedPerDay]&[nextPartitionKey]&[nextRowKey]
*/
    
  $aade_url_template=($aade_mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).'RequestE3Info?';
  if ($entityVatNumber!='') $aade_url_template.='&entityVatNumber='.$entityVatNumber;
  if ($dateFrom!='') $aade_url_template.='&dateFrom='.$dateFrom;
  if ($dateTo!='') $aade_url_template.='&dateTo='.$dateTo;
  
  if ($GroupedPerDay!='') $aade_url_template.='&GroupedPerDay='.$GroupedPerDay; //"true" or "false"
  
  
  $aade_url=$aade_url_template;
  
  $xml_final=
  '<?xml version="1.0" encoding="utf-8"?>'.
  '<RequestedE3Info xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.aade.gr/myDATA/invoice/v1.0">';
  
  
  //echo '<pre>';print $aade_url;die();
  do {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$aade_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array(
      'Content-Type: text/xml',
      'aade-user-id: '.$aade_mydata_user_id,
      'Ocp-Apim-Subscription-Key: '.$aade_mydata_subscription_key,
    )); 
    
    $result=curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info = curl_getinfo($ch);
    curl_close ($ch);
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    if ($gks_curl_http_code==0) { //HTTP Host not found
      $ret['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');debug_mail(false,$ret['message'],$sql); return $ret;
    } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
      $ret['message']=gks_lang('Δεν βρέθηκε η υπηρεσία RequestTransmittedDocs της ΑΑΔΕ');debug_mail(false,$ret['message'],$result); return $ret;
    } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
      $ret['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει ο κωδικός ΜΑΡΚ');debug_mail(false,$ret['message'],$result); return $ret;
    } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
      $ret['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');debug_mail(false,$ret['message'],$result); return $ret;
    } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
      $ret['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;debug_mail(false,$ret['message'],$result); return $ret;
    } 
  
    //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/aade_docs_raw'.time().'.xml',$result);//die();
    
    $xml = new SimpleXMLElement($result, LIBXML_NOERROR);
    $xmlname=$xml->getName();
    
    if ($xmlname!='RequestedE3Info') break;
/*
  <continuationToken>
    <nextPartitionKey>065938168</nextPartitionKey>
    <nextRowKey>400001946495438</nextRowKey>
  </continuationToken>
*/
  
    $nextPartitionKey='';
    $nextRowKey='';
    foreach ($xml->children() as $vatitem) {
      $iname=$vatitem->getName();
      if ($iname=='continuationToken') {
        $nextPartitionKey=(string)$vatitem->nextPartitionKey;
        $nextRowKey=(string)$vatitem->nextRowKey;
      } else {
        $child=$vatitem->asXML(); //ti kanei kai peza
        //echo '<pre>'.$child;die();
        $xml_final.=$child;
      }
    }
    
    ///echo '<pre>'.$nextPartitionKey.' '.$nextRowKey;  die();
    if ($nextPartitionKey=='' or $nextRowKey=='') break;
    
    $aade_url=$aade_url_template;
    $aade_url.='&nextPartitionKey='.$nextPartitionKey;
    $aade_url.='&nextRowKey='.$nextRowKey;
    
  } while (true);

  
  $xml_final.='</RequestedE3Info>';
  //echo '<pre>'.$xml_final;die();
  
  $ret['out_xml']=$xml_final;
  $ret['message']='OK';
  $ret['success']=true;
  return $ret;
}
