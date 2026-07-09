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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_ergasies',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$gks_custom_prepare = gks_custom_table_item_prepare('gks_production_ergasies',['from'=>'item']);



if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

if ($id==-1) {
  $my_page_title=gks_lang('Νέα Εργασία');
  $row=array();
  
  $row['user_id_add']=0;
  $row['gks_nickname_add']='';
  $row['mydate_add']=null;
  $row['user_id_edit']=0;
  $row['gks_nickname_edit']='';
  $row['mydate_edit']=null;
  $row['myip']='';

  $row['id_production_ergasia']=-1;
  $row['production_ergasia_descr']='';
  $row['production_ergasia_sortorder']=1000;
  
} else {
    
  $sql ="SELECT gks_production_ergasies.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_production_ergasies
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_production_ergasies.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_production_ergasies.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_production_ergasia = ".$id;
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

  $my_page_title=gks_lang('Εργασία').': '.$row['production_ergasia_descr'];
  $object_title=$row['production_ergasia_descr'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

stat_record();
$nav_active_array=array('production','production_ergasies');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Εργασία');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Εργασία');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="production_ergasia_descr"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm" id="production_ergasia_descr"  value="<?php echo htmlspecialchars_gks($row['production_ergasia_descr']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="production_ergasia_sortorder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-md-8">
              <input id="production_ergasia_sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['production_ergasia_sortorder'];?>" min="0" step="1">
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


<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_production_ergasia'];?>" data-model="gks_production_ergasies" data-backurl="admin-production-ergasies.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

              
              
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προαπαιτούμενες Εργασίες');?>
          <small class="form-text text-muted"><?php echo gks_lang('Για να προχωρήσει η παραπάνω εργασία, θα πρέπει πρώτα να ολοκληρωθουν όλες οι παρακάτω εργασίες εφόσον υπάρχουν στην διαδικασία');?></small>
        </div>
        
        <div class="card-body" <?php echo gks_card_body('proer');?>>        
          <?php
          $query = "SELECT gks_production_ergasies_mustdone.*, gks_production_ergasies.production_ergasia_descr
          FROM gks_production_ergasies_mustdone LEFT JOIN gks_production_ergasies ON gks_production_ergasies_mustdone.ergasia_mustdone_id = gks_production_ergasies.id_production_ergasia
          WHERE gks_production_ergasies_mustdone.ergasia_id=".$id."
          ORDER BY gks_production_ergasies.production_ergasia_sortorder, gks_production_ergasies.production_ergasia_descr;";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Εργασία');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_production_ergasia_mustdone'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-id="<?php echo $row_list['id_production_ergasia_mustdone'];?>" data-model="gks_production_ergasies_mustdone">            
              </td>
              <td nowrap align="center"><a href="admin-production-ergasies-item.php?id=<?php echo $row_list['ergasia_mustdone_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td nowrap><?php echo $row_list['production_ergasia_descr'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="ergasia_mustdone"    id="ergasia_mustdone"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="ergasia_mustdone_id" id="ergasia_mustdone_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_ergasia_mustdone"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>      

        </div>
      </div>               
              
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εξαρτώμενες Εργασίες');?>
          <small class="form-text text-muted"><?php echo gks_lang('Για να προχωρήσουν οι παρακάτω εργασίες θα πρέπει πρώτα να ολοκληρωθεί η παραπάνω εργασία εφόσον υπάρχει στην διαδικασία');?></small>
        </div>
        
        <div class="card-body" <?php echo gks_card_body('ejerg');?>>        
          <?php
          $query = "SELECT gks_production_ergasies_mustdone.*, gks_production_ergasies.production_ergasia_descr
          FROM gks_production_ergasies_mustdone LEFT JOIN gks_production_ergasies ON gks_production_ergasies_mustdone.ergasia_id = gks_production_ergasies.id_production_ergasia
          WHERE gks_production_ergasies_mustdone.ergasia_mustdone_id=".$id."
          ORDER BY gks_production_ergasies.production_ergasia_sortorder, gks_production_ergasies.production_ergasia_descr;";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Εργασία');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_production_ergasia_mustdone'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-id="<?php echo $row_list['id_production_ergasia_mustdone'];?>" data-model="gks_production_ergasies_mustdone">            
              </td>
              <td nowrap align="center"><a href="admin-production-ergasies-item.php?id=<?php echo $row_list['ergasia_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td nowrap><?php echo $row_list['production_ergasia_descr'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="ergasia_n"    id="ergasia_n"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="ergasia_n_id" id="ergasia_n_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_ergasia_n"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>      

        </div>
      </div>                
              
      <?php 
      echo getObjectRels('gks_production_ergasies',$id);   
      echo getActivityObjectTable('gks_production_ergasies',$id); 
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_production_ergasies','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
      


    </div>

    <div class="col-md-6">

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πόστα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('posta');?>>        

          <?php
          $query = "SELECT gks_production_posta_ergasies.*, gks_production_posta.production_posto_descr
          FROM gks_production_posta_ergasies 
          LEFT JOIN gks_production_posta ON gks_production_posta_ergasies.production_posto_id = gks_production_posta.id_production_posto
          WHERE gks_production_posta_ergasies.production_ergasia_id=".$id."
          ORDER BY gks_production_posta.production_posto_sortorder, gks_production_posta.production_posto_descr";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Πόστο');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_production_posta_ergasies'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-id="<?php echo $row_list['id_production_posta_ergasies'];?>" data-model="gks_production_posta_ergasies">            
              </td>
              <td nowrap align="center"><a href="admin-production-posta-item.php?id=<?php echo $row_list['production_posto_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              
              <td nowrap><?php echo $row_list['production_posto_descr'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="posto"    id="posto"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="posto_id" id="posto_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_posto"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>      

        </div>
      </div>        
        
        

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κατηγορίες Ειδών');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('catei');?>>        

          <?php
          $query = "SELECT
gks_production_ergasies_eidoscat.*,

gks_eshop_products_categories.*, ccproducts.ccc,
ug2.product_category_descr AS gt2, 
ug3.product_category_descr AS gt3, 
ug4.product_category_descr AS gt4, 
ug5.product_category_descr AS gt5,
ug6.product_category_descr AS gt6,
ug7.product_category_descr AS gt7,
ug8.product_category_descr AS gt8,
ug9.product_category_descr AS gt9,
ug10.product_category_descr AS gt10,

ug2.id_product_category AS id2, 
ug3.id_product_category AS id3, 
ug4.id_product_category AS id4, 
ug5.id_product_category AS id5,
ug6.id_product_category AS id6,
ug7.id_product_category AS id7,
ug8.id_product_category AS id8,
ug9.id_product_category AS id9,
ug10.id_product_category AS id10,
CONCAT_WS('\\\\',
        ug10.product_category_descr,
        ug9.product_category_descr,
        ug8.product_category_descr,
        ug7.product_category_descr,
        ug6.product_category_descr,
        ug5.product_category_descr,
        ug4.product_category_descr,
        ug3.product_category_descr,
        ug2.product_category_descr,
        gks_eshop_products_categories.product_category_descr) as fullpath,
CONCAT_WS('\\\\',
        ug10.product_category_descr,
        ug9.product_category_descr,
        ug8.product_category_descr,
        ug7.product_category_descr,
        ug6.product_category_descr,
        ug5.product_category_descr,
        ug4.product_category_descr,
        ug3.product_category_descr,
        ug2.product_category_descr) as dirpath
FROM ((((((((((gks_production_ergasies_eidoscat
LEFT JOIN gks_eshop_products_categories ON gks_production_ergasies_eidoscat.cateidos_id = gks_eshop_products_categories.id_product_category)
LEFT JOIN (
SELECT product_category_id, Count(product_id) AS ccc
FROM gks_eshop_products_categories_products
GROUP BY product_category_id
) AS ccproducts ON gks_eshop_products_categories.id_product_category = ccproducts.product_category_id)
LEFT JOIN gks_eshop_products_categories AS ug2  ON gks_eshop_products_categories.product_category_parent_id = ug2.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.product_category_parent_id = ug3.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.product_category_parent_id = ug4.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.product_category_parent_id = ug5.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.product_category_parent_id = ug6.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.product_category_parent_id = ug7.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.product_category_parent_id = ug8.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.product_category_parent_id = ug9.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
WHERE gks_production_ergasies_eidoscat.production_ergasia_id=".$id."
          ORDER BY fullpath;";
          
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Πόστο');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_production_ergasies_eidoscat'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-id="<?php echo $row_list['id_production_ergasies_eidoscat'];?>" data-model="gks_production_ergasies_eidoscat">            
              </td>
              <td nowrap align="center"><a href="admin-product-categories-item.php?id=<?php echo $row_list['cateidos_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              
              <td nowrap><?php echo $row_list['fullpath'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="cateidos"    id="cateidos"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="cateidos_id" id="cateidos_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_cateidos"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>      

        </div>
      </div>    


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('eidi2');?>>        

          <?php
          $query = "SELECT gks_production_ergasies_eidos.*, gks_eshop_products.id_product, gks_eshop_products.product_descr,
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
          FROM (gks_production_ergasies_eidos 
          LEFT JOIN gks_eshop_products ON gks_production_ergasies_eidos.eidos_id = gks_eshop_products.id_product)
          LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
          WHERE gks_production_ergasies_eidos.production_ergasia_id=".$id."
          ORDER BY gks_eshop_products.product_descr;";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Είδος');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_production_ergasies_eidos'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-id="<?php echo $row_list['id_production_ergasies_eidos'];?>" data-model="gks_production_ergasies_eidos">            
              </td>
              <td nowrap align="center"><a href="admin-products-item.php?id=<?php echo $row_list['eidos_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              
              <td nowrap><?php echo $row_list['product_descr_p'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="product"    id="product"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="product_id" id="product_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_product"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>      

        </div>
      </div>    



      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>       

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><input id="id_nomos" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php if ($row['id_production_ergasia']>0) echo $row['id_production_ergasia'];?>"></div>
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>';?></span></div>
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


var from_php_dialog_object_rel_curr='gks_production_ergasies';
var from_php_activity_model='gks_production_ergasies';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_ergasies','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_ergasies','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_ergasies','delete',$id);?>;



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
  
  


 
    
  function mysubmit() {
    
    datasend='';


    datasend+='&production_ergasia_descr='  + encodeURI($("#mypostform #production_ergasia_descr").val().trim());
    datasend+='&production_ergasia_sortorder='  + $("#mypostform #production_ergasia_sortorder").val().trim();


    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-ergasies-item-exec.php?id=' + <?php echo $id;?>,
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
  


  $('#posto').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-posto.php',
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
    select: function( event, ui ) {
      $("#posto_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#posto").val("");
          $("#posto_id").val("");
        }
    }
  });  
  
  $('#add_posto').click(function(event) {  
    
    datasend='';
    datasend+='ergasia_id= <?php echo $id;?>';    
    datasend+='&posto_id='  + encodeURI($("#posto_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-ergasies-item-posto_add.php',
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
  });    


  $('#product').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode:'simple',
        and_variable:1,
      };
      $.ajax({
        url: 'admin-autocomplete-product.php', //and_variable=1
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
    select: function( event, ui ) {
      $("#product_id").val(ui.item.id);
    },
//    change: function (event, ui) {
//        if(!ui.item){
//          $("#product").val("");
//          $("#product_id").val("");
//        }
//    },
//    create: function () {
//      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
//        return $('<li>')
//          .append('<a class="gks_autocomplete_id">' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + item.descr + '</span>')
//          .appendTo(ul);
//      };
//    },    
//    open: function(event, ui) {
//      var mymaxui_id=0;
//      $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
//        temp=$(this).outerWidth();
//        if (temp>mymaxui_id) mymaxui_id=temp;
//      });
//      var mymaxui_text=0;
//      $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text').each(function() {
//        temp=$(this).outerWidth();
//        if (temp>mymaxui_text) mymaxui_text=temp;
//      });
//      mymaxui_id+=4;
//      $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
//        $(this).css({'min-width':mymaxui_id + 'px','display' : 'inline-block'});
//      }); 
//      mymaxui_text+=mymaxui_id + 4;
//      $(this).data('ui-autocomplete').menu.element.css('width',mymaxui_text+'px');
//    },
//    
  });
  
  $('#add_product').click(function(event) {  
    
    datasend='';
    datasend+='ergasia_id= <?php echo $id;?>';    
    datasend+='&eidos_id='  + encodeURI($("#product_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-products-item-ergasia.php',
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
  });  
    

  $('#cateidos').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-cateidos.php',
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
    select: function( event, ui ) {
      $("#cateidos_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#cateidos").val("");
          $("#cateidos_id").val("");
        }
    }
  });

  $('#add_cateidos').click(function(event) {  
    
    datasend='';
    datasend+='ergasia_id= <?php echo $id;?>';    
    datasend+='&cat_id='  + encodeURI($("#cateidos_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-categories-item-ergasia.php',
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
  });  
        
  $('#ergasia_mustdone').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml: 1,
        notin: <?php echo $id;?>,
      };
      $.ajax({
        url: 'admin-autocomplete-ergasies.php',
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
    select: function( event, ui ) {
      $("#ergasia_mustdone_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#ergasia_mustdone").val("");
          $("#ergasia_mustdone_id").val("");
        }
    }
  });    

  $('#add_ergasia_mustdone').click(function(event) {  
    
    datasend='';
    datasend+='ergasia_id= <?php echo $id;?>';    
    datasend+='&ergasia_mustdone_id='  + encodeURI($("#ergasia_mustdone_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-ergasies-item-mustdone_add.php',
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
  });

  $('#ergasia_n').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml: 1,
        notin: <?php echo $id;?>,
      };
      $.ajax({
        url: 'admin-autocomplete-ergasies.php',
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
    select: function( event, ui ) {
      $("#ergasia_n_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#ergasia_n").val("");
          $("#ergasia_n_id").val("");
        }
    }
  });   

  $('#add_ergasia_n').click(function(event) {  
    
    datasend='';
    datasend+='ergasia_mustdone_id= <?php echo $id;?>';    
    datasend+='&ergasia_id='  + encodeURI($("#ergasia_n_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-ergasies-item-mustdone_add.php',
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
  });
        
});
</script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


