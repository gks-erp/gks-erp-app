<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_paroxos_signature_status_descr($id) {
  if ($id==null or $id=='') return '';
  switch ($id) {   
    case 'draft':     return gks_lang('Νέα','part4','paroxos_signature_status_descr');
    case 'assign':    return gks_lang('Στάλθηκε σε POS','part4','paroxos_signature_status_descr');
    case 'canreuse':  return gks_lang('Μπορεί να ξαναχρησιμοποιηθεί','part4','paroxos_signature_status_descr');
    case 'used':      return gks_lang('Χρησιμοποιήθηκε','part4','paroxos_signature_status_descr');
    case 'send':      return gks_lang('Στάλθηκε στον πάροχο','part4','paroxos_signature_status_descr');
    case 'canceled':  return gks_lang('Ακυρώθηκε','part4','paroxos_signature_status_descr');
    
  }  
  return $id;  
}



function gks_eftpos_transaction_type_descr($id) {
  if ($id==null or $id=='') return '';
  switch ($id) {   


    case 'control':                   return 'Control';
    case 'echo':                      return 'Echo';
    case 'fullvoid':                  return 'Full Void';
    case 'fullvoidecrtoken':          return 'Full Void Ecr Token';
    case 'fullvoiderp':               return 'Full Void ERP';
    case 'merchantinfo':              return 'Merchant Info';
    case 'preauthcompletion':         return 'Pre Auth Completion';
    case 'preauthcompletionecrtoken': return 'Pre Auth Completion Ecr Token';
    case 'preauthcompletionerp':      return 'Pre Auth Completion ERP';
    case 'preauthnormal':             return 'Pre Auth';
    case 'preauthonetap':             return 'One Tap Preauth';
    case 'preauthonetapcompletion':   return 'One Tap Preauth Completion';
    case 'reconciliation':            return 'Reconciliation';
    case 'refund':                    return 'Refund';
    case 'refundecrtoken':            return 'Refund Ecr Token';
    case 'refundecrtokenfree':        return 'Free Refund Ecr Token';
    case 'refunderp':                 return 'Refund ERP';
    case 'refunderpfree':             return 'Free Refund ERP';
    case 'refundfree':                return 'Free Refund';
    case 'regreceiptecrtoken':        return 'Registration Receipt Ecr Token';
    case 'regreceipterp':             return 'Registration Receipt ERP';
    case 'resendallfinish':           return 'Resend All Finish';
    case 'resendallnext':             return 'Resend All Next';
    case 'resendallstart':            return 'Resend All Start';
    case 'sale':                      return 'Sale';
    case 'saleecrtoken':              return 'Sale Ecr Token';
    case 'saleerp':                   return 'Sale ERP';
    case 'transactiondetails':        return 'Transaction Details';

  }
  return $id;
}

function gks_eftpos_transaction_status_descr($id) {
  if ($id==null or $id=='') return '';
  switch ($id) {   
    case 'draft':     return gks_lang('Πρόχειρη','part4','eftpos_transaction_status_descr');
    case 'async':     return gks_lang('Στάλθηκε','part4','eftpos_transaction_status_descr');
    case 'processed': return gks_lang('Σε εξέλιξη','part4','eftpos_transaction_status_descr');
    case 'canceled':  return gks_lang('Ακυρώθηκε','part4','eftpos_transaction_status_descr');
    case 'abort':     return gks_lang('Ματαιώθηκε','part4','eftpos_transaction_status_descr');
    case 'done':      return gks_lang('Έγινε','part4','eftpos_transaction_status_descr');
    case 'agnosto':   return gks_lang('Άγνωστο','part4','eftpos_transaction_status_descr');
    
    
  }  
  return $id;
  
}


function guid_for_eftpos_sessionId($not_in = array()) {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $guid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
    $guid = strtolower($guid);
    
    if (!(is_array($not_in) and count($not_in) and in_array($guid, $not_in))) {
      
      $sql = "SELECT sessionId from gks_eftpos_transaction where sessionId='".$db_link->escape_string($guid)."'";
      $result = $db_link->query($sql);
      
      if ($result->num_rows == 0) {
        return $guid; 
      }
    }
  }
}

function guid_for_eftpos_my_uniqueTxnId($not_in = array()) {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $guid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
    $guid = strtolower($guid);
    
    $guid=md5($guid);
    
    if (!(is_array($not_in) and count($not_in) and in_array($guid, $not_in))) {
      
      $sql = "SELECT my_uniqueTxnId from gks_eftpos_transaction where my_uniqueTxnId='".$db_link->escape_string($guid)."'";
      $result = $db_link->query($sql);
      
      if ($result->num_rows == 0) {
        return $guid; 
      }
    }
  }
}

function gks_eftpos_is_terminal_valid_for_this_payment_acquirer_with($id_payment_acquirer_with,$row_asset) {
  switch ($id_payment_acquirer_with) {
    case 1://viva 
      if (trim_gks($row_asset['viva_terminal_id'])=='') return false;
      break;
    case 2://megeftpos 
      if (trim_gks($row_asset['megeftpos_terminal_id'])=='') return false;
      break;
    case 3: //mellon
      if (trim_gks($row_asset['mellon_id'])=='' or trim_gks($row_asset['mellon_terminal_id'])=='') return false;
      break;
    case 4: //cardlink
      if (trim_gks($row_asset['cardlink_terminal_id'])=='') return false;
      break;
    case 5: //epay
      if (trim_gks($row_asset['epay_id'])=='' or trim_gks($row_asset['epay_terminal_id'])=='') return false;
      break;
    case 6: //worldline
      if (trim_gks($row_asset['worldline_id'])=='' or trim_gks($row_asset['worldline_terminal_id'])=='') return false;
      break;
    case 7: //nexi
      if (trim_gks($row_asset['nexi_id'])=='' or trim_gks($row_asset['nexi_terminal_id'])=='') return false;
      break;
    default:
      return false;
  }
  return true;
  
}
function gks_eftpos_get_terminal_id($id_payment_acquirer_with,$row_asset) {
  switch ($id_payment_acquirer_with) {
    case 1://viva 
      return trim_gks($row_asset['viva_terminal_id']);
      break;
    case 2://megeftpos 
      return trim_gks($row_asset['megeftpos_terminal_id']);
      break;
    case 3: //mellon
      return trim_gks($row_asset['mellon_terminal_id']);
      break;
    case 4: //cardlink
      return trim_gks($row_asset['cardlink_terminal_id']);
      break;
    case 5: //epay
      return trim_gks($row_asset['epay_terminal_id']);
      break;
    case 6: //worldline
      return trim_gks($row_asset['worldline_terminal_id']);
      break;
    case 7: //nexi
      return trim_gks($row_asset['nexi_terminal_id']);
      break;
  }
  return '';
  
}

function gks_eftpos_has_credentials($id_payment_acquirer_with,$row_company) {
  switch ($id_payment_acquirer_with) {
    case 1://viva 
      return gks_eftpos_has_credentials_viva($row_company);
      break;
    case 2://megeftpos 
      //den xreiazontai
      return array('success' => true, 'message' => 'OK');
      break;
    case 3://mellon 
      return gks_eftpos_has_credentials_mellon($row_company);
      break;
    case 4: //cardlink
      //den xreiazontai
      return array('success' => true, 'message' => 'OK'); 
      break;
    case 5://epay 
      return gks_eftpos_has_credentials_epay($row_company);
      break;
    case 6://worldline 
      return gks_eftpos_has_credentials_worldline($row_company);
      break;
    case 7://nexi 
      return gks_eftpos_has_credentials_nexi($row_company);
      break;
  }
  return array('success' => false, 'message' => gks_lang('Δεν έχει υλοποιηθεί ακόμα ο πάροχος πληρωμής με ID').' '.$id_payment_acquirer_with);  
}


function gks_eftpos_get_token($id_payment_acquirer_with,$row_company) {
  switch ($id_payment_acquirer_with) {
    case 1://viva 
      return gks_eftpos_get_token_viva($row_company);
      break;
    case 2: //megeftpos
      //den xreiazontai
      return array('success' => true, 'message' => 'OK','data' => array('access_token' => '')); 
      break;
    case 3://mellon 
      return gks_eftpos_get_token_mellon($row_company);
      break;
    case 4: //cardlink
      //den xreiazontai
      return array('success' => true, 'message' => 'OK','data' => array('access_token' => '')); 
      break;
    case 5://epay 
      return gks_eftpos_get_token_epay($row_company);
      break;
    case 6://worldline 
      return gks_eftpos_get_token_worldline($row_company);
      break;
    case 7://nexi 
      return gks_eftpos_get_token_nexi($row_company);
      break;
  }
  return array('success' => false, 'message' => gks_lang('Δεν έχει υλοποιηθεί ακόμα ο πάροχος πληρωμής με ID').' '.$id_payment_acquirer_with);  
}
  
  

function gks_eftpos_get_transaction_html($input) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $return=array('success' => false, 'message' => 'generic error',
    'transaction'=>array(
      'html'=> gks_lang('Σφάλμα').'. '.gks_lang('Δεν βρέθηκε η συναλλαγή'),
      'id_acc_xxx_payment' => 0,
    ),
  );
  //echo '<pre>';print_r($input);echo '</pre>';
  
  $sessionId=''; if (isset($input['sessionId'])) $sessionId=$input['sessionId'];
  $id_eftpos_transaction=''; if (isset($input['id_eftpos_transaction'])) $id_eftpos_transaction=intval($input['id_eftpos_transaction']);
  if ($id_eftpos_transaction==0 and $sessionId=='') {
    $return['message']=gks_lang('Δεν έχουν ορισθεί όλα τα δεδομένα');
    debug_mail(false,$return['message'],print_r($input,true));return $return;}
    
  
  $sql="SELECT gks_eftpos_transaction.*, 
  gks_payment_acquirer_with.payment_paroxos_name, 
  gks_aade_paroxos.paroxos_name,
  gks_assets.asset_title,
  gks_payment_acquirers.payment_acquirer_name
  FROM (((gks_eftpos_transaction 
  LEFT JOIN gks_assets ON gks_eftpos_transaction.asset_id = gks_assets.id_asset) 
  LEFT JOIN gks_payment_acquirer_with ON gks_eftpos_transaction.payment_acquirer_with_id = gks_payment_acquirer_with.id_payment_acquirer_with) 
  LEFT JOIN gks_aade_paroxos ON gks_eftpos_transaction.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos)
  LEFT JOIN gks_payment_acquirers ON gks_eftpos_transaction.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer
  where transaction_status='done' ";
  if ($sessionId!='') $sql.=" and sessionId='".$db_link->escape_string($sessionId)."'";
  else if ($id_eftpos_transaction>0) $sql.=" and id_eftpos_transaction=".$id_eftpos_transaction;
  else $sql.=" 1=2";

  //echo '<pre>'.$sql.'</pre>';
  
  $result = $db_link->query($sql);  
  if (!$result) {
    $return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows==1) {
    $row_tra2 = $result->fetch_assoc();  
    $transaction_type=$row_tra2['transaction_type'];
    $payment_paroxos_name=$row_tra2['payment_paroxos_name'];
    $paroxos_name=$row_tra2['paroxos_name'];
    $asset_id=intval($row_tra2['asset_id']);
    $asset_title=$row_tra2['asset_title'];
    $viva_terminal_id=$row_tra2['terminalId'];
    $transaction_status=$row_tra2['transaction_status'];
    $amount=floatval($row_tra2['amount']);
    $tipAmount=floatval($row_tra2['tipAmount']);
    $installments=intval($row_tra2['installments']);
    $merchantReference=$row_tra2['merchantReference'];
    $customerTrns=$row_tra2['customerTrns'];
    $aadeProviderId=$row_tra2['aadeProviderId'];
    $aadeProviderSignatureData=$row_tra2['aadeProviderSignatureData'];
    $aadeProviderSignature=$row_tra2['aadeProviderSignature'];
    $transactionId=trim_gks($row_tra2['transactionId']);
    $response_array=$row_tra2['response_array'];
    $id_acc_xxx_payment=0;
    if (intval($row_tra2['acc_inv_payment_id'])>0) $id_acc_xxx_payment=intval($row_tra2['acc_inv_payment_id']);
    if (intval($row_tra2['acc_pay_payment_id'])>0) $id_acc_xxx_payment=intval($row_tra2['acc_pay_payment_id']);
    $id_payment_acquirer=intval($row_tra2['payment_acquirer_id']);
    $payment_acquirer_name=trim_gks($row_tra2['payment_acquirer_name']);
    $sessionId_tra2=trim_gks($row_tra2['sessionId']);
    $donedate=$row_tra2['mydate_edit'];
    $aadeTransactionId=$row_tra2['aadeTransactionId'];
    
    $id_eftpos_transaction_db=intval($row_tra2['id_eftpos_transaction']);
    //echo '<pre>';print_r($donedate);die();
    
    $extra_html='';
    if ($transactionId!='') {
      switch ($row_tra2['payment_acquirer_with_id']) {
        case 1: // viva 
          $ret=gks_eftpos_get_transaction_extra_html_viva([
            'transactionId'=> $transactionId,
            'sessionId' => $sessionId_tra2,
            'id_eftpos_transaction'=>$id_eftpos_transaction_db,
            'amount' => $amount,
            'asset_id' => $asset_id,
            'asset_title' => $asset_title,
            'id_acc_xxx_payment' => $id_acc_xxx_payment,
          ]);
          if ($ret['success'] and isset($ret['transaction']['html'])) {
            $extra_html=$ret['transaction']['html'];
            if (isset($ret['transaction']['donedate'])) $donedate=$ret['transaction']['donedate'];
            if (isset($ret['transaction']['amount'])) $amount=$ret['transaction']['amount'];
            if (isset($ret['transaction']['tipamount'])) $tipAmount=$ret['transaction']['tipamount'];
            if (isset($ret['transaction']['installments'])) $installments=$ret['transaction']['installments'];
          }
          break;
        case 2: // megeftpos 
          $ret=gks_eftpos_get_transaction_extra_html_megeftpos([
            'transactionId'=> $transactionId,
            'sessionId' => $sessionId_tra2,
            'id_eftpos_transaction'=>$id_eftpos_transaction_db,
            'amount' => $amount,
            'asset_id' => $asset_id,
            'asset_title' => $asset_title,
            'id_acc_xxx_payment' => $id_acc_xxx_payment,
          ]);
          if ($ret['success'] and isset($ret['transaction']['html'])) {
            $extra_html=$ret['transaction']['html'];
            if (isset($ret['transaction']['donedate'])) $donedate=$ret['transaction']['donedate'];
            if (isset($ret['transaction']['amount'])) $amount=$ret['transaction']['amount'];
            if (isset($ret['transaction']['tipamount'])) $tipAmount=$ret['transaction']['tipamount'];
            if (isset($ret['transaction']['installments'])) $installments=$ret['transaction']['installments'];
          }
          break;
          
          
        case 3: // mellon 
          $ret=gks_eftpos_get_transaction_extra_html_mellon([
            'transactionId'=> $transactionId,
            'sessionId' => $sessionId_tra2,
            'id_eftpos_transaction'=>$id_eftpos_transaction_db,
            'amount' => $amount,
            'asset_id' => $asset_id,
            'asset_title' => $asset_title,
            'id_acc_xxx_payment' => $id_acc_xxx_payment,
          ]);
          if ($ret['success'] and isset($ret['transaction']['html'])) {
            $extra_html=$ret['transaction']['html'];
            if (isset($ret['transaction']['donedate'])) $donedate=$ret['transaction']['donedate'];
            if (isset($ret['transaction']['amount'])) $amount=$ret['transaction']['amount'];
            if (isset($ret['transaction']['tipamount'])) $tipAmount=$ret['transaction']['tipamount'];
            if (isset($ret['transaction']['installments'])) $installments=$ret['transaction']['installments'];
          }
          break;
          
          
        case 4: // cardlink 
          $ret=gks_eftpos_get_transaction_extra_html_cardlink([
            'transactionId'=> $transactionId,
            'sessionId' => $sessionId_tra2,
            'id_eftpos_transaction'=>$id_eftpos_transaction_db,
            'amount' => $amount,
            'asset_id' => $asset_id,
            'asset_title' => $asset_title,
            'id_acc_xxx_payment' => $id_acc_xxx_payment,
          ]);
          if ($ret['success'] and isset($ret['transaction']['html'])) {
            $extra_html=$ret['transaction']['html'];
            if (isset($ret['transaction']['donedate'])) $donedate=$ret['transaction']['donedate'];
            if (isset($ret['transaction']['amount'])) $amount=$ret['transaction']['amount'];
            if (isset($ret['transaction']['tipamount'])) $tipAmount=$ret['transaction']['tipamount'];
            if (isset($ret['transaction']['installments'])) $tipAmount=$ret['transaction']['installments'];
          }
          break;

        case 5: // epay 
          $ret=gks_eftpos_get_transaction_extra_html_epay([
            'transactionId'=> $transactionId,
            'sessionId' => $sessionId_tra2,
            'id_eftpos_transaction'=>$id_eftpos_transaction_db,
            'amount' => $amount,
            'asset_id' => $asset_id,
            'asset_title' => $asset_title,
            'id_acc_xxx_payment' => $id_acc_xxx_payment,
          ]);
          if ($ret['success'] and isset($ret['transaction']['html'])) {
            $extra_html=$ret['transaction']['html'];
            if (isset($ret['transaction']['donedate'])) $donedate=$ret['transaction']['donedate'];
            if (isset($ret['transaction']['amount'])) $amount=$ret['transaction']['amount'];
            if (isset($ret['transaction']['tipamount'])) $tipAmount=$ret['transaction']['tipamount'];
            if (isset($ret['transaction']['installments'])) $installments=$ret['transaction']['installments'];
          }
          break;
        case 6: // worldline 
          $ret=gks_eftpos_get_transaction_extra_html_worldline([
            'transactionId'=> $transactionId,
            'sessionId' => $sessionId_tra2,
            'id_eftpos_transaction'=>$id_eftpos_transaction_db,
            'amount' => $amount,
            'asset_id' => $asset_id,
            'asset_title' => $asset_title,
            'id_acc_xxx_payment' => $id_acc_xxx_payment,
          ]);
          if ($ret['success'] and isset($ret['transaction']['html'])) {
            $extra_html=$ret['transaction']['html'];
            if (isset($ret['transaction']['donedate'])) $donedate=$ret['transaction']['donedate'];
            if (isset($ret['transaction']['amount'])) $amount=$ret['transaction']['amount'];
            if (isset($ret['transaction']['tipamount'])) $tipAmount=$ret['transaction']['tipamount'];
            if (isset($ret['transaction']['installments'])) $installments=$ret['transaction']['installments'];
          }
          break;
        case 7: // nexi 
          $ret=gks_eftpos_get_transaction_extra_html_nexi([
            'transactionId'=> $transactionId,
            'sessionId' => $sessionId_tra2,
            'id_eftpos_transaction'=>$id_eftpos_transaction_db,
            'amount' => $amount,
            'asset_id' => $asset_id,
            'asset_title' => $asset_title,
            'id_acc_xxx_payment' => $id_acc_xxx_payment,
          ]);
          if ($ret['success'] and isset($ret['transaction']['html'])) {
            $extra_html=$ret['transaction']['html'];
            if (isset($ret['transaction']['donedate'])) $donedate=$ret['transaction']['donedate'];
            if (isset($ret['transaction']['amount'])) $amount=$ret['transaction']['amount'];
            if (isset($ret['transaction']['tipamount'])) $tipAmount=$ret['transaction']['tipamount'];
            if (isset($ret['transaction']['installments'])) $installments=$ret['transaction']['installments'];
          }
          break;
          
        default:
            
      }
      
    }
    
    $html=[];
    $html[]=gks_lang('Επιτυχής συναλλαγή στις').' '.showDate(strtotime($donedate), 'd/m/Y H:i', 1);
    $html[]=gks_lang('Ποσό').': <b>'.myCurrencyFormat($amount).'</b>';
    if ($tipAmount!=0) $html[]=gks_lang('Φιλοδώρημα').': <b>'.myCurrencyFormat($tipAmount).'</b>';
    if ($installments!=0) $html[]=gks_lang('Δόσεις').': <b>'.$installments.'</b>';
    if ($aadeTransactionId!='') $html[]=gks_lang('ΑΑΔΕ ID').': '.$aadeTransactionId;
    $html[]=gks_lang('Κωδικός συναλλαγής').': '.$transactionId;
    //$html[]=gks_lang('Κωδικός συναλλαγής').': <span title="'.$transactionId.'"><i class="fas fa-exclamation-circle"></i></span>';
    $html[]=gks_lang('Πάροχος πληρωμής').': '.$payment_paroxos_name;
    $html[]=gks_lang('Τερματικό').': '.$asset_title;
    $html[]=gks_lang('ID Τερματικού').': '.$viva_terminal_id;
    
    $html=implode('<br>',$html);
    if ($extra_html!='') $html.='<br>'.$extra_html;
    
    $return['success']=true;
    $return['message']='OK';
    $return['transaction']=array(
      'html'=>$html,
      'id_acc_xxx_payment' => $id_acc_xxx_payment,
      'id_payment_acquirer' => $id_payment_acquirer,
      'payment_acquirer_name' => $payment_acquirer_name,
      'asset_id' => $asset_id,
      'asset_title' => $asset_title,
      
    );
    
  
  }  
  return $return;
}



function gks_eftpos_build_json_for_send($input) {

  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $ret = array('success' => false, 'message' => 'generic error');
  
  $id_payment_acquirer_with=0; if (isset($input['id_payment_acquirer_with'])) $id_payment_acquirer_with=intval($input['id_payment_acquirer_with']);
  $transaction_type='';  if (isset($input['transaction_type'])) $transaction_type=trim_gks($input['transaction_type']);
  $id_asset=0;  if (isset($input['id_asset'])) $id_asset=intval($input['id_asset']);

  
  if ($id_asset>0) {
    
    $sql_asset="select * from gks_assets where asset_disable=0 and id_asset=".$id_asset;
    $result_asset = $db_link->query($sql_asset);
    if (!$result_asset) {
      $ret['message']='error sql';
      debug_mail(false,$ret['message'],$sql_asset);return $ret;}
      
    if ($result_asset->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε το πάγιο με ID').' '.$id_asset.'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
      debug_mail(false,$ret['message'],$sql_asset);return $ret;}
    $row_asset = $result_asset->fetch_assoc();
    
    $input['row_asset']=$row_asset;
  }
  
  //echo '<pre>asset id '.$id_asset;die();
  //echo '<pre>input ';print_r($input);die();
  
  
  //echo '<pre>ddddddddddaa '.$transaction_type;die();
  
  switch ($transaction_type) {   
    case 'ping_terminal':
      $ret=gks_eftpos_build_json_for_send_ping_terminal($input);
      break;  
    case 'ping_service':
      $ret=gks_eftpos_build_json_for_send_ping_service($input);
      break;  
    case 'merchantinfo':
      $ret=gks_eftpos_build_json_for_send_merchantinfo($input);
      break;  
    case 'reconciliation':
      $ret=gks_eftpos_build_json_for_send_reconciliation($input);
      break;  
    default:
      $ret['message']=gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος συναλλαγής').' '.$transaction_type;
		  debug_mail(false,$ret['message'],''); return $ret;  
  }
  
  return $ret;
  
  
}
function gks_eftpos_build_json_for_send_ping_terminal($input) {
  $id_payment_acquirer_with=intval($input['id_payment_acquirer_with']);
  $transaction_type='';  if (isset($input['transaction_type'])) $transaction_type=trim_gks($input['transaction_type']);
  $ret = array('success' => false, 'message' => 'generic error');
  //echo '<pre>sssssssssssssssss';die();
  switch ($id_payment_acquirer_with) {   
    case 2: //megeftpos
      $ret=gks_eftpos_build_json_for_send_ping_terminal_megeftpos($input);
      break;
    case 4: //cardlink
      $ret=gks_eftpos_build_json_for_send_ping_terminal_cardlink($input);
      break;
    default:
      $ret['message']=gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος συναλλαγής').' '.$transaction_type.
      ' '.gks_lang('για τον πάροχο').' '.$id_payment_acquirer_with;
		  debug_mail(false,$ret['message'],''); return $ret;  
      break;
   
  }
  return $ret;
}

function gks_eftpos_build_json_for_send_ping_service($input) {
  $id_payment_acquirer_with=intval($input['id_payment_acquirer_with']);
  $transaction_type='';  if (isset($input['transaction_type'])) $transaction_type=trim_gks($input['transaction_type']);
  $ret = array('success' => false, 'message' => 'generic error');
  
  switch ($id_payment_acquirer_with) {   
    case 4: //cardlink
      $ret=gks_eftpos_build_json_for_send_ping_service_cardlink($input);
      break;
    default:
      $ret['message']=gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος συναλλαγής').' '.$transaction_type.
      ' '.gks_lang('για τον πάροχο').' '.$id_payment_acquirer_with;
		  debug_mail(false,$ret['message'],''); return $ret;  
      break;
   
  }
  return $ret;
}

function gks_eftpos_build_json_for_send_merchantinfo($input) {
  $id_payment_acquirer_with=intval($input['id_payment_acquirer_with']);
  $transaction_type='';  if (isset($input['transaction_type'])) $transaction_type=trim_gks($input['transaction_type']);
  $ret = array('success' => false, 'message' => 'generic error');
  
  switch ($id_payment_acquirer_with) {   
    case 4: //cardlink
      $ret=gks_eftpos_build_json_for_send_merchantinfo_cardlink($input);
      break;
    default:
      $ret['message']=gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος συναλλαγής').' '.$transaction_type.
      ' '.gks_lang('για τον πάροχο').' '.$id_payment_acquirer_with;
		  debug_mail(false,$ret['message'],''); return $ret;  
      break;
   
  }
  return $ret;
}

function gks_eftpos_build_json_for_send_reconciliation($input) {
  $id_payment_acquirer_with=intval($input['id_payment_acquirer_with']);
  $transaction_type='';  if (isset($input['transaction_type'])) $transaction_type=trim_gks($input['transaction_type']);
  $ret = array('success' => false, 'message' => 'generic error');
  
  switch ($id_payment_acquirer_with) {   
    case 4: //cardlink
      $ret=gks_eftpos_build_json_for_send_reconciliation_cardlink($input);
      break;
    default:
      $ret['message']=gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος συναλλαγής').' '.$transaction_type.
      ' '.gks_lang('για τον πάροχο').' '.$id_payment_acquirer_with;
		  debug_mail(false,$ret['message'],''); return $ret;  
      break;
   
  }
  return $ret;
  
}

function gks_eftpos_sales_request($data) {
  $id_payment_acquirer_with=intval($data['payment_acquirer_with_id']);
  $ret = array('success' => false, 'message' => 'generic error');
  switch ($id_payment_acquirer_with) {
    case 1://viva 
      return gks_eftpos_sales_request_viva($data);
      break;
    case 2://megeftpos 
      return gks_eftpos_sales_request_megeftpos($data);
      break;
    case 3://mellon 
      return gks_eftpos_sales_request_mellon($data);
      break;
    case 4: //cardlink
      return gks_eftpos_sales_request_cardlink($data);
      break;
    case 5://epay 
      return gks_eftpos_sales_request_epay($data);
      break;
    case 6://worldline 
      return gks_eftpos_sales_request_worldline($data);
      break;
    case 7://nexi 
      return gks_eftpos_sales_request_nexi($data);
      break;
    default:
      $ret['message']=gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος συναλλαγής SALE για τον πάροχο').' '.$id_payment_acquirer_with;
		  debug_mail(false,$ret['message'],''); return $ret;  
      break;
  }
  return $ret;
    
  
}

function gks_eftpos_sales_request_get_status($id_payment_acquirer_with,$data) {
  $ret = array('success' => false, 'message' => 'generic error');
  switch ($id_payment_acquirer_with) {   
    case 1: //viva
      return gks_eftpos_sales_request_get_status_viva($data);
      break;
    case 2: //megeftpos
      return gks_eftpos_sales_request_get_status_megeftpos($data);
      break;
    case 3: //mellon
      return gks_eftpos_sales_request_get_status_mellon($data);
      break;
    case 4: //cardlink
      return gks_eftpos_sales_request_get_status_cardlink($data);
      break;
    case 5: //epay
      return gks_eftpos_sales_request_get_status_epay($data);
      break;
    case 6: //worldline
      return gks_eftpos_sales_request_get_status_worldline($data);
      break;
    case 7: //nexi
      return gks_eftpos_sales_request_get_status_nexi($data);
      break;
      
    default:      
      $ret['message']=gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος SALE get_status για τον πάροχο').' '.$id_payment_acquirer_with;
		  debug_mail(false,$ret['message'],''); return $ret;  

  }
  
}


function gks_eftpos_fullvoid_request($data) {
  $id_payment_acquirer_with=intval($data['payment_acquirer_with_id']);
  $ret = array('success' => false, 'message' => 'generic error');
  $transaction_type=$data['transaction_type'];
  switch ($id_payment_acquirer_with) {
    case 1://viva 
      return gks_eftpos_fullvoid_request_viva($data);
      break;
    case 2://megeftpos 
      return gks_eftpos_fullvoid_request_megeftpos($data);
      break;
    case 3://mellon 
      if (in_array($transaction_type,['fullvoid','fullvoiderp'])) {
        return gks_eftpos_fullvoid_request_mellon($data);
      } else {
        return gks_eftpos_sales_request_mellon($data);
      }
      break;
    case 4: //cardlink
      return gks_eftpos_fullvoid_request_cardlink($data);
      break;
    case 5://epay 
      if (in_array($transaction_type,['fullvoid','fullvoiderp'])) {
        return gks_eftpos_fullvoid_request_epay($data);
      } else {
        return gks_eftpos_sales_request_epay($data);
      }
      break;
    case 6://worldline 
      if (in_array($transaction_type,['fullvoid','fullvoiderp'])) {
        return gks_eftpos_fullvoid_request_worldline($data);
      } else {
        return gks_eftpos_sales_request_worldline($data);
      }
      break;
    case 7://nexi 
      if (in_array($transaction_type,['fullvoid','fullvoiderp'])) {
        return gks_eftpos_fullvoid_request_nexi($data);
      } else {
        return gks_eftpos_sales_request_nexi($data);
      }
      break;
    default:
      $ret['message']=gks_lang('Δεν έχει υλοποιηθεί ακόμη ο τύπος συναλλαγής fullvoid για τον πάροχο').' '.$id_payment_acquirer_with;
		  debug_mail(false,$ret['message'],''); return $ret;  
      break;
  }
  return $ret;
    
  
}



function gks_eftpos_set_payment_via_iris($id_eftpos_transaction) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $id_eftpos_transaction=intval($id_eftpos_transaction);
  if ($id_eftpos_transaction<=0) return array('success' => true, 'message' => 'OK');
  
  $sql="select acc_inv_payment_id,acc_pay_payment_id,
  payment_acquirer_with_id,xxx_transaction_id
  from gks_eftpos_transaction
  where id_eftpos_transaction=".$id_eftpos_transaction."
  and transaction_status='done'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
  if ($result->num_rows==0) return array('success' => true, 'message' => 'OK');
  $row = $result->fetch_assoc(); 
  $payment_acquirer_with_id=intval($row['payment_acquirer_with_id']);
  $xxx_transaction_id=intval($row['xxx_transaction_id']);
  if ($payment_acquirer_with_id<=0 or $xxx_transaction_id<=0) return array('success' => true, 'message' => 'OK');
  
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
  } else {
    return array('success' => true, 'message' => 'OK'); 
  }

  if ($value_acc_xxx_payment_id<=0) return array('success' => true, 'message' => 'OK');
  
  
  $pmd_item_is_iris=false;
  switch ($payment_acquirer_with_id) {   
    case 1://viva
      $sql_xxx_tra="SELECT BankId from gks_viva_transaction 
      where id_viva_transaction=".$xxx_transaction_id;
      $result_xxx_tra = $db_link->query($sql_xxx_tra); 
      if (!$result_xxx_tra) {debug_mail(false,'error sql',$sql_xxx_tra);$ret['message']='sql error'; return $ret;}
      if ($result_xxx_tra->num_rows>=1) {
        $row_xxx_tra=$result_xxx_tra->fetch_assoc();  
        if ($row_xxx_tra['BankId']=='NET_IRIS') {
          $pmd_item_is_iris=true;
          //echo '<pre>ssssssssssssss '.$row_xxx_tra['BankId'];die();
        }
      }  
      break;  
    case 3://mellon
      $sql_xxx_tra="SELECT PaymentType from gks_mellon_transaction 
      where id_mellon_transaction=".$xxx_transaction_id;
      $result_xxx_tra = $db_link->query($sql_xxx_tra); 
      if (!$result_xxx_tra) {debug_mail(false,'error sql',$sql_xxx_tra);$ret['message']='sql error'; return $ret;}
      if ($result_xxx_tra->num_rows>=1) {
        $row_xxx_tra=$result_xxx_tra->fetch_assoc();  
        if (empty($row_xxx_tra['PaymentType'])==false and intval($row_xxx_tra['PaymentType'])==1) {
          $pmd_item_is_iris=true;
          //echo '<pre>ssssssssssssss '.$row_xxx_tra['BankId'];die();
        }
      }  
      break;            
    case 4://Cardlink
      $sql_xxx_tra="SELECT cardType from gks_cardlink_transaction 
      where id_cardlink_transaction=".$xxx_transaction_id;
      $result_xxx_tra = $db_link->query($sql_xxx_tra); 
      if (!$result_xxx_tra) {debug_mail(false,'error sql',$sql_xxx_tra);$ret['message']='sql error'; return $ret;}
      if ($result_xxx_tra->num_rows>=1) {
        $row_xxx_tra=$result_xxx_tra->fetch_assoc();  
        if (empty($row_xxx_tra['cardType'])==false and trim_gks($row_xxx_tra['cardType'])=='IRIS') {
          $pmd_item_is_iris=true;
          //echo '<pre>ssssssssssssss '.$row_xxx_tra['BankId'];die();
        }
      }  
      break;            
    case 5://epay
      $sql_xxx_tra="SELECT PaymentType from gks_epay_transaction 
      where id_epay_transaction=".$xxx_transaction_id;
      $result_xxx_tra = $db_link->query($sql_xxx_tra); 
      if (!$result_xxx_tra) {debug_mail(false,'error sql',$sql_xxx_tra);$ret['message']='sql error'; return $ret;}
      if ($result_xxx_tra->num_rows>=1) {
        $row_xxx_tra=$result_xxx_tra->fetch_assoc();  
        if (empty($row_xxx_tra['PaymentType'])==false and intval($row_xxx_tra['PaymentType'])==1) {
          $pmd_item_is_iris=true;
          //echo '<pre>ssssssssssssss '.$row_xxx_tra['BankId'];die();
        }
      }  
      break;               
    default:
    
      break;
  }  
  
  if ($pmd_item_is_iris==false) return array('success' => true, 'message' => 'OK');
  

  $sql_temp="update ".$t_gks_acc_xxx_payment." 
  set payment_acquirer_via='IRIS'
  where ".$f_id_acc_xxx_payment."=".$value_acc_xxx_payment_id;
  $result_temp = $db_link->query($sql_temp);        
  if (!$result_temp) {
    debug_mail(false,'error sql',$sql_temp);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}

  $sql_temp="update gks_eftpos_transaction
  set payment_acquirer_via='IRIS'
  where id_eftpos_transaction=".$id_eftpos_transaction;
  $result_temp = $db_link->query($sql_temp);   
  if (!$result_temp) {
    debug_mail(false,'error sql',$sql_temp);
    return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
            
  if ($t_gks_acc_xxx_payment=='gks_acc_inv_payment') {
    $sql_temp="select acc_inv_id from gks_acc_inv_payment
    where id_acc_inv_payment=".$value_acc_xxx_payment_id;
    $result_temp = $db_link->query($sql_temp);        
    if (!$result_temp) {
      debug_mail(false,'error sql',$sql_temp);
      return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
    if ($result_temp->num_rows==1) {
      $row_temp = $result_temp->fetch_assoc();
      $acc_inv_id=intval($row_temp['acc_inv_id']);
      if ($acc_inv_id>0) {
        $sql_temp="select tropos_pliromis,
        tropos_pliromis_one_multi,
        tropos_pliromis_via 
        from gks_acc_inv
        where id_acc_inv=".$acc_inv_id;
        $result_temp = $db_link->query($sql_temp);        
        if (!$result_temp) {
          debug_mail(false,'error sql',$sql_temp);
          return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
        if ($result_temp->num_rows==1) {
          $row_temp = $result_temp->fetch_assoc();
          $tropos_pliromis=intval($row_temp['tropos_pliromis']);
          $tropos_pliromis_one_multi=intval($row_temp['tropos_pliromis_one_multi']);
          $tropos_pliromis_via=trim_gks($row_temp['tropos_pliromis_via']);
          if ($tropos_pliromis_via=='') {
            $temp=[];
          } else {
            $temp=explode(',',$tropos_pliromis_via);
          }
          if (in_array('IRIS',$temp)==false) $temp[]='IRIS';
          $tropos_pliromis_via=implode(',',$temp);
          $sql_temp="update gks_acc_inv set 
          tropos_pliromis_via='".$db_link->escape_string($tropos_pliromis_via)."'
          where id_acc_inv=".$acc_inv_id;
          $result_temp = $db_link->query($sql_temp);        
          if (!$result_temp) {
            debug_mail(false,'error sql',$sql_temp);
            return array('success' => false, 'message' => 'sql error', 'status' => 'sql error');}
        }
      }
    }
  }
  //die($payment_acquirer_with_id.'|'.$pmd_item_is_iris.'|');
  return array('success' => true, 'message' => 'OK !!!');  
}


include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eftpos_viva.php');    //1
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eftpos_megeftpos.php');    //1
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eftpos_mellon.php');  //3
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eftpos_cardlink.php');//4
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eftpos_epay.php');//5
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eftpos_worldline.php');//6
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eftpos_nexi.php');//7
