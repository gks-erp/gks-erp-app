<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');


$my_page_title=gks_lang('Αναφορά - Pivot Table - Παραγγελίες - Λήψη δεδομένων').'...';
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders_pivot','view',0);
if ($perm_ret['success']==false) {
  $myjson=array();
  echo json_encode($myjson); die();
}







$sql = "SELECT gks_orders.*,
gks_cmsb_base_type.base_type_descr,
if(gks_orders.gks_price_net<>0, gks_orders.gks_price_net, gks_orders.connect_price) as sortprice,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.ma_odos, gks_users.ma_perioxi, gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_nomos_id, gks_nomoi.nomos_descr, gks_users.ma_country_id, gks_country.country_name,
gks_orders_occasion.title as occasion_title,gks_occasion_types.occasion_type_descr, gks_orders_occasion.mydate_add as occasion_mydate_add,

gks_eshop_products.product_code, gks_eshop_products.product_descr_small, 
gks_orders_products.product_sheets, gks_orders_products.product_quantity, gks_orders_products.product_price_final_all_net, 
gks_orders_products.product_price_coupon_use, gks_orders_products.product_price_coupon_use_disabled,


cmsb_after_records_lamination.title_el AS lamination_descr,
cmsb_after_records_mounting.title_el AS mounting_descr,
cmsb_albums.title_el AS album_size_descr,
cmsb_albums.size_closed AS size_closed_descr,
cmsb_albums.size_open AS size_open_descr,
cmsb_albums_sub.title_el AS sub_album_size_descr,
cmsb_albums_sub.size_closed AS sub_size_closed_descr,
cmsb_albums_sub.size_open AS sub_size_open_descr,
cmsb_book_attr.size AS book_size_descr,
cmsb_book_covers.code AS cover_code_descr,
cmsb_book_covers_sub.code AS subcover_code_descr,
cmsb_box_attr_color.color AS box_color_descr,
cmsb_box_attr_size.size AS box_size_descr,
cmsb_bundle_records_book.title_el AS book_type_descr,
cmsb_bundle_records_box.title_el AS box_type_descr,
cmsb_bundle_records_pack.title_el AS pack_type_descr,
cmsb_bundle_records_present.title_el AS present_type_descr,
cmsb_categories.name_el AS order_type_descr,
cmsb_categories_sub.name_el AS sub_type_descr,
cmsb_collections.code AS coll_design_descr,
cmsb_collections_sub.code AS subcoll_design_descr,
cmsb_papers.name AS print_paper_descr,
cmsb_papers_sub.name AS sub_print_paper_descr,
cmsb_present_attr.size AS present_size_descr,
cmsb_present_attr_color.color AS present_color_descr,
cmsb_present_attr_passcolor.color AS pass_color_descr,
cmsb_printing_types.subtitle AS printing_type_descr,
gks_cmsb_coating.coating_descr,
gks_cmsb_cover_type.cover_type_descr,
gks_cmsb_fcolor.fcolor_descr,
gks_cmsb_foiltype.foiltype_descr,
gks_cmsb_frame.frame_descr,
gks_cmsb_paper_size.paper_size_descr,
gks_cmsb_ppcolor.ppcolor_descr,
gks_cmsb_printype.printype_descr,
gks_cmsb_subtype.subtype_descr,
gks_cmsb_telaro.telaro_descr,
gks_cmsb_thickness.thickness_descr,
gks_cmsb_phototype.phototype_descr

FROM (((((((((((((((((((((((((((((((((((
((((((((((((((gks_orders 
LEFT JOIN gks_cmsb_base_type ON gks_orders.base_type_id = gks_cmsb_base_type.id_base_type)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_orders.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_orders.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
LEFT JOIN gks_orders_occasion on gks_orders.order_occasion_id = gks_orders_occasion.id_order_occasion)
LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type) 
LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer)
LEFT JOIN gks_delivery_methods ON gks_orders.tropos_apostolis = gks_delivery_methods.id_delivery_method)
LEFT JOIN gks_eshop_fiscal_position ON gks_orders.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_orders.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)

LEFT JOIN gks_orders_products ON gks_orders.id_order = gks_orders_products.order_id) 
LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product)


LEFT JOIN cmsb_after_records AS cmsb_after_records_lamination ON gks_orders.lamination = cmsb_after_records_lamination.num) 
LEFT JOIN cmsb_after_records AS cmsb_after_records_mounting ON gks_orders.mounting = cmsb_after_records_mounting.num)
LEFT JOIN cmsb_albums AS cmsb_albums_sub ON gks_orders.subalbum_size = cmsb_albums_sub.num) 
LEFT JOIN cmsb_albums ON gks_orders.album_size = cmsb_albums.num) 
LEFT JOIN cmsb_book_attr ON gks_orders.book_size = cmsb_book_attr.num) 
LEFT JOIN cmsb_book_covers AS cmsb_book_covers_sub ON gks_orders.subcover_code = cmsb_book_covers_sub.num) 
LEFT JOIN cmsb_book_covers ON gks_orders.cover_code = cmsb_book_covers.num) 
LEFT JOIN cmsb_box_attr AS cmsb_box_attr_color ON gks_orders.box_color = cmsb_box_attr_color.num) 
LEFT JOIN cmsb_box_attr AS cmsb_box_attr_size ON gks_orders.box_size = cmsb_box_attr_size.num) 
LEFT JOIN cmsb_bundle_records AS cmsb_bundle_records_book ON gks_orders.book_type = cmsb_bundle_records_book.num) 
LEFT JOIN cmsb_bundle_records AS cmsb_bundle_records_box ON gks_orders.box_type = cmsb_bundle_records_box.num) 
LEFT JOIN cmsb_bundle_records AS cmsb_bundle_records_pack ON gks_orders.pack_type = cmsb_bundle_records_pack.num) 
LEFT JOIN cmsb_bundle_records AS cmsb_bundle_records_present ON gks_orders.present_type = cmsb_bundle_records_present.num) 
LEFT JOIN cmsb_categories AS cmsb_categories_sub ON gks_orders.sub_type = cmsb_categories_sub.num) 
LEFT JOIN cmsb_categories ON gks_orders.order_type = cmsb_categories.num) 
LEFT JOIN cmsb_collections AS cmsb_collections_sub ON gks_orders.subcoll_design = cmsb_collections_sub.num) 
LEFT JOIN cmsb_collections ON gks_orders.coll_design = cmsb_collections.num) 
LEFT JOIN cmsb_papers AS cmsb_papers_sub ON gks_orders.subpaper_type = cmsb_papers_sub.num) 
LEFT JOIN cmsb_papers ON gks_orders.print_paper = cmsb_papers.num) 
LEFT JOIN cmsb_present_attr AS cmsb_present_attr_color ON gks_orders.present_color = cmsb_present_attr_color.num) 
LEFT JOIN cmsb_present_attr AS cmsb_present_attr_passcolor ON gks_orders.pass_color = cmsb_present_attr_passcolor.num) 
LEFT JOIN cmsb_present_attr ON gks_orders.present_size = cmsb_present_attr.num) 
LEFT JOIN cmsb_printing_types ON gks_orders.printing_type = cmsb_printing_types.num) 
LEFT JOIN gks_cmsb_coating ON gks_orders.coating = gks_cmsb_coating.id_coating) 
LEFT JOIN gks_cmsb_cover_type ON gks_orders.cover_type = gks_cmsb_cover_type.id_cover_type) 
LEFT JOIN gks_cmsb_fcolor ON gks_orders.fcolor = gks_cmsb_fcolor.id_fcolor) 
LEFT JOIN gks_cmsb_foiltype ON gks_orders.foiltype = gks_cmsb_foiltype.id_foiltype) 
LEFT JOIN gks_cmsb_frame ON gks_orders.frame = gks_cmsb_frame.id_frame) 
LEFT JOIN gks_cmsb_paper_size ON gks_orders.paper_size = gks_cmsb_paper_size.id_paper_size) 
LEFT JOIN gks_cmsb_ppcolor ON gks_orders.ppcolor = gks_cmsb_ppcolor.id_ppcolor) 
LEFT JOIN gks_cmsb_printype ON gks_orders.printype = gks_cmsb_printype.id_printype) 
LEFT JOIN gks_cmsb_subtype ON gks_orders.subtype = gks_cmsb_subtype.id_subtype) 
LEFT JOIN gks_cmsb_telaro ON gks_orders.telaro = gks_cmsb_telaro.id_telaro) 
LEFT JOIN gks_cmsb_thickness ON gks_orders.thickness = gks_cmsb_thickness.id_thickness)
LEFT JOIN gks_cmsb_phototype ON gks_orders.phototype = gks_cmsb_phototype.id_phototype
";  


$sql = "SELECT gks_orders.*,
gks_cmsb_base_type.base_type_descr,
if(gks_orders.gks_price_net<>0, gks_orders.gks_price_net, gks_orders.connect_price) as sortprice,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.ma_odos, gks_users.ma_perioxi, gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_nomos_id, gks_nomoi.nomos_descr, gks_users.ma_country_id, gks_country.country_name,
gks_orders_occasion.title as occasion_title,gks_occasion_types.occasion_type_descr, gks_orders_occasion.mydate_add as occasion_mydate_add,





cmsb_after_records_lamination.title_el AS lamination_descr,
cmsb_after_records_mounting.title_el AS mounting_descr,
cmsb_albums.title_el AS album_size_descr,
cmsb_albums.size_closed AS size_closed_descr,
cmsb_albums.size_open AS size_open_descr,
cmsb_albums_sub.title_el AS sub_album_size_descr,
cmsb_albums_sub.size_closed AS sub_size_closed_descr,
cmsb_albums_sub.size_open AS sub_size_open_descr,
cmsb_book_attr.size AS book_size_descr,
cmsb_book_covers.code AS cover_code_descr,
cmsb_book_covers_sub.code AS subcover_code_descr,
cmsb_box_attr_color.color AS box_color_descr,
cmsb_box_attr_size.size AS box_size_descr,
cmsb_bundle_records_book.title_el AS book_type_descr,
cmsb_bundle_records_box.title_el AS box_type_descr,
cmsb_bundle_records_pack.title_el AS pack_type_descr,
cmsb_bundle_records_present.title_el AS present_type_descr,
cmsb_categories.name_el AS order_type_descr,
cmsb_categories_sub.name_el AS sub_type_descr,
cmsb_collections.code AS coll_design_descr,
cmsb_collections_sub.code AS subcoll_design_descr,
cmsb_papers.name AS print_paper_descr,
cmsb_papers_sub.name AS sub_print_paper_descr,
cmsb_present_attr.size AS present_size_descr,
cmsb_present_attr_color.color AS present_color_descr,
cmsb_present_attr_passcolor.color AS pass_color_descr,
cmsb_printing_types.subtitle AS printing_type_descr,
gks_cmsb_coating.coating_descr,
gks_cmsb_cover_type.cover_type_descr,
gks_cmsb_fcolor.fcolor_descr,
gks_cmsb_foiltype.foiltype_descr,
gks_cmsb_frame.frame_descr,
gks_cmsb_paper_size.paper_size_descr,
gks_cmsb_ppcolor.ppcolor_descr,
gks_cmsb_printype.printype_descr,
gks_cmsb_subtype.subtype_descr,
gks_cmsb_telaro.telaro_descr,
gks_cmsb_thickness.thickness_descr,
gks_cmsb_phototype.phototype_descr

FROM (((((((((((((((((((((((((((((((((
((((((((((((((gks_orders 
LEFT JOIN gks_cmsb_base_type ON gks_orders.base_type_id = gks_cmsb_base_type.id_base_type)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_orders.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_orders.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
LEFT JOIN gks_orders_occasion on gks_orders.order_occasion_id = gks_orders_occasion.id_order_occasion)
LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type) 
LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer)
LEFT JOIN gks_delivery_methods ON gks_orders.tropos_apostolis = gks_delivery_methods.id_delivery_method)
LEFT JOIN gks_eshop_fiscal_position ON gks_orders.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_orders.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)





LEFT JOIN cmsb_after_records AS cmsb_after_records_lamination ON gks_orders.lamination = cmsb_after_records_lamination.num) 
LEFT JOIN cmsb_after_records AS cmsb_after_records_mounting ON gks_orders.mounting = cmsb_after_records_mounting.num)
LEFT JOIN cmsb_albums AS cmsb_albums_sub ON gks_orders.subalbum_size = cmsb_albums_sub.num) 
LEFT JOIN cmsb_albums ON gks_orders.album_size = cmsb_albums.num) 
LEFT JOIN cmsb_book_attr ON gks_orders.book_size = cmsb_book_attr.num) 
LEFT JOIN cmsb_book_covers AS cmsb_book_covers_sub ON gks_orders.subcover_code = cmsb_book_covers_sub.num) 
LEFT JOIN cmsb_book_covers ON gks_orders.cover_code = cmsb_book_covers.num) 
LEFT JOIN cmsb_box_attr AS cmsb_box_attr_color ON gks_orders.box_color = cmsb_box_attr_color.num) 
LEFT JOIN cmsb_box_attr AS cmsb_box_attr_size ON gks_orders.box_size = cmsb_box_attr_size.num) 
LEFT JOIN cmsb_bundle_records AS cmsb_bundle_records_book ON gks_orders.book_type = cmsb_bundle_records_book.num) 
LEFT JOIN cmsb_bundle_records AS cmsb_bundle_records_box ON gks_orders.box_type = cmsb_bundle_records_box.num) 
LEFT JOIN cmsb_bundle_records AS cmsb_bundle_records_pack ON gks_orders.pack_type = cmsb_bundle_records_pack.num) 
LEFT JOIN cmsb_bundle_records AS cmsb_bundle_records_present ON gks_orders.present_type = cmsb_bundle_records_present.num) 
LEFT JOIN cmsb_categories AS cmsb_categories_sub ON gks_orders.sub_type = cmsb_categories_sub.num) 
LEFT JOIN cmsb_categories ON gks_orders.order_type = cmsb_categories.num) 
LEFT JOIN cmsb_collections AS cmsb_collections_sub ON gks_orders.subcoll_design = cmsb_collections_sub.num) 
LEFT JOIN cmsb_collections ON gks_orders.coll_design = cmsb_collections.num) 
LEFT JOIN cmsb_papers AS cmsb_papers_sub ON gks_orders.subpaper_type = cmsb_papers_sub.num) 
LEFT JOIN cmsb_papers ON gks_orders.print_paper = cmsb_papers.num) 
LEFT JOIN cmsb_present_attr AS cmsb_present_attr_color ON gks_orders.present_color = cmsb_present_attr_color.num) 
LEFT JOIN cmsb_present_attr AS cmsb_present_attr_passcolor ON gks_orders.pass_color = cmsb_present_attr_passcolor.num) 
LEFT JOIN cmsb_present_attr ON gks_orders.present_size = cmsb_present_attr.num) 
LEFT JOIN cmsb_printing_types ON gks_orders.printing_type = cmsb_printing_types.num) 
LEFT JOIN gks_cmsb_coating ON gks_orders.coating = gks_cmsb_coating.id_coating) 
LEFT JOIN gks_cmsb_cover_type ON gks_orders.cover_type = gks_cmsb_cover_type.id_cover_type) 
LEFT JOIN gks_cmsb_fcolor ON gks_orders.fcolor = gks_cmsb_fcolor.id_fcolor) 
LEFT JOIN gks_cmsb_foiltype ON gks_orders.foiltype = gks_cmsb_foiltype.id_foiltype) 
LEFT JOIN gks_cmsb_frame ON gks_orders.frame = gks_cmsb_frame.id_frame) 
LEFT JOIN gks_cmsb_paper_size ON gks_orders.paper_size = gks_cmsb_paper_size.id_paper_size) 
LEFT JOIN gks_cmsb_ppcolor ON gks_orders.ppcolor = gks_cmsb_ppcolor.id_ppcolor) 
LEFT JOIN gks_cmsb_printype ON gks_orders.printype = gks_cmsb_printype.id_printype) 
LEFT JOIN gks_cmsb_subtype ON gks_orders.subtype = gks_cmsb_subtype.id_subtype) 
LEFT JOIN gks_cmsb_telaro ON gks_orders.telaro = gks_cmsb_telaro.id_telaro) 
LEFT JOIN gks_cmsb_thickness ON gks_orders.thickness = gks_cmsb_thickness.id_thickness)
LEFT JOIN gks_cmsb_phototype ON gks_orders.phototype = gks_cmsb_phototype.id_phototype
";  
$sql.= " where 1=1 ";

if (isset($_gks_session['temp']['wherepivot1']) and $_gks_session['temp']['wherepivot1']!='') {
  $sql.=$_gks_session['temp']['wherepivot1'];
}



$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('error sql');
}

$dir_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/';
if (file_exists($dir_path) == false) mkdir($dir_path); 
$file_path='pivot1_'.$my_wp_user_id.'_'.rand(100000,999999).'.csv';
$mypath=$dir_path.$file_path;
if (file_exists($mypath)) unlink($mypath);

$myfile = fopen($mypath, "a");

fwrite($myfile, '"Έτος","Μήνας","Ημερομηνία","Ημέρα","Ώρα","Κατάσταση","Χρήστης","Πελάτης","Παραγγελίες","Τρόπος Αποστολής",'.
                '"Τρόπος Πληρωμής","Περίσταση","Αξία","Ποσότητα","Συν. Ποσότητα","Κατηγορία","Τύπος Άλμπουμ","Τύπος Βιβλίου","Τεχνολογία Εκτύπωσης","Προϊόν Παρουσίασης",'.
                '"Προϊόν Συσκευασίας","Είδος Παρουσίασης","Cover","Διάσταση Book","Διάσταση Σαλονιού","Διάσταση Φωτογραφίας","Διάσταση Συσκευασίας","Απόχρωση Ακρυλικού","Υλικό Επένδυσης","Εκτύπωση/Χαρτί",'.
                '"Διάσταση Εκτύπωσης","Σαλόνια/Αρ.Σ/Αρ.Φ","Χρώμα Υλικού/Ξύλου","Χρώμα Πασπαρτού","Είδος Εκτύπωσης","Είδος Foil","Φόδρα","Εξώφυλλο","Κουτί","Πάχος",'.
                '"Πλαστικοποίηση","Επικόλληση","Τελάρο","Κορνίζα","Χρώμα","Πασπαρτού","Συν. Τύπος Άλμπουμ","Συν. Διάσταση Σαλονιού","Συν. Εκτύπωση/Χαρτί","Συν. Σαλόνια",'.
                '"Συν. Φόδρα","Συν. Εξώφυλλο"'.
                "\n");


while ($row = $result->fetch_assoc()) {

  fwrite($myfile, 

    showDate(strtotime($row['order_date']), 'Y', 1).','.
    showDate(strtotime($row['order_date']), 'm', 1).','.
    showDate(strtotime($row['order_date']), 'd', 1).','.
    getWeekDayName(date('w', strtotime($row['order_date']) - GKS_ERP_START_VARDIA*60*60)).','.
    showDate(strtotime($row['order_date']), 'H', 1).':00'.','.
    '"'.gks_csv_txt(getOrderStateDescr($row['order_state'])).'",'.
    '"'.gks_csv_txt($row['gks_nickname_add']).'",'.
    '"'.gks_csv_txt($row['gks_nickname']).'",'.
    '1,'.
    '"'.gks_csv_txt($row['delivery_method_name']).'",'.
    
    
    '"'.gks_csv_txt($row['payment_acquirer_name']).'",'.
    '"'.gks_csv_txt($row['occasion_type_descr']).'",'.
    '"'.gks_csv_txt($row['sortprice']).'",'.
    $row['qty'].','.
    $row['subqty'].','.
    '"'.gks_csv_txt($row['base_type_descr']).'",'.
    '"'.gks_csv_txt($row['order_type_descr']).'",'.
    '"'.gks_csv_txt($row['book_type_descr']).'",'.
    '"'.gks_csv_txt($row['printing_type_descr']).'",'.
    '"'.gks_csv_txt($row['present_type_descr']).'",'.

    '"'.gks_csv_txt($row['box_type_descr']).'",'.
    '"'.gks_csv_txt($row['subtype_descr']).'",'.
    '"'.gks_csv_txt($row['cover_type_descr']).'",'.
    '"'.gks_csv_txt($row['book_size_descr']).'",'.
    '"'.gks_csv_txt((empty($row['album_size_descr'])==false ? $row['album_size_descr'] : $row['size_open_descr'])).'",'.
    '"'.gks_csv_txt($row['present_size_descr']).'",'.
    '"'.gks_csv_txt($row['box_size_descr']).'",'.
    '"'.gks_csv_txt($row['box_color_descr']).'",'.
    '"'.gks_csv_txt($row['coating_descr']).'",'.
    '"'.gks_csv_txt($row['print_paper_descr']).'",'.
    
    
    
    '"'.gks_csv_txt($row['paper_size']).'",'.
    '"'.gks_csv_txt($row['open_pages']).'",'.
    '"'.gks_csv_txt($row['present_color_descr']).'",'.
    '"'.gks_csv_txt($row['pass_color_descr']).'",'.
    '"'.gks_csv_txt($row['printype_descr']).'",'.
    '"'.gks_csv_txt($row['foiltype_descr']).'",'.
    '"'.gks_csv_txt($row['cover_code_descr']).'",'.
    '"'.gks_csv_txt($row['coll_design_descr']).'",'.
    '"'.gks_csv_txt($row['pack_type_descr']).'",'.
    '"'.gks_csv_txt($row['thickness_descr']).'",'.
    
    
    '"'.gks_csv_txt($row['lamination_descr']).'",'.
    '"'.gks_csv_txt($row['mounting_descr']).'",'.
    '"'.gks_csv_txt($row['telaro_descr']).'",'.
    '"'.gks_csv_txt($row['frame_descr']).'",'.
    '"'.gks_csv_txt($row['fcolor_descr']).'",'.
    '"'.gks_csv_txt($row['ppcolor_descr']).'",'.
    '"'.gks_csv_txt($row['sub_type_descr']).'",'.
    '"'.gks_csv_txt((empty($row['sub_album_size_descr'])==false ? $row['sub_album_size_descr'] : $row['sub_size_open_descr'])).'",'.
    '"'.gks_csv_txt($row['sub_print_paper_descr']).'",'.
    '"'.gks_csv_txt($row['subopen_pages']).'",'.
    
    
    
    '"'.gks_csv_txt($row['subcover_code_descr']).'",'.
    '"'.gks_csv_txt($row['subcoll_design_descr']).'"'.

  "\n");

    

}  

fclose($myfile);


$return = array('success' => true, 'message' => base64_encode('OK'),'url' => '/my/temp/'.$file_path);
echo json_encode($return); die();

$offset = 0; // 60 * 60 * 24; //24 ores
//$info = getimagesize($mypath);
//header('Content-Type: '.$info['mime']);
header('Content-Disposition: attachment; filename="'.basename($mypath).'"');
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
header("Cache-Control: max-age=$offset, must-revalidate"); 
header("Pragma: private");

readfile($mypath);
die();