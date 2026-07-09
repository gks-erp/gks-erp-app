<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function getAADE_InvoiceDeliveryStatus($mystate) {
  switch ($mystate) {
    case 'agnosto': return gks_lang('Άγνωστο','part4','aade_invoicedeliverystatus');
    case 'REGISTERED': return gks_lang('Το ΔΑ έχει εκδοθεί επιτυχώς (Registered)','part4','aade_invoicedeliverystatus'); break; 
    case 'CANCELLED': return gks_lang('Ο εκδότης ακύρωσε το ΔΑ πριν την έναρξη της διακίνησης. (Cancelled)','part4','aade_invoicedeliverystatus'); break; 
    case 'IN_TRANSIT': return gks_lang('Η διακίνηση έχει ξεκινήσει (InTransit)','part4','aade_invoicedeliverystatus'); break; 
    case 'REJECTED': return gks_lang('Ο λήπτης απέρριψε την παραλαβή. (Rejected)','part4','aade_invoicedeliverystatus'); break; 
    case 'DELIVERED_BY_CARRIER': return gks_lang('Ο μεταφορέας δήλωσε παράδοση (αναμονή επιβεβαίωσης από λήπτη B2B). (DeliveredByCarrier)','part4','aade_invoicedeliverystatus'); break; 
    case 'FAILED_DELIVERY': return gks_lang('Ο μεταφορέας δήλωσε αποτυχία παράδοσης (FailedDelivery)','part4','aade_invoicedeliverystatus'); break; 
    case 'COMPLETED': return gks_lang('Η διακίνηση ολοκληρώθηκε με επιτυχία (Completed)','part4','aade_invoicedeliverystatus'); break; 
    default: return $mystate; break; 
  }   
}
function getAADE_InvoiceDeliveryStatusID($id) {
  switch ($id) {
    case 0: return getAADE_InvoiceDeliveryStatus('agnosto');
    case 1: return getAADE_InvoiceDeliveryStatus('REGISTERED');
    case 2: return getAADE_InvoiceDeliveryStatus('CANCELLED');
    case 3: return getAADE_InvoiceDeliveryStatus('IN_TRANSIT');
    case 4: return getAADE_InvoiceDeliveryStatus('REJECTED');
    case 5: return getAADE_InvoiceDeliveryStatus('DELIVERED_BY_CARRIER');
    case 6: return getAADE_InvoiceDeliveryStatus('FAILEDDELIVERY');
    case 7: return getAADE_InvoiceDeliveryStatus('FAILEDDELIVERY');
    case 8: return getAADE_InvoiceDeliveryStatus('COMPLETED');
    default: return $id; break; 
  }
}

function getAADE_DeliveryEventType($mystate) {
  switch ($mystate) {
    case 'RegisterTransfer': return gks_lang('Έναρξη διακίνησης (RegisterTransfer)','part4','aade_deliveryeventtype'); break; 
    case 'ConfirmOutcome': return gks_lang('Δηλώση του αποτέλεσματος της παράδοσης (ConfirmOutcome)','part4','aade_deliveryeventtype'); break; 
    case 'Rejection': return gks_lang('Απόρριψη (Rejection)','part4','aade_deliveryeventtype'); break; 
    default: return $mystate; break; 
  } 
}
$getAADE_PackagingTypeDescr_max=6;
function getAADE_PackagingTypeDescr($mystate) {
  switch ($mystate) {
    case 1: return gks_lang('Παλέτα','part4','aade_packagingtypedescr'); break; 
    case 2: return gks_lang('Κούτα','part4','aade_packagingtypedescr'); break; 
    case 3: return gks_lang('Κιβώτιο','part4','aade_packagingtypedescr'); break; 
    case 4: return gks_lang('Βαρέλι','part4','aade_packagingtypedescr'); break; 
    case 5: return gks_lang('Σάκος','part4','aade_packagingtypedescr'); break; 
    case 6: return gks_lang('Λοιπά','part4','aade_packagingtypedescr'); break; 

    default: return $mystate; break; 
  } 
}

function getAADE_TransportTypeDescr($id) {
  switch ($id) {
    case 1: return gks_lang('Φορτηγό Δημόσιας Χρήσης','part4','aade_transporttypedescr'); break; 
    case 2: return gks_lang('Φορτηγό Ιδιωτικής Χρήσης','part4','aade_transporttypedescr'); break; 
    case 3: return gks_lang('Πλοίο','part4','aade_transporttypedescr'); break; 
    case 4: return gks_lang('Τρένο','part4','aade_transporttypedescr'); break; 
    case 5: return gks_lang('Αεροπλάνο','part4','aade_transporttypedescr'); break; 
    case 6: return gks_lang('Λοιπά Μεταφορικά Μέσα (π.χ Δίκυκλα, ...)','part4','aade_transporttypedescr'); break; 
    case 7: return gks_lang('Άνευ','part4','aade_transporttypedescr'); break; 

    default: return $id; break; 
  } 
}




function getAADE_lch_outcome($mystate) {
  switch ($mystate) {
    case 'FULL': return gks_lang('Πλήρες (FULL)','part4','aade_lch_outcome'); break; 
    case 'PARTIAL': return gks_lang('Ένα μέρος (PARTIAL)','part4','aade_lch_outcome'); break; 
    case 'NONE': return gks_lang('Τίποτα (NONE)','part4','aade_lch_outcome'); break; 
    default: return $mystate; break; 
  } 
}

$getAADE_ReverseDeliveryNotePurposeDescr_max=5;
function getAADE_ReverseDeliveryNotePurposeDescr($mystate) {
  switch ($mystate) {
    case 1: return gks_lang('ΜΗ ΥΠΟΧΡΕΟΣ ΕΚΔΟΣΗΣ','part4','aade_reversedeliverynotepurposedescr'); break; 
    case 2: return gks_lang('ΑΡΝΗΣΗ ΕΚΔΟΣΗΣ/ΕΚ ΠΑΡΑΔΡΟΜΗΣ ΜΗ ΕΚΔΟΣΗ','part4','aade_reversedeliverynotepurposedescr'); break; 
    case 3: return gks_lang('ΕΝΔΟΚΟΙΝΟΤΙΚΗ ΑΠΟΚΤΗΣΗ','part4','aade_reversedeliverynotepurposedescr'); break; 
    case 4: return gks_lang('ΑΠΟΚΤΗΣΗ ΤΡΙΤΗ ΧΩΡΑ','part4','aade_reversedeliverynotepurposedescr'); break; 
    case 5: return gks_lang('ΑΝΤΙΣΤΡΟΦΗ ΥΠΟΧΡΕΩΣΗΣ','part4','aade_reversedeliverynotepurposedescr'); break; 

    default: return $mystate; break; 
  } 
}

function gks_aade_delivery_note_get_record($params) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  $ret = array(
    'success' => false, 
    'message' => 'generic error', 
    'rec_id'=>0, 
    'mark'=>'', 
    'qrUrl'=>'',
    'vat_issuer'=>'',
    'vat_customer'=>'',
    'last_state'=>'',
    'last_date_get_data'=>'',
  );
  $input_mark='';if (isset($params['mark'])) $input_mark=$params['mark'];
  $input_qrUrl='';if (isset($params['qrUrl'])) $input_qrUrl=$params['qrUrl'];
  if ($input_mark=='' and $input_qrUrl=='') {
    $ret['message']=gks_lang('Ορίστε το mark ή το QRCode URL'); return $ret;}
  
  
  if ($input_mark!='') {
    $sql="select * from gks_aade_delivery_note where mark='".$db_link->escape_string($input_mark)."'";
  } else if ($input_qrUrl) {
    $sql="select * from gks_aade_delivery_note where qrUrl='".$db_link->escape_string($input_qrUrl)."'";
  }
  //die('ggggggg '.$sql);
  $result = $db_link->query($sql);  
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $ret['success']=true;
    $ret['message']=gks_lang('Βρέθηκε το ΜΑΡΚ');
    $ret['rec_id']=intval($row['id_aade_delivery_note']);
    $ret['mark']=trim_gks($row['mark']);
    $ret['qrUrl']=trim_gks($row['qrUrl']);
    $ret['vat_issuer']=trim_gks($row['vat_issuer']);
    $ret['vat_customer']=trim_gks($row['vat_customer']);
    $ret['last_state']=trim_gks($row['last_state']);
    $ret['last_date_get_data']=trim_gks($row['last_date_get_data']);

    if ($ret['qrUrl']!='' and ($ret['mark']=='' or $ret['vat_issuer']=='')) {
      $myparse=gks_parse_aade_gr_qrcode($ret['qrUrl']);
      $found_mark=$myparse['found_mark'];
      $found_vat_issuer=$myparse['found_vat_issuer'];
      $found_vat_customer=$myparse['found_vat_customer'];      
      if ($found_mark!='') {
        $sql="update gks_aade_delivery_note set 
        mark='".$db_link->escape_string($found_mark)."',
        vat_issuer='".$db_link->escape_string($found_vat_issuer)."',
        vat_customer='".$db_link->escape_string($found_vat_customer)."'
        where id_aade_delivery_note=".$ret['rec_id'];
        $result = $db_link->query($sql);  
        if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
        
        $ret['mark']=trim_gks($found_mark);
        $ret['vat_issuer']=trim_gks($found_vat_issuer);
        $ret['vat_customer']=trim_gks($found_vat_customer);        
        
      }
      
      $ret['message']=gks_lang('Βρέθηκε το ΜΑΡΚ');
    } else if ($ret['mark']!='' and $ret['qrUrl']=='') {
      
      $sql="select id_acc_inv as rid,aade_qrurl,'gks_acc_inv' as doc_table
      from gks_acc_inv
      where aade_invoicemark='".$db_link->escape_string($input_mark)."' and aade_qrurl<>''
      union
      select id_acc_pay as rid,aade_qrurl,'gks_acc_pay' as doc_table
      from gks_acc_pay
      where aade_invoicemark='".$db_link->escape_string($input_mark)."' and aade_qrurl<>''
      union
      select id_whi_mov as rid,aade_qrurl,'gks_whi_mov' as doc_table
      from gks_whi_mov
      where aade_invoicemark='".$db_link->escape_string($input_mark)."' and aade_qrurl<>''";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $ret['qrUrl']=trim_gks($row['aade_qrurl']);
        $sql="update gks_aade_delivery_note set 
        qrUrl='".$db_link->escape_string($ret['qrUrl'])."'
        where id_aade_delivery_note=".$ret['rec_id'];  
        $result = $db_link->query($sql);  
        if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
      } else {
        $cmd='';if (isset($params['cmd'])) $cmd=$params['cmd'];
        $ret2=gks_get_qrurl_from_mark_from_mydata(['mark'=>$input_mark,'cid'=>$params['cid'],'cmd'=>$cmd]);
        //ta errors den mas noiazoyn edo ??
        if ($ret2['success']==false and $cmd=='get_qrUrl') {
          $ret['message']=$ret2['message'];
          return $ret;
        }
        if ($ret2['success']) {
          if ($ret2['qrUrl']!='') $ret['qrUrl']=$ret2['qrUrl'];
          if (isset($ret2['vat_issuer']))   $ret['vat_issuer']=$ret2['vat_issuer'];
          if (isset($ret2['vat_customer'])) $ret['vat_customer']=$ret2['vat_customer'];
        }
      }
      
    }
    return $ret;
  }
  
  if ($input_mark!='') {
    $sql="select id_acc_inv as rid,aade_qrurl,'gks_acc_inv' as doc_table
    from gks_acc_inv
    where aade_invoicemark='".$db_link->escape_string($input_mark)."'
    union
    select id_acc_pay as rid,aade_qrurl,'gks_acc_pay' as doc_table
    from gks_acc_pay
    where aade_invoicemark='".$db_link->escape_string($input_mark)."'
    union
    select id_whi_mov as rid,aade_qrurl,'gks_whi_mov' as doc_table
    from gks_whi_mov
    where aade_invoicemark='".$db_link->escape_string($input_mark)."'";
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
    if ($result->num_rows>=1) {
      //mono i proti eggrafi ftanei, giati thelo to qrurl
      $row = $result->fetch_assoc();
      $found_qrUrl=trim_gks($row['aade_qrurl']);
      $found_vat_issuer='';
      $found_vat_customer='';
      if ($found_qrUrl!='') {
        $sql="select * from gks_aade_delivery_note where qrUrl='".$db_link->escape_string($found_qrUrl)."'";
        $result = $db_link->query($sql);  
        if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
        if ($result->num_rows>=1) {
          $row = $result->fetch_assoc();        
          $sql="update gks_aade_delivery_note set 
          mark='".$db_link->escape_string($input_mark)."'
          where id_aade_delivery_note=".$row['id_aade_delivery_note'];
          $result = $db_link->query($sql);  
          if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
          
          $ret['success']=true;
          $ret['message']=gks_lang('Βρέθηκε το QRCode URL');
          $ret['rec_id'] = $row['id_aade_delivery_note']; 
          $ret['mark']=trim_gks($input_mark);
          $ret['qrUrl']=trim_gks($found_qrUrl);
          $ret['vat_issuer']=trim_gks($row['vat_issuer']);
          $ret['vat_customer']=trim_gks($row['vat_customer']);  
          return $ret;          
        }
      }
      
      $sql="insert into gks_aade_delivery_note (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      mark,qrUrl,vat_issuer,vat_customer
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      '".$db_link->escape_string($input_mark)."',
      '".$db_link->escape_string($found_qrUrl)."',
      '".$db_link->escape_string($found_vat_issuer)."',
      '".$db_link->escape_string($found_vat_customer)."')";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
      $ret['success']=true;
      $ret['message']=gks_lang('Βρέθηκε το QRCode URL');
      $ret['rec_id'] = $db_link->insert_id; 
      $ret['mark']=trim_gks($input_mark);
      $ret['qrUrl']=trim_gks($found_qrUrl);
      $ret['vat_issuer']=trim_gks($found_vat_issuer);
      $ret['vat_customer']=trim_gks($found_vat_customer);  
      return $ret;
            
    } else {
      $sql="insert into gks_aade_delivery_note (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      mark
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      '".$db_link->escape_string($input_mark)."')";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
      $ret['success']=true;
      $ret['message']=gks_lang('Δεν βρέθηκε το QRCode URL'); 
      $ret['rec_id'] = $db_link->insert_id; 
      $ret['mark']=trim_gks($input_mark);

      $cmd='';if (isset($params['cmd'])) $cmd=$params['cmd'];
      $ret2=gks_get_qrurl_from_mark_from_mydata(['mark'=>$input_mark,'cid'=>$params['cid'],'cmd'=>$cmd]);
      //ta errors den mas noiazoyn edo ??
      if ($ret2['success']==false and $cmd=='get_qrUrl') {
        $ret['message']=$ret2['message'];
        return $ret;
      }
      if ($ret2['success']) {
        if ($ret2['qrUrl']!='') $ret['qrUrl']=$ret2['qrUrl'];
        if (isset($ret2['vat_issuer']))   $ret['vat_issuer']=$ret2['vat_issuer'];
        if (isset($ret2['vat_customer'])) $ret['vat_customer']=$ret2['vat_customer'];
      }
              
      // edit here die('ggggggggg');
      return $ret;      
    }
  } else if ($input_qrUrl!='') {
    $myparse=gks_parse_aade_gr_qrcode($input_qrUrl);
    $found_mark=$myparse['found_mark'];
    $found_vat_issuer=$myparse['found_vat_issuer'];
    $found_vat_customer=$myparse['found_vat_customer'];
    
    //print '<pre>ssssssss';print_r($myparse);die();
    
    if ($found_mark!='') {
      //mipos iparxei kataxorisi mono me to mark
      $sql="select * from gks_aade_delivery_note where mark='".$db_link->escape_string($found_mark)."'";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $ret['success']=true;
        $ret['message']=gks_lang('Βρέθηκε το ΜΑΡΚ');
        $ret['rec_id'] = intval($row['id_aade_delivery_note']); 
        $ret['mark']=trim_gks($found_mark);
        $ret['qrUrl']=trim_gks($input_qrUrl);
        $ret['vat_issuer']=trim_gks($found_vat_issuer);
        $ret['vat_customer']=trim_gks($found_vat_customer); 
                
        $sql="update gks_aade_delivery_note set
        qrUrl='".$db_link->escape_string($input_qrUrl)."',
        vat_issuer='".$db_link->escape_string($found_vat_issuer)."',
        vat_customer='".$db_link->escape_string($found_vat_customer)."'
        where mark=".$db_link->escape_string($found_mark);
        $result = $db_link->query($sql);  
        if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
        return $ret;
        
      } else {
        
        $sql="insert into gks_aade_delivery_note (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        mark,qrUrl,vat_issuer,vat_customer
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        '".$db_link->escape_string($found_mark)."',
        '".$db_link->escape_string($input_qrUrl)."',
        '".$db_link->escape_string($found_vat_issuer)."',
        '".$db_link->escape_string($found_vat_customer)."')";
        $result = $db_link->query($sql);  
        if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
        $ret['success']=true;
        $ret['message']=gks_lang('Βρέθηκε το ΜΑΡΚ');
        $ret['rec_id'] = $db_link->insert_id; 
        $ret['mark']=trim_gks($found_mark);
        $ret['qrUrl']=trim_gks($input_qrUrl);
        $ret['vat_issuer']=trim_gks($found_vat_issuer);
        $ret['vat_customer']=trim_gks($found_vat_customer);  
        return $ret;
      //} else {
      //  $ret['message']=gks_lang('Δεν βρέθηκε το ΜΑΡΚ');  
      //  return $ret;
      }
    } else {
      $sql="insert into gks_aade_delivery_note (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      qrUrl
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      '".$db_link->escape_string($input_qrUrl)."')";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
      $ret['success']=true;
      $ret['message']=gks_lang('Δεν βρέθηκε το ΜΑΡΚ');
      $ret['rec_id'] = $db_link->insert_id; 
      $ret['mark']='';
      $ret['qrUrl']=trim_gks($input_qrUrl);
      $ret['vat_issuer']='';
      $ret['vat_customer']='';  
      return $ret;      
      
    }
    //echo '<pre>ssddddd '.$found_mark;die();
  }
  
  
  return $ret;
}

function gks_parse_aade_gr_qrcode($input_qrUrl) {
  $found_mark='';
  $found_vat_issuer='';
  $found_vat_customer='';
  
  $aaa=file_get_contents($input_qrUrl);
  $pos1=strpos($aaa, 'name="tmark" readonly');
  if ($pos1!==false) {
    $pos2=strpos($aaa, '</td>',$pos1+21+5);
    if ($pos2!==false) {
      $text=substr($aaa, $pos1,$pos2-$pos1);
      $pos1=strpos($text, '>');
      if ($pos1!==false) {
        $found_mark=substr($text, $pos1+1);
      } 
      //echo '<pre>aaaa'.$text;die();
    }
  }
  $pos1=strpos($aaa, 'name="vatnumber"');
  if ($pos1!==false) {
    $pos2=strpos($aaa, '">',$pos1+16+5);
    if ($pos2!==false) {
      $text=substr($aaa, $pos1,$pos2-$pos1);
      $pos1=strpos($text, 'value="');
      if ($pos1!==false) {
        $found_vat_issuer=substr($text, $pos1+7);
      }
      //echo '<pre>aaaa '.$found_vat_issuer;die();
    }
  }
  $pos1=strpos($aaa, 'name="crvatnumber"');
  if ($pos1!==false) {
    $pos2=strpos($aaa, '">',$pos1+18+5);
    if ($pos2!==false) {
      $text=substr($aaa, $pos1,$pos2-$pos1);
      $pos1=strpos($text, 'value="');
      if ($pos1!==false) {
        $found_vat_customer=substr($text, $pos1+7);
      } 
      //echo '<pre>aaaa '.$found_vat_customer;die();
    }
  }  
  
  return [
    'found_mark'=>$found_mark,
    'found_vat_issuer'=>$found_vat_issuer,
    'found_vat_customer'=>$found_vat_customer,   
  ];
}
function gks_get_mydata_keys_per_company($params) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $cid=$params['cid'];
  
  $ret=array(
    'success'=>false, 
    'message'=>'generic error',
    'title'=>'',
    'mydata_user_id'=>'',
    'mydata_subscription_key'=>'',
    'mydata_live'=>'',
  );
  
  
  $company_id=0;
  $company_sub_id=0;
  $parts=explode('|',$cid);
  if (count($parts)==2) {
  	$company_id=intval($parts[0]);
  	$company_sub_id=intval($parts[1]);
  }
  if ($company_id<=0 and $company_sub_id<=0) {
    $ret['message']=gks_lang('Επιλέξτε ποια εταιρεία αφορά η συγκεκριμένη ενέργεια');
    debug_mail(false,$ret['message'],'');return $ret;}
  
  if ($company_sub_id==0)	 {
    $sql="select company_title as title,
    aade_mydata_user_id as mydata_user_id,
    aade_mydata_subscription_key as mydata_subscription_key,
    aade_mydata_live as mydata_live
    from gks_company 
    where id_company=".$company_id."
    and company_disable=0";
  } else {
    $sql="select company_sub_title as title,
    aade_mydata_user_id_sub as mydata_user_id,
    aade_mydata_subscription_key_sub as mydata_subscription_key,
    aade_mydata_live_sub as mydata_live
    from gks_company_subs 
    where id_company_sub=".$company_sub_id."
    and company_id=".$company_id."
    and company_sub_disable=0";
  }
  $result = $db_link->query($sql);  
  if (!$result) {
    $ret['message']='error sql';
    debug_mail(false,$ret['message'],'');return $ret;}
  if ($result->num_rows!=1) {
    $ret['message']=gks_lang('Δεν βρέθηκε η εταιρεία ή δεν είναι ενεργή');
    debug_mail(false,$ret['message'],'');return $ret;}
  $row = $result->fetch_assoc();
  $title=$row['title'];
  $mydata_user_id=trim_gks($row['mydata_user_id']);
  $mydata_subscription_key=trim_gks($row['mydata_subscription_key']);
  $mydata_live=intval($row['mydata_live']);
  
  if ($mydata_user_id=='' or $mydata_subscription_key=='') {
    $ret['message']=str_replace('%1',$title,gks_lang('Η εταιρεία %1 δεν έχει κωδικούς myData'));
    debug_mail(false,$ret['message'],'');return $ret;}

  $ret['success']=true; 
  $ret['message']='OK';
  $ret['title']=$title;
  $ret['mydata_user_id']=$mydata_user_id;
  $ret['mydata_subscription_key']=$mydata_subscription_key;
  $ret['mydata_live']=$mydata_live;

  
  return $ret;
}


function gks_get_qrurl_from_mark_from_mydata($params) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $cid=$params['cid'];
  
  $ret=array(
    'success'=>false, 
    'message'=>'generic error',
    'qrUrl'=>'',
  );
  
  $ret2=gks_get_mydata_keys_per_company(['cid'=>$cid]);
  if ($ret2['success']==false) {
    $ret['message']=$ret2['message'];
    return $ret;
  }

  $title=$ret2['title'];
  $mydata_user_id=$ret2['mydata_user_id'];
  $mydata_subscription_key=$ret2['mydata_subscription_key'];
  $mydata_live=$ret2['mydata_live'];

  //esteilan alloi kai me affora
  $aade_url=($mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).'RequestDocs?mark='.($params['mark']-1);
  $aade_url.='&maxMark='.($params['mark']+1);
  //echo '<pre>uuuuuuuu '.$mydata_user_id.'|'.$mydata_subscription_key.'|'.$aade_url;die();
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
  
  $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
  //file_put_contents('/var/www/php/test.easyfilesselection.com/httpdocs/my/temp/hhh.txt',$result);    
  if ($gks_curl_http_code==200 and strpos($result,$params['mark'])!=='') {
    try {
      $xml = new SimpleXMLElement($result, LIBXML_NOERROR);
      if (isset($xml->invoicesDoc)) {
        $invoicesDoc=$xml->invoicesDoc;
        foreach ($invoicesDoc->children() as $invoice) {
          $mark_doc=(string)$invoice->mark;
          if ($mark_doc==$params['mark']) {
            $ret['qrUrl']=(string)$invoice->qrCodeUrl;
            break;
          }
        }
      }
    } catch (Exception $e) {}  
  }  
  
  //echo '<pre>kkkkkkkkk '.$ret['qrUrl'];die();
  
  if ($ret['qrUrl']=='') {
    //den vrethike parapano
    //esteila ego
    $aade_url=($mydata_live==0 ? GKS_AADE_MYDATA_URL_TEST : GKS_AADE_MYDATA_URL_LIVE).'RequestTransmittedDocs?mark='.($params['mark']-1);
    $aade_url.='&maxMark='.($params['mark']+1);
    //echo '<pre>uuuuuuuu '.$mydata_user_id.'|'.$mydata_subscription_key.'|'.$aade_url;die();
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
    
    $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
    //file_put_contents('/var/www/php/test.easyfilesselection.com/httpdocs/my/temp/hhh.txt',$result);    
    if ($gks_curl_http_code==200 and strpos($result,$params['mark'])!=='') {
      try {
        $xml = new SimpleXMLElement($result, LIBXML_NOERROR);
        if (isset($xml->invoicesDoc)) {
          $invoicesDoc=$xml->invoicesDoc;
          foreach ($invoicesDoc->children() as $invoice) {
            $mark_doc=(string)$invoice->mark;
            if ($mark_doc==$params['mark']) {
              $ret['qrUrl']=(string)$invoice->qrCodeUrl;
              break;
            }
          }
        }
      } catch (Exception $e) {}  
    }  
  }
    
  if ($ret['qrUrl']!='') {
    $sql="update gks_aade_delivery_note set 
    qrUrl='".$db_link->escape_string($ret['qrUrl'])."'
    where mark='".$db_link->escape_string($params['mark'])."'";
    $result = $db_link->query($sql);  
    if (!$result) {
      $ret['message']='error sql';
      debug_mail(false,$ret['message'],'');return $ret;}

    $myparse=gks_parse_aade_gr_qrcode($ret['qrUrl']);
    $found_mark=$myparse['found_mark'];
    $found_vat_issuer=$myparse['found_vat_issuer'];
    $found_vat_customer=$myparse['found_vat_customer'];      
    if ($found_mark!='') {
      $sql="update gks_aade_delivery_note set 
      mark='".$db_link->escape_string($found_mark)."',
      vat_issuer='".$db_link->escape_string($found_vat_issuer)."',
      vat_customer='".$db_link->escape_string($found_vat_customer)."'
      where mark='".$db_link->escape_string($params['mark'])."'";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error';return $ret;}
      
      $ret['vat_issuer']=trim_gks($found_vat_issuer);
      $ret['vat_customer']=trim_gks($found_vat_customer);        
    }
  }

  if ($ret['qrUrl']!='') {
    $ret['success']=true;
  } else { 
    $ret['success']=false;
  }
  return $ret;
}

function gks_aade_delivery_note_parse_xml_status($xml_input) {
  $return = array(
    'success' => false, 
    'message' => base64_encode('generic error'), 
    'html'=>'',
    'aade_delivery_status'=>'agnosto',
  );

  $html=[];
  try {
    $xml = new SimpleXMLElement($xml_input, LIBXML_NOERROR);
    
    $rootnode = $xml->xpath('/GetDeliveryNoteStatusResponse');
    if (count($rootnode)!=1) {
      $return['message']= gks_lang('Σφάλμα κατά την αναγνώριση του XML (GetDeliveryNoteStatusResponse)');
      debug_mail(false,$return['message'],$xml_input); return $return;
                
    }
    $nodes=$xml->xpath('/GetDeliveryNoteStatusResponse/invoiceMark');
    $xml_mark='';if (count($nodes)==1) $xml_mark=((string)$nodes[0]);
    $html[]=gks_lang('ΜΑΡΚ').': <b>'.$xml_mark.'</b>';
     
    $nodes=$xml->xpath('/GetDeliveryNoteStatusResponse/status');
    $xml_status='';if (count($nodes)==1) $xml_status=((string)$nodes[0]);
    $html[]=gks_lang('Κατάσταση').': <span class="aade_delivery_status_'.$xml_status.'">'.getAADE_InvoiceDeliveryStatus($xml_status).'</span>';
    $return['aade_delivery_status']=$xml_status;

    $nodes=$xml->xpath('/GetDeliveryNoteStatusResponse/dispatchTimestamp');
    $xml_dispatchTimestamp='';if (count($nodes)==1) $xml_dispatchTimestamp=showDate(strtotime((string)$nodes[0]),'d/m/Y H:i:s',1);
    if ($xml_dispatchTimestamp!='') $html[]=gks_lang('Ημερομηνία και Ώρα Εκκίνησης/Μεταφόρτωσης Διακίνησης').': <b>'.$xml_dispatchTimestamp.'</b>';
    
    $lifecycleHistory=[];
    $nodes = $xml->xpath('/GetDeliveryNoteStatusResponse/lifecycleHistory');
    foreach ($nodes as $item) {
      $tt=[];
      $lch_eventType=(string)$item->eventType;
      $tt[]=gks_lang('Τύπος του γεγονότος').': <span class="aade_delivery_event_type_'.$lch_eventType.'">'.getAADE_DeliveryEventType($lch_eventType).'</span>';
      
      $lch_eventTimestamp=(string)$item->eventTimestamp;
      $tt[]=gks_lang('Πότε έγινε').': <b>'.showDate(strtotime($lch_eventTimestamp),'d/m/Y H:i:s',1).'</b>';
      
      $lch_actorVat=(string)$item->actorVat;
      $tt[]=gks_lang('ΑΦΜ Χρήστη που δημιούργησε το συμβάν').': <b>'.$lch_actorVat.'</b> '.gks_get_user_from_afm($lch_actorVat);
      
      $lch_mark=(string)$item->mark;
      if ($lch_mark!='') $tt[]=gks_lang('ΜΑΡΚ γεγονότος').': <b>'.$lch_mark.'</b>';
      
      $lch_transportDetails=$item->transportDetails;
      if (count($lch_transportDetails)==1) {
        $mm=[];
        $lch_vehicleNumber=(string)$item->transportDetails->vehicleNumber;
        if ($lch_vehicleNumber!='') $mm[]='<li>'.gks_lang('Αριθμός Μεταφορικού Μέσου').': <b>'.$lch_vehicleNumber.'</b></li>';
        
        $lch_transportType=(string)$item->transportDetails->transportType;
        if ($lch_transportType!='') $mm[]='<li>'.gks_lang('Είδος Μεταφορικού Μέσου').': <b>'.getAADE_TransportTypeDescr($lch_transportType).'</b></li>';
        
        $lch_timeStamp=(string)$item->transportDetails->timeStamp;
        if ($lch_timeStamp!='') $mm[]='<li>'.gks_lang('Πότε').': <b>'.showDate(strtotime($lch_timeStamp),'d/m/Y H:i:s',1).'</b></li>';
        
        $lch_carrierVatNumber=(string)$item->transportDetails->carrierVatNumber;
        if ($lch_carrierVatNumber!='') $mm[]='<li>'.gks_lang('ΑΦΜ Μεταφορικής Εταιρείας').': <b>'.$lch_carrierVatNumber.'</b> '.gks_get_user_from_afm($lch_carrierVatNumber).'</li>';
        
        $lch_pNumber=(string)$item->transportDetails->pNumber;
        if ($lch_pNumber!='') $mm[]='<li>'.gks_lang('Αριθμός κυκλοφορίας <b>Ρ</b>').': <b>'.$lch_pNumber.'</b></li>';
        
        $lch_location=$item->transportDetails->location;
        if (count($lch_location)==1) {
          $lch_longitude=floatval((string)$item->transportDetails->location->longitude);
          $lch_latitude=floatval((string)$item->transportDetails->location->latitude);
          if ($lch_longitude!=0 or $lch_longitude!=0) {
            $mm[]='<li>'.gks_lang('Στίγμα').': <b>'.$lch_latitude.' '.$lch_longitude.'</b> <a href="https://www.google.com/maps/search/?api=1&query='.$lch_latitude.','.$lch_longitude.'" target="_blank"><i class="fas fa-map-marker-alt"></i></a></li>';
          }
        }
        if (count($mm)>0) {
          $tt[]='<span>'.gks_lang('Λεπτομέρειες Μεταφοράς').':</span><ul>'.implode('',$mm).'</ul>';
        }
      }

      $lch_outcomeDetails=$item->outcomeDetails;
      if (count($lch_outcomeDetails)==1) {
        $mm=[];
        $lch_outcome=(string)$item->outcomeDetails->outcome;
        if ($lch_outcome!='') $mm[]='<li>'.gks_lang('Το αποτέλεσμα της παράδοσης').': <span class="aade_delivery_lch_outcome_'.$lch_outcome.'">'.getAADE_lch_outcome($lch_outcome).'</span></li>';
      
        $lch_deliveredWithoutRecipient=(string)$item->outcomeDetails->deliveredWithoutRecipient;
        if ($lch_deliveredWithoutRecipient!='') $mm[]='<li>'.gks_lang('Η παράδοση έγινε χωρίς την παρουσία του παραλήπτη').': <b>'.($lch_deliveredWithoutRecipient=='true' ? 'Ναι' : 'Όχι').'</b></li>';
      
        $lch_deliveredPackaging=$item->outcomeDetails->deliveredPackaging;
        if (count($lch_deliveredPackaging)>=1) {
          
          $kk1=[];
          foreach ($lch_deliveredPackaging as $dpitem) {
            $kk2=[];
            $pd_packagingType= (string)$dpitem->packagingType;
            if ($pd_packagingType!='') $kk2[]=gks_lang('Είδος Συσκευασίας').': <b>'.getAADE_PackagingTypeDescr($pd_packagingType).'</b>';
            
            $pd_quantity= (string)$dpitem->quantity;
            if ($pd_quantity!='') $kk2[]=gks_lang('Πλήθος').': <b>'.$pd_quantity.'</b>';
            
            $pd_otherPackagingTypeTitle= (string)$dpitem->otherPackagingTypeTitle;
            if ($pd_otherPackagingTypeTitle!='') $kk2[]=gks_lang('Τίτλος για Λοιπά Είδη Συσκευασίας').': <b>'.$pd_otherPackagingTypeTitle.'</b>';
            
            
            $kk1[]='<li>'.implode('<br>',$kk2).'</li>';
            
            
          }
          if (count($kk1)>0) {
            $mm[]='<span>'.gks_lang('Πληροφορίες Συσκευασίας').':</span><ol>'.implode('',$kk1).'</ol>';
          }
        }
        
        
        if (count($mm)>0) {
          $tt[]='<span>'.gks_lang('Λεπτομέρειες για το αποτέλεσμα της παράδοσης').':</span><ul>'.implode('',$mm).'</ul>';
        }
      }       
      
      $lch_rejectionDetails=$item->rejectionDetails;
      if (count($lch_rejectionDetails)==1) {
        $mm=[];
        $rd_reason=(string)$item->rejectionDetails->reason;
        if ($rd_reason!='') $mm[]='<li>'.gks_lang('Αιτιολογία απόρριψης').': <b>'.$rd_reason.'</b></li>';
        
        if (count($mm)>0) {
          $tt[]='<span>'.gks_lang('Λεπτομέρειες για την απόρριψη').':</span><ul>'.implode('',$mm).'</ul>';
        }
      }
      
      
      $lifecycleHistory[]='<li>'.implode('<br>',$tt).'</li>';
    }
    if (count($lifecycleHistory)>0) {
      $html[]='<span>'.gks_lang('Ιστορικό Γεγονότων Διακίνησης').':</span><ol>'.implode('',$lifecycleHistory).'</ol>';
    }
     
    $return['html']= implode('<br>',$html);
    
    
  } catch (Exception $e) { 
    $return['message']= gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)');
    debug_mail(false,$return['message'],$xml_input); return $return;
  }  
   
  $return['success']=true;
  return $return;    
  
}

function gks_admin_aade_delivery_note_cmd_log($sxolio) {
  global $db_link;
  global $id_aade_delivery_note;
  global $my_wp_user_id;
  //$sxolio=chacke katastasi<br>'.$return['message'];
  $sql="insert into gks_aade_delivery_note_log (
  aade_delivery_note_id,add_date,user_id,sxolio
  ) values (
  ".$id_aade_delivery_note.",now(),".$my_wp_user_id.",
  '".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
        
}
function gks_get_user_from_afm($afm) {
  global $db_link;
  if (trim_gks($afm)=='') return '';
  $sql="SELECT id_company,company_title, company_eponimia FROM gks_company WHERE company_afm='".$db_link->escape_string($afm)."' order by id_company desc";
  $result = $db_link->query($sql);  
  if (!$result) {debug_mail(false,'error sql',$sql); return '';}
  if ($result->num_rows>=1) {  
    $row = $result->fetch_assoc();      
    $ret='<a href="admin-company-item.php?id='.$row['id_company'].'">';
    if (trim_gks($row['company_title'])!='') $ret.=trim_gks($row['company_title']);
    else if (trim_gks($row['company_eponimia'])!='') $ret.=trim_gks($row['company_eponimia']);
    else $ret.='#'.$row['id_company'];
    $ret.='</a>';
    return $ret;
  }
  
  $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.ID,".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  gks_users.title, gks_users.eponimia
  FROM gks_users 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
  AND gks_users.afm='".$db_link->escape_string($afm)."'
  ORDER BY ".GKS_WP_TABLE_PREFIX."users.ID DESC;";
  $result = $db_link->query($sql);  
  if (!$result) {debug_mail(false,'error sql',$sql); return '';}
  if ($result->num_rows>=1) {  
    $row = $result->fetch_assoc();      
    $ret='<a href="admin-users-item.php?id='.$row['ID'].'">';
    if (trim_gks($row['title'])!='') $ret.=trim_gks($row['title']);
    else if (trim_gks($row['eponimia'])!='') $ret.=trim_gks($row['eponimia']);
    else if (trim_gks($row['gks_nickname'])!='') $ret.=trim_gks($row['gks_nickname']);
    else $ret.='#'.$row['ID'];
    $ret.='</a>';
    return $ret;
  }
  return '';
}
