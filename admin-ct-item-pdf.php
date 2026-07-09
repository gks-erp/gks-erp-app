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
if ($id<=0 and $id<>-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$ctid=0;
if (isset($_GET['ctid'])) $ctid=intval($_GET['ctid']);
if ($ctid < 10000) {
  debug_mail(false,'the ctid is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ctid. ('.$ctid.')'));
  echo json_encode($return); die();
}


$my_page_title=gks_lang('Εκτύπωση Δικά μου αντικείμενα').'ctid: '.$ctid.' id: '.$id;
db_open();
stat_record();

$sql_ct="select * 
from gks_custom_table 
where custom_table_disabled=0
and id_custom_table=".$ctid;
$result_ct = $db_link->query($sql_ct);        
if (!$result_ct) {debug_mail(false,'error sql',$sql_ct);die('sql error');}
if ($result_ct->num_rows!=1) {debug_mail(false,'record not found',$sql_ct);die('custom table not found ('.$ctid.')'); }
$row_ct = $result_ct->fetch_assoc();
$custom_table_descr=$row_ct['custom_table_descr'];
$custom_table_name=$row_ct['custom_table_name'];
$custom_table_name_real='gks_customt_'.$row_ct['custom_table_name'];
$field_name_id_parent=$row_ct['field_name_id_parent'];
$field_name_id_current=$row_ct['field_name_id_current'];
$field_id='id_gks_customt_gks_ct_'.$ctid;


$perm_ret=gks_permission_user_can_action($my_wp_user_id, $custom_table_name,($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');



if ($id==-1) {
  debug_mail(false,'error sql','save custom before print');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Αποθηκεύστε πρώτα την εγγραφή')));
  echo json_encode($return); die();}

$sql ="SELECT ".$field_id.",cf_mydate_add,cf_mydate_edit,cf_user_id_add,cf_user_id_edit,cf_myip,
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
FROM (".$custom_table_name_real." 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on ".$custom_table_name_real.".cf_user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on ".$custom_table_name_real.".cf_user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
where ".$field_id." = ".$id;
$sql.=" limit 1"; 

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}

//echo '<pre>sssssss';die();


$row = $result->fetch_assoc();


$save_folder='customtable/'.$ctid.'/'.$id.'/print/';
$save_basename='ct_'.$ctid.'_'.$id.'_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);

$print_params=array(
  'table' => 'gks_customt',
  'ctid' => $ctid ,
  'id' => $id,
  'fileserver' => GKS_FileServerShare,
  'folder'=> $save_folder,
  'filename' => $save_basename,
  'override' => array(
    'gks_lang' => '',     //  '' is default, 'el-GR', 'en-US' 
    'file_type' => '',    //  '' is default, 'pdf','html',
    'grayscale' => -1,    // '-1 is default', 0, 1
    'zoom' => -1,         // '-1 is default', 1, 1.5, 0.8
    'is_landscape' => -1, // '-1 is default', 0, 1
    'is_preview' => 0,
    'createthump' => 0,
  ),
);

if (isset($_POST['file_type'])) {
  if ($_POST['file_type']=='pdf') $print_params['override']['file_type']='pdf';
  else if ($_POST['file_type']=='html') $print_params['override']['file_type']='html';
  else if ($_POST['file_type']=='jpg') $print_params['override']['file_type']='jpg';
}
if (isset($_POST['grayscale'])) {
  if (intval($_POST['grayscale'])==0) $print_params['override']['grayscale']=0;
  else $print_params['override']['grayscale']=1;
}
if (isset($_POST['landscape'])) {
  if (intval($_POST['landscape'])==0) $print_params['override']['is_landscape']=0;
  else $print_params['override']['is_landscape']=1;
}
if (isset($_POST['zoom'])) {
  $zoom=intval($_POST['zoom']);
  if ($zoom < 10 or $zoom > 200) $zoom=100;
  $print_params['override']['zoom']=$zoom/100;
}
$preview=0;
if (isset($_POST['preview'])) {
  $preview=intval($_POST['preview']);
  if ($preview ==0) $print_params['override']['is_preview']=0;
  else $print_params['override']['is_preview']=1;
  
  if ($preview==1) {
    $print_params['fileserver'] = GKS_SITE_PATH.'tmp/';
    $print_params['folder'] = '';
  }  
}

$gks_print_send_gks_erp_app=0;if (isset($_POST['gks_print_send_gks_erp_app'])) $gks_print_send_gks_erp_app=intval($_POST['gks_print_send_gks_erp_app']);
if ($gks_print_send_gks_erp_app!=0) $gks_print_send_gks_erp_app=1;

if ($gks_print_send_gks_erp_app==1 and $preview==0) {
  $sql_send_erp_app="SELECT erp_app_id, erp_app_dest,  
  erp_app_dest_printer, 
  erp_app_dest_printer_method,
  erp_app_dest_printer_lpr_ip,
  erp_app_dest_printer_copies, 
  erp_app_dest_folder, 
  gks_erp_app.id_erp_app, gks_erp_app.erp_app_name, gks_erp_app.erp_app_last_ping
  FROM gks_custom_table 
  LEFT JOIN gks_erp_app ON gks_custom_table.erp_app_id = gks_erp_app.id_erp_app
  where gks_custom_table.id_custom_table=".$ctid;
  $result_send_erp_app = $db_link->query($sql_send_erp_app);        
  if (!$result_send_erp_app) {
    debug_mail(false,'error sql',$sql_send_erp_app);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
    
  if ($result_send_erp_app->num_rows==1) {
    $row_send_erp_app = $result_send_erp_app->fetch_assoc();
    $print_params['gks_erp_app']=array(
      'id_erp_app'=>intval($row_send_erp_app['erp_app_id']),
      'erp_app_dest'=>trim_gks($row_send_erp_app['erp_app_dest']),
      'erp_app_dest_printer'=>trim_gks($row_send_erp_app['erp_app_dest_printer']),
      'erp_app_dest_printer_method'=>intval($row_send_erp_app['erp_app_dest_printer_method']),
      'erp_app_dest_printer_lpr_ip'=>trim_gks($row_send_erp_app['erp_app_dest_printer_lpr_ip']),
      'erp_app_dest_printer_copies'=>intval($row_send_erp_app['erp_app_dest_printer_copies']),
      'erp_app_dest_folder'=>trim_gks($row_send_erp_app['erp_app_dest_folder']),
    );
  }
}

$form_id=0;if (isset($_POST['form_id'])) $form_id=intval($_POST['form_id']);

//echo '<pre>'; print_r($print_params); die();

$ret_print = gks_print_form('gks_customt',$id,$form_id,$print_params);

//echo '<pre>'; print_r($ret_print); die();



if ($ret_print['success']==false) {
  debug_mail(false,'error print task',$ret_print['message']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την δημιουργία της εκτύπωσης').'<br>'.$ret_print['message']));
  echo json_encode($return); die();}

$save_but_message='';
$preview_url='';

if ($preview==0) {
//  $sql="update gks_crm_tasksaaaaaaaa set 
//    print_date=now(),
//    print_file_name='".$db_link->escape_string($ret_print['save_basename'])."',
//    print_file_url='".$db_link->escape_string($ret_print['url_file'])."',
//    print_user_id=".$my_wp_user_id.",
//    print_crm_task_status='".$db_link->escape_string($task_status)."',
//    print_crm_task_status_id=".$task_status_id."
//    where id_crm_task=".$id;
//  
//  $result = $db_link->query($sql);  
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die(); }  
  
  $sxolio_log = gks_lang('Εκτύπωση').'<br>'.
  gks_lang('Αρχείο').': <a href="'.$ret_print['url_file'].'" target="_blank">'.$ret_print['save_basename'].'</a> '.
  '<a href="'.$ret_print['url_file'].'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
  
  $sql="insert into ".$custom_table_name_real."_log (
  gks_customt_gks_".$field_name_id_current.", add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
    
  $save_but_message=gks_lang('Επιτυχής δημιουργία του αρχείου εκτύπωσης').'<br>'.gks_lang('Τι θέλετε να κάνετε ;').'<br>'.
  '<a href="'.$ret_print['url_file'].'"            class="gks_link" target="_blank">'.gks_lang('Άνοιγμα σε νέα καρτέλα').'</a><br>'.
  '<a href="'.$ret_print['url_file'].'&download=1" class="gks_link" target="_blank">'.gks_lang('Λήψη').'</a>';
  if (isset($ret_print['gks_erp_message'])) {
    $save_but_message.=$ret_print['gks_erp_message'];
  }
} else {
  $preview_url=$ret_print['url_file'];
}


if (isset($_POST['set_def']) and $_POST['set_def']=='1') {
  $user_settings=array();
  $user_settings['print']['file_type']= $print_params['override']['file_type'];
  $user_settings['print']['grayscale']=($print_params['override']['grayscale']==0 ? 'false' : 'true');
  $user_settings['print']['landscape']=($print_params['override']['is_landscape']==0 ? 'false' : 'true');
  $user_settings['print']['zoom']=     number_format($print_params['override']['zoom']*100,1,'.','');
  $user_settings['print']['form_id_customt']=  number_format($form_id,0,'','');



  gks_set_user_settings($my_wp_user_id, $user_settings);
  
}

//echo '<pre>'; print_r($ret_print); die();

$return = array('success' => true, 
  'message' => base64_encode('ok'), 
  'save_but_message' => base64_encode($save_but_message),
  'redirect'=> base64_encode('admin-ct-item.php?ctid='.$ctid.'&id='.$id.'&scrollto=gks_print'),
  'preview_url' => $preview_url,
);
echo json_encode($return); die();
