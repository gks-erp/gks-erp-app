<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


//https://test.easyfilesselection.com/my/admin-assets-item.php?id=-1
$nav_active_array=array('assets','assets_assets');


db_open();
$id=0; if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_gks_assets_service_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_service','delete',0);


$gks_custom_prepare = gks_custom_table_item_prepare('gks_assets',['from'=>'item']);
if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  
  $row['id_asset']=-1;
  $row['asset_code']='';
  $row['asset_photo']='';
  $row['asset_title']='';
  $row['asset_serialnumber']='';
  $row['asset_type']=0;
  $row['asset_sxolio']='';
  $row['asset_last_warehouse_id']=0;
  $row['asset_last_user_id']=0;
  $row['asset_last_company_id']=0;
  //$row['asset_last_mixani_id']=0;
  $row['asset_disable']=0;
  $row['is_fotografou']=0;
  $row['bank_id']=0;
  $row['xreosi_val']=0;
  $row['xreosi_type']=0;
  $row['oxima_km']=0;
  $row['oxima_next_kteo']=null;
  $row['oxima_next_service_km']=0;
  //$row['oxima_liji_asfaleia']=null;
  $row['oxima_elastika']='';
  $row['service_frontier_text']='';
  //$row['oxima_km_date']=null;
  //$row['last_action_date']=null;
  $row['last_action_warehouse_id']=0;
  $row['last_action_source']='';
  $row['last_action_ip']='';
  $row['mac_address']='';
  //$row['mixani_esn']=0;
  $row['asset_thesi']='';
  
  $row['viva_company_id']=0;
  $row['viva_terminal_id']='';
  $row['viva_terminal_code']='';
  $row['viva_action_after']='';
  $row['viva_def_ref_pliromis']='';

  $row['megeftpos_company_id']=0;
  $row['megeftpos_terminal_id']='';
  $row['megeftpos_static_ip']='';
  $row['megeftpos_port']=0;
  $row['megeftpos_protocol']=0;
  $row['megeftpos_erp_app_id']=0;
  //$row['megeftpos_ecr2eftweb_service_url']='';
  $row['megeftpos_pos_id']='';
  $row['megeftpos_api_key']='';
  
  
  
  $row['mellon_company_id']=0;
  $row['mellon_id']='';
  $row['mellon_terminal_id']='';
  
  $row['cardlink_company_id']=0;
  $row['cardlink_terminal_id']='';
  $row['cardlink_static_ip']='';
  $row['cardlink_port']=0;
  $row['cardlink_ecr2eftweb_erp_app_id']=0;
  $row['cardlink_ecr2eftweb_service_url']='';

  $row['epay_company_id']=0;
  $row['epay_id']='';
  $row['epay_terminal_id']='';
  
  $row['worldline_company_id']=0;
  $row['worldline_id']='';
  $row['worldline_terminal_id']='';
  
  $row['nexi_company_id']=0;
  $row['nexi_id']='';
  $row['nexi_terminal_id']='';
  
  
  $row['asset_rental_status']='';

  $row['warehouse_name']='';
  $row['gks_nickname']='';
  $row['company_title']='';
  

  
  
  $my_page_title=gks_lang('Νέο πάγιο');
} else {
  $sql ="SELECT gks_assets.*, gks_warehouses.warehouse_name, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  gks_company.company_title, gks_banks.bank_descr, gks_assets_type.asset_type_descr,
  lastactionwarehouse.warehouse_name as last_action_warehouse_name,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, 
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  
  FROM (((((((gks_assets 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_assets.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_assets.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_warehouses ON gks_assets.asset_last_warehouse_id = gks_warehouses.id_warehouse) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets.asset_last_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_company ON gks_assets.asset_last_company_id = gks_company.id_company) 
  LEFT JOIN gks_banks ON gks_assets.bank_id = gks_banks.id_bank) 
  LEFT JOIN gks_assets_type ON gks_assets.asset_type = gks_assets_type.id_asset_type)
  LEFT JOIN gks_warehouses as lastactionwarehouse ON gks_assets.last_action_warehouse_id = lastactionwarehouse.id_warehouse

  where gks_assets.id_asset = ".$id;
  //echo '<pre>'.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Πάγιο').': '.$row['asset_title'];  
}
stat_record();

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

$row_asset_last_warehouse_id=$row['asset_last_warehouse_id'];
$row_asset_last_user_id = $row['asset_last_user_id'];
$row_asset_last_mixani_id =0 ;// $row['asset_last_mixani_id'];
$row_asset_type_id = intval($row['asset_type']);

$from_php_asset_type_mixani0='true'; //(!isset($row['asset_last_mixani_id']) or $row['asset_last_mixani_id']==0) ? 'true'; : 'false';

$isservice=false;
if ($id>0) {
  $sql="select id_assets_service from gks_assets_service where asset_id=".$id." and (mydate_return is null or (mydate_return is not null and isconfirm=0))";
  $result_service = $db_link->query($sql);        
  if (!$result_service) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  
  if ($result_service->num_rows>0) {
    $isservice=true;
  }
}

$lang_data_obj=gks_lang_data_obj_prepare('gks_assets','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);

include_once('_my_header_admin.php');
?>



<link href="css/admin-assets-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Πάγιο');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $row['asset_title'];?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Πάγιο');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
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
            <label for="asset_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="asset_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['asset_code']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="asset_title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="asset_title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['asset_title']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('asset_title'));
          ?>          
          <div class="form-group row">
            <label for="asset_type" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-md-8">
              <select name="asset_type" id="asset_type" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
                $sql="select * FROM gks_assets_type where asset_type_disabled=0 ORDER BY asset_type_sortorder ";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_asset_type'].'" ';
                  if ($row_select['id_asset_type']==$row['asset_type']) echo ' selected ';
                  echo '>'.$row_select['asset_type_descr'].'</option>';
                }?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="asset_serialnumber" class="col-md-4 col-form-label form-control-sm text-md-right" id="label_serialnumber">Serial Number:</label>
            <div class="col-md-8">
              <input id="asset_serialnumber" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['asset_serialnumber']);?>">
            </div>
          </div>

          <div class="form-group row">
            <label for="asset_date_activate" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία Ενεργοποίησης');?>:</label>
            <div class="col-md-8">
              <input id="asset_date_activate" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['asset_date_activate'])) echo  showDate(strtotime($row['asset_date_activate']), 'd/m/Y', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
            </div>
          </div>
          <div class="form-group row">
            <label for="asset_date_aposirsi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία Απόσυρσης');?>:</label>
            <div class="col-md-8">
              <input id="asset_date_aposirsi" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['asset_date_aposirsi'])) echo  showDate(strtotime($row['asset_date_aposirsi']), 'd/m/Y', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
            </div>
          </div>
          <div class="form-group row">
            <label for="asset_sxolio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="asset_sxolio" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;"><?php echo $row['asset_sxolio'];?></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="asset_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="asset_disable" value="1" <?php if ($row['asset_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="is_fotografou" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Είναι του συνεργάτη');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="is_fotografou" value="1" <?php if ($row['is_fotografou']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>

          <div id="tr_bank_id" style="display:none;" class="form-group row">
            <label for="bank_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τράπεζα');?>:</label>
            <div class="col-md-8">
              <select id="bank_id" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
                $sql="select * FROM gks_banks ORDER BY bank_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_bank'].'" ';
                  if ($row_select['id_bank']==$row['bank_id']) echo ' selected ';
                  echo '>'.$row_select['bank_descr'].'</option>';
                }?>
              </select>
            </div>
          </div>
          
          <div id="tr_xreosi_val" style="display:none;" class="form-group row">
            <label for="xreosi_val" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρέωση');?>:</label>
            <div class="col-md-8">
              <input id="xreosi_val" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['xreosi_val']!=0) echo $row['xreosi_val'];?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
            </div>
          </div>

          <div id="tr_xreosi_type" style="display:none;" class="form-group row">
            <label for="xreosi_type" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος χρέωσης');?>:</label>
            <div class="col-md-8">
              <select id="xreosi_type" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <option value="1" <?php if ($row['xreosi_type']==1) echo ' selected ';?> ><?php echo gks_lang('ΔΩΡΕΑΝ');?></option>
                <option value="2" <?php if ($row['xreosi_type']==2) echo ' selected ';?> ><?php echo gks_lang('ΑΝΑ ΜΗΝΑ/ ΠΛΕΟΝ ΦΠΑ');?></option>
              </select>
            </div>
          </div>
 
          <div id="tr_oxima_elastika" style="display:none;" class="form-group row">
            <label for="oxima_elastika" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ελαστικά');?>:</label>
            <div class="col-md-8">
              <input id="oxima_elastika" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['oxima_elastika']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 195/65 R15">
            </div>
          </div>

          <div id="tr_oxima_km" style="display:none;" class="form-group row">
            <label for="oxima_km" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χιλιόμετρα τώρα');?>:</label>
            <div class="col-md-8">
              <input id="oxima_km" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['oxima_km']!=0) echo $row['oxima_km'];?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px" min="0" step="100">
            </div>
          </div>
          <div id="tr_oxima_next_service_km" style="display:none;" class="form-group row">
            <label for="oxima_next_service_km" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επόμενο Service σε Km');?>:</label>
            <div class="col-md-8">
              <input id="oxima_next_service_km" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['oxima_next_service_km']!=0) echo $row['oxima_next_service_km'];?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px" min="0" step="100">
            </div>
          </div>
          <div id="tr_oxima_next_kteo" style="display:none;" class="form-group row">
            <label for="oxima_next_kteo" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία Επόμενου ΚΤΕΟ');?>:</label>
            <div class="col-md-8">
              <input id="oxima_next_kteo" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['oxima_next_kteo'])) echo  showDate(strtotime($row['oxima_next_kteo']), 'd/m/Y', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
            </div>
          </div>
          <div id="tr_oxima_liji_asfaleia" style="display:none;" class="form-group row">
            <label for="oxima_liji_asfaleia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία Λήξης Ασφάλειας');?>:</label>
            <div class="col-md-8">
              <input id="oxima_liji_asfaleia" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['oxima_liji_asfaleia'])) echo  showDate(strtotime($row['oxima_liji_asfaleia']), 'd/m/Y', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:150px">
            </div>
          </div>


          <div id="tr_asset_thesi" style="display:none;" class="form-group row">
            <label for="asset_thesi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Θέση');?>:</label>
            <div class="col-md-8">
              <input id="asset_thesi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['asset_thesi']);?>" placeholder="<?php echo gks_lang('π.χ.');?> Γραφείο CEO">
            </div>
          </div>
          <div id="tr_mac_address" style="display:none;" class="form-group row">
            <label for="mac_address" class="col-md-4 col-form-label form-control-sm text-md-right">Mac Address:</label>
            <div class="col-md-8">
              <input id="mac_address" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['mac_address']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 0022B068FFFB,989096A6BA66">
            </div>
          </div>



        </div>
      </div>
    </div>
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Φωτογραφίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('photo');?>>       
          <div class="row">
            <div class="col-md-12" style="text-align:center;"><?php echo gks_lang('Η προεπιλεγμένη φωτογραφία του παγίου');?></div>
            
            <div class="col-md-12" style="text-align:center;">
              <?php
              $user_photo_value="";
              $myimgurl = $row['asset_photo']; //get_user_meta($my_wp_user_id, 'wsl_current_user_image', true);
              //echo $myimgurl;
              if ($myimgurl.'' == '') {
                $myimgurl="/my/img/product.png";
              } else {
                $user_photo_value = $myimgurl;
              }
              ?>
              <img src="<?php echo $myimgurl;?>" border="0" style="max-width:96px;max-height:96px;" id="form_asset_photo_img"/><br>
              
              <a href="" id="reset_profile_photo" title="<?php echo gks_lang('Διαγραφή');?>" <?php 
                if ($user_photo_value == '') {
                  echo ' style="display:none" ';
                }
                ?> ><img src="/my/img/0.png" border="0" width="16" ></a>
              <br><input type="hidden" id="form_asset_photo" name="form_asset_photo" value="<?php echo $user_photo_value;?>" />
            </div>                     
          </div>
          <div class="row">
            <div class="col-md-12" style="text-align:center; padding-top: 24px;"><?php echo gks_lang('Φωτογραφίες του παγίου');?></div>
            
            <form role="form" method="post" action="admin-assets-item-photo-upload.php" id="myphoto_upload" enctype="multipart/form-data" style="width: 100%;">
              <input type="hidden" name="asset_id" id="asset_id" value="<?php echo $id;?>">
              <div id="lightgallery_user">
                <div class="form-group" id="imagelist_photo">
                <?php   
                  $sql="select * from gks_assets_photo where asset_id=".$id." and filesobjectlist=0 order by id_asset_photo";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    $photo_url = $row_select['photo_url'];
                    $photo_url_thumb = dirname($row_select['photo_url']).'/thumbnail/'.mb_basename($row_select['photo_url']);


                    ?>
                    <div id="item_upload_photo_<?php echo $row_select['id_asset_photo'];?>" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">
                      <a class="lightgalleryitem_user" href="<?php echo $photo_url;?>" data-download-url="<?php echo $photo_url;?>">
                        <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="<?php echo $photo_url_thumb;?>">
                      </a>
                      <br>
                      <div style="padding-top:4px">
                        <a href="" class="set_profile_photo"   data-url="<?php echo $photo_url_thumb;?>" title="<?php echo gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία');?>"><img src="/my/img/icons/photo.png" border="0" width="16"></a>
                        <a href="" class="delete_upload_photo" data-url="<?php echo $photo_url_thumb;?>" data-id="<?php echo $row_select['id_asset_photo'];?>" title="<?php echo gks_lang('Διαγραφή');?>"><img src="/my/img/0.png" border="0" width="16"></a>
                      </div>
                    </div>
                  <?php }?>
                </div>
              </div>
              <?php gks_f_button_add_files_photo_html('gks_assets',$id);?>
            </form>                      
          </div>
          
        </div>
      </div>
      

      

      <div class="card gks_card_expand" id="card_viva" style="<?php if (!in_array($row_asset_type_id,[23,24,25,27])) echo 'display:none;';?>">
        <div class="card-header" style="text-align:center">
          Viva
        </div>
        <div class="card-body" <?php echo gks_card_body('viva');?>>       

          <div class="form-group row">
            <label for="viva_company_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="viva_company_id" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
                $sql="select id_company,company_title FROM gks_company where viva_merchant_id<>'' ORDER BY company_title";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_company'].'" ';
                  if ($row_select['id_company']==$row['viva_company_id']) echo ' selected ';
                  echo '>'.$row_select['company_title'].'</option>';
                }?>
              </select>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="viva_terminal_id" class="col-md-4 col-form-label form-control-sm text-md-right">Terminal ID:</label>
            <div class="col-md-8">
              <input id="viva_terminal_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['viva_terminal_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>
          <div class="form-group row">
            <label for="viva_terminal_code" class="col-md-4 col-form-label form-control-sm text-md-right">Virtual Terminal ID:</label>
            <div class="col-md-8">
              <input id="viva_terminal_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['viva_terminal_code']);?>" placeholder="<?php echo gks_lang('π.χ.');?> eNJQI_fiSkCDDP5Y1uWMIY">
            </div>
          </div>
          <div class="form-group row">
            <label for="viva_action_after" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μετά την συναλλαγή');?>:</label>
            <div class="col-md-8">
              <select id="viva_action_after" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0" <?php if ($row['viva_action_after']==0) echo 'selected';?>><?php echo gks_lang('Αυτόματο');?></option>
                <option value="1" <?php if ($row['viva_action_after']==1) echo 'selected';?>><?php echo gks_lang('Τίποτα');?></option>
                <option value="2" <?php if ($row['viva_action_after']==2) echo 'selected';?>><?php echo gks_lang('Απόκρυψη Viva');?></option>
                <option value="3" <?php if ($row['viva_action_after']==3) echo 'selected';?>><?php echo gks_lang('Εμφάνιση gks ERP App Mobile');?></option>
                <option value="4" <?php if ($row['viva_action_after']==4) echo 'selected';?>><?php echo gks_lang('Εμφάνιση Chrome');?></option>
                <option value="5" <?php if ($row['viva_action_after']==5) echo 'selected';?>><?php echo gks_lang('Εμφάνιση Safari');?></option>
                <option value="6" <?php if ($row['viva_action_after']==6) echo 'selected';?>><?php echo gks_lang('Εμφάνιση Firefox');?></option>
                
              </select>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="viva_def_ref_pliromis" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προεπιλεγμένη αναφορά πληρωμής');?>:</label>
            <div class="col-md-8">
              <input id="viva_def_ref_pliromis" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['viva_def_ref_pliromis']);?>">
            </div>
          </div>
          
        </div>
      </div>

      <div class="card gks_card_expand"  id="card_megeftpos" style="<?php if (!in_array($row_asset_type_id,[23,24,25,27])) echo 'display:none;';?>">
        <div class="card-header" style="text-align:center">
          Meg EFT/POS Driver
        </div>
        <div class="card-body" <?php echo gks_card_body('megeftpos');?>>  
          <div class="form-group row">
            <label for="megeftpos_company_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="megeftpos_company_id" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
                $sql="select id_company,company_title FROM gks_company ORDER BY company_title";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_company'].'" ';
                  if ($row_select['id_company']==$row['megeftpos_company_id']) echo ' selected ';
                  echo '>'.$row_select['company_title'].'</option>';
                }?>
              </select>
            </div>
          </div>
                         
          <div class="form-group row">
            <label for="megeftpos_terminal_id" class="col-md-4 col-form-label form-control-sm text-md-right">Terminal ID:</label>
            <div class="col-md-8">
              <input id="megeftpos_terminal_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['megeftpos_terminal_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>
          <div class="form-group row">
            <label for="megeftpos_protocol" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρωτόκολλο');?>:</label>
            <div class="col-md-8">
              <select id="megeftpos_protocol" class="form-control form-control-sm myneedsave">
                <option value="0"  <?php if ($row['megeftpos_protocol']==0)  echo 'selected';?>>NOT_SET</option>
                <option value="1"  <?php if ($row['megeftpos_protocol']==1)  echo 'selected';?>>EDPS_JSON</option>
                <option value="2"  <?php if ($row['megeftpos_protocol']==2)  echo 'selected';?>>CARDLINK_DLL</option>
                <option value="3"  <?php if ($row['megeftpos_protocol']==3)  echo 'selected';?>>MELLON_WEB_ECR</option>
                <option value="4"  <?php if ($row['megeftpos_protocol']==4)  echo 'selected';?>>EPAY_WEB_ECR</option>
                <option value="5"  <?php if ($row['megeftpos_protocol']==5)  echo 'selected';?>>NEXI_WEB_ECR</option>
                <option value="6"  <?php if ($row['megeftpos_protocol']==6)  echo 'selected';?>>VIVA_CLOUD</option>
                <option value="7"  <?php if ($row['megeftpos_protocol']==7)  echo 'selected';?>>ATTICA_WEB_ECR</option>
                <option value="8"  <?php if ($row['megeftpos_protocol']==8)  echo 'selected';?>>WORLDLINE_WEB_ECR</option>
                <option value="9"  <?php if ($row['megeftpos_protocol']==9)  echo 'selected';?>>INSS_RESTAPI</option>
                <option value="10" <?php if ($row['megeftpos_protocol']==10) echo 'selected';?>>EDPS_COMMON_TCP_SOCKET</option>
                <option value="11" <?php if ($row['megeftpos_protocol']==11) echo 'selected';?>>NEXI_COMMON_TCP_SOCKET</option>
              </select>
            </div>
          </div>           
          <div class="form-group row megeftpos_protocol megeftpos_protocol1 megeftpos_protocol2" style="<?php if (in_array($row['megeftpos_protocol'],[1,2])==false) echo 'display:none;';?>">
            <label for="megeftpos_static_ip" class="col-md-4 col-form-label form-control-sm text-md-right">Static IP:</label>
            <div class="col-md-8">
              <input id="megeftpos_static_ip" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['megeftpos_static_ip']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 192.168.1.123">
            </div>
          </div>
          <div class="form-group row megeftpos_protocol megeftpos_protocol1 megeftpos_protocol2" style="<?php if (in_array($row['megeftpos_protocol'],[1,2])==false) echo 'display:none;';?>">
            <label for="megeftpos_port" class="col-md-4 col-form-label form-control-sm text-md-right">Port:</label>
            <div class="col-md-8">
              <input id="megeftpos_port" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['megeftpos_port']!=0) echo intval($row['megeftpos_port']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 4000" min=1 max=65530>
            </div>
          </div>

          

          <div class="form-group row megeftpos_protocol megeftpos_protocol3 megeftpos_protocol4 megeftpos_protocol8" style="<?php if (in_array($row['megeftpos_protocol'],[3,4,8])==false) echo 'display:none;';?>">
            <label for="megeftpos_api_key" class="col-md-4 col-form-label form-control-sm text-md-right">Api Key:</label>
            <div class="col-md-8">
              <input id="megeftpos_api_key" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $row['megeftpos_api_key'];?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="megeftpos_pos_id" class="col-md-4 col-form-label form-control-sm text-md-right">POS ID:</label>
            <div class="col-md-8">
              <input id="megeftpos_pos_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $row['megeftpos_pos_id'];?>" disabled>
            </div>
          </div>          

                    
          <div class="form-group row">
            <label for="megeftpos_erp_app_id" class="col-md-4 col-form-label form-control-sm text-md-right">gks ERP App Desktop:</label>
            <div class="col-md-8">
              <select id="megeftpos_erp_app_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $row['megeftpos_erp_app_id']=intval($row['megeftpos_erp_app_id']);
                $sql="SELECT * from gks_erp_app where erp_app_disabled=0 order by erp_app_sortorder";
                $result_select = $db_link->query($sql);
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_erp_app'].'"';
                  if ($row_select['id_erp_app']==$row['megeftpos_erp_app_id']) {
                    echo ' selected';
                  }
                  echo '>'.$row_select['erp_app_name'].'</option>';
                }?>
              </select>
            </div>
          </div>
          
          <div class="col-md-12">
            <div style="text-align: center;font-weight: bold;margin-top: 12px;"><?php echo gks_lang('Δοκιμή επικοινωνίας');?></div>
          </div>
                    
          
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Ping στο Τερματικό');?>:</label>
            <div class="col-sm-2">
              <i data-api_call="ping_terminal" id="megeftpos_run_command_ping_terminal" class="megeftpos_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="megeftpos_run_command_ping_terminal_result"></span></div>
          </div>

          
          
          
          
        </div>
      </div>
      

      <div class="card gks_card_expand"  id="card_mellon" style="<?php if (!in_array($row_asset_type_id,[23,24,25,27])) echo 'display:none;';?>">
        <div class="card-header" style="text-align:center">
          Mellon Technologies 
        </div>
        <div class="card-body" <?php echo gks_card_body('mellon');?>>       
          <div class="form-group row">
            <label for="mellon_company_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="mellon_company_id" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
                $sql="select id_company,company_title FROM gks_company where mellon_username<>'' ORDER BY company_title";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_company'].'" ';
                  if ($row_select['id_company']==$row['mellon_company_id']) echo ' selected ';
                  echo '>'.$row_select['company_title'].'</option>';
                }?>
              </select>
            </div>
          </div>          
          
          
          <div class="form-group row">
            <label for="mellon_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8">
              <input id="mellon_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['mellon_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>
          <div class="form-group row">
            <label for="mellon_terminal_id" class="col-md-4 col-form-label form-control-sm text-md-right">Terminal ID:</label>
            <div class="col-md-8">
              <input id="mellon_terminal_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['mellon_terminal_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>

          <div class="col-md-12">
            <div style="text-align: center;font-weight: bold;margin-top: 12px;"><?php echo gks_lang('Δοκιμή επικοινωνίας');?></div>
          </div>

          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Τερματικά');?>:</label>
            <div class="col-sm-2">
              <i data-api_call="terminal_list" id="mellon_run_command_terminal_list" class="mellon_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="mellon_run_command_terminal_list_result"></span></div>
          </div>                     
          
          
          
        </div>
      </div>


      <div class="card gks_card_expand"  id="card_cardlink" style="<?php if (!in_array($row_asset_type_id,[23,24,25,27])) echo 'display:none;';?>">
        <div class="card-header" style="text-align:center">
          Cardlink
        </div>
        <div class="card-body" <?php echo gks_card_body('cardlink');?>>   

          <div class="form-group row">
            <label for="cardlink_company_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="cardlink_company_id" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
                $sql="select id_company,company_title FROM gks_company ORDER BY company_title";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_company'].'" ';
                  if ($row_select['id_company']==$row['cardlink_company_id']) echo ' selected ';
                  echo '>'.$row_select['company_title'].'</option>';
                }?>
              </select>
            </div>
          </div>
                        
          <div class="form-group row">
            <label for="cardlink_terminal_id" class="col-md-4 col-form-label form-control-sm text-md-right">Terminal ID:</label>
            <div class="col-md-8">
              <input id="cardlink_terminal_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['cardlink_terminal_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>
          <div class="form-group row">
            <label for="cardlink_static_ip" class="col-md-4 col-form-label form-control-sm text-md-right">Static IP:</label>
            <div class="col-md-8">
              <input id="cardlink_static_ip" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['cardlink_static_ip']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 192.168.1.123">
            </div>
          </div>
          <div class="form-group row">
            <label for="cardlink_port" class="col-md-4 col-form-label form-control-sm text-md-right">Port:</label>
            <div class="col-md-8">
              <input id="cardlink_port" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['cardlink_port']!=0) echo intval($row['cardlink_port']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 4000" min=1 max=65530>
            </div>
          </div>

          <div class="form-group row">
            <label for="cardlink_ecr2eftweb_erp_app_id" class="col-md-4 col-form-label form-control-sm text-md-right">gks ERP App Desktop:</label>
            <div class="col-md-8">
              <select id="cardlink_ecr2eftweb_erp_app_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $row['cardlink_ecr2eftweb_erp_app_id']=intval($row['cardlink_ecr2eftweb_erp_app_id']);
                $sql="SELECT * from gks_erp_app where erp_app_disabled=0 order by erp_app_sortorder";
                $result_select = $db_link->query($sql);
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_erp_app'].'"';
                  if ($row_select['id_erp_app']==$row['cardlink_ecr2eftweb_erp_app_id']) {
                    echo ' selected';
                  }
                  echo '>'.$row_select['erp_app_name'].'</option>';
                }?>
              </select>
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="cardlink_ecr2eftweb_service_url" class="col-md-4 col-form-label form-control-sm text-md-right">Ecr2EftWEB Service Url:</label>
            <div class="col-md-8">
              <input id="cardlink_ecr2eftweb_service_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['cardlink_ecr2eftweb_service_url']);?>" placeholder="<?php echo gks_lang('π.χ.');?> http://localhost:9090">
            </div>
          </div>
          
          <div class="col-md-12">
            <div style="text-align: center;font-weight: bold;margin-top: 12px;"><?php echo gks_lang('Δοκιμή επικοινωνίας');?></div>
          </div>
                    
          
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Ping στο Service');?>:</label>
            <div class="col-sm-2">
              <i data-api_call="ping_service" id="cardlink_run_command_ping_service" class="cardlink_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="cardlink_run_command_ping_service_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Ping στο Τερματικό');?>:</label>
            <div class="col-sm-2">
              <i data-api_call="ping_terminal" id="cardlink_run_command_ping_terminal" class="cardlink_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="cardlink_run_command_ping_terminal_result"></span></div>
          </div>          
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left">Merchant Info:</label>
            <div class="col-sm-2">
              <i data-api_call="merchantinfo" id="cardlink_run_command_merchantinfo" class="cardlink_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="cardlink_run_command_merchantinfo_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left">Reconciliation:</label>
            <div class="col-sm-2">
              <i data-api_call="reconciliation" id="cardlink_run_command_reconciliation" class="cardlink_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="cardlink_run_command_reconciliation_result"></span></div>
          </div>
          
          
          
          
        </div>
      </div>

      <div class="card gks_card_expand"  id="card_epay" style="<?php if (!in_array($row_asset_type_id,[23,24,25,27])) echo 'display:none;';?>">
        <div class="card-header" style="text-align:center">
          ePay
        </div>
        <div class="card-body" <?php echo gks_card_body('epay');?>>       
          <div class="form-group row">
            <label for="epay_company_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="epay_company_id" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
                $sql="select id_company,company_title FROM gks_company where epay_username<>'' ORDER BY company_title";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_company'].'" ';
                  if ($row_select['id_company']==$row['epay_company_id']) echo ' selected ';
                  echo '>'.$row_select['company_title'].'</option>';
                }?>
              </select>
            </div>
          </div>          
          
          
          <div class="form-group row">
            <label for="epay_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8">
              <input id="epay_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['epay_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>
          <div class="form-group row">
            <label for="epay_terminal_id" class="col-md-4 col-form-label form-control-sm text-md-right">Terminal ID:</label>
            <div class="col-md-8">
              <input id="epay_terminal_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['epay_terminal_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>

          <div class="col-md-12">
            <div style="text-align: center;font-weight: bold;margin-top: 12px;"><?php echo gks_lang('Δοκιμή επικοινωνίας');?></div>
          </div>

          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Τερματικά');?>:</label>
            <div class="col-sm-2">
              <i data-api_call="terminal_list" id="epay_run_command_terminal_list" class="epay_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="epay_run_command_terminal_list_result"></span></div>
          </div>                     
          
          
          
        </div>
      </div>

      <div class="card gks_card_expand"  id="card_worldline" style="<?php if (!in_array($row_asset_type_id,[23,24,25,27])) echo 'display:none;';?>">
        <div class="card-header" style="text-align:center">
          Worldline
        </div>
        <div class="card-body" <?php echo gks_card_body('worldline');?>>       
          <div class="form-group row">
            <label for="worldline_company_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="worldline_company_id" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
                $sql="select id_company,company_title FROM gks_company where worldline_username<>'' ORDER BY company_title";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_company'].'" ';
                  if ($row_select['id_company']==$row['worldline_company_id']) echo ' selected ';
                  echo '>'.$row_select['company_title'].'</option>';
                }?>
              </select>
            </div>
          </div>          
          
          
          <div class="form-group row">
            <label for="worldline_id" class="col-md-4 col-form-label form-control-sm text-md-right">Mobile device:</label>
            <div class="col-md-8">
              <input id="worldline_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['worldline_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 601ba51f3c68ebe6_23">
            </div>
          </div>
          <div class="form-group row">
            <label for="worldline_terminal_id" class="col-md-4 col-form-label form-control-sm text-md-right">Terminal ID:</label>
            <div class="col-md-8">
              <input id="worldline_terminal_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['worldline_terminal_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>

          <div class="col-md-12">
            <div style="text-align: center;font-weight: bold;margin-top: 12px;"><?php echo gks_lang('Δοκιμή επικοινωνίας');?></div>
          </div>

          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Σύνδεση με Server');?>:</label>
            <div class="col-sm-2">
              <i data-api_call="terminal_link" id="worldline_run_command_terminal_link" class="worldline_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="worldline_run_command_terminal_link_result"></span></div>
          </div>                     
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-sm-2">
              <i data-api_call="terminal_status" id="worldline_run_command_terminal_status" class="worldline_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="worldline_run_command_terminal_status_result"></span></div>
          </div>          
          
          
        </div>
      </div>

      <div class="card gks_card_expand"  id="card_nexi" style="<?php if (!in_array($row_asset_type_id,[23,24,25,27])) echo 'display:none;';?>">
        <div class="card-header" style="text-align:center">
          NEXI
        </div>
        <div class="card-body" <?php echo gks_card_body('nexi');?>>       
          <div class="form-group row">
            <label for="nexi_company_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="nexi_company_id" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
                $sql="select id_company,company_title FROM gks_company where nexi_username<>'' ORDER BY company_title";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_company'].'" ';
                  if ($row_select['id_company']==$row['nexi_company_id']) echo ' selected ';
                  echo '>'.$row_select['company_title'].'</option>';
                }?>
              </select>
            </div>
          </div>          
          
          
          <div class="form-group row">
            <label for="nexi_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8">
              <input id="nexi_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['nexi_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>
          <div class="form-group row">
            <label for="nexi_terminal_id" class="col-md-4 col-form-label form-control-sm text-md-right">Terminal ID:</label>
            <div class="col-md-8">
              <input id="nexi_terminal_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['nexi_terminal_id']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 16150489">
            </div>
          </div>

          <div class="col-md-12">
            <div style="text-align: center;font-weight: bold;margin-top: 12px;"><?php echo gks_lang('Δοκιμή επικοινωνίας');?></div>
          </div>

          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Τερματικά');?>:</label>
            <div class="col-sm-2">
              <i data-api_call="terminal_list" id="nexi_run_command_terminal_list" class="nexi_run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="nexi_run_command_terminal_list_result"></span></div>
          </div>                     
          
          
          
        </div>
      </div>

      
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>
      
            
    </div>
  </div>
</div>

<?php if ($isservice==false) {?> 
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <?php if ($id>0) {?>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_asset'];?>" data-model="gks_assets" data-backurl="admin-assets.php"><?php echo gks_lang('Διαγραφή');?></button>
      <?php } ?>
      <?php if (1==2 and $id>0) {?>
      <button type="button" class="btn btn-primary" id="submit_button_copy" onclick="window.location.href='admin-assets-item.php?id=-1&copy=<?php echo $id;?>'"><?php echo gks_lang('Δημιουργία αντιγράφου');?></button>
      <?php } ?>
    </div> 
  </div> 
</div> 
<?php } ?>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">

      <?php if (GKS_TRANSFER) {?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          Transfer
        </div>
        <div class="card-body" <?php echo gks_card_body('transfer');?>>       
          
          <?php
          $query = "SELECT gks_transfer_oxima2type2transfer.*, 
          gks_transfer_oxima_type.transfer_oxima_type_descr, 
          gks_transfer.transfer_title
          FROM (gks_transfer_oxima2type2transfer 
          LEFT JOIN gks_transfer_oxima_type ON gks_transfer_oxima2type2transfer.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type) 
          LEFT JOIN gks_transfer ON gks_transfer_oxima2type2transfer.transfer_id = gks_transfer.id_transfer
          where gks_transfer_oxima2type2transfer.asset_id=".$id."
          ORDER BY gks_transfer_oxima2type2transfer.id_transfer_oxima2type2transfer";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="oxima2type2transfer_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"><?php echo gks_lang('Κανάλι');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"><?php echo gks_lang('Τύπος οχήματος');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo gks_lang('Ημερομηνία');?></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="oxima2type2transfer_tr_exist" data-id="<?php echo $row_list['id_transfer_oxima2type2transfer'];?>">
              <th scope="row" nowrap align="right" class="oxima2type2transfer_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_oxima2type2transfer_delete_after|<?php echo $row_list['id_transfer_oxima2type2transfer'];?>" data-id="<?php echo $row_list['id_transfer_oxima2type2transfer'];?>" data-model="gks_transfer_oxima2type2transfer">
              </td>
              <td nowrap><?php echo $row_list['transfer_title'];?></td>  
              <td nowrap><?php echo $row_list['transfer_oxima_type_descr'];?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr>
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap>
                <input data-id="0" type="text" name="oxima2type2transfer_transfer_title" id="oxima2type2transfer_transfer_title" class="form-control form-control-sm" style="display: inline-block;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              </td>
              <td nowrap>
                <input data-id="0" type="text" name="oxima2type2transfer_oxima_type"     id="oxima2type2transfer_oxima_type"     class="form-control form-control-sm" style="display: inline-block;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              </td>
              <td nowrap>
                <button style="justify-content: center!important;vertical-align: baseline;" type="button" class="btn btn-sm btn-primary" id="add_oxima2type2transfer"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>
      
          </tbody>
          </table> 

            
        </div>
      </div>
      <?php } ?>
      
      
      <?php 
      echo getObjectRels('gks_assets',$id);
      echo getActivityObjectTable('gks_assets',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_assets','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
      
    </div>
    <div class="col-xl-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Που είναι ;');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('thesi');?>>       

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποθήκη');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm">
                <a href="admin-warehouses-item.php?id=<?php echo $row['asset_last_warehouse_id']?>"><?php echo $row['warehouse_name'];?></a>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Συνεργάτης');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm">
                <a href="admin-users-item.php?id=<?php echo $row['asset_last_user_id']?>"><?php echo $row['gks_nickname'];?></a>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm">
                <a href="admin-company-item.php?id=<?php echo $row['asset_last_company_id']?>"><?php echo $row['company_title'];?></a>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αναθέσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('anathe');?>>        
          <?php if ($isservice==false) {?>
          <div class="form-group row">
            <label for="anath_warehouse_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σε αποθήκη');?>:</label>
            <div class="col-md-4">
              <input id="anath_warehouse_id" data-id="0" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            </div>
            <div class="col-md-4">
              <span class="btn btn-sm btn-primary" id="add_warehouse"><?php echo gks_lang('Ανάθεση');?></span>
            </div>
          </div>
          <div class="form-group row">
            <label for="anath_sinergati_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σε συνεργάτη');?>:</label>
            <div class="col-md-4">
              <input id="anath_sinergati_id" data-id="0" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            </div>
            <div class="col-md-4">
              <span class="btn btn-sm btn-primary" id="add_sinergati"><?php echo gks_lang('Ανάθεση');?></span>
            </div>
          </div>
          <div class="form-group row">
            <label for="anath_company_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σε εταιρεία');?>:</label>
            <div class="col-md-4">
              <input id="anath_company_id" data-id="0" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            </div>
            <div class="col-md-4">
              <span class="btn btn-sm btn-primary" id="add_company"><?php echo gks_lang('Ανάθεση');?></span>
            </div>
          </div>

          <?php if ($row_asset_last_user_id > 0) {?>
          <div class="form-group row">
            <div class="col-md-12 text-center">
              <span class="btn btn-sm btn-primary" id="return_to_warehouse"><?php echo gks_lang('Επιστροφή στην αποθήκη');?></span>
            </div>
          </div>
          <?php } ?> 
          <?php if ($row_asset_last_mixani_id==0 and $row_asset_last_user_id==0) { ?>
          <?php if ($id>0) { ?>
          <div class="form-group row">
            <div class="col-md-12 text-center">
              <a class="btn btn-sm btn-primary" href="admin-assets-service-item.php?id=-1&asset_id=<?php echo $id;?>"><?php echo gks_lang('Αποστολή για Service');?></a>
            </div>
          </div>
                    
          <?php }} else { ?>
          <div class="form-group row">
            <div class="col-md-12 text-center">
              <p style="font-weight: bold;color:red;font-size:10pt">
                <?php echo gks_lang('Για να αποσταλεί το πάγιο για Service:<br>δεν θα πρέπει να ανήκει σε συνεργάτη,<br>ούτε να ανήκει σετ και να είναι ενεργό.');?>
              </p>              
            </div>
          </div>          
          <?php } ?> 
          <? } else { ?>
          <div class="form-group row">
            <div class="col-md-12 text-center">
              <p style="font-weight: bold;color:red;font-size:14pt"><?php echo gks_lang('Το πάγιο είναι σε Service');?></p>
            </div>
          </div>          
          
           
          <?php } ?>  

        
          
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 50 τελευταίες κινήσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last50m');?>>
        <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th class="table-dark" scope="col" style="text-align:center !important;" nowrap  width="0%"><?php echo gks_lang('A/A');?></th>  
              <th class="table-dark" scope="col" style="text-align:left   !important;" nowrap  width="0%"><?php echo gks_lang('Ημερομηνία');?></th>  
              <th class="table-dark" scope="col" style="text-align:left   !important;" nowrap  width="30%"><?php echo gks_lang('Αποθήκη');?></th>  
              <th class="table-dark" scope="col" style="text-align:left   !important;" nowrap  width="0%"><?php echo gks_lang('Τύπος');?></th> 
              <th class="table-dark" scope="col" style="text-align:left   !important;" nowrap  width="30%"><?php echo gks_lang('Συνεργάτης');?></th>
              <th class="table-dark" scope="col" style="text-align:left   !important;" nowrap  width="20%"><?php echo gks_lang('Εταιρεία');?></th>
              <th class="table-dark" scope="col" style="text-align:left   !important;" nowrap  width="20%"><?php echo gks_lang('Χρήστης');?></th>
            </tr>
          </thead>  
          <tbody> 
            
          <?php
          $sql_list = "SELECT gks_assets_moves.*, gks_warehouses.warehouse_name, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
          user_add.gks_nickname AS gks_nickname_user_add, gks_company.company_title
          FROM (((gks_assets_moves 
          LEFT JOIN gks_warehouses ON gks_assets_moves.warehouse_id = gks_warehouses.id_warehouse) 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_moves.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS user_add ON gks_assets_moves.user_id_add = user_add.ID) 
          LEFT JOIN gks_company ON gks_assets_moves.company_id = gks_company.id_company
          WHERE (((gks_assets_moves.asset_id)=".$id."))
          ORDER BY gks_assets_moves.mydate DESC limit 50";
          
          $result_list = $db_link->query($sql_list);        
          if (!$result_list) debug_mail(false,'error sql',$sql_list);
          if (!$result_list) die('sql error');
          
          $j = 0;
          while ($row_list = $result_list->fetch_assoc()) {
            $j++; ?>

          <tr id="tr_<?php echo $row_list['id_assets_moves'];?>">
            <th class="mytdcm" scope="row"><?php echo $j;?></th>     
            <td class="mytdcml" nowrap><?php echo showDate(strtotime($row_list['mydate']), 'd/m/Y H:i:s', 1);?></td>   
            <td class="mytdcml"><?php echo '<a href="admin-warehouses-item.php?id='.$row_list['warehouse_id'].'">'.$row_list['warehouse_name'].'</a>';?></td>  
            <td class="mytdcm"><?php
            if ($row_list['user_id']>0 or $row_list['company_id']>0) {?>
              <i class="fas fa-sign-out-alt assetmovearrowright"></i>
            <?php } else { ?>
              <i class="fas fa-sign-in-alt fa-rotate-180 assetmovearrowleft"></i>
            <?php } ?></td>
            <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.$row_list['gks_nickname'].'</a>';?></td>  
            <td class="mytdcml"><?php echo '<a href="admin-company-item.php?id='.$row_list['company_id'].'">'.$row_list['company_title'].'</a>';?></td>  
            <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id_add'].'">'.$row_list['gks_nickname_user_add'].'</a>';?></td>  
            
            
          </tr>
                

          <?php } ?>                      
          </tbody>   
          </table>
        </div>                                   
      </div>
      

      <div class="card gks_card_expand" id="gks_assets_oximata_km">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 50 τελευταίες Καταγραφές Km');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('lastkm');?>>
        <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th class="table-dark" scope="col" style="text-align:center !important;" nowrap  width='0%'><?php echo gks_lang('A/A');?></th>  
              <th class="table-dark" scope="col" style="text-align:left   !important;" nowrap  width='20%'><?php echo gks_lang('Ημερομηνία');?></th>  
              <th class="table-dark" scope="col" style="text-align:center !important;" nowrap  width='20%'><?php echo gks_lang('Km');?></th>  
              <th class="table-dark" scope="col" style="text-align:left   !important;" nowrap  width='60%'><?php echo gks_lang('Χρήστης');?></th> 
            </tr>
          </thead>  
          <tbody> 
            
          <?php
          $sql_list = "SELECT gks_assets_oximata_km.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_assets_oximata_km LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_oximata_km.user_id_add = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE (((gks_assets_oximata_km.asset_id)=".$id."))
          ORDER BY gks_assets_oximata_km.id_assets_oximata_km DESC limit 50";
          
          $result_list = $db_link->query($sql_list);        
          if (!$result_list) debug_mail(false,'error sql',$sql_list);
          if (!$result_list) die('sql error');
          
          $j = 0;
          while ($row_list = $result_list->fetch_assoc()) {
            $j++; ?>

          <tr id="tr_<?php echo $row_list['id_assets_oximata_km'];?>">
            <th class="mytdcm" scope="row"><?php echo $j;?></th>     
            <td class="mytdcm" nowrap ><?php echo showDate(strtotime($row_list['mydateadd']), 'd/m/Y H:i:s', 1);?></td>   
            <td class="mytdcm" nowrap><?php echo number_format($row_list['km'], 0, '', '.');?></td>   
            <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id_add'].'">'.$row_list['gks_nickname'].'</a>';?></td>  
          </tr>
                

          <?php } ?>                      
          </tbody>   
          </table>
        </div>                                   
      </div>      



      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 50 τελευταίες απογραφές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('apograf');?>>
        <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th class="table-dark" scope="col" style="text-align:center !important;" width='0%'  nowrap>A/A</th>
              <th class="table-dark" scope="col" style="text-align:left   !important;" width="0%"  nowrap></th></th> 
              <th class="table-dark" scope="col" style="text-align:left   !important;" width="0%"  nowrap><?php echo gks_lang('Ημερομηνία');?></th></th> 
              <th class="table-dark" scope="col" style="text-align:center !important;" width="0%"  nowrap><?php echo gks_lang('Κατάσταση');?></th>        
              <th class="table-dark" scope="col" style="text-align:left   !important;" width="30%" nowrap><?php echo gks_lang('Αποθήκη');?></th>        
              <th class="table-dark" scope="col" style="text-align:center !important;" width="0%"  nowrap><span title="<?php echo gks_lang('Υπάρχει Θεωρητικά');?>" class="tooltipster"><?php echo gks_lang('Υ.Θ.');?></span></th>     
              <th class="table-dark" scope="col" style="text-align:center !important;" width="0%"  nowrap><span title="<?php echo gks_lang('Υπάρχει Πραγματικά');?>" class="tooltipster"><?php echo gks_lang('Υ.Π.');?></span></th>     
              <th class="table-dark" scope="col" style="text-align:left   !important;" width="70%" nowrap><?php echo gks_lang('Σχόλιο');?></th>     
            </tr>
          </thead>  
          <tbody> 
            
          <?php
          $sql_list = "SELECT gks_assets_whi_mov_assets.assets_whi_mov_id, gks_assets_whi_mov.mydate, 
          gks_assets_whi_mov.assets_whi_mov_status, gks_assets_whi_mov.warehouse_id, gks_warehouses.warehouse_name, 
          gks_assets_whi_mov_assets.posotita_theori, gks_assets_whi_mov_assets.posotita_found, gks_assets_whi_mov_assets.posotita_sxolio
          FROM (gks_assets_whi_mov_assets 
          LEFT JOIN gks_assets_whi_mov ON gks_assets_whi_mov_assets.assets_whi_mov_id = gks_assets_whi_mov.id_assets_whi_mov) 
          LEFT JOIN gks_warehouses ON gks_assets_whi_mov.warehouse_id = gks_warehouses.id_warehouse
          WHERE (((gks_assets_whi_mov_assets.asset_id)=".$id."))
          ORDER BY gks_assets_whi_mov.mydate DESC
          limit 50";
          
          $result_list = $db_link->query($sql_list);        
          if (!$result_list) debug_mail(false,'error sql',$sql_list);
          if (!$result_list) die('sql error');
          
          $j = 0;
          while ($row_log = $result_list->fetch_assoc()) {
            $j++; ?>

          <tr>
            <th class="mytdcm" scope="row"><?php echo $j;?></th>  
            <td class="mytdcm">
              <a href="admin-assets-whi-mov-item.php?id=<?php echo $row_log['assets_whi_mov_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
            </td>
            <td class="mytdcml" nowrap><?php if (isset($row_log['mydate'])) echo showDate(strtotime($row_log['mydate']), 'd/m/Y H:i', 1);?></td>   
            <td class="mytdcm" nowrap><span class="assets_apografi_state_<?php echo $row_log['assets_whi_mov_status'];?>"><?php echo get_assets_whi_mov_descr($row_log['assets_whi_mov_status']);?></span></td>
            <td class="mytdcml"><?php echo $row_log['warehouse_name'];?></td>   
            
           <td class="mytdcm" nowrap><?php 
              //var_dump($val['posotita_theori']);
              if ($row_log['posotita_theori']!==null) {
                if ($row_log['posotita_theori']==0)
                  echo '<img src="img/0.png" border="0" width="32" class="item_posotita_theori" data-val="0">';
                else if ($row_log['posotita_theori']==1)
                  echo '<img src="img/1.png" border="0" width="32" class="item_posotita_theori" data-val="1">';
              } else {
                echo '<img src="img/1bg.png" border="0" width="32" class="item_posotita_theori" data-val="">';
              }
            ?></td>     
            <td class="mytdcm" nowrap><?php 
              if ($row_log['posotita_found']!==null) {
                if ($row_log['posotita_found']==0)
                  echo '<img src="img/0.png" border="0" width="32" class="item_posotita_found" data-val="0">';
                else if ($row_log['posotita_found']==1)
                  echo '<img src="img/1.png" border="0" width="32" class="item_posotita_found" data-val="1">';
              } else {
                echo '<img src="img/1bg.png" border="0" width="32" class="item_posotita_found" data-val="">';
              }
            ?></td>     
            <td class="mytdcml" nowrap>
              <?php echo nl2br_gks($row_log['posotita_sxolio']);?>
            </td>  
            
            
          </tr>
                

          <?php } ?>                      
          </tbody>   
          </table>
        </div>                                   
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιστορικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('hist');?>>
        <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th class="table-dark" scope="col" width="0%" nowrap>#</th>
              <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
              <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>
              <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Τι');?></th>
            </tr>
          </thead>  
          <tbody> 
            
          <?php
          $sql_log="SELECT gks_assets_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_assets_log LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_assets_log.asset_id=".$id."
          ORDER BY gks_assets_log.id_assets_log DESC;";
          $result_log = $db_link->query($sql_log);        
          if (!$result_log) debug_mail(false,'error sql',$sql_log);
          if (!$result_log) die('sql error');
          
          $j = 0;
          while ($row_log = $result_log->fetch_assoc()) {
            $j++; ?>
        
          <tr>
            <th class="mytdcml" scope="row"><?php echo $j;?></th>
            <td class="mytdcml"><?php echo showDate(strtotime($row_log['add_date']), 'd/m/Y H:i:s', 1);?></td>  
            <td class="mytdcml"><?php echo $row_log['gks_nickname'];?></td>  
            <td class="mytdcml"><?php echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_log['sxolio']);?></td>    
          </tr>
          <?php } ?>                      
          </tbody>   
          </table>
        </div>                                   
      </div>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body"  <?php echo gks_card_body('kat');?>>  


          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['id_asset'])) echo $row['id_asset'];?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['gks_nickname_add'])) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['gks_nickname_edit'])) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['myip'])) echo '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>';?></span></div>
          </div>


        </div>      
      </div>        
            
    </div>
  </div>
</div>


    
<?php 
$perm_ret_allservice=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_service','view',0);
if ($perm_ret_allservice['success']) {

?>
<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Όλα τα service του παγίου');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('allserv');?>> 
<?php

$gks_custom_prepare_allservice = gks_custom_table_item_prepare('gks_assets_service',['from'=>'list']);

$sql_allservice = "SELECT SQL_CALC_FOUND_ROWS gks_assets_service.*,gks_assets.id_asset, gks_assets.asset_code, gks_assets.asset_photo ,
gks_assets.asset_title, gks_assets.asset_serialnumber, 
gks_assets_service_reasons.reasons_descr, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_warehouses.warehouse_name, wp_users_edit.gks_nickname AS useredit
".$gks_custom_prepare_allservice['sql_all_list_sele']."  
FROM ".$gks_custom_prepare_allservice['sql_all_list_from']." ((((gks_assets_service 
".$gks_custom_prepare_allservice['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_assets_service.mixanikos_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_assets_service_reasons ON gks_assets_service.reason_id = gks_assets_service_reasons.id_assets_service_reasons) 
LEFT JOIN gks_assets ON gks_assets_service.asset_id = gks_assets.id_asset) 
LEFT JOIN gks_warehouses ON gks_assets_service.warehouse_id = gks_warehouses.id_warehouse) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS wp_users_edit ON gks_assets_service.user_id_edit = wp_users_edit.ID
where asset_id=".$id."
ORDER BY gks_assets_service.id_assets_service desc";

$result_allservice = $db_link->query($sql_allservice);        
if (!$result_allservice) debug_mail(false,'error sql',$sql_allservice);
if (!$result_allservice) die('sql error');
      
?>        
          <table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('ID');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Ημερομηνία<br>Αποστολής');?></th>   
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Αποθήκη');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo gks_lang('Αιτία');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo gks_lang('Σχόλιο<br>Αποστολής');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Τεχνικός');?></th>  
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Ημερομηνία<br>Επιστροφής');?></th>   
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%" nowrap><?php echo gks_lang('Σχόλιο<br>Επιστροφής');?></th>  
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('Αξία');?></th>  
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><span title="<?php echo gks_lang('Επιβεβαιωμένο');?>"><?php echo gks_lang('Επιβ.');?></span></th>  
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('Χρήστης');?></th>  
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo gks_lang('IP');?></th>
          
              <?php 
              echo gks_custom_table_list_header($gks_custom_prepare_allservice, true);
              ?>
            
            </tr>        
          </thead>
          <tbody>
    <?php
    $row_rec=$row;
    $i = 0;
    while ($row = $result_allservice->fetch_assoc()) {

	$i++;
?>
  <tr>
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i );?></th>
    <td nowrap class="mytdcm">
      <table cellpadding=0 cellspacing=0 class="gks_tb1">
        <tr class="gks_tr1">
          <td class="gks_ttd1" colspan="2"><?php echo $row['id_assets_service'];?></td>
        </tr>  
        <tr class="gks_tr1">
          <td class="gks_ttd2"><a href="admin-assets-service-item.php?id=<?php echo $row['id_assets_service'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <?php if ($perm_gks_assets_service_delete) {?>
          <td class="gks_ttd3" ><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row['id_assets_service'];?>" data-model="gks_assets_service"></i></td>
          <?php } ?>
        </tr>  
     </table>
    </td>
    <td class="mytdcm" nowrap><?php echo showDate(strtotime($row['mydate_send']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    
    <td class="mytdcml"><?php echo '<a href="admin-warehouses-item.php?id='.$row['warehouse_id'].'">'.$row['warehouse_name'].'</a>';?></td>  
    <td class="mytdcml"><?php echo $row['reasons_descr']; ?></td>
    <td class="mytdcml"><?php echo nl2br_gks(htmlspecialchars_gks($row['aitiolog'])); ?></td>
    <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row['mixanikos_id'].'">'.$row['gks_nickname'].'</a>';?></td>  
    <td class="mytdcm" nowrap><?php if (isset($row['mydate_return'])) echo showDate(strtotime($row['mydate_return']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td class="mytdcml"><?php echo nl2br_gks(htmlspecialchars_gks($row['aitiolog2'])); ?></td>
    <td class="mytdcmr" nowrap><?php if ($row['ajia']>0) echo number_format($row['ajia'],2,',','.'); ?></td>
    <td class="mytdcm" nowrap><img src="img/<?php echo $row['isconfirm']!=0 ? "1" :"0";  ?>.png" border="0" width="16"></td>
    <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['useredit'].'</a>';?></td>  
    
    

    <td class="mytdcml"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>">V</a> 




<?php
    echo gks_custom_table_list_rows($gks_custom_prepare_allservice,$row);
?>  
  </tr>
<?php    
    }
    $row=$row_rec;
?>          
          
          </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php } ?>






 
<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_dialog_object_rel_curr='gks_assets';
var from_php_activity_model='gks_assets';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

  
var from_php_id=<?php echo $id;?>;




var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;

var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets','delete',$id);?>;




var from_php_row_asset_last_warehouse_id='<?php echo $row_asset_last_warehouse_id;?>';
var from_php_asset_type_mixani0='<?php echo $from_php_asset_type_mixani0;?>';

var from_php_GKS_TRANSFER=<?php echo (GKS_TRANSFER ? 'true' : 'false');?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});

</script>  

<script src='/my/js/tinymce/tinymce.min.js'></script>


<script src="js/admin-assets-item.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


  
  