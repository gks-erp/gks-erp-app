<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Τιμές');
$nav_active_array=array('hotel','hotel_price');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_price','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_hotel_price','edit',0);

$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_price',['from'=>'list']);

$user_hotels=gks_get_hotels_list();


$today_vardia_this = date('Y-m-d',_time_user(time(), 1));

//echo date('d/m/Y H:i:s',_time_user(time(), 1));
//die();

$filters = array();

if (count($user_hotels)>=1) {
  $vals=array();
  foreach ($user_hotels as $key=>$value) {
    $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "gks_hotel_price.hotel_id=".$value['id']);
  } 
  $filters[] = array(
    'name' => 'fhotel_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ξενοδοχείο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => $vals,
  );  
}

$filters[] = array(
    'name' => 'fhotel_room_type_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος Δωματίου'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_price.hotel_room_type_id = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT id_hotel_room_type as id, room_type_descr as descr FROM gks_hotel_room_type 
    ".(count($perm_id_hotel_ids)>0 ? ' where hotel_id in ('.implode(',',$perm_id_hotel_ids).')' : '')."
    ORDER BY room_type_sortorder,room_type_descr;",
);



$filters[] = array(
			'name' => 'fprice',
			'class' => 'filterselectbox ui-state-default ui-corner-all',
			'style' => '',
		  'title' => gks_lang('Ημερομηνία'),
			'has_custom_date' => true,
			'field' => 'gks_hotel_price.price_from',
			'has_custom_default' => 1,
//		'mywherepos'=>1,
			'vals' => array(
  			        array('value' => 1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
								array('value' => 25,
											'text' => vardia_name($today_vardia_this, 8),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_price.price_to   < DATE_ADD('{$today_vardia_this}', INTERVAL 9 DAY)) or
											(gks_hotel_price.price_from >= DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_price.price_from < DATE_ADD('{$today_vardia_this}', INTERVAL 9 DAY)) or
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_price.price_to   > DATE_ADD('{$today_vardia_this}', INTERVAL 9 DAY)) or 
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 24,
											'text' => vardia_name($today_vardia_this, 7),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_price.price_to   < DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY)) or
											(gks_hotel_price.price_from >= DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_price.price_from < DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY)) or
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_price.price_to   > DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY)) or 
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 23,
											'text' => vardia_name($today_vardia_this, 6),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_price.price_to   < DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY)) or
											(gks_hotel_price.price_from >= DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_price.price_from < DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY)) or
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_price.price_to   > DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY)) or 
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 22,
											'text' => vardia_name($today_vardia_this, 5),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_price.price_to   < DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY)) or
											(gks_hotel_price.price_from >= DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_price.price_from < DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY)) or
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_price.price_to   > DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY)) or 
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 21,
											'text' => vardia_name($today_vardia_this, 4),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_price.price_to   < DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY)) or
											(gks_hotel_price.price_from >= DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_price.price_from < DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY)) or
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_price.price_to   > DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY)) or 
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 20,
											'text' => vardia_name($today_vardia_this, 3),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_price.price_to   < DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY)) or
											(gks_hotel_price.price_from >= DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_price.price_from < DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY)) or
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_price.price_to   > DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY)) or 
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 19,
											'text' => vardia_name($today_vardia_this, 2),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_price.price_to   < DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY)) or
											(gks_hotel_price.price_from >= DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_price.price_from < DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY)) or
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_price.price_to   > DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY)) or 
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 3,
											'text' => gks_lang('Αύριο'),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_price.price_to   < DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY)) or
											(gks_hotel_price.price_from >= DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_price.price_from < DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY)) or
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_price.price_to   > DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY)) or 
											(gks_hotel_price.price_from <= DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 5,
											'text' => gks_lang('Σήμερα'),
											'sql' => "
										 ((gks_hotel_price.price_to   >= '{$today_vardia_this}' and gks_hotel_price.price_to   < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)) or
											(gks_hotel_price.price_from >= '{$today_vardia_this}' and gks_hotel_price.price_from < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)) or
											(gks_hotel_price.price_from <= '{$today_vardia_this}' and gks_hotel_price.price_to   > DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)) or 
											(gks_hotel_price.price_from <= '{$today_vardia_this}' and gks_hotel_price.price_to is null))"),
								array('value' => 7,
											'text' => gks_lang('Χθες'),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_price.price_to   < DATE_SUB('{$today_vardia_this}', INTERVAL 0 DAY)) or
											(gks_hotel_price.price_from >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_price.price_from < DATE_SUB('{$today_vardia_this}', INTERVAL 0 DAY)) or
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_price.price_to   > DATE_SUB('{$today_vardia_this}', INTERVAL 0 DAY)) or 
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 9,
											'text' => vardia_name($today_vardia_this, -2),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_price.price_to   < DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY)) or
											(gks_hotel_price.price_from >= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_price.price_from < DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY)) or
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_price.price_to   > DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY)) or 
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 10,
											'text' => vardia_name($today_vardia_this, -3),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_price.price_to   < DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY)) or
											(gks_hotel_price.price_from >= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_price.price_from < DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY)) or
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_price.price_to   > DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY)) or 
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 11,
											'text' => vardia_name($today_vardia_this, -4),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_price.price_to   < DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY)) or
											(gks_hotel_price.price_from >= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_price.price_from < DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY)) or
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_price.price_to   > DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY)) or 
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 12,
											'text' => vardia_name($today_vardia_this, -5),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_price.price_to   < DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY)) or
											(gks_hotel_price.price_from >= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_price.price_from < DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY)) or
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_price.price_to   > DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY)) or 
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 13,
											'text' => vardia_name($today_vardia_this, -6),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_price.price_to   < DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY)) or
											(gks_hotel_price.price_from >= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_price.price_from < DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY)) or
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_price.price_to   > DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY)) or 
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 14,
											'text' => vardia_name($today_vardia_this, -7),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_price.price_to   < DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY)) or
											(gks_hotel_price.price_from >= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_price.price_from < DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY)) or
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_price.price_to   > DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY)) or 
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_price.price_to is null))"),
								array('value' => 15,
											'text' => vardia_name($today_vardia_this, -8),
											'sql' => "
										 ((gks_hotel_price.price_to   >= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_price.price_to   < DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY)) or
											(gks_hotel_price.price_from >= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_price.price_from < DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY)) or
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_price.price_to   > DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY)) or 
											(gks_hotel_price.price_from <= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_price.price_to is null))"),

							)
);



$filters[] = array(
	'name' => 'fafrom',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Από'),
	'has_custom_date' => true,
	'field' => 'gks_hotel_price.price_from',
	'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_hotel_price.price_from','future'=>true,'today'=>$today_vardia_this, 'today_vardia'=>$today_vardia_this, 'set_vardia'=>false]),


);

$filters[] = array(
	'name' => 'fato',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Έως'),
	'has_custom_date' => true,
	'field' => 'gks_hotel_price.price_to',
	'has_custom_default' => 2,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_hotel_price.price_to','future'=>true,'today'=>$today_vardia_this, 'today_vardia'=>$today_vardia_this, 'set_vardia'=>false]),

);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);





$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_hotel_price.id_hotel_price'),
  						array('name' => 'sohotel', 'field' => 'gks_hotel.hotel_title'),
  						array('name' => 'soroomtype', 'field' => 'gks_hotel_room_type.room_type_descr'),
  						array('name' => 'sodescr', 'field' => 'gks_hotel_price.price_descr'),
  						array('name' => 'sofrom', 'field' => 'gks_hotel_price.price_from'),
  						array('name' => 'soto', 'field' => 'gks_hotel_price.price_to'),
  						array('name' => 'soprice', 'field' => 'gks_hotel_price.price'),
  						array('name' => 'sodays', 'field' => 'price_weekday_de,price_weekday_tr,price_weekday_te,price_weekday_pe,price_weekday_pa,price_weekday_sa,price_weekday_ky')
  						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_hotel_room_type.room_type_descr', 
'gks_hotel_price.price_descr',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
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


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_hotel_price.*, 
gks_hotel_room_type.room_type_descr,  
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_hotel.hotel_title
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((gks_hotel_price 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN gks_hotel ON gks_hotel_price.hotel_id = gks_hotel.id_hotel)
LEFT JOIN gks_hotel_room_type ON gks_hotel_price.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_price.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_price.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
where 1=1 ".$where . $search_where;
if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_price.hotel_id in (".implode(',',$perm_id_hotel_ids).")";      
      

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_hotel_price.price_from, gks_hotel_price.price_to";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;


//echo $sql;
//die();
	
//echo 'ffffffff'.time();
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
      <a class="btn btn-primary gks_add_new_record" href="admin-hotel-price-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας τιμής');?></a>
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
    <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sohotel', gks_lang('Ξενοδοχείο')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soroomtype', gks_lang('Τύπος-Δωμάτιο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Σχόλιο')); ?></th>  
    <th class="table-dark" scope="col" style="text-align: center !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sofrom', gks_lang('Από')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soto', gks_lang('Έως')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="15%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodays', gks_lang('Ημέρες')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice', gks_lang('Τιμή')); ?></th>        
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_hotel_price'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-hotel-price-item.php?id=<?php echo $row['id_hotel_price'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_hotel_price'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_hotel_price'];?>" data-model="gks_hotel_price"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcml"><?php echo $row['hotel_title'];?></td>
    <td class="mytdcml"><?php echo $row['room_type_descr'];?></td>
    <td class="mytdcml"><?php echo $row['price_descr'];?></td>
    <td nowrap class="mytdcm"><?php if (isset($row['price_from'])) echo myDateFormatw(strtotime($row['price_from'])) ;?></td>
    <td nowrap class="mytdcm"><?php if (isset($row['price_to'])) echo myDateFormatw(strtotime($row['price_to'])) ;?></td>
    
    <td nowrap class="mytdcm"><?php
    $seldays1=false;
    if ($row['price_weekday_de']!=0 and $row['price_weekday_tr']!=0 and $row['price_weekday_te']!=0 and $row['price_weekday_pe']!=0 and 
        $row['price_weekday_pa']!=0 and $row['price_weekday_sa']!=0 and $row['price_weekday_ky']!=0) $seldays1=true;     
    if ($seldays1) {
      echo gks_lang('Όλες οι ημέρες');
    } else {
      $out='';
      if ($row['price_weekday_de']) $out.= mb_substr(getWeekDayName(1),0,2).', ';
      if ($row['price_weekday_tr']) $out.= mb_substr(getWeekDayName(2),0,2).', ';
      if ($row['price_weekday_te']) $out.= mb_substr(getWeekDayName(3),0,2).', ';
      if ($row['price_weekday_pe']) $out.= mb_substr(getWeekDayName(4),0,2).', ';
      if ($row['price_weekday_pa']) $out.= mb_substr(getWeekDayName(5),0,2).', ';
      if ($row['price_weekday_sa']) $out.= mb_substr(getWeekDayName(6),0,2).', ';
      if ($row['price_weekday_ky']) $out.= mb_substr(getWeekDayName(0),0,2).', ';
      if ($out!='')  echo substr($out, 0, strlen($out)-2);
    }  
    ?></td>
    
    <td nowrap class="mytdcm"><?php echo myCurrencyFormat($row['price']);?></td>
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_price','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_price','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_price','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#fafrom-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fafrom-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fato-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fato-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fafrom' || sname == 'fato' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fafrom' || sname == 'fato' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });  



});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


