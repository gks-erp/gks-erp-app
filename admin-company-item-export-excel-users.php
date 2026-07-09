<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$company_id=intval($_GET['company_id']);
if ($company_id<=0) die('id not set');


$my_page_title=gks_lang('Εξαγωγή λίστας υπαλλήλων εταιρείας σε Excel').': '.$company_id;

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_company_users','view',$company_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="select * from gks_company where id_company=".$company_id;
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
if ($result->num_rows!=1) die('company not found');
$row = $result->fetch_assoc();
$company_title=$row['company_title'];

$sql = "SELECT DISTINCT ".GKS_WP_TABLE_PREFIX."users.*, gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, gks_users.ma_poli, gks_users.ma_tk, 
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
gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr,gks_users.oikogeniaki_katastasti_id, gks_users.oikogeniaki_katastasti_paidia,
gks_company_users.date_hire,gks_company_users.sxolio
FROM (((((((((((gks_company_users
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_company_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist) 
LEFT JOIN gks_users_oikogeniaki_katastasti ON gks_users_oikogeniaki_katastasti.id_oikogeniaki_katastasti = gks_users.oikogeniaki_katastasti_id) 
LEFT JOIN gks_users_groups_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users_groups_users.user_id)
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

WHERE gks_company_users.company_id=".$company_id."
ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname";




$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');

//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('UTC');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
//require_once dirname(__FILE__) . '/PHPExcel/PHPExcel.php';


// Create new PHPExcel object
//$objPHPExcel = new PHPExcel();
$objPHPExcel = new PhpOffice\PhpSpreadsheet\Spreadsheet();

// Set document properties
$objPHPExcel->getProperties()
               ->setCreator($GKS_SITE_HUMAN_NAME)
							 ->setLastModifiedBy($GKS_SITE_HUMAN_NAME)
							 ->setTitle($company_title." Users")
							 ->setSubject($company_title." Users")
							 ->setDescription($company_title." Users")
							 ->setKeywords($company_title.' Users '.$GKS_SITE_HUMAN_NAME)
							 ->setCategory($company_title.' Users '.$GKS_SITE_HUMAN_NAME)
							 ;

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', gks_lang('ID'))
            ->setCellValue('B1', gks_lang('Επίθετο'))
            ->setCellValue('C1', gks_lang('Όνομα'))
            ->setCellValue('D1', gks_lang('Υποκοριστικό'))
            ->setCellValue('E1', gks_lang('Τηλέφωνο'))
            ->setCellValue('F1', gks_lang('Πατρώνυμο'))
            ->setCellValue('G1', gks_lang('Μητρώνυμο'))
            ->setCellValue('H1', gks_lang('Ημερομηνία Γέννησης'))
            ->setCellValue('I1', gks_lang('Πρόσληψη'))
            ->setCellValue('J1', gks_lang('Σχόλιο'))
            ->setCellValue('K1', gks_lang('ΑΦΜ'))
            ->setCellValue('L1', gks_lang('ΑΜΑ - ΕΑΜ'))
            ->setCellValue('M1', gks_lang('ΑΜΚΑ'))
            ;

$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('B1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('C1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('D1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('E1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('F1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('G1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('H1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('I1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('J1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('K1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('L1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
$objPHPExcel->setActiveSheetIndex(0)->getStyle('M1')->applyFromArray(array('fill' => array('type' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));





$i=1;
while ($line = $result->fetch_assoc()) {

	$i++;

            
  $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setWrapText(true);
  $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setWrapText(true);
            
  $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $line['ID'])
            ->setCellValue('B'.$i, trim_gks($line["mylast_name"]))
            ->setCellValue('C'.$i, trim_gks($line["myfirst_name"]))
            ->setCellValue('D'.$i, trim_gks($line["gks_nickname"]))
            ->setCellValueExplicit('E'.$i, trim_gks($line["mymoobile"]), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
            ->setCellValue('F'.$i, trim_gks($line["onoma_patera"]))
            ->setCellValue('G'.$i, trim_gks($line["onoma_miteras"]))
            ->setCellValue('J'.$i, trim_gks($line["sxolio"]))
            ->setCellValueExplicit('K'.$i, trim_gks($line["afm"]), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
            ->setCellValueExplicit('L'.$i, trim_gks($line["ama_eam"]), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
            ->setCellValueExplicit('M'.$i, trim_gks($line["amka"]), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
            ;
            


            
            
            
  if (isset($line['genisi_date'])) {
    $objPHPExcel->setActiveSheetIndex(0)    
                ->setCellValue('H'.$i, PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($line['genisi_date'])));
              
    $objPHPExcel->getActiveSheet()
      //->getCell([8, $i])
      ->getCellByColumnAndRow(8, $i)
      ->getStyle()
      ->getNumberFormat()
      ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY); //FORMAT_DATE_DATETIME


  }
  if (isset($line['date_hire'])) {
    $ddd = strtotime($line['date_hire']);
    
     
    $objPHPExcel->setActiveSheetIndex(0)    
                ->setCellValue('I'.$i, PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($ddd));

    $objPHPExcel->getActiveSheet()
      //->getCell([9, $i])
      ->getCellByColumnAndRow(9, $i)
      ->getStyle()
      ->getNumberFormat()
      ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY); //FORMAT_DATE_DATETIME
                      
    
  }
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);




// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle($company_title);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


//
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$company_title.' Users '.showDate(time(), 'Y-m-d His', 1).'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
$objWriter->save('php://output');
exit;


