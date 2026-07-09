<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Εξερεύνηση αρχείων');
$nav_active_array=array('pages','filesexplore');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__filesexplore','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>

<div id="gks_filesexplore_div_insert"></div>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_perm_filesexplore_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks__filesexplore','edit',  0);?>;
var from_php_perm_filesexplore_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks__filesexplore','add',   0);?>;
var from_php_perm_filesexplore_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks__filesexplore','delete',0);?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  
});
</script>

<script> 
  
var from_php_filesobjectlist_max_upload_file_size=<?php echo gks_get_max_upload_file_size(true);?>;
var from_php_filesobjectlist_object_name='/';
</script> 

<link rel="stylesheet" href="/my/css/_gks_filesobjectlist.css?v=<?php echo $gks_cache_version;?>" type="text/css">    
<link rel="stylesheet" href="/my/js/jquery.fileupload/jquery.fileupload.css?v=<?php echo $gks_cache_version;?>" type="text/css">    
<script src="/my/js/jquery.fileupload/vendor/jquery.ui.widget.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="/my/js/jquery.fileupload/jquery.iframe-transport.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="/my/js/jquery.fileupload/jquery.fileupload.js?v=<?php echo $gks_cache_version;?>"></script> 
<script src="/my/js/jquery.fileupload/jquery.fileupload-process.js?v=<?php echo $gks_cache_version;?>"></script> 
<script src="/my/js/jquery.fileupload/jquery.fileupload-validate.js?v=<?php echo $gks_cache_version;?>"></script> 

<script src="js/_gks_filesobjectlist.js?v=<?php echo $gks_cache_version;?>"></script>

<style>
  
.gks_footer_last_p {
  display:none;  
}
#gks_filesexplore_div {
  padding:0px 20px 10px 20px;  
}
#gks_filesexplore_div_header_row1 {
  display:none;  
}

</style>

<script>
jQuery(document).ready(function($) {
  
  gks_filesexplore_div_start();
});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');

