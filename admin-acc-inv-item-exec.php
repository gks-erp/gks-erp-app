<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id<>-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Παραστατικού').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');






$_gks_session['temp_mypropertiesheight'] = 0;
if (isset($_POST['mypropertiesheight'])) $_gks_session['temp_mypropertiesheight']=intval($_POST['mypropertiesheight']); gks_erp_cookie_save();
$inv_state=''; if (isset($_POST['inv_state'])) $inv_state=trim_gks(base64_decode($_POST['inv_state']));

if ($inv_state=='create_acc_pay') {
  //echo '<pre>'.$inv_state.' '.$id; die();   
  unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  $id_create_acc_pay=gks_acc_inv_create_acc_pay($id);
  if (is_array($id_create_acc_pay) and count($id_create_acc_pay) > 0) {
    //echo '<pre>'.$pay_state.' '.$id_credit_memo; die();
    if (count($id_create_acc_pay)==1) {
      $message=gks_lang('Η πληρωμή έχει δημιουργηθεί').'<br>'.
      gks_lang('To ID της είναι').' <b>'.$id_create_acc_pay[0].'</b><br>'.
      gks_lang('Θα πρέπει να την ελέγξετε και να την εκδώσετε').'<br>'.
      '<a class="gks_link" href="admin-acc-pay-item.php?id='.$id_create_acc_pay[0].'">'.gks_lang('Προβολή').'</a>';
    } else {
      
      $message=gks_lang('Έχουν δημιουργηθεί οι παρακάτω πληρωμές').':<br>';
      foreach ($id_create_acc_pay as $i => $value) {
         $message.='ID: <b>'.$value.'</b> '.
         '<a class="gks_link" href="admin-acc-pay-item.php?id='.$value.'">'.gks_lang('Προβολή').'</a><br>';
      } 
      $message.=gks_lang('Θα πρέπει να τις ελέγξετε και να τις εκδώσετε');
    }
    $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode($message));
    echo json_encode($return); die();
    
  } else {
    debug_mail(false,'error gks_orders_create_acc_pay',$id.' '.$id_create_acc_pay);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα')),'redirect'=> '');
    echo json_encode($return); die();
  }
}



$mybasketarray=false;
$cache_file='';if (isset($_POST['cache_file']) and trim_gks($_POST['cache_file'])!='') $cache_file=trim_gks($_POST['cache_file']);
if ($cache_file!= '' and file_exists(GKS_CACHE.$cache_file)) {
  $mybasketarray=json_decode(file_get_contents(GKS_CACHE.$cache_file), true);
}
$inv_state_old='';
$idiotites_old='';
$inv_acc_number_int_old=0;
$inv_acc_number_str_old='';
$inv_acc_ekdosi_date_old='';
$seira_code_old='';
$is_xeirografi_old=0;
$seira_isdeliverynote_old=0;


$myarray_old=array();
$myarray_line_old=array();

$row_old=array();
$products_old=array();
$extra_address_old=array();
$cancel_for_acc_inv_id=0;
$credit_memo_for_acc_inv_id=0;

$all_products_for_balance=array();

$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
  $row_old['inv_state']='010draft';
  $row_old['inv_acc_number_int']=0;
} else {
  $sql=select_gks_acc_inv()." where id_acc_inv=".$id; 
  
  if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  $sql.=" limit 1";
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row_old = $result->fetch_assoc();
  $inv_state_old=trim_gks($row_old['inv_state']);
  $idiotites_old=get_acc_inv_details_txt($id, $myarray_old, $myarray_line_old); 
  $inv_acc_number_int_old=$row_old['inv_acc_number_int']; 
  $inv_acc_number_str_old=trim_gks($row_old['inv_acc_number_str']); 
  $inv_acc_ekdosi_date_old=trim_gks($row_old['inv_acc_ekdosi_date']); 
  $seira_code_old=trim_gks($row_old['seira_code']); 
  $is_xeirografi_old=intval($row_old['is_xeirografi']); 
  $seira_isdeliverynote_old=intval($row_old['seira_isdeliverynote']);
  $cancel_for_acc_inv_id=$row_old['cancel_for_acc_inv_id'];
  $credit_memo_for_acc_inv_id=$row_old['credit_memo_for_acc_inv_id'];
  
  $sql="SELECT gks_acc_inv_products.*, gks_monades_metrisis.monada_descr, 
  gks_eshop_fpa_base.fpa_base_descr,
  gks_aade_katigoria_fpa.aade_katigoria_fpa_descr
  FROM ((gks_acc_inv_products 
  LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada)
  LEFT JOIN gks_eshop_fpa_base ON gks_acc_inv_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base)
  LEFT JOIN gks_aade_katigoria_fpa ON gks_acc_inv_products.product_fpa_aade_id = gks_aade_katigoria_fpa.id_aade_katigoria_fpa
  WHERE gks_acc_inv_products.acc_inv_id=".$id."
  ORDER BY gks_acc_inv_products.product_aa;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row = $result->fetch_assoc()) {
    $products_old[]=$row;
    if ($row['product_id']>0 and in_array($row['product_id'],$all_products_for_balance)==false)
      $all_products_for_balance[]=$row['product_id'];    
  }
  
  if ($row_old['address_extra']>0) {
    $sql="SELECT gks_users_extra_address.*, gks_nomoi.nomos_descr, gks_country.country_name
    FROM (gks_users_extra_address 
    LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
    LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
    WHERE gks_users_extra_address.id_users_extra_address=".$row_old['address_extra'];
    $result_select = $db_link->query($sql);        
    if (!$result_select) {
      debug_mail(false,'error sql',$sql);
      die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    }
    if ($result_select->num_rows==1) {
      $extra_address_old = $result_select->fetch_assoc();
    }    
  }
  
  $gks_custom_prepare=gks_custom_table_item_prepare('gks_acc_inv',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 

  
}

$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;
if ($row_old['inv_state']=='040cancelled' or $row_old['inv_state']=='070ypoekdosi' or $row_old['inv_state']=='080listing' or $row_old['inv_state']=='090ekdosi' or $row_old['inv_state']=='100payment') {
  $gks_lock=true;
} else {
  if ($row_old['inv_acc_number_int'] > 0 and $row_old['is_xeirografi']==0 and ($row_old['inv_state']=='010draft' or $row_old['inv_state']=='050proinvoice')) {
    $gks_number_lock=true;
  }
}
if ($credit_memo_for_acc_inv_id!=0) {
  $gks_number_lock=true;
  $gks_user_lock=true;
}
if ($cancel_for_acc_inv_id!=0) {
  $gks_lock=true;
}

$gks_lock_user=''; if (isset($_POST['gks_lock'])) $gks_lock_user=intval($_POST['gks_lock']);
if ($gks_lock!=$gks_lock_user) {
    debug_mail(false,'gks_lock != gks_lock_user',$gks_lock.' | '.$gks_lock_user);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατάστασης εγγραφής').' (1)<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}

$gks_number_lock_user=''; if (isset($_POST['gks_number_lock'])) $gks_number_lock_user=intval($_POST['gks_number_lock']);
if ($gks_number_lock!=$gks_number_lock_user) {
    debug_mail(false,'gks_number_lock != gks_number_lock_user',$gks_number_lock.' | '.$gks_number_lock_user);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατάστασης εγγραφής').' (2)<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}

$gks_user_lock_user=''; if (isset($_POST['gks_user_lock'])) $gks_user_lock_user=intval($_POST['gks_user_lock']);
if ($gks_user_lock!=$gks_user_lock_user) {
    debug_mail(false,'gks_user_lock != gks_user_lock_user',$gks_user_lock.' | '.$gks_user_lock_user);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατάστασης εγγραφής').' (3)<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}


$aade_send=false;$paroxos_send=false;
if ($inv_state=='aade_send') {
  $aade_send=true; 
  $inv_state='';
} else if ($inv_state=='paroxos_send') {
  $paroxos_send=true; 
  $inv_state='';
}


$affect_balance=0; if (isset($_POST['affect_balance'])) $affect_balance=intval($_POST['affect_balance']);  
if ($affect_balance!=1) $affect_balance=0;
$affect_balance_all_poso=0; if (isset($_POST['affect_balance_all_poso'])) $affect_balance_all_poso=intval($_POST['affect_balance_all_poso']);  
if ($affect_balance_all_poso!=1) $affect_balance_all_poso=0;
$affect_balance_all_poso_type=''; if (isset($_POST['affect_balance_all_poso_type'])) $affect_balance_all_poso_type=trim_gks($_POST['affect_balance_all_poso_type']);
if ($affect_balance_all_poso_type=='') $affect_balance_all_poso_type='total_price_net'; 
$affect_balance_poso=0;  if (isset($_POST['affect_balance_poso']))  $affect_balance_poso=floatval($_POST['affect_balance_poso']);


$assigned_id=0; if (isset($_POST['assigned_id'])) $assigned_id=intval($_POST['assigned_id']);
$merchant_ref_trns=''; if (isset($_POST['merchant_ref_trns'])) $merchant_ref_trns=trim_gks(base64_decode($_POST['merchant_ref_trns']));

if ($GKS_CRM_ENABLE) {
  $crm_channel_id=0; if (isset($_POST['crm_channel_id'])) $crm_channel_id=intval($_POST['crm_channel_id']);
  $crm_channel_contact_id=0; if (isset($_POST['crm_channel_contact_id'])) $crm_channel_contact_id=intval($_POST['crm_channel_contact_id']);
  $crm_channel_campain_id=0; if (isset($_POST['crm_channel_campain_id'])) $crm_channel_campain_id=intval($_POST['crm_channel_campain_id']);
  $crm_channel_url=''; if (isset($_POST['crm_channel_url'])) $crm_channel_url=trim_gks(base64_decode($_POST['crm_channel_url']));
  $crm_channel_code=''; if (isset($_POST['crm_channel_code'])) $crm_channel_code=trim_gks(base64_decode($_POST['crm_channel_code']));
  $crm_channel_text=''; if (isset($_POST['crm_channel_text'])) $crm_channel_text=trim_gks(base64_decode($_POST['crm_channel_text']));
  
  if ($crm_channel_id<=0) {
    $crm_channel_contact_id=0;
    $crm_channel_campain_id=0;
    $crm_channel_url='';
    $crm_channel_code='';
    $crm_channel_text='';
  } else {
    $sql_channel="select * from gks_crm_channel_sale where id_crm_channel_sale=".$crm_channel_id;
    $result_channel = $db_link->query($sql_channel);        
    if (!$result_channel) {
      debug_mail(false,'error sql',$sql_channel);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    if ($result_channel->num_rows!=1) {
      debug_mail(false,'id_crm_channel_sale not found',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το κανάλι πωλήσεων').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
      echo json_encode($return); die();}
    $row_channel = $result_channel->fetch_assoc();
    if ($row_channel['crm_channel_has_contact']==0)  $crm_channel_contact_id=0;
    if ($row_channel['crm_channel_has_campain']==0)  $crm_channel_campain_id=0;
    if ($row_channel['crm_channel_has_url']==0)  $crm_channel_url='';
    if ($row_channel['crm_channel_has_code']==0)  $crm_channel_code='';
    if ($row_channel['crm_channel_has_text']==0)  $crm_channel_text='';
  }  
}


if ($gks_lock) {
  
  
  
  $warning_message='';
  $sql_ekdosi='';
  if ($cancel_for_acc_inv_id!=0 && $inv_state=='090ekdosi') { 
    //otan to inv einai akoritiko gia allo
    $inv_acc_seira_id=$row_old['inv_acc_seira_id'];
    $has_ekdosi=false;
    $save_but_message='';
    gks_inv_get_ekdosi_numbers();
    $warning_message=$save_but_message;
    
    if ($has_ekdosi) {
      $sql_ekdosi="inv_acc_number_int=".$inv_acc_number_int_new.",
             inv_acc_number_str='".$db_link->escape_string($inv_acc_number_str_new)."',
             inv_acc_ekdosi_date=now(),
             inv_acc_seira_code='".$db_link->escape_string($inv_acc_seira_code_new)."',";
    }  
        
//    $return = array('success' => false, 'message' => base64_encode('<pre>gks_lock:'.$gks_lock.
//     "\ngks_number_lock:".$gks_number_lock.
//     "\naade_send:".$aade_send.
//     "\ninv_state:".$inv_state.
//     "\nhas_ekdosi:".$has_ekdosi.
//     "\ninv_acc_number_int_new:".$inv_acc_number_int_new.
//     "\ninv_acc_number_str_new:".$inv_acc_number_str_new.
//     "\ninv_acc_seira_code_new:".$inv_acc_seira_code_new.
//     "\nsql_ekdosi:".$sql_ekdosi
//    ));echo json_encode($return); die();    
//    //kostas
    
  } else if ($cancel_for_acc_inv_id==0 && $inv_state=='090ekdosi') { 
    //otan kanaoniki ekdosi, efoson einai idi apothikeumeno
    $inv_acc_seira_id=$row_old['inv_acc_seira_id'];
    $has_ekdosi=false;
    $save_but_message='';
    gks_inv_get_ekdosi_numbers();
    $warning_message=$save_but_message;
    
    if ($has_ekdosi) {
      $sql_ekdosi="inv_acc_number_int=".$inv_acc_number_int_new.",
             inv_acc_number_str='".$db_link->escape_string($inv_acc_number_str_new)."',
             inv_acc_ekdosi_date=now(),
             inv_acc_seira_code='".$db_link->escape_string($inv_acc_seira_code_new)."',";
    }  
    
      
    
  } else {
    
    if ($inv_state!='' and $inv_state!='010draft' and $inv_state!='040cancelled' and $inv_state!='credit_memo')  {
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος κατάσταση').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die(); }
  }
  
  //echo '<pre>aaaaaaaaaaaaaa';print $inv_state;die(); 
  
  if ($inv_state=='040cancelled') {
    $sql_canceled="SELECT id_acc_inv, cancel_for_acc_inv_id FROM gks_acc_inv WHERE cancel_for_acc_inv_id=".$id;
    $result_canceled = $db_link->query($sql_canceled);  
    if (!$result_canceled) {
      debug_mail(false,'error sql',$sql_canceled);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_canceled->num_rows>=1) {
      $row_canceled=$result_canceled->fetch_assoc();
      if ($row_canceled['cancel_for_acc_inv_id']!=0) {
        $message=gks_lang('Για αυτό το παραστατικό υπάρχει ήδη το ακυρωτικό παραστατικό με').'<br>'.
        'ID :<b>'.$row_canceled['id_acc_inv'].'</b><br>'.
        '<a class="gks_link" href="admin-acc-inv-item.php?id='.$row_canceled['id_acc_inv'].'">'.gks_lang('Προβολή').'</a>';
        debug_mail(false,'cancel_for_acc_inv_id='.$row_canceled['cancel_for_acc_inv_id'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die(); }
    }
    $sql_credit_memo="SELECT id_acc_inv, credit_memo_for_acc_inv_id FROM gks_acc_inv WHERE credit_memo_for_acc_inv_id=".$id;
    //die($sql_credit_memo);
    $result_credit_memo = $db_link->query($sql_credit_memo);  
    if (!$result_credit_memo) {
      debug_mail(false,'error sql',$sql_credit_memo);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_credit_memo->num_rows>=1) {
      $row_credit_memo=$result_credit_memo->fetch_assoc();
      if ($row_credit_memo['credit_memo_for_acc_inv_id']!=0) {
        $message=gks_lang('Για αυτό το παραστατικό υπάρχει ήδη το συσχετιζόμενο πιστωτικό παραστατικό με').'<br>'.
        'ID :<b>'.$row_credit_memo['id_acc_inv'].'</b><br>'.
        '<a class="gks_link" href="admin-acc-inv-item.php?id='.$row_credit_memo['id_acc_inv'].'">'.gks_lang('Προβολή').'</a>';
        debug_mail(false,'credit_memo_for_acc_inv_id='.$row_credit_memo['credit_memo_for_acc_inv_id'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die(); }
    }
    
  }
  
  
  //echo '<pre>'.$inv_state; die();
  
  if ($inv_state=='010draft' and $inv_state_old!='010draft' and $is_xeirografi_old==0 and $inv_acc_number_int_old>0) {
    //echo '<pre>vvv';die();
    $warning_message=gks_inv_to_draft($id);
    if ($warning_message!='') 
      $warning_message=gks_lang('Έγινε επαναφορά σε <b>Πρόχειρο</b> αλλά δεν μπόρεσε μηδενιστεί ο αριθμός του παραστατικού διότι').':<br>'.
                        $warning_message.'<br>'.gks_lang('Κάντε άμεσα τις αλλαγές και ξανα εκδώστε το').'<br>'.
                        gks_lang('Διαφορετικά θα δημιουργηθεί κενό στην αρίθμηση της σειράς');
  }
  //print '<pre>'.$warning_message;die();
  
  $note_doc=''; if (isset($_POST['note_doc'])) $note_doc=trim_gks(base64_decode($_POST['note_doc']));
  $note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));

  
  $tropos_apostolis=0; if (isset($_POST['tropos_apostolis'])) $tropos_apostolis=intval($_POST['tropos_apostolis']);  
  $tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);  
  $tropos_pliromis_one_multi=0; if (isset($_POST['tropos_pliromis_one_multi'])) $tropos_pliromis_one_multi=intval($_POST['tropos_pliromis_one_multi']);  
  $delivery_id_8=0; if (isset($_POST['delivery_id_8'])) $delivery_id_8=intval($_POST['delivery_id_8']);  
  $delivery_number=''; if (isset($_POST['delivery_number'])) $delivery_number=trim_gks(base64_decode($_POST['delivery_number']));
  $vehicle_number=''; if (isset($_POST['vehicle_number'])) $vehicle_number=trim_gks(base64_decode($_POST['vehicle_number']));
  
  
  if ($_POST['dispatch_date'] == '__/__/____') $_POST['dispatch_date']='';
  $dispatch_date=trim_gks(stripslashes(urldecode($_POST['dispatch_date'])));
  if ($dispatch_date!='') $dispatch_date = gks_myFormatDate($dispatch_date);
  if ($dispatch_date!=='') $dispatch_date=date('Y-m-d',$dispatch_date);
  
  if ($_POST['dispatch_time'] == '__:__') $_POST['dispatch_time']='';
  $dispatch_time=trim_gks(stripslashes(urldecode($_POST['dispatch_time'])));
  if ($dispatch_time!='') $dispatch_time = gks_myFormatTime($dispatch_time);
  if ($dispatch_time!=='') $dispatch_time=date('H:i:s',$dispatch_time);
  
  $kostos_apostolis=0; if (isset($_POST['kostos_apostolis'])) $kostos_apostolis=floatval($_POST['kostos_apostolis']);
  $kostos_pliromis=0;  if (isset($_POST['kostos_pliromis']))  $kostos_pliromis=floatval($_POST['kostos_pliromis']);

  $affect_balance=0; if (isset($_POST['affect_balance'])) $affect_balance=intval($_POST['affect_balance']);  
  if ($affect_balance!=1) $affect_balance=0;
  $affect_balance_all_poso=0; if (isset($_POST['affect_balance_all_poso'])) $affect_balance_all_poso=intval($_POST['affect_balance_all_poso']);  
  if ($affect_balance_all_poso!=1) $affect_balance_all_poso=0;
  $affect_balance_all_poso_type=''; if (isset($_POST['affect_balance_all_poso_type'])) $affect_balance_all_poso_type=trim_gks($_POST['affect_balance_all_poso_type']);
  if ($affect_balance_all_poso_type=='') $affect_balance_all_poso_type='total_price_net'; 
  $affect_balance_poso=0;  if (isset($_POST['affect_balance_poso']))  $affect_balance_poso=floatval($_POST['affect_balance_poso']);
  

  $delivery_method_type='';
  $sql_dmt="select delivery_method_type from gks_delivery_methods where id_delivery_method=".$tropos_apostolis;
  $result_dmt = $db_link->query($sql_dmt);  
  if (!$result_dmt) {
    debug_mail(false,'error sql',$sql_dmt);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_dmt->num_rows==1) {
    $row_dmt = $result_dmt->fetch_assoc();  
    $delivery_method_type=trim_gks($row_dmt['delivery_method_type']);
  }
  if (!($delivery_method_type=='delivery' or $delivery_method_type=='pelatis' or $delivery_method_type=='post')) {
    $delivery_number='';
    $vehicle_number='';
    $dispatch_date='';
    $dispatch_time='';
  }
  if ($tropos_apostolis!=8) $delivery_id_8=0;
  
  
  if ($inv_state=='040cancelled') gks_acc_inv_cancel_create($id,true);
  if ($inv_state=='credit_memo')  gks_acc_inv_credit_memo_create($id,true);
  
  $gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_acc_inv');
  
  $acc_inv_payment_array=array();
  gks_acc_inv_acc_inv_payment_part_a();
  
  $gks_price_total=floatval($row_old['gks_price_total']);
  $not_del_id_acc_inv_payment=array();
  gks_acc_inv_acc_inv_payment_part_b();
  
  //echo '<pre>ssssss '.$tropos_pliromis.'|'.$tropos_pliromis_one_multi;die();

  
  $sql="update gks_acc_inv set ";
  if ($inv_state!= '' and $inv_state!='credit_memo') {
    $sql.="inv_state='".$db_link->escape_string($inv_state)."', ";
  }
  
  $sql.=$sql_ekdosi;
  
  $sql.="
  note_doc='".$db_link->escape_string($note_doc)."',
  note_logistirio='".$db_link->escape_string($note_logistirio)."',
  tropos_apostolis=".$tropos_apostolis.",
  tropos_pliromis=".$tropos_pliromis.",
  tropos_pliromis_one_multi=".$tropos_pliromis_one_multi.",
  delivery_id_8=".$delivery_id_8.",
  delivery_number='".$db_link->escape_string($delivery_number)."',
  vehicle_number='".$db_link->escape_string($vehicle_number)."',
  dispatch_date=".($dispatch_date == '' ? 'null' : "'".$db_link->escape_string($dispatch_date)."'") .", 
  dispatch_time=".($dispatch_time == '' ? 'null' : "'".$db_link->escape_string($dispatch_time)."'") .", 
  kostos_apostolis=".number_format($kostos_apostolis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').", 
  kostos_pliromis=".number_format($kostos_pliromis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
  affect_balance=".$affect_balance.",
  affect_balance_all_poso=".$affect_balance_all_poso.",
  affect_balance_all_poso_type='".$db_link->escape_string($affect_balance_all_poso_type)."',";

  if ($affect_balance == 0) {
    $affect_balance_poso=0;
  } else {
    if ($affect_balance_all_poso==1) {
      switch ($affect_balance_all_poso_type) {
        case 'price_net':
          $affect_balance_poso=$row_old['gks_price_net'];
          break;  
        case 'price_netfpa':
          $affect_balance_poso=$row_old['gks_price_netfpa'];
          break;  
        case 'price_total':
          $affect_balance_poso=$row_old['gks_price_total'];
          break;  
        case 'pliroteo':
          $affect_balance_poso=$row_old['gks_price_total'] + $kostos_apostolis + $kostos_pliromis;
          break;  
        default:     
        
      }
    } else {
      //$affect_balance_poso=$affect_balance_poso;
    }
  }
  $sql.="affect_balance_poso=".number_format($affect_balance_poso, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",";
  
  $affect_balance_pros=$row_old['eidos_parastatikou_balance_pros'];
  
  
  //print '<pre>';print_r($row_old);die();
  
  if ($affect_balance_pros!=-1 and $affect_balance_pros!=1) {
    $affect_balance_pros=0;
  }  
  $sql.="affect_balance_pros=".$affect_balance_pros.",";

  $sql.="assigned_id=".$assigned_id.",
  merchant_ref_trns=". ($merchant_ref_trns =='' ? 'null' : "'".$db_link->escape_string($merchant_ref_trns)."'").",";
  
  
  if ($GKS_CRM_ENABLE) {
  $sql.=
  "crm_channel_id=".$crm_channel_id.",
  crm_channel_contact_id=".$crm_channel_contact_id.",
  crm_channel_campain_id=".$crm_channel_campain_id.",
  crm_channel_url=". ($crm_channel_url =='' ? 'null' : "'".$db_link->escape_string($crm_channel_url)."'").",
  crm_channel_code=". ($crm_channel_code =='' ? 'null' : "'".$db_link->escape_string($crm_channel_code)."'").",
  crm_channel_text=". ($crm_channel_text =='' ? 'null' : "'".$db_link->escape_string($crm_channel_text)."'").",";
  }
  
  
  //echo '<pre>'.$affect_balance_poso;die();
  
  $sql.="update_from_gks=1,
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."',
  session_id='".$_gks_id_session."'
  where id_acc_inv = ".$id." limit 1";  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  if (is_array($mybasketarray)) {
    $sql='';
    if (isset($mybasketarray['tropoi_apostolis_all']) and 
        isset($mybasketarray['tropos_apostolis']) and
        isset($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]) ) {
          $sql.="tropos_apostolis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]))."',";
    }
    if (isset($mybasketarray['tropoi_pliromis_all']) and 
        isset($mybasketarray['tropos_pliromis']) and
        isset($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]) ) {
          $sql.="kostos_pliromis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]))."',";
    }
    if ($sql!='') {
      $sql=substr($sql, 0, strlen($sql)-1);
      $sql="update gks_acc_inv set ".$sql." where id_acc_inv=".$id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
    }

  }
  
  gks_acc_inv_acc_inv_payment_part_c();
    
  $gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


//  $return = array('success' => false, 'message' => base64_encode('<pre>gks_lock:'.$gks_lock.
//   "\ngks_number_lock:".$gks_number_lock.
//   "\naade_send:".$aade_send.
//   "\ninv_state:".$inv_state.
//   "\ncancel_for_acc_inv_id:".$cancel_for_acc_inv_id
//  ));echo json_encode($return); die();
  
  
  
  $sql="update gks_acc_inv_products set p_inv_state='".$db_link->escape_string($inv_state!='' ? $inv_state : $inv_state_old)."' where acc_inv_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}    
  

  
  gks_whi_mov_balance_calc($all_products_for_balance);
  gks_whi_after_balance_for_inv($id);
  

  if ($aade_send) {

    $aade_mydata_live=''; if (isset($_POST['aade_mydata_live'])) $aade_mydata_live=intval($_POST['aade_mydata_live']);

    $force_options=array();
    
    if ($aade_mydata_live==0) {
      $force_options=array(
        'aade_mydata_sender_afm' => $GKS_AADE_MYDATA_SANDBOX_AFM,
        'aade_mydata_live' => $aade_mydata_live,
        'aade_branch' => $GKS_AADE_MYDATA_SANDBOX_BRANCE,
        'aade_mydata_user_id' => $GKS_AADE_MYDATA_SANDBOX_USER_ID,
        'aade_mydata_subscription_key' => $GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY,
      );
    }

    //print '<pre>';print_r($force_options);die();

    $force_options['doc_table']='gks_acc_inv';
    $ret=gks_aade_invoice($id,$force_options);
    
    //echo '<pre>σσσσσσσσσ';print_r($ret);die();

    $row_old['aade_errors']='';
    $myparams=[];
    if ($ret['success']==false and $ret['message']!='') $myparams['ret_aade_errors']=$ret['message'];
    gks_inv_sxolio_log($id,$row_old,$products_old,$extra_address_old,($ret['mydata_live']==0 ? gks_lang('Δοκιμαστική αποστολή σε myData').'<br>' : gks_lang('Πραγματική αποστολή σε myData').'<br>'),$myparams,$gks_custom_row_old);
    
    $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
    
    //print '<pre>'; print_r($ret); die(); 
    if ($ret['success']==false) {
      $return = array('success' => true, 'message' => base64_encode('error'),'redirect'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode($ret['message']));
      echo json_encode($return); die();
    } else {
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode(gks_lang('Επιτυχής αποστολή δεδομένων myData στην ΑΑΔΕ')));
      echo json_encode($return); die();      
    }
  } else if ($paroxos_send) {
    $paroxos_mydata_live=''; if (isset($_POST['paroxos_mydata_live'])) $paroxos_mydata_live=intval($_POST['paroxos_mydata_live']);

    $force_options=array();
    
    if ($paroxos_mydata_live==0) {
      $force_options=array(
        'paroxos_mydata_live' => $paroxos_mydata_live,
      );
    }

    //print '<pre>';print_r($force_options);die();
    $force_options['doc_table']='gks_acc_inv';
    $ret=gks_paroxos_invoice($id,$force_options);
    
    $row_old['aade_errors']='';
    $myparams=[];
    if ($ret['success']==false and $ret['message']!='') $myparams['ret_aade_errors']=$ret['message'];
    gks_inv_sxolio_log($id,$row_old,$products_old,$extra_address_old,($paroxos_mydata_live==false ? gks_lang('Δοκιμαστική αποστολή σε πάροχο').'<br>' : gks_lang('Πραγματική αποστολή σε πάροχο').'<br>'),$myparams,$gks_custom_row_old);
    
    $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
    
    //print '<pre>'; print_r($ret); die(); 
    if ($ret['success']==false) {
      $return = array('success' => true, 'message' => base64_encode('error'),'redirect'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode($ret['message']));
      echo json_encode($return); die();
    } else {
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode($ret['save_but_message']));
      echo json_encode($return); die();      
    }    
  } else {
    gks_inv_sxolio_log($id,$row_old,$products_old,$extra_address_old,'',[],$gks_custom_row_old);
  }
  
  if ($inv_state=='040cancelled') {
    $id_canceled=gks_acc_inv_cancel_create($id,false);
    if ($id_canceled>0) {
      //echo '<pre>'.$inv_state.' '.$id_canceled; die();
      $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
      
      $message=gks_lang('Το ακυρωτικό παραστατικό έχει δημιουργηθεί').'<br>'.
      gks_lang('To ID του είναι').' <b>'.$id_canceled.'</b><br>'.
      gks_lang('Θα πρέπει να το ελέγξετε και να το εκδώσετε').'<br>'.
      '<a class="gks_link" href="admin-acc-inv-item.php?id='.$id_canceled.'">'.gks_lang('Προβολή').'</a>';
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode($message));
      echo json_encode($return); die();
      
    } else {
      $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
      debug_mail(false,'error gks_acc_inv_cancel_create',$id.' '.$id_canceled);
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();
    }
  }
  
  if ($inv_state=='credit_memo') {
//    $return = array('success' => false, 'message' => base64_encode('<pre>gks_lock:'.$gks_lock.
//     "\ngks_number_lock:".$gks_number_lock.
//     "\naade_send:".$aade_send.
//     "\ninv_state:".$inv_state.
//     "\ncancel_for_acc_inv_id:".$cancel_for_acc_inv_id
//    ));echo json_encode($return); die();
    $id_credit_memo=gks_acc_inv_credit_memo_create($id,false);
    if ($id_credit_memo>0) {
      //echo '<pre>'.$inv_state.' '.$id_credit_memo; die();
      $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
      $message=gks_lang('Το πιστωτικό παραστατικό έχει δημιουργηθεί').'<br>'.
      gks_lang('To ID του είναι').' <b>'.$id_credit_memo.'</b><br>'.
      gks_lang('Θα πρέπει να το ελέγξετε και να το εκδώσετε').'<br>'.
      '<a class="gks_link" href="admin-acc-inv-item.php?id='.$id_credit_memo.'">'.gks_lang('Προβολή').'</a>';
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode($message));
      echo json_encode($return); die();
      
    } else {
      $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
      debug_mail(false,'error gks_acc_inv_credit_memo_create',$id.' '.$id_credit_memo);
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();
    }
    
    
  }
  
  $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
  //echo '<pre>'; echo $row_old['user_id'];die();
  
  $return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> '','save_but_message' => base64_encode($warning_message));
  echo json_encode($return); die();
  
  
}
    
if ($aade_send) {
  debug_mail(false,'Wrong state',$id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος κατάσταση').'<br>'.gks_lang('Ανανεώστε την σελίδα').' (2)'));
  echo json_encode($return); die(); }



if ($gks_number_lock) {
  $company_id=$row_old['company_id'];
  $company_sub_id=$row_old['company_sub_id'];
} else {
  $company_id=0;
  $company_sub_id=0;
  $user_companys=gks_get_companys_list();
  if (count($user_companys)==1) {
    foreach ($user_companys as $value) {
      $company_id=$value['id_company'];
      $company_sub_id=$value['id_company_sub'];
      break;
    }    
  } else {
    $company_id_sub_id=''; if (isset($_POST['company_id_sub_id'])) $company_id_sub_id=trim_gks(base64_decode($_POST['company_id_sub_id']));
    if ($company_id_sub_id!='') {
      $parts=explode('|',$company_id_sub_id);
      if (count($parts)==2) {
        $company_id=intval($parts[0]);
        $company_sub_id=intval($parts[1]);
        $found=false;
        foreach ($user_companys as $value) {
          if ($value['id_company'] == $company_id and $value['id_company_sub'] == $company_sub_id) {
            $found=true;
            break;
          }
        }
        if ($found==false) {$company_id=0;$company_sub_id=0;}
      }
    }
  }
}

if ($company_id<=0) {
  debug_mail(false,'company_id is not found',$company_id.' '.$company_sub_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την εταιρεία/υποκατάστημα')));
  echo json_encode($return); die();}  
  
$inv_acc_journal_id=0;if (isset($_POST['inv_acc_journal_id'])) $inv_acc_journal_id=intval($_POST['inv_acc_journal_id']);
if ($gks_number_lock) $inv_acc_journal_id=$row_old['inv_acc_journal_id'];
if ($inv_acc_journal_id<=0) {
  debug_mail(false,'inv_acc_journal_id is not found',$inv_acc_journal_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Ημερολόγιο')));
  echo json_encode($return); die();}  
  
$inv_acc_seira_id=0;if (isset($_POST['inv_acc_seira_id'])) $inv_acc_seira_id=intval($_POST['inv_acc_seira_id']);
if ($gks_number_lock) $inv_acc_seira_id=$row_old['inv_acc_seira_id'];
if ($inv_acc_seira_id<=0) {
  debug_mail(false,'inv_acc_seira_id is not found',$inv_acc_seira_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Σειρά')));
  echo json_encode($return); die();}  

$inv_acc_number_int_user=0;if (isset($_POST['inv_acc_number_int'])) $inv_acc_number_int_user=intval($_POST['inv_acc_number_int']);



$sql="SELECT gks_acc_seires.id_acc_seira,gks_acc_seires.is_xeirografi,gks_acc_seires.seira_isdeliverynote
FROM (((gks_acc_seires 
LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE eidos_parastatikou_type_id in (1,2,5) AND id_acc_eidos_parastatikou not in (702,703,704)
AND gks_acc_seires.is_disable=0 AND gks_acc_journal.is_disable=0 
AND gks_company.company_disable=0 
AND gks_acc_seires.id_acc_seira=".$inv_acc_seira_id." 
AND gks_acc_journal.id_acc_journal=".$inv_acc_journal_id." 
AND gks_company.id_company=".$company_id;
//$save_but_message='<pre>'.$sql;

if (count($perm_id_company_ids)>0) $sql.=" and gks_company.id_company in (".implode(',',$perm_id_company_ids).")";
if ($company_sub_id>0) {
  $sql.=" AND gks_company_subs.company_sub_disable=0 AND gks_company_subs.id_company_sub=".$company_sub_id;
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_company_subs.id_company_sub in (".implode(',',$perm_id_company_sub_ids).")";
} else {
  $sql.=" AND gks_acc_journal.company_sub_id=0";
  if (count($perm_id_company_sub_ids)>0 and in_array(0,$perm_id_company_sub_ids)==false) $sql.=" and 1=2";
}
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_journal.id_acc_journal in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_seires.id_acc_seira in (".implode(',',$perm_id_acc_seira_ids).")";


$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows!=1) {
  debug_mail(false,'company+journal+series not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο συνδυασμός εταιρείας/υποκαταστήματος, ημερολογίου και σειράς')));
  echo json_encode($return); die();}
$row_seira = $result->fetch_assoc();
$is_xeirografi=intval($row_seira['is_xeirografi']);
$seira_isdeliverynote=intval($row_seira['seira_isdeliverynote']);


if ($inv_state=='010draft' and $inv_state_old!='010draft' and $is_xeirografi_old==0 and $inv_acc_number_int_old>0) {
  //echo '<pre>vvv';die();
 gks_inv_to_draft($id);
}



if ($inv_state=='080listing' and $is_xeirografi==0) {
  debug_mail(false,'inv_state 080listing is_xeirografi',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η σειρά είναι χειρόγραφη, άρα το παραστατικό θα πρέπει να εκδοθεί και όχι να καταχωρηθεί')));
  echo json_encode($return); die();}    
  
if ($inv_state=='090ekdosi' and $is_xeirografi!=0) {
  debug_mail(false,'inv_state 090ekdosi is_xeirografi',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η σειρά είναι μηχανογραφημένη, άρα το παραστατικό θα πρέπει να καταχωρηθεί και όχι να εκδοθεί')));
  echo json_encode($return); die();}
  
//if ($inv_state=='080listing' and $inv_acc_number_int_old>0) {
//  debug_mail(false,'inv_state 080listing inv_acc_number_int_old','');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το παραστατικό έχει ήδη καταχωρηθεί').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
//  echo json_encode($return); die();}
//  
//if ($inv_state=='090ekdosi' and $inv_acc_number_int_old>0) {
//  debug_mail(false,'inv_state 080listing inv_acc_number_int_old','');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το παραστατικό έχει ήδη εκδοθεί').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
//  echo json_encode($return); die();}
  



$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);

if ($user_id<=0) {
  debug_mail(false,'user_id zero','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή')));
  echo json_encode($return); die();}



$dr_user_first_name=''; if (isset($_POST['dr_user_first_name'])) $dr_user_first_name=trim_gks(base64_decode($_POST['dr_user_first_name']));
if ($gks_user_lock) $dr_user_first_name=$row_old['user_first_name'];

$dr_user_last_name=''; if (isset($_POST['dr_user_last_name'])) $dr_user_last_name=trim_gks(base64_decode($_POST['dr_user_last_name']));
if ($gks_user_lock) $dr_user_last_name=$row_old['user_last_name'];

$dr_user_email=''; if (isset($_POST['dr_user_email'])) $dr_user_email=trim_gks(base64_decode($_POST['dr_user_email']));
if ($gks_user_lock) $dr_user_email=$row_old['user_email'];

$dr_user_mobile=''; if (isset($_POST['dr_user_mobile'])) $dr_user_mobile=trim_gks(base64_decode($_POST['dr_user_mobile']));
if ($gks_user_lock) $dr_user_mobile=$row_old['user_mobile'];

$dr_user_lang=''; if (isset($_POST['dr_user_lang'])) $dr_user_lang=trim_gks(base64_decode($_POST['dr_user_lang']));
if ($gks_user_lock) $dr_user_lang=$row_old['user_lang'];

$dr_user_ma_odos=''; if (isset($_POST['dr_user_ma_odos'])) $dr_user_ma_odos=trim_gks(base64_decode($_POST['dr_user_ma_odos']));
if ($gks_user_lock) $dr_user_ma_odos=$row_old['ma_odos'];

$dr_user_ma_arithmos=''; if (isset($_POST['dr_user_ma_arithmos'])) $dr_user_ma_arithmos=trim_gks(base64_decode($_POST['dr_user_ma_arithmos']));
if ($gks_user_lock) $dr_user_ma_arithmos=$row_old['ma_arithmos'];

$dr_user_ma_orofos=''; if (isset($_POST['dr_user_ma_orofos'])) $dr_user_ma_orofos=trim_gks(base64_decode($_POST['dr_user_ma_orofos']));
if ($gks_user_lock) $dr_user_ma_orofos=$row_old['ma_orofos'];

$dr_user_ma_perioxi=''; if (isset($_POST['dr_user_ma_perioxi'])) $dr_user_ma_perioxi=trim_gks(base64_decode($_POST['dr_user_ma_perioxi']));
if ($gks_user_lock) $dr_user_ma_perioxi=$row_old['ma_perioxi'];

$dr_user_ma_poli=''; if (isset($_POST['dr_user_ma_poli'])) $dr_user_ma_poli=trim_gks(base64_decode($_POST['dr_user_ma_poli']));
if ($gks_user_lock) $dr_user_ma_poli=$row_old['ma_poli'];

$dr_user_ma_tk=''; if (isset($_POST['dr_user_ma_tk'])) $dr_user_ma_tk=trim_gks(base64_decode($_POST['dr_user_ma_tk']));
if ($gks_user_lock) $dr_user_ma_tk=$row_old['ma_tk'];

$dr_user_ma_country_id=0; if (isset($_POST['dr_user_ma_country_id'])) $dr_user_ma_country_id=intval($_POST['dr_user_ma_country_id']);
if ($gks_user_lock) $dr_user_ma_country_id=$row_old['ma_country_id'];

$dr_user_ma_nomos_id=0; if (isset($_POST['dr_user_ma_nomos_id'])) $dr_user_ma_nomos_id=intval($_POST['dr_user_ma_nomos_id']);
if ($gks_user_lock) $dr_user_ma_nomos_id=$row_old['ma_nomos_id'];



$dr_user_eponimia=''; if (isset($_POST['dr_user_eponimia'])) $dr_user_eponimia=trim_gks(base64_decode($_POST['dr_user_eponimia']));
if ($gks_user_lock) $dr_user_eponimia=$row_old['eponimia'];

$dr_user_title=''; if (isset($_POST['dr_user_title'])) $dr_user_title=trim_gks(base64_decode($_POST['dr_user_title']));
if ($gks_user_lock) $dr_user_title=$row_old['title'];

$dr_user_afm=''; if (isset($_POST['dr_user_afm'])) $dr_user_afm=trim_gks(base64_decode($_POST['dr_user_afm']));
if ($gks_user_lock) $dr_user_afm=$row_old['afm'];

$dr_user_doy=''; if (isset($_POST['dr_user_doy'])) $dr_user_doy=trim_gks(base64_decode($_POST['dr_user_doy']));
if ($gks_user_lock) $dr_user_doy=$row_old['doy'];

$dr_user_epaggelma=''; if (isset($_POST['dr_user_epaggelma'])) $dr_user_epaggelma=trim_gks(base64_decode($_POST['dr_user_epaggelma']));
if ($gks_user_lock) $dr_user_epaggelma=$row_old['epaggelma'];



$form_select_apostoli=-1; if (isset($_POST['form_select_apostoli'])) $form_select_apostoli=intval($_POST['form_select_apostoli']);
if ($gks_user_lock) $form_select_apostoli=$row_old['address_extra'];

$form_ea_name=''; if (isset($_POST['form_ea_name'])) $form_ea_name=trim_gks(base64_decode($_POST['form_ea_name']));
$form_ea_phone=''; if (isset($_POST['form_ea_phone'])) $form_ea_phone=trim_gks(base64_decode($_POST['form_ea_phone']));
$form_ea_odos=''; if (isset($_POST['form_ea_odos'])) $form_ea_odos=trim_gks(base64_decode($_POST['form_ea_odos']));
$form_ea_arithmos=''; if (isset($_POST['form_ea_arithmos'])) $form_ea_arithmos=trim_gks(base64_decode($_POST['form_ea_arithmos']));
$form_ea_orofos=''; if (isset($_POST['form_ea_orofos'])) $form_ea_orofos=trim_gks(base64_decode($_POST['form_ea_orofos']));
$form_ea_perioxi=''; if (isset($_POST['form_ea_perioxi'])) $form_ea_perioxi=trim_gks(base64_decode($_POST['form_ea_perioxi']));
$form_ea_poli=''; if (isset($_POST['form_ea_poli'])) $form_ea_poli=trim_gks(base64_decode($_POST['form_ea_poli']));
$form_ea_tk=''; if (isset($_POST['form_ea_tk'])) $form_ea_tk=trim_gks(base64_decode($_POST['form_ea_tk']));
$form_ea_country_id=0; if (isset($_POST['form_ea_country_id'])) $form_ea_country_id=intval($_POST['form_ea_country_id']);
$form_ea_nomos_id=0; if (isset($_POST['form_ea_nomos_id'])) $form_ea_nomos_id=intval($_POST['form_ea_nomos_id']);



$destination_data_name='';
$destination_data_phone='';
$destination_data_odos='';
$destination_data_arithmos='';
$destination_data_orofos='';
$destination_data_perioxi='';
$destination_data_poli='';
$destination_data_tk='';
$destination_data_country_id=0;
$destination_data_nomos_id=0;

$destination_data_name=$form_ea_name;
$destination_data_phone=$form_ea_phone;
$destination_data_odos=$form_ea_odos;
$destination_data_arithmos=$form_ea_arithmos;
$destination_data_orofos=$form_ea_orofos;
$destination_data_perioxi=$form_ea_perioxi;
$destination_data_poli=$form_ea_poli;
$destination_data_tk=$form_ea_tk;
$destination_data_country_id=$form_ea_country_id;
$destination_data_nomos_id=$form_ea_nomos_id; 

if ($_POST['inv_date'] == '__/__/____ __:__') $_POST['inv_date']='';
$inv_date=trim_gks(stripslashes(urldecode($_POST['inv_date'])));
if ($inv_date!='') {
  $inv_date = mystrtodb($inv_date);
}

$note_doc=''; if (isset($_POST['note_doc'])) $note_doc=trim_gks(base64_decode($_POST['note_doc']));
$note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));

$tropos_apostolis=0; if (isset($_POST['tropos_apostolis'])) $tropos_apostolis=intval($_POST['tropos_apostolis']);  
$tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);  
$tropos_pliromis_one_multi=0; if (isset($_POST['tropos_pliromis_one_multi'])) $tropos_pliromis_one_multi=intval($_POST['tropos_pliromis_one_multi']);  

$delivery_id_8=0; if (isset($_POST['delivery_id_8'])) $delivery_id_8=intval($_POST['delivery_id_8']);  
$delivery_number=''; if (isset($_POST['delivery_number'])) $delivery_number=trim_gks(base64_decode($_POST['delivery_number']));
$vehicle_number=''; if (isset($_POST['vehicle_number'])) $vehicle_number=trim_gks(base64_decode($_POST['vehicle_number']));

if ($_POST['dispatch_date'] == '__/__/____') $_POST['dispatch_date']='';
$dispatch_date=trim_gks(stripslashes(urldecode($_POST['dispatch_date'])));
if ($dispatch_date!='') $dispatch_date = gks_myFormatDate($dispatch_date);
if ($dispatch_date!=='') $dispatch_date=date('Y-m-d',$dispatch_date);

if ($_POST['dispatch_time'] == '__:__') $_POST['dispatch_time']='';
$dispatch_time=trim_gks(stripslashes(urldecode($_POST['dispatch_time'])));
if ($dispatch_time!='') $dispatch_time = gks_myFormatTime($dispatch_time);
if ($dispatch_time!=='') $dispatch_time=date('H:i:s',$dispatch_time);
  
$kostos_apostolis=0; if (isset($_POST['kostos_apostolis'])) $kostos_apostolis=floatval($_POST['kostos_apostolis']);
$kostos_pliromis=0;  if (isset($_POST['kostos_pliromis']))  $kostos_pliromis=floatval($_POST['kostos_pliromis']);

$delivery_method_type='';
$sql_dmt="select delivery_method_type from gks_delivery_methods where id_delivery_method=".$tropos_apostolis;
$result_dmt = $db_link->query($sql_dmt);  
if (!$result_dmt) {
  debug_mail(false,'error sql',$sql_dmt);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result_dmt->num_rows==1) {
  $row_dmt = $result_dmt->fetch_assoc();  
  $delivery_method_type=trim_gks($row_dmt['delivery_method_type']);
}
if (!($delivery_method_type=='delivery' or $delivery_method_type=='pelatis' or $delivery_method_type=='post')) {
  $delivery_number='';
  $vehicle_number='';
  $dispatch_date='';
  $dispatch_time='';
}
if ($tropos_apostolis!=8) $delivery_id_8=0;

$acc_inv_payment_array=array();
gks_acc_inv_acc_inv_payment_part_a();


$b2g_inv_aaht_name=''; if (isset($_POST['b2g_inv_aaht_name'])) $b2g_inv_aaht_name=trim_gks(base64_decode($_POST['b2g_inv_aaht_name']));
//if ($gks_user_lock) $b2g_inv_aaht_name=$row_old['b2g_inv_aaht_name'];

$project_reference=''; if (isset($_POST['project_reference'])) $project_reference=trim_gks(base64_decode($_POST['project_reference']));
//if ($gks_user_lock) $project_reference=$row_old['project_reference'];

$contract_reference=''; if (isset($_POST['contract_reference'])) $contract_reference=trim_gks(base64_decode($_POST['contract_reference']));
//if ($gks_user_lock) $contract_reference=$row_old['contract_reference'];

$b2g_inv_buyer_name=''; if (isset($_POST['b2g_inv_buyer_name'])) $b2g_inv_buyer_name=trim_gks(base64_decode($_POST['b2g_inv_buyer_name']));
//if ($gks_user_lock) $b2g_inv_buyer_name=$row_old['b2g_inv_buyer_name'];

$b2g_inv_aaht_code=''; if (isset($_POST['b2g_inv_aaht_code'])) $b2g_inv_aaht_code=trim_gks(base64_decode($_POST['b2g_inv_aaht_code']));
//if ($gks_user_lock) $b2g_inv_aaht_code=$row_old['b2g_inv_aaht_code'];




$aade_skopos_diakinisis_id=0; if (isset($_POST['aade_skopos_diakinisis_id'])) $aade_skopos_diakinisis_id=intval($_POST['aade_skopos_diakinisis_id']);  
$aade_skopos_19_descr=''; if (isset($_POST['aade_skopos_19_descr'])) $aade_skopos_19_descr=trim_gks(base64_decode($_POST['aade_skopos_19_descr']));
//echo '<pre>ssssssssssss '.$aade_skopos_19_descr;die();
if ($aade_skopos_diakinisis_id!=22) $aade_skopos_19_descr='';

$fiscal_position_id=0; if (isset($_POST['fiscal_position_id'])) $fiscal_position_id=intval($_POST['fiscal_position_id']);  
if ($gks_user_lock) $fiscal_position_id=$row_old['fiscal_position_id'];

$pricelist_id=0; if (isset($_POST['pricelist_id'])) $pricelist_id=intval($_POST['pricelist_id']);  
if ($gks_user_lock) $pricelist_id=$row_old['pricelist_id'];

$def_ekptosi=0;  if (isset($_POST['def_ekptosi']))  $def_ekptosi=floatval($_POST['def_ekptosi']);
 
$affect_balance=0; if (isset($_POST['affect_balance'])) $affect_balance=intval($_POST['affect_balance']);  
if ($affect_balance!=1) $affect_balance=0;
$affect_balance_all_poso=0; if (isset($_POST['affect_balance_all_poso'])) $affect_balance_all_poso=intval($_POST['affect_balance_all_poso']);  
if ($affect_balance_all_poso!=1) $affect_balance_all_poso=0;
$affect_balance_all_poso_type=''; if (isset($_POST['affect_balance_all_poso_type'])) $affect_balance_all_poso_type=trim_gks($_POST['affect_balance_all_poso_type']);
if ($affect_balance_all_poso_type=='') $affect_balance_all_poso_type='total_price_net'; 
$affect_balance_poso=0;  if (isset($_POST['affect_balance_poso']))  $affect_balance_poso=floatval($_POST['affect_balance_poso']);

   
$other_entity_array_str = trim_gks(base64_decode($_POST['other_entity_array_str']));
$other_entity_array = json_decode($other_entity_array_str, true);
if ($other_entity_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error other_entity_array',$_POST['other_entity_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
  

$correlated_invoices_array_str = trim_gks(base64_decode($_POST['correlated_invoices_array_str']));
$correlated_invoices_array = json_decode($correlated_invoices_array_str, true);
if ($correlated_invoices_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error correlated_invoices_array',$_POST['correlated_invoices_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
//echo '<pre>';print_r($correlated_invoices_array);die();

$multiple_connected_marks_array_str = trim_gks(base64_decode($_POST['multiple_connected_marks_array_str']));
$multiple_connected_marks_array = json_decode($multiple_connected_marks_array_str, true);
if ($multiple_connected_marks_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error multiple_connected_marks_array',$_POST['multiple_connected_marks_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
//echo '<pre>';print_r($multiple_connected_marks_array);die();

$packings_declarations_array_str = trim_gks(base64_decode($_POST['packings_declarations_array_str']));
$packings_declarations_array = json_decode($packings_declarations_array_str, true);
if ($packings_declarations_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error packings_declarations_array',$_POST['packings_declarations_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
//echo '<pre>';print_r($packings_declarations_array);die();

$eidi_array_str = trim_gks(base64_decode($_POST['eidi_array_str']));
//$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 
$eidi_array = json_decode($eidi_array_str, true);
if ($eidi_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['eidi_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

if (count($eidi_array)==0 and ($inv_state=='070ypoekdosi' or $inv_state=='080listing' or $inv_state=='090ekdosi')) {
  debug_mail(false,'eidi_array 0',print_r($eidi_array,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχετε προσθέσει είδη')));
  echo json_encode($return); die();}

$eidos_parastatikou_type_id=0;
$eidos_parastatikou_need_afm=0;
$eidos_parastatikou_has_fpa=1;
$affect_balance_pros=0;
$whi_eidos_parastatikou_stock_pros=0;
$whi_eidos_parastatikou_stock_pros_org=0;
$whi_eidos_parastatikou_type_id=0;
$whi_eidos_parastatikou_type_id_org=0;
$acc_eidos_parastatikou_other_entity=0;
$journal_has_correlated_invoices=0;
$journal_has_multiple_connected_marks=0;
$journal_has_packings_declarations=0;

//die('<pre>|'.$gks_lock.'|'.$gks_number_lock.'|'.$gks_user_lock.'|');
if ($inv_acc_journal_id>0) {
  $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,
  gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
  gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
  gks_acc_journal.acc_eidos_parastatikou_other_entity,
  gks_acc_journal.journal_has_correlated_invoices,
  gks_acc_journal.journal_has_multiple_connected_marks,
  gks_acc_journal.journal_has_packings_declarations
  FROM (gks_acc_journal 
  LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
  WHERE gks_acc_journal.id_acc_journal=".$inv_acc_journal_id." and gks_acc_eidi_parastatikon.eidos_parastatikou_type_id>0";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $eidos_parastatikou_type_id=$row['eidos_parastatikou_type_id'];
    $eidos_parastatikou_need_afm=$row['eidos_parastatikou_need_afm'];
    $eidos_parastatikou_has_fpa=$row['eidos_parastatikou_has_fpa'];
    $eidos_parastatikou_aade_code=$row['eidos_parastatikou_aade_code'];
    $affect_balance_pros=$row['eidos_parastatikou_balance_pros'];
    $whi_eidos_parastatikou_stock_pros=$row['whi_eidos_parastatikou_stock_pros'];
    $whi_eidos_parastatikou_stock_pros_org=$whi_eidos_parastatikou_stock_pros;
    $whi_eidos_parastatikou_type_id=$row['whi_eidos_parastatikou_type_id'];
    $whi_eidos_parastatikou_type_id_org=$whi_eidos_parastatikou_type_id;
    $acc_eidos_parastatikou_other_entity=intval($row['acc_eidos_parastatikou_other_entity']);
    $journal_has_correlated_invoices=intval($row['journal_has_correlated_invoices']);
    $journal_has_multiple_connected_marks=intval($row['journal_has_multiple_connected_marks']);
    $journal_has_packings_declarations=intval($row['journal_has_packings_declarations']);
    
    if ($eidos_parastatikou_aade_code=='5.1' and $credit_memo_for_acc_inv_id<=0) {
      $message=gks_lang('Παραστατικά με ημερολόγιο το οποίο έχει ως τύπο παραστατικού το <b>Πιστωτικό Τιμολόγιο / Συσχετιζόμενο</b> δεν μπορούν να δημιουργηθούν άμεσα').'<br>'.
      gks_lang('Θα πρέπει να δημιουργηθούν μέσα από το συσχετιζόμενο παραστατικό');
      
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' => base64_encode($message));
      echo json_encode($return); die(); 
    }
  }
  //echo '<pre>';print_r($row);die();
  
}

if ($credit_memo_for_acc_inv_id>0) {
  $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,
  gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id

  FROM ((gks_acc_inv LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
  WHERE gks_acc_inv.id_acc_inv=".$credit_memo_for_acc_inv_id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $eidos_parastatikou_type_id=$row['eidos_parastatikou_type_id'];
    $eidos_parastatikou_need_afm=$row['eidos_parastatikou_need_afm'];
    $eidos_parastatikou_has_fpa=$row['eidos_parastatikou_has_fpa'];
    $affect_balance_pros=-$row['eidos_parastatikou_balance_pros'];
    $whi_eidos_parastatikou_stock_pros=$row['whi_eidos_parastatikou_stock_pros'];
    $whi_eidos_parastatikou_type_id=$row['whi_eidos_parastatikou_type_id'];
  } else {
    debug_mail(false,'eidos_parastatikou_type_id empty for credit_memo_for_acc_inv_id',$id.' '.$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το συσχετιζόμενο παραστατικό')));
    echo json_encode($return); die();
    
  }
  
  
}


if ($eidos_parastatikou_type_id<=0) {
  debug_mail(false,'eidos_parastatikou_type_id empty',$inv_acc_journal_id.' '.$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο γενικός τύπου του παραστατικού')));
  echo json_encode($return); die();}

$warehouses_id_from=0; if (isset($_POST['warehouses_id_from'])) $warehouses_id_from=intval($_POST['warehouses_id_from']);
$load_branch=''; if (isset($_POST['load_branch']) and trim_gks($_POST['load_branch'])!='') $load_branch=intval($_POST['load_branch']);
$load_odos=''; if (isset($_POST['load_odos'])) $load_odos=trim_gks(base64_decode($_POST['load_odos']));
$load_arithmos=''; if (isset($_POST['load_arithmos'])) $load_arithmos=trim_gks(base64_decode($_POST['load_arithmos']));
$load_orofos=''; if (isset($_POST['load_orofos'])) $load_orofos=trim_gks(base64_decode($_POST['load_orofos']));
$load_perioxi=''; if (isset($_POST['load_perioxi'])) $load_perioxi=trim_gks(base64_decode($_POST['load_perioxi']));
$load_poli=''; if (isset($_POST['load_poli'])) $load_poli=trim_gks(base64_decode($_POST['load_poli']));
$load_tk=''; if (isset($_POST['load_tk'])) $load_tk=trim_gks(base64_decode($_POST['load_tk']));
$load_country_id=0; if (isset($_POST['load_country_id'])) $load_country_id=intval($_POST['load_country_id']);
$load_nomos_id=0; if (isset($_POST['load_nomos_id'])) $load_nomos_id=intval($_POST['load_nomos_id']);

$warehouses_id_to=0;   if (isset($_POST['warehouses_id_to']))   $warehouses_id_to=intval($_POST['warehouses_id_to']);
$deli_branch=''; if (isset($_POST['deli_branch']) and trim_gks($_POST['deli_branch'])!='') $deli_branch=intval($_POST['deli_branch']);
$deli_odos=''; if (isset($_POST['deli_odos'])) $deli_odos=trim_gks(base64_decode($_POST['deli_odos']));
$deli_arithmos=''; if (isset($_POST['deli_arithmos'])) $deli_arithmos=trim_gks(base64_decode($_POST['deli_arithmos']));
$deli_orofos=''; if (isset($_POST['deli_orofos'])) $deli_orofos=trim_gks(base64_decode($_POST['deli_orofos']));
$deli_perioxi=''; if (isset($_POST['deli_perioxi'])) $deli_perioxi=trim_gks(base64_decode($_POST['deli_perioxi']));
$deli_poli=''; if (isset($_POST['deli_poli'])) $deli_poli=trim_gks(base64_decode($_POST['deli_poli']));
$deli_tk=''; if (isset($_POST['deli_tk'])) $deli_tk=trim_gks(base64_decode($_POST['deli_tk']));
$deli_country_id=0; if (isset($_POST['deli_country_id'])) $deli_country_id=intval($_POST['deli_country_id']);
$deli_nomos_id=0; if (isset($_POST['deli_nomos_id'])) $deli_nomos_id=intval($_POST['deli_nomos_id']);

if ($seira_isdeliverynote==0) {
  $load_branch='';
  $load_odos='';
  $load_arithmos='';
  $load_orofos='';
  $load_perioxi='';
  $load_poli='';
  $load_tk='';
  $load_country_id=0;
  $load_nomos_id=0;
  
  $deli_branch='';
  $deli_odos='';
  $deli_arithmos='';
  $deli_orofos='';
  $deli_perioxi='';
  $deli_poli='';
  $deli_tk='';
  $deli_country_id=0;
  $deli_nomos_id=0;
}

if ($seira_isdeliverynote!=0 and $aade_skopos_diakinisis_id<=0) {
  debug_mail(false,'aade_skopos_diakinisis_id zero','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον σκοπό διακίνησης')));
  echo json_encode($return); die();}

if ($aade_skopos_diakinisis_id==22 and $aade_skopos_19_descr=='') {
  debug_mail(false,'aade_skopos_diakinisis_id 22 aade_skopos_19_descr empty','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Τίτλο της Λοιπής Αιτίας Διακίνησης')));
  echo json_encode($return); die();}

if ($seira_isdeliverynote!=0) {
//  if ($load_branch=='') {
//    $error_text=gks_lang('Ορίστε τον <b>Αριθμό Εγκατάστασης</b> στο <b>Από</b> της <b>Αποθήκης</b>');
//    debug_mail(false,$error_text,'');
//    $return = array('success' => false, 'message' => base64_encode($error_text));
//    echo json_encode($return); die();}
  if ($load_odos=='') {
    $error_text=gks_lang('Ορίστε την <b>Οδό</b> στο <b>Από</b> της <b>Αποθήκης</b>');
    debug_mail(false,$error_text,'');
    $return = array('success' => false, 'message' => base64_encode($error_text));
    echo json_encode($return); die();}
  if ($load_arithmos=='') {
    $error_text=gks_lang('Ορίστε τον <b>Αριθμό</b> στο <b>Από</b> της <b>Αποθήκης</b>');
    debug_mail(false,$error_text,'');
    $return = array('success' => false, 'message' => base64_encode($error_text));
    echo json_encode($return); die();}
  if ($load_poli=='') {
    $error_text=gks_lang('Ορίστε την <b>Πόλη</b> στο <b>Από</b> της <b>Αποθήκης</b>');
    debug_mail(false,$error_text,'');
    $return = array('success' => false, 'message' => base64_encode($error_text));
    echo json_encode($return); die();}
  if ($load_tk=='') {
    $error_text=gks_lang('Ορίστε τον <b>TK</b> στο <b>Από</b> της <b>Αποθήκης</b>');
    debug_mail(false,$error_text,'');
    $return = array('success' => false, 'message' => base64_encode($error_text));
    echo json_encode($return); die();}
    
    
//  if ($deli_branch=='') {
//    $error_text=gks_lang('Ορίστε τον <b>Αριθμό Εγκατάστασης</b> στο <b>Προς</b> της <b>Αποθήκης</b>');
//    debug_mail(false,$error_text,'');
//    $return = array('success' => false, 'message' => base64_encode($error_text));
//    echo json_encode($return); die();}
  if ($deli_odos=='') {
    $error_text=gks_lang('Ορίστε την <b>Οδό</b> στο <b>Προς</b> της <b>Αποθήκης</b>');
    debug_mail(false,$error_text,'');
    $return = array('success' => false, 'message' => base64_encode($error_text));
    echo json_encode($return); die();}
  if ($deli_arithmos=='') {
    $error_text=gks_lang('Ορίστε τον <b>Αριθμό</b> στο <b>Προς</b> της <b>Αποθήκης</b>');
    debug_mail(false,$error_text,'');
    $return = array('success' => false, 'message' => base64_encode($error_text));
    echo json_encode($return); die();}
  if ($deli_poli=='') {
    $error_text=gks_lang('Ορίστε την <b>Πόλη</b> στο <b>Προς</b> της <b>Αποθήκης</b>');
    debug_mail(false,$error_text,'');
    $return = array('success' => false, 'message' => base64_encode($error_text));
    echo json_encode($return); die();}
  if ($deli_tk=='') {
    $error_text=gks_lang('Ορίστε τον <b>TK</b> στο <b>Προς</b> της <b>Αποθήκης</b>');
    debug_mail(false,$error_text,'');
    $return = array('success' => false, 'message' => base64_encode($error_text));
    echo json_encode($return); die();}

} 


$warehouses_id_from_is_virtual=false;
$warehouses_id_to_is_virtual=false;
if ($whi_eidos_parastatikou_type_id_org==null) $whi_eidos_parastatikou_type_id_org=0;

if ($whi_eidos_parastatikou_type_id_org==0) {
  $warehouses_id_from=0;
  $warehouses_id_to=0;
  //echo 'hhh ';var_dump($whi_eidos_parastatikou_type_id_org);die();
} else {
  if ($whi_eidos_parastatikou_type_id_org==24) { //apografi
    $warehouses_id_from=0;  
//    $aade_skopos_diakinisis_id=0;
//    $pricelist_id=0;
//    $fiscal_position_id=0;
//    $tropos_apostolis=1; //den apiteite apostoli

    $load_branch='';
    $load_odos='';
    $load_arithmos='';
    $load_orofos='';
    $load_perioxi='';
    $load_poli='';
    $load_tk='';
    $load_country_id=0;
    $load_nomos_id=0;
    
    $deli_branch='';
    $deli_odos='';
    $deli_arithmos='';
    $deli_orofos='';
    $deli_perioxi='';
    $deli_poli='';
    $deli_tk='';
    $deli_country_id=0;
    $deli_nomos_id=0;
        
  } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
    
  } else {
    if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
        $warehouses_id_from=1; //virtual warehouse pelates
        $warehouses_id_from_is_virtual=true;
      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutes
        $warehouses_id_from=2; //virtual warehouse promitheutes
        $warehouses_id_from_is_virtual=true;
      }
    } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
        $warehouses_id_to=1; //virtual warehouse pelates
        $warehouses_id_to_is_virtual=true;
      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutes
        $warehouses_id_to=2; //virtual warehouse promitheutes
        $warehouses_id_to_is_virtual=true;
      }
    }
  }
  
  //echo '<pre>'.$whi_eidos_parastatikou_type_id_org;die();
  
  
  if ($warehouses_id_from>0 and $warehouses_id_from_is_virtual==false) { //ektos virtual
    $sql="select * from gks_warehouses where is_virtual=0 and id_warehouse=".$warehouses_id_from;
    if ($whi_eidos_parastatikou_type_id!=23) { //not endodiakinisi
      if ($company_id>0) $sql.=" and company_id=".$company_id;
      if ($company_sub_id==0) $sql.=" and company_sub_id=0";
      else if ($company_sub_id>0) $sql.=" and company_sub_id=".$company_sub_id;
    }
    
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      debug_mail(false,'warehouses_id_from not found',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η αποθήκη <b>Από</b>')));
      echo json_encode($return); die();}  
  }
  if ($warehouses_id_to>0 and $warehouses_id_to_is_virtual==false) { //ektos virtual
    $sql="select * from gks_warehouses where is_virtual=0 and id_warehouse=".$warehouses_id_to;
    if ($whi_eidos_parastatikou_type_id!=23) { //not endodiakinisi
      if ($company_id>0) $sql.=" and company_id=".$company_id;
      if ($company_sub_id==0) $sql.=" and company_sub_id=0";
      else if ($company_sub_id>0) $sql.=" and company_sub_id=".$company_sub_id;
    }
    
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die(); }
    if ($result->num_rows==0) {
      debug_mail(false,'warehouses_id_from not found',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η αποθήκη <b>Προς</b>')));
      echo json_encode($return); die();}  
  }


  if ($whi_eidos_parastatikou_type_id==23 or $whi_eidos_parastatikou_type_id==24) { //endodiakinisi, apografi
//    $user_id=0;
//    $dr_user_first_name=''; 
//    $dr_user_last_name='';
//    $dr_user_email='';
//    $dr_user_mobile='';
//    $dr_user_lang='';
//    $dr_user_ma_odos='';
//    $dr_user_ma_perioxi='';
//    $dr_user_ma_poli='';
//    $dr_user_ma_tk='';
//    $dr_user_ma_country_id=0;
//    $dr_user_ma_nomos_id=0;
//    $dr_user_eponimia='';
//    $dr_user_title='';
//    $dr_user_afm='';
//    $dr_user_doy='';
//    $dr_user_epaggelma='';
//    
//    $form_select_apostoli=-1;
//    $form_ea_name='';
//    $form_ea_phone='';
//    $form_ea_odos='';
//    $form_ea_perioxi='';
//    $form_ea_poli='';
//    $form_ea_tk='';
//    $form_ea_country_id=0; 
//    $form_ea_nomos_id=0;
//    
//    $destination_data_name='';
//    $destination_data_phone='';
//    $destination_data_odos='';
//    $destination_data_perioxi='';
//    $destination_data_poli='';
//    $destination_data_tk='';
//    $destination_data_country_id=0;
//    $destination_data_nomos_id=0;  
  }
  
  if ($whi_eidos_parastatikou_type_id==21 or $whi_eidos_parastatikou_type_id==22) { //deltio apostolis/paralavis
    if ($user_id<=0) {
      debug_mail(false,'user_id zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή (πελάτη/προμηθευτή)')));
      echo json_encode($return); die();}
    
    if ($warehouses_id_from<=0) {
      debug_mail(false,'warehouses_id_from zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
      echo json_encode($return); die();}
    
    if ($warehouses_id_to<=0) {
      debug_mail(false,'warehouses_id_to zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
      echo json_encode($return); die();}
  }
    
  if ($whi_eidos_parastatikou_type_id==23) { //endodiakinisi
    if ($warehouses_id_from<=0) {
      debug_mail(false,'warehouses_id_from zero endodiakinisi','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
      echo json_encode($return); die();}
  
    if ($warehouses_id_to<=0) {
      debug_mail(false,'warehouses_id_to zero endodiakinisi','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
      echo json_encode($return); die();}
  }
    
  
  if ($whi_eidos_parastatikou_type_id==24) { //apografi
    if ($warehouses_id_to<=0) {
      debug_mail(false,'warehouses_id_to zero apografi','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη που <b>Αφορά</b> η απογραφή')));
      echo json_encode($return); die();}
  }
  
  
  
  if ($warehouses_id_from==$warehouses_id_to) {
    debug_mail(false,'warehouses_id_from=warehouses_id_to','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η αποθήκη <b>Από</b> πρέπει να είναι διαφορετική από την αποθήκη <b>Προς</b>')));
    echo json_encode($return); die();}    




//
////////////    
//    
//    if ($whi_eidos_parastatikou_type_id==23 and $warehouses_id_from<=0) {
//      debug_mail(false,'whi_eidos_parastatikou_type_id 23 warehouses_id_from 0','');
//      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
//      echo json_encode($return); die();}
//  
//    if ($whi_eidos_parastatikou_type_id==24) {
//      if ($warehouses_id_to<=0) {
//        debug_mail(false,'whi_eidos_parastatikou_type_id 24 warehouses_id_to zero','');
//        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη που <b>Αφορά</b> η απογραφή')));
//        echo json_encode($return); die();}
//    } else {
//      if ($warehouses_id_to<=0) {
//        debug_mail(false,'whi_eidos_parastatikou_type_id 24 warehouses_id_to zero','');
//        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
//        echo json_encode($return); die();}
//    }
//    
//    if ($warehouses_id_from==$warehouses_id_to) {
//      debug_mail(false,'warehouses_id_from = warehouses_id_to','');
//      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η αποθήκη <b>Από</b> πρέπει να είναι διαφορετική από την αποθήκη <b>Προς</b>')));
//      echo json_encode($return); die();}    
//       
//  } else {
////    if ($user_id<=0) {
////      debug_mail(false,'user_id zero','');
////      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή (πελάτη/προμηθευτή)')));
////      echo json_encode($return); die();}
//  }

}





if ($eidos_parastatikou_need_afm==0) {
  $dr_user_eponimia=''; 
  $dr_user_title='';
  $dr_user_afm='';
  $dr_user_doy='';
  $dr_user_epaggelma='';
  
  $b2g_inv_aaht_name='';
  $project_reference='';
  $contract_reference='';
  $b2g_inv_buyer_name='';
  $b2g_inv_aaht_code='';
}  
//print '<pre>';print $eidos_parastatikou_type_id;die();


if ($gks_user_lock==false) {
  if ($form_select_apostoli==-1) { //apostoli stin idia address
    //tipota...  
  } else if ($form_select_apostoli == 0) { // new address
    $sql="insert into gks_users_extra_address (
    mydate_add,user_id_add,myip,
    user_id,ea_name,ea_phone,ea_odos,ea_arithmos,ea_orofos,ea_perioxi,ea_poli,ea_tk,ea_country_id,ea_nomos_id
    ) values (
    now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$user_id.",
    '".$db_link->escape_string($form_ea_name)."',
    '".$db_link->escape_string($form_ea_phone)."',
    '".$db_link->escape_string($form_ea_odos)."',
    '".$db_link->escape_string($form_ea_arithmos)."',
    '".$db_link->escape_string($form_ea_orofos)."',
    '".$db_link->escape_string($form_ea_perioxi)."',
    '".$db_link->escape_string($form_ea_poli)."',
    '".$db_link->escape_string($form_ea_tk)."',
    ".$form_ea_country_id.",
    ".$form_ea_nomos_id.")";
    $run = $db_link->query($sql);
    if (!$run) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die(); }
    
    $form_select_apostoli = $db_link->insert_id;  
    
  } else if ($form_select_apostoli>0) { //update iparxousa address
    
    $sql="update gks_users_extra_address set 
    ea_name='".$db_link->escape_string($form_ea_name)."',
    ea_phone='".$db_link->escape_string($form_ea_phone)."',
    ea_odos='".$db_link->escape_string($form_ea_odos)."',
    ea_arithmos='".$db_link->escape_string($form_ea_arithmos)."',
    ea_orofos='".$db_link->escape_string($form_ea_orofos)."',
    ea_perioxi='".$db_link->escape_string($form_ea_perioxi)."',
    ea_poli='".$db_link->escape_string($form_ea_poli)."',
    ea_tk='".$db_link->escape_string($form_ea_tk)."',
    ea_country_id=".$db_link->escape_string($form_ea_country_id).",
    ea_nomos_id=".$db_link->escape_string($form_ea_nomos_id).",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where user_id=".$user_id."
    and id_users_extra_address=".$form_select_apostoli;
    $run = $db_link->query($sql);
    if (!$run) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die(); } 
      
     
  } 
}


$not_del_id_acc_inv_other_entity=array();
if ($acc_eidos_parastatikou_other_entity==0) $other_entity_array=array();
$other_entity_cc=0;
foreach ($other_entity_array as &$other_entity_item) {
  $other_entity_cc++;
  if ($other_entity_item['aade_entitytype_id']<=0) {
    debug_mail(false,'aade_entitytype_id zero on line'. $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]',gks_n_h($other_entity_cc),gks_lang('Επιλέξτε τον τύπο στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['entity_user_id']<=0) {
    debug_mail(false,'entity_user_id zero on line'. $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]',gks_n_h($other_entity_cc),gks_lang('Επιλέξτε τον συσχετιζόμενο στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['address_extra']==0 or $other_entity_item['address_extra']<-1) {
    debug_mail(false,'address_extra zero on line'. $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]',gks_n_h($other_entity_cc),gks_lang('Επιλέξτε το υποκατάστημα στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή'))));
    echo json_encode($return); die(); }    
  
  $other_entity_item['ret']=gks_other_entity_get_data('gks_acc_inv',-1,$other_entity_item['entity_user_id'],$other_entity_item['address_extra']);
  
  if ($other_entity_item['ret']['data']['afm']=='') {
    debug_mail(false,'afm zero on line'. $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]',gks_n_h($other_entity_cc),gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει ΑΦΜ'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['ret']['data']['country_initials']=='') {
    debug_mail(false,'country_initials zero on line'. $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]',gks_n_h($other_entity_cc),gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Χώρα'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['ret']['data']['country_initials']=='') {
    debug_mail(false,'country_initials zero on line'. $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]',gks_n_h($other_entity_cc),gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Χώρα'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['ret']['data']['branch']=='') {
    debug_mail(false,'branch zero on line'. $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]',gks_n_h($other_entity_cc),gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Αριθμό Εγκατάστασης'))));
    echo json_encode($return); die(); }    

  $recid=intval($other_entity_item['recid']);
  if ($recid>0) $not_del_id_acc_inv_other_entity[] = $recid;
}
unset($other_entity_item);
//print '<pre>';var_dump($other_entity_array);die();
//print '<pre>sssss sssssss '."\n";print_r($other_entity_array);die();



$not_del_id_acc_inv_correlated_invoices=array();
if ($journal_has_correlated_invoices==0) $correlated_invoices_array=array();
$correlated_invoices_cc=0;
foreach ($correlated_invoices_array as &$correlated_invoices_item) {
  $correlated_invoices_cc++;
  $recid=intval($correlated_invoices_item['recid']);
  if ($recid>0) $not_del_id_acc_inv_correlated_invoices[] = $recid;
}
unset($correlated_invoices_item);
//print '<pre>';var_dump($correlated_invoices_array);die();
//print '<pre>sssss sssssss '."\n";print_r($correlated_invoices_array);die();

$not_del_id_acc_inv_multiple_connected_marks=array();
if ($journal_has_multiple_connected_marks==0) $multiple_connected_marks_array=array();
$multiple_connected_marks_cc=0;
foreach ($multiple_connected_marks_array as &$multiple_connected_marks_item) {
  $multiple_connected_marks_cc++;
  $recid=intval($multiple_connected_marks_item['recid']);
  if ($recid>0) $not_del_id_acc_inv_multiple_connected_marks[] = $recid;
}
unset($multiple_connected_marks_item);
//print '<pre>';var_dump($multiple_connected_marks_array);die();
//print '<pre>sssss sssssss '."\n";print_r($multiple_connected_marks_array);die();

$not_del_id_acc_inv_packings_declarations=array();
if ($journal_has_packings_declarations==0) $packings_declarations_array=array();
$packings_declarations_cc=0;
foreach ($packings_declarations_array as &$packings_declarations_item) {
  $packings_declarations_cc++;
  $recid=intval($packings_declarations_item['recid']);
  if ($recid>0) $not_del_id_acc_inv_packings_declarations[] = $recid;
}
unset($packings_declarations_item);
//print '<pre>';var_dump($packings_declarations_array);die();
//print '<pre>sssss sssssss '."\n";print_r($packings_declarations_array);die();

$not_del_id_acc_inv_product=array();
$orders_products=array();
$gks_price_original_net=0;
$gks_price_net=0;
$gks_price_fpa=0;
$gks_price_netfpa=0;
$gks_price_total=0;

$totalWithheldAmount=0;
$totalOtherTaxesAmount=0;
$totalStampDutyamount=0;
$totalFeesAmount=0;
$totalDeductionsAmount=0;

$coupons_array=array();

foreach ($eidi_array as $eidi_array_item) {
  $id_acc_inv_product=intval($eidi_array_item['id_acc_inv_product']);
  $product_aa=intval($eidi_array_item['aa']);
  $product_id=intval($eidi_array_item['product_id']);
  $product_fpa_base_id=(isset($eidi_array_item['product_fpa_base_id']) ? intval($eidi_array_item['product_fpa_base_id']) : 0);
  $product_fpa_aade_id=(isset($eidi_array_item['product_fpa_aade_id']) ? intval($eidi_array_item['product_fpa_aade_id']) : 0);
  if ($product_fpa_base_id>0) $product_fpa_aade_id=0;
  
  $product_fpa_id=intval($eidi_array_item['product_fpa_id']);
  $product_fpa_ejeresi_id=intval($eidi_array_item['product_fpa_ejeresi_id']);
  if ($eidos_parastatikou_has_fpa==0 or ($eidos_parastatikou_has_fpa==1 and $product_fpa_base_id!=1004)) $product_fpa_ejeresi_id=0;
  $product_fpa_pososto=floatval($eidi_array_item['product_fpa_pososto']);
  $from_aade_import_user_fpa=intval($eidi_array_item['from_aade_import_user_fpa']);
  $from_aade_import_user_fpa_value=floatval($eidi_array_item['from_aade_import_user_fpa_value']);
  
  //echo '<pre>';print_r($eidi_array_item); die();
  
  $product_sheets=0; //intval($eidi_array_item['product_sheets']);
  $product_quantity=floatval($eidi_array_item['product_quantity']);
  $product_monada_id=intval($eidi_array_item['product_monada_id']);
  $product_price_check_fpa = $eidi_array_item['product_price_check_fpa'] ? 1 : 0;  
  $product_price_start_all_net = floatval($eidi_array_item['product_price_start_all_net']);  
  $product_price_ekptosi_pososto = floatval($eidi_array_item['product_price_ekptosi_pososto']);  
  $product_price_final_all_net = floatval($eidi_array_item['product_price_final_all_net']);  
  $product_price_final_all_fpa = floatval($eidi_array_item['product_price_final_all_fpa']);  
  $product_price_final_all_total=  $product_price_final_all_net + $product_price_final_all_fpa;
  $product_descr=trim_gks($eidi_array_item['product_descr']);
  $product_comments=trim_gks($eidi_array_item['product_comments']);
  $product_set=''; //trim_gks($eidi_array_item['product_set']);
  $product_price_coupon_use=trim_gks($eidi_array_item['product_price_coupon_use']);
  $product_price_coupon_use_disabled=intval($eidi_array_item['product_price_coupon_use_disabled']);
  
  $product_withheldPercentCategory = intval($eidi_array_item['product_withheldPercentCategory']);  
  $product_withheldAmount = floatval($eidi_array_item['product_withheldAmount']);  
  $product_otherTaxesPercentCategory = intval($eidi_array_item['product_otherTaxesPercentCategory']);  
  $product_otherTaxesAmount = floatval($eidi_array_item['product_otherTaxesAmount']);  
  $product_stampDutyPercentCategory = intval($eidi_array_item['product_stampDutyPercentCategory']);  
  $product_stampDutyAmount = floatval($eidi_array_item['product_stampDutyAmount']);  
  $product_feesPercentCategory = intval($eidi_array_item['product_feesPercentCategory']);  
  $product_feesAmount = floatval($eidi_array_item['product_feesAmount']);  
  $product_deductionsSelection= trim_gks($eidi_array_item['product_deductionsSelection']);  
  $product_deductionsAmount = floatval($eidi_array_item['product_deductionsAmount']);  
  
  $xarakt_esoda=array();
  foreach($eidi_array_item['xarakt_esoda'] as $xarakt_item) {
    $cat_id=intval($xarakt_item['cat_id']);
    $typos_id=intval($xarakt_item['typos_id']);
    $ammount=floatval($xarakt_item['ammount']);
    if (($cat_id!=0 or $typos_id!=0) and $ammount>=0) {
      $xarakt_esoda[]=array('cat_id'=>$cat_id, 'typos_id'=>$typos_id, 'ammount'=>$ammount);
    }
  }
  $xarakt_eksoda=array();
  foreach($eidi_array_item['xarakt_eksoda'] as $xarakt_item) {
    $cat_id=intval($xarakt_item['cat_id']);
    $typos_id=intval($xarakt_item['typos_id']);
    $ammount=floatval($xarakt_item['ammount']);
    if (($cat_id!=0 or $typos_id!=0) and $ammount>=0) {
      $xarakt_eksoda[]=array('cat_id'=>$cat_id, 'typos_id'=>$typos_id, 'ammount'=>$ammount);
    }
  }
  
  //print '<pre>';print_r($eidi_array_item);die();
  $product_lots_serials=array();
  if ($GKS_PRODUCT_LOTS_SERIALS) {
    foreach($eidi_array_item['product_lots_serials'] as $lot_product_item) {
      $lot_name=trim_gks($lot_product_item['lot_name']);
      $lot_product_quantity=floatval($lot_product_item['lot_product_quantity']);
      $lot_descr=trim_gks($lot_product_item['lot_descr']);
      $lot_date_production='';
      if (trim_gks($lot_product_item['lot_date_production'])!='' and trim_gks($lot_product_item['lot_date_production'])!='__/__/____') {
        $lot_date_production=trim_gks($lot_product_item['lot_date_production']);
        if ($lot_date_production=='__/__/____') $lot_date_production='';
        if ($lot_date_production!='') {
          $lot_date_production = mystrtodb($lot_date_production.' 00:00');
        }
      }
      $lot_date_expire='';
      if (trim_gks($lot_product_item['lot_date_expire'])!='' and trim_gks($lot_product_item['lot_date_expire'])!='__/__/____') {
        $lot_date_expire=trim_gks($lot_product_item['lot_date_expire']);
        if ($lot_date_expire=='__/__/____') $lot_date_expire='';
        if ($lot_date_expire!='') {
          $lot_date_expire = mystrtodb($lot_date_expire.' 00:00');
        }
      }

      $sql="select * from gks_eshop_product_lots where lot_name like '".$db_link->escape_string($lot_name)."' and lotproduct_id=".$product_id;
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $lot_product_id=$row['id_lot_product'];
        
//        $sql="update gks_eshop_product_lots set
//        lot_descr='".$db_link->escape_string($lot_product_item['lot_descr'])."',
//        lot_date_production=".($lot_product_item['lot_date_production']=='' ? 'null' : "'".$db_link->escape_string($lot_product_item['lot_date_production'])."'").",
//        lot_date_expire=".($lot_product_item['lot_date_expire']=='' ? 'null' : "'".$db_link->escape_string($lot_product_item['lot_date_expire'])."'").",
//        mydate_edit=now(),
//        user_id_edit=".$my_wp_user_id.",
//        myip='".$db_link->escape_string($gkIP)."'
//        where id_lot_product=".$lot_product_item['id_lot_product']."
//        and (
//          lot_descr<>'".$db_link->escape_string($lot_product_item['lot_descr'])."'
//          or ".($lot_product_item['lot_date_production']=='' ? 'lot_date_production is not null' : "(lot_date_production<>'".$db_link->escape_string($lot_product_item['lot_date_production'])."' or lot_date_production is null)")."
//          or ".($lot_product_item['lot_date_expire']=='' ? 'lot_date_expire is not null' : "(lot_date_expire<>'".$db_link->escape_string($lot_product_item['lot_date_expire'])."' or lot_date_expire is null)")."
//        )";
//        
//        $result = $db_link->query($sql);
//        if (!$result) {
//          debug_mail(false,'error sql',$sql);
//          $return = array('success' => false, 'message' => base64_encode('sql error'));
//          echo json_encode($return); die(); }
      } else {
        $sql="insert into gks_eshop_product_lots (
        lotproduct_id,
        lot_name,
        lot_descr,
        lot_date_production,
        lot_date_expire,
        lot_disabled,
        
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip
        ) values (
        ".$product_id.",
        '".$db_link->escape_string($lot_name)."',
        '".$db_link->escape_string($lot_descr)."',
        ".($lot_date_production=='' ? 'null' : "'".$db_link->escape_string($lot_date_production)."'").",
        ".($lot_date_expire=='' ? 'null' : "'".$db_link->escape_string($lot_date_expire)."'").",
        0,
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
        )";
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        $lot_product_id= $db_link->insert_id;           
        
      }
            
      if ($lot_name!='' and $lot_product_quantity!=0) {
        $product_lots_serials[]=array(
          'lot_product_id'=>$lot_product_id,
          'lot_name'=>$lot_name,
          'lot_product_quantity'=>$lot_product_quantity,
          'lot_descr'=>$lot_descr,
          'lot_date_production'=>$lot_date_production,
          'lot_date_expire'=>$lot_date_expire,
        );
      }
    }
  }
  
  
  
      
  //print '<pre>';print_r($product_lots_serials);die();
  
  
  //if ($product_quantity>0) {
    if ($id_acc_inv_product>0) $not_del_id_acc_inv_product[] = $id_acc_inv_product;
    
    $orders_products[]=array(
      'id_acc_inv_product' => $id_acc_inv_product,
      'product_aa' => $product_aa,
      'product_id' => $product_id,
      'product_fpa_base_id' => $product_fpa_base_id,
      'product_fpa_aade_id' => $product_fpa_aade_id,
      'product_fpa_id' => $product_fpa_id,
      'product_fpa_ejeresi_id' => $product_fpa_ejeresi_id,
      'product_fpa_pososto' => $product_fpa_pososto,
      'from_aade_import_user_fpa' => $from_aade_import_user_fpa,
      'from_aade_import_user_fpa_value' => $from_aade_import_user_fpa_value,
      'product_sheets' => $product_sheets,
      'product_quantity' => $product_quantity,
      'product_monada_id' => $product_monada_id,
      'product_price_check_fpa' => $product_price_check_fpa,
      'product_price_start_all_net' => $product_price_start_all_net,
      'product_price_ekptosi_pososto' => $product_price_ekptosi_pososto,
      'product_price_final_all_net' => $product_price_final_all_net,
      'product_price_final_all_fpa' => $product_price_final_all_fpa,
      'product_price_final_all_total' => $product_price_final_all_total,
      'product_descr' => $product_descr,
      'product_comments' => $product_comments,
      'product_set' => $product_set,
      'product_price_coupon_use' => $product_price_coupon_use,
      'product_price_coupon_use_disabled' => $product_price_coupon_use_disabled,
      
      
      'product_withheldPercentCategory' => $product_withheldPercentCategory,
      'product_withheldAmount' => $product_withheldAmount,
      'product_otherTaxesPercentCategory' => $product_otherTaxesPercentCategory,
      'product_otherTaxesAmount' => $product_otherTaxesAmount,
      'product_stampDutyPercentCategory' => $product_stampDutyPercentCategory,
      'product_stampDutyAmount' => $product_stampDutyAmount,
      'product_feesPercentCategory' => $product_feesPercentCategory,
      'product_feesAmount' => $product_feesAmount,
      'product_deductionsSelection' => $product_deductionsSelection,
      'product_deductionsAmount' => $product_deductionsAmount,
      
      'xarakt_esoda' => $xarakt_esoda,
      'xarakt_eksoda' => $xarakt_eksoda,
      'product_lots_serials' => $product_lots_serials,

    );  
    
    //echo '<pre>';print_r($orders_products); die();
    
    $gks_price_original_net+=$product_price_start_all_net;
    $gks_price_net+=$product_price_final_all_net;
    $gks_price_fpa+=$product_price_final_all_fpa;
    $gks_price_netfpa+=$product_price_final_all_net+$product_price_final_all_fpa;
    $gks_price_total+=$product_price_final_all_total;

    $totalWithheldAmount+=$product_withheldAmount;
    $totalOtherTaxesAmount+=$product_otherTaxesAmount;
    $totalStampDutyamount+=$product_stampDutyAmount;
    $totalFeesAmount+=$product_feesAmount;
    $totalDeductionsAmount+=$product_deductionsAmount;

    
    if ($product_price_coupon_use!='' and in_array($product_price_coupon_use,$coupons_array)==false) {
      $coupons_array[]= $product_price_coupon_use;
    }
  //}
  if ($product_id>0 and in_array($product_id,$all_products_for_balance)==false)
    $all_products_for_balance[]=$product_id;
}

$gks_price_total=
   $gks_price_net 
    + $gks_price_fpa
    - $totalWithheldAmount
    + $totalOtherTaxesAmount
    + $totalStampDutyamount
    + $totalFeesAmount
    - $totalDeductionsAmount;

$coupons_str='';
if (count($coupons_array)>=1) {
  $coupons_str='|' . implode('|',$coupons_array).'|';
}

$not_del_id_acc_inv_payment=array();
gks_acc_inv_acc_inv_payment_part_b();






$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_acc_inv');


$redirect='';

if ($id==-1) {
  $inv_guid=guid_for_acc_inv();
  $bank_deposit_9digit=gks_get_bank_deposit_9digit();
  
  $sql="insert into gks_acc_inv (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,inv_guid,bank_deposit_9digit
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  '".$db_link->escape_string($inv_guid)."','".$db_link->escape_string($bank_deposit_9digit)."')";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }  
  $id = $db_link->insert_id;
  $redirect=base64_encode('admin-acc-inv-item.php?id='.$id);  
  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
} 

$sql="delete from gks_acc_inv_other_entity where acc_inv_id=".$id;
if (count($not_del_id_acc_inv_other_entity)>0) {
  $sql.=" and id_acc_inv_other_entity not in (".implode(',', $not_del_id_acc_inv_other_entity).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

foreach ($other_entity_array as $myrec) {
  //print '<pre>ssssssssssssssss ';print_r($myrec);die();
  $d=$myrec['ret']['data'];
  if ($myrec['recid']<=0) {
    $sql="insert into gks_acc_inv_other_entity (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_inv_id,
      aade_entitytype_id,
      entity_user_id,
      entity_afm,
      entity_name,
      entity_sub_name,
      address_extra,
      entity_branch,
      entity_odos,
      entity_arithmos,
      entity_orofos,
      entity_perioxi,
      entity_poli,
      entity_tk,
      entity_country_id,
      entity_nomos_id
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      ".$myrec['aade_entitytype_id'].",
      ".$myrec['entity_user_id'].",
      '".$db_link->escape_string($d['afm'])."',
      '".$db_link->escape_string($d['name'])."',
      '".$db_link->escape_string($d['sub_name'])."',
      ".$myrec['address_extra'].",
      ".($d['branch']=='' ? 'null' : intval($d['branch'])).",
      '".$db_link->escape_string($d['odos'])."',
      '".$db_link->escape_string($d['arithmos'])."',
      '".$db_link->escape_string($d['orofos'])."',
      '".$db_link->escape_string($d['perioxi'])."',
      '".$db_link->escape_string($d['poli'])."',
      '".$db_link->escape_string($d['tk'])."',
      ".$d['country_id'].",
      ".$d['nomos_id']."
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }     
  } else {
    $sql="update gks_acc_inv_other_entity set 
    aade_entitytype_id=".$myrec['aade_entitytype_id'].",
    entity_user_id=".$myrec['entity_user_id'].",
    entity_afm='".$db_link->escape_string($d['afm'])."',
    entity_name='".$db_link->escape_string($d['name'])."',
    entity_sub_name='".$db_link->escape_string($d['sub_name'])."',
    address_extra=".$myrec['address_extra'].",
    entity_branch=".($d['branch']=='' ? 'null' : intval($d['branch'])).",
    entity_odos='".$db_link->escape_string($d['odos'])."',
    entity_arithmos='".$db_link->escape_string($d['arithmos'])."',
    entity_orofos='".$db_link->escape_string($d['orofos'])."',
    entity_perioxi='".$db_link->escape_string($d['perioxi'])."',
    entity_poli='".$db_link->escape_string($d['poli'])."',
    entity_tk='".$db_link->escape_string($d['tk'])."',
    entity_country_id=".$d['country_id'].",
    entity_nomos_id=".$d['nomos_id'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_acc_inv_other_entity=".$myrec['recid']."
    and acc_inv_id=".$id;  
    //echo '<pre>'.$sql; die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
}

$sql="delete from gks_acc_inv_correlated_invoices where acc_inv_id=".$id;
if (count($not_del_id_acc_inv_correlated_invoices )>0) {
  $sql.=" and id_acc_inv_correlated_invoices not in (".implode(',', $not_del_id_acc_inv_correlated_invoices).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

foreach ($correlated_invoices_array as $myrec) {
  //print '<pre>ssssssssssssssss ';print_r($myrec);die();
  $myrec['recid']=intval($myrec['recid']);
  $myrec['coi_acc_inv_id']=intval($myrec['coi_acc_inv_id']);
  $myrec['coi_acc_pay_id']=intval($myrec['coi_acc_pay_id']);
  $myrec['coi_whi_mov_id']=intval($myrec['coi_whi_mov_id']);
  $myrec['coiaa']=intval($myrec['coiaa']);
  $myrec['coi_mark']=trim($myrec['coi_mark']);
  if ($myrec['coi_mark']=='') {
    $myrec['coi_mark']=gks_aade_get_mark_from_id($myrec);
  }
  if ($myrec['recid']<=0) {
    $sql="insert into gks_acc_inv_correlated_invoices (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_inv_id,
      coi_mark,
      coi_acc_inv_id,
      coi_acc_pay_id,
      coi_whi_mov_id,
      coi_aa
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      '".$db_link->escape_string($myrec['coi_mark'])."',
      ".$myrec['coi_acc_inv_id'].",
      ".$myrec['coi_acc_pay_id'].",
      ".$myrec['coi_whi_mov_id'].",
      ".$myrec['coiaa']."
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }     
  } else {
    $sql="update gks_acc_inv_correlated_invoices set 
    coi_mark='".$db_link->escape_string($myrec['coi_mark'])."',
    coi_acc_inv_id=".$myrec['coi_acc_inv_id'].",
    coi_acc_pay_id=".$myrec['coi_acc_pay_id'].",
    coi_whi_mov_id=".$myrec['coi_whi_mov_id'].",
    coi_aa=".$myrec['coiaa'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_acc_inv_correlated_invoices=".$myrec['recid']."
    and acc_inv_id=".$id;  
    //echo '<pre>'.$sql; die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
  
}



$sql="delete from gks_acc_inv_multiple_connected_marks where acc_inv_id=".$id;
if (count($not_del_id_acc_inv_multiple_connected_marks )>0) {
  $sql.=" and id_acc_inv_multiple_connected_marks not in (".implode(',', $not_del_id_acc_inv_multiple_connected_marks).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

foreach ($multiple_connected_marks_array as $myrec) {
  //print '<pre>ssssssssssssssss ';print_r($myrec);die();
  $myrec['recid']=intval($myrec['recid']);
  $myrec['mcm_acc_inv_id']=intval($myrec['mcm_acc_inv_id']);
  $myrec['mcm_acc_pay_id']=intval($myrec['mcm_acc_pay_id']);
  $myrec['mcm_whi_mov_id']=intval($myrec['mcm_whi_mov_id']);
  $myrec['mcmaa']=intval($myrec['mcmaa']);
  $myrec['mcm_mark']=trim($myrec['mcm_mark']);
  if ($myrec['mcm_mark']=='') {
    $myrec['mcm_mark']=gks_aade_get_mark_from_id($myrec);
  }

  if ($myrec['recid']<=0) {
    $sql="insert into gks_acc_inv_multiple_connected_marks (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_inv_id,
      mcm_mark,
      mcm_acc_inv_id,
      mcm_acc_pay_id,
      mcm_whi_mov_id,
      mcm_aa
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      '".$db_link->escape_string($myrec['mcm_mark'])."',
      ".$myrec['mcm_acc_inv_id'].",
      ".$myrec['mcm_acc_pay_id'].",
      ".$myrec['mcm_whi_mov_id'].",
      ".$myrec['mcmaa']."
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }     
  } else {
    $sql="update gks_acc_inv_multiple_connected_marks set 
    mcm_mark='".$db_link->escape_string($myrec['mcm_mark'])."',
    mcm_acc_inv_id=".$myrec['mcm_acc_inv_id'].",
    mcm_acc_pay_id=".$myrec['mcm_acc_pay_id'].",
    mcm_whi_mov_id=".$myrec['mcm_whi_mov_id'].",
    mcm_aa=".$myrec['mcmaa'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_acc_inv_multiple_connected_marks=".$myrec['recid']."
    and acc_inv_id=".$id;  
    //echo '<pre>'.$sql; die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
}


$sql="delete from gks_acc_inv_packings_declarations where acc_inv_id=".$id;
if (count($not_del_id_acc_inv_packings_declarations )>0) {
  $sql.=" and id_acc_inv_packings_declarations not in (".implode(',', $not_del_id_acc_inv_packings_declarations).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

foreach ($packings_declarations_array as $myrec) {
  //print '<pre>ssssssssssssssss ';print_r($myrec);die();
  $myrec['recid']=intval($myrec['recid']);
  $myrec['pde_type_id']=intval($myrec['pde_type_id']);
  $myrec['pde_quantity']=intval($myrec['pde_quantity']);
  $myrec['pdeaa']=intval($myrec['pdeaa']);
  $myrec['pde_type_6_descr']=trim($myrec['pde_type_6_descr']);
  if ($myrec['pde_type_id']!=6) $myrec['pde_type_6_descr']='';
  
  if ($myrec['recid']<=0) {
    $sql="insert into gks_acc_inv_packings_declarations (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_inv_id,
      packaging_type_id,
      packaging_type_6_descr,
      packaging_quantity,
      packaging_aa
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      ".$myrec['pde_type_id'].",
      '".$db_link->escape_string($myrec['pde_type_6_descr'])."',
      ".$myrec['pde_quantity'].",
      ".$myrec['pdeaa']."
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }     
  } else {
    $sql="update gks_acc_inv_packings_declarations set 
    packaging_type_id=".$myrec['pde_type_id'].",
    packaging_type_6_descr='".$db_link->escape_string($myrec['pde_type_6_descr'])."',
    packaging_quantity=".$myrec['pde_quantity'].",
    packaging_aa=".$myrec['pdeaa'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_acc_inv_packings_declarations=".$myrec['recid']."
    and acc_inv_id=".$id;  
    //echo '<pre>'.$sql; die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
}











$sql="select id_acc_inv_product from gks_acc_inv_products where acc_inv_id=".$id;
if (count($not_del_id_acc_inv_product)>0) {
  $sql.=" and id_acc_inv_product not in (".implode(',', $not_del_id_acc_inv_product).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  

$del_id_acc_inv_product=array();
while ($row = $result->fetch_assoc()) {
  $del_id_acc_inv_product[]=$row['id_acc_inv_product'];
}
if (count($del_id_acc_inv_product)>0) {

  $sql="delete from gks_acc_inv_products_income where acc_inv_product_id in (".implode(',',$del_id_acc_inv_product).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
    

  
  $sql="delete from gks_acc_inv_products_expenses where acc_inv_product_id in (".implode(',',$del_id_acc_inv_product).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
    
  
  $sql="delete from gks_acc_inv_products_lots where acc_inv_product_id in (".implode(',',$del_id_acc_inv_product).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
    
  
  
  $sql="delete from gks_acc_inv_products where id_acc_inv_product in (".implode(',',$del_id_acc_inv_product).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
    
  
}



//$return = array('success' => false, 'message' => base64_encode($sql));
//echo json_encode($return); die();

foreach ($orders_products as $myrec) {
  if ($myrec['product_id']>0 and in_array($myrec['product_id'],$all_products_for_balance)==false)
    $all_products_for_balance[]=$myrec['product_id'];
  
  $gks_id_acc_inv_product=$myrec['id_acc_inv_product'];
  if ($myrec['id_acc_inv_product']==0) {
    $sql="insert into gks_acc_inv_products (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_inv_id,product_set,product_aa,product_id,product_fpa_base_id,product_fpa_aade_id,product_fpa_id,product_fpa_ejeresi_id,product_fpa_pososto,
      from_aade_import_user_fpa,
      
      product_sheets,
      product_monada_id,product_quantity,product_price_check_fpa,
      
      product_price_start_all_net,product_price_ekptosi_pososto,product_price_final_all_net,product_price_final_all_fpa,product_price_final_all_total,product_descr,product_comments,
      product_price_coupon_use,product_price_coupon_use_disabled,
      
      product_withheldPercentCategory,product_withheldAmount,
      product_otherTaxesPercentCategory,product_otherTaxesAmount,
      product_stampDutyPercentCategory,product_stampDutyAmount,
      product_feesPercentCategory,product_feesAmount,
      product_deductionsSelection,product_deductionsAmount,
      p_warehouses_id_from,p_warehouses_id_to
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      '".$db_link->escape_string($myrec['product_set'])."',
      ".$myrec['product_aa'].",
      ".$myrec['product_id'].",
      ".$myrec['product_fpa_base_id'].",
      ".$myrec['product_fpa_aade_id'].",
      ".$myrec['product_fpa_id'].",
      ".$myrec['product_fpa_ejeresi_id'].",
      ".number_format($myrec['product_fpa_pososto'],10, '.','').",
      ".$myrec['from_aade_import_user_fpa'].",
      
      
      
      ".$myrec['product_sheets'].",
      ".$myrec['product_monada_id'].",
      ".$myrec['product_quantity'].",
      ".$myrec['product_price_check_fpa'].",
      
      ".number_format($myrec['product_price_start_all_net'],10, '.','').",
      ".number_format($myrec['product_price_ekptosi_pososto'],10, '.','').",
      
      ".number_format($myrec['product_price_final_all_net'],10, '.','').",
      ".number_format($myrec['product_price_final_all_fpa'],10, '.','').",
      ".number_format($myrec['product_price_final_all_total'],10, '.','').",
      '".$db_link->escape_string($myrec['product_descr'])."',
      '".$db_link->escape_string($myrec['product_comments'])."',
      '".$db_link->escape_string($myrec['product_price_coupon_use'])."',
      ".$myrec['product_price_coupon_use_disabled'].",
      
      ".$myrec['product_withheldPercentCategory'].",
      ".number_format($myrec['product_withheldAmount'],10, '.','').",
      ".$myrec['product_otherTaxesPercentCategory'].",
      ".number_format($myrec['product_otherTaxesAmount'],10, '.','').",
      ".$myrec['product_stampDutyPercentCategory'].",
      ".number_format($myrec['product_stampDutyAmount'],10, '.','').",
      ".$myrec['product_feesPercentCategory'].",
      ".number_format($myrec['product_feesAmount'],10, '.','').",
      '".$db_link->escape_string($myrec['product_deductionsSelection'])."',
      ".number_format($myrec['product_deductionsAmount'],10, '.','').",
      ".$warehouses_id_from.",
      ".$warehouses_id_to."
      
      
    )";
    //echo '<pre>'.$sql;die();
    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }   
      
    $gks_id_acc_inv_product=$db_link->insert_id;
     
  } else {
    $sql="update gks_acc_inv_products set 
      product_set='".$db_link->escape_string($myrec['product_set'])."',
      product_aa=".$myrec['product_aa'].",
      product_id=".$myrec['product_id'].",
      product_fpa_base_id=".$myrec['product_fpa_base_id'].",
      product_fpa_aade_id=".$myrec['product_fpa_aade_id'].",
      product_fpa_id=".$myrec['product_fpa_id'].",
      product_fpa_ejeresi_id=".$myrec['product_fpa_ejeresi_id'].",
      product_fpa_pososto= ".number_format($myrec['product_fpa_pososto'],10, '.','').",
      from_aade_import_user_fpa=".$myrec['from_aade_import_user_fpa'].",
      product_sheets=".$myrec['product_sheets'].",
      product_monada_id=".$myrec['product_monada_id'].",
      product_quantity=".$myrec['product_quantity'].",
      product_price_check_fpa=".$myrec['product_price_check_fpa'].",
      product_price_start_all_net=".number_format($myrec['product_price_start_all_net'],10, '.','').",
      product_price_ekptosi_pososto=".number_format($myrec['product_price_ekptosi_pososto'],10, '.','').",
      product_price_final_all_net=".number_format($myrec['product_price_final_all_net'],10, '.','').",
      product_price_final_all_fpa=".number_format($myrec['product_price_final_all_fpa'],10, '.','').",
      product_price_final_all_total=".number_format($myrec['product_price_final_all_total'],10, '.','').",
      product_descr='".$db_link->escape_string($myrec['product_descr'])."',
      product_comments='".$db_link->escape_string($myrec['product_comments'])."',
      product_price_coupon_use='".$db_link->escape_string($myrec['product_price_coupon_use'])."',
      product_price_coupon_use_disabled=".$myrec['product_price_coupon_use_disabled'].",
      
      product_withheldPercentCategory=".$myrec['product_withheldPercentCategory'].",
      product_withheldAmount=".number_format($myrec['product_withheldAmount'],10, '.','').",
      product_otherTaxesPercentCategory=".$myrec['product_otherTaxesPercentCategory'].",
      product_otherTaxesAmount=".number_format($myrec['product_otherTaxesAmount'],10, '.','').",
      product_stampDutyPercentCategory=".$myrec['product_stampDutyPercentCategory'].",
      product_stampDutyAmount=".number_format($myrec['product_stampDutyAmount'],10, '.','').",
      product_feesPercentCategory=".$myrec['product_feesPercentCategory'].",
      product_feesAmount=".number_format($myrec['product_feesAmount'],10, '.','').",
      product_deductionsSelection='".$db_link->escape_string($myrec['product_deductionsSelection'])."',
      product_deductionsAmount=".number_format($myrec['product_deductionsAmount'],10, '.','').",
      p_warehouses_id_from=".$warehouses_id_from.",
      p_warehouses_id_to=".$warehouses_id_to.",
      
      
      mydate_edit=now(),
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where id_acc_inv_product=".$myrec['id_acc_inv_product']." and acc_inv_id=".$id;
    //echo '<pre>'.$sql;die();
   
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
    
    
  }
  
  
  if (1==1) { //Esoda
    $sql="update gks_acc_inv_products_income set 
    aade_typos_xarakt_esodon_id=0,aade_katigoria_xarakt_esodon_id=0,acc_inv_product_income_ammount=0
    where acc_inv_product_id=".$gks_id_acc_inv_product;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select id_acc_inv_product_income from gks_acc_inv_products_income where acc_inv_product_id=".$gks_id_acc_inv_product." order by id_acc_inv_product_income";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $exist_ids=array();
    while ($row = $result->fetch_assoc()) {
      $exist_ids[]=$row['id_acc_inv_product_income'];
    }   
    
    foreach ($myrec['xarakt_esoda'] as $value) {
      $id_found=0;
      foreach ($exist_ids as &$oid) {if ($oid>0) {$id_found=$oid;$oid=0;break;}} unset($oid);
      if ($id_found>0) {
        $sql="update gks_acc_inv_products_income set 
        aade_katigoria_xarakt_esodon_id=".$value['cat_id'].",
        aade_typos_xarakt_esodon_id=".$value['typos_id'].",
        acc_inv_product_income_ammount=".number_format($value['ammount'],10, '.','') ."
        where id_acc_inv_product_income=".$id_found;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      } else {
        $sql="insert into gks_acc_inv_products_income (
          acc_inv_product_id,aade_katigoria_xarakt_esodon_id,aade_typos_xarakt_esodon_id,acc_inv_product_income_ammount
        ) values (
          ".$gks_id_acc_inv_product.",
          ".$value['cat_id'].",
          ".$value['typos_id'].",
          ".number_format($value['ammount'],10, '.','')."
        )";        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      }
    }
    $for_del=array();
    foreach ($exist_ids as $oid) if ($oid>0) $for_del[]=$oid;
    if (count($for_del)>0) {
      $sql="delete from gks_acc_inv_products_income where id_acc_inv_product_income in (".implode(',', $for_del).")";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
  
  
  if (1==1) { //Eksoda
    
    $sql="update gks_acc_inv_products_expenses set 
    aade_typos_xarakt_eksodon_id=0,aade_katigoria_xarakt_eksodon_id=0,acc_inv_product_expenses_ammount=0
    where acc_inv_product_id=".$gks_id_acc_inv_product;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select id_acc_inv_product_expenses from gks_acc_inv_products_expenses where acc_inv_product_id=".$gks_id_acc_inv_product." order by id_acc_inv_product_expenses";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $exist_ids=array();
    while ($row = $result->fetch_assoc()) {
      $exist_ids[]=$row['id_acc_inv_product_expenses'];
    }   
    
    foreach ($myrec['xarakt_eksoda'] as $value) {
      $id_found=0;
      foreach ($exist_ids as &$oid) {if ($oid>0) {$id_found=$oid;$oid=0;break;}} unset($oid);
      if ($id_found>0) {
        $sql="update gks_acc_inv_products_expenses set 
        aade_katigoria_xarakt_eksodon_id=".$value['cat_id'].",
        aade_typos_xarakt_eksodon_id=".$value['typos_id'].",
        acc_inv_product_expenses_ammount=".number_format($value['ammount'],10, '.','') ."
        where id_acc_inv_product_expenses=".$id_found;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      } else {
        $sql="insert into gks_acc_inv_products_expenses (
          acc_inv_product_id,aade_katigoria_xarakt_eksodon_id,aade_typos_xarakt_eksodon_id,acc_inv_product_expenses_ammount
        ) values (
          ".$gks_id_acc_inv_product.",
          ".$value['cat_id'].",
          ".$value['typos_id'].",
          ".number_format($value['ammount'],10, '.','')."
        )";        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      }
    }
    $for_del=array();
    foreach ($exist_ids as $oid) if ($oid>0) $for_del[]=$oid;
    if (count($for_del)>0) {
      $sql="delete from gks_acc_inv_products_expenses where id_acc_inv_product_expenses in (".implode(',', $for_del).")";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }    
  }
  
  
  
  if ($GKS_PRODUCT_LOTS_SERIALS) {
    $sql="update gks_acc_inv_products_lots set 
    lot_product_id=0,lot_product_quantity=0
    where acc_inv_product_id=".$gks_id_acc_inv_product;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select id_acc_inv_product_lots from gks_acc_inv_products_lots where acc_inv_product_id=".$gks_id_acc_inv_product." order by id_acc_inv_product_lots";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $exist_ids=array();
    while ($row = $result->fetch_assoc()) {
      $exist_ids[]=$row['id_acc_inv_product_lots'];
    }   
    
    foreach ($myrec['product_lots_serials'] as $lot_product_item) {
      $id_found=0;
      foreach ($exist_ids as &$oid) {if ($oid>0) {$id_found=$oid;$oid=0;break;}} unset($oid);
      if ($id_found>0) {
        $sql="update gks_acc_inv_products_lots set 
        lot_product_id=".$lot_product_item['lot_product_id'].",
        lot_product_quantity=".number_format($lot_product_item['lot_product_quantity'],10,'.','')."
        where id_acc_inv_product_lots=".$id_found;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      } else {
        $sql="insert into gks_acc_inv_products_lots (
          acc_inv_product_id,lot_product_id,lot_product_quantity
        ) values (
          ".$gks_id_acc_inv_product.",
          ".$lot_product_item['lot_product_id'].",
          ".number_format($lot_product_item['lot_product_quantity'],10,'.','')."
        )";        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      }
    }
    $for_del=array();
    foreach ($exist_ids as $oid) if ($oid>0) $for_del[]=$oid;
    if (count($for_del)>0) {
      $sql="delete from gks_acc_inv_products_lots where id_acc_inv_product_lots in (".implode(',', $for_del).")";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }    
    
    
    
  }
  
  
  
} 

if (count($del_id_acc_inv_product)>0) {
  $sql="delete from gks_acc_inv_products_lots where acc_inv_product_id in (".implode(',',$del_id_acc_inv_product).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  //echo $sql;die();
}   



$gks_pricelist_item_id=0;




//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($not_del_id_acc_inv_product,true).print_r($orders_products,true).print_r($eidi_array,true)));
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($orders_products,true).'</pre>'));
//echo json_encode($return); die();

//echo '<pre>sssssssssssssss '.$inv_state;die();

$has_ekdosi=false;
$save_but_message='';
if ($inv_state=='090ekdosi' and $is_xeirografi==0) {
  //ekdosi

  gks_inv_get_ekdosi_numbers();
  
}



//inv_state
$sql="update gks_acc_inv set ";
if ($inv_state!= '') {
  $sql.="inv_state='".$db_link->escape_string($inv_state)."', ";
}
if ($is_xeirografi!=0) {

  $sql_seira="select * from gks_acc_seires where id_acc_seira=".$inv_acc_seira_id;
  $result_seira = $db_link->query($sql_seira);  
  if (!$result_seira) {
    debug_mail(false,'error sql',$sql_seira);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_seira->num_rows==0) {
    debug_mail(false,'seira not founs',$sql_seira);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η σειρά')));
    echo json_encode($return); die(); }

  $row_seira = $result_seira->fetch_assoc();
  $seires_prefix=trim_gks($row_seira['prefix']);
  $seires_suffix=trim_gks($row_seira['suffix']);
  $seires_number_size=$row_seira['number_size'];  

  
  $inv_acc_number_str_user=$seires_prefix.str_pad($inv_acc_number_int_user, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
  $inv_acc_seira_code_user=trim_gks($row_seira['seira_code']);
  
  $sql.="inv_acc_number_int=".$inv_acc_number_int_user.",
         inv_acc_number_str='".$db_link->escape_string($inv_acc_number_str_user)."',
         inv_acc_seira_code='".$db_link->escape_string($inv_acc_seira_code_user)."',";
           
  if ($inv_state=='080listing' and $inv_acc_ekdosi_date_old=='') {
    $sql.="inv_acc_ekdosi_date=now(),";
  }
} else {
  if ($has_ekdosi) {
    $sql.="inv_acc_number_int=".$inv_acc_number_int_new.",
           inv_acc_number_str='".$db_link->escape_string($inv_acc_number_str_new)."',
           inv_acc_ekdosi_date=now(),
           inv_acc_seira_code='".$db_link->escape_string($inv_acc_seira_code_new)."',";
  }
}

//price=".number_format($price, 10, '.', '').",
$sql.="
company_id=".$company_id.",
inv_acc_journal_id=".$inv_acc_journal_id.",
inv_acc_seira_id=".$inv_acc_seira_id.",
company_sub_id=".$company_sub_id.",
inv_date=".($inv_date == '' ? 'null' : "'".$db_link->escape_string($inv_date)."'") .", 
user_id=".$user_id.",

user_first_name='".$db_link->escape_string($dr_user_first_name)."',
user_last_name='".$db_link->escape_string($dr_user_last_name)."',
user_email='".$db_link->escape_string($dr_user_email)."',
user_mobile='".$db_link->escape_string($dr_user_mobile)."',
user_lang='".$db_link->escape_string($dr_user_lang)."',
ma_odos='".$db_link->escape_string($dr_user_ma_odos)."',
ma_arithmos='".$db_link->escape_string($dr_user_ma_arithmos)."',
ma_orofos='".$db_link->escape_string($dr_user_ma_orofos)."',
ma_perioxi='".$db_link->escape_string($dr_user_ma_perioxi)."',
ma_poli='".$db_link->escape_string($dr_user_ma_poli)."',
ma_tk='".$db_link->escape_string($dr_user_ma_tk)."',
ma_country_id=".$dr_user_ma_country_id.",
ma_nomos_id=".$dr_user_ma_nomos_id.",

eponimia='".$db_link->escape_string($dr_user_eponimia)."',
title='".$db_link->escape_string($dr_user_title)."',
afm='".$db_link->escape_string($dr_user_afm)."',
doy='".$db_link->escape_string($dr_user_doy)."',
epaggelma='".$db_link->escape_string($dr_user_epaggelma)."',
address_extra=".$form_select_apostoli.",

destination_data_name='".$db_link->escape_string($destination_data_name)."',
destination_data_phone='".$db_link->escape_string($destination_data_phone)."',
destination_data_odos='".$db_link->escape_string($destination_data_odos)."',
destination_data_arithmos='".$db_link->escape_string($destination_data_arithmos)."',
destination_data_orofos='".$db_link->escape_string($destination_data_orofos)."',
destination_data_perioxi='".$db_link->escape_string($destination_data_perioxi)."',
destination_data_poli='".$db_link->escape_string($destination_data_poli)."',
destination_data_tk='".$db_link->escape_string($destination_data_tk)."',
destination_data_country_id=".$destination_data_country_id.",
destination_data_nomos_id=".$destination_data_nomos_id.",



note_doc='".$db_link->escape_string($note_doc)."',
note_logistirio='".$db_link->escape_string($note_logistirio)."',

tropos_apostolis=".$tropos_apostolis.",
tropos_pliromis=".$tropos_pliromis.",
tropos_pliromis_one_multi=".$tropos_pliromis_one_multi.",
delivery_id_8=".$delivery_id_8.",
delivery_number='".$db_link->escape_string($delivery_number)."',
vehicle_number='".$db_link->escape_string($vehicle_number)."',
dispatch_date=".($dispatch_date == '' ? 'null' : "'".$db_link->escape_string($dispatch_date)."'") .", 
dispatch_time=".($dispatch_time == '' ? 'null' : "'".$db_link->escape_string($dispatch_time)."'") .", 
kostos_apostolis=".number_format($kostos_apostolis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').", 
kostos_pliromis=".number_format($kostos_pliromis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
def_ekptosi=".number_format($def_ekptosi, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
coupons='".$db_link->escape_string($coupons_str)."',

affect_balance=".$affect_balance.",
affect_balance_all_poso=".$affect_balance_all_poso.",
affect_balance_all_poso_type='".$db_link->escape_string($affect_balance_all_poso_type)."',";

if ($affect_balance == 0) {
  $affect_balance_poso=0;
} else {
  if ($affect_balance_all_poso==1) {
    switch ($affect_balance_all_poso_type) {
      case 'price_net':
        $affect_balance_poso=$gks_price_net;
        break;  
      case 'price_netfpa':
        $affect_balance_poso=$gks_price_netfpa;
        break;  
      case 'price_total':
        $affect_balance_poso=$gks_price_total;
        break;  
      case 'pliroteo':
        $affect_balance_poso=$gks_price_total + $kostos_apostolis + $kostos_pliromis;
        break;  
      default:     
      
    }
  } else {
    //$affect_balance_poso=$affect_balance_poso;
  }
}  
$sql.="affect_balance_poso=".number_format($affect_balance_poso, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",";

if ($affect_balance_pros!=-1 and $affect_balance_pros!=1) {
  $affect_balance_pros=0;
}
$sql.="affect_balance_pros=".$affect_balance_pros.",";

$sql.="assigned_id=".$assigned_id.",
merchant_ref_trns=". ($merchant_ref_trns =='' ? 'null' : "'".$db_link->escape_string($merchant_ref_trns)."'").",";

if ($GKS_CRM_ENABLE) {
$sql.=
"crm_channel_id=".$crm_channel_id.",
crm_channel_contact_id=".$crm_channel_contact_id.",
crm_channel_campain_id=".$crm_channel_campain_id.",
crm_channel_url=". ($crm_channel_url =='' ? 'null' : "'".$db_link->escape_string($crm_channel_url)."'").",
crm_channel_code=". ($crm_channel_code =='' ? 'null' : "'".$db_link->escape_string($crm_channel_code)."'").",
crm_channel_text=". ($crm_channel_text =='' ? 'null' : "'".$db_link->escape_string($crm_channel_text)."'").",";
}


$sql.="
gks_price_original_net=".number_format($gks_price_original_net, 10, '.', '').", 
gks_price_net=".number_format($gks_price_net, 10, '.', '').", 
gks_price_fpa=".number_format($gks_price_fpa, 10, '.', '').", 
gks_price_netfpa=".number_format($gks_price_netfpa, 10, '.', '').", 
gks_price_total=".number_format($gks_price_total, 10, '.', '').", 

totalWithheldAmount=".number_format($totalWithheldAmount, 10, '.', '').", 
totalOtherTaxesAmount=".number_format($totalOtherTaxesAmount, 10, '.', '').", 
totalStampDutyamount=".number_format($totalStampDutyamount, 10, '.', '').", 
totalFeesAmount=".number_format($totalFeesAmount, 10, '.', '').", 
totalDeductionsAmount=".number_format($totalDeductionsAmount, 10, '.', '').", 

aade_skopos_diakinisis_id=".$aade_skopos_diakinisis_id.",
aade_skopos_19_descr='".$db_link->escape_string($aade_skopos_19_descr)."', 
fiscal_position_id=".$fiscal_position_id.",
pricelist_id=".$pricelist_id.",

warehouses_id_from=".$warehouses_id_from.",
load_branch=".($load_branch === '' ? 'null' : $load_branch) .", 
load_odos='".$db_link->escape_string($load_odos)."', 
load_arithmos='".$db_link->escape_string($load_arithmos)."', 
load_orofos='".$db_link->escape_string($load_orofos)."', 
load_perioxi='".$db_link->escape_string($load_perioxi)."', 
load_poli='".$db_link->escape_string($load_poli)."', 
load_tk='".$db_link->escape_string($load_tk)."', 
load_country_id=".$load_country_id.",
load_nomos_id=".$load_nomos_id.",

warehouses_id_to=".$warehouses_id_to.",
deli_branch=".($deli_branch === '' ? 'null' : $deli_branch) .", 
deli_odos='".$db_link->escape_string($deli_odos)."', 
deli_arithmos='".$db_link->escape_string($deli_arithmos)."', 
deli_orofos='".$db_link->escape_string($deli_orofos)."', 
deli_perioxi='".$db_link->escape_string($deli_perioxi)."', 
deli_poli='".$db_link->escape_string($deli_poli)."', 
deli_tk='".$db_link->escape_string($deli_tk)."', 
deli_country_id=".$deli_country_id.",
deli_nomos_id=".$deli_nomos_id.",

b2g_inv_aaht_name='".$db_link->escape_string($b2g_inv_aaht_name)."',
project_reference='".$db_link->escape_string($project_reference)."',
contract_reference='".$db_link->escape_string($contract_reference)."',
b2g_inv_buyer_name='".$db_link->escape_string($b2g_inv_buyer_name)."',
b2g_inv_aaht_code='".$db_link->escape_string($b2g_inv_aaht_code)."',


update_from_gks=1,
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'

where id_acc_inv = ".$id." limit 1";
//echo '<pre>ssssssssssssss '.$sql;die();
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }





$myarray_new=array();
$myarray_line_new=array();
$idiotites_new=get_acc_inv_details_txt($id, $myarray_new, $myarray_line_new); 

$sql="update gks_acc_inv set idiotites='".$db_link->escape_string(json_encode($myarray_new))."' where id_acc_inv = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
  


$sql="UPDATE gks_acc_inv_products
LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product
SET
gks_acc_inv_products.product_need_apostoli = gks_eshop_products.product_need_apostoli,
gks_acc_inv_products.product_varos = gks_eshop_products.product_varos,
gks_acc_inv_products.product_ogos_x = gks_eshop_products.product_ogos_x,
gks_acc_inv_products.product_ogos_y = gks_eshop_products.product_ogos_y,
gks_acc_inv_products.product_ogos_z = gks_eshop_products.product_ogos_z,
gks_acc_inv_products.product_is_digital = gks_eshop_products.product_is_digital,
gks_acc_inv_products.product_is_simple_download = gks_eshop_products.product_is_simple_download,
gks_acc_inv_products.product_normal = gks_eshop_products.product_normal,
gks_acc_inv_products.product_type = gks_eshop_products.product_type,
gks_acc_inv_products.product_need_multi_files = gks_eshop_products.product_need_multi_files,
gks_acc_inv_products.product_need_multi_files_min = gks_eshop_products.product_need_multi_files_min,
gks_acc_inv_products.product_need_multi_files_max = gks_eshop_products.product_need_multi_files_max,
gks_acc_inv_products.product_monada_id_org=gks_eshop_products.product_monada_id

WHERE gks_acc_inv_products.acc_inv_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 
$sql="select * from gks_acc_inv_products where acc_inv_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 
$products_posotita=0;
//$products_varos=0;
//$products_ogos=0;
//$products_ogos_max_x=0;
//$products_ogos_max_y=0;
//$products_ogos_max_z=0;
$products_need_apostoli=0;

while ($row = $result->fetch_assoc()) {
  $products_posotita+=$row['product_quantity'];
  //$products_varos+=$row['product_quantity'] * $row['product_varos'];
  //$products_ogos+=$row['product_quantity'] * ($row['product_ogos_x'] * $row['product_ogos_y'] * $row['product_ogos_z']);
  
  //if ($row['product_ogos_x'] > $products_ogos_max_x) $products_ogos_max_x=$row['product_ogos_x'];
  //if ($row['product_ogos_y'] > $products_ogos_max_y) $products_ogos_max_y=$row['product_ogos_y'];
  //$products_ogos_max_z+=$row['product_quantity'] * $row['product_ogos_z'];  
  
  if ($row['product_need_apostoli']!=0) $products_need_apostoli=1;
}

$products_need_pliromi=0;
if ($gks_price_net>0) $products_need_pliromi=1;

//products_varos=".number_format($products_varos,8,'.','').",
//products_ogos=".number_format($products_ogos,8,'.','').",
//products_ogos_max_x=".number_format($products_ogos_max_x,8,'.','').",
//products_ogos_max_y=".number_format($products_ogos_max_y,8,'.','').",
//products_ogos_max_z=".number_format($products_ogos_max_z,8,'.','').",

$sql="update gks_acc_inv set 
products_posotita=".number_format($products_posotita,8,'.','').",
products_need_apostoli=".$products_need_apostoli.",
products_need_pliromi=".$products_need_pliromi.",
session_id='".$_gks_id_session."'
where id_acc_inv=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 




if (is_array($mybasketarray)) {
  //print '<pre>';
  //print_r($mybasketarray);
  //die();
  $sql='';
  if (isset($mybasketarray['products_varos'])) $sql.="products_varos=".number_format($mybasketarray['products_varos'],8,'.','').",";
  if (isset($mybasketarray['products_ogos'])) $sql.="products_ogos=".number_format($mybasketarray['products_ogos'],8,'.','').",";
  if (isset($mybasketarray['products_ogos_max_x'])) $sql.="products_ogos_max_x=".number_format($mybasketarray['products_ogos_max_x'],8,'.','').",";
  if (isset($mybasketarray['products_ogos_max_y'])) $sql.="products_ogos_max_y=".number_format($mybasketarray['products_ogos_max_y'],8,'.','').",";
  if (isset($mybasketarray['products_ogos_max_z'])) $sql.="products_ogos_max_z=".number_format($mybasketarray['products_ogos_max_z'],8,'.','').",";
  if (isset($mybasketarray['tropoi_apostolis_all']) and 
      isset($mybasketarray['tropos_apostolis']) and
      isset($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]) ) {
        $sql.="tropos_apostolis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]))."',";
  }
  if (isset($mybasketarray['tropoi_pliromis_all']) and 
      isset($mybasketarray['tropos_pliromis']) and
      isset($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]) ) {
        $sql.="kostos_pliromis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]))."',";
  }
  
  if ($sql!='') {
    $sql=substr($sql, 0, strlen($sql)-1);
    $sql="update gks_acc_inv set ".$sql." where id_acc_inv=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
  }
  
  
  if (isset($mybasketarray['products'])) {
    foreach ($mybasketarray['products'] as $aa => $product) {
      $sql='';
      if (isset($product['product_id']['product_fpa_id_array'])) $sql.="product_fpa_id_json='".$db_link->escape_string(json_encode($product['product_id']['product_fpa_id_array']))."',";
      if (isset($product['product_id']['product_price_include_vat'])) $sql.="product_price_include_vat=".intval($product['product_id']['product_price_include_vat']).",";
      if (isset($product['product_id']['product_price_start_peritem_db'])) $sql.="product_price_start_peritem_db=".number_format($product['product_id']['product_price_start_peritem_db'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_peritem_net'])) $sql.="product_price_start_peritem_net=".number_format($product['product_id']['product_price_start_peritem_net'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_peritem_fpa'])) $sql.="product_price_start_peritem_fpa=".number_format($product['product_id']['product_price_start_peritem_fpa'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_peritem_total'])) $sql.="product_price_start_peritem_total=".number_format($product['product_id']['product_price_start_peritem_total'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_all_fpa'])) $sql.="product_price_start_all_fpa=".number_format($product['product_id']['product_price_start_all_fpa'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_all_total'])) $sql.="product_price_start_all_total=".number_format($product['product_id']['product_price_start_all_total'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_db'])) $sql.="product_price_final_peritem_db=".number_format($product['product_id']['product_price_final_peritem_db'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_net'])) $sql.="product_price_final_peritem_net=".number_format($product['product_id']['product_price_final_peritem_net'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_fpa'])) $sql.="product_price_final_peritem_fpa=".number_format($product['product_id']['product_price_final_peritem_fpa'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_total'])) $sql.="product_price_final_peritem_total=".number_format($product['product_id']['product_price_final_peritem_total'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_all_net']) and 
          isset($product['product_id']['product_price_final_all_net'])) {
        $product_price_ekptosi_net=$product['product_id']['product_price_start_all_net']-$product['product_id']['product_price_final_all_net'];
        $sql.="product_price_ekptosi_net=".number_format($product_price_ekptosi_net,8,'.','').",";
      }
      if (isset($product['product_id']['product_pricelist_item_id'])) $sql.="product_pricelist_item_id=".intval($product['product_id']['product_pricelist_item_id']).",";
      if (isset($product['product_id']['product_pricelist_item_descr'])) $sql.="product_pricelist_item_descr='".$db_link->escape_string($product['product_id']['product_pricelist_item_descr'])."',";
      if (isset($product['product_id']['product_pricelist_item_percent'])) $sql.="product_pricelist_item_percent=".number_format($product['product_id']['product_pricelist_item_percent'],8,'.','').",";

      if (isset($product['product_id']['monada_convert'])) $sql.="monada_convert_json='".$db_link->escape_string(json_encode($product['product_id']['monada_convert']))."',";
    
      if (isset($product['product_id']['monada_convert']) and 
          isset($product['product_id']['monada_convert']['ok']) and 
          isset($product['product_id']['monada_convert']['epi']) and 
          $product['product_id']['monada_convert']['ok'] and 
          $product['product_id']['monada_convert']['epi'] !=0 and
          $product['product_id']['monada_convert']['epi'] != 1) {
        $sql.="monada_convert_epi=".number_format($product['product_id']['monada_convert']['epi'],16,'.','').",";
        $sql.="monada_convert_epi_rev=".number_format($product['product_id']['monada_convert']['epi_rev'],16,'.','').",";
      } else {
        $sql.="monada_convert_epi=1,";
        $sql.="monada_convert_epi_rev=1,";
      }
//  , 
//  , 
//  
//  

      if ($sql!='') {
        $sql=substr($sql, 0, strlen($sql)-1);
        $sql="update gks_acc_inv_products set ".$sql." where acc_inv_id=".$id." and product_aa=".$aa." limit 1";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();
        }    
      }


    }   

    
  }
//  print '<pre>';
//  echo $sql;
//  die();
  
  

  
  
}




gks_acc_inv_acc_inv_payment_part_c();












$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

if ($is_new_rec == false) {

  gks_inv_sxolio_log($id,$row_old,$products_old,$extra_address_old,'',[],$gks_custom_row_old);
  
}

$balance_user=gks_balance_calc(['id' => $user_id]);
if (isset($row_old['user_id']) and $row_old['user_id']>0 and $row_old['user_id']!=$user_id) gks_balance_calc(['id' => $row_old['user_id']]);

$p_inv_state=$inv_state!='' ? $inv_state : $inv_state_old;
$sql="update gks_acc_inv_products set p_inv_state='".$db_link->escape_string($p_inv_state)."' where acc_inv_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}    

  
$mybal = gks_whi_mov_balance_calc($all_products_for_balance);

gks_whi_after_balance_for_inv($id);

gks_update_user_from_some_move(array('user_id'=>$user_id,'table'=>'gks_acc_inv','id_table'=>$id));


$return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> $redirect,'save_but_message' => base64_encode($save_but_message));
echo json_encode($return); die();







function gks_inv_to_draft($id) {
  
  global $db_link;

  $sql_credit_memo="select id_acc_inv from gks_acc_inv where credit_memo_for_acc_inv_id=".$id;
  $result_credit_memo = $db_link->query($sql_credit_memo);  
  if (!$result_credit_memo) {
    debug_mail(false,'error sql',$result_credit_memo);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_credit_memo->num_rows>=1) {
    $row_credit_memo=$result_credit_memo->fetch_assoc();
    $message=gks_lang('Για αυτό το παραστατικό υπάρχει συσχετιζόμενο πιστωτικό παραστατικό με').'<br>'.
    'ID :<b>'.$row_credit_memo['id_acc_inv'].'</b><br>'.
    '<a class="gks_link" href="admin-acc-inv-item.php?id='.$row_credit_memo['id_acc_inv'].'">'.gks_lang('Προβολή').'</a><br>'.
    gks_lang('Οπότε, δεν μπορεί να γίνει αυτή η αλλαγή, εκτός και εάν διαγραφεί το συσχετιζόμενο πιστωτικό παραστατικό');
    debug_mail(false,'cancel_for_acc_inv_id='.$row_credit_memo['id_acc_inv'],$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }

  
  $sql_canceled="select id_acc_inv from gks_acc_inv where cancel_for_acc_inv_id=".$id;
  $result_canceled = $db_link->query($sql_canceled);  
  if (!$result_canceled) {
    debug_mail(false,'error sql',$result_canceled);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_canceled->num_rows>=1) {
    $row_canceled=$result_canceled->fetch_assoc();
    $message=gks_lang('Για αυτό το παραστατικό υπάρχει ακυρωτικό παραστατικό με').'<br>'.
    'ID :<b>'.$row_canceled['id_acc_inv'].'</b><br>'.
    '<a class="gks_link" href="admin-acc-inv-item.php?id='.$row_canceled['id_acc_inv'].'">'.gks_lang('Προβολή').'</a><br>'.
    gks_lang('Οπότε, δεν μπορεί να γίνει αυτή η αλλαγή, εκτός και εάν διαγραφεί το ακυρωτικό παραστατικό');
    debug_mail(false,'cancel_for_acc_inv_id='.$row_canceled['id_acc_inv'],$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
  

    
  //die('<pre>ssss');
  
  $sql="select * from gks_acc_inv where id_acc_inv=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_inv = $result->fetch_assoc();
  $inv_acc_seira_id=$row_inv['inv_acc_seira_id'];
  $inv_acc_number_int_old=$row_inv['inv_acc_number_int'];
  $aade_invoicemark=trim_gks($row_inv['aade_invoicemark']);
  $from_aade_import=trim_gks($row_inv['from_aade_import']);
  
  
  if ($aade_invoicemark!='' and $from_aade_import=='') {
    $message=gks_lang('Αυτό το παραστατικό έχει ήδη αποσταλεί στην ΑΑΔΕ οπότε δεν μπορεί να γίνει πρόχειρο');
    debug_mail(false,'gks_inv_to_draft '.$id,$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
  
  $sql="select * from gks_acc_seires where id_acc_seira=".$inv_acc_seira_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_seira = $result->fetch_assoc();
  $prev_number=$row_seira['next_number']-$row_seira['number_step'];
  
  
  $warning_message='';
  if ($prev_number!=$inv_acc_number_int_old) {
    $warning_message=
          gks_lang('Επόμενος αριθμός σειράς').': <b>'.$row_seira['next_number'].'</b><br>'.
          gks_lang('Βήμα σειράς').': <b>'.$row_seira['number_step'].'</b><br>'.
          gks_lang('Τρέχον αριθμός παραστατικού').': <b>'.$inv_acc_number_int_old.'</b> (<>'.
          $row_seira['next_number'].'-'.$row_seira['number_step'].')';
          
    debug_mail(false,'prev_number is not equal inv_acc_number_int_old',$prev_number.' != '.$inv_acc_number_int_old.' '.$warning_message);

  } else {  
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $sql="update gks_acc_seires set next_number=next_number-number_step where id_acc_seira=".$inv_acc_seira_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $sql="unlock tables;";       
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
      
    $sql_auto_number="update gks_acc_seires_auto_numbers set disabled_date = now()
    where acc_seira_id=".$inv_acc_seira_id." and acc_inv_id=".$id." and disabled_date is null";
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    

  }
  //$return = array('success' => false, 'message' => base64_encode('sssssss'));
  //echo json_encode($return); die();
  
  if ($prev_number==$inv_acc_number_int_old) {
    $sql="update gks_acc_inv set inv_state='010draft', inv_acc_number_int=0, inv_acc_number_str=null,inv_acc_ekdosi_date=null where id_acc_inv=".$id;
  } else {
    $sql="update gks_acc_inv set inv_state='010draft' where id_acc_inv=".$id;
  }
  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
    
  return $warning_message;
}


function gks_acc_inv_acc_inv_payment_part_a() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;  
  global $id;
  global $tropos_pliromis_one_multi;
  global $tropos_pliromis;
  global $acc_inv_payment_array;
  
  
  if ($tropos_pliromis_one_multi==1) {
    $acc_inv_payment_str=''; if (isset($_POST['acc_inv_payment_str'])) $acc_inv_payment_str=trim_gks(base64_decode($_POST['acc_inv_payment_str']));
    $acc_inv_payment_array = json_decode($acc_inv_payment_str, true);
    if ($acc_inv_payment_array === null && json_last_error() !== JSON_ERROR_NONE) {
      debug_mail(false,'json_decode error',$_POST['acc_inv_payment_str']);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (payments)<br>'.gks_lang('Ξαναδοκιμάστε')));
      echo json_encode($return); die();}
    //echo '<pre>aaa '; print_r($acc_inv_payment_array);die();
  
  } else {
    
    $acc_inv_payment_array[]=array(
      'pp'=>1,
      'rec'=>0,
      'pid'=> $tropos_pliromis,
      'val'=> 0,
      'aid'=>0,
    );
    if (isset($_POST['payment_one_asset_id'])) {
      $acc_inv_payment_array[0]['aid']=intval($_POST['payment_one_asset_id']);
    }
    //echo '<pre>'; print_r($acc_inv_payment_array);die();
    
    $sql_pp="select id_acc_inv_payment,transaction_id,payment_acquirer_id from gks_acc_inv_payment where acc_inv_id=".$id." order by transaction_id desc limit 1";
    $result_pp = $db_link->query($sql_pp);  
    if (!$result_pp) {
      debug_mail(false,'error sql',$sql_pp);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_pp->num_rows==1) {
      $row_pp = $result_pp->fetch_assoc();
      $acc_inv_payment_array[0]['rec']=intval($row_pp['id_acc_inv_payment']);
      if (intval($row_pp['transaction_id'])>0 and intval($row_pp['payment_acquirer_id'])>0) {
        $acc_inv_payment_array[0]['rec']=intval($row_pp['payment_acquirer_id']);
        $tropos_pliromis=intval($row_pp['payment_acquirer_id']);
      }
    }
    //echo '<pre>'; print_r($acc_inv_payment_array);die();    
  }
  
  //oti pos exei ginei epipleon sto parelthon na to valo edo, min tixon kai exei lathos apo javascript
  $exist_transaction_ids=[];
  foreach ($acc_inv_payment_array as $pp_item) {
    $exist_transaction_ids[]=$pp_item['rec'];
  }
  $sql_pp="select * from gks_acc_inv_payment where transaction_id>0 and acc_inv_id=".$id;
  if (count($exist_transaction_ids)>0) {
    $sql_pp.=" and id_acc_inv_payment not in (".implode(',',$exist_transaction_ids).")";
  }
  //echo '<pre>';echo $sql_pp; die(); 
  $result_pp = $db_link->query($sql_pp); 
  if (!$result_pp) {
    debug_mail(false,'error sql',$sql_pp);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  while ($row_pp = $result_pp->fetch_assoc()) {
    $acc_inv_payment_array[]=array(
      'pp'=>intval($row_pp['pp']),
      'rec'=>intval($row_pp['id_acc_inv_payment']), 
      'pid'=> intval($row_pp['payment_acquirer_id']),
      'val'=> floatval($row_pp['poso']),
      'aid'=>intval($row_pp['asset_id']),
    );
  }
  //echo '<pre>bbb '; print_r($acc_inv_payment_array);die();    

}

function gks_acc_inv_acc_inv_payment_part_b() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;  
  global $id;
  global $tropos_pliromis_one_multi;
  global $tropos_pliromis;
  global $acc_inv_payment_array;
  global $not_del_id_acc_inv_payment;
  global $gks_price_total;
  
  if ($tropos_pliromis_one_multi==1) {
    $sum_pp_items=0;
    foreach ($acc_inv_payment_array as &$pp_item) {
      $pp_item['pp']=intval($pp_item['pp']);
      $pp_item['rec']=intval($pp_item['rec']);
      $pp_item['pid']=intval($pp_item['pid']);
      $pp_item['val']=floatval($pp_item['val']);
      $pp_item['aid']=intval($pp_item['aid']);
      $sum_pp_items+=$pp_item['val'];
      if ($pp_item['rec']>0) {
        $not_del_id_acc_inv_payment[]=$pp_item['rec'];
      }
    }
    unset($pp_item);
    
    if (abs($gks_price_total-$sum_pp_items)>=0.01) {
      debug_mail(false,'sum_pp_items error',$sum_pp_items.'!='.$gks_price_total.'<br>'.print_r($acc_inv_payment_array,true));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Το άθροισμα των τρόπων πληρωμών δεν συμφωνεί με το σύνολο του παραστατικού').'<br>'.$sum_pp_items.'&lt;&gt;'.$gks_price_total));
      echo json_encode($return); die();}    
    
    //echo '<pre>'; print_r($acc_inv_payment_array);die();
    
  } else {
    $acc_inv_payment_array[0]['val']=$gks_price_total;
    $not_del_id_acc_inv_payment[]=$acc_inv_payment_array[0]['rec'];
  }
  
  //echo '<pre>'; print_r($acc_inv_payment_array);die();  
}

function gks_acc_inv_acc_inv_payment_part_c() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;  
  global $id;  
  global $tropos_pliromis_one_multi;
  global $tropos_pliromis;
  global $acc_inv_payment_array;
  global $not_del_id_acc_inv_payment;
  
//if ($tropos_pliromis_one_multi==0) {
//  $sql="delete from gks_acc_inv_payment where acc_inv_id=".$id." and transaction_id=0";
//  $result = $db_link->query($sql);  
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die(); }  
//} else {  
  $sql="select id_acc_inv_payment,transaction_id from gks_acc_inv_payment where acc_inv_id=".$id;
  if (count($not_del_id_acc_inv_payment)>0) {
    $sql.=" and id_acc_inv_payment not in (".implode(',', $not_del_id_acc_inv_payment).")";
  }
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $del_id_acc_inv_payment=array();
  while ($row = $result->fetch_assoc()) {
    if (intval($row['transaction_id'])>0) {
      $not_del_id_acc_inv_payment[]=$row['id_acc_inv_payment'];
    } else {
      $del_id_acc_inv_payment[]=$row['id_acc_inv_payment'];
    }
  }
  
  if (count($del_id_acc_inv_payment)>0) {
    $sql="delete from gks_acc_inv_payment 
    where id_acc_inv_payment in (".implode(',',$del_id_acc_inv_payment).")
    and transaction_id=0";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
  foreach ($acc_inv_payment_array as &$pp_item) {
    if ($pp_item['rec']==0) {
      $sql="insert into gks_acc_inv_payment (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_inv_id,pp,payment_acquirer_id,poso,asset_id
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      ".$pp_item['pp'].",
      ".$pp_item['pid'].",
      ".number_format($pp_item['val'],10, '.','').",
      ".$pp_item['aid']."
      )";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }   
      $pp_item['rec']=$db_link->insert_id;
    } else {
      $sql="update gks_acc_inv_payment set 
      pp=".$pp_item['pp'].",
      payment_acquirer_id=".$pp_item['pid'].",
      poso=".number_format($pp_item['val'],10, '.','').",
      asset_id=".$pp_item['aid'].",
      mydate_edit=now(),
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where id_acc_inv_payment=".$pp_item['rec']." and acc_inv_id=".$id."
      and transaction_id=0";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }      
    }        
  }
  unset($pp_item);
  
  //}
}
