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
if ($id<=0 and $id!=-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Κράτησης').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_reservation',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$user_hotels=gks_get_hotels_list();

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_hotel_reservation');



$show_customer_more=0; if (isset($_POST['show_customer_more'])) $show_customer_more=intval($_POST['show_customer_more']);
$_gks_session['gks']['basket']['hotel']['reservation']['show_customer_more']=$show_customer_more;

  
$mybasketarray=false;
$cache_file='';if (isset($_POST['cache_file']) and trim_gks($_POST['cache_file'])!='') $cache_file=trim_gks($_POST['cache_file']);
if ($cache_file!= '' and file_exists(GKS_CACHE.$cache_file)) {
  $mybasketarray=json_decode(file_get_contents(GKS_CACHE.$cache_file), true);
}

$reservation_status_old='';
$reservation_number_int_old=0;
$reservation_number_str_old='';
$reservation_ekdosi_date_old='';
$seira_code_old='';
$is_xeirografi_old=0;








$_gks_session['temp_mypropertiesheight'] = 0;
if (isset($_POST['mypropertiesheight'])) $_gks_session['temp_mypropertiesheight']=intval($_POST['mypropertiesheight']); gks_erp_cookie_save();

$reservation_status=''; if (isset($_POST['reservation_status'])) $reservation_status=trim_gks(base64_decode($_POST['reservation_status']));
    

if ($reservation_status=='create_acc_inv') {
  unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  $id_create_acc_inv=gks_hotel_reservation_create_acc_inv($id);
  if (is_array($id_create_acc_inv) and count($id_create_acc_inv) > 0) {
    //echo '<pre>'.$reservation_state.' '.$id_credit_memo; die();
    if (count($id_create_acc_inv)==1) {
      $message=gks_lang('Το παραστατικό έχει δημιουργηθεί').'<br>'.
      gks_lang('To ID του είναι').' <b>'.$id_create_acc_inv[0].'</b><br>'.
      gks_lang('Θα πρέπει να το ελέγξετε και να το εκδώσετε').'<br>'.
      '<a class="gks_link" href="admin-acc-inv-item.php?id='.$id_create_acc_inv[0].'">'.gks_lang('Προβολή').'</a>';
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
    debug_mail(false,'error gks_hotel_reservation_create_acc_inv',$id.' '.$id_create_acc_inv);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα')),'redirect'=> '');
    echo json_encode($return); die();
  }
}


if ($reservation_status=='create_acc_pay') {
  unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  $id_create_acc_pay=gks_hotel_reservation_create_acc_pay($id);
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
    debug_mail(false,'error gks_hotel_reservation_create_acc_pay',$id.' '.print_r($id_create_acc_pay,true));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Προέκυψε κάποιο σφάλμα')),'redirect'=> '');
    echo json_encode($return); die();
  }
}




$row_old=array();
$rooms_old=array();

$reservation_status_old='';
$is_new_rec= true;
if ($id==-1) {
  $is_new_rec=true;
  $row_old['reservation_status']='010draft';
  $row_old['reservation_number_int']=0;
  
} else {

  $is_new_rec= false;
  $sql =select_gks_hotel_reservation();
  $sql.=" where id_hotel_reservation = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_reservation.hotel_id in (".implode(',',$perm_id_hotel_ids).")";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row_old = $result->fetch_assoc();

  $reservation_status_old=trim_gks($row_old['reservation_status']);
  $reservation_number_int_old=$row_old['reservation_number_int']; 
  $reservation_number_str_old=trim_gks($row_old['reservation_number_str']); 
  $reservation_ekdosi_date_old=trim_gks($row_old['reservation_ekdosi_date']); 
  $seira_code_old=trim_gks($row_old['seira_code']); 
  $is_xeirografi_old=trim_gks($row_old['is_xeirografi']); 


  $sql_rooms=select_gks_hotel_reservation_room();
  $sql_rooms.=" where hotel_reservation_id=".$id."
  order by id_hotel_reservation_room";  
  
  $result_rooms = $db_link->query($sql_rooms);
  if (!$result_rooms) {
    debug_mail(false,'error sql',$sql_rooms);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  while ($row_room = $result_rooms->fetch_assoc()) {
    $rooms_old[]=$row_room;
  }
  $gks_custom_prepare=gks_custom_table_item_prepare('gks_hotel_reservation',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
  
    
}

$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;

if (in_array($row_old['reservation_status'], array(
      //'070wait_payment','080confirm',
      '100completed','110payment'))) {
  $gks_lock=true;
} else {
  if ($row_old['reservation_number_int'] > 0 and $row_old['is_xeirografi']==0 and 
    in_array($row_old['reservation_status'],array(
      '005prodraft','010draft',
      '070wait_payment','080confirm',
      ))) { 
    $gks_number_lock=true;
  }
}
$gks_lock_user=''; if (isset($_POST['gks_lock'])) $gks_lock_user=intval($_POST['gks_lock']);
if ($gks_lock!=$gks_lock_user) {
    debug_mail(false,'gks_lock != gks_lock_user',$gks_lock.' | '.$gks_lock_user);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατάστασης εγγραφής').' (1)<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}

$gks_user_lock_user=''; if (isset($_POST['gks_user_lock'])) $gks_user_lock_user=intval($_POST['gks_user_lock']);
if ($gks_user_lock!=$gks_user_lock_user) {
    debug_mail(false,'gks_user_lock != gks_user_lock_user',$gks_user_lock.' | '.$gks_user_lock_user);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατάστασης εγγραφής').' (3)<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}

$postype=''; if (isset($_POST['postype'])) $postype=trim_gks($_POST['postype']);

$user_notes=''; if (isset($_POST['user_notes'])) $user_notes=trim_gks(base64_decode($_POST['user_notes']));
$sxolio=''; if (isset($_POST['sxolio'])) $sxolio=trim_gks(base64_decode($_POST['sxolio']));
$note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));

$tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);  
$kostos_pliromis=0;  if (isset($_POST['kostos_pliromis']))  $kostos_pliromis=floatval($_POST['kostos_pliromis']);
$kostos_pliromis_mode='';  if (isset($_POST['kostos_pliromis_mode']))  $kostos_pliromis_mode= trim_gks($_POST['kostos_pliromis_mode']);
    

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
      debug_mail(false,'error sql',                                  gks_lang('Δεν βρέθηκε το κανάλι πωλήσεων').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
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
  
  //echo '<pre>postype '.$postype;die();
  
  if ($postype=='calc' or $postype=='calc_dialog_room') {  
    
    $row=$row_old;
    $products_posotita=$row['products_posotita'];
    $products_varos=$row['products_varos'];
    $products_ogos=$row['products_ogos'];;
    $products_ogos_max_x=$row['products_ogos_max_x'];
    $products_ogos_max_y=$row['products_ogos_max_y'];
    $products_ogos_max_z=$row['products_ogos_max_z'];
    $products_need_apostoli=$row['products_need_apostoli']==0 ? false : true;
    $products_need_pliromi=$row['products_need_pliromi']==0 ? false : true;
    
    unset($mybasketarray);
    gks_mybasketarray_create($mybasketarray);
    $mybasketarray['from']='reservation';
    $mybasketarray['id_object'] = $id;
    $mybasketarray['company_id']= $row['company_id'];
    $mybasketarray['company_sub_id']= $row['company_sub_id'];
//    $mybasketarray['order_journal_id']=intval($row['reservation_journal_id']);
//    $mybasketarray['order_seira_id']=intval($row['reservation_seira_id']);
//    $mybasketarray['order_state']=trim_gks($row['reservation_status']);
//    $mybasketarray['order_date']=trim_gks($row['order_date']);
    
    $mybasketarray['user']['user_id']=$row['user_id'];
    $mybasketarray['user']['afm']=$row['afm'];
    $mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
    $mybasketarray['parastatiko']= $row['eidos_parastatikou_need_afm'];
    
    
    
    $mybasketarray['products_varos']= $products_varos;
    $mybasketarray['products_ogos']= $products_ogos;
    $mybasketarray['products_ogos_max_x']= $products_ogos_max_x;
    $mybasketarray['products_ogos_max_y']= $products_ogos_max_y;
    $mybasketarray['products_ogos_max_z']= $products_ogos_max_z;
    $mybasketarray['products_need_apostoli']=$products_need_apostoli;
    $mybasketarray['products_need_pliromi']=$products_need_pliromi;
//    $mybasketarray['destination_data']['name'] = trim_gks($row['destination_data_name']);
//    $mybasketarray['destination_data']['phone'] = trim_gks($row['destination_data_phone']);
//    $mybasketarray['destination_data']['odos'] = trim_gks($row['destination_data_odos']);
//    $mybasketarray['destination_data']['perioxi'] = trim_gks($row['destination_data_perioxi']);
//    $mybasketarray['destination_data']['poli'] = trim_gks($row['destination_data_poli']);
//    $mybasketarray['destination_data']['tk'] = trim_gks($row['destination_data_tk']);
//    $mybasketarray['destination_data']['country_id'] = intval($row['destination_data_country_id']);
//    $mybasketarray['destination_data']['nomos_id'] = intval($row['destination_data_nomos_id']);
    $mybasketarray['products_total'] = $row['gks_price_total'];
    
    $mybasketarray['tropos_apostolis'] = 1; //intval($mydata['tropos_apostolis']);
    $mybasketarray['tropos_pliromis'] = $tropos_pliromis; 
  
    
    $mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);
    $mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);
  
    //if ($kostos_apostolis_mode=='manual') $mybasketarray['kostos_apostolis']=$mydata['kostos_apostolis'];
    if ($kostos_pliromis_mode=='manual')  $mybasketarray['kostos_pliromis']= $kostos_pliromis;
    
  
  
    $pliroteo = $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'] + $mybasketarray['kostos_pliromis'];
  
  
    $cache_file='reservation_'.$id.'_'.date('Y-m-d_H-i-s').'_'.rand(10000,99999).'.json';
    file_put_contents(GKS_CACHE.$cache_file,json_encode($mybasketarray));
    //if (GKS_DEBUG) file_put_contents(GKS_CACHE.'basket.txt',print_r($mybasketarray,true));
  
    $return = array('success' => true, 'message' => base64_encode('OK'),
      //'eidi_array' => $eidi_array,
      'kostos_apostolis'  => (myCurrencyFormat($mybasketarray['kostos_apostolis'],true,true)),
      'kostos_apostolis_val' => number_format($mybasketarray['kostos_apostolis'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', ''),
      'kostos_pliromis'   => (myCurrencyFormat($mybasketarray['kostos_pliromis'],true,true)),
      'kostos_pliromis_val' => number_format($mybasketarray['kostos_pliromis'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', ''),
      'pliroteo'    => (myCurrencyFormat($pliroteo ,true,true)),
      'pliroteo_val'    => $pliroteo,
      
      'tropoi_apostolis_all' => $mybasketarray['tropoi_apostolis_all'],
      'tropoi_pliromis_all' => $mybasketarray['tropoi_pliromis_all'],
      'products_need_apostoli' => $mybasketarray['products_need_apostoli'],
      'products_need_pliromi' => $mybasketarray['products_need_pliromi'],
      'cache_file' =>$cache_file,
    );
    echo json_encode($return); die();    
    
    
    
  }
  
  
  $warning_message='';
  $sql_ekdosi='';

  if ($reservation_status!='' and in_array($reservation_status, array(
      '005prodraft','010draft','040cancelled','050rejected','070wait_payment','080confirm','100completed'
      ))==false) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος κατάσταση').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}

  if ($reservation_status=='010draft' and $reservation_status_old!='010draft' and $is_xeirografi_old==0 and $reservation_number_int_old>0) {
    //echo '<pre>vvv';die();
    $warning_message=gks_hotel_reservation_to_draft($id);
    if ($warning_message!='') 
      $warning_message=gks_lang('Έγινε επαναφορά σε <b>Πρόχειρο</b> αλλά δεν μπόρεσε μηδενιστεί ο αριθμός της κράτησης διότι').':<br>'.
                        $warning_message.'<br>'.gks_lang('Κάντε άμεσα τις αλλαγές και ξανα εκδώστε την').'<br>'.gks_lang('Διαφορετικά θα δημιουργηθεί κενό στην αρίθμηση της σειράς');
                        
  }
  

 

  


  $gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_hotel_reservation');
    
  
  $sql="update gks_hotel_reservation set ";
  if ($reservation_status!= '') {
    $sql.="reservation_status='".$db_link->escape_string($reservation_status)."', ";
  }
  
  $sql.=$sql_ekdosi;
  
  $sql.="
  user_notes='".$db_link->escape_string($user_notes)."',
  sxolio='".$db_link->escape_string($sxolio)."',
  note_logistirio='".$db_link->escape_string($note_logistirio)."',
  tropos_pliromis=".$tropos_pliromis.",
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
          $affect_balance_poso=$row_old['gks_price_total'] + $kostos_pliromis; //$kostos_apostolis + 
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
  where id_hotel_reservation = ".$id." limit 1";  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  if (is_array($mybasketarray)) {
    $sql='';
    if (isset($mybasketarray['tropoi_pliromis_all']) and 
        isset($mybasketarray['tropos_pliromis']) and
        isset($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]) ) {
          $sql.="kostos_pliromis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]))."',";
    }
    if ($sql!='') {
      $sql=substr($sql, 0, strlen($sql)-1);
      $sql="update gks_hotel_reservation set ".$sql." where id_hotel_reservation=".$id;
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
//   "\nreservation_status:".$inv_state.
//   "\ncancel_for_order_id:".$cancel_for_order_id
//  ));echo json_encode($return); die();
  
  
  
  if ($reservation_status!= '' and $reservation_status !='010draft') { //ginete apo to gks_hotel_reservation_to_draft
   
    $sql="update gks_hotel_reservation_room_day set dreservation_status='".$reservation_status."' where hotel_reservation_id=".$id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
  
  $balance_user=gks_balance_calc(['id' => $row_old['user_id']]);
  //echo '<pre>'; echo $row_old['user_id'];die();
  

  
  $return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> '','save_but_message' => base64_encode($warning_message));
  echo json_encode($return); die();        
  
  $return = array('success' => false, 'message' => base64_encode('fffffff φφφφ'));
  echo json_encode($return); die();  
}





$hotel_id=0; if (isset($_POST['hotel_id'])) $hotel_id=intval($_POST['hotel_id']);

if ($reservation_status!='' and $reservation_status!='005prodraft' and $reservation_status!='010draft' and $reservation_status!='040cancelled' and $reservation_status!='050rejected' and $reservation_status!='070wait_payment' and $reservation_status!='080confirm' and $reservation_status!='100completed') {
  debug_mail(false,'reservation_status',$reservation_status);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Κατάσταση')));
  echo json_encode($return); die();}

if ($reservation_status=='') $reservation_status=$reservation_status_old;

//echo '<pre>';
//echo '|'.$reservation_status.'|';
//die();

if ($_POST['reservation_date'] == '__/__/____ __:__') $_POST['reservation_date']='';
$reservation_date=trim_gks(stripslashes(urldecode($_POST['reservation_date'])));
if ($reservation_date!='') $reservation_date = mystrtodb($reservation_date);


if ($_POST['check_in'] == '__/__/____ __:__') $_POST['check_in']='';
$check_in=trim_gks(stripslashes(urldecode($_POST['check_in'])));
if ($check_in!='') $check_in = mystrtodb_st($check_in.':00');


if ($_POST['check_out'] == '__/__/____ __:__') $_POST['check_out']='';
$check_out=trim_gks(stripslashes(urldecode($_POST['check_out'])));
if ($check_out!='') $check_out = mystrtodb_st($check_out.':00');

//die('<pre>'.$check_out);


$mycmd='';if (isset($_POST['mycmd'])) $mycmd=trim_gks($_POST['mycmd']);
$myfile='';if (isset($_POST['myfile'])) $myfile=trim_gks($_POST['myfile']);

$cmd_is_for_coupon=false;
$mycoupon='';
if ($mycmd=='couponadd' or $mycmd=='coupondelete') {
  $cmd_is_for_coupon=true;
  $mycoupon=$myfile;
}
 
//print '<pre>mycoupon: ';
//print_r($mycoupon);
//die();

$days_round=hotel_round_days($hotel_id, $check_in, $check_out);
//print '<pre>';
//print_r($days_round);
//die();
  

$num_days = $days_round['num_days'];
$num_adults=0;  if (isset($_POST['num_adults'])) $num_adults=intval($_POST['num_adults']); 
$num_childs=0;  if (isset($_POST['num_childs'])) $num_childs=intval($_POST['num_childs']); 
$num_child_kounies=0;  if (isset($_POST['num_child_kounies'])) $num_child_kounies=intval($_POST['num_child_kounies']); 
$num_extra_beds=0;  if (isset($_POST['num_extra_beds'])) $num_extra_beds=intval($_POST['num_extra_beds']); 
$user_notes=''; if (isset($_POST['user_notes'])) $user_notes=trim_gks(base64_decode($_POST['user_notes']));
$sxolio=''; if (isset($_POST['sxolio'])) $sxolio=trim_gks(base64_decode($_POST['sxolio']));
$note_logistirio=''; if (isset($_POST['note_logistirio'])) $note_logistirio=trim_gks(base64_decode($_POST['note_logistirio']));

$fiscal_position_id=0; if (isset($_POST['fiscal_position_id'])) $fiscal_position_id=intval($_POST['fiscal_position_id']);  
//print '<pre>';print $fiscal_position_id; die();

$pricelist_id=0; if (isset($_POST['pricelist_id'])) $pricelist_id=intval($_POST['pricelist_id']);  
$def_ekptosi=0;  if (isset($_POST['def_ekptosi']))  $def_ekptosi=floatval($_POST['def_ekptosi']);

$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
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
$tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);
//if ($form_parastatiko == 0) {
//  $dr_user_eponimia=''; 
//  $dr_user_title=''; 
//  $dr_user_afm=''; 
//  $dr_user_doy=''; 
//  $dr_user_epaggelma='';
//} else {
  $dr_user_eponimia=''; if (isset($_POST['dr_user_eponimia'])) $dr_user_eponimia=trim_gks(base64_decode($_POST['dr_user_eponimia']));
  $dr_user_title=''; if (isset($_POST['dr_user_title'])) $dr_user_title=trim_gks(base64_decode($_POST['dr_user_title']));
  $dr_user_afm=''; if (isset($_POST['dr_user_afm'])) $dr_user_afm=trim_gks(base64_decode($_POST['dr_user_afm']));
  $dr_user_doy=''; if (isset($_POST['dr_user_doy'])) $dr_user_doy=trim_gks(base64_decode($_POST['dr_user_doy']));
  $dr_user_epaggelma=''; if (isset($_POST['dr_user_epaggelma'])) $dr_user_epaggelma=trim_gks(base64_decode($_POST['dr_user_epaggelma']));
//}


$tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);
$kostos_pliromis=0;  if (isset($_POST['kostos_pliromis']))  $kostos_pliromis=floatval($_POST['kostos_pliromis']);
$kostos_pliromis_mode='';  if (isset($_POST['kostos_pliromis_mode']))  $kostos_pliromis_mode= trim_gks($_POST['kostos_pliromis_mode']);


$coupons = array();
if (isset($_POST['coupons_str'])) {
  $coupons_s = trim_gks(base64_decode($_POST['coupons_str']));
  if ($coupons_s != '') {
    $coupons_s=trim_gks(base64_decode($_POST['coupons_str']));
    $coupons = json_decode($coupons_s, true);
  }
}

//print '<pre>';
//print_r($_POST['coupons_str']);
//print_r($coupons);
//die();

$childs_ages_list = array();
if (isset($_POST['childs_ages_list_str'])) {
  $childs_ages_list_s = trim_gks(base64_decode($_POST['childs_ages_list_str']));
  if ($childs_ages_list_s != '') {
    $childs_ages_list_s=trim_gks(base64_decode($_POST['childs_ages_list_str']));
    $childs_ages_list = json_decode($childs_ages_list_s, true);
  }
}
//print '<pre>';
//print_r($childs_ages_list);
//die();

  
  



$roolist = array();
if (isset($_POST['roolist'])) {
  $roolist_s = trim_gks($_POST['roolist']);
  if ($roolist_s != '') {
    $roolist_s=trim_gks(stripslashes(urldecode($_POST['roolist'])));
    $roolist = json_decode($roolist_s, true);
  }
}

$coupons_array=array();
$rooms_plithos=0;
foreach ($roolist as &$myroom) {
  //print '<pre>';print_r($myroom['rchilds_ages_list']);die();
  
  $myroom['aa'] = intval($myroom['aa']);
  $myroom['add'] = intval($myroom['add']);
  $myroom['edit'] = intval($myroom['edit']);
  $myroom['delete'] = intval($myroom['delete']);
  $myroom['recid'] = intval($myroom['recid']);
  $myroom['hotel_room_id'] = intval($myroom['hotel_room_id']);
  $myroom['room_descr'] = trim_gks($myroom['room_descr']);
  $myroom['rnum_adults'] = intval($myroom['rnum_adults']);
  $myroom['rnum_childs'] = intval($myroom['rnum_childs']);
  $myroom['rchilds_ages_list'] = json_encode($myroom['rchilds_ages_list']);
  $myroom['rnum_child_kounies'] = intval($myroom['rnum_child_kounies']);
  $myroom['rnum_extra_beds'] = intval($myroom['rnum_extra_beds']);
  $myroom['ajia_total'] = floatval($myroom['ajia_total']);
  $myroom['gks_ekptosi_pososto'] = floatval($myroom['gks_ekptosi_pososto']);
  $myroom['rsxolio'] = trim_gks($myroom['rsxolio']);
  $myroom['ruser_id'] = intval($myroom['ruser_id']);
  if ($myroom['ruser_id']<=-1) {
    $myroom['ruser_lang'] = '';
    $myroom['ruser_first_name'] = '';
    $myroom['ruser_last_name'] = '';
    $myroom['ruser_email'] = '';
    $myroom['ruser_mobile'] = '';
    $myroom['ruser_ma_odos'] = '';
    $myroom['ruser_ma_arithmos'] = '';
    $myroom['ruser_ma_orofos'] = '';
    $myroom['ruser_ma_perioxi'] = '';
    $myroom['ruser_ma_poli'] = '';
    $myroom['ruser_ma_tk'] = '';
    $myroom['ruser_ma_country_id'] = 0;
    $myroom['ruser_ma_nomos_id'] = 0;
    $myroom['ruser_fiscal_position_id'] = 0;
    $myroom['ruser_pricelist_id'] = 0;
  } else {
    $myroom['ruser_lang'] = trim_gks($myroom['ruser_lang']);
    $myroom['ruser_first_name'] = trim_gks($myroom['ruser_first_name']);
    $myroom['ruser_last_name'] = trim_gks($myroom['ruser_last_name']);
    $myroom['ruser_email'] = trim_gks($myroom['ruser_email']);
    $myroom['ruser_mobile'] = trim_gks($myroom['ruser_mobile']);
    $myroom['ruser_ma_odos'] = trim_gks($myroom['ruser_ma_odos']);
    $myroom['ruser_ma_arithmos'] = trim_gks($myroom['ruser_ma_arithmos']);
    $myroom['ruser_ma_orofos'] = trim_gks($myroom['ruser_ma_orofos']);
    $myroom['ruser_ma_perioxi'] = trim_gks($myroom['ruser_ma_perioxi']);
    $myroom['ruser_ma_poli'] = trim_gks($myroom['ruser_ma_poli']);
    $myroom['ruser_ma_tk'] = trim_gks($myroom['ruser_ma_tk']);
    $myroom['ruser_ma_country_id'] = intval($myroom['ruser_ma_country_id']);
    $myroom['ruser_ma_nomos_id'] = intval($myroom['ruser_ma_nomos_id']);
    $myroom['ruser_fiscal_position_id'] = intval($myroom['ruser_fiscal_position_id']);
    $myroom['ruser_pricelist_id'] = intval($myroom['ruser_pricelist_id']);
  }
  //print '<pre>';print_r($myroom);die();
  
  
  if ($postype == '' and $myroom['delete']==0) {
    $myroom['pdata']['product_id'] =                           intval($myroom['pdata']['product_id']);
    $myroom['pdata']['product_fpa_base_id'] =                  intval($myroom['pdata']['product_fpa_base_id']);                   
    $myroom['pdata']['product_fpa_id'] =                       intval($myroom['pdata']['product_fpa_id']);                   
    $myroom['pdata']['product_fpa_pososto'] =                  floatval($myroom['pdata']['product_fpa_pososto']);                  
    $myroom['pdata']['product_fpa_id_json'] =                  json_decode(base64_decode($myroom['pdata']['product_fpa_id_json']), true);               
    $myroom['pdata']['product_price_include_vat'] =            intval($myroom['pdata']['product_price_include_vat']);            
    $myroom['pdata']['product_price_start_peritem_db'] =       floatval($myroom['pdata']['product_price_start_peritem_db']);       
    $myroom['pdata']['product_price_start_peritem_net'] =      floatval($myroom['pdata']['product_price_start_peritem_net']);      
    $myroom['pdata']['product_price_start_peritem_fpa'] =      floatval($myroom['pdata']['product_price_start_peritem_fpa']);      
    $myroom['pdata']['product_price_start_peritem_total'] =    floatval($myroom['pdata']['product_price_start_peritem_total']);    
    $myroom['pdata']['product_price_start_all_net'] =          floatval($myroom['pdata']['product_price_start_all_net']);          
    $myroom['pdata']['product_price_start_all_fpa'] =          floatval($myroom['pdata']['product_price_start_all_fpa']);          
    $myroom['pdata']['product_price_start_all_total'] =        floatval($myroom['pdata']['product_price_start_all_total']);    
    $myroom['pdata']['product_price_final_peritem_db'] =       floatval($myroom['pdata']['product_price_final_peritem_db']);   
    $myroom['pdata']['product_price_final_peritem_net'] =      floatval($myroom['pdata']['product_price_final_peritem_net']);  
    $myroom['pdata']['product_price_final_peritem_fpa'] =      floatval($myroom['pdata']['product_price_final_peritem_fpa']);  
    $myroom['pdata']['product_price_final_peritem_total'] =    floatval($myroom['pdata']['product_price_final_peritem_total']);
    $myroom['pdata']['product_price_final_all_net'] =          floatval($myroom['pdata']['product_price_final_all_net']);      
    $myroom['pdata']['product_price_final_all_fpa'] =          floatval($myroom['pdata']['product_price_final_all_fpa']);      
    $myroom['pdata']['product_price_final_all_total'] =        floatval($myroom['pdata']['product_price_final_all_total']);    
    $myroom['pdata']['product_price_ekptosi_net'] =            floatval($myroom['pdata']['product_price_ekptosi_net']);        
    $myroom['pdata']['product_price_ekptosi_pososto'] =        floatval($myroom['pdata']['product_price_ekptosi_pososto']);    
    $myroom['pdata']['product_pricelist_item_id'] =            intval($myroom['pdata']['product_pricelist_item_id']);        
    $myroom['pdata']['product_pricelist_item_percent'] =       floatval($myroom['pdata']['product_pricelist_item_percent']);   
    $myroom['pdata']['product_price_coupon_use'] =             trim_gks($myroom['pdata']['product_price_coupon_use']);         
    $myroom['pdata']['product_price_coupon_use_disabled'] =    intval($myroom['pdata']['product_price_coupon_use_disabled']);
    
    $myroom['pdata']['ajia_table_math'] =                      trim_gks($myroom['pdata']['ajia_table_math']);
    $myroom['pdata']['ajia_table_html'] =                      trim_gks($myroom['pdata']['ajia_table_html']);
    $myroom['pdata']['ajia_table_array']=                      base64_decode($myroom['pdata']['ajia_table_array']); 
        
    if ($myroom['pdata']['product_price_coupon_use']!='' and in_array($myroom['pdata']['product_price_coupon_use'],$coupons_array)==false) {
      $coupons_array[]= $myroom['pdata']['product_price_coupon_use'];
    } 
    
    
    //print '<pre>';print_r($myroom);die();    

        
  }
  
  if (intval($myroom['delete'])==0) $rooms_plithos++;
 
}
unset($myroom);

//print '<pre>'; print_r($roolist); die();

$coupons_str='';
if (count($coupons_array)>=1) {
  $coupons_str='|' . implode('|',$coupons_array).'|';
}


if ($postype=='' and $rooms_plithos<=0 and ($reservation_status=='070wait_payment' or $reservation_status=='080confirm')) {
  debug_mail(false,                                              gks_lang('Δεν έχετε προσθέσει δωμάτια'),print_r($roolist,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχετε προσθέσει δωμάτια')));
  echo json_encode($return); die();}

$roomcheck1=array();
foreach ($roolist as $myroom) {
  if ($myroom['delete']==0) {
    if ($myroom['hotel_room_id'] > 0) {
      $roomcheck1[]=$myroom['hotel_room_id'];
    }
  }
}

//echo $postype;
//die();
$sql="select * from gks_hotel where id_hotel=".$hotel_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); 
}
$company_id=0;
$company_sub_id=0;
$hotel_booking_number_prefix='';
if ($result->num_rows == 1) {
  $row = $result->fetch_assoc();
  $company_id=$row['company_id'];
  $company_sub_id=$row['company_sub_id'];
  $hotel_booking_number_prefix=trim_gks($row['hotel_booking_number_prefix']);
} else {
  debug_mail(false,'hotel not found',$reservation_status);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Ξενοδοχείο')));
  echo json_encode($return); die();  
}






  
unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);

$mybasketarray['from']='reservation';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']=intval($company_id);
$mybasketarray['company_sub_id']=intval($company_sub_id);
$mybasketarray['user']['user_id']=$user_id;
$mybasketarray['user']['first_name']=$dr_user_first_name;
$mybasketarray['user']['last_name']=$dr_user_last_name;
$mybasketarray['user']['email']=$dr_user_email;
$mybasketarray['user']['mobile']=$dr_user_mobile;
$mybasketarray['user']['lang']=$dr_user_lang;
$mybasketarray['user']['ma_odos']=$dr_user_ma_odos;
$mybasketarray['user']['ma_arithmos']=$dr_user_ma_arithmos;
$mybasketarray['user']['ma_orofos']=$dr_user_ma_orofos;
$mybasketarray['user']['ma_perioxi']=$dr_user_ma_perioxi;
$mybasketarray['user']['ma_poli']=$dr_user_ma_poli;
$mybasketarray['user']['ma_tk']=$dr_user_ma_tk;
$mybasketarray['user']['ma_country_id']=$dr_user_ma_country_id;
$mybasketarray['user']['ma_nomos_id']=$dr_user_ma_nomos_id;
$mybasketarray['user']['eponimia']=$dr_user_eponimia;
$mybasketarray['user']['title']=$dr_user_title;
$mybasketarray['user']['afm']=$dr_user_afm;
$mybasketarray['user']['doy']=$dr_user_doy;
$mybasketarray['user']['epaggelma']=$dr_user_epaggelma;
$mybasketarray['address_extra']=-1;



//$mybasketarray['user']['ma_country_id']=91;
$mybasketarray['fiscal_position']=intval($fiscal_position_id);
if ($mybasketarray['fiscal_position']<1) $mybasketarray['fiscal_position']=1;

$mybasketarray['pricelist_id']=intval($pricelist_id);
if ($mybasketarray['pricelist_id']<1) $mybasketarray['pricelist_id']=1;
$mybasketarray['coupons']=$coupons;

//$mybasketarray['coupons']['meion20']='Meion 20%';
//$mybasketarray['coupons']['meion21']='Meion 21%';
//$mybasketarray['coupons']['meion22']='Meion 22%';
//$mybasketarray['coupons']['meion23']='Meion 23%';
//$mybasketarray['coupons']['meion24']='Meion 24%';
//$mybasketarray['coupons']['meion25']='Meion 25%';

$mybasketarray['parastatiko']=intval($form_parastatiko);  

$tropos_apostolis=1;$kostos_apostolis=0;
$mybasketarray['tropos_apostolis'] = $tropos_apostolis;
$mybasketarray['tropos_pliromis'] = $tropos_pliromis;







if ($postype=='calc' or $postype=='calc_dialog_room') {
  

  
  if ($cmd_is_for_coupon) { //cmd is for coupon
    
  
    $pricelist_id= $mybasketarray['pricelist_id'];
    if ($pricelist_id <= 0) $pricelist_id = 1;  
  
    //$return = array('success' => false, 'message' => base64_encode($pricelist_id));
    //echo json_encode($return); die();  
    
    switch ($mycmd) {
      case 'couponadd':
        $sql="SELECT gks_eshop_pricelist_items.pricelist_item_coupon, gks_eshop_pricelist_items.pricelist_item_descr, 
        gks_eshop_pricelist_items.pricelist_item_date_from, gks_eshop_pricelist_items.pricelist_item_date_to
        FROM gks_eshop_pricelist_items
        WHERE gks_eshop_pricelist_items.pricelist_item_coupon='".$db_link->escape_string($mycoupon)."' 
        AND gks_eshop_pricelist_items.pricelist_id=".$pricelist_id." 
        AND gks_eshop_pricelist_items.pricelist_item_disable=0;";
        //echo '|'.$pricelist_id;
        
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
          echo json_encode($return); die(); 
        }
        if ($result->num_rows == 0) {
          debug_mail(false,'cupon not found:'.$mycoupon );
          $msg_temp=gks_lang('Δεν βρέθηκε το κουπόνι <b>[1]</b>').'<br>'.
          gks_lang('Βεβαιωθείτε ότι το έχετε γράψει σωστά').'<br>'.
          gks_lang('Βεβαιωθείτε ότι έχετε επιλέξει το σωστό τιμοκατάλογο').' (1)';
          $msg_temp=str_replace('[1]', $mycoupon, $msg_temp);
          $return = array('success' => false, 'message' => base64_encode($msg_temp));
          echo json_encode($return); die();
        }  
  
  
        $pricelist_item_coupon='';
        $pricelist_item_descr='';
        while ($row = $result->fetch_assoc()) {  
          $pricelist_item_coupon=$row['pricelist_item_coupon'];
          $pricelist_item_descr=$row['pricelist_item_descr'];
          
          if (isset($row['pricelist_item_date_to'])) {
            if ( ! (time() >= strtotime($row['pricelist_item_date_from']) and time() <= strtotime($row['pricelist_item_date_to']))) {
              if (time() < strtotime($row['pricelist_item_date_from'])) { //den exei energopoiuei akoma
                debug_mail(false,'coupon not start yet:'.$mycoupon );
                $msg_temp=gks_lang('Δεν βρέθηκε το κουπόνι <b>[1]</b>').'<br>'.
                gks_lang('Βεβαιωθείτε ότι το έχετε γράψει σωστά').'<br>'.
                gks_lang('Βεβαιωθείτε ότι έχετε επιλέξει το σωστό τιμοκατάλογο').' (2)';
                $msg_temp=str_replace('[1]', $mycoupon, $msg_temp);
                $return = array('success' => false, 'message' => base64_encode($msg_temp));
                echo json_encode($return); die();          
              }
              debug_mail(false,'coupon expire',$mycoupon.':'. showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i:s', 1));
              $msg_temp=gks_lang('Το κουπόνι <b>[1]</b> έχει λήξει στις<br>[2]');
              $msg_temp=str_replace('[1]', $pricelist_item_coupon, $msg_temp);
              $msg_temp=str_replace('[2]', showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i:s', 1), $msg_temp);
              $return = array('success' => false, 'message' =>  base64_encode($msg_temp));
              echo json_encode($return); die();          
            }
          }
        }
        
        if (isset($mybasketarray['coupons'][$pricelist_item_coupon])) {
          debug_mail(false,'coupen is already added:'. $mycoupon);
          $msg_temp=gks_lang('Το κουπόνι <b>[1]</b> το έχετε καταχωρήσει ήδη');
          $msg_temp=str_replace('[1]', $pricelist_item_coupon, $msg_temp);
          
          $return = array('success' => false, 'message' => base64_encode($msg_temp));
          echo json_encode($return); die();           
        } else {
          $mybasketarray['coupons'][$pricelist_item_coupon]=$pricelist_item_descr;
          $out[] =array('id' => '#input_coupon','type'=>'val', 'data' => base64_encode(''));
        }
        
        break;
      case 'coupondelete':
        //$return = array('success' => false, 'message' => base64_encode($mycoupon));
        //echo json_encode($return); die();
      
        if (isset($mybasketarray['coupons'][$mycoupon])) {
          unset($mybasketarray['coupons'][$mycoupon]);// = array();
        } else {
          debug_mail(false,'try to remove coupon:'. $mycoupon);
        }
        
        break;
      default:
        debug_mail(false,'error on cmd for coupon: '.$mycmd);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
        echo json_encode($return); die();
    }
    
    $coupons_html='';
    foreach ($mybasketarray['coupons'] as $key => $coupon) {
       $coupons_html.='<span class="tooltipster_basket" title="'.$coupon.'" style="text-align:left;border: 1px solid gray;border-radius: 4px;padding:8px;margin-right: 6px;">
       <span class="coupons">'.$key.' 
       <i class="coupon_delete gks_fas gks_fa-trash-alt gks_reservation_delete_icon" data-coupon="'.$key.'" style="cursor:pointer;"></i>
       </span></span> ';
    } 
    if ($coupons_html!='') {
      $coupons_html=gks_lang('Τα κουπόνια').': '.$coupons_html;
    }
    $out[] =array('id' => '#coupons_html','type'=>'html', 'data' => base64_encode($coupons_html));  
    
  }  
  

  $fields_change=trim_gks(base64_decode($_POST['fields_change']));
  $fields_change = json_decode($fields_change, true);
  $fields_change_curr_name=trim_gks($_POST['fields_change_curr_name']);
  $fields_change_curr_aa=intval($_POST['fields_change_curr_aa']);
  
//  print '|'.$fields_change_curr_name;
//  print '|'.$fields_change_curr_aa;
//  print '|';
//  print_r($fields_change);
//  die();

  $array_rooms_ids=array();
  $room_product=array();
  foreach ($roolist as $myroom) {
    if ($myroom['delete']==0) {
      $array_rooms_ids[]=$myroom['hotel_room_id'];
    }
  }
  if (count($array_rooms_ids)>0) {
  $sql="SELECT gks_hotel_room_type.id_hotel_room_type, gks_hotel_room_type.product_id, gks_hotel_room_type.room_type_descr, 
    gks_hotel_room_type.room_type_visitors, gks_hotel_room_type.room_type_visitors_childs, gks_hotel_room_type.room_type_visitors_max, 
    gks_hotel_room.id_hotel_room,
    gks_eshop_products.product_fpa_base_id
    FROM (gks_hotel_room 
    LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type)
    LEFT JOIN gks_eshop_products ON gks_hotel_room_type.product_id = gks_eshop_products.id_product
    WHERE gks_hotel_room_type.id_hotel_room_type Is Not Null
    AND gks_hotel_room_type.product_id>0
    AND gks_hotel_room.id_hotel_room In (".implode(',',$array_rooms_ids).")";
    
    //echo '<pre>';print $sql; die();
    
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    while ($row = $result->fetch_assoc()) {
      $room_product[$row['id_hotel_room']] = $row;
    }   
    //echo '<pre>';print_r($room_product); die();
    
    
    
  }
  
//  print '<pre>';
//  print $_POST['fields_change'];
//  print_r($fields_change);
//  die();
  

  $basket_products_temp =array();
  foreach ($roolist as $value) {
    $aa=$value['aa']; //+1;
    if (isset($room_product[$value['hotel_room_id']]) and $value['delete']==0) {
      
      $user_field_change='';
      if ($aa == $fields_change_curr_aa) $user_field_change=$fields_change_curr_name;
      $user_change_ekptosi_or_final_net='';
      if (isset($fields_change[$aa])) $user_change_ekptosi_or_final_net=$fields_change[$aa];

      //print '['.$user_field_change;
      //print "|";
      //print $user_change_ekptosi_or_final_net;
      //print ']';
      //die();
      //$user_change_ekptosi_or_final_net='gks_price_total';
      //$user_field_change='gks_price_final'; //gks_ekptosi  or gks_price or gks_quantity
      
      $user_ekptosi = floatval($value['gks_ekptosi_pososto']); //floatval($value['product_price_ekptosi_pososto']);  
      
      $value['product_withheldPercentCategory']=0;
      $value['product_withheldAmount']=0;
      $value['product_otherTaxesPercentCategory']=0;  
      $value['product_otherTaxesAmount']=0; 
      $value['product_stampDutyPercentCategory']=0;  
      $value['product_stampDutyAmount']=0;
      $value['product_feesPercentCategory']=0;  
      $value['product_feesAmount']=0;  
      $value['product_deductionsAmount']=0;  
      
      $sql="select * from gks_eshop_products where id_product=".$room_product[$value['hotel_room_id']]['product_id'];  
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        echo json_encode($return); die(); 
      }
      if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $value['product_withheldPercentCategory']=$row['product_withheldPercentCategory'];
        $value['product_otherTaxesPercentCategory']=$row['product_otherTaxesPercentCategory'];
        $value['product_stampDutyPercentCategory']=$row['product_stampDutyPercentCategory'];
        $value['product_feesPercentCategory']=$row['product_feesPercentCategory'];
      }        
      
      //print '<pre>'; print_r($days_round);die();
      
      $objects=array();
      $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $days_round['num_days'], 'files' => array(), 'warnings'=>array());
      //print '<pre>';print_r($room_product[$value['hotel_room_id']]);die();
      
      //print '<pre>';print_r($value);die();
      
      $basket_products_temp[$aa]=array(
        'is_hotel_room_type' => true,
        'product_id'=>array(
          'id_product'=>$room_product[$value['hotel_room_id']]['product_id'], 
          'product_monada_id' => 100,
          'product_fpa_base_id' => $room_product[$value['hotel_room_id']]['product_fpa_base_id'], 
          'product_sheets'=>0, 
          'product_set' => '',
        ), 
        'objects'=>$objects,
        'user_ekptosi' => $user_ekptosi,
        'user_final_net' => floatval($value['ajia_total']) ,
        'user_final_total' => floatval($value['ajia_total']) ,
        'user_change_ekptosi_or_final_net' => $user_change_ekptosi_or_final_net,
        'user_field_change' => $user_field_change,
        
        
        'id_hotel'=> $hotel_id,
        'user_check_in'=> $days_round['check_in_round'],
        'user_check_out'=> $days_round['check_out_round'],
        'user_room_id' => $value['hotel_room_id'],
        'user_rnum_adults' => $value['rnum_adults'],
        'user_rnum_childs' => $value['rnum_childs'],
        'user_rchilds_ages_list' => $value['rchilds_ages_list'],
        'user_rnum_child_kounies' => $value['rnum_child_kounies'],
        'user_rnum_extra_beds' => $value['rnum_extra_beds'],


        'other_taxes' => array(
          'withheldPercentCategory' => intval($value['product_withheldPercentCategory']),  
          'withheldAmount' => floatval($value['product_withheldAmount']),  
          'otherTaxesPercentCategory' => intval($value['product_otherTaxesPercentCategory']),  
          'otherTaxesAmount' => floatval($value['product_otherTaxesAmount']),  
          'stampDutyPercentCategory' => intval($value['product_stampDutyPercentCategory']),  
          'stampDutyAmount' => floatval($value['product_stampDutyAmount']), 
          'feesPercentCategory' => intval($value['product_feesPercentCategory']),  
          'feesAmount' => floatval($value['product_feesAmount']),  
          'deductionsAmount' => floatval($value['product_deductionsAmount']),  
        ),
        
      );
      
//      print '<pre>';
//      var_dump($basket_products_temp[$aa]['user_rchilds_ages_list']);
//      print_r($basket_products_temp[$aa]);
//      die();
    }
  }
  
 
  
  $mybasketarray['products'] = $basket_products_temp;
  $myproducts = gks_basket_recalc($mybasketarray, $fields_change, array());
  

  $mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);
  $mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);

  

  

  
  if ($kostos_pliromis_mode=='manual')  $mybasketarray['kostos_pliromis']= $kostos_pliromis;
  
  
  $pliroteo = $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'] + $mybasketarray['kostos_pliromis'];
  
  
  $eidi=array();
  
  gks_CheckAFM_Live($mybasketarray);
  
  $coupons_html='';
  foreach ($mybasketarray['coupons'] as $key => $coupon) {
     $coupons_html.='<span class="tooltipster coupons_span" title="'.$coupon.'">
     <span class="coupons text-sm">'.$key.' 
     <i class="coupon_delete fas fa-trash-alt gks_reservation_delete_icon" data-coupon="'.$key.'" style=""></i>
     </span></span> ';
  }
  if ($coupons_html!='') {
    $coupons_html=gks_lang('Κουπόνια').': '.$coupons_html;
  }
  
  //file_put_contents(GKS_SITE_PATH.'tmp/res.txt', print_r($mybasketarray, true)."\n".print_r($roolist,true)."\n".print_r($days_round,true));
//  file_put_contents(GKS_SITE_PATH.'tmp/bas.txt', print_r($_gks_session['gks']['basket'], true));
  $eidi=array();
  foreach ($mybasketarray['products'] as $aa => $value) {
    $product_price_ekptosi_net=round($value['product_id']['product_price_start_all_net']-$value['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    $product_price_ekptosi_total=round($value['product_id']['product_price_start_all_total']-$value['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    $product_price_ekptosi_pososto=0;
    if ($value['product_id']['product_price_start_all_net']!=0 and $value['product_id']['product_price_include_vat']==0) {
      $product_price_ekptosi_pososto=round($product_price_ekptosi_net*100/$value['product_id']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    } else if ($value['product_id']['product_price_start_all_total']!=0 and $value['product_id']['product_price_include_vat']!=0) {
      $product_price_ekptosi_pososto=round($product_price_ekptosi_total*100/$value['product_id']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    }


    if (isset($value['user_change_ekptosi_or_final_net']) and $value['user_change_ekptosi_or_final_net']=='gks_ekptosi' and isset($value['user_ekptosi'])) {
      $product_price_ekptosi_pososto=$value['user_ekptosi'];
    }
    
    $ekptosi_poso_html='';
    if ($product_price_ekptosi_total!=0) $ekptosi_poso_html=myCurrencyFormat($product_price_ekptosi_total,false,true);
    

    //print '<pre>';print_r($value['product_id']);die();
    
    $eidi[] = array(
      'aa' => $aa,
      
      'product_id' => $value['product_id']['id_product'],
      'product_fpa_base_id' => $value['product_id']['product_fpa_base_id'],
      'product_fpa_id' => $value['product_id']['product_fpa_id_array']['id_fpa_to'],
      'product_fpa_pososto' => $value['product_id']['product_fpa_id_array']['fpa_pososto'],
      'product_fpa_id_json' => json_encode($value['product_id']['product_fpa_id_array']),
      'product_price_include_vat' => $value['product_id']['product_price_include_vat'],
      'product_price_start_peritem_db' => round($value['product_id']['product_price_start_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_peritem_net' => round($value['product_id']['product_price_start_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_peritem_fpa' => round($value['product_id']['product_price_start_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_peritem_total' => round($value['product_id']['product_price_start_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_all_net' => round($value['product_id']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_all_fpa' => round($value['product_id']['product_price_start_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_all_total' => round($value['product_id']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_peritem_db' => round($value['product_id']['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_peritem_net' => round($value['product_id']['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_peritem_fpa' => round($value['product_id']['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_peritem_total' => round($value['product_id']['product_price_final_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_all_net' => round($value['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_all_fpa' => round($value['product_id']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_all_total' => round($value['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_ekptosi_net' => $product_price_ekptosi_net,
      'product_price_ekptosi_pososto' => $product_price_ekptosi_pososto,
      'product_pricelist_item_id' => $value['product_id']['product_pricelist_item_id'],
      'product_pricelist_item_percent' => round($value['product_id']['product_pricelist_item_percent'],2),
      'product_price_coupon_use' => $value['product_id']['product_price_coupon_use'],
      'product_price_coupon_use_disabled' => $value['product_id']['product_price_coupon_use_disabled'],
      
      'fpa_descr_print' => $value['product_id']['product_fpa_id_array']['fpa_descr_print'],
      'ekptosi_poso_html' => $ekptosi_poso_html,

      'room_ajia_table' => $value['product_id']['room_ajia_table'],
      
      'roomaf_other_taxes_tooltip' => $value['other_taxes_tooltip'],
      
      'other_taxes' => $value['other_taxes'],
    );
  }   

  $cache_file='reservation_'.$id.'_'.date('Y-m-d_H-i-s').'_'.rand(10000,99999).'.json';
  file_put_contents(GKS_CACHE.$cache_file,json_encode($mybasketarray));
  //if (GKS_DEBUG) file_put_contents(GKS_CACHE.$cache_file.'.txt',print_r($mybasketarray,true));

  
  $return = array('success' => true, 'message' => base64_encode('ok'),
    //'eidi_array' => $eidi_array,
    'eidi' => $eidi,
  
    'products_posotita' => (myNumberFormat($mybasketarray['products_posotita'],0)),
    'products_posotita_val'    => $mybasketarray['products_posotita'],
//    'products_ogos' => ($products_ogos),
//    'products_varos' => ($products_varos),
    'products_netvalue'    => (myCurrencyFormat($mybasketarray['products_netvalue'],true,true)),
    'products_netvalue_fl' => floatval($mybasketarray['products_netvalue']),
    'products_fpa'         => ($mybasketarray['products_fpa']==0 ? '' : myCurrencyFormat($mybasketarray['products_fpa'],true,true)),
    'products_fpa_fl'      => floatval($mybasketarray['products_fpa']),
    'products_netfpa'      => (($mybasketarray['products_netvalue'] + $mybasketarray['products_fpa']==0) ? '' : myCurrencyFormat($mybasketarray['products_netvalue'] + $mybasketarray['products_fpa'],true,true)),
    'products_netfpa_fl'   => floatval($mybasketarray['products_netvalue'] + $mybasketarray['products_fpa']),

    'totalWithheldAmount' => ($mybasketarray['totalWithheldAmount']==0 ? '' : myCurrencyFormat($mybasketarray['totalWithheldAmount'],true,true)),
    'totalOtherTaxesAmount' => ($mybasketarray['totalOtherTaxesAmount']==0 ? '' :myCurrencyFormat($mybasketarray['totalOtherTaxesAmount'],true,true)),
    'totalStampDutyamount' => ($mybasketarray['totalStampDutyamount']==0 ? '' : myCurrencyFormat($mybasketarray['totalStampDutyamount'],true,true)),
    'totalFeesAmount' => ($mybasketarray['totalFeesAmount']==0 ? '' : myCurrencyFormat($mybasketarray['totalFeesAmount'],true,true)),

    'products_total' => (myCurrencyFormat($mybasketarray['products_total'],true,true)),
    'products_total_fl' => floatval($mybasketarray['products_total']),
    'kostos_apostolis'  => (myCurrencyFormat($mybasketarray['kostos_apostolis'],true,true)),
    'kostos_apostolis_val' => number_format($mybasketarray['kostos_apostolis'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', ''),
    'kostos_pliromis'   => (myCurrencyFormat($mybasketarray['kostos_pliromis'],true,true)),
    'kostos_pliromis_val' => number_format($mybasketarray['kostos_pliromis'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', ''),
    'pliroteo'    => (myCurrencyFormat($pliroteo ,true,true)),
    'pliroteo_val'    => $pliroteo,
  
    
    'tropoi_apostolis_all' => $mybasketarray['tropoi_apostolis_all'],
    'tropoi_pliromis_all' => $mybasketarray['tropoi_pliromis_all'],
    'products_need_apostoli' => $mybasketarray['products_need_apostoli'],
    'products_need_pliromi' => $mybasketarray['products_need_pliromi'],
    //'products_total' => $mybasketarray['products_total'],
    'check_vies' => $mybasketarray['check_vies'],
    //'views_run_img' => $mybasketarray['check_vies']['views_run_img'],
    'coupons_html' => $coupons_html,
    'coupons_array' => $mybasketarray['coupons'],
    'fields_change' => $fields_change,
    'gks_postype' => $postype,
    'cache_file' =>$cache_file,
  );
  echo json_encode($return); die();  
  
  
  
  
}



$reservation_journal_id=0;if (isset($_POST['reservation_journal_id'])) $reservation_journal_id=intval($_POST['reservation_journal_id']);
if ($gks_number_lock) $reservation_journal_id=$row_old['reservation_journal_id'];
//echo '<pre>';print '|'.$gks_lock.'|'.$gks_number_lock.'|'.$reservation_journal_id.'|'.$row_old['reservation_status'];die();

if ($reservation_journal_id<=0) {
  debug_mail(false,'reservation_journal_id is not found',$reservation_journal_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Ημερολόγιο')));
  echo json_encode($return); die();}  
  
$reservation_seira_id=0;if (isset($_POST['reservation_seira_id'])) $reservation_seira_id=intval($_POST['reservation_seira_id']);
if ($gks_number_lock) $reservation_seira_id=$row_old['reservation_seira_id'];
if ($reservation_seira_id<=0) {
  debug_mail(false,'reservation_seira_id is not found',$reservation_seira_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Σειρά')));
  echo json_encode($return); die();}  

$reservation_number_int_user=0;if (isset($_POST['reservation_number_int'])) $reservation_number_int_user=intval($_POST['reservation_number_int']);

$sql="SELECT gks_acc_seires.id_acc_seira,gks_acc_seires.is_xeirografi
FROM (((gks_acc_seires 
LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE eidos_parastatikou_type_id in (1100) AND id_acc_eidos_parastatikou not in (702,703,704)
AND gks_acc_seires.is_disable=0 AND gks_acc_journal.is_disable=0 
AND gks_company.company_disable=0 
AND gks_acc_seires.id_acc_seira=".$reservation_seira_id." 
AND gks_acc_journal.id_acc_journal=".$reservation_journal_id." 
AND gks_company.id_company=".$company_id;

if (count($perm_id_company_ids)>0) $sql.=" and gks_company.id_company in (".implode(',',$perm_id_company_ids).")";
if ($company_sub_id>0) {
  $sql.=" AND gks_company_subs.company_sub_disable=0 AND gks_company_subs.id_company_sub=".$company_sub_id;
  //if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_company_subs.id_company_sub in (".implode(',',$perm_id_company_sub_ids).")";
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
  debug_mail(false,                                              gks_lang('Δεν βρέθηκε ο συνδυασμός εταιρείας/υποκαταστήματος, ημερολογίου και σειράς'),$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο συνδυασμός εταιρείας/υποκαταστήματος, ημερολογίου και σειράς')));
  echo json_encode($return); die();}
$row_seira = $result->fetch_assoc();
$is_xeirografi=$row_seira['is_xeirografi'];

if ($reservation_status=='010draft' and $reservation_status_old!='010draft' and $is_xeirografi_old==0 and $reservation_number_int_old>0) {
  //echo '<pre>vvv';die();
 gks_hotel_reservation_to_draft($id);
}
  
//if ($reservation_status=='080listing' and $is_xeirografi==0) {
//  debug_mail(false,'Η σειρά είναι χειρόγραφη, άρα η κράτηση θα πρέπει να εκδοθεί και όχι να καταχωρηθεί',$sql);
//  $return = array('success' => false, 'message' => base64_encode('Η σειρά είναι χειρόγραφη, άρα η κράτηση θα πρέπει να εκδοθεί και όχι να καταχωρηθεί'));
//  echo json_encode($return); die();}    
//  
//if ($reservation_status=='070wait_payment' and $is_xeirografi!=0) {
//  debug_mail(false,'Η σειρά είναι μηχανογραφημένη, άρα η κράτηση θα πρέπει να καταχωρηθεί και όχι να εκδοθεί',$sql);
//  $return = array('success' => false, 'message' => base64_encode('Η σειρά είναι μηχανογραφημένη, άρα η κράτηση θα πρέπει να καταχωρηθεί και όχι να εκδοθεί'));
//  echo json_encode($return); die();}



if ($reservation_date=='') {
  debug_mail(false,'reservation_date',$reservation_date);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Κράτησης')));
  echo json_encode($return); die();}

if ($check_in=='') {
  debug_mail(false,'check_in',$check_in);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Άφιξης')));
  echo json_encode($return); die();}

if ($check_out=='') {
  debug_mail(false,'check_out',$check_out);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Αναχώρησης')));
  echo json_encode($return); die();}
   
if (strtotime($check_in) > strtotime($check_out)) {
  debug_mail(false,'check_in check_out');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η ημερομηνία άφιξης δεν μπορεί να είναι μεγαλύτερη από την ημερομηνία αναχώρησης')));
  echo json_encode($return); die();}

if ($user_id<=0) {
  debug_mail(false,gks_lang('Επιλέξτε κάποια επαφή ή δημιουργήστε μία'),'');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή ή δημιουργήστε μία')));
  echo json_encode($return); die();}

if ($dr_user_email != '' and !filter_var($dr_user_email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,'email is not ok : '.$dr_user_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  echo json_encode($return); die();}


//if ($dr_user_ma_country_id == 91 and $dr_user_mobile != '' and (strlen($dr_user_mobile) != 10 or substr($dr_user_mobile,0,2) != '69') ) {
//  debug_mail(false,'mobile is not OK: '.$dr_user_mobile);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To κινητό δεν είναι σωστό για την επιλεγμένη χώρα')));
//  echo json_encode($return); die();} 

//if (count($childs_ages_list) != $num_childs) {
//  debug_mail(false,'emptyl',          'childs_ages_list');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τις ηλικές των παιδιών')));
//  echo json_encode($return); die(); }


if ($fiscal_position_id<=0) {
  debug_mail(false,'emptyl',          'user_fiscal_position_id can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η φορολογική θέση δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }

if ($pricelist_id<=0) {
  debug_mail(false,'emptyl',                'user_pricelist_id can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο τιμοκατάλογος δεν μπορεί να είναι κενός')));
  echo json_encode($return); die(); }




//if (count($roomcheck1)<=0)  {
//  debug_mail(false,'hotel_room_id','<pre>'.print_r($roolist,true).'</pre>');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Προσθέστε τουλάχιστον ένα δωμάτιο')));
//  echo json_encode($return); die();}  

$roomcheck2=array();
if (count($roomcheck1)>0) {
  $sql="SELECT gks_hotel_room.id_hotel_room, gks_hotel_room.room_descr, 
  gks_hotel_room.room_status, gks_hotel_room_type.room_type_status, 
  gks_hotel_room_type.room_type_visitors, gks_hotel_room_type.room_type_visitors_childs, gks_hotel_room_type.room_type_visitors_max,
  gks_hotel_room_type.room_type_price,
  gks_hotel_room.hotel_room_type_id,
  gks_hotel_room.hotel_id
  FROM gks_hotel_room 
  LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type
  WHERE gks_hotel_room.id_hotel_room In (".implode(',', $roomcheck1).")";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  while ($row = $result->fetch_assoc()) {
    $roomcheck2[$row['id_hotel_room']] = $row;
  }  
  
  $error_not_in_this_hotel='';
  foreach ($roomcheck2 as $value) {
    if ($hotel_id!=$value['hotel_id']) {
      $error_not_in_this_hotel.=str_replace('[1]',$value['room_descr'],gks_lang('Το δωμάτιο <b>[1]</b> δεν ανήκει σε αυτό το ξενοδοχείο')).'<br>';
    }
  } 
  if ($error_not_in_this_hotel!='') {
    debug_mail(false,'error_not_in_this_hotel',$error_not_in_this_hotel);
    $return = array('success' => false, 'message' => base64_encode($error_not_in_this_hotel));
    echo json_encode($return); die(); }     
    
  
}
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($roomcheck1,true).'</pre>'));
//echo json_encode($return); die();


if ($reservation_status=='070wait_payment' or $reservation_status=='080confirm' or $reservation_status=='100completed') {
  $get_availability_rooms_imput=array(
    'id_hotel' => $hotel_id,
    'date_from' => $days_round['check_in_round'],
    'date_to' => $days_round['check_out_round'],
    'alldata' => false,
    'id_hotel_room' => 0,
    'id_hotel_room_type' => 0,
    'not_id_hotel_reservation' => $id,
    'not_id_hotel_folio' => 0,
    'not_id_hotel_room' => array(),
    'rnum_adults' => 0,
    'rnum_childs' => 0,
    'rchilds_ages_list' => array(),
    'rnum_child_kounies' => 0,
    'rnum_extra_beds' => 0,
  );
  $rooms_array = get_availability_rooms($get_availability_rooms_imput);
  
}
//print '<pre>'.$reservation_status;print_r($rooms_array);die();

//check rooms
$roomcheck3=array();
$sum_num_adults=0;
$sum_num_childs=0;
$sum_num_child_kounies=0;
$sum_num_extra_beds=0;
$sum_ajia_total=0;
$sum_rooms_plithos=0;

foreach ($roolist as $myroom) {
  if ($myroom['delete']==0) {
    if ($myroom['hotel_room_id']<=0) {
      debug_mail(false,'hotel_room_id','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το δωμάτιο σε όλες τις εγγραφές')));
      echo json_encode($return); die();}
    if (isset($roomcheck2[$myroom['hotel_room_id']]) == false) {
      debug_mail(false,'hotel_room_id not found','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$myroom['room_descr'],gks_lang('Το δωμάτιο <b>[1]</b> δεν βρέθηκε')).'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();}
      
    if ($roomcheck2[$myroom['hotel_room_id']]['room_status'] != 'available' or $roomcheck2[$myroom['hotel_room_id']]['room_type_status'] != 'available') {
      debug_mail(false,'hotel_room_id not available','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$myroom['room_descr'],gks_lang('Το δωμάτιο <b>[1]</b> δεν είναι διαθέσιμο'))));
      echo json_encode($return); die();}
    
    if (in_array($myroom['hotel_room_id'],$roomcheck3)) {
      debug_mail(false,'hotel_room_id more one','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$myroom['room_descr'],gks_lang('Το δωμάτιο <b>[1]</b> υπάρχει πάνω από μία φορά στην λίστα'))));
      echo json_encode($return); die();}
    
    $roomcheck3[] = $myroom['hotel_room_id'];

    if ($myroom['rnum_adults']<=0 and $myroom['rnum_childs']<=0) {
      debug_mail(false,'rnum_adults and rnum_childs','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$myroom['room_descr'],gks_lang('Στο δωμάτιο <b>[1]</b> ορίστε το πλήθος των επισκεπτών'))));
      echo json_encode($return); die();}      
    
    
    if (($myroom['rnum_adults']+$myroom['rnum_childs']) > ($roomcheck2[$myroom['hotel_room_id']]['room_type_visitors_max'] + $myroom['rnum_extra_beds'])) {
      debug_mail(false,'rnum_adults rnum_childs','<pre>'.print_r($myroom,true).'</pre>');
      $temp=($myroom['rnum_adults']+$myroom['rnum_childs']).'/'.($roomcheck2[$myroom['hotel_room_id']]['room_type_visitors_max']);
      $return = array('success' => false, 'message' => base64_encode(str_replace('[2]',$temp,gks_lang(str_replace('[1]',$myroom['room_descr'],gks_lang('Στο δωμάτιο <b>[1]</b> ορίσατε παραπάνω επισκέπτες από την δυνατότητα του δωματίου: [2]'))))));
      echo json_encode($return); die();}      
    
    if ($myroom['ajia_total'] <0) {
      debug_mail(false,'ajia_total','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$myroom['room_descr'],gks_lang('Στο δωμάτιο <b>[1]</b> ορίστε την αξία'))));
      echo json_encode($return); die();}
    
    if ($myroom['ruser_email'] != '' and !filter_var($myroom['ruser_email'], FILTER_VALIDATE_EMAIL)) {
      debug_mail(false,'ruser_email','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode(str_replace('[2]',$myroom['ruser_email'],str_replace('[1]',$myroom['room_descr'],gks_lang('Στο δωμάτιο <b>[1]</b> το email <b>[2]</b> δεν είναι σωστό')))));
      echo json_encode($return); die();}

    if ($myroom['ruser_ma_country_id'] == 91 and $myroom['ruser_mobile'] != '' and (strlen($myroom['ruser_mobile']) != 10 or substr($myroom['ruser_mobile'],0,2) != '69') ) {
      debug_mail(false,'ruser_mobile','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode(str_replace('[2]',$myroom['ruser_mobile'],str_replace('[1]',$myroom['room_descr'],gks_lang('Στο δωμάτιο <b>[1]</b> το κινητό <b>[2]</b> δεν είναι σωστό')))));
      echo json_encode($return); die();}
    
    $sum_num_adults+=$myroom['rnum_adults'];
    $sum_num_childs+=$myroom['rnum_childs'];
    $sum_num_child_kounies+=$myroom['rnum_child_kounies'];
    $sum_num_extra_beds+=$myroom['rnum_extra_beds'];
    
    $sum_ajia_total+=$myroom['ajia_total'];
    $sum_rooms_plithos++;
    
    if ($reservation_status=='070wait_payment' or $reservation_status=='080confirm' or $reservation_status=='100completed') {
      if (!(isset($rooms_array['rooms'][$myroom['hotel_room_id']]['is_avl_state_folio']) and $rooms_array['rooms'][$myroom['hotel_room_id']]['is_avl_state_folio'] == true)) {
        debug_mail(false,'room is not aval',str_replace('[1]',$myroom['room_descr'],gks_lang('Το δωμάτιο <b>[1]</b> δεν είναι διαθέσιμο για αυτές τις ημερομηνίες')));
        //debug_mail(false,'room is not aval','<pre>'.print_r($myroom,true).'</pre>');
        //debug_mail(false,'room is not aval','<pre>'.print_r($myroom,true).'</pre><pre>'.print_r($rooms_array,true).'</pre>');
        //debug_mail(false,'room is not aval','<pre>'.print_r($myroom,true).'</pre><pre>'.print_r($rooms_array,true).'</pre>');
        $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$myroom['room_descr'],gks_lang('Το δωμάτιο <b>[1]</b> δεν είναι διαθέσιμο για αυτές τις ημερομηνίες'))));
        echo json_encode($return); die();}
    }
      
  }
}

if ($num_adults==0 and $num_childs==0) {
  $num_adults = $sum_num_adults;
  $num_childs = $sum_num_childs;
} else {
  if ($num_adults!=$sum_num_adults) {
        debug_mail(false,'num_adults != sum_num_adults','<pre>'.print_r($roolist,true).'</pre>');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν συμφωνεί το πλήθος των ενηλίκων της κράτησης με το άθροισμα των ενηλίκων από τα δωμάτια')));
        echo json_encode($return); die();}
  
  
  if ($num_childs!=$sum_num_childs) {
        debug_mail(false,'num_childs != sum_num_childs','<pre>'.print_r($roolist,true).'</pre>');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν συμφωνεί το πλήθος των παιδιών της κράτησης με το άθροισμα των παιδιών από τα δωμάτια')));
        echo json_encode($return); die();}
}
//$return = array('success' => false, 'message' => base64_encode('φφφφ'));
//echo json_encode($return); die();




//write

if ($id <= 0) {
  $reservation_guid=guid_for_reservation();
  $bank_deposit_9digit=gks_get_bank_deposit_9digit();
  $sql="insert into gks_hotel_reservation (
  reservation_guid,reservation_status,bank_deposit_9digit,
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip
  ) values (
  '".$db_link->escape_string($reservation_guid)."','010draft','".$db_link->escape_string($bank_deposit_9digit)."',
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
 
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
   
  $id = $db_link->insert_id;  

  $hotel_booking_number=$hotel_booking_number_prefix.$id;
  $sql="update gks_hotel_reservation set hotel_booking_number='".$hotel_booking_number."' where id_hotel_reservation=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
      
  $sxolio_log=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_hotel_reservation_log (hotel_reservation_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }     
    
}



$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray,-1);
$mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray,-1);

$kostos_pliromis_json='';
if (isset($mybasketarray['tropoi_pliromis_all'][$tropos_pliromis])) {
  $kostos_pliromis_json=json_encode($mybasketarray['tropoi_pliromis_all'][$tropos_pliromis]);
}

//print '<pre>';print_r($mybasketarray);die();

$tropos_apostolis_json='';
if (isset($mybasketarray['tropoi_apostolis_all'][$tropos_apostolis])) {
  $tropos_apostolis_json=json_encode($mybasketarray['tropoi_apostolis_all'][$tropos_apostolis]);
}

//$return = array('success' => false, 'message' => base64_encode($id.'<br>'.$check_in.'<br>'.$check_out.'<br>'.$reservation_guid));
//echo json_encode($return); die(); 


//ajia_total=".number_format($sum_ajia_total, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
$gks_price_original_net=0;
$gks_price_net=0;
$gks_price_fpa=0;
$gks_price_netfpa=0;
$gks_price_total=0;

$totalWithheldAmount=0;
$totalOtherTaxesAmount=0;
$totalStampDutyamount=0;
$totalFeesAmount=0;

foreach ($roolist as $myroom) {
  if ($myroom['delete'] == 0) {
    $gks_price_original_net+=$myroom['pdata']['product_price_start_all_net'];
    $gks_price_net+=$myroom['pdata']['product_price_final_all_net'];
    $gks_price_fpa+=$myroom['pdata']['product_price_final_all_fpa'];
    $gks_price_netfpa+=$myroom['pdata']['product_price_final_all_net']+$myroom['pdata']['product_price_final_all_fpa'];
    $gks_price_total+=$myroom['pdata']['product_price_final_all_total'];



    $totalWithheldAmount+=$myroom['pdata']['other_taxes']['withheldAmount'];
    $totalOtherTaxesAmount+=$myroom['pdata']['other_taxes']['otherTaxesAmount'];
    $totalStampDutyamount+=$myroom['pdata']['other_taxes']['stampDutyAmount'];
    $totalFeesAmount+=$myroom['pdata']['other_taxes']['feesAmount'];


    
  }
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
    


$has_ekdosi=false;
$save_but_message='';
if (($reservation_status=='070wait_payment' or $reservation_status=='080confirm') and $is_xeirografi==0) {
  //ekdosi

  gks_hotel_reservation_get_ekdosi_numbers();
  
}

$sql="update gks_hotel_reservation set ";
if ($reservation_status!= '') {
  $sql.="reservation_status='".$db_link->escape_string($reservation_status)."', ";
}

if ($is_xeirografi!=0) {
  $sql.="reservation_number_int=".$reservation_number_int_user.", ";
  if (($reservation_status=='070wait_payment' or $reservation_status=='080confirm') and $reservation_ekdosi_date_old=='') {
    $sql.="reservation_ekdosi_date=now(),";
  }
} else {
  if ($has_ekdosi) {
    $sql.="reservation_number_int=".$reservation_number_int_new.",
           reservation_number_str='".$db_link->escape_string($reservation_number_str_new)."',
           reservation_ekdosi_date=now(),
           reservation_seira_code='".$db_link->escape_string($reservation_seira_code_new)."',";
  }
}

$sql.="
hotel_id=".$hotel_id.",
reservation_journal_id=".$reservation_journal_id.",
reservation_seira_id=".$reservation_seira_id.",

reservation_date=".($reservation_date == '' ? 'null' : "'".$db_link->escape_string($reservation_date)."'") .", 
check_in=".($check_in == '' ? 'null' : "'".$db_link->escape_string($check_in)."'") .", 
check_out=".($check_out == '' ? 'null' : "'".$db_link->escape_string($check_out)."'") .", 
num_days=".$num_days.",
num_adults=".$num_adults.",
num_childs=".$num_childs.",
childs_ages_list='".$db_link->escape_string(json_encode($childs_ages_list))."',
num_child_kounies=".$sum_num_child_kounies.",
num_extra_beds=".$sum_num_extra_beds.",
user_notes='".$db_link->escape_string($user_notes)."',
sxolio='".$db_link->escape_string($sxolio)."',
note_logistirio='".$db_link->escape_string($note_logistirio)."',
rooms_plithos=".$sum_rooms_plithos.",
products_posotita=".($num_days*$sum_rooms_plithos).",

user_id=".$user_id.",
user_lang='".$db_link->escape_string($dr_user_lang)."',
user_first_name='".$db_link->escape_string($dr_user_first_name)."',
user_last_name='".$db_link->escape_string($dr_user_last_name)."',
user_email='".$db_link->escape_string($dr_user_email)."',
user_mobile='".$db_link->escape_string($dr_user_mobile)."',
ma_odos='".$db_link->escape_string($dr_user_ma_odos)."',
ma_arithmos='".$db_link->escape_string($dr_user_ma_arithmos)."',
ma_orofos='".$db_link->escape_string($dr_user_ma_orofos)."',
ma_perioxi='".$db_link->escape_string($dr_user_ma_perioxi)."',
ma_poli='".$db_link->escape_string($dr_user_ma_poli)."',
ma_tk='".$db_link->escape_string($dr_user_ma_tk)."',
ma_country_id=".$dr_user_ma_country_id.",
ma_nomos_id=".$dr_user_ma_nomos_id.",
fiscal_position_id=".$fiscal_position_id.",
pricelist_id=".$pricelist_id.",
def_ekptosi=".number_format($def_ekptosi, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
coupons='".$db_link->escape_string($coupons_str)."',
parastatiko=".$form_parastatiko.",
eponimia='".$db_link->escape_string($dr_user_eponimia)."',
title='".$db_link->escape_string($dr_user_title)."',
afm='".$db_link->escape_string($dr_user_afm)."',
doy='".$db_link->escape_string($dr_user_doy)."',
epaggelma='".$db_link->escape_string($dr_user_epaggelma)."',

products_need_pliromi=".($gks_price_total==0 ? '0':'1').",
products_need_pliromi=".($gks_price_total==0 ? '0':'1').",

kostos_apostolis=".number_format($kostos_apostolis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
tropos_apostolis=".$tropos_apostolis.",
tropos_apostolis_json='".$db_link->escape_string($tropos_apostolis_json)."',

kostos_pliromis=".number_format($kostos_pliromis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
tropos_pliromis=".$tropos_pliromis.",
kostos_pliromis_json='".$db_link->escape_string($kostos_pliromis_json)."',

gks_price_original_net=".number_format($gks_price_original_net, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
gks_price_net=".number_format($gks_price_net, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
gks_price_fpa=".number_format($gks_price_fpa, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
gks_price_netfpa=".number_format($gks_price_netfpa, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').", 
gks_price_total=".number_format($gks_price_total, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",

totalWithheldAmount=".number_format($totalWithheldAmount, 10, '.', '').", 
totalOtherTaxesAmount=".number_format($totalOtherTaxesAmount, 10, '.', '').", 
totalStampDutyamount=".number_format($totalStampDutyamount, 10, '.', '').", 
totalFeesAmount=".number_format($totalFeesAmount, 10, '.', '').", 

affect_balance= ".$affect_balance.",
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
        $affect_balance_poso=$gks_price_total + $kostos_pliromis; // + $kostos_apostolis
        break;  
      default:     
      
    }
  } else {
    //$affect_balance_poso=$affect_balance_poso;
  }
}
$sql.="affect_balance_poso=".number_format($affect_balance_poso, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",";

$affect_balance_pros=1; //$row_old['eidos_parastatikou_balance_pros'];


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
$sql.="  
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hotel_reservation = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
//if ($postype == '') {
//  print '<pre>';
//  print_r($roolist);
//  die();
//}

$sql="UPDATE gks_hotel_reservation_room 
LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product 
SET gks_hotel_reservation_room.product_descr = gks_eshop_products.product_descr
WHERE gks_eshop_products.product_descr is not null
AND gks_eshop_products.id_product Is Not Null
and gks_hotel_reservation_room.hotel_reservation_id=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$sql="UPDATE gks_hotel_reservation_room 
LEFT JOIN gks_eshop_pricelist_items ON gks_hotel_reservation_room.product_pricelist_item_id = gks_eshop_pricelist_items.id_pricelist_item 
SET gks_hotel_reservation_room.product_pricelist_item_descr = gks_eshop_pricelist_items.pricelist_item_descr
WHERE gks_eshop_pricelist_items.pricelist_item_descr Is Not Null 
AND gks_eshop_pricelist_items.id_pricelist_item Is Not Null
AND gks_hotel_reservation_room.hotel_reservation_id=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }




$roolist_day=array();
foreach ($roolist as &$myroom) {
  //echo '<pre>';var_dump($myroom);die();
  //if ($myroom['add']==1 or $myroom['edit']==1 or $myroom['delete']==1) {
    if ($myroom['delete'] == 1) {
      if ($myroom['recid'] >0) {
        $sql="delete from gks_hotel_reservation_room where id_hotel_reservation_room=".$myroom['recid']." and hotel_reservation_id=".$id." limit 1";
        $result = $db_link->query($sql); 
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); } 
        $sql="delete from gks_hotel_reservation_room_day where hotel_reservation_room_id=".$myroom['recid']." and hotel_reservation_id=".$id;
        $result = $db_link->query($sql); 
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); } 
        
        //die();         
      }
    } else if ($myroom['add'] == 1) {
      $sql="insert into gks_hotel_reservation_room (
      user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
      hotel_reservation_id,hotel_room_id,rnum_adults,rnum_childs,rchilds_ages_list,
      rnum_child_kounies,rnum_extra_beds,
      ruser_id,ruser_lang,ruser_first_name,ruser_last_name,ruser_email,ruser_mobile,
      ruser_ma_odos,ruser_ma_arithmos,ruser_ma_orofos,ruser_ma_perioxi,ruser_ma_poli,ruser_ma_tk,ruser_ma_country_id,ruser_ma_nomos_id,rsxolio,
      ruser_fiscal_position_id,ruser_pricelist_id,
      product_id,
      product_fpa_base_id,
      product_fpa_id,
      product_fpa_pososto,
      product_fpa_id_json,
      product_price_include_vat,
      product_price_start_peritem_db,
      product_price_start_peritem_net,
      product_price_start_peritem_fpa,
      product_price_start_peritem_total,
      product_price_start_all_net,
      product_price_start_all_fpa,
      product_price_start_all_total,
      product_price_final_peritem_db,
      product_price_final_peritem_net,
      product_price_final_peritem_fpa,
      product_price_final_peritem_total,
      product_price_final_all_net,
      product_price_final_all_fpa,
      product_price_final_all_total,
      product_price_ekptosi_net,
      product_price_ekptosi_pososto,
      product_pricelist_item_id,
      product_pricelist_item_percent,
      product_price_coupon_use,
      product_price_coupon_use_disabled,
      
      product_quantity,
      
      room_ajia_table_math,
      room_ajia_table_html,
      room_ajia_table_array,
      
      product_withheldPercentCategory,
      product_withheldAmount,
      product_otherTaxesPercentCategory,
      product_otherTaxesAmount,
      product_stampDutyPercentCategory,
      product_stampDutyAmount,
      product_feesPercentCategory,
      product_feesAmount

      
      
      ) values (
      ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
      ".$id.",".$myroom['hotel_room_id'].",".$myroom['rnum_adults'].",".$myroom['rnum_childs'].",
      '".$db_link->escape_string($myroom['rchilds_ages_list'])."',
      ".$myroom['rnum_child_kounies'].",".$myroom['rnum_extra_beds'].",
      
      ".$myroom['ruser_id'].",
      '".$db_link->escape_string($myroom['ruser_lang'])."',
      '".$db_link->escape_string($myroom['ruser_first_name'])."',
      '".$db_link->escape_string($myroom['ruser_last_name'])."',
      '".$db_link->escape_string($myroom['ruser_email'])."',
      '".$db_link->escape_string($myroom['ruser_mobile'])."',
      '".$db_link->escape_string($myroom['ruser_ma_odos'])."',
      '".$db_link->escape_string($myroom['ruser_ma_arithmos'])."',
      '".$db_link->escape_string($myroom['ruser_ma_orofos'])."',
      '".$db_link->escape_string($myroom['ruser_ma_perioxi'])."',
      '".$db_link->escape_string($myroom['ruser_ma_poli'])."',
      '".$db_link->escape_string($myroom['ruser_ma_tk'])."',
      ".$myroom['ruser_ma_country_id'].",
      ".$myroom['ruser_ma_nomos_id'].",
      '".$db_link->escape_string($myroom['rsxolio'])."',
      ".$myroom['ruser_fiscal_position_id'].",
      ".$myroom['ruser_pricelist_id'].",
      
      ".$myroom['pdata']['product_id'].",
      ".$myroom['pdata']['product_fpa_base_id'].",
      ".$myroom['pdata']['product_fpa_id'].",
      ".number_format($myroom['pdata']['product_fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      '".$db_link->escape_string(json_encode($myroom['pdata']['product_fpa_id_json']))."',
      ".$myroom['pdata']['product_price_include_vat'].",
      ".number_format($myroom['pdata']['product_price_start_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_ekptosi_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_ekptosi_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".$myroom['pdata']['product_pricelist_item_id'].",
      ".number_format($myroom['pdata']['product_pricelist_item_percent'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      '".$db_link->escape_string($myroom['pdata']['product_price_coupon_use'])."',
      ".$myroom['pdata']['product_price_coupon_use_disabled'].",
      
      ".$num_days.",
      '".$db_link->escape_string($myroom['pdata']['ajia_table_math'])."',
      '".$db_link->escape_string($myroom['pdata']['ajia_table_html'])."',
      '".$db_link->escape_string($myroom['pdata']['ajia_table_array'])."',
      
      
      ".intval($myroom['pdata']['other_taxes']['withheldPercentCategory']).",
      ".floatval($myroom['pdata']['other_taxes']['withheldAmount']).",
      ".intval($myroom['pdata']['other_taxes']['otherTaxesPercentCategory']).",
      ".floatval($myroom['pdata']['other_taxes']['otherTaxesAmount']).",
      ".intval($myroom['pdata']['other_taxes']['stampDutyPercentCategory']).",
      ".floatval($myroom['pdata']['other_taxes']['stampDutyAmount']).",
      ".intval($myroom['pdata']['other_taxes']['feesPercentCategory']).",
      ".floatval($myroom['pdata']['other_taxes']['feesAmount'])."


      
      
      )";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }          
      
      $myroom['recid'] = $db_link->insert_id;    
      
    } else if ($myroom['recid']>0) { //($myroom['edit'] == 1) {
      
      //print '<pre>';print_r($myroom['pdata']); die();
      
      $sql="update gks_hotel_reservation_room set 
      user_id_edit=".$my_wp_user_id.",
      mydate_edit=now(),
      myip='".$db_link->escape_string($gkIP)."',
      hotel_room_id=".$myroom['hotel_room_id'].",
      rnum_adults=".$myroom['rnum_adults'].",
      rnum_childs=".$myroom['rnum_childs'].",
      rchilds_ages_list='".$db_link->escape_string($myroom['rchilds_ages_list'])."',
      rnum_child_kounies=".$myroom['rnum_child_kounies'].",
      rnum_extra_beds=".$myroom['rnum_extra_beds'].",
      
      ruser_id=".$myroom['ruser_id'].",
      ruser_lang='".$db_link->escape_string($myroom['ruser_lang'])."',
      ruser_first_name='".$db_link->escape_string($myroom['ruser_first_name'])."',
      ruser_last_name='".$db_link->escape_string($myroom['ruser_last_name'])."',
      ruser_email='".$db_link->escape_string($myroom['ruser_email'])."',
      ruser_mobile='".$db_link->escape_string($myroom['ruser_mobile'])."',
      ruser_ma_odos='".$db_link->escape_string($myroom['ruser_ma_odos'])."',
      ruser_ma_arithmos='".$db_link->escape_string($myroom['ruser_ma_arithmos'])."',
      ruser_ma_orofos='".$db_link->escape_string($myroom['ruser_ma_orofos'])."',
      ruser_ma_perioxi='".$db_link->escape_string($myroom['ruser_ma_perioxi'])."',
      ruser_ma_poli='".$db_link->escape_string($myroom['ruser_ma_poli'])."',
      ruser_ma_tk='".$db_link->escape_string($myroom['ruser_ma_tk'])."',
      ruser_ma_country_id=".$myroom['ruser_ma_country_id'].",
      ruser_ma_nomos_id=".$myroom['ruser_ma_nomos_id'].",
      rsxolio='".$db_link->escape_string($myroom['rsxolio'])."',
      ruser_fiscal_position_id=".$myroom['ruser_fiscal_position_id'].",
      ruser_pricelist_id=".$myroom['ruser_pricelist_id'].",
      
      
      product_id=".$myroom['pdata']['product_id'].",
      product_fpa_base_id=".$myroom['pdata']['product_fpa_base_id'].",
      product_fpa_id=".$myroom['pdata']['product_fpa_id'].",
      product_fpa_pososto=".number_format($myroom['pdata']['product_fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",

      product_fpa_id_json='".$db_link->escape_string(json_encode($myroom['pdata']['product_fpa_id_json']))."',
      product_price_include_vat=".$myroom['pdata']['product_price_include_vat'].",
      product_price_start_peritem_db=".number_format($myroom['pdata']['product_price_start_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_peritem_net=".number_format($myroom['pdata']['product_price_start_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_peritem_fpa=".number_format($myroom['pdata']['product_price_start_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_peritem_total=".number_format($myroom['pdata']['product_price_start_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_all_net=".number_format($myroom['pdata']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_all_fpa=".number_format($myroom['pdata']['product_price_start_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_all_total=".number_format($myroom['pdata']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_peritem_db=".number_format($myroom['pdata']['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_peritem_net=".number_format($myroom['pdata']['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_peritem_fpa=".number_format($myroom['pdata']['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_peritem_total=".number_format($myroom['pdata']['product_price_final_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_all_net=".number_format($myroom['pdata']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_all_fpa=".number_format($myroom['pdata']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_all_total=".number_format($myroom['pdata']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_ekptosi_net=".number_format($myroom['pdata']['product_price_ekptosi_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_ekptosi_pososto=".number_format($myroom['pdata']['product_price_ekptosi_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_pricelist_item_id=".$myroom['pdata']['product_pricelist_item_id'].",
      product_pricelist_item_percent=".number_format($myroom['pdata']['product_pricelist_item_percent'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_coupon_use='".$db_link->escape_string($myroom['pdata']['product_price_coupon_use'])."',
      product_price_coupon_use_disabled=".$myroom['pdata']['product_price_coupon_use_disabled'].",
      
      product_quantity=".$num_days.",

      room_ajia_table_math='".$db_link->escape_string($myroom['pdata']['ajia_table_math'])."',
      room_ajia_table_html='".$db_link->escape_string($myroom['pdata']['ajia_table_html'])."',
      room_ajia_table_array='".$db_link->escape_string($myroom['pdata']['ajia_table_array'])."',
      
      product_withheldPercentCategory=".intval($myroom['pdata']['other_taxes']['withheldPercentCategory']).",
      product_withheldAmount=".floatval($myroom['pdata']['other_taxes']['withheldAmount']).",
      product_otherTaxesPercentCategory=".intval($myroom['pdata']['other_taxes']['otherTaxesPercentCategory']).",
      product_otherTaxesAmount=".floatval($myroom['pdata']['other_taxes']['otherTaxesAmount']).",
      product_stampDutyPercentCategory=".intval($myroom['pdata']['other_taxes']['stampDutyPercentCategory']).",
      product_stampDutyAmount=".floatval($myroom['pdata']['other_taxes']['stampDutyAmount']).",
      product_feesPercentCategory=".intval($myroom['pdata']['other_taxes']['feesPercentCategory']).",
      product_feesAmount=".floatval($myroom['pdata']['other_taxes']['feesAmount'])."

      
      where id_hotel_reservation_room=".$myroom['recid']." and hotel_reservation_id=".$id." limit 1";
      //echo 'ddddddd';
      //die();
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }         
      
    
    }
  //}
  $hotel_type_room_id=0;
  if (isset($roomcheck2[$myroom['hotel_room_id']])) {
    $hotel_type_room_id=$roomcheck2[$myroom['hotel_room_id']]['hotel_room_type_id'];
  }
  
  $roolist_day[]=array('delete'=>$myroom['delete'], 'hotel_room_id'=> $myroom['hotel_room_id'], 'recid'=> $myroom['recid'], 'hotel_type_room_id'=>$hotel_type_room_id);
}
unset($myroom);

//print '<pre>|'.$id.'|'.$reservation_status."\r\n|".$reservation_status_old."\r\n"; print_r($roolist_day);print_r($days_round);die();
  
gks_hotel_reservation_room_day_recs($id,$roolist_day,
  ($reservation_status!='' ? $reservation_status : $reservation_status_old),
  $days_round['check_in_round_time'],$days_round['check_out_round_time']
);

//print '<pre>';
//print_r($myroom);
//print_r($exist_data);
//print_r($delete_ids);
//die(); 
        

//    if ($myroom['delete'] == 1) {
//      if ($myroom['recid'] >0) {
//        $sql="delete from gks_hotel_reservation_room_day where hotel_reservation_room_id=".$myroom['recid']." and hotel_reservation_id=".$id;
//        $result = $db_link->query($sql); 
//        if (!$result) {
//          debug_mail(false,'error sql',$sql);
//          $return = array('success' => false, 'message' => base64_encode('sql error'));
//          echo json_encode($return); die(); }         
//      }
//      
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($roolist,true).'</pre>'));
//$return = array('success' => false, 'message' => base64_encode('ppppppppp'));
//echo json_encode($return); die();


$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

if ($is_new_rec == false) {

  gks_hotel_reservation_sxolio_log($id,$row_old,$rooms_old,'',$gks_custom_row_old);
  
}

$balance_user=gks_balance_calc(['id' => $user_id]);
if (isset($row_old['user_id']) and $row_old['user_id']>0 and $row_old['user_id']!=$user_id) gks_balance_calc(['id' => $row_old['user_id']]);


gks_update_user_from_some_move(array('user_id'=>$user_id,'table'=>'gks_hotel_reservation','id_table'=>$id));

$redirect='';
if ($is_new_rec)  {
  $redirect='admin-hotel-reservation-item.php?id='.$id;
}
$return = array('success' => true, 'message' => base64_encode('ok'),'redirect' => base64_encode($redirect),'save_but_message' => '');
echo json_encode($return); die();


function gks_hotel_reservation_to_draft($id) {
  
  global $db_link;


  //die('<pre>ssss');
  
  $sql="select * from gks_hotel_reservation where id_hotel_reservation=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_reservation = $result->fetch_assoc();
  $reservation_seira_id=$row_reservation['reservation_seira_id'];
  $reservation_number_int_old=$row_reservation['reservation_number_int'];
  
  $sql="select * from gks_acc_seires where id_acc_seira=".$reservation_seira_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $row_seira = $result->fetch_assoc();
  $prev_number=$row_seira['next_number']-$row_seira['number_step'];
  
  
  $warning_message='';
  if ($prev_number!=$reservation_number_int_old) {
    $warning_message=
          gks_lang('Επόμενος αριθμός σειράς').': <b>'.$row_seira['next_number'].'</b><br>'.
          gks_lang('Βήμα σειράς').': <b>'.$row_seira['number_step'].'</b><br>'.
          gks_lang('Τρέχον αριθμός κράτησης').': <b>'.$reservation_number_int_old.'</b> (<>'.
          $row_seira['next_number'].'-'.$row_seira['number_step'].')';
          
    debug_mail(false,'prev_number is not equal reservation_number_int_old',$prev_number.' != '.$reservation_number_int_old.' '.$warning_message);
    //$return = array('success' => false, 'message' => base64_encode('Έχουν εκδοθεί ενδιάμεσα παραστατικά σε αυτήν την σειρά και θα δημιουργηθούν κενά στην αρίθμηση.<br>'.$temp));
    //echo json_encode($return); die(); 
  } else {  
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $sql="update gks_acc_seires set next_number=next_number-number_step where id_acc_seira=".$reservation_seira_id;
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
    where acc_seira_id=".$reservation_seira_id." and reservation_id=".$id." and disabled_date is null";
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    

  }
  //$return = array('success' => false, 'message' => base64_encode('sssssss'));
  //echo json_encode($return); die();
  
  if ($prev_number==$reservation_number_int_old) {
    $sql="update gks_hotel_reservation set reservation_status='010draft', reservation_number_int=0, reservation_number_str=null,reservation_ekdosi_date=null where id_hotel_reservation=".$id;
  } else {
    $sql="update gks_hotel_reservation set reservation_status='010draft' where id_hotel_reservation=".$id;
  }
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $sql="update gks_hotel_reservation_room_day set dreservation_status='010draft' where hotel_reservation_id=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  
  return $warning_message;
}


function gks_hotel_reservation_get_ekdosi_numbers() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $reservation_number_int_old;
  global $reservation_number_int_new;
  global $reservation_number_str_new;
  global $reservation_seira_code_new;
  global $reservation_seira_id;
  global $has_ekdosi;
  global $save_but_message;
  global $id;
  global $reservation_status;
  
  //die('<pre>reservation_number_int_old:'.$reservation_number_int_old);
  if ($reservation_number_int_old>0) {
    $sql_auto_number="select auto_number from gks_acc_seires_auto_numbers where disabled_date is null and acc_seira_id=".$reservation_seira_id." and reservation_id=".$id;
    $result_auto_number = $db_link->query($sql_auto_number);  
    if (!$result_auto_number) {
      debug_mail(false,'error sql',$sql_auto_number);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result_auto_number->num_rows>=1) {
      $row_auto_number = $result_auto_number->fetch_assoc();    
      $reservation_number_int_old=$row_auto_number['auto_number'];
      $reservation_number_int_new=$row_auto_number['auto_number'];

      $sql="select * from gks_acc_seires where id_acc_seira=".$reservation_seira_id;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      $row_seira = $result->fetch_assoc();
      $reservation_seira_code_new=trim_gks($row_seira['seira_code']);
      $seires_prefix=trim_gks($row_seira['prefix']);
      $seires_suffix=trim_gks($row_seira['suffix']);
      $seires_number_size=$row_seira['number_size'];
      $reservation_number_str_new=$seires_prefix.str_pad($reservation_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
      $has_ekdosi=true;
    }
  }
  
  if ($reservation_number_int_old==0) {
    $reservation_state='';
    
    
    
    $sql="lock tables gks_acc_seires WRITE;";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="select * from gks_acc_seires where id_acc_seira=".$reservation_seira_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $row_seira = $result->fetch_assoc();
    $reservation_seira_code_new=trim_gks($row_seira['seira_code']);
    $seires_prefix=trim_gks($row_seira['prefix']);
    $seires_suffix=trim_gks($row_seira['suffix']);
    $seires_number_size=$row_seira['number_size'];
    $reservation_number_int_new=$row_seira['next_number'];
    $reservation_number_str_new=$seires_prefix.str_pad($reservation_number_int_new, $seires_number_size, '0', STR_PAD_LEFT).$seires_suffix;
    //$save_but_message='<pre>'.$reservation_number_str_new;
    
    $sql="update gks_acc_seires set next_number=next_number+number_step where id_acc_seira=".$reservation_seira_id;
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
  
    $reservation_state='070wait_payment';
    $has_ekdosi=true;
    if ($save_but_message!='') {
      $save_but_message=gks_lang('Η κράτηση έχει αποθηκευτεί αλλά δεν έχει εκδοθεί διότι').':<br>'.$save_but_message;
    }
    
    $sql="insert into gks_acc_seires_auto_numbers (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_seira_id,reservation_id,auto_number
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$reservation_seira_id.",".$id.",".$reservation_number_int_new."
    )";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
  
}
