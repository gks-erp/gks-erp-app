<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

ini_set('max_execution_time', 5);
set_time_limit(5);


putenv("ENV=PRODUCTION");

define('SECURE', 1);
//include_once('functions.php');
require_once('_current/_config.php');
//require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-config.php');

require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');

debug_mail(false,'gks_fb_webhook.php','');


if (isset($_GET['hub_mode']) and isset($_GET['hub_challenge']) and isset($_GET['hub_verify_token'])) {
  echo $_GET['hub_challenge'];
  die();
}

$raw_data='';

if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
//delete me
//$HTTP_RAW_POST_DATA='{"entry": [{"id": "119440231087027", "time": 1782663736, "changes": [{"value": {"adgroup_id": "120250549031410414", "ad_id": "120250549031410414", "created_time": 1782663732, "leadgen_id": "1530066778863063", "page_id": "119440231087027", "form_id": "1391684086110361"}, "field": "leadgen"}]}], "object": "page"}';

if ( isset($HTTP_RAW_POST_DATA)) {
  $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
  if ($HTTP_RAW_POST_DATA!='') {
    //parse_str($HTTP_RAW_POST_DATA, $data);
    $data=json_decode($HTTP_RAW_POST_DATA,true);
    if (is_array($data) and isset($data['entry'])) {
      $raw_data=$HTTP_RAW_POST_DATA;
      
      
//Array
//(
//    [entry] => Array
//        (
//            [0] => Array
//                (
//                    [id] => 0
//                    [time] => 1681931379
//                    [changes] => Array
//                        (
//                            [0] => Array
//                                (
//                                    [field] => leadgen
//                                    [value] => Array
//                                        (
//                                            [ad_id] => 444444444
//                                            [form_id] => 444444444444
//                                            [leadgen_id] => 444444444444
//                                            [created_time] => 1681931379
//                                            [page_id] => 444444444444
//                                            [adgroup_id] => 44444444444
//                                        )
//
//                                )
//
//                        )
//
//                )
//
//        )
//
//    [object] => page
//)      
    
      if (isset($data['entry'][0]['changes'][0]['value']['leadgen_id'])) {
        $leadgen_id=$data['entry'][0]['changes'][0]['value']['leadgen_id'];
        $form_id=$data['entry'][0]['changes'][0]['value']['form_id'];
        
        //delete me
        //$leadgen_id=1017370570664693;
        
        $page_access_token='';
        $form_name='Facebook Lead Form';
        $company_id=1;
        $company_sub_id=0;
        $user_lang='en-US';
        $subject='From Facebook Lead Form';
        $assigned_id=0;
        $crm_channel_id=0;
        $crm_channel_campain_id=0;
        $esoda=0;
        $lead_color='';
        
        //$array=[];
        //$array[]=['FORM_ID'=>'11111111111','PAGE_ACCESS_TOKEN'=>'222222222222'];
        //echo '<pre>';echo json_encode($array);die();

        $my_page_title='Facebook webhook lead form';
        db_open();
        stat_record();
        
        $sql="select myvalue from gks_settings where mykey='GKS_FACEBOOK_FORMS_PAGE_TOKENS'";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          echo 'sql error';die();}        
        if ($result->num_rows==0) {
          debug_mail(false,'GKS_FACEBOOK_FORMS_PAGE_TOKENS is not set',$sql);
          echo 'sql error';die();}        
          
        $row = $result->fetch_assoc();
        $myvalue=trim_gks($row['myvalue']);
        if ($myvalue!='') {
          $myvalue=json_decode($myvalue,true);
          if (is_array($myvalue)) {
            foreach($myvalue as $myv) {
              if ($myv['FORM_ID']==$form_id) {
                $page_access_token=$myv['PAGE_ACCESS_TOKEN'];
                
                if (isset($myv['form_name'])) $form_name=$myv['form_name'];
                if (isset($myv['company_id'])) $company_id=$myv['company_id'];
                if (isset($myv['company_sub_id'])) $company_sub_id=$myv['company_sub_id'];
                if (isset($myv['user_lang'])) $user_lang=$myv['user_lang'];
                if (isset($myv['subject'])) $subject=$myv['subject'];
                if (isset($myv['assigned_id'])) $assigned_id=$myv['assigned_id'];
                if (isset($myv['crm_channel_id'])) $crm_channel_id=$myv['crm_channel_id'];
                if (isset($myv['crm_channel_campain_id'])) $crm_channel_campain_id=$myv['crm_channel_campain_id'];
                if (isset($myv['esoda'])) $esoda=$myv['esoda'];
                if (isset($myv['lead_color'])) $lead_color=$myv['lead_color'];

                break;
              }
            }
          }
        }
        if ($page_access_token=='') {
          debug_mail(false,'PAGE_ACCESS_TOKEN for FORM_ID '.$form_id.' not found','');
          echo 'sql error';die();}        
          
        //echo '<pre>';echo $page_access_token;die();
        
        $url='https://graph.facebook.com/v25.0/'.$leadgen_id.'?access_token='.$page_access_token;
        

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        $lead_data = curl_exec($ch);
        curl_close($ch);        
        
        if ($lead_data!=false and strlen($lead_data)>10) {
          $raw_data.="\r\n\r\n".$lead_data;
          
          $lead_data=json_decode($lead_data,true);
          if (isset($lead_data['id']) and isset($lead_data['field_data']) and is_array($lead_data['field_data'])) {
            $created_time=date('Y-m-d H:i:s');
            if (isset($lead_data['created_time'])) {
              $created_time=date('Y-m-d H:i:s',strtotime($lead_data['created_time']));
            }
            $field_data=$lead_data['field_data'];

   
            $f_full_name='';
            $f_phone='';
            $f_email='';
            $f_others=[];
            
            foreach ($field_data as $fd) {
              $myvalue='';
              if (is_array($fd['values'])) {
                $myvalue=implode(', ',$fd['values']);
              } else if (is_string($fd['values'])) {
                $myvalue=$fd['values'];
              }
              
              switch ($fd['name']) {
                case 'full_name':
                  $f_full_name=$myvalue;
                  break;
                case 'phone':
                  $f_phone=$myvalue;
                  break;
                case 'email':
                  $f_email=$myvalue;
                  break;
                default:
                  $f_others[]=$fd['name'].': '.$myvalue;
              }
              
            }
            $f_others=implode("\r\n",$f_others);

            $sqlF='';$sqlV='';

            $sqlF.="mydate_add,";$sqlV.="now(),";
            $sqlF.="mydate_edit,";$sqlV.="now(),";
            $sqlF.="user_id_add,";$sqlV.="2,";
            $sqlF.="user_id_edit,";$sqlV.="2,";
            $sqlF.="myip,";$sqlV.="'".$db_link->escape_string($gkIP)."',";
            $sqlF.="lead_status_id,";$sqlV.="1,";
            $sqlF.="lead_date,";$sqlV.="'".$created_time."',";
            $sqlF.="company_id,";$sqlV.=$company_id.",";
            $sqlF.="company_sub_id,";$sqlV.=$company_sub_id.",";
            $sqlF.="form_id,";$sqlV.=$form_id.",";
            $sqlF.="form_name,";$sqlV.="'".$db_link->escape_string($form_name)."',";

            $sqlF.="user_id,";$sqlV.="0,";
            $sqlF.="user_lang,";$sqlV.="'".$db_link->escape_string($user_lang)."',";
            
            $sqlF.="first_name,";$sqlV.="'".$db_link->escape_string($f_full_name)."',";
            $sqlF.="email,";$sqlV.="'".$db_link->escape_string($f_email)."',";
            $sqlF.="mobile,";$sqlV.="'".$db_link->escape_string($f_phone)."',";
            $sqlF.="subject,";$sqlV.="'".$db_link->escape_string($subject)."',";
            $sqlF.="message,";$sqlV.="'".$db_link->escape_string($f_others)."',";
            
            $sqlF.="assigned_id,";$sqlV.=$assigned_id.",";
            $sqlF.="crm_channel_id,";$sqlV.=$crm_channel_id.",";
            $sqlF.="crm_channel_campain_id,";$sqlV.=$crm_channel_campain_id.",";
            $sqlF.="source_url,";$sqlV.="'".$db_link->escape_string('www.facebook.com')."',";
            $sqlF.="esoda,";$sqlV.=number_format(floatval($esoda),8,'.','').",";
            $sqlF.="lead_color,";$sqlV.="'".$db_link->escape_string($lead_color)."',";


            
            
            $sqlF.="raw_data,";$sqlV.="'".$db_link->escape_string($raw_data)."',";

            $sqlF=substr($sqlF, 0, strlen($sqlF)-1);
            $sqlV=substr($sqlV, 0, strlen($sqlV)-1);

            $sql='insert into gks_crm_leads ('.$sqlF.') values ('.$sqlV.')';

            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              echo 'sql error';die();}  

            $id = $db_link->insert_id; 

            $sxolio=gks_lang('Προσθήκη από Facebook'); 
            $sql="insert into gks_crm_leads_log (crm_lead_id, add_date,user_id,sxolio) values (
            ".$id.",now(),2,'".$db_link->escape_string($sxolio)."')";
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              echo 'sql error';die();}  
           
$mysubject=gks_lang('Νέα Ευκαιρία από το Facebook').' #'.$id;

$message_email=gks_lang('Νέα Ευκαιρία').':<br><a href="'.GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.'">'.GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.'</a><br><br>';

$message_email.='<b>'.gks_lang('Δεδομένα').'</b><br>';
$message_email.=gks_lang('Όνομα').': '.$f_full_name.'<br>';
$message_email.=gks_lang('email').': '.$f_email.'<br>';
$message_email.=gks_lang('Κινητό').': '.$f_phone.'<br>';

$message_email.=gks_lang('Θέμα').': '.$subject.'<br>';
$message_email.=gks_lang('Μήνυμα').': '.str_replace("\r\n", '<br>', $f_others).'<br>';

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
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
  echo json_encode($return); die(); }  
$send_viber=array();
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


$message=gks_lang('Νέα Ευκαιρία από το Facebook').' #'.$id;

$message_notification=gks_lang('Νέα Ευκαιρία από το Facebook').': <a href="'.GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.'">#'.$id.'</a>';

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
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
  echo json_encode($return); die(); }  



$sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
FROM gks_notification_userperm 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
AND gks_notification_userperm.notification_type_id=100 AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
//debug_mail(false,'sql',$sql);
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
  echo json_encode($return); die(); }  
$send_viber=array();
while ($row = $result->fetch_assoc()) {
  $send_viber[]=$row['viber_id'];
}
foreach ($send_viber as $value) {
  $subject = (isset($_POST['subject']) ? "\n".gks_lang('Θέμα').': '.trim_gks($_POST['subject']) : '');
  gks_viber_send('crm_leads',$id,$value,gks_lang('Νέα Ευκαιρία από το Facebook').' #'.$id."\n".GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.$subject);
}

              
            //print '<pre>'.$f_full_name.'|'.$f_phone.'|'.$f_email.'|'.$f_others;die();
            //print '<pre>';print_r($lead_data);die();
            
          }
        }

        
        //die('ggg '.$leadgen_id.' '.$url.' '.$lead_data);
      }
      //echo '<pre>';print_r($data);die();
      
      debug_mail(false,'gks_fb_webhook.php RAW',print_r($data,true));
    
    }
  }
}



echo 'OK'; die();
echo time();
