<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$nav_active_array=array('transfer','transfer_poi');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_poi',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_poi_ids=gks_permission_user_condition($my_wp_user_id,'gks_poi','01');


$gks_custom_prepare = gks_custom_table_item_prepare('gks_poi',['from'=>'item']);

//print '<pre>';print_r($gks_user_settings);die();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_poi']=-1;
  $row['poi_descr']='';
  $row['poi_type_id']=0;
  $row['poi_locode']='';
  $row['poi_iata_code']='';
  $row['poi_icao_code']='';
  $row['poi_parent_id']=0;
  $row['fullpath']='';
  $row['poi_phone']='';
  $row['poi_email']='';
  $row['poi_website']='';
  $row['poi_odos']='';
  $row['poi_arithmos']='';
  $row['poi_orofos']='';
  $row['poi_perioxi']='';
  $row['poi_poli']='';
  $row['poi_tk']='';
  $row['poi_nomos_id']=0;
  $row['poi_country_id']=91;
  $row['poi_map_latitude']='';
  $row['poi_map_longitude']='';
  $row['poi_disable']=0;

  $row['gks_nickname'] ='';
  $row['poi_color']='';

  $row['poi_comments']='';
  $row['poi_areas']='';


  $row['poi_company_id']=0;
  $row['poi_company_sub_id']=0;

  $row['poi_parastatiko_apodiji_journal_id']=0;
  $row['poi_parastatiko_apodiji_seira_id']=0;
  $row['poi_parastatiko_timologio_journal_id']=0;
  $row['poi_parastatiko_timologio_seira_id']=0;
  
  $my_page_title=gks_lang('Νέο Σημείο Ενδιαφέροντος');
} else {
  $sql ="SELECT gks_poi.*, gks_country.country_name, gks_nomoi.nomos_descr,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
  pointsfullpath.fullpath

  FROM ((((gks_poi 
  LEFT JOIN (
    SELECT gks_poi.id_poi, 
    CONCAT_WS('\\\\',
                    ug10.poi_descr,
                    ug9.poi_descr,
                    ug8.poi_descr,
                    ug7.poi_descr,
                    ug6.poi_descr,
                    ug5.poi_descr,
                    ug4.poi_descr,
                    ug3.poi_descr,
                    ug2.poi_descr,
                    gks_poi.poi_descr) as fullpath
    FROM ((((((((gks_poi 
    LEFT JOIN gks_poi AS ug2 ON gks_poi.poi_parent_id = ug2.id_poi) 
    LEFT JOIN gks_poi AS ug3 ON ug2.poi_parent_id = ug3.id_poi)
    LEFT JOIN gks_poi AS ug4 ON ug3.poi_parent_id = ug4.id_poi)
    LEFT JOIN gks_poi AS ug5 ON ug4.poi_parent_id = ug5.id_poi)
    LEFT JOIN gks_poi AS ug6 ON ug5.poi_parent_id = ug6.id_poi)
    LEFT JOIN gks_poi AS ug7 ON ug6.poi_parent_id = ug7.id_poi)
    LEFT JOIN gks_poi AS ug8 ON ug7.poi_parent_id = ug8.id_poi)
    LEFT JOIN gks_poi AS ug9 ON ug8.poi_parent_id = ug9.id_poi)
    LEFT JOIN gks_poi AS ug10 ON ug9.poi_parent_id = ug10.id_poi
  
  ) as pointsfullpath on gks_poi.poi_parent_id=pointsfullpath.id_poi)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_poi.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_poi.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN gks_country ON gks_poi.poi_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_poi.poi_nomos_id = gks_nomoi.id_nomos
  
  where gks_poi.id_poi = ".$id;
  if (count($perm_id_poi_ids)>0) $sql.=" and gks_poi.id_poi in (".implode(',',$perm_id_poi_ids).")";
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
  $my_page_title=gks_lang('Σημείο Ενδιαφέροντος').': '.$row['poi_descr'];
  $object_title=$row['poi_descr'];
}


$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

stat_record();

$areas=[];
$areas['circles']=[];
$areas['rectangles']=[];
$areas['polygons']=[];


if (trim_gks($row['poi_areas'])!='') {
  $temp=unserialize(trim_gks($row['poi_areas'])); 
  if (is_array($temp) and isset($areas['circles']) and isset($areas['rectangles']) and isset($areas['polygons'])) {
    $areas=$temp;
  }
}
 

$user_companys=gks_get_companys_list();
if (count($user_companys)==0) {
  debug_mail(false,'company not found','');
  echo 'company not found';
  die(); 
}
      
$lang_data_obj=gks_lang_data_obj_prepare('gks_poi','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>

<link href="css/admin-poi-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<div id="map_div_float_pos"></div>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Σημείο Ενδιαφέροντος');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Σημείο Ενδιαφέροντος');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
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
        <div class="card-body" <?php echo gks_card_body('comp');?>> 

          <div class="form-group row">
            <label for="poi_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="poi_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_descr']);?>">
            </div>
          </div>
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('poi_descr'));
          ?>
          <div class="form-group row">
            <label for="poi_type_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-md-8">
              <select name="poi_type_id" id="poi_type_id"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="select id_poi_type,poi_type_descr FROM gks_poi_type ORDER BY poi_type_sortorder ";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_poi_type'].'" ';
                if ($row_select['id_poi_type']==$row['poi_type_id']) echo ' selected ';
                echo '>'.$row_select['poi_type_descr'].'</option>';
              }?></select>
            </div>
          </div>
          <div class="form-group row" id="poi_locode_div" style="<?php if (in_array($row['poi_type_id'],[1,3])==false) echo 'display:none;';?>">
            <label for="poi_locode" class="col-md-4 col-form-label form-control-sm text-md-right">UN/LOCODE:</label>
            <div class="col-md-8">
              <input id="poi_locode" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_locode']);?>">
            </div>
          </div> 
          <div class="form-group row" id="poi_iata_code_div" style="<?php if (in_array($row['poi_type_id'],[2,4])==false) echo 'display:none;';?>">
            <label for="poi_iata_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός IATA');?>:</label>
            <div class="col-md-8">
              <input id="poi_iata_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_iata_code']);?>">
            </div>
          </div> 
          <div class="form-group row" id="poi_icao_code_div" style="<?php if (in_array($row['poi_type_id'],[2,4])==false) echo 'display:none;';?>">
            <label for="poi_icao_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός ICAO');?>:</label>
            <div class="col-md-8">
              <input id="poi_icao_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_icao_code']);?>">
            </div>
          </div> 



                    
          <div class="form-group row">
            <label for="poi_parent_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γονικό Σημείο');?>:</label>
            <div class="col-md-8">
              <input id="poi_parent_id" data-id="<?php echo $row['poi_parent_id'];?>" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['fullpath']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <small class="form-text text-muted"><?php echo gks_lang('Έως 10 επίπεδα');?></small>
            </div>
          </div>                    
          <div class="form-group row">
            <label for="poi_phone" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερό Τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="poi_phone" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_phone']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="poi_email" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ηλ. διεύθυνση');?>:</label>
            <div class="col-md-8">
              <input id="poi_email" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_email']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="poi_website" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ιστότοπος');?>:</label>
            <div class="col-md-8">
              <input id="poi_website" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_website']);?>">
            </div>
          </div> 
 
          <div class="form-group row">
            <label for="poi_odos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-md-8">
              <input id="poi_odos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_odos']);?>">
              <small class="form-text text-muted auto_googlemaps" id="poi_odos_auto_googlemaps"></small>
            </div>
          </div> 
          <div class="form-group row">
            <label for="poi_arithmos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <input id="poi_arithmos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_arithmos']);?>">
            </div>
          </div> 
          
          
          <div class="form-group row">
            <label for="poi_orofos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-md-8">
              <input id="poi_orofos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_orofos']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="poi_perioxi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-md-8">
              <input id="poi_perioxi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_perioxi']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="poi_poli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-md-8">
              <input id="poi_poli" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_poli']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="poi_tk" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΤΚ');?>:</label>
            <div class="col-md-8">
              <input id="poi_tk" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_tk']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="poi_nomos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-md-8">
              <select id="poi_nomos_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomos['sql']['from2']."
                where country_id=".$row['poi_country_id']." ORDER BY nomos_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_nomos'].'" ';
                  if ($row_select['id_nomos']==$row['poi_nomos_id']) echo ' selected ';
                  echo '>'.$row_select['nomos_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>
          <div class="form-group row">
            <label for="poi_country_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-md-8">
              <select id="poi_country_id" class="form-control form-control-sm myneedsave">
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
                  if ($row_select['id_country']==$row['poi_country_id']) echo ' selected ';
                  echo '>'.$row_select['country_name'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
    
          

          <div class="form-group row">
            <label for="poi_map_latitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Πλάτος');?>:</label>
            <div class="col-md-8">
              <input id="poi_map_latitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_map_latitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-90" max="90" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label for="poi_map_longitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Μήκος');?>:</label>
            <div class="col-md-8">
              <input id="poi_map_longitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_map_longitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-180" max="180" step="0.00001">
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
            <div class="col-md-12" id="map_div_float">
              <div id="map_div" style="display: none;">
                <div id="map_panel">
                  
                  <div class="btn-group" id="map_nav_shape_group">
                    <button data-dir="f" class="map_nav_shape button fc-prevYear-button btn btn-primary btn-sm tooltipster" type="button" title="<?php echo gks_lang('Επιλογή πρώτου σχήματος');?>">
                      <span class="fa fa-angle-double-left"></span>
                    </button>
                    <button data-dir="-1" class="map_nav_shape fc-prev-button btn btn-primary btn-sm tooltipster" type="button" title="<?php echo gks_lang('Επιλογή προηγούμενου σχήματος');?>">
                      <span class="fa fa-chevron-left"></span>
                    </button>
                    <span id="map_nav_shape_label"><span id="map_nav_shape_label2">--</span></span>
                    <button data-dir="1" class="map_nav_shape fc-next-button btn btn-primary btn-sm tooltipster" type="button" title="<?php echo gks_lang('Επιλογή επόμενου σχήματος');?>">
                      <span class="fa fa-chevron-right"></span>
                    </button>
                    <button data-dir="l" class="map_nav_shape fc-nextYear-button btn btn-primary btn-sm tooltipster" type="button" title="<?php echo gks_lang('Επιλογή τελευταίου σχήματος');?>">
                      <span class="fa fa-angle-double-right"></span>
                    </button>
                  </div>
                  <div id="map_color_div" class="btn btn-sm btn-secondary tooltipster" title="<?php echo gks_lang('Χρώμα επιλεγμένου σχήματος');?>">
                    <span id="map_color_label"><?php echo gks_lang('Χρώμα');?>: </span><span id="map_color_palette"></span>
                  </div>
                    
                  
                  <button id="map_delete_button" class="btn btn-sm btn-primary"><?php echo gks_lang('Διαγραφή επιλεγμένου σχήματος');?></button>
                  <button id="map_delete_all_button" class="btn btn-sm btn-primary"><?php echo gks_lang('Διαγραφή όλων των σχημάτων');?></button>
                  <button id="map_center_point" class="btn btn-primary btn-sm tooltipster" type="button" title="<?php echo gks_lang('Ορισμός σημείου στο κέντρο των σχημάτων');?>">
                    <span class="fas fa-map-marker-alt"></span>
                  </button>
                  <button id="map_zoom_bounds" class="btn btn-primary btn-sm tooltipster" type="button" title="<?php echo gks_lang('Εστίαση σε όλα τα σχήματα');?>">
                    <span class="fas fa-search-location"></span>
                  </button>                  
                  <button id="map_measure_tool" class="btn btn-primary btn-sm tooltipster" type="button" title="<?php echo gks_lang('Μέτρηση απόστασης');?>">
                    <span class="fas fa-ruler"></span>
                  </button>                  


                  <button id="map_fullscreen" class="btn btn-primary btn-sm tooltipster" type="button" title="<?php echo gks_lang('Πλήρης οθόνη');?>">
                    <span class="fa fa-expand"></span>
                  </button>
                  
                </div>
                <div id="map"></div>  
              </div>
            </div>             
          </div>


          <div class="form-group row">
            <label for="poi_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="poi_disable" value="1" <?php if ($row['poi_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>  
          

          
          <div class="form-group row">
            <label for="poi_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα');?>:</label>
            <div class="col-md-8">
              <input id="poi_color" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poi_color']);?>" style="max-width:200px;">
            </div>
          </div> 

          <div class="form-group row">
            <label for="poi_comments" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="poi_comments" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;"><?php echo htmlspecialchars_gks($row['poi_comments']); ?></textarea>
            </div>
          </div>

        </div>
      </div>


    




      

    </div>
    
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Λογιστική');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('log');?>>

          <div class="form-group row">
            <label for="poi_company_id_sub_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <select id="poi_company_id_sub_id" class="form-control form-control-sm myneedsave">
                <option value="0|0"><?php echo gks_lang('Προεπιλογή');?></option>
                <?php
                $poi_company_id_sub_id=$row['poi_company_id'].'|'.$row['poi_company_sub_id'];
                foreach ($user_companys as $row_select) {
                  echo '<option value="'.$row_select['id'].'" ';
                  if ($row_select['id']==$poi_company_id_sub_id) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>
              <small class="form-text text-muted"><?php echo gks_lang('Είτε είναι σημείο αναχώρησης είτε άφιξης');?></small>
            </div>
          </div>
          
          <div id="div_poi_company_id_sub_id" style="<?php echo ($poi_company_id_sub_id=='0|0' ? 'display:none;' : '');?>">
            <div style="text-align:center;font-weight:bold;"><?php echo gks_lang('Απόδειξη');?></div>

            <div class="form-group row" >
              <label for="poi_parastatiko_apodiji_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?>:</label>
              <div class="col-md-8">
                <select id="poi_parastatiko_apodiji_journal_id" class="form-control form-control-sm myneedsave">
                  <option value="0"></option>
                  <?php
                  $sql_list="SELECT gks_acc_journal.id_acc_journal AS id, gks_acc_journal.acc_journal_descr AS descr, 
                  gks_acc_journal.company_id AS c, gks_acc_journal.company_sub_id AS cs
                  FROM gks_acc_journal 
                  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                  WHERE gks_acc_journal.is_disable=0 
                  AND gks_acc_eidi_parastatikon.eidos_parastatikou_type_id In (1)
                  AND eidos_parastatikou_aade_code='11.2'
                  ORDER BY gks_acc_journal.sortorder;";
                  $result_list = $db_link->query($sql_list); 
                  $id_acc_journal_array=array(-1,);
                  if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                  while ($row_list= $result_list->fetch_assoc()) { 
                    echo '<option '.($row_list['id']==$row['poi_parastatiko_apodiji_journal_id'] ? 'selected' : '').' value="'.$row_list['id'].'" data-c="'.$row_list['c'].'" data-cs="'.$row_list['cs'].'">'.$row_list['descr'].'</option>';
                    $id_acc_journal_array[]=$row_list['id'];
                  }?>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="poi_parastatiko_apodiji_seira_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά');?>:</label>
              <div class="col-md-8">
                <select id="poi_parastatiko_apodiji_seira_id" class="form-control form-control-sm myneedsave">
                  <option value="0"></option>
                  <?php
                  $sql_list="SELECT id_acc_seira as id, seira_descr as descr, acc_journal_id as j
                  FROM gks_acc_seires
                  WHERE is_disable=0
                  and acc_journal_id in (".implode(',',$id_acc_journal_array).")
                  ORDER BY sortorder";
                  $result_list = $db_link->query($sql_list);  
                  if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                  while ($row_list= $result_list->fetch_assoc()) { 
                    echo '<option '.($row_list['id']==$row['poi_parastatiko_apodiji_seira_id'] ? 'selected' : '').' value="'.$row_list['id'].'" data-j="'.$row_list['j'].'">'.$row_list['descr'].'</option>';
                  }?>
                </select>
              </div>
            </div>          
          
            <div style="text-align:center;font-weight:bold;"><?php echo gks_lang('Τιμολόγιο');?></div>          
            <div class="form-group row" >
              <label for="poi_parastatiko_timologio_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?>:</label>
              <div class="col-md-8">
                <select id="poi_parastatiko_timologio_journal_id" class="form-control form-control-sm myneedsave">
                  <option value="0"></option>
                  <?php
                  $sql_list="SELECT gks_acc_journal.id_acc_journal AS id, gks_acc_journal.acc_journal_descr AS descr, 
                  gks_acc_journal.company_id AS c, gks_acc_journal.company_sub_id AS cs
                  FROM gks_acc_journal 
                  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                  WHERE gks_acc_journal.is_disable=0 
                  AND gks_acc_eidi_parastatikon.eidos_parastatikou_type_id In (1)
                  AND eidos_parastatikou_aade_code='2.1'
                  ORDER BY gks_acc_journal.sortorder;";
                  $result_list = $db_link->query($sql_list); 
                  $id_acc_journal_array=array(-1,);
                  if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                  while ($row_list= $result_list->fetch_assoc()) { 
                    echo '<option '.($row_list['id']==$row['poi_parastatiko_timologio_journal_id'] ? 'selected' : '').' value="'.$row_list['id'].'" data-c="'.$row_list['c'].'" data-cs="'.$row_list['cs'].'">'.$row_list['descr'].'</option>';
                    $id_acc_journal_array[]=$row_list['id'];
                  }?>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="poi_parastatiko_timologio_seira_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά');?>:</label>
              <div class="col-md-8">
                <select id="poi_parastatiko_timologio_seira_id" class="form-control form-control-sm myneedsave">
                  <option value="0"></option>
                  <?php
                  $sql_list="SELECT id_acc_seira as id, seira_descr as descr, acc_journal_id as j
                  FROM gks_acc_seires
                  WHERE is_disable=0
                  and acc_journal_id in (".implode(',',$id_acc_journal_array).")
                  ORDER BY sortorder";
                  $result_list = $db_link->query($sql_list);  
                  if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                  while ($row_list= $result_list->fetch_assoc()) { 
                    echo '<option '.($row_list['id']==$row['poi_parastatiko_timologio_seira_id'] ? 'selected' : '').' value="'.$row_list['id'].'" data-j="'.$row_list['j'].'">'.$row_list['descr'].'</option>';
                  }?>
                </select>
              </div>
            </div>            
          
          </div>
          
        </div>
      </div>
          

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Περιοχές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('area');?>>        

          <?php
          
          $query = "SELECT
gks_transfer_area2poi.*,
ccpois.ccc,
ug2.transfer_area_descr AS gt2, 
ug3.transfer_area_descr AS gt3, 
ug4.transfer_area_descr AS gt4, 
ug5.transfer_area_descr AS gt5,
ug6.transfer_area_descr AS gt6,
ug7.transfer_area_descr AS gt7,
ug8.transfer_area_descr AS gt8,
ug9.transfer_area_descr AS gt9,
ug10.transfer_area_descr AS gt10,

ug2.id_transfer_area AS id2, 
ug3.id_transfer_area AS id3, 
ug4.id_transfer_area AS id4, 
ug5.id_transfer_area AS id5,
ug6.id_transfer_area AS id6,
ug7.id_transfer_area AS id7,
ug8.id_transfer_area AS id8,
ug9.id_transfer_area AS id9,
ug10.id_transfer_area AS id10,
CONCAT_WS('\\\\',
        ug10.transfer_area_descr,
        ug9.transfer_area_descr,
        ug8.transfer_area_descr,
        ug7.transfer_area_descr,
        ug6.transfer_area_descr,
        ug5.transfer_area_descr,
        ug4.transfer_area_descr,
        ug3.transfer_area_descr,
        ug2.transfer_area_descr,
        gks_transfer_area.transfer_area_descr) as fullpath,
CONCAT_WS('\\\\',
        ug10.transfer_area_descr,
        ug9.transfer_area_descr,
        ug8.transfer_area_descr,
        ug7.transfer_area_descr,
        ug6.transfer_area_descr,
        ug5.transfer_area_descr,
        ug4.transfer_area_descr,
        ug3.transfer_area_descr,
        ug2.transfer_area_descr) as dirpath
FROM ((((((((((gks_transfer_area2poi
LEFT JOIN gks_transfer_area ON gks_transfer_area2poi.transfer_area_id = gks_transfer_area.id_transfer_area)
LEFT JOIN (
  SELECT transfer_area_id, Count(poi_id) AS ccc
  FROM gks_transfer_area2poi
  GROUP BY transfer_area_id
) AS ccpois ON gks_transfer_area.id_transfer_area = ccpois.transfer_area_id)
LEFT JOIN gks_transfer_area AS ug2  ON gks_transfer_area.transfer_area_parent_id = ug2.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug3  ON ug2.transfer_area_parent_id = ug3.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug4  ON ug3.transfer_area_parent_id = ug4.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug5  ON ug4.transfer_area_parent_id = ug5.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug6  ON ug5.transfer_area_parent_id = ug6.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug7  ON ug6.transfer_area_parent_id = ug7.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug8  ON ug7.transfer_area_parent_id = ug8.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug9  ON ug8.transfer_area_parent_id = ug9.id_transfer_area)
LEFT JOIN gks_transfer_area AS ug10 ON ug9.transfer_area_parent_id = ug10.id_transfer_area
WHERE gks_transfer_area2poi.poi_id=".$id."
          ORDER BY fullpath;";
          
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="transfer_area_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Ομάδα');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="transfer_area_tr_exist" data-id="<?php echo $row_list['id_transfer_area2poi'];?>">
              <th scope="row" nowrap align="right" class="transfer_area_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_transfer_area_delete_after|<?php echo $row_list['id_transfer_area2poi'];?>" data-id="<?php echo $row_list['id_transfer_area2poi'];?>" data-model="gks_transfer_area2poi">            
              </td>
              <td nowrap align="center"><a href="admin-transfer-area-item.php?id=<?php echo $row_list['transfer_area_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td nowrap><?php echo $row_list['fullpath'];?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr>
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="transfer_area"    id="transfer_area"   class="form-control form-control-sm" style="width:calc(100% - 110px);display: inline-block;margin-right: 10px;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="transfer_area_id" id="transfer_area_id">
                <button style="justify-content: center!important;vertical-align: baseline;" type="button" class="btn btn-sm btn-primary" id="add_transfer_area2poi"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>
      
          </tbody>
          </table>      

        </div>
      </div>
 
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κοινωνικά Δίκτυα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('socialml');?>> 
      		<?php echo gks_sociallinks_item('gks_poi',$id);?>
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_poi'];?>" data-model="gks_poi" data-backurl="admin-poi.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>
      
<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">
      <?php 
      echo getObjectRels('gks_poi',$id);   
      echo getActivityObjectTable('gks_poi',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_poi','id'=>$id));
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
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_poi']>0) echo $row['id_poi'];?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add']))echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
          </div>
          
          <?php
          gks_plugins_functions_run('admin_poi_item_katagrafi',array(
            'id'=>&$id,
            'row'=>&$row,
          ));
          ?>
        </div>
      </div>
      
    </div>
  </div>
</div>

 



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;


var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 
  
  


var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_poi';
var from_php_activity_model='gks_poi';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');


var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_poi','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_poi','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_poi','delete',$id);?>;



var place_map_latitude = <?php echo floatval($row['poi_map_latitude']);?>;
var place_map_longitude = <?php echo floatval($row['poi_map_longitude']);?>;


var from_php_map_areas=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($areas));?>'));



jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});

 
</script>

<script src='/my/js/tinymce/tinymce.min.js'></script>

<script src="js/admin-poi-item.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

echo gks_from_googlemaps_scripts('maps,places,drawing,geometry,marker','gks_map_js_load_initialize_default',true,true);



include_once('_my_footer_admin.php');

//https://zhenyanghua.github.io/MeasureTool-GoogleMaps-V3/



