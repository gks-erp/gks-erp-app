<?php
//https://github.com/PHPOffice/PHPWord/
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Εξαγωγή επαφής σε Word');

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id <= 0) {header('Location: /my'); die(); }

$my_page_title=gks_lang('Εξαγωγή επαφής σε Word για λογιστή');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}






$sql = "SELECT ".GKS_WP_TABLE_PREFIX."users.*, gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, 
gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_country_id, gks_users.ma_nomos_id, 
gks_users.phone_home, gks_users.genisi_date, gks_users.ethnikotita, 
gks_users.alli_apasxolisi,gks_users.cv_proipiresia, gks_users.cv_spoydes, gks_users.cv_seminaria, gks_users.cv_mitriki_glossa, gks_users.cv_jenes_glosses,
gks_users.cv_sxesi_me_photografia, 
gks_users.cv_metaforiko_meso, gks_users.cv_has_bike, gks_users.cv_has_motorcycle, gks_users.cv_has_car,
gks_users.profilepososto_user, gks_users.profilepososto_job,
gks_country.country_name, gks_nomoi.nomos_descr, 
table_last_name.mylast_name, table_first_name.myfirst_name, table_mobile.mymoobile, table_roles.mywp_capabilities,
gks_users.user_HumanInitial,
gks_users.amka ,gks_users.ama_eam ,gks_users.arithmos_tautoitas ,gks_users.arxi_ekdosis, gks_users.onoma_patera, gks_users.onoma_miteras,
gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr,gks_users.oikogeniaki_katastasti_id, gks_users.oikogeniaki_katastasti_paidia
FROM (((((((((".GKS_WP_TABLE_PREFIX."users 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist) 
LEFT JOIN gks_users_oikogeniaki_katastasti ON gks_users_oikogeniaki_katastasti.id_oikogeniaki_katastasti = gks_users.oikogeniaki_katastasti_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
)  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
)  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mymoobile
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='mobile'))
)  AS table_mobile ON ".GKS_WP_TABLE_PREFIX."users.ID = table_mobile.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mywp_capabilities
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='".GKS_WP_TABLE_PREFIX."capabilities'))
)  AS table_roles ON ".GKS_WP_TABLE_PREFIX."users.ID = table_roles.user_id
where ".GKS_WP_TABLE_PREFIX."users.id = ".$id;

$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$query);
if (!$result) die('sql error');

if ($result->num_rows!=1) {
  debug_mail(false,'record not found sql',$sql); 
  die('no record found');
}
$row = $result->fetch_assoc();

 
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

//require_once 'PHPWord/vendor/autoload.php';
//$phpWord = new \PhpOffice\PhpWord\PhpWord();

$phpWord = new PhpOffice\PhpWord\PhpWord();


/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...
$section->addText(gks_lang('Προσωπικά στοιχεία'), array('name' => 'Tahoma', 'size' => 20, 'bold' => true),array('alignment'  => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));

$section->addText(gks_lang('Επίθετο').': '.htmlspecialchars_gks($row['mylast_name']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('Όνομα').': '.htmlspecialchars_gks($row['myfirst_name']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('Πατρώνυμο').': '.htmlspecialchars_gks($row['onoma_patera']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('Μητρώνυμο').': '.htmlspecialchars_gks($row['onoma_miteras']), array('name' => 'Tahoma', 'size' => 12));
if ($row['gks_sex']==1) {
  $section->addText(gks_lang('Φύλο').': '.gks_lang('Άρρεν'), array('name' => 'Tahoma', 'size' => 12));
} else if ($row['gks_sex']==2)  {
  $section->addText(gks_lang('Φύλο').': '.gks_lang('Θύλη'), array('name' => 'Tahoma', 'size' => 12));
} else {
  $section->addText(gks_lang('Φύλο').': ', array('name' => 'Tahoma', 'size' => 12));
}
$section->addText(gks_lang('Ημερομηνία Γέννησης').': '.((isset($row['genisi_date']) and $row['genisi_date'] !='') ? date('d/m/Y', strtotime($row['genisi_date'])) : '') , array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('Αριθμός Ταυτότητας').': '.htmlspecialchars_gks($row['arithmos_tautoitas']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('Αρχή Έκδοσης').': '.htmlspecialchars_gks($row['arxi_ekdosis']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('ΑΦΜ').': '.htmlspecialchars_gks($row['afm']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('ΔΟΥ').': '.htmlspecialchars_gks($row['doy']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('Οικογενειακή Κατάσταση').': '.htmlspecialchars_gks($row['oikogeniaki_katastasti_descr']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('Παιδιά').': '.htmlspecialchars_gks($row['oikogeniaki_katastasti_paidia']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('ΑΜΑ - ΕΑΜ').': '.htmlspecialchars_gks($row['ama_eam']), array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('ΑΜΚΑ').': '.htmlspecialchars_gks($row['amka']), array('name' => 'Tahoma', 'size' => 12));

$user_address=$row['ma_odos'].' '.$row['ma_arithmos'].', '.$row['ma_orofos'].', '.$row['ma_perioxi'].', '.$row['ma_poli'].', '.$row['ma_tk'].', '.$row['nomos_descr'];

$section->addText(gks_lang('Διεύθυνση Κατοικίας').': '.$user_address, array('name' => 'Tahoma', 'size' => 12));
$section->addText(gks_lang('Εθνικότητα').': '.htmlspecialchars_gks($row['ethnikotita']), array('name' => 'Tahoma', 'size' => 12));


$myfile=$row['mylast_name'].'_'.$row['myfirst_name'];


// Saving the document as OOXML file...


header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="'.$myfile.'_'.showDate(time(), 'Y-m-d His', 1).'.docx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0


$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');



exit;