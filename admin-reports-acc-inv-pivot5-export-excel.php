<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$my_page_title=gks_lang('Αναφορά - Pivot Table - Παραστατικά με Είδη - Εξαγωγή σε Excel');
$nav_active_array=array('accounting','accounting_inv_pivot5');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv_pivot5','view',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$source='';if (isset($_POST['source'])) $source=trim_gks(base64_decode($_POST['source']));
$mytable='';if (isset($_POST['mytable'])) $mytable=trim_gks(base64_decode($_POST['mytable']));

$ret = gks_pivot_table_convert_to_excel($source,$mytable,gks_lang('Παραστατικά με Είδη'));

$return = $ret;
echo json_encode($return); die();





