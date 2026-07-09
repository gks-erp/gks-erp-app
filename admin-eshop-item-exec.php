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
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση eshop').' id:' . $id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshops',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




if ($id>0) {
  $sql="select * from gks_eshops where id_eshop=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
}


$eshop_name=''; if (isset($_POST['eshop_name'])) $eshop_name=trim_gks(base64_decode($_POST['eshop_name']));
$eshop_url=''; if (isset($_POST['eshop_url'])) $eshop_url=trim_gks(base64_decode($_POST['eshop_url']));
$company_id=0; if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$company_sub_id=0; if (isset($_POST['company_sub_id'])) $company_sub_id=intval($_POST['company_sub_id']);
$eshop_key=''; if (isset($_POST['eshop_key'])) $eshop_key=trim_gks(base64_decode($_POST['eshop_key']));
$eshop_autosync=0; if (isset($_POST['eshop_autosync'])) $eshop_autosync=intval($_POST['eshop_autosync']);
$eshop_sortorder=0; if (isset($_POST['eshop_sortorder'])) $eshop_sortorder=intval($_POST['eshop_sortorder']);
$eshop_disable=0; if (isset($_POST['eshop_disable'])) $eshop_disable=intval($_POST['eshop_disable']);
$tax_class_basikos=''; if (isset($_POST['tax_class_basikos'])) $tax_class_basikos=trim_gks(base64_decode($_POST['tax_class_basikos']));
$tax_class_meiomenos=''; if (isset($_POST['tax_class_meiomenos'])) $tax_class_meiomenos=trim_gks(base64_decode($_POST['tax_class_meiomenos']));
$tax_class_ypermeiomenos=''; if (isset($_POST['tax_class_ypermeiomenos'])) $tax_class_ypermeiomenos=trim_gks(base64_decode($_POST['tax_class_ypermeiomenos']));
$tax_class_yperypermeiomenos=''; if (isset($_POST['tax_class_yperypermeiomenos'])) $tax_class_yperypermeiomenos=trim_gks(base64_decode($_POST['tax_class_yperypermeiomenos']));
$tax_class_xorisfpa=''; if (isset($_POST['tax_class_xorisfpa'])) $tax_class_xorisfpa=trim_gks(base64_decode($_POST['tax_class_xorisfpa']));

$order_find_user_from=''; if (isset($_POST['order_find_user_from'])) $order_find_user_from=trim_gks(base64_decode($_POST['order_find_user_from']));
$parts=explode(',',trim_gks($order_find_user_from));
$temp=array();
foreach ($parts as $value) {
  switch ($value) {   
    case 'ΑΦΜ': case gks_lang('ΑΦΜ'): $temp[]='afm';break;      
    case 'Κινητό': case gks_lang('Κινητό'): $temp[]='mobile';break;      
    case 'email': case gks_lang('email'): $temp[]='email';break;      
    case 'Σταθερό': case gks_lang('Σταθερό'): $temp[]='phone';break;      
    case 'Επαφή Παραγγελίας': case gks_lang('Επαφή Παραγγελίας'): $temp[]='user';break;      
  }
} 
$order_find_user_from=implode(',',$temp);

$order_meta_user_lang=''; if (isset($_POST['order_meta_user_lang'])) $order_meta_user_lang=trim_gks(base64_decode($_POST['order_meta_user_lang']));
$order_meta_parastatiko=''; if (isset($_POST['order_meta_parastatiko'])) $order_meta_parastatiko=trim_gks(base64_decode($_POST['order_meta_parastatiko']));
$order_meta_eponimia=''; if (isset($_POST['order_meta_eponimia'])) $order_meta_eponimia=trim_gks(base64_decode($_POST['order_meta_eponimia']));
$order_meta_title=''; if (isset($_POST['order_meta_title'])) $order_meta_title=trim_gks(base64_decode($_POST['order_meta_title']));
$order_meta_afm=''; if (isset($_POST['order_meta_afm'])) $order_meta_afm=trim_gks(base64_decode($_POST['order_meta_afm']));
$order_meta_doy=''; if (isset($_POST['order_meta_doy'])) $order_meta_doy=trim_gks(base64_decode($_POST['order_meta_doy']));
$order_meta_epaggelma=''; if (isset($_POST['order_meta_epaggelma'])) $order_meta_epaggelma=trim_gks(base64_decode($_POST['order_meta_epaggelma']));

$woo_start_booking_number=''; if (isset($_POST['woo_start_booking_number'])) $woo_start_booking_number=trim_gks(base64_decode($_POST['woo_start_booking_number']));
              
//echo '<pre>'; echo $order_find_user_from;die();

$unique_tax=array();
if ($tax_class_basikos!='') $unique_tax[]=$tax_class_basikos;
if ($tax_class_meiomenos!='') {
  if (in_array($tax_class_meiomenos,$unique_tax)) {
    $message=gks_lang('Η κλάση <b>[1]</b> θα πρέπει να ορισθεί μία μόνο φορά');
    $message=str_replace('[1]',$tax_class_meiomenos,$message);
    debug_mail(false,'emptyl',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  $unique_tax[]=$tax_class_meiomenos;
}
if ($tax_class_ypermeiomenos!='') {
  if (in_array($tax_class_ypermeiomenos,$unique_tax)) {
    $message=gks_lang('Η κλάση <b>[1]</b> θα πρέπει να ορισθεί μία μόνο φορά');
    $message=str_replace('[1]',$tax_class_ypermeiomenos,$message);
    debug_mail(false,'emptyl',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  $unique_tax[]=$tax_class_ypermeiomenos;
}
if ($tax_class_yperypermeiomenos!='') {
  if (in_array($tax_class_yperypermeiomenos,$unique_tax)) {
    $message=gks_lang('Η κλάση <b>[1]</b> θα πρέπει να ορισθεί μία μόνο φορά');
    $message=str_replace('[1]',$tax_class_yperypermeiomenos,$message);
    debug_mail(false,'emptyl',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  $unique_tax[]=$tax_class_yperypermeiomenos;
}
if ($tax_class_xorisfpa!='') {
  if (in_array($tax_class_xorisfpa,$unique_tax)) {
    $message=gks_lang('Η κλάση <b>'.$tax_class_xorisfpa.'</b> θα πρέπει να ορισθεί μία μόνο φορά');
    $message=str_replace('[1]',$tax_class_xorisfpa,$message);
    debug_mail(false,'emptyl',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  $unique_tax[]=$tax_class_xorisfpa;
}



if (endwith($eshop_url,'/')) $eshop_url=substr($eshop_url, 0, strlen($eshop_url)-1);
if (endwith($eshop_url,'/')) $eshop_url=substr($eshop_url, 0, strlen($eshop_url)-1);
if (endwith($eshop_url,'/')) $eshop_url=substr($eshop_url, 0, strlen($eshop_url)-1);
if (endwith($eshop_url,'/')) $eshop_url=substr($eshop_url, 0, strlen($eshop_url)-1);


$woo_delivery_to_gks_str = trim_gks(base64_decode($_POST['woo_delivery_to_gks_str']));
$woo_delivery_to_gks = json_decode($woo_delivery_to_gks_str, true);
if ($woo_delivery_to_gks === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['woo_delivery_to_gks_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
$temp=[];
foreach ($woo_delivery_to_gks as $value) {
  $value['g']=intval($value['g']);
  $value['w']=trim_gks($value['w']);
  $value['wt']=trim_gks($value['wt']);
  if ($value['g']>=0 and $value['w']!=''and $value['wt']!='') {
    $temp[]=array('g' => $value['g'], 'w' => $value['w'], 'wt' => $value['wt']);
  }
} 
$woo_delivery_to_gks=$temp;
//print '<pre>'; print_r($woo_delivery_to_gks);die();  

$woo_payment_to_gks_str = trim_gks(base64_decode($_POST['woo_payment_to_gks_str']));
$woo_payment_to_gks = json_decode($woo_payment_to_gks_str, true);
if ($woo_payment_to_gks === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['woo_payment_to_gks_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
$temp=[];
foreach ($woo_payment_to_gks as $value) {
  $value['g']=intval($value['g']);
  $value['w']=trim_gks($value['w']);
  $value['wt']=trim_gks($value['wt']);
  if ($value['g']>=0 and $value['w']!='') {
    $temp[]=array('g' => $value['g'], 'w' => $value['w'], 'wt' => $value['wt']);
  }
} 
$woo_payment_to_gks=$temp;
//print '<pre>'; print_r($woo_payment_to_gks);die();  


$import_yes=0;if (isset($_POST['import_yes'])) $import_yes=intval($_POST['import_yes']);
if ($import_yes!=1) $import_yes=0;
$import_as='';if (isset($_POST['import_as'])) $import_as=trim_gks(base64_decode($_POST['import_as']));
if ($import_as!='transfer' and $import_as!='reservation' and $import_as!='order' and $import_as!='acc_inv') $import_as='';
$acc_journal_id=0;if (isset($_POST['acc_journal_id'])) $acc_journal_id=intval($_POST['acc_journal_id']);
$acc_journal_id_tim=0;if (isset($_POST['acc_journal_id_tim'])) $acc_journal_id_tim=intval($_POST['acc_journal_id_tim']);
$acc_seira_id=0;if (isset($_POST['acc_seira_id'])) $acc_seira_id=intval($_POST['acc_seira_id']);
$acc_seira_id_tim=0;if (isset($_POST['acc_seira_id_tim'])) $acc_seira_id_tim=intval($_POST['acc_seira_id_tim']);
$warehouses_id_from=0;if (isset($_POST['warehouses_id_from'])) $warehouses_id_from=intval($_POST['warehouses_id_from']);
$warehouses_id_from_tim=0;if (isset($_POST['warehouses_id_from_tim'])) $warehouses_id_from_tim=intval($_POST['warehouses_id_from_tim']);

$will_update=0;if (isset($_POST['will_update'])) $will_update=intval($_POST['will_update']);
if ($will_update!=1) $will_update=0;
$update_if_gks_change=0;if (isset($_POST['update_if_gks_change'])) $update_if_gks_change=intval($_POST['update_if_gks_change']);
if ($update_if_gks_change!=1) $update_if_gks_change=0;
$update_state_gks_transfer=''; if (isset($_POST['update_state_gks_transfer'])) $update_state_gks_transfer=trim_gks(base64_decode($_POST['update_state_gks_transfer']));
$update_state_gks_reservation=''; if (isset($_POST['update_state_gks_reservation'])) $update_state_gks_reservation=trim_gks(base64_decode($_POST['update_state_gks_reservation']));
$update_state_gks_order=''; if (isset($_POST['update_state_gks_order'])) $update_state_gks_order=trim_gks(base64_decode($_POST['update_state_gks_order']));
$update_state_gks_acc_inv=''; if (isset($_POST['update_state_gks_acc_inv'])) $update_state_gks_acc_inv=trim_gks(base64_decode($_POST['update_state_gks_acc_inv']));
$update_state_woo=''; if (isset($_POST['update_state_woo'])) $update_state_woo=trim_gks(base64_decode($_POST['update_state_woo']));


$temp=$update_state_gks_transfer;
if ($temp!='') {
  $parts=explode(',',$temp);  
  $clean=array();
  if (defined('GKS_TRANSFER') and GKS_TRANSFER) {
    foreach ($parts as $value) {
      if      ($value==getTransferReservationStatusDescr('005prodraft'))        $clean[]='005prodraft';
      else if ($value==getTransferReservationStatusDescr('010draft'))           $clean[]='010draft';
      else if ($value==getTransferReservationStatusDescr('040cancelled'))       $clean[]='040cancelled';
      else if ($value==getTransferReservationStatusDescr('050rejected'))        $clean[]='050rejected';
      else if ($value==getTransferReservationStatusDescr('070wait_payment'))    $clean[]='070wait_payment';
      else if ($value==getTransferReservationStatusDescr('080confirm'))         $clean[]='080confirm';
      else if ($value==getTransferReservationStatusDescr('100completed'))       $clean[]='100completed';
      else if ($value==getTransferReservationStatusDescr('110payment'))         $clean[]='110payment';
    }
  }
  $update_state_gks_transfer=implode(',',$clean);
}
  
$temp=$update_state_gks_reservation;
if ($temp!='') {
  $parts=explode(',',$temp);  
  $clean=array();
  foreach ($parts as $value) {
    if      ($value==getHotelReservationStatusDescr('005prodraft'))        $clean[]='005prodraft';
    else if ($value==getHotelReservationStatusDescr('010draft'))           $clean[]='010draft';
    else if ($value==getHotelReservationStatusDescr('040cancelled'))       $clean[]='040cancelled';
    else if ($value==getHotelReservationStatusDescr('050rejected'))        $clean[]='050rejected';
    else if ($value==getHotelReservationStatusDescr('070wait_payment'))    $clean[]='070wait_payment';
    else if ($value==getHotelReservationStatusDescr('080confirm'))         $clean[]='080confirm';
    else if ($value==getHotelReservationStatusDescr('100completed'))       $clean[]='100completed';
    else if ($value==getHotelReservationStatusDescr('110payment'))         $clean[]='110payment';
  }
  $update_state_gks_reservation=implode(',',$clean);
}

$temp=$update_state_gks_order;
if ($temp!='') {
  $parts=explode(',',$temp);  
  $clean=array();
  foreach ($parts as $value) {
    if      ($value==getOrderStateDescr('005prodraft'))        $clean[]='005prodraft';
    else if ($value==getOrderStateDescr('010draft'))           $clean[]='010draft';
    else if ($value==getOrderStateDescr('020pending'))         $clean[]='020pending';
    else if ($value==getOrderStateDescr('025offer'))           $clean[]='025offer';
    else if ($value==getOrderStateDescr('030forcancellation')) $clean[]='030forcancellation';
    else if ($value==getOrderStateDescr('040cancelled'))       $clean[]='040cancelled';
    else if ($value==getOrderStateDescr('050rejected'))        $clean[]='050rejected';
    else if ($value==getOrderStateDescr('055wait_payment'))    $clean[]='055wait_payment';
    else if ($value==getOrderStateDescr('060registered'))      $clean[]='060registered';
    else if ($value==getOrderStateDescr('070inproduction'))    $clean[]='070inproduction';
    else if ($value==getOrderStateDescr('080failed'))          $clean[]='080failed';
    else if ($value==getOrderStateDescr('090indelivery'))      $clean[]='090indelivery';
    else if ($value==getOrderStateDescr('095execute'))         $clean[]='095execute';
    else if ($value==getOrderStateDescr('100completed'))       $clean[]='100completed';
    else if ($value==getOrderStateDescr('110payment'))         $clean[]='110payment';
  }
  $update_state_gks_order=implode(',',$clean);
}

$temp=$update_state_gks_acc_inv;
if ($temp!='') {
  $parts=explode(',',$temp);  
  $clean=array();
  foreach ($parts as $value) {
    if      ($value==getAccInvStateDescr('010draft'))      $clean[]='010draft';
    else if ($value==getAccInvStateDescr('040cancelled'))  $clean[]='040cancelled';
    else if ($value==getAccInvStateDescr('050proinvoice')) $clean[]='050proinvoice';
    else if ($value==getAccInvStateDescr('080listing'))    $clean[]='080listing';
    else if ($value==getAccInvStateDescr('090ekdosi'))     $clean[]='090ekdosi';
    else if ($value==getAccInvStateDescr('100payment'))    $clean[]='100payment';
  }
  $update_state_gks_acc_inv=implode(',',$clean);
}

$temp=$update_state_woo;
if ($temp!='') {
  $parts=explode(',',$temp);  
  $clean=array();
  foreach ($parts as $value) {
    if      ($value==gks_woo_order_state_descr('pending'))    $clean[]='pending';
    else if ($value==gks_woo_order_state_descr('processing')) $clean[]='processing';
    else if ($value==gks_woo_order_state_descr('on-hold'))    $clean[]='on-hold';
    else if ($value==gks_woo_order_state_descr('completed'))  $clean[]='completed';
    else if ($value==gks_woo_order_state_descr('cancelled'))  $clean[]='cancelled';
    else if ($value==gks_woo_order_state_descr('refunded'))   $clean[]='refunded';
    else if ($value==gks_woo_order_state_descr('failed'))     $clean[]='failed';
  }
  $update_state_woo=implode(',',$clean);
}

$acc_inv_product_shipping=0;if (isset($_POST['acc_inv_product_shipping'])) $acc_inv_product_shipping=intval($_POST['acc_inv_product_shipping']);
$acc_inv_product_fees=0;if (isset($_POST['acc_inv_product_fees'])) $acc_inv_product_fees=intval($_POST['acc_inv_product_fees']);


if ($company_sub_id<0) $company_sub_id=0;

if ($eshop_name=='') {debug_mail(false,'emptyl',                 gks_lang('Ορίστε το όνομα του eshop'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το όνομα του eshop')));
  echo json_encode($return); die();}

if ($eshop_url=='') {debug_mail(false,'emptyl',                  gks_lang('Ορίστε το όνομα το URL του eshop'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το όνομα το URL του eshop')));
  echo json_encode($return); die();}


if(filter_var($eshop_url, FILTER_VALIDATE_URL)==false) {
  debug_mail(false,'emptyl',                                     gks_lang('Το URL δεν είναι σωστό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το URL δεν είναι σωστό')));
  echo json_encode($return); die();}
  
if ($company_id<=0) {debug_mail(false,'emptyl',                  gks_lang('Ορίστε την εταιρεία'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την εταιρεία')));
  echo json_encode($return); die();}

if ($eshop_key=='') {debug_mail(false,'eshop_key',               gks_lang('Ορίστε το κλειδί για το eshop'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το κλειδί για το eshop')));
  echo json_encode($return); die();}

if (mb_strlen($eshop_key) < 32) {debug_mail(false,'eshop_key',   gks_lang('Το κλειδί θα πρέπει να είναι τουλάχιστον 32 χαρακτήρες'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το κλειδί θα πρέπει να είναι τουλάχιστον 32 χαρακτήρες')));
  echo json_encode($return); die();}

if ($import_as=='transfer' and $woo_start_booking_number=='') {
  debug_mail(false,'woo_start_booking_number',                   gks_lang('Ορίστε το Πρόθεμα Κράτησης'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Πρόθεμα Κράτησης')));
  echo json_encode($return); die();}

if ($import_as!='acc_inv') {
  $acc_journal_id_tim=0;
  $acc_seira_id_tim=0;
  $warehouses_id_from_tim=0;
}

if ($company_id>0 and $company_sub_id>0) {
  $sql="select * from gks_company_subs where company_id=".$company_id." and id_company_sub=".$company_sub_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }    
  if ($result->num_rows==0) {
    debug_mail(false,'emptyl',                                     gks_lang('Αυτό το υποκατάστημα δεν ανήκει σε αυτήν την εταιρεία'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το υποκατάστημα δεν ανήκει σε αυτήν την εταιρεία')));
    echo json_encode($return); die();}
}

$sql="select * from gks_eshops 
where eshop_name like '".$db_link->escape_string($eshop_name)."' 
and id_eshop<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το όνομα <b>[1]</b> υπάρχει ήδη στο eshop:<br><a href="admin-eshop-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$eshop_name,$message);  
  $message=str_replace('[2]',$row['id_eshop'],$message);  
  debug_mail(false,'eshop exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();}
  
$sql="select * from gks_eshops 
where eshop_url like '".$db_link->escape_string($eshop_url)."' 
and id_eshop<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το URL <b>'.$eshop_url.'</b> υπάρχει ήδη στο eshop:<br><a href="admin-eshop-item.php?id='.$row['id_eshop'].'" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$eshop_url,$message);  
  $message=str_replace('[2]',$row['id_eshop'],$message);  
  debug_mail(false,'eshop exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();}

//$sql="select * from gks_eshops 
//where company_id=".$company_id."
//and company_sub_id=".$company_sub_id."
//and id_eshop<>".$id;
//$result = $db_link->query($sql);  
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  echo json_encode($return); die(); } 
//if ($result->num_rows>=1) {
//  $row = $result->fetch_assoc();
//  $message=gks_lang('Αυτή η εταιρεία/υποκατάστημα έχει ήδη eshop:<br><a href="admin-eshop-item.php?id=[1]" class="gks_link">Προβολή</a>';
//  $message=str_replace('[1]',$row['id_eshop'],$message);  
//  debug_mail(false,'eshop exist',$message);
//  $return = array('success' => false, 'message' => base64_encode($message));
//  echo json_encode($return); die();}

  
$sql="select * from gks_eshops 
where eshop_key like '".$db_link->escape_string($eshop_key)."' 
and id_eshop<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το κλειδί <b>[1]</b> υπάρχει ήδη στο eshop:<br><a href="admin-eshop-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$eshop_key,$message);  
  $message=str_replace('[2]',$row['id_eshop'],$message);   
  debug_mail(false,'eshop exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();}



$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_eshops');

$redirect='';
if ($id==-1) {
  $sql="insert into gks_eshops (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-eshop-item.php?id='.$id); 
}



$sql="update gks_eshops set 
eshop_name='".$db_link->escape_string($eshop_name)."',
eshop_url='".$db_link->escape_string($eshop_url)."',
company_id=".$company_id.",
company_sub_id=".$company_sub_id.",
eshop_key='".$db_link->escape_string($eshop_key)."',
eshop_autosync=".$eshop_autosync.",
eshop_sortorder=".$eshop_sortorder.",
eshop_disable=".$eshop_disable.",
tax_class_basikos='".$db_link->escape_string($tax_class_basikos)."',
tax_class_meiomenos='".$db_link->escape_string($tax_class_meiomenos)."',
tax_class_ypermeiomenos='".$db_link->escape_string($tax_class_ypermeiomenos)."',
tax_class_yperypermeiomenos='".$db_link->escape_string($tax_class_yperypermeiomenos)."',
tax_class_xorisfpa='".$db_link->escape_string($tax_class_xorisfpa)."',


order_find_user_from='".$db_link->escape_string($order_find_user_from)."',
order_meta_user_lang='".$db_link->escape_string($order_meta_user_lang)."',
order_meta_parastatiko='".$db_link->escape_string($order_meta_parastatiko)."',
order_meta_eponimia='".$db_link->escape_string($order_meta_eponimia)."',
order_meta_title='".$db_link->escape_string($order_meta_title)."',
order_meta_afm='".$db_link->escape_string($order_meta_afm)."',
order_meta_doy='".$db_link->escape_string($order_meta_doy)."',
order_meta_epaggelma='".$db_link->escape_string($order_meta_epaggelma)."',

woo_delivery_to_gks='".$db_link->escape_string(serialize($woo_delivery_to_gks))."',
woo_payment_to_gks='".$db_link->escape_string(serialize($woo_payment_to_gks))."',

import_yes=".$import_yes.",
import_as='".$db_link->escape_string($import_as)."',
acc_journal_id=".$acc_journal_id.",
acc_journal_id_tim=".$acc_journal_id_tim.",
acc_seira_id=".$acc_seira_id.",
acc_seira_id_tim=".$acc_seira_id_tim.",
warehouses_id_from=".$warehouses_id_from.",
warehouses_id_from_tim=".$warehouses_id_from_tim.",
will_update=".$will_update.",
update_if_gks_change=".$update_if_gks_change.",
update_state_gks_transfer='".$db_link->escape_string($update_state_gks_transfer)."',
update_state_gks_reservation='".$db_link->escape_string($update_state_gks_reservation)."',
update_state_gks_order='".$db_link->escape_string($update_state_gks_order)."',
update_state_gks_acc_inv='".$db_link->escape_string($update_state_gks_acc_inv)."',
update_state_woo='".$db_link->escape_string($update_state_woo)."',


acc_inv_product_shipping=".$acc_inv_product_shipping.",
acc_inv_product_fees=".$acc_inv_product_fees.",
woo_start_booking_number='".$db_link->escape_string($woo_start_booking_number)."',

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_eshop = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();








