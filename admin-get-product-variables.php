<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Λήψη παραλαγών είδους');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);



$sql="SELECT *  
FROM gks_eshop_products 
where product_class='variable' and product_disable=0 and id_product=".$id." limit 1";
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

$sql="SELECT id_eshop_products_idiotites,product_idiotita_id, idiotita_name
FROM gks_eshop_products_idiotites 
LEFT JOIN gks_product_idiotites ON gks_eshop_products_idiotites.product_idiotita_id = gks_product_idiotites.id_product_idiotita
WHERE gks_eshop_products_idiotites.product_id=".$id."
and idiotita_is_variable=1
and id_product_idiotita is not null
order by idiotita_sortorder";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

$idiotites=[];
$ids=[];
while ($row=$result->fetch_assoc()) {
  $row['id_eshop_products_idiotites']=intval($row['id_eshop_products_idiotites']);
  $row['product_idiotita_id']=intval($row['product_idiotita_id']);
  $ids[]=$row['id_eshop_products_idiotites'];
  $idiotites[$row['id_eshop_products_idiotites']]=array(
    'id_eshop_products_idiotites' => $row['id_eshop_products_idiotites'],
    'idiotita_id' => $row['product_idiotita_id'],
    'name'=>$row['idiotita_name'],
    'terms' => [],
  );
}
if (count($ids)>0) {
  $sql="SELECT eshop_products_idiotites_id,gks_product_idiotites_terms.id_product_idiotita_term, 
  gks_product_idiotites_terms.idiotita_term_name
  FROM gks_eshop_products_idiotites_terms LEFT JOIN gks_product_idiotites_terms ON gks_eshop_products_idiotites_terms.product_idiotita_term_id = gks_product_idiotites_terms.id_product_idiotita_term
  WHERE gks_eshop_products_idiotites_terms.eshop_products_idiotites_id in (".implode(',',$ids).")
  ORDER BY gks_product_idiotites_terms.idiotita_term_sortorder;";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row=$result->fetch_assoc()) {
    $row['id_product_idiotita_term']=intval($row['id_product_idiotita_term']);
    
    $idiotites[$row['eshop_products_idiotites_id']]['terms'][$row['id_product_idiotita_term']]=array(
      'id_product_idiotita_term' => $row['id_product_idiotita_term'],
      'idiotita_term_name' => $row['idiotita_term_name'],
    );
  }
}

$sql="SELECT id_product,product_descr_variable
FROM gks_eshop_products
WHERE product_parent_id=".$id."
AND product_class='variable_item'
AND gks_eshop_products.product_disable=0";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

$products=[];$ids=[];
while ($row=$result->fetch_assoc()) {
  $row['id_product']=intval($row['id_product']);
  
  $ids[]=$row['id_product'];
  $products[$row['id_product']]=array(
    'id_product'=>$row['id_product'],
    'product_descr_variable'=>$row['product_descr_variable'],
    'terms'=>[],
  );
}

if (count($ids)>0) {
  $sql="SELECT product_id,product_idiotita_term_id
  FROM gks_eshop_products_variables
  WHERE product_id in (".implode(',',$ids).")
  order by product_id,product_idiotita_term_id";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row=$result->fetch_assoc()) {
    $row['product_idiotita_term_id']=intval($row['product_idiotita_term_id']);
    $products[$row['product_id']]['terms'][]=$row['product_idiotita_term_id'];
  }
}

$data=[];
$data['idiotites']=$idiotites;
$data['products']=$products;

//echo '<pre>';print_r($data);die();


$return = array('success' => true, 'message' => base64_encode('OK'),'data' => array(
  'idiotites'=>$idiotites,
  'products'=>$products,
));
echo json_encode($return); die();
