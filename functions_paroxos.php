<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_paroxos_ilyda_com.php');   //8
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_paroxos_tesae_gr.php');    //16
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_paroxos_parochos_gr.php'); //20
 
function gks_paroxos_log($params) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  if (count($params) < 3) return;
  if (isset($params[3])==false) $params[3]='';
  if (isset($params[4])==false) $params[4]='';
  
  
  $sql="insert into gks_company_paroxos_log (
  mydate_add,user_id_add,myip,
  acc_inv_id,acc_pay_id,company_paroxos_id,p_send,p_response
  ) values (
  now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$params[0].",
  ".$params[1].",
  ".$params[2].",
  '".$db_link->escape_string(serialize($params[3]))."',
  '".$db_link->escape_string(serialize($params[4]))."'
  )";
  $result = $db_link->query($sql); 
  
}


function gks_paroxos_loginToSubscription($params) {
	$ret = array('success' => false, 'message' => 'generic error');

  if (isset($params['id_company_paroxos'])==false) {
    $ret['message']=gks_lang('Δεν έχει ορισθεί το id_company_paroxos για αυτήν την εταιρεία'); debug_mail(false,$ret['message']); return $ret;
  }
  if (isset($params['aade_paroxos_id'])==false) {
    $ret['message']=gks_lang('Δεν έχει ορισθεί ο πάροχος'); debug_mail(false,$ret['message']); return $ret;
  }
  
  switch ($params['aade_paroxos_id']) {   
    case 20: //parochos_gr
      $ret=gks_paroxos_loginToSubscription_parochos_gr($params);
      break;
    default:
      $ret['message']=gks_lang('Δεν γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο'); 
      break;
  }

  if ($ret['success']==false) {
    debug_mail(false,'error gks_paroxos_loginToSubscription',$ret['message'].'<br>params: '.print_r($params,true));
    return $ret;    
  }      

  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;  
}

function gks_paroxos_load_params($company_id,$company_sub_id,$force_options) {
  global $db_link;
  global $my_wp_user_id;

  $ret = array('success' => false, 'message' => 'generic error');
    
  $paroxos_params=array();
  if ($company_sub_id==0) { //kentriko
    $sql_paroxos="SELECT gks_company_paroxos.*, 
    gks_aade_paroxos.paroxos_need_username, 
    gks_aade_paroxos.paroxos_need_password, 
    gks_aade_paroxos.paroxos_need_key
    FROM gks_company_paroxos 
    LEFT JOIN gks_aade_paroxos ON gks_company_paroxos.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos
    where gks_company_paroxos.paroxos_send=1 and gks_company_paroxos.company_id=".$company_id;
    $result_paroxos = $db_link->query($sql_paroxos); 
    if (!$result_paroxos) {debug_mail(false,'error sql',$sql_paroxos);$ret['message']='sql error'; return $ret;}
    if ($result_paroxos->num_rows==0) {
      $ret['message']=gks_lang('Αυτή η εταιρεία δεν έχει ορισθεί για αποστολή σε πάροχο');debug_mail(false,$ret['message'],$sql); return $ret;}

    $row_paroxos = $result_paroxos->fetch_assoc();
    $paroxos_params['id_company_paroxos']=intval($row_paroxos['id_company_paroxos']);
    $paroxos_params['aade_paroxos_id']=intval($row_paroxos['aade_paroxos_id']);
    $paroxos_params['paroxos_send']=intval($row_paroxos['paroxos_send']);
    $paroxos_params['paroxos_mydata_live']=intval($row_paroxos['paroxos_mydata_live'])==1;
    if (is_array($force_options)) {
      if (isset($force_options['paroxos_mydata_live'])) $paroxos_params['paroxos_mydata_live'] = $force_options['paroxos_mydata_live'];
    }
      
    $sandbox =''; if ($paroxos_params['paroxos_mydata_live']==false) $sandbox='sandbox_';
    $paroxos_params['paroxos_branch']=intval($row_paroxos['paroxos_branch']);
    $paroxos_params['pc_username']=trim_gks($row_paroxos[$sandbox.'pc_username']);
    $paroxos_params['pc_password']=trim_gks($row_paroxos[$sandbox.'pc_password']);
    $paroxos_params['pc_key']=trim_gks($row_paroxos[$sandbox.'pc_key']);
    $paroxos_params['paroxos_need_username']=intval($row_paroxos['paroxos_need_username'])==1;
    $paroxos_params['paroxos_need_password']=intval($row_paroxos['paroxos_need_password'])==1;
    $paroxos_params['paroxos_need_key']=intval($row_paroxos['paroxos_need_key'])==1;     
  } else {
    $sql_paroxos="SELECT gks_company_paroxos.*, 
    gks_aade_paroxos.paroxos_need_username, 
    gks_aade_paroxos.paroxos_need_password, 
    gks_aade_paroxos.paroxos_need_key
    FROM gks_company_paroxos 
    LEFT JOIN gks_aade_paroxos ON gks_company_paroxos.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos
    where gks_company_paroxos.paroxos_send=1 and gks_company_paroxos.company_sub_id=".$company_sub_id;
    $result_paroxos = $db_link->query($sql_paroxos); 
    if (!$result_paroxos) {debug_mail(false,'error sql',$sql_paroxos);$ret['message']='sql error'; return $ret;}
    if ($result_paroxos->num_rows==0) {
      $ret['message']=gks_lang('Αυτό το υποκατάστημα δεν έχει ορισθεί για αποστολή σε πάροχο');debug_mail(false,$ret['message'],$sql); return $ret;}

    $row_paroxos = $result_paroxos->fetch_assoc();
    $paroxos_params['id_company_paroxos']=intval($row_paroxos['id_company_paroxos']);
    $paroxos_params['aade_paroxos_id']=intval($row_paroxos['aade_paroxos_id']);
    $paroxos_params['paroxos_send']=intval($row_paroxos['paroxos_send']);
    $paroxos_params['paroxos_mydata_live']=intval($row_paroxos['paroxos_mydata_live'])==1;
    if (is_array($force_options)) {
      if (isset($force_options['paroxos_mydata_live'])) $paroxos_params['paroxos_mydata_live'] = $force_options['paroxos_mydata_live'];
    }
    $sandbox =''; if ($paroxos_params['paroxos_mydata_live']==false) $sandbox='sandbox_';
    $paroxos_params['paroxos_branch']=intval($row_paroxos['paroxos_branch']);
    $paroxos_params['pc_username']=trim_gks($row_paroxos[$sandbox.'pc_username']);
    $paroxos_params['pc_password']=trim_gks($row_paroxos[$sandbox.'pc_password']);
    $paroxos_params['pc_key']=trim_gks($row_paroxos[$sandbox.'pc_key']);
    $paroxos_params['paroxos_need_username']=intval($row_paroxos['paroxos_need_username'])==1;
    $paroxos_params['paroxos_need_password']=intval($row_paroxos['paroxos_need_password'])==1;
    $paroxos_params['paroxos_need_key']=intval($row_paroxos['paroxos_need_key'])==1;     
    
  }  
  
  $ret['paroxos_params']=$paroxos_params;
  $ret['message']='OK';
  $ret['success']=true;
  
  return $ret;
    
}

function gks_paroxos_invoice($id,$force_options=array()) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  //echo 444/0;die();
  
  $ret = array('success' => false, 'message' => 'generic error');
  $ret['paroxos_mydata_live']=false;

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
    $ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό');debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();  
  $aade_sending=trim_gks($row['aade_sending']);

  if ($aade_sending!='') {
    $aade_sending=unserialize($aade_sending);
    if (isset($aade_sending['time']) and isset($aade_sending['id']) and $aade_sending['id']==$id) {
      $diafora=time() - intval($aade_sending['time']);
      
      if ($diafora <= 3*60) { //3 lepta
        //echo '<pre>qqqqqqqqqqqq '.$diafora.' ||| '.print_r($aade_sending,true);die();
        
        $ret['message']=gks_lang('Υπάρχει ήδη σε εξέλιση αυτή η διαδικασία εδώ και <b>[1] δευτερόλεπτα</b>').'<br>'.
        gks_lang('Κάντε ανανέωση της σελίδας για να δείτε το αποτέλεσμα').'<br>'.
        gks_lang('Εάν σε 3 λεπτά δεν έχει τελειώσει τότε μπορείτε να ξαναδοκιμάσετε με την αποστολή');
        $ret['message']=str_replace('[1]',$diafora,$ret['message']);
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
    'aade' => 0,
    'paroxos' => 1,
    'force_options' => $force_options,
  );
  $aade_sending=serialize($aade_sending);
  

  
  
  $sql_start="update ".$doc_table." set aade_errors='',aade_sending='".$db_link->escape_string($aade_sending)."' where id_".$ttt."=".$id." limit 1";
  $result_start = $db_link->query($sql_start);        
  if (!$result_start) {debug_mail(false,'error sql',$sql_start);$ret['message']='sql error'; return $ret;}
  //echo '<pre>sssssssssssss '.$aade_sending;die();
    
  //sleep(20);
  $ret = gks_paroxos_invoice_run($id,$force_options);

    
  $sql_start="update ".$doc_table." set aade_sending=null where id_".$ttt."=".$id." limit 1";
  $result_start = $db_link->query($sql_start);        
  if (!$result_start) {debug_mail(false,'error sql',$sql_start);}

  if ($ret['success']) {
    gks_plugins_functions_run('functions_paroxos_invoice_after',array(
      'id'=>&$id,
      'doc_table' => &$doc_table,
    ));  
  }
  
  
  return $ret;
}

function gks_paroxos_invoice_run($id,$force_options) {
  global $db_link;
  global $my_wp_user_id;
    
  
  
  $ret = array('success' => false, 'message' => 'generic error');
  $ret['paroxos_mydata_live']=false;
  
  $doc_table='';
  if (isset($force_options['doc_table'])) $doc_table=$force_options['doc_table'];

  //echo '<pre>'.$doc_table;die();
  if ($doc_table=='gks_acc_inv') {
    $sql="SELECT gks_acc_inv.aade_invoiceuid, gks_acc_inv.aade_invoicemark, gks_acc_inv.aade_statuscode, gks_acc_inv.aade_send_date, 
    id_company,    gks_acc_inv.company_id,    company_title,
    id_company_sub,gks_acc_inv.company_sub_id,company_sub_title,
    gks_acc_inv.cancel_for_acc_inv_id,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.is_xeirografi,
    gks_acc_seires.send_mydata,gks_acc_seires.send_paroxos,gks_acc_seires.aade_lock_send_numbers,
    gks_acc_seires.seira_descr,
    gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou,
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
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
    gks_whi_mov.cancel_for_whi_mov_id,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.is_xeirografi,
    gks_acc_seires.send_mydata,gks_acc_seires.send_paroxos,gks_acc_seires.aade_lock_send_numbers,
    gks_acc_seires.seira_descr,
    gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou,
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
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
    $xxx='';
  }
  
  


  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql 111',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {
    $ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό');
    debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();  
  if ($row['company_id'] <= 0 or isset($row['id_company'])==false) {
    $ret['message']=gks_lang('Δεν έχει ορισθεί η εταιρεία');debug_mail(false,$ret['message'],$sql); return $ret;}
  if ($row['company_sub_id'] > 0 and isset($row['id_company_sub'])==false) {
    $ret['message']=gks_lang('Δεν έχει ορισθεί το υποκατάστημα');debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $is_xeirografi=intval($row['is_xeirografi']);
  $id_acc_eidos_parastatikou=trim_gks($row['id_acc_eidos_parastatikou']);
  $eidos_parastatikou_aade_code=trim_gks($row['eidos_parastatikou_aade_code']);
  if ($eidos_parastatikou_aade_code=='' and in_array($id_acc_eidos_parastatikou,[702,703,704])==false) {
    $ret['message']=str_replace('[1]',trim_gks($row['eidos_parastatikou_descr']),gks_lang('Τα ημερολόγια τύπου <b>[1]</b> δεν μπορούν να αποσταλούν στο myData'));
    debug_mail(false,$ret['message'],$sql); return $ret;}
  $cancel_for_acc_xxx_id=$row['cancel_for_'.$ttt.'_id'];
  $eidos_parastatikou_type_id=intval($row['eidos_parastatikou_type_id']);
  


  $ret_params=gks_paroxos_load_params($row['company_id'],$row['company_sub_id'],$force_options);
  if ($ret_params['success']==false) {$ret['message']=$ret_params['message']; debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $paroxos_params=$ret_params['paroxos_params'];
  //print '<pre>';print_r($paroxos_params);die();
  
  
  $send_paroxos=intval($row['send_paroxos']); //apo seira, oxi apo eteria
  if ($send_paroxos==0) {
    $ret['message']=gks_lang('Αυτή η σειρά δεν έχει ρυθμιστεί για αποστολή σε πάροχο');debug_mail(false,$ret['message'],$sql); return $ret;}
    
  
  

  

  
  //print '<pre>';print_r($paroxos_params);die();
  //aade_mydata_live
  $ret['paroxos_mydata_live']=$paroxos_params['paroxos_mydata_live'];


  
  
  if ($paroxos_params['paroxos_mydata_live']) {
    $acc_journal_descr=trim_gks($row['acc_journal_descr']);
    $seira_descr=trim_gks($row['seira_descr']);
    $rrr_number_int=intval($row[$rrr.'_number_int']);
    $rrr_journal_id=intval($row[$rrr.'_journal_id']);
    $rrr_seira_id=intval($row[$rrr.'_seira_id']);
    $aade_lock_send_numbers=intval($row['aade_lock_send_numbers']);
    
    //print '<pre>paroxos_paramssss ';print_r($paroxos_params);die();
    
    if ($aade_lock_send_numbers!=0) {
      //echo '<pre>fffffffffffffffff2';die();
      $sql_prev_number="SELECT Count(id_".$ttt.") AS cc
      FROM ".$doc_table."
      WHERE aade_send_date is not null
      AND ".$rrr."_journal_id=".$rrr_journal_id."
      AND ".$rrr."_seira_id=".$rrr_seira_id." 
      AND company_id=".$row['company_id']." 
      AND company_sub_id=".$row['company_sub_id'];
      //echo '<pre>'.$sql_prev_number;die();
      $result_prev_number = $db_link->query($sql_prev_number);        
      if (!$result_prev_number) {debug_mail(false,'error sql',$sql_prev_number);$ret['message']='sql error'; return $ret;}
      $seira_recs=0;
      if ($result_prev_number->num_rows>=1) {
        $row_prev_number = $result_prev_number->fetch_assoc();
        $seira_recs=$row_prev_number['cc'];
      }
      if ($seira_recs > 0) { //den einai i proti apostoli, ara tha prepei na iparxei na ;exei apostalei o proigoumenos arithmos
  
        $sql_prev_number="SELECT id_".$ttt.",aade_send_date,paroxos_tf1_url
        FROM ".$doc_table."
        WHERE company_id=".$row['company_id']." 
        AND company_sub_id=".$row['company_sub_id']."
        AND ".$rrr."_journal_id=".$rrr_journal_id."
        AND ".$rrr."_seira_id=".$rrr_seira_id." 
        AND ".$rrr."_number_int=".($rrr_number_int - 1);
        //echo '<pre>'.$sql_prev_number;die();
        $result_prev_number = $db_link->query($sql_prev_number);        
        if (!$result_prev_number) {debug_mail(false,'error sql',$sql_prev_number);$ret['message']='sql error'; return $ret;}
        
        //aade_send_date is not null
        if ($result_prev_number->num_rows>=1) {
          $row_prev_number = $result_prev_number->fetch_assoc(); 
          $paroxos_tf1_url=$row_prev_number['paroxos_tf1_url'];
          $aade_send_date=$row_prev_number['aade_send_date'];
          
          if ($aade_send_date=='' and $paroxos_tf1_url=='') {
            $ret['message']=gks_lang('Το παραστατικό του ίδιου ημερολογίου (<b>[1]</b>),<br>της ίδιας σειράς (<b>[2]</b>),<br>με τον προηγούμενο αριθμό (<b>[3]</b>)<br>δεν έχει αποσταλεί στον πάροχο.<br>Στείλτε πρώτα το προηγούμενο παραστατικό');
            $ret['message']=str_replace('[1]',$acc_journal_descr,$ret['message']);
            $ret['message']=str_replace('[2]',$seira_descr,$ret['message']);
            $ret['message']=str_replace('[3]',($rrr_number_int-1),$ret['message']);            
            debug_mail(false,$ret['message'],$sql); return $ret;
          }
        }
      }
    }

    
    if ($paroxos_params['paroxos_send']==0) {
      if ($row['company_sub_id']==0) { //kentriko
        $ret['message']=gks_lang('Δεν είναι ενεργή η <b>Αποστολή δεδομένων σε πάροχο</b> για την εταιρεία <b>[1]</b>');
        $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      } else {
        $ret['message']=gks_lang('Δεν είναι ενεργή η <b>Αποστολή δεδομένων σε πάροχο</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b>');
        $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
        $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      }    
    }

    if ($paroxos_params['paroxos_need_username'] and $paroxos_params['pc_username']=='') {
      if ($row['company_sub_id']==0) { //kentriko
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων σε πάροχο, τo <b>Όνομα Χρήστη</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      } else {
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων σε πάροχο, τo <b>Όνομα Χρήστη</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
        $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);        
        debug_mail(false,$ret['message'],$sql); return $ret;
      }    
    }
    if ($paroxos_params['paroxos_need_password'] and $paroxos_params['pc_password']=='') {
      if ($row['company_sub_id']==0) { //kentriko
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων σε πάροχο, τo <b>Κωδικός Πρόσβασης</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      } else {
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων σε πάροχο, τo <b>Κωδικός Πρόσβασης</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
        $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);        
        debug_mail(false,$ret['message'],$sql); return $ret;
      }    
    }
    if ($paroxos_params['paroxos_need_key'] and $paroxos_params['pc_key']=='') {
      if ($row['company_sub_id']==0) { //kentriko
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων σε πάροχο, το <b>Κλειδί API</b> για την εταιρεία <b>[1]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_title'],$ret['message']);
        debug_mail(false,$ret['message'],$sql); return $ret;
      } else {
        $ret['message']=gks_lang('Στην Αποστολή δεδομένων σε πάροχο, το <b>Κλειδί API</b> για το υποκατάστημα <b>[1]</b> της εταιρείας <b>[2]</b> δεν έχει ορισθεί');
        $ret['message']=str_replace('[1]',$row['company_sub_title'],$ret['message']);
        $ret['message']=str_replace('[2]',$row['company_title'],$ret['message']);        
        debug_mail(false,$ret['message'],$sql); return $ret;
      }
    }
    
  
    
    //$ret['message']='<pre>'.print_r($paroxos_params,true);return $ret;
    
    
    //if (empty($row['aade_invoiceuid'])==false or empty($row['aade_invoicemark'])==false or empty($row['aade_statuscode'])==false or isset($row['aade_send_date'])) {
    if (trim_gks($row['aade_statuscode'])=='Success' and isset($row['aade_send_date'])) {
      $ret['message']=gks_lang('Το παραστατικό έχει ήδη αποσταλεί στον πάροχο').'<br>'.
      gks_lang('Το αναγνωριστικό του παραστατικού είναι').': <b>'.$row['aade_invoiceuid'].'</b>';
      if (empty($row['aade_invoicemark'])) {
        $ret['message'].='<br>'.gks_lang('Δεν έχει λάβει ακόμα ΜΑΡΚ');
      } else {
        $ret['message'].='<br>'.gks_lang('Το ΜΑΡΚ του παραστατικού είναι').': <b>'.$row['aade_invoicemark'].'</b>';
      }
      debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;
    }
  }


  
  //echo '<pre>ret_xml ';print_r($ret_xml);die();
  
  if ($cancel_for_acc_xxx_id==0) {
    $ret_xml = gks_paroxos_invoice_xml_create($id,$doc_table,$paroxos_params);
    
    //echo '<pre>ret_xml 11111111 ';print_r($ret_xml);die();
    
    if ($ret_xml['success'] == false) {$ret['message']=$ret_xml['message'];debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;}
    
    //echo '<pre>sssss';print_r($ret_xml['struct_data']['xml']['paymentMethods']);die();
    
//    if (1==2) {
//      $pos_eftpos_has=false;
//      $pos_eftpos_amount=0;
//      if (isset($ret_xml['struct_data']['xml']['paymentMethods']) and 
//         is_array($ret_xml['struct_data']['xml']['paymentMethods']) and 
//         count($ret_xml['struct_data']['xml']['paymentMethods'])>0) {
//        
//        foreach ($ret_xml['struct_data']['xml']['paymentMethods'] as $mypm) {
//          if ($mypm['type']==7) {
//            $pos_eftpos_has=true;
//            $pos_eftpos_amount+=$mypm['amount'];
//          }
//        } 
//        //echo '<pre>sssss';print_r($ret_xml['struct_data']['xml']['paymentMethods']);die();        
//      }
//      if ($pos_eftpos_has) { //todo
//        
//        
//        
//        if ($paroxos_params['aade_paroxos_id']==8) { //ilyda
//          
//        } else {
//          echo '<pre>check paroxos sign';die();
//          $ret_sig=gks_paroxos_payment_sign($id,$paroxos_params,$ret_xml['struct_data']);
//          if ($ret_sig['success'] == false) {$ret['message']=$ret_sig['message'];debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;}
//        }
//        //echo '<pre>sssss'."\n".print_r($ret_xml['struct_data']['xml'],true);die(); 
//        
//        
//        
//        
//      }
//    }
    //echo '<pre>sssss'."\n".$pos_eftpos_has."\n".$pos_eftpos_amount."\n".print_r($ret_xml['struct_data']['xml']['paymentMethods'],true);die(); 
    
    //echo '<pre>check eft pos here';print_r($ret_xml);die();
    //echo '<pre>sssss';print_r($ret_xml['struct_data']['xml']['paymentMethods']);die();
    
    //$ret['message']='<pre>'.print_r($ret_xml,true);return $ret;

    
    //echo '<pre>ret_xml 222222 ';print_r($ret_xml);die();
    
    
		$ret_build=gks_paroxos_invoice_xml_build($id,$paroxos_params,$ret_xml['struct_data']);
    //echo '<pre>sssss fffffff ';print_r($ret_build);die();
    if ($ret_build['success'] == false) {$ret['message']=$ret_build['message'];debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;}
    
    $paroxos_signature_id_array=array();
    if (isset($ret_build['paroxos_signature_id_array'])) {
      $paroxos_signature_id_array=$ret_build['paroxos_signature_id_array'];
    }
    //print '<pre>1111 paroxos_signature_id_array ';print_r($paroxos_signature_id_array);die();
    
    
    $ret = gks_paroxos_invoice_xml_send($id,$paroxos_params,$ret_xml['struct_data'],$ret_build['file_data']);
    if ($ret['success'] == false) {
      //debug_mail(false,'error at gks_paroxos_invoice_xml_send',$ret['message'].'<br>'.'main sql: '.$sql); 
      return $ret;
    }
    
    //echo '<pre>fffffffffffffffff 22';die();
    //$ret['message']='<pre>'.print_r($ret_send,true);return $ret;

    //$ret_parse=gks_paroxos_invoice_xml_parse_response($ret_send['out_xml']);
    //$ret['message']='<pre>'.print_r($ret_parse,true);return $ret;
  
    if (is_array($paroxos_signature_id_array) and count($paroxos_signature_id_array)>0) {
      $sql_signature="update gks_paroxos_signature set 
      signature_status='send'
      where id_paroxos_signature in (".implode(',',$paroxos_signature_id_array).")
      and signature_status='used'";
      $result_signature = $db_link->query($sql_signature);        
      if (!$result_signature) {debug_mail(false,'error sql',$sql_signature);$ret['message']='sql error'; return $ret;}
    }
    
  } else {
    
    $ret['message']='<pre>einai akurotiko vre !!'."\n".print_r($paroxos_params,true)."\n".print_r($ret,true);return $ret;
    
    $sql_cancel="select * from gks_acc_inv where id_acc_inv=".$cancel_for_acc_xxx_id;
    $result_cancel = $db_link->query($sql_cancel);        
    if (!$result_cancel) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result_cancel->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό αναφοράς για ακύρωση');debug_mail(false,$ret['message'],$sql); return $ret;}
    $row_cancel = $result_cancel->fetch_assoc();  
    $aade_invoicemark=trim_gks($row_cancel['aade_invoicemark']);
    if (strlen($aade_invoicemark)<5 or ctype_digit($aade_invoicemark)==false) {$ret['message']=gks_lang('To ΜΑΡΚ του παραστατικού προς ακύρωση δεν είναι σωστό').':<br><b>'.$aade_invoicemark.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
      
      
    
    $ret_send = gks_aade_invoice_cancel_send($id,$aade_invoicemark,$paroxos_params);
    if ($ret_send['success'] == false) {$ret['message']=$ret_send['message'];debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;}
    //$ret['message']='<pre>'.print_r($ret_send,true);return $ret;

    $ret_parse=gks_aade_invoice_cancel_xml_parse_response($ret_send['out_xml']);
    //$ret['message']='<pre>'.print_r($ret_parse,true);return $ret;
    
    //die('<pre>δ'.$cancel_for_acc_xxx_id.' '.$aade_invoicemark);
    
  }
   
  
  //$ret['message']='<pre>sssssssssssssssss'."\n\n".'cancel_for_acc_xxx_id:'.$cancel_for_acc_xxx_id."\n".print_r($paroxos_params,true)."\n".print_r($ret,true);return $ret;
  
  

//  
//  if ($paroxos_params['paroxos_mydata_live']==1) {
//    if ($ret_parse['statusCode']=='Success') {
//      $sql_update="update gks_acc_inv set 
//      aade_statuscode='".$db_link->escape_string($ret_parse['statusCode'])."',
//      aade_invoiceuid='".$db_link->escape_string($ret_parse['invoiceUid'])."',
//      aade_invoicemark='".$db_link->escape_string($ret_parse['invoiceMark'])."',
//      aade_qrurl='".$db_link->escape_string($ret_parse['qrurl'])."',
//      
//      aade_errors='".$db_link->escape_string($ret_parse['errors_human'])."',
//      aade_send_date=now(),
//      aade_user_id=".$my_wp_user_id."
//      where id_acc_inv=".$id." limit 1";
//    } else { // ValidationError, TechnicalError, XMLSyntaxError
//      $sql_update="update gks_acc_inv set 
//      aade_statuscode='".$db_link->escape_string($ret_parse['statusCode'])."',
//      aade_errors='".$db_link->escape_string($ret_parse['errors_human'])."'
//      where id_acc_inv=".$id." limit 1";
//    }
//    $result = $db_link->query($sql_update);        
//    if (!$result) {debug_mail(false,'error sql',$sql_update);$ret['message']='sql error'; return $ret;}
//  } else {
//    $sql_update="update gks_acc_inv set 
//    aade_errors='".$db_link->escape_string($ret_parse['errors_human'])."'
//    where id_acc_inv=".$id." limit 1";
//    $result = $db_link->query($sql_update);        
//    if (!$result) {debug_mail(false,'error sql',$sql_update);$ret['message']='sql error'; return $ret;}
//    
//  }
  
  //print '<pre>';print_r($ret_parse);die();
  
//
//  
//  if ($cancel_for_acc_xxx_id==0) {  
//    $ret_save = gks_aade_invoice_xml_save($id,$ret_xml['out_xml'], '');
//    if ($ret_save['success'] == false) {$ret['message']=$ret_save['message'];debug_mail(false,$ret['message'],'main sql: '.$sql); return $ret;}
//    $filename=$ret_save['filename'];
//    
//    $filename_r=substr($filename, 0, strlen($filename) - 11).'-2-response.xml';
//    
//    $ret_respone_save = gks_aade_invoice_xml_save($id,$ret_send['out_xml'], $filename_r);
//    if ($ret_respone_save['success'] == false) {$ret['message']=$ret_respone_save['message'];debug_mail(false,$ret['message'],$sql); return $ret;}
//    
//    
//  } else { //cancel invoice
//    $filename='';
//    $filename_r='invoice_cancel_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999).'-2-response.xml';;
//
//    $ret_respone_save = gks_aade_invoice_xml_save($id,$ret_send['out_xml'], $filename_r);
//    if ($ret_respone_save['success'] == false) {$ret['message']=$ret_respone_save['message'];debug_mail(false,$ret['message'],$sql); return $ret;}
//    
//    
//  }
//
//
//  $sql="update gks_acc_inv set aade_xml_send='".$db_link->escape_string($filename)."', aade_xml_response='".$db_link->escape_string($filename_r)."' where id_acc_inv=".$id;
//  $result = $db_link->query($sql);        
//  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
//  
//  
  $ret['paroxos_mydata_live']=$paroxos_params['paroxos_mydata_live'];
  
  
  
  if ($ret['success']==false) {
    
    //$ret['message']=$ret_send['errors_human']; //'<pre>'.$ret_send['statusCode'].'<br>'.print_r($ret_send['errors'],true)."\n".print_r($ret_parse['errors_human'],true);
    debug_mail(false,'gks_aade_invoice_xml_parse_response',print_r($ret,true)); 
    return $ret;
    
  }

  
  //$ret['out_xml']=$ret_send;
  $ret['message']='OK';
  $ret['success']=true;
  
  return $ret;
  
}


function gks_paroxos_invoice_xml_create($id,$doc_table,$paroxos_params) {
  global $db_link;
  $ret = array('success' => false, 'message' => 'generic error');
  
  //echo '<pre>gks_paroxos_invoice_xml_create'."\n".$doc_table."\n";print_r($paroxos_params);die();
  
  if ($id<=0) {$ret['message']=gks_lang('Δεν έχει ορισθεί το').' ID.';debug_mail(false,$ret['message'],''); return $ret;}
  
  $struct_data=[];
  $struct_data['doc_table']=$doc_table;
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
  
  if ($doc_table=='gks_acc_inv') {
    
    $sql="SELECT gks_acc_inv.*, 
    ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
    
    gks_company.company_title, gks_company.company_eponimia,gks_company.company_afm,gks_company.company_doy,gks_company.aade_branch,
    gks_company.company_odos,gks_company.company_arithmos,gks_company.company_orofos,gks_company.company_perioxi,gks_company.company_poli,gks_company.company_tk,company_nomoi.nomos_descr as company_nomos_descr,
    company_country.country_initials as company_country_initials,company_country.country_name as company_country_name,
    company_country.country_ee as company_country_ee,
    gks_company.company_email,gks_company.company_phone,
    gks_company.company_gemi_number,
    
    gks_company_subs.company_sub_title,
    gks_company_subs.company_sub_odos,gks_company_subs.company_sub_arithmos,gks_company_subs.company_sub_orofos,gks_company_subs.company_sub_perioxi,gks_company_subs.company_sub_poli,gks_company_subs.company_sub_tk,company_sub_nomoi.nomos_descr as company_sub_nomos_descr,
    company_sub_country.country_initials as company_sub_country_initials,company_sub_country.country_name as company_sub_country_name,
    company_sub_country.country_ee as company_sub_country_ee,
    gks_company_subs.company_sub_email,gks_company_subs.company_sub_phone,
    
    gks_acc_journal.acc_journal_code, gks_acc_journal.acc_journal_descr, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
    gks_acc_eidi_parastatikon.peppol_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_posotita,
    gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, gks_aade_tropoi_pliromis.aade_tropos_pliromis_code,
    gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
    gks_users.order_sxolio,gks_users.pelati_sxolio,
    
    party_country.country_initials as party_country_initials,party_country.country_name as party_country_name,
    party_country.country_ee as party_country_ee,
    party_nomoi.nomos_descr as party_nomos_descr,
    
    party_delivery_country.country_initials as party_delivery_country_initials,party_delivery_country.country_name as party_delivery_country_name,
    party_delivery_country.country_ee as party_delivery_country_ee,
    party_delivery_nomoi.nomos_descr as party_delivery_nomos_descr,
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
    gks_aade_skopos_diakinisis.aade_skopos_diakinisis_code,
    gks_users.gemi_number,
    gks_users.is_b2g,
    gks_users.b2g_aaht_code,
    gks_users.b2g_aaht_name,
    gks_users.b2g_aaht_foreas,
    gks_users.b2g_aaht_typos_forea,
    gks_users.b2g_aaht_kodikos_ekatharisis
    
    FROM ((((((((((((((((((((((gks_acc_inv
    
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_inv.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_inv.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
    LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_nomoi as company_nomoi ON gks_company.company_nomos_id = company_nomoi.id_nomos)
    LEFT JOIN gks_country as company_country ON gks_company.company_country_id = company_country.id_country)
    LEFT JOIN gks_nomoi as company_sub_nomoi ON gks_company_subs.company_sub_nomos_id = company_sub_nomoi.id_nomos)
    LEFT JOIN gks_country as company_sub_country ON gks_company_subs.company_sub_country_id = company_sub_country.id_country)
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
    LEFT JOIN gks_aade_tropoi_pliromis ON gks_payment_acquirers.aade_tropos_pliromis_id = gks_aade_tropoi_pliromis.id_aade_tropos_pliromis)
    LEFT JOIN gks_delivery_methods ON gks_acc_inv.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
    LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
    LEFT JOIN gks_eshop_fiscal_position ON gks_acc_inv.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
    LEFT JOIN gks_eshop_pricelist ON gks_acc_inv.pricelist_id = gks_eshop_pricelist.id_pricelist)
    LEFT JOIN gks_country as party_country ON gks_acc_inv.ma_country_id = party_country.id_country)
    LEFT JOIN gks_country as party_delivery_country ON gks_acc_inv.destination_data_country_id = party_delivery_country.id_country)
    LEFT JOIN gks_nomoi as party_nomoi ON gks_acc_inv.ma_nomos_id = party_nomoi.id_nomos)
    LEFT JOIN gks_nomoi as party_delivery_nomoi ON gks_acc_inv.destination_data_nomos_id = party_delivery_nomoi.id_nomos)
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_aade_skopos_diakinisis ON gks_acc_inv.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis
    
    where gks_acc_inv.id_acc_inv=".$id;
  } else if ($doc_table=='gks_acc_pay') {
    $sql="SELECT gks_acc_pay.*,
    ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
    
    gks_company.company_title, gks_company.company_eponimia,gks_company.company_afm,gks_company.company_doy,gks_company.aade_branch,
    gks_company.company_odos,gks_company.company_arithmos,gks_company.company_orofos,gks_company.company_perioxi,gks_company.company_poli,gks_company.company_tk,company_nomoi.nomos_descr as company_nomos_descr,
    company_country.country_initials as company_country_initials,company_country.country_name as company_country_name,
    company_country.country_ee as company_country_ee,
    gks_company.company_email,gks_company.company_phone,
    gks_company.company_gemi_number,
    
    gks_company_subs.company_sub_title,
    gks_company_subs.company_sub_odos,gks_company_subs.company_sub_arithmos,gks_company_subs.company_sub_orofos,gks_company_subs.company_sub_perioxi,gks_company_subs.company_sub_poli,gks_company_subs.company_sub_tk,company_sub_nomoi.nomos_descr as company_sub_nomos_descr,
    company_sub_country.country_initials as company_sub_country_initials,company_sub_country.country_name as company_sub_country_name,
    company_sub_country.country_ee as company_sub_country_ee,
    gks_company_subs.company_sub_email,gks_company_subs.company_sub_phone,
    
    gks_acc_journal.acc_journal_code, gks_acc_journal.acc_journal_descr, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
    gks_acc_eidi_parastatikon.peppol_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_posotita,
  
    gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
    gks_users.order_sxolio,gks_users.pelati_sxolio,
    
    party_country.country_initials as party_country_initials,party_country.country_name as party_country_name,
    party_country.country_ee as party_country_ee,
    party_nomoi.nomos_descr as party_nomos_descr,
    
  
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
  
    gks_users.gemi_number,
    gks_users.is_b2g,
    gks_users.b2g_aaht_code,
    gks_users.b2g_aaht_name,
    gks_users.b2g_aaht_foreas,
    gks_users.b2g_aaht_typos_forea,
    gks_users.b2g_aaht_kodikos_ekatharisis
    
    FROM ((((((((((((((((gks_acc_pay
    
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_pay.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_pay.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
    LEFT JOIN gks_company on gks_acc_pay.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_pay.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_nomoi as company_nomoi ON gks_company.company_nomos_id = company_nomoi.id_nomos)
    LEFT JOIN gks_country as company_country ON gks_company.company_country_id = company_country.id_country)
    LEFT JOIN gks_nomoi as company_sub_nomoi ON gks_company_subs.company_sub_nomos_id = company_sub_nomoi.id_nomos)
    LEFT JOIN gks_country as company_sub_country ON gks_company_subs.company_sub_country_id = company_sub_country.id_country)
    LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira)
   
  
    LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
    LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
    LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist)
    LEFT JOIN gks_country as party_country ON gks_users.ma_country_id = party_country.id_country)
  
    LEFT JOIN gks_nomoi as party_nomoi ON gks_users.ma_nomos_id = party_nomoi.id_nomos)
  
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    
    where gks_acc_pay.id_acc_pay=".$id;

    /*
    
  LEFT JOIN gks_payment_acquirers ON gks_acc_pay.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer)
  LEFT JOIN gks_aade_tropoi_pliromis ON gks_payment_acquirers.aade_tropos_pliromis_id = gks_aade_tropoi_pliromis.id_aade_tropos_pliromis)
  LEFT JOIN gks_delivery_methods ON gks_acc_pay.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
  LEFT JOIN gks_country as party_delivery_country ON gks_acc_pay.destination_data_country_id = party_delivery_country.id_country)
  LEFT JOIN gks_nomoi as party_delivery_nomoi ON gks_acc_pay.destination_data_nomos_id = party_delivery_nomoi.id_nomos)  
  LEFT JOIN gks_aade_skopos_diakinisis ON gks_acc_pay.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis

  
  gks_payment_acquirers.payment_acquirer_name, 
  gks_aade_tropoi_pliromis.aade_tropos_pliromis_code,
  gks_delivery_methods.delivery_method_name, 
  party_delivery_country.country_initials as party_delivery_country_initials,party_delivery_country.country_name as party_delivery_country_name,
  party_delivery_country.country_ee as party_delivery_country_ee,
  party_delivery_nomoi.nomos_descr as party_delivery_nomos_descr,
  gks_aade_skopos_diakinisis.aade_skopos_diakinisis_code,
    
    
    */
    
  } else if ($doc_table=='gks_whi_mov') {
    
    $sql="SELECT gks_whi_mov.*, 
    ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
    
    gks_company.company_title, gks_company.company_eponimia,gks_company.company_afm,gks_company.company_doy,gks_company.aade_branch,
    gks_company.company_odos,gks_company.company_arithmos,gks_company.company_orofos,gks_company.company_perioxi,gks_company.company_poli,gks_company.company_tk,company_nomoi.nomos_descr as company_nomos_descr,
    company_country.country_initials as company_country_initials,company_country.country_name as company_country_name,
    company_country.country_ee as company_country_ee,
    gks_company.company_email,gks_company.company_phone,
    gks_company.company_gemi_number,
    
    gks_company_subs.company_sub_title,
    gks_company_subs.company_sub_odos,gks_company_subs.company_sub_arithmos,gks_company_subs.company_sub_orofos,gks_company_subs.company_sub_perioxi,gks_company_subs.company_sub_poli,gks_company_subs.company_sub_tk,company_sub_nomoi.nomos_descr as company_sub_nomos_descr,
    company_sub_country.country_initials as company_sub_country_initials,company_sub_country.country_name as company_sub_country_name,
    company_sub_country.country_ee as company_sub_country_ee,
    gks_company_subs.company_sub_email,gks_company_subs.company_sub_phone,
    
    gks_acc_journal.acc_journal_code, gks_acc_journal.acc_journal_descr, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
    gks_acc_eidi_parastatikon.peppol_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_posotita,
    gks_delivery_methods.delivery_method_name, 
    gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
    gks_users.order_sxolio,gks_users.pelati_sxolio,
    
    party_country.country_initials as party_country_initials,party_country.country_name as party_country_name,
    party_country.country_ee as party_country_ee,
    party_nomoi.nomos_descr as party_nomos_descr,
    
    party_delivery_country.country_initials as party_delivery_country_initials,party_delivery_country.country_name as party_delivery_country_name,
    party_delivery_country.country_ee as party_delivery_country_ee,
    party_delivery_nomoi.nomos_descr as party_delivery_nomos_descr,
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
    gks_aade_skopos_diakinisis.aade_skopos_diakinisis_code,
    gks_users.gemi_number,
    gks_users.is_b2g,
    gks_users.b2g_aaht_code,
    gks_users.b2g_aaht_name,
    gks_users.b2g_aaht_foreas,
    gks_users.b2g_aaht_typos_forea,
    gks_users.b2g_aaht_kodikos_ekatharisis
    
    FROM ((((((((((((((((((((gks_whi_mov
    
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_whi_mov.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_whi_mov.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
    LEFT JOIN gks_company on gks_whi_mov.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_whi_mov.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_nomoi as company_nomoi ON gks_company.company_nomos_id = company_nomoi.id_nomos)
    LEFT JOIN gks_country as company_country ON gks_company.company_country_id = company_country.id_country)
    LEFT JOIN gks_nomoi as company_sub_nomoi ON gks_company_subs.company_sub_nomos_id = company_sub_nomoi.id_nomos)
    LEFT JOIN gks_country as company_sub_country ON gks_company_subs.company_sub_country_id = company_sub_country.id_country)
    LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_delivery_methods ON gks_whi_mov.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
    LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
    LEFT JOIN gks_eshop_fiscal_position ON gks_whi_mov.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
    LEFT JOIN gks_eshop_pricelist ON gks_whi_mov.pricelist_id = gks_eshop_pricelist.id_pricelist)
    LEFT JOIN gks_country as party_country ON gks_whi_mov.ma_country_id = party_country.id_country)
    LEFT JOIN gks_country as party_delivery_country ON gks_whi_mov.destination_data_country_id = party_delivery_country.id_country)
    LEFT JOIN gks_nomoi as party_nomoi ON gks_whi_mov.ma_nomos_id = party_nomoi.id_nomos)
    LEFT JOIN gks_nomoi as party_delivery_nomoi ON gks_whi_mov.destination_data_nomos_id = party_delivery_nomoi.id_nomos)
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_aade_skopos_diakinisis ON gks_whi_mov.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis
    
    where gks_whi_mov.id_whi_mov=".$id;  
  }
  
  
  //print '<pre>'.$sql;die();
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό');debug_mail(false,$ret['message'],$sql); return $ret;}
    
  $row = $result->fetch_assoc();
  $struct_data['row']=$row;

  $struct_data['eidos_parastatikou_has_posotita']=$row['eidos_parastatikou_has_posotita'];
  $struct_data['eidos_parastatikou_need_afm']=intval($row['eidos_parastatikou_need_afm']);
  $struct_data['credit_memo_for_'.$ttt.'_id']=intval($row['credit_memo_for_'.$ttt.'_id']);
  if ($xxx=='inv') $struct_data['dimotikos_foros_for_'.$ttt.'_id']=intval($row['dimotikos_foros_for_'.$ttt.'_id']);
  $struct_data['eidos_parastatikou_aade_code']=trim_gks($row['eidos_parastatikou_aade_code']);
  $struct_data['eidos_parastatikou_type_id']=intval($row['eidos_parastatikou_type_id']);
  $struct_data['correlatedInvoices']=[];
  if ($doc_table=='gks_acc_inv' or $doc_table=='gks_whi_mov') $struct_data['aade_skopos_diakinisis_code']=intval($row['aade_skopos_diakinisis_code']);
  
  //print '<pre>struct_data'."\n";print_r($struct_data);die();
  
  //print '<pre>eidos_parastatikou_aade_code '.$struct_data['eidos_parastatikou_aade_code'];die();
  
  if ($struct_data['eidos_parastatikou_aade_code']=='5.1') { //Pistotiko Timologio / Syschetizomeno
    //echo '<pre>check this ';print_r($struct_data);die();
    if ($struct_data['credit_memo_for_'.$ttt.'_id']<=0) {
      debug_mail(false,'error Pistotiko Timologio / Syschetizomeno',
       'eidos_parastatikou_aade_code: '.$struct_data['eidos_parastatikou_aade_code'].'<br>credit_memo_for_'.$ttt.'_id: '.$struct_data['credit_memo_for_'.$ttt.'_id']);
      $ret['message']=gks_lang('Δεν έχει ορισθεί το συσχετιζόμενο παραστατικό για αυτό το πιστωτικό παραστατικό'); return $ret;
    }
    
    $sql_credit_memo="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
    gks_acc_inv.aade_invoicemark,
    gks_acc_inv.paroxos_invoice_number,
    gks_acc_inv.paroxos_date_send
    FROM (((gks_acc_inv 
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
    where gks_acc_inv.id_acc_inv=".$struct_data['credit_memo_for_acc_inv_id'];
    $result_credit_memo = $db_link->query($sql_credit_memo);        
    if (!$result_credit_memo) {debug_mail(false,'error sql',$sql_credit_memo);die('sql error');}
    if ($result_credit_memo->num_rows==0) {
      $struct_data['credit_memo_descr_for']=gks_lang('Δεν βρέθηκε το συσχετιζόμενο παραστατικό με ID').': '.
      '<a href="admin-acc-inv-item.php?id='.$struct_data['credit_memo_for_acc_inv_id'].'" class="gks_link">'.$struct_data['credit_memo_for_acc_inv_id'].'</a>';
      debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not found',$struct_data['credit_memo_descr_for'].' '.$sql_credit_memo);
      $ret['message']=$struct_data['credit_memo_descr_for']; return $ret;

      //die('no record found (2)');
    } else {
      $struct_data['row_credit_memo'] = $result_credit_memo->fetch_assoc();
    
      //$antisimvalomenos_label=$row_credit_memo['antisimvalomenos_label'];
      //$acc_eidos_parastatikou_id=intval($row_credit_memo['acc_eidos_parastatikou_id']);
      //$eidos_parastatikou_type_id=intval($row_credit_memo['eidos_parastatikou_type_id']);
      //$eidos_parastatikou_need_prev=intval($row_credit_memo['eidos_parastatikou_need_prev']);
      //$eidos_parastatikou_has_fpa=intval($row_credit_memo['eidos_parastatikou_has_fpa']);
      //$eidos_parastatikou_has_othertaxes=trim_gks($row_credit_memo['eidos_parastatikou_has_othertaxes']);
      //$eidos_parastatikou_has_esoda=intval($row_credit_memo['eidos_parastatikou_has_esoda']);
      //$eidos_parastatikou_has_eksoda=intval($row_credit_memo['eidos_parastatikou_has_eksoda']);
      $struct_data['eidos_parastatikou_need_afm']=intval($struct_data['row_credit_memo']['eidos_parastatikou_need_afm']);
      
      $struct_data['aade_invoicemark']=trim_gks($struct_data['row_credit_memo']['aade_invoicemark']);
      if ($struct_data['aade_invoicemark']=='') {
        $struct_data['credit_memo_descr_for']=gks_lang('Το συσχετιζόμενο παραστατικό με ID').': '.
        '<a href="admin-acc-inv-item.php?id='.$struct_data['credit_memo_for_acc_inv_id'].'" class="gks_link">'.$struct_data['credit_memo_for_acc_inv_id'].'</a><br>'.
        gks_lang('δεν έχει ΜΑΡΚ').'<br>'.gks_lang('Σίγουρα έχει σταλεί σε πάροχο ;');
        debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not mark',$struct_data['credit_memo_descr_for'].' '.$sql_credit_memo);
        $ret['message']=$struct_data['credit_memo_descr_for']; return $ret;
      }
      
      
      $struct_data['correlatedInvoices'][]=array(
        'type' => 'credit_memo',
      	'acc_inv_id' => $struct_data['credit_memo_for_acc_inv_id'],
      	'aade_invoicemark' => trim_gks($struct_data['aade_invoicemark']),
      	'paroxos_invoice_number' => trim_gks($struct_data['row_credit_memo']['paroxos_invoice_number']),
      	'paroxos_date_send' => $struct_data['row_credit_memo']['paroxos_date_send'],
      );
      //print '<pre>';print_r($struct_data['correlatedInvoices']);die();
      
    }    
    
  }
  
  
  if ($struct_data['eidos_parastatikou_aade_code']=='8.2') { //Eidiko Stoicheio - Apodeixis Eispraxis Forou Diamonis
    if ($struct_data['dimotikos_foros_for_acc_inv_id']<=0) {
      debug_mail(false,'error Apodeixis Eispraxis Forou Diamonis',
       'eidos_parastatikou_aade_code: '.$struct_data['eidos_parastatikou_aade_code'].'<br>dimotikos_foros_for_acc_inv_id: '.$struct_data['dimotikos_foros_for_acc_inv_id']);
      $ret['message']=gks_lang('Δεν έχει ορισθεί το συσχετιζόμενο παραστατικό για αυτό το παραστατικό'); return $ret;
    }
    
    $sql_dimotikos_foros="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
    gks_acc_inv.aade_invoicemark,
    gks_acc_inv.paroxos_invoice_number,
    gks_acc_inv.paroxos_date_send
    FROM (((gks_acc_inv 
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
    where gks_acc_inv.id_acc_inv=".$struct_data['dimotikos_foros_for_acc_inv_id'];
    $result_dimotikos_foros = $db_link->query($sql_dimotikos_foros);        
    if (!$result_dimotikos_foros) {debug_mail(false,'error sql',$sql_dimotikos_foros);die('sql error');}
    if ($result_dimotikos_foros->num_rows==0) {
      $struct_data['dimotikos_foros_descr_for']=gks_lang('Δεν βρέθηκε το συσχετιζόμενο παραστατικό με ID').': '.
      '<a href="admin-acc-inv-item.php?id='.$struct_data['dimotikos_foros_for_acc_inv_id'].'" class="gks_link">'.$struct_data['dimotikos_foros_for_acc_inv_id'].'</a>';
      debug_mail(false,'error Apodeixis Eispraxis Forou Diamonis, record parent not found',$struct_data['dimotikos_foros_descr_for'].' '.$sql_dimotikos_foros);
      $ret['message']=$struct_data['dimotikos_foros_descr_for']; return $ret;

      //die('no record found (2)');
    } else {
      $struct_data['row_dimotikos_foros'] = $result_dimotikos_foros->fetch_assoc();
    
      //$antisimvalomenos_label=$row_dimotikos_foros['antisimvalomenos_label'];
      //$acc_eidos_parastatikou_id=intval($row_dimotikos_foros['acc_eidos_parastatikou_id']);
      //$eidos_parastatikou_type_id=intval($row_dimotikos_foros['eidos_parastatikou_type_id']);
      //$eidos_parastatikou_need_prev=intval($row_dimotikos_foros['eidos_parastatikou_need_prev']);
      //$eidos_parastatikou_has_fpa=intval($row_dimotikos_foros['eidos_parastatikou_has_fpa']);
      //$eidos_parastatikou_has_othertaxes=trim_gks($row_dimotikos_foros['eidos_parastatikou_has_othertaxes']);
      //$eidos_parastatikou_has_esoda=intval($row_dimotikos_foros['eidos_parastatikou_has_esoda']);
      //$eidos_parastatikou_has_eksoda=intval($row_dimotikos_foros['eidos_parastatikou_has_eksoda']);
      //$eidos_parastatikou_need_afm=intval($row_dimotikos_foros['eidos_parastatikou_need_afm']);
      
      $struct_data['aade_invoicemark']=trim_gks($struct_data['row_dimotikos_foros']['aade_invoicemark']);
      if ($struct_data['aade_invoicemark']=='') {
        $struct_data['dimotikos_foros_descr_for']=gks_lang('Το συσχετιζόμενο παραστατικό με ID').': '.
        '<a href="admin-acc-inv-item.php?id='.$struct_data['dimotikos_foros_for_acc_inv_id'].'" class="gks_link">'.$struct_data['dimotikos_foros_for_acc_inv_id'].'</a><br>'.
        gks_lang('δεν έχει ΜΑΡΚ').'<br>'.gks_lang('Σίγουρα έχει σταλεί στην ΑΑΔΕ ;');
        debug_mail(false,'error Apodeixis Eispraxis Forou Diamonis, record parent not mark',$struct_data['dimotikos_foros_descr_for'].' '.$sql_dimotikos_foros);
        $ret['message']=$struct_data['dimotikos_foros_descr_for']; return $ret;
      }
      
      
      
      $struct_data['correlatedInvoices'][]=array(
        'type' => 'dimotikos_foros',
      	'acc_inv_id' => $struct_data['dimotikos_foros_for_acc_inv_id'],
      	'aade_invoicemark' => trim_gks($struct_data['aade_invoicemark']),
      	'paroxos_invoice_number' => trim_gks($struct_data['row_credit_memo']['paroxos_invoice_number']),
      	'paroxos_date_send' => $struct_data['row_credit_memo']['paroxos_date_send'],
      );
            
    }    
    
  }
  
  
  $is_endodiakinisi=false;
  if ($struct_data['eidos_parastatikou_aade_code']=='9.3' and  //deltio apostolis
      trim_gks($row['afm'])=='' and 
      in_array($struct_data['aade_skopos_diakinisis_code'],[8,18])) { //8->metaxy Enkatastaseon Ontotitas, 18=>diakinisi Pagion (Endodiakinisi)
    $is_endodiakinisi=true;
  }
  
  
  
  $struct_data['xml']=array();
  $struct_data['xml']['issuer']=array();
  
  
  //print '<pre>struct_data xml'."\n";print_r($struct_data);die();
  
  $company_afm=trim_gks($row['company_afm']);
  if (isset($paroxos_params['force_afm']) and $paroxos_params['force_afm']!='') {
    $company_afm=$paroxos_params['force_afm'];
  }
  //print '<pre>';
  //print_r($paroxos_params);
  //die();
  
  
  if ($company_afm=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί το ΑΦΜ της εταιρείας σας'); debug_mail(false,$ret['message'],''); return $ret;}
  $struct_data['xml']['issuer']['vatNumber']=$company_afm;
  
  
  $company_country_initials=trim_gks($row['company_country_initials']);
  if ($company_country_initials=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί ο κωδικός χώρας της εταιρείας σας'); debug_mail(false,$ret['message'],''); return $ret;}
  $struct_data['xml']['issuer']['country']=$company_country_initials;
  
  //if ($paroxos_params['branch'] < 0) {$ret['message']=gks_lang('Ορίστε τον Αριθμό Εγκατάστασης της εταιρείας σας'); debug_mail(false,$ret['message'],''); return $ret;}
  //$struct_data['xml']['issuer']['branch']=$paroxos_params['branch'];
    
    
  //if ($company_country_initials!='GR') {
  $company_eponimia=trim_gks($row['company_eponimia']);
  if($company_eponimia != '') $struct_data['xml']['issuer']['name']=$company_eponimia;
  $struct_data['xml']['issuer']['address']=array();
  
  $company_gemi_number=trim_gks($row['company_gemi_number']);
  if($company_gemi_number != '') $struct_data['xml']['issuer']['company_gemi_number']=$company_gemi_number;
  
  if ($row['company_sub_id']==0) { //kentriko
    $company_odos=trim_gks($row['company_odos']);
    if ($company_odos!='') $struct_data['xml']['issuer']['address']['street']=$company_odos;
    $company_arithmos=trim_gks($row['company_arithmos']);
    if ($company_arithmos!='') $struct_data['xml']['issuer']['address']['number']=$company_arithmos;
    $company_tk=trim_gks($row['company_tk']);
    if ($company_tk!='') $struct_data['xml']['issuer']['address']['postalCode']=$company_tk;
    $company_poli=trim_gks($row['company_poli']);
    if ($company_poli!='') $struct_data['xml']['issuer']['address']['city']=$company_poli;
    $company_email=trim_gks($row['company_email']);
    if ($company_email!='') $struct_data['xml']['issuer']['email']=$company_email;
    $company_phone=trim_gks($row['company_phone']);
    if ($company_phone!='') $struct_data['xml']['issuer']['phone']=$company_phone;
    
    
  } else { //ypokatastima
    $company_sub_odos=trim_gks($row['company_sub_odos']);
    if ($company_sub_odos!='') $struct_data['xml']['issuer']['address']['street']=$company_sub_odos;
    $company_sub_arithmos=trim_gks($row['company_sub_arithmos']);
    if ($company_sub_arithmos!='') $struct_data['xml']['issuer']['address']['number']=$company_sub_arithmos;
    $company_sub_tk=trim_gks($row['company_sub_tk']);
    if ($company_sub_tk!='') $struct_data['xml']['issuer']['address']['postalCode']=$company_sub_tk;
    $company_sub_poli=trim_gks($row['company_sub_poli']);
    if ($company_sub_poli!='') $struct_data['xml']['issuer']['address']['city']=$company_sub_poli;
    $company_sub_email=trim_gks($row['company_sub_email']);
    if ($company_sub_email!='') {
      $struct_data['xml']['issuer']['email']=$company_sub_email;
    } else {
      $company_email=trim_gks($row['company_email']);
      if ($company_email!='') $struct_data['xml']['issuer']['email']=$company_email;
    }
    $company_sub_phone=trim_gks($row['company_sub_phone']);
    if ($company_sub_phone!='') {
      $struct_data['xml']['issuer']['phone']=$company_sub_phone;
    } else {
      $company_phone=trim_gks($row['company_phone']);
      if ($company_phone!='') $struct_data['xml']['issuer']['phone']=$company_phone;
      
    }
    
    
  }
  //}
  
  //print '<pre>struct_data xml 222'."\n";print_r($struct_data);die();
  
  if ($struct_data['eidos_parastatikou_need_afm']!=0) {
    $struct_data['xml']['counterpart']=array();
    
    
    $afm=trim_gks($row['afm']);
    $party_country_initials=trim_gks($row['party_country_initials']);
    if ($is_endodiakinisi) {
      $struct_data['xml']['counterpart']['vatNumber']='000000000';
      $party_country_initials='';
      if (isset($row['deli_country_id']) and intval($row['deli_country_id'])>0) {
        $sql_temp="select country_initials from gks_country where id_country=".intval($row['deli_country_id']);
        $result_temp = $db_link->query($sql_temp);        
        if (!$result_temp) {debug_mail(false,'error sql',$sql_temp);$ret['message']='sql error'; return $ret;}
        if ($result_temp->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η χώρα με κωδικό').' '.intval($row['deli_country_id']);debug_mail(false,$ret['message'],$sql_temp); return $ret;}
        $row_temp = $result_temp->fetch_assoc();
        $party_country_initials=$row_temp['country_initials'];
        //
      }
      $struct_data['xml']['counterpart']['country']=$party_country_initials;
      //echo '<pre>dddd ssss'.$party_country_initials;die();      
    } else {
      if ($afm=='' and $struct_data['eidos_parastatikou_aade_code']!='8.2') {
        $ret['message']=gks_lang('Δεν έχει ορισθεί το ΑΦΜ του πελάτη/προμηθευτή'); debug_mail(false,$ret['message'],''); return $ret;}
    
      if ($struct_data['eidos_parastatikou_aade_code']=='8.2' and $afm=='') {
        $struct_data['xml']['counterpart']['vatNumber']='000000000';
      } else {  
        $struct_data['xml']['counterpart']['vatNumber']=$afm;
      }
      if ($party_country_initials=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί ο κωδικός χώρας του πελάτη/προμηθευτή'); debug_mail(false,$ret['message'],''); return $ret;}
      $struct_data['xml']['counterpart']['country']=$party_country_initials;
      
      
    }
  


  
   
    //$struct_data['xml']['counterpart']['branch']=0;
    
    //if ($party_country_initials!='GR') {
      $eponimia=trim_gks($row['eponimia']);
      if($eponimia != '') {
      	$struct_data['xml']['counterpart']['name']=$eponimia;
      } else {
      	$eponimia=trim_gks($row['user_last_name']).' '.trim_gks($row['user_first_name']);
      	$eponimia=str_replace('  ',' ',$eponimia);
      	$eponimia=str_replace('  ',' ',$eponimia);
      	$eponimia=str_replace('  ',' ',$eponimia);
      	$eponimia=trim_gks($eponimia);
      	if($eponimia != '') $struct_data['xml']['counterpart']['name']=$eponimia;
      	
      }
    //}
    
    $struct_data['xml']['counterpart']['address']=array();
    $ma_odos=trim_gks($row['ma_odos']);
    if ($ma_odos!='') $struct_data['xml']['counterpart']['address']['street']=$ma_odos;
    $ma_arithmos=trim_gks($row['ma_arithmos']);
    if ($ma_arithmos!='') $struct_data['xml']['counterpart']['address']['number']=$ma_arithmos;
    $ma_tk=trim_gks($row['ma_tk']);
    if ($ma_tk!='') $struct_data['xml']['counterpart']['address']['postalCode']=$ma_tk;
    $ma_poli=trim_gks($row['ma_poli']);
    if ($ma_poli!='') $struct_data['xml']['counterpart']['address']['city']=$ma_poli;
    
    $user_email=trim_gks($row['user_email']);
    if ($user_email!='') $struct_data['xml']['counterpart']['email']=$user_email;
    
    if (intval($row['address_extra'])>0) {
    	$struct_data['xml']['counterpart']['deliveryAddress']=array();
    	
    	//country
			$party_delivery_country_initials=trim_gks($row['party_delivery_country_initials']);
			
	    if ($party_delivery_country_initials=='') {
	      $ret['message']=gks_lang('Δεν έχει ορισθεί ο κωδικός χώρας του πελάτη/προμηθευτή στην διεύθυνση αποστολής'); 
	      debug_mail(false,$ret['message'],''); return $ret;}
	    $struct_data['xml']['counterpart']['deliveryAddress']['country']=$party_delivery_country_initials;

    	
    	$destination_data_odos=trim_gks($row['destination_data_odos']);
    	if ($destination_data_odos!='') $struct_data['xml']['counterpart']['deliveryAddress']['street']=$destination_data_odos;
    	
    	$destination_data_arithmos=trim_gks($row['destination_data_arithmos']);
    	if ($destination_data_arithmos!='') $struct_data['xml']['counterpart']['deliveryAddress']['number']=$destination_data_arithmos;
    	
    	$destination_data_tk=trim_gks($row['destination_data_tk']);
    	if ($destination_data_tk!='') $struct_data['xml']['counterpart']['deliveryAddress']['postalCode']=$destination_data_tk;
    	
    	$destination_data_poli=trim_gks($row['destination_data_poli']);
    	if ($destination_data_poli!='') $struct_data['xml']['counterpart']['deliveryAddress']['city']=$destination_data_poli;

    	$destination_data_name=trim_gks($row['destination_data_name']);
    	if ($destination_data_name!='') $struct_data['xml']['counterpart']['deliveryAddress']['name']=$destination_data_name;


    	
    
    }
    
    
    
    
  }

  //


  //print '<pre>struct_data xml 333'."\n";print_r($struct_data);die();
  
  $struct_data['xml']['invoiceHeader']=array();
  

  $rrr_seira_code=trim_gks($row[$rrr.'_seira_code']);
  if ($rrr_seira_code=='') {$ret['message']=gks_lang('Δεν βρέθηκε η σειρά'); debug_mail(false,$ret['message'],''); return $ret;}
  $struct_data['xml']['invoiceHeader']['series']=$rrr_seira_code;
  
  $rrr_number_int=intval($row[$rrr.'_number_int']);
  if ($rrr_number_int<=0) {$ret['message']=gks_lang('Δεν βρέθηκε ο αριθμός του παραστατικού'); debug_mail(false,$ret['message'],''); return $ret;}
  $struct_data['xml']['invoiceHeader']['aa']=$rrr_number_int;
  
  
  $xxx_date=trim_gks($row[$xxx.'_date']);
  if ($xxx_date=='') {$ret['message']=gks_lang('Δεν ορίσθηκε η ημερομηνία'); debug_mail(false,$ret['message'],''); return $ret;}
  $xxx_date_str=showDate(strtotime($xxx_date),'Y-m-d',1); // H:i:s
  $struct_data['xml']['invoiceHeader']['issueDate']=$xxx_date_str;
  $struct_data['xml']['invoiceHeader']['issueDate_compact']=showDate(strtotime($xxx_date),'Ymd',1);

  $xxx_time_str=showDate(strtotime($xxx_date),'H:i:s',1); // H:i:s
  $struct_data['xml']['invoiceHeader']['issueTime']=$xxx_time_str;
  $struct_data['xml']['invoiceHeader']['issueTime_compact']=showDate(strtotime($xxx_date),'His',1);


  $xxx_date_str_iso_8601=date('c', strtotime($xxx_date_str));
  $struct_data['xml']['invoiceHeader']['issueDate_iso_8601']=$xxx_date_str_iso_8601;
  
  $date_obj = new DateTime(showDate(strtotime($xxx_date),'Y-m-d H:i:s',1), new DateTimeZone('Europe/Athens'));
  $xxx_date_str_iso_8601=$date_obj->format('c');
  $struct_data['xml']['invoiceHeader']['issueDate_iso_8601_r']=$xxx_date_str_iso_8601;
  
  
  $eidos_parastatikou_aade_code=trim_gks($row['eidos_parastatikou_aade_code']);
  if ($eidos_parastatikou_aade_code=='') {$ret['message']=gks_lang('Δεν βρέθηκε ο κωδικός ΑΑΔΕ για το παραστατικό'); debug_mail(false,$ret['message'],''); return $ret;}
  $struct_data['xml']['invoiceHeader']['invoiceType']=$eidos_parastatikou_aade_code;
  $struct_data['xml']['invoiceHeader']['invoiceCode']=trim_gks($row['acc_journal_code']);
  $eidos_parastatikou_type_id=intval($row['eidos_parastatikou_type_id']);
  $struct_data['xml']['invoiceHeader']['eidos_parastatikou_type_id']=$eidos_parastatikou_type_id;
  $struct_data['xml']['invoiceHeader']['peppol_code']=intval($row['peppol_code']);
  //vatPaymentSuspension
  
  $struct_data['xml']['invoiceHeader']['currency']='EUR';
  
  //exchangeRate
  //correlatedInvoices
  //selfPricing
  
  if ($doc_table=='gks_acc_inv') {
    $dispatch_date=trim_gks($row['dispatch_date']);
    if ($dispatch_date!='') {
      $struct_data['xml']['invoiceHeader']['dispatchDate']=showDate(strtotime($dispatch_date),'Y-m-d',0);
      $struct_data['xml']['invoiceHeader']['dispatchDate_iso_8601']=showDate(strtotime($dispatch_date),'c',0);
    }

    $dispatch_time=trim_gks($row['dispatch_time']);
    if ($dispatch_time!='') {
      $struct_data['xml']['invoiceHeader']['dispatchTime']=$dispatch_time;
    }
    

    $vehicle_number=trim_gks($row['vehicle_number']);
    if ($vehicle_number!='') $struct_data['xml']['invoiceHeader']['vehicleNumber']=$vehicle_number;
    
    if ($struct_data['aade_skopos_diakinisis_code']>=1 and $struct_data['aade_skopos_diakinisis_code']<=8) $struct_data['xml']['invoiceHeader']['movePurpose']=$struct_data['aade_skopos_diakinisis_code'];
  }
  
  if (count($struct_data['correlatedInvoices'])>0) {
  	$struct_data['xml']['invoiceHeader']['correlatedInvoices']=$struct_data['correlatedInvoices'];
  }
  
  
  
  $note_doc=trim_gks($row['note_doc']);
  if ($note_doc!='') $struct_data['xml']['invoiceHeader']['note_doc']=$note_doc;

  //print '<pre>struct_data xml 444'."\n";print_r($struct_data);die();
  
//  $struct_data['aade_tropos_pliromis_code']=intval($struct_data['row']['aade_tropos_pliromis_code']);
//  if ($struct_data['aade_tropos_pliromis_code']>0 and $struct_data['row']['gks_price_total']>0) {
//     $pay_item=array(
//      'type'=>$struct_data['aade_tropos_pliromis_code'],
//      'amount'=>floatval($row['gks_price_total']),
//    );
//    $payment_acquirer_name=trim_gks($row['payment_acquirer_name']);
//    if ($payment_acquirer_name!='') $pay_item['paymentMethodInfo']=$payment_acquirer_name;
//    $struct_data['xml']['paymentMethods'][]=$pay_item;
//  }

	$struct_data['xml']['paymentMethods']=array();
	
	if ($doc_table=='gks_acc_inv' or $doc_table=='gks_acc_pay') {
    $sql_payments="SELECT gks_".$ttt."_payment.poso, 
    gks_".$ttt."_payment.payment_acquirer_id, 
    gks_payment_acquirers.payment_acquirer_name, 
    gks_payment_acquirers.aade_tropos_pliromis_id,
    gks_aade_tropoi_pliromis.aade_tropos_pliromis_code
    FROM (gks_".$ttt."_payment 
    LEFT JOIN gks_payment_acquirers ON gks_".$ttt."_payment.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer)
    LEFT JOIN gks_aade_tropoi_pliromis ON gks_payment_acquirers.aade_tropos_pliromis_id = gks_aade_tropoi_pliromis.id_aade_tropos_pliromis
    WHERE gks_".$ttt."_payment.".$ttt."_id=".$id."
    and gks_".$ttt."_payment.poso<>0
    order by gks_".$ttt."_payment.pp";
    $result_payments = $db_link->query($sql_payments); 
    if (!$result_payments) {debug_mail(false,'error sql',$sql_payments);$ret['message']='sql error'; return $ret;}
  
    
    while ($pa_row = $result_payments->fetch_assoc()) {
  
      if (floatval($pa_row['poso'])==0) {
        $ret['message']=gks_lang('O τρόπος πληρωμής [1] δεν έχει ποσό');
        $ret['message']=str_replace('[1]',$pa_row['payment_acquirer_name'],$ret['message']);
        debug_mail(false,$ret['message'],$sql_payments);return $ret;}
      if (intval($pa_row['aade_tropos_pliromis_code'])==0) {
        $ret['message']=gks_lang('O τρόπος πληρωμής [1] δεν έχει κωδικό για ΑΑΔΕ');
        $ret['message']=str_replace('[1]',$pa_row['payment_acquirer_name'],$ret['message']);
        debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        
      $pay_item=array(
        'type'=>intval($pa_row['aade_tropos_pliromis_code']),
        'amount'=>number_format($pa_row['poso'],2,'.',''),
      );
      $payment_acquirer_name=trim_gks($pa_row['payment_acquirer_name']);
      if ($payment_acquirer_name!='') $pay_item['paymentMethodInfo']=$payment_acquirer_name;
      $struct_data['xml']['paymentMethods'][]=$pay_item;
  
    }	
  }
  
  //echo '<pre>dddddddddd111 a ';print_r($struct_data['xml']['paymentMethods']);die();
  
  
  
  
  
  if ($doc_table=='gks_acc_inv') {
  
    $sql_products="SELECT gks_acc_inv_products.*, 
    gks_aade_eidos_posotitas.aade_eidos_posotitas_code, gks_aade_katigoria_fpa.aade_katigoria_fpa_code,
    gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_code,
    gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_code,
    gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr,
    gks_aade_katigoria_xartosimou.xartosimou_peppol_code,
    gks_aade_katigoria_telon.aade_katigoria_telon_code,
    gks_aade_katigoria_telon.aade_katigoria_telon_descr,
    gks_aade_katigoria_telon.telon_peppol_code,
    gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_code,
    gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr,
    gks_aade_katigoria_loipon_foron.loipon_foron_peppol_code,
    
    gks_aade_katigoria_fpa_ejeresi.aade_katigoria_fpa_ejeresi_code,
    gks_aade_katigoria_fpa_ejeresi.fpa_ejeresi_peppol_code,gks_aade_katigoria_fpa_ejeresi.aade_katigoria_fpa_ejeresi_descr,
    
    gks_monades_metrisis.monada_descr,
    gks_monades_metrisis.monada_peppol_code,
    gks_eshop_products.product_code
    FROM (((((((((gks_acc_inv_products 
    LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product) 
    LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada) 
    LEFT JOIN gks_aade_eidos_posotitas ON gks_monades_metrisis.aade_eidos_posotitas_id = gks_aade_eidos_posotitas.id_aade_eidos_posotitas) 
    LEFT JOIN gks_eshop_fpa ON gks_acc_inv_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
    LEFT JOIN gks_aade_katigoria_fpa ON gks_eshop_fpa.aade_katigoria_fpa_id = gks_aade_katigoria_fpa.id_aade_katigoria_fpa)
    LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_acc_inv_products.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron)
    LEFT JOIN gks_aade_katigoria_xartosimou ON gks_acc_inv_products.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou)
    LEFT JOIN gks_aade_katigoria_telon ON gks_acc_inv_products.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon)
    LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_acc_inv_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron)
    LEFT JOIN gks_aade_katigoria_fpa_ejeresi ON gks_acc_inv_products.product_fpa_ejeresi_id = gks_aade_katigoria_fpa_ejeresi.id_aade_katigoria_fpa_ejeresi
    where gks_acc_inv_products.acc_inv_id=".$id."
    ORDER BY gks_acc_inv_products.product_aa";
    $result_products = $db_link->query($sql_products); 
    if (!$result_products) {debug_mail(false,'error sql',$sql_products);$ret['message']='sql error'; return $ret;}
    
    $struct_data['prow_array']=array();
    $struct_data['prow_ids']=array();
    while ($prow = $result_products->fetch_assoc()) {
      $struct_data['prow_array'][]=$prow;
      $struct_data['prow_ids'][]=$prow['id_acc_inv_product'];
    }
    
    $struct_data['pirow_array']=array();
    $struct_data['perow_array']=array();
    if (count($struct_data['prow_ids'])>0) {
      $sql_products_income="SELECT gks_acc_inv_products_income.*, 
      gks_aade_typos_xarakt_esodon.aade_typos_xarakt_esodon_code, gks_aade_katigoria_xarakt_esodon.aade_katigoria_xarakt_esodon_code
      FROM (gks_acc_inv_products_income 
      LEFT JOIN gks_aade_typos_xarakt_esodon ON gks_acc_inv_products_income.aade_typos_xarakt_esodon_id = gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon) 
      LEFT JOIN gks_aade_katigoria_xarakt_esodon ON gks_acc_inv_products_income.aade_katigoria_xarakt_esodon_id = gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon
      WHERE gks_acc_inv_products_income.acc_inv_product_id In (".implode(',',$struct_data['prow_ids']).")
      order by id_acc_inv_product_income";
      $result_products_income = $db_link->query($sql_products_income); 
      if (!$result_products_income) {debug_mail(false,'error sql',$sql_products_income);$ret['message']='sql error'; return $ret;}
      while ($pirow = $result_products_income->fetch_assoc()) {
        if (isset($struct_data['pirow_array'][$pirow['acc_inv_product_id']])==false) $struct_data['pirow_array'][$pirow['acc_inv_product_id']]=array();
        $struct_data['pirow_array'][$pirow['acc_inv_product_id']][] = array(
          'classificationType' => (trim_gks($pirow['aade_typos_xarakt_esodon_code']) != '' ? trim_gks($pirow['aade_typos_xarakt_esodon_code']) : ''),
          'classificationCategory' => (trim_gks($pirow['aade_katigoria_xarakt_esodon_code']) != '' ? trim_gks($pirow['aade_katigoria_xarakt_esodon_code']) : ''),
          'amount' => $pirow['acc_inv_product_income_ammount'],
        );
      }
      
      $sql_products_expenses="SELECT gks_acc_inv_products_expenses.*, 
      gks_aade_typos_xarakt_eksodon.aade_typos_xarakt_eksodon_code, gks_aade_katigoria_xarakt_eksodon.aade_katigoria_xarakt_eksodon_code
      FROM (gks_acc_inv_products_expenses 
      LEFT JOIN gks_aade_typos_xarakt_eksodon ON gks_acc_inv_products_expenses.aade_typos_xarakt_eksodon_id = gks_aade_typos_xarakt_eksodon.id_aade_typos_xarakt_eksodon) 
      LEFT JOIN gks_aade_katigoria_xarakt_eksodon ON gks_acc_inv_products_expenses.aade_katigoria_xarakt_eksodon_id = gks_aade_katigoria_xarakt_eksodon.id_aade_katigoria_xarakt_eksodon
      WHERE gks_acc_inv_products_expenses.acc_inv_product_id In (".implode(',',$struct_data['prow_ids']).")
      order by id_acc_inv_product_expenses";
      $result_products_expenses = $db_link->query($sql_products_expenses); 
      if (!$result_products_expenses) {debug_mail(false,'error sql',$sql_products_expenses);$ret['message']='sql error'; return $ret;}
      while ($perow = $result_products_expenses->fetch_assoc()) {
        if (isset($struct_data['perow_array'][$perow['acc_inv_product_id']])==false) $struct_data['perow_array'][$perow['acc_inv_product_id']]=array();
        
        $struct_data['perow_array'][$perow['acc_inv_product_id']][] = array(
          'classificationType' => (trim_gks($perow['aade_typos_xarakt_eksodon_code']) != '' ? trim_gks($perow['aade_typos_xarakt_eksodon_code']) : ''), 
          'classificationCategory' => (trim_gks($perow['aade_katigoria_xarakt_eksodon_code']) != '' ? trim_gks($perow['aade_katigoria_xarakt_eksodon_code']) : ''),  
          'amount' => $perow['acc_inv_product_expenses_ammount'],
        );
      }    
    }
    //print '<pre>';print_r($pirow_array);print_r($perow_array);die();
    
    
  
    $struct_data['income_sum_array']=array();
    $struct_data['expenses_sum_array']=array();
  
    $lineNumber=0;
    foreach ($struct_data['prow_array'] as &$prow) {
      $lineNumber++;
      $prow['xml_lineNumber']=$lineNumber;
      
      //print '<pre>';
      if ($struct_data['eidos_parastatikou_aade_code']=='8.2') {
        //xoris quantity
      } else {
        //if ($struct_data['eidos_parastatikou_has_posotita']!=0) {
          $prow['xml_quantity']=$prow['product_quantity'];
          
          
          if (isset($prow['aade_eidos_posotitas_code'])) {
          	$prow['xml_measurementUnit']=$prow['aade_eidos_posotitas_code'];
          	
          } else {
          	$ret['message']=gks_lang('Δεν έχει ορισθεί κωδικός μονάδας μέτρησης για ΑΑΔΕ στην γραμμή').' '.$lineNumber;
          	debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
          	
          }
        //}
      }
      
      $product_descr=trim_gks($prow['product_descr']);
      if ($product_descr!='') $prow['xml_product_descr']=$product_descr;
      
      $product_comments=trim_gks($prow['product_comments']);
  		if ($product_comments!='') $prow['xml_product_comments']=$product_comments;
  
      if ($struct_data['eidos_parastatikou_aade_code']=='8.2') {
        $prow['product_price_final_all_net']=0;
        $prow['product_price_final_all_total']=0;
      } 
      
      $prow['aade_katigoria_fpa_code']=(isset($prow['aade_katigoria_fpa_code']) ? intval($prow['aade_katigoria_fpa_code']) : 0);
      //if ($aade_katigoria_fpa_code==0) $aade_katigoria_fpa_code=7; //miden
      
      if ($prow['aade_katigoria_fpa_code']==0 and $struct_data['eidos_parastatikou_aade_code']=='1.2') { //Timologio Polisis / Endokoinotikes Paradoseis
        $prow['aade_katigoria_fpa_code']=7;
      }
      if ($prow['aade_katigoria_fpa_code']==0 and $struct_data['eidos_parastatikou_aade_code']=='1.3') { //Timologio Polisis / Paradoseis Triton Choron
        $prow['aade_katigoria_fpa_code']=7;
      }
      if ($prow['aade_katigoria_fpa_code']==0 and $struct_data['eidos_parastatikou_aade_code']=='3.1') { //Titlos Ktisis (mi ypochreos Ekdotis)
        $prow['aade_katigoria_fpa_code']=8;
      }
      if ($prow['aade_katigoria_fpa_code']==0 and $struct_data['eidos_parastatikou_aade_code']=='8.1') { //Enoikia - Esodo
        $prow['aade_katigoria_fpa_code']=8;
      }
      if ($prow['aade_katigoria_fpa_code']==0 and $struct_data['eidos_parastatikou_aade_code']=='8.2') { //Eidiko Stoicheio - Apodeixis Eispraxis Forou Diamonis
        $prow['aade_katigoria_fpa_code']=8;
      }
      
      if ($prow['aade_katigoria_fpa_code']<=0) {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία ΦΠΑ της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; debug_mail(false,$ret['message'],''); return $ret;}
      //if ($prow['aade_katigoria_fpa_code']>0)
      $prow['xml_vatCategory']=$prow['aade_katigoria_fpa_code'];
      
      //if ($prow['product_price_final_all_fpa']!=0)
      $prow['xml_vatAmount']=$prow['product_price_final_all_fpa'];
      
      if ($prow['aade_katigoria_fpa_ejeresi_code']!=0) {
        $prow['xml_vatExemptionCategory']=$prow['aade_katigoria_fpa_ejeresi_code'];
      }    
  
        //vatExemptionCategory
        //dienergia
        
        if ($prow['product_withheldAmount']!=0) {
          $prow['xml_withheldAmount']=floatval($prow['product_withheldAmount']);
          $prow['xml_withheldPercentCategory']=$prow['aade_katigoria_parakratoumemenon_foron_code'];
        }
          
        if ($prow['product_stampDutyAmount']!=0) {
          $prow['xml_stampDutyAmount']=floatval($prow['product_stampDutyAmount']);
          $prow['xml_stampDutyPercentCategory']=$prow['aade_katigoria_xartosimou_code'];
        }
          
        if ($prow['product_feesAmount']!=0) {
          $prow['xml_feesAmount']=floatval($prow['product_feesAmount']);
          $prow['xml_feesPercentCategory']=$prow['aade_katigoria_telon_code'];
        }
          
        if ($prow['product_otherTaxesAmount']!=0) {
          $prow['xml_otherTaxesPercentCategory']=$prow['aade_katigoria_loipon_foron_code'];
          $prow['xml_otherTaxesAmount']=floatval($prow['product_otherTaxesAmount']);
        }
        
        if ($prow['product_deductionsAmount']!=0) {
          $prow['xml_deductionsAmount']=floatval($prow['product_deductionsAmount']);
        }
  
  			$prow['xml_incomeClassification']=array();
        if (isset($struct_data['pirow_array'][$prow['id_acc_inv_product']])) { //incomeClassification
          foreach ($struct_data['pirow_array'][$prow['id_acc_inv_product']] as $value) {
            
            
            $cl_item=array();
            if ($value['classificationType']!='e3_null') {
              $cl_item['type']=$value['classificationType'];
            }
            if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; debug_mail(false,$ret['message']); return $ret;}
            if ($value['classificationCategory']!='category_vat') {
              $cl_item['category']=$value['classificationCategory'];
            }
            
            if ($struct_data['eidos_parastatikou_aade_code']=='8.2') {
              $value['amount']=0;
            }
            
            
            $cl_item['amount']=$value['amount'];
            
            $prow['xml_incomeClassification'][]=$cl_item;
            
            $sum_key=$value['classificationType'].'||'.$value['classificationCategory'];
            //echo $sum_key.'||';
            if (isset($struct_data['income_sum_array'][$sum_key])==false) {
              $struct_data['income_sum_array'][$sum_key]=array(
                'classificationType' => $value['classificationType'],
                'classificationCategory' => $value['classificationCategory'],
                'amount'=>0,
              );
            }
            
            $struct_data['income_sum_array'][$sum_key]['amount']+=$value['amount'];
          }
        }
        
        $prow['xml_expensesClassification']=array();
        if (isset($struct_data['perow_array'][$prow['id_acc_inv_product']])) { //expensesClassification
          foreach ($struct_data['perow_array'][$prow['id_acc_inv_product']] as $value) {
            
            $cl_item=array();
            if ($value['classificationType']!='e3_null') {
              $cl_item['type']=$value['classificationType'];
            }
            if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; debug_mail(false,$ret['message']); return $ret;}
            if ($value['classificationCategory']!='category_vat') {
              $cl_item['category']=$value['classificationCategory'];
            }
            $cl_item['amount']=$value['amount'];
          
            $prow['xml_expensesClassification'][]=$cl_item;
            
            $sum_key=$value['classificationType'].'||'.$value['classificationCategory'];
            //echo $sum_key.'||';
            if (isset($struct_data['expenses_sum_array'][$sum_key])==false) {
              $struct_data['expenses_sum_array'][$sum_key]=array(
                'classificationType' => $value['classificationType'],
                'classificationCategory' => $value['classificationCategory'],
                'amount'=>0,
              );
            }
            $struct_data['expenses_sum_array'][$sum_key]['amount']+=$value['amount'];
          }
        } 
        
        
        if (count($prow['xml_incomeClassification'])==0 and 
            count($prow['xml_expensesClassification'])==0) {
          $ret['message']=gks_lang('Δεν βρέθηκαν Χαρακτηρισμοί Εσόδων ή Εξόδων στο είδος').' <b>'.$product_descr.'</b>';
          debug_mail(false,'income-expenses-Classification',print_r($prow,true));
          return $ret;
        
        }
        
                  
    }
    unset($prow);
    //echo '<pre>dddddddddd111 b';print_r($struct_data);die();
    
  } else if ($doc_table=='gks_acc_pay') {
    $lineNumber=1;

    
    
    $struct_data['prow_array']=array();
    $struct_data['prow_ids']=array();
    $struct_data['pirow_array']=array();
    $struct_data['perow_array']=array();    
    
    $struct_data['prow_array'][0]=array(
      'xml_lineNumber' => 1,
      'xml_quantity' => 1,
      'xml_measurementUnit' => 1,
      'xml_product_descr'=>$struct_data['xml']['paymentMethods'][0]['paymentMethodInfo'],
      'xml_vatCategory'=> 8,
      'xml_vatAmount' => 0,
      'xml_netValue'=>$struct_data['xml']['paymentMethods'][0]['amount'],
      
    );
    $struct_data['prow_array'][0]['xml_incomeClassification']=array();
    $struct_data['prow_array'][0]['xml_incomeClassification'][]=array(
      'type'=> 'e3_null',
      'category' => 'category1_95',
      'amount' => $struct_data['xml']['paymentMethods'][0]['amount'],
    );
    $struct_data['prow_array'][0]['xml_expensesClassification']=array();
    
    $struct_data['income_sum_array']=array();
    $struct_data['income_sum_array']['e3_null||category1_95']=array(
      'classificationType' => 'e3_null',
      'classificationCategory' => 'category1_95',
      'amount' => $struct_data['xml']['paymentMethods'][0]['amount'],
    
    );
    
    $struct_data['expenses_sum_array']=array();

  } else if ($doc_table=='gks_whi_mov') {
  
    $sql_products="SELECT gks_whi_mov_products.*,
    gks_aade_eidos_posotitas.aade_eidos_posotitas_code,
    gks_monades_metrisis.monada_descr,
    gks_monades_metrisis.monada_peppol_code,
    gks_eshop_products.product_code
    FROM ((gks_whi_mov_products
    LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product)
    LEFT JOIN gks_monades_metrisis ON gks_whi_mov_products.product_monada_id = gks_monades_metrisis.id_monada)
    LEFT JOIN gks_aade_eidos_posotitas ON gks_monades_metrisis.aade_eidos_posotitas_id = gks_aade_eidos_posotitas.id_aade_eidos_posotitas

    where gks_whi_mov_products.whi_mov_id=".$id."
    ORDER BY gks_whi_mov_products.product_aa";
    $result_products = $db_link->query($sql_products); 
    if (!$result_products) {debug_mail(false,'error sql',$sql_products);$ret['message']='sql error'; return $ret;}
    
    $struct_data['prow_array']=array();
    $struct_data['prow_ids']=array();
    while ($prow = $result_products->fetch_assoc()) {
      $struct_data['prow_array'][]=$prow;
      $struct_data['prow_ids'][]=$prow['id_whi_mov_product'];
    }
    
    $struct_data['pirow_array']=array();
    $struct_data['perow_array']=array();

    //print '<pre>';print_r($pirow_array);print_r($perow_array);die();
    
    
  
    $struct_data['income_sum_array']=array();
    $struct_data['expenses_sum_array']=array();
  
    $lineNumber=0;
    foreach ($struct_data['prow_array'] as &$prow) {
      $lineNumber++;
      $prow['xml_lineNumber']=$lineNumber;
      
      //print '<pre>';
      if ($struct_data['eidos_parastatikou_aade_code']=='8.2') {
        //xoris quantity
      } else {
        //if ($struct_data['eidos_parastatikou_has_posotita']!=0) {
          $prow['xml_quantity']=$prow['product_quantity'];
          
          
          if (isset($prow['aade_eidos_posotitas_code'])) {
          	$prow['xml_measurementUnit']=$prow['aade_eidos_posotitas_code'];
          	
          } else {
          	$ret['message']=gks_lang('Δεν έχει ορισθεί κωδικός μονάδας μέτρησης για ΑΑΔΕ στην γραμμή').' '.$lineNumber;debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
          	
          }
        //}
      }
      
      $product_descr=trim_gks($prow['product_descr']);
      if ($product_descr!='') $prow['xml_product_descr']=$product_descr;
      
      $product_comments=trim_gks($prow['product_comments']);
  		if ($product_comments!='') $prow['xml_product_comments']=$product_comments;
  
      
      $prow['product_price_final_all_net']=0;
      $prow['product_price_final_all_total']=0;
       
      
      $prow['aade_katigoria_fpa_code']=0; //0 7 8 

      
      //if ($prow['aade_katigoria_fpa_code']<=0) {
      // $ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία ΦΠΑ της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; 
      // debug_mail(false,$ret['message'],''); return $ret;}
      $prow['xml_vatCategory']=$prow['aade_katigoria_fpa_code'];
      
      $prow['xml_vatAmount']=0;
      
      //if ($prow['aade_katigoria_fpa_ejeresi_code']!=0) {
      //  $prow['xml_vatExemptionCategory']=$prow['aade_katigoria_fpa_ejeresi_code'];
      //}    
  
			$prow['xml_incomeClassification']=array();
      
      
      $prow['xml_expensesClassification']=array();
        
        
                  
    }
    unset($prow);
    //echo '<pre>dddddddddd111 b';print_r($struct_data);die();
        
  }

  
  
  //echo '<pre>struct_data prow_array'."\n";print_r($struct_data);die();
  
  if ($lineNumber==0) {debug_mail(false,'error sql',$sql);
    $ret['message']=gks_lang('Δεν βρέθηκαν γραμμές στο παραστατικό'); 
    return $ret;}
  
  //echo '<pre>';print_r($income_sum_array);die();




    
  
  
  
  

  //$taxesTotals = $invoice->addChild('taxesTotals'); //TaxesType
  $struct_data['xml']['invoiceSummary']=array();
  

  if ($struct_data['eidos_parastatikou_aade_code']=='8.2') {
    $row['gks_price_net']=0;
    $row['gks_price_total']=$row['totalOtherTaxesAmount'];
  }
  
  
  //if ($row['gks_price_net']!=0) 
    
  if ($doc_table=='gks_acc_inv') {
      $struct_data['xml']['invoiceSummary']['totalNetValue']=floatval($row['gks_price_net']);
    //if ($row['gks_price_fpa']!=0) 
      $struct_data['xml']['invoiceSummary']['totalVatAmount']=floatval($row['gks_price_fpa']);
      $struct_data['xml']['invoiceSummary']['totalVatAmount_NetValue']=floatval($row['gks_price_netfpa']);
        
    //if ($row['totalWithheldAmount']!=0) 
      $struct_data['xml']['invoiceSummary']['totalWithheldAmount']=floatval($row['totalWithheldAmount']);
    //if ($row['totalFeesAmount']!=0) 
      $struct_data['xml']['invoiceSummary']['totalFeesAmount']=floatval($row['totalFeesAmount']);
    //if ($row['totalStampDutyamount']!=0) 
      $struct_data['xml']['invoiceSummary']['totalStampDutyAmount']=floatval($row['totalStampDutyamount']);
    //if ($row['totalOtherTaxesAmount']!=0) 
      $struct_data['xml']['invoiceSummary']['totalOtherTaxesAmount']=floatval($row['totalOtherTaxesAmount']);
    //if ($row['totalDeductionsAmount']!=0) 
      $struct_data['xml']['invoiceSummary']['totalDeductionsAmount']=floatval($row['totalDeductionsAmount']);
    //if ($row['gks_price_total']!=0) 
      $struct_data['xml']['invoiceSummary']['totalGrossValue']=floatval($row['gks_price_total']);
    
  } else if ($doc_table=='gks_acc_pay') {
    $struct_data['xml']['invoiceSummary']['totalNetValue']=$struct_data['xml']['paymentMethods'][0]['amount'];
    $struct_data['xml']['invoiceSummary']['totalVatAmount']=0;
    $struct_data['xml']['invoiceSummary']['totalVatAmount_NetValue']=$struct_data['xml']['invoiceSummary']['totalNetValue'];
  } else if ($doc_table=='gks_whi_mov') {
    $struct_data['xml']['invoiceSummary']['totalNetValue']=0;
    $struct_data['xml']['invoiceSummary']['totalVatAmount']=0;
    $struct_data['xml']['invoiceSummary']['totalVatAmount_NetValue']=0;
  }
  
  //echo '<pre>struct_data prow_array'."\n";print_r($struct_data);die();
  
	$struct_data['xml']['invoiceSummary']['incomeClassification']=array();
	
  //print '<pre>';print_r($income_sum_array); die();
  if (count($struct_data['income_sum_array'])>0) {
    foreach ($struct_data['income_sum_array'] as $value) {
      
      $in_item=array();
      
      if ($value['classificationType']!='e3_null') {
        $in_item['classificationType']=$value['classificationType'];
      }
      if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στην σύνοψη του παραστατικού'); debug_mail(false,$ret['message']); return $ret;}
      $in_item['classificationCategory']=$value['classificationCategory'];
      $in_item['amount']=$value['amount'];
    
    	$struct_data['xml']['invoiceSummary']['incomeClassification'][]=$in_item;
    	
    } 
    
  }



	$struct_data['xml']['invoiceSummary']['expensesClassification']=array();
  if (count($struct_data['expenses_sum_array'])>0) {
    foreach ($struct_data['expenses_sum_array'] as $value) {
      $ex_item=array();
      
      if ($value['classificationType']!='e3_null') {
        $ex_item['classificationType']=$value['classificationType'];
      }
      if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στην σύνοψη του παραστατικού'); debug_mail(false,$ret['message']); return $ret;}
      if ($value['classificationCategory']!='category_vat') {
        $ex_item['classificationCategory']=$value['classificationCategory'];
      }
      $ex_item['amount']=$value['amount'];
      
      
      $struct_data['xml']['invoiceSummary']['expensesClassification'][]=$ex_item;
    } 
  }

  //echo '<pre>struct_data prow_array 333'."\n";print_r($struct_data);die();
      
  //echo '<pre>dddddddddd111 c ';print_r($struct_data);die();

  

   
  $ret['struct_data']=$struct_data;
  $ret['message']='OK';
  $ret['success']=true;

  return $ret;

}


function gks_paroxos_invoice_xml_build($id,$paroxos_params,$struct_data) {
	//echo  '<pre>';print_r($struct_data); die();
/*
$paroxos_params:
Array
(
    [id_company_paroxos] => 10002
    [aade_paroxos_id] => 20
    [paroxos_send] => 1
    [paroxos_mydata_live] => 1
    [paroxos_branch] => 0
    [pc_username] => CC89D2F3193D433985A1
    [pc_password] => hi;0N=pxJh=oYog7fWqt
    [pc_key] => 1C73A73A1B9D49E38E64343DADDD0928
)
*/
  $ret = array('success' => false, 'message' => 'generic error');
	if ($id<=0) {$ret['message']=gks_lang('Δεν έχει ορισθεί το').' ID.';debug_mail(false,$ret['message'],''); return $ret;}


    
  //echo '<pre>doc_table '.$doc_table;die();
  
	if (is_array($paroxos_params)==false or isset($paroxos_params['id_company_paroxos'])==false or isset($paroxos_params['aade_paroxos_id'])==false or isset($paroxos_params['paroxos_send'])==false or 
		$paroxos_params['id_company_paroxos']<1 or $paroxos_params['aade_paroxos_id']<1 or $paroxos_params['paroxos_send']==false) {
		$ret['message']=gks_lang('Δεν μπορεί να γίνει αποστολή σε πάροχο αυτού του παραστατικού γιατί δεν έχουν ορισθεί σωστά όλες οι παράμετροι'); debug_mail(false,$ret['message']); return $ret;
  }
  
  if (is_array($struct_data)==false or 
      isset($struct_data['row'])==false or 
      isset($struct_data['xml'])==false or 
      isset($struct_data['doc_table'])==false) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build - 1)'; debug_mail(false,$ret['message']); return $ret;
  }
  $doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv' and (
      isset($struct_data['row']['id_acc_inv'])==false or 
      $id<>$struct_data['row']['id_acc_inv'])) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build sign - 2)'; debug_mail(false,$ret['message']); return $ret;
  }
  if ($doc_table=='gks_acc_pay' and (
      isset($struct_data['row']['id_acc_pay'])==false or 
      $id<>$struct_data['row']['id_acc_pay'])) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build sign - 3)'; debug_mail(false,$ret['message']); return $ret;
  }    
  if ($doc_table=='gks_whi_mov' and (
      isset($struct_data['row']['id_whi_mov'])==false or 
      $id<>$struct_data['row']['id_whi_mov'])) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build sign - 4)'; debug_mail(false,$ret['message']); return $ret;
  }    

  
	
	switch ($paroxos_params['aade_paroxos_id']) {   
    case 8: //ilyda_com
      $ret=gks_paroxos_invoice_xml_build_ilyda_com($id,$paroxos_params,$struct_data);
      break;
    
    case 16: //tesae_gr
      $ret=gks_paroxos_invoice_xml_build_tesae_gr($id,$paroxos_params,$struct_data);
      break;
    case 20: //parochos_gr
      $ret=gks_paroxos_invoice_xml_build_parochos_gr($id,$paroxos_params,$struct_data);
      break;
    default:
    	$ret['message']=gks_lang('Δεν έχει γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο'); 
      break;
  }
  //echo '<pre>fffffffffffffffff 22wwwsss';die();

  if ($ret['success']==false) {
    debug_mail(false,'error gks_paroxos_invoice_xml_build',$ret['message'].'<br>id: '.$id.'<br>paroxos_params: '.print_r($paroxos_params,true).'<br>struct_data: '.print_r($struct_data,true));
    return $ret;    
  }

  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;
}

function gks_paroxos_invoice_xml_send($id,$paroxos_params,$struct_data,$file_data) {
	//echo  '<pre>';print_r($paroxos_params); die();
/*
$paroxos_params:
Array
(
    [id_company_paroxos] => 10002
    [aade_paroxos_id] => 20
    [paroxos_send] => 1
    [paroxos_mydata_live] => 1
    [paroxos_branch] => 0
    [pc_username] => CC89D2F3193D433985A1
    [pc_password] => hi;0N=pxJh=oYog7fWqt
    [pc_key] => 1C73A73A1B9D49E38E64343DADDD0928
)
*/
  $ret = array('success' => false, 'message' => 'generic error');
	if ($id<=0) {$ret['message']=gks_lang('Δεν έχει ορισθεί το').' ID.';debug_mail(false,$ret['message'],''); return $ret;}

	
	if (is_array($paroxos_params)==false or isset($paroxos_params['id_company_paroxos'])==false or isset($paroxos_params['aade_paroxos_id'])==false or isset($paroxos_params['paroxos_send'])==false or 
		$paroxos_params['id_company_paroxos']<1 or $paroxos_params['aade_paroxos_id']<1 or $paroxos_params['paroxos_send']==false) {
		$ret['message']=gks_lang('Δεν μπορεί να γίνει αποστολή σε πάροχο αυτού του παραστατικού γιατί δεν έχουν ορισθεί σωστά όλες οι παράμετροι'); debug_mail(false,$ret['message']); return $ret;
  }

  if (is_array($struct_data)==false or 
      isset($struct_data['row'])==false or 
      isset($struct_data['xml'])==false or 
      isset($struct_data['doc_table'])==false) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build send - 1)'; debug_mail(false,$ret['message']); return $ret;
  }
    
  $doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv' and (
      isset($struct_data['row']['id_acc_inv'])==false or 
      $id<>$struct_data['row']['id_acc_inv'])) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build send - 2)'; debug_mail(false,$ret['message']); return $ret;
  }
  if ($doc_table=='gks_acc_pay' and (
      isset($struct_data['row']['id_acc_pay'])==false or 
      $id<>$struct_data['row']['id_acc_pay'])) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build send - 3)'; debug_mail(false,$ret['message']); return $ret;
  }
    

  
	//echo 'fffffddddddf';die();
	switch ($paroxos_params['aade_paroxos_id']) {   
    case 8: //ilyda_com
      $ret=gks_paroxos_invoice_xml_send_ilyda_com($id,$paroxos_params,$struct_data,$file_data);
      break;

    case 16: //tesae_gr
      $ret=gks_paroxos_invoice_xml_send_tesae_gr($id,$paroxos_params,$struct_data,$file_data);
      break;
    case 20: //parochos_gr
      $ret=gks_paroxos_invoice_xml_send_parochos_gr($id,$paroxos_params,$struct_data,$file_data);
      break;
    default:
    	$ret['message']=gks_lang('Δεν έχει γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο'); 
      break;
  }

  if ($ret['success']==false) {
    debug_mail(false,'error gks_paroxos_invoice_xml_send',$ret['message'].'<br>id: '.$id.'<br>paroxos_params: '.print_r($paroxos_params,true).'<br>struct_data: '.print_r($struct_data,true).'<br>file_data: '.print_r($file_data,true));
    return $ret;    
  }      

  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;
}


function gks_paroxos_invoice_xml_get_in_progress($doc_table,$ids=[], $aade_paroxos_id=[], $force=false) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $ret = array('success' => false, 'message' => 'generic error');
  /*
  parochos_gr status:
  The progress status of the procedure. InProgress = 0, Completed = 1, Failed = 2.
  */
  
  $sql="select id_acc_inv,company_id,company_sub_id,aade_paroxos_id,paroxos_processId,paroxos_status,
  paroxos_user_send,paroxos_date_send 
  from gks_acc_inv
  where paroxos_status=0
  and aade_paroxos_id>0
  and inv_state in ('090ekdosi','100payment')";
  if ($force==false) $sql.=" and paroxos_date_send < date_sub(now(), interval 5 minute)";
  if (count($ids)>0) $sql.=" and id_acc_inv in (".implode(',',$ids).")";
  if (count($aade_paroxos_id)>0) $sql.=" and aade_paroxos_id in (".implode(',',$aade_paroxos_id).")";
  $sql.=" order by id_acc_inv";
  
  $inv_array=[];

  $result = $db_link->query($sql);
  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  $inv_array=[];
  while ($row= $result->fetch_assoc()) {
    $inv_array[$row['id_acc_inv']]=$row;
  }
  //echo '<pre>';print_r($inv_array);die();
  if (count($ids)==1 and count($inv_array)!=1) {
    $ret['message']=gks_lang('Το παραστατικό δεν έχει τις απαραίτητες προϋποθέσεις').' (1)';
    $ret['success']=false;  
    return $ret;    
  }
    
  foreach ($inv_array as $inv_item) {

    $row_old=[];$products_old=[];$extra_address_old=[];
    gks_inv_sxolio_log_prepare($inv_item['id_acc_inv'],$row_old,$products_old,$extra_address_old);
    $gks_custom_prepare=gks_custom_table_item_prepare('gks_acc_inv',['from'=>'item']);
    $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 


  	switch ($inv_item['aade_paroxos_id']) {   
      case 20: //parochos_gr
        $ret=gks_paroxos_invoice_xml_get_in_progress_item_parochos_gr($doc_table,$inv_item);
        break;
      default:
      	$ret['message']=gks_lang('Δεν έχει γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο'); 
        break;
    }
    gks_inv_sxolio_log($inv_item['id_acc_inv'],$row_old,$products_old,$extra_address_old,gks_lang('Ενημέρωση από πάροχο').'<br>',[],$gks_custom_row_old);

    if ($ret['success']==false) {
      debug_mail(false,'error gks_paroxos_invoice_xml_get_in_progress',$ret['message'].'<br>inv_item: '.print_r($inv_item,true).'<br>ids: '.print_r($ids,true).'<br>aade_paroxos_id: '.print_r($aade_paroxos_id,true));
      return $ret;    
    }      
  } 

  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;
}


function gks_paroxos_invoice_xml_get_files($doc_table,$ids=[], $aade_paroxos_id=[], $force=false) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $ret = array('success' => false, 'message' => 'generic error');
  /*
  parochos_gr status:
  The progress status of the procedure. InProgress = 0, Completed = 1, Failed = 2.
  */

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
    
  $sql="select id_".$ttt.",company_id,company_sub_id,aade_paroxos_id,paroxos_processId,aade_invoiceuid
  from ".$doc_table."
  where paroxos_status=1
  and aade_paroxos_id>0";
  if ($force==false) $sql.=" and paroxos_get_files is null";
  if (count($ids)>0) $sql.=" and id_".$ttt." in (".implode(',',$ids).")";
  if (count($aade_paroxos_id)>0) $sql.=" and aade_paroxos_id in (".implode(',',$aade_paroxos_id).")";
  $sql.=" order by id_".$ttt;
  //echo $sql;die();
  $inv_array=[];

  $result = $db_link->query($sql);
  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  $inv_array=[];
  while ($row= $result->fetch_assoc()) {
    $inv_array[$row['id_'.$ttt]]=$row;
  }
  //echo '<pre>';print_r($inv_array);die();
  if (count($ids)==1 and count($inv_array)!=1) {
    $ret['message']=gks_lang('Το παραστατικό δεν έχει τις απαραίτητες προϋποθέσεις').' (2)';
    $ret['success']=false;  
    return $ret;    
  }
    
  foreach ($inv_array as $inv_item) {

  	switch ($inv_item['aade_paroxos_id']) {   
      case 8: //ilyda_com
        $ret=gks_paroxos_invoice_xml_get_files_item_ilyda_com($doc_table,$inv_item);
        break;
      case 20: //parochos_gr
        $ret=gks_paroxos_invoice_xml_get_files_item_parochos_gr($doc_table,$inv_item);
        break;
      default:
      	$ret['message']=gks_lang('Δεν έχει γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο'); 
        break;
    }
    if ($ret['success']==false) {
      debug_mail(false,'error gks_paroxos_invoice_xml_get_files',$ret['message'].'<br>inv_item: '.print_r($inv_item,true).'<br>ids: '.print_r($ids,true).'<br>aade_paroxos_id: '.print_r($aade_paroxos_id,true));
      return $ret;    
    }      
  } 

  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;
}

function gks_paroxos_invoice_xml_send_pdf($doc_table,$ids=[], $aade_paroxos_id=[],$from_print=[]) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $ret = array('success' => false, 'message' => 'generic error');
  /*
  parochos_gr status:
  The progress status of the procedure. InProgress = 0, Completed = 1, Failed = 2.
  */

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
  //echo '<pre>sssssssssssssss';die();
  
  $sql="select id_".$ttt.",company_id,company_sub_id,aade_paroxos_id,paroxos_processId,
  print_date,print_file_name,print_file_url,print_user_id,print_".$xxx."_state
  from gks_".$ttt."
  where paroxos_status=1
  and aade_paroxos_id>0
  and paroxos_send_pdf is null";
  if (isset($from_print['fromprint'])==false) $sql.=" and print_".$xxx."_state in ('090ekdosi','100payment') and print_file_name<>''";
  if (count($ids)>0) $sql.=" and id_".$ttt." in (".implode(',',$ids).")";
  if (count($aade_paroxos_id)>0) $sql.=" and aade_paroxos_id in (".implode(',',$aade_paroxos_id).")";
  $sql.=" order by id_".$ttt;
  


  $result = $db_link->query($sql);
  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  $xxx_array=[];
  while ($row= $result->fetch_assoc()) {
    $xxx_array[$row['id_'.$ttt]]=$row;
  }
  //echo '<pre>';print_r($xxx_array);die();
  if (count($ids)==1 and count($xxx_array)==1 and isset($from_print['fromprint'])) {
    $xxx_array[$ids[0]]['print_date']=$from_print['print_date'];
    $xxx_array[$ids[0]]['print_file_name']=$from_print['print_file_name'];
    $xxx_array[$ids[0]]['print_file_url']=$from_print['print_file_url'];
    $xxx_array[$ids[0]]['print_user_id']=$from_print['print_user_id'];
    $xxx_array[$ids[0]]['print_'.$xxx.'_state']=$from_print['print_'.$xxx.'_state'];
    
  }
  //echo '<pre>';print_r($xxx_array);die();
  
  if (count($ids)==1 and count($xxx_array)!=1) {
    $ret['message']=gks_lang('Το παραστατικό δεν έχει τις απαραίτητες προϋποθέσεις').' (3)';
    $ret['success']=false;  
    return $ret;    
  }
  
  foreach ($xxx_array as $xxx_item) {

  	switch ($xxx_item['aade_paroxos_id']) {   
      case 8: //ilyda_com
        $ret=gks_paroxos_invoice_xml_send_pdf_item_ilyda_com($doc_table,$xxx_item);
        break;
      case 20: //parochos_gr
        $ret=gks_paroxos_invoice_xml_send_pdf_item_parochos_gr($doc_table,$xxx_item);
        break;
      default:
      	$ret['message']=gks_lang('Δεν έχει γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο'); 
        break;
    }
    if ($ret['success']==false) {
      debug_mail(false,'error gks_paroxos_invoice_xml_send_pdf',$ret['message'].'<br>xxx_item: '.print_r($xxx_item,true).'<br>ids: '.print_r($ids,true).'<br>aade_paroxos_id: '.print_r($aade_paroxos_id,true));
      return $ret;    
    }
  } 
  
  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;
}

function gks_paroxos_invoice_get_docstate($doc_table,$ids=[], $aade_paroxos_id=[],$from_print=[]) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $ret = array('success' => false, 'message' => 'generic error');
  /*
  parochos_gr status:
  The progress status of the procedure. InProgress = 0, Completed = 1, Failed = 2.
  */

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
  //echo '<pre>sssssssssssssss';die();
  
  $sql="select id_".$ttt.",company_id,company_sub_id,aade_paroxos_id,paroxos_processId,
  
  aade_invoiceuid,aade_invoicemark,
  aade_qrurl,aade_paroxos_qrurl,aade_statuscode,aade_errors,aade_send_date,aade_sending
  from gks_".$ttt."
  where paroxos_status=1
  and aade_paroxos_id>0
  and aade_invoiceuid<>''
  and (aade_invoicemark is null or aade_invoicemark='')
  and aade_send_date is not null";
  
  if (count($ids)>0) $sql.=" and id_".$ttt." in (".implode(',',$ids).")";
  if (count($aade_paroxos_id)>0) $sql.=" and aade_paroxos_id in (".implode(',',$aade_paroxos_id).")";
  $sql.=" order by id_".$ttt;
  


  $result = $db_link->query($sql);
  if (!$result) {$ret['message']='sql error';debug_mail(false,$ret['message'],$sql.' '.$db_link->errno . '-'.$db_link->error); return $ret;}
  $xxx_array=[];
  while ($row= $result->fetch_assoc()) {
    $row['id']=$row['id_'.$ttt];
    $xxx_array[$row['id_'.$ttt]]=$row;
  }
  //echo '<pre>';print_r($xxx_array);die();

  if (count($ids)==1 and count($xxx_array)!=1) {
    $ret['message']=gks_lang('Το παραστατικό δεν έχει τις απαραίτητες προϋποθέσεις').' (4)';
    $ret['success']=false;  
    return $ret;    
  }
  //echo '<pre>';print_r($xxx_array);die();
  
  foreach ($xxx_array as $xxx_item) {

  	switch ($xxx_item['aade_paroxos_id']) {   
      case 8: //ilyda_com
        $ret=gks_paroxos_invoice_get_docstate_ilyda_com($doc_table,$xxx_item);
        break;
      default:
      	$ret['message']=gks_lang('Δεν έχει γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο'); 
        break;
    }
    if ($ret['success']==false) {
      debug_mail(false,'error gks_paroxos_invoice_get_docstate',$ret['message'].'<br>xxx_item: '.print_r($xxx_item,true).'<br>ids: '.print_r($ids,true).'<br>aade_paroxos_id: '.print_r($aade_paroxos_id,true));
      return $ret;    
    }
  } 
  $ret['success']=true;  
  return $ret;
}

function gks_paroxos_payment_sign($id,$paroxos_params,$struct_data) {
  $ret = array('success' => false, 'message' => 'generic error');
	if ($id<=0) {$ret['message']=gks_lang('Δεν έχει ορισθεί το').' ID.';debug_mail(false,$ret['message'],''); return $ret;}

	
	if (is_array($paroxos_params)==false or isset($paroxos_params['id_company_paroxos'])==false or isset($paroxos_params['aade_paroxos_id'])==false or isset($paroxos_params['paroxos_send'])==false or 
		$paroxos_params['id_company_paroxos']<1 or $paroxos_params['aade_paroxos_id']<1 or $paroxos_params['paroxos_send']==false) {
		$ret['message']=gks_lang('Δεν μπορεί να γίνει αποστολή σε πάροχο αυτού του παραστατικού γιατί δεν έχουν ορισθεί σωστά όλες οι παράμετροι'); debug_mail(false,$ret['message']); return $ret;
  }
  
  
  
  if (is_array($struct_data)==false or 
      isset($struct_data['row'])==false or 
      isset($struct_data['xml'])==false or 
      isset($struct_data['doc_table'])==false) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build sign - 1)'; debug_mail(false,$ret['message']); return $ret;
  }

  $doc_table=$struct_data['doc_table'];
  if ($doc_table=='gks_acc_inv' and (
      isset($struct_data['row']['id_acc_inv'])==false or 
      $id<>$struct_data['row']['id_acc_inv'])) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build sign - 2)'; debug_mail(false,$ret['message']); return $ret;
  }
  if ($doc_table=='gks_acc_pay' and (
      isset($struct_data['row']['id_acc_pay'])==false or 
      $id<>$struct_data['row']['id_acc_pay'])) {
		$ret['message']=gks_lang('Δεν έχουν ορισθεί σωστά όλα τα δεδομένα').' (build sign - 3)'; debug_mail(false,$ret['message']); return $ret;
  }
  
  //echo '<pre>ssssssssss ';print_r($struct_data);die();
	
	switch ($paroxos_params['aade_paroxos_id']) {   
    case 8: //ilyda_com
      $ret=gks_paroxos_payment_sign_ilyda_com($id,$paroxos_params,$struct_data);
      break;
    case 16: //tesae_gr
      $ret=gks_paroxos_payment_sign_tesae_gr($id,$paroxos_params,$struct_data);
      break;
    //case 20: //parochos_gr
    //  $ret=gks_paroxos_payment_sign_parochos_gr($id,$paroxos_params,$struct_data);
    //  break;
    default:
    	$ret['message']=gks_lang('Δεν έχει γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο'); 
      break;
  }
  //echo '<pre>fffffffffffffffff 22wwwsss';die();

  if ($ret['success']==false) {
    debug_mail(false,'error gks_paroxos_payment_sign',$ret['message'].'<br>id: '.$id.'<br>paroxos_params: '.print_r($paroxos_params,true).'<br>struct_data: '.print_r($struct_data,true));
    return $ret;    
  }
  
  
  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;  
}

function gks_paroxos_get_signature_data($signature_data) {
  $ret = array('success' => false, 'message' => 'generic error get_signature_data');
  switch ($signature_data['id_aade_paroxos']) {   
    case 8: //ylida
      $ret=gks_paroxos_get_signature_data_ilyda_com($signature_data);
      break;
    default:
    	$ret['message']=gks_lang('Δεν έχει γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο').' get_signature_data'; 
      break;    
  }
  
  return $ret; 
}


function gks_paroxos_overview_get_afms($paroxos_id) {
  global $db_link;

  $sql="SELECT gks_company.company_afm, gks_company_1.company_afm as company_afm1,
  pc_username,pc_password,paroxos_mydata_live
  FROM ((gks_company_paroxos 
  LEFT JOIN gks_company ON gks_company_paroxos.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_company_paroxos.company_sub_id = gks_company_subs.id_company_sub) 
  LEFT JOIN gks_company AS gks_company_1 ON gks_company_subs.company_id = gks_company_1.id_company
  WHERE gks_company_paroxos.paroxos_send=1
  AND (gks_company.company_afm<>'' or gks_company_1.company_afm<>'')
  AND gks_company_paroxos.aade_paroxos_id=".$paroxos_id."
  and pc_username<>''
  and pc_password<>''";
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $afms=[];
  while ($row = $result->fetch_assoc()) {
    $paroxos_mydata_live=intval($row['paroxos_mydata_live'])==1;
    $ccafm=trim_gks($row['company_afm']);
    if ($ccafm!='' and isset($afms[$ccafm])==false) $afms[$ccafm]=array('afm'=>$ccafm,'username'=>$row['pc_username'],'password'=>$row['pc_password'],'paroxos_mydata_live'=>$paroxos_mydata_live);
    $ccafm=trim_gks($row['company_afm1']);
    if ($ccafm!='' and isset($afms[$ccafm])==false) $afms[$ccafm]=array('afm'=>$ccafm,'username'=>$row['pc_username'],'password'=>$row['pc_password'],'paroxos_mydata_live'=>$paroxos_mydata_live);
  }
  //print '<pre>afmsafmsafms ';print_r($afms);die();
  
  return $afms;  
}


function gks_paroxos_get_keys($paroxos_id) {
  $ret = array('success' => false, 'message' => 'generic error gks_paroxos_get_keys');
	if ($paroxos_id<=0) {$ret['message']=gks_lang('Δεν έχει ορισθεί το').' paroxos_id';debug_mail(false,$ret['message'],''); return $ret;}

	switch ($paroxos_id) {
    case 8: //ilyda_com
      $ret=gks_paroxos_get_keys_ilyda_com();
      break;
    default:
    	$ret['message']=gks_lang('Δεν έχει γίνει ακόμα η υλοποίηση για αυτόν τον πάροχο'); 
      break;
  }
  //echo '<pre>fffffffffffffffff 22wwwsss';die();

  if ($ret['success']==false) {
    debug_mail(false,'error gks_paroxos_get_keys',$ret['message'].'<br>paroxos_id: '.$paroxos_id);
    return $ret;    
  }
  
  
  $ret['message']='OK';
  $ret['success']=true;  
  return $ret;  
}
