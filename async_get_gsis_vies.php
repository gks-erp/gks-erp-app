<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');



//sleep(1);
if ( !isset( $HTTP_RAW_POST_DATA ) ) {
	$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
}
  
// fix for mozBlog and other cases where '<?xml' isn't on the very first line
if ( isset($HTTP_RAW_POST_DATA) )
	$HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);

//print '<pre>';print_r($_POST);print_r($_GET);print_r($HTTP_RAW_POST_DATA);die();

if (isset($HTTP_RAW_POST_DATA)==false or $HTTP_RAW_POST_DATA=='') {
	debug_mail(false,'error async_get_gsis_vies','');
	die();}

$data=json_decode($HTTP_RAW_POST_DATA, true);
if (isset($data['afm'])==false or 
    isset($data['company_id'])==false or 
    isset($data['country_ee'])==false or 
    isset($data['poio'])==false) {
	debug_mail(false,'error async_get_gsis_vies','');
	die();    	
}

$my_page_title=gks_lang('async_get_gsis_vies');
db_open();
stat_record();
/*
Array
(
    [afm] => 065053317
    [company_id] => 1
    [country_ee] => 
    [poio] => gsis
)*/
if ($data['poio']=='gsis') {
	$ret=CheckAFM_GSIS($data['afm'],$data['company_id'],true);
} else if ($data['poio']=='vies') { 
	$ret=CheckAFM_VIES($data['country_ee'],$data['afm'],true);
} else {
	debug_mail(false,'error async_get_gsis_vies','');
	die();
}

debug_mail(false,'async_get_gsis_vies run',print_r($ret,true));


//print '<pre>';print_r($data);die();

//echo time();
