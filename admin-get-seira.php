<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

if (!isset($_POST['acc_journal_id'])) {
  debug_mail(false,'error on acc_journal_id');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' acc_journal_id<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}
$acc_journal_id=intval($_POST['acc_journal_id']);

if ($acc_journal_id<=0) {
  debug_mail(false,'error on id (2):'.$acc_journal_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' acc_journal_id<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die(); }



$my_page_title=gks_lang('Λήψη λίστας σειρών');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_seires','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');


$out=array();

$sql="SELECT id_acc_seira, seira_code, seira_descr,
is_xeirografi,
seira_isdeliverynote,
seira_is_reverse_delivery_note,
seira_is_self_pricing,
seira_is_vat_payment_suspension
FROM gks_acc_seires 
WHERE is_disable=0 
and acc_journal_id=".$acc_journal_id;
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_seires.id_acc_seira in (".implode(',',$perm_id_acc_seira_ids).")";


$sql.=" ORDER BY sortorder,seira_code";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }

while ($row = $result->fetch_assoc()) {  
  $out[]=array(
    'id' => $row['id_acc_seira'], 
    'descr'=> $row['seira_code'].' - '.$row['seira_descr'],
    'is_xeirografi'=>intval($row['is_xeirografi']),
    'is_deliverynote'=>intval($row['seira_isdeliverynote']),
    'is_reverse_delivery_note'=>intval($row['seira_is_reverse_delivery_note']),
    'is_self_pricing'=>intval($row['seira_is_self_pricing']),
    'is_vat_payment_suspension'=>intval($row['seira_is_vat_payment_suspension']),
  );
}
  
$return = array('success' => true, 'message' => base64_encode('ok'),'out' => $out);
echo json_encode($return); die();  
