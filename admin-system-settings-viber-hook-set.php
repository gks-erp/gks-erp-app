<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$my_page_title=gks_lang('Ορισμός σελίδας Viber Hook Page');
db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {
  debug_mail(false,'admin-deny',$_SERVER['HTTP_REFERER']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν επιτρέπεται η πρόσβαση')));
  echo json_encode($return); die();
}

$token=''; if (isset($_POST['GKS_VIBER_TOKEN'])) $token=trim_gks(base64_decode($_POST['GKS_VIBER_TOKEN']));

if ($token=='') {
  debug_mail(false,'token is empty','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Token')));
  echo json_encode($return); die();
}

$url = 'https://chatapi.viber.com/pa/set_webhook';
$jsonData='{ "auth_token": "'.$token.'", "url": "'.GKS_SITE_URL.'my/admin-viber_webhook_page.php", "send_name": true,"send_photo": true }';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

//echo '<pre>';print $response; die();


$viber_response=json_decode($response,true);
//response: {"status":0,"status_message":"ok","chat_hostname":"SN-CHAT-02_","event_types":["subscribed","unsubscribed","webhook","conversation_started","client_status","action","delivered","failed","message","seen"]}
//Array
//(
//    [status] => 0
//    [status_message] => ok
//    [chat_hostname] => SN-CHAT-09_
//    [event_types] => Array
//        (
//            [0] => subscribed
//            [1] => unsubscribed
//            [2] => webhook
//            [3] => conversation_started
//            [4] => client_status
//            [5] => action
//            [6] => delivered
//            [7] => failed
//            [8] => message
//            [9] => seen
//        )
//)
if (!(is_array($viber_response) 
   and isset($viber_response['status']) 
   and $viber_response['status']==0 
   and isset($viber_response['status_message']) 
   and $viber_response['status_message']=='ok')) {
    
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα κατά τον ορισμό της σελίδας').':<br>'.
    (is_array($viber_response) ? '<pre>'.print_r($viber_response,true).'</pre>': $response)
  ));
  echo json_encode($return); die();    
}


$sql="replace into gks_settings (mykey,myvalue) values ('gks_data_viber_hook_page_response','".$db_link->escape_string($response)."')";
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  

$return = array('success' => true, 'message' => base64_encode(gks_lang('Επιτυχής ορισμός σελίδας')));
echo json_encode($return); die();


$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($viber_response,true)));
echo json_encode($return); die();
