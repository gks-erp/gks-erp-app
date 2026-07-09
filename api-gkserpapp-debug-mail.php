<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

//echo strtotime('0001-01-01T00:00:00');
//die();

//$my_RAW = file_get_contents( 'php://input' );
//$my_RAW = urldecode($my_RAW);
//$my_RAW = json_decode($my_RAW, true);




$rnd1s='';
if (isset($_GET['rnd1s'])) $rnd1s=trim_gks($_GET['rnd1s']);

$send1='';
if (isset($_GET['send1'])) $send1=trim_gks($_GET['send1']);

$id_erp_app=0;
if (isset($_GET['id'])) $id_erp_app = intval($_GET['id']);


if ($rnd1s=='' or $send1=='') {
  debug_mail(false,gks_lang('Δεν έχουν ορισθεί όλες οι παράμετροι'),'');
  echo 'error:'.gks_lang('Δεν έχουν ορισθεί όλες οι παράμετροι');
  die(); 
}





db_open();
stat_record();


$sql="SELECT * FROM gks_erp_app WHERE id_erp_app=".$id_erp_app;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql); 
  //echo 'error:error sql';  die();
} 
  
if ($result->num_rows < 1) {
  if ($id_erp_app!=0) {
	  debug_mail(false,'id_erp_app not found',$sql); 
	}
	
	$erp_app_name='--';
	$erp_app_secret='--';
} else {
  $row = $result->fetch_assoc();  
  $erp_app_name = $row['erp_app_name'];  
  $erp_app_secret = $row['erp_app_secret'];  
}

$send1_calc= md5($rnd1s . $rnd1s . $id_erp_app . $rnd1s . $erp_app_secret .  GKS_ERP_HASHMD5KEY09);
//echo $send1_calc; die();

if ($send1 != $send1_calc) {
  debug_mail(false,'security error api','');
  echo 'error:security error';
  die(); 
}



$message='';
if (isset($_POST['message'])) $message = urldecode($_POST['message']);
$group='';
if (isset($_POST['group'])) $group = urldecode($_POST['group']);

$mysubject = 'gks ERP '.$erp_app_name. ' '. $group;

//if ($group=='wav' or $group=='picasa_sync') {
  //debug_mail(false, $group, $message, $mysubject);
  debug_mail(false, '', $message,'Lab: '.$group);
//}

$sql="insert into gks_erp_app_log (mydate,erp_app_id,mygroup,message,ip) values (
now(),
".$id_erp_app.",
'".$db_link->escape_string($group)."',
'".$db_link->escape_string($message)."',
'".$db_link->escape_string($gkIP)."')";
$run = $db_link->query($sql);
if (!$run) {
  debug_mail(false,'error sql',$sql); 
  //echo 'error:error sql';  die();
} 





$responseok = md5($rnd1s . $id_erp_app .  GKS_ERP_HASHMD5KEY10);
echo $responseok;
die();

