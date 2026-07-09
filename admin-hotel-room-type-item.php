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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_room_type',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');


$user_hotels=gks_get_hotels_list();


$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_room_type',['from'=>'item']);

//print date('Y-m-d H:i:s',1647820800);die();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id==-1) {
  $row=array();
  $row['id_hotel_room_type']=-1;
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['hotel_id']=0;
  if (count($user_hotels)>=1) foreach ($user_hotels as $value) {$row['hotel_id']=$value['id']; break;}
  $row['hotel_room_type_fix_id']=0;
  $row['product_id']=0;
  $row['product_photo_p']='';
  $row['product_descr_p']='';
  $row['room_type_descr']='';
  $row['room_type_price']=0;
  $row['room_type_status']='disable';
  $row['room_type_embado']=0;
  $row['room_type_visitors']=0;
  $row['room_type_visitors_childs']=0;
  $row['room_type_bedrooms']=0;
  $row['room_type_living_rooms']=0;
  $row['room_type_bathrooms']=0;
  $row['room_type_child_kounies']=0;
  $row['room_type_extra_beds']=0;
  
  $row['cc']=0;
  $row['room_type_fix_is_multi']=0;
  $row['room_type_fix_descr']='';
  
  $row['room_type_photo']='';
  
  $my_page_title=gks_lang('Νέος Τύπος δωματίου');
} else {
  
  
  $sql ="SELECT gks_hotel_room_type.*, cc_rooms.cc, 
  gks_hotel_room_type_fix.room_type_fix_descr,
  gks_hotel_room_type_fix.room_type_fix_is_multi,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_photo<>'' THEN
          gks_eshop_products.product_photo
        ELSE
          gks_eshop_products_parent.product_photo
      END
    ELSE gks_eshop_products.product_photo
  
  END as product_photo_p,
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_descr<>'' THEN
          gks_eshop_products.product_descr
        ELSE
          CASE
            WHEN gks_eshop_products.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_parent.product_descr, gks_eshop_products.product_descr_variable)
            ELSE
              gks_eshop_products_parent.product_descr
          END
      END
    ELSE gks_eshop_products.product_descr
  END as product_descr_p  
  
  FROM (((((gks_hotel_room_type 
  LEFT JOIN (
    SELECT gks_hotel_room.hotel_room_type_id, Count(*) AS cc 
    FROM gks_hotel_room GROUP BY gks_hotel_room.hotel_room_type_id
  )  AS cc_rooms ON gks_hotel_room_type.id_hotel_room_type = cc_rooms.hotel_room_type_id) 
  LEFT JOIN gks_hotel_room_type_fix ON gks_hotel_room_type.hotel_room_type_fix_id = gks_hotel_room_type_fix.id_hotel_room_type_fix) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_room_type.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_room_type.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_eshop_products ON gks_hotel_room_type.product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
  
  where id_hotel_room_type = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_room_type.hotel_id in (".implode(',',$perm_id_hotel_ids).")";

  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Τύπος δωματίου').': '.$row['room_type_descr'];
  $object_title=$row['room_type_descr'];

}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

$room_type_fix_is_multi = $row['room_type_fix_is_multi'];

if ($row['room_type_bedrooms']<=0) $row['room_type_bedrooms']=1;

stat_record();
$nav_active_array=array('hotel','hotel_room_types');

$lang_data_obj=gks_lang_data_obj_prepare('gks_hotel_room_type','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Τύπος δωματίου');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Τύπος δωματίου');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέος');?></span></h3>
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
            <label for="hotel_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ξενοδοχείο');?>:</label>
            <div class="col-md-8">
              <select id="hotel_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                foreach ($user_hotels as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ';
                  if ($row_select['id']==$row['hotel_id']) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>

          <div class="form-group row">
            <label for="room_type_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος δωματίου');?>:</label>
            <div class="col-md-8">
              <input id="room_type_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['room_type_descr']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('room_type_descr'));
          ?>          
          
          
          <div class="form-group row">
            <label for="room_type_status" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-md-8">
              <select name="room_type_status" id="room_type_status"  class="form-control form-control-sm myneedsave">
                <option value="disable"    <?php echo ($row['room_type_status']=='disable' ? ' selected ':'');?>    ><?php echo getHotelRoomTypeStatusDescr('disable');?></option>
                <option value="available"  <?php echo ($row['room_type_status']=='available' ? ' selected ':'');?>  ><?php echo getHotelRoomTypeStatusDescr('available');?></option>
                <option value="renovation" <?php echo ($row['room_type_status']=='renovation' ? ' selected ':'');?> ><?php echo getHotelRoomTypeStatusDescr('renovation');?></option>
              </select>
            </div>
          </div>         
          <div class="form-group row">
            <label for="hotel_room_type_fix_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ομάδα');?>:</label>
            <div class="col-md-8">
              <select name="hotel_room_type_fix_id" id="hotel_room_type_fix_id"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="select * FROM gks_hotel_room_type_fix where room_type_fix_disabled=0 ORDER BY room_type_fix_descr";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              $room_type_fix_is_multi_array=array();
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_hotel_room_type_fix'].'" ';
                if ($row_select['id_hotel_room_type_fix']==$row['hotel_room_type_fix_id']) echo ' selected ';
                echo '>'.$row_select['room_type_fix_descr'].'</option>';
                
                $room_type_fix_is_multi_array[] = $row_select;
              }?></select>
            </div>
          </div>
          <div class="form-group row">
            <label for="room_type_price" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμή');?>:</label>
            <div class="col-md-8">
              <input id="room_type_price" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['room_type_price']);?>" min="0">
            </div>
          </div>        
          
         
  
          <div class="form-group row">
            <label for="room_type_embado" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εμβαδό σε τ.μ.');?>:</label>
            <div class="col-md-8">
              <input id="room_type_embado" type="number" class="form-control form-control-sm myneedsave" value="<?php echo myNumberFormatNo0($row['room_type_embado'],1);?>" min="0">
            </div>
          </div>
  

          <div class="form-group row" id="room_type_bedrooms_div" style="<?php if ($room_type_fix_is_multi==0) echo 'display:none';?>">
            <label for="room_type_bedrooms" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πλήθος Υπνοδωματίων');?>:</label>
            <div class="col-md-8">
              <select name="room_type_bedrooms" id="room_type_bedrooms"  class="form-control form-control-sm myneedsave">
                <?php for ($cc=1;$cc <= 20;$cc++) {
                  echo '<option value="'.$cc.'" '.($cc == $row['room_type_bedrooms'] ? ' selected ': '').'>'.$cc.'</option>';
                } ?>
              </select>
            </div>
          </div>
          <div class="form-group row" id="room_type_living_rooms_div" style="<?php if ($room_type_fix_is_multi==0) echo 'display:none';?>">
            <label for="room_type_living_rooms" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πλήθος Σαλονιών');?>:</label>
            <div class="col-md-8">
              <select name="room_type_living_rooms" id="room_type_living_rooms"  class="form-control form-control-sm myneedsave">
                <?php for ($cc=0;$cc <= 20;$cc++) {
                  echo '<option value="'.$cc.'" '.($cc == $row['room_type_living_rooms'] ? ' selected ': '').'>'.$cc.'</option>';
                } ?>
              </select>
            </div>
          </div>
          <div class="form-group row" id="room_type_bathrooms_div" style="<?php if ($room_type_fix_is_multi==0) echo 'display:none';?>">
            <label for="room_type_bathrooms" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πλήθος Μπάνιων');?>:</label>
            <div class="col-md-8">
              <select name="room_type_bathrooms" id="room_type_bathrooms"  class="form-control form-control-sm myneedsave">
                <?php for ($cc=0;$cc <= 20;$cc++) {
                  echo '<option value="'.$cc.'" '.($cc == $row['room_type_bathrooms'] ? ' selected ': '').'>'.$cc.'</option>';
                } ?>
              </select>
            </div>
          </div>




        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Επισκέπτες');?>
        </div>
        <div class="card-body"  <?php echo gks_card_body('visit');?>>       
          <?php
          if ($row['room_type_visitors']<=0) $row['room_type_visitors']=1;
          if ($row['room_type_visitors_childs']<=0) $row['room_type_visitors_max']=$row['room_type_visitors'];
          if ($row['room_type_visitors_max'] > $row['room_type_visitors'] + $row['room_type_visitors_childs']) $row['room_type_visitors_max']=$row['room_type_visitors'] + $row['room_type_visitors_childs'];
          
          
          ?>
          <div class="form-group row">
            <label for="room_type_visitors" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέγιστος αριθμός ενηλίκων');?>:</label>
            <div class="col-md-6">
              <select name="room_type_visitors" id="room_type_visitors"  class="form-control form-control-sm myneedsave" style="max-width:100px">
                <?php for ($cc=1;$cc <= 50;$cc++) {
                  echo '<option value="'.$cc.'" '.($cc == $row['room_type_visitors'] ? ' selected ': '').'>'.$cc.'</option>';
                } ?>
              </select>
              
            </div>
          </div>
          <div class="form-group row">
            <label for="room_type_visitors_childs" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέγιστος αριθμός παιδιών');?>:</label>
            <div class="col-md-6">
              <select name="room_type_visitors_childs" id="room_type_visitors_childs"  class="form-control form-control-sm myneedsave" style="max-width:100px">
                <?php for ($cc=0;$cc <= 20;$cc++) {
                  echo '<option value="'.$cc.'" '.($cc == $row['room_type_visitors_childs'] ? ' selected ': '').'>'.$cc.'</option>';
                } ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="room_type_visitors_max" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέγιστος αριθμός επισκεπτών');?>:</label>
            <div class="col-md-6">
              <select name="room_type_visitors_max" id="room_type_visitors_max"  class="form-control form-control-sm myneedsave" style="max-width:100px" <?php if ($row['room_type_visitors_childs']<=0) echo ' disabled ';?>>
                <?php for ($cc=1;$cc <= 70;$cc++) {
                  echo '<option value="'.$cc.'" '.($cc == $row['room_type_visitors_max'] ? ' selected ': '').'>'.$cc.'</option>';
                } ?>
              </select>
            </div>
          </div>

          <div class="form-group row">
            <label for="room_type_child_kounies" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέγιστος αριθμός βρεφικών κρεβατιών');?>:</label>
            <div class="col-md-6">
              <select name="room_type_child_kounies" id="room_type_child_kounies"  class="form-control form-control-sm myneedsave" style="max-width:100px">
                <?php for ($cc=0;$cc <= 9;$cc++) {
                  echo '<option value="'.$cc.'" '.($cc == $row['room_type_child_kounies'] ? 'selected': '').
                  ($cc>$row['room_type_visitors_childs'] ? ' style="display:none;"' : '').
                  '>'.$cc.'</option>';
                } ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="room_type_extra_beds" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέγιστος αριθμός επιπλέον κρεβατιών');?>:</label>
            <div class="col-md-6">
              <select name="room_type_extra_beds" id="room_type_extra_beds"  class="form-control form-control-sm myneedsave" style="max-width:100px">
                <?php for ($cc=0;$cc <= 9;$cc++) {
                  echo '<option value="'.$cc.'" '.($cc == $row['room_type_extra_beds'] ? ' selected ': '').'>'.$cc.'</option>';
                } ?>
              </select>
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
            <div class="col-md-12" style="text-align:center;"><?php echo gks_lang('Η προεπιλεγμένη φωτογραφία του τύπου δωματίου');?></div>
            
            <div class="col-md-12" style="text-align:center;">
              <?php
              $user_photo_value="";
              $myimgurl = $row['room_type_photo']; 
              //echo $myimgurl;
              if ($myimgurl.'' == '') {
                $myimgurl="/my/img/product.png";
              } else {
                $user_photo_value = $myimgurl;
              }
              ?>
              <img src="<?php echo $myimgurl;?>" border="0" style="max-width:96px;max-height:96px;" id="form_room_type_photo_img"/><br>
              
              <a href="" id="reset_profile_photo" title="<?php echo gks_lang('Διαγραφή');?>" <?php 
                if ($user_photo_value == '') {
                  echo ' style="display:none" ';
                }
                ?> ><img src="/my/img/0.png" border="0" width="16" ></a>
              <br><input type="hidden" id="form_room_type_photo" name="form_room_type_photo" value="<?php echo $user_photo_value;?>" />
            </div>                     
          </div>
          <div class="row">
            <div class="col-md-12" style="text-align:center; padding-top: 24px;"><?php echo gks_lang('Φωτογραφίες του τύπου δωματίου');?></div>
            
            <form role="form" method="post" action="admin-hotel-room-type-item-photo-upload.php" id="myphoto_upload" enctype="multipart/form-data" style="width: 100%;">
              <input type="hidden" name="hotel_room_type_id" id="hotel_room_type_id" value="<?php echo $id;?>">
              <div id="lightgallery_user">
                <div class="form-group" id="imagelist_photo">
                <?php   
                  $sql="select * from gks_hotel_room_type_photo where hotel_room_type_id=".$id." and filesobjectlist=0 order by id_hotel_room_type_photo";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    $photo_url = $row_select['photo_url'];
                    $photo_url_thumb = dirname($row_select['photo_url']).'/thumbnail/'.mb_basename($row_select['photo_url']);


                    ?>
                    <div id="item_upload_photo_<?php echo $row_select['id_hotel_room_type_photo'];?>" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">
                      <a class="lightgalleryitem_user" href="<?php echo $photo_url;?>" data-download-url="<?php echo $photo_url;?>">
                        <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="<?php echo $photo_url_thumb;?>">
                      </a>
                      <br>
                      <div style="padding-top:4px">
                        <a href="" class="set_profile_photo"   data-url="<?php echo $photo_url_thumb;?>" title="<?php echo gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία');?>"><img src="/my/img/icons/photo.png" border="0" width="16"></a>
                        <a href="" class="delete_upload_photo" data-url="<?php echo $photo_url_thumb;?>" data-id="<?php echo $row_select['id_hotel_room_type_photo'];?>" title="<?php echo gks_lang('Διαγραφή');?>"><img src="/my/img/0.png" border="0" width="16"></a>
                      </div>
                    </div>
                  <?php }?>
                </div>
              </div>
              <?php gks_f_button_add_files_photo_html('gks_hotel_room_type',$id);?>
            </form>                      
            
            
          </div>

        </div>
      </div>  


      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center" >
          <?php echo gks_lang('Είδος Τιμολόγησης');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('eidos');?>>       

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="product_id"><?php echo gks_lang('Είδος για τιμολόγηση');?>:</label>
            <div class="col-sm-8">
              <input id="product_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_descr_p']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php if ($id==-1) echo gks_lang('Θα δημιουργηθεί αυτόματα'); else echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
              data-id="<?php echo $row['product_id'];?>">
              <a id="autocomplete_product_id" tabindex="-1" href="admin-products-item.php?id=<?php echo $row['product_id'];?>" style="<?php if ($row['product_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή είδους');?>"></i></a>
            </div>
          </div>
          <?php
          $myimgurl=trim_gks($row['product_photo_p'].'');
          
          if ($myimgurl == '') {
            $myimgurl="/my/img/product.png";
            $photo_url='';
          } else {
            $mydir = dirname($myimgurl);
            if (endwith($mydir,'/thumbnail')) {
              $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
            } else {
              $photo_url=$myimgurl;
            }
          }
          ?>
          <div class="form-group row" id="div_product_photo" style="<?php if ($photo_url=='') echo 'display:none;';?>">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="product_id"><?php echo gks_lang('Φωτογραφία');?>:</label>
            <div class="col-sm-8" id="div_photo"><?php
              echo '<a href="'.$photo_url.'" class="class_a_product_photo"><img id="img_product_photo" src="'.$myimgurl.'" style="max-width:96px;max-height:96px;"></a>';
            ?>
            </div>
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





<?php

$subrooms=array();
$sql_subroom="select gks_hotel_room_type_subroom.*, gks_hotel_room_type_subroom_en_US.subroom_descr_en_US
from gks_hotel_room_type_subroom 
LEFT JOIN (
  SELECT hotel_room_type_subroom_id, subroom_descr as subroom_descr_en_US FROM gks_hotel_room_type_subroom_lang WHERE lang_code='en-US'
) AS gks_hotel_room_type_subroom_en_US ON gks_hotel_room_type_subroom.id_hotel_room_type_subroom = gks_hotel_room_type_subroom_en_US.hotel_room_type_subroom_id  

where hotel_room_type_id=".$id." 
order by subroom_descr, id_hotel_room_type_subroom";
$result_subroom = $db_link->query($sql_subroom);
if (!$result_subroom) {
  debug_mail(false,'error sql',$sql_subroom);
  die('sql error');
}
$subroom_in='';
while ($row_subroom = $result_subroom->fetch_assoc()) {
  $subroom_in.=$row_subroom['id_hotel_room_type_subroom'].',';
  $subrooms[$row_subroom['id_hotel_room_type_subroom']]=array(
    'id' => $row_subroom['id_hotel_room_type_subroom'],
    'hotel_room_type_id' => $row_subroom['hotel_room_type_id'],
    'subroom_type' => $row_subroom['subroom_type'],
    'subroom_descr' => $row_subroom['subroom_descr'],
    'subroom_descr_en_US' => $row_subroom['subroom_descr_en_US'],
    'subroom_visitors' => $row_subroom['subroom_visitors'],
    'subroom_private_bath' => $row_subroom['subroom_private_bath']!=0,
    'beds' => array(),
  );
}
if (strlen($subroom_in)>0) {
  $subroom_in=substr($subroom_in, 0, strlen($subroom_in)-1);
  $sql_subroom="SELECT gks_hotel_room_type_subroom_bed.*, gks_hotel_bed_type_fix.bed_type_fix_descr, gks_hotel_bed_type_fix.bed_type_fix_descr_extra
  FROM gks_hotel_room_type_subroom_bed 
  LEFT JOIN gks_hotel_bed_type_fix ON gks_hotel_room_type_subroom_bed.hotel_bed_type_fix_id = gks_hotel_bed_type_fix.id_hotel_bed_type_fix
  WHERE gks_hotel_room_type_subroom_bed.hotel_room_type_subroom_id In (".$subroom_in.")
  ORDER BY gks_hotel_room_type_subroom_bed.id_hotel_room_type_subroom_bed;";
  $result_subroom = $db_link->query($sql_subroom);
  if (!$result_subroom) {
    debug_mail(false,'error sql',$sql_subroom);
    die('sql error');
  }
  while ($row_subroom = $result_subroom->fetch_assoc()) {
    $subrooms[$row_subroom['hotel_room_type_subroom_id']]['beds'][] = array (
      'hotel_bed_type_fix_id' => $row_subroom['hotel_bed_type_fix_id'],
      'subroom_bed_plithos' => $row_subroom['subroom_bed_plithos'],
      'bed_type_fix_descr' => $row_subroom['bed_type_fix_descr'],
      'bed_type_fix_descr_extra' => $row_subroom['bed_type_fix_descr_extra'],
    );
  }
}
if (count($subrooms) == 0) {
  $subrooms[0] = array(
    'id' => 0,
    'hotel_room_type_id' =>0,
    'subroom_type' => 'bedroom',
    'subroom_descr' => '',
    'subroom_descr_en_US' => '',
    'subroom_visitors' => 0,
    'subroom_private_bath' => false,
    'beds' => array(),    
  );
}
$sql="select * FROM gks_hotel_bed_type_fix where bed_type_fix_disabled = 0 ORDER BY bed_type_fix_descr";
$result_select = $db_link->query($sql);
if (!$result_select) {
  debug_mail(false,'error sql',$sql);
  die('sql error');
}
$bed_types=array();
while ($row_select = $result_select->fetch_assoc()) {
  $bed_types[]=array(
    'id' => $row_select['id_hotel_bed_type_fix'], 
    'descr' => $row_select['bed_type_fix_descr'].((isset($row_select['bed_type_fix_descr_extra']) and $row_select['bed_type_fix_descr_extra']!='') ? ' ('.$row_select['bed_type_fix_descr_extra'].')' : '' ),
    'bed_type_fix_onlybedroom'=> $row_select['bed_type_fix_onlybedroom']!=0,
    'bed_type_fix_visitors'=> $row_select['bed_type_fix_visitors'],
  );
}

//print '<pre>';
//print_r($subrooms);
//die();


$template_bedroom_bed='
      <div class="row bed_row mb-2" id="bed_[[subroomindex]]_[[bedindex]]">
        <div class="offset-md-1 col-md-7">
          <select id="bed_type_[[subroomindex]]_[[bedindex]]" name="bed_type_[[subroomindex]]_[[bedindex]]" class="bed_type_select form-control form-control-sm myneedsave">
            [[bed_type]]
          </select>
        </div>
        <div class="col-md-4 text-right">
          <span>x</span>
          <select name="bed_num_[[subroomindex]]_[[bedindex]]" id="bed_num_[[subroomindex]]_[[bedindex]]"  class="bed_num_select form-control form-control-sm myneedsave" style="width:calc(100% - 65px);display:inline;margin-left:10px;margin-right:10px;">
            [[bed_num]]
          </select>
          <i id="bed_del_[[subroomindex]]_[[bedindex]]" class="bed_del_class fas fa-trash-alt" style="cursor: pointer;font-size: 150%;position:relative;top:4px" data-toggle="tooltip" title="'.gks_lang('Αφαίρεση').'"></i>
        </div>
      </div>';

      
$template_bedroom='
<div class="card gks_card_expand text-left text-white bg-secondary mb-4 subroom_bedroom" id="subroom_[[subroomindex]]">
  <h6 class="card-header"><strong><span id="subroom_name_def_[[subroomindex]]">'.gks_lang('Υπνοδωμάτιο').' [[subroomindex]]</span></strong> 
    <span id="subroom_name_def_en_US_[[subroomindex]]" style="display:none;">Bedroom [[subroomindex]]</span>
    <input id="subroom_name_[[subroomindex]]"  type="text" class="form-control form-control-sm myneedsave mystoppropagation" style="max-width:200px;display:inline" placeholder="'.gks_lang('Όνομα υπνοδωματίου').'" value="[[subroom_name_value]]"/>
    <input id="subroom_name_en_US_[[subroomindex]]"  type="text" class="form-control form-control-sm myneedsave mystoppropagation" style="max-width:200px;display:inline" placeholder="'.gks_lang('Αγγλικά').'" value="[[subroom_name_value_en_US]]"/>
  
  </h6>
  <div class="card-body" '.gks_card_body('ypnodom').'>
    <p class="card-text mb-0">'.gks_lang('Οι τύπου κρεβατιών του δωματίου').':</p>
    <div class="bed_list" id="bed_list_subroom_[[subroomindex]]">
      [[bedlist]]
    </div>
    <div class="row">
      <div class="offset-md-1 col-md-11">
        <button id="bed_add_[[subroomindex]]" type="button" class="bed_add_class btn btn-info btn-sm">
          <i class="fas fa-plus-circle" style="cursor: pointer;font-size: 100%;"></i>
          <span>'.gks_lang('Προσθήκη νέου τύπου κρεβατιού').'</span>
        </button>
      </div>
    </div>
    <p class="card-text mt-2 mb-0">'.gks_lang('Σύνολο επισκεπτών αυτού του δωματίου').':</p>
    <div class="row">
      <div class="offset-md-1 col-md-11">
        <input id="subroom_visitors_[[subroomindex]]" type="number" class="bed_num_visitors form-control form-control-sm myneedsave" min="1" max="1000" style="max-width:100px" value="[[subroom_visitors_value]]">
      </div>
    </div>
    <p class="card-text mt-2 mb-0">'.gks_lang('Έχει ιδιωτικό μπάνιο').':</p>
    <div class="row">
      <div class="offset-md-1 col-md-11">
        <div class="custom-control custom-checkbox">
          <input id="subroom_bath_[[subroomindex]]" type="checkbox" class="custom-control-input myneedsave" [[subroom_bath_checked]]>
          <label class="custom-control-label" for="subroom_bath_[[subroomindex]]">'.gks_lang('Ναι').'</label>
        </div>
      </div>
    </div>
  </div>
</div>
';

$template_livingroom='
<div class="card gks_card_expand text-left text-white bg-dark mb-4 subroom_livingroom" id="lrsubroom_[[subroomindex]]">
  <h6 class="card-header"><strong><span id="lrsubroom_name_def_[[subroomindex]]">'.gks_lang('Σαλόνι').' [[subroomindex]]</span></strong> 
    <span id="lrsubroom_name_def_en_US_[[subroomindex]]" style="display:none;">Livingroom [[subroomindex]]</span>
    <input id="lrsubroom_name_[[subroomindex]]" type="text" class="form-control form-control-sm myneedsave mystoppropagation" style="max-width:200px;display:inline" placeholder="'.gks_lang('Όνομα σαλονιού').'" value="[[subroom_name_value]]"/>
    <input id="lrsubroom_name_en_US_[[subroomindex]]" type="text" class="form-control form-control-sm myneedsave mystoppropagation" style="max-width:200px;display:inline" placeholder="'.gks_lang('Αγγλικά').'" value="[[subroom_name_value_en_US]]"/>
  
  
  </h6>
  <div class="card-body" '.gks_card_body('saloni').'>
    <p class="card-text mb-0">'.gks_lang('Οι τύπου καναπέδων του σαλονιού').':</p>
    <div class="bed_list" id="bed_list_lrsubroom_[[subroomindex]]">
      [[bedlist]]
    </div>
    <div class="row">
      <div class="offset-md-1 col-md-11">
        <button id="lrbed_add_[[subroomindex]]" type="button" class="bed_add_class btn btn-info btn-sm">
          <i class="fas fa-plus-circle" style="cursor: pointer;font-size: 100%;"></i>
          <span>'.gks_lang('Προσθήκη νέου τύπου καναπέ').'</span>
        </button>
      </div>
    </div>
    <p class="card-text mt-2 mb-0">'.gks_lang('Σύνολο επισκεπτών αυτού του σαλονιού').':</p>
    <div class="row">
      <div class="offset-md-1 col-md-11">
        <input id="lrsubroom_visitors_[[subroomindex]]" type="number" class="bed_num_visitors form-control form-control-sm myneedsave" min="0" max="1000" style="max-width:100px" value="[[subroom_visitors_value]]">
      </div>
    </div>
  </div>
</div>
';
?>


<?php

//echo '<pre>'.gks_card_body('saloni').'</pre>';
?>
        
<div class="container-fluid" id="room_type_bedrooms_list">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand text-center">
        <h5 class="card-header"><?php echo gks_lang('Λίστα Υπνοδωματίων');?></h5>
        <div class="card-body" <?php echo gks_card_body('lstyp');?>>
        <?php
        $index=0;
        foreach ($subrooms as $value) {
          if ($value['subroom_type'] == 'bedroom') {
            $index++;
            $bedlist='';
            $bed_index=0;
            foreach ($value['beds'] as $bed) {
              $bed_index++;
              $bed_out = $template_bedroom_bed;

              $bed_num_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
              for ($cc=1;$cc <= 10;$cc++) {
                $bed_num_.= '<option value="'.$cc.'" '.(($bed['subroom_bed_plithos'] ==$cc) ? ' selected ': '').'>'.$cc.'</option>';
              }
              $bed_type_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
              foreach ($bed_types as $bedtype) {
                $bed_type_.= '<option value="'.$bedtype['id'].'" '.(($bed['hotel_bed_type_fix_id'] ==$bedtype['id']) ? ' selected ': '').'>'.$bedtype['descr'].'</option>';
              } 
              
              $bed_out=str_replace('[[bed_num]]', $bed_num_, $bed_out);
              $bed_out=str_replace('[[bed_type]]', $bed_type_, $bed_out);
              $bed_out=str_replace('[[subroomindex]]', $index, $bed_out);
              $bed_out=str_replace('[[bedindex]]', $bed_index, $bed_out);
              $bedlist.=$bed_out;
            }
            if ($bed_index==0) {
              $bed_index++;
              $bed_out = $template_bedroom_bed;
              
              $bed_num_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
              for ($cc=1;$cc <= 10;$cc++) {
                $bed_num_.= '<option value="'.$cc.'" >'.$cc.'</option>';
              }
              $bed_type_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
              foreach ($bed_types as $bedtype) {
                $bed_type_.= '<option value="'.$bedtype['id'].'">'.$bedtype['descr'].'</option>';
              } 
              $bed_out=str_replace('[[bed_num]]', $bed_num_, $bed_out);
              $bed_out=str_replace('[[bed_type]]', $bed_type_, $bed_out);
              $bed_out=str_replace('[[subroomindex]]', $index, $bed_out);
              $bed_out=str_replace('[[bedindex]]', $bed_index, $bed_out);
              $bedlist.=$bed_out;
            }
            $out=$template_bedroom;
            $out=str_replace('[[subroomindex]]', $index, $out);
            $out=str_replace('[[bedlist]]', $bedlist, $out);
            $out=str_replace('[[subroom_name_value]]', $value['subroom_descr'], $out);
            $out=str_replace('[[subroom_name_value_en_US]]', trim_gks($value['subroom_descr_en_US']), $out);
            $out=str_replace('[[subroom_bath_checked]]', ($value['subroom_private_bath'] ? ' checked ': ''), $out);
            $out=str_replace('[[subroom_visitors_value]]', $value['subroom_visitors'], $out);
            print $out;
          }
        } 
        ?>
        </div>
      </div> 
    </div> 
  </div> 
</div> 
<div class="container-fluid mt-4" id="room_type_living_rooms_list" style="<?php echo ($row['room_type_living_rooms']>0 ? '' : 'display:none');?>">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand text-center">
        <h5 class="card-header"><?php echo gks_lang('Λίστα Σαλονιών');?></h5>
        <div class="card-body"  <?php echo gks_card_body('lstsal');?> id="room_type_living_rooms_list_inner">
        <?php
        $index=0;
        foreach ($subrooms as $value) {
          if ($value['subroom_type'] == 'livingroom') {
            $index++;
            $bedlist='';
            $bed_index=0;
            foreach ($value['beds'] as $bed) {
              $bed_index++;
              $bed_out = $template_bedroom_bed;

              $bed_num_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
              for ($cc=1;$cc <= 10;$cc++) {
                $bed_num_.= '<option value="'.$cc.'" '.(($bed['subroom_bed_plithos'] ==$cc) ? ' selected ': '').'>'.$cc.'</option>';
              }
              $bed_type_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
              foreach ($bed_types as $bedtype) {
                if ($bedtype['bed_type_fix_onlybedroom'] == false) {
                  $bed_type_.= '<option value="'.$bedtype['id'].'" '.(($bed['hotel_bed_type_fix_id'] ==$bedtype['id']) ? ' selected ': '').'>'.$bedtype['descr'].'</option>';
                }
              } 
              
              $bed_out=str_replace('[[bed_num]]', $bed_num_, $bed_out);
              $bed_out=str_replace('[[bed_type]]', $bed_type_, $bed_out);
              $bed_out=str_replace('[[subroomindex]]', $index, $bed_out);
              $bed_out=str_replace('[[bedindex]]', $bed_index, $bed_out);
              
              
              $bedlist.=$bed_out;
            }
            if ($bed_index==0) {
              $bed_index++;
              $bed_out = $template_bedroom_bed;
              
              $bed_num_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
              for ($cc=1;$cc <= 10;$cc++) {
                $bed_num_.= '<option value="'.$cc.'" >'.$cc.'</option>';
              }
              $bed_type_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
              foreach ($bed_types as $bedtype) {
                if ($bedtype['bed_type_fix_onlybedroom'] == false) {
                  $bed_type_.= '<option value="'.$bedtype['id'].'">'.$bedtype['descr'].'</option>';
                }
              } 
              
              $bed_out=str_replace('[[bed_num]]', $bed_num_, $bed_out);
              $bed_out=str_replace('[[bed_type]]', $bed_type_, $bed_out);
              $bed_out=str_replace('[[subroomindex]]', $index, $bed_out);
              $bed_out=str_replace('[[bedindex]]', $bed_index, $bed_out);
              $bedlist.=$bed_out;
            }
            $out=$template_livingroom;
            $out=str_replace('[[subroomindex]]', $index, $out);
            $out=str_replace('[[bedlist]]', $bedlist, $out);
            $out=str_replace('[[subroom_name_value]]', $value['subroom_descr'], $out);
            $out=str_replace('[[subroom_name_value_en_US]]', $value['subroom_descr_en_US'], $out);
            $out=str_replace('[[subroom_visitors_value]]', $value['subroom_visitors'], $out);
            print $out;
          }
        } 
        ?>




        </div>
      </div> 
    </div> 
  </div> 
</div> 




<div class="container-fluid mt-4">
  <div class="row">
    
    <div class="col-md-12">
      <div class="card gks_card_expand text-center">
        <h5 class="card-header"><?php echo gks_lang('Παροχές Δωματίων');?></h5>
        <div class="card-body" <?php echo gks_card_body('paroxes');?>>
        <?php
        $sql_amenitygroup="SELECT hotel_room_amenity_type_fix_id FROM gks_hotel_room_type_amenity WHERE hotel_room_type_id=".$id;
        $result_amenitygroup = $db_link->query($sql_amenitygroup);
        if (!$result_amenitygroup) {
          debug_mail(false,'error sql',$sql_amenitygroup);
          die('sql error');
        }
        $hotel_room_amenity_type_fix_id=array();
        while ($row_amenitygroup = $result_amenitygroup->fetch_assoc()) {         
          $hotel_room_amenity_type_fix_id[] = $row_amenitygroup['hotel_room_amenity_type_fix_id'];
        }

        
        $sql_amenitygroup="SELECT * FROM gks_hotel_room_amenity_group_type_fix order by room_amenity_group_type_fix_sortorder";
        $result_amenitygroup = $db_link->query($sql_amenitygroup);
        if (!$result_amenitygroup) {
          debug_mail(false,'error sql',$sql_amenitygroup);
          die('sql error');
        }
        $amenity_groups=array();
        while ($row_amenitygroup = $result_amenitygroup->fetch_assoc()) {         
          $amenity_groups[] = array(
            'id'=> $row_amenitygroup['id_hotel_room_amenity_group_type_fix'],
            'descr'=> $row_amenitygroup['room_amenity_group_type_fix_descr'],
          );
        }
        
        foreach ($amenity_groups as $amenitygroup) {
        ?>
        
       
          <div class="card gks_card_expand text-left bg-light mb-4">
            <h6 class="card-header text-center"><strong><?php echo $amenitygroup['descr'];?></strong></h6>
            <div class="card-body" <?php echo gks_card_body('ame'.$amenitygroup['id']);?>>
              <?php
              $sql_amenitygroup="SELECT * FROM gks_hotel_room_amenity_type_fix
              WHERE hotel_room_amenity_group_type_fix_id=".$amenitygroup['id']." and room_amenity_type_fix_disabled=0 
              ORDER BY room_amenity_type_fix_descr;";
              $result_amenitygroup = $db_link->query($sql_amenitygroup);
              if (!$result_amenitygroup) {
                debug_mail(false,'error sql',$sql_amenitygroup);
                die('sql error');
              }
              while ($row_amenitygroup = $result_amenitygroup->fetch_assoc()) { 
                $aid=$row_amenitygroup['id_hotel_room_amenity_type_fix'];
                $adescr=$row_amenitygroup['room_amenity_type_fix_descr'];
                $amemo=$row_amenitygroup['room_amenity_type_fix_memo'].'';
                $achecked = '';
                if (in_array($aid,$hotel_room_amenity_type_fix_id)) $achecked = 'checked';
                ?>
                <div class="row mb-1">
                  <div class="col-4 text-right">
                    <input type="checkbox" name="amenity-<?php echo $aid;?>" id="amenity-<?php echo $aid;?>" class="my_amenity" data-id="<?php echo $aid;?>" <?php echo $achecked;?>  >
                  </div>
                  <div class="col-8">
                    <label for="amenity-<?php echo $aid;?>" class="room_type_amenity_label"><?php echo $adescr;?></label>
                    <?php if ($amemo!='') {
                      echo '<span class="room_type_amenity_label_text">'.$amemo.'</span>';
                    } ?>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        
        <?php } ?>
        
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_hotel_room_type'];?>" data-model="gks_hotel_room_type" data-backurl="admin-hotel-room-type.php"><?php echo gks_lang('Διαγραφή');?></button>
      
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    
    <div class="col-xl-6">
    
      <?php 
      echo getObjectRels('gks_hotel_room_type',$id);
      echo getActivityObjectTable('gks_hotel_room_type',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_hotel_room_type','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
            
    </div>
    <div class="col-xl-6">

        
      <div class="card gks_card_expand">
         

        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_hotel_room_type']>0) echo $row['id_hotel_room_type'];?></span></div>
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
<script src='/my/js/tinymce/tinymce.min.js'></script>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 


var from_php_dialog_object_rel_curr='gks_hotel_room_type';
var from_php_activity_model='gks_hotel_room_type';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room_type','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room_type','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_room_type','delete',$id);?>;



tinymce.init({
  language: from_php_gks_tinymce_locale,
  entity_encoding : 'raw',
  forced_root_block:false, 
  remove_trailing_brs: false,
  theme: 'silver', 
  browser_spellcheck: true,
  plugins: 'autoresize print preview  searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount imagetools textpattern help code',
  toolbar: 'undo redo formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
  menubar:true,
  statusbar: true,
  contextmenu: '', //gia na gine disable to default
  templates: [],
  content_css: [],
  content_style: '.mce-content-body {font-size:12px;font-family:"Open Sans",sans-serif;}',
  relative_urls : true,
  convert_urls: true,
  document_base_url : (window.location.origin + '/'),
  min_height: 200,
    
  selector: '.gks_tinymce',
  init_instance_callback: function(editor) {
    editor.on('Change', function(e) {
      need_save=true;
    });
  },
  readonly : (from_php_perm_ret_edit ? 0 : 1),
    
});
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  
  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      //console.log(event.ctrlKey);
      //console.log(event.which);
      event.preventDefault();
      event.stopPropagation();
      elem=$('#submit_button_ok_custom');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
    }  
  });  

  

 
  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
    
  function mysubmit() {
    total_visitors= parseInt($('#room_type_visitors_max').val());
    if (isNaN(total_visitors)) total_visitors=0;
    if (total_visitors<=0) {
      myalert('error:'.gks_lang('Ο μέγιστος αριθμός ατόμων δωματίου πρέπει να είναι τουλάχιστον 1'));
      return;
    }  
    
      
    myroomdata=[];
    $('.subroom_bedroom').each(function() {
      room_idd = $(this).attr('id');
      room_id = room_idd.replace('subroom_','');
      var thisroom={};
      thisroom.id =room_id;
      thisroom.pre ='';
      thisroom.type ='bedroom';
      thisroom.name = $('#subroom_name_'+ room_id).val().trim();
      thisroom.name_en_US = $('#subroom_name_en_US_'+ room_id).val().trim();
      if (thisroom.name == '' ) thisroom.name = $('#subroom_name_def_'+ room_id).html().trim();
      if (thisroom.name_en_US == '' ) thisroom.name_en_US = $('#subroom_name_def_en_US_'+ room_id).html().trim();
      thisroom.visitors = parseInt($('#subroom_visitors_'+ room_id).val());
      if (isNaN(thisroom.visitors)) thisroom['visitors']=0;
      thisroom.bath = $('#subroom_bath_'+ room_id).is(":checked");
      var bedarray=[];
      $('#bed_list_subroom_' + room_id + ' .bed_row').each(function() {
        bedidd=$(this).attr('id');
        bedid=bedidd.replace('bed_','');
        beditem={};
        beditem.id= bedid;
        beditem.type= parseInt($('#bed_list_subroom_' + room_id + ' .bed_row #bed_type_' + bedid).val());
        beditem.num= parseInt($('#bed_list_subroom_' + room_id + ' .bed_row #bed_num_' + bedid).val());
        bedarray.push(beditem);
      });
      thisroom.rooms=bedarray;
      myroomdata.push(thisroom);
    });

     
    
    $('.subroom_livingroom').each(function() {
      room_idd = $(this).attr('id');
      room_id = room_idd.replace('lrsubroom_','');
      var thisroom={};
      thisroom.id =room_id;
      thisroom.pre ='lr';
      thisroom.type ='livingroom';
      thisroom.name = $('#lrsubroom_name_'+ room_id).val().trim();
      thisroom.name_en_US = $('#lrsubroom_name_en_US_'+ room_id).val().trim();
      if (thisroom.name == '' ) thisroom.name = $('#lrsubroom_name_def_'+ room_id).html().trim();
      if (thisroom.name_en_US == '' ) thisroom.name_en_US = $('#lrsubroom_name_def_en_US_'+ room_id).html().trim();
      thisroom.visitors = parseInt($('#lrsubroom_visitors_'+ room_id).val());
      if (isNaN(thisroom.visitors)) thisroom['visitors']=0;
      var bedarray=[];
      $('#bed_list_lrsubroom_' + room_id + ' .bed_row').each(function() {
        bedidd=$(this).attr('id');
        bedid=bedidd.replace('bed_','');
        beditem={};
        beditem.id= bedid;
        beditem.type= parseInt($('#bed_list_lrsubroom_' + room_id + ' .bed_row #bed_type_' + bedid).val());
        beditem.num= parseInt($('#bed_list_lrsubroom_' + room_id + ' .bed_row #bed_num_' + bedid).val());
        bedarray.push(beditem);
      });
      thisroom.rooms=bedarray;
      myroomdata.push(thisroom);
    });
    
    rooms_visitors=0;
    for (ii=0; ii < myroomdata.length; ii++) {
      rooms_visitors+=myroomdata[ii].visitors;
      if (myroomdata[ii].rooms.length == 0) {
        $('#' + myroomdata[ii].pre + 'subroom_' + myroomdata[ii].id + ' #bed_add_' + myroomdata[ii].id).focus();
        myalert('error:'.gks_lang('Ορίστε κρεβάτια στο δωμάτιο')+'<br><b>' + myroomdata[ii].name + '</b>');
        return;
      }
      visitors=0;
      for (jj=0; jj < myroomdata[ii].rooms.length; jj++) {
        if (myroomdata[ii].rooms[jj].type<=0) {
          $('#' + myroomdata[ii].pre + 'subroom_' + myroomdata[ii].id + ' #bed_type_' + myroomdata[ii].rooms[jj].id).focus();
          myalert('error:'.gks_lang('Ορίστε στο δωμάτιο')+'<br><b>' + myroomdata[ii].name + '</b><br>τον τύπο του <b>' + (jj + 1) + 'ου</b> κρεβατιού');
          return;
        }
        if (myroomdata[ii].rooms[jj].num<=0) {
          $('#' + myroomdata[ii].pre + 'subroom_' + myroomdata[ii].id + ' #bed_num_' + myroomdata[ii].rooms[jj].id).focus();
          myalert('error:'.gks_lang('Ορίστε στο δωμάτιο')+'<br><b>' + myroomdata[ii].name + '</b><br>το πλήθος των κρεβατιών του <b>' + (jj + 1) + 'ου</b> κρεβατιού');
          return;
        }
      }
      
      if (myroomdata[ii].visitors < 0) {
        $('#' + myroomdata[ii].pre + 'subroom_visitors_' + myroomdata[ii].id).focus();
        myalert('error:'+gks_lang('Ορίστε το πλήθος επισκεπτών στο δωμάτιο')+'<br><b>' + myroomdata[ii].name + '</b>');
        return;
      }
      
    }
    
    if (rooms_visitors != total_visitors) {
      $('#room_type_visitors').focus();
      myalert('error:'+gks_lang('Ο μέγιστος αριθμός ατόμων του δωματίου')+' (' + total_visitors + ') '+gks_lang('δεν είναι ίσος με το άθροισμα των επιμέρους δωματίων')+' (' + rooms_visitors + ').');
      return;      
    }
    
    
    //console.log(myroomdata);
    myroomdata = JSON.stringify(myroomdata);
    //console.log(myroomdata);
    
    var myamenity=[];
    $('.my_amenity').each(function( index ) {
      if ($(this).is(":checked")) myamenity.push($(this).attr('data-id'));
    }); 
    myamenity = JSON.stringify(myamenity);
    //console.log(myamenity);
        
    //return;
    
    
    datasend='';
    datasend+='&hotel_id='  + encodeURIComponent($("#mypostform #hotel_id").val().trim());
    datasend+='&room_type_descr='  + encodeURI($("#mypostform #room_type_descr").val().trim());
    datasend+='&room_type_status='  + encodeURI($("#mypostform #room_type_status").val().trim());
    datasend+='&hotel_room_type_fix_id='  + encodeURI($("#mypostform #hotel_room_type_fix_id").val().trim());
    datasend+='&room_type_price='  + encodeURI($("#mypostform #room_type_price").val().trim());
    datasend+='&room_type_embado='  + encodeURI($("#mypostform #room_type_embado").val().trim());
    datasend+='&room_type_visitors='  + encodeURI($("#mypostform #room_type_visitors").val().trim());
    datasend+='&room_type_visitors_childs='  + encodeURI($("#mypostform #room_type_visitors_childs").val().trim());
    datasend+='&room_type_visitors_max='  + encodeURI($("#mypostform #room_type_visitors_max").val().trim());
    datasend+='&room_type_bedrooms='  + encodeURI($("#mypostform #room_type_bedrooms").val().trim());
    datasend+='&room_type_living_rooms='  + encodeURI($("#mypostform #room_type_living_rooms").val().trim());
    datasend+='&room_type_bathrooms='  + encodeURI($("#mypostform #room_type_bathrooms").val().trim());
    datasend+='&myroomdata='  + encodeURI(myroomdata);
    datasend+='&myamenity='  + encodeURI(myamenity);
    
    datasend+='&room_type_child_kounies='  + encodeURI($("#mypostform #room_type_child_kounies").val().trim());
    datasend+='&room_type_extra_beds='  + encodeURI($("#mypostform #room_type_extra_beds").val().trim());
    datasend+='&product_id='  + encodeURI($("#mypostform #product_id").attr('data-id').trim());
    
    datasend+='&form_room_type_photo='  + encodeURI($("#form_room_type_photo").val().trim());
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-hotel-room-type-item-exec.php?id=' + <?php echo $id;?>,
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
					  need_save=false;
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
  



<?php
$bed_index=1;
$bed_num_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
for ($cc=1;$cc <= 10;$cc++) {
  $bed_num_.= '<option value="'.$cc.'" >'.$cc.'</option>';
}
$bed_type_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
foreach ($bed_types as $bedtype) {
  $bed_type_.= '<option value="'.$bedtype['id'].'">'.$bedtype['descr'].'</option>';
} 
$bed_out = $template_bedroom_bed;
$bed_out=str_replace('[[bed_num]]', $bed_num_, $bed_out);
$bed_out=str_replace('[[bed_type]]', $bed_type_, $bed_out);
$out_template_bedroom_bed = $bed_out;
$bed_out=str_replace('[[bedindex]]', $bed_index, $bed_out);
$bedlist=$bed_out;

$out_template_bedroom=$template_bedroom;
$out_template_bedroom=str_replace('data-item="ypnodom" style="display:none"', 'data-item="ypnodom"', $out_template_bedroom);
$out_template_bedroom=str_replace('[[bedlist]]', $bedlist, $out_template_bedroom);


$bed_type_='<option value="0" >-- '.gks_lang('Κάντε μια επιλογή').' --</option>';
foreach ($bed_types as $bedtype) {
  if ($bedtype['bed_type_fix_onlybedroom'] == false) {
    $bed_type_.= '<option value="'.$bedtype['id'].'">'.$bedtype['descr'].'</option>';
  }
} 
$bed_out = $template_bedroom_bed;
$bed_out=str_replace('[[bed_num]]', $bed_num_, $bed_out);
$bed_out=str_replace('[[bed_type]]', $bed_type_, $bed_out);
$out_template_livingroom_bed = $bed_out;
$bed_out=str_replace('[[bedindex]]', $bed_index, $bed_out);
$bedlist=$bed_out;




$out_template_livingroom=$template_livingroom;
$out_template_livingroom=str_replace('data-item="saloni" style="display:none"', 'data-item="saloni"', $out_template_livingroom);
$out_template_livingroom=str_replace('[[bedlist]]', $bedlist, $out_template_livingroom);
?>

  var room_type_bedrooms=<?php echo $row['room_type_bedrooms'];?>;  
  var room_type_living_rooms=<?php echo $row['room_type_living_rooms'];?>;  
  var room_type_bathrooms=<?php echo $row['room_type_bathrooms'];?>;  
  var template_bedroom=$.base64.decode('<?php echo base64_encode($out_template_bedroom);?>');
  var template_livingroom=$.base64.decode('<?php echo base64_encode($out_template_livingroom);?>');
  var template_bedroom_bed=$.base64.decode('<?php echo base64_encode($out_template_bedroom_bed);?>');
  var template_livingroom_bed=$.base64.decode('<?php echo base64_encode($out_template_livingroom_bed);?>');

  
  var bed_type_fix=[];
<?php
  foreach ($bed_types as $val) {
    echo "  bed_type_fix[".$val['id']."] = ".$val['bed_type_fix_visitors'].";\n";
  } 
?>

  
    
  room_type_bedrooms_func = function() {
    room_type_bedrooms_old= room_type_bedrooms;
    room_type_bedrooms=parseInt($('#room_type_bedrooms').val());
    if (room_type_bedrooms_old == room_type_bedrooms) return;
    if (room_type_bedrooms_old < room_type_bedrooms) {
      for (myindex=(room_type_bedrooms_old + 1) ;myindex<=room_type_bedrooms; myindex++) {
        bedlist='';
        bed_index=0; 
        out=template_bedroom;
        out=out.replaceAll('[[subroomindex]]', myindex);
        out=out.replaceAll('[[subroom_name_value]]', '');
        out=out.replaceAll('[[subroom_name_value_en_US]]', '');
        out=out.replaceAll('[[subroom_bath_checked]]', '');
        out=out.replaceAll('[[subroom_visitors_value]]', '0');
        $('#subroom_' + (myindex-1)).after(out); 
        $('#subroom_' + (myindex) + ' .bed_add_class').click(bed_add_class_func);
        $('#subroom_' + (myindex) + ' .bed_del_class').click(bed_del_class_func);
       }
      
    } else {
      for (myindex=room_type_bedrooms_old ;myindex > room_type_bedrooms; myindex--) {
        $('#subroom_' + myindex).remove(); 
      }
    }
  }
  $('#room_type_bedrooms').change(room_type_bedrooms_func);
  
    
  room_type_living_rooms_func = function() { 
    need_save=true; 
    room_type_living_rooms_old= room_type_living_rooms;
    room_type_living_rooms=parseInt($('#room_type_living_rooms').val());
    if (room_type_living_rooms_old == room_type_living_rooms) return;
    if (room_type_living_rooms_old < room_type_living_rooms) {
      for (myindex=(room_type_living_rooms_old + 1) ;myindex<=room_type_living_rooms; myindex++) {
        bedlist='';
        bed_index=0; 
        
        
        out=template_livingroom;
        out=out.replaceAll('[[subroomindex]]', myindex);
        //out=str_replace('[[bedlist]]', $bedlist, $out);
        out=out.replaceAll('[[subroom_name_value]]', '');
        out=out.replaceAll('[[subroom_name_value_en_US]]', '');
        out=out.replaceAll('[[subroom_bath_checked]]', '');
        out=out.replaceAll('[[subroom_visitors_value]]', '0');
        if (myindex==1) {
           $('#room_type_living_rooms_list_inner').html(out);
           $('#room_type_living_rooms_list').show();
        } else {
          $('#lrsubroom_' + (myindex-1)).after(out); 
        } 
          
        $('#lrsubroom_' + (myindex) + ' .bed_add_class').click(bed_add_class_func);
        $('#lrsubroom_' + (myindex) + ' .bed_del_class').click(bed_del_class_func);
        $('#lrsubroom_' + (myindex) + ' .bed_type_select').click(bed_num_select_func);
        $('#lrsubroom_' + (myindex) + ' .bed_num_select').click(bed_num_select_func);

          
        
      }
    } else {
      for (myindex=room_type_living_rooms_old ;myindex > room_type_living_rooms; myindex--) {
        $('#lrsubroom_' + myindex).remove(); 
      }
      if (room_type_living_rooms == 0) $('#room_type_living_rooms_list').hide();
    }
  }
  $('#room_type_living_rooms').change(room_type_living_rooms_func);
  
  $('#room_type_bathrooms').change(function () {
    room_type_bathrooms=$('#room_type_bathrooms').val();
  });
  
  bed_add_class_func = function(event){		  
    need_save=true;
    myid = $(event.currentTarget).attr('id');
    if (myid.startsWith('bed_add')) {
      substart='subroom';
      out=template_bedroom_bed;
    } else {
      substart='lrsubroom';
      out=template_livingroom_bed;
    }
    myvars= myid.split('_');
    myindex=myvars[2];
    
    var bedindex=0;
    $('#' + substart + '_' + myindex + ' .bed_list .bed_row').each(function () {
      vmid = $(this).attr('id');
      vmid=vmid.split('_');
      vmid = parseInt(vmid[2]);
      if (vmid> bedindex) bedindex = vmid;
    });
    bedindex++;
    out=out.replaceAll('[[subroomindex]]', myindex);
    out=out.replaceAll('[[bedindex]]', bedindex);

    
    if (bedindex == 1) {
      $('#' + substart + '_' + myindex + ' .bed_list').html(out);
    } else {
      $('#' + substart + '_' + myindex + ' .bed_list #bed_' + myindex + '_' + (bedindex -1)).after(out); 
    }

    $('#' + substart + '_' + myindex + ' .bed_list #bed_' + myindex + '_' + (bedindex) + ' .bed_del_class').click(bed_del_class_func);
    $('#' + substart + '_' + myindex + ' .bed_list #bed_' + myindex + '_' + (bedindex) + ' .bed_type_select').click(bed_num_select_func);
    $('#' + substart + '_' + myindex + ' .bed_list #bed_' + myindex + '_' + (bedindex) + ' .bed_num_select').click(bed_num_select_func);
    
    
  }
  $('.bed_add_class').click(bed_add_class_func);
  
  bed_del_class_func = function(event){		
    need_save=true;
    myid = $(event.currentTarget).attr('id');
    $(event.currentTarget).parent().parent().remove();
    $('.tooltip').hide();
  }
  $('.bed_del_class').click(bed_del_class_func);
  
  bed_num_select_func = function(event){		
    need_save=true;
    var myid = $(event.currentTarget).parent().parent().parent().attr('id');
    //console.log(myid);
    
    var bed_num_visitors = 0;
    $('#' + myid + ' .bed_row').each(function() {
      bedidd=$(this).attr('id');
      //console.log(bedidd);
      bedid=bedidd.replace('bed_','');
      //console.log(bedid);
      mytype= parseInt($('#' + myid + ' #bed_type_' + bedid).val());
      num = parseInt($('#' + myid + ' #bed_num_' + bedid).val());
      if (mytype>0) bed_num_visitors+=num * bed_type_fix[mytype];
    }); 
    
    //console.log(bed_num_visitors);
    myid = $('#' + myid).parent().parent().attr('id');
    //console.log(myid);
    $('#' + myid + ' .bed_num_visitors').val(bed_num_visitors);
  }
  $('.bed_num_select').change(bed_num_select_func);
  $('.bed_type_select').change(bed_num_select_func);
  
  var room_type_fix_is_multi=[];
<?php
  foreach ($room_type_fix_is_multi_array as $val) {
    if ($val['room_type_fix_is_multi']!=0) {
      echo "  room_type_fix_is_multi.push(".$val['id_hotel_room_type_fix'].");\n";
    }
  } 
?>
  
  $('#hotel_room_type_fix_id').change(function() {
    need_save=true;
    myid = parseInt($(event.currentTarget).val());
    //console.log(myid);
    myy = room_type_fix_is_multi.indexOf(myid);
    if (myy == -1) {
      $('#room_type_bedrooms').val(1);
      $('#room_type_living_rooms').val(0);
      $('#room_type_bathrooms').val(0);
      room_type_bedrooms_func();
      room_type_living_rooms_func();
      $('#room_type_bedrooms_div').hide();
      $('#room_type_living_rooms_div').hide();
      $('#room_type_bathrooms_div').hide();
      
    } else {
      room_type_bedrooms_func();
      room_type_living_rooms_func();
      $('#room_type_bedrooms_div').show();
      $('#room_type_living_rooms_div').show();
      $('#room_type_bathrooms_div').show();
      
    }
  });
  

  var elems_my_amenity = Array.prototype.slice.call(document.querySelectorAll('.my_amenity'));
  elems_my_amenity.forEach(function(html) {
    var switchery1 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });  

  $('#room_type_visitors').change(function() {
    room_type_visitors_change();
  });
  $('#room_type_visitors_childs').change(function() {
    room_type_visitors_change();
  });
  $('#room_type_visitors_max').change(function() {
    room_type_visitors_change();
  });
  function room_type_visitors_change() {
    val_vi=parseInt($('#room_type_visitors').val());
    val_vc=parseInt($('#room_type_visitors_childs').val());
    val_mv=parseInt($('#room_type_visitors_max').val());
    if (isNaN(val_vi)) val_vi=0;
    if (isNaN(val_vc)) val_vc=0;
    if (isNaN(val_mv)) val_mv=0;
    
    if (val_vc<=0) {
      $('#room_type_visitors_max').prop('disabled', true);
    } else {
      $('#room_type_visitors_max').prop('disabled', false);
    }
    var val_mv_min=val_vi;
    var val_mv_max=val_vi + val_vc;
    //console.log('room_type_visitors');
    //console.log(val_vi);
    //console.log(val_vc);
    //console.log(val_mv);
    //console.log('val_mv_min:' + val_mv_min);
    //console.log('val_mv_max:' + val_mv_max);
    
    $("#room_type_visitors_max > option").each(function() {
      tmp_val=parseInt($(this).val());
      if (tmp_val >=val_vi && tmp_val <= val_mv_max) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });       
    if (val_mv<val_mv_min) $('#room_type_visitors_max').val(val_mv_min);
    else if (val_mv>val_mv_max) $('#room_type_visitors_max').val(val_mv_max);
    
    prev_val=parseInt($('#room_type_child_kounies').val());
    $('#room_type_child_kounies > option').each(function() {
      tmp_val=parseInt($(this).val());
      if (tmp_val>0) {
        if (tmp_val<=val_vc) {
          $(this).show();
        } else {
          $(this).hide();
        }
      }
    });
    if ($('#room_type_child_kounies option[value=' + prev_val + ']').css('display')=='none') {
      if (val_vc<=9) 
        $('#room_type_child_kounies').val(val_vc);
       else
        $('#room_type_child_kounies').val('0');
    }
    
    
    
  }
  room_type_visitors_change();


  $('#product_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode:'simple',
        and_variable:1,
      };
      $.ajax({
        url: 'admin-autocomplete-product.php',
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
    minLength: 3,
    delay: 300, //default
    autoFocus: true,
    select: function( event, ui ) {
      $("#product_id").attr('data-id',ui.item.id);
      $('#autocomplete_product_id').attr('href', 'admin-products-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_product_id').show();
      //$('#bom_monada_id').val(ui.item.monada_id);

      need_save=true;
      id_product=0;
      id_product=parseInt($('#product_id').attr('data-id'));
      if (isNaN(id_product)) id_product=0;
      if (id_product<=0) return;
      datasend='cmd=get&id=' + id_product + '&aa=1&sheets=0&quantity=1';
              
      $.ajax({
  			url: 'admin-get-product-data.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},				
  			success: function(data) {
  			  need_save=true;
  				if (!data) {
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {
  					if (data.success == true) {
  					  if (data.product_photo=='') {
  					    $('#div_product_photo').hide();
  					  } else {
  					    $('#div_product_photo').show();
  					    $('#img_product_photo').attr('src', data.product_photo).parent().attr('href',data.photo_url);
  					    mylgbase_restart();
  					  }
  					} else {
  						myalert('error:' + $.base64.decode(data.message));
  					  $('#product_id').attr('data-id','0');
  					}
  				}
  			}
  		}); 
  		
  		          
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#product_id").val('').attr('data-id','0');
        $('#autocomplete_product_id').hide(); 
        $('#img_product_photo').attr('src', '/my/img/product.png').parent().attr('href','#');
        $('#div_product_photo').hide();
      }
    },
    create: function () {
//      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
//        return $('<li>')
//          //.append('<a>' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + item.descr + '</span>')
//          .append('<a>' + item.descr + '</a>')
//          .appendTo(ul);
//      };
    }    
  });
  
  var mylgbase = $("#div_photo");
  function mylgbase_restart() {
    if (!(mylgbase.data('lightGallery') === undefined)) {
      mylgbase.data('lightGallery').destroy(true);
    }
    mylgbase.lightGallery({selector: '.class_a_product_photo',thumbnail:true,hideBarsDelay:1000,});
  }
  mylgbase_restart();  




  var file_cc=0;
    
  jqXHR = $('#myphoto_upload').fileupload({
      dropZone:$('#f_button_add_files_photo'),
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {
        
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          if (from_php_id<=0) {
             uploadErrors.push(gks_lang('Αποθηκεύστε πρώτα τον τύπο δωματίου'));
          }
          
          var acceptFileTypes = gks_image_extension; //['.gif','.jpg','.jpeg','.png','.webp'];
          if(acceptFileTypes.indexOf(ext)<0) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Μη αποδεκτός τύπος αρχείου')+': ' + ext);
          }
          if(data.originalFiles[0]['size'] > from_php_gks_get_max_upload_file_size) {
              uploadErrors.push(gks_lang('Αρχείο')+': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Πολύ μεγάλο μέγεθος αρχείου')+': ' + data.originalFiles[0]['size']);
          }
          
          if(uploadErrors.length > 0) {
              myalert('error:' + uploadErrors.join("<br>"));
          } else {
        
            file_cc++;
            data.mycc=file_cc;

            data.submit();
            $('#progress-bar_photo').show();
            $('#progress-extended_photo').show();
          }
      },
      done: function (e, data) {
          
          $.each(data.result.files, function (index, file) {
            if (typeof file.error == 'undefined') {
              
              
              myhtmlimg='';
              myhtmlimg+='<div id="item_upload_photo_' + file.insert_id + '" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">';
              myhtmlimg+='  <a class="lightgalleryitem_user" href="' + file.url + '" data-download-url="' + file.url + '">';
              myhtmlimg+='    <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="' + file.url_thumb + '">';
              myhtmlimg+='  </a>';
              myhtmlimg+='  <br>';
              myhtmlimg+='  <div style="padding-top:4px">';
              myhtmlimg+='      <a href="" class="set_profile_photo"   data-url="' + file.url_thumb + '" title="' + gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία') + '"><img src="/my/img/icons/photo.png" border="0" width="16"></a>';
              myhtmlimg+='      <a href="" class="delete_upload_photo" data-url="' + file.url_thumb + '" data-id="' + file.insert_id + '" title="' + gks_lang('Διαγραφή') + '"><img src="/my/img/0.png" border="0" width="16"></a>';
              myhtmlimg+='  </div>';
              myhtmlimg+='</div>';


              $('#imagelist_photo').append(myhtmlimg);
              $('#item_upload_photo_' + file.insert_id + ' .delete_upload_photo').click(delete_upload_click_photo);
              $('#item_upload_photo_' + file.insert_id + ' .set_profile_photo').click(set_profile_photo);
              
             
            
              $("#lightgallery_user").data('lightGallery').destroy(true);
              $("#lightgallery_user").lightGallery({
              	selector: '.lightgalleryitem_user',
              	thumbnail:true,
              	hideBarsDelay:1000,
              }); 
              
              if ($('#form_room_type_photo').val() == '') {
                $('#form_room_type_photo').val(file.url_thumb);
                $('#form_room_type_photo_img').attr("src",file.url_thumb);  
                $('#reset_profile_photo').show(); 
                need_save=true;         
              }
            }
          });
      },
      progressall: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress-bar_photo .bar_photo').css(
            'width',
            progress + '%'
        );
        $('#progress-extended_photo').html(_renderExtendedProgress(data));
      },
      fail: function (e, data) {
        myalert('error:'+gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε')+'<br>' + data.jqXHR.responseText);
      },
      progress: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progressfile_photo' + data.mycc + ' .bar_photo').css(
            'width',
            progress + '%'
        );
      },
      stop: function (e) {
        $('#progress-bar_photo').hide();
        $('#progress-extended_photo').hide();
      },
      
  });
      
	delete_upload_click_photo = function(event){	
    var uid=$(event.target.parentNode).attr('data-id');
    var data_url=$(event.target.parentNode).attr('data-url');
    
    
    $.ajax({
			url: '/my/admin-hotel-room-type-item-photo-delete.php?id=' + uid,
			myuid: uid,
			type: 'POST',
			cache: false,
			dataType: 'json',
			mydata_url:data_url,
			data: '',
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
  					$('#item_upload_photo_' + this.myuid).remove();
  					$('#myfileid_photo_' + this.myuid).remove();
  					
  					if (this.mydata_url == $('#form_room_type_photo').val()) {
    					need_save=true;
    					if ($(".set_profile_photo").length == 0) {
    					  
                $('#form_room_type_photo').val('');
                $('#form_room_type_photo_img').attr("src",'/my/img/product.png');
                $('#reset_profile_photo').hide();
              } else {
                
                $(".set_profile_photo").each(function( index ) {
                  var data_url=$(this).attr('data-url');
                  $('#form_room_type_photo').val(data_url);
                  $('#form_room_type_photo_img').attr("src",data_url);
                  $('#reset_profile_photo').show();
                  return;
                });  					
      				}
            }
            
            $("#lightgallery_user").data('lightGallery').destroy(true);
            $("#lightgallery_user").lightGallery({
            	selector: '.lightgalleryitem_user',
            	thumbnail:true,
            	hideBarsDelay:1000,
            }); 
					  
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  }

  $('.delete_upload_photo').click(delete_upload_click_photo);

	set_profile_photo = function(event){	
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα τον τύπο δωματίου')); return;}	  
    need_save=true;      
    var data_url=$(event.target.parentNode).attr('data-url');
    $('#form_room_type_photo').val(data_url);
    $('#form_room_type_photo_img').attr("src",data_url);
    $('#reset_profile_photo').show();
    return false;
  }

  $('.set_profile_photo').click(set_profile_photo);

  $('#reset_profile_photo').click(function() {
    if (from_php_id<=0) {myalert('error:'+gks_lang('Αποθηκεύστε πρώτα το είδος')); return;}	  
    need_save=true;
    $('#form_room_type_photo').val('');
    $('#form_room_type_photo_img').attr("src",'/my/img/product.png');   
    $('#reset_profile_photo').hide(); 
    return false;
  });
  
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });   
  
  
  function mystoppropagation() {
    event.stopPropagation();
  }
  $('.mystoppropagation').click(mystoppropagation);
  
  
  
  
  //generic
  gks_page_loading=false;
  
  if (from_php_scrollto!='') {
    if ($('#' + from_php_scrollto).length>0) {
      $([document.documentElement, document.body]).animate({
          scrollTop: $('#' + from_php_scrollto).offset().top
      }, 500);
    }
    if (window.location.href.endsWith('&scrollto=' + from_php_scrollto)) {
      newurl=window.location.href;
      newurl=newurl.substring(0,newurl.length-('&scrollto=' + from_php_scrollto).length);
      
      window.history.pushState({}, window.document.title, newurl);
    }
  } else if (from_php_temp_mypropertiesheight!=0) {
    $("html").scrollTop(from_php_temp_mypropertiesheight);
  }



  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 

  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;  
    
});
</script>



 
<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


