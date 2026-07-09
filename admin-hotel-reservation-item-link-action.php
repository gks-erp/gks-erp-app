<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');




$id=0;
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$action='';if (isset($_POST['action'])) $action=trim_gks($_POST['action']);
if ($action!='start' and $action!='stop' and $action!='reset') {
  debug_mail(false,'the link is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εντολή λήψης')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Εντολή λήψης αρχείου κράτησης');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_reservation','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$sql="select * from gks_hotel_reservation_links where id_hotel_reservation_links=".$id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  
if ($result->num_rows <= 0) {  
  debug_mail(false,'record not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
  echo json_encode($return); die();}

$row=$result->fetch_assoc();
$url=encodeURI($row['url']);


if ($action == 'start') {

  if ($GKS_SEND_ANYWHERE_API_KEY=='' and startwith(strtolower($url),'http://sendanywhe.re/') and strlen($url)==29) {
    debug_mail(false,'GKS_SEND_ANYWHERE_API_KEY is not set',$url);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το<br><b>GKS_SEND_ANYWHERE_API_KEY</b></br>από τις ρυθμίσεις.<br>Θα πρέπει να το κατεβάσετε χειροκίνητα')));
    echo json_encode($return); die();    
  }

  if ($row['download_status']!=0) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η κατάσταση του συνδέσμου δεν είναι η σωστή').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
    echo json_encode($return); die();
  }



  if ($url=='' or filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το url δεν είναι σωστό').': '.$url));
    echo json_encode($return); die();
  }
  
  gks_curl_post_async(GKS_SITE_URL.'my/exec_hotel_reservation_link_start.php',array('id' =>$id));

  $return = array('success' => true, 'message' => base64_encode('running...'),'id' => $id, 'action' => $action);
  echo json_encode($return); die();
} else if ($action == 'stop') {
  $sql="update gks_hotel_reservation_links set html_tds=null,relative_path='',download_status=3,download_start=null,download_end=null,download_pososto=0,download_size_until_now=0,download_size_total=0,
  download_message='".$db_link->escape_string(gks_lang('Ακυρώθηκε από τον χρήστη'))."' 
  where id_hotel_reservation_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  $return = array('success' => true, 'message' => base64_encode('OK'),'id' => $id, 'action' => $action);
  echo json_encode($return); die();
    
} else if ($action == 'reset') {
  $sql="update gks_hotel_reservation_links set html_tds=null,relative_path='',download_status=0,download_start=null,download_end=null,download_pososto=0,download_size_until_now=0,download_size_total=0,download_message='' where id_hotel_reservation_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  $return = array('success' => true, 'message' => base64_encode('OK'),'id' => $id, 'action' => $action);
  echo json_encode($return); die();
  
}



$return = array('success' => false, 'message' => base64_encode('Δddd '.$action.' '.$id.' '.$url));
echo json_encode($return); die();

