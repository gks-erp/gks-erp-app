<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

ini_set('max_execution_time', 600);
set_time_limit(600);


putenv("ENV=PRODUCTION");

define('SECURE', 1);
require_once('_current/_config.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');

$my_wp_user_id=2;


//$db_link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
//if ($db_link->connect_error) {
//  debug_mail(false,'DB error',$db_link->connect_errno . '-'.$db_link->connect_error);
//  die();
//}
//$db_link->set_charset('utf8'); 

db_open();


//debug_mail(false,'cron_crm_calendar.php','');

$sql="SELECT id_calendar_notification,notification_type,notification_rundate,
calendar_id,id_calendar,

calendar_title,calendar_start,calendar_end,calendar_allday,
calendar_odos,calendar_arithmos,calendar_orofos,calendar_perioxi,calendar_poli,calendar_tk,
calendar_nomos_id,nomos_descr,
calendar_country_id,country_name,
calendar_map_latitude,calendar_map_longitude,
calendar_user_id,".GKS_WP_TABLE_PREFIX."users.gks_nickname,".GKS_WP_TABLE_PREFIX."users.user_email,
calendar_color,
calendar_is_exclusive,calendar_is_private,
calendar_message,
calendardata
FROM (((gks_calendar_notification
LEFT JOIN gks_calendar ON gks_calendar_notification.calendar_id = gks_calendar.id_calendar)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_calendar.calendar_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_nomoi ON gks_calendar.calendar_nomos_id = gks_nomoi.id_nomos) 
LEFT JOIN gks_country ON gks_calendar.calendar_country_id = gks_country.id_country
WHERE gks_calendar.id_calendar is not null
and gks_calendar.calendar_user_id>=0
AND gks_calendar_notification.notification_rundate<=DATE_ADD(Now(), INTERVAL 6 MINUTE)
AND gks_calendar_notification.notification_rundate>DATE_SUB(now(), INTERVAL 1 HOUR)
AND gks_calendar_notification.notification_send_at Is Null;";

$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}

$rows=array();
while ($row = $result->fetch_assoc()) {
	$rows[$row['notification_type']][$row['calendar_id']][$row['id_calendar_notification']]=$row;
}

//print '<pre>';print_r($rows);die();



//notifications
if (isset($rows['notif'])) {
	foreach ($rows['notif'] as $calendar_id => $simvan) {

//		$sql="delete from gks_notification 
//		where model='calendar' and model_id=".$calendar_id."
//		and has_ok=0 and for_date>=now()";
//		$result = $db_link->query($sql);
//		if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}
		
		
		foreach ($simvan as $row) {
			$calendar_title=$row['calendar_title'];
	 		if (mb_strlen($calendar_title)>50) $calendar_title=mb_substr($calendar_title,0,50).'...';
	 		$message=gks_lang('Ημερολόγιο').' '.myDateTimeFormatw(_time_user(strtotime($row['calendar_start']),1)).'<br>'.
	 	  '<a href="admin-crm-calendar.php?id='.$row['calendar_id'].'">'.$calendar_title.'</a>';
	 		
	 		$sql="select user_id from gks_notification_userperm 
	 		where user_id=".$row['calendar_user_id']." 
	 		and notification_type_id=50 
	 		and from_admin=1 
	 		and from_user=1".gks_notification_userperm_internal_users();
			$result = $db_link->query($sql);
			if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}

      if ($result->num_rows>0) {
  	 		$sql="insert into gks_notification (
  	 		message,sender_id,for_user_id,date_add,for_date,model,model_id
  	 		) values (
  	 		'".$db_link->escape_string($message)."',
  	 		".$row['calendar_user_id'].",
  	 		".$row['calendar_user_id'].",
  	 		now(),
  	 		'".$row['notification_rundate']."',
  	 		'calendar',
  	 		".$row['calendar_id']."
  	 		)";
  			$result = $db_link->query($sql);
  			if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}
  		}
	 		
	 		$sql="update gks_calendar_notification set notification_send_at=now() where id_calendar_notification=".$row['id_calendar_notification'];
			$result = $db_link->query($sql);
			if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}
	 		
	 		
      $sql_viber="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
      FROM gks_notification_userperm 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
      AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
      AND ".GKS_WP_TABLE_PREFIX."users.ID =".$row['calendar_user_id']."
      AND gks_notification_userperm.notification_type_id=50 
      AND gks_notification_userperm.from_admin=1 
      AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();

      $result_viber = $db_link->query($sql_viber);        
      if (!$result_viber) {
        debug_mail(false,'error sql',$sql_viber);
        $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
        echo json_encode($return); die(); }  
      $send_viber=array();
      while ($row_viber = $result_viber->fetch_assoc()) {
        $send_viber[]=$row_viber['viber_id'];
      }
      foreach ($send_viber as $value) {
  	 		$message=gks_lang('Ημερολόγιο').' '.myDateTimeFormatw(_time_user(strtotime($row['calendar_start']),1))."\n".$calendar_title."\n".
  	 	  GKS_SITE_URL.'my/admin-crm-calendar.php?id='.$row['calendar_id'];
        gks_viber_send('calendar', $row['calendar_id'], $value,$message);
      } 	 		
	 		
	 		
	 	}
	}
}


//emails
if (isset($rows['email'])) {
	foreach ($rows['email'] as $calendar_id => $simvan) {
		
		foreach ($simvan as $row) {

			//echo '<pre>';print_r($row);echo '</pre>';//die();
			 
			$user_email=trim_gks($row['user_email']);
			
			if ($user_email!='') {
				if ($row['calendar_allday']!=0) {
					$is_oloimero=gks_lang('Ολοήμερο');
					$apo=myDateTimeFormatw(_time_user(strtotime($row['calendar_start']),1));
					$eos=myDateTimeFormatw(_time_user(strtotime($row['calendar_end']),1));
					
				} else {
					$is_oloimero='';
					$apo=myDateFormatw(_time_user(strtotime($row['calendar_start']),1));
					$eos=myDateFormatw(_time_user(strtotime($row['calendar_end']),1));
				}
				
				$temp=array();
				$tttt=trim_gks(trim_gks($row['calendar_odos']).' '.trim_gks($row['calendar_arithmos']));
				if ($tttt != '') $temp[]= $tttt;
				if (trim_gks($row['calendar_orofos']) != '') $temp[]= trim_gks($row['calendar_orofos']);
				if (trim_gks($row['calendar_perioxi']) != '') $temp[]= trim_gks($row['calendar_perioxi']);
				if (trim_gks($row['calendar_poli']) != '') $temp[]= trim_gks($row['calendar_poli']);
				if (trim_gks($row['calendar_tk']) != '') $temp[]= trim_gks($row['calendar_tk']);
				if (trim_gks($row['nomos_descr']) != '') $temp[]= trim_gks($row['nomos_descr']);
				if (trim_gks($row['country_name']) != '') $temp[]= trim_gks($row['country_name']);
				$topothesia=implode(', ',$temp);
				if ($row['calendar_map_latitude'] != 0 and $row['calendar_map_longitude'] != 0) {
					$topothesia.='<br><a href="https://www.google.com/maps/search/?api=1&query='.$row['calendar_map_latitude'].','.$row['calendar_map_longitude'].'">'.
					$row['calendar_map_latitude'].','.$row['calendar_map_longitude'].'</a>';
				}
				
				
			  $replaces=array();
			  $replaces[] = array('[[id_calendar]]',$row['id_calendar']);
			  $replaces[] = array('[[message]]','');
			  $replaces[] = array('[[is_oloimero]]',$is_oloimero);
			  $replaces[] = array('[[apo]]',$apo);
			  $replaces[] = array('[[eos]]',$eos);
			  $replaces[] = array('[[perigrafi]]',nl2br_gks($row['calendar_message']));
			  $replaces[] = array('[[topothesia]]',$topothesia);
			  
			  $calendardata=$row['calendardata'];
			  $Attachments=array();
			  $myfilepath='';
			  if ($calendardata!='') {
			    
			    $myfilename='invite.ics'; //showDate(strtotime($row['calendar_start']),'Y-m-d_H-i',1).'.ics'; //'invite.ics';
			    //print $myfilename; die();
			    $myfilepath=GKS_SITE_PATH.'tmp/'.$myfilename;
			    if (file_exists($myfilepath)) {
			      @unlink($myfilepath);  
			    }
			    if (file_exists($myfilepath)==false) {
			      file_put_contents($myfilepath,$calendardata);
			      $Attachments[]=array($myfilepath,$myfilename);
			    }
			  }
			  //print $calendardata; die();
			  
			  $sql="select user_id from gks_notification_userperm 
			  where user_id=".$row['calendar_user_id']." 
			  and notification_type_id=50 
			  and from_admin=1 
			  and to_email=1".gks_notification_userperm_internal_users();
  			$result = $db_link->query($sql);
  			if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}
  
        if ($result->num_rows>0) {
  			  $params=array(
  			    'model'=>'calendar',
  			    'model_id'=>$row['id_calendar'],
  			    'to'=> $user_email, //'goutoudis@gmail.com',
  			    'subject'=>$row['calendar_title'],
  			    'template'=> 5, //'calendar_notification.html',
  			    'replaces'=>$replaces,
  			    'Attachments' => $Attachments,
  			  );
  			  //$mailer->addAttachment('/path/to/your/file/schedule.ics', 'alternativename.ics', 'base64', 'text/calendar');
  			  
  			  //echo '<pre>';print_r($params);echo '</pre>';
  			   
  			  $send_email_res = gks_mymail_template($params);
  			  
  			  if ($send_email_res) {
  			 		$sql="update gks_calendar_notification set notification_send_at=now() where id_calendar_notification=".$row['id_calendar_notification'];
  					$result = $db_link->query($sql);
  					if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}
  			  }
  			}
			  
			  if ($myfilepath!='' and file_exists($myfilepath)) {
			    @unlink($myfilepath);
			  }
			  
			  //echo '<pre>';echo $send_email_res; echo '</pre>'; 
			}
		}
	}
}


//echo gks_notification_userperm_internal_users();die();

//gks_crm_activity
$sql="SELECT gks_crm_activity.id_crm_activity, 
gks_crm_activity.activity_duedate, 
gks_crm_activity.activity_type_id, 
gks_crm_activity_types.crm_activity_type_descr, 
gks_crm_activity.activity_color, 
gks_crm_activity.activity_subject, 
gks_crm_activity.activity_message, 
gks_crm_activity.activity_user_id, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
".GKS_WP_TABLE_PREFIX."users.user_email,
".GKS_WP_TABLE_PREFIX."users.viber_id,
".GKS_WP_TABLE_PREFIX."users.viber_subscribed,
gks_crm_activity.activity_model,
gks_crm_activity.activity_model_id,
gks_crm_activity_objects.crm_activity_object_descr,
gks_crm_activity_objects.crm_activity_object_page
FROM ((gks_crm_activity 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_activity.activity_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_crm_activity_types ON gks_crm_activity.activity_type_id = gks_crm_activity_types.id_crm_activity_type)
LEFT JOIN gks_crm_activity_objects ON gks_crm_activity.activity_model = gks_crm_activity_objects.crm_activity_object_code

WHERE gks_crm_activity.activity_status='050new'
AND gks_crm_activity.activity_notification=1
AND gks_crm_activity.activity_notification_send_at Is Null
AND gks_crm_activity.activity_user_id>0
AND gks_crm_activity.activity_duedate<=DATE_ADD(Now(), INTERVAL 6 MINUTE)
AND gks_crm_activity.activity_duedate>DATE_SUB(now(), INTERVAL 1 HOUR)
order by gks_crm_activity.activity_duedate";


$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}

$rows=array();
$objects=array();
while ($row = $result->fetch_assoc()) {
	$rows[]=$row;
  if (empty($row['activity_model'])== false and $row['activity_model_id']>0) {
    if (isset($objects[$row['activity_model']])==false) $objects[$row['activity_model']]=array();
    
    if (isset($objects[$row['activity_model']][$row['activity_model_id']])==false) {
      $objects[$row['activity_model']][$row['activity_model_id']]=array();
    }
  }	
}
gks_get_activity_objects($objects);
//echo '<pre>';print_r($objects);print_r($rows);die();


foreach ($rows as $row) {

	$sql="update gks_crm_activity set activity_notification_send_at=now() 
	where id_crm_activity=".$row['id_crm_activity'];
	$result = $db_link->query($sql);
	if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}


  $row['viber_id']=trim_gks($row['viber_id']);
  $row['viber_subscribed']=intval($row['viber_subscribed']);
  $row['user_email']=trim_gks($row['user_email']);
  

  $activity_subject=$row['activity_subject']; 
  $activity_message=$row['activity_message']; 


  $obj_name='';
  if (isset($objects[$row['activity_model']][$row['activity_model_id']]['obj_name'])) {
    $obj_name=$objects[$row['activity_model']][$row['activity_model_id']]['obj_name'];
  } else if (isset($row['crm_activity_object_descr']) and $row['activity_model_id']>0) { 
    $obj_name=$row['crm_activity_object_descr'].' id:'.$row['activity_model_id'];
  } else if (isset($row['crm_activity_object_descr'])) {
    $obj_name=$row['crm_activity_object_descr'];
  } else {
    $obj_name=$row['activity_model'];
  }
  
  $obj_link='';
  if (isset($row['crm_activity_object_page']) and $row['activity_model_id']>0) {
    $obj_link=str_replace('%s',$row['activity_model_id'],$row['crm_activity_object_page']);
  }
  
  $contact_name='';
  $contact_url='';
  if (isset($objects[$row['activity_model']][$row['activity_model_id']]['contact_name'])) {
    $contact_name=$objects[$row['activity_model']][$row['activity_model_id']]['contact_name'];
    if ($objects[$row['activity_model']][$row['activity_model_id']]['contact_id']>0) {
      $contact_url='admin-users-item.php?id='.$objects[$row['activity_model']][$row['activity_model_id']]['contact_id'];
    }
  }
  $esoda='';
  if (isset($objects[$row['activity_model']][$row['activity_model_id']]['esoda']) and $objects[$row['activity_model']][$row['activity_model_id']]['esoda']!=0) {
    $esoda=myCurrencyFormat($objects[$row['activity_model']][$row['activity_model_id']]['esoda']);
  }
    
//  echo '<pre>';
//  echo $obj_name;
//  echo "\r\n";
//  echo $obj_link;
//  echo "\r\n";
//  echo $contact_name;
//  echo "\r\n";
//  echo $contact_url;
//  echo "\r\n";
//  echo $esoda;
//  echo '</pre>';
//  die();
  
	$sql="select user_id from gks_notification_userperm 
	where user_id=".$row['activity_user_id']." 
	and notification_type_id=20
	and from_admin=1
	and from_user=1".gks_notification_userperm_internal_users();
	$result = $db_link->query($sql);
	if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}

  if ($result->num_rows>0) {
  	$message_notif=gks_lang('Δραστηριότητα').' '.myDateTimeFormatw(_time_user(strtotime($row['activity_duedate']),1)).'<br>'.
    '<a href="admin-crm-activity.php?id='.$row['id_crm_activity'].'">'.($activity_subject!=''? $activity_subject : '('.gks_lang('χωρίς θέμα').')').'</a>';
    if ($activity_message!='') $message_notif.='<br>'.nl2br($activity_message);
    if ($obj_name!='') {
      $message_notif.='<br>'.
      gks_lang('Αντικείμενο').': ';
      if ($obj_link!='') {
        $message_notif.= '<a href="'.$obj_link.'">'.$obj_name.'</a>';
      } else {
        $message_notif.= $obj_name;
      }
    }
    if ($contact_name!='') {
      $message_notif.='<br>'.
      gks_lang('Επαφή').': ';
      if ($contact_url!='') {
        $message_notif.= '<a href="'.$contact_url.'">'.$contact_name.'</a>';
      } else {
        $message_notif.= $contact_name;
      }
    }
    if ($esoda!='') {
      $message_notif.='<br>'.
      gks_lang('Αναμενόμενα έσοδα').': '.$esoda;
    }
    
    

 		$sql="insert into gks_notification (
 		message,sender_id,for_user_id,date_add,for_date,model,model_id
 		) values (
 		'".$db_link->escape_string($message_notif)."',
 		".$row['activity_user_id'].",
 		".$row['activity_user_id'].",
 		now(),
 		'".$row['activity_duedate']."',
 		'activity',
 		".$row['id_crm_activity']."
 		)";
		$result = $db_link->query($sql);
		if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}
	}
	
	if ($row['viber_id']!='' and $row['viber_subscribed']==1) {

    $sql="select user_id from gks_notification_userperm 
  	where user_id=".$row['activity_user_id']." 
  	and notification_type_id=20 
  	and from_admin=1 
  	and to_viber=1".gks_notification_userperm_internal_users();
  
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
      echo json_encode($return); die(); }  
    if ($result->num_rows>0) {
    	$message_viber=gks_lang('Δραστηριότητα')."\n".myDateTimeFormatw(_time_user(strtotime($row['activity_duedate']),1))."\n".
      GKS_SITE_URL.'my/admin-crm-activity.php?id='.$row['id_crm_activity'];
      
      if ($activity_subject!='') $message_viber.="\n".'*'.$activity_subject.'*';
      if ($activity_message!='') $message_viber.="\n".$activity_message;

      if ($obj_name!='') {
        $message_viber.="\n".
        gks_lang('Αντικείμενο').': *'.$obj_name."*\n";
        if ($obj_link!='') {
          $message_viber.= GKS_SITE_URL.'my/'.$obj_link;
        }
      }
      if ($contact_name!='') {
        $message_viber.="\n".
        gks_lang('Επαφή').': *'.$contact_name."*\n";
        if ($obj_link!='') {
          $message_viber.= GKS_SITE_URL.'my/'.$contact_url;
        }
      }
      if ($esoda!='') {
        $message_viber.="\n".
        gks_lang('Αναμενόμενα έσοδα').': *'.str_replace('&euro;','€',$esoda).'*';
      }
      gks_viber_send('activity', $row['id_crm_activity'], $row['viber_id'],$message_viber);
    }
  }

	
	if ($row['user_email']!='') {
    $sql="select user_id from gks_notification_userperm 
    where user_id=".$row['activity_user_id']." 
    and notification_type_id=20 
    and from_admin=1 
    and to_email=1".gks_notification_userperm_internal_users();
  	$result = $db_link->query($sql);
  	if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}
  
    if ($result->num_rows>0) {
    	$message_email_subject=gks_lang('Δραστηριότητα').' #'.$row['id_crm_activity'];
    	if ($activity_subject!='') $message_email_subject.=', '.$activity_subject;
    	$message_email_body=gks_lang('Δραστηριότητα').'<br>'.myDateTimeFormatw(_time_user(strtotime($row['activity_duedate']),1)).'<br>'.
      '<a href="'.GKS_SITE_URL.'my/admin-crm-activity.php?id='.$row['id_crm_activity'].'">'.($activity_subject!=''? $activity_subject : '('.gks_lang('χωρίς θέμα').')').'</a>';
      if ($activity_message!='') $message_email_body.= '<br>'.nl2br($activity_message);
      if ($obj_name!='') {
        $message_email_subject.=', '.$obj_name;
        $message_email_body.='<br>'.
        gks_lang('Αντικείμενο').': ';
        if ($obj_link!='') {
          $message_email_body.= '<a href="'.GKS_SITE_URL.'my/'.$obj_link.'">'.$obj_name.'</a>';
        } else {
          $message_email_body.= $obj_name;
        }
      }
      if ($contact_name!='') {
        $message_email_subject.=', '.$contact_name;
        $message_email_body.='<br>'.
        gks_lang('Επαφή').': ';
        if ($obj_link!='') {
          $message_email_body.= '<a href="'.GKS_SITE_URL.'my/'.$contact_url.'">'.$contact_name.'</a>';
        } else {
          $message_email_body.= $contact_name;
        }
      }
      if ($esoda!='') {
        $message_email_body.='<br>'.
        gks_lang('Αναμενόμενα έσοδα').': '.$esoda;
      }
            
      $replaces=array();
      $replaces[] = array('[[message]]',$message_email_body);

  	  $params=array(
  	    'model'=>'activity',
  	    'model_id'=>$row['id_crm_activity'],
  	    'to'=> $row['user_email'],
  	    'subject'=>$message_email_subject ,
  	    'template'=> 3, //empty
  	    'replaces'=> $replaces,
  	    'Attachments' => [],
  	  );
  	  $send_email_res = gks_mymail_template($params);
  	}
  }
  	
}
	 		

//print '<pre>';print_r($rows);die();


