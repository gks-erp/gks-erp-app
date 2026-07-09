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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_item',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');




if ($id<=0) {
  debug_mail(false,'record not found sql',$id); 
  die('no record found');
}

$nav_active_array=array('production','production_orders');


//gks_production_sintagi_after_balance_for_order($id);

gks_production_order_sintagi($id);
//gks_production_order_ergasies($id);
//echo time(); die();


gks_production_order_calc_ergasies_setready($id);
gks_production_order_calc_ergasies_tree($id);
gks_production_order_calc_ergasies_time($id);


//die();

$sql=select_gks_orders($id)." where gks_orders.id_order = ".$id;
if (count($perm_id_company_ids)>0) $sql.=" and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
}
if ($result->num_rows!=1) {
  debug_mail(false,'record not found sql',$sql); 
  die('no record found');
}
$row = $result->fetch_assoc();
$my_page_title=gks_lang('Παραγωγή Παραγγελίας').': #'.$id;

  
$row['country_name']=gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');
$row['nomos_descr']=gks_lang_data_trans($row['nomos_descr'],$row['ma_nomos_id'],'gks_nomoi','nomos_descr');

$address='';
$pelati_sxolio=nl2br_gks($row['pelati_sxolio']);
$order_sxolio=nl2br_gks($row['order_sxolio']);

$addressL1=trim_gks((empty($row['ma_odos']) ? '' : $row['ma_odos'].', ').' '.$row['ma_orofos'].' '.$row['ma_perioxi']);
if (endwith($addressL1,',')) $addressL1=substr($addressL1, 0, strlen($addressL1)-1);
$addressL2=trim_gks((empty($row['ma_poli']) ? '' : $row['ma_poli'].', ').
                (empty($row['nomos_descr']) ? '' : $row['nomos_descr'].', ').
                $row['ma_tk']);
if (endwith($addressL2,',')) $addressL1=substr($addressL2, 0, strlen($addressL2)-1);
$addressL3=trim_gks($row['country_name']);

$address='';
if ($addressL1!='') $address.=$addressL1.'<br>';
if ($addressL2!='') $address.=$addressL2.'<br>';
$address.=$addressL3;
if (endwith($address,'<br>')) $address=substr($address, 0, strlen($address)-4);  



$row['ma_country_id']=intval($row['ma_country_id']);
$row['ma_poli']=trim_gks($row['ma_poli']);



$order_state=$row['order_state'];


$sql_eidi="SELECT gks_orders_products.*, 
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
gks_eshop_pricelist.pricelist_descr,
gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
gks_monades_metrisis.monada_symbol,gks_monades_metrisis.monada_descr

FROM ((((gks_orders_products
LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN gks_eshop_fpa ON gks_orders_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
LEFT JOIN gks_eshop_pricelist ON gks_orders_products.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_monades_metrisis ON gks_orders_products.product_monada_id=gks_monades_metrisis.id_monada
WHERE gks_orders_products.order_id=".$id."
AND gks_orders_products.product_is_optional in (0,2)
ORDER BY gks_orders_products.id_order_product;";

$result_eidi = $db_link->query($sql_eidi);        
if (!$result_eidi) {debug_mail(false,'error sql',$sql_eidi); die('sql error');}

$eidos_array = array();



$_gks_session['gks']['basket']['products_need_apostoli']=false;

while ($eidos = $result_eidi->fetch_assoc()) {
  $eidos_array[]=$eidos;
}


$sql_er = "SELECT gks_orders_products_sets.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
FROM gks_orders_products_sets LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders_products_sets.last_user_id_set = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE gks_orders_products_sets.order_id=".$id."
ORDER BY gks_orders_products_sets.set_id";
$result_er = $db_link->query($sql_er);        
if (!$result_er) debug_mail(false,'error sql',$sql_er);
if (!$result_er) die('sql error');

$sets_array=array();
while ($row_er = $result_er->fetch_assoc()) {
  $sets_array[]=$row_er;
}

$has_ergasies=true;
if (count($sets_array)==0) { //den exei sets-ergasies, diladi ergasies
  $has_ergasies=false;
  $sets_array[]=array(
    'set_id'=>'',
    'gks_nickname'=>'',
    'last_user_id_set'=>0,
    'production_set_pososto'=>0,
    'set_sum_time'=>0,
    'ergasies_tree'=>'',
  );
}

$gks_lock=false;


stat_record();

include_once('_my_header_admin.php');
?>


<link href="css/admin-production-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-6" style="text-align:center">
      <h3>
        <?php echo gks_lang('Παραγωγή Παραγγελίας');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span>
        <?php echo gks_lang('Ref');?>: <span class="order_ref_number_head"><?php echo $row['order_ref_number'];?></span>
      </h3>
    </div>
    <div class="col-md-6" style="text-align:center">
      <a href="admin-orders-item.php?id=<?php echo $id;?>">
        <button type="button" class="btn btn-primary" data-id="9"><?php echo gks_lang('Παραγγελία');?></button>
      </a>
    </div>
  </div>
</div>



<div class="container-fluid">
  <div class="row">
    <div class="col-md-4">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποθήκη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('warehouse');?> id="mypostform">
          <div class="form-group row"> 

            <label for="prod_warehouses_id_from" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Υλικά Από');?>:</label>
            <div class="col-sm-8">
            <?php if ($gks_lock) {
              
                echo '<div class="gks_flock form-control-sm">';
                  echo '<a href="admin-warehouses-item.php?id='.$row['prod_warehouses_id_from'].'">'.$row['prod_warehouse_name_from'].'</a>';
                echo '</div>'; 
                          
            } else {?>              
              <input id="prod_warehouses_id_from" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['prod_warehouse_name_from']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['prod_warehouses_id_from'];?>"
              >
            <?php } ?>
            </div>
          </div>
          <div class="form-group row"> 
          
            <label for="prod_warehouses_id_to" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Παραγόμενα Σε');?>:</label>
            <div class="col-sm-8">
            <?php if ($gks_lock) {
              
                echo '<div class="gks_flock form-control-sm">';
                  echo '<a href="admin-warehouses-item.php?id='.$row['prod_warehouses_id_to'].'">'.$row['prod_warehouse_name_to'].'</a>';
                echo '</div>';  
                           
            } else {?> 
              <input id="prod_warehouses_id_to" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['prod_warehouse_name_to']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['prod_warehouses_id_to'];?>"
              >
            <?php } ?>
            </div>          

          </div>
          <div class="form-group row">
            <div class="offset-sm-4 col-sm-8 mb-2">
              <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
            </div>
          </div>
                    
          
        </div>
      </div>
    </div>
    <div class="col-md-8">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>
          <div class="row">  
            <div class="col-md-6">
              <div class="form-group row">
                <label for="user" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πελάτης');?>:</label>
                <div class="col-sm-8">
                  <div class="form-control-sm gks_flock">
                    <?php echo htmlspecialchars_gks($row['gks_nickname']);?>
                  
                    <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['user_id'];?>" style="<?php if ($row['user_id']==0) echo 'display:none';?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
                  </div>
                </div>
              </div>  
              
             
      
              <div class="form-group1 row" id="div_order_sxolio"style="<?php echo (trim_gks($row['order_sxolio'])=='' ? 'display:none;' : '');?>">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 6px;"><?php echo nl2br_gks($row['order_sxolio']);?></div>
              </div>       
              
              <div class="form-group row">
                <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Διεύθυνση');?>:</label>
                <div class="col-sm-8">
                  <div class="form-control-sm gks_flock" id="div_pelati_address"><?php echo $address;?></div>
                </div>
              </div>                
<?php if ($has_ergasies) { ?>
              <div class="form-group row">
                <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ποσοστό');?>:</label>
                <div class="col-sm-8">
                  <div class="progress" style="background-color: darkgray;margin-top: 4px;height: 24px;">
                    <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: <?php echo number_format($row['production_pososto'],2,'.','');?>%" aria-valuenow="<?php echo number_format($row['production_pososto'],2,'.','');?>" aria-valuemin="0" aria-valuemax="100">
                      <?php echo number_format($row['production_pososto'],2,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND);?>%
                    </div>
                  </div>                            
                </div>
              </div>
              <div class="form-group row">
                <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χρόνος');?>:</label>
                <div class="col-sm-8">
                  <div class="form-control-sm gks_flock"><?php echo time_duration_format($row['production_sum_time']); ?></div>
                </div>
              </div>
<?php } ?>
              <div class="form-group row">
                <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κόστος Παραγγελίας');?>:</label>
                <div class="col-sm-8">
                  <div class="form-control-sm gks_flock">
                    <span id="production_kostos"><?php if (isset($row['production_kostos'])) echo myCurrencyFormat($row['production_kostos']);?></span>
                  </div>
                </div>
              </div>


            </div>
            
            <div class="col-md-6">
              <div class="form-group row">
                <label for="ddate" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παράδοση');?>:</label>
                <div class="col-md-8">
                  <div class="form-control-sm gks_flock">
                    <?php if (isset($row['ddate'])) echo  showDate(strtotime($row['ddate']), 'd/m/Y', 1);?>
                  </div>
                </div>
              </div> 
                                
              <div class="form-group row">
                <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση');?>:</label>
                <div class="col-sm-8">
                  <div class="form-control-sm gks_flock">
                    <span class="order_state_<?php echo $row['order_state'];?>"><?php echo getOrderStateDescr($row['order_state']);?></span>
                  </div>
                </div>
              </div> 
                              
              <?php 
              
              gks_plugins_functions_run('admin_production_item_after_state',array(
                'row' => &$row,
              ));
              
                          
              if ($GKS_ORDERS_OCCASION) { ?>
              <div class="form-group row">
                <label for="order_occasion" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περίσταση');?>:</label>
                <div class="col-sm-8">
                  <div class="form-control-sm gks_flock">
                  <?php
                  $occasion_title = '';
                  $temp = trim_gks($row['occasion_type_descr']);         if ($temp!='') $occasion_title.=$temp.' / ';
                  $temp = trim_gks($row['occasion_title']);      if ($temp!='') $occasion_title.=$temp.' / ';
                  //$temp =  trim_gks($row['payment_acquirer_name']); if ($temp!='') $occasion_title.=$temp.' / ';
                  $temp =  trim_gks($row['occasion_mydate_add']);   if ($temp!='') $occasion_title.=showDate(strtotime($row['occasion_mydate_add']), 'd/m/Y H:i', 1) .' / ';
                  if ($occasion_title!='') $occasion_title=substr($occasion_title, 0, strlen($occasion_title) - 3);
                  
                  ?>
                  <?php echo htmlspecialchars_gks($occasion_title);?>
                  </div>
                </div>
              </div> 
              <?php } ?>
              
              <div class="form-group row">
                <label for="note_production" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερική σημείωση για παραγωγή');?>:</label>
                <div class="col-md-8">
                  <div class="form-control-sm gks_flock">
                  <?php echo nl2br_gks(htmlspecialchars_gks($row['note_production']));?>
                  </div>
                </div>
              </div>                             
                            
            </div>
          </div>
        </div>
      </div>  
      

    </div>
  </div>
</div>

<?php


if ($has_ergasies) { ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          Sets
        </div>
        <div class="card-body" <?php echo gks_card_body('sets');?>>



            
<table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" id="eidi_table" style="width:100%;">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap>#</th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo gks_lang('Σετ');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="80%" nowrap><?php echo gks_lang('Είδη');?></th>        
      
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo gks_lang('Υπάλληλος');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="20%" nowrap><?php echo gks_lang('Ποσοστό<br>Χρόνος');?></th>        
           
    </tr>
</thead>
<tbody>  

<?php

  
  $i=0;
  foreach ($sets_array as $row) {

    $i++;  
    $set_id=trim_gks($row['set_id']);
    if ($set_id=='') $set_id=gks_lang('κενό');
    ?>
  <tr class="treropid_main" data-setid="<?php echo $row['set_id'];?>">
    <th scope="row" nowrap class="mytdcm" rowspan="2"><?php echo ($i);?></th>


    <td nowrap class="mytdcm" rowspan="2"><?php echo $set_id;?></td>  
    <td class="mytdcm">
      <table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">
        <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="">#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap=""><?php echo gks_lang('Κωδικός');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap=""><?php echo gks_lang('Φωτό');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="30%" nowrap=""><?php echo gks_lang('Περιγραφή');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="50%" nowrap=""><?php echo gks_lang('Παρατηρήσεις');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap=""><?php echo gks_lang('Σελίδες');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap=""><?php echo gks_lang('Ποσότητα');?></th>        
            </tr>
        </thead>
        <tbody>      
        <?php
        $aa = 0;
        foreach ($eidos_array as $eidos) {
          $pro_set_id=trim_gks($eidos['product_set']);
          if ($pro_set_id=='') $pro_set_id=gks_lang('κενό');          
          if ($pro_set_id == $set_id) {
          $aa++;
        ?> 
          <tr class="treidos" data-id="<?php echo $eidos['product_id'];?>" data-setid="<?php echo $set_id;?>">
          
            <th scope="row" nowrap class="mytdcm"><?php echo ($aa);?></th>
            <td nowrap class="mytdcm"><?php echo $eidos['product_code']?></td>   
            <td nowrap class="mytdcm"><?php 
              $myimgurl=trim_gks($eidos['product_photo_p'].'');
              if ($myimgurl == '') {
                $myimgurl="/my/img/product.png";
                echo '<a class="gks_photo_link" data-aa="'.$aa.'" tabIndex="-1" href="/my/img/product.png" style="display:none;"><img class="gks_img" data-aa="'.$aa.'" src="/my/img/product.png"></a>';
              } else {
                $mydir = dirname($myimgurl);
                if (endwith($mydir,'/thumbnail')) {
                  $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
                } else {
                  $photo_url=$myimgurl;
                }
                echo '<a class="lightgalleryitem_user gks_photo_link" data-aa="'.$aa.'"  tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$eidos['product_code'].'"><img class="gks_img" data-aa="'.$aa.'" src="'.$myimgurl.'"></a>';
              }
              
            ?></td>   
            <td class="mytdcml">
              <a href="admin-products-item.php?id=<?php echo $eidos['product_id'];?>"><?php echo nl2br_gks(htmlspecialchars_gks($eidos['product_descr']));?></a>
            </td>   
            <td class="mytdcml">
              <?php echo nl2br_gks(htmlspecialchars_gks($eidos['product_comments']));?>
            </td>   
            <td class="mytdcm">
              <?php if ($eidos['product_sheets']!=0) echo $eidos['product_sheets'];?>
            </td>   
            <td class="mytdcm" nowrap><?php if ($eidos['product_quantity']!=0) echo $eidos['product_quantity'].' '.$eidos['monada_symbol'];?></td>   
      

          </tr>
      <?php } } ?>
        </tbody>
      </table>        
           
    </td>
    
     
   
    <td nowrap class="mytdcm"><a href="admin-users-item.php?id=<?php echo $row['last_user_id_set'];?>"><?php echo $row['gks_nickname'];?></a></td>
    <td class="mytdcm"> 
      <div class="progress" style="background-color: darkgray;">
        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: <?php echo number_format($row['production_set_pososto'],2,'.','');?>%" aria-valuenow="<?php echo number_format($row['production_set_pososto'],2,'.','');?>" aria-valuemin="0" aria-valuemax="100">
          <?php echo number_format($row['production_set_pososto'],2,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND);?>%
        </div>
      </div>      
      <div><?php echo time_duration_format($row['set_sum_time']); ?></div>
    </td>
  </tr>
  <tr class="treropid_main" data-setid="<?php echo $row['set_id'];?>">
    <td nowrap class="mytdcm" colspan="3"><div class="mermaid" style="text-align: center;"><?php 
      echo $row['ergasies_tree'];
    ?></div></td> 
  </tr>
<?php              
  }
  
?>            
  </tbody>
</table>  
<small class="form-text text-muted" style="text-align:center"><?php echo gks_lang('Για να αλλάξετε την κατάσταση μιας εργασίας στον παραπάνω πίνακα, κάτε δεξί κλικ επάνω της.');?></small>

        </div>
      </div>
    </div>
  </div>
</div>



<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εργασίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('ergasies');?>>


        
<table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" style="width:100%;">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap>#</th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('ID');?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="12%" nowrap><?php echo gks_lang('Προσθήκη');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('Χρόνος');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('Σετ');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="30%" nowrap><?php echo gks_lang('Είδος');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="16%" nowrap><?php echo gks_lang('Εργασία');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('Κατάσταση');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="14%" nowrap><?php echo gks_lang('Σχόλιο Παραγωγής');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo gks_lang('Υπάλληλος');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo gks_lang('Πόστο');?></th>        
           
    </tr>
</thead>
<tbody>  

<?php
  $sql_er = "SELECT gks_production_line.*, 
  gks_orders.order_state, gks_orders.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  gks_production_ergasies.production_ergasia_descr, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
  gks_orders.note_production,
  ".GKS_WP_TABLE_PREFIX."users_lastuser.gks_nickname AS gks_nickname_lastuser,
  gks_production_posta.production_posto_descr
  FROM ((((((gks_production_line 
  LEFT JOIN gks_production_ergasies ON gks_production_line.ergasia_id = gks_production_ergasies.id_production_ergasia) 
  LEFT JOIN gks_orders ON gks_production_line.order_id = gks_orders.id_order) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_production_line.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_production_line.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_lastuser ON gks_production_line.last_user_id_production = ".GKS_WP_TABLE_PREFIX."users_lastuser.ID)
  LEFT JOIN gks_production_posta ON gks_production_line.last_posto_id = gks_production_posta.id_production_posto
  where gks_production_line.order_id=".$id."
  ORDER BY gks_production_line.id_production_line";

  $result_er = $db_link->query($sql_er);        
  if (!$result_er) debug_mail(false,'error sql',$sql_er);
  if (!$result_er) die('sql error');
  
  $i=0;
  while ($row = $result_er->fetch_assoc()) {
    $set_id=trim_gks($row['set_id']);
    if ($set_id=='') $set_id=gks_lang('κενό');

    $i++;  
    
    $ids_eidos='';
    $html_eidos='';
    $sql_eidi="SELECT gks_eshop_products.id_product, 
    gks_eshop_products.product_code, 
    gks_orders_products.product_descr, 
    gks_orders_products.product_sheets, 
    gks_orders_products.product_quantity,
    gks_orders_products.product_comments
    FROM ((gks_production_line_pid 
    LEFT JOIN gks_orders_products ON gks_production_line_pid.order_product_id = gks_orders_products.id_order_product) 
    LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product)
    LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
    WHERE gks_production_line_pid.production_line_id=".$row['id_production_line']."
    AND gks_orders_products.product_is_optional in (0,2)
    ORDER BY gks_eshop_products.product_code";
    $result_eidi = $db_link->query($sql_eidi);
    if (!$result_eidi) debug_mail(false,'error sql',$sql_eidi);
    if (!$result_eidi) die('sql error');
    while ($row_eidi = $result_eidi->fetch_assoc()) {
      $html_eidos.='<a href="admin-products-item.php?id='.$row_eidi['id_product'].'" '.
      'class="tooltipster" '.
      'title="'.gks_lang('Σελίδες').': '.$row_eidi['product_sheets'].
      '<br>'.gks_lang('Ποσότητα').': '.$row_eidi['product_quantity'].
      '<br>'.gks_lang('Παρατηρήσεις').': '.$row_eidi['product_comments'].
      //'">'.(trim_gks($row_eidi['product_code'])!='' ? trim_gks($row_eidi['product_code']) : trim_gks($row_eidi['product_descr_p'])).'</a>, ';
      '">'.trim_gks($row_eidi['product_descr']).'</a>, ';
      $ids_eidos.=$row_eidi['id_product'].',';
    }
    if ($html_eidos!='') $html_eidos=substr($html_eidos, 0, strlen($html_eidos)-2);
    if ($ids_eidos!='') $ids_eidos=substr($ids_eidos, 0, strlen($ids_eidos)-1);
    
    
    ?>
  <tr class="treropid" data-id="<?php echo $row['ergasia_id'];?>" data-setid="<?php echo $set_id;?>" data-ids_eidos="<?php echo $ids_eidos;?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></th>

    <td nowrap class="mytdcm">
      <table cellpadding=0 cellspacing=0 style="width:100%;padding: 0px;margin: 0px;border-width: 0px;">
        <tr style="background-color: transparent;">
          <td style="width:33%;text-align:left;   border-width: 0px;padding: 0px 0px 0px 0px;margin: 0px;"  ><a href="admin-production-line-item.php?id=<?php echo $row['id_production_line'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td style="width:34%;text-align:center; border-width: 0px;padding: 0px 4px 0px 4px;margin: 0px;"><?php echo $row['id_production_line'];?></td>
          <td style="width:33%;text-align:right;  border-width: 0px;padding: 0px 0px 0px 0px;margin: 0px;" ><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row['id_production_line'];?>" data-model="gks_production_line"></i></td>
        </tr>  
      </table>
    </td>
    <td        class="mytdcm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><?php echo time_duration_format($row['prod_sum_time']);?></td>
    <td nowrap class="mytdcm"><?php echo $row['set_id'];?></td>   
    <td        class="mytdcml"><?php 
      echo $html_eidos;
      
    ?></td>    
    
    <td        class="mytdcml"><a href="admin-production-ergasies-item.php?id=<?php echo $row['ergasia_id'];?>"><?php echo $row['production_ergasia_descr'];?></a></td>
    <td nowrap class="mytdcm"><span class="production_line_state_<?php echo $row['pl_state'];?>"><?php echo getProductionLineStateDescr($row['pl_state']);?></span></td>
    <td        class="mytdcml" style="font-size:0.8rem"><?php echo nl2br_gks(htmlentities($row['prod_comments']));?></td>
    <td nowrap class="mytdcm"><a href="admin-users-item.php?id=<?php echo $row['last_user_id_production'];?>"><?php echo $row['gks_nickname_lastuser'];?></a></td>
    <td        class="mytdcm"><a href="admin-production-posta-item.php?id=<?php echo $row['last_posto_id'];?>"><?php echo $row['production_posto_descr'];?></a></td>
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

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κατρέλες χρόνου');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('times');?>>

<table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center"  style="width:100%;">
  <thead>
      <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="">#</th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="12%" nowrap=""><?php echo gks_lang('Έναρξη');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="12%" nowrap=""><?php echo gks_lang('Λήξη');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap=""><?php echo gks_lang('Χρόνος');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap=""><?php echo gks_lang('Σετ');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="30%" nowrap=""><?php echo gks_lang('Είδος');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="12%" nowrap=""><?php echo gks_lang('Εργασία');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap=""><?php echo gks_lang('Από');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap=""><?php echo gks_lang('Σε');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="14%" nowrap=""><?php echo gks_lang('Σχόλιο Παραγωγής');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap=""><?php echo gks_lang('Υπάλληλος');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap=""><?php echo gks_lang('Πόστο');?></th>        
      </tr>
  </thead>
  <tbody>

<?php              

  $sql_ts="SELECT gks_production_line_time.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  gks_production_line.id_production_line, 
  gks_production_line.ergasia_id, gks_production_ergasies.production_ergasia_descr, 
  gks_production_line.prod_comments, gks_production_line.set_id, 
  gks_production_posta.production_posto_descr
  FROM (((gks_production_line 
  INNER JOIN gks_production_line_time ON gks_production_line.id_production_line = gks_production_line_time.production_line_id) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_production_line_time.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_production_ergasies ON gks_production_line.ergasia_id = gks_production_ergasies.id_production_ergasia) 
  LEFT JOIN gks_production_posta ON gks_production_line_time.posto_id = gks_production_posta.id_production_posto
  
  WHERE (((gks_production_line.order_id)=".$id."))
  ORDER BY gks_production_line_time.id_production_line_time;";

  $result_ts = $db_link->query($sql_ts);        
  if (!$result_ts) debug_mail(false,'error sql',$sql_ts);
  if (!$result_ts) die('sql error');
  
  $aa=0;
  while ($row_ts = $result_ts->fetch_assoc()) {
    $aa++;  
    $set_id=trim_gks($row_ts['set_id']);
    if ($set_id=='') $set_id=gks_lang('κενό');    
    ?>
    <tr class="tropid" data-id="<?php echo $row_ts['ergasia_id'];?>" data-setid="<?php echo $set_id;?>">    
      <th scope="row" nowrap class="mytdcm"><?php echo ($aa);?></th>
      <td        class="mytdcm"><?php if (isset($row_ts['time_start'])) echo showDate(strtotime($row_ts['time_start']), 'd/m/Y H:i:s', 1);?></td>
      <td        class="mytdcm"><?php if (isset($row_ts['time_end'])) echo showDate(strtotime($row_ts['time_end']), 'd/m/Y H:i:s', 1);?></td>
      <td nowrap class="mytdcm"><?php
      if (isset($row_ts['duration_secs']) and $row_ts['duration_secs']>0) {
        echo time_duration_format($row_ts['duration_secs']);
      }?></td>

      <td nowrap class="mytdcm"><?php echo $row_ts['set_id'];?></td>
      <td        class="mytdcml"><?php 
        $html_eidos='';
        $sql_eidi="SELECT gks_eshop_products.id_product, 
        gks_eshop_products.product_code, 
        gks_orders_products.product_descr, 
        gks_orders_products.product_sheets, 
        gks_orders_products.product_quantity,
        gks_orders_products.product_comments
        FROM (gks_production_line_pid 
        LEFT JOIN gks_orders_products ON gks_production_line_pid.order_product_id = gks_orders_products.id_order_product) 
        LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
        WHERE gks_production_line_pid.production_line_id=".$row_ts['id_production_line']."
        AND gks_orders_products.product_is_optional in (0,2)
        ORDER BY gks_eshop_products.product_code";
        $result_eidi = $db_link->query($sql_eidi);        
        if (!$result_eidi) debug_mail(false,'error sql',$sql_eidi);
        if (!$result_eidi) die('sql error');
        while ($row_eidi = $result_eidi->fetch_assoc()) {
          $html_eidos.='<a href="admin-products-item.php?id='.$row_eidi['id_product'].'" class="tooltipster" title="'.gks_lang('Σελίδες').': '.$row_eidi['product_sheets'].'<br>'.gks_lang('Ποσότητα').': '.$row_eidi['product_quantity'].'<br>'.gks_lang('Παρατηρήσεις').': '.$row_eidi['product_comments'].'">'.$row_eidi['product_descr'].'</a>, ';
          
        }
        if ($html_eidos!='') $html_eidos=substr($html_eidos, 0, strlen($html_eidos)-2);
        echo $html_eidos;
        
      ?></td> 
    
      <td        class="mytdcml"><a href="admin-production-ergasies-item.php?id=<?php echo $row_ts['ergasia_id'];?>"><?php echo $row_ts['production_ergasia_descr'];?></a></td>
      <td nowrap class="mytdcm"><span class="production_line_state_<?php echo $row_ts['prev_state'];?>"><?php echo getProductionLineStateDescr($row_ts['prev_state']);?></span></td>            
      <td nowrap class="mytdcm"><span class="production_line_state_<?php echo $row_ts['curr_state'];?>"><?php echo getProductionLineStateDescr($row_ts['curr_state']);?></span></td>
      <td class="mytdcml" style="font-size:0.8rem"><?php echo nl2br_gks(htmlentities($row_ts['prod_comments']));?></td>
      <td        class="mytdcm"><a href="admin-users-item.php?id=<?php echo $row_ts['user_id'];?>"><?php echo $row_ts['gks_nickname'];?></a></td>
      <td        class="mytdcm"><a href="admin-production-posta-item.php?id=<?php echo $row_ts['posto_id'];?>"><?php echo $row_ts['production_posto_descr'];?></a></td>
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

<?php
}

$sql_sintagi="SELECT gks_production_sintagi.*, gks_production_bom.bom_descr
FROM gks_production_sintagi 
LEFT JOIN gks_production_bom ON gks_production_sintagi.production_bom_id = gks_production_bom.id_production_bom
WHERE gks_production_sintagi.order_id=".$id."
order by gks_production_bom.bom_descr";
$result_sintagi = $db_link->query($sql_sintagi);
if (!$result_sintagi) {debug_mail(false,'error sql',$sql_sintagi);die('sql error');}
$sintagi_array=array();
$id_production_sintagi_ids=array();
while ($row_sintagi = $result_sintagi->fetch_assoc()) {
  $row_sintagi['products']=array();
  $row_sintagi['costs']=array();
  
  $sintagi_array[$row_sintagi['order_product_id']][$row_sintagi['id_production_sintagi']]=$row_sintagi;
  $id_production_sintagi_ids[]=$row_sintagi['id_production_sintagi'];
}

//print '<pre>';print_r($sintagi_array);die();

if (count($id_production_sintagi_ids)>0) {
  $sql_sintagi="SELECT gks_production_sintagi.order_product_id, gks_production_sintagi_product.*,
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
  gks_eshop_products.product_code,
  gks_monades_metrisis.monada_symbol,gks_monades_metrisis.monada_descr
  FROM (((gks_production_sintagi_product 
  LEFT JOIN gks_production_sintagi ON gks_production_sintagi_product.production_sintagi_id = gks_production_sintagi.id_production_sintagi)
  LEFT JOIN gks_eshop_products ON gks_production_sintagi_product.spbom_product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
  LEFT JOIN gks_monades_metrisis ON gks_production_sintagi_product.spbom_monada_id=gks_monades_metrisis.id_monada
  where production_sintagi_id in (".implode(',',$id_production_sintagi_ids).")
  ORDER BY gks_production_sintagi_product.spbom_aa,gks_production_sintagi.order_product_id";
  $result_sintagi = $db_link->query($sql_sintagi);
  if (!$result_sintagi) {debug_mail(false,'error sql',$sql_sintagi);die('sql error');}
  while ($row_sintagi = $result_sintagi->fetch_assoc()) {
    $sintagi_array[$row_sintagi['order_product_id']][$row_sintagi['production_sintagi_id']]['products'][]=$row_sintagi;
  }
  
  $sql_sintagi="SELECT gks_production_sintagi.order_product_id, gks_production_sintagi_cost.*
  FROM gks_production_sintagi_cost 
  LEFT JOIN gks_production_sintagi ON gks_production_sintagi_cost.production_sintagi_id = gks_production_sintagi.id_production_sintagi
  where production_sintagi_id in (".implode(',',$id_production_sintagi_ids).")
  ORDER BY gks_production_sintagi_cost.scbom_aa,gks_production_sintagi.order_product_id";
  $result_sintagi = $db_link->query($sql_sintagi);
  if (!$result_sintagi) {debug_mail(false,'error sql',$sql_sintagi);die('sql error');}
  while ($row_sintagi = $result_sintagi->fetch_assoc()) {
    $sintagi_array[$row_sintagi['order_product_id']][$row_sintagi['production_sintagi_id']]['costs'][]=$row_sintagi;
  }
}

//print '<pre>';print_r($sintagi_array);die();

  
  

  
$sintages_sets_html='';
$i=0;
$kostos_all=0;
foreach ($sets_array as $row) {

  $i++;  
  $set_id=trim_gks($row['set_id']);
  if ($set_id=='') $set_id=gks_lang('κενό');
  
  $aa = 0;
  $kostos_set=0;
  $sintages_set_item_html='';
  foreach ($eidos_array as $eidos) {
    $pro_set_id=trim_gks($eidos['product_set']);
    if ($pro_set_id=='') $pro_set_id=gks_lang('κενό');          
    if ($pro_set_id == $set_id) {
      $aa++;
      
      $sintages_eidos_html='';
      $kostos_eidous=0;
        
      if (isset($sintagi_array[$eidos['id_order_product']])) {
  
        $tmp_out='';
        $aa_s=0;
        foreach ($sintagi_array[$eidos['id_order_product']] as $sintagi) {
          $aa_s++;
          $tmp_out.=
          '<tr>'.
            '<th scope="row" nowrap class="mytdcm">'.$aa_s.'</th>'.
            '<td class="mytdcml"><a href="admin-production-bom-item.php?id='.$sintagi['production_bom_id'].'">'.$sintagi['bom_descr'].'</a></td>'.
            '<td class="mytdcm" nowrap>[[kostos_sintagi]]</td>'.
            '<td valign="top">';
          
          $tmp_p_out='';
          $aa_p=0;
          
          $kostos_products=0;
          
          foreach ($sintagi['products'] as $pitem) {
            $aa_p++;
            $kostos_products+=$pitem['spbom_kostos_value'];
            $tmp_p_out.=
            '<tr>'.
              '<th scope="row" nowrap class="mytdcm">'.$aa_s.'</th>'.
              '<td nowrap class="mytdcm">';
                $myimgurl=trim_gks($pitem['product_photo_p'].'');
                if ($myimgurl == '') {
                  $myimgurl="/my/img/product.png";
                  $tmp_p_out.= '<a class="gks_photo_link" data-aa="'.$aa.'" tabIndex="-1" href="/my/img/product.png" style="display:none;"><img class="gks_img" data-aa="'.$aa.'" src="/my/img/product.png"></a>';
                } else {
                  $mydir = dirname($myimgurl);
                  if (endwith($mydir,'/thumbnail')) {
                    $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
                  } else {
                    $photo_url=$myimgurl;
                  }
                  $tmp_p_out.= '<a class="lightgalleryitem_user1 gks_photo_link" data-aa="'.$aa.'"  tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$pitem['product_code'].'"><img class="gks_img" data-aa="'.$aa.'" src="'.$myimgurl.'"></a>';
                }
                
            $tmp_p_out.=
              '</td>'.             
              '<td class="mytdcml"><a href="admin-products-item.php?id='.$pitem['spbom_product_id'].'">'.$pitem['product_descr_p'].'</a></td>'.
              '<td class="mytdcm" nowrap>'.myNumberFormatNo0Local($pitem['spbom_quantity']).' '.$pitem['monada_symbol'].'</td>'.
              '<td class="mytdcm">'.myCurrencyFormat($pitem['spbom_kostos_value']).'</td>'.
            '</tr>';                  
          }
          if ($tmp_p_out!='') {
            $tmp_out.=
            '<table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" style="width:100%;">'.
              '<thead>'.
                '<tr>'.
                  '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="">#</th>'.
                  '<th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="">'.gks_lang('Φωτό').'</th>'.        
                  '<th class="table-dark" scope="col" style="text-align: left   !important;" width="60%" nowrap="">'.gks_lang('Περιγραφή').'</th>'.        
                  '<th class="table-dark" scope="col" style="text-align: center !important;" width="20%" nowrap="">'.gks_lang('Ποσότητα').'</th>'.        
                  '<th class="table-dark" scope="col" style="text-align: center !important;" width="20%" nowrap="">'.gks_lang('Κόστος').'</th>'.        
                '</tr>'.
              '</thead>'.
              '<tbody>'.
            $tmp_p_out.
                '<tr>'.
                  '<td class="mytdcml" colspan="4" style="font-weight: bold;">'.gks_lang('Σύνολο').'</td>'.
                  '<td class="mytdcm" style="font-weight: bold;">'.myCurrencyFormat($kostos_products).'</td>'.
                '</tr>'.  
              '</tbody>'.
            '</table>';
          }
          
          
          $tmp_out.=  
            '</td>'.
            '<td valign="top">';

          $tmp_c_out='';
          $aa_c=0;
          $kostos_costs=0;
          foreach ($sintagi['costs'] as $citem) {
            $aa_c++;
            $kostos_costs+=$citem['scbom_kostos_value'];
            $tmp_c_out.=
            '<tr>'.
              '<th scope="row" nowrap class="mytdcm">'.$aa_c.'</th>'.
              '<td class="mytdcml">'.$citem['scbom_cost'].'</td>'.
              '<td class="mytdcm">'.myCurrencyFormat($citem['scbom_kostos_value']).'</td>'.
            '</tr>';                  
          }
          if ($tmp_c_out!='') {
            $tmp_out.=
            '<table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" style="width:100%;">'.
              '<thead>'.
                '<tr>'.
                  '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="">#</th>'.
                  '<th class="table-dark" scope="col" style="text-align: left   !important;" width="60%" nowrap="">'.gks_lang('Περιγραφή').'</th>'.        
                  '<th class="table-dark" scope="col" style="text-align: center !important;" width="40%" nowrap="">'.gks_lang('Κόστος').'</th>'.        
                '</tr>'.
              '</thead>'.
              '<tbody>'.
             $tmp_c_out.
                '<tr>'.
                  '<td class="mytdcml" colspan="2" style="font-weight: bold;">'.gks_lang('Σύνολο').'</td>'.
                  '<td class="mytdcm" style="font-weight: bold;">'.myCurrencyFormat($kostos_costs).'</td>'.
                '</tr>'.              
              '</tbody>'.
            '</table>';
          }
            
          $tmp_out.=  
            '</td>'.
          '</tr>';
          
          $kostos_sintagi=$kostos_products+$kostos_costs;
          $tmp_out=str_replace('[[kostos_sintagi]]', myCurrencyFormat($kostos_sintagi), $tmp_out);
           
          
          $kostos_eidous+=$kostos_sintagi;
        }
        
        if ($tmp_out!='') {
          $sintages_eidos_html.= 
          '<table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" style="width:100%;">'.
            '<thead>'.
              '<tr>'.
                '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="">#</th>'.
                '<th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="">'.gks_lang('Συνταγή').'</th>'.        
                '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="">'.gks_lang('Κόστος').'</th>'.        
                '<th class="table-dark" scope="col" style="text-align: center !important;" width="40%" nowrap="">'.gks_lang('Υλικά').'</th>'.        
                '<th class="table-dark" scope="col" style="text-align: center !important;" width="40%" nowrap="">'.gks_lang('Άλλα Κόστη').'</th>'.        
              '</tr>'.
            '</thead>'.
            '<tbody>'.
           $tmp_out.
            '</tbody>'.
          '</table>';
        
        } 
          
         //print '<pre>';print_r($eidos); print_r($sintagi_array[$eidos['id_order_product']]);print '</pe>';die();
      }
        
   
          


      if ($sintages_eidos_html!='') {

        $sintagi_eidos_tr_1=
        '<tr class="" data-id="'.$eidos['product_id'].'" data-setid="'.$set_id.'">'.
          '<th scope="row" nowrap class="mytdcm">'.$aa.'</th>'.
          '<td nowrap class="mytdcm">';
          
            $myimgurl=trim_gks($eidos['product_photo_p'].'');
            if ($myimgurl == '') {
              $myimgurl="/my/img/product.png";
              $sintagi_eidos_tr_1.= '<a class="gks_photo_link" data-aa="'.$aa.'" tabIndex="-1" href="/my/img/product.png" style="display:none;"><img class="gks_img" data-aa="'.$aa.'" src="/my/img/product.png"></a>';
            } else {
              $mydir = dirname($myimgurl);
              if (endwith($mydir,'/thumbnail')) {
                $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
              } else {
                $photo_url=$myimgurl;
              }
              $sintagi_eidos_tr_1.= '<a class="lightgalleryitem_user1 gks_photo_link" data-aa="'.$aa.'"  tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$eidos['product_code'].'"><img class="gks_img" data-aa="'.$aa.'" src="'.$myimgurl.'"></a>';
            }
            
        $sintagi_eidos_tr_1.=
          '</td>'.
          '<td class="mytdcml">'.
            '<a href="admin-products-item.php?id='.$eidos['product_id'].'">'.nl2br_gks(htmlspecialchars_gks($eidos['product_descr'])).'</a>'.
          '</td>'.   
          '<td class="mytdcm">'.
            ($eidos['product_quantity']!=0 ? $eidos['product_quantity'].' '.$eidos['monada_symbol'] : '').
          '</td>'. 
          '<td class="mytdcm">'.
            myCurrencyFormat($kostos_eidous).
          '</td>'. 
            
          '<td class="mytdcml">';
      
      
        $sintagi_eidos_tr_2=
        '</td>'.
        '</tr>';
        $kostos_set+=$kostos_eidous;
        $sintages_set_item_html.=$sintagi_eidos_tr_1.$sintages_eidos_html.$sintagi_eidos_tr_2;
      }

    } 
  } 
      
        
  if ($sintages_set_item_html!='') {
    $kostos_all+=$kostos_set;
    $sintages_set_item_html=
      
    '<tr class="" data-setid="<'.$row['set_id'].'">'.
      '<th scope="row" nowrap class="mytdcm">'.$i.'</th>'.
      '<td nowrap class="mytdcm">'.$set_id.'</td>'.
      '<td nowrap class="mytdcm">'.myCurrencyFormat($kostos_set).'</td>'.
      '<td class="mytdcm">'.
        '<table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" style="width:100%;">'.
          '<thead>'.
            '<tr>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="">#</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="">'.gks_lang('Φωτό').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="">'.gks_lang('Περιγραφή').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="">'.gks_lang('Ποσότητα').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="">'.gks_lang('Κόστος').'</th>'.
              '<th class="table-dark" scope="col" style="text-align: center !important;" width="90%" nowrap="">'.gks_lang('Συνταγές').'</th>'.
            '</tr>'.
          '</thead>'.
          '<tbody>'.    
      
          $sintages_set_item_html.
          '</tbody>'.
        '</table>'.
      '</td>'.
    '</tr>';
    
    $sintages_sets_html.=$sintages_set_item_html;
  }
            
}

if ($sintages_sets_html!='') {
  
  $sintages_sets_html=
  '<table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" id="sintages_table" style="width:100%;">'.
  '<thead>'.
    '<tr >'.
      '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap>#</th>'.
      '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap>'.gks_lang('Σετ').'</th>'.
      '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap>'.gks_lang('Κόστος').'</th>'.
      '<th class="table-dark" scope="col" style="text-align: center !important;" width="100%" nowrap>'.gks_lang('Είδη').'</th>'.
    '</tr>'.
  '</thead>'.
  '<tbody>'.    
  
  $sintages_sets_html.
  '</tbody>'.
  '</table>';
  
  if ($kostos_all!=0) {
    $sintages_sets_html.='<div style="text-align: center;">'.gks_lang('Συνολικό κόστος παραγγελίας').': <b>'.myCurrencyFormat($kostos_all).'</b></div>';
  } 
}

?>            
 
<?php if ($sintages_sets_html!='') { ?>
<div class="container-fluid" id="sintagi">
  <div class="row">
    <div class="col-md-12">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συνταγές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('sintagi');?>>

          <?php echo $sintages_sets_html;?>

        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>


<script  src="js/mermaid-8.3.1/mermaid.min.js"></script>
<script>
  
  var callback = function(){
      alert('A callback was triggered');
  }
  var config = {
    startOnLoad:true,
    theme: 'default', //default forest dark neutral
    flowchart:{
      useMaxWidth:true,
      htmlLabels:true,
      curve:'basis', //basis linear cardinal
      //width: '100px',
      height:230,
      boxMargin:100,
    },
    securityLevel:'loose',
  };
  
  mermaid.initialize(config);  
  
  
</script>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  //$('.tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});

  function line_state_mouseenter() {
    data_id=$(this).attr('data-id');
    data_setid=$(this).attr('data-setid');
    $('.line_state[data-id=' + data_id + '][data-setid=' + data_setid + ']').each(function() {
      $(this).addClass('line_state_hover');  
    });    
  }  
  
  function line_state_mouseleave() {
    data_id=$(this).attr('data-id');
    data_setid=$(this).attr('data-setid');
    $('.line_state[data-id=' + data_id + '][data-setid=' + data_setid + ']').each(function() {
      $(this).removeClass('line_state_hover');  
    });      
  }

  var id_production_line=0;
  var oldstate='';

  function line_state_right_click() {
    id_production_line = parseInt($(this).attr('data-recid'));
    if (isNaN(id_production_line)) id_production_line=0;
    oldstate = $(this).attr('data-oldstate');    
  }
  function line_state_click() {
    line_state_right_click();
        
    if ($(this).hasClass('line_state_click')) {
      $(this).removeClass('line_state_click');
      data_id=$(this).attr('data-id');
      data_setid=$(this).attr('data-setid');
      $('.line_state[data-id=' + data_id + '][data-setid=' + data_setid + ']').each(function() {
        $(this).removeClass('line_state_click');  
      });            
    } else {
      $(this).addClass('line_state_click');
      data_id=$(this).attr('data-id');
      data_setid=$(this).attr('data-setid');
      $('.line_state[data-id=' + data_id + '][data-setid=' + data_setid + ']').each(function() {
        $(this).addClass('line_state_click');  
      });
    }
    
    var mysels=[];
    $('.line_state_click').each(function() {
      data_id=$(this).attr('data-id');
      data_setid=$(this).attr('data-setid');
      mysels.push({'id': data_id, 'opid': data_setid});
    });
    //console.log(mysels);
    
    if (mysels.length==0) {
      $('.tropid').each(function() {
        $(this).show();
      });
    } else {
      $('.tropid').each(function() {
        $(this).hide();
      });
      mysels.forEach(function(item) {
        $('.tropid[data-id=' + item.id + '][data-setid=' + item.opid + ']').each(function() {
          $(this).show();
        });
      });
      
    }
    if (mysels.length==0) {
      $('.treropid').each(function() {
        $(this).show();
      });
      $('.treidos').each(function() {
        $(this).show();    
      });      
    } else {
      $('.treropid').each(function() {
        $(this).hide();
      });
      var ids_eidos=[];
      mysels.forEach(function(item) {
        $('.treropid[data-id=' + item.id + '][data-setid=' + item.opid + ']').each(function() {
          $(this).show();
          temp=$(this).attr('data-ids_eidos');
          temp=temp.split(',');
          for (i=0; i<temp.length; i++) {
            if (ids_eidos.includes(temp[i]) == false) {
              ids_eidos.push(temp[i]);
            }
          }
          //console.log(ids_eidos);
        });
      });
      $('.treidos').each(function() {
        $(this).hide();    
      });
      ids_eidos.forEach(function(item) {
        $('.treidos[data-id=' + item + ']').show();
      });
    }
    
    if (mysels.length==0) {
      $('.treropid_main').each(function() {
        $(this).show();
      });
    } else {
      $('.treropid_main').each(function() {
        $(this).hide();
      });
      mysels.forEach(function(item) {
        $('.treropid_main[data-setid=' + item.opid + ']').each(function() {
          $(this).show();
        });
      });
    }
  }

	line_state_contextMenu_config={
		event: 'contextmenu',
    items: function(e) {
  		var arr = [];
  		arr.push({type: 'item', text: '<span class="production_line_state_010draft"><?php echo getProductionLineStateDescr('010draft');?></span>', disabled: oldstate=='010draft' || (oldstate=='050processing'), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('010draft');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_020cancelled"><?php echo getProductionLineStateDescr('020cancelled');?></span>', disabled: oldstate=='020cancelled' || (oldstate=='050processing'), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('020cancelled');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_030pending"><?php echo getProductionLineStateDescr('030pending');?></span>', disabled: oldstate=='030pending' || (oldstate=='050processing'), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('030pending');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_040ready"><?php echo getProductionLineStateDescr('040ready');?></span>', disabled: oldstate=='040ready' || (oldstate=='050processing'), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('040ready');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_050processing"><?php echo getProductionLineStateDescr('050processing');?></span>', disabled: oldstate=='050processing' || (!(oldstate=='040ready' || oldstate=='060pause')), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('050processing');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_060pause"><?php echo getProductionLineStateDescr('060pause');?></span>', disabled: oldstate=='060pause' || (!(oldstate=='050processing')), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('060pause');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_070failed"><?php echo getProductionLineStateDescr('070failed');?></span>', disabled: oldstate=='070failed' || (!(oldstate=='050processing')), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('070failed');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_100completed"><?php echo getProductionLineStateDescr('100completed');?></span>', disabled: 1==2 & (oldstate=='100completed' || (!(oldstate=='040ready' || oldstate=='060pause' || oldstate=='050processing'))), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('100completed');
  		}});

      return arr;
    }
	};  
    
  function myTimer_g_node() {
    var g_node_can_run=true;
    $('.mermaid').each(function() {
      gggg = $(this).find('g .node').length;
      if (gggg<=0) {
        g_node_can_run=false;
        return;
      }
    });
    if (g_node_can_run==false) return;
    window.clearInterval(var_myTimer_g_node);
    $('g .node').each(function() {
      myclass=$(this).attr('class');
      if (myclass.length>5 && myclass.substring(0, 5) == 'node ') {
        myclass=myclass.substring(5);
        if (myclass.substring(0,3) == 'svg') {
          parts=myclass.split('_');
          if (parts.length==5) {
            //console.log(myclass);  
            $(this).attr('class', 'node line_state svg' + parts[4]).
                    attr('data-id',parts[1]).
                    attr('data-setid',parts[2]).
                    attr('data-recid',parts[3]).
                    attr('data-oldstate',parts[4]);
          }
        }
      }
    });
    
    $('.line_state').mouseenter(line_state_mouseenter);
    $('.line_state').mouseleave(line_state_mouseleave);
    $('.line_state').contextMenu(line_state_contextMenu_config);
    $('.line_state').contextmenu(line_state_right_click);
    $('.line_state').click(line_state_click);	
    
    
  }
  var var_myTimer_g_node = setInterval(myTimer_g_node, 100);  
  

  
  
  
  

	

  function line_state_cmd(newstate) {
    
    datasend='';
    datasend+='id=' + id_production_line;    
    datasend+='&newstate='  + encodeURI(newstate.trim());    
    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: 'admin-production-posto-run-exec.php',
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
  }
  
  

  
  $("#eidi_table").lightGallery({selector: '.lightgalleryitem_user',thumbnail:true,galleryId:1,hideBarsDelay:1000,}); 
  $("#sintages_table").lightGallery({selector: '.lightgalleryitem_user1',thumbnail:true,galleryId:2,hideBarsDelay:1000,}); 
  


  $('#prod_warehouses_id_from').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-warehouse.php',
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
      $('#prod_warehouses_id_from').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#prod_warehouses_id_from').val('').attr('data-id','0');
      }
    }
  });
  $('#prod_warehouses_id_to').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-warehouse.php',
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
      $('#prod_warehouses_id_to').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#prod_warehouses_id_to').val('').attr('data-id','0');
      }
    }
  });
  

  function mysubmit() {
    //console.log('mysubmit');  
    datasend='';
    datasend+='&prod_warehouses_id_from='  + encodeURIComponent(($("#mypostform #prod_warehouses_id_from").attr('data-id').trim()));
    datasend+='&prod_warehouses_id_to='  + encodeURIComponent(($("#mypostform #prod_warehouses_id_to").attr('data-id').trim()));
    //console.log(datasend);  

    $('body').addClass("myloading");
    
    $.ajax({
			url: 'admin-production-item-exec.php?id=' + <?php echo $id;?>,
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

  }

  
  $('.submit_button_back').click(function() {
  <?php if ((isset($_gks_session['gks']['recordback']) and $_gks_session['gks']['recordback']!='') and
            (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']!='' and endwith($_SERVER['HTTP_REFERER'], '?id=-1')) ) { ?>
    window.location.href='<?php echo $_gks_session['gks']['recordback'];?>';
  <?php } else if (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']!='' and endwith($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI']) == false ) { ?>
    window.location.href='<?php echo $_SERVER['HTTP_REFERER'];?>';
  <?php } else { ?>
    window.location.href='<?php
    if (endwith($_SERVER['SCRIPT_NAME'],'-item.php')) {
      echo substr($_SERVER['SCRIPT_NAME'], 0,strlen($_SERVER['SCRIPT_NAME'])-9) . '.php';
    } else {
      echo '/';  
    }
    ?>';
  <?php } ?>
  });  



  
});



</script>  

<style>
  
 
.line_state_hover > rect {
  fill:#000000 !important;
  stroke:#000000 !important;
  stroke-width:2px !important;   
} 
.line_state_hover > .label {
  color:white !important;
} 

.line_state_click > rect {
  fill:#000000 !important;
  stroke:#000000 !important;
  stroke-width:2px !important;   
} 
.line_state_click > .label {
  font-weight: bold !important; 
  color:white !important;
}



</style>


<?php
//db_close();
include_once('_my_footer_admin.php');


