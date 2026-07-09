<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Μονάδες Μέτρησης');
$nav_active_array=array('manage','manage_monades');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_monades_metrisis','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_monades_metrisis','edit',0);






$filters = array();
$filters[] = array(
    'name' => 'fparent',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Βασική μονάδα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_monades_metrisis.monada_parent_id = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Βασική μονάδα'),          'sql' => "gks_monades_metrisis.monada_parent_id=0"),
        array('value' => 2, 'text' => gks_lang('Είναι μετατροπή'),        'sql' => "gks_monades_metrisis.monada_parent_id<>0"),
    ),
);

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_monades_metrisis.id_monada'),
  						array('name' => 'sodescr', 'field' => 'gks_monades_metrisis.monada_descr'),
  						array('name' => 'sosymbol', 'field' => 'gks_monades_metrisis.monada_symbol'),
  						array('name' => 'sosort', 'field' => 'gks_monades_metrisis.monada_sortorder'),
  						array('name' => 'sopath', 'field' => 'full_path'), //sortorder_path,full_path
  						array('name' => 'soepi', 'field' => 'gks_monades_metrisis.monada_parent_epi'), 
  						
  						
);

$search_fields = array(
'gks_monades_metrisis.monada_descr',

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

//SELECT SQL_CALC_FOUND_ROWS gks_monades_metrisis.*, 
//other.monada_descr AS other_descr
//FROM gks_monades_metrisis 
//LEFT JOIN gks_monades_metrisis AS other ON gks_monades_metrisis.monada_parent_id = other.id_monada

$sql = "
select SQL_CALC_FOUND_ROWS gks_monades_metrisis.*,
other.monada_descr AS other_descr,
CONCAT_WS('\\\\',
                 ug10.monada_descr,
                 ug9.monada_descr,
                 ug8.monada_descr,
                 ug7.monada_descr,
                 ug6.monada_descr,
                 ug5.monada_descr,
                 ug4.monada_descr,
                 ug3.monada_descr,
                 ug2.monada_descr,
                 gks_monades_metrisis.monada_descr) as full_path,
CONCAT_WS('\\\\',
                 LPAD(ug10.monada_sortorder,5,0),
                 LPAD(ug9.monada_sortorder,5,0),
                 LPAD(ug8.monada_sortorder,5,0),
                 LPAD(ug7.monada_sortorder,5,0),
                 LPAD(ug6.monada_sortorder,5,0),
                 LPAD(ug5.monada_sortorder,5,0),
                 LPAD(ug4.monada_sortorder,5,0),
                 LPAD(ug3.monada_sortorder,5,0),
                 LPAD(ug2.monada_sortorder,5,0),
                 LPAD(gks_monades_metrisis.monada_sortorder,5,0)) as sortorder_path

FROM (((((((((gks_monades_metrisis
LEFT JOIN gks_monades_metrisis AS ug2 ON gks_monades_metrisis.monada_parent_id = ug2.id_monada)
LEFT JOIN gks_monades_metrisis AS ug3 ON ug2.monada_parent_id = ug3.id_monada)
LEFT JOIN gks_monades_metrisis AS ug4 ON ug3.monada_parent_id = ug4.id_monada)
LEFT JOIN gks_monades_metrisis AS ug5 ON ug4.monada_parent_id = ug5.id_monada)
LEFT JOIN gks_monades_metrisis AS ug6 ON ug5.monada_parent_id = ug6.id_monada)
LEFT JOIN gks_monades_metrisis AS ug7 ON ug6.monada_parent_id = ug7.id_monada)
LEFT JOIN gks_monades_metrisis AS ug8 ON ug7.monada_parent_id = ug8.id_monada)
LEFT JOIN gks_monades_metrisis AS ug9 ON ug8.monada_parent_id = ug9.id_monada)
LEFT JOIN gks_monades_metrisis AS ug10 ON ug9.monada_parent_id = ug10.id_monada)

LEFT JOIN gks_monades_metrisis AS other ON gks_monades_metrisis.monada_parent_id = other.id_monada

where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY monada_sortorder,monada_descr";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-monades-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας μονάδας μέτρησης');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_monades_metrisis">
<thead>
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Μονάδα Μέτρησης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosymbol', gks_lang('Σύμβολο')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soepi', gks_lang('Μετατροπή')); ?></th>        
<?php if ($perm_edit) {?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="40%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopath', gks_lang('Διαδρομή Μετατροπής')); ?></th>        
    </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_monada'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-monades-item.php?id=<?php echo $row['id_monada'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_monada'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_monada'];?>" data-model="gks_monades_metrisis"></i></td>
        </tr>      
      </table>
    </td>

    <td       ><?php echo $row['monada_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['monada_symbol'];?></td>
    <td nowrap><?php 
      if ($row['monada_parent_id']>0)  {
        
        echo '= '.$row['monada_parent_epi'].' x '.$row['other_descr'];
      }
    
    ?></td>
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['monada_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['monada_sortorder'];?></span>
    </td>
<?php } ?>
    <td       ><?php echo $row['full_path'];?></td>

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


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_monades_metrisis','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_monades_metrisis','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_monades_metrisis','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#table_gks_monades_metrisis > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_monades_metrisis',mylist,'#table_gks_monades_metrisis > tbody');
    }
  });  

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


