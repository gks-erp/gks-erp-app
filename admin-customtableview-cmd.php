<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Αποθήκευση custom css');
db_open();
stat_record();


$cmd='';if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
$page='';if (isset($_POST['page'])) $page=trim_gks($_POST['page']);
$tableindex='';if (isset($_POST['tableindex'])) $tableindex=intval($_POST['tableindex']);
$run_type='';if (isset($_POST['run_type'])) $run_type=trim_gks($_POST['run_type']);
$viewindex='';if (isset($_POST['viewindex'])) $viewindex=intval($_POST['viewindex']);
$data_str='';if (isset($_POST['data_str'])) $data_str=trim_gks($_POST['data_str']);

$data_array=[];
if ($data_str!='') {
  $data_array = json_decode(base64_decode($data_str), true);
  if ($data_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error data_array',$_POST['data_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
}

if ($cmd!='save') {
  $message=gks_lang('Δεν έχει ορισθεί η εντολή');
  $message.='<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
  debug_mail(false,'gks_customtableview',                        $message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die(); }
    
if ($page=='') {
  $message=gks_lang('Δεν έχει ορισθεί η σελίδα');
  $message.='<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
  debug_mail(false,'gks_customtableview',                        $message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die(); }  

if ($tableindex<1) {
  $message=gks_lang('Δεν έχει ορισθεί το [1] του πίνακα');
  $message=str_replace('[1]','index',$message);
  $message.='<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
  debug_mail(false,'gks_customtableview',                        $message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die(); }

if (in_array($run_type,['new','edit','apply','none'])==false) {
  $message=gks_lang('Δεν έχει ορισθεί το [1] του πίνακα');
  $message=str_replace('[1]','run_type',$message);
  $message.='<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
  debug_mail(false,'gks_customtableview',                        $message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die(); } 
    
if ($viewindex<1) {
  $message=gks_lang('Δεν έχει ορισθεί το [1] του πίνακα');
  $message=str_replace('[1]','viewindex',$message);
  $message.='<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
  debug_mail(false,'gks_customtableview',                        $message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die(); }  
   
  
if ($cmd=='save') {
  if (count($data_array)==0) {
    $message=gks_lang('Δεν έχουν ορισθεί τα δεδομένα προβολής του πίνακα');
    $message.='<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
    debug_mail(false,'gks_customtableview',                        $message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die(); }
    
  if (in_array($run_type,['new','edit','none'])) {
    gks_set_user_settings($my_wp_user_id, array('gks_customtableview'=>array($page=>serialize($data_array))));  
  } else {
    $message=gks_lang('Λάθος κατάσταση του [1]');
    $message=str_replace('[1]','run_type',$message);
    $message.='<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα');
    debug_mail(false,'gks_customtableview',                        $message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
}

$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();

$return = array('success' => false, 'message' => base64_encode(
  '<pre>'.
  'cmd:'.$cmd."\n".
  'page:'.$page."\n".
  'tableindex:'.$tableindex."\n".
  'run_type:'.$run_type."\n".
  'viewindex:'.$viewindex."\n".
  'cmd:'.$data_str."\n".
  print_r($data_array,true)
));
echo json_encode($return); die();

