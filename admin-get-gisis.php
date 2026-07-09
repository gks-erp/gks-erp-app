<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Λήψη δεδομένων επαφής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_gsis_check','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$afm=''; if (isset($_POST['afm'])) $afm=trim_gks($_POST['afm']);
if ($afm=='') {
  debug_mail(false,'afm is  empty','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Πληκτρολογήστε το ΑΦΜ').'<br>'.gks_lang('Θα πρέπει να είναι αριθμός')));
  echo json_encode($return); die();
}

$company_id=0; if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$force=false; if (isset($_POST['force'])) $force=(intval($_POST['force'])==1);




$out = CheckAFM_GSIS($afm,$company_id,$force);
$out_error='';
if (isset($out['error']) and trim_gks($out['error'])!='') $out_error=trim_gks($out['error']);
if ($out_error=='' and isset($out['error_rec']) and isset($out['error_rec']['error_descr']) and trim_gks($out['error_rec']['error_descr'])!='') {
  $out_error=trim_gks($out['error_rec']['error_descr']);
}
if ($out_error != '' and isset($out['basic_rec']['firm_flag_descr'])==false ) {
  $return = array('success' => false, 'message' => base64_encode($out_error),'out' => $out);
  echo json_encode($return); die();
} else {
  $out['user_id']=0;
  $out['gks_nickname']='';
  
  $sql="SELECT gks_users.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  FROM gks_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_users.afm='".$db_link->escape_string($afm)."' AND gks_users.ma_country_id=91 AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null";

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
  
  $return = array('success' => true, 'message' => base64_encode('OK'),'out' => $out);
  echo json_encode($return); die();
}
$return = array('success' => false, 'message' => base64_encode('test'),'out' => $out);
echo json_encode($return); die();
