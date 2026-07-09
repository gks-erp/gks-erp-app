<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Λήψεις - Σύνδεσμοι');
$nav_active_array=array('pages','downloads');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__downloads','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$GKS_ERP_APP_MOBILE_VER=gks_erp_app_mobile_get_later_version();
$GKS_ERP_APP_MOBILE_VER_link='https://tools.gks.gr/download/gks_ERP_App_Mobile_v'.$GKS_ERP_APP_MOBILE_VER.'.apk';
$GKS_ERP_APP_MOBILE_VER_img='';
include_once('vendor_inc/phpqrcode/qrlib.php');
$fileName = 'qr_code_temp_'.md5($GKS_ERP_APP_MOBILE_VER_link).'.png';
$pngAbsoluteFilePath = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$fileName;
$urlRelativeFilePath = GKS_SITE_URL.'my/temp/'.$fileName;
if (!file_exists($pngAbsoluteFilePath)) {
  QRcode::png($GKS_ERP_APP_MOBILE_VER_link, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 0);
} 
if (file_exists($pngAbsoluteFilePath)) {
  $GKS_ERP_APP_MOBILE_VER_img=$urlRelativeFilePath;
} else {
	$GKS_ERP_APP_MOBILE_VER_img='/my/img/gks_ERP_App_Mobile_qrcode.png';
}

include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>



<table class="table table-striped table-bordered gkstable" border="0" style="width:10%" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr>
      <th class="table-dark" scope="col" nowrap width="0%">#</th>
      <th class="table-dark" scope="col" nowrap width="50%"><?php echo gks_lang('Αρχείο');?></th>
      <th class="table-dark" scope="col" nowrap width="50%"><?php echo gks_lang('Μέγεθος σε ΜΒ');?></th>
    </tr>
  
  </thead>
  <tbody>
    <?php 
    $myfiles = scandir(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/install',SCANDIR_SORT_ASCENDING);
    $i=0;
    foreach ($myfiles as $value) {
      if ($value !='.' and $value!='..') {
        $i++;
        echo '<tr>
        <th scope="row">'.$i.'</th>
        <td nowrap><a href="/my/install/'.$value.'">'.$value.'</a></td>
        <td nowrap align="right">'.myNumberFormat(filesize(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/install/'.$value)/1024/1024,2) .'</td>
        </tr>';
      }
    } 
    ?>    
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="https://tools.gks.gr/gks_erp_app/gksErpApp.zip" target="_blank">gksErpApp.zip</a></td>
      <td nowrap align="right">25,30</td>
    </tr>     
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="<?php echo $GKS_ERP_APP_MOBILE_VER_link;?>" target="_blank">gks_ERP_App_Mobile_v<?php echo $GKS_ERP_APP_MOBILE_VER;?>.apk</a></td>
      <td nowrap align="right">26,00</td>
    </tr>     
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="https://anydesk.com/en/downloads/thank-you?dv=win_exe" target="_blank">AnyDesk Win</a></td>
      <td nowrap align="right">8,00</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="https://anydesk.com/en/downloads/thank-you?dv=mac_dmg" target="_blank">AnyDesk Mac</a></td>
      <td nowrap align="right">26,00</td>
    </tr>    
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="https://tools.gks.gr/download/Ammyy-Admin.exe" target="_blank">Ammyy-Admin.exe</a></td>
      <td nowrap align="right">0,74</td>
    </tr>    
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="https://www.microsoft.com/el-gr/download/details.aspx?id=48130" target="_blank">Microsoft .NET Framework 4.6</a></td>
      <td nowrap align="right">--</td>
    </tr>   
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="https://get.adobe.com/reader/" target="_blank">Acrobat Reader</a></td>
      <td nowrap align="right">--</td>
    </tr>   
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="http://www.tightvnc.com/download.php" target="_blank">TightVNC</a></td>
      <td nowrap align="right">--</td>
    </tr> 
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="https://play.google.com/store/apps/details?id=at.bitfire.davdroid" target="_blank">DAVx⁵</a></td>
      <td nowrap align="right">--</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="https://cyberduck.io/download/" target="_blank">Cyberduck </a></td>
      <td nowrap align="right">--</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td nowrap><a href="https://www.raidrive.com" target="_blank">RaiDrive </a></td>
      <td nowrap align="right">--</td>
    </tr>







 


    
    
    
      
  </tbody>
</table>

<?php gks_erp_app_purchase_ads_fix_970x90('page');?>
<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          gks ERP App Mobile

        </div>
        <div class="card-body" <?php echo gks_card_body('mobile');?>> 
					<div class="row">
			    	<div class="col-12">
							<?php echo gks_lang('H εφαρμογή gks ERP App Mobile λειτουργεί σε κινητά Android');?>
				  	</div>
				  </div>
					

				  
				  <div class="row" style="margin-top: 30px; margin-bottom: 30px;">
			    	<div class="col-12">
			    		<?php echo gks_lang('Μπορείτε να κατεβάσετε την εφαρμογή και να την εγκαταστήσετε στο κινητό σας από τον σύνδεσμο');?>:<br>
			    		<a href="<?php echo $GKS_ERP_APP_MOBILE_VER_link;?>"><?php echo $GKS_ERP_APP_MOBILE_VER_link;?></a>
			    	</div>
				  </div>
				  <div class="row" style="margin-top: 30px; margin-bottom: 30px;">
			    	<div class="col-12" style="text-align:center;">
			    		<img src="<?php echo $GKS_ERP_APP_MOBILE_VER_img;?>" style="max-width:100%;"/>
			    	</div>
				  </div>
            	
        </div>
      </div>
      
    </div>
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Δημιουργία QR Code');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('mobile');?>> 
					<div class="form-group row">
            <label for="qr_url" class="col-sm-12 col-form-label form-control-sm text-sm-right1"><?php echo gks_lang('URL-text');?>:</label>
            <div class="col-sm-12">
            	<textarea id="qr_url" class="form-control form-control-sm" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="min-height:100px"></textarea>
            	
            </div>
          </div>
					<div class="form-group row">
          	<div class="col-sm-12" id="qr_result" style="text-align:center;">
          		
          	</div>
          </div>
        </div>
      </div>
		</div>
  </div>
</div>
  
<p align="center">IP: <?php echo $gkIP;?></p>  


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var mychange = 'change keyup paste';

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  

  $('#qr_url').on(mychange,function() {
  	url=$('#qr_url').val();
  	//console.log(url);
  	if (url=='') {$('#qr_result').html('');return;}
  	$('#qr_result').html('<img src="/my/img/wait.gif">');
    
  	datasend='';
  	datasend+='&cmd=' + encodeURIComponent($.base64.encode('createqr'));
  	datasend+='&url=' + encodeURIComponent($.base64.encode(url));

    $.ajax({
			url: '/my/admin-qrcode-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('#qr_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+' '+jqXHR.responseText + '</div>')
			},				
			success: function(data) {
				if (!data) {
					$('#qr_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
				} else {
					if (data.success == true) {
						$('#qr_result').html($.base64.decode(data.qrhtml));
						
					} else {
						$('#qr_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+ $.base64.decode(data.message) + '</div>');
					}
				}
			}
		
    });
      		
  });

  function qr_url_change() {gks_resize_textarea($(this));}
  $('#qr_url').on(mychange, qr_url_change);
  gks_resize_textarea($('#qr_url'));
    
});
</script>



<?php
//db_close();
include_once('_my_footer_admin.php');


