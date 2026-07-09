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


$my_page_title=gks_lang('Αποθήκευση gks ERP App Mobile').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_erp_app_mobile',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


//if ($id>0) {
//  $sql ="SELECT * FROM gks_erp_app_mobile where id_erp_app_mobile = ".$id;
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die(); }
//  if ($result->num_rows!=1) {
//    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
//    echo json_encode($return); die();  }
//  $row = $result->fetch_assoc();
//  $erp_app_mobile_user_id=intval($row['erp_app_mobile_user_id']);
//}

$erp_app_mobile_name=''; if (isset($_POST['erp_app_mobile_name'])) $erp_app_mobile_name=trim_gks(base64_decode($_POST['erp_app_mobile_name']));
$erp_app_mobile_country=''; if (isset($_POST['erp_app_mobile_country'])) $erp_app_mobile_country=trim_gks(base64_decode($_POST['erp_app_mobile_country']));
$erp_app_mobile_phonenumber=''; if (isset($_POST['erp_app_mobile_phonenumber'])) $erp_app_mobile_phonenumber=trim_gks(base64_decode($_POST['erp_app_mobile_phonenumber']));
$erp_app_mobile_cost_per_sms=''; if (isset($_POST['erp_app_mobile_cost_per_sms'])) $erp_app_mobile_cost_per_sms=floatval($_POST['erp_app_mobile_cost_per_sms']);
$erp_app_mobile_descr=''; if (isset($_POST['erp_app_mobile_descr'])) $erp_app_mobile_descr=trim_gks(base64_decode($_POST['erp_app_mobile_descr']));
$erp_app_mobile_url=''; if (isset($_POST['erp_app_mobile_url'])) $erp_app_mobile_url=trim_gks(base64_decode($_POST['erp_app_mobile_url']));
$erp_app_mobile_port=0; if (isset($_POST['erp_app_mobile_port'])) $erp_app_mobile_port=intval($_POST['erp_app_mobile_port']);
$erp_app_mobile_sortorder=0; if (isset($_POST['erp_app_mobile_sortorder'])) $erp_app_mobile_sortorder=intval($_POST['erp_app_mobile_sortorder']);
$erp_app_mobile_disabled=0; if (isset($_POST['erp_app_mobile_disabled'])) $erp_app_mobile_disabled=intval($_POST['erp_app_mobile_disabled']);
$erp_app_mobile_token_new=0; if (isset($_POST['erp_app_mobile_token_new'])) $erp_app_mobile_token_new=intval($_POST['erp_app_mobile_token_new']);
if ($id<=0) $erp_app_mobile_token_new=0;
$erp_app_mobile_secret=''; if (isset($_POST['erp_app_mobile_secret'])) $erp_app_mobile_secret=trim_gks(base64_decode($_POST['erp_app_mobile_secret']));
$erp_app_mobile_user_id=0; if (isset($_POST['erp_app_mobile_user_id'])) $erp_app_mobile_user_id=intval($_POST['erp_app_mobile_user_id']); 
$erp_app_mobile_can_capture=0; if (isset($_POST['erp_app_mobile_can_capture'])) $erp_app_mobile_can_capture=intval($_POST['erp_app_mobile_can_capture']);
$erp_app_mobile_can_sms=0; if (isset($_POST['erp_app_mobile_can_sms'])) $erp_app_mobile_can_sms=intval($_POST['erp_app_mobile_can_sms']);
$erp_app_mobile_can_gps=0; if (isset($_POST['erp_app_mobile_can_gps'])) $erp_app_mobile_can_gps=intval($_POST['erp_app_mobile_can_gps']);
$erp_app_mobile_gps_dt=0; if (isset($_POST['erp_app_mobile_gps_dt'])) $erp_app_mobile_gps_dt=intval($_POST['erp_app_mobile_gps_dt']);
if ($erp_app_mobile_gps_dt<1) $erp_app_mobile_gps_dt=1;
if ($erp_app_mobile_gps_dt>3600) $erp_app_mobile_gps_dt=3600;
$erp_app_mobile_gps_ds=0; if (isset($_POST['erp_app_mobile_gps_ds'])) $erp_app_mobile_gps_ds=intval($_POST['erp_app_mobile_gps_ds']);
if ($erp_app_mobile_gps_ds<1) $erp_app_mobile_gps_ds=1;
if ($erp_app_mobile_gps_ds>1000) $erp_app_mobile_gps_ds=1000;
$erp_app_mobile_gps_chunk=0; if (isset($_POST['erp_app_mobile_gps_chunk'])) $erp_app_mobile_gps_chunk=intval($_POST['erp_app_mobile_gps_chunk']);
if ($erp_app_mobile_gps_chunk<1) $erp_app_mobile_gps_chunk=1;
if ($erp_app_mobile_gps_chunk>10) $erp_app_mobile_gps_chunk=10;
$erp_app_mobile_gps_timegap=0; if (isset($_POST['erp_app_mobile_gps_timegap'])) $erp_app_mobile_gps_timegap=intval($_POST['erp_app_mobile_gps_timegap']);
if ($erp_app_mobile_gps_timegap<60) $erp_app_mobile_gps_timegap=60;
if ($erp_app_mobile_gps_timegap>21600) $erp_app_mobile_gps_timegap=21600;

$erp_app_mobile_can_pos=0; if (isset($_POST['erp_app_mobile_can_pos'])) $erp_app_mobile_can_pos=intval($_POST['erp_app_mobile_can_pos']);


$erp_app_mobile_pos_list=''; if (isset($_POST['erp_app_mobile_pos_list'])) $erp_app_mobile_pos_list=trim_gks(base64_decode($_POST['erp_app_mobile_pos_list']));
//echo $erp_app_mobile_pos_list; die();
if ($erp_app_mobile_pos_list!='') {
  $parts=explode(']][[',$erp_app_mobile_pos_list);
  $parts_c=array();
  foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
  if (count($parts_c)>0) {
    $sqltags="select id_pos as myid from gks_pos where id_pos in (".implode(',',$parts_c).")";
    $resulttags = $db_link->query($sqltags);        
    if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
    $rdata=array();
    while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
    $erp_app_mobile_pos_list=serialize($rdata);
    //echo $erp_app_mobile_pos_list;die();
  }
  
}

$erp_app_mobile_can_transfer=0; if (isset($_POST['erp_app_mobile_can_transfer'])) $erp_app_mobile_can_transfer=intval($_POST['erp_app_mobile_can_transfer']);


if ($erp_app_mobile_name=='') {debug_mail(false,'emptyl',        gks_lang('Το όνομα δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

$erp_app_mobile_country_clean='';
for ($i=0; $i < strlen($erp_app_mobile_country); $i++) {
  if (in_array($erp_app_mobile_country[$i],array('+','0','1','2','3','4','5','6','7','8','9'))) {
    $erp_app_mobile_country_clean.=$erp_app_mobile_country[$i];
  }
}
$erp_app_mobile_country=$erp_app_mobile_country_clean;

$erp_app_mobile_phonenumber_clean='';
for ($i=0; $i < strlen($erp_app_mobile_phonenumber); $i++) {
  if (in_array($erp_app_mobile_phonenumber[$i],array('0','1','2','3','4','5','6','7','8','9'))) {
    $erp_app_mobile_phonenumber_clean.=$erp_app_mobile_phonenumber[$i];
  }
}
$erp_app_mobile_phonenumber=$erp_app_mobile_phonenumber_clean;
//echo '<pre>'.$erp_app_mobile_phonenumber_clean;die();



$sql="select * from gks_erp_app_mobile where erp_app_mobile_name like '".$db_link->escape_string($erp_app_mobile_name)."' and id_erp_app_mobile<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το όνομα <b>[1]</b> υπάρχει ήδη:<br><br><a href="admin-erp-app-mobile-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$erp_app_mobile_descr,$message);
  $message=str_replace('[2]',$row['id_erp_app_mobile'],$message);
  debug_mail(false,'erp-app-item exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

if (strlen($erp_app_mobile_country)<2 or substr($erp_app_mobile_country, 0,1)!='+') {
  debug_mail(false,'emptyl',                                     gks_lang('O κωδικός χώρας θα πρέπει να ξεκινά από + και να έχει τουλάχιστον έναν αριθμό π.χ. +1 ή +30'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('O κωδικός χώρας θα πρέπει να ξεκινά από + και να έχει τουλάχιστον έναν αριθμό π.χ. +1 ή +30')));
  echo json_encode($return); die(); }  
  

if (strlen($erp_app_mobile_phonenumber)<10 or startwith($erp_app_mobile_phonenumber,$erp_app_mobile_country) or startwith($erp_app_mobile_phonenumber,substr($erp_app_mobile_country,1))) {
  debug_mail(false,'emptyl',                                     gks_lang('O αριθμός πρέπει να είναι 10 ψηφία και να ξεκινά μην ξεκινά από τον κωδικό χώρας'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('O αριθμός πρέπει να είναι 10 ψηφία και να ξεκινά μην ξεκινά από τον κωδικό χώρας')));
  echo json_encode($return); die(); }
  
$sql="select * from gks_erp_app_mobile where erp_app_mobile_phonenumber like '".$db_link->escape_string($erp_app_mobile_phonenumber)."' and id_erp_app_mobile<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Ο Αριθμός <b>[1]</b> υπάρχει ήδη:<br><br><a href="admin-erp-app-mobile-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$erp_app_mobile_phonenumber,$message);
  $message=str_replace('[2]',$row['id_erp_app_mobile'],$message);
  debug_mail(false,'erp-app-item exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}





if ($erp_app_mobile_cost_per_sms<0) $erp_app_mobile_cost_per_sms=0;

if ($erp_app_mobile_user_id==0 and strlen($erp_app_mobile_secret)<32) {
  debug_mail(false,'emptyl',                                     gks_lang('Το Ιδιωτικό Κλειδί πρέπει να είναι τουλάχιστον 32 χαρακτήρες'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Ιδιωτικό Κλειδί πρέπει να είναι τουλάχιστον 32 χαρακτήρες')));
  echo json_encode($return); die(); }


if ($erp_app_mobile_user_id==0 and $erp_app_mobile_url=='') {
  debug_mail(false,'emptyl',                                     gks_lang('Το Url δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το Url δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }
  
if ($erp_app_mobile_port<10000 or $erp_app_mobile_port>65000) {
  debug_mail(false,'emptyl',                                     gks_lang('Η πόρτα πρέπει να είναι από 10000 έως 65000'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η πόρτα πρέπει να είναι από 10000 έως 65000')));
  echo json_encode($return); die(); }
  
$create_token=false;
if ($id==-1 or $erp_app_mobile_token_new==1) $create_token=true;
if ($create_token) {
  $erp_app_mobile_token='';
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
    $erp_app_mobile_token=$server_output;
  } else {
    debug_mail(false,'emptyl',                                     gks_lang('Δεν μπορεί να δημιουργηθεί το κλειδί').'<br>'.gks_lang('Δοκιμάστε αργότερα'),server_output);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί το κλειδί').'<br>'.gks_lang('Δοκιμάστε αργότερα').'<br>'.$server_output));
    echo json_encode($return); die();
  }

}


if ($erp_app_mobile_url!='' and $erp_app_mobile_url!='frp') {
  $sql="select * from gks_erp_app_mobile where erp_app_mobile_url like '".$db_link->escape_string($erp_app_mobile_url)."' and erp_app_mobile_port=".$erp_app_mobile_port." and id_erp_app_mobile<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Υπάρχει ήδη εφαρμογή με το ίδιο URL και την ίδια πόρτα:<br><br><a href="admin-erp-app-mobile-item.php?id=[1]" class="gks_link">Προβολή</a>');
    $message=str_replace('[1]',$row['id_erp_app_mobile'],$message);
    debug_mail(false,'erp-app-item exist symbol',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();
  }
  
  
} 

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_erp_app_mobile');



$redirect='';
if ($id==-1) {
  $sql="insert into gks_erp_app_mobile (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-erp-app-mobile-item.php?id='.$id); 
}

$sql="update gks_erp_app_mobile set 
".($create_token ? "erp_app_mobile_token='".$db_link->escape_string($erp_app_mobile_token)."'," : '')."
erp_app_mobile_country='".$db_link->escape_string($erp_app_mobile_country)."',
erp_app_mobile_phonenumber='".$db_link->escape_string($erp_app_mobile_phonenumber)."',
erp_app_mobile_cost_per_sms=".$erp_app_mobile_cost_per_sms.",
erp_app_mobile_descr='".$db_link->escape_string($erp_app_mobile_descr)."',
erp_app_mobile_name='".$db_link->escape_string($erp_app_mobile_name)."',
erp_app_mobile_secret='".$db_link->escape_string($erp_app_mobile_secret)."',
erp_app_mobile_user_id=".$erp_app_mobile_user_id.",
erp_app_mobile_url='".$db_link->escape_string($erp_app_mobile_url)."',
erp_app_mobile_port=".$erp_app_mobile_port.",
erp_app_mobile_sortorder=".$erp_app_mobile_sortorder.",
erp_app_mobile_disabled=".$erp_app_mobile_disabled.",
erp_app_mobile_can_capture=".$erp_app_mobile_can_capture.",
erp_app_mobile_can_sms=".$erp_app_mobile_can_sms.",
erp_app_mobile_can_gps=".$erp_app_mobile_can_gps.",
erp_app_mobile_gps_dt=".$erp_app_mobile_gps_dt.",
erp_app_mobile_gps_ds=".$erp_app_mobile_gps_ds.",
erp_app_mobile_gps_chunk=".$erp_app_mobile_gps_chunk.",
erp_app_mobile_gps_timegap=".$erp_app_mobile_gps_timegap.",
erp_app_mobile_can_pos=".$erp_app_mobile_can_pos.",
erp_app_mobile_pos_list='".$db_link->escape_string($erp_app_mobile_pos_list)."',";

if (GKS_TRANSFER) {
  $sql.="erp_app_mobile_can_transfer=".$erp_app_mobile_can_transfer.",";
}
  

$sql.="user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_erp_app_mobile = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();




