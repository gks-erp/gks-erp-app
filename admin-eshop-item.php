<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$nav_active_array=array('manage','manage_eshop');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshops',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_eshops_edit=gks_permission_user_can_action_php($my_wp_user_id,'gks_eshops','edit',0);


//gks_plugins_functions_run('woo_import_coupon_after','1111','2222','3333','4444','5555');




$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshops',['from'=>'item']);


$sql_list="SELECT id_delivery_method as id, delivery_method_name as descr
FROM gks_delivery_methods
WHERE delivery_method_disabled=0 ORDER BY mysortorder";
$result_list = $db_link->query($sql_list);        
if (!$result_list) {debug_mail(false,'error sql',$sql_list);die('sql error');}
$delivery_gks=[];
while ($row_list = $result_list->fetch_assoc()) { 
  $delivery_gks[]=array('id'=>intval($row_list['id']), 'descr' => trim_gks($row_list['descr']));
}

$sql_list="SELECT id_payment_acquirer as id, payment_acquirer_name as descr
FROM gks_payment_acquirers
WHERE payment_acquirer_disabled=0 ORDER BY mysortorder";
$result_list = $db_link->query($sql_list);        
if (!$result_list) {debug_mail(false,'error sql',$sql_list);die('sql error');}
$payment_gks=[];
while ($row_list = $result_list->fetch_assoc()) { 
  $payment_gks[]=array('id'=>intval($row_list['id']), 'descr' => trim_gks($row_list['descr']));
}


if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_eshop']=-1;
  $row['eshop_name']='';
  $row['eshop_url']='';
  $row['company_id']=0;
  $row['company_title']='';
  $row['company_sub_id']=-1;
  $row['company_sub_title']='';
  $row['eshop_key']='';
  $row['eshop_autosync']=0;
  $row['eshop_sortorder']=1000;
  $row['eshop_disable']=0;
  $row['tax_class_basikos']='';
  $row['tax_class_meiomenos']='';
  $row['tax_class_ypermeiomenos']='';
  $row['tax_class_yperypermeiomenos']='';
  $row['tax_class_xorisfpa']='';
  
  $row['order_find_user_from']='afm,mobile,email,phone,user';
  $row['order_meta_user_lang']='';
  $row['order_meta_parastatiko']='';
  $row['order_meta_eponimia']='';
  $row['order_meta_title']='';
  $row['order_meta_afm']='';
  $row['order_meta_doy']='';
  $row['order_meta_epaggelma']='';
  $row['woo_delivery_to_gks']='';
  $row['woo_payment_to_gks']='';
  
  $row['import_yes']=0;
  $row['import_as']='order';
  $row['acc_journal_id']=0;
  $row['acc_journal_id_tim']=0;
  $row['acc_seira_id']=0;
  $row['acc_seira_id_tim']=0;
  $row['warehouses_id_from']=0;
  $row['warehouses_id_from_tim']=0;
  $row['will_update']=0;
  $row['update_if_gks_change']=0;
  $row['update_state_gks_transfer']='010draft,040cancelled,050rejected,070wait_payment,080confirm,100completed,110payment';
  $row['update_state_gks_reservation']='010draft,040cancelled,050rejected,070wait_payment,080confirm,100completed,110payment';
  $row['update_state_gks_order']='005prodraft,010draft,020pending,025offer,030forcancellation,040cancelled,050rejected,055wait_payment,060registered,070inproduction,080failed,090indelivery,095execute,100completed,110payment';
  $row['update_state_gks_acc_inv']='010draft,040cancelled,050proinvoice,080listing,090ekdosi,100payment';
  $row['update_state_woo']='pending,processing,on-hold,completed,cancelled,refunded,failed';
  $row['acc_inv_product_shipping']=0;
  $row['acc_inv_product_fees']=0;
  
  $row['woo_start_booking_number']='';
  $row['wpml_enable']=0;
  $row['wpml_icl_language_code']='';
  $row['wpml_default_lang']='';
  $row['wpml_languages']='';
  $row['woo_version']='';
  $row['woo_currency']='';
  $row['woo_weight_unit']='';
  $row['woo_dimension_unit']='';
  $row['woo_calc_taxes']=0;
  $row['woo_prices_include_tax']=0;
  $row['woo_manage_stock']=0;
  $row['woo_taxes']='';

    
  $my_page_title=gks_lang('Νέο eshop');

  $company_sub_id=0; if (isset($_GET['company_sub_id'])) $company_sub_id=intval($_GET['company_sub_id']);
  if ($company_sub_id>0) {
    $sql="SELECT company_sub_title, company_id, company_title
    FROM gks_company_subs LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
    WHERE id_company_sub=".$company_sub_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==1) {
      $row_user = $result->fetch_assoc();  
      $row['company_sub_id'] =$company_sub_id;
      $row['company_sub_title']=$row_user['company_sub_title'];
      $row['company_id'] =$row_user['company_id'];
      $row['company_title']=$row_user['company_title'];
    }    
       
    
  } else {
    $company_id=0; if (isset($_GET['company_id'])) $company_id=intval($_GET['company_id']);
    if ($company_id>0) {
      $sql="SELECT company_title FROM gks_company WHERE id_company=".$company_id;
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows==1) {
        $row_user = $result->fetch_assoc();  
        $row['company_id'] =$company_id;
        $row['company_title']=$row_user['company_title'];
        $row['company_sub_id']=0;
        $row['company_sub_title']=gks_lang('Κεντρικό');
      }
    }
  }
  

} else {
  $sql ="SELECT gks_eshops.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  gks_company.company_title, gks_company_subs.company_sub_title,

  CASE
    WHEN gks_eshop_products_shipping.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products_shipping.product_descr<>'' THEN
          gks_eshop_products_shipping.product_descr
        ELSE
          CASE
            WHEN gks_eshop_products_shipping.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_shipping_parent.product_descr, gks_eshop_products_shipping.product_descr_variable)
            ELSE
              gks_eshop_products_shipping_parent.product_descr
          END
      END
    ELSE gks_eshop_products_shipping.product_descr
  END as product_shipping_descr_p,
  
  CASE
    WHEN gks_eshop_products_fees.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products_fees.product_descr<>'' THEN
          gks_eshop_products_fees.product_descr
        ELSE
          CASE
            WHEN gks_eshop_products_fees.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_fees_parent.product_descr, gks_eshop_products_fees.product_descr_variable)
            ELSE
              gks_eshop_products_fees_parent.product_descr
          END
      END
    ELSE gks_eshop_products_fees.product_descr
  END as product_fees_descr_p
            
  FROM (((((((gks_eshops 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_eshops.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_eshops.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN gks_company ON gks_eshops.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_eshops.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN gks_eshop_products AS gks_eshop_products_shipping ON gks_eshops.acc_inv_product_shipping = gks_eshop_products_shipping.id_product)
  LEFT JOIN gks_eshop_products AS gks_eshop_products_shipping_parent ON gks_eshop_products_shipping.product_parent_id = gks_eshop_products_shipping_parent.id_product)
  LEFT JOIN gks_eshop_products AS gks_eshop_products_fees ON gks_eshops.acc_inv_product_fees = gks_eshop_products_fees.id_product)
  LEFT JOIN gks_eshop_products AS gks_eshop_products_fees_parent ON gks_eshop_products_fees.product_parent_id = gks_eshop_products_fees_parent.id_product
  



  where id_eshop = ".$id;
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
  $my_page_title=gks_lang('eshop').': '.$row['eshop_name'];
  $object_title=$row['eshop_name'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

if (empty($row['import_as'])) $row['import_as']='order';

stat_record();

$run_guid_eidi='';
$sql_run_guid_eidi="select guid from gks_async_queue 
where mytype='woo' and status='pending' and cmd='get_product' and param1='".$id."' order by id_async_queue limit 1";
$result_run_guid_eidi = $db_link->query($sql_run_guid_eidi);        
if (!$result_run_guid_eidi) {
  debug_mail(false,'error sql',$sql_run_guid_eidi);
  die('sql error');}
if ($result_run_guid_eidi->num_rows>=1) {
  $row_run_guid_eidi = $result_run_guid_eidi->fetch_assoc();
  $run_guid_eidi=$row_run_guid_eidi['guid'];
}
//echo $run_guid_eidi;

$run_guid_coupon='';
$sql_run_guid_coupon="select guid from gks_async_queue 
where mytype='woo' and status='pending' and cmd='get_coupon' and param1='".$id."' order by id_async_queue limit 1";
$result_run_guid_coupon = $db_link->query($sql_run_guid_coupon);        
if (!$result_run_guid_coupon) {
  debug_mail(false,'error sql',$sql_run_guid_coupon);
  die('sql error');}
if ($result_run_guid_coupon->num_rows>=1) {
  $row_run_guid_coupon = $result_run_guid_coupon->fetch_assoc();
  $run_guid_coupon=$row_run_guid_coupon['guid'];
}
//echo $run_guid_coupon;




$woo_delivery_to_gks=[]; if (trim_gks($row['woo_delivery_to_gks'])!='') $woo_delivery_to_gks=unserialize($row['woo_delivery_to_gks']);
$woo_payment_to_gks=[]; if (trim_gks($row['woo_payment_to_gks'])!='') $woo_payment_to_gks=unserialize($row['woo_payment_to_gks']);

//print '<pre>';print_r($woo_delivery_to_gks);print_r($woo_payment_to_gks);die();





include_once('_my_header_admin.php');
?>
<style>
.btn_order_meta_user_lang {
  margin-bottom: 6px;
  padding: 2px 4px;
}

</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('eshop');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('eshop');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          eshop
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>> 
          <div class="form-group row">
            <label for="eshop_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-md-8">
              <input id="eshop_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['eshop_name']);?>">
            </div>
          </div>          
          <div class="form-group row">
            <label for="eshop_url" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('URL');?>:</label>
            <div class="col-md-8">
              <input id="eshop_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['eshop_url']);?>">
            </div>
          </div>          
          <div class="form-group row">
            <label for="company" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <input id="company" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['company_title']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <input id="company_id" type="hidden" value="<?php echo $row['company_id'];?>" class="myneedsave">
            </div>
          </div>
          <div class="form-group row">
            <label for="company_sub_title" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Υποκατάστημα');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_title" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php if ($row['company_sub_id']==0) echo gks_lang('Κεντρικό'); else echo htmlspecialchars_gks($row['company_sub_title']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <input id="company_sub_id" type="hidden" value="<?php echo $row['company_sub_id'];?>" class="myneedsave">
            </div>
          </div>
          <div class="form-group row">
            <label for="eshop_key" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κλειδί');?>:</label>
            <div class="col-md-8">
              <input id="eshop_key" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['eshop_key']);?>">
              <small>
                <?php echo gks_lang('Θα πρέπει να έχει μήκος τουλάχιστον 32 χαρακτήρες');?><br>
                <span id="eshop_key_new" class="btn btn-primary btn-sm"><?php echo gks_lang('Δημιουργία τυχαίου');?></span>
              </small>
            </div>
          </div> 
          <div class="form-group row">
            <label for="eshop_autosync" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αυτόματος συγχρονισμός');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eshop_autosync" value="1" <?php if ($row['eshop_autosync']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>           
          <div class="form-group row">
            <label for="eshop_sortorder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-md-8">
              <input id="eshop_sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['eshop_sortorder'];?>" min="0" step="1">
            </div>
          </div> 

          <div class="form-group row">
            <label for="eshop_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="eshop_disable" value="1" <?php if ($row['eshop_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          

        </div>
      </div>

         
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εισαγωγή παραγγελίας από WooCommerce');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('import');?>> 
          <div class="form-group row">
            <label for="import_yes" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Να γίνεται αυτόματη εισαγωγή στο WooCommerce');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="import_yes" value="1" <?php if ($row['import_yes']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0) echo 'display:none;';?>">
            <label for="import_as" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Να γίνεται εισαγωγή ως');?>:</label>
            <div class="col-md-8">
              <?php if (defined('GKS_TRANSFER') and GKS_TRANSFER) {?>
              <input type="radio" name="import_as" id="import_as_transfer"   value="transfer"   <?php if ($row['import_as']=='transfer')  echo ' checked '; ?> class="myneedsave">
              <label for="import_as_transfer"   style="font-size: 0.875rem;margin-bottom:0px;display: inline;"><?php echo gks_lang('Κράτηση transfer');?></label>
              <br>
              <?php } ?>
              <?php if ($GKS_HOTEL_RESERVATIONS_ONLINE) {?>
              <input type="radio" name="import_as" id="import_as_reservation"   value="reservation"   <?php if ($row['import_as']=='reservation')  echo ' checked '; ?> class="myneedsave">
              <label for="import_as_reservation"   style="font-size: 0.875rem;margin-bottom:0px;display: inline;"><?php echo gks_lang('Κράτηση ξενοδοχείου');?></label>
              <br>
              <?php } ?>
              
              
              <input type="radio" name="import_as" id="import_as_order"   value="order"   <?php if ($row['import_as']=='order')  echo ' checked '; ?> class="myneedsave">
              <label for="import_as_order"   style="font-size: 0.875rem;margin-bottom:0px;display: inline;"><?php echo gks_lang('Παραγγελία');?></label>
              <br>
              <input type="radio" name="import_as" id="import_as_acc_inv" value="acc_inv" <?php if ($row['import_as']=='acc_inv') echo ' checked '; ?> class="myneedsave">
              <label for="import_as_acc_inv" style="font-size: 0.875rem;margin-bottom:0px;display: inline;"><?php echo gks_lang('Παραστατικό');?></label>
            </div>
          </div>
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0) echo 'display:none;';?>">
            <label for="acc_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?><span class="sh_import_yes_import_as_acc_inv_ins"> <?php echo gks_lang('για απόδειξη');?></span>:</label>
            <div class="col-md-8">
              <select id="acc_journal_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $div_warehouses_id_from_hide=true;
                $sql_list="SELECT gks_acc_journal.id_acc_journal AS id, gks_acc_journal.acc_journal_descr AS descr, 
                gks_acc_journal.company_id AS c, gks_acc_journal.company_sub_id AS cs, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as t,
                gks_acc_journal.acc_eidos_parastatikou_whi_id AS w
                FROM gks_acc_journal 
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                WHERE gks_acc_journal.is_disable=0 
                AND gks_acc_eidi_parastatikon.eidos_parastatikou_type_id In (1,31,1100,2100)
                ORDER BY gks_acc_journal.sortorder;";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id']==$row['acc_journal_id'] ? 'selected' : '').' value="'.$row_list['id'].'" data-c="'.$row_list['c'].'" data-cs="'.$row_list['cs'].'" data-t="'.$row_list['t'].'" data-w="'.$row_list['w'].'">'.$row_list['descr'].'</option>';
                  if ($row_list['id']==$row['acc_journal_id'] && $row_list['w']>0) $div_warehouses_id_from_hide=false;
                }?>
              </select>
            </div>
          </div>
          

          
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0) echo 'display:none;';?>">
            <label for="acc_seira_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά');?><span class="sh_import_yes_import_as_acc_inv_ins"> <?php echo gks_lang('για απόδειξη');?></span>:</label>
            <div class="col-md-8">
              <select id="acc_seira_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $sql_list="SELECT id_acc_seira as id, seira_descr as descr, acc_journal_id as j
                FROM gks_acc_seires
                WHERE is_disable=0
                ORDER BY sortorder";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id']==$row['acc_seira_id'] ? 'selected' : '').' value="'.$row_list['id'].'" data-j="'.$row_list['j'].'">'.$row_list['descr'].'</option>';
                }?>
              </select>
            </div>
          </div>
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0 or $div_warehouses_id_from_hide) echo 'display:none;';?>" id="div_warehouses_id_from">
            <label for="warehouses_id_from" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποθήκη');?><span class="sh_import_yes_import_as_acc_inv_ins"> <?php echo gks_lang('για απόδειξη');?></span>:</label>
            <div class="col-md-8">
              <select id="warehouses_id_from" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $sql_list="SELECT id_warehouse as id, warehouse_name as descr, 
                company_id as c, company_sub_id as cs
                FROM gks_warehouses
                WHERE warehouse_disable=0
                ORDER BY warehouse_sortorder";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id']==$row['warehouses_id_from'] ? 'selected' : '').' value="'.$row_list['id'].'" data-c="'.$row_list['c'].'" data-cs="'.$row_list['cs'].'">'.$row_list['descr'].'</option>';
                }?>
              </select>
            </div>
          </div>
          

          <div class="form-group row sh_import_yes_import_as_acc_inv" style="<?php if ($row['import_yes']==0 or $row['import_as']!='acc_inv') echo 'display:none;';?>">
            <label for="acc_journal_id_tim" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο για τιμολόγιο');?>:</label>
            <div class="col-md-8">
              <select id="acc_journal_id_tim" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $div_warehouses_id_from_tim_hide=true;
                $sql_list="SELECT gks_acc_journal.id_acc_journal AS id, gks_acc_journal.acc_journal_descr AS descr, 
                gks_acc_journal.company_id AS c, gks_acc_journal.company_sub_id AS cs, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as t,
                gks_acc_journal.acc_eidos_parastatikou_whi_id AS w
                FROM gks_acc_journal 
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                WHERE gks_acc_journal.is_disable=0 
                AND gks_acc_eidi_parastatikon.eidos_parastatikou_type_id In (1,31,1100,2100)
                ORDER BY gks_acc_journal.sortorder;";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id']==$row['acc_journal_id_tim'] ? 'selected' : '').' value="'.$row_list['id'].'" data-c="'.$row_list['c'].'" data-cs="'.$row_list['cs'].'" data-t="'.$row_list['t'].'" data-w="'.$row_list['w'].'">'.$row_list['descr'].'</option>';
                  if ($row_list['id']==$row['acc_journal_id_tim'] && $row_list['w']>0) $div_warehouses_id_from_tim_hide=false;
                }?>
              </select>
            </div>
          </div>          

          <div class="form-group row sh_import_yes_import_as_acc_inv" style="<?php if ($row['import_yes']==0 or $row['import_as']!='acc_inv') echo 'display:none;';?>">
            <label for="acc_seira_id_tim" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά για τιμολόγιο');?>:</label>
            <div class="col-md-8">
              <select id="acc_seira_id_tim" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $sql_list="SELECT id_acc_seira as id, seira_descr as descr, acc_journal_id as j
                FROM gks_acc_seires
                WHERE is_disable=0
                ORDER BY sortorder";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id']==$row['acc_seira_id_tim'] ? 'selected' : '').' value="'.$row_list['id'].'" data-j="'.$row_list['j'].'">'.$row_list['descr'].'</option>';
                }?>
              </select>
            </div>
          </div>
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0 or $row['import_as']!='acc_inv' or $div_warehouses_id_from_tim_hide) echo 'display:none;';?>" id="div_warehouses_id_from_tim">
            <label for="warehouses_id_from_tim" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποθήκη για τιμολόγιο');?>:</label>
            <div class="col-md-8">
              <select id="warehouses_id_from_tim" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $sql_list="SELECT id_warehouse as id, warehouse_name as descr, 
                company_id as c, company_sub_id as cs
                FROM gks_warehouses
                WHERE warehouse_disable=0
                ORDER BY warehouse_sortorder";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id']==$row['warehouses_id_from_tim'] ? 'selected' : '').' value="'.$row_list['id'].'" data-c="'.$row_list['c'].'" data-cs="'.$row_list['cs'].'">'.$row_list['descr'].'</option>';
                }?>
              </select>
            </div>
          </div>          
          
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0) echo 'display:none;';?>">
            <label for="will_update" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενημέρωση');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="will_update" id="will_update_no"  value="0" <?php if ($row['will_update']==0)  echo ' checked '; ?> class="myneedsave">
              <label for="will_update_no"  style="font-size: 0.875rem;margin-bottom:0px;display: inline;"><?php echo gks_lang('Να γίνει μόνο εισαγωγή στο gks ERP κατά την προσθήκη της παραγγελίας στο WooCommerce');?></label>
              <br>
              <input type="radio" name="will_update" id="will_update_yes" value="1" <?php if ($row['will_update']==1) echo ' checked '; ?> class="myneedsave">
              <label for="will_update_yes" style="font-size: 0.875rem;margin-bottom:0px;display: inline;"><?php echo gks_lang('Να ενημερώνεται η παραγγελία/παρασταστικό στο gks ERP κάθε φορά που αλλάζει η παραγγελία στο WooCommerce');?></label>
            </div>
          </div>
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0 ) echo 'display:none;';?>">
            <label for="update_if_gks_change" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενημέρωση μετά από το gks ERP');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="update_if_gks_change" id="update_if_gks_change_no"  value="0" <?php if ($row['update_if_gks_change']==0)  echo ' checked '; ?> class="myneedsave">
              <label for="update_if_gks_change_no"  style="font-size: 0.875rem;margin-bottom:0px;display: inline;"><?php echo gks_lang('Εάν γίνει ενημέρωση της παραγγελίας/παρασταστικού στο gks ERP να μην ενημερωθεί ξανά από το WooCommerce');?></label>
              <br>
              <input type="radio" name="update_if_gks_change" id="update_if_gks_change_yes" value="1" <?php if ($row['update_if_gks_change']==1) echo ' checked '; ?> class="myneedsave">
              <label for="update_if_gks_change_yes" style="font-size: 0.875rem;margin-bottom:0px;display: inline;"><?php echo gks_lang('Να ενημερώνεται η παραγγελία/παρασταστικό κάθε φορά που αλλάζει η παραγγελία στο WooCommerce <small class="text-muted">(δεν συνιστάται)</small>');?></label>
            </div>
          </div>
          <div class="form-group row sh_import_yes_import_as_transfer" style="<?php if ($row['import_yes']==0 or $row['import_as']!='transfer') echo 'display:none;';?>">
            <label for="update_state_gks_transfer" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση στο gks ERP');?>:</label>
            <div class="col-md-8">
              <?php
              if (defined('GKS_TRANSFER') and GKS_TRANSFER) {
                $parts=explode(',',trim_gks($row['update_state_gks_transfer']));
                $temp=array();
                foreach ($parts as $value) {
                  $temp[]=getTransferReservationStatusDescr($value);
                } 
                $temp=implode(',',$temp);
              }
              ?>
              <input id="update_state_gks_transfer" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $temp;?>">
              
              
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Εάν η κατάσταση της κράτησης στο gks ERP είναι μία από τις παραπάνω, τότε θα μπορεί να ενημερωθεί από το WooCommerce');?>
              </small>
            </div>
          </div>
                                              
          <div class="form-group row sh_import_yes_import_as_reservation" style="<?php if ($row['import_yes']==0 or $row['import_as']!='reservation') echo 'display:none;';?>">
            <label for="update_state_gks_reservation" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση στο gks ERP');?>:</label>
            <div class="col-md-8">
              <?php
              $parts=explode(',',trim_gks($row['update_state_gks_reservation']));
              $temp=array();
              foreach ($parts as $value) {
                $temp[]=getHotelReservationStatusDescr($value);
              } 
              $temp=implode(',',$temp);
              ?>
              <input id="update_state_gks_reservation" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $temp;?>">
              
              
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Εάν η κατάσταση της κράτησης στο gks ERP είναι μία από τις παραπάνω, τότε θα μπορεί να ενημερωθεί από το WooCommerce');?>
              </small>
            </div>
          </div>
      
          <div class="form-group row sh_import_yes_import_as_order" style="<?php if ($row['import_yes']==0 or $row['import_as']!='order') echo 'display:none;';?>">
            <label for="update_state_gks_order" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση στο gks ERP');?>:</label>
            <div class="col-md-8">
              <?php
              $parts=explode(',',trim_gks($row['update_state_gks_order']));
              $temp=array();
              foreach ($parts as $value) {
                $temp[]=getOrderStateDescr($value);
              } 
              $temp=implode(',',$temp);
              ?>
              <input id="update_state_gks_order" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $temp;?>">
              
              
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Εάν η κατάσταση της παραγγελίας στο gks ERP είναι μία από τις παραπάνω, τότε θα μπορεί να ενημερωθεί από το WooCommerce');?>
              </small>
            </div>
          </div>
          <div class="form-group row sh_import_yes_import_as_acc_inv" style="<?php if ($row['import_yes']==0 or $row['import_as']!='acc_inv') echo 'display:none;';?>">
            <label for="update_state_gks_acc_inv" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση στο gks ERP');?>:</label>
            <div class="col-md-8">
              <?php
              $parts=explode(',',trim_gks($row['update_state_gks_acc_inv']));
              $temp=array();
              foreach ($parts as $value) {
                $temp[]=getAccInvStateDescr($value);
              } 
              $temp=implode(',',$temp);
              ?>
              <input id="update_state_gks_acc_inv" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $temp;?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Εάν η κατάσταση του παρασταστικού στο gks ERP είναι μία από τις παραπάνω, τότε θα μπορεί να ενημερωθεί από το WooCommerce');?>
              </small>
            </div>
          </div>          
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0) echo 'display:none;';?>">
            <label for="update_state_woo" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση στο WooCommerce');?>:</label>
            <div class="col-md-8">
              <?php
              $parts=explode(',',trim_gks($row['update_state_woo']));
              $temp=array();
              foreach ($parts as $value) {
                $temp[]=gks_woo_order_state_descr($value);
              } 
              $temp=implode(',',$temp);
              ?>
              <input id="update_state_woo" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $temp;?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Εάν η κατάσταση της παραγγελίας στο WooCommerce είναι μία από τις παραπάνω, τότε θα μπορεί να ενημερωθεί από το WooCommerce');?>
              </small>
            </div>
          </div>

          
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0) echo 'display:none;';?>">
            <label for="acc_inv_product_shipping" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έξοδα αποστολής');?>:</label>
            <div class="col-md-8">
              <input id="acc_inv_product_shipping" type="text" class="form-control form-control-sm myneedsave" 
              data-id="<?php echo $row['acc_inv_product_shipping'];?>" value="<?php if (isset($row['product_shipping_descr_p'])) echo $row['product_shipping_descr_p'];?>"
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Το είδος που θα χρησιμοποιηθεί για τα έξοδα αποστολής');?>
              </small>
            </div>
          </div>
          <div class="form-group row sh_import_yes" style="<?php if ($row['import_yes']==0) echo 'display:none;';?>">
            <label for="acc_inv_product_fees" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Άλλα έξοδα');?>:</label>
            <div class="col-md-8">
              <input id="acc_inv_product_fees" type="text" class="form-control form-control-sm myneedsave" 
              data-id="<?php echo $row['acc_inv_product_fees'];?>" value="<?php if (isset($row['product_fees_descr_p'])) echo $row['product_fees_descr_p'];?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Το είδος που θα χρησιμοποιηθεί για τα άλλα έξοδα, π.χ. έξοδα πληρωμής');?>
              </small>
            </div>
          </div>

          <div class="form-group row">
            <label for="woo_start_booking_number" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πρόθεμα Κράτησης');?>:</label>
            <div class="col-md-8">
              <input id="woo_start_booking_number" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['woo_start_booking_number']);?>">
            </div>
          </div>          
      
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ρυθμίσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('sett');?>> 
          <?php if ($perm_eshops_edit) {?>
          <div class="form-group row">
            <div class="col-md-12 text-center">
              <button type="button" class="btn btn-sm btn-primary" id="eshop_get_settings"><?php echo gks_lang('Λήψη ρυθμίσεων');?></button> 
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-12 text-center" id="div_eshop_settings">
            <?php   
            if ($id>0) { 
              $html='';
              
              $html.='WPML: <b>'.((isset($row['wpml_enable']) and $row['wpml_enable']==1) ? gks_lang('Ναι') : gks_lang('Όχι')).'</b><br>';
              $html.='WPML ICL_LANGUAGE_CODE: <b>'.$row['wpml_icl_language_code'].'</b><br>'; 
              if (isset($row['wpml_languages']) and $row['wpml_languages']!='') {
                $wpml_languages=unserialize($row['wpml_languages']);
                $temp=[];
                foreach ($wpml_languages as $value) {
                  $temp[]=$value['translated_name'] .' ('.$value['language_code'].')';
                }
                if (count($temp)>0) {
                  $html.=gks_lang('WPML Γλώσσες').': <b>'.implode(', ',$temp).'</b><br>'; 
                } 
              }
              if ($row['wpml_default_lang']!='') {
                 $html.=gks_lang('WPML Προεπιλεγμένη γλώσσα').': <b>'.$row['wpml_default_lang'].'</b><br>'; 
              }

              $html.=gks_lang('Έκδοση WooCommerce').': <b>'.$row['woo_version'].'</b><br>';
              $html.=gks_lang('Νόμισμα').': <b>'.$row['woo_currency'].'</b><br>';
              $html.=gks_lang('Μονάδα Βάρους').': <b>'.$row['woo_weight_unit'].'</b><br>';
              $html.=gks_lang('Μονάδα διαστάσεων').': <b>'.$row['woo_dimension_unit'].'</b><br>';
              $html.=gks_lang('Ενεργοποίηση φόρων').': <b>'.($row['woo_calc_taxes']==1 ? gks_lang('Ναι') : gks_lang('Όχι')).'</b><br>';
              $html.=gks_lang('Οι Τιμές Εισάγονται Με Φόρο').': <b>'.($row['woo_prices_include_tax']==1 ? gks_lang('Ναι') : gks_lang('Όχι')).'</b><br>';
              $html.=gks_lang('Διαχείριση του αποθέματος').': <b>'.($row['woo_manage_stock']==1 ? gks_lang('Ναι') : gks_lang('Όχι')).'</b><br>';
              if (isset($row['woo_taxes'])) {
                $wootaxes=unserialize($row['woo_taxes']);
                $html.=gks_lang('Κλάσεις ΦΠΑ').': <b>'.(count($wootaxes)==0 ? '' : implode(', ',$wootaxes)).'</b><br>';
              }
              
              if ($html!='') {
                $html=substr($html, 0, strlen($html)-4);
                echo $html;
                
              }
            }
            ?>
            </div>
          </div>
          
          <?php }?>
          
          <div class="form-group row" style="background-color: rgba(0, 0, 0, 0.03);border-top: 1px solid rgba(0, 0, 0, 0.125);border-bottom: 1px solid rgba(0, 0, 0, 0.125);padding: 11px 0px;margin: 16px -20px;">
            <div class="col-md-12 text-center">
              <?php echo gks_lang('Αντιστοίχιση κλάσεων ΦΠΑ με το WooCommerce');?>  
            </div>
          </div>
          <div class="form-group row table-dark" style="font-size: 0.875rem;font-weight: bold;padding: 6px;border-radius: 16px;">
            <div class="col-md-6 text-md-right ">
              <?php echo gks_lang('gks ERP');?>
            </div>
            <div class="col-md-6 ">
              <?php echo gks_lang('WooCommerce');?>
            </div>
          </div>
                    
          <div class="form-group row">
            <label for="tax_class_basikos" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΦΠΑ Κανονικός');?>:</label>
            <div class="col-md-6">
              <select id="tax_class_basikos" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php if ($row['tax_class_basikos']!='') echo '<option selected value="'.$row['tax_class_basikos'].'">'.$row['tax_class_basikos'].'</option>';?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="tax_class_meiomenos" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΦΠΑ Μειωμένος');?>:</label>
            <div class="col-md-6">
              <select id="tax_class_meiomenos" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php if ($row['tax_class_meiomenos']!='') echo '<option selected value="'.$row['tax_class_meiomenos'].'">'.$row['tax_class_meiomenos'].'</option>';?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="tax_class_ypermeiomenos" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΦΠΑ Υπερμειωμένος');?>:</label>
            <div class="col-md-6">
              <select id="tax_class_ypermeiomenos" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php if ($row['tax_class_ypermeiomenos']!='') echo '<option selected value="'.$row['tax_class_ypermeiomenos'].'">'.$row['tax_class_ypermeiomenos'].'</option>';?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="tax_class_yperypermeiomenos" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΦΠΑ Υπερ-υπερμειωμένος');?>:</label>
            <div class="col-md-6">
              <select id="tax_class_yperypermeiomenos" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php if ($row['tax_class_yperypermeiomenos']!='') echo '<option selected value="'.$row['tax_class_yperypermeiomenos'].'">'.$row['tax_class_yperypermeiomenos'].'</option>';?>
              </select>
            </div>
          </div>
                    
          <div class="form-group row">
            <label for="tax_class_xorisfpa" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΦΠΑ Χωρίς ΦΠΑ');?>:</label>
            <div class="col-md-6">
              <select id="tax_class_xorisfpa" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php if ($row['tax_class_xorisfpa']!='') echo '<option selected value="'.$row['tax_class_xorisfpa'].'">'.$row['tax_class_xorisfpa'].'</option>';?>
              </select>
            </div>
          </div>

          <div class="form-group row" style="background-color: rgba(0, 0, 0, 0.03);border-top: 1px solid rgba(0, 0, 0, 0.125);border-bottom: 1px solid rgba(0, 0, 0, 0.125);padding: 11px 0px;margin: 16px -20px;">
            <div class="col-md-12 text-center">
              <?php echo gks_lang('Αντιστοίχιση τρόπων αποστολής με το WooCommerce');?>
            </div>
          </div>
          <div class="form-group row table-dark" style="font-size: 0.875rem;font-weight: bold;padding: 6px;border-radius: 16px;">
            <div class="col-md-6 text-md-right ">
              <?php echo gks_lang('gks ERP');?>
            </div>
            <div class="col-md-6 ">
              <?php echo gks_lang('WooCommerce');?>
            </div>
          </div>
          <div id="delivery_one2one_div">
              
          <?php
          foreach ($woo_delivery_to_gks as $aa => $mval) {
            echo 
            '<div class="form-group row">'.
              '<div class="col-md-6">'.
                '<select class="form-control form-control-sm myneedsave delivery_one2one_gks" data-aa="'.$aa.'">'.
                  '<option value="0"></option>';
                  foreach ($delivery_gks as $value) {
                    echo '<option value="'.$value['id'].'" '.($value['id']==$mval['g'] ? 'selected' : '').'>'.$value['descr'].'</option>';
                  }
            echo     
                '</select>'.
              '</div>'.
              '<div class="col-md-6">'.
                '<span class="form-control form-control-sm myneedsave delivery_one2one_woo" data-aa="'.$aa.'" data-id="'.$mval['w'].'">'.
                  $mval['wt'].
                '</span>'.
              '</div>'.
            '</div>';
          } 
          ?>
          </div>
          <div class="form-group row" style="background-color: rgba(0, 0, 0, 0.03);border-top: 1px solid rgba(0, 0, 0, 0.125);border-bottom: 1px solid rgba(0, 0, 0, 0.125);padding: 11px 0px;margin: 16px -20px;">
            <div class="col-md-12 text-center">
              <?php echo gks_lang('Αντιστοίχιση τρόπων πληρωμής με το WooCommerce');?>  
            </div>
          </div>          
          <div class="form-group row table-dark" style="font-size: 0.875rem;font-weight: bold;padding: 6px;border-radius: 16px;">
            <div class="col-md-6 text-md-right ">
              <?php echo gks_lang('gks ERP');?>
            </div>
            <div class="col-md-6 ">
              <?php echo gks_lang('WooCommerce');?>
            </div>
          </div>
          <div id="payment_one2one_div">
          <?php
          foreach ($woo_payment_to_gks as $aa => $mval) {
            echo 
            '<div class="form-group row">'.
              '<div class="col-md-6">'.
                '<select class="form-control form-control-sm myneedsave payment_one2one_gks" data-aa="'.$aa.'">'.
                  '<option value="0"></option>';
                  foreach ($payment_gks as $value) {
                    echo '<option value="'.$value['id'].'" '.($value['id']==$mval['g'] ? 'selected' : '').'>'.$value['descr'].'</option>';
                  }
            echo     
                '</select>'.
              '</div>'.
              '<div class="col-md-6">'.
                '<span class="form-control form-control-sm myneedsave payment_one2one_woo" data-aa="'.$aa.'" data-id="'.$mval['w'].'">'.
                  $mval['wt'].
                '</span>'.
              '</div>'.
            '</div>';
          } 
          ?>
          </div>
        </div>          
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ειδικές Ρυθμίσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('spe_sett');?>> 
          <div class="col-sm-12 text-center" style="background-color1: rgba(0, 0, 0, 0.03);border-radius: 10px 10px 0px 0px;border1: 1px solid #bbbbbb;">
            <small class="form-text text-muted" style="margin: 12px;">
              <?php echo gks_lang('Ορισμός των ειδικών πεδίων του WooCommerce και προεπιλογών κατά τη εισαγωγή παραγγελίας');?>
            </small>
          </div>          
          
          <div class="form-group row">
            <label for="order_find_user_from" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εντοπισμός Επαφής Παραγγελίας');?>:</label>
            <div class="col-md-8">
              <?php
              $parts=explode(',',trim_gks($row['order_find_user_from']));
              $temp=array();
              foreach ($parts as $value) {
                switch ($value) {   
                  case 'afm': $temp[]=gks_lang('ΑΦΜ');break;      
                  case 'mobile': $temp[]=gks_lang('Κινητό');break;      
                  case 'email': $temp[]=gks_lang('email');break;      
                  case 'phone': $temp[]=gks_lang('Σταθερό');break;      
                  case 'user': $temp[]=gks_lang('Επαφή Παραγγελίας');break;      
                }
              } 
              $temp=implode(',',$temp);
              ?>
              <input id="order_find_user_from" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $temp;?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Ορισμός των πεδίων και της σειράς που θα χρησιμοποιηθούν για να εντοπιστεί η επαφή ως συνδεδεμένος πελάτης για την παραγγελία');?>
              </small>
            </div>
          </div>
          <div class="form-group row">
            <label for="order_meta_user_lang" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γλώσσα Επαφής Παραγγελίας');?>:</label>
            <div class="col-md-8">
              <input id="order_meta_user_lang" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['order_meta_user_lang']);?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Ορισμός του τρόπου που εντοπιστεί η γλώσσα της επαφής για την παραγγελία με έναν από τους παρακάτω 2 τρόπους');?>:<br>
                <?php echo gks_lang('<b>1)</b> Ορισμός του ονόματος του Ειδικού Πεδίου από την παραγγελία στο WooCommerce');?><br>
                <?php echo gks_lang('Αυτό το ειδικό πεδίο στην παραγγελία θα πρέπει να έχει τιμές μία από τις παραπάτω τιμές');?>:<br>
                <?php
                $sql_list="SELECT id_lang, lang_name FROM gks_lang order by lang_sortorder,lang_name;";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                $temp1='';
                $temp2='';
                while ($row_list= $result_list->fetch_assoc()) { 
                  $temp1.= '<span class="tooltipster" title="'.$row_list['lang_name'].'" style="font-weight: bold;">'.$row_list['id_lang'].'</span> '.gks_lang('ή').' ';
                  
                  $temp2.= '<button class="btn btn-sm btn-primary tooltipster btn_order_meta_user_lang" title="'.$row_list['lang_name'].'">def:'.$row_list['id_lang'].'</button> ';
                }
                if (strlen($temp1)>0) $temp1=substr($temp1, 0, strlen($temp1)-3);
                echo $temp1;
                ?><br>
                <?php echo gks_lang('<b>2)</b> Ορισμός γλώσσας στην παραγγελία. Επιλέξτε μία από τις παρακάτω τιμές');?>:<br>
                <?php echo $temp2;?>
              </small>
              
            </div>
          </div>
          <div class="form-group row">
            <label for="order_meta_parastatiko" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Απόδειξη ή Τιμολόγιο');?>:</label>
            <div class="col-md-8">
              <input id="order_meta_parastatiko" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['order_meta_parastatiko']);?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Ορισμός του τρόπου που θα εντοπιστεί εάν στην παραγγελία θα εκδοθεί απόδειξη ή τιμολόγιο');?>:<br>
                <?php echo gks_lang('<b>1)</b> Ορισμός απόδειξης ή τιμολόγιο');?>: <br>
                <button class="btn btn-sm btn-primary tooltipster btn_order_meta_parastatiko" title="<?php echo gks_lang('Απόδειξη');?>">def:0</button>
                <button class="btn btn-sm btn-primary tooltipster btn_order_meta_parastatiko" title="<?php echo gks_lang('Τιμολόγιο');?>">def:1</button><br>
                <?php echo gks_lang('<b>2)</b> Εάν υπάρχει ΑΦΜ τότε να είναι τιμολόγιο');?>:<br>
                <button class="btn btn-sm btn-primary tooltipster btn_order_meta_parastatiko" title="<?php echo gks_lang('Εάν υπάρχει ΑΦΜ τότε να είναι τιμολόγιο');?>">if_afm</button><br>
                <?php echo gks_lang('<b>3)</b> Εάν υπάρχει Επωνυμία τότε να είναι τιμολόγιο');?>:<br>
                <button class="btn btn-sm btn-primary tooltipster btn_order_meta_parastatiko" title="'<?php echo gks_lang('Εάν υπάρχει Επωνυμία τότε να είναι τιμολόγιο');?>">if_eponimia</button><br>
                <?php echo gks_lang('<b>4)</b> Ορισμός του ονόματος του Ειδικού Πεδίου από την παραγγελία στο WooCommerce');?><br>
                <?php echo gks_lang('Αυτό το ειδικό πεδίο στην παραγγελία θα πρέπει να έχει τιμές');?>:<br>
                <?php echo gks_lang('<b>1</b> ή <b>yes</b> τότε θα είναι τιμολόγιο, διαφορετικά θα είναι απόδειξη');?>
              </small>  
            </div>
          </div>
          <div class="form-group row">
            <label for="order_meta_eponimia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επωνυμία');?>:</label>
            <div class="col-md-8">
              <input id="order_meta_eponimia" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['order_meta_eponimia']);?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Ορισμός του ονόματος του Ειδικού Πεδίου από την παραγγελία στο WooCommerce το οποίο θα περιέχει την Επωνυμία');?>
              </small>
            </div>
          </div>
          <div class="form-group row">
            <label for="order_meta_title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τίτλος');?>:</label>
            <div class="col-md-8">
              <input id="order_meta_title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['order_meta_title']);?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Ορισμός του ονόματος του Ειδικού Πεδίου από την παραγγελία στο WooCommerce το οποίο θα περιέχει τον Τίτλο');?><br>
                <?php echo gks_lang('Αυτή η ρύθμιση υπερτερεί από την εταιρεία που υπάρχει στην διεύθυνση χρέωσης');?>
              </small>
            </div>
          </div>
          <div class="form-group row">
            <label for="order_meta_afm" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
            <div class="col-md-8">
              <input id="order_meta_afm" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['order_meta_afm']);?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Ορισμός του ονόματος του Ειδικού Πεδίου από την παραγγελία στο WooCommerce το οποίο θα περιέχει τον ΑΦΜ');?>
              </small>
            </div>
          </div>
          <div class="form-group row">
            <label for="order_meta_doy" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
            <div class="col-md-8">
              <input id="order_meta_doy" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['order_meta_doy']);?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Ορισμός του ονόματος του Ειδικού Πεδίου από την παραγγελία στο WooCommerce το οποίο θα περιέχει την ΔΟΥ');?>
              </small>
            </div>
          </div>
          <div class="form-group row">
            <label for="order_meta_epaggelma" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
            <div class="col-md-8">
              <input id="order_meta_epaggelma" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['order_meta_epaggelma']);?>">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Ορισμός του ονόματος του Ειδικού Πεδίου από την παραγγελία στο WooCommerce το οποίο θα περιέχει το Επάγγελμα');?>
              </small>
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
          

<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_eshop'];?>" data-model="gks_eshops" data-backurl="admin-eshop.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

      <?php 
      echo getObjectRels('gks_eshops',$id);
      echo getActivityObjectTable('gks_eshops',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_eshops','id'=>$id));
      echo $obj_fileslist['html'];
      ?>


      <?php if ($perm_eshops_edit) {?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ενέργειες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('actions');?>>      

          <div id="eshop_get_eidi_div">
            <div class="form-group row">
              <div class="col-md-12 text-center">
                <button type="button" class="btn btn-sm btn-primary" id="eshop_get_eidi" <?php if ($run_guid_eidi!='') echo 'disabled';?>><?php echo gks_lang('Λήψη ειδών');?></button> 
              </div>
            </div>
            <div class="form-group row" id="div_gks_run_guid_eidi" style="<?php if ($run_guid_eidi=='') echo 'display:none;';?>">
              <div class="col-md-12 text-center">
                <div class="progress" style="height: 20px;">
                  <div id="gks_run_guid_eidi" class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>          
              </div>
              <div class="col-md-12 text-center" style="font-size: 0.875rem;">
                <div>
                  <?php echo gks_lang('Είδη');?>: <span id="gks_run_guid_eidi_cc">--</span>
                </div>
                <div>
                  <?php echo gks_lang('Σφάλματα');?>: <span id="gks_run_guid_eidi_errors">--</span>
                </div>
              </div>
            </div>
            <?php
            $run_guid_eidi
            ?>
          </div>

          <div id="eshop_get_coupon_div">
            <div class="form-group row">
              <div class="col-md-12 text-center">
                <button type="button" class="btn btn-sm btn-primary" id="eshop_get_coupon" <?php if ($run_guid_coupon!='') echo 'disabled';?>><?php echo gks_lang('Λήψη κουπονιών');?></button> 
              </div>
            </div>
            <div class="form-group row" id="div_gks_run_guid_coupon" style="<?php if ($run_guid_coupon=='') echo 'display:none;';?>">
              <div class="col-md-12 text-center">
                <div class="progress" style="height: 20px;">
                  <div id="gks_run_guid_coupon" class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>          
              </div>
              <div class="col-md-12 text-center" style="font-size: 0.875rem;">
                <div>
                  <?php echo gks_lang('Κουπόνια');?>: <span id="gks_run_guid_coupon_cc">--</span>
                </div>
                <div>
                  <?php echo gks_lang('Σφάλματα');?>: <span id="gks_run_guid_coupon_errors">--</span>
                </div>
              </div>
            </div>
            <?php
            $run_guid_coupon
            ?>
          </div>          
          
        </div>
      </div>
      
      <?php } ?>
    </div>

    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_eshop']>0) echo $row['id_eshop'];?></span></div>
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


var from_php_dialog_object_rel_curr='gks_eshops';
var from_php_activity_model='gks_eshops';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;
var from_php_run_guid_eidi='<?php echo $run_guid_eidi;?>';
var from_php_run_guid_coupon='<?php echo $run_guid_coupon;?>';



var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshops','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshops','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshops','delete',$id);?>;


var from_php_delivery_gks=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($delivery_gks));?>'));
var from_php_payment_gks=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($payment_gks));?>'));

var from_php_last_woo_delivery_to_gks=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($woo_delivery_to_gks));?>'));
var from_php_last_woo_payment_to_gks= JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($woo_payment_to_gks));?>'));


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
    //var tag = e.target.tagName.toLowerCase();
    
    
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      //console.log(event.ctrlKey);
      //console.log(event.which);
      event.preventDefault();
      event.stopPropagation();
      
      elem=$('#submit_button_ok');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
      
    }  
    
  });
  
  function get_from_php_last_woo() {
    from_php_last_woo_delivery_to_gks=[];
    $('.delivery_one2one_gks').each(function() {
      aa=$(this).attr('data-aa');
      valgks=$(this).val();
      valwoo=$('.delivery_one2one_woo[data-aa=' + aa + ']').attr('data-id');
      valwoot=$('.delivery_one2one_woo[data-aa=' + aa + ']').text();
      item={};
      item.g=valgks;
      item.w=valwoo;
      item.wt=valwoot;
      from_php_last_woo_delivery_to_gks.push(item);
    });
    //console.log(from_php_last_woo_delivery_to_gks);
    
    from_php_last_woo_payment_to_gks=[];
    $('.payment_one2one_gks').each(function() {
      aa=$(this).attr('data-aa');
      valgks=$(this).val();
      valwoo=$('.payment_one2one_woo[data-aa=' + aa + ']').attr('data-id');
      valwoot=$('.payment_one2one_woo[data-aa=' + aa + ']').text();
      item={};
      item.g=valgks;
      item.w=valwoo;
      item.wt=valwoot;
      from_php_last_woo_payment_to_gks.push(item);
    });

    //console.log(from_php_last_woo_payment_to_gks);

    
  }
  
  
  function mysubmit() {
    
    datasend='';
    datasend+='&eshop_name='  + encodeURIComponent($.base64.encode($("#mypostform #eshop_name").val().trim()));
    datasend+='&eshop_url='  + encodeURIComponent($.base64.encode($("#mypostform #eshop_url").val().trim()));
    datasend+='&company_id='  + encodeURIComponent(($("#mypostform #company_id").val().trim()));
    datasend+='&company_sub_id='  + encodeURIComponent(($("#mypostform #company_sub_id").val().trim()));
    datasend+='&eshop_key='  + encodeURIComponent($.base64.encode($("#mypostform #eshop_key").val().trim()));
    datasend+='&eshop_autosync='  + (($('#eshop_autosync').is(':checked')) ? '1':'0');
    datasend+='&eshop_sortorder='  + encodeURIComponent(($("#mypostform #eshop_sortorder").val().trim()));
    datasend+='&eshop_disable=' + (($('#eshop_disable').is(':checked')) ? '0':'1');
    datasend+='&tax_class_basikos='  + encodeURIComponent($.base64.encode($("#mypostform #tax_class_basikos").val().trim()));
    datasend+='&tax_class_meiomenos='  + encodeURIComponent($.base64.encode($("#mypostform #tax_class_meiomenos").val().trim()));
    datasend+='&tax_class_ypermeiomenos='  + encodeURIComponent($.base64.encode($("#mypostform #tax_class_ypermeiomenos").val().trim()));
    datasend+='&tax_class_yperypermeiomenos='  + encodeURIComponent($.base64.encode($("#mypostform #tax_class_yperypermeiomenos").val().trim()));
    datasend+='&tax_class_xorisfpa='  + encodeURIComponent($.base64.encode($("#mypostform #tax_class_xorisfpa").val().trim()));
    
    datasend+='&order_find_user_from='  + encodeURIComponent($.base64.encode($("#mypostform #order_find_user_from").val().trim()));
    datasend+='&order_meta_user_lang='  + encodeURIComponent($.base64.encode($("#mypostform #order_meta_user_lang").val().trim()));
    datasend+='&order_meta_parastatiko='  + encodeURIComponent($.base64.encode($("#mypostform #order_meta_parastatiko").val().trim()));
    datasend+='&order_meta_eponimia='  + encodeURIComponent($.base64.encode($("#mypostform #order_meta_eponimia").val().trim()));
    datasend+='&order_meta_title='  + encodeURIComponent($.base64.encode($("#mypostform #order_meta_title").val().trim()));
    datasend+='&order_meta_afm='  + encodeURIComponent($.base64.encode($("#mypostform #order_meta_afm").val().trim()));
    datasend+='&order_meta_doy='  + encodeURIComponent($.base64.encode($("#mypostform #order_meta_doy").val().trim()));
    datasend+='&order_meta_epaggelma='  + encodeURIComponent($.base64.encode($("#mypostform #order_meta_epaggelma").val().trim()));
    
    datasend+='&import_yes=' + (($('#mypostform #import_yes').is(':checked')) ? '1':'0');
    datasend+='&import_as=' + encodeURIComponent($.base64.encode($('#mypostform input[name=import_as]:checked').val()));
    datasend+='&acc_journal_id='  + encodeURIComponent(($("#mypostform #acc_journal_id").val().trim()));
    datasend+='&acc_journal_id_tim='  + encodeURIComponent(($("#mypostform #acc_journal_id_tim").val().trim()));
    datasend+='&acc_seira_id='  + encodeURIComponent(($("#mypostform #acc_seira_id").val().trim()));
    datasend+='&acc_seira_id_tim='  + encodeURIComponent(($("#mypostform #acc_seira_id_tim").val().trim()));
    datasend+='&warehouses_id_from='  + encodeURIComponent(($("#mypostform #warehouses_id_from").val().trim()));
    datasend+='&warehouses_id_from_tim='  + encodeURIComponent(($("#mypostform #warehouses_id_from_tim").val().trim()));
    datasend+='&will_update=' + $('#mypostform input[name=will_update]:checked').val();
    datasend+='&update_if_gks_change=' + $('#mypostform input[name=update_if_gks_change]:checked').val();
    datasend+='&update_state_gks_transfer='  + encodeURIComponent($.base64.encode($("#mypostform #update_state_gks_transfer").val().trim()));
    datasend+='&update_state_gks_reservation='  + encodeURIComponent($.base64.encode($("#mypostform #update_state_gks_reservation").val().trim()));
    datasend+='&update_state_gks_order='  + encodeURIComponent($.base64.encode($("#mypostform #update_state_gks_order").val().trim()));
    datasend+='&update_state_gks_acc_inv='  + encodeURIComponent($.base64.encode($("#mypostform #update_state_gks_acc_inv").val().trim()));
    datasend+='&update_state_woo='  + encodeURIComponent($.base64.encode($("#mypostform #update_state_woo").val().trim()));
    
    datasend+='&acc_inv_product_shipping='  + encodeURIComponent($("#mypostform #acc_inv_product_shipping").attr('data-id').trim());
    datasend+='&acc_inv_product_fees='  + encodeURIComponent($("#mypostform #acc_inv_product_fees").attr('data-id').trim());
    datasend+='&woo_start_booking_number='  + encodeURIComponent($.base64.encode($("#mypostform #woo_start_booking_number").val()));
    
    
    
    
    get_from_php_last_woo();
    
    woo_delivery_to_gks_str = encodeURIComponent($.base64.encode(JSON.stringify(from_php_last_woo_delivery_to_gks)));
    datasend+='&woo_delivery_to_gks_str=' + woo_delivery_to_gks_str;
    
    woo_payment_to_gks_str = encodeURIComponent($.base64.encode(JSON.stringify(from_php_last_woo_payment_to_gks)));
    datasend+='&woo_payment_to_gks_str=' + woo_payment_to_gks_str;
    
    //return;
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-eshop-item-exec.php?id=' + <?php echo $id;?>,
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


  $('#company').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-company.php',
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
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    select: function( event, ui ) {
      $('#company_id').val(ui.item.id);
      $('#company_sub_title').val(gks_lang('Κεντρικό'));
      $('#company_sub_id').val('0'); 
      filter_journal_seira_warehouses();
      
      //console.log(ui.item);     
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#company').val('');
        $('#company_id').val('');
        $('#company_sub_title').val('');
        $('#company_sub_id').val('');
      }
      filter_journal_seira_warehouses();
    }
  });  
  
  $('#company_sub_title').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        company_id: $('#company_id').val(),
        and_kentriko:1,
      };
      $.ajax({
        url: 'admin-autocomplete-company-sub.php',
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
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    select: function( event, ui ) {
      $('#company_sub_id').val(ui.item.id);
      filter_journal_seira_warehouses();      
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#company_sub_title').val('');
        $('#company_sub_id').val('');
      }
      filter_journal_seira_warehouses();
    }
  });      


  $('#eshop_get_settings').click(function() {
    //if (need_save) {
    //  myalert('error:' + gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
    //  return;  
    //}
    get_from_php_last_woo();
    
    $('#div_eshop_settings').html('<div style="text-align: center;"><img src="img/wait.gif"></div>');
    $('#delivery_one2one_div').html('<div style="text-align: center;"><img src="img/wait.gif"></div>');
    $('#payment_one2one_div').html('<div style="text-align: center;"><img src="img/wait.gif"></div>');
    
    //return;
    gks_myscroll();

    datasend='id=' + from_php_id;
    $.ajax({
			url: '/my/admin-eshop-item-get-settings.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#div_eshop_settings').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + jqXHR.responseText + '</div>');
			  gks_myscroll();
			},				
			success: function(data) {
			  need_save=true;
				if (!data) {
			    $('#div_eshop_settings').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
          $('#delivery_one2one_div').html($('#div_eshop_settings').html());
          $('#payment_one2one_div').html($('#div_eshop_settings').html());

				} else {
					if (data.success == true) {
					  $('#div_eshop_settings').html(data.data.html);
            //console.log(data.wootaxes);
            tax_class_basikos=$('#tax_class_basikos').val();
            tax_class_meiomenos=$('#tax_class_meiomenos').val();
            tax_class_ypermeiomenos=$('#tax_class_ypermeiomenos').val();
            tax_class_yperypermeiomenos=$('#tax_class_yperypermeiomenos').val();
            tax_class_xorisfpa=$('#tax_class_xorisfpa').val();
            
            
            $('#tax_class_basikos option').each(function() {if ($(this).attr('value') != '') $(this).remove();});
            $('#tax_class_meiomenos option').each(function() {if ($(this).attr('value') != '') $(this).remove();});
            $('#tax_class_ypermeiomenos option').each(function() {if ($(this).attr('value') != '') $(this).remove();});
            $('#tax_class_yperypermeiomenos option').each(function() {if ($(this).attr('value') != '') $(this).remove();});
            $('#tax_class_xorisfpa option').each(function() {if ($(this).attr('value') != '') $(this).remove();});
                
				    for (i = 0; i < data.wootaxes.length; i++) {
				      if (data.wootaxes[i]!='') {
				        $('#tax_class_basikos').append('<option value="'+ data.wootaxes[i] + '"' + (data.wootaxes[i]==tax_class_basikos ? ' selected' : '') + '>' + data.wootaxes[i] + '</option>');
				      }
				    }
				    for (i = 0; i < data.wootaxes.length; i++) {
				      if (data.wootaxes[i]!='') {
				        $('#tax_class_meiomenos').append('<option value="'+ data.wootaxes[i] + '"' + (data.wootaxes[i]==tax_class_meiomenos ? ' selected' : '') + '>' + data.wootaxes[i] + '</option>');
				      }
				    }
				    for (i = 0; i < data.wootaxes.length; i++) {
				      if (data.wootaxes[i]!='') {
				        $('#tax_class_ypermeiomenos').append('<option value="'+ data.wootaxes[i] + '"' + (data.wootaxes[i]==tax_class_ypermeiomenos ? ' selected' : '') + '>' + data.wootaxes[i] + '</option>');
				      }
				    }
				    for (i = 0; i < data.wootaxes.length; i++) {
				      if (data.wootaxes[i]!='') {
				        $('#tax_class_yperypermeiomenos').append('<option value="'+ data.wootaxes[i] + '"' + (data.wootaxes[i]==tax_class_yperypermeiomenos ? ' selected' : '') + '>' + data.wootaxes[i] + '</option>');
				      }
				    }
				    for (i = 0; i < data.wootaxes.length; i++) {
				      if (data.wootaxes[i]!='') {
				        $('#tax_class_xorisfpa').append('<option value="'+ data.wootaxes[i] + '"' + (data.wootaxes[i]==tax_class_xorisfpa ? ' selected' : '') + '>' + data.wootaxes[i] + '</option>');
				      }
				    }
            
            //console.log(data.delivery);
            html='';
            for (i = 0; i < data.delivery.length; i++) {
              curr_value=0;
              for (j=0; j < from_php_last_woo_delivery_to_gks.length; j++) {
                if (data.delivery[i].id==from_php_last_woo_delivery_to_gks[j].w) {
                  curr_value=from_php_last_woo_delivery_to_gks[j].g;
                  break;
                }
              }
              html+=
              '<div class="form-group row">' +
                '<div class="col-md-6">'+
                  '<select class="form-control form-control-sm myneedsave delivery_one2one_gks" data-aa="' + i + '">' +
                    '<option value="0"></option>';
                    for (j=0; j < from_php_delivery_gks.length; j++) {
                      html+='<option value="' + from_php_delivery_gks[j].id + '" ' + 
                      (curr_value==from_php_delivery_gks[j].id ? 'selected' : '') + 
                      '>' + from_php_delivery_gks[j].descr + '</option>';    
                    }
               html+=
                  '</select>' +
                '</div>' +
                '<div class="col-md-6">' +
                  '<span class="form-control form-control-sm myneedsave delivery_one2one_woo" data-aa="' + i + '" data-id="' + data.delivery[i].id + '">' +
                    data.delivery[i].title +
                  '</span>' +
                '</div>' +
              '</div>';
            }
            $('#delivery_one2one_div').html(html);
            $('#delivery_one2one_div').find('.myneedsave').on('input keyup paste', function() {
              need_save=true; 
            });

            //console.log(data.payments);
            html='';
            for (i = 0; i < data.payments.length; i++) {
              curr_value=0;
              for (j=0; j < from_php_last_woo_payment_to_gks.length; j++) {
                if (data.payments[i].id==from_php_last_woo_payment_to_gks[j].w) {
                  curr_value=from_php_last_woo_payment_to_gks[j].g;
                  break;
                }
              }
              html+=
              '<div class="form-group row">' +
                '<div class="col-md-6">'+
                  '<select class="form-control form-control-sm myneedsave payment_one2one_gks" data-aa="' + i + '">'+
                    '<option value="0"></option>';
                    for (j=0; j < from_php_payment_gks.length; j++) {
                      html+='<option value="' + from_php_payment_gks[j].id + '" ' + 
                      (curr_value==from_php_payment_gks[j].id ? 'selected' : '') + 
                      '>' + from_php_payment_gks[j].descr + '</option>';    
                    }
               html+=
                  '</select>' +
                '</div>' +
                '<div class="col-md-6">' +
                  '<span class="form-control form-control-sm myneedsave payment_one2one_woo" data-aa="' + i + '" data-id="' + data.payments[i].id + '">' +
                    data.payments[i].title +
                  '</span>' +
                '</div>' +
              '</div>';
            }
            $('#payment_one2one_div').html(html);
            
            $('#payment_one2one_div').find('.myneedsave').on('input keyup paste', function() {
              need_save=true; 
            });
            
            
            
					} else {
  			    $('#div_eshop_settings').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message) + '</div>');
            $('#delivery_one2one_div').html($('#div_eshop_settings').html());
            $('#payment_one2one_div').html($('#div_eshop_settings').html());
  			    
					}
				}
				gks_myscroll();
			}				  
		});
    
    
  });
  
  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
    


  $('#eshop_get_eidi').click(function() {
    if (need_save) {
      myalert('error:' + gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }

    $('body').addClass("myloading");
    datasend='id=' + from_php_id;
    $.ajax({
			url: '/my/admin-eshop-item-get-eidi.php',
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
					  //console.log(data);
					  if (data.count>0 && data.guid!='') {
					    $('#eshop_get_eidi').prop('disabled',true);
					    
					    from_php_run_guid_eidi=data.guid;

					    timer_run_guid_eidi_start();
					    
					  }
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}				  
				}
			}
		});

  });
   
  var timer_run_guid_eidi=null;
  function timer_run_guid_eidi_start() {
    $('#div_gks_run_guid_eidi').show();
    $('#gks_run_guid_eidi').css('width', '0%').attr('aria-valuenow','0').html('0%');
    $('#gks_run_guid_eidi_cc').html('');
    $('#gks_run_guid_eidi_errors').html('');    
    timer_run_guid_eidi = setInterval(timer_run_guid_eidi_fnc, 2000);    
  }
  function timer_run_guid_eidi_fnc () {
    //console.log('timer_run_guid_eidi_fnc');

    datasend='id=' + from_php_id + '&cmd=progress&runguid=' + from_php_run_guid_eidi;
    //console.log(datasend);
    
    $.ajax({
			url: '/my/admin-eshop-item-get-eidi.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  myalert('error:' + jqXHR.responseText); 
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
			    myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  //console.log(data);
					  $('#gks_run_guid_eidi').css('width', data.pososto + '%').attr('aria-valuenow',data.pososto).html(data.pososto_str + '%');
            $('#gks_run_guid_eidi_cc').html(data.done + '/' + data.count);
            $('#gks_run_guid_eidi_errors').html(data.errors);		
            if (data.count==data.done) {
              clearInterval(timer_run_guid_eidi);  
            }		  
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}				  
				}
			}
		});    
    
  }
  
  $('#eshop_get_coupon').click(function() {
    if (need_save) {
      myalert('error:' + gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }

    $('body').addClass("myloading");
    datasend='id=' + from_php_id;
    $.ajax({
			url: '/my/admin-eshop-item-get-coupon.php',
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
					  //console.log(data);
					  if (data.count>0 && data.guid!='') {
					    $('#eshop_get_coupon').prop('disabled',true);
					    
					    from_php_run_guid_coupon=data.guid;

					    timer_run_guid_coupon_start();
					    
					  }
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}				  
				}
			}
		});

  });
   
  var timer_run_guid_coupon=null;
  function timer_run_guid_coupon_start() {
    $('#div_gks_run_guid_coupon').show();
    $('#gks_run_guid_coupon').css('width', '0%').attr('aria-valuenow','0').html('0%');
    $('#gks_run_guid_coupon_cc').html('');
    $('#gks_run_guid_coupon_errors').html('');    
    timer_run_guid_coupon = setInterval(timer_run_guid_coupon_fnc, 2000);    
  }
  function timer_run_guid_coupon_fnc () {
    //console.log('timer_run_guid_coupon_fnc');

    datasend='id=' + from_php_id + '&cmd=progress&runguid=' + from_php_run_guid_coupon;
    //console.log(datasend);
    
    $.ajax({
			url: '/my/admin-eshop-item-get-coupon.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  myalert('error:' + jqXHR.responseText); 
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
			    myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  //console.log(data);
					  $('#gks_run_guid_coupon').css('width', data.pososto + '%').attr('aria-valuenow',data.pososto).html(data.pososto_str + '%');
            $('#gks_run_guid_coupon_cc').html(data.done + '/' + data.count);
            $('#gks_run_guid_coupon_errors').html(data.errors);		
            if (data.count==data.done) {
              clearInterval(timer_run_guid_coupon);  
            }		  
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}				  
				}
			}
		});    
    
  }  
  
  
  
  
  //afm,mobile,email,phone,user
  var order_find_user_from_tags = [gks_lang('ΑΦΜ'), gks_lang('Κινητό'), gks_lang('Σταθερό'), gks_lang('email'), gks_lang('Επαφή Παραγγελίας'),];
  $('#order_find_user_from').tagit({allowSpaces: true, availableTags: order_find_user_from_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  $('.btn_order_meta_user_lang').click(function() {
    $('#order_meta_user_lang').val($(this).html());  
  });

  $('.btn_order_meta_parastatiko').click(function() {
    $('#order_meta_parastatiko').val($(this).html());  
  });

  if (from_php_run_guid_eidi!='') timer_run_guid_eidi_start();
  if (from_php_run_guid_coupon!='') timer_run_guid_coupon_start();


  var update_state_gks_transfer_tags = [];
  <?php if (defined('GKS_TRANSFER') and GKS_TRANSFER) {?>
  update_state_gks_transfer_tags.push('<?php echo getTransferReservationStatusDescr('005prodraft');?>');
  update_state_gks_transfer_tags.push('<?php echo getTransferReservationStatusDescr('010draft');?>');
  update_state_gks_transfer_tags.push('<?php echo getTransferReservationStatusDescr('040cancelled');?>');
  update_state_gks_transfer_tags.push('<?php echo getTransferReservationStatusDescr('050rejected');?>');
  update_state_gks_transfer_tags.push('<?php echo getTransferReservationStatusDescr('070wait_payment');?>');
  update_state_gks_transfer_tags.push('<?php echo getTransferReservationStatusDescr('080confirm');?>');
  update_state_gks_transfer_tags.push('<?php echo getTransferReservationStatusDescr('100completed');?>');
  update_state_gks_transfer_tags.push('<?php echo getTransferReservationStatusDescr('110payment');?>');
  <?php } ?>
  $('#update_state_gks_transfer').tagit({allowSpaces: true, availableTags: update_state_gks_transfer_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var update_state_gks_reservation_tags = [];
  update_state_gks_reservation_tags.push('<?php echo getHotelReservationStatusDescr('005prodraft');?>');
  update_state_gks_reservation_tags.push('<?php echo getHotelReservationStatusDescr('010draft');?>');
  update_state_gks_reservation_tags.push('<?php echo getHotelReservationStatusDescr('040cancelled');?>');
  update_state_gks_reservation_tags.push('<?php echo getHotelReservationStatusDescr('050rejected');?>');
  update_state_gks_reservation_tags.push('<?php echo getHotelReservationStatusDescr('070wait_payment');?>');
  update_state_gks_reservation_tags.push('<?php echo getHotelReservationStatusDescr('080confirm');?>');
  update_state_gks_reservation_tags.push('<?php echo getHotelReservationStatusDescr('100completed');?>');
  update_state_gks_reservation_tags.push('<?php echo getHotelReservationStatusDescr('110payment');?>');
  $('#update_state_gks_reservation').tagit({allowSpaces: true, availableTags: update_state_gks_reservation_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});


  var update_state_gks_order_tags = [];
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('005prodraft');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('010draft');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('020pending');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('025offer');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('030forcancellation');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('040cancelled');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('050rejected');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('055wait_payment');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('060registered');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('070inproduction');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('080failed');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('090indelivery');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('095execute');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('100completed');?>');
  update_state_gks_order_tags.push('<?php echo getOrderStateDescr('110payment');?>');
  $('#update_state_gks_order').tagit({allowSpaces: true, availableTags: update_state_gks_order_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var update_state_gks_acc_inv_tags = [];
  update_state_gks_acc_inv_tags.push('<?php echo getAccInvStateDescr('010draft');?>');
  update_state_gks_acc_inv_tags.push('<?php echo getAccInvStateDescr('040cancelled');?>');
  update_state_gks_acc_inv_tags.push('<?php echo getAccInvStateDescr('050proinvoice');?>');
  update_state_gks_acc_inv_tags.push('<?php echo getAccInvStateDescr('080listing');?>');
  update_state_gks_acc_inv_tags.push('<?php echo getAccInvStateDescr('090ekdosi');?>');
  update_state_gks_acc_inv_tags.push('<?php echo getAccInvStateDescr('100payment');?>');
  $('#update_state_gks_acc_inv').tagit({allowSpaces: true, availableTags: update_state_gks_acc_inv_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});


								  
  var update_state_woo_tags = [];
  update_state_woo_tags.push('<?php echo gks_woo_order_state_descr('pending');?>');
  update_state_woo_tags.push('<?php echo gks_woo_order_state_descr('processing');?>');
  update_state_woo_tags.push('<?php echo gks_woo_order_state_descr('on-hold');?>');
  update_state_woo_tags.push('<?php echo gks_woo_order_state_descr('completed');?>');
  update_state_woo_tags.push('<?php echo gks_woo_order_state_descr('cancelled');?>');
  update_state_woo_tags.push('<?php echo gks_woo_order_state_descr('refunded');?>');
  update_state_woo_tags.push('<?php echo gks_woo_order_state_descr('failed');?>');
  $('#update_state_woo').tagit({allowSpaces: true, availableTags: update_state_woo_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  

  $('#import_yes').change(function() {

    if ($(this).is(':checked')) {
      $('.sh_import_yes').slideDown();
      val=$('#mypostform input[name=import_as]:checked').val();
      if (val=='transfer') {
        $('.sh_import_yes_import_as_transfer').slideDown();
        $('.sh_import_yes_import_as_reservation').slideUp();
        $('.sh_import_yes_import_as_order').slideUp();
        $('.sh_import_yes_import_as_acc_inv').slideUp();
        $('.sh_import_yes_import_as_acc_inv_ins').hide();
      } else if (val=='reservation') {
        $('.sh_import_yes_import_as_transfer').slideUp();
        $('.sh_import_yes_import_as_reservation').slideDown();
        $('.sh_import_yes_import_as_order').slideUp();
        $('.sh_import_yes_import_as_acc_inv').slideUp();
        $('.sh_import_yes_import_as_acc_inv_ins').hide();
      } else if (val=='order') {
        $('.sh_import_yes_import_as_transfer').slideUp();
        $('.sh_import_yes_import_as_reservation').slideUp();
        $('.sh_import_yes_import_as_order').slideDown();
        $('.sh_import_yes_import_as_acc_inv').slideUp();
        $('.sh_import_yes_import_as_acc_inv_ins').hide();
      } else if (val=='acc_inv') {
        $('.sh_import_yes_import_as_transfer').slideUp();
        $('.sh_import_yes_import_as_reservation').slideUp();
        $('.sh_import_yes_import_as_order').slideUp();
        $('.sh_import_yes_import_as_acc_inv').slideDown();
        $('.sh_import_yes_import_as_acc_inv_ins').show();
      }
    } else {
      $('.sh_import_yes').slideUp();
      $('.sh_import_yes_import_as_transfer').slideUp();
      $('.sh_import_yes_import_as_reservation').slideUp();
      $('.sh_import_yes_import_as_order').slideUp();
      $('.sh_import_yes_import_as_acc_inv').slideUp();
      $('.sh_import_yes_import_as_acc_inv_ins').hide();
      
    }
  });

  $('#mypostform input[name=import_as]').change(function() {
    val=$('#mypostform input[name=import_as]:checked').val();
    if (val=='transfer') {
      $('.sh_import_yes_import_as_transfer').slideDown();
      $('.sh_import_yes_import_as_reservation').slideUp();
      $('.sh_import_yes_import_as_order').slideUp();
      $('.sh_import_yes_import_as_acc_inv').slideUp();
      $('.sh_import_yes_import_as_acc_inv_ins').hide();
    } else if (val=='reservation') {
      $('.sh_import_yes_import_as_transfer').slideUp();
      $('.sh_import_yes_import_as_reservation').slideDown();
      $('.sh_import_yes_import_as_order').slideUp();
      $('.sh_import_yes_import_as_acc_inv').slideUp();
      $('.sh_import_yes_import_as_acc_inv_ins').hide();
    } else if (val=='order') {
      $('.sh_import_yes_import_as_transfer').slideUp();
      $('.sh_import_yes_import_as_reservation').slideUp();
      $('.sh_import_yes_import_as_order').slideDown();
      $('.sh_import_yes_import_as_acc_inv').slideUp();
      $('.sh_import_yes_import_as_acc_inv_ins').hide();
    } else if (val=='acc_inv') {
      $('.sh_import_yes_import_as_transfer').slideUp();
      $('.sh_import_yes_import_as_reservation').slideUp();
      $('.sh_import_yes_import_as_order').slideUp();
      $('.sh_import_yes_import_as_acc_inv').slideDown();
      $('.sh_import_yes_import_as_acc_inv_ins').show();
    } 
  });
  
  
  function filter_journal_seira_warehouses() {
    var dc =$('#company_id').val();
    var dcs=$('#company_sub_id').val();
    var import_as=$('#mypostform input[name=import_as]:checked').val();
    //console.log(dc,dcs,import_as);
    
    dj=$('#acc_journal_id').val();
    $('#acc_journal_id option').each(function() { 
      if ($(this).attr('value') > 0 ) {
        data_c=$(this).attr('data-c');
        data_cs=$(this).attr('data-cs');
        data_t=$(this).attr('data-t');
        data_w=$(this).attr('data-w');
        if (data_c!=dc || data_cs!=dcs || (data_t!=2100 && import_as=='transfer') || (data_t!=1100 && import_as=='reservation') || (data_t!=31 && import_as=='order') || (data_t!=1 && import_as=='acc_inv')) $(this).hide(); else $(this).show();
      }
    });
    if ($('#acc_journal_id option[value=' + dj + ']').css('display')=='none') $('#acc_journal_id').val('0');
    dj=$('#acc_journal_id').val();  
    
    dj_tim=$('#acc_journal_id_tim').val();
    $('#acc_journal_id_tim option').each(function() { 
      if ($(this).attr('value') > 0 ) {
        data_c=$(this).attr('data-c');
        data_cs=$(this).attr('data-cs');
        data_t=$(this).attr('data-t');
        data_w=$(this).attr('data-w');
        if (data_c!=dc || data_cs!=dcs || (data_t!=2100 && import_as=='transfer') || (data_t!=1100 && import_as=='reservation') || (data_t!=31 && import_as=='order') || (data_t!=1 && import_as=='acc_inv')) $(this).hide(); else $(this).show();
      }
    });
    if ($('#acc_journal_id_tim option[value=' + dj_tim + ']').css('display')=='none') $('#acc_journal_id_tim').val('0');
    dj_tim=$('#acc_journal_id_tim').val();  
    
    
    ds=$('#acc_seira_id').val();
    $('#acc_seira_id option').each(function() { 
      if ($(this).attr('value') > 0 ) {
        data_j=$(this).attr('data-j');
        if (data_j!=dj) $(this).hide(); else $(this).show();
      }
    });
    if ($('#acc_seira_id option[value=' + ds + ']').css('display')=='none') $('#acc_seira_id').val('0');
    ds=$('#acc_seira_id').val();  

    ds_tim=$('#acc_seira_id_tim').val();
    $('#acc_seira_id_tim option').each(function() { 
      if ($(this).attr('value') > 0 ) {
        data_j=$(this).attr('data-j');
        if (data_j!=dj_tim) $(this).hide(); else $(this).show();
      }
    });
    if ($('#acc_seira_id_tim option[value=' + ds_tim + ']').css('display')=='none') $('#acc_seira_id_tim').val('0');
    ds_tim=$('#acc_seira_id_tim').val();  


    if ($('#acc_journal_id').val()==0) {
      $('#div_warehouses_id_from').slideUp();
    } else {
      if ($('#acc_journal_id option:selected').attr('data-w')==0) {
        $('#div_warehouses_id_from').slideUp();
      } else {
        if ($('#import_yes').is(':checked')) {
          $('#div_warehouses_id_from').slideDown();
        }
      }
    }

    if ($('#acc_journal_id_tim').val()==0) {
      $('#div_warehouses_id_from_tim').slideUp();
    } else {
      if ($('#acc_journal_id_tim option:selected').attr('data-w')==0) {
        $('#div_warehouses_id_from_tim').slideUp();
      } else {
        if ($('#import_yes').is(':checked')) {
          $('#div_warehouses_id_from_tim').slideDown();
        }
      }
    }    

    dw=$('#warehouses_id_from').val();
    $('#warehouses_id_from option').each(function() { 
      if ($(this).attr('value') > 0 ) {
        data_c=$(this).attr('data-c');
        data_cs=$(this).attr('data-cs');
        if (data_c!=dc || data_cs!=dcs) $(this).hide(); else $(this).show();
      }
    });
    if ($('#warehouses_id_from option[value=' + dw + ']').css('display')=='none') $('#warehouses_id_from').val('0');
    dw=$('#warehouses_id_from').val();


    dw_tim=$('#warehouses_id_from_tim').val();
    $('#warehouses_id_from_tim option').each(function() { 
      if ($(this).attr('value') > 0 ) {
        data_c=$(this).attr('data-c');
        data_cs=$(this).attr('data-cs');
        if (data_c!=dc || data_cs!=dcs) $(this).hide(); else $(this).show();
      }
    });
    if ($('#warehouses_id_from_tim option[value=' + dw_tim + ']').css('display')=='none') $('#warehouses_id_from_tim').val('0');
    dw_tim=$('#warehouses_id_from_tim').val();

    
    
    
  }
  
  filter_journal_seira_warehouses();
  
  $('input[name=import_as]').change(filter_journal_seira_warehouses);
  $('#acc_journal_id').change(filter_journal_seira_warehouses);
  $('#acc_journal_id_tim').change(filter_journal_seira_warehouses);
  $('#acc_seira_id').change(filter_journal_seira_warehouses);
  $('#acc_seira_id_tim').change(filter_journal_seira_warehouses);
  
  $('#acc_inv_product_shipping').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode:'simple',
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
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    select: function( event, ui ) {
      $("#acc_inv_product_shipping").val(ui.item.descr).attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#acc_inv_product_shipping").val('').attr('data-id','0');
        }
    },
  });

  $('#acc_inv_product_fees').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode:'simple',
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
    create: function(event, ui){
      $(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    select: function( event, ui ) {
      $("#acc_inv_product_fees").val(ui.item.descr).attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#acc_inv_product_fees").val('').attr('data-id','0');
        }
    },
  });
  
  $('#eshop_key_new').click(function() {
    
    myk='';
    for (i=1;i<=100;i++) {
      myk+= (Math.floor(Math.random()*1000)) + '';
      if (myk.length>32) break;
    }
    $('#eshop_key').val(myk);
    return false;
  });
    
  
  //generic
  gks_page_loading=false;
  



  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang(gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;'));
  };



  need_save=false;
        
  
});



 
</script>




<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


