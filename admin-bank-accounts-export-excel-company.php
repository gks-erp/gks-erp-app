<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

$my_page_title=gks_lang('Εξαγωγή Τραπεζικών Λογαριασμών και εταιρειών σε Excel');



db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_bank_accounts','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$query = "SELECT gks_company.company_title,gks_bank_accounts.mydate_edit, gks_banks.bank_descr, ".GKS_WP_TABLE_PREFIX."users.gks_fullname, gks_bank_accounts.IBAN, gks_bank_accounts.account_dikaiouxos
FROM (((gks_bank_accounts LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank) LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_bank_accounts.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) LEFT JOIN gks_company_users ON gks_bank_accounts.user_id = gks_company_users.user_id) LEFT JOIN gks_company ON gks_company_users.company_id = gks_company.id_company
WHERE (((".GKS_WP_TABLE_PREFIX."users.ID)>0) AND ((gks_bank_accounts.show_eshop)=0) AND ((gks_bank_accounts.deleted_from_user)=0))
ORDER BY gks_company.company_sortorder, gks_company.company_title, gks_banks.bank_descr, ".GKS_WP_TABLE_PREFIX."users.gks_fullname, gks_bank_accounts.IBAN;";

$result = $db_link->query($query);        
if (!$result) debug_mail(false,'error sql',$query);
if (!$result) die('sql error');



//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('UTC');

//if (PHP_SAPI == 'cli') die('This example should only be run from a Web Browser');

/** Include PHPExcel */
//require_once dirname(__FILE__) . '/PHPExcel/PHPExcel.php';


// Create new PHPExcel object
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//$objPHPExcel = new Spreadsheet();
$objPHPExcel = new PhpOffice\PhpSpreadsheet\Spreadsheet();

// Set document properties
$objPHPExcel->getProperties()->setCreator($GKS_SITE_HUMAN_NAME)
							 ->setLastModifiedBy($GKS_SITE_HUMAN_NAME)
							 ->setTitle('Company-Bank Accounts')
							 ->setSubject('Company-Bank Accounts')
							 ->setDescription('Company-Bank Accounts')
							 ->setKeywords('Company-Bank Accounts '.$GKS_SITE_HUMAN_NAME)
							 ->setCategory('Company-Bank Accounts '.$GKS_SITE_HUMAN_NAME);


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', gks_lang('Εταιρεία'))
            ->setCellValue('B1', gks_lang('Τράπεζα'))
            ->setCellValue('C1', gks_lang('IBAN'))
            ->setCellValue('D1', gks_lang('Υπάλληλος'))
            ->setCellValue('E1', gks_lang('Δικαιούχος'))
            ->setCellValue('F1', gks_lang('Επεξεργασία'))
            ;
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('B1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('C1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('D1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('E1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));
//$objPHPExcel->setActiveSheetIndex(0)->getStyle('F1')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'aaaaaa'))));


$i=1;
while ($line = $result->fetch_assoc()) {

	$i++;
  $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $line['company_title'])
            ->setCellValue('B'.$i, $line["bank_descr"])
            ->setCellValue('C'.$i, $line["IBAN"])
            ->setCellValue('D'.$i, $line["gks_fullname"])
            ->setCellValue('E'.$i, $line["account_dikaiouxos"])
            //->setCellValue('F'.$i, PHPExcel_Shared_Date::PHPToExcel(_time_user(strtotime($line['date_edit']),1)))
            ;
            
  $objPHPExcel->getActiveSheet()
    //->getStyle([5, $i])
    ->getStyleByColumnAndRow(5, $i)
//    ->getNumberFormat()->setFormatCode(
//        PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME
//    )
    ;            

}
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);


$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Bank Accounts');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);



header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Company-Bank Accounts '.showDate(time(), 'Y-m-d His', 1).'.xlsx"');
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
