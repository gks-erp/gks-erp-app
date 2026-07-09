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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sms_viber_template',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }




if ($id==-1) {

  $copy_id=0;
  if (isset($_GET['copy'])) $copy_id=intval($_GET['copy']);
  if ($copy_id > 0) {
    //echo time();
    //die();
    //product_photo
      
    $sql="INSERT INTO gks_sms_viber_template (
    sms_viber_template_name,
    sms_viber_template_text,
    sms_viber_template_disabled,
    sms_viber_template_sortorder,
    sms_enabled,
    viber_enabled,
    other_fields,
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip)
    
    SELECT CONCAT(sms_viber_template_name,' draft ".rand(1000,9999)."') as sms_viber_template_name,
    sms_viber_template_text,
    1 as sms_viber_template_disabled,
    sms_viber_template_sortorder,
    sms_enabled,
    viber_enabled,
    other_fields,
    now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip
    from gks_sms_viber_template
    WHERE id_sms_viber_template=".$copy_id;
    
    //print '<pre>';
    //print $sql;
    //die();
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    $id = $db_link->insert_id;

    //var_dump($id);
    //die();    
    if ($id > 0) {
      header('Location: ?id='.$id);
      die();
    }
  }

  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_sms_viber_template']=-1;
  $row['sms_viber_template_name']='';
  $row['sms_viber_template_text']='';
  $row['sms_viber_template_sortorder']=1000;
  $row['sms_viber_template_disabled']=0;
  $row['sms_enabled']=1;
  $row['viber_enabled']=1;
  $row['other_fields']='';
  
  $my_page_title=gks_lang('Νέο Πρότυπο κείμενο για SMS-Viber');
} else {
  $sql ="SELECT gks_sms_viber_template.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM (gks_sms_viber_template 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_sms_viber_template.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_sms_viber_template.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_sms_viber_template = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Πρότυπο κείμενο για SMS-Viber').': '.$row['sms_viber_template_name'];
  $object_title=$row['sms_viber_template_name'];
}




$nav_active_array=array('crm','manage_sms','manage_viber','manage_sms_viber_templates');

$sql_fobjects="SELECT gks_sms_viber_template_object.object_descr
FROM gks_sms_viber_template_object_forms 
LEFT JOIN gks_sms_viber_template_object ON gks_sms_viber_template_object_forms.sms_viber_template_object_id = gks_sms_viber_template_object.id_sms_viber_template_object
WHERE gks_sms_viber_template_object_forms.sms_viber_template_id=".$id."
ORDER BY gks_sms_viber_template_object.object_descr";
$result_fobjects = $db_link->query($sql_fobjects);        
if (!$result_fobjects) {debug_mail(false,'error sql',$sql_fobjects);die('sql error');}
$fobjects=array();
while ($row_fobjects = $result_fobjects->fetch_assoc()) {
  $fobjects[]=$row_fobjects['object_descr'];
}
$fobjects_text=implode(']][[',$fobjects);

$gks_fobjects_tags=array();
$sql_fobjects="select object_descr from gks_sms_viber_template_object order by object_descr";
$result_fobjects = $db_link->query($sql_fobjects);        
if (!$result_fobjects) {debug_mail(false,'error sql',$sql_fobjects);die('sql error');}
$gks_fobjects_tags=array();
while ($row_fobjects = $result_fobjects->fetch_assoc()) {
  $gks_fobjects_tags[]=$row_fobjects['object_descr'];
}

stat_record();

include_once('_my_header_admin.php');
?>
<link rel="stylesheet" href="/my/css/admin-sms-viber-templates-item.css?v=<?php echo $gks_cache_version;?>" type="text/css">    


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Πρότυπο κείμενο για SMS-Viber');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Πρότυπο κείμενο για SMS-Viber');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
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
            <label for="sms_viber_template_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-8">
              <input id="sms_viber_template_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['sms_viber_template_name']);?>">
            </div>
          </div>

          <div class="form-group row">
            <label for="sms_viber_template_text" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κείμενο');?>:</label>
            <div class="col-sm-8">
              <textarea id="sms_viber_template_text" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['sms_viber_template_text']);?></textarea>
              <br />
              <small id="sms_chars"></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="sms_viber_template_sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="sms_viber_template_sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['sms_viber_template_sortorder']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="sms_enabled" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Για SMS');?>:</label>
            <div class="col-sm-8">
              <input type="checkbox" id="sms_enabled" value="1" <?php if ($row['sms_enabled']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row">
            <label for="viber_enabled" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Για Viber');?>:</label>
            <div class="col-sm-8">
              <input type="checkbox" id="viber_enabled" value="1" <?php if ($row['viber_enabled']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          
          
          <div class="form-group row">
            <label for="sms_viber_template_disabled" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-sm-8">
              <input type="checkbox" id="sms_viber_template_disabled" value="1" <?php if ($row['sms_viber_template_disabled']==0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>

          

        </div>
      </div>


    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Περιορισμοί');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('apply');?>>      
          <div class="form-group row">
            <label for="gks_fobjects" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αντικείμενα');?>:</label>
            <div class="col-md-8" id="field_gks_fobjects">
              <input id="gks_fobjects" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $fobjects_text;?>">
            </div>
          </div>

        </div>
      </div>
      
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Παράμετροι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('params');?>>
          <?php
          
//1 label id
//2 type
//3 px
//4 icon
//5 value
//6 jquery_selector
//7 buttons

          
          $gkscols_parameter1 ='col-12 col-sm-6  col-md-4  col-lg-1 gks_items_col';
          $gkscols_parameter2 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col';
          $gkscols_parameter3 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col';
          $gkscols_parameter4 ='col-12 col-sm-6  col-md-3  col-lg-2 gks_items_col';
          $gkscols_parameter5 ='col-12 col-sm-6  col-md-3  col-lg-2 gks_items_col';
          $gkscols_parameter6 ='col-12 col-sm-6  col-md-3  col-lg-2 gks_items_col';
          $gkscols_parameter7 ='col-12 col-sm-12 col-md-3  col-lg-1 gks_items_col';
          
          
          ?>
          <div class="form-group row gks_parameter_label">
            <div class="<?php echo $gkscols_parameter1;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Παράμετρος');?></div>
            </div>
            <div class="<?php echo $gkscols_parameter2;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Τύπος');?></div>
            </div>

            <div class="<?php echo $gkscols_parameter3;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Παράδειγμα');?></div>
            </div>
            <div class="<?php echo $gkscols_parameter4;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Εικονίδιο');?></div>
            </div>
            <div class="<?php echo $gkscols_parameter5;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Τιμή');?></div>
            </div>
            <div class="<?php echo $gkscols_parameter6;?>">
              <div class="table-dark gks_parameter_label">jQuery Selector</div>
            </div>
            
            <div class="<?php echo $gkscols_parameter7;?>">
              <div class="table-dark gks_parameter_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
            </div> 

          </div>          
          <div id="parameters_table">

          <?php
          $params=[];
          $row['other_fields']=trim_gks($row['other_fields']);
          if ($row['other_fields']!='') {
            $params=json_decode($row['other_fields'],true);
          }
          $bb = 0;
          foreach ($params as $vparam) {
                      
          
          
            $bb++;
            ?>
          <div class="form-group row gks_parameter_line" data-bb="<?php echo $bb;?>">
            <div class="<?php echo $gkscols_parameter1;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_label" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['label'])) echo $vparam['label']?>"/>
            </div>
            
            <div class="<?php echo $gkscols_parameter2;?>">
              <select class="form-control form-control-sm myneedsave gks_select2 gks_fparam_type" data-bb="<?php echo $bb;?>">
              <option <?php if ($vparam['type']=='text') echo 'selected';?> value="text"><?php echo gks_lang('Κείμενο');?></option>
              <option <?php if ($vparam['type']=='textarea') echo 'selected';?> value="textarea"><?php echo gks_lang('Μεγάλο κείμενο');?></option>
              </select>
            </div>
            <div class="<?php echo $gkscols_parameter3;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_px" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['px'])) echo $vparam['px']?>"/>
            </div>
            <div class="<?php echo $gkscols_parameter4;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_icon" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['icon'])) echo $vparam['icon']?>"/>
            </div>
            <div class="<?php echo $gkscols_parameter5;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_value" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['value'])) echo $vparam['value']?>"/>
            </div>            
            <div class="<?php echo $gkscols_parameter6;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_jquery_selector" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['jquery_selector'])) echo $vparam['jquery_selector']?>"/>
            </div> 
                          
            <div class="<?php echo $gkscols_parameter7;?>">
              <div class="text-center gks_parameter_icons">
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_delete_parameterline" data-bb="<?php echo $bb;?>"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_parameterline_handle"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_add_parameterline"  data-bb="<?php echo $bb;?>"></i>
                </div>
                
                
              </div>
            </div>
            
          </div>
          <?php 
          }
          ?>        
        
        
          <div class="row" id="gks_parameter_footer1"></div>
          </div>            
            

                    
        </div>
      </div>
    </div>
  </div>
</div>           


<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_sms_viber_template'];?>" data-model="gks_sms_viber_template" data-backurl="admin-sms-viber-templates.php"><?php echo gks_lang('Διαγραφή');?></button>
      <?php if ($id>0) {?>
      <button type="button" class="btn btn-primary" id="submit_button_copy" onclick="window.location.href='admin-sms-viber-templates-item.php?id=-1&copy=<?php echo $id;?>'"><?php echo gks_lang('Δημιουργία αντιγράφου');?></button>
      <?php } ?>

    </div>
  </div>
</div>


<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
                
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_sms_viber_template']>0) echo $row['id_sms_viber_template'];?></span></div>
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
<?php echo from_php_global_vars_echo();?>;

var from_php_id=<?php echo $id;?>;

var from_php_dialog_object_rel_curr='gks_sms_viber_template';
var from_php_activity_model='gks_sms_viber_template';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_sms_viber_template','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_sms_viber_template','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_sms_viber_template','delete',$id);?>;

var last_bb=<?php echo $bb;?>;
var from_php_gkscols_parameter1='<?php echo $gkscols_parameter1;?>';
var from_php_gkscols_parameter2='<?php echo $gkscols_parameter2;?>';
var from_php_gkscols_parameter3='<?php echo $gkscols_parameter3;?>';
var from_php_gkscols_parameter4='<?php echo $gkscols_parameter4;?>';
var from_php_gkscols_parameter5='<?php echo $gkscols_parameter5;?>';
var from_php_gkscols_parameter6='<?php echo $gkscols_parameter6;?>';
var from_php_gkscols_parameter7='<?php echo $gkscols_parameter7;?>';


var gks_fobjects_tags = [];
<?php 
  foreach ($gks_fobjects_tags as $value) {
     echo "  gks_fobjects_tags.push('".$value."');"."\n";
  } 
?> 

var from_php_enter_parameter_order=[];
<?php
$enter_parameter_order=array(
  'gks_fparam_label',
  'gks_fparam_type',
  'gks_fparam_px',
  'gks_fparam_icon',
  'gks_fparam_value',
  'gks_fparam_jquery_selector',
  'new_row',
);
foreach ($enter_parameter_order as $value) {
  echo 'from_php_enter_parameter_order.push(\''.$value.'\');'."\n";
}
?>

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  var control_enter_active=false;
  

    
});
</script>

<script src="js/admin-sms-viber-templates-item.js?v=<?php echo $gks_cache_version;?>"></script>


<?php


include_once('_my_footer_admin.php');


