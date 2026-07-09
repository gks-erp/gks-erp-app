<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Εντολή για το ψηφιακό δελτίο αποστολής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_aade_delivery_note','view',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_ret_edit=gks_permission_user_can_action($my_wp_user_id, 'gks_aade_delivery_note','edit',0);
$perm_ret_add=gks_permission_user_can_action($my_wp_user_id, 'gks_aade_delivery_note','add',0);


//if ($perm_ret_acc_inv['success']==false and $perm_ret_whi_mov['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret_acc_inv['message']));echo json_encode($return); die();}


$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks(base64_decode($_POST['cmd']));
$cid=''; if (isset($_POST['cid'])) $cid=trim_gks(base64_decode($_POST['cid']));
$mark=''; if (isset($_POST['mark'])) $mark=trim_gks(base64_decode($_POST['mark']));
$qrUrl=''; if (isset($_POST['qrUrl'])) $qrUrl=trim_gks(base64_decode($_POST['qrUrl']));

if ($cid!='') {
  $temp=['gks_aade_delivery_note'=>['def_company'=>$cid]];
  gks_set_user_settings($my_wp_user_id, $temp);
}

if (in_array($cmd,['history','get_records','get_qrUrl','get_mark','status','register','confirm','reject'])==false) {
  debug_mail(false,'cmd not OK','cmd not OK');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εντολή είναι λάθος').' ('.$cmd.')'));
  echo json_encode($return); die();}

if ($cmd=='history') {
  //die('ssssssssssss1 '.$cmd);
  $id_aade_delivery_note=0;if (isset($_POST['id'])) $id_aade_delivery_note=intval($_POST['id']);  
  if ($id_aade_delivery_note<=0) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID'));
    echo json_encode($return); die();}
  
  $html='<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">'.
  '<thead>'.
    '<tr>'.
      '<th class="table-dark" scope="col" width="0%" nowrap>#</th>'.
      '<th class="table-dark" scope="col" width="20%" nowrap>'.gks_lang('Πότε').'</th>'.
      '<th class="table-dark" scope="col" width="20%" nowrap align="left">'.gks_lang('Ποιος').'</th>'.
      '<th class="table-dark" scope="col" width="60%" nowrap align="left">'.gks_lang('Τι').'</th>'.
    '</tr>'.
  '</thead>'. 
  '<tbody>';

  $sql_log="SELECT gks_aade_delivery_note_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  FROM gks_aade_delivery_note_log 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_aade_delivery_note_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_aade_delivery_note_log.aade_delivery_note_id=".$id_aade_delivery_note."
  ORDER BY id_aade_delivery_note_log DESC;";
  $result_log = $db_link->query($sql_log);        
  if (!$result_log) {
    debug_mail(false,'error sql',$sql_log);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}

  
  $j = 0;
  while ($row_log = $result_log->fetch_assoc()) {
    $j++; 

    $html.='<tr>'.
      '<th scope="row" align="center">'.$j.'</th>'.
      '<td align="left">'.showDate(strtotime($row_log['add_date']), 'd/m/Y H:i:s', 1).'</td>'.
      '<td align="left">'.$row_log['gks_nickname'].'</td>'.
      '<td align="left">'.str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_log['sxolio']).'</td>'.
    '</tr>';
  }
  $html.='</tbody></table>';
            
  $return = array('success' => true, 'message' => base64_encode('OK'),'html'=>base64_encode($html));
  echo json_encode($return); die();
            
  die('ssssssssssss '.$cmd);
  
}

if ($cmd=='get_records') {
  
  
} else if ($cmd=='get_qrUrl') {
  $qrUrl='';
} else if ($cmd=='get_mark') {
  $mark='';
} else if ($cmd=='status') {
  $qrUrl='';
} else if ($cmd=='register') {
  $mark='';
} else if ($cmd=='confirm') {
  $mark='';
} else if ($cmd=='reject') {
  //$mark='';
}

$ret=gks_aade_delivery_note_get_record(['mark'=>$mark,'qrUrl'=>$qrUrl,'cid'=>$cid,'cmd'=>$cmd]);
$id_aade_delivery_note=$ret['rec_id'];
if ($ret['success']==false) {
  $return = array(
    'success' => $ret['success'], 
    'message' => base64_encode($ret['message']),
    'id_aade_delivery_note'=>$id_aade_delivery_note,
  ); 
  echo json_encode($return); die();   
}


//print '<pre>ssssssssssq ';print_r($ret);die();


if ($cmd=='get_qrUrl') {
  if ($ret['qrUrl']!='') $ret['message']=gks_lang('Βρέθηκε το QRCode URL'); else $ret['message']=gks_lang('Δεν βρέθηκε το QRCode URL').'<br>'.$ret['message'];
  $return = array(
    'success' => ($ret['qrUrl']!='' ? true : false), 
    'message' => base64_encode($ret['message']),
    'qrUrl' => $ret['qrUrl'],
    'vat_issuer' => $ret['vat_issuer'],
    'vat_customer' => $ret['vat_customer'],
    'id_aade_delivery_note'=>$id_aade_delivery_note,
  ); 
  echo json_encode($return); die();     
}


if ($cmd=='get_mark') {
  if ($ret['mark']!='') $ret['message']=gks_lang('Βρέθηκε το ΜΑΡΚ'); else $ret['message']=gks_lang('Δεν βρέθηκε το ΜΑΡΚ');
  $return = array(
    'success' => ($ret['mark']!='' ? true : false), 
    'message' => base64_encode($ret['message']),
    'mark' => $ret['mark'],
    'vat_issuer' => $ret['vat_issuer'],
    'vat_customer' => $ret['vat_customer'],
    'id_aade_delivery_note'=>$id_aade_delivery_note,
  ); 
  echo json_encode($return); die();     
}

if ($cmd!='get_records') {
  $ret=gks_get_mydata_keys_per_company(['cid'=>$cid]);
  if ($ret['success']==false) {
    $return = array('success' => false, 'message' => base64_encode($ret['message']));
  	echo json_encode($return); die();}

  $title=$ret['title'];
  $mydata_user_id=$ret['mydata_user_id'];
  $mydata_subscription_key=$ret['mydata_subscription_key'];
  $mydata_live=$ret['mydata_live'];
}


$records=[];
if ($mark!='' or $qrUrl!='') {
  $sql_where=[];
  if ($qrUrl!='') {
    $sql_where[]="(aade_qrurl='".$db_link->escape_string($qrUrl)."')";
  } 
  if ($mark!='') {
    $sql_where[]="(aade_invoicemark='".$db_link->escape_string($mark)."')";
  } 
  $sql_where='('.implode(' or ',$sql_where).')';
  //echo '<pre>aaaaaaaaaaa '.$sql_where;die();
  
  $sql="select id_acc_inv,inv_date,inv_acc_seira_code,inv_acc_number_int,inv_state,
  aade_invoicemark,aade_qrurl,
  gks_acc_journal.acc_journal_descr
  from gks_acc_inv 
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal
  where ".$sql_where;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row = $result->fetch_assoc()) { 
    $records[]=array(
      'doc_table'=>'gks_acc_inv',
      'xxx_xxx_id'=>$row['id_acc_inv'],
      'from_mark'=> ($row['aade_invoicemark']==$mark ? true : false),
      'from_qrUrl'=> (strtolower($row['aade_qrurl'])==strtolower($qrUrl) ? true : false),
      'row'=>$row,
    );
  }
  
  $sql="select id_acc_pay,pay_date,pay_acc_seira_code,pay_acc_number_int,pay_state,
  aade_invoicemark,aade_qrurl,
  gks_acc_journal.acc_journal_descr
  from gks_acc_pay 
  LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal
  where ".$sql_where;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row = $result->fetch_assoc()) { 
    $records[]=array(
      'doc_table'=>'gks_acc_pay',
      'xxx_xxx_id'=>$row['id_acc_pay'],
      'from_mark'=> ($row['aade_invoicemark']==$mark ? true : false),
      'from_qrUrl'=> (strtolower($row['aade_qrurl'])==strtolower($qrUrl) ? true : false),
      'row'=>$row,
    );
  }
  
  $sql="select id_whi_mov,mov_date,mov_whi_seira_code,mov_whi_number_int,mov_state,
  aade_invoicemark,aade_qrurl,
  gks_acc_journal.acc_journal_descr 
  from gks_whi_mov 
  LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal
  where ".$sql_where;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row = $result->fetch_assoc()) { 
    $records[]=array(
      'doc_table'=>'gks_whi_mov',
      'xxx_xxx_id'=>$row['id_whi_mov'],
      'from_mark'=> ($row['aade_invoicemark']==$mark ? true : false),
      'from_qrUrl'=> (strtolower($row['aade_qrurl'])==strtolower($qrUrl) ? true : false),
      'row'=>$row,
    );
  }
}

$records_html=[];
foreach ($records as $rec) {
  $doc_descr='';
  $rec_status='';
  $rec_link='';
  $rec_date='';
  $rec_number='';
  switch ($rec['doc_table']) {   
    case 'gks_acc_inv':
      $doc_descr=gks_lang('Παραστατικό');
      $rec_status='<span class="acc_inv_state_'.$rec['row']['inv_state'].'">'.getAccInvStateDescr($rec['row']['inv_state']).'</span>';
      $rec_link='admin-acc-inv-item.php?id='.$rec['xxx_xxx_id'];
      $rec_date=showDate(strtotime($rec['row']['inv_date']),'d/m/Y H:i',1);
      $rec_number=$rec['row']['inv_acc_seira_code'].' | '.$rec['row']['inv_acc_number_int'];
      break;  
    case 'gks_acc_pay':
      $doc_descr=gks_lang('Είσπραξη/Πληρωμή');
      $rec_status='<span class="acc_pay_state_'.$rec['row']['pay_state'].'">'.getAccPayStateDescr($rec['row']['pay_state']).'</span>';
      $rec_link='admin-acc-pay-item.php?id='.$rec['xxx_xxx_id'];
      $rec_date=showDate(strtotime($rec['row']['pay_date']),'d/m/Y H:i',1);
      $rec_number=$rec['row']['pay_acc_seira_code'].' | '.$rec['row']['pay_acc_number_int'];
      break;  
    case 'gks_whi_mov':
      $doc_descr=gks_lang('Δελτίο Αποστολής');
      $rec_status='<span class="whi_mov_state_'.$rec['row']['mov_state'].'">'.getWhiMovStateDescr($rec['row']['mov_state']).'</span>';
      $rec_link='admin-whi-mov-item.php?id='.$rec['xxx_xxx_id'];
      $rec_date=showDate(strtotime($rec['row']['mov_date']),'d/m/Y H:i',1);
      $rec_number=$rec['row']['mov_whi_seira_code'].' | '.$rec['row']['mov_whi_number_int'];
      break;
    default:
  }
  
  $records_html[]='<a href="'.$rec_link.'">#'.$rec['xxx_xxx_id'].'</a> '.$doc_descr.
  ' '.$rec_status.' '.$rec_date.' | '.$rec['row']['acc_journal_descr'].' | '.$rec_number.
  ($rec['from_mark'] ? ' | '.gks_lang('από ΜΑΡΚ') :'').
  ($rec['from_qrUrl'] ? ' | '.gks_lang('από QRCode URL') :'');
} 
//echo '<pre>bbbbbbb '.$cmd."\r\n".$mark."\r\n".$qrUrl."\r\n".print_r($records,true); die();

if (count($records_html)>0) {
  $records_html=gks_lang('Βρέθηκε εδώ').' '.gks_lang('Οι καταχωρήσεις είναι').': <br>'.implode('<br>',$records_html);
} else {
  $records_html=gks_lang('Δεν βρέθηκε εδώ κάποια καταχώρηση');
}
$return = array(
  'success' => false, 
  'message' => base64_encode('generic error'), 
  'records' => base64_encode($records_html),
  'html'=>'', 
  'raw_data_send'=>'',
  'raw_data_response'=>'',
  'id_aade_delivery_note'=>$id_aade_delivery_note,
);

if ($cmd=='get_records') {
  $return['success']=true;
  $return['message']=base64_encode('OK');
  echo json_encode($return); die();
}

switch ($cmd) {
  case 'status':
    if ($mark=='') {
      debug_mail(false,'mark is not set','');
      $return['message']=base64_encode(gks_lang('Δεν έχει ορισθεί το').' ΜΑΡΚ');
      echo json_encode($return); die();}      
      
    $issuerVatNumber='';if (isset($_POST['issuerVatNumber'])) $issuerVatNumber=trim_gks(base64_decode($_POST['issuerVatNumber']));
    
  
    $aade_url=($mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).
      'GetDeliveryNoteStatus?mark='.$mark;
    if ($issuerVatNumber!='') $aade_url.='&issuerVatNumber='.$issuerVatNumber; 
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$aade_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array(
      'Content-Type: text/xml',
      'aade-user-id: '.$mydata_user_id,
      'Ocp-Apim-Subscription-Key: '.$mydata_subscription_key,
    )); 
    
    $result=curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info = curl_getinfo($ch);
    curl_close ($ch); 

    $return['raw_data_send']=base64_encode('GET mark='.$mark. ($issuerVatNumber!='' ? '&issuerVatNumber='.$issuerVatNumber : ''));

    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    if ($gks_curl_http_code==0) { //HTTP Host not found
      $return['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
      $return['message']=gks_lang('Δεν βρέθηκε');
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      $sxolio=gks_lang('Έλεγχος κατάστασης').'<br>'.$return['message'];
      gks_admin_aade_delivery_note_cmd_log($sxolio);
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
      $return['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει ο κωδικός ΜΑΡΚ');
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
      $return['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
      $return['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } 
    
    $return['raw_data_response']=base64_encode($result);
    
    $ret_parse=gks_aade_delivery_note_parse_xml_status($result);
    if ($ret_parse['success']==false) {
      $return['message']=base64_encode($ret_parse['message']);
      echo json_encode($return); die();}
    
    
    $return['html']=base64_encode($ret_parse['html']);
    $sxolio=gks_lang('Έλεγχος κατάστασης').'<br>'.$ret_parse['html'];
    gks_admin_aade_delivery_note_cmd_log($sxolio);
    
    $sql_status="update gks_aade_delivery_note set 
    last_state='".$db_link->escape_string($ret_parse['aade_delivery_status'])."',
    last_date_get_data=now(),
    last_raw_data='".$db_link->escape_string($result)."'
    where id_aade_delivery_note=".$id_aade_delivery_note;
    $result_status = $db_link->query($sql_status);  
    if (!$result_status) {
      debug_mail(false,'error sql',$sql_status);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    
    $call_from=''; if (isset($_POST['call_from'])) $call_from=trim_gks(base64_decode($_POST['call_from']));
    if ($call_from=='item') {
      unset($return['raw_data_response']);
      unset($return['raw_data_send']);
      unset($return['records']);
      $return['gsdn_status']=$ret_parse['aade_delivery_status'];
      $return['gsdn_status_descr']=base64_encode(getAADE_InvoiceDeliveryStatus($ret_parse['aade_delivery_status']));
      $return['date']=base64_encode(showDate(time(), 'd/m/Y H:i:s', 1));
      $return['records_cc']=0;
      $sql_adn="select count(*) as cc from gks_aade_delivery_note_log where aade_delivery_note_id=".$id_aade_delivery_note;
      $result_adn = $db_link->query($sql_adn);        
      if (!$result_adn) {
        debug_mail(false,'error sql',$sql_adn);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      if ($result_adn->num_rows==1) {
        $row_adn = $result_adn->fetch_assoc();
        $return['records_cc']=intval($row_adn['cc']);
      }  
      
      $return['vat_issuer']='';
      $return['vat_customer']='';
      $sql_adn="select vat_issuer,vat_customer from gks_aade_delivery_note where id_aade_delivery_note=".$id_aade_delivery_note;    
      $result_adn = $db_link->query($sql_adn);        
      if (!$result_adn) {
        debug_mail(false,'error sql',$sql_adn);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      if ($result_adn->num_rows==1) {
        $row_adn = $result_adn->fetch_assoc();
        $return['vat_issuer']=base64_encode(trim_gks($row_adn['vat_issuer']).' '.gks_get_user_from_afm(trim_gks($row_adn['vat_issuer'])));
        $return['vat_customer']=base64_encode(trim_gks($row_adn['vat_customer']).' '.gks_get_user_from_afm(trim_gks($row_adn['vat_customer'])));
      }  
      
      
    }
    
    break;  
  case 'register':

    if ($qrUrl=='') {
      debug_mail(false,'qrUrl is not set','');
      $return['message']=base64_encode(gks_lang('Δεν έχει ορισθεί το').' QRCode URL');
      echo json_encode($return); die();}      
      
    $vehicleNumber='';if (isset($_POST['vehicleNumber'])) $vehicleNumber=trim_gks(base64_decode($_POST['vehicleNumber']));
    $transportType='';if (isset($_POST['transportType'])) $transportType=trim_gks(base64_decode($_POST['transportType']));
    //$timeStamp='';if (isset($_POST['timeStamp'])) $timeStamp=trim_gks(base64_decode($_POST['timeStamp']));
    $carrierVatNumber='';if (isset($_POST['carrierVatNumber'])) $carrierVatNumber=trim_gks(base64_decode($_POST['carrierVatNumber']));
    $pNumber='';if (isset($_POST['pNumber'])) $pNumber=trim_gks(base64_decode($_POST['pNumber']));
    $longitude='';if (isset($_POST['longitude'])) $longitude=trim_gks(base64_decode($_POST['longitude']));
    $latitude='';if (isset($_POST['latitude'])) $latitude=trim_gks(base64_decode($_POST['latitude']));

    if ($vehicleNumber=='') {
      debug_mail(false,'vehicleNumber is not set','');
      $return['message']=base64_encode(gks_lang('Δεν έχει ορισθεί ο Αριθμός Μεταφορικού Μέσου'));
      echo json_encode($return); die();}      

    $transportType=intval($transportType);
    if ($transportType<1 or $transportType>6) {
      debug_mail(false,'transportType is not set','');
      $return['message']=base64_encode(gks_lang('Δεν έχει ορισθεί το Είδος Μεταφορικού Μέσου'));
      echo json_encode($return); die();}      
    
//    if ($timeStamp=='__/__/____ __:__') $timeStamp='';
//    if ($timeStamp!='') {
//      $timeStamp =mystrtodb($timeStamp);
//      $timeStamp=_time_user(strtotime($timeStamp),1);
//      $date = new DateTime(date('Y-m-d H:i:s',$timeStamp), new DateTimeZone('Europe/Athens'));
//      $timeStamp=$date->format('c');
//    }

    if ($carrierVatNumber=='') {
      debug_mail(false,'carrierVatNumber is not set','');
      $return['message']=base64_encode(gks_lang('Δεν έχει ορισθεί το ΑΦΜ Μεταφορικής Εταιρείας'));
      echo json_encode($return); die();}      

    $longitude=floatval($longitude);  
    $latitude=floatval($latitude);  

    $xml_string='<Transport>'."\n";
      $xml_string.='<transferMark>0</transferMark>'."\n";
      $xml_string.='<qrUrl>'.$qrUrl.'</qrUrl>'."\n";
      
      $xml_string.='<transportDetail>'."\n";
        if ($vehicleNumber!='') $xml_string.='<vehicleNumber>'.$vehicleNumber.'</vehicleNumber>'."\n";
        if ($transportType>0) $xml_string.='<transportType>'.$transportType.'</transportType>'."\n";
        //if ($timeStamp!='') $xml_string.='<timeStamp>'.$timeStamp.'</timeStamp>'."\n";
        if ($carrierVatNumber!='') $xml_string.='<carrierVatNumber>'.$carrierVatNumber.'</carrierVatNumber>'."\n";
        if ($pNumber!='') $xml_string.='<pNumber>'.$pNumber.'</pNumber>'."\n";
        if ($longitude!=0 or $latitude!=0)  {
          $xml_string.='<location>'."\n";
            $xml_string.='<longitude>'.$longitude.'</longitude>'."\n";
            $xml_string.='<latitude>'.$latitude.'</latitude>'."\n";
          $xml_string.='</location>'."\n";
        }
      $xml_string.='</transportDetail>'."\n";
    $xml_string.='</Transport>';
      
    //echo '<pre>sssssss<pre>'. htmlspecialchars($xml_string, ENT_NOQUOTES);die();
    //echo '<pre>sssssss<pre>'.$vehicleNumber."\r\n".$transportType."\r\n".$timeStamp."\r\n".$carrierVatNumber."\r\n".$pNumber."\r\n".$longitude."\r\n".$latitude  ;die();
  
  
    $aade_url=($mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).
      'RegisterTransfer';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$aade_url);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$xml_string);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array(
      'Content-Type: text/xml',
      'aade-user-id: '.$mydata_user_id,
      'Ocp-Apim-Subscription-Key: '.$mydata_subscription_key,
    )); 
    
    $result=curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info = curl_getinfo($ch);
    curl_close ($ch); 

    $return['raw_data_send']=base64_encode('POST '.$xml_string);

    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    if ($gks_curl_http_code==0) { //HTTP Host not found
      $return['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
      $return['message']=base64_encode(gks_lang('Δεν βρέθηκε'));
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
      $return['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει το QRCode URL');
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
      $return['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
      $return['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } 
    
    $return['raw_data_response']=base64_encode($result);

    $html=[];
    try {
      $xml = new SimpleXMLElement($result, LIBXML_NOERROR);
      $rootnode = $xml->xpath('/ResponseDoc');
      if (count($rootnode)!=1) {
        $return['message']= gks_lang('Σφάλμα κατά την αναγνώριση του XML (ResponseDoc)');
        debug_mail(false,$return['message'],$result);
        $return['message']=base64_encode($return['message']);
        echo json_encode($return); die();          
      }
      
      $nodes_errors=$xml->xpath('/ResponseDoc/response/errors/error');
      if (count($nodes_errors)>0) {
        $error_list=[];
        foreach ($nodes_errors as $myerror) {
          $error_list[]=gks_lang('Κωδικός σφάλματος').': '.$myerror->code.'<br>'.gks_lang('Περιγραφή').': '.$myerror->message;
        }
        $return['message']= gks_lang('Σφάλμα κατά την διαδικασία').':<br>'.implode('<br>',$error_list);

        $sxolio=getAADE_DeliveryEventType('RegisterTransfer').'<br>'.$return['message'];
        gks_admin_aade_delivery_note_cmd_log($sxolio);
        debug_mail(false,$return['message'],$result);
        $return['message']=base64_encode($return['message']);
        echo json_encode($return); die(); 
 
      }
      
      $nodes_mark=$xml->xpath('/ResponseDoc/response/transferMark');
      $xml_mark='';if (count($nodes_mark)==1) $xml_mark=((string)$nodes_mark[0]);
      
      $nodes_status=$xml->xpath('/ResponseDoc/response/statusCode');
      $xml_status='';if (count($nodes_status)==1) $xml_status=((string)$nodes_status[0]);
      
      if ($xml_mark!='' and $xml_status=='Success') {
        $html[]=gks_lang('Η διαδικασία έγινε επιτυχώς<br>Ο Μοναδικός Αριθμός Εκκίνησης/Μεταφόρτωσης Διακίνησης είναι ο').':<br><b>'.$xml_mark.'</b>';
      } else {
        $return['message']= gks_lang('Γενικό σφάλμα κατά την διαδικασία');
        debug_mail(false,$return['message'],$result);
        $return['message']=base64_encode($return['message']);
        echo json_encode($return); die();
      }
      
      $sxolio=getAADE_DeliveryEventType('RegisterTransfer').'<br>'.implode('<br>',$html);
      gks_admin_aade_delivery_note_cmd_log($sxolio);
              
    } catch (Exception $e) { 
      $return['message']= gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)');
      debug_mail(false,$return['message'],$result);
      $return['message']=base64_encode($return['message']);
      echo json_encode($return); die();
    }
    
    $return['html']= base64_encode(implode('<br>',$html));
    break;  
    
  case 'confirm':
    if ($qrUrl=='') {
      debug_mail(false,'qrUrl is not set','');
      $return['message']=base64_encode(gks_lang('Δεν έχει ορισθεί το').' QRCode URL');
      echo json_encode($return); die();}      
      
    $outcome='';if (isset($_POST['outcome'])) $outcome=trim_gks(base64_decode($_POST['outcome']));
    $deliveredWithoutRecipient='';if (isset($_POST['deliveredWithoutRecipient'])) $deliveredWithoutRecipient=trim_gks(base64_decode($_POST['deliveredWithoutRecipient']));

    if ($outcome!='FULL' and $outcome!='PARTIAL' and $outcome!='NONE') {
      debug_mail(false,'outcome is not set','');
      $return['message']=base64_encode(gks_lang('Δεν έχει ορισθεί το αποτέλεσμα της παράδοσης'));
      echo json_encode($return); die();}
    $deliveredWithoutRecipient=intval($deliveredWithoutRecipient);
    if ($deliveredWithoutRecipient!=1) $deliveredWithoutRecipient=0;
    

    $xml_string='<ConfirmDeliveryOutcomeRequest>'."\n";
      $xml_string.='<qrUrl>'.$qrUrl.'</qrUrl>'."\n";
      $xml_string.='<outcome>'.$outcome.'</outcome>'."\n";
      if ($deliveredWithoutRecipient==1) $xml_string.='<deliveredWithoutRecipient>true</deliveredWithoutRecipient>'."\n";
      if ($outcome=='PARTIAL') {
        $dpitems_str=''; if (isset($_POST['dpitems_str'])) $dpitems_str=trim_gks(base64_decode($_POST['dpitems_str']));
        $dpitems_array = json_decode($dpitems_str, true);
        if ($dpitems_array === null && json_last_error() !== JSON_ERROR_NONE) {
          debug_mail(false,'json_decode',$_POST['dpitems_str']);
          $return['message']=base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (dpitems)<br>'.gks_lang('Ξαναδοκιμάστε'));
          echo json_encode($return); die();}
        
        //print '<pre>';print_r($dpitems_array);die();
        
        foreach ($dpitems_array as $dpitem) {
          $dpitem['packagingType']=intval($dpitem['packagingType']);
          $dpitem['quantity']=intval($dpitem['quantity']);
          $dpitem['othertitle']=trim_gks($dpitem['othertitle']);
          if ($dpitem['packagingType']>=1 and $dpitem['packagingType']<=6 and $dpitem['quantity']>0) {
            $xml_string.='<deliveredPackaging>';
              $xml_string.='<packagingType>'.$dpitem['packagingType'].'</packagingType>';
              $xml_string.='<quantity>'.$dpitem['quantity'].'</quantity>';
              if ($dpitem['packagingType']==6 and $dpitem['othertitle']!='') {
                $xml_string.='<otherPackagingTypeTitle>'.$dpitem['othertitle'].'</otherPackagingTypeTitle>';
              }
            $xml_string.='</deliveredPackaging>';
          }
        } 
      }
    $xml_string.='</ConfirmDeliveryOutcomeRequest>';

  
    $aade_url=($mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).
      'ConfirmDeliveryOutcome';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$aade_url);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$xml_string);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array(
      'Content-Type: text/xml',
      'aade-user-id: '.$mydata_user_id,
      'Ocp-Apim-Subscription-Key: '.$mydata_subscription_key,
    )); 
    
    $result=curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info = curl_getinfo($ch);
    curl_close ($ch); 

    $return['raw_data_send']=base64_encode('POST '.$xml_string);

    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    if ($gks_curl_http_code==0) { //HTTP Host not found
      $return['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
      $return['message']=base64_encode(gks_lang('Δεν βρέθηκε'));
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
      $return['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει το QRCode URL ή δεν μπορεί να βρεθεί στην ΑΑΔΕ.<br>Ίσως έχει ολοκληθωθεί/απορριθφεί');
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
      $return['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
      $return['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } 
    
    $return['raw_data_response']=base64_encode($result);

    $html=[];
    try {
      $xml = new SimpleXMLElement($result, LIBXML_NOERROR);
      $rootnode = $xml->xpath('/ResponseDoc');
      if (count($rootnode)!=1) {
        $return['message']= gks_lang('Σφάλμα κατά την αναγνώριση του XML (ResponseDoc)');
        debug_mail(false,$return['message'],$result);
        $return['message']=base64_encode($return['message']);
        echo json_encode($return); die();          
      }
      
      $nodes_errors=$xml->xpath('/ResponseDoc/response/errors/error');
      if (count($nodes_errors)>0) {
        $error_list=[];
        foreach ($nodes_errors as $myerror) {
          $error_list[]=gks_lang('Κωδικός σφάλματος').': '.$myerror->code.'<br>'.'Περιγραφή: '.$myerror->message;
        }
        $return['message']= gks_lang('Σφάλμα κατά την διαδικασία').':<br>'.implode('<br>',$error_list);
        
        $sxolio=getAADE_DeliveryEventType('ConfirmOutcome').'<br>'.$return['message'];
        gks_admin_aade_delivery_note_cmd_log($sxolio);
        debug_mail(false,$return['message'],$result);
        $return['message']=base64_encode($return['message']);
        echo json_encode($return); die(); 
 
      }
      
      //$nodes_mark=$xml->xpath('/ResponseDoc/response/invoiceMark');
      $nodes_mark=$xml->xpath('/ResponseDoc/response/deliveryOutcomeMark');
      $xml_mark='';if (count($nodes_mark)==1) $xml_mark=((string)$nodes_mark[0]);
      
      $nodes_status=$xml->xpath('/ResponseDoc/response/statusCode');
      $xml_status='';if (count($nodes_status)==1) $xml_status=((string)$nodes_status[0]);
      
      if ($xml_mark!='' and $xml_status=='Success') {
        $html[]=gks_lang('Η διαδικασία έγινε επιτυχώς<br>Ο Μοναδικός Αριθμός Καταχώρησης του παραστατικού είναι ο').':<br><b>'.$xml_mark.'</b>';
      } else {
        $return['message']= gks_lang('Γενικό σφάλμα κατά την διαδικασία');
        debug_mail(false,$return['message'],$result);
        $return['message']=base64_encode($return['message']);
        echo json_encode($return); die();
      }
      

      $sxolio=getAADE_DeliveryEventType('ConfirmOutcome').'<br>'.implode('<br>',$html);
      gks_admin_aade_delivery_note_cmd_log($sxolio);
              
    } catch (Exception $e) { 
      $return['message']= gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)');
      debug_mail(false,$return['message'],$result);
      $return['message']=base64_encode($return['message']);
      echo json_encode($return); die();
    }
    $return['html']= base64_encode(implode('<br>',$html));  
    break;  
  case 'reject':
    //echo '<pre>'.$mark;die();
    if ($qrUrl=='' and $mark=='') {
      debug_mail(false,'qrUrl or mark is not set','');
      $return['message']=base64_encode(gks_lang('Δεν έχει ορισθεί το').' QRCode URL'.' '.gks_lang('ή').' '.gks_lang('ΜΑΡΚ'));
      echo json_encode($return); die();}      
    
      
    $rejectionReason='';if (isset($_POST['rejectionReason'])) $rejectionReason=trim_gks(base64_decode($_POST['rejectionReason']));

    $xml_string='<RejectDeliveryNoteRequest>'."\n";
      if ($mark!='') {
        $xml_string.='<invoiceMark>'.$mark.'</invoiceMark>'."\n";
      } else if ($qrUrl!='') {
        $xml_string.='<qrUrl>'.$qrUrl.'</qrUrl>'."\n";
      }
      if ($rejectionReason!='') $xml_string.='<rejectionReason>'.$rejectionReason.'</rejectionReason>'."\n";
    $xml_string.='</RejectDeliveryNoteRequest>';

  
    $aade_url=($mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).
      'RejectDeliveryNote';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$aade_url);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$xml_string);    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array(
      'Content-Type: text/xml',
      'aade-user-id: '.$mydata_user_id,
      'Ocp-Apim-Subscription-Key: '.$mydata_subscription_key,
    )); 
    
    $result=curl_exec($ch);
    $gks_curl_errno=curl_errno($ch);
    $gks_curl_info = curl_getinfo($ch);
    curl_close ($ch); 

    $return['raw_data_send']=base64_encode('POST '.$xml_string);

    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    if ($gks_curl_http_code==0) { //HTTP Host not found
      $return['message']=gks_lang('Δεν βρέθηκε ο διακομιστής της ΑΑΔΕ');
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
      $return['message']=base64_encode(gks_lang('Δεν βρέθηκε'));
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
      $return['message']=gks_lang('ΑΑΔΕ mydata: Γενικό σφάλμα ή λείπει το QRCode URL ή δεν μπορεί να βρεθεί στην ΑΑΔΕ.<br>Ίσως έχει ολοκληθωθεί/απορριθφεί');
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      $sxolio=getAADE_DeliveryEventType('Rejection').'<br>'.$return['message'];
      gks_admin_aade_delivery_note_cmd_log($sxolio);
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
      $return['message']=gks_lang('ΑΑΔΕ mydata: Το Όνομα Χρήστη ή/και ο Κωδικός API είναι λάθος');
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
      $return['message']=gks_lang('ΑΑΔΕ mydata: HTTP Response Error').': '.$gks_curl_http_code;
      if ($result!='') {
        $ff=json_decode($result,true);  
        if (isset($ff['message'])) $return['message'].='<br>'.$ff['message'];
      }
      debug_mail(false,$return['message'],$result); 
      $return['message']=base64_encode($return['message']);
      $return['raw_data_response']=base64_encode($result);
      echo json_encode($return); die();
    } 
    
    $return['raw_data_response']=base64_encode($result);

    $html=[];
    try {
      $xml = new SimpleXMLElement($result, LIBXML_NOERROR);
      $rootnode = $xml->xpath('/ResponseDoc');
      if (count($rootnode)!=1) {
        $return['message']= gks_lang('Σφάλμα κατά την αναγνώριση του XML (ResponseDoc)');
        debug_mail(false,$return['message'],$result);
        $return['message']=base64_encode($return['message']);
        echo json_encode($return); die();          
      }
      
      $nodes_errors=$xml->xpath('/ResponseDoc/response/errors/error');
      if (count($nodes_errors)>0) {
        $error_list=[];
        foreach ($nodes_errors as $myerror) {
          $error_list[]=gks_lang('Κωδικός σφάλματος').': '.$myerror->code.'<br>'.gks_lang('Περιγραφή').': '.$myerror->message;
        }
        $return['message']= gks_lang('Σφάλμα κατά την διαδικασία').':<br>'.implode('<br>',$error_list);

        $sxolio=getAADE_DeliveryEventType('Rejection').'<br>'.$return['message'];
        gks_admin_aade_delivery_note_cmd_log($sxolio);
        debug_mail(false,$return['message'],$result);
        $return['message']=base64_encode($return['message']);
        echo json_encode($return); die(); 
 
      }
      
      $nodes_mark=$xml->xpath('/ResponseDoc/response/rejectMark');
      $xml_mark='';if (count($nodes_mark)==1) $xml_mark=((string)$nodes_mark[0]);
      
      $nodes_status=$xml->xpath('/ResponseDoc/response/statusCode');
      $xml_status='';if (count($nodes_status)==1) $xml_status=((string)$nodes_status[0]);
      
      if ($xml_mark!='' and $xml_status=='Success') {
        $html[]=gks_lang('Η διαδικασία έγινε επιτυχώς<br>Ο Μοναδικός Αριθμός Καταχώρησης του γεγονότος απόρριψης είναι ο').':<br><b>'.$xml_mark.'</b>';
      } else {
        $return['message']= gks_lang('Γενικό σφάλμα κατά την διαδικασία');
        debug_mail(false,$return['message'],$result);
        $return['message']=base64_encode($return['message']);
        echo json_encode($return); die();
      }

      $sxolio=getAADE_DeliveryEventType('Rejection').'<br>'.implode('<br>',$html);
      gks_admin_aade_delivery_note_cmd_log($sxolio);
      
    } catch (Exception $e) { 
      $return['message']= gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)');
      debug_mail(false,$return['message'],$result);
      $return['message']=base64_encode($return['message']);
      echo json_encode($return); die();
    }
    $return['html']= base64_encode(implode('<br>',$html));  
    break;  
  default:
    break;  
  
}

  
//echo '<pre>'.$cmd."\r\n".$mark."\r\n".$qrUrl."\r\n".print_r($records,true); die();



$return['success']=true;
$return['message']= base64_encode('OK');


echo json_encode($return); die();



