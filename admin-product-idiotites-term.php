<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Όροι Ιδιοτήτων Ειδών');
$nav_active_array=array('manage','manage_menu_product','manage_product_idiotites_terms');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_product_idiotites_terms','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}






$filters = array();

$filters[] = array(
    'name' => 'fidiotita',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ιδιότητα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_product_idiotites_terms.idiotita_id = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => "all",          'sql' => "1=1"),
    ),
    'sql'=> "SELECT gks_product_idiotites.id_product_idiotita AS id, gks_product_idiotites.idiotita_name AS descr
FROM gks_product_idiotites_terms LEFT JOIN gks_product_idiotites ON gks_product_idiotites_terms.idiotita_id = gks_product_idiotites.id_product_idiotita
WHERE (((gks_product_idiotites.id_product_idiotita) Is Not Null))
GROUP BY gks_product_idiotites.id_product_idiotita, gks_product_idiotites.idiotita_name, gks_product_idiotites.idiotita_sortorder
ORDER BY gks_product_idiotites.idiotita_sortorder;"
);

$filters[] = array(
    'name' => 'ftype',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_product_idiotites.idiotita_type = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => "all",          'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Χωρίς τύπο'),            'sql' => "(gks_product_idiotites.idiotita_type='' or gks_product_idiotites.idiotita_type is null)"),
        array('value' => 2, 'text' => getIdiotitaTypeDescr('10button'),  'sql' => "gks_product_idiotites.idiotita_type='10button'"),
        array('value' => 3, 'text' => getIdiotitaTypeDescr('20color'),   'sql' => "gks_product_idiotites.idiotita_type='20color'"),
        array('value' => 4, 'text' => getIdiotitaTypeDescr('30image'),   'sql' => "gks_product_idiotites.idiotita_type='30image'"),
    ),
);

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_product_idiotites_terms.id_product_idiotita_term'),
  						array('name' => 'soname', 'field' => 'gks_product_idiotites_terms.idiotita_term_name'),
  						array('name' => 'sodescr', 'field' => 'gks_product_idiotites_terms.idiotita_term_descr'),
  						array('name' => 'sobutton', 'field' => 'gks_product_idiotites_terms.idiotita_term_button'),
  						array('name' => 'socolor', 'field' => 'gks_product_idiotites_terms.idiotita_term_color'),
  						array('name' => 'soimage', 'field' => 'gks_product_idiotites_terms.idiotita_term_image'),
  						array('name' => 'sosort', 'field' => 'gks_product_idiotites_terms.idiotita_term_sortorder'),
  						array('name' => 'soidiotita', 'field' => 'gks_product_idiotites.idiotita_name'),
  						array('name' => 'sotype', 'field' => 'gks_product_idiotites.idiotita_type'),
  						array('name' => 'soterms_cc', 'field' => 'terms_cc'),
  						array('name' => 'soproducts_cc', 'field' => 'products_cc'),
  						
  						
);

$search_fields = array(
'gks_product_idiotites.idiotita_name',
'gks_product_idiotites.idiotita_descr',
'gks_product_idiotites.idiotita_type',
'gks_product_idiotites_terms.idiotita_term_name',
'gks_product_idiotites_terms.idiotita_term_descr',
'gks_product_idiotites_terms.idiotita_term_color',
'gks_product_idiotites_terms.idiotita_term_button',
'gks_product_idiotites_terms.idiotita_term_image',

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

$sql = "SELECT SQL_CALC_FOUND_ROWS gks_product_idiotites_terms.*, 
gks_product_idiotites.idiotita_name, gks_product_idiotites.idiotita_type,
table_products.products_cc
FROM (gks_product_idiotites_terms 
LEFT JOIN gks_product_idiotites ON gks_product_idiotites_terms.idiotita_id = gks_product_idiotites.id_product_idiotita)
LEFT JOIN (
  select product_idiotita_term_id,count(*) as products_cc from gks_eshop_products_idiotites_terms group by product_idiotita_term_id
) as table_products on gks_product_idiotites_terms.id_product_idiotita_term = table_products.product_idiotita_term_id
where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY idiotita_term_sortorder,idiotita_sortorder,idiotita_name,idiotita_term_name";
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
      <a class="btn btn-primary gks_add_new_record" href="admin-product-idiotites-term-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου όρου');?></a>
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
<table class="table table-sm table-responsive table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_product_idiotites_terms">
<thead>
    <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soname', gks_lang('Όρος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sobutton', gks_lang('Κουμπί')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socolor', gks_lang('Χρώμα')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soimage', gks_lang('Φωτό')); ?></th>        
        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soproducts_cc', gks_lang('Προϊόντα')); ?></th>        
       
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soidiotita', gks_lang('Ιδιότητα')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
        
        
    </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_product_idiotita_term'];?>">
    <th scope="row" nowrap class="mytdcm" style="text-align: center"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-product-idiotites-term-item.php?id=<?php echo $row['id_product_idiotita_term'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_product_idiotita_term'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_product_idiotita_term'];?>" data-model="gks_product_idiotites_terms"></i></td>
        </tr>      
      </table>
    </td>
    <td nowrap><?php echo $row['idiotita_term_name'];?></td>
    <td       ><?php echo $row['idiotita_term_descr'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['idiotita_type']=='10button') echo $row['idiotita_term_button'];?></td>
    <?php
    $bgcolor='';
    if ($row['idiotita_type']=='20color' and !empty($row['idiotita_term_color'])) $bgcolor=$row['idiotita_term_color'];
    ?>
    <td nowrap class="mytdcm" style="<?php if ($bgcolor!='') echo 'background-color:'.$bgcolor.';color:'.color_inverse($bgcolor);?>"><?php //echo $bgcolor;?></td>
    <td nowrap class="mytdcm"><?php 
      if ($row['idiotita_type']=='30image' and empty($row['idiotita_term_image']) == false) 
       echo '<img src="'.$row['idiotita_term_image'].'" style="height:32px;"/>';
     ?></td>

    <td nowrap class="mytdcm"><?php echo $row['products_cc'];?></td>
    <td nowrap class="mytdcm"><a href="admin-product-idiotites-item.php?id=<?php echo $row['idiotita_id'];?>"><?php echo $row['idiotita_name'];?></a></td>
    <td nowrap class="mytdcm"><?php echo getIdiotitaTypeDescr($row['idiotita_type']);?></td>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['idiotita_term_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['idiotita_term_sortorder'];?></span>
    </td>

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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_product_idiotites_terms','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_product_idiotites_terms','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_product_idiotites_terms','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  
  $('#table_gks_product_idiotites_terms > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_product_idiotites_terms',mylist,'#table_gks_product_idiotites_terms > tbody');
    }
  }); 
  



});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


