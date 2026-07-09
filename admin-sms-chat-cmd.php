<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$return = array('success' => false, 'message' => base64_encode('general error'));
$cmd='';if (isset($_POST['cmd'])) $cmd=trim_gks(base64_decode($_POST['cmd']));

$my_page_title=gks_lang('Συζήτηση SMS - Εντολή').' '.$cmd;
db_open();
//stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sms_chat','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

switch ($cmd) {
  case 'get_chats':

    $sql_app="select * from gks_erp_app_mobile where erp_app_mobile_disabled=0 and erp_app_mobile_can_sms=1 order by erp_app_mobile_sortorder";
    $result_app = $db_link->query($sql_app);        
    if (!$result_app) {debug_mail(false,'error sql',$sql_app);$return['message']=base64_encode('sql error');echo json_encode($return); die();}
    $myapps=[];$usenders=[];
    while ($row_app = $result_app->fetch_assoc()) {
      $row_app['sprovider']='gks_erp_app_mobile';
      $myapps[]=$row_app;
      $u_number=$row_app['erp_app_mobile_country'].$row_app['erp_app_mobile_phonenumber'];
      if (in_array($u_number, $usenders)==false)
        $usenders[]=$u_number;
    }
    //print '<pre>'.$GKS_SMS_SENDER;die();
    $parts=explode(',',$GKS_SMS_SENDER);
    foreach ($parts as $value) {
      $value=trim_gks($value);
      if ($value!='') {
        $row_app=array(
          'sprovider'=>'smsapi',
          'erp_app_mobile_phonenumber' => $value
        );
        $myapps[]=$row_app;
        if (in_array($row_app['erp_app_mobile_phonenumber'], $usenders)==false)
          $usenders[]=(ctype_digit($row_app['erp_app_mobile_phonenumber']) ? '+' : '').$row_app['erp_app_mobile_phonenumber'];
        
      }
    }
    
                  
    //print '<pre>';print_r($myapps);print_r($usenders);die();
    //print '<pre>';print_r($usenders);die();
    
    
    $sql_chat="SELECT myfrom, myto, Max(date_add) AS maxdate, max(display_name) as dmaxname
    FROM gks_sms
    GROUP BY myfrom, myto
    order by maxdate desc, myfrom, myto";
    $result_chat = $db_link->query($sql_chat);        
    if (!$result_chat) {debug_mail(false,'error sql',$sql_chat);$return['message']=base64_encode('sql error');echo json_encode($return); die();}
    $mychats=[];
    while ($row_chat = $result_chat->fetch_assoc()) {
      
      if (in_array($row_chat['myfrom'],$usenders)) { //einai diko mas, ejerxomeno
        if (isset($mychats[$row_chat['myto']])==false) {
          $mychats[$row_chat['myto']]=array(
            'other'=>$row_chat['myto'],
            'maxdate'=> $row_chat['maxdate'],
            'type' => 'sent',
            'dmaxname' => $row_chat['dmaxname'],
          );
        }
      } else { //einai dikou toy, eiserxomeno
        if (isset($mychats[$row_chat['myfrom']])==false) {
          $mychats[$row_chat['myfrom']]=array(
            'other'=>$row_chat['myto'],
            'maxdate'=> $row_chat['maxdate'],
            'type' => 'inbox',
            'dmaxname' => $row_chat['dmaxname'],
          );
        }
      }
    }
    
    
    
    $sql_users="SELECT gks_users_communication.phone_fix, gks_users_communication.user_id, 
    gks_nickname, gks_wsl_current_user_image
    FROM gks_users_communication 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_communication.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE gks_users_communication.phone_fix<>''
    AND gks_users_communication.comm_type='phone'";
    $result_users = $db_link->query($sql_users);        
    if (!$result_users) {debug_mail(false,'error sql',$sql_users);$return['message']=base64_encode('sql error');echo json_encode($return); die();}

    //echo '<pre>'.$sql_users;die();
    
    $myusers=[];
    while ($row_users = $result_users->fetch_assoc()) {
      $myusers[$row_users['phone_fix']]=$row_users;
    }
    
    
    
    $html='';
    
    foreach ($mychats as $myp => $myi) {
      if (in_array($myp,$usenders)==false) {
        $user_id=0;$name='';$img='<img src="/my/img/avatar.png" border="0" style="max-width:64px;max-height:64px;">'; 
        //echo '<pre>'.$myp; print_r($myusers[$myp]); die();
        
        
        $mypf=$myp;
        //if (strlen($mypf)==12 and substr($mypf,0,4)=='3069') {
        //  $mypf=substr($mypf,2);
        //}
        
        if (isset($myusers[$mypf])) {
          $user_id=$myusers[$mypf]['user_id'];
          $name=$myusers[$mypf]['gks_nickname'];
          //$img=$myusers[$mypf]['gks_wsl_current_user_image'];
          $img= getUserPhoto($user_id,$myusers[$mypf]['gks_wsl_current_user_image'],64);
          $name_class='';
        }
        
        $lasttime='';
        if (isset($myi['maxdate'])) {
          $lasttime=secondsago(strtotime($myi['maxdate']));
        }
        
        if ($name=='') {
          $name=$myi['dmaxname'];
          $name_class='gks_sms_user_div_name_from_mobile';
        }
        
        $name_conv=greeklish($name).' '.greekkeybord($name);
        $name_conv=str_replace('"','',$name_conv);
        $name_conv=str_replace("'",'',$name_conv);
        $name_conv=str_replace('&','',$name_conv);
        $name_conv=trim($name_conv);
        
        $html.=
        '<div class="gks_sms_user_div" data-number="'.$mypf.'" data-user_id="'.$user_id.'" data-user_name="'.$name_conv.'">'.
          '<div class="gks_sms_user_div_photo">'.
          $img.
          '</div>'.
          '<div class="gks_sms_user_div_detail">'.
            '<div class="gks_sms_user_div_name '.$name_class.'">'.
              $name.
            '</div>'.
            '<div class="gks_sms_user_div_number">'.
            $mypf.
            '</div>'.
            '<div class="gks_sms_user_div_date">'.
            $lasttime.
            '</div>'.
          '</div>'.
        '</div>';
      }
    }
    
    
    
    
    $return['success']=true;
    $return['html']=$html;
    //$return['html']='<pre>'.print_r($mychats, true).'</pre>';
    //$return['html']='<pre>'.print_r($usenders, true).'</pre>';
    $return['message'] = base64_encode('OK');
    echo json_encode($return); die();    
    break;
    
  case 'get_from_number':
  case 'updateme':
    $number='';  if (isset($_POST['number'])) $number=trim_gks(base64_decode($_POST['number']));
    if ($number=='') { 
      $return['message']=base64_encode(gks_lang('Δεν ορίσθηκε ο αριθμός'));
      echo json_encode($return); die();
    }
    $prev_date='';
    
    $updateme_sql='';
    if ($cmd=='updateme') {
      $last_id=0; if (isset($_POST['last_id'])) $last_id=intval($_POST['last_id']); 
      
      $insert_id_array_str = trim_gks(base64_decode($_POST['insert_id_array_str']));
      $insert_id_array = json_decode($insert_id_array_str, true);
      if ($insert_id_array === null && json_last_error() !== JSON_ERROR_NONE) {
        debug_mail(false,'json_decode error',$_POST['insert_id_array_str']);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
        echo json_encode($return); die();}
      
      
      $updateme_sql.=" and id > ".$last_id;
      if (count($insert_id_array)>0) {
        $updateme_sql.=" and id not in (".implode(',',$insert_id_array).")";
        
      } 
      if (isset($_POST['prev_date'])) $prev_date=trim_gks(base64_decode($_POST['prev_date']));
      //echo '<pre>'; die();
    }
    
    if (strlen($number)==12 and substr($number,0,4)=='3069') {
      $number=substr($number,2);
    }
    
    $html='';
    $sql="SELECT *
    FROM gks_sms
    WHERE (myfrom like '%".$db_link->escape_string($number)."' OR myto like '%".$db_link->escape_string($number)."')
    ".$updateme_sql."
    ORDER BY gks_sms.date_add asc;";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$return['message']=base64_encode('sql error');echo json_encode($return); die();}
    
    $count_msgs=0;$last_id=0;$rechecks=array();$last_inbox_id=0;
    $limit_time=time()-28*60*60;
    $msgs_ids=[];
    while ($row = $result->fetch_assoc()) {
      $count_msgs++;
      $msgs_ids[]=intval($row['id']);
      
      $date_add_time=strtotime($row['date_add']);
      $curr_date = showDate($date_add_time, 'd/m/Y', 1);
      
      if ($curr_date!=$prev_date) {
        $html.='<div class="gks_sms_date_sep">'.mb_substr(getWeekDayName(showDate($date_add_time, 'w', 1)),0,2).' '.$curr_date.'</div>';
        $prev_date=$curr_date;
      }
      
      $icons='';
      $html.=gks_sms_chat_render_row($row, '',$icons);
      
      
      
      if ($row['sms_folder']=='sent' and 
         ($row['status']==409 or $row['status']==403) and 
         strtotime($row['date_add'])>$limit_time) {
        $rechecks[]=intval($row['id']);
      }
      if ($row['sms_folder']=='inbox') {
        if (intval($row['id'])>$last_inbox_id) $last_inbox_id=intval($row['id']);
      }
      
      if (intval($row['id'])>$last_id) $last_id=intval($row['id']);
    }
    
    $out_icons=[];
    $finish_rechecks=[];
    if ($cmd=='updateme') {
      $rechecks_array_str = trim_gks(base64_decode($_POST['rechecks_str']));
      $rechecks_array = json_decode($rechecks_array_str, true);
      if ($rechecks_array === null && json_last_error() !== JSON_ERROR_NONE) {
        debug_mail(false,'json_decode error',$_POST['insert_id_array_str']);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
        echo json_encode($return); die();}

      if (count($rechecks_array)>0) {
        $sql="select * from gks_sms where id in (".implode(',',$rechecks_array).")";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'error sql',$sql);$return['message']=base64_encode('sql error');echo json_encode($return); die();}
        
        
        
        while ($row = $result->fetch_assoc()) {
          $icons='';
          gks_sms_chat_render_row($row, 'gks_msgs_item_new ',$icons);
          $out_icons[]=array(
            'id' => $row['id'],
            'icons' => $icons,
          );
          
          if ($row['status']==409 or $row['status']==403) { //sinexisei na kanei chek //stalthike stin oura
          
          } else {
            //den einai stalthike stin oura ara samata to check
            $finish_rechecks[]=intval($row['id']);
          }
        }
      
      }

    }
    
    $sql="select count(*) as cc from gks_sms 
    where (myfrom like '%".$db_link->escape_string($number)."' OR myto like '%".$db_link->escape_string($number)."')";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$return['message']=base64_encode('sql error');echo json_encode($return); die();}
    $row = $result->fetch_assoc();
    $total_sms=0;if (isset($row['cc'])) $total_sms=intval($row['cc']);

    $return['total_sms']=$total_sms;

    //echo 'ggggggg'.$sql;die();
    $return['success']=true;
    $return['prev_date']=$prev_date;
    $return['out_icons']=$out_icons;
    $return['finish_rechecks']=$finish_rechecks;
    $return['rechecks']=$rechecks;
    $return['html']=$html;
    $return['count_msgs']=$count_msgs;
    $return['msgs_ids']=$msgs_ids;
    $return['last_inbox_id']=$last_inbox_id;
    $return['last_id']=$last_id;
    $return['message'] = base64_encode('OK');
    
    //echo 'h3 <pre>';
    //print_r($return);die();
    
    echo json_encode($return,JSON_INVALID_UTF8_IGNORE); die();    
    break;
    
  case 'sent_text':
    $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_sms','add',0);
    if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

  
    $number='';  if (isset($_POST['number'])) $number=trim_gks(base64_decode($_POST['number']));
    if ($number=='') { 
      $return['message']=base64_encode(gks_lang('Δεν ορίσθηκε ο αριθμός'));
      echo json_encode($return); die();
    }
    $text='';  if (isset($_POST['text'])) $text=trim_gks(base64_decode($_POST['text']));
    if ($text=='') { 
      $return['message']=base64_encode(gks_lang('Δεν ορίσθηκε το κείμενο'));
      echo json_encode($return); die();
    }
        
    if (!isset($_POST['from'])|| trim_gks($_POST['from'])=='') {
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε τον αποστολέα').' (1)'));
      echo json_encode($return); die();
    }
    $myfrom=trim_gks(base64_decode($_POST['from']));
    
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

    //echo '<pre>'.$myfrom.'|'.$sender_sms_provider.'|'.$sender_sms_sender;die();
    
    $gks_sms_send_insert_id=0;
    $model='admin';
    $model_id=0;
    $to=$number;
    $szMessageText=$text;
    $ret=gks_sms_send($model, $model_id, $sender_sms_sender, $to, $szMessageText,$sender_sms_provider);
    
    if ($gks_sms_send_insert_id==0) {
      $return['message']=base64_encode(gks_lang('Σφάλμα αποστολής'));
      echo json_encode($return); die();}
    
    $html='';
    $sql="SELECT * FROM gks_sms WHERE id=".$gks_sms_send_insert_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$return['message']=base64_encode('sql error');echo json_encode($return); die();}

    $prev_date='';if (isset($_POST['prev_date'])) $prev_date=trim_gks(base64_decode($_POST['prev_date']));
    $last_id=0;
    while ($row = $result->fetch_assoc()) {
      $date_add_time=strtotime($row['date_add']);
      $curr_date = showDate($date_add_time, 'd/m/Y', 1);
      if ($curr_date!=$prev_date) {
        $html.='<div class="gks_sms_date_sep">'.mb_substr(getWeekDayName(showDate($date_add_time, 'w', 1)),0,2).' '.$curr_date.'</div>';
        $prev_date=$curr_date;
      }
    
      $icons='';
      $html.=gks_sms_chat_render_row($row, 'gks_msgs_item_new ',$icons);
      
      $last_id=$row['id'];
    } 

    $sql="select count(*) as cc from gks_sms 
    where (myfrom like '%".$db_link->escape_string($number)."' OR myto like '%".$db_link->escape_string($number)."')";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$return['message']=base64_encode('sql error');echo json_encode($return); die();}
    $row = $result->fetch_assoc();
    $total_sms=0;if (isset($row['cc'])) $total_sms=intval($row['cc']);

    $return['success']=true;
    $return['total_sms']=$total_sms;
    $return['prev_date']=$prev_date;
    $return['html']=$html;
    $return['gks_sms_send_insert_id']=$gks_sms_send_insert_id;
    $return['message'] = base64_encode('OK');
    echo json_encode($return); die();    
    break; 
  case 'get_user_details':
    $user_id=0;if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
    if ($user_id<=0) {
      $return['success']=true;
      $return['html']='contact not found';
      $return['message'] = base64_encode('OK');
      echo json_encode($return); die();  
    }
    $sql="select ".GKS_WP_TABLE_PREFIX."users.*,
    table_last_name.mylast_name, table_first_name.myfirst_name,
    gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
    gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, gks_users.ma_poli, gks_users.ma_tk, 
    gks_users.ma_country_id, gks_users.ma_nomos_id, 
    gks_country.country_name, gks_nomoi.nomos_descr,
    pelati_sxolio
    from ((((".GKS_WP_TABLE_PREFIX."users 
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
    LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
    LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
    LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos
    
    where ID=".$user_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$return['message']=base64_encode('sql error');echo json_encode($return); die();}
    if ($result->num_rows==0) {
      $return['success']=true;
      $return['html']=gks_lang('Δεν βρέθηκε η επαφή');
      $return['message'] = base64_encode('OK');
      echo json_encode($return); die();      
    }
    $row_user = $result->fetch_assoc();
    
    $html='';
    $photo=getUserPhoto($user_id,$row_user['gks_wsl_current_user_image'],64);
    $html.='<div class="user_photo">'.$photo.'</div>';
    if (isset($row_user['pelati_sxolio']) and empty($row_user['pelati_sxolio'])==false) {
      $html.='<div class="user_sxolio alert alert-danger" style="margin-top:10px;">'.nl2br_gks($row_user['pelati_sxolio']).'</div>';
      
    }
    $html.='<div class="user_icons" style="margin-top:10px;">'.
    '<a href="admin-users-item.php?id='.$user_id.'"><i class="enterrow fas fa-pen tooltipster" title="'.gks_lang('Προβολή').'"></i></a>'.
    '<a href="admin-users-item-card.php?id='.$user_id.'"><i class="fas fa-list-alt gks_user_item_card tooltipster" title="'.gks_lang('Οικονομική Καρτέλα').'"></i></a>'.
    '<a href="admin-users-item-overview.php?id='.$user_id.'"><i class="fas fa-list-alt gks_user_item_overview tooltipster" title="'.gks_lang('Επισκόπηση').'"></i></a>'.
    '</div>';
    
    $html.='<div class="user_nickname" style="margin-top:10px;">'.$row_user['gks_nickname'].'</div>';
    $html.='<div class="user_last">'.(isset($row_user['myfirst_name']) ? $row_user['myfirst_name'] : '').' '.(isset($row_user['mylast_name']) ? $row_user['mylast_name'] : '').'</div>';
    
    $sql="SELECT comm_type, comm_value, comm_descr
    FROM gks_users_communication
    WHERE user_id=".$user_id."
    ORDER BY comm_type, comm_primary";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$return['message']=base64_encode('sql error');echo json_encode($return); die();}
    
    $comms=[];
    while ($row_comm = $result->fetch_assoc()) {
      switch ($row_comm['comm_type']) {
        case 'email':
          $comms[]='<a href="mailto:'.$row_comm['comm_value'].'">'.$row_comm['comm_value'].'</a>';
          break;  
        case 'phone':
          $comms[]='<a href="tel:'.$row_comm['comm_value'].'">'.$row_comm['comm_value'].'</a>';
          break;  
        case 'url':
          $prefix='';
          if (startwith($row_comm['comm_value'],'http')==false) $prefix='http://';
          $comms[]='<a href="'.$prefix.$row_comm['comm_value'].'" target="_blank">'.$row_comm['comm_value'].'</a>';
          break;  
          
        default:
          
          break; 
      }
    }

    $html.='<div class="user_comms">'.implode('<br>',$comms).'</div>';
    
    $address=[];
    $tttt=trim_gks(trim_gks($row_user['ma_odos']).' '.trim_gks($row_user['ma_arithmos']));
    if ($tttt!='') $address[]=$tttt;
    if (isset($row_user['ma_orofos']) and empty($row_user['ma_orofos'])==false) $address[]=$row_user['ma_orofos'];
    if (isset($row_user['ma_perioxi']) and empty($row_user['ma_perioxi'])==false) $address[]=$row_user['ma_perioxi'];
    if (isset($row_user['ma_poli']) and empty($row_user['ma_poli'])==false) $address[]=$row_user['ma_poli'];
    if (isset($row_user['ma_tk']) and empty($row_user['ma_tk'])==false) $address[]=$row_user['ma_tk'];
    if (isset($row_user['nomos_descr']) and empty($row_user['nomos_descr'])==false) $address[]=$row_user['nomos_descr'];
    if (isset($row_user['country_name']) and empty($row_user['country_name'])==false) $address[]=$row_user['country_name'];
    $html.='<div class="user_address">'.implode('<br>',$address).'</div>';
    
 
    $afm_doy=[];
    if (isset($row_user['title']) and empty($row_user['title'])==false) $afm_doy[]=$row_user['title'];
    if (isset($row_user['eponimia']) and empty($row_user['eponimia'])==false) $afm_doy[]=$row_user['eponimia'];
    if (isset($row_user['afm']) and empty($row_user['afm'])==false) $afm_doy[]=$row_user['afm'];
    if (isset($row_user['doy']) and empty($row_user['doy'])==false) $afm_doy[]=$row_user['doy'];
    if (isset($row_user['epaggelma']) and empty($row_user['epaggelma'])==false) $afm_doy[]=$row_user['epaggelma'];
    $html.='<div class="user_afm_doy">'.implode('<br>',$afm_doy).'</div>';


    
    $return['success']=true;
    $return['html']=$html;
    $return['message'] = base64_encode('OK');
    echo json_encode($return); die();    
  
    break;
  default:
    debug_mail(false,'cmd error','');
    $return['message'] = base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    echo json_encode($return); die();    
}



function gks_sms_chat_render_row($row, $extraclass,&$icons) {

  
  //$lasttime='<span>'.secondsago(strtotime($row['date_add'])).'</span>';
  
  $lasttime='<span>'.showDate(strtotime($row['date_add']), 'H:i:s', 1).'</span>';
  
  $icons='';
  if ($row['sms_folder']=='sent') {
    $icons='';
    if (gks_sms_can_resend_status($row['status'],$row['model'])) {
      $icons.='<i class="gks_sms_chat_resend icon_resend fas fa-sync-alt tooltipster" title="'.gks_lang('Επαναποστολή').'" data-id="'.$row['id'].'"></i>';
    } else {
      $icons.='<i class="icon_space_resend"></i>';
    }
       
    if ($row['myret']==1) {
      $icons.='<i class="icon_ret icon_ret_ok fas fa-check-circle"></i>';
    } else {
      $icons.='<i class="icon_ret icon_ret_error fas fa-times-circle" title="';
      $sms_result=trim_gks($row['sms_result']);
      if (0 === strpos($sms_result, 'ERROR:'))
        $icons.= substr($sms_result,6);
      else
        $icons.= $sms_result;
        
      $icons.='"></i>';
    }
    $fas='';
    if (in_array($row['status'],[410,409])) {
      $color='yellow';$fas='fas fa-check-circle';
    } else if (in_array($row['status'],[403])) {
      $color='blue';$fas='fas fa-check-circle';
    } else if (in_array($row['status'],[404])) {
      $color='green';$fas='fas fa-check-circle';
    } else if (in_array($row['status'],[401,402,405,406,407,408,412])) {
      $color='red';$fas='fas fa-times-circle';
    } else {
      $color='gray';$fas='fas fa-minus-circle';
    }
    
    $icons.='<i class="icon_status icon_status_'.$row['status'].' icon_color_'.$color.' '.$fas.'" title="';
    $icons.=$row['status_name'];
    $icons.='"></i>';
    

    
    //<i class="fas fa-times-circle"></i>
    //<i class="fas fa-check-circle"></i>
    //<i class="fas fa-check-circle"></i>
    
  }
  $text=($row['Message'] != '' ? nl2br_gks($row['Message']) : nl2br_gks($row['Message_post']));
  $text='<span class="msg_text"><div>'.$text.'</div><span class="msg_time">'.$lasttime.'<span class="icons">'.$icons.'</span></span></span>';
        
  $html=
  '<div class="'.$extraclass.'gks_msgs_item gks_msgs_item_'.$row['sms_folder'].'" data-msg-id="'.$row['id'].'">'.
    '<div class="gks_msgs_item_cell1">'.
    ($row['sms_folder']!='inbox' ? '' : $text).
    '</div>'.
    '<div class="gks_msgs_item_cell2">'.
    ($row['sms_folder']=='inbox' ? '' : $text).
    '</div>'.
  
  '</div>';  
  
  return $html;  
}

$return = array('success' => false, 'message' => base64_encode(gks_lang('Γενικό σφάλμα')));
echo json_encode($return); die();



