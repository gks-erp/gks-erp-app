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
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$page='';if (isset($_POST['page'])) $page=trim_gks(base64_decode($_POST['page']));
$doc_table='';$prev_doc_table='';
if ($page=='/my/admin-acc-inv-item.php') $doc_table='gks_acc_inv';
if ($page=='/my/admin-acc-pay-item.php') $doc_table='gks_acc_pay';
if ($page=='/my/admin-whi-mov-item.php') $doc_table='gks_whi_mov';

//$xxx
if ($doc_table=='gks_acc_inv') {
  $xxx='inv_acc'; $xxxx='inv';
} else if ($doc_table=='gks_acc_pay') {
  $xxx='pay_acc'; $xxxx='pay';
} else if ($doc_table=='gks_whi_mov') {
  $xxx='mov_whi'; $xxxx='mov';
} else {
  echo '<pre>error on doc_table-page'; die();
}


$my_page_title=gks_lang('Εντολές σχετικές με πάροχο Παραστατικού').' id: '.$id.' xxx: '.$xxx;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, $doc_table,'edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');



if ($doc_table=='gks_acc_inv') {
  $sql=select_gks_acc_inv()." where id_acc_inv=".$id; 
} else if ($doc_table=='gks_acc_pay') {
  $sql=select_gks_acc_pay()." where id_acc_pay=".$id; 
} else if ($doc_table=='gks_whi_mov') {
  $sql=select_gks_whi_mov()." where id_whi_mov=".$id; 
}

if (count($perm_id_company_ids)>0) $sql.=" and     ".$doc_table.".company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and ".$doc_table.".company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and ".$doc_table.".".$xxx."_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and   ".$doc_table.".".$xxx."_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
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
$row = $result->fetch_assoc();
$xxxx_state=trim_gks($row[$xxxx.'_state']);
$aade_paroxos_id=intval($row['aade_paroxos_id']);
$paroxos_status=intval($row['paroxos_status']);
$print_ffff_state=trim_gks($row['print_'.$xxxx.'_state']);
$print_file_name=trim_gks($row['print_file_name']);
$paroxos_send_pdf=trim_gks($row['paroxos_send_pdf']);

$aade_invoiceuid=trim_gks($row['aade_invoiceuid']);
$aade_invoicemark=trim_gks($row['aade_invoicemark']);
$aade_qrurl=trim_gks($row['aade_qrurl']);
$aade_paroxos_qrurl=trim_gks($row['aade_paroxos_qrurl']);
$aade_statuscode=trim_gks($row['aade_statuscode']);
$aade_send_date=trim_gks($row['aade_send_date']);
$aade_sending=trim_gks($row['aade_sending']);





$mycmd=''; if (isset($_POST['mycmd'])) $mycmd=trim_gks($_POST['mycmd']);

switch ($mycmd) {   
  case 'paroxos_status_check':
    if ($xxxx_state<>'090ekdosi' and $xxxx_state<>'100payment') {
      
      if ($doc_table=='gks_acc_inv') $message=gks_lang('H κατάσταση του παραστατικού πρέπει να είναι').' <b>'.getAccInvStateDescr('090ekdosi').'</b> '.gks_lang('ή').' <b>'.getAccInvStateDescr('100payment').'</b>';
      if ($doc_table=='gks_acc_pay') $message=gks_lang('H κατάσταση του παραστατικού πρέπει να είναι').' <b>'.getAccPayStateDescr('090ekdosi').'</b>';
      if ($doc_table=='gks_whi_mov') $message=gks_lang('H κατάσταση του παραστατικού πρέπει να είναι').' <b>'.getWhiMovStateDescr('090ekdosi').'</b> '.gks_lang('ή').' <b>'.getWhiMovStateDescr('100closed').'</b>';
      
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($aade_paroxos_id==0) {
      $message=gks_lang('Το συγκεκριμένο παραστατικό δεν έχει σταλεί σε πάροχο');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($paroxos_status!=0) {
      $message=gks_lang('Το συγκεκριμένο παραστατικό δεν είναι σε κατάσταση <b>Ολοκληρώθηκε</b> στον πάροχο');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    
    $ret=gks_paroxos_invoice_xml_get_in_progress($doc_table,[$id],[],true);
    $ret['message']=base64_encode($ret['message']);
    echo json_encode($ret); die();
    break;

  case 'paroxos_files_check':
    if ($aade_paroxos_id==0) {
      $message=gks_lang('Το συγκεκριμένο παραστατικό δεν έχει σταλεί σε πάροχο');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    $ret=gks_paroxos_invoice_xml_get_files($doc_table,[$id],[],true);
    $ret['message']=base64_encode($ret['message']);
    echo json_encode($ret); die();
    break;
    
  case 'paxoros_send_pdf':
    if ($doc_table=='gks_acc_inv' and $xxxx_state<>'090ekdosi' and $xxxx_state<>'100payment') {
      $message=gks_lang('H κατάσταση του παραστατικού πρέπει να είναι').' <b>'.getAccInvStateDescr('090ekdosi').'</b> '.gks_lang('ή').' <b>'.getAccInvStateDescr('100payment').'</b>';
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($doc_table=='gks_acc_pay' and $xxxx_state<>'090ekdosi') {
      $message=gks_lang('H κατάσταση του παραστατικού πρέπει να είναι').' <b>'.getAccPayStateDescr('090ekdosi').'</b>';
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($doc_table=='gks_whi_mov' and $xxxx_state<>'090ekdosi' and $xxxx_state<>'100closed') {
      $message=gks_lang('H κατάσταση του παραστατικού πρέπει να είναι').' <b>'.getWhiMovStateDescr('090ekdosi').'</b> '.gks_lang('ή').' <b>'.getWhiMovStateDescr('100closed').'</b>';
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($aade_paroxos_id==0) {
      $message=gks_lang('Το συγκεκριμένο παραστατικό δεν έχει σταλεί σε πάροχο');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($paroxos_status!=1) {
      $message=gks_lang('Το συγκεκριμένο παραστατικό δεν είναι σε επεξεργασία');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }

    if ($paroxos_send_pdf!='') {
      $message=gks_lang('Το αρχείο pdf έχει ήδη αποσταλεί στον πάροχο');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($print_file_name=='') {
      $message=gks_lang('Δεν έχει δημιουργηθεί η εκτύπωση, το αρχείο pdf');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if (endwith($print_file_name,'.pdf')==false) {
      $message=gks_lang('Η εκτύπωση δεν είναι αρχείο τύπου <b>pdf</b>').'<br>'.gks_lang('Δημιουργήστε ξανά την εκύπωση σε αρχείο pdf και ξαναδοκιμάστε');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message));
      echo json_encode($return); die();      
    }


    if ($doc_table=='gks_acc_inv' and $print_ffff_state<>'090ekdosi' and $print_ffff_state<>'100payment') {
      $message=gks_lang('H κατάσταση του παραστατικού δεν ήταν').' <b>'.getAccInvStateDescr('090ekdosi').'</b> '.gks_lang('ή').' <b>'.getAccInvStateDescr('100payment').'</b> '.gks_lang('όταν δημιουργήθηκε η εκτύπωση').'<br>'.gks_lang('Δημιουργήστε ξανά την εκύπωση και ξαναδοκιμάστε');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message));
      echo json_encode($return); die();      
    }
    if ($doc_table=='gks_acc_pay' and $print_ffff_state<>'090ekdosi') {
      $message=gks_lang('H κατάσταση του παραστατικού δεν ήταν').' <b>'.getAccPayStateDescr('090ekdosi').'</b> '.gks_lang('όταν δημιουργήθηκε η εκτύπωση').'<br>'.gks_lang('Δημιουργήστε ξανά την εκύπωση και ξαναδοκιμάστε');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message));
      echo json_encode($return); die();      
    }
    
    $ret=gks_paroxos_invoice_xml_send_pdf($doc_table,[$id],[],true);
    $ret['message']=base64_encode($ret['message']);
    echo json_encode($ret); die();
    break;

  case 'paroxos_get_docstate':
    //Transmission failure
    if ($doc_table=='gks_acc_inv' and $xxxx_state<>'090ekdosi' and $xxxx_state<>'100payment') {
      $message=gks_lang('H κατάσταση του παραστατικού πρέπει να είναι').' <b>'.getAccInvStateDescr('090ekdosi').'</b> '.gks_lang('ή').' <b>'.getAccInvStateDescr('100payment').'</b>';
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($doc_table=='gks_acc_pay' and $xxxx_state<>'090ekdosi') {
      $message=gks_lang('H κατάσταση του παραστατικού πρέπει να είναι').' <b>'.getAccPayStateDescr('090ekdosi').'</b>';
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($doc_table=='gks_whi_mov' and $xxxx_state<>'090ekdosi' and $xxxx_state<>'100closed') {
      $message=gks_lang('H κατάσταση του παραστατικού πρέπει να είναι').' <b>'.getWhiMovStateDescr('090ekdosi').'</b> '.gks_lang('ή').' <b>'.getWhiMovStateDescr('100closed').'</b>';
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($aade_paroxos_id==0) {
      $message=gks_lang('Το συγκεκριμένο παραστατικό δεν έχει σταλεί σε πάροχο');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($aade_send_date=='' or $aade_invoiceuid=='' or $aade_paroxos_qrurl=='') {
      $message=gks_lang('Το παραστατικό δεν έχει σταλεί στον πάροχο');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    if ($aade_invoicemark!='') {
      $message=gks_lang('Το παραστατικό έχει ήδη ΜΑΡΚ');
      debug_mail(false,$message,$sql);
      $return = array('success' => false, 'message' =>  base64_encode($message.'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();      
    }
    
    $ret=gks_paroxos_invoice_get_docstate($doc_table,[$id],[],true);
    $ret['message']=base64_encode($ret['message']);
    echo json_encode($ret); die();
    //echo '<pre>sssssssssssss';die();
    break;
 
  default:
    debug_mail(false,gks_lang('Δεν βρέθηκε η εντολή'),$mycmd);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εντολή').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();
    break;  
}

echo '<pre>asdfasdasdas';die();

