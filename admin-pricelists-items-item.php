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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_pricelist_items',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_pricelist_items',['from'=>'item']);


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
  $row['id_pricelist_item']=-1;
  $row['pricelist_id']=0; if (isset($_GET['pricelist_id'])) $row['pricelist_id']=intval($_GET['pricelist_id']);
  $row['pricelist_item_descr']='';
  $row['pricelist_item_sequence']=0;
  $row['pricelist_item_disable']=0;
  $row['pricelist_item_coupon']='';
  //$row['pricelist_item_date_from']='';
  //$row['pricelist_item_date_to']='';
  $row['pricelist_item_event_category_id']=0;
  
  $row['pricelist_item_min_posotita']=0;
  $row['pricelist_item_price_epi']=0;
  $row['pricelist_item_price_plus']=0;
  $row['pricelist_item_price_eval']='';
  
  $row['pricelist_item_min_price']=0;
  $row['pricelist_item_max_price']=0;
  $row['pricelist_item_individual_use']=0;
  $row['pricelist_item_exclude_sale_items']=0;
  $row['pricelist_item_users_emails']='';
  $row['pricelist_item_usage_limit']=0;
  $row['pricelist_item_limit_usage_to_x_items']=0;
  $row['pricelist_item_usage_limit_per_user']=0;
  
  $row['product_descr_p']='';
  $row['product_photo_p']='';
  $row['category_fullpath']='';
  
  $my_page_title=gks_lang('Νέο Στοιχείο Τιμοκαταλόγου-Κουπόνι');  
  
} else {


  
  
  $sql ="SELECT gks_eshop_pricelist_items.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, 
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM (gks_eshop_pricelist_items
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_eshop_pricelist_items.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_eshop_pricelist_items.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_pricelist_item = ".$id;  
  
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Στοιχείο Τιμοκαταλόγου-Κουπόνι').': '.$row['pricelist_item_descr'];
  $object_title=$row['pricelist_item_descr'];
    
  
}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();
$nav_active_array=array('manage','manage_pricelist_items');



include_once('_my_header_admin.php');
?>
<style>
.myaddpcb {
  display:flex;
  flex-direction: row;
  gap:10px;
}  
.myaddpcb input {
  width:50%;
  display: inline-block;
  flex: 1 1 auto;
}
.myaddpcb select {
  width:25%;
  flex: 1 1 auto;  
}
.myaddpcb button {
  justify-content: center!important;
  vertical-align: baseline;
  width:25%;
  flex: 1 1 auto;
}
.is_include_val_1 {
  background-color: #00ff0066;
}

.is_include_val_-1 {
  background-color: #ff000066;
}
  
</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Στοιχείο Τιμοκαταλόγου-Κουπόνι');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Στοιχείο Τιμοκαταλόγου-Κουπόνι');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_descr"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="pricelist_item_descr"  value="<?php echo htmlspecialchars_gks($row['pricelist_item_descr']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="pricelist_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμοκατάλογος');?>:</label>
            <div class="col-md-8">
              <select id="pricelist_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_pricelist=gks_lang_data_obj_prepare('gks_eshop_pricelist','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_pricelist, array('pricelist_descr'));
                $sql="select id_pricelist,".gks_lang_sql_field('pricelist_descr',$lang_prepare_gks_eshop_pricelist)." 
                FROM ".$lang_prepare_gks_eshop_pricelist['sql']['from1']." gks_eshop_pricelist 
                ".$lang_prepare_gks_eshop_pricelist['sql']['from2']."
                where pricelist_disable=0 
                order by sortorder,pricelist_descr";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_pricelist'].'" ';
                  if ($row_select['id_pricelist']==$row['pricelist_id']) echo ' selected ';
                  echo '>'.$row_select['pricelist_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div> 

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_sequence"><?php echo gks_lang('Σειρά');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="pricelist_item_sequence"  value="<?php echo htmlspecialchars_gks($row['pricelist_item_sequence']);?>" min="0" style="max-width:200px">
              <small class="form-text text-muted"><?php echo gks_lang('Η σειρά εφαρμογής όταν πολλά στοιχείο - κουπόνια πιθανόν να ισχύουν');?></small>
            </div>
          </div>
          
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_price_epi"><?php echo gks_lang('Επί (a)');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="pricelist_item_price_epi"  value="<?php echo htmlspecialchars_gks($row['pricelist_item_price_epi']);?>" min="0" step="0.01" style="max-width:200px">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_price_plus"><?php echo gks_lang('Συν (β)');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="pricelist_item_price_plus"  value="<?php echo htmlspecialchars_gks($row['pricelist_item_price_plus']);?>" min="0" step="0.01" style="max-width:200px">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_price_plus"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-sm-8" style="font-size: 0.875rem;padding: 0.25rem 1rem;">
              f(x) = <?php echo gks_lang('Τιμή');?> * (1 + a) + b <br>
              f(x) = <?php echo gks_lang('Τιμή');?> * (1 + <span id="price_epi"></span>) + <span id="price_plus"></span> <br>
              f(100) = <span id="example"></span>              
            </div>
          </div>          
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_price_eval"><?php echo gks_lang('Έκφραση');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="pricelist_item_price_eval"  value="<?php echo htmlspecialchars_gks($row['pricelist_item_price_eval']);?>" placeholder="=2*[[posotita]]/12">
              <small class="form-text text-muted">
              	<?php echo gks_lang('Εάν ορισθεί η έκφραση τότε υπερισχύει αυτή.<br>Μπορείτε να χρησιμοποιήσετε τα παρακάτω:<br><b>[[posotita]]</b> = Ποσότητα<br><b>[[price]]</b> = Αρχική Τιμή');?><br></small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_min_posotita"><?php echo gks_lang('Ελάχιστη Ποσότητα');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="pricelist_item_min_posotita"  value="<?php echo $row['pricelist_item_min_posotita'];?>" min="0" style="max-width:200px">
            </div>
          </div>          
          <div class="form-group row">
            <label for="pricelist_item_date_from" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από Ημερομηνία');?>:</label>
            <div class="col-md-8">
              <input id="pricelist_item_date_from" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['pricelist_item_date_from'])) echo  showDate(strtotime($row['pricelist_item_date_from']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div>
          <div class="form-group row">
            <label for="pricelist_item_date_to" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Έως Ημερομηνία');?>:</label>
            <div class="col-md-8">
              <input id="pricelist_item_date_to" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['pricelist_item_date_to'])) echo  showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div>
          <div class="form-group row">
            <label for="pricelist_item_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-sm-8">
              <input type="checkbox" id="pricelist_item_disable" value="1" <?php if ($row['pricelist_item_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>           
        </div>
      </div>



      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κουπόνι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('coupon');?>> 

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_coupon"><?php echo gks_lang('Κουπόνι');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="pricelist_item_coupon"  value="<?php echo htmlspecialchars_gks($row['pricelist_item_coupon']);?>">
            </div>
          </div>
            

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_min_price"><?php echo gks_lang('Ελάχιστο Ποσό');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="pricelist_item_min_price"  value="<?php echo $row['pricelist_item_min_price'];?>" min="0" style="max-width:200px">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_max_price"><?php echo gks_lang('Μέγιστο Ποσό');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="pricelist_item_max_price"  value="<?php echo $row['pricelist_item_max_price'];?>" min="0" style="max-width:200px">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="pricelist_item_individual_use" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποκλειστικά μεμονωμένη χρήση');?>:</label>
            <div class="col-sm-8">
              <input type="checkbox" id="pricelist_item_individual_use" value="1" <?php if ($row['pricelist_item_individual_use']!=0) echo ' checked '; ?> class="switchery1_this">
              <small class="form-text text-muted"><?php echo gks_lang('Επιλέξτε αυτό το κουτάκι αν το κουπόνι δεν μπορεί να χρησιμοποιηθεί σε συνδυασμό με άλλα κουπόνια');?></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="pricelist_item_exclude_sale_items" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εξαίρεση των προϊόντων σε προσφορά');?>:</label>
            <div class="col-sm-8">
              <input type="checkbox" id="pricelist_item_exclude_sale_items" value="1" <?php if ($row['pricelist_item_exclude_sale_items']!=0) echo ' checked '; ?> class="switchery1_this">
              <small class="form-text text-muted"><?php echo gks_lang('Επιλέξτε αυτό το κουτάκι αν το κουπόνι δε θα πρέπει να χρησιμοποιηθεί σε προϊόντα σε προσφορά. Τα κουπόνια ανά προϊόν θα λειτουργήσουν μόνο αν το προϊόν δεν βρίσκεται σε προσφορά. Τα κουπόνια ανά καλάθι θα λειτουργήσουν μόνο αν δεν υπάρχει κανένα προϊόν σε προσφορά στο καλάθι');?></small>
              
            
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_users_emails"><?php echo gks_lang('Επιτρεπόμενες διευθύνσεις email');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="pricelist_item_users_emails"  value="<?php echo htmlspecialchars_gks($row['pricelist_item_users_emails']);?>">
              <small class="form-text text-muted"><?php echo gks_lang('Λίστα των επιτρεπομένων email χρέωσης για αντιστοίχιση όταν γίνει μια παραγγελία. Χωρίστε τις διευθύνσεις email με κόμμα. Μπορείτε επίσης να χρησιμοποιείσετε αστερίσκο (*) για να ταιριάξετε μέρη από ένα email. Για παράδειγμα το *@gmail.com αντιστοιχεί σε όλα τα email από το gmail');?></small>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_usage_limit"><?php echo gks_lang('Όριο χρήσης ανά κουπόνι');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="pricelist_item_usage_limit"  value="<?php echo $row['pricelist_item_usage_limit'];?>" min="0" style="max-width:200px">
              <small class="form-text text-muted"><?php echo gks_lang('Πόσες φορές μπορεί να χρησιμοποιηθεί αυτό το κουπόνι πριν ακυρωθεί');?></small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_limit_usage_to_x_items"><?php echo gks_lang('Περιορισμός χρήσης σε X προϊόντα');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="pricelist_item_limit_usage_to_x_items"  value="<?php echo $row['pricelist_item_limit_usage_to_x_items'];?>" min="0" style="max-width:200px">
              <small class="form-text text-muted"><?php echo gks_lang('Ο μέγιστος αριθμός των μεμονωμένων προϊόντων στα οποία μπορεί να εφαρμοστεί αυτό το κουπόνι όταν χρησιμοποιείτε τις εκπτώσεις προϊόντων. Αφήστε κενό για να εφαρμοστεί σε όλα τα προϊόντα που πληρούν τις προϋποθέσεις στο καλάθι');?></small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="pricelist_item_usage_limit_per_user"><?php echo gks_lang('Όριο χρήσης ανά χρήστη');?>:</label>
            <div class="col-sm-8">
              <input type="number" class="form-control form-control-sm myneedsave" id="pricelist_item_usage_limit_per_user"  value="<?php echo $row['pricelist_item_usage_limit_per_user'];?>" min="0" style="max-width:200px">
              <small class="form-text text-muted"><?php echo gks_lang('Πόσες φορές μπορεί να χρησιμοποιηθεί αυτό το κουπόνι από ένα μεμονωμένο χρήστη. Χρησιμοποιεί το email χρέωσης για τους επισκέπτες, και το ID χρήστη για τους συνδεδεμένους χρήστες');?></small>
            </div>
          </div>
          
          


          

                    
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('eidi');?>>        

          <?php
          $query = "SELECT gks_eshop_pricelist_items_products.*, 
          gks_eshop_products.product_code,
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
          END as product_descr_p,
          gks_eshop_products.product_photo,
          gks_eshop_products.product_descr

          FROM (gks_eshop_pricelist_items_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_pricelist_items_products.product_id = gks_eshop_products.id_product)
          LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
          WHERE gks_eshop_pricelist_items_products.pricelist_item_id=".$id."
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
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κωδικός');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Προϊόν');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Συνθήκη');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="product_tr_exist" data-id="<?php echo $row_list['id_pricelist_item_product'];?>">
              <th scope="row" class="mytdcm product_aa"><?php echo ($i);?></td>       
              <td class="mytdcm">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_product_delete_after|<?php echo $row_list['id_pricelist_item_product'];?>" data-id="<?php echo $row_list['id_pricelist_item_product'];?>" data-model="gks_eshop_pricelist_items_products">            
              </td>
              <td class="mytdcm"><a href="admin-products-item.php?id=<?php echo $row_list['product_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td class="mytdcm"><?php echo getProductPhoto($row_list['product_id'],$row_list['product_photo_p'],32);?></td>
              <td class="mytdcml" nowrap><?php echo $row_list['product_code'];?></td>  
              <td class="mytdcml"><?php echo $row_list['product_descr_p'];?></td> 
              <td class="mytdcml is_include_val_<?php echo $row_list['is_include']?>"><?php
                if ($row_list['is_include']==1) echo gks_lang('Απαιτείται');
                else if ($row_list['is_include']==-1) echo gks_lang('Εξαίρεση');
              ?></td>
              <td class="mytdcm" nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="6">
                <div class="myaddpcb">
                  <input type="text"   name="product"    id="product"   class="form-control form-control-sm" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                  <input type="hidden" name="product_id" id="product_id">
                  <select class="form-control form-control-sm" id="product_include">
                    <option value="1"><?php echo gks_lang('Απαιτείται');?></option>
                    <option value="-1"><?php echo gks_lang('Εξαίρεση');?></option>
                  </select>
                  <button type="button" class="btn btn-sm btn-primary" id="add_product" ><?php echo gks_lang('Προσθήκη');?></button>
                </div>                  
              </td>  
            </tr>
     
          </tbody>
          </table>      

        </div>
      </div>
      

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κατηγορίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('catei');?>>        

          <?php
          //gks_eshop_products_categories.*,
          $query = "SELECT
gks_eshop_pricelist_items_categories.*,
gks_eshop_products_categories.category_photo,
ccproducts.ccc,
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
FROM ((((((((((gks_eshop_pricelist_items_categories
LEFT JOIN gks_eshop_products_categories ON gks_eshop_pricelist_items_categories.product_category_id = gks_eshop_products_categories.id_product_category)
LEFT JOIN (
SELECT product_category_id, Count(pricelist_item_id) AS ccc
FROM gks_eshop_pricelist_items_categories
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
WHERE gks_eshop_pricelist_items_categories.pricelist_item_id=".$id."
          ORDER BY fullpath;";
          
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="categories_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Κατηγορία');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Συνθήκη');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="categories_tr_exist" data-id="<?php echo $row_list['id_pricelist_item_category'];?>">
              <th scope="row" class="mytdcm categories_aa"><?php echo ($i);?></td>       
              <td class="mytdcm">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_categories_delete_after|<?php echo $row_list['id_pricelist_item_category'];?>" data-id="<?php echo $row_list['id_pricelist_item_category'];?>" data-model="gks_eshop_pricelist_items_categories">            
              </td>
              <td class="mytdcm"><a href="admin-product-categories-item.php?id=<?php echo $row_list['product_category_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td class="mytdcm"><?php echo getCategoryPhoto($row_list['product_category_id'],$row_list['category_photo'],32);?></td>  
              <td class="mytdcml"><?php echo $row_list['fullpath'];?></td>  
              <td class="mytdcml is_include_val_<?php echo $row_list['is_include']?>"><?php
                if ($row_list['is_include']==1) echo gks_lang('Απαιτείται');
                else if ($row_list['is_include']==-1) echo gks_lang('Εξαίρεση');
              ?></td>
              <td class="mytdcm" nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr>
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="6">
                <div class="myaddpcb">
                  <input type="text"   name="cateidos"    id="cateidos"   class="form-control form-control-sm" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                  <input type="hidden" name="cateidos_id" id="cateidos_id">
                  <select class="form-control form-control-sm" id="cateidos_include">
                    <option value="1"><?php echo gks_lang('Απαιτείται');?></option>
                    <option value="-1"><?php echo gks_lang('Εξαίρεση');?></option>
                  </select>                  
                  
                  <button type="button" class="btn btn-sm btn-primary" id="add_cateidos"><?php echo gks_lang('Προσθήκη');?></button>
                </div>
              </td>  
            </tr>
      
          </tbody>
          </table>      

        </div>
      </div>    

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Μάρκα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('brands');?>>        

          <?php
          //gks_eshop_products_brands.*,
          $query = "SELECT
gks_eshop_pricelist_items_brands.*,
gks_eshop_products_brands.brand_photo,
ccproducts.ccc,
ug2.product_brand_descr AS gt2, 
ug3.product_brand_descr AS gt3, 
ug4.product_brand_descr AS gt4, 
ug5.product_brand_descr AS gt5,
ug6.product_brand_descr AS gt6,
ug7.product_brand_descr AS gt7,
ug8.product_brand_descr AS gt8,
ug9.product_brand_descr AS gt9,
ug10.product_brand_descr AS gt10,

ug2.id_product_brand AS id2, 
ug3.id_product_brand AS id3, 
ug4.id_product_brand AS id4, 
ug5.id_product_brand AS id5,
ug6.id_product_brand AS id6,
ug7.id_product_brand AS id7,
ug8.id_product_brand AS id8,
ug9.id_product_brand AS id9,
ug10.id_product_brand AS id10,
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
        gks_eshop_products_brands.product_brand_descr) as fullpath,
CONCAT_WS('\\\\',
        ug10.product_brand_descr,
        ug9.product_brand_descr,
        ug8.product_brand_descr,
        ug7.product_brand_descr,
        ug6.product_brand_descr,
        ug5.product_brand_descr,
        ug4.product_brand_descr,
        ug3.product_brand_descr,
        ug2.product_brand_descr) as dirpath
FROM ((((((((((gks_eshop_pricelist_items_brands
LEFT JOIN gks_eshop_products_brands ON gks_eshop_pricelist_items_brands.product_brand_id = gks_eshop_products_brands.id_product_brand)
LEFT JOIN (
SELECT product_brand_id, Count(pricelist_item_id) AS ccc
FROM gks_eshop_pricelist_items_brands
GROUP BY product_brand_id
) AS ccproducts ON gks_eshop_products_brands.id_product_brand = ccproducts.product_brand_id)
LEFT JOIN gks_eshop_products_brands AS ug2  ON gks_eshop_products_brands.product_brand_parent_id = ug2.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.product_brand_parent_id = ug3.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.product_brand_parent_id = ug4.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.product_brand_parent_id = ug5.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.product_brand_parent_id = ug6.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.product_brand_parent_id = ug7.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.product_brand_parent_id = ug8.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.product_brand_parent_id = ug9.id_product_brand)
LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.product_brand_parent_id = ug10.id_product_brand
WHERE gks_eshop_pricelist_items_brands.pricelist_item_id=".$id."
          ORDER BY fullpath;";
          
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="brands_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Brand');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Συνθήκη');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="brands_tr_exist" data-id="<?php echo $row_list['id_pricelist_item_brand'];?>">
              <th scope="row" class="mytdcm brands_aa"><?php echo ($i);?></td>       
              <td class="mytdcm">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_brands_delete_after|<?php echo $row_list['id_pricelist_item_brand'];?>" data-id="<?php echo $row_list['id_pricelist_item_brand'];?>" data-model="gks_eshop_pricelist_items_brands">            
              </td>
              <td class="mytdcm"><a href="admin-product-brands-item.php?id=<?php echo $row_list['product_brand_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              
              <td class="mytdcm" nowrap><?php echo getBrandPhoto($row_list['product_brand_id'],$row_list['brand_photo'],32);?></td>  
              <td class="mytdcml"><?php echo $row_list['fullpath'];?></td>  
              <td class="mytdcml is_include_val_<?php echo $row_list['is_include']?>"><?php
                if ($row_list['is_include']==1) echo gks_lang('Απαιτείται');
                else if ($row_list['is_include']==-1) echo gks_lang('Εξαίρεση');
              ?></td>
              <td class="mytdcm" nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr>
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <div class="myaddpcb">
                  <input type="text"   name="brand_eidos"    id="brand_eidos"   class="form-control form-control-sm" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                  <input type="hidden" name="brand_eidos_id" id="brand_eidos_id">
                  <select class="form-control form-control-sm" id="brand_eidos_include">
                    <option value="1"><?php echo gks_lang('Απαιτείται');?></option>
                    <option value="-1"><?php echo gks_lang('Εξαίρεση');?></option>
                  </select>
                  <button type="button" class="btn btn-sm btn-primary" id="add_brand_eidos"><?php echo gks_lang('Προσθήκη');?></button>
                </div>
              </td>  
            </tr>
      
          </tbody>
          </table>      

        </div>
      </div>      


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
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_pricelist_item'];?>" data-model="gks_eshop_pricelist_items" data-backurl="admin-pricelists-items.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
      
    <div class="col-md-6">

      <?php 
      echo getObjectRels('gks_eshop_pricelist_items',$id);
      echo getActivityObjectTable('gks_eshop_pricelist_items',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_eshop_pricelist_items','id'=>$id));
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_pricelist_item']>0) echo $row['id_pricelist_item'];?></span></div>
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



var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 



var from_php_dialog_object_rel_curr='gks_eshop_pricelist_items';
var from_php_activity_model='gks_eshop_pricelist_items';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');


var from_php_id=<?php echo $id;?>;


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist_items','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist_items','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist_items','delete',$id);?>;






jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


  
 
});

 
 
</script>

<script src="js/admin-pricelists-items-item.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


