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
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_tasks_status',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}






if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id==-1) {
  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_crm_task_status'] =-1;

  $row['task_status_descr']='';
  $row['task_status_color']='';
  $row['task_status_sortorder']=1000;
  $row['task_status_disabled']=0;
  

  $my_page_title=gks_lang('Νέα Κατάσταση Εργασιών');
} else {

  $sql ="SELECT gks_crm_tasks_status.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
  FROM (gks_crm_tasks_status 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_tasks_status.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_tasks_status.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_crm_task_status = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Κατάσταση Εργασιών').': '.$row['task_status_descr'];
  $object_title=$row['task_status_descr'];
}



stat_record();
$nav_active_array=array('crm','crm_tasks_status');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Κατάσταση Εργασιών');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Κατάσταση Εργασιών');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>         


          <div class="form-group row">
            <label for="task_status_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="task_status_descr" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['task_status_descr']);?>">
            </div>
          </div>

          <div class="form-group row">
            <label for="company_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα');?>:</label>
            <div class="col-md-8">
              <input id="task_status_color" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['task_status_color']);?>" style="max-width:200px;">
            </div>
          </div> 
          <div class="form-group row">
            <label for="task_status_sortorder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-md-8">
              <input id="task_status_sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['task_status_sortorder'];?>" min="0" step="1">
            </div>
          </div>           

          <div class="form-group row">
            <label for="task_status_disabled" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="task_status_disabled" value="1" <?php if ($row['task_status_disabled']==0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>  


        </div>
      </div>
    </div>
            
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
    </div>
  </div>
</div>


<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_crm_task_status'];?>" data-model="gks_crm_tasks_status" data-backurl="admin-crm-tasks-status.php"><?php echo gks_lang('Διαγραφή');?></button>
      
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <?php 
      echo getObjectRels('gks_crm_tasks_status',$id);
      echo getActivityObjectTable('gks_crm_tasks_status',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_crm_tasks_status','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
      

    </div>
        
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>       
                  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_crm_task_status']>0) echo $row['id_crm_task_status'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add']))echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
          </div>                  
                  
               
        </div>
      </div>
            
    </div>
    
  </div>
</div>









              

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>



var from_php_id=<?php echo $id;?>;

var from_php_dialog_object_rel_curr='gks_crm_tasks_status';
var from_php_activity_model='gks_crm_tasks_status';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_tasks_status','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_tasks_status','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_tasks_status','delete',$id);?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  

  $('#task_status_color').spectrum({
    type: "component",
    locale:'el',
    togglePaletteOnly: true,
    hideAfterPaletteSelect: true,
    showInput: true,
    showInitial: true,
    allowEmpty:true,
    //preferredFormat:'hex',
    chooseText: 'OK',
    cancelText: gks_lang('Άκυρο'),
    togglePaletteMoreText: gks_lang('Περισσότερα'), 
    togglePaletteLessText: gks_lang('Παλέτα'),
    clearText : gks_lang('Καθαρισμός'),
    noColorSelectedText: gks_lang('Διάφανο'),
  });
 
    
  function mysubmit() {
    datasend='';
    datasend+='&task_status_descr='  + encodeURIComponent($.base64.encode($("#mypostform #task_status_descr").val().trim()));
    datasend+='&task_status_color='  + encodeURIComponent($.base64.encode($("#mypostform #task_status_color").val().trim()));
    datasend+='&task_status_sortorder='  + $("#mypostform #task_status_sortorder").val().trim();
    datasend+='&task_status_disabled=' + (($('#mypostform #task_status_disabled').is(':checked')) ? '0':'1');

    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-crm-tasks-status-item-exec.php?id=' + <?php echo $id;?>,
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
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


