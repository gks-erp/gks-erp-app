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
$my_page_title=gks_lang('Αποθήκευση Πληρωμής').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_pay',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');



$_gks_session['temp_mypropertiesheight'] = 0;
if (isset($_POST['mypropertiesheight'])) $_gks_session['temp_mypropertiesheight']=intval($_POST['mypropertiesheight']); gks_erp_cookie_save();
$pay_state=''; if (isset($_POST['pay_state'])) $pay_state=trim_gks(base64_decode($_POST['pay_state']));




$pay_state_old='';
$idiotites_old='';
$pay_acc_number_int_old=0;
$pay_acc_number_str_old='';
$pay_acc_ekdosi_date_old='';
$seira_code_old='';
$is_xeirografi_old=0;


$myarray_old=array();
$myarray_line_old=array();

$row_old=array();
$products_old=array();
$extra_address_old=array();
$credit_memo_for_acc_pay_id=0;

$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
  $pay_state_old='010draft';
  $row_old['pay_state']='010draft';
  $row_old['pay_acc_number_int']=0;
} else {
  $sql=select_gks_acc_pay()." where id_acc_pay=".$id;
  if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_pay.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_pay.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_pay.pay_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_pay.pay_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
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
  $pay_state_old=trim_gks($row_old['pay_state']);
  $idiotites_old=get_acc_pay_details_txt($id, $myarray_old, $myarray_line_old); 
  $pay_acc_number_int_old=$row_old['pay_acc_number_int']; 
  $pay_acc_number_str_old=trim_gks($row_old['pay_acc_number_str']); 
  $pay_acc_ekdosi_date_old=trim_gks($row_old['pay_acc_ekdosi_date']); 
  $seira_code_old=trim_gks($row_old['seira_code']); 
  $is_xeirografi_old=trim_gks($row_old['is_xeirografi']); 
  $credit_memo_for_acc_pay_id=$row_old['credit_memo_for_acc_pay_id'];
  
  $sql="SELECT gks_acc_pay_method.*
  FROM gks_acc_pay_method 
  WHERE gks_acc_pay_method.acc_pay_id=".$id."
  ORDER BY gks_acc_pay_method.paymethod_aa;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row = $result->fetch_assoc()) {
    $products_old[]=$row;
  }

  $gks_custom_prepare=gks_custom_table_item_prepare('gks_acc_pay',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
  
}

$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;
if ($row_old['pay_state']=='040cancelled' or $row_old['pay_state']=='080listing' or $row_old['pay_state']=='090ekdosi' or $row_old['pay_state']=='100payment') {
  $gks_lock=true;
} else {
  if ($row_old['pay_acc_number_int'] > 0 and $row_old['is_xeirografi']==0 and ($row_old['pay_state']=='010draft')) {
    $gks_number_lock=true;
  }
}
if ($credit_memo_for_acc_pay_id!=0) {
  $gks_number_lock=true;
  $gks_user_lock=true;
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
if ($pay_state=='aade_send') {
  $aade_send=true; 
  $pay_state='';
} else if ($pay_state=='paroxos_send') {
  $paroxos_send=true; 
  $pay_state='';
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
      debug_mail(false,'channel not found');
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
  //echo '<pre>pay_state '.$pay_state;die();
  
  if ($pay_state!='' and $pay_state!='010draft' and $pay_state!='040cancelled' and $pay_state!='credit_memo')  {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος κατάσταση').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die(); }

  
  
  if ($pay_state=='040cancelled') {

    $sql_credit_memo="SELECT id_acc_pay, credit_memo_for_acc_pay_id FROM gks_acc_pay WHERE credit_memo_for_acc_pay_id=".$id;
    //die($sql_credit_memo);
    $result_credit_memo = $db_link->query($sql_credit_memo);  
    if (!$result_credit_memo) {
      debug_mail(false,'error sql',$sql_credit_memo);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_credit_memo->num_rows>=1) {
      $row_credit_memo=$result_credit_memo->fetch_assoc();
      if ($row_credit_memo['credit_memo_for_acc_pay_id']!=0) {
        $message=gks_lang('Για αυτό το παραστατικό υπάρχει ήδη το συσχετιζόμενο πιστωτικό παραστατικό με').' <br>ID :<b>'.$row_credit_memo['id_acc_pay'].'</b><br>'.
        '<a class="gks_link" href="admin-acc-pay-item.php?id='.$row_credit_memo['id_acc_pay'].'">'.gks_lang('Προβολή').'</a>';
        debug_mail(false,'credit_memo_for_acc_pay_id='.$row_credit_memo['credit_memo_for_acc_pay_id'],$message);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die(); }
    }
    
    $affect_balance_pros=intval($row_old['eidos_parastatikou_balance_pros']);
    gks_acc_pay_poso_revert($id,$affect_balance_pros);
   
    
  }
  
  
  //echo '<pre>'.$pay_state; die();
  
  if ($pay_state=='010draft' and $pay_state_old!='010draft' and $is_xeirografi_old==0 and $pay_acc_number_int_old>0) {
    //echo '<pre>vvv';die();
    $warning_message=gks_pay_to_draft($id);
    if ($warning_message!='') 
      $warning_message=gks_lang('Έγινε επαναφορά σε <b>Πρόχειρο</b> αλλά δεν μπόρεσε μηδενιστεί ο αριθμός του παραστατικού διότι').':<br>'.
                        $warning_message.'<br>'.gks_lang('Κάντε άμεσα τις αλλαγές και ξανα εκδώστε το').'<br>'.gks_lang('Διαφορετικά θα δημιουργηθεί κενό στην αρίθμηση της σειράς');
  }
  //print '<pre>'.$warning_message;die();
  
  $note_doc=''; if (isset($_POST['note_doc'])) $note_doc=trim_gks(base64_decode($_POST['note_doc']));
  $note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));


  $affect_balance=0; if (isset($_POST['affect_balance'])) $affect_balance=intval($_POST['affect_balance']);  
  if ($affect_balance!=1) $affect_balance=0;
  $affect_balance_all_poso=0; if (isset($_POST['affect_balance_all_poso'])) $affect_balance_all_poso=intval($_POST['affect_balance_all_poso']);  
  if ($affect_balance_all_poso!=1) $affect_balance_all_poso=0;
  $affect_balance_all_poso_type=''; if (isset($_POST['affect_balance_all_poso_type'])) $affect_balance_all_poso_type=trim_gks($_POST['affect_balance_all_poso_type']);
  if ($affect_balance_all_poso_type=='') $affect_balance_all_poso_type='total_price_net'; 
  $affect_balance_poso=0;  if (isset($_POST['affect_balance_poso']))  $affect_balance_poso=floatval($_POST['affect_balance_poso']);
  

  if ($pay_state=='credit_memo') gks_acc_pay_credit_memo_create($id, true);


  $gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_acc_pay');

  
  
  $sql="update gks_acc_pay set ";
  if ($pay_state!= '' and $pay_state!='credit_memo') {
    $sql.="pay_state='".$db_link->escape_string($pay_state)."', ";
  }
  
  $sql.=$sql_ekdosi;
  
  $sql.="
  note_doc='".$db_link->escape_string($note_doc)."',
  note_logistirio='".$db_link->escape_string($note_logistirio)."',
  affect_balance=".$affect_balance.",
  affect_balance_all_poso=".$affect_balance_all_poso.",
  affect_balance_all_poso_type='".$db_link->escape_string($affect_balance_all_poso_type)."',";

  if ($affect_balance == 0) {
    $affect_balance_poso=0;
  } else {
    if ($affect_balance_all_poso==1) {
      switch ($affect_balance_all_poso_type) {
        case 'price_total':
          $affect_balance_poso=$row_old['gks_price_total'];
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

  
  
  //echo '<pre>'.$affect_balance_poso;die();
  
  $sql.="user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."',
  session_id='".$_gks_id_session."'
  where id_acc_pay = ".$id." limit 1";  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  


  $gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);



  
//  if ($pay_state=='040cancelled') {
//    $id_canceled=gks_acc_pay_cancel_create($id);
//    if ($id_canceled>0) {
//      //echo '<pre>'.$pay_state.' '.$id_canceled; die();
//      $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
//      
//      $message=gks_lang('Το ακυρωτικό παραστατικό έχει δημιουργηθεί').'<br>'.
//      gks_lang('To ID του είναι').' <b>'.$id_canceled.'</b><br>'.
//      gks_lang('Θα πρέπει να το ελέγξετε και να το εκδώσετε').'<br>'.
//      '<a class="gks_link" href="admin-acc-pay-item.php?id='.$id_canceled.'">'.gks_lang('Προβολή').'</a>';
//      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode($message));
//      echo json_encode($return); die();
//      
//    } else {
//      $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
//      debug_mail(false,'error gks_acc_pay_cancel_create',$id.' '.$id_canceled);
//      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
//      echo json_encode($return); die();
//    }
//  }
  
  if ($pay_state=='credit_memo') {

    $id_credit_memo=gks_acc_pay_credit_memo_create($id, false);
    if ($id_credit_memo>0) {
      //echo '<pre>'.$pay_state.' '.$id_credit_memo; die();
      $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
      $message=gks_lang('Το πιστωτικό παραστατικό έχει δημιουργηθεί').'<br>'.
      gks_lang('To ID του είναι').' <b>'.$id_credit_memo.'</b><br>'.
      gks_lang('Θα πρέπει να το ελέγξετε και να το εκδώσετε').'<br>'.
      '<a class="gks_link" href="admin-acc-pay-item.php?id='.$id_credit_memo.'">'.gks_lang('Προβολή').'</a>';
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode($message));
      echo json_encode($return); die();
      
    } else {
      $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
      debug_mail(false,'error gks_acc_pay_credit_memo_create',$id.' '.$id_credit_memo);
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();
    }
    
    
  }
  
  $eidi_array_asset_str = trim_gks(base64_decode($_POST['eidi_array_asset_str']));
  //$eidi_array_asset_str=substr($eidi_array_asset_str, 10); //gia test otan iparxei error 

  $eidi_array_asset = json_decode($eidi_array_asset_str, true);
  if ($eidi_array_asset === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error eidi_array',$_POST['eidi_array_asset_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (1)<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  foreach ($eidi_array_asset as $value) {
    if (isset($value['id_acc_pay_method'])) {
      if ($value['asset_id']>=0) {
        $sql="update gks_acc_pay_payment set 
        asset_id=".$value['asset_id'].",
        mydate_edit=now(),
        user_id_edit=".$my_wp_user_id.",
        myip='".$db_link->escape_string($gkIP)."'
        where asset_id<>".$value['asset_id']."
        and acc_pay_method_id=".$value['id_acc_pay_method']."
        and acc_pay_id=".$id."
        and transaction_id=0";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        
        
      }
      
    } 
  } 
  
  
  //echo '<pre>ssssssssssss';print_r($eidi_array_asset);die();
  
  $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
  //echo '<pre>'; echo $row_old['user_id'];die();
  //echo '<pre>sssssssssss|'.$aade_send.'|'.$paroxos_send.'|';die();
  
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
    $force_options['doc_table']='gks_acc_pay';
    $ret=gks_aade_invoice($id,$force_options);
    
    $row_old['aade_errors']='';
    $myparams=[];
    if ($ret['success']==false and $ret['message']!='') $myparams['ret_aade_errors']=$ret['message'];
    gks_pay_sxolio_log($id,$row_old,$products_old,$extra_address_old,($ret['mydata_live']==0 ? gks_lang('Δοκιμαστική αποστολή σε myData').'<br>' : gks_lang('Πραγματική αποστολή σε myData').'<br>'),$myparams,$gks_custom_row_old);
    
    $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
    
    //print '<pre>'; print_r($ret); die(); 
    if ($ret['success']==false) {
      $return = array('success' => true, 'message' => base64_encode('error'),'redirect'=> base64_encode('admin-acc-pay-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode($ret['message']));
      echo json_encode($return); die();
    } else {
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> base64_encode('admin-acc-pay-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode(gks_lang('Επιτυχής αποστολή δεδομένων myData στην ΑΑΔΕ')));
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

    $force_options['doc_table']='gks_acc_pay';
    //print '<pre>';print_r($force_options);die();
    $ret=gks_paroxos_invoice($id,$force_options);
    
    $row_old['aade_errors']='';
    $myparams=[];
    if ($ret['success']==false and $ret['message']!='') $myparams['ret_aade_errors']=$ret['message'];
    gks_pay_sxolio_log($id,$row_old,$products_old,$extra_address_old,($paroxos_mydata_live==false ? gks_lang('Δοκιμαστική αποστολή σε πάροχο').'<br>' : gks_lang('Πραγματική αποστολή σε πάροχο').'<br>'),$myparams,$gks_custom_row_old);

    $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
    
    //print '<pre>'; print_r($ret); die(); 
    if ($ret['success']==false) {
      $return = array('success' => true, 'message' => base64_encode('error'),'redirect'=> base64_encode('admin-acc-pay-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode($ret['message']));
      echo json_encode($return); die();
    } else {
      $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> base64_encode('admin-acc-pay-item.php?id='.$id.'&scrollto=gks_aade'),'save_but_message' => base64_encode($ret['save_but_message']));
      echo json_encode($return); die();      
    }    
  } else {  
    gks_pay_sxolio_log($id,$row_old,$products_old,$extra_address_old,'',[],$gks_custom_row_old);
  }
  
  $return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> '','save_but_message' => base64_encode($warning_message));
  echo json_encode($return); die();
  
  
}
    



$vfields=array(); 




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
  
$pay_acc_journal_id=0;if (isset($_POST['pay_acc_journal_id'])) $pay_acc_journal_id=intval($_POST['pay_acc_journal_id']);
if ($gks_number_lock) $pay_acc_journal_id=$row_old['pay_acc_journal_id'];
if ($pay_acc_journal_id<=0) {
  debug_mail(false,'pay_acc_journal_id is not found',$pay_acc_journal_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Ημερολόγιο')));
  echo json_encode($return); die();}  
  
$pay_acc_seira_id=0;if (isset($_POST['pay_acc_seira_id'])) $pay_acc_seira_id=intval($_POST['pay_acc_seira_id']);
if ($gks_number_lock) $pay_acc_seira_id=$row_old['pay_acc_seira_id'];
if ($pay_acc_seira_id<=0) {
  debug_mail(false,'pay_acc_seira_id is not found',$pay_acc_seira_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Σειρά')));
  echo json_encode($return); die();}  

$pay_acc_number_int_user=0;if (isset($_POST['pay_acc_number_int'])) $pay_acc_number_int_user=intval($_POST['pay_acc_number_int']);



$sql="SELECT gks_acc_seires.id_acc_seira,gks_acc_seires.is_xeirografi
FROM (((gks_acc_seires 
LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE eidos_parastatikou_type_id in (11,12) and id_acc_eidos_parastatikou not in (702,703,704) 
AND gks_acc_seires.is_disable=0 AND gks_acc_journal.is_disable=0 
AND gks_company.company_disable=0 
AND gks_acc_seires.id_acc_seira=".$pay_acc_seira_id." 
AND gks_acc_journal.id_acc_journal=".$pay_acc_journal_id." 
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
  debug_mail(false,'company journal seires not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο συνδυασμός εταιρείας/υποκαταστήματος, ημερολογίου και σειράς')));
  echo json_encode($return); die();}
$row_seira = $result->fetch_assoc();
$is_xeirografi=$row_seira['is_xeirografi'];



if ($pay_state=='010draft' and $pay_state_old!='010draft' and $is_xeirografi_old==0 and $pay_acc_number_int_old>0) {
  //echo '<pre>vvv';die();
 gks_pay_to_draft($id);
}



if ($pay_state=='080listing' and $is_xeirografi==0) {
  debug_mail(false,'pay_state 080listing is_xeirografi',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η σειρά είναι χειρόγραφη, άρα το παραστατικό θα πρέπει να εκδοθεί και όχι να καταχωρηθεί')));
  echo json_encode($return); die();}    
  
if ($pay_state=='090ekdosi' and $is_xeirografi!=0) {
  debug_mail(false,'pay_state 090ekdosi is_xeirografi',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η σειρά είναι μηχανογραφημένη, άρα το παραστατικό θα πρέπει να καταχωρηθεί και όχι να εκδοθεί')));
  echo json_encode($return); die();}
  



$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);

if ($user_id<=0) {
  debug_mail(false,'user_id zero','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποιον πελάτη')));
  echo json_encode($return); die();}







if ($_POST['pay_date'] == '__/__/____ __:__') $_POST['pay_date']='';
$pay_date=trim_gks(stripslashes(urldecode($_POST['pay_date'])));
if ($pay_date!='') {
  $pay_date = mystrtodb($pay_date);
}

$note_doc=''; if (isset($_POST['note_doc'])) $note_doc=trim_gks(base64_decode($_POST['note_doc']));
$note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));





 
$affect_balance=0; if (isset($_POST['affect_balance'])) $affect_balance=intval($_POST['affect_balance']);  
if ($affect_balance!=1) $affect_balance=0;
$affect_balance_all_poso=0; if (isset($_POST['affect_balance_all_poso'])) $affect_balance_all_poso=intval($_POST['affect_balance_all_poso']);  
if ($affect_balance_all_poso!=1) $affect_balance_all_poso=0;
$affect_balance_all_poso_type=''; if (isset($_POST['affect_balance_all_poso_type'])) $affect_balance_all_poso_type=trim_gks($_POST['affect_balance_all_poso_type']);
if ($affect_balance_all_poso_type=='') $affect_balance_all_poso_type='total_price_net'; 
$affect_balance_poso=0;  if (isset($_POST['affect_balance_poso']))  $affect_balance_poso=floatval($_POST['affect_balance_poso']);


$multiple_connected_marks_array_str = trim_gks(base64_decode($_POST['multiple_connected_marks_array_str']));
$multiple_connected_marks_array = json_decode($multiple_connected_marks_array_str, true);
if ($multiple_connected_marks_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error multiple_connected_marks_array',$_POST['multiple_connected_marks_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
//echo '<pre>';print_r($multiple_connected_marks_array);die();

   
$eidi_array_str = trim_gks(base64_decode($_POST['eidi_array_str']));
//$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 

$eidi_array = json_decode($eidi_array_str, true);
if ($eidi_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error eidi_array',$_POST['eidi_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (1)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

if (count($eidi_array)==0 and ($pay_state=='080listing' or $pay_state=='090ekdosi')) {
  debug_mail(false,'eidi_array zero',print_r($eidi_array,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχετε προσθέσει είδη')));
  echo json_encode($return); die();}

$pay_poso=null;
if (isset($_POST['pay_poso_str']) and trim_gks($_POST['pay_poso_str'])!='') {
  $pay_poso_str = trim_gks(base64_decode($_POST['pay_poso_str']));
  $pay_poso = json_decode($pay_poso_str, true);
  if ($pay_poso === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error pay_poso',$_POST['pay_poso_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (2)<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  $pay_poso_clean=array();
  foreach ($pay_poso as $val) {
    if (isset($val['v']) and $val['v']!=0) $pay_poso_clean[]=$val;
  }
  $pay_poso=$pay_poso_clean;
  //echo '<pre>';print_r($pay_poso);die();
}
  

$eidos_parastatikou_type_id=0;
$eidos_parastatikou_need_afm=0;
$eidos_parastatikou_has_fpa=1;
$affect_balance_pros=0;
$acc_eidos_parastatikou_other_entity=0;
$journal_has_correlated_invoices=0;
$journal_has_multiple_connected_marks=0;

//die('<pre>|'.$gks_lock.'|'.$gks_number_lock.'|'.$gks_user_lock.'|');

if ($credit_memo_for_acc_pay_id>0) {
  $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,
  gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
  gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  gks_acc_journal.acc_eidos_parastatikou_other_entity,
  gks_acc_journal.journal_has_correlated_invoices,
  gks_acc_journal.journal_has_multiple_connected_marks
  FROM (gks_acc_pay 
  LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
  WHERE gks_acc_pay.id_acc_pay=".$credit_memo_for_acc_pay_id;
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
    $affect_balance_pros=-$row['eidos_parastatikou_balance_pros'];
    $acc_eidos_parastatikou_other_entity=intval($row['acc_eidos_parastatikou_other_entity']);
    $journal_has_correlated_invoices=intval($row['journal_has_correlated_invoices']);
    $journal_has_multiple_connected_marks=intval($row['journal_has_multiple_connected_marks']);
  } else {
    debug_mail(false,'eidos_parastatikou_type_id empty for credit_memo_for_acc_pay_id',$id.' '.$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το συσχετιζόμενο παραστατικό')));
    echo json_encode($return); die();
    
  }
  
  
} else {

  if ($pay_acc_journal_id>0) {
    $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
    eidos_parastatikou_need_afm,eidos_parastatikou_has_fpa,
    gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
    gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
    gks_acc_journal.acc_eidos_parastatikou_other_entity,
    gks_acc_journal.journal_has_correlated_invoices,
    gks_acc_journal.journal_has_multiple_connected_marks
    FROM gks_acc_journal 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE gks_acc_journal.id_acc_journal=".$pay_acc_journal_id." and eidos_parastatikou_type_id>0";
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
      $acc_eidos_parastatikou_other_entity=intval($row['acc_eidos_parastatikou_other_entity']);
      $journal_has_correlated_invoices=intval($row['journal_has_correlated_invoices']);
      $journal_has_multiple_connected_marks=intval($row['journal_has_multiple_connected_marks']);
      if ($eidos_parastatikou_aade_code=='5.1') {
        $message=gks_lang('Παραστατικά με ημερολόγιο το οποίο έχει ως τύπο παραστατικού το <b>Πιστωτικό Τιμολόγιο / Συσχετιζόμενο</b> δεν μπορούν να δημιουργηθούν άμεσα').'<br>'.
        gks_lang('Θα πρέπει να δημιουργηθούν μέσα από το συσχετιζόμενο παραστατικό');
        
        debug_mail(false,$message,$sql);
        $return = array('success' => false, 'message' => base64_encode($message));
        echo json_encode($return); die(); 
      }
    }
    //echo '<pre>';print_r($row);die();
    
  }
}
if ($eidos_parastatikou_type_id<=0) {
  debug_mail(false,'eidos_parastatikou_type_id empty',$pay_acc_journal_id.' '.$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο γενικός τύπου του παραστατικού')));
  echo json_encode($return); die();}

if ($eidos_parastatikou_need_afm==0) {
  $dr_user_eponimia=''; 
  $dr_user_title='';
  $dr_user_afm='';
  $dr_user_doy='';
  $dr_user_epaggelma='';
}  
//print '<pre>';print $eidos_parastatikou_type_id;die();



//die('nn'.$journal_has_multiple_connected_marks);

$not_del_id_acc_pay_multiple_connected_marks=array();
if ($journal_has_multiple_connected_marks==0) $multiple_connected_marks_array=array();
$multiple_connected_marks_cc=0;
foreach ($multiple_connected_marks_array as &$multiple_connected_marks_item) {
  $multiple_connected_marks_cc++;
  $recid=intval($multiple_connected_marks_item['recid']);
  if ($recid>0) $not_del_id_acc_pay_multiple_connected_marks[] = $recid;
}
unset($multiple_connected_marks_item);
//print '<pre>';var_dump($multiple_connected_marks_array);die();
//print '<pre>sssss sssssss '."\n";print_r($multiple_connected_marks_array);die();




$not_del_id_acc_pay_method=array();
$orders_products=array();
$gks_price_total=0;


//print '<pre>';print_r($eidi_array);die();
$num_of_zero=0;
foreach ($eidi_array as $eidi_array_item) {
  $id_acc_pay_method=intval($eidi_array_item['id_acc_pay_method']);
  $paymethod_aa=intval($eidi_array_item['aa']);
  $paymethod_id=intval($eidi_array_item['paymethod_id']);
  $paymethod_total=  floatval($eidi_array_item['paymethod_total']);
  $paymethod_descr=trim_gks($eidi_array_item['paymethod_descr']);
  $paymethod_comments=trim_gks($eidi_array_item['paymethod_comments']);
  $asset_id=intval($eidi_array_item['asset_id']);
  if ($asset_id<0) $asset_id=0;

  if ($id_acc_pay_method>0) $not_del_id_acc_pay_method[] = $id_acc_pay_method;
    
  $orders_products[]=array(
    'id_acc_pay_method' => $id_acc_pay_method,
    'paymethod_aa' => $paymethod_aa,
    'paymethod_id' => $paymethod_id,
    'paymethod_total' => $paymethod_total,
    'paymethod_descr' => $paymethod_descr,
    'paymethod_comments' => $paymethod_comments,
    'asset_id' => $asset_id,
  );  
  $gks_price_total+=$paymethod_total;
  
  if ($paymethod_total<=0) {
    $num_of_zero++;
  }
}

$gks_price_total=$gks_price_total;


if ($gks_price_total <= 0) {//and $pay_state=='090ekdosi'
  debug_mail(false,'gks_price_total zero');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το συνολικό ποσό είναι μηδέν')));
  echo json_encode($return); die();}

if ($num_of_zero >0) {
  debug_mail(false,'num zero');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε ποσό σε κάθε γραμμή')));
  echo json_encode($return); die();}


//echo '<pre>eidos_parastatikou_aade_code '.$eidos_parastatikou_aade_code;die();
if ($eidos_parastatikou_aade_code=='8.4' or $eidos_parastatikou_aade_code=='8.5') {
  //echo '<pre>sssss ';print_r($orders_products);die();
  $ids=[];
  foreach ($orders_products as $value) {
    $ids[]=$value['paymethod_id'];
  }
  $has_not_7=false;
  if (count($ids)>0) {
    $sql="select id_payment_acquirer from gks_payment_acquirers
    where id_payment_acquirer in (".implode(',',$ids).")
    and aade_tropos_pliromis_id<>7";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die(); }
    if ($result->num_rows > 0) {
      $has_not_7=true;
    }
  }
  if ($has_not_7 or count($orders_products)>1) {
    debug_mail(false,'eidos_parastatikou_type_id is 8.4 or 8.5',$eidos_parastatikou_aade_code);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτός ο τύπος παραστατικού πρέπει να έχει μόνο έναν τρόπο πληρωμής και αυτός να είναι με POS')));
    echo json_encode($return); die();
  }
}

//echo '<pre>sssss ';print_r($orders_products);die();

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_acc_pay');


$redirect='';

if ($id==-1) {
  $pay_guid=guid_for_acc_pay();
  $bank_deposit_9digit=gks_get_bank_deposit_9digit();
  $sql="insert into gks_acc_pay (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,pay_guid,bank_deposit_9digit
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  '".$db_link->escape_string($pay_guid)."','".$db_link->escape_string($bank_deposit_9digit)."')";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }  
  $id = $db_link->insert_id;
  $redirect=base64_encode('admin-acc-pay-item.php?id='.$id);  
  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_acc_pay_log (acc_pay_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  
    
} 
$sql="select id_acc_pay_method from gks_acc_pay_method where acc_pay_id=".$id;
if (count($not_del_id_acc_pay_method)>0) {
  $sql.=" and id_acc_pay_method not in (".implode(',', $not_del_id_acc_pay_method).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  

$del_id_acc_pay_method=array();
while ($row = $result->fetch_assoc()) {
  $del_id_acc_pay_method[]=$row['id_acc_pay_method'];
}
if (count($del_id_acc_pay_method)>0) {
  $sql="delete from gks_acc_pay_method where id_acc_pay_method in (".implode(',',$del_id_acc_pay_method).") and acc_pay_id=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  

  $sql="delete from gks_acc_pay_payment where acc_pay_method_id in (".implode(',',$del_id_acc_pay_method).") and acc_pay_id=".$id." and transaction_id=0";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  

  
}



//$return = array('success' => false, 'message' => base64_encode($sql));
//echo json_encode($return); die();

foreach ($orders_products as &$myrec) {
  $gks_id_acc_pay_method=$myrec['id_acc_pay_method'];
  if ($myrec['id_acc_pay_method']==0) {
    $sql="insert into gks_acc_pay_method (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_pay_id,paymethod_aa,paymethod_id,
      paymethod_total,paymethod_descr,paymethod_comments
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      ".$myrec['paymethod_aa'].",
      ".$myrec['paymethod_id'].",
      ".number_format($myrec['paymethod_total'],10, '.','').",
      '".$db_link->escape_string($myrec['paymethod_descr'])."',
      '".$db_link->escape_string($myrec['paymethod_comments'])."'
    )";
    //echo '<pre>'.$sql;die();
    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }   
      
    $gks_id_acc_pay_method=$db_link->insert_id;
    
    $myrec['id_acc_pay_method']=$gks_id_acc_pay_method;
    
    $sql="insert into gks_acc_pay_payment (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_pay_id,acc_pay_method_id,pp,
      payment_acquirer_id,
      poso,asset_id
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",".$myrec['id_acc_pay_method'].",".$myrec['paymethod_aa'].",
      ".$myrec['paymethod_id'].",
      ".number_format($myrec['paymethod_total'],10, '.','').",
      ".$myrec['asset_id']."
    )";
    //echo '<pre>'.$sql;die();
    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }    
        
  } else {
    $sql="update gks_acc_pay_method set 
      paymethod_aa=".$myrec['paymethod_aa'].",
      paymethod_id=".$myrec['paymethod_id'].",
      paymethod_total=".number_format($myrec['paymethod_total'],10, '.','').",
      paymethod_descr='".$db_link->escape_string($myrec['paymethod_descr'])."',
      paymethod_comments='".$db_link->escape_string($myrec['paymethod_comments'])."',
      mydate_edit=now(),
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where id_acc_pay_method=".$myrec['id_acc_pay_method']." and acc_pay_id=".$id;
    //echo '<pre>'.$sql;die();
   
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
    
    $sql="update gks_acc_pay_payment set 
      pp=".$myrec['paymethod_aa'].",
      payment_acquirer_id=".$myrec['paymethod_id'].",
      poso=".number_format($myrec['paymethod_total'],10, '.','').",
      asset_id=".$myrec['asset_id'].",
      mydate_edit=now(),
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where acc_pay_method_id=".$myrec['id_acc_pay_method']." 
      and acc_pay_id=".$id."
      and transaction_id=0";
    //echo '<pre>'.$sql;die();
   
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
    
    
    
  }
}
unset($myrec);


$gks_pricelist_item_id=0;




//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($not_del_id_acc_pay_method,true).print_r($orders_products,true).print_r($eidi_array,true)));
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($orders_products,true).'</pre>'));
//echo json_encode($return); die();



$has_ekdosi=false;
$save_but_message='';
if ($pay_state=='090ekdosi' and $is_xeirografi==0) {
  //ekdosi

  gks_pay_get_ekdosi_numbers();
  
}



//pay_state
$sql="update gks_acc_pay set ";
if ($pay_state!= '') {
  $sql.="pay_state='".$db_link->escape_string($pay_state)."', ";
}
if ($is_xeirografi!=0) {
  $sql.="pay_acc_number_int=".$pay_acc_number_int_user.", ";
  if ($pay_state=='090ekdosi' and $pay_acc_ekdosi_date_old=='') {
    $sql.="pay_acc_ekdosi_date=now(),";
  }
} else {
  if ($has_ekdosi) {
    $sql.="pay_acc_number_int=".$pay_acc_number_int_new.",
           pay_acc_number_str='".$db_link->escape_string($pay_acc_number_str_new)."',
           pay_acc_ekdosi_date=now(),
           pay_acc_seira_code='".$db_link->escape_string($pay_acc_seira_code_new)."',";
  }
}

//price=".number_format($price, 10, '.', '').",
$sql.="
company_id=".$company_id.",
pay_acc_journal_id=".$pay_acc_journal_id.",
pay_acc_seira_id=".$pay_acc_seira_id.",
company_sub_id=".$company_sub_id.",
pay_date=".($pay_date == '' ? 'null' : "'".$db_link->escape_string($pay_date)."'") .", 
user_id=".$user_id.",



note_doc='".$db_link->escape_string($note_doc)."',
note_logistirio='".$db_link->escape_string($note_logistirio)."',


affect_balance=".$affect_balance.",
affect_balance_all_poso=".$affect_balance_all_poso.",
affect_balance_all_poso_type='".$db_link->escape_string($affect_balance_all_poso_type)."',";

if ($affect_balance == 0) {
  $affect_balance_poso=0;
} else {
  if ($affect_balance_all_poso==1) {
    switch ($affect_balance_all_poso_type) {
      case 'price_total':
        $affect_balance_poso=$gks_price_total;
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

$sql.="gks_price_total=".number_format($gks_price_total, 10, '.', '').",";



if ($pay_poso!=null and is_array($pay_poso)) {
  $sql.="pay_poso_str='".$db_link->escape_string(serialize($pay_poso))."',";
}

$sql.="user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'

where id_acc_pay = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }





$myarray_new=array();
$myarray_line_new=array();
$idiotites_new=get_acc_pay_details_txt($id, $myarray_new, $myarray_line_new); 

$sql="update gks_acc_pay set idiotites='".$db_link->escape_string(json_encode($myarray_new))."' where id_acc_pay = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
  




$sql="delete from gks_acc_pay_multiple_connected_marks where acc_pay_id=".$id;
if (count($not_del_id_acc_pay_multiple_connected_marks )>0) {
  $sql.=" and id_acc_pay_multiple_connected_marks not in (".implode(',', $not_del_id_acc_pay_multiple_connected_marks).")";
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
    $sql="insert into gks_acc_pay_multiple_connected_marks (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_pay_id,
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
    $sql="update gks_acc_pay_multiple_connected_marks set 
    mcm_mark='".$db_link->escape_string($myrec['mcm_mark'])."',
    mcm_acc_inv_id=".$myrec['mcm_acc_inv_id'].",
    mcm_acc_pay_id=".$myrec['mcm_acc_pay_id'].",
    mcm_whi_mov_id=".$myrec['mcm_whi_mov_id'].",
    mcm_aa=".$myrec['mcmaa'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_acc_pay_multiple_connected_marks=".$myrec['recid']."
    and acc_pay_id=".$id;  
    //echo '<pre>'.$sql; die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
}


$sql="select * from gks_acc_pay_method where acc_pay_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 










$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);






if ($is_new_rec == false) {

  gks_pay_sxolio_log($id,$row_old,$products_old,$extra_address_old,'',[],$gks_custom_row_old);
  
}

$balance_user=gks_balance_calc(['id' => $user_id]);

//

//echo '<pre>';print $pay_state_old.'|'.$pay_state.'|'; print_r($pay_poso);die();

if ($pay_state_old!='' and $pay_state!='' and $pay_state_old!='090ekdosi' and $pay_state=='090ekdosi') {
  
  
  gks_pay_poso_ekdosi($id,$pay_poso,$affect_balance_pros);
  
  

}


$return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> $redirect,'save_but_message' => base64_encode($save_but_message));
echo json_encode($return); die();







function gks_pay_to_draft($id) {
  
  global $db_link;

  $sql_credit_memo="select id_acc_pay from gks_acc_pay where credit_memo_for_acc_pay_id=".$id;
  $result_credit_memo = $db_link->query($sql_credit_memo);  
  if (!$result_credit_memo) {
    debug_mail(false,'error sql',$result_credit_memo);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result_credit_memo->num_rows>=1) {
    $row_credit_memo=$result_credit_memo->fetch_assoc();
    $message=gks_lang('Για αυτό το παραστατικό υπάρχει συσχετιζόμενο πιστωτικό παραστατικό με').' <br>ID :<b>'.$row_credit_memo['id_acc_pay'].'</b><br>'.
    '<a class="gks_link" href="admin-acc-pay-item.php?id='.$row_credit_memo['id_acc_pay'].'">'.gks_lang('Προβολή').'</a><br>'.
    gks_lang('Οπότε, δεν μπορεί να γίνει αυτή η αλλαγή, εκτός και εάν διαγραφεί το συσχετιζόμενο πιστωτικό παραστατικό');
    debug_mail(false,'credit_memo_for_acc_pay_id='.$row_credit_memo['id_acc_pay'],$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }


  

    
  //die('<pre>ssss');
  
  $sql="select * from gks_acc_pay where id_acc_pay=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_pay = $result->fetch_assoc();
  $pay_acc_seira_id=$row_pay['pay_acc_seira_id'];
  $pay_acc_number_int_old=$row_pay['pay_acc_number_int'];
  
  $sql="select * from gks_acc_seires where id_acc_seira=".$pay_acc_seira_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_seira = $result->fetch_assoc();
  $prev_number=$row_seira['next_number']-$row_seira['number_step'];
  
  
  $warning_message='';
  if ($prev_number!=$pay_acc_number_int_old) {
    $warning_message=
          gks_lang('Επόμενος αριθμός σειράς').': <b>'.$row_seira['next_number'].'</b><br>'.
          gks_lang('Βήμα σειράς').': <b>'.$row_seira['number_step'].'</b><br>'.
          gks_lang('Τρέχον αριθμός παραστατικού').': <b>'.$pay_acc_number_int_old.'</b> (<>'.
          $row_seira['next_number'].'-'.$row_seira['number_step'].')';
          
    debug_mail(false,'prev_number is not equal pay_acc_number_int_old',$prev_number.' != '.$pay_acc_number_int_old.' '.$warning_message);
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Έχουν εκδοθεί ενδιάμεσα παραστατικά σε αυτήν την σειρά και θα δημιουργηθούν κενά στην αρίθμηση').'<br>'.$temp));
    //echo json_encode($return); die(); 
  } else {  
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $sql="update gks_acc_seires set next_number=next_number-number_step where id_acc_seira=".$pay_acc_seira_id;
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
    where acc_seira_id=".$pay_acc_seira_id." and acc_pay_id=".$id." and disabled_date is null";
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    

  }
  //$return = array('success' => false, 'message' => base64_encode('sssssss'));
  //echo json_encode($return); die();
  
  if ($prev_number==$pay_acc_number_int_old) {
    $sql="update gks_acc_pay set pay_state='010draft', pay_acc_number_int=0, pay_acc_number_str=null,pay_acc_ekdosi_date=null where id_acc_pay=".$id;
  } else {
    $sql="update gks_acc_pay set pay_state='010draft' where id_acc_pay=".$id;
  }
  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $affect_balance_pros=intval($row_pay['affect_balance_pros']);
  gks_acc_pay_poso_revert($id,$affect_balance_pros);
  
    
  return $warning_message;
}


function gks_pay_get_ekdosi_numbers() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $pay_acc_number_int_old;
  global $pay_acc_number_int_new;
  global $pay_acc_number_str_new;
  global $pay_acc_seira_code_new;
  global $pay_acc_seira_id;
  global $has_ekdosi;
  global $save_but_message;
  global $id;
  global $pay_state;
  
  //die('<pre>pay_acc_number_int_old:'.$pay_acc_number_int_old);
  if ($pay_acc_number_int_old>0) {
    $sql_auto_number="select auto_number from gks_acc_seires_auto_numbers where disabled_date is null and acc_seira_id=".$pay_acc_seira_id." and acc_pay_id=".$id;
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_auto_number->num_rows>=1) {
      $row_auto_number = $result_auto_number->fetch_assoc();    
      $pay_acc_number_int_old=$row_auto_number['auto_number'];
      $pay_acc_number_int_new=$row_auto_number['auto_number'];

      $sql="select * from gks_acc_seires where id_acc_seira=".$pay_acc_seira_id;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      $row_seira = $result->fetch_assoc();
      $pay_acc_seira_code_new=trim_gks($row_seira['seira_code']);
      $seires_prefix=trim_gks($row_seira['prefix']);
      $seires_suffix=trim_gks($row_seira['suffix']);
      $seires_number_size=$row_seira['number_size'];
      $pay_acc_number_str_new=$seires_prefix.str_pad($pay_acc_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
      $has_ekdosi=true;
    }
  }
  
  if ($pay_acc_number_int_old==0) {
    $pay_state='';
    
    
    
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select * from gks_acc_seires where id_acc_seira=".$pay_acc_seira_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $row_seira = $result->fetch_assoc();
    $pay_acc_seira_code_new=trim_gks($row_seira['seira_code']);
    $seires_prefix=trim_gks($row_seira['prefix']);
    $seires_suffix=trim_gks($row_seira['suffix']);
    $seires_number_size=$row_seira['number_size'];
    $pay_acc_number_int_new=$row_seira['next_number'];
    $pay_acc_number_str_new=$seires_prefix.str_pad($pay_acc_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
    //$save_but_message='<pre>'.$pay_acc_number_str_new;
    
    $sql="update gks_acc_seires set next_number=next_number+number_step where id_acc_seira=".$pay_acc_seira_id;
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
  
    $pay_state='090ekdosi';
    $has_ekdosi=true;
    if ($save_but_message!='') {
      $save_but_message=gks_lang('Το παραστατικό έχει αποθηκευτεί αλλά δεν έχει εκδοθεί διότι').':<br>'.$save_but_message;
    }
    
    $sql="insert into gks_acc_seires_auto_numbers (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_seira_id,acc_pay_id,auto_number
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$pay_acc_seira_id.",".$id.",".$pay_acc_number_int_new."
    )";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
  
}

function gks_acc_pay_poso_revert($id,$affect_balance_pros) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;

  if ($affect_balance_pros==1) $affect_balance_pros_revert=-1; else $affect_balance_pros_revert=1;
  //echo '<pre>ssssssssss ';print_r($pay_poso);
  //echo $affect_balance_pros.'||'.$affect_balance_pros_revert.'||';print_r($pay_poso); die();

  
  $gks_orders_ids=array();
  $sql="select order_id from gks_acc_pay_poso_order where acc_pay_id=".$id." and order_id>0 group by order_id";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  while ($row = $result->fetch_assoc()) {
    $gks_orders_ids[]=$row['order_id'];
  }
    
  $gks_acc_inv_ids=array();
  $sql="select acc_inv_id from gks_acc_pay_poso_acc_inv where acc_pay_id=".$id." and acc_inv_id>0 group by acc_inv_id";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  while ($row = $result->fetch_assoc()) {
    $gks_acc_inv_ids[]=$row['acc_inv_id'];
  }
  
  $gks_hotel_reservation_ids=array();
  $sql="select hotel_reservation_id from gks_acc_pay_poso_hotel_reservation where acc_pay_id=".$id." and hotel_reservation_id>0 group by hotel_reservation_id";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  while ($row = $result->fetch_assoc()) {
    $gks_hotel_reservation_ids[]=$row['hotel_reservation_id'];
  }
  
  
  $sql="delete from gks_acc_pay_poso_order where acc_pay_id=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $sql="delete from gks_acc_pay_poso_acc_inv where acc_pay_id=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  $sql="delete from gks_acc_pay_poso_hotel_reservation where acc_pay_id=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  

          
  
  //debug_mail(false,'gks_orders_ids',print_r($gks_orders_ids,true));
  if (count($gks_orders_ids)>0) {
    
    $sql="delete from gks_object_rel 
    where (object_name1='gks_acc_pay' and object_id1=".$id." 
       and object_name2='gks_orders'  and object_id2 in (".implode(',',$gks_orders_ids).")) or 
          (object_name2='gks_acc_pay' and object_id2=".$id." 
       and object_name1='gks_orders'  and object_id1 in (".implode(',',$gks_orders_ids)."))";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    
    
    
    $sql="SELECT gks_orders.id_order, gks_orders.order_state,
    CASE
      WHEN (order_state='060registered' or order_state='070inproduction' or 
           order_state='090indelivery' or order_state='095execute' or order_state='100completed' or order_state='110payment') and affect_balance=1
        THEN affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    sumtable.sum_poso
    FROM gks_orders LEFT JOIN (
      SELECT order_id, Sum(poso) AS sum_poso
      FROM gks_acc_pay_poso_order
      WHERE order_id In (".implode(',',$gks_orders_ids).")
      GROUP BY order_id
    ) AS sumtable ON gks_orders.id_order = sumtable.order_id
    WHERE gks_orders.id_order In (".implode(',',$gks_orders_ids).")
    AND gks_orders.order_state In ('110payment')";
    //debug_mail(false,'gks_orders_ids sql',$sql);

    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    $set_100payment=array();
    while ($row = $result->fetch_assoc()) {
      $set_100payment[]=$row;
    }
    foreach ($set_100payment as &$row) {
      $row['will_be']=false;
      $row['affect_balance_calc']=floatval($row['affect_balance_calc']);
      $row['sum_poso']=$affect_balance_pros_revert*floatval($row['sum_poso']);
      $diafora=round($row['affect_balance_calc']-$row['sum_poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $row['diafora']=$diafora;
      if (abs($diafora) != 0) $row['will_be']=true;
      if ($row['will_be']) {
        $sql="update gks_orders set 
        mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."',
        order_state='100completed'
        where id_order=".$row['id_order']." limit 1";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
      }
    }
    unset($row);
    //debug_mail(false,'gks_orders_ids set_100payment',print_r($set_100payment,true));
    //print '<pre>';print_r($set_100payment);die();
  }    

  
  //debug_mail(false,'gks_acc_inv_ids',print_r($gks_acc_inv_ids,true));
  if (count($gks_acc_inv_ids)>0) {
    
    $sql="delete from gks_object_rel 
    where (object_name1='gks_acc_pay' and object_id1=".$id." 
       and object_name2='gks_acc_inv' and object_id2 in (".implode(',',$gks_acc_inv_ids).")) or 
          (object_name2='gks_acc_pay' and object_id2=".$id." 
       and object_name1='gks_acc_inv' and object_id1 in (".implode(',',$gks_acc_inv_ids)."))";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}

    
    $sql="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.inv_state,
    CASE
      WHEN (inv_state='080listing' or inv_state='090ekdosi' or inv_state='100payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    sumtable.sum_poso
    FROM gks_acc_inv LEFT JOIN (
      SELECT acc_inv_id, Sum(poso) AS sum_poso
      FROM gks_acc_pay_poso_acc_inv
      WHERE acc_inv_id In (".implode(',',$gks_acc_inv_ids).")
      GROUP BY acc_inv_id
    ) AS sumtable ON gks_acc_inv.id_acc_inv = sumtable.acc_inv_id
    WHERE gks_acc_inv.id_acc_inv In (".implode(',',$gks_acc_inv_ids).")
    AND gks_acc_inv.inv_state In ('100payment')";
    //debug_mail(false,'gks_acc_inv_ids sql',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    $set_100payment=array();
    while ($row = $result->fetch_assoc()) {
      $set_100payment[]=$row;
    }
    foreach ($set_100payment as &$row) {
      $row['will_be']=false;
      $row['affect_balance_calc']=floatval($row['affect_balance_calc']);
      $row['sum_poso']=$affect_balance_pros_revert*floatval($row['sum_poso']);
      $diafora=round($row['affect_balance_calc']-$row['sum_poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $row['diafora']=$diafora;
      if (abs($diafora) != 0) $row['will_be']=true;
      if ($row['will_be']) {
        $sql="update gks_acc_inv set 
        mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."',
        inv_state='090ekdosi'
        where id_acc_inv=".$row['id_acc_inv']." limit 1";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
      }
    }
    unset($row);
    //debug_mail(false,'gks_acc_inv_ids set_100payment',print_r($set_100payment,true));
    //print '<pre>';print_r($set_100payment);die();
  }
  
  if (count($gks_hotel_reservation_ids)>0) {
    
    $sql="delete from gks_object_rel 
    where (object_name1='gks_acc_pay' and object_id1=".$id." 
       and object_name2='gks_hotel_reservation'  and object_id2 in (".implode(',',$gks_hotel_reservation_ids).")) or 
          (object_name2='gks_acc_pay' and object_id2=".$id." 
       and object_name1='gks_hotel_reservation'  and object_id1 in (".implode(',',$gks_hotel_reservation_ids)."))";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    
    
    
    $sql="SELECT gks_hotel_reservation.id_hotel_reservation, gks_hotel_reservation.reservation_status,
    CASE
      WHEN (reservation_status='070wait_payment' or reservation_status='080confirm' or reservation_status='100completed' or reservation_status='110payment') 
        and affect_balance=1
        THEN affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    sumtable.sum_poso,
    gks_hotel_reservation.hotel_id,
    gks_hotel_reservation.check_in,
    gks_hotel_reservation.check_out
    FROM gks_hotel_reservation LEFT JOIN (
      SELECT hotel_reservation_id, Sum(poso) AS sum_poso
      FROM gks_acc_pay_poso_hotel_reservation
      WHERE hotel_reservation_id In (".implode(',',$gks_hotel_reservation_ids).")
      GROUP BY hotel_reservation_id
    ) AS sumtable ON gks_hotel_reservation.id_hotel_reservation = sumtable.hotel_reservation_id
    WHERE gks_hotel_reservation.id_hotel_reservation In (".implode(',',$gks_hotel_reservation_ids).")
    AND gks_hotel_reservation.reservation_status In ('110payment')";
    //debug_mail(false,'gks_hotel_reservation_ids sql',$sql);

    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    $set_100payment=array();
    while ($row = $result->fetch_assoc()) {
      $set_100payment[]=$row;
    }
    foreach ($set_100payment as &$row) {
      $row['will_be']=false;
      $row['affect_balance_calc']=floatval($row['affect_balance_calc']);
      $row['sum_poso']=$affect_balance_pros_revert*floatval($row['sum_poso']);
      $diafora=round($row['affect_balance_calc']-$row['sum_poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $row['diafora']=$diafora;
      if (abs($diafora) != 0) $row['will_be']=true;
      if ($row['will_be']) {
        $sql="update gks_hotel_reservation set 
        mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."',
        reservation_status='100completed'
        where id_hotel_reservation=".$row['id_hotel_reservation']." limit 1";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}

        $id_hotel=$row['hotel_id'];
        $hotel_params=gks_hotel_get_params($id_hotel);
        $days_round=hotel_round_days($id_hotel, $row['check_in'], $row['check_out']);
        
        $sql_rooms="SELECT gks_hotel_reservation_room.id_hotel_reservation_room, gks_hotel_reservation_room.hotel_room_id, gks_hotel_room.hotel_room_type_id
        FROM gks_hotel_reservation_room 
        LEFT JOIN gks_hotel_room ON gks_hotel_reservation_room.hotel_room_id = gks_hotel_room.id_hotel_room
        WHERE gks_hotel_reservation_room.hotel_reservation_id=".$row['id_hotel_reservation']."
        ORDER BY gks_hotel_reservation_room.id_hotel_reservation_room";
        $result_rooms = $db_link->query($sql_rooms);        
        if (!$result_rooms) {
          debug_mail(false,'error sql',$sql_rooms);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
        $roolist_day=array();
        while ($row_rooms = $result_rooms->fetch_assoc()) {
          $roolist_day[]=array(
            'delete'=>0, 
            'recid'=> $row_rooms['id_hotel_reservation_room'], 
            'hotel_room_id'=> $row_rooms['hotel_room_id'], 
            'hotel_type_room_id'=>$row_rooms['hotel_room_type_id'],
          );        
        }
        
        if (count($roolist_day)>0) {
          //print $row['id_hotel_reservation'].'|'.$id_hotel.'|'; print_r($days_round);print_r($roolist_day);die();
          gks_hotel_reservation_room_day_recs($row['id_hotel_reservation'],$roolist_day,
            '100completed',
            $days_round['check_in_round_time'],$days_round['check_out_round_time']
          );
        }
        
       


      }
    }
    unset($row);
    //debug_mail(false,'gks_hotel_reservation_ids set_100payment',print_r($set_100payment,true));
    //print '<pre>';print_r($set_100payment);die();
  }
  
        
}


function gks_pay_poso_ekdosi($id,$pay_poso,$affect_balance_pros) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
    
  $gks_orders_ids=array();
  $gks_acc_inv_ids=array();
  $gks_hotel_reservation_ids=array();
  
  if ($affect_balance_pros==1) $affect_balance_pros_revert=-1; else $affect_balance_pros_revert=1;
  //echo '<pre>ssssssssss ';print_r($pay_poso);
  //echo $affect_balance_pros.'||'.$affect_balance_pros_revert.'||';print_r($pay_poso); die();
  
  foreach ($pay_poso as $pitem) {
    if ($pitem['f']=='order') {
      $gks_orders_ids[]=intval($pitem['i']);
      $sql="insert into gks_acc_pay_poso_order (
        user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
        acc_pay_id,order_id,poso
      ) values (
        ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
        ".$id.",".intval($pitem['i']).",".number_format(floatval($pitem['v']),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      
      $sql="select * from gks_object_rel
      where (object_name1='gks_acc_pay' and object_id1=".$id." and 
             object_name2='gks_orders'  and object_id2=".intval($pitem['i']).") or 
            (object_name1='gks_orders'  and object_id1=".intval($pitem['i'])." and 
             object_name2='gks_acc_pay' and object_id2=".$id.")";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}

      if ($result->num_rows==0) {
        $sql="insert into gks_object_rel (
          user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
          object_name1,object_id1,object_name2,object_id2
        ) values (
          ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
          'gks_acc_pay',".$id.",'gks_orders',".intval($pitem['i'])."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
      }
    } else if ($pitem['f']=='inv') {
      $gks_acc_inv_ids[]=intval($pitem['i']);
      $sql="insert into gks_acc_pay_poso_acc_inv (
        user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
        acc_pay_id,acc_inv_id,poso
      ) values (
        ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
        ".$id.",".intval($pitem['i']).",".number_format(floatval($pitem['v']),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}

      $sql="select * from gks_object_rel
      where (object_name1='gks_acc_pay' and object_id1=".$id." and 
             object_name2='gks_acc_inv' and object_id2=".intval($pitem['i']).") or 
            (object_name1='gks_acc_inv' and object_id1=".intval($pitem['i'])." and 
             object_name2='gks_acc_pay' and object_id2=".$id.")";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      
      if ($result->num_rows==0) {
        $sql="insert into gks_object_rel (
          user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
          object_name1,object_id1,object_name2,object_id2
        ) values (
          ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
          'gks_acc_pay',".$id.",'gks_acc_inv',".intval($pitem['i'])."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
      }
    } else if ($pitem['f']=='reservation') {
      $gks_hotel_reservation_ids[]=intval($pitem['i']);
      $sql="insert into gks_acc_pay_poso_hotel_reservation (
        user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
        acc_pay_id,hotel_reservation_id,poso
      ) values (
        ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
        ".$id.",".intval($pitem['i']).",".number_format(floatval($pitem['v']),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
      
      $sql="select * from gks_object_rel
      where (object_name1='gks_acc_pay' and object_id1=".$id." and 
             object_name2='gks_hotel_reservation'  and object_id2=".intval($pitem['i']).") or 
            (object_name1='gks_hotel_reservation'  and object_id1=".intval($pitem['i'])." and 
             object_name2='gks_acc_pay' and object_id2=".$id.")";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}

      if ($result->num_rows==0) {
        $sql="insert into gks_object_rel (
          user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
          object_name1,object_id1,object_name2,object_id2
        ) values (
          ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
          'gks_acc_pay',".$id.",'gks_hotel_reservation',".intval($pitem['i'])."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
      }
    
    
    
    }
    
    
    
  }
  if (count($gks_orders_ids)>0) {
    $sql="SELECT gks_orders.id_order, gks_orders.order_state,
    CASE
      WHEN (order_state='060registered' or order_state='070inproduction' or 
           order_state='090indelivery' or order_state='095execute' or order_state='100completed' or order_state='110payment') and affect_balance=1
        THEN affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    sumtable.sum_poso
    FROM gks_orders LEFT JOIN (
      SELECT order_id, Sum(poso) AS sum_poso
      FROM gks_acc_pay_poso_order
      WHERE order_id In (".implode(',',$gks_orders_ids).")
      GROUP BY order_id
    ) AS sumtable ON gks_orders.id_order = sumtable.order_id
    WHERE gks_orders.id_order In (".implode(',',$gks_orders_ids).")
    AND gks_orders.order_state In ('100completed')
    AND sumtable.order_id Is Not Null";

    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    $set_100payment=array();
    while ($row = $result->fetch_assoc()) {
      $set_100payment[]=$row;
    }
    foreach ($set_100payment as &$row) {
      $row['will_be']=false;
      $row['affect_balance_calc']=floatval($row['affect_balance_calc']);
      $row['sum_poso']=$affect_balance_pros_revert*floatval($row['sum_poso']);
      $diafora=round($row['affect_balance_calc']-$row['sum_poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $row['diafora']=$diafora;
      if ($row['affect_balance_calc']!=0 and $row['sum_poso']!=0 and abs($diafora) == 0) $row['will_be']=true;
      if ($row['will_be']) {
        $sql="update gks_orders set 
        mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."',
        order_state='110payment'
        where id_order=".$row['id_order']." limit 1";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
      }
    }
    unset($row);
    //print '<pre>';print_r($set_100payment);die();
  }  
  if (count($gks_acc_inv_ids)>0) {
    $sql="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.inv_state,
    CASE
      WHEN (inv_state='080listing' or inv_state='090ekdosi' or inv_state='100payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    sumtable.sum_poso
    FROM gks_acc_inv LEFT JOIN (
      SELECT acc_inv_id, Sum(poso) AS sum_poso
      FROM gks_acc_pay_poso_acc_inv
      WHERE acc_inv_id In (".implode(',',$gks_acc_inv_ids).")
      GROUP BY acc_inv_id
    ) AS sumtable ON gks_acc_inv.id_acc_inv = sumtable.acc_inv_id
    WHERE gks_acc_inv.id_acc_inv In (".implode(',',$gks_acc_inv_ids).")
    AND gks_acc_inv.inv_state In ('080listing','090ekdosi') 
    AND sumtable.acc_inv_id Is Not Null";

    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    $set_100payment=array();
    while ($row = $result->fetch_assoc()) {
      $set_100payment[]=$row;
    }
    foreach ($set_100payment as &$row) {
      $row['will_be']=false;
      $row['affect_balance_calc']=floatval($row['affect_balance_calc']);
      $row['sum_poso']=$affect_balance_pros_revert*floatval($row['sum_poso']);
      $diafora=round($row['affect_balance_calc']-$row['sum_poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $row['diafora']=$diafora;
      //echo '<pre>'.$diafora;die();
      if ($row['affect_balance_calc']!=0 and $row['sum_poso']!=0 and abs($diafora) == 0) $row['will_be']=true;
      if ($row['will_be']) {
        $sql="update gks_acc_inv set 
        mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."',
        inv_state='100payment'
        where id_acc_inv=".$row['id_acc_inv']." limit 1";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
      }
    }
    unset($row);
    //print '<pre>';print_r($set_100payment);die();
  }
  
  if (count($gks_hotel_reservation_ids)>0) {
    $sql="SELECT gks_hotel_reservation.id_hotel_reservation, gks_hotel_reservation.reservation_status,
    CASE
      WHEN (reservation_status='070wait_payment' or reservation_status='080confirm' or 
           reservation_status='100completed' or reservation_status='110payment') and affect_balance=1
        THEN affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    sumtable.sum_poso,
    gks_hotel_reservation.hotel_id,
    gks_hotel_reservation.check_in,
    gks_hotel_reservation.check_out
    FROM gks_hotel_reservation LEFT JOIN (
      SELECT hotel_reservation_id, Sum(poso) AS sum_poso
      FROM gks_acc_pay_poso_hotel_reservation
      WHERE hotel_reservation_id In (".implode(',',$gks_hotel_reservation_ids).")
      GROUP BY hotel_reservation_id
    ) AS sumtable ON gks_hotel_reservation.id_hotel_reservation = sumtable.hotel_reservation_id
    WHERE gks_hotel_reservation.id_hotel_reservation In (".implode(',',$gks_hotel_reservation_ids).")
    AND gks_hotel_reservation.reservation_status In ('100completed')
    AND sumtable.hotel_reservation_id Is Not Null";

    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    $set_100payment=array();
    while ($row = $result->fetch_assoc()) {
      $set_100payment[]=$row;
    }
    //print '<pre>';print $sql;die();
    foreach ($set_100payment as &$row) {
      $row['will_be']=false;
      $row['affect_balance_calc']=floatval($row['affect_balance_calc']);
      $row['sum_poso']=$affect_balance_pros_revert*floatval($row['sum_poso']);
      $diafora=round($row['affect_balance_calc']-$row['sum_poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $row['diafora']=$diafora;
      if ($row['affect_balance_calc']!=0 and $row['sum_poso']!=0 and abs($diafora) == 0) $row['will_be']=true;
      if ($row['will_be']) {
        $sql="update gks_hotel_reservation set 
        mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."',
        reservation_status='110payment'
        where id_hotel_reservation=".$row['id_hotel_reservation']." limit 1";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
        
        $id_hotel=$row['hotel_id'];
        $hotel_params=gks_hotel_get_params($id_hotel);
        $days_round=hotel_round_days($id_hotel, $row['check_in'], $row['check_out']);
        
        $sql_rooms="SELECT gks_hotel_reservation_room.id_hotel_reservation_room, gks_hotel_reservation_room.hotel_room_id, gks_hotel_room.hotel_room_type_id
        FROM gks_hotel_reservation_room 
        LEFT JOIN gks_hotel_room ON gks_hotel_reservation_room.hotel_room_id = gks_hotel_room.id_hotel_room
        WHERE gks_hotel_reservation_room.hotel_reservation_id=".$row['id_hotel_reservation']."
        ORDER BY gks_hotel_reservation_room.id_hotel_reservation_room";
        $result_rooms = $db_link->query($sql_rooms);        
        if (!$result_rooms) {
          debug_mail(false,'error sql',$sql_rooms);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die();}
        $roolist_day=array();
        while ($row_rooms = $result_rooms->fetch_assoc()) {
          $roolist_day[]=array(
            'delete'=>0, 
            'recid'=> $row_rooms['id_hotel_reservation_room'], 
            'hotel_room_id'=> $row_rooms['hotel_room_id'], 
            'hotel_type_room_id'=>$row_rooms['hotel_room_type_id'],
          );        
        }
        
        if (count($roolist_day)>0) {
          //print $row['id_hotel_reservation'].'|'.$id_hotel.'|'; print_r($days_round);print_r($roolist_day);die();
          gks_hotel_reservation_room_day_recs($row['id_hotel_reservation'],$roolist_day,
            '110payment',
            $days_round['check_in_round_time'],$days_round['check_out_round_time']
          );
        }
                  
      }
    }
    unset($row);
    //print '<pre>';print_r($set_100payment);die();
  }  
  
}

