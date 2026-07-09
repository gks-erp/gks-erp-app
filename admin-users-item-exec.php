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
  echo json_encode($return); die();}



$my_page_title=gks_lang('Αποθήκευση επαφής id:').' '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
  $row_wp_user=array();
} else {



  $sql="select * from ".GKS_WP_TABLE_PREFIX."users where id=".$id;
  if (ur_ad() == false) {
    $sql.= " and ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities not like '".$db_link->escape_string('%administrator%')."'
             and ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities not like '".$db_link->escape_string('%adminmy%')."'";
  }
  $sql.=" limit 1";
  
  $result_users = $db_link->query($sql);        
  if (!$result_users) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
    echo json_encode($return); die(); }
  if ($result_users->num_rows!=1) {
    debug_mail(false,'record not found',                           gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row_wp_user = $result_users->fetch_assoc();

}


$communication=trim_gks(base64_decode($_POST['communication']));
$communication_array = array();
if ($communication !='') $communication_array = json_decode($communication, true);
//echo '<pre>';var_dump($communication_array);die();
//echo '<pre>';var_dump($_POST['communication']);die();
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($communication_array,true))); echo json_encode($return); die();
$communication_fix=array();
$communication_fix['email']=array();
$communication_fix['phone']=array();
$communication_fix['url']=array();
foreach ($communication_array as $item) {
  if (isset($item['type']) and ($item['type']=='email' or $item['type']=='phone' or $item['type']=='url')) {
    $val=''; if (isset($item['value'])) $val=trim_gks($item['value']);
    if ($val!='') {
      if ($item['type']=='email') {
        if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
          debug_mail(false,gks_lang('To email δεν είναι σωστό').' : '.$val);
          $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$val,gks_lang('To email [1] δεν είναι σωστό'))));
          echo json_encode($return); die();}
      }
      if ($item['type']=='phone') {
        //echo '<pre>'.$val.'</pre>';
        if (gks_CheckPhone($val)==false) {
          debug_mail(false,gks_lang('To phone δεν είναι σωστό').' : '.$val);
          $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$val,gks_lang('To τηλέφωνο <b>[1]</b> δεν είναι σωστό.<br>Επιτρέπονται μόνο οι <b>αριθμοί</b>, τα παρακάτω σύμβολα<br><b>* # + - , . ( ) / N ;</b><br>και το <b>κενό</b>'))));
          echo json_encode($return); die();}
      }
//      if ($item['type']=='url') {
//        if (!filter_var($val, FILTER_VALIDATE_URL)) {
//          debug_mail(false,gks_lang('To url δεν είναι σωστό').' : '.$val);
//          $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$val,gks_lang('To url <b>[1]</b> δεν είναι σωστό')));
//          echo json_encode($return); die();}
//      }
      $item['ispr']=intval($item['ispr']);
      if ($item['ispr']!=0) $item['ispr']=1;
      $communication_fix[$item['type']][$val]=array('descr' => trim_gks($item['descr']), 'ispr' => $item['ispr']);
    }
  }
}

//ean den exei default, na valo to proto
$found_ispr=false; foreach ($communication_fix['email'] as $value) {if ($value['ispr']==1) {$found_ispr=true; break;}}
if ($found_ispr==false and count($communication_fix['email'])>0)
  foreach ($communication_fix['email'] as $key=>$value) {$communication_fix['email'][$key]['ispr']=1;break;}

$found_ispr=false; foreach ($communication_fix['phone'] as $value) {if ($value['ispr']==1) {$found_ispr=true; break;}}
if ($found_ispr==false and count($communication_fix['phone'])>0)
  foreach ($communication_fix['phone'] as $key=>$value) {$communication_fix['phone'][$key]['ispr']=1;break;}

$found_ispr=false; foreach ($communication_fix['url'] as $value) {if ($value['ispr']==1) {$found_ispr=true; break;}}
if ($found_ispr==false and count($communication_fix['url'])>0)
  foreach ($communication_fix['url'] as $key=>$value) {$communication_fix['url'][$key]['ispr']=1;break;}

//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($communication_fix,true))); echo json_encode($return); die();




$user_email='';
foreach ($communication_fix['email'] as $key=>$value) if ($value['ispr']==1) {$user_email=$key; break;}

$user_mobile='';
$phone_home='';
foreach ($communication_fix['phone'] as $key=>$value) {
  if ($value['ispr']==1) {
    if (startwith($key,'69')) $user_mobile=$key;
    if (startwith($key,'2')) $phone_home=$key;
  }
}
foreach ($communication_fix['phone'] as $key=>$value) {
  if ($user_mobile=='' and startwith($key,'69')) $user_mobile=$key;
  if ($phone_home=='' and startwith($key,'2')) $phone_home=$key;
}

$user_url='';
foreach ($communication_fix['url'] as $key=>$value) if ($value['ispr']==1) {$user_url=$key; break;}



//$return = array('success' => false, 'message' => base64_encode('<pre>'.
//'user_email:'.$user_email."\n".
//'user_mobile:'.$user_mobile."\n".
//'phone_home:'.$phone_home."\n".
//'user_url:'.$user_url
//)); echo json_encode($return); die();

//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($communication_fix,true))); echo json_encode($return); die();




$user_login=trim_gks(stripslashes(urldecode($_POST['user_login'])));
$gks_sex=intval($_POST['gks_sex']);
$gks_lang=trim_gks($_POST['gks_lang']);

$oikogeniaki_katastasti_id=intval($_POST['oikogeniaki_katastasti_id']);
$oikogeniaki_katastasti_paidia =-1; if ($_POST['oikogeniaki_katastasti_paidia'] != '') $oikogeniaki_katastasti_paidia = intval($_POST['oikogeniaki_katastasti_paidia']);
$myfirst_name=trim_gks(base64_decode($_POST['myfirst_name']));
$mylast_name=trim_gks(base64_decode($_POST['mylast_name']));
$user_nicename=trim_gks(base64_decode($_POST['user_nicename']));
$display_name=trim_gks(base64_decode($_POST['display_name']));
$onoma_patera=trim_gks(base64_decode($_POST['onoma_patera']));
$onoma_miteras=trim_gks(base64_decode($_POST['onoma_miteras']));
$viber_id=base64_decode(trim_gks(stripslashes(urldecode($_POST['viber_id']))));
$user_pass_pure=trim_gks(base64_decode($_POST['user_pass_pure']));
$user_pin=trim_gks(base64_decode($_POST['user_pin']));
$fiscal_position_id=intval($_POST['fiscal_position_id']);
$pricelist_id=intval($_POST['pricelist_id']);
$generic_ekprosi=floatval($_POST['generic_ekprosi']);

$job_title=trim_gks(base64_decode($_POST['job_title']));
$eponimia=trim_gks(base64_decode($_POST['eponimia']));
$title=trim_gks(base64_decode($_POST['title']));
$afm=trim_gks(base64_decode($_POST['afm']));
$doy=trim_gks(base64_decode($_POST['doy']));
$epaggelma=trim_gks(base64_decode($_POST['epaggelma']));
$gemi_number=trim_gks(base64_decode($_POST['gemi_number']));
$is_b2g=intval($_POST['is_b2g']); if ($is_b2g!=1) $is_b2g=0;
$b2g_aaht_code=trim_gks(base64_decode($_POST['b2g_aaht_code']));
$b2g_aaht_name=trim_gks(base64_decode($_POST['b2g_aaht_name']));
$b2g_aaht_foreas=trim_gks(base64_decode($_POST['b2g_aaht_foreas']));
$b2g_aaht_typos_forea=trim_gks(base64_decode($_POST['b2g_aaht_typos_forea']));
$b2g_aaht_kodikos_ekatharisis=trim_gks(base64_decode($_POST['b2g_aaht_kodikos_ekatharisis']));
if ($is_b2g!=1) {
  $b2g_aaht_code='';
  $b2g_aaht_name='';
  $b2g_aaht_foreas='';
  $b2g_aaht_typos_forea='';
  $b2g_aaht_kodikos_ekatharisis='';
} else {
  if ($b2g_aaht_code=='')  {
    debug_mail(false,gks_lang('Εφόσον η επαφή είναι B2G θα πρέπει να υπάρχει τουλάχιστον ο Κωδικός ΑΑΗΤ'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον η επαφή είναι B2G θα πρέπει να υπάρχει τουλάχιστον ο Κωδικός ΑΑΗΤ')));
    echo json_encode($return); die();}    
    
  
  
}




$ma_branch=trim_gks(base64_decode($_POST['ma_branch']));
if ($ma_branch!='') $ma_branch=intval($ma_branch).'';

$ma_odos=trim_gks(base64_decode($_POST['ma_odos']));
$ma_arithmos=trim_gks(base64_decode($_POST['ma_arithmos']));
$ma_orofos=trim_gks(base64_decode($_POST['ma_orofos']));
$ma_perioxi=trim_gks(base64_decode($_POST['ma_perioxi']));
$ma_poli=trim_gks(base64_decode($_POST['ma_poli']));
$ma_tk=trim_gks(base64_decode($_POST['ma_tk']));
$ma_country_id=intval($_POST['ma_country_id']);
$ma_nomos_id=intval($_POST['ma_nomos_id']);

$ma_latitude=0; if (isset($_POST['ma_latitude'])) $ma_latitude=floatval(str_replace(',','.', $_POST['ma_latitude']));
$ma_longitude=0; if (isset($_POST['ma_longitude'])) $ma_longitude=floatval(str_replace(',','.', $_POST['ma_longitude']));


$order_sxolio=trim_gks(stripslashes(urldecode($_POST['order_sxolio'])));
$pelati_sxolio=trim_gks(stripslashes(urldecode($_POST['pelati_sxolio'])));

if ($generic_ekprosi<0) $generic_ekprosi=0;
if ($generic_ekprosi>99) $generic_ekprosi=99;



if (ur_ad() or ur_hr() or ur_lo()) {
	$old_code=trim_gks(stripslashes(urldecode($_POST['old_code'])));
	

	
}
if (ur_ad() or ur_hr() or ur_lo()) {
  $genisi_date=trim_gks(stripslashes(urldecode($_POST['genisi_date'])));
}

if (ur_ad() or ur_lo() or ur_hr()) {

  $description=trim_gks(stripslashes(urldecode($_POST['description'])));
  $ethnikotita=trim_gks(stripslashes(urldecode($_POST['ethnikotita'])));
  $alli_apasxolisi=trim_gks(stripslashes(urldecode($_POST['alli_apasxolisi'])));
  $cv_proipiresia=trim_gks(stripslashes(urldecode($_POST['cv_proipiresia'])));
  $cv_spoydes=trim_gks(stripslashes(urldecode($_POST['cv_spoydes'])));
  $cv_seminaria=trim_gks(stripslashes(urldecode($_POST['cv_seminaria'])));
  $cv_mitriki_glossa=trim_gks(stripslashes(urldecode($_POST['cv_mitriki_glossa'])));
  $cv_jenes_glosses=trim_gks(stripslashes(urldecode($_POST['cv_jenes_glosses'])));
  $cv_sxesi_me_photografia=trim_gks(stripslashes(urldecode($_POST['cv_sxesi_me_photografia'])));
  $cv_has_bike=intval($_POST['cv_has_bike']);
  $cv_has_motorcycle=intval($_POST['cv_has_motorcycle']);
  $cv_has_car=intval($_POST['cv_has_car']);
  $cv_has_car_theseis=intval($_POST['cv_has_car_theseis']);
  $cv_metaforiko_meso=trim_gks(stripslashes(urldecode($_POST['cv_metaforiko_meso'])));
  $sistasi_from=trim_gks(stripslashes(urldecode($_POST['sistasi_from'])));
  $days_to_work=trim_gks(stripslashes(urldecode($_POST['days_to_work'])));
}








//$phone_home=trim_gks(stripslashes(urldecode($_POST['phone_home'])));
$user_HumanInitial=trim_gks(stripslashes(urldecode($_POST['user_HumanInitial'])));


$arithmos_tautoitas=trim_gks(stripslashes(urldecode($_POST['arithmos_tautoitas'])));
$arxi_ekdosis=trim_gks(stripslashes(urldecode($_POST['arxi_ekdosis'])));
$amka=trim_gks(stripslashes(urldecode($_POST['amka'])));
$ama_eam=trim_gks(stripslashes(urldecode($_POST['ama_eam'])));

$form_newsletter_email=trim_gks(stripslashes(urldecode($_POST['form_newsletter_email'])));
$form_newsletter_sms=trim_gks(stripslashes(urldecode($_POST['form_newsletter_sms'])));

$form_user_photo=trim_gks(stripslashes(urldecode($_POST['form_user_photo'])));



if ($user_login=='') {debug_mail(false,'emptyl',                 'user_login can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα χρήστη δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

if ($user_nicename=='') {debug_mail(false,'emptyl',                 'user_nicename can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το υποκοριστικό δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

$sql="SELECT user_id FROM ".GKS_WP_TABLE_PREFIX."usermeta WHERE user_id<>".$id." AND meta_key='nickname' and meta_value='".$db_link->escape_string($user_nicename)."'";
$result = $db_link->query($sql); 
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
  echo json_encode($return); die();}
if ($result->num_rows>0) {
  debug_mail(false,'user_nicename error',str_replace('[1]',$user_nicename,gks_lang('Το υποκοριστικό <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλη επαφή')));
  $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$user_nicename,gks_lang('Το υποκοριστικό <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλη επαφή'))));
  echo json_encode($return); die(); }


//if ($user_email=='' and $user_mobile=='') {debug_mail(false,'emptyl',gks_lang('Θα πρέπει να καταχωρήστε τουλάχιστον το email ή το κινητό'));
//  $return = array('success' => false, 'message' =>   base64_encode(gks_lang('Θα πρέπει να καταχωρήστε τουλάχιστον το email ή το κινητό')));
//  echo json_encode($return); die(); }

if ($user_email != '' and !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,gks_lang('To email δεν είναι σωστό').' : '.$user_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  echo json_encode($return); die();}

//if ($user_mobile != '' and startwith($user_mobile,'00')==false and startwith($user_mobile,'+')==false and (strlen($user_mobile) != 10 or substr($user_mobile,0,2) != '69') ) {
//  debug_mail(false,gks_lang('To κινητό δεν είναι σωστό').' : '.$user_mobile);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To κινητό δεν είναι σωστό')));
//  echo json_encode($return); die();}  


//if ($phone_home != '' and (strlen($phone_home) != 10 or substr($phone_home,0,1) != '2') ) {
//  debug_mail(false,'the phone is not OK. : '.$phone_home);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό')));
//  echo json_encode($return); die();}  

//if ($afm != '' and CheckAFM($afm) == false) {debug_mail(false,'emptyl',          'afm is not OK'.$afm);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To ΑΦΜ δεν είναι έγκυρο')));
//  echo json_encode($return); die();}  

if ($fiscal_position_id<=0) {debug_mail(false,'emptyl',          'fiscal_position_id can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η φορολογική θέση δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }

if ($pricelist_id<=0) {debug_mail(false,'emptyl',                'pricelist_id can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο τιμοκατάλογος δεν μπορεί να είναι κενός')));
  echo json_encode($return); die(); }




$sql="select user_login from ".GKS_WP_TABLE_PREFIX."users where id<>".$id." and user_login like '".$db_link->escape_string($user_login)."'";
$result = $db_link->query($sql); 
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>0) {
  debug_mail(false,'user_login error',str_replace('[1]',$user_login,gks_lang('Το όνομα χρήστη <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλη επαφή')));
  $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$user_login,gks_lang('Το όνομα χρήστη <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλη επαφή'))));
  echo json_encode($return); die(); }

//if ($user_email!='') {
//  $sql="select user_email from ".GKS_WP_TABLE_PREFIX."users where id<>".$id." and user_email like '".$db_link->escape_string($user_email)."'";
//  $result = $db_link->query($sql); 
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die();}
//  if ($result->num_rows>0) {
//    debug_mail(false,'user_email error','the email <b>'.$user_email.'</b> exist in other user');
//    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$user_email,gks_lang('Το email <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλον χρήστη'))));
//    echo json_encode($return); die(); }
//}



//if ($user_mobile!='') {
//  $sql="SELECT meta_value FROM ".GKS_WP_TABLE_PREFIX."usermeta WHERE meta_key='mobile' AND user_id<>".$id." AND meta_value like '".$db_link->escape_string($user_mobile)."'";
//  $result = $db_link->query($sql); 
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die();}
//  if ($result->num_rows>0) {
//    debug_mail(false,'user_mobile error','the mobile <b>'.$user_mobile.'</b> exist in other user');
//    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$user_mobile,gks_lang('Το κινητό <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλον χρήστη'))));
//    echo json_encode($return); die(); }
//}

if ($user_HumanInitial!='') {
  if (strlen($user_HumanInitial) !=2) {
    debug_mail(false,'user_HumanInitial error','Employee Code <b>'.$user_HumanInitial.'</b> must is 2 latin characters.');
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$user_HumanInitial,gks_lang('Ο Κωδικός Υπαλλήλου <b>[1]</b> θα πρέπει να είναι 2 Λατινικοί χαρακτήρες'))));
    echo json_encode($return); die(); }
  if (strtolower($user_HumanInitial=='au')) {
    debug_mail(false,'user_HumanInitial error','Employee Code <b>'.$user_HumanInitial.'</b>is not avaliable, set other.');
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$user_HumanInitial,gks_lang('Ο Κωδικός Υπαλλήλου <b>[1]</b> δεν είναι διαθέσιμος, ορίστε κάποιον άλλο'))));
    echo json_encode($return); die(); }
    
    
  
  $sql="SELECT user_HumanInitial FROM gks_users WHERE user_id<>".$id." AND user_HumanInitial like '".$db_link->escape_string($user_HumanInitial)."'";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
    echo json_encode($return); die();}
  if ($result->num_rows>0) {
    debug_mail(false,'user_HumanInitial error','Employee Code <b>'.$user_HumanInitial.'</b> exist to oather user');
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$user_HumanInitial,gks_lang('Ο Κωδικός Υπαλλήλου <b>[1]</b> υπάρχει ήδη καταχωρημένος σε άλλη επαφή'))));
    echo json_encode($return); die(); }
}





if ($user_pass_pure!='' and mb_strlen($user_pass_pure)<5) {
  debug_mail(false,'user_pass_pure error','password is to small. set 5 chars or more.');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο κωδικός πρόσβασης είναι πολύ μικρός<br>Θα πρέπει να είναι τουλάχιστον 5 χαρακτήρες')));
  echo json_encode($return); die(); }  


if ((ur_ad() or ur_hr() or ur_lo()) and $old_code!='') {
  $sql="select old_code from ".GKS_WP_TABLE_PREFIX."users where id<>".$id." and old_code = '".$db_link->escape_string($old_code)."'";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
    echo json_encode($return); die();}
  if ($result->num_rows>0) {
    debug_mail(false,'old_code user error','OLD code is register to other user '.$old_code);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$old_code,gks_lang('Ο παλιός κωδικός <b>[1]</b> υπάρχει ήδη καταχωρημένος σε άλλη επαφή'))));
    echo json_encode($return); die(); }
}

if ($user_pin != '') {
  if (strlen($user_pin)!=4 or ctype_digit($user_pin)==false) {
    debug_mail(false,'user_pass_pure error','The PIN should be 4 numbers.');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το PIN θα πρέπει να είναι 4 αριθμοί')));
    echo json_encode($return); die(); }
  $sql="select * from ".GKS_WP_TABLE_PREFIX."users where user_pin='".$user_pin."' and ID<>".$id;
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
    echo json_encode($return); die();}
  if ($result->num_rows>0 or $user_pin=='0000' or $user_pin=='1111' or $user_pin=='2222' or $user_pin=='3333' or $user_pin=='4444' or $user_pin=='1234') {
    debug_mail(false,'user_pin error','PIN '.$user_pin.' is register to other user.');
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$user_pin,gks_lang('Το PIN <b>[1]</b> υπάρχει ήδη καταχωρημένο σε άλλη επαφή'))));
    echo json_encode($return); die(); }


}  


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'wp_users');


$redirect='';
if ($id==-1) {
  $randid=rand(10000,99999);
  $sql="insert into ".GKS_WP_TABLE_PREFIX."users (
  mydate_add,user_id_add,myip,
  user_login,display_name,user_nicename,gks_nickname,user_registered,update_from_gks,user_url
  ) values (
  now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  'newuser".$randid."','newuser".$randid."','newuser".$randid."','newuser".$randid."',NOW(),1,''
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'admin-users-item.php error sql',$sql);
    die('sql error');
  }  
  $id = $db_link->insert_id;
  
  $sql="insert into ".GKS_WP_TABLE_PREFIX."usermeta (user_id,meta_key,meta_value) values (".$id.",'nickname','newuser".$randid."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'admin-users-item.php error sql',$sql);
    die('sql error');
  }  
  
  $user_object = new WP_User($id);
  $user_object->add_role('subscriber');
  
 
  $redirect=base64_encode('admin-users-item.php?id='.$id); 
}

$sql="update ".GKS_WP_TABLE_PREFIX."users set ";
if ($user_pass_pure!='') {
  $sql.="user_pass='".$db_link->escape_string(wp_hash_password($user_pass_pure))."',";
  $sql.="user_pass_pure='".$db_link->escape_string($user_pass_pure)."',";
}
$sql.="
user_pin='".$db_link->escape_string($user_pin)."',
user_login='".$db_link->escape_string($user_login)."',
user_nicename='".$db_link->escape_string($user_login)."',
user_email='".$db_link->escape_string($user_email)."',
user_url='".$db_link->escape_string($user_url)."',
viber_id='".$db_link->escape_string($viber_id)."',
display_name='".$db_link->escape_string($display_name)."',
fiscal_position_id=".$fiscal_position_id.",
pricelist_id=".$pricelist_id.",
generic_ekprosi=".number_format($generic_ekprosi,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
user_activation_key='',
user_status=0,";
if (ur_ad() or ur_hr() or ur_lo()) {
$sql.=" 
old_code='".$db_link->escape_string($old_code)."',
";

}
$sql.="gks_sex=".$gks_sex.",
gks_lang='".$db_link->escape_string($gks_lang)."',

update_from_gks=1,
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'

where id=".$id." limit 1";
$result = $db_link->query($sql); 
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
  echo json_encode($return); die();}

//echo get_user_meta($id, 'nickname', true).'---'.$user_nicename;
//die();

update_user_meta( $id, 'nickname', $user_nicename);
update_user_meta( $id, 'first_name', $myfirst_name);
update_user_meta( $id, 'last_name', $mylast_name);
update_user_meta( $id, 'display_name', $display_name);
update_user_meta( $id, 'mobile', $user_mobile);

  
if (ur_ad() or ur_hr()) {
  
  update_user_meta( $id, 'description', $description);
}  

if ($form_user_photo != '') {
  if (substr($form_user_photo, 0,1) =='/') {
    //$form_user_photo = substr(GKS_SITE_URL, 0, strlen(GKS_SITE_URL)-1) . $form_user_photo;
  }
}
update_user_meta( $id, 'wsl_current_user_image', $form_user_photo);


if (ur_ad() or ur_hr() or ur_lo()) {
  $genisi_date_int=0;  
  if (strlen($genisi_date) >= 2 and substr($genisi_date,0,2) =='__') $genisi_date='';
  if ($genisi_date != '') {
    $limit_genisi_date=time() - 15*365*24*60*60; // tha prepei na einai toylaxiston 15 eton
    $genisi_date_int = gks_myFormatDate($genisi_date);
    if ($genisi_date_int > $limit_genisi_date) {
      debug_mail(false,'emptyl', 'genisi_date to small '. $genisi_date);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Ημερομηνία Γέννησης είναι πολύ μικρή')));
      echo json_encode($return); die(); 
    }
    $genisi_date = "'".date('Y-m-d',$genisi_date_int)."'";
  } else {
    $genisi_date = 'null';
  }
}

if ($arithmos_tautoitas != '' and strlen($arithmos_tautoitas)<=6 ) {
  debug_mail(false,'arithmos_tautoitas is not OK : '.$arithmos_tautoitas);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο Αριθμός Ταυτότητας δεν είναι έγκυρος')));
  echo json_encode($return); die();}  

if ($arxi_ekdosis != '' and strlen($arxi_ekdosis)<=7 ) {
  debug_mail(false,'arxi_ekdosis is not OK : '.$arxi_ekdosis);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('H Αρχή Έκδοσης δεν είναι έγκυρη')));
  echo json_encode($return); die();}  

if ($amka != '' and CheckAMKA($amka) == false) {
  debug_mail(false,'amka is not OK : '.$amka);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To ΑΜΚΑ δεν είναι έγκυρο')));
  echo json_encode($return); die();}  

//if ($amka!='' and $genisi_date_int>0 ) {
//  if (date('dmy',$genisi_date_int) != substr($amka, 0,6) and $id!=446) {
//    debug_mail(false,'amka and genisi_date_int is not sync: '.$amka.'--'.date('dmy',$genisi_date_int));
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το ΑΜΚΑ δεν συμβαδίζει με την ημερομηνία γέννησης')));
//    echo json_encode($return); die();
//  }
//}

if ($ama_eam != '' and (strlen($ama_eam)<=5 or ctype_digit($ama_eam) == false)) {
  debug_mail(false,'ama_eam is not OK : '.$ama_eam);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To ΑΜΑ - ΕΑΜ δεν είναι έγκυρο')));
  echo json_encode($return); die();}  



$sql="select user_id from gks_users where user_id=".$id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
  echo json_encode($return); die(); }
if ($result->num_rows==0) {
  $sql="insert into gks_users (user_id,mydate_add,user_id_add,myip) values (".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
  $result_gks_users = $db_link->query($sql);    
}



$sql="update gks_users set  
job_title = '".$db_link->escape_string($job_title)."',
eponimia = '".$db_link->escape_string($eponimia)."',
title = '".$db_link->escape_string($title)."',
afm = '".$db_link->escape_string($afm)."',
doy = '".$db_link->escape_string($doy)."',
epaggelma = '".$db_link->escape_string($epaggelma)."',
gemi_number = '".$db_link->escape_string($gemi_number)."',
is_b2g=".$is_b2g.",
b2g_aaht_code = '".$db_link->escape_string($b2g_aaht_code)."',
b2g_aaht_name = '".$db_link->escape_string($b2g_aaht_name)."',
b2g_aaht_foreas = '".$db_link->escape_string($b2g_aaht_foreas)."',
b2g_aaht_typos_forea = '".$db_link->escape_string($b2g_aaht_typos_forea)."',
b2g_aaht_kodikos_ekatharisis = '".$db_link->escape_string($b2g_aaht_kodikos_ekatharisis)."',
ma_branch = ".($ma_branch=='' ? 'null' : $ma_branch).",
ma_odos = '".$db_link->escape_string($ma_odos)."',
ma_arithmos = '".$db_link->escape_string($ma_arithmos)."',
ma_orofos = '".$db_link->escape_string($ma_orofos)."',
ma_perioxi = '".$db_link->escape_string($ma_perioxi)."',
ma_poli = '".$db_link->escape_string($ma_poli)."',
ma_tk = '".$db_link->escape_string($ma_tk)."',
ma_country_id = ".$ma_country_id.",
ma_nomos_id = ".$ma_nomos_id.",
ma_latitude='".number_format($ma_latitude,16,'.','')."',
ma_longitude='".number_format($ma_longitude,16,'.','')."',
phone_home = '".$db_link->escape_string($phone_home)."',
order_sxolio = '".$db_link->escape_string($order_sxolio)."',
pelati_sxolio = '".$db_link->escape_string($pelati_sxolio)."',";



if (ur_ad() or ur_hr() or ur_lo()) {
  $sql.=" genisi_date = ".$genisi_date.",";
}

if (ur_ad() or ur_lo() or ur_hr()) {
$sql.=" ethnikotita = '".$db_link->escape_string($ethnikotita)."',
alli_apasxolisi = '".$db_link->escape_string($alli_apasxolisi)."',
cv_proipiresia = '".$db_link->escape_string($cv_proipiresia)."',
cv_spoydes = '".$db_link->escape_string($cv_spoydes)."',
cv_seminaria = '".$db_link->escape_string($cv_seminaria)."',
cv_mitriki_glossa = '".$db_link->escape_string($cv_mitriki_glossa)."',
cv_jenes_glosses = '".$db_link->escape_string($cv_jenes_glosses)."',
cv_sxesi_me_photografia = '".$db_link->escape_string($cv_sxesi_me_photografia)."',
cv_has_bike = ".$cv_has_bike.",
cv_has_motorcycle = ".$cv_has_motorcycle.",
cv_has_car = ".$cv_has_car.",
cv_has_car_theseis = ".$cv_has_car_theseis.",
cv_metaforiko_meso = '".$db_link->escape_string($cv_metaforiko_meso)."',
sistasi_from = '".$db_link->escape_string($sistasi_from)."',";
$days_to_work_a=array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 0 => 0);
if (strlen($days_to_work) == 7) {
  if (substr($days_to_work, 0, 1) == '1') $days_to_work_a[1] = 1;
  if (substr($days_to_work, 1, 1) == '1') $days_to_work_a[2] = 1;
  if (substr($days_to_work, 2, 1) == '1') $days_to_work_a[3] = 1;
  if (substr($days_to_work, 3, 1) == '1') $days_to_work_a[4] = 1;
  if (substr($days_to_work, 4, 1) == '1') $days_to_work_a[5] = 1;
  if (substr($days_to_work, 5, 1) == '1') $days_to_work_a[6] = 1;
  if (substr($days_to_work, 6, 1) == '1') $days_to_work_a[0] = 1;
}
$days_to_work=serialize($days_to_work_a);
$sql.="days_to_work = '".$db_link->escape_string($days_to_work)."',";
}

$sql.=" arithmos_tautoitas = '".$db_link->escape_string($arithmos_tautoitas)."',
arxi_ekdosis = '".$db_link->escape_string($arxi_ekdosis)."',
amka = '".$db_link->escape_string($amka)."',
ama_eam = '".$db_link->escape_string($ama_eam)."',
user_HumanInitial = '".$db_link->escape_string($user_HumanInitial)."',
onoma_patera = '".$db_link->escape_string($onoma_patera)."',
onoma_miteras = '".$db_link->escape_string($onoma_miteras)."',
oikogeniaki_katastasti_id = ".$oikogeniaki_katastasti_id.",
oikogeniaki_katastasti_paidia = ".($oikogeniaki_katastasti_paidia < 0 ? 'null' : $oikogeniaki_katastasti_paidia).",
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'

where user_id=".$id." limit 1";
$result = $db_link->query($sql); 
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
  echo json_encode($return); die();}


$gks_wp_system_roles = gks_wp_system_roles_func();
$min_hierarchy_login_user=9999;
foreach ($my_wp_user_info->roles as $value) {
  if (isset($gks_wp_system_roles[$value]) and $gks_wp_system_roles[$value]['hierarchy'] < $min_hierarchy_login_user) 
    $min_hierarchy_login_user = $gks_wp_system_roles[$value]['hierarchy'];
}
if ($min_hierarchy_login_user==1) $min_hierarchy_login_user=0; //ean einai admin, na mporei na kanei admin

$user_roles_new=array();
foreach ($_POST as $key => $value) {
  if (startwith($key,'role_') and intval($value)!=0) {
    $role_id=substr($key, 5);
    if (isset($gks_wp_system_roles[$role_id]) and $gks_wp_system_roles[$role_id]['hierarchy'] > $min_hierarchy_login_user) {
      if ($role_id!='subscriber') {
        $user_roles_new[]=$role_id;
      }
    }
  }
}
if (count($user_roles_new)==0) $user_roles_new[]='subscriber';

$user_object = new WP_User($id);
$user_roles_exist=(array)$user_object->roles;

foreach ($gks_wp_system_roles as $role_item) {
  if ($role_item['hierarchy'] > $min_hierarchy_login_user) {
    if (in_array($role_item['id'],$user_roles_new)) {
      if (in_array($role_item['id'],$user_roles_exist) == false) $user_object->add_role($role_item['id']);
    } else {
      if (in_array($role_item['id'],$user_roles_exist)) $user_object->remove_role($role_item['id']);
    }
    
  }
} 


//$return = array('success' => false, 'message' => base64_encode('<pre>'.$min_hierarchy_login_user."\n".print_r($my_wp_user_info->roles,true).print_r($gks_wp_system_roles,true).print_r($user_roles_exist,true).print_r($user_roles_new,true)));
//echo json_encode($return); die();

//if ("6907231111--"=="6907231111") die('dddddddd');
 
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($communication_fix,true))); echo json_encode($return); die();

$sql_comm="select * from gks_users_communication where user_id=".$id." order by comm_primary desc";
$result_comm = $db_link->query($sql_comm);
if (!$result_comm) {debug_mail(false,'admin-users-item.php error sql',$sql_comm);die('sql error');}
$rows_comm=array();
while ($row_comm = $result_comm->fetch_assoc()) {
  $row_comm['for_delete']=true;
  $rows_comm[]=$row_comm;
}
//echo '<pre>';
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($rows_comm,true))); echo json_encode($return); die();
foreach ($communication_fix as $type=>&$items) {
  foreach ($items as $key => &$item) {
    $found=false;
    foreach ($rows_comm as &$row_comm) {
      
      if ($row_comm['comm_type'] == $type) {
        //echo '<pre>'.$row_comm['comm_value'].'|'.$key.'</pre>';
        if ('|'.$row_comm['comm_value'] == '|'.$key) {
          //echo '<pre>found</pre>';
          $row_comm['for_delete']=false;
          $found=true;
          if ($row_comm['comm_descr']!=$item['descr'] or $row_comm['comm_primary']!=$item['ispr']) {
            $sql_comm="update gks_users_communication set mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."',
            comm_descr='".$db_link->escape_string($item['descr'])."',comm_primary=".$item['ispr']."
            where id_user_communication=".$row_comm['id_user_communication'];
            $result_comm = $db_link->query($sql_comm);
            if (!$result_comm) {debug_mail(false,'admin-users-item.php error sql',$sql_comm);die('sql error');}
            
            //echo '<pre>'.$sql_comm.'</pre>';
          }
          break;
        }
        
      }
    }
    unset($row_comm);
    if ($found==false) {
      $sql_comm="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      user_id,comm_type,comm_value,comm_descr,comm_primary
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",'".$type."','".$db_link->escape_string($key)."','".$db_link->escape_string($item['descr'])."',".$item['ispr']."
      )";
      //echo '<pre>'.$sql_comm.'</pre>';
      $result_comm = $db_link->query($sql_comm);
      if (!$result_comm) {debug_mail(false,'admin-users-item.php error sql',$sql_comm);die('sql error');}
    }
  }
} 

//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($communication_fix,true))); echo json_encode($return); die();


$for_delete=array();
foreach ($rows_comm as $row_comm) if ($row_comm['for_delete']) $for_delete[]=$row_comm['id_user_communication'];
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($for_delete,true))); echo json_encode($return); die();
if (count($for_delete)>0) {
  $sql_comm="delete from gks_users_communication where id_user_communication in (".implode(',',$for_delete).")";
  $result_comm = $db_link->query($sql_comm);
  if (!$result_comm) {debug_mail(false,'admin-users-item.php error sql',$sql_comm);die('sql error');}
}
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($rows_comm,true))); echo json_encode($return); die();




//$return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'.print_r($user_roles,true)));
//echo json_encode($return); die();

//newsletter
$form_newsletter_email_array = array();
if ($form_newsletter_email !='') $form_newsletter_email_array = json_decode($form_newsletter_email, true);

$form_newsletter_sms_array = array();
if ($form_newsletter_sms !='') $form_newsletter_sms_array = json_decode($form_newsletter_sms, true);


//print '<pre>';
//print_r($form_newsletter_email_array);
//print $user_email;
//die();

foreach ($form_newsletter_email_array as $key => $value) {
  $newsletter_list_id= $value[0];
   
  if ($user_email!='') { 
    $isapproval = intval($value[1]) == 1 ? 1 : 0;
     
    $sql="insert into gks_newsletter_log (
    mydate,myip,user_id,mytype,mydata,newsletter_list_id,isapproval
    ) values (
    now(),
    '".$db_link->escape_string($gkIP)."',
    ".$id.",
    'email',
    '".$db_link->escape_string($user_email)."',
    ".$newsletter_list_id.",
    ".$isapproval."
    )";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
    echo json_encode($return); die();}
    $sql="select * from gks_newsletter_emails where myemail like '".$db_link->escape_string($user_email)."' and newsletter_list_id=".$newsletter_list_id;
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('sql error')));
      echo json_encode($return); die();}    
    if ($result->num_rows == 0) {   
      $sql="insert into gks_newsletter_emails (user_id,myemail,newsletter_list_id,isapproval) values (
      ".$id.",
      '".$db_link->escape_string($user_email)."',
      ".$newsletter_list_id.",
      ".$isapproval.")";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
    } else {
      $sql="update gks_newsletter_emails set isapproval=".$isapproval.",user_id=".$id."
      where myemail like '".$db_link->escape_string($user_email)."' 
      and newsletter_list_id=".$newsletter_list_id. " limit 1";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
    }
      
  }
  if ($user_mobile !='') {
    $isapproval = intval($form_newsletter_sms_array[$key][1]) == 1 ? 1 : 0;
    $sql="insert into gks_newsletter_log (
    mydate,myip,user_id,mytype,mydata,newsletter_list_id,isapproval
    ) values (
    now(),
    '".$db_link->escape_string($gkIP)."',
    ".$id.",
    'sms',
    '".$db_link->escape_string($user_mobile)."',
    ".$newsletter_list_id.",
    ".$isapproval."
    )";
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    $sql="select * from gks_newsletter_sms where mysms like '".$db_link->escape_string($user_mobile)."' and newsletter_list_id=".$newsletter_list_id;
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
    if ($result->num_rows == 0) {   
      $sql="insert into gks_newsletter_sms (user_id,mysms,newsletter_list_id,isapproval) values (
      ".$id.",
      '".$db_link->escape_string($user_mobile)."',
      ".$newsletter_list_id.",
      ".$isapproval.")";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
  } else {
      $sql="update gks_newsletter_sms set isapproval=".$isapproval.",user_id=".$id."
      where mysms like '".$db_link->escape_string($user_mobile)."' 
      and newsletter_list_id=".$newsletter_list_id. " limit 1";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}      
    }
      
    } else {
      $sql="update gks_newsletter_sms set isapproval=0
      where user_id=".$id;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
  }
} 
foreach ($_POST as $ff => $vvv) {
  if (startwith($ff,'iiucc')) {
    $id_user_cv=intval(str_replace('iiucc', '', $ff));
    if ($id_user_cv>0) {
      $sql="update gks_users_cv set show_on_user_profile = ".(intval($vvv) ==0 ? '0' : '1') ." where id_user_cv=".$id_user_cv;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}      
    }

    }
  if (startwith($ff,'iiucd')) {
    $id_user_cv=intval(str_replace('iiucd', '', $ff));
    if ($id_user_cv>0) {
      $sql="update gks_users_cv set file_descr = '".$db_link->escape_string($vvv)."' where id_user_cv=".$id_user_cv;
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}         
    }
  } 
  
  
} 

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

$ret_run=gks_sociallinks_item_save($_POST,'wp_users',$id);
if ($ret_run['success']==false) {die(json_encode(array('success'=>false,'message'=>base64_encode($ret_run['message']))));}


calc_profilepososto($id,$is_new_rec);

gks_cache_update_menu_version($id);

gks_plugins_functions_run('admin_users_item_exec_after',array(
  'user_id'=>&$id,
));



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();
