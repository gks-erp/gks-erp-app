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
$my_page_title=gks_lang('Εκτύπωση Παραστατικού').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','view',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





if ($id==-1) {
    debug_mail(false,'save before');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αποθηκεύστε πρώτα το παραστατικό')));
    echo json_encode($return); die();}

$sql=select_gks_acc_inv()." where id_acc_inv=".$id." limit 1"; 
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows!=1) {
  debug_mail(false,'record nt found',$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();}

$row = $result->fetch_assoc();
$inv_state=trim_gks($row['inv_state']);



$save_folder='acc/inv/'.$id.'/print/';
$save_basename='INV_'.$id.'_'.greeklish(getAccInvStateDescr($inv_state)).'_'.showDate(time(), 'Y-m-d_H.i.s',1).'.'.rand(100,999);
$print_params=array(
  'table' => 'gks_acc_inv',
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

$gks_paroxos_send_pdf=0;if (isset($_POST['gks_paroxos_send_pdf'])) $gks_paroxos_send_pdf=intval($_POST['gks_paroxos_send_pdf']);
if ($gks_paroxos_send_pdf!=0) $gks_paroxos_send_pdf=1;

if ($gks_paroxos_send_pdf==1 and $preview==0) {
  
  $sql_paroxos_send_pdf="select id_acc_inv,company_id,company_sub_id,aade_paroxos_id,paroxos_processId,
  print_date,print_file_name,print_file_url,print_user_id,print_inv_state
  from gks_acc_inv
  where paroxos_status=1
  and aade_paroxos_id>0
  and inv_state in ('090ekdosi','100payment')
  and paroxos_send_pdf is null
  and id_acc_inv=".$id;
  $result_paroxos_send_pdf = $db_link->query($sql_paroxos_send_pdf);        
  if (!$result_paroxos_send_pdf) {
    debug_mail(false,'error sql',$sql_paroxos_send_pdf);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  if ($result_paroxos_send_pdf->num_rows==0) {
    debug_mail(false,'can not send paroxos_send_pdf',$sql_paroxos_send_pdf);
    
    $tmpmsg=gks_lang('Αυτό το παραστατικό δεν μπορεί να αποσταλεί στον πάροχο γιατί').'<br>'.
            gks_lang('ή το παραστατικό (xml) δεν έχει αποσταλεί στον πάροχο').',<br>'.
            gks_lang('ή έχει σταλεί ήδη η εκτύπωση').',<br>'.
            gks_lang('ή η κατάσταση δεν είναι').' <b>'.getAccInvStateDescr('090ekdosi').'</b>-<b>'.getAccInvStateDescr('100payment').'</b>,<br>'.
            gks_lang('ή δεν έχει ορισθεί η σειρά για αποστολή σε πάροχο');
    
    
    $return = array('success' => false, 'message' => base64_encode($tmpmsg));
    echo json_encode($return); die();}

  $print_params['paroxos_send_pdf']=true;
  
  //echo '<pre>fffffffff';die();
  
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
  FROM (gks_acc_inv 
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

$ret_print = gks_print_form('gks_acc_inv',$id,$form_id,$print_params);

//echo '<pre>'; print_r($ret_print); die();



if ($ret_print['success']==false) {
  debug_mail(false,'errpr print ',$ret_print['message']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά την δημιουργία της εκτύπωσης').'<br>'.$ret_print['message']));
  echo json_encode($return); die();}

$save_but_message='';
$preview_url='';

if ($preview==0) {
  $sql="update gks_acc_inv set 
    print_date=now(),
    print_file_name='".$db_link->escape_string($ret_print['save_basename'])."',
    print_file_url='".$db_link->escape_string($ret_print['url_file'])."',
    print_user_id=".$my_wp_user_id.",
    print_inv_state='".$db_link->escape_string($inv_state)."'
    where id_acc_inv=".$id;
  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $sxolio_log = gks_lang('Εκτύπωση').'<br>'.
  gks_lang('Κατάσταση').': <span class="acc_inv_state_'.$inv_state.'">'.getAccInvStateDescr($inv_state).'</span><br>'.
  gks_lang('Αρχείο').': <a href="'.$ret_print['url_file'].'" target="_blank">'.$ret_print['save_basename'].'</a> '.
  '<a href="'.$ret_print['url_file'].'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
  
  $sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
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
  $user_settings['print']['form_id_inv']=  number_format($form_id,0,'','');



  gks_set_user_settings($my_wp_user_id, $user_settings);
  
}



//echo '<pre>'; print_r($ret_print); die();

$return = array('success' => true, 
  'message' => base64_encode('ok'), 
  'save_but_message' => base64_encode($save_but_message),
  'redirect'=> base64_encode('admin-acc-inv-item.php?id='.$id.'&scrollto=gks_print'),
  'preview_url' => $preview_url,
);
echo json_encode($return); die();
