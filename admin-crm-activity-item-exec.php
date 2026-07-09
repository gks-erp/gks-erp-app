<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$id=0;
if (isset($_POST['id'])) $id=intval($_POST['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Δραστηριότητας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_activity',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
if ($cmd!='edit' and $cmd!='get') {debug_mail(false,'emptyl','cmd is not ok',$cmd);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος εντολή').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}
$page=''; if (isset($_POST['page'])) $page=trim_gks(base64_decode($_POST['page']));
$model=''; if (isset($_POST['model'])) $model=trim_gks(base64_decode($_POST['model']));
$model_id=0;if (isset($_POST['model_id'])) $model_id=intval($_POST['model_id']);
$status=''; if (isset($_POST['status'])) $status=trim_gks(base64_decode($_POST['status']));
$user_id=0;if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$type_id=0;if (isset($_POST['type_id'])) $type_id=intval($_POST['type_id']);
$duedate='';if (isset($_POST['duedate'])) {
  if ($_POST['duedate'] == '__/__/____' or $_POST['duedate'] == '__/__/____ __:__') $_POST['duedate']='';
  $duedate=trim_gks(stripslashes(urldecode($_POST['duedate'])));
  if ($duedate!='') {
    if (strlen($duedate)==10) { // 31/12/2021
      $parts=explode('/',$duedate);
      $duedate=$parts[2].'-'.$parts[1].'-'.$parts[0];
    } else if (strlen($duedate)==16) { //31/12/2021 12:34
      $duedate = date_create_from_format('d/m/Y H:i',$duedate );
      $duedate = date_timestamp_get($duedate);
      $duedate=_time_user($duedate,-1); //se utc
      $duedate = date('Y-m-d H:i:s',$duedate);
      //echo '<pre>'.$duedate;die();
    }
    //echo $duedate;die();
    //$duedate = mystrtodb($duedate.' 00:00');
    //$duedate=strtotime($duedate) + (24 + GKS_ERP_START_VARDIA)*60*60;
    //$duedate=date('Y-m-d H:i:s', $duedate);
  }
  //echo $_POST['duedate'].'|'.$duedate; die();
}
$notification=0;if (isset($_POST['notification'])) $notification=intval($_POST['notification']);
if ($notification!=1) $notification=0;
$diarkeia=0;if (isset($_POST['diarkeia'])) $diarkeia=intval($_POST['diarkeia']);
$color=''; if (isset($_POST['color'])) $color=trim_gks(base64_decode($_POST['color']));
$subject=''; if (isset($_POST['subject'])) $subject=trim_gks(base64_decode($_POST['subject']));
$message=''; if (isset($_POST['message'])) $message=trim_gks(base64_decode($_POST['message']));  





$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  $sql_row ="SELECT gks_crm_activity.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_crm_activity_types.crm_activity_type_descr
  FROM (((gks_crm_activity 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_activity.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_activity.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN gks_crm_activity_types ON gks_crm_activity.activity_type_id = gks_crm_activity_types.id_crm_activity_type) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_activity.activity_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  where gks_crm_activity.id_crm_activity=".$id." limit 1";
  
  $result = $db_link->query($sql_row);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_row);
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


if ($cmd=='get') {
  if ($id<=0) {
    debug_mail(false,'the id is invalid for get','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Το ID είναι λάθος')));
    echo json_encode($return); die();}
  
  //$duedate=date('Y-m-d H:i:s',$duedate);
  //echo '<pre>';echo $duedate;die();
  //echo '<pre>';print_r($row_old);die();
  
  $duedate=strtotime($row_old['activity_duedate']);
  //$duedate=_time_user($duedate, -1); //i javascript to thele se utc, kai i vasi to exei se utc
  
  
  $row_out=array();
  $row_out['duedate']=$duedate;
  $row_out['type_id']=intval($row_old['activity_type_id']);
  $row_out['status']=trim_gks($row_old['activity_status']);
  $row_out['color']=trim_gks($row_old['activity_color']);
  $row_out['subject']=trim_gks($row_old['activity_subject']);
  $row_out['message']=trim_gks($row_old['activity_message']);
  $row_out['user_id']=intval($row_old['activity_user_id']);
  $row_out['user_nickname']=trim_gks($row_old['gks_nickname']);
  $row_out['notification']=intval($row_old['activity_notification']);
  $row_out['diarkeia']=15;
  
  if ($row_old['activity_type_id']==4 and $row_old['calendar_id']>0) { //meeting 
    $sql_diarkeia="SELECT calendar_start, calendar_end FROM gks_calendar WHERE id_calendar=".$row_old['calendar_id'];
    $result_diarkeia = $db_link->query($sql_diarkeia);        
    if (!$result_diarkeia) {
      debug_mail(false,'error sql',$sql_diarkeia);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    if ($result_diarkeia->num_rows==1) {
      $row_diarkeia = $result_diarkeia->fetch_assoc();
      $diarkeia=strtotime($row_diarkeia['calendar_end'])-strtotime($row_diarkeia['calendar_start']);
      $diarkeia=intval($diarkeia/60);
      //echo '<pre>'.$diarkeia;die();
      if ($diarkeia<=15) $diarkeia=15;
      else if ($diarkeia<=30) $diarkeia=30;
      else if ($diarkeia<=45) $diarkeia=45;
      else if ($diarkeia<=60) $diarkeia=60;
      else if ($diarkeia<=90) $diarkeia=90;
      else if ($diarkeia<=120) $diarkeia=120;
      else if ($diarkeia<=150) $diarkeia=150;
      else if ($diarkeia<=180) $diarkeia=180;
      else if ($diarkeia<=210) $diarkeia=210;
      else if ($diarkeia<=240) $diarkeia=240;
      else if ($diarkeia<=270) $diarkeia=270;
      else if ($diarkeia<=300) $diarkeia=300;
      else if ($diarkeia<=330) $diarkeia=330;
      else if ($diarkeia<=360) $diarkeia=360;
      else if ($diarkeia<=390) $diarkeia=390;
      else if ($diarkeia<=420) $diarkeia=420;
      else if ($diarkeia<=450) $diarkeia=450;
      else if ($diarkeia<=480) $diarkeia=480;

      $row_out['diarkeia']=$diarkeia;
      //echo '<pre>'.$diarkeia;die();
    }
  }
  $return = array('success' => true, 'message' => base64_encode('OK'), 'row_out' => $row_out);
  echo json_encode($return); die();  
}  




if ($status!='050new' and $status!='100done' and $status!='200cancel') {
  debug_mail(false,'emptyl','status is not ok');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η κατάσταση δεν είναι σωστή').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}


if ($user_id<=0) {debug_mail(false,'emptyl','user_id is not ok');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Ανάθεση σε')));
  echo json_encode($return); die();}

if ($type_id<=0) {debug_mail(false,'emptyl','type_id is not ok');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον τύπο')));
  echo json_encode($return); die();}

if ($duedate=='') {debug_mail(false,'emptyl','duedate is not ok');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την ημερομηνία λήξης')));
  echo json_encode($return); die();}

//if ($is_new_rec) {
//  if (strtotime($duedate) < time() - 24*60*60) {debug_mail(false,'emptyl','duedate is not ok');
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η ημερομηνία λήξης δεν μπορεί να είναι στο παρελθόν')));
//    echo json_encode($return); die();}
//}

//if ($subject=='') {debug_mail(false,'emptyl','subject is not ok');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Θέμα')));
//  echo json_encode($return); die();}


  
  
    
//$return = array('success' => false, 'message' => base64_encode('xa xa xa '.$duedate));
//echo json_encode($return); die();






$redirect='';
if ($id==-1) {
  $sql="insert into gks_crm_activity (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-crm-activity-item.php?id='.$id); 
  
 
}

$sql_activity_model='';
if ($page=='/my/admin-crm-calendar.php' or 
    $page=='/my/admin-crm-activity.php') {
  $sql_activity_model='';  
} else {
  $sql_activity_model="
  activity_model=".($model == '' ? 'null' : "'".$db_link->escape_string($model)."'") .",
  activity_model_id=".$model_id.",
  ";
}

$sql_activity_notification_send_at='';
if (isset($row_old) and is_array($row_old) and 
   isset($row_old['activity_duedate']) and
   $status=='050new' and 
   $duedate!='' and strtotime($duedate)>=time() and 
   $duedate!=$row_old['activity_duedate']) {
  $sql_activity_notification_send_at='activity_notification_send_at=null,';  
}


$sql="update gks_crm_activity set 
".$sql_activity_model."
activity_status='".$db_link->escape_string($status)."',
activity_user_id=".$user_id.",
activity_type_id=".$type_id.",
activity_duedate=".($duedate == '' ? 'null' : "'".$db_link->escape_string($duedate)."'") .", 
activity_notification=".$notification.",
activity_color=". ($color =='' ? 'null' : "'".$db_link->escape_string($color)."'").",
activity_subject='".$db_link->escape_string($subject)."',
activity_message='".$db_link->escape_string($message)."',
".$sql_activity_notification_send_at."

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_crm_activity = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }



$calendar_id      = ($is_new_rec ? 0 : intval($row_old['calendar_id']));
$activity_type_id = intval($type_id);
if ($activity_type_id==4) { //meeting
  
  $calendar_start=_time_user(strtotime($duedate),0);
  $calendar_end=$calendar_start + $diarkeia * 60;
  //echo '<pre>'.$diarkeia; die();
  
  if ($calendar_id>0) {
    $sql_calendar="update gks_calendar set 
    calendar_start='".date('Y-m-d H:i:s', $calendar_start)."',
    calendar_end='".date('Y-m-d H:i:s', $calendar_end)."',
    calendar_allday=0,
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_calendar=".$calendar_id;
    $result_calendar = $db_link->query($sql_calendar);        
    if (!$result_calendar) {
      debug_mail(false,'error sql',$sql_calendar);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}   
      
    gks_calendar_event_notification_recalc_rundate($calendar_id,false);
  } else {
    $sql_calendar="insert into gks_calendar (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    calendar_user_id,calendar_start,calendar_end,calendar_allday,
    calendar_title,calendar_message,
    calendar_is_exclusive,calendar_is_private,calendar_color
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$user_id.",
    '".date('Y-m-d H:i:s', $calendar_start)."',
    '".date('Y-m-d H:i:s', $calendar_end)."',
    0,
    '".$db_link->escape_string(($subject=='' ? gks_lang('Συνάντηση') : $subject))."',
    '".$db_link->escape_string($message)."',
    1,
    0,
    ". (($color =='') ? 'null' : "'".$db_link->escape_string($color)."'")."
    )";
    $result_calendar = $db_link->query($sql_calendar);        
    $calendar_id = $db_link->insert_id; 
    
    if (!$result_calendar) {
      debug_mail(false,'error sql',$sql_calendar);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}   
    
    $sql_calendar="insert into gks_calendar_notification (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    calendar_id,notification_type,notification_number,notification_unit,notification_rundate
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$calendar_id.",'email',30,'minute',
    '".date('Y-m-d H:i:s', $calendar_start + 30*60)."'
    ),(
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$calendar_id.",'notif',10,'minute',
    '".date('Y-m-d H:i:s', $calendar_start + 10*60)."'
    )";
    $result_calendar = $db_link->query($sql_calendar);        
    if (!$result_calendar) {
      debug_mail(false,'error sql',$sql_calendar);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}   

    
    gks_calendar_event_notification_recalc_rundate($calendar_id,true);
    
    $sql_calendar="update gks_crm_activity set 
    calendar_id=".$calendar_id."
    where id_crm_activity = ".$id." limit 1";
    $result_calendar = $db_link->query($sql_calendar);        
    if (!$result_calendar) {
      debug_mail(false,'error sql',$sql_calendar);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}   
  }
} else {
    $sql_calendar="update gks_crm_activity set 
    calendar_id=0
    where id_crm_activity = ".$id." limit 1";
    $result_calendar = $db_link->query($sql_calendar);        
    if (!$result_calendar) {
      debug_mail(false,'error sql',$sql_calendar);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}   
  
}



gks_calendar_event_update_dav_activity($id,false);

//$week_date_ranges=gks_week_date_ranges(false);

$sql_row ="SELECT gks_crm_activity.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_crm_activity_types.crm_activity_type_descr,gks_crm_activity_types.crm_activity_type_icon
FROM (((gks_crm_activity 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_activity.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_activity.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN gks_crm_activity_types ON gks_crm_activity.activity_type_id = gks_crm_activity_types.id_crm_activity_type) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_activity.activity_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
where gks_crm_activity.id_crm_activity=".$id." limit 1";
$result = $db_link->query($sql_row);        
if (!$result) {
  debug_mail(false,'error sql',$sql_row);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
$row_activity = $result->fetch_assoc();

$type_icon='';
if (!empty($row_activity['crm_activity_type_icon'])) {
  $type_icon=$row_activity['crm_activity_type_icon'];
  if (trim_gks($row_activity['activity_color'])!='') {
    $type_icon=str_replace(' class="', ' style="color:'.$row_activity['activity_color'].'" class="', $type_icon);
  }
  $type_icon.=' ';
}

$row_html=
'<tr class="activity_tr_new" data-id='.$row_activity['id_crm_activity'].'>'.
  '<th scope="row" nowrap class="mytdcm activity_aa">*</th>'.
  '<td nowrap class="mytdcm">'.
    '<i class="activity_edit enterrow fas fa-pen" data-id="'.$row_activity['id_crm_activity'].'" title="'.gks_lang('Επεξεργασία').'"></i>'.
    ' '.$row_activity['id_crm_activity'].
    ' <i class="fas fa-trash-alt deleterow" data-deleteafter="gks_fnc_activity_delete_after|'.$row_activity['id_crm_activity'].'" data-id="'.$row_activity['id_crm_activity'].'" data-model="gks_crm_activity"></i>'. 
  '</td>'.  
  '<td nowrap class="mytdcm"><span class="activity_status_'.$row_activity['activity_status'].'">'.getActivityStatusDescr($row_activity['activity_status']).'</span></td>'.
  '<td class="mytdcml">'.$row_activity['gks_nickname'].'</td>'.
  '<td class="mytdcml">';
  
  if ($row_activity['activity_type_id']==4 and $row_activity['calendar_id']>0) { //meeting
    $row_html.= $type_icon.'<a href="admin-crm-calendar.php?id='.$row_activity['calendar_id'].'">'.$row_activity['crm_activity_type_descr'].'</a>';
  } else {
    $row_html.= $type_icon.$row_activity['crm_activity_type_descr'];
  }
  
  $row_html.=
  '</td>'.
  '<td class="mytdcml">';
  
  //$row_html.= getActivityduedateDescr($row_activity['activity_duedate'],$row_activity['activity_status'],$week_date_ranges);
  
  if ($row_activity['activity_notification']==1) {
    $row_html.='<i class="activity_notification_bell fas fa-bell"></i> ';
  }
  
  $row_html.= secondsago(strtotime($row_activity['activity_duedate']));
  if ($row_activity['activity_type_id']==4) { //meeting
    if ($row_activity['calendar_id']>0) {
      $row_html.= '<br><a href="admin-crm-calendar.php?id='.$row_activity['calendar_id'].'">'.showDate(strtotime($row_activity['activity_duedate']),'H:i',1).'</a>';
    } else {
      $row_html.= '<br>'.showDate(strtotime($row_activity['activity_duedate']),'H:i',1);
    }
  }

$row_html.=
   '</td>'.
   '<td '; 
    if (trim_gks($row_activity['activity_color'])!='') {
      $row_html.= ' style="background-color:'.$row_activity['activity_color'].'"';  
    }
$row_html.=
  '>'.$row_activity['activity_subject'].'</td>'.
  '<td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">'.
     nl2br_gks($row_activity['activity_message']).
  '</div></div></td>'.
'</tr>';



 

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect, 'id'=>$id, 'row_html' => base64_encode($row_html),'is_new_rec' => $is_new_rec);
echo json_encode($return); die();







