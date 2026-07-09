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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_whi_mov',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$gks_custom_prepare = gks_custom_table_item_prepare('gks_assets_whi_mov',['from'=>'item']);



if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id==-1) {
  $row=array();
  $row['id_assets_whi_mov']=-1;
  $row['mydate']=date('Y-m-d H:i:s');
  $row['assets_whi_mov_status']='00draft';
  $row['warehouse_id']=0;
  $row['warehouse_name']='';
  $row['whi_mov_sxolio']='';
  $row['user_id_add']=0;
  $row['user_id_edit']=0;
  $row['myip']='';
  
  $my_page_title=gks_lang('Νέα Απογραφή Παγίων');
} else {
  $sql = "SELECT gks_assets_whi_mov.*, 
  wp_users_add.gks_nickname AS gks_nickname_add, 
  wp_users_edit.gks_nickname AS gks_nickname_edit, 
  gks_warehouses.warehouse_name
  FROM ((gks_assets_whi_mov 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS wp_users_add ON gks_assets_whi_mov.user_id_add = wp_users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS wp_users_edit ON gks_assets_whi_mov.user_id_edit = wp_users_edit.ID) 
  LEFT JOIN gks_warehouses ON gks_assets_whi_mov.warehouse_id = gks_warehouses.id_warehouse
  where id_assets_whi_mov=".$id;

  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Απογραφή Παγίων').': '.$id;
}

$mystate=$row['assets_whi_mov_status'];
$warehouse_id = $row['warehouse_id'];

$gks_lock=false;$gks_flock='';
if ($mystate=='99complete') {
  $gks_lock=true;
  $gks_flock='form-control-sm gks_flock';
}




$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();
$nav_active_array=array('assets','assets_whi_mov');

$sql_eidi="SELECT gks_assets_whi_mov_assets.*, 
gks_assets.asset_code, gks_assets.asset_title, gks_assets.asset_serialnumber, gks_assets.asset_type, gks_assets_type.asset_type_descr, gks_assets.is_fotografou
FROM (gks_assets_whi_mov_assets 
LEFT JOIN gks_assets ON gks_assets_whi_mov_assets.asset_id = gks_assets.id_asset) 
LEFT JOIN gks_assets_type ON gks_assets.asset_type = gks_assets_type.id_asset_type
where assets_whi_mov_id=".$id."
order by id_assets_whi_mov_assets";
$result_eidi = $db_link->query($sql_eidi);     
if (!$result_eidi) debug_mail(false,'error sql',$sql_eidi);
if (!$result_eidi) die('sql error');
$eidi_list=array();
while ($row_eidi = $result_eidi->fetch_assoc()) {
  $eidi_list[] = $row_eidi;
}


include_once('_my_header_admin.php');
?>

<link href="css/admin-assets-whi-mov-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<style>
<?php if ($mystate=='00draft') {?>
.item_posotita_found {
  cursor:pointer; 
}
<?php } ?>  
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Απογραφή Παγίων');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Απογραφή Παγίων');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="assets_whi_mov_status" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($mystate=='99complete') {
              echo get_assets_whi_mov_descr('99complete');
            } else {?>
            <select id="assets_whi_mov_status" class="form-control form-control-sm myneedsave gks_select2">
              <option value="00draft"    <?php if ($row['assets_whi_mov_status']=='00draft') echo 'selected';?>><?php echo get_assets_whi_mov_descr('00draft');?></option>  
              <option value="99complete" <?php if ($row['assets_whi_mov_status']=='99complete') echo 'selected';?>><?php echo get_assets_whi_mov_descr('99complete');?></option>  
            </select>
            <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="mydate" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($mystate=='99complete') {
              echo showDate(strtotime($row['mydate']), 'd/m/Y H:i', 1);
            } else {?>
              <input id="mydate" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['mydate'])) echo  showDate(strtotime($row['mydate']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
            <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="warehouse_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποθήκη');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($mystate=='99complete') {
              echo $row['warehouse_name'];
            } else {?>
              <input id="warehouse_id" data-id="<?php echo $row['warehouse_id']?>" type="text" class="form-control form-control-sm" value="<?php if ($row['warehouse_id']>0) echo $row['warehouse_name']?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="whi_mov_sxolio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8 <?php echo $gks_flock;?>">
            <?php if ($mystate=='99complete') {
              echo nl2br_gks($row['whi_mov_sxolio']);
            } else {?>
              <textarea id="whi_mov_sxolio" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;"><?php echo $row['whi_mov_sxolio'];?></textarea>
            <?php } ?>
            </div>
          </div>
 


        </div>
      </div>
    </div>
            
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>
     
    </div>
  </div>
</div>


<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πάγια');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('items');?>>       

<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="mythistable">
<thead>
  <tr >	

    <th class="table-dark" scope="col" style="text-align:center !important;width:0%"  nowrap><?php echo gks_lang('Α/Α');?></th>     
    <th class="table-dark" scope="col" style="text-align:left   !important;width:30%" nowrap><?php echo gks_lang('Πάγιο');?></th>     
    <th class="table-dark" scope="col" style="text-align:center !important;width:10%" nowrap><span class="tooltipster" title="<?php echo gks_lang('Υπάρχει Θεωρητικά');?>"><?php echo gks_lang('Υ.Θ.');?></span></th>     
    <th class="table-dark" scope="col" style="text-align:center !important;width:10%" nowrap><span class="tooltipster" title="<?php echo gks_lang('Υπάρχει Πραγματικά');?>"><?php echo gks_lang('Υ.Π.');?></span></th>     
    <th class="table-dark" scope="col" style="text-align:left   !important;width:50%" nowrap><?php echo gks_lang('Σχόλιο');?></th>     
<?php if ($gks_lock==false) {?>
    <th class="table-dark" scope="col" style="text-align:center !important;width:0%"  nowrap><i class="fas fa-cog"></i></th>     
<?php }?>    
  <tr>
</thead>
<tbody>
<?php

$aa=0;
foreach ($eidi_list as $val) {
  $aa++;
  ?>
 
  <tr class="item_tr" data-aa="<?php echo $aa;?>" data-rec="<?php echo $val['id_assets_whi_mov_assets'];?>">
    <td class="mytdcm item_aa" nowrap><?php echo $aa;?></td>     
    <td class="mytdcml" nowrap>
      <?php if ($gks_lock) {?>
        <a href="admin-assets-item.php?id=<?php echo $val['asset_id'];?>"><?php echo $val['asset_code'].' - '.$val['asset_title'].' - '.$val['asset_serialnumber'];?></a>
      <?php } else { ?>
        <input data-id="<?php echo $val['asset_id'];?>" value="<?php echo $val['asset_code'].' - '.$val['asset_title'].' - '.$val['asset_serialnumber'];?>" class="item_asset form-control form-control-sm myneedsave" data-aa="<?php echo $aa;?>" type="text"> 
      <?php }?>
   </td>     
          
    <td class="mytdcm" nowrap><?php 
      //var_dump($val['posotita_theori']);
      if ($val['posotita_theori']!==null) {
        if ($val['posotita_theori']==0)
          echo '<img src="img/0.png" border="0" width="32" data-aa="'.$aa.'" class="item_posotita_theori" data-val="0">';
        else if ($val['posotita_theori']==1)
          echo '<img src="img/1.png" border="0" width="32" data-aa="'.$aa.'" class="item_posotita_theori" data-val="1">';
      } else {
        echo '<img src="img/1bg.png" border="0" width="32" data-aa="'.$aa.'" class="item_posotita_theori" data-val="">';
      }
    ?></td>     
    <td class="mytdcm" nowrap><?php 
      if ($val['posotita_found']!==null) {
        if ($val['posotita_found']==0)
          echo '<img src="img/0.png" border="0" width="32" data-aa="'.$aa.'" class="item_posotita_found" data-val="0">';
        else if ($val['posotita_found']==1)
          echo '<img src="img/1.png" border="0" width="32" data-aa="'.$aa.'" class="item_posotita_found" data-val="1">';
      } else {
        echo '<img src="img/1bg.png" border="0" width="32" data-aa="'.$aa.'" class="item_posotita_found" data-val="">';
      }
    ?></td>     
    <td class="mytdcml" nowrap>
      <?php if ($gks_lock) {?>
        <?php echo nl2br_gks($val['posotita_sxolio']);?>
      <?php } else { ?>
        <input value="<?php echo $val['posotita_sxolio'];?>" class="item_sxolio form-control form-control-sm myneedsave" data-aa="<?php echo $aa;?>" type="text"> 
      <?php }?>
    </td> 
<?php if ($gks_lock==false) {?>
    <td class="mytdcm" nowrap>
      <i class="fas fa-trash-alt   item_remove" data-aa="<?php echo $aa;?>"></i>
      <i class="fas fa-plus-circle item_add"    data-aa="<?php echo $aa;?>"></i>
    </td>
<?php }?>    
        
  </tr>


<?php 
} 
?>
</tbody>
</table>  


        </div>
      </div>
    </div>
  </div>
</div>


<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_assets_whi_mov'];?>" data-model="gks_assets_whi_mov" data-backurl="admin-assets-whi-mov.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <?php
      echo getObjectRels('gks_assets_whi_mov',$id);
      echo getActivityObjectTable('gks_assets_whi_mov',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_assets_whi_mov','id'=>$id));
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_assets_whi_mov']>0) echo $row['id_assets_whi_mov'];?></span></div>
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

var from_php_dialog_object_rel_curr='gks_assets_whi_mov';
var from_php_activity_model='gks_assets_whi_mov';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');




var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;

var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 

var from_php_perm_ret_edit  =<?php if ($gks_lock) echo 'false'; else echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_whi_mov','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_whi_mov','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_whi_mov','delete',$id);?>;



var from_php_aa=<?php echo $aa;?>;
var from_php_mystate='<?php echo $mystate;?>';

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  

 
  
});
</script>

<script src='/my/js/tinymce/tinymce.min.js'></script>


<script src="js/admin-assets-whi-mov-item.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


