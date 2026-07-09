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
$my_page_title=gks_lang('Αποθήκευση Πρότυπου HTML');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_template_html',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}







if ($id>0) {
  $sql="select * from gks_template_html where id_template_html=".$id." limit 1";
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




$template_html_descr=''; if (isset($_POST['template_html_descr'])) $template_html_descr=trim_gks(base64_decode($_POST['template_html_descr']));
$template_html_type=''; if (isset($_POST['template_html_type'])) $template_html_type=intval($_POST['template_html_type']);

$gks_lang='el-GR'; if (isset($_POST['gks_lang'])) $gks_lang=trim_gks(base64_decode($_POST['gks_lang']));
$edit_mode=''; if (isset($_POST['edit_mode'])) $edit_mode=trim_gks(base64_decode($_POST['edit_mode']));
if ($edit_mode!='html' and $edit_mode!='raw') $edit_mode='html';

$orders_online_url=''; if (isset($_POST['orders_online_url'])) $orders_online_url=trim_gks(base64_decode($_POST['orders_online_url']));
$orders_online_sms_sender=''; if (isset($_POST['orders_online_sms_sender'])) $orders_online_sms_sender=trim_gks(base64_decode($_POST['orders_online_sms_sender']));



$is_disable=0; if (isset($_POST['is_disable'])) $is_disable=intval($_POST['is_disable']);
$sortorder=0; if (isset($_POST['sortorder'])) $sortorder=intval($_POST['sortorder']);


//tha kano metatropi apo tin glosa tou xristi sta ellikina


$loc_langs=''; if (isset($_POST['loc_langs'])) $loc_langs=trim_gks(base64_decode($_POST['loc_langs']));
$loc_langs_array=array();
if ($loc_langs!='') $loc_langs_array=json_decode($loc_langs, true);
//print '<pre>'; print_r($loc_langs_array); die();

$loc_langs_array_clean=array();
foreach ($loc_langs_array as $value) {
  if (isset($value['lang']) and isset($value['form_id'])) {
    $lang=trim_gks($value['lang']);
    $form_id=intval($value['form_id']);
    if ($lang!='' and $form_id>0 and $lang!=$gks_lang and isset($loc_langs_array_clean[$lang])==false) {
      $loc_langs_array_clean[$lang]=$form_id;
    }
  }
} 
//print '<pre>'; print_r($loc_langs_array_clean); die();


 
if ($template_html_descr=='') {debug_mail(false,'emptyl',           gks_lang('Ορίστε την Περιγραφή της φόρμας εκτύπωσης'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή της φόρμας εκτύπωσης')));
  echo json_encode($return); die();}

if ($template_html_type<=0) {debug_mail(false,'emptyl',          gks_lang('Ορίστε τον Τύπο'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Τύπο')));
  echo json_encode($return); die();}


$sql="select * from gks_template_html where template_html_descr like '".$db_link->escape_string($template_html_descr)."' and id_template_html<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το Πρότυπο HTML με όνομα <b>[1]</b> υπάρχει ήδη');
  $message=str_replace('[1]',$template_html_descr,$message);
  debug_mail(false,'already exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}


if ($template_html_type==1) {
  if ($orders_online_url=='') {debug_mail(false,'emptyl',          gks_lang('Ορίστε το OnLine Προσφορά URL'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το OnLine Προσφορά URL')));
    echo json_encode($return); die();}
  
  if (filter_var($orders_online_url, FILTER_VALIDATE_URL)==false) {
    debug_mail(false,'emptyl',                                     gks_lang('Το OnLine Προσφορά URL δεν έχει σωστή μορφή URL'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το OnLine Προσφορά URL δεν έχει σωστή μορφή URL')));
    echo json_encode($return); die();}
}




$html_part_1='';        if (isset($_POST['html_part_1']))       $html_part_1=trim_gks(base64_decode($_POST['html_part_1']));
$html_part_2='';        if (isset($_POST['html_part_2']))       $html_part_2=trim_gks(base64_decode($_POST['html_part_2']));
$html_part_3='';        if (isset($_POST['html_part_3']))       $html_part_3=trim_gks(base64_decode($_POST['html_part_3']));
$html_part_4='';        if (isset($_POST['html_part_4']))       $html_part_4=trim_gks(base64_decode($_POST['html_part_4']));
$html_part_5='';        if (isset($_POST['html_part_5']))       $html_part_5=trim_gks(base64_decode($_POST['html_part_5']));
$html_part_6='';        if (isset($_POST['html_part_6']))       $html_part_6=trim_gks(base64_decode($_POST['html_part_6']));
$html_part_7='';        if (isset($_POST['html_part_7']))       $html_part_7=trim_gks(base64_decode($_POST['html_part_7']));
$html_part_8='';        if (isset($_POST['html_part_8']))       $html_part_8=trim_gks(base64_decode($_POST['html_part_8']));
$html_part_9='';        if (isset($_POST['html_part_9']))       $html_part_9=trim_gks(base64_decode($_POST['html_part_9']));
$custom_css='';         if (isset($_POST['custom_css']))        $custom_css=trim_gks(base64_decode($_POST['custom_css']));
$custom_javascript='';  if (isset($_POST['custom_javascript'])) $custom_javascript=trim_gks(base64_decode($_POST['custom_javascript']));



$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_template_html');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_template_html (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-template_html-item.php?id='.$id); 
}

$localization_set_id=0;
while (true) {
  $localization_set_id=rand(1000000,1999999);
  $sql = "SELECT localization_set_id from gks_template_html where localization_set_id=".$localization_set_id;
  $result = $db_link->query($sql);
  if ($result->num_rows == 0) {
    break;
  }
}


$sql="update gks_template_html set 
template_html_descr='".$db_link->escape_string($template_html_descr)."',
template_html_type=".$template_html_type.",
gks_lang='".$db_link->escape_string($gks_lang)."',
edit_mode='".$db_link->escape_string($edit_mode)."',
orders_online_url='".$db_link->escape_string($orders_online_url)."',
orders_online_sms_sender='".$db_link->escape_string($orders_online_sms_sender)."',
is_disable=".$is_disable.",
sortorder=".$sortorder.",



html_part_1='".$db_link->escape_string($html_part_1)."',
html_part_3='".$db_link->escape_string($html_part_3)."',
html_part_5='".$db_link->escape_string($html_part_5)."',
html_part_6='".$db_link->escape_string($html_part_6)."',
html_part_7='".$db_link->escape_string($html_part_7)."',
html_part_4='".$db_link->escape_string($html_part_4)."',
html_part_2='".$db_link->escape_string($html_part_2)."',
html_part_8='".$db_link->escape_string($html_part_8)."',
html_part_9='".$db_link->escape_string($html_part_9)."',
custom_css='".$db_link->escape_string($custom_css)."',
custom_javascript='".$db_link->escape_string($custom_javascript)."',

localization_set_id=".$localization_set_id.",

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_template_html = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }



//echo '<pre>';echo $localization_set_id; die();
if ($localization_set_id>0 and count($loc_langs_array_clean)>0) {
  $sql="update gks_template_html set localization_set_id=".$localization_set_id."
  where id_template_html in (".implode(',',$loc_langs_array_clean).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
}


$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







