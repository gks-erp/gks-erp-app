<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Λήψη δεδομένων παρτίδας-serial number-υπόλοιπο');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_product_lots','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


//echo '<pre>dddddddddddd';die();



$aa=0; if (isset($_POST['aa'])) $aa=intval($_POST['aa']);
$ls=0; if (isset($_POST['ls'])) $ls=intval($_POST['ls']);
$lot_product_id=0; if (isset($_POST['lot_product_id'])) $lot_product_id=intval($_POST['lot_product_id']);
$lot_serial_text=''; if (isset($_POST['lot_serial_text'])) $lot_serial_text=trim_gks(base64_decode($_POST['lot_serial_text']));

if ($lot_product_id<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το είδος')));
  echo json_encode($return); die();}


  
  
  
$sql="select * from gks_eshop_products where id_product=".$lot_product_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows==0) {
  debug_mail(false,'product not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το είδος')));
  echo json_encode($return); die();}
$row_product = $result->fetch_assoc();
$product_lot_serial=trim_gks($row_product['product_lot_serial']);

$id_lot_product=0;
if ($lot_serial_text!='') {
  $sql="select id_lot_product from gks_eshop_product_lots where lotproduct_id=".$lot_product_id." and lot_name like '".$db_link->escape_string($lot_serial_text)."'";
  //$sql="select id_lot_product from gks_eshop_product_lots where lotproduct_id=".$lot_product_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows==0) {
    //debug_mail(false,'lot_product_id',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η παρτίδα-serial number')));
    echo json_encode($return); die();}
  $row_lot = $result->fetch_assoc();
  $id_lot_product=intval($row_lot['id_lot_product']);
}

$sql="select id_lot_product as id, lot_name as name,lot_date_expire as expire 
from gks_eshop_product_lots where lotproduct_id=".$lot_product_id." 
order by lot_sortorder, lot_name";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$lots=array();
while ($row = $result->fetch_assoc()) {
  $lots[$row['id']]=$row;
}  
if (count($lots)<=0) {
  $return = array('success' => true, 'message' => base64_encode('ok'), 'html' => base64_encode(gks_lang('Δεν βρέθηκαν παρτίδες serial-numbers')));
  echo json_encode($return); die();}
$lots_ids=array_keys($lots);


$sql="SELECT id_warehouse as id, warehouse_name as name FROM gks_warehouses where is_virtual=0 ORDER BY warehouse_sortorder";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}
$warehouses=array();
while ($row = $result->fetch_assoc()) {
  $warehouses[$row['id']]=$row;
}  
if (count($warehouses)<=0) {
  $return = array('success' => true, 'message' => base64_encode('ok'), 'html' => base64_encode(gks_lang('Δεν βρέθηκαν αποθήκες')));
  echo json_encode($return); die();}

$warehouses_ids=array_keys($warehouses);


$sql="select warehouse_id,lot_product_id,balance
from gks_warehouse_balance_lots_serials
where lot_product_id in (".implode(',',$lots_ids).")
and balance<>0
and warehouse_id>2";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
  $return = array('success' => true, 'message' => base64_encode('ok'), 'html' => base64_encode('sql error'));
  echo json_encode($return); die();}

$wbdata=[];
while ($row = $result->fetch_assoc()) {
  if (isset($wbdata[$row['lot_product_id']])==false) {
    $wbdata[$row['lot_product_id']]=array(
      'warehouses' => array(),
    );    
  }
  $wbdata[$row['lot_product_id']]['warehouses'][$row['warehouse_id']]=$row['balance'];
}
//$return = array('success' => true, 'message' => base64_encode('ok'), 'html' => base64_encode('<pre>'.print_r($warehouses,true).'</pre>'));
//echo json_encode($return); die();

$warehouses_clean=array();
foreach ($warehouses as $witem) {
  if (isset($warehouses_clean[$witem['id']])==false) {
    foreach ($wbdata as $wbproid) {
      if (isset($wbproid['warehouses'][$witem['id']])) {
        $warehouses_clean[$witem['id']]=$witem;
        break;
      }
    } 
  }
}
//$return = array('success' => true, 'message' => base64_encode('ok'), 'html' => base64_encode('<pre>'.print_r($warehouses_clean,true).'</pre>'));
//echo json_encode($return); die();

  
$html='';
//echo '<pre>';print_r($warehouses_clean);die();
if ($product_lot_serial=='lot')
  $html.='<div style="text-align: center;">'.gks_lang('Υπόλοιπα παρτίδων ανά αποθήκη').'</div>';
else if ($product_lot_serial=='serial')
  $html.='<div style="text-align: center;">'.gks_lang('Υπόλοιπα serial number ανά αποθήκη').'</div>';

$html.='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable"><thead><tr><td></td>';
foreach ($warehouses_clean as $witem) {
  $html.='<th class="table-dark" scope="col" style="text-align: center !important;">'.$witem['name'].'</th>';
}
$html.='</tr></thead><tbody>';

foreach ($lots as $lot) {
  if (isset($wbdata[$lot['id']])) {
    $html.='<tr class="'.($id_lot_product==$lot['id'] ? 'gks_row_lot_selected' : '').'">'.
      '<td scope="row" class="mytdcml" nowrap>'.$lot['name'].'</td>';
    foreach ($warehouses_clean as $witem) {
      $html.='<td class="mytdcm">';
      if (isset($wbdata[$lot['id']]['warehouses'][$witem['id']]) and $wbdata[$lot['id']]['warehouses'][$witem['id']]!=0) {
        $html.=$wbdata[$lot['id']]['warehouses'][$witem['id']];
      }
      $html.='</td>';
    }      
    $html.='</tr>';
  }
}
$html.='</tbody></table>';

$return = array('success' => true, 'message' => base64_encode('ok'), 'html' => base64_encode($html));
echo json_encode($return); die();



//$return = array('success' => true, 'message' => base64_encode('ok'), 'html' => base64_encode('<pre>'.print_r($wbdata,true).'</pre>'));
//echo json_encode($return); die();
