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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_channel_sale',($id==-1 ? 'add' : 'view'),$id);
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
  $row['id_crm_channel_sale'] =-1;

  $row['crm_channel_sale_descr']='';
  $row['crm_channel_sale_sortorder']=1000;
  $row['crm_channel_sale_disabled']=0;
  $row['crm_channel_has_contact']=0;
  $row['crm_channel_has_contact_filter']='';
  $row['crm_channel_has_campain']=0;
  $row['crm_channel_has_url']=0;
  $row['crm_channel_has_text']=0;
  $row['crm_channel_has_code']=0;

  $my_page_title=gks_lang('Νέο Κανάλι πωλήσεων');
} else {

  $sql ="SELECT gks_crm_channel_sale.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
  FROM (gks_crm_channel_sale 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_channel_sale.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_channel_sale.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_crm_channel_sale = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Κανάλι πωλήσεων').': '.$row['crm_channel_sale_descr'];
  $object_title=$row['crm_channel_sale_descr'];
}



stat_record();
$nav_active_array=array('crm','crm_channel_sale');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Κανάλι πωλήσεων');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Κανάλι πωλήσεων');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
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
            <label for="crm_channel_sale_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_sale_descr" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['crm_channel_sale_descr']);?>">
            </div>
          </div>

 


          <div class="form-group row">
            <label for="crm_channel_has_contact" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έχει επαφή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="crm_channel_has_contact" value="1" <?php if ($row['crm_channel_has_contact']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>  

          <div class="form-group row" id="crm_channel_has_contact_filter_div" style="<?php if ($row['crm_channel_has_contact']==0) echo  'display:none';?>">
            <label for="crm_channel_has_contact_filter" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φίλτρο επαφών');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_has_contact_filter" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['crm_channel_has_contact_filter']);?>">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="crm_channel_has_campain" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έχει καμπάνια');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="crm_channel_has_campain" value="1" <?php if ($row['crm_channel_has_campain']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>  

          <div class="form-group row">
            <label for="crm_channel_has_url" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έχει URL');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="crm_channel_has_url" value="1" <?php if ($row['crm_channel_has_url']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>  
          <div class="form-group row">
            <label for="crm_channel_has_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έχει κωδικό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="crm_channel_has_code" value="1" <?php if ($row['crm_channel_has_code']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>  

          <div class="form-group row">
            <label for="crm_channel_has_text" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έχει σχόλιο');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="crm_channel_has_text" value="1" <?php if ($row['crm_channel_has_text']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>  



          <div class="form-group row">
            <label for="crm_channel_sale_sortorder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_sale_sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['crm_channel_sale_sortorder'];?>" min="0" step="1">
            </div>
          </div>           
          <div class="form-group row">
            <label for="crm_channel_sale_disabled" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="crm_channel_sale_disabled" value="1" <?php if ($row['crm_channel_sale_disabled']==0) echo ' checked '; ?> class="switchery1_sel">
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_crm_channel_sale'];?>" data-model="gks_crm_channel_sale" data-backurl="admin-crm-channel-sale.php"><?php echo gks_lang('Διαγραφή');?></button>
      
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      
      <?php 
      echo getObjectRels('gks_crm_channel_sale',$id);
      echo getActivityObjectTable('gks_crm_channel_sale',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_crm_channel_sale','id'=>$id));
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_crm_channel_sale']>0) echo $row['id_crm_channel_sale'];?></span></div>
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

var from_php_dialog_object_rel_curr='gks_crm_channel_sale';
var from_php_activity_model='gks_crm_channel_sale';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_channel_sale','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_channel_sale','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_channel_sale','delete',$id);?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  

    
  function mysubmit() {
    datasend='';
    datasend+='&crm_channel_sale_descr='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_sale_descr").val().trim()));
    datasend+='&crm_channel_sale_sortorder='  + $("#mypostform #crm_channel_sale_sortorder").val().trim();
    datasend+='&crm_channel_sale_disabled=' + (($('#mypostform #crm_channel_sale_disabled').is(':checked')) ? '0':'1');
    datasend+='&crm_channel_has_contact=' + (($('#mypostform #crm_channel_has_contact').is(':checked')) ? '1':'0');
    datasend+='&crm_channel_has_contact_filter='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_has_contact_filter").val().trim()));
    datasend+='&crm_channel_has_campain=' + (($('#mypostform #crm_channel_has_campain').is(':checked')) ? '1':'0');
    datasend+='&crm_channel_has_url=' + (($('#mypostform #crm_channel_has_url').is(':checked')) ? '1':'0');
    datasend+='&crm_channel_has_text=' + (($('#mypostform #crm_channel_has_text').is(':checked')) ? '1':'0');
    datasend+='&crm_channel_has_code=' + (($('#mypostform #crm_channel_has_code').is(':checked')) ? '1':'0');

    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-crm-channel-sale-item-exec.php?id=' + <?php echo $id;?>,
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
  

  $('#crm_channel_has_contact').change(function() {
    if ($(this).is(':checked')) {
      $('#crm_channel_has_contact_filter_div').slideDown();
    } else {
      $('#crm_channel_has_contact_filter_div').slideUp();
    }
  });
  

  
});
</script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


