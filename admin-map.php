<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

if (GKS_TRANSFER) {
  $nav_active_array=array('transfer','transfer_map');
} else {
  $nav_active_array=array('crm','crm_map');
}
db_open();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_map','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



$my_page_title=gks_lang('Χάρτης');
stat_record();


include_once('_my_header_admin.php');
?>

<link href="css/admin-map.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<div id="gks_map_div_main">
  <div id="gks_map_div_panel_left">
    <div id="text_search_auto_googlemaps">
      <i class="fas fa-window-close" id="close_text_search_auto_googlemaps"></i>
      <small class="form-text text-muted"></small>
    </div> 
    <div id="gks_map_div_panel_left_tools">
      <button data-id="users" class="button_tool_settings btn btn-success btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('Επαφές');?>">
        <span class="fas fa-user"></span>
      </button>
      <button data-id="appmobile" class="button_tool_settings btn btn-success btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('gks ERP App Mobile');?>">
        <span class="fas fa-mobile-alt"></span>
      </button>
      <button data-id="calendar" class="button_tool_settings btn btn-success btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('Ημερολόγιο','part2');?>">
        <span class="fa fa-calendar-alt"></span>
      </button>
      <button data-id="lead" class="button_tool_settings btn btn-success btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('Ευκαιρίες');?>">
        <span class="fas fa-smile"></span>
      </button>
      <button data-id="task" class="button_tool_settings btn btn-success btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('Εργασίες');?>">
        <span class="fas fa-tools"></span>
      </button>
      <button data-id="machine" class="button_tool_settings btn btn-success btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('Συσκευές');?>">
        <span class="fas fa-hdd"></span>
      </button>
      <button data-id="poi" class="button_tool_settings btn btn-success btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('Σημεία Ενδιαφέροντος');?>">
        <span class="fas fa-map-marker"></span>
      </button>
    </div>
    
   
    
    <div id="gks_map_div_panel_users" style="display:none;">
      <div id="gks_map_div_panel_users_header">
        <span class="cell1"><?php echo gks_lang('Επαφές');?></span>
        <span class="cell2">
          <input type="checkbox" id="users_enable" value="1" class="switchery1_sel">
        </span>
      </div>
      <div id="list_users"></div>
    </div>

    <div id="gks_map_div_panel_appmobile" style="display:none;">
      <div id="gks_map_div_panel_appmobile_header">
        <span class="cell1"><?php echo gks_lang('gks ERP App Mobile');?></span>
        <span class="cell2">
          <input type="checkbox" id="appmobile_enable" value="1" class="switchery1_sel">
        </span>
      </div>
      <div id="list_appmobile"></div>
    </div>

    <div id="gks_map_div_panel_lead" style="display:none;">
      <div id="gks_map_div_panel_lead_header">
        <span class="cell1"><?php echo gks_lang('Ευκαιρίες');?></span>
        <span class="cell2">
          <input type="checkbox" id="lead_enable" value="1" class="switchery1_sel">
        </span>
      </div>
      <div id="list_lead"></div>
    </div>
        
    <div id="gks_map_div_panel_calendar" style="display:none;">
      <div id="gks_map_div_panel_calendar_header">
        <span class="cell1"><?php echo gks_lang('Ημερολόγιο','part2');?></span>
        <span class="cell2">
          <input type="checkbox" id="calendar_enable" value="1" class="switchery1_sel">
        </span>
      </div>
      <div id="list_calendar"></div>
    </div>
        
    <div id="gks_map_div_panel_task" style="display:none;">
      <div id="gks_map_div_panel_task_header">
        <span class="cell1"><?php echo gks_lang('Εργασίες');?></span>
        <span class="cell2">
          <input type="checkbox" id="task_enable" value="1" class="switchery1_sel">
        </span>
      </div>
      <div id="list_task"></div>
    </div>
    
    <div id="gks_map_div_panel_machine" style="display:none;">
      <div id="gks_map_div_panel_machine_header">
        <span class="cell1"><?php echo gks_lang('Συσκευές');?></span>
        <span class="cell2">
          <input type="checkbox" id="machine_enable" value="1" class="switchery1_sel">
        </span>
      </div>
      <div id="list_machine"></div>
    </div>
        
    <div id="gks_map_div_panel_poi" style="display:none;">
      <div id="gks_map_div_panel_poi_header">
        <span class="cell1"><?php echo gks_lang('Σημεία Ενδιαφέροντος');?></span>
        <span class="cell2">
          <input type="checkbox" id="poi_enable" value="1" class="switchery1_sel">
        </span>
      </div>
      <div id="list_poi"></div>
    </div>
    
    
  </div>  
  
  <div id="gks_map_div_panel_map">
    <div id="gks_map_div_panel_map_top">
      <button id="button_hide_left" class="btn btn-primary btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('Απόκρυψη/Εμφάνιση αριστερής στήλης');?>">
        <span class="far fa-window-maximize"></span>
      </button>
      <button id="button_full" class="btn btn-primary btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('Απόκρυψη/Εμφάνιση κεφαλίδας και υποσέλιδου');?>">
        <span class="fa fa-expand"></span>
      </button>
      <button id="map_pos" class="btn btn-primary btn-sm tooltipster" type="button" data-s="0" title="<?php echo gks_lang('Εντοπισμός τοποθεσίας');?>">
        <i class="far fa-dot-circle"></i>
      </button>
      <button id="map_measure_tool" class="btn btn-primary btn-sm tooltipster" type="button" title="<?php echo gks_lang('Μέτρηση απόστασης');?>">
        <span class="fas fa-ruler"></span>
      </button> 
 
      
      <div style="display:inline-block;vertical-align: bottom;">
      <input id="text_search" class="form-control form-control-sm tooltipster" type="text" placeholder="<?php echo gks_lang('Αναζήτηση');?>" title="<?php echo gks_lang('Αναζήτηση σημείου');?>">
      </div>
      <input id="poi_map_latitude"  class="form-control form-control-sm tooltipster" type="number" placeholder="Latitude"  step="0.0001" title="<?php echo gks_lang('Γεωγραφικό πλάτος');?>">
      <input id="poi_map_longitude" class="form-control form-control-sm tooltipster" type="number" placeholder="Longitude" step="0.0001" title="<?php echo gks_lang('Γεωγραφικό μήκος');?>">

      <button id="mygetdata_hourglass" class="btn btn-primary btn-sm tooltipster" title="<?php echo gks_lang('Λήψη δεδομένων');?>...">
        <span class="fas fa-hourglass-half"></span>
      </button> 

      

        
      
    </div>
    <div id="gks_map_div_panel_map_show">
      
      
      
    </div>
    
  </div>

  
</div>


<script type="text/javascript">


//var dddd = new Date();
//var dddd1=dddd.getMinutes()*60*1000 + dddd.getSeconds() * 1000 + dddd.getMilliseconds();
//console.log(dddd1);

function gks_map_js_load_initialize_current() {
  //console.log('map_js_load_initialize');
  //dddd = new Date();
  //dddd2=dddd.getMinutes()*60*1000 + dddd.getSeconds() * 1000 + dddd.getMilliseconds();
  //console.log(dddd2,dddd1,dddd2-dddd1);
  showmap_run();
  gks_map_js_load_initialize_default();
}

</script>  
<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_poi','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_poi','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_poi','delete',0);?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});

 
</script>



<script src="js/admin-map.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo gks_from_googlemaps_scripts('maps,places,drawing,geometry,marker', 'gks_map_js_load_initialize_current', true, true);


include_once('_my_footer_admin.php');




