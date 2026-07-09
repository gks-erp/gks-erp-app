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
$my_page_title=gks_lang('Αποθήκευση Σειράς').' id:' . $id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_seires',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}







if ($id>0) {
  $sql="select * from gks_acc_seires where id_acc_seira=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
}

$acc_journal_id=0; if (isset($_POST['acc_journal_id'])) $acc_journal_id=intval($_POST['acc_journal_id']);
$seira_code=''; if (isset($_POST['seira_code'])) $seira_code=trim_gks(base64_decode($_POST['seira_code']));
$seira_descr=''; if (isset($_POST['seira_descr'])) $seira_descr=trim_gks(base64_decode($_POST['seira_descr']));
$seira_comments=''; if (isset($_POST['seira_comments'])) $seira_comments=trim_gks(base64_decode($_POST['seira_comments']));
$prefix=''; if (isset($_POST['prefix'])) $prefix=trim_gks(base64_decode($_POST['prefix']));
$suffix=''; if (isset($_POST['suffix'])) $suffix=trim_gks(base64_decode($_POST['suffix']));
$number_size=0; if (isset($_POST['number_size'])) $number_size=intval($_POST['number_size']);
$number_step=0; if (isset($_POST['number_step'])) $number_step=intval($_POST['number_step']);
$next_number=0; if (isset($_POST['next_number'])) $next_number=intval($_POST['next_number']);
$sortorder=0; if (isset($_POST['sortorder'])) $sortorder=intval($_POST['sortorder']);
$send_mydata=0; if (isset($_POST['send_mydata'])) $send_mydata=intval($_POST['send_mydata']);
$send_paroxos=0; if (isset($_POST['send_paroxos'])) $send_paroxos=intval($_POST['send_paroxos']);
$seira_need_signature=0; if (isset($_POST['seira_need_signature'])) $seira_need_signature=intval($_POST['seira_need_signature']);
$seira_isdeliverynote=0; if (isset($_POST['seira_isdeliverynote'])) $seira_isdeliverynote=intval($_POST['seira_isdeliverynote']);
$seira_is_reverse_delivery_note=0; if (isset($_POST['seira_is_reverse_delivery_note'])) $seira_is_reverse_delivery_note=intval($_POST['seira_is_reverse_delivery_note']);
$seira_is_self_pricing=0; if (isset($_POST['seira_is_self_pricing'])) $seira_is_self_pricing=intval($_POST['seira_is_self_pricing']);
$seira_is_vat_payment_suspension=0; if (isset($_POST['seira_is_vat_payment_suspension'])) $seira_is_vat_payment_suspension=intval($_POST['seira_is_vat_payment_suspension']);
$aade_lock_send_numbers=0; if (isset($_POST['aade_lock_send_numbers'])) $aade_lock_send_numbers=intval($_POST['aade_lock_send_numbers']);
$is_xeirografi=0; if (isset($_POST['is_xeirografi'])) $is_xeirografi=intval($_POST['is_xeirografi']);
$is_disable=0; if (isset($_POST['is_disable'])) $is_disable=intval($_POST['is_disable']);

if ($send_mydata==0 and $send_paroxos==0) $aade_lock_send_numbers=0;

$payacq_str=''; if (isset($_POST['payacq_str'])) $payacq_str=trim_gks(base64_decode($_POST['payacq_str']));
$payacq_array = json_decode($payacq_str, true);
if ($payacq_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['payacq_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (payacq)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
//echo '<pre>';print_r($payacq_array);die();



$erp_app_id_check=0; if (isset($_POST['erp_app_id_check'])) $erp_app_id_check=intval($_POST['erp_app_id_check']);
$erp_app_filter_val_webpage_computer=0; if (isset($_POST['erp_app_filter_val_webpage_computer'])) $erp_app_filter_val_webpage_computer=intval($_POST['erp_app_filter_val_webpage_computer']);
$erp_app_filter_val_webpage_tablet=0; if (isset($_POST['erp_app_filter_val_webpage_tablet'])) $erp_app_filter_val_webpage_tablet=intval($_POST['erp_app_filter_val_webpage_tablet']);
$erp_app_filter_val_webpage_mobile=0; if (isset($_POST['erp_app_filter_val_webpage_mobile'])) $erp_app_filter_val_webpage_mobile=intval($_POST['erp_app_filter_val_webpage_mobile']);
$erp_app_filter_val_app_with_thermal=0; if (isset($_POST['erp_app_filter_val_app_with_thermal'])) $erp_app_filter_val_app_with_thermal=intval($_POST['erp_app_filter_val_app_with_thermal']);
$erp_app_filter_val_app_no_thermal=0; if (isset($_POST['erp_app_filter_val_app_no_thermal'])) $erp_app_filter_val_app_no_thermal=intval($_POST['erp_app_filter_val_app_no_thermal']);
$erp_app_id=0; if (isset($_POST['erp_app_id'])) $erp_app_id=intval($_POST['erp_app_id']);
$erp_app_dest=''; if (isset($_POST['erp_app_dest'])) $erp_app_dest=trim_gks(base64_decode($_POST['erp_app_dest']));
$erp_app_dest_printer=''; if (isset($_POST['erp_app_dest_printer'])) $erp_app_dest_printer=trim_gks(base64_decode($_POST['erp_app_dest_printer']));
$erp_app_dest_printer_method=0; if (isset($_POST['erp_app_dest_printer_method'])) $erp_app_dest_printer_method=intval($_POST['erp_app_dest_printer_method']);
$erp_app_dest_printer_lpr_ip=''; if (isset($_POST['erp_app_dest_printer_lpr_ip'])) $erp_app_dest_printer_lpr_ip=trim_gks(base64_decode($_POST['erp_app_dest_printer_lpr_ip']));
$erp_app_dest_printer_copies=0; if (isset($_POST['erp_app_dest_printer_copies'])) $erp_app_dest_printer_copies=intval($_POST['erp_app_dest_printer_copies']);
$erp_app_dest_folder=''; if (isset($_POST['erp_app_dest_folder'])) $erp_app_dest_folder=trim_gks(base64_decode($_POST['erp_app_dest_folder']));



if ($erp_app_id_check!=0) $erp_app_id_check=1;
if ($erp_app_id_check==0) {
  $erp_app_id=0;  
  $erp_app_dest='';
  $erp_app_dest_printer='';
  $erp_app_dest_printer_method=0;
  $erp_app_dest_printer_lpr_ip='';
  $erp_app_dest_printer_copies=0;
  $erp_app_dest_folder='';
  $erp_app_filter='';
} else {
  if ($erp_app_id<1) {
    debug_mail(false,'erp_app_id is empty','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε την εφαρμογή gks ERP App Desktop')));
    echo json_encode($return); die(); } 
  
  $erp_app_filter=[];
  if ($erp_app_filter_val_webpage_computer) $erp_app_filter[]='webpage_computer';
  if ($erp_app_filter_val_webpage_tablet) $erp_app_filter[]='webpage_tablet';
  if ($erp_app_filter_val_webpage_mobile) $erp_app_filter[]='webpage_mobile';
  if ($erp_app_filter_val_app_with_thermal) $erp_app_filter[]='app_with_thermal';
  if ($erp_app_filter_val_app_no_thermal) $erp_app_filter[]='app_no_thermal';
  if (count($erp_app_filter)==0) {
    debug_mail(false,'erp_app_filter is empty','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τουλάχιστον ένα φίλτρο στο gks ERP App Desktop')));
    echo json_encode($return); die(); }    
  
  $erp_app_filter=json_encode($erp_app_filter);
  
  $sql="select * from gks_erp_app where id_erp_app=".$erp_app_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows<=0) {
    debug_mail(false,'erp_app_id not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η gks ERP App Desktop')));
    echo json_encode($return); die(); } 

  if ($erp_app_dest!='printer' and $erp_app_dest!='folder') $erp_app_dest='';
  if ($erp_app_dest=='') {
    debug_mail(false,'erp_app_dest is empty','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον Προορισμό')));
    echo json_encode($return); die(); }
  
  if ($erp_app_dest=='printer') {
    $erp_app_dest_folder='';
    
    if ($erp_app_dest_printer_method==0 or $erp_app_dest_printer_method==1) $erp_app_dest_printer_lpr_ip='';
    if ($erp_app_dest_printer_method==2) $erp_app_dest_printer='';
    if ($erp_app_dest_printer_method==3) {$erp_app_dest_printer_lpr_ip=''; $erp_app_dest_printer=''; }
    

    if ($erp_app_dest_printer_method < 0 or $erp_app_dest_printer_method > 3) {
      debug_mail(false,'erp_app_dest_printer_method is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η μέθοδος πρέπει να είναι 0,1,2 ή 3')));
      echo json_encode($return); die(); } 
    
    if ($erp_app_dest_printer=='' and ($erp_app_dest_printer_method==0 or $erp_app_dest_printer_method==1)) {
      debug_mail(false,'erp_app_dest_printer is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον Εκτυπωτή')));
      echo json_encode($return); die(); } 
    if ($erp_app_dest_printer_lpr_ip=='' and $erp_app_dest_printer_method==2) {
      debug_mail(false,'erp_app_dest_printer_lpr_ip is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε την IP του εκτυπωτή')));
      echo json_encode($return); die(); } 
      
      
    if ($erp_app_dest_printer_copies < 1 and $erp_app_dest_printer_copies > 5) {
      debug_mail(false,'erp_app_dest_printer_copies is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Τα αντίτυπα πρέπει να είναι 1,2,3,4 ή 5')));
      echo json_encode($return); die(); } 
    
    //echo '<pre>'. $erp_app_dest_printer;die();    
    
  } else if ($erp_app_dest=='folder') {
    $erp_app_dest_printer='';
    $erp_app_dest_printer_method=0;
    $erp_app_dest_printer_lpr_ip='';
    $erp_app_dest_printer_copies=0;
    
    if ($erp_app_dest_folder=='') {
      debug_mail(false,'erp_app_dest_folder is empty','');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον φάκελο αποστολής')));
      echo json_encode($return); die(); } 
    
    if (endwith($erp_app_dest_folder,'\\')==false) $erp_app_dest_folder.='\\';
    
    $params=array(
      'id' => $erp_app_id,
      'cmd' => 'run_command_folder_exist',
      'postdata' => array (
        'folder' => $erp_app_dest_folder,
        'and_writable' => true,
      ),
    );
    $gks_erp_run_result=gks_erp_app_run_command($params);

    if ($gks_erp_run_result['success']==false) {
      $return = array('success' => false, 'message' => base64_encode($gks_erp_run_result['message']));
      echo json_encode($return); die(); }
    
    

            
    //print '<pre>wwwwwwwwwwwww';print_r($gks_erp_run_result);die();
    
  }
  
}




if ($acc_journal_id<=0) {
  debug_mail(false,'emptyl',                                     gks_lang('Επιλέξτε το Ημερολόγιο'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε το Ημερολόγιο')));
  echo json_encode($return); die();}

if ($seira_code=='') {
  debug_mail(false,'emptyl',                                     gks_lang('Ορίστε τον Κωδικό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον Κωδικό')));
  echo json_encode($return); die();}

$sql="select * from gks_acc_seires where seira_code like '".$db_link->escape_string($seira_code)."' and acc_journal_id=".$acc_journal_id." and id_acc_seira<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η σειρά με κωδικό <b>[1]</b> υπάρχει ήδη για αυτό το ημερολόγιο:<br><a href="admin-acc_seires-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$seira_code,$message);
  $message=str_replace('[2]',$row['id_acc_seira'],$message);
  debug_mail(false,'journal exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}


if ($seira_descr=='') {
  debug_mail(false,'emptyl',                                     gks_lang('Ορίστε την Περιγραφή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Περιγραφή')));
  echo json_encode($return); die();}

$sql="select * from gks_acc_seires where seira_descr like '".$db_link->escape_string($seira_descr)."' and acc_journal_id=".$acc_journal_id." and id_acc_seira<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η σειρά με περιγραφή <b>[1]</b> υπάρχει ήδη για αυτό το ημερολόγιο:<br><a href="admin-acc_seires-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$seira_descr,$message);
  $message=str_replace('[2]',$row['id_acc_seira'],$message);  
  debug_mail(false,'journal exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

if ($number_size < 0 or $number_size > 20) {
  debug_mail(false,'emptyl',                                     gks_lang('Το <b>Πλήθος ψηφίων</b> πρέπει να είναι θετικός ακέραιος αριθμός και μικρότερος το 20'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το <b>Πλήθος ψηφίων</b> πρέπει να είναι θετικός ακέραιος αριθμός και μικρότερος το 20')));
  echo json_encode($return); die();}

if ($number_step < 1) {
  debug_mail(false,'emptyl',                                     gks_lang('Το <b>Βήμα</b> πρέπει να είναι θετικός ακέραιος αριθμός και μεγαλύτερος ή ίσος του 1'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το <b>Βήμα</b> πρέπει να είναι θετικός ακέραιος αριθμός και μεγαλύτερος ή ίσος του 1')));
  echo json_encode($return); die();}

if ($next_number< 1) {
  debug_mail(false,'emptyl',                                     gks_lang('O <b>Επόμενος Αριθμός</b> πρέπει να είναι θετικός ακέραιος αριθμός και μεγαλύτερος ή ίσος του 1'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('O <b>Επόμενος Αριθμός</b> πρέπει να είναι θετικός ακέραιος αριθμός και μεγαλύτερος ή ίσος του 1')));
  echo json_encode($return); die();}

$sql="SELECT id_acc_inv,inv_acc_number_int FROM gks_acc_inv where inv_acc_seira_id=".$id." and inv_acc_number_int>0 order by inv_acc_number_int desc limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
$exist_max_number=0;
if ($result->num_rows==1) {
  $row = $result->fetch_assoc();
  $exist_max_number=intval($row['inv_acc_number_int']);
  $id_acc_inv=intval($row['id_acc_inv']);
  if ($next_number <= $exist_max_number) {
    $message=gks_lang('O <b>Επόμενος Αριθμός</b> δεν μπορεί να είναι μικρότερος ή ίσος με <b>[1]</b> διότι <b>υπάρχει ήδη</b> παραστατικό με αριθμό <a href="admin-acc-inv-item.php?id=[2]" class="gks_link">[3]</a>');
    $message=str_replace('[1]',$exist_max_number,$message);
    $message=str_replace('[2]',$id_acc_inv,$message);   
    $message=str_replace('[3]',$exist_max_number,$message);   
    debug_mail(false,'emptyl',$message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
}

$sql="SELECT gks_acc_seires_auto_numbers.acc_seira_id, gks_acc_seires_auto_numbers.acc_inv_id, gks_acc_seires_auto_numbers.auto_number
FROM gks_acc_seires_auto_numbers 
LEFT JOIN gks_acc_inv ON gks_acc_seires_auto_numbers.acc_inv_id = gks_acc_inv.id_acc_inv
WHERE gks_acc_seires_auto_numbers.acc_seira_id=".$id."
AND gks_acc_inv.id_acc_inv Is Not Null
ORDER BY gks_acc_seires_auto_numbers.auto_number DESC, gks_acc_seires_auto_numbers.acc_inv_id DESC limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
$exist_max_number=0;
if ($result->num_rows==1) {
  $row = $result->fetch_assoc();
  $exist_max_number=intval($row['auto_number']);
  $id_acc_inv=intval($row['acc_inv_id']);
  if ($next_number <= $exist_max_number) {
    $message=gks_lang('O <b>Επόμενος Αριθμός</b> δεν μπορεί να είναι μικρότερος ή ίσος με <b>[1]</b> διότι αυτός ο αριθμός έχει <b>δεσμευτεί</b> για το παραστατικό με αριθμό <a href="admin-acc-inv-item.php?id=[2]" class="gks_link">[3]</a>');
    $message=str_replace('[1]',$exist_max_number,$message);
    $message=str_replace('[2]',$id_acc_inv,$message);   
    $message=str_replace('[3]',$exist_max_number,$message);  
        
    debug_mail(false,'emptyl',                                     $message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
}

if ($send_mydata==1 and $send_paroxos==1) {
  debug_mail(false,'emptyl',                                     gks_lang('Θα πρέπει να επιλέξετε ή αποστολή στο myData ή σε πάροχο ή κανέναν'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Θα πρέπει να επιλέξετε ή αποστολή στο myData ή σε πάροχο ή κανέναν')));
  echo json_encode($return); die();}
    
  
if ($send_paroxos==0 && $seira_need_signature!=0) {
  debug_mail(false,'emptyl',                                     gks_lang('Δεν μπορεί να είναι ενεργή η <b>Απαιτείται υπογραφή από πάροχο</b> εφόσον δεν είναι ενεργή <b>Αποστολή σε Πάροχο</b>'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να είναι ενεργή η <b>Απαιτείται υπογραφή από πάροχο</b> εφόσον δεν είναι ενεργή <b>Αποστολή σε Πάροχο</b>')));
  echo json_encode($return); die();}  
  

$sql="SELECT gks_acc_journal.id_acc_journal, 
gks_acc_journal.journal_has_correlated_invoices,
gks_acc_journal.journal_has_multiple_connected_marks,
gks_acc_journal.journal_has_packings_declarations,
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_aade_code
FROM gks_acc_journal 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_acc_journal.id_acc_journal=".$acc_journal_id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows==0) {   
  debug_mail(false,'emptyl',                                     gks_lang('Δεν βρέθηκε το ημερολόγιο με ID').' '.$acc_journal_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το ημερολόγιο με ID').' '.$acc_journal_id));
  echo json_encode($return); die();}    

$row = $result->fetch_assoc();
$id_acc_eidos_parastatikou=trim_gks($row['id_acc_eidos_parastatikou']);
$eidos_parastatikou_aade_code=trim_gks($row['eidos_parastatikou_aade_code']);
$journal_has_correlated_invoices=intval($row['journal_has_correlated_invoices']);
$journal_has_multiple_connected_marks=intval($row['journal_has_multiple_connected_marks']);
$journal_has_packings_declarations=intval($row['journal_has_packings_declarations']);

if ($eidos_parastatikou_aade_code=='' and in_array($id_acc_eidos_parastatikou,[702,703,704])==false) {
  if ($send_mydata!=0) {
    debug_mail(false,'emptyl',                                     gks_lang('Αυτό το ημερολόγιο δεν μπορεί να αποσταλεί στο myData'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το ημερολόγιο δεν μπορεί να αποσταλεί στο myData')));
    echo json_encode($return); die();}     

  if ($send_paroxos!=0) {
    debug_mail(false,'emptyl',                                     gks_lang('Αυτό το ημερολόγιο δεν μπορεί να αποσταλεί στον Πάροχο'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αυτό το ημερολόγιο δεν μπορεί να αποσταλεί στον Πάροχο')));
    echo json_encode($return); die();}     
}

if ($send_paroxos==0 and $seira_need_signature!=0) {
  debug_mail(false,'emptyl',                                     gks_lang('Εφόσον αυτή η σειρά δεν θα <b>αποσταλεί στον Πάροχο</b> δεν μπορεί να <b>Απαιτείται υπογραφή από πάροχο</b>'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον αυτή η σειρά δεν θα <b>αποσταλεί στον Πάροχο</b> δεν μπορεί να <b>Απαιτείται υπογραφή από πάροχο</b>')));
  echo json_encode($return); die();}  


//if ($send_mydata==0 and $send_paroxos==0 and $seira_isdeliverynote!=0) {
//  debug_mail(false,'emptyl',                                     gks_lang('Εφόσον αυτή η σειρά δεν θα <b>αποσταλεί στο myData ή στον Πάροχο</b> δεν μπορεί να έχει <b>Ένδειξη Παραστατικού Διακίνησης για ΑΑΔΕ</b>'));
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον αυτή η σειρά δεν θα <b>αποσταλεί στο myData ή στον Πάροχο</b> δεν μπορεί να έχει <b>Ένδειξη Παραστατικού Διακίνησης για ΑΑΔΕ</b>')));
//  echo json_encode($return); die();}  

if ($eidos_parastatikou_aade_code=='9.3' and ($send_mydata!=0 or $send_paroxos!=0) and $seira_isdeliverynote==0) {
  debug_mail(false,'emptyl',                                     gks_lang('Εφόσον αυτή η σειρά είναι για παραστατικό διακίνησης και θα σταλεί στο myData ή στον Πάροχο θα πρέπει να έχει ενεργοποιημένη την <b>Ένδειξη Παραστατικού Διακίνησης για ΑΑΔΕ</b>'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον αυτή η σειρά είναι για παραστατικό διακίνησης και θα σταλεί στο myData ή στον Πάροχο θα πρέπει να έχει ενεργοποιημένη την <b>Ένδειξη Παραστατικού Διακίνησης για ΑΑΔΕ</b>')));
  echo json_encode($return); die();}  
//echo '<pre>'.$id_acc_eidos_parastatikou.'|'.$eidos_parastatikou_aade_code;die();
if ($send_mydata!=0 and in_array($id_acc_eidos_parastatikou,[702,703,704])==false and in_array($eidos_parastatikou_aade_code,['1.1','1.2','1.3','1.4','1.5','1.6','2.1','2.2','2.3','2.4','3.1','3.2','4','5.1','5.2','6.1','6.2','7.1','8.1','8.2','8.3','8.4','8.5','8.6','9.1','9.2','9.3','10.1','10.2','11.1','11.2','11.3','11.4','11.5','12','13.1','13.2','13.3','13.4','13.30','13.31','14.1','14.2','14.3','14.4','14.5','14.30','14.31','15.1','16.1','17.1','17.2','17.3','17.4','17.5','17.6'])==false) {
  $message=gks_lang('Το ημερολόγιο τύπου [1] δεν μπορεί να αποσταλεί στο myData');
  $message=str_replace('[1]',$eidos_parastatikou_aade_code,$message);
  debug_mail(false,'emptyl',                                     $message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();}    
//echo '<pre>'.$id_acc_eidos_parastatikou.'|'.$eidos_parastatikou_aade_code.'|'.$journal_has_correlated_invoices;die();
if (($send_mydata!=0 or $send_paroxos!=0) and in_array($eidos_parastatikou_aade_code,['9.1','10.1']) and $journal_has_correlated_invoices<>1) {
  debug_mail(false,'emptyl',                                     gks_lang('Εφόσον το συγκεκριμένο ημερολόγιο είναι <b>Συσχετιζόμενο</b> θα πρέπει να έχει ενεργοποιημένο το <b>Συσχετιζόμενα Παραστατικά</b> για να μπορεί να αποσταλεί myData/Πάροχο'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εφόσον το συγκεκριμένο ημερολόγιο είναι <b>Συσχετιζόμενο</b> θα πρέπει να έχει ενεργοποιημένο το <b>Συσχετιζόμενα Παραστατικά</b> για να μπορεί να αποσταλεί myData/Πάροχο')));
  echo json_encode($return); die();}  

if ($send_paroxos!=0 and in_array($id_acc_eidos_parastatikou,[702,703,704])) {
  debug_mail(false,'emptyl',                                     gks_lang('Οι πάροχοι δεν υποστηρίζουν το Ακυρωτικό Παραστατικό ή Δελτίο'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Οι πάροχοι δεν υποστηρίζουν το Ακυρωτικό Παραστατικό ή Δελτίο')));
  echo json_encode($return); die();}   

//delete me, mporei na paei to 9.3 ston paroxo ?   
if ($send_paroxos!=0 and in_array($eidos_parastatikou_aade_code,['1.1','1.2','1.3','1.4','1.5','1.6','2.1','2.2','2.3','2.4','3.1','3.2','5.1','5.2','6.1','6.2','7.1','8.1','8.2','8.4','8.5','9.1','9.2','9.3','10.1','10.2','11.1','11.2','11.3','11.4','11.5'])==false) {
  $message=gks_lang('Το ημερολόγιο τύπου [1] δεν μπορεί να αποσταλεί στον Πάροχο');
  $message=str_replace('[1]',$eidos_parastatikou_aade_code,$message);
  debug_mail(false,'emptyl',                                     $message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();}    
  
  

$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_acc_seires');

$redirect='';
if ($id==-1) {
  $sql="insert into gks_acc_seires (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-acc_seires-item.php?id='.$id); 
}






$sql="update gks_acc_seires set 
acc_journal_id=".$acc_journal_id.",
seira_code='".$db_link->escape_string($seira_code)."',
seira_descr='".$db_link->escape_string($seira_descr)."',
seira_comments='".$db_link->escape_string($seira_comments)."',
prefix='".$db_link->escape_string($prefix)."',
suffix='".$db_link->escape_string($suffix)."',
number_size=".$number_size.",
number_step=".$number_step.",
next_number=".$next_number.",
sortorder=".$sortorder.",
send_mydata=".$send_mydata.",
send_paroxos=".$send_paroxos.",
seira_need_signature=".$seira_need_signature.",
seira_isdeliverynote=".$seira_isdeliverynote.",
seira_is_reverse_delivery_note=".$seira_is_reverse_delivery_note.",
seira_is_self_pricing=".$seira_is_self_pricing.",
seira_is_vat_payment_suspension=".$seira_is_vat_payment_suspension.",

aade_lock_send_numbers=".$aade_lock_send_numbers.",
is_xeirografi=".$is_xeirografi.",
is_disable=".$is_disable.",
erp_app_id=".$erp_app_id.",
erp_app_filter='".$db_link->escape_string($erp_app_filter)."',
erp_app_dest='".$db_link->escape_string($erp_app_dest)."',
erp_app_dest_printer='".$db_link->escape_string($erp_app_dest_printer)."',
erp_app_dest_printer_method=".$erp_app_dest_printer_method.",
erp_app_dest_printer_lpr_ip='".$db_link->escape_string($erp_app_dest_printer_lpr_ip)."',
erp_app_dest_printer_copies=".$erp_app_dest_printer_copies.",
erp_app_dest_folder='".$db_link->escape_string($erp_app_dest_folder)."',

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_acc_seira = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }



$sql="delete from gks_acc_seires_paymentacquirers where acc_seira_id=".$id;  
if (count($payacq_array)>0) {
  $sql.=" and payment_acquirer_id not in (".implode(',',$payacq_array).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }

$sql="select payment_acquirer_id from gks_acc_seires_paymentacquirers where acc_seira_id=".$id;
$result_select = $db_link->query($sql);        
if (!$result_select) {
  debug_mail(false,'admin-users-item.php error sql',$sql);
  die('sql error');}
$exist_ids=array();
while ($row_select = $result_select->fetch_assoc()) {
  $exist_ids[]=intval($row_select['payment_acquirer_id']);
}

foreach ($payacq_array as $paid) {
  if (in_array($paid,$exist_ids)==false) {
    $sql="insert into gks_acc_seires_paymentacquirers (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    acc_seira_id,payment_acquirer_id
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$id.",".$paid."
    )";
    $result_select = $db_link->query($sql);        
    if (!$result_select) {
      debug_mail(false,'admin-users-item.php error sql',$sql);
      die('sql error');}
    
  }
} 
  



$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);
gks_lang_data_obj_save_exec_php('gks_acc_seires',$id);


$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







