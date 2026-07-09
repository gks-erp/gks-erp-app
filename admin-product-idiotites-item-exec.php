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


$my_page_title=gks_lang('Αποθήκευση Ιδιότητας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_product_idiotites',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_product_idiotites where id_product_idiotita = ".$id;
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

$idiotita_name=''; if (isset($_POST['idiotita_name'])) $idiotita_name=trim_gks(base64_decode($_POST['idiotita_name']));
$idiotita_descr=''; if (isset($_POST['idiotita_descr'])) $idiotita_descr=trim_gks(base64_decode($_POST['idiotita_descr']));
$idiotita_type=''; if (isset($_POST['idiotita_type'])) $idiotita_type=trim_gks(base64_decode($_POST['idiotita_type']));
$idiotita_sortorder=0; if (isset($_POST['idiotita_sortorder'])) $idiotita_sortorder=intval($_POST['idiotita_sortorder']);


if ($idiotita_name=='') {debug_mail(false,'emptyl',              gks_lang('Το όνομα δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

if ($idiotita_type!='10button' and $idiotita_type!='20color' and $idiotita_type!='30image') $idiotita_type='';
if ($idiotita_type=='') {debug_mail(false,'emptyl',              gks_lang('Ο τύπος δεν μπορεί να είναι κενός'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο τύπος δεν μπορεί να είναι κενός')));
  echo json_encode($return); die(); }




$sql="select * from gks_product_idiotites where idiotita_name like '".$db_link->escape_string($idiotita_name)."' and id_product_idiotita<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=str_replace('[1]',$idiotita_name,gks_lang('Η ιδιότητα με όνομα <b>[1]</b> υπάρχει ήδη')).':<br><br><a href="admin-product-idiotites-item.php?id='.$row['id_product_idiotita'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
  debug_mail(false,'idiotita term exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}



$redirect='';
if ($id==-1) {
  $sql="insert into gks_product_idiotites (mydate_add,mydate_edit,user_id_add,user_id_edit,myip) values (now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-product-idiotites-item.php?id='.$id); 
}

$sql="update gks_product_idiotites set 
idiotita_name='".$db_link->escape_string($idiotita_name)."',
idiotita_descr='".$db_link->escape_string($idiotita_descr)."',
idiotita_type='".$db_link->escape_string($idiotita_type)."',
idiotita_sortorder=".$idiotita_sortorder.",
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_product_idiotita = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

gks_lang_data_obj_save_exec_php('gks_product_idiotites',$id);
  
$GKS_IDIOTITES_CACHE_VER=time();
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_IDIOTITES_CACHE_VER','".$db_link->escape_string($GKS_IDIOTITES_CACHE_VER)."')";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  
$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

