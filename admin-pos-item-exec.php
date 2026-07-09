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
$my_page_title=gks_lang('Αποθήκευση Εντατική Λιανική').' id: '.$id;
db_open();
stat_record();


$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_pos',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_pos_ids=gks_permission_user_condition($my_wp_user_id,'gks_pos','01');

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');





$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  $sql="SELECT * FROM gks_pos 
  where id_pos = ".$id;
    
  if (count($perm_id_pos_ids)>0) $sql.=" and gks_pos.id_pos in (".implode(',',$perm_id_pos_ids).")";

  if (count($perm_id_company_ids)>0) $sql.=" and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  
    
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
  
}


$_gks_session['temp_mypropertiesheight'] = 0;
if (isset($_POST['mypropertiesheight'])) $_gks_session['temp_mypropertiesheight']=intval($_POST['mypropertiesheight']); gks_erp_cookie_save();






$pos_name=''; if (isset($_POST['pos_name'])) $pos_name=trim_gks(base64_decode($_POST['pos_name']));
if ($pos_name=='') {
  debug_mail(false,'set name','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το όνομα')));
  echo json_encode($return); die();}

$pos_descr=''; if (isset($_POST['pos_descr'])) $pos_descr=trim_gks(base64_decode($_POST['pos_descr']));
$pos_user_can_change_prices=0;if (isset($_POST['pos_user_can_change_prices'])) $pos_user_can_change_prices=intval($_POST['pos_user_can_change_prices']); if ($pos_user_can_change_prices!=1) $pos_user_can_change_prices=0;
$pos_max_ammount=0;if (isset($_POST['pos_max_ammount'])) $pos_max_ammount=floatval($_POST['pos_max_ammount']); if ($pos_max_ammount<0) $pos_max_ammount=0;
$pos_aade_mydata_live=0; if (isset($_POST['pos_aade_mydata_live'])) $pos_aade_mydata_live=intval($_POST['pos_aade_mydata_live']); if ($pos_aade_mydata_live!=1) $pos_aade_mydata_live=0;
$pos_multi_copies=0; if (isset($_POST['pos_multi_copies'])) $pos_multi_copies=intval($_POST['pos_multi_copies']); if ($pos_multi_copies<=0) $pos_multi_copies=0;
$pos_installments=0; if (isset($_POST['pos_installments'])) $pos_installments=intval($_POST['pos_installments']); if ($pos_installments<=0) $pos_installments=0;
$pos_tip=0; if (isset($_POST['pos_tip'])) $pos_tip=intval($_POST['pos_tip']); if ($pos_tip!=1) $pos_tip=0;
$pos_can_search_products=0; if (isset($_POST['pos_can_search_products'])) $pos_can_search_products=intval($_POST['pos_can_search_products']); if ($pos_can_search_products!=1) $pos_can_search_products=0;
$pos_indexeddb=0; if (isset($_POST['pos_indexeddb'])) $pos_indexeddb=intval($_POST['pos_indexeddb']); if ($pos_indexeddb!=1) $pos_indexeddb=0;
$pos_auto_click_start_at_paywith=0; if (isset($_POST['pos_auto_click_start_at_paywith'])) $pos_auto_click_start_at_paywith=intval($_POST['pos_auto_click_start_at_paywith']); if ($pos_auto_click_start_at_paywith!=1) $pos_auto_click_start_at_paywith=0;
$pos_disable=0; if (isset($_POST['pos_disable'])) $pos_disable=intval($_POST['pos_disable']); if ($pos_disable!=1) $pos_disable=0;
$app_mobile_userlogin_id=0; if (isset($_POST['app_mobile_userlogin_id'])) $app_mobile_userlogin_id=intval($_POST['app_mobile_userlogin_id']); 
$pos_sms_erp_app_mobile_id_code=''; if (isset($_POST['pos_sms_erp_app_mobile_id_code'])) $pos_sms_erp_app_mobile_id_code=trim_gks(base64_decode($_POST['pos_sms_erp_app_mobile_id_code']));
$pos_sms_template_text=''; if (isset($_POST['pos_sms_template_text'])) $pos_sms_template_text=trim_gks(base64_decode($_POST['pos_sms_template_text']));


$pos_print_enable=0; if (isset($_POST['pos_print_enable'])) $pos_print_enable=intval($_POST['pos_print_enable']);  
if ($pos_print_enable!=1) $pos_print_enable=0;
$pos_paroxos_send_pdf=0; if (isset($_POST['pos_paroxos_send_pdf'])) $pos_paroxos_send_pdf=intval($_POST['pos_paroxos_send_pdf']);  
if ($pos_paroxos_send_pdf!=1) $pos_paroxos_send_pdf=0;

$file_type=''; if (isset($_POST['file_type'])) $file_type=trim_gks(base64_decode($_POST['file_type']));
$is_landscape=0; if (isset($_POST['is_landscape'])) $is_landscape=intval($_POST['is_landscape']); if ($is_landscape!=1) $is_landscape=0;
$grayscale=0; if (isset($_POST['grayscale'])) $grayscale=intval($_POST['grayscale']); if ($grayscale!=1) $grayscale=0;
$zoom=1; if (isset($_POST['zoom'])) $zoom=floatval($_POST['zoom'])/100; //if ($zoom<0.2 or $zoom>2) $zoom=1;

if ($file_type=='') {debug_mail(false,'set form type');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον τύπο της φόρμας εκτύπωσης')));
  echo json_encode($return); die();}
$pos_print_form_id=0; if (isset($_POST['pos_print_form_id'])) $pos_print_form_id=intval($_POST['pos_print_form_id']);
$pos_thermal_form_id=0; if (isset($_POST['pos_thermal_form_id'])) $pos_thermal_form_id=intval($_POST['pos_thermal_form_id']);
$pos_print_x_form_id=0; if (isset($_POST['pos_print_x_form_id'])) $pos_print_x_form_id=intval($_POST['pos_print_x_form_id']);

$erp_app_id_check=0; if (isset($_POST['erp_app_id_check'])) $erp_app_id_check=intval($_POST['erp_app_id_check']);
$erp_app_filter_val_webpage_computer=0; if (isset($_POST['erp_app_filter_val_webpage_computer'])) $erp_app_filter_val_webpage_computer=intval($_POST['erp_app_filter_val_webpage_computer']);
$erp_app_filter_val_webpage_tablet=0; if (isset($_POST['erp_app_filter_val_webpage_tablet'])) $erp_app_filter_val_webpage_tablet=intval($_POST['erp_app_filter_val_webpage_tablet']);
$erp_app_filter_val_webpage_mobile=0; if (isset($_POST['erp_app_filter_val_webpage_mobile'])) $erp_app_filter_val_webpage_mobile=intval($_POST['erp_app_filter_val_webpage_mobile']);
$erp_app_filter_val_app_with_thermal=0; if (isset($_POST['erp_app_filter_val_app_with_thermal'])) $erp_app_filter_val_app_with_thermal=intval($_POST['erp_app_filter_val_app_with_thermal']);
$erp_app_filter_val_app_no_thermal=0; if (isset($_POST['erp_app_filter_val_app_no_thermal'])) $erp_app_filter_val_app_no_thermal=intval($_POST['erp_app_filter_val_app_no_thermal']);
$erp_app_id=0; if (isset($_POST['erp_app_id'])) $erp_app_id=intval($_POST['erp_app_id']);
$erp_app_dest=''; if (isset($_POST['erp_app_dest'])) $erp_app_dest=trim_gks(base64_decode($_POST['erp_app_dest']));
$erp_app_dest_printer=''; if (isset($_POST['erp_app_dest_printer'])) $erp_app_dest_printer=trim_gks(base64_decode($_POST['erp_app_dest_printer']));
$erp_app_dest_printer_method=0; if (isset($_POST['erp_app_dest_printer_method'])) $erp_app_dest_printer_method=intval($_POST['erp_app_dest_printer_method']);
$erp_app_dest_printer_lpr_ip=''; if (isset($_POST['erp_app_dest_printer_lpr_ip'])) $erp_app_dest_printer_lpr_ip=trim_gks(base64_decode($_POST['erp_app_dest_printer_lpr_ip']));
$erp_app_dest_printer_copies=0; if (isset($_POST['erp_app_dest_printer_copies'])) $erp_app_dest_printer_copies=intval($_POST['erp_app_dest_printer_copies']);
$erp_app_dest_folder=''; if (isset($_POST['erp_app_dest_folder'])) $erp_app_dest_folder=trim_gks(base64_decode($_POST['erp_app_dest_folder']));


if ($erp_app_id_check!=0) $erp_app_id_check=1;
if ($erp_app_id_check==0) {
  $erp_app_id=0;  
  $erp_app_dest='';
  $erp_app_dest_printer='';
  $erp_app_dest_printer_method=0;
  $erp_app_dest_printer_lpr_ip='';
  $erp_app_dest_printer_copies=0;
  $erp_app_dest_folder='';
  $erp_app_filter='';
} else {
  if ($erp_app_id<1) {
    debug_mail(false,'erp_app_id is empty','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την εφαρμογή gks ERP App Desktop')));
    echo json_encode($return); die(); } 
  
  $erp_app_filter=[];
  if ($erp_app_filter_val_webpage_computer) $erp_app_filter[]='webpage_computer';
  if ($erp_app_filter_val_webpage_tablet) $erp_app_filter[]='webpage_tablet';
  if ($erp_app_filter_val_webpage_mobile) $erp_app_filter[]='webpage_mobile';
  if ($erp_app_filter_val_app_with_thermal) $erp_app_filter[]='app_with_thermal';
  if ($erp_app_filter_val_app_no_thermal) $erp_app_filter[]='app_no_thermal';
  if (count($erp_app_filter)==0) {
    debug_mail(false,'erp_app_filter is empty','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τουλάχιστον ένα φίλτρο στο gks ERP App Desktop')));
    echo json_encode($return); die(); }    
  
  $erp_app_filter=json_encode($erp_app_filter);
  
  
  $sql="select * from gks_erp_app where id_erp_app=".$erp_app_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows<=0) {
    debug_mail(false,'erp_app_id not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η gks ERP App Desktop')));
    echo json_encode($return); die(); } 

  if ($erp_app_dest!='printer' and $erp_app_dest!='folder') $erp_app_dest='';
  if ($erp_app_dest=='') {
    debug_mail(false,'erp_app_dest is empty','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον Προορισμός')));
    echo json_encode($return); die(); }
  
  if ($erp_app_dest=='printer') {
    $erp_app_dest_folder='';
    
    if ($erp_app_dest_printer_method==0 or $erp_app_dest_printer_method==1) $erp_app_dest_printer_lpr_ip='';
    if ($erp_app_dest_printer_method==2) $erp_app_dest_printer='';
    if ($erp_app_dest_printer_method==3) {$erp_app_dest_printer_lpr_ip=''; $erp_app_dest_printer=''; }
    

    if ($erp_app_dest_printer_method < 0 or $erp_app_dest_printer_method > 3) {
      debug_mail(false,'erp_app_dest_printer_method is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η μέθοδος πρέπει να είναι 0,1,2 ή 3')));
      echo json_encode($return); die(); } 
    
    if ($erp_app_dest_printer=='' and ($erp_app_dest_printer_method==0 or $erp_app_dest_printer_method==1)) {
      debug_mail(false,'erp_app_dest_printer is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον Εκτυπωτή')));
      echo json_encode($return); die(); } 
    if ($erp_app_dest_printer_lpr_ip=='' and $erp_app_dest_printer_method==2) {
      debug_mail(false,'erp_app_dest_printer_lpr_ip is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε την IP του εκτυπωτή')));
      echo json_encode($return); die(); } 
      
      
    if ($erp_app_dest_printer_copies < 1 and $erp_app_dest_printer_copies > 5) {
      debug_mail(false,'erp_app_dest_printer_copies is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Τα αντίτυπα πρέπει να είναι 1,2,3,4 ή 5')));
      echo json_encode($return); die(); } 
    
    //echo '<pre>'. $erp_app_dest_printer;die();    
    
  } else if ($erp_app_dest=='folder') {
    $erp_app_dest_printer='';
    $erp_app_dest_printer_method=0;
    $erp_app_dest_printer_lpr_ip='';
    $erp_app_dest_printer_copies=0;
    
    if ($erp_app_dest_folder=='') {
      debug_mail(false,'erp_app_dest_folder is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον φάκελο αποστολής')));
      echo json_encode($return); die(); } 
    
    if (endwith($erp_app_dest_folder,'\\')==false) $erp_app_dest_folder.='\\';
    
    $params=array(
      'id' => $erp_app_id,
      'cmd' => 'run_command_folder_exist',
      'postdata' => array (
        'folder' => $erp_app_dest_folder,
        'and_writable' => true,
      ),
    );
    $gks_erp_run_result=gks_erp_app_run_command($params);

    if ($gks_erp_run_result['success']==false) {
      $return = array('success' => false, 'message' => base64_encode($gks_erp_run_result['message']));
      echo json_encode($return); die(); }
    
    

            
    //print '<pre>wwwwwwwwwwwww';print_r($gks_erp_run_result);die();
    
  }
  
}

$user_companys=gks_get_companys_list();
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
if ($company_id<=0) {
  debug_mail(false,'company_id is not found',$company_id.' '.$company_sub_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την εταιρεία/υποκατάστημα')));
  echo json_encode($return); die();}  

$pos_journal_id=0;if (isset($_POST['pos_journal_id'])) $pos_journal_id=intval($_POST['pos_journal_id']);
if ($pos_journal_id<=0) {
  debug_mail(false,'select journal');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε Ημερολόγιο')));
  echo json_encode($return); die();}
$pos_seira_id=0;if (isset($_POST['pos_seira_id'])) $pos_seira_id=intval($_POST['pos_seira_id']);
if ($pos_seira_id<=0) {
  debug_mail(false,'select seira');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε Σειρά')));
  echo json_encode($return); die();}
$def_aade_skopos_diakinisis_id=0;if (isset($_POST['def_aade_skopos_diakinisis_id'])) $def_aade_skopos_diakinisis_id=intval($_POST['def_aade_skopos_diakinisis_id']);
if ($def_aade_skopos_diakinisis_id<=0) {
  debug_mail(false,'select def_aade_skopos_diakinisis_id');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε Σκοπός Διακίνησης')));
  echo json_encode($return); die();}
$def_fiscal_position_id=0;if (isset($_POST['def_fiscal_position_id'])) $def_fiscal_position_id=intval($_POST['def_fiscal_position_id']);
if ($def_fiscal_position_id<=0) {
  debug_mail(false,'select def_fiscal_position_id');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε Φορολογική Θέση')));
  echo json_encode($return); die();}
$def_pricelist_id=0;if (isset($_POST['def_pricelist_id'])) $def_pricelist_id=intval($_POST['def_pricelist_id']);
if ($def_pricelist_id<=0) {
  debug_mail(false,'select def_pricelist_id');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τιμοκατάλογο')));
  echo json_encode($return); die();}
$def_assigned_id=0;if (isset($_POST['def_assigned_id'])) $def_assigned_id=intval($_POST['def_assigned_id']);
$user_id=0;if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);

//if ($user_id<=0) {
//  debug_mail(false,'select user_id');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε πελάτη')));
//  echo json_encode($return); die();}

$def_user_lang=''; if (isset($_POST['def_user_lang'])) $def_user_lang=trim_gks(base64_decode($_POST['def_user_lang']));
   

$tropos_apostolis=0; if (isset($_POST['tropos_apostolis'])) $tropos_apostolis=intval($_POST['tropos_apostolis']);  
$tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);  
$delivery_id_8=0; if (isset($_POST['delivery_id_8'])) $delivery_id_8=intval($_POST['delivery_id_8']);  

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
}
if ($tropos_apostolis!=8) $delivery_id_8=0;


$def_tropos_pliromis_array=array(); 
$temp=''; if (isset($_POST['def_tropos_pliromis_array'])) $temp=trim_gks(base64_decode($_POST['def_tropos_pliromis_array']));
//$temp=$tropos_pliromis.','.$temp;
//$parts=explode(',',$temp);
//foreach ($parts as $value) {
//  $value=intval(trim_gks($value));
//  if ($value>0 and in_array($value,$def_tropos_pliromis_array)==false) $def_tropos_pliromis_array[]=$value;
//}
$def_tropos_pliromis_array=json_decode($temp,true);
//print '<pre>';print_r($def_tropos_pliromis_array);die();



$affect_balance=0; if (isset($_POST['def_affect_balance'])) $affect_balance=intval($_POST['def_affect_balance']);  
if ($affect_balance!=1) $affect_balance=0;
$affect_balance_all_poso=0; if (isset($_POST['def_affect_balance_all_poso'])) $affect_balance_all_poso=intval($_POST['def_affect_balance_all_poso']);  
if ($affect_balance_all_poso!=1) $affect_balance_all_poso=0;
$affect_balance_all_poso_type=''; if (isset($_POST['def_affect_balance_all_poso_type'])) $affect_balance_all_poso_type=trim_gks($_POST['def_affect_balance_all_poso_type']);
if ($affect_balance_all_poso_type=='') $affect_balance_all_poso_type='total_price_net'; 


if ($GKS_CRM_ENABLE) {
  $def_crm_channel_id=0; if (isset($_POST['def_crm_channel_id'])) $def_crm_channel_id=intval($_POST['def_crm_channel_id']);
  $def_crm_channel_contact_id=0; if (isset($_POST['def_crm_channel_contact_id'])) $def_crm_channel_contact_id=intval($_POST['def_crm_channel_contact_id']);
  $def_crm_channel_campain_id=0; if (isset($_POST['def_crm_channel_campain_id'])) $def_crm_channel_campain_id=intval($_POST['def_crm_channel_campain_id']);
  $def_crm_channel_url=''; if (isset($_POST['def_crm_channel_url'])) $def_crm_channel_url=trim_gks(base64_decode($_POST['def_crm_channel_url']));
  $def_crm_channel_code=''; if (isset($_POST['def_crm_channel_code'])) $def_crm_channel_code=trim_gks(base64_decode($_POST['def_crm_channel_code']));
  $def_crm_channel_text=''; if (isset($_POST['def_crm_channel_text'])) $def_crm_channel_text=trim_gks(base64_decode($_POST['def_crm_channel_text']));
  
  if ($def_crm_channel_id<=0) {
    $def_crm_channel_contact_id=0;
    $def_crm_channel_campain_id=0;
    $def_crm_channel_url='';
    $def_crm_channel_code='';
    $def_crm_channel_text='';
  } else {
    $sql_channel="select * from gks_crm_channel_sale where id_crm_channel_sale=".$def_crm_channel_id;
    $result_channel = $db_link->query($sql_channel);        
    if (!$result_channel) {
      debug_mail(false,'error sql',$sql_channel);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    if ($result_channel->num_rows!=1) {
      debug_mail(false,'def_crm_channel_id empty');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το κανάλι πωλήσεων').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
      echo json_encode($return); die();}
    $row_channel = $result_channel->fetch_assoc();
    if ($row_channel['crm_channel_has_contact']==0)  $def_crm_channel_contact_id=0;
    if ($row_channel['crm_channel_has_campain']==0)  $def_crm_channel_campain_id=0;
    if ($row_channel['crm_channel_has_url']==0)  $def_crm_channel_url='';
    if ($row_channel['crm_channel_has_code']==0)  $def_crm_channel_code='';
    if ($row_channel['crm_channel_has_text']==0)  $def_crm_channel_text='';
  }  
}


$sql="SELECT gks_acc_seires.id_acc_seira,gks_acc_seires.is_xeirografi
FROM (((gks_acc_seires 
LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE eidos_parastatikou_type_id in (1,2,5) AND id_acc_eidos_parastatikou not in (702,703,704)
AND gks_acc_seires.is_disable=0 AND gks_acc_journal.is_disable=0 
AND gks_company.company_disable=0 
AND gks_acc_seires.id_acc_seira=".$pos_seira_id." 
AND gks_acc_journal.id_acc_journal=".$pos_journal_id." 
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
  debug_mail(false,'seira not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο συνδυασμός εταιρείας/υποκαταστήματος, ημερολογίου και σειράς')));
  echo json_encode($return); die();}
$row_seira = $result->fetch_assoc();
$is_xeirografi=$row_seira['is_xeirografi'];

$eidos_parastatikou_type_id=0;
$eidos_parastatikou_need_afm=0;
$eidos_parastatikou_has_fpa=1;
$affect_balance_pros=0;
$whi_eidos_parastatikou_stock_pros=0;
$whi_eidos_parastatikou_stock_pros_org=0;
$whi_eidos_parastatikou_type_id=0;
$whi_eidos_parastatikou_type_id_org=0;

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
WHERE gks_acc_journal.id_acc_journal=".$pos_journal_id." and gks_acc_eidi_parastatikon.eidos_parastatikou_type_id>0";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
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
  if ($eidos_parastatikou_aade_code=='5.1' and $credit_memo_for_acc_inv_id<=0) {
    $message=gks_lang('Παραστατικά με ημερολόγιο το οποίο έχει ως τύπο παραστατικού το <b>Πιστωτικό Τιμολόγιο / Συσχετιζόμενο</b> δεν μπορούν να δημιουργηθούν άμεσα').
    '<br>'.
    gks_lang('Θα πρέπει να δημιουργηθούν μέσα από το συσχετιζόμενο παραστατικό');
    
    debug_mail(false,$message,$sql);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die(); 
  }
}
if ($affect_balance_pros!=-1 and $affect_balance_pros!=1) {
  $affect_balance_pros=0;
}

if ($eidos_parastatikou_type_id<=0) {
  debug_mail(false,'eidos_parastatikou_type_id empty',$pos_journal_id.' '.$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο γενικός τύπου του παραστατικού')));
  echo json_encode($return); die();}
  
$warehouses_id_from=0; if (isset($_POST['pos_warehouses_id_from'])) $warehouses_id_from=intval($_POST['pos_warehouses_id_from']);
$warehouses_id_to=0;   if (isset($_POST['pos_warehouses_id_to']))   $warehouses_id_to=intval($_POST['pos_warehouses_id_to']);

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
//    $tropos_apostolis=1; //den apaiteitai apostoli
    
  } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
    
  } else {
    //echo '<pre>ddddddddddddd '.$whi_eidos_parastatikou_stock_pros_org.'|'.$whi_eidos_parastatikou_type_id_org;die();
    if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
        $warehouses_id_from=1; //virtual warehouse pelates
        $warehouses_id_from_is_virtual=true;
      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutis
        $warehouses_id_from=2; //virtual warehouse promitheutis
        $warehouses_id_from_is_virtual=true;
      }
    } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
      if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
        $warehouses_id_to=1; //virtual warehouse pelates
        $warehouses_id_to_is_virtual=true;
      } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheutis
        $warehouses_id_to=2; //virtual warehouse promitheutis
        $warehouses_id_to_is_virtual=true;
      }
    }
  }
  
  //echo '<pre>'.$whi_eidos_parastatikou_type_id_org;die();
  
  //echo '<pre>'.$warehouses_id_from.'|'.$warehouses_id_from_is_virtual;die();
  if ($warehouses_id_from>0 and $warehouses_id_from_is_virtual==false) { //ektos virtual
    $sql="select * from gks_warehouses where is_virtual=0 and id_warehouse=".$warehouses_id_from;
    if ($whi_eidos_parastatikou_type_id!=23) { //not endodiakinisi
      if ($company_id>0) $sql.=" and company_id=".$company_id;
      if ($company_sub_id==0) $sql.=" and company_sub_id=0";
      else if ($company_sub_id>0) $sql.=" and company_sub_id=".$company_sub_id;
    }
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
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
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
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
  
  
  if ($whi_eidos_parastatikou_type_id==21 or $whi_eidos_parastatikou_type_id==22) { //deltio apostolis, deltio parpalavis
    if ($user_id<=0) {
      debug_mail(false,'select user_id','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποια επαφή (πελάτη/προμηθευτή)')));
      echo json_encode($return); die();}
    
    if ($warehouses_id_from<=0) {
      debug_mail(false,'select warehouses_id_from','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
      echo json_encode($return); die();}
    
    if ($warehouses_id_to<=0) {
      debug_mail(false,'select warehouses_id_to','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
      echo json_encode($return); die();}
  }
    
  if ($whi_eidos_parastatikou_type_id==23) { //endodiakinisi
    if ($warehouses_id_from<=0) {
      debug_mail(false,'select warehouses_id_from','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Από</b> την οποία θα φύγουν τα πράγματα')));
      echo json_encode($return); die();}
  
    if ($warehouses_id_to<=0) {
      debug_mail(false,'select warehouses_id_to','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
      echo json_encode($return); die();}
  }
    
  
  if ($whi_eidos_parastatikou_type_id==24) { //apografi
    if ($warehouses_id_to<=0) {
      debug_mail(false,'select apografi warehouses_id_to','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη που <b>Αφορά</b> η απογραφή')));
      echo json_encode($return); die();}
  }
  
  
  
  if ($warehouses_id_from==$warehouses_id_to) {
    debug_mail(false,'warehouses_id_from warehouses_id_to','');
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
//        debug_mail(false,'whi_eidos_parastatikou_type_id 24 warehouses_id_to','');
//        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη που <b>Αφορά</b> η απογραφή')));
//        echo json_encode($return); die();}
//    } else {
//      if ($warehouses_id_to<=0) {
//        debug_mail(false,'whi_eidos_parastatikou_type_id<>24 warehouses_id_to 0','');
//        $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την αποθήκη <b>Προς</b> την οποία θα πάνε τα πράγματα')));
//        echo json_encode($return); die();}
//    }
//    
//    if ($warehouses_id_from==$warehouses_id_to) {
//      debug_mail(false,'whi_eidos_parastatikou_type_id == warehouses_id_to','');
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


$def_products=array(
  'ids'=>array(),
  'cats'=> array(),
  'brands'=>array(),
  'text'=>array(),  
);

$def_products_ids=''; if (isset($_POST['def_products_ids'])) $def_products_ids=trim_gks(base64_decode($_POST['def_products_ids']));
if ($def_products_ids!='') {
  $parts=explode(",",$def_products_ids);
  foreach ($parts as $value) {
    $value=intval(trim_gks($value));
    if ($value>0) {
      if (in_array($value,$def_products['ids'])==false) {
        $def_products['ids'][]=$value;
      }
    }
  }  
}


$def_products_text=''; if (isset($_POST['def_products_text'])) $def_products_text=trim_gks(base64_decode($_POST['def_products_text']));
if ($def_products_text!='') {
  $def_products_text=str_replace("\r\n", "\n", $def_products_text);
  $def_products_text=str_replace("\r",   "\n", $def_products_text);
  $def_products_text=str_replace("\n\n", "\n", $def_products_text);
  $def_products_text=str_replace("\n\n", "\n", $def_products_text);
  
  $parts=explode("\n",$def_products_text);
  foreach ($parts as $value) {
    $value=trim_gks($value);
    if ($value!='') {
      if (in_array($value,$def_products['text'])==false) {
        $def_products['text'][]=$value;
        
      }
      
    }
  } 
  
}
$def_products=json_encode($def_products);






$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_pos');


$redirect='';
if ($id==-1) {
  $pos_guid=guid_for_pos();
  $sql="insert into gks_pos (mydate_add,user_id_add,myip,pos_guid) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."','".$db_link->escape_string($pos_guid)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-pos-item.php?id='.$id); 
  
}


//echo '<pre>'.$warehouses_id_from.'|'.$warehouses_id_from_is_virtual;die();

$sql="update gks_pos set 
pos_name='".$db_link->escape_string($pos_name)."',
pos_descr='".$db_link->escape_string($pos_descr)."',
pos_user_can_change_prices=".$pos_user_can_change_prices.",
pos_max_ammount=".number_format($pos_max_ammount,16,'.','').",
pos_aade_mydata_live=".$pos_aade_mydata_live.",
pos_multi_copies=".$pos_multi_copies.",
pos_installments=".$pos_installments.",
pos_tip=".$pos_tip.",
pos_can_search_products=".$pos_can_search_products.",
pos_indexeddb=".$pos_indexeddb.",
pos_auto_click_start_at_paywith=".$pos_auto_click_start_at_paywith.",
pos_disable=".$pos_disable.",
pos_print_enable=".$pos_print_enable.",
pos_print_file_type='".$db_link->escape_string($file_type)."',
pos_print_landscape=".$is_landscape.",
pos_print_grayscale=".$grayscale.",
pos_print_zoom=".number_format($zoom,2,'.','').",
pos_print_form_id=".$pos_print_form_id.",
pos_thermal_form_id=".$pos_thermal_form_id.",
pos_print_x_form_id=".$pos_print_x_form_id.",
pos_paroxos_send_pdf=".$pos_paroxos_send_pdf.",

pos_company_id=".$company_id.",
pos_company_sub_id=".$company_sub_id.",
pos_journal_id=".$pos_journal_id.",
pos_seira_id=".$pos_seira_id.",
def_aade_skopos_diakinisis_id=".$def_aade_skopos_diakinisis_id.",
def_fiscal_position_id=".$def_fiscal_position_id.",
def_pricelist_id=".$def_pricelist_id.",
def_assigned_id=".$def_assigned_id.",

def_user_id=".$user_id.",
def_user_lang='".$db_link->escape_string($def_user_lang)."',


def_tropos_apostolis=".$tropos_apostolis.",
def_tropos_pliromis=".$tropos_pliromis.",
def_delivery_id_8=".$delivery_id_8.",

def_affect_balance=".$affect_balance.",
def_affect_balance_all_poso=".$affect_balance_all_poso.",
def_affect_balance_all_poso_type='".$db_link->escape_string($affect_balance_all_poso_type)."',
def_affect_balance_pros=".$affect_balance_pros.",

pos_warehouses_id_from=".$warehouses_id_from.",
pos_warehouses_id_to=".$warehouses_id_to.",

";



if ($GKS_CRM_ENABLE) {
  $sql.=
  "def_crm_channel_id=".$def_crm_channel_id.",
  def_crm_channel_contact_id=".$def_crm_channel_contact_id.",
  def_crm_channel_campain_id=".$def_crm_channel_campain_id.",
  def_crm_channel_url=". ($def_crm_channel_url =='' ? 'null' : "'".$db_link->escape_string($def_crm_channel_url)."'").",
  def_crm_channel_code=". ($def_crm_channel_code =='' ? 'null' : "'".$db_link->escape_string($def_crm_channel_code)."'").",
  def_crm_channel_text=". ($def_crm_channel_text =='' ? 'null' : "'".$db_link->escape_string($def_crm_channel_text)."'").",";
}



$sql.="
def_products='".$db_link->escape_string($def_products)."',
def_tropos_pliromis_array='".$db_link->escape_string(json_encode($def_tropos_pliromis_array))."',
app_mobile_userlogin_id=".$app_mobile_userlogin_id.",
pos_sms_erp_app_mobile_id_code='".$db_link->escape_string($pos_sms_erp_app_mobile_id_code)."',
pos_sms_template_text='".$db_link->escape_string($pos_sms_template_text)."',

erp_app_id=".$erp_app_id.",
erp_app_filter='".$db_link->escape_string($erp_app_filter)."',
erp_app_dest='".$db_link->escape_string($erp_app_dest)."',
erp_app_dest_printer='".$db_link->escape_string($erp_app_dest_printer)."',
erp_app_dest_printer_method=".$erp_app_dest_printer_method.",
erp_app_dest_printer_lpr_ip='".$db_link->escape_string($erp_app_dest_printer_lpr_ip)."',
erp_app_dest_printer_copies=".$erp_app_dest_printer_copies.",
erp_app_dest_folder='".$db_link->escape_string($erp_app_dest_folder)."',

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_pos = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }




$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


  
  
$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();
    

// def_products
