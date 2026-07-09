<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');

$my_page_title=gks_lang('CM Webhook Page SITE');
debug_mail(false,$my_page_title,'');

db_open();
stat_record();


$php_input = trim_gks(file_get_contents('php://input'));
if ($php_input=='') {debug_mail(false,'empty data SITE '.$my_page_title,'empty data');die();}
$mydata = json_decode($php_input,true);

if (is_array($mydata)==false or 
    isset($mydata['channelId'])==false or isset($mydata['propertyId'])==false or
    isset($mydata['reservationId'])==false or isset($mydata['reservationStatus'])==false or 
    isset($mydata['ssss1'])==false or isset($mydata['ssss2'])==false) {

  debug_mail(false,'error lathos data SITE'.$my_page_title,'lathos data');      
  die();
}

$ssss1=trim_gks($mydata['ssss1']);
$ssss2=trim_gks($mydata['ssss2']);
$ssss2_calc=md5($ssss1.'kostasgksgoutoudiserp'.$ssss1.$ssss1);

if ($ssss2!=$ssss2_calc) {debug_mail(false,'security error SITE '.$ssss2.'!='.$ssss2_calc,$ssss1.' '.$ssss2.' '.$ssss2_calc);  die();}

//echo '<pre>';print_r($mydata);die();

$resdata=gks_zodomus_get_url('/reservations','GET',array('channelId'=>$mydata['channelId'],'propertyId'=>$mydata['propertyId'],'reservationId'=>$mydata['reservationId']));
//var_dump($resdata);die();
//echo '<pre>';print_r($resdata);die();

if (is_array($resdata)==false or isset($resdata['success'])==false or $resdata['success']==false or isset($resdata['response_array'])==false) {
  debug_mail(false,'CM Hook error not array',print_r($resdata,true)); die();}


$log_path=GKS_SITE_PATH.'logs_cm/';  

if (file_exists($log_path)==false) {
  if (@mkdir($log_path , 0755, true) == false ) {
    debug_mail(false,'can not create dir: ',$log_path);
    //die('error');
  }
}  
$log_file=$log_path.showDate(time(),'Y-m-d_H-i-s',1).'_'.time().'_'.rand(10000,99999).'.txt';
file_put_contents($log_file,print_r($resdata,true));

$response_array_str=json_encode($resdata['response_array']);

$params=array('channelId' => $mydata['channelId'],'propertyId' => $mydata['propertyId']);
$ret = gks_hotel_cm_reservation_parse($response_array_str, $params);


//echo '<pre>';print_r($ret);die();


echo time();
