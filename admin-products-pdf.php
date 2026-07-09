<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Εκτύπωση Ειδών');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','view',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


include_once('admin-products_filters.php');


//echo '<pre>'.$sql;die();


$save_folder='lists/print/';
$save_basename='products_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
$print_params=array(
  'table' => 'gks_eshop_products',
  'id' => 0,
  'sql' => $sql,
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
  $sql_send_erp_app="SELECT gks_acc_inv.id_acc_inv, gks_acc_seires.erp_app_id, gks_acc_seires.erp_app_dest, 
  gks_acc_seires.erp_app_dest_printer, 
  gks_acc_seires.erp_app_dest_printer_method,
  gks_acc_seires.erp_app_dest_printer_lpr_ip,
  gks_acc_seires.erp_app_dest_printer_copies, 
  gks_acc_seires.erp_app_dest_folder
  FROM (gks_acc_inv1111111111 
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira) 
  LEFT JOIN gks_erp_app ON gks_acc_seires.erp_app_id = gks_erp_app.id_erp_app
  WHERE gks_acc_inv.id_acc_inv=".$id." 
  AND gks_acc_inv.inv_acc_seira_id>0
  AND gks_acc_seires.erp_app_id>0
  AND gks_erp_app.id_erp_app>0";
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

$ret_print = gks_print_form('gks_eshop_products',0,$form_id,$print_params);

//echo '<pre>'; print_r($ret_print); die();



if ($ret_print['success']==false) {
  debug_mail(false,gks_lang('Σφάλμα κατά την δημιουργία της εκτύπωσης'),gks_lang('Σφάλμα κατά την δημιουργία της εκτύπωσης').'<br>'.$ret_print['message']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την δημιουργία της εκτύπωσης').'<br>'.$ret_print['message']));
  echo json_encode($return); die();}

$save_but_message='';
$preview_url='';

if ($preview==0) {
    
  $save_but_message=gks_lang('Επιτυχής δημιουργία του αρχείου εκτύπωσης').'<br>'.gks_lang('Τι θέλετε να κάνετε ;').'<br>'.
  '<a href="'.$ret_print['url_file'].'"            class="gks_link" target="_blank">'.gks_lang('Άνοιγμα σε νέα καρτέλα').'</a><br>'.
  '<a href="'.$ret_print['url_file'].'&download=1" class="gks_link" target="_blank">'.gks_lang('Λήψη').'</a>';
  if (isset($ret_print['gks_erp_message'])) {
    $save_but_message.=$ret_print['gks_erp_message'];
  }
  if (isset($ret_print['gks_paroxos_send_pdf_message'])) {
    $save_but_message.=$ret_print['gks_paroxos_send_pdf_message'];
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
  $user_settings['print']['form_id_products']=  number_format($form_id,0,'','');



  gks_set_user_settings($my_wp_user_id, $user_settings);
  
}



//echo '<pre>'; print_r($ret_print); die();

$return = array('success' => true, 
  'message' => base64_encode('ok'), 
  'save_but_message' => base64_encode($save_but_message),
  'redirect'=> '',
  'preview_url' => $preview_url,
);
echo json_encode($return); die();
