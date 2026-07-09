<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Λήψη δεδομένων είδους');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);
$aa=0; if (isset($_POST['aa'])) $aa=intval($_POST['aa']);
$sheets=0; if (isset($_POST['sheets'])) $sheets=intval($_POST['sheets']);
$quantity=0; if (isset($_POST['quantity'])) $quantity=intval($_POST['quantity']);
$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$anddescr=0; if (isset($_POST['anddescr'])) $anddescr=intval($_POST['anddescr']);
$pricelist_id=0;if (isset($_POST['pricelist_id'])) $pricelist_id=intval($_POST['pricelist_id']);
$inv_acc_journal_id=0;if (isset($_POST['inv_acc_journal_id'])) $inv_acc_journal_id=intval($_POST['inv_acc_journal_id']);



$mydate='';
if (isset($_POST['mydate'])) {
  if ($_POST['mydate'] == '__/__/____ __:__') $_POST['mydate']='';
  $mydate=trim_gks(stripslashes(urldecode($_POST['mydate'])));
  if ($mydate!='') {
    $mydate = mystrtodb($mydate);
  }
}
if ($mydate=='') $mydate=date('Y-m-d H:i:s'); 

$id_acc_eidos_parastatikou=0;
if ($inv_acc_journal_id>0) {
  $sql="select acc_eidos_parastatikou_id from gks_acc_journal where id_acc_journal=".$inv_acc_journal_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();  
    $id_acc_eidos_parastatikou=$row['acc_eidos_parastatikou_id'];
  }
}


$sql="SELECT 
gks_eshop_products.id_product,
gks_eshop_products.product_class,
gks_eshop_products.product_parent_id,
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

CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_def_comments<>'' THEN
        gks_eshop_products.product_def_comments
      ELSE
        gks_eshop_products_parent.product_def_comments
    END
  ELSE gks_eshop_products.product_def_comments
END as product_def_comments_p,

CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_descr_small<>'' THEN
        gks_eshop_products.product_descr_small
      ELSE
        CASE
          WHEN gks_eshop_products.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
          ELSE
            gks_eshop_products_parent.product_descr_small
        END
    END
  ELSE gks_eshop_products.product_descr_small
END as product_descr_small_p,

if (gks_eshop_products.product_price_retail_sale=0,gks_eshop_products.product_price_retail,
  if (gks_eshop_products.product_price_retail_sale_from is null and gks_eshop_products.product_price_retail_sale_to is null , gks_eshop_products.product_price_retail_sale,
    if (gks_eshop_products.product_price_retail_sale_from is not null and gks_eshop_products.product_price_retail_sale_from<='".$mydate."' and
        gks_eshop_products.product_price_retail_sale_to   is not null and gks_eshop_products.product_price_retail_sale_to>='".$mydate."', gks_eshop_products.product_price_retail_sale,
      if (gks_eshop_products.product_price_retail_sale_from is null and
          gks_eshop_products.product_price_retail_sale_to   is not null and gks_eshop_products.product_price_retail_sale_to>='".$mydate."', gks_eshop_products.product_price_retail_sale,
        if (gks_eshop_products.product_price_retail_sale_from is not null and gks_eshop_products.product_price_retail_sale_from<='".$mydate."' and
            gks_eshop_products.product_price_retail_sale_to   is null, gks_eshop_products.product_price_retail_sale,
          gks_eshop_products.product_price_retail
        )
      )
    )
  )
) as product_price_retail_calc,
if (gks_eshop_products.product_price_sale=0,gks_eshop_products.product_price,
  if (gks_eshop_products.product_price_sale_from is null and gks_eshop_products.product_price_sale_to is null , gks_eshop_products.product_price_sale,
    if (gks_eshop_products.product_price_sale_from is not null and gks_eshop_products.product_price_sale_from<='".$mydate."' and
        gks_eshop_products.product_price_sale_to   is not null and gks_eshop_products.product_price_sale_to>='".$mydate."', gks_eshop_products.product_price_sale,
      if (gks_eshop_products.product_price_sale_from is null and
          gks_eshop_products.product_price_sale_to   is not null and gks_eshop_products.product_price_sale_to>='".$mydate."', gks_eshop_products.product_price_sale,
        if (gks_eshop_products.product_price_sale_from is not null and gks_eshop_products.product_price_sale_from<='".$mydate."' and
            gks_eshop_products.product_price_sale_to   is null, gks_eshop_products.product_price_sale,
          gks_eshop_products.product_price
        )
      )
    )
  )
) as product_price_calc,

if (gks_eshop_products.product_price_yperx_sale=0,gks_eshop_products.product_price_yperx,
  if (gks_eshop_products.product_price_yperx_sale_from is null and gks_eshop_products.product_price_yperx_sale_to is null , gks_eshop_products.product_price_yperx_sale,
    if (gks_eshop_products.product_price_yperx_sale_from is not null and gks_eshop_products.product_price_yperx_sale_from<='".$mydate."' and
        gks_eshop_products.product_price_yperx_sale_to   is not null and gks_eshop_products.product_price_yperx_sale_to>='".$mydate."', gks_eshop_products.product_price_yperx_sale,
      if (gks_eshop_products.product_price_yperx_sale_from is null and
          gks_eshop_products.product_price_yperx_sale_to   is not null and gks_eshop_products.product_price_yperx_sale_to>='".$mydate."', gks_eshop_products.product_price_yperx_sale,
        if (gks_eshop_products.product_price_yperx_sale_from is not null and gks_eshop_products.product_price_yperx_sale_from<='".$mydate."' and
            gks_eshop_products.product_price_yperx_sale_to   is null, gks_eshop_products.product_price_yperx_sale,
          gks_eshop_products.product_price_yperx
        )
      )
    )
  )
) as product_price_yperx_calc,

gks_eshop_products.product_kostos,

gks_eshop_products.product_price_yperx,
gks_eshop_products.product_price_yperx_include_vat,
gks_eshop_products.product_price_yperx_sheets_formula,
gks_eshop_products.product_price_yperx_quantity_formula,

gks_eshop_products.product_price,
gks_eshop_products.product_price_include_vat,
gks_eshop_products.product_price_sheets_formula,
gks_eshop_products.product_price_quantity_formula,

gks_eshop_products.product_price_retail,
gks_eshop_products.product_price_retail_include_vat,
gks_eshop_products.product_price_retail_sheets_formula,
gks_eshop_products.product_price_retail_quantity_formula,

gks_eshop_products.product_fpa_base_id,
gks_eshop_products.product_fpa_ejeresi_id,
gks_eshop_products.product_monada_id,
gks_eshop_products.product_varos,
gks_eshop_products.product_ogos_x,
gks_eshop_products.product_ogos_y,
gks_eshop_products.product_ogos_z,
gks_eshop_products.product_need_apostoli,
gks_eshop_products.product_withheldPercentCategory,
gks_eshop_products.product_otherTaxesPercentCategory,
gks_eshop_products.product_stampDutyPercentCategory,
gks_eshop_products.product_feesPercentCategory,
gks_eshop_fpa_base.fpa_base_descr,
gks_monades_metrisis.monada_descr, 
gks_monades_metrisis.monada_symbol,
gks_eshop_products.product_lot_serial
FROM ((gks_eshop_products 
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
LEFT JOIN gks_eshop_fpa_base ON gks_eshop_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base)
LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada
where gks_eshop_products.id_product=".$id." limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  
}


$row_product = $result->fetch_assoc();
$product_class=$row_product['product_class'];
$product_parent_id=intval($row_product['product_parent_id']);

$row_product['product_price']=floatval($row_product['product_price']);
$row_product['product_price_retail']=floatval($row_product['product_price_retail']);
$row_product['product_price_include_vat']=intval($row_product['product_price_include_vat']);
$row_product['product_price_retail_include_vat']=intval($row_product['product_price_retail_include_vat']);

$row_product['product_price_plist_calc']=0;
$row_product['product_price_plist']=0;
$row_product['product_price_plist_sale']=0;
$row_product['product_price_plist_sale_from']='';
$row_product['product_price_plist_sale_to']='';
$row_product['product_price_plist_sheets_formula']='';
$row_product['product_price_plist_quantity_formula']='';
$row_product['product_price_plist_include_vat']=0;
$row_product['quantitycheck_price_plist']=0;
if ($pricelist_id>=10001) {
  $sql_plist="select gks_eshop_products_prices.*,
  if (product_price_plist_sale=0,product_price_plist,
    if (product_price_plist_sale_from is null and product_price_plist_sale_to is null , product_price_plist_sale,
      if (product_price_plist_sale_from is not null and product_price_plist_sale_from<='".$mydate."' and
          product_price_plist_sale_to   is not null and product_price_plist_sale_to>='".$mydate."', product_price_plist_sale,
        if (product_price_plist_sale_from is null and
            product_price_plist_sale_to   is not null and product_price_plist_sale_to>='".$mydate."', product_price_plist_sale,
          if (product_price_plist_sale_from is not null and product_price_plist_sale_from<='".$mydate."' and
              product_price_plist_sale_to   is null, product_price_plist_sale,
            product_price_plist
          )
        )
      )
    )
  ) as product_price_plist_calc
  from gks_eshop_products_prices
  where pricelist_id=".$pricelist_id."
  and product_id=".$id;
  $res_plist = $db_link->query($sql_plist);
  if (!$res_plist) {
    debug_mail(false,'products_get error sql',$sql_plist);
    die('sql error');
  }
  if ($res_plist->num_rows >= 1){
    $row_plist = $res_plist->fetch_assoc(); 
    $row_product['product_price_plist_calc']=floatval($row_plist['product_price_plist_calc']);

    $row_product['product_price_plist']=floatval($row_plist['product_price_plist']);
    $row_product['product_price_plist_sale']=floatval($row_plist['product_price_plist_sale']);
    $row_product['product_price_plist_sale_from']=$row_plist['product_price_plist_sale_from'];
    $row_product['product_price_plist_sale_to']=$row_plist['product_price_plist_sale_to'];
    $row_product['product_price_plist_sheets_formula']=trim_gks($row_plist['product_price_plist_sheets_formula']);
    $row_product['product_price_plist_quantity_formula']=trim_gks($row_plist['product_price_plist_quantity_formula']);
    $row_product['product_price_plist_include_vat']=intval($row_plist['product_price_plist_include_vat']);
    $row_product['quantitycheck_price_plist']=floatval($row_plist['quantitycheck_price_plist']);
  }
}


$fpa_base_id=intval($row_product['product_fpa_base_id']);
$product_fpa_ejeresi_id=intval($row_product['product_fpa_ejeresi_id']);
if ($fpa_base_id!=1004) $product_fpa_ejeresi_id=0;

  

$id_fpa=0;
$fpa_pososto=0;
$fpa_descr_print='';
if (isset($row_product['id_fpa']) and $row_product['fpa_disable']==0) {
  $id_fpa=intval($row_product['id_fpa']);
  $fpa_pososto=floatval($row_product['fpa_pososto']); 
  $fpa_descr_print=trim_gks($row_product['fpa_descr_print']);
}


$myimgurl=trim_gks($row_product['product_photo_p'].'');
$photo_url='';
if ($myimgurl != '') {
  $mydir = dirname($myimgurl);
  if (endwith($mydir,'/thumbnail')) {
    $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
  } else {
    $photo_url=$myimgurl;
  }
}
//print'<pre>';print_r($row_product);die();

$ret = gks_price_formula_calc($row_product, $quantity, $row_product['product_monada_id'], $sheets, $out, false,11);


$product_descr_small=trim_gks($row_product['product_descr_small_p']);
if ($product_descr_small!='') {
  $product_descr_small="<table style='max-width:300px' border=0><tr><td>".str_replace('"',"'", $product_descr_small)."</td></tr></table>";
}


$xarakt_product_id=$id;
if ($product_class=='variable_item') {
  $xarakt_product_id=$product_parent_id;
}

$out_xarakt_esoda=array();
$out_xarakt_eksoda=array();

$sql_income="select 
acc_eidos_parastatikou_id as ep_id,
aade_typos_xarakt_esodon_id as typos_id,
aade_katigoria_xarakt_esodon_id as cat_id,
acc_inv_product_income_pososto as pososto
from gks_eshop_products_income
where product_id in (".$xarakt_product_id.")
and (acc_eidos_parastatikou_id=0 ".($id_acc_eidos_parastatikou > 0 ? ' or acc_eidos_parastatikou_id='.$id_acc_eidos_parastatikou : '').")
order by id_product_income";
$result_income = $db_link->query($sql_income);        
if (!$result_income) {debug_mail(false,'error sql',$sql_income); die('sql error');}
while ($row_income = $result_income->fetch_assoc()) {
  $out_xarakt_esoda[]=array(
    'ep_id'=> intval($row_income['ep_id']),
    'typos_id'=> intval($row_income['typos_id']),
    'cat_id'=> intval($row_income['cat_id']),
    'pososto'=> floatval($row_income['pososto']),
  );
}


          
$sql_expenses="select 
acc_eidos_parastatikou_id as ep_id,
aade_typos_xarakt_eksodon_id as typos_id,
aade_katigoria_xarakt_eksodon_id as cat_id,
acc_inv_product_expenses_pososto as pososto
from gks_eshop_products_expenses
where product_id in (".$xarakt_product_id.")
and (acc_eidos_parastatikou_id=0 ".($id_acc_eidos_parastatikou > 0 ? ' or acc_eidos_parastatikou_id='.$id_acc_eidos_parastatikou : '').")
order by id_product_expenses";
$result_expenses = $db_link->query($sql_expenses);        
if (!$result_expenses) {debug_mail(false,'error sql',$sql_expenses); die('sql error');}
while ($row_expenses = $result_expenses->fetch_assoc()) {
  $out_xarakt_eksoda[]=array(
    'ep_id'=> intval($row_expenses['ep_id']),
    'typos_id'=> intval($row_expenses['typos_id']),
    'cat_id'=> intval($row_expenses['cat_id']),
    'pososto'=> floatval($row_expenses['pososto']),
  );
}

$sql_brand="SELECT gks_eshop_products_brands_products.product_id, gks_eshop_products_brands_products.product_brand_id,
mybrands.fullpath, mybrands.brand_level
FROM gks_eshop_products_brands_products
LEFT JOIN (
  SELECT gks_eshop_products_brands.id_product_brand,
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
                  gks_eshop_products_brands.product_brand_descr) as fullpath,
  CASE
    WHEN ug10.id_product_brand is not null THEN 10
    WHEN ug9.id_product_brand is not null THEN 9
    WHEN ug8.id_product_brand is not null THEN 8
    WHEN ug7.id_product_brand is not null THEN 7
    WHEN ug6.id_product_brand is not null THEN 6
    WHEN ug5.id_product_brand is not null THEN 5
    WHEN ug4.id_product_brand is not null THEN 4
    WHEN ug3.id_product_brand is not null THEN 3
    WHEN ug2.id_product_brand is not null THEN 2
    ELSE 1
  END as brand_level
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
) as mybrands on gks_eshop_products_brands_products.product_brand_id = mybrands.id_product_brand
WHERE gks_eshop_products_brands_products.product_id in (".$id.($product_parent_id==0 ? '': ','.$product_parent_id).")
order by mybrands.brand_level desc, fullpath";

$result_brand = $db_link->query($sql_brand);
if (!$result_brand) {debug_mail(false,'error sql',$sql_brand); die('sql error');}
$brand_id=0;
$brand_descr='';
if ($result_brand->num_rows>=1) {
  $row_brand = $result_brand->fetch_assoc();
  $brand_id=intval($row_brand['product_brand_id']);
  $brand_descr=trim_gks($row_brand['fullpath']);
  
}

$itemprice_check_fpa=false;
if ($GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA) {
  if ($pricelist_id>0) {
    $sql_check_fpa="select price_is_xondriki from gks_eshop_pricelist where id_pricelist=".$pricelist_id;  
    $result_check_fpa = $db_link->query($sql_check_fpa);
    if (!$result_check_fpa) {debug_mail(false,'error sql',$sql_check_fpa); die('sql error');}
    if ($result_check_fpa->num_rows>=1) {
      $row_check_fpa = $result_check_fpa->fetch_assoc();
      if ($row_check_fpa['price_is_xondriki']==0) { //lianiki
        $itemprice_check_fpa=($row_product['product_price_retail_include_vat']==0 ? false : true);
      } else if ($row_check_fpa['price_is_xondriki']==1) { //xondriki
        $itemprice_check_fpa=($row_product['product_price_include_vat']==0 ? false : true);
      } else if ($row_check_fpa['price_is_xondriki']==2) { //yperxondriki
        $itemprice_check_fpa=($row_product['product_price_yperx_include_vat']==0 ? false : true);
      }
    }
    
    if ($pricelist_id>=10001 and $row_product['product_price_plist_calc']>0) {
      $itemprice_check_fpa=($row_product['product_price_plist_include_vat']==0 ? false : true);
    }
    
  }
} 

$product_variants=array();
if ($product_class=='variable') {
  $sql_variants="SELECT id_product, product_descr_variable
  FROM gks_eshop_products
  WHERE product_parent_id=".$id." AND product_disable=0
  order by product_variable_sortorder,product_descr_variable";
  $result_variants = $db_link->query($sql_variants);        
  if (!$result_variants) {debug_mail(false,'error sql',$sql_variants); die('sql error');}
  while ($row_variants= $result_variants->fetch_assoc()) {
    $product_variants[]=array(
      'id'=> intval($row_variants['id_product']),
      'descr'=> trim_gks($row_variants['product_descr_variable']),
    );
  }
}


$return = array('success' => true, 'message' => base64_encode('OK'), 
  'id'=>$id, 
  'aa'=>$aa, 
  'product_parent_id' => $product_parent_id,
  'product_code' => trim_gks($row_product['product_code']),
  'product_descr' => trim_gks($row_product['product_descr_p']), 
  'product_def_comments' => trim_gks($row_product['product_def_comments_p']), 
  'product_descr_small' => $product_descr_small, 
  'product_monada_id' => $row_product['product_monada_id'], 
  'monada_symbol' => $row_product['monada_symbol'], 
  'monada_descr' => $row_product['monada_descr'], 
  'product_photo'=> trim_gks($row_product['product_photo_p']),
  'photo_url'=>$photo_url, 
//  'out' => $out,
//  'id_fiscal_position' => $id_fiscal_position,
//  'id_pricelist' => $id_pricelist,
//  'based_pricelist_id' => $based_pricelist_id,
  'fpa_base_id'=>$fpa_base_id,
  'product_fpa_ejeresi_id' => $product_fpa_ejeresi_id,
  'itemprice_check_fpa' => $itemprice_check_fpa,
  'product_lot_serial' => trim_gks($row_product['product_lot_serial']), 
  'product_lot_serial_label' => (trim_gks($row_product['product_lot_serial'])=='lot' ? gks_lang('Παρτίδες') : 
    (trim_gks($row_product['product_lot_serial'])=='serial' ? 'Serial Numbers' : '')
  ), 
  
//  'id_fpa'=>$id_fpa,
//
//  'fpa_pososto'=>$fpa_pososto,
//  'fpa_descr_print'=>$fpa_descr_print,
//  'product_price_start_all_net'=>$product_price_start_all_net,
//  'product_price_ekptosi_pososto' => $product_price_ekptosi_pososto,
//  'product_price_final_all_net'=>100, //$product_price_final_all_net,
//  'product_price_final_all_fpa'=>$product_price_final_all_fpa,
//  'product_price_final_all_total'=>$product_price_final_all_total,
//  'product_price_retail_include_vat'=>$row_product['product_price_retail_include_vat'],
//  'product_price_include_vat'=>$row_product['product_price_include_vat'],
  'anddescr' => $anddescr,
  //'ekptosi_poso_html' => $ekptosi_poso_html,
  //'ekptosi_pososto_html'=> '',
  
  'varos' => intval($row_product['product_varos']),
  'ogos_x' => intval($row_product['product_ogos_x']),
  'ogos_y' => intval($row_product['product_ogos_y']),
  'ogos_z' => intval($row_product['product_ogos_z']),
  'need_apostoli' => intval($row_product['product_need_apostoli']),
  
  'withheldPercentCategory' => intval($row_product['product_withheldPercentCategory']),
  'otherTaxesPercentCategory' => intval($row_product['product_otherTaxesPercentCategory']),
  'stampDutyPercentCategory' => intval($row_product['product_stampDutyPercentCategory']),
  'feesPercentCategory' => intval($row_product['product_feesPercentCategory']),

  'xarakt_esoda' => $out_xarakt_esoda,
  'xarakt_eksoda' => $out_xarakt_eksoda,
  
  'brand_id' => $brand_id,
  'brand_descr' => $brand_descr,

  'product_class' => $product_class,
  'product_variants' => $product_variants,
);



echo json_encode($return); die();



