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
$my_page_title=gks_lang('Αποθήκευση υποκαταστήματος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company_subs',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');


if ($id>0) {
  $sql="select * from gks_company_subs where id_company_sub=".$id;
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_company_subs.id_company_sub in (".implode(',',$perm_id_company_sub_ids).")";
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
}


$company_id=0; if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$company_sub_title=''; if (isset($_POST['company_sub_title'])) $company_sub_title=trim_gks(base64_decode($_POST['company_sub_title']));
$company_sub_tagline=''; if (isset($_POST['company_sub_tagline'])) $company_sub_tagline=trim_gks(base64_decode($_POST['company_sub_tagline']));
$company_sub_eponimia=''; if (isset($_POST['company_sub_eponimia'])) $company_sub_eponimia=trim_gks(base64_decode($_POST['company_sub_eponimia']));
$company_sub_phone=''; if (isset($_POST['company_sub_phone'])) $company_sub_phone=trim_gks(base64_decode($_POST['company_sub_phone']));
$company_sub_email=''; if (isset($_POST['company_sub_email'])) $company_sub_email=trim_gks(base64_decode($_POST['company_sub_email']));
$company_sub_url=''; if (isset($_POST['company_sub_url'])) $company_sub_url=trim_gks(base64_decode($_POST['company_sub_url']));
$company_sub_odos=''; if (isset($_POST['company_sub_odos'])) $company_sub_odos=trim_gks(base64_decode($_POST['company_sub_odos']));
$company_sub_arithmos=''; if (isset($_POST['company_sub_arithmos'])) $company_sub_arithmos=trim_gks(base64_decode($_POST['company_sub_arithmos']));
$company_sub_orofos=''; if (isset($_POST['company_sub_orofos'])) $company_sub_orofos=trim_gks(base64_decode($_POST['company_sub_orofos']));
$company_sub_perioxi=''; if (isset($_POST['company_sub_perioxi'])) $company_sub_perioxi=trim_gks(base64_decode($_POST['company_sub_perioxi']));
$company_sub_poli=''; if (isset($_POST['company_sub_poli'])) $company_sub_poli=trim_gks(base64_decode($_POST['company_sub_poli']));
$company_sub_tk=''; if (isset($_POST['company_sub_tk'])) $company_sub_tk=trim_gks(base64_decode($_POST['company_sub_tk']));
$company_sub_country_id=0; if (isset($_POST['company_sub_country_id'])) $company_sub_country_id=intval($_POST['company_sub_country_id']);
$company_sub_nomos_id=0; if (isset($_POST['company_sub_nomos_id'])) $company_sub_nomos_id=intval($_POST['company_sub_nomos_id']);
$company_sub_map_latitude=0; if (isset($_POST['company_sub_map_latitude'])) $company_sub_map_latitude=floatval(str_replace(',','.', $_POST['company_sub_map_latitude']));
$company_sub_map_longitude=0; if (isset($_POST['company_sub_map_longitude'])) $company_sub_map_longitude=floatval(str_replace(',','.', $_POST['company_sub_map_longitude']));
$company_sub_disable=0; if (isset($_POST['company_sub_disable'])) $company_sub_disable=intval($_POST['company_sub_disable']);
$company_sub_related_user_id=0; if (isset($_POST['company_sub_related_user_id'])) $company_sub_related_user_id=intval($_POST['company_sub_related_user_id']);
$company_sub_color=''; if (isset($_POST['company_sub_color'])) $company_sub_color=trim_gks(base64_decode($_POST['company_sub_color']));
$company_sub_sortorder=0; if (isset($_POST['company_sub_sortorder'])) $company_sub_sortorder=intval($_POST['company_sub_sortorder']);

$aade_send_sub=0; if (isset($_POST['aade_send_sub'])) $aade_send_sub=intval($_POST['aade_send_sub']);
$aade_branch_sub=0; if (isset($_POST['aade_branch_sub'])) $aade_branch_sub=intval($_POST['aade_branch_sub']);
$aade_mydata_user_id_sub=''; if (isset($_POST['aade_mydata_user_id_sub'])) $aade_mydata_user_id_sub=trim_gks(base64_decode($_POST['aade_mydata_user_id_sub']));
$aade_mydata_subscription_key_sub=''; if (isset($_POST['aade_mydata_subscription_key_sub'])) $aade_mydata_subscription_key_sub=trim_gks(base64_decode($_POST['aade_mydata_subscription_key_sub']));
$aade_mydata_live_sub=0; if (isset($_POST['aade_mydata_live_sub'])) $aade_mydata_live_sub=intval($_POST['aade_mydata_live_sub']);

$paroxos_send=0;if (isset($_POST['paroxos_send'])) $paroxos_send=intval($_POST['paroxos_send']);
if ($paroxos_send!=1) $paroxos_send=0;
$paroxos_mydata_live=0;if (isset($_POST['paroxos_mydata_live'])) $paroxos_mydata_live=intval($_POST['paroxos_mydata_live']);
if ($paroxos_mydata_live!=1) $paroxos_mydata_live=0;
$aade_paroxos_id=0;if (isset($_POST['aade_paroxos_id'])) $aade_paroxos_id=intval($_POST['aade_paroxos_id']);
$paroxos_branch=0;if (isset($_POST['paroxos_branch'])) $paroxos_branch=intval($_POST['paroxos_branch']);
$pc_username=''; if (isset($_POST['pc_username'])) $pc_username=trim_gks(base64_decode($_POST['pc_username']));
$pc_password=''; if (isset($_POST['pc_password'])) $pc_password=trim_gks(base64_decode($_POST['pc_password']));
$pc_key=''; if (isset($_POST['pc_key'])) $pc_key=trim_gks(base64_decode($_POST['pc_key']));



if ($company_id<=0) {debug_mail(false,'emptyl',                  gks_lang('Ορίστε την εταιρεία'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την εταιρεία')));
  echo json_encode($return); die();}


if ($company_sub_title=='') {debug_mail(false,'emptyl',          gks_lang('Ορίστε τον τίτλο του υποκαταστήματος'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον τίτλο του υποκαταστήματος')));
  echo json_encode($return); die();}



if ($company_sub_email != '' and !filter_var($company_sub_email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,                                              gks_lang('To email δεν είναι σωστό').' : '.$company_sub_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό').' : '.$company_sub_email));
  echo json_encode($return); die();}

//if ($company_sub_phone != '' and (strlen($company_sub_phone) != 10 or substr($company_sub_phone,0,1) != '2') ) {
//  debug_mail(false,                                              gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό').' : '.$company_sub_phone);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό')));
//  echo json_encode($return); die();}  

  
if ($company_sub_country_id==0) {debug_mail(false,'emptyl',      gks_lang('Επιλέξτε μία χώρα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε μία χώρα')));
  echo json_encode($return); die();}

//if ($company_sub_nomos_id==0) {debug_mail(false,'emptyl',        gks_lang('Επιλέξτε έναν νομό'));
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε έναν νομό')));
//  echo json_encode($return); die();}

$sql="select * from gks_company_subs where company_sub_title like '".$db_link->escape_string($company_sub_title)."' and id_company_sub<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το υποκατάστημα με όνομα <b>[1]</b> υπάρχει ήδη:<br><a href="admin-company-sub-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$company_sub_title,$message);
  $message=str_replace('[2]',$row['id_company_sub'],$message);
  debug_mail(false,'warehouse_sub exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

if ($aade_send_sub!=0 and $aade_branch_sub<=0) {debug_mail(false,'emptyl',gks_lang('Ο Αριθμός Εγκατάστασης για την Αποστολή δεδομένων στην ΑΑΔΕ με myDATA πρέπει να είναι μεγαλύτερος από το μηδέν'));
  $return = array('success' => false, 'message' => base64_encode(         gks_lang('Ο Αριθμός Εγκατάστασης για την Αποστολή δεδομένων στην ΑΑΔΕ με myDATA πρέπει να είναι μεγαλύτερος από το μηδέν')));
	echo json_encode($return); die();}
	
	
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

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_company_subs');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_company_subs (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-company-sub-item.php?id='.$id); 
}



$sql="update gks_company_subs set 
company_id=".$company_id.",
company_sub_title='".$db_link->escape_string($company_sub_title)."',
company_sub_tagline='".$db_link->escape_string($company_sub_tagline)."',
company_sub_eponimia='".$db_link->escape_string($company_sub_eponimia)."',
company_sub_phone=". ($company_sub_phone =='' ? 'null' : "'".$db_link->escape_string($company_sub_phone)."'").",
company_sub_email=". ($company_sub_email =='' ? 'null' : "'".$db_link->escape_string($company_sub_email)."'").",
company_sub_url=". ($company_sub_url =='' ? 'null' : "'".$db_link->escape_string($company_sub_url)."'").",
company_sub_odos=". ($company_sub_odos =='' ? 'null' : "'".$db_link->escape_string($company_sub_odos)."'").",
company_sub_arithmos=". ($company_sub_arithmos =='' ? 'null' : "'".$db_link->escape_string($company_sub_arithmos)."'").",
company_sub_orofos=". ($company_sub_orofos =='' ? 'null' : "'".$db_link->escape_string($company_sub_orofos)."'").",
company_sub_perioxi=". ($company_sub_perioxi =='' ? 'null' : "'".$db_link->escape_string($company_sub_perioxi)."'").",
company_sub_poli=". ($company_sub_poli =='' ? 'null' : "'".$db_link->escape_string($company_sub_poli)."'").",
company_sub_tk=". ($company_sub_tk =='' ? 'null' : "'".$db_link->escape_string($company_sub_tk)."'").",
company_sub_country_id=".$company_sub_country_id.",
company_sub_nomos_id=".$company_sub_nomos_id.",

company_sub_map_latitude='".number_format($company_sub_map_latitude,16,'.','')."',
company_sub_map_longitude='".number_format($company_sub_map_longitude,16,'.','')."',

company_sub_disable=".$company_sub_disable.",
company_sub_color=". ($company_sub_color =='' ? 'null' : "'".$db_link->escape_string($company_sub_color)."'").",
company_sub_related_user_id=".$company_sub_related_user_id.",
company_sub_sortorder=".$company_sub_sortorder.",


aade_send_sub=".$aade_send_sub.",
aade_branch_sub=".$aade_branch_sub.",
aade_mydata_user_id_sub='".$db_link->escape_string($aade_mydata_user_id_sub)."',
aade_mydata_subscription_key_sub='".$db_link->escape_string($aade_mydata_subscription_key_sub)."',
aade_mydata_live_sub=".$aade_mydata_live_sub.",



mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_company_sub = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }


$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

gks_lang_data_obj_save_exec_php('gks_company_subs',$id);

$ret_run=gks_sociallinks_item_save($_POST,'gks_company_subs',$id);
if ($ret_run['success']==false) {die(json_encode(array('success'=>false,'message'=>base64_encode($ret_run['message']))));}


gks_warehouse_address_update(array('id_company_sub' => $id));  


$sql_paroxos="select * from gks_company_paroxos where company_sub_id=".$id;
$result_paroxos = $db_link->query($sql_paroxos); 
if (!$result_paroxos) {
  debug_mail(false,'error sql',$sql_paroxos);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }



if ($result_paroxos->num_rows==0) {
  $sql_paroxos="insert into gks_company_paroxos (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  company_sub_id,aade_paroxos_id,paroxos_send,paroxos_mydata_live,
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
  //echo '<pre>';echo $sql_paroxos;die();
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
  where id_company_paroxos=".$id_company_paroxos." and company_sub_id=".$id;
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

$sql_fpa="INSERT INTO gks_company_subs_basefpa 
(company_sub_id, fpa_base_id, fpa_id)
SELECT ".$id." AS aaaa, gks_eshop_fpa_base.id_fpa_base, 0 AS bbbb
FROM gks_eshop_fpa_base 
LEFT JOIN (
  SELECT fpa_base_id FROM gks_company_subs_basefpa WHERE company_sub_id=".$id."
) AS iparxoun ON gks_eshop_fpa_base.id_fpa_base=iparxoun.fpa_base_id
WHERE iparxoun.fpa_base_id Is Null";
$result_fpa = $db_link->query($sql_fpa); 
if (!$result_fpa) {
  debug_mail(false,'error sql',$sql_fpa);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$sql_fpa="update gks_company_subs_basefpa set fpa_id=0 where company_sub_id=".$id;
$result_fpa = $db_link->query($sql_fpa); 
if (!$result_fpa) {
  debug_mail(false,'error sql',$sql_fpa);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$fpa_base_hasvalue=[];
foreach ($fpa_base_array as $myval) {
  $fpa_base_hasvalue[]=intval($myval['base_id']);
  $sql_fpa="update gks_company_subs_basefpa set fpa_id=".intval($myval['base_val'])." where company_sub_id=".$id." and fpa_base_id=".intval($myval['base_id']);
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


$sql_fpa="INSERT INTO gks_company_subs_fpa 
(company_sub_id,fiscal_position_id, fpa_base_id, fpa_id)
SELECT ".$id." as aaaa, sindiasmoi.id_fiscal_position, sindiasmoi.id_fpa_base, 0 AS bbbb
FROM (
  SELECT gks_eshop_fiscal_position.id_fiscal_position, gks_eshop_fpa_base.id_fpa_base
  FROM gks_eshop_fpa_base, gks_eshop_fiscal_position
  WHERE gks_eshop_fpa_base.fpa_base_disable=0 
  AND gks_eshop_fiscal_position.fiscal_position_disable=0
  ORDER BY gks_eshop_fiscal_position.id_fiscal_position, gks_eshop_fpa_base.id_fpa_base
) AS sindiasmoi 
LEFT JOIN (
  SELECT gks_company_subs_fpa.fiscal_position_id, gks_company_subs_fpa.fpa_base_id,fpa_id
  FROM gks_company_subs_fpa
  WHERE gks_company_subs_fpa.company_sub_id=".$id."
) AS iparxoun ON (sindiasmoi.id_fiscal_position = iparxoun.fiscal_position_id) AND (sindiasmoi.id_fpa_base = iparxoun.fpa_base_id)
WHERE iparxoun.fpa_id Is Null";
$result_fpa = $db_link->query($sql_fpa); 
if (!$result_fpa) {
  debug_mail(false,'error sql',$sql_fpa);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$sql_fpa="update gks_company_subs_fpa set fpa_id=0 where company_sub_id=".$id;
$result_fpa = $db_link->query($sql_fpa); 
if (!$result_fpa) {
  debug_mail(false,'error sql',$sql_fpa);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

foreach ($fpa_fiscals_array as $myval) {
  $myval['base_id']=intval($myval['base_id']);
  if (in_array($myval['base_id'],$fpa_base_hasvalue)==false) $myval['base_val']=0;
  $sql_fpa="update gks_company_subs_fpa set fpa_id=".intval($myval['base_val'])." where company_sub_id=".$id." and fiscal_position_id=".intval($myval['fiscal_id'])." and fpa_base_id=".$myval['base_id'];
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







