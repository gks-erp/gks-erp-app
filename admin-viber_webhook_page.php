<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


/*
from:
my_viber($sender_id,$re_text, $buttons_newuser1
to:
gks_viber_send($model, $model_id, $receiver, $mytext, $mykeyboard=array(), $is_file = false, $file_size = 0, $file_name = '',$id_rec)
gks_viber_send('hook',0,'xxxxxxxx','mplampla',[],false,0,'',$id_rec)
*/

define('SECURE', 1);
include_once('functions.php');
$my_page_title=gks_lang('Viber Hook Page');

$welcome_message1 =
gks_lang('Καλωσήρθατε στο viber bot της [1]!')."\n".
gks_lang('Μπορείτε να δείτε αναλυτικά όλα τα προϊόντα και τις υπηρεσίες μας στο [1]')."\n".
gks_lang('Μην διστάσετε να επικοινωνήσετε μαζί μας.')."\n".
gks_lang('Μέσω email: [2]')."\n".
gks_lang('Θα χαρούμε να σας εξυπηρετήσουμε.');
$welcome_message1=str_replace('[1]',$GKS_OFFICIAL_SITE_URL,$welcome_message1);
$welcome_message1=str_replace('[2]',$GKS_SITE_EMAIL,$welcome_message1);

$welcome_message2 = gks_lang('Εάν έχετε λογαριασμό στο [1], μπορείτε να συνδεθείτε κάνοντας κλικ στο κουμπί *Σύνδεση*');
$welcome_message2=str_replace('[1]',$GKS_OFFICIAL_SITE_URL,$welcome_message2);

$welcome_message3 =gks_lang('Αυτοματοποιημένη απάντηση').': '.
gks_lang('Μπορείτε να δείτε αναλυτικά όλα τα προϊόντα και τις υπηρεσίες μας στο [1]')."\n".
gks_lang('Μην διστάσετε να επικοινωνήσετε μαζί μας.')."\n".
gks_lang('Μέσω email: [2]')."\n".
gks_lang('Θα χαρούμε να σας εξυπηρετήσουμε.');
$welcome_message3=str_replace('[1]',$GKS_OFFICIAL_SITE_URL,$welcome_message3);
$welcome_message3=str_replace('[2]',$GKS_SITE_EMAIL,$welcome_message3);

$homebutton = array (
			'Columns' => 2,
			'Rows' =>  1,    	
			'ActionType' => 'reply',
      'ActionBody' => 'arxiki',
      'Text' => '<font color="#ffffff">'.gks_lang('Αρχική').'</font>',
      'TextSize' => 'small',
      'BgColor' => '#f26660'
    );
$only_home = array (
	'Type' => 'keyboard',
  'DefaultHeight' => True,
  'Buttons' => array($homebutton),
  );

$pin_buttons = array (
	'Type' => 'keyboard',
  'DefaultHeight' => True,
  'Buttons' => array(
			array ('Columns' => 2,
  			'Rows' =>  1,    	
  			'ActionType' => 'reply',
        'ActionBody' => 'arxiki',
        'Text' => '<font color="#ffffff">'.gks_lang('Αρχική').'</font>',
        'TextSize' => 'small',
        'BgColor' => '#f26660'
      ),
			array ('Columns' => 2,
  			'Rows' =>  1,    	
  			'ActionType' => 'reply',
        'ActionBody' => 'setnewpin1',
        'Text' => '<font color="#ffffff">'.gks_lang('Ορισμός νέου PIN').'</font>',
        'TextSize' => 'small',
        'BgColor' => '#374300'
      ),      
    ),  
  
);

$buttons_home = array (
	'Type' => 'keyboard',
  'DefaultHeight' => True,
  'Buttons' => array (
  
  	array (
			'Columns' => 2,
			'Rows' =>  1,    	
			'ActionType' => 'reply',
      'ActionBody' => 'profil',
      'Text' => '<font color="#000000">'.gks_lang('Προφίλ').'</font>',
      'TextSize' => 'small',
      'BgColor' => '#9f96b1'
    ),
		array ('Columns' => 2,
			'Rows' =>  1,    	
			'ActionType' => 'reply',
      'ActionBody' => 'getpin',
      'Text' => '<font color="#ffffff">'.gks_lang('PIN').'</font>',
      'TextSize' => 'small',
      'BgColor' => '#374300'
    ), 

  	array (
			'Columns' => 2,
			'Rows' =>  1,    	
			'ActionType' => 'reply',
      'ActionBody' => 'time',
      'Text' => '<font color="#ffffff">'.gks_lang('Ώρα').'</font>',
      'TextSize' => 'small',
      'BgColor' => '#f26660'
    ),
  	array (
			'Columns' => 2,
			'Rows' =>  1,    	
			'ActionType' => 'reply',
      'ActionBody' => '?',
      'Text' => '<font color="#ffffff">'.gks_lang('Βοήθεια').'</font>',
      'TextSize' => 'small',
      'BgColor' => '#f26660'
    ),



        
  )
);
  
$buttons_newuser1 = array (
	'Type' => 'keyboard',
  'DefaultHeight' => True,
  'Buttons' => array (
  	array (
			'Columns' => 3,
			'Rows' =>  1,    	
			'ActionType' => 'reply',
      'ActionBody' => 'login',
      'Text' => '<font color="#ffffff">'.gks_lang('Σύνδεση').'</font>',
      'TextSize' => 'small',
      'BgColor' => '#f26660'
    ),
  ),
);     
$buttons_newuser2 = array (
	'Type' => 'keyboard',
  'DefaultHeight' => True,
  'Buttons' => array (
  	array (
			'Columns' => 3,
			'Rows' =>  1,    	
			'ActionType' => 'reply',
      'ActionBody' => 'welcome',
      'Text' => '<font color="#ffffff">'.gks_lang('Αρχική').'</font>',
      'TextSize' => 'small',
      'BgColor' => '#f26660'
    ),
  ),
);   


db_open();
stat_record();
	
$request = file_get_contents("php://input");
$input = json_decode($request, true);
if (isset($input['event'])==false) {
  debug_mail(false,'viber else',print_r($input, true));
  die();
}

if($input['event'] == 'webhook') {
  debug_mail(false,'viber webhook','');
  $webhook_response['status']=0;
  $webhook_response['status_message']="ok";
  $webhook_response['event_types']='delivered';
  echo json_encode($webhook_response);

  die();
} else if($input['event'] == "conversation_started"){
  // when a conversation is started
  
  //Array
  //(
  //    [event] => conversation_started
  //    [timestamp] => 1619305229971
  //    [chat_hostname] => SN-414_
  //    [message_token] => 5566879304647055191
  //    [type] => open
  //    [user] => Array
  //        (
  //            [id] => WKp5AScR4V5fqOmGeNleWg==
  //            [name] => Subscriber
  //            [avatar] => http://dl-media.viber.com/1/share/2/long/bots/generic-avatar%402x.png
  //            [language] => el-GR
  //            [country] => GR
  //            [api_version] => 10
  //        )
  //
  //    [subscribed] => 
  //)

  debug_mail(false,'viber conversation_started',print_r($input, true),'','');
  $message_token=trim_gks($input['message_token']);
  $type = trim_gks($input['type']); //type of message received (text/picture)
  $text=gks_lang('Εγγραφή χρήστη');
  $sender_id = $input['user']['id']; //unique viber id of user who sent the message
  $sender_name='';if (isset($input['user']['name'])) $sender_name = trim_gks($input['user']['name']); //name of the user who sent the message
  $sender_avatar='';if (isset($input['user']['avatar'])) $sender_avatar = trim_gks($input['user']['avatar']); //name of the user who sent the message
  $sender_language='';if (isset($input['user']['language'])) $sender_language = trim_gks($input['user']['language']); //name of the user who sent the message
  $sender_country='';if (isset($input['user']['country'])) $sender_country = trim_gks($input['user']['country']); //name of the user who sent the message

  $sql="SELECT gks_nickname, ID, viber_id, gks_wp_capabilities, gks_sex
	FROM ".GKS_WP_TABLE_PREFIX."users
	WHERE viber_id='".$db_link->escape_string($sender_id)."'";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'));die();}
  $user_id=0;
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    //$user_id =6;
    $user_id = $row['ID'];
  }
  $sql="insert into gks_viber_msgs (mydate,sender_id,user_id,
  message_token,message_type,sender_name,sender_avatar,sender_language,sender_country,
  message,
  action_cmd,
  response,
  model, model_id
  ) values (
  now(),'".$db_link->escape_string($sender_id)."',".$user_id.",
  '".$db_link->escape_string($message_token)."',
  '".$db_link->escape_string($type)."',
  '".$db_link->escape_string($sender_name)."',
  '".$db_link->escape_string($sender_avatar)."',
  '".$db_link->escape_string($sender_language)."',
  '".$db_link->escape_string($sender_country)."',
  
  '".$db_link->escape_string($text)."',
  'conversation_started',
  '".$db_link->escape_string($request)."',
  'hook',0
  )";
	$result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'));die();}
  $id_rec = $db_link->insert_id;
  
  if (isset($input['subscribed']) and $input['subscribed']) {
  	$sql="update ".GKS_WP_TABLE_PREFIX."users set viber_subscribed=1 where viber_id like '".$db_link->escape_string($sender_id)."' limit 1";
  	$result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'));die();}
  }
  
  gks_viber_send('hook',0,$sender_id,$welcome_message1, $buttons_newuser1,false,0,'',$id_rec);

  
  die();  
          
} else if($input['event'] == "unsubscribed"){
  // when a conversation is started
//  Array
//  (
//      [event] => unsubscribed
//      [timestamp] => 1619306255947
//      [chat_hostname] => SN-414_
//      [user_id] => WKp5AScR4V5fqOmGeNleWg==
//      [message_token] => 5566883607902295277
//  )

  debug_mail(false,'viber unsubscribed',print_r($input, true),'','');
  $sender_id = $input['user_id']; //unique viber id of user who sent the message
  $type='';
  $text=gks_lang('Αποεγγραφή');
  $message_token=trim_gks($input['message_token']);
  
  $sql="SELECT gks_nickname, ID, viber_id, gks_wp_capabilities, gks_sex
	FROM ".GKS_WP_TABLE_PREFIX."users
	WHERE viber_id='".$db_link->escape_string($sender_id)."'";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'));die();}
  $user_id=0;
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    //$user_id =6;
    $user_id = $row['ID'];
  }
  $sql="insert into gks_viber_msgs (mydate,sender_id,user_id,
  message_token,message_type,
  message,
  action_cmd,
  response,
  model, model_id
  ) values (
  now(),'".$db_link->escape_string($sender_id)."',".$user_id.",
  '".$db_link->escape_string($message_token)."',
  '".$db_link->escape_string($type)."',

  
  '".$db_link->escape_string($text)."',
  'unsubscribed',
  '".$db_link->escape_string($request)."',
  'hook',0
  )";
	$result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'));die();}
  $id_rec = $db_link->insert_id;

	$sql="update ".GKS_WP_TABLE_PREFIX."users set viber_subscribed=0 where viber_id like '".$db_link->escape_string($sender_id)."' limit 1";
	$result = $db_link->query($sql);


//  $sql="update ".GKS_WP_TABLE_PREFIX."users set viber_id='' where viber_id like '".$db_link->escape_string($sender_id)."'";
//	$result = $db_link->query($sql);
	//if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
  die();
  


} else if($input['event'] == "subscribed") {  
  //Array
  //(
  //    [event] => subscribed
  //    [timestamp] => 1619307279403
  //    [chat_hostname] => SN-414_
  //    [user] => Array
  //        (
  //            [id] => WKp5AScR4V5fqOmGeNleWg==
  //            [name] => Subscriber
  //            [avatar] => http://dl-media.viber.com/1/share/2/long/bots/generic-avatar%402x.png
  //            [language] => el-GR
  //            [country] => GR
  //            [api_version] => 10
  //        )
  //
  //    [message_token] => 5566887900587890547
  //)
  
  $message_token=trim_gks($input['message_token']);
  $type = ''; 
  $text =gks_lang('Εγγραφή χρήστη'); 
  $sender_id = trim_gks($input['user']['id']); //unique viber id of user who sent the message
  $sender_name='';if (isset($input['user']['name'])) $sender_name = trim_gks($input['user']['name']); //name of the user who sent the message
  $sender_avatar='';if (isset($input['user']['avatar'])) $sender_avatar = trim_gks($input['user']['avatar']); //name of the user who sent the message
  $sender_language='';if (isset($input['user']['language'])) $sender_language = trim_gks($input['user']['language']); //name of the user who sent the message
  $sender_country='';if (isset($input['user']['country'])) $sender_country = trim_gks($input['user']['country']); //name of the user who sent the message

	$sql="update ".GKS_WP_TABLE_PREFIX."users set viber_subscribed=1 where viber_id like '".$db_link->escape_string($sender_id)."' and viber_subscribed = 0 limit 1";
	$result = $db_link->query($sql);

  $sql="SELECT gks_nickname, ID, viber_id, gks_wp_capabilities, gks_sex
	FROM ".GKS_WP_TABLE_PREFIX."users
	WHERE viber_id='".$db_link->escape_string($sender_id)."'";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'));die();}
  $user_id=0;
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['ID'];
  }  

  $sql="insert into gks_viber_msgs (mydate,sender_id,user_id,
  message_token,message_type,sender_name,sender_avatar,sender_language,sender_country,
  message,
  action_cmd,
  response,
  model, model_id
  ) values (
  now(),'".$db_link->escape_string($sender_id)."',".$user_id.",
  '".$db_link->escape_string($message_token)."',
  '".$db_link->escape_string($type)."',
  '".$db_link->escape_string($sender_name)."',
  '".$db_link->escape_string($sender_avatar)."',
  '".$db_link->escape_string($sender_language)."',
  '".$db_link->escape_string($sender_country)."',
  
  '".$db_link->escape_string($text)."',
  'subscribed',
  '".$db_link->escape_string($request)."',
  'hook',0
  )";
	$result = $db_link->query($sql);
	if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'));die();}
  $id_rec = $db_link->insert_id;
  
  gks_viber_send('hook',0,$sender_id, gks_lang('H παραλαβή μηνυμάτων έχει ενεργοποιηθεί'),[],false,0,'',$id_rec);

  die();

} else if($input['event'] == "delivered"){
  //debug_mail(false,'viber delivered ',print_r($input, true));
  $message_token=trim_gks($input['message_token']);
  if ($message_token!='') {
    $sql="update gks_viber_msgs set delivered=now() where message_token='".$db_link->escape_string($message_token)."' and delivered is null";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}
  }
  die();
} else if($input['event'] == "seen"){
  //debug_mail(false,'viber delivered ',print_r($input, true));
  $message_token=trim_gks($input['message_token']);
  if ($message_token!='') {
    $sql="update gks_viber_msgs set seen='".date('Y-m-d H:i:s',intval($input['timestamp']/1000))."' where message_token='".$db_link->escape_string($message_token)."' and seen is null";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}
  }
  
  $sql="select id_viber_msgs,delivered,receiver_id from gks_viber_msgs where delivered is not null and message_token='".$db_link->escape_string($input['message_token'])."' limit 1";
	//debug_mail(false,'viber seen1 ',$sql);
	$result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $delivered=$row['delivered'];
    $receiver_id=$row['receiver_id'];
    $id_viber_msgs=$row['id_viber_msgs'];
    
    $sql="select id_viber_msgs,seen from gks_viber_msgs where id_viber_msgs<>".$id_viber_msgs." and delivered <='".$delivered."' and receiver_id='".$db_link->escape_string($receiver_id)."' order by delivered desc limit 100";
	  //debug_mail(false,'viber seen2 ',$sql);
	  $result = $db_link->query($sql);
    if ($result) {
      $ids=array();
      while ($row = $result->fetch_assoc()) {
        if (trim_gks($row['seen'])=='') $ids[]=$row['id_viber_msgs']; else break;
      }
      if (count($ids)>0) {
      	$sql="update gks_viber_msgs set seen='".date('Y-m-d H:i:s',intval($input['timestamp']/1000))."' where id_viber_msgs in (".implode(',',$ids).")";
      	//debug_mail(false,'viber seen3 ',$sql);
      	$result = $db_link->query($sql);
      	if (!$result) {debug_mail(false,'error sql',$sql);die();}
      }
    }
    
    
  }
    
  die();

} else if($input['event'] == "message") {  
  $message_token=trim_gks($input['message_token']);
  $type = trim_gks($input['message']['type']); //type of message received (text/picture)
  $text =''; if (isset($input['message']['text'])) $text = trim_gks($input['message']['text']); //actual message the user has sent
  $parts = explode('|', $text);
  $sender_id = trim_gks($input['sender']['id']); //unique viber id of user who sent the message

  $sender_name='';if (isset($input['sender']['name'])) $sender_name = trim_gks($input['sender']['name']); //name of the user who sent the message
  $sender_avatar='';if (isset($input['sender']['avatar'])) $sender_avatar = trim_gks($input['sender']['avatar']); //name of the user who sent the message
  $sender_language='';if (isset($input['sender']['language'])) $sender_language = trim_gks($input['sender']['language']); //name of the user who sent the message
  $sender_country='';if (isset($input['sender']['country'])) $sender_country = trim_gks($input['sender']['country']); //name of the user who sent the message

	$sql="update ".GKS_WP_TABLE_PREFIX."users set viber_subscribed=1 where viber_id like '".$db_link->escape_string($sender_id)."' and viber_subscribed = 0 limit 1";
	$result = $db_link->query($sql);

  $sql="SELECT gks_nickname, ID, viber_id, gks_wp_capabilities, gks_sex
	FROM ".GKS_WP_TABLE_PREFIX."users
	WHERE viber_id='".$db_link->escape_string($sender_id)."'";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'));die();}
  $user_id=0;
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    //$user_id =6;
    $user_id = $row['ID'];
    $user_roles = unserialize($row['gks_wp_capabilities']);	
    $user_nickname = $row['gks_nickname'];	
    $user_sex = $row['gks_sex'];    
  }  

  $sql="insert into gks_viber_msgs (mydate,sender_id,user_id,
  message_token,message_type,sender_name,sender_avatar,sender_language,sender_country,
  message,
  action_cmd,action_cmd_part1,action_cmd_part2,action_cmd_part3,
  response,
  model, model_id
  ) values (
  now(),'".$db_link->escape_string($sender_id)."',".$user_id.",
  '".$db_link->escape_string($message_token)."',
  '".$db_link->escape_string($type)."',
  '".$db_link->escape_string($sender_name)."',
  '".$db_link->escape_string($sender_avatar)."',
  '".$db_link->escape_string($sender_language)."',
  '".$db_link->escape_string($sender_country)."',
  '',
  '".$db_link->escape_string($text)."',
  ".(count($parts)>=1 ? "'".$db_link->escape_string($parts[0])."'" : 'null').",
  ".(count($parts)>=2 ? "'".$db_link->escape_string($parts[1])."'" : 'null').",
  ".(count($parts)>=3 ? "'".$db_link->escape_string($parts[2])."'" : 'null').",
  '".$db_link->escape_string($request)."',
  'hook',0

  )";
	$result = $db_link->query($sql);
	if (!$result) {debug_mail(false,'error sql',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'));die();}
  $id_rec = $db_link->insert_id;
  
  
  if ($user_id ==0) {
    switch ($parts[0]) {
    
      case 'welcome':
        $re_text=$welcome_message1 . "\n\n". $welcome_message2;
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
				$result = $db_link->query($sql);
				if (!$result) {debug_mail(false,'error sql4',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
        
        gks_viber_send('hook',0,$sender_id,$re_text, $buttons_newuser1,false,0,'',$id_rec);
        die();    

		 	case 'login':
		 	  $re_text=gks_lang('Πληκτρολογήστε και στείλτε το όνομα χρήστη ή το email σας');
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
				$result = $db_link->query($sql);
				if (!$result) {debug_mail(false,'error sql5',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
        gks_viber_send('hook',0,$sender_id,$re_text,[],false,0,'',$id_rec);
		 	  die();
		 	
		 	  break;
		 	      
		 	default: 		

        // is from login, username
        $sql="select * from gks_viber_msgs where sender_id='".$sender_id."' and user_id=0 and mydate >=date_sub(now(), interval 2 minute) order by id_viber_msgs desc limit 2";
				$result = $db_link->query($sql);
				if (!$result) {debug_mail(false,'error sql6',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
        if ($result->num_rows == 2) {
          $isok=true;
          $i=0;
          while ($row = $result->fetch_assoc()) {
            $i++;
            if ($i==1) if ($row['action_cmd_part1'].'' == '') $isok = false;

            if ($i==2) if ($row['action_cmd_part1'] != 'login') $isok = false;
           
          }
          if ($isok) {
            $re_text==gks_lang('Πληκτρολογήστε και στείλτε τον κωδικό πρόσβασης');
            $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    				$result = $db_link->query($sql);
    				if (!$result) {debug_mail(false,'error sql7',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
            
            gks_viber_send('hook',0,$sender_id,$re_text,[],false,0,'',$id_rec);
            die();
          }
        }
        
        // is from login, username
        $sql="select * from gks_viber_msgs where sender_id='".$sender_id."' and user_id=0 and mydate >=date_sub(now(), interval 5 minute) order by id_viber_msgs desc limit 3";
				$result = $db_link->query($sql);
				if (!$result) {debug_mail(false,'error sql8',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
        if ($result->num_rows == 3) {
          $isok=true;
          $i=0;
          $username='';
          $userpass='';
          while ($row = $result->fetch_assoc()) {
            $i++;
            if ($i==1) if ($row['action_cmd_part1'].'' == '')     $isok = false; else $userpass=$row['action_cmd_part1'].'';
            if ($i==2) if ($row['action_cmd_part1'].'' == '')     $isok = false; else $username=$row['action_cmd_part1'].'';
            if ($i==3) if ($row['action_cmd_part1'] != 'login')   $isok = false;
           
          }
          

            
          if ($isok) {
            $sql="SELECT ID, user_login, user_email, user_pass, user_registered
            FROM ".GKS_WP_TABLE_PREFIX."users
            where (user_login like '".$db_link->escape_string($username)."' or user_email like '".$db_link->escape_string($username)."') and user_registered is not null";
    				$result = $db_link->query($sql);
    				if (!$result) {debug_mail(false,'error sql9',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
            if ($result->num_rows != 1) {
              debug_mail(false,'username not found',$sql);
              $re_text==gks_lang('Το όνομα χρήστη δεν βρέθηκε. Ξαναδοκιμάστε');
              $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
      				$result = $db_link->query($sql);
      				if (!$result) {debug_mail(false,'error sql10',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
              gks_viber_send('hook',0,$sender_id,$re_text,$buttons_newuser1,false,0,'',$id_rec);die();
            }
            $row = $result->fetch_assoc();
            $user_id=$row['ID'];
            if (!wp_check_password($userpass, $row['user_pass'], $row['ID'])) {
              debug_mail(false,'userpass is lathos',$sql);
              $re_text=gks_lang('Ο κωδικός πρόσβασης είναι λάθος. Ξαναδοκιμάστε');
              $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
      				$result = $db_link->query($sql);
      				if (!$result) {debug_mail(false,'error sql11',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
              gks_viber_send('hook',0,$sender_id,$re_text,$buttons_newuser1,false,0,'',$id_rec);die();
            }
            
            $sql="update ".GKS_WP_TABLE_PREFIX."users set viber_id='',viber_subscribed=0 where viber_id like '".$db_link->escape_string($sender_id)."'";
    				$result = $db_link->query($sql);
    				if (!$result) {debug_mail(false,'error sql12',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
            
            $sql="update ".GKS_WP_TABLE_PREFIX."users set viber_id='".$db_link->escape_string($sender_id)."',viber_subscribed=1 where ID=".$user_id." limit 1";
    				$result = $db_link->query($sql);
    				if (!$result) {debug_mail(false,'error sql13',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
            
            $sql="update gks_viber_msgs set user_id=".$user_id." 
            where user_id=0 and 
            (sender_id like '".$db_link->escape_string($sender_id)."' or 
             receiver_id like '".$db_link->escape_string($sender_id)."')";
    				$result = $db_link->query($sql);
    				if (!$result) {debug_mail(false,'error sql14d',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
            
            $re_text=gks_lang('Επιτυχής σύνδεση!');
            $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    				$result = $db_link->query($sql);
    				if (!$result) {debug_mail(false,'error sql15',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}

            gks_viber_send('hook',0,$sender_id,$re_text, $only_home,false,0,'',$id_rec);
            die();
          }
        }
                
        
     
		 		$re_text=gks_lang('Άγνωστη εντολή');
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
				$result = $db_link->query($sql);
				if (!$result) {debug_mail(false,'error sql16',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}

        gks_viber_send('hook',0,$sender_id,$re_text, $buttons_newuser2,false,0,'',$id_rec);
        die();  
    }   
  }
  
  $user_role='';
	if (isset($user_roles['subscriber'])) 					$user_role='subscriber';
	if (isset($user_roles['device'])) 					    $user_role='device';
	if (isset($user_roles['employee']))     				$user_role='employee';
	if (isset($user_roles['driver']))     					$user_role='driver';
	if (isset($user_roles['photographer'])) 				$user_role='photographer';
	if (isset($user_roles['omadarxis'])) 						$user_role='omadarxis';
	if (isset($user_roles['xiristismixanimaton']))  $user_role='xiristismixanimaton';
	if (isset($user_roles['ipethinosperioxis']))		$user_role='ipethinosperioxis';
	if (isset($user_roles['babys'])) 								$user_role='babys';
	if (isset($user_roles['findphotos'])) 					$user_role='findphotos';
	if (isset($user_roles['logistis'])) 						$user_role='logistis';
	if (isset($user_roles['hrmanager'])) 						$user_role='hrmanager';
	if (isset($user_roles['texnikos'])) 						$user_role='texnikos';
	if (isset($user_roles['author']))  							$user_role='author';
	if (isset($user_roles['contributor'])) 					$user_role='contributor';
	if (isset($user_roles['editor'])) 							$user_role='editor';
	if (isset($user_roles['adminmy'])) 							$user_role='admin';
	if (isset($user_roles['administrator'])) 				$user_role='admin';
	//if (isset($user_roles['device'])) 					    $user_role='device';
	
	$user_role=trim($user_role);
	if ($user_role=='' or $user_role == 'subscriber') {
 		$re_text=$welcome_message3;
    $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
		$result = $db_link->query($sql);
		if (!$result) {debug_mail(false,'error sql17',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}

    //debug_mail(false,'viber id not found');
    gks_viber_send('hook',0,$sender_id,$re_text,$only_home,false,0,'',$id_rec); 
    die();
	}
	
	$re_text='';
	switch ($parts[0]) {
		 	case '?':
		 	case ';':
		 		$re_text=gks_lang('Γεια σου').' '.$user_nickname."\n".
        gks_lang('Οι διαθέσιμες εντολές είναι αυτές που εμφανίζονται από κάτω σαν κουμπιά').':';
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql47',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}

		 	 	gks_viber_send('hook',0,$sender_id,$re_text,$buttons_home,false,0,'',$id_rec);die();break;
		 	case 'time':
		 		$re_text = showDate(time(), 'd/m/Y H:i:s', 1);
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql48',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 	 	gks_viber_send('hook',0,$sender_id,$re_text,$buttons_home,false,0,'',$id_rec);die();break;
		 	case 'ip':
		 		$re_text = $gkIP;
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql49',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 	 	gks_viber_send('hook',0,$sender_id,$re_text,$buttons_home,false,0,'',$id_rec);die();break;

		 	case 'welcome':
		 	case 'arxiki':
        $sql="update gks_viber_msgs set user_id=".$user_id." where user_id=0 and 
        (sender_id like '".$db_link->escape_string($sender_id)."' or 
         receiver_id like '".$db_link->escape_string($sender_id)."')";
				$result = $db_link->query($sql);
				if (!$result) {debug_mail(false,'error sql14e',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
			  
        $re_text=gks_lang('Αρχική');
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql18',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 	 	gks_viber_send('hook',0,$sender_id,$re_text,$buttons_home,false,0,'',$id_rec);die();break;
		 	 	
      case 'getpin':
		 	  $sql="select user_pin from ".GKS_WP_TABLE_PREFIX."users where ID=".$user_id;
				$result = $db_link->query($sql);
				if (!$result) {debug_mail(false,'error sql14a',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
        if ($result->num_rows!=1) {
		 	    debug_mail(false,'error sql59-s1',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα').' '.gks_lang('Δεν βρέθηκε η εγγραφή')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();
		 	  }
		 	  $row = $result->fetch_assoc();
		 	  $user_pin=trim($row['user_pin']);
		 	  if ($user_pin=='') $re_text=gks_lang('Δεν έχετε ορίσει PIN');
		 	  else $re_text=gks_lang('Το PIN σας είναι').': ' . $user_pin;

        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 	  
		 	  gks_viber_send('hook',0,$sender_id,$re_text,$pin_buttons,false,0,'',$id_rec);die();break;

		 	case 'setnewpin1':
		 	  $re_text=gks_lang('Πληκτρολογήστε και στείλτε το νέο PIN που θέλετε')."\n".gks_lang('Θα πρέπει να είναι τουλάχιστον 4 ψηφία');
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
        gks_viber_send('hook',0,$sender_id,$re_text,[],false,0,'',$id_rec);die();break;

		 	case 'profil':
		 	
		 	  //".GKS_WP_TABLE_PREFIX."user
	 	    $sql = "SELECT ".GKS_WP_TABLE_PREFIX."users.*, gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
        gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, gks_users.ma_poli, gks_users.ma_tk, 
        gks_users.ma_country_id, gks_users.ma_nomos_id, 
        gks_users.phone_home, gks_users.genisi_date, gks_users.ethnikotita, 
        gks_users.alli_apasxolisi,gks_users.cv_proipiresia, gks_users.cv_spoydes, gks_users.cv_seminaria, gks_users.cv_mitriki_glossa, gks_users.cv_jenes_glosses,
        gks_users.cv_sxesi_me_photografia, 
        gks_users.cv_metaforiko_meso, gks_users.cv_has_bike, gks_users.cv_has_motorcycle, gks_users.cv_has_car,
        gks_users.profilepososto_user, gks_users.profilepososto_job,
        gks_country.country_name, gks_nomoi.nomos_descr, 
        table_last_name.mylast_name, table_first_name.myfirst_name, table_mobile.mymoobile, table_roles.mywp_capabilities,
        gks_users.user_HumanInitial,
        gks_users.amka ,gks_users.ama_eam ,gks_users.arithmos_tautoitas ,gks_users.arxi_ekdosis, gks_users.onoma_patera, gks_users.onoma_miteras,
        gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr,gks_users.oikogeniaki_katastasti_id, gks_users.oikogeniaki_katastasti_paidia,
        gks_users.sistasi_from,gks_users.days_to_work
        
        FROM (((((((((".GKS_WP_TABLE_PREFIX."users 
        LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
        LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
        LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
        LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
        LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist) 
        LEFT JOIN gks_users_oikogeniaki_katastasti ON gks_users_oikogeniaki_katastasti.id_oikogeniaki_katastasti = gks_users.oikogeniaki_katastasti_id) 
        LEFT JOIN (
          SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
          FROM ".GKS_WP_TABLE_PREFIX."usermeta
          WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
        )  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
        LEFT JOIN (
          SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
          FROM ".GKS_WP_TABLE_PREFIX."usermeta
          WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
        )  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
        LEFT JOIN (
          SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mymoobile
          FROM ".GKS_WP_TABLE_PREFIX."usermeta
          WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='mobile'))
        )  AS table_mobile ON ".GKS_WP_TABLE_PREFIX."users.ID = table_mobile.user_id) 
        LEFT JOIN (
          SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mywp_capabilities
          FROM ".GKS_WP_TABLE_PREFIX."usermeta
          WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='wp_capabilities'))
        )  AS table_roles ON ".GKS_WP_TABLE_PREFIX."users.ID = table_roles.user_id
        where ".GKS_WP_TABLE_PREFIX."users.id = ".$user_id;
				$result = $db_link->query($sql);
				if (!$result) {debug_mail(false,'error sql56',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
        if ($result->num_rows == 0) {debug_mail(false,'viber user not found',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}      
        $row = $result->fetch_assoc();
        
        $re_text='';
        if (!empty($row['myfirst_name'])) $re_text.=gks_lang('Όνομα').': '.$row['myfirst_name']."\n";
        if (!empty($row['mylast_name'])) $re_text.=gks_lang('Επίθετο').': '.$row['mylast_name']."\n";
        if (!empty($row['gks_nickname'])) $re_text.=gks_lang('Υποκοριστικό').': '.$row['gks_nickname']."\n";
        if (!empty($row['onoma_patera'])) $re_text.=gks_lang('Όνομα πατέρα').': '.$row['onoma_patera']."\n";
        if (!empty($row['onoma_miteras'])) $re_text.=gks_lang('Όνομα μητέρας').': '.$row['onoma_miteras']."\n";
        if ($row['gks_sex']==1 or $row['gks_sex']==2) $re_text.=gks_lang('Φύλο').': '.($row['gks_sex']==1 ? 'Άρρεν' : 'Θύλη')."\n";
        if (!empty($row['user_email'])) $re_text.=gks_lang('email').': '.$row['user_email']."\n";
        if (!empty($row['mymoobile'])) $re_text.=gks_lang('Κινητό').': '.$row['mymoobile']."\n";
        //if (!empty($row['users_category_descr'])) $re_text.=gks_lang('Κατηγορία').': '.$row['users_category_descr'].$row['category_aa']."\n";
        //if (!empty($row['onoma_patera'])) $re_text.=gks_lang('Πατρώνυμο').': '.$row['onoma_patera']."\r\n";
		 	
		 	  
		 	  if ($re_text == '') $re_text=gks_lang('Δεν βρέθηκαν δεδομένα');
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql57',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 		gks_viber_send('hook',0,$sender_id,$re_text,$buttons_home,false,0,'',$id_rec);die();break;		
		 	
		 	case 'massmessage':
		 	
		 	  $re_text=gks_lang('Η απάντηση έχει καταχωρηθεί');
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 	  gks_viber_send('hook',0,$sender_id,$re_text,$only_home,false,0,'',$id_rec);die();break;
		 	  break;


		 	default: 		  
		 		$sql="select * from gks_viber_msgs where user_id=".$user_id." order by id_viber_msgs desc limit 10";
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql57',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 		$row_array=array();
		 		while ($row = $result->fetch_assoc()) {
		 		  $row_array[]=$row;
		 		}


		 		if (count($row_array)>=3) {  
		 		  if ($row_array[1]['action_cmd'] == 'setnewpin1' and trim($row_array[0]['action_cmd_part3'])!='ok') {
		 		    $user_pin=trim($text);
    		 	  if (strlen($user_pin)<4 or ctype_digit($user_pin)==false) {
    		 	    $re_text = gks_lang('Το PIN θα πρέπει να είναι τουλάχιστον 4 ψηφία');
              $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."',action_cmd_part3='error' where id_viber_msgs=".$id_rec;
          		$result = $db_link->query($sql);
          		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
    		 	    gks_viber_send('hook',0,$sender_id,$re_text,$pin_buttons,false,0,'',$id_rec);die();
    		 	  }	
            $sql="select * from ".GKS_WP_TABLE_PREFIX."users where user_pin='".$user_pin."' and ID<>".$user_id;
            $result = $db_link->query($sql); 
            if (!$result) {debug_mail(false,'error sql14b',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
            if ($result->num_rows>0 or $user_pin=='0000' or $user_pin=='1111' or $user_pin=='2222' or $user_pin=='3333' or $user_pin=='4444' or $user_pin=='1234') {
              debug_mail(false,'user_pin error','the PIN <b>'.$user_pin.'</b> is registe to other user');
              $re_text = gks_lang('Το PIN [1] υπάρχει ήδη καταχωρημένο σε άλλον χρήστη');
              $re_text=str_replace('[1]',$user_pin,$re_text);
              $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."',action_cmd_part3='error' where id_viber_msgs=".$id_rec;
          		$result = $db_link->query($sql);
          		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
    		 	    gks_viber_send('hook',0,$sender_id,$re_text,$pin_buttons,false,0,'',$id_rec);die();
            }	    		 	  
    		 	  	 		    
		 		    $re_text=gks_lang('Πληκτρολογήστε ξανά το ίδιο PIN για επιβεβαίωση');
            $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."',action_cmd_part3='ok' where id_viber_msgs=".$id_rec;
        		$result = $db_link->query($sql);
        		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 	      gks_viber_send('hook',0,$sender_id,$re_text,[],false,0,'',$id_rec);die();
		 		  }
		 		}
		 		
		 		if (count($row_array)>=3) {
		 		  if ($row_array[2]['action_cmd'] == 'setnewpin1' and ($row_array[1]['action_cmd_part3']) == 'ok') {
		 		    $user_pin=trim($text);
		 		    $user_pin_prev=trim($row_array[1]['action_cmd']);
		 		    if ($user_pin!=$user_pin_prev) {
  		 		    $re_text=gks_lang('Δεν πληκτρολογήσατε το ίδιο PIN');
              $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."',action_cmd_part3='okset' where id_viber_msgs=".$id_rec;
          		$result = $db_link->query($sql);
          		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
  		 	      gks_viber_send('hook',0,$sender_id,$re_text,$pin_buttons,false,0,'',$id_rec);die();		 		    
		 		    }
            if (strlen($user_pin)<4 or ctype_digit($user_pin)==false) {
    		 	    $re_text = gks_lang('Το PIN θα πρέπει να είναι τουλάχιστον 4 ψηφία');
              $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."',action_cmd_part3='error' where id_viber_msgs=".$id_rec;
          		$result = $db_link->query($sql);
          		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
    		 	    gks_viber_send('hook',0,$sender_id,$re_text,$pin_buttons,false,0,'',$id_rec);die();
    		 	  }	
            $sql="select * from ".GKS_WP_TABLE_PREFIX."users where user_pin='".$user_pin."' and ID<>".$user_id;
            $result = $db_link->query($sql); 
            if (!$result) {debug_mail(false,'error sql14c',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
            if ($result->num_rows>0 or $user_pin=='0000' or $user_pin=='1111' or $user_pin=='2222' or $user_pin=='3333' or $user_pin=='4444' or $user_pin=='1234') {
              debug_mail(false,'user_pin error','PIN <b>'.$user_pin.'</b> is registe to other user');
              $re_text = gks_lang('Το PIN [1] υπάρχει ήδη καταχωρημένο σε άλλον χρήστη');
              $re_text=str_replace('[1]', $user_pin, $re_text);
              
              $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."',action_cmd_part3='error' where id_viber_msgs=".$id_rec;
          		$result = $db_link->query($sql);
          		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
    		 	    gks_viber_send('hook',0,$sender_id,$re_text,$pin_buttons,false,0,'',$id_rec);die();
            }	
            
            $sql="update ".GKS_WP_TABLE_PREFIX."users set user_pin='".$db_link->escape_string($user_pin)."' where ID=".$user_id;
        		$result = $db_link->query($sql);
        		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
           
            
		 		    $re_text=gks_lang('Το PIN σας έχει ορισθεί επιτυχώς'); //pin ok '.$user_pin.' '.$user_pin_prev;
            $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."',action_cmd_part3='okset' where id_viber_msgs=".$id_rec;
        		$result = $db_link->query($sql);
        		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 	      gks_viber_send('hook',0,$sender_id,$re_text,$only_home,false,0,'',$id_rec);die();		 		    
		 		  } 
		 		}
		 		
		 		$re_text=gks_lang('Άγνωστη εντολή');
        $sql="update gks_viber_msgs set message='".$db_link->escape_string($re_text)."' where id_viber_msgs=".$id_rec;
    		$result = $db_link->query($sql);
    		if (!$result) {debug_mail(false,'error sql58',$sql);gks_viber_send('hook',0,$sender_id, gks_lang('Σφάλμα συστήματος')."\n".gks_lang('Ξαναδοκιμάστε αργότερα'),$only_home);die();}
		 		gks_viber_send('hook',0,$sender_id,$re_text,$buttons_home,false,0,'',$id_rec);die();break;

  }          		 	  		 	 		  
  
  
  //gks_viber_send('hook',0,$sender_id, "sss ssss ".$id_rec);
  die();
  
} else {
  debug_mail(false,'viber else',print_r($input, true));
}



