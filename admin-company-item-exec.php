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
$my_page_title=gks_lang('Αποθήκευση εταιρείας').' id:' . $id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');

$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  
  $sql="select * from gks_company where id_company=".$id;
  if (count($perm_id_company_ids)>0) $sql.=" and gks_company.id_company in (".implode(',',$perm_id_company_ids).")";
  $sql.=" limit 1";
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
  $row = $result->fetch_assoc();
  $old_viva_merchant_id=trim_gks($row['viva_merchant_id']);
  $old_viva_api_key=trim_gks($row['viva_api_key']);
  
  $old_mellon_mid=trim_gks($row['mellon_mid']);
  $old_mellon_username=trim_gks($row['mellon_username']);
  $old_mellon_password=trim_gks($row['mellon_password']);
  $old_mellon_authorization_code=trim_gks($row['mellon_authorization_code']);
  
  $old_cardlink_mid=trim_gks($row['cardlink_mid']);
  
  $old_epay_mid=trim_gks($row['epay_mid']);
  $old_epay_username=trim_gks($row['epay_username']);
  $old_epay_password=trim_gks($row['epay_password']);
  $old_epay_authorization_code=trim_gks($row['epay_authorization_code']);
  
  $old_worldline_mid=trim_gks($row['worldline_mid']);
  $old_worldline_username=trim_gks($row['worldline_username']);
  $old_worldline_password=trim_gks($row['worldline_password']);
  $old_worldline_authorization_code=trim_gks($row['worldline_authorization_code']);
  
  $old_nexi_mid=trim_gks($row['nexi_mid']);
  $old_nexi_username=trim_gks($row['nexi_username']);
  $old_nexi_password=trim_gks($row['nexi_password']);
  $old_nexi_authorization_code=trim_gks($row['nexi_authorization_code']);
  
  
}


$company_title=''; if (isset($_POST['company_title'])) $company_title=trim_gks(base64_decode($_POST['company_title']));
$company_tagline=''; if (isset($_POST['company_tagline'])) $company_tagline=trim_gks(base64_decode($_POST['company_tagline']));
$company_eponimia=''; if (isset($_POST['company_eponimia'])) $company_eponimia=trim_gks(base64_decode($_POST['company_eponimia']));
$company_afm=''; if (isset($_POST['company_afm'])) $company_afm=trim_gks(base64_decode($_POST['company_afm']));
$company_doy=''; if (isset($_POST['company_doy'])) $company_doy=trim_gks(base64_decode($_POST['company_doy']));
$company_epaggelma=''; if (isset($_POST['company_epaggelma'])) $company_epaggelma=trim_gks(base64_decode($_POST['company_epaggelma']));
$company_gemi_number=''; if (isset($_POST['company_gemi_number'])) $company_gemi_number=trim_gks(base64_decode($_POST['company_gemi_number']));
$company_phone=''; if (isset($_POST['company_phone'])) $company_phone=trim_gks(base64_decode($_POST['company_phone']));
$company_email=''; if (isset($_POST['company_email'])) $company_email=trim_gks(base64_decode($_POST['company_email']));
$company_url=''; if (isset($_POST['company_url'])) $company_url=trim_gks(base64_decode($_POST['company_url']));
$company_odos=''; if (isset($_POST['company_odos'])) $company_odos=trim_gks(base64_decode($_POST['company_odos']));
$company_arithmos=''; if (isset($_POST['company_arithmos'])) $company_arithmos=trim_gks(base64_decode($_POST['company_arithmos']));
$company_orofos=''; if (isset($_POST['company_orofos'])) $company_orofos=trim_gks(base64_decode($_POST['company_orofos']));
$company_perioxi=''; if (isset($_POST['company_perioxi'])) $company_perioxi=trim_gks(base64_decode($_POST['company_perioxi']));
$company_poli=''; if (isset($_POST['company_poli'])) $company_poli=trim_gks(base64_decode($_POST['company_poli']));
$company_tk=''; if (isset($_POST['company_tk'])) $company_tk=trim_gks(base64_decode($_POST['company_tk']));
$company_country_id=0; if (isset($_POST['company_country_id'])) $company_country_id=intval($_POST['company_country_id']);
$company_nomos_id=0; if (isset($_POST['company_nomos_id'])) $company_nomos_id=intval($_POST['company_nomos_id']);
$company_map_latitude=0; if (isset($_POST['company_map_latitude'])) $company_map_latitude=floatval(str_replace(',','.', $_POST['company_map_latitude']));
$company_map_longitude=0; if (isset($_POST['company_map_longitude'])) $company_map_longitude=floatval(str_replace(',','.', $_POST['company_map_longitude']));
$company_disable=0; if (isset($_POST['company_disable'])) $company_disable=intval($_POST['company_disable']);
$company_related_user_id=0; if (isset($_POST['company_related_user_id'])) $company_related_user_id=intval($_POST['company_related_user_id']);
$company_color=''; if (isset($_POST['company_color'])) $company_color=trim_gks(base64_decode($_POST['company_color']));

$aade_send=0; if (isset($_POST['aade_send'])) $aade_send=intval($_POST['aade_send']);
$aade_branch=0; if (isset($_POST['aade_branch'])) $aade_branch=intval($_POST['aade_branch']);
$aade_mydata_user_id=''; if (isset($_POST['aade_mydata_user_id'])) $aade_mydata_user_id=trim_gks(base64_decode($_POST['aade_mydata_user_id']));
$aade_mydata_subscription_key=''; if (isset($_POST['aade_mydata_subscription_key'])) $aade_mydata_subscription_key=trim_gks(base64_decode($_POST['aade_mydata_subscription_key']));
$aade_mydata_live=0; if (isset($_POST['aade_mydata_live'])) $aade_mydata_live=intval($_POST['aade_mydata_live']);

$gsis_afm_check_username=''; if (isset($_POST['gsis_afm_check_username'])) $gsis_afm_check_username=trim_gks(base64_decode($_POST['gsis_afm_check_username']));
$gsis_afm_check_password=''; if (isset($_POST['gsis_afm_check_password'])) $gsis_afm_check_password=trim_gks(base64_decode($_POST['gsis_afm_check_password']));

$company_sortorder=0; if (isset($_POST['company_sortorder'])) $company_sortorder=intval($_POST['company_sortorder']);

$paroxos_send=0;if (isset($_POST['paroxos_send'])) $paroxos_send=intval($_POST['paroxos_send']);
if ($paroxos_send!=1) $paroxos_send=0;
$paroxos_mydata_live=0;if (isset($_POST['paroxos_mydata_live'])) $paroxos_mydata_live=intval($_POST['paroxos_mydata_live']);
if ($paroxos_mydata_live!=1) $paroxos_mydata_live=0;
$aade_paroxos_id=0;if (isset($_POST['aade_paroxos_id'])) $aade_paroxos_id=intval($_POST['aade_paroxos_id']);
$paroxos_branch=0;if (isset($_POST['paroxos_branch'])) $paroxos_branch=intval($_POST['paroxos_branch']);
$pc_username=''; if (isset($_POST['pc_username'])) $pc_username=trim_gks(base64_decode($_POST['pc_username']));
$pc_password=''; if (isset($_POST['pc_password'])) $pc_password=trim_gks(base64_decode($_POST['pc_password']));
$pc_key=''; if (isset($_POST['pc_key'])) $pc_key=trim_gks(base64_decode($_POST['pc_key']));


$viva_merchant_id=''; if (isset($_POST['viva_merchant_id'])) $viva_merchant_id=trim_gks(base64_decode($_POST['viva_merchant_id']));
$viva_api_key=''; if (isset($_POST['viva_api_key'])) $viva_api_key=trim_gks(base64_decode($_POST['viva_api_key']));
$viva_pos_client_id=''; if (isset($_POST['viva_pos_client_id'])) $viva_pos_client_id=trim_gks(base64_decode($_POST['viva_pos_client_id']));
$viva_pos_client_secret=''; if (isset($_POST['viva_pos_client_secret'])) $viva_pos_client_secret=trim_gks(base64_decode($_POST['viva_pos_client_secret']));
$payment_with_ppm_radio_tap_viva=0;if (isset($_POST['payment_with_ppm_radio_tap_viva'])) $payment_with_ppm_radio_tap_viva=intval($_POST['payment_with_ppm_radio_tap_viva']);
$payment_with_ppm_radio_iris_viva=0;if (isset($_POST['payment_with_ppm_radio_iris_viva'])) $payment_with_ppm_radio_iris_viva=intval($_POST['payment_with_ppm_radio_iris_viva']);
$viva_preferred_payment_methods=[];
if ($payment_with_ppm_radio_tap_viva!=0) $viva_preferred_payment_methods[]='tap';
if ($payment_with_ppm_radio_iris_viva!=0) $viva_preferred_payment_methods[]='iris';


$mellon_mid=''; if (isset($_POST['mellon_mid'])) $mellon_mid=trim_gks(base64_decode($_POST['mellon_mid']));
$mellon_username=''; if (isset($_POST['mellon_username'])) $mellon_username=trim_gks(base64_decode($_POST['mellon_username']));
$mellon_password=''; if (isset($_POST['mellon_password'])) $mellon_password=trim_gks(base64_decode($_POST['mellon_password']));
$mellon_authorization_code=''; if (isset($_POST['mellon_authorization_code'])) $mellon_authorization_code=trim_gks(base64_decode($_POST['mellon_authorization_code']));
$payment_with_ppm_radio_tap_mellon=0;if (isset($_POST['payment_with_ppm_radio_tap_mellon'])) $payment_with_ppm_radio_tap_mellon=intval($_POST['payment_with_ppm_radio_tap_mellon']);
$payment_with_ppm_radio_iris_mellon=0;if (isset($_POST['payment_with_ppm_radio_iris_mellon'])) $payment_with_ppm_radio_iris_mellon=intval($_POST['payment_with_ppm_radio_iris_mellon']);
$mellon_preferred_payment_methods=[];
if ($payment_with_ppm_radio_tap_mellon!=0) $mellon_preferred_payment_methods[]='tap';
if ($payment_with_ppm_radio_iris_mellon!=0) $mellon_preferred_payment_methods[]='iris';

$cardlink_mid=''; if (isset($_POST['cardlink_mid'])) $cardlink_mid=trim_gks(base64_decode($_POST['cardlink_mid']));
$payment_with_ppm_radio_tap_cardlink=0;if (isset($_POST['payment_with_ppm_radio_tap_cardlink'])) $payment_with_ppm_radio_tap_cardlink=intval($_POST['payment_with_ppm_radio_tap_cardlink']);
$payment_with_ppm_radio_iris_cardlink=0;if (isset($_POST['payment_with_ppm_radio_iris_cardlink'])) $payment_with_ppm_radio_iris_cardlink=intval($_POST['payment_with_ppm_radio_iris_cardlink']);
$cardlink_preferred_payment_methods=[];
if ($payment_with_ppm_radio_tap_cardlink!=0) $cardlink_preferred_payment_methods[]='tap';
if ($payment_with_ppm_radio_iris_cardlink!=0) $cardlink_preferred_payment_methods[]='iris';

$epay_mid=''; if (isset($_POST['epay_mid'])) $epay_mid=trim_gks(base64_decode($_POST['epay_mid']));
$epay_username=''; if (isset($_POST['epay_username'])) $epay_username=trim_gks(base64_decode($_POST['epay_username']));
$epay_password=''; if (isset($_POST['epay_password'])) $epay_password=trim_gks(base64_decode($_POST['epay_password']));
$epay_authorization_code=''; if (isset($_POST['epay_authorization_code'])) $epay_authorization_code=trim_gks(base64_decode($_POST['epay_authorization_code']));
$payment_with_ppm_radio_tap_epay=0;if (isset($_POST['payment_with_ppm_radio_tap_epay'])) $payment_with_ppm_radio_tap_epay=intval($_POST['payment_with_ppm_radio_tap_epay']);
$payment_with_ppm_radio_iris_epay=0;if (isset($_POST['payment_with_ppm_radio_iris_epay'])) $payment_with_ppm_radio_iris_epay=intval($_POST['payment_with_ppm_radio_iris_epay']);
$epay_preferred_payment_methods=[];
if ($payment_with_ppm_radio_tap_epay!=0) $epay_preferred_payment_methods[]='tap';
if ($payment_with_ppm_radio_iris_epay!=0) $epay_preferred_payment_methods[]='iris';

$worldline_mid=''; if (isset($_POST['worldline_mid'])) $worldline_mid=trim_gks(base64_decode($_POST['worldline_mid']));
$worldline_username=''; if (isset($_POST['worldline_username'])) $worldline_username=trim_gks(base64_decode($_POST['worldline_username']));
$worldline_password=''; if (isset($_POST['worldline_password'])) $worldline_password=trim_gks(base64_decode($_POST['worldline_password']));
$worldline_authorization_code=''; if (isset($_POST['worldline_authorization_code'])) $worldline_authorization_code=trim_gks(base64_decode($_POST['worldline_authorization_code']));

$nexi_mid=''; if (isset($_POST['nexi_mid'])) $nexi_mid=trim_gks(base64_decode($_POST['nexi_mid']));
$nexi_username=''; if (isset($_POST['nexi_username'])) $nexi_username=trim_gks(base64_decode($_POST['nexi_username']));
$nexi_password=''; if (isset($_POST['nexi_password'])) $nexi_password=trim_gks(base64_decode($_POST['nexi_password']));
$nexi_authorization_code=''; if (isset($_POST['nexi_authorization_code'])) $nexi_authorization_code=trim_gks(base64_decode($_POST['nexi_authorization_code']));
$payment_with_ppm_radio_tap_nexi=0;if (isset($_POST['payment_with_ppm_radio_tap_nexi'])) $payment_with_ppm_radio_tap_nexi=intval($_POST['payment_with_ppm_radio_tap_nexi']);
$payment_with_ppm_radio_iris_nexi=0;if (isset($_POST['payment_with_ppm_radio_iris_nexi'])) $payment_with_ppm_radio_iris_nexi=intval($_POST['payment_with_ppm_radio_iris_nexi']);
$nexi_preferred_payment_methods=[];
if ($payment_with_ppm_radio_tap_nexi!=0) $nexi_preferred_payment_methods[]='tap';
if ($payment_with_ppm_radio_iris_nexi!=0) $nexi_preferred_payment_methods[]='iris';


if ($company_title=='') {debug_mail(false,'emptyl',              gks_lang('O Διακριτικός Τίτλος ΔΕΝ μπορεί να είναι κενός'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('O Διακριτικός Τίτλος ΔΕΝ μπορεί να είναι κενός')));
  echo json_encode($return); die();}

if ($company_eponimia=='') {debug_mail(false,'emptyl',           gks_lang('Η Επωνυμία ΔΕΝ μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Επωνυμία ΔΕΝ μπορεί να είναι κενή')));
  echo json_encode($return); die();}

if ($company_afm != '' and (CheckAFM($company_afm) == false and $company_country_id==91)) {debug_mail(false,'emptyl',          'afm is not OK'.$company_afm);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To ΑΦΜ δεν είναι έγκυρο')));
  echo json_encode($return); die();}  

//if ($company_epaggelma == '') {debug_mail(false,'emptyl',        'company_epaggelma is not OK'.$company_epaggelma);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Δραστηριότητα ΔΕΝ μπορεί να είναι κενή')));
//  echo json_encode($return); die();}  


if ($company_email != '' and !filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,'email is not ok : '.$company_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  echo json_encode($return); die();}

//if ($company_phone != '' and (strlen($company_phone) != 10 or substr($company_phone,0,1) != '2') ) {
//  debug_mail(false,'company phone is not ok. : '.$company_phone);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό')));
//  echo json_encode($return); die();}  

  
if ($company_country_id==0) {debug_mail(false,'emptyl',          gks_lang('Επιλέξτε μία χώρα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε μία χώρα')));
  echo json_encode($return); die();}

//if ($company_nomos_id==0) {debug_mail(false,'emptyl',            gks_lang('Επιλέξτε έναν νομό'));
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε έναν νομό')));
//  echo json_encode($return); die();}

$sql="select * from gks_company where company_title like '".$db_link->escape_string($company_title)."' and id_company<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η εταιρεία με τίτλο <b>[1]</b> υπάρχει ήδη:<br><a href="admin-company-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$company_title,$message);
  $message=str_replace('[2]',$row['id_company'],$message);
  
  debug_mail(false,'warehouse exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

$sql="select * from gks_company where company_eponimia like '".$db_link->escape_string($company_eponimia)."' and id_company<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η εταιρεία με επωνυμία <b>[1]</b> υπάρχει ήδη:<br><a href="admin-company-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$company_eponimia,$message);
  $message=str_replace('[2]',$row['id_company'],$message);
  debug_mail(false,'warehouse exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

if ($company_afm!='') {
  $sql="select * from gks_company where company_afm like '".$db_link->escape_string($company_afm)."' and id_company<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Η εταιρεία με ΑΦΜ <b>[1]</b> υπάρχει ήδη:<br><a href="admin-company-item.php?id=[2]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',$company_afm,$message);
    $message=str_replace('[2]',$row['id_company'],$message);
    debug_mail(false,'company exist afm',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
}

if ($aade_branch<0) {debug_mail(false,'emptyl',                  gks_lang('Ο Αριθμός Εγκατάστασης για την Αποστολή δεδομένων στην ΑΑΔΕ με myDATA πρέπει να είναι μεγαλύτερος ή ίσο με μηδέν'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο Αριθμός Εγκατάστασης για την Αποστολή δεδομένων στην ΑΑΔΕ με myDATA πρέπει να είναι μεγαλύτερος ή ίσο με μηδέν')));
	echo json_encode($return); die();}
	


if ($viva_merchant_id!='') {
  
  $sql="select * from gks_company where viva_merchant_id like '".$db_link->escape_string($viva_merchant_id)."' and id_company<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Ο κωδικός [1] Merchant ID υπάρχει ήδη σε άλλη σε εταιρεία με τίτλο <b>[2]</b>:<br><a href="admin-company-item.php?id=[3]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',gks_lang('Viva'),$message);
    $message=str_replace('[2]',$company_title,$message);
    $message=str_replace('[3]',$row['id_company'],$message);
    debug_mail(false,'viva_merchant_id',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }  
}

if ($mellon_mid!='') {
  $sql="select * from gks_company where mellon_mid like '".$db_link->escape_string($mellon_mid)."' and id_company<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Ο κωδικός [1] Merchant ID υπάρχει ήδη σε άλλη σε εταιρεία με τίτλο <b>[2]</b>:<br><a href="admin-company-item.php?id=[3]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',gks_lang('Mellon Technologies'),$message);
    $message=str_replace('[2]',$company_title,$message);
    $message=str_replace('[3]',$row['id_company'],$message);
    debug_mail(false,'Mellon merchant_id',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }  
}

if ($cardlink_mid!='') {
  $sql="select * from gks_company where cardlink_mid like '".$db_link->escape_string($cardlink_mid)."' and id_company<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Ο κωδικός [1] Merchant ID υπάρχει ήδη σε άλλη σε εταιρεία με τίτλο <b>[2]</b>:<br><a href="admin-company-item.php?id=[3]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',gks_lang('Cardlink'),$message);
    $message=str_replace('[2]',$company_title,$message);
    $message=str_replace('[3]',$row['id_company'],$message);
    debug_mail(false,'Cardlink merchant_id',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }  
}

if ($epay_mid!='') {
  $sql="select * from gks_company where epay_mid like '".$db_link->escape_string($epay_mid)."' and id_company<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Ο κωδικός [1] Merchant ID υπάρχει ήδη σε άλλη σε εταιρεία με τίτλο <b>[2]</b>:<br><a href="admin-company-item.php?id=[3]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',gks_lang('ePay'),$message);
    $message=str_replace('[2]',$company_title,$message);
    $message=str_replace('[3]',$row['id_company'],$message);
    debug_mail(false,'ePay merchant_id',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }  
}

if ($worldline_mid!='') {
  $sql="select * from gks_company where worldline_mid like '".$db_link->escape_string($worldline_mid)."' and id_company<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Ο κωδικός [1] Merchant ID υπάρχει ήδη σε άλλη σε εταιρεία με τίτλο <b>[2]</b>:<br><a href="admin-company-item.php?id=[3]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',gks_lang('Worldline'),$message);
    $message=str_replace('[2]',$company_title,$message);
    $message=str_replace('[3]',$row['id_company'],$message);
    debug_mail(false,'worldline merchant_id',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }  
}

if ($nexi_mid!='') {
  $sql="select * from gks_company where nexi_mid like '".$db_link->escape_string($nexi_mid)."' and id_company<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Ο κωδικός [1] Merchant ID υπάρχει ήδη σε άλλη σε εταιρεία με τίτλο <b>[2]</b>:<br><a href="admin-company-item.php?id=[3]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',gks_lang('NEXI'),$message);
    $message=str_replace('[2]',$company_title,$message);
    $message=str_replace('[3]',$row['id_company'],$message);
    debug_mail(false,'NEXI merchant_id',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }  
}

if ($paroxos_send==1) {
  if ($aade_paroxos_id<=0) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε έναν πάροχο')));
    echo json_encode($return); die();}
  $sql="select * from gks_aade_paroxos where id_aade_paroxos=".$aade_paroxos_id." and paroxos_implemented=1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows==0) {
    $message=gks_lang('Δεν βρέθηκε ο πάροχος με id').' '.$aade_paroxos_id;
    debug_mail(false,'paroxos not found',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
  $row = $result->fetch_assoc();
  $paroxos_need_username=intval($row['paroxos_need_username'])==1;
  $paroxos_need_password=intval($row['paroxos_need_password'])==1;
  $paroxos_need_key=intval($row['paroxos_need_key'])==1;   

	if ($paroxos_need_username==false) $pc_username='';
	if ($paroxos_need_password==false) $pc_password='';
	if ($paroxos_need_key==false)      $pc_key='';
		
	if ($paroxos_branch<0) {debug_mail(false,'emptyl',               gks_lang('Ο Αριθμός Εγκατάστασης για την Αποστολή δεδομένων με πάροχο πρέπει να είναι μεγαλύτερος ή ίσο με μηδέν'));
	  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο Αριθμός Εγκατάστασης για την Αποστολή δεδομένων με πάροχο πρέπει να είναι μεγαλύτερος ή ίσο με μηδέν')));
		echo json_encode($return); die();}
	
  if ($paroxos_need_username and $pc_username=='') {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Όνομα Χρήστη στον πάροχο')));
    echo json_encode($return); die();}
  if ($paroxos_need_password and $pc_password=='') {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Κωδικό Πρόσβασης στον πάροχο')));
    echo json_encode($return); die();}
  if ($paroxos_need_key and $pc_key=='') {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Κλειδί API στον πάροχο')));
    echo json_encode($return); die();}
}

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_company');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_company (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-company-item.php?id='.$id); 
}



$sql="update gks_company set 
company_title='".$db_link->escape_string($company_title)."',
company_tagline='".$db_link->escape_string($company_tagline)."',
company_eponimia='".$db_link->escape_string($company_eponimia)."',
company_afm='".$db_link->escape_string($company_afm)."',
company_doy='".$db_link->escape_string($company_doy)."',
company_epaggelma='".$db_link->escape_string($company_epaggelma)."',
company_gemi_number='".$db_link->escape_string($company_gemi_number)."',
company_phone=". ($company_phone =='' ? 'null' : "'".$db_link->escape_string($company_phone)."'").",
company_email=". ($company_email =='' ? 'null' : "'".$db_link->escape_string($company_email)."'").",
company_url=". ($company_url =='' ? 'null' : "'".$db_link->escape_string($company_url)."'").",
company_odos=". ($company_odos =='' ? 'null' : "'".$db_link->escape_string($company_odos)."'").",
company_arithmos=". ($company_arithmos =='' ? 'null' : "'".$db_link->escape_string($company_arithmos)."'").",
company_orofos=". ($company_orofos =='' ? 'null' : "'".$db_link->escape_string($company_orofos)."'").",
company_perioxi=". ($company_perioxi =='' ? 'null' : "'".$db_link->escape_string($company_perioxi)."'").",
company_poli=". ($company_poli =='' ? 'null' : "'".$db_link->escape_string($company_poli)."'").",
company_tk=". ($company_tk =='' ? 'null' : "'".$db_link->escape_string($company_tk)."'").",
company_country_id=".$company_country_id.",
company_nomos_id=".$company_nomos_id.",

company_map_latitude='".number_format($company_map_latitude,16,'.','')."',
company_map_longitude='".number_format($company_map_longitude,16,'.','')."',

company_disable=".$company_disable.",
company_related_user_id=".$company_related_user_id.",
company_color=". ($company_color =='' ? 'null' : "'".$db_link->escape_string($company_color)."'").",


aade_send=".$aade_send.",
aade_branch=".$aade_branch.",
aade_mydata_user_id='".$db_link->escape_string($aade_mydata_user_id)."',
aade_mydata_subscription_key='".$db_link->escape_string($aade_mydata_subscription_key)."',
aade_mydata_live=".$aade_mydata_live.",

gsis_afm_check_username='".$db_link->escape_string($gsis_afm_check_username)."',
gsis_afm_check_password='".$db_link->escape_string($gsis_afm_check_password)."',

company_sortorder=".$company_sortorder.",

viva_merchant_id='".$db_link->escape_string($viva_merchant_id)."',
viva_api_key='".$db_link->escape_string($viva_api_key)."',";
if ($is_new_rec==false) {
  if ($old_viva_merchant_id!=$viva_merchant_id or $old_viva_api_key!=$viva_api_key) {
    $sql.="viva_verify_webhook_page_key=null,";
  }
}
$sql.="
viva_pos_client_id='".$db_link->escape_string($viva_pos_client_id)."',
viva_pos_client_secret='".$db_link->escape_string($viva_pos_client_secret)."',
viva_preferred_payment_methods='".$db_link->escape_string(json_encode($viva_preferred_payment_methods))."',

mellon_mid='".$db_link->escape_string($mellon_mid)."',
mellon_username='".$db_link->escape_string($mellon_username)."',
mellon_password='".$db_link->escape_string($mellon_password)."',
mellon_authorization_code='".$db_link->escape_string($mellon_authorization_code)."',
mellon_preferred_payment_methods='".$db_link->escape_string(json_encode($mellon_preferred_payment_methods))."',

cardlink_mid='".$db_link->escape_string($cardlink_mid)."',
cardlink_preferred_payment_methods='".$db_link->escape_string(json_encode($cardlink_preferred_payment_methods))."',

epay_mid='".$db_link->escape_string($epay_mid)."',
epay_username='".$db_link->escape_string($epay_username)."',
epay_password='".$db_link->escape_string($epay_password)."',
epay_authorization_code='".$db_link->escape_string($epay_authorization_code)."',
epay_preferred_payment_methods='".$db_link->escape_string(json_encode($epay_preferred_payment_methods))."',

worldline_mid='".$db_link->escape_string($worldline_mid)."',
worldline_username='".$db_link->escape_string($worldline_username)."',
worldline_password='".$db_link->escape_string($worldline_password)."',
worldline_authorization_code='".$db_link->escape_string($worldline_authorization_code)."',

nexi_mid='".$db_link->escape_string($nexi_mid)."',
nexi_username='".$db_link->escape_string($nexi_username)."',
nexi_password='".$db_link->escape_string($nexi_password)."',
nexi_authorization_code='".$db_link->escape_string($nexi_authorization_code)."',
nexi_preferred_payment_methods='".$db_link->escape_string(json_encode($nexi_preferred_payment_methods))."',

";

if ($is_new_rec==false) {
  if ($old_mellon_username!=$mellon_username or $old_mellon_password!=$mellon_password or $old_mellon_authorization_code!=$mellon_authorization_code) {
    $sql.="mellon_x_api_key=null,";
  }
  if ($old_epay_username!=$epay_username or $old_epay_password!=$epay_password or $old_epay_authorization_code!=$epay_authorization_code) {
    $sql.="epay_x_api_key=null,";
  }
  if ($old_worldline_username!=$worldline_username or $old_worldline_password!=$worldline_password or $old_worldline_authorization_code!=$worldline_authorization_code) {
    $sql.="worldline_x_api_key=null,";
  }
  if ($old_nexi_username!=$nexi_username or $old_nexi_password!=$nexi_password or $old_nexi_authorization_code!=$nexi_authorization_code) {
    $sql.="nexi_x_api_key=null,";
  }
    
}

$sql.="
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_company = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }


$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

gks_lang_data_obj_save_exec_php('gks_company',$id);

$ret_run=gks_sociallinks_item_save($_POST,'gks_company',$id);
if ($ret_run['success']==false) {die(json_encode(array('success'=>false,'message'=>base64_encode($ret_run['message']))));}

  
gks_warehouse_address_update(array('id_company' => $id));


$sql_paroxos="select * from gks_company_paroxos where company_id=".$id;
$result_paroxos = $db_link->query($sql_paroxos); 
if (!$result_paroxos) {
  debug_mail(false,'error sql',$sql_paroxos);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

if ($result_paroxos->num_rows==0) {
  $sql_paroxos="insert into gks_company_paroxos (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  company_id,aade_paroxos_id,paroxos_send,paroxos_mydata_live,
  paroxos_branch,
  pc_username,
  pc_password,
  pc_key,
  
  pc_token_id,pc_token_expiration,
  pc_refresh_token_id,pc_refresh_token_expiration,
  pc_item_identifier,pc_item_family_identifier,pc_app_identifier,
  pc_url1,pc_url2,
  
  sandbox_pc_token_id,sandbox_pc_token_expiration,
  sandbox_pc_refresh_token_id,sandbox_pc_refresh_token_expiration,
  sandbox_pc_item_identifier,sandbox_pc_item_family_identifier,sandbox_pc_app_identifier,
  sandbox_pc_url1,sandbox_pc_url2
    
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$id.",".$aade_paroxos_id.",".$paroxos_send.",".$paroxos_mydata_live.",
	".$paroxos_branch.",
  '".$db_link->escape_string($pc_username)."',
  '".$db_link->escape_string($pc_password)."',
  '".$db_link->escape_string($pc_key)."',
  
  '',null,
  '',null,
  '','','',
  '','',
  
  '',null,
  '',null,
  '','','',
  '',''
  )";
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {
    debug_mail(false,'error sql',$sql_paroxos);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
    
} else {
  $row_paroxos = $result_paroxos->fetch_assoc();
  $id_company_paroxos=intval($row_paroxos['id_company_paroxos']);
  $sql_paroxos="update gks_company_paroxos set 
  aade_paroxos_id=".$aade_paroxos_id.",
  paroxos_send=".$paroxos_send.",
  paroxos_mydata_live=".$paroxos_mydata_live.",
  paroxos_branch=".$paroxos_branch.",
  pc_username='".$db_link->escape_string($pc_username)."',
  pc_password='".$db_link->escape_string($pc_password)."',
  pc_key='".$db_link->escape_string($pc_key)."',
  pc_token_id='',
  pc_token_expiration=null,
  pc_refresh_token_id='',
  pc_refresh_token_expiration=null,
  pc_item_identifier='',
  pc_item_family_identifier='',
  pc_app_identifier='',
  pc_url1='',
  pc_url2='',

  sandbox_pc_token_id='',
  sandbox_pc_token_expiration=null,
  sandbox_pc_refresh_token_id='',
  sandbox_pc_refresh_token_expiration=null,
  sandbox_pc_item_identifier='',
  sandbox_pc_item_family_identifier='',
  sandbox_pc_app_identifier='',
  sandbox_pc_url1='',
  sandbox_pc_url2='',
  
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  where id_company_paroxos=".$id_company_paroxos." and company_id=".$id;
  $result_paroxos = $db_link->query($sql_paroxos); 
  if (!$result_paroxos) {
    debug_mail(false,'error sql',$sql_paroxos);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
}


$fpa_base_str='';if (isset($_POST['fpa_base_str'])) $fpa_base_str = trim_gks(base64_decode($_POST['fpa_base_str']));
$fpa_base_array = json_decode($fpa_base_str, true);
if ($fpa_base_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error sociallinks_array','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (1)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die(); }

$sql_fpa="INSERT INTO gks_company_basefpa 
(company_id, fpa_base_id, fpa_id)
SELECT ".$id." AS aaaa, gks_eshop_fpa_base.id_fpa_base, 0 AS bbbb
FROM gks_eshop_fpa_base 
LEFT JOIN (
  SELECT fpa_base_id FROM gks_company_basefpa WHERE company_id=".$id."
) AS iparxoun ON gks_eshop_fpa_base.id_fpa_base=iparxoun.fpa_base_id
WHERE iparxoun.fpa_base_id Is Null";
$result_fpa = $db_link->query($sql_fpa); 
if (!$result_fpa) {
  debug_mail(false,'error sql',$sql_fpa);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$sql_fpa="update gks_company_basefpa set fpa_id=0 where company_id=".$id;
$result_fpa = $db_link->query($sql_fpa); 
if (!$result_fpa) {
  debug_mail(false,'error sql',$sql_fpa);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$fpa_base_hasvalue=[];
foreach ($fpa_base_array as $myval) {
  $fpa_base_hasvalue[]=intval($myval['base_id']);
  $sql_fpa="update gks_company_basefpa set fpa_id=".intval($myval['base_val'])." where company_id=".$id." and fpa_base_id=".intval($myval['base_id']);
  $result_fpa = $db_link->query($sql_fpa); 
  if (!$result_fpa) {
    debug_mail(false,'error sql',$sql_fpa);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
} 
//print '<pre>';print_r($fpa_base_array);die();
//print '<pre>';print_r($fpa_base_hasvalue);die();

$fpa_fiscals_str='';if (isset($_POST['fpa_fiscals_str'])) $fpa_fiscals_str = trim_gks(base64_decode($_POST['fpa_fiscals_str']));
$fpa_fiscals_array = json_decode($fpa_fiscals_str, true);
if ($fpa_fiscals_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error sociallinks_array','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (1)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die(); }


$sql_fpa="INSERT INTO gks_company_fpa 
(company_id,fiscal_position_id, fpa_base_id, fpa_id)
SELECT ".$id." as aaaa, sindiasmoi.id_fiscal_position, sindiasmoi.id_fpa_base, 0 AS bbbb
FROM (
  SELECT gks_eshop_fiscal_position.id_fiscal_position, gks_eshop_fpa_base.id_fpa_base
  FROM gks_eshop_fpa_base, gks_eshop_fiscal_position
  WHERE gks_eshop_fpa_base.fpa_base_disable=0 
  AND gks_eshop_fiscal_position.fiscal_position_disable=0
  ORDER BY gks_eshop_fiscal_position.id_fiscal_position, gks_eshop_fpa_base.id_fpa_base
) AS sindiasmoi 
LEFT JOIN (
  SELECT gks_company_fpa.fiscal_position_id, gks_company_fpa.fpa_base_id,fpa_id
  FROM gks_company_fpa
  WHERE gks_company_fpa.company_id=".$id."
) AS iparxoun ON (sindiasmoi.id_fiscal_position = iparxoun.fiscal_position_id) AND (sindiasmoi.id_fpa_base = iparxoun.fpa_base_id)
WHERE iparxoun.fpa_id Is Null";
$result_fpa = $db_link->query($sql_fpa); 
if (!$result_fpa) {
  debug_mail(false,'error sql',$sql_fpa);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$sql_fpa="update gks_company_fpa set fpa_id=0 where company_id=".$id;
$result_fpa = $db_link->query($sql_fpa); 
if (!$result_fpa) {
  debug_mail(false,'error sql',$sql_fpa);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

foreach ($fpa_fiscals_array as $myval) {
  $myval['base_id']=intval($myval['base_id']);
  if (in_array($myval['base_id'],$fpa_base_hasvalue)==false) $myval['base_val']=0;  
  $sql_fpa="update gks_company_fpa set fpa_id=".intval($myval['base_val'])." where company_id=".$id." and fiscal_position_id=".intval($myval['fiscal_id'])." and fpa_base_id=".$myval['base_id'];
  $result_fpa = $db_link->query($sql_fpa); 
  if (!$result_fpa) {
    debug_mail(false,'error sql',$sql_fpa);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
}

$afms=gks_paroxos_overview_get_afms(8); //ilyda
if (count($afms)>0) {
  $db_link->query("update gks_crons set disable_cron=0 where id_cron=5");
} else {
  $db_link->query("update gks_crons set disable_cron=1 where id_cron=5");
}


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







