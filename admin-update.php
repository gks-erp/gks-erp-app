<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Αναβάθμιση');
$nav_active_array=array('manage','manage_settings','manage_system_info');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_app_info','edit',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php?message='.rawurlencode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));die();}


if (file_exists(GKS_CACHE.'/latest.json')) @unlink(GKS_CACHE.'/latest.json');

//print '<pre>';echo wp_enqueue_script('my-upload');die();

include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>

<style>
.gks_text_in_div {
	
	padding-left: 1.5rem;
	padding-right: 0.5rem;
	padding-top: calc(0.375rem + 1px);    
	padding-bottom: calc(0.375rem + 1px);
}	

		
</style>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αναβάθμιση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('update');?>>   


          <div class="form-group row">
            <label class="col-md-6 col-form-label form-control-md text-md-right"><?php echo gks_lang('Τρέχουσα έκδοση');?>:</label>
            <div class="col-md-6 gks_text_in_div">
              <?php echo $GKS_CACHE_DB_VER.'.'.$gks_cache_version;?>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-6 col-form-label form-control-md text-md-right"><?php echo gks_lang('Τελευταία αναβάθμιση');?>:</label>
            <div class="col-md-6 gks_text_in_div">
              <?php 
              $html=[];
              $sql="select myvalue from gks_settings where mykey='GKS_ERP_APP_LAST_UDPATE_DATA'";
              $result = $db_link->query($sql);        
              if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
              if ($result->num_rows==1) {
                $row=$result->fetch_assoc();
                $temp=$row['myvalue'];
                $temp=json_decode($temp,true);

                if (isset($temp['latest_json'])) {
                  $latest_json=$temp['latest_json'];
                  $tempv='';
                  if (isset($latest_json['DB Version'])) $tempv.=$latest_json['DB Version'];
                  $tempv.='.';
                  if (isset($latest_json['Cache Version'])) $tempv.=$latest_json['Cache Version'];
                  $html[]=gks_lang('Έκδοση').': '.$tempv;
                  
                  if (isset($latest_json['Date'])) {
                    $html[]=gks_lang('Ημερομηνία δημοσίευσης').': '.showDate(strtotime($latest_json['Date']),'d/m/Y H:i:s',1);
                  }
                  $total_filesize=0;
                  if (isset($latest_json['filesize'])) $total_filesize+=intval($latest_json['filesize']);
                  if (isset($latest_json['filesize_img_site'])) $total_filesize+=intval($latest_json['filesize_img_site']);
                  if (isset($latest_json['filesize_theme'])) $total_filesize+=intval($latest_json['filesize_theme']);
                  if (isset($latest_json['filesize_gks_core'])) $total_filesize+=intval($latest_json['filesize_gks_core']);
                  if (isset($latest_json['filesize_mu_plugins'])) $total_filesize+=intval($latest_json['filesize_mu_plugins']);
                  if (isset($latest_json['filesize_maxmind'])) $total_filesize+=intval($latest_json['filesize_maxmind']);
                  
                  if ($total_filesize>0) {
                    $html[]=gks_lang('Μέγεθος').': '.number_format($total_filesize/1024/1024,2,',','.').' MB';
                  }
                  
                }
                if (isset($temp['time'])) {
                  $html[]=gks_lang('Εγκατάσταση στις').': '.showDate(intval($temp['time']),'d/m/Y H:i:s',1);
                }                
                
              }
              echo implode('<br>',$html);
              ?>
            </div>
          </div>
                    
          <div class="form-group row" style="margin-top:20px">
            <div class="col-sm-12" style="text-align:center;">
              <button type="button" class="btn btn-primary" id="check_new_version"><?php echo gks_lang('Έλεγχος για αναβάθμιση');?></button>  
            </div>
          </div>
          <div class="form-group row" id="update_version_step1_div" style="margin-top:20px;display:none;">
            <div class="col-sm-12" id="update_version_step1_html" style="text-align:center;"></div>
          </div>          
          <div class="form-group row" id="update_version_step1force_div" style="display:none;">
            <div class="col-sm-12" style="text-align:center;">
              <input type="checkbox" id="force_reinstall" class="switchery1_sel">
              <label for="force_reinstall"><?php echo gks_lang('Επανεγκατάσταση');?></label>
            </div>
          </div> 
          
          <div class="form-group row" id="update_version_step2_div" style="margin-top:20px;display:none;">
            <div class="col-sm-12" style="text-align:center;">
              <button type="button" class="btn btn-primary" id="downloadfiles"><?php echo gks_lang('Λήψη αρχείων');?></button>  
            </div>
          </div>
          <div class="form-group row" id="update_version_step3_div" style="margin-top:20px;display:none;">
            <div class="col-sm-12" id="update_version_step3_html" style="text-align:center;"></div>
          </div> 
                 
          <div class="form-group row" id="update_version_step4_div" style="margin-top:20px;display:none;">
            <div class="col-sm-12" style="text-align:center;">
              <button type="button" class="btn btn-primary" id="unzip"><?php echo gks_lang('Αποσυμπίεση και αντιγραφή αρχείων');?></button>  
            </div>
          </div>
          <div class="form-group row" id="update_version_step5_div" style="margin-top:20px;display:none;">
            <div class="col-sm-12" id="update_version_step5_html" style="text-align:center;"></div>
          </div> 

          <div class="form-group row" id="update_version_step6_div" style="margin-top:20px;display:none;">
            <div class="col-sm-12" style="text-align:center;">
              <a type="button" class="btn btn-primary" href="_system_update.php"><?php echo gks_lang('Αναβάθμιση βάσης δεδομένων');?></a>  
            </div>
          </div>



          
        </div>              
      </div>  

    </div>

  </div>
</div>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>




jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  $('#force_reinstall').change(function() {
    if ($('#force_reinstall').is(':checked')) {
      $('#update_version_step2_div').slideDown();
    } else {
      $('#update_version_step2_div').slideUp();
    }
  });
  
  $('#check_new_version').click(function() {
    $('#update_version_step1_html').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    $('#update_version_step1_div').slideDown();
    $('#update_version_step1force_div').hide();
    
    $('#check_new_version').prop('disabled',true);
    datasend='cmd=check_version';
    $.ajax({
			url: '/my/admin-update-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('#update_version_step1_html').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + jqXHR.responseText + '</div>');
				$('#check_new_version').prop('disabled',false);
			},				
			success: function(data) {
				if (!data) {
					$('#update_version_step1_html').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
				  $('#check_new_version').prop('disabled',false);
				} else {
					if (data.success == true) {
					  $('#update_version_step1_html').html($.base64.decode(data.message));
					  if (data.newversion) {
					    $('#update_version_step2_div').slideDown();
					  } else {
					    $('#update_version_step1force_div').slideDown();  
					  }
					} else {
						$('#update_version_step1_html').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message) + '</div>');
						$('#check_new_version').prop('disabled',false);
					}
				}
			}
		});	
  });
  
  $('#downloadfiles').click(function() {
    $('#update_version_step3_html').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    $('#update_version_step3_div').slideDown();
    $('#downloadfiles').prop('disabled',true);
    datasend='cmd=downloadfiles';
    $.ajax({
			url: '/my/admin-update-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('#update_version_step3_html').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + jqXHR.responseText + '</div>');
				$('#downloadfiles').prop('disabled',false);
			},				
			success: function(data) {
				if (!data) {
					$('#update_version_step3_html').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
					$('#downloadfiles').prop('disabled',false);
				} else {
					if (data.success == true) {
					  $('#update_version_step3_html').html($.base64.decode(data.message));
					  $('#update_version_step4_div').slideDown();
					} else {
						$('#update_version_step3_html').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message) + '</div>');
						$('#downloadfiles').prop('disabled',false);
					}
				}
			}
		});	
  });
  
  
  $('#unzip').click(function() {
    $('#update_version_step5_html').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    $('#update_version_step5_div').slideDown();
    $('#unzip').prop('disabled',true);
    datasend='cmd=unzip';
    $.ajax({
			url: '/my/admin-update-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('#update_version_step5_html').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + jqXHR.responseText + '</div>');
				$('#unzip').prop('disabled',false);
			},				
			success: function(data) {
				if (!data) {
					$('#update_version_step5_html').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
					$('#unzip').prop('disabled',false);
				} else {
					if (data.success == true) {
					  $('#update_version_step5_html').html($.base64.decode(data.message));
					  $('#update_version_step6_div').slideDown();
					} else {
						$('#update_version_step5_html').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message) + '</div>');
						$('#unzip').prop('disabled',false);
					}
				}
			}
		});	
  });  
  
  
});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


