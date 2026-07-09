<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Πληροφορίες Εφαρμογής');
$nav_active_array=array('manage','manage_settings','manage_system_info');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_app_info','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php?message='.rawurlencode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));die();}


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

.gks_serial_label {
  font-size:0.8rem;
  padding: 5px 0px 5px 0px;
  border-radius: 10px;
  text-align: center; 
  margin-top: 2px; 
}
.gks_serial_cell {
  font-size:0.8rem;
  padding: 5px 0px 5px 0px;
  margin-top: 2px; 
}
.gks_serial_cell1 {
  text-align: left; 
}
.gks_serial_cell2 {
  text-align: left; 
}
.gks_serial_cell3 {
  text-align: center; 
}
.gks_serial_cell4 {
  text-align: center; 
}
		
</style>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συνδρομή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('reg');?>>   
          <?php 
          //$GKS_ERP_APP_PURCHASE_CODE
          ?>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Url');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php
              if (isset($GKS_ERP_APP_PURCHASE_CODE['url']))
                echo $GKS_ERP_APP_PURCHASE_CODE['url'];
              ?>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('email');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php
              if (isset($GKS_ERP_APP_PURCHASE_CODE['register_email']))
                echo $GKS_ERP_APP_PURCHASE_CODE['register_email'];
              ?>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Κινητό');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php
              if (isset($GKS_ERP_APP_PURCHASE_CODE['register_mobile']))
                echo $GKS_ERP_APP_PURCHASE_CODE['register_mobile'];
              ?>
            </div>
          </div>
          <div class="col-md-12">
            <div style="text-align: center;font-weight: bold;margin-top: 12px;"><?php echo gks_lang('Serial Numbers');?></div>
          </div>
          <div class="form-group row">
            <div class="col-sm-4 gks_items_col"><div class="table-dark gks_serial_label"><?php echo gks_lang('Τύπος');?></div></div>
            <div class="col-sm-3 gks_items_col"><div class="table-dark gks_serial_label"><?php echo gks_lang('Serial number');?></div></div>
            <div class="col-sm-3 gks_items_col"><div class="table-dark gks_serial_label"><?php echo gks_lang('Λήξη');?></div></div>
            <div class="col-sm-2 gks_items_col"><div class="table-dark gks_serial_label"><?php echo gks_lang('Ενεργό');?></div></div>
          </div> 
          <div id="serial_numbers_status_div">
          <?php 
          if (isset($GKS_ERP_APP_PURCHASE_CODE['purchase_codes']) and is_array($GKS_ERP_APP_PURCHASE_CODE['purchase_codes'])) {
          foreach ($GKS_ERP_APP_PURCHASE_CODE['purchase_codes'] as $module => $serial) {?>          
          <div class="form-group row">
            <div class="col-sm-4 gks_items_col"><div class="gks_serial_cell gks_serial_cell1"><?php echo $serial['type_descr'];?></div></div>
            <div class="col-sm-3 gks_items_col"><div class="gks_serial_cell gks_serial_cell2"><?php echo gks_format_serial_number($serial['code']);?></div></div>
            <div class="col-sm-3 gks_items_col"><div class="gks_serial_cell gks_serial_cell3"><?php if (!empty($serial['expire_date'])) echo showDate(strtotime($serial['expire_date']),'d/m/Y H:i',1);?></div></div>
            <div class="col-sm-2 gks_items_col"><div class="gks_serial_cell gks_serial_cell4"><?php echo myimg010(($serial['valid'] ? 1 : 0));?></div></div>
          </div>
          
          <?php }} ?>
          </div>
          <div class="form-group row" style="margin-top:20px">
            <div class="col-sm-12" style="text-align:center;">
              <button type="button" class="btn btn-primary" id="check_serial_numbers_status"><?php echo gks_lang('Ενημέρωση Κατάστασης');?></button>  
            </div>
          </div>
                    
          
        </div>              
      </div>  
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Επικοινωνία με gks');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('contact');?>>   
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Web');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <a href="https://www.gks.gr" target="_blank">www.gks.gr</a>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('email');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <a href="mailto:info@gks.gr" target="_blank">info@gks.gr</a>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <a href="tel:+302310698507" target="_blank"><?php echo gks_lang('2310 698 507');?></a>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Κινητό');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <a href="tel:+306971881406" target="_blank"><?php echo gks_lang('697 188 1406');?></a>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Διεύθυνση');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <a href="https://g.page/r/CQWfyQiLLBH6EAg/" target="_blank"> <?php echo gks_lang('Ικάρων 1-Β');?>,
								<?php echo gks_lang('Ωραιόκαστρο');?>, 57 013,
								<?php echo gks_lang('Θεσσαλονίκη');?></a>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Social Media');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <a href="https://www.facebook.com/gks.business.software" target="_blank"><?php echo gks_lang('Facebook');?></a>
            </div>
          </div> 

				</div>
			</div>
      


      
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πληροφορίες Εφαρμογής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>   
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Όνομα εφαρμογής');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php
              if (isset($GKS_ERP_APP_PURCHASE_CODE['name']))
                echo $GKS_ERP_APP_PURCHASE_CODE['name'];
              ?>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Έκδοση');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php echo $GKS_CACHE_DB_VER.'.'.$gks_cache_version;?>
            </div>
          </div>
          
          
          
                    
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('URL');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php echo GKS_SITE_URL;?>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('DB Version');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php echo $GKS_CACHE_DB_VER;?>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Cache Version');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php echo $gks_cache_version;?>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('Web Server');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php echo $_SERVER['SERVER_SOFTWARE'];?>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('MySQL Version');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php        
              $db_link =  @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			        if ($db_link===false) {
			          $rt=false;
			          $msg=mysqli_connect_errno().'-'.mysqli_connect_error();
			        } else {
			          $rt=true;
			          $result=$db_link->query("select version() as vv");
			          $row=$result->fetch_assoc();
			          $msg=$row['vv'];
			          $parts=explode('-',$msg);
			          $rt=version_compare($parts[0],'5.7.0','>=');
			        }
       				echo $msg;
      				?>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('PHP Version');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php echo phpversion();?>
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo gks_lang('WordPress Version');?>:</label>
            <div class="col-md-8 gks_text_in_div">
              <?php echo $wp_version;?>
            </div>
          </div> 

          <div class="form-group row" style="margin-top:20px">
            <div class="col-sm-12" style="text-align:center;">
              <a type="button" class="btn btn-primary" href="admin-update.php"><?php echo gks_lang('Έλεγχος για αναβάθμιση');?></a>  
            </div>
          </div>
                    
        </div>
      </div>
      
      <?php if (isset($gks_plugins_data) and isset($gks_plugins_data['plugins']) and count($gks_plugins_data['plugins'])>0) {?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Plugins');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('plugins');?>>   
          <?php foreach ($gks_plugins_data['plugins'] as $mypitem) {
          //echo '<pre>';print_r($mypitem['plugins']);  
          ?>
          
          
          <div class="form-group row">
            <div class="col-md-4 gks_text_in_div">
              <?php echo $mypitem['info']['name'];?>
            </div>
            <div class="col-md-4 gks_text_in_div">
              <?php echo $mypitem['info']['version'];?>
            </div>
            <div class="col-md-4 gks_text_in_div">
              <?php echo $mypitem['info']['url'];?>
            </div>
          </div> 
          <?php } ?>

        </div>
      </div>      
      <?php } ?>

      <style>
        .mydirdiv .col-md-6 i {
          padding: 0px 10px;
          color: lightgray;
        }  
      </style>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Μέγεθος αρχείων - εφαρμογής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('filesize');?>>   
          <div class="form-group row mydirdiv" data-mydir="database">
            <div class="col-md-6 gks_text_in_div"><?php echo gks_lang('Βάση δεδομένων');?></div>
            <div class="col-md-3 gks_text_in_div text-center myresdir" data-bytes="0"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>
          <div class="form-group row mydirdiv" data-mydir="erpfi">
            <div class="col-md-6 gks_text_in_div"><?php echo gks_lang('ERP Αρχεία');?></div>
            <div class="col-md-3 gks_text_in_div text-center myresdir"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>
          <div class="form-group row mydirdiv" data-mydir="website">
            <div class="col-md-6 gks_text_in_div"><?php echo gks_lang('Ιστότοπος');?></div>
            <div class="col-md-3 gks_text_in_div text-center myresdir" data-bytes="0"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>        
          <div class="form-group row mydirdiv" data-mydir="gkserp">
            <div class="col-md-6 gks_text_in_div"><i class="fa-solid fa-chevron-right"></i><?php echo gks_lang('gks ERP App');?></div>
            <div class="col-md-3 gks_text_in_div text-center myresdir" data-bytes="0"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>
          <div class="form-group row mydirdiv" data-mydir="erplo">
            <div class="col-md-6 gks_text_in_div"><i class="fa-solid fa-chevron-right"></i><i class="fa-solid fa-chevron-right"></i><?php echo gks_lang('ERP Λογότυπα');?></div>
            <div class="col-md-3 gks_text_in_div text-center myresdir" data-bytes="0"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>
          <div class="form-group row mydirdiv" data-mydir="erpul">
            <div class="col-md-6 gks_text_in_div"><i class="fa-solid fa-chevron-right"></i><i class="fa-solid fa-chevron-right"></i><?php echo gks_lang('ERP Μεταφορτώσεις');?></div>
            <div class="col-md-3 gks_text_in_div text-center myresdir" data-bytes="0"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>
          <div class="form-group row mydirdiv" data-mydir="erpdl">
            <div class="col-md-6 gks_text_in_div"><i class="fa-solid fa-chevron-right"></i><i class="fa-solid fa-chevron-right"></i><?php echo gks_lang('ERP Λήψεις');?></div>
            <div class="col-md-3 gks_text_in_div text-center myresdir" data-bytes="0"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>
          <div class="form-group row mydirdiv" data-mydir="wordpress">
            <div class="col-md-6 gks_text_in_div"></i><i class="fa-solid fa-chevron-right"></i><?php echo gks_lang('Wordpress');?></div>
            <div class="col-md-3 gks_text_in_div text-center myresdir" data-bytes="0"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>
          <div class="form-group row mydirdiv" data-mydir="wodpr">
            <div class="col-md-6 gks_text_in_div"><i class="fa-solid fa-chevron-right"></i><i class="fa-solid fa-chevron-right"></i><?php echo gks_lang('Wordpress Uploads');?></div>
            <div class="col-md-3 gks_text_in_div text-center myresdir" data-bytes="0"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>
          <div class="form-group row mydirdiv" data-mydir="total">
            <div class="col-md-6 gks_text_in_div"><b><?php echo gks_lang('Σύνολο');?></b><br>
            <small class="form-text text-muted"><?php echo gks_lang('Βάση δεδομένων+ERP Αρχεία+Ιστότοπος');?></small>
            </div>
            <div class="col-md-3 gks_text_in_div text-center myresdir" data-bytes="0"></div>
            <div class="col-md-3 gks_text_in_div text-right"><button class="gks_dirsize_calc btn btn-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i></button></div>
          </div>
        </div>
      </div> 
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προγραμματισμένες εργασίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('cron');?>>       
          <?php 
          $sql="select * from gks_settings where mykey like 'GKS_CRON_%'";
          $result = $db_link->query($sql);        
          if (!$result) debug_mail(false,'error sql',$sql);
          if (!$result) die('sql error');


          while ($row = $result->fetch_assoc()) {
            $name=strtolower(str_replace('GKS_CRON_','',$row['mykey']));
            $name=str_replace('_',' ',$name);
          ?>

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-md text-md-right"><?php echo $name;?>:</label>
            <div class="col-md-8 gks_text_in_div" title="<?php
              echo showDate(strtotime($row['myvalue']),'d/m/Y H:i:s',1);
              ?>"><?php echo secondsago(strtotime($row['myvalue']));?></div>
          </div>
          <?php } ?>
        </div>
      </div>
           
    </div>
  </div>
</div>











              

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>




var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_app_info','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_app_info','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_app_info','delete',0);?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  $('#check_serial_numbers_status').click(function() {
    $('#check_serial_numbers_status').prop('disabled',true);
    //console.log('sss');  
    //$('body').addClass("myloading");
    
    $('#serial_numbers_status_div').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    datasend='cmd=check_status';
    
    $.ajax({
			url: '/my/admin-license-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  //$("body").removeClass("myloading");
				//myalert('error:' + jqXHR.responseText);
				$('#serial_numbers_status_div').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + jqXHR.responseText + '</div>');
				$('#check_serial_numbers_status').prop('disabled',false);
			},				
			success: function(data) {
				//$("body").removeClass("myloading");
				$('#check_serial_numbers_status').prop('disabled',false);
				if (!data) {
					//myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					$('#serial_numbers_status_div').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
				} else {
					if (data.success == true) {
					  $('#serial_numbers_status_div').html(data.html);
					} else {
						//myalert('error:' + $.base64.decode(data.message));
						$('#serial_numbers_status_div').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message) + '</div>');
						
					}
				}
			}
			
		});					    
  });
  
  
 
  function gks_get_dir_size(mydir) {
    $('.mydirdiv[data-mydir="' + mydir + '"] .myresdir').html('<i class="fa-solid fa-hourglass"></i>');
    $('.mydirdiv[data-mydir="' + mydir + '"] .gks_dirsize_calc').prop('disabled' , true);
    datasend='&mydir=' + encodeURIComponent($.base64.encode(mydir));
    $.ajax({
			url: '/my/admin-system-info-dirsize.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_mydir:mydir,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('.mydirdiv[data-mydir="' + this.gks_mydir + '"] .myresdir').html('error:' + jqXHR.responseText);
			  $('.mydirdiv[data-mydir="' + this.gks_mydir + '"] .gks_dirsize_calc').prop('disabled' , false);
			},				
			success: function(data) {
				$('.mydirdiv[data-mydir="' + this.gks_mydir + '"] .gks_dirsize_calc').prop('disabled' , false);
				if (!data) {
				  $('.mydirdiv[data-mydir="' + this.gks_mydir + '"] .myresdir').html('error:'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
            $('.mydirdiv[data-mydir="' + mydir + '"] .myresdir').html('<i class="fa-solid fa-hourglass"></i>');
					  $('.mydirdiv[data-mydir="' + this.gks_mydir + '"] .myresdir').html(data.myhuman).attr('data-bytes',data.mybytes);
					} else {
					  $('.mydirdiv[data-mydir="' + this.gks_mydir + '"] .myresdir').html('error:' + $.base64.decode(data.message));
					}
				}
			}
		});     
  }  
  
  $('.gks_dirsize_calc').click(function() {
    mydir=$(this).parent().parent().attr('data-mydir');
    gks_get_dir_size(mydir);
  });
  

  
});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


