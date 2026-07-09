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

$my_page_title=gks_lang('Αποθήκευση Γλώσσας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_lang',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$id_lang=''; if (isset($_POST['id_lang'])) $id_lang=trim_gks(base64_decode($_POST['id_lang']));
$lang_name=''; if (isset($_POST['lang_name'])) $lang_name=trim_gks(base64_decode($_POST['lang_name']));
$lang_ico=''; if (isset($_POST['lang_ico'])) $lang_ico=trim_gks(base64_decode($_POST['lang_ico']));
$lang_on_backend=0; if (isset($_POST['lang_on_backend'])) $lang_on_backend=intval($_POST['lang_on_backend']);


if ($id>0) {
  $sql ="SELECT * FROM gks_lang where idd_lang = ".$id;
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
  
  if ($row['id_lang']=='el-GR' or $row['id_lang']=='en-US') $id_lang=$row['id_lang'];
}



if ($id_lang=='') {debug_mail(false,'emptyl',                    gks_lang('Ο κωδικός δεν μπορεί να είναι κενός'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο κωδικός δεν μπορεί να είναι κενός')));
  echo json_encode($return); die(); }

$sql="select * from gks_lang where id_lang like '".$db_link->escape_string($id_lang)."' and idd_lang<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Ο κωδικός γλώσσας <b>[1]</b> υπάρχει ήδη:<br><a href="admin-lang-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$id_lang,$message);
  $message=str_replace('[2]',$row['idd_lang'],$message);
  
  debug_mail(false,'lang exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

$id_lang_ok=false;
$temp_id_lang='';
$letter_array=array('-','q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m');

for ($ii=0; $ii< strlen($id_lang);$ii++) {
  $letter=substr($id_lang, $ii, 1);
  if (in_array($letter,$letter_array)) $temp_id_lang.=$letter;
  else if (in_array(strtolower($letter),$letter_array)) $temp_id_lang.=$letter;
} 

//echo $temp_id_lang;die();
if (strlen($temp_id_lang)==5 and
    substr($id_lang, 2,1)=='-' and
    substr($id_lang, 0,2)==strtolower(substr($id_lang, 0,2)) and
    substr($id_lang, 3,2)==strtoupper(substr($id_lang, 3,2))) {
  $id_lang_ok=true;
  
}
if ($id_lang_ok==false) {
  debug_mail(false,'lang code is not ok','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο κωδικός δεν έχει την σωστή μορφή')));
  echo json_encode($return); die();}


if ($lang_name=='') {debug_mail(false,'emptyl',                  gks_lang('Η γλώσσα δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η γλώσσα δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }

$sql="select * from gks_lang where lang_name like '".$db_link->escape_string($lang_name)."' and idd_lang<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η γλώσσα <b>[1]</b> υπάρχει ήδη:<br><a href="admin-lang-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$lang_name,$message);
  $message=str_replace('[2]',$row['idd_lang'],$message);
 
  
  debug_mail(false,'lang exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}


$redirect='';
if ($id==-1) {
  $sql="insert into gks_lang (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-lang-item.php?id='.$id); 
}



$sql="update gks_lang set ";
if ($id_lang!='el-GR' and $id_lang!='en-US') {
  $sql.="id_lang='".$db_link->escape_string($id_lang)."',";
}
$sql.="
lang_name='".$db_link->escape_string($lang_name)."',
lang_ico='".$db_link->escape_string($lang_ico)."',
lang_on_backend=".$lang_on_backend.",
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where idd_lang = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
//echo '<pre>'.$sql;die();

gks_lang_data_obj_save_exec_php('gks_lang',$id);



$sql="select id_lang from gks_lang where id_lang='el-GR' or lang_on_backend=1 order by lang_sortorder";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
$temp=array();
while ($row = $result->fetch_assoc()) {
  $temp[]=trim_gks($row['id_lang']);
}
  
//a:3:{i:0;s:5:"el-GR";i:1;s:5:"en-US";i:2;s:5:"de-DE";}

$temp=serialize($temp);
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_LANG_DATA_ENABLED','".$db_link->escape_string($temp)."')";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }



 
gks_cache_update_menu_version(-1);

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

