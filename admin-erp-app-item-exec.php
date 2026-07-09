<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



//die();


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Αποθήκευση gks ERP App Desktop').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_erp_app',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($id>0) {
  $sql ="SELECT * FROM gks_erp_app where id_erp_app = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();
}

$erp_app_name=''; if (isset($_POST['erp_app_name'])) $erp_app_name=trim_gks(base64_decode($_POST['erp_app_name']));
$erp_app_descr=''; if (isset($_POST['erp_app_descr'])) $erp_app_descr=trim_gks(base64_decode($_POST['erp_app_descr']));
$erp_app_url=''; if (isset($_POST['erp_app_url'])) $erp_app_url=trim_gks(base64_decode($_POST['erp_app_url']));
$erp_app_port=0; if (isset($_POST['erp_app_port'])) $erp_app_port=intval($_POST['erp_app_port']);
$erp_app_sortorder=0; if (isset($_POST['erp_app_sortorder'])) $erp_app_sortorder=intval($_POST['erp_app_sortorder']);
$erp_app_disabled=0; if (isset($_POST['erp_app_disabled'])) $erp_app_disabled=intval($_POST['erp_app_disabled']);
$erp_app_token_new=0; if (isset($_POST['erp_app_token_new'])) $erp_app_token_new=intval($_POST['erp_app_token_new']);
if ($id<=0) $erp_app_token_new=0;
$erp_app_secret=''; if (isset($_POST['erp_app_secret'])) $erp_app_secret=trim_gks(base64_decode($_POST['erp_app_secret']));
$voip_localdb=''; if (isset($_POST['voip_localdb'])) $voip_localdb=trim_gks(base64_decode($_POST['voip_localdb']));
$voip_ip=''; if (isset($_POST['voip_ip'])) $voip_ip=trim_gks(base64_decode($_POST['voip_ip']));
$voip_AIM_port=''; if (isset($_POST['voip_AIM_port'])) $voip_AIM_port=intval($_POST['voip_AIM_port']);
$voip_AIM_username=''; if (isset($_POST['voip_AIM_username'])) $voip_AIM_username=trim_gks(base64_decode($_POST['voip_AIM_username']));
$voip_AIM_password=''; if (isset($_POST['voip_AIM_password'])) $voip_AIM_password=trim_gks(base64_decode($_POST['voip_AIM_password']));
$voip_call_originate=0; if (isset($_POST['voip_call_originate'])) $voip_call_originate=intval($_POST['voip_call_originate']);
$voip_call_monitoring=0; if (isset($_POST['voip_call_monitoring'])) $voip_call_monitoring=intval($_POST['voip_call_monitoring']);

if ($erp_app_name=='') {debug_mail(false,'emptyl',               gks_lang('Το όνομα δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }





$sql="select * from gks_erp_app where erp_app_name like '".$db_link->escape_string($erp_app_name)."' and id_erp_app<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το όνομα <b>[1]</b> υπάρχει ήδη:<br><br><a href="admin-erp-app-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$erp_app_descr,$message);
  $message=str_replace('[2]',$row['id_erp_app'],$message);
  
  debug_mail(false,'erp-app-item exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}


if (strlen($erp_app_secret)<32) {debug_mail(false,'emptyl',      gks_lang('Το Ιδιωτικό Κλειδί πρέπει να είναι τουλάχιστον 32 χαρακτήρες'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Ιδιωτικό Κλειδί πρέπει να είναι τουλάχιστον 32 χαρακτήρες')));
  echo json_encode($return); die(); }
  
if ($erp_app_url=='') {debug_mail(false,'emptyl',                gks_lang('Το Url δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Url δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }
  
if ($erp_app_port<10000 or $erp_app_port>65000) {
  debug_mail(false,'emptyl',                                     gks_lang('Η πόρτα πρέπει να είναι από 10000 έως 65000'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η πόρτα πρέπει να είναι από 10000 έως 65000')));
  echo json_encode($return); die(); }
  
$create_token=false;
if ($id==-1 or $erp_app_token_new==1) $create_token=true;
if ($create_token) {
  $erp_app_token='';
  $post = http_build_query(array(
    'id_erp_app' => $id,
    'site' => GKS_SITE_URL,
    'source_file' => base64_encode($_SERVER['SCRIPT_FILENAME']),
    'user_id' => $my_wp_user_id,
  ));
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,'https://tools.gks.gr/gks_erp_app/create_token.php');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $server_output = curl_exec($ch);
  curl_close ($ch);
  
  if (strlen($server_output)==9 and ctype_digit($server_output)) {
    $erp_app_token=$server_output;
  } else {
    debug_mail(false,'emptyl',                                     gks_lang('Δεν μπορεί να δημιουργηθεί το κλειδί').'<br>'.gks_lang('Δοκιμάστε αργότερα'),$server_output);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί το κλειδί').'<br>'.gks_lang('Δοκιμάστε αργότερα').'<br>|'.$server_output.'|'));
    echo json_encode($return); die();
  }

}

if ($erp_app_url!='frp') {
  $sql="select * from gks_erp_app where erp_app_url like '".$db_link->escape_string($erp_app_url)."' and erp_app_port=".$erp_app_port." and id_erp_app<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Υπάρχει ήδη εφαρμογή με το ίδιο URL και την ίδια πόρτα:<br><br><a href="admin-erp-app-item.php?id=[2]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',$row['id_erp_app'],$message);
    debug_mail(false,'erp-app-item exist symbol',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
  
  
} 

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_erp_app');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_erp_app (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-erp-app-item.php?id='.$id); 
}

$sql="update gks_erp_app set 
".($create_token ? "erp_app_token='".$db_link->escape_string($erp_app_token)."'," : '')."
erp_app_descr='".$db_link->escape_string($erp_app_descr)."',
erp_app_name='".$db_link->escape_string($erp_app_name)."',
erp_app_secret='".$db_link->escape_string($erp_app_secret)."',
erp_app_url='".$db_link->escape_string($erp_app_url)."',
erp_app_port=".$erp_app_port.",
erp_app_sortorder=".$erp_app_sortorder.",
erp_app_disabled=".$erp_app_disabled.",
voip_localdb='".$db_link->escape_string($voip_localdb)."',
voip_ip='".$db_link->escape_string($voip_ip)."',
voip_AIM_port=".$voip_AIM_port.",
voip_AIM_username='".$db_link->escape_string($voip_AIM_username)."',
voip_AIM_password='".$db_link->escape_string($voip_AIM_password)."',
voip_call_originate=".$voip_call_originate.",
voip_call_monitoring=".$voip_call_monitoring.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_erp_app = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();




