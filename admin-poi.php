<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Σημεία Ενδιαφέροντος');
$nav_active_array=array('transfer','transfer_poi');

db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_poi','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_poi_ids=gks_permission_user_condition($my_wp_user_id,'gks_poi','01');
//print '<pre>';print_r($perm_id_poi_ids);die();

$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_poi','edit',0);





$gks_custom_prepare = gks_custom_table_item_prepare('gks_poi',['from'=>'list']);


$filters = array();


$filters[] = array(
    'name' => 'ftype',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_poi.poi_type_id = %V%",
    'vals' => array(
        array('value' => -100, 'text' => gks_lang('Χωρίς τύπο'),          'sql' => "gks_poi.poi_type_id=0"),
    ),
    'sql' => "SELECT gks_poi.poi_type_id as id, gks_poi_type.poi_type_descr as descr
FROM gks_poi LEFT JOIN gks_poi_type ON gks_poi.poi_type_id = gks_poi_type.id_poi_type
WHERE gks_poi_type.id_poi_type Is Not Null
GROUP BY gks_poi.poi_type_id, gks_poi_type.poi_type_descr
ORDER BY gks_poi_type.poi_type_sortorder"
);

$filters[] = array(
    'name' => 'fparent',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Γονικό Σημείο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "%V%,",
    'vals' => array(
        array('value' => -100, 'text' => gks_lang('Χωρίς Γονικό Σημείο'),          'sql' => "gks_poi.poi_parent_id is null"),
    ),
    'mywherepos' =>1,
    'sql' => "select gks_poi.id_poi as id, 
    CONCAT_WS('\\\\',
                     ug10.poi_descr,
                     ug9.poi_descr,
                     ug8.poi_descr,
                     ug7.poi_descr,
                     ug6.poi_descr,
                     ug5.poi_descr,
                     ug4.poi_descr,
                     ug3.poi_descr,
                     ug2.poi_descr,
                     gks_poi.poi_descr) as descr
    FROM ((((((((gks_poi
    LEFT JOIN gks_poi AS ug2 ON gks_poi.poi_parent_id = ug2.id_poi)
    LEFT JOIN gks_poi AS ug3 ON ug2.poi_parent_id = ug3.id_poi)
    LEFT JOIN gks_poi AS ug4 ON ug3.poi_parent_id = ug4.id_poi)
    LEFT JOIN gks_poi AS ug5 ON ug4.poi_parent_id = ug5.id_poi)
    LEFT JOIN gks_poi AS ug6 ON ug5.poi_parent_id = ug6.id_poi)
    LEFT JOIN gks_poi AS ug7 ON ug6.poi_parent_id = ug7.id_poi)
    LEFT JOIN gks_poi AS ug8 ON ug7.poi_parent_id = ug8.id_poi)
    LEFT JOIN gks_poi AS ug9 ON ug8.poi_parent_id = ug9.id_poi)
    LEFT JOIN gks_poi AS ug10 ON ug9.poi_parent_id = ug10.id_poi

    WHERE gks_poi.id_poi in (
      SELECT gks_poi.id_poi
      FROM gks_poi LEFT JOIN gks_poi AS gks_poi_kati_exei ON gks_poi.id_poi = gks_poi_kati_exei.poi_parent_id
      WHERE gks_poi_kati_exei.poi_parent_id Is Not Null
      GROUP BY gks_poi.id_poi
    )
    ORDER BY descr",



    
);

$filters[] = array(
    'name' => 'fpricelist',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τιμοκατάλογοι'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "table_pricelistcount.pricelistcount %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Δεν έχει'),      'sql' => "(table_pricelistcount.pricelistcount =0 or table_pricelistcount.pricelistcount is null)"),
        array('value' => -200, 'text' => gks_lang('Έχει'),          'sql' => "table_pricelistcount.pricelistcount>0"),
    ),
//    'sql' => "SELECT poi_poli as descr, poi_poli as id FROM gks_poi WHERE poi_poli<>'' GROUP BY poi_poli ORDER BY poi_poli",
);
$filters[] = array(
    'name' => 'fdiadromes',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Διαδρομές'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "table_diadromescount.diadromescount %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Δεν έχει'),      'sql' => "(table_diadromescount.diadromescount =0 or table_diadromescount.diadromescount is null)"),
        array('value' => -200, 'text' => gks_lang('Έχει'),          'sql' => "table_diadromescount.diadromescount>0"),
    ),
//    'sql' => "SELECT poi_poli as descr, poi_poli as id FROM gks_poi WHERE poi_poli<>'' GROUP BY poi_poli ORDER BY poi_poli",
);

$filters[] = array(
    'name' => 'fnomos',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Νομός'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_poi.poi_nomos_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_poi.poi_nomos_id as id, gks_nomoi.nomos_descr as descr
FROM gks_poi LEFT JOIN gks_nomoi ON gks_poi.poi_nomos_id = gks_nomoi.id_nomos
WHERE (((gks_nomoi.id_nomos) Is Not Null))
GROUP BY gks_poi.poi_nomos_id, gks_nomoi.nomos_descr
ORDER BY gks_nomoi.nomos_descr"
);

$filters[] = array(
    'name' => 'fxora',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χώρα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_poi.poi_country_id = %V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_poi.poi_country_id as id, gks_country.country_name as descr
FROM gks_poi LEFT JOIN gks_country ON gks_poi.poi_country_id = gks_country.id_country
WHERE (((gks_country.id_country) Is Not Null))
GROUP BY gks_poi.poi_country_id, gks_country.country_name
ORDER BY gks_country.country_name"
);


$filters[] = array(
    'name' => 'fpoint',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Στίγμα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),             'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Έχει στίγμα'),     'sql' => "gks_poi.poi_map_latitude <> 0 or gks_poi.poi_map_longitude <>0"),
        array('value' => 2, 'text' => gks_lang('Δεν έχει στίγμα'), 'sql' => "gks_poi.poi_map_latitude = 0 and gks_poi.poi_map_longitude = 0"),
    ),
);
$filters[] = array(
    'name' => 'farea',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Σχήματα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),             'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Έχει σχήματα'),     'sql' => "gks_poi.poi_bound_north<>0"),
        array('value' => 2, 'text' => gks_lang('Δεν έχει σχήματα'), 'sql' => "gks_poi.poi_bound_north =0"),
    ),
);

$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργό'),
    'has_custom_default' => 1,
    'multiselect' => true,    
    'field'  => "1=1",
    'vals' => array(
        //array('value' => -1,'text' => gks_lang('Όλα'),        'sql' => "1=1"),
        array('value' => 1, 'text' => gks_lang('Ενεργό'),     'sql' => "gks_poi.poi_disable = 0"),
        array('value' => 2, 'text' => gks_lang('Έχει'),  'sql' => "gks_poi.poi_disable <> 0"),
    ),
);
$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);





$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_poi.id_poi'),
  						array('name' => 'sotitle', 'field' => 'gks_poi.poi_descr'),
  						array('name' => 'sotype', 'field' => 'gks_poi_type.poi_type_descr'),
  						array('name' => 'soparent', 'field' => 'dirpath'),
  						array('name' => 'soccc', 'field' => 'ccsubpoi.ccc'),
  						
  						array('name' => 'sophone', 'field' => 'gks_poi.poi_phone'),
  						array('name' => 'soemail', 'field' => 'gks_poi.poi_email'),
  						array('name' => 'soodos', 'field' => 'gks_poi.poi_odos'),
  						array('name' => 'soperioxi', 'field' => 'gks_poi.poi_perioxi'),
  						array('name' => 'sopoli', 'field' => 'gks_poi.poi_poli'),
  						array('name' => 'sotk', 'field' => 'gks_poi.poi_tk'),
  						array('name' => 'socountry', 'field' => 'gks_country.country_name'),
  						array('name' => 'sonomos', 'field' => 'gks_nomoi.nomos_descr'),
  						array('name' => 'sodisable', 'field' => 'gks_poi.poi_disable'),
  						array('name' => 'sosort', 'field' => 'gks_poi.poi_sortorder'),
  						array('name' => 'sopricelistcount', 'field' => 'pricelistcount'),
  						array('name' => 'sodiadromescount', 'field' => 'diadromescount'),
  						
  						
            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_poi.poi_descr',
'gks_poi.poi_phone',
'gks_poi.poi_email',
'gks_poi.poi_odos',
'gks_poi.poi_perioxi',
'gks_poi.poi_poli',
'gks_poi.poi_tk',
'gks_country.country_name',
'gks_nomoi.nomos_descr',
'gks_poi.poi_comments',
'gks_poi.poi_iata_code',
'gks_poi.poi_icao_code',
'gks_poi_type.poi_type_descr',


);
$search_fields=array_merge($search_fields,$gks_custom_prepare['sql_search_fields']);



$filter = array('html' => '', 'sql' => '', 'url' => '');
$search_string_value = (isset($_GET['search_string']) ? $_GET['search_string'] : '');
makeFilters($filters, $filter, $_GET,true,true,$search_string_value);




$search_where = make_search_where($search_string_value,$search_fields);
$search_where = !empty($search_where) ? ' AND '.$search_where : '';
//echo $search_where;
//die();


$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';

//echo $where1.'|';
$where1_calc='';
if ($where1!='') {
  //print  $where1;
  //die();
  //AND (gks_poi.poi_parent_id is null or 38, or 37,)
  //AND (35, or 33, or 27,)
  
  $fgroup_100=false;
  if (strpos($where1, 'gks_poi.poi_parent_id is null') !== false) $fgroup_100=true;
  $where1=str_replace('gks_poi.poi_parent_id is null or ', '', $where1);
  
  $where1=trim_gks($where1);
  $where1=substr($where1, 6);
  $where1=substr($where1, 0, strlen($where1) - 2);
  $where1=str_replace(', or ', ',', $where1);
  $vals=explode(',', $where1);
  
  $vals_array=array();
  foreach ($vals as $value) {
    $value=intval($value);
    if ($value>0) {
      if (in_array($value, $vals_array)==false) {
        $vals_array[]=$value;
      }
    }
  } 
  
  //print_r( $vals_array);
  //die();
  
  $group_ids=array();
  foreach ($vals_array as $value) {
    $sql_gu="SELECT ug1.id_poi AS gid1, 
                 ug2.id_poi AS gid2, 
                 ug3.id_poi AS gid3, 
                 ug4.id_poi AS gid4, 
                 ug5.id_poi AS gid5,
                 ug6.id_poi AS gid6,
                 ug7.id_poi AS gid7,
                 ug8.id_poi AS gid8,
                 ug9.id_poi AS gid9,
                 ug10.id_poi AS gid10
    FROM ((((((((gks_poi as ug1
    LEFT JOIN gks_poi AS ug2 ON ug1.id_poi = ug2.poi_parent_id)
    LEFT JOIN gks_poi AS ug3 ON ug2.id_poi = ug3.poi_parent_id)
    LEFT JOIN gks_poi AS ug4 ON ug3.id_poi = ug4.poi_parent_id)
    LEFT JOIN gks_poi AS ug5 ON ug4.id_poi = ug5.poi_parent_id)
    LEFT JOIN gks_poi AS ug6 ON ug5.id_poi = ug6.poi_parent_id)
    LEFT JOIN gks_poi AS ug7 ON ug6.id_poi = ug7.poi_parent_id)
    LEFT JOIN gks_poi AS ug8 ON ug7.id_poi = ug8.poi_parent_id)
    LEFT JOIN gks_poi AS ug9 ON ug8.id_poi = ug9.poi_parent_id)
    LEFT JOIN gks_poi AS ug10 ON ug9.id_poi = ug10.poi_parent_id
    where ug1.id_poi=".$value;
    
    $result_gu = $db_link->query($sql_gu);        
    if (!$result_gu) {
      debug_mail(false,'error sql',$sql_gu);
      die('sql error');
    }
    $gu_in='';
    
    while ($row_gu = $result_gu->fetch_assoc()) {
      if (isset($row_gu['gid1']))  if (in_array($row_gu['gid1'],  $group_ids)==false) $group_ids[]=$row_gu['gid1'];
      if (isset($row_gu['gid2']))  if (in_array($row_gu['gid2'],  $group_ids)==false) $group_ids[]=$row_gu['gid2'];
      if (isset($row_gu['gid3']))  if (in_array($row_gu['gid3'],  $group_ids)==false) $group_ids[]=$row_gu['gid3'];
      if (isset($row_gu['gid4']))  if (in_array($row_gu['gid4'],  $group_ids)==false) $group_ids[]=$row_gu['gid4'];
      if (isset($row_gu['gid5']))  if (in_array($row_gu['gid5'],  $group_ids)==false) $group_ids[]=$row_gu['gid5'];
      if (isset($row_gu['gid6']))  if (in_array($row_gu['gid6'],  $group_ids)==false) $group_ids[]=$row_gu['gid6'];
      if (isset($row_gu['gid7']))  if (in_array($row_gu['gid7'],  $group_ids)==false) $group_ids[]=$row_gu['gid7'];
      if (isset($row_gu['gid8']))  if (in_array($row_gu['gid8'],  $group_ids)==false) $group_ids[]=$row_gu['gid8'];
      if (isset($row_gu['gid9']))  if (in_array($row_gu['gid9'],  $group_ids)==false) $group_ids[]=$row_gu['gid9'];
      if (isset($row_gu['gid10'])) if (in_array($row_gu['gid10'], $group_ids)==false) $group_ids[]=$row_gu['gid10'];
    }
  } 
  
  if (count($group_ids) >0) {
    
    if ($fgroup_100) {
      $where1_calc= ' and (gks_poi.poi_parent_id in ('.implode(',',$group_ids).') or gks_poi.poi_parent_id=0) ';  
    } else {
      $where1_calc= ' and gks_poi.poi_parent_id in ('.implode(',',$group_ids).') ';  
    }
  } else {
    if ($fgroup_100) {
      $where1_calc=' and gks_poi.poi_parent_id=0';
    }
  }

  //echo $where1_calc.'|';
  //print '<pre>';
  //print_r($vals);
  //print_r($vals_array);
  //print_r($group_ids);
  //print '</pre>';  
  //print $where1_calc;
  //echo $where1;
  //echo '</pre>';
  //die();

} 


$sorted = array('sql' => '', 'url' => '');

makeSortable($sortable, $sorted, $_GET);
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_poi.*, gks_country.country_name, gks_nomoi.nomos_descr,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
gks_poi_type.poi_type_descr,
ccsubpoi.ccc,

ug2.poi_descr AS gt2, 
ug3.poi_descr AS gt3, 
ug4.poi_descr AS gt4, 
ug5.poi_descr AS gt5, 
ug6.poi_descr AS gt6, 
ug7.poi_descr AS gt7, 
ug8.poi_descr AS gt8, 
ug9.poi_descr AS gt9, 
ug10.poi_descr AS gt10, 


ug2.id_poi AS id2, 
ug3.id_poi AS id3, 
ug4.id_poi AS id4, 
ug5.id_poi AS id5,
ug6.id_poi AS id6,
ug7.id_poi AS id7,
ug8.id_poi AS id8,
ug9.id_poi AS id9,
ug10.id_poi AS id10,

CONCAT_WS('\\\\',
                 ug10.poi_descr,
                 ug9.poi_descr,
                 ug8.poi_descr,
                 ug7.poi_descr,
                 ug6.poi_descr,
                 ug5.poi_descr,
                 ug4.poi_descr,
                 ug3.poi_descr,
                 ug2.poi_descr,
                 gks_poi.poi_descr) as fullpath,
CONCAT_WS('\\\\',
                 ug10.poi_descr,
                 ug9.poi_descr,
                 ug8.poi_descr,
                 ug7.poi_descr,
                 ug6.poi_descr,
                 ug5.poi_descr,
                 ug4.poi_descr,
                 ug3.poi_descr,
                 ug2.poi_descr) as dirpath,
table_pricelistcount.pricelistcount,
table_diadromescount.diadromescount

".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." ((((((((((((((((gks_poi 
".$gks_custom_prepare['sql_all_list_left']."
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_poi.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_poi.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN gks_poi_type ON gks_poi.poi_type_id = gks_poi_type.id_poi_type) 
LEFT JOIN gks_country ON gks_poi.poi_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_poi.poi_nomos_id = gks_nomoi.id_nomos)

LEFT JOIN (
  SELECT gks_poi.poi_parent_id, Count(gks_poi.id_poi) AS ccc
  FROM gks_poi
  GROUP BY gks_poi.poi_parent_id
) AS ccsubpoi ON gks_poi.id_poi = ccsubpoi.poi_parent_id)

LEFT JOIN gks_poi AS ug2 ON gks_poi.poi_parent_id = ug2.id_poi) 
LEFT JOIN gks_poi AS ug3 ON ug2.poi_parent_id = ug3.id_poi)
LEFT JOIN gks_poi AS ug4 ON ug3.poi_parent_id = ug4.id_poi)
LEFT JOIN gks_poi AS ug5 ON ug4.poi_parent_id = ug5.id_poi)
LEFT JOIN gks_poi AS ug6 ON ug5.poi_parent_id = ug6.id_poi)
LEFT JOIN gks_poi AS ug7 ON ug6.poi_parent_id = ug7.id_poi)
LEFT JOIN gks_poi AS ug8 ON ug7.poi_parent_id = ug8.id_poi)
LEFT JOIN gks_poi AS ug9 ON ug8.poi_parent_id = ug9.id_poi)
LEFT JOIN gks_poi AS ug10 ON ug9.poi_parent_id = ug10.id_poi)

LEFT JOIN (
  select my_point,sum(cc) as pricelistcount
  from (
    SELECT poi_id_from as my_point, Count(id_transfer_pricelist) AS cc
    FROM gks_transfer_pricelist
    WHERE transfer_pricelist_disable=0
    GROUP BY poi_id_from
    union
    SELECT poi_id_to as my_point, Count(id_transfer_pricelist) AS cc
    FROM gks_transfer_pricelist
    WHERE transfer_pricelist_disable=0
    GROUP BY poi_id_to
  ) as table_mycc
  group by my_point
) as table_pricelistcount ON gks_poi.id_poi=table_pricelistcount.my_point)

LEFT JOIN (
  select my_point,sum(cc) as diadromescount
  from (
    SELECT poi_id_from as my_point, Count(id_poi_diadromes) AS cc
    FROM gks_poi_diadromes
    WHERE poi_diadromes_disable=0
    GROUP BY poi_id_from
    union
    SELECT poi_id_to as my_point, Count(id_poi_diadromes) AS cc
    FROM gks_poi_diadromes
    WHERE poi_diadromes_disable=0
    GROUP BY poi_id_to
  ) as table_mycc
  group by my_point
) as table_diadromescount ON gks_poi.id_poi=table_diadromescount.my_point

where 1=1 " .$where . $where1_calc . $search_where;
if (count($perm_id_poi_ids)>0) $sql.=" and gks_poi.id_poi in (".implode(',',$perm_id_poi_ids).")";

if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_poi.poi_sortorder, gks_poi.poi_descr";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo '<pre>'.$sql;die();
	
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
      <a class="btn btn-primary gks_add_new_record" href="admin-poi-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέου σημείου');?></a>
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gks_poi">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotitle', gks_lang('Περιγραφή')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotype', gks_lang('Τύπος')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soparent', gks_lang('Γονικό Σημείο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soccc', gks_lang('Πλήθος<br>ΥποΣημείων')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo gks_lang('Συνολικό<br>Πλήθος<br>ΥποΣημείων');?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopricelistcount', gks_lang('Πλήθος<br>Τιμοκαταλόγων')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodiadromescount', gks_lang('Πλήθος<br>Διαδρομών')); ?></th>        
    
    
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Χρώμα');?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophone', gks_lang('Τηλέφωνο')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soemail', 'email'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soodos', gks_lang('Οδός')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soperioxi', gks_lang('Περιοχή')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopoli', gks_lang('Πόλη')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sotk', 'ΤΚ'); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%"  nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonomos', gks_lang('Νομός')); ?></th>   
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="10%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry', gks_lang('Χώρα')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Στίγμα');?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodisable', gks_lang('Ενεργό')); ?></th>   
<?php if ($perm_edit) {?>
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sosort', '<span class="tooltipster" title="'.gks_lang('Σειρά Ταξινόμησης').'">'.gks_lang('ΣειράΤ').'</span>'); ?></th>        
<?php } ?>
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
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_poi'];?>">
    <th scope="row" class="mytdcm"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-poi-item.php?id=<?php echo $row['id_poi'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_poi'];?></td>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_poi'];?>" data-model="gks_poi"></i></td>
        </tr>      
      </table>
    </td>

    <td class="mytdcml"><?php echo $row['poi_descr'];?></td>
    <td class="mytdcml"><?php echo $row['poi_type_descr'];?></td>
    <td class="mytdcml"><?php echo $row['dirpath'];?></td>
    <td class="mytdcm" nowrap><?php if ($row['ccc']>0) echo number_format($row['ccc'], 0, ',', '.');?></td>  
    <td class="mytdcm" nowrap><?php
      $gu_num=0;
      
      $sql="SELECT 
      ug1.id_poi AS gid1, 
      ug2.id_poi AS gid2, 
      ug3.id_poi AS gid3, 
      ug4.id_poi AS gid4, 
      ug5.id_poi AS gid5,
      ug6.id_poi AS gid6,
      ug7.id_poi AS gid7,
      ug8.id_poi AS gid8,
      ug9.id_poi AS gid9,
      ug10.id_poi AS gid10
      FROM ((((((((gks_poi AS ug1 
      LEFT JOIN gks_poi AS ug2 ON ug1.id_poi = ug2.poi_parent_id) 
      LEFT JOIN gks_poi AS ug3 ON ug2.id_poi = ug3.poi_parent_id) 
      LEFT JOIN gks_poi AS ug4 ON ug3.id_poi = ug4.poi_parent_id) 
      LEFT JOIN gks_poi AS ug5 ON ug4.id_poi = ug5.poi_parent_id)
      LEFT JOIN gks_poi AS ug6 ON ug5.id_poi = ug6.poi_parent_id)
      LEFT JOIN gks_poi AS ug7 ON ug6.id_poi = ug7.poi_parent_id)
      LEFT JOIN gks_poi AS ug8 ON ug7.id_poi = ug8.poi_parent_id)
      LEFT JOIN gks_poi AS ug9 ON ug8.id_poi = ug9.poi_parent_id)
      LEFT JOIN gks_poi AS ug10 ON ug9.id_poi = ug10.poi_parent_id
      
      where ug1.id_poi=".$row['id_poi'];
      $result_gu = $db_link->query($sql);        
      if (!$result_gu) {
        debug_mail(false,'error sql',$sql);
        die('sql error');
      }
      $gu_in='';
      
      while ($row_gu = $result_gu->fetch_assoc()) {
        if (isset($row_gu['gid1'])) $gu_in.=$row_gu['gid1'].',';
        if (isset($row_gu['gid2'])) $gu_in.=$row_gu['gid2'].',';
        if (isset($row_gu['gid3'])) $gu_in.=$row_gu['gid3'].',';
        if (isset($row_gu['gid4'])) $gu_in.=$row_gu['gid4'].',';
        if (isset($row_gu['gid5'])) $gu_in.=$row_gu['gid5'].',';
        if (isset($row_gu['gid6'])) $gu_in.=$row_gu['gid6'].',';
        if (isset($row_gu['gid7'])) $gu_in.=$row_gu['gid7'].',';
        if (isset($row_gu['gid8'])) $gu_in.=$row_gu['gid8'].',';
        if (isset($row_gu['gid9'])) $gu_in.=$row_gu['gid9'].',';
        if (isset($row_gu['gid10'])) $gu_in.=$row_gu['gid10'].',';
      }
      if (strlen($gu_in)>0) $gu_in=substr($gu_in, 0, strlen($gu_in)-1);
      if (strlen($gu_in)>0) {
        $sql="SELECT count(Distinct id_poi) as ccc2 FROM gks_poi WHERE poi_parent_id In (".$gu_in.")";
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
    <td class="mytdcm" nowrap><?php if ($row['pricelistcount']>0) echo number_format($row['pricelistcount'], 0, ',', '.');?></td>
    <td class="mytdcm" nowrap><?php if ($row['diadromescount']>0) echo number_format($row['diadromescount'], 0, ',', '.');?></td>

     
    <td class="mytdcm" style="background-color: <?php echo $row['poi_color'];?>"></td>
    <td class="mytdcml"><?php echo $row['poi_phone'];?></td>
    <td class="mytdcml"><?php echo $row['poi_email'];?></td>
    <td class="mytdcml"><?php echo $row['poi_odos'].' '.$row['poi_arithmos'];?></td>
    <td class="mytdcml"><?php echo $row['poi_perioxi'];?></td>
    <td class="mytdcml"><?php echo $row['poi_poli'];?></td>
    <td class="mytdcml"><?php echo $row['poi_tk'];?></td>
    <td class="mytdcml"><?php echo $row['nomos_descr'];?></td>
    <td class="mytdcml"><?php echo $row['country_name'];?></td>
    <td nowrap class="mytdcm"><?php 
      if ($row['poi_map_latitude']==0 and $row['poi_map_longitude']==0) {
        $pos_poi=0;
      } else {
        $pos_poi=1;
      }?>
      <img src="img/<?php echo $pos_poi;?>.png" border="0" width="16" title="<?php echo gks_lang('Στίγμα');?>">
    <?php
      if ($row['poi_bound_north']==0) {
        $pos_poi=0;
      } else {
        $pos_poi=1;
      }?>
      <img src="img/<?php echo $pos_poi;?>.png" border="0" width="16" title="<?php echo gks_lang('Σχήματα');?>">

    </td>
    <td nowrap class="mytdcm"><?php echo myimg010r($row['poi_disable']);?></td> 
    
<?php if ($perm_edit) {?>
    <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['poi_sortorder'];?>">
      <i class="fas fa-arrows-alt-v"></i>
      <span><?php echo $row['poi_sortorder'];?></span>
    </td>
<?php } ?>
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
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_poi','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_poi','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_poi','delete',0);?>;

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

  $('#table_gks_poi > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_poi',mylist,'#table_gks_poi > tbody');
    }
  }); 
  
});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');

