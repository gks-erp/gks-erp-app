<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();
$my_page_title=gks_lang('Εξαγωγή Τραπεζικών Λογαριασμών σε Excel');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_bank_accounts','view',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



/*
where gks_bank_accounts.user_id>0 
and gks_bank_accounts.show_eshop=0 
and deleted_from_user=0
*/
$query = "SELECT gks_bank_accounts.*, tabletr.trcc, gks_banks.bank_descr, ".GKS_WP_TABLE_PREFIX."users.gks_fullname
FROM ((gks_bank_accounts LEFT JOIN (SELECT gks_payments_bank.our_bank_account, Count(gks_payments_bank.id_payment_bank) AS trcc
  FROM gks_payments_bank
  GROUP BY gks_payments_bank.our_bank_account
)  AS tabletr ON gks_bank_accounts.id_bank_account = tabletr.our_bank_account) LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank) LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_bank_accounts.user_id = ".GKS_WP_TABLE_PREFIX."users.ID

ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_fullname,gks_banks.bank_descr, gks_bank_accounts.IBAN";

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
//$objPHPExcel = new PHPExcel();
$objPHPExcel = new PhpOffice\PhpSpreadsheet\Spreadsheet();

// Set document properties
$objPHPExcel->getProperties()->setCreator($GKS_SITE_HUMAN_NAME)
							 ->setLastModifiedBy($GKS_SITE_HUMAN_NAME)
							 ->setTitle('Bank Accounts')
							 ->setSubject('Bank Accounts')
							 ->setDescription('Bank Accounts')
							 ->setKeywords('Bank Accounts '.$GKS_SITE_HUMAN_NAME)
							 ->setCategory('Bank Accounts '.$GKS_SITE_HUMAN_NAME);


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', gks_lang('ID'))
            ->setCellValue('B1', gks_lang('Επαφή'))
            ->setCellValue('C1', gks_lang('Περιγραφή'))
            ->setCellValue('D1', gks_lang('Τράπεζα'))
            ->setCellValue('E1', gks_lang('IBAN'))
            ->setCellValue('F1', gks_lang('Αριθμός Λογαριασμού'))
            ->setCellValue('G1', gks_lang('Τύπος'))
            ->setCellValue('H1', gks_lang('Δικαιούχος'))
            ->setCellValue('I1', gks_lang('Επεξεργασία'))
            //->setCellValue('I1', gks_lang('Προβολή στο eshop'))
            //->setCellValue('J1', gks_lang('Διαγραμμένο από χρήστη'))
            //->setCellValue('K1', gks_lang('Πλήθος συναλλαγών'))
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


$i=1;
while ($line = $result->fetch_assoc()) {

	$i++;
	if (empty($line['date_edit'])) $line['date_edit']='2000-01-01';
  $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $line['id_bank_account'])
            ->setCellValue('B'.$i, $line["gks_fullname"])
            ->setCellValue('C'.$i, $line["account_descr"])
            ->setCellValue('D'.$i, $line["bank_descr"])
            ->setCellValue('E'.$i, $line["IBAN"])
            ->setCellValue('F'.$i, $line["account_number"].' ')
            ->setCellValue('G'.$i, $line["account_type"])
            ->setCellValue('H'.$i, $line["account_dikaiouxos"])
            ->setCellValue('I'.$i, PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(_time_user(strtotime($line['mydate_edit']),1)))
            ;

  $objPHPExcel->getActiveSheet()
    //->getCell([9, $i])
    ->getCellByColumnAndRow(9, $i)
    ->getStyle()
    ->getNumberFormat()
    ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY); //FORMAT_DATE_DATETIME

            
    
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
//$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
//$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

//$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Bank Accounts');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);



header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Bank Accounts '.showDate(time(), 'Y-m-d His', 1).'.xlsx"');
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
