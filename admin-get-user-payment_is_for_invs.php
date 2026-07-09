<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Λήψη δεδομένων επαφής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);

$acc_pay_id=0;  if (isset($_POST['acc_pay_id'])) $acc_pay_id=intval($_POST['acc_pay_id']);
$pay_acc_journal_id=0;if (isset($_POST['pay_acc_journal_id'])) $pay_acc_journal_id=intval($_POST['pay_acc_journal_id']);

$html=gks_get_user_payment_is_for_invs($id, array(),$acc_pay_id,0,'010draft',$pay_acc_journal_id);

$return = array('success' => true, 'message' => base64_encode('OK'), 'html'=>$html);
echo json_encode($return); die();
