<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Ιστορικό είδους σε αποθήκη');
$nav_active_array=array('warehouse','warehouse_history_product');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov_balance_history','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



$fwarehouse_id=0; if (isset($_GET['fwarehouse_id'])) $fwarehouse_id=intval($_GET['fwarehouse_id']);
$fproduct_id=0; if (isset($_GET['fproduct_id'])) $fproduct_id=intval($_GET['fproduct_id']);
$is_one_and_one=false;
if ($fwarehouse_id>0 and $fproduct_id>0 and isset($_GET['fwarehouse_id']) and isset($_GET['fproduct_id'])) {
  if (trim_gks($fwarehouse_id.'')==trim_gks($_GET['fwarehouse_id']) and trim_gks($fproduct_id.'')==trim_gks($_GET['fproduct_id'])) {
    $is_one_and_one=true;
  }
}

$filters = array();

$filters[] = array(
    'name' => 'fproduct_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Είδος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "myutable.product_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ptable.product_id AS id, 
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
    FROM ( (
      select product_id from (
        select product_id from gks_whi_mov_products where p_mov_state in ('080listing','090ekdosi','100payment') group by product_id
        union 
        select product_id from gks_acc_inv_products where p_inv_state in ('080listing','090ekdosi','100payment') group by product_id
        union 
        select product_id from gks_orders_products where p_order_state in ('060registered','070inproduction','090indelivery','095execute','100completed','110payment') group by product_id
        union 
        select spbom_product_id from gks_production_sintagi_product where sp_order_state in ('070inproduction','090indelivery','095execute','100completed','110payment') group by spbom_product_id
      ) as pproduct
      group by product_id 
    ) as ptable 
    LEFT JOIN gks_eshop_products ON ptable.product_id = gks_eshop_products.id_product)
    LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
    ORDER BY gks_eshop_products.product_descr",
);

$filters[] = array(
    'name' => 'fwarehouse_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποθήκη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "(myutable.warehouses_id_from=%V% or myutable.warehouses_id_to=%V%)",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT id_warehouse as id , warehouse_name as descr
    FROM gks_warehouses
    WHERE is_virtual=0
    ORDER BY gks_warehouses.warehouse_sortorder, gks_warehouses.warehouse_name",
);


$sortable = array(
  						array('name' => 'soid', 'field' => 'myutable.id_rec'),
  						array('name' => 'soproduct', 'field' => 'product_descr_p'),
  						array('name' => 'sowfrom', 'field' => 'gks_warehouses_from.warehouse_name'),
  						array('name' => 'soquantity', 'field' => 'myutable.product_quantity'),
  						array('name' => 'soqfrom', 'field' => '[[ordercustom1]]'),
  						//array('name' => 'sobalance', 'field' => 'myutable.product_quantity'),
  						array('name' => 'soqto', 'field' =>   '[[ordercustom2]]'),
  						
  						
  						array('name' => 'sowto', 'field' => 'gks_warehouses_to.warehouse_name'),
  						array('name' => 'somonada', 'field' => 'gks_monades_metrisis.monada_symbol'),
  						array('name' => 'sopid', 'field' => 'myutable.id_o_rec'),
  						array('name' => 'sopstate', 'field' => 'myutable.state_rec'),
  						array('name' => 'sopdate', 'field' => 'myutable.date_rec'),
  						array('name' => 'sopuedit', 'field' => GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname'),
  						array('name' => 'sopjournal', 'field' => 'gks_acc_journal.acc_journal_descr'),
  						array('name' => 'sopseira', 'field' => 'gks_acc_seires.seira_code'),
  						array('name' => 'sopnumber', 'field' => 'myutable.number_int_rec'),
  						
  						
            );

$search_fields = array(
'gks_eshop_products.product_descr',
'gks_eshop_products.product_descr_variable',
'gks_eshop_products_parent.product_descr',
'gks_warehouses_from.warehouse_name',
'gks_warehouses_to.warehouse_name',
'gks_monades_metrisis.monada_symbol',
'gks_monades_metrisis.monada_descr',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'gks_acc_journal.acc_journal_descr',
'gks_acc_seires.seira_code',

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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_whi_mov_products.*, 
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
  
gks_whi_mov.user_id_edit, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
gks_whi_mov.mov_date, gks_whi_mov.mov_whi_ekdosi_date, gks_whi_mov.mov_state, gks_whi_mov.mov_whi_number_int,
gks_whi_mov.warehouses_id_from, gks_warehouses_from.warehouse_name AS warehouse_name_from, gks_warehouses_from.is_virtual as is_virtual_from,
gks_whi_mov.warehouses_id_to,   gks_warehouses_to.warehouse_name AS warehouse_name_to,     gks_warehouses_to.is_virtual as is_virtual_to,
gks_monades_metrisis.monada_descr, gks_monades_metrisis.monada_symbol,
gks_acc_journal.acc_journal_descr,gks_acc_seires.seira_code 
FROM ((((((((gks_whi_mov_products 
LEFT JOIN gks_whi_mov ON gks_whi_mov_products.whi_mov_id = gks_whi_mov.id_whi_mov) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_whi_mov.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN gks_warehouses AS gks_warehouses_from ON gks_whi_mov.warehouses_id_from = gks_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_warehouses_to ON gks_whi_mov.warehouses_id_to = gks_warehouses_to.id_warehouse) 
LEFT JOIN gks_monades_metrisis ON gks_whi_mov_products.product_monada_id = gks_monades_metrisis.id_monada)
LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal)
LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_eshop_products ON gks_eshop_products.id_product = gks_whi_mov_products.product_id)
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product


where 1=1 ".$where . $search_where;

$sql = "SELECT SQL_CALC_FOUND_ROWS myutable.*, 
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_warehouses_from.warehouse_name AS warehouse_name_from, gks_warehouses_from.is_virtual as is_virtual_from,
gks_warehouses_to.warehouse_name AS warehouse_name_to,     gks_warehouses_to.is_virtual as is_virtual_to,
gks_monades_metrisis.monada_descr, gks_monades_metrisis.monada_symbol,
gks_acc_journal.acc_journal_descr,gks_acc_seires.seira_code,
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
END as product_descr_p
FROM ((((((( (
  select 'whi_mov' as from_rec, id_whi_mov_product as id_rec,
  gks_whi_mov_products.mydate_add,gks_whi_mov_products.mydate_edit,gks_whi_mov_products.user_id_add,gks_whi_mov_products.user_id_edit,gks_whi_mov_products.myip,
  product_quantity,product_id,product_monada_id, monada_convert_epi_rev,
  whi_mov_id as id_o_rec, whi_mov_id,0 as acc_inv_id,0 as order_id,
  mov_date as date_rec,mov_whi_number_int as number_int_rec,mov_state as state_rec,
  warehouses_id_from,warehouses_id_to,
  mov_whi_journal_id as journal_id_rec,
  mov_whi_seira_id as seira_id_rec,
  seira_is_reverse_delivery_note
  from (gks_whi_mov_products
  LEFT JOIN gks_whi_mov ON gks_whi_mov_products.whi_mov_id = gks_whi_mov.id_whi_mov)
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira
  where gks_whi_mov.id_whi_mov is not null and p_mov_state in ('080listing','090ekdosi','100payment')
  UNION 
  
  select 'acc_inv' as from_rec, id_acc_inv_product as id_rec,
  gks_acc_inv_products.mydate_add,gks_acc_inv_products.mydate_edit,gks_acc_inv_products.user_id_add,gks_acc_inv_products.user_id_edit,gks_acc_inv_products.myip,
  product_quantity,product_id,product_monada_id, monada_convert_epi_rev,
  acc_inv_id as id_o_rec, 0 as whi_mov_id, acc_inv_id,0 as order_id,
  inv_date as date_rec,inv_acc_number_int as number_int_rec,inv_state as state_rec,
  warehouses_id_from,warehouses_id_to,
  inv_acc_journal_id as journal_id_rec,
  inv_acc_seira_id as seira_id_rec,
  0 as seira_is_reverse_delivery_note
  from gks_acc_inv_products
  LEFT JOIN gks_acc_inv ON gks_acc_inv_products.acc_inv_id = gks_acc_inv.id_acc_inv
  where gks_acc_inv.id_acc_inv is not null and p_inv_state in ('080listing','090ekdosi','100payment')
  
  UNION
  select 'order' as from_rec, id_order_product as id_rec,
  gks_orders_products.mydate_add,gks_orders_products.mydate_edit,gks_orders_products.user_id_add,gks_orders_products.user_id_edit,gks_orders_products.myip,
  product_quantity,product_id,product_monada_id, monada_convert_epi_rev,
  order_id as id_o_rec, 0 as whi_mov_id, 0 as acc_inv_id, order_id,
  order_date as date_rec,order_number_int as number_int_rec,order_state as state_rec,
  warehouses_id_from,warehouses_id_to,
  order_journal_id as journal_id_rec,
  order_seira_id as seira_id_rec,
  0 as seira_is_reverse_delivery_note
  from gks_orders_products
  LEFT JOIN gks_orders ON gks_orders_products.order_id = gks_orders.id_order
  where gks_orders.id_order is not null and p_order_state in ('060registered','070inproduction','090indelivery','095execute','100completed','110payment')  
  
  UNION
  SELECT 'production' AS from_rec, id_production_sintagi_product AS id_rec, 
  gks_production_sintagi_product.mydate_add, gks_production_sintagi_product.mydate_edit, gks_production_sintagi_product.user_id_add, gks_production_sintagi_product.user_id_edit, gks_production_sintagi_product.myip, 
  spbom_quantity as product_quantity, spbom_product_id as product_id, spbom_monada_id as product_monada_id, monada_convert_epi_rev, 
  order_id AS id_o_rec, 0 AS whi_mov_id, 0 AS acc_inv_id, order_id, 
  order_date AS date_rec, order_number_int AS number_int_rec, order_state AS state_rec, 
  sp_warehouses_id_from as warehouses_id_from, 0 as warehouses_id_to, 
  order_journal_id AS journal_id_rec, 
  order_seira_id AS seira_id_rec,
  0 as seira_is_reverse_delivery_note
  FROM gks_production_sintagi_product 
  LEFT JOIN gks_orders ON gks_production_sintagi_product.order_id = gks_orders.id_order
  WHERE gks_orders.id_order Is Not Null AND sp_order_state In ('070inproduction','090indelivery','095execute','100completed','110payment')

  
  
  
)  AS myutable 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON myutable.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN gks_warehouses AS gks_warehouses_from ON myutable.warehouses_id_from = gks_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_warehouses_to ON myutable.warehouses_id_to = gks_warehouses_to.id_warehouse)
LEFT JOIN gks_monades_metrisis ON myutable.product_monada_id = gks_monades_metrisis.id_monada)
LEFT JOIN gks_acc_journal ON myutable.journal_id_rec = gks_acc_journal.id_acc_journal)
LEFT JOIN gks_acc_seires ON myutable.seira_id_rec = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_eshop_products ON gks_eshop_products.id_product = myutable.product_id)
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product

where 1=1 ".$where . $search_where;


//echo '<pre>';echo $where;die();   

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY myutable.date_rec desc";
} else {
	$sql .= " ORDER BY "; 
	$sorted_sql=$sorted['sql'];
	$sorted_sql=str_replace('[[ordercustom1]]', '(if (warehouses_id_from='.$fwarehouse_id.',null,myutable.product_quantity))', $sorted_sql);
	$sorted_sql=str_replace('[[ordercustom2]]', '(if (warehouses_id_to='.$fwarehouse_id.',null,myutable.product_quantity))', $sorted_sql);
	$sql .=$sorted_sql;
	
	
	
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
.splittd {
  border-left: 2px solid gray !important;
}
  
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings);?>
    </div>
  </div>
</div>






<?php if ($is_one_and_one) {
  $sql_one="select gks_eshop_products.product_code,
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
  gks_monades_metrisis.monada_symbol,gks_monades_metrisis.monada_descr
  FROM (gks_eshop_products
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
  LEFT JOIN gks_monades_metrisis ON gks_monades_metrisis.id_monada = gks_eshop_products.product_monada_id
  
  where gks_eshop_products.id_product=".$fproduct_id;
  $result_one = $db_link->query($sql_one);        
  if (!$result_one) debug_mail(false,'error sql',$sql_one);
  if (!$result_one) die('sql error');
  $fproduct_title='';
  $photo_html='';
  $monada_symbol='';
  $monada_descr='';
  if ($result_one->num_rows==1) {
    $row_one = $result_one->fetch_assoc();
    $fproduct_title=$row_one['product_descr_p'];
    $monada_symbol=$row_one['monada_symbol'];
    $monada_descr=$row_one['monada_descr'];

    $myimgurl=trim_gks($row_one['product_photo_p'].'');
    if ($myimgurl != '') {
      $mydir = dirname($myimgurl);
      if (endwith($mydir,'/thumbnail')) {
        $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
      } else {
        $photo_url=$myimgurl;
      }
      $photo_html='<a class="lightgalleryitem gks_photo_link" tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$row_one['product_code'].'"><img style="max-height: 64px;" src="'.$myimgurl.'"></a>';
    }

    
  }
  
  
  $sql_one="select warehouse_name from gks_warehouses where id_warehouse=".$fwarehouse_id;
  $result_one = $db_link->query($sql_one);        
  if (!$result_one) debug_mail(false,'error sql',$sql_one);
  if (!$result_one) die('sql error');
  $fwarehouse_title='';
  if ($result_one->num_rows==1) {
    $row_one = $result_one->fetch_assoc();
    $fwarehouse_title=$row_one['warehouse_name'];
  }
  
  $sql_one="select balance 
  from gks_warehouse_balance_eidi
  where product_id=".$fproduct_id."
  and warehouse_id=".$fwarehouse_id;
  $result_one = $db_link->query($sql_one);        
  if (!$result_one) debug_mail(false,'error sql',$sql_one);
  if (!$result_one) die('sql error');
  $curr_balance=false;
  if ($result_one->num_rows==1) {
    $row_one = $result_one->fetch_assoc();
    $curr_balance=floatval($row_one['balance']);
  }
  

  
  
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Είδος σε αποθήκη');?>
        </div>
        <div class="card-body">
          <?php if ($photo_html!='') {?>
          <div class="form-group row" id="lightgallery">
            <div class="col-sm-6 text-sm-right"><span style="line-height: 64px;"><?php echo gks_lang('Φωτό');?>:</span></div>
            <div class="col-sm-6 text-sm-left" style="font-weight: bold;"><?php echo $photo_html;?></div>
          </div>
          <?php } ?>
          <div class="form-group row">
            <div class="col-sm-6 text-sm-right"><?php echo gks_lang('Είδος');?>:</div>
            <div class="col-sm-6 text-sm-left" style="font-weight: bold;"><a href="admin-products-item.php?id=<?php echo $fproduct_id;?>"><?php echo $fproduct_title;?></a></div>
          </div>
          <div class="form-group row">
            <div class="col-sm-6 text-sm-right"><?php echo gks_lang('Αποθήκη');?>:</div>
            <div class="col-sm-6 text-sm-left" style="font-weight: bold;"><?php echo $fwarehouse_title;?></div>
          </div>
          <div class="form-group row">
            <div class="col-sm-6 text-sm-right"><?php echo gks_lang('Τρέχον υπόλοιπο');?>:</div>
            <div class="col-sm-6 text-sm-left"><?php 
              if (is_float($curr_balance)) {
                echo '<span style="font-weight: bold;">'.myNumberFormatNo0Local($curr_balance).'</span>';
                echo ' '.$monada_descr;
                if ($monada_symbol!='') echo ' ('.$monada_symbol.')'; 
              }
             ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } else {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">

      <div class="alert alert-primary" role="alert" style="text-align:center">
        <?php echo gks_lang('Εάν επιλέξετε <b>μία</b> αποθήκη και <b>ένα</b> είδος θα δείτε την διαμόρφωση του υπολοίπου');?>
      </div>
    </div>
  </div>
</div>

<?php } ?>


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
<div class="container-fluid" style="margin:10px 0px;">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <span class="table-dark" style="padding:4px 10px;border-radius:10px;"><?php echo gks_lang('Κίνηση');?></span>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <span class="table-dark" style="padding:4px 10px;border-radius:10px;"><?php echo gks_lang('Αντικείμενο');?></span>
    </div>
  </div>
</div>

<table class="table table-sm table-responsive1 table-striped table-bordered gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
<!--  
  <tr >	
    <th nowrap class="table-dark"         scope="col" style="text-align: center !important;" width="<?php echo ($is_one_and_one ? 50 : 60);?>%" colspan="<?php echo ($is_one_and_one ? 8 : 7);?>"><?php echo gks_lang('Κίνηση');?></th>
    <th nowrap class="table-dark splittd" scope="col" style="text-align: center !important;" width="<?php echo ($is_one_and_one ? 50 : 40);?>%" colspan="8"><?php echo gks_lang('Αντικείμενο');?></th>
  </tr>
-->  
  <tr >	
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
<?php if ($is_one_and_one==false) {?>
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo ($is_one_and_one ? 25 : 20);?>%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soproduct', gks_lang('Είδος')); ?></th>        
<?php } ?>

    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo ($is_one_and_one ? 25 : 20);?>%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowfrom', gks_lang('Από')); ?></th> 

<?php if ($is_one_and_one==false) {?>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soquantity', gks_lang('Ποσότητα')); ?></th>        
<?php } else { ?>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soqfrom', gks_lang('Ποσότητα')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Υπόλοιπο');?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soqto', gks_lang('Ποσότητα')); ?></th>        
<?php } ?>

    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo ($is_one_and_one ? 25 : 20);?>%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowto', gks_lang('Προς')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somonada', gks_lang('Μονάδα')); ?></th>        

    <th nowrap class="table-dark splittd" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopid', '<span class="tooltipster" title="'.gks_lang('Προβολή').'">'.gks_lang('Π','part2').'</span>'); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center   !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopid', 'ID'); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopstate', gks_lang('Κατάσταση')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="<?php echo ($is_one_and_one ? 25 : 20);?>%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopdate', gks_lang('Ημερομηνία')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopuedit', gks_lang('Χρήστης')); ?></th>    
    <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="<?php echo ($is_one_and_one ? 25 : 20);?>%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopjournal', gks_lang('Ημερολόγιο')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopseira', gks_lang('Σειρά')); ?></th>        
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopnumber', gks_lang('Αριθμός')); ?></th>        

  
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap align="right" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap align="center" class="mytdcm"><?php echo $row['id_rec'];?></td>
<?php if ($is_one_and_one==false) {?>
    <td nowrap class="mytdcml"><a href="admin-products-item.php?id=<?php echo $row['product_id'];?>"><?php echo $row['product_descr_p'];?></a></td>
<?php } ?>    
    
    <td nowrap class="mytdcml"><?php
      if ($row['seira_is_reverse_delivery_note']==0) {
        if ($row['is_virtual_from']==0) {
          if ($is_one_and_one==false or 
             ($is_one_and_one and $row['warehouses_id_from']!= $fwarehouse_id))
            echo '<a href="admin-warehouses-item.php?id='.$row['warehouses_id_from'].'">'.$row['warehouse_name_from'].'</a>';
        } else {
          echo $row['warehouse_name_from'];
        }
      } else {
        if ($row['is_virtual_to']==0) {
          if ($is_one_and_one==false or ($is_one_and_one and $row['warehouses_id_to']!= $fwarehouse_id))
            echo '<a href="admin-warehouses-item.php?id='.$row['warehouses_id_to'].'">'.$row['warehouse_name_to'].'</a>';
        } else {
          echo $row['warehouse_name_to'];
        } 
        
      }
      
    ?></td>

<?php if ($is_one_and_one==false) {?>
    <td nowrap class="mytdcm"><?php 
      if (isset($row['product_quantity']) and $row['product_quantity']<>0) {
        echo myNumberFormatNo0Local($row['product_quantity']);
        if ($row['monada_convert_epi_rev']!=1) echo ' * '.myNumberFormatNo0Local($row['monada_convert_epi_rev']);
      }
    ?></td>
<?php } else { 
      
      $temp=gks_whi_mov_balance_calc(array($row['product_id']),$row['date_rec']);
  
  
     ?>
    <td nowrap class="mytdcm"><?php 
      if ($row['seira_is_reverse_delivery_note']==0) {
        if ($row['warehouses_id_from']!= $fwarehouse_id and isset($row['product_quantity']) and $row['product_quantity']<>0) {
         echo myNumberFormatNo0Local($row['product_quantity']);
         if ($row['monada_convert_epi_rev']!=1) echo ' * '.myNumberFormatNo0Local($row['monada_convert_epi_rev']);
        }
      } else {
        if ($row['warehouses_id_to']!= $fwarehouse_id and isset($row['product_quantity']) and $row['product_quantity']<>0) {
          echo myNumberFormatNo0Local($row['product_quantity'], 0);
          if ($row['monada_convert_epi_rev']!=1) echo ' * '.myNumberFormatNo0Local($row['monada_convert_epi_rev']);
        }        
      }
    ?></td>
    <td nowrap class="mytdcm"><?php
      
      if (isset($temp[$row['product_id']]) and isset($temp[$row['product_id']]['warehouses'][$fwarehouse_id])) {
        echo myNumberFormatNo0Local($temp[$row['product_id']]['warehouses'][$fwarehouse_id]['bal']);
      }
      //echo '<pre>';print_r($temp);echo '</pre>';
      
      ?></td>
    <td nowrap class="mytdcm"><?php 
      if ($row['seira_is_reverse_delivery_note']==0) {
        if ($row['warehouses_id_to']!= $fwarehouse_id and isset($row['product_quantity']) and $row['product_quantity']<>0) {
          echo myNumberFormatNo0Local($row['product_quantity'], 0);
          if ($row['monada_convert_epi_rev']!=1) echo ' * '.myNumberFormatNo0Local($row['monada_convert_epi_rev']);
        }
      } else {
        if ($row['warehouses_id_from']!= $fwarehouse_id and isset($row['product_quantity']) and $row['product_quantity']<>0) {
         echo myNumberFormatNo0Local($row['product_quantity']);
         if ($row['monada_convert_epi_rev']!=1) echo ' * '.myNumberFormatNo0Local($row['monada_convert_epi_rev']);
        }        
      }
    ?></td>
<?php } ?>
    
    
    <td nowrap class="mytdcml"><?php 
      if ($row['seira_is_reverse_delivery_note']==0) {
        if ($row['is_virtual_to']==0) {
          if ($is_one_and_one==false or ($is_one_and_one and $row['warehouses_id_to']!= $fwarehouse_id))
            echo '<a href="admin-warehouses-item.php?id='.$row['warehouses_id_to'].'">'.$row['warehouse_name_to'].'</a>';
        } else {
          echo $row['warehouse_name_to'];
        }
      } else {
        if ($row['is_virtual_from']==0) {
          if ($is_one_and_one==false or 
             ($is_one_and_one and $row['warehouses_id_from']!= $fwarehouse_id))
            echo '<a href="admin-warehouses-item.php?id='.$row['warehouses_id_from'].'">'.$row['warehouse_name_from'].'</a>';
        } else {
          echo $row['warehouse_name_from'];
        }        
      }
      
    ?></td>
    <td nowrap class="mytdcm"><?php echo $row['monada_symbol'];?></td>


    
    
    <td nowrap class="mytdcm splittd"><?php 
      if ($row['from_rec']=='whi_mov') {
        echo '<a href="admin-whi-mov-item.php?id='.$row['whi_mov_id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>';
      } else if ($row['from_rec']=='acc_inv') {
        echo '<a href="admin-acc-inv-item.php?id='.$row['acc_inv_id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>';
      } else if ($row['from_rec']=='order') {
        echo '<a href="admin-orders-item.php?id='.$row['order_id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>';
      } else if ($row['from_rec']=='production') {
        echo '<a href="admin-production-item.php?id='.$row['order_id'].'#sintagi"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a>';
      }?></td>
    <td nowrap class="mytdcm"><?php 
      if ($row['from_rec']=='whi_mov') echo $row['whi_mov_id'];
      else if ($row['from_rec']=='acc_inv') echo $row['acc_inv_id'];
      else if ($row['from_rec']=='order') echo $row['order_id'];
      else if ($row['from_rec']=='production') echo $row['order_id'];
    ?></td>
    <td nowrap class="mytdcm"><?php 
      if ($row['from_rec']=='whi_mov') {
        echo '<span class="whi_mov_state_'.$row['state_rec'].'">'.getWhiMovStateDescr($row['state_rec']).'</span>';
      } else if ($row['from_rec']=='acc_inv') {
        echo '<span class="acc_inv_state_'.$row['state_rec'].'">'.getAccInvStateDescr($row['state_rec']).'</span>';
      } else if ($row['from_rec']=='order') {
        echo '<span class="order_state_'.$row['state_rec'].'">'.getOrderStateDescr($row['state_rec']).'</span>';
      } else if ($row['from_rec']=='production') {
        echo '<span class="order_state_'.$row['state_rec'].'">'.getOrderStateDescr($row['state_rec']).'</span>';
      }?></td>
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['date_rec']), 'd/m/Y H:i', 1);?></td>  
    <td        class="mytdcm gks_td08"><a href="admin-users-item.php?id=<?php echo $row['user_id_edit'];?>"><?php echo $row['gks_nickname_edit'];?></a></td>
    <td        class="mytdcml"><?php echo $row['acc_journal_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['seira_code'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['number_int_rec']<>0) echo $row['number_int_rec'];?></td>
    
    
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
  
var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $("#lightgallery").lightGallery({
  	selector: '.lightgalleryitem',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });
  
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');



