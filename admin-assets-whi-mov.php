<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Απογραφές Παγίων');
$nav_active_array=array('assets','assets_whi_mov');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_assets_whi_mov','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_gks_assets_whi_mov_edit=gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_whi_mov','edit',0);
$perm_gks_assets_whi_mov_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_whi_mov','delete',0);


$gks_custom_prepare = gks_custom_table_item_prepare('gks_assets_whi_mov',['from'=>'list']);



$filters = array();

$filters[] = array(
    'name' => 'fasset_type',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_whi_mov.mydate = '%V%'",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),               'sql' => "1=1"),
        array('value' => 1,  'text' => get_assets_whi_mov_descr('00draft'),    'sql' => "gks_assets_whi_mov.assets_whi_mov_status='00draft'"),
        array('value' => 2,  'text' => get_assets_whi_mov_descr('99complete'), 'sql' => "gks_assets_whi_mov.assets_whi_mov_status='99complete'"),
    ),
);

$filters[] = array(
	'name' => 'fmydate',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία'),
	'has_custom_date' => true,
	'field' => 'gks_assets_whi_mov.mydate',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_assets_whi_mov.mydate','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
    'name' => 'fwarehouse_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποθήκη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_assets_whi_mov.warehouse_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_assets_whi_mov.warehouse_id as id, gks_warehouses.warehouse_name as descr
    FROM gks_assets_whi_mov LEFT JOIN gks_warehouses ON gks_assets_whi_mov.warehouse_id = gks_warehouses.id_warehouse
    WHERE (((gks_warehouses.id_warehouse) Is Not Null)) 
    GROUP BY gks_assets_whi_mov.warehouse_id, gks_warehouses.warehouse_name
    ORDER BY gks_warehouses.warehouse_name",
);

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);

$sortable = array(
	array('name' => 'soid', 'field' => 'gks_assets_whi_mov.id_assets_whi_mov'),
	array('name' => 'sodate', 'field' => 'gks_assets_whi_mov.mydate'),
	array('name' => 'sostatus', 'field' => 'gks_assets_whi_mov.assets_whi_mov_status'),
	array('name' => 'sowarehouse', 'field' => 'gks_warehouses.warehouse_name'),
	array('name' => 'socc', 'field' => 'tassets_cc.assets_cc'),
	array('name' => 'souser', 'field' => 'wp_users_edit.gks_nickname'),
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
  'gks_warehouses.warehouse_name',
  'wp_users_edit.gks_nickname',
  'gks_assets_whi_mov.whi_mov_sxolio',
);

$search_fields=array_merge($search_fields,$gks_custom_prepare['sql_search_fields']);


$filter = array('html' => '', 'sql' => '', 'url' => '');
$search_string_value = (isset($_GET['search_string']) ? $_GET['search_string'] : '');
makeFilters($filters, $filter, $_GET,true,true,$search_string_value);

$search_where = make_search_where($search_string_value,$search_fields);
$search_where = !empty($search_where) ? ' AND '.$search_where : '';
//echo $search_where;
//die();

//$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
//$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';

$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
//$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';

$sorted = array('sql' => '', 'url' => '');

makeSortable($sortable, $sorted, $_GET);
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;




$sql = "SELECT SQL_CALC_FOUND_ROWS gks_assets_whi_mov.*, 
wp_users_edit.gks_nickname AS gks_nickname_edit, 
gks_warehouses.warehouse_name,
tassets_cc.assets_cc
".$gks_custom_prepare['sql_all_list_sele']."  
FROM ".$gks_custom_prepare['sql_all_list_from']." ((gks_assets_whi_mov 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS wp_users_edit ON gks_assets_whi_mov.user_id_edit = wp_users_edit.ID) 
LEFT JOIN gks_warehouses ON gks_assets_whi_mov.warehouse_id = gks_warehouses.id_warehouse)
LEFT JOIN (
  select assets_whi_mov_id, count(*) as assets_cc
  from gks_assets_whi_mov_assets
  group by assets_whi_mov_id
) as tassets_cc ON gks_assets_whi_mov.id_assets_whi_mov = tassets_cc.assets_whi_mov_id

where 1=1";

//echo '<pre>';echo $sql;die();

$sql.=$where . $search_where;

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_assets_whi_mov.mydate desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $sql;
//die();
	
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');

$sql_numrows = "SELECT FOUND_ROWS() AS `found_rows`;";
$res_numrows = $db_link->query($sql_numrows);
$row_numrows = $res_numrows->fetch_assoc();
$total_records = $row_numrows['found_rows'];

$pages = ceil($total_records / $rows_per_page) - 1;

$paging = array('records' => '', 'total' => '', 'pages' => '');
$url = $_SERVER['SCRIPT_NAME'].'?';
$params='';
if (isset($filter['url']) && $filter['url']!='') $params.='&'.$filter['url'];
if (isset($sorted['url']) && $sorted['url']!='') $params.='&'.$sorted['url'];
if (isset($_GET['search_string']) && $_GET['search_string']!='') $params.='&search_string='.urlencode($_GET['search_string']);

pagination($pages, $page, $total_records, $url, $paging, false, $params);
    
$sortable_url='?';
if (isset($filter['url']) && $filter['url']!='') $sortable_url.='&'.$filter['url'];
if (isset($page) && $page>0) $sortable_url.='&page='.$page;
if (isset($_GET['search_string']) && $_GET['search_string']!='') $sortable_url.='&search_string='.urlencode($_GET['search_string']);

$sortfields = explode("=", $sorted['url']);
if (count($sortfields) < 2) {
    $sortfields[0] = '';
    $sortfields[1] = '';
}



$gks_customtableview_user_settings=gks_customtableview_get_user_settings();

include_once('_my_header_admin.php');
?>
<link href="css/_gks_customtableview.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style class="gks_customtableview_style" data-index="1" data-rs=".gkstable" data-rs-pa=".gkstable > thead > tr > th">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,1);?>
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record tooltipster" href="admin-assets-whi-mov-item.php?id=-1" 
        title="<?php echo gks_lang('Μέσα από την σελίδα των παγίων<br>μπορείτε με την βοήθεια των φίλτρων<br>να επιλέξτε τα πάγια που θέλετε.<br>Mετά κάνοντας κλικ στο κουμπί <b>Δημιουργία απογραφής</b><br>θα δημιουργηθεί μία νεά απογραφή με τα επιλεγμένα πάγια.<br><br>Μετάβαση στην σελίδα των <a href=admin-assets.php class=gks_link>Πάγια</a>');?>"
        ><?php echo gks_lang('Προσθήκη Νέας Απογραφή Παγίων');?></a>
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings);?>
    </div>
  </div>
</div>



<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<?php mytablepages($paging, $total_records); ?>
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_assets_whi_mov">
<thead>
  <tr >	

    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">A/A</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodate', gks_lang('Ημερομηνία')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowarehouse', gks_lang('Αποθήκη')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"  nowrap="nowrap"><?php echo gks_lang('Σχόλιο');?></th>   
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socc', gks_lang('Πάγια')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Χρήστης')); ?></th>        
<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_assets_whi_mov'];?>">
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm">
      <table cellpadding=0 cellspacing=0 class="gks_tb1">
        <tr class="gks_tr1">
          <td class="gks_ttd1" colspan="2"><?php echo $row['id_assets_whi_mov'];?></td>
        </tr>  
        <tr class="gks_tr1">
          <td class="gks_ttd2"><a href="admin-assets-whi-mov-item.php?id=<?php echo $row['id_assets_whi_mov'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <?php if ($perm_gks_assets_whi_mov_delete) {?>
          <td class="gks_ttd3" ><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row['id_assets_whi_mov'];?>" data-model="gks_assets_whi_mov"></i></td>
          <?php } ?>
        </tr>  
     </table>
    </td>
    <td class="mytdcm" nowrap><?php echo showDate(strtotime($row['mydate']), 'd/m/Y\<\b\r\>H:i', 1);?></td>   
    <td class="mytdcm" nowrap><span class="assets_apografi_state_<?php echo $row['assets_whi_mov_status'];?>"><?php echo get_assets_whi_mov_descr($row['assets_whi_mov_status']);?></span></td>

 
    <td class="mytdcml" nowrap><?php echo $row['warehouse_name'];?></td>   
    <td class="mytdcml" nowrap><?php echo nl2br_gks($row['whi_mov_sxolio']);?></td>   
    <td class="mytdcm" nowrap><?php echo $row['assets_cc'];?></td>   
    <td class="mytdcml" nowrap><?php echo $row['gks_nickname_edit'];?></td>   

<?php
    echo gks_custom_table_list_rows($gks_custom_prepare,$row);
?>  
    
  </tr>
<?php    
    }
?>

</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_whi_mov','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_whi_mov','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_assets_whi_mov','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fmydate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fmydate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fmydate') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fmydate') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  }); 
  
;
  
    
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


