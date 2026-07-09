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


$my_page_title=gks_lang('Αποθήκευση Μικρό URL').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_urlshort',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($id>0) {
  $sql ="SELECT * FROM gks_urlshort where id_urlshort = ".$id;
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

$urlsort_descr=''; if (isset($_POST['urlsort_descr'])) $urlsort_descr=trim_gks(base64_decode($_POST['urlsort_descr']));
$shorturl=''; if (isset($_POST['shorturl'])) $shorturl=trim_gks(base64_decode($_POST['shorturl']));
$longurl=''; if (isset($_POST['longurl'])) $longurl=trim_gks(base64_decode($_POST['longurl']));
$urlsort_sortorder=0; if (isset($_POST['urlsort_sortorder'])) $urlsort_sortorder=intval($_POST['urlsort_sortorder']);
$urlsort_disabled=0; if (isset($_POST['urlsort_disabled'])) $urlsort_disabled=intval($_POST['urlsort_disabled']);

$assigned_id=0; if (isset($_POST['assigned_id'])) $assigned_id=intval($_POST['assigned_id']);
$crm_channel_id=0; if (isset($_POST['crm_channel_id'])) $crm_channel_id=intval($_POST['crm_channel_id']);
$crm_channel_contact_id=0; if (isset($_POST['crm_channel_contact_id'])) $crm_channel_contact_id=intval($_POST['crm_channel_contact_id']);
$crm_channel_campain_id=0; if (isset($_POST['crm_channel_campain_id'])) $crm_channel_campain_id=intval($_POST['crm_channel_campain_id']);
//$crm_channel_url=''; if (isset($_POST['crm_channel_url'])) $crm_channel_url=trim_gks(base64_decode($_POST['crm_channel_url']));
$crm_channel_code=''; if (isset($_POST['crm_channel_code'])) $crm_channel_code=trim_gks(base64_decode($_POST['crm_channel_code']));
$crm_channel_text=''; if (isset($_POST['crm_channel_text'])) $crm_channel_text=trim_gks(base64_decode($_POST['crm_channel_text']));

if ($urlsort_descr=='') {debug_mail(false,'empty urlsort_descr', '');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }


if ($shorturl=='') {debug_mail(false,'emptyl','shorturl is not ok');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Μικρό URL δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

if (filter_var(GKS_SITE_URL.'s/'.$shorturl, FILTER_VALIDATE_URL) === false) {debug_mail(false,'emptyl','shorturl is not ok, not validate');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Μικρό URL δεν είναι έγκυρη διεύθυνση')));
  echo json_encode($return); die(); }


if ($longurl=='') {debug_mail(false,'emptyl',                    'longurl is empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το URL δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

if (filter_var($longurl, FILTER_VALIDATE_URL) === false) {debug_mail(false,'emptyl','longurl is not url');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το URL δεν είναι έγκυρη διεύθυνση')));
  echo json_encode($return); die(); }
  

if ($crm_channel_id<=0) {
  $crm_channel_contact_id=0;
  $crm_channel_campain_id=0;
  //$crm_channel_url='';
  $crm_channel_code='';
  $crm_channel_text='';
} else {
  $sql_channel="select * from gks_crm_channel_sale where id_crm_channel_sale=".$crm_channel_id;
  $result_channel = $db_link->query($sql_channel);        
  if (!$result_channel) {
    debug_mail(false,'error sql',$sql_channel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result_channel->num_rows!=1) {
    debug_mail(false,'channel not found',$sql_channel);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το κανάλι πωλήσεων').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
    echo json_encode($return); die();}
  $row_channel = $result_channel->fetch_assoc();
  if ($row_channel['crm_channel_has_contact']==0)  $crm_channel_contact_id=0;
  if ($row_channel['crm_channel_has_campain']==0)  $crm_channel_campain_id=0;
  //if ($row_channel['crm_channel_has_url']==0)  $crm_channel_url='';
  if ($row_channel['crm_channel_has_code']==0)  $crm_channel_code='';
  if ($row_channel['crm_channel_has_text']==0)  $crm_channel_text='';
}


$sql="select * from gks_urlshort where shorturl like '".$db_link->escape_string($shorturl)."' and id_urlshort<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το Μικρό URL <b>[1]</b> υπάρχει ήδη:<br><a href="admin-urlshort-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$shorturl,$message);  
  $message=str_replace('[2]',$row['id_urlshort'],$message);  
  debug_mail(false,'shorturl in gks_urlshort already exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

if ($shorturl[0]=='s' or $shorturl[0]=='s') { //ean ksekina apo s
  $message=gks_lang('Το Μικρό URL δεν μπορεί να ξεκινά από <b>[1]</b>.<br>Είναι δεσμευμένο από το σύστημα');
  $message=str_replace('[1]','s',$message);    
  debug_mail(false,'shorturl can not start with s',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}  
  


if (strlen($shorturl)>=3) {
  $first3chars=substr($shorturl,0,3);
  $sql="select * from gks_custom_table where shortcode_prefix like '".$db_link->escape_string($first3chars)."'";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    //$row = $result->fetch_assoc();
    $message=gks_lang('Το Μικρό URL δεν μπορεί να ξεκινά από <b>[1]</b>.<br>Είναι δεσμευμένο από το σύστημα');
    $message=str_replace('[1]',$first3chars,$message);    
    debug_mail(false,'shorturl can not start with (calc) '.$first3chars,$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
}

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_urlshort');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_urlshort (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-urlshort-item.php?id='.$id); 
}

//crm_channel_url=". ($crm_channel_url =='' ? 'null' : "'".$db_link->escape_string($crm_channel_url)."'").",

$sql="update gks_urlshort set 
urlsort_descr='".$db_link->escape_string($urlsort_descr)."',
shorturl='".$db_link->escape_string($shorturl)."',
longurl='".$db_link->escape_string($longurl)."',
urlsort_sortorder=".$urlsort_sortorder.",
urlsort_disabled=".$urlsort_disabled.",

assigned_id=".$assigned_id.",
crm_channel_id=".$crm_channel_id.",
crm_channel_contact_id=".$crm_channel_contact_id.",
crm_channel_campain_id=".$crm_channel_campain_id.",
crm_channel_code=". ($crm_channel_code =='' ? 'null' : "'".$db_link->escape_string($crm_channel_code)."'").",
crm_channel_text=". ($crm_channel_text =='' ? 'null' : "'".$db_link->escape_string($crm_channel_text)."'").",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_urlshort = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

