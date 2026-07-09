<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

if (!isset($_POST['company_id'])) {
  debug_mail(false,'error on company_id');
  $return = array('success' => false, 'message' => base64_encode('error on company_id<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}
$company_id=intval($_POST['company_id']);

if ($company_id<=0) {
  debug_mail(false,'error on id (2):'.$company_id);
  $return = array('success' => false, 'message' => base64_encode('error on company_id<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die(); }


if (!isset($_POST['company_sub_id'])) {
  debug_mail(false,'error on company_sub_id');
  $return = array('success' => false, 'message' => base64_encode('error on company_sub_id<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die(); }
$company_sub_id=intval($_POST['company_sub_id']);

if ($company_sub_id<0) {
  debug_mail(false,'error on id (2):'.$company_sub_id);
  $return = array('success' => false, 'message' => base64_encode('error on company_sub_id<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die(); } 

$types=''; if (isset($_POST['types'])) $types=trim_gks($_POST['types']);
if ($types!='order' and $types!='inv' and $types!='pay' and $types!='whi' and $types!='reservation' and $types!='transfer') $types='';
if ($types=='') {
  debug_mail(false,'error on types:'.$types);
  $return = array('success' => false, 'message' => base64_encode('error on types<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die(); } 
 

$types_sql='';
if ($types=='order') $types_sql='gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (31,32)';
if ($types=='inv') $types_sql='gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (1,2,5)';
if ($types=='pay') $types_sql='gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (11,12)';
if ($types=='whi') $types_sql='gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (21,22,23,24)';
if ($types=='reservation') $types_sql='gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (1100)';
if ($types=='transfer') $types_sql='gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (2100)';



$my_page_title=gks_lang('Λήψη λίστας ημερολογίων');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_journal','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');


$out=array();

$sql="SELECT id_acc_journal, acc_journal_descr,
acc_eidos_parastatikou_id, gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev,
gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes,
gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda,gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,
gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
acc_eidos_parastatikou_other_entity,
journal_has_correlated_invoices,
journal_has_multiple_connected_marks,
journal_has_packings_declarations
FROM (gks_acc_journal 
LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou not in (702,703,704) 
and ".$types_sql."
and gks_acc_journal.is_disable=0 
and company_id=".$company_id." 
and company_sub_id=".$company_sub_id;
if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_journal.company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_journal.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";

if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_journal.id_acc_journal in (".implode(',',$perm_id_acc_journal_ids).")";

$sql.=" ORDER BY gks_acc_journal.sortorder,acc_journal_descr";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die(); }

while ($row = $result->fetch_assoc()) {  
  $out[]=array(
    'id' => $row['id_acc_journal'], 
    'descr'=> $row['acc_journal_descr'],
    'eidi_id'=> $row['acc_eidos_parastatikou_id'],
    'type_id'=> $row['eidos_parastatikou_type_id'],
    'need_prev'=> $row['eidos_parastatikou_need_prev'],
    'fpa'=> $row['eidos_parastatikou_has_fpa'],
    'othertaxes'=> $row['eidos_parastatikou_has_othertaxes'],
    'esoda'=> $row['eidos_parastatikou_has_esoda'],
    'eksoda'=> $row['eidos_parastatikou_has_eksoda'],
    'need_afm'=> $row['eidos_parastatikou_need_afm'],
    'balance_pros'=> $row['eidos_parastatikou_balance_pros'],
    'stock_pros'=> $row['eidos_parastatikou_stock_pros'],
    'whi_stock_pros'=> intval($row['whi_eidos_parastatikou_stock_pros']), 
    'whi_type_id'=> intval($row['whi_eidos_parastatikou_type_id']),       
    'other_entity'=> intval($row['acc_eidos_parastatikou_other_entity']), 
    'correlated_invoices'=> intval($row['journal_has_correlated_invoices']), 
    'multiple_connected_marks'=> intval($row['journal_has_multiple_connected_marks']), 
    'packings_declarations'=> intval($row['journal_has_packings_declarations']), 
    
    
  );
}
  
$return = array('success' => true, 'message' => base64_encode('ok'),'out' => $out);
echo json_encode($return); die();  
