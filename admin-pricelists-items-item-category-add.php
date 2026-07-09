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
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το στοιχείο τιμοκαταλόγου')));
  echo json_encode($return); die();}

$category_id=0;
if (isset($_POST['category_id'])) $category_id=intval($_POST['category_id']);
if ($category_id<=0) {
  debug_mail(false,'the category_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η κατηγορία')));
  echo json_encode($return); die();}

$is_include=0;
if (isset($_POST['is_include'])) $is_include=intval($_POST['is_include']);
if (in_array($is_include,[1,-1])==false) $is_include=0;

$my_page_title=gks_lang('Προσθήκη κατηγορίας σε στοιχείο τιμοκαταλόγου');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_pricelist_items','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$sql="SELECT id_pricelist_item FROM gks_eshop_pricelist_items where id_pricelist_item = ".$id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty',gks_lang('Δεν βρέθηκε το στοιχείο τιμοκαταλόγου'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το στοιχείο τιμοκαταλόγου')));
  echo json_encode($return); die();}  


$sql="SELECT id_product_category FROM gks_eshop_products_categories where id_product_category = ".$category_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows == 0) {
  debug_mail(false,'empty',gks_lang('Δεν βρέθηκε η κατηγορία'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η κατηγορία')));
  echo json_encode($return); die();}  




$sql="SELECT * FROM gks_eshop_pricelist_items_categories where pricelist_item_id = ".$id." and product_category_id=".$category_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result->num_rows != 0) {
  debug_mail(false,'empty',gks_lang('Το στοιχείο τιμοκαταλόγου - κατηγορία υπάρχει ήδη'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το προϊόν - κατηγορία υπάρχει ήδη')));
  echo json_encode($return); die();}  


$sql="insert into gks_eshop_pricelist_items_categories (product_category_id,pricelist_item_id,is_include,
user_id_add,user_id_edit,mydate_add,mydate_edit,myip
) values (
".$category_id.",
".$id.",
".$is_include.",
".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
$id_pricelist_item_category=$db_link->insert_id; 




$row_html='';
if (1==2 and isset($_POST['from']) and $_POST['from']=='cat') {
$sql_list = "SELECT gks_eshop_pricelist_items_categories.*, gks_eshop_products.product_descr,product_photo,product_code
FROM gks_eshop_pricelist_items_categories 
LEFT JOIN gks_eshop_products ON gks_eshop_pricelist_items_categories.pricelist_item_id = gks_eshop_products.id_product
WHERE id_pricelist_item_category=".$id_pricelist_item_category;
$result_list = $db_link->query($sql_list); 
if (!$result_list) {
  debug_mail(false,'error sql',$sql_list);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$row_list = $result_list->fetch_assoc();

$row_html=
'<tr class="product_tr_new" data-id="'.$row_list['id_pricelist_item_category'].'">'.
  '<th scope="row" nowrap align="right" class="product_aa">*</td>'.
  '<td nowrap align="center">'.
    '<img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_product_delete_after|'.$row_list['id_pricelist_item_category'].'" data-id="'.$row_list['id_pricelist_item_category'].'" data-model="gks_eshop_pricelist_items_categories">'.
  '</td>'.
  '<td>'.getProductPhoto($row_list['pricelist_item_id'],$row_list['product_photo'],32).'</td>'.
  '<td nowrap>'.$row_list['product_code'].'</td>'.
  '<td ><a href="admin-products-item.php?id='.$row_list['pricelist_item_id'].'">'.$row_list['product_descr'].'</a></td>'.  
  '<td nowrap>'.showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1).'</td>'.  
'</tr>';
}

if (isset($_POST['from']) and $_POST['from']=='pricelistitem') {
$sql_list = "SELECT
gks_eshop_pricelist_items_categories.*,
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
FROM ((((((((((gks_eshop_pricelist_items_categories
LEFT JOIN gks_eshop_products_categories ON gks_eshop_pricelist_items_categories.product_category_id = gks_eshop_products_categories.id_product_category)
LEFT JOIN (
SELECT product_category_id, Count(pricelist_item_id) AS ccc
FROM gks_eshop_pricelist_items_categories
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
WHERE id_pricelist_item_category=".$id_pricelist_item_category;


$result_list = $db_link->query($sql_list); 
if (!$result_list) {
  debug_mail(false,'error sql',$sql_list);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$row_list = $result_list->fetch_assoc();

$row_html=
'<tr class="categories_tr_new" data-id="'.$row_list['id_pricelist_item_category'].'">'.
  '<th scope="row" nowrap class="mytdcm categories_aa">*</td>'.
  '<td class="mytdcm" nowrap align="center">'.
    '<img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_categories_delete_after|'.$row_list['id_pricelist_item_category'].'" data-id="'.$row_list['id_pricelist_item_category'].'" data-model="gks_eshop_pricelist_items_categories">'.
  '</td>'.
  '<td class="mytdcm" nowrap><a href="admin-product-categories-item.php?id='.$row_list['product_category_id'].'"><i class="enterrow fas fa-pen" title="'.gks_lang('Προβολή').'"></i></a></td>'.
  '<td class="mytdcm" nowrap>'.getCategoryPhoto($row_list['product_category_id'],$row_list['category_photo'],32).'</td>'.
  '<td class="mytdcml" nowrap>'.$row_list['fullpath'].'</td>'.
  '<td class="mytdcml is_include_val_'.$is_include.'" >'.
    ($is_include==1 ? gks_lang('Απαιτείται') : '').
    ($is_include==-1 ? gks_lang('Εξαίρεση') : '').
  '</td>'.  
  '<td class="mytdcm" nowrap>'.showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1).'</td>'.
'</tr>';


}

$return = array('success' => true, 'message' => base64_encode('OK'),'row_html'=>base64_encode($row_html));
echo json_encode($return); die();