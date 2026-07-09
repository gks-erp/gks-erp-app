<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$aid=0; if (isset($_POST['aid'])) $aid=intval($_POST['aid']);

//echo '<pre>';
//var_dump($id); die();

if ($user_id<0) {
  debug_mail(false,'error on id (2):'.$user_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' user_id<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}
  
if (!($aid>0 or $aid==-1)) {
  debug_mail(false,'error on id (3):'.$aid);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' aid<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Λήψη διεύθυνσης');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_users_extra_address','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$out=array();

$data=array();
$data['ea_name']='';
$data['ea_phone']='';
$data['ea_branch']='';
$data['ea_odos']='';
$data['ea_arithmos']='';
$data['ea_orofos']='';
$data['ea_perioxi']='';
$data['ea_tk']='';
$data['ea_country_id']=0;
$data['ea_country_name']='';
$data['ea_nomos_id']=0;
$data['ea_nomos_descr']='';
$data['ea_latitude']=0;
$data['ea_longitude']=0;

if ($aid==-1) {
  $sql="SELECT gks_users.*, gks_country.country_name, gks_nomoi.nomos_descr
  FROM (gks_users 
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos
  WHERE gks_users.user_id=".$user_id." limit 1";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); 
  }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $data['ea_branch']=trim_gks($row['ma_branch']);
    $data['ea_odos']=trim_gks($row['ma_odos']);
    $data['ea_arithmos']=trim_gks($row['ma_arithmos']);
    $data['ea_orofos']=trim_gks($row['ma_orofos']);
    $data['ea_perioxi']=trim_gks($row['ma_perioxi']);
    $data['ea_poli']=trim_gks($row['ma_poli']);
    $data['ea_tk']=trim_gks($row['ma_tk']);
    $data['ea_country_id']=intval($row['ma_country_id']);
    $data['ea_country_name']=gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');
    $data['ea_nomos_id']=intval($row['ma_nomos_id']);
    $data['ea_nomos_descr']=gks_lang_data_trans($row['nomos_descr'],$row['ma_nomos_id'],'gks_nomoi','nomos_descr');
    $data['ea_latitude']=floatval($row['ma_latitude']);
    $data['ea_longitude']=floatval($row['ma_longitude']);
    
  } 
  
} else { 
  $sql="SELECT gks_users_extra_address.*, gks_country.country_name, gks_nomoi.nomos_descr
  FROM (gks_users_extra_address 
  LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
  WHERE gks_users_extra_address.user_id=".$user_id." and id_users_extra_address=".$aid." limit 1";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); 
  }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $data['ea_name']=trim_gks($row['ea_name']);
    $data['ea_phone']=trim_gks($row['ea_phone']);
    $data['ea_branch']=trim_gks($row['ea_branch']);
    $data['ea_odos']=trim_gks($row['ea_odos']);
    $data['ea_arithmos']=trim_gks($row['ea_arithmos']);
    $data['ea_orofos']=trim_gks($row['ea_orofos']);
    $data['ea_perioxi']=trim_gks($row['ea_perioxi']);
    $data['ea_poli']=trim_gks($row['ea_poli']);
    $data['ea_tk']=trim_gks($row['ea_tk']);
    $data['ea_country_id']=intval($row['ea_country_id']);
    $data['ea_country_name']=gks_lang_data_trans($row['country_name'],$row['ea_country_id'],'gks_country','country_name');
    $data['ea_nomos_id']=intval($row['ea_nomos_id']);
    $data['ea_nomos_descr']=gks_lang_data_trans($row['nomos_descr'],$row['ea_nomos_id'],'gks_nomoi','nomos_descr');
    $data['ea_latitude']=floatval($row['ea_latitude']);
    $data['ea_longitude']=floatval($row['ea_longitude']);
  } 
}

 
$return = array('success' => true, 'message' => base64_encode('ok'), 'data' => $data);
echo json_encode($return); die();  
