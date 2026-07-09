<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Ρυθμίσεις Εφαρμογής');
$nav_active_array=array('manage','manage_settings','manage_system_settings');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_settings','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php?message='.rawurlencode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));die();}


//print '<pre>';echo wp_enqueue_script('my-upload');die();

include_once('_my_header_admin.php');
?>
<style>
.gks_logo_row {
  
}
.gks_logo_row2 {
  text-align:center;
}
.gks_logo_divider {
  height: 1px;
  width: 100%;
  background-color: lightgray;
  margin-bottom: 16px;
}
.gks_logo_divider:last-child {
  display:none;
}

.gks_logo_label,gks_logo_label2 { 
  padding: 0.25rem 0.5rem; 
  font-size: 0.875rem;
  line-height: 1.5;
  height: unset !important;
  border-radius: 0.2rem;
}
.gks_logo_label {
  font-weight: bold; 
}
.gks_logo_img {
  padding: 20px;
}
.gks_logo_img:hover {
  background-image: url(img/grid_transparent.png);
}
.gks_logo_img > img {
  max-width:100%;
  border: 1px solid transparent;
}
.gks_logo_img > img:hover {
  border: 1px solid gray;
}
.gks_logo_wait {
  display:none;  
}
.gks_logo_links i {
  font-size: 150%;
  padding: 4px 6px;
}
.gks_logo_form_files {
  width: 100%;
  height: 100%;  
}
.logo_update_show {
  display:none;
}
</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>



<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικές Ρυθμίσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>   
          <div class="form-group row">
            <label for="GKS_SITE_HUMAN_NAME" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα εφαρμογής');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_SITE_HUMAN_NAME" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_SITE_HUMAN_NAME;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_SITE_URL" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('URL εφαρμογής');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_SITE_URL" type="text" class="form-control form-control-sm" value="<?php echo GKS_SITE_URL;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" disabled>
              <?php if (($_SERVER['HTTPS']=='on' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/' != GKS_SITE_URL) {?>
              <small class="form-text text-muted"><?php echo gks_lang('Μάλλον το σωστό είναι');?>: <?php echo ($_SERVER['HTTPS']=='on' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/';?></small>
              <?php } ?>
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_OFFICIAL_SITE_URL" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επίσημος ιστότοπος');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_OFFICIAL_SITE_URL" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_OFFICIAL_SITE_URL;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('π.χ.');?> https://www.gks.gr">
            </div>
          </div>          
          <div class="form-group row">
            <label for="GKS_SITE_NAME" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επίσημο όνομα ιστότοπου');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_SITE_NAME" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_SITE_NAME;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('π.χ.');?> gks.gr on web">
            </div>
          </div> 
                
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Γλώσσα');?> 
        </div>
        <div class="card-body" <?php echo gks_card_body('lang');?>>         
          <div class="form-group row">
            <label for="GKS_LANG_DEFAULT" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προεπιλεγμένη γλώσσα δεδομένων');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_LANG_DEFAULT"  class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php
                $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
                $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
                FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
                ".$lang_prepare_gks_lang['sql']['from2']."
                ORDER BY lang_sortorder,lang_name";                  
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_lang'].'" ';
                  if ($GKS_LANG_DEFAULT == $row_select['id_lang']) echo ' selected ';
                  echo '>'.$row_select['lang_name'].'</option>';
                }
                ?>                
              </select>
            </div>
          </div>
        </div>
      </div>
                   
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Νόμισμα, ημερομηνία, ώρα');?> 
        </div>
        <div class="card-body" <?php echo gks_card_body('nomis');?>>         

                    
          <div class="form-group row">
            <label for="GKS_NUMBER_FORMAT_DECIMAL" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σύμβολο δεκαδικών');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_NUMBER_FORMAT_DECIMAL" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_NUMBER_FORMAT_DECIMAL;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div> 

          <div class="form-group row">
            <label for="GKS_NUMBER_FORMAT_THOUSAND" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σύμβολο διαχωριστικού χιλιάδων');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_NUMBER_FORMAT_THOUSAND" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_NUMBER_FORMAT_THOUSAND;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_NUMBER_FORMAT_CURRENCY_DECIMAL" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πλήθος δεκαδικών');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_NUMBER_FORMAT_CURRENCY_DECIMAL" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px" min="0" max="10" step="1">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_NUMBER_FORMAT_CURRENCY_SYMBOL" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σύμβολο νομίσματος');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_NUMBER_FORMAT_CURRENCY_SYMBOL" type="text  " class="form-control form-control-sm myneedsave" value="<?php echo $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Θέση εμφάνισης νομισματικού συμβόλου');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW"  class="form-control form-control-sm myneedsave" style="max-width:200px">
                <option value='before' <?php if ($GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='before') echo 'selected';?>><?php echo gks_lang('Πριν τον αριθμό');?></option>
                <option value='after'  <?php if ($GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=='after')  echo 'selected';?>><?php echo gks_lang('Μετά τον αριθμό');?></option>
              </select>
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_NUMBER_FORMAT_DATE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μορφή ημερομηνίας');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_NUMBER_FORMAT_DATE" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_NUMBER_FORMAT_DATE;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_NUMBER_FORMAT_TIME" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μορφή ώρας');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_NUMBER_FORMAT_TIME" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_NUMBER_FORMAT_TIME;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div> 


        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('eidi');?>>       
          <div class="form-group row">
            <label for="GKS_PRODUCT_DESCR_SMALL" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία μικρής περιγραφής');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_PRODUCT_DESCR_SMALL" class="switchery" <?php if ($GKS_PRODUCT_DESCR_SMALL) echo ' checked ';?> >
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_PRODUCT_DESCR_BIG" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία μεγάλης περιγραφή');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_PRODUCT_DESCR_BIG" class="switchery" <?php if ($GKS_PRODUCT_DESCR_BIG) echo ' checked ';?> >
            </div>
          </div> 


          
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ξενοδοχείο');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('hotel');?>>       
          <div class="form-group row">
            <label for="GKS_HOTEL_BACKEND" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_HOTEL_BACKEND" class="switchery" <?php if ($GKS_HOTEL_BACKEND) echo ' checked ';?> >
            </div>
          </div> 
          <div id="GKS_HOTEL_BACKEND_div" style="<?php if ($GKS_HOTEL_BACKEND==false) echo 'display:none;';?>">
            <div class="form-group row">
              <label for="GKS_HOTEL_RESERVATIONS_ONLINE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('OnLine Κρατήσεις');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_HOTEL_RESERVATIONS_ONLINE" class="switchery" <?php if ($GKS_HOTEL_RESERVATIONS_ONLINE) echo ' checked ';?> >
              </div>
            </div>
          </div>
          
        </div>
      </div>
                  
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('CRM');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('wareh');?>>       
          <div class="form-group row">
            <label for="GKS_CRM_ENABLE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_CRM_ENABLE" class="switchery" <?php if ($GKS_CRM_ENABLE) echo ' checked ';?> >
            </div>
          </div> 
          <div id="GKS_CRM_ENABLE_div" style="<?php if ($GKS_CRM_ENABLE==false) echo 'display:none;';?>">
            <div class="form-group row">
              <label for="GKS_CRM_LEADS_ENABLE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ευκαιρίες');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_CRM_LEADS_ENABLE" class="switchery" <?php if ($GKS_CRM_LEADS_ENABLE) echo ' checked ';?> >
              </div>
            </div>
            <div class="form-group row">
              <label for="GKS_CRM_TASKS_ENABLE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εργασίες');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_CRM_TASKS_ENABLE" class="switchery" <?php if ($GKS_CRM_TASKS_ENABLE) echo ' checked ';?> >
              </div>
            </div> 
            <div class="form-group row">
              <label for="GKS_CRM_MACHINE_ENABLE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Συσκευές');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_CRM_MACHINE_ENABLE" class="switchery" <?php if ($GKS_CRM_MACHINE_ENABLE) echo ' checked ';?> >
              </div>
            </div> 
          </div>
          
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποθήκη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('crm');?>>       
          <div class="form-group row">
            <label for="GKS_WARE_HOUSE_ENABLE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_WARE_HOUSE_ENABLE" class="switchery" <?php if ($GKS_WARE_HOUSE_ENABLE) echo ' checked ';?> >
            </div>
          </div> 

          <div class="form-group row">
            <label for="GKS_PRODUCT_LOTS_SERIALS" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πατρίδες και Serial Numbers');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_PRODUCT_LOTS_SERIALS" class="switchery" <?php if ($GKS_PRODUCT_LOTS_SERIALS) echo ' checked ';?> >
            </div>
          </div>
        </div>
      </div>

      
      



      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πωλήσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('order2');?>>       
          
          <div class="form-group row">
            <label for="GKS_ORDERS_ENABLE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_ORDERS_ENABLE" class="switchery" <?php if ($GKS_ORDERS_ENABLE) echo ' checked ';?> >
            </div>
          </div>         
          <div id="GKS_ORDERS_ENABLE_div" style="<?php if ($GKS_ORDERS_ENABLE==false) echo 'display:none;';?>">
            <div class="form-group row">
              <label for="GKS_ORDERS_OCCASION" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περίσταση');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ORDERS_OCCASION" class="switchery" <?php if ($GKS_ORDERS_OCCASION) echo ' checked ';?> >
              </div>
            </div> 
  
            <div class="form-group row">
              <label for="GKS_ORDERS_COL_ITEMPRICE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εμφάνιση στήλης τιμής μονάδος');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ORDERS_COL_ITEMPRICE" class="switchery" <?php if ($GKS_ORDERS_COL_ITEMPRICE) echo ' checked ';?> >
              </div>
            </div> 
            <div class="form-group row">
              <label for="GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τιμή μονάδος ΚΑΙ με ΦΠΑ');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA" class="switchery" <?php if ($GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA) echo ' checked ';?> >
                <small class="form-text text-muted"><?php echo gks_lang('Κατά την εισαγωγή τιμής μονάδος να μπορώ, εάν θέλω, να εισάγω την τιμή μαζί με το ΦΠΑ');?></small>
              </div>
            </div> 
            <div class="form-group row">
              <label for="GKS_ORDERS_COL_FPA" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εμφάνιση στήλης ΦΠΑ');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ORDERS_COL_FPA" class="switchery" <?php if ($GKS_ORDERS_COL_FPA) echo ' checked ';?> >
              </div>
            </div> 
            
            
            
            <div class="form-group row">
              <label for="GKS_ORDERS_SETS" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σετς');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ORDERS_SETS" class="switchery" <?php if ($GKS_ORDERS_SETS) echo ' checked ';?> >
              </div>
            </div> 
            <div class="form-group row" id="div_GKS_ORDERS_SETS_VALS" style="<?php if ($GKS_ORDERS_SETS==false) echo 'display:none;';?>">
              <label for="GKS_ORDERS_SETS_VALS" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προεπιλογές Σετ');?>:</label>
              <div class="col-sm-6">
                <input id="GKS_ORDERS_SETS_VALS" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_ORDERS_SETS_VALS;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div> 
  
  
  
            <div class="form-group row">
              <label for="GKS_ORDERS_SHEETS" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σελίδες');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ORDERS_SHEETS" class="switchery" <?php if ($GKS_ORDERS_SHEETS) echo ' checked ';?> >
              </div>
            </div> 


          </div>
        </div>
      </div>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Λογιστική');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('paras');?>>       
          <div class="form-group row">
            <label for="GKS_ACC_ENABLE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_ACC_ENABLE" class="switchery" <?php if ($GKS_ACC_ENABLE) echo ' checked ';?> >
            </div>
          </div>
          <div id="GKS_ACC_ENABLE_div" style="<?php if ($GKS_ACC_ENABLE==false) echo 'display:none;';?>">

            <div class="form-group row">
              <label for="GKS_ACC_INV_COL_ITEMPRICE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εμφάνιση στήλης τιμής μονάδος');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ACC_INV_COL_ITEMPRICE" class="switchery" <?php if ($GKS_ACC_INV_COL_ITEMPRICE) echo ' checked ';?> >
              </div>
            </div> 
            <div class="form-group row">
              <label for="GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τιμή μονάδος ΚΑΙ με ΦΠΑ');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA" class="switchery" <?php if ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA) echo ' checked ';?> >
                <small class="form-text text-muted"><?php echo gks_lang('Κατά την εισαγωγή τιμής μονάδος να μπορώ, εάν θέλω, να εισάγω την τιμή μαζί με το ΦΠΑ');?></small>
              </div>
            </div>
            <div class="form-group row">
              <label for="GKS_ACC_INV_COL_FPA" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εμφάνιση στήλης ΦΠΑ');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ACC_INV_COL_FPA" class="switchery" <?php if ($GKS_ACC_INV_COL_FPA) echo ' checked ';?> >
              </div>
            </div> 
            <div class="form-group row">
              <label for="GKS_ACC_INV_EXTRA_OPEN" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εμφάνιση φόρων και χαρακτηριστικών');?>:</label>
              <div class="col-sm-6">
                <input type="checkbox" id="GKS_ACC_INV_EXTRA_OPEN" class="switchery" <?php if ($GKS_ACC_INV_EXTRA_OPEN) echo ' checked ';?> >
              </div>
            </div> 

          </div>
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Παραγωγή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('produ');?>>       
          <div class="form-group row">
            <label for="GKS_ORDERS_PRODUCTION" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_ORDERS_PRODUCTION" class="switchery" <?php if ($GKS_ORDERS_PRODUCTION) echo ' checked ';?> >
            </div>
          </div> 

        </div>
      </div>

      <div class="card gks_card_expand" id="deltia_praggelies_parastatika" style="<?php
        if (!($GKS_ORDERS_ENABLE or $GKS_ACC_ENABLE or $GKS_WARE_HOUSE_ENABLE)) {
          echo 'display:none;';
        }  
        ?>">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Δελτία, Παραγγελίες, Παραστατικά');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('order');?>>         
          <div class="form-group row">
            <label for="GKS_ORDER_DEFAULT_DELIVERY" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προεπιλεγμένος τρόπος αποστολής');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_ORDER_DEFAULT_DELIVERY"  class="form-control form-control-sm myneedsave"  >
              <option value="0"></option>
              <?php
              $sql="SELECT * FROM gks_delivery_methods ORDER BY mysortorder,delivery_method_name";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_delivery_method'].'" ';
                if ($row_select['id_delivery_method']==$GKS_ORDER_DEFAULT_DELIVERY) echo ' selected ';
                echo '>'.$row_select['delivery_method_name'].'</option>';
              }?></select>
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αυτόματο κόστος αποστολής');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK" class="switchery" <?php if ($GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK==0) echo ' checked ';?> >
              <small class="form-text text-muted"><?php echo gks_lang('Το κόστος αποστολής ορίζεται αυτόματα κατά την αλλαγή του τρόπου αποστολής');?></small>
            </div>
          </div> 

          
          <div class="form-group row">
            <label for="GKS_ORDER_DEFAULT_PAYMENT" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προεπιλεγμένος τρόπος πληρωμής');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_ORDER_DEFAULT_PAYMENT"  class="form-control form-control-sm myneedsave"  >
              <option value="0"></option>
              <?php
              $sql="SELECT * FROM gks_payment_acquirers ORDER BY mysortorder,payment_acquirer_name";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('sql error');}
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_payment_acquirer'].'" ';
                if ($row_select['id_payment_acquirer']==$GKS_ORDER_DEFAULT_PAYMENT) echo ' selected ';
                echo '>'.$row_select['payment_acquirer_name'].'</option>';
              }?></select>
            </div>
          </div> 

          <div class="form-group row">
            <label for="GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αυτόματο κόστος αποστολής/πληρωμής');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK" class="switchery" <?php if ($GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK==0) echo ' checked ';?> >
              <small class="form-text text-muted"><?php echo gks_lang('Το κόστος πληρωμής ορίζεται αυτόματα κατά την αλλαγή του τρόπου πληρωμής');?></small>
            </div>
          </div> 


          <div class="form-group row">
            <label for="GKS_BASKET_ROUND_DIAFORA_001" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Στρογγυλοποίηση αξίας έκπτωσης');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_BASKET_ROUND_DIAFORA_001" class="switchery" <?php if ($GKS_BASKET_ROUND_DIAFORA_001) echo ' checked ';?> >
              <small class="form-text text-muted"><?php echo gks_lang('Όταν η αξία της έκπτωσης είναι μικρότερη από 0.01 να γίνεται μηδέν');?></small>
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σταθερή τελική τιμή λιανικής');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI" class="switchery" <?php if ($GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI) echo ' checked ';?> >
              <small class="form-text text-muted">
                <?php echo gks_lang('Εάν κάποιο είδος στην τιμή λιανικής περιέχει τον ΦΠΑ, τότε ο ΦΠΑ θα υπολογιστεί έτσι ώστε πάντα η τελική τιμή να είναι η τιμή του είδους.');?>
                <br>
                <?php echo gks_lang('π.χ. ένα είδος έχει τιμή λιανικής μαζί με ΦΠΑ 100€, δηλαδή σε 80,65€ καθαρή αξία + 19,35€ ΦΠΑ (24%) = <b>100,00€</b>');?>
                <br>
                <?php echo gks_lang('εάν η φορολογική θέση του πελάτη είναι Χονδρικής Εσωτερικού Μειωμένο τότε:');?>
                <br>
                <?php echo gks_lang('Εάν <b>ΔΕΝ</b> είναι ενεργοποιημένη η παραπάνω επιλογή, η τιμή θα υπολογιστεί ως 80,65€ καθαρή αξία + 13,71€ ΦΠΑ (17%) = <b>94,36€</b>');?>
                <br>
                <?php echo gks_lang('Εάν <b>ΕΙΝΑΙ</b> ενεργοποιημένη η παραπάνω επιλογή, η τιμή θα υπολογιστεί ως 85,47€ καθαρή αξία + 14,53€ ΦΠΑ (17%) = <b>100,00€</b>');?>
                <br>
                <?php echo gks_lang('Το ίδιο ισχύει και για τις άλλες φορολογικές θέσεις, ενδοκοινοτικές και τρίτων χωρών.');?>
              </small>
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σταθερή τελική τιμή χονδρικής');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI" class="switchery" <?php if ($GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI) echo ' checked ';?> >
              <small class="form-text text-muted">
                <?php echo gks_lang('Το ίδιο με το παραπάνω, απλά αφορά την τιμή χονδρικής του είδους');?> 
              </small>  
            
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_INPUT_STEP_AJIA" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Βήμα αριθμού στην αξία');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_INPUT_STEP_AJIA"  class="form-control form-control-sm myneedsave" style="max-width:200px">
                <option value='1'    <?php if ($GKS_INPUT_STEP_AJIA=='1')    echo 'selected';?>>1</option>
                <option value='0.5'  <?php if ($GKS_INPUT_STEP_AJIA=='0.5')  echo 'selected';?>>0,5</option>
                <option value='0.1'  <?php if ($GKS_INPUT_STEP_AJIA=='0.1')  echo 'selected';?>>0,1</option>
                <option value='0.05' <?php if ($GKS_INPUT_STEP_AJIA=='0.05') echo 'selected';?>>0,05</option>
                <option value='0.01' <?php if ($GKS_INPUT_STEP_AJIA=='0.01') echo 'selected';?>>0,01</option>
              </select>
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_INPUT_STEP_POSOTITA" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Βήμα αριθμού στην ποσότητα');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_INPUT_STEP_POSOTITA"  class="form-control form-control-sm myneedsave" style="max-width:200px">
                <option value='1'    <?php if ($GKS_INPUT_STEP_POSOTITA=='1')    echo 'selected';?>>1</option>
                <option value='0.5'  <?php if ($GKS_INPUT_STEP_POSOTITA=='0.5')  echo 'selected';?>>0,5</option>
                <option value='0.1'  <?php if ($GKS_INPUT_STEP_POSOTITA=='0.1')  echo 'selected';?>>0,1</option>
                <option value='0.05' <?php if ($GKS_INPUT_STEP_POSOTITA=='0.05') echo 'selected';?>>0,05</option>
                <option value='0.01' <?php if ($GKS_INPUT_STEP_POSOTITA=='0.01') echo 'selected';?>>0,01</option>
              </select>
            </div>
          </div>
          
          
          <div class="form-group row">
            <label for="GKS_INPUT_STEP_POSOSTO" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Βήμα αριθμού στο ποσοστό έκπτωσης');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_INPUT_STEP_POSOSTO"  class="form-control form-control-sm myneedsave" style="max-width:200px">
                <option value='1'    <?php if ($GKS_INPUT_STEP_POSOSTO=='1')    echo 'selected';?>>1</option>
                <option value='0.5'  <?php if ($GKS_INPUT_STEP_POSOSTO=='0.5')  echo 'selected';?>>0,5</option>
                <option value='0.1'  <?php if ($GKS_INPUT_STEP_POSOSTO=='0.1')  echo 'selected';?>>0,1</option>
                <option value='0.05' <?php if ($GKS_INPUT_STEP_POSOSTO=='0.05') echo 'selected';?>>0,05</option>
                <option value='0.01' <?php if ($GKS_INPUT_STEP_POSOSTO=='0.01') echo 'selected';?>>0,01</option>
              </select>
            </div>
          </div> 


          <div class="form-group row">
            <label for="GKS_BASKET_CALC_ITEM_DECIMAL" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πλήθος δεκαδικών για την στρογγυλοποίηση της αξίας ανά τεμάχιο');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_BASKET_CALC_ITEM_DECIMAL"  class="form-control form-control-sm myneedsave" style="max-width:200px">
                <option value='0' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='0') echo 'selected';?>>0</option>
                <option value='1' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='1') echo 'selected';?>>1</option>
                <option value='2' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='2') echo 'selected';?>>2</option>
                <option value='3' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='3') echo 'selected';?>>3</option>
                <option value='4' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='4') echo 'selected';?>>4</option>
                <option value='5' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='5') echo 'selected';?>>5</option>
                <option value='6' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='6') echo 'selected';?>>6</option>
                <option value='7' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='7') echo 'selected';?>>7</option>
                <option value='8' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='8') echo 'selected';?>>8</option>
                <option value='9' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='9') echo 'selected';?>>9</option>
                <option value='10' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='10') echo 'selected';?>>10</option>
                <option value='11' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='11') echo 'selected';?>>11</option>
                <option value='12' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='12') echo 'selected';?>>12</option>
                <option value='13' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='13') echo 'selected';?>>13</option>
                <option value='14' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='14') echo 'selected';?>>14</option>
                <option value='15' <?php if ($GKS_BASKET_CALC_ITEM_DECIMAL=='15') echo 'selected';?>>15</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="GKS_BASKET_CALC_EKPTOSI_DECIMAL" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πλήθος δεκαδικών για την στρογγυλοποίηση του ποσοστού έκπτωσης');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_BASKET_CALC_EKPTOSI_DECIMAL"  class="form-control form-control-sm myneedsave" style="max-width:200px">
                <option value='0' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='0') echo 'selected';?>>0</option>
                <option value='1' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='1') echo 'selected';?>>1</option>
                <option value='2' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='2') echo 'selected';?>>2</option>
                <option value='3' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='3') echo 'selected';?>>3</option>
                <option value='4' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='4') echo 'selected';?>>4</option>
                <option value='5' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='5') echo 'selected';?>>5</option>
                <option value='6' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='6') echo 'selected';?>>6</option>
                <option value='7' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='7') echo 'selected';?>>7</option>
                <option value='8' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='8') echo 'selected';?>>8</option>
                <option value='9' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='9') echo 'selected';?>>9</option>
                <option value='10' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='10') echo 'selected';?>>10</option>
                <option value='11' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='11') echo 'selected';?>>11</option>
                <option value='12' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='12') echo 'selected';?>>12</option>
                <option value='13' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='13') echo 'selected';?>>13</option>
                <option value='14' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='14') echo 'selected';?>>14</option>
                <option value='15' <?php if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL=='15') echo 'selected';?>>15</option>
              </select>
            </div>
          </div>



        </div>
      </div>



      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πάγια');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('assets');?>>       
          <div class="form-group row">
            <label for="GKS_ASSETS_ENABLE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_ASSETS_ENABLE" class="switchery" <?php if ($GKS_ASSETS_ENABLE) echo ' checked ';?> >
            
            </div>
          </div>
        </div>
      </div>
      
      <?php
      $GKS_ERP_APP_MOBILE_VER=gks_erp_app_mobile_get_later_version();

      ?>      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('gks ERP App Mobile');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('erpappmobile');?>>       
          <div class="form-group row">
            <label for="GKS_ERP_APP_MOBILE_VER" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Έκδοση του gks ERP App Mobile');?>:</label>
            <div class="col-sm-6">
              <select id="GKS_ERP_APP_MOBILE_VER"  class="form-control form-control-sm myneedsave" style="max-width:200px">
                <option value='2.2' <?php if ($GKS_ERP_APP_MOBILE_VER=='2.2') echo 'selected';?>>2.2</option>
                <option value='2.1' <?php if ($GKS_ERP_APP_MOBILE_VER=='2.1') echo 'selected';?>>2.1</option>
                <option value='2.0' <?php if ($GKS_ERP_APP_MOBILE_VER=='2.0') echo 'selected';?>>2.0</option>
                <option value='1.9' <?php if ($GKS_ERP_APP_MOBILE_VER=='1.9') echo 'selected';?>>1.9</option>
                <option value='1.8' <?php if ($GKS_ERP_APP_MOBILE_VER=='1.8') echo 'selected';?>>1.8</option>
                <option value='1.7' <?php if ($GKS_ERP_APP_MOBILE_VER=='1.7') echo 'selected';?>>1.7</option>
                <option value='1.6' <?php if ($GKS_ERP_APP_MOBILE_VER=='1.6') echo 'selected';?>>1.6</option>
                <option value='1.3' <?php if ($GKS_ERP_APP_MOBILE_VER=='1.3') echo 'selected';?>>1.3</option>
                <option value='1.2' <?php if ($GKS_ERP_APP_MOBILE_VER=='1.2') echo 'selected';?>>1.2</option>
                <option value='1.1' <?php if ($GKS_ERP_APP_MOBILE_VER=='1.1') echo 'selected';?>>1.1</option>
                <option value='1.0' <?php if ($GKS_ERP_APP_MOBILE_VER=='1.0') echo 'selected';?>>1.0</option>
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
          <?php echo gks_lang('Ρόλοι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('roles');?>>       
          <div class="form-group row">
            <div class="col-sm-12" style="font-size: 0.875rem;text-align:center;">
              <?php echo gks_lang('Ενεργοποιήστε τους ρόλους των επαφών που θέλετε να έχουν πρόσβαση στην εφαρμογή');?>
              <br>
              <?php echo gks_lang('Οι διαχειριστές θα έχουν πρόσβαση');?>
              <br>
              <?php echo gks_lang('Οι επισκέπτες (απλές επαφές) δεν θα έχουν πρόσβαση');?>
            </div>
          </div>      
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ρόλοι επαφών');?>:</label>
            <div class="col-md-8">

              <?php
              $gks_wp_system_roles = gks_wp_system_roles_func();
              foreach ($gks_wp_system_roles as $role_item) {
                //if ($role_item['hierarchy'] > $min_hierarchy_login_user) {
                  $role_checked='';
                  $role_disabled='';
                  if ($role_item['id']=='administrator' or $role_item['id']=='adminmy') {
                    $role_checked='checked';
                    $role_disabled='disabled';
                  } else if ($role_item['id']=='subscriber') {
                    $role_checked='';
                    $role_disabled='disabled';
                  } else if (in_array($role_item['id'],$GKS_USERS_ACCESS_ROLES)) {
                    $role_checked='checked';
                  }
                  echo '<input class="rolecheckbox" '.$role_disabled.' type="checkbox" style="height:32px" name="role_'.$role_item['id'].'" id="role_'.$role_item['id'].'" value="'.$role_item['id'].'." '.
                  $role_checked. '> '.$role_item['name'].'<br>';
                //}
              }
                            
              //print '<pre>';
              //print_r($gks_wp_system_roles);
              //print '</pre>';
              ?>          
            </div>
          </div>
        </div>
      </div>
      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Email');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('email');?>>       
          <div class="form-group row">
            <label for="GKS_SITE_EMAIL" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('email Official');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_SITE_EMAIL" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_SITE_EMAIL;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="GKS_EMAIL_HOST" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('email server hostname');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_EMAIL_HOST" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_EMAIL_HOST;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="GKS_EMAIL_PORT" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('email server port');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_EMAIL_PORT" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_EMAIL_PORT;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="GKS_EMAIL_SMTPAUTH" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('email server smtp auth');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="GKS_EMAIL_SMTPAUTH" class="switchery" <?php if ($GKS_EMAIL_SMTPAUTH) echo ' checked ';?> >
            </div>
          </div>          
          <div class="form-group row">
            <label for="GKS_EMAIL_USERNAME" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('email username');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_EMAIL_USERNAME" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_EMAIL_USERNAME;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="GKS_EMAIL_PASSWORD" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('email password');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_EMAIL_PASSWORD" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_EMAIL_PASSWORD;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="GKS_EMAIL_BCC1" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('email BCC 1');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_EMAIL_BCC1" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_EMAIL_BCC1;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="GKS_EMAIL_BCC2" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('email BCC 2');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_EMAIL_BCC2" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_EMAIL_BCC2;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="GKS_EMAIL_BCC3" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('email BCC 3');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_EMAIL_BCC3" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_EMAIL_BCC3;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-sm-12" style="text-align: center;">
              <a href="admin-email-send.php" class="btn btn-primary">Test mail settings</a>
            </div>
          </div>
        </div>
      </div>
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('SMS');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('sms');?>>       
          <div class="form-group row">
            <div class="col-sm-12" style="text-align: center;font-size: 0.875rem;">
              <?php echo gks_lang('Αποστολή μέσω');?> <a href="https://www.smsapi.com/el" target="_blank">SMSAPI</a>
            </div>
          </div>
          <div class="form-group row">
            <label for="GKS_SMS_SENDER" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Sender');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_SMS_SENDER" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_SMS_SENDER;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_SMS_TOKEN" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Token');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_SMS_TOKEN" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_SMS_TOKEN;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="sms_call_back" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Callback URL');?>:</label>
            <div class="col-sm-6">
              <input id="sms_call_back" type="text" class="form-control form-control-sm myneedsave" value="<?php echo GKS_SITE_URL;?>my/smsapi_callback.php" autocomplete="<?php echo $autocomplete_gks_disable;?>" disabled>
            </div>
          </div>          
          
          
          
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Viber');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('viber');?>>       
          <div class="form-group row">
            <label for="GKS_VIBER_URI" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('URI');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_VIBER_URI" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_VIBER_URI;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <small class="form-text text-muted" id="GKS_VIBER_URI_link"><?php
                if (trim_gks($GKS_VIBER_URI)!='') {
                  echo '<a href="viber://pa/info?uri='.$GKS_VIBER_URI.'" target="_blank">viber://pa/info?uri='.$GKS_VIBER_URI.'</a>';
                } ?>
              </small>
            </div>
          </div> 

                    
          <div class="form-group row">
            <label for="GKS_VIBER_TOKEN" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Token');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_VIBER_TOKEN" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_VIBER_TOKEN;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row" id="viber_hook_page_div" style="<?php if (trim_gks($GKS_VIBER_TOKEN)=='') echo 'display:none;';?>;">
            <label for="" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ορισμός Σελίδας');?>:</label>
            <div class="col-sm-6">
              <div style="margin-bottom: 10px;" id="viber_hook_page_span">
              <?php
              $html_span='<span style="color:white;padding:4px 10px;;border-radius:10px;font-size:80%;background-color:red;">'.gks_lang('Δεν έχει ορισθεί σελίδα').'</span>';
              $sql_get_viber="select * from gks_settings where mykey='gks_data_viber_hook_page_response';";
              $result_get_viber = $db_link->query($sql_get_viber);        
              if (!$result_get_viber) {debug_mail(false,'error sql',$sql_get_viber); echo 'sql error';}
              if ($result_get_viber->num_rows > 0) {
                $row_get_viber = $result_get_viber->fetch_assoc();
                $response=trim_gks($row_get_viber['myvalue']);
                $viber_response=json_decode($response,true);
                if (!(is_array($viber_response) 
                   and isset($viber_response['status']) 
                   and $viber_response['status']==0 
                   and isset($viber_response['status_message']) 
                   and $viber_response['status_message']=='ok')) {
                  //nothing
                } else {
                  $html_span= '<span style="color:white;padding:4px 10px;;border-radius:10px;font-size:80%;background-color:green;">'.gks_lang('Έχει ορισθεί σελίδα').'</span>';
                }
              }
              echo $html_span;
              ?>
              </div>
              <div><button type="button" class="btn btn-sm btn-primary" id="viber_hook_page_set"><?php echo gks_lang('Ορισμός');?></button></div>
              <small class="form-text text-muted"><?php echo gks_lang('Ορισμός σελίδας αποστολής δεδομέων από Viber Server');?></small>
              
            </div>
          </div> 
          
          
        </div>
      </div>

            
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κοινωνικά Δίκτυα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('socialml');?>> 
          <?php echo gks_sociallinks_item('gks_settings',1);?>
        </div>
      </div>


    
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center" id>
          <?php echo gks_lang('Google');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('google');?>>       
          <div class="form-group row">
            <label for="GKS_GOOGLE_MAPS_API_KEY" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Google Maps Api Key (client)');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_GOOGLE_MAPS_API_KEY" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_GOOGLE_MAPS_API_KEY;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
     
          <div class="form-group row">
            <label for="GKS_GOOGLE_MAPS_API_KEY_SERVER" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Google Maps Api Key (server)');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_GOOGLE_MAPS_API_KEY_SERVER" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_GOOGLE_MAPS_API_KEY_SERVER;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
        </div>
        
      </div>

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('AWS');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('aws');?>>       
          <div class="form-group row">
            <label for="GKS_AWS_BUCKET" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Bucket name');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_AWS_BUCKET" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_AWS_BUCKET;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_AWS_KEY" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Bucket key');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_AWS_KEY" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_AWS_KEY;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_AWS_SECRET" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Bucket secret');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_AWS_SECRET" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_AWS_SECRET;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_AWS_FOLDER" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Bucket root folder');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_AWS_FOLDER" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_AWS_FOLDER;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Send Anywhere');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('sendany');?>>       
          <div class="form-group row">
            <label for="GKS_SEND_ANYWHERE_API_KEY" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Api key');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_SEND_ANYWHERE_API_KEY" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_SEND_ANYWHERE_API_KEY;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 

          
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('ΑΑΔΕ Sandbox for Developers');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('aade');?>>       
          <div class="form-group row">
            <label for="GKS_AADE_MYDATA_SANDBOX_AFM" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_AADE_MYDATA_SANDBOX_AFM" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_AADE_MYDATA_SANDBOX_AFM;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_AADE_MYDATA_SANDBOX_BRANCE" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός Εγκατάστασης');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_AADE_MYDATA_SANDBOX_BRANCE" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_AADE_MYDATA_SANDBOX_BRANCE;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_AADE_MYDATA_SANDBOX_USER_ID" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα Χρήστη');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_AADE_MYDATA_SANDBOX_USER_ID" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_AADE_MYDATA_SANDBOX_USER_ID;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κωδικός API');?>:</label>
            <div class="col-sm-6">
              <input id="GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY;?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 

          
        </div>
      </div>
      
      <div class="card gks_card_expand" id="custom_css">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Γενικό CSS');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('css');?>>
          
          <div class="form-group row">
            <div class="col-md-12" style="text-align:left;">
              <?php
              $custom_css_global_val='';
              $sql="select myvalue from gks_settings where mykey='custom_css_global'";
              $result_select = $db_link->query($sql);
              if (!$result_select) {debug_mail(false,'error sql',$sql);die('error sql');} 
              if ($result_select->num_rows==1) {
                $row_select = $result_select->fetch_assoc();
                $custom_css_global_val=trim_gks($row_select['myvalue']);
              }
              ?>               
              <textarea id="custom_css_global" style="border:1px solid black;"><?php echo $custom_css_global_val;?></textarea>
            </div>
            <div class="col-md-12" style="text-align:left;">
              <small class="form-text text-muted"><?php echo gks_lang('Για προβολή μεγάλου παραθύρου επεξεργασίας πατήστε το πλήκτρο F11 ενώ είστε μέσα στον επεξεργαστή. Για επαναφορά πατήστε πάλι το F11 ή το esc');?></small>
              <small class="form-text text-muted"><?php echo gks_lang('Αυτή η ρύθμιση αφορά τις προσαρμογές όλων των χρηστών.<br>Εάν θέλετε να ρυθμίσετε τις δικές σας προσαρμογές μεταβείτε στην σελίδα');?> <a href="admin-user-settings.php#custom_css"><?php echo gks_lang('Οι Ρυθμίσεις μου');?></a></small>
            </div>
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
      <button style="justify-content: center!important;" type="button" class="btn btn-danger submit_button_back" id="goback"><?php echo gks_lang('Ακύρωση');?></button>
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">


      <div class="card gks_card_expand" id="custom_css">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Λογότυπος');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('logo');?>>
          <?php
          $images=array(
            array('logo50.png',64,64),
            array('logo100.png',100,21),
            array('icon.png',128,128),
            array('app_logo_1x.png',148,43),
            array('logo200.png',200,40),
            array('app_logo_2x.png',296,86),
            array('logo.png',300,100),
            array('logo2.png',300,100),
            array('logo2x2.png',600,200),
            array('paypal_header.png',750,90),
            array('finallogonew.png',1250,405),
          );
          foreach ($images as $logoaa=>$myimg) {
            $myctime=time();
            $logocc=$logoaa+1;
          ?>
          <div class="form-group row gks_logo_row" data-cc="<?php echo $logocc;?>">
            <div class="col-md-12 gks_logo_row2">
              <div class="gks_logo_label"><?php echo $myimg[0]?></div>
              <div class="gks_logo_label2"><?php echo $myimg[1].'x'.$myimg[2];?> pixels</div>
              <div class="gks_logo_img"><img src="_current/_img_site/<?php echo $myimg[0].'?v='.$myctime?>"></div>
              <div class="gks_logo_wait"><img src="img/wait.gif"></div>
              <div class="gks_logo_form_div">
                  <form class="gks_logo_form" data-cc="<?php echo $logocc;?>" data-filename="<?php echo $myimg[0];?>" action="admin-system-settings-logo-upload.php" role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="gksfilename" value="<?php echo $myimg[0];?>">
                    <input type="hidden" name="gkssizex" value="<?php echo $myimg[1];?>">
                    <input type="hidden" name="gkssizey" value="<?php echo $myimg[2];?>">
                    
                    <div style="clear: both;"></div>
                    <span data-cc="<?php echo $logocc;?>" class="f_button_add_files_photo fileinput-button"  href="#"     data-options="thumbnail: ''" style="padding-top:10px;">
                      <div class="gks_logo_links">
                        <!--<a href="#"><i class="fas fa-upload"></i></a>-->
                        <span style="position:relative;display: inline-block;">
                          <button type="submit" class="btn btn-primary" ><i class="fas fa-upload"></i></button>
                          <input class="gks_logo_form_files" type="file" name="files[]" multiple style="display:none11;" >
                        </span>
                        <a class="btn btn-primary" href="_current/_img_site/<?php echo $myimg[0].'?v='.$myctime?>" download><i class="fas fa-download"></i></a>
                        <a class="btn btn-primary" href="_current/_img_site/<?php echo $myimg[0].'?v='.$myctime?>" target="_blank"><i class="fas fa-external-link-alt"></i></a>
                      </div>
                      
                      <div><?php echo gks_lang('Μέγιστο μέγεθος');?> <?php echo gks_get_max_upload_file_size();?>. <?php echo gks_lang('Τύπος αρχείου');?> .png</div>
                      
                    </span>
                    <div data-cc="<?php echo $logocc;?>" class="progress-bar_photo" style="margin-top:10px; display:none;background: rgb(230,230,230);">
                      <div class="bar_photo" style="padding-top:0px;padding-bottom:0px;width: 0%;height: 20px;background: green;"></div>
                    </div>
                    <div data-cc="<?php echo $logocc;?>" class="progress-extended_photo" style="display:none;">&nbsp;</div>
                  </form>               
              </div>
              
            </div>
          </div>
          <div class="form-group row logo_update_show" data-cc="<?php echo $logocc;?>">
            <div class="col-md-12">
              <div class="alert alert-primary d-flex align-items-center" role="alert">
                <i class="fa-solid fa-circle-info"></i>
                <div style="margin:10px">
                  <?php echo gks_lang('Για να δείτε τις αλλαγές, ανανεώστε την σελίδα');?>
                </div>
              </div>
            </div>
          </div>          
          <div class="gks_logo_divider"></div>
          <?php } ?>
          

          
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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_settings','delete',0);?>;



jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
  
});
</script>


<link rel="stylesheet" href="/my/js/codemirror-5.65.16/lib/codemirror.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/hint/show-hint.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/fold/foldgutter.css" />
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/display/fullscreen.css">
<script src="/my/js/codemirror-5.65.16/lib/codemirror.js"></script>
<script src="/my/js/codemirror-5.65.16/mode/css/css.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/show-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/css-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/edit/closebrackets.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/selection/active-line.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldcode.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldgutter.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/brace-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/comment-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/display/fullscreen.js"></script>

<script src="js/admin-system-settings.js?v=<?php echo $gks_cache_version;?>"></script>

<link rel="stylesheet" href="/my/js/jquery.fileupload/jquery.fileupload.css" type="text/css">    
<script src="/my/js/jquery.fileupload/vendor/jquery.ui.widget.js"></script>
<script src="/my/js/jquery.fileupload/jquery.iframe-transport.js"></script>
<script src="/my/js/jquery.fileupload/jquery.fileupload.js"></script> 
<script src="/my/js/jquery.fileupload/jquery.fileupload-process.js"></script> 
<script src="/my/js/jquery.fileupload/jquery.fileupload-validate.js"></script> 


<?php
//db_close();
include_once('_my_footer_admin.php');


