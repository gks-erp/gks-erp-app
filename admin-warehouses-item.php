<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$nav_active_array=array('manage','manage_warehouse');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_warehouses',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_warehouse_ids=gks_permission_user_condition($my_wp_user_id,'gks_warehouses','01');



$gks_custom_prepare = gks_custom_table_item_prepare('gks_warehouses',['from'=>'item']);


if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['warehouse_can_pelatis_paralavei'] =0;
  $row['id_warehouse']=-1;
  $row['company_id']=0;
  $row['company_title']='';
  $row['company_sub_id']=-1;
  $row['company_sub_title']='';
  $row['warehouse_is_company_place']=1;
  $row['warehouse_name']='';
  $row['warehouse_topos_fortosis']='';
  $row['warehouse_phone']='';
  $row['warehouse_email']='';
  $row['warehouse_website']='';
  $row['warehouse_branch']='';
  $row['warehouse_odos']='';
  $row['warehouse_arithmos']='';
  $row['warehouse_orofos']='';
  $row['warehouse_perioxi']='';
  $row['warehouse_poli']='';
  $row['warehouse_tk']='';
  $row['warehouse_nomos_id']=0;
  $row['warehouse_country_id']=0;
  $row['warehouse_map_latitude']='';
  $row['warehouse_map_longitude']='';
  $row['warehouse_disable']=0;
  $row['warehouse_color']='';
  $row['warehouse_sortorder']=1000;
  
  
  $my_page_title=gks_lang('Νέα αποθήκη');

  $company_sub_id=0; if (isset($_GET['company_sub_id'])) $company_sub_id=intval($_GET['company_sub_id']);
  if ($company_sub_id>0) {
    $sql="SELECT company_sub_title, company_id, company_title,company_sub_country_id,company_sub_nomos_id,company_sub_color
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
      $row['warehouse_country_id']=$row_user['company_sub_country_id'];
      $row['warehouse_nomos_id']=$row_user['company_sub_nomos_id'];
      $row['warehouse_is_company_place']=1;
      $row['warehouse_name'] = trim_gks(trim_gks($row_user['company_title']) .' - '.trim_gks($row_user['company_sub_title']));
      $row['warehouse_color'] = $row_user['company_sub_color'];
      $my_page_title=gks_lang('Νέα αποθήκη της Εταιρείας').': '.$row['company_title'];
    }    
       
    
  } else {
    $company_id=0; if (isset($_GET['company_id'])) $company_id=intval($_GET['company_id']);
    if ($company_id>0) {
      $sql="SELECT company_title, company_country_id,company_nomos_id,company_color FROM gks_company WHERE id_company=".$company_id;
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows==1) {
        $row_user = $result->fetch_assoc();  
        $row['company_id'] =$company_id;
        $row['company_title']=$row_user['company_title'];
        $row['warehouse_country_id']=$row_user['company_country_id'];
        $row['warehouse_nomos_id']=$row_user['company_nomos_id'];
        $row['warehouse_is_company_place']=1;
        $row['warehouse_name'] = trim_gks($row_user['company_title']) .' - '.gks_lang('Κεντρικό');
        $row['warehouse_color'] = $row_user['company_color'];
        $my_page_title=gks_lang('Νέα αποθήκη της Εταιρείας').': '.$row['company_title'];
      }
    }
  }
  

} else {
  $sql ="SELECT gks_warehouses.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  gks_company.company_title,gks_company_subs.company_sub_title
  FROM (((gks_warehouses
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_warehouses.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_warehouses.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_company ON gks_company.id_company = gks_warehouses.company_id)
  LEFT JOIN gks_company_subs ON gks_company_subs.id_company_sub = gks_warehouses.company_sub_id
  where gks_warehouses.is_virtual=0 and id_warehouse = ".$id;
  if (count($perm_id_warehouse_ids)>0) $sql.=" and gks_warehouses.id_warehouse in (".implode(',',$perm_id_warehouse_ids).")";
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
  $my_page_title=gks_lang('Αποθήκη').': '.$row['warehouse_name'];
}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$lang_data_obj=gks_lang_data_obj_prepare('gks_warehouses','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Αποθήκη');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $row['warehouse_name'];?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Αποθήκη');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποθήκη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('ware');?>> 
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
            <label for="warehouse_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τίτλος');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_name']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('warehouse_name'));
          ?>

          <div class="form-group row">
            <label for="warehouse_topos_fortosis" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ως Τόπος Φόρτωσης');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_topos_fortosis" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_topos_fortosis']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <small class="form-text text-muted"><?php echo gks_lang('Μπορεί να χρησιμοποιηθεί στην εκτύπωση ως Τόπος Φόρτωσης');?></small>
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('warehouse_topos_fortosis'));
          ?>
                    
          <div class="form-group row">
            <label for="warehouse_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="warehouse_disable" value="1" <?php if ($row['warehouse_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="warehouse_can_pelatis_paralavei" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μπορεί ο πελάτης να παραλάβει προϊόντα');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="warehouse_can_pelatis_paralavei" value="1" <?php if ($row['warehouse_can_pelatis_paralavei']!=0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="warehouse_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_color" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_color']);?>" style="max-width:200px;" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 

          
          <div class="form-group row">
            <label for="warehouse_is_company_place" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Διαφορετικός χώρος');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="warehouse_is_company_place" value="1" <?php if ($row['warehouse_is_company_place']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>  
          
                    
          <div id="warehouse_is_company_place_div" style="<?php if ($row['warehouse_is_company_place']!=0) echo 'display:none;';?>">




          <div class="form-group row">
            <label for="warehouse_phone" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερό Τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_phone" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_phone']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('warehouse_phone'));
          ?>
          <div class="form-group row">
            <label for="warehouse_email" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ηλ. διεύθυνση');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_email" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_email']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>           
          <div class="form-group row">
            <label for="warehouse_website" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ιστότοπος');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_website" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_website']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="warehouse_branch" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Εγκατάστασης');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_branch" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['warehouse_branch'];?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min=0>
            </div>
          </div>                    
          <div class="form-group row">
            <label for="warehouse_odos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_odos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_odos']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <small class="form-text text-muted auto_googlemaps" id="warehouse_odos_auto_googlemaps"></small>
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('warehouse_odos'));
          ?>
          <div class="form-group row">
            <label for="warehouse_arithmos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_arithmos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_arithmos']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('warehouse_arithmos'));
          ?>
          <div class="form-group row">
            <label for="warehouse_orofos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_orofos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_orofos']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('warehouse_orofos'));
          ?>          
          
          <div class="form-group row">
            <label for="warehouse_perioxi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_perioxi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_perioxi']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('warehouse_perioxi'));
          ?>
          <div class="form-group row">
            <label for="warehouse_poli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_poli" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_poli']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('warehouse_poli'));
          ?>
          <div class="form-group row">
            <label for="warehouse_tk" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('TK');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_tk" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_tk']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="warehouse_nomos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-md-8">
              <select id="warehouse_nomos_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomos['sql']['from2']."
                where country_id=".$row['warehouse_country_id']." ORDER BY nomos_descr";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_nomos'].'" ';
                  if ($row_select['id_nomos']==$row['warehouse_nomos_id']) echo ' selected ';
                  echo '>'.$row_select['nomos_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>    

          <div class="form-group row">
            <label for="warehouse_country_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-md-8">
              <select id="warehouse_country_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
                $sql="select id_country,country_ee,country_initials,".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
                FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
                ".$lang_prepare_gks_country['sql']['from2']."
                ORDER BY country_name";                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" ';
                  if ($row_select['id_country']==$row['warehouse_country_id']) echo ' selected ';
                  echo '>'.$row_select['country_name'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          

          <div class="form-group row">
            <label for="warehouse_map_latitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Πλάτος');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_map_latitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_map_latitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-90" max="90" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label for="warehouse_map_longitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Μήκος');?>:</label>
            <div class="col-md-8">
              <input id="warehouse_map_longitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_map_longitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-180" max="180" step="0.00001">
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
  
          

          
          </div>

          <div class="form-group row">
            <label for="warehouse_sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="warehouse_sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['warehouse_sortorder']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="0">
            </div>
          </div>
          
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κοινωνικά Δίκτυα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('socialml');?>> 
      		<?php echo gks_sociallinks_item('gks_warehouses',$id);?>
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_warehouse'];?>" data-model="gks_warehouses" data-backurl="admin-warehouses.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">
      <?php 
      echo getObjectRels('gks_warehouses',$id);
      echo getActivityObjectTable('gks_warehouses',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_warehouses','id'=>$id));
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_warehouse']>0) echo $row['id_warehouse'];?></span></div>
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



var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_warehouses';
var from_php_activity_model='gks_warehouses';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;  

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_warehouses','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_warehouses','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_warehouses','delete',$id);?>;





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
      event.preventDefault();
      event.stopPropagation();
      elem=$('#submit_button_ok_custom');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
    }  
  });

  
  $('#warehouse_color').spectrum({
    type: "component",
    locale:'el',
    togglePaletteOnly: true,
    hideAfterPaletteSelect: true,
    showInput: true,
    showInitial: true,
    allowEmpty:true,
    //preferredFormat:'hex',
    chooseText: gks_lang('OK'),
    cancelText: gks_lang('Άκυρο'),
    togglePaletteMoreText: gks_lang('Περισσότερα'),
    togglePaletteLessText: gks_lang('Παλέτα'),
    clearText : gks_lang('Καθαρισμός'),
    noColorSelectedText: gks_lang('Διάφανο'),
  });
   
  $('#warehouse_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('warehouse_nomos_id',v,0);
  });  
  
<?php if ($id==-1) {?>
  v=parseInt($('#warehouse_country_id').val());
  if (isNaN()) v=0;
  if (v>0) nomos_fill('warehouse_nomos_id',v,0);
<?php } ?> 

  $('#submit_button_ok_custom').click(function(event) {mysubmit(); return false;});
  function mysubmit() {
    
    datasend='';

    datasend+='&company_id='  + encodeURIComponent(($("#mypostform #company_id").val().trim()));
    datasend+='&company_sub_id='  + encodeURIComponent(($("#mypostform #company_sub_id").val().trim()));
    datasend+='&warehouse_is_company_place=' + (($('#warehouse_is_company_place').is(':checked')) ? '0':'1');
    datasend+='&warehouse_name='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_name").val().trim()));
    datasend+='&warehouse_topos_fortosis='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_topos_fortosis").val().trim()));
    datasend+='&warehouse_phone='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_phone").val().trim()));
    datasend+='&warehouse_email='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_email").val().trim()));
    datasend+='&warehouse_website='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_website").val().trim()));
    datasend+='&warehouse_branch='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_branch").val().trim()));
    datasend+='&warehouse_odos='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_odos").val().trim()));
    datasend+='&warehouse_arithmos='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_arithmos").val().trim()));
    datasend+='&warehouse_orofos='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_orofos").val().trim()));
    datasend+='&warehouse_perioxi='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_perioxi").val().trim()));
    datasend+='&warehouse_poli='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_poli").val().trim()));
    datasend+='&warehouse_tk='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_tk").val().trim()));
    datasend+='&warehouse_country_id='  + encodeURIComponent(($("#mypostform #warehouse_country_id").val().trim()));
    datasend+='&warehouse_nomos_id='  + encodeURIComponent(($("#mypostform #warehouse_nomos_id").val().trim()));
    datasend+='&warehouse_map_latitude='  + encodeURIComponent(($("#mypostform #warehouse_map_latitude").val().trim()));
    datasend+='&warehouse_map_longitude='  + encodeURIComponent(($("#mypostform #warehouse_map_longitude").val().trim()));
    datasend+='&warehouse_disable=' + (($('#warehouse_disable').is(':checked')) ? '0':'1');
    datasend+='&warehouse_can_pelatis_paralavei=' + (($('#warehouse_can_pelatis_paralavei').is(':checked')) ? '1':'0');
    datasend+='&warehouse_color='  + encodeURIComponent($.base64.encode($("#mypostform #warehouse_color").val().trim()));
    datasend+='&warehouse_sortorder='  + encodeURIComponent(($("#mypostform #warehouse_sortorder").val().trim()));
    
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    datasend+='&sociallinks_array_str=' + encodeURIComponent($.base64.encode(JSON.stringify(gks_sociallinks_input_collect())));
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-warehouses-item-exec.php?id=' + <?php echo $id;?>,
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
      
        
          
        $('#warehouse_map_latitude').val(place_map_latitude);
        $('#warehouse_map_longitude').val(place_map_longitude);
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
    datasend+='&odos='  + encodeURIComponent($.base64.encode($("#warehouse_odos").val().trim()));
    datasend+='&arithmos='  + encodeURIComponent($.base64.encode($("#warehouse_arithmos").val().trim()));
    datasend+='&orofos='  + encodeURIComponent($.base64.encode($("#warehouse_orofos").val().trim()));
    datasend+='&perioxi='  + encodeURIComponent($.base64.encode($("#warehouse_perioxi").val().trim()));
    datasend+='&poli='  + encodeURIComponent($.base64.encode($("#warehouse_poli").val().trim()));
    datasend+='&tk='  + encodeURIComponent($.base64.encode($("#warehouse_tk").val().trim()));
    datasend+='&country_id='  + encodeURIComponent($("#warehouse_country_id").val().trim());
    datasend+='&nomos_id='  + encodeURIComponent($("#warehouse_nomos_id").val().trim());
    
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
					  $('#warehouse_map_latitude' ).val(data.pos.lat);
					  $('#warehouse_map_longitude').val(data.pos.lng);

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
      if (user_change_warehouse_name==false) {
        valname=ui.item.value.trim() + ' - ' + $('#company_sub_title').val().trim();
        $('#warehouse_name').val(valname.trim());
      }
      if (user_change_warehouse_color==false) {
        $('#warehouse_color').val(ui.item.color);
      }
      
      //console.log(ui.item);     
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#company').val('');
          $('#company_id').val('');
          $('#company_sub_title').val('');
          $('#company_sub_id').val('');
        }
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
      if (user_change_warehouse_name==false) {
        valname=$('#company').val().trim() + ' - ' + ui.item.value.trim();
        $('#warehouse_name').val(valname.trim());
      }
      if (user_change_warehouse_color==false) {
        $('#warehouse_color').val(ui.item.color);
      }      
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#company_sub_title').val('');
          $('#company_sub_id').val('');
        }
    }
  });      

  $('#warehouse_is_company_place').change(function() {
    if ($('#warehouse_is_company_place').is(':checked')) {
      $('#warehouse_is_company_place_div').show();
    } else {
      $('#warehouse_is_company_place_div').hide();
    }
  });
 
<?php if ($id==-1) { ?>
  var user_change_warehouse_name=false;
  var user_change_warehouse_color=false;
  $('#warehouse_name').on('change keyup paste',function () {
    //console.log('warehouse_name change');  
    user_change_warehouse_name=true;
  });
  $('#warehouse_color').on('change keyup paste',function () {
    //console.log('warehouse_color change');  
    user_change_warehouse_color=true;
  });
  
  
<?php } else  { ?>
  var user_change_warehouse_name=true;
  var user_change_warehouse_color=true;
  
  
<?php } ?>

  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });

  gks_address_autocomplete('warehouse_odos','warehouse_arithmos','warehouse_orofos','warehouse_perioxi','warehouse_poli','warehouse_tk','warehouse_nomos_id','warehouse_country_id','warehouse_map_latitude','warehouse_map_longitude',true);

  $('#warehouse_map_latitude, #warehouse_map_longitude').on(mychange,function() {
    lat=parseFloat($('#warehouse_map_latitude').val());
    lng=parseFloat($('#warehouse_map_longitude').val());
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
var place_map_latitude = <?php echo floatval($row['warehouse_map_latitude']);?>;
var place_map_longitude = <?php echo floatval($row['warehouse_map_longitude']);?>;
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
    title: gks_lang('Τοποθεσία'),
    gmpDraggable: true,
  });
 
}

function handleEvent_Marker(event) {
    document.getElementById('warehouse_map_latitude').value = event.latLng.lat();
    document.getElementById('warehouse_map_longitude').value = event.latLng.lng();
    need_save=true;
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

          jQuery('#warehouse_map_latitude').val(place_map_latitude);
          jQuery('#warehouse_map_longitude').val(place_map_longitude);
          
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


