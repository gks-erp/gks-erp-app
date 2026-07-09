<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Λήψη VIES');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_vies_check','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$afm=''; if (isset($_POST['afm'])) $afm=trim_gks($_POST['afm']);
if ($afm=='') {
  debug_mail(false,'afm is  empty','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε το ΑΦΜ').'<br>'.gks_lang('Θα πρέπει να είναι αριθμός')));
  echo json_encode($return); die();
}

$country_ee=''; if (isset($_POST['country_ee'])) $country_ee=trim_gks($_POST['country_ee']);
$force=false; if (isset($_POST['force'])) $force=(intval($_POST['force'])==1);




$out = CheckAFM_VIES($country_ee,$afm,$force);
$out_error='';
if (isset($out['error']) and trim_gks($out['error'])!='') $out_error=trim_gks($out['error']);
if ($out_error=='' and isset($out['error_rec']) and isset($out['error_rec']['error_descr']) and trim_gks($out['error_rec']['error_descr'])!='') {
  $out_error=trim_gks($out['error_rec']['error_descr']);
}
if ($out_error != '') {
  $return = array('success' => false, 'message' => base64_encode($out_error),'out' => $out);
  echo json_encode($return); die();
} else {
  $out['user_id']=0;
  $out['gks_nickname']='';
  
  $sql="SELECT gks_users.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  FROM (gks_users 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country
  WHERE gks_users.afm='".$db_link->escape_string($afm)."' 
  AND gks_country.country_ee like '".$db_link->escape_string($country_ee)."' 
  AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }    
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $out['user_id']=intval($row['user_id']);
    $out['gks_nickname']=$row['gks_nickname'];
  }
  
  
  $out['id_country']=0;
  $out['country_name']='';
  
  $sql="select id_country,country_name from gks_country where country_ee like '".$db_link->escape_string($country_ee)."'";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }    
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $out['id_country']=intval($row['id_country']);
    $out['country_name']=$row['country_name'];
  }
  
  
  $return = array('success' => true, 'message' => base64_encode('OK'),'out' => $out);
  echo json_encode($return); die();
}
$return = array('success' => false, 'message' => base64_encode('test'),'out' => $out);
echo json_encode($return); die();
