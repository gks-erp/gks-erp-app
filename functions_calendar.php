<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



//2016-12-12T20:00:00+00:00
function gks_calendertime($str,$plus_seconds=0) {
    if (!isset($str)) {
        return '';
    }
    //$time = _time_user(strtotime($str), 1);
    $time = strtotime($str);
    $time+=$plus_seconds;
    return date('Y-m-d\TH:i:s', $time).'+00:00';
}

function gks_calendar_event_array($row,$colors_per_user) {
  global $db_link;
  $event=array();
  $event['id'] = 'cal'.$row['id_calendar'];
  $event['rec_id'] = $row['id_calendar'];
  $event['c_table'] = 'gks_calendar';
  $event['title'] = $row['calendar_title'];
  $event['start'] = gks_calendertime($row['calendar_start']);
  $event['end'] =   gks_calendertime($row['calendar_end']);
  $event['allDay']= ($row['calendar_allday'] == 0 ? false : true);
  //$event['display']='auto'; //'none';
  
  if (isset($row['calendar_color']) and strlen(trim_gks($row['calendar_color'])) == 7) {
    $event['backgroundColor']=$row['calendar_color'];
    $event['c_custom_color'] = 1;
  } else {
    $event['c_custom_color'] = 0;
    if (isset($row['calendar_user_id']) and $row['calendar_user_id'] >0 and isset($colors_per_user['cal'][$row['calendar_user_id']])) {
      $event['backgroundColor']=$colors_per_user['cal'][$row['calendar_user_id']];
    }
  }
  $event['c_color'] =            trim_gks($row['calendar_color']);

  
  
  $event['c_user_id_multi'] =  []; 
  $event['c_user_id'] =        intval($row['calendar_user_id']);
  $event['c_gks_nickname'] =     trim_gks($row['gks_nickname']);
  
  $event['c_customer'] =         '';
  $event['c_odos'] =             trim_gks($row['calendar_odos']);
  $event['c_arithmos'] =         trim_gks($row['calendar_arithmos']);
  $event['c_orofos'] =           trim_gks($row['calendar_orofos']);
  $event['c_perioxi'] =          trim_gks($row['calendar_perioxi']);
  $event['c_poli'] =             trim_gks($row['calendar_poli']);
  $event['c_tk'] =               trim_gks($row['calendar_tk']);
  $event['c_nomos_id'] =       intval($row['calendar_nomos_id']);
  $event['c_country_id'] =     intval($row['calendar_country_id']);
  $event['c_map_latitude'] = floatval($row['calendar_map_latitude']);
  $event['c_map_longitude'] =floatval($row['calendar_map_longitude']);
  $event['c_is_exclusive'] =   intval($row['calendar_is_exclusive']);
  $event['c_is_private'] =     intval($row['calendar_is_private']);
  $event['c_message'] =          trim_gks($row['calendar_message']);

  $event['c_event_user_id_add']  = ($row['user_id_add']>0  ? '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'. $row['gks_nickname_add'].'</a>'  : '');
  $event['c_event_user_id_edit'] = ($row['user_id_edit']>0 ? '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>' : '');
  $event['c_event_mydate_add']   = (isset($row['mydate_add'])  ? showDate(strtotime($row['mydate_add']),  'd/m/Y H:i:s', 1) : '');
  $event['c_event_mydate_edit']  = (isset($row['mydate_edit']) ? showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1) : '');
  $event['c_event_myip']         = (isset($row['myip']) ? '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>' : '');


  $event['c_notification']=array();
  foreach ($row['notification'] as $notification) {
  	
		$event['c_notification'][]=array(
			'type' => $notification['notification_type'],
			'number' => intval($notification['notification_number']),
			'unit' => $notification['notification_unit'],
		);
  } 
  
  //print '<pre>';print_r($event['c_notification']);die();
  
  
  $event['c_participant']=array();
  foreach ($row['participant'] as $participant) {
  	
		$event['c_participant'][]=array(
			'user_id' => intval($participant['participant_id']),
			'name' => $participant['gks_nickname'],
			'email' => $participant['user_email'],
			'mobile' => $participant['gks_mobile'],
			'is_org' => intval($participant['is_organizer']),
			'is_opt' => intval($participant['is_optional']),
			'r_type' => trim_gks($participant['response_type']),
			'r_date' => (isset($participant['response_date']) ? showDate(strtotime($participant['response_date']),'d/m/Y H:i',1) : ''),
		);
  }
  
  $event['c_objects']=array();
  //print '<pre>';print_r($row);print '</pre>';
  foreach ($row['objects'] as $object) {
  	if (isset($object['obj_name'])) {
    	$event['c_objects'][]=array(
    	  'obj_name' => $object['obj_name'],
    	  'contact_name' => $object['contact_name'],
    	  'contact_id' => $object['contact_id'],
    	  'esoda' => ($object['esoda']==0 ? '' : myCurrencyFormat($object['esoda'])),
    	);
    }
  }
  
  $event['object_rel']=getObjectRels('gks_calendar',$event['rec_id']);

  
  return $event;
}
function gks_calendar_event_task_array($row,$colors_per_user) {
  global $db_link;
  $event=array();
  $event['id'] = 'task'.$row['id_crm_task'];
  $event['rec_id'] = $row['id_crm_task'];
  $event['c_table'] = 'gks_crm_tasks';
  $event['title'] = $row['subject'];
  $event['start'] = gks_calendertime($row['task_planned_date_from']);
  $event['end'] =   gks_calendertime($row['task_planned_date_to']);
  $event['allDay']= false; //($row['calendar_allday'] == 0 ? false : true);
  
  if (isset($row['task_color']) and strlen(trim_gks($row['task_color'])) == 7) {
    $event['backgroundColor']=$row['task_color'];
    $event['c_custom_color'] = 1;
  } else {
    $event['c_custom_color'] = 0;
    //echo '<pre>';print_r($row['multi_users']);die();
    foreach ($row['multi_users'] as $value) {
      if ($value>0 and isset($colors_per_user['task'][$value])) {
        $event['backgroundColor']=$colors_per_user['task'][$value];
        break;
      }
    } 
  }
  //$event['c_color'] =            trim_gks($row['calendar_color']);

  
  
  $event['c_user_id_multi'] =  $row['multi_users']; 
  $event['c_user_id'] =        0; //intval($row['calendar_user_id']);
  $event['c_gks_nickname'] =   ''; //  trim_gks($row['gks_nickname']);
  
  //print '<pre>';print_r($row);die();
  
  $event['c_customer'] =         trim_gks($row['first_name'].' '.$row['last_name']);
  $event['c_odos'] =             trim_gks($row['odos']);
  $event['c_arithmos'] =         trim_gks($row['arithmos']);
  $event['c_orofos'] =           trim_gks($row['orofos']);
  $event['c_perioxi'] =          trim_gks($row['perioxi']);
  $event['c_poli'] =             trim_gks($row['poli']);
  $event['c_tk'] =               trim_gks($row['tk']);
  $event['c_nomos_id'] =       intval($row['nomos_id']);
  $event['c_country_id'] =     intval($row['country_id']);
  $event['c_map_latitude'] = floatval($row['map_latitude']);
  $event['c_map_longitude'] =floatval($row['map_longitude']);
  $event['c_is_exclusive'] =   0; //intval($row['calendar_is_exclusive']);
  $event['c_is_private'] =     0; //intval($row['calendar_is_private']);
  
  $event['c_message'] = '<a href="admin-crm-task-item.php?id='.$row['id_crm_task'].'">'.gks_lang('Εργασία').': #'.$row['id_crm_task'].'</a><br><b>'.trim_gks($row['subject']).'</b><br>'.trim_gks($row['message']);

  $event['c_event_user_id_add']  = ($row['user_id_add']>0  ? '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'. $row['gks_nickname_add'].'</a>'  : '');
  $event['c_event_user_id_edit'] = ($row['user_id_edit']>0 ? '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>' : '');
  $event['c_event_mydate_add']   = (isset($row['mydate_add'])  ? showDate(strtotime($row['mydate_add']),  'd/m/Y H:i:s', 1) : '');
  $event['c_event_mydate_edit']  = (isset($row['mydate_edit']) ? showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1) : '');
  $event['c_event_myip']         = (isset($row['myip']) ? '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>' : '');


  $event['c_notification']=array();
  $event['c_notification'][]=array(
			'type' => 'notif',
			'number' => 0,
			'unit' => 'minute',
		);

  
  
  $event['c_participant']=array();
//  foreach ($row['participant'] as $participant) {
//  	
//		$event['c_participant'][]=array(
//			'user_id' => intval($participant['participant_id']),
//			'name' => $participant['gks_nickname'],
//			'email' => $participant['user_email'],
//			'mobile' => $participant['gks_mobile'],
//			'is_org' => intval($participant['is_organizer']),
//			'is_opt' => intval($participant['is_optional']),
//			'r_type' => trim_gks($participant['response_type']),
//			'r_date' => (isset($participant['response_date']) ? showDate(strtotime($participant['response_date']),'d/m/Y H:i',1) : ''),
//		);
//  }
  
  $event['c_objects']=array();
//  foreach ($row['objects'] as $object) {
//  	$event['c_objects'][]=array(
//  	  'obj_name' => $object['obj_name'],
//  	  'contact_name' => $object['contact_name'],
//  	  'contact_id' => $object['contact_id'],
//  	  'esoda' => ($object['esoda']==0 ? '' : myCurrencyFormat($object['esoda'])),
//  	);
//  }
  
  $event['object_rel']=getObjectRels('gks_crm_tasks',$event['rec_id']);

  
  return $event;
}


function gks_calendar_event_activity_array($row,$colors_per_user) {
  global $db_link;
  $event=array();
  $event['id'] = 'activ'.$row['id_crm_activity'];
  $event['rec_id'] = $row['id_crm_activity'];
  $event['c_table'] = 'gks_crm_activity';
  $event['title'] = $row['activity_subject'];
  $event['start'] = gks_calendertime($row['activity_duedate']);
  $event['end'] =   gks_calendertime($row['activity_duedate'],30*60);
  $event['allDay']= false; //($row['calendar_allday'] == 0 ? false : true);
  
  //$event['editable']=false;// disable drag & drop + resize
  //$event['eventStartEditable']=false;  // disable only drag 
  $event['durationEditable']=false; // disable only resize  
  
  if (isset($row['activity_color']) and strlen(trim_gks($row['activity_color'])) == 7) {
    $event['backgroundColor']=$row['activity_color'];
    $event['c_custom_color'] = 1;
  } else {
    $event['c_custom_color'] = 0;
    if (isset($row['activity_user_id']) and $row['activity_user_id'] >0 and isset($colors_per_user['activ'][$row['activity_user_id']])) {
      $event['backgroundColor']=$colors_per_user['activ'][$row['activity_user_id']];
    }
  }
  //$event['c_color'] =            trim_gks($row['calendar_color']);

  
  
  $event['c_user_id_multi'] =  []; 
  $event['c_user_id'] =        intval($row['activity_user_id']);
  $event['c_gks_nickname'] =   ''; //  trim_gks($row['gks_nickname']);
  
  //print '<pre>';print_r($row);die();
  
  $event['c_customer'] =         '';
  $event['c_odos'] =             '';
  $event['c_arithmos'] =         '';
  $event['c_orofos'] =           '';
  $event['c_perioxi'] =          '';
  $event['c_poli'] =             '';
  $event['c_tk'] =               '';
  $event['c_nomos_id'] =        0;
  $event['c_country_id'] =      0;
  $event['c_map_latitude'] =    0;
  $event['c_map_longitude'] =   0;
  $event['c_is_exclusive'] =    0; 
  $event['c_is_private'] =      0; 
  
  $type_icon='';
  if (!empty($row['crm_activity_type_icon'])) {
    $type_icon=$row['crm_activity_type_icon'];
    if (trim_gks($row['activity_color'])!='') {
      $type_icon=str_replace(' class="', ' style="color:'.$row['activity_color'].'" class="', $type_icon);
    }
    $type_icon.=' ';
  }
  $bell='';
  if ($row['activity_notification']==1) {
    $bell= '<i class="activity_notification_bell fas fa-bell"></i> ';
  }
      
  $time_icon=$bell; //$bell.secondsago(strtotime($row['activity_duedate']));
  $activity_status='<div class="calendar_activity_status"><span class="activity_status_'.$row['activity_status'].'">'.getActivityStatusDescr($row['activity_status']).'</span></div>';
      
  $event['c_message'] = '<div><a href="admin-crm-activity.php?id='.$row['id_crm_activity'].'">'.gks_lang('Δραστηριότητα').': #'.$row['id_crm_activity'].'</a></div>'.
  '<div>'.$row['gks_nickname_activity'].'</div>'.
  '<div>'.$activity_status.'</div>'.
  '<div>'.$time_icon.'</div>'.
  '<div>'.$type_icon.' '.trim_gks($row['crm_activity_type_descr']).'</div>'.
  '<div><b>'.trim_gks($row['activity_subject']).'</b></div>'.
  '<div>'.trim_gks($row['activity_message']).'</div>';
  $message_notif='';
  if ($row['activity_object']['obj_name']!='') {
    $message_notif.='<br>'.
    gks_lang('Αντικείμενο').': ';
    if ($row['activity_object']['obj_link']!='') {
      $message_notif.= '<a href="'.$row['activity_object']['obj_link'].'">'.$row['activity_object']['obj_name'].'</a>';
    } else {
      $message_notif.= $row['activity_object']['obj_name'];
    }
  }
  if ($row['activity_object']['contact_name']!='') {
    $message_notif.='<br>'.
    gks_lang('Επαφή').': ';
    if ($row['activity_object']['contact_url']!='') {
      $message_notif.= '<a href="'.$row['activity_object']['contact_url'].'">'.$row['activity_object']['contact_name'].'</a>';
    } else {
      $message_notif.= $row['activity_object']['contact_name'];
    }
  }
  if ($row['activity_object']['esoda']!='') {
    $message_notif.='<br>'.
    gks_lang('Αναμενόμενα έσοδα').': '.$row['activity_object']['esoda'];
  }
  if ($message_notif!='') {
    $message_notif=substr($message_notif, 4);
    $event['c_message'].='<div>'.$message_notif.'</div>';
    //$event['c_message'].=print_r($row['activity_object'],true);
  }

  $event['c_event_user_id_add']  = ($row['user_id_add']>0  ? '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'. $row['gks_nickname_add'].'</a>'  : '');
  $event['c_event_user_id_edit'] = ($row['user_id_edit']>0 ? '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>' : '');
  $event['c_event_mydate_add']   = (isset($row['mydate_add'])  ? showDate(strtotime($row['mydate_add']),  'd/m/Y H:i:s', 1) : '');
  $event['c_event_mydate_edit']  = (isset($row['mydate_edit']) ? showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1) : '');
  $event['c_event_myip']         = (isset($row['myip']) ? '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>' : '');


  $event['c_notification']=array();

	
  $event['c_participant']=array();

  
  $event['c_objects']=array();

  
  $event['object_rel']=array(); //getObjectRels('gks_crm_tasks',$event['rec_id']);

  
  return $event;
}

function gks_calendar_sql_event($sql_where) {
  $sql="SELECT gks_calendar.*, 
	".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
	".GKS_WP_TABLE_PREFIX."users.gks_nickname,".GKS_WP_TABLE_PREFIX."users.user_email,
	nomos_descr,country_name
	from ((((gks_calendar
	LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_calendar.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
	LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_calendar.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
	LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_calendar.calendar_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_nomoi ON gks_calendar.calendar_nomos_id = gks_nomoi.id_nomos) 
  LEFT JOIN gks_country ON gks_calendar.calendar_country_id = gks_country.id_country
	
	where ".$sql_where."
	ORDER BY gks_calendar.id_calendar";
  //echo '<pre>';echo $sql;
  return $sql;
}

function gks_crm_tasks_sql_event($sql_where_tasks1,$sql_where_tasks2) {
  
  $sql="SELECT gks_crm_tasks.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  ".GKS_WP_TABLE_PREFIX."pelatis.gks_nickname AS gks_nickname_pelatis, 
  ".GKS_WP_TABLE_PREFIX."pelatis.user_email AS user_email_pelatis, 
  gks_nomoi.nomos_descr, gks_country.country_name,
  gks_crm_tasks_status.task_status_descr, gks_crm_tasks_status.task_status_color
  FROM ".($sql_where_tasks2!='' ? '(' : '')." (((((gks_crm_tasks ";
  if ($sql_where_tasks2!='') {
    $sql.=" LEFT JOIN (
      SELECT gks_crm_tasks_employee.crm_task_id
      FROM gks_crm_tasks_employee
      WHERE gks_crm_tasks_employee.crm_task_employee_id In (".$sql_where_tasks2.")
      GROUP BY gks_crm_tasks_employee.crm_task_id
    )  AS users_tasks ON gks_crm_tasks.id_crm_task = users_tasks.crm_task_id)";
  }
  $sql.=" LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_tasks.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_tasks.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."pelatis ON gks_crm_tasks.user_id = ".GKS_WP_TABLE_PREFIX."pelatis.ID) 
  LEFT JOIN gks_nomoi ON gks_crm_tasks.nomos_id = gks_nomoi.id_nomos) 
  LEFT JOIN gks_country ON gks_crm_tasks.country_id = gks_country.id_country)
  LEFT JOIN gks_crm_tasks_status ON gks_crm_tasks.task_status_id = gks_crm_tasks_status.id_crm_task_status
  WHERE ".($sql_where_tasks2!='' ? ' users_tasks.crm_task_id Is Not Null and ' : '')." 
  ".$sql_where_tasks1."
	ORDER BY gks_crm_tasks.id_crm_task";
  //echo '<pre>';echo $sql;
  return $sql;
}
function gks_crm_activity_sql_event($sql_where_activity) {
  
  $sql="SELECT gks_crm_activity.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  ".GKS_WP_TABLE_PREFIX."users_activity.gks_nickname AS gks_nickname_activity,
  gks_crm_activity_types.crm_activity_type_descr,
  gks_crm_activity_types.crm_activity_type_icon,
  gks_crm_activity_objects.crm_activity_object_descr,
  gks_crm_activity_objects.crm_activity_object_page
  FROM ((((gks_crm_activity
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_activity.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_activity.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_activity ON gks_crm_activity.activity_user_id = ".GKS_WP_TABLE_PREFIX."users_activity.ID) 
  LEFT JOIN gks_crm_activity_types ON gks_crm_activity.activity_type_id = gks_crm_activity_types.id_crm_activity_type)
  LEFT JOIN gks_crm_activity_objects ON gks_crm_activity.activity_model = gks_crm_activity_objects.crm_activity_object_code
  WHERE 
  ".$sql_where_activity."
  ORDER BY gks_crm_activity.activity_duedate";
  // activity_status='050new' and
  //echo '<pre>sssssssss aaaaaa ';echo $sql;die();
  return $sql;
}
/*
function gks_transfer_reservation_sql_event($sql_where_tasks1,$sql_where_tasks2) {
  
  $sql="SELECT gks_transfer_reservation.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, 
  ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  ".GKS_WP_TABLE_PREFIX."pelatis.gks_nickname AS gks_nickname_pelatis, 
  ".GKS_WP_TABLE_PREFIX."pelatis.user_email AS user_email_pelatis, 
  gks_nomoi.nomos_descr, gks_country.country_name
  FROM ".($sql_where_tasks2!='' ? '(' : '')." ((((gks_transfer_reservation ";
  if ($sql_where_tasks2!='') {
    $sql.=" LEFT JOIN (
      SELECT gks_transfer_reservation_oximata.transfer_reservation_id
      FROM gks_transfer_reservation_oximata
      WHERE gks_transfer_reservation_oximata.transfer_oxima_driver_id In (".$sql_where_tasks2.")
      GROUP BY gks_transfer_reservation_oximata.transfer_reservation_id
    )  AS users_tasks ON gks_transfer_reservation.id_transfer_reservation = users_tasks.transfer_reservation_id)";
  }
  $sql.=" LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_transfer_reservation.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_transfer_reservation.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."pelatis ON gks_transfer_reservation.user_id = ".GKS_WP_TABLE_PREFIX."pelatis.ID) 
  LEFT JOIN gks_nomoi ON gks_transfer_reservation.ma_nomos_id = gks_nomoi.id_nomos) 
  LEFT JOIN gks_country ON gks_transfer_reservation.ma_country_id = gks_country.id_country
  WHERE ".($sql_where_tasks2!='' ? ' users_tasks.transfer_reservation_id Is Not Null and ' : '')." 
  ".$sql_where_tasks1."
	ORDER BY gks_transfer_reservation.id_transfer_reservation";
  //echo '<pre>';echo $sql;
  return $sql;
}
*/
function gks_calendar_get_events($params) {
	global $db_link;
	global $my_wp_user_id;
	global $gks_user_settings;
	
	$sql_where='';
	$sql_where_tasks1='';
	$sql_where_tasks2='';
	$sql_where_activity='';
	
	if (isset($params['id_calendar'])) {
	  $sql_where="gks_calendar.id_calendar=".$params['id_calendar'];
	} else if (isset($params['id_crm_task'])) {
	  $sql_where_tasks1="gks_crm_tasks.id_crm_task=".$params['id_crm_task'];
	  $sql_where_tasks2='';
	} else if (isset($params['id_crm_activity'])) {
	  $sql_where_activity="gks_crm_activity.id_crm_activity=".$params['id_crm_activity'];
	} else {
	
  	$range_start=$params['range_start'];
  	$range_end=$params['range_end'];
  	$users=$params['users'];
  	
    
    $where='and gks_calendar.calendar_user_id in ('.implode(',',$users['cal']).')';
    $sql_where=" (
    	(gks_calendar.calendar_start >='".$range_start."' and gks_calendar.calendar_start <'".$range_end."') or 
    	(gks_calendar.calendar_end >'".$range_start."'    and gks_calendar.calendar_end <='".$range_end."') or
    	(gks_calendar.calendar_start <='".$range_start."' and gks_calendar.calendar_end >='".$range_end."')
    	) ".$where;	
    
  	
    $sql_where_tasks1=" (
    	(gks_crm_tasks.task_planned_date_from >='".$range_start."' and gks_crm_tasks.task_planned_date_from <'".$range_end."') or 
    	(gks_crm_tasks.task_planned_date_to >'".$range_start."'    and gks_crm_tasks.task_planned_date_to <='".$range_end."') or
    	(gks_crm_tasks.task_planned_date_from <='".$range_start."' and gks_crm_tasks.task_planned_date_to >='".$range_end."')
    	) ";	
    $sql_where_tasks2=implode(',',$users['task']);
  	
    $where='and gks_crm_activity.activity_user_id in ('.implode(',',$users['activ']).')';
    $sql_where_activity=" (
    	(gks_crm_activity.activity_duedate >='".$range_start."' and gks_crm_activity.activity_duedate <='".$range_end."')
    	) ".$where;	
  	
  	
  }
  $colors_per_user=array('cal' => array(), 'task' => array(), 'activ' => array());
	
	if (isset($gks_user_settings['calendar']['user_color']))        $colors_per_user['cal'][$my_wp_user_id]   = $gks_user_settings['calendar']['user_color'];
	if (isset($gks_user_settings['calendar']['user_color_task']))   $colors_per_user['task'][$my_wp_user_id]  = $gks_user_settings['calendar']['user_color_task'];
	if (isset($gks_user_settings['calendar']['user_color_activ']))  $colors_per_user['activ'][$my_wp_user_id] = $gks_user_settings['calendar']['user_color_activ'];
	
	$sql="select other_user_id,other_user_color,other_myobj from gks_calendar_other_users where this_user_id=".$my_wp_user_id;
	$result = $db_link->query($sql);        
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die();}
	while ($row = $result->fetch_assoc()) {
    $colors_per_user[$row['other_myobj']][$row['other_user_id']]=$row['other_user_color'];
  }
	//echo '<pre>';print_r($colors_per_user);die();
	
	$myout=array();
	
	/////////////////////////////          gks_calendar          /////////////////////////////
	if ($sql_where!='') {
  	$sql=gks_calendar_sql_event($sql_where);
  	
  	$result = $db_link->query($sql);        
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die();}
  	
  	$rows=array();
  	$ids=array();
  	while ($row = $result->fetch_assoc()) {
  	  $ids[]=$row['id_calendar'];
  	  
  	  $row['notification']=array();
  	  $row['participant']=array();
  	  $row['objects']=array();
  	  $row['multi_users']=array();
  	  $rows[$row['id_calendar']]=$row;
  	}
  	
  	
  	if (count($ids)>0) {
  		$sql="SELECT calendar_id,notification_type,notification_number,notification_unit 
  		FROM gks_calendar_notification 
  		WHERE calendar_id In (".implode(',',$ids).")";
  		$result = $db_link->query($sql);        
  		if (!$result) {
  		  debug_mail(false,'error sql',$sql);
  		  $return = array('success' => false, 'message' => base64_encode('sql error'));
  		  echo json_encode($return); die();}
  		
  		while ($row = $result->fetch_assoc()) {
  			if (isset($rows[$row['calendar_id']])) {
  				$rows[$row['calendar_id']]['notification'][]=$row;
  			}
  		}
  		
  
  		$sql="SELECT gks_calendar_participant.calendar_id, gks_calendar_participant.participant_id, 
  		".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile, 
  		gks_calendar_participant.is_organizer, gks_calendar_participant.is_optional,
  		gks_calendar_participant.response_type, gks_calendar_participant.response_date
  		FROM gks_calendar_participant 
  		LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_calendar_participant.participant_id = ".GKS_WP_TABLE_PREFIX."users.ID
  		WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is NOT Null and calendar_id in (".implode(',',$ids).")
  		ORDER BY gks_calendar_participant.is_organizer DESC, gks_calendar_participant.is_optional, ".GKS_WP_TABLE_PREFIX."users.gks_nickname";
  		$result = $db_link->query($sql);        
  		if (!$result) {
  		  debug_mail(false,'error sql',$sql);
  		  $return = array('success' => false, 'message' => base64_encode('sql error'));
  		  echo json_encode($return); die();}
  		
  		while ($row = $result->fetch_assoc()) {
  			if (isset($rows[$row['calendar_id']])) {
  				$rows[$row['calendar_id']]['participant'][]=$row;
  			}
  		}
  		//print_r($sql);
  		//print_r($rows);
  		//die();
  		
      $sql="SELECT calendar_id, activity_model, activity_model_id
      FROM gks_crm_activity
      WHERE calendar_id In (".implode(',',$ids).")";
  		$result = $db_link->query($sql);        
  		if (!$result) {
  		  debug_mail(false,'error sql',$sql);
  		  $return = array('success' => false, 'message' => base64_encode('sql error'));
  		  echo json_encode($return); die();}
  		
      $objects=array();
  		while ($row = $result->fetch_assoc()) {
  			if (isset($rows[$row['calendar_id']])) {
  				$rows[$row['calendar_id']]['objects'][]=$row;
  			}
  			
        if (empty($row['activity_model'])== false and $row['activity_model_id']>0) {
          if (isset($objects[$row['activity_model']])==false) $objects[$row['activity_model']]=array();
          
          if (isset($objects[$row['activity_model']][$row['activity_model_id']])==false) {
            $objects[$row['activity_model']][$row['activity_model_id']]=array();
          }
        }
      }		
      //print '<pre>';print_r($objects);die();
      gks_get_activity_objects($objects);
      //print '<pre>';print_r($objects);die();
      
      $sql="SELECT crm_activity_object_code, crm_activity_object_page 
      FROM gks_crm_activity_objects 
      WHERE crm_activity_object_code<>'' AND crm_activity_object_page<>''";
  		$result = $db_link->query($sql);        
  		if (!$result) {
  		  debug_mail(false,'error sql',$sql);
  		  $return = array('success' => false, 'message' => base64_encode('sql error'));
  		  echo json_encode($return); die();}
      $object_links=array();
  		while ($row = $result->fetch_assoc()) {
        $object_links[$row['crm_activity_object_code']]=$row['crm_activity_object_page'];
      }
          
      foreach ($rows as &$row) {
        foreach ($row['objects'] as &$object) {
          if (isset($objects[$object['activity_model']][$object['activity_model_id']]['obj_name'])) {
            $object['obj_name'] = $objects[$object['activity_model']][$object['activity_model_id']]['obj_name'];
            $object['contact_name'] = $objects[$object['activity_model']][$object['activity_model_id']]['contact_name'];
            $object['contact_id'] = $objects[$object['activity_model']][$object['activity_model_id']]['contact_id'];
            $object['esoda'] = $objects[$object['activity_model']][$object['activity_model_id']]['esoda'];
            //$object['link'] ='';
  
            $obj_link='';
            if (isset($object_links[$object['activity_model']])) {
              $obj_link=str_replace('%s',$object['activity_model_id'],$object_links[$object['activity_model']]);
            }
            if ($obj_link!='') {
              $object['obj_name'] = '<a href="'.$obj_link.'">'.$object['obj_name'].'</a>';
            } 
          }
        }
        unset($object);
      }
      unset($row);
      
      //echo '<pre>';print_r($rows);print_r($objects);die();
  		
  	}
  	
  	foreach ($rows as $row) {
  	  $event = gks_calendar_event_array($row,$colors_per_user);
  	  $myout[] = $event;
  	} 
  	
  }
	
	//if (GKS_DEBUG) file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/ggg1.txt',print_r($rows,true));
	
	
	
	/////////////////////////////          gks_crm_tasks          /////////////////////////////
	if ($sql_where_tasks1!='') {
  	$sql=gks_crm_tasks_sql_event($sql_where_tasks1,$sql_where_tasks2);
  	
  	$result = $db_link->query($sql);        
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die();}
  	
  	$rows=array();
  	$ids=array();
  	while ($row = $result->fetch_assoc()) {
  	  $ids[]=$row['id_crm_task'];
  	  
  	  $row['notification']=array();
  	  $row['participant']=array();
  	  $row['objects']=array();
  	  $rows[$row['id_crm_task']]=$row;
  	}
  
    if (count($ids)>0) {
  		$sql="SELECT crm_task_id, crm_task_employee_id
      FROM gks_crm_tasks_employee
      WHERE crm_task_id In (".implode(',',$ids).")";
  		$result = $db_link->query($sql);        
  		if (!$result) {
  		  debug_mail(false,'error sql',$sql);
  		  $return = array('success' => false, 'message' => base64_encode('sql error'));
  		  echo json_encode($return); die();}
  		
  		while ($row = $result->fetch_assoc()) {
  			if (isset($rows[$row['crm_task_id']])) {
  				$rows[$row['crm_task_id']]['multi_users'][]=intval($row['crm_task_employee_id']);
  			}
  		}

    
    }
  
    //print '<pre>';print_r($rows);die();
    
  	foreach ($rows as $row) {
  	  $event = gks_calendar_event_task_array($row,$colors_per_user);
  	  $myout[] = $event;
  	} 
  	//print '<pre>';print_r($myout);die();
  	
  }

	/////////////////////////////          gks_crm_activity          /////////////////////////////
		
	if ($sql_where_activity!='') {
  	$sql=gks_crm_activity_sql_event($sql_where_activity);
  	//echo '<pre>ssssssss '.$sql;die();
  	
  	$result = $db_link->query($sql);        
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die();}
  	
  	$rows=array();
  	$ids=array();
  	$objects=array();
  	while ($row = $result->fetch_assoc()) {
  	  $ids[]=$row['id_crm_activity'];
  	  
  	  $row['notification']=array();
  	  $row['participant']=array();
  	  $row['objects']=array();
  	  $rows[$row['id_crm_activity']]=$row;
      if (empty($row['activity_model'])== false and $row['activity_model_id']>0) {
        if (isset($objects[$row['activity_model']])==false) $objects[$row['activity_model']]=array();
        
        if (isset($objects[$row['activity_model']][$row['activity_model_id']])==false) {
          $objects[$row['activity_model']][$row['activity_model_id']]=array();
        }
      }	  	  
  	}
    gks_get_activity_objects($objects);
    //echo '<pre>';print_r($objects);print_r($rows);die();
    foreach ($rows as &$row) {
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
      
      $row['activity_object']=array(
        'obj_name'=>$obj_name,
        'obj_link'=>$obj_link,
        'contact_name'=>$contact_name,
        'contact_url'=>$contact_url,
        'esoda'=>$esoda,
      );
    }
    unset($row);
    //print '<pre>';print_r($rows);die();
    
  	foreach ($rows as $row) {
  	  $event = gks_calendar_event_activity_array($row,$colors_per_user);
  	  $myout[] = $event;
  	} 
  	//print '<pre>';print_r($myout);die();
  	
  }	
	
	
	
	
	
	
	//if (GKS_DEBUG) file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/ggg3.txt',print_r($rows,true));
	
	
	//if (GKS_DEBUG) file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/ggg2.txt',print_r($myout,true));
	return $myout;
}

function gks_calendar_event_notification_recalc_rundate($id,$is_new_rec) {
	global $db_link;
	
	$sql="select * from gks_calendar where id_calendar=".$id;
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	
	if ($result->num_rows<=0) return;
	$row = $result->fetch_assoc();
	$calendar_start=strtotime($row['calendar_start']);
	
	
	$sql="select * from gks_calendar_notification where calendar_id=".$id;
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	
	$rows=array();
	while ($row = $result->fetch_assoc()) {
		$rows[]=$row;
	}
	
	foreach ($rows as $row) {
		$notification_rundate=0;
		switch ($row['notification_unit']) {
			case 'minute':
				$notification_rundate= $calendar_start - $row['notification_number'] * 60;
				break;
			case 'hour':
				$notification_rundate= $calendar_start - $row['notification_number'] * 60*60;
				break;
			case 'day':
				$notification_rundate= $calendar_start - $row['notification_number'] * 24*60*60;
				break;
			case 'week':
				$notification_rundate= $calendar_start - $row['notification_number'] * 7*24*60*60;
				break;
		}
		
		if ($notification_rundate>0) {
			$sql="update gks_calendar_notification set notification_rundate='".date('Y-m-d H:i:s',$notification_rundate)."' where id_calendar_notification=".$row['id_calendar_notification'];
			$result = $db_link->query($sql);  
			if (!$result) {
			  debug_mail(false,'error sql',$sql);
			  $return = array('success' => false, 'message' => base64_encode('sql error'));
			  echo json_encode($return); die(); }  
		}
	}
	
	$sql="update gks_calendar_notification set notification_send_at=null where notification_rundate >= now() and calendar_id=".$id;
	$result = $db_link->query($sql);
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	
	
	$sql="delete from gks_notification where model='calendar' and model_id=".$id." and has_ok=0"; 
 	$result = $db_link->query($sql);
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  

	
	
	
  gks_calendar_event_update_dav($id,$is_new_rec);
	
}

function gks_calendar_event_update_dav($id,$is_new_rec) {
  global $db_link;
	global $GKS_SITE_HUMAN_NAME;
	global $GKS_SITE_EMAIL;
	global $GKS_CACHE_DB_VER;
	global $gks_cache_version;
	  
	$sql=gks_calendar_sql_event("id_calendar=".$id);
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	
	if ($result->num_rows<=0) return;
	$row = $result->fetch_assoc();
  $calendar_user_id=$row['calendar_user_id'];
  $vcalendar = new Sabre\VObject\Component\VCalendar();
  $vcalendar->PRODID='-//gks Software//gks ERP '.$GKS_CACHE_DB_VER.'.'.$gks_cache_version.'//EN';
  
  //$vcalendar->TZID = 'Europe/Athens';
  $vcalendar->METHOD='REQUEST';//REPLY REQUEST
  
  $vevent = $vcalendar->createComponent('VEVENT');
  $uid=trim_gks($row['uid']);
  //echo $uid;die();
  if ($uid == '') $uid=guid_for_calendar_ics();
  $vevent->UID=$uid;
  
  $vtimezone = $vcalendar->add('VTIMEZONE', [
      'TZID'           => 'Europe/Athens',
      'X-LIC-LOCATION' => 'Europe/Athens'
  ]);
  $vevent->TZID='Europe/Athens';
  
  $dateTime = new \DateTime(showDate(strtotime($row['calendar_start']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->DTSTART = $dateTime;
  
  
  $dateTime = new \DateTime(showDate(strtotime($row['calendar_end']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->DTEND =$dateTime;
  
  
  $dateTime = new \DateTime(showDate(strtotime($row['mydate_add']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->CREATED = $dateTime;
  
  $dateTime = new \DateTime(showDate(strtotime($row['mydate_edit']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->add('LAST-MODIFIED', $dateTime);    
  
  $calendar_title = trim_gks($row['calendar_title']);
  if ($calendar_title=='') $calendar_title =gks_lang('Νέο συμβάν');
  $vevent->SUMMARY = $calendar_title;
  
  //$calendar_message = trim_gks($row['calendar_message']);
  
//  $calendar_message = 
//  	GKS_SITE_URL.'my/admin-crm-calendar.php?id='.$id."\n".
//  	//gks_lang('Κατάσταση εργασίας').': '.$row['task_status_descr']."\n".
//  	//(isset($row['mobile']) ? gks_lang('Κινητό').': '.trim_gks($row['mobile']) : '')."\n".
//  	//(isset($row['phone']) ? gks_lang('Σταθερό').': '.trim_gks($row['phone']) : '')."\n".
//  	trim_gks($row['calendar_message']);

  $calendar_message = 
  	GKS_SITE_URL.'my/admin-crm-calendar.php?id='.$id."\n".
  	//gks_lang('Κατάσταση εργασίας').': '.$row['task_status_descr']."\n".
  	//gks_lang('Πελάτης').': '.trim_gks($row['first_name'].' '.$row['last_name'])."\n".
  	//(isset($row['mobile']) ? gks_lang('Κινητό').': '.trim_gks($row['mobile']) : '')."\n".
  	//(isset($row['phone']) ? gks_lang('Σταθερό').': '.trim_gks($row['phone']) : '')."\n".
  	gks_lang('Περιγραφή').': '.trim_gks($row['calendar_message']);
  	

	$temp=array();
	$aaaa=trim_gks($row['calendar_odos'].' '.$row['calendar_arithmos']);
	if ($aaaa != '') $temp[]= $aaaa;
	if (trim_gks($row['calendar_orofos']) != '') $temp[]= trim_gks($row['calendar_orofos']);
	if (trim_gks($row['calendar_perioxi']) != '') $temp[]= trim_gks($row['calendar_perioxi']);
	if (trim_gks($row['calendar_poli']) != '') $temp[]= trim_gks($row['calendar_poli']);
	if (trim_gks($row['calendar_tk']) != '') $temp[]= trim_gks($row['calendar_tk']);
	if (trim_gks($row['nomos_descr']) != '') $temp[]= trim_gks($row['nomos_descr']);
	if (trim_gks($row['country_name']) != '') $temp[]= trim_gks($row['country_name']);
	$topothesia=implode(', ',$temp);
	
	if ($row['calendar_map_latitude'] != 0 and $row['calendar_map_longitude'] != 0) {
		//$vevent->GEO = $row['calendar_map_latitude'].';'.$row['calendar_map_longitude'];
		$vevent->GEO = [$row['calendar_map_latitude'], $row['calendar_map_longitude']];
		
		//$event = $cal->add('VEVENT', ['GEO' => [51.96668, 7.61876],
		$geo_s='GEO: https://www.google.com/maps/search/?api=1&query='.$row['calendar_map_latitude'].','.$row['calendar_map_longitude']; //$row['calendar_map_latitude'].','.$row['calendar_map_longitude'];
		
		if (!(strpos($calendar_message, $geo_s) !== false)) {
		  if ($calendar_message != '') $calendar_message.="\n";
		  $calendar_message.=$geo_s;
		}
	}
  
  
  $vevent->DESCRIPTION =$calendar_message;
  $vevent->LOCATION =$topothesia;
  $vevent->SEQUENCE='0';
  $vevent->STATUS='CONFIRMED';  
  $vevent->TRANSP = ($row['calendar_is_exclusive']==1 ? 'OPAQUE' : 'TRANSPARENT');
  
  
  $sql_notif="SELECT notification_number,notification_unit,notification_rundate
  FROM gks_calendar_notification
  WHERE calendar_id=".$id." AND notification_type='notif'
  ORDER BY id_calendar_notification;";
	$result_notif = $db_link->query($sql_notif);  
	if (!$result_notif) {
	  debug_mail(false,'error sql',$sql_notif);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  
  while ($row_notif = $result_notif->fetch_assoc()) {
		$mytrigger=0;
		
		switch ($row_notif['notification_unit']) {
			case 'minute':
				$mytrigger= '-PT'.intval($row_notif['notification_number']).'M';
				break;
			case 'hour':
				$mytrigger= '-PT'.intval($row_notif['notification_number']).'H';
				break;
			case 'day':
				$mytrigger= '-P'.intval($row_notif['notification_number']).'D';
				break;
			case 'week':
				$mytrigger= '-P'.(intval($row_notif['notification_number'])*7).'D';
				break;
		}
		    
    $valarm = $vcalendar->createComponent('VALARM');
    //$valarm->DESCRIPTION = 'my alarm';
    $valarm->ACTION = 'DISPLAY';
    $valarm->TRIGGER = $mytrigger; //'-P'.$myminutes.'M'; //.date('Ymd\THis\Z',strtotime($row_notif['notification_rundate'])); //.'+00:00'
    $vevent->add($valarm);
  }
  
  
  
  

	$sql_participant="SELECT gks_calendar_participant.calendar_id, gks_calendar_participant.participant_id, 
	".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile, 
	gks_calendar_participant.is_organizer, gks_calendar_participant.is_optional,
	gks_calendar_participant.response_type, gks_calendar_participant.response_date
	FROM gks_calendar_participant 
	LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_calendar_participant.participant_id = ".GKS_WP_TABLE_PREFIX."users.ID
	WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is NOT Null and calendar_id=".$id."
	ORDER BY gks_calendar_participant.is_organizer DESC, gks_calendar_participant.is_optional, ".GKS_WP_TABLE_PREFIX."users.gks_nickname";
	$result_participant = $db_link->query($sql_participant);  
	if (!$result_participant) {
	  debug_mail(false,'error sql',$sql_participant);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  
  while ($row_participant = $result_participant->fetch_assoc()) {
    $email=trim_gks($row_participant['user_email']);
    
    $isorg='ATTENDEE';
    if ($row_participant['is_organizer']==1) $isorg='ORGANIZER';
    //$vevent->add($isorg,'mailto:'.$email);
    
    $PARTSTAT='';
    if (trim_gks($row_participant['response_type']) == '')          $PARTSTAT='NEEDS-ACTION';
    else if (trim_gks($row_participant['response_type']) == 'no')   $PARTSTAT='DECLINED';
    else if (trim_gks($row_participant['response_type']) == 'yes')  $PARTSTAT='ACCEPTED';
    else if (trim_gks($row_participant['response_type']) == 'isos') $PARTSTAT='TENTATIVE';
    
     
    //$vevent->add($isorg, 'mailto:'.$email, ['CN' => $row_participant['gks_nickname'], 'PARTSTAT' => $PARTSTAT]);
//  	if ($isorg=='ORGANIZER') {
//  		$vevent->add('ORGANIZER', 'mailto:'.$email, ['CN' => $email]);
//  	} else {
//	  	$vevent->add($isorg, 'mailto:'.$email, [
//	  		'CUTYPE'=>'INDIVIDUAL',
//	  		'ROLE'=>'REQ-PARTICIPANT',
//				'PARTSTAT' => 'NEEDS-ACTION',
//	  		'RSVP'=>'TRUE',
//				'CN' => $email, //$row['gks_nickname_pelatis'], 
//	  	  'X-NUM-GUESTS'=>'0',
//	  	]);
//	  }
    
    //ATTENDEE;RSVP=TRUE:mailto:foo@example.org
    //ATTENDEE;PARTSTAT=DECLINED;CN=One:mailto:one@example.org
    //ATTENDEE;PARTSTAT=TENTATIVE;CN=One:mailto:one@example.org
    //ATTENDEE;PARTSTAT=ACCEPTED;CN=One:mailto:one@example.org
    //ATTENDEE;CN=One:mailto:one@example.org
    //ATTENDEE;CN=White;PARTSTAT=NEEDS-ACTION:mailto:white@example.org
    //ATTENDEE;CUTYPE=INDIVIDUAL;LANGUAGE=en;PARTSTAT=NEEDS-ACTION;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:a2@example.org
    //ATTENDEE;CUTYPE=INDIVIDUAL;LANGUAGE=en;PARTSTAT=ACCEPTED;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:o@example.org
    //ATTENDEE;CUTYPE=INDIVIDUAL;LANGUAGE=en;PARTSTAT=NEEDS-ACTION;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:a1@example.org
    //ATTENDEE;CUTYPE=INDIVIDUAL;LANGUAGE=en;PARTSTAT=NEEDS-ACTION;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:a3@example.org

    //'ATTENDEE' => VObject\Property\ICalendar\CalAddress::class,

    
  }  
  
  if (isset($row['calendar_color']) and trim_gks($row['calendar_color'])!='') {
    $vevent->add('X-APPLE-CALENDAR-COLOR', trim_gks($row['calendar_color']));
    $vevent->add('COLOR', gks_hexToClosestCss3Name(trim_gks($row['calendar_color'])));
  }
  
  $vcalendar->add($vevent);
  
  $vcalendar_str = $vcalendar->serialize();
  //$vcalendar_str.="\r\n";
  
  //echo '<pre>'; print $vcalendar_str; die();
  
  $etag= md5($vcalendar_str);
  $size = strlen($vcalendar_str);
  $uri=$uid.'.ics';
  
  $sql_event="update gks_calendar set
  calendardata='".$db_link->escape_string($vcalendar_str)."',
  uri='".$db_link->escape_string($uri)."',
  etag='".$db_link->escape_string($etag)."',
  size=".$size.",
  componenttype='VEVENT',
  uid='".$db_link->escape_string($uid)."'
  where id_calendar=".$id;
	$result_event = $db_link->query($sql_event);  
	if (!$result_event) {
	  debug_mail(false,'error sql',$sql_event);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  
  
  $sql_event="select id_dav_calendar,caldav_synctoken from gks_calendar_dav_calendars where user_id=".$calendar_user_id." and other_myobj='cal'";
	$result_event = $db_link->query($sql_event);  
	if (!$result_event) {
	  debug_mail(false,'error sql',$sql_event);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
    
  $caldav_synctoken=0;
  $id_dav_calendar=0;
  if ($result_event->num_rows>=1) {
    $row_event = $result_event->fetch_assoc();
    $caldav_synctoken=$row_event['caldav_synctoken'];
    $id_dav_calendar=$row_event['id_dav_calendar'];
  }
  $sql_event="update gks_calendar_dav_calendars set caldav_synctoken=".($caldav_synctoken + 1)." where id_dav_calendar=".$id_dav_calendar;
	$result_event = $db_link->query($sql_event); 
	if (!$result_event) {
	  debug_mail(false,'error sql',$sql_event);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  
  
  $operation=($is_new_rec ? 1 : 2);
  $sql_change="INSERT INTO gks_calendar_dav_changes (
  uri, synctoken, calendarid, operation
  ) values (
  '".$db_link->escape_string($uri)."',".$caldav_synctoken.",".$id_dav_calendar.",".$operation."
  )";
	$result_change = $db_link->query($sql_change); 
	if (!$result_change) {
	  debug_mail(false,'error sql',$sql_change);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  
  
  //echo '<pre>'; print $caldav_synctoken; die();
  
  
  
  
  //file_put_contents(GKS_SITE_PATH.'tmp/componenttype.txt',$componenttype);
  
}
 
function gks_calendar_event_update_dav_task($id,$is_new_rec) {
  global $db_link;
	global $GKS_SITE_HUMAN_NAME;
	global $GKS_SITE_EMAIL;
	global $GKS_CACHE_DB_VER;
	global $gks_cache_version;
	  
	$sql=gks_crm_tasks_sql_event("id_crm_task=".$id,'');
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	
	if ($result->num_rows<=0) return;
	$row = $result->fetch_assoc();
  
  $vcalendar = new Sabre\VObject\Component\VCalendar();
  $vcalendar->PRODID='-//gks Software//gks ERP '.$GKS_CACHE_DB_VER.'.'.$gks_cache_version.'//EN';
  
  //$vcalendar->TZID = 'Europe/Athens';
  //$vcalendar->METHOD='REQUEST';//REPLY REQUEST
  
  $vevent = $vcalendar->createComponent('VEVENT');
  $uid=trim_gks($row['uid']);
  //echo $uid;die();
  if ($uid == '') $uid=guid_for_calendar_ics();
  $vevent->UID=$uid;
  
  $vtimezone = $vcalendar->add('VTIMEZONE', [
      'TZID'           => 'Europe/Athens',
      'X-LIC-LOCATION' => 'Europe/Athens'
  ]);

  $dateTime = new \DateTime(showDate(strtotime($row['task_planned_date_from']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->DTSTART = $dateTime;
  
  
  $dateTime = new \DateTime(showDate(strtotime($row['task_planned_date_to']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->DTEND =$dateTime;

  $dateTime = new \DateTime(showDate(strtotime($row['mydate_add']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->CREATED = $dateTime;    

  
  $vevent->TZID='Europe/Athens';
  
  $calendar_title = trim_gks($row['subject']);
  if ($calendar_title=='') $calendar_title =gks_lang('Νέα εργασία');
  //$calendar_title=trim_gks($calendar_title .' '.$row['first_name'].' '.$row['last_name']);
  
  $vevent->SUMMARY = $calendar_title;
  
  $calendar_message = 
  	GKS_SITE_URL.'my/admin-crm-task-item.php?id='.$id."\n".
  	gks_lang('Κατάσταση εργασίας').': '.$row['task_status_descr']."\n".
  	gks_lang('Πελάτης').': '.trim_gks($row['first_name'].' '.$row['last_name'])."\n".
  	(isset($row['mobile']) ? gks_lang('Κινητό').': '.trim_gks($row['mobile']) : '')."\n".
  	(isset($row['phone']) ? gks_lang('Σταθερό').': '.trim_gks($row['phone']) : '')."\n".
  	gks_lang('Περιγραφή').': '.trim_gks($row['message']);
  

	$temp=array();
	$aaaa=trim_gks($row['odos'].' '.$row['arithmos']);
	if ($aaaa != '') $temp[]= $aaaa;
	if (trim_gks($row['orofos']) != '') $temp[]= trim_gks($row['orofos']);
	if (trim_gks($row['perioxi']) != '') $temp[]= trim_gks($row['perioxi']);
	if (trim_gks($row['poli']) != '') $temp[]= trim_gks($row['poli']);
	if (trim_gks($row['tk']) != '') $temp[]= trim_gks($row['tk']);
	if (trim_gks($row['nomos_descr']) != '') $temp[]= trim_gks($row['nomos_descr']);
	if (trim_gks($row['country_name']) != '') $temp[]= trim_gks($row['country_name']);
	$topothesia=implode(', ',$temp);


	
	if ($row['map_latitude'] != 0 and $row['map_longitude'] != 0) {
		//$vevent->GEO = $row['calendar_map_latitude'].';'.$row['calendar_map_longitude'];
		$vevent->GEO = [$row['map_latitude'], $row['map_longitude']];
		
		//$event = $cal->add('VEVENT', ['GEO' => [51.96668, 7.61876],
		//$geo_s='GEO: '.$row['map_latitude'].','.$row['map_longitude'];
		$geo_s='GEO: https://www.google.com/maps/search/?api=1&query='.$row['map_latitude'].','.$row['map_longitude']; //$row['calendar_map_latitude'].','.$row['calendar_map_longitude'];

		if (!(strpos($calendar_message, $geo_s) !== false)) {
		  if ($calendar_message != '') $calendar_message.="\n";
		  $calendar_message.=$geo_s;
		}
	}
  
	  
  $vevent->DESCRIPTION =$calendar_message;
  $vevent->LOCATION =$topothesia;
  $vevent->SEQUENCE='0';
  $vevent->STATUS='CONFIRMED';  
  $vevent->TRANSP ='OPAQUE'; // ($row['calendar_is_exclusive']==1 ? 'OPAQUE' : 'TRANSPARENT');

	$mytrigger= '-PT'.'0'.'M';
  $valarm = $vcalendar->createComponent('VALARM');
  //$valarm->DESCRIPTION = 'my alarm';
  $valarm->ACTION = 'DISPLAY';
  $valarm->TRIGGER = $mytrigger; //'-P'.$myminutes.'M'; //.date('Ymd\THis\Z',strtotime($row_notif['notification_rundate'])); //.'+00:00'
  $vevent->add($valarm);
  
  if (isset($row['task_color']) and trim_gks($row['task_color'])!='') {
    $vevent->add('X-APPLE-CALENDAR-COLOR', trim_gks($row['task_color']));
    $vevent->add('COLOR', gks_hexToClosestCss3Name(trim_gks($row['task_color'])));
  }
  
  $vcalendar->add($vevent);
  
  $vcalendar_str = $vcalendar->serialize();
  //echo '<pre>'; print $vcalendar_str; die();
  
  $etag= md5($vcalendar_str);
  $size = strlen($vcalendar_str);
  $uri=$uid.'.ics';
  
  $sql_event="update gks_crm_tasks set
  calendardata='".$db_link->escape_string($vcalendar_str)."',
  uri='".$db_link->escape_string($uri)."',
  etag='".$db_link->escape_string($etag)."',
  size=".$size.",
  componenttype='VEVENT',
  uid='".$db_link->escape_string($uid)."'
  where id_crm_task=".$id;
	$result_event = $db_link->query($sql_event);  
	if (!$result_event) {
	  debug_mail(false,'error sql',$sql_event);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  

  $sql="select crm_task_employee_id from gks_crm_tasks_employee where crm_task_id=".$id;
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  $users=array();
  while ($row = $result->fetch_assoc()) {
    $users[]=array('user_id' => $row['crm_task_employee_id'], 'id_dav_calendar'=>0, 'caldav_synctoken'=>1);
  }  
  
  foreach ($users as &$value) {
    $sql="select id_dav_calendar,caldav_synctoken from gks_calendar_dav_calendars where user_id=".$value['user_id']." and other_myobj='task'";
  	$result = $db_link->query($sql);  
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $value['id_dav_calendar']=$row['id_dav_calendar'];
      $value['caldav_synctoken']=$row['caldav_synctoken'];
    } else {
      $sql="insert into gks_calendar_dav_calendars (
        user_id,other_myobj,caldav_synctoken
      ) values (
        ".$value['user_id'].",'task',1
      )";
    	$result = $db_link->query($sql);  
    	if (!$result) {
    	  debug_mail(false,'error sql',$sql);
    	  $return = array('success' => false, 'message' => base64_encode('sql error'));
    	  echo json_encode($return); die(); }  
      $value['id_dav_calendar'] = $db_link->insert_id; 
      $value['caldav_synctoken']=1;
    }
    
  }
  unset($value);
  
  foreach ($users as $value) {
    $sql="update gks_calendar_dav_calendars set
    caldav_synctoken=".($value['caldav_synctoken'] + 1)."
    where id_dav_calendar=".$value['id_dav_calendar'];
  	$result = $db_link->query($sql);  
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
    
  } 

  $operation=($is_new_rec ? 1 : 2);
  
  foreach ($users as $value) {
    $sql="INSERT INTO gks_calendar_dav_changes (
    uri, synctoken, calendarid, operation
    ) values (
    '".$db_link->escape_string($uri)."',".$value['caldav_synctoken'].",".$value['id_dav_calendar'].",".$operation."
    )";
  	$result = $db_link->query($sql); 
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
  }  
  //file_put_contents(GKS_SITE_PATH.'tmp/componenttype.txt',$componenttype);
}

function gks_calendar_event_update_dav_activity($id,$is_new_rec) {
  global $db_link;
	global $GKS_SITE_HUMAN_NAME;
	global $GKS_SITE_EMAIL;
	global $GKS_CACHE_DB_VER;
	global $gks_cache_version;
	  
	$sql=gks_crm_activity_sql_event("id_crm_activity=".$id,'');
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	//echo '<pre>';print_r($sql);die();
	if ($result->num_rows<=0) return;
	$row = $result->fetch_assoc();
  
  $objects=array();  
  if (empty($row['activity_model'])== false and $row['activity_model_id']>0) {
    if (isset($objects[$row['activity_model']])==false) $objects[$row['activity_model']]=array();
    
    if (isset($objects[$row['activity_model']][$row['activity_model_id']])==false) {
      $objects[$row['activity_model']][$row['activity_model_id']]=array();
    }
  }	  
  gks_get_activity_objects($objects);
  //echo '<pre>';print_r($objects);die();
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
  $message_notif='';
  if ($obj_name!='') {
    $message_notif.="\n".
    gks_lang('Αντικείμενο').': '.$obj_name;
    if ($obj_link!='') {
      $message_notif.= "\n".GKS_SITE_URL.'my/'.$obj_link;
    }
  }
  if ($contact_name!='') {
    $message_notif.="\n".
    gks_lang('Επαφή').': '.$contact_name;
    if ($contact_url!='') {
      $message_notif.= "\n".GKS_SITE_URL.'my/'.$contact_url;
    }
  }
  if ($esoda!='') {
    $message_notif.="\n".
    gks_lang('Αναμενόμενα έσοδα').': '.str_replace('&euro;','€',$esoda);
  }  
  $message_notif=substr($message_notif,1);  
  
  
  //echo '<pre>'.$message_notif;die();
  
  //echo '<pre>';print_r($row);die();
  $vcalendar = new Sabre\VObject\Component\VCalendar();
  $vcalendar->PRODID='-//gks Software//gks ERP '.$GKS_CACHE_DB_VER.'.'.$gks_cache_version.'//EN';
  
  //$vcalendar->TZID = 'Europe/Athens';
  //$vcalendar->METHOD='REQUEST';//REPLY REQUEST
  
  $vevent = $vcalendar->createComponent('VEVENT');
  $uid=trim_gks($row['uid']);
  if ($uid == '') $uid=guid_for_calendar_ics();
  //echo '<pre>ggg '.$uid;die();
  $vevent->UID=$uid;
  
  $vtimezone = $vcalendar->add('VTIMEZONE', [
      'TZID'           => 'Europe/Athens',
      'X-LIC-LOCATION' => 'Europe/Athens'
  ]);

  $dateTime = new \DateTime(showDate(strtotime($row['activity_duedate']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->DTSTART = $dateTime;
  
  $dateTime = new \DateTime(showDate(strtotime($row['activity_duedate'])+30*60,'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->DTEND =$dateTime;

  $dateTime = new \DateTime(showDate(strtotime($row['mydate_add']),'Y-m-d H:i:s', 1), new \DateTimeZone('Europe/Athens'));
  $vevent->CREATED = $dateTime;    

  
  $vevent->TZID='Europe/Athens';
  
  $calendar_title = trim_gks($row['activity_subject']);
  if ($calendar_title=='') $calendar_title =gks_lang('Νέα δραστηριότητα');
  //$calendar_title=trim_gks($calendar_title .' '.$row['first_name'].' '.$row['last_name']);
  
  $vevent->SUMMARY = $calendar_title;
  
  
  $calendar_message = 
  	GKS_SITE_URL.'my/admin-crm-activity.php?id='.$id."\n".
  	gks_lang('Κατάσταση δραστηριότητας').': '.getActivityStatusDescr($row['activity_status'])."\n".
//  	gks_lang('Πελάτης').': '.trim_gks($row['first_name'].' '.$row['last_name'])."\n".
//  	(isset($row['mobile']) ? gks_lang('Κινητό').': '.trim_gks($row['mobile']) : '')."\n".
//  	(isset($row['phone']) ? gks_lang('Σταθερό').': '.trim_gks($row['phone']) : '')."\n".
  	$message_notif."\n".
  	gks_lang('Περιγραφή').': '.trim_gks($row['activity_message']);
  //echo '<pre>ggg '.$calendar_message;die();

	  
  $vevent->DESCRIPTION =$calendar_message;
  //$vevent->LOCATION =$topothesia;
  $vevent->SEQUENCE='0';
  $vevent->STATUS='CONFIRMED';  
  $vevent->TRANSP ='OPAQUE'; // ($row['calendar_is_exclusive']==1 ? 'OPAQUE' : 'TRANSPARENT');
  
  if ($row['activity_notification']==1 and $row['activity_status']=='050new') {
  	$mytrigger= '-PT'.'5'.'M';
    $valarm = $vcalendar->createComponent('VALARM');
    //$valarm->DESCRIPTION = 'my alarm';
    $valarm->ACTION = 'DISPLAY';
    $valarm->TRIGGER = $mytrigger; //'-P'.$myminutes.'M'; //.date('Ymd\THis\Z',strtotime($row_notif['notification_rundate'])); //.'+00:00'
    $vevent->add($valarm);
  }      
  if (isset($row['activity_color']) and trim_gks($row['activity_color'])!='') {
    $vevent->add('X-APPLE-CALENDAR-COLOR', trim_gks($row['activity_color']));
    $vevent->add('COLOR', gks_hexToClosestCss3Name(trim_gks($row['activity_color'])));
  }
  

  $vcalendar->add($vevent);
  
  $vcalendar_str = $vcalendar->serialize();
  //echo '<pre>'; print $vcalendar_str; die();
  
  $etag= md5($vcalendar_str);
  $size = strlen($vcalendar_str);
  $uri=$uid.'.ics';
  
  $sql_event="update gks_crm_activity set
  calendardata='".$db_link->escape_string($vcalendar_str)."',
  uri='".$db_link->escape_string($uri)."',
  etag='".$db_link->escape_string($etag)."',
  size=".$size.",
  componenttype='VEVENT',
  uid='".$db_link->escape_string($uid)."'
  where id_crm_activity=".$id;
	$result_event = $db_link->query($sql_event);  
	if (!$result_event) {
	  debug_mail(false,'error sql',$sql_event);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  
  //echo '<pre>ggg '.$sql_event;die();


  $users[]=array('user_id' => $row['activity_user_id'], 'id_dav_calendar'=>0, 'caldav_synctoken'=>1);
  //echo '<pre>ggg ';print_r($users);die();
  
  
  foreach ($users as &$value) {
    $sql="select id_dav_calendar,caldav_synctoken from gks_calendar_dav_calendars where user_id=".$value['user_id']." and other_myobj='activity'";
  	$result = $db_link->query($sql);  
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $value['id_dav_calendar']=$row['id_dav_calendar'];
      $value['caldav_synctoken']=$row['caldav_synctoken'];
    } else {
      $sql="insert into gks_calendar_dav_calendars (
        user_id,other_myobj,caldav_synctoken
      ) values (
        ".$value['user_id'].",'activity',1
      )";
    	$result = $db_link->query($sql);  
    	if (!$result) {
    	  debug_mail(false,'error sql',$sql);
    	  $return = array('success' => false, 'message' => base64_encode('sql error'));
    	  echo json_encode($return); die(); }  
      $value['id_dav_calendar'] = $db_link->insert_id; 
      $value['caldav_synctoken']=1;
    }
    
  }
  unset($value);
  
  foreach ($users as $value) {
    $sql="update gks_calendar_dav_calendars set
    caldav_synctoken=".($value['caldav_synctoken'] + 1)."
    where id_dav_calendar=".$value['id_dav_calendar'];
  	$result = $db_link->query($sql);  
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
    
  } 

  $operation=($is_new_rec ? 1 : 2);
  
  foreach ($users as $value) {
    $sql="INSERT INTO gks_calendar_dav_changes (
    uri, synctoken, calendarid, operation
    ) values (
    '".$db_link->escape_string($uri)."',".$value['caldav_synctoken'].",".$value['id_dav_calendar'].",".$operation."
    )";
  	$result = $db_link->query($sql); 
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
  }
  //file_put_contents(GKS_SITE_PATH.'tmp/componenttype.txt',$componenttype);
}

function gks_calendar_event_update_dav_transfer_reservation($id,$is_new_rec) {
  global $db_link;
	global $GKS_SITE_HUMAN_NAME;
	global $GKS_SITE_EMAIL;
	global $GKS_CACHE_DB_VER;
	global $gks_cache_version;
	global $GKS_ERP_APP_DEF_TIMEZONE;
	  
	//$sql=gks_transfer_reservation_sql_event("id_transfer_reservation=".$id,'');
	
  $sql="SELECT gks_transfer_reservation.uid,
  gks_transfer_reservation.mydate_add,
  gks_transfer_reservation.ma_odos,
  gks_transfer_reservation.ma_arithmos,
  gks_transfer_reservation.ma_orofos,
  gks_transfer_reservation.ma_perioxi,
  gks_transfer_reservation.ma_poli,
  gks_transfer_reservation.ma_tk,

  
  gks_transfer_reservation.id_transfer_reservation,
  gks_transfer_reservation.transfer_reservation_guid,
  gks_transfer_reservation.transfer_booking_number,
  gks_transfer_reservation.transfer_reservation_date,
  gks_transfer_reservation.transfer_id,
  gks_transfer_reservation.transfer_area_id,
  gks_transfer_reservation.is_return_transfer_for_id,

  gks_transfer_reservation.transfer_reservation_status,
  gks_transfer_reservation.transfer_start,
  gks_transfer_reservation.transfer_end,
  gks_transfer_reservation.duration_secs,
  gks_transfer_reservation.num_adults,
  gks_transfer_reservation.num_childs,
  gks_transfer_reservation.num_babys,
  gks_transfer_reservation.user_id,
  gks_transfer_reservation.user_email,
  gks_transfer_reservation.user_first_name,
  gks_transfer_reservation.user_last_name,
  gks_transfer_reservation.user_mobile,
  gks_transfer_reservation.user_lang,

  gks_transfer_reservation.sxolio,
  gks_transfer_reservation.user_notes,
  gks_transfer_reservation.note_logistirio,

  gks_transfer_reservation.gks_price_total,


  gks_transfer_reservation.tropos_pliromis,


  gks_transfer_reservation.assigned_id,
  gks_transfer_reservation.crm_channel_id,
  gks_transfer_reservation.crm_channel_contact_id,
  gks_transfer_reservation.crm_channel_campain_id,
  gks_transfer_reservation.crm_channel_url,
  gks_transfer_reservation.crm_channel_text,
  gks_transfer_reservation.crm_channel_code,
  gks_transfer_reservation.poi_id_from,
  gks_transfer_reservation.poi_from_place_id,
  gks_transfer_reservation.poi_from_place_formatted_address,
  gks_transfer_reservation.poi_from_place_lat,
  gks_transfer_reservation.poi_from_place_lng,
  gks_transfer_reservation.poi_id_to,
  gks_transfer_reservation.poi_to_place_id,
  gks_transfer_reservation.poi_to_place_formatted_address,
  gks_transfer_reservation.poi_to_place_lat,
  gks_transfer_reservation.poi_to_place_lng,
  gks_transfer_reservation.poi_diadromes_id,
  gks_transfer_reservation.direction,
  gks_transfer_reservation.apostasi_se_metra,
  gks_transfer_reservation.diarkeia_se_lepta,
  gks_transfer_reservation.outward_from_pick_up_point,
  gks_transfer_reservation.outward_from_pick_up_time,
  gks_transfer_reservation.outward_from_pick_up_time_max,
  gks_transfer_reservation.outward_from_airline,
  gks_transfer_reservation.outward_from_flight_number,
  gks_transfer_reservation.outward_from_originating_airport,
  gks_transfer_reservation.outward_from_flight_arrival_time,
  gks_transfer_reservation.outward_to_drop_off_point,
  gks_transfer_reservation.outward_to_departure_airline,
  gks_transfer_reservation.outward_to_flight_number,
  gks_transfer_reservation.outward_to_flight_departure_time,
  gks_transfer_reservation.return_from_address_different,
  gks_transfer_reservation.return_from_pick_up_point,
  gks_transfer_reservation.return_from_pick_up_time,
  gks_transfer_reservation.return_from_pick_up_time_max,
  gks_transfer_reservation.return_from_airline,
  gks_transfer_reservation.return_from_flight_number,
  gks_transfer_reservation.return_from_originating_airport,
  gks_transfer_reservation.return_from_flight_arrival_time,
  gks_transfer_reservation.return_to_airline,
  gks_transfer_reservation.return_to_flight_number,
  gks_transfer_reservation.return_to_flight_departure_time,
  gks_transfer_reservation.return_to_address_different,
  gks_transfer_reservation.return_to_drop_off_point,

     
  gks_transfer.transfer_title, 
  gks_transfer_area.transfer_area_descr, 
  user_pelatis.gks_nickname AS gks_nickname_pelatis, 
  gks_lang.lang_name, 
  gks_lang.lang_ico,
  gks_country.country_name, 
  gks_country.country_initials,
  gks_nomoi.nomos_descr, 
  gks_crm_channel_sale.crm_channel_sale_descr, 
  user_channel.gks_nickname AS gks_nickname_channel, 
  gks_ads_campain.ads_campain_name, 
  gks_eshops.eshop_name, 
  gks_poi_from.poi_type_id as poi_type_id_from,
  gks_poi_from.poi_descr AS poi_descr_from, 
  gks_poi_from.poi_iata_code as poi_iata_code_from,
  gks_poi_from.poi_locode as poi_locode_from,
  gks_poi_type_from.poi_type_descr AS poi_type_descr_from, 
  gks_poi_type_from.poi_type_html_icon AS poi_type_html_icon_from, 
  gks_poi_to.poi_type_id as poi_type_id_to,
  gks_poi_to.poi_descr AS poi_descr_to, 
  gks_poi_to.poi_iata_code as poi_iata_code_to,
  gks_poi_to.poi_locode as poi_locode_to,
  gks_poi_type_to.poi_type_descr AS poi_type_descr_to, 
  gks_poi_type_to.poi_type_html_icon AS poi_type_html_icon_to, 
  gks_poi_diadromes.poi_diadromes_apostasi_se_metra, 
  gks_poi_diadromes.poi_diadromes_diarkeia_se_lepta
  
      
  
  FROM ((((((((((((((((((((gks_transfer_reservation 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS users_add ON gks_transfer_reservation.user_id_add = users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS users_edit ON gks_transfer_reservation.user_id_edit = users_edit.ID) 
  LEFT JOIN gks_transfer ON gks_transfer_reservation.transfer_id = gks_transfer.id_transfer) 
  LEFT JOIN gks_transfer_area ON gks_transfer_reservation.transfer_area_id = gks_transfer_area.id_transfer_area) 
  LEFT JOIN gks_acc_journal ON gks_transfer_reservation.transfer_reservation_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_seires ON gks_transfer_reservation.transfer_reservation_seira_id = gks_acc_seires.id_acc_seira) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS user_pelatis ON gks_transfer_reservation.user_id = user_pelatis.ID) 
  LEFT JOIN gks_country ON gks_transfer_reservation.ma_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_transfer_reservation.ma_nomos_id = gks_nomoi.id_nomos) 
  LEFT JOIN gks_lang ON gks_transfer_reservation.user_lang = gks_lang.id_lang) 
  LEFT JOIN gks_eshop_fiscal_position ON gks_transfer_reservation.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
  LEFT JOIN gks_eshop_pricelist ON gks_transfer_reservation.pricelist_id = gks_eshop_pricelist.id_pricelist) 
  LEFT JOIN gks_crm_channel_sale ON gks_transfer_reservation.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS user_channel ON gks_transfer_reservation.crm_channel_contact_id = user_channel.ID) 
  LEFT JOIN gks_ads_campain ON gks_transfer_reservation.crm_channel_campain_id = gks_ads_campain.id_ads_campain) 
  LEFT JOIN gks_eshops ON gks_transfer_reservation.woo_eshop_id = gks_eshops.id_eshop) 
  LEFT JOIN gks_poi AS gks_poi_from ON gks_transfer_reservation.poi_id_from = gks_poi_from.id_poi) 
  LEFT JOIN gks_poi_type AS gks_poi_type_from ON gks_poi_from.poi_type_id = gks_poi_type_from.id_poi_type) 
  LEFT JOIN gks_poi AS gks_poi_to ON gks_transfer_reservation.poi_id_to = gks_poi_to.id_poi) 
  LEFT JOIN gks_poi_type AS gks_poi_type_to ON gks_poi_to.poi_type_id = gks_poi_type_to.id_poi_type) 
  LEFT JOIN gks_poi_diadromes ON gks_transfer_reservation.poi_diadromes_id = gks_poi_diadromes.id_poi_diadromes
  where id_transfer_reservation=".$id;
  
  //if (count($perm_id_transfer_ids)>0) $sql.=" and gks_transfer_reservation.transfer_id in (".implode(',',$perm_id_transfer_ids).")";
  //if (count($perm_id_transfer_area_ids)>0) $sql.=" and gks_transfer_reservation.transfer_area_id in (".implode(',',$perm_id_transfer_area_ids).")";
  //if (count($perm_id_company_ids)>0) $sql.=" and gks_transfer.company_id in (".implode(',',$perm_id_company_ids).")";
  //if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_transfer.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  //if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_transfer_reservation.transfer_reservation_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  //if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_transfer_reservation.transfer_reservation_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  
  //$sql.=" ORDER BY 
  //gks_transfer_reservation.transfer_start desc,
  //gks_transfer_reservation.id_transfer_reservation";	
	
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	
	if ($result->num_rows<=0) return;
	$row = $result->fetch_assoc();
  
  $row['status_name']=getTransferReservationStatusDescr($row['transfer_reservation_status']);
  
  $row['poi_type_id_from']=intval($row['poi_type_id_from']);
  $row['poi_type_id_to']=intval($row['poi_type_id_to']);
  if (in_array($row['poi_type_id_from'],[2,3,4])) {
    $row['type_rsrv']=gks_lang('Άφιξη');
    $row['type_rsrvn']=1;
  } else if (in_array($row['poi_type_id_to'],[2,3,4])) {
    $row['type_rsrv']=gks_lang('Αναχώρηση');
    $row['type_rsrvn']=2;
  } else {
    $row['type_rsrv']=gks_lang('Άλλο');
    $row['type_rsrvn']=3;
  }  
  

  $dd_t=strtotime($row['transfer_start']);
  $row['rsrvdt']=this_curr_date_caldav($row['transfer_start']);
  //$row['rsrvd']=mb_substr(getWeekDayName(date('w',$dd_t)),0,2).' '.date('d/m/Y', $dd_t);
  $row['rsrvd']=date('d/m/y', $dd_t);
  $row['rsrvt']=date('H:i', $dd_t);
  
  $row['so_rsrvdt']=strtotime($row['transfer_start']); // + (strtotime($row['transfer_end'])-strtotime($row['transfer_start']));
  
  $row['poi_from_place_formatted_address']=trim_gks($row['poi_from_place_formatted_address']);
  $row['poi_to_place_formatted_address']=trim_gks($row['poi_to_place_formatted_address']);
  $row['poi_descr_from']=trim_gks($row['poi_descr_from']);
  $row['poi_descr_to']=trim_gks($row['poi_descr_to']);
    
  $row['poi_descr_short_from']='';
  if ($row['poi_type_id_from']==2 && trim_gks($row['poi_iata_code_from'])!='') $row['poi_descr_short_from']=trim_gks($row['poi_iata_code_from']); //airport
  else if ($row['poi_type_id_from']==3 && trim_gks($row['poi_locode_from'])!='') $row['poi_descr_short_from']=trim_gks($row['poi_locode_from']); //limani
  else if ($row['poi_type_id_from']==4 && trim_gks($row['poi_iata_code_from'])!='') $row['poi_descr_short_from']=trim_gks($row['poi_iata_code_from']); //traina

  $row['poi_descr_short_to']='';
  if ($row['poi_type_id_to']==2 && trim_gks($row['poi_iata_code_to'])!='') $row['poi_descr_short_to']=trim_gks($row['poi_iata_code_to']); //airport
  else if ($row['poi_type_id_to']==3 && trim_gks($row['poi_locode_to'])!='') $row['poi_descr_short_to']=trim_gks($row['poi_locode_to']); //limani
  else if ($row['poi_type_id_to']==4 && trim_gks($row['poi_iata_code_to'])!='') $row['poi_descr_short_to']=trim_gks($row['poi_iata_code_to']); //traina 

  $row['transfer_title']=trim_gks($row['transfer_title']);
  $row['transfer_area_descr']=trim_gks($row['transfer_area_descr']);
  $row['user_first_name']=trim_gks($row['user_first_name']);
  $row['user_last_name']=trim_gks($row['user_last_name']);
  $row['user_notes']=nl2br(trim_gks($row['user_notes']));
  $row['sxolio']=nl2br(trim_gks($row['sxolio']));
  $row['note_logistirio']=nl2br(trim_gks($row['note_logistirio']));

  $row['apostasi_se_metra']=intval($row['apostasi_se_metra']);
  $row['apostasi']='';
  if ($row['apostasi_se_metra']>0) $row['apostasi']=round($row['apostasi_se_metra']/1000,0).'km';

  $row['diarkeia_se_lepta']=intval($row['diarkeia_se_lepta']);
  $row['diarkeia']='';
  if ($row['diarkeia_se_lepta']>0) $row['diarkeia']=gks_myFormatDurationTime($row['diarkeia_se_lepta']*60);
  
  
  $row['gks_price_total']=floatval($row['gks_price_total']);
  $row['ajia']=myCurrencyFormat($row['gks_price_total'],false);
  
  $row['is_return_transfer_for_id']=intval($row['is_return_transfer_for_id']);
  
  $row['outward_from_pick_up_point']=trim_gks($row['outward_from_pick_up_point']);
  $row['outward_from_pick_up_time']=this_curr_date_caldav($row['outward_from_pick_up_time']);
  $row['outward_from_pick_up_time_max']=this_curr_date_caldav($row['outward_from_pick_up_time_max']);
  $row['outward_from_airline']=trim_gks($row['outward_from_airline']);
  $row['outward_from_flight_number']=trim_gks($row['outward_from_flight_number']);
  $row['outward_from_originating_airport']=trim_gks($row['outward_from_originating_airport']);
  
  $row['offat']=0; if (isset($row['outward_from_flight_arrival_time'])) $row['offat']=strtotime($row['outward_from_flight_arrival_time']);
  $row['outward_from_flight_arrival_time']=this_curr_date_caldav($row['outward_from_flight_arrival_time']);
  
  $row['outward_to_drop_off_point']=trim_gks($row['outward_to_drop_off_point']);
  $row['outward_to_departure_airline']=trim_gks($row['outward_to_departure_airline']);
  $row['outward_to_flight_number']=trim_gks($row['outward_to_flight_number']);
  
  $row['otfdt']=0; if (isset($row['outward_to_flight_departure_time'])) $row['otfdt']=strtotime($row['outward_to_flight_departure_time']);
  $row['outward_to_flight_departure_time']=this_curr_date_caldav($row['outward_to_flight_departure_time']);
  
  $row['return_from_address_different']=intval($row['return_from_address_different'])!=0;

  $row['return_from_pick_up_point']=trim_gks($row['return_from_pick_up_point']);
  $row['return_from_pick_up_time']=this_curr_date_caldav($row['return_from_pick_up_time']);
  $row['return_from_pick_up_time_max']=this_curr_date_caldav($row['return_from_pick_up_time_max']);
  $row['return_from_airline']=trim_gks($row['return_from_airline']);
  $row['return_from_flight_number']=trim_gks($row['return_from_flight_number']);
  $row['return_from_originating_airport']=trim_gks($row['return_from_originating_airport']);
  
  $row['rffat']=0; if (isset($row['return_from_flight_arrival_time'])) $row['rffat']=strtotime($row['return_from_flight_arrival_time']);
  $row['return_from_flight_arrival_time']=this_curr_date_caldav($row['return_from_flight_arrival_time']);
  
  $row['return_to_airline']=trim_gks($row['return_to_airline']);
  $row['return_to_flight_number']=trim_gks($row['return_to_flight_number']);
  
  $row['rtfdt']=0; if (isset($row['return_to_flight_departure_time'])) $row['rtfdt']=strtotime($row['return_to_flight_departure_time']);
  $row['return_to_flight_departure_time']=this_curr_date_caldav($row['return_to_flight_departure_time']);

  $row['return_to_address_different']=intval($row['return_to_address_different'])!=0;
  $row['return_to_drop_off_point']=trim_gks($row['return_to_drop_off_point']);

  
  $row['lang_name']=trim_gks($row['lang_name']);
  $row['lang_ico']=trim_gks($row['lang_ico']);
  $row['lang_icon']='';
  if ($row['lang_name']!='' and $row['lang_ico']!='') $row['lang_icon']='/my/img/flags/flags_iso/32/'.strtolower($row['lang_ico']).'.png';
  $row_lang_name=$row['lang_name'];
  
  $row['country_name']=trim_gks($row['country_name']);
  $row['country_initials']=trim_gks($row['country_initials']);
  $row['country_icon']='';
  if ($row['country_name']!='' and $row['country_initials']!='') $row['country_icon']='/my/img/flags/flags_iso/32/'.strtolower($row['country_initials']).'.png';
  $row_country_name=$row['country_name'];
  
  
  $row['oximata']=[];  
  
  
  $sql_oximata="SELECT 
  gks_transfer_reservation_oximata.id_transfer_reservation_oximata,
  gks_transfer_reservation_oximata.transfer_reservation_id,
  gks_transfer_reservation_oximata.transfer_oxima_type_id,
  gks_transfer_reservation_oximata.transfer_oxima_asset_id,
  gks_transfer_reservation_oximata.is_return_oxima_for_id,
  gks_assets.asset_code,
  gks_assets.asset_title,
  gks_assets.asset_photo,
  gks_transfer_reservation_oximata.transfer_oxima_driver_id,
  users_driver.gks_nickname AS gks_nickname_driver,
  users_driver.gks_wsl_current_user_image as gks_wsl_current_user_image_driver,
  gks_transfer_reservation_oximata.dromologio_id,
  gks_transfer_dromologio.dromologio_descr,
  gks_transfer_reservation_oximata.externalpartner_id,
  externalpartner.gks_nickname AS gks_nickname_externalpartner,
  
  gks_transfer_reservation_oximata.oximata_aa,
  gks_transfer_reservation_oximata.rnum_adults,
  gks_transfer_reservation_oximata.rnum_childs,
  gks_transfer_reservation_oximata.rnum_babys,

  gks_transfer_reservation_oximata.ruser_id,
  gks_transfer_reservation_oximata.ruser_lang,
  gks_transfer_reservation_oximata.ruser_first_name,
  gks_transfer_reservation_oximata.ruser_last_name,
  gks_transfer_reservation_oximata.ruser_email,
  gks_transfer_reservation_oximata.ruser_mobile,

  gks_transfer_reservation_oximata.rsxolio,

  gks_transfer_reservation_oximata.product_quantity,

  gks_transfer_reservation_oximata.product_price_final_all_total,

  gks_transfer_reservation_oximata.product_comments,

  gks_transfer_reservation_oximata.group_type,
  gks_transfer_reservation_oximata.rsrv_oxima_num_booster,
  gks_transfer_reservation_oximata.rsrv_oxima_num_kareklakia,
  gks_transfer_reservation_oximata.rsrv_oxima_num_amajidia,
  gks_transfer_reservation_oximata.rsrv_oxima_num_golfbag,
  gks_transfer_reservation_oximata.rsrv_oxima_num_skis,
  gks_transfer_reservation_oximata.rsrv_oxima_num_5minstop,
  gks_transfer_reservation_oximata.rsrv_oxima_5minstop_descr,  
   
  gks_transfer_oxima_type.transfer_oxima_type_code, 
  gks_transfer_oxima_type.transfer_oxima_type_descr, 
  gks_transfer_oxima_type.transfer_oxima_type_photo, 
  
  users_pelatis.gks_nickname AS gks_nickname_pelatis, 
  gks_lang.lang_name, 
  gks_lang.lang_ico,
  gks_country.country_name, 
  gks_country.country_initials,
  gks_nomoi.nomos_descr
  
  FROM ((((((((((((gks_transfer_reservation_oximata 
  LEFT JOIN gks_transfer_oxima_type ON gks_transfer_reservation_oximata.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type) 
  LEFT JOIN gks_assets ON gks_transfer_reservation_oximata.transfer_oxima_asset_id = gks_assets.id_asset) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS users_driver ON gks_transfer_reservation_oximata.transfer_oxima_driver_id = users_driver.ID)
  LEFT JOIN gks_transfer_dromologio ON gks_transfer_reservation_oximata.dromologio_id = gks_transfer_dromologio.id_transfer_dromologio) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS externalpartner ON gks_transfer_reservation_oximata.externalpartner_id = externalpartner.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS users_add ON gks_transfer_reservation_oximata.user_id_add = users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS users_edit ON gks_transfer_reservation_oximata.user_id_edit = users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS users_pelatis ON gks_transfer_reservation_oximata.ruser_id = users_pelatis.ID) 
  LEFT JOIN gks_lang ON gks_transfer_reservation_oximata.ruser_lang = gks_lang.id_lang) 
  LEFT JOIN gks_country ON gks_transfer_reservation_oximata.ruser_ma_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_transfer_reservation_oximata.ruser_ma_nomos_id = gks_nomoi.id_nomos) 
  LEFT JOIN gks_eshop_fiscal_position ON gks_transfer_reservation_oximata.ruser_fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
  LEFT JOIN gks_eshop_pricelist ON gks_transfer_reservation_oximata.ruser_pricelist_id = gks_eshop_pricelist.id_pricelist
  where transfer_reservation_id=".$id."
  ORDER BY gks_transfer_reservation_oximata.transfer_reservation_id, 
  gks_transfer_reservation_oximata.oximata_aa;";  

	$result_oximata = $db_link->query($sql_oximata);  
	if (!$result_oximata) {
	  debug_mail(false,'error sql',$sql_oximata);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	
  $oximata=[];
  while ($rowox= $result_oximata->fetch_assoc()) {  
    $rowox['oximata_aa']=intval($rowox['oximata_aa']);
    $rowox['transfer_oxima_type_code']=trim_gks($rowox['transfer_oxima_type_code']);
    $rowox['transfer_oxima_type_descr']=trim_gks($rowox['transfer_oxima_type_descr']);

    $rowox['asset_code']=trim_gks($rowox['asset_code']);
    $rowox['asset_title']=trim_gks($rowox['asset_title']);

    $rowox['gks_nickname_driver']=trim_gks($rowox['gks_nickname_driver']);
    $rowox['dromologio_descr']=trim_gks($rowox['dromologio_descr']);
    $rowox['gks_nickname_externalpartner']=trim_gks($rowox['gks_nickname_externalpartner']);
    
    $rowox['rnum_adults']=intval($rowox['rnum_adults']);
    $rowox['rnum_childs']=intval($rowox['rnum_childs']);
    $rowox['rnum_babys']=intval($rowox['rnum_babys']);

    $rowox['rsrv_oxima_num_booster']=intval($rowox['rsrv_oxima_num_booster']);
    $rowox['rsrv_oxima_num_kareklakia']=intval($rowox['rsrv_oxima_num_kareklakia']);
    $rowox['rsrv_oxima_num_amajidia']=intval($rowox['rsrv_oxima_num_amajidia']);
    $rowox['rsrv_oxima_num_golfbag']=intval($rowox['rsrv_oxima_num_golfbag']);
    $rowox['rsrv_oxima_num_skis']=intval($rowox['rsrv_oxima_num_skis']);
    $rowox['rsrv_oxima_num_5minstop']=intval($rowox['rsrv_oxima_num_5minstop']);
    $rowox['rsrv_oxima_5minstop_descr']=trim_gks($rowox['rsrv_oxima_5minstop_descr']);
  
  

    $rowox['lang_name']=trim_gks($rowox['lang_name']);
    $rowox['country_name']=trim_gks($rowox['country_name']);


    $rowox['ruser_id']=intval($rowox['ruser_id']);
    $rowox['ruser_first_name']=trim_gks($rowox['ruser_first_name']);
    $rowox['ruser_last_name']=trim_gks($rowox['ruser_last_name']);
    $rowox['gks_nickname_pelatis']=trim_gks($rowox['gks_nickname_pelatis']);
        
    $rowox['rsxolio']=trim_gks($rowox['rsxolio']);
    
    $rowox['product_price_final_all_total']=floatval($rowox['product_price_final_all_total']);
    $rowox['ajia']=myCurrencyFormat($rowox['product_price_final_all_total'],false);

              
    $oximata[]=$rowox;  
  }
  
  $htmlox=gks_lang('Οχήματα').':'."\n";
  $aa=0;
  foreach ($oximata as $rowox) {
    $aa++;
    $htmlox.=$aa.') '.gks_lang('Τύπος').': '.$rowox['transfer_oxima_type_code']."\n";
    $htmlox.=$rowox['transfer_oxima_type_descr']."\n";
    $htmlox.=gks_lang('Όχημα').': '.$rowox['asset_code'].' '.$rowox['asset_title']."\n";
    if ($rowox['gks_nickname_driver']!='') $htmlox.=gks_lang('Οδηγός').': '.$rowox['gks_nickname_driver']."\n";
    if ($rowox['dromologio_descr']!='') $htmlox.=gks_lang('Δρομολόγιο').': '.$rowox['dromologio_descr']."\n";
    if ($rowox['gks_nickname_externalpartner']!='') $htmlox.=gks_lang('Συνεργάτης').': '.$rowox['gks_nickname_externalpartner']."\n";
    
    $htmlox.=gks_lang('Ενήλικες').': '.$rowox['rnum_adults']."\n";
    if ($rowox['rnum_childs']>0) $htmlox.=gks_lang('Παιδιά').': '.$rowox['rnum_childs']."\n";
    if ($rowox['rnum_babys']>0) $htmlox.=gks_lang('Βρέφη').': '.$rowox['rnum_babys']."\n";

    if ($rowox['rsrv_oxima_num_booster']>0) $htmlox.=gks_lang('Παιδικά Booster').': '.$rowox['rsrv_oxima_num_booster']."\n";
    if ($rowox['rsrv_oxima_num_kareklakia']>0) $htmlox.=gks_lang('Παιδικά Καρεκλάκια').': '.$rowox['rsrv_oxima_num_kareklakia']."\n";
    if ($rowox['rsrv_oxima_num_amajidia']>0) $htmlox.=gks_lang('Αναπηρικά Αμαξίδια').': '.$rowox['rsrv_oxima_num_amajidia']."\n";
    if ($rowox['rsrv_oxima_num_golfbag']>0) $htmlox.=gks_lang('Golf Bag').': '.$rowox['rsrv_oxima_num_golfbag']."\n";
    if ($rowox['rsrv_oxima_num_skis']>0) $htmlox.=gks_lang('Skis / Snowboard').': '.$rowox['rsrv_oxima_num_skis']."\n";
    if ($rowox['rsrv_oxima_num_5minstop']>0) $htmlox.=gks_lang('5 min extra stop').': '.$rowox['rsrv_oxima_num_5minstop']."\n";
    if ($rowox['rsrv_oxima_5minstop_descr']!='') $htmlox.=gks_lang('Στάσεις').': '.$rowox['rsrv_oxima_5minstop_descr']."\n";

    $rsrvope2='';
    if ($rowox['ruser_id']==0) {
      $rsrvope2=$rowox['ruser_first_name'].' '.$rowox['ruser_last_name'];
    } else if ($rowox['ruser_id']>0) {
      $rsrvope2=$rowox['gks_nickname_pelatis'];
    }
    if ($rsrvope2!='') $htmlox.=gks_lang('Πελάτης').': ' . $rsrvope2."\n";
    if ($rowox['lang_name']!='') $htmlox.=gks_lang('Γλώσσα').': ' . $rowox['lang_name']."\n";
    if ($rowox['country_name']!='') $htmlox.=gks_lang('Χώρα').': ' . $rowox['country_name']."\n";
                     
    
    $htmlox.=gks_lang('Αξία').': '.$rowox['ajia']."\n";
    if ($rowox['rsxolio']!='') $htmlox.=gks_lang('Σχόλιο').': '.$rowox['rsxolio']."\n";
      
    $htmlox.="\n";
  } 
  
  
  $html='';
  $html.=gks_lang('ID Κράτησης').': ' . $row['id_transfer_reservation'] ."\n". 
  GKS_SITE_URL.'my/admin-transfer-reservation-item.php?id='.$id."\n";
  $html.=gks_lang('Κατάσταση').': '. $row['status_name']."\n";
  $html.=gks_lang('Κωδικός αναφοράς').': '.$row['transfer_booking_number']."\n";
  $html.=gks_lang('Τύπος').': '.$row['type_rsrv']."\n";
  $html.=gks_lang('Ενήλικες').': '.$row['num_adults']."\n";
  if ($row['num_childs']>0) $html.=gks_lang('Παιδιά').': '.$row['num_childs']."\n";
  if ($row['num_babys']>0) $html.=gks_lang('Βρέφη').': '.$row['num_babys']."\n";
  $html.=gks_lang('Επιβάτες').': '.($row['num_adults']+$row['num_childs']+$row['num_babys'])."\n";
  $html.=gks_lang('Ημερομηνία Έναρξης transfer').': '.$row['rsrvd']."\n";
  $html.=gks_lang('Ώρα Έναρξης transfer').': '.$row['rsrvt']."\n";
  

  
  $airline='';
  $rsrvfn='';$rsrvft='';$so_flight_time=0;
  if ($row['is_return_transfer_for_id']==0) {
    if ($row['outward_from_airline']!='') $airline=$row['outward_from_airline'];
    else if ($row['outward_to_departure_airline']!='') $airline=$row['outward_to_departure_airline'];
    
    if ($row['outward_from_flight_number']!='') $rsrvfn=$row['outward_from_flight_number'];
    else if ($row['outward_to_flight_number']!='') $rsrvfn=$row['outward_to_flight_number'];
    
    if ($row['outward_from_flight_arrival_time']!='')      {$rsrvft=$row['outward_from_flight_arrival_time']; $so_flight_time=$row['offat'];}
    else if ($row['outward_to_flight_departure_time']!='') {$rsrvft=$row['outward_to_flight_departure_time']; $so_flight_time=$row['otfdt'];}
    
  } else if ($row['is_return_transfer_for_id']>0) {
    if ($row['return_from_airline']!='') $airline=$row['return_from_airline'];
    else if ($row['return_to_airline']!='') $airline=$row['return_to_airline'];

    if ($row['return_from_flight_number']!='') $rsrvfn=$row['return_from_flight_number'];
    else if ($row['return_to_flight_number']!='') $rsrvfn=$row['return_to_flight_number'];

    if ($row['return_from_flight_arrival_time']!='')      {$rsrvft=$row['return_from_flight_arrival_time'];$so_flight_time=$row['rffat'];}
    else if ($row['return_to_flight_departure_time']!='') {$rsrvft=$row['return_to_flight_departure_time'];$so_flight_time=$row['rtfdt'];}
  }
  $html.=gks_lang('Airline').': '.$airline."\n";
  $html.=gks_lang('Αριθμός Πτήσης').': ' . $rsrvfn."\n";
  $html.=gks_lang('Άφιξη/Αναχώρηση πτήσης').': ' . $rsrvft."\n";
  

  

  $from_to_airport=false;
  if (in_array($row['poi_type_id_from'], [2,3,4]) || in_array($row['poi_type_id_to'], [2,3,4])) {
    $from_to_airport=true;
  }
  
  $rsrvpf='';$rsrvpt='';$rsrvph='';
  if ($row['poi_descr_short_from']!='') {
    $rsrvpf=$row['poi_descr_short_from'];
  } else {
    if ($row['poi_descr_from']!='') {
      $rsrvpf=$row['poi_descr_from'];
    }
    if ($row['poi_from_place_formatted_address']!='') {
      if ($from_to_airport) {
        $rsrvph=$row['poi_from_place_formatted_address'];
      } else {
        $rsrvpf.=($rsrvpf=='' ? '' : ' ') . $row['poi_from_place_formatted_address'];
      }
    }
  }
  $html.=gks_lang('Από').': '.$rsrvpf."\n";
  

  if ($row['poi_descr_short_to']!='') {
    $rsrvpt=$row['poi_descr_short_to'];
  } else {
    if ($row['poi_descr_to']!='') {
      $rsrvpt=$row['poi_descr_to'];
    }
    if ($row['poi_to_place_formatted_address']!='') {
      if ($from_to_airport) {
        $rsrvph=$row['poi_to_place_formatted_address'];
      } else {
        $rsrvpt.=($rsrvpt=='' ? '' : ' ') . $row['poi_to_place_formatted_address'];
      }
    }
  }
  $html.=gks_lang('Προς').': '.$rsrvpt."\n";
  
  $topothesia=$rsrvpf.' -> '.$rsrvpt;


  $html.=gks_lang('Προορισμός').': '.$rsrvph."\n";
  $html.=gks_lang('Απόσταση (km)').': '.$row['apostasi']."\n";
  $html.=gks_lang('Διάρκεια (ώρες:λεπτά)').': '.$row['diarkeia']."\n";
  $html.=gks_lang('Αξία τελική με ΦΠΑ και extra').': '.$row['ajia']."\n";
  $html.=gks_lang('Κανάλι').': '.$row['transfer_title']."\n";
  $html.=gks_lang('Περιοχή').': '.$row['transfer_area_descr']."\n";
  $html.=gks_lang('Πελάτης').': '.$row['user_first_name'] . ' ' . $row['user_last_name']."\n";
  
  if ($row['lang_name']!='') $html.=gks_lang('Γλώσσα').': '.$row['lang_name']."\n";
  if ($row['country_name']!='') $html.=gks_lang('Χώρα').': '.$row['country_name']."\n";
  
  
  if ($row['user_notes']!='') $html.=gks_lang('Σχόλιο από πελάτη').': '.$row['user_notes']."\n";
  if ($row['sxolio']!='') $html.=gks_lang('Σχόλιο κράτησης').': '.$row['sxolio']."\n";
  if ($row['note_logistirio']!='') $html.=gks_lang('Εσωτερική σημείωση για λογιστήριο').': '.$row['note_logistirio']."\n";


  $html.="\n".$htmlox;
  
  
  $vcalendar = new Sabre\VObject\Component\VCalendar();
  $vcalendar->PRODID='-//gks Software//gks ERP '.$GKS_CACHE_DB_VER.'.'.$gks_cache_version.'//EN';
  
  //$vcalendar->TZID = 'Europe/Athens';
  //$vcalendar->METHOD='REQUEST';//REPLY REQUEST
  
  $vevent = $vcalendar->createComponent('VEVENT');
  $uid=trim_gks($row['uid']);
  //echo $uid;die();
  if ($uid == '') $uid=guid_for_calendar_ics();
  $vevent->UID=$uid;
  
  $vtimezone = $vcalendar->add('VTIMEZONE', [
      'TZID'           => $GKS_ERP_APP_DEF_TIMEZONE,
      'X-LIC-LOCATION' => $GKS_ERP_APP_DEF_TIMEZONE
  ]);

  $dateTime = new \DateTime(date('Y-m-d H:i:s', strtotime($row['transfer_start'])), new \DateTimeZone($GKS_ERP_APP_DEF_TIMEZONE));
  $vevent->DTSTART = $dateTime;
  
  $dateTime = new \DateTime(date('Y-m-d H:i:s', strtotime($row['transfer_end'])), new \DateTimeZone($GKS_ERP_APP_DEF_TIMEZONE));
  $vevent->DTEND =$dateTime;

  $dateTime = new \DateTime(showDate(strtotime($row['mydate_add']),'Y-m-d H:i:s', 1), new \DateTimeZone($GKS_ERP_APP_DEF_TIMEZONE));
  $vevent->CREATED = $dateTime;    


  
  $vevent->TZID=$GKS_ERP_APP_DEF_TIMEZONE;
  
  $calendar_title = 'Transfer '.$row['type_rsrv'].' '.$topothesia;
  //if ($calendar_title=='') $calendar_title =gks_lang('Νέο Transfer');
  //$calendar_title=trim_gks($calendar_title .' '.$row['first_name'].' '.$row['last_name']);
  
  $vevent->SUMMARY = $calendar_title;
  
//  $calendar_message = 
//  	GKS_SITE_URL.'my/admin-transfer-reservation-item.php?id='.$id."\n".
//  	gks_lang('Κατάσταση transfer').': '.getTransferReservationStatusDescr($row['transfer_reservation_status'])."\n".
//  	gks_lang('Πελάτης').': '.trim_gks($row['user_first_name'].' '.$row['user_last_name'])."\n".
//  	(isset($row['user_mobile']) ? gks_lang('Κινητό').': '.trim_gks($row['user_mobile']) : '')."\n".
//  	gks_lang('Σχόλιο Πελάτη').': '.trim_gks($row['user_notes']);
  

//	$temp=array();
//	$aaaa=trim_gks($row['ma_odos'].' '.$row['ma_arithmos']);
//	if ($aaaa != '') $temp[]= $aaaa;
//	if (trim_gks($row['ma_perioxi']) != '') $temp[]= trim_gks($row['ma_perioxi']);
//	if (trim_gks($row['ma_poli']) != '') $temp[]= trim_gks($row['ma_poli']);
//	if (trim_gks($row['ma_tk']) != '') $temp[]= trim_gks($row['ma_tk']);
//	if (trim_gks($row['nomos_descr']) != '') $temp[]= trim_gks($row['nomos_descr']);
//	if (trim_gks($row['country_name']) != '') $temp[]= trim_gks($row['country_name']);
//	$topothesia=implode(', ',$temp);


	$row['poi_from_place_lat']=floatval($row['poi_from_place_lat']);
	$row['poi_from_place_lng']=floatval($row['poi_from_place_lng']);
	
	if ($row['poi_from_place_lat'] != 0 and $row['poi_from_place_lng'] != 0) {
		$vevent->GEO = [$row['poi_from_place_lat'], $row['poi_from_place_lng']];

		$geo_s='GEO: https://www.google.com/maps/search/?api=1&query='.$row['poi_from_place_lat'].','.$row['poi_from_place_lng']; //$row['calendar_map_latitude'].','.$row['calendar_map_longitude'];

		if (!(strpos($html, $geo_s) !== false)) {
		  if ($html != '') $html.="\n";
		  $html.=$geo_s;
		}
	}
	
  
//	$vevent->add('ORGANIZER', 'mailto:'.$GKS_SITE_EMAIL, ['CN' => $GKS_SITE_EMAIL]); //$GKS_SITE_HUMAN_NAME
//	$vevent->add('ATTENDEE', 'mailto:'.$GKS_SITE_EMAIL, [
//		'CUTYPE'=>'INDIVIDUAL',
//		'ROLE'=>'REQ-PARTICIPANT',
//		'PARTSTAT' => 'NEEDS-ACTION',
//		'RSVP'=>'TRUE',
//		'CN' => $GKS_SITE_EMAIL, //$row['gks_nickname_pelatis'], 
//	  'X-NUM-GUESTS'=>'0',
//	]);
//  if (trim_gks($row['gks_nickname_pelatis'])!='') { 
//  	$vevent->add('ATTENDEE', 'mailto:'.$row['user_email_pelatis'], [
//  		'CUTYPE'=>'INDIVIDUAL',
//  		'ROLE'=>'REQ-PARTICIPANT',
//			'PARTSTAT' => 'NEEDS-ACTION',
//  		'RSVP'=>'TRUE',
//			'CN' => $row['user_email_pelatis'], //$row['gks_nickname_pelatis'], 
//  	  'X-NUM-GUESTS'=>'0',
//  	]);
//	}
	  
  $vevent->DESCRIPTION =$html;// ."\n\n". $calendar_message;
  $vevent->LOCATION =$rsrvph; //$topothesia;
  //$vevent->DESTINATION ='ggggggg asd ASD asd dssdf';
  $vevent->SEQUENCE='0';
  $vevent->STATUS='CONFIRMED';  
  $vevent->TRANSP ='OPAQUE'; // ($row['calendar_is_exclusive']==1 ? 'OPAQUE' : 'TRANSPARENT');
  
  
//  $sql_notif="SELECT notification_number,notification_unit,notification_rundate
//  FROM gks_calendar_notification
//  WHERE calendar_id=".$id." AND notification_type='notif'
//  ORDER BY id_calendar_notification;";
//	$result_notif = $db_link->query($sql_notif);  
//	if (!$result_notif) {
//	  debug_mail(false,'error sql',$sql_notif);
//	  $return = array('success' => false, 'message' => base64_encode('sql error'));
//	  echo json_encode($return); die(); }  
//  
//  while ($row_notif = $result_notif->fetch_assoc()) {
//		$mytrigger=0;
//		
//		switch ($row_notif['notification_unit']) {
//			case 'minute':
//				$mytrigger= '-PT'.intval($row_notif['notification_number']).'M';
//				break;
//			case 'hour':
//				$mytrigger= '-PT'.intval($row_notif['notification_number']).'H';
//				break;
//			case 'day':
//				$mytrigger= '-P'.intval($row_notif['notification_number']).'D';
//				break;
//			case 'week':
//				$mytrigger= '-P'.(intval($row_notif['notification_number'])*7).'D';
//				break;
//		}
//		    
//    $valarm = $vcalendar->createComponent('VALARM');
//    //$valarm->DESCRIPTION = 'my alarm';
//    $valarm->ACTION = 'DISPLAY';
//    $valarm->TRIGGER = $mytrigger; //'-P'.$myminutes.'M'; //.date('Ymd\THis\Z',strtotime($row_notif['notification_rundate'])); //.'+00:00'
//    $vevent->add($valarm);
//  }
  

//	$sql_participant="SELECT gks_calendar_participant.calendar_id, gks_calendar_participant.participant_id, 
//	".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile, 
//	gks_calendar_participant.is_organizer, gks_calendar_participant.is_optional,
//	gks_calendar_participant.response_type, gks_calendar_participant.response_date
//	FROM gks_calendar_participant 
//	LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_calendar_participant.participant_id = ".GKS_WP_TABLE_PREFIX."users.ID
//	WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is NOT Null and calendar_id=".$id."
//	ORDER BY gks_calendar_participant.is_organizer DESC, gks_calendar_participant.is_optional, ".GKS_WP_TABLE_PREFIX."users.gks_nickname";
//	$result_participant = $db_link->query($sql_participant);  
//	if (!$result_participant) {
//	  debug_mail(false,'error sql',$sql_participant);
//	  $return = array('success' => false, 'message' => base64_encode('sql error'));
//	  echo json_encode($return); die(); }  
//  
//  while ($row_participant = $result_participant->fetch_assoc()) {
//    $email=trim_gks($row_participant['user_email']);
//    
//    $isorg='ATTENDEE';
//    if ($row_participant['is_organizer']==1) $isorg='ORGANIZER';
//    //$vevent->add($isorg,'mailto:'.$email);
//    
//    $PARTSTAT='';
//    if (trim_gks($row_participant['response_type']) == '')          $PARTSTAT='NEEDS-ACTION';
//    else if (trim_gks($row_participant['response_type']) == 'no')   $PARTSTAT='DECLINED';
//    else if (trim_gks($row_participant['response_type']) == 'yes')  $PARTSTAT='ACCEPTED';
//    else if (trim_gks($row_participant['response_type']) == 'isos') $PARTSTAT='TENTATIVE';
//    
//     
//    $vevent->add($isorg, 'mailto:'.$email, ['CN' => $row_participant['gks_nickname'], 'PARTSTAT' => $PARTSTAT]);
//    
//    //ATTENDEE;RSVP=TRUE:mailto:foo@example.org
//    //ATTENDEE;PARTSTAT=DECLINED;CN=One:mailto:one@example.org
//    //ATTENDEE;PARTSTAT=TENTATIVE;CN=One:mailto:one@example.org
//    //ATTENDEE;PARTSTAT=ACCEPTED;CN=One:mailto:one@example.org
//    //ATTENDEE;CN=One:mailto:one@example.org
//    //ATTENDEE;CN=White;PARTSTAT=NEEDS-ACTION:mailto:white@example.org
//    //ATTENDEE;CUTYPE=INDIVIDUAL;LANGUAGE=en;PARTSTAT=NEEDS-ACTION;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:a2@example.org
//    //ATTENDEE;CUTYPE=INDIVIDUAL;LANGUAGE=en;PARTSTAT=ACCEPTED;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:o@example.org
//    //ATTENDEE;CUTYPE=INDIVIDUAL;LANGUAGE=en;PARTSTAT=NEEDS-ACTION;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:a1@example.org
//    //ATTENDEE;CUTYPE=INDIVIDUAL;LANGUAGE=en;PARTSTAT=NEEDS-ACTION;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:a3@example.org
//
//    //'ATTENDEE' => VObject\Property\ICalendar\CalAddress::class,
//
//    
//  }  
  
//  if (isset($row['activity_color']) and trim_gks($row['activity_color'])!='') {
//    $vevent->add('X-APPLE-CALENDAR-COLOR', trim_gks($row['activity_color']));
//    $vevent->add('COLOR', gks_hexToClosestCss3Name(trim_gks($row['activity_color'])));
//  }

  $vcalendar->add($vevent);
  
  $vcalendar_str = $vcalendar->serialize();
  //echo '<pre>'; print $vcalendar_str; die();
  
  $etag= md5($vcalendar_str);
  $size = strlen($vcalendar_str);
  $uri=$uid.'.ics';
  
  $sql_event="update gks_transfer_reservation set
  calendardata='".$db_link->escape_string($vcalendar_str)."',
  uri='".$db_link->escape_string($uri)."',
  etag='".$db_link->escape_string($etag)."',
  size=".$size.",
  componenttype='VEVENT',
  uid='".$db_link->escape_string($uid)."'
  where id_transfer_reservation=".$id;
	$result_event = $db_link->query($sql_event);  
	if (!$result_event) {
	  debug_mail(false,'error sql',$sql_event);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  

  $sql="select transfer_oxima_driver_id from gks_transfer_reservation_oximata where transfer_reservation_id=".$id;
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  $users=array();
  while ($row = $result->fetch_assoc()) {
    $users[]=array('user_id' => $row['transfer_oxima_driver_id'], 'id_dav_calendar'=>0, 'caldav_synctoken'=>1);
  }  
  
  foreach ($users as &$value) {
    $sql="select id_dav_calendar,caldav_synctoken from gks_calendar_dav_calendars where user_id=".$value['user_id']." and other_myobj='transfer_reservation'";
  	$result = $db_link->query($sql);  
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $value['id_dav_calendar']=$row['id_dav_calendar'];
      $value['caldav_synctoken']=$row['caldav_synctoken'];
    } else {
      $sql="insert into gks_calendar_dav_calendars (
        user_id,other_myobj,caldav_synctoken
      ) values (
        ".$value['user_id'].",'transfer_reservation',1
      )";
    	$result = $db_link->query($sql);  
    	if (!$result) {
    	  debug_mail(false,'error sql',$sql);
    	  $return = array('success' => false, 'message' => base64_encode('sql error'));
    	  echo json_encode($return); die(); }  
      $value['id_dav_calendar'] = $db_link->insert_id; 
      $value['caldav_synctoken']=1;
    }
    
  }
  unset($value);
  
  foreach ($users as $value) {
    $sql="update gks_calendar_dav_calendars set
    caldav_synctoken=".($value['caldav_synctoken'] + 1)."
    where id_dav_calendar=".$value['id_dav_calendar'];
  	$result = $db_link->query($sql);  
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
    
  } 

  $operation=($is_new_rec ? 1 : 2);
  
  foreach ($users as $value) {
    $sql="INSERT INTO gks_calendar_dav_changes (
    uri, synctoken, calendarid, operation
    ) values (
    '".$db_link->escape_string($uri)."',".$value['caldav_synctoken'].",".$value['id_dav_calendar'].",".$operation."
    )";
  	$result = $db_link->query($sql); 
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
  }  
  

  
  
  
  
  //file_put_contents(GKS_SITE_PATH.'tmp/componenttype.txt',$componenttype);
  
}

function this_curr_date_caldav($dd) {
  if (empty($dd)) {
    $ret=''; 
  } else {
    $dd_t=strtotime($dd);
    
    //$ret=mb_substr(getWeekDayName(date('w',$dd_t)),0,2).' '.date('d/m/Y H:i', $dd_t);
    $ret=date('d/m/y H:i', $dd_t);
  }
  return $ret;
}

function guid_for_calendar_ics() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8).'-'
        .substr($charid, 8, 4).'-'
        .substr($charid,12, 4).'-'
        .substr($charid,16, 4).'-'
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT uid from gks_calendar where uid='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    if ($result->num_rows == 0) {
      $sql = "SELECT uid from gks_crm_tasks where uid='".$db_link->escape_string($guid)."'";
      $result = $db_link->query($sql);
      if ($result->num_rows == 0) {
        $sql = "SELECT uid from gks_crm_activity where uid='".$db_link->escape_string($guid)."'";
        $result = $db_link->query($sql);
        if ($result->num_rows == 0) {
          return $guid; 
        }
      }
    }
  }
}


function gks_get_activity_objects(&$objects) {
  global $db_link;
  //print '<pre>';print_r($objects);die();
  
  foreach ($objects as $objkey => &$myobj) {
    $objids=array();
    foreach ($myobj as $objid => $item) {
       $objids[]=$objid;
    } 
      
    if (count($objids)>0) {
  
      
      switch ($objkey) {   
        case 'gks_acc_inv': 
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.user_first_name, gks_acc_inv.user_last_name, 
          gks_acc_inv.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_acc_inv.gks_price_net,inv_acc_number_int
          FROM gks_acc_inv LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_acc_inv.id_acc_inv In (".implode(',',$objids).")";
          //echo '<pre>';print_r($sql_obj);die();
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_acc_inv']]=array(
              'obj_name' => gks_lang('Παραστατικό').': #'.$row_obj['id_acc_inv'],
              'contact_name' => (isset($row_obj['gks_nickname']) ? $row_obj['gks_nickname'] : trim_gks($row_obj['user_last_name'].' '.$row_obj['user_first_name'])),
              'contact_id' => $row_obj['user_id'],
              'esoda' => $row_obj['gks_price_net'],
            );
          }
          break;
        case 'gks_acc_journal':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_acc_journal, acc_journal_descr FROM gks_acc_journal WHERE id_acc_journal In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_acc_journal']]=array(
              'obj_name' => gks_lang('Ημερολόγιο').': '.$row_obj['acc_journal_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_acc_seires':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_acc_seira, seira_descr FROM gks_acc_seires WHERE id_acc_seira In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_acc_seira']]=array(
              'obj_name' => gks_lang('Σειρά').': '.$row_obj['seira_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_company':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_company, company_title FROM gks_company WHERE id_company In (".implode(',',$objids).") order by company_sortorder,company_title";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_company']]=array(
              'obj_name' => gks_lang('Εταιρεία').': '.$row_obj['company_title'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_company_subs':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_company_sub, company_sub_title FROM gks_company_subs WHERE id_company_sub In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_company_sub']]=array(
              'obj_name' => gks_lang('Υποκατάστημα').': '.$row_obj['company_sub_title'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_crm_leads': 
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT gks_crm_leads.id_crm_lead, gks_crm_leads.subject, gks_crm_leads.first_name, gks_crm_leads.last_name, 
          gks_crm_leads.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_crm_leads.esoda
          FROM gks_crm_leads LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_leads.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_crm_leads.id_crm_lead In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_crm_lead']]=array(
              'obj_name' => gks_lang('Ευκαιρία').': '.(empty($row_obj['subject']) ? '#'.$row_obj['id_crm_lead'] : $row_obj['subject']),
              'contact_name' => (isset($row_obj['gks_nickname']) ? $row_obj['gks_nickname'] : trim_gks($row_obj['last_name'].' '.$row_obj['first_name'])),
              'contact_id' => $row_obj['user_id'],
              'esoda' => $row_obj['esoda'],
            );
          }
          break;
        case 'gks_eshop_products':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_product, product_descr FROM gks_eshop_products WHERE id_product In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_product']]=array(
              'obj_name' => gks_lang('Είδος').': '.$row_obj['product_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_eshop_products_categories':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_product_category, product_category_descr FROM gks_eshop_products_categories WHERE id_product_category In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_product_category']]=array(
              'obj_name' => gks_lang('Κατηγορία Είδους').': '.$row_obj['product_category_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_hotel':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_hotel, hotel_title FROM gks_hotel WHERE id_hotel In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_hotel']]=array(
              'obj_name' => gks_lang('Ξενοδοχείο').': '.$row_obj['hotel_title'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_hotel_availability':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_hotel_availability, availability_descr,
          IFNULL(IFNULL(gks_hotel_room_type.room_type_descr, gks_hotel_room.room_descr),'hotel') as mydescr
          FROM (gks_hotel_availability 
          LEFT JOIN gks_hotel_room_type ON gks_hotel_availability.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
          LEFT JOIN gks_hotel_room ON gks_hotel_availability.hotel_room_id = gks_hotel_room.id_hotel_room
          WHERE id_hotel_availability In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_hotel_availability']]=array(
              'obj_name' => gks_lang('Διαθεσιμότητα').': '.$row_obj['mydescr'].((isset($row_obj['availability_descr']) and trim_gks($row_obj['availability_descr'])!='') ? ' - '. $row_obj['availability_descr'] : ''),
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_hotel_floor':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_hotel_floor, floor_descr FROM gks_hotel_floor WHERE id_hotel_floor In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_hotel_floor']]=array(
              'obj_name' => gks_lang('Όροφος').': '.(empty($row_obj['floor_descr']) ? '#'.$row_obj['id_hotel_floor'] : $row_obj['floor_descr']),
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_hotel_price':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_hotel_price, price_descr,gks_hotel_room_type.room_type_descr,gks_hotel_price.price
          FROM gks_hotel_price 
          LEFT JOIN gks_hotel_room_type ON gks_hotel_price.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type
          WHERE id_hotel_price In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_hotel_price']]=array(
              'obj_name' => gks_lang('Τιμή').': '.$row_obj['room_type_descr'] . ((isset($row_obj['price_descr']) and trim_gks($row_obj['price_descr'])!='') ? ' - '. $row_obj['price_descr'] : ''),
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => $row_obj['price'],
            );
          }
          break;
        
        case 'gks_hotel_reservation':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT gks_hotel_reservation.id_hotel_reservation, gks_hotel_reservation.reservation_date, 
          gks_hotel_reservation.user_first_name, gks_hotel_reservation.user_last_name, gks_hotel_reservation.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
          gks_hotel_reservation.gks_price_net
          FROM gks_hotel_reservation 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE id_hotel_reservation In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_hotel_reservation']]=array(
              'obj_name' => gks_lang('Κράτηση').': #'.$row_obj['id_hotel_reservation'],
              'contact_name' => (isset($row_obj['gks_nickname']) ? $row_obj['gks_nickname'] : trim_gks($row_obj['user_last_name'].' '.$row_obj['user_first_name'])),
              'contact_id' => $row_obj['user_id'],
              'esoda' => $row_obj['gks_price_net'],
            );
          }
          break;
        case 'gks_hotel_room':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_hotel_room, room_descr FROM gks_hotel_room  WHERE id_hotel_room In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_hotel_room']]=array(
              'obj_name' => gks_lang('Δωμάτιο').': '.$row_obj['room_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_hotel_room_type':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_hotel_room_type, room_type_descr FROM gks_hotel_room_type  WHERE id_hotel_room_type In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_hotel_room_type']]=array(
              'obj_name' => gks_lang('Τύπος δωματίου').': '.$row_obj['room_type_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_orders':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT gks_orders.id_order, gks_orders.order_date, gks_orders.user_last_name, gks_orders.user_first_name, 
          gks_orders.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_orders.gks_price_net
          FROM gks_orders 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE id_order In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_order']]=array(
              'obj_name' => gks_lang('Παραγγελία').': #'.$row_obj['id_order'],
              'contact_name' => (isset($row_obj['gks_nickname']) ? $row_obj['gks_nickname'] : trim_gks($row_obj['user_last_name'].' '.$row_obj['user_first_name'])),
              'contact_id' => $row_obj['user_id'],
              'esoda' => $row_obj['gks_price_net'],
            );
          }
          break;
        case 'gks_print_forms':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_print_form, print_form_descr FROM gks_print_forms  WHERE id_print_form In (".implode(',',$objids).") order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_print_form']]=array(
              'obj_name' => gks_lang('Φόρμα Εκτύπωσης').': '.$row_obj['print_form_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_production_ergasies':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_production_ergasia, production_ergasia_descr FROM gks_production_ergasies  WHERE id_production_ergasia In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_production_ergasia']]=array(
              'obj_name' => gks_lang('Εργασία').': '.$row_obj['production_ergasia_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_production_posta':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_production_posto, production_posto_descr FROM gks_production_posta  WHERE id_production_posto In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_production_posto']]=array(
              'obj_name' => gks_lang('Πόστο').': '.$row_obj['production_posto_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_production_bom':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_production_bom, bom_descr FROM gks_production_bom  WHERE id_production_bom In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_production_posto']]=array(
              'obj_name' => gks_lang('Συνταγή').': '.$row_obj['bom_descr'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
          
        case 'gks_users_groups':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_users_group, group_title FROM gks_users_groups  WHERE id_users_group In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_users_group']]=array(
              'obj_name' => gks_lang('Ομάδα Επαφών').': '.$row_obj['group_title'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'gks_warehouses':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_warehouse, warehouse_name FROM gks_warehouses WHERE id_warehouse In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_warehouse']]=array(
              'obj_name' => gks_lang('Αποθήκη').': '.$row_obj['warehouse_name'],
              'contact_name' => '',
              'contact_id' => 0,
              'esoda' => 0,
            );
          }
          break;
        case 'wp_users':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT ID, gks_nickname FROM ".GKS_WP_TABLE_PREFIX."users WHERE ID In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['ID']]=array(
              'obj_name' => gks_lang('Επαφή').': '.$row_obj['gks_nickname'],
              'contact_name' => '',
              'contact_id' => '',
              'esoda' => 0,
            );
          }
          break;


        case 'gks_acc_pay': 
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT gks_acc_pay.id_acc_pay, gks_acc_pay.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_acc_pay.gks_price_total, gks_acc_pay.pay_acc_number_int
          FROM gks_acc_pay 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_acc_pay.id_acc_pay In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_acc_pay']]=array(
              'obj_name' => gks_lang('Πληρωμή').': #'.$row_obj['id_acc_pay'],
              'contact_name' => $row_obj['gks_nickname'],
              'contact_id' => $row_obj['user_id'],
              'esoda' => $row_obj['gks_price_total'],
            );
          }
          break;
          

        case 'gks_eshops':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_eshop, eshop_name FROM gks_eshops WHERE id_eshop In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_eshop']]=array(
              'obj_name' => gks_lang('eshop').': '.$row_obj['eshop_name'],
              'contact_name' => '',
              'contact_id' => '',
              'esoda' => 0,
            );
          }
          break;


        case 'gks_eshop_products_brands':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_product_brand, product_brand_descr FROM gks_eshop_products_brands WHERE id_product_brand In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_product_brand']]=array(
              'obj_name' => gks_lang('Μάρκα').': '.$row_obj['product_brand_descr'],
              'contact_name' => '',
              'contact_id' => '',
              'esoda' => 0,
            );
          }
          break;


        case 'gks_crm_tasks':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT gks_crm_tasks.id_crm_task, gks_crm_tasks.subject, gks_crm_tasks.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,gks_crm_tasks.esoda
          FROM gks_crm_tasks 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE id_crm_task In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_crm_task']]=array(
              'obj_name' => gks_lang('Εργασία').': '.$row_obj['subject'],
              'contact_name' => $row_obj['gks_nickname'],
              'contact_id' => $row_obj['user_id'],
              'esoda' => $row_obj['esoda'],
            );
          }
          break;
            
        case 'gks_crm_machine':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT gks_crm_machine.id_crm_machine, gks_crm_machine.crm_machine_name, gks_crm_machine.crm_machine_user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_crm_machine 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_machine.crm_machine_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE id_crm_machine In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_crm_machine']]=array(
              'obj_name' => gks_lang('Συσκευή').': '.$row_obj['crm_machine_name'],
              'contact_name' => $row_obj['gks_nickname'],
              'contact_id' => $row_obj['crm_machine_user_id'],
              'esoda' => 0,
            );
          }
          break;
  
  
  
        case 'gks_orders_occasion':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT gks_orders_occasion.id_order_occasion, gks_orders_occasion.title, 
          gks_orders_occasion.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_occasion_types.occasion_type_descr
          FROM (gks_orders_occasion 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders_occasion.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
          LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type
          WHERE gks_orders_occasion.id_order_occasion In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_order_occasion']]=array(
              'obj_name' => gks_lang('Περίσταση').': '.trim_gks($row_obj['occasion_type_descr']).' - '.$row_obj['title'],
              'contact_name' => $row_obj['gks_nickname'],
              'contact_id' => $row_obj['user_id'],
              'esoda' => 0,
            );
          }
          break;  
  
        case 'gks_custom_table':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_custom_table, custom_table_descr FROM gks_custom_table WHERE id_custom_table In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_custom_table']]=array(
              'obj_name' => gks_lang('Προσαρμογή αντικειμένου').': '.$row_obj['custom_table_descr'],
              'contact_name' => '',
              'contact_id' => '',
              'esoda' => 0,
            );
          }
          break;
  
        case 'gks_crm_channel_sale':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_crm_channel_sale, crm_channel_sale_descr FROM gks_crm_channel_sale WHERE id_crm_channel_sale In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_crm_channel_sale']]=array(
              'obj_name' => gks_lang('Κανάλι πωλήσεων').': '.$row_obj['crm_channel_sale_descr'],
              'contact_name' => '',
              'contact_id' => '',
              'esoda' => 0,
            );
          }
          break;
        case 'gks_crm_leads_status':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_crm_lead_status, lead_status_descr FROM gks_crm_leads_status WHERE id_crm_lead_status In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_crm_lead_status']]=array(
              'obj_name' => gks_lang('Κατάσταση Ευκαιριών').': '.$row_obj['lead_status_descr'],
              'contact_name' => '',
              'contact_id' => '',
              'esoda' => 0,
            );
          }
          break;
        case 'gks_crm_tasks_status':
          //echo '<pre>'.$objkey;die();
          $sql_obj="SELECT id_crm_task_status, task_status_descr FROM gks_crm_tasks_status WHERE id_crm_task_status In (".implode(',',$objids).")";
          $res_obj = $db_link->query($sql_obj);        
          if (!$res_obj) {debug_mail(false,'error sql',$sql_obj);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die();}   
          while ($row_obj = $res_obj->fetch_assoc()) {
            $myobj[$row_obj['id_crm_task_status']]=array(
              'obj_name' => gks_lang('Κατάσταση Εργασιών').': '.$row_obj['task_status_descr'],
              'contact_name' => '',
              'contact_id' => '',
              'esoda' => 0,
            );
          }
          break;
  
  
  
  
  
            
  
        default: 
      
      }  
    }
  
  } 
  unset($myobj);  
}

function gks_hexToClosestCss3Name(string $hex): string {
  if (!(strlen($hex)==7 and substr($hex,0,1)=='#')) return $hex;
  
//  $css3Colors = [
//      'aliceblue' => '#f0f8ff', 'antiquewhite' => '#faebd7', 'aqua' => '#00ffff',
//      'aquamarine' => '#7fffd4', 'azure' => '#f0ffff', 'beige' => '#f5f5dc',
//      'bisque' => '#ffe4c4', 'black' => '#000000', 'blanchedalmond' => '#ffebcd',
//      'blue' => '#0000ff', 'blueviolet' => '#8a2be2', 'brown' => '#a52a2a',
//      'burlywood' => '#deb887', 'cadetblue' => '#5f9ea0', 'chartreuse' => '#7fff00',
//      'chocolate' => '#d2691e', 'coral' => '#ff7f50', 'cornflowerblue' => '#6495ed',
//      'cornsilk' => '#fff8dc', 'crimson' => '#dc143c', 'cyan' => '#00ffff',
//      'darkblue' => '#00008b', 'darkcyan' => '#008b8b', 'darkgoldenrod' => '#b8860b',
//      'darkgray' => '#a9a9a9', 'darkgreen' => '#006400', 'darkkhaki' => '#bdb76b',
//      'darkmagenta' => '#8b008b', 'darkolivegreen' => '#556b2f', 'darkorange' => '#ff8c00',
//      'darkorchid' => '#9932cc', 'darkred' => '#8b0000', 'darksalmon' => '#e9967a',
//      'darkseagreen' => '#8fbc8f', 'darkslateblue' => '#483d8b', 'darkslategray' => '#2f4f4f',
//      'darkturquoise' => '#00ced1', 'darkviolet' => '#9400d3', 'deeppink' => '#ff1493',
//      'deepskyblue' => '#00bfff', 'dimgray' => '#696969', 'dodgerblue' => '#1e90ff',
//      'firebrick' => '#b22222', 'floralwhite' => '#fffaf0', 'forestgreen' => '#228b22',
//      'fuchsia' => '#ff00ff', 'gainsboro' => '#dcdcdc', 'ghostwhite' => '#f8f8ff',
//      'gold' => '#ffd700', 'goldenrod' => '#daa520', 'gray' => '#808080',
//      'green' => '#008000', 'greenyellow' => '#adff2f', 'honeydew' => '#f0fff0',
//      'hotpink' => '#ff69b4', 'indianred' => '#cd5c5c', 'indigo' => '#4b0082',
//      'ivory' => '#fffff0', 'khaki' => '#f0e68c', 'lavender' => '#e6e6fa',
//      'lavenderblush' => '#fff0f5', 'lawngreen' => '#7cfc00', 'lemonchiffon' => '#fffacd',
//      'lightblue' => '#add8e6', 'lightcoral' => '#f08080', 'lightcyan' => '#e0ffff',
//      'lightgoldenrodyellow' => '#fafad2', 'lightgray' => '#d3d3d3', 'lightgreen' => '#90ee90',
//      'lightpink' => '#ffb6c1', 'lightsalmon' => '#ffa07a', 'lightseagreen' => '#20b2aa',
//      'lightskyblue' => '#87cefa', 'lightslategray' => '#778899', 'lightsteelblue' => '#b0c4de',
//      'lightyellow' => '#ffffe0', 'lime' => '#00ff00', 'limegreen' => '#32cd32',
//      'linen' => '#faf0e6', 'magenta' => '#ff00ff', 'maroon' => '#800000',
//      'mediumaquamarine' => '#66cdaa', 'mediumblue' => '#0000cd', 'mediumorchid' => '#ba55d3',
//      'mediumpurple' => '#9370db', 'mediumseagreen' => '#3cb371', 'mediumslateblue' => '#7b68ee',
//      'mediumspringgreen' => '#00fa9a', 'mediumturquoise' => '#48d1cc', 'mediumvioletred' => '#c71585',
//      'midnightblue' => '#191970', 'mintcream' => '#f5fffa', 'mistyrose' => '#ffe4e1',
//      'moccasin' => '#ffe4b5', 'navajowhite' => '#ffdead', 'navy' => '#000080',
//      'oldlace' => '#fdf5e6', 'olive' => '#808000', 'olivedrab' => '#6b8e23',
//      'orange' => '#ffa500', 'orangered' => '#ff4500', 'orchid' => '#da70d6',
//      'palegoldenrod' => '#eee8aa', 'palegreen' => '#98fb98', 'paleturquoise' => '#afeeee',
//      'palevioletred' => '#db7093', 'papayawhip' => '#ffefd5', 'peachpuff' => '#ffdab9',
//      'peru' => '#cd853f', 'pink' => '#ffc0cb', 'plum' => '#dda0dd',
//      'powderblue' => '#b0e0e6', 'purple' => '#800080', 
//      'red' => '#ff0000', 'rosybrown' => '#bc8f8f', 'royalblue' => '#4169e1',
//      'saddlebrown' => '#8b4513', 'salmon' => '#fa8072', 'sandybrown' => '#f4a460',
//      'seagreen' => '#2e8b57', 'seashell' => '#fff5ee', 'sienna' => '#a0522d',
//      'silver' => '#c0c0c0', 'skyblue' => '#87ceeb', 'slateblue' => '#6a5acd',
//      'slategray' => '#708090', 'snow' => '#fffafa', 'springgreen' => '#00ff7f',
//      'steelblue' => '#4682b4', 'tan' => '#d2b48c', 'teal' => '#008080',
//      'thistle' => '#d8bfd8', 'tomato' => '#ff6347', 'turquoise' => '#40e0d0',
//      'violet' => '#ee82ee', 'wheat' => '#f5deb3', 'white' => '#ffffff',
//      'whitesmoke' => '#f5f5f5', 'yellow' => '#ffff00', 'yellowgreen' => '#9acd32',
//  ];

  $css3Colors = [
    'aliceblue'            => '#f0f8ff',
    'antiquewhite'         => '#faebd7',
    'aqua'                 => '#00ffff',
    'aquamarine'           => '#7fffd4',
    'azure'                => '#f0ffff',
    'beige'                => '#f5f5dc',
    'bisque'               => '#ffe4c4',
    'black'                => '#000000',
    'blanchedalmond'       => '#ffebcd',
    'blue'                 => '#0000ff',
    'blueviolet'           => '#8a2be2',
    'brown'                => '#a52a2a',
    'burlywood'            => '#deb887',
    'cadetblue'            => '#5f9ea0',
    'chartreuse'           => '#7fff00',
    'chocolate'            => '#d2691e',
    'coral'                => '#ff7f50',
    'cornflowerblue'       => '#6495ed',
    'cornsilk'             => '#fff8dc',
    'crimson'              => '#dc143c',
    'cyan'                 => '#00ffff',
    'darkblue'             => '#00008b',
    'darkcyan'             => '#008b8b',
    'darkgoldenrod'        => '#b8860b',
    'darkgray'             => '#a9a9a9',
    'darkgreen'            => '#006400',
    'darkgrey'             => '#a9a9a9',
    'darkkhaki'            => '#bdb76b',
    'darkmagenta'          => '#8b008b',
    'darkolivegreen'       => '#556b2f',
    'darkorange'           => '#ff8c00',
    'darkorchid'           => '#9932cc',
    'darkred'              => '#8b0000',
    'darksalmon'           => '#e9967a',
    'darkseagreen'         => '#8fbc8f',
    'darkslateblue'        => '#483d8b',
    'darkslategray'        => '#2f4f4f',
    'darkslategrey'        => '#2f4f4f',
    'darkturquoise'        => '#00ced1',
    'darkviolet'           => '#9400d3',
    'deeppink'             => '#ff1493',
    'deepskyblue'          => '#00bfff',
    'dimgray'              => '#696969',
    'dimgrey'              => '#696969',
    'dodgerblue'           => '#1e90ff',
    'firebrick'            => '#b22222',
    'floralwhite'          => '#fffaf0',
    'forestgreen'          => '#228b22',
    'fuchsia'              => '#ff00ff',
    'gainsboro'            => '#dcdcdc',
    'ghostwhite'           => '#f8f8ff',
    'gold'                 => '#ffd700',
    'goldenrod'            => '#daa520',
    'gray'                 => '#808080',
    'green'                => '#008000',
    'greenyellow'          => '#adff2f',
    'grey'                 => '#808080',
    'honeydew'             => '#f0fff0',
    'hotpink'              => '#ff69b4',
    'indianred'            => '#cd5c5c',
    'indigo'               => '#4b0082',
    'ivory'                => '#fffff0',
    'khaki'                => '#f0e68c',
    'lavender'             => '#e6e6fa',
    'lavenderblush'        => '#fff0f5',
    'lawngreen'            => '#7cfc00',
    'lemonchiffon'         => '#fffacd',
    'lightblue'            => '#add8e6',
    'lightcoral'           => '#f08080',
    'lightcyan'            => '#e0ffff',
    'lightgoldenrodyellow' => '#fafad2',
    'lightgray'            => '#d3d3d3',
    'lightgreen'           => '#90ee90',
    'lightgrey'            => '#d3d3d3',
    'lightpink'            => '#ffb6c1',
    'lightsalmon'          => '#ffa07a',
    'lightseagreen'        => '#20b2aa',
    'lightskyblue'         => '#87cefa',
    'lightslategray'       => '#778899',
    'lightslategrey'       => '#778899',
    'lightsteelblue'       => '#b0c4de',
    'lightyellow'          => '#ffffe0',
    'lime'                 => '#00ff00',
    'limegreen'            => '#32cd32',
    'linen'                => '#faf0e6',
    'magenta'              => '#ff00ff',
    'maroon'               => '#800000',
    'mediumaquamarine'     => '#66cdaa',
    'mediumblue'           => '#0000cd',
    'mediumorchid'         => '#ba55d3',
    'mediumpurple'         => '#9370db',
    'mediumseagreen'       => '#3cb371',
    'mediumslateblue'      => '#7b68ee',
    'mediumspringgreen'    => '#00fa9a',
    'mediumturquoise'      => '#48d1cc',
    'mediumvioletred'      => '#c71585',
    'midnightblue'         => '#191970',
    'mintcream'            => '#f5fffa',
    'mistyrose'            => '#ffe4e1',
    'moccasin'             => '#ffe4b5',
    'navajowhite'          => '#ffdead',
    'navy'                 => '#000080',
    'oldlace'              => '#fdf5e6',
    'olive'                => '#808000',
    'olivedrab'            => '#6b8e23',
    'orange'               => '#ffa500',
    'orangered'            => '#ff4500',
    'orchid'               => '#da70d6',
    'palegoldenrod'        => '#eee8aa',
    'palegreen'            => '#98fb98',
    'paleturquoise'        => '#afeeee',
    'palevioletred'        => '#db7093',
    'papayawhip'           => '#ffefd5',
    'peachpuff'            => '#ffdab9',
    'peru'                 => '#cd853f',
    'pink'                 => '#ffc0cb',
    'plum'                 => '#dda0dd',
    'powderblue'           => '#b0e0e6',
    'purple'               => '#800080',
    'red'                  => '#ff0000',
    'rosybrown'            => '#bc8f8f',
    'royalblue'            => '#4169e1',
    'saddlebrown'          => '#8b4513',
    'salmon'               => '#fa8072',
    'sandybrown'           => '#f4a460',
    'seagreen'             => '#2e8b57',
    'seashell'             => '#fff5ee',
    'sienna'               => '#a0522d',
    'silver'               => '#c0c0c0',
    'skyblue'              => '#87ceeb',
    'slateblue'            => '#6a5acd',
    'slategray'            => '#708090',
    'slategrey'            => '#708090',
    'snow'                 => '#fffafa',
    'springgreen'          => '#00ff7f',
    'steelblue'            => '#4682b4',
    'tan'                  => '#d2b48c',
    'teal'                 => '#008080',
    'thistle'              => '#d8bfd8',
    'tomato'               => '#ff6347',
    'turquoise'            => '#40e0d0',
    'violet'               => '#ee82ee',
    'wheat'                => '#f5deb3',
    'white'                => '#ffffff',
    'whitesmoke'           => '#f5f5f5',
    'yellow'               => '#ffff00',
    'yellowgreen'          => '#9acd32',
  ];
  
  $hex = ltrim($hex, '#');
  if (strlen($hex) === 3) {
      $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
  }

  $r1 = hexdec(substr($hex, 0, 2));
  $g1 = hexdec(substr($hex, 2, 2));
  $b1 = hexdec(substr($hex, 4, 2));

  $bestName = '';
  $bestDist = PHP_INT_MAX;

  foreach ($css3Colors as $name => $colorHex) {
      $colorHex = ltrim($colorHex, '#');
      $r2 = hexdec(substr($colorHex, 0, 2));
      $g2 = hexdec(substr($colorHex, 2, 2));
      $b2 = hexdec(substr($colorHex, 4, 2));

      $dist = sqrt(($r1-$r2)**2 + ($g1-$g2)**2 + ($b1-$b2)**2);

      if ($dist < $bestDist) {
          $bestDist = $dist;
          $bestName = $name;
      }
  }

  return $bestName;
}
  