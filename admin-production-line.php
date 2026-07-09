<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Γραμμές Παραγωγής');
$nav_active_array=array('production','production_line');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_line','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



$today_vardia_this=user_server_curdate();

$filters = array();

$filters[] = array(
  'name' => 'fdate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Προσθήκη'),
  'has_custom_date' => true,
  'field' => 'gks_production_line.mydate_add', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => array(
    array('value' => 1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
  
  	array('value' => 6,
  				'text' => gks_lang('Σήμερα'),
  				'sql' => "gks_production_line.mydate_add >= '{$today_vardia_this}' and gks_production_line.mydate_add < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)"),
  	array('value' => 8,
  				'text' => gks_lang('Χθες'),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) AND gks_production_line.mydate_add < '{$today_vardia_this}'"),
  	array('value' => 9,
  				'text' => vardia_name($today_vardia_this, -2),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) AND gks_production_line.mydate_add < DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY)"),
  	array('value' => 10,
  				'text' => vardia_name($today_vardia_this, -3),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) AND gks_production_line.mydate_add < DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY)"),
  	array('value' => 11,
  				'text' => vardia_name($today_vardia_this, -4),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) AND gks_production_line.mydate_add < DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY)"),
  	array('value' => 12,
  				'text' => vardia_name($today_vardia_this, -5),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) AND gks_production_line.mydate_add < DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY)"),
  	array('value' => 13,
  				'text' => vardia_name($today_vardia_this, -6),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) AND gks_production_line.mydate_add < DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY)"),
  	array('value' => 14,
  				'text' => vardia_name($today_vardia_this, -7),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) AND gks_production_line.mydate_add < DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY)"),
  	array('value' => 15,
  				'text' => vardia_name($today_vardia_this, -8),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) AND gks_production_line.mydate_add < DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY)"),
  	array('value' => 16,
  				'text' => gks_lang('Προηγούμενη εβδομάδα'),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 WEEK) and gks_production_line.mydate_add < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)"),
  	array('value' => 17,
  				'text' => gks_lang('Προηγούμενος μήνας'),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 MONTH) and gks_production_line.mydate_add < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)"),
  	array('value' => 18,
  				'text' => gks_lang('Προηγούμενοι 3 μήνες'),
  				'sql' => "gks_production_line.mydate_add >= DATE_SUB('{$today_vardia_this}', INTERVAL 3 MONTH) and gks_production_line.mydate_add < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)"),  		
  	array('value' => 19,
  				'text' => gks_lang('Παρελθόν'),
  				'sql' => "gks_production_line.mydate_add < now()"),
  	array('value' => -2,
  				'text' => gks_lang('Επιλογή ημερών'),
  				'sql' => '',
  				'is_custom_date' => true,
  				'vardiacustomdate' => (GKS_ERP_START_VARDIA!=0 ? true : false)
  			 ),  			        
  )
);

$filters[] = array(
  'name' => 'feidos',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Είδος'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders_products.product_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.base_type_id=0"),
  ),
  'sql' => "SELECT gks_eshop_products.id_product as id, gks_eshop_products.product_code as descr
FROM ((gks_production_line LEFT JOIN gks_production_line_pid ON gks_production_line.id_production_line = gks_production_line_pid.production_line_id) LEFT JOIN gks_orders_products ON gks_production_line_pid.order_product_id = gks_orders_products.id_order_product) LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
WHERE (((gks_eshop_products.id_product) Is Not Null))
GROUP BY gks_eshop_products.id_product, gks_eshop_products.product_code
ORDER BY gks_eshop_products.product_code;",    
);

$filters[] = array(
  'name' => 'fergasia',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Εργασία'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_production_line.ergasia_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.base_type_id=0"),
  ),
  'sql' => "SELECT gks_production_line.ergasia_id as id, gks_production_ergasies.production_ergasia_descr as descr
FROM gks_production_line LEFT JOIN gks_production_ergasies ON gks_production_line.ergasia_id = gks_production_ergasies.id_production_ergasia
WHERE (((gks_production_ergasies.id_production_ergasia) Is Not Null))
GROUP BY gks_production_line.ergasia_id, gks_production_ergasies.production_ergasia_descr
ORDER BY gks_production_ergasies.production_ergasia_sortorder, gks_production_ergasies.production_ergasia_descr;",    
);

if (isset($_GET['fstate']) == false) $_GET['fstate']='10,30,40,50,60';

$filters[] = array(
  'name' => 'fstate',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_production_line.pl_state = '%V%'",
  'vals' => array(
    array('value' => 10, 'text' => getProductionLineStateDescr('010draft'),      'sql' => "gks_production_line.pl_state='010draft'"),
    array('value' => 20, 'text' => getProductionLineStateDescr('020cancelled'),      'sql' => "gks_production_line.pl_state='020cancelled'"),
    array('value' => 30, 'text' => getProductionLineStateDescr('030pending'),      'sql' => "gks_production_line.pl_state='030pending'"),
    array('value' => 40, 'text' => getProductionLineStateDescr('040ready'),      'sql' => "gks_production_line.pl_state='040ready'"),
    array('value' => 50, 'text' => getProductionLineStateDescr('050processing'),      'sql' => "gks_production_line.pl_state='050processing'"),
    array('value' => 60, 'text' => getProductionLineStateDescr('060pause'),      'sql' => "gks_production_line.pl_state='060pause'"),
    array('value' => 70, 'text' => getProductionLineStateDescr('070failed'),      'sql' => "gks_production_line.pl_state='070failed'"),
    array('value' => 100, 'text' => getProductionLineStateDescr('100completed'),      'sql' => "gks_production_line.pl_state='100completed'"),

  
  ),
);



$filters[] = array(
  'name' => 'fypallilos',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τελευταίος Υπάλληλος'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_production_line.last_user_id_production = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.base_type_id=0"),
  ),
  'sql' => "SELECT gks_production_line.last_user_id_production as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
  FROM gks_production_line LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_production_line.last_user_id_production = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (((gks_production_line.last_user_id_production)>0) AND ((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
  GROUP BY gks_production_line.last_user_id_production, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;",    
);

$filters[] = array(
  'name' => 'fposto',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τελευταίο Πόστο'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_production_line.last_posto_id = %V%",
  'vals' => array(
      //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.base_type_id=0"),
  ),
  'sql' => "SELECT gks_production_posta.id_production_posto as id, gks_production_posta.production_posto_descr as descr
FROM gks_production_line LEFT JOIN gks_production_posta ON gks_production_line.last_posto_id = gks_production_posta.id_production_posto
WHERE (((gks_production_posta.id_production_posto) Is Not Null))
GROUP BY gks_production_posta.id_production_posto, gks_production_posta.production_posto_descr
ORDER BY gks_production_posta.production_posto_sortorder, gks_production_posta.production_posto_descr;",    
);

$filters[] = array(
  'name' => 'fdate_edit',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Τελευταία Ενημέρωση'),
  'has_custom_date' => true,
  'field' => 'gks_production_line.mydate_edit', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => array(
    array('value' => 1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
  
  	array('value' => 6,
  				'text' => gks_lang('Σήμερα'),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= '{$today_vardia_this}' and gks_production_line.mydate_edit < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)"),
  	array('value' => 8,
  				'text' => gks_lang('Χθες'),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) AND gks_production_line.mydate_edit < '{$today_vardia_this}'"),
  	array('value' => 9,
  				'text' => vardia_name($today_vardia_this, -2),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) AND gks_production_line.mydate_edit < DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY)"),
  	array('value' => 10,
  				'text' => vardia_name($today_vardia_this, -3),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) AND gks_production_line.mydate_edit < DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY)"),
  	array('value' => 11,
  				'text' => vardia_name($today_vardia_this, -4),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) AND gks_production_line.mydate_edit < DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY)"),
  	array('value' => 12,
  				'text' => vardia_name($today_vardia_this, -5),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) AND gks_production_line.mydate_edit < DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY)"),
  	array('value' => 13,
  				'text' => vardia_name($today_vardia_this, -6),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) AND gks_production_line.mydate_edit < DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY)"),
  	array('value' => 14,
  				'text' => vardia_name($today_vardia_this, -7),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) AND gks_production_line.mydate_edit < DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY)"),
  	array('value' => 15,
  				'text' => vardia_name($today_vardia_this, -8),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) AND gks_production_line.mydate_edit < DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY)"),
  	array('value' => 16,
  				'text' => gks_lang('Προηγούμενη εβδομάδα'),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 WEEK) and gks_production_line.mydate_edit < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)"),
  	array('value' => 17,
  				'text' => gks_lang('Προηγούμενος μήνας'),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 MONTH) and gks_production_line.mydate_edit < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)"),
  	array('value' => 18,
  				'text' => gks_lang('Προηγούμενοι 3 μήνες'),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit >= DATE_SUB('{$today_vardia_this}', INTERVAL 3 MONTH) and gks_production_line.mydate_edit < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)"),  		
  	array('value' => 19,
  				'text' => gks_lang('Παρελθόν'),
  				'sql' => "gks_production_line.mydate_edit<>gks_production_line.mydate_add and gks_production_line.mydate_edit < now()"),
  	array('value' => -2,
  				'text' => gks_lang('Επιλογή ημερών'),
  				'sql' => '',
  				'is_custom_date' => true,
  				'vardiacustomdate' => (GKS_ERP_START_VARDIA!=0 ? true : false)
  			 ),  			        
  )
);

$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_production_line.id_production_line'),
  						array('name' => 'somydate_add', 'field' => 'gks_production_line.mydate_add'),
  						array('name' => 'soorderid', 'field' => 'gks_production_line.order_id'),
  						array('name' => 'socustomer', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  						array('name' => 'soeidos', 'field' => 'gks_eshop_products.product_descr'),
  						array('name' => 'sosheets', 'field' => 'gks_orders_products.product_sheets'),
  						array('name' => 'soqua', 'field' => 'gks_orders_products.product_quantity'),
  						array('name' => 'soergasia', 'field' => 'gks_production_ergasies.production_ergasia_descr'),
  						array('name' => 'sopl_state', 'field' => 'gks_production_line.pl_state'),
  						array('name' => 'souseredit', 'field' => GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname'),
  						array('name' => 'sotime', 'field' => 'gks_production_line.prod_sum_time'),
  						array('name' => 'soset', 'field' => 'gks_production_line.set_id'),
  						array('name' => 'soposto', 'field' => 'gks_production_posta.production_posto_descr'),
  						
  						
           );

$search_fields = array(
'gks_eshop_products.product_code',
'gks_eshop_products.product_descr',
'gks_production_ergasies.production_ergasia_descr',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'product_comments',
'prod_comments',

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
 

$sql = "SELECT DISTINCTROW SQL_CALC_FOUND_ROWS gks_production_line.*, gks_orders.order_state, gks_orders.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_production_ergasies.production_ergasia_descr, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
gks_orders.note_production, ".GKS_WP_TABLE_PREFIX."users_lastuser.gks_nickname AS gks_nickname_lastuser,
gks_production_posta.production_posto_descr
FROM (((((((((gks_production_line 
LEFT JOIN gks_production_ergasies ON gks_production_line.ergasia_id = gks_production_ergasies.id_production_ergasia) 
LEFT JOIN gks_orders ON gks_production_line.order_id = gks_orders.id_order) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_production_line.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_production_line.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_lastuser ON gks_production_line.last_user_id_production = ".GKS_WP_TABLE_PREFIX."users_lastuser.ID) 
LEFT JOIN gks_production_line_pid ON gks_production_line.id_production_line = gks_production_line_pid.production_line_id) 
LEFT JOIN gks_orders_products ON gks_production_line_pid.order_product_id = gks_orders_products.id_order_product)
LEFT JOIN gks_production_posta ON gks_production_line.last_posto_id = gks_production_posta.id_production_posto)
LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product 

where 1=1 ".$where . $search_where;
//echo '<pre>'.$sql;die();

//gks_eshop_products.product_descr, gks_eshop_products.product_code, 
//LEFT JOIN gks_eshop_products ON gks_production_line.product_id = gks_eshop_products.id_product) 

//gks_orders_products.product_sheets, gks_orders_products.product_quantity, gks_orders_products.product_comments, 
//LEFT JOIN gks_orders_products ON gks_production_line.order_product_id = gks_orders_products.id_order_product) 
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_production_line.id_production_line";
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
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somydate_add', gks_lang('Προσθήκη')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soorderid', gks_lang('Παραγγελία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socustomer', gks_lang('Πελάτης')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soset', gks_lang('Σετ')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap><?php echo gks_lang('Είδη');?></th>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  ><?php echo gks_lang('Σχόλιο από Παραγγελία');?></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="15%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soergasia', gks_lang('Εργασία')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopl_state', gks_lang('Κατάσταση')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"  ><?php echo gks_lang('Σχόλιο Παραγωγής');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotime', gks_lang('Χρόνος')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'souseredit', '<span title="'.gks_lang('Τελευταίος Υπάλληλος').'" class="tooltipster">'.gks_lang('Υπάλληλος').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soposto', '<span title="'.gks_lang('Τελευταίο Πόστο').'" class="tooltipster">'.gks_lang('Πόστο').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soposto', '<span title="'.gks_lang('Τελευταία Ενημέρωση').'" class="tooltipster">'.gks_lang('Ενημέρωση').'</span>'); ?></th>        
           
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
          <td><a href="admin-production-line-item.php?id=<?php echo $row['id_production_line'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_production_line'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_production_line'];?>" data-model="gks_production_line"></i></td>
        </tr>      
      </table>
    </td>

    <td        class="mytdcm"><?php echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
    <td nowrap class="mytdcm">
      <a href="admin-orders-item.php?id=<?php echo $row['order_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Παραγγελία');?>"></i></a>
      <a href="admin-production-item.php?id=<?php echo $row['order_id'];?>" title="<?php echo gks_lang('Παραγωγή Παραγγελίας');?>"><?php echo $row['order_id'];?></a>
    </td>
    <td        class="mytdcm"><a href="admin-users-item.php?id=<?php echo $row['user_id'];?>"><?php echo $row['gks_nickname'];?></a></td>
    <td nowrap class="mytdcm"><?php echo $row['set_id'];?></td>
    <td        class="mytdcml"><?php 
      $html_eidos='';
      $sql_eidi="SELECT gks_eshop_products.id_product, gks_eshop_products.product_code, gks_eshop_products.product_descr, gks_orders_products.product_sheets, gks_orders_products.product_quantity,
      gks_orders_products.product_comments
      FROM (gks_production_line_pid 
      LEFT JOIN gks_orders_products ON gks_production_line_pid.order_product_id = gks_orders_products.id_order_product) 
      LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
      WHERE (((gks_production_line_pid.production_line_id)=".$row['id_production_line']."))
      ORDER BY gks_eshop_products.product_code;";
      $result_eidi = $db_link->query($sql_eidi);        
      if (!$result_eidi) debug_mail(false,'error sql',$sql_eidi);
      if (!$result_eidi) die('sql error');
      while ($row_eidi = $result_eidi->fetch_assoc()) {
        $html_eidos.='<a href="admin-products-item.php?id='.$row_eidi['id_product'].'" class="tooltipster" title="Σελίδες: '.$row_eidi['product_sheets'].'<br>Ποσότητα: '.$row_eidi['product_quantity'].'<br>'.gks_lang('Παρατηρήσεις').': '.$row_eidi['product_comments'].'">'.$row_eidi['product_code'].'</a>, ';
        
      }
      if ($html_eidos!='') $html_eidos=substr($html_eidos, 0, strlen($html_eidos)-2);
      echo $html_eidos;
      
    ?></td>
    
    <td        class="mytdcml" style="font-size:0.8rem"><?php echo nl2br_gks(htmlentities($row['note_production']));?></td>
    <td        class="mytdcml"><a href="admin-production-ergasies-item.php?id=<?php echo $row['ergasia_id'];?>"><?php echo $row['production_ergasia_descr'];?></a></td>
    <td nowrap class="mytdcm"><span class="line_state production_line_state_<?php echo $row['pl_state'];?>" data-recid="<?php echo $row['id_production_line'];?>" data-oldstate="<?php echo $row['pl_state'];?>"><?php echo getProductionLineStateDescr($row['pl_state']);?></span></td>
    <td        class="mytdcml" style="font-size:0.8rem"><?php echo nl2br_gks(htmlentities($row['prod_comments']));?></td>
    <td nowrap class="mytdcm"><?php echo time_duration_format($row['prod_sum_time']);?></td>
    <td        class="mytdcm"><a href="admin-users-item.php?id=<?php echo $row['last_user_id_production'];?>"><?php echo $row['gks_nickname_lastuser'];?></a></td>
    <td        class="mytdcm"><a href="admin-production-posta-item.php?id=<?php echo $row['last_posto_id'];?>"><?php echo $row['production_posto_descr'];?></a></td>
    
    <td        class="mytdcm"><?php 
      if (isset($row['mydate_edit']) and $row['mydate_edit']!=$row['mydate_add']) {
        echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);
      }
      
    ?></td>   
    
    
  </tr>
<?php    
    }
?>

</tbody>
</table>
<style>
.line_state {
  cursor:pointer; 
}  
</style>

<?php mytablepages($paging, $total_records); ?>

 



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_line','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_line','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_line','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fdate_add-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_add-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fdate_edit-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_edit-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_add' || sname =='fdate_edit' || sname=='fddate') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate_add' || sname =='fdate_edit' || sname=='fddate') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });
  

	line_state_contextMenu={
		event: 'click',
    items: function(e) {
  		var arr = [];
  		arr.push({type: 'item', text: '<span class="production_line_state_010draft"><?php echo getProductionLineStateDescr('010draft');?></span>', disabled: oldstate=='010draft' || (oldstate=='050processing'), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('010draft');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_020cancelled"><?php echo getProductionLineStateDescr('020cancelled');?></span>', disabled: oldstate=='020cancelled' || (oldstate=='050processing'), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('020cancelled');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_030pending"><?php echo getProductionLineStateDescr('030pending');?></span>', disabled: oldstate=='030pending' || (oldstate=='050processing'), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('030pending');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_040ready"><?php echo getProductionLineStateDescr('040ready');?></span>', disabled: oldstate=='040ready' || (oldstate=='050processing'), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('040ready');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_050processing"><?php echo getProductionLineStateDescr('050processing');?></span>', disabled: oldstate=='050processing' || (!(oldstate=='040ready' || oldstate=='060pause')), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('050processing');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_060pause"><?php echo getProductionLineStateDescr('060pause');?></span>', disabled: oldstate=='060pause' || (!(oldstate=='050processing')), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('060pause');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_070failed"><?php echo getProductionLineStateDescr('070failed');?></span>', disabled: oldstate=='070failed' || (!(oldstate=='050processing')), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('070failed');
  		}});
  		arr.push({type: 'item', text: '<span class="production_line_state_100completed"><?php echo getProductionLineStateDescr('100completed');?></span>', disabled: 1==2 & (oldstate=='100completed' || (!(oldstate=='040ready' || oldstate=='060pause' || oldstate=='050processing'))), click: function(e){	
  		  e.preventDefault();  
				line_state_cmd('100completed');
  		}});

      return arr;
    }
	};
	
  $('.line_state').contextMenu(line_state_contextMenu);
  
  var id_production_line=0;
  var oldstate='';
  $('.line_state').click(function(event) {  
    id_production_line = parseInt($(this).attr('data-recid'));
    if (isNaN(id_production_line)) id_production_line=0;
    oldstate = $(this).attr('data-oldstate');
    //console.log(id_production_line);
    //console.log(oldstate);
  });	

  function line_state_cmd(newstate) {
    
    datasend='';
    datasend+='id=' + id_production_line;    
    datasend+='&newstate='  + encodeURI(newstate.trim());    
    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: 'admin-production-posto-run-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});
  }
  
  

});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


