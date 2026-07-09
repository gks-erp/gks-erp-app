<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Συνταγές');
$nav_active_array=array('production','production_bom');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_bom','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}






$gks_custom_prepare = gks_custom_table_item_prepare('gks_production_bom',['from'=>'list']);
//print '<pre>';print_r($gks_custom_prepare);die();


$filters = array();

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_production_bom.bom_disable = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ενεργή'),      'sql' => "gks_production_bom.bom_disable=0"),
        array('value' => 101, 'text' => gks_lang('Μη ενεργή'),   'sql' => "gks_production_bom.bom_disable<>0"),
    ),
);

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);


$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_production_bom.id_production_bom'),
  						array('name' => 'sophoto', 'field' => 'product_photo_p'),
  						array('name' => 'socode', 'field' => 'gks_eshop_products.product_code'),
  						array('name' => 'soproduct', 'field' => 'product_descr_p'),
  						array('name' => 'somm', 'field' => 'gks_monades_metrisis.monada_descr'),
  						array('name' => 'soquantity', 'field' => 'gks_production_bom.bom_quantity'),
  						array('name' => 'sodescr', 'field' => 'gks_production_bom.bom_descr'),

  						array('name' => 'soref', 'field' => 'gks_production_bom.reference'),
  						array('name' => 'socompany', 'field' => 'gks_company.company_title,gks_company_subs.company_sub_title'),
  						array('name' => 'sodisable', 'field' => 'gks_production_bom.bom_disable'),
  						array('name' => 'soylika', 'field' => 'ylika_count'),
  						array('name' => 'soothercost', 'field' => 'cost_count'),
  						array('name' => 'socost', 'field' => 'gks_production_bom.bom_kostos'),
  						array('name' => 'socostmin', 'field' => 'gks_production_bom.bom_kostos_min'),
  						array('name' => 'socostmax', 'field' => 'gks_production_bom.bom_kostos_max'),


            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'gks_eshop_products.product_code',
'gks_eshop_products_parent.product_code',
'gks_eshop_products.product_descr',
'gks_eshop_products_parent.product_descr',
'gks_eshop_products.product_descr_small',
'gks_eshop_products.product_descr_big',
'gks_eshop_products.product_object_name',
'gks_eshop_products.product_descr_variable',

'gks_production_bom.bom_descr',
'gks_production_bom.reference',

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
 

$sql = "SELECT SQL_CALC_FOUND_ROWS gks_production_bom.*,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
gks_eshop_products.product_code,
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
gks_eshop_products.product_descr_small,
gks_monades_metrisis.monada_descr,
gks_company.company_title,
gks_company_subs.company_sub_title,
ylika_count,cost_count

".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((((((gks_production_bom
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_production_bom.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_production_bom.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_eshop_products ON gks_production_bom.bom_product_id = gks_eshop_products.id_product) 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN gks_monades_metrisis ON gks_production_bom.bom_monada_id = gks_monades_metrisis.id_monada)
LEFT JOIN gks_company ON gks_production_bom.company_id = gks_company.id_company)
LEFT JOIN gks_company_subs ON gks_production_bom.company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN (
  SELECT production_bom_id, Count(*) AS ylika_count
  FROM gks_production_bom_product
  GROUP BY production_bom_id
) as table_ylika on gks_production_bom.id_production_bom = table_ylika.production_bom_id)
LEFT JOIN (
  SELECT production_bom_id, Count(*) AS cost_count
  FROM gks_production_bom_cost
  GROUP BY production_bom_id
) as table_cost on gks_production_bom.id_production_bom = table_cost.production_bom_id

where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_production_bom.id_production_bom desc";
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
#table_gks_production_bom > tbody > tr > .tdimg {
  padding:0px;
}  
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-production-bom-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας συνταγής');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_production_bom">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophoto', gks_lang('Φωτό')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soproduct', gks_lang('Είδος')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somm', gks_lang('Μονάδα<br>Μέτρησης')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soquantity', gks_lang('Ποσότητα')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soref', gks_lang('Αναφορά')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soylika', gks_lang('Υλικά')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soothercost', gks_lang('Άλλα<br>Κόστη')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'socost', gks_lang('Κόστος')); 
          echo '<br>';
          echo makeSortLink($sortable, $sortable_url, $_GET, 'socostmin', '<span class="tooltipster" title="'.gks_lang('Ελάχιστο').'">'.gks_lang('Ελ').'</span>').' &#8767; '; 
          echo makeSortLink($sortable, $sortable_url, $_GET, 'socostmax', '<span class="tooltipster" title="'.gks_lang('Μέγιστο').'">'.gks_lang('Με').'</span>'); 
        ?></th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργή')); ?></th> 

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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap align="right"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-production-bom-item.php?id=<?php echo $row['id_production_bom'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_production_bom'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_production_bom'];?>" data-model="gks_production_bom"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcm tdimg"><?php 
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
      echo '<a class="lightgalleryitem_bom" href="'.$photo_url.'" data-sub-html="'.$row['product_code'].'"><img style="max-width:64px;max-height:64px;" src="'.$myimgurl.'"></a>';
    }
    ?></td>


    <td class="mytdcml" nowrap><?php echo $row['product_code'];?></td>
    <td class="mytdcml"><a href="admin-products-item.php?id=<?php echo $row['bom_product_id'];?>"><?php echo $row['product_descr_p'];?></a></td>
    <td class="mytdcml"><?php echo $row['monada_descr'];?></td>
    <td class="mytdcm" nowrap><?php echo $row['bom_quantity'];?></td>
    <td class="mytdcml"><?php echo $row['bom_descr'];?></td>
    <td class="mytdcml"><?php echo $row['reference'];?></td>
    
    <td nowrap class="mytdcm"><?php echo $row['ylika_count'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['cost_count'];?></td>
    <td nowrap class="mytdcm" ><?php
      $temp1='';
      if ($row['bom_kostos']!=0) $temp1= myCurrencyFormat($row['bom_kostos']);
      $temp2='';
      if ($row['bom_kostos_min']!=0 or $row['bom_kostos_max']!=0) {
        if (!($row['bom_kostos_min']==$row['bom_kostos'] and $row['bom_kostos_max']==$row['bom_kostos'])) {
          if ($row['bom_kostos_min']!=0) $temp2=myCurrencyFormat($row['bom_kostos_min']);
          if ($row['bom_kostos_max']!=0) $temp2.= ' &#8767; '.myCurrencyFormat($row['bom_kostos_max']);
          if ($temp1!='' and $temp2!='') $temp2='<br>'.$temp2;
        }
      }
      echo $temp1.$temp2;
      
    ?></td>
        
    <td class="mytdcml"><?php echo $row['company_title']; if (isset($row['company_sub_title'])) echo '<br>'.$row['company_sub_title'];?></td> 
    
    
    <td class="mytdcm" nowrap><?php echo myimg010r($row['bom_disable']);?></td>


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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_bom','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_bom','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_bom','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        $('#filter-form').submit();
      }
  });  


  $("#table_gks_production_bom").lightGallery({
  	selector: '.lightgalleryitem_bom',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });
  

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


