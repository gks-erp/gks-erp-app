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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_service_reasons',($id==-1 ? 'add' : 'view'),$id);
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
  $row['id_assets_service_reasons'] =-1;

  $row['reasons_descr']='';
  $row['assets_service_reason_sortorder']=1000;
  $row['assets_service_reason_disable']=0;
  

  $my_page_title=gks_lang('Νέα Αιτία Service Παγίου');
} else {

  $sql ="SELECT gks_assets_service_reasons.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
  FROM (gks_assets_service_reasons 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_assets_service_reasons.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_assets_service_reasons.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_assets_service_reasons = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Αιτία Service Παγίου').': '.$row['reasons_descr'];
  $object_title=$row['reasons_descr'];
}



stat_record();
$nav_active_array=array('assets','assets_service_reasons');


$sql_lista="SELECT gks_assets_type.asset_type_descr
FROM gks_assets_service_reasons_types LEFT JOIN gks_assets_type ON gks_assets_service_reasons_types.type_id = gks_assets_type.id_asset_type
WHERE (((gks_assets_service_reasons_types.reasons_id)=".$id."))
ORDER BY asset_type_sortorder,gks_assets_type.asset_type_descr;";
$result_lista = $db_link->query($sql_lista);        
if (!$result_lista) {debug_mail(false,'error sql',$sql_lista);die('sql error');}
$row['assets_types']=array();
while ($row_lista = $result_lista->fetch_assoc()) {
  $row['assets_types'][]=$row_lista['asset_type_descr'];
}  

include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Αιτία Service Παγίου');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Αιτία Service Παγίου');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="reasons_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="reasons_descr" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['reasons_descr']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="assets_types" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύποι Παγίων');?>:</label>
            <div class="col-md-8">
              <input id="assets_types" type="text" class="form-control form-control-sm" value="">
            </div>
          </div>
          <div class="form-group row">
            <label for="assets_service_reason_sortorder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-md-8">
              <input id="assets_service_reason_sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['assets_service_reason_sortorder'];?>" min="0" step="1">
            </div>
          </div>           

          <div class="form-group row">
            <label for="assets_service_reason_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="assets_service_reason_disable" value="1" <?php if ($row['assets_service_reason_disable']==0) echo ' checked '; ?> class="switchery1_sel">
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_assets_service_reasons'];?>" data-model="gks_assets_service_reasons" data-backurl="admin-assets-service-reasons.php"><?php echo gks_lang('Διαγραφή');?></button>
      
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <?php 
      echo getObjectRels('gks_assets_service_reasons',$id); 
      echo getActivityObjectTable('gks_assets_service_reasons',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_assets_service_reasons','id'=>$id));
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_assets_service_reasons']>0) echo $row['id_assets_service_reasons'];?></span></div>
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

var from_php_dialog_object_rel_curr='gks_assets_service_reasons';
var from_php_activity_model='gks_assets_service_reasons';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_service_reasons','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_service_reasons','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_service_reasons','delete',$id);?>;



jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  

 
    
  function mysubmit() {
    datasend='';
    datasend+='&reasons_descr='  + encodeURIComponent($.base64.encode($("#mypostform #reasons_descr").val().trim()));
    datasend+='&assets_types='  + encodeURIComponent($.base64.encode($("#mypostform #assets_types").val().trim()));
    datasend+='&assets_service_reason_sortorder='  + $("#mypostform #assets_service_reason_sortorder").val().trim();
    datasend+='&assets_service_reason_disable=' + (($('#mypostform #assets_service_reason_disable').is(':checked')) ? '0':'1');

    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-assets-service-reasons-item-exec.php?id=' + <?php echo $id;?>,
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
  



  var load_tags=true;
  
  var assets_types_valid=[];
  $('#assets_types').tagit({
    gks_this: $(this),
    allowSpaces: true, 
    showAutocompleteOnFocus : false,
    removeConfirmation: true,
    singleFieldDelimiter: ']][[',
    //placeholderText:'ssss',
    autocomplete: {
      gks_this: $(this),
      source: function(request, response) {
        mydata={
          term: request.term,
        };
        $.ajax({
          url: 'admin-autocomplete-assets_types-tagit.php',
          dataType: "json",
          cache: false,
          data: mydata,
          error : function(jqXHR ,textStatus,  errorThrown) {
    				myalert('error:' + jqXHR.responseText);
    			},
          success: function( data ) {
            if (data.success == true) {
              response( data.list);
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        });
      },
      minLength: 0,
      autoFocus: true,
      delay: 300, //default
    },
    preprocessTag: function(val) {
      if (!val) { return ''; }
  		if (load_tags || $.inArray(val, assets_types_valid) >= 0) {
  		  return val;
  		} else {
        $.ajax({
    			url: 'admin-autocomplete-assets_types-tagit.php?equal=1&term=' + encodeURI(val),
    			type: 'GET',
    			cache: false,
    			dataType: 'json',
    			error : function(jqXHR ,textStatus,  errorThrown) {
    				myalert('error:' + jqXHR.responseText);
    			},				
    			success: function(data) {
    			  //console.log('equal term',data);
    				if (!data) {
    					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    				} else {
    					if (data.success == true) {
    					  assets_types_valid.push(data.out[0].value);
      					$('#assets_types').tagit('createTag', data.out[0].value);
    					} else {
    						myalert('error:'+gks_lang('Επιλέξτε κάποιο από τα διαθέσιμα'));
    					}
    				}
    			}
    		});   		  
  		  return '';
  		}
    },
  });  


<?php
  foreach ($row['assets_types'] as $value) {echo "$('#assets_types').tagit('createTag', '".$value."');"."\r\n";}   
  
  
?>

  load_tags=false;
  
  $('#assets_types').tagit('set_showAutocompleteOnFocus_on');
  
  
});
</script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


