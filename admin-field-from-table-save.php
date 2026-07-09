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

//$dev_page_starttime11=microtime(true);



$my_page_title=gks_lang('Αποθήκευση Service Παγίου').' field_from_table';
db_open();
stat_record();


$table_name=''; if (isset($_POST['table_name'])) $table_name=trim_gks(base64_decode($_POST['table_name']));
$table_id=0; if (isset($_POST['table_id'])) $table_id=intval($_POST['table_id']);
$field_name=''; if (isset($_POST['field_name'])) $field_name=trim_gks(base64_decode($_POST['field_name']));
$myvalue=''; if (isset($_POST['myvalue'])) $myvalue=trim_gks(base64_decode($_POST['myvalue']));

if ($table_name=='' or $table_id<=0 or $field_name=='') {
  debug_mail(false,'table_name empty, table_id zero,field_name empty ');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχουν ορισθεί όλοι οι παράμετροι')));
  echo json_encode($return); die();}

$perm_table=$table_name;


switch ($table_name) {   
  case 'gks_aade_katigoria_loipon_foron':
    $table_field_id='id_aade_katigoria_loipon_foron';$perm_table='gks_aade';break;
  case 'gks_aade_katigoria_telon':
    $table_field_id='id_aade_katigoria_telon';$perm_table='gks_aade';break;
  case 'gks_aade_katigoria_xartosimou':
    $table_field_id='id_aade_katigoria_xartosimou';$perm_table='gks_aade';break;
  case 'gks_aade_katigoria_fpa_ejeresi':
    $table_field_id='id_aade_katigoria_fpa_ejeresi';$perm_table='gks_aade';break;
  case 'gks_aade_katigoria_parakratoumemenon_foron':
    $table_field_id='id_aade_katigoria_parakratoumemenon_foron';$perm_table='gks_aade';break;
  case 'gks_acc_eidi_parastatikon':
    $table_field_id='id_acc_eidos_parastatikou';$perm_table='gks_aade';break;
  
  default:      
    debug_mail(false,'not permited','admin-field-from-table-save table '.$table_name);
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$table_name,gks_lang('Δεν είναι δυνατή η διαδικασία <b>admin-field-from-table-save</b> για τον πίνακα <b>[1]</b>'))));
    echo json_encode($return); die();    
}

$perm_ret=gks_permission_user_can_action($my_wp_user_id, $perm_table,'edit',$table_id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

switch ($table_name.'|'.$field_name) {   
  case 'gks_aade_katigoria_loipon_foron|loipon_foron_peppol_code':break;
  case 'gks_aade_katigoria_telon|telon_peppol_code':break;
  case 'gks_aade_katigoria_xartosimou|xartosimou_peppol_code':break;
  case 'gks_aade_katigoria_fpa_ejeresi|fpa_ejeresi_peppol_code':break;
  case 'gks_aade_katigoria_parakratoumemenon_foron|parakrat_peppol_code':break;
  case 'gks_acc_eidi_parastatikon|peppol_code':break;
  
 
  
  default:      
    debug_mail(false,'not permited',$table_name.'|'.$field_name);
    $tmpmsg=gks_lang('Δεν είναι δυνατή η διαδικασία <b>admin-field-from-table-save</b> για τον πίνακα <b>[1]</b> και για το πεδίο <b>[2]</b>');
    $tmpmsg=str_replace('[1]',$table_name,$tmpmsg);
    $tmpmsg=str_replace('[2]',$field_name,$tmpmsg);    
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die();    
}

$sql="update ".$table_name." 
set ".$field_name."='".$db_link->escape_string($myvalue)."'
where ".$table_field_id."=".$table_id." limit 1";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}

$return = array(
  'success' => true, 
  'message' => base64_encode('OK'), 
  'table_name' => $table_name,
  'table_id' => $table_id,
  'field_name' => $field_name,
  'myvalue' => $myvalue,
);
echo json_encode($return); die();

    
echo '<pre>ddddddddd';die();
