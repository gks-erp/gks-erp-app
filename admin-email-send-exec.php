<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$my_page_title=gks_lang('Εκτέλεση Αποστολής email');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_email','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


//print '<pre>';
//print_r($_POST);
//die();


$ispreview=false; if (isset($_POST['ispreview']) and $_POST['ispreview']=='1') $ispreview=true;
$myfrom=''; if (isset($_POST['from'])) $myfrom=trim_gks(base64_decode($_POST['from']));
$myto=''; if (isset($_POST['to'])) $myto=trim_gks(base64_decode($_POST['to']));
$template=0; if (isset($_POST['mytemplate'])) $template=intval($_POST['mytemplate']);
$mysubject=''; if (isset($_POST['subject'])) $mysubject=trim_gks(base64_decode($_POST['subject']));
$mymessage=''; if (isset($_POST['message'])) $mymessage=trim_gks(base64_decode($_POST['message']));
$model='admin'; if (isset($_POST['model'])) $model=trim_gks(base64_decode($_POST['model']));
$model_id=0;if (isset($_POST['model_id'])) $model_id=intval($_POST['model_id']);

//echo '<pre>'.$template;die();

if ($ispreview==false and $myfrom=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε κάποιον αποστολέα')));
  echo json_encode($return); die();}
if ($ispreview==false and $myto=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε κάποιον αποδέκτη')));
  echo json_encode($return); die();}
if ($ispreview==false and $mysubject=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε κάποιο θέμα')));
  echo json_encode($return); die();}
if ($ispreview==false and $template<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποιο πρότυπο')));
  echo json_encode($return); die();}





$replaces=array();
if ($mymessage=='') {
  $replaces[] = array('<p>[[message]]</p>', '');
  $replaces[] = array('[[message]]', '');
} else {
  $replaces[] = array('[[message]]', nl2br_gks($mymessage));
}
foreach ($_POST as $key => $value) {
  if (substr($key,0,12)=='email_param_') {
    $replaces[] = array('[['.substr($key,12).']]', trim_gks(base64_decode($value)));    
  }
} 
$email_attachments=array();
if (isset($_POST['email_attachments']) && trim_gks($_POST['email_attachments'])!='') {
  $v=explode('a',$_POST['email_attachments']);
  foreach ($v as $value) {
    if (trim_gks($value)<>'') {
      $email_attachments[] = intval(trim_gks($value));
    }
  } 
}





//print '<pre>';print_r($replaces);die();





$Attachments=array();
if (count($email_attachments) >0) {
  if (isset($_POST['page']) and $_POST['page']=='order_item') {
    $sql="SELECT * FROM gks_orders_uploads WHERE id_order_uploads In (".implode(',',$email_attachments).')';
    $res = $db_link->query($sql);        
    if (!$res) debug_mail(false,'admin-email-send-exec.php error sql',$sql);
    if (!$res) die('sql error');
    while ($row = $res->fetch_assoc()) { 
      $attac_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$row['url'];
      $attac_name=mb_basename($attac_path);
      if (file_exists($attac_path)) {
        $Attachments[] = array($attac_path,$attac_name);
      }
    }
  } else {


    $sql="SELECT gks_file.id_file, gks_file.file_name, gks_event.event_path
    FROM gks_file LEFT JOIN gks_event ON gks_file.event_id = gks_event.id_event
    WHERE gks_file.id_file In (".implode(',',$email_attachments).')';
    
    $res = $db_link->query($sql);        
    if (!$res) debug_mail(false,'admin-email-send-exec.php error sql',$sql);
    if (!$res) die('sql error');
    while ($row = $res->fetch_assoc()) { 
      $attac_path=GKS_DATA.$row['event_path'].'/original/'.$row['file_name'];
      $attac_name=$row['file_name'];
      if (file_exists($attac_path)) {
        $Attachments[] = array($attac_path,$attac_name);
      }
    }
  }
}


$EmbeddedImages = array();



$myreport_error='';
$myreport_ok='';
$mytotal=0;
$myok=0;

//echo '<pre>';  echo $myfrom;  die();

$mya=explode(',',$myto);
foreach ($mya as $user) {
  $mytotal++;
  
  $res=false;
  $user=trim_gks($user);
  
  if ($ispreview or filter_var(trim_gks($user), FILTER_VALIDATE_EMAIL)) {
    //mymail_template($model, $model_id, $from, $from_name, $replyto, $sender, $to, $to_name, $subject, $template, $replaces = array(), $Attachments = array(), $EmbeddedImages = array()) {
    $params=array(
      'model'=>$model,
      'model_id'=>$model_id,
      'from'=>$myfrom,
      'to'=>$user,
      'subject'=>$mysubject,
      'template'=>$template,
      'replaces'=>$replaces,
      'Attachments'=>$Attachments,
      'EmbeddedImages'=>$EmbeddedImages,
      'force_template'=>true,
      'ispreview'=>$ispreview,
    );
    $res=gks_mymail_template($params);
    
    if ($ispreview) {
      
      $tmp_filename='email_preview_'.showDate(time(), 'Y-m-d_H-i-s',1).'_'.rand(10000,99999).'.html';
      $tmp_filepath=GKS_SITE_PATH.'tmp/'.$tmp_filename;
      @file_put_contents($tmp_filepath,$res['body']);
      $tmp_url='admin-get-file.php?fs=tmp&file='.urlencode($tmp_filename);

      $return = array('success' => true, 'message' => base64_encode('OK'), 'preview_url'=>base64_encode($tmp_url),'subject'=>base64_encode($res['subject']));
      echo json_encode($return); die(); 

//      
//      echo '<pre>';
//      echo $tmp_url;
//      die();
    }
    
  }
  if ($res) {
    $myok++;
    $myreport_ok.=$user.'</br>';
 } else {
    $myreport_error.=$user.'</br>';
  }
} 

if ($mytotal == $myok) {
  $return = array('success' => true, 'message' => base64_encode(gks_lang('Επιτυχής αποστολή')));
  echo json_encode($return); die(); 
  
} else {
  $myout = gks_lang('Αποτυχία αποστολής').'.<br/><br/>';
  if ($myok>0) {
    $myout.=gks_lang('Η αποστολή στις παρακάτω διευθύνσεις ήταν <b>επιτυχής</b>').':<br>'.$myreport_ok.'<br><br>';
  }
  $myout.=gks_lang('Η αποστολή στις παρακάτω διευθύνσεις <b>δεν ήταν επιτυχής</b>').':<br>'.$myreport_error;
  
  $return = array('success' => false, 'message' => base64_encode($myout ));
  echo json_encode($return); die();  
}



