<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_paroxos_invoice_xml_build_etimologiera_gr($id,$paroxos_params,$struct_data) {
	global $db_link;
  
	$ret = array('success' => false, 'message' => 'generic error');
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
	          '8.1','8.2','9.3','10.1','10.2'))) {
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
  $row=$struct_data['row'];
  $rrr_seira_id=intval($row[$rrr.'_seira_id']);
  $eidos_parastatikou_has_posotita=$row['eidos_parastatikou_has_posotita'];
  $eidos_parastatikou_need_afm=intval($row['eidos_parastatikou_need_afm']);
  $credit_memo_for_ttt_id=intval($row['credit_memo_for_'.$ttt.'_id']);
  if ($doc_table=='gks_acc_inv') $dimotikos_foros_for_ttt_id=intval($row['dimotikos_foros_for_'.$ttt.'_id']);
  $eidos_parastatikou_aade_code=$struct_data['eidos_parastatikou_aade_code'];
  $acc_eidos_parastatikou_whi_id=intval($row['acc_eidos_parastatikou_whi_id']);
  $seira_isdeliverynote=intval($row['seira_isdeliverynote']);
  $seira_is_reverse_delivery_note=intval($row['seira_is_reverse_delivery_note']);
  $reverse_delivery_purpose=0; 
  if (isset($row['reverse_delivery_purpose'])) $reverse_delivery_purpose=intval($row['reverse_delivery_purpose']);
  $seira_is_self_pricing=intval($row['seira_is_self_pricing']);
  $seira_is_vat_payment_suspension=intval($row['seira_is_vat_payment_suspension']);

  $aade_skopos_diakinisis_code=intval($row['aade_skopos_diakinisis_code']);
  $aade_skopos_19_descr=trim_gks($row['aade_skopos_19_descr']);
  
  $acc_eidos_parastatikou_other_entity=intval($row['acc_eidos_parastatikou_other_entity']);
  
  $afm=trim_gks($row['afm']);
  
  //echo '<pre>sssssssssssss '.$eidos_parastatikou_aade_code.'|'.$credit_memo_for_ttt_id ;die();
  
  $correlatedInvoices=[];

  if ($eidos_parastatikou_aade_code=='5.1') { //Pistotiko Timologio / Syschetizomeno
    if ($credit_memo_for_ttt_id<=0) {
      debug_mail(false,'error Pistotiko Timologio / Syschetizomeno',
       'eidos_parastatikou_aade_code: '.$eidos_parastatikou_aade_code.'<br>credit_memo_for_ttt_id: '.$credit_memo_for_ttt_id);
      $ret['message']=gks_lang('Δεν έχει ορισθεί το συσχετιζόμενο παραστατικό για αυτό το πιστωτικό παραστατικό'); return $ret;
    }
    //echo '<pre>sssssss'; die();
    
    $sql_credit_memo="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
    gks_acc_inv.aade_invoicemark
    FROM (((gks_acc_inv 
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
    where gks_acc_inv.id_acc_inv=".$credit_memo_for_ttt_id;
    $result_credit_memo = $db_link->query($sql_credit_memo);        
    if (!$result_credit_memo) {debug_mail(false,'error sql',$sql_credit_memo);die('sql error');}
    if ($result_credit_memo->num_rows==0) {
      $credit_memo_descr_for=gks_lang('Δεν βρέθηκε το συσχετιζόμενο παραστατικό με ID').': '.
      '<a href="admin-acc-inv-item.php?id='.$credit_memo_for_ttt_id.'" class="gks_link">'.$credit_memo_for_ttt_id.'</a>';
      debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not found',$credit_memo_descr_for.' '.$sql_credit_memo);
      $ret['message']=$credit_memo_descr_for; return $ret;

      //die('no record found (2)');
    } else {
      $row_credit_memo = $result_credit_memo->fetch_assoc();
    
      //$antisimvalomenos_label=$row_credit_memo['antisimvalomenos_label'];
      //$acc_eidos_parastatikou_id=intval($row_credit_memo['acc_eidos_parastatikou_id']);
      //$eidos_parastatikou_type_id=intval($row_credit_memo['eidos_parastatikou_type_id']);
      //$eidos_parastatikou_need_prev=intval($row_credit_memo['eidos_parastatikou_need_prev']);
      //$eidos_parastatikou_has_fpa=intval($row_credit_memo['eidos_parastatikou_has_fpa']);
      //$eidos_parastatikou_has_othertaxes=trim_gks($row_credit_memo['eidos_parastatikou_has_othertaxes']);
      //$eidos_parastatikou_has_esoda=intval($row_credit_memo['eidos_parastatikou_has_esoda']);
      //$eidos_parastatikou_has_eksoda=intval($row_credit_memo['eidos_parastatikou_has_eksoda']);
      $eidos_parastatikou_need_afm=intval($row_credit_memo['eidos_parastatikou_need_afm']);
      
      $aade_invoicemark=trim_gks($row_credit_memo['aade_invoicemark']);
      if ($aade_invoicemark=='') {
        $credit_memo_descr_for=gks_lang('Το συσχετιζόμενο παραστατικό με ID').': '.
        '<a href="admin-acc-inv-item.php?id='.$credit_memo_for_ttt_id.'" class="gks_link">'.$credit_memo_for_ttt_id.'</a><br>'.
        gks_lang('δεν έχει ΜΑΡΚ<br>Σίγουρα έχει σταλεί στην ΑΑΔΕ ;');
        debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not mark',$credit_memo_descr_for.' '.$sql_credit_memo);
        $ret['message']=$credit_memo_descr_for; return $ret;
      }
      
      
      $correlatedInvoices[]=trim_gks($aade_invoicemark);
      
      $sql_corri="SELECT gks_".$ttt."_correlated_invoices.coi_mark, gks_".$ttt.".aade_invoicemark
      FROM gks_".$ttt."_correlated_invoices 
      LEFT JOIN gks_".$ttt." ON gks_".$ttt."_correlated_invoices.coi_".$ttt."_id = gks_".$ttt.".id_".$ttt."
      where gks_".$ttt."_correlated_invoices.".$ttt."_id=".$id."
      ORDER BY gks_".$ttt."_correlated_invoices.coi_aa;";
      $result_corri = $db_link->query($sql_corri);        
      if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
      $coi_mark=[];
      while ($row_corri = $result_corri->fetch_assoc()) {
        $vvv=trim_gks($row_corri['coi_mark']);
        if ($vvv=='') $vvv=trim_gks($row_corri['aade_invoicemark']);
        if ($vvv!='') $coi_mark[]=$vvv;
      }
      $coi_mark=implode('|',$coi_mark);
      if ($coi_mark!=$correlatedInvoices[0]) {
        $ret['message']=gks_lang('Δεν βρέθηκε το ΜΑΡΚ <br><b>[1]</b><br>στα <b>Συσχετιζόμενα Παραστατικά</b>.<br>Βρέθηκαν τα: <br><b>[2]</b>');
        $ret['message']=str_replace('[1]',$correlatedInvoices[0],$ret['message']);
        $ret['message']=str_replace('[2]',$coi_mark,$ret['message']);
        debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not mark',$ret['message']);
        return $ret;}
      //echo '<pre>sssssss'; die();
      
    }    
    
  } else if ($eidos_parastatikou_aade_code=='8.2') { //Eidiko Stoicheio - Apodeixis Eispraxis Forou Diamonis
    if ($dimotikos_foros_for_ttt_id<=0) {
      debug_mail(false,'error Apodeixis Eispraxis Forou Diamonis',
       'eidos_parastatikou_aade_code: '.$eidos_parastatikou_aade_code.'<br>dimotikos_foros_for_ttt_id: '.$dimotikos_foros_for_ttt_id);
      $ret['message']=gks_lang('Δεν έχει ορισθεί το συσχετιζόμενο παραστατικό για αυτό το παραστατικό'); return $ret;
    }
    
    $sql_dimotikos_foros="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
    gks_acc_inv.aade_invoicemark
    FROM (((gks_acc_inv 
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
    where gks_acc_inv.id_acc_inv=".$dimotikos_foros_for_ttt_id;
    $result_dimotikos_foros = $db_link->query($sql_dimotikos_foros);        
    if (!$result_dimotikos_foros) {debug_mail(false,'error sql',$sql_dimotikos_foros);die('sql error');}
    if ($result_dimotikos_foros->num_rows==0) {
      $dimotikos_foros_descr_for=gks_lang('Δεν βρέθηκε το συσχετιζόμενο παραστατικό με ID').': '.
      '<a href="admin-acc-inv-item.php?id='.$dimotikos_foros_for_ttt_id.'" class="gks_link">'.$dimotikos_foros_for_ttt_id.'</a>';
      debug_mail(false,'error Apodeixis Eispraxis Forou Diamonis, record parent not found',$dimotikos_foros_descr_for.' '.$sql_dimotikos_foros);
      $ret['message']=$dimotikos_foros_descr_for; return $ret;

      //die('no record found (2)');
    } else {
      $row_dimotikos_foros = $result_dimotikos_foros->fetch_assoc();
    
      //$antisimvalomenos_label=$row_dimotikos_foros['antisimvalomenos_label'];
      //$acc_eidos_parastatikou_id=intval($row_dimotikos_foros['acc_eidos_parastatikou_id']);
      //$eidos_parastatikou_type_id=intval($row_dimotikos_foros['eidos_parastatikou_type_id']);
      //$eidos_parastatikou_need_prev=intval($row_dimotikos_foros['eidos_parastatikou_need_prev']);
      //$eidos_parastatikou_has_fpa=intval($row_dimotikos_foros['eidos_parastatikou_has_fpa']);
      //$eidos_parastatikou_has_othertaxes=trim_gks($row_dimotikos_foros['eidos_parastatikou_has_othertaxes']);
      //$eidos_parastatikou_has_esoda=intval($row_dimotikos_foros['eidos_parastatikou_has_esoda']);
      //$eidos_parastatikou_has_eksoda=intval($row_dimotikos_foros['eidos_parastatikou_has_eksoda']);
      //$eidos_parastatikou_need_afm=intval($row_dimotikos_foros['eidos_parastatikou_need_afm']);
      
      $aade_invoicemark=trim_gks($row_dimotikos_foros['aade_invoicemark']);
      if ($aade_invoicemark=='') {
        $dimotikos_foros_descr_for=gks_lang('Το συσχετιζόμενο παραστατικό με ID').': '.
        '<a href="admin-acc-inv-item.php?id='.$dimotikos_foros_for_ttt_id.'" class="gks_link">'.$dimotikos_foros_for_ttt_id.'</a><br>'.
        gks_lang('δεν έχει ΜΑΡΚ<br>Σίγουρα έχει σταλεί στην ΑΑΔΕ ;');
        debug_mail(false,'error Apodeixis Eispraxis Forou Diamonis, record parent not mark',$dimotikos_foros_descr_for.' '.$sql_dimotikos_foros);
        $ret['message']=$dimotikos_foros_descr_for; return $ret;
      }
      
      
      $correlatedInvoices[]=trim_gks($aade_invoicemark);
      
      $sql_corri="SELECT gks_".$ttt."_correlated_invoices.coi_mark, 
      gks_".$ttt.".aade_invoicemark
      FROM gks_".$ttt."_correlated_invoices 
      LEFT JOIN gks_".$ttt." ON gks_".$ttt."_correlated_invoices.coi_".$ttt."_id = gks_".$ttt.".id_".$ttt."
      where gks_".$ttt."_correlated_invoices.".$ttt."_id=".$id."
      ORDER BY gks_".$ttt."_correlated_invoices.coi_aa;";
      $result_corri = $db_link->query($sql_corri);        
      if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
      $coi_mark=[];
      while ($row_corri = $result_corri->fetch_assoc()) {
        $vvv=trim_gks($row_corri['coi_mark']);
        if ($vvv=='') $vvv=trim_gks($row_corri['aade_invoicemark']);
        if ($vvv!='') $coi_mark[]=$vvv;
      }
      $coi_mark=implode('|',$coi_mark);
      if ($coi_mark!=$correlatedInvoices[0]) {
        $ret['message']=gks_lang('Δεν βρέθηκε το ΜΑΡΚ <br><b>[1]</b><br>στα <b>Συσχετιζόμενα Παραστατικά</b>.<br>Βρέθηκαν τα: <br><b>[2]</b>');
        $ret['message']=str_replace('[1]',$correlatedInvoices[0],$ret['message']);
        $ret['message']=str_replace('[2]',$coi_mark,$ret['message']);
        debug_mail(false,'error Pistotiko Timologio / Syschetizomeno, record parent not mark',$ret['message']);
        return $ret;}
      //echo '<pre>sssssss'; die();
            
    }    
    
  } else {
    
    //echo '<pre>ggggggggggggggggggg'; die();
      
    $sql_corri="SELECT gks_".$ttt."_correlated_invoices.coi_mark, gks_".$ttt.".aade_invoicemark
    FROM gks_".$ttt."_correlated_invoices 
    LEFT JOIN gks_".$ttt." ON gks_".$ttt."_correlated_invoices.coi_".$ttt."_id = gks_".$ttt.".id_".$ttt."
    where gks_".$ttt."_correlated_invoices.".$ttt."_id=".$id."
    ORDER BY gks_".$ttt."_correlated_invoices.coi_aa;";
    $result_corri = $db_link->query($sql_corri);        
    if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
    $coi_mark=[];
    while ($row_corri = $result_corri->fetch_assoc()) {
      $vvv=trim_gks($row_corri['coi_mark']);
      if ($vvv=='') $vvv=trim_gks($row_corri['aade_invoicemark']);
      if ($vvv!='') $coi_mark[]=$vvv;
    }
    
    $correlatedInvoices=$coi_mark;
  }  
  //echo '<pre>sssssssssssss correlatedInvoices ';print_r($correlatedInvoices);die();
  
  $sql_corri="SELECT gks_".$ttt."_multiple_connected_marks.mcm_mark, 
  gks_".$ttt.".aade_invoicemark
  FROM gks_".$ttt."_multiple_connected_marks 
  LEFT JOIN gks_".$ttt." ON gks_".$ttt."_multiple_connected_marks.mcm_".$ttt."_id = gks_".$ttt.".id_".$ttt."
  where gks_".$ttt."_multiple_connected_marks.".$ttt."_id=".$id."
  ORDER BY gks_".$ttt."_multiple_connected_marks.mcm_aa;";
  $result_corri = $db_link->query($sql_corri);        
  if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
  $mcm_mark=[];
  while ($row_corri = $result_corri->fetch_assoc()) {
    $vvv=trim_gks($row_corri['mcm_mark']);
    if ($vvv=='') $vvv=trim_gks($row_corri['aade_invoicemark']);
    if ($vvv!='') $mcm_mark[]=$vvv;
  }
  $multipleConnectedMarks=$mcm_mark;
  //echo '<pre>sssssssssssss multipleConnectedMarks ';print_r($multipleConnectedMarks);die();
  
  $isDeliveryNote=false;
  if (in_array($eidos_parastatikou_aade_code,['9.1','9.2','9.3'])) {
    $isDeliveryNote=true;
  } else if ($acc_eidos_parastatikou_whi_id>0) {
    if ($seira_isdeliverynote!=0) $isDeliveryNote=true;
  }
  $reverseDeliveryNote=false;
  $reverseDeliveryNotePurpose=0;
  if (in_array($eidos_parastatikou_aade_code,['9.3'])) {
    if ($seira_is_reverse_delivery_note!=0) {
      $reverseDeliveryNote=true;
      $reverseDeliveryNotePurpose=$reverse_delivery_purpose;
    }
  }

  $is_endodiakinisi=false;
  if ($isDeliveryNote and 
        in_array($eidos_parastatikou_aade_code,['9.1','9.2','9.3','10.1','10.2']) and 
        $afm=='' and 
        in_array($aade_skopos_diakinisis_code,[8,18])) { //8->metaxy Enkatastaseon Ontotitas, 18=>diakinisi Pagion (Endodiakinisi)
    $is_endodiakinisi=true;
  }

  
    
  //echo '<pre>sssssssssssss3 ';print_r($struct_data);die();
	
  $uid_array=[];
  $adata=[];
  $adata['isUnsigned']=true;
	$adata['issuer']=[];

  
  $c_sub='';
  if (isset($row['company_sub_id']) and $row['company_sub_id']>0) $c_sub='sub_';
  
  $company_afm=trim_gks($row['company_afm']);
  if ($company_afm=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί το ΑΦΜ της εταιρείας σας'); debug_mail(false,$ret['message'],''); return $ret;}
  $adata['issuer']['vatNumber']=$company_afm;
  $uid_array['vatNumber']=$company_afm;
  
  $company_country_initials=trim_gks($row['company_'.$c_sub.'country_initials']);
  if ($company_country_initials=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί ο κωδικός χώρας της εταιρείας σας'); debug_mail(false,$ret['message'],''); return $ret;}
  $adata['issuer']['country']=$company_country_initials;
  
  if (isset($paroxos_params['paroxos_branch'])) {
    $adata['issuer']['branch']=$paroxos_params['paroxos_branch'];
    $uid_array['branch']=$adata['issuer']['branch'];
  }
  
  //delete me 1==1
  if ($company_country_initials!='GR' or $isDeliveryNote) { //check
    $adata['issuer']['name']=trim_gks($row['company_eponimia']);
    
    $adata['issuer']['address']=[];

    if ($row['company_sub_id']==0) { //kentriko
      $company_odos=trim_gks($row['company_odos']);
      if ($company_odos!='') $adata['issuer']['address']['street']=$company_odos;
      $company_arithmos=trim_gks($row['company_arithmos']);
      if ($company_arithmos!='') $adata['issuer']['address']['number']=$company_arithmos;
      $company_tk=trim_gks($row['company_tk']);
      if ($company_tk!='') $adata['issuer']['address']['postalCode']=$company_tk;
      $company_poli=trim_gks($row['company_poli']);
      if ($company_poli!='') $adata['issuer']['address']['city']=$company_poli;
    } else { //ypokatastima
      $company_sub_odos=trim_gks($row['company_sub_odos']);
      if ($company_sub_odos!='') $adata['issuer']['address']['street']=$company_sub_odos;
      $company_sub_arithmos=trim_gks($row['company_sub_arithmos']);
      if ($company_sub_arithmos!='') $adata['issuer']['address']['number']=$company_sub_arithmos;
      $company_sub_tk=trim_gks($row['company_sub_tk']);
      if ($company_sub_tk!='') $adata['issuer']['address']['postalCode']=$company_sub_tk;
      $company_sub_poli=trim_gks($row['company_sub_poli']);
      if ($company_sub_poli!='') $adata['issuer']['address']['city']=$company_sub_poli;
    }

  
  }
  //todo invoice[].issuer.documentIdNo
  //todo invoice[].issuer.supplyAccountNo
  //todo invoice[].issuer.countryDocumentId

  //delete me
  if ($eidos_parastatikou_need_afm!=0 or 
      ($eidos_parastatikou_aade_code=='11.1' and $isDeliveryNote)) {
    $adata['counterpart']=[];
    
    $party_country_initials=trim_gks($row['party_country_initials']);

    if ($is_endodiakinisi) {
      //echo '<pre>dddd';die();
      $adata['counterpart']['vatNumber']='000000000';

      
      if (isset($row['deli_country_id']) and intval($row['deli_country_id'])>0) {
        $sql_temp="select country_initials from gks_country where id_country=".intval($row['deli_country_id']);
        $result_temp = $db_link->query($sql_temp);        
        if (!$result_temp) {debug_mail(false,'error sql',$sql_temp);$ret['message']='sql error'; return $ret;}
        if ($result_temp->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η χώρα με κωδικό').' '.intval($row['deli_country_id']);debug_mail(false,$ret['message'],$sql_temp); return $ret;}
        $row_temp = $result_temp->fetch_assoc();
        $party_country_initials=$row_temp['country_initials'];
        //echo '<pre>dddd ssss'.$party_country_initials;die();
      }
      
//      if (isset($row['deli_odos'])) and trim_gks($row['deli_odos'])!='') $row['ma_odos']=$row['deli_odos'];
//      if (isset($row['deli_arithmos'])) and trim_gks($row['deli_arithmos'])!='') $row['ma_arithmos']=$row['deli_arithmos'];
//      if (isset($row['deli_tk'])) and trim_gks($row['deli_tk'])!='') $row['ma_tk']=$row['deli_tk'];
//      if (isset($row['deli_poli'])) and trim_gks($row['deli_poli'])!='') $row['ma_poli']=$row['deli_poli'];

      
      
    } else {
      if ($afm=='' and in_array($eidos_parastatikou_aade_code,['8.2','11.1'])==false) {
        $ret['message']=gks_lang('Δεν έχει ορισθεί το ΑΦΜ του πελάτη/προμηθευτή'); 
        debug_mail(false,$ret['message'],''); 
        return $ret;
      }
      if ($eidos_parastatikou_aade_code=='11.1') {
        $adata['counterpart']['vatNumber']='000000000';
      } else if ($eidos_parastatikou_aade_code=='8.2' and $afm=='') {
        $adata['counterpart']['vatNumber']='000000000';
      } else {
        $adata['counterpart']['vatNumber']=$afm;
      }
    }
  

    if ($party_country_initials=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί ο κωδικός χώρας του πελάτη/προμηθευτή'); debug_mail(false,$ret['message'],''); return $ret;}
    $adata['counterpart']['country']=$party_country_initials;
  
    $ma_branch=0;
    if (isset($row['ma_branch_user'])) {
      if (trim_gks($row['ma_branch_user'])!='') {
        $ma_branch=intval($row['ma_branch_user']);
        //echo '<pre>'.$ma_branch;die();
      }
    }
    //echo '<pre>'.$ma_branch;die();
    if ($is_endodiakinisi) {
      if (isset($row['deli_branch'])) {
        if (trim_gks($row['deli_branch'])!='') {
          $ma_branch=intval($row['deli_branch']);
        }
      }
    }
    //echo '<pre>|'.$ma_branch.'|';die();
    
    
    $adata['counterpart']['branch']=$ma_branch;
    
    if ($is_endodiakinisi) {

      $adata['counterpart']['name']=trim_gks($row['company_eponimia']);
      
      

      $adata['counterpart']['address']=[];
      $ma_odos=trim_gks($row['deli_odos']);
      if ($ma_odos!='') $adata['counterpart']['address']['street']=$ma_odos;
      $ma_arithmos=trim_gks($row['deli_arithmos']);
      if ($ma_arithmos!='') $adata['counterpart']['address']['number']=$ma_arithmos;
      $ma_tk=trim_gks($row['deli_tk']);
      if ($ma_tk!='') $adata['counterpart']['address']['postalCode']=$ma_tk;
      $ma_poli=trim_gks($row['deli_poli']);
      if ($ma_poli!='') $adata['counterpart']['address']['city']=$ma_poli;
      
    } else {
    
      //delete me 1==1
      if ($party_country_initials!='GR' or $isDeliveryNote) { //check
      //if ($party_country_initials!='GR') {
        $eponimia=trim_gks($row['eponimia']);
        
        if($eponimia != '') {
          $adata['counterpart']['name']=$eponimia;
        } else if ($eidos_parastatikou_aade_code=='11.1') {
          $pelatis_name=trim_gks(trim_gks($row['user_first_name']).' '.trim_gks($row['user_last_name']));
          $adata['counterpart']['name']=$pelatis_name;
        }
      }

      $adata['counterpart']['address']=[];
      $ma_odos=trim_gks($row['ma_odos']);
      if ($ma_odos!='') $adata['counterpart']['address']['street']=$ma_odos;
      $ma_arithmos=trim_gks($row['ma_arithmos']);
      if ($ma_arithmos!='') $adata['counterpart']['address']['number']=$ma_arithmos;
      $ma_tk=trim_gks($row['ma_tk']);
      if ($ma_tk!='') $adata['counterpart']['address']['postalCode']=$ma_tk;
      $ma_poli=trim_gks($row['ma_poli']);
      if ($ma_poli!='') $adata['counterpart']['address']['city']=$ma_poli;
      
      
    }
    
  
  }
  
  
  /* if (isset($xml['counterpart']['vatNumber'])) {
    $adata['counterpart']=[];
    $adata['counterpart']['vatNumber']=$xml['counterpart']['vatNumber'];
    if (isset($xml['counterpart']['country'])) {
      $adata['counterpart']['country']=$xml['counterpart']['country'];
    }
    
    $my_branch=0;
    
    if (isset($row['address_extra'])) {
      if (intval($row['address_extra'])<=0) {
        if (isset($row['user_id']) and intval($row['user_id'])>0) {
          $sql_branch="select ma_branch from gks_users 
          where user_id=".$row['user_id']."
          and ma_branch>=0";
          $result_branch = $db_link->query($sql_branch);        
          if (!$result_branch) {debug_mail(false,'error sql',$sql_branch);$ret['message']='sql error'; return $ret;}
          if ($result_branch->num_rows>=1) {
            $row_branch = $result_branch->fetch_assoc();
            $my_branch=intval($row_branch['ma_branch']);
          }
        }
      } else if (intval($row['address_extra'])>0) {
        $sql_branch="select ea_branch from gks_users_extra_address 
        where user_id=".$row['user_id']."
        and ea_branch>=0
        and id_users_extra_address=".$row['address_extra'];
        $result_branch = $db_link->query($sql_branch);        
        if (!$result_branch) {debug_mail(false,'error sql',$sql_branch);$ret['message']='sql error'; return $ret;}
        if ($result_branch->num_rows>=1) {
          $row_branch = $result_branch->fetch_assoc();
          $my_branch=intval($row_branch['ea_branch']);
        }
      }
    }
    $adata['counterpart']['branch']=$my_branch;

    //print '<pre>ssssssdddddddd';print_r($adata);die();
    
    
    if (isset($xml['counterpart']['country'])==false or $xml['counterpart']['country']!='GR') {
      if (isset($xml['counterpart']['name'])) {
        $adata['counterpart']['name']=$xml['counterpart']['name'];
      }
    }
    
    if (isset($xml['counterpart']['address']['city'])) {
      $adata['counterpart']['address']=[];
      if (isset($xml['counterpart']['address']['street'])) {
        $adata['counterpart']['address']['street']=$xml['counterpart']['address']['street'];
      }
      if (isset($xml['counterpart']['address']['number'])) {
        $adata['counterpart']['address']['number']=$xml['counterpart']['address']['number'];
      }
      if (isset($xml['counterpart']['address']['postalCode'])) {
        $adata['counterpart']['address']['postalCode']=$xml['counterpart']['address']['postalCode'];
      }
      $adata ['counterpart']['address']['city']=$xml['counterpart']['address']['city'];
    }
  } */
  
  //todo invoice[].counterpart.documentIdNo
  //todo invoice[].counterpart.supplyAccountNo
  //todo invoice[].counterpart.countryDocumentId

  
  $adata['invoiceHeader']=[];
  $rrr_seira_code=trim_gks($row[$rrr.'_seira_code']);
  if ($rrr_seira_code=='') {$ret['message']=gks_lang('Δεν βρέθηκε η σειρά'); debug_mail(false,$ret['message'],''); return $ret;}
  $adata['invoiceHeader']['series']=$rrr_seira_code;
  $uid_array['series']=$rrr_seira_code;

  $rrr_acc_number_int=intval($row[$rrr.'_number_int']);
  if ($rrr_acc_number_int<=0) {$ret['message']=gks_lang('Δεν βρέθηκε ο αριθμός του παραστατικού'); debug_mail(false,$ret['message'],''); return $ret;}
  $adata['invoiceHeader']['aa']=$rrr_acc_number_int.'';
  $uid_array['aa']=$rrr_acc_number_int;

  $xxx_date=trim_gks($row[$xxx.'_date']);
  if ($xxx_date=='') {$ret['message']=gks_lang('Δεν ορίσθηκε η ημερομηνία'); debug_mail(false,$ret['message'],''); return $ret;}
  $xxx_date_str=showDate(strtotime($xxx_date),'Y-m-d',1); 
  $adata['invoiceHeader']['issueDate']=$xxx_date_str;
  $uid_array['issueDate']=$xxx_date_str;


  $eidos_parastatikou_aade_code=trim_gks($row['eidos_parastatikou_aade_code']);
  if ($eidos_parastatikou_aade_code=='') {$ret['message']=gks_lang('Δεν βρέθηκε ο κωδικός ΑΑΔΕ για το παραστατικό'); debug_mail(false,$ret['message'],''); return $ret;}
  $adata['invoiceHeader']['invoiceType']=$eidos_parastatikou_aade_code;
  $uid_array['invoiceType']=$eidos_parastatikou_aade_code;

  $xxx_time_str=showDate(strtotime($xxx_date),'H:i:s',1);
  $adata['invoiceHeader']['issueTime']=$xxx_time_str;

  if ($seira_is_vat_payment_suspension==1) {
    $adata['invoiceHeader']['vatPaymentSuspension']=true;
  }

  if (in_array($doc_table,['gks_acc_inv','gks_acc_pay'])) {
    $adata['invoiceHeader']['currency']='EUR';
  }
  
  //todo invoice[].invoiceHeader.tableAA 
  //todo invoice[].invoiceHeader.totalCancelDeliveryOrders
  
  
  if (count($multipleConnectedMarks)>0) {
    $adata['invoiceHeader']['multipleConnectedMarks']=[];
    foreach ($multipleConnectedMarks as $vvv_item) {
      $adata['invoiceHeader']['multipleConnectedMarks'][]=intval($vvv_item);
    }
  }
  
  //todo invoice[].invoiceHeader.multipleConnectedClientRequestID
  


  if (count($correlatedInvoices)>0) {
    $adata['invoiceHeader']['correlatedInvoices']=[];
    foreach ($correlatedInvoices as $vvv_item) {
      $adata['invoiceHeader']['correlatedInvoices'][]=intval($vvv_item);
    }
  }
  
  //todo invoice[].invoiceHeader.exchangeRate
  
  if ($seira_is_self_pricing==1) {
    $adata['invoiceHeader']['selfPricing']=true;
  }

  if (in_array($doc_table,['gks_acc_inv','gks_whi_mov'])) {
    $dispatch_date=trim_gks($row['dispatch_date']);
    if ($dispatch_date!='') {
      $adata['invoiceHeader']['dispatchDate']=showDate(strtotime($dispatch_date),'Y-m-d',0);
    }
    $dispatch_time=trim_gks($row['dispatch_time']);
    if ($dispatch_time!='') {
      $adata['invoiceHeader']['dispatchTime']=$dispatch_time;
    }
  
    
  
    $vehicle_number=trim_gks($row['vehicle_number']);
    if ($vehicle_number!='') $adata['invoiceHeader']['vehicleNumber']=$vehicle_number;
                                                        
    
    //if ($aade_skopos_diakinisis_code>=1 and $aade_skopos_diakinisis_code<=8) 
    if ($aade_skopos_diakinisis_code>=1) {
      $adata['invoiceHeader']['movePurpose']=$aade_skopos_diakinisis_code;
    }

    if ($aade_skopos_diakinisis_code==19 and $aade_skopos_19_descr!='') {
      $adata['invoiceHeader']['otherMovePurposeTitle']=$aade_skopos_19_descr;
    }
  }
  
  //todo invoice[].invoiceHeader.fuelInvoice
  //todo invoice[].invoiceHeader.specialInvoiceCategory
  //  Ειδική κατηγορία τιμολογίου (1–12). Χρησιμοποιήστε 12 για πληρωμές εστίασης


  

  //todo invoice[].invoiceHeader.receivingNotePurpose
  //  Νέο — myDATA 2.0.2. Τιμή 1–7.Υποχρεωτικό όταν το invoiceType είναι 10.1/10.2, 
  //  απαγορεύεταιδιαφορετικά. 
  //  Σφάλμα 620.
  //todo invoice[].invoiceHeader.otherReceivingNotePurposeTitle
  //  Νέο — myDATA 2.0.2. Υποχρεωτικό όταν receivingNotePurpose = 7,
  //  απαγορεύεται διαφορετικά. Μη κενό, έως 150 χαρακτήρες Unicode. 
  //  Σφάλμα 621
  
  if (in_array($doc_table,['gks_whi_mov'])) {
    if (intval($row['receivingNotePurpose'])>=1) {
      $sql_select="SELECT aade_receiving_note_purpose_code FROM gks_aade_receiving_note_purpose 
      where id_aade_receiving_note_purpose=".intval($row['receivingNotePurpose']);
      $result_select = $db_link->query($sql_select);   
      if (!$result_select) {debug_mail(false,'error sql',$sql_select);$ret['message']='sql error'; return $ret;}
      if ($result_select->num_rows==1) {
        $row_select = $result_select->fetch_assoc();
        $aade_receiving_note_purpose_code=intval($row_select['aade_receiving_note_purpose_code']);
        if ($aade_receiving_note_purpose_code>0) {
          $adata['invoiceHeader']['receivingNotePurpose']=$aade_receiving_note_purpose_code;
        }
        if ($aade_receiving_note_purpose_code==7) {
          $otherReceivingNotePurposeTitle=trim_gks($row['otherReceivingNotePurposeTitle']);
          if ($otherReceivingNotePurposeTitle!='') {
            $adata['invoiceHeader']['otherReceivingNotePurposeTitle']=$otherReceivingNotePurposeTitle;
          }
        }
      }
    }
  }  
  
  //todo invoice[].invoiceHeader.nonObligatedRecipient
  //  Νέο — myDATA 2.0.2. Η παρουσία του επιτρέπεται μόνο σε παραστατικά διακίνησης
  //  (9.1/9.2/9.3/10.1/10.2 ή isDeliveryNote = true). Ελέγχεται η παρουσία, όχι η τιμή. 
  //  Σφάλμα 622.
  //todo invoice[].invoiceHeader.withoutDigitalTransportTracking
  //  Νέο — myDATA 2.0.2. Η παρουσία του επιτρέπεται μόνο σε παραστατικά διακίνησης, όπως παραπάνω. 
  //  Σφάλμα 623.
  if (in_array($doc_table,['gks_acc_inv','gks_whi_mov'])) {
    if (intval($row['nonObligatedRecipient'])!=0) {
      $adata['invoiceHeader']['nonObligatedRecipient']=true;
    }
    if (intval($row['withoutDigitalTransportTracking'])!=0) {
      $adata['invoiceHeader']['withoutDigitalTransportTracking']=true;
    }
  }  

  //todo invoice[].invoiceHeader.invoiceVariationType
  //  Τύπος παραλλαγής τιμολογίου (1–4). Δεν επιτρέπεται για παρόχους.



  if ($isDeliveryNote) {

    
    if (in_array($eidos_parastatikou_aade_code,['9.1','9.2','9.3','10.1','10.2'])==false) {
      $adata['invoiceHeader']['isDeliveryNote']=true;
    }
    $adata['invoiceHeader']['otherDeliveryNoteHeader']=[];
    $adata['invoiceHeader']['otherDeliveryNoteHeader']['loadingAddress']=[];
    $adata['invoiceHeader']['otherDeliveryNoteHeader']['deliveryAddress']=[];
    
    $load_odos=trim_gks($row['load_odos']);
    if ($load_odos!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['loadingAddress']['street']=$load_odos;
    $load_arithmos=trim_gks($row['load_arithmos']);
    if ($load_arithmos!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['loadingAddress']['number']=$load_arithmos;
    $load_tk=trim_gks($row['load_tk']);
    if ($load_tk!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['loadingAddress']['postalCode']=$load_tk;
    $load_poli=trim_gks($row['load_poli']);
    if ($load_poli!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['loadingAddress']['city']=$load_poli;

    $deli_odos=trim_gks($row['deli_odos']);
    if ($deli_odos!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['deliveryAddress']['street']=$deli_odos;
    $deli_arithmos=trim_gks($row['deli_arithmos']);
    if ($deli_arithmos!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['deliveryAddress']['number']=$deli_arithmos;
    $deli_tk=trim_gks($row['deli_tk']);
    if ($deli_tk!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['deliveryAddress']['postalCode']=$deli_tk;
    $deli_poli=trim_gks($row['deli_poli']);
    if ($deli_poli!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['deliveryAddress']['city']=$deli_poli;

    $startShippingBranch=trim_gks($row['load_branch']);
    if ($startShippingBranch!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['startShippingBranch']=intval($startShippingBranch);

    $completeShippingBranch=trim_gks($row['deli_branch']);
    if ($completeShippingBranch!='') $adata['invoiceHeader']['otherDeliveryNoteHeader']['completeShippingBranch']=intval($completeShippingBranch);


  }
  
  

  if ($acc_eidos_parastatikou_other_entity==1) {
    $sql_temp="SELECT ".$doc_table."_other_entity.*, 
    gks_aade_entitytype.aade_entitytype_code, gks_aade_entitytype.aade_entitytype_descr, 
    gks_country.country_initials, gks_country.country_name
    FROM (".$doc_table."_other_entity 
    LEFT JOIN gks_aade_entitytype ON ".$doc_table."_other_entity.aade_entitytype_id = gks_aade_entitytype.id_aade_entitytype) 
    LEFT JOIN gks_country ON ".$doc_table."_other_entity.entity_country_id = gks_country.id_country
    WHERE ".$doc_table."_other_entity.".$ttt."_id=".$id."
    ORDER BY entity_aa;";
    //echo '<pre>ssss '.$sql_temp;die();
    $result_temp = $db_link->query($sql_temp);        
    if (!$result_temp) {debug_mail(false,'error sql',$sql_temp);$ret['message']='sql error'; return $ret;}
    
    $other_entity_array=array();
    $other_entity_cc=0;
    while ($row_temp = $result_temp->fetch_assoc()) {
      $other_entity_cc++;

      $aade_entitytype_code=intval($row_temp['aade_entitytype_code']);
      if ($aade_entitytype_code<1 or $aade_entitytype_code>6) {
        $ret['message']=gks_lang('Δεν βρέθηκε ο κωδικός του τύπου στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_afm=trim_gks($row_temp['entity_afm']);
      if ($entity_afm=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει ΑΦΜ');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}

      $country_initials=trim_gks($row_temp['country_initials']);
      if ($country_initials=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Χώρα');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}

      $entity_branch=trim_gks($row_temp['entity_branch']);
      if ($entity_branch=='' or intval($entity_branch)!=$entity_branch) {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Αριθμό Εγκατάστασης');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_name=trim_gks($row_temp['entity_name']);
      if ($entity_name=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Επωνυμία');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_odos=trim_gks($row_temp['entity_odos']);
      if ($entity_odos=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Οδό');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_arithmos=trim_gks($row_temp['entity_arithmos']);
      if ($entity_arithmos=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Αριθμό Οδού');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_tk=trim_gks($row_temp['entity_tk']);
      if ($entity_tk=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει TK');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      $entity_poli=trim_gks($row_temp['entity_poli']);
      if ($entity_poli=='') {
        $ret['message']=gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Πόλη');
        $ret['message']=str_replace('[n]',gks_n_h($other_entity_cc),$ret['message']);
        debug_mail(false,$ret['message'],$sql_temp); return $ret;}
      
      
        
      $other_entity_array[]=array(
        'type' => $aade_entitytype_code,
        'entityData' => array(
          'vatNumber' => $entity_afm,
          'country' => $country_initials,
          'branch' => $entity_branch,
          'name' => $entity_name,
          'address' => array(
            'street' => $entity_odos,
            'number' => $entity_arithmos,
            'postalCode' => $entity_tk, 
            'city' => $entity_poli,
          ),
        ),
      );
    }
    
    if (count($other_entity_array)>0) {
      
      $adata['invoiceHeader']['otherCorrelatedEntities']=[];
      
      foreach ($other_entity_array as $oe_item) {
        
        $oeitem=[];
        $oeitem['type']=$oe_item['type'];
        $oeitem['entityData']=[];
        $oeitem['entityData']['vatNumber']=$oe_item['entityData']['vatNumber'];
        $oeitem['entityData']['country']=$oe_item['entityData']['country'];
        $oeitem['entityData']['branch']=intval($oe_item['entityData']['branch']);
        $oeitem['entityData']['name']=$oe_item['entityData']['name'];

        $oeitem['entityData']['address']=[];
        $oeitem['entityData']['address']['street']=$oe_item['entityData']['address']['street'];
        $oeitem['entityData']['address']['number']=$oe_item['entityData']['address']['number'];
        $oeitem['entityData']['address']['postalCode']=$oe_item['entityData']['address']['postalCode'];
        $oeitem['entityData']['address']['city']=$oe_item['entityData']['address']['city'];
        
        $adata['invoiceHeader']['otherCorrelatedEntities'][]=$oeitem;
      } 
    }
    //echo '<pre>ddddddddddddd ';var_dump($invoiceHeader);die();
  }

  
  //todo invoice[].invoiceHeader.thirdPartyCollection

  if ($reverseDeliveryNote) {
    $adata['invoiceHeader']['reverseDeliveryNote']=true;
    $adata['invoiceHeader']['reverseDeliveryNotePurpose']=$reverseDeliveryNotePurpose;
  }
  
  //todo invoice[].invoiceHeader.toWeigh
  //  Ένδειξη ζύγισης. Τύποι 9.1–9.3.

  $note_doc=trim_gks($row['note_doc']);
  if ($note_doc!='') {
    //$adata['invoiceHeader']['pInvoiceNote']=$note_doc; //UBL BT-22
  }
  
  
  
  

  
  $paymentMethodName=[];
  $paymentMethodDetails=[];
  
  if (in_array($doc_table,['gks_acc_inv','gks_acc_pay'])) {
  
    $sql_need_signature="select payment_acquirer_id from gks_acc_seires_paymentacquirers 
    where acc_seira_id=".$rrr_seira_id."
    order by payment_acquirer_id";
    $result_need_signature = $db_link->query($sql_need_signature);        
    if (!$result_need_signature) {debug_mail(false,'error sql',$sql_need_signature);$ret['message']='sql error'; return $ret;}
    $seira_need_signature_array=[];
    while ($row_need_signature = $result_need_signature->fetch_assoc()) {
      $seira_need_signature_array[$row_need_signature['payment_acquirer_id']]=true;
    }
    //echo '<pre>'.$sql_need_signature;die();
    
    $sql_payments="SELECT gks_acc_".$xxx."_payment.id_acc_".$xxx."_payment, 
    gks_acc_".$xxx."_payment.poso, 
    gks_acc_".$xxx."_payment.payment_acquirer_id, 
    gks_payment_acquirers.payment_acquirer_name, 
    gks_payment_acquirers.aade_tropos_pliromis_id, 
    gks_aade_tropoi_pliromis.aade_tropos_pliromis_code, 
    gks_acc_".$xxx."_payment.transaction_id
    FROM ((gks_acc_".$xxx."_payment 
    LEFT JOIN gks_payment_acquirers ON gks_acc_".$xxx."_payment.payment_acquirer_id = gks_payment_acquirers.id_payment_acquirer) 
    LEFT JOIN gks_aade_tropoi_pliromis ON gks_payment_acquirers.aade_tropos_pliromis_id = gks_aade_tropoi_pliromis.id_aade_tropos_pliromis) 
    LEFT JOIN gks_eftpos_transaction ON gks_acc_".$xxx."_payment.transaction_id = gks_eftpos_transaction.id_eftpos_transaction
    WHERE gks_acc_".$xxx."_payment.poso<>0 
    AND gks_acc_".$xxx."_payment.acc_".$xxx."_id=".$id."
    ORDER BY gks_acc_".$xxx."_payment.pp";
    $result_payments = $db_link->query($sql_payments); 
    if (!$result_payments) {debug_mail(false,'error sql',$sql_payments);$ret['message']='sql error'; return $ret;}
  
    $pa_row_array=[];
    while ($pa_row = $result_payments->fetch_assoc()) {
      $pa_row_array[]=$pa_row;
    }
    $paroxos_signature_id_array=[];


    foreach ($pa_row_array as $pa_row) {

      if (floatval($pa_row['poso'])<=0) {
        $ret['message']=gks_lang('O τρόπος πληρωμής [1] δεν έχει ποσό');
        $ret['message']=str_replace('[1]',$pa_row['payment_acquirer_name'],$ret['message']);
        debug_mail(false,$ret['message'],$sql_payments);return $ret;}
      if (intval($pa_row['aade_tropos_pliromis_code'])==0) {
        $ret['message']=gks_lang('O τρόπος πληρωμής [1] δεν έχει κωδικό για ΑΑΔΕ');
        $ret['message']=str_replace('[1]',$pa_row['payment_acquirer_name'],$ret['message']);
        debug_mail(false,$ret['message'],$sql_payments);return $ret;}
  
      
      $pmd_item_type=$pa_row['aade_tropos_pliromis_code'];
      $pmd_item_ammount=number_format($pa_row['poso'],2,'.','');
      $pmd_item_paymentMethodInfo=trim_gks($pa_row['payment_acquirer_name']);
      $pmd_item_tipAmount=false;
      $pmd_item_tid='';
      $pmd_item_transactionId='';
      $pmd_item_SigningAuthor='';
      $pmd_item_Signature='';
      $pmd_item_EndToEndReferenceID='';
      $pmd_item_nspCode='';
      
      //print '<pre>sdddddddddddddd2 ';print_r($seira_need_signature_array);die();
      
      //print '<pre>sdddddddddddddd ';print_r($pa_row);die();
      if ($pa_row['aade_tropos_pliromis_code']==7 and //POS / e-POS
          isset($seira_need_signature_array[$pa_row['payment_acquirer_id']])) {
        
        //print '<pre>sdddddddddddddd1 ';print_r($pa_row);die();
        
        $transaction_id=intval($pa_row['transaction_id']);
        if ($transaction_id==0) {
          $ret['message']=gks_lang('Δεν βρέθηκε η πληρωμή για αυτό το παραστατικό για τον τρόπο πληρωμής').' <b>'.$pa_row['payment_acquirer_name'].'</b>';
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        $sql_transaction="SELECT gks_eftpos_transaction.*, 
        gks_aade_paroxos.paroxos_name, 
        gks_aade_paroxos.signing_author
        FROM gks_eftpos_transaction 
        LEFT JOIN gks_aade_paroxos ON gks_eftpos_transaction.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos
        where id_eftpos_transaction=".$transaction_id;
        $result_transaction = $db_link->query($sql_transaction); 
        if (!$result_transaction) {debug_mail(false,'error sql',$sql_transaction);$ret['message']='sql error'; return $ret;}
        if ($result_transaction->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η συναλλαγή EFT/POS με id').': '.$transaction_id;debug_mail(false,$ret['message'],$sql); return $ret;}
        $row_transaction=$result_transaction->fetch_assoc();
        if (trim_gks($row_transaction['transaction_status'])!='done') {
          $ret['message']=gks_lang('Η συναλλαγή EFT/POS με id: [1] δεν είναι ολοκληρωμένη');
          $ret['message']=str_replace('[1]',$transaction_id,$ret['message']);
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        
        //print '<pre>sdddddddddddddd ';print_r($row_transaction);die();
        
        
        $terminalId=trim_gks($row_transaction['terminalId']);
        if ($terminalId=='') {
          $ret['message']=gks_lang('Η συναλλαγή EFT/POS με id: [1] δεν έχει τερματικό');
          $ret['message']=str_replace('[1]',$transaction_id,$ret['message']);
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        $pmd_item_tid=$terminalId;
        
        $aadeTransactionId=trim_gks($row_transaction['aadeTransactionId']);
        if ($aadeTransactionId=='') {
          $ret['message']=gks_lang('Η συναλλαγή EFT/POS με id: [1] δεν έχει κωδικό ΑΑΔΕ');
          $ret['message']=str_replace('[1]',$transaction_id,$ret['message']);
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        $pmd_item_transactionId=$aadeTransactionId;
        
        $aadeProviderSignature=trim_gks($row_transaction['aadeProviderSignature']);
        if ($aadeProviderSignature=='') {
          $ret['message']=gks_lang('Η συναλλαγή EFT/POS με id: [1] δεν έχει υπογραφή παρόχου');
          $ret['message']=str_replace('[1]',$transaction_id,$ret['message']);
          debug_mail(false,$ret['message'],$sql_payments);return $ret;}
        
        
        $pmd_item_nspCode='';
        $pmd_item_Signature=$aadeProviderSignature;
        $pmd_item_SigningAuthor=$row_transaction['signing_author'];
        $pmd_item_EndToEndReferenceID='';
        
        $tipAmount=floatval($row_transaction['tipAmount']);
        if ($tipAmount!=0) $pmd_item_tipAmount=number_format($tipAmount,2,'.','');
        
        
        $paroxos_signature_id_array[]=intval($row_transaction['paroxos_signature_id']);
        
        $payment_acquirer_with_id=intval($row_transaction['payment_acquirer_with_id']);
        $xxx_transaction_id=intval($row_transaction['xxx_transaction_id']);
        //echo '<pre>ssssssssssssss '.$payment_acquirer_with_id.'|'.$xxx_transaction_id;die();
        
        $pmd_item_is_iris=false;
        if ($payment_acquirer_with_id>0 and $xxx_transaction_id>0) {
          switch ($payment_acquirer_with_id) {   
            case 1://viva
              $pmd_item_nspCode='02';
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
              $pmd_item_nspCode='01';
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
              $pmd_item_nspCode='';
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
              $pmd_item_nspCode='04';
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
          
          
        }
        
        if ($pmd_item_is_iris) {
          $pmd_item_type=8; 
          $pmd_item_paymentMethodInfo='IRIS via '.$pmd_item_paymentMethodInfo;
        }
        
        //echo '<pre>ssssssssssssss ';print_r($pa_row);die();
        
      }

      
      $payment_item=[];
      if ($pmd_item_nspCode!='') $payment_item['nspCode']=$pmd_item_nspCode;
      $payment_item['type']=intval($pmd_item_type);
      $payment_item['amount']=floatval($pmd_item_ammount);
      
      
      if ($pmd_item_paymentMethodInfo!='') {
        $payment_item['paymentMethodInfo']=$pmd_item_paymentMethodInfo;
        $paymentMethodName[]=$pmd_item_paymentMethodInfo;
      }
      if ($pmd_item_tipAmount!==false) {
        $payment_item['tipAmount']=floatval($pmd_item_tipAmount);
      } else {
        $payment_item['tipAmount']=0;
      }
      if ($pmd_item_transactionId!='') $payment_item['transactionId']=$pmd_item_transactionId;
      if ($pmd_item_tid!='') $payment_item['tid']=$pmd_item_tid;
      if ($pmd_item_Signature!='') {
        $payment_item['providersSignature']=[];
        $payment_item['providersSignature']['SigningAuthor']=$pmd_item_SigningAuthor;
        $payment_item['providersSignature']['Signature']=$pmd_item_Signature;
        if ($pmd_item_EndToEndReferenceID!='') $payment_item['providersSignature']['EndToEndReferenceID']=$pmd_item_EndToEndReferenceID;
        
      }
      
      $paymentMethodDetails[]=$payment_item;
      
      
    }
    
     
    $ret['paroxos_signature_id_array']=$paroxos_signature_id_array;
    //echo '<pre>ddddddddddd ';print_r($ret['paroxos_signature_id_array']);die();
    
  }
  
    
  if (count($paymentMethodDetails)>0) {
    $adata['paymentMethods']=[];
    $adata['paymentMethods']['paymentMethodDetails']=$paymentMethodDetails;
  }

  if ($doc_table=='gks_whi_mov') {
    $ret['paroxos_signature_id_array']=[];
  }
  
  $lineNumber=0;
  $invoiceDetails=[];
  $VatAnalysis=[];
  if ($doc_table=='gks_acc_inv') {
    $sql_products="SELECT gks_acc_inv_products.*, 
    gks_aade_eidos_posotitas.aade_eidos_posotitas_code, gks_aade_katigoria_fpa.aade_katigoria_fpa_code,
    gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_code,
    gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_code,
    gks_aade_katigoria_telon.aade_katigoria_telon_code,
    gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_code,
    gks_aade_katigoria_fpa_ejeresi.aade_katigoria_fpa_ejeresi_code,
    gks_eshop_products.product_sku,
    gks_eshop_products.product_taric,
    gks_eshop_products.product_cpv,
    gks_eshop_products.product_code,
    gks_monades_metrisis.monada_descr,
    gks_monades_metrisis.monada_peppol_code
    FROM (((((((((gks_acc_inv_products 
    LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada) 
    LEFT JOIN gks_aade_eidos_posotitas ON gks_monades_metrisis.aade_eidos_posotitas_id = gks_aade_eidos_posotitas.id_aade_eidos_posotitas) 
    LEFT JOIN gks_eshop_fpa ON gks_acc_inv_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
    LEFT JOIN gks_aade_katigoria_fpa ON gks_eshop_fpa.aade_katigoria_fpa_id = gks_aade_katigoria_fpa.id_aade_katigoria_fpa)
    LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_acc_inv_products.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron)
    LEFT JOIN gks_aade_katigoria_xartosimou ON gks_acc_inv_products.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou)
    LEFT JOIN gks_aade_katigoria_telon ON gks_acc_inv_products.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon)
    LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_acc_inv_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron)
    LEFT JOIN gks_aade_katigoria_fpa_ejeresi ON gks_acc_inv_products.product_fpa_ejeresi_id = gks_aade_katigoria_fpa_ejeresi.id_aade_katigoria_fpa_ejeresi)
    LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product
    
    where gks_acc_inv_products.acc_inv_id=".$id."
    ORDER BY gks_acc_inv_products.product_aa";
    $result_products = $db_link->query($sql_products); 
    if (!$result_products) {debug_mail(false,'error sql',$sql_products);$ret['message']='sql error'; return $ret;}
    
    $prow_array=array();
    $prow_ids=array();
    while ($prow = $result_products->fetch_assoc()) {
      $prow_array[]=$prow;
      $prow_ids[]=$prow['id_acc_inv_product'];
    }
    
    $pirow_array=array();
    $perow_array=array();
    if (count($prow_ids)>0) {
      $sql_products_income="SELECT gks_acc_inv_products_income.*, 
      gks_aade_typos_xarakt_esodon.aade_typos_xarakt_esodon_code, gks_aade_katigoria_xarakt_esodon.aade_katigoria_xarakt_esodon_code
      FROM (gks_acc_inv_products_income 
      LEFT JOIN gks_aade_typos_xarakt_esodon ON gks_acc_inv_products_income.aade_typos_xarakt_esodon_id = gks_aade_typos_xarakt_esodon.id_aade_typos_xarakt_esodon) 
      LEFT JOIN gks_aade_katigoria_xarakt_esodon ON gks_acc_inv_products_income.aade_katigoria_xarakt_esodon_id = gks_aade_katigoria_xarakt_esodon.id_aade_katigoria_xarakt_esodon
      WHERE gks_acc_inv_products_income.acc_inv_product_id In (".implode(',',$prow_ids).")
      order by id_acc_inv_product_income";
      $result_products_income = $db_link->query($sql_products_income); 
      if (!$result_products_income) {debug_mail(false,'error sql',$sql_products_income);$ret['message']='sql error'; return $ret;}
      while ($pirow = $result_products_income->fetch_assoc()) {
        if (isset($pirow_array[$pirow['acc_inv_product_id']])==false) $pirow_array[$pirow['acc_inv_product_id']]=array();
        $pirow_array[$pirow['acc_inv_product_id']][] = array(
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
      WHERE gks_acc_inv_products_expenses.acc_inv_product_id In (".implode(',',$prow_ids).")
      order by id_acc_inv_product_expenses";
      $result_products_expenses = $db_link->query($sql_products_expenses); 
      if (!$result_products_expenses) {debug_mail(false,'error sql',$sql_products_expenses);$ret['message']='sql error'; return $ret;}
      while ($perow = $result_products_expenses->fetch_assoc()) {
        if (isset($perow_array[$perow['acc_inv_product_id']])==false) $perow_array[$perow['acc_inv_product_id']]=array();
        
        $perow_array[$perow['acc_inv_product_id']][] = array(
          'classificationType' => (trim_gks($perow['aade_typos_xarakt_eksodon_code']) != '' ? trim_gks($perow['aade_typos_xarakt_eksodon_code']) : ''), 
          'classificationCategory' => (trim_gks($perow['aade_katigoria_xarakt_eksodon_code']) != '' ? trim_gks($perow['aade_katigoria_xarakt_eksodon_code']) : ''),  
          'amount' => $perow['acc_inv_product_expenses_ammount'],
        );
      }    
    }
    //print '<pre>';print_r($pirow_array);print_r($perow_array);die();
    
    
  
    $income_sum_array=array();
    $expenses_sum_array=array();
  
    

    foreach ($prow_array as $prow) {
      $lineNumber++;
      $lineitem=[]; //InvoiceRowType
    
      $product_descr=trim_gks($prow['product_descr']);
      if ($product_descr!='') $prow['xml_product_descr']=$product_descr;
  
      $lineitem['lineNumber']=$lineNumber;
      
      if ($isDeliveryNote) {
        $product_taric=trim_gks($prow['product_taric']);
        if ($product_taric!='') $lineitem['TaricNo']=$product_taric;
      }  
      $product_code=trim_gks($prow['product_code']);
      if ($product_code!='') $lineitem['code']=$product_code;
      
      $lineitem['name']=$prow['xml_product_descr'];
      $lineitem['itemDescr_xaxaxaxa']=$prow['xml_product_descr'];
      //echo '<pre>wwwwwwwwww ggg';print_r($prow);die();
    
      $product_comments=trim_gks($prow['product_comments']);
      if ($product_comments!='') {
        $lineitem['lineComments']=$product_comments;
      }
      

      
      
      if ($eidos_parastatikou_aade_code=='8.2') {
        //xoris quantity
      } else {
        if ($eidos_parastatikou_has_posotita!=0) {
             
          $lineitem['quantity']=floatval($prow['product_quantity']);
          if (isset($prow['aade_eidos_posotitas_code'])) {
            $lineitem['measurementUnit']=intval($prow['aade_eidos_posotitas_code']);
            $lineitem['measurementUnitName']=trim_gks($prow['monada_descr']);
          } else {
            $ret['message']=gks_lang('Δεν έχει ορισθεί κωδικός μονάδας μέτρησης για ΑΑΔΕ στην γραμμή').' '.$lineNumber;debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
          }
          
          if ($b2x=='b2g') {
            if (isset($prow['monada_peppol_code']) and trim_gks($prow['monada_peppol_code'])!='') {
              $lineitem['ublMeasurementUnit']=trim_gks($prow['monada_peppol_code']);
            } else {
              $ret['message']=gks_lang('Η μονάδα μέτρησης <b>[1]</b> δεν έχει κωδικό Peppol');
              $ret['message']=str_replace('[1]',$prow['monada_descr'],$ret['message']);
              debug_mail(false,$ret['message'],''); return $ret;
            }         

            if (isset($prow['product_cpv']) and trim_gks($prow['product_cpv'])!='') {
              $lineitem['ublCpvCode']=trim_gks($prow['product_cpv']);
            } else {
              $ret['message']=gks_lang('Το είδος <b>[1]</b> δεν έχει κωδικό <b>CPV</b>');
              $ret['message']=str_replace('[1]',$prow['xml_product_descr'],$ret['message']);
              debug_mail(false,$ret['message'],''); return $ret;
            }
            //ublCpvCode -> CPV (Common Procurement Vocabulary
            
          }
          
        }
      }
      

      
      
      //invoiceDetailType
      if ($eidos_parastatikou_aade_code=='8.2') {
        $prow['product_price_final_all_net']=0;
      } 
      $lineitem['netValue']=floatval($prow['product_price_final_all_net']);
      
      if (isset($lineitem['quantity']) and $lineitem['quantity']!=0) {
        $lineitem['price']=floatval($lineitem['netValue'])/floatval($lineitem['quantity']);
      }      
      
      $lineitem['vatPercent']=100*floatval($prow['product_fpa_pososto']);
      
      $aade_katigoria_fpa_code=(isset($prow['aade_katigoria_fpa_code']) ? intval($prow['aade_katigoria_fpa_code']) : 0);
      //if ($aade_katigoria_fpa_code==0) $aade_katigoria_fpa_code=7; //miden
      
      if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='1.2') { //Timologio Polisis / Endokoinotikes Paradoseis
        $aade_katigoria_fpa_code=7;
      }
      if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='1.3') { //Timologio Polisis / Paradoseis Triton Choron
        $aade_katigoria_fpa_code=7;
      }
      if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='3.1') { //Titlos Ktisis (mi ypochreos Ekdotis)
        $aade_katigoria_fpa_code=8;
      }
      if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='8.1') { //Enoikia - Esodo
        $aade_katigoria_fpa_code=8;
      }
      if ($aade_katigoria_fpa_code==0 and $eidos_parastatikou_aade_code=='8.2') { //Eidiko Stoicheio - Apodeixis Eispraxis Forou Diamonis
        $aade_katigoria_fpa_code=8;
      }
      
      
      if ($aade_katigoria_fpa_code<=0) {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία ΦΠΑ της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; debug_mail(false,$ret['message'],''); return $ret;}
      //if ($aade_katigoria_fpa_code>0)
      $lineitem['vatCategory']=$aade_katigoria_fpa_code;
      
      //if ($prow['product_price_final_all_fpa']!=0)
      $lineitem['vatAmount']=floatval($prow['product_price_final_all_fpa']);

      //vatExemptionCategory      
      if ($prow['aade_katigoria_fpa_ejeresi_code']!=0) {
        $lineitem['vatExemptionCategory']=intval($prow['aade_katigoria_fpa_ejeresi_code']);
      }

      /* $aade_katigoria_fpa_ejeresi_code='';$exemptionReasonCode=null;$exemptionReasonText=null;
      if (isset($prow['aade_katigoria_fpa_ejeresi_code']) and trim_gks($prow['aade_katigoria_fpa_ejeresi_code'])!='') {
        $aade_katigoria_fpa_ejeresi_code=$prow['aade_katigoria_fpa_ejeresi_code'];
        $exemptionReasonCode=$prow['fpa_ejeresi_peppol_code'];
        $exemptionReasonText=$prow['aade_katigoria_fpa_ejeresi_descr'];
      } */
      
      if ($b2x=='b2g') {


        
        
        //https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL5305/
        //S - Standard rate Code specifying the standard rate.
        //E - Exempt from tax
        //Z - Zero rated goods Code specifying that the goods are at a zero rate. 
        //O - Services outside scope of tax | Code specifying that taxes are not applicable to the services.
           //8 - Εγγραφές χωρίς ΦΠΑ (πχ Μισθοδοσία, Αποσβέσεις)
        
        $categoryCode='';
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
          if ($prow['product_fpa_pososto']==0) $categoryCode='E'; //E - Exempt from tax
          
        }
        
        if ($categoryCode!='')  $lineitem['ublVatCategory']=$categoryCode;
      }
      
      
      

      //dienergia
      $lineitem['discountOption']=true;
      
      
      if ($prow['product_withheldAmount']!=0) {
        $lineitem['withheldAmount']=floatval($prow['product_withheldAmount']);
        $lineitem['withheldPercentCategory']=intval($prow['aade_katigoria_parakratoumemenon_foron_code']);
      }
        
      if ($prow['product_stampDutyAmount']!=0) {
        $lineitem['stampDutyAmount']=floatval($prow['product_stampDutyAmount']);
        $lineitem['stampDutyPercentCategory']=intval($prow['aade_katigoria_xartosimou_code']);
      }
        
      if ($prow['product_feesAmount']!=0) {
        $lineitem['feesAmount']=floatval($prow['product_feesAmount']);
        $lineitem['feesPercentCategory']=intval($prow['aade_katigoria_telon_code']);
      }
        
      if ($prow['product_otherTaxesAmount']!=0) {
        $lineitem['otherTaxesAmount']=floatval($prow['product_otherTaxesAmount']);
        $lineitem['otherTaxesPercentCategory']=intval($prow['aade_katigoria_loipon_foron_code']);
      }
      
      if ($prow['product_deductionsAmount']!=0) {
        $lineitem['deductionsAmount']=floatval($prow['product_deductionsAmount']);
      }


      //lineComments
      $found_cc_income=0;
      $incomeClassification=[];
      if (isset($pirow_array[$prow['id_acc_inv_product']])) { //incomeClassification
        foreach ($pirow_array[$prow['id_acc_inv_product']] as $value) {
          
          $incomeitem=[];
          
          if ($value['classificationType']!='e3_null') {
            $incomeitem['classificationType']=$value['classificationType'];
          }
          if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; debug_mail(false,$ret['message']); return $ret;}
          if ($value['classificationCategory']!='category_vat') {
            $incomeitem['classificationCategory']=$value['classificationCategory'];
          }
          
          if ($eidos_parastatikou_aade_code=='8.2') {
            $value['amount']=0;
          }
          
          
          $incomeitem['amount']=floatval($value['amount']);
          $incomeClassification[]=$incomeitem;
          
          
          $sum_key=$value['classificationType'].'||'.$value['classificationCategory'];
          //echo $sum_key.'||';
          if (isset($income_sum_array[$sum_key])==false) {
            $income_sum_array[$sum_key]=array(
              'classificationType' => $value['classificationType'],
              'classificationCategory' => $value['classificationCategory'],
              'amount'=>0,
            );
          }
          $income_sum_array[$sum_key]['amount']+=floatval($value['amount']);
          $found_cc_income++;
        }
      }
      if (count($incomeClassification)>0) {
        $lineitem['incomeClassification']=$incomeClassification;
      }

      $found_cc_expenses=0;
      $expensesClassification=[];
      if (isset($perow_array[$prow['id_acc_inv_product']])) { //expensesClassification
        foreach ($perow_array[$prow['id_acc_inv_product']] as $value) {
          
          $expenseitem=[];
          
          if ($value['classificationType']!='e3_null') {
            $expenseitem['classificationType']=$value['classificationType'];
          }
          if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στο είδος').' '.$prow['product_descr']; debug_mail(false,$ret['message']); return $ret;}
          if ($value['classificationCategory']!='category_vat') {
            $expenseitem['classificationCategory']=$value['classificationCategory'];
          }
          $expenseitem['amount']=floatval($value['amount']);
          $expensesClassification[]=$expenseitem;
          
          $sum_key=$value['classificationType'].'||'.$value['classificationCategory'];
          //echo $sum_key.'||';
          if (isset($expenses_sum_array[$sum_key])==false) {
            $expenses_sum_array[$sum_key]=array(
              'classificationType' => $value['classificationType'],
              'classificationCategory' => $value['classificationCategory'],
              'amount'=>0,
            );
          }
          
          $expenses_sum_array[$sum_key]['amount']+=floatval($value['amount']);
          $found_cc_expenses++;
        }
      }
      if (count($expensesClassification)>0) {
        $lineitem['expensesClassification']=$expensesClassification;
      }
      
      if ($found_cc_income==0 and $found_cc_expenses==0) {
        $ret['message']=gks_lang('Δεν βρέθηκαν Χαρακτηρισμοί Εσόδων ή Εξόδων στο είδος').' <b>'.$product_descr.'</b>'; 
        debug_mail(false,'income-expenses-Classification',print_r($prow,true));
        return $ret;
      }
        
      $invoiceDetails[]=$lineitem; 
      
      $vat_key=$prow['product_fpa_pososto'].'|'.$lineitem['vatCategory'];
      if (isset($VatAnalysis[$vat_key])==false) {
        $VatAnalysis[$vat_key]=array(
          'vatCategory'=>$lineitem['vatCategory'],
          'vatPercent'=>100*floatval($prow['product_fpa_pososto']),
          'netValuePerVat'=>0,
          'vatAmount'=>0
        );
      }
      $VatAnalysis[$vat_key]['netValuePerVat']+=$lineitem['netValue'];
      $VatAnalysis[$vat_key]['vatAmount']+=$lineitem['vatAmount'];
      
    }
  } else if ($doc_table=='gks_acc_pay') {
    $lineNumber=1;
    $lineitem=[];
    
    $lineitem['lineNumber']=$lineNumber;
    
    $lineitem['code']=$pa_row_array[0]['payment_acquirer_name'];
    $lineitem['name']=$pa_row_array[0]['payment_acquirer_name'];
    
    //$product_comments=trim_gks($prow['product_comments']);
    //if ($product_comments!='') {
    //  $lineitem['lineComments']=$product_comments;
    //}
      
    
    $lineitem['netValue']=floatval($pa_row_array[0]['poso']);
    $lineitem['vatCategory']=8;
    $lineitem['vatAmount']=0;
    $lineitem['incomeClassification']=[];
    $lineitem['incomeClassification'][]=array(
      'classificationCategory'=>'category1_95',
      'amount'=>floatval($pa_row_array[0]['poso'])
    );
  
    $invoiceDetails[]= $lineitem; 
    
    $income_sum_array=array();
    $income_sum_array[0]=array(
      'classificationType' => 'e3_null',
      'classificationCategory' => 'category1_95',
      'amount'=>floatval($pa_row_array[0]['poso']),
    );
    
    $expenses_sum_array=array();
    
  } else if ($doc_table=='gks_whi_mov') {
    $sql_products="SELECT gks_whi_mov_products.*, 
    gks_aade_eidos_posotitas.aade_eidos_posotitas_code,
    gks_eshop_products.product_sku,
    gks_eshop_products.product_taric,
    gks_eshop_products.product_cpv,
    gks_eshop_products.product_code,
    gks_monades_metrisis.monada_descr,
    gks_monades_metrisis.monada_peppol_code
    FROM ((gks_whi_mov_products 
    LEFT JOIN gks_monades_metrisis ON gks_whi_mov_products.product_monada_id = gks_monades_metrisis.id_monada) 
    LEFT JOIN gks_aade_eidos_posotitas ON gks_monades_metrisis.aade_eidos_posotitas_id = gks_aade_eidos_posotitas.id_aade_eidos_posotitas)
    LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product
    where gks_whi_mov_products.whi_mov_id=".$id."
    ORDER BY gks_whi_mov_products.product_aa";
    
    //echo '<pre>ddddddddd '.$sql_products;die();
    $result_products = $db_link->query($sql_products); 
    if (!$result_products) {debug_mail(false,'error sql',$sql_products);$ret['message']='sql error'; return $ret;}
    
    $prow_array=array();
    $prow_ids=array();
    while ($prow = $result_products->fetch_assoc()) {
      $prow_array[]=$prow;
      $prow_ids[]=$prow['id_whi_mov_product'];
    }
    
    //$pirow_array=array();
    //$perow_array=array();

    //print '<pre>';print_r($pirow_array);print_r($perow_array);die();
    
    
  
  
    $lineNumber=0;
    foreach ($prow_array as $prow) {
      $lineNumber++;
      $lineitem=[];
      

  
      $product_descr=trim_gks($prow['product_descr']);
      if ($product_descr!='') $prow['xml_product_descr']=$product_descr;
  
      $lineitem['lineNumber']=$lineNumber;
      
//    if ($eidos_parastatikou_aade_code=='10.1' and $prow['product_quantity'] < 0) {
//      $lineitem['recType']=7;
//    }
        
      $product_taric=trim_gks($prow['product_taric']);
      if ($product_taric!='') $lineitem['TaricNo']=$product_taric;
        
      $product_code=trim_gks($prow['product_code']);
      if ($product_code!='') $lineitem['code']=$product_code;
      
      $lineitem['name']=$prow['xml_product_descr'];
      //$lineitem['itemDescr']=$prow['xml_product_descr'];
      
      $product_comments=trim_gks($prow['product_comments']);
      if ($product_comments!='') {
        $lineitem['lineComments']=$product_comments;
      }
        
      if ($eidos_parastatikou_has_posotita!=0) {
        $lineitem['quantity']=floatval($prow['product_quantity']);
        
        if (isset($prow['aade_eidos_posotitas_code'])) {
          $lineitem['measurementUnit']=intval($prow['aade_eidos_posotitas_code']);
          $lineitem['measurementUnitName']=trim_gks($prow['monada_descr']);
        } else {
          $ret['message']=gks_lang('Δεν έχει ορισθεί κωδικός μονάδας μέτρησης για ΑΑΔΕ στην γραμμή').' '.$lineNumber;debug_mail(false,$ret['message'],print_r($prow, true)); return $ret;
        }
        
        if ($b2x=='b2g') {
          if (isset($prow['monada_peppol_code']) and trim_gks($prow['monada_peppol_code'])!='') {
            $lineitem['ublMeasurementUnit']=trim_gks($prow['monada_peppol_code']);
          } else {
            $ret['message']=gks_lang('Η μονάδα μέτρησης <b>[1]</b> δεν έχει κωδικό Peppol');
            $ret['message']=str_replace('[1]',$prow['monada_descr'],$ret['message']);
            debug_mail(false,$ret['message'],''); return $ret;
          }

          if (isset($prow['product_cpv']) and trim_gks($prow['product_cpv'])!='') {
            $lineitem['ublCpvCode']=trim_gks($prow['product_cpv']);
          } else {
            $ret['message']=gks_lang('Το είδος <b>[1]</b> δεν έχει κωδικό CPV');
            $ret['message']=str_replace('[1]',$prow['xml_product_descr'],$ret['message']);
            debug_mail(false,$ret['message'],''); return $ret;
          }
        }         
      }
      
     
      
      //print '<pre>';print_r($prow);die();
      //$invoiceDetails->addChild('itemDescr',$prow['xml_product_descr']);
      
      //$invoiceDetails->addChild('discountOption','true');
      $lineitem['netValue']=0;
      $lineitem['vatCategory']=8;
      $lineitem['vatAmount']=0;
      $lineitem['incomeClassification']=[];
      $lineitem['incomeClassification'][]=array(
        'classificationCategory'=>'category3',
        'amount'=>0
      );
           
      
      $invoiceDetails[]=$lineitem; 
      
    } 

    $income_sum_array=array();
    $income_sum_array[0]=array(
      'classificationType' => 'e3_null',
      'classificationCategory' => 'category3', //
      'amount'=>0,
    );    
    $expenses_sum_array=array();
       
  }
  
  //print '<pre>';print_r($ret);var_dump($xml);die();
  
  if (count($invoiceDetails)==0) {debug_mail(false,'error sql',$sql);$ret['message']=gks_lang('Δεν βρέθηκαν γραμμές στο παραστατικό'); return $ret;}
  $adata['invoiceDetails']=$invoiceDetails;
  
  $adata['invoiceSummary']=[];
  
  
  if ($eidos_parastatikou_aade_code=='8.2') {
    $row['gks_price_net']=0;
    $row['gks_price_total']=$row['totalOtherTaxesAmount'];
  }

  if ($doc_table=='gks_acc_inv') {  
    $adata['invoiceSummary']['totalNetValue']=floatval($row['gks_price_net']);
    $adata['invoiceSummary']['totalVatAmount']=floatval($row['gks_price_fpa']);
    $adata['invoiceSummary']['totalWithheldAmount']=floatval($row['totalWithheldAmount']);
    $adata['invoiceSummary']['totalFeesAmount']=floatval($row['totalFeesAmount']);
    $adata['invoiceSummary']['totalStampDutyAmount']=floatval($row['totalStampDutyamount']);
    $adata['invoiceSummary']['totalOtherTaxesAmount']=floatval($row['totalOtherTaxesAmount']);
    $adata['invoiceSummary']['totalDeductionsAmount']=floatval($row['totalDeductionsAmount']);
    $adata['invoiceSummary']['totalGrossValue']=floatval($row['gks_price_total']);
  
  } else if ($doc_table=='gks_acc_pay') {
    $adata['invoiceSummary']['totalNetValue']=floatval($pa_row_array[0]['poso']);         
    $adata['invoiceSummary']['totalVatAmount']=0;
    $adata['invoiceSummary']['totalWithheldAmount']=0;
    $adata['invoiceSummary']['totalFeesAmount']=0;
    $adata['invoiceSummary']['totalStampDutyAmount']=0;
    $adata['invoiceSummary']['totalOtherTaxesAmount']=0;
    $adata['invoiceSummary']['totalDeductionsAmount']=0;
    $adata['invoiceSummary']['totalGrossValue']=floatval($pa_row_array[0]['poso']); 
  } else if ($doc_table=='gks_whi_mov') {
    $adata['invoiceSummary']['totalNetValue']=0;         
    $adata['invoiceSummary']['totalVatAmount']=0;
    $adata['invoiceSummary']['totalWithheldAmount']=0;
    $adata['invoiceSummary']['totalFeesAmount']=0;
    $adata['invoiceSummary']['totalStampDutyAmount']=0;
    $adata['invoiceSummary']['totalOtherTaxesAmount']=0;
    $adata['invoiceSummary']['totalDeductionsAmount']=0;
    $adata['invoiceSummary']['totalGrossValue']=0; 
  }
  

  if (count($income_sum_array)>0) {
    $adata['invoiceSummary']['incomeClassification']=[];
    foreach ($income_sum_array as $value) {
      $ssitem=[];
      if ($value['classificationType']!='e3_null') {
        $ssitem['classificationType']=$value['classificationType'];
      }
      if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στην σύνοψη του παραστατικού'); debug_mail(false,$ret['message']); return $ret;}
      $ssitem['classificationCategory']=$value['classificationCategory'];
      $ssitem['amount']=floatval($value['amount']);
      $adata['invoiceSummary']['incomeClassification'][]=$ssitem;
    } 
  }

  if (count($expenses_sum_array)>0) {
    $adata['invoiceSummary']['expensesClassification']=[];
    foreach ($expenses_sum_array as $value) {
      $ssitem=[];
      if ($value['classificationType']!='e3_null') {
        $ssitem['classificationType']=$value['classificationType'];
      }
      if ($value['classificationCategory']=='') {$ret['message']=gks_lang('Δεν βρέθηκε η κατηγορία εσόδων της ΑΑΔΕ στην σύνοψη του παραστατικού'); debug_mail(false,$ret['message']); return $ret;}
      if ($value['classificationCategory']!='category_vat') {
        $ssitem['classificationCategory']=$value['classificationCategory'];
      }
      $ssitem['amount']=floatval($value['amount']);
      $adata['invoiceSummary']['expensesClassification'][]=$ssitem;
    } 
  }


  $adata['invoiceVatAnalysis']=[];
  foreach($VatAnalysis as $vat_item) {
    $adata['invoiceVatAnalysis'][]=$vat_item;
  }

  if (in_array($doc_table,['gks_acc_inv','gks_whi_mov'])) {
    $sql_corri="SELECT *
    FROM gks_".$ttt."_packings_declarations
    where ".$ttt."_id=".$id."
    ORDER BY packaging_aa";
    $result_corri = $db_link->query($sql_corri);        
    if (!$result_corri) {debug_mail(false,'error sql',$sql_corri);die('sql error');}
    $pde_items=[];
    while ($row_corri = $result_corri->fetch_assoc()) {
      $type_id=intval($row_corri['packaging_type_id']);
      $type_6_descr=trim_gks($row_corri['packaging_type_6_descr']);
      if ($type_id!=6) $type_6_descr='';
      $quantity=intval($row_corri['packaging_quantity']);
      if ($type_id>0 and $quantity>0) {
        $pde_items[]=array(
          'type_id'=> $type_id,
          'type_6_descr'=> $type_6_descr,
          'quantity'=> $quantity,
        );
      }
    }
    if (count($pde_items)>0) {
      $adata['packingsDeclarations']=[];
      $adata['packingsDeclarations'][]=array('Packages'=>[]);
      
      foreach ($pde_items as $pde_item) {
        $pditem=[];
        if ($pde_item['type_6_descr']!='') {
          $pditem['otherPackagingTypeTitle']=$pde_item['type_6_descr']; 
        }
        $pditem['packagingType']=$pde_item['type_id']; 
        $pditem['quantity']=floatval($pde_item['quantity']); 
        $adata['packingsDeclarations'][0]['Packages'][]=$pditem;
        
        //$adata['packingsDeclarations'][]=
      } 
    }
    //echo '<pre>';print_r($pde_items);die();

  }
  
  //echo '<pre>sssssssssssss3 |'.$c_sub.'|'.$rrr_seira_code.'|';print_r($adata);die();

  
  //print '<pre>sssssssss';print_r($struct_data);die();
  //print '<pre>sssssssss';print_r($adata);die();
  
  /* 
  foreach ($struct_data['prow_array'] as $pitem) {
    
    $item=[];
    $item['name']=$pitem['xml_product_descr'];
    $item['lineNumber']=$pitem['xml_lineNumber'];
    
    if ($doc_table=='gks_acc_inv' or $doc_table=='gks_whi_mov') {
      $item['code']=$pitem['product_code'];
      $item['quantity']=floatval($pitem['xml_quantity']);
      $item['measurementUnit']=intval($pitem['xml_measurementUnit']);
      $item['measurementUnitName']=trim_gks($pitem['monada_descr']);
      $item['vatPercent']=100*floatval($pitem['product_fpa_pososto']);
    } else if ($doc_table=='gks_acc_pay') {
      $item['code']=$pitem['xml_product_descr'];
    }
      
    $item['netValueBeforeDiscount']=floatval($pitem['xml_netValue']);
    $item['netValue']=floatval($pitem['xml_netValue']);
    $item['vatAmount']=floatval($pitem['xml_vatAmount']);
    $item['vatCategory']=intval($pitem['xml_vatCategory']);
    
    if (isset($pitem['xml_incomeClassification']) and count($pitem['xml_incomeClassification'])>0) {
      $item['incomeClassification']=[];
      foreach($pitem['xml_incomeClassification'] as $come) {
        $item_come=[];
        if (isset($come['type']) and $come['type']!='e3_null') $item_come['classificationType']=$come['type'];
        if (isset($come['category'])) $item_come['classificationCategory']=$come['category'];
        if (isset($come['amount'])) $item_come['amount']=floatval($come['amount']);
        
        $item['incomeClassification'][]=$item_come;
      }
    }
    if (isset($pitem['xml_expensesClassification']) and count($pitem['xml_expensesClassification'])>0) {
      $item['incomeClassification']=[];
      foreach($pitem['xml_expensesClassification'] as $come) {
        $item_come=[];
        if (isset($come['type']) and $come['type']!='e3_null') $item_come['classificationType']=$come['type'];
        if (isset($come['category'])) $item_come['classificationCategory']=$come['category'];
        if (isset($come['amount'])) $item_come['amount']=floatval($come['amount']);
        
        $item['expensesClassification'][]=$item_come;
      }
    }
    
    
    $item['price']=floatval($pitem['xml_netValue'])/floatval($pitem['xml_quantity']);
    $item['priceIncludeVAT']=0;
    $adata['invoiceDetails'][]=$item;

  }

 */
  // extra
  $adata['extra']=[];
  $adata['extra']['customerSendEmail']=true;
  if ($row['company_sub_id']!=0) {
    $adata['extra']['salerTitle']=trim_gks($row['company_sub_title']);
    $adata['extra']['salerName']=trim_gks($row['company_eponimia']);
    $adata['extra']['salerActivity']=trim_gks($row['company_epaggelma']);
    $adata['extra']['salerStreetName']=trim_gks(trim_gks($row['company_sub_odos']).' '.trim_gks($row['company_sub_arithmos']));
    $adata['extra']['salerAdditionalStreetName']=trim_gks($row['company_sub_perioxi']);
    $adata['extra']['salerTk']=trim_gks($row['company_sub_tk']);
    $adata['extra']['salerCity']=trim_gks($row['company_sub_poli']);
    $adata['extra']['salerPhone']=trim_gks($row['company_sub_phone']);
    $adata['extra']['salerEmail']=trim_gks($row['company_sub_email']);
    $adata['extra']['salerWebsite']=trim_gks($row['company_sub_url']);
    $adata['extra']['salerGemh']=trim_gks($row['company_gemi_number']);
    $adata['extra']['salerVat']=trim_gks($row['company_afm']);
    $adata['extra']['salerDoyName']=trim_gks($row['company_doy']);
    
  } else {
    $adata['extra']['salerTitle']=trim_gks($row['company_title']);
    $adata['extra']['salerName']=trim_gks($row['company_eponimia']);
    $adata['extra']['salerActivity']=trim_gks($row['company_epaggelma']);
    $adata['extra']['salerStreetName']=trim_gks(trim_gks($row['company_odos']).' '.trim_gks($row['company_sub_arithmos']));
    $adata['extra']['salerAdditionalStreetName']=trim_gks($row['company_perioxi']);
    $adata['extra']['salerTk']=trim_gks($row['company_tk']);
    $adata['extra']['salerCity']=trim_gks($row['company_poli']);
    $adata['extra']['salerPhone']=trim_gks($row['company_phone']);
    $adata['extra']['salerEmail']=trim_gks($row['company_email']);
    $adata['extra']['salerWebsite']=trim_gks($row['company_url']);
    $adata['extra']['salerGemh']=trim_gks($row['company_gemi_number']);
    $adata['extra']['salerVat']=trim_gks($row['company_afm']);
    $adata['extra']['salerDoyName']=trim_gks($row['company_doy']);
    
  }
  
  
  
  $adata['extra']['customerName']=trim_gks($row['eponimia']);
  $adata['extra']['customerActivity']=trim_gks($row['epaggelma']);
  $adata['extra']['customerVat']=trim_gks($row['afm']);
  $adata['extra']['customerDoyName']=trim_gks($row['doy']);
  $adata['extra']['customerStreetName']=trim_gks(trim_gks($row['ma_odos'].' '.$row['ma_arithmos']));
  $adata['extra']['customerTk']=trim_gks($row['ma_tk']);
  $adata['extra']['customerCity']=trim_gks($row['ma_poli']);
  $adata['extra']['customerPhone']=trim_gks($row['user_mobile']);
  $adata['extra']['customerEmail']=trim_gks($row['user_email']);
  $adata['extra']['shipmentName']=trim_gks($row['delivery_method_name']);
  //loadingAddress
  //destinationAddress
  $adata['extra']['paymentMethodName']=implode(', ',$paymentMethodName);
  $adata['extra']['movePurpose']=trim_gks($row['aade_skopos_diakinisis_descr']);
  $adata['extra']['invoiceTypeName']=trim_gks($row['acc_journal_descr']);

  if (isset($vehicle_number) and $vehicle_number!='') $adata['extra']['vehicleNumber']=$vehicle_number;
  if (isset($note_doc) and $note_doc!='') $adata['extra']['invoiceRemarks']=$note_doc;

  //$adata['downloadingInvoiceUrl']='https://test.easyfilesselection.com/s/565rmmpz';

  if ($b2x=='b2g') {
    $adata['ublFields']=[];
    $adata['ublFields']['invoiceTypeCode']=$row['peppol_code'];
    $adata['ublFields']['issuerInfo']=[];
    $adata['ublFields']['issuerInfo']['name']=trim_gks($row['company_eponimia']);
    
    if ($row['company_sub_id']!=0) {
      $adata['ublFields']['issuerInfo']['country']=trim_gks($row['company_sub_country_initials']);
      $adata['ublFields']['issuerInfo']['address']=[];
      $adata['ublFields']['issuerInfo']['address']['street']=trim_gks($row['company_sub_odos']);
      $adata['ublFields']['issuerInfo']['address']['number']=trim_gks($row['company_sub_arithmos']);
      $adata['ublFields']['issuerInfo']['address']['postalCode']=trim_gks($row['company_sub_tk']);
      $adata['ublFields']['issuerInfo']['address']['city']=trim_gks($row['company_sub_poli']);
    } else {
      $adata['ublFields']['issuerInfo']['country']=trim_gks($row['company_country_initials']);
      $adata['ublFields']['issuerInfo']['address']=[];
      $adata['ublFields']['issuerInfo']['address']['street']=trim_gks($row['company_odos']);
      $adata['ublFields']['issuerInfo']['address']['number']=trim_gks($row['company_arithmos']);
      $adata['ublFields']['issuerInfo']['address']['postalCode']=trim_gks($row['company_tk']);
      $adata['ublFields']['issuerInfo']['address']['city']=trim_gks($row['company_poli']);
    }
   
    $adata['ublFields']['counterInfo']=[];
    $adata['ublFields']['counterInfo']['name']=trim_gks($row['eponimia']);
    $adata['ublFields']['counterInfo']['country']=$party_country_initials;
    $adata['ublFields']['counterInfo']['address']=[];
    $adata['ublFields']['counterInfo']['address']['street']=trim_gks($row['ma_odos']);
    $adata['ublFields']['counterInfo']['address']['number']=trim_gks($row['ma_arithmos']);
    $adata['ublFields']['counterInfo']['address']['postalCode']=trim_gks($row['ma_tk']);
    $adata['ublFields']['counterInfo']['address']['city']=trim_gks($row['ma_poli']);
  

    if ($isDeliveryNote and $is_endodiakinisi==false) {
      
      $adata['ublFields']['delivery']=[];
      
      $deli_country_initials='';
      if (isset($row['deli_country_id']) and intval($row['deli_country_id'])>0) {
        $sql_temp="select country_initials from gks_country where id_country=".intval($row['deli_country_id']);
        $result_temp = $db_link->query($sql_temp);        
        if (!$result_temp) {debug_mail(false,'error sql',$sql_temp);$ret['message']='sql error'; return $ret;}
        if ($result_temp->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η χώρα με κωδικό').' '.intval($row['deli_country_id']);debug_mail(false,$ret['message'],$sql_temp); return $ret;}
        $row_temp = $result_temp->fetch_assoc();
        $deli_country_initials=$row_temp['country_initials'];
      }
      $adata['ublFields']['delivery']['country']=$deli_country_initials;
      
      $adata['ublFields']['delivery']['name']='Delivery';

      $adata['ublFields']['delivery']['address']=[];
      $deli_odos=trim_gks($row['deli_odos']);
      if ($deli_odos!='') $adata['ublFields']['delivery']['address']['street']=$deli_odos;
      $deli_arithmos=trim_gks($row['deli_arithmos']);
      if ($deli_arithmos!='') $adata['ublFields']['delivery']['address']['number']=$deli_arithmos;
      $deli_tk=trim_gks($row['deli_tk']);
      if ($deli_tk!='') $adata['ublFields']['delivery']['address']['postalCode']=$deli_tk;
      $deli_poli=trim_gks($row['deli_poli']);
      if ($deli_poli!='') $adata['ublFields']['delivery']['address']['city']=$deli_poli;
      
    }
    
    $adata['B2G']=[];

    //BT-10
    //todo B2G.buyerReference  Οργανωτική μονάδα αγοραστή.
    //todo B2G.buyerReference.orgUnitName Όνομα οργανωτικής μονάδας (PEPPOL BT-10).
    //todo B2G.buyerReference.orgUnitCode  Κωδικός οργανωτικής μονάδας.
    if (isset($struct_data['row']['b2g_inv_aaht_name']) and trim_gks($struct_data['row']['b2g_inv_aaht_name']!='')) { 
      $adata['B2G']['buyerReference']=[];
      $adata['B2G']['buyerReference']['orgUnitName']=trim_gks($struct_data['row']['b2g_inv_aaht_name']); 
      //$adata['B2G']['buyerReference']['orgUnitCode']=''; 
    }

    //BT-11
    //todo B2G.projectReference  Απαιτείται για τιμολόγια μη τύπου 5.1. BT-11
    //todo B2G.projectReference.type Τύπος αναφοράς έργου.
    //todo B2G.projectReference.id ΑΔΑ ή "0" για ΔΕΚΟ/Δ.Ε.Υ.Α.
    if (isset($struct_data['row']['project_reference']) and trim_gks($struct_data['row']['project_reference']!='')) { 
      $projectReference=trim_gks($struct_data['row']['project_reference']); 
      $parts=explode('|',$projectReference);
      if (count($parts)==2) {
        $adata['B2G']['projectReference']=[];
        $adata['B2G']['projectReference']['type']=intval($parts[0]);
        $adata['B2G']['projectReference']['id']=trim_gks($parts[1]);
      }
    } 
    
    //BT-12
    //todo B2G.contractDocumentId Αναφορά δημόσιας σύμβασης (PEPPOL BT-12).
    if (isset($struct_data['row']['contract_reference']) and trim_gks($struct_data['row']['contract_reference']!='')) { 
      $adata['B2G']['contractDocumentId']=trim_gks($struct_data['row']['contract_reference']);
    }    

    //BT-44
    if (isset($struct_data['row']['b2g_inv_buyer_name']) and trim_gks($struct_data['row']['b2g_inv_buyer_name']!='')) { 
      //$adata['B2G']['buyerName']=trim_gks($struct_data['row']['b2g_inv_buyer_name']); 
    } else {
      if (isset($xml['counterpart']['name'])) {
        //$adata['B2G']['buyerName']=$adata['counterpart']['name'];
      }
    }
    
    //BT-46
    //todo B2G.accountingCustomerPartyId Αναγνωριστικό αναθέτουσας 
    // αρχής/ αγοραστή (PEPPOL BT-46) 
    if (isset($struct_data['row']['b2g_inv_aaht_code']) and trim_gks($struct_data['row']['b2g_inv_aaht_code']!='')) { 
      $adata['B2G']['accountingCustomerPartyId']=trim_gks($struct_data['row']['b2g_inv_aaht_code']);
    } else {
      if (isset($struct_data['row']['b2g_aaht_code']) and trim_gks($struct_data['row']['b2g_aaht_code']!='')) { 
        $adata['B2G']['accountingCustomerPartyId']=trim_gks($struct_data['row']['b2g_aaht_code']);
      }   
    }    

    
    

      
    //todo B2G.purchaseOrderReference Αναφορά εντολής αγοράς (PEPPOL BT-13).
    //todo B2G.remittanceInformation Κωδικός πληρωμής RF για τιμολόγια
    //  κοινής ωφέλειας (PEPPOL BT-83). Ενεργοποιεί το στοιχείο εντολής μεταφοράς
    //todo B2G.paymentMeansCode Κωδικός μέσου πληρωμής.
    //  Προεπιλογή "31" όταν ορίζεται το remittanceInformation
    //todo B2G.utilityMeterReferences Αναφορές μετρητή κατανάλωσης 
    // για τιμολόγια κοινής ωφέλειας.
    //todo B2G.utilityMeterReferences[].meterCode Κωδικός μετρητή (PEPPOL BT-122).
    //todo B2G.utilityMeterReferences[].billUrl URL εγγράφου λογαριασμού 
    //  κοινής ωφέλειας (PEPPOL BT-124).
    
    
  }




	//print '<pre>';print_r($adata);die();
	//print '<pre>';print_r($xml);die();
	//print '<pre>';print_r($struct_data);die();



  
  $ret['file_data']=[]; 
  $ret['file_data']['invoice']=[];
  $ret['file_data']['invoice'][]=$adata;
  
  $ret['paroxos_signature_id_array']=[]; //$paroxos_signature_id_array;
  $ret['message']='OK';
  $ret['success']=true;

  return $ret;
  
	
}
