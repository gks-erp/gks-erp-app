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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');






$_gks_session['temp_mypropertiesheight'] = 0;
if (isset($_POST['mypropertiesheight'])) $_gks_session['temp_mypropertiesheight']=intval($_POST['mypropertiesheight']); gks_erp_cookie_save();
$mov_state=''; if (isset($_POST['mov_state'])) $mov_state=trim_gks(base64_decode($_POST['mov_state']));

if ($mov_state=='create_acc_pay') {
  //echo '<pre>'.$mov_state.' '.$id; die();
  unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  $id_create_acc_pay=gks_whi_mov_create_acc_pay($id);
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
$mov_state_old='';
$idiotites_old='';
$mov_whi_number_int_old=0;
$mov_whi_number_str_old='';
$mov_whi_ekdosi_date_old='';
$seira_code_old='';
$is_xeirografi_old=0;
$seira_isdeliverynote_old=0;

$myarray_old=array();
$myarray_line_old=array();

$row_old=array();
$products_old=array();
$extra_address_old=array();
$cancel_for_whi_mov_id=0;
$credit_memo_for_whi_mov_id=0;

$all_products_for_balance=array();

$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
  $row_old['mov_state']='010draft';
  $row_old['mov_whi_number_int']=0;
} else {
  $sql=select_gks_whi_mov()." where id_whi_mov=".$id;
  if (count($perm_id_company_ids)>0) $sql.=" and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";

  
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
  $mov_state_old=trim_gks($row_old['mov_state']);
  $idiotites_old=get_whi_mov_details_txt($id, $myarray_old, $myarray_line_old); 
  $mov_whi_number_int_old=$row_old['mov_whi_number_int']; 
  $mov_whi_number_str_old=trim_gks($row_old['mov_whi_number_str']); 
  $mov_whi_ekdosi_date_old=trim_gks($row_old['mov_whi_ekdosi_date']); 
  $seira_code_old=trim_gks($row_old['seira_code']); 
  $is_xeirografi_old=trim_gks($row_old['is_xeirografi']); 
  $seira_isdeliverynote_old=intval($row_old['seira_isdeliverynote']);
  $cancel_for_whi_mov_id=$row_old['cancel_for_whi_mov_id'];
  $credit_memo_for_whi_mov_id=$row_old['credit_memo_for_whi_mov_id'];
  
  $sql="SELECT gks_whi_mov_products.*, gks_monades_metrisis.monada_descr
  FROM gks_whi_mov_products 
  LEFT JOIN gks_monades_metrisis ON gks_whi_mov_products.product_monada_id = gks_monades_metrisis.id_monada
  WHERE gks_whi_mov_products.whi_mov_id=".$id."
  ORDER BY gks_whi_mov_products.product_aa;";
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
  
  $gks_custom_prepare=gks_custom_table_item_prepare('gks_whi_mov',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
  
    
}

$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;
if ($row_old['mov_state']=='040cancelled' or $row_old['mov_state']=='080listing' or $row_old['mov_state']=='090ekdosi' or $row_old['mov_state']=='100closed') {
  $gks_lock=true;
} else {
  if ($row_old['mov_whi_number_int'] > 0 and $row_old['is_xeirografi']==0 and ($row_old['mov_state']=='010draft')) {
    $gks_number_lock=true;
  }
}
if ($credit_memo_for_whi_mov_id!=0) {
  $gks_number_lock=true;
  $gks_user_lock=true;
}
if ($cancel_for_whi_mov_id!=0) {
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
if ($mov_state=='aade_send') {
  $aade_send=true; 
  $mov_state='';
} else if ($mov_state=='paroxos_send') {
  $paroxos_send=true; 
  $mov_state='';
}


$assigned_id=0; if (isset($_POST['assigned_id'])) $assigned_id=intval($_POST['assigned_id']);
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
      debug_mail(false,'channel not found',$sql_channel);
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
  if ($cancel_for_whi_mov_id!=0 && $mov_state=='090ekdosi') { //otan to inv einai akoritiko gia allo
    $mov_whi_seira_id=$row_old['mov_whi_seira_id'];
    $has_ekdosi=false;
    $save_but_message='';
    gks_whi_mov_get_ekdosi_numbers();
    $warning_message=$save_but_message;
    
    if ($has_ekdosi) {
      $sql_ekdosi="mov_whi_number_int=".$mov_whi_number_int_new.",
             mov_whi_number_str='".$db_link->escape_string($mov_whi_number_str_new)."',
             mov_whi_ekdosi_date=now(),
             mov_whi_seira_code='".$db_link->escape_string($mov_whi_seira_code_new)."',";
             
    }  
        
//    $return = array('success' => false, 'message' => base64_encode('<pre>gks_lock:'.$gks_lock.
//     "\ngks_number_lock:".$gks_number_lock.
//     "\nmov_state:".$mov_state.
//     "\nhas_ekdosi:".$has_ekdosi.
//     "\nmov_whi_number_int_new:".$mov_whi_number_int_new.
//     "\nmov_whi_number_str_new:".$mov_whi_number_str_new.
//     "\nmov_whi_seira_code_new:".$mov_whi_seira_code_new.
//     "\nsql_ekdosi:".$sql_ekdosi
//    ));echo json_encode($return); die();    
//    //kostas
    
  
    
  } else {
    if ($mov_state!='' and $mov_state!='010draft' and $mov_state!='040cancelled' and $mov_state!='credit_memo')  {
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος κατάσταση').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die(); }
  }
  
  
  if ($mov_state=='040cancelled') {
    $sql_canceled="SELECT id_whi_mov, cancel_for_whi_mov_id FROM gks_whi_mov WHERE cancel_for_whi_mov_id=".$id;
    $result_canceled = $db_link->query($sql_canceled);  
    if (!$result_canceled) {
      debug_mail(false,'error sql',$sql_canceled);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_canceled->num_rows>=1) {
      $row_canceled=$result_canceled->fetch_assoc();
      if ($row_canceled['cancel_for_whi_mov_id']!=0) {
        $message=gks_lang('Για αυτό το δελτίο υπάρχει ήδη το ακυρωτικό δελτίο με').' <br>ID :<b>'.$row_canceled['id_whi_mov'].'</b><br>'.
        '<a class="gks_link" href="admin-whi-mov-item.php?id='.$row_canceled['id_whi_mov'].'">'.gks_lang('Προβολή').'</a>';
        debug_mail(false,'cancel_for_whi_mov_id='.$row_canceled['cancel_for_whi_mov_id'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die(); }
    }
    $sql_credit_memo="SELECT id_whi_mov, credit_memo_for_whi_mov_id FROM gks_whi_mov WHERE credit_memo_for_whi_mov_id=".$id;
    //die($sql_credit_memo);
    $result_credit_memo = $db_link->query($sql_credit_memo);  
    if (!$result_credit_memo) {
      debug_mail(false,'error sql',$sql_credit_memo);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_credit_memo->num_rows>=1) {
      $row_credit_memo=$result_credit_memo->fetch_assoc();
      if ($row_credit_memo['credit_memo_for_whi_mov_id']!=0) {
        $message=gks_lang('Για αυτό το δελτίο υπάρχει ήδη το συσχετιζόμενο δελτίο επιστροφής με').' <br>ID :<b>'.$row_credit_memo['id_whi_mov'].'</b><br>'.
        '<a class="gks_link" href="admin-whi-mov-item.php?id='.$row_credit_memo['id_whi_mov'].'">'.gks_lang('Προβολή').'</a>';
        debug_mail(false,'credit_memo_for_whi_mov_id='.$row_credit_memo['credit_memo_for_whi_mov_id'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die(); }
    }
    
  }
  
  
  //echo '<pre>'.$mov_state; die();
  
  if ($mov_state=='010draft' and $mov_state_old!='010draft' and $is_xeirografi_old==0 and $mov_whi_number_int_old>0) {
    //echo '<pre>vvv';die();
    $warning_message=gks_whi_mov_to_draft($id);
    if ($warning_message!='') 
      $warning_message=gks_lang('Έγινε επαναφορά σε <b>Πρόχειρο</b> αλλά δεν μπόρεσε μηδενιστεί ο αριθμός του δελτίου διότι').
        ':<br>'.
        $warning_message.'<br>'.
        gks_lang('Κάντε άμεσα τις αλλαγές και ξανα εκδώστε το').'<br>'.
        gks_lang('Διαφορετικά θα δημιουργηθεί κενό στην αρίθμηση της σειράς');
  }
  //print '<pre>'.$warning_message;die();
  
  $note_doc=''; if (isset($_POST['note_doc'])) $note_doc=trim_gks(base64_decode($_POST['note_doc']));
  $note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));

  $tropos_apostolis=0; if (isset($_POST['tropos_apostolis'])) $tropos_apostolis=intval($_POST['tropos_apostolis']);  
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
  
  
  if ($mov_state=='040cancelled') gks_whi_mov_cancel_create($id,true);
  if ($mov_state=='credit_memo') gks_whi_mov_credit_memo_create($id,true);

  
  
  $gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_whi_mov');
    
  
  $sql="update gks_whi_mov set ";
  if ($mov_state!= '' and $mov_state!='credit_memo') {
    $sql.="mov_state='".$db_link->escape_string($mov_state)."', ";
  }
  
  $sql.=$sql_ekdosi;
  
  $sql.="
  note_doc='".$db_link->escape_string($note_doc)."',
  note_logistirio='".$db_link->escape_string($note_logistirio)."',
  tropos_apostolis=".$tropos_apostolis.",
  delivery_id_8=".$delivery_id_8.",
  delivery_number='".$db_link->escape_string($delivery_number)."',
  vehicle_number='".$db_link->escape_string($vehicle_number)."',
  dispatch_date=".($dispatch_date == '' ? 'null' : "'".$db_link->escape_string($dispatch_date)."'") .", 
  dispatch_time=".($dispatch_time == '' ? 'null' : "'".$db_link->escape_string($dispatch_time)."'") .", 
  kostos_apostolis=".number_format($kostos_apostolis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').", ";



  $sql.="assigned_id=".$assigned_id.",";
  if ($GKS_CRM_ENABLE) {
  $sql.=
  "crm_channel_id=".$crm_channel_id.",
  crm_channel_contact_id=".$crm_channel_contact_id.",
  crm_channel_campain_id=".$crm_channel_campain_id.",
  crm_channel_url=". ($crm_channel_url =='' ? 'null' : "'".$db_link->escape_string($crm_channel_url)."'").",
  crm_channel_code=". ($crm_channel_code =='' ? 'null' : "'".$db_link->escape_string($crm_channel_code)."'").",
  crm_channel_text=". ($crm_channel_text =='' ? 'null' : "'".$db_link->escape_string($crm_channel_text)."'").",";
  }
  
  

  
  $sql.="user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."',
  session_id='".$_gks_id_session."'
  where id_whi_mov = ".$id." limit 1";  
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
    if ($sql!='') {
      $sql=substr($sql, 0, strlen($sql)-1);
      $sql="update gks_whi_mov set ".$sql." where id_whi_mov=".$id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
    }

  }
  
  $gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


//  $return = array('success' => false, 'message' => base64_encode('<pre>gks_lock:'.$gks_lock.
//   "\ngks_number_lock:".$gks_number_lock.
//   "\nmov_state:".$mov_state.
//   "\ncancel_for_whi_mov_id:".$cancel_for_whi_mov_id
//  ));echo json_encode($return); die();
  
  


  

  $sql="update gks_whi_mov_products set p_mov_state='".$db_link->escape_string($mov_state!='' ? $mov_state : $mov_state_old)."' where whi_mov_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}    
  
  gks_whi_mov_balance_calc($all_products_for_balance);
  gks_whi_after_balance_for_whi($id);
  
  
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

    
    $force_options['doc_table']='gks_whi_mov';
    $ret=gks_aade_invoice($id,$force_options);
    
    
    //echo '<pre>';print_r($ret);die();
    
    $row_old['aade_errors']='';
    $myparams=[];
    if ($ret['success']==false and $ret['message']!='') $myparams['ret_aade_errors']=$ret['message'];
    gks_whi_mov_sxolio_log($id,$row_old,$products_old,$extra_address_old,($ret['mydata_live']==0 ? gks_lang('Δοκιμαστική αποστολή σε myData').'<br>' : gks_lang('Πραγματική αποστολή σε myData').'<br>'),$myparams,$gks_custom_row_old);
    
    $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
    
    //print '<pre>'; print_r($ret); die(); 
    if ($ret['success']==false) {
      $return = array('success' => true, 'message' => base64_encode('error'),'redirect'=> base64_encode('admin-whi-mov-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode($ret['message']));
      echo json_encode($return); die();
    } else {
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> base64_encode('admin-whi-mov-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode(gks_lang('Επιτυχής αποστολή δεδομένων myData στην ΑΑΔΕ')));
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
    $force_options['doc_table']='gks_whi_mov';
    $ret=gks_paroxos_invoice($id,$force_options);
    
    $row_old['aade_errors']='';
    $myparams=[];
    if ($ret['success']==false and $ret['message']!='') $myparams['ret_aade_errors']=$ret['message'];
    gks_whi_mov_sxolio_log($id,$row_old,$products_old,$extra_address_old,($paroxos_mydata_live==false ? gks_lang('Δοκιμαστική αποστολή σε πάροχο').'<br>' : gks_lang('Πραγματική αποστολή σε πάροχο').'<br>'),$myparams,$gks_custom_row_old);
    
    $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
    
    //print '<pre>'; print_r($ret); die(); 
    if ($ret['success']==false) {
      $return = array('success' => true, 'message' => base64_encode('error'),'redirect'=> base64_encode('admin-whi-mov-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode($ret['message']));
      echo json_encode($return); die();
    } else {
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> base64_encode('admin-whi-mov-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode($ret['save_but_message']));
      echo json_encode($return); die();      
    }    
  } else {
    gks_whi_mov_sxolio_log($id,$row_old,$products_old,$extra_address_old,'',[],$gks_custom_row_old);
  }  
  
  
  if ($mov_state=='040cancelled') {
    $id_canceled=gks_whi_mov_cancel_create($id,false);
    if ($id_canceled>0) {
      //echo '<pre>'.$mov_state.' '.$id_canceled; die();
      
      $message=gks_lang('Το ακυρωτικό δελτίο έχει δημιουργηθεί').'<br>'.
      gks_lang('To ID του είναι').' <b>'.$id_canceled.'</b><br>'.
      gks_lang('Θα πρέπει να το ελέγξετε και να το εκδώσετε').'<br>'.
      '<a class="gks_link" href="admin-whi-mov-item.php?id='.$id_canceled.'">'.gks_lang('Προβολή').'</a>';
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode($message));
      echo json_encode($return); die();
      
    } else {
      debug_mail(false,'error gks_whi_mov_cancel_create',$id.' '.$id_canceled);
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();
    }
  }
  
  if ($mov_state=='credit_memo') {
    $id_credit_memo=gks_whi_mov_credit_memo_create($id,false);
    if ($id_credit_memo>0) {
      //echo '<pre>'.$mov_state.' '.$id_credit_memo; die();
      $message=gks_lang('Το δελτίο επιστροφής έχει δημιουργηθεί').'<br>'.
      gks_lang('To ID του είναι').' <b>'.$id_credit_memo.'</b><br>'.
      gks_lang('Θα πρέπει να το ελέγξετε και να το εκδώσετε').'<br>'.
      '<a class="gks_link" href="admin-whi-mov-item.php?id='.$id_credit_memo.'">'.gks_lang('Προβολή').'</a>';
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode($message));
      echo json_encode($return); die();
      
    } else {
      debug_mail(false,'error gks_whi_mov_credit_memo_create',$id.' '.$id_credit_memo);
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();
    }
    
    
  }
  
  
  //echo $mov_state;die();
  
  $return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> '','save_but_message' => base64_encode($warning_message));
  echo json_encode($return); die();
  
  
}
    




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
  
$mov_whi_journal_id=0;if (isset($_POST['mov_whi_journal_id'])) $mov_whi_journal_id=intval($_POST['mov_whi_journal_id']);
if ($gks_number_lock) $mov_whi_journal_id=$row_old['mov_whi_journal_id'];
if ($mov_whi_journal_id<=0) {
  debug_mail(false,'mov_whi_journal_id is not found',$mov_whi_journal_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Ημερολόγιο')));
  echo json_encode($return); die();}  
  
$mov_whi_seira_id=0;if (isset($_POST['mov_whi_seira_id'])) $mov_whi_seira_id=intval($_POST['mov_whi_seira_id']);
if ($gks_number_lock) $mov_whi_seira_id=$row_old['mov_whi_seira_id'];
if ($mov_whi_seira_id<=0) {
  debug_mail(false,'mov_whi_seira_id is not found',$mov_whi_seira_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Σειρά')));
  echo json_encode($return); die();}  

$reverse_delivery_purpose=0;if (isset($_POST['reverse_delivery_purpose'])) $reverse_delivery_purpose=intval($_POST['reverse_delivery_purpose']);
if ($gks_number_lock) $reverse_delivery_purpose=$row_old['reverse_delivery_purpose'];



//echo '<pre>'.$reverse_delivery_purpose;die();

$mov_whi_number_int_user=0;if (isset($_POST['mov_whi_number_int'])) $mov_whi_number_int_user=intval($_POST['mov_whi_number_int']);



$sql="SELECT gks_acc_seires.id_acc_seira,gks_acc_seires.is_xeirografi,
gks_acc_seires.seira_isdeliverynote,
gks_acc_seires.seira_is_reverse_delivery_note
FROM (((gks_acc_seires 
LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE eidos_parastatikou_type_id in (21,22,23,24) AND id_acc_eidos_parastatikou not in (702,703,704)
AND gks_acc_seires.is_disable=0 AND gks_acc_journal.is_disable=0 
AND gks_company.company_disable=0 
AND gks_acc_seires.id_acc_seira=".$mov_whi_seira_id." 
AND gks_acc_journal.id_acc_journal=".$mov_whi_journal_id." 
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
  debug_mail(false,'company-journal-series not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο συνδυασμός εταιρείας/υποκαταστήματος, ημερολογίου και σειράς')));
  echo json_encode($return); die();}
$row_seira = $result->fetch_assoc();
$is_xeirografi=$row_seira['is_xeirografi'];
$seira_isdeliverynote=intval($row_seira['seira_isdeliverynote']);
$seira_is_reverse_delivery_note=intval($row_seira['seira_is_reverse_delivery_note']);


if ($mov_state=='010draft' and $mov_state_old!='010draft' and $is_xeirografi_old==0 and $mov_whi_number_int_old>0) {
  //echo '<pre>vvv';die();
  gks_whi_mov_to_draft($id);
}



if ($mov_state=='080listing' and $is_xeirografi==0) {
  debug_mail(false,'not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η σειρά είναι χειρόγραφη, άρα το δελτίο θα πρέπει να εκδοθεί και όχι να καταχωρηθεί')));
  echo json_encode($return); die();}    
  
if ($mov_state=='090ekdosi' and $is_xeirografi!=0) {
  debug_mail(false,'not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η σειρά είναι μηχανογραφημένη, άρα το δελτίο θα πρέπει να καταχωρηθεί και όχι να εκδοθεί')));
  echo json_encode($return); die();}
  
//if ($mov_state=='080listing' and $mov_whi_number_int_old>0) {
//  debug_mail(false,'deltio issue. Refresh','');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το δελτίο έχει ήδη καταχωρηθεί').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
//  echo json_encode($return); die();}
//  
//if ($mov_state=='090ekdosi' and $mov_whi_number_int_old>0) {
//  debug_mail(false,'deltio issue. Refresh','');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το δελτίο έχει ήδη εκδοθεί').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
//  echo json_encode($return); die();}
  
if ($seira_is_reverse_delivery_note==0) {
  $reverse_delivery_purpose=0;
} else {
  if ($reverse_delivery_purpose==0) {
    debug_mail(false,'reverse_delivery_purpose is not found',$reverse_delivery_purpose);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Αιτία Αντίστροφης Διακίνησης')));
    echo json_encode($return); die();
  }
}


$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);




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

if ($_POST['mov_date'] == '__/__/____ __:__') $_POST['mov_date']='';
$mov_date=trim_gks(stripslashes(urldecode($_POST['mov_date'])));
if ($mov_date!='') {
  $mov_date = mystrtodb($mov_date);
}

$note_doc=''; if (isset($_POST['note_doc'])) $note_doc=trim_gks(base64_decode($_POST['note_doc']));
$note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));

$tropos_apostolis=0; if (isset($_POST['tropos_apostolis'])) $tropos_apostolis=intval($_POST['tropos_apostolis']);  
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


$aade_skopos_diakinisis_id=0; if (isset($_POST['aade_skopos_diakinisis_id'])) $aade_skopos_diakinisis_id=intval($_POST['aade_skopos_diakinisis_id']);  
$aade_skopos_19_descr=''; if (isset($_POST['aade_skopos_19_descr'])) $aade_skopos_19_descr=trim_gks(base64_decode($_POST['aade_skopos_19_descr']));
//echo '<pre>ssssssssssss '.$aade_skopos_19_descr;die();
if ($aade_skopos_diakinisis_id!=22) $aade_skopos_19_descr='';

$fiscal_position_id=0; if (isset($_POST['fiscal_position_id'])) $fiscal_position_id=intval($_POST['fiscal_position_id']);  
if ($gks_user_lock) $fiscal_position_id=$row_old['fiscal_position_id'];

$pricelist_id=0; if (isset($_POST['pricelist_id'])) $pricelist_id=intval($_POST['pricelist_id']);  
if ($gks_user_lock) $pricelist_id=$row_old['pricelist_id'];

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

if (count($eidi_array)==0 and ($mov_state=='080listing' or $mov_state=='090ekdosi')) {
  debug_mail(false,'products not found',print_r($eidi_array,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχετε προσθέσει είδη')));
  echo json_encode($return); die();}

$eidos_parastatikou_type_id=0;
$eidos_parastatikou_type_id_org=0;
$eidos_parastatikou_stock_pros=0;
$eidos_parastatikou_stock_pros_org=0;
$acc_eidos_parastatikou_other_entity=0;
$journal_has_correlated_invoices=0;
$journal_has_multiple_connected_marks=0;
$journal_has_packings_declarations=0;

//die('<pre>|'.$gks_lock.'|'.$gks_number_lock.'|'.$gks_user_lock.'|');

if ($mov_whi_journal_id>0) {
  $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,eidos_parastatikou_stock_pros,
  gks_acc_journal.acc_eidos_parastatikou_other_entity,
  gks_acc_journal.journal_has_correlated_invoices,
  gks_acc_journal.journal_has_multiple_connected_marks,
  gks_acc_journal.journal_has_packings_declarations
  FROM gks_acc_journal 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
  WHERE gks_acc_journal.id_acc_journal=".$mov_whi_journal_id." and eidos_parastatikou_type_id>0";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $eidos_parastatikou_type_id=$row['eidos_parastatikou_type_id'];
    $eidos_parastatikou_type_id_org=$eidos_parastatikou_type_id;
    $eidos_parastatikou_stock_pros=$row['eidos_parastatikou_stock_pros'];
    $eidos_parastatikou_stock_pros_org=$eidos_parastatikou_stock_pros;
    $acc_eidos_parastatikou_other_entity=intval($row['acc_eidos_parastatikou_other_entity']);
    $journal_has_correlated_invoices=intval($row['journal_has_correlated_invoices']);
    $journal_has_multiple_connected_marks=intval($row['journal_has_multiple_connected_marks']);
    $journal_has_packings_declarations=intval($row['journal_has_packings_declarations']);
  }
  //echo '<pre>';print_r($row);die();
}

if ($credit_memo_for_whi_mov_id>0) {
  $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,eidos_parastatikou_stock_pros
  FROM (gks_whi_mov LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
  WHERE gks_whi_mov.id_whi_mov=".$credit_memo_for_whi_mov_id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $eidos_parastatikou_type_id=$row['eidos_parastatikou_type_id'];
    $eidos_parastatikou_stock_pros=$row['eidos_parastatikou_stock_pros'];
  } else {
    debug_mail(false,'eidos_parastatikou_type_id empty for credit_memo_for_whi_mov_id',$id.' '.$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το συσχετιζόμενο δελτίο')));
    echo json_encode($return); die();
    
  }
}

if ($eidos_parastatikou_type_id<=0) {
  debug_mail(false,'eidos_parastatikou_type_id empty',$mov_whi_journal_id.' '.$sql);
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
  debug_mail(false,'aade_skopos_diakinisis_id empty','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον σκοπό διακίνησης')));
  echo json_encode($return); die();}

if ($aade_skopos_diakinisis_id==22 and $aade_skopos_19_descr=='') {
  debug_mail(false,'aade_skopos_diakinisis_id empty','');
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
//echo '<pre>'.$gks_number_lock.'|'.$warehouses_id_from.'|'.$warehouses_id_to.'|'.$company_id.'|'.$company_sub_id.'|'.time();die();

$warehouses_id_from_is_virtual=false;
$warehouses_id_to_is_virtual=false;
if ($eidos_parastatikou_type_id_org==24) { //apografi
  $warehouses_id_from=0;  
  $aade_skopos_diakinisis_id=0;
  $aade_skopos_19_descr='';
  $pricelist_id=0;
  $fiscal_position_id=0;
  $tropos_apostolis=1; //no delinery need
  
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
  
  
  
  
} else if ($eidos_parastatikou_type_id_org==23) { //endodiakinisi
  
} else {
  if ($eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
    if ($eidos_parastatikou_type_id_org==21) { //pelates
      $warehouses_id_from=1; //pelates virtual
      $warehouses_id_from_is_virtual=true;
    } else if ($eidos_parastatikou_type_id_org==22) { //promitheuton
      $warehouses_id_from=2; //promitheutes virtual
      $warehouses_id_from_is_virtual=true;
    }
  } else if ($eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
    if ($eidos_parastatikou_type_id_org==21) { //pelates
      $warehouses_id_to=1; //virtual pelaton
      $warehouses_id_to_is_virtual=true;
    } else if ($eidos_parastatikou_type_id_org==22) { //promitheuton
      $warehouses_id_to=2; //virtua promitheuton
      $warehouses_id_to_is_virtual=true;
    }
  }
}



if ($warehouses_id_from>0 and $warehouses_id_from_is_virtual==false) { //ektos virtual
  $sql="select * from gks_warehouses where is_virtual=0 and id_warehouse=".$warehouses_id_from;
  if ($eidos_parastatikou_type_id!=23) { //not endodiakinisi
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
  //echo '<pre>';echo $sql;die();
  if ($eidos_parastatikou_type_id!=23) { //not endodiakinisi
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


//echo '<pre>'.$gks_number_lock.'|'.$warehouses_id_from.'|'.$warehouses_id_to.'|'.$company_id.'|'.$company_sub_id.'|'.time();die();

if ($eidos_parastatikou_type_id==23 or $eidos_parastatikou_type_id==24) { //endodiakinisi, apografi
  $user_id=0;
  $dr_user_first_name=''; 
  $dr_user_last_name='';
  $dr_user_email='';
  $dr_user_mobile='';
  $dr_user_lang='';
  $dr_user_ma_odos='';
  $dr_user_ma_arithmos='';
  $dr_user_ma_orofos='';
  $dr_user_ma_perioxi='';
  $dr_user_ma_poli='';
  $dr_user_ma_tk='';
  $dr_user_ma_country_id=0;
  $dr_user_ma_nomos_id=0;
  $dr_user_eponimia='';
  $dr_user_title='';
  $dr_user_afm='';
  $dr_user_doy='';
  $dr_user_epaggelma='';
  
  $form_select_apostoli=-1;
  $form_ea_name='';
  $form_ea_phone='';
  $form_ea_odos='';
  $form_ea_arithmos='';
  $form_ea_orofos='';
  $form_ea_perioxi='';
  $form_ea_poli='';
  $form_ea_tk='';
  $form_ea_country_id=0; 
  $form_ea_nomos_id=0;
  
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
  
}


if ($eidos_parastatikou_type_id==21 or $eidos_parastatikou_type_id==22) { //deltio apostoli kaiparalavis
  if ($user_id<=0) {
    debug_mail(false,'select counterpart','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή (πελάτη/προμηθευτή)')));
    echo json_encode($return); die();}
  
  if ($warehouses_id_from<=0) {
    debug_mail(false,'select warehouse from','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
    echo json_encode($return); die();}
  
  if ($warehouses_id_to<=0) {
    debug_mail(false,'select warehouse to','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
    echo json_encode($return); die();}
}
  
if ($eidos_parastatikou_type_id==23) { //endodiakinisi
  if ($warehouses_id_from<=0) {
    debug_mail(false,'select warehouse endodia','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
    echo json_encode($return); die();}

  if ($warehouses_id_to<=0) {
    debug_mail(false,'select warehouse to','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
    echo json_encode($return); die();}
}
  

if ($eidos_parastatikou_type_id==24) { //apografi
  if ($warehouses_id_to<=0) {
    debug_mail(false,'select warehouse apografi','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη που <b>Αφορά</b> η απογραφή')));
    echo json_encode($return); die();}
}



if ($warehouses_id_from==$warehouses_id_to) {
  debug_mail(false,'warehouse from must <> warehouse to','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η αποθήκη <b>Από</b> πρέπει να είναι διαφορετική από την αποθήκη <b>Προς</b>')));
  echo json_encode($return); die();}    

     




if (1==2) {
  $dr_user_eponimia=''; 
  $dr_user_title='';
  $dr_user_afm='';
  $dr_user_doy='';
  $dr_user_epaggelma='';
}  
//print '<pre>';print $eidos_parastatikou_type_id;die();


if ($gks_user_lock==false) {
  if ($form_select_apostoli==-1) { //send same addreessss
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

$not_del_id_whi_mov_other_entity=array();
if ($acc_eidos_parastatikou_other_entity==0) $other_entity_array=array();
$other_entity_cc=0;
foreach ($other_entity_array as &$other_entity_item) {
  $other_entity_cc++;
  if ($other_entity_item['aade_entitytype_id']<=0) {
    debug_mail(false,'other_entity_item aade_entitytype_id' . $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]', gks_n_h($other_entity_cc),gks_lang('Επιλέξτε τον τύπο στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['entity_user_id']<=0) {
    debug_mail(false,'other_entity_item entity_user_id ' . $other_entity_cc ,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]', gks_n_h($other_entity_cc), gks_lang('Επιλέξτε τον συσχετιζόμενο στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['address_extra']==0 or $other_entity_item['address_extra']<-1) {
    debug_mail(false,'other_entity_item address_extra ' . $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(tr_replace('[n]', gks_n_h($other_entity_cc), gks_lang('Επιλέξτε το υποκατάστημα στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή'))));
    echo json_encode($return); die(); }    
  
  $other_entity_item['ret']=gks_other_entity_get_data('gks_whi_mov',-1,$other_entity_item['entity_user_id'],$other_entity_item['address_extra']);
  
  if ($other_entity_item['ret']['data']['afm']=='') {
    debug_mail(false,'other_entity_item other_entity_item ' . $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]', gks_n_h($other_entity_cc),gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει ΑΦΜ'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['ret']['data']['country_initials']=='') {
    debug_mail(false, 'other_entity_item country_initials' . $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]', gks_n_h($other_entity_cc),gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Χώρα'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['ret']['data']['country_initials']=='') {
    debug_mail(false, 'other_entity_item country_initials ' . $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]', gks_n_h($other_entity_cc),gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Χώρα'))));
    echo json_encode($return); die(); }    
  if ($other_entity_item['ret']['data']['branch']=='') {
    debug_mail(false,'other_entity_item branch ' . $other_entity_cc,print_r($other_entity_item,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[n]', gks_n_h($other_entity_cc),gks_lang('Ο Συσχετιζόμενος στο <b>Λοιπoί Συσχετιζόμενοι ΑΦΜ</b> στην [n] γραμμή δεν έχει Αριθμό Εγκατάστασης'))));
    echo json_encode($return); die(); }    

  $recid=intval($other_entity_item['recid']);
  if ($recid>0) $not_del_id_whi_mov_other_entity[] = $recid;
}
unset($other_entity_item);
//print '<pre>';var_dump($other_entity_array);die();
//print '<pre>sssss sssssss '."\n";print_r($other_entity_array);die();



$not_del_id_whi_mov_correlated_invoices=array();
if ($journal_has_correlated_invoices==0) $correlated_invoices_array=array();
$correlated_invoices_cc=0;
foreach ($correlated_invoices_array as &$correlated_invoices_item) {
  $correlated_invoices_cc++;

  $recid=intval($correlated_invoices_item['recid']);
  if ($recid>0) $not_del_id_whi_mov_correlated_invoices[] = $recid;
}
unset($correlated_invoices_item);
//print '<pre>';var_dump($correlated_invoices_array);die();
//print '<pre>sssss sssssss '."\n";print_r($correlated_invoices_array);die();

$not_del_id_whi_mov_multiple_connected_marks=array();
if ($journal_has_multiple_connected_marks==0) $multiple_connected_marks_array=array();
$multiple_connected_marks_cc=0;
foreach ($multiple_connected_marks_array as &$multiple_connected_marks_item) {
  $multiple_connected_marks_cc++;

  $recid=intval($multiple_connected_marks_item['recid']);
  if ($recid>0) $not_del_id_whi_mov_multiple_connected_marks[] = $recid;
}
unset($multiple_connected_marks_item);
//print '<pre>';var_dump($multiple_connected_marks_array);die();
//print '<pre>sssss sssssss '."\n";print_r($multiple_connected_marks_array);die();

$not_del_id_whi_mov_packings_declarations=array();
if ($journal_has_packings_declarations==0) $packings_declarations_array=array();
$packings_declarations_cc=0;
foreach ($packings_declarations_array as &$packings_declarations_item) {
  $packings_declarations_cc++;
  $recid=intval($packings_declarations_item['recid']);
  if ($recid>0) $not_del_id_whi_mov_packings_declarations[] = $recid;
}
unset($packings_declarations_item);
//print '<pre>';var_dump($packings_declarations_array);die();
//print '<pre>sssss sssssss '."\n";print_r($packings_declarations_array);die();

$not_del_id_whi_mov_product=array();
$orders_products=array();
$all_products_for_balance=array();
foreach ($eidi_array as $eidi_array_item) {
  $id_whi_mov_product=intval($eidi_array_item['id_whi_mov_product']);
  $product_aa=intval($eidi_array_item['aa']);
  $product_id=intval($eidi_array_item['product_id']);
  if ($eidos_parastatikou_type_id==24) { //apografi
    $product_quantity=0;
    $apografi_posotitaonhand=floatval($eidi_array_item['product_quantity']);
  } else {
    $product_quantity=floatval($eidi_array_item['product_quantity']);
    $apografi_posotitaonhand=null;
  }
  $product_monada_id=intval($eidi_array_item['product_monada_id']);
  $product_descr=trim_gks($eidi_array_item['product_descr']);
  $product_comments=trim_gks($eidi_array_item['product_comments']);
  
  $product_lots_serials=array();
  if ($GKS_PRODUCT_LOTS_SERIALS) {
    foreach($eidi_array_item['product_lots_serials'] as $lot_product_item) {
      $lot_name=trim_gks($lot_product_item['lot_name']);
      if ($eidos_parastatikou_type_id==24) { //apografi
        $lot_product_quantity=0;
        $apografi_lot_posotitaonhand=floatval($lot_product_item['lot_product_quantity']);
      } else {
        $lot_product_quantity=floatval($lot_product_item['lot_product_quantity']);
        $apografi_lot_posotitaonhand=null;
      }
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
      
      
      if ($lot_name!='') { //and $lot_product_quantity!=0
        $product_lots_serials[]=array(
          'lot_product_id'=>$lot_product_id,
          'lot_name'=>$lot_name,
          'lot_product_quantity'=>$lot_product_quantity,
          'apografi_lot_posotitaonhand'=>$apografi_lot_posotitaonhand,
          'lot_descr'=>$lot_descr,
          'lot_date_production'=>$lot_date_production,
          'lot_date_expire'=>$lot_date_expire,
        );
      }
    }
  }
  
  if ($id_whi_mov_product>0) $not_del_id_whi_mov_product[] = $id_whi_mov_product;
    
  $orders_products[]=array(
    'id_whi_mov_product' => $id_whi_mov_product,
    'product_aa' => $product_aa,
    'product_id' => $product_id,
    'product_quantity' => $product_quantity,
    'apografi_posotitaonhand' => $apografi_posotitaonhand,
    'product_monada_id' => $product_monada_id,
    'product_descr' => $product_descr,
    'product_comments' => $product_comments,
    'product_lots_serials' => $product_lots_serials,

  );  
    
  $all_products_for_balance[]=$product_id;
    

      
}

if ($eidos_parastatikou_type_id==24) { //apografi
  $mybal = gks_whi_mov_balance_calc($all_products_for_balance,$mov_date);
  //print '<pre>';print_r($mybal);die();
  foreach ($orders_products as &$my_product) {
    $curr_balance=0;
    if (isset($mybal[$my_product['product_id']]) and isset($mybal[$my_product['product_id']]['warehouses'][$warehouses_id_to])) {
      $diafora = $my_product['apografi_posotitaonhand'] - $mybal[$my_product['product_id']]['warehouses'][$warehouses_id_to]['bal'];
      $my_product['product_quantity']=$diafora;
    } else {
      $my_product['product_quantity']=$my_product['apografi_posotitaonhand'];
    }
  }
  unset($my_product);
  
  //print '<pre>';print_r($orders_products);die();
  
  
  $mybal = gks_whi_mov_lots_serials_balance_calc($all_products_for_balance,$mov_date);

  //print '<pre>';print_r($mybal);die();
  
  foreach ($orders_products as &$my_product) {
    foreach ($my_product['product_lots_serials'] as &$lot_product_item) {
      
      $curr_balance=0;
      if (isset($mybal[$lot_product_item['lot_product_id']]) and isset($mybal[$lot_product_item['lot_product_id']]['warehouses'][$warehouses_id_to])) {
        $diafora = $lot_product_item['apografi_lot_posotitaonhand'] - $mybal[$lot_product_item['lot_product_id']]['warehouses'][$warehouses_id_to]['bal'];
        $lot_product_item['lot_product_quantity']=$diafora;
      } else {
        $lot_product_item['lot_product_quantity']=$lot_product_item['apografi_lot_posotitaonhand'];
      }
    }
    unset($lot_product_item);
    
  }
  unset($my_product);
    
  //print '<pre>';print_r($orders_products);die();
  
}


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_whi_mov');


$redirect='';

if ($id==-1) {
  $mov_guid=guid_for_whi_mov();
  $bank_deposit_9digit=gks_get_bank_deposit_9digit();
  $sql="insert into gks_whi_mov (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,mov_guid,bank_deposit_9digit
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  '".$db_link->escape_string($mov_guid)."','".$db_link->escape_string($bank_deposit_9digit)."')";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }  
  $id = $db_link->insert_id;
  $redirect=base64_encode('admin-whi-mov-item.php?id='.$id);  
  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_whi_mov_log (whi_mov_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
} 

$sql="delete from gks_whi_mov_other_entity where whi_mov_id=".$id;
if (count($not_del_id_whi_mov_other_entity)>0) {
  $sql.=" and id_whi_mov_other_entity not in (".implode(',', $not_del_id_whi_mov_other_entity).")";
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
    $sql="insert into gks_whi_mov_other_entity (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      whi_mov_id,
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
    $sql="update gks_whi_mov_other_entity set 
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
    where id_whi_mov_other_entity=".$myrec['recid']."
    and whi_mov_id=".$id;  
    //echo '<pre>'.$sql; die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
  
}

$sql="delete from gks_whi_mov_correlated_invoices where whi_mov_id=".$id;
if (count($not_del_id_whi_mov_correlated_invoices )>0) {
  $sql.=" and id_whi_mov_correlated_invoices not in (".implode(',', $not_del_id_whi_mov_correlated_invoices).")";
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
    //echo '<pre>';print_r($myrec);die();
  }
  
  if ($myrec['recid']<=0) {
    $sql="insert into gks_whi_mov_correlated_invoices (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      whi_mov_id,
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
    $sql="update gks_whi_mov_correlated_invoices set 
    coi_mark='".$db_link->escape_string($myrec['coi_mark'])."',
    coi_acc_inv_id=".$myrec['coi_acc_inv_id'].",
    coi_acc_pay_id=".$myrec['coi_acc_pay_id'].",
    coi_whi_mov_id=".$myrec['coi_whi_mov_id'].",
    coi_aa=".$myrec['coiaa'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_whi_mov_correlated_invoices=".$myrec['recid']."
    and whi_mov_id=".$id;  
    //echo '<pre>'.$sql; die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
}


$sql="delete from gks_whi_mov_multiple_connected_marks where whi_mov_id=".$id;
if (count($not_del_id_whi_mov_multiple_connected_marks )>0) {
  $sql.=" and id_whi_mov_multiple_connected_marks not in (".implode(',', $not_del_id_whi_mov_multiple_connected_marks).")";
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
    //echo '<pre>';print_r($myrec);die();
  }
  
  if ($myrec['recid']<=0) {
    $sql="insert into gks_whi_mov_multiple_connected_marks (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      whi_mov_id,
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
    $sql="update gks_whi_mov_multiple_connected_marks set 
    mcm_mark='".$db_link->escape_string($myrec['mcm_mark'])."',
    mcm_acc_inv_id=".$myrec['mcm_acc_inv_id'].",
    mcm_acc_pay_id=".$myrec['mcm_acc_pay_id'].",
    mcm_whi_mov_id=".$myrec['mcm_whi_mov_id'].",
    mcm_aa=".$myrec['mcmaa'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_whi_mov_multiple_connected_marks=".$myrec['recid']."
    and whi_mov_id=".$id;  
    //echo '<pre>'.$sql; die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
}

$sql="delete from gks_whi_mov_packings_declarations where whi_mov_id=".$id;
if (count($not_del_id_whi_mov_packings_declarations )>0) {
  $sql.=" and id_whi_mov_packings_declarations not in (".implode(',', $not_del_id_whi_mov_packings_declarations).")";
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
    $sql="insert into gks_whi_mov_packings_declarations (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      whi_mov_id,
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
    $sql="update gks_whi_mov_packings_declarations set 
    packaging_type_id=".$myrec['pde_type_id'].",
    packaging_type_6_descr='".$db_link->escape_string($myrec['pde_type_6_descr'])."',
    packaging_quantity=".$myrec['pde_quantity'].",
    packaging_aa=".$myrec['pdeaa'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_whi_mov_packings_declarations=".$myrec['recid']."
    and whi_mov_id=".$id;  
    //echo '<pre>'.$sql; die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
}




$sql="select id_whi_mov_product from gks_whi_mov_products where whi_mov_id=".$id;
if (count($not_del_id_whi_mov_product)>0) {
  $sql.=" and id_whi_mov_product not in (".implode(',', $not_del_id_whi_mov_product).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  

$del_id_whi_mov_product=array();
while ($row = $result->fetch_assoc()) {
  $del_id_whi_mov_product[]=$row['id_whi_mov_product'];
}
if (count($del_id_whi_mov_product)>0) {
  $sql="delete from gks_whi_mov_products where id_whi_mov_product in (".implode(',',$del_id_whi_mov_product).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  //echo $sql;die();
}

 

//$return = array('success' => false, 'message' => base64_encode($sql));
//echo json_encode($return); die();

foreach ($orders_products as $myrec) {
  if ($myrec['product_id']>0 and in_array($myrec['product_id'],$all_products_for_balance)==false)
    $all_products_for_balance[]=$myrec['product_id'];
  
  $gks_id_whi_mov_product=$myrec['id_whi_mov_product'];
  if ($myrec['id_whi_mov_product']==0) {
    $sql="insert into gks_whi_mov_products (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      whi_mov_id,product_aa,product_id,
      product_monada_id,product_quantity,apografi_posotitaonhand,
      product_descr,product_comments,
      p_warehouses_id_from,p_warehouses_id_to
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      ".$myrec['product_aa'].",
      ".$myrec['product_id'].",
      ".$myrec['product_monada_id'].",
      ".$myrec['product_quantity'].",
      ".($myrec['apografi_posotitaonhand']===null ? 'null' : $myrec['apografi_posotitaonhand']).",
      '".$db_link->escape_string($myrec['product_descr'])."',
      '".$db_link->escape_string($myrec['product_comments'])."',
      ".$warehouses_id_from.",
      ".$warehouses_id_to."
    )";
    //echo '<pre>'.$sql;die();
    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }   
      
    $gks_id_whi_mov_product=$db_link->insert_id;
     
  } else {
    $sql="update gks_whi_mov_products set 
      product_aa=".$myrec['product_aa'].",
      product_id=".$myrec['product_id'].",
      product_monada_id=".$myrec['product_monada_id'].",
      product_quantity=".$myrec['product_quantity'].",
      apografi_posotitaonhand=".($myrec['apografi_posotitaonhand']===null ? 'null' : $myrec['apografi_posotitaonhand']).",
      product_descr='".$db_link->escape_string($myrec['product_descr'])."',
      product_comments='".$db_link->escape_string($myrec['product_comments'])."',
      p_warehouses_id_from=".$warehouses_id_from.",
      p_warehouses_id_to=".$warehouses_id_to.",
      
      mydate_edit=now(),
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where id_whi_mov_product=".$myrec['id_whi_mov_product']." and whi_mov_id=".$id;
    //echo '<pre>'.$sql;die();
   
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
  

  if ($GKS_PRODUCT_LOTS_SERIALS) {
    $sql="update gks_whi_mov_products_lots set 
    lot_product_id=0,lot_product_quantity=0
    where whi_mov_product_id=".$gks_id_whi_mov_product;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select id_whi_mov_product_lots from gks_whi_mov_products_lots where whi_mov_product_id=".$gks_id_whi_mov_product." order by id_whi_mov_product_lots";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $exist_ids=array();
    while ($row = $result->fetch_assoc()) {
      $exist_ids[]=$row['id_whi_mov_product_lots'];
    }   
    
    foreach ($myrec['product_lots_serials'] as $lot_product_item) {
      $id_found=0;
      foreach ($exist_ids as &$oid) {if ($oid>0) {$id_found=$oid;$oid=0;break;}} unset($oid);
      if ($id_found>0) {
        $sql="update gks_whi_mov_products_lots set 
        lot_product_id=".$lot_product_item['lot_product_id'].",
        lot_product_quantity=".number_format($lot_product_item['lot_product_quantity'],10,'.','').",
        apografi_lot_posotitaonhand=".($lot_product_item['apografi_lot_posotitaonhand']===null ? 'null' : $lot_product_item['apografi_lot_posotitaonhand'])."
        where id_whi_mov_product_lots=".$id_found;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      } else {
        $sql="insert into gks_whi_mov_products_lots (
          whi_mov_product_id,lot_product_id,lot_product_quantity,apografi_lot_posotitaonhand
        ) values (
          ".$gks_id_whi_mov_product.",
          ".$lot_product_item['lot_product_id'].",
          ".number_format($lot_product_item['lot_product_quantity'],10,'.','').",
          ".($lot_product_item['apografi_lot_posotitaonhand']===null ? 'null' : $lot_product_item['apografi_lot_posotitaonhand'])."
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
      $sql="delete from gks_whi_mov_products_lots where id_whi_mov_product_lots in (".implode(',', $for_del).")";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
}

if (count($del_id_whi_mov_product)>0) {
  $sql="delete from gks_whi_mov_products_lots where whi_mov_product_id in (".implode(',',$del_id_whi_mov_product).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  //echo $sql;die();
}    

$gks_pricelist_item_id=0;




//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($not_del_id_whi_mov_product,true).print_r($orders_products,true).print_r($eidi_array,true)));
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($orders_products,true).'</pre>'));
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($all_products_for_balance,true)));
//echo json_encode($return); die();



$has_ekdosi=false;
$save_but_message='';
if ($mov_state=='090ekdosi' and $is_xeirografi==0) {
  //ekdosi

  gks_whi_mov_get_ekdosi_numbers();
  
}



//mov_state
$sql="update gks_whi_mov set ";
if ($mov_state!= '') {
  $sql.="mov_state='".$db_link->escape_string($mov_state)."', ";
}
if ($is_xeirografi!=0) {
  $sql.="mov_whi_number_int=".$mov_whi_number_int_user.", ";
  if ($mov_state=='090ekdosi' and $mov_whi_ekdosi_date_old=='') {
    $sql.="mov_whi_ekdosi_date=now(),";
  }
} else {
  if ($has_ekdosi) {
    $sql.="mov_whi_number_int=".$mov_whi_number_int_new.",
           mov_whi_number_str='".$db_link->escape_string($mov_whi_number_str_new)."',
           mov_whi_ekdosi_date=now(),
           mov_whi_seira_code='".$db_link->escape_string($mov_whi_seira_code_new)."',";
  }
}

//price=".number_format($price, 10, '.', '').",
$sql.="
company_id=".$company_id.",
mov_whi_journal_id=".$mov_whi_journal_id.",
mov_whi_seira_id=".$mov_whi_seira_id.",
reverse_delivery_purpose=".$reverse_delivery_purpose.",
company_sub_id=".$company_sub_id.",
mov_date=".($mov_date == '' ? 'null' : "'".$db_link->escape_string($mov_date)."'") .", 
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
delivery_id_8=".$delivery_id_8.",
delivery_number='".$db_link->escape_string($delivery_number)."',
vehicle_number='".$db_link->escape_string($vehicle_number)."',
dispatch_date=".($dispatch_date == '' ? 'null' : "'".$db_link->escape_string($dispatch_date)."'") .", 
dispatch_time=".($dispatch_time == '' ? 'null' : "'".$db_link->escape_string($dispatch_time)."'") .", 
kostos_apostolis=".number_format($kostos_apostolis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').", ";


$sql.="assigned_id=".$assigned_id.",";
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


user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'

where id_whi_mov = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }





$myarray_new=array();
$myarray_line_new=array();
$idiotites_new=get_whi_mov_details_txt($id, $myarray_new, $myarray_line_new); 

$sql="update gks_whi_mov set idiotites='".$db_link->escape_string(json_encode($myarray_new))."' where id_whi_mov = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
  


$sql="UPDATE gks_whi_mov_products
LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product
SET
gks_whi_mov_products.product_need_apostoli = gks_eshop_products.product_need_apostoli,
gks_whi_mov_products.product_varos = gks_eshop_products.product_varos,
gks_whi_mov_products.product_ogos_x = gks_eshop_products.product_ogos_x,
gks_whi_mov_products.product_ogos_y = gks_eshop_products.product_ogos_y,
gks_whi_mov_products.product_ogos_z = gks_eshop_products.product_ogos_z,
gks_whi_mov_products.product_normal = gks_eshop_products.product_normal,
gks_whi_mov_products.product_type = gks_eshop_products.product_type,
gks_whi_mov_products.product_need_multi_files = gks_eshop_products.product_need_multi_files,
gks_whi_mov_products.product_need_multi_files_min = gks_eshop_products.product_need_multi_files_min,
gks_whi_mov_products.product_need_multi_files_max = gks_eshop_products.product_need_multi_files_max,
gks_whi_mov_products.product_monada_id_org=gks_eshop_products.product_monada_id

WHERE gks_whi_mov_products.whi_mov_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 
$sql="select * from gks_whi_mov_products where whi_mov_id=".$id;
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
  if ($eidos_parastatikou_type_id==24) { //apografi
    $products_posotita+=$row['apografi_posotitaonhand'];
  } else {
    $products_posotita+=$row['product_quantity'];
  }
  //$products_varos+=$row['product_quantity'] * $row['product_varos'];
  //$products_ogos+=$row['product_quantity'] * ($row['product_ogos_x'] * $row['product_ogos_y'] * $row['product_ogos_z']);
  
  //if ($row['product_ogos_x'] > $products_ogos_max_x) $products_ogos_max_x=$row['product_ogos_x'];
  //if ($row['product_ogos_y'] > $products_ogos_max_y) $products_ogos_max_y=$row['product_ogos_y'];
  //$products_ogos_max_z+=$row['product_quantity'] * $row['product_ogos_z'];  
  
  if ($row['product_need_apostoli']!=0) $products_need_apostoli=1;
}


//products_varos=".number_format($products_varos,8,'.','').",
//products_ogos=".number_format($products_ogos,8,'.','').",
//products_ogos_max_x=".number_format($products_ogos_max_x,8,'.','').",
//products_ogos_max_y=".number_format($products_ogos_max_y,8,'.','').",
//products_ogos_max_z=".number_format($products_ogos_max_z,8,'.','').",

$sql="update gks_whi_mov set 
products_posotita=".number_format($products_posotita,8,'.','').",
products_need_apostoli=".$products_need_apostoli.",
session_id='".$_gks_id_session."'
where id_whi_mov=".$id;
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
  
  if ($sql!='') {
    $sql=substr($sql, 0, strlen($sql)-1);
    $sql="update gks_whi_mov set ".$sql." where id_whi_mov=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
  }
  
  
  if (isset($mybasketarray['products'])) {
    foreach ($mybasketarray['products'] as $aa => $product) {
      $sql='';
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
        $sql="update gks_whi_mov_products set ".$sql." where whi_mov_id=".$id." and product_aa=".$aa." limit 1";
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










$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

if ($is_new_rec == false) {

  gks_whi_mov_sxolio_log($id,$row_old,$products_old,$extra_address_old,'',[],$gks_custom_row_old);
  
}
$p_mov_state=$mov_state!='' ? $mov_state : $mov_state_old;
$sql="update gks_whi_mov_products set p_mov_state='".$db_link->escape_string($p_mov_state)."' where whi_mov_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}    

$mybal = gks_whi_mov_balance_calc($all_products_for_balance);

gks_whi_after_balance_for_whi($id);

gks_update_user_from_some_move(array('user_id'=>$user_id,'table'=>'gks_whi_mov','id_table'=>$id));



//echo '<pre>';print_r($all_products_for_balance);die();

$return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> $redirect,'save_but_message' => base64_encode($save_but_message));
echo json_encode($return); die();


function gks_whi_after_balance_for_whi($id) {
  global $db_link;
  
  //if ($p_mov_state=='090ekdosi') {
    //$sql="select mov_date from gks_whi_mov where id_whi_mov=".$id." and mov_state='090ekdosi' and mov_date is not null";
    $sql="select mov_date, warehouses_id_from, warehouses_id_to from gks_whi_mov where id_whi_mov=".$id." and mov_date is not null";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      $mov_date=$row['mov_date'];
      $warehouses_id_from=$row['warehouses_id_from'];
      $warehouses_id_to=$row['warehouses_id_to'];
      
      
      $sql="SELECT product_id FROM gks_whi_mov_products WHERE whi_mov_id=".$id." GROUP BY product_id ORDER BY product_id";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      $all_products_for_balance=array();
      while ($row = $result->fetch_assoc()) {
        $all_products_for_balance[]=$row['product_id'];
      }
      
      
      $mybal = gks_whi_mov_balance_calc($all_products_for_balance,$mov_date);
      //echo '<pre>'; print_r($mybal); echo '</pre>';die();
      
      foreach ($mybal as $id_product => $data) {
        $after_balance_warehouses_id_from=0;
        $after_balance_warehouses_id_to=0;
        if (isset($data['warehouses'][$warehouses_id_from])) $after_balance_warehouses_id_from=$data['warehouses'][$warehouses_id_from]['bal'];
        if (isset($data['warehouses'][$warehouses_id_to]))   $after_balance_warehouses_id_to=  $data['warehouses'][$warehouses_id_to]['bal'];
        
        $sql="update gks_whi_mov_products set 
        after_balance_warehouses_id_from=".number_format($after_balance_warehouses_id_from, 8, '.', '').",
        after_balance_warehouses_id_to=".number_format($after_balance_warehouses_id_to, 8, '.', '')."
        where whi_mov_id=".$id." and product_id=".$id_product;
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}    
      } 
    }
    
  //}  
  
  
}





function gks_whi_mov_to_draft($id) {
  
  global $db_link;

  $sql_credit_memo="select id_whi_mov from gks_whi_mov where credit_memo_for_whi_mov_id=".$id;
  $result_credit_memo = $db_link->query($sql_credit_memo);  
  if (!$result_credit_memo) {
    debug_mail(false,'error sql',$result_credit_memo);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_credit_memo->num_rows>=1) {
    $row_credit_memo=$result_credit_memo->fetch_assoc();
    $message=gks_lang('Για αυτό το δελτίο υπάρχει συσχετιζόμενο δελτίο επιστροφής με').' <br>ID :<b>'.$row_credit_memo['id_whi_mov'].'</b><br>'.
    '<a class="gks_link" href="admin-whi-mov-item.php?id='.$row_credit_memo['id_whi_mov'].'">'.gks_lang('Προβολή').'</a><br>'.
    gks_lang('Οπότε, δεν μπορεί να γίνει αυτή η αλλαγή, εκτός και εάν διαγραφεί το συσχετιζόμενο δελτίο επιστροφής');
    debug_mail(false,'cancel_for_whi_mov_id='.$row_credit_memo['id_whi_mov'],$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }

  
  $sql_canceled="select id_whi_mov from gks_whi_mov where cancel_for_whi_mov_id=".$id;
  $result_canceled = $db_link->query($sql_canceled);  
  if (!$result_canceled) {
    debug_mail(false,'error sql',$result_canceled);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_canceled->num_rows>=1) {
    $row_canceled=$result_canceled->fetch_assoc();
    $message=gks_lang('Για αυτό το δελτίο υπάρχει ακυρωτικό δελτίο με').' <br>ID :<b>'.$row_canceled['id_whi_mov'].'</b><br>'.
    '<a class="gks_link" href="admin-whi-mov-item.php?id='.$row_canceled['id_whi_mov'].'">'.gks_lang('Προβολή').'</a><br>'.
    gks_lang('Οπότε, δεν μπορεί να γίνει αυτή η αλλαγή, εκτός και εάν διαγραφεί το ακυρωτικό δελτίο');
    debug_mail(false,'cancel_for_whi_mov_id='.$row_canceled['id_whi_mov'],$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
  

    
  //die('<pre>ssss');
  
  $sql="select * from gks_whi_mov where id_whi_mov=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_inv = $result->fetch_assoc();
  $mov_whi_seira_id=$row_inv['mov_whi_seira_id'];
  $mov_whi_number_int_old=$row_inv['mov_whi_number_int'];
  $aade_invoicemark=trim_gks($row_inv['aade_invoicemark']);
  if ($aade_invoicemark!='') {
    $message=gks_lang('Αυτό το δελτίο έχει ήδη αποσταλεί στην ΑΑΔΕ οπότε δεν μπορεί να γίνει πρόχειρο');
    debug_mail(false,'gks_whi_mov_to_draft '.$id,$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
    
  $sql="select * from gks_acc_seires where id_acc_seira=".$mov_whi_seira_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_seira = $result->fetch_assoc();
  $prev_number=$row_seira['next_number']-$row_seira['number_step'];
  
  
  $warning_message='';
  if ($prev_number!=$mov_whi_number_int_old) {
    $warning_message=
          gks_lang('Επόμενος αριθμός σειράς').': <b>'.$row_seira['next_number'].'</b><br>'.
          gks_lang('Βήμα σειράς').': <b>'.$row_seira['number_step'].'</b><br>'.
          gks_lang('Τρέχον αριθμός παραστατικού').': <b>'.$mov_whi_number_int_old.'</b> (<>'.
          $row_seira['next_number'].'-'.$row_seira['number_step'].')';
          
    debug_mail(false,'prev_number is not equal mov_whi_number_int_old',$prev_number.' != '.$mov_whi_number_int_old.' '.$warning_message);
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Έχουν εκδοθεί ενδιάμεσα παραστατικά σε αυτήν την σειρά και θα δημιουργηθούν κενά στην αρίθμηση').'<br>'.$temp));
    //echo json_encode($return); die(); 
  } else {  
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $sql="update gks_acc_seires set next_number=next_number-number_step where id_acc_seira=".$mov_whi_seira_id;
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
    where acc_seira_id=".$mov_whi_seira_id." and whi_mov_id=".$id." and disabled_date is null";
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    

  }
  //$return = array('success' => false, 'message' => base64_encode('sssssss'));
  //echo json_encode($return); die();
  
  if ($prev_number==$mov_whi_number_int_old) {
    $sql="update gks_whi_mov set mov_state='010draft', mov_whi_number_int=0, mov_whi_number_str=null,mov_whi_ekdosi_date=null where id_whi_mov=".$id;
  } else {
    $sql="update gks_whi_mov set mov_state='010draft' where id_whi_mov=".$id;
  }
  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
    
  return $warning_message;
}






