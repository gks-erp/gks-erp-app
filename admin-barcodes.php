<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Barcodes Ειδών');
$nav_active_array=array('manage','manage_menu_product','manage_product_barcodes');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_barcodes','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$filters = array();
$filters[] = array(
    'name' => 'ftype',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_barcodes.barcode_type_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 'has',    'text' => gks_lang('Έχει'),          'sql' => "gks_barcodes.barcode_type_id>0"),
        array('value' => 'notnot', 'text' => gks_lang('Δεν έχει'),      'sql' => "gks_barcodes.barcode_type_id=0"),
    ),
    'sql' => "SELECT gks_barcodes.barcode_type_id AS id, gks_barcodes_types.barcode_type_code AS descr
FROM gks_barcodes 
LEFT JOIN gks_barcodes_types ON gks_barcodes.barcode_type_id = gks_barcodes_types.id_barcode_type
WHERE (((gks_barcodes_types.id_barcode_type) Is Not Null))
GROUP BY gks_barcodes.barcode_type_id, gks_barcodes_types.barcode_type_code, gks_barcodes_types.id_barcode_type
ORDER BY gks_barcodes_types.id_barcode_type;",

);

/*
$filters[] = array(
    'name' => 'fproduct',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Είδος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_barcodes.product_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 'has',    'text' => gks_lang('Έχει'),          'sql' => "gks_barcodes.product_id>0"),
        array('value' => 'notnot', 'text' => gks_lang('Δεν έχει'),      'sql' => "gks_barcodes.product_id=0"),
    ),
    'sql' => "select product_id as id,
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
END as descr
FROM (gks_barcodes
LEFT JOIN gks_eshop_products ON gks_barcodes.product_id = gks_eshop_products.id_product)
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
where gks_eshop_products.id_product is not null
group by product_id,descr
order by descr",
);
*/

$filters[] = array(
    'name' => 'fuser_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Επαφή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_barcodes.user_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 'has',    'text' => gks_lang('Έχει'),          'sql' => "gks_barcodes.user_id>0"),
        array('value' => 'notnot', 'text' => gks_lang('Δεν έχει'),      'sql' => "gks_barcodes.user_id=0"),
    ),
    'sql' => "select user_id as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
FROM gks_barcodes
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_barcodes.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
where ".GKS_WP_TABLE_PREFIX."users.ID is not null
group by ID,gks_nickname
order by gks_nickname",
);

$filters[] = array(
    'name' => 'fdisable_barcode',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργό'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "disable_barcode=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => "gks_barcodes.disable_barcode=0"),
        array('value' => 2, 'text' => gks_lang('Όχι'),          'sql' => "gks_barcodes.disable_barcode=1"),
    ),
    
);




$sortable = array(
	array('name' => 'soid', 'field' => 'gks_barcodes.id_barcode'),
  array('name' => 'sobarcode', 'field' => 'gks_barcodes.barcode'),
  array('name' => 'sodescr', 'field' => 'gks_barcodes.barcode_descr'),
  array('name' => 'sotype', 'field' => 'gks_barcodes_types.barcode_type_descr'),
  array('name' => 'soproduct', 'field' => 'product_descr_p'),
	array('name' => 'souser', 'field' => GKS_WP_TABLE_PREFIX."users.gks_nickname"),
	array('name' => 'soactive', 'field' => 'gks_barcodes.disable_barcode'),
);



$search_fields = array(
'gks_barcodes.barcode',
'gks_barcodes.barcode_descr',
'gks_barcodes_types.barcode_type_code',
'gks_barcodes_types.barcode_type_descr',
'gks_eshop_products.product_descr',
'gks_eshop_products.product_descr_variable',
'gks_eshop_products_parent.product_descr',
GKS_WP_TABLE_PREFIX."users.gks_nickname",
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_barcodes.*,
gks_barcodes_types.barcode_type_code, gks_barcodes_types.barcode_type_descr, 
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
END as product_descr_p,
gks_eshop_products.product_descr, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname
FROM (((gks_barcodes 
LEFT JOIN gks_barcodes_types ON gks_barcodes.barcode_type_id = gks_barcodes_types.id_barcode_type)
LEFT JOIN gks_eshop_products ON gks_barcodes.product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_barcodes.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
where 1=1 ".$where . $search_where;
      


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_barcodes.id_barcode desc";
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
<style>


</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-barcodes-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου Barcode');?></a>
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="20%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobarcode', gks_lang('Barcode')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soproduct', gks_lang('Είδος')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souser', gks_lang('Επαφή')); ?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" ><?php echo gks_lang('Σχόλιο');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soactive', gks_lang('Ενεργό')); ?></th>
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-barcodes-item.php?id=<?php echo $row['id_barcode'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_barcode'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_barcode'];?>" data-model="gks_barcodes"></i></td>
        </tr>      
      </table>
    </td>
        
    <td class="mytdcml" nowrap><?php echo $row['barcode'];?></td>
    <td class="mytdcml" nowrap title="<?php echo $row['barcode_type_descr'];?>"><?php echo $row['barcode_type_code'];?></td>
    <td class="mytdcml"><?php echo $row['barcode_descr'];?></td>
    <td class="mytdcml"><a href="admin-products-item.php?id=<?php echo  $row['product_id'];?>"><?php echo $row['product_descr_p']?></a></td>
    <td class="mytdcml"><?php echo $row['gks_nickname'];?></td>
    <td class="mytdcml"><?php echo nl2br_gks($row['comments']);?></td>
    <td class="mytdcm" nowrap><?php echo myimg010r($row['disable_barcode']);?></td> 


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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_barcodes','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_barcodes','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_barcodes','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php

include_once('_my_footer_admin.php');


