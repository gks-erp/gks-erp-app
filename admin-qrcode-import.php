<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


//die();


$my_page_title=gks_lang('Εισαγωγή απόδειξη λιανικής από QR Code');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','add',-1);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$url=''; if (isset($_POST['url'])) $url=trim_gks(base64_decode($_POST['url']));
if ($url=='') {
	debug_mail(false,'set url');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το url')));
	echo json_encode($return); die();}

$cid=''; if (isset($_POST['cid'])) $cid=trim_gks(base64_decode($_POST['cid']));
$company_id=0;
$company_sub_id=0;
$parts=explode('|',$cid);
if (count($parts)==2) {
	$company_id=intval($parts[0]);
	$company_sub_id=intval($parts[1]);
}
if ($company_id<=0 and $company_sub_id<=0) {
	debug_mail(false,'select a company');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε μια εταιρία την οποία αφορά η απόδειξη')));
	echo json_encode($return); die();}	
	
gks_set_user_settings($my_wp_user_id, array('gks_acc_inv'=>array('def_company_qrcode'=>$cid)));

$params=array(
	'company_id'=>$company_id,
	'company_sub_id' => $company_sub_id,
);

//$url= 'https://www1.aade.gr/tameiakes/myweb/q1.php?SIG=DLF20001965000837735CAB70BF2BE309F0DE10E142E1D7687CB002ADD212.80';
$ret=gks_get_www1_aade_gr_tameiakes_myweb_q1_php($url,$params);

$ret['message']=base64_encode($ret['message']);
echo json_encode($ret); die();  
print '<pre>';print_r($ret);die();

