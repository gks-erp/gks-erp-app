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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_machine',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_crm_tasks_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_tasks','view',0);
$perm_crm_tasks_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_tasks','edit',0);
$perm_crm_tasks_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_tasks','add',0);
$perm_crm_tasks_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_tasks','delete',0);

$gks_voip_params=gks_voip_user_params();

$gks_custom_prepare = gks_custom_table_item_prepare('gks_crm_machine',['from'=>'item']);
//print '<pre>';print_r($gks_custom_prepare);die();
//echo '<pre>';echo gks_notification_userperm_internal_users();die();

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
  $row['id_crm_machine']=-1;
  $row['crm_machine_name'] ='';
  $row['crm_machine_descr'] ='';
  $row['crm_machine_serial_number'] ='';
  $row['crm_machine_product_id']=0;
  $row['product_descr_p']='';
  $row['crm_machine_brand_id']=0;

  $row['crm_machine_user_id']=0;
  $row['users_extra_address_id']=-1;
  $row['gks_nickname']='';
  $row['user_email']='';
  $row['user_mobile']='';
  
  $row['user_last_name']='';
  $row['user_first_name']='';
  $row['eponimia']='';
  $row['title']='';
  $row['afm']='';
  $row['doy']='';
  $row['epaggelma']='';
  $row['order_sxolio']='';
  $row['pelati_sxolio']='';
  $row['lang_name']='';
  $row['user_lang']='';
  $row['ma_odos']='';
  $row['ma_arithmos']='';
  $row['ma_orofos']='';
  $row['ma_perioxi']='';
  $row['ma_poli']='';
  $row['ma_tk']='';
  $row['ma_country_id']=0;
  $row['country_name']='';
  $row['ma_nomos_id']=0;
  $row['nomos_descr']='';


  $my_page_title=gks_lang('Νέα Συσκευή');


} else {
 $sql ="SELECT gks_crm_machine.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  
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
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname,".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile as user_mobile,
  table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name,
  gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
  gks_users.order_sxolio,gks_users.pelati_sxolio,
  gks_lang.lang_name, ".GKS_WP_TABLE_PREFIX."users.gks_lang as user_lang,
  gks_users.ma_odos,gks_users.ma_arithmos,gks_users.ma_orofos,gks_users.ma_perioxi,gks_users.ma_poli,gks_users.ma_tk,
  gks_users.ma_country_id,gks_country.country_name,
  gks_users.ma_nomos_id,gks_nomoi.nomos_descr,
  
  gks_users_extra_address.ea_name
  FROM (((((((((((gks_crm_machine
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_crm_machine.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_crm_machine.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_eshop_products ON gks_crm_machine.crm_machine_product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_machine.crm_machine_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
  )  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
  )  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
  LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang)
  LEFT JOIN gks_users_extra_address ON gks_crm_machine.users_extra_address_id = gks_users_extra_address.id_users_extra_address
  
  where gks_crm_machine.id_crm_machine = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Συσκευή').': '.$row['crm_machine_name'];
  $object_title=$row['crm_machine_name'];
}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);
//print '<pre>';print_r($gks_custom_row);die();


stat_record();
$nav_active_array=array('crm','crm_machine');

$crm_machine_brand_path='';
$crm_machine_brand_id=intval($row['crm_machine_brand_id']);
if ($crm_machine_brand_id>0) {
$sql_brand="SELECT gks_eshop_products_brands.*, ccproducts.ccc,
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
FROM (((((((((gks_eshop_products_brands
LEFT JOIN (
  SELECT product_brand_id, Count(product_id) AS ccc
  FROM gks_eshop_products_brands_products
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
WHERE gks_eshop_products_brands.id_product_brand=".$crm_machine_brand_id."
ORDER BY fullpath";  
  
  $result_brand = $db_link->query($sql_brand);        
  if (!$result_brand) debug_mail(false,'error sql',$sql_brand);
  if (!$result_brand) die('sql error');
  if ($result_brand->num_rows>=1) {
    $row_brand = $result_brand->fetch_assoc();
    $crm_machine_brand_path=$row_brand['fullpath'];
  }
  
}


unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='crm_machine';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']= 0; //$row['company_id'];
$mybasketarray['company_sub_id']= 0; //$row['company_sub_id'];
$mybasketarray['user']['user_id']=$row['crm_machine_user_id'];
$mybasketarray['user']['afm']=$row['afm'];
$mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
$mybasketarray['parastatiko']=1;
gks_CheckAFM_Live($mybasketarray);
$check_vies=$mybasketarray['check_vies'];


$user_comms=gks_get_user_communications($row['crm_machine_user_id']);
//print '<pre>'; print_r($user_comms);die();


include_once('_my_header_admin.php');
?>
<link rel="stylesheet" href="css/admin-crm-machine-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Συσκευή');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Συσκευή');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="crm_machine_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-8">
              <input id="crm_machine_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_machine_name']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="crm_machine_descr" class="col-sm-12 col-form-label form-control-sm text-sm-right1" ><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-12">
              <textarea id="crm_machine_descr" type="text" class="gks_tinymce form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['crm_machine_descr']);?></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="crm_machine_serial_number" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Serial Number');?>:</label>
            <div class="col-sm-8">
              <input id="crm_machine_serial_number" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_machine_serial_number']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="crm_machine_product" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Είδος');?>:</label>
            <div class="col-sm-8">
              <input type="text"   name="crm_machine_product"    id="crm_machine_product"    value="<?php echo htmlspecialchars_gks($row['product_descr_p']);?>" class="form-control form-control-sm" style="width:100%;display: inline-block;margin-right: 10px;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input type="hidden" name="crm_machine_product_id" id="crm_machine_product_id" value="<?php echo $row['crm_machine_product_id'];?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="crm_machine_brand" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Μάρκα');?>:</label>
            <div class="col-sm-8">
              <input type="text"   name="crm_machine_brand"    id="crm_machine_brand"    value="<?php echo htmlspecialchars_gks($crm_machine_brand_path);?>" class="form-control form-control-sm" style="width:100%;display: inline-block;margin-right: 10px;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input type="hidden" name="crm_machine_brand_id" id="crm_machine_brand_id" value="<?php echo $row['crm_machine_brand_id'];?>">
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
          <?php echo gks_lang('Τοποθεσία Συσκευής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('customer');?>>  
          <div class="form-group row">
            <label for="crm_machine_user" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
            <div class="col-sm-10">

              <input id="crm_machine_user" type="text" class="form-control form-control-sm myneedsave email_contact_name"  
              value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="crm_machine_user_id" type="hidden" value="<?php echo $row['crm_machine_user_id'];?>" class="myneedsave">
              <a id="autocomplete_crm_machine_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['crm_machine_user_id'];?>" style="<?php if ($row['crm_machine_user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
            </div>
          </div>


          <div class="form-group row" style="margin-bottom: 0px;">
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_pelati_sxolio" style="<?php echo (trim_gks($row['pelati_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_pelati_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['pelati_sxolio']);?></div>
              </div>
                            
            </div>
            
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_order_sxolio" style="<?php echo (trim_gks($row['order_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['order_sxolio']);?></div>
              </div>               
            </div>
          </div>


          <div class="form-group row">
            <label for="dr_user_first_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_first_name">
                <?php echo $row['user_first_name'];?>
              </div>
            </div>
            <label for="dr_user_last_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επώνυμο');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_last_name">
                <?php echo $row['user_last_name'];?>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="dr_user_email" class="col-sm-2 col-form-label form-control-sm text-sm-right">email:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_email">
                <?php 
                if (isset($user_comms['email'])) {
                  $temp=array();
                  foreach ($user_comms['email'] as $value) $temp[]=$value['html'];
                  echo implode('<br>', $temp);
                }?>
              </div>
            </div>
            <label for="dr_user_mobile" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_mobile">
                <?php 
                if (isset($user_comms['phone'])) {
                  $temp=array();
                  foreach ($user_comms['phone'] as $value) $temp[]=$value['html'];
                  echo implode('<br>', $temp);
                }?>
              </div>                
            </div>
          </div>
              
          <div class="form-group row">
            <label for="dr_user_lang" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_lang" data-val="<?php echo $row['user_lang'];?>">
                <?php echo $row['lang_name'];?>
              </div>
              
            </div>
          </div>
          
          <div class="form-group row">
            <label for="dr_user_eponimia" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επωνυμία');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_eponimia">
                <?php echo $row['eponimia'];?>
              </div>
            </div>
            <label for="dr_user_title" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τίτλος');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_title">
                <?php echo $row['title'];?>
              </div>
            </div>
          </div>
          <?php
          $ee_initials='';
          $sql="select id_country,country_ee,country_name,country_initials 
          FROM gks_country where id_country=".intval($row['ma_country_id'])." ORDER BY country_name";
          $result_select = $db_link->query($sql);        
          if (!$result_select) {
            debug_mail(false,'error sql',$sql);
            die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          }
          if ($result_select->num_rows==1) {
            $row_select = $result_select->fetch_assoc();
            $ee_initials=trim_gks($row_select['country_ee']);
          }
          $this_select='';
          ?>
          
          
          <div class="form-group row">
            <label for="dr_user_afm" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height">
                <span id="dr_user_afm_ee_initial_static" style="<?php echo ($ee_initials!='' ? '' : 'display:none;');?>"><?php echo $ee_initials;?></span><span 
                  style="display: inline-block;text-align:left;vertical-align: middle;"
                  id="dr_user_afm" class=" <?php echo ($ee_initials=='' ? '':'dr_user_afm_views');?>"><?php echo htmlspecialchars_gks($row['afm']);?></span><span 
                  id="dr_user_afm_views_run_static" style="height:25px;<?php echo ($check_vies['run'] ? '' : 'display:none;');?>"><?php echo $check_vies['views_run_img'];?></span>
            
              </div>
            </div>
            <label for="dr_user_doy" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_doy">
                <?php echo $row['doy'];?>
              </div>
            </div>
          </div>


          <div class="form-group row">
            <label for="dr_user_epaggelma" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
            <div class="col-sm-10">
              <div class="form-control-sm gks_unset_height" id="dr_user_epaggelma">
                <?php echo $row['epaggelma'];?>
              </div>
            </div>
          </div>  

          <div class="form-group row">
            <label for="users_extra_address_id" class="col-sm-2 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τόπος');?>:</label>
            <div class="col-sm-10">
              
                <select id="users_extra_address_id" class="form-control form-control-sm myneedsave">
                  <option value="-1" <?php echo ($row['users_extra_address_id']==-1 ? ' selected ' : '');?>><?php echo gks_lang('Βασική διεύθυνση');?></option>

                  <?php
                  $row['ea_name']='';
                  $row['ea_phone']='';
                  
                  $sql="SELECT gks_users_extra_address.*, country_name,nomos_descr
                  FROM (gks_users_extra_address 
                  LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
                  LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
                  WHERE (gks_users_extra_address.user_id=".$row['crm_machine_user_id']." and gks_users_extra_address.user_id>0)
                  
                  ORDER BY gks_users_extra_address.id_users_extra_address";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  }
                  $selected_ea=array();
                  $selected_ea['ea_name']='';
                  $selected_ea['ea_phone']='';
                  $selected_ea['ea_odos']='';
                  $selected_ea['ea_arithmos']='';
                  $selected_ea['ea_orofos']='';
                  $selected_ea['ea_perioxi']='';
                  $selected_ea['ea_poli']='';
                  $selected_ea['ea_tk']='';
                  $selected_ea['ea_country_id']=0;
                  $selected_ea['ea_nomos_id']=0;
                  
                  
                  while ($row_select = $result_select->fetch_assoc()) {
                    $row_select['country_name']=gks_lang_data_trans($row_select['country_name'],$row_select['ea_country_id'],'gks_country','country_name');
                    $row_select['nomos_descr']=gks_lang_data_trans($row_select['nomos_descr'],$row_select['ea_nomos_id'],'gks_nomoi','nomos_descr');
                    
                    $address_name=$row_select['ea_name'].', '.trim_gks($row_select['ea_odos'].' '.$row_select['ea_arithmos']).', '.$row_select['ea_orofos'].', '.$row_select['ea_perioxi'].', '.$row_select['ea_poli'].', '.$row_select['ea_tk'].', '.$row_select['country_name'].', '.$row_select['nomos_descr'];
                  
                    $address_name=str_replace(', , ', ', ', $address_name);
                    $address_name=str_replace(', , ', ', ', $address_name);
                    $address_name=str_replace(', , ', ', ', $address_name);
                    $address_name=str_replace(', , ', ', ', $address_name);
                    
                    if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
                    if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
                    if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
                  
                    if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
                    if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
                    if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
                    
                    
                    echo '<option value="'.$row_select['id_users_extra_address'].'" ';
                    if ($row['users_extra_address_id'] == $row_select['id_users_extra_address']) {
                      echo ' selected ';
                      $selected_ea=$row_select;
                      $row['ea_name']=$row_select['ea_name'];
                      $row['ea_phone']=$row_select['ea_phone'];
                      
                      $row['ma_odos']=$row_select['ea_odos'];
                      $row['ma_arithmos']=$row_select['ea_arithmos'];
                      $row['ma_orofos']=$row_select['ea_orofos'];
                      $row['ma_perioxi']=$row_select['ea_perioxi'];
                      $row['ma_poli']=$row_select['ea_poli'];
                      $row['ma_tk']=$row_select['ea_tk'];
                      $row['ma_country_id']=$row_select['ea_country_id'];
                      $row['country_name']=$row_select['country_name'];
                      $row['ma_nomos_id']=$row_select['ea_nomos_id'];
                      $row['nomos_descr']=$row_select['nomos_descr'];
                    }
                    echo '>'.$address_name.'</option>';
                  }
                  ?>
                                    
                </select>
              
            </div>
          </div>  

          <div class="form-group row" id="dr_ea_" style="<?php if ($row['users_extra_address_id']==-1) echo 'display:none;';?>">
            <label for="dr_ea_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_ea_name">
                <?php echo $row['ea_name'];?>
              </div>
            </div>
            <label for="dr_ea_phone" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_ea_phone">
                <?php echo $row['ea_phone'];?>
              </div>
            </div>
          </div>


          <div class="form-group row">
            <label for="dr_user_ma_odos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_odos">
                <?php echo $row['ma_odos'];?>
              </div>
            </div>
            <label for="dr_user_ma_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_arithmos">
                <?php echo $row['ma_arithmos'];?>
              </div>
            </div>
            
          </div>
          <div class="form-group row">
            <label for="dr_user_ma_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_orofos">
                <?php echo $row['ma_orofos'];?>
              </div>
            </div>
            <label for="dr_user_ma_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_perioxi">
                <?php echo $row['ma_perioxi'];?>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label for="dr_user_ma_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_poli">
                <?php echo $row['ma_poli'];?>
              </div>
            </div>
            <label for="dr_user_ma_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_tk">
                <?php echo $row['ma_tk'];?>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label for="dr_user_ma_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_country_id" data-id="<?php echo $row['ma_country_id'];?>">
                <?php 
                echo gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');
                ?>
              </div>
            </div>
            <label for="dr_user_ma_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_nomos_id">
                <?php 
                echo gks_lang_data_trans($row['nomos_descr'],$row['ma_nomos_id'],'gks_nomoi','nomos_descr');
                ?>
              </div>
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_crm_machine'];?>" data-model="gks_crm_machine" data-backurl="admin-crm-machine.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90();?>

<?php if ($GKS_CRM_TASKS_ENABLE) {?>
<?php if ($perm_crm_tasks_view) {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-xl-12">
  
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Εργασίες');?></span>
          <?php if ($id>0 and $perm_crm_tasks_add) {?>
          <a class="btn btn-sm btn-primary gks_stoppropagation" style="margin-left:10px;" href="admin-crm-task-item.php?id=-1&crm_task_machine_id=<?php echo $id;?>">
            <?php echo gks_lang('Προσθήκη');?>
          </a>
          <?php } ?>
        </div>
        <div class="card-body" <?php echo gks_card_body('tasks');?>>  
 
<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  colspan="<?php
          if ($perm_crm_tasks_delete) echo '3'; else echo '2';
        ?>">ID</th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo gks_lang('Ημερομηνία');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Κατάσταση');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Εργασία');?></th>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><span title="<?php echo gks_lang('Αναμενόμενα έσοδα');?>"><?php echo gks_lang('Α.Έσοδα');?></span></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Προγραμματισμός');?></th>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Υπάλληλοι');?></th>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Επαφή');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Κινητό');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Τηλέφωνο');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo gks_lang('email');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Πόλη');?></th>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Στίγμα');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Εταιρεία');?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Υποκατάστημα');?></th>        
    </tr>
</thead>
<tbody> 
<?php
$sql_list = "SELECT gks_crm_tasks.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_crm_tasks_status.task_status_descr, gks_crm_tasks_status.task_status_color, gks_crm_tasks_status.task_status_sortorder,
gks_company.company_title, gks_company_subs.company_sub_title,
gks_country.country_name, gks_nomoi.nomos_descr
FROM (((((((gks_crm_tasks 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_tasks.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_tasks.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_crm_tasks_status ON gks_crm_tasks.task_status_id = gks_crm_tasks_status.id_crm_task_status)
LEFT JOIN gks_company ON gks_crm_tasks.company_id = gks_company.id_company) 
LEFT JOIN gks_company_subs ON gks_crm_tasks.company_sub_id = gks_company_subs.id_company_sub) 
LEFT JOIN gks_country ON gks_crm_tasks.country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_crm_tasks.nomos_id = gks_nomoi.id_nomos
where gks_crm_tasks.id_crm_task in (
  select crm_task_id from gks_crm_tasks_machine where crm_task_machine_id=".$id." group by crm_task_id
)
ORDER BY gks_crm_tasks.id_crm_task desc";
$result_list = $db_link->query($sql_list); 
if (!$result_list) {
  debug_mail(false,'error sql',$sql_list);
  die('sql error');
}
$data=array();
$id_crm_task_ids=array();
while ($row_list = $result_list->fetch_assoc()) {
  $row_list['employee']=array();
  $data[$row_list['id_crm_task']]=$row_list;
  $id_crm_task_ids[]=$row_list['id_crm_task'];
}

if (count($id_crm_task_ids)>0) {
  $sql_list="SELECT gks_crm_tasks_employee.crm_task_id, gks_crm_tasks_employee.crm_task_employee_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  FROM gks_crm_tasks_employee 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks_employee.crm_task_employee_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_crm_tasks_employee.crm_task_id in (".implode(',',$id_crm_task_ids).")
  ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;";
  $result_list = $db_link->query($sql_list);        
  if (!$result_list) debug_mail(false,'error sql',$sql_listv);
  if (!$result_list) die('sql error');
  while ($row_list = $result_list->fetch_assoc()) {
    $data[$row_list['crm_task_id']]['employee'][]='<a href="admin-users-item.php?id='.$row_list['crm_task_employee_id'].'">'.$row_list['gks_nickname'].'</a>';
  }
  
}

 
$i = 0;
foreach ($data as $row_list) {

  $i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?> crm_task_tr" data-id="<?php echo $row_list['id_crm_task'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></th>
    <td nowrap class="mytdcm p-0"><a href="admin-crm-task-item.php?id=<?php echo $row_list['id_crm_task'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
    <td nowrap class="mytdcm p-0"><?php echo $row_list['id_crm_task'];?></td>
    <?php if ($perm_crm_tasks_delete) {?>    
    <td nowrap class="mytdcm p-0"><i class="fas fa-trash-alt deleterow" data-deleteafter="gks_fnc_crm_task_delete_after|<?php echo $row_list['id_crm_task'];?>" data-id="<?php echo $row_list['id_crm_task'];?>" data-model="gks_crm_tasks"></i></td>
    <?php } ?>
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row_list['task_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    <td nowrap class="mytdcm"><span class="task_status_<?php echo $row_list['task_status_id'];?>"><?php if (isset($tasks_status[$row_list['task_status_id']])) echo $tasks_status[$row_list['task_status_id']]['task_status_descr'];?></span></td>

    <td class="mytdcml" <?php if (trim_gks($row_list['task_color'])!='') echo 'style="background-color:'.$row_list['task_color'].'"';?>><?php echo $row_list['subject'];?></td>
    <td class="mytdcmr"><?php if ($row_list['esoda']!=0) echo myCurrencyFormat($row_list['esoda']);?></td>
    <td nowrap class="mytdcm"><?php 
      if (isset($row_list['task_planned_date_from'])) echo showDate(strtotime($row_list['task_planned_date_from']), 'd/m/Y H:i', 1);
      echo '<br>';
      if (isset($row_list['task_planned_date_to'])) echo showDate(strtotime($row_list['task_planned_date_to']), 'd/m/Y H:i', 1);
    ?></td>   

    <td class="mytdcml"><?php
      echo implode('<br>',$row_list['employee']);
    ?></td>
    <td class="mytdcml"><?php 
    $taskuname=trim_gks(trim_gks($row_list['last_name']).' '.trim_gks($row_list['first_name']));
    if ($row_list['user_id']>0) {
      echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.($taskuname!='' ? $taskuname : $row_list['gks_nickname']).'</a>';
    } else {
      echo $taskuname;
    }?></td>
    
    
    <td class="mytdcml"><?php echo $row_list['mobile'];?></td>
    <td class="mytdcml"><?php echo $row_list['phone'];?></td>
    <td class="mytdcml"><?php echo $row_list['email'];?></td>
    <td class="mytdcml"><?php echo $row_list['poli'];?></td>
    <td class="mytdcm"><?php if ($row_list['map_latitude']==0 and $row_list['map_longitude']==0) {
        $pos_task=0;
      } else {
        $pos_task=1;
      }?>
      <img src="img/<?php echo $pos_task;?>.png" border="0" width="16"></td>
    </td>
    <td class="mytdcml"><a href="admin-company-item.php?id=<?php echo $row_list['company_id'];?>"><?php echo $row_list['company_title'];?></a></td>
    <td class="mytdcml"><a href="admin-company-sub-item.php?id=<?php echo $row_list['company_sub_id'];?>"><?php echo $row_list['company_sub_title'];?></a></td>
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
<?php }} ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      
      <?php echo getObjectRels('gks_crm_machine',$id); ?>
      <?php echo getActivityObjectTable('gks_crm_machine',$id); ?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Μηνύματα');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="message_item_add"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('message');?>>
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
            <thead>
              <tr>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;">#</th>
                <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
                <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>                
                <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Μήνυμα');?></th>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;"><i class="fas fa-envelope" style="color: #35dc35;font-size: 120%;"></i></th>
              </tr>
            </thead>  
            <tbody id="item_messages_body"> 
              
            <?php
            $sql_msg="SELECT gks_crm_machine_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_crm_machine_messages LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_machine_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_crm_machine_messages.crm_machine_id=".$id."
            ORDER BY gks_crm_machine_messages.mydate_add DESC, gks_crm_machine_messages.id_crm_machine_message DESC;";
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg['id_crm_machine_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_msg['crm_machine_message']);
                ?></div></div></td>    
              <td class="mytdcm"><?php 
                if ($row_msg['email_id']!=0) {
                  echo '<i class="fas fa-envelope gks_email_view" data-id="'.$row_msg['email_id'].'"></i>';
                }
                if ($row_msg['sms_id']!=0) {
                  echo '<i class="fas fa-sms gks_sms_view" data-id="'.$row_msg['sms_id'].'"></i>';
                }                
                ?></td>
            </tr>
            <?php } ?>                      
            </tbody>   
          </table>                
        </div>
      </div>
            
			<?php
			$obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_crm_machine','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
      

       
    </div>
    <div class="col-md-6">


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιστορικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('his');?>>      

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
            $sql_log="SELECT gks_crm_machine_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_crm_machine_log LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_machine_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_crm_machine_log.crm_machine_id=".$id."
            ORDER BY gks_crm_machine_log.id_gks_crm_machine_log DESC;";
            $result_log = $db_link->query($sql_log);        
            if (!$result_log) debug_mail(false,'error sql',$sql_log);
            if (!$result_log) die('sql error');
            
            $j = 0;
            while ($row_log = $result_log->fetch_assoc()) {
              $j++; ?>
          
            <tr>
              <th scope="row" align="center"><?php echo $j;?></th>
              <td align="left"><?php echo showDate(strtotime($row_log['add_date']), 'd/m/Y H:i:s', 1);?></td>  
              <td align="left"><?php echo $row_log['gks_nickname'];?></td>  
              <td align="left"><?php echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_log['sxolio']);?></td>    
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
        <div class="card-body" <?php echo gks_card_body('kat');?>>       


          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_crm_machine']>0) echo $row['id_crm_machine'];?></span></div>
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


<?php include_once 'admin-obj-send-message.php'; ?>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>



var from_php_dialog_object_rel_curr='gks_crm_machine';
var from_php_activity_model='gks_crm_machine';
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



var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_machine','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_machine','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_machine','delete',$id);?>;

var from_php_check_vies_valid_wait=<?php echo ($mybasketarray['check_vies']['valid']==2 ? 'true' : 'false');?>;

var from_php_dialog_item_message_email_from_array=[];
<?php 
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>









jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  



  
  

    
});
</script>


<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="js/admin-crm-machine-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


