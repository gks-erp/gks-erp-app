<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$nav_active_array=array('hotel','hotel_manage');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');


$perm_gks_hotel_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel','view',0);
$perm_gks_hotel_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel','edit',0);
$perm_gks_hotel_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel','add',0);
$perm_gks_hotel_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel','delete',0);


$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel',['from'=>'item']);


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_hotel']=-1;
  $row['hotel_title']='';

  $row['hotel_phone']='';
  $row['hotel_email']='';
  $row['hotel_website']='';
  $row['hotel_odos']='';
  $row['hotel_arithmos']='';
  $row['hotel_orofos']='';
  $row['hotel_perioxi']='';
  $row['hotel_poli']='';
  $row['hotel_tk']='';
  $row['hotel_nomos_id']=0;
  $row['hotel_country_id']=91;
  $row['hotel_map_latitude']='';
  $row['hotel_map_longitude']='';
  $row['hotel_disable']=0;

  $row['gks_nickname'] ='';
  $row['hotel_color']='';


  $row['default_eshop_hotel']=0;
  
  $row['company_id']=0;
  $row['company_sub_id']=0;


  $row['hotel_default_availability']=0;
  $row['hotel_date_open']=date('Y-m-d');
  $row['hotel_date_close']='';
  $row['hotel_default_checkin']='14:00';
  $row['hotel_default_checkout']='12:00';
  $row['hotel_default_price']=0;
  $row['hotel_reservation_can_select_room']=0;
  $row['hotel_reservation_days_future']=90;
  $row['hotel_reservation_min_days_online']=0;
  $row['hotel_reservation_max_days_online']=365;
  
  
  $row['hotel_child_accept']=1;
  $row['hotel_child_accept_above_age']=0;
  
  
  $row['hotel_child_age_price']='';
  $row['hotel_child_kounies']='';
  $row['hotel_extra_beds']='';
  
  $row['hotel_efd_product_id']=0;
  $row['product_descr_p']='';


  $row['hotel_template_eidos_descr']=
      '{room_name ['.gks_lang('Δωμάτιο').': %%]}'."\r\n".
      '{room_type ['.gks_lang('Τύπος δωματίου').': %%]}'."\r\n".
      '{check_in_dtw ['.gks_lang('Από').': %%]}'."\r\n".
      '{check_out_dtw ['.gks_lang('Έως').': %%]}'."\r\n".
      '{days ['.gks_lang('Διανυκτερεύσεις').': %%]}'."\r\n".
      '{adults [hide:zero]['.gks_lang('Ενήλικες').': %%]}'."\r\n".
      '{childs [hide:zero]['.gks_lang('Παιδιά').': %%]}'."\r\n".
      '{visitors [hide:zero]['.gks_lang('Επισκέπτες').': %%]}'."\r\n".
      '{child_kounies [hide:zero]['.gks_lang('Βρεφικά κρεβάτια').': %%]}'."\r\n".
      '{extra_beds [hide:zero]['.gks_lang('Επιπλέον κρεβάτια').': %%]}'; //."\r\n".

//  $row['hotel_template_eidos_descr_en_US']=
//      '{room_name [Room: %%]}'."\r\n".
//      '{room_type [Room Type: %%]}'."\r\n".
//      '{check_in_dtw [From: %%]}'."\r\n".
//      '{check_out_dtw [To: %%]}'."\r\n".
//      '{days [Nights: %%]}'."\r\n".
//      '{adults [hide:zero][Adults: %%]}'."\r\n".
//      '{childs [hide:zero][Children: %%]}'."\r\n".
//      '{visitors [hide:zero][Visitors: %%]}'."\r\n".
//      '{child_kounies [hide:zero][Baby cots: %%]}'."\r\n".
//      '{extra_beds [hide:zero][Extra beds: %%]}'; //."\r\n".

  $row['hotel_template_efd_descr']=
      '{room_name ['.gks_lang('Δωμάτιο').': %%]}'."\r\n".
      '{room_type ['.gks_lang('Τύπος δωματίου').': %%]}'."\r\n".
      '{check_in_dtw ['.gks_lang('Από').': %%]}'."\r\n".
      '{check_out_dtw ['.gks_lang('Έως').': %%]}'."\r\n".
      '{days ['.gks_lang('Διανυκτερεύσεις').': %%]}'."\r\n".
      '{adults [hide:zero]['.gks_lang('Ενήλικες').': %%]}'."\r\n".
      '{childs [hide:zero]['.gks_lang('Παιδιά').': %%]}'."\r\n".
      '{visitors [hide:zero]['.gks_lang('Επισκέπτες').': %%]}'."\r\n".
      '{child_kounies [hide:zero]['.gks_lang('Βρεφικά κρεβάτια').': %%]}'."\r\n".
      '{extra_beds [hide:zero]['.gks_lang('Επιπλέον κρεβάτια').': %%]}'; //."\r\n".

//  $row['hotel_template_efd_descr_en_US']=
//      '{room_name [Room: %%]}'."\r\n".
//      '{room_type [Room Type: %%]}'."\r\n".
//      '{check_in_dtw [From: %%]}'."\r\n".
//      '{check_out_dtw [To: %%]}'."\r\n".
//      '{days [Nights: %%]}'."\r\n".
//      '{adults [hide:zero][Adults: %%]}'."\r\n".
//      '{childs [hide:zero][Children: %%]}'."\r\n".
//      '{visitors [hide:zero][Visitors: %%]}'."\r\n".
//      '{child_kounies [hide:zero][Baby cots: %%]}'."\r\n".
//      '{extra_beds [hide:zero][Extra beds: %%]}'; //."\r\n".

  $row['hotel_template_woo_descr']=
      '{room_name ['.gks_lang('Δωμάτιο').': %%'."\r\n".']}'.
      '{room_type ['.gks_lang('Τύπος δωματίου').': %%'."\r\n".']}'.
      '{check_in_dtw ['.gks_lang('Από').': %%'."\r\n".']}'.
      '{check_out_dtw ['.gks_lang('Έως').': %%'."\r\n".']}'.
      '{days ['.gks_lang('Διανυκτερεύσεις').': %%'."\r\n".']}'.
      '{adults [hide:zero]['.gks_lang('Ενήλικες').': %%'."\r\n".']}'.
      '{childs [hide:zero]['.gks_lang('Παιδιά').': %%'."\r\n".']}'.
      '{visitors [hide:zero]['.gks_lang('Επισκέπτες').': %%'."\r\n".']}'.
      '{child_kounies [hide:zero]['.gks_lang('Βρεφικά κρεβάτια').': %%'."\r\n".']}'.
      '{extra_beds [hide:zero]['.gks_lang('Επιπλέον κρεβάτια').': %%]}'; //."\r\n".
  
//  $row['hotel_template_woo_descr_en_US']=
//      '{room_name [Room: %%'."\r\n".']}'.
//      '{room_type [Room Type: %%'."\r\n".']}'.
//      '{check_in_dtw [From: %%'."\r\n".']}'.
//      '{check_out_dtw [To: %%'."\r\n".']}'.
//      '{days [Nights: %%'."\r\n".']}'.
//      '{adults [hide:zero][Adults: %%'."\r\n".']}'.
//      '{childs [hide:zero][Children: %%'."\r\n".']}'.
//      '{visitors [hide:zero][Visitors: %%'."\r\n".']}'.
//      '{child_kounies [hide:zero][Baby cots: %%'."\r\n".']}'.
//      '{extra_beds [hide:zero][Extra beds: %%]}'; //."\r\n".
//  
  
  
  $row['hotel_use_checkout_system']='';

  $row['hotel_website_key']=rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
  
  
  $row['hotel_id_booking']='';
  $row['hotel_id_airbnb']='';

  $row['hotel_booking_number_prefix']='';
    
  $my_page_title=gks_lang('Νέο ξενοδοχείο');
} else {
  $sql ="SELECT gks_hotel.*, gks_country.country_name, gks_nomoi.nomos_descr,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  gks_company.company_title, gks_company_subs.company_sub_title,
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
  FROM (((((((gks_hotel 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN gks_company ON gks_hotel.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_hotel.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN gks_country ON gks_hotel.hotel_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_hotel.hotel_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_eshop_products ON gks_hotel.hotel_efd_product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
  
  where id_hotel = ".$id;
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel.id_hotel in (".implode(',',$perm_id_hotel_ids).")";
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
  $my_page_title=gks_lang('Ξενοδοχείο').': '.$row['hotel_title'];
  $object_title=$row['hotel_title'];
}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

stat_record();


$hotel_child_age_price=trim_gks($row['hotel_child_age_price']);
if ($hotel_child_age_price=='') $hotel_child_age_price=array();
else $hotel_child_age_price=json_decode($hotel_child_age_price, true);



$hotel_child_kounies=trim_gks($row['hotel_child_kounies']);
if ($hotel_child_kounies=='') $hotel_child_kounies=array('enable' => false, 'from' => 0, 'to' => 0, 'price' => 0, 'type' => 'night');
else $hotel_child_kounies=json_decode($hotel_child_kounies, true);


$hotel_extra_beds=trim_gks($row['hotel_extra_beds']);
if ($hotel_extra_beds=='') $hotel_extra_beds=array('enabled' => false,'beds' => array());
else $hotel_extra_beds=json_decode($hotel_extra_beds, true); 
      
//usort($hotel_child_age_price, "GKS_HOTEL_CHILD_AGE_PRICE_SORT");
//usort($hotel_extra_beds['beds'], "GKS_HOTEL_EXTRA_BEDS_SORT");

$user_companys=gks_get_companys_list();
if (count($user_companys)==0) {
  debug_mail(false,'company not found','');
  echo 'company not found';
  die(); 
}

$lang_data_obj=gks_lang_data_obj_prepare('gks_hotel','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);

      
//print '<pre>';print_r($hotel_extra_beds);die();

include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Ξενοδοχείο');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Ξενοδοχείο');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
       
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εταιρεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('comp');?>> 

          <div class="form-group row">
            <label for="hotel_title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τίτλος');?>:</label>
            <div class="col-md-8">
              <input id="hotel_title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_title']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="company_id_sub_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="company_id_sub_id" class="form-control form-control-sm myneedsave">
                <option value="0|0"></option>
                <?php
                $company_id_sub_id=$row['company_id'].'|'.$row['company_sub_id'];
                foreach ($user_companys as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ' .
                  ' data-afm="'.$row_select['company_afm'].'" ';
                  if ($row_select['id']==$company_id_sub_id) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>
            </div>
          </div>          
          <div class="form-group row">
            <label for="hotel_phone" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερό Τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="hotel_phone" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_phone']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="hotel_email" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ηλ. διεύθυνση');?>:</label>
            <div class="col-md-8">
              <input id="hotel_email" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_email']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="hotel_website" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ιστότοπος');?>:</label>
            <div class="col-md-8">
              <input id="hotel_website" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_website']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="hotel_odos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-md-8">
              <input id="hotel_odos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_odos']);?>">
              <small class="form-text text-muted auto_googlemaps" id="hotel_odos_auto_googlemaps"></small>
            </div>
          </div> 
          <div class="form-group row">
            <label for="hotel_arithmos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <input id="hotel_arithmos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_arithmos']);?>">
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="hotel_orofos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-md-8">
              <input id="hotel_orofos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_orofos']);?>">
            </div>
          </div>          
          <div class="form-group row">
            <label for="hotel_perioxi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-md-8">
              <input id="hotel_perioxi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_perioxi']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="hotel_poli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-md-8">
              <input id="hotel_poli" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_poli']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="hotel_tk" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΤΚ');?>:</label>
            <div class="col-md-8">
              <input id="hotel_tk" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_tk']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="hotel_nomos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-md-8">
              <select id="hotel_nomos_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php

                $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomos['sql']['from2']."
                where country_id=".$row['hotel_country_id']." ORDER BY nomos_descr";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_nomos'].'" ';
                  if ($row_select['id_nomos']==$row['hotel_nomos_id']) echo ' selected ';
                  echo '>'.$row_select['nomos_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>
          <div class="form-group row">
            <label for="hotel_country_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-md-8">
              <select id="hotel_country_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
                $sql="select id_country,country_ee,country_initials,".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
                FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
                ".$lang_prepare_gks_country['sql']['from2']."
                ORDER BY country_name";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" ';
                  if ($row_select['id_country']==$row['hotel_country_id']) echo ' selected ';
                  echo '>'.$row_select['country_name'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
    
          

          <div class="form-group row">
            <label for="hotel_map_latitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Πλάτος');?>:</label>
            <div class="col-md-8">
              <input id="hotel_map_latitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_map_latitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-90" max="90" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label for="hotel_map_longitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Μήκος');?>:</label>
            <div class="col-md-8">
              <input id="hotel_map_longitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_map_longitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-180" max="180" step="0.00001">
            </div>
          </div> 

          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χάρτης');?>:</label>
            <div class="col-md-8">
              <div style="text-align:left;">
                <button id="showmap" class="btn btn-sm btn-primary" style="cursor:pointer"><?php echo gks_lang('Εμφάνιση χάρτη');?></button>
                <button id="geocode_pos" class="btn btn-sm btn-info" style="cursor:pointer;" disabled><?php echo gks_lang('Στίγμα');?> <span id="geocode_pos_icon"><i class="fas fa-map-marker-alt"></i></span></button>
                <button id="map_pos" class="btn btn-sm btn-info" style="cursor:pointer;" disabled title="<?php echo gks_lang('Εντοπισμός της τρέχουσας θέσης σας');?>"><?php echo gks_lang('Εδώ');?></button>
                
                </div>
            </div>
            <div class="col-md-12" style="height:0px">
              <div id="map" style="width:100%;height:100%"></div>  
            </div>             
          </div>


          <div class="form-group row">
            <label for="hotel_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="hotel_disable" value="1" <?php if ($row['hotel_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>  
          

          
          <div class="form-group row">
            <label for="hotel_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα');?>:</label>
            <div class="col-md-8">
              <input id="hotel_color" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_color']);?>" style="max-width:200px;">
            </div>
          </div> 



        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Λογιστική');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('logistiki');?>> 

          <div class="form-group row">
            <label for="hotel_template_eidos_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή είδους για παραστατικό');?>:</label>
            <div class="col-md-8">
              <textarea id="hotel_template_eidos_descr" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_hotel_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['hotel_template_eidos_descr']);?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('hotel_template_eidos_descr'));
          ?>
                    
          
          <div class="form-group row">
            <label for="hotel_template_efd_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή είδους για φόρο διαμονής');?>:</label>
            <div class="col-md-8">
              <textarea id="hotel_template_efd_descr" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_hotel_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['hotel_template_efd_descr']);?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('hotel_template_efd_descr'));
          ?>

          
          <div class="form-group row">
            <label for="hotel_template_woo_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή είδους για WooCommerce');?>:</label>
            <div class="col-md-8">
              <textarea id="hotel_template_woo_descr" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_hotel_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['hotel_template_woo_descr']);?></textarea>
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('hotel_template_woo_descr'));
          ?>



          <div class="form-group row">
            <label for="hotel_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πεδία');?>:</label>
            <div class="col-md-8" style="font-size: 0.875rem;padding-top: 4px;">
              <?php
              $tt=_time_user(time(),1);
              ?>
              {room_name} : <?php echo gks_lang('Όνομα δωματίου');?><br>
              {room_name_en} : <?php echo gks_lang('Όνομα δωματίου στα Αγγλικά');?><br>
              {room_type} : <?php echo gks_lang('Όνομα τύπου δωματίου');?><br>
              {room_type_en} : <?php echo gks_lang('Όνομα τύπου δωματίου στα Αγγλικά');?><br>
              {check_in} : <?php echo gks_lang('Άφιξη με μορφή');?>: <?php echo myDateTimeFormat($tt);?><br>
              {check_in_d} : <?php echo gks_lang('Άφιξη με μορφή');?>: <?php echo myDateFormat($tt);?><br>
              {check_in_dw} : <?php echo gks_lang('Άφιξη με μορφή');?>: <?php echo myDateFormatw($tt);?><br>
              {check_in_dt} : <?php echo gks_lang('Άφιξη με μορφή');?>');?>: <?php echo myDateTimeFormat($tt);?><br>
              {check_in_dtw} : <?php echo gks_lang('Άφιξη με μορφή');?>: <?php echo myDateTimeFormatw($tt);?><br>
              {check_out} : <?php echo gks_lang('Αναχώρηση με μορφή');?>: <?php echo myDateTimeFormat($tt);?><br>
              {check_out_d} : <?php echo gks_lang('Αναχώρηση με μορφή');?>: <?php echo myDateFormat($tt);?><br>
              {check_out_dw} : <?php echo gks_lang('Αναχώρηση με μορφή');?>: <?php echo myDateFormatw($tt);?><br>
              {check_out_dt} : <?php echo gks_lang('Αναχώρηση με μορφή');?>: <?php echo myDateTimeFormat($tt);?><br>
              {check_out_dtw} : <?php echo gks_lang('Αναχώρηση με μορφή');?>: <?php echo myDateTimeFormatw($tt);?><br>

              {days} : <?php echo gks_lang('Διανυκτερεύσεις διαμονής');?><br>
              {adults} : <?php echo gks_lang('Πλήθος ενηλίκων');?><br>
              {childs} : <?php echo gks_lang('Πλήθος παιδιών');?><br>
              {visitors} : <?php echo gks_lang('Πλήθος επισκεπτών');?><br>
              {child_kounies} : <?php echo gks_lang('Βρεφικά κρεβάτια');?><br>
              {extra_beds}: <?php echo gks_lang('Επιπλέον κρεβάτια');?><br>
              
            </div>
          </div> 

          <div class="form-group row">
            <label for="hotel_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παράμετροι');?>:</label>
            <div class="col-md-8" style="font-size: 0.875rem;padding-top: 4px;">
              [hide:zero] <?php echo gks_lang('θα γίνει απόκρυψη εάν η τιμή είναι μηδέν');?><br>
              [<?php echo gks_lang('Κείμενο');?>: %%] <?php echo gks_lang('θα γίνει αντικατάσταση του %% με την τιμή');?>
              
            </div>
          </div>
          
          <div class="form-group row">
            <label for="hotel_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παράδειγμα');?>:</label>
            <div class="col-md-8" style="font-size: 0.875rem;padding-top: 4px;">
              <?php echo gks_lang('Το');?><br>
              <b>{extra_beds [hide:zero][<?php echo gks_lang('Επιπλέον κρεβάτια');?>: %%]}</b><br>
              <?php echo gks_lang('εάν η τιμή είναι 5, θα έχει ως αποτέλεσμα το');?>:<br>
              <b><?php echo gks_lang('Επιπλέον κρεβάτια');?>: 5</b><br>
              <?php echo gks_lang('ενώ εάν η τιμή είναι 0 τότε θα γίνει απόκρυψη.');?>
              
            </div>
          </div>
          
          
        </div>
      </div>
    
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('eshop');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('eshop');?>> 
          <div class="form-group row">
            <label for="default_eshop_hotel" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προεπιλεγμένο ξενοδοχείο για eshop');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="default_eshop_hotel" value="1" <?php if ($row['default_eshop_hotel']==1) echo ' checked '; ?> class="switchery1_this">
              <?php
              $text_exist_no=gks_lang('Δεν έχει ορισθεί προεπιλεγμένο ξενοδοχείο');
              $sql_defeshop="select id_hotel,hotel_title from gks_hotel where default_eshop_hotel<>0";
              $result_defeshop = $db_link->query($sql_defeshop);
              if (!$result_defeshop) {
                debug_mail(false,'error sql',$sql_defeshop);
                $return = array('success' => false, 'message' => base64_encode('sql error'));
                echo json_encode($return); die(); }
              if ($result_defeshop->num_rows>0) {
                $row_defeshop = $result_defeshop->fetch_assoc();
                if ($row_defeshop['id_hotel']!=$id) {
                   $text_exist_no=gks_lang('Το προεπιλεγμένο ξενοδοχείο είναι το <b>[1]</b>');
                   $text_exist_no=str_replace('[1]',$row_defeshop['hotel_title'],$text_exist_no);
                }
              } 
              ?>
              <small class="form-text text-muted" id="text_default_eshop_hotel_yes" style="<?php if ($row['default_eshop_hotel']==0) echo 'display:none;'; ?>"><?php echo gks_lang('Το τρέχον ξενοδοχείο είναι το προεπιλεγμένο');?></small>
              <small class="form-text text-muted" id="text_default_eshop_hotel_no"  style="<?php if ($row['default_eshop_hotel']!=0) echo 'display:none;'; ?>"><?php echo $text_exist_no;?></small>
            </div>
          </div>      
     
          <div class="form-group row">
            <label for="hotel_website_key" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κλειδί');?>:</label>
            <div class="col-md-8">
              <input id="hotel_website_key" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_website_key']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="hotel_use_checkout_system" class="col-md-4 col-form-label form-control-sm text-md-right">Checkout:</label>
            <div class="col-md-8">
              <select id="hotel_use_checkout_system"  class="form-control form-control-sm">
                <option value="" <?php if ($row['hotel_use_checkout_system']=='') echo 'selected';?>>gks</option>
                <option value="woocommerce" <?php if ($row['hotel_use_checkout_system']=='woocommerce') echo 'selected';?>>WooCommerce</option>
              </select>
            </div>
          </div> 

     
        </div>
      
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κανάλια πωλήσεων');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('salescha');?>> 

          <div class="form-group row">
            <label for="hotel_id_booking" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Booking Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="hotel_id_booking" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_id_booking']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="hotel_id_airbnb" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Airbnb Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="hotel_id_airbnb" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hotel_id_airbnb']);?>">
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



        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικές ρυθμίσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>         
          <div class="form-group row">
            <label for="gks_hotel_default_availability" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Το ξενοδοχείο είναι ανοιχτό');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="gks_hotel_default_availability" class="switchery1_this" <?php if ($row['hotel_default_availability']!=0) echo ' checked ';?> >
            </div>
          </div> 

          <div class="form-group row">
            <label for="hotel_booking_number_prefix" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πρόθεμα Κράτησης');?>:</label>
            <div class="col-sm-6">
              <input id="hotel_booking_number_prefix" type="text" class="form-control form-control-sm myneedsave" value="<?php echo trim_gks($row['hotel_booking_number_prefix']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
              <small class="form-text text-muted">
                <?php echo gks_lang('Αφορά τις κρατήσεις που γίνονται από εδώ, μέσω του gks ERP.');?> 
                <?php echo gks_lang('Οι κρατήσεις που εισάγονται από τον ιστότοπο έχουν το δικό τους πρόθεμα.');?>
                <?php echo gks_lang('Δείτε τα σχετικά <a href="admin-eshop.php">eshop</a> σας');?>
              </small>
            </div>
          </div>

          <div class="form-group row">
            <label for="gks_hotel_date_open" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία Έναρξης Λειτουργίας');?>:</label>
            <div class="col-sm-6">
              <input id="gks_hotel_date_open" type="text" class="form-control form-control-sm myneedsave" value="<?php 
              $hotel_date_open=trim_gks($row['hotel_date_open']);
              
              if ($hotel_date_open!='') echo  date('d/m/Y',strtotime($hotel_date_open));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div> 
          <div class="form-group row">
            <label for="gks_hotel_date_close" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία Λήξης Λειτουργίας');?>:</label>
            <div class="col-sm-6">
              <input id="gks_hotel_date_close" type="text" class="form-control form-control-sm myneedsave" value="<?php 
              $hotel_date_close=trim_gks($row['hotel_date_close']);
              if ($hotel_date_close!='') echo  date('d/m/Y',strtotime($hotel_date_close));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div>
          

          <div class="form-group row">
            <label for="gks_hotel_default_checkin" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προεπιλεγμένη ώρα άφιξης');?>:</label>
            <div class="col-md-6">
              <select id="gks_hotel_default_checkin"  class="form-control form-control-sm" style="max-width:200px">
                <?php
                $parts=explode(':',$row['hotel_default_checkin']);
                $val=(intval($parts[0])*60+intval($parts[1])) *60;
                for ($i=0; $i < 24*60*60; $i+=30*60) {
                  echo '<option '.($val==$i ? 'selected': '').'>'.date('H:i',$i).'</option>';
                }
                ?>
              </select>
            </div>
          </div>    
          <div class="form-group row">
            <label for="gks_hotel_default_checkout" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προεπιλεγμένη ώρα αναχώρησης');?>:</label>
            <div class="col-md-6">
              <select id="gks_hotel_default_checkout"  class="form-control form-control-sm" style="max-width:200px">
                <?php
                $parts=explode(':',$row['hotel_default_checkout']);
                $val=(intval($parts[0])*60+intval($parts[1])) *60;
                for ($i=0; $i < 24*60*60; $i+=30*60) {
                  echo '<option '.($val==$i ? 'selected': '').'>'.date('H:i',$i).'</option>';
                }
                ?>
              </select>
            </div>
          </div>               
          <div class="form-group row">
            <label for="gks_hotel_default_price" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προεπιλεγμένη τιμή δωματίου');?>:</label>
            <div class="col-md-6">
              <input id="gks_hotel_default_price" type="number" class="form-control form-control-sm" value="<?php echo number_format($row['hotel_default_price'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="max-width:200px" min="0" step="1">
            </div>
          </div>  
          <div class="form-group row">
            <label for="gks_hotel_reservation_can_select_room" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επιλογή δωματίου στο Online');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="gks_hotel_reservation_can_select_room" class="switchery1_this" <?php if ($row['hotel_reservation_can_select_room']) echo ' checked ';?> aria-describedby="gks_hotel_reservation_can_select_roomHelp">
              <small id="gks_hotel_reservation_can_select_roomHelp" class="form-text text-muted"><?php echo gks_lang('Εάν είναι ενεργό θα μπορεί ο πελάτης να επιλέξει συγκεκριμένο δωμάτιο κατά την κράτηση, διαφορετικά θα επιλέξει τύπο δωματίου');?></small>
            </div>
          </div> 

          <div class="form-group row">
            <label for="gks_hotel_reservation_days_future" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όριο ημερών έναρξης κράτησης στο Online');?>:</label>
            <div class="col-md-6">
              <input id="gks_hotel_reservation_days_future" type="number" class="form-control form-control-sm" value="<?php echo $row['hotel_reservation_days_future'];?>" 
              aria-describedby="gks_hotel_reservation_days_futureHelp" style="max-width:200px" min="1" max="365" step="1">
              <small id="gks_hotel_reservation_days_futureHelp" class="form-text text-muted">
                <?php
                $tmpmsg=gks_lang('Η κράτηση στο Online θα μπορεί να γίνει από σήμερα έως <span id="gks_hotel_reservation_days_futureHelp1">[1]</span> ημέρες στο μέλλον. Δεν θα μπορεί να γίνει κράτηση μετά από <span id="gks_hotel_reservation_days_futureHelp2">[1]</span> ημέρες</small>');
                $tmpmsg=str_replace('[1]',$row['hotel_reservation_days_future'],$tmpmsg);
                echo $tmpmsg;
                ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="gks_hotel_reservation_min_days_online" class="col-md-6 col-form-label form-control-sm text-md-right" style="height:unset;"><?php echo gks_lang('Ελάχιστο πλήθος διανυκτερεύσεων ανά κράτηση στο Online');?>:</label>
            <div class="col-md-6">
              <input id="gks_hotel_reservation_min_days_online" type="number" class="form-control form-control-sm" value="<?php echo $row['hotel_reservation_min_days_online'];?>" style="max-width:200px" min="1" max="365" step="1">
            </div>
          </div>

          <div class="form-group row">
            <label for="gks_hotel_reservation_max_days_online" class="col-md-6 col-form-label form-control-sm text-md-right" style="height:unset;"><?php echo gks_lang('Μέγιστο πλήθος διανυκτερεύσεων ανά κράτηση στο Online');?>:</label>
            <div class="col-md-6">
              <input id="gks_hotel_reservation_max_days_online" type="number" class="form-control form-control-sm" value="<?php echo $row['hotel_reservation_max_days_online'];?>" style="max-width:200px" min="1" max="365" step="1">
            </div>
          </div>        
          
          <div class="form-group row">
            <label for="hotel_efd_product_id" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Είδος για Είσπραξη Φόρου Διαμονής');?>:</label>
            <div class="col-md-6">
              <input id="hotel_efd_product_id" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['product_descr_p']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
              data-id="<?php echo $row['hotel_efd_product_id'];?>">
              <a id="autocomplete_hotel_efd_product_id" tabindex="-1" href="admin-products-item.php?id=<?php echo $row['hotel_efd_product_id'];?>" style="<?php if ($row['hotel_efd_product_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή είδους');?>"></i></a>
            </div>
          </div>


        </div>
      </div>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Παιδιά');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('child');?>>       

          <div class="form-group row">
            <label for="gks_hotel_child_accept" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επιτρέπονται τα παιδιά');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="gks_hotel_child_accept" class="switchery1_this" <?php if ($row['hotel_child_accept']) echo ' checked ';?> >
            </div>
          </div> 

          <div class="form-group row row_child" style="<?php echo ($row['hotel_child_accept']==false ? 'display:none' : '');?>">
            <label for="gks_hotel_child_accept_above_age" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επιτρέπονται από αυτήν την ηλικία και μεγαλύτερα');?>:</label>
            <div class="col-md-6">
              <select id="gks_hotel_child_accept_above_age"  class="form-control form-control-sm" style="max-width:200px">
                <?php
                for ($i=0; $i <= 17; $i++) {
                  echo '<option '.($i==$row['hotel_child_accept_above_age'] ? 'selected': '').'>'.$i.'</option>';
                }
                ?>
              </select>
            </div>
          </div> 
          
          <div class="row row_child" style="<?php echo ($row['hotel_child_accept']==0 ? 'display:none' : '');?>">
            <div class="col-sm-12" style="text-align:center;background-color: rgba(0, 0, 0, 0.03);padding: 10px;border-radius: 10px 10px 0px 0px;border: 1px solid rgba(0, 0, 0, 0.3);">
            <?php echo gks_lang('Τιμές ανά παιδί και διανυκτέρευση ανάλογα με την ηλικία');?>
            </div> 
            
            <div class="col-sm-12" style="padding: 10px;border-radius: 0px 0px 10px 10px;border: 1px  solid rgba(0, 0, 0, 0.3);border-top-width: 0px;">
              <?php

              $i=0;
              foreach ($hotel_child_age_price as $value) {
                $i++;
                ?>
                
                <table data-aa="<?php echo $i;?>" cellspacing="0" cellpadding="2" style="width:100%;" border="0" class="gks_child_price_row_table">
                  <tr>
                    <td nowrap style="width:0%;text-align:right;"><?php echo gks_lang('Από');?>:</td>  
                    <td nowrap style="width:50%;text-align:left;">
                      <select data-aa="<?php echo $i;?>" class="gks_child_price_row_from form-control form-control-sm" style="max-width:100px">
                        <?php for ($j=0;$j<=17;$j++) {
                          echo '<option '.($j==$value['from'] ? 'selected' : '').'>'.$j.'</option>';  
                        }?>
                      </select>  
                    </td>
                    <td nowrap style="width:0%;text-align:right;"><?php echo gks_lang('Τιμή');?>:</td>  
                    <td nowrap style="width:50%;text-align:left;">
                      <input data-aa="<?php echo $i;?>" class="gks_child_price_row_price form-control form-control-sm" value="<?php echo number_format($value['price'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" type="number" style="max-width:100px" min="0" step="1">
                    </td>
                    <td  rowspan="2" style="text-align:center;vertical-align:middle;width:0%;">
                      <i class="fas fa-trash-alt   gks_child_price_row_delete" data-aa="<?php echo $i;?>" style=""></i>
                                          
                    </td>  
                  </tr>
                  <tr>
                    <td nowrap style="text-align:right;"><?php echo gks_lang('Έως');?>:</td>  
                    <td nowrap style="text-align:left;">
                      <select data-aa="<?php echo $i;?>" class="gks_child_price_row_to form-control form-control-sm" style="max-width:100px">
                        <?php for ($j=0;$j<=17;$j++) {
                          echo '<option '.($j==$value['to'] ? 'selected' : '').'>'.$j.'</option>';  
                        }?>
                      </select>                  
                    </td>  
                    <td nowrap style="text-align:right;"><?php echo gks_lang('Τύπος');?>:</td>
                    <td nowrap style="text-align:left;">  
                      <select data-aa="<?php echo $i;?>"  class="gks_child_price_row_type form-control form-control-sm" style="max-width:100px">
                        <option value="night" <?php echo ('night'==$value['type'] ? 'selected' : '');?>><?php echo gks_lang('Βράδυ');?></option>
                        <option value="stay" <?php echo ('stay'==$value['type']  ? 'selected' : '');?>><?php echo gks_lang('Κράτηση');?></option>
                      </select>               
                    </td>  
                  </tr>
                </table>
                <div data-aa="<?php echo $i;?>" class="gks_child_price_row_div" style="border-bottom: 1px solid lightgray;margin:20px"></div>  
              <?php
              } 
              
              ?>
              <div id="gks_child_price_row_add_div" style="text-align: center;">
                <i class="fas fa-plus-circle gks_child_price_row_add" style="font-size: 200%;"></i>  
              </div>
              <div id="gks_child_price_errors" style="text-align: left;padding-top: 20px;display:none;">
                
                  
                
                              
              </div>
            
            
            </div> 
          </div>          
        </div>
        
        
        
        
      </div>
       

       
      <div class="card gks_card_expand" id="gks_hotel_child_kounies_div" style="<?php if ($row['hotel_child_accept']==0 or $row['hotel_child_accept_above_age']>=7) echo 'display:none';?>">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βρεφικό κρεβάτι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('babybed');?>>       

          <div class="form-group row">
            <label for="gks_hotel_child_kounies_enable" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Υπάρχει η δυνατότητα');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="gks_hotel_child_kounies_enable" class="switchery1_this" <?php if ($hotel_child_kounies['enable']) echo ' checked ';?> >
            </div>
          </div>
                  
          <table cellspacing="0" cellpadding="2" border="0" id="gks_hotel_child_kounies_table" style="width:100%;<?php if ($hotel_child_kounies['enable'] == false) echo 'display:none;';?>">
            <tr>
              <td nowrap style="width:0%;text-align:right;"><?php echo gks_lang('Από');?>:</td>  
              <td nowrap style="width:50%;text-align:left;">
                <select id="gks_hotel_child_kounies_from" class="form-control form-control-sm" style="max-width:100px">
                  <?php for ($j=0;$j<=6;$j++) {
                    echo '<option '.($j==$hotel_child_kounies['from'] ? 'selected' : '').'>'.$j.'</option>';  
                  }?>
                </select>  
              </td>
              <td nowrap style="width:0%;text-align:right;"><?php echo gks_lang('Τιμή');?>:</td>  
              <td nowrap style="width:50%;text-align:left;">
                <input id="gks_hotel_child_kounies_price" class="form-control form-control-sm" value="<?php echo number_format($hotel_child_kounies['price'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" type="number" style="max-width:100px" min="0">
              </td>
              <td  rowspan="2" style="text-align:center;vertical-align:middle;width:0%;">
                
                                    
              </td>  
            </tr>
            <tr>
              <td nowrap style="text-align:right;"><?php echo gks_lang('Έως');?>:</td>  
              <td nowrap style="text-align:left;">
                <select id="gks_hotel_child_kounies_to" class="form-control form-control-sm" style="max-width:100px">
                  <?php for ($j=0;$j<=6;$j++) {
                    echo '<option '.($j==$hotel_child_kounies['to'] ? 'selected' : '').'>'.$j.'</option>';  
                  }?>
                </select>                  
              </td>  
              <td nowrap style="text-align:right;"><?php echo gks_lang('Τύπος');?>:</td>
              <td nowrap style="text-align:left;">  
                <select id="gks_hotel_child_kounies_type" class="form-control form-control-sm" style="max-width:100px">
                  <option value="night" <?php echo ('night'==$hotel_child_kounies['type'] ? 'selected' : '');?>><?php echo gks_lang('Βράδυ');?></option>
                  <option value="stay" <?php echo ('stay'==$hotel_child_kounies['type']  ? 'selected' : '');?>><?php echo gks_lang('Κράτηση');?></option>
                </select>               
              </td>  
            </tr>
          </table>      
      
        </div>
      </div>

      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Επιπλέον κρεβάτια');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('extrab');?>>       

          <div class="form-group row">
            <label for="gks_hotel_extra_beds_enabled" class="col-sm-6 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Υπάρχει η δυνατότητα');?>:</label>
            <div class="col-sm-6">
              <input type="checkbox" id="gks_hotel_extra_beds_enabled" class="switchery1_this" <?php if ($hotel_extra_beds['enabled']) echo ' checked ';?> >
            </div>
          </div> 

          
          <div class="row row_beds" style="<?php echo ($hotel_extra_beds['enabled']==false ? 'display:none' : '');?>">
            <div class="col-sm-12" style="text-align:center;background-color: rgba(0, 0, 0, 0.03);padding: 10px;border-radius: 10px 10px 0px 0px;border: 1px solid rgba(0, 0, 0, 0.3);">
            <?php echo gks_lang('Τιμές ανά κρεβάτι και διανυκτέρευση ανάλογα με την ηλικία');?>
            </div> 
            
            <div class="col-sm-12" style="padding: 10px;border-radius: 0px 0px 10px 10px;border: 1px  solid rgba(0, 0, 0, 0.3);border-top-width: 0px;">
              <?php


              $i=0;
              foreach ($hotel_extra_beds['beds'] as $value) {
                $i++;
                
                ?>
                 
                <table data-aa="<?php echo $i;?>" cellspacing="0" cellpadding="2" style="width:100%;" border="0" class="gks_hotel_extra_beds_row_table">
                  <tr>
                    <td nowrap style="width:0%;text-align:right;"><?php echo gks_lang('Από');?>:</td>  
                    <td nowrap style="width:50%;text-align:left;">
                      <select data-aa="<?php echo $i;?>" class="gks_hotel_extra_beds_row_from form-control form-control-sm" style="max-width:100px">
                        <?php 
                        for ($j=0;$j<=17;$j++) {
                          echo '<option value="'.$j.'" '.($j==$value['from'] ? 'selected' : '').'>'.$j.'</option>';  
                        }
                        echo '<option value="18" '.(18==$value['from'] ? 'selected' : '').'>'.gks_lang('Ενήλικες').'</option>';  
                        ?>
                      </select>  
                    </td>
                    <td nowrap style="width:0%;text-align:right;"><?php echo gks_lang('Τιμή');?>:</td>  
                    <td nowrap style="width:50%;text-align:left;">
                      <input data-aa="<?php echo $i;?>" class="gks_hotel_extra_beds_row_price form-control form-control-sm" value="<?php echo number_format($value['price'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" type="number" style="max-width:100px" min="0" step="1">
                    </td>
                    <td  rowspan="2" style="text-align:center;vertical-align:middle;width:0%;">
                      <i class="fas fa-trash-alt   gks_hotel_extra_beds_row_delete" data-aa="<?php echo $i;?>" style=""></i>
                                          
                    </td>  
                  </tr>
                  <tr>
                    <td nowrap style="text-align:right;">'.gks_lang('Έως');?>:</td>  
                    <td nowrap style="text-align:left;">
                      <select data-aa="<?php echo $i;?>" class="gks_hotel_extra_beds_row_to form-control form-control-sm" style="max-width:100px">
                        <?php for ($j=0;$j<=17;$j++) {
                          echo '<option value="'.$j.'" '.($j==$value['to'] ? 'selected' : '').'>'.$j.'</option>';  
                        }
                        echo '<option value="18" '.(18==$value['to'] ? 'selected' : '').'>'.gks_lang('Ενήλικες').'</option>'; 
                        ?>
                      </select>                  
                    </td>  
                    <td nowrap style="text-align:right;"><?php echo gks_lang('Τύπος');?>:</td>
                    <td nowrap style="text-align:left;">  
                      <select data-aa="<?php echo $i;?>"  class="gks_hotel_extra_beds_row_type form-control form-control-sm" style="max-width:100px">
                        <option value="night" <?php echo ('night'==$value['type'] ? 'selected' : '');?>><?php echo gks_lang('Βράδυ');?></option>
                        <option value="stay" <?php echo ('stay'==$value['type']  ? 'selected' : '');?>><?php echo gks_lang('Κράτηση');?></option>
                      </select>               
                    </td>  
                  </tr>
                </table>
                <div data-aa="<?php echo $i;?>" class="gks_hotel_extra_beds_row_div" style="border-bottom: 1px solid lightgray;margin:20px"></div>  
              <?php
              } 
              
              ?>
              <div id="gks_hotel_extra_beds_row_add_div" style="text-align: center;">
                <i class="fas fa-plus-circle gks_hotel_extra_beds_row_add" style="font-size: 200%;"></i>  
              </div>
              <div id="gks_hotel_extra_beds_errors" style="text-align: left;padding-top: 20px;display:none;">
                
                  
                
                              
              </div>
            
            
            </div> 
          </div>          
        </div>
        
        
      </div>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κοινωνικά Δίκτυα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('socialml');?>> 
      		<?php echo gks_sociallinks_item('gks_hotel',$id);?>
      	</div>        
      </div>        
        

             

    </div>
  </div>
</div>

<style>
  
.gks_child_price_row_delete {
    color: #dc3545;
    cursor: pointer;
    vertical-align: middle;
}
.gks_child_price_row_add {
  color: #35dc35;
  cursor:pointer;
  vertical-align: middle;
}
.gks_child_price_row {
  border-bottom:1px solid gray;
}

.gks_hotel_extra_beds_row_delete {
    color: #dc3545;
    cursor: pointer;
    vertical-align: middle;
}
.gks_hotel_extra_beds_row_add {
  color: #35dc35;
  cursor:pointer;
  vertical-align: middle;
}
.gks_hotel_extra_beds_row {
  border-bottom:1px solid gray;
}

  
</style>
          
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_hotel'];?>" data-model="gks_hotel" data-backurl="admin-hotel.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>
      
<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">
      <?php 
      echo getObjectRels('gks_hotel',$id);
      echo getActivityObjectTable('gks_hotel',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_hotel','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
    </div>
    <div class="col-xl-6">
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body"  <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_hotel']>0) echo $row['id_hotel'];?></span></div>
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
  
  
var dialog_exit_date;


var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_hotel';
var from_php_activity_model='gks_hotel';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel','delete',$id);?>;



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
    
  $('#date_hire').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#exit_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));


  $('#hotel_color').spectrum({
    type: "component",
    locale:'el',
    togglePaletteOnly: true,
    hideAfterPaletteSelect: true,
    showInput: true,
    showInitial: true,
    allowEmpty:true,
    //preferredFormat:'hex',
    chooseText: 'OK',
    cancelText: gks_lang('Άκυρο'),
    togglePaletteMoreText: gks_lang('Περισσότερα'), 
    togglePaletteLessText: gks_lang('Παλέτα'),
    clearText : gks_lang('Καθαρισμός'),
    noColorSelectedText: gks_lang('Διάφανο'),
  });
  dialog_exit_date = $( "#dialog_exit_date" ).dialog({
    autoOpen: false,
    width: 500,
    height: 500,
    modal: true,
    buttons: [
      {
        id: "dialog_exit_date_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('OK'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          $(this).dialog('close');
        
        
          var datasend='mymodel=gks_hotel_users&myid=' + dialog_exit_date.id_hotel_users + '&exit_date=' + $('#exit_date').val();
          
          
          $('body').addClass("myloading");  
          $.ajax({
        		url: '/my/admin-deleterow.php',
        		type: 'POST',
        		cache: false,
        		dataType: 'json',
        		data: datasend,
        		error : function(jqXHR ,textStatus,  errorThrown) {
        		  $("body").removeClass("myloading");
        			myalert('error:' + jqXHR.responseText);
        		},				
        		success: function(data) {
        			if (!data) {
        			  $("body").removeClass("myloading");
        				myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        			} else {
        				if (data.success == true) {
        				  need_save=false;
        				  window.location.reload();
        				  
        				} else {
        				  $("body").removeClass("myloading");
        					myalert('error:' + $.base64.decode(data.message));
        				}
        			}
        		}
        	});                
			
		    }	
      },
      {
        id: "dialog_exit_date_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }			
      },      
    ]
        

  });
  
  

  


  $('#hotel_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('hotel_nomos_id',v,0);
  });  
  
<?php if ($id==-1) {?>
  v=parseInt($('#hotel_country_id').val());
  if (isNaN()) v=0;
  if (v>0) nomos_fill('hotel_nomos_id',v,0);
<?php } ?> 


  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});

  function mysubmit() {
    if ($('.child_alert').length>0) {
      myalert('error:'+gks_lang('Διορθώστε τα λάθη στη ενότητα <b>Παιδιά</b>'));
      return;
    }
    if ($('.bed_alert').length>0) {
      myalert('error:'+gks_lang('Διορθώστε τα λάθη στη ενότητα <b>Επιπλέον κρεβάτια</b>'));
      return;
    }

    
    datasend='';
    datasend+='&company_id_sub_id=' + encodeURIComponent($.base64.encode($("#company_id_sub_id").val().trim()));
    datasend+='&hotel_title='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_title").val().trim()));
    datasend+='&hotel_phone='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_phone").val().trim()));
    datasend+='&hotel_email='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_email").val().trim()));
    datasend+='&hotel_website='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_website").val().trim()));
    datasend+='&hotel_odos='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_odos").val().trim()));
    datasend+='&hotel_arithmos='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_arithmos").val().trim()));
    datasend+='&hotel_orofos='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_orofos").val().trim()));
    datasend+='&hotel_perioxi='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_perioxi").val().trim()));
    datasend+='&hotel_poli='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_poli").val().trim()));
    datasend+='&hotel_tk='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_tk").val().trim()));
    datasend+='&hotel_country_id='  + encodeURIComponent(($("#mypostform #hotel_country_id").val().trim()));
    datasend+='&hotel_nomos_id='  + encodeURIComponent(($("#mypostform #hotel_nomos_id").val().trim()));
    datasend+='&hotel_map_latitude='  + encodeURIComponent(($("#mypostform #hotel_map_latitude").val().trim()));
    datasend+='&hotel_map_longitude='  + encodeURIComponent(($("#mypostform #hotel_map_longitude").val().trim()));
    datasend+='&hotel_disable=' + (($('#mypostform #hotel_disable').is(':checked')) ? '0':'1');
    datasend+='&hotel_color='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_color").val().trim()));
    datasend+='&default_eshop_hotel=' + (($('#default_eshop_hotel').is(':checked')) ? '1':'0');
    
    //settings
    hotel_booking_number_prefix
    datasend+='&hotel_booking_number_prefix='  + encodeURIComponent($.base64.encode($("#mypostform #hotel_booking_number_prefix").val().trim()));
    datasend+='&gks_hotel_default_availability='  + encodeURIComponent(($("#gks_hotel_default_availability").is(':checked') ? '1' : '0'));
    datasend+='&gks_hotel_date_open='  + encodeURIComponent($("#gks_hotel_date_open").val().trim());
    datasend+='&gks_hotel_date_close='  + encodeURIComponent($("#gks_hotel_date_close").val().trim());
    datasend+='&gks_hotel_default_checkin='  + encodeURIComponent($("#gks_hotel_default_checkin").val().trim());
    datasend+='&gks_hotel_default_checkout='  + encodeURIComponent($("#gks_hotel_default_checkout").val().trim());
    datasend+='&gks_hotel_default_price='  + encodeURIComponent($.base64.encode($("#gks_hotel_default_price").val().trim()));
    datasend+='&gks_hotel_reservation_can_select_room='  + encodeURIComponent(($("#gks_hotel_reservation_can_select_room").is(':checked') ? '1' : '0'));
    datasend+='&hotel_efd_product_id='  + encodeURIComponent($("#hotel_efd_product_id").attr('data-id').trim());
    
    
    datasend+='&gks_hotel_reservation_days_future='  + encodeURIComponent($("#gks_hotel_reservation_days_future").val().trim());
    datasend+='&gks_hotel_reservation_min_days_online='  + encodeURIComponent($("#gks_hotel_reservation_min_days_online").val().trim());
    datasend+='&gks_hotel_reservation_max_days_online='  + encodeURIComponent($("#gks_hotel_reservation_max_days_online").val().trim());
    datasend+='&gks_hotel_child_accept='  + encodeURIComponent(($("#gks_hotel_child_accept").is(':checked') ? '1' : '0'));
    datasend+='&gks_hotel_child_accept_above_age='  + encodeURIComponent($("#gks_hotel_child_accept_above_age").val().trim());
    datasend+='&gks_hotel_child_kounies_enable='  + encodeURIComponent(($("#gks_hotel_child_kounies_enable").is(':checked') ? '1' : '0'));
    datasend+='&gks_hotel_child_kounies_from='  + encodeURIComponent(($("#gks_hotel_child_kounies_from").val() == null ? '0': $("#gks_hotel_child_kounies_from").val().trim()));
    datasend+='&gks_hotel_child_kounies_to='  + encodeURIComponent(($("#gks_hotel_child_kounies_to").val() == null ? '' : $("#gks_hotel_child_kounies_to").val().trim()));
    datasend+='&gks_hotel_child_kounies_price='  + encodeURIComponent($("#gks_hotel_child_kounies_price").val().trim());
    datasend+='&gks_hotel_child_kounies_type='  + encodeURIComponent($("#gks_hotel_child_kounies_type").val().trim());
    
    datasend+='&hotel_template_eidos_descr='  + encodeURIComponent($.base64.encode($("#hotel_template_eidos_descr").val().trim()));
    datasend+='&hotel_template_efd_descr='  + encodeURIComponent($.base64.encode($("#hotel_template_efd_descr").val().trim()));
    datasend+='&hotel_template_woo_descr='  + encodeURIComponent($.base64.encode($("#hotel_template_woo_descr").val().trim()));
    datasend+='&hotel_website_key='  + encodeURIComponent($.base64.encode($("#hotel_website_key").val().trim()));
    datasend+='&hotel_use_checkout_system='  + encodeURIComponent($.base64.encode($("#hotel_use_checkout_system").val().trim()));
    
    datasend+='&hotel_id_booking='  + encodeURIComponent($.base64.encode($("#hotel_id_booking").val().trim()));
    datasend+='&hotel_id_airbnb='  + encodeURIComponent($.base64.encode($("#hotel_id_airbnb").val().trim()));

    
    gks_child_price_row_data_get();
    child_data_str = encodeURIComponent($.base64.encode(JSON.stringify(child_data)));
    datasend+='&child_data_str=' + child_data_str;
    
    gks_hotel_extra_beds_row_data_get();
    extra_beds_data_str = encodeURIComponent($.base64.encode(JSON.stringify(extra_beds)));
    datasend+='&extra_beds_data_str=' + extra_beds_data_str;
    datasend+='&extra_beds_enabled=' + encodeURIComponent(($("#gks_hotel_extra_beds_enabled").is(':checked') ? '1' : '0'));
    datasend+='&sociallinks_array_str=' + encodeURIComponent($.base64.encode(JSON.stringify(gks_sociallinks_input_collect())));

    //console.log(extra_beds);
    //return;
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
        
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-hotel-item-exec.php?id=' + <?php echo $id;?>,
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
              need_save=false;
  					  window.location.reload();
  					} else {
  					  need_save=false;
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


  $('#showmap').click(function(event) {  
    if (map_is_open==false) {
    
      $('#map').parent().css('height','500px').css('margin-top','10px');
      showmap_run();
      $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
      $('#map_pos, #geocode_pos').prop('disabled',false);
    } else {
      if ($('#showmap').html() ==gks_lang('Απόκρυψη χάρτη')) {
        $('#map_pos, #geocode_pos').prop('disabled',true);
        $('#showmap').html(gks_lang('Εμφάνιση χάρτη'));
        $('#map').parent().hide();
      } else {
        $('#map_pos, #geocode_pos').prop('disabled',false);
        $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
        $('#map').parent().show();
      }
    }
    gks_myscroll();
  });
  
  $('#map_pos').click(function(event){
    if (infoWindow_userpos==null) infoWindow_userpos = new google.maps.InfoWindow({map: map});
    
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
  
        
        infoWindow_userpos.setContent(gks_lang('Η τοποθεσία σας έχει εντοπιστεί'));
        map.setCenter(pos);
        
          
        marker.position=pos;
        place_map_latitude = marker.position.lat;
        place_map_longitude = marker.position.lng;
        infoWindow_userpos.open(map, marker);
        map.setZoom(17);
      
        
          
        $('#hotel_map_latitude').val(place_map_latitude);
        $('#hotel_map_longitude').val(place_map_longitude);
        need_save=true;
        
      }, function() {
        handleLocationError(true, infoWindow_userpos, map.getCenter());
      });
    } else {
      // Browser doesn't support Geolocation
      handleLocationError(false, infoWindow_userpos, map.getCenter());
    }
        
  });  
  
  $('#geocode_pos').tooltipster();
  $('#geocode_pos').click(function() {
    
    datasend='';
    datasend+='&odos='  + encodeURIComponent($.base64.encode($("#hotel_odos").val().trim()));
    datasend+='&arithmos='  + encodeURIComponent($.base64.encode($("#hotel_arithmos").val().trim()));
    datasend+='&orofos='  + encodeURIComponent($.base64.encode($("#hotel_orofos").val().trim()));
    datasend+='&perioxi='  + encodeURIComponent($.base64.encode($("#hotel_perioxi").val().trim()));
    datasend+='&poli='  + encodeURIComponent($.base64.encode($("#hotel_poli").val().trim()));
    datasend+='&tk='  + encodeURIComponent($.base64.encode($("#hotel_tk").val().trim()));
    datasend+='&country_id='  + encodeURIComponent($("#hotel_country_id").val().trim());
    datasend+='&nomos_id='  + encodeURIComponent($("#hotel_nomos_id").val().trim());
    
    $('#geocode_pos').prop('disabled',true);
    $('#geocode_pos_icon').html('<i class="fas fa-hourglass"></i>');
    //console.log(datasend);
    $.ajax({
			url: '/my/admin-get-geocode_pos.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#geocode_pos').prop('disabled',false);
			  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': ' + jqXHR.responseText).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
			},				
			success: function(data) {
			  $('#geocode_pos').prop('disabled',false);
				if (!data) {
				  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  $('#hotel_map_latitude' ).val(data.pos.lat);
					  $('#hotel_map_longitude').val(data.pos.lng);

            var pos = {lat: data.pos.lat,lng: data.pos.lng};      
            marker.position=pos;
            map.setOptions({center: pos});
            map.setOptions({zoom: 17});
            					  
					  $('#geocode_pos_icon').html('<i class="fas fa-check-circle"></i>').parent().tooltipster('destroy').attr('title','GEO:' + data.pos.lat + ',' + data.pos.lng).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					} else {
					  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message)).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					}
				}
			}
			
		});
  }); 


  
  $('#default_eshop_hotel').change(function() {
    if ($(this).is(':checked')) {
      $('#text_default_eshop_hotel_yes').show();
      $('#text_default_eshop_hotel_no').hide();
    } else {
      $('#text_default_eshop_hotel_yes').hide();
      $('#text_default_eshop_hotel_no').show();
    }
  });


  //settings
  
  $('#gks_hotel_date_open').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));   
  $('#gks_hotel_date_close').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('#gks_hotel_reservation_days_future').change(function() {
    val=parseInt($(this).val());
    if (isNaN(val)) val=0;
    
    $('#gks_hotel_reservation_days_futureHelp1').html(val);
    $('#gks_hotel_reservation_days_futureHelp2').html(val);
    
  });
  
  function gks_child_price_row_delete_click() {
    aa=$(this).attr('data-aa');
    //console.log(aa);
    $('.gks_child_price_row_table[data-aa=' + aa + ']').remove();
    $('.gks_child_price_row_div[data-aa=' + aa + ']').remove();
    gks_child_price_row_data_get();
  }
  
  $('.gks_child_price_row_delete').click(gks_child_price_row_delete_click);
  
  
  function gks_child_price_row_add_click() {
    
    aa=$('.gks_child_price_row_div:last');
    if (aa.length==0) aa=1;
    else {
      aa=parseInt(aa.attr('data-aa'));
      if (isNaN(aa)) aa=0;
      aa++;
    }
    
    //console.log(aa);
    var ageval = parseInt($('#gks_hotel_child_accept_above_age').val());
    myhtml='';
    myhtml+='<table data-aa="' + aa + '" cellspacing="0" cellpadding="2" style="width:100%;" border="0" class="gks_child_price_row_table">'+
            '<tr>' +
            '  <td nowrap style="width:0%;text-align:right;">'+gks_lang('Από')+':</td>' +  
            '  <td nowrap style="width:50%;text-align:left;">' +
            '    <select data-aa="' + aa + '" class="gks_child_price_row_from form-control form-control-sm" style="max-width:100px">';
                  for (j=0;j<=17;j++) {
                    myhtml+= '<option' + (j>=ageval ? '' : ' style="display: none;"') + '>' + j + '</option>';  
                  }
    myhtml+='   </select>' +   
            '  </td>' +
            '  <td nowrap style="width:0%;text-align:right;">'+gks_lang('Τιμή')+':</td>' +
            '  <td nowrap style="width:50%;text-align:left;">' +
            '    <input data-aa="' + aa + '" class="gks_child_price_row_price form-control form-control-sm" value="" type="number" style="max-width:100px" min="0" step="1">' +
            '  </td>' +
            '  <td  rowspan="2" style="text-align:center;vertical-align:middle;width:0%;">' +
            '    <i class="fas fa-trash-alt   gks_child_price_row_delete" data-aa="' + aa + '" style=""></i>' +
            '  </td>' +  
            '</tr>' +
            '<tr>' +
            '  <td nowrap style="text-align:right;">'+gks_lang('Έως')+':</td>' +  
            '  <td nowrap style="text-align:left;">' +
            '    <select data-aa="' + aa + '" class="gks_child_price_row_to form-control form-control-sm" style="max-width:100px">';
                  for (j=0;j<=17;j++) {
                    myhtml+= '<option' + (j>=ageval ? '' : ' style="display: none;"') + '>' + j + '</option>';  
                  }
    myhtml+='    </select>' +
            '  </td>' +
            '  <td nowrap style="text-align:right;">'+gks_lang('Τύπος')+':</td>' +
            '  <td nowrap style="text-align:left;">' +
            '    <select data-aa="' + aa + '"  class="gks_child_price_row_type form-control form-control-sm" style="max-width:100px">' +
            '      <option value="night">'+gks_lang('Βράδυ')+'</option>' +
            '      <option value="stay">'+gks_lang('Κράτηση')+'</option>' +
            '    </select>' +               
            '  </td>' +  
            '</tr>' +
            '</table>' +
            '<div data-aa="' + aa + '" class="gks_child_price_row_div" style="border-bottom: 1px solid lightgray;margin:20px"></div>'; 
    
    //if ($('.gks_child_price_row_div').length==0) {
    $('#gks_child_price_row_add_div').before(myhtml);
    if (ageval>0) {
      $('.gks_child_price_row_from:last').val(ageval);  
      $('.gks_child_price_row_to:last').val(ageval);  
    }    
    $('.gks_child_price_row_delete:last').click(gks_child_price_row_delete_click);
    $('.gks_child_price_row_from:last').change(gks_child_price_row_data_get);
    $('.gks_child_price_row_to:last').change(gks_child_price_row_data_get);

    gks_child_price_row_data_get();  
  }
  $('.gks_child_price_row_add').click(gks_child_price_row_add_click);
  
  var child_data=[];
  function gks_child_price_row_data_get() {
    child_data=[];
    if ($("#gks_hotel_child_accept").is(':checked') == false) {
      $('#gks_child_price_errors').html('').hide();
      return;
    }
    
    $('.gks_child_price_row_from').each(function() {
      
      aa=parseInt($(this).attr('data-aa'));
      if (isNaN(aa)) aa=0;
      if (aa>0) {
        item={};
        item.aa=aa;
        item.from=parseInt($('.gks_child_price_row_from[data-aa=' + aa + ']').val());
        item.to=parseInt($('.gks_child_price_row_to[data-aa=' + aa + ']').val());
        item.price=$('.gks_child_price_row_price[data-aa=' + aa + ']').val();
        item.type=$('.gks_child_price_row_type[data-aa=' + aa + ']').val();
        child_data.push(item);
        
        
      }
    });
    //console.log(child_data);
    
    $('.gks_child_price_row_table').each(function() {
      $(this).css('background-color','unset');
    });
    
    overlaperrors=false;
    max_age=-1;
    anf=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
    for (i=0; i < child_data.length; i++) {
      if (child_data[i].to > max_age) max_age=child_data[i].to;
      for(k=child_data[i].from; k<=child_data[i].to;k++) anf[k]=1;
      if (child_data[i].to<child_data[i].from) {
        $('.gks_child_price_row_table[data-aa=' + child_data[i].aa + ']').css('background-color','rgba(255,0,0,0.1)');
        overlaperrors=true;
      }
      for (j=0; j < child_data.length; j++) {
        if (i!=j) {
          if ((child_data[i].from >= child_data[j].from && child_data[i].from <= child_data[j].to) ||
              (child_data[i].to >= child_data[j].from && child_data[i].to <= child_data[j].from) ||
              (child_data[i].from <= child_data[j].from && child_data[i].to >= child_data[j].to)) { 
            $('.gks_child_price_row_table[data-aa=' + child_data[i].aa + ']').css('background-color','rgba(255,0,0,0.1)');
            overlaperrors=true;
          }
        }
      }
    }
    //console.log(ageval);
    myagesnotfound=[];
    var ageval = parseInt($('#gks_hotel_child_accept_above_age').val());
    for (k=ageval; k<max_age; k++) {
      if (anf[k]==0) myagesnotfound.push(k);
    }
    
    //console.log(anf);
    
    
    myoutrep='';
    if (max_age >=0 && max_age < 17) {
      max_age++;
      myoutrep='<div class="alert alert-primary" role="alert">'+gks_lang('Τα παιδιά')+' ' +  max_age + ' ετών ή μεγαλύτερα θα υπολογίζονται ως ενήλικες</div>';
    }
    if (overlaperrors) {
      myoutrep+='<div class="alert alert-danger child_alert" role="alert">'+gks_lang('Υπάρχουν αλληλοεπικαλύψεις ηλικιών')+'</div>';
    }
    
    if (myagesnotfound.length>0) {
      myoutrep+='<div class="alert alert-danger child_alert" role="alert">'+gks_lang('Δεν βρέθηκαν οι ηλικίες')+': ' + myagesnotfound.join(',') + '</div>';
    }
    
    if (myoutrep=='') $('#gks_child_price_errors').html('').hide();
    else $('#gks_child_price_errors').html(myoutrep).show();
    
    
    
  }
  
  $('#gks_hotel_child_accept').change(function() {
    var valaccept=false;
    if ($("#gks_hotel_child_accept").is(':checked')) valaccept=true;
    $('.row_child').each(function() {
      if (valaccept) $(this).show(); else $(this).hide();
    });
    gks_child_price_row_data_get();
    
    var ageval = parseInt($('#gks_hotel_child_accept_above_age').val());
    if (valaccept && ageval<=6) $('#gks_hotel_child_kounies_div').show(); else $('#gks_hotel_child_kounies_div').hide();
  });
  
  function gks_hotel_child_accept_above_age_change() {
    var ageval = parseInt($('#gks_hotel_child_accept_above_age').val());
    //console.log(ageval);
    
    $('.gks_child_price_row_from').each(function() {
      tmpval= parseInt($(this).val());
      if (tmpval<ageval) $(this).val(ageval);
      $(this).find('option').each(function() {
        tmpval= parseInt($(this).val());
        if (tmpval>=ageval) $(this).show(); else $(this).hide();
      });
    });
    $('.gks_child_price_row_to').each(function() {
      tmpval= parseInt($(this).val());
      if (tmpval<ageval) $(this).val(ageval);
      $(this).find('option').each(function() {
        tmpval= parseInt($(this).val());
        if (tmpval>=ageval) $(this).show(); else $(this).hide();
      });
    });
    

    tmpval= parseInt($('#gks_hotel_child_kounies_from').val());
    if (tmpval<ageval && ageval<=6) $('#gks_hotel_child_kounies_from').val(ageval);
    else if (ageval>6) $('#gks_hotel_child_kounies_from').val('');
    $('#gks_hotel_child_kounies_from').find('option').each(function() {
      tmpval= parseInt($(this).val());
      if (tmpval>=ageval) $(this).show(); else $(this).hide();
    });

    var valaccept=false;
    if ($("#gks_hotel_child_accept").is(':checked')) valaccept=true;

    if (valaccept && ageval<=6) $('#gks_hotel_child_kounies_div').show();
    else $('#gks_hotel_child_kounies_div').hide();
    
    tmpval= parseInt($('#gks_hotel_child_kounies_to').val());
    if (tmpval<ageval && ageval<=6) $('#gks_hotel_child_kounies_to').val(ageval);
    else if (ageval>6) $('#gks_hotel_child_kounies_to').val('');
    $('#gks_hotel_child_kounies_to').find('option').each(function() {
      tmpval= parseInt($(this).val());
      if (tmpval>=ageval) $(this).show(); else $(this).hide();
    });
    
    if (ageval>6) {
      var valchild_kounies=false;
      if ($("#gks_hotel_child_kounies_enable").is(':checked')) {
        $('#gks_hotel_child_kounies_enable').click();
        //$('#gks_hotel_child_kounies_table').hide();
      }
    }
    
    gks_child_price_row_data_get();
  } 
  $('#gks_hotel_child_accept_above_age').change(gks_hotel_child_accept_above_age_change);
  gks_hotel_child_accept_above_age_change();
  
  $('.gks_child_price_row_from').change(gks_child_price_row_data_get);
  $('.gks_child_price_row_to').change(gks_child_price_row_data_get);
  
  $('#gks_hotel_child_kounies_enable').change(function() {
    var valchild_kounies=false;
    if ($("#gks_hotel_child_kounies_enable").is(':checked')) valchild_kounies=true;
    if (valchild_kounies) $('#gks_hotel_child_kounies_table').show(); else $('#gks_hotel_child_kounies_table').hide();
  });
  
  $('#gks_hotel_child_kounies_from').change(function() {
    var tmpfrom= parseInt($('#gks_hotel_child_kounies_from').val());
    tmpval= parseInt($('#gks_hotel_child_kounies_to').val());
    if (tmpval<tmpfrom) $('#gks_hotel_child_kounies_to').val(tmpfrom);
    $('#gks_hotel_child_kounies_to').find('option').each(function() {
      tmpval= parseInt($(this).val());
      if (tmpval>=tmpfrom) $(this).show(); else $(this).hide();
    });    
  });
  
  $('#gks_hotel_extra_beds_enabled').change(function() {
    var valbeds=false;
    if ($("#gks_hotel_extra_beds_enabled").is(':checked')) valbeds=true;
    $('.row_beds').each(function() {
      if (valbeds) $(this).show(); else $(this).hide();
    });
    gks_hotel_extra_beds_row_data_get();
  });

  function gks_hotel_extra_beds_row_delete_click() {
    aa=$(this).attr('data-aa');
    //console.log(aa);
    $('.gks_hotel_extra_beds_row_table[data-aa=' + aa + ']').remove();
    $('.gks_hotel_extra_beds_row_div[data-aa=' + aa + ']').remove();
    gks_hotel_extra_beds_row_data_get();
  }
  
  $('.gks_hotel_extra_beds_row_delete').click(gks_hotel_extra_beds_row_delete_click);


  function gks_hotel_extra_beds_row_add_click() {
    
    aa=$('.gks_hotel_extra_beds_row_div:last');
    if (aa.length==0) aa=1;
    else {
      aa=parseInt(aa.attr('data-aa'));
      if (isNaN(aa)) aa=0;
      aa++;
    }
    
    //console.log(aa);
    var ageval = parseInt($('#gks_hotel_child_accept_above_age').val());
    myhtml='';
    myhtml+='<table data-aa="' + aa + '" cellspacing="0" cellpadding="2" style="width:100%;" border="0" class="gks_hotel_extra_beds_row_table">'+
            '<tr>' +
            '  <td nowrap style="width:0%;text-align:right;">'+gks_lang('Από')+':</td>' +  
            '  <td nowrap style="width:50%;text-align:left;">' +
            '    <select data-aa="' + aa + '" class="gks_hotel_extra_beds_row_from form-control form-control-sm" style="max-width:100px">';
                  for (j=0;j<=17;j++) {
                    myhtml+= '<option value="' + j + '" ' + (j>=ageval ? '' : ' style="display: none;"') + '>' + j + '</option>';  
                  }
                  myhtml+= '<option value="18">'+gks_lang('Ενήλικες')+'</option>'; 
    myhtml+='   </select>' +   
            '  </td>' +
            '  <td nowrap style="width:0%;text-align:right;">'+gks_lang('Τιμή')+':</td>' +
            '  <td nowrap style="width:50%;text-align:left;">' +
            '    <input data-aa="' + aa + '" class="gks_hotel_extra_beds_row_price form-control form-control-sm" value="" type="number" style="max-width:100px" min="0" step="1">' +
            '  </td>' +
            '  <td  rowspan="2" style="text-align:center;vertical-align:middle;width:0%;">' +
            '    <i class="fas fa-trash-alt   gks_hotel_extra_beds_row_delete" data-aa="' + aa + '" style=""></i>' +
            '  </td>' +  
            '</tr>' +
            '<tr>' +
            '  <td nowrap style="text-align:right;">'+gks_lang('Έως')+':</td>' +  
            '  <td nowrap style="text-align:left;">' +
            '    <select data-aa="' + aa + '" class="gks_hotel_extra_beds_row_to form-control form-control-sm" style="max-width:100px">';
                  for (j=0;j<=17;j++) {
                    myhtml+= '<option value="' + j + '" ' + (j>=ageval ? '' : ' style="display: none;"') + '>' + j + '</option>';  
                  }
                  myhtml+= '<option value="18">'+gks_lang('Ενήλικες')+'</option>';
    myhtml+='    </select>' +
            '  </td>' +
            '  <td nowrap style="text-align:right;">'+gks_lang('Τύπος')+':</td>' +
            '  <td nowrap style="text-align:left;">' +
            '    <select data-aa="' + aa + '"  class="gks_hotel_extra_beds_row_type form-control form-control-sm" style="max-width:100px">' +
            '      <option value="night">'+gks_lang('Βράδυ')+'</option>' +
            '      <option value="stay">'+gks_lang('Κράτηση')+'</option>' +
            '    </select>' +               
            '  </td>' +  
            '</tr>' +
            '</table>' +
            '<div data-aa="' + aa + '" class="gks_hotel_extra_beds_row_div" style="border-bottom: 1px solid lightgray;margin:20px"></div>'; 
    
    //if ($('.gks_hotel_extra_beds_row_div').length==0) {
    $('#gks_hotel_extra_beds_row_add_div').before(myhtml);
    if (ageval>0) {
      $('.gks_hotel_extra_beds_row_from:last').val(ageval);  
      $('.gks_hotel_extra_beds_row_to:last').val(ageval);  
    }    
    $('.gks_hotel_extra_beds_row_delete:last').click(gks_hotel_extra_beds_row_delete_click);
    $('.gks_hotel_extra_beds_row_from:last').change(gks_hotel_extra_beds_row_data_get);
    $('.gks_hotel_extra_beds_row_to:last').change(gks_hotel_extra_beds_row_data_get);

    gks_hotel_extra_beds_row_data_get();  
  }
  $('.gks_hotel_extra_beds_row_add').click(gks_hotel_extra_beds_row_add_click);
  
  var extra_beds=[];   
  function gks_hotel_extra_beds_row_data_get() {
    extra_beds=[];
    if ($("#gks_hotel_child_accept").is(':checked') == false) {
      $('#gks_hotel_extra_beds_errors').html('').hide();
      return;
    }
    
    $('.gks_hotel_extra_beds_row_from').each(function() {
      
      aa=parseInt($(this).attr('data-aa'));
      if (isNaN(aa)) aa=0;
      if (aa>0) {
        item={};
        item.aa=aa;
        item.from=parseInt($('.gks_hotel_extra_beds_row_from[data-aa=' + aa + ']').val());
        item.to=parseInt($('.gks_hotel_extra_beds_row_to[data-aa=' + aa + ']').val());
        item.price=$('.gks_hotel_extra_beds_row_price[data-aa=' + aa + ']').val();
        item.type=$('.gks_hotel_extra_beds_row_type[data-aa=' + aa + ']').val();
        extra_beds.push(item);
        
        
      }
    });
    //console.log(extra_beds);
    
    $('.gks_hotel_extra_beds_row_table').each(function() {
      $(this).css('background-color','unset');
    });
    
    overlaperrors=false;
    max_age=-1;
    anf=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
    for (i=0; i < extra_beds.length; i++) {
      if (extra_beds[i].to > max_age) max_age=extra_beds[i].to;
      for(k=extra_beds[i].from; k<=extra_beds[i].to;k++) anf[k]=1;
      if (extra_beds[i].to<extra_beds[i].from) {
        $('.gks_hotel_extra_beds_row_table[data-aa=' + extra_beds[i].aa + ']').css('background-color','rgba(255,0,0,0.1)');
        overlaperrors=true;
      }
      for (j=0; j < extra_beds.length; j++) {
        if (i!=j) {
          if ((extra_beds[i].from >= extra_beds[j].from && extra_beds[i].from <= extra_beds[j].to) ||
              (extra_beds[i].to >= extra_beds[j].from && extra_beds[i].to <= extra_beds[j].from) ||
              (extra_beds[i].from <= extra_beds[j].from && extra_beds[i].to >= extra_beds[j].to)) { 
            $('.gks_hotel_extra_beds_row_table[data-aa=' + extra_beds[i].aa + ']').css('background-color','rgba(255,0,0,0.1)');
            overlaperrors=true;
          }
        }
      }
    }
    //console.log(ageval);
    myagesnotfound=[];
    var ageval = parseInt($('#gks_hotel_child_accept_above_age').val());
    for (k=ageval; k<max_age; k++) {
      if (anf[k]==0) myagesnotfound.push(k);
    }
    
    //console.log(anf);
    
    
    myoutrep='';
    if (overlaperrors) {
      myoutrep+='<div class="alert alert-danger bed_alert" role="alert">'+gks_lang('Υπάρχουν αλληλοεπικαλύψεις ηλικιών')+'</div>';
    }
    
    if (myagesnotfound.length>0) {
      myoutrep+='<div class="alert alert-danger bed_alert" role="alert">'+gks_lang('Δεν βρέθηκαν οι ηλικίες')+': ' + myagesnotfound.join(',') + '</div>';
    }
    
    if (myoutrep=='') $('#gks_hotel_extra_beds_errors').html('').hide();
    else $('#gks_hotel_extra_beds_errors').html(myoutrep).show();
    
    
    //console.log(anf);
    //console.log(extra_beds);
  }

  $('.gks_hotel_extra_beds_row_from').change(gks_hotel_extra_beds_row_data_get);
  $('.gks_hotel_extra_beds_row_to').change(gks_hotel_extra_beds_row_data_get);

  $('#hotel_efd_product_id').autocomplete({
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
      $("#hotel_efd_product_id").attr('data-id',ui.item.id);
      $('#autocomplete_hotel_efd_product_id').attr('href', 'admin-products-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_hotel_efd_product_id').show();
      //$('#bom_monada_id').val(ui.item.monada_id);
      need_save=true;
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#hotel_efd_product_id").val('').attr('data-id','0');
        $('#autocomplete_hotel_efd_product_id').hide(); 
      }
    },
  });      


  function hotel_template_eidos_descr_change() {gks_resize_textarea($(this));}
  $('#hotel_template_eidos_descr').on(mychange, hotel_template_eidos_descr_change);
  gks_resize_textarea($('#hotel_template_eidos_descr'));

  function hotel_template_efd_descr_change() {gks_resize_textarea($(this));}
  $('#hotel_template_efd_descr').on(mychange, hotel_template_efd_descr_change);
  gks_resize_textarea($('#hotel_template_efd_descr'));

  function hotel_template_woo_descr_change() {gks_resize_textarea($(this));}
  $('#hotel_template_woo_descr').on(mychange, hotel_template_woo_descr_change);
  gks_resize_textarea($('#hotel_template_woo_descr'));


  gks_address_autocomplete('hotel_odos','hotel_arithmos','hotel_orofos','hotel_perioxi','hotel_poli','hotel_tk','hotel_nomos_id','hotel_country_id','hotel_map_latitude','hotel_map_longitude',true);

  var elems_switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  elems_switchery1_this.forEach(function(html) {
    var switchery1_this = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });

  $('#hotel_map_latitude, #hotel_map_longitude').on(mychange,function() {
    lat=parseFloat($('#hotel_map_latitude').val());
    lng=parseFloat($('#hotel_map_longitude').val());
    gks_this_map_set_pos(lat,lng);
  });  

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



var map;
var marker;
var place_map_latitude = <?php echo  floatval($row['hotel_map_latitude']);?>;
var place_map_longitude = <?php echo  floatval($row['hotel_map_longitude']);?>;
var myLatLng;
var infoWindow_userpos=null;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: myLatLng,
    zoom: 17,
    mapId: "gks1234567890",
  });
  marker = new google.maps.marker.AdvancedMarkerElement({
    position: myLatLng,
    map: map,
    title:gks_lang('Τοποθεσία'),
    gmpDraggable: true,
  }); 
}

function handleEvent_Marker(event) {
    document.getElementById('hotel_map_latitude').value = event.latLng.lat();
    document.getElementById('hotel_map_longitude').value = event.latLng.lng();
}
 
 
var map_is_open=false; 
function showmap_run() {
  if (place_map_latitude == 0 && place_map_longitude == 0) {
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
          var pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };      
          place_map_latitude = position.coords.latitude;
          place_map_longitude = position.coords.longitude;
          myLatLng = {lat: place_map_latitude, lng: place_map_longitude};
          marker.position=pos;
          map.setOptions({center: pos});
          map.setOptions({zoom: 17});

          jQuery('#hotel_map_latitude').val(place_map_latitude);
          jQuery('#hotel_map_longitude').val(place_map_longitude);
          
          need_save=true;
          
          //console.log('2' + myLatLng);
      }, function() {
        
      });
    } 
  }      

  myLatLng = {lat: place_map_latitude, lng: place_map_longitude};

  initMap();
  marker.addListener('drag', handleEvent_Marker);
  marker.addListener('dragend', handleEvent_Marker);
  map_is_open=true;
}
window.gks_this_map_set_pos = function(lat,lng) {
  place_map_latitude=lat;
  place_map_longitude=lng;
  
  myLatLng = {lat: lat, lng: lng};
  if (typeof marker != 'undefined') marker.position=myLatLng;
  if (typeof marker != 'undefined') map.setOptions({center: myLatLng});
  //map.setOptions({zoom: 17});
}
 
</script>




<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

echo gks_from_googlemaps_scripts();

include_once('_my_footer_admin.php');


