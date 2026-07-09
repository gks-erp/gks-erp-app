<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
if (mb_strlen($term) < 3 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση κατηγορίας ειδών');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products_categories','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$sql="SELECT gks_eshop_products_categories.*, ccproducts.ccc,
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
FROM (((((((((gks_eshop_products_categories
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
WHERE gks_eshop_products_categories.category_disable=0    
and (
gks_eshop_products_categories.product_category_descr like '%".$db_link->escape_string($term)."%' or 
ug2.product_category_descr like '%".$db_link->escape_string($term)."%' or 
ug3.product_category_descr like '%".$db_link->escape_string($term)."%' or 
ug4.product_category_descr like '%".$db_link->escape_string($term)."%' or 
ug5.product_category_descr like '%".$db_link->escape_string($term)."%' or
ug6.product_category_descr like '%".$db_link->escape_string($term)."%' or
ug7.product_category_descr like '%".$db_link->escape_string($term)."%' or
ug8.product_category_descr like '%".$db_link->escape_string($term)."%' or
ug9.product_category_descr like '%".$db_link->escape_string($term)."%' or
ug10.product_category_descr like '%".$db_link->escape_string($term)."%'

)
ORDER BY fullpath
limit 1000";



//echo $sql;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('error sql'));
  echo json_encode($return); die();}

$fount_count=0;
$out=array();
while ($row = $result->fetch_assoc()) {
  $fount_count++;
  $out[] = array('id' => $row['id_product_category'], 'value' => $row['fullpath']);
}

$return = array('success' => true, 'message' => base64_encode('OK'),'list'=>$out);
echo json_encode($return); die();


echo json_encode($out);



