<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title= gks_lang('Κατηγορίες Ειδών');
$nav_active_array=array('manage','manage_menu_product','manage_product_category');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products_categories','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}






$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_products_categories',['from'=>'list']);


$filters = array();

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' =>  gks_lang('Ενεργή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' =>  gks_lang('Ενεργή'),     'sql' => "gks_eshop_products_categories.category_disable = 0"),
        array('value' => 2, 'text' =>  gks_lang('Μη ενεργή'),  'sql' => "gks_eshop_products_categories.category_disable <> 0"),
    ),
);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_eshop_products_categories.id_product_category'),
  						array('name' => 'sodescr', 'field' => 'gks_eshop_products_categories.product_category_descr'),
  						array('name' => 'socount', 'field' => 'ccc'),
  						array('name' => 'soadd', 'field' => 'gks_eshop_products_categories.mydate_add'),
  						array('name' => 'sodisable', 'field' => 'gks_eshop_products_categories.category_disable'),
  						array('name' => 'soergasies', 'field' => 'cc_ergasies'),
  						array('name' => 'sophoto', 'field' => 'gks_eshop_products_categories.category_photo'),
  						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'ug10.product_category_descr',
'ug9.product_category_descr',
'ug8.product_category_descr',
'ug7.product_category_descr',
'ug6.product_category_descr',
'ug5.product_category_descr',
'ug4.product_category_descr',
'ug3.product_category_descr',
'ug2.product_category_descr',
'gks_eshop_products_categories.product_category_descr',
                
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_eshop_products_categories.*, ccproducts.ccc,
ug2.product_category_descr AS gt2, 
ug3.product_category_descr AS gt3, 
ug4.product_category_descr AS gt4, 
ug5.product_category_descr AS gt5,
ug6.product_category_descr AS gt6,
ug7.product_category_descr AS gt7,
ug8.product_category_descr AS gt8,
ug9.product_category_descr AS gt9,
ug10.product_category_descr AS gt10,

ug2.id_product_category AS id2, 
ug3.id_product_category AS id3, 
ug4.id_product_category AS id4, 
ug5.id_product_category AS id5,
ug6.id_product_category AS id6,
ug7.id_product_category AS id7,
ug8.id_product_category AS id8,
ug9.id_product_category AS id9,
ug10.id_product_category AS id10,
CONCAT_WS('\\\\',
                ug10.product_category_descr,
                ug9.product_category_descr,
                ug8.product_category_descr,
                ug7.product_category_descr,
                ug6.product_category_descr,
                ug5.product_category_descr,
                ug4.product_category_descr,
                ug3.product_category_descr,
                ug2.product_category_descr,
                gks_eshop_products_categories.product_category_descr) as fullpath,
CONCAT_WS('\\\\',
                ug10.product_category_descr,
                ug9.product_category_descr,
                ug8.product_category_descr,
                ug7.product_category_descr,
                ug6.product_category_descr,
                ug5.product_category_descr,
                ug4.product_category_descr,
                ug3.product_category_descr,
                ug2.product_category_descr) as dirpath,
table_ergasies.cc_ergasies
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((((((((gks_eshop_products_categories
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN (
  SELECT product_category_id, Count(product_id) AS ccc
  FROM gks_eshop_products_categories_products
  GROUP BY product_category_id
) AS ccproducts ON gks_eshop_products_categories.id_product_category = ccproducts.product_category_id)
LEFT JOIN (
  SELECT cateidos_id, Count(id_production_ergasies_eidoscat) AS cc_ergasies
  FROM gks_production_ergasies_eidoscat
  GROUP BY cateidos_id
) as table_ergasies ON table_ergasies.cateidos_id = gks_eshop_products_categories.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug2  ON gks_eshop_products_categories.product_category_parent_id = ug2.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.product_category_parent_id = ug3.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.product_category_parent_id = ug4.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.product_category_parent_id = ug5.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.product_category_parent_id = ug6.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.product_category_parent_id = ug7.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.product_category_parent_id = ug8.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.product_category_parent_id = ug9.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category


where 1=1 ".$where . $search_where;
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY fullpath";
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
<style>
#lightgallery_user > tbody > tr > td {
  vertical-align: middle;
}  
#lightgallery_user > tbody > tr > th {
  vertical-align: middle;
}  
#lightgallery_user > tbody > tr > .tdimg {
  padding:0px;
}  
</style>
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
      <a class="btn btn-primary gks_add_new_record" href="admin-product-categories-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας κατηγορίας ειδών');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="lightgallery_user">
<thead>
    <tr >	
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><a href="?">#</a></th>
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophoto', gks_lang('Φωτό')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="50%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparent', gks_lang('Πλήρης διαδρομή')); ?></th> 
        <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Κατηγορία')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socount', gks_lang('Πλήθος<br>Προϊόντων')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo gks_lang('Συνολικό<br>Πλήθος<br>Προϊόντων');?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soergasies', gks_lang('Εργασίες')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="30%"><?php echo gks_lang('Σχόλιο');?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soadd', gks_lang('Ημερομηνία<br>Προσθήκης')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργή')); ?></th>        
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
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-product-categories-item.php?id=<?php echo $row['id_product_category'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_product_category'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_product_category'];?>" data-model="gks_eshop_products_categories"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcm p-0"><?php 
    $myimgurl=trim_gks($row['category_photo'].'');
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
      echo '<a class="lightgalleryitem_user" href="'.$photo_url.'" data-sub-html="'.$row['fullpath'].'"><img style="max-width:64px;max-height:64px;" src="'.$myimgurl.'"></a>';
    }
    ?></td>
    
    <td class="mytdcml"><?php echo $row['fullpath'];?></td>
    <td class="mytdcml"><?php echo $row['product_category_descr'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['ccc']>0) echo number_format($row['ccc'], 0, ',', '.');?></td>  
    <td nowrap class="mytdcm"><?php
      $gu_num=0;
      
      $sql="SELECT ug1.id_product_category AS gid1, 
                   ug2.id_product_category AS gid2, 
                   ug3.id_product_category AS gid3, 
                   ug4.id_product_category AS gid4, 
                   ug5.id_product_category AS gid5,
                   ug5.id_product_category AS gid6,
                   ug5.id_product_category AS gid7,
                   ug5.id_product_category AS gid8,
                   ug5.id_product_category AS gid9,
                   ug5.id_product_category AS gid10
                   
      FROM ((((((((gks_eshop_products_categories AS ug1 
      LEFT JOIN gks_eshop_products_categories AS ug2  ON ug1.id_product_category = ug2.product_category_parent_id) 
      LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.id_product_category = ug3.product_category_parent_id) 
      LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.id_product_category = ug4.product_category_parent_id) 
      LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.id_product_category = ug5.product_category_parent_id)
      LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.id_product_category = ug6.product_category_parent_id)
      LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.id_product_category = ug7.product_category_parent_id)
      LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.id_product_category = ug8.product_category_parent_id)
      LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.id_product_category = ug9.product_category_parent_id)
      LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.id_product_category = ug10.product_category_parent_id
      where ug1.id_product_category=".$row['id_product_category'];
      $result_gu = $db_link->query($sql);        
      if (!$result_gu) {
        debug_mail(false,'error sql',$sql);
        die('sql error');
      }
      $gu_in='';
      
      while ($row_gu = $result_gu->fetch_assoc()) {
        if (isset($row_gu['gid1']))  $gu_in.=$row_gu['gid1'].',';
        if (isset($row_gu['gid2']))  $gu_in.=$row_gu['gid2'].',';
        if (isset($row_gu['gid3']))  $gu_in.=$row_gu['gid3'].',';
        if (isset($row_gu['gid4']))  $gu_in.=$row_gu['gid4'].',';
        if (isset($row_gu['gid5']))  $gu_in.=$row_gu['gid5'].',';
        if (isset($row_gu['gid6']))  $gu_in.=$row_gu['gid6'].',';
        if (isset($row_gu['gid7']))  $gu_in.=$row_gu['gid7'].',';
        if (isset($row_gu['gid8']))  $gu_in.=$row_gu['gid8'].',';
        if (isset($row_gu['gid9']))  $gu_in.=$row_gu['gid9'].',';
        if (isset($row_gu['gid10'])) $gu_in.=$row_gu['gid10'].',';
      }
      if (strlen($gu_in)>0) $gu_in=substr($gu_in, 0, strlen($gu_in)-1);
      if (strlen($gu_in)>0) {
        $sql="SELECT count(Distinct product_id) as ccc2 FROM gks_eshop_products_categories_products WHERE product_category_id In (".$gu_in.")";
        $result_gu = $db_link->query($sql);        
        if (!$result_gu) {
          debug_mail(false,'error sql',$sql);
          die('sql error');
        }
        $row_gu = $result_gu->fetch_assoc();
        $gu_num = $row_gu['ccc2'];
      }
      if ($gu_num>0) echo number_format($gu_num, 0, ',', '.');
    ?></td>
    
    <td nowrap class="mytdcm"><?php if ($row['cc_ergasies']!=0) echo number_format($row['cc_ergasies'], 0, ',', '.');?></td>  
    <td class="mytdcml"><?php echo nl2br_gks($row['category_comments']);?></td>
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
    <td class="mytdcm"><?php echo myimg010r($row['category_disable']);?></td>
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products_categories','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products_categories','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_eshop_products_categories','delete',0);?>;

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

  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
  }); 


});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


