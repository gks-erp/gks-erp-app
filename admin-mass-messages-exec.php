<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Μαζική Αποστολή Viber-SMS-email - Αποστολή');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_mass_messages','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$isrealsend=0; if (isset($_POST['isrealsend'])) $isrealsend=intval($_POST['isrealsend'])==1;
$sender_sms_provider='';if (isset($_POST['sender_sms_provider'])) $sender_sms_provider=trim_gks(base64_decode($_POST['sender_sms_provider']));
if ($sender_sms_provider=='' or ($sender_sms_provider!='gks_erp_app_mobile' and $sender_sms_provider!='smsapi')) {
  debug_mail(false,'sender_sms_provider is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποστολέα').' (2)'));
  echo json_encode($return); die();}

$sender_sms_sender='';if (isset($_POST['sender_sms_sender'])) $sender_sms_sender=trim_gks(base64_decode($_POST['sender_sms_sender']));
if ($sender_sms_sender=='') {
  debug_mail(false,'sender_sms_sender is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποστολέα').' (3)'));
  echo json_encode($return); die();}



$mymessage=''; if (isset($_POST['message'])) $mymessage=trim(base64_decode($_POST['message']));
if ($mymessage=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε κάποιο μήνυμα')));
  echo json_encode($return); die();}

$send_with_viber=0; if (isset($_POST['send_with_viber'])) $send_with_viber=intval($_POST['send_with_viber']);
$send_with_sms=0; if (isset($_POST['send_with_sms'])) $send_with_sms=intval($_POST['send_with_sms']);
$send_with_email=0; if (isset($_POST['send_with_email'])) $send_with_email=intval($_POST['send_with_email']);

$send_with_email_from=trim(base64_decode($_POST['send_with_email_from']));
$send_with_email_template=intval($_POST['send_with_email_template']);
$send_with_email_subject=trim(base64_decode($_POST['send_with_email_subject']));

if ($send_with_viber==0 and $send_with_sms==0 and $send_with_email==0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τουλάχιστον ένα τρόπο αποστολής')));
  echo json_encode($return); die();}
  
if ($send_with_email==1 and $send_with_email_from=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε το <b>Από email<b>')));
  echo json_encode($return); die();}

if ($send_with_email==1 and $send_with_email_template==0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε το <b>Πρότυπο email<b>')));
  echo json_encode($return); die();}

if ($send_with_email==1 and $send_with_email_subject=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε το <b>Θέμα email<b>')));
  echo json_encode($return); die();}




$viberbuttons=''; if (isset($_POST['viberbuttons'])) $viberbuttons=trim(base64_decode($_POST['viberbuttons']));
//$viberbuttons='yes
//no
//Maybe';





ini_set('max_execution_time', 10000);
set_time_limit(10000);



if ($isrealsend) {
  $mylist_str=trim(base64_decode($_POST['mylist_str']));
  $mylist_array = json_decode($mylist_str, true);
  if ($mylist_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['mylist_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  if (count($mylist_array)==0) {
    debug_mail(false,'mylist_array is empty','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποιους αποδέκτες')));
    echo json_encode($return); die();}
  foreach ($mylist_array as &$value) {
    $value=intval($value); 
  }
  unset($value);


  
  $sql="SELECT ID, gks_nickname, 
  viber_id,viber_subscribed,user_email,gks_mobile
  from ".GKS_WP_TABLE_PREFIX."users
  where ID in (".implode(',',$mylist_array).")
  order by gks_nickname";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  $mydata=array();
  while ($row = $result->fetch_assoc()) {
    $gks_mobile=trim_gks($row['gks_mobile']);
    $gks_mobile=str_replace(' ', '', $gks_mobile);
    $gks_mobile=str_replace('*', '', $gks_mobile);
    $gks_mobile=str_replace('-', '', $gks_mobile);
    $gks_mobile=str_replace('(', '', $gks_mobile);
    $gks_mobile=str_replace(')', '', $gks_mobile);
    $gks_mobile=str_replace('#', '', $gks_mobile);
    $row['gks_mobile']=$gks_mobile;
    
    $mydata[]=$row;
  }

} else {
  
  $mylist_array=[];
  $mydata=[];
  
  $test_send_viber=''; if (isset($_POST['test_send_viber'])) $test_send_viber=trim(base64_decode($_POST['test_send_viber']));
  $test_send_email=''; if (isset($_POST['test_send_email'])) $test_send_email=trim(base64_decode($_POST['test_send_email']));
  
  /*
Array (
    [ID] => 31288
    [gks_nickname] => Kostas Goutoudis
    [viber_id] => 
    [viber_subscribed] => 0
    [user_email] => 
    [gks_mobile] => 6971881406
)  
*/
  if ($send_with_sms==1) {
    $test_send_sms='';   if (isset($_POST['test_send_sms']))   $test_send_sms=trim(base64_decode($_POST['test_send_sms']));
    $parts=explode(']][[',$test_send_sms);
    foreach ($parts as $value) {
      $pp=explode('|',$value);
      if (count($pp)==3) {
        $gks_mobile=trim_gks($pp[0]);
        $gks_mobile=str_replace(' ', '', $gks_mobile);
        $gks_mobile=str_replace('*', '', $gks_mobile);
        $gks_mobile=str_replace('-', '', $gks_mobile);
        $gks_mobile=str_replace('(', '', $gks_mobile);
        $gks_mobile=str_replace(')', '', $gks_mobile);
        $gks_mobile=str_replace('#', '', $gks_mobile);
        
        $gks_nickname=trim_gks($pp[1]);
        $user_id=trim_gks($pp[2]);
        $user_id=str_replace('(', '', $user_id);
        $user_id=str_replace(')', '', $user_id);
        $user_id=str_replace('#', '', $user_id);
        if (strlen($gks_mobile)>=5 and ctype_digit(str_replace('+','',$gks_mobile))) {
          $mydata[]=array(
            'ID' => $user_id,
            'gks_nickname' => $gks_nickname,  
            'viber_id' => '',  
            'viber_subscribed' => 0,  
            'user_email' => '',  
            'gks_mobile' => $gks_mobile,             
          );
        }
      } else if (count($pp)==1) {
        $value=trim_gks($value);
        $value=str_replace(' ', '', $value);
        $value=str_replace('*', '', $value);
        $value=str_replace('-', '', $value);
        $value=str_replace('(', '', $value);
        $value=str_replace(')', '', $value);
        $value=str_replace('#', '', $value);
        if (strlen($value)>=5 and ctype_digit(str_replace('+','',$value))) {
          $mydata[]=array(
            'ID' => 0,
            'gks_nickname' => $value,  
            'viber_id' => '',  
            'viber_subscribed' => 0,  
            'user_email' => '',  
            'gks_mobile' => $value,             
          );
        }
      }
    } 
  }
  
  if ($send_with_viber==1) {
    $test_send_viber='';   if (isset($_POST['test_send_viber']))   $test_send_viber=trim(base64_decode($_POST['test_send_viber']));
    $parts=explode(']][[',$test_send_viber);
    foreach ($parts as $value) {
      $pp=explode('|',$value);
      if (count($pp)==2) {
        $gks_nickname=trim_gks($pp[0]);
        $user_id=trim_gks($pp[1]);
        $user_id=str_replace('(', '', $user_id);
        $user_id=str_replace(')', '', $user_id);
        $user_id=str_replace('#', '', $user_id);
        if (intval($user_id)>0) {
          $sql="SELECT viber_id
          from ".GKS_WP_TABLE_PREFIX."users
          where ID in (".$user_id.")
          and viber_id<>'' and viber_subscribed=1
          order by gks_nickname";
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die();}
          if ($result->num_rows==1) {
            $row = $result->fetch_assoc();
            $mydata[]=array(
              'ID' => $user_id,
              'gks_nickname' => $gks_nickname,  
              'viber_id' => $row['viber_id'],  
              'viber_subscribed' => 1,  
              'user_email' => '',  
              'gks_mobile' => '',             
            );
          }
        }
      }
    } 
  }
    
  if ($send_with_email==1) {
    $test_send_email='';   if (isset($_POST['test_send_email']))   $test_send_email=trim(base64_decode($_POST['test_send_email']));
    $parts=explode(']][[',$test_send_email);
    foreach ($parts as $value) {
      $pp=explode('|',$value);
      if (count($pp)==3) {
        $gks_email=trim_gks($pp[0]);
        $gks_nickname=trim_gks($pp[1]);
        $user_id=trim_gks($pp[2]);
        $user_id=str_replace('(', '', $user_id);
        $user_id=str_replace(')', '', $user_id);
        $user_id=str_replace('#', '', $user_id);
        if (strlen($gks_email)>=5 and filter_var($gks_email, FILTER_VALIDATE_EMAIL)) {
          $mydata[]=array(
            'ID' => $user_id,
            'gks_nickname' => $gks_nickname,  
            'viber_id' => '',  
            'viber_subscribed' => 0,  
            'user_email' => $gks_email,  
            'gks_mobile' => '',             
          );
        }
      } else if (count($pp)==1) {
        $value=trim_gks($value);
        if (strlen($value)>=5 and filter_var($value, FILTER_VALIDATE_EMAIL)) {
          $mydata[]=array(
            'ID' => 0,
            'gks_nickname' => $value,  
            'viber_id' => '',  
            'viber_subscribed' => 0,  
            'user_email' => $value,  
            'gks_mobile' => '',             
          );
        }
        
      }
    } 
  }  

}




//echo '<pre>';print_r($mydata);die();


$re_text=$mymessage;
$re_text_sms=$mymessage;
$re_text_email_body=$mymessage;
$re_text_email_body=str_replace("\r\n",'<br>',$re_text_email_body);
$re_text_email_body=str_replace("\n",'<br>',$re_text_email_body);
$re_text_email_body=str_replace("\r",'<br>',$re_text_email_body);
$re_text_email_subject=$send_with_email_subject;

$out='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">';
$out.=
'<thead>'.
'<tr>'.
  '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">'.gks_lang('A/A').'</th>'.
  '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">'.gks_lang('ID').'</th>'.
  '<th class="table-dark" scope="col" style="text-align: left   !important;" width="30%" nowrap="nowrap">'.gks_lang('Όνομα').'</th>'.
  '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">'.gks_lang('Αποστολή με').'</th>'.
  '<th class="table-dark" scope="col" style="text-align: left   !important;" width="50%" nowrap="nowrap">'.gks_lang('Κείμενο').'</th>'.
  '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">'.gks_lang('Αποτ.').'</th>'.
'</tr>
<thead>
<tbody>';

if ($isrealsend) {
  $sql="insert into gks_mass_messages (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  date_send_start,
  sender_sms_provider,
  sender_sms_sender,
  mymessage,
  send_with_viber,
  send_with_sms,
  send_with_email,
  viber_from,
  email_from,
  email_subject,
  email_template_id,
  mylist
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  now(),
  '".$db_link->escape_string($sender_sms_provider)."',
  '".$db_link->escape_string($sender_sms_sender)."',
  '".$db_link->escape_string($mymessage)."',
  ".$send_with_viber.",
  ".$send_with_sms.",
  ".$send_with_email.",
  '".$db_link->escape_string($GKS_VIBER_URI)."',
  '".$db_link->escape_string($send_with_email_from)."',
  '".$db_link->escape_string($send_with_email_subject)."',
  '".$db_link->escape_string($send_with_email_template)."',
  '".$db_link->escape_string(json_encode($mylist_array))."'
  )";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $id_mass_message=$db_link->insert_id;
} else {
  $id_mass_message=0;
}

$viberbuttons=str_replace("\r\n",']][[',$viberbuttons);
$viberbuttons=str_replace("\n",']][[',$viberbuttons);
$viberbuttons=str_replace("\r",']][[',$viberbuttons);
$parts=explode(']][[',$viberbuttons);

$myButtons=[];
$myButtons_db=[];
foreach ($parts as $value) {
  $value=trim($value);
  if ($value!='') {
    $pp=explode('|',$value);
    $b_desc=$pp[0];
    $b_colorb='#006744';
    if (count($pp)>=2) $b_colorb=$pp[1];
    $b_colorf='#ffffff';
    if (count($pp)>=3) $b_colorf=$pp[2];
    
    
    $myButtons[]=array (
  		'Columns' => 2,
  		'Rows' =>  1,    	
  		'ActionType' => 'reply',
      'ActionBody' => 'massmessage|'.$id_mass_message.'|'.$b_desc,
      'Text' => '<font color="'.$b_colorf.'">'.$b_desc.'</font>',
      'TextSize' => 'small',
      'BgColor' => $b_colorb
    ); 
    $myButtons_db[]=array (
      'desc' => $b_desc,
      'colorb' => $b_colorb,
      'colorf' => $b_colorf,
    );
    
  }
} 


//echo '<pre>';print_r($myButtons); die();

if (count($myButtons)==0) {
  $mykeyboard=array();
} else {
  
  
  
  $myButtons[]=array (
		'Columns' => 2,
		'Rows' =>  1,
		'ActionType' => 'reply',
    'ActionBody' => 'arxiki',
    'Text' => '<font color="#ffffff">'.gks_lang('Αρχική').'</font>',
    'TextSize' => 'small',
    'BgColor' => '#f26660'
  );
  
  $mykeyboard = array (
  	'Type' => 'keyboard',
    'DefaultHeight' => True,
    'Buttons' => $myButtons,
  );  
  
}

$i=0;
$cc_all=0;
$cc_viber=0;
$cc_sms=0;
$cc_email=0;
$cc_none=0;
$myresult=[];
$sms_for_gks_async_queue=0;

foreach ($mydata as $row) {

  $i++;$cc_all++;
  $id= intval($row['ID']);
  $user_email=trim($row['user_email']);
  $gks_mobile=trim($row['gks_mobile']);
  $sender_id=trim($row['viber_id']);
  $viber_subscribed=intval($row['viber_subscribed']);
  $send_with='';

  $myitem=array(
    'id' => $id,
    'email' => $user_email,
    'mobile' => $gks_mobile,
    'v_se' => $sender_id,
    'v_su' => $viber_subscribed,
    'with'=> '',
    'res' => 0,
  );
  
  

  $td_text='';
  if ($send_with_viber==1 and $sender_id!='' && $viber_subscribed!=0) {
    $send_with='viber';$cc_viber++;
    //$myjson=gks_my_viber($sender_id,$id,$re_text,$mykeyboard);
    $myjson=gks_viber_send('mass',$id_mass_message,$sender_id,$re_text,$mykeyboard);
    if (isset($myjson['message_token'])) {
      $myitem['vmt']=$myjson['message_token'];
      $res=1;
    } else {
      $res=0;
    }
  } else if ($send_with_sms==1 and $gks_mobile!='' and startwith($gks_mobile, '69') and strlen($gks_mobile)==10) {
    $send_with='sms';$cc_sms++;
    $mymessage=$re_text_sms;
    //$res=mysms('mass',$id_mass_message,$myfrom,$gks_mobile,$mymessage);
    //echo '<pre>|'.$gks_mobile.'|'.$sender_sms_sender.'|'.$sender_sms_provider.'|';die();
    if ($isrealsend) {
      $res=gks_sms_send('mass',$id_mass_message,$sender_sms_sender,$gks_mobile,$mymessage,$sender_sms_provider,0,true);
      $sms_for_gks_async_queue++;
    } else {
      $res=gks_sms_send('mass',$id_mass_message,$sender_sms_sender,$gks_mobile,$mymessage,$sender_sms_provider); 
    }
    if ($res) {
      $res=1;
    } else {
      $res=0;
    }

  } else if ($send_with_email==1 and $user_email!='') {
    $send_with='email';$cc_email++;
        
    $message=$re_text_email_body;
    $message=$re_text_email_subject;
    
    $replaces=array();
    $replaces[] = array('[[message]]', $re_text_email_body);
    $Attachments=array();
    
    
    $params=array(
      'model'=>'mass',
      'model_id'=>$id_mass_message,
      'from'=>$send_with_email_from,
      'to'=>$user_email,
      'subject'=>$re_text_email_subject,
      'template'=>$send_with_email_template,
      'replaces'=>$replaces,
      'Attachments'=>$Attachments,
      //'EmbeddedImages'=>$EmbeddedImages,
      'force_template'=>true,
      //'ispreview'=>$ispreview,
    );
    $send_email_res=gks_mymail_template($params);
        
    //$send_email_res = mymail_template('mass', $id_mass_message, '', '', '', '', $user_email, '', $re_text_email_subject,'default.html', $replaces,$Attachments);
    if ($send_email_res) {
      $res=1;
    } else {
      $res=0;
    }
  } else {
    $send_with='none';$cc_none++;
    $res=0;
  }
  
  $myitem['with']=$send_with;
  $myitem['res']=$res;

  $myresult[]=$myitem;
  
  $out.=
  '<tr>'.
    '<th scope="row" nowrap="" class="mytdcm aa">'.$i.'</th>'.
    '<td class="mytdcm">'.$id.'</td> '.
    '<td class="mytdcml">'.
      ($id>0 ? 
      '<a href="'.GKS_SITE_URL.'my/admin-users-item.php?id='.$id.'">'.$row['gks_nickname'].'</a>' : 
      $row['gks_nickname']).
    '</td>'.
    '<td class="mytdcm">'.$send_with.'</td>'.
    '<td class="mytdcml">'.($send_with=='sms' ? $re_text_sms : $re_text).'</td>'.
    '<td class="mytdcm">'.$res.'</td>'.
  '</tr>';

} 

if ($isrealsend) {
  $sql="update gks_mass_messages set 
  date_send_end=now(),
  myresult='".$db_link->escape_string(json_encode($myresult))."',
  mybuttons='".$db_link->escape_string(json_encode($myButtons_db))."',
  cc_all=".$cc_all.",
  cc_viber=".$cc_viber.",
  cc_sms=".$cc_sms.",
  cc_email=".$cc_email.",
  cc_none=".$cc_none."
  where id_mass_message=".$id_mass_message;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
}

$out.='</tbody></table>';

$out='<div style="text-align: center;margin-bottom: 20px;">'.
($isrealsend ? 
'<a href="admin-mass-messages-item.php?id='.$id_mass_message.'">'.gks_lang('Προβολή αναφοράς').' #'.$id_mass_message.'</a>' :
gks_lang('Αναφορά δοκιμαστικής αποστολής')).
'</div>'.$out;


if ($sms_for_gks_async_queue>0) {
  $GKS_SMS_MASS_CHUNK_SIZE=10;
  $sql_sms="select myvalue from gks_settings where mykey='GKS_SMS_MASS_CHUNK_SIZE'";
  $result_sms = $db_link->query($sql_sms);
  if (!$result_sms) {debug_mail(false,'sql error',$sql_sms.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}
  //echo $sql;die();
  if ($result_sms->num_rows==1) {
    $row_sms=$result_sms->fetch_assoc();
    $GKS_SMS_MASS_CHUNK_SIZE=intval($row_sms['myvalue']);
    if ($GKS_SMS_MASS_CHUNK_SIZE<1) $GKS_SMS_MASS_CHUNK_SIZE=10;
  }
  
  $guid=guid_for_async_queue();
  $chucks=ceil($sms_for_gks_async_queue/$GKS_SMS_MASS_CHUNK_SIZE);

  $sql_values=[];
  for ($i = 1; $i <= $chucks; $i++) {
  
    $sql_values[]= "(
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    '".$db_link->escape_string($guid)."','mass','pending','send','".$db_link->escape_string($id_mass_message)."','".$db_link->escape_string($GKS_SMS_MASS_CHUNK_SIZE)."'
    )";
    if (count($sql_values)>=250) {
      $sql="insert into gks_async_queue (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      guid,mytype,status,cmd,param1,param2
      ) values ".
      implode(',',$sql_values);
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); } 
      
      $sql_values=[];
    }
  }
  if (count($sql_values)>0) {
    $sql="insert into gks_async_queue (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    guid,mytype,status,cmd,param1,param2
    ) values ".
    implode(',',$sql_values);
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); } 
  }
  
  gks_curl_post_async(GKS_SITE_URL.'my/cron_async_queue.php',array('guid' =>$guid));
}


$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headers .= 'From: debug@gks.gr' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";


mail('kostas@gks.gr', gks_lang('Μαζική Αποστολή Viber-SMS-email'), $out,$headers);

$return = array('success' => true, 'message' => base64_encode('OK'),'out' => $out);
echo json_encode($return); die();
