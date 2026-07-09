<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$my_page_title=gks_lang('Υπόλοιπα Ειδών');
$nav_active_array=array('warehouse','warehouse_balance');





db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov_balance','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}











$sql="select gks_warehouses.*,
gks_company.company_title, gks_company_subs.company_sub_title
from (gks_warehouses
LEFT JOIN gks_company ON gks_warehouses.company_id = gks_company.id_company)
LEFT JOIN gks_company_subs ON gks_warehouses.company_sub_id = gks_company_subs.id_company_sub
where gks_warehouses.is_virtual=0
order by warehouse_sortorder,warehouse_name,id_warehouse";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  
$warehouses=array();
while ($row = $result->fetch_assoc()) {
  $warehouses[]=array(
    'id' => $row['id_warehouse'],
    'field' => 'wh_'.$row['id_warehouse'],
    'name' =>  $row['warehouse_name'],
    'title' =>  mb_substr($row['warehouse_name'],0,50),
    'spantitle' => $row['warehouse_name'].'<br>'.
    (trim_gks($row['warehouse_poli'])=='' ? '' : trim_gks($row['warehouse_poli']).'<br>').
    $row['company_title'].'<br>'.
    ($row['company_sub_id']==0 ? gks_lang('Κεντρικό') : $row['company_sub_title'])
    
  );
}
//print '<pre>';print_r($warehouses);die();

$filters = array();

$filters[] = array(
    'name' => 'fskip',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Προβολή'),
    'has_custom_default' => 1,
    //'multiselect' => true,
    'field'  => "%V%",
    'mywherepos'=>10,
    'vals' => array(
        array('value' => 1,  'text' => gks_lang('Όπου υπάρχει υπόλοιπο'),  'sql' => "1"),
        array('value' => 2,  'text' => gks_lang('Όλες οι αποθήκες'),  'sql' => "2"),
        array('value' => 3,  'text' => gks_lang('Όλα τα είδη'),  'sql' => "3"),
        array('value' => 4,  'text' => gks_lang('Όλες οι αποθήκες και όλα τα είδη'),  'sql' => "1=1"),
    ),

);

$fitems=array();
foreach ($warehouses as $witem) {
  $fitems[]=array('value' => $witem['field'], 'text' => $witem['name'],      'sql' => $witem['id']);
}
$filters[] = array(
    'name' => 'fwarehouse',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αποθήκη'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "%V%",
    'mywherepos' =>3,
    'vals' => $fitems,
);


$filters[] = array(
    'name' => 'fclass',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Βασικός τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_class = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Απλό'),       'sql' => "gks_eshop_products.product_class='simple'"),
        array('value' => 101, 'text' => gks_lang('Παραλλαγή'), 'sql' => "gks_eshop_products.product_class='variable_item'"),
    ),
);


$filters[] = array(
    'name' => 'fgroup',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατηγορία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "%V%,",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς Κατηγορία'),          'sql' => "nocategory"),
    ),
    'mywherepos' =>1,
    'sql' => "select gks_eshop_products_categories.id_product_category as id,
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
                    gks_eshop_products_categories.product_category_descr) as descr
    FROM ((((((((gks_eshop_products_categories
    LEFT JOIN gks_eshop_products_categories AS ug2  ON gks_eshop_products_categories.product_category_parent_id = ug2.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.product_category_parent_id = ug3.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.product_category_parent_id = ug4.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.product_category_parent_id = ug5.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.product_category_parent_id = ug6.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.product_category_parent_id = ug7.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.product_category_parent_id = ug8.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.product_category_parent_id = ug9.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
    ORDER BY descr",
);

$filters[] = array(
    'name' => 'fbrand',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μάρκα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "%V%,",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -100, 'text' => gks_lang('Χωρίς Μάρκα'),  'sql' => "nobrand"),
    ),
    'mywherepos' =>2,
    'sql' => "select gks_eshop_products_brands.id_product_brand as id,
    CONCAT_WS('\\\\',
                    ug10.product_brand_descr,
                    ug9.product_brand_descr,
                    ug8.product_brand_descr,
                    ug7.product_brand_descr,
                    ug6.product_brand_descr,
                    ug5.product_brand_descr,
                    ug4.product_brand_descr,
                    ug3.product_brand_descr,
                    ug2.product_brand_descr,
                    gks_eshop_products_brands.product_brand_descr) as descr
    FROM ((((((((gks_eshop_products_brands
    LEFT JOIN gks_eshop_products_brands AS ug2  ON gks_eshop_products_brands.product_brand_parent_id = ug2.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.product_brand_parent_id = ug3.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.product_brand_parent_id = ug4.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.product_brand_parent_id = ug5.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.product_brand_parent_id = ug6.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.product_brand_parent_id = ug7.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.product_brand_parent_id = ug8.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.product_brand_parent_id = ug9.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.product_brand_parent_id = ug10.id_product_brand
    ORDER BY descr",
);



$filters[] = array(
    'name' => 'fdisable',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ενεργό'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_disable = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ενεργό'),      'sql' => "gks_eshop_products.product_disable=0"),
        array('value' => 101, 'text' => gks_lang('Μη ενεργό'),   'sql' => "gks_eshop_products.product_disable<>0"),
    ),
);

$filters[] = array(
    'name' => 'fcan_sell',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μπορεί να πουληθεί'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_can_sell = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ναι'),      'sql' => "gks_eshop_products.product_can_sell<>0"),
        array('value' => 101, 'text' => gks_lang('Όχι'),      'sql' => "gks_eshop_products.product_can_sell=0"),
    ),
);
$filters[] = array(
    'name' => 'fcan_buy',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μπορεί να αγορασθεί'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_can_buy = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ναι'),      'sql' => "gks_eshop_products.product_can_buy<>0"),
        array('value' => 101, 'text' => gks_lang('Όχι'),      'sql' => "gks_eshop_products.product_can_buy=0"),
    ),
);
$filters[] = array(
    'name' => 'fmm',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Μονάδα Μέτρησης'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_monada_id = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
//        array('value' => 100, 'text' => gks_lang('Ναι'),      'sql' => "gks_eshop_products.product_price_include_vat<>0"),
//        array('value' => 101, 'text' => gks_lang('Όχι'),      'sql' => "gks_eshop_products.product_price_include_vat=0"),
    ),
    'sql' => "SELECT id_monada as id,monada_descr as descr
              FROM gks_monades_metrisis
              ORDER BY monada_sortorder,monada_descr",    
);

$filters[] = array(
    'name' => 'fservice',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Τύπος'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_base_type = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 0, 'text' => gks_product_base_type_descr(0,true),'sql' => "gks_eshop_products.product_base_type=0"),
        array('value' => 1, 'text' => gks_product_base_type_descr(1,true),'sql' => "gks_eshop_products.product_base_type=1"),
//        array('value' => 2, 'text' => gks_product_base_type_descr(2,true),'sql' => "gks_eshop_products.product_base_type=2"),

    ),
);

$filters[] = array(
    'name' => 'fneed_apostoli',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Χρειάζεται αποστολή'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_need_apostoli = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ναι'),      'sql' => "gks_eshop_products.product_need_apostoli<>0"),
        array('value' => 101, 'text' => gks_lang('Όχι'),      'sql' => "gks_eshop_products.product_need_apostoli=0"),
    ),
);








$sortable = array(
  array('name' => 'soid', 'field' => 'gks_eshop_products.id_product'),
  
  array('name' => 'sophoto', 'field' => 'product_photo_p'),
  array('name' => 'socode', 'field' => 'gks_eshop_products.product_code'),
  array('name' => 'sodescr', 'field' => 'product_descr_p'),
  array('name' => 'socansell', 'field' => 'gks_eshop_products.product_can_sell'),
  array('name' => 'socanbuy', 'field' => 'gks_eshop_products.product_can_buy'),
  array('name' => 'soorder', 'field' => 'gks_eshop_products.product_sortorder'),
  array('name' => 'sodisable', 'field' => 'gks_eshop_products.product_disable'),
  array('name' => 'sois_digital', 'field' => 'gks_eshop_products.product_is_digital'),
  array('name' => 'sois_simple_download', 'field' => 'gks_eshop_products.product_is_simple_download'),
  array('name' => 'sobtype', 'field' => 'gks_eshop_products.product_base_type'),
  array('name' => 'soneed_apostoli', 'field' => 'gks_eshop_products.product_need_apostoli'),
  array('name' => 'soneed_multi_files', 'field' => 'gks_eshop_products.product_need_multi_files,gks_eshop_products.product_need_multi_files_min,gks_eshop_products.product_need_multi_files_max'),
  array('name' => 'sovaros', 'field' => 'gks_eshop_products.product_varos'),
  array('name' => 'soogos', 'field' => 'gks_eshop_products.product_ogos_x,gks_eshop_products.product_ogos_y,gks_eshop_products.product_ogos_z'),
  
  array('name' => 'somm', 'field' => 'gks_monades_metrisis.monada_symbol'),
  array('name' => 'sowtotal', 'field' => 'mybalance.balance'),
  
);
        
foreach ($warehouses as $witem) {
  $sortable[]=array('name' => 'sow'.$witem['id'], 'field' => 'mybalance.balance');
}

$search_fields = array(
'gks_eshop_products.product_code',
'gks_eshop_products_parent.product_code',
'gks_eshop_products.product_descr',
'gks_eshop_products.product_descr_variable',
'gks_eshop_products_parent.product_descr',
'gks_eshop_products.product_descr_small',
'gks_eshop_products.product_descr_big',
'gks_eshop_products.product_object_name',


);




$filter = array('html' => '', 'sql' => '', 'url' => '');
$search_string_value = (isset($_GET['search_string']) ? $_GET['search_string'] : '');
makeFilters($filters, $filter, $_GET,true,true,$search_string_value);




$search_where = make_search_where($search_string_value,$search_fields);
$search_where = !empty($search_where) ? ' AND '.$search_where : '';
//echo $search_where;
//die();

$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';
$where2 = isset($filter['sql2']) ? ' AND '.$filter['sql2'] : '';
$where3 = isset($filter['sql3']) ? $filter['sql3'] : '';
$sql_FROM_1='';
$sql_FROM_2='';



$where3=trim_gks($where3);
$where3_array=array();
if (strlen($where3) > 2) {
  $where3=substr($where3, 1, strlen($where3)-2);
  $parts=explode(' or ',$where3);
  foreach ($parts as $value) {
    $value=trim_gks($value);
    $value=intval($value);
    if ($value>0) $where3_array[]=$value;
  } 
  //echo '<pre>';print $where3;print_r($parts); print_r($where3_array); print '</pre>';//die();
}


$fskip=1;if (isset($_GET['fskip'])) $fskip=intval($_GET['fskip']);
//warehouses
if ($fskip==1 or $fskip == 3) {
  
  
  $sql="SELECT warehouse_id
  FROM gks_warehouse_balance_eidi
  WHERE balance<>0 AND warehouse_id>2
  GROUP BY warehouse_id;";
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
  
  $ware_not_zero=array();
  while ($row = $result->fetch_assoc()) {
    $ware_not_zero[]=$row['warehouse_id'];
  }
  $ware_zero=array();
  foreach ($warehouses as $witem) {
    if (in_array($witem['id'],$ware_not_zero)==false) $ware_zero[]=$witem['id'];
  } 

  if (count($where3_array)==0) {
    $where3_array=$ware_not_zero;
  } else {
    $where3_array=array_diff($where3_array, $ware_zero);
    if (count($where3_array)==0) $where3_array[]=-1; //gia na exei kati mesa, gia na leitourgisei to filtro
  }
    
  
//    echo '<pre>warehouses ';print $fskip; print "\n"; print $sql;
//    print 'ware_zero'."\n";
//    print_r($ware_zero); 
//    print 'ware_not_zero'."\n";
//    print_r($ware_not_zero); 
//    print 'where3_array'."\n";
//    print_r($where3_array); 
//    echo '</pre>';

  
}

//products
$sql_filter_product='';
if ($fskip==1 or $fskip==2) {
  //echo '<pre>products ';print $fskip;echo '</pre>';
  
  $sql_filter_product=" and gks_eshop_products.id_product in (
    SELECT product_id
    FROM gks_warehouse_balance_eidi
    WHERE balance<>0 AND warehouse_id>2
    GROUP BY product_id
  )";
}





  
$sql_FROM_cat_1='';
$sql_FROM_cat_2='';
$where_cat_calc='';
if ($where1!='') {
  $fgroup_100=false;
  if (strpos($where1, 'nocategory') !== false) $fgroup_100=true;
  $where1=str_replace('nocategory or ', '', $where1);
  
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
    $sql_gu="SELECT ug1.id_product_category AS gid1, 
                 ug2.id_product_category AS gid2, 
                 ug3.id_product_category AS gid3, 
                 ug4.id_product_category AS gid4, 
                 ug5.id_product_category AS gid5,
                 ug6.id_product_category AS gid6,
                 ug7.id_product_category AS gid7,
                 ug8.id_product_category AS gid8,
                 ug9.id_product_category AS gid9,
                 ug10.id_product_category AS gid10
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
    where ug1.id_product_category=".$value;
    
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
      $sql_FROM_cat_1='((';
      $sql_FROM_cat_2='LEFT JOIN (
        SELECT product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_categories_products 
          WHERE product_category_id In ('.implode(',',$group_ids).')
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_categories_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_categories_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products_categories_products.product_category_id In ('.implode(',',$group_ids).') 
          AND gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pcat1
        GROUP BY product_id   
      ) as products_categories_subq on gks_eshop_products.id_product = products_categories_subq.product_id)
      LEFT JOIN (
        SELECT product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_categories_products 
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_categories_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_categories_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products.product_parent_id Is Not Null          
        ) as tabletemp_pcat2        
        GROUP BY product_id  
      ) as products_categories_subq_all on gks_eshop_products.id_product = products_categories_subq_all.product_id)';
      $where_cat_calc= ' and (products_categories_subq.product_id is not null or products_categories_subq_all.product_id is null) ';  
      //echo '<pre>'.$sql_FROM_cat_2."\n\n".$where_cat_calc;die();
    } else {
      $sql_FROM_cat_1='(';
      $sql_FROM_cat_2='LEFT JOIN (
        select product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_categories_products 
          WHERE product_category_id In ('.implode(',',$group_ids).')
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_categories_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_categories_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products_categories_products.product_category_id In ('.implode(',',$group_ids).') 
          AND gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pcat
        GROUP BY product_id  
      ) as products_categories_subq on gks_eshop_products.id_product = products_categories_subq.product_id)';
      $where_cat_calc= ' and (products_categories_subq.product_id is not null) ';  
      //echo '<pre>'.$sql_FROM_cat_2."\n\n".$where_cat_calc;die();
      
    }
  } else {
    if ($fgroup_100) {
      $sql_FROM_cat_1='(';
      $sql_FROM_cat_2='LEFT JOIN (
        select product_id
        from (
          SELECT product_id 
          FROM gks_eshop_products_categories_products 
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_categories_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_categories_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products.product_parent_id Is Not Null          
        ) as tabletemp_pcat
        GROUP BY product_id  
      ) as products_categories_subq_all on gks_eshop_products.id_product = products_categories_subq_all.product_id)';
      $where_cat_calc= ' and (products_categories_subq_all.product_id is null) ';  
      //echo '<pre>'.$sql_FROM_cat_2."\n\n".$where_cat_calc;die();
    }
  }
  
//  print '<pre>';
//  print_r($vals);
//  print_r($vals_array);
//  print_r($group_ids);
//  //print '</pre>';  
//  print $sql_FROM_bra_1."\n";
//  print $sql_FROM_bra_2."\n";
//  print $where_cat_calc."\n";
//  echo $where1;
//  die();

}


$sql_FROM_bra_1='';
$sql_FROM_bra_2='';
$where_bra_calc='';
if ($where2!='') {
  $fgroup_100=false;
  if (strpos($where2, 'nobrand') !== false) $fgroup_100=true;
  $where2=str_replace('nobrand or ', '', $where2);
  
  $where2=trim_gks($where2);
  $where2=substr($where2, 6);
  $where2=substr($where2, 0, strlen($where2) - 2);
  $where2=str_replace(', or ', ',', $where2);
  $vals=explode(',', $where2);
  
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
    $sql_gu="SELECT ug1.id_product_brand AS gid1, 
                 ug2.id_product_brand AS gid2, 
                 ug3.id_product_brand AS gid3, 
                 ug4.id_product_brand AS gid4, 
                 ug5.id_product_brand AS gid5,
                 ug6.id_product_brand AS gid6,
                 ug7.id_product_brand AS gid7,
                 ug8.id_product_brand AS gid8,
                 ug9.id_product_brand AS gid9,
                 ug10.id_product_brand AS gid10
    FROM ((((((((gks_eshop_products_brands AS ug1
    LEFT JOIN gks_eshop_products_brands AS ug2  ON ug1.id_product_brand = ug2.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.id_product_brand = ug3.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.id_product_brand = ug4.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.id_product_brand = ug5.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.id_product_brand = ug6.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.id_product_brand = ug7.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.id_product_brand = ug8.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.id_product_brand = ug9.product_brand_parent_id)
    LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.id_product_brand = ug10.product_brand_parent_id
    where ug1.id_product_brand=".$value;
    
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
      $sql_FROM_bra_1='((';
      $sql_FROM_bra_2='LEFT JOIN (
        SELECT product_id
        from (
          select product_id
          FROM gks_eshop_products_brands_products 
          WHERE product_brand_id In ('.implode(',',$group_ids).')
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_brands_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products_brands_products.product_brand_id In ('.implode(',',$group_ids).')
          AND gks_eshop_products.product_parent_id Is Not Null        
        ) as tabletemp_pbra1
        GROUP BY product_id   
      ) as products_brands_subq on gks_eshop_products.id_product = products_brands_subq.product_id)
      LEFT JOIN (
        SELECT product_id
        from (
          SELECT product_id 
          FROM gks_eshop_products_brands_products 
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_brands_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pbra2
        GROUP BY product_id  
      ) as products_brands_subq_all on gks_eshop_products.id_product = products_brands_subq_all.product_id)';
      $where_bra_calc= ' and (products_brands_subq.product_id is not null or products_brands_subq_all.product_id is null) ';  
      //echo '<pre>'.$sql_FROM_bra_2."\n\n".$where_bra_calc;die();
    } else {
      $sql_FROM_bra_1='(';
      $sql_FROM_bra_2='LEFT JOIN (
        SELECT product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_brands_products 
          WHERE product_brand_id In ('.implode(',',$group_ids).')
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_brands_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products_brands_products.product_brand_id In ('.implode(',',$group_ids).')
          AND gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pbra
        GROUP BY product_id  
      ) as products_brands_subq on gks_eshop_products.id_product = products_brands_subq.product_id)';
      $where_bra_calc= ' and (products_brands_subq.product_id is not null) ';  
      //echo '<pre>'.$sql_FROM_bra_2."\n\n".$where_bra_calc;die();
      
    }
  } else {
    if ($fgroup_100) {
      $sql_FROM_bra_1='(';
      $sql_FROM_bra_2='LEFT JOIN (
        SELECT product_id 
        from (
          SELECT product_id 
          FROM gks_eshop_products_brands_products 
          union
          SELECT gks_eshop_products.id_product as product_id
          FROM gks_eshop_products_brands_products 
          LEFT JOIN gks_eshop_products ON gks_eshop_products_brands_products.product_id = gks_eshop_products.product_parent_id
          WHERE gks_eshop_products.product_parent_id Is Not Null
        ) as tabletemp_pbra
        GROUP BY product_id  
      ) as products_brands_subq_all on gks_eshop_products.id_product = products_brands_subq_all.product_id)';
      $where_bra_calc= ' and (products_brands_subq_all.product_id is null) ';  
      //echo '<pre>'.$sql_FROM_bra_2."\n\n".$where_bra_calc;die();
    }
  }
  
  //print '<pre>';
  //print_r($vals);
  //print_r($vals_array);
  //print_r($group_ids);
  //print '</pre>';  
  //print $where_bra_calc;
  //echo $where2;
  //die();

} 
  
$sql_FROM_balance_1='';
$sql_FROM_balance_2='';
$sort_warehouseid=-1;
if (isset($_GET['sowtotal'])) {
  $sort_warehouseid=0;
} else {
  foreach ($_GET as $kvf => $vg) {
    if (strlen($kvf)>=3 and substr($kvf,0,3)=='sow') {
      $rr=substr($kvf, 3);
      if (ctype_digit($rr)) {
        $sort_warehouseid=intval($rr);
        break;
      }
    }
  }
}

if ($sort_warehouseid>=0) { 
  $sql_FROM_balance_1='(';
  $sql_FROM_balance_2="
  left join (
    select product_id,balance from gks_warehouse_balance_eidi where warehouse_id=".$sort_warehouseid."
  ) as mybalance ON gks_eshop_products.id_product = mybalance.product_id)
";
}


$sorted = array('sql' => '', 'url' => '');

makeSortable($sortable, $sorted, $_GET);
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;


//SELECT gks_eshop_products.id_product, gks_eshop_products.product_parent_id, gks_eshop_products.product_class, gks_eshop_products.product_descr, gks_eshop_products_parent.product_descr
//FROM gks_eshop_products 
//LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product;


//DISTINCTROW
$sql = "SELECT SQL_CALC_FOUND_ROWS 
gks_eshop_products.id_product,
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
gks_eshop_products.product_disable,
gks_monades_metrisis.monada_symbol,gks_monades_metrisis.monada_descr


FROM  ".$sql_FROM_cat_1." ".$sql_FROM_bra_1." ".$sql_FROM_balance_1." (gks_eshop_products

LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)


".$sql_FROM_cat_2."
".$sql_FROM_bra_2."
".$sql_FROM_balance_2."
LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada

where gks_eshop_products.id_product Is Not Null
and gks_eshop_products.product_base_type in (0,1)
and gks_eshop_products.product_parent_old_id=0 
".$sql_filter_product."
".$where . $where_cat_calc . $where_bra_calc . $search_where;
      
//LEFT JOIN gks_eshop_products_categories_products ON gks_eshop_products.id_product = gks_eshop_products_categories_products.product_id)


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_eshop_products.product_code, gks_eshop_products.product_descr, gks_eshop_products.id_product desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo '<pre>';
//echo $where1;
//echo $where2;
//echo "\r\n";
//echo $sql;
//echo '</pre>';
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
<link href="css/admin-whi-mov-balance.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
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
  ?>" border="0" cellspacing="0" cellpadding="5" align="center" id="lightgallery_user">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sophoto', gks_lang('Φωτό')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socode', gks_lang('Κωδικός')); ?></th>        
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="40%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodescr', gks_lang('Περιγραφή')); ?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'somm', '<span class="tooltipsterfast" title="'.gks_lang('Μονάδα Μέτρησης').'">'.gks_lang('ΜΜ').'</span>'); ?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowtotal', gks_lang('Σύνολο')); ?></th>        

<?php
$ccww=0;
foreach ($warehouses as $witem) {
  if (count($where3_array)==0 or in_array($witem['id'],$where3_array)) {
    $ccww++;  
  }
}
$mywidth='0';
if ($ccww>0) $mywidth=number_format(50/$ccww,2,'.','');

foreach ($warehouses as $witem) {
  if (count($where3_array)==0 or in_array($witem['id'],$where3_array)) {
    echo '<th class="table-dark" scope="col" style="text-align: center !important;" width="'.$mywidth.'%" >'.makeSortLink($sortable, $sortable_url, $_GET, 'sow'.$witem['id'], '<span class="tooltipsterfast" title="'.$witem['spantitle'].'" id="warehouse_title_'.$witem['id'].'">'.$witem['title'].'</span>').'</th>';
  }
}

?>

      
    </tr>
</thead>
<tbody>
  
    <?php
    $row_array=[];
    $ids=[];
    while ($row = $result->fetch_assoc()) {
      $row_array[]=$row;
      $ids[]=$row['id_product'];
    }
    $bbb=[];
    if (count($ids)>0) {
      $sql="select * from gks_warehouse_balance_eidi where product_id in (".implode(',',$ids).")";
      //echo '<pre>'.$sql;die();
       
      $result = $db_link->query($sql);        
      if (!$result) debug_mail(false,'error sql',$sql);
      if (!$result) die('sql error');
      while ($row = $result->fetch_assoc()) {
        if (isset($bbb[$row['product_id']])==false) {
          $bbb[$row['product_id']]=array(
            'warehouses'=>array(),
          );
        }
        if (isset($bbb[$row['product_id']]['warehouses'][$row['warehouse_id']])==false) {
          $bbb[$row['product_id']]['warehouses'][$row['warehouse_id']]=0;
        }
        $bbb[$row['product_id']]['warehouses'][$row['warehouse_id']]+=$row['balance'];
         
      }
    }
    //print '<pre>';print_r($bbb);die();
    
    $i = 0;
    foreach ($row_array as $row) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>

    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-products-item.php?id=<?php echo $row['id_product'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_product'];?></td>
        </tr>      
      </table>
    </td>


    <td class="tdimg"><?php 
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
      echo '<a class="lightgalleryitem_user" href="'.$photo_url.'" data-sub-html="'.$row['product_code'].'"><img style="max-width:64px;max-height:64px;" src="'.$myimgurl.'"></a>';
    }
    ?></td>
    <td nowrap class="mytdcml"><?php echo $row['product_code'];?></td>
    <td id="product_title_<?php echo $row['id_product'];?>"><?php echo $row['product_descr_p'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['monada_symbol'];?></td> 
    <td nowrap class="mytdcm mytdtotal" 
      data-product="<?php echo $row['id_product'];?>"><?php 
      
      $this_bal=0;
      if (isset($bbb[$row['id_product']]['warehouses'][0])) {
        $this_bal=$bbb[$row['id_product']]['warehouses'][0];
      }      
      if ($this_bal!=0) echo myNumberFormatNo0Local($this_bal);
            
      ?></td> 
<?php    
foreach ($warehouses as $witem) {
  if (count($where3_array)==0 or in_array($witem['id'],$where3_array)) {
    $this_bal=0;
    if (isset($bbb[$row['id_product']]['warehouses'][$witem['id']])) {
      $this_bal=$bbb[$row['id_product']]['warehouses'][$witem['id']];
    }
    
    echo '<td nowrap class="mytdcm mytdypoloipo" data-warehouse="'.$witem['id'].'" '.
    'data-product="'.$row['id_product'].'" '.
    'data-val="'.myNumberFormatNo0($this_bal).'">'.
    ($this_bal==0 ? '' : myNumberFormatNo0($this_bal)).
    '</td>';
 
  }
}




?>
    
    
  </tr>
<?php    
    }
?>

</tbody>
</table>

<?php mytablepages($paging, $total_records); ?>

 
<div id="dialog_posotitaonhand" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div>
    <?php echo gks_lang('Ορίστε την ποσότητα που έχετε τώρα στο χέρι για το είδος');?>:<br>
    <b><span id="dialog_posotitaonhand_product"></span></b><br>
    <?php echo gks_lang('στην αποθήκη');?>:<br>
    <b><span id="dialog_posotitaonhand_warehouse"></span></b>
      
  </div>
  <div style="padding: 20px 0px;">
    <input id="dialog_posotitaonhand_posostita" value="" class="form-control form-control-sm" style="max-width:200px;" type="number" min="0" step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>">
  </div>  
  <div>
    <span id="dialog_posotitaonhand_history" style="color:blue;text-decoration: underline;cursor:pointer" ><?php echo gks_lang('Μετάβαση στο ιστορικό');?></span>
  </div>
</div>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 
var from_php_perm_ret_apografi_add=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_whi_mov_balance_apografi','add',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));
  

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


  

});
</script>



<script src="js/admin-whi-mov-balance.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
//db_close();
include_once('_my_footer_admin.php');


