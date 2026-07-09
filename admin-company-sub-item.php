<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$nav_active_array=array('manage','manage_company_subs');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company_subs',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_warehouse_ids=gks_permission_user_condition($my_wp_user_id,'gks_warehouses','01');




$gks_custom_prepare = gks_custom_table_item_prepare('gks_company_subs',['from'=>'item']);



if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_company_sub']=-1;
  $row['company_id']=0;
  $row['company_title']='';
  $row['company_sub_title']='';
  $row['company_sub_tagline']='';
  $row['company_sub_eponimia']='';
  $row['company_sub_phone']='';
  $row['company_sub_email']='';
  $row['company_sub_url']='';
  $row['company_sub_odos']='';
  $row['company_sub_arithmos']='';
  $row['company_sub_orofos']='';
  $row['company_sub_perioxi']='';
  $row['company_sub_poli']='';
  $row['company_sub_tk']='';
  $row['company_sub_nomos_id']=0;
  $row['company_sub_country_id']=91;
  $row['company_sub_map_latitude']='';
  $row['company_sub_map_longitude']='';
  $row['company_sub_disable']=0;
  $row['company_sub_related_user_id']=0;
  $row['gks_nickname'] ='';  
  $row['company_sub_color']='';
  $row['aade_send_sub']='';
  $row['aade_branch_sub']=1;
  $row['aade_mydata_user_id_sub']='';
  $row['aade_mydata_subscription_key_sub']='';
  $row['aade_mydata_live_sub']=0;

  $row['company_sub_sortorder']=1000;
  
  $my_page_title=gks_lang('Νέο υποκατάστημα');

  $company_id=0; if (isset($_GET['company_id'])) $company_id=intval($_GET['company_id']);
  if ($company_id>0) {
    $sql="SELECT * FROM gks_company WHERE id_company=".$company_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==1) {
      $row_user = $result->fetch_assoc();  
      $row['company_title']=$row_user['company_title'];
      $my_page_title=gks_lang('Νέο υποκατάστημα της Εταιρείας').': '.$row['company_title'];
      
      $row['company_sub_title']=trim_gks(gks_lang('Υποκατάστημα της').' '.$row_user['company_title']);
      $row['company_sub_color']=$row_user['company_color'];
      $row['company_id'] =$company_id;
      $row['company_sub_country_id']=$row_user['company_country_id'];
      $row['company_sub_nomos_id']=$row_user['company_nomos_id'];
      $row['aade_send_sub']=$row_user['aade_send'];
      $row['aade_mydata_user_id_sub']=$row_user['aade_mydata_user_id'];
      $row['aade_mydata_subscription_key_sub']=$row_user['aade_mydata_subscription_key'];
      $row['aade_mydata_live_sub']=$row_user['aade_mydata_live'];
      if ($row_user['aade_branch']>=0) $row['aade_branch_sub']=$row_user['aade_branch']+1;
      
      $sql="SELECT max(aade_branch_sub) AS maxb FROM gks_company_subs WHERE aade_branch_sub>=0 and company_id=".$company_id." HAVING Max(aade_branch_sub) >=0";
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows==1) {
        $row_user = $result->fetch_assoc();  
        if (($row_user['maxb'] + 1) > $row['aade_branch_sub']) $row['aade_branch_sub']=$row_user['maxb'] + 1;
      }
    }    
  }
  

} else {
  $sql ="SELECT gks_company_subs.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname,gks_company.company_title
  
  FROM (((gks_company_subs
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_company_subs.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_company_subs.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_company_subs.company_sub_related_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_company ON gks_company.id_company = gks_company_subs.company_id
  where id_company_sub = ".$id;
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_company_subs.id_company_sub in (".implode(',',$perm_id_company_sub_ids).")";
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
  $my_page_title=gks_lang('Υποκατάστημα της Εταιρείας').': '.$row['company_title'].' '.$row['company_sub_title'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$lang_data_obj=gks_lang_data_obj_prepare('gks_company_subs','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);



include_once('_my_header_admin.php');
?>
<style>
.gks_cost_label {
  font-size: 0.8rem;
  padding: 5px 0px 5px 0px;
  border-radius: 10px;
  text-align: center;
  margin-top: 2px;
}  
.col-form-label {
  height:unset;    
}
.gks_mybasefpa_div {
  text-align: left; 
}
.gks_mybasefpa {
  width: unset;
  text-align: center; 
  display: inline-block;   
}
.gks_myfpa_div {
  text-align: center; 
}
.gks_myfpa {
  width: unset;
  text-align: center; 
  display: inline-block; 
}
.delete_company_user {
  cursor: pointer;
  color: #dc3545;
  font-size: 100%;
}  
</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Υποκατάστημα');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $row['company_title'].' \ '.$row['company_sub_title'];?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Υποκατάστημα');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υποκατάστημα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('ypok');?>> 
          <div class="form-group row">
            <label for="company" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <input id="company" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['company_title']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="company_id" type="hidden" value="<?php echo $row['company_id'];?>" class="myneedsave">
            </div>
          </div>

          <div class="form-group row">
            <label for="company_sub_title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Διακριτικός Τίτλος');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_title']);?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('company_sub_title'));
          ?>
          <div class="form-group row">
            <label for="company_sub_tagline" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Το μότο μου');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_tagline" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_tagline']);?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('company_sub_tagline'));
          ?>
          <div class="form-group row">
            <label for="company_sub_email" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ηλ. διεύθυνση');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_email" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_email']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="company_sub_url" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ιστότοπος');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_url']);?>">
            </div>
          </div> 
          
          
          
          <div class="form-group row">
            <label for="company_sub_phone" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερό Τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_phone" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_phone']);?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('company_sub_phone'));
          ?>
          <div class="form-group row">
            <label for="company_sub_odos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_odos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_odos']);?>">
              <small class="form-text text-muted auto_googlemaps" id="company_sub_odos_auto_googlemaps"></small>
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('company_sub_odos'));
          ?>
          <div class="form-group row">
            <label for="company_sub_arithmos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_arithmos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_arithmos']);?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('company_sub_arithmos'));
          ?>
          
          <div class="form-group row">
            <label for="company_sub_orofos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_orofos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_orofos']);?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('company_sub_orofos'));
          ?>          
          <div class="form-group row">
            <label for="company_sub_perioxi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_perioxi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_perioxi']);?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('company_sub_perioxi'));
          ?>
          <div class="form-group row">
            <label for="company_sub_poli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_poli" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_poli']);?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('company_sub_poli'));
          ?>
          <div class="form-group row">
            <label for="company_sub_tk" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('TK');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_tk" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_tk']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="company_sub_nomos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-md-8">
              <select id="company_sub_nomos_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomos['sql']['from2']."
                where country_id=".$row['company_sub_country_id']." ORDER BY nomos_descr";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_nomos'].'" ';
                  if ($row_select['id_nomos']==$row['company_sub_nomos_id']) echo ' selected ';
                  echo '>'.$row_select['nomos_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>    

          <div class="form-group row">
            <label for="company_sub_country_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-md-8">
              <select id="company_sub_country_id" class="form-control form-control-sm myneedsave">
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
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" ';
                  if ($row_select['id_country']==$row['company_sub_country_id']) echo ' selected ';
                  echo '>'.$row_select['country_name'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          

          <div class="form-group row">
            <label for="company_sub_map_latitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Πλάτος');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_map_latitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_map_latitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-90" max="90" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label for="company_sub_map_longitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Μήκος');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_map_longitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_map_longitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-180" max="180" step="0.00001">
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
            <label for="company_sub_related_user" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σχετική επαφή');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_related_user" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="company_sub_related_user_id" type="hidden" value="<?php echo $row['company_sub_related_user_id'];?>" class="myneedsave">
            </div>
  
          </div>          

          
          <div class="form-group row">
            <label for="company_sub_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_color" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_color']);?>" style="max-width:200px;">
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="company_sub_sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="company_sub_sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_sortorder']);?>">
            </div>
          </div>

          <div class="form-group row">
            <label for="company_sub_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="company_sub_disable" value="1" <?php if ($row['company_sub_disable']==0) echo ' checked '; ?> class="switchery1_this">
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
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('eshop');?>:</label>
            <div class="col-md-8" style="font-size: 0.875rem;padding-top:4px;">
              <?php
              $sql_eshop="select * from gks_eshops where company_id=".$row['company_id']." and company_sub_id=".$id;
              $result_eshop = $db_link->query($sql_eshop);        
              if (!$result_eshop) {debug_mail(false,'error sql',$sql_eshop);die('sql error');}
              if ($result_eshop->num_rows==0) {
                echo gks_lang('Δεν έχει ορισθεί eshop για αυτό το υποκατάστημα').'<br>'.
                     '<a href="admin-eshop-item.php?id=-1&company_sub_id='.$id.'">'.gks_lang('Δημιουργία νέου eshop').'</a>';
              } else {
                $row_eshop = $result_eshop->fetch_assoc();
                echo '<a href="admin-eshop-item.php?id='.$row_eshop['id_eshop'].'">'.$row_eshop['eshop_name'].'</a><br>'.
                     '<a href="'.$row_eshop['eshop_url'].'" target="_blank">'.$row_eshop['eshop_url'].'</a><br>'.
                     gks_lang('Αυτόματος συγχρονισμός').': <img src="img/'.($row_eshop['eshop_autosync']==0 ? '0' :'1').'.png" border="0" width="20"><br>'.
                     gks_lang('Ενεργό').': <img src="img/'.($row_eshop['eshop_disable']==0 ? '1' :'0').'.png" border="0" width="20">';
              }              
              ?>
              
            </div>
          </div>      
     
        </div>
      
      </div>      
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand" >
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Λογιστική');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('log');?>> 
          <div class="form-group row">
            <label for="company_sub_eponimia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επωνυμία');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_eponimia" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['company_sub_eponimia']);?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('company_sub_eponimia'));
          ?>
        

          
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('ΑΑΔΕ');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('aade');?>> 

          
          
          <div class="col-sm-12 text-center" style="background-color1: rgba(0, 0, 0, 0.03);border-radius: 10px 10px 0px 0px;border1: 1px solid #bbbbbb;">
            <a href="https://www.aade.gr/mydata" target="_blank"
              ><?php echo gks_lang('myData');?></a>
          </div>

          <div class="form-group row">
            <label for="aade_send_sub" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποστολή δεδομένων στην ΑΑΔΕ με myDATA');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="aade_send_sub" value="1" <?php if ($row['aade_send_sub']==1) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div> 

          <div class="form-group row" id="div_aade_branch_sub" style="<?php if ($row['aade_send_sub']!=1) echo 'display:none;';?>">
            <label for="aade_branch_sub" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Εγκατάστασης');?>:</label>
            <div class="col-md-8">
              <input id="aade_branch_sub" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['aade_branch_sub']);?>" min="0" step="1" style="max-width:100px;">
            </div>
          </div> 
          <div class="form-group row"  id="div_aade_aade_mydata_user_id_sub" style="<?php if ($row['aade_send_sub']!=1) echo 'display:none;';?>">
            <label for="aade_mydata_user_id_sub" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα Χρήστη');?>:</label>
            <div class="col-md-8">
              <input id="aade_mydata_user_id_sub" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['aade_mydata_user_id_sub']);?>">
            </div>
          </div> 
          <div class="form-group row"  id="div_aade_mydata_subscription_key_sub" style="<?php if ($row['aade_send_sub']!=1) echo 'display:none;';?>">
            <label for="aade_mydata_subscription_key_sub" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός API');?>:</label>
            <div class="col-md-8">
              <input id="aade_mydata_subscription_key_sub" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['aade_mydata_subscription_key_sub']);?>">
            </div>
          </div> 

          <div class="form-group row"  id="div_aade_mydata_live_sub"  style="<?php if ($row['aade_send_sub']!=1) echo 'display:none;';?>">
            <label for="aade_mydata_live_sub" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πραγματική αποστολή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="aade_mydata_live_sub" value="1" <?php if ($row['aade_mydata_live_sub']!=0) echo ' checked '; ?> class="switchery1_this">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Όταν <strong style="font-size:150%;">δεν είναι</strong> ενεργοποιημένη η επιλογή <strong>"Πραγματική αποστολή"</strong>, τότε το παραστατικό θα αποσταλεί σε ένα άλλο δοκιμαστικό σύστημα της ΑΑΔΕ απλά και μόνο για έλεγχο της σύνταξης του παραστατικού και το παραστατικό είναι σαν να μην το στείλατε ποτέ.');?>
                <br>
                <?php echo gks_lang('Όταν <strong style="font-size:150%;">είναι</strong> ενεργοποιημένη η επιλογή <strong>"Πραγματική αποστολή"</strong> τότε το παραστατικό θα αποσταλεί στην ΑΑΔΕ.');?>
              </small>              
            </div>
          </div>
        </div>
      </div>

<?php
      $paroxos_send=0;
      $aade_paroxos_id=0;
      $paroxos_mydata_live=0;
      $paroxos_branch=1;
      $pc_username='';
      $pc_password='';
      $pc_key='';
      $paroxos_need_username=0;
      $paroxos_need_password=0;
      $paroxos_need_key=0;
      
      $sql_paroxos="SELECT gks_company_paroxos.*, 
      gks_aade_paroxos.paroxos_need_username, 
      gks_aade_paroxos.paroxos_need_password, 
      gks_aade_paroxos.paroxos_need_key
      FROM gks_company_paroxos 
      LEFT JOIN gks_aade_paroxos ON gks_company_paroxos.aade_paroxos_id = gks_aade_paroxos.id_aade_paroxos
      where gks_company_paroxos.company_sub_id=".$id;
      $result_paroxos = $db_link->query($sql_paroxos); 
      if (!$result_paroxos) debug_mail(false,'error sql',$sql_paroxos);
      if (!$result_paroxos) die('sql error');

      if ($result_paroxos->num_rows>0) {
        $row_paroxos = $result_paroxos->fetch_assoc();
        $aade_paroxos_id=intval($row_paroxos['aade_paroxos_id']);
        $paroxos_send=intval($row_paroxos['paroxos_send']);
        $paroxos_mydata_live=intval($row_paroxos['paroxos_mydata_live']);
        $paroxos_branch=intval($row_paroxos['paroxos_branch']);
        $pc_username=trim_gks($row_paroxos['pc_username']);
        $pc_password=trim_gks($row_paroxos['pc_password']);
        $pc_key=trim_gks($row_paroxos['pc_key']);
        $paroxos_need_username=intval($row_paroxos['paroxos_need_username'])==1;
        $paroxos_need_password=intval($row_paroxos['paroxos_need_password'])==1;
        $paroxos_need_key=intval($row_paroxos['paroxos_need_key'])==1;
      }
?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πάροχος');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('paroxos');?>> 

          <div class="form-group row">
            <label for="paroxos_send" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποστολή δεδομένων σε πάροχο');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="paroxos_send" value="1" <?php if ($paroxos_send==1) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row div_row_paroxos" style="<?php if ($paroxos_send!=1) echo 'display:none;';?>">
            <label for="aade_paroxos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πάροχος');?>:</label>
            <div class="col-md-8">
              <select id="aade_paroxos_id" class="form-control form-control-sm myneedsave">
                <option value="0" data-paroxos_need_username="0" data-paroxos_need_password="0" data-paroxos_need_key="0"></option>
                <?php
                $sql="select * FROM gks_aade_paroxos where paroxos_implemented=1 ORDER BY paroxos_sortorder ";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_aade_paroxos'].'" '.
                  'data-paroxos_need_username="'.$row_select['paroxos_need_username'].'" '.
                  'data-paroxos_need_password="'.$row_select['paroxos_need_password'].'" '.
                  'data-paroxos_need_key="'.$row_select['paroxos_need_key'].'" ';
                  if ($row_select['id_aade_paroxos']==$aade_paroxos_id) echo ' selected ';
                  echo '>'.$row_select['paroxos_name'].'</option>';
                }?>
              </select>    
            </div>
          </div>           
          <div class="form-group row div_row_paroxos" style="<?php if ($paroxos_send!=1) echo 'display:none;';?>">
            <label for="paroxos_branch" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Εγκατάστασης');?>:</label>
            <div class="col-md-8">
              <input id="paroxos_branch" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($paroxos_branch);?>" min="0" step="1" style="max-width:100px;">
            </div>
          </div> 
          <div class="form-group row div_row_paroxos div_paroxos_need_username" style="<?php if ($paroxos_send!=1 or $paroxos_need_username==0) echo 'display:none;';?>">
            <label for="pc_username" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα Χρήστη');?>:</label>
            <div class="col-md-8">
              <input id="pc_username" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($pc_username);?>">
            </div>
          </div> 
          <div class="form-group row div_row_paroxos div_paroxos_need_password" style="<?php if ($paroxos_send!=1 or $paroxos_need_password==0) echo 'display:none;';?>">
            <label for="pc_password" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός Πρόσβασης');?>:</label>
            <div class="col-md-8">
              <input id="pc_password" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($pc_password);?>">
            </div>
          </div> 
          <div class="form-group row div_row_paroxos div_paroxos_need_key" style="<?php if ($paroxos_send!=1 or $paroxos_need_key==0) echo 'display:none;';?>">
            <label for="pc_key" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κλειδί API');?>:</label>
            <div class="col-md-8">
              <input id="pc_key" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($pc_key);?>">
            </div>
          </div> 
          <div class="form-group row div_row_paroxos" style="<?php if ($paroxos_send!=1) echo 'display:none;';?>">
            <label for="paroxos_mydata_live" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πραγματική αποστολή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="paroxos_mydata_live" value="1" <?php if ($paroxos_mydata_live!=0) echo ' checked '; ?> class="switchery1_this">
              <small class="form-text text-muted" style="">
                <?php echo gks_lang('Όταν <strong style="font-size:150%;">δεν είναι</strong> ενεργοποιημένη η επιλογή <strong>"Πραγματική αποστολή"</strong>, τότε το παραστατικό θα αποσταλεί σε ένα άλλο δοκιμαστικό σύστημα του παρόχου απλά και μόνο για έλεγχο της σύνταξης του παραστατικού και το παραστατικό είναι σαν να μην το στείλατε ποτέ.');?>
                <br>
                <?php echo gks_lang('Όταν <strong style="font-size:150%;">είναι</strong> ενεργοποιημένη η επιλογή <strong>"Πραγματική αποστολή"</strong> τότε το παραστατικό θα αποσταλεί στον πάροχο.');?>
              </small>              
            </div>
          </div>          

          <div class="form-group row div_row_paroxos" style="<?php if ($paroxos_send!=1) echo 'display:none;';?>">
            <div class="col-md-8 offset-md-4">
              <button id="button_paroxos_check" class="btn btn-primary btm-sm"><?php echo gks_lang('Δοκιμή');?></button>
            </div>
          </div>
          
        </div>
      </div>      
      
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κοινωνικά Δίκτυα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('socialml');?>> 
      		<?php echo gks_sociallinks_item('gks_company_subs',$id);?>
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
$sql_fiscal="SELECT id_fiscal_position, fiscal_position_descr
FROM gks_eshop_fiscal_position
where fiscal_position_disable=0
ORDER BY fiscal_position_sortorder";
$result_fiscal = $db_link->query($sql_fiscal);        
if (!$result_fiscal) {debug_mail(false,'error sql',$sql_fiscal);die('sql error');}
$fiscal_list=array(); 
while ($row_fiscal = $result_fiscal->fetch_assoc()) {
  $fiscal_list[]=array(
    'id' => $row_fiscal['id_fiscal_position'],
    'descr' => gks_lang($row_fiscal['fiscal_position_descr'],'part4','fiscal_position_descr'),
  );
}
//print '<pre>';print_r($fiscal_list);die();

$sql_basefpa="SELECT id_fpa_base,fpa_base_descr
FROM gks_eshop_fpa_base
where fpa_base_disable=0
ORDER BY fpa_base_sortorder";
$result_basefpa = $db_link->query($sql_basefpa);        
if (!$result_basefpa) {debug_mail(false,'error sql',$sql_basefpa);die('sql error');}
$basefpa_list=array(); 
while ($row_basefpa = $result_basefpa->fetch_assoc()) {
  $basefpa_list[]=array(
    'id' => $row_basefpa['id_fpa_base'],
    'descr' => gks_lang($row_basefpa['fpa_base_descr'],'part4','fpa_base_descr'),
  );
}
//print '<pre>';print_r($basefpa_list);die();

$sql_fpa="SELECT id_fpa,fpa_descr_print
FROM gks_eshop_fpa
where can_select=1 and fpa_disable=0
ORDER BY fpa_sortorder";
$result_fpa = $db_link->query($sql_fpa);        
if (!$result_fpa) {debug_mail(false,'error sql',$sql_fpa);die('sql error');}
$fpa_list=array(); 
while ($row_fpa = $result_fpa->fetch_assoc()) {
  $fpa_list[]=array(
    'id' => $row_fpa['id_fpa'],
    'descr' => $row_fpa['fpa_descr_print'],
  );
}
//print '<pre>';print_r($fpa_list);die();

$sql_valbasefpa="SELECT fpa_base_id,fpa_id
FROM gks_company_subs_basefpa
where company_sub_id=".$id." and fpa_base_id<>0 and fpa_id<>0
ORDER BY fpa_base_id,fpa_id";
$result_valbasefpa = $db_link->query($sql_valbasefpa);        
if (!$result_valbasefpa) {debug_mail(false,'error sql',$sql_valbasefpa);die('sql error');}
$valbasefpa_list=array(); 
while ($row_valbasefpa = $result_valbasefpa->fetch_assoc()) {
  $key=$row_valbasefpa['fpa_base_id'];
  $valbasefpa_list[$key]=array(
    'fpa_base_id' => $row_valbasefpa['fpa_base_id'],
    'fpa_id' => $row_valbasefpa['fpa_id'],
  );
}
//print '<pre>';print_r($valbasefpa_list);die();

$sql_valfpa="SELECT fiscal_position_id,fpa_base_id,fpa_id
FROM gks_company_subs_fpa
where company_sub_id=".$id." and fpa_base_id<>0 and fpa_id<>0
ORDER BY fiscal_position_id,fpa_base_id,fpa_id";
$result_valfpa = $db_link->query($sql_valfpa);        
if (!$result_valfpa) {debug_mail(false,'error sql',$sql_valfpa);die('sql error');}
$valfpa_list=array(); 
while ($row_valfpa = $result_valfpa->fetch_assoc()) {
  $key=$row_valfpa['fiscal_position_id'].'_'.$row_valfpa['fpa_base_id'];
  $valfpa_list[$key]=array(
    'fiscal_id' => $row_valfpa['fiscal_position_id'],
    'fpa_base_id' => $row_valfpa['fpa_base_id'],
    'fpa_id' => $row_valfpa['fpa_id'],
  );
}
//print '<pre>';print_r($valfpa_list);die();


?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
       
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('ΦΠΑ');?>
        </div>
        <div class="card-body"<?php echo gks_card_body('fpa');?>> 
          <div class="row">
            <div class="col-md-6">
              <div style="text-align:center;font-weight:bold;margin: 16px 0px;"><?php echo gks_lang('Βασικοί συντελεστές ΦΠΑ');?></div>
              <?php 
              $visible_col=[];
              foreach ($basefpa_list as $mybase) {
                $visible_col[$mybase['id']]='display:none;';
              ?>
              <div class="form-group row justify-content-md-center1">
                <label for="mybasefpa_<?php echo $mybase['id'];?>" class="col-md-3 col-form-label form-control-sm text-md-right"><?php echo $mybase['descr'];?>:</label>
                <div class="col-md-3 gks_mybasefpa_div">
    
                  <select id="mybasefpa_<?php echo $mybase['id'];?>"
                  data-base_id="<?php echo $mybase['id'];?>"
                  class="gks_mybasefpa form-control form-control-sm myneedsave ">
                    <option value="0"></option>
                    <?php 
                      $curr_key=$mybase['id'];
                      $curr_value=0;
                      if (isset($valbasefpa_list[$curr_key])) $curr_value=$valbasefpa_list[$curr_key]['fpa_id'];
                      if ($curr_value>0) $visible_col[$mybase['id']]='';
                      foreach ($fpa_list as $myfpa) {
                      ?>
                    <option value="<?php echo $myfpa['id'];?>"
                      <?php if ($curr_value==$myfpa['id']) echo 'selected';?>
                      ><?php echo $myfpa['descr'];?></option>  
                    <?php } ?>
                  </select>
                                
                </div>
              </div>
              <?php } ?>
            </div>
            <div class="col-md-6" style="text-align:center;">
              <div style="text-align:center;font-weight:bold;margin: 16px 0px;"><?php echo gks_lang('Προεπιλογές');?></div>
              <p style="font-size: 0.875rem;"><?php echo gks_lang('Εφαρμογή των προεπιλογών για κάποιες χώρες / τύπoυς επιχειρήσεων');?></p>
              <button class="btn btn-sm btn-info" id="gks_fpa_template_show" data-show="0"><?php echo gks_lang('Εμφάνιση');?></button>
              <div id="div_fpa_templates" style="margin:4px;text-align:center;display:none;">
                  <button data-template-id="gr_normal" class="gks_fpa_template_apply btn btn-sm btn-warning" style="margin:4px;"><?php echo gks_lang('Ελλάδα - Κανονικό ΦΠΑ');?></button><br>
                  <button data-template-id="gr_meiome" class="gks_fpa_template_apply btn btn-sm btn-warning" style="margin:4px;"><?php echo gks_lang('Ελλάδα - Μειωμένο ΦΠΑ');?></button><br>
                  <button data-template-id="gr_mideni" class="gks_fpa_template_apply btn btn-sm btn-warning" style="margin:4px;"><?php echo gks_lang('Ελλάδα - Απαλλαγής ΦΠΑ');?></button><br>
                  <button data-template-id="cy_normal" class="gks_fpa_template_apply btn btn-sm btn-warning" style="margin:4px;"><?php echo gks_lang('Κύπρος');?></button>
              </div>
            </div>
          </div>
                    
          <div style="height: 1px;width: 100%;background-color: lightgray;margin: 16px 0px;"></div>
          <div style="text-align:center;font-weight:bold;margin: 16px 0px;"><?php echo gks_lang('Ανά φορολογική θέση');?></div>
          <div class="form-group row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 gks_items_col">
              <div class="table-dark gks_cost_label"><?php echo gks_lang('Φορολογική θέση');?></div>
            </div>
            <?php foreach ($basefpa_list as $mybase) {?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 gks_items_col gks_div_fpa_base_<?php echo $mybase['id'];?>" 
              style="<?php echo $visible_col[$mybase['id']];?>">
              <div class="table-dark gks_cost_label">
                <?php echo $mybase['descr'];?>  
              </div>
            </div>
            <?php } ?>
          </div>          
          
        <?php
        foreach ($fiscal_list as $myfiscal) {

          //$width_label=20;
          //$width_fpacol=(100-20)/count($basefpa_list);
        ?>
          <div class="form-group row">
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 col-form-label form-control-sm text-md-right"><?php echo $myfiscal['descr']?>:</div>
            <?php foreach ($basefpa_list as $mybase) {?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 gks_items_col gks_myfpa_div gks_div_fpa_base_<?php echo $mybase['id'];?>"
              style="<?php echo $visible_col[$mybase['id']];?>">
              
              <select 
              data-fiscal_id="<?php echo $myfiscal['id'];?>" 
              data-base_id="<?php echo $mybase['id'];?>"
              class="gks_myfpa form-control form-control-sm tooltipster myneedsave " 
              title="<?php echo $myfiscal['descr'].'<br>'.$mybase['descr'];?>">
                <option value="0"></option>
                <?php 
                  $curr_key=$myfiscal['id'].'_'.$mybase['id'];
                  foreach ($fpa_list as $myfpa) {
                    $curr_value=0;
                    if (isset($valfpa_list[$curr_key])) $curr_value=$valfpa_list[$curr_key]['fpa_id'];
                  ?>
                <option value="<?php echo $myfpa['id'];?>"
                  <?php if ($curr_value==$myfpa['id']) echo 'selected';?>
                  ><?php echo $myfpa['descr'];?></option>  
                <?php } ?>
              </select>
            </div>
            <?php }?>
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_company_sub'];?>" data-model="gks_company_subs" data-backurl="admin-company.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">
      <?php 
      echo getObjectRels('gks_company_subs',$id);
      echo getActivityObjectTable('gks_company_subs',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_company_subs','id'=>$id));
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_company_sub']>0) echo $row['id_company_sub'];?></span></div>
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

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Αποθήκες');?></span>
          <a class="btn btn-sm btn-primary gks_stoppropagation" style="margin-left:10px;" href="admin-warehouses-item.php?id=-1&company_sub_id=<?php echo $id;?>">
            <?php echo gks_lang('Προσθήκη');?>
          </a>          
        </div>
        <div class="card-body" <?php echo gks_card_body('ware');?>> 
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
              <tr >	
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  >#</th>
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo gks_lang('ID');?></th> 
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><span title="<?php echo gks_lang('Διαφορετικός χώρος');?>"><?php echo gks_lang('ΔΧ');?></span></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Τίτλος');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Χρώμα');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Τηλέφωνο');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%" nowrap="nowrap"><?php echo gks_lang('email');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Οδός');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Όροφος');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Περιοχή');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Πόλη');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('ΤΚ');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo gks_lang('Νομός');?></th>   
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo gks_lang('Χώρα');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Στίγμα');?></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap">'<?php echo gks_lang('Ενεργή');?></th>   
              </tr>
          </thead>
          <tbody>

<?php
      $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
      gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
      $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
      gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
      
      $sql = "SELECT gks_warehouses.*, 
      ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
      ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
      gks_company.company_title, gks_company_subs.company_sub_title, 
      ".gks_lang_sql_field('country_name',$lang_prepare_gks_country).",
      ".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)."
      FROM 
      ".$lang_prepare_gks_country['sql']['from1']."
      ".$lang_prepare_gks_nomos['sql']['from1']."
      (((((gks_warehouses 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_warehouses.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_warehouses.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
      LEFT JOIN gks_country ON gks_warehouses.warehouse_country_id = gks_country.id_country) 
      LEFT JOIN gks_nomoi ON gks_warehouses.warehouse_nomos_id = gks_nomoi.id_nomos)
      ".$lang_prepare_gks_country['sql']['from2']."
      ".$lang_prepare_gks_nomos['sql']['from2']."
      LEFT JOIN gks_company ON gks_warehouses.company_id = gks_company.id_company) 
      LEFT JOIN gks_company_subs ON gks_warehouses.company_sub_id = gks_company_subs.id_company_sub
      where gks_warehouses.company_sub_id=".$id;
      if (count($perm_id_warehouse_ids)>0) $sql.=" and gks_warehouses.id_warehouse in (".implode(',',$perm_id_warehouse_ids).")";
      $sql.=" order by gks_warehouses.warehouse_sortorder, gks_warehouses.warehouse_name";



      $result_list = $db_link->query($sql); 
      if (!$result_list) debug_mail(false,'error sql',$sql);
      if (!$result_list) die('sql error');


          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
      
      	  $i++;
          ?>
          
          <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
            <th scope="row" class="mytdcm"><?php echo ($i);?></th>
            <td nowrap class="mytdcm p-0">
              <table class="tableids3col">
                <tr>
                  <td><a href="admin-warehouses-item.php?id=<?php echo $row_list['id_warehouse'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
                  <td><?php echo $row_list['id_warehouse'];?></td>
                  <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row_list['id_warehouse'];?>" data-model="gks_warehouses"></i></td>
                </tr>      
              </table>
            </td>

        
            <td nowrap class="mytdcm"><img src="img/<?php echo $row_list['warehouse_is_company_place']==0 ? "1" :"0";  ?>.png" border="0" width="16"></td>
            <td class="mytdcml"><?php echo $row_list['warehouse_name'];?></td>
            <td style="background-color: <?php echo $row_list['warehouse_color'];?>"></td>
            
            
            <td class="mytdcml"><?php echo $row_list['warehouse_phone'];?></td>
            <td class="mytdcml"><?php echo $row_list['warehouse_email'];?></td>
            <td class="mytdcml"><?php echo $row_list['warehouse_odos'];?></td>
            <td class="mytdcml"><?php echo $row_list['warehouse_orofos'];?></td>
            <td class="mytdcml"><?php echo $row_list['warehouse_perioxi'];?></td>
            <td class="mytdcml"><?php echo $row_list['warehouse_poli'];?></td>
            <td class="mytdcml"><?php echo $row_list['warehouse_tk'];?></td>
            <td nowrap class="mytdcml"><?php echo $row_list['nomos_descr'];?></td>
            <td nowrap class="mytdcml"><?php echo $row_list['country_name'];?></td>
            <td nowrap class="mytdcm"><?php if ($row_list['warehouse_map_latitude']==0 and $row_list['warehouse_map_longitude']==0) {
                $pos_warehouse=0;
              } else {
                $pos_warehouse=1;
              }?>
              <img src="img/<?php echo $pos_warehouse;?>.png" border="0" width="16"></td>
              </td>
            
            <td class="mytdcm"><?php echo myimg010r($row_list['warehouse_disable']);?></td> 
          </tr>
          
        
      <?php } ?>  

  

                        
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
          <span style="vertical-align: middle;"><?php echo gks_lang('Υπάλληλοι');?></span>
          <button type="button" class="btn btn-sm btn-primary" onclick="window.location.href='admin-company-sub-item-export-excel-users.php?company_sub_id=<?php echo $id;?>'" style="vertical-align: middle;"><?php echo gks_lang('Εξαγωγή σε Excel');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('ypall');?>> 
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
              <tr >	
                  <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
                  <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" colspan=2>ID</th>         
     

                  <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"></th>  
                  <th class="table-dark" scope="col" style="text-align: left !important;" width="20%" nowrap="nowrap"><?php echo gks_lang('Επαφή');?></th>  
                  <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"><?php echo gks_lang('Ρόλοι');?></th>        
                  <th class="table-dark" scope="col" style="text-align: left !important;" width="0%" nowrap="nowrap"><?php echo gks_lang('Πρόσληψη');?></th>  
                  <th class="table-dark" scope="col" style="text-align: left !important;" width="20%" nowrap="nowrap"><?php echo gks_lang('Σχόλιο');?></th>         
                  <th class="table-dark" scope="col" style="text-align: left !important;" width="10%" nowrap="nowrap"><?php echo gks_lang('Ημερομηνία');?></th>         
                  <th class="table-dark" scope="col" style="text-align: left !important;" width="50%" nowrap="nowrap"><?php echo gks_lang('Τραπεζικοί Λογαριασμοί');?></th> 
                                
              </tr>
          </thead>
          <tbody>

<?php
      $sql = "SELECT gks_company_users.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities, ".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image
      FROM gks_company_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_company_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE (((gks_company_users.company_sub_id)=".$id."))
      ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;";
      $result_list = $db_link->query($sql); 
      if (!$result_list) debug_mail(false,'error sql',$sql);
      if (!$result_list) die('sql error');


          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
      
      	  $i++;
          ?>
        <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_company_users'];?>">
          <th scope="row" class="mytdcm"><?php echo $i;?></td>      
          <td nowrap class="mytdcm p-0"><?php echo $row_list['id_company_users'];?></td>
          <td nowrap class="mytdcm p-0"><i class="fas fa-trash-alt delete_company_user" data-id="<?php echo $row_list['id_company_users'];?>" data-model="gks_company_users"></i></td>
      
          <td class="p-0"><?php echo getUserPhoto($row_list['user_id'],$row_list['gks_wsl_current_user_image'],64);?></td>
          <td nowrap class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.$row_list['gks_nickname'].'</a>';?></td>
          <td nowrap class="mytdcml"><?php echo getUserRoleDescr($row_list['user_id']);?></td> 
          <td nowrap class="mytdcml"><?php echo date('d/m/Y',strtotime($row_list['date_hire']));?></td>   
          <td nowrap class="mytdcml"><?php echo nl2br_gks($row_list['sxolio']);?></td>   
          <td nowrap class="mytdcml"><?php echo showDate(strtotime($row_list['add_date']), 'd/m/Y H:i:s', 1);?></td>   
          <td nowrap>
          <?php
          $query_bank_accounts = "SELECT gks_bank_accounts.*, gks_banks.bank_descr
          FROM gks_bank_accounts LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank
          WHERE (((gks_bank_accounts.user_id)=".$row_list['user_id']."))
          ORDER BY gks_banks.bank_descr;";
          $result_bank_accounts = $db_link->query($query_bank_accounts); 
          if (!$result_bank_accounts) debug_mail(false,'error sql',$query_bank_accounts);
          if (!$result_bank_accounts) die('sql error');
          if ($result_bank_accounts->num_rows>0) {
          ?>
          <div style="overflow-x:auto;">
          <table class="generic-table" border="0" width="100%" cellspacing="0" cellpadding="5"  align=left id="list_bank_accounts">

              <?php
              $j = 0;
              while ($row_bank_accounts = $result_bank_accounts->fetch_assoc()) {          
                $j++;
                ?>
              <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_company_users'];?>">
                <td nowrap align="right" width="0%" class="mytdcml"><?php echo ($j);?></td>    
                <!--
                <td nowrap align="left"><a href="admin-banks-item.php?id=<?php echo $row_bank_accounts["bank_id"];?>"><?php echo $row_bank_accounts["bank_descr"];?></a></td>
                -->
                <td nowrap align="left" width="100%" class="mytdcml"><a href="admin-bank_accounts-item.php?id=<?php echo $row_bank_accounts["id_bank_account"];?>"><?php echo $row_bank_accounts["IBAN"];?></a></td>
              </tr>
              <?php
              }
              
              ?>
          </table>
          </div>
          <?php } ?>
        </tr>
        
      <?php } ?>  

        <tr class="" id="tr_new">
          <td nowrap align="right"></td>      
          <td nowrap align="center" colspan="2">
            <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
          </td>
          <td nowrap colspan="3">
            <input type="text"   name="company_sub_user"    id="company_sub_user"   class="form-control" style="width:98%;min-width:100px" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            <input type="hidden" name="company_sub_user_id" id="company_sub_user_id">
          </td>  
          <td nowrap align="center">
            <input type="text"   name="date_hire"    id="date_hire"   class="form-control" style="width:98%;min-width:150px" >
          </td>
          <td nowrap align="center">
            <input type="text"   name="hire_sxolio"  id="hire_sxolio"   class="form-control" style="width:98%;min-width:150px" >
          </td>
          <td nowrap colspan="2"></td>

        </tr>
        <tr class="" id="tr_new_button">
          <td nowrap colspan="3"></td>      
          <td nowrap colspan="7">
            <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_company_sub_user"><?php echo gks_lang('Προσθήκη');?></button>
          </td>  

        </tr>    

                        
          </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>



<div id="dialog_exit_date" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <table>
    <tr>
      <td style="width:1%;vertical-align:top">
        <i class="fas fa-question-circle" style="color: #dca327;font-size: 500%;"></i>
        
      </td>
      <td style="width:99%;vertical-align:top;padding: 20px 0px 0px 0px;">
        <span style="font-size: 120%;"><?php echo gks_lang('Σίγουρα θέλετε να διαγράψετε την εγγραφή;');?><br><br><?php echo gks_lang('Ορίστε την ημερομηνία απόλυσης');?>:</span>
        
        <input type="text"   name="exit_date"    id="exit_date"   class="form-control" style="width:50%;min-width:150px" >
      </td>
    </tr> 
  </table> 
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


var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_company_subs';
var from_php_activity_model='gks_company_subs';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_company_subs','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_company_subs','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_company_subs','delete',$id);?>;




var place_map_latitude = <?php echo floatval($row['company_sub_map_latitude']);?>;
var place_map_longitude = <?php echo floatval($row['company_sub_map_longitude']);?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

});
</script>

<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="js/admin-company-sub-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-company-and-sub-fpa-templates.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

echo gks_from_googlemaps_scripts();


include_once('_my_footer_admin.php');


