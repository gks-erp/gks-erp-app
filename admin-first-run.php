<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

db_open();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_settings','view',-1);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php?message='.rawurlencode('Access denied'));die();}


if ($GKS_LANG_DEFAULT!='') {header('Location: /my/');die();}

$my_page_title='First Run';

stat_record();
$nav_active_array=array('manage','manage_settings','manage_system_settings');

include_once('_my_header_empty.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h3>Welcome to gks ERP App</h3>
    </div>
  </div>
</div>

<div style="text-align:center;margin:50px;">
  <img src="/my/_current/_img_site/logo2.png" alt="logo" class="gks_logo_300">
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-lg-6 offset-lg-3 ">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          Language
        </div>
        <div class="card-body">  
          <div class="form-group row">
            <label for="user_lang_backend_el-GR" class="col-sm-6 col-form-label form-control-sm text-sm-right">Language:</label>
            <div class="col-sm-6" style="font-size: 0.875rem;">
              <input type="radio" name="user_lang_backend" id="user_lang_backend_en-US" value="en-US" checked=""> <label for="user_lang_backend_en-US">English</label>
              <br>
              <input type="radio" name="user_lang_backend" id="user_lang_backend_el-GR" value="el-GR"> <label for="user_lang_backend_el-GR">Greek</label>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom">Save</button>
    </div>            
  </div>            
</div>            
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;



  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  function mysubmit() {
    
    datasend='';
    datasend+='&GKS_LANG_DEFAULT=' + $('input[name=user_lang_backend]:checked').val();
    console.log(datasend);
    
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-first-run-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + 'Please Try again');
				} else {
				  
					if (data.success == true) {
					  need_save=false;
            if (data.redirect=='') {
  					  window.location.reload();
  					} else {
  					  window.location.href = '/my/';
  					}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }  
  


    
});
</script>


<?php

include_once('_my_footer_empty.php');


