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

$my_page_title=gks_lang('Αποθήκευση Χρονοπρογραμματισμός Εργασίας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crons',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_crons where id_cron = ".$id;
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


$fetch_url=''; if (isset($_POST['fetch_url'])) $fetch_url=trim_gks(base64_decode($_POST['fetch_url']));
$every_seconds=''; if (isset($_POST['every_seconds'])) $every_seconds=intval($_POST['every_seconds']);
if ($_POST['next_run'] == '__/__/____ __:__') $_POST['next_run']='';
$next_run=trim_gks(stripslashes(urldecode($_POST['next_run'])));
if ($next_run!='') {
  $next_run = mystrtodb($next_run);
}
$comments=''; if (isset($_POST['comments'])) $comments=trim_gks(base64_decode($_POST['comments']));
$disable_cron=''; if (isset($_POST['disable_cron'])) $disable_cron=intval($_POST['disable_cron']);
if ($disable_cron!=1) $disable_cron=0;


if ($fetch_url=='') {debug_mail(false,'emptyl',                  gks_lang('Το URL δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το URL δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

$fetch_url_check=$fetch_url;
if (substr($fetch_url_check, 0, 4)=='/my/' or $fetch_url_check=='/wp-cron.php') {
  $fetch_url_check=GKS_SITE_URL.substr($fetch_url_check, 1);
}
  
if (!filter_var($fetch_url_check, FILTER_VALIDATE_URL)) {debug_mail(false,'emptyl', gks_lang('Το URL δεν είναι link/url'));
  $return = array('success' => false, 'message' => base64_encode(             gks_lang('Το URL δεν είναι link/url')));
  echo json_encode($return); die(); }

if ($every_seconds<60) {debug_mail(false,'emptyl',               gks_lang('Το Εκτέλεση κάθε δεν μπορεί να είναι μικρότερο από 60'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Εκτέλεση κάθε δεν μπορεί να είναι μικρότερο από 60')));
  echo json_encode($return); die(); }

if ($every_seconds>86400) {debug_mail(false,'emptyl',            gks_lang('Το Εκτέλεση κάθε δεν μπορεί να είναι μεγαλύτερο από 86400 (24 ώρες)'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Εκτέλεση κάθε δεν μπορεί να είναι μεγαλύτερο από 86400 (24 ώρες)')));
  echo json_encode($return); die(); }

$redirect='';
if ($id==-1) {
  $sql="insert into gks_crons (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-crons-item.php?id='.$id); 
}



$sql="update gks_crons set 
fetch_url='".$db_link->escape_string($fetch_url)."',
every_seconds=".$every_seconds.",
next_run=".($next_run=='' ? 'null' : "'".$db_link->escape_string($next_run)."'").",
comments='".$db_link->escape_string($comments)."',
disable_cron=".$disable_cron.",
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_cron = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  





$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

