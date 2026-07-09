<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
/*
https://test.easyfilesselection.com/my/cron_paroxos.php?in_progress=1
https://test.easyfilesselection.com/my/cron_paroxos.php?get_files=1
https://test.easyfilesselection.com/my/cron_paroxos.php?get_files=1&id=11512
https://test.easyfilesselection.com/my/cron_paroxos.php?send_pdf=1&id=11518
https://test.easyfilesselection.com/my/cron_paroxos.php?get_keys=8
*/

ini_set('max_execution_time', 600);
set_time_limit(600);
putenv("ENV=PRODUCTION");
define('SECURE', 1);

require_once('_current/_config.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');


$my_page_title='cron_paroxos.php';
$my_wp_user_id=2;
$gkIP='127.0.0.1';
db_open();
stat_record();



$ret=[];

$ids=[]; $aade_paroxos_id=[];
if (isset($_GET['id'])) $ids[]=$_GET['id'];
if (isset($_GET['pid'])) $aade_paroxos_id[]=$_GET['pid'];

$doc_table='';
if (isset($_GET['doc_table'])) $doc_table=$_GET['doc_table'];

if (isset($_GET['in_progress'])) {
  $ret=gks_paroxos_invoice_xml_get_in_progress($doc_table,$ids,$aade_paroxos_id); 
}

if (isset($_GET['get_files'])) {
  $ret=gks_paroxos_invoice_xml_get_files($doc_table,$ids,$aade_paroxos_id); 
}

if (isset($_GET['send_pdf'])) {
  $ret=gks_paroxos_invoice_xml_send_pdf($doc_table,$ids,$aade_paroxos_id); 
}
if (isset($_GET['get_keys'])) {
  $ret=gks_paroxos_get_keys(intval($_GET['get_keys']));
}


//debug_mail(false,'cron_paroxos.php',print_r($ret,true));
//echo '<pre>';print_r($ret);

