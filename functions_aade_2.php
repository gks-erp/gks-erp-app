<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_aade_invoice($id,$force_options=array()) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $ret = array('success' => false, 'message' => 'generic error');
  $ret['mydata_live']=0;

  $doc_table='';
  if (isset($force_options['doc_table'])) $doc_table=$force_options['doc_table'];
  
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
    $ret['message']='not ready yet, doc_table error '.$doc_table;debug_mail(false,$ret['message'],''); return $ret;
  }
  
  $sql="SELECT aade_sending from ".$doc_table." where id_".$ttt."=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {
    $ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό');
    debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();  
  $aade_sending=trim_gks($row['aade_sending']);

  if ($aade_sending!='') {
    $aade_sending=unserialize($aade_sending);
    if (isset($aade_sending['time']) and isset($aade_sending['id']) and $aade_sending['id']==$id) {
      $diafora=time() - intval($aade_sending['time']);
      
      if ($diafora <= 3*60) { //3 lepta
        //echo '<pre>qqqqqqqqqqqq '.$diafora.' ||| '.print_r($aade_sending,true);die();
        
        $ret['message']=gks_lang('Υπάρχει ήδη σε εξέλιση αυτή η διαδικασία εδώ και <b>[1] δευτερόλεπτα</b>');
        $ret['message']=str_replace('[1]',$diafora,$ret['message']);
        $ret['message'].='<br>';
        $ret['message'].=gks_lang('Κάντε ανανέωση της σελίδας για να δείτε το αποτέλεσμα');
        $ret['message'].='<br>';
        $ret['message'].=gks_lang('Εάν σε 3 λεπτά δεν έχει τελειώσει τότε μπορείτε να ξαναδοκιμάσετε με την αποστολή');
        debug_mail(false,'aade_sending mydata '.$id,$ret['message']); 
        return $ret;
      }
    }
  }
  
  $aade_sending=array(
    'id' => $id,
    'time' => time(),
    'user' => $my_wp_user_id,
    'ip' => $gkIP,
    'aade' => 1,
    'paroxos' => 0,
    'force_options' => $force_options,
  );
  $aade_sending=serialize($aade_sending);
  
  

  
  
  $sql_start="update ".$doc_table." set aade_errors='',aade_sending='".$db_link->escape_string($aade_sending)."' where id_".$ttt."=".$id." limit 1";
  $result_start = $db_link->query($sql_start);        
  if (!$result_start) {debug_mail(false,'error sql',$sql_start);$ret['message']='sql error'; return $ret;}
  //echo '<pre>sssssssssssss '.$aade_sending;die();
  
  //sleep(10);  
  $ret = gks_aade_invoice_run($id,$force_options);


    
  $sql_start="update ".$doc_table." set aade_sending=null where id_".$ttt."=".$id." limit 1";
  $result_start = $db_link->query($sql_start);        
  if (!$result_start) {debug_mail(false,'error sql',$sql_start);}

  
  return $ret;
}




  

function gks_aade_invoice_run($id,$force_options) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
    
  $ret = array('success' => false, 'message' => 'generic error');
  $ret['mydata_live']=0;

  $doc_table='';
  if (isset($force_options['doc_table'])) $doc_table=$force_options['doc_table'];
  
  
  //echo '<pre>'.$doc_table;die();
  
  if ($doc_table=='gks_acc_inv') {
    $sql="SELECT gks_acc_inv.aade_invoiceuid, gks_acc_inv.aade_invoicemark, gks_acc_inv.aade_statuscode, gks_acc_inv.aade_send_date, 
    id_company,    gks_acc_inv.company_id,    company_title,
    id_company_sub,gks_acc_inv.company_sub_id,company_sub_title,
    aade_send,     aade_branch,     aade_mydata_user_id,     aade_mydata_subscription_key,     aade_mydata_live, 
    aade_send_sub, aade_branch_sub, aade_mydata_user_id_sub, aade_mydata_subscription_key_sub, aade_mydata_live_sub,
    gks_acc_inv.cancel_for_acc_inv_id,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.is_xeirografi,
    gks_acc_seires.send_mydata,gks_acc_seires.send_paroxos,gks_acc_seires.aade_lock_send_numbers,
    gks_acc_seires.seira_descr,
    gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou,gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_descr,
    gks_acc_inv.inv_acc_journal_id,
    gks_acc_inv.inv_acc_seira_id,
    gks_acc_inv.inv_acc_number_int
    
    FROM ((((gks_acc_inv 
    LEFT JOIN gks_company ON gks_acc_inv.company_id = gks_company.id_company) 
    LEFT JOIN gks_company_subs ON gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE gks_acc_inv.id_acc_inv=".$id;
    $xxx='inv';
    $ttt='acc_inv';
    $rrr='inv_acc';
  } else if ($doc_table=='gks_acc_pay') {
    $sql="SELECT gks_acc_pay.aade_invoiceuid, gks_acc_pay.aade_invoicemark, gks_acc_pay.aade_statuscode, gks_acc_pay.aade_send_date, 
    id_company,    gks_acc_pay.company_id,    company_title,
    id_company_sub,gks_acc_pay.company_sub_id,company_sub_title,
    aade_send,     aade_branch,     aade_mydata_user_id,     aade_mydata_subscription_key,     aade_mydata_live, 
    aade_send_sub, aade_branch_sub, aade_mydata_user_id_sub, aade_mydata_subscription_key_sub, aade_mydata_live_sub,
    gks_acc_pay.cancel_for_acc_pay_id,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.is_xeirografi,
    gks_acc_seires.send_mydata,gks_acc_seires.send_paroxos,gks_acc_seires.aade_lock_send_numbers,
    gks_acc_seires.seira_descr,
    gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou,
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
    gks_acc_eidi_parastatikon.eidos_parastatikou_descr,
    gks_acc_pay.pay_acc_journal_id,
    gks_acc_pay.pay_acc_seira_id,
    gks_acc_pay.pay_acc_number_int
    
    FROM ((((gks_acc_pay 
    LEFT JOIN gks_company ON gks_acc_pay.company_id = gks_company.id_company) 
    LEFT JOIN gks_company_subs ON gks_acc_pay.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE gks_acc_pay.id_acc_pay=".$id;    
    $xxx='pay';
    $ttt='acc_pay';
    $rrr='pay_acc';
  } else if ($doc_table=='gks_whi_mov') {
    $sql="SELECT gks_whi_mov.aade_invoiceuid, gks_whi_mov.aade_invoicemark, gks_whi_mov.aade_statuscode, gks_whi_mov.aade_send_date, 
    id_company,    gks_whi_mov.company_id,    company_title,
    id_company_sub,gks_whi_mov.company_sub_id,company_sub_title,
    aade_send,     aade_branch,     aade_mydata_user_id,     aade_mydata_subscription_key,     aade_mydata_live, 
    aade_send_sub, aade_branch_sub, aade_mydata_user_id_sub, aade_mydata_subscription_key_sub, aade_mydata_live_sub,
    gks_whi_mov.cancel_for_whi_mov_id,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.is_xeirografi,
    gks_acc_seires.send_mydata,gks_acc_seires.send_paroxos,gks_acc_seires.aade_lock_send_numbers,
    gks_acc_seires.seira_descr,
    gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou,gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_descr,
    gks_whi_mov.mov_whi_journal_id,
    gks_whi_mov.mov_whi_seira_id,
    gks_whi_mov.mov_whi_number_int
    
    FROM ((((gks_whi_mov 
    LEFT JOIN gks_company ON gks_whi_mov.company_id = gks_company.id_company) 
    LEFT JOIN gks_company_subs ON gks_whi_mov.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE gks_whi_mov.id_whi_mov=".$id;
    $xxx='mov';
    $ttt='whi_mov'; 
    $rrr='mov_whi';
  } else {
    $ret['message']='not ready yet, doc_table error '.$doc_table;debug_mail(false,$ret['message'],''); return $ret;
  }


  
  //echo '<pre>ggggg '.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {
    $ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό');
    debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();  
  if ($row['company_id'] <= 0 or isset($row['id_company'])==false) {
    $ret['message']=gks_lang('Δεν έχει ορισθεί η εταιρεία');
    debug_mail(false,$ret['message'],$sql); return $ret;}
  if ($row['company_sub_id'] > 0 and isset($row['id_company_sub'])==false) {
    $ret['message']=gks_lang('Δεν έχει ορισθεί το υποκατάστημα');
    debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $is_xeirografi=intval($row['is_xeirografi']);
  $send_mydata=intval($row['send_mydata']);
  if ($send_mydata==0) {
    $ret['message']=gks_lang('Αυτή η σειρά δεν έχει ρυθμιστεί για αποστολή στο myData');
    debug_mail(false,$ret['message'],$sql); return $ret;}
  $id_acc_eidos_parastatikou=trim_gks($row['id_acc_eidos_parastatikou']);
  $eidos_parastatikou_aade_code=trim_gks($row['eidos_parastatikou_aade_code']);
  if ($eidos_parastatikou_aade_code=='' and in_array($id_acc_eidos_parastatikou,[702,703,704])==false) {
    $ret['message']=gks_lang('Τα ημερολόγια τύπου <b>[1]</b> δεν μπορούν να αποσταλούν στο myData');
    $ret['message']=str_replace('[1]',trim_gks($row['eidos_parastatikou_descr']),$ret['message']);
    debug_mail(false,$ret['message'],$sql); return $ret;}
    
  
  $cancel_for_ttt_id=$row['cancel_for_'.$ttt.'_id'];
  //echo '<pre>'.$cancel_for_ttt_id;die();
  

  
  
  
  $aade_params=array();
  if ($row['company_sub_id']==0) { //kentriko
    $aade_params['send']=intval($row['aade_send']);
    $aade_params['branch']=intval($row['aade_branch']);
    $aade_params['mydata_user_id']=trim_gks($row['aade_mydata_user_id']);
    $aade_params['mydata_subscription_key']=trim_gks($row['aade_mydata_subscription_key']);
    $aade_params['mydata_live']=intval($row['aade_mydata_live']);
  } else {
    $aade_params['send']=intval($row['aade_send_sub']);
    $aade_params['branch']=intval($row['aade_branch_sub']);
    $aade_params['mydata_user_id']=trim_gks($row['aade_mydata_user_id_sub']);
    $aade_params['mydata_subscription_key']=trim_gks($row['aade_mydata_subscription_key_sub']);
    $aade_params['mydata_live']=intval($row['aade_mydata_live_sub']);
  }
  
  if (is_array($force_options)) {
    if (isset($force_options['aade_mydata_sender_afm']))       $aade_params['force_afm'] = $force_options['aade_mydata_sender_afm'];
    if (isset($force_options['aade_mydata_live']))             $aade_params['mydata_live'] = $force_options['aade_mydata_live'];
    if (isset($force_options['aade_branch']))                  $aade_params['branch']=$force_options['aade_branch'];
    if (isset($force_options['aade_mydata_user_id']))          $aade_params['mydata_user_id']=$force_options['aade_mydata_user_id'];
    if (isset($force_options['aade_mydata_subscription_key'])) $aade_params['mydata_subscription_key']=  $force_options['aade_mydata_subscription_key'];
  }
  
  //print '<pre>';print_r($aade_params);die();
  //aade_mydata_live
  $ret['mydata_live']=$aade_params['mydata_live'];
  
  if ($aade_params['mydata_live']==1) {
    $acc_journal_descr=trim_gks($row['acc_journal_descr']);
    $seira_descr=trim_gks($row['seira_descr']);
    $rrr_number_int=intval($row[$rrr.'_number_int']);
    $rrr_journal_id=intval($row[$rrr.'_journal_id']);
    $rrr_seira_id=intval($row[$rrr.'_seira_id']);
    $aade_lock_send_numbers=intval($row['aade_lock_send_numbers']);
    
    
    if ($aade_lock_send_numbers!=0) {
      $sql_prev_number="SELECT Count(id_".$ttt.") AS cc
      FROM ".$doc_table."
      WHERE aade_send_date is not null
      AND ".$rrr."_journal_id=".$rrr_journal_id."
      AND ".$rrr."_seira_id=".$rrr_seira_id." 
      AND company_id=".$row['company_id']." 
      AND company_sub_id=".$row['company_sub_id'];
      //echo '<pre>bbbbbbb '.$sql_prev_number;die();
      $result_prev_number = $db_link->query($sql_prev_number);        
      if (!$result_prev_number) {debug_mail(false,'error sql',$sql_prev_number);$ret['message']='sql error'; return $ret;}
      $seira_recs=0;
      if ($result_prev_number->num_rows>=1) {
        $row_prev_number = $result_prev_number->fetch_assoc();
        $seira_recs=$row_prev_number['cc'];
      }
      //echo '<pre>bbbbbbbvvv '.$seira_recs;die();
      if ($seira_recs > 0) { //den einai i proti apostoli, ara tha prepei na iparxei na ;exei apostalei o proigoumenos arithmos
  
        $sql_prev_number="SELECT id_".$ttt."
        FROM ".$doc_table."
        WHERE aade_send_date is not null
        AND ".$rrr."_journal_id=".$rrr_journal_id."
        AND ".$rrr."_seira_id=".$rrr_seira_id." 
        AND company_id=".$row['company_id']." 
        AND company_sub_id=".$row['company_sub_id']."
        AND ".$rrr."_number_int=".($rrr_number_int - 1);
        //echo '<pre>ssssssss '.$sql_prev_number;die();
        $result_prev_number = $db_link->query($sql_prev_number);        
        if (!$result_prev_number) {debug_mail(false,'error sql',$sql_prev_number);$ret['message']='sql error'; return $ret;}
        
        if ($result_prev_number->num_rows==0) {
          $ret['message']=gks_lang('Το παραστατικό του ίδιου ημερολογίου (<b>[1]</b>),<br>της ίδιας σειράς (<b>[2]</b>),<br>με τον προηγούμενο αριθμό (<b>[2]</b>)<br>δεν έχει αποσταλεί στην ΑΑΔΕ με myDATA.<br>Στείλτε πρώτα το προηγούμενο παραστατικό');
          $ret['message']=str_replace('[1]',$acc_journal_descr,$ret['message']);
          $ret['message']=str_replace('[2]',$seira_descr,$ret['message']);
          $ret['message']=str_replace('[3]',($rrr_number_int-1),$ret['message']);
          debug_mail(false,$ret['message'],$sql); return $ret;
        }
      }
    }

    
    if ($aade_params['send']==0) {
      if ($row['company_sub_id']==0) { //kentriko
        $ret['message']=gks_lang('Δεν είναι ενεργή η <b>Αποστολή δεδομένων στην ΑΑΔΕ με myDATA</b> για την εταιρεία <b>[1]</b>');
        $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      } else {
        $ret['message']=gks_lang('Δεν είναι ενεργή η <b>Αποστολή δεδομένων στην ΑΑΔΕ με myDATA</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b>');
        $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
        $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      }    
    }
    if ($aade_params['branch']<0) {
      if ($row['company_sub_id']==0) { //kentriko
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο αριμός <b>Εγκατάστασης</b> για την εταιρεία <b>[1]</b> θα πρέπει να είναι μεγαλύτερος ή ίσος με μηδέν');
        $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      } else {
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο αριμός <b>Εγκατάστασης</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> θα πρέπει να είναι μεγαλύτερος ή ίσος με μηδέν');
        $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
        $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      }    
    }
    if ($aade_params['mydata_user_id']=='') {
      if ($row['company_sub_id']==0) { //kentriko
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      } else {
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, τo <b>Όνομα Χρήστη</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
        $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      }    
    }
    if ($aade_params['mydata_subscription_key']=='') {
      if ($row['company_sub_id']==0) { //kentriko
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      } else {
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων στην ΑΑΔΕ με myDATA, ο <b>Κωδικός API</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
        $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      }
    }
    
  
    
    //$ret['message']='<pre>'.print_r($aade_params,true);return $ret;
    
    
    //if (empty($row['aade_invoiceuid'])==false or empty($row['aade_invoicemark'])==false or empty($row['aade_statuscode'])==false or isset($row['aade_send_date'])) {
    if (trim_gks($row['aade_statuscode'])=='Success' and isset($row['aade_send_date'])) {
      $ret['message']=gks_lang('Το παραστατικό έχει ήδη αποσταλεί στη ΑΑΔΕ').'<br>'.
      gks_lang('Το ΜΑΡΚ του παραστατικού είναι').': <b>'.$row['aade_invoicemark'].'</b>';
      debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;
    }
  }

  
  //echo '<pre>sssssssssss '.$cancel_for_ttt_id;die();
  
  if ($cancel_for_ttt_id==0) {
    $ret_xml = gks_aade_invoice_xml_create($id,$doc_table,$aade_params);
    

    
    
    if ($ret_xml['success'] == false) {$ret['message']=$ret_xml['message'];debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;}
    //$ret['message']='<pre>'.print_r($ret_xml,true);return $ret;
    
    $ret_send = gks_aade_invoice_xml_send($id,$ret_xml['out_xml'],$aade_params);
    if ($ret_send['success'] == false) {$ret['message']=$ret_send['message'];debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;}
    //$ret['message']='<pre>'.print_r($ret_send,true);return $ret;

    $ret_parse=gks_aade_invoice_xml_parse_response($ret_send['out_xml']);
    //$ret['message']='<pre>'.print_r($ret_parse,true);return $ret;
  
    
  } else {
    $sql_cancel="select * from ".$doc_table." where id_".$ttt."=".$cancel_for_ttt_id;
    $result_cancel = $db_link->query($sql_cancel);        
    if (!$result_cancel) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result_cancel->num_rows!=1) {
      $ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό αναφοράς για ακύρωση');
      debug_mail(false,$ret['message'],$sql); return $ret;}
    $row_cancel = $result_cancel->fetch_assoc();  
    $aade_invoicemark=trim_gks($row_cancel['aade_invoicemark']);
    if (strlen($aade_invoicemark)<5 or ctype_digit($aade_invoicemark)==false) {
      $ret['message']=gks_lang('To MARK του παραστατικού προς ακύρωση δεν είναι σωστό').':<br><b>'.$aade_invoicemark.'</b>';
      debug_mail(false,$ret['message'],$sql); return $ret;}
      
      
    
    $ret_send = gks_aade_invoice_cancel_send($id,$aade_invoicemark,$aade_params);
    if ($ret_send['success'] == false) {$ret['message']=$ret_send['message'];debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;}
    //$ret['message']='<pre>'.print_r($ret_send,true);return $ret;

    $ret_parse=gks_aade_invoice_cancel_xml_parse_response($ret_send['out_xml']);
    //$ret['message']='<pre>'.print_r($ret_parse,true);return $ret;
    
    //die('<pre>δ'.$cancel_for_acc_inv_id.' '.$aade_invoicemark);
    
  }
   

  
  

  
  if ($aade_params['mydata_live']==1) {
    if ($ret_parse['statusCode']=='Success') {
      
      if (isset($ret_parse['qrurl'])==false) $ret_parse['qrurl']='';
      
      $sql_update="update ".$doc_table." set 
      aade_statuscode='".$db_link->escape_string($ret_parse['statusCode'])."',
      aade_invoiceuid='".$db_link->escape_string($ret_parse['invoiceUid'])."',
      aade_invoicemark='".$db_link->escape_string($ret_parse['invoiceMark'])."',
      aade_qrurl='".$db_link->escape_string($ret_parse['qrurl'])."',
      
      aade_errors='".$db_link->escape_string($ret_parse['errors_human'])."',
      aade_send_date=now(),
      aade_user_id=".$my_wp_user_id."
      where id_".$ttt."=".$id." limit 1";
      
      gks_aade_update_mark_from_id(['mark'=>$ret_parse['invoiceMark'],$ttt.'_id'=>$id]);

    } else { // ValidationError, TechnicalError, XMLSyntaxError
      $sql_update="update ".$doc_table." set 
      aade_statuscode='".$db_link->escape_string($ret_parse['statusCode'])."',
      aade_errors='".$db_link->escape_string($ret_parse['errors_human'])."'
      where id_".$ttt."=".$id." limit 1";
    }
    $result = $db_link->query($sql_update);        
    if (!$result) {debug_mail(false,'error sql',$sql_update);$ret['message']='sql error'; return $ret;}
    
    
  } else {
    $sql_update="update ".$doc_table." set 
    aade_errors='".$db_link->escape_string($ret_parse['errors_human'])."'
    where id_".$ttt."=".$id." limit 1";
    $result = $db_link->query($sql_update);        
    if (!$result) {debug_mail(false,'error sql',$sql_update);$ret['message']='sql error'; return $ret;}
    
  }
  
  //print '<pre>';print_r($ret_parse);die();
  

  
  if ($cancel_for_ttt_id==0) {  
    $ret_save = gks_aade_invoice_xml_save($id,$ret_xml['out_xml'], '',$doc_table);
    if ($ret_save['success'] == false) {$ret['message']=$ret_save['message'];debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;}
    $filename=$ret_save['filename'];
    
    $filename_r=substr($filename, 0, strlen($filename) - 11).'-2-response.xml';
    
    $ret_respone_save = gks_aade_invoice_xml_save($id,$ret_send['out_xml'], $filename_r,$doc_table);
    if ($ret_respone_save['success'] == false) {$ret['message']=$ret_respone_save['message'];debug_mail(false,$ret['message'],$sql); return $ret;}
    
    
  } else { //cancel invoice
    $filename='';
    $filename_r='invoice_cancel_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999).'-2-response.xml';;

    $ret_respone_save = gks_aade_invoice_xml_save($id,$ret_send['out_xml'], $filename_r,$doc_table);
    if ($ret_respone_save['success'] == false) {$ret['message']=$ret_respone_save['message'];debug_mail(false,$ret['message'],$sql); return $ret;}
    
    
  }


  $sql="update ".$doc_table." set aade_xml_send='".$db_link->escape_string($filename)."', aade_xml_response='".$db_link->escape_string($filename_r)."' where id_".$ttt."=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  
  
  $ret['mydata_live']=$aade_params['mydata_live'];
  
  
  
  if ($ret_parse['statusCode'] != 'Success') {
    
    $ret['message']=$ret_parse['errors_human']; //'<pre>'.$ret_parse['statusCode'].'<br>'.print_r($ret_parse['errors'],true)."\n".print_r($ret_parse['errors_human'],true);
    debug_mail(false,'gks_aade_invoice_xml_parse_response',print_r($ret_parse,true)); 
    return $ret;
    
  }

  $ret['out_xml']=$ret_parse;
  $ret['message']='OK';
  $ret['success']=true;

  //print '<pre>sssssssssss ';print_r($ret);die();
    
  return $ret;
  
}

function gks_aade_update_mark_from_id($params) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;  
  
  $mark='';if (isset($params['mark'])) $mark=trim_gks($params['mark']);
  $acc_inv_id=0;if (isset($params['acc_inv_id'])) $acc_inv_id=intval($params['acc_inv_id']);
  $acc_pay_id=0;if (isset($params['acc_pay_id'])) $acc_pay_id=intval($params['acc_pay_id']);
  $whi_mov_id=0;if (isset($params['whi_mov_id'])) $whi_mov_id=intval($params['whi_mov_id']);
  
  if ($mark=='') return;
  if ($acc_inv_id==0 and $acc_pay_id==0 and $whi_mov_id==0) return;
  $xxx_xx_id=0;
  if ($acc_inv_id>0) {
    $field_where1='coi_acc_inv_id';
    $field_where2='mcm_acc_inv_id';
    $xxx_xx_id=$acc_inv_id;
  } else if ($acc_pay_id>0) {
    $field_where1='coi_acc_pay_id';
    $field_where2='mcm_acc_pay_id';
    $xxx_xx_id=$acc_pay_id;
  } else if ($whi_mov_id>0) {
    $field_where1='coi_whi_mov_id';
    $field_where2='mcm_whi_mov_id';
    $xxx_xx_id=$whi_mov_id;
  } 

  $mytable=['gks_acc_inv_correlated_invoices','gks_acc_pay_correlated_invoices','gks_whi_mov_correlated_invoices'];
  foreach ($mytable as $value) {
    $sql="update ".$value." set 
    coi_mark='".$db_link->escape_string($mark)."'
    where ".$field_where1."=".$xxx_xx_id."
    and (coi_mark is null or coi_mark='')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  } 
  
  $mytable=['gks_acc_inv_multiple_connected_marks','gks_acc_pay_multiple_connected_marks','gks_whi_mov_multiple_connected_marks'];
  foreach ($mytable as $value) {
    $sql="update ".$value." set 
    mcm_mark='".$db_link->escape_string($mark)."'
    where ".$field_where2."=".$xxx_xx_id."
    and (mcm_mark is null or mcm_mark='')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  } 
  
}

function gks_aade_get_mark_from_id($params) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;  

  $acc_inv_id=0;
  if (isset($params['coi_acc_inv_id'])) $acc_inv_id=intval($params['coi_acc_inv_id']);
  if (isset($params['mcm_acc_inv_id'])) $acc_inv_id=intval($params['mcm_acc_inv_id']);
  $acc_pay_id=0;
  if (isset($params['coi_acc_pay_id'])) $acc_pay_id=intval($params['coi_acc_pay_id']);
  if (isset($params['mcm_acc_pay_id'])) $acc_pay_id=intval($params['mcm_acc_pay_id']);
  $whi_mov_id=0;
  if (isset($params['coi_whi_mov_id'])) $whi_mov_id=intval($params['coi_whi_mov_id']);
  if (isset($params['mcm_whi_mov_id'])) $whi_mov_id=intval($params['mcm_whi_mov_id']);


  if ($acc_inv_id==0 and $acc_pay_id==0 and $whi_mov_id==0) return '';
  $xxx_xx_id=0;
  if ($acc_inv_id>0) {
    $mytable='gks_acc_inv';
    $field_where='id_acc_inv';
    $xxx_xx_id=$acc_inv_id;
  } else if ($acc_pay_id>0) {
    $mytable='gks_acc_pay';
    $field_where='id_acc_pay';
    $xxx_xx_id=$acc_pay_id;
  } else if ($whi_mov_id>0) {
    $mytable='gks_whi_mov';
    $field_where='id_whi_mov';
    $xxx_xx_id=$whi_mov_id;
  } else {
    return '';  
  } 

  $sql="select aade_invoicemark from ".$mytable." 
  where ".$field_where."=".$xxx_xx_id."
  and aade_invoicemark<>''";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    return trim_gks($row['aade_invoicemark']);
  }

  return '';
}
