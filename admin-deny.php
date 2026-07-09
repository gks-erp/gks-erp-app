<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');


//print '<pre>';
//print_r($_SERVER);
//die();
//die();


$my_page_title=gks_lang('Δεν επιτρέπεται η πρόσβαση');
db_open();
stat_record();

if (isset($_GET['noredirect'])==false) {
  if ($my_wp_user_id<=0) {header('Location: /wp-login.php'); die(); }
}

//echo $my_wp_user_id;
//print_r($my_wp_user_info->roles);
//die();


if ($my_wp_user_id>0) {
  $userrole='';
  if (isset($my_wp_user_info->roles)) {
    if (in_array('subscriber',$my_wp_user_info->roles) and count($my_wp_user_info->roles)<=1)  $userrole='subscriber';
  }
  debug_mail(false,'admin-deny and goto home my',isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' );
  if ($userrole=='subscriber') {header('Location: /'); die(); }
}

$perm_ret=gks_permission_get_user($my_wp_user_id);
//print '<pre>';print_r($perm_ret);die();
if (count($perm_ret['data']['objects'])==0 and $perm_ret['data']['user_is_admin']==false) {
  //den einai admin, xoris na exei oristhei kapoio dikaioma, opote redirect to home page
  debug_mail(false,'admin-deny and goto home my. No permission objects',isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' );
  header('Location: /'); die();
}

//echo time();die();


include_once('_my_header_admin.php');
?>


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>

<div class="container-fluid ">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if (isset($_GET['message'])) { ?>
      <div class="alert alert-danger" role="alert" style="text-align: center;">
          <?php echo rawurldecode($_GET['message']);?>
      </div>
      <?php } ?>
      <p align="center">
        <button id="mybuttonback" type="button" class="btn btn-primary submit_button_back"><?php echo gks_lang('Επιστροφή');?></button>
      </p>
      <p align="center">
        <img src="/my/img/access_denied1.gif" border="0">
      </p>
    </div>
  </div>
</div>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  


  
  $('.submit_button_back').click(function() {
    <?php if (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']!='' and endwith($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI']) == false ) { ?>
      window.location.href='<?php echo $_SERVER['HTTP_REFERER'];?>';
    <?php } else { ?>
      window.location.href='/login';
    <?php } ?>
  }); 

});

</script>
  
<?php
//db_close();
include_once('_my_footer_admin.php');
