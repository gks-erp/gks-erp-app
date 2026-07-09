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
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση δικαιωμάτων χρήστη').' id:' . $id;
db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {$return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχετε δικαιώματα για αυτήν την ενέργεια')));echo json_encode($return); die();}



$mydata_str = trim_gks(base64_decode($_POST['mydata']));
$mydata_array = json_decode($mydata_str, true);
if ($mydata_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['mydata']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'. '.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
  

//print '<pre>';print_r($mydata_array);die();

$mydata=array();
foreach ($mydata_array as $arrayval) {
  $oid=intval($arrayval['oid']);
  if ($oid>0) {
    $view=0;if (isset($arrayval['view'])) $view=(intval($arrayval['view'])==1 ? 1 : 0);
    $edit=0;if (isset($arrayval['edit'])) $edit=(intval($arrayval['edit'])==1 ? 1 : 0);
    $add=0;if (isset($arrayval['add'])) $add=(intval($arrayval['add'])==1 ? 1 : 0);
    $delete=0;if (isset($arrayval['delete'])) $delete=(intval($arrayval['delete'])==1 ? 1 : 0);
    $autocomplete=0;if (isset($arrayval['autocomplete'])) $autocomplete=(intval($arrayval['autocomplete'])==1 ? 1 : 0);
    $perm_condition01=''; if (isset($arrayval['perm_condition01'])) $perm_condition01=trim_gks($arrayval['perm_condition01']);
    $perm_condition02=''; if (isset($arrayval['perm_condition02'])) $perm_condition02=trim_gks($arrayval['perm_condition02']);
    
    if ($perm_condition01!='') {
      switch ($oid) {
        case 310: //gks_company
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_company as myid from gks_company where id_company in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;
        case 320: //gks_company_subs
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>=0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_company_sub as myid from gks_company_subs where id_company_sub in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array(); if (in_array(0,$parts_c)) $rdata[]=0;
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=intval($rowtag['myid']);  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;
        case 340: //gks_acc_journal
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_acc_journal as myid from gks_acc_journal where id_acc_journal in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;
        case 350: //gks_acc_seires
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_acc_seira as myid from gks_acc_seires where id_acc_seira in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;
        case 360: //gks_warehouses
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_warehouse as myid from gks_warehouses where id_warehouse in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;
        case 370: //gks_print_forms
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_print_form as myid from gks_print_forms where id_print_form in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;
        case 380: //gks_eshops
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_eshop as myid from gks_eshops where id_eshop in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;
        case 1200: //gks_hotel
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_hotel as myid from gks_hotel where id_hotel in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;

        case 2200: //gks_transfer
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_transfer as myid from gks_transfer where id_transfer in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;

        case 2180: //gks_transfer_area
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_transfer_area as myid from gks_transfer_area where id_transfer_area in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;



        case 683: //gks_pos
        case 684: //gks_pos_run
          $parts=explode(']][[',$perm_condition01);
          $parts_c=array();
          foreach ($parts as $value) {$value=trim_gks($value);if ($value!='') {$pp=explode('#',$value);if (count($pp)==2) {$value=$pp[1];$value=str_replace(')', '', $value);$value=intval($value);if ($value>0) $parts_c[]=$value;}}}   
          if (count($parts_c)>0) {
            $sqltags="select id_pos as myid from gks_pos where id_pos in (".implode(',',$parts_c).")";
            $resulttags = $db_link->query($sqltags);        
            if (!$resulttags) {debug_mail(false,'error sql',$sqltags);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
            $rdata=array();
            while ($rowtag = $resulttags->fetch_assoc()) $rdata[]= $rowtag['myid'];  
            $perm_condition01=serialize($rdata);
            //echo $perm_condition01;die();
          }
          break;
        default:      
      }
    }
    
    if ($perm_condition02!='') {
      switch ($oid) {
        case 684: //gks_pos_run

          break;
        default:      
      }
    }
        
    $perm_int_cond01=0; if (isset($arrayval['perm_int_cond01'])) $perm_int_cond01=intval($arrayval['perm_int_cond01']);
    if ($perm_int_cond01!=1) $perm_int_cond01=0;
    
    $perm_int_cond02=0; if (isset($arrayval['perm_int_cond02'])) $perm_int_cond02=intval($arrayval['perm_int_cond02']);

    
 
    
    $mydata[]=array(
      'oid'=> $oid,
      'view'=> $view,
      'edit'=> $edit,
      'add'=> $add,
      'delete'=> $delete,
      'autocomplete'=> $autocomplete,
      'condition01' => $perm_condition01,
      'condition02' => $perm_condition02,
      'int_cond01' => $perm_int_cond01,
      'int_cond02' => $perm_int_cond02,
    );
  }
} 

//print '<pre>aaaaaaaaaaaa ';print_r($mydata);die();


$sql="INSERT INTO gks_permission_user (permission_object_id, user_id, 
mydate_add,mydate_edit,user_id_add,user_id_edit,myip)
SELECT gks_permission_object.id_permission_object, ".$id." AS user_id, 
now() AS mydate_add,now() AS mydate_edit,".$my_wp_user_id." as user_id_add,".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip
FROM gks_permission_object LEFT JOIN (
  SELECT gks_permission_user.permission_object_id
  FROM gks_permission_user
  WHERE gks_permission_user.user_id=".$id."
) AS existoid ON gks_permission_object.id_permission_object = existoid.permission_object_id
WHERE existoid.permission_object_id Is Null";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}  

//die();


foreach ($mydata as $value) {
  $sql="update gks_permission_user set 
  perm_view=".$value['view'].",
  perm_edit=".$value['edit'].",
  perm_add=".$value['add'].",
  perm_delete=".$value['delete'].",
  perm_autocomplete=".$value['autocomplete'].",
  perm_condition01='".$db_link->escape_string($value['condition01'])."',
  perm_condition02='".$db_link->escape_string($value['condition02'])."',
  perm_int_cond01=".$value['int_cond01'].",
  perm_int_cond02=".$value['int_cond02'].",
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  where user_id=".$id." and permission_object_id=".$value['oid'];
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }  
}


$sql="DELETE gks_permission_user.*
FROM gks_permission_user LEFT JOIN gks_permission_object ON gks_permission_user.permission_object_id = gks_permission_object.id_permission_object
WHERE gks_permission_object.id_permission_object Is Null";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 


$notif_str = trim_gks(base64_decode($_POST['notif']));
$notif_array = json_decode($notif_str, true);
if ($notif_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error notif',$_POST['notif']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (2). '.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
  
//print '<pre>';print_r($notif_array);die();

$notif=array();
foreach ($notif_array as $value) {
  $nid=intval($value['nid']);
  if ($nid>0) {
    $admin=0;if (isset($value['admin'])) $admin=(intval($value['admin'])==1 ? 1 : 0);
    $user=0;if (isset($value['user'])) $user=(intval($value['user'])==1 ? 1 : 0);
    $email=0;if (isset($value['email'])) $email=(intval($value['email'])==1 ? 1 : 0);
    $viber=0;if (isset($value['viber'])) $viber=(intval($value['viber'])==1 ? 1 : 0);
    $notif[$nid]=array(
      'nid'=> $nid,
      'admin'=> $admin,
      'user'=> $user,
      'email'=> $email,
      'viber'=> $viber,
    );
  }
}
//print '<pre>';print_r($notif);die();

$sql_notif_type="SELECT id_notification_type FROM gks_notification_type WHERE notification_type_disabled=0";
if ($GKS_HOTEL_BACKEND==false and GKS_TRANSFER==false) $sql_notif_type.=" and id_notification_type not in (1010)";
if ($GKS_ORDERS_PRODUCTION==false) $sql_notif_type.=" and id_notification_type not in (510)";
if ($GKS_CRM_ENABLE==false) $sql_notif_type.=" and id_notification_type not in (50)";
$sql_notif_type.=" ORDER BY notification_type_sortorder;";
$result_notif_type = $db_link->query($sql_notif_type);
if (!$result_notif_type) {debug_mail(false,'error sql',$sql_notif_type);  die('sql error');}
$notifs_array=array();
while ($row_notif_type = $result_notif_type->fetch_assoc()) {
  $row_notif_type['admin']=0;
  $row_notif_type['user']=0;
  $row_notif_type['email']=0;
  $row_notif_type['viber']=0;
  if (isset($notif[$row_notif_type['id_notification_type']])) {
    $row_notif_type['admin']=$notif[$row_notif_type['id_notification_type']]['admin'];
    $row_notif_type['user']=$notif[$row_notif_type['id_notification_type']]['user'];
    $row_notif_type['email']=$notif[$row_notif_type['id_notification_type']]['email'];
    $row_notif_type['viber']=$notif[$row_notif_type['id_notification_type']]['viber'];
  }
  if ($row_notif_type['admin']==0) {
    $row_notif_type['user']=0;
    $row_notif_type['email']=0;
    $row_notif_type['viber']=0;
  }
  $notifs_array[$row_notif_type['id_notification_type']]=$row_notif_type;
}

//print '<pre>';print_r($notifs_array);die();

foreach ($notifs_array as $value) {
  $sql="select id_notification_userperm from gks_notification_userperm where notification_type_id=".$value['id_notification_type']." and user_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
    
  if ($result->num_rows==0) {
    $sql="insert into gks_notification_userperm (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    user_id, notification_type_id,from_admin,from_user,to_email,to_viber
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$id.",".$value['id_notification_type'].",".$value['admin'].",".$value['user'].",".$value['email'].",".$value['viber']."
    )";
    
  } else {
    $row = $result->fetch_assoc();
    $id_notification_userperm=$row['id_notification_userperm'];
    $sql="update gks_notification_userperm set
    from_admin=".$value['admin'].",
    from_user=".$value['user'].",
    to_email=".$value['email'].",
    to_viber=".$value['viber'].",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_notification_userperm=".$id_notification_userperm;
  }
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
    
  
} 

gks_cache_update_menu_version($id);



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> '');
echo json_encode($return); die();
