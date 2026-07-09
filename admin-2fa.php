<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('2FA');
$nav_active_array=array('user','2fa');


db_open();
stat_record();




include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>



<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ενεργοποίηση 2FA');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('odigies');?>> 
<div id="gks_2fa_text1"></div>
<p><a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2</a></p>
<div id="gks_2fa_text2"></div>
<p><a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank">https://apps.apple.com/us/app/google-authenticator/id388497605</a></p>
<div id="gks_2fa_text3"></div>
<p><i class="fas fa-arrow-circle-right" style="color: green;"></i> <a href="/wp-admin/admin.php?page=WFLS" target="_blank">Two-Factor Authentication (/wp-admin/admin.php?page=WFLS)</a> <i class="fas fa-arrow-circle-left" style="color: green;"></i></p>
<div id="gks_2fa_text4"></div>
<p style="text-align:center;"><img src="/my/img/2fa/2fa_1.png" style="max-width:100%;"></p>
<div id="gks_2fa_text5"></div>
<p style="text-align:center;"><img src="/my/img/2fa/2fa_2.png" style="max-width:100%;max-height:500px;;border:1px solid #1883d7;"></p>
<div id="gks_2fa_text6"></div>
<p style="text-align:center;"><img src="/my/img/2fa/2fa_3.png" style="max-width:100%;"></p>
<div id="gks_2fa_text7"></div>

<p><hr></p>    

<div id="gks_2fa_text8"></div>
<p style="text-align:center;"><img src="/my/img/2fa/2fa_4.png" style="max-width:100%;"></p>
<div id="gks_2fa_text9"></div>
<p style="text-align:center;"><img src="/my/img/2fa/2fa_5.png" style="max-width:100%;"></p>
<div id="gks_2fa_text10"></div>
<p style="text-align:center;"><img src="/my/img/2fa/2fa_6.png" style="max-width:100%;"></p>
<div id="gks_2fa_text11"></div>

        </div>
      </div>
    </div>

    <div class="col-md-6">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σύνδεση με 2FA');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('sindesi');?>> 

<div id="gks_2fa_text12"></div>
<p style="text-align:center;"><img src="/my/img/2fa/2fa_7.png" style="max-width:100%;"></p>
<div id="gks_2fa_text13"></div>
<p style="text-align:center;"><img src="/my/img/2fa/2fa_8.png" style="max-width:100%;"></p>
<div id="gks_2fa_text14"></div>


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

});
</script>

<?php echo gks_lang_big_texts('admin-2fa');?>

<?php
//db_close();
include_once('_my_footer_admin.php');


