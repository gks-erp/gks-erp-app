<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


if ($my_wp_user_id <= 0) {
  debug_mail(false,'user not login','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Θα πρέπει πρώτα να συνδεθείτε')),'myreload' => true);
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση προφίλ');
db_open();
stat_record();

$sql="select * from ".GKS_WP_TABLE_PREFIX."users where id=".$my_wp_user_id." limit 1";
$result_users = $db_link->query($sql);        
if (!$result_users) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result_users->num_rows!=1) {
  debug_mail(false,'record not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  }
$row_wp_user = $result_users->fetch_assoc();



$show_job_fields=(bool)intval($_POST['show_job_fields']);

$form_first_name=trim_gks(stripslashes(urldecode($_POST['form_first_name'])));
$form_last_name=trim_gks(stripslashes(urldecode($_POST['form_last_name'])));
//$form_user_nicename=trim_gks(stripslashes(urldecode($_POST['form_user_nicename'])));
$form_display_name=trim_gks(stripslashes(urldecode($_POST['form_display_name'])));
$form_gks_sex=intval($_POST['form_gks_sex']);
$form_gks_lang=trim_gks($_POST['form_gks_lang']);
$form_password_old=trim_gks(stripslashes(urldecode($_POST['form_password_old'])));
$form_password_new1=trim_gks(stripslashes(urldecode($_POST['form_password_new1'])));
$form_password_new2=trim_gks(stripslashes(urldecode($_POST['form_password_new2'])));
$form_user_photo=trim_gks(stripslashes(urldecode($_POST['form_user_photo'])));
$form_user_email=trim_gks(stripslashes(urldecode($_POST['form_user_email'])));
$form_user_mobile=trim_gks(stripslashes(urldecode($_POST['form_user_mobile'])));
$form_phone_home=trim_gks(stripslashes(urldecode($_POST['form_phone_home'])));
$form_user_url=trim_gks(stripslashes(urldecode($_POST['form_user_url'])));
$form_ma_odos=trim_gks(stripslashes(urldecode($_POST['form_ma_odos'])));
$form_ma_arithmos=trim_gks(stripslashes(urldecode($_POST['form_ma_arithmos'])));
$form_ma_orofos=trim_gks(stripslashes(urldecode($_POST['form_ma_orofos'])));
$form_ma_perioxi=trim_gks(stripslashes(urldecode($_POST['form_ma_perioxi'])));
$form_ma_poli=trim_gks(stripslashes(urldecode($_POST['form_ma_poli'])));
$form_ma_tk=trim_gks(stripslashes(urldecode($_POST['form_ma_tk'])));
$form_ma_country_id=intval($_POST['form_ma_country_id']);
$form_ma_nomos_id=intval($_POST['form_ma_nomos_id']);
$form_extra_address_delete=trim_gks(stripslashes(urldecode($_POST['form_extra_address_delete'])));
$form_eponimia=trim_gks(stripslashes(urldecode($_POST['form_eponimia'])));
$form_title=trim_gks(stripslashes(urldecode($_POST['form_title'])));
$form_afm=trim_gks(stripslashes(urldecode($_POST['form_afm'])));
$form_doy=trim_gks(stripslashes(urldecode($_POST['form_doy'])));
$form_epaggelma=trim_gks(stripslashes(urldecode($_POST['form_epaggelma'])));

if ($show_job_fields) {
  $form_genisi_date=trim_gks(stripslashes(urldecode($_POST['form_genisi_date'])));
  $form_description=trim_gks(stripslashes(urldecode($_POST['form_description'])));
  $form_ethnikotita=trim_gks(stripslashes(urldecode($_POST['form_ethnikotita'])));
  $form_alli_apasxolisi=trim_gks(stripslashes(urldecode($_POST['form_alli_apasxolisi'])));
  $form_cv_proipiresia=trim_gks(stripslashes(urldecode($_POST['form_cv_proipiresia'])));
  $form_cv_spoydes=trim_gks(stripslashes(urldecode($_POST['form_cv_spoydes'])));
  $form_cv_seminaria=trim_gks(stripslashes(urldecode($_POST['form_cv_seminaria'])));
  $form_cv_mitriki_glossa=trim_gks(stripslashes(urldecode($_POST['form_cv_mitriki_glossa'])));
  $form_cv_jenes_glosses=trim_gks(stripslashes(urldecode($_POST['form_cv_jenes_glosses'])));
  $form_cv_sxesi_me_photografia=trim_gks(stripslashes(urldecode($_POST['form_cv_sxesi_me_photografia'])));
  $form_cv_metaforiko_meso=trim_gks(stripslashes(urldecode($_POST['form_cv_metaforiko_meso'])));
  $form_cv_has_bike=intval($_POST['form_cv_has_bike']);
  $form_cv_has_motorcycle=intval($_POST['form_cv_has_motorcycle']);
  $form_cv_has_car=intval($_POST['form_cv_has_car']);
  
  $form_arithmos_tautoitas=trim_gks(stripslashes(urldecode($_POST['form_arithmos_tautoitas'])));
  $form_arxi_ekdosis=trim_gks(stripslashes(urldecode($_POST['form_arxi_ekdosis'])));
  $form_amka=trim_gks(stripslashes(urldecode($_POST['form_amka'])));
  $form_ama_eam=trim_gks(stripslashes(urldecode($_POST['form_ama_eam'])));
  $form_onoma_patera=trim_gks(stripslashes(urldecode($_POST['form_onoma_patera'])));
  $form_onoma_miteras=trim_gks(stripslashes(urldecode($_POST['form_onoma_miteras'])));
  $form_oikogeniaki_katastasti_id=intval($_POST['form_oikogeniaki_katastasti_id']);
  $form_oikogeniaki_katastasti_paidia =-1; if ($_POST['form_oikogeniaki_katastasti_paidia'] != '') $form_oikogeniaki_katastasti_paidia = intval($_POST['form_oikogeniaki_katastasti_paidia']);
}

$form_newsletter_email=trim_gks(stripslashes(urldecode($_POST['form_newsletter_email'])));
$form_newsletter_sms=trim_gks(stripslashes(urldecode($_POST['form_newsletter_sms'])));



//tmima elegxou 1 start 




if ($form_first_name=='') {debug_mail(false,'emptyl', 'form_first_name can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Το Όνομά μου δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }
  
if ($form_last_name=='') {debug_mail(false,'emptyl', 'form_last_name can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Το Επώνυμό μου δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }
  
//if ($form_user_nicename=='') {debug_mail(false,'emptyl', 'form_user_nicename can not be empty');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Υποκοριστικό δεν μπορεί να είναι κενό')));
//  echo json_encode($return); die(); }
//
//$sql="SELECT user_id FROM ".GKS_WP_TABLE_PREFIX."usermeta WHERE user_id<>".$my_wp_user_id." AND meta_key='nickname' and meta_value='".$db_link->escape_string($form_user_nicename)."'";
//$result = $db_link->query($sql); 
//if (!$result) {
//  debug_mail(false,'error sql',$sql);
//  $return = array('success' => false, 'message' => base64_encode('sql error'));
//  echo json_encode($return); die();}
//if ($result->num_rows>0) {
//  debug_mail(false,'user_nicename error',$form_user_nicename);
//  $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$form_user_nicename,gks_lang('Το υποκοριστικό <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλη επαφή').'<br>'.gks_lang('Επιλέξτε κάτι άλλο'))));
//  echo json_encode($return); die(); }



if ($form_display_name=='') {debug_mail(false,'emptyl', 'form_display_name can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Προβολή δημοσίως ως δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

$form_genisi_date_int=0;

if ($show_job_fields) {
  if (strlen($form_genisi_date) >= 2 and substr($form_genisi_date,0,2) =='__') $form_genisi_date='';
  if ($form_genisi_date != '') {
    $limit_genisi_date=time() - 15*365*24*60*60; // tha prepei na einai toylaxiston 15 eton
    $form_genisi_date_int = gks_myFormatDate($form_genisi_date);
    if ($form_genisi_date_int > $limit_genisi_date) {
      debug_mail(false,'emptyl', 'form_genisi_date to small '. $form_genisi_date);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Ημερομηνία Γέννησης είναι πολύ μικρή')));
      echo json_encode($return); die(); 
    }
    $form_genisi_date = "'".date('Y-m-d',$form_genisi_date_int)."'";
  } else {
    $form_genisi_date = 'null';
  }
}

if ($form_password_new1 != '' or $form_password_new2 != '')  {
  if ($form_password_old=='') {debug_mail(false,'emptyl', 'form_password_old can not be empty');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Για να αλλάξετε τον κωδικό σας, θα πρέπει να εισάγετε τον παλιό σας κωδικό')));
    echo json_encode($return); die(); }  
  
  if ($form_password_new1 != $form_password_new2) {debug_mail(false,'emptyl', 'form_password_new1 != form_password_new2');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Θα πρέπει να εισάγετε τον ίδιο κωδικό στα πεδία Νέος Κωδικός και Νέος Κωδικός ξανά')));
    echo json_encode($return); die(); }  
  
  if ($form_password_new1!='' and mb_strlen($form_password_new1)<5) {
    debug_mail(false,'form_password_new1 error','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο νέος κωδικός πρόσβασης είναι πολύ μικρός. Θα πρέπει να είναι τουλάχιστον 5 χαρακτήρες')));
    echo json_encode($return); die(); }  
  
  
  
  if (!wp_check_password($form_password_old, $row_wp_user['user_pass'], $my_wp_user_id)) {
    debug_mail(false,'emptyl', 'form_password_old not wp_check_password');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο Παλιός κωδικός δεν είναι σωστός')));
    echo json_encode($return); die(); }    
}

if ($form_user_email=='') {debug_mail(false,'emptyl', 'form_user_email can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Email δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }
  
if (strpos($form_user_email, '@example.com') !== false) {
  debug_mail(false,'email error',$form_user_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  echo json_encode($return); die();}
	
	  

if (!filter_var($form_user_email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,gks_lang('To email δεν είναι σωστό').' : '.$form_user_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  echo json_encode($return); die();}


//if ($form_user_mobile != '' and (strlen($form_user_mobile) != 10 or substr($form_user_mobile,0,2) != '69') ) {
//  debug_mail(false,'form_user_mobile error',$form_user_mobile);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To Κινητό Τηλέφωνο δεν είναι σωστό')));
//  echo json_encode($return); die();}  

//if ($form_phone_home != '' and (strlen($form_phone_home) != 10 or substr($form_phone_home,0,1) != '2') ) {
//  debug_mail(false,gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό').' : '.$form_phone_home);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό')));
//  echo json_encode($return); die();}  


if ($form_user_email!='') {
  $sql="select user_email from ".GKS_WP_TABLE_PREFIX."users where id<>".$my_wp_user_id." and user_email like '".$db_link->escape_string($form_user_email)."'";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows>0) {
    debug_mail(false,'form_user_email error',$form_user_email);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$form_user_email,gks_lang('Το email <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλον χρήστη'))));
    echo json_encode($return); die(); }
}

if ($form_user_mobile!='') {
  $sql="SELECT meta_value FROM ".GKS_WP_TABLE_PREFIX."usermeta WHERE meta_key='mobile' AND user_id<>".$my_wp_user_id." AND meta_value like '".$db_link->escape_string($form_user_mobile)."'";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows>0) {
    debug_mail(false,'form_user_mobile error',$form_user_mobile);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$form_user_mobile,gks_lang('Το κινητό <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλον χρήστη'))));
    echo json_encode($return); die(); }
}


if ($form_afm != '' and CheckAFM($form_afm) == false) {
  debug_mail(false,'form_afm',$form_afm);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To ΑΦΜ δεν είναι έγκυρο')));
  echo json_encode($return); die();}  




if ($show_job_fields) {

  if ($form_arithmos_tautoitas != '' and strlen($form_arithmos_tautoitas)<=6 ) {
    debug_mail(false,'form_arithmos_tautoitas',$form_arithmos_tautoitas);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο Αριθμός Ταυτότητας δεν είναι έγκυρος')));
    echo json_encode($return); die();}  

  if ($form_arxi_ekdosis != '' and strlen($form_arxi_ekdosis)<=7 ) {
    debug_mail(false,'form_arxi_ekdosis',$form_arxi_ekdosis);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('H Αρχή Έκδοσης δεν είναι έγκυρη')));
    echo json_encode($return); die();}  

  if ($form_amka != '' and CheckAMKA($form_amka) == false) {
    debug_mail(false,'form_amka',$form_amka);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('To ΑΜΚΑ δεν είναι έγκυρο')));
    echo json_encode($return); die();}  

  //if ($form_amka!='' and $form_genisi_date_int>0) {
  //  if (date('dmy',$form_genisi_date_int) != substr($form_amka, 0,6) and $my_wp_user_id!=446) {
  //    debug_mail(false,'form_amka form_genisi_date_int',$form_amka.'--'.date('dmy',$form_genisi_date_int));
  //    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το ΑΜΚΑ δεν συμβαδίζει με την ημερομηνία γέννησης')));
  //    echo json_encode($return); die();
  //  }
  //}

  if ($form_ama_eam != '' and (strlen($form_ama_eam)<=5 or ctype_digit($form_ama_eam) == false)) {
    debug_mail(false,'form_ama_eam'.$form_ama_eam);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('To ΑΜΑ - ΕΑΜ δεν είναι έγκυρο')));
    echo json_encode($return); die();}  
 
}




//tmima elegxou 1 end


//tmima enimerosis 1 start 
if ($form_password_new1 != '' and $form_password_new2 != '')  {
  if (!isset($GLOBALS['hook_suffix'])) {
    $GLOBALS['hook_suffix']='';
  }
  $mytt['ID'] = $my_wp_user_id; //user ID
  $mytt['user_pass'] = $form_password_new1;
  wp_update_user( $mytt );
}

$sql="update ".GKS_WP_TABLE_PREFIX."users set
gks_last_update=now(),
user_email='".$db_link->escape_string($form_user_email)."',
user_url='".$db_link->escape_string($form_user_url)."',
display_name='".$db_link->escape_string($form_display_name)."',
gks_sex=".$form_gks_sex.",
gks_lang='".$db_link->escape_string($form_gks_lang)."',
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."' 
where id=".$my_wp_user_id." limit 1";
$result = $db_link->query($sql); 
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

if (trim_gks(get_user_meta($my_wp_user_id, 'nickname', true).'') == '') update_user_meta( $my_wp_user_id, 'nickname', $form_first_name.' '.$form_last_name);

//if ($form_user_nicename != get_user_meta($my_wp_user_id, 'nickname', true) and update_user_meta( $my_wp_user_id, 'nickname', $form_user_nicename) == false) {
//  debug_mail(false,'error update_user_meta nickname',$form_user_nicename);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την ενημέρωση του').' '.gks_lang('Υποκοριστικό').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
//  echo json_encode($return); die();}  
//

if ($form_first_name != get_user_meta($my_wp_user_id, 'first_name', true) and update_user_meta( $my_wp_user_id, 'first_name', $form_first_name) == false) {
  debug_mail(false,'error update_user_meta form_first_name',$form_first_name);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την ενημέρωση του').' '.gks_lang('Το Όνομά μου').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}  
  
if ($form_last_name != get_user_meta($my_wp_user_id, 'last_name', true) and update_user_meta( $my_wp_user_id, 'last_name', $form_last_name) == false) {
  debug_mail(false,'error update_user_meta form_last_name',$form_last_name);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την ενημέρωση του').' '.gks_lang('Το Επώνυμό μου').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}  
  
if ($form_display_name != get_user_meta($my_wp_user_id, 'display_name', true) and update_user_meta( $my_wp_user_id, 'display_name', $form_display_name) == false) {
  debug_mail(false,'error update_user_meta form_display_name',$form_display_name);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την ενημέρωση του').' '.gks_lang('Προβολή δημοσίως ως').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}  

if ($form_user_mobile != get_user_meta($my_wp_user_id, 'mobile', true) and update_user_meta( $my_wp_user_id, 'mobile', $form_user_mobile) == false) {
  debug_mail(false,'error update_user_meta form_user_mobile',$form_user_mobile);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την ενημέρωση του').' '.gks_lang('Κινητό Τηλέφωνο').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}  
  

if ($show_job_fields) {  
  if ($form_description != get_user_meta($my_wp_user_id, 'description', true) and update_user_meta( $my_wp_user_id, 'description', $form_description) == false) {
    debug_mail(false,'error update_user_meta form_description',$form_description);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την ενημέρωση του').' '.gks_lang('Σύντομο βιογραφικό').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}  
}

if ($form_user_photo != '') {
  if (substr($form_user_photo, 0,1) =='/') { 
    //$form_user_photo = substr(GKS_SITE_URL, 0, strlen(GKS_SITE_URL)-1) . $form_user_photo;
  }
}
if ($form_user_photo != get_user_meta($my_wp_user_id, 'wsl_current_user_image', true) and update_user_meta( $my_wp_user_id, 'wsl_current_user_image', $form_user_photo) == false) {
  debug_mail(false,'error update_user_meta form_user_photo',$form_user_photo);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την ενημέρωση της').' '.gks_lang('φωτογραφίας').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}  
 

if ($form_user_email!='') {
  $sql_comm="select * from gks_users_communication where user_id=".$my_wp_user_id." and comm_type='email' and comm_value like '".$db_link->escape_string($form_user_email)."'";
  $result_comm = $db_link->query($sql_comm); 
  if (!$result_comm) {
    debug_mail(false,'sql error',$form_user_photo);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}  
  if ($result_comm->num_rows == 0) {
    $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='email'";
    $result_comm = $db_link->query($sql_comm);
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
    
    $sql_comm="insert into gks_users_communication (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    user_id,comm_type,comm_value,comm_descr,comm_primary
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$my_wp_user_id.",'email','".$db_link->escape_string($form_user_email)."','".$db_link->escape_string(gks_lang('Εργασίας'))."',1
    )";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {debug_mail(false,'sql error','<pre>'.htmlspecialchars_gks($sql_comm)."\r\n".$db_link->errno . '-'.$db_link->error.'</pre>'); die('sql eror');}
  } else {
    $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='email' and comm_value not like '".$db_link->escape_string($form_user_email)."'";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
    $sql_comm="update gks_users_communication set comm_primary=1 where user_id=".$my_wp_user_id." and comm_type='email' and comm_value like '".$db_link->escape_string($form_user_email)."'";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
  }
}


if ($form_phone_home!='') {
  $sql_comm="select * from gks_users_communication where user_id=".$my_wp_user_id." and comm_type='phone' and comm_value like '".$db_link->escape_string($form_phone_home)."'";
  $result_comm = $db_link->query($sql_comm); 
  if (!$result_comm) {
    debug_mail(false,'sql error',$form_user_photo);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}  
  if ($result_comm->num_rows == 0) {
    $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='phone'";
    $result_comm = $db_link->query($sql_comm);
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
    
    $sql_comm="insert into gks_users_communication (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    user_id,comm_type,comm_value,comm_descr,comm_primary
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$my_wp_user_id.",'phone','".$db_link->escape_string($form_phone_home)."','".$db_link->escape_string(gks_lang('Εργασίας'))."',1
    )";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {debug_mail(false,'sql error','<pre>'.htmlspecialchars_gks($sql_comm)."\r\n".$db_link->errno . '-'.$db_link->error.'</pre>'); die('sql eror');}
  } else {
    $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='phone' and comm_value not like '".$db_link->escape_string($form_phone_home)."'";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
    $sql_comm="update gks_users_communication set comm_primary=1 where user_id=".$my_wp_user_id." and comm_type='phone' and comm_value like '".$db_link->escape_string($form_phone_home)."'";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
  }  
  
} 

if ($form_user_mobile!='') {
  $sql_comm="select * from gks_users_communication where user_id=".$my_wp_user_id." and comm_type='phone' and comm_value like '".$db_link->escape_string($form_user_mobile)."'";
  $result_comm = $db_link->query($sql_comm); 
  if (!$result_comm) {
    debug_mail(false,'sql error',$form_user_photo);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}  
  if ($result_comm->num_rows == 0) {
    $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='phone'";
    $result_comm = $db_link->query($sql_comm);
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
    
    $sql_comm="insert into gks_users_communication (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    user_id,comm_type,comm_value,comm_descr,comm_primary
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$my_wp_user_id.",'phone','".$db_link->escape_string($form_user_mobile)."','".$db_link->escape_string(gks_lang('Κινητό'))."',1
    )";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {debug_mail(false,'sql error','<pre>'.htmlspecialchars_gks($sql_comm)."\r\n".$db_link->errno . '-'.$db_link->error.'</pre>'); die('sql eror');}
  } else {
    $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='phone' and comm_value not like '".$db_link->escape_string($form_user_mobile)."'";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
    $sql_comm="update gks_users_communication set comm_primary=1 where user_id=".$my_wp_user_id." and comm_type='phone' and comm_value like '".$db_link->escape_string($form_user_mobile)."'";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
  }  
  
}

if ($form_phone_home=='' and $form_user_mobile=='') {
  $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='phone'";
  $result_comm = $db_link->query($sql_comm); 
  if (!$result_comm) {
    debug_mail(false,'sql error',$form_user_photo);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}  
}

if ($form_user_url!='') {
  $sql_comm="select * from gks_users_communication where user_id=".$my_wp_user_id." and comm_type='url' and comm_value like '".$db_link->escape_string($form_user_url)."'";
  $result_comm = $db_link->query($sql_comm); 
  if (!$result_comm) {
    debug_mail(false,'sql error',$form_user_photo);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}  
  if ($result_comm->num_rows == 0) {
    $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='url'";
    $result_comm = $db_link->query($sql_comm);
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
    
    $sql_comm="insert into gks_users_communication (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    user_id,comm_type,comm_value,comm_descr,comm_primary
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$my_wp_user_id.",'url','".$db_link->escape_string($form_user_url)."','".$db_link->escape_string(gks_lang('Εταιρικό'))."',1
    )";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {debug_mail(false,'sql error','<pre>'.htmlspecialchars_gks($sql_comm)."\r\n".$db_link->errno . '-'.$db_link->error.'</pre>'); die('sql eror');}
  } else {
    $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='url' and comm_value not like '".$db_link->escape_string($form_user_url)."'";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
    $sql_comm="update gks_users_communication set comm_primary=1 where user_id=".$my_wp_user_id." and comm_type='url' and comm_value like '".$db_link->escape_string($form_user_url)."'";
    $result_comm = $db_link->query($sql_comm); 
    if (!$result_comm) {
      debug_mail(false,'sql error',$form_user_photo);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}  
  }  
  
} else {
  $sql_comm="update gks_users_communication set comm_primary=0 where user_id=".$my_wp_user_id." and comm_type='url'";
  $result_comm = $db_link->query($sql_comm); 
  if (!$result_comm) {
    debug_mail(false,'sql error',$form_user_photo);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}   
}



$sql="select user_id from gks_users where user_id=".$my_wp_user_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows==0) {
  $sql="insert into gks_users (user_id,mydate_add,user_id_add,myip) values (".$my_wp_user_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
  $result_gks_users = $db_link->query($sql);    
}

$sql="update gks_users set ";
if ($show_job_fields) {
  $sql.="genisi_date = ".$form_genisi_date.",
  ethnikotita = '".$db_link->escape_string($form_ethnikotita)."',
  alli_apasxolisi = '".$db_link->escape_string($form_alli_apasxolisi)."',
  cv_proipiresia = '".$db_link->escape_string($form_cv_proipiresia)."',
  cv_spoydes = '".$db_link->escape_string($form_cv_spoydes)."',
  cv_seminaria = '".$db_link->escape_string($form_cv_seminaria)."',
  cv_mitriki_glossa = '".$db_link->escape_string($form_cv_mitriki_glossa)."',
  cv_jenes_glosses = '".$db_link->escape_string($form_cv_jenes_glosses)."',
  

  cv_sxesi_me_photografia = '".$db_link->escape_string($form_cv_sxesi_me_photografia)."',
  cv_has_bike = ".$form_cv_has_bike.",
  cv_has_motorcycle = ".$form_cv_has_motorcycle.",
  cv_has_car = ".$form_cv_has_car.",
  cv_metaforiko_meso = '".$db_link->escape_string($form_cv_metaforiko_meso)."',
  
  arithmos_tautoitas = '".$db_link->escape_string($form_arithmos_tautoitas)."',
  arxi_ekdosis = '".$db_link->escape_string($form_arxi_ekdosis)."',
  amka = '".$db_link->escape_string($form_amka)."',
  ama_eam = '".$db_link->escape_string($form_ama_eam)."',
  onoma_patera = '".$db_link->escape_string($form_onoma_patera)."',
  onoma_miteras = '".$db_link->escape_string($form_onoma_miteras)."',
  oikogeniaki_katastasti_id=".$form_oikogeniaki_katastasti_id.",
  oikogeniaki_katastasti_paidia = ".($form_oikogeniaki_katastasti_paidia < 0 ? 'null' : $form_oikogeniaki_katastasti_paidia).",";
}
$sql.="eponimia = '".$db_link->escape_string($form_eponimia)."',
title = '".$db_link->escape_string($form_title)."',
afm = '".$db_link->escape_string($form_afm)."',
doy = '".$db_link->escape_string($form_doy)."',
epaggelma = '".$db_link->escape_string($form_epaggelma)."',
ma_odos = '".$db_link->escape_string($form_ma_odos)."',
ma_arithmos = '".$db_link->escape_string($form_ma_arithmos)."',
ma_orofos = '".$db_link->escape_string($form_ma_orofos)."',
ma_perioxi = '".$db_link->escape_string($form_ma_perioxi)."',
ma_poli = '".$db_link->escape_string($form_ma_poli)."',
ma_tk = '".$db_link->escape_string($form_ma_tk)."',
ma_country_id = ".$form_ma_country_id.",
ma_nomos_id = ".$form_ma_nomos_id.",
phone_home = '".$db_link->escape_string($form_phone_home)."',
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'

where user_id=".$my_wp_user_id." limit 1";
$result = $db_link->query($sql); 
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}


if ($form_extra_address_delete !='') {
  $my_extra_address_delete = explode(',', $form_extra_address_delete);
  //print '<pre>';
  //print_r($my_extra_address_delete);
  //die();
  
  foreach ($my_extra_address_delete as $value) {
    if ($value != '') {
      $valint=intval($value);
      if ($valint>0) {
        $sql="delete from gks_users_extra_address where user_id=".$my_wp_user_id." and id_users_extra_address=".$valint." limit 1";
        $db_link->query($sql); 
      }
    }
  } 
}

//newsletter
$form_newsletter_email_array = array();
if ($form_newsletter_email !='') $form_newsletter_email_array = json_decode($form_newsletter_email, true);

$form_newsletter_sms_array = array();
if ($form_newsletter_sms !='') $form_newsletter_sms_array = json_decode($form_newsletter_sms, true);



foreach ($form_newsletter_email_array as $key => $value) {
  $newsletter_list_id= $value[0];
  
  if ($form_user_email!='') {
    $isapproval = intval($value[1]) == 1 ? 1 : 0;
    
    $sql="insert into gks_newsletter_log (
    mydate,myip,user_id,mytype,mydata,newsletter_list_id,isapproval
    ) values (
    now(),
    '".$db_link->escape_string($gkIP)."',
    ".$my_wp_user_id.",
    'email',
    '".$db_link->escape_string($form_user_email)."',
    ".$newsletter_list_id.",
    ".$isapproval."
    )";
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
      
    $sql="select * from gks_newsletter_emails where myemail like '".$db_link->escape_string($form_user_email)."' and newsletter_list_id=".$newsletter_list_id;
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($result->num_rows == 0) {   
      $sql="insert into gks_newsletter_emails (user_id,myemail,newsletter_list_id,isapproval) values (
      ".$my_wp_user_id.",
      '".$db_link->escape_string($form_user_email)."',
      ".$newsletter_list_id.",
      ".$isapproval.")";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
      
    } else {
      $sql="update gks_newsletter_emails set isapproval=".$isapproval.",user_id=".$my_wp_user_id."
      where myemail like '".$db_link->escape_string($form_user_email)."' 
      and newsletter_list_id=".$newsletter_list_id. " limit 1";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
    }
    
  }
  
  
  if ($form_user_mobile !='') {
    $isapproval = intval($form_newsletter_sms_array[$key][1]) == 1 ? 1 : 0;
    
    $sql="insert into gks_newsletter_log (
    mydate,myip,user_id,mytype,mydata,newsletter_list_id,isapproval
    ) values (
    now(),
    '".$db_link->escape_string($gkIP)."',
    ".$my_wp_user_id.",
    'sms',
    '".$db_link->escape_string($form_user_mobile)."',
    ".$newsletter_list_id.",
    ".$isapproval."
    )";
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
      
    $sql="select * from gks_newsletter_sms where mysms like '".$db_link->escape_string($form_user_mobile)."' and newsletter_list_id=".$newsletter_list_id;
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($result->num_rows == 0) {   
      $sql="insert into gks_newsletter_sms (user_id,mysms,newsletter_list_id,isapproval) values (
      ".$my_wp_user_id.",
      '".$db_link->escape_string($form_user_mobile)."',
      ".$newsletter_list_id.",
      ".$isapproval.")";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
      
    } else {
      $sql="update gks_newsletter_sms set isapproval=".$isapproval.",user_id=".$my_wp_user_id."
      where mysms like '".$db_link->escape_string($form_user_mobile)."' 
      and newsletter_list_id=".$newsletter_list_id. " limit 1";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
    }
    
  } else {
      $sql="update gks_newsletter_sms set isapproval=0
      where user_id=".$my_wp_user_id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
    
    
  }
  
  
  
} 



//tmima enimerosis 1 end 

$ret_run=gks_sociallinks_item_save($_POST,'wp_users',$my_wp_user_id);
if ($ret_run['success']==false) {die(json_encode(array('success'=>false,'message'=>base64_encode($ret_run['message']))));}
 



$calc = calc_profilepososto($my_wp_user_id,false);


//debug_mail(false,'error sql',print_r($calc,true));
$send_email_profile=send_email_profile($my_wp_user_id);

gks_cache_update_menu_version();

$return = array('success' => true, 'message' => base64_encode('OK'),'myreload' => true,
  'profilepososto_user' => $calc['user'],
  'profilepososto_job' => $calc['job'], 
  'user_rf' => $calc['user_rf'], 
  'job_rf' => $calc['job_rf']);
  
echo json_encode($return); die();

