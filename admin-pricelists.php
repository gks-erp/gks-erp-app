<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Τιμοκατάλογοι');
$nav_active_array=array('manage','manage_pricelist');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_pricelist','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



$perm_item_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist_items','view',0);
$perm_item_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist_items','edit',0);
$perm_item_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist_items','add',0);
$perm_item_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist_items','delete',0);

$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_pricelist',['from'=>'list']);


$filters = array();
$filters[] = array(
    'name' => 'ftype',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_pricelist.price_is_xondriki = '%V%'",
    'vals' => array(
        array('value' => 100, 'text' => gks_lang('Λιανικής'),     'sql' => "gks_eshop_pricelist.price_is_xondriki=0"),
        array('value' => 101, 'text' => gks_lang('Χονδρικής'),    'sql' => "gks_eshop_pricelist.price_is_xondriki=1"),
        array('value' => 102, 'text' => gks_lang('ΥπερΧονδρικής'),'sql' => "gks_eshop_pricelist.price_is_xondriki=2"),
        array('value' => 103, 'text' => gks_lang('Αγοράς'),       'sql' => "gks_eshop_pricelist.price_is_xondriki=3"),
    ),
);
$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_pricelist.pricelist_disable = '%V%'",
    'vals' => array(
        array('value' => 100, 'text' => gks_lang('Ενεργό'),      'sql' => "gks_eshop_pricelist.pricelist_disable=0"),
        array('value' => 101, 'text' => gks_lang('Μη ενεργό'),   'sql' => "gks_eshop_pricelist.pricelist_disable<>0"),
    ),
);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_eshop_pricelist.id_pricelist'),
  						array('name' => 'sodescr', 'field' => 'gks_eshop_pricelist.pricelist_descr'),
  						array('name' => 'sotype', 'field' => 'gks_eshop_pricelist.price_is_xondriki'),
  						array('name' => 'sodisable', 'field' => 'gks_eshop_pricelist.pricelist_disable'),
  						array('name' => 'soso', 'field' => 'gks_eshop_pricelist.sortorder'),
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

            
$search_fields = array(
'pricelist_descr',

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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_eshop_pricelist.* 
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." gks_eshop_pricelist 
".$gks_custom_prepare['sql_all_list_left']."
where 1=1 ".$where . $search_where;


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_eshop_pricelist.sortorder, id_pricelist";
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
<style class="gks_customtableview_style" data-index="2">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,2);?>
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-pricelists-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου τιμοκαταλόγου');?></a>
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings,1,'.gkstable > thead > tr > th',gks_lang('Τιμοκατάλογος'));?>
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings,2,'.gkssubtable:first > thead > tr > th',gks_lang('Στοιχείο τιμοκαταλόγου'));?>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_eshop_pricelist">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Τιμοκατάλογος')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="1%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργός')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosotorder', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>
<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>
  </tr>
</thead>
<tbody> 
<?php 
$i=0;
while ($row = $result->fetch_assoc()) {

	$i++;
?>

  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_pricelist'];?>">
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-pricelists-item.php?id=<?php echo $row['id_pricelist'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_pricelist'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_pricelist'];?>" data-model="gks_eshop_pricelist"></i></td>
        </tr>      
      </table>
    </td>
    <td nowrap class="mytdcml"><?php echo $row["pricelist_descr"];?></td>
    <td class="mytdcm"><?php 
      if ($row['price_is_xondriki']==0) echo gks_lang('Λιανικής');
      else if ($row['price_is_xondriki']==1) echo gks_lang('Χονδρικής');
      else if ($row['price_is_xondriki']==2) echo gks_lang('ΥπερΧονδρικής');
      else if ($row['price_is_xondriki']==3) echo gks_lang('Αγοράς');
      ?></td>
    <td class="mytdcm"><?php echo myimg010r($row['pricelist_disable']);?></td>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['sortorder'];?></span>
    </td>    
<?php
  echo gks_custom_table_list_rows($gks_custom_prepare,$row);
?>     
  </tr>
  
<?php 

} ?>

</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_pricelist','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  $('#table_eshop_pricelist > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_eshop_pricelist',mylist,'#table_eshop_pricelist > tbody');
    }
  });

});

 
 
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>
  
<?php

include_once('_my_footer_admin.php');

