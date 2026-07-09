<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');



$id=0;
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η κατηγορία')));
  echo json_encode($return); die();}

$product_id=0;
if (isset($_POST['product_id'])) $product_id=intval($_POST['product_id']);
if ($product_id<=0) {
  debug_mail(false,'the product_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το είδος')));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Προσθήκη προϊόντος σε κατηγορία');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="SELECT id_product_category FROM gks_eshop_products_categories where id_product_category = ".$id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'category not found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η κατηγορία')));
  echo json_encode($return); die();}  


$sql="SELECT id_product FROM gks_eshop_products where id_product = ".$product_id." and product_disable=0";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'product notfound',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το προϊόν ή είναι απενεργοποιημένο')));
  echo json_encode($return); die();}  


$sql="SELECT * FROM gks_eshop_products_categories_products where product_id = ".$product_id." and product_category_id=".$id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'product-brand exists',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το προϊόν - κατηγορία υπάρχει ήδη')));
  echo json_encode($return); die();}  


$sql="insert into gks_eshop_products_categories_products (product_category_id,product_id,
user_id_add,user_id_edit,mydate_add,mydate_edit,myip
) values (
".$id.",
".$product_id.",
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
$id_eshop_products_categories_products=$db_link->insert_id; 

$sql="insert into gks_log_eshop_products_categories_product (action_date,action_user_id,action_type,action_myip,category_id,product_id) values(
NOW(),
".$my_wp_user_id.",
'add',
'".$db_link->escape_string($gkIP)."',
".$id.",
".$product_id.")";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}


$row_html='';
if (isset($_POST['from']) and $_POST['from']=='cat') {
$sql_list = "SELECT gks_eshop_products_categories_products.*, gks_eshop_products.product_descr,product_photo,product_code
FROM gks_eshop_products_categories_products 
LEFT JOIN gks_eshop_products ON gks_eshop_products_categories_products.product_id = gks_eshop_products.id_product
WHERE id_eshop_products_categories_products=".$id_eshop_products_categories_products;
$result_list = $db_link->query($sql_list); 
if (!$result_list) {
  debug_mail(false,'error sql',$sql_list);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$row_list = $result_list->fetch_assoc();

$row_html=
'<tr class="product_tr_new" data-id="'.$row_list['id_eshop_products_categories_products'].'">'.
  '<th scope="row" nowrap align="right" class="product_aa">*</td>'.
  '<td nowrap align="center">'.
    '<img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_product_delete_after|'.$row_list['id_eshop_products_categories_products'].'" data-id="'.$row_list['id_eshop_products_categories_products'].'" data-model="gks_eshop_products_categories_products">'.
  '</td>'.
  '<td>'.getProductPhoto($row_list['product_id'],$row_list['product_photo'],32).'</td>'.
  '<td nowrap>'.$row_list['product_code'].'</td>'.
  '<td ><a href="admin-products-item.php?id='.$row_list['product_id'].'">'.$row_list['product_descr'].'</a></td>'.  
  '<td nowrap>'.showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1).'</td>'.  
'</tr>';
}

if (isset($_POST['from']) and $_POST['from']=='product') {
$sql_list = "SELECT
gks_eshop_products_categories_products.*,
gks_eshop_products_categories.category_photo,
ccproducts.ccc,
ug2.product_category_descr AS gt2, 
ug3.product_category_descr AS gt3, 
ug4.product_category_descr AS gt4, 
ug5.product_category_descr AS gt5,
ug6.product_category_descr AS gt6,
ug7.product_category_descr AS gt7,
ug8.product_category_descr AS gt8,
ug9.product_category_descr AS gt9,
ug10.product_category_descr AS gt10,

ug2.id_product_category AS id2, 
ug3.id_product_category AS id3, 
ug4.id_product_category AS id4, 
ug5.id_product_category AS id5,
ug6.id_product_category AS id6,
ug7.id_product_category AS id7,
ug8.id_product_category AS id8,
ug9.id_product_category AS id9,
ug10.id_product_category AS id10,
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
        gks_eshop_products_categories.product_category_descr) as fullpath,
CONCAT_WS('\\\\',
        ug10.product_category_descr,
        ug9.product_category_descr,
        ug8.product_category_descr,
        ug7.product_category_descr,
        ug6.product_category_descr,
        ug5.product_category_descr,
        ug4.product_category_descr,
        ug3.product_category_descr,
        ug2.product_category_descr) as dirpath
FROM ((((((((((gks_eshop_products_categories_products
LEFT JOIN gks_eshop_products_categories ON gks_eshop_products_categories_products.product_category_id = gks_eshop_products_categories.id_product_category)
LEFT JOIN (
SELECT product_category_id, Count(product_id) AS ccc
FROM gks_eshop_products_categories_products
GROUP BY product_category_id
) AS ccproducts ON gks_eshop_products_categories.id_product_category = ccproducts.product_category_id)
LEFT JOIN gks_eshop_products_categories AS ug2  ON gks_eshop_products_categories.product_category_parent_id = ug2.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.product_category_parent_id = ug3.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.product_category_parent_id = ug4.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.product_category_parent_id = ug5.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.product_category_parent_id = ug6.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.product_category_parent_id = ug7.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.product_category_parent_id = ug8.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.product_category_parent_id = ug9.id_product_category)
LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
WHERE id_eshop_products_categories_products=".$id_eshop_products_categories_products;


$result_list = $db_link->query($sql_list); 
if (!$result_list) {
  debug_mail(false,'error sql',$sql_list);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$row_list = $result_list->fetch_assoc();

$row_html=
'<tr class="categories_tr_new" data-id="'.$row_list['id_eshop_products_categories_products'].'">'.
  '<th scope="row" nowrap align="right" class="categories_aa">*</td>'.
  '<td nowrap align="center">'.
    '<img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_categories_delete_after|'.$row_list['id_eshop_products_categories_products'].'" data-id="'.$row_list['id_eshop_products_categories_products'].'" data-model="gks_eshop_products_categories_products">'.
  '</td>'.
  '<td nowrap align="center"><a href="admin-product-categories-item.php?id='.$row_list['product_category_id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a></td>'.
  '<td nowrap>'.getCategoryPhoto($row_list['product_category_id'],$row_list['category_photo'],32).'</td>'.
  '<td nowrap>'.$row_list['fullpath'].'</td>'.
  '<td nowrap>'.showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1).'</td>'.
'</tr>';


}

$return = array('success' => true, 'message' => base64_encode('OK'),'row_html'=>base64_encode($row_html));
echo json_encode($return); die();