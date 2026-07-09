<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Πατρίδες - Serial Numbers');
$nav_active_array=array('warehouse','product_lots');

db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_product_lots','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_eshop_product_lots','edit',0);




$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_product_lots',['from'=>'list']);




$filters = array();

$filters[] = array(
    'name' => 'flot',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Παρτίδα/Serial'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_product.lot_disabled = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Παρτίδα'),       'sql' => "gks_eshop_products.product_lot_serial='lot'"),
        array('value' => 2, 'text' => gks_lang('Serial Number'), 'sql' => "gks_eshop_products.product_lot_serial='serial'"),
    ),
);


$filters[] = array(
  'name' => 'fdpro',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία Παραγωγής'),
  'has_custom_date' => true,
  'field' => 'gks_eshop_product_lots.lot_date_production', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_eshop_product_lots.lot_date_production','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

$filters[] = array(
	'name' => 'fcout',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
	'title' => gks_lang('Ημερομηνία Λήξης'),
	'has_custom_date' => true,
	'field' => 'gks_eshop_product_lots.lot_date_expire',
  'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_eshop_product_lots.lot_date_expire','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia]),
);


$filters[] = array(
  'name' => 'fjournal',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Είδος'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_eshop_product_lots.lotproduct_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.user_id=0"),
      //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.user_id<>0"),
  ),
  'sql' => "
SELECT gks_eshop_product_lots.lotproduct_id as id,
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
FROM (gks_eshop_product_lots 
LEFT JOIN gks_eshop_products ON gks_eshop_product_lots.lotproduct_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
where gks_eshop_product_lots.lotproduct_id>0
GROUP BY gks_eshop_product_lots.lotproduct_id
",    
);


$filters[] = array(
    'name' => 'fenable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_product_lots.lot_disabled = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),   'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργή'),    'sql' => "gks_eshop_product_lots.lot_disabled=0"),
        array('value' => 2, 'text' => gks_lang('Μη Ενεργή'), 'sql' => "gks_eshop_product_lots.lot_disabled<>0"),
    ),
);

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  array('name' => 'soid', 'field' => 'gks_eshop_product_lots.id_lot_product'),
  array('name' => 'solotsn', 'field' => 'gks_eshop_products.product_lot_serial'),
  array('name' => 'soname', 'field' => 'gks_eshop_product_lots.lot_name'),
  array('name' => 'sodescr', 'field' => 'gks_eshop_product_lots.lot_descr'),
  array('name' => 'sodpro', 'field' => 'gks_eshop_product_lots.lot_date_production'),
  array('name' => 'sodexpi', 'field' => 'gks_eshop_product_lots.lot_date_expire'),
  array('name' => 'sophoto', 'field' => 'product_photo_p'),
  array('name' => 'socode', 'field' => 'gks_eshop_products.product_code'),
  array('name' => 'soproduct', 'field' => 'product_descr_p'),
  array('name' => 'sosort', 'field' => 'gks_eshop_product_lots.lot_sortorder'),
  array('name' => 'sodisable', 'field' => 'gks_eshop_product_lots.lot_disabled'),
);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);


$search_fields = array(
'gks_eshop_product_lots.lot_name',
'gks_eshop_product_lots.lot_descr',
'gks_eshop_products.product_code',
'gks_eshop_products.product_descr',
'gks_eshop_products_parent.product_descr',
'gks_eshop_products.product_descr_small',
'gks_eshop_products.product_descr_big',
'gks_eshop_products.product_object_name',
'gks_eshop_products.product_descr_variable',

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

//SELECT SQL_CALC_FOUND_ROWS gks_eshop_product_lots.*, 
//other.lot_descr AS other_descr
//FROM gks_eshop_product_lots 
//LEFT JOIN gks_eshop_product_lots AS other ON gks_eshop_product_lots.monada_parent_id = other.id_lot_product

$sql = "select SQL_CALC_FOUND_ROWS gks_eshop_product_lots.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_eshop_products.product_code,
gks_eshop_products.product_lot_serial,
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
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_photo<>'' THEN
          gks_eshop_products.product_photo
        ELSE
          gks_eshop_products_parent.product_photo
      END
    ELSE gks_eshop_products.product_photo
  END as product_photo_p 
  
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((gks_eshop_product_lots
".$gks_custom_prepare['sql_all_list_left']."
 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_eshop_product_lots.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_eshop_product_lots.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_eshop_products ON gks_eshop_product_lots.lotproduct_id = gks_eshop_products.id_product)
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product

where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY lot_sortorder, id_lot_product desc";
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
#table_gks_eshop_product_lots > tbody > tr > .tdimg {
  padding:0px;
}  
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-products-lots-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας παρτίδας-serial number');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_eshop_product_lots">
<thead>
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'solotsn', '<span title="'.gks_lang('Παρτίδα').'" class="tooltipster">'.gks_lang('Lot').'</span><br><span title="'.gks_lang('Serial Number').'" class="tooltipster">'.gks_lang('Sn').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Παρτίδα').'<br>'.gks_lang('Serial Number')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodpro', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία Παραγωγής').'">'.gks_lang('Ημερ.<br>Παραγωγής').'</span>'); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodexpi', '<span class="tooltipster" title="'.gks_lang('Ημερομηνία Λήξης').'">'.gks_lang('Ημερ.<br>Λήξης').'</span>'); ?></th> 
               
        <th class="table-dark" scope="col" style="text-align: center !important;display:none1;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophoto', gks_lang('Φωτό')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soproduct', gks_lang('Είδος')); ?></th> 

<?php if ($perm_edit) {?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργή')); ?></th>   
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_lot_product'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-products-lots-item.php?id=<?php echo $row['id_lot_product'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_lot_product'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_lot_product'];?>" data-model="gks_eshop_product_lots"></i></td>
        </tr>      
      </table>
    </td>

    
    <td nowrap class="mytdcm"><?php
      if (trim_gks($row['product_lot_serial'])=='lot') echo 'Lot';
      else if (trim_gks($row['product_lot_serial'])=='serial') echo 'SN';
    ?></td>
    
    <td nowrap class="mytdcml"><?php echo $row['lot_name'];?></td>
    <td class="mytdcml"><?php echo nl2br_gks($row['lot_descr']);?></td>
    <td nowrap class="mytdcm"><?php if (isset($row['lot_date_production'])) echo showDate(strtotime($row['lot_date_production']), 'd/m/Y', 1);?></td>
    <td nowrap class="mytdcm"><?php if (isset($row['lot_date_expire'])) echo showDate(strtotime($row['lot_date_expire']), 'd/m/Y', 1);?></td>
    
    
    
    <td class="mytdcm tdimg" style="display:none1;"> <?php 
    $myimgurl=trim_gks($row['product_photo_p'].'');
    if ($myimgurl == '') {
      $myimgurl="/my/img/product.png";
      echo '<img src="/my/img/product.png" border="0" style="max-width:64px;max-height:64px;"/>';
    } else {
      $mydir = dirname($myimgurl);
      if (endwith($mydir,'/thumbnail')) {
        $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
      } else {
        $photo_url=$myimgurl;
      }
      echo '<a class="lightgalleryitem_lot" href="'.$photo_url.'" data-sub-html="'.$row['product_code'].'"><img style="max-width:64px;max-height:64px;" src="'.$myimgurl.'"></a>';
    }
    ?></td>


    <td class="mytdcml" nowrap><?php echo $row['product_code'];?></td>
    <td class="mytdcml"><a href="admin-products-item.php?id=<?php echo $row['lotproduct_id'];?>"><?php echo $row['product_descr_p'];?></a></td>

    
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['lot_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['lot_sortorder'];?></span>
    </td>
<?php } ?>
    <td class="mytdcm"><img src="img/<?php echo $row['lot_disabled']==0 ? "1" :"0";  ?>.png" border="0" width="16"></td>    

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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_product_lots','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_product_lots','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_product_lots','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fdpro-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdpro-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));


  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'fdpro' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'fdpro' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        $('#filter-form').submit();
      }
  });  

  $('#table_gks_eshop_product_lots > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_eshop_product_lots',mylist,'#table_gks_eshop_product_lots > tbody');
    }
  });  


  $("#table_gks_eshop_product_lots").lightGallery({
  	selector: '.lightgalleryitem_lot',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


