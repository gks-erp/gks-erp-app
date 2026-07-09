<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\Exception;
//use Greew\OAuth2\Client\Provider\Azure;



function debug_mail($and_die, $message_public, $message_dev='', $subject='', $add_email_to ='') {
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/debug_mail_'.time().'_'.rand(1000,9999).rand(1000,9999).'.txt',$subject."\n".$message_public."\n".$message_dev);
  //if ($_SERVER['HTTP_HOST']=='test.easyfilesselection.com') return;
  
  global $_gks_session;
  global $_gks_id_session;
  if (GKS_EMAIL_DEBUG_FROM=='' or GKS_EMAIL_DEBUG_HOST=='') return;
  
  if ($subject =='') {
    $subject ='debug_mail';
  }

  
  $gkIP = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');

  $message = '';


  $message.="\r\n<b>SERVER</b>\r\n";
  $message.=print_r($_SERVER, true);

  $message.="\r\n<b>REQUEST</b>\r\n";
  $message.=print_r($_REQUEST, true);

  $message.="\r\n<b>GET</b>\r\n";
  $message.=print_r($_GET, true);

  $message.="\r\n<b>POST</b>\r\n";
  $message.=print_r($_POST, true);

  $message.="\r\n<b>FILES</b>\r\n";
  $message.=print_r($_FILES, true);

  $message.="\r\n<b>COOKIE</b>\r\n";
  $message.=print_r($_COOKIE, true);
  //if (!isset($_SESSION))
  //    session_start();
  //$message.="\r\n<b>SESSION</b>\r\n";
  //$message.=print_r($_SESSION, true);
  
  $message.="\r\n<b>_gks_id_session</b>\r\n";
  $message.=$_gks_id_session;
  
  //$message.="\r\n<b>_gks_session</b>\r\n";
  //$message.=print_r($_gks_session, true);
  
  //$message.="\r\nGLOBALS\r\n";    
  //$message.=print_r($GLOBALS, true);


  //$message.="\r\n<b>Wordpress user info</b>\r\n";
  //$message.=json_encode(wp_get_current_user(),JSON_PRETTY_PRINT);





// A bug in PHP < 5.2.2 makes $HTTP_RAW_POST_DATA not set by default,
// but we can do it ourself.
  if ( !isset( $HTTP_RAW_POST_DATA ) ) {
  	$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
  }
  
  // fix for mozBlog and other cases where '<?xml' isn't on the very first line
  if ( isset($HTTP_RAW_POST_DATA) )
  	$HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
  	
  $message.="\r\n<b>HTTP_RAW_POST_DATA</b>\r\n";
  $message.=print_r(htmlentities( $HTTP_RAW_POST_DATA), true);
   
     	
   
  $message.="\r\n<b>IP      :" . $gkIP . "</b>";
  $message.="\r\n<b>hostname:" ;
  if ($gkIP!='') {
   $message.=gethostbyaddr($gkIP);
  }
  $message.="</b>";



  $message_mail = '<html>
  <head>
    <title>gks debug</title>
  </head>
  <body>
  	<pre>message_public: '. $message_public."\r\n";
  if ($message_dev !='') {
    $message_mail.='message_dev: '. $message_dev."\r\n";
  }
  
  $message_mail.=$message."\r\n".'
  	</pre>
  </body>
  </html>
  ';
  $subject = $subject . ' ' . $gkIP. ' '. $message_public;
  

  
  $mail = new PHPMailer();
  $mail->isSMTP();
  $mail->Host = GKS_EMAIL_DEBUG_HOST;
  $mail->Port = GKS_EMAIL_DEBUG_PORT;
  $mail->SMTPAuth = GKS_EMAIL_DEBUG_SMTPAUTH;
  $mail->Username = GKS_EMAIL_DEBUG_USERNAME;
  $mail->Password = GKS_EMAIL_DEBUG_PASSWORD;
  
  //$mail->setLanguage('el');
  $mail->CharSet='utf-8';
  $mail->setFrom(GKS_EMAIL_DEBUG_FROM,GKS_EMAIL_DEBUG_FROM);
  
  if (GKS_EMAIL_DEBUG_TO_1!='') $mail->addAddress(GKS_EMAIL_DEBUG_TO_1);  
  if (GKS_EMAIL_DEBUG_TO_2!='') $mail->addAddress(GKS_EMAIL_DEBUG_TO_2);  
  if (GKS_EMAIL_DEBUG_TO_3!='') $mail->addAddress(GKS_EMAIL_DEBUG_TO_3);  
  
  if ($add_email_to!='') {
  	$dd=explode(',',$add_email_to);
  	foreach ($dd as $value) {
  		$mail->addAddress($value);  
  	} 
  }
  $mail->Subject = $subject;
  $mail->msgHTML($message_mail);
  $myret = $mail->send();

  
  if ($and_die) {
    echo $message_public;
    die(); 
  }
  
}




function gks_mymail_template($params) {
  global $db_link;
  global $my_wp_user_id;
  global $_gks_session;
  

  global $GKS_SITE_HUMAN_NAME;
  global $GKS_OFFICIAL_SITE_URL;
  global $GKS_SITE_NAME;
  global $GKS_SITE_EMAIL;
  

  $ispreview=false;
  $model='';
  $model_id=0;
  $from='';
  $from_name='';
  $replyto='';
  $sender='';
  $to='';
  $to_name='';
  $subject='';
  $template=0;
  $replaces = array();
  $Attachments = array();
  $EmbeddedImages = array();
  
  if (isset($params['ispreview']))      $ispreview=$params['ispreview'];            //-100
  if (isset($params['model']))          $model=$params['model'];                    //1
  if (isset($params['model_id']))       $model_id=$params['model_id'];              //2
  if (isset($params['from']))           $from=$params['from'];                      //3
  if (isset($params['from_name']))      $from_name=$params['from_name'];            //4
  if (isset($params['replyto']))        $replyto=$params['replyto'];                //5 
  if (isset($params['sender']))         $sender=$params['sender'];                  //6
  if (isset($params['to']))             $to=$params['to'];                          //7
  if (isset($params['to_name']))        $to_name=$params['to_name'];                //8
  if (isset($params['subject']))        $subject=$params['subject'];                //9
  if (isset($params['template']))       $template=intval($params['template']);      //10
  if (isset($params['replaces']))       $replaces=$params['replaces'];              //11
  if (isset($params['Attachments']))    $Attachments=$params['Attachments'];        //12
  if (isset($params['EmbeddedImages'])) $EmbeddedImages=$params['EmbeddedImages'];  //13
  
  //echo '<pre>';  echo $from_name.'|'.$from.'|'.$GKS_SITE_EMAIL;  die();

  

  
  $sql="select * from gks_email_template where id_email_template=".$template;
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);return false;}
  $sociallinks_type=[];
  if ($result->num_rows==0) {
    debug_mail(false,'template error','record not found: '.$sql);
    return false;
  }
  
  $template_row = $result->fetch_assoc();
  
  //print '<pre>';print_r($template_row);die();
    
  $body = $template_row['email_body'];
  
  $body=str_replace('[[GKS_SITE_HUMAN_NAME]]',$GKS_SITE_HUMAN_NAME, $body);
  $body=str_replace('[[GKS_OFFICIAL_SITE_URL]]',$GKS_OFFICIAL_SITE_URL, $body);
  $body=str_replace('[[GKS_SITE_URL]]',GKS_SITE_URL, $body);
  $body=str_replace('[[GKS_SITE_EMAIL]]',$GKS_SITE_EMAIL, $body);
  $body=str_replace('[[GKS_SITE_NAME]]',$GKS_SITE_NAME, $body);
  $body=str_replace('[[year]]',showDate(time(),'Y',1), $body);



  $subject=str_replace('[[GKS_SITE_HUMAN_NAME]]',$GKS_SITE_HUMAN_NAME, $subject);
  $subject=str_replace('[[GKS_OFFICIAL_SITE_URL]]',$GKS_OFFICIAL_SITE_URL, $subject);
  $subject=str_replace('[[GKS_SITE_URL]]',GKS_SITE_URL, $subject);
  $subject=str_replace('[[GKS_SITE_EMAIL]]',$GKS_SITE_EMAIL, $subject);
  $subject=str_replace('[[GKS_SITE_NAME]]',$GKS_SITE_NAME, $subject);
  $subject=str_replace('[[year]]',showDate(time(),'Y',1), $subject);


  $sql="select id_sociallinks_type,sociallinks_type_descr,sociallinks_type_icon_email from gks_sociallinks_type where sociallinks_type_disable=0";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);return false;}
  $sociallinks_type=[];
  while ($row = $result->fetch_assoc()) {
    $row['descr_lower']=strtolower($row['sociallinks_type_descr']);
    $sociallinks_type[$row['id_sociallinks_type']]=$row;
  }

  $sql="select sociallinks_type_id,url from gks_sociallinks where object_name='gks_settings' and object_id=1 and url<>''";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);return false;}
  $sociallinks=[];
  while ($row = $result->fetch_assoc()) {
    $sociallinks[$row['sociallinks_type_id']]=$row['url'];
  }    
  
  foreach ($sociallinks_type as $mysl_type) {
    $mysl_url='';$newhtml='';
    if (isset($sociallinks[$mysl_type['id_sociallinks_type']])) {
      $mysl_url=$sociallinks[$mysl_type['id_sociallinks_type']];
      $mysl_icon=$mysl_type['sociallinks_type_icon_email'];
      $mysl_icon=str_replace(' src="/my/',' width="20px" src="'.GKS_SITE_URL.'my/',$mysl_icon);
      
      
      $newhtml='&nbsp;<a href="'.$mysl_url.'" target="_blank">'.$mysl_icon.'</a>';
    }
    $body=str_replace('[[link_'.$mysl_type['descr_lower'].']]',$newhtml, $body);
    $subject=str_replace('[[link_'.$mysl_type['descr_lower'].']]',$newhtml, $subject);
  }     
  
  
  
  
  foreach ($replaces as $repval) {
    $body = str_replace($repval[0],$repval[1], $body);
    $subject = str_replace($repval[0],$repval[1], $subject);
  }

  $body=str_replace('[[get_list_bank_accounts]]',gks_get_list_bank_accounts(), $body);
  
  if ($ispreview) {
    return array(
      'subject'=>$subject,
      'body'=>$body,
    );
      
  }
  
  if (strpos($body, '"/my/_current/_img_site/finallogonew.png"') !== false) {
    $EmbeddedImages[]=array(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_img_site/finallogonew.png','my_img_finallogonew.png');
    $body = str_replace('"/my/_current/_img_site/finallogonew.png"','"cid:my_img_finallogonew.png"', $body);
  }
  if (strpos($body, '"/my/_current/_img_site/logo200.png"') !== false) {
    $EmbeddedImages[]=array(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_img_site/logo200.png','my_img_logo200.png');
    $body = str_replace('"/my/_current/_img_site/logo200.png"','"cid:my_img_logo200.png"', $body);
  }
  if (strpos($body, '"/my/_current/_img_site/logo100.png"') !== false) {
    $EmbeddedImages[]=array(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_img_site/logo100.png','my_img_logo100.png');
    $body = str_replace('"/my/_current/_img_site/logo100.png"','"cid:my_img_logo100.png"', $body);
  }
  
  foreach ($sociallinks_type as $mysl_type) {
    $mysrc=GKS_SITE_URL.'my/img/sociallinks/20/'.$mysl_type['descr_lower'].'.png';
    $mycid='mysociallinks20'.$mysl_type['descr_lower'].'.png';
    if (strpos($body, '"'.$mysrc.'"') !== false) {
      $EmbeddedImages[]=array(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/img/sociallinks/20/'.$mysl_type['descr_lower'].'.png',$mycid);
      $body = str_replace('"'.$mysrc.'"','"cid:'.$mycid.'"', $body);
    }
    $mysrc=GKS_SITE_URL.'my/img/sociallinks/'.$mysl_type['descr_lower'].'.png';
    $mycid='mysociallinks'.$mysl_type['descr_lower'].'.png';
    if (strpos($body, '"'.$mysrc.'"') !== false) {
      $EmbeddedImages[]=array(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/img/sociallinks/'.$mysl_type['descr_lower'].'.png',$mycid);
      $body = str_replace('"'.$mysrc.'"','"cid:'.$mycid.'"', $body);
    }
  }
    
  
  
  
  
  return gks_mymail($model, $model_id, $from, $from_name, $replyto, $sender, $to, $to_name, $subject, $body, $Attachments, $EmbeddedImages,$template);

  
}
function gks_mymail($model, $model_id, $from, $from_name, $replyto, $sender, $to, $to_name, $subject, $body, $Attachments = array(), $EmbeddedImages = array(),$template=0) {
  global $db_link;
  global $my_wp_user_id;
  global $GKS_SITE_HUMAN_NAME;
  global $GKS_OFFICIAL_SITE_URL;
  global $GKS_SITE_EMAIL;
  
  

  global $GKS_EMAIL_BCC1;
  global $GKS_EMAIL_BCC2;
  global $GKS_EMAIL_BCC3;
  global $GKS_EMAIL_HOST;
  global $GKS_EMAIL_PORT;
  global $GKS_EMAIL_SMTPAUTH;
  global $GKS_EMAIL_USERNAME;
  global $GKS_EMAIL_PASSWORD;
  
  global $gks_mymail_last_email;
  
  $gks_mymail_last_email=0;
  
  
  mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
  $charid = strtoupper(md5(uniqid(rand(), true)));
  $hyphen = ''; //chr(45);// "-"
  $guid = substr($charid, 0, 8)
      .substr($charid, 8, 4)
      .substr($charid,12, 4)
      .substr($charid,16, 4)
      .substr($charid,20,12);
  $guid = strtolower($guid);  
  
  $callback_img=GKS_SITE_URL.'my/email.png/?id='.$guid;
  $callback_img='<img src="'.$callback_img.'" width="1" height="1" alt="small image"/>';
  $body=str_replace('</body>', $callback_img.'</body>', $body);
 
   
  //echo '<pre>';  echo $from_name.'|'.$from.'|'.$GKS_SITE_EMAIL;  die();
  
  if ($from == '') {$from=$GKS_SITE_EMAIL;}
  if ($from_name == '' && $from==$GKS_SITE_EMAIL) {
    $from_name=$GKS_SITE_HUMAN_NAME; 
  } else {
    if ($from_name=='') {
      $from_name=$GKS_SITE_HUMAN_NAME;
    }
  }
  
  
  if ($to == '') {$to=$GKS_SITE_EMAIL;}
  if ($to_name == '' && $to==$GKS_SITE_EMAIL) {$to_name=$GKS_SITE_HUMAN_NAME; } else {$to_name=$to;}
  
  //echo '<pre>'.$from.'|'.$replyto.'|'.$sender;die();
  if ($replyto == '') {
    if ($sender!='') {
      $replyto = $sender;
    } else {
      if ($from!=$GKS_EMAIL_USERNAME) {
        $replyto = $from;
      } else {
        $replyto = $GKS_SITE_EMAIL;
      }
    }
  }
  
  if ($replyto == $GKS_SITE_EMAIL) {
    $replyto_name = $GKS_SITE_HUMAN_NAME;
  } else {
    $replyto_name = $replyto;
  }
  
  

  
  $mail = new PHPMailer();
  $mail->isSMTP();
  $mail->Host = $GKS_EMAIL_HOST;
  $mail->Port = $GKS_EMAIL_PORT;
  $mail->SMTPAuth = $GKS_EMAIL_SMTPAUTH;
  $mail->Username = $GKS_EMAIL_USERNAME;
  $mail->Password = $GKS_EMAIL_PASSWORD;
  
  //$mail->message_type='alt';// alt, alt_inline, alt_attach, alt_inline_attach, inline
//  echo '<pre>';
//  echo $GKS_EMAIL_HOST."\n";
//  echo $GKS_EMAIL_PORT."\n";
//  echo $GKS_EMAIL_SMTPAUTH."\n";
//  echo $GKS_EMAIL_USERNAME."\n";
//  echo $GKS_EMAIL_PASSWORD."\n";
//  die();
  
  //$mail->setLanguage('el');
  $mail->CharSet='utf-8';
  $mail->setFrom($from, $from_name);
  if ($replyto!='') {
    $mail->addReplyTo($replyto,$replyto_name);
  }
  if ($sender!='') {
    $mail->Sender = $sender;
  }
  
  $to_ar=explode(',',$to);
  if (count($to_ar)>1) {
    foreach ($to_ar as &$value) {
       $mail->addAddress($value, '');  
    } 
  } else {
    $mail->addAddress($to, $to_name);  
  }
  
  $mail->Subject = $subject;
  $mail->msgHTML($body);
  
  if ($GKS_EMAIL_BCC1 != '') {
    $parts=explode(',',$GKS_EMAIL_BCC1); 
    foreach ($parts as $ppemail) {
      if (trim_gks($ppemail)!='') $mail->addBCC(trim_gks($ppemail));
    }} 
    
  if ($GKS_EMAIL_BCC2 != '') {
    $parts=explode(',',$GKS_EMAIL_BCC2); 
    foreach ($parts as $ppemail) {
      if (trim_gks($ppemail)!='') $mail->addBCC(trim_gks($ppemail));
    }} 
  if ($GKS_EMAIL_BCC3 != '') {
    $parts=explode(',',$GKS_EMAIL_BCC3); 
    foreach ($parts as $ppemail) {
      if (trim_gks($ppemail)!='') $mail->addBCC(trim_gks($ppemail));
    }} 

  if ($from!=$GKS_EMAIL_USERNAME) {
    $mail->addCustomHeader('Sender', $GKS_EMAIL_USERNAME);
  }
  
  //$mail->addCustomHeader('List-Unsubscribe', 'mailto:'.$GKS_SITE_EMAIL.'?subject=remove');
  
  $dd=GKS_SITE_URL;
  $dd=str_replace('https://','',$dd);
  $dd=str_replace('http://','',$dd);
  $dd=str_replace('/','',$dd);
  $dd=str_replace('\\','',$dd);
  
  $my_messageid=date('YmdHis').rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).'@'.$dd;
  $mail->MessageID = '<'.$my_messageid.'>'; //<qWJf8lWwymvFhBqqArU6MYRU9e0373TzZqoOSdIHFI@www.gks.gr>
  
  
  foreach ($EmbeddedImages as $fileval) {
      $mail->addEmbeddedImage($fileval[0],$fileval[1]);
  }
  
  foreach ($Attachments as $fileval) {
    //echo '<pre>';print_r($fileval);die();
    if (endwith($fileval[1],'.ics')) {
      $mail->addAttachment($fileval[0],$fileval[1]); //kai auto alla kai to Ical
      $mail->Ical=file_get_contents($fileval[0]);
      // ekana fix sto \my\vendor\phpmailer\phpmailer\src\PHPMailer.php
      
      
      //echo '<pre>';print_r($fileval);die();
      //$mail->ContentType = 'text/calendar'; //This seems to be important for Outlook
      //$mail->addCustomHeader('Content-Type', 'text/calendar');
      //$mail->Ical=str_replace("\r\n","\n", file_get_contents($fileval[0]));
      //echo '<pre>';echo $mail->Ical;die();
      //$mail->AltBody = $mail->Ical.'';
      
      //echo $mail->AltBody; die();
      //$mail->addAttachment($fileval[0],$fileval[1], 'base64', 'text/calendar');
      //$mail->addStringAttachment(file_get_contents($fileval[0]),$fileval[1],'quoted-printable','text/calendar');
      //echo 'ggggg';die();
      //$mail->addStringAttachment(file_get_contents($fileval[0]), $fileval[1], 'base64', 'text/calendar; name="'.$fileval[1].'"'); //This seems to be important for Gmail
      //$mail->addAttachment($fileval[0], $fileval[1], 'quoted-printable', 'text/calendar'); //This seems to be important for Gmail

    } else {
      $mail->addAttachment($fileval[0],$fileval[1]);
    }
  }

  
  
  
  //send the message, check for errors
  $myret = $mail->send();
  if ($myret==false) {
    debug_mail(false,'mymail error ',$mail->ErrorInfo);
  }
   
  
  
  $sql="insert into gks_email (date_add,messageid,
  user_id,model,model_id,myfrom,myfrom_name,replyto,sender,myto,myto_name,subject,body,Attachments,EmbeddedImages,mycc,mybcc,myret,template_id,guid) values (
  now(),
  '".$db_link->escape_string($my_messageid)."',
  
  ".$my_wp_user_id.",
  '".$db_link->escape_string($model)."',
  ".$model_id.",
  '".$db_link->escape_string($from)."',
  '".$db_link->escape_string($from_name)."',
  '".$db_link->escape_string($replyto)."',
  '".$db_link->escape_string($sender)."',
  '".$db_link->escape_string($to)."',
  '".$db_link->escape_string($to_name)."',
  '".$db_link->escape_string($subject)."',
  '".$db_link->escape_string($body)."',
  '".$db_link->escape_string(json_encode($Attachments))."',
  '".$db_link->escape_string(json_encode($EmbeddedImages))."',
  '".$db_link->escape_string(json_encode($mail->getCcAddresses()))."',
  '".$db_link->escape_string(json_encode($mail->getBccAddresses()))."',";
  if ($myret) {
    $sql.='1,';
  } else {
    $sql.='0,';
  }
  $sql.="'".$db_link->escape_string($template)."',
  '".$db_link->escape_string($guid)."')";
  
  $myrun = $db_link->query($sql);
  if (!$myrun) {
    debug_mail(false,'warning on mymail error sql',$sql);
  }
  
  $gks_mymail_last_email = $db_link->insert_id;

  
  return $myret;
}

function send_email_profile($id) {
  global $db_link;
  global $gkIP;
  global $GKS_EMAIL_BCC1;
  
  $sql = "SELECT ".GKS_WP_TABLE_PREFIX."users.*, gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
  gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
  gks_users.ma_odos, gks_users.ma_arithmos, gks_users.orofos, gks_users.ma_perioxi, 
  gks_users.ma_poli, gks_users.ma_tk, 
  gks_users.ma_country_id, gks_users.ma_nomos_id, 
  gks_users.phone_home, gks_users.genisi_date, gks_users.ethnikotita, 
  gks_users.alli_apasxolisi,gks_users.cv_proipiresia, gks_users.cv_spoydes, gks_users.cv_seminaria, gks_users.cv_mitriki_glossa, gks_users.cv_jenes_glosses,
  gks_users.cv_sxesi_me_photografia, 
  gks_users.cv_metaforiko_meso, gks_users.cv_has_bike, gks_users.cv_has_motorcycle, gks_users.cv_has_car,
  gks_users.profilepososto_user, gks_users.profilepososto_job,
  gks_country.country_name, gks_nomoi.nomos_descr, 
  table_last_name.mylast_name, table_first_name.myfirst_name, table_mobile.mymoobile, table_roles.mywp_capabilities,
  gks_users.amka ,gks_users.ama_eam ,gks_users.arithmos_tautoitas ,gks_users.arxi_ekdosis, gks_users.onoma_patera, gks_users.onoma_miteras,
  gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr,gks_users.oikogeniaki_katastasti_id,gks_users.oikogeniaki_katastasti_paidia
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
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='".GKS_WP_TABLE_PREFIX."capabilities'))
  )  AS table_roles ON ".GKS_WP_TABLE_PREFIX."users.ID = table_roles.user_id
  where ".GKS_WP_TABLE_PREFIX."users.id = ".$id;
  	
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'send_email_profile error sql',$sql);
    return false;
  }  
  $row = $result->fetch_assoc();  
  
  

  $message='<p>'.gks_lang('Ενημέρωση προφίλ').'</p>';
  
  $mysubject =  gks_lang('Ενημέρωση προφίλ').': '.$row['display_name'];
  
  
  
  $message.='<p>'.gks_lang('Αποστολή').': '.showDate(time(),'d/m/Y H:i',1).'<br>';
  $message.='IP: <a href="'.GKS_SITE_URL.'/my/admin-stat-ip.php?ip='.$gkIP.'">'.$gkIP.'</a></p>';
  $message.='<p>ID: <b><a href='.GKS_SITE_URL.'/my/admin-users-item.php?id='.$id.'">'.$id.'</a></b><br>';
  
  $message.=gks_lang('Ρόλοι της επαφής').': <b>';
  $user_roles=array();
  if (isset($row['mywp_capabilities']) and $row['mywp_capabilities']!='') {
    $user_roles = unserialize($row['mywp_capabilities']);
  }
  if (isset($user_roles['administrator'])) $message.= '<br>'.gks_lang('Διαχειριστής','part4','userroles');
  if (isset($user_roles['adminmy'])) $message.= '<br>'.gks_lang('Διαχειριστής my','part4','userroles');
  if (isset($user_roles['editor'])) $message.= '<br>'.gks_lang('Αρχισυντάκτης','part4','userroles');
  if (isset($user_roles['subscriber'])) $message.= '<br>'.gks_lang('Συνδρομητής','part4','userroles');
  if (isset($user_roles['contributor'])) $message.= '<br>'.gks_lang('Συνεργάτης','part4','userroles');
  if (isset($user_roles['author'])) $message.= '<br>'.gks_lang('Συντάκτης','part4','userroles');
  if (isset($user_roles['photographer'])) $message.= '<br>'.gks_lang('Φωτογράφος','part4','userroles');

  if (isset($user_roles['logistis'])) $message.= '<br>'.gks_lang('Λογιστής','part4','userroles');
  if (isset($user_roles['driver'])) $message.= '<br>'.gks_lang('Οδηγός','part4','userroles');
  if (isset($user_roles['omadarxis'])) $message.= '<br>'.gks_lang('Ομαδάρχης','part4','userroles');
  if (isset($user_roles['texnikos'])) $message.= '<br>'.gks_lang('Τεχνικός','part4','userroles');
  if (isset($user_roles['ipethinosperioxis'])) $message.= '<br>'.gks_lang('Υπεύθυνος Περιοχής','part4','userroles');
  if (isset($user_roles['xiristismixanimaton'])) $message.= '<br>'.gks_lang('Χειριστής Μηχανημάτων','part4','userroles');
  if (isset($user_roles['findphotos'])) $message.= '<br>'.gks_lang('Find Your Photos','part4','userroles');
  if (isset($user_roles['hrmanager'])) $message.= '<br>'.gks_lang('HR Manager','part4','userroles');
  if (isset($user_roles['babys'])) $message.= '<br>'.gks_lang('Κλινικές','part4','userroles');
  if (isset($user_roles['promitheutis'])) $message.= '<br>'.gks_lang('Προμηθευτής','part4','userroles');
  if (isset($user_roles['apothikarios'])) $message.= '<br>'.gks_lang('Αποθηκάριος','part4','userroles');
  if (isset($user_roles['kalitexnis'])) $message.= '<br>'.gks_lang('Καλλιτέχνης','part4','userroles');
  if (isset($user_roles['tamias'])) $message.= '<br>'.gks_lang('Ταμίας','part4','userroles');
  
  $message.='</b><br>';
  
  
  
  $message.=gks_lang('Όνομα χρήστη').': <b>'.$row['user_login'].'</b><br>';
  $message.=gks_lang('Όνομα').': <b>'.$row['myfirst_name'].'</b><br>';
  $message.=gks_lang('Επίθετο').': <b>'.$row['mylast_name'].'</b><br>';
  $message.=gks_lang('Υποκοριστικό').': <b>'.$row['gks_nickname'].'</b><br>';
  $message.=gks_lang('Προβολή δημοσίως ως').': <b>'.$row['display_name'].'</b><br>';
  $message.=gks_lang('Όνομα πατέρα').': <b>'.$row['onoma_patera'].'</b><br>';
  $message.=gks_lang('Όνομα μητέρας').': <b>'.$row['onoma_miteras'].'</b><br>';
  $message.=gks_lang('Φύλο').': <b>'.($row['gks_sex'] == 1 ? gks_lang('Άρρεν'): '') . ($row['gks_sex'] == 2 ? gks_lang('Θύλη'): '') .'</b><br>';
  $message.=gks_lang('Οικογενειακή Κατάσταση').': <b>'.$row['oikogeniaki_katastasti_descr'].'</b><br>';
  $message.=gks_lang('Παιδιά').': <b>'.$row['oikogeniaki_katastasti_paidia'].'</b><br>';
  
  
  $message.=gks_lang('H φωτογραφία του προφίλ').': ';
  
  $myimgurl = get_user_meta($id, 'wsl_current_user_image', true);
  if ($myimgurl.'' != '') {
    if (substr($myimgurl, 0, 1)=='/') $myimgurl = GKS_SITE_URL.$myimgurl;
    $message.='<br><a href="'.$myimgurl.'"><img src="'.$myimgurl.'" border="0" height="96" width="96"/></a> ';
  }
  $message.='<br>';
 
  $message.=gks_lang('Οι φωτογραφίες').': ';
  $sql="select * from gks_users_photo where user_id=".$id." order by id_user_photo";
  $result_select = $db_link->query($sql);        
  if (!$result_select) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  while ($row_select = $result_select->fetch_assoc()) {
    $photo_url = $row_select['photo_url'];
    $photo_url_thumb = dirname($row_select['photo_url']).'/thumbnail/'.mb_basename($row_select['photo_url']);
    $message.='<br><a href='.GKS_SITE_URL.$photo_url.'"><img height="96" width="96" src='.GKS_SITE_URL.''.$photo_url_thumb.'"></a> ';
  }
  $message.='<br>';
  $message.=gks_lang('Ηλ. διεύθυνση').': <b>'.$row['user_email'].'</b><br>';
  $message.=gks_lang('Κινητό Τηλέφωνο').': <b>'.$row['mymoobile'].'</b><br>';
  $message.=gks_lang('Σταθερό Τηλέφωνο').': <b>'.$row['phone_home'].'</b><br>';
  $message.=gks_lang('Ιστότοπος').': <b><a href="'.$row['user_url'].'">'.$row['user_url'].'</a></b><br>';

  $message.=gks_lang('Οδός').': <b>'.$row['ma_odos'].'</b><br>';
  $message.=gks_lang('Αριθμός').': <b>'.$row['ma_arithmos'].'</b><br>';
  $message.=gks_lang('Όροφος').': <b>'.$row['ma_orofos'].'</b><br>';
  $message.=gks_lang('Περιοχή').': <b>'.$row['ma_perioxi'].'</b><br>';
  $message.=gks_lang('Πόλη').': <b>'.$row['ma_poli'].'</b><br>';
  $message.=gks_lang('TK').': <b>'.$row['ma_tk'].'</b><br>';
  $message.=gks_lang('Χώρα').': <b>'.$row['country_name'].'</b><br>';
  $message.=gks_lang('Νομός').': <b>'.$row['nomos_descr'].'</b><br>';
  $message.=gks_lang('Φορολογική Θέση').': <b>'.$row['fiscal_position_descr'].'</b><br>';
  $message.=gks_lang('Τιμοκατάλογος').': <b>'.$row['pricelist_descr'].'</b><br>';
  $message.=gks_lang('Αριθμός Ταυτότητας').': <b>'.$row['arithmos_tautoitas'].'</b><br>';
  $message.=gks_lang('Αρχή Έκδοσης').': <b>'.$row['arxi_ekdosis'].'</b><br>';
  $message.=gks_lang('ΑΜΚΑ').': <b>'.$row['amka'].'</b><br>';
  $message.=gks_lang('ΑΜΑ - ΕΑΜ').': <b>'.$row['ama_eam'].'</b><br>';

  $message.=gks_lang('Επωνυμία').': <b>'.$row['eponimia'].'</b><br>';
  $message.=gks_lang('Τίτλος').': <b>'.$row['title'].'</b><br>';
  $message.=gks_lang('ΑΦΜ').': <b>'.$row['afm'].'</b><br>';
  $message.=gks_lang('ΔΟΥ').': <b>'.$row['doy'].'</b><br>';
  $message.=gks_lang('Επάγγελμα').': <b>'.$row['epaggelma'].'</b><br>';
  $message.=gks_lang('Ημερομηνία Γέννησης').': <b>';
  if (isset($row['genisi_date']) and $row['genisi_date'] !='') {
    $message.= date('d/m/Y', strtotime($row['genisi_date']));
  }
  $message.='</b><br>';
  
  $message.=gks_lang('Σύντομο βιογραφικό').': <b>'.get_user_meta($id, 'description', true).'</b><br>';
  $message.=gks_lang('Εθνικότητα').': <b>'.$row['ethnikotita'].'</b><br>';
  $message.=gks_lang('Άλλη Απασχόληση').': <b>'.$row['alli_apasxolisi'].'</b><br>';
  $message.=gks_lang('Προϋπηρεσία').': <b>'.$row['cv_proipiresia'].'</b><br>';
  $message.=gks_lang('Σπουδές').': <b>'.$row['cv_spoydes'].'</b><br>';
  $message.=gks_lang('Σεμινάρια').': <b>'.$row['cv_seminaria'].'</b><br>';
  $message.=gks_lang('Μητρική Γλώσσα').': <b>'.$row['cv_mitriki_glossa'].'</b><br>';
  $message.=gks_lang('Ξένες Γλώσσες').': <b>'.$row['cv_jenes_glosses'].'</b><br>';
  $message.=gks_lang('Σχέση με την Φωτογραφία').': <b>'.$row['cv_sxesi_me_photografia'].'</b><br>';
  $message.=gks_lang('Έχω ποδήλατο').': <b>'.($row['cv_has_bike']!=0 ? 'Ναι' : 'Όχι').'</b><br>';
  $message.=gks_lang('Έχω μηχανή').': <b>'.($row['cv_has_motorcycle']!=0 ? 'Ναι' : 'Όχι').'</b><br>';
  $message.=gks_lang('Έχω αυτοκίνητο').': <b>'.($row['cv_has_car']!=0 ? 'Ναι' : 'Όχι').'</b><br>';
  $message.=gks_lang('Άλλο μεταφορικό Μέσο').': <b>'.$row['cv_metaforiko_meso'].'</b><br>';
  $message.=gks_lang('Πλήρες βιογραφικό').': ';
  $sql="select * from gks_users_cv where user_id=".$id." order by id_user_cv";
  $result_select = $db_link->query($sql);        
  if (!$result_select) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  while ($row_select = $result_select->fetch_assoc()) {
    $message.='<br><a href='.GKS_SITE_URL.$row_select['cv_url'].'" target="_blank">'.mb_basename($row_select['cv_url']).' ('.number_format($row_select['mysize']/1024/1024,2,',','.').' MB) </a>';
  }  
  
  $message.='<br>';
  $message.=gks_lang('Τραπεζικοί λογαριασμοί').': <b>';
  $sql_bank_accounts="SELECT gks_bank_accounts.*, gks_banks.bank_descr
  FROM gks_bank_accounts LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank
  WHERE gks_bank_accounts.deleted_from_user=0 AND gks_bank_accounts.user_id=".$id."
  ORDER BY gks_bank_accounts.id_bank_account";
  $result_bank_accounts = $db_link->query($sql_bank_accounts);        
  if (!$result_bank_accounts) {
    debug_mail(false,'error sql',$sql_bank_accounts);
    die('sql error');
  }
  $i = 0;
  while ($row_bank_accounts = $result_bank_accounts->fetch_assoc()) { 
    $i++;
    $message.= '<br>'.gks_lang('IBAN').': ';
                                
    $iban = iban_to_machine_format($row_bank_accounts['IBAN']);
    
    if(verify_iban($iban)) {
      $message.= iban_to_human_format($iban);
    } else {
      $message.= $row_bank_accounts['IBAN'];
    }
    
    $message.= '<br>'.
    gks_lang('Τράπεζα').': '.$row_bank_accounts['bank_descr'].'<br>'.
    gks_lang('Δικαιούχος').': '.$row_bank_accounts['account_dikaiouxos'];
  }  
  
  $message.='</b><br>';
  $message.=gks_lang('Newsletter').': ';
  
  $user_email = trim_gks($row['user_email'].'');
  $user_mobile = trim_gks($row['mymoobile'].'');
  
  
                            $nl_emails=array();
                            if ($user_email!='') {
                              $sql_nl_emails="select * from gks_newsletter_emails where myemail like '".$db_link->escape_string($user_email)."'";
                              $result_nl_emails = $db_link->query($sql_nl_emails);        
                              if (!$result_nl_emails) {
                                debug_mail(false,'error sql',$sql_nl_emails);
                                die('sql error');
                              }
                              while ($row_nl_emails = $result_nl_emails->fetch_assoc()) {
                                $nl_emails[$row_nl_emails['newsletter_list_id']] = $row_nl_emails['isapproval'];
                              }
                            }
                            $nl_sms=array();
                            if ($user_mobile!='') {
                              $sql_nl_sms="select * from gks_newsletter_sms where mysms like '".$db_link->escape_string($user_mobile)."'";
                              $result_nl_sms = $db_link->query($sql_nl_sms);        
                              if (!$result_nl_sms) {
                                debug_mail(false,'error sql',$sql_nl_sms);
                                die('sql error');
                              }
                              while ($row_nl_sms = $result_nl_sms->fetch_assoc()) {
                                $nl_sms[$row_nl_sms['newsletter_list_id']] = $row_nl_sms['isapproval'];
                              }
                            }
                            
                            
                            $sql_newsletter_lists="select * from gks_newsletter_lists where newsletter_list_disabled=0 order by id_newsletter_list";
                            $result_newsletter = $db_link->query($sql_newsletter_lists);        
                            if (!$result_newsletter) {
                              debug_mail(false,'error sql',$sql_newsletter_lists);
                              die('sql error');
                            }
                            $i = 0;
                            while ($row_newsletter_lists = $result_newsletter->fetch_assoc()) { 
                              $i++;    
                              
                              $message.='<br>&nbsp;&nbsp;'.$row_newsletter_lists['newsletter_list_title']. ' emails: ';
                              if (isset($nl_emails[$row_newsletter_lists['id_newsletter_list']]) and $nl_emails[$row_newsletter_lists['id_newsletter_list']] == 1) 
                                $message.='<b>'.gks_lang('Ναι').'</b>';
                              else 
                                $message.='<b>'.gks_lang('Όχι').'</b>';
                              
                              $message.='<br>&nbsp;&nbsp;'.$row_newsletter_lists['newsletter_list_title']. ' SMS: ';
                              if (isset($nl_sms[$row_newsletter_lists['id_newsletter_list']]) and $nl_sms[$row_newsletter_lists['id_newsletter_list']] == 1) 
                                $message.='<b>'.gks_lang('Ναι').'</b>';
                              else 
                                $message.='<b>'.gks_lang('Όχι').'</b>';
                                 
                            

                        
                        }                            
                              
  
  
  $message.='<br>';

  $message.=gks_lang('Ποσοστό συμπλήρωσης του προφίλ Ως επαφή').': <b>'.$row['profilepososto_user'].'%</b><br>';
  $message.=gks_lang('Ποσοστό συμπλήρωσης του προφίλ Ως συνεργάτης').': <b>'.$row['profilepososto_job'].'%</b><br>';

  
    
  
  $replaces=array();
  $replaces[] = array('[[message]]', $message);

  $params=array(
    'model'=>'profile',
    'model_id'=>$id,
    'to'=>$GKS_EMAIL_BCC1,
    'subject'=>$mysubject,
    'template'=>3, //'empty.html',
    'replaces'=>$replaces,
  );
      
  $send_email_res = gks_mymail_template($params);
  
}
