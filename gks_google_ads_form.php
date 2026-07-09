<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');

//https://www.gks.gr/my/gks_google_ads_form.php

//https://developers.google.com/google-ads/webhook/docs/implementation

/*

{
  "lead_id": "TeSter-123-ABCDEFGHIJKLMNOPQRSTUVWXYZ-abcdefghijklmnopqrstuvwxyz-0123456789-AaBbCcDdEeFfGgHhIiJjKkLl",
  "user_column_data": [
    {
      "column_name": "Full Name",
      "string_value": "Kostas Goutoudis",
      "column_id": "FULL_NAME"
    },
    {
      "column_name": "User Phone",
      "string_value": "+306971881406",
      "column_id": "PHONE_NUMBER"
    }
  ],
  "api_version": "1.0",
  "form_id": 106736310474,
  "campaign_id": 20648090804,
  "google_key": "gkskey",
  "is_test": true,
  "gcl_id": "TeSter-123-ABCDEFGHIJKLMNOPQRSTUVWXYZ-abcdefghijklmnopqrstuvwxyz-0123456789-AaBbCcDdEeFfGgHhIiJjKkLl",
  "adgroup_id": 20000000000,
  "creative_id": 30000000000
}
*/

//echo 'OK'; die();


if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
if (isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
$data=json_decode($HTTP_RAW_POST_DATA, true);

debug_mail(false,'OK google ads form',print_r($data,true));




if (isset($data['lead_id'])==false or isset($data['user_column_data'])==false) {
  debug_mail(false,'SPAM google ads form',$HTTP_RAW_POST_DATA);
  echo 'error';
  die();  
}




$my_page_title=gks_lang('Αποθήκευση Φόρμας από Google Ads Form');
db_open();
stat_record();

$ip=$gkIP;
if (isset($_POST['ip']) and trim_gks($_POST['ip'])!='') $ip=trim_gks($_POST['ip']);
$user_id=2;

$sqlF='';$sqlV='';
$sqlF.="mydate_add,";$sqlV.="now(),";
$sqlF.="mydate_edit,";$sqlV.="now(),";
$sqlF.="user_id_add,";$sqlV.=$user_id.",";
$sqlF.="user_id_edit,";$sqlV.=$user_id.",";
$sqlF.="myip,";$sqlV.="'".$db_link->escape_string($ip)."',";
$sqlF.="lead_status_id,";$sqlV.="1,";
$sqlF.="lead_date,";$sqlV.="now(),";
$sqlF.="subject,";$sqlV.="'Google Ads Form',";
$sqlF.="raw_data,";$sqlV.="'".$db_link->escape_string(json_encode($HTTP_RAW_POST_DATA))."',";
//if (isset($data['form_id'])) {
//  $sqlF.="form_id,";$sqlV.=intval($data['form_id']).","; //einai mono gia avada form
//}

$other_form_data=array();
foreach ($data['user_column_data'] as $value) {
  if (isset($value['column_id'])) {
    $val_str="'',"; if (isset($value['string_value'])) $val_str="'".$db_link->escape_string(trim_gks($value['string_value']))."',";
    
    switch ($value['column_id']) {   
      case 'FULL_NAME':       $sqlF.='first_name,';   $sqlV.=$val_str;break;  
      case 'FIRST_NAME':      $sqlF.='first_name,';   $sqlV.=$val_str;break;  
      case 'LAST_NAME':       $sqlF.='last_name,';    $sqlV.=$val_str;break;  
      case 'EMAIL':           $sqlF.='email,';        $sqlV.=$val_str;break;  
      case 'PHONE_NUMBER':    $sqlF.='mobile,';       $sqlV.=$val_str;break;  
      case 'POSTAL_CODE':     $sqlF.='tk,';           $sqlV.=$val_str;break;  
      case 'STREET_ADDRESS':  $sqlF.='odos,';         $sqlV.=$val_str;break;  
      case 'CITY':            $sqlF.='poli,';         $sqlV.=$val_str;break;  
        
      default:
        $temp='';
        if (isset($value['column_name'])) $temp=trim_gks($value['column_name']);
        if (isset($value['string_value'])) $temp.=($temp=='' ? '' : ': ').trim_gks($value['string_value']);
        
        if ($temp!='') $other_form_data[]=$temp;
        
        break;
    }
  }
} 

if (count($other_form_data)>0)  {
  $sqlF.="message,";$sqlV.="'".$db_link->escape_string(implode("\r\n",$other_form_data))."',";
}

$sqlF=substr($sqlF, 0, strlen($sqlF)-1);
$sqlV=substr($sqlV, 0, strlen($sqlV)-1);


$sql='insert into gks_crm_leads ('.$sqlF.') values ('.$sqlV.')';

$result = $db_link->query($sql);  
if (!$result) {debug_mail(false,'error sql',$sql);echo 'sql error'; die(); }  

$id = $db_link->insert_id; 


$sxolio=gks_lang('Προσθήκη από Google Ads Form'); 
if (isset($data['form_id'])) $sxolio.='<br>form_id: '.trim_gks($data['form_id']);
if (isset($data['campaign_id'])) $sxolio.='<br>campaign_id: '.trim_gks($data['campaign_id']);
if (isset($data['adgroup_id'])) $sxolio.='<br>adgroup_id: '.trim_gks($data['adgroup_id']);
if (isset($data['creative_id'])) $sxolio.='<br>creative_id: '.trim_gks($data['creative_id']);
if (isset($data['google_key'])) $sxolio.='<br>google_key: '.trim_gks($data['google_key']);
if (isset($data['lead_id'])) $sxolio.='<br>lead_id: '.trim_gks($data['lead_id']);


$sql="insert into gks_crm_leads_log (crm_lead_id, add_date,user_id,sxolio) values (
".$id.",now(),".$user_id.",'".$db_link->escape_string($sxolio)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);echo 'sql error'; die(); }  



require_once('vendor_inc/Nicer.php');

$raw_file='<!DOCTYPE html><html dir="ltr" lang="en-US"><head>
		<link rel="stylesheet" type="text/css" href="'.GKS_SITE_URL.'my/vendor_inc/nice_r.css?v='.$gks_cache_version.'"/>
		<script type="text/javascript" src="'.GKS_SITE_URL.'my/vendor_inc/nice_r.js?v='.$gks_cache_version.'"></script>
	</head><body>';
        $obj_nicer = new Nicer($data, true, true);
        $raw_file.=$obj_nicer->render(false);
        $raw_file.='<div id="raw_print_r_b" onclick="raw_toggle()">RAW Print_r</div>';
        $raw_file.='<div style="display:none;" id="raw_print_r"><pre>';
        $raw_file.=print_r($data,true);
        $raw_file.='</pre></div>';
$raw_file.='</body>
</html>'; 
$file_html=GKS_FileServerShare.'crm/lead/'.$id.'/';
if (file_exists($file_html)==false) {
  if (@mkdir($file_html , 0755, true) == false ) {
    debug_mail(false,'can not create dir: ',$file_html);
    //die('error');
  }
}
if (file_exists($file_html)) {
  $file_html.='raw_data_'.showDate(time(),'Y_m_d_H_i_s',1).'_'.rand(1000,9999).rand(1000,9999).'.html';
  file_put_contents($file_html,$raw_file);
}



$mysubject=gks_lang('Νέα Ευκαιρία από Google Ads Form').' #'.$id;

$message_email=gks_lang('Νέα Ευκαιρία').':<br><a href="'.GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.'">'.GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.'</a><br><br>';
$message_email.=gks_lang('Από').': Google Ads Form<br><br>';
if (count($other_form_data)>0)  {
  $message_email.='<b>'.gks_lang('Δεδομένα').':</b><br>';
  $message_email.=implode('<br>',$other_form_data);
}

$message_email.='<br><br><b>Raw data:</b><br><pre>'.print_r($data, true).'<,pre>';

$replaces=array();
$replaces[] = array('[[message]]', $message_email);

$params=array(
  'model'=>'crm_leads',
  'model_id'=>$id,
  'to'=>$GKS_SITE_EMAIL,
  'subject'=>$mysubject,
  'template'=>3, //'empty.html',
  'replaces'=>$replaces,
);
$send_email_res = gks_mymail_template($params);

$sql="SELECT ".GKS_WP_TABLE_PREFIX."users.user_email
FROM gks_notification_userperm 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE ".GKS_WP_TABLE_PREFIX."users.user_email<>''
AND gks_notification_userperm.notification_type_id=100 AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_email=1".gks_notification_userperm_internal_users();
//debug_mail(false,'sql',$sql);
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);echo 'sql error'; die(); }  
while ($row = $result->fetch_assoc()) {
  if ($row['user_email']!=$GKS_SITE_EMAIL) {
    $params=array(
      'model'=>'crm_leads',
      'model_id'=>$id,
      'to'=>$row['user_email'],
      'subject'=>$mysubject,
      'template'=>3, //'empty.html',
      'replaces'=>$replaces,
    );
    $send_email_res = gks_mymail_template($params);
  }
}



$message=gks_lang('Νέα Ευκαιρία από Google Ads Form').' #'.$id;

$message_notification=gks_lang('Νέα Ευκαιρία από Google Ads Form').': <a href="'.GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.'">#'.$id.'</a>';

$sql="insert into gks_notification (
message,for_user_id,`date_add`,for_date,has_ok,model,model_id
)
select
'".$db_link->escape_string($message_notification)."' as message,
user_id as for_user_id,
now() as `date_add`,
now() as `for_date`,
0 as has_ok,
'crm_leads' as model,
".$id." as model_id
from gks_notification_userperm where notification_type_id=100 and from_admin=1 and from_user=1".gks_notification_userperm_internal_users();
//from ".GKS_WP_TABLE_PREFIX."users where gks_wp_capabilities like '%administrator%' or gks_wp_capabilities like '%adminmy%';";

$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);echo 'sql error'; die(); }  
  
$sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
FROM gks_notification_userperm 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
AND gks_notification_userperm.notification_type_id=100 AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
//debug_mail(false,'sql',$sql);
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);echo 'sql error'; die(); }  
$send_viber=array();
while ($row = $result->fetch_assoc()) {
  $send_viber[]=$row['viber_id'];
}
foreach ($send_viber as $value) {
  gks_viber_send('crm_leads',$id,$value,gks_lang('Νέα Ευκαιρία από Google Ads Form').' #'.$id."\n".GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id);
}



die('OK');

