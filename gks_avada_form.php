<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');

//die(json_encode(array('status'  => 'success','info' => 'It is NOT OK !!!!!!! ')));
//die(json_encode(array('status'  => 'error','info' => 'It is NOT OK !!!!!!! ')));

//
//echo 'OK'; die();

if (isset($_POST['gks_source_url'])==false and isset($_GET['gks_source_url'])==false) {
  if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
  if (isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
  parse_str($HTTP_RAW_POST_DATA, $output);
  if (is_array($output) and isset($output['gks_source_url'])) {
    $_POST=$output;
  }
}
if (isset($_POST['gks_source_url'])==false and isset($_GET['gks_source_url'])) $_POST=$_GET;


if (isset($_POST['gks_source_url'])==false or trim_gks($_POST['gks_source_url'])=='') {

  debug_mail(false,'SPAM avada form','');
  $res = array('status'  => 'error','info' => 'Data Error');
  echo json_encode($res);
  die();  
}

debug_mail(false,'OK avada form','');


$my_page_title=gks_lang('Αποθήκευση Φόρμας από Avada');
db_open();
stat_record();


$fields_manage=array(
  'form_id','time','source_url','gks_source_url','post_id','user_id','user_agent',
  'ip','is_read','privacy_scrub_date','on_privacy_scrub',
  'company_id','company_sub_id','esoda',
  'first_name','last_name','email','mobile','phone','web',
  'odos','arithmos','orofos','perioxi','poli','tk','nomos','nomos_id','country','country_id',
  'map_latitude','map_longitude',
  'subject','message',
  'birthday','user_lang',
  'eponimia','title','afm','doy','epaggelma','lead_color',
  'assigned_id','crm_channel_id','crm_channel_contact_id','crm_channel_campain_id','crm_channel_code','crm_channel_text','crm_channel_url',
  'gkssourlgid',
);

$extra_fieds='';
foreach ($_POST as $mykey => $mvalue) {
  if (in_array($mykey,$fields_manage)==false) {
    if (is_array($mvalue)) {
      if (count($mvalue) == 1) {
        $extra_fieds.=$mykey.': '.$mvalue[0]."\r\n";
      } else {
        $extra_fieds.=$mykey.': '.implode(', ',$mvalue)."\r\n";
      }
    } else {
      $extra_fieds.=$mykey.': '.$mvalue."\r\n";
    }
    
  }
} 
if ($extra_fieds!='') $extra_fieds=substr($extra_fieds, 0, strlen($extra_fieds)-2);

$user_id=0;//$my_wp_user_id;
if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);

$ip=$gkIP;
if (isset($_POST['ip']) and trim_gks($_POST['ip'])!='') $ip=trim_gks($_POST['ip']);

$company_id=1;if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$company_sub_id=0;if (isset($_POST['company_sub_id'])) $company_sub_id=intval($_POST['company_sub_id']);
if ($company_id>0) {
  $sql2="select id_company from gks_company where id_company=".$company_id;
  $result2 = $db_link->query($sql2);  
  if (!$result2) {
    debug_mail(false,'error sql',$sql2);
    $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
    echo json_encode($return); die(); }  
  if ($result2->num_rows==0) $company_id=0;
}
if ($company_id==0) $company_sub_id=0;
if ($company_id>0 and $company_sub_id>0) {
  $sql2="select id_company_sub from gks_company_subs where company_id=".$company_id." and id_company_sub=".$company_sub_id;
  $result2 = $db_link->query($sql2);  
  if (!$result2) {
    debug_mail(false,'error sql',$sql2);
    $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
    echo json_encode($return); die(); }  
  if ($result2->num_rows==0) $company_sub_id=0;
}
//$return = array('status'  => 'error','info' => $company_id.'|'.$company_sub_id);echo json_encode($return); die();   



$sqlF='';$sqlV='';

$sqlF.="mydate_add,";$sqlV.="now(),";
$sqlF.="mydate_edit,";$sqlV.="now(),";
$sqlF.="user_id_add,";$sqlV.=$user_id.",";
$sqlF.="user_id_edit,";$sqlV.=$user_id.",";
$sqlF.="myip,";$sqlV.="'".$db_link->escape_string($ip)."',";
$sqlF.="lead_status_id,";$sqlV.="1,";
$sqlF.="lead_date,";$sqlV.="now(),";
$sqlF.="company_id,";$sqlV.=$company_id.",";
$sqlF.="company_sub_id,";$sqlV.=$company_sub_id.",";

if (isset($_POST['form_id'])) {
  $form_id=intval($_POST['form_id']);
  if ($form_id>0) {
    $sqlF.="form_id,";$sqlV.=$form_id.",";
  
    $sql2="select post_title from ".GKS_WP_TABLE_PREFIX."posts where ID=".$form_id;
    $result2 = $db_link->query($sql2);  
    if (!$result2) {
      debug_mail(false,'error sql',$sql2);
      $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
      echo json_encode($return); die(); }  
    if ($result2->num_rows>=1) {
      $row2 = $result2->fetch_assoc();
      $form_name=trim_gks($row2['post_title']);
      if ($form_name!='') {
        $sqlF.="form_name,";$sqlV.="'".$db_link->escape_string($form_name)."',";
      }
    }
  }
}  


if (isset($_POST['gks_source_url']) and trim_gks($_POST['gks_source_url'])!='') {
  $sqlF.="source_url,";$sqlV.="'".$db_link->escape_string(rawurldecode($_POST['gks_source_url']))."',";
} else if (isset($_POST['source_url']) and trim_gks($_POST['source_url'])!='') {
  $sqlF.="source_url,";$sqlV.="'".$db_link->escape_string($_POST['source_url'])."',";
}  

if (isset($_POST['post_id'])) {
  $post_id=intval($_POST['post_id']);
  if ($post_id>0) {
    $sqlF.="post_id,";$sqlV.=$post_id.",";
  
    $sql2="select post_title from ".GKS_WP_TABLE_PREFIX."posts where ID=".$post_id;
    $result2 = $db_link->query($sql2);  
    if (!$result2) {
      debug_mail(false,'error sql',$sql2);
      $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
      echo json_encode($return); die(); }  
    if ($result2->num_rows>=1) {
      $row2 = $result2->fetch_assoc();
      $post_name=trim_gks($row2['post_title']);
      if ($form_name!='') {
        $sqlF.="post_name,";$sqlV.="'".$db_link->escape_string($post_name)."',";
      }
    }
  }
}
$sqlF.="user_id,";$sqlV.=$user_id.",";
if (isset($_POST['user_agent'])) {$sqlF.="user_agent,";$sqlV.="'".$db_link->escape_string($_POST['user_agent'])."',";}
if (isset($_POST['first_name'])) {$sqlF.="first_name,";$sqlV.="'".$db_link->escape_string($_POST['first_name'])."',";}
if (isset($_POST['last_name'])) {$sqlF.="last_name,";$sqlV.="'".$db_link->escape_string($_POST['last_name'])."',";}
if (isset($_POST['email'])) {$sqlF.="email,";$sqlV.="'".$db_link->escape_string($_POST['email'])."',";}
if (isset($_POST['mobile'])) {$sqlF.="mobile,";$sqlV.="'".$db_link->escape_string($_POST['mobile'])."',";}
if (isset($_POST['phone'])) {$sqlF.="phone,";$sqlV.="'".$db_link->escape_string($_POST['phone'])."',";}
if (isset($_POST['web'])) {$sqlF.="web,";$sqlV.="'".$db_link->escape_string($_POST['web'])."',";}
if (isset($_POST['odos'])) {$sqlF.="odos,";$sqlV.="'".$db_link->escape_string($_POST['odos'])."',";}
if (isset($_POST['arithmos'])) {$sqlF.="arithmos,";$sqlV.="'".$db_link->escape_string($_POST['arithmos'])."',";}
if (isset($_POST['orofos'])) {$sqlF.="orofos,";$sqlV.="'".$db_link->escape_string($_POST['orofos'])."',";}
if (isset($_POST['perioxi'])) {$sqlF.="perioxi,";$sqlV.="'".$db_link->escape_string($_POST['perioxi'])."',";}
if (isset($_POST['poli'])) {$sqlF.="poli,";$sqlV.="'".$db_link->escape_string($_POST['poli'])."',";}
if (isset($_POST['tk'])) {$sqlF.="tk,";$sqlV.="'".$db_link->escape_string($_POST['tk'])."',";}
if (isset($_POST['nomos'])) {$sqlF.="nomos,";$sqlV.="'".$db_link->escape_string($_POST['nomos'])."',";}
if (isset($_POST['nomos_id'])) {$sqlF.="nomos_id,";$sqlV.=intval($_POST['nomos_id']).",";}
if (isset($_POST['country'])) {$sqlF.="country,";$sqlV.="'".$db_link->escape_string($_POST['country'])."',";}

$country_id=91;if (isset($_POST['country_id'])) $country_id=intval($_POST['country_id']);
if ($country_id<=0) $country_id=91;
$sqlF.="country_id,";$sqlV.=$country_id.",";

if (isset($_POST['map_latitude'])) {$sqlF.="map_latitude,";$sqlV.=number_format(floatval($_POST['map_latitude']),8,'.','').",";}
if (isset($_POST['map_longitude'])) {$sqlF.="map_longitude,";$sqlV.=number_format(floatval($_POST['map_longitude']),8,'.','').",";}
if (isset($_POST['subject'])) {$sqlF.="subject,";$sqlV.="'".$db_link->escape_string($_POST['subject'])."',";}
$message='';
if (isset($_POST['message']) or $extra_fieds!='') {
  if (isset($_POST['message'])) $message=trim_gks($_POST['message']);
  if ($extra_fieds!='') {
    if ($message=='') $message= $extra_fieds;
    else $message.="\r\n\r\n".$extra_fieds;
  }
  
  $sqlF.="message,";$sqlV.="'".$db_link->escape_string($message)."',";
}



if (isset($_POST['birthday'])) {
  $birthday='';
  $value=trim_gks($_POST['birthday']);
  $parts=explode('-',$value);
  if (count($parts)==3) {
    if (strlen($parts[0])==4 and strlen($parts[1])>=1 and strlen($parts[1])<=2 and strlen($parts[2])>=1 and strlen($parts[2])<=2) {
      $birthday=$parts[0].'-'.$parts[1].'-'.$parts[2];
    } else if (strlen($parts[2])==4 and strlen($parts[0])>=1 and strlen($parts[0])<=2 and strlen($parts[1])>=1 and strlen($parts[1])<=2) {
      $birthday=$parts[2].'-'.$parts[1].'-'.$parts[0];
    }
  } else {
    $parts=explode('\\',$value);
    if (count($parts)==3) {
      if (strlen($parts[0])==4 and strlen($parts[1])>=1 and strlen($parts[1])<=2 and strlen($parts[2])>=1 and strlen($parts[2])<=2) {
        $birthday=$parts[0].'-'.$parts[1].'-'.$parts[2];
      } else if (strlen($parts[2])==4 and strlen($parts[0])>=1 and strlen($parts[0])<=2 and strlen($parts[1])>=1 and strlen($parts[1])<=2) {
        $birthday=$parts[2].'-'.$parts[1].'-'.$parts[0];
      }
    } 
  }
  if ($birthday!='') {
    $sqlF.="birthday,";$sqlV.="'".$db_link->escape_string($birthday)."',";
  }
}
if (isset($_POST['esoda'])) {$sqlF.="esoda,";$sqlV.=number_format(floatval($_POST['esoda']),8,'.','').",";}


$user_lang='el-GR';if (isset($_POST['user_lang'])) $user_lang=trim_gks($_POST['user_lang']);
$sqlF.="user_lang,";$sqlV.="'".$db_link->escape_string($user_lang)."',";

if (isset($_POST['eponimia'])) {$sqlF.="eponimia,";$sqlV.="'".$db_link->escape_string($_POST['eponimia'])."',";}
if (isset($_POST['title'])) {$sqlF.="title,";$sqlV.="'".$db_link->escape_string($_POST['title'])."',";}
if (isset($_POST['afm'])) {$sqlF.="afm,";$sqlV.="'".$db_link->escape_string($_POST['afm'])."',";}
if (isset($_POST['doy'])) {$sqlF.="doy,";$sqlV.="'".$db_link->escape_string($_POST['doy'])."',";}
if (isset($_POST['epaggelma'])) {$sqlF.="epaggelma,";$sqlV.="'".$db_link->escape_string($_POST['epaggelma'])."',";}
if (isset($_POST['lead_color'])) {$sqlF.="lead_color,";$sqlV.="'".$db_link->escape_string($_POST['lead_color'])."',";}


if (isset($_POST['assigned_id'])) {$sqlF.="assigned_id,";$sqlV.=intval($_POST['assigned_id']).",";}
if (isset($_POST['crm_channel_id'])) {$sqlF.="crm_channel_id,";$sqlV.=intval($_POST['crm_channel_id']).",";}
if (isset($_POST['crm_channel_contact_id'])) {$sqlF.="crm_channel_contact_id,";$sqlV.=intval($_POST['crm_channel_contact_id']).",";}
if (isset($_POST['crm_channel_campain_id'])) {$sqlF.="crm_channel_campain_id,";$sqlV.=intval($_POST['crm_channel_campain_id']).",";}
if (isset($_POST['crm_channel_code'])) {$sqlF.="crm_channel_code,";$sqlV.="'".$db_link->escape_string($_POST['crm_channel_code'])."',";}
if (isset($_POST['crm_channel_text'])) {$sqlF.="crm_channel_text,";$sqlV.="'".$db_link->escape_string($_POST['crm_channel_text'])."',";}
if (isset($_POST['crm_channel_url'])) {$sqlF.="crm_channel_url,";$sqlV.="'".$db_link->escape_string($_POST['crm_channel_url'])."',";}


$raw_data=''; if (isset($_POST) and count($_POST)>0) {$sqlF.="raw_data,";$sqlV.="'".$db_link->escape_string(json_encode($_POST))."',";}


if (isset($_POST['gkssourlgid'])) {
  $urlshort_hit_guid=trim_gks($_POST['gkssourlgid']);
  if (strlen($urlshort_hit_guid)==32) {
    $sql_hit="SELECT gks_urlshort_hit.*, gks_urlshort.longurl
    FROM gks_urlshort_hit 
    LEFT JOIN gks_urlshort ON gks_urlshort_hit.urlshort_id = gks_urlshort.id_urlshort
    where gks_urlshort_hit.urlshort_hit_guid='".$db_link->escape_string($urlshort_hit_guid)."'";

    $result_hit = $db_link->query($sql_hit);
    if (!$result_hit) {
      debug_mail(false,'sql error',$sql_hit);
    } else {
      if ($result_hit->num_rows == 0) {
        debug_mail(false,'gks_urlshort_hit not found: '.$_SERVER['REQUEST_URI'],$sql_hit."\n".$_SERVER['REQUEST_URI']);
      } else {
        $row_hit = $result_hit->fetch_assoc();
        
        if (isset($row_hit['assigned_id']) and isset($_POST['assigned_id'])==false) {
          $sqlF.="assigned_id,";$sqlV.=intval($row_hit['assigned_id']).",";
        }
        if (isset($row_hit['crm_channel_id']) and isset($_POST['crm_channel_id'])==false) {
          $sqlF.="crm_channel_id,";$sqlV.=intval($row_hit['crm_channel_id']).",";
        }
        if (isset($row_hit['crm_channel_contact_id']) and isset($_POST['crm_channel_contact_id'])==false) {
          $sqlF.="crm_channel_contact_id,";$sqlV.=intval($row_hit['crm_channel_contact_id']).",";
        }
        if (isset($row_hit['crm_channel_campain_id']) and isset($_POST['crm_channel_campain_id'])==false) {
          $sqlF.="crm_channel_campain_id,";$sqlV.=intval($row_hit['crm_channel_campain_id']).",";
        }
        if (isset($row_hit['longurl']) and isset($_POST['crm_channel_url'])==false) {
          $sqlF.="crm_channel_url,";$sqlV.="'".$db_link->escape_string($row_hit['longurl'])."',";
        }
        if (isset($row_hit['crm_channel_code']) and isset($_POST['crm_channel_code'])==false) {
          $sqlF.="crm_channel_code,";$sqlV.="'".$db_link->escape_string($row_hit['crm_channel_code'])."',";
        }
        if (isset($row_hit['crm_channel_text']) and isset($_POST['crm_channel_text'])==false) {
          $sqlF.="crm_channel_text,";$sqlV.="'".$db_link->escape_string($row_hit['crm_channel_text'])."',";
        }
        
        
        
        if (isset($row_hit['urlshort_id']) and intval($row_hit['urlshort_id'])>0 ) {
          $sqlF.="urlshort_id,";$sqlV.=intval($row_hit['urlshort_id']).",";
        }
        if (isset($row_hit['id_urlshort_hit']) and intval($row_hit['id_urlshort_hit'])>0 ) {
          $sqlF.="urlshort_hit_id,";$sqlV.=intval($row_hit['id_urlshort_hit']).",";
        }      
      }
    }
  }
}

$sqlF=substr($sqlF, 0, strlen($sqlF)-1);
$sqlV=substr($sqlV, 0, strlen($sqlV)-1);

$sql='insert into gks_crm_leads ('.$sqlF.') values ('.$sqlV.')';

$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
  echo json_encode($return); die(); }  

$id = $db_link->insert_id; 

  
$sxolio=gks_lang('Προσθήκη από FrontEnd'); 
if (isset($_POST['gks_source_url']) and trim_gks($_POST['gks_source_url'])!='') {
  $sxolio.='<br><a href="'.rawurldecode($_POST['gks_source_url']).'" target="_blank">'.rawurldecode($_POST['gks_source_url']).'</a>';
} else if (isset($_POST['source_url']) and trim_gks($_POST['source_url'])!='') {
  $sxolio.='<br><a href="'.$_POST['source_url'].'" target="_blank">'.$_POST['source_url'].'</a>';
}

$sql="insert into gks_crm_leads_log (crm_lead_id, add_date,user_id,sxolio) values (
".$id.",now(),".$user_id.",'".$db_link->escape_string($sxolio)."')";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
  echo json_encode($return); die(); }  


//file_put_contents(GKS_SITE_PATH.'tmp/post.txt',print_r($_POST,true));



$mysubject=gks_lang('Νέα Ευκαιρία από το site').' #'.$id;

$message_email=gks_lang('Νέα Ευκαιρία').':<br><a href="'.GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.'">'.GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.'</a><br><br>';
if (isset($_POST['gks_source_url']) and trim_gks($_POST['gks_source_url'])!='') {
  $message_email.=gks_lang('Από').':<br><a href="'.rawurldecode($_POST['gks_source_url']).'" target="_blank">'.rawurldecode($_POST['gks_source_url']).'</a><br><br>';
} else if (isset($_POST['source_url']) and trim_gks($_POST['source_url'])!='') {
  $message_email.=gks_lang('Από').':<br><a href="'.rawurldecode($_POST['source_url']).'" target="_blank">'.$_POST['source_url'].'</a><br><br>';
}
$message_email.='<b>'.gks_lang('Δεδομένα').'</b><br>';
if (isset($_POST['first_name'])) $message_email.=gks_lang('Όνομα').': '.$_POST['first_name'].'<br>';
if (isset($_POST['last_name'])) $message_email.=gks_lang('Επώνυμο').': '.$_POST['last_name'].'<br>';
if (isset($_POST['email'])) $message_email.=gks_lang('email').': '.$_POST['email'].'<br>';
if (isset($_POST['mobile'])) $message_email.=gks_lang('Κινητό').': '.$_POST['mobile'].'<br>';
if (isset($_POST['phone'])) $message_email.=gks_lang('Τηλέφωνο').': '.$_POST['phone'].'<br>';
if (isset($_POST['web'])) $message_email.=gks_lang('Ιστότοπος').': '.$_POST['web'].'<br>';
if (isset($_POST['odos'])) $message_email.=gks_lang('Οδός').': '.$_POST['odos'].'<br>';
if (isset($_POST['arithmos'])) $message_email.=gks_lang('Αριθμός').': '.$_POST['arithmos'].'<br>';
if (isset($_POST['orofos'])) $message_email.=gks_lang('Όροφος').': '.$_POST['orofos'].'<br>';
if (isset($_POST['perioxi'])) $message_email.=gks_lang('Περιοχή').': '.$_POST['perioxi'].'<br>';
if (isset($_POST['poli'])) $message_email.=gks_lang('Πόλη').': '.$_POST['poli'].'<br>';
if (isset($_POST['tk'])) $message_email.=gks_lang('TK').': '.$_POST['tk'].'<br>';
if (isset($_POST['nomos'])) $message_email.=gks_lang('Νομός').': '.$_POST['nomos'].'<br>';
if (isset($_POST['country'])) $message_email.=gks_lang('Χώρα').': '.$_POST['country'].'<br>';
if (isset($_POST['eponimia'])) $message_email.=gks_lang('Επωνυμία').': '.$_POST['eponimia'].'<br>';
if (isset($_POST['title'])) $message_email.=gks_lang('Τίτλος').': '.$_POST['title'].'<br>';
if (isset($_POST['afm'])) $message_email.=gks_lang('ΑΦΜ').': '.$_POST['afm'].'<br>';
if (isset($_POST['doy'])) $message_email.=gks_lang('ΔΟΥ').': '.$_POST['doy'].'<br>';
if (isset($_POST['epaggelma'])) $message_email.=gks_lang('Επάγγελμα').': '.$_POST['epaggelma'].'<br>';



if (isset($_POST['subject'])) $message_email.=gks_lang('Θέμα').': '.$_POST['subject'].'<br>';
if ($message!='') $message_email.=gks_lang('Μήνυμα').': '.str_replace("\r\n", '<br>', $message).'<br>';

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


$message=gks_lang('Νέα Ευκαιρία από το site').' #'.$id;

$message_notification=gks_lang('Νέα Ευκαιρία από το site').': <a href="'.GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.'">#'.$id.'</a>';

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
  gks_viber_send('crm_leads',$id,$value,gks_lang('Νέα Ευκαιρία από το site').' #'.$id."\n".GKS_SITE_URL.'my/admin-crm-lead-item.php?id='.$id.$subject);
}


foreach ($_POST as $mykey => $mvalue) {
  $mykey=trim_gks(mb_strtolower($mykey));
  
  if (startwith($mykey,'myupload')) {

    $parts=explode(' | ', trim_gks($mvalue));
    foreach ($parts as $pval) {


      $myupload=trim_gks($pval);
      if ($myupload!='' and filter_var($myupload, FILTER_VALIDATE_URL)) {
        $sql="insert into gks_crm_leads_links (
          crm_lead_id,url,mydate,ip,user_id
        ) values (
          ".$id.",'".$db_link->escape_string($myupload)."',now(),'".$db_link->escape_string($ip)."',".$user_id."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
          echo json_encode($return); die(); }  
        
        $id_crm_leads_links = $db_link->insert_id; 
        
        
        $fileurl=GKS_SITE_URL.'my/admin-crm-lead-item-link-action_start.php?id='.$id_crm_leads_links;
    
        debug_mail(false,'start auto crm-lead_links','<pre>crm-lead id: '.$id_crm_leads_links."\r\n".'id_crm_leads_links: '.$id_crm_leads_links."\r\n".'url: '.$myupload."\r\n".'fileurl: '.$fileurl.'</pre>');
        
        //echo 'end|'.$url.'|'.$id_order_links.'|'.$order_id;
        //die();
          
        $opts = array(
          'http'=>array(
            'timeout' => 30,  //Seconds  
            'method'=>"POST",
            'header'=>"Content-type: application/x-www-form-urlencoded\r\n" .
                      "Accept-language: en\r\n" ,
          ),
          "ssl"=>array(
              "verify_peer"=>false,
              "verify_peer_name"=>false,
          ),
        );
        
        $context = stream_context_create($opts);
        $file = @file_get_contents($fileurl, false, $context); 
        
      }
    }
  }
    
}

if (file_exists('_custom/gks_avada_form_custom.php')) {
  include_once('_custom/gks_avada_form_custom.php');
}


echo 'Form data is saved to CRM.';
die();

$res = array('status'  => 'success','info' => 'It is OK !');
//or, for error:
//$res = array('status'  => 'error','info' => 'It is NOT OK !!!!!!! ');
//$res = array('status'  => 'error','info' => '<br>'.$sql.' | '.$id);

 
echo json_encode($res);
die();
