<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Επιλογή σημείου Εντατικής Λιανικής');
$nav_active_array=array('accounting','accounting_pos_run');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_pos_run','add',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_pos_run_ids=gks_permission_user_condition($my_wp_user_id,'gks_pos_run','01');
//print '<pre>';print_r($perm_id_pos_run_ids);die();

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_gks_acc_inv_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','view',0);
$perm_gks_acc_inv_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','edit',0);
$perm_gks_acc_inv_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','add',0);
$perm_gks_acc_inv_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','delete',0);


$extra_where='';
gks_plugins_functions_run('admin_pos_run_select',array(
  'extra_where'=>&$extra_where,
  'id_erp_app_mobile' =>0,
  'login_type' => 'user',
  'user_id' => $my_wp_user_id,
));

$sql = "SELECT gks_pos.* from gks_pos
where pos_disable=0 ";
if (count($perm_id_pos_run_ids)>0) $sql.=" and gks_pos.id_pos in (".implode(',',$perm_id_pos_run_ids).")";

if (count($perm_id_company_ids)>0) $sql.=" and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";


$sql.= $extra_where." order by pos_name";

//echo '<pre>'.$sql;die();

$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');




include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>

<div class="container" style="margin-top: 36px;">
  <div class="row">

<?php
$i = 0;
while ($row = $result->fetch_assoc()) {

	$i++;
?>

    <div class="col-sm-6 col-md-4  col-lg-3" style="text-align: center;margin-bottom: 26px;">
      <a href="admin-pos-run.php?id=<?php echo $row['id_pos'];?>">
        <button type="button" class="btn btn-primary btn-lg"><?php echo $row['pos_name'];?></button>
      </a>
    </div>
<?php } ?>

  </div>
</div>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_pos_run','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_pos_run','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_pos_run','delete',0);?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  

});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


