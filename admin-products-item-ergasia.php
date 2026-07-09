<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$ergasia_id=0;
if (isset($_POST['ergasia_id'])) $ergasia_id=intval($_POST['ergasia_id']);
if ($ergasia_id<=0) {
  debug_mail(false,'the ergasia_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εργασία')));
  echo json_encode($return); die();}

$eidos_id=0;
if (isset($_POST['eidos_id'])) $eidos_id=intval($_POST['eidos_id']);
if ($eidos_id<=0) {
  debug_mail(false,'the eidos_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το είδος')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Προσθήκη εργασίας σε είδος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','edit',$eidos_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$sql="SELECT * FROM gks_eshop_products where id_product = ".$eidos_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty',gks_lang('Δεν βρέθηκε το είδος'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το είδος')));
  echo json_encode($return); die();}  

$sql="SELECT id_production_ergasia FROM gks_production_ergasies where id_production_ergasia = ".$ergasia_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty',gks_lang('Δεν βρέθηκε η εργασία'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εργασία')));
  echo json_encode($return); die();}  




$sql="SELECT * FROM gks_production_ergasies_eidos where production_ergasia_id = ".$ergasia_id." and eidos_id=".$eidos_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'empty',gks_lang('Η εργασία - κατηγορία υπάρχει ήδη').'<br>'.gks_lang('Ανανεώστε την σελίδα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εργασία - είδος υπάρχει ήδη').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}  


$sql="insert into gks_production_ergasies_eidos (eidos_id,production_ergasia_id,
user_id_add,user_id_edit,mydate_add,mydate_edit,myip
) values (
".$eidos_id.",
".$ergasia_id.",
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}

$id_production_ergasies_eidos=$db_link->insert_id; 


$row_html='';
if (isset($_POST['from']) and $_POST['from']=='product') {
$sql_list = "SELECT gks_production_ergasies_eidos.*, gks_production_ergasies.production_ergasia_descr
FROM gks_production_ergasies_eidos
LEFT JOIN gks_production_ergasies ON gks_production_ergasies_eidos.production_ergasia_id = gks_production_ergasies.id_production_ergasia
WHERE id_production_ergasies_eidos=".$id_production_ergasies_eidos;
$result_list = $db_link->query($sql_list); 
if (!$result_list) {
  debug_mail(false,'error sql',$sql_list);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$row_list = $result_list->fetch_assoc();

$row_html=
'<tr class="ergasies_tr_new" data-id="'.$row_list['id_production_ergasies_eidos'].'">'.
  '<th scope="row" nowrap align="right" class="ergasies_aa">*</td>'.
  '<td nowrap align="center">'.
    '<img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_ergasies_delete_after|'.$row_list['id_production_ergasies_eidos'].'" data-id="'.$row_list['id_production_ergasies_eidos'].'" data-model="gks_production_ergasies_eidos">'.
  '</td>'.
  '<td nowrap align="center"><a href="admin-production-ergasies-item.php?id='.$row_list['production_ergasia_id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a></td>'.
  '<td nowrap>'.$row_list['production_ergasia_descr'].'</td>'.
  '<td nowrap>'.showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1).'</td>'.
'</tr>';

}

$return = array('success' => true, 'message' => base64_encode('OK'),'row_html'=>base64_encode($row_html));
echo json_encode($return); die();