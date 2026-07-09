<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_cmd_checkout_edit($id_hotel,$row_hotel,$input_data) {
  global $db_link;
  global $gks_cache_version;
  global $_gks_session;
  global $_gks_id_session;
  global $gks_user_settings;
  
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW;
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;
  
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  
  $gks_erp_cookie_id='';
  if(isset($input_data['gks_erp_cookie_id'])) {
    $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
  }
  //print '<pre>|'.$gks_erp_cookie_id.'|';
  $hotel_title=$row_hotel['hotel_title'];
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  gks_erp_cookie_start($gks_erp_cookie_id);
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  if (isset($input_data['post']['ui_lang']) and trim_gks($input_data['post']['ui_lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['post']['ui_lang']);
  }
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  $defs = get_def_check($id_hotel);
  $hotel_params=gks_hotel_get_params($id_hotel);

  
  $my_wp_user_id=0;

  $_POST=$input_data['post'];

$_gks_session['gks']['basket']['parastatiko'] = intval($_POST['form_parastatiko']);
$_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservation_other'] = intval($_POST['form_reservation_other']);


$_gks_session['gks']['basket']['user']['first_name'] = trim_gks(stripslashes(urldecode($_POST['dr_user_first_name'])));
$_gks_session['gks']['basket']['user']['last_name'] = trim_gks(stripslashes(urldecode($_POST['dr_user_last_name'])));
$_gks_session['gks']['basket']['user']['email'] = trim_gks(stripslashes(urldecode($_POST['dr_user_email'])));
$_gks_session['gks']['basket']['user']['mobile'] = trim_gks(stripslashes(urldecode($_POST['dr_user_mobile'])));
$_gks_session['gks']['basket']['user']['lang'] = trim_gks(stripslashes(urldecode($_POST['dr_user_lang'])));
$_gks_session['gks']['basket']['user']['ma_odos'] = trim_gks(stripslashes(urldecode($_POST['dr_user_ma_odos'])));
$_gks_session['gks']['basket']['user']['ma_orofos'] = trim_gks(stripslashes(urldecode($_POST['dr_user_ma_orofos'])));
$_gks_session['gks']['basket']['user']['ma_perioxi'] = trim_gks(stripslashes(urldecode($_POST['dr_user_ma_perioxi'])));
$_gks_session['gks']['basket']['user']['ma_poli'] = trim_gks(stripslashes(urldecode($_POST['dr_user_ma_poli'])));
$_gks_session['gks']['basket']['user']['ma_tk'] = trim_gks(stripslashes(urldecode($_POST['dr_user_ma_tk'])));
$_gks_session['gks']['basket']['user']['ma_country_id'] = intval($_POST['dr_user_ma_country_id']);
$_gks_session['gks']['basket']['user']['ma_nomos_id'] = intval($_POST['dr_user_ma_nomos_id']);

if ($_gks_session['gks']['basket']['parastatiko'] == 0) {
  $_gks_session['gks']['basket']['user']['eponimia'] = '';
  $_gks_session['gks']['basket']['user']['title'] = '';
  $_gks_session['gks']['basket']['user']['afm'] = '';
  $_gks_session['gks']['basket']['user']['doy'] = '';
  $_gks_session['gks']['basket']['user']['epaggelma'] = '';
} else {
  $_gks_session['gks']['basket']['user']['eponimia'] = trim_gks(stripslashes(urldecode($_POST['dr_user_eponimia'])));
  $_gks_session['gks']['basket']['user']['title'] = trim_gks(stripslashes(urldecode($_POST['dr_user_title'])));
  $_gks_session['gks']['basket']['user']['afm'] = trim_gks(stripslashes(urldecode($_POST['dr_user_afm'])));
  $_gks_session['gks']['basket']['user']['doy'] = trim_gks(stripslashes(urldecode($_POST['dr_user_doy'])));
  $_gks_session['gks']['basket']['user']['epaggelma'] = trim_gks(stripslashes(urldecode($_POST['dr_user_epaggelma'])));
}

if ($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservation_other'] == 0) {
  $_gks_session['gks']['basket']['user_other']['first_name'] = '';
  $_gks_session['gks']['basket']['user_other']['last_name'] = '';
  $_gks_session['gks']['basket']['user_other']['email'] = '';
  $_gks_session['gks']['basket']['user_other']['mobile'] = '';
  $_gks_session['gks']['basket']['user_other']['lang'] = '';
  $_gks_session['gks']['basket']['user_other']['ma_odos'] = '';
  $_gks_session['gks']['basket']['user_other']['ma_orofos'] = '';
  $_gks_session['gks']['basket']['user_other']['ma_perioxi'] = '';
  $_gks_session['gks']['basket']['user_other']['ma_poli'] = '';
  $_gks_session['gks']['basket']['user_other']['ma_tk'] = '';
  $_gks_session['gks']['basket']['user_other']['ma_country_id'] = 0;
  $_gks_session['gks']['basket']['user_other']['ma_nomos_id'] = 0;
} else {
  $_gks_session['gks']['basket']['user_other']['first_name'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_first_name'])));
  $_gks_session['gks']['basket']['user_other']['last_name'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_last_name'])));
  $_gks_session['gks']['basket']['user_other']['email'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_email'])));
  $_gks_session['gks']['basket']['user_other']['mobile'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_mobile'])));
  $_gks_session['gks']['basket']['user_other']['lang'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_lang'])));
  $_gks_session['gks']['basket']['user_other']['ma_odos'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_ma_odos'])));
  $_gks_session['gks']['basket']['user_other']['ma_orofos'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_ma_orofos'])));
  $_gks_session['gks']['basket']['user_other']['ma_perioxi'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_ma_perioxi'])));
  $_gks_session['gks']['basket']['user_other']['ma_poli'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_ma_poli'])));
  $_gks_session['gks']['basket']['user_other']['ma_tk'] = trim_gks(stripslashes(urldecode($_POST['other_dr_user_ma_tk'])));
  $_gks_session['gks']['basket']['user_other']['ma_country_id'] = intval($_POST['other_dr_user_ma_country_id']);
  $_gks_session['gks']['basket']['user_other']['ma_nomos_id'] = intval($_POST['other_dr_user_ma_nomos_id']);
}





$_gks_session['gks']['basket']['address_extra'] = intval($_POST['form_select_apostoli']);
$_gks_session['gks']['basket']['destination_data']=array();
$_gks_session['gks']['basket']['destination_data']['name'] = trim_gks(stripslashes(urldecode($_POST['form_ea_name'])));
$_gks_session['gks']['basket']['destination_data']['phone'] = trim_gks(stripslashes(urldecode($_POST['form_ea_phone'])));
$_gks_session['gks']['basket']['destination_data']['odos'] = trim_gks(stripslashes(urldecode($_POST['form_ea_odos'])));
$_gks_session['gks']['basket']['destination_data']['orofos'] = trim_gks(stripslashes(urldecode($_POST['form_ea_orofos'])));
$_gks_session['gks']['basket']['destination_data']['perioxi'] = trim_gks(stripslashes(urldecode($_POST['form_ea_perioxi'])));
$_gks_session['gks']['basket']['destination_data']['poli'] = trim_gks(stripslashes(urldecode($_POST['form_ea_poli'])));
$_gks_session['gks']['basket']['destination_data']['tk'] = trim_gks(stripslashes(urldecode($_POST['form_ea_tk'])));
$_gks_session['gks']['basket']['destination_data']['country_id'] =  intval($_POST['form_ea_country_id']);
$_gks_session['gks']['basket']['destination_data']['nomos_id'] =  intval($_POST['form_ea_nomos_id']);

$showloading=intval($_POST['showloading']);
$showerrors=intval($_POST['showerrors']);
$gonext=intval($_POST['gonext']);



$hrb_user = $_gks_session['gks']['basket']['user'];
$hrb_user_other = $_gks_session['gks']['basket']['user_other'];

$myproducts = gks_basket_recalc($_gks_session['gks']['basket'], array(), array());


$errors_out=array();

if ($hrb_user['first_name']=='') {
  //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'first_name is empty');
  $errors_out[]=gks_lang('Πληκτρολογήστε το όνομά σας');
  
  
}

if ($hrb_user['last_name']=='') {
  //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'last_name is empty');
  $errors_out[]=gks_lang('Πληκτρολογήστε το επώνυμό σας');
  
  
}

if ($hrb_user['email']=='') {
  //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'email is empty');
  $errors_out[]=gks_lang('Πληκτρολογήστε το email σας');
  //$return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε το email σας')));
  
}


if ($hrb_user['email'] != '' and !filter_var($hrb_user['email'], FILTER_VALIDATE_EMAIL)) {
  $_gks_session['gks']['basket']['user']['email']='';
  //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'email is not ok: '.$hrb_user['email']);
  $errors_out[]=str_replace('[1]', $hrb_user['email'], gks_lang('To email [1] δεν είναι σωστό'));
  //$return = array('success' => false, 'message' => base64_encode(str_replace('[1]', $hrb_user['email'], gks_lang('To email [1] δεν είναι σωστό'))));
  
}
  
//if ($hrb_user['mobile']=='') {
//  //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'mobile is empty');
//  $errors_out[]=str_replace('[1]', $hrb_user['email'], gks_lang('Πληκτρολογήστε το κινητό σας'));
//  //$return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε το κινητό σας')));
//  
//}  
  
if ($hrb_user['mobile'] != '' and $hrb_user['ma_country_id'] == 91 and (strlen($hrb_user['mobile']) != 10 or (substr($hrb_user['mobile'],0,2) != '69' and substr($hrb_user['mobile'],0,1) != '2'))) {
  $_gks_session['gks']['basket']['user']['mobile']='';
  //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'mobile is not ok: '.$hrb_user['mobile']);
  $errors_out[]=str_replace('[1]', $hrb_user['mobile'], gks_lang('To κινητό <b>[1]</b> δεν είναι σωστό'));
  //$return = array('success' => false, 'message' => base64_encode(str_replace('[1]', $hrb_user['mobile'], gks_lang('To κινητό <b>[1]</b> δεν είναι σωστό'))));
  
}  


if ($hrb_user['lang']=='') {
  //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'lang is empty');
  $errors_out[]=str_replace('[1]', $hrb_user['email'], gks_lang('Επιλέξτε την γλώσσα επικοινωνίας'));
  //$return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την γλώσσα επικοινωνίας')));
  
}  

if ($_gks_session['gks']['basket']['products_need_apostoli']) {
  if ($hrb_user['ma_odos']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_odos is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε την διεύθυνσή σας');
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε την διεύθυνσή σας')));
    
  }  
  if ($hrb_user['ma_perioxi']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_perioxi is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε την περιοχή σας');
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε την περιοχή σας')));
    
  }  
  if ($hrb_user['ma_poli']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_poli is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε την πόλη σας');
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε την πόλη σας')));
    
  }  
  if ($hrb_user['ma_tk']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_tk is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε τον ΤΚ σας');
  }  
  
}

if ($hrb_user['ma_country_id']<=0) {
  //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_country_id is empty');
  $errors_out[]=gks_lang('Επιλέξτε την χώρα σας');
  
} 

$sql="select * FROM gks_nomoi where country_id=".$hrb_user['ma_country_id'];
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'sql error',$sql);  $return = array('success' => true, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'))); return $return;}
if ($result->num_rows >0) {
  if ($hrb_user['ma_nomos_id']<=0 and $_gks_session['gks']['basket']['products_need_apostoli']) {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_nomos_id is empty');
    $errors_out[]=gks_lang('Επιλέξτε τoν νομό σας');
    
  }   
}


if ($_gks_session['gks']['basket']['parastatiko'] != 0) {
  if ($hrb_user['eponimia']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'eponimia is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε την Επωνυμία της εταιρείας σας');
  }

  if ($hrb_user['afm']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'afm is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε το ΑΦΜ της εταιρείας σας');
  }

  if ($hrb_user['ma_country_id'] == 91) {
    if ($hrb_user['doy']=='') {
      //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_tk is empty');
      $errors_out[]=gks_lang('Πληκτρολογήστε την ΔΟΥ σας');
    }     
    if ($hrb_user['epaggelma']=='') {
      //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_tk is empty');
      $errors_out[]=gks_lang('Πληκτρολογήστε την επιχειρηματική σας δραστηριότητα');
    }    
//    $res_vies = CheckAFM_GSIS($hrb_user['afm']);
//    if ($res_vies['valid'] == false) {
//      //$_gks_session['gks']['basket']['user']['afm']='';
//      $errors_out[]=str_replace('[1]', $hrb_user['afm'], gks_lang('To ΑΦΜ <b>[1]</b> δεν είναι έγκυρο'));
//    }
// 
//    if (CheckAFM($hrb_user['afm']) == false) {
//      //$_gks_session['gks']['basket']['user']['afm']='';
//      $errors_out[]=str_replace('[1]', $hrb_user['afm'], gks_lang('To ΑΦΜ <b>[1]</b> δεν είναι έγκυρο'));
//    }
  }
  
}

if ($_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservation_other'] != 0) {
  
  if ($hrb_user_other['first_name']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'first_name is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε το όνομα που αφορά την κράτηση');

    
  }
  
  if ($hrb_user_other['last_name']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'last_name is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε το επώνυμο που αφορά την κράτηση');
    
    
  }
  
  if ($hrb_user_other['email']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'email is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε το email που αφορά την κράτηση');
    
    
  }
  
  
  if ($hrb_user_other['email'] != '' and !filter_var($hrb_user_other['email'], FILTER_VALIDATE_EMAIL)) {
    $_gks_session['gks']['basket']['user']['email']='';
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'email is not ok: '.$hrb_user_other['email']);
    $errors_out[]=str_replace('[1]', $hrb_user_other['email'], gks_lang('To email <b>[1]</b> που αφορά την κράτηση δεν είναι σωστό'));
    //$return = array('success' => false, 'message' => base64_encode(str_replace('[1]', $hrb_user_other['email'], gks_lang('To email <b>[1]</b> που αφορά την κράτηση δεν είναι σωστό'))));
    
  }
    
  if ($hrb_user_other['mobile']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'mobile is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε το κινητό που αφορά την κράτηση');
    
  }  
    
  if ($hrb_user_other['mobile'] != '' and $hrb_user_other['ma_country_id'] == 91 and (strlen($hrb_user_other['mobile']) != 10 or (substr($hrb_user_other['mobile'],0,2) != '69' and substr($hrb_user_other['mobile'],0,1) != '2'))) {
    $_gks_session['gks']['basket']['user']['mobile']='';
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'mobile is not ok '.$hrb_user_other['mobile'].' for reservation');
    $errors_out[]=str_replace('[1]', $hrb_user_other['mobile'], gks_lang('To κινητό <b>[1]</b> που αφορά την κράτηση δεν είναι σωστό'));
    //$return = array('success' => false, 'message' => base64_encode(str_replace('[1]', $hrb_user_other['mobile'], gks_lang('To κινητό <b>[1]</b> που αφορά την κράτηση δεν είναι σωστό'))));
    
  }  
  
  
  if ($hrb_user_other['lang']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'lang is empty');
    $errors_out[]=gks_lang('Επιλέξτε την γλώσσα επικοινωνίας που αφορά την κράτηση');
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την γλώσσα επικοινωνίας που αφορά την κράτηση')));
    
  }  
  
  
  
  if ($hrb_user_other['ma_country_id']<=0) {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_country_id is empty');
    $errors_out[]=gks_lang('Επιλέξτε την χώρα που αφορά την κράτηση');
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την χώρα που αφορά την κράτηση')));
    
  } 
  
  $sql="select * FROM gks_nomoi where country_id=".$hrb_user_other['ma_country_id'];
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'sql error',$sql);  $return = array('success' => true, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));return $return;}
  if ($result->num_rows >0) {
    if ($hrb_user_other['ma_nomos_id']<=0) {
      //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ma_nomos_id is empty');
      $errors_out[]=gks_lang('Επιλέξτε τoν νομό που αφορά την κράτηση');
      //$return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τoν νομό που αφορά την κράτηση')));
      
    }   
  }
 
}  

if ($_gks_session['gks']['basket']['address_extra']==0) {
  if ($_gks_session['gks']['basket']['destination_data']['name']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'first_name is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε το όνομα παραλήπτη που αφορά την αποστολή');
  }
  
  if ($_gks_session['gks']['basket']['destination_data']['phone'] != '' and 
      $_gks_session['gks']['basket']['destination_data']['country_id'] == 91 and 
      (strlen($_gks_session['gks']['basket']['destination_data']['phone']) != 10 or 
        (substr($_gks_session['gks']['basket']['destination_data']['phone'],0,2) != '69' and substr($_gks_session['gks']['basket']['destination_data']['phone'],0,1) != '2'))) {
    $_gks_session['gks']['basket']['destination_data']['phone']='';
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'phoe is not ok: '.$_gks_session['gks']['basket']['destination_data']['phone']);
    $errors_out[]=str_replace('[1]', $_gks_session['gks']['basket']['destination_data']['phone'], gks_lang('To τηλέφωνο <b>[1]</b> που αφορά την αποστολή δεν είναι σωστό'));
    //$return = array('success' => false, 'message' => base64_encode(str_replace('[1]', $_gks_session['gks']['basket']['destination_data']['phone'], gks_lang('To τηλέφωνο <b>[1]</b> που αφορά την αποστολή δεν είναι σωστό'))));
    
  }
  
  if ($_gks_session['gks']['basket']['destination_data']['odos']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ea_odos is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε την διεύθυνση που αφορά την αποστολή');
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε την διεύθυνση που αφορά την αποστολή')));
    
  }  
  if ($_gks_session['gks']['basket']['destination_data']['perioxi']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ea_perioxi is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε την περιοχή που αφορά την αποστολή');
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε την περιοχή που αφορά την αποστολή')));
    
  }  
  if ($_gks_session['gks']['basket']['destination_data']['poli']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ea_poli is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε την πόλη που αφορά την αποστολή');
    
    
  }  
  if ($_gks_session['gks']['basket']['destination_data']['tk']=='') {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'ea_tk is empty');
    $errors_out[]=gks_lang('Πληκτρολογήστε τον ΤΚ που αφορά την αποστολή');
  }  
  

  
  if ($_gks_session['gks']['basket']['destination_data']['country_id']<=0) {
    //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'country_id is empty');
    $errors_out[]=gks_lang('Επιλέξτε την χώρα που αφορά την αποστολή');
    //$return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την χώρα που αφορά την αποστολή')));
    
  } 
  
  $sql="select * FROM gks_nomoi where country_id=".$_gks_session['gks']['basket']['destination_data']['country_id'];
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'sql error',$sql);  $return = array('success' => true, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));return $return;}
  if ($result->num_rows >0) {
    if ($_gks_session['gks']['basket']['destination_data']['nomos_id']<=0) {
      //if ($gonext!=0 or $showerrors!=0) debug_mail(false,'nomos_id is empty');
      $errors_out[]=gks_lang('Επιλέξτε τoν νομό που αφορά την αποστολή');
      //$return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τoν νομό που αφορά την αποστολή')));
      
    }   
  }
}

  
//if ($hrb_user_other['email'] != '' and !filter_var($hrb_user_other['email'], FILTER_VALIDATE_EMAIL)) {
//  $_gks_session['gks']['basket']['user_other']['email']='';
//  if ($gonext!=0 or $showerrors!=0) debug_mail(false,'email is not ok: '.$hrb_user_other['email']);
//  $return = array('success' => false, 'message' => base64_encode( str_replace('[1]', $hrb_user_other['email'], gks_lang('To email [1] δεν είναι σωστό'))));
//  }
//  
//
//  
//if ($hrb_user_other['mobile'] != '' and $hrb_user_other['ma_country_id'] == 91 and (strlen($hrb_user_other['mobile']) != 10 or (substr($hrb_user_other['mobile'],0,2) != '69' and substr($hrb_user_other['mobile'],0,1) != '2'))) {
//  $_gks_session['gks']['basket']['user_other']['mobile']='';
//  if ($gonext!=0 or $showerrors!=0) debug_mail(false,'mobile is not ok: '.$hrb_user_other['mobile']);
//  $return = array('success' => false, 'message' => base64_encode(str_replace('[1]', $hrb_user_other['mobile'], gks_lang('To κινητό <b>[1]</b> δεν είναι σωστό'));
//  }





$out=array();
$pliroteo=$_gks_session['gks']['basket']['products_total'] + $_gks_session['gks']['basket']['kostos_apostolis'] + $_gks_session['gks']['basket']['kostos_pliromis'];
$ids_hide=array();
$ids_show=array();

if ($_gks_session['gks']['basket']['products_fpa']==0) $ids_hide[]='#tr_basket_products_fpa'; else $ids_show[]= '#tr_basket_products_fpa';
if ($_gks_session['gks']['basket']['kostos_apostolis']==0) $ids_hide[]='#tr_basket_kostos_apostolis'; else $ids_show[]= '#tr_basket_kostos_apostolis';
if ($_gks_session['gks']['basket']['kostos_pliromis']==0) $ids_hide[]='#tr_basket_kostos_pliromis'; else $ids_show[]= '#tr_basket_kostos_pliromis';
if ($_gks_session['gks']['basket']['products_netvalue']==$pliroteo) $ids_hide[]='#tr_basket_products_netvalue'; else $ids_show[]= '#tr_basket_products_netvalue';


$views_run_img='';
if ($_gks_session['gks']['basket']['check_vies']['run']) {
  if ($_gks_session['gks']['basket']['check_vies']['valid']) $views_run_img='<img src="/my/img/1.png" style="width:24px;" title="'.
  ($_gks_session['gks']['basket']['check_vies']['function']=='CheckAFM_VIES' ? gks_lang('EU VIES Check: VAT number is valid') : gks_lang('Έλεγχος ΑΦΜ μέσω του gsis.gr: Είναι έκγυρο')).
  '" class="tooltipster">';
  else {
    if ($_gks_session['gks']['basket']['check_vies']['error']=='') $views_run_img='<img src="/my/img/0.png" style="width:24px;" title="'.
      ($_gks_session['gks']['basket']['check_vies']['function']=='CheckAFM_VIES' ? gks_lang('EU VIES Check: VAT number is not valid') : gks_lang('Έλεγχος ΑΦΜ μέσω του gsis.gr: Δεν είναι έγκυρο')).
      '" class="tooltipster">';
    else $views_run_img='<img src="/my/img/warning.gif" style="width:24px;" title="'.
      ($_gks_session['gks']['basket']['check_vies']['function']=='CheckAFM_VIES' ? 'EU VIES Check error: ' . $_gks_session['gks']['basket']['check_vies']['error'] : gks_lang('Σφάλμα κατά τον έλεγχο του ΑΦΜ μέσω του gsis.gr').': '.$_gks_session['gks']['basket']['check_vies']['error']).
      '" class="tooltipster">';
  }
}


$message='OK';
if (count($errors_out)>0) {
  $message=implode('<br>', $errors_out);
  if ($gonext!=0 or $showerrors!=0) debug_mail(false,'checkout',$message);
}

$return = array('success' => (count($errors_out)>0 ? false : true), 
  'message' => base64_encode($message),
  'products_posotita' => base64_encode(myNumberFormat($_gks_session['gks']['basket']['products_posotita'],0)),
  'products_netvalue' => base64_encode(myCurrencyFormat($_gks_session['gks']['basket']['products_netvalue'],true,true)),
  'products_fpa'      => base64_encode(myCurrencyFormat($_gks_session['gks']['basket']['products_fpa'],true,true)),
  'kostos_apostolis'  => base64_encode(myCurrencyFormat($_gks_session['gks']['basket']['kostos_apostolis'],true,true)),
  'kostos_pliromis'   => base64_encode(myCurrencyFormat($_gks_session['gks']['basket']['kostos_pliromis'],true,true)),
  'products_total'    => base64_encode(myCurrencyFormat($pliroteo ,true,true)),
  'products_total_val'    => $pliroteo,
  'out' => $out,
  'ids_hide' => $ids_hide,
  'ids_show' => $ids_show,
  'check_vies' => $_gks_session['gks']['basket']['check_vies'],
  'views_run_img' => $views_run_img,
  'gggggggggggg' => $gks_erp_cookie_id,
  'ggggggg1' => $_gks_session['gks']['basket']['user']['first_name'],
);


gks_erp_cookie_save($gks_erp_cookie_id);

return $return;
  
}

