<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');



$asset_id=0;
if (isset($_POST['asset_id'])) $asset_id=intval($_POST['asset_id']);
if ($asset_id<=0) {
  debug_mail(false,'the asset_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το πάγιο')));
  echo json_encode($return); die();}

$transfer_id=0;
if (isset($_POST['transfer_id'])) $transfer_id=intval($_POST['transfer_id']);
if ($transfer_id<=0) {
  debug_mail(false,'the eidos_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' transfer.'));
  echo json_encode($return); die();}

$transfer_oxima_type_id=0;
if (isset($_POST['transfer_oxima_type_id'])) $transfer_oxima_type_id=intval($_POST['transfer_oxima_type_id']);
if ($transfer_oxima_type_id<=0) {
  debug_mail(false,'the transfer_oxima_type_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο τύπος οχήματος')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Προσθήκη παγίου σε transfer με τύπο οχήματος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets','edit',$asset_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="SELECT id_asset FROM gks_assets where id_asset = ".$asset_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty','asset  mpt found');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το πάγιο')));
  echo json_encode($return); die();}  



$sql="SELECT id_transfer FROM gks_transfer where id_transfer = ".$transfer_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty','transfer not found');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το transfer')));
  echo json_encode($return); die();}  

$sql="SELECT id_transfer_oxima_type FROM gks_transfer_oxima_type where id_transfer_oxima_type = ".$transfer_oxima_type_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty','oxima type not found');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ο τύπος οχήματος')));
  echo json_encode($return); die();}  




$sql="SELECT * FROM gks_transfer_oxima2type2transfer where asset_id = ".$asset_id." and transfer_oxima_type_id=".$transfer_oxima_type_id." and transfer_id=".$transfer_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'asset_id and transfer_oxima_type_id exist',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτός ο συνδυασμός υπάρχει ήδη').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}  




$sql="insert into gks_transfer_oxima2type2transfer (
user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
asset_id,transfer_oxima_type_id,transfer_id
) values (
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
".$asset_id.",
".$transfer_oxima_type_id.",
".$transfer_id."
)";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}

$id_transfer_oxima2type2transfer=$db_link->insert_id; 

$row_html='';
if (isset($_POST['from']) and $_POST['from']=='asset') {
$sql_list = "SELECT gks_transfer_oxima2type2transfer.*, 
gks_transfer_oxima_type.transfer_oxima_type_descr, 
gks_transfer.transfer_title
FROM (gks_transfer_oxima2type2transfer 
LEFT JOIN gks_transfer_oxima_type ON gks_transfer_oxima2type2transfer.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type) 
LEFT JOIN gks_transfer ON gks_transfer_oxima2type2transfer.transfer_id = gks_transfer.id_transfer
where id_transfer_oxima2type2transfer=".$id_transfer_oxima2type2transfer;
          
$result_list = $db_link->query($sql_list); 
if (!$result_list) {
  debug_mail(false,'error sql',$sql_list);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$row_list = $result_list->fetch_assoc();

$row_html=
'<tr class="oxima2type2transfer_tr_new" data-id="'.$row_list['id_transfer_oxima2type2transfer'].'">'.
  '<th scope="row" nowrap align="right" class="oxima2type2transfer_aa">*</td>'.
  '<td nowrap align="center">'.
    '<img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_oxima2type2transfer_delete_after|'.$row_list['id_transfer_oxima2type2transfer'].'" data-id="'.$row_list['id_transfer_oxima2type2transfer'].'" data-model="gks_transfer_oxima2type2transfer">'.
  '</td>'.
  '<td nowrap>'.$row_list['transfer_title'].'</td>'.
  '<td nowrap>'.$row_list['transfer_oxima_type_descr'].'</td>'.
  '<td nowrap>'.showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1).'</td>'.
'</tr>';

}

$return = array('success' => true, 'message' => base64_encode('OK'),'row_html'=>base64_encode($row_html));
echo json_encode($return); die();