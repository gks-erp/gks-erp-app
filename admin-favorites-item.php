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

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id==-1) {
  $row=array();
  
  $row['id_favorites']=-1;
  $row['descr']='';
  $row['url']='';
  $row['user_id']=$my_wp_user_id;

  $row['user_id_add']=0;
  $row['gks_nickname_add']='';
  $row['mydate_add']=null;
  $row['user_id_edit']=0;
  $row['gks_nickname_edit']='';
  $row['mydate_edit']=null;
  $row['myip']='';

  

  $my_page_title=gks_lang('Νέο Αγαπημένο');
} else {




  $sql="select gks_users_favorites.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_users_favorites 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_users_favorites.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_users_favorites.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where user_id=".$my_wp_user_id." and id_favorites=".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Αγαπημένο').': '.$row['descr'];
  $object_title=$row['descr'];

}

stat_record();
$nav_active_array=array('favorites');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Αγαπημένο');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Αγαπημένο');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <form id="mypostform" class="container-fluid gksdataarea" style="width:96%">
        <div class="form-group row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="descr"><?php echo gks_lang('Όνομα σελίδας');?>:</label>
          <div class="col-sm-8">
            <input type="text" class="form-control form-control-sm" id="descr" value="<?php echo htmlspecialchars_gks($row['descr']);?>">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="url"><?php echo gks_lang('Σύνδεσμος');?>:</label>
          <div class="col-sm-8">
            <input type="text" class="form-control form-control-sm" id="url" value="<?php echo htmlspecialchars_gks($row['url']);?>">
          </div>
        </div>
        <div class="form-group row">
          <div class="offset-sm-4 col-sm-8 mb-2">
            <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
            <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_favorites'];?>" data-model="gks_users_favorites" data-backurl="admin-favorites.php"><?php echo gks_lang('Διαγραφή');?></button>
          </div>
        </div>
      </form>
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="container-fluid gksdataarea" style="width:96%">
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
          <div class="col-sm-8"><input id="id_nomos" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php echo $row['id_favorites'];?>"></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
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
  
  


 
    
  function mysubmit() {
    
    datasend='';


    datasend+='&descr='+ encodeURIComponent($.base64.encode($("#mypostform #descr").val().trim()));   
    datasend+='&url='  + encodeURIComponent($.base64.encode($("#mypostform #url").val().trim()));
    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-favorites-item-exec.php?id=' + <?php echo $id;?>,
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
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
            if (data.redirect=='') {
  					  window.location.reload();
  					} else {
  					  window.location.href = $.base64.decode(data.redirect);
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
//db_close();
include_once('_my_footer_admin.php');


