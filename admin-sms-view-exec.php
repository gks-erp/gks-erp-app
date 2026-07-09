<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$headers=0; if (isset($_GET['headers'])) $headers=intval($_GET['headers']);


$myid=0; if (isset($_GET['id'])) $myid=intval($_GET['id']);
if ($myid<=0) {
  debug_mail(false,'id is not set','');
  if ($headers==1) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
    echo json_encode($return); die();
  } else {
    die(gks_lang('Δεν έχει ορισθεί το').' ID.');
  }
}  

$my_page_title=gks_lang('Προβολή απεσταλμένου sms').' id:'.$myid.' headers='.$headers;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sms','view',$myid);
if ($headers==1) {
  if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}
} else {
  if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
}



$sql = "SELECT gks_sms.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_erp_app_mobile.erp_app_mobile_name
FROM (gks_sms 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_sms.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_erp_app_mobile ON gks_sms.erp_app_mobile_id = gks_erp_app_mobile.id_erp_app_mobile
where gks_sms.id=".$myid;

$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'sql error',$sql);
  if ($headers==1) {
    $return = array('success' => false, 'message' => base64_encode('SQL Error'));
    echo json_encode($return); die();
  } else {
    die('SQL Error');
  }
}
if ($result->num_rows <= 0) {
  debug_mail(false,'sms record not found',$sql);
  if ($headers==1) {
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή')));
    echo json_encode($return); die();
  } else {
    die(gks_lang('Δεν βρέθηκε η εγγραφή'));
  }
}
$row = $result->fetch_assoc();  

$html='
<h5 align="center" style="padding-top:0px;">SMS</h5>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" 
border="0" cellspacing="0" cellpadding="5" align="center" style="width:100%">
<tbody>
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('ID').'</th>        
    <td nowrap class="mytdcml">'.$row['id'].'</td>   
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Ημερομηνία').'</th>        
    <td nowrap class="mytdcml">'.showDate(strtotime($row['date_add']), 'd/m/Y H:i:s', 1).'</td>   
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Μέσω').'</th>        
    <td nowrap class="mytdcml">';
      if ($row['sms_provider']=='smsapi') $html.= 'smsapi';
      else if ($row['sms_provider']=='gks_erp_app_mobile') {
        $html.= $row['erp_app_mobile_name'];
      }
$html.='</td>
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Χρήστης').'</th>        
    <td nowrap class="mytdcml">'.$row['gks_nickname'].'</td>  
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Από').'</th>        
    <td nowrap class="mytdcml">'.$row['myfrom'].'</td>      
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Προς').'</th>        
    <td nowrap class="mytdcml">'.$row['myto'].'</td>      
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Μήνυμα').'</th>
    <td        class="mytdcml tdtext" style="min-width:100px">'.($row['Message'] != '' ? nl2br_gks($row['Message']) : nl2br_gks($row['Message_post'])).'</td>      
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Φάκελος').'</th>        
    <td nowrap class="mytdcml">'.$row['sms_folder'].'</td>      
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Parts').'</th>        
    <td nowrap class="mytdcml">'.$row['Parts'].'</td>      
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Κόστος').'</th>        
    <td nowrap class="mytdcml">'.((isset($row['cost']) and $row['cost']<>0) ? $row['cost'] : '').'</td>      
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap><span title="'.gks_lang('Αποτέλεσμα αποστολής').'">'.gks_lang('Αποτ.').'</span></th>  
    <td nowrap class="mytdcml"><img src="img/'.$row['myret'].'.png" border="0" width="16"></td>    
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Σφάλμα').'</th>  
    <td nowrap class="mytdcml">';
    if ($row['myret']==0) {
      $sms_result=trim_gks($row['sms_result']);
      if (0 === strpos($sms_result, 'ERROR:'))
        $html.= substr($sms_result,6);
      else
        $html.= $sms_result;
    }
$html.='</td>
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Κατάσταση').'</th>        
    <td        class="mytdcml"><span class="sms_status sms_status_'.$row['status'].'">'.$row['status_name'].'</span></td>
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Ενέργεια').'</th>        
    <td        class="mytdcml">';
      if (gks_sms_can_resend_status($row['status'],$row['model'])) {
        $html.= '<i class="gks_sms_command_resend fas fa-sync-alt tooltipster" title="'.gks_lang('Επαναποστολή').'" data-id="'.$row['id'].'"></i>';  
      }
$html.='</td>
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap><span class="tooltipster" title="'.gks_lang('Ημερομηνία αναφοράς παράδοσης').'">'.gks_lang('Ημερομηνία ΑΠ').'</span></th>        
    <td nowrap class="mytdcml">'.(isset($row['donedate_date']) ? showDate(strtotime($row['donedate_date']), 'd/m/Y H:i:s', 1) : '').'</td>   
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap>'.gks_lang('Μοντέλο').'</th>        
    <td nowrap class="mytdcml">'.$row['model'].'</td>      
  </tr>	
  <tr>	
    <th class="table-dark" scope="col" style="text-align:right !important;" width="0%" nowrap><span class="tooltipster" title="'.gks_lang('Model ID').'">'.gks_lang('mID').'</span></th>
    <td nowrap class="mytdcml">'.$row['model_id'].'</td>      
  </tr>	
<tbody>
</table>';

if ($headers==0) {
  include_once('_my_header_empty.php');
  echo $html;
  include_once('_my_footer_empty.php');
} else {
  $return = array('success' => true, 'message' => base64_encode('OK'), 'html'=> base64_encode($html));
  echo json_encode($return); die();
}
die();
