<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Εξαγωγή παραστατικών σε Excel');




db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','view',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');



$perm_gks_acc_inv_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','view',0);
$perm_gks_acc_inv_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','edit',0);
$perm_gks_acc_inv_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','add',0);
$perm_gks_acc_inv_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','delete',0);




$user_companys=gks_get_companys_list();





include_once('admin-acc-inv_filters.php');
//$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo '<pre>';echo $sql;die();
  
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');


$id_export_excel=0;if (isset($_GET['id_export_excel'])) $id_export_excel=intval($_GET['id_export_excel']);




$export_excel_params=array();
$export_excel_params['id_export_excel']=$id_export_excel;
$export_excel_params['result']=$result;
if (isset($data)) $export_excel_params['data']=$data;

gks_export_excel($export_excel_params);
