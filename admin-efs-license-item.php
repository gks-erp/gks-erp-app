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
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php'); die(); }

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id==-1) {

  $sql="insert into gks_efs_license (email,quantity,
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip
  ) values (
  '',1,
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";

  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }  
  $id = $db_link->insert_id;
  header('Location: ?id='.$id);
  die();
}
if ($id <= 0) {header('Location: /my'); die(); }



$sql="SELECT gks_efs_license.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
FROM (gks_efs_license 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_efs_license.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_efs_license.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
where id_lic=".$id;
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
if ($result->num_rows!=1) die('record not found'); 
$row = $result->fetch_assoc();



stat_record();
$my_page_title=gks_lang('Άδεια Χρήσης').': '.$row['email'].' ('.$row['quantity'].')';
$nav_active_array=array('license','license_efs_license');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <form id="mypostform" class="container-fluid gksdataarea" style="width:96%">
        <div class="form-group row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="email">email:</label>
          <div class="col-sm-8">
            <input type="text" class="form-control form-control-sm" id="email" value="<?php echo htmlspecialchars_gks($row['email']);?>">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="quantity"><?php echo gks_lang('Ποσότητα');?>:</label>
          <div class="col-sm-8">
            <input type="text" class="form-control form-control-sm" id="quantity" value="<?php echo htmlspecialchars_gks($row['quantity']);?>">
          </div>
        </div>
        <div class="form-group row">
          <div class="offset-sm-4 col-sm-8 mb-2">
            <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
            <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_lic'];?>" data-model="gks_efs_license" data-backurl="admin-efs-license.php"><?php echo gks_lang('Διαγραφή');?></button>
          </div>
        </div>
      </form>
    </div>

    <div class="col-md-6">
      <div class="container-fluid gksdataarea" style="width:96%">
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
          <div class="col-sm-8"><input id="id_nomos" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php echo $row['id_lic'];?>"></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="/wp-admin/user-edit.php?user_id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="/wp-admin/user-edit.php?user_id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label form-control-sm text-sm-right">Last Ping:</label>
          <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['last_ping'])) echo mb_substr( getWeekDayName(showDate(strtotime($row['last_ping']), 'w', 1)),0,2).' '. showDate(strtotime($row['last_ping']), 'd/m/Y H:i:s',1);?></span></div>
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


    datasend+='&email='+ encodeURIComponent($.base64.encode($("#mypostform #email").val().trim()));   
    datasend+='&quantity='  + encodeURIComponent($.base64.encode($("#mypostform #quantity").val().trim()));
    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-efs-license-item-exec.php?id=' + <?php echo $id;?>,
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
  					//myalert('ok:' + 'OK');
  					window.location.reload();
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


