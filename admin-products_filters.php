<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

if (isset($gks_eshop_products_extra_sqls)==false) {
  $gks_eshop_products_extra_sqls=[
    'select'=> '',
    'from'=> '',
    'left_join'=> '',
    'so'=>[],
  ];  
}

$gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_products',['from'=>'list']);


//print '<pre>';print_r($_GET); die();
$datecheck='';if (isset($_GET['datecheck'])) $datecheck=trim_gks($_GET['datecheck']);
//echo $datecheck;die();
if ($datecheck== '__/__/____ __:__') $datecheck='';
$datecheck=trim_gks(stripslashes(urldecode($datecheck)));
if ($datecheck!='') {
  $datecheck = mystrtodb($datecheck);
}

$quantitycheck=0;if (isset($_GET['quantitycheck'])) $quantitycheck= intval($_GET['quantitycheck']);
if ($quantitycheck<1) $quantitycheck=0;
$sheetscheck=0;if (isset($_GET['sheetscheck'])) $sheetscheck= intval($_GET['sheetscheck']);
if ($sheetscheck<1) $sheetscheck=0;
if ($quantitycheck>0 or $sheetscheck>0) {
  if ($quantitycheck==0) $quantitycheck=1;  
  if ($sheetscheck==0) $sheetscheck=1;
}

if ($datecheck!= '' or $quantitycheck>0 or $sheetscheck>0) {
  $sql="update gks_eshop_products set quantitycheck_price_yperx=null, quantitycheck_price=null, quantitycheck_price_retail=null, eval_error=null;"; 
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql); die('sql error');}
  
  $datecheck_sql=$datecheck; 
  if ($datecheck_sql=='') $datecheck_sql=date('Y-m-d H:i:s'); 

  $sql="update gks_eshop_products set 
  quantitycheck_price_yperx=
  if (product_price_yperx_sale=0,product_price_yperx,
    if (product_price_yperx_sale_from is null and product_price_yperx_sale_to is null , product_price_yperx_sale,
      if (product_price_yperx_sale_from is not null and product_price_yperx_sale_from<='".$datecheck_sql."' and
          product_price_yperx_sale_to   is not null and product_price_yperx_sale_to>='".$datecheck_sql."', product_price_yperx_sale,
        if (product_price_yperx_sale_from is null and
            product_price_yperx_sale_to   is not null and product_price_yperx_sale_to>='".$datecheck_sql."', product_price_yperx_sale,
          if (product_price_yperx_sale_from is not null and product_price_yperx_sale_from<='".$datecheck_sql."' and
              product_price_yperx_sale_to   is null, product_price_yperx_sale,
            product_price_yperx
          )
        )
      )
    )
  ) * ".$quantitycheck." 
        where (product_price_yperx_sheets_formula is null   or product_price_yperx_sheets_formula='') and 
              (product_price_yperx_quantity_formula is null or product_price_yperx_quantity_formula='')"; 
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql); die('sql error');}
    
  $sql="update gks_eshop_products set 
  quantitycheck_price=
  if (product_price_sale=0,product_price,
    if (product_price_sale_from is null and product_price_sale_to is null , product_price_sale,
      if (product_price_sale_from is not null and product_price_sale_from<='".$datecheck_sql."' and
          product_price_sale_to   is not null and product_price_sale_to>='".$datecheck_sql."', product_price_sale,
        if (product_price_sale_from is null and
            product_price_sale_to   is not null and product_price_sale_to>='".$datecheck_sql."', product_price_sale,
          if (product_price_sale_from is not null and product_price_sale_from<='".$datecheck_sql."' and
              product_price_sale_to   is null, product_price_sale,
            product_price
          )
        )
      )
    )
  ) * ".$quantitycheck." 
        where (product_price_sheets_formula is null   or product_price_sheets_formula='') and 
              (product_price_quantity_formula is null or product_price_quantity_formula='')"; 
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql); die('sql error');}



  
    
  
  $sql="update gks_eshop_products set 
  quantitycheck_price_retail=
    if (product_price_retail_sale=0,product_price_retail,
    if (product_price_retail_sale_from is null and product_price_retail_sale_to is null , product_price_retail_sale,
      if (product_price_retail_sale_from is not null and product_price_retail_sale_from<='".$datecheck_sql."' and
          product_price_retail_sale_to   is not null and product_price_retail_sale_to>='".$datecheck_sql."', product_price_retail_sale,
        if (product_price_retail_sale_from is null and
            product_price_retail_sale_to   is not null and product_price_retail_sale_to>='".$datecheck_sql."', product_price_retail_sale,
          if (product_price_retail_sale_from is not null and product_price_retail_sale_from<='".$datecheck_sql."' and
              product_price_retail_sale_to   is null, product_price_retail_sale,
            product_price_retail
          )
        )
      )
    )
  ) * ".$quantitycheck."
        where (product_price_retail_sheets_formula is null or product_price_retail_sheets_formula='') and
              (product_price_retail_quantity_formula is null or product_price_retail_quantity_formula='')"; 
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql); die('sql error');}
  
  

  $sql="select gks_eshop_products.*,
  if (product_price_retail_sale=0,product_price_retail,
    if (product_price_retail_sale_from is null and product_price_retail_sale_to is null , product_price_retail_sale,
      if (product_price_retail_sale_from is not null and product_price_retail_sale_from<='".$datecheck_sql."' and
          product_price_retail_sale_to   is not null and product_price_retail_sale_to>='".$datecheck_sql."', product_price_retail_sale,
        if (product_price_retail_sale_from is null and
            product_price_retail_sale_to   is not null and product_price_retail_sale_to>='".$datecheck_sql."', product_price_retail_sale,
          if (product_price_retail_sale_from is not null and product_price_retail_sale_from<='".$datecheck_sql."' and
              product_price_retail_sale_to   is null, product_price_retail_sale,
            product_price_retail
          )
        )
      )
    )
  ) as product_price_retail_calc,
  
  if (product_price_yperx_sale=0,product_price_yperx,
    if (product_price_yperx_sale_from is null and product_price_yperx_sale_to is null , product_price_yperx_sale,
      if (product_price_yperx_sale_from is not null and product_price_yperx_sale_from<='".$datecheck_sql."' and
          product_price_yperx_sale_to   is not null and product_price_yperx_sale_to>='".$datecheck_sql."', product_price_yperx_sale,
        if (product_price_yperx_sale_from is null and
            product_price_yperx_sale_to   is not null and product_price_yperx_sale_to>='".$datecheck_sql."', product_price_yperx_sale,
          if (product_price_yperx_sale_from is not null and product_price_yperx_sale_from<='".$datecheck_sql."' and
              product_price_yperx_sale_to   is null, product_price_yperx_sale,
            product_price_yperx
          )
        )
      )
    )
  ) as product_price_yperx_calc, 
     
  if (product_price_sale=0,product_price,
    if (product_price_sale_from is null and product_price_sale_to is null , product_price_sale,
      if (product_price_sale_from is not null and product_price_sale_from<='".$datecheck_sql."' and
          product_price_sale_to   is not null and product_price_sale_to>='".$datecheck_sql."', product_price_sale,
        if (product_price_sale_from is null and
            product_price_sale_to   is not null and product_price_sale_to>='".$datecheck_sql."', product_price_sale,
          if (product_price_sale_from is not null and product_price_sale_from<='".$datecheck_sql."' and
              product_price_sale_to   is null, product_price_sale,
            product_price
          )
        )
      )
    )
  ) as product_price_calc  
  from gks_eshop_products 
  where product_price_sheets_formula<>'' or 
        product_price_quantity_formula<>'' or 
        product_price_retail_sheets_formula<>'' or
        product_price_retail_quantity_formula<>'' or
        product_price_yperx_sheets_formula<>'' or
        product_price_yperx_quantity_formula<>''";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql); die('sql error');}
  while ($row = $result->fetch_assoc()) {
    
    $row['product_price_plist_calc']=0;
    $row['product_price_plist']=0;
    $row['product_price_plist_sale']=0;
    $row['product_price_plist_sale_from']='';
    $row['product_price_plist_sale_to']='';
    $row['product_price_plist_sheets_formula']='';
    $row['product_price_plist_quantity_formula']='';
    $row['product_price_plist_include_vat']=0;
    $row['quantitycheck_price_plist']=0;
          
    $ret = gks_price_formula_calc($row, $quantitycheck, $row['product_monada_id'], $sheetscheck, $out, false,11);

    if ($ret=='') {
      $sql_update="update gks_eshop_products set 
      quantitycheck_price =".number_format($out['quantitycheck_price'],10,'.','').",
      quantitycheck_price_retail =".number_format($out['quantitycheck_price_retail'],10,'.','').",
      quantitycheck_price_yperx =".number_format($out['quantitycheck_price_yperx'],10,'.','')."
      where id_product=".$row['id_product'];
      //echo $sql_update;echo '<br>';
      $result_update = $db_link->query($sql_update);        
      if (!$result_update) {debug_mail(false,'error sql',$sql_update); die('sql error');}
    } else {
      $sql_update="update gks_eshop_products set 
      eval_error ='". $db_link->escape_string($ret)."'
      where id_product=".$row['id_product'];
      $result_update = $db_link->query($sql_update);        
      if (!$result_update) {debug_mail(false,'error sql',$sql_update); die('sql error');}
    }
  }

}

$filters = array();


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
        array('value' => 101, 'text' => gks_lang('Μεταβλητό'),  'sql' => "gks_eshop_products.product_class='variable'"),
        array('value' => 102, 'text' => gks_lang('Παραλλαγή'),  'sql' => "gks_eshop_products.product_class='variable_item'"),
    ),
);
//$filters[] = array(
//    'name' => 'fvariables',
//    'class' => 'filterselectbox',
//    'style' => '',
//    'title' => gks_lang('Παραλλαγές'),
//    'has_custom_default' => -1,
//    'multiselect' => true,
//    'field'  => "gks_eshop_products.product_class = '%V%'",
//    'vals' => array(
////        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
//        array('value' => 100, 'text' => gks_lang('Γονικά είδη'),     'sql' => "gks_eshop_products.product_class<>'variable_item'"),
//        array('value' => 101, 'text' => gks_lang('Παραλλαγές ειδών'),'sql' => "gks_eshop_products.product_class='variable_item'"),
//    ),
//);

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
    'name' => 'fprice_include_vat',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Η χονδρική έχει ΦΠΑ'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_need_multi_files = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ναι'),      'sql' => "gks_eshop_products.product_price_include_vat<>0"),
        array('value' => 101, 'text' => gks_lang('Όχι'),      'sql' => "gks_eshop_products.product_price_include_vat=0"),
    ),
);
$filters[] = array(
    'name' => 'fprice_retail_include_vat',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Η λιανική έχει ΦΠΑ'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_price_retail_include_vat = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ναι'),      'sql' => "gks_eshop_products.product_price_retail_include_vat<>0"),
        array('value' => 101, 'text' => gks_lang('Όχι'),      'sql' => "gks_eshop_products.product_price_retail_include_vat=0"),
    ),
);

$filters[] = array(
    'name' => 'ffpa_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('ΦΠΑ'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_fpa_base_id = '%V%'",
    'vals' => array(
    ),
    'sql' => "SELECT id_fpa_base as id,fpa_base_descr as descr
              FROM gks_eshop_fpa_base
              ORDER BY fpa_base_sortorder",    
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
        array('value' => 2, 'text' => gks_product_base_type_descr(2,true),'sql' => "gks_eshop_products.product_base_type=2"),

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





$filters[] = array(
    'name' => 'fdigital',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ψηφιακό'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_is_digital = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ψηφιακό'),      'sql' => "gks_eshop_products.product_is_digital<>0"),
        array('value' => 101, 'text' => gks_lang('Μη Ψηφιακό'),   'sql' => "gks_eshop_products.product_is_digital=0"),
    ),
);
$filters[] = array(
    'name' => 'fdownload',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Απλό Download'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_is_simple_download = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Απλό Download'),      'sql' => "gks_eshop_products.product_is_simple_download<>0"),
        array('value' => 101, 'text' => gks_lang('Μη Απλό Download'),   'sql' => "gks_eshop_products.product_is_simple_download=0"),
    ),
);

$filters[] = array(
    'name' => 'fneed_multi_files',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Απαιτούνται αρχεία'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_eshop_products.product_need_multi_files = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => 100, 'text' => gks_lang('Ναι'),      'sql' => "gks_eshop_products.product_need_multi_files<>0"),
        array('value' => 101, 'text' => gks_lang('Όχι'),      'sql' => "gks_eshop_products.product_need_multi_files=0"),
    ),
);

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);




$sortable = array(
  						array('name' => 'soid', 'field' => 'gks_eshop_products.id_product'),
  						
  						array('name' => 'sophoto', 'field' => 'product_photo_p'),
  						array('name' => 'socode', 'field' => 'gks_eshop_products.product_code'),
  						array('name' => 'sodescr', 'field' => 'product_descr_p'),
  						array('name' => 'sokostos', 'field' => 'product_kostos'),
  						
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
  						array('name' => 'sopricey', 'field' => (($quantitycheck>0 or $sheetscheck>0) ? 'quantitycheck_price_yperx' : 'product_price_yperx_calc')),
  						array('name' => 'sopricer', 'field' => (($quantitycheck>0 or $sheetscheck>0) ? 'quantitycheck_price_retail' : 'product_price_retail_calc')),
  						array('name' => 'soprice', 'field' =>  (($quantitycheck>0 or $sheetscheck>0) ? 'quantitycheck_price' : 'product_price_calc')),
  						array('name' => 'soprice_include_vat', 'field' => 'gks_eshop_products.product_price_include_vat'),
  						array('name' => 'soprice_retail_include_vat', 'field' => 'gks_eshop_products.product_price_retail_include_vat'),
  						array('name' => 'soprice_yperx_include_vat', 'field' => 'gks_eshop_products.product_price_yperx_include_vat'),
  						
  						array('name' => 'sofpa_base_descr', 'field' => 'fpa_base_descr'),
  						array('name' => 'somm', 'field' => 'gks_monades_metrisis.monada_symbol'),
  						array('name' => 'socount_var', 'field' => 'table_var_cc.count_var'),
  						
  						
  						//prices
  						array('name' => 'sovaty', 'field' => 'gks_eshop_products.product_price_yperx_include_vat'),

            );
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);
$sortable=array_merge($sortable,$gks_eshop_products_extra_sqls['so']);
  
            

$search_fields = array(
'gks_eshop_products_parent.product_code',
'gks_eshop_products.product_code',
'gks_eshop_products.product_sku',
'gks_eshop_products.product_gtin',
'gks_eshop_products.product_upc',
'gks_eshop_products.product_ean',
'gks_eshop_products.product_isbn',
'gks_eshop_products.product_descr',
'gks_eshop_products.product_descr_variable',
'gks_eshop_products_parent.product_descr',
'gks_eshop_products.product_descr_small',
'gks_eshop_products.product_descr_big',
'gks_eshop_products.product_object_name',
'fpa_base_descr',

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
$where2 = isset($filter['sql2']) ? ' AND '.$filter['sql2'] : '';


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
gks_eshop_products.product_sku, 
gks_eshop_products.product_gtin,
gks_eshop_products.product_upc,
gks_eshop_products.product_ean,
gks_eshop_products.product_isbn,
gks_eshop_products.product_taric,
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


gks_eshop_products.product_photo,
gks_eshop_products.product_descr,
gks_eshop_products.product_kostos,
gks_eshop_products.product_price_yperx,
gks_eshop_products.product_price_yperx_sale,
gks_eshop_products.product_price_yperx_include_vat,
gks_eshop_products.quantitycheck_price_yperx,

gks_eshop_products.product_price,
gks_eshop_products.product_price_sale,
gks_eshop_products.product_price_include_vat,
gks_eshop_products.quantitycheck_price,

gks_eshop_products.product_price_retail,
gks_eshop_products.product_price_retail_sale,
gks_eshop_products.product_price_retail_include_vat,
gks_eshop_products.quantitycheck_price_retail,

gks_eshop_products.product_disable,
gks_eshop_products.product_can_sell,
gks_eshop_products.product_can_buy,
gks_eshop_products.product_base_type,
gks_eshop_products.product_need_apostoli,
gks_eshop_products.product_varos,
gks_eshop_products.product_ogos_x,
gks_eshop_products.product_ogos_y,
gks_eshop_products.product_ogos_z,
gks_eshop_products.product_is_digital,
gks_eshop_products.product_is_simple_download,
gks_eshop_products.product_need_multi_files,
gks_eshop_products.product_need_multi_files_min,
gks_eshop_products.product_need_multi_files_max,

gks_eshop_fpa_base.fpa_base_descr,
gks_eshop_products.product_monada_id,
gks_monades_metrisis.monada_symbol,gks_monades_metrisis.monada_descr,

if (gks_eshop_products.product_price_yperx_sale=0,gks_eshop_products.product_price_yperx,
  if (gks_eshop_products.product_price_yperx_sale_from is null and gks_eshop_products.product_price_yperx_sale_to is null , gks_eshop_products.product_price_yperx_sale,
    if (gks_eshop_products.product_price_yperx_sale_from is not null and gks_eshop_products.product_price_yperx_sale_from<=now() and
        gks_eshop_products.product_price_yperx_sale_to   is not null and gks_eshop_products.product_price_yperx_sale_to>=now(), gks_eshop_products.product_price_yperx_sale,
      if (gks_eshop_products.product_price_yperx_sale_from is null and
          gks_eshop_products.product_price_yperx_sale_to   is not null and gks_eshop_products.product_price_yperx_sale_to>=now(), gks_eshop_products.product_price_yperx_sale,
        if (gks_eshop_products.product_price_yperx_sale_from is not null and gks_eshop_products.product_price_yperx_sale_from<=now() and
            gks_eshop_products.product_price_yperx_sale_to   is null, gks_eshop_products.product_price_yperx_sale,
          gks_eshop_products.product_price_yperx
        )
      )
    )
  )
) as product_price_yperx_calc,

if (gks_eshop_products.product_price_retail_sale=0,gks_eshop_products.product_price_retail,
  if (gks_eshop_products.product_price_retail_sale_from is null and gks_eshop_products.product_price_retail_sale_to is null , gks_eshop_products.product_price_retail_sale,
    if (gks_eshop_products.product_price_retail_sale_from is not null and gks_eshop_products.product_price_retail_sale_from<=now() and
        gks_eshop_products.product_price_retail_sale_to   is not null and gks_eshop_products.product_price_retail_sale_to>=now(), gks_eshop_products.product_price_retail_sale,
      if (gks_eshop_products.product_price_retail_sale_from is null and
          gks_eshop_products.product_price_retail_sale_to   is not null and gks_eshop_products.product_price_retail_sale_to>=now(), gks_eshop_products.product_price_retail_sale,
        if (gks_eshop_products.product_price_retail_sale_from is not null and gks_eshop_products.product_price_retail_sale_from<=now() and
            gks_eshop_products.product_price_retail_sale_to   is null, gks_eshop_products.product_price_retail_sale,
          gks_eshop_products.product_price_retail
        )
      )
    )
  )
) as product_price_retail_calc,
if (gks_eshop_products.product_price_sale=0,gks_eshop_products.product_price,
  if (gks_eshop_products.product_price_sale_from is null and gks_eshop_products.product_price_sale_to is null , gks_eshop_products.product_price_sale,
    if (gks_eshop_products.product_price_sale_from is not null and gks_eshop_products.product_price_sale_from<=now() and
        gks_eshop_products.product_price_sale_to   is not null and gks_eshop_products.product_price_sale_to>=now(), gks_eshop_products.product_price_sale,
      if (gks_eshop_products.product_price_sale_from is null and
          gks_eshop_products.product_price_sale_to   is not null and gks_eshop_products.product_price_sale_to>=now(), gks_eshop_products.product_price_sale,
        if (gks_eshop_products.product_price_sale_from is not null and gks_eshop_products.product_price_sale_from<=now() and
            gks_eshop_products.product_price_sale_to   is null, gks_eshop_products.product_price_sale,
          gks_eshop_products.product_price
        )
      )
    )
  )
) as product_price_calc,
table_var_cc.count_var

".$gks_eshop_products_extra_sqls['select']."

".$gks_custom_prepare['sql_all_list_sele']."  
FROM ".$gks_eshop_products_extra_sqls['from']." ".
       $gks_custom_prepare['sql_all_list_from']." ".
       $sql_FROM_cat_1." ".
       $sql_FROM_bra_1." 
       (((gks_eshop_products
".$gks_eshop_products_extra_sqls['left_join']." 
".$gks_custom_prepare['sql_all_list_left']."
".$sql_FROM_cat_2."
".$sql_FROM_bra_2."

LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN gks_eshop_fpa_base ON gks_eshop_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base)
LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada)
LEFT JOIN (
  SELECT product_parent_id, Count(id_product) AS count_var
  FROM gks_eshop_products
  WHERE product_parent_id>0
  GROUP BY product_parent_id
) as table_var_cc on gks_eshop_products.id_product = table_var_cc.product_parent_id

where gks_eshop_products.id_product>2 and gks_eshop_products.product_parent_old_id=0 ".$where . $where_cat_calc . $where_bra_calc . $search_where;
      
//LEFT JOIN gks_eshop_products_categories_products ON gks_eshop_products.id_product = gks_eshop_products_categories_products.product_id)


if (empty($sorted['sql'])) {
	$sql .= " ORDER BY gks_eshop_products.id_product desc, gks_eshop_products.product_code, gks_eshop_products.product_descr";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}

//echo '<pre>';print $sql;die();