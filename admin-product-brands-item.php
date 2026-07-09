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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products_brands',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_products_brands',['from'=>'item']);



if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_product_brand']=-1;
  $row['product_brand_parent_id']=0;
  $row['product_brand_descr']='';
  $row['brand_disable']=0;
  $row['brand_comments']='';
  $row['brand_photo']='';

//$id=0;
//if (isset($_GET['id'])) $id=intval($_GET['id']);
//if ($id==-1) {
//  $sql="insert into gks_eshop_products_brands (product_brand_descr,product_brand_parent_id,
//  user_id_add,user_id_edit,mydate_add,mydate_edit,myip
//  ) values ('draft ".time()." ',0,
//  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
//
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    die('sql error');
//  }  
//  $id = $db_link->insert_id;
//  header('Location: ?id='.$id);
//  die();
//}
//if ($id <= 0) {header('Location: /my'); die(); }

  $my_page_title=gks_lang('Νέα Μάρκα');
} else {


  
  $sql ="SELECT gks_eshop_products_brands.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_eshop_products_brands 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_eshop_products_brands.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_eshop_products_brands.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_product_brand = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();

  $my_page_title=gks_lang('Μάρκα').': '.$row['product_brand_descr'];
  $object_title=$row['product_brand_descr'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

stat_record();
$nav_active_array=array('manage','manage_menu_product','manage_product_brands');

$lang_data_obj=gks_lang_data_obj_prepare('gks_eshop_products_brands','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>
<style>
.eshop_sync {
    font-size: 200%;
    vertical-align: middle;
    color: blue;
    cursor: pointer;
}  
  
</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Μάρκα');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Μάρκα');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="product_brand_descr" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μάρκα');?>:</label>
            <div class="col-sm-8">
              <input id="product_brand_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_brand_descr']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('product_brand_descr'));
          ?>          
          <div class="form-group row">
            <label for="product_brand_parent_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γονική Μάρκα');?>:</label>
            <div class="col-sm-8">
              <select name="product_brand_parent_id" id="product_brand_parent_id"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="
              select gks_eshop_products_brands.id_product_brand as id,
              CONCAT_WS('\\\\',
                              ug10.product_brand_descr,
                              ug9.product_brand_descr,
                              ug8.product_brand_descr,
                              ug7.product_brand_descr,
                              ug6.product_brand_descr,
                              ug5.product_brand_descr,
                              ug4.product_brand_descr,
                              ug3.product_brand_descr,
                              ug2.product_brand_descr,
                              gks_eshop_products_brands.product_brand_descr) as descr
              FROM ((((((((gks_eshop_products_brands
              LEFT JOIN gks_eshop_products_brands AS ug2  ON gks_eshop_products_brands.product_brand_parent_id = ug2.id_product_brand)
              LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.product_brand_parent_id = ug3.id_product_brand)
              LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.product_brand_parent_id = ug4.id_product_brand)
              LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.product_brand_parent_id = ug5.id_product_brand)
              LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.product_brand_parent_id = ug6.id_product_brand)
              LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.product_brand_parent_id = ug7.id_product_brand)
              LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.product_brand_parent_id = ug8.id_product_brand)
              LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.product_brand_parent_id = ug9.id_product_brand)
              LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.product_brand_parent_id = ug10.id_product_brand

              where gks_eshop_products_brands.id_product_brand<>".$id."
              ORDER BY descr";
              
              
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id'].'" ';
                if ($row_select['id']==$row['product_brand_parent_id']) echo ' selected ';
                echo '>'.$row_select['descr'].'</option>';
              }?></select>
              <small class="form-text text-muted"><?php echo gks_lang('Έως 10 επίπεδα');?></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="group_comments" class="col-sm-12 col-form-label form-control-sm text-sm-right1"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-12">
              <textarea id="brand_comments" type="text" class="gks_tinymce form-control form-control-sm myneedsave" style="height:200px;"><?php echo htmlspecialchars_gks($row['brand_comments']); ?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('brand_comments'));
          ?> 
                           
          <div class="form-group row">
            <label for="brand_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" name="brand_disable"  id="brand_disable" value="1" <?php if ($row['brand_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>                    
        </div>
      </div>
              
              
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>
      
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Φωτογραφίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('photo');?>>       
          <div class="row">
            <div class="col-md-12" style="text-align:center;"><?php echo gks_lang('Η προεπιλεγμένη φωτογραφία της μάρκας');?></div>
            
            <div class="col-md-12" style="text-align:center;">
              <?php
              $product_brand_photo_value="";
              $myimgurl = $row['brand_photo']; //get_user_meta($my_wp_user_id, 'wsl_current_user_image', true);
              //echo $myimgurl;
              if ($myimgurl.'' == '') {
                $myimgurl="/my/img/product.png";
              } else {
                $product_brand_photo_value = $myimgurl;
              }
              ?>
              <img src="<?php echo $myimgurl;?>" border="0" style="max-width:96px;max-height:96px;" id="form_product_brand_photo_img"/><br>
              
              <a href="" id="reset_brand_photo" title="<?php echo gks_lang('Διαγραφή');?>" <?php 
                if ($product_brand_photo_value == '') {
                  echo ' style="display:none" ';
                }
                ?> ><img src="/my/img/0.png" border="0" width="16" ></a>
              <br><input type="hidden" id="form_product_brand_photo" name="form_product_brand_photo" value="<?php echo $product_brand_photo_value;?>" />
            </div>                     
          </div>
          <div class="row">
            <div class="col-md-12" style="text-align:center; padding-top: 24px;"><?php echo gks_lang('Φωτογραφίες της μάρκας');?></div>
            
            <form role="form" method="post" action="admin-product-brands-item-photo-upload.php" id="myphoto_brand_upload" enctype="multipart/form-data" style="width: 100%;">
              <input type="hidden" name="product_brand_id" id="product_brand_id" value="<?php echo $id;?>">
              <div id="lightgallery_brand">
                <div class="form-group" id="imagelist_photo">
                <?php   
                  $sql="select * from gks_eshop_products_brands_photo where product_brand_id =".$id." and filesobjectlist=0 order by id_eshop_products_brands_photo";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    $photo_url = $row_select['photo_url'];
                    $photo_url_thumb = dirname($row_select['photo_url']).'/thumbnail/'.mb_basename($row_select['photo_url']);


                    ?>
                    <div id="item_upload_photo_<?php echo $row_select['id_eshop_products_brands_photo'];?>" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">
                      <a class="lightgallery_item_brand" href="<?php echo $photo_url;?>" data-download-url="<?php echo $photo_url;?>">
                        <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="<?php echo $photo_url_thumb;?>">
                      </a>
                      <br>
                      <div style="padding-top:4px">
                        <a href="" class="set_brand_photo"   data-url="<?php echo $photo_url_thumb;?>" title="<?php echo gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία');?>"><img src="/my/img/icons/photo.png" border="0" width="16"></a>
                        <a href="" class="delete_brand_upload_photo" data-url="<?php echo $photo_url_thumb;?>" data-id="<?php echo $row_select['id_eshop_products_brands_photo'];?>" title="<?php echo gks_lang('Διαγραφή');?>"><img src="/my/img/0.png" border="0" width="16"></a>
                      </div>
                    </div>
                  <?php }?>
                </div>
              </div>
              <?php gks_f_button_add_files_photo_html('gks_eshop_products_brands',$id);?>
            </form>                      
            
            
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
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_product_brand'];?>" data-model="gks_eshop_products_brands" data-backurl="admin-product-brands.php"><?php echo gks_lang('Διαγραφή');?></button>

    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Σύνδεση με μάρκα eshop');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="eshoplink_add" style="margin-left: 10px;"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('eshops');?>>
          
          <?php
         
          $query = "SELECT gks_woo_brands.*, gks_eshops.eshop_name, gks_eshops.eshop_url, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM (gks_woo_brands 
          LEFT JOIN gks_eshops ON gks_woo_brands.eshop_id = gks_eshops.id_eshop) 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_woo_brands.last_update_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_woo_brands.product_brand_id=".$id."
          ORDER BY gks_eshops.eshop_sortorder, gks_woo_brands.id_woo_brand;";



          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="eshoplink_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo gks_lang('eshop');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%"  ><?php echo gks_lang('url');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Πρόσθετο');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="20%"  nowrap><?php echo gks_lang('ID eshop');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="15%"  nowrap><span class="tooltipster" title="<?php echo gks_lang('Συγχρονισμός');?>"><?php echo gks_lang('Συγχ');?></span></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="15%"  nowrap><span class="tooltipster" title="<?php echo gks_lang('Τελευταία ενημέρωση πότε και από ποιον');?>"><?php echo gks_lang('Ενημε.');?></span></th> 
                     
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="eshoplink_tr_exist" data-id="<?php echo $row_list['id_woo_brand'];?>">
              <th scope="row" nowrap class="mytdcm eshoplink_aa"><?php echo ($i);?></td>       
              <td nowrap class="mytdcm">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_eshoplink_delete_after|<?php echo $row_list['id_woo_brand'];?>" data-id="<?php echo $row_list['id_woo_brand'];?>" data-model="gks_woo_brands">
              </td>
              <td class="mytdcm"><a href="admin-eshop-item.php?id=<?php echo $row_list['eshop_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td class="mytdcml"><?php echo $row_list['eshop_name'];?></td>  
              <td class="mytdcml"><a href="<?php echo $row_list['eshop_url'];?>" target="_blank"><?php echo $row_list['eshop_url'];?></a></td>  
              <td class="mytdcm" nowrap><?php 
                if ($row_list['pluginname']=='berocket') echo '<img src="/my/img/berocket.png" style="height:32px;" class="tooltipster" title="'.gks_lang('Brands for WooCommerce | Από BeRocket').'">';
                else if ($row_list['pluginname']=='woocommercebrand') echo '<img src="/my/img/woocommerce.png" style="max-height:32px;max-width:50px;" class="tooltipster" title="'.gks_lang('Woocomerce Brands | Από Woocomerce').'">'; 
                else if (startwith($row_list['pluginname'],'gks-bai-')) {
                  $gks_bai_title='';
                  foreach (GKS_ESHOP_BRANDS_TAXONOMY as $brand_as_idiotita) {
                    if ('gks-bai-'.$brand_as_idiotita['taxonomy']==$row_list['pluginname']) {
                      $gks_bai_title=$brand_as_idiotita['name'];
                    }
                  } 
                  echo '<i class="fab fa-wordpress tooltipster" style="font-size: 32px;" title="'.$gks_bai_title.'"></i>';
                } else echo $row_list['pluginname'];
              ?></td>
              <td class="mytdcm" nowrap><a href="<?php echo $row_list['eshop_url'];
              if ($row_list['pluginname']=='berocket') echo '/wp-admin/term.php?taxonomy=berocket_brand&post_type=product&tag_ID='.$row_list['remote_brand_id'];
              else if ($row_list['pluginname']=='woocommercebrand') echo '/wp-admin/term.php?taxonomy=product_brand&post_type=product&tag_ID='.$row_list['remote_brand_id'];
              else if (startwith($row_list['pluginname'],'gks-bai-')) echo '/wp-admin/term.php?taxonomy=pa_brand&post_type=product&tag_ID='.$row_list['remote_brand_id'];
              else echo '/wp-admin/';
              ?>" target="_blank"><?php echo $row_list['remote_brand_id'];?></a></td>  
              <td class="mytdcm" nowrap><i class="eshop_sync fas fa-sync-alt tooltipster" data-eshop_id="<?php echo $row_list['eshop_id'];?>" title="<?php echo gks_lang('Συγχρονισμός τώρα');?>"></i></td>
              <td class="mytdcm" nowrap ><span class="tooltipster" title="<?php echo gks_lang('Από').': '.$row_list['gks_nickname'];?>"><?php if (isset($row_list['last_update_date'])) echo showDate(strtotime($row_list['last_update_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></span></td>
            </tr>
          <?php } ?>


     
          </tbody>
          </table>      

          
        </div>
      </div>       

      <?php 
      echo getObjectRels('gks_eshop_products_brands',$id);
      echo getActivityObjectTable('gks_eshop_products_brands',$id); 
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_eshop_products_brands','id'=>$id));
      echo $obj_fileslist['html'];
      ?>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>   
          
                

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><input id="id_product_brand" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php echo $row['id_product_brand'];?>"></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
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
      
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('eidi');?>>        

          <?php
          $query = "SELECT gks_eshop_products_brands_products.*, gks_eshop_products.product_descr,product_photo,product_code
          FROM gks_eshop_products_brands_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.id_product
          WHERE gks_eshop_products_brands_products.product_brand_id=".$id."
          ORDER BY product_code,gks_eshop_products.product_descr";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="product_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κωδικός');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Προϊόν');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="product_tr_exist" data-id="<?php echo $row_list['id_eshop_products_brands_products'];?>">
              <th scope="row" nowrap class="mytdcm product_aa"><?php echo ($i);?></td>       
              <td class="mytdcm" nowrap>
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_product_delete_after|<?php echo $row_list['id_eshop_products_brands_products'];?>" data-id="<?php echo $row_list['id_eshop_products_brands_products'];?>" data-model="gks_eshop_products_brands_products">            
              </td>
              <td class="mytdcm"><?php echo getProductPhoto($row_list['product_id'],$row_list['product_photo'],32);?></td>
              <td class="mytdcml" nowrap><?php echo $row_list['product_code'];?></td>  
              <td class="mytdcml"><?php echo '<a href="admin-products-item.php?id='.$row_list['product_id'].'">'.$row_list['product_descr'].'</a>';?></td>  
              <td class="mytdcm" nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="product"    id="product"   class="form-control form-control-sm" style="width:calc(100% - 110px);display: inline-block;margin-right: 10px;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="product_id" id="product_id">
                <button style="justify-content: center!important;vertical-align: baseline;" type="button" class="btn btn-sm btn-primary" id="add_product"
                  ><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>
     
          </tbody>
          </table>      

        </div>
      </div>        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Όλοι τα Είδη, και από τις υπο-μάρκες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('alleidi');?>>        

          <?php


          $sql="SELECT ug1.id_product_brand AS gid1, 
                       ug2.id_product_brand AS gid2, 
                       ug3.id_product_brand AS gid3, 
                       ug4.id_product_brand AS gid4, 
                       ug5.id_product_brand AS gid5,
                       ug6.id_product_brand AS gid6,
                       ug7.id_product_brand AS gid7,
                       ug8.id_product_brand AS gid8,
                       ug9.id_product_brand AS gid9,
                       ug10.id_product_brand AS gid10
          FROM ((((((((gks_eshop_products_brands AS ug1
          LEFT JOIN gks_eshop_products_brands AS ug2  ON ug1.id_product_brand = ug2.product_brand_parent_id)
          LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.id_product_brand = ug3.product_brand_parent_id)
          LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.id_product_brand = ug4.product_brand_parent_id)
          LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.id_product_brand = ug5.product_brand_parent_id)
          LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.id_product_brand = ug6.product_brand_parent_id)
          LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.id_product_brand = ug7.product_brand_parent_id)
          LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.id_product_brand = ug8.product_brand_parent_id)
          LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.id_product_brand = ug9.product_brand_parent_id)
          LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.id_product_brand = ug10.product_brand_parent_id
          where ug1.id_product_brand=".$id;
          //echo $sql;
          $result_gu = $db_link->query($sql);        
          if (!$result_gu) {
            debug_mail(false,'error sql',$sql);
            die('sql error');
          }
          $gu_in='';
          
          while ($row_gu = $result_gu->fetch_assoc()) {
            if (isset($row_gu['gid1']))  $gu_in.=$row_gu['gid1'].',';
            if (isset($row_gu['gid2']))  $gu_in.=$row_gu['gid2'].',';
            if (isset($row_gu['gid3']))  $gu_in.=$row_gu['gid3'].',';
            if (isset($row_gu['gid4']))  $gu_in.=$row_gu['gid4'].',';
            if (isset($row_gu['gid5']))  $gu_in.=$row_gu['gid5'].',';
            if (isset($row_gu['gid6']))  $gu_in.=$row_gu['gid6'].',';
            if (isset($row_gu['gid7']))  $gu_in.=$row_gu['gid7'].',';
            if (isset($row_gu['gid8']))  $gu_in.=$row_gu['gid8'].',';
            if (isset($row_gu['gid9']))  $gu_in.=$row_gu['gid9'].',';
            if (isset($row_gu['gid10'])) $gu_in.=$row_gu['gid10'].',';
          }
          if (strlen($gu_in)>0) $gu_in=substr($gu_in, 0, strlen($gu_in)-1);
          if (strlen($gu_in)==0) $gu_in='-1'; //gia na exei kati
            
          $sql="SELECT DISTINCT gks_eshop_products_brands_products.product_id, gks_eshop_products.product_descr,product_photo,product_code
          FROM gks_eshop_products_brands_products LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.id_product
          WHERE gks_eshop_products_brands_products.product_brand_id In (".$gu_in.")
          ORDER BY product_code,gks_eshop_products.product_descr";
          
          //echo $sql;
          
          $result_list = $db_link->query($sql); 
          if (!$result_list) debug_mail(false,'error sql',$sql);
          if (!$result_list) die('sql error');
          ?>                
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κωδικός');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Προϊόν');?></th> 

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
              <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></td>       
              <td class="mytdcm"><?php echo getProductPhoto($row_list['product_id'],$row_list['product_photo'],32);?></td>
              <td class="mytdcml" nowrap><?php echo $row_list['product_code'];?></td>  
              <td class="mytdcml" nowrap><?php echo '<a href="admin-products-item.php?id='.$row_list['product_id'].'">'.$row_list['product_descr'].'</a>';?></td>  
            </tr>
          <?php } ?>



          </tbody>
          </table>  
        </div>
      </div>
              
              
    </div> 
    
  </div>
</div>

<div id="dialog_eshoplink" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid" style="" >
    <div class="row" style="margin-bottom: 10px;">
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Σύνδεση μάρκας eshop');?></div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
      <label for="dialog_eshoplink_eshop" class="col-md-12 col-form-label form-control-sm text-sm-center">eshop:</label>
      <div class="col-md-12 text-sm-center">
        <select id="dialog_eshoplink_eshop" class="form-control form-control-sm">
          <option value="0"></option>
          <?php
          $query = "SELECT id_eshop, eshop_name FROM gks_eshops ORDER BY eshop_sortorder;";
          $result_list = $db_link->query($query);
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          while ($row_list = $result_list->fetch_assoc()) {
            echo '<option value="'.$row_list['id_eshop'].'">'.$row_list['eshop_name'].'</option>';
          }?>
        </select>
      </div>
    </div>
    
    <div class="row" style="margin-bottom: 10px;">
      <label for="dialog_eshoplink_eshop_pluginname" class="col-md-12 col-form-label form-control-sm text-sm-center"><?php echo gks_lang('Πρόσθετο');?>:</label>
      <div class="col-md-12 text-sm-center">
        <select id="dialog_eshoplink_eshop_pluginname" class="form-control form-control-sm">
          <option value="woocommercebrand"><?php echo gks_lang('Woocomerce Brands | Από Woocomerce');?></option>
          <option value="berocket"><?php echo gks_lang('Brands for WooCommerce | Από BeRocket');?></option>
          <?php
          foreach (GKS_ESHOP_BRANDS_TAXONOMY as $brand_as_idiotita) {
            echo '<option value="gks-bai-'.$brand_as_idiotita['taxonomy'].'">'.$brand_as_idiotita['name'].'</option>';
          } 
          ?>
        </select>
      </div>
    </div>
    
    <div class="row" style="margin-bottom: 10px;">
      <label for="dialog_eshoplink_list" class="col-md-12 col-form-label form-control-sm text-sm-center"><?php echo gks_lang('Μάρκα');?>:</label>
      <div class="col-md-12 text-sm-center">
        <input type="text" class="form-control form-control-sm" id="dialog_eshoplink_search" placeholder="<?php echo gks_lang('Αναζήτηση');?> ..." style="margin-bottom: 10px;">
      </div>

      <div class="col-md-12 text-sm-center">
        <select id="dialog_eshoplink_list" class="form-control form-control-sm" size="10">
          <option value="0"></option>
        </select>
      </div>
    </div>

        
  </div>
</div>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 


var from_php_dialog_object_rel_curr='gks_eshop_products_brands';
var from_php_activity_model='gks_eshop_products_brands';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products_brands','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products_brands','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products_brands','delete',$id);?>;


  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  
    
});
</script>

<script src='/my/js/tinymce/tinymce.min.js'></script>

<script src="js/admin-product-brands-item.js?v=<?php echo $gks_cache_version;?>"></script>

 
 
<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


