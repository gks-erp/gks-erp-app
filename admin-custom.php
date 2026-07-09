<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Προσαρμογή');
$nav_active_array=array('manage','manage_settings','manage_custom');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_custom_table','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}










$filters = array();
$sortable = array(
	array('name' => 'soid',    'field' => 'gks_custom_table.id_custom_table'),
	array('name' => 'sodescr', 'field' => 'gks_custom_table.custom_table_descr'),
	array('name' => 'sofc',    'field' => 'table_fc.field_count'),
	array('name' => 'sosp',    'field' => 'gks_custom_table.shortcode_prefix'),
);
$search_fields = array(
'gks_custom_table.custom_table_descr',
);

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




$sql = "SELECT SQL_CALC_FOUND_ROWS gks_custom_table.*, table_fc.field_count
FROM gks_custom_table 
LEFT JOIN (
  SELECT custom_table_id, Count(id_custom_field) AS field_count
  FROM gks_custom_field
  WHERE field_disabled=0
  GROUP BY custom_table_id
) AS table_fc ON gks_custom_table.id_custom_table = table_fc.custom_table_id

";  
//echo '<pre>';echo $sql;die();
$sql.= " where custom_table_disabled=0 ";
if ($GKS_HOTEL_BACKEND==false) {
  $sql.= " and id_custom_table not in (9,10,11,12,13,14,15) ";
}
if ($GKS_CRM_ENABLE==false) {
  $sql.= " and id_custom_table not in (6,26,27) ";
}
if ($GKS_ORDERS_ENABLE==false) {
  $sql.= " and id_custom_table not in (16) ";
  if ($GKS_ORDERS_OCCASION==false) {
    
  }
}
if ($GKS_ORDERS_PRODUCTION==false) {
  $sql.= " and id_custom_table not in (18,19) ";
}
if ($GKS_ACC_ENABLE==false) {
  $sql.= " and id_custom_table not in (1,2,3,23) ";
}
if ($GKS_WARE_HOUSE_ENABLE==false) {
  $sql.= " and id_custom_table not in (28) ";
}


$sql.=$where . $search_where;

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY custom_sortorder";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-custom-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου αντικειμένου');?></a>
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
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr >	
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><a href="?">#</a></th>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="45%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Αντικείμενο')); ?></th>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofc', gks_lang('Πεδία')); ?></th>
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="35%"><?php echo gks_lang('Προβολή');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosp', '<span class="tooltipster" title="'.gks_lang('Πρόθεμα σύντομου συνδέσμου για δημόσια χρήση αρχείων').'">'.gks_lang('ΠΣΣΔΧ').'</span>'); ?></th>            
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-custom-item.php?id=<?php echo $row['id_custom_table'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_custom_table'];?></td>
          <td><?php if ($row['id_custom_table']>=10000) {?>
            <i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_custom_table'];?>" data-model="gks_custom_table"></i>
            <?php } ?>
          </td>
        </tr>      
      </table>
    </td>
    
    <td        class="mytdcml"><?php echo $row['custom_table_descr'];?></td>
    <td        class="mytdcm"><?php echo $row['field_count'];?></td>
    
    <td        class="mytdcml"><?php 
      if (!empty($row['obj_url'])) {
        echo '<a class="btn btn-sm button_custom_table_view" href="'.$row['obj_url'].'">'.gks_lang('Προβολή').'</a>';
      } 
    ?></td>
    <td        class="mytdcm"><?php echo $row['shortcode_prefix'];?></td>
    
    
    
    
    
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_custom_table','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_custom_table','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_custom_table','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


