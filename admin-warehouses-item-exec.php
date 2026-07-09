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
$my_page_title=gks_lang('Αποθήκευση αποθήκης');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_warehouses',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_warehouse_ids=gks_permission_user_condition($my_wp_user_id,'gks_warehouses','01');






if ($id>0) {
  $sql="select * from gks_warehouses where gks_warehouses.is_virtual=0 and id_warehouse=".$id;
  if (count($perm_id_warehouse_ids)>0) $sql.=" and gks_warehouses.id_warehouse in (".implode(',',$perm_id_warehouse_ids).")";
  $sql.=" limit 1";
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


$company_id=0; if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$company_sub_id=0; if (isset($_POST['company_sub_id'])) $company_sub_id=intval($_POST['company_sub_id']);
$warehouse_is_company_place=0; if (isset($_POST['warehouse_is_company_place'])) $warehouse_is_company_place=intval($_POST['warehouse_is_company_place']);
$warehouse_name=''; if (isset($_POST['warehouse_name'])) $warehouse_name=trim_gks(base64_decode($_POST['warehouse_name']));
$warehouse_topos_fortosis=''; if (isset($_POST['warehouse_topos_fortosis'])) $warehouse_topos_fortosis=trim_gks(base64_decode($_POST['warehouse_topos_fortosis']));
$warehouse_phone=''; if (isset($_POST['warehouse_phone'])) $warehouse_phone=trim_gks(base64_decode($_POST['warehouse_phone']));
$warehouse_email=''; if (isset($_POST['warehouse_email'])) $warehouse_email=trim_gks(base64_decode($_POST['warehouse_email']));
$warehouse_website=''; if (isset($_POST['warehouse_website'])) $warehouse_website=trim_gks(base64_decode($_POST['warehouse_website']));
$warehouse_branch=''; if (isset($_POST['warehouse_branch'])) $warehouse_branch=trim_gks(base64_decode($_POST['warehouse_branch']));
if ($warehouse_branch!='') $warehouse_branch=intval($warehouse_branch).'';
$warehouse_odos=''; if (isset($_POST['warehouse_odos'])) $warehouse_odos=trim_gks(base64_decode($_POST['warehouse_odos']));
$warehouse_arithmos=''; if (isset($_POST['warehouse_arithmos'])) $warehouse_arithmos=trim_gks(base64_decode($_POST['warehouse_arithmos']));
$warehouse_orofos=''; if (isset($_POST['warehouse_orofos'])) $warehouse_orofos=trim_gks(base64_decode($_POST['warehouse_orofos']));
$warehouse_perioxi=''; if (isset($_POST['warehouse_perioxi'])) $warehouse_perioxi=trim_gks(base64_decode($_POST['warehouse_perioxi']));
$warehouse_poli=''; if (isset($_POST['warehouse_poli'])) $warehouse_poli=trim_gks(base64_decode($_POST['warehouse_poli']));
$warehouse_tk=''; if (isset($_POST['warehouse_tk'])) $warehouse_tk=trim_gks(base64_decode($_POST['warehouse_tk']));
$warehouse_country_id=0; if (isset($_POST['warehouse_country_id'])) $warehouse_country_id=intval($_POST['warehouse_country_id']);
$warehouse_nomos_id=0; if (isset($_POST['warehouse_nomos_id'])) $warehouse_nomos_id=intval($_POST['warehouse_nomos_id']);
$warehouse_map_latitude=0; if (isset($_POST['warehouse_map_latitude'])) $warehouse_map_latitude=floatval(str_replace(',','.', $_POST['warehouse_map_latitude']));
$warehouse_map_longitude=0; if (isset($_POST['warehouse_map_longitude'])) $warehouse_map_longitude=floatval(str_replace(',','.', $_POST['warehouse_map_longitude']));
$warehouse_disable=0; if (isset($_POST['warehouse_disable'])) $warehouse_disable=intval($_POST['warehouse_disable']);
$warehouse_can_pelatis_paralavei=0; if (isset($_POST['warehouse_can_pelatis_paralavei'])) $warehouse_can_pelatis_paralavei=intval($_POST['warehouse_can_pelatis_paralavei']);
$warehouse_color=''; if (isset($_POST['warehouse_color'])) $warehouse_color=trim_gks(base64_decode($_POST['warehouse_color']));

$warehouse_sortorder=0; if (isset($_POST['warehouse_sortorder'])) $warehouse_sortorder=intval($_POST['warehouse_sortorder']);


if ($company_id<=0 and $warehouse_is_company_place !=0) {
  debug_mail(false,'emptyl',                                     gks_lang('Ορίστε την εταιρεία'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την εταιρεία')));
  echo json_encode($return); die();}

if ($company_sub_id<=0) $company_sub_id=0;

if ($warehouse_name=='') {debug_mail(false,'emptyl',             gks_lang('Ορίστε τον Τίτλο της αποθήκης'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Τίτλο της αποθήκης')));
  echo json_encode($return); die();}



if ($warehouse_email != '' and !filter_var($warehouse_email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,'email is not ok: '.$warehouse_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  echo json_encode($return); die();}

//if ($warehouse_phone != '' and (strlen($warehouse_phone) != 10 or substr($warehouse_phone,0,1) != '2') ) {
//  debug_mail(false,'phone is not OK. : '.$warehouse_phone);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό')));
//  echo json_encode($return); die();}  

  
//if ($warehouse_country_id==0) {debug_mail(false,'emptyl',        gks_lang('Επιλέξτε μία χώρα'));
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε μία χώρα')));
//  echo json_encode($return); die();}

//if ($warehouse_nomos_id==0) {debug_mail(false,'emptyl',          gks_lang('Επιλέξτε έναν νομό'));
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε έναν νομό')));
//  echo json_encode($return); die();}

if ($company_id>0 and $company_sub_id>0) {
  $sql="select * from gks_company_subs where company_id=".$company_id." and id_company_sub=".$company_sub_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }    
  if ($result->num_rows==0) {
    debug_mail(false,'emptyl',                                     gks_lang('Αυτό το υποκατάστημα δεν ανήκει σε αυτήν την εταιρεία'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το υποκατάστημα δεν ανήκει σε αυτήν την εταιρεία')));
    echo json_encode($return); die();}

}

if ($warehouse_is_company_place != 0) {
  $sql="select id_warehouse from gks_warehouses where warehouse_is_company_place<>0 and company_id=".$company_id." and company_sub_id=".$company_sub_id." and id_warehouse<>".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }     
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();  
    $message=gks_lang('Αυτή η αποθήκη υπάρχει ήδη.<br><a class="gks_link" href="admin-warehouses-item.php?id=[1]">Μετάβαση</a>');
    $message=str_replace('[1]',$row['id_warehouse'],$message);    
    debug_mail(false,'emptyl',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die(); }
}

$sql="select * from gks_warehouses where warehouse_name like '".$db_link->escape_string($warehouse_name)."' and id_warehouse<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η αποθήκη με όνομα <b>[1]</b> υπάρχει ήδη:<br><a href="admin-warehouses-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$warehouse_name,$message);    
  $message=str_replace('[2]',$row['id_warehouse'],$message);    

  debug_mail(false,'warehouse exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_warehouses');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_warehouses (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-warehouses-item.php?id='.$id); 
  
}



$sql="update gks_warehouses set 
company_id=".$company_id.",
company_sub_id=".$company_sub_id.",
warehouse_is_company_place=".$warehouse_is_company_place.",
warehouse_name='".$db_link->escape_string($warehouse_name)."',
warehouse_topos_fortosis='".$db_link->escape_string($warehouse_topos_fortosis)."',
warehouse_phone=". ($warehouse_phone =='' ? 'null' : "'".$db_link->escape_string($warehouse_phone)."'").",
warehouse_email=". ($warehouse_email =='' ? 'null' : "'".$db_link->escape_string($warehouse_email)."'").",
warehouse_website=". ($warehouse_email =='' ? 'null' : "'".$db_link->escape_string($warehouse_website)."'").",
warehouse_branch = ".($warehouse_branch=='' ? 'null' : $warehouse_branch).",
warehouse_odos=". ($warehouse_odos =='' ? 'null' : "'".$db_link->escape_string($warehouse_odos)."'").",
warehouse_arithmos=". ($warehouse_arithmos =='' ? 'null' : "'".$db_link->escape_string($warehouse_arithmos)."'").",
warehouse_orofos=". ($warehouse_orofos =='' ? 'null' : "'".$db_link->escape_string($warehouse_orofos)."'").",
warehouse_perioxi=". ($warehouse_perioxi =='' ? 'null' : "'".$db_link->escape_string($warehouse_perioxi)."'").",
warehouse_poli=". ($warehouse_poli =='' ? 'null' : "'".$db_link->escape_string($warehouse_poli)."'").",
warehouse_tk=". ($warehouse_tk =='' ? 'null' : "'".$db_link->escape_string($warehouse_tk)."'").",
warehouse_country_id=".$warehouse_country_id.",
warehouse_nomos_id=".$warehouse_nomos_id.",

warehouse_map_latitude='".number_format($warehouse_map_latitude,16,'.','')."',
warehouse_map_longitude='".number_format($warehouse_map_longitude,16,'.','')."',

warehouse_disable=".$warehouse_disable.",
warehouse_can_pelatis_paralavei=".$warehouse_can_pelatis_paralavei.",
warehouse_color=". ($warehouse_color =='' ? 'null' : "'".$db_link->escape_string($warehouse_color)."'").",
warehouse_sortorder=".$warehouse_sortorder.",

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_warehouse = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }


$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

gks_lang_data_obj_save_exec_php('gks_warehouses',$id);

$ret_run=gks_sociallinks_item_save($_POST,'gks_warehouses',$id);
if ($ret_run['success']==false) {die(json_encode(array('success'=>false,'message'=>base64_encode($ret_run['message']))));}


if ($company_sub_id>0) gks_warehouse_address_update(array('id_company_sub' => $company_sub_id));
else if ($company_id>0) gks_warehouse_address_update(array('id_company' => $company_id));



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







