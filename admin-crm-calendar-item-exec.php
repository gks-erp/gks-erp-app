<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Επεξεργασία καταχώρησης ημερολογίου');
db_open();
stat_record();
$id=0;if (isset($_POST['id'])) $id=intval($_POST['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_calendar',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
if ($cmd!='add' && $cmd!='edit' && $cmd!='resize' && $cmd!='move') {
  debug_mail(false,'cmd is not good','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εντολή δεν είναι σωστή').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
  echo json_encode($return); die();}



if ($id<=0 and $id!=-1) {  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
  echo json_encode($return); die();}

$allday=0; if (isset($_POST['allday'])) $allday=(intval($_POST['allday']) == 0 ? 0 : 1);

$start='';
if (isset($_POST['start'])) {
  $start=trim_gks($_POST['start']);
  if ($allday==1 and strlen($start)==19) {  // 2021-01-14 09:30:00 -> 2021-01-14 00:00:00
    $start=substr($start, 0, 11).' 00:00:00';
    //echo $start; die();
  }
  $start=_time_user(strtotime($start),-1);
  $start=date('Y-m-d H:i:s',$start);
}

$end='';
if (isset($_POST['end'])) {
  $end=trim_gks($_POST['end']);
  $end=_time_user(strtotime($end),-1);
  $end=date('Y-m-d H:i:s',$end);
} else {
  if (showDate(strtotime($start),"H:i:s",1)=='00:00:00') {
    $end = $start;
  } else {
    $end = date('Y-m-d H:i:s', strtotime($start) + 1*60*60);
  }
}



$user_is_other=0; if (isset($_POST['user_is_other'])) $user_is_other=intval($_POST['user_is_other']);
$c_user_id_other=0; if (isset($_POST['c_user_id_other'])) $c_user_id_other=intval($_POST['c_user_id_other']);
$calendar_user_id=$my_wp_user_id;
if ($user_is_other==1 and $c_user_id_other>0) $calendar_user_id=$c_user_id_other;

$c_title=''; if (isset($_POST['c_title'])) $c_title=trim_gks(base64_decode($_POST['c_title']));
$c_message=''; if (isset($_POST['c_message'])) $c_message=trim_gks(base64_decode($_POST['c_message']));
$c_is_exclusive=0; if (isset($_POST['c_is_exclusive'])) $c_is_exclusive=intval($_POST['c_is_exclusive']);
$c_is_private=0; if (isset($_POST['c_is_private'])) $c_is_private=intval($_POST['c_is_private']);
$c_color=''; if (isset($_POST['c_color'])) $c_color=trim_gks(base64_decode($_POST['c_color']));
$c_odos=''; if (isset($_POST['c_odos'])) $c_odos=trim_gks(base64_decode($_POST['c_odos']));
$c_arithmos=''; if (isset($_POST['c_arithmos'])) $c_arithmos=trim_gks(base64_decode($_POST['c_arithmos']));
$c_orofos=''; if (isset($_POST['c_orofos'])) $c_orofos=trim_gks(base64_decode($_POST['c_orofos']));
$c_perioxi=''; if (isset($_POST['c_perioxi'])) $c_perioxi=trim_gks(base64_decode($_POST['c_perioxi']));
$c_poli=''; if (isset($_POST['c_poli'])) $c_poli=trim_gks(base64_decode($_POST['c_poli']));
$c_tk=''; if (isset($_POST['c_tk'])) $c_tk=trim_gks(base64_decode($_POST['c_tk']));
$c_country_id=0; if (isset($_POST['c_country_id'])) $c_country_id=intval($_POST['c_country_id']);
$c_nomos_id=0; if (isset($_POST['c_nomos_id'])) $c_nomos_id=intval($_POST['c_nomos_id']);
$c_map_latitude=0; if (isset($_POST['c_map_latitude'])) $c_map_latitude=floatval($_POST['c_map_latitude']);
$c_map_longitude=0; if (isset($_POST['c_map_longitude'])) $c_map_longitude=floatval($_POST['c_map_longitude']);


$c_notification=''; if (isset($_POST['c_notification'])) $c_notification = trim_gks(base64_decode($_POST['c_notification']));
if ($c_notification!='') {
	$c_notification = json_decode($c_notification, true);
	if ($c_notification === null && json_last_error() !== JSON_ERROR_NONE) {
	  debug_mail(false,'json_decode error',$_POST['c_notification']);
	  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
	  echo json_encode($return); die();}
} else {
	$c_notification=array();
}
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($c_notification,true)));
//echo json_encode($return); die();


$c_participant=''; if (isset($_POST['c_participant'])) $c_participant = trim_gks(base64_decode($_POST['c_participant']));
if ($c_participant!='') {
	$c_participant = json_decode($c_participant, true);
	if ($c_participant === null && json_last_error() !== JSON_ERROR_NONE) {
	  debug_mail(false,'json_decode error',$_POST['c_participant']);
	  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
	  echo json_encode($return); die();}
} else {
	$c_participant=array();
}

if ($cmd=='edit' or $cmd=='add') {
	if ($c_title=='') {
	  debug_mail(false,'c_title is empty',$c_title);
	  $return = array('success' => false, 'message' => base64_encode(gks_lang('Εισάγετε το Θέμα')));
	  echo json_encode($return); die();}
}







//$return = array('success' => false, 'message' => base64_encode('<pre>'.$id."\n".$start."\n".$end."\n".print_r($_POST,true)));
//echo json_encode($return); die();



$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  $sql=gks_calendar_sql_event("id_calendar=".$id);
  $sql.=" limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
  $row_old = $result->fetch_assoc();
}
  

if ($cmd=='resize' or $cmd=='move') {
      
  $sql="update gks_calendar set
  calendar_start ='".$start."',
  calendar_end ='".$end."',
  calendar_allday = ".$allday.",
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  where id_calendar=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  gks_activity_update($id,$start);
  
  gks_calendar_event_notification_recalc_rundate($id,$is_new_rec);
  
  
} else if ($cmd=='edit' or $cmd=='add') {
  
  if ($id==-1) {
    $sql="insert into gks_calendar (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    
    $id = $db_link->insert_id; 
  }
    
  if ($allday!=0) {
    $end=date('Y-m-d H:i:s',strtotime($end)+ 24*60*60);
  }
  
  $sql="update gks_calendar set 
  calendar_user_id=".$calendar_user_id.",
  calendar_start ='".$start."',
  calendar_end ='".$end."',
  calendar_allday = ".$allday.",
  calendar_title='".$db_link->escape_string($c_title)."',
  calendar_message='".$db_link->escape_string($c_message)."',
  calendar_is_exclusive=".$c_is_exclusive.",
  calendar_is_private=".$c_is_private.",
  calendar_color=".($c_color=='' ? 'null' : "'".$db_link->escape_string($c_color)."'").",
  calendar_odos='".$db_link->escape_string($c_odos)."',
  calendar_arithmos='".$db_link->escape_string($c_arithmos)."',
  calendar_orofos='".$db_link->escape_string($c_orofos)."',
  calendar_perioxi='".$db_link->escape_string($c_perioxi)."',
  calendar_poli='".$db_link->escape_string($c_poli)."',
  calendar_tk='".$db_link->escape_string($c_tk)."',
  calendar_country_id=".$c_country_id.",
  calendar_nomos_id=".$c_nomos_id.",
  calendar_map_latitude='".number_format($c_map_latitude,16,'.','')."',
  calendar_map_longitude='".number_format($c_map_longitude,16,'.','')."',
  
  
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  where id_calendar = ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
    
    
  gks_activity_update($id,$start);  
	
	 
	$notification=array();
	foreach ($c_notification as $value) {
		$t=''; if (isset($value['type'])) $t=trim_gks($value['type']);
		$n=0; if (isset($value['number'])) $n=intval($value['number']);
		$u=''; if (isset($value['unit'])) $u=trim_gks($value['unit']);
		if ( ($t=='email' or $t=='notif') and
		     ($n>=0) and
		     ($u=='minute' or $u=='hour' or $u=='day' or $u=='week')) {
			$notification[]=array(
				'type' => $t,
				'number' => $n,
				'unit' => $u,
			);
		}
	}
	//print '<pre>';print_r($c_notification);print_r($notification);die();
	$not_delete_records=array();
	foreach($notification as $value) {
		$sql="select id_calendar_notification 
		from gks_calendar_notification 
		where calendar_id=".$id." 
		and notification_type='".$db_link->escape_string($value['type'])."' 
		and notification_number=".$value['number']."
		and notification_unit='".$db_link->escape_string($value['unit'])."'";
	  $result = $db_link->query($sql);  
	  if (!$result) {
	    debug_mail(false,'error sql',$sql);
	    $return = array('success' => false, 'message' => base64_encode('sql error'));
	    echo json_encode($return); die(); }  
		
		if ($result->num_rows>=1) {
			$row=$result->fetch_assoc();
			$not_delete_records[]=$row['id_calendar_notification'];
		} else {
			$sql="insert into gks_calendar_notification (
			mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
			calendar_id,notification_type,notification_number,notification_unit
			) values (
			now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
			".$id.",'".$db_link->escape_string($value['type'])."',".$value['number'].",'".$db_link->escape_string($value['unit'])."'
			)";
		  $result = $db_link->query($sql);  
		  if (!$result) {
		    debug_mail(false,'error sql',$sql);
		    $return = array('success' => false, 'message' => base64_encode('sql error'));
		    echo json_encode($return); die(); }  
			$id_calendar_notification = $db_link->insert_id; 
			$not_delete_records[]=$id_calendar_notification;
		}
	}
	$sql="delete from gks_calendar_notification where calendar_id=".$id;
	if (count($not_delete_records)>0) $sql.=" and id_calendar_notification not in (".implode(',',$not_delete_records).")";
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  



	$participant=array();
	foreach ($c_participant as $value) {
		$us=0; if (isset($value['user_id'])) $us=intval($value['user_id']);
		$or=0; if (isset($value['is_org']))  $or=intval($value['is_org']);
		$op=0; if (isset($value['is_opt']))  $op=intval($value['is_opt']);
		$rt=''; if (isset($value['r_type'])) $rt=trim_gks($value['r_type']);
		if ( ($us>=0) and
		     ($or==0 or $or==1) and
		     ($op==0 or $op==1) and
		     ($rt=='' or $rt=='yes' or $rt=='no' or $rt=='isos')) {
			$participant[]=array(
				'participant_id' => $us,
				'is_organizer' => $or,
				'is_optional' => $op,
				'response_type' => $rt,
			);
		}
	}
	//print '<pre>';print_r($c_participant);print_r($participant);die();
	$not_delete_records=array();
	foreach($participant as $value) {
		$sql="select * 
		from gks_calendar_participant 
		where calendar_id=".$id." 
		and participant_id=".$value['participant_id'];
	  $result = $db_link->query($sql);  
	  if (!$result) {
	    debug_mail(false,'error sql',$sql);
	    $return = array('success' => false, 'message' => base64_encode('sql error'));
	    echo json_encode($return); die(); }  
		
		if ($result->num_rows>=1) {
			$row=$result->fetch_assoc();
			$not_delete_records[]=$row['id_calendar_participant'];
			$id_calendar_participant=$row['id_calendar_participant'];
			if ($row['is_organizer'] != $value['is_organizer'] or 
			    $row['is_optional'] != $value['is_optional'] or 
			    trim_gks($row['response_type']) != trim_gks($value['response_type'])) {
			      
			  $sql="update gks_calendar_participant set 
			  is_organizer=".$value['is_organizer'].",
			  is_optional=".$value['is_optional'].",
			  response_type='".$db_link->escape_string($value['response_type'])."',
			  mydate_edit=now(),
			  user_id_edit=".$my_wp_user_id.",
			  myip='".$db_link->escape_string($gkIP)."'
			  where id_calendar_participant=".$row['id_calendar_participant'];
    	  $result = $db_link->query($sql);  
    	  if (!$result) {
    	    debug_mail(false,'error sql',$sql);
    	    $return = array('success' => false, 'message' => base64_encode('sql error'));
    	    echo json_encode($return); die(); }
			}
		} else {
			$sql="insert into gks_calendar_participant (
			mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
			calendar_id,participant_id,is_organizer,is_optional,response_type
			) values (
			now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
			".$id.",".$value['participant_id'].",".$value['is_organizer'].",".$value['is_optional'].",'".$db_link->escape_string($value['response_type'])."'
			)";
		  $result = $db_link->query($sql);  
		  if (!$result) {
		    debug_mail(false,'error sql',$sql);
		    $return = array('success' => false, 'message' => base64_encode('sql error'));
		    echo json_encode($return); die(); }  
			$id_calendar_participant = $db_link->insert_id; 
			$not_delete_records[]=$id_calendar_participant;
		}
	}
	$sql="delete from gks_calendar_participant where calendar_id=".$id;
	if (count($not_delete_records)>1) $sql.=" and id_calendar_participant not in (".implode(',',$not_delete_records).")"; //na einai toulaxiston 2 records
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
	  
	  
	
	gks_calendar_event_notification_recalc_rundate($id,$is_new_rec);

}


$params=array();
$params['id_calendar']=$id;

//$event=gks_calendar_get_events("gks_calendar.id_calendar=".$id);
$event=gks_calendar_get_events($params);
$event=$event[0];

$return = array('success' => true, 'message' => base64_encode('OK'), 'event' => $event);
echo json_encode($return); die();  



function gks_activity_update($id,$calendar_start) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  if ($id<=0) return;
  $sql="select * from gks_crm_activity where calendar_id=".$id;
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  
  
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $id_crm_activity=intval($row['id_crm_activity']);
    
    $activity_duedate=date('Y-m-d H:i:s',_time_user(strtotime($calendar_start),0));
    $sql="update gks_crm_activity set 
    activity_duedate='".$activity_duedate."', 
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_crm_activity=".$id_crm_activity;
  	$result = $db_link->query($sql);  
  	if (!$result) {
  	  debug_mail(false,'error sql',$sql);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
    
    gks_calendar_event_update_dav_activity($id_crm_activity,false);
  }
  
  
  
}