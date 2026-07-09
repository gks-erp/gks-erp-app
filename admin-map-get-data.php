<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



$my_page_title=gks_lang('Χάρτης - Λήψη δεδομένων');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_map','view',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$mydaydif=0;
$mytimenow=time() + $mydaydif*24*60*60; // + 0*24*60*60;
$time_vardia=_time_user($mytimenow, 1);
$time_vardia-= GKS_ERP_START_VARDIA*60*60;
$today_vardia = date('Y-m-d',$time_vardia);
$today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
$today_vardia = _time_user($today_vardia, -1);
$today_vardia_time = $today_vardia;
$today_vardia = date('Y-m-d H:i:s', $today_vardia);

//echo '<pre>'.$today_vardia;die();

$mybounds_str=''; if (isset($_POST['mybounds_str'])) $mybounds_str = trim_gks(base64_decode($_POST['mybounds_str']));

$mybounds = json_decode($mybounds_str, true);
if ($mybounds === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['mybounds_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (1)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

/*Array
(
    [north] => 40.725015643264
    [south] => 40.720787470279
    [east] => 22.918836948329
    [west] => 22.903795120174
)*/

if (isset($mybounds['north'])==false or isset($mybounds['south'])==false or isset($mybounds['east'])==false or isset($mybounds['west'])==false) {
  debug_mail(false,'mybounds error',$_POST['mybounds_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (2)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}

$mybounds['north']=floatval($mybounds['north']);
$mybounds['south']=floatval($mybounds['south']);
$mybounds['east']=floatval($mybounds['east']);
$mybounds['west']=floatval($mybounds['west']);

$enable_users=false;if (isset($_POST['users'])) $enable_users=intval($_POST['users'])==1;
$enable_appmobile=false;if (isset($_POST['appmobile'])) $enable_appmobile=intval($_POST['appmobile'])==1;
$enable_lead=false;if (isset($_POST['lead'])) $enable_lead=intval($_POST['lead'])==1;
$enable_calendar=false;if (isset($_POST['calendar'])) $enable_calendar=intval($_POST['calendar'])==1;
$enable_task=false;if (isset($_POST['task'])) $enable_task=intval($_POST['task'])==1;
$enable_machine=false;if (isset($_POST['machine'])) $enable_machine=intval($_POST['machine'])==1;
$enable_poi=false;if (isset($_POST['poi'])) $enable_poi=intval($_POST['poi'])==1;
$only_mobile=false;if (isset($_POST['only_mobile'])) $only_mobile=intval($_POST['only_mobile'])==1;
if ($only_mobile==1) {
  $enable_users=false;
  $enable_lead=false;
  $enable_calendar=false;
  $enable_task=false;
  $enable_machine=false;
  $enable_poi=false;
}

$last_id_gps_str=''; if (isset($_POST['last_id_gps_str'])) $last_id_gps_str = trim_gks(base64_decode($_POST['last_id_gps_str']));
$last_id_gps = json_decode($last_id_gps_str, true);
if ($last_id_gps === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['last_id_gps_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (3)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
//print '<pre>';print_r($last_id_gps_str);die();  
$last_id_gps_fix=[];
foreach ($last_id_gps as $value) {
  $last_id_gps_fix[intval($value['i'])]=intval($value['m']);
} 
//print '<pre>';print_r($last_id_gps_fix);die();



//echo'<pre>|'.$enable_users.'|';die();

$labels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

$data_users=[];
$data_appmobile=[];
$data_calendar=[];
$data_lead=[];
$data_task=[];
$data_machine=[];
$data_poi=[];

if ($enable_users) {
  $sql="SELECT gks_users.user_id, gks_nickname, 
  gks_users.ma_latitude, gks_users.ma_longitude
  FROM gks_users 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  where (not (gks_users.ma_latitude=0 and gks_users.ma_longitude=0))
  and ma_latitude<=".$mybounds['north']."
  and ma_latitude>=".$mybounds['south']."
  and ma_longitude>=".$mybounds['west']."
  and ma_longitude<=".$mybounds['east']."
  and ".GKS_WP_TABLE_PREFIX."users.ID is not null
  and ".GKS_WP_TABLE_PREFIX."users.gks_nickname <> ''
  order by gks_nickname";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  $myrows=[];
  while ($row = $result->fetch_assoc()) {
    $myrows[]=$row;
  }
  
  foreach ($myrows as $row) {
    $data_users[]=array(
      'obj' => 'users',
      'id' => intval($row['user_id']),
      'descr' => trim_gks($row['gks_nickname']),
      'point'=> array(
        'lat' => floatval($row['ma_latitude']),
        'lng' => floatval($row['ma_longitude']),
      ), 
    );
  }
}

if ($enable_appmobile) {
  $data_appmobile=[];
  $sql="SELECT id_erp_app_mobile, erp_app_mobile_name
  FROM gks_erp_app_mobile
  where erp_app_mobile_disabled=0
  order by erp_app_mobile_sortorder,erp_app_mobile_name,id_erp_app_mobile";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  while ($row = $result->fetch_assoc()) {
    $row['id_erp_app_mobile']=
    $data_appmobile[intval($row['id_erp_app_mobile'])]=array(
      'obj' => 'appmobile',
      'id' => intval($row['id_erp_app_mobile']),
      'descr' => trim_gks($row['erp_app_mobile_name']),
      'point'=> array(
        'lat' => 0,
        'lng' => 0,
      ),
      'paths' => [],
      'paths_start' => [],
      'paths_end' => [],
      'max_id_gps' => 0,
      'last_dia' => '',
    );
  }
  
  $sql="SELECT id_gps, erp_app_mobile_id, mylat, mylng
  FROM gks_gps
  WHERE gks_gps.id_gps In (
    SELECT Max(id_gps) AS maxid
    FROM gks_gps
    GROUP BY erp_app_mobile_id
  )";  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  while ($row = $result->fetch_assoc()) {
    if (isset($data_appmobile[intval($row['erp_app_mobile_id'])])) {
      $data_appmobile[intval($row['erp_app_mobile_id'])]['point']['lat']=floatval($row['mylat']);
      $data_appmobile[intval($row['erp_app_mobile_id'])]['point']['lng']=floatval($row['mylng']);
    }
  }
  
  foreach ($data_appmobile as &$myapp) {
    $from_id_gps=0;
    if (isset($last_id_gps_fix[$myapp['id']])) $from_id_gps=$last_id_gps_fix[$myapp['id']];
    //echo '<pre>';print_r($last_id_gps_fix);die();
    //echo '<pre>'.$last_id_gps_fix[$myapp['id']];die();
    
    $first_mydiadromi='';
    if ($from_id_gps==0) {
      $sql="select id_gps, mydiadromi
      from gks_gps
      where erp_app_mobile_id=".$myapp['id']."
      and mytime >='".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'
      order by id_gps asc
      limit 1";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      if ($result->num_rows==1) {	
        $row = $result->fetch_assoc();
        if (!empty($row['mydiadromi'])) $first_mydiadromi= $row['mydiadromi'];
        if ($first_mydiadromi!='') {
          $sql="select id_gps
          from gks_gps
          where erp_app_mobile_id=".$myapp['id']."
          and mydiadromi='".$db_link->escape_string($first_mydiadromi)."'
          order by id_gps asc
          limit 1";
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }
          if ($result->num_rows==1) {	
            $row = $result->fetch_assoc();  
            $from_id_gps=intval($row['id_gps']) - 1;//giati pio kato exo > anti tou >=
          }
        }
      }
    }
    
    $sql="select id_gps,mylat,mylng,mytime,mydiadromi
    from gks_gps
    where erp_app_mobile_id=".$myapp['id'];
    if ($from_id_gps>0) {
      $sql.=" and id_gps > ".$from_id_gps;
    } else {
      $sql.=" and mytime >='".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'";
    }
    $sql.=" order by mytime asc,id_gps asc";
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
      
    while ($row = $result->fetch_assoc()) {
      $id_gps=intval($row['id_gps']);
      $mylat=floatval($row['mylat']);
      $mylng=floatval($row['mylng']);
      $mydiadromi=trim_gks($row['mydiadromi']);
      if ($mydiadromi=='') $mydiadromi='empty';
      
      if ($id_gps>$myapp['max_id_gps']) {
        $myapp['max_id_gps']=$id_gps;
        $myapp['last_dia']=$mydiadromi;
      }
      
      if (isset($myapp['paths'][$mydiadromi])==false) {
        $myapp['paths'][$mydiadromi]=[];
      }
      
      
      $myapp['paths'][$mydiadromi][]=array(
        'lat'=>$mylat,
        'lng'=>$mylng,
      );
      
      
      if (isset($myapp['paths_start'][$mydiadromi])==false) {
        $myapp['paths_start'][$mydiadromi]=showDate(strtotime($row['mytime']),'Y-m-d-H-i-s',1);
      }
      
      $myapp['paths_end'][$mydiadromi]=showDate(strtotime($row['mytime']),'Y-m-d-H-i-s',1);
      
    }
  }
  unset($myapp);
  
}



if ($enable_calendar) {
  $colors=[];
  
  $def_color='#3788d8';
  $calendar_user_color=$def_color;
  if (isset($gks_user_settings['calendar']['user_color'])) $calendar_user_color=$gks_user_settings['calendar']['user_color'];
  $colors[$my_wp_user_id]=$calendar_user_color;
  
  
  $sql="SELECT other_user_id, other_user_color
  FROM gks_calendar_other_users
  WHERE other_myobj='cal' AND this_user_id=".$my_wp_user_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  while ($row = $result->fetch_assoc()) {
    $colors[$row['other_user_id']]=$row['other_user_color'];
  }
  
  $sql="SELECT id_calendar, calendar_title, calendar_color,
  calendar_map_latitude, calendar_map_longitude, 
  calendar_user_id
  FROM gks_calendar
  where (not (calendar_map_latitude=0 and calendar_map_longitude=0))
  and calendar_map_latitude<=".$mybounds['north']."
  and calendar_map_latitude>=".$mybounds['south']."
  and calendar_map_longitude>=".$mybounds['west']."
  and calendar_map_longitude<=".$mybounds['east']."
  order by id_calendar";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  $myrows=[];
  while ($row = $result->fetch_assoc()) {
    $myrows[]=$row;
  }
  
  foreach ($myrows as $row) {
    $color=$def_color;
    if (isset($colors[$row['calendar_user_id']])) {
      $color=$colors[$row['calendar_user_id']];
    }
    if (!empty($row['calendar_color'])) $color=$row['calendar_color'];
    
    $data_calendar[]=array(
      'obj' => 'calendar',
      'id' => intval($row['id_calendar']),
      'descr' => trim_gks($row['calendar_title']),
      'color' => $color,
      'point'=> array(
        'lat' => floatval($row['calendar_map_latitude']),
        'lng' => floatval($row['calendar_map_longitude']),
      ), 
    );
  }
}

if ($enable_lead) {
  
  $sql="SELECT id_crm_lead_status, lead_status_descr, lead_status_color
  FROM gks_crm_leads_status";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  $mystatus=[];
  while ($row = $result->fetch_assoc()) {
    $mystatus[$row['id_crm_lead_status']]=array(
      'status'=>$row['lead_status_descr'],
      'color'=>$row['lead_status_color'],
    );
  }  
  
  $sql="SELECT id_crm_lead, subject,  lead_status_id, lead_color,
  first_name, last_name, map_latitude, map_longitude
  FROM gks_crm_leads
  where (not (map_latitude=0 and map_longitude=0))
  and map_latitude<=".$mybounds['north']."
  and map_latitude>=".$mybounds['south']."
  and map_longitude>=".$mybounds['west']."
  and map_longitude<=".$mybounds['east']."
  order by first_name,last_name,subject";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  $myrows=[];
  while ($row = $result->fetch_assoc()) {
    $myrows[]=$row;
  }
  
  foreach ($myrows as $row) {
    $color='#000000';
    $status='--';
    if (isset($mystatus[$row['lead_status_id']])) {
      $color=$mystatus[$row['lead_status_id']]['color'];
      $status=$mystatus[$row['lead_status_id']]['status'];
    }
    if (!empty($row['lead_color'])) $color=$row['lead_color'];
    
    $data_lead[]=array(
      'obj' => 'lead',
      'id' => intval($row['id_crm_lead']),
      'descr' => trim(trim_gks($row['first_name']).' '.trim_gks($row['last_name']).' '.trim_gks($row['subject'])),
      'color' => $color,
      'status' => $status,
      'point'=> array(
        'lat' => floatval($row['map_latitude']),
        'lng' => floatval($row['map_longitude']),
      ), 
    );
  }
}

if ($enable_task) {
  
  $sql="SELECT id_crm_task_status, task_status_descr, task_status_color
  FROM gks_crm_tasks_status";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  $mystatus=[];
  while ($row = $result->fetch_assoc()) {
    $mystatus[$row['id_crm_task_status']]=array(
      'status'=>$row['task_status_descr'],
      'color'=>$row['task_status_color'],
    );
  }  
  
  $sql="SELECT id_crm_task, subject, task_status_id, task_color,
  first_name, last_name, map_latitude, map_longitude
  FROM gks_crm_tasks
  where (not (map_latitude=0 and map_longitude=0))
  and map_latitude<=".$mybounds['north']."
  and map_latitude>=".$mybounds['south']."
  and map_longitude>=".$mybounds['west']."
  and map_longitude<=".$mybounds['east']."
  order by first_name,last_name,subject";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  $myrows=[];
  while ($row = $result->fetch_assoc()) {
    $myrows[]=$row;
  }
  
  foreach ($myrows as $row) {
    $color='#000000';
    $status='--';
    if (isset($mystatus[$row['task_status_id']])) {
      $color=$mystatus[$row['task_status_id']]['color'];
      $status=$mystatus[$row['task_status_id']]['status'];
    }
    if (!empty($row['task_color'])) $color=$row['task_color'];
    
    $data_task[]=array(
      'obj' => 'task',
      'id' => intval($row['id_crm_task']),
      'descr' => trim(trim_gks($row['first_name']).' '.trim_gks($row['last_name']).' '.trim_gks($row['subject'])),
      'color' => $color,
      'status' => $status,
      'point'=> array(
        'lat' => floatval($row['map_latitude']),
        'lng' => floatval($row['map_longitude']),
      ), 
    );
  }
}


if ($enable_machine) {
  

  
  $sql="SELECT gks_crm_machine.id_crm_machine, gks_crm_machine.crm_machine_name, 
  gks_crm_machine.crm_machine_user_id, gks_crm_machine.users_extra_address_id, 
  gks_users.ma_latitude, gks_users.ma_longitude, 
  gks_users_extra_address.ea_latitude, gks_users_extra_address.ea_longitude
  FROM (gks_crm_machine 
  LEFT JOIN gks_users ON gks_crm_machine.crm_machine_user_id = gks_users.user_id) 
  LEFT JOIN gks_users_extra_address ON gks_crm_machine.users_extra_address_id = gks_users_extra_address.id_users_extra_address
  where (
  (not (ma_latitude=0 and ma_longitude=0))
  and ma_latitude<=".$mybounds['north']."
  and ma_latitude>=".$mybounds['south']."
  and ma_longitude>=".$mybounds['west']."
  and ma_longitude<=".$mybounds['east']."
  and users_extra_address_id=-1
  ) or (
  (not (ea_latitude=0 and ea_longitude=0))
  and ea_latitude<=".$mybounds['north']."
  and ea_latitude>=".$mybounds['south']."
  and ea_longitude>=".$mybounds['west']."
  and ea_longitude<=".$mybounds['east']."
  and users_extra_address_id>0
  )
  order by crm_machine_name";
  //echo '<pre>'.$sql;die();
  
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }

  $myrows=[];
  while ($row = $result->fetch_assoc()) {
    $myrows[]=$row;
  }
  
  foreach ($myrows as $row) {
    if ($row['users_extra_address_id']==-1) {
      $lat=floatval($row['ma_latitude']);
      $lng=floatval($row['ma_longitude']);
    } else {
      $lat=floatval($row['ea_latitude']);
      $lng=floatval($row['ea_longitude']);
    }
    
    $data_machine[]=array(
      'obj' => 'machine',
      'id' => intval($row['id_crm_machine']),
      'descr' => trim_gks($row['crm_machine_name']),
      'point'=> array(
        'lat' => $lat,
        'lng' => $lng,
      ), 
    );
  }
}

if ($enable_poi) {
  $sql="SELECT id_poi_type, poi_type_html_icon,poi_type_descr
  FROM gks_poi_type
  WHERE poi_type_html_icon<>'' AND poi_type_descr<>'' AND poi_type_disable=0
  order by id_poi_type";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  $poi_type_class=[];
  $poi_type_icon=[];
  $poi_type_descr=[];
  while ($row = $result->fetch_assoc()) {
    $a=trim_gks($row['poi_type_html_icon']);
    $poi_type_icon[$row['id_poi_type']]=$a;
    $poi_type_descr[$row['id_poi_type']]=$row['poi_type_descr'];
    
    $pos1=strpos($a,'class="');
    if ($pos1!==false) {
      $pos2=strpos($a,'"',$pos1+8);
      if ($pos2!==false) {
        $poi_type_class[$row['id_poi_type']]=substr($a,$pos1+7,$pos2-$pos1-7);
      }
    }
    if (isset($poi_type_class[$row['id_poi_type']])==false) {
      $pos1=strpos($a,"class='");
      if ($pos1!==false) {
        $pos2=strpos($a,"'",$pos1+8);
        if ($pos2!==false) {
          $poi_type_class[$row['id_poi_type']]=substr($a,$pos1+7,$pos2-$pos1-7);
        }
      }
    }
  }
  //echo '<pre>';print_r($poi_type_class);die();

  $filter_gks='';
  if (isset($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'],'?f=1')!==false) {
      $filter_gks=' and (poi_type_id<>2 or (poi_type_id=2 and poi_country_id=91)) ';
    }
    if (strpos($_SERVER['HTTP_REFERER'],'?f=2')!==false) {
      $filter_gks=' and (poi_type_id<>2 or (poi_type_id=2 and poi_country_id=91)) and poi_type_id<>101 ';
    }
  }
  
  $sql="SELECT id_poi, poi_type_id, poi_descr, poi_map_latitude, poi_map_longitude,poi_areas
  FROM gks_poi
  WHERE poi_disable=0
  and (not (poi_map_latitude=0 and poi_map_longitude=0))
  ".$filter_gks."
  and poi_map_latitude<=".$mybounds['north']."
  and poi_map_latitude>=".$mybounds['south']."
  and poi_map_longitude>=".$mybounds['west']."
  and poi_map_longitude<=".$mybounds['east']."
  
  order by poi_descr";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  
  
  
  $myrows=[];
  while ($row = $result->fetch_assoc()) {
    $myrows[]=$row;
  }
  
  foreach ($myrows as $row) {
    $row['id_poi']=intval($row['id_poi']);
    $row['poi_type_id']=intval($row['poi_type_id']);
    
    $mclass='';$lbl='';
    if (isset($poi_type_class[$row['poi_type_id']])) {
      $mclass=$poi_type_class[$row['poi_type_id']];
    } else {
      $lbl=greeklish(trim_gks($row['poi_descr']));
      if (strlen($lbl)==0) {
        $lbl='#';
      } else {
        $lbl=strtoupper(substr($lbl,0,1));
        if (strpos($labels, $lbl)===false) $lbl='#';
      }
    }
    //$lbl.='ssssssss';
    $icon='';
    if (isset($poi_type_icon[$row['poi_type_id']])) $icon=$poi_type_icon[$row['poi_type_id']]; 
    
    $areas=[];
    $areas['circles']=[];
    $areas['rectangles']=[];
    $areas['polygons']=[];
  
    if (trim_gks($row['poi_areas'])!='') {
      $temp=unserialize(trim_gks($row['poi_areas'])); 
      if (is_array($temp) and isset($areas['circles']) and isset($areas['rectangles']) and isset($areas['polygons'])) {
        $areas=$temp;
      }
    }
  
    $pr_list=[];
    if (GKS_TRANSFER) {
      $sqlpr="
      SELECT gks_poi.poi_descr, gks_poi.poi_locode, gks_poi.poi_iata_code, gks_poi.poi_icao_code, Count(gks_transfer_pricelist.id_transfer_pricelist) AS cc
      FROM (gks_transfer_pricelist 
      LEFT JOIN gks_poi ON gks_transfer_pricelist.poi_id_to = gks_poi.id_poi) 
      LEFT JOIN gks_transfer_oxima_type ON gks_transfer_pricelist.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type
      WHERE (((gks_transfer_pricelist.poi_id_from)=".$row['id_poi'].")
      AND ((gks_poi.poi_type_id) In (2,3,4)) 
      AND ((gks_transfer_pricelist.transfer_pricelist_disable)=0) 
      AND ((gks_transfer_oxima_type.transfer_oxima_type_disable)=0))
      GROUP BY gks_poi.poi_descr, gks_poi.poi_locode, gks_poi.poi_iata_code, gks_poi.poi_icao_code
      union
      SELECT gks_poi.poi_descr, gks_poi.poi_locode, gks_poi.poi_iata_code, gks_poi.poi_icao_code, Count(gks_transfer_pricelist.id_transfer_pricelist) AS cc
      FROM (gks_transfer_pricelist 
      LEFT JOIN gks_poi ON gks_transfer_pricelist.poi_id_from = gks_poi.id_poi) 
      LEFT JOIN gks_transfer_oxima_type ON gks_transfer_pricelist.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type
      WHERE (((gks_transfer_pricelist.poi_id_to)=".$row['id_poi'].")
      AND ((gks_poi.poi_type_id) In (2,3,4)) 
      AND ((gks_transfer_pricelist.transfer_pricelist_disable)=0) 
      AND ((gks_transfer_oxima_type.transfer_oxima_type_disable)=0))
      GROUP BY gks_poi.poi_descr, gks_poi.poi_locode, gks_poi.poi_iata_code, gks_poi.poi_icao_code
      order by cc desc,poi_descr";
    
      $resultpr = $db_link->query($sqlpr);        
      if (!$resultpr) {
        debug_mail(false,'error sql',$sqlpr);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      
      while ($rowpr = $resultpr->fetch_assoc()) {
        $pr_name=trim_gks($rowpr['poi_descr']);
        $pr_list[]=array(
          'n' => $pr_name,
          'c' => intval($rowpr['cc']),
        );
        
        
      }
    }
      
    $type_descr='';if (isset($poi_type_descr[$row['poi_type_id']])) $type_descr=$poi_type_descr[$row['poi_type_id']];
    
    $data_poi[]=array(
      'obj' => 'poi',
      'id' => $row['id_poi'],
      'tid' => $row['poi_type_id'],
      'descr' => trim_gks($row['poi_descr']),
      'type_descr' => $type_descr,
      'icon' => $icon, 
      'lbl' => $lbl,
      'mclass' => $mclass,
      'point'=> array(
        'lat' => floatval($row['poi_map_latitude']),
        'lng' => floatval($row['poi_map_longitude']),
      ),
      'areas' => $areas,
      'pr' => $pr_list,
    );
  }
}
    

$return = array('success' => true, 
  'message' => base64_encode('OK'),
  'data_users'=>$data_users,
  'data_appmobile'=>$data_appmobile,
  'data_calendar'=>$data_calendar,
  'data_lead'=>$data_lead,
  'data_task'=>$data_task,
  'data_machine'=>$data_machine,
  'data_poi'=>$data_poi,
  
);
echo json_encode($return); die();  

