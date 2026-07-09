<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_voip_user_params() {
  global $gks_user_settings;
  global $db_link;
  $ret=array(
    'can_originatecall'=>false,
    'id_erp_app'=>0,
    'erp_app_name'=>'',
    'extensions'=>[],
    'class_input'=>'',
    'class_span'=>'',
    'html_after_input'=>'',
    'html_after_span'=>'',

    'error'=>'',
    
  );
  if (isset($gks_user_settings)==false or isset($gks_user_settings['voip'])==false or count($gks_user_settings['voip']['extensions'])==0) return $ret;
  $sql="SELECT id_erp_app,erp_app_name
  FROM gks_erp_app
  WHERE erp_app_disabled=0
  and voip_ip<>'' and voip_AIM_port>0 and voip_AIM_username<>'' and voip_AIM_password<>''
  and erp_app_last_ping>date_sub(now(),interval 15 minute)
  and voip_call_originate=1";
  $result = $db_link->query($sql);   
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $ret['error']='sql error';return $ret;}

  if ($result->num_rows==0) return $ret;
  $row = $result->fetch_assoc();
  $ret['id_erp_app']=intval($row['id_erp_app']);
  $ret['erp_app_name']=trim($row['erp_app_name']);
  
  $ret['extensions']=$gks_user_settings['voip']['extensions'];
  $ret['class_input']='gks_voip_originate_input';
  $ret['class_span']='gks_voip_originate_span';
  
  $ret['html_after_input']='<i class="gks_voip_originate_after_input"></i>';
  $ret['html_after_span']='<i class="gks_voip_originate_after_span"></i>';
  
  return $ret;
}
