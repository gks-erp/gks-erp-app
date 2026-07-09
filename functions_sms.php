<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

/*
status
-100 Procheiro
101 Anamoni // gia apostoli me async, tha staloun sto gks Erp App

401 Den vrethike //NOT_FOUND Wrong ID or report has expired
402 Elixe //EXPIRED Messages expired.
403 Stalthike //SENT Message is sent without final delivery report.
404 Paradothike //DELIVERED Message is delivered to recipient
405 Den paradothike //UNDELIVERED Message is undelivered (invalid number, roaming error etc)
406 Apotychia // FAILED Sending message failed – please report it to us SMSAPI
407 Aporifthike // REJECTED Message is undelivered (invalid number, roaming error etc)
408 Agnosto // UNKNOWN No report (message may be either delivered or not)
409 Se oura // QUEUE Message is waiting to be sent
410 Apodechthike // ACCEPTED Message is delivered to operator

1000 Lifthike

*/


function gks_phone_number_fix($prefix,$number) {
  
  $cc=$number;
  $cc=str_replace('+','',$cc);
  $cc=str_replace('(','',$cc);
  $cc=str_replace(')','',$cc);
  $cc=str_replace(' ','',$cc);
  $cc=str_replace('-','',$cc);
  if (ctype_digit($cc)==false) return $number;
  
  $cc=$number;
  $cc=str_replace('(','',$cc);
  $cc=str_replace(')','',$cc);
  $cc=str_replace(' ','',$cc);
  $cc=str_replace('-','',$cc);
  
  if (startwith($cc,'00')) {
    $number_f='+'.substr($cc, 2);
  } else if (startwith($cc,'+')) {
    $number_f=$cc;
  } else {
    $number_f=$prefix.$cc;
  }
  return $number_f;
}


//function mysms
function gks_sms_send($model, $model_id, $from, $to, $szMessageText,$sender_sms_provider,$resend_id=0,$for_async=false) {
  global $db_link;
  global $my_wp_user_id;
  global $GKS_SMS_SENDER;
  global $GKS_SMS_TOKEN;
  global $gks_sms_send_insert_id;

  
  
  //$szMessageText = str_replace('€', 'E', $szMessageText);
  //$szMessageText = str_replace("\r\n", ' ', $szMessageText);
  //$szMessageText = str_replace("\n", ' ', $szMessageText);
  //$szMessageText = str_replace("\r", ' ', $szMessageText);
  $szMessageText = str_replace("\t", ' ', $szMessageText);

  //echo '<pre>'.$to;die();
  $to_f=$to;
  if (startwith($to,'00')) {
    $to_f='+'.substr($to, 2);
  } else if (startwith($to,'+')) {
    $to_f=$to;
  } else {
    $to_f='+30'.$to;
  }
  $to_db=$to_f;
  //if (startwith($to_db,'+')) $to_db=substr($to_db,1);

  $sms_res_status=409;
  $sms_res_status_name=gks_lang('Σε ουρά');
  if ($for_async) {
    $sms_res_status=101;
    $sms_res_status_name=gks_lang('Αναμονή');
  }
  $erp_app_mobile_id=0;
  $erp_app_mobile_cost_per_sms=0;
  
  $sms_res_part_Smscount=1;
  $sms_res_part_OK='';
  $sms_res_part_ID='';
  $sms_result='';


  
  if ($sender_sms_provider=='gks_erp_app_mobile') {
    $erp_app_mobile_id=intval($from);
    
    $sql="select * from gks_erp_app_mobile where id_erp_app_mobile=".$erp_app_mobile_id." and erp_app_mobile_disabled=0 and erp_app_mobile_can_sms=1";
    $myrun = $db_link->query($sql);
    if (!$myrun) {
      debug_mail(false,'warning on mysms error sql',$sql);
      return false;
    }
    
    if ($myrun->num_rows==0) {
      return false;  
    }
    $row=$myrun->fetch_assoc();
    $erp_app_mobile_cost_per_sms=floatval($row['erp_app_mobile_cost_per_sms']);
    $from=$row['erp_app_mobile_country'].$row['erp_app_mobile_phonenumber'];

  } else if ($sender_sms_provider=='smsapi') {
    if ($GKS_SMS_SENDER=='' or $GKS_SMS_TOKEN=='') {
      return false;  
    }    
    if ($from=='') {
     $from=$GKS_SMS_SENDER;
    }    
   
  } else {
    $erp_app_mobile_cost_per_sms=0.04;
  }
  
  $sms_res_part_Message=$szMessageText;
  $sms_res_part_Length=mb_strlen($szMessageText);
  $sms_res_part_Parts=ceil($sms_res_part_Length/160);
  $sms_res_part_cost=$sms_res_part_Parts*$erp_app_mobile_cost_per_sms;

  $sql="insert into gks_sms (sms_provider,erp_app_mobile_id,
  sms_mms_type,sms_folder,
  sms_result,date_add,user_id,model,model_id,message_id,myfrom,myto,Message,Message_post,
  Length,Smscount,Parts,OK,cost,points,myret,
  status,status_name
  ) values (
  '".$db_link->escape_string($sender_sms_provider)."',
  ".$erp_app_mobile_id.",
  'sms','sent',
  '".$db_link->escape_string($sms_result)."',
  now(),
  ".$my_wp_user_id.",
  '".$db_link->escape_string($model)."',
  ".$model_id.",
  '".$db_link->escape_string($sms_res_part_ID)."',
  '".$db_link->escape_string($from)."',
  '".$db_link->escape_string($to_db)."',
  '".$db_link->escape_string($sms_res_part_Message)."',
  '".$db_link->escape_string($szMessageText)."',
  ".$sms_res_part_Length.",
  ".$sms_res_part_Smscount.",
  ".$sms_res_part_Parts.",
  '".$db_link->escape_string($sms_res_part_OK)."',
  ".$sms_res_part_cost.",
  ".$sms_res_part_cost.",";
  if (0 === strpos($sms_res_part_OK, 'OK:')) {
    $sql.='1,';
  } else {
    $sql.='0,';
  }
   $sql.="
   ".$sms_res_status.",
   '".$db_link->escape_string($sms_res_status_name)."')";
   
  
  //debug_mail(false,'insert sms sql',$to.' | '.$sql);
  
  $myrun = $db_link->query($sql);
  if (!$myrun) {
    debug_mail(false,'warning on mysms error sql',$sql);
  }
  $id_rec = $db_link->insert_id;
  $gks_sms_send_insert_id=$id_rec;
  
  
  if ($resend_id>0) {
    $sql_resend="update gks_sms set model='resend', model_id=".$id_rec." where id=".$resend_id;
    $myrun_resend = $db_link->query($sql_resend);
    if (!$myrun_resend) {
      debug_mail(false,'warning on mysms error sql',$sql_resend);
    }
  }
  //echo '<pre>'. $id_rec;die(); 
  
  if ($for_async) {
    return true;
  }
  
  if ($sender_sms_provider=='gks_erp_app_mobile') {
    
        

    
  
    $public_url='';
    if ($row['erp_app_mobile_url']=='frp') {
      if (trim_gks($row['erp_app_mobile_token'])!='') {
        $public_url='http://'.$row['erp_app_mobile_token'].GKS_PROXY['DOMAIN_BASE_NAME'].':'.GKS_PROXY['VPORT'];
      }
    } else {
      if (trim_gks($row['erp_app_mobile_url'])!='' and $row['erp_app_mobile_port']>0) {
        $public_url='http://'.$row['erp_app_mobile_url'].':'.$row['erp_app_mobile_port'];
      }
    }
    if ($public_url=='') {
      $sms_res_part_OK='url error';
      
    } else {
      $rand1=rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
      $semd5=md5($rand1.$row['erp_app_mobile_secret']);

      
      
      
      $public_url.='/sendsms';
      
      $params=array(
        'rand1'=>$rand1,
        'semd5'=>$semd5,
        'rec_id' => $id_rec,
        'to' => $to_f,
        'text' => $szMessageText,
        'user_id' => intval($my_wp_user_id),
        'model' => $model,
        'model_id' => intval($model_id),
      );
      
      $c = curl_init();
      curl_setopt( $c, CURLOPT_URL, $public_url );
      curl_setopt( $c, CURLOPT_POST, true );
      curl_setopt( $c, CURLOPT_POSTFIELDS, $params );
      curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
  
  
      $content = curl_exec( $c );
      $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);
      curl_close( $c );
  
      
      if($http_status != 200){
        $sms_res_part_OK= 'http_status error '.$http_status;
        $sms_result='ERROR:http_status error '.$http_status;
        
      } else {
        //die('<pre>'.$public_url."\n".$content);
        $response_data=json_decode($content,true);
        if ($response_data === null && json_last_error() !== JSON_ERROR_NONE) {
          $sms_res_part_OK='json decode error';
          $sms_result='ERROR:json decode error';
        } else {
          if (isset($response_data['success'])==false) {
            $sms_res_part_OK= 'data response error data';
            $sms_result='ERROR:data response error data';
          } else {
            if ($response_data['success']==false) {
              $sms_res_part_OK=$response_data['message'];
              $sms_result='ERROR:'.$response_data['message'];
            } else {
              $sms_res_part_OK='OK:';
              $sms_result='';
            }
          }
        }
      }
      //die('<pre>ffff '.$sender_sms_provider.' '.$sql);
    }
    
    
     
        
  } else if ($sender_sms_provider=='smsapi') {


    
    $token = $GKS_SMS_TOKEN;
    $params = array(
  //    'test' => 1,
      'idx' => 1,
      'details' => 1,
    //  'single' => 1,
      'datacoding' => 'gsm',
  //    'normalize' => '1',
  //    'encoding' => 'iso-8859-7',
      'encoding' => 'utf-8',
      'to' => $to_f,
      'from' => $from,
    //  'message' => $szMessageText_conv ,
      'message' => $szMessageText,
    );
    
    $sms_result = sms_send_api($params,$token);
    debug_mail(false,'sms_result:',$sms_result);
    //echo $sms_result;
    $sms_res_parts=explode("\n",$sms_result);
    //print_r($sms_res_parts);
    
    $sms_res_part_Message='';
    $sms_res_part_Length=0;
    $sms_res_part_Smscount=0;
    $sms_res_part_Parts=0;
    $sms_res_part_OK='';
    $sms_res_part_ID='';
    $sms_res_part_cost=0;
  
    $my_status=409;
    $my_status_name=gks_lang('Σε ουρά');
            
    if (0 === strpos($sms_result, 'ERROR:')) {
      debug_mail(false,'warning on mysms error:',$sms_result);
    } else {
    
      foreach ($sms_res_parts as $part) {
        if (0 === strpos($part, 'Message: ')) {
          $sms_res_part_Message=substr($part, strlen('Message: '));
        }
        if (0 === strpos($part, 'Length: ')) {
          $sms_res_part_Length=intval(substr($part,  strlen('Length: ')));
        }
        if (0 === strpos($part, 'Sms count: ')) {
          $sms_res_part_Smscount=intval(substr($part, strlen('Sms count: ')));
        }
        if (0 === strpos($part, 'Parts: ')) {
          $sms_res_part_Parts=intval(substr($part, strlen('Parts: ')));
        }
        if (0 === strpos($part, 'OK:')) {
          $sms_res_part_OK = $part;
          $sms_res_part_OK_parts=explode(':',$sms_res_part_OK);
          $sms_res_part_ID=$sms_res_part_OK_parts[1];
          $sms_res_part_cost=$sms_res_part_OK_parts[2];
        }
      }
    }
    
  //  echo '<pre>';
  //  echo $sms_res_part_Message."\r\n";
  //  echo $sms_res_part_Length."\r\n";
  //  echo $sms_res_part_Smscount."\r\n";
  //  echo $sms_res_part_Parts."\r\n";
  //  echo $sms_res_part_OK."\r\n";
  //  echo $sms_res_part_ID."\r\n";
  //  echo $sms_res_part_cost."\r\n";
  
 
  }

  $sql="update gks_sms set
  sms_result='".$db_link->escape_string($sms_result)."',
  message_id='".$db_link->escape_string($sms_res_part_ID)."',
  Message='".$db_link->escape_string($sms_res_part_Message)."',
  Length=".$sms_res_part_Length.",
  Smscount=".$sms_res_part_Smscount.",
  Parts=".$sms_res_part_Parts.",
  OK='".$db_link->escape_string($sms_res_part_OK)."',
  cost=".$sms_res_part_cost.",
  points=".$sms_res_part_cost.",
  myret=".((0 === strpos($sms_res_part_OK, 'OK:')) ?  '1' : '0').",
  status=".$sms_res_status.",
  status_name='".$db_link->escape_string($sms_res_status_name)."'
  where id=".$id_rec." limit 1";
  
  $myrun = $db_link->query($sql);
  if (!$myrun) {
    debug_mail(false,'warning on mysms error sql',$sql);
  } 
      
  //echo '<pre>ddddd'.$http_status.'|'.$sms_res_part_OK; die();

  if (0 === strpos($sms_res_part_OK, 'OK:')) {
    return true;
  } else {
    return false;
  }
}

function sms_send_api($params, $token, $backup = false ) {

    static $content;

    if($backup == true){
        $url = 'https://api2.smsapi.com/sms.do';
    }else{
        $url = 'https://api.smsapi.com/sms.do';
    }

    $c = curl_init();
    curl_setopt( $c, CURLOPT_URL, $url );
    curl_setopt( $c, CURLOPT_POST, true );
    curl_setopt( $c, CURLOPT_POSTFIELDS, $params );
    curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $c, CURLOPT_HTTPHEADER, array(
       "Authorization: Bearer ".$token
    ));

    $content = curl_exec( $c );
    $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);

    if($http_status != 200 && $backup == false){
        $backup = true;
        sms_send_api($params, $token, $backup);
    }

    curl_close( $c );
    return $content;
}

function gks_sms_can_resend_status($status,$model) {
  $status=intval($status);
  //101 Anamoni
  //403 Stalthike
  //406 FAILED, Error - Generic failure
  //409 Se oura
  if (in_array($status,[101,406,409]) and $model!='resend') {
    return true;  
  }
  return false;
}
