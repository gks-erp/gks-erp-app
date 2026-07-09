<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Λήψη κατάστασης ΑΦΜ');
db_open();
stat_record();

//$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_journal',($id==-1 ? 'add':'edit'),$id);
//if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$id=-1; if (isset($_POST['id'])) $id=intval($_POST['id']);
$from=''; if (isset($_POST['from'])) $from=trim_gks($_POST['from']);
$company_id=0; if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$company_sub_id=0; if (isset($_POST['company_sub_id'])) $company_sub_id=intval($_POST['company_sub_id']);
$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$afm=''; if (isset($_POST['afm'])) $afm=trim_gks($_POST['afm']);
$ma_country_id=0; if (isset($_POST['ma_country_id'])) $ma_country_id=intval($_POST['ma_country_id']);
$parastatiko=0; if (isset($_POST['parastatiko'])) $parastatiko=intval($_POST['parastatiko']);


unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']=$from;
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']= $company_id;
$mybasketarray['company_sub_id']= $company_sub_id;
$mybasketarray['user']['user_id']=$user_id;
$mybasketarray['user']['afm']=$afm;
$mybasketarray['user']['ma_country_id']=$ma_country_id;
$mybasketarray['parastatiko']=$parastatiko; //parastatiko

gks_CheckAFM_Live($mybasketarray);
$check_vies=$mybasketarray['check_vies'];

$return = array('success' => true, 'message' => base64_encode('OK'), 'check_vies' =>$check_vies);
echo json_encode($return); die();
    
//echo '<pre>ssssssssss ';print_r($check_vies);die();

//CheckAFM_GSIS_VIES_async('065053317',1,'','gsis');
//die();


