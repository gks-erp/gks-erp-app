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
$my_page_title=gks_lang('Αποθήκευση Παραγγελίας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');







$_gks_session['temp_mypropertiesheight'] = 0;
if (isset($_POST['mypropertiesheight'])) $_gks_session['temp_mypropertiesheight']=intval($_POST['mypropertiesheight']); gks_erp_cookie_save();
$order_state=''; if (isset($_POST['order_state'])) $order_state=trim_gks(base64_decode($_POST['order_state']));

$mybasketarray=false;
$cache_file='';if (isset($_POST['cache_file']) and trim_gks($_POST['cache_file'])!='') $cache_file=trim_gks($_POST['cache_file']);
if ($cache_file!= '' and file_exists(GKS_CACHE.$cache_file)) {
  $mybasketarray=json_decode(file_get_contents(GKS_CACHE.$cache_file), true);
}
$order_state_old='';
$idiotites_old='';
$order_number_int_old=0;
$order_number_str_old='';
$order_ekdosi_date_old='';
$seira_code_old='';
$is_xeirografi_old=0;


$myarray_old=array();
$myarray_line_old=array();

$row_old=array();
$products_old=array();
$extra_address_old=array();



$all_products_for_balance=array();

$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
  $row_old['order_state']='010draft';
  $row_old['order_number_int']=0;  
} else {
  $sql=select_gks_orders($id)." where id_order=".$id;
  if (count($perm_id_company_ids)>0) $sql.=" and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
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
  $order_state_old=trim_gks($row_old['order_state']);
  $idiotites_old=get_order_details_txt($id, $myarray_old, $myarray_line_old); 
  $order_number_int_old=$row_old['order_number_int']; 
  $order_number_str_old=trim_gks($row_old['order_number_str']); 
  $order_ekdosi_date_old=trim_gks($row_old['order_ekdosi_date']); 
  $seira_code_old=trim_gks($row_old['seira_code']); 
  $is_xeirografi_old=trim_gks($row_old['is_xeirografi']); 



  $sql="SELECT gks_orders_products.*, gks_monades_metrisis.monada_descr, 
  gks_eshop_fpa_base.fpa_base_descr,
  gks_aade_katigoria_fpa.aade_katigoria_fpa_descr
  FROM ((gks_orders_products 
  LEFT JOIN gks_monades_metrisis ON gks_orders_products.product_monada_id = gks_monades_metrisis.id_monada)
  LEFT JOIN gks_eshop_fpa_base ON gks_orders_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base)
  LEFT JOIN gks_aade_katigoria_fpa ON gks_orders_products.product_fpa_aade_id = gks_aade_katigoria_fpa.id_aade_katigoria_fpa
  WHERE gks_orders_products.order_id=".$id."
  ORDER BY gks_orders_products.product_aa;";
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

  $gks_custom_prepare=gks_custom_table_item_prepare('gks_orders',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
    
}




$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;
if (in_array($row_old['order_state'], array(
      '030forcancellation','040cancelled','050rejected','060registered','070inproduction','080failed',
      '060registered','070inproduction','080failed','090indelivery','095execute','100completed','110payment'))) {
  $gks_lock=true;
} else {
  if ($row_old['order_number_int'] > 0 and $row_old['is_xeirografi']==0 and 
    in_array($row_old['order_state'],array(
      '005prodraft','010draft','020pending','025offer','055wait_payment'))) { 
    $gks_number_lock=true;
  }
}


//if ($credit_memo_for_acc_inv_id!=0) {
//  $gks_number_lock=true;
//  $gks_user_lock=true;
//}
//if ($cancel_for_acc_inv_id!=0) {
//  $gks_lock=true;
//}

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




$affect_balance=0; if (isset($_POST['affect_balance'])) $affect_balance=intval($_POST['affect_balance']);  
if ($affect_balance!=1) $affect_balance=0;
$affect_balance_all_poso=0; if (isset($_POST['affect_balance_all_poso'])) $affect_balance_all_poso=intval($_POST['affect_balance_all_poso']);  
if ($affect_balance_all_poso!=1) $affect_balance_all_poso=0;
$affect_balance_all_poso_type=''; if (isset($_POST['affect_balance_all_poso_type'])) $affect_balance_all_poso_type=trim_gks($_POST['affect_balance_all_poso_type']);
if ($affect_balance_all_poso_type=='') $affect_balance_all_poso_type='total_price_net'; 
$affect_balance_poso=0;  if (isset($_POST['affect_balance_poso']))  $affect_balance_poso=floatval($_POST['affect_balance_poso']);


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
    
//echo '<pre>';print $order_state;print_r($_POST);die();
//create_acc_inv

if ($order_state=='create_acc_inv') {
  unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  $id_create_acc_inv=gks_orders_create_acc_inv($id);
  if (is_array($id_create_acc_inv) and count($id_create_acc_inv) > 0) {
    //echo '<pre>'.$order_state.' '.$id_credit_memo; die();
    if (count($id_create_acc_inv)==1) {
      $message=gks_lang('Το παραστατικό έχει δημιουργηθεί').'<br>'.
      gks_lang('To ID του είναι').' <b>[1]</b><br>'.
      gks_lang('Θα πρέπει να το ελέγξετε και να το εκδώσετε').'<br>'.
      '<a class="gks_link" href="admin-acc-inv-item.php?id=[1]">'.gks_lang('Προβολή').'</a>';
      $message=str_replace('[1]',$id_create_acc_inv[0],$message);
    } else {
      
      $message=gks_lang('Έχουν δημιουργηθεί τα παρακάτω παραστατικά').':<br>';
      foreach ($id_create_acc_inv as $i => $value) {
         $message.='ID: <b>'.$value.'</b> '.
         '<a class="gks_link" href="admin-acc-inv-item.php?id='.$value.'">'.gks_lang('Προβολή').'</a><br>';
      } 
      $message.=gks_lang('Θα πρέπει να τα ελέγξετε και να τα εκδώσετε');
    }
    
    
    $return = array('success' => true, 'message' => base64_encode('ok'),'redirect'=> '','save_but_message' => base64_encode($message));
    echo json_encode($return); die();
    
  } else {
    debug_mail(false,'error gks_orders_create_acc_inv',$id.' '.$id_create_acc_inv);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα')),'redirect'=> '');
    echo json_encode($return); die();
  }
}


if ($order_state=='create_acc_pay') {
  unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  $id_create_acc_pay=gks_orders_create_acc_pay($id);
  if (is_array($id_create_acc_pay) and count($id_create_acc_pay) > 0) {
    //echo '<pre>'.$pay_state.' '.$id_credit_memo; die();
    if (count($id_create_acc_pay)==1) {
      $message=gks_lang('Η πληρωμή έχει δημιουργηθεί').'<br>'.
      gks_lang('To ID της είναι').' <b>[1]</b><br>'.
      gks_lang('Θα πρέπει να την ελέγξετε και να την εκδώσετε').'<br>'.
      '<a class="gks_link" href="admin-acc-pay-item.php?id=[1]">'.gks_lang('Προβολή').'</a>';
      $message=str_replace('[1]',$id_create_acc_pay[0],$message);
      
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

if ($gks_lock) {
  
  $warning_message='';
  $sql_ekdosi='';

  if ($order_state!='' and in_array($order_state, array(
      '010draft','030forcancellation','040cancelled','050rejected',
      '070inproduction','080failed','090indelivery','095execute','100completed'
      ))==false) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος κατάσταση').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}

  if ($order_state=='010draft' and $order_state_old!='010draft' and $is_xeirografi_old==0 and $order_number_int_old>0) {
    //echo '<pre>vvv';die();
    $warning_message=gks_order_to_draft($id);
    if ($warning_message!='') 
      $warning_message=gks_lang('Έγινε επαναφορά σε <b>Πρόχειρο</b> αλλά δεν μπόρεσε μηδενιστεί ο αριθμός του παραστατικού διότι').':<br>'.
                       $warning_message.'<br>'.gks_lang('Κάντε άμεσα τις αλλαγές και ξανα εκδώστε το').'<br>'.gks_lang('Διαφορετικά θα δημιουργηθεί κενό στην αρίθμηση της σειράς');
                        
  }
  
  if ($_POST['ddate'] == '__/__/____') $_POST['ddate']='';
  $ddate=trim_gks(stripslashes(urldecode($_POST['ddate'])));
  if ($ddate!='') {
    $ddate = mystrtodb_s($ddate.' 00:00:00');
  }
  if ($_POST['mdate_expire'] == '__/__/____ __:__') $_POST['mdate_expire']='';
  $mdate_expire=trim_gks(stripslashes(urldecode($_POST['mdate_expire'])));
  if ($mdate_expire!='') {
    $mdate_expire = mystrtodb($mdate_expire);
  }  
  $online_enable=0;if (isset($_POST['online_enable'])) $online_enable=intval($_POST['online_enable']);  
  $online_password=''; if (isset($_POST['online_password'])) $online_password=trim_gks(base64_decode($_POST['online_password']));
  $online_template_html_id=''; if (isset($_POST['online_template_html_id'])) $online_template_html_id=intval($_POST['online_template_html_id']);
    
  if ($online_enable!=0 and $online_template_html_id<=0) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε ένα Πρότυπο HTML')));
    echo json_encode($return); die(); }
    
  $note_doc=''; if (isset($_POST['note_doc'])) $note_doc=trim_gks(base64_decode($_POST['note_doc']));
  $note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));
  $order_priority=0; if (isset($_POST['order_priority'])) $order_priority=intval($_POST['order_priority']);  

  $note_production=''; if (isset($_POST['note_production'])) $note_production=trim_gks(base64_decode($_POST['note_production']));

  $tropos_apostolis=0; if (isset($_POST['tropos_apostolis'])) $tropos_apostolis=intval($_POST['tropos_apostolis']);  
  $tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);  
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
  


  $gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_orders');
    
  
  $sql="update gks_orders set ";
  if ($order_state!= '') {
    $sql.="order_state='".$db_link->escape_string($order_state)."', ";
  }
  
  $sql.=$sql_ekdosi;
  
  $sql.="
  ddate=".($ddate == '' ? 'null' : "'".$db_link->escape_string($ddate)."'") .", 
  mdate_expire=".($mdate_expire == '' ? 'null' : "'".$db_link->escape_string($mdate_expire)."'") .", 
  online_enable=".$online_enable.",
  online_password='".$db_link->escape_string($online_password)."',
  online_template_html_id=".$online_template_html_id.",
  
  note_doc='".$db_link->escape_string($note_doc)."',
  note_logistirio='".$db_link->escape_string($note_logistirio)."',
  order_priority=".$order_priority.",
  note_production='".$db_link->escape_string($note_production)."',
  tropos_apostolis=".$tropos_apostolis.",
  tropos_pliromis=".$tropos_pliromis.",
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
  where id_order = ".$id." limit 1";  
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
      $sql="update gks_orders set ".$sql." where id_order=".$id;
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
//   "\naade_send:".$aade_send.
//   "\norder_state:".$inv_state.
//   "\ncancel_for_order_id:".$cancel_for_order_id
//  ));echo json_encode($return); die();
  
  
  
  $sql="update gks_orders_products set p_order_state='".$db_link->escape_string($order_state!='' ? $order_state : $order_state_old)."' where order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}    
  
  gks_whi_mov_balance_calc($all_products_for_balance);
  gks_whi_after_balance_for_order($id);
  

  
  gks_order_sxolio_log($id,$row_old,$products_old,$extra_address_old,'',$gks_custom_row_old);
  
    

  
  $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
  //echo '<pre>'; echo $row_old['user_id'];die();
  
  if ($GKS_ORDERS_PRODUCTION) {
    //echo '<pre>';echo time();die();
    
    gks_order_production_warehouses_set($id);
    gks_production_order_sintagi($id);
    gks_whi_mov_balance_calc_for_production_sintagi($id);
    
    
    gks_production_order_ergasies($id);
    gks_production_order_calc_ergasies_setready($id);
    gks_production_order_calc_ergasies_tree($id);
    
    
  }
  //echo 'ggggggggggg';
  
  gks_plugins_functions_run('admin_orders_item_exec_after_save',array(
    'id'=>&$id,
  ));
  
  $return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> '','save_but_message' => base64_encode($warning_message));
  echo json_encode($return); die();        
  
  $return = array('success' => false, 'message' => base64_encode('fffffff φφφφ'));
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
  
$order_journal_id=0;if (isset($_POST['order_journal_id'])) $order_journal_id=intval($_POST['order_journal_id']);
if ($gks_number_lock) $order_journal_id=$row_old['order_journal_id'];
if ($order_journal_id<=0) {
  debug_mail(false,'order_journal_id is not found',$order_journal_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Ημερολόγιο')));
  echo json_encode($return); die();}  
  
$order_seira_id=0;if (isset($_POST['order_seira_id'])) $order_seira_id=intval($_POST['order_seira_id']);
if ($gks_number_lock) $order_seira_id=$row_old['order_seira_id'];
if ($order_seira_id<=0) {
  debug_mail(false,'order_seira_id is not found',$order_seira_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Σειρά')));
  echo json_encode($return); die();}  

$order_number_int_user=0;if (isset($_POST['order_number_int'])) $order_number_int_user=intval($_POST['order_number_int']);

$sql="SELECT gks_acc_seires.id_acc_seira,gks_acc_seires.is_xeirografi
FROM (((gks_acc_seires 
LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE eidos_parastatikou_type_id in (31,32) AND id_acc_eidos_parastatikou not in (702,703,704) 
AND gks_acc_seires.is_disable=0 AND gks_acc_journal.is_disable=0 
AND gks_company.company_disable=0 
AND gks_acc_seires.id_acc_seira=".$order_seira_id." 
AND gks_acc_journal.id_acc_journal=".$order_journal_id." 
AND gks_company.id_company=".$company_id;

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
  debug_mail(false,'company - journal',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο συνδυασμός εταιρείας/υποκαταστήματος, ημερολογίου και σειράς')));
  echo json_encode($return); die();}
$row_seira = $result->fetch_assoc();
$is_xeirografi=$row_seira['is_xeirografi'];

if ($order_state=='010draft' and $order_state_old!='010draft' and $is_xeirografi_old==0 and $order_number_int_old>0) {
  //echo '<pre>vvv';die();
 gks_order_to_draft($id);
}



if ($order_state=='080listing' and $is_xeirografi==0) {
  debug_mail(false,'080listing and is_xeirografi',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η σειρά είναι χειρόγραφη, άρα η παραγγελία θα πρέπει να εκδοθεί και όχι να καταχωρηθεί')));
  echo json_encode($return); die();}    
  
if ($order_state=='060registered' and $is_xeirografi!=0) {
  debug_mail(false,'060registered and is_xeirografi',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η σειρά είναι μηχανογραφημένη, άρα η παραγγελία θα πρέπει να καταχωρηθεί και όχι να εκδοθεί')));
  echo json_encode($return); die();}



  
$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$order_occasion_id=0; if (isset($_POST['order_occasion_id'])) $order_occasion_id=intval($_POST['order_occasion_id']);

$dr_user_first_name=''; if (isset($_POST['dr_user_first_name'])) $dr_user_first_name=trim_gks(base64_decode($_POST['dr_user_first_name']));
$dr_user_last_name=''; if (isset($_POST['dr_user_last_name'])) $dr_user_last_name=trim_gks(base64_decode($_POST['dr_user_last_name']));
$dr_user_email=''; if (isset($_POST['dr_user_email'])) $dr_user_email=trim_gks(base64_decode($_POST['dr_user_email']));
$dr_user_mobile=''; if (isset($_POST['dr_user_mobile'])) $dr_user_mobile=trim_gks(base64_decode($_POST['dr_user_mobile']));
$dr_user_lang=''; if (isset($_POST['dr_user_lang'])) $dr_user_lang=trim_gks(base64_decode($_POST['dr_user_lang']));
$dr_user_ma_odos=''; if (isset($_POST['dr_user_ma_odos'])) $dr_user_ma_odos=trim_gks(base64_decode($_POST['dr_user_ma_odos']));
$dr_user_ma_arithmos=''; if (isset($_POST['dr_user_ma_arithmos'])) $dr_user_ma_arithmos=trim_gks(base64_decode($_POST['dr_user_ma_arithmos']));
$dr_user_ma_orofos=''; if (isset($_POST['dr_user_ma_orofos'])) $dr_user_ma_orofos=trim_gks(base64_decode($_POST['dr_user_ma_orofos']));
$dr_user_ma_perioxi=''; if (isset($_POST['dr_user_ma_perioxi'])) $dr_user_ma_perioxi=trim_gks(base64_decode($_POST['dr_user_ma_perioxi']));
$dr_user_ma_poli=''; if (isset($_POST['dr_user_ma_poli'])) $dr_user_ma_poli=trim_gks(base64_decode($_POST['dr_user_ma_poli']));
$dr_user_ma_tk=''; if (isset($_POST['dr_user_ma_tk'])) $dr_user_ma_tk=trim_gks(base64_decode($_POST['dr_user_ma_tk']));
$dr_user_ma_country_id=0; if (isset($_POST['dr_user_ma_country_id'])) $dr_user_ma_country_id=intval($_POST['dr_user_ma_country_id']);
$dr_user_ma_nomos_id=0; if (isset($_POST['dr_user_ma_nomos_id'])) $dr_user_ma_nomos_id=intval($_POST['dr_user_ma_nomos_id']);
$form_parastatiko=0; if (isset($_POST['form_parastatiko'])) $form_parastatiko=intval($_POST['form_parastatiko']);

if ($form_parastatiko == 0) {
  $dr_user_eponimia=''; 
  $dr_user_title=''; 
  $dr_user_afm=''; 
  $dr_user_doy=''; 
  $dr_user_epaggelma='';
} else {
  $dr_user_eponimia=''; if (isset($_POST['dr_user_eponimia'])) $dr_user_eponimia=trim_gks(base64_decode($_POST['dr_user_eponimia']));
  $dr_user_title=''; if (isset($_POST['dr_user_title'])) $dr_user_title=trim_gks(base64_decode($_POST['dr_user_title']));
  $dr_user_afm=''; if (isset($_POST['dr_user_afm'])) $dr_user_afm=trim_gks(base64_decode($_POST['dr_user_afm']));
  $dr_user_doy=''; if (isset($_POST['dr_user_doy'])) $dr_user_doy=trim_gks(base64_decode($_POST['dr_user_doy']));
  $dr_user_epaggelma=''; if (isset($_POST['dr_user_epaggelma'])) $dr_user_epaggelma=trim_gks(base64_decode($_POST['dr_user_epaggelma']));
}



$form_select_apostoli=-1; if (isset($_POST['form_select_apostoli'])) $form_select_apostoli=intval($_POST['form_select_apostoli']);
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

if ($form_parastatiko >= 0) {
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
} 




if ($user_id<=0) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποιον πελάτη')));
    echo json_encode($return); die();}


if ($_POST['order_date'] == '__/__/____ __:__') $_POST['order_date']='';
$order_date=trim_gks(stripslashes(urldecode($_POST['order_date'])));
if ($order_date!='') {
  $order_date = mystrtodb($order_date);
}

if ($_POST['ddate'] == '__/__/____') $_POST['ddate']='';
$ddate=trim_gks(stripslashes(urldecode($_POST['ddate'])));
if ($ddate!='') {
  $ddate = mystrtodb_s($ddate.' 00:00:00');
}
if ($_POST['mdate_expire'] == '__/__/____ __:__') $_POST['mdate_expire']='';
$mdate_expire=trim_gks(stripslashes(urldecode($_POST['mdate_expire'])));
if ($mdate_expire!='') {
  $mdate_expire = mystrtodb($mdate_expire);
}
$online_enable=0;if (isset($_POST['online_enable'])) $online_enable=intval($_POST['online_enable']);  
$online_password=''; if (isset($_POST['online_password'])) $online_password=trim_gks(base64_decode($_POST['online_password']));
$online_template_html_id=''; if (isset($_POST['online_template_html_id'])) $online_template_html_id=intval($_POST['online_template_html_id']);
if ($online_enable!=0 and $online_template_html_id<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε ένα Πρότυπο HTML')));
  echo json_encode($return); die(); }

//$price=0; if (isset($_POST['price'])) $price=myParseCurrency(trim_gks(stripslashes(urldecode($_POST['price'])))); 
$product_id=0; if (isset($_POST['product_id'])) $product_id=intval($_POST['product_id']);

$note_doc=''; if (isset($_POST['note_doc'])) $note_doc=trim_gks(base64_decode($_POST['note_doc']));
$note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));
$order_priority=0; if (isset($_POST['order_priority'])) $order_priority=intval($_POST['order_priority']); 
$note_production=''; if (isset($_POST['note_production'])) $note_production=trim_gks(base64_decode($_POST['note_production']));

$tropos_apostolis=0; if (isset($_POST['tropos_apostolis'])) $tropos_apostolis=intval($_POST['tropos_apostolis']);  
$tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);  
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

gks_plugins_functions_run('admin_orders_item_exec_check_input',array(
  'id'=>&$id,
));



$eidi_array_str = trim_gks(base64_decode($_POST['eidi_array_str']));
//$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 

$eidi_array = json_decode($eidi_array_str, true);
if ($eidi_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['eidi_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

if (count($eidi_array)==0 and ($order_state=='080listing' or $order_state=='060registered')) {
  debug_mail(false,'eidi_array count 0',print_r($eidi_array,true));
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

//die('<pre>|'.$gks_lock.'|'.$gks_number_lock.'|'.$gks_user_lock.'|');
if ($order_journal_id>0) {
  $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
  gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,
  gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code,
  gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id
  FROM (gks_acc_journal 
  LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
  WHERE gks_acc_journal.id_acc_journal=".$order_journal_id." and gks_acc_eidi_parastatikon.eidos_parastatikou_type_id>0";
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


if ($eidos_parastatikou_type_id<=0) {
  debug_mail(false,'eidos_parastatikou_type_id empty',$order_journal_id.' '.$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο γενικός τύπου του παραστατικού')));
  echo json_encode($return); die();}

$warehouses_id_from=0; if (isset($_POST['warehouses_id_from'])) $warehouses_id_from=intval($_POST['warehouses_id_from']);
$warehouses_id_to=0;   if (isset($_POST['warehouses_id_to']))   $warehouses_id_to=intval($_POST['warehouses_id_to']);


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
//    $tropos_apostolis=1; //den apaitei apostoli
    
  } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
    
  } else {
    if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
        $warehouses_id_from=1; //virtual apothiki pelaton
        $warehouses_id_from_is_virtual=true;
      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutis
        $warehouses_id_from=2; //virtual apothiki promitheuton
        $warehouses_id_from_is_virtual=true;
      }
    } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
        $warehouses_id_to=1; //virtual apothiki pelaton
        $warehouses_id_to_is_virtual=true;
      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutis
        $warehouses_id_to=2; //virtual apothiki promitheuton
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
  
  if ($whi_eidos_parastatikou_type_id==21 or $whi_eidos_parastatikou_type_id==22) { //deltio apostolis paralavis
    if ($user_id<=0) {
      debug_mail(false,'user_id is zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή (πελάτη/προμηθευτή)')));
      echo json_encode($return); die();}
    
    if ($warehouses_id_from<=0) {
      debug_mail(false,'warehouses_id_from is zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
      echo json_encode($return); die();}
    
    if ($warehouses_id_to<=0) {
      debug_mail(false,'warehouses_id_to is zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
      echo json_encode($return); die();}
  }
    
  if ($whi_eidos_parastatikou_type_id==23) { //endodiakinisi
    if ($warehouses_id_from<=0) {
      debug_mail(false,'warehouses_id_from is zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
      echo json_encode($return); die();}
  
    if ($warehouses_id_to<=0) {
      debug_mail(false,'warehouses_id_to is zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
      echo json_encode($return); die();}
  }
    
  
  if ($whi_eidos_parastatikou_type_id==24) { //apografi
    if ($warehouses_id_to<=0) {
      debug_mail(false,'apografi warehouses_id_to zero','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη που <b>Αφορά</b> η απογραφή')));
      echo json_encode($return); die();}
  }
  
  
  
  if ($warehouses_id_from==$warehouses_id_to) {
    debug_mail(false,'warehouses_id_from=warehouses_id_to','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η αποθήκη <b>Από</b> πρέπει να είναι διαφορετική από την αποθήκη <b>Προς</b>')));
    echo json_encode($return); die();}

}

if ($eidos_parastatikou_need_afm==0) {
  $dr_user_eponimia=''; 
  $dr_user_title='';
  $dr_user_afm='';
  $dr_user_doy='';
  $dr_user_epaggelma='';
} 

if ($gks_user_lock==false) {
  if ($form_select_apostoli==-1) { //apostoli stin idia adress
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

$not_del_id_order_product=array();
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


$coupons_array=array();
foreach ($eidi_array as $eidi_array_item) {
  
  $pifs=true;//product_is_for_sum
  if ($online_enable) {
    //echo '<pre>';print_r($eidi_array_item);die();
    $is_optional=0;
    if (isset($eidi_array_item['product_is_optional'])) {
      $is_optional=intval($eidi_array_item['product_is_optional']);
    } 
    //0-> metraei sto sinolo - to default
    //1-> mporei na to prosuesei o pelatis
    //2-> to prosthese o pelatis
    if ($is_optional==1) {
      $pifs=false;
    }
    //echo '<pre>'.$is_optional.'|'.$pifs;die();
  } else {
    $eidi_array_item['product_is_optional']=0;
  }
  
  $id_order_product=intval($eidi_array_item['id_order_product']);
  $product_aa=intval($eidi_array_item['aa']);
  $product_id=intval($eidi_array_item['product_id']);
  $product_fpa_base_id=(isset($eidi_array_item['product_fpa_base_id']) ? intval($eidi_array_item['product_fpa_base_id']) : 0);
  $product_fpa_aade_id=(isset($eidi_array_item['product_fpa_aade_id']) ? intval($eidi_array_item['product_fpa_aade_id']) : 0);
  if ($product_fpa_base_id>0) $product_fpa_aade_id=0;
  
  $product_fpa_id=intval($eidi_array_item['product_fpa_id']);
  $product_fpa_pososto=floatval($eidi_array_item['product_fpa_pososto']);
  $product_sheets=floatval($eidi_array_item['product_sheets']);
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
  $product_set=trim_gks($eidi_array_item['product_set']);
  $product_is_optional=intval($eidi_array_item['product_is_optional']);
  $product_price_coupon_use=trim_gks($eidi_array_item['product_price_coupon_use']);
  $product_price_coupon_use_disabled=intval($eidi_array_item['product_price_coupon_use_disabled']);
  

  $product_withheldPercentCategory=intval($eidi_array_item['product_withheldPercentCategory']);
  $product_withheldAmount=floatval($eidi_array_item['product_withheldAmount']);
  $product_otherTaxesPercentCategory=intval($eidi_array_item['product_otherTaxesPercentCategory']);
  $product_otherTaxesAmount=floatval($eidi_array_item['product_otherTaxesAmount']);
  $product_stampDutyPercentCategory=intval($eidi_array_item['product_stampDutyPercentCategory']);
  $product_stampDutyAmount=floatval($eidi_array_item['product_stampDutyAmount']);
  $product_feesPercentCategory=intval($eidi_array_item['product_feesPercentCategory']);
  $product_feesAmount=floatval($eidi_array_item['product_feesAmount']);

  
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
  
  //print '<pre>'; print_r($eidi_array_item); die();
  
  
  //if ($product_quantity>0) {
    if ($id_order_product>0) $not_del_id_order_product[] = $id_order_product;
    
    
    
    $orders_products[]=array(
      'id_order_product' => $id_order_product,
      'product_aa' => $product_aa,
      'product_id' => $product_id,
      'product_fpa_base_id' => $product_fpa_base_id,
      'product_fpa_aade_id' => $product_fpa_aade_id,
      'product_fpa_id' => $product_fpa_id,
      'product_fpa_pososto' => $product_fpa_pososto,
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
      'product_is_optional' => $product_is_optional,
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

      'product_lots_serials' => $product_lots_serials,
      
    );  
    
    if ($pifs) $gks_price_original_net+=$product_price_start_all_net;
    if ($pifs) $gks_price_net+=$product_price_final_all_net;
    if ($pifs) $gks_price_fpa+=$product_price_final_all_fpa;
    if ($pifs) $gks_price_netfpa+=$product_price_final_all_net+$product_price_final_all_fpa;
    if ($pifs) $gks_price_total+=$product_price_final_all_total;

    if ($pifs) $totalWithheldAmount+=$product_withheldAmount;
    if ($pifs) $totalOtherTaxesAmount+=$product_otherTaxesAmount;
    if ($pifs) $totalStampDutyamount+=$product_stampDutyAmount;
    if ($pifs) $totalFeesAmount+=$product_feesAmount;

    
    if ($product_price_coupon_use!='' and in_array($product_price_coupon_use,$coupons_array)==false) {
      $coupons_array[]= $product_price_coupon_use;
    }
  //}
  
  if ($product_id>0 and in_array($product_id,$all_products_for_balance)==false)
    $all_products_for_balance[]=$product_id;
}

$totalDeductionsAmount=0;

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



$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_orders');



$redirect='';

if ($id==-1) {
  $order_guid=guid_for_order();
  $bank_deposit_9digit=gks_get_bank_deposit_9digit();
  $sql="insert into gks_orders (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip,order_guid,bank_deposit_9digit
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
  '".$db_link->escape_string($order_guid)."','".$db_link->escape_string($bank_deposit_9digit)."')";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }  
  $id = $db_link->insert_id;
  $redirect=base64_encode('admin-orders-item.php?id='.$id);  
  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_orders_log (order_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
    
}


$sql="select id_order_product from gks_orders_products where order_id=".$id;
if (count($not_del_id_order_product)>0) {
  $sql.=" and id_order_product not in (".implode(',', $not_del_id_order_product).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  

$del_id_order_product=array();
while ($row = $result->fetch_assoc()) {
  $del_id_order_product[]=$row['id_order_product'];
}
if (count($del_id_order_product)>0) {

  $sql="delete from gks_orders_products_lots where order_product_id in (".implode(',',$del_id_order_product).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  

  $sql="delete from gks_orders_products where id_order_product in (".implode(',',$del_id_order_product).")";
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

  $gks_id_order_product=$myrec['id_order_product'];
  if ($myrec['id_order_product']==0) {
    $sql="insert into gks_orders_products (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      order_id,product_set,product_is_optional,product_aa,product_id,product_fpa_base_id,product_fpa_aade_id,product_fpa_id,product_fpa_pososto,product_sheets,
      product_monada_id,product_quantity,product_price_check_fpa,
      product_price_start_all_net,product_price_ekptosi_pososto,product_price_final_all_net,product_price_final_all_fpa,product_price_final_all_total,product_descr,product_comments,
      product_price_coupon_use,product_price_coupon_use_disabled,
      
      product_withheldPercentCategory,product_withheldAmount,
      product_otherTaxesPercentCategory,product_otherTaxesAmount,
      product_stampDutyPercentCategory,product_stampDutyAmount,
      product_feesPercentCategory,product_feesAmount,
      p_warehouses_id_from,p_warehouses_id_to
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      '".$db_link->escape_string($myrec['product_set'])."',
      ".$myrec['product_is_optional'].",
      ".$myrec['product_aa'].",
      ".$myrec['product_id'].",
      ".$myrec['product_fpa_base_id'].",
      ".$myrec['product_fpa_aade_id'].",
      ".$myrec['product_fpa_id'].",
      ".number_format($myrec['product_fpa_pososto'],10, '.','').",
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
      ".$warehouses_id_from.",
      ".$warehouses_id_to."
      
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $gks_id_order_product=$db_link->insert_id;
         
  } else {
    $sql="update gks_orders_products set 
      product_set='".$db_link->escape_string($myrec['product_set'])."',
      product_is_optional='".$db_link->escape_string($myrec['product_is_optional'])."',
      product_aa=".$myrec['product_aa'].",
      product_id=".$myrec['product_id'].",
      product_fpa_base_id=".$myrec['product_fpa_base_id'].",
      product_fpa_aade_id=".$myrec['product_fpa_aade_id'].",
      product_fpa_id=".$myrec['product_fpa_id'].",
      product_fpa_pososto= ".number_format($myrec['product_fpa_pososto'],10, '.','').",
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
      p_warehouses_id_from=".$warehouses_id_from.",
      p_warehouses_id_to=".$warehouses_id_to.",
      
      mydate_edit=now(),
      user_id_edit=".$my_wp_user_id.",
      myip='".$db_link->escape_string($gkIP)."'
      where id_order_product=".$myrec['id_order_product']." and order_id=".$id;

  

    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
  }
  
  
  if ($GKS_PRODUCT_LOTS_SERIALS) {
    $sql="update gks_orders_products_lots set 
    lot_product_id=0,lot_product_quantity=0
    where order_product_id=".$gks_id_order_product;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select id_order_product_lots from gks_orders_products_lots where order_product_id=".$gks_id_order_product." order by id_order_product_lots";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $exist_ids=array();
    while ($row = $result->fetch_assoc()) {
      $exist_ids[]=$row['id_order_product_lots'];
    }   
    
    foreach ($myrec['product_lots_serials'] as $lot_product_item) {
      $id_found=0;
      foreach ($exist_ids as &$oid) {if ($oid>0) {$id_found=$oid;$oid=0;break;}} unset($oid);
      if ($id_found>0) {
        $sql="update gks_orders_products_lots set 
        lot_product_id=".$lot_product_item['lot_product_id'].",
        lot_product_quantity=".number_format($lot_product_item['lot_product_quantity'],10,'.','')."
        where id_order_product_lots=".$id_found;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      } else {
        $sql="insert into gks_orders_products_lots (
          order_product_id,lot_product_id,lot_product_quantity
        ) values (
          ".$gks_id_order_product.",
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
      $sql="delete from gks_orders_products_lots where id_order_product_lots in (".implode(',', $for_del).")";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }    
    
    
    
  }  
} 

if (count($del_id_order_product)>0) {
  $sql="delete from gks_orders_products_lots where order_product_id in (".implode(',',$del_id_order_product).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  //echo $sql;die();
}   





$gks_pricelist_item_id=0;




//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($not_del_id_order_product,true).print_r($orders_products,true).print_r($eidi_array,true)));
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($orders_products,true).'</pre>'));
//echo json_encode($return); die();


$has_ekdosi=false;
$save_but_message='';
if ($order_state=='060registered' and $is_xeirografi==0) {
  //ekdosi

  gks_order_get_ekdosi_numbers();
  
}



//order_state
$sql="update gks_orders set ";
if ($order_state!= '') {
  $sql.="order_state='".$db_link->escape_string($order_state)."', ";
}
if ($is_xeirografi!=0) {
  $sql.="order_number_int=".$order_number_int_user.", ";
  if ($order_state=='060registered' and $order_ekdosi_date_old=='') {
    $sql.="order_ekdosi_date=now(),";
  }
} else {
  if ($has_ekdosi) {
    $sql.="order_number_int=".$order_number_int_new.",
           order_number_str='".$db_link->escape_string($order_number_str_new)."',
           order_ekdosi_date=now(),
           order_seira_code='".$db_link->escape_string($order_seira_code_new)."',";
  }
}

//price=".number_format($price, 10, '.', '').",
$sql.="
company_id=".$company_id.",
company_sub_id=".$company_sub_id.",
order_journal_id=".$order_journal_id.",
order_seira_id=".$order_seira_id.",
order_date=".($order_date == '' ? 'null' : "'".$db_link->escape_string($order_date)."'") .", 
order_priority=".$order_priority.",
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
parastatiko=".$form_parastatiko.",
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


order_occasion_id=".$order_occasion_id.",
ddate=".($ddate == '' ? 'null' : "'".$db_link->escape_string($ddate)."'") .", 
mdate_expire=".($mdate_expire == '' ? 'null' : "'".$db_link->escape_string($mdate_expire)."'") .", 
online_enable=".$online_enable.",
online_password='".$db_link->escape_string($online_password)."',
online_template_html_id=".$db_link->escape_string($online_template_html_id).",

note_doc='".$db_link->escape_string($note_doc)."',
note_logistirio='".$db_link->escape_string($note_logistirio)."',
order_priority=".$order_priority.",
note_production='".$db_link->escape_string($note_production)."',

tropos_apostolis=".$tropos_apostolis.",
tropos_pliromis=".$tropos_pliromis.",
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
gks_price_original_net=".number_format($gks_price_original_net, 10, '.', '').", 
gks_price_net=".number_format($gks_price_net, 10, '.', '').", 
gks_price_fpa=".number_format($gks_price_fpa, 10, '.', '').", 
gks_price_netfpa=".number_format($gks_price_netfpa, 10, '.', '').", 
gks_price_total=".number_format($gks_price_total, 10, '.', '').", 

totalWithheldAmount=".number_format($totalWithheldAmount, 10, '.', '').", 
totalOtherTaxesAmount=".number_format($totalOtherTaxesAmount, 10, '.', '').", 
totalStampDutyamount=".number_format($totalStampDutyamount, 10, '.', '').", 
totalFeesAmount=".number_format($totalFeesAmount, 10, '.', '').", 


fiscal_position_id=".$fiscal_position_id.",
pricelist_id=".$pricelist_id.",

warehouses_id_from=".$warehouses_id_from.",
warehouses_id_to=".$warehouses_id_to.",";

$ret_plugin_sql='';
gks_plugins_functions_run('admin_orders_item_exec_sql_update',array(
  'id'=>&$id,
  'ret_plugin_sql'=>&$ret_plugin_sql,
));
$sql.=$ret_plugin_sql;
//echo '<pre>'.$ret_plugin_sql;die();

$sql.="
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."',
update_from_gks=1
where id_order = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }





$myarray_new=array();
$myarray_line_new=array();
$idiotites_new=get_order_details_txt($id, $myarray_new, $myarray_line_new); 

$sql="update gks_orders set idiotites='".$db_link->escape_string(json_encode($myarray_new))."' where id_order = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
  


$sql="UPDATE gks_orders_products
LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
SET
gks_orders_products.product_need_apostoli = IFNULL(gks_eshop_products.product_need_apostoli,0),
gks_orders_products.product_varos = IFNULL(gks_eshop_products.product_varos,0),
gks_orders_products.product_ogos_x = IFNULL(gks_eshop_products.product_ogos_x,0),
gks_orders_products.product_ogos_y = IFNULL(gks_eshop_products.product_ogos_y,0),
gks_orders_products.product_ogos_z = IFNULL(gks_eshop_products.product_ogos_z,0),
gks_orders_products.product_is_digital = IFNULL(gks_eshop_products.product_is_digital,0),
gks_orders_products.product_is_simple_download = IFNULL(gks_eshop_products.product_is_simple_download,0),
gks_orders_products.product_normal = IFNULL(gks_eshop_products.product_normal,0),
gks_orders_products.product_type = IFNULL(gks_eshop_products.product_type,0),
gks_orders_products.product_need_multi_files = IFNULL(gks_eshop_products.product_need_multi_files,0),
gks_orders_products.product_need_multi_files_min = IFNULL(gks_eshop_products.product_need_multi_files_min,0),
gks_orders_products.product_need_multi_files_max = IFNULL(gks_eshop_products.product_need_multi_files_max,0),
gks_orders_products.product_monada_id_org=IFNULL(gks_eshop_products.product_monada_id,0)

WHERE gks_orders_products.order_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 
$sql="select * from gks_orders_products where order_id=".$id;
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

$sql="update gks_orders set 
products_posotita=".number_format($products_posotita,8,'.','').",
products_need_apostoli=".$products_need_apostoli.",
products_need_pliromi=".$products_need_pliromi.",
session_id='".$_gks_id_session."'
where id_order=".$id;
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
    $sql="update gks_orders set ".$sql." where id_order=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }    
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
        $sql="update gks_orders_products set ".$sql." where order_id=".$id." and product_aa=".$aa." limit 1";
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







//echo 'fffffffffffff';die();

gks_plugins_functions_run('admin_orders_item_exec_after_save',array(
  'id'=>&$id,
));



$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


if ($is_new_rec == false) {


    
  gks_order_sxolio_log($id,$row_old,$products_old,$extra_address_old,'',$gks_custom_row_old);
  
  
  
}








$balance_user=gks_balance_calc(['id' => $user_id]);
if (isset($row_old['user_id']) and $row_old['user_id']>0 and $row_old['user_id']!=$user_id) gks_balance_calc(['id' => $row_old['user_id']]);

$p_order_state=$order_state!='' ? $order_state : $order_state_old;
$sql="update gks_orders_products set p_order_state='".$db_link->escape_string($p_order_state)."' where order_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}    
  
$mybal = gks_whi_mov_balance_calc($all_products_for_balance);

gks_whi_after_balance_for_order($id);


if ($GKS_ORDERS_PRODUCTION) {
  //echo '<pre>';echo time();die();
  
  gks_order_production_warehouses_set($id);
  gks_production_order_sintagi($id);
  gks_whi_mov_balance_calc_for_production_sintagi($id);
  
  
  gks_production_order_ergasies($id);
  gks_production_order_calc_ergasies_setready($id);
  gks_production_order_calc_ergasies_tree($id);
  
  
  
}

gks_update_user_from_some_move(array('user_id'=>$user_id,'table'=>'gks_orders','id_table'=>$id));


$return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> $redirect,'save_but_message'=>'');
echo json_encode($return); die();










