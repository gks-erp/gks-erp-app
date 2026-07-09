<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση προϊόντος');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$temp =''; if (isset($_POST['def_column_show'])) $temp = trim_gks(base64_decode($_POST['def_column_show']));
$temp = json_decode($temp, true);
//print '<pre>';print_r($temp);die();
if (is_array($temp) and count($temp)>10) {
  $sql="replace into gks_settings_users (
  user_id,myobject,mysubobject,myvalue
  ) values (
  ".$my_wp_user_id.",'products_item','def_column_show',
  '".$db_link->escape_string(serialize($temp))."'
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
}

$temp =''; if (isset($_POST['def_column_width'])) $temp = trim_gks(base64_decode($_POST['def_column_width']));
$temp = json_decode($temp, true);
//print '<pre>';print_r($temp);die();
if (is_array($temp) and count($temp)>10) {
  $sql="replace into gks_settings_users (
  user_id,myobject,mysubobject,myvalue
  ) values (
  ".$my_wp_user_id.",'products_item','def_column_width',
  '".$db_link->escape_string(serialize($temp))."'
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
}



$product_monada_id_old=-1;
if ($id>0) {
  $sql="select * from gks_eshop_products where id_product=".$id." limit 1";
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
  $row = $result->fetch_assoc();
  $product_monada_id_old=$row['product_monada_id'];
}


$form_product_photo=trim_gks(stripslashes(urldecode($_POST['form_product_photo'])));

//if ($form_product_photo != '') {
//  if (substr($form_product_photo, 0,1) =='/') { 
//    $form_product_photo = GKS_SITE_URL . $form_product_photo;
//  }
//}
$product_class=''; if (isset($_POST['product_class'])) $product_class=trim_gks(base64_decode($_POST['product_class']));
if ($product_class!='simple' and $product_class!='variable') $product_class='simple';

$product_code=''; if (isset($_POST['product_code'])) $product_code=trim_gks(base64_decode($_POST['product_code']));
$product_descr=''; if (isset($_POST['product_descr'])) $product_descr=trim_gks(base64_decode($_POST['product_descr']));
$product_def_comments=''; if (isset($_POST['product_def_comments'])) $product_def_comments=trim_gks(base64_decode($_POST['product_def_comments']));
$product_descr_small=''; if (isset($_POST['product_descr_small'])) $product_descr_small=trim_gks(base64_decode($_POST['product_descr_small']));
$product_descr_big=''; if (isset($_POST['product_descr_big'])) $product_descr_big=trim_gks(base64_decode($_POST['product_descr_big']));
$product_object_name=''; if (isset($_POST['product_object_name'])) $product_object_name=trim_gks(base64_decode($_POST['product_object_name']));

$product_can_sell=0; if (isset($_POST['product_can_sell'])) $product_can_sell=intval($_POST['product_can_sell']);
$product_can_buy=0; if (isset($_POST['product_can_buy'])) $product_can_buy=intval($_POST['product_can_buy']);

$product_is_digital=0; if (isset($_POST['product_is_digital'])) $product_is_digital=intval($_POST['product_is_digital']);
$product_is_simple_download=0; if (isset($_POST['product_is_simple_download'])) $product_is_simple_download=intval($_POST['product_is_simple_download']);
$product_base_type=0; if (isset($_POST['product_base_type'])) $product_base_type=intval($_POST['product_base_type']);
$product_sku=''; if (isset($_POST['product_sku'])) $product_sku=trim_gks(base64_decode($_POST['product_sku']));
$product_gtin=''; if (isset($_POST['product_gtin'])) $product_gtin=trim_gks(base64_decode($_POST['product_gtin']));
$product_upc=''; if (isset($_POST['product_upc'])) $product_upc=trim_gks(base64_decode($_POST['product_upc']));
$product_ean=''; if (isset($_POST['product_ean'])) $product_ean=trim_gks(base64_decode($_POST['product_ean']));
$product_isbn=''; if (isset($_POST['product_isbn'])) $product_isbn=trim_gks(base64_decode($_POST['product_isbn']));
$product_taric=''; if (isset($_POST['product_taric'])) $product_taric=trim_gks(base64_decode($_POST['product_taric']));


$product_lot_serial='';
if ($GKS_PRODUCT_LOTS_SERIALS) {
  if (isset($_POST['product_lot_serial'])) $product_lot_serial =trim_gks(base64_decode($_POST['product_lot_serial']));
  if ($product_lot_serial!='lot' and $product_lot_serial!='serial') $product_lot_serial!='';
}

$product_need_apostoli=0; if (isset($_POST['product_need_apostoli'])) $product_need_apostoli=intval($_POST['product_need_apostoli']);
$product_need_multi_files=0; if (isset($_POST['product_need_multi_files'])) $product_need_multi_files=intval($_POST['product_need_multi_files']);
$product_show_on_dialog=0; if (isset($_POST['product_show_on_dialog'])) $product_show_on_dialog=intval($_POST['product_show_on_dialog']);
$product_min_pixels_can_rotate=0; if (isset($_POST['product_min_pixels_can_rotate'])) $product_min_pixels_can_rotate=intval($_POST['product_min_pixels_can_rotate']);
$product_disable=0; if (isset($_POST['product_disable'])) $product_disable=intval($_POST['product_disable']);

$product_fpa_base_id=0; if (isset($_POST['product_fpa_base_id'])) $product_fpa_base_id=intval($_POST['product_fpa_base_id']);
$product_fpa_ejeresi_id=0; if (isset($_POST['product_fpa_ejeresi_id'])) $product_fpa_ejeresi_id=intval($_POST['product_fpa_ejeresi_id']);
if ($product_fpa_base_id!=1004) $product_fpa_ejeresi_id=0;

$product_monada_id=0; if (isset($_POST['product_monada_id'])) $product_monada_id=intval($_POST['product_monada_id']);
$product_need_multi_files_min=0; if (isset($_POST['product_need_multi_files_min'])) $product_need_multi_files_min=intval($_POST['product_need_multi_files_min']);
$product_need_multi_files_max=0; if (isset($_POST['product_need_multi_files_max'])) $product_need_multi_files_max=intval($_POST['product_need_multi_files_max']);
//$product_sortorder=0; if (isset($_POST['product_sortorder'])) $product_sortorder=intval($_POST['product_sortorder']);
$product_min_pixels_x=0; if (isset($_POST['product_min_pixels_x'])) $product_min_pixels_x=intval($_POST['product_min_pixels_x']);
$product_min_pixels_y=0; if (isset($_POST['product_min_pixels_y'])) $product_min_pixels_y=intval($_POST['product_min_pixels_y']);

$product_varos=0;  if (isset($_POST['product_varos']))  $product_varos=floatval($_POST['product_varos']);
$product_ogos_x=0; if (isset($_POST['product_ogos_x'])) $product_ogos_x=floatval($_POST['product_ogos_x']);
$product_ogos_y=0; if (isset($_POST['product_ogos_y'])) $product_ogos_y=floatval($_POST['product_ogos_y']);
$product_ogos_z=0; if (isset($_POST['product_ogos_z'])) $product_ogos_z=floatval($_POST['product_ogos_z']);

$product_price_yperx=0; if (isset($_POST['product_price_yperx'])) $product_price_yperx=floatval($_POST['product_price_yperx']); 
$product_price_yperx_include_vat=0; if (isset($_POST['product_price_yperx_include_vat'])) $product_price_yperx_include_vat=intval($_POST['product_price_yperx_include_vat']);
$product_price_yperx_sale=0; if (isset($_POST['product_price_yperx_sale'])) $product_price_yperx_sale=floatval($_POST['product_price_yperx_sale']); 
$product_price_yperx_sale_dates=0; if (isset($_POST['product_price_yperx_sale_dates'])) $product_price_yperx_sale_dates=intval($_POST['product_price_yperx_sale_dates']);
if ($product_price_yperx_sale_dates==0) {
  $product_price_yperx_sale_from='';
  $product_price_yperx_sale_to='';
} else {
  if ($_POST['product_price_yperx_sale_from'] == '__/__/____ __:__') $_POST['product_price_yperx_sale_from']='';
  $product_price_yperx_sale_from=trim_gks(stripslashes(urldecode($_POST['product_price_yperx_sale_from'])));
  if ($product_price_yperx_sale_from!='') {
    $product_price_yperx_sale_from = mystrtodb($product_price_yperx_sale_from);
  }
  if ($_POST['product_price_yperx_sale_to'] == '__/__/____ __:__') $_POST['product_price_yperx_sale_to']='';
  $product_price_yperx_sale_to=trim_gks(stripslashes(urldecode($_POST['product_price_yperx_sale_to'])));
  if ($product_price_yperx_sale_to!='') {
    $product_price_yperx_sale_to = mystrtodb($product_price_yperx_sale_to);
  }
}
$product_price_yperx_sheets_formula=''; if (isset($_POST['product_price_yperx_sheets_formula'])) $product_price_yperx_sheets_formula=trim_gks(base64_decode($_POST['product_price_yperx_sheets_formula']));
$product_price_yperx_quantity_formula=''; if (isset($_POST['product_price_yperx_quantity_formula'])) $product_price_yperx_quantity_formula=trim_gks(base64_decode($_POST['product_price_yperx_quantity_formula']));


$product_price=0; if (isset($_POST['product_price'])) $product_price=floatval($_POST['product_price']); 
$product_price_include_vat=0; if (isset($_POST['product_price_include_vat'])) $product_price_include_vat=intval($_POST['product_price_include_vat']);
$product_price_sale=0; if (isset($_POST['product_price_sale'])) $product_price_sale=floatval($_POST['product_price_sale']); 
$product_price_sale_dates=0; if (isset($_POST['product_price_sale_dates'])) $product_price_sale_dates=intval($_POST['product_price_sale_dates']);
if ($product_price_sale_dates==0) {
  $product_price_sale_from='';
  $product_price_sale_to='';
} else {
  if ($_POST['product_price_sale_from'] == '__/__/____ __:__') $_POST['product_price_sale_from']='';
  $product_price_sale_from=trim_gks(stripslashes(urldecode($_POST['product_price_sale_from'])));
  if ($product_price_sale_from!='') {
    $product_price_sale_from = mystrtodb($product_price_sale_from);
  }
  if ($_POST['product_price_sale_to'] == '__/__/____ __:__') $_POST['product_price_sale_to']='';
  $product_price_sale_to=trim_gks(stripslashes(urldecode($_POST['product_price_sale_to'])));
  if ($product_price_sale_to!='') {
    $product_price_sale_to = mystrtodb($product_price_sale_to);
  }
}
$product_price_sheets_formula=''; if (isset($_POST['product_price_sheets_formula'])) $product_price_sheets_formula=trim_gks(base64_decode($_POST['product_price_sheets_formula']));
$product_price_quantity_formula=''; if (isset($_POST['product_price_quantity_formula'])) $product_price_quantity_formula=trim_gks(base64_decode($_POST['product_price_quantity_formula']));

$product_price_retail=0; if (isset($_POST['product_price_retail'])) $product_price_retail=floatval($_POST['product_price_retail']); 
$product_price_retail_include_vat=0; if (isset($_POST['product_price_retail_include_vat'])) $product_price_retail_include_vat=intval($_POST['product_price_retail_include_vat']);
$product_price_retail_sale=0; if (isset($_POST['product_price_retail_sale'])) $product_price_retail_sale=floatval($_POST['product_price_retail_sale']); 
$product_price_retail_sale_dates=0; if (isset($_POST['product_price_retail_sale_dates'])) $product_price_retail_sale_dates=intval($_POST['product_price_retail_sale_dates']);
if ($product_price_retail_sale_dates==0) {
  $product_price_retail_sale_from='';
  $product_price_retail_sale_to='';
} else {
  if ($_POST['product_price_retail_sale_from'] == '__/__/____ __:__') $_POST['product_price_retail_sale_from']='';
  $product_price_retail_sale_from=trim_gks(stripslashes(urldecode($_POST['product_price_retail_sale_from'])));
  if ($product_price_retail_sale_from!='') {
    $product_price_retail_sale_from = mystrtodb($product_price_retail_sale_from);
  }
  if ($_POST['product_price_retail_sale_to'] == '__/__/____ __:__') $_POST['product_price_retail_sale_to']='';
  $product_price_retail_sale_to=trim_gks(stripslashes(urldecode($_POST['product_price_retail_sale_to'])));
  if ($product_price_retail_sale_to!='') {
    $product_price_retail_sale_to = mystrtodb($product_price_retail_sale_to);
  }
}
$product_price_retail_sheets_formula=''; if (isset($_POST['product_price_retail_sheets_formula'])) $product_price_retail_sheets_formula=trim_gks(base64_decode($_POST['product_price_retail_sheets_formula']));
$product_price_retail_quantity_formula=''; if (isset($_POST['product_price_retail_quantity_formula'])) $product_price_retail_quantity_formula=trim_gks(base64_decode($_POST['product_price_retail_quantity_formula']));

$temp = trim_gks(base64_decode($_POST['product_price_plist']));
$temp = json_decode($temp, true);
$array_product_price_plist=array();
foreach($temp as $plist_item) {
  $id_pricelist=floatval($plist_item['id_pricelist']); 
  $product_price_plist=floatval($plist_item['product_price_plist']); 
  $product_price_plist_include_vat=intval($plist_item['product_price_plist_include_vat']);
  $product_price_plist_sale=floatval($plist_item['product_price_plist_sale']); 
  $product_price_plist_sale_dates=intval($plist_item['product_price_plist_sale_dates']);
  if ($product_price_plist_sale_dates==0) {
    $product_price_plist_sale_from='';
    $product_price_plist_sale_to='';
  } else {
    if ($plist_item['product_price_plist_sale_from'] == '__/__/____ __:__') $plist_item['product_price_plist_sale_from']='';
    $product_price_plist_sale_from=trim_gks(stripslashes(urldecode($plist_item['product_price_plist_sale_from'])));
    if ($product_price_plist_sale_from!='') {
      $product_price_plist_sale_from = mystrtodb($product_price_plist_sale_from);
    }
    if ($plist_item['product_price_plist_sale_to'] == '__/__/____ __:__') $plist_item['product_price_plist_sale_to']='';
    $product_price_plist_sale_to=trim_gks(stripslashes(urldecode($plist_item['product_price_plist_sale_to'])));
    if ($product_price_plist_sale_to!='') {
      $product_price_plist_sale_to = mystrtodb($product_price_plist_sale_to);
    }
  }
  $product_price_plist_sheets_formula=trim_gks(base64_decode($plist_item['product_price_plist_sheets_formula']));
  $product_price_plist_quantity_formula=trim_gks(base64_decode($plist_item['product_price_plist_quantity_formula']));
  
  if ($product_price_plist>0 or 
      $product_price_plist_sale>0 or
      $product_price_plist_sheets_formula<>'' or
      $product_price_plist_quantity_formula<>'') {
    $array_product_price_plist[]=array(
      'id_pricelist'=>$id_pricelist,
      'product_price_plist'=>$product_price_plist,
      'product_price_plist_sale'=>$product_price_plist_sale,
      'product_price_plist_sale_from'=>$product_price_plist_sale_from,
      'product_price_plist_sale_to'=>$product_price_plist_sale_to,
      'product_price_plist_sheets_formula'=>$product_price_plist_sheets_formula,
      'product_price_plist_quantity_formula'=>$product_price_plist_quantity_formula,
      'product_price_plist_include_vat'=>$product_price_plist_include_vat,
    );
  }
  
}
//print '<pre>';print_r($array_product_price_plist);die();

$product_kostos=''; if (isset($_POST['product_kostos']) and $_POST['product_kostos']!='') $product_kostos=floatval($_POST['product_kostos']); 


$use_only_mine_ergasies=0; if (isset($_POST['use_only_mine_ergasies'])) $use_only_mine_ergasies=intval($_POST['use_only_mine_ergasies']);

$product_withheldPercentCategory=0; if (isset($_POST['product_withheldPercentCategory'])) $product_withheldPercentCategory=intval($_POST['product_withheldPercentCategory']);
$product_otherTaxesPercentCategory=0; if (isset($_POST['product_otherTaxesPercentCategory'])) $product_otherTaxesPercentCategory=intval($_POST['product_otherTaxesPercentCategory']);
$product_stampDutyPercentCategory=0; if (isset($_POST['product_stampDutyPercentCategory'])) $product_stampDutyPercentCategory=intval($_POST['product_stampDutyPercentCategory']);
$product_feesPercentCategory=0; if (isset($_POST['product_feesPercentCategory'])) $product_feesPercentCategory=intval($_POST['product_feesPercentCategory']);

$internal_note=''; if (isset($_POST['internal_note'])) $internal_note=trim_gks(base64_decode($_POST['internal_note']));
$min_quantity_alert=0; if (isset($_POST['min_quantity_alert'])) $min_quantity_alert=floatval($_POST['min_quantity_alert']);
$def_supplier=0; if (isset($_POST['def_supplier'])) $def_supplier=intval($_POST['def_supplier']);



if ($product_varos<0) $product_varos=0;
if ($product_ogos_x<0) $product_ogos_x=0;
if ($product_ogos_y<0) $product_ogos_y=0;
if ($product_ogos_z<0) $product_ogos_z=0;
if ($product_need_multi_files_min<0) $product_need_multi_files_min=0;
if ($product_need_multi_files_max<0) $product_need_multi_files_max=0;


if ($product_base_type==0) { //emporeuma
  $product_need_multi_files=0;
  $product_min_pixels_x=0;
  $product_min_pixels_y=0;
  $product_min_pixels_can_rotate=0;  
} else if ($product_base_type==1) { //proion
  
} else if ($product_base_type==2) { //ypiresia
  $product_need_apostoli=0;
  $product_is_digital=0;
  $product_need_multi_files=0;
  $product_min_pixels_x=0;
  $product_min_pixels_y=0;
  $product_min_pixels_can_rotate=0;
  $product_lot_serial='';
}

if ($product_need_apostoli==0) {$product_varos=0;$product_ogos_x=0;$product_ogos_y=0;$product_ogos_z=0;$product_lot_serial='';}
if ($product_is_digital==0) $product_is_simple_download=0;
if ($product_need_multi_files==0) {$product_need_multi_files_min=0;$product_need_multi_files_max=0;$product_object_name='';}


$temp = trim_gks(base64_decode($_POST['xarakt_esoda']));
$temp = json_decode($temp, true);
$xarakt_esoda=array();
foreach($temp as $xarakt_item) {
  $ep_id=intval($xarakt_item['ep_id']);
  $cat_id=intval($xarakt_item['cat_id']);
  $typos_id=intval($xarakt_item['typos_id']);
  $ammount=floatval($xarakt_item['ammount']);
  if (($cat_id!=0 or $typos_id!=0) and $ammount!=0) {
    $xarakt_esoda[]=array('ep_id'=>$ep_id, 'cat_id'=>$cat_id, 'typos_id'=>$typos_id, 'ammount'=>$ammount);
  }
}
//print '<pre>';print_r($xarakt_esoda);die();

$temp = trim_gks(base64_decode($_POST['xarakt_eksoda']));
$temp = json_decode($temp, true);
$xarakt_eksoda=array();
foreach($temp as $xarakt_item) {
  $ep_id=intval($xarakt_item['ep_id']);
  $cat_id=intval($xarakt_item['cat_id']);
  $typos_id=intval($xarakt_item['typos_id']);
  $ammount=floatval($xarakt_item['ammount']);
  if (($cat_id!=0 or $typos_id!=0) and $ammount!=0) {
    $xarakt_eksoda[]=array('ep_id'=>$ep_id, 'cat_id'=>$cat_id, 'typos_id'=>$typos_id, 'ammount'=>$ammount);
  }
}
//print '<pre>';print_r($xarakt_eksoda);die();





if ($product_code!='') {
  $sql="select * from gks_eshop_products where product_code like '". $db_link->escape_string($product_code)."' and id_product<>".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=0) {
    $row = $result->fetch_assoc();
    $message=gks_lang('Ο κωδικός <b>[1]</b> υπάρχει ήδη σε άλλο είδος:<br><b>[2]</b>');
    $message=str_replace('[1]',$product_code,$message);
    $message=str_replace('[2]',$row['product_descr'],$message);
    $message.='<br><a href="admin-products-item.php?id='.$row['id_product'].'" class="gks_link">'.gks_lang('Προβολή').'</a>';
    
    
    debug_mail(false,'error sql',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();  
  }  
}

if ($product_descr=='') {debug_mail(false,'emptyl',              gks_lang('Η περιγραφή ΔΕΝ μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή ΔΕΝ μπορεί να είναι κενή')));
  echo json_encode($return); die();
}
if ($product_is_simple_download!=0 and $product_is_digital==0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον είναι επιλεγμένο το <b>Απλό Download</b>, θα πρέπει το προϊόν να είναι και <b>Ψηφιακό</b>')));
  echo json_encode($return); die();
} 
if ($product_need_apostoli!=0 and $product_is_digital!=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον είναι επιλεγμένο το <b>Χρειάζεται αποστολή</b>, το προϊόν ΔΕΝ θα πρέπει να είναι <b>Ψηφιακό</b>')));
  echo json_encode($return); die();
} 

if ($product_need_multi_files!=0) {
  if ($product_need_multi_files_min==0 or $product_need_multi_files_max==0) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον είναι επιλεγμένο το <b>Απαιτούνται αρχεία</b>, θα πρέπει να ορίσετε και το <b>Ελάχιστο Πλήθος Αρχείων</b> και το <b>Μέγιστο Πλήθος Αρχείων</b>')));
    echo json_encode($return); die();   
  }
  if ($product_object_name=='') {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον είναι επιλεγμένο το <b>Απαιτούνται αρχεία</b>, θα πρέπει να ορίσετε και το <b>Όνομα Αντικειμένου</b>')));
    echo json_encode($return); die();    
  }
  if (!(strpos($product_object_name, '[[]]') !== false)) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον είναι επιλεγμένο το <b>Απαιτούνται αρχεία</b>, θα το <b>Όνομα Αντικειμένου</b> πρέπει να περιέχει και το <b>[[]]</b> για να γίνεται αντικατάσταση με τον αύξον αριθμό')));
    echo json_encode($return); die();    
  }
}



if ($product_need_apostoli!=0) {
//  if ($product_varos==0) {
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το <b>Βάρος gr</b>')));
//    echo json_encode($return); die();}
//  if ($product_ogos_x==0) {
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το <b>Μήκος cm</b>')));
//    echo json_encode($return); die();}
//  if ($product_ogos_y==0) {
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το <b>Πλάτος cm</b>')));
//    echo json_encode($return); die();}
//  if ($product_ogos_z==0) {
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το <b>Ύψος cm</b>')));
//    echo json_encode($return); die();}
} 
 

//if ($product_taric!='') {
//  $temp='';
//  for($ci=0;$ci < strlen($product_taric);$ci++) {
//    if (in_array($product_taric[$ci],[' ','0','1','2','3','4','5','6','7','8','9'])) {
//      $temp.=$product_taric[$ci];
//    }
//  }
//  if (strlen($temp)!=10 or strlen($product_taric)!=10) {
//    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',trim_gks($_POST['product_taric']), gks_lang('Ο <b>Taric No</b> πρέπει να έχει πλήθος 10 ψηφία.<br>Το <b>[1]</b> είναι λάθος'))));
//    echo json_encode($return); die();      
//  }
//  $product_taric=$temp;
//}

$row_temp = array();
$row_temp['id_product']=$id;
$row_temp['product_monada_id']=$product_monada_id;

$row_temp['product_price_yperx']=$product_price_yperx;
$row_temp['product_price_yperx_include_vat']=$product_price_yperx_include_vat;
$row_temp['product_price_yperx_sale']=$product_price_yperx_sale;
$row_temp['product_price_yperx_sale_from']=$product_price_yperx_sale_from;
$row_temp['product_price_yperx_sale_to']=$product_price_yperx_sale_to;
$row_temp['product_price_yperx_sheets_formula']=$product_price_yperx_sheets_formula;
$row_temp['product_price_yperx_quantity_formula']=$product_price_yperx_quantity_formula;
$row_temp['product_price_yperx_calc']=$product_price_yperx;

$row_temp['product_price']=$product_price;
$row_temp['product_price_include_vat'] =$product_price_include_vat;
$row_temp['product_price_sale']=$product_price_sale;
$row_temp['product_price_sale_from']=$product_price_sale_from;
$row_temp['product_price_sale_to']=$product_price_sale_to;
$row_temp['product_price_sheets_formula']=$product_price_sheets_formula;
$row_temp['product_price_quantity_formula']=$product_price_quantity_formula;
$row_temp['product_price_calc']=$product_price; //den einai aparetito edo na ginei sostos ypologismos toy calc

$row_temp['product_price_retail']=$product_price_retail;
$row_temp['product_price_retail_include_vat']=$product_price_retail_include_vat;
$row_temp['product_price_retail_sale']=$product_price_retail_sale;
$row_temp['product_price_retail_sale_from']=$product_price_retail_sale_from;
$row_temp['product_price_retail_sale_to']=$product_price_retail_sale_to;
$row_temp['product_price_retail_sheets_formula']=$product_price_retail_sheets_formula;
$row_temp['product_price_retail_quantity_formula']=$product_price_retail_quantity_formula;
$row_temp['product_price_retail_calc']=$product_price_retail;

$row_temp['product_price_plist_calc']=0;
$row_temp['product_price_plist']=0;
$row_temp['product_price_plist_sale']=0;
$row_temp['product_price_plist_sale_from']='';
$row_temp['product_price_plist_sale_to']='';
$row_temp['product_price_plist_sheets_formula']='';
$row_temp['product_price_plist_quantity_formula']='';
$row_temp['product_price_plist_include_vat']=0;
$row_temp['quantitycheck_price_plist']=0;

$row_temp['product_kostos']=$product_kostos;
$row_temp['min_quantity_alert']=$min_quantity_alert;


$ret = gks_price_formula_calc($row_temp, 1, $row_temp['product_monada_id'], 1, $out, true,11);
if ($ret!='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Υπάρχει κάποιο πρόβλημα στον υπολογισμό τιμής').'<br>'.$ret));
  echo json_encode($return); die();  
}




if ($product_class=='variable') {
  //variable_products
  $variable_products_temp=trim_gks(base64_decode($_POST['variable_products']));
  $variable_products_temp=json_decode($variable_products_temp, true);
  
  
  foreach ($variable_products_temp as &$value) {
    
    $value['item']['product_photo']=trim_gks($value['item']['product_photo']);
    $value['item']['product_code']=trim_gks($value['item']['product_code']);
    $value['item']['product_descr']=trim_gks($value['item']['product_descr']);
    $value['item']['product_def_comments']=trim_gks($value['item']['product_def_comments']);
    if ($GKS_PRODUCT_DESCR_SMALL) $value['item']['product_descr_small']=trim_gks($value['item']['product_descr_small']);
    $value['item']['product_sku']=trim_gks($value['item']['product_sku']);
    $value['item']['product_gtin']=trim_gks($value['item']['product_gtin']);
    $value['item']['product_upc']=trim_gks($value['item']['product_upc']);
    $value['item']['product_ean']=trim_gks($value['item']['product_ean']);
    $value['item']['product_isbn']=trim_gks($value['item']['product_isbn']);
    $value['item']['product_taric']=trim_gks($value['item']['product_taric']);

    //print '<pre>';print_r($value['item']);die();
    
    
//    if ($value['item']['product_taric']!='') {
//      $temp='';
//      for($ci=0;$ci < strlen($value['item']['product_taric']);$ci++) {
//        if (in_array($value['item']['product_taric'][$ci],[' ','0','1','2','3','4','5','6','7','8','9'])) {
//          $temp.=$value['item']['product_taric'][$ci];
//        }
//      }
//      if (strlen($temp)!=10 or strlen($value['item']['product_taric'])!=10) {
//        $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$value['item']['product_taric'],gks_lang('Ο <b>Taric No</b> πρέπει να έχει πλήθος 10 ψηφία.<br>Το <b>[1]</b> είναι λάθος')));
//        echo json_encode($return); die();      
//      }
//      $value['item']['product_taric']=$temp;
//    }

    $value['item']['product_price_yperx']=floatval($value['item']['product_price_yperx']);
    $value['item']['product_price_yperx_include_vat']=intval($value['item']['product_price_yperx_include_vat']);
    $value['item']['product_price_yperx_sale']=floatval($value['item']['product_price_yperx_sale']);
    $value['item']['product_price_yperx_sale_dates']=intval($value['item']['product_price_yperx_sale_dates']);
    if ($value['item']['product_price_yperx_sale_dates']==0) {
      $value['item']['product_price_yperx_sale_from']='';
      $value['item']['product_price_yperx_sale_to']='';
    } else {
      if ($value['item']['product_price_yperx_sale_from'] == '__/__/____ __:__') $value['item']['product_price_yperx_sale_from']='';
      $value['item']['product_price_yperx_sale_from']=trim_gks(stripslashes(urldecode($value['item']['product_price_yperx_sale_from'])));
      if ($value['item']['product_price_yperx_sale_from']!='') {
        $value['item']['product_price_yperx_sale_from'] = mystrtodb($value['item']['product_price_yperx_sale_from']);
      }
      if ($value['item']['product_price_yperx_sale_to'] == '__/__/____ __:__') $value['item']['product_price_yperx_sale_to']='';
      $value['item']['product_price_yperx_sale_to']=trim_gks(stripslashes(urldecode($value['item']['product_price_yperx_sale_to'])));
      if ($value['item']['product_price_yperx_sale_to']!='') {
        $value['item']['product_price_yperx_sale_to'] = mystrtodb($value['item']['product_price_yperx_sale_to']);
      }
    }    
    $value['item']['product_price_yperx_sheets_formula']=trim_gks($value['item']['product_price_yperx_sheets_formula']);
    $value['item']['product_price_yperx_quantity_formula']=trim_gks($value['item']['product_price_yperx_quantity_formula']);

    $value['item']['product_price']=floatval($value['item']['product_price']);
    $value['item']['product_price_include_vat']=intval($value['item']['product_price_include_vat']);
    $value['item']['product_price_sale']=floatval($value['item']['product_price_sale']);
    $value['item']['product_price_sale_dates']=intval($value['item']['product_price_sale_dates']);
    if ($value['item']['product_price_sale_dates']==0) {
      $value['item']['product_price_sale_from']='';
      $value['item']['product_price_sale_to']='';
    } else {
      if ($value['item']['product_price_sale_from'] == '__/__/____ __:__') $value['item']['product_price_sale_from']='';
      $value['item']['product_price_sale_from']=trim_gks(stripslashes(urldecode($value['item']['product_price_sale_from'])));
      if ($value['item']['product_price_sale_from']!='') {
        $value['item']['product_price_sale_from'] = mystrtodb($value['item']['product_price_sale_from']);
      }
      if ($value['item']['product_price_sale_to'] == '__/__/____ __:__') $value['item']['product_price_sale_to']='';
      $value['item']['product_price_sale_to']=trim_gks(stripslashes(urldecode($value['item']['product_price_sale_to'])));
      if ($value['item']['product_price_sale_to']!='') {
        $value['item']['product_price_sale_to'] = mystrtodb($value['item']['product_price_sale_to']);
      }
    }
    $value['item']['product_price_sheets_formula']=trim_gks($value['item']['product_price_sheets_formula']);
    $value['item']['product_price_quantity_formula']=trim_gks($value['item']['product_price_quantity_formula']);

    $value['item']['product_price_retail']=floatval($value['item']['product_price_retail']);
    $value['item']['product_price_retail_include_vat']=intval($value['item']['product_price_retail_include_vat']);
    $value['item']['product_price_retail_sale']=floatval($value['item']['product_price_retail_sale']);
    $value['item']['product_price_retail_sale_dates']=intval($value['item']['product_price_retail_sale_dates']);
    if ($value['item']['product_price_retail_sale_dates']==0) {
      $value['item']['product_price_retail_sale_from']='';
      $value['item']['product_price_retail_sale_to']='';
    } else {
      if ($value['item']['product_price_retail_sale_from'] == '__/__/____ __:__') $value['item']['product_price_retail_sale_from']='';
      $value['item']['product_price_retail_sale_from']=trim_gks(stripslashes(urldecode($value['item']['product_price_retail_sale_from'])));
      if ($value['item']['product_price_retail_sale_from']!='') {
        $value['item']['product_price_retail_sale_from'] = mystrtodb($value['item']['product_price_retail_sale_from']);
      }
      if ($value['item']['product_price_retail_sale_to'] == '__/__/____ __:__') $value['item']['product_price_retail_sale_to']='';
      $value['item']['product_price_retail_sale_to']=trim_gks(stripslashes(urldecode($value['item']['product_price_retail_sale_to'])));
      if ($value['item']['product_price_retail_sale_to']!='') {
        $value['item']['product_price_retail_sale_to'] = mystrtodb($value['item']['product_price_retail_sale_to']);
      }
    }    
    $value['item']['product_price_retail_sheets_formula']=trim_gks($value['item']['product_price_retail_sheets_formula']);
    $value['item']['product_price_retail_quantity_formula']=trim_gks($value['item']['product_price_retail_quantity_formula']);
    
    
    $value['item']['product_kostos']=($value['item']['product_kostos']==='' ? '' : floatval($value['item']['product_kostos']));
    $value['item']['min_quantity_alert']=($value['item']['min_quantity_alert']==='' ? 0 : floatval($value['item']['min_quantity_alert']));
    
    
    
    $value['item']['product_varos']=floatval($value['item']['product_varos']);
    $value['item']['product_ogos_x']=floatval($value['item']['product_ogos_x']);
    $value['item']['product_ogos_y']=floatval($value['item']['product_ogos_y']);
    $value['item']['product_ogos_z']=floatval($value['item']['product_ogos_z']);
    if ($value['item']['product_varos']<0) $value['item']['product_varos']=0;
    if ($value['item']['product_ogos_x']<0) $value['item']['product_ogos_x']=0;
    if ($value['item']['product_ogos_y']<0) $value['item']['product_ogos_y']=0;
    if ($value['item']['product_ogos_z']<0) $value['item']['product_ogos_z']=0;
    if ($product_need_apostoli==0) {$value['item']['product_varos']=0;$value['item']['product_ogos_x']=0;$value['item']['product_ogos_y']=0;$value['item']['product_ogos_z']=0;}

   
    $value['item']['product_fpa_base_id']=intval($value['item']['product_fpa_base_id']);
    
    
    $row_temp = array();
    $row_temp['id_product']=0; $id;
    $row_temp['product_monada_id']=$product_monada_id;
    
    $row_temp['product_price_yperx']=$value['item']['product_price_yperx'];
    $row_temp['product_price_yperx_include_vat']=$value['item']['product_price_yperx_include_vat'];
    $row_temp['product_price_yperx_sale']=$value['item']['product_price_yperx_sale'];
    $row_temp['product_price_yperx_sale_from']=$value['item']['product_price_yperx_sale_from'];
    $row_temp['product_price_yperx_sale_to']=$value['item']['product_price_yperx_sale_to'];
    $row_temp['product_price_yperx_sheets_formula']=$value['item']['product_price_yperx_sheets_formula'];
    $row_temp['product_price_yperx_quantity_formula']=$value['item']['product_price_yperx_quantity_formula'];
    $row_temp['product_price_yperx_calc']=$value['item']['product_price_yperx'];
    
    $row_temp['product_price']=$value['item']['product_price'];
    $row_temp['product_price_include_vat'] =$value['item']['product_price_include_vat'];
    $row_temp['product_price_sale']=$value['item']['product_price_sale'];
    $row_temp['product_price_sale_from']=$value['item']['product_price_sale_from'];
    $row_temp['product_price_sale_to']=$value['item']['product_price_sale_to'];
    $row_temp['product_price_sheets_formula']=$value['item']['product_price_sheets_formula'];
    $row_temp['product_price_quantity_formula']=$value['item']['product_price_quantity_formula'];
    $row_temp['product_price_calc']=$value['item']['product_price']; //den einai aparetito edo na ginei sostos ypologismos toy calc
    
    $row_temp['product_price_retail']=$value['item']['product_price_retail'];
    $row_temp['product_price_retail_include_vat']=$value['item']['product_price_retail_include_vat'];
    $row_temp['product_price_retail_sale']=$value['item']['product_price_retail_sale'];
    $row_temp['product_price_retail_sale_from']=$value['item']['product_price_retail_sale_from'];
    $row_temp['product_price_retail_sale_to']=$value['item']['product_price_retail_sale_to'];
    $row_temp['product_price_retail_sheets_formula']=$value['item']['product_price_retail_sheets_formula'];
    $row_temp['product_price_retail_quantity_formula']=$value['item']['product_price_retail_quantity_formula'];
    $row_temp['product_price_retail_calc']=$value['item']['product_price_retail'];
    
    $row_temp['product_price_plist_calc']=0;
    $row_temp['product_price_plist']=0;
    $row_temp['product_price_plist_sale']=0;
    $row_temp['product_price_plist_sale_from']='';
    $row_temp['product_price_plist_sale_to']='';
    $row_temp['product_price_plist_sheets_formula']='';
    $row_temp['product_price_plist_quantity_formula']='';
    $row_temp['product_price_plist_include_vat']=0;
    $row_temp['quantitycheck_price_plist']=0;
    
    $row_temp['product_kostos']=$value['item']['product_kostos'];
    $row_temp['min_quantity_alert']=$value['item']['min_quantity_alert'];
    
    
    $ret = gks_price_formula_calc($row_temp, 1, $row_temp['product_monada_id'], 1, $out, true,11);
    if ($ret!='') {
      debug_mail(false,'error gks_price_formula_calc',$ret);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Υπάρχει κάποιο πρόβλημα στον υπολογισμό τιμής').'<br>'.$ret));
      echo json_encode($return); die();  
    }
    
        
  }
  unset($value);
  //print '<pre>';print_r($variable_products_temp);die();
  
  
  
  $all_codes=array();
  if ($product_code!='') $all_codes[]=$product_code;
  foreach ($variable_products_temp as $value) {
    if ($value['item']['product_code']!='') {
      if (in_array($value['item']['product_code'], $all_codes)) {
        debug_mail(false,'error gks_price_formula_calc',$ret);
        $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$value['item']['product_code'],gks_lang('Ο κωδικός <b>[1]</b> υπάρχει 2 φορές'))));
        echo json_encode($return); die();  
      }
      $all_codes[]=$value['item']['product_code'];
    }
  }
  
  if (count($all_codes)>0) {
    $sql_in='';
    foreach ($all_codes as $value) {
      $sql_in.="product_code like '".$db_link->escape_string($value)."' or ";
    } 
    $sql_in=substr($sql_in, 0, strlen($sql_in)-4);
    
    
    $sql="select id_product,product_code,product_descr from gks_eshop_products 
    where (".$sql_in.") 
    and id_product<>".$id."
    and product_parent_id<>".$id."
    and product_parent_old_id=0";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    $message='';
    while ($row = $result->fetch_assoc()) {
      $temp= gks_lang('Ο κωδικός <b>[1]</b> υπάρχει ήδη σε άλλο είδος:<br><b>[2]</b>');
      $temp=str_replace('[1]',$row['product_code'],$temp);
      $temp=str_replace('[2]',$row['product_descr'],$temp);
      
      $message.=$temp.
      '<br><a href="admin-products-item.php?id='.$row['id_product'].'" class="gks_link">'.gks_lang('Προβολή').'</a><br>';
    }  
    if ($message!='') {
      debug_mail(false,'error sql',$message);
      $return = array('success' => false, 'message' => base64_encode($message));
      echo json_encode($return); die();  
    }  
  }

  //print '<pre>';print_r($all_codes);die();
  
  //print '<pre>';print_r($variable_products_temp);die();
  
}

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_eshop_products');


$redirect='';
if ($id==-1) {
  $sql="insert into gks_eshop_products (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-products-item.php?id='.$id); 
  

}

//product_sortorder=".$product_sortorder.",

$sql="update gks_eshop_products set 
product_class='".$db_link->escape_string($product_class)."',
product_photo='".$db_link->escape_string($form_product_photo)."',
product_code='".$db_link->escape_string($product_code)."',
product_descr='".$db_link->escape_string($product_descr)."',
product_def_comments='".$db_link->escape_string($product_def_comments)."',
";

if ($GKS_PRODUCT_DESCR_SMALL) $sql.="product_descr_small=". ($product_descr_small =='' ? 'null' : "'".$db_link->escape_string($product_descr_small)."'").",";
if ($GKS_PRODUCT_DESCR_BIG)   $sql.="product_descr_big=". ($product_descr_big =='' ? 'null' : "'".$db_link->escape_string($product_descr_big)."'").",";


$sql.="
product_object_name=". ($product_object_name =='' ? 'null' : "'".$db_link->escape_string($product_object_name)."'").",
product_can_sell=".$product_can_sell.",
product_can_buy=".$product_can_buy.",


product_is_digital=".$product_is_digital.",
product_is_simple_download=".$product_is_simple_download.",
product_base_type=".$product_base_type.",
product_sku='".$db_link->escape_string($product_sku)."',
product_gtin='".$db_link->escape_string($product_gtin)."',
product_upc='".$db_link->escape_string($product_upc)."',
product_ean='".$db_link->escape_string($product_ean)."',
product_isbn='".$db_link->escape_string($product_isbn)."',
product_taric='".$db_link->escape_string($product_taric)."',
".($GKS_PRODUCT_LOTS_SERIALS ? "product_lot_serial=".($product_lot_serial=='' ? 'null' : "'".$db_link->escape_string($product_lot_serial)."'")."," : '')."
product_need_apostoli=".$product_need_apostoli.",
product_need_multi_files=".$product_need_multi_files.",
product_show_on_dialog=".$product_show_on_dialog.",
product_fpa_base_id=".$product_fpa_base_id.",
product_fpa_ejeresi_id=".$product_fpa_ejeresi_id.",
product_monada_id=".$product_monada_id.",
product_need_multi_files_min=".$product_need_multi_files_min.",
product_need_multi_files_max=".$product_need_multi_files_max.",
product_min_pixels_x=".$product_min_pixels_x.",
product_min_pixels_y=".$product_min_pixels_y.",

product_price_yperx=".number_format($product_price_yperx,8,'.','').",
product_price_yperx_include_vat=".$product_price_yperx_include_vat.",
product_price_yperx_sale=".number_format($product_price_yperx_sale,8,'.','').",
product_price_yperx_sale_from=".($product_price_yperx_sale_from == '' ? 'null' : "'".$db_link->escape_string($product_price_yperx_sale_from)."'") .", 
product_price_yperx_sale_to=".($product_price_yperx_sale_to == '' ? 'null' : "'".$db_link->escape_string($product_price_yperx_sale_to)."'") .", 
product_price_yperx_sheets_formula='".$db_link->escape_string($product_price_yperx_sheets_formula)."',
product_price_yperx_quantity_formula='".$db_link->escape_string($product_price_yperx_quantity_formula)."',

product_price=".number_format($product_price,8,'.','').",
product_price_include_vat=".$product_price_include_vat.",
product_price_sale=".number_format($product_price_sale,8,'.','').",
product_price_sale_from=".($product_price_sale_from == '' ? 'null' : "'".$db_link->escape_string($product_price_sale_from)."'") .", 
product_price_sale_to=".($product_price_sale_to == '' ? 'null' : "'".$db_link->escape_string($product_price_sale_to)."'") .", 
product_price_sheets_formula='".$db_link->escape_string($product_price_sheets_formula)."',
product_price_quantity_formula='".$db_link->escape_string($product_price_quantity_formula)."',

product_price_retail=".number_format($product_price_retail,8,'.','').",
product_price_retail_include_vat=".$product_price_retail_include_vat.",
product_price_retail_sale=".number_format($product_price_retail_sale,8,'.','').",
product_price_retail_sale_from=".($product_price_retail_sale_from == '' ? 'null' : "'".$db_link->escape_string($product_price_retail_sale_from)."'") .", 
product_price_retail_sale_to=".($product_price_retail_sale_to == '' ? 'null' : "'".$db_link->escape_string($product_price_retail_sale_to)."'") .", 
product_price_retail_sheets_formula='".$db_link->escape_string($product_price_retail_sheets_formula)."',
product_price_retail_quantity_formula='".$db_link->escape_string($product_price_retail_quantity_formula)."',

product_kostos=".($product_kostos==='' ? 'null' : number_format($product_kostos,8,'.','')).",

product_varos='".number_format($product_varos,8,'.','')."',
product_ogos_x='".number_format($product_ogos_x,8,'.','')."',
product_ogos_y='".number_format($product_ogos_y,8,'.','')."',
product_ogos_z='".number_format($product_ogos_z,8,'.','')."',

product_min_pixels_can_rotate=".$product_min_pixels_can_rotate.",
product_disable=".$product_disable.",
use_only_mine_ergasies=".$use_only_mine_ergasies.",

product_withheldPercentCategory=".$product_withheldPercentCategory.",
product_otherTaxesPercentCategory=".$product_otherTaxesPercentCategory.",
product_stampDutyPercentCategory=".$product_stampDutyPercentCategory.",
product_feesPercentCategory=".$product_feesPercentCategory.",

internal_note='".$db_link->escape_string($internal_note)."',
def_supplier=".$def_supplier.",
min_quantity_alert=".$min_quantity_alert.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_product = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
    
//
//product_descr_small=". ($product_descr_small =='' ? 'null' : "'".$db_link->escape_string($product_descr_small)."'").",
//if ($GKS_PRODUCT_DESCR_BIG) $sql.="product_descr_big=". ($product_descr_big =='' ? 'null' : "'".$db_link->escape_string($product_descr_big)."'").",";

$sql="update gks_eshop_products set 
product_descr_variable='',";


$sql.="
product_object_name=". ($product_object_name =='' ? 'null' : "'".$db_link->escape_string($product_object_name)."'").",
product_can_sell=".$product_can_sell.",
product_can_buy=".$product_can_buy.",

product_is_digital=".$product_is_digital.",
product_is_simple_download=".$product_is_simple_download.",
product_base_type=".$product_base_type.",
product_need_apostoli=".$product_need_apostoli.",
".($GKS_PRODUCT_LOTS_SERIALS ? "product_lot_serial=".($product_lot_serial=='' ? 'null' : "'".$db_link->escape_string($product_lot_serial)."'")."," : '')."
product_need_multi_files=".$product_need_multi_files.",
product_show_on_dialog=".$product_show_on_dialog.",
product_fpa_ejeresi_id=".$product_fpa_ejeresi_id.",
product_monada_id=".$product_monada_id.",
product_need_multi_files_min=".$product_need_multi_files_min.",
product_need_multi_files_max=".$product_need_multi_files_max.",
product_min_pixels_x=".$product_min_pixels_x.",
product_min_pixels_y=".$product_min_pixels_y.",



product_min_pixels_can_rotate=".$product_min_pixels_can_rotate.",
product_disable=".$product_disable.",
use_only_mine_ergasies=".$use_only_mine_ergasies.",

product_withheldPercentCategory=".$product_withheldPercentCategory.",
product_otherTaxesPercentCategory=".$product_otherTaxesPercentCategory.",
product_stampDutyPercentCategory=".$product_stampDutyPercentCategory.",
product_feesPercentCategory=".$product_feesPercentCategory.",

internal_note='".$db_link->escape_string($internal_note)."',
def_supplier=".$def_supplier.",


user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where product_parent_id = ".$id;
//echo '<pre>';echo $sql;die();
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

// Alles times se timokatalagous
$sql="SELECT id_product_price, pricelist_id
FROM gks_eshop_products_prices
WHERE product_id=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
$exist_prices=[];
while ($row = $result->fetch_assoc()) {
  $exist_prices[]=$row;
}   

$not_delete_id_product_price=[];  
foreach ($array_product_price_plist as $plist_item) {
  $id_product_price=0;
  foreach ($exist_prices as $value) {
    if ($value['pricelist_id']==$plist_item['id_pricelist']) {
      $id_product_price=$value['id_product_price'];break;
    }
  }
  if ($id_product_price==0) {
    $sql="insert into gks_eshop_products_prices (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    pricelist_id,product_id,
    product_price_plist,
    product_price_plist_include_vat,
    product_price_plist_sale,
    product_price_plist_sale_from,
    product_price_plist_sale_to,
    product_price_plist_sheets_formula,
    product_price_plist_quantity_formula
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$plist_item['id_pricelist'].",".$id.",
    ".number_format($plist_item['product_price_plist'],8,'.','').",
    ".$plist_item['product_price_plist_include_vat'].",
    ".number_format($plist_item['product_price_plist_sale'],8,'.','').",
    ".($plist_item['product_price_plist_sale_from'] == '' ? 'null' : "'".$db_link->escape_string($plist_item['product_price_plist_sale_from'])."'") .", 
    ".($plist_item['product_price_plist_sale_to'] == '' ? 'null' : "'".$db_link->escape_string($plist_item['product_price_plist_sale_to'])."'") .", 
    '".$db_link->escape_string($plist_item['product_price_plist_sheets_formula'])."',
    '".$db_link->escape_string($plist_item['product_price_plist_quantity_formula'])."'
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $not_delete_id_product_price[] = $db_link->insert_id; 
  } else {
    $sql="update gks_eshop_products_prices set
    product_price_plist=".number_format($plist_item['product_price_plist'],8,'.','').",
    product_price_plist_include_vat=".$plist_item['product_price_plist_include_vat'].",
    product_price_plist_sale=".number_format($plist_item['product_price_plist_sale'],8,'.','').",
    product_price_plist_sale_from=".($plist_item['product_price_plist_sale_from'] == '' ? 'null' : "'".$db_link->escape_string($plist_item['product_price_plist_sale_from'])."'") .", 
    product_price_plist_sale_to=".($plist_item['product_price_plist_sale_to'] == '' ? 'null' : "'".$db_link->escape_string($plist_item['product_price_plist_sale_to'])."'") .", 
    product_price_plist_sheets_formula='".$db_link->escape_string($plist_item['product_price_plist_sheets_formula'])."',
    product_price_plist_quantity_formula='".$db_link->escape_string($plist_item['product_price_plist_quantity_formula'])."',
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_product_price=".$id_product_price;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $not_delete_id_product_price[]=$id_product_price;
    
  }  
}
$sql="delete from gks_eshop_products_prices 
where product_id=".$id;
if (count($not_delete_id_product_price)>0) {
  $sql.=" and id_product_price not in (".implode(',',$not_delete_id_product_price).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }



//print '<pre>';print_r($exist_prices);die();

if (1==1) { //Esoda
  $sql="update gks_eshop_products_income set 
  acc_eidos_parastatikou_id=0,aade_typos_xarakt_esodon_id=0,aade_katigoria_xarakt_esodon_id=0,acc_inv_product_income_pososto=0
  where product_id=".$id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $sql="select id_product_income from gks_eshop_products_income where product_id=".$id." order by id_product_income";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $exist_ids=array();
  while ($row = $result->fetch_assoc()) {
    $exist_ids[]=$row['id_product_income'];
  }   
  
  foreach ($xarakt_esoda as $value) {
    $id_found=0;
    foreach ($exist_ids as &$oid) {if ($oid>0) {$id_found=$oid;$oid=0;break;}} unset($oid);
    if ($id_found>0) {
      $sql="update gks_eshop_products_income set 
      acc_eidos_parastatikou_id=".$value['ep_id'].",
      aade_katigoria_xarakt_esodon_id=".$value['cat_id'].",
      aade_typos_xarakt_esodon_id=".$value['typos_id'].",
      acc_inv_product_income_pososto=".number_format($value['ammount'],10, '.','') ."
      where id_product_income=".$id_found;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    } else {
      $sql="insert into gks_eshop_products_income (
        product_id,acc_eidos_parastatikou_id,aade_katigoria_xarakt_esodon_id,aade_typos_xarakt_esodon_id,acc_inv_product_income_pososto
      ) values (
        ".$id.",
        ".$value['ep_id'].",
        ".$value['cat_id'].",
        ".$value['typos_id'].",
        ".number_format($value['ammount'],10, '.','')."
      )";        
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
  $for_del=array();
  foreach ($exist_ids as $oid) if ($oid>0) $for_del[]=$oid;
  if (count($for_del)>0) {
    $sql="delete from gks_eshop_products_income where id_product_income in (".implode(',', $for_del).")";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
}
  
if (1==1) { //Ejoda
  
  $sql="update gks_eshop_products_expenses set 
  acc_eidos_parastatikou_id=0,aade_typos_xarakt_eksodon_id=0,aade_katigoria_xarakt_eksodon_id=0,acc_inv_product_expenses_pososto=0
  where product_id=".$id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $sql="select id_product_expenses from gks_eshop_products_expenses where product_id=".$id." order by id_product_expenses";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $exist_ids=array();
  while ($row = $result->fetch_assoc()) {
    $exist_ids[]=$row['id_product_expenses'];
  }   
  
  foreach ($xarakt_eksoda as $value) {
    $id_found=0;
    foreach ($exist_ids as &$oid) {if ($oid>0) {$id_found=$oid;$oid=0;break;}} unset($oid);
    if ($id_found>0) {
      $sql="update gks_eshop_products_expenses set 
      acc_eidos_parastatikou_id=".$value['ep_id'].",
      aade_katigoria_xarakt_eksodon_id=".$value['cat_id'].",
      aade_typos_xarakt_eksodon_id=".$value['typos_id'].",
      acc_inv_product_expenses_pososto=".number_format($value['ammount'],10, '.','') ."
      where id_product_expenses=".$id_found;
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    } else {
      $sql="insert into gks_eshop_products_expenses (
        product_id,acc_eidos_parastatikou_id,aade_katigoria_xarakt_eksodon_id,aade_typos_xarakt_eksodon_id,acc_inv_product_expenses_pososto
      ) values (
        ".$id.",
        ".$value['ep_id'].",
        ".$value['cat_id'].",
        ".$value['typos_id'].",
        ".number_format($value['ammount'],10, '.','')."
      )";        
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
  $for_del=array();
  foreach ($exist_ids as $oid) if ($oid>0) $for_del[]=$oid;
  if (count($for_del)>0) {
    $sql="delete from gks_eshop_products_expenses where id_product_expenses in (".implode(',', $for_del).")";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }    
}



//idiotites
$idiotites_temp=trim_gks(base64_decode($_POST['idiotites']));
$idiotites_temp=json_decode($idiotites_temp, true);
$sql="select id_product_idiotita_term as id, idiotita_id as piid,idiotita_term_name as `name` 
from gks_product_idiotites_terms
order by id_product_idiotita_term";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$all_terms=array();
while ($row = $result->fetch_assoc()) {
  $all_terms[$row['id']]=array('id'=>$row['id'],'piid'=>$row['piid'],'name'=>$row['name']);
}

gks_build_GKS_LANG_DATA_ARRAY();
//print '<pre>';print_r($GKS_LANG_DATA_ARRAY);die();
$all_terms_per_lang=array();
foreach ($GKS_LANG_DATA_ARRAY as $lang_item) {
  $sql="SELECT gks_product_idiotites_terms.id_product_idiotita_term AS id, gks_product_idiotites_terms.idiotita_id AS piid, 
  gks_product_idiotites_terms.idiotita_term_name AS name, name_en_us, name_other
  FROM (gks_product_idiotites_terms 
  LEFT JOIN (
    SELECT product_idiotita_term_id, idiotita_term_name as name_en_us
    FROM gks_product_idiotites_terms_lang
    WHERE lang_code='".$lang_item['id_lang']."'
  ) AS lang_en_us ON gks_product_idiotites_terms.id_product_idiotita_term = lang_en_us.product_idiotita_term_id) 
  LEFT JOIN (
    SELECT product_idiotita_term_id, idiotita_term_name as name_other
    FROM gks_product_idiotites_terms_lang
    WHERE lang_code='de-DE'
  ) AS lang_other ON gks_product_idiotites_terms.id_product_idiotita_term = lang_other.product_idiotita_term_id
  order by id_product_idiotita_term";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $all_terms_per_lang[$lang_item['id_lang']]=array();
  while ($row = $result->fetch_assoc()) {
    $tname='';
    if ($lang_item['id_lang']=='en-US') {
      $tname=trim_gks($row['name_en_us']);
    } else {
      $tname=trim_gks($row['name_other']);
      if ($tname=='') $tname=trim_gks($row['name_en_us']);
    }
    if ($tname=='') $tname=$row['name'];
    
    $all_terms_per_lang[$lang_item['id_lang']][$row['id']]=array('id'=>$row['id'],'piid'=>$row['piid'],'name'=>$tname);
  }  
  
} 
//print '<pre>';print_r($all_terms_per_lang);die();

$idiotites_new=array();
foreach ($idiotites_temp as $value1) {
  $terms_ids=array();
  foreach ($value1['terms'] as $value2) {
    foreach ($all_terms as $value3) {
      if ($value3['piid']==$value1['id']) {
        if ($value3['name'] == $value2) {
          if (in_array($value3['id'],$terms_ids)==false) $terms_ids[]=$value3['id'];
          break;
        }
      }
    }
  }
  if (count($terms_ids)>0) {
    $idiotites_new[$value1['id']]=array(
      'id' => $value1['id'],
      'terms' => $terms_ids,
      'isv' => ($value1['isv']==1 ? 1 : 0),
      'is_new' => true,
    );
  }
}


$sql="select id_eshop_products_idiotites,product_idiotita_id,idiotita_is_variable from gks_eshop_products_idiotites where product_id=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
$ids=array();  
$idiotites_exist=array();
while ($row = $result->fetch_assoc()) {
  $ids[]=$row['id_eshop_products_idiotites'];
  $idiotites_exist[$row['id_eshop_products_idiotites']]=array(
    'id_eshop_products_idiotites' => $row['id_eshop_products_idiotites'],
    'product_idiotita_id' => $row['product_idiotita_id'],
    'idiotita_is_variable' => $row['idiotita_is_variable'],
    'terms' => array(),
    'nodelete' => false,
  );
}

if (count($ids)>0) {
  $sql="select id_eshop_products_idiotites_terms,eshop_products_idiotites_id,product_idiotita_term_id 
  from gks_eshop_products_idiotites_terms 
  where eshop_products_idiotites_id in (".implode(',',$ids).")";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  while ($row = $result->fetch_assoc()) {
    if (isset($idiotites_exist[$row['eshop_products_idiotites_id']])) {
      $idiotites_exist[$row['eshop_products_idiotites_id']]['terms'][]=array(
        'id_eshop_products_idiotites_terms' => $row['id_eshop_products_idiotites_terms'],
        'product_idiotita_term_id' => $row['product_idiotita_term_id'],
      );
    }
  }  
}


foreach ($idiotites_new as &$v_new) {
  foreach ($idiotites_exist as &$v_exist) {
    if ($v_new['id']==$v_exist['product_idiotita_id']) { //vrika idiotita
      $v_exist['nodelete']=true; //na min diagrafei
      $v_new['is_new']=false;
      
      //update to record
      $sql="update gks_eshop_products_idiotites set 
      user_id_edit=".$my_wp_user_id.",
      mydate_edit=now(),
      myip='".$db_link->escape_string($gkIP)."',
      idiotita_is_variable=".$v_new['isv']."
      where id_eshop_products_idiotites=".$v_exist['id_eshop_products_idiotites'];
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      
      foreach ($v_new['terms'] as &$term_new) {
        foreach ($v_exist['terms'] as &$term_exist) {
          if ($term_new==$term_exist['product_idiotita_term_id']) {
            $term_exist['nodelete_term']=true;
            $term_new=0; //is ok, no new add
            break;
          }
        }
        unset($term_exist);
      }
      unset($term_new);

      $del_id_eshop_products_idiotites_terms=array();
      foreach ($v_exist['terms'] as $term_exist) {
        if (!(isset($term_exist['nodelete_term']) and $term_exist['nodelete_term'])) {
          $del_id_eshop_products_idiotites_terms[]=$term_exist['id_eshop_products_idiotites_terms'];
        }
      }
      if (count($del_id_eshop_products_idiotites_terms) > 0) {
        $sql="delete from gks_eshop_products_idiotites_terms where id_eshop_products_idiotites_terms in (".implode(',',$del_id_eshop_products_idiotites_terms).")";
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      }
      
      foreach ($v_new['terms'] as $term_new) {
        if ($term_new>0) { //einai neo, na prostethei
          $sql="insert into gks_eshop_products_idiotites_terms (
            user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
            eshop_products_idiotites_id,product_idiotita_term_id
          ) values (
            ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
            ".$v_exist['id_eshop_products_idiotites'].",".$term_new."
          )";
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }
        }
      }
      break;
    }
   
  }
  unset($v_exist);
}
unset($v_new);

foreach ($idiotites_exist as $v_exist) {
  if (!(isset($v_exist['nodelete']) and $v_exist['nodelete'])) {
    $sql="delete from gks_eshop_products_idiotites where id_eshop_products_idiotites=".$v_exist['id_eshop_products_idiotites'];
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $sql="delete from gks_eshop_products_idiotites_terms where eshop_products_idiotites_id=".$v_exist['id_eshop_products_idiotites'];
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
  }
}

foreach ($idiotites_new as $v_new) {
  if ($v_new['is_new']) {
    $sql="insert into gks_eshop_products_idiotites (
      user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
      product_id,product_idiotita_id,idiotita_is_variable
    ) values (
      ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
      ".$id.",".$v_new['id'].",".$v_new['isv']."
    )";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $id_eshop_products_idiotites = $db_link->insert_id; 
    
    foreach ($v_new['terms'] as $term_new) {
      $sql="insert into gks_eshop_products_idiotites_terms (
        user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
        eshop_products_idiotites_id,product_idiotita_term_id
      ) values (
        ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
        ".$id_eshop_products_idiotites.",".$term_new."
      )";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
    }
  }
}
//print '<pre>';print_r($idiotites_temp);print_r($idiotites_new);die();//print_r($all_terms);
//print '<pre>';print_r($idiotites_new);die();//print_r($all_terms);
//print '<pre>'; print_r($idiotites_new);print_r($idiotites_exist);die();






if ($product_class!='variable') {

  $sql="update gks_eshop_products set 
  product_disable=1,
  product_parent_id=0,
  product_parent_old_id=".$id."
  where product_parent_id=".$id;
  //if (count($exist_ids)>0) $sql.=" and id_product not in (".implode(',',$exist_ids).")";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
      
} else {
  
   
  $sql="SELECT gks_eshop_products_idiotites.product_idiotita_id, gks_eshop_products_idiotites_terms.product_idiotita_term_id
  FROM gks_eshop_products_idiotites 
  LEFT JOIN gks_eshop_products_idiotites_terms ON gks_eshop_products_idiotites.id_eshop_products_idiotites = gks_eshop_products_idiotites_terms.eshop_products_idiotites_id
  WHERE gks_eshop_products_idiotites.product_id=".$id."
  AND gks_eshop_products_idiotites.idiotita_is_variable=1
  AND gks_eshop_products_idiotites_terms.eshop_products_idiotites_id Is Not Null;";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $product_valids=array();
  while ($row = $result->fetch_assoc()) {
    $product_valids[$row['product_idiotita_id']][$row['product_idiotita_term_id']]=1;
  } 
  //print '<pre>';print_r($product_valids);die();
  
  $variable_products_new=array();
  foreach ($variable_products_temp as $value1) {
  
    $pidiotites=array();
    foreach ($value1['pidiotites'] as $value2) {
      if (isset($product_valids[$value2['iid']][$value2['val']])) { //idiotita kai term exist in product
        $pidiotites[$value2['iid']]=$value2['val'];
      }
    }
    $variable_products_new[]=array(
      'paa'=>$value1['paa'],
      'pid'=>$value1['pid'],
      'pidiotites'=>$pidiotites,
      'item' =>$value1['item'],
    );
  }
  
  $sql="select id_product from gks_eshop_products where product_parent_old_id=".$id." and product_parent_id=0 and product_class='variable_item'";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $old_variable_products=array();
  while ($row = $result->fetch_assoc()) {
    $old_variable_products[$row['id_product']]=array('id'=>$row['id_product'],'terms' => array());
  }
  if (count($old_variable_products)>0) {
    $ids=array_keys($old_variable_products);
    $sql="SELECT product_id,product_idiotita_term_id
    FROM gks_eshop_products_variables
    WHERE product_id in (".implode(',',$ids).")";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    while ($row = $result->fetch_assoc()) {
      $old_variable_products[$row['product_id']]['terms'][]=$row['product_idiotita_term_id'];
    }
  }
  //print '<pre>old_variable_products';print_r($old_variable_products);die();

  $lang_data_obj_variable=gks_lang_data_obj_prepare('gks_eshop_products','default');
  if ($lang_data_obj_variable['success']==false) {
    debug_mail(false,'gks_lang_data_obj_prepare', print_r($lang_data_obj_variable,true));
    $return = array('success' => false, 'message' => base64_encode($lang_data_obj_variable['message']));
    echo json_encode($return); die(); }

  
  foreach ($variable_products_new as &$value) {
    if ($value['pid']==0) {
      //prin prosteso neo proion, na dv mipos yparxei palio me ta idia terms
      
      $find_old_product=0;
      $check1=$value['pidiotites'];
      foreach ($old_variable_products as &$value2) {
        if ($value2['id']>0) {
          $check2=$value2['terms'];
          if (count($check1)==count($check2)) {
            //echo '<pre>sss ';print_r($check1);print_r($check2);//die();
            foreach ($check1 as $kc1 => $kv1) {
              foreach ($check2 as $kc2 => $kv2) { //iparxei to antistoixo term
                if ($kv1==$kv2) {
                  $check2[$kc2]=0;
                }
              } 
            }
            $is_all_zero=true;
            foreach ($check2 as $kc2 => $kv2) { 
              if ($kv2!=0) {$is_all_zero=false;break;}//ean ola zero, tote einai omoiioi pinakes, ara yparxei to antistoixo
            } 
            if ($is_all_zero) {
              $find_old_product=$value2['id'];
              $value2['id']=0; //ayto vrethike kai apo palio tha ksanaginei neo, opote ektos listas, kameno..
              //echo '<pre>';print_r($check1);print_r($check2);print "\n".$find_old_product;die();
              break;
            }
          }
        }
      }
      unset($value2);
      
      //echo '<pre>';print_r($value['pidiotites']);print_r($check1);print_r($old_variable_products);print "\n".$find_old_product;die();
      if ($find_old_product>0) {
        $sql="update gks_eshop_products set
        product_disable=0,
        product_parent_id=".$id.",
        product_parent_old_id=0
        where id_product=".$find_old_product;
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        $value['pid']=$find_old_product;
        
      } else {
        $sql="insert into gks_eshop_products (
          user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
          product_parent_id,product_class
        ) values (
          ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
          ".$id.",'variable_item'
        )";
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        $value['pid'] = $db_link->insert_id;
        

      } 
    }
    
    //check it before save any
    
    //print '<pre>';  echo 'variable_products_new:'; print_r($variable_products_new);die();
    $product_descr_variable=array();
    foreach ($value['pidiotites'] as $pterm) {
      if ($pterm > 0 and isset($all_terms[$pterm]) ) $product_descr_variable[]=$all_terms[$pterm]['name'];
    } 
    //print '<pre>';  print_r($product_descr_variable);print_r($value);print_r($all_terms);  die();
    
    $value['item']['product_descr_variable']=implode('-',$product_descr_variable);
    
    $sql="update gks_eshop_products set 
    product_photo='".$db_link->escape_string($value['item']['product_photo'])."',
    product_code='".$db_link->escape_string($value['item']['product_code'])."',
    product_descr='".$db_link->escape_string($value['item']['product_descr'])."',
    product_def_comments='".$db_link->escape_string($value['item']['product_def_comments'])."',";
    
    if ($GKS_PRODUCT_DESCR_SMALL) $sql.="product_descr_small='".$db_link->escape_string($value['item']['product_descr_small'])."',";
    
    $sql.="
    product_descr_variable='".$db_link->escape_string($value['item']['product_descr_variable'])."',
    product_sku='".$db_link->escape_string($value['item']['product_sku'])."',
    product_gtin='".$db_link->escape_string($value['item']['product_gtin'])."',
    product_upc='".$db_link->escape_string($value['item']['product_upc'])."',
    product_ean='".$db_link->escape_string($value['item']['product_ean'])."',
    product_isbn='".$db_link->escape_string($value['item']['product_isbn'])."',
    product_taric='".$db_link->escape_string($value['item']['product_taric'])."',

    product_price_yperx=".number_format($value['item']['product_price_yperx'],8,'.','').",
    product_price_yperx_include_vat=".$value['item']['product_price_yperx_include_vat'].",
    product_price_yperx_sale=".number_format($value['item']['product_price_yperx_sale'],8,'.','').",
    product_price_yperx_sale_from=".($value['item']['product_price_yperx_sale_from'] == '' ? 'null' : "'".$db_link->escape_string($value['item']['product_price_yperx_sale_from'])."'") .", 
    product_price_yperx_sale_to=".($value['item']['product_price_yperx_sale_to'] == '' ? 'null' : "'".$db_link->escape_string($value['item']['product_price_yperx_sale_to'])."'") .", 
    product_price_yperx_sheets_formula='".$db_link->escape_string($value['item']['product_price_yperx_sheets_formula'])."',
    product_price_yperx_quantity_formula='".$db_link->escape_string($value['item']['product_price_yperx_quantity_formula'])."',


    product_price=".number_format($value['item']['product_price'],8,'.','').",
    product_price_include_vat=".$value['item']['product_price_include_vat'].",
    product_price_sale=".number_format($value['item']['product_price_sale'],8,'.','').",
    product_price_sale_from=".($value['item']['product_price_sale_from'] == '' ? 'null' : "'".$db_link->escape_string($value['item']['product_price_sale_from'])."'") .", 
    product_price_sale_to=".($value['item']['product_price_sale_to'] == '' ? 'null' : "'".$db_link->escape_string($value['item']['product_price_sale_to'])."'") .", 
    product_price_sheets_formula='".$db_link->escape_string($value['item']['product_price_sheets_formula'])."',
    product_price_quantity_formula='".$db_link->escape_string($value['item']['product_price_quantity_formula'])."',
    
    product_price_retail=".number_format($value['item']['product_price_retail'],8,'.','').",
    product_price_retail_include_vat=".$value['item']['product_price_retail_include_vat'].",
    product_price_retail_sale=".number_format($value['item']['product_price_retail_sale'],8,'.','').",
    product_price_retail_sale_from=".($value['item']['product_price_retail_sale_from'] == '' ? 'null' : "'".$db_link->escape_string($value['item']['product_price_retail_sale_from'])."'") .", 
    product_price_retail_sale_to=".($value['item']['product_price_retail_sale_to'] == '' ? 'null' : "'".$db_link->escape_string($value['item']['product_price_retail_sale_to'])."'") .", 
    product_price_retail_sheets_formula='".$db_link->escape_string($value['item']['product_price_retail_sheets_formula'])."',
    product_price_retail_quantity_formula='".$db_link->escape_string($value['item']['product_price_retail_quantity_formula'])."',
    
    product_kostos=".($value['item']['product_kostos']==='' ? 'null' : number_format($value['item']['product_kostos'],8,'.','')).",
    min_quantity_alert=".($value['item']['min_quantity_alert']==='' ? 0 : number_format($value['item']['min_quantity_alert'],8,'.','')).",
    
    product_varos='".number_format($value['item']['product_varos'],8,'.','')."',
    product_ogos_x='".number_format($value['item']['product_ogos_x'],8,'.','')."',
    product_ogos_y='".number_format($value['item']['product_ogos_y'],8,'.','')."',
    product_ogos_z='".number_format($value['item']['product_ogos_z'],8,'.','')."',

    product_fpa_base_id=".$value['item']['product_fpa_base_id'].",
    product_variable_sortorder=".intval($value['paa']).",
    
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_product=".$value['pid'];
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    //die('ddddddd');
    $data_lang=array();
    if (isset($value['item']['data_lang'])) {
      foreach ($value['item']['data_lang'] as $lang_item) {
        $data_lang[$lang_item['id']]=base64_encode($lang_item['value']);
      }
      
      
      
      foreach ($GKS_LANG_DATA_ARRAY as $lang_item) {
        $product_descr_variable=array();
        foreach ($value['pidiotites'] as $pterm) {
          if ($pterm > 0 and isset($all_terms_per_lang[$lang_item['id_lang']][$pterm])) 
            $product_descr_variable[]=$all_terms_per_lang[$lang_item['id_lang']][$pterm]['name'];
        }
      
        $data_lang['product_descr_variable_'.$lang_item['id_lang']]=base64_encode(implode('-',$product_descr_variable));
        
        //echo '<pre>';print_r($data_lang);print_r($value);die(); 
      }
      
      if (count($data_lang)>0) {
        $lang_data_save_variable=gks_lang_data_obj_save($lang_data_obj_variable,$value['pid'],$data_lang);
        if ($lang_data_save_variable['success']==false) {
          debug_mail(false,'gks_lang_data_obj_save', print_r($lang_data_save_variable,true));
          $return = array('success' => false, 'message' => base64_encode($lang_data_save_variable['message']));
          echo json_encode($return); die(); }
      }
    }
    

    // Alles times se timokatalagous
    
    //echo '<pre>';print_r($value);die();  
    
    $temp=$value['item']['product_price_plist'];
    $array_product_price_plist=array();
    foreach($temp as $plist_item) {
      $id_pricelist=floatval($plist_item['id_pricelist']); 
      $product_price_plist=floatval($plist_item['product_price_plist']); 
      $product_price_plist_include_vat=intval($plist_item['product_price_plist_include_vat']);
      $product_price_plist_sale=floatval($plist_item['product_price_plist_sale']); 
      $product_price_plist_sale_dates=intval($plist_item['product_price_plist_sale_dates']);
      if ($product_price_plist_sale_dates==0) {
        $product_price_plist_sale_from='';
        $product_price_plist_sale_to='';
      } else {
        if ($plist_item['product_price_plist_sale_from'] == '__/__/____ __:__') $plist_item['product_price_plist_sale_from']='';
        $product_price_plist_sale_from=trim_gks(stripslashes(urldecode($plist_item['product_price_plist_sale_from'])));
        if ($product_price_plist_sale_from!='') {
          $product_price_plist_sale_from = mystrtodb($product_price_plist_sale_from);
        }
        if ($plist_item['product_price_plist_sale_to'] == '__/__/____ __:__') $plist_item['product_price_plist_sale_to']='';
        $product_price_plist_sale_to=trim_gks(stripslashes(urldecode($plist_item['product_price_plist_sale_to'])));
        if ($product_price_plist_sale_to!='') {
          $product_price_plist_sale_to = mystrtodb($product_price_plist_sale_to);
        }
      }
      $product_price_plist_sheets_formula=trim_gks(base64_decode($plist_item['product_price_plist_sheets_formula']));
      $product_price_plist_quantity_formula=trim_gks(base64_decode($plist_item['product_price_plist_quantity_formula']));
      
      if ($product_price_plist>0 or 
          $product_price_plist_sale>0 or
          $product_price_plist_sheets_formula<>'' or
          $product_price_plist_quantity_formula<>'') {      
        $array_product_price_plist[]=array(
          'id_pricelist'=>$id_pricelist,
          'product_price_plist'=>$product_price_plist,
          'product_price_plist_sale'=>$product_price_plist_sale,
          'product_price_plist_sale_from'=>$product_price_plist_sale_from,
          'product_price_plist_sale_to'=>$product_price_plist_sale_to,
          'product_price_plist_sheets_formula'=>$product_price_plist_sheets_formula,
          'product_price_plist_quantity_formula'=>$product_price_plist_quantity_formula,
          'product_price_plist_include_vat'=>$product_price_plist_include_vat,
        );
      }
      
    }
    //echo '<pre>';print_r($array_product_price_plist);die(); 
        
    $sql="SELECT id_product_price, pricelist_id
    FROM gks_eshop_products_prices
    WHERE product_id=".$value['pid'];
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $exist_prices=[];
    while ($row = $result->fetch_assoc()) {
      $exist_prices[]=$row;
    }   
 
    $not_delete_id_product_price=[]; 
    foreach ($array_product_price_plist as $plist_item) {
      $id_product_price=0;
      foreach ($exist_prices as $valueep) {
        if ($valueep['pricelist_id']==$plist_item['id_pricelist']) {
          $id_product_price=$valueep['id_product_price'];break;
        }
      }
      if ($id_product_price==0) {
        $sql="insert into gks_eshop_products_prices (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        pricelist_id,product_id,
        product_price_plist,
        product_price_plist_include_vat,
        product_price_plist_sale,
        product_price_plist_sale_from,
        product_price_plist_sale_to,
        product_price_plist_sheets_formula,
        product_price_plist_quantity_formula
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        ".$plist_item['id_pricelist'].",".$value['pid'].",
        ".number_format($plist_item['product_price_plist'],8,'.','').",
        ".$plist_item['product_price_plist_include_vat'].",
        ".number_format($plist_item['product_price_plist_sale'],8,'.','').",
        ".($plist_item['product_price_plist_sale_from'] == '' ? 'null' : "'".$db_link->escape_string($plist_item['product_price_plist_sale_from'])."'") .", 
        ".($plist_item['product_price_plist_sale_to'] == '' ? 'null' : "'".$db_link->escape_string($plist_item['product_price_plist_sale_to'])."'") .", 
        '".$db_link->escape_string($plist_item['product_price_plist_sheets_formula'])."',
        '".$db_link->escape_string($plist_item['product_price_plist_quantity_formula'])."'
        )";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        $not_delete_id_product_price[] = $db_link->insert_id; 
      } else {
        $sql="update gks_eshop_products_prices set
        product_price_plist=".number_format($plist_item['product_price_plist'],8,'.','').",
        product_price_plist_include_vat=".$plist_item['product_price_plist_include_vat'].",
        product_price_plist_sale=".number_format($plist_item['product_price_plist_sale'],8,'.','').",
        product_price_plist_sale_from=".($plist_item['product_price_plist_sale_from'] == '' ? 'null' : "'".$db_link->escape_string($plist_item['product_price_plist_sale_from'])."'") .", 
        product_price_plist_sale_to=".($plist_item['product_price_plist_sale_to'] == '' ? 'null' : "'".$db_link->escape_string($plist_item['product_price_plist_sale_to'])."'") .", 
        product_price_plist_sheets_formula='".$db_link->escape_string($plist_item['product_price_plist_sheets_formula'])."',
        product_price_plist_quantity_formula='".$db_link->escape_string($plist_item['product_price_plist_quantity_formula'])."',
        mydate_edit=now(),
        user_id_edit=".$my_wp_user_id.",
        myip='".$db_link->escape_string($gkIP)."'
        where id_product_price=".$id_product_price;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        $not_delete_id_product_price[]=$id_product_price;
      }  
    }

    $sql="delete from gks_eshop_products_prices 
    where product_id=".$value['pid'];
    if (count($not_delete_id_product_price)>0) {
      $sql.=" and id_product_price not in (".implode(',',$not_delete_id_product_price).")";
    }
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    
    
//  echo 'hhhh';die();
       
    
  } 
  unset($value);
  
  //print '<pre>old_variable_products';print_r($old_variable_products);print 'variable_products_new';print_r($variable_products_new);die();
  
  
  $exist_ids=array();
  foreach ($variable_products_new as $value) $exist_ids[]=$value['pid'];
  $sql="update gks_eshop_products set 
  product_disable=1,
  product_parent_id=0,
  product_parent_old_id=".$id."
  where product_parent_id=".$id;
  if (count($exist_ids)>0) $sql.=" and id_product not in (".implode(',',$exist_ids).")";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  
  
   
  
  //print '<pre>';print_r($variable_products_new);die();
  
  
  //add or delete products
  
  $product_ids=array();
  foreach ($variable_products_new as $value1) $product_ids[]=$value1['pid'];
  if (count($product_ids)>0) { 
    $sql="select id_product_variable,product_id,product_idiotita_term_id from gks_eshop_products_variables where product_id in (".implode(',',$product_ids).")";
    //echo $sql;die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    $exist_products_variables=array();
    while ($row = $result->fetch_assoc()) {
      $exist_products_variables[]=$row;
    }
    
    $not_delete=array();
    foreach ($variable_products_new as $value1) {
      foreach ($value1['pidiotites'] as $value2) {
        $found=false;
        foreach ($exist_products_variables as $value3) {
          if ($value3['product_id'] == $value1['pid'] and $value3['product_idiotita_term_id']==$value2) {
            $not_delete[]=$value3['id_product_variable'];
            //echo '<pre>value3 ';print_r($value3);die();
            $found=true;
          }
        }
        if ($found==false) {
          
          $sql="insert into gks_eshop_products_variables (
            user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
            product_id,product_idiotita_term_id
          ) values (
            ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
            ".$value1['pid'].",".$value2."
          )";
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }
          
          $not_delete[] = $db_link->insert_id;  
        }
      }
    }
    $sql="delete from gks_eshop_products_variables where product_id in (".implode(',',$product_ids).")";
    if (count($not_delete)>0) $sql.=" and id_product_variable not in (".implode(',',$not_delete).")";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    //echo '<pre>';print_r($not_delete);die();
    
    
    
  
  }
  
  
  
  //print '<pre>';
  //echo 'variable_products_temp:'; print_r($variable_products_temp);
  //echo 'variable_products_new:'; print_r($variable_products_new);
  //echo 'exist_products_variables:'; print_r($exist_products_variables);
  //echo 'product_valids:'; print_r($product_valids);
  //die();//print_r($all_terms);
}


$gks_custom_save_run=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);
gks_lang_data_obj_save_exec_php('gks_eshop_products',$id);






//$gks_custom_prepare = gks_custom_table_item_prepare('gks_orders',['from'=>'item']);
//$gks_custom_save_prepare
$cf_fields=array();
if (isset($gks_custom_save_prepare['gks_custom_prepare']['fields']) and is_array($gks_custom_save_prepare['gks_custom_prepare']['fields'])) {
  foreach ($gks_custom_save_prepare['gks_custom_prepare']['fields'] as $value) {
    $cf_fields[]=$value['gks_field_name'];
  }
  if (count($cf_fields)>0) {
    
    $cf_table=$gks_custom_save_prepare['gks_custom_prepare']['table']['table_name'];
    $cf_field_id=$gks_custom_save_prepare['gks_custom_prepare']['table']['field_name_id_current'];
    
    $sql="select id_product from gks_eshop_products where product_parent_id = ".$id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }    
    $update_ids=array();
    while ($row = $result->fetch_assoc()) {
      $update_ids[]=$row['id_product'];
    }
    if (count($update_ids)>0) {
      $sql="UPDATE gks_customt_gks_eshop_products, (
        SELECT ".implode(',',$cf_fields)."
        FROM gks_customt_gks_eshop_products
        WHERE product_id=".$id."
      )  AS parvalue
      
      SET ";
      foreach ($cf_fields as $value) {
       $sql.="gks_customt_gks_eshop_products.".$value."=parvalue.".$value.",\n";
      }
      $sql.="
      gks_customt_gks_eshop_products.cf_mydate_edit=now(),
      gks_customt_gks_eshop_products.cf_user_id_edit=".$my_wp_user_id.",
      cf_myip='".$db_link->escape_string($gkIP)."' 
      where gks_customt_gks_eshop_products.product_id in (".implode(',',$update_ids).")";
      //echo '<pre>';echo $sql;die();
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }    
    }
  }
}

//print '<pre>';print_r($gks_custom_save_prepare);die();
//print '<pre>';print_r($gks_custom_save_prepare['gks_custom_prepare']['fields']);die();
//print '<pre>';print_r($cf_fields);die();



if ($product_monada_id_old!=-1 and $product_monada_id_old!=$product_monada_id) {
  //echo '<pre>'.$product_monada_id_old.'|'.$product_monada_id;die();  
  $id_product=array();
  $id_product[]=$id;
  
  $sql="select id_product from gks_eshop_products where product_parent_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row = $result->fetch_assoc()) {
    $id_product[]=$row['id_product'];
  }  
  
  
  $sql="update gks_orders_products set 
  product_monada_id_org=".$product_monada_id." 
  where product_monada_id_org<>".$product_monada_id." 
  and product_id in (".implode(',',$id_product).")";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $sql="update gks_acc_inv_products set 
  product_monada_id_org=".$product_monada_id." 
  where product_monada_id_org<>".$product_monada_id." 
  and product_id in (".implode(',',$id_product).")";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $sql="update gks_whi_mov_products set 
  product_monada_id_org=".$product_monada_id." 
  where product_monada_id_org<>".$product_monada_id." 
  and product_id in (".implode(',',$id_product).")";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $sql="update gks_production_sintagi_product set 
  spbom_monada_id_org=".$product_monada_id." 
  where spbom_monada_id_org<>".$product_monada_id." 
  and spbom_product_id in (".implode(',',$id_product).")";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  
  $filters=array('id_product'=>$id);
  gks_monada_recs_convert($filters);  


}

gks_whi_mov_balance_calc(array($id));
$ret=gks_products_update_barcodes(array($id));
if ($ret['success']==false) {
  $return = array('success' => false, 'message' => base64_encode($ret['message']), 'redirect'=> '');
  echo json_encode($return); die();
}

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







