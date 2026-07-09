<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
//gks_permission_user_must_login_post();

$return = array('success' => false, 'message' => base64_encode('general error'));
$cmd='';if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);

$my_page_title=gks_lang('License - Εντολή').' '.$cmd;
db_open();
//stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_app_info','edit',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


switch ($cmd) {
  case 'check_status':
    $res=gks_license_get_status();
    if ($res['success']==false) {
      $return['success']=false;
      $return['html']='';
      $return['message'] = base64_encode($res['message']);
      echo json_encode($return); die();      
    }
    
    $html='';
    if (isset($GKS_ERP_APP_PURCHASE_CODE['purchase_codes']) and is_array($GKS_ERP_APP_PURCHASE_CODE['purchase_codes'])) {
      foreach ($GKS_ERP_APP_PURCHASE_CODE['purchase_codes'] as $module => $serial) {       
        $html.='<div class="form-group row">';
          $html.='<div class="col-sm-4 gks_items_col"><div class="gks_serial_cell gks_serial_cell1">'.$serial['type_descr'].'</div></div>';
          $html.='<div class="col-sm-3 gks_items_col"><div class="gks_serial_cell gks_serial_cell2">'.gks_format_serial_number($serial['code']).'</div></div>';
          $html.='<div class="col-sm-3 gks_items_col"><div class="gks_serial_cell gks_serial_cell3">';
          if (!empty($serial['expire_date'])) $html.= showDate(strtotime($serial['expire_date']),'d/m/Y H:i',1);
          $html.='</div></div>';
          $html.='<div class="col-sm-2 gks_items_col"><div class="gks_serial_cell gks_serial_cell4">'.myimg010(($serial['valid'] ? 1 : 0)).'</div></div>';
        $html.='</div>';
      }
    }
          
    $return['success']=true;
    $return['html']=$html;
    $return['message'] = base64_encode('OK');
    echo json_encode($return); die();    
    break;
    

  default:
    debug_mail(false,'cmd error','');
    $return['message'] = base64_encode(gks_lang('Σφάλμα εντολής').'<br>'.gks_lang('Ανανεώστε την σελίδα'));
    echo json_encode($return); die();    
}




$return = array('success' => false, 'message' => base64_encode(gks_lang('Γενικό σφάλμα')));
echo json_encode($return); die();



