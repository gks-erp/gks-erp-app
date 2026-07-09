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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_product_idiotites_terms',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



if ($id>0) {
  $sql ="SELECT * FROM gks_product_idiotites_terms where id_product_idiotita_term = ".$id;
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

$idiotita_id=''; if (isset($_POST['idiotita_id'])) $idiotita_id=intval($_POST['idiotita_id']);
$idiotita_term_name=''; if (isset($_POST['idiotita_term_name'])) $idiotita_term_name=trim_gks(base64_decode($_POST['idiotita_term_name']));
$idiotita_term_descr=''; if (isset($_POST['idiotita_term_descr'])) $idiotita_term_descr=trim_gks(base64_decode($_POST['idiotita_term_descr']));
$idiotita_term_button=''; if (isset($_POST['idiotita_term_button'])) $idiotita_term_button=trim_gks(base64_decode($_POST['idiotita_term_button']));
$idiotita_term_color=''; if (isset($_POST['idiotita_term_color'])) $idiotita_term_color=trim_gks(base64_decode($_POST['idiotita_term_color']));
$idiotita_term_image=''; if (isset($_POST['idiotita_term_image'])) $idiotita_term_image=trim_gks(base64_decode($_POST['idiotita_term_image']));
$idiotita_term_sortorder=0; if (isset($_POST['idiotita_term_sortorder'])) $idiotita_term_sortorder=intval($_POST['idiotita_term_sortorder']);


if ($idiotita_id<=0) {debug_mail(false,'emptyl',                 gks_lang('Ορίστε την ιδιότητα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την ιδιότητα')));
  echo json_encode($return); die(); }

$sql="select * from gks_product_idiotites where id_product_idiotita=".$idiotita_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
if ($result->num_rows!=1) {  debug_mail(false,'error sql',       gks_lang('Δεν βρέθηκε η ιδιότητα.<br>Ανανεώστε την σελίδα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η ιδιότητα.<br>Ανανεώστε την σελίδα')));
  echo json_encode($return); die();  }

$row_idiotita = $result->fetch_assoc();
$idiotita_type=$row_idiotita['idiotita_type'];

if ($idiotita_term_name=='') {debug_mail(false,'emptyl',         gks_lang('Το όνομα δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το όνομα δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

if ($idiotita_type=='10button') {
  $idiotita_term_color='';
  $idiotita_term_image='';
  if ($idiotita_term_button=='') {debug_mail(false,'emptyl',       gks_lang('Ορίστε το κουμπί'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το κουμπί')));
    echo json_encode($return); die(); }
}
if ($idiotita_type=='20color') {
  $idiotita_term_button='';
  $idiotita_term_image='';
  if ($idiotita_term_color=='') {debug_mail(false,'emptyl',        gks_lang('Ορίστε το χρώμα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το χρώμα')));
    echo json_encode($return); die(); }
}
if ($idiotita_type=='30image') {
  $idiotita_term_button='';
  $idiotita_term_color='';
  if ($idiotita_term_image=='') {debug_mail(false,'emptyl',        gks_lang('Ορίστε την φωτογραφία'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την φωτογραφία')));
    echo json_encode($return); die(); }
}




$sql="select * from gks_product_idiotites_terms where idiotita_term_name like '".$db_link->escape_string($idiotita_term_name)."' and id_product_idiotita_term<>".$id." and idiotita_id=".$idiotita_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=str_replace('[1]',$idiotita_term_name,gks_lang('Ο όρος με όνομα <b>[1]</b> υπάρχει ήδη')).':<br><br><a href="admin-product-idiotites-term-item.php?id='.$row['id_product_idiotita_term'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
  debug_mail(false,'idiotita exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

//$return = array('success' => false, 'message' => base64_encode('errrrorrr'));
//echo json_encode($return); die(); 



$redirect='';
if ($id==-1) {
  $sql="insert into gks_product_idiotites_terms (mydate_add,mydate_edit,user_id_add,user_id_edit,myip) values (now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-product-idiotites-term-item.php?id='.$id); 
}

$sql="update gks_product_idiotites_terms set 
idiotita_id=".$idiotita_id.",
idiotita_term_name='".$db_link->escape_string($idiotita_term_name)."',
idiotita_term_descr='".$db_link->escape_string($idiotita_term_descr)."',
idiotita_term_button='".$db_link->escape_string($idiotita_term_button)."',
idiotita_term_color='".$db_link->escape_string($idiotita_term_color)."',
idiotita_term_image='".$db_link->escape_string($idiotita_term_image)."',
idiotita_term_sortorder=".$idiotita_term_sortorder.",
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_product_idiotita_term = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
gks_lang_data_obj_save_exec_php('gks_product_idiotites_terms',$id);


$GKS_IDIOTITES_CACHE_VER=time();
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_IDIOTITES_CACHE_VER','".$db_link->escape_string($GKS_IDIOTITES_CACHE_VER)."')";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  
$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

