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
$my_page_title=gks_lang('Αποθήκευση Ημερολογίου').' id:' . $id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_journal',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}








if ($id>0) {
  $sql="select * from gks_acc_journal where id_acc_journal=".$id." limit 1";
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
$acc_journal_code=''; if (isset($_POST['acc_journal_code'])) $acc_journal_code=trim_gks(base64_decode($_POST['acc_journal_code']));
$acc_journal_descr=''; if (isset($_POST['acc_journal_descr'])) $acc_journal_descr=trim_gks(base64_decode($_POST['acc_journal_descr']));
$acc_eidos_parastatikou_id=0; if (isset($_POST['acc_eidos_parastatikou_id'])) $acc_eidos_parastatikou_id=intval($_POST['acc_eidos_parastatikou_id']);
$sortorder=0; if (isset($_POST['sortorder'])) $sortorder=intval($_POST['sortorder']);
$whi_id=0;if (isset($_POST['whi_id'])) $whi_id=intval($_POST['whi_id']);
$acc_eidos_parastatikou_other_entity=0;if (isset($_POST['other_entity'])) $acc_eidos_parastatikou_other_entity=intval($_POST['other_entity']);
$journal_has_correlated_invoices=0;if (isset($_POST['correlated_invoices'])) $journal_has_correlated_invoices=intval($_POST['correlated_invoices']);
$journal_has_multiple_connected_marks=0;if (isset($_POST['multiple_connected_marks'])) $journal_has_multiple_connected_marks=intval($_POST['multiple_connected_marks']);
$journal_has_packings_declarations=0;if (isset($_POST['packings_declarations'])) $journal_has_packings_declarations=intval($_POST['packings_declarations']);

$is_disable=0; if (isset($_POST['is_disable'])) $is_disable=intval($_POST['is_disable']);



  
if ($company_id<=0) {debug_mail(false,'emptyl',                  gks_lang('Ορίστε την εταιρεία'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την εταιρεία')));
  echo json_encode($return); die();}





if ($acc_journal_code=='') {debug_mail(false,'empty code',       gks_lang('Ορίστε τον Κωδικό για το Ημερολόγιο'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Κωδικό για το Ημερολόγιο')));
  echo json_encode($return); die();}

if ($acc_journal_descr=='') {debug_mail(false,'emptyl',          gks_lang('Ορίστε την Περιγραφή του Ημερολογίου'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή του Ημερολογίου')));
  echo json_encode($return); die();}


if ($acc_eidos_parastatikou_id<=0) {debug_mail(false,'emptyl',   gks_lang('Επιλέξτε τον τύπου παραστατικού'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον τύπου παραστατικού')));
  echo json_encode($return); die();}


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




if (in_array($acc_eidos_parastatikou_id,[16,24,51,82,702,703,704,803,813,850,903,913])) {
  if ($journal_has_correlated_invoices==0) {
    debug_mail(false,'emptyl',                                     gks_lang('Για αυτό τον τύπο παραστατικού θα πρέπει να είναι ενεργοποιημένο το <b>Συσχετιζόμενα Παραστατικά</b>'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Για αυτό τον τύπο παραστατικού θα πρέπει να είναι ενεργοποιημένο το <b>Συσχετιζόμενα Παραστατικά</b>')));
    echo json_encode($return); die();}    
}


$sql="select * from gks_acc_journal 
where acc_journal_code like '".$db_link->escape_string($acc_journal_code)."' 
and id_acc_journal<>".$id."
and company_id=".$company_id."
and company_sub_id=".$company_sub_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το ημερολόγιο με κωδικό <b>[1]</b> υπάρχει ήδη για αυτήν την εταιρεία/υποκατάστημα:<br><a href="admin-acc_journal-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$acc_journal_code,$message);
  $message=str_replace('[2]',$row['id_acc_journal'],$message);
  debug_mail(false,'journal exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}


$sql="select * from gks_acc_journal 
where acc_journal_descr like '".$db_link->escape_string($acc_journal_descr)."' 
and id_acc_journal<>".$id."
and company_id=".$company_id."
and company_sub_id=".$company_sub_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το ημερολόγιο με όνομα <b>[1]</b> υπάρχει ήδη για αυτήν την εταιρεία/υποκατάστημα:<br><a href="admin-acc_journal-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$acc_journal_descr,$message);
  $message=str_replace('[2]',$row['id_acc_journal'],$message);
  debug_mail(false,'journal exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

$sql="select * from gks_acc_eidi_parastatikon 
where id_acc_eidos_parastatikou=".$acc_eidos_parastatikou_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows==0) {
  debug_mail(false,'journal exist',                              gks_lang('Δεν βρέθηκε ο Τύπος Παραστατικού'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο Τύπος Παραστατικού')));
  echo json_encode($return); die();
}
$row = $result->fetch_assoc();
$acc_eidos_parastatikou_whi_id=intval($row['eidos_parastatikou_whi_type_id']);
if ($whi_id!=0) {
  $acc_eidos_parastatikou_whi_id=$acc_eidos_parastatikou_whi_id; //tipota
} else {
  $acc_eidos_parastatikou_whi_id=0;
}
$eidos_parastatikou_other_entity=intval($row['eidos_parastatikou_other_entity']);
if ($eidos_parastatikou_other_entity==0) $acc_eidos_parastatikou_other_entity=0;

$eidos_parastatikou_correlated_invoices=intval($row['eidos_parastatikou_correlated_invoices']);
if ($eidos_parastatikou_correlated_invoices==0) $journal_has_correlated_invoices=0;

$eidos_parastatikou_multiple_connected_marks=intval($row['eidos_parastatikou_multiple_connected_marks']);
if ($eidos_parastatikou_multiple_connected_marks==0) $journal_has_multiple_connected_marks=0;

$eidos_parastatikou_packings_declarations=intval($row['eidos_parastatikou_packings_declarations']);
if ($eidos_parastatikou_packings_declarations==0) $journal_has_packings_declarations=0;




$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_acc_journal');

$redirect='';
if ($id==-1) {
  $sql="insert into gks_acc_journal (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-acc_journal-item.php?id='.$id); 
}



$sql="update gks_acc_journal set 
company_id=".$company_id.",
company_sub_id=".$company_sub_id.",
acc_journal_code='".$db_link->escape_string($acc_journal_code)."',
acc_journal_descr='".$db_link->escape_string($acc_journal_descr)."',
acc_eidos_parastatikou_id=".$acc_eidos_parastatikou_id.",
acc_eidos_parastatikou_whi_id=".$acc_eidos_parastatikou_whi_id.",
acc_eidos_parastatikou_other_entity=".$acc_eidos_parastatikou_other_entity.",
journal_has_correlated_invoices=".$journal_has_correlated_invoices.",
journal_has_multiple_connected_marks=".$journal_has_multiple_connected_marks.",
journal_has_packings_declarations=".$journal_has_packings_declarations.",
sortorder=".$sortorder.",
is_disable=".$is_disable.",

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_acc_journal = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }


$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

gks_lang_data_obj_save_exec_php('gks_acc_journal',$id);

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







