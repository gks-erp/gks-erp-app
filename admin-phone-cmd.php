<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$cmd='';if (isset($_POST['cmd'])) $cmd=trim($_POST['cmd']);
if (in_array($cmd,['addfavorites','getlistcalls','getlatestphonecalls','percall'])==false) {
  debug_mail(false,'the cmd is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' cmd'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Εκτέλεση εντολής για Τηλέφωνο').': '.$cmd;
db_open();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_voip_calls','view',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

if ($cmd=='addfavorites') {
  stat_record();
  $gks_voip_params=gks_voip_user_params();

  $phone=''; if (isset($_POST['phone'])) $phone=base64_decode($_POST['phone']);
  $nickname=''; if (isset($_POST['nickname'])) $nickname=base64_decode($_POST['nickname']);
  if ($phone=='') {
    debug_mail(false,'the phone is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' '.gks_lang('Τηλέφωνο')));
    echo json_encode($return); die();}
  if ($nickname=='') {
    debug_mail(false,'the nickname is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' '.gks_lang('Όνομα')));
    echo json_encode($return); die();}
  
  $sql="insert into gks_voip_favorites (
  mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  user_id,phone,nickname,mysortorder
  ) values (
  now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$my_wp_user_id.",
  '".$db_link->escape_string($phone)."',
  '".$db_link->escape_string($nickname)."',
  1000)";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $id_voip_favorite = $db_link->insert_id; 

  $row_html=
  '<tr class="favorites_tr_new" data-id="'.$id_voip_favorite.'">'.
    '<th scope="row" nowrap align="center" class="favorites_aa">*</td>'.
    '<td nowrap align="center">'.
      '<img src="img/delete.png" border="0" width="16" class="deleterow" data-deleteafter="gks_fnc_favorites_delete_after|'.$id_voip_favorite.'" data-id="'.$id_voip_favorite.'" data-model="gks_voip_favorites">'.
    '</td>'.
    '<td class="p-40">'.
      '<a href="tel:'.$phone.'" class="'.$gks_voip_params['class_span'].'">'.$phone.'</a>'.
      $gks_voip_params['html_after_span'].
    '</td>'.
    '<td>'.$nickname.'</td>'.
    '<td nowrap class="mytdcm sortorder_handle" title="1000">'.
      '<i class="fas fa-arrows-alt-v"></i>'.
      '<span>1000</span>'.
    '</td>'.
  '</tr>';

  $return = array('success' => true, 'message' => base64_encode('OK'),'row_html'=>base64_encode($row_html));
  echo json_encode($return); die();  
  
}
if ($cmd=='getlistcalls') {
  //stat_record();
  $sql = "SELECT gks_voip_calls.id_voip_call, 
  gks_voip_calls.mydate_add,
  gks_voip_calls.src,
  gks_voip_calls.uniqueid,
  gks_voip_calls.gks_user_id,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
  gks_erp_app.erp_app_name
  FROM (gks_voip_calls 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_voip_calls.gks_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_erp_app ON gks_voip_calls.erp_app_id = gks_erp_app.id_erp_app
  where gks_voip_calls.gks_primary_rec=1
  and gks_voip_calls.mydate_add>='".$today_vardia."'
  order by id_voip_call desc";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}

  
  $html='<table class="table table-sm table-responsive table-striped table-bordered gkstable " border="0" cellspacing="0" cellpadding="5" align="center" style="width:100%;" id="gks_calls_table">
<thead>
  <tr>
    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap width="0%">#</th>
    <th class="table-dark" scope="col" style="text-align: center !important;" nowrap width="0%"></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="20%" nowrap="nowrap">'.gks_lang('Πότε').'</th>        
    <th class="table-dark" scope="col" style="text-align: center !important;" width="30%" nowrap="nowrap">'.gks_lang('Από').'</th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="50%" nowrap="nowrap">'.gks_lang('Επαφή').'</th>      
  </tr>
</thead>
<tbody>';
    $i = 0;
    $latest_id_voip_call=0;
    while ($row = $result->fetch_assoc()) {
      if ($i==0) {
        $gks_voip_params=gks_voip_user_params();
      }
      if ($row['id_voip_call']>$latest_id_voip_call) $latest_id_voip_call=intval($row['id_voip_call']);
	    $i++;
    
  $html.=
'<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').' gks_tr1_uniqueid" data-tr1-uniqueid="'.$row['uniqueid'].'">
  <th scope="row" nowrap class="mytdcm">'.$i.'</th>
  <td nowrap class="mytdcm p-0"><i class="fas fa-caret-square-down voip_more_data_row" data-uniqueid="'.$row['uniqueid'].'" data-status="0"></i></td>
  <td nowrap class="mytdcm" title="'.showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1).'">'.secondsago(strtotime($row['mydate_add'])).'</td>   
  <td nowrap class="mytdcm p-40">
    <a href="tel:'.$row['src'].'" class="'.$gks_voip_params['class_span'].'">'.$row['src'].'</a>
    '.$gks_voip_params['html_after_span'].'
  </td>   
  <td class="mytdcml"><a href="admin-users-item-overview.php?id='.$row['gks_user_id'].'" target="_blank">'.$row['gks_nickname'].'</a></td>
</tr>';



    }
  $html.='</tbody>
</table>';
 
  
  $return = array('success' => true, 'message' => base64_encode('OK'),
    'html'=>base64_encode($html),
    'latest_id_voip_call'=>$latest_id_voip_call,
  );
  echo json_encode($return); die();   
}

if ($cmd=='getlatestphonecalls') {
  $latest_id_voip_call=0;if (isset($_POST['latest_id_voip_call'])) $latest_id_voip_call=intval($_POST['latest_id_voip_call']);
  
  $start_time=time();
  do {
    $sql = "SELECT gks_voip_calls.id_voip_call, 
    gks_voip_calls.gks_user_id,
    gks_voip_calls.mydate_add,
    gks_voip_calls.src,
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    FROM gks_voip_calls 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_voip_calls.gks_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    where gks_voip_calls.gks_primary_rec=1
    and gks_voip_calls.mydate_add>='".$today_vardia."'
    and id_voip_call>".$latest_id_voip_call."
    order by id_voip_call desc";
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    $data=[];
    if ($result->num_rows==0) {
      sleep(2);
    } else {
      while ($row = $result->fetch_assoc()) {
        $data[]=array(
          'id_voip_call'=>$row['id_voip_call'],
          'gks_user_id'=>intval($row['gks_user_id']),
          'gks_nickname'=>$row['gks_nickname'],
          'ago'=>secondsago(strtotime($row['mydate_add'])),
          'src'=>$row['src'],
        );
        if ($row['id_voip_call']>$latest_id_voip_call) $latest_id_voip_call=intval($row['id_voip_call']);
      }
      break;
    }
  } while ((time()-$start_time)<10);
  
  $return = array('success' => true, 'message' => base64_encode('OK'),
    'data'=>$data,
    'latest_id_voip_call'=>$latest_id_voip_call,  
  );
  echo json_encode($return); die();   

}

if ($cmd=='percall') {
  $uniqueid='';if (isset($_POST['uniqueid'])) $uniqueid=trim($_POST['uniqueid']);
  if ($uniqueid=='') {
    debug_mail(false,'the uniqueid is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' uniqueid.'));
    echo json_encode($return); die();}
  
  $view='';if (isset($_POST['view'])) $view=trim($_POST['view']);
  if (in_array($view,['short','long'])==false) {
    debug_mail(false,'the view is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' view.'));
    echo json_encode($return); die();}

  $html='<table class="table table-sm table-responsive'.($view=='long'?'1':'').' table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">
  <thead>
      <tr>';
  
  if ($view=='short') {
  $html.='    	
          <th class="table-dark" scope="col" style="text-align: center !important;" nowrap width="0%">#</th>
          <th class="table-dark" scope="col" style="text-align: center !important;" width="40%" nowrap="nowrap">'.gks_lang('Προς').'</th>
          <th class="table-dark" scope="col" style="text-align: center !important;" width="60%" nowrap="nowrap">disposition</th>
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">duration</th>
  
  ';  
  } else {
  $html.='    	
          <th class="table-dark" scope="col" style="text-align: center !important;" nowrap width="0%">#</th>
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">'.gks_lang('ID').'</th>
          <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap">'.gks_lang('Ημερομηνία').'</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap">'.gks_lang('Πριν από').'</th>        
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="nowrap">'.gks_lang('Επαφή').'</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap">'.gks_lang('Από').'</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" nowrap="nowrap">'.gks_lang('Προς').'</th>        
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="nowrap">'.gks_lang('clid').'</th>        
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%" nowrap="nowrap">Caller Name</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">disposition</th>        
  
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap">Desktop App</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">AcctId</th>        
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap">dcontext</th>        
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap">channel</th>        
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap">dstchannel</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">lastapp</th>        
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap">lastdata</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">start</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">answer</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">end</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">duration</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">billsec</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">amaflags</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">uniqueid</th>        
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap">userfield</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">channel_ext</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">dstchannel_ext</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">service</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">dstanswer</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">recordfiles</th>        
          <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap">session</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">action_owner</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">action_type</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">src_trunk_name</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">dst_trunk_name</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">new_src</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">reason</th>        
          <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap">sn</th>        
  ';
  } 
  $html.='     
      </tr>
  </thead>
  <tbody>';
  
  $sql = "SELECT SQL_CALC_FOUND_ROWS gks_voip_calls.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
  gks_erp_app.erp_app_name
  FROM (gks_voip_calls 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_voip_calls.gks_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_erp_app ON gks_voip_calls.erp_app_id = gks_erp_app.id_erp_app
  where gks_voip_calls.uniqueid='".$db_link->escape_string($uniqueid)."'
  order by id_voip_call desc";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $i = 0;
  while ($row = $result->fetch_assoc()) {
  	$i++;
  $html.='
    <tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'" >';
    
  if ($view=='short') {
  $html.='
      <th scope="row" nowrap class="mytdcm">'.$i.'</th>
      <td nowrap class="mytdcm">'.$row['dst'].'</td>   
      <td nowrap class="mytdcm">'.$row['disposition'].'</td>   
      <td nowrap class="mytdcm">'.$row['duration'].'</td>   
  ';
  } else {
  $html.='
      <th scope="row" nowrap class="mytdcm">'.$i.'</th>
      <td nowrap class="mytdcm">'.$row['id_voip_call'].'</td>   
      <td nowrap class="mytdcm">'.showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1).'</td>   
      <td nowrap class="mytdcm">'.secondsago(strtotime($row['mydate_add'])).'</td>   
      <td nowrap class="mytdcml"><a href="admin-users-item-overview.php?id='.$row['gks_user_id'].'">'.$row['gks_nickname'].'</a></td>   
      <td nowrap class="mytdcm">'.$row['src'].'</td>   
      <td nowrap class="mytdcm">'.$row['dst'].'</td>   
      <td nowrap class="mytdcml">'.$row['clid'].'</td>   
      <td nowrap class="mytdcml">'.$row['caller_name'].'</td>   
  
      <td nowrap class="mytdcm">'.$row['disposition'].'</td>   
      <td nowrap class="mytdcml"><a href="admin-erp-app-item.php?id='.$row['erp_app_id'].'">'.$row['erp_app_name'].'</a></td>   
      <td nowrap class="mytdcm">'.$row['AcctId'].'</td>   
      <td nowrap class="mytdcml">'.$row['dcontext'].'</td>   
      <td nowrap class="mytdcml">'.$row['channel'].'</td>   
      <td nowrap class="mytdcml">'.$row['dstchannel'].'</td>   
      <td nowrap class="mytdcm">'.$row['lastapp'].'</td>   
      <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">'.
        $row['lastdata'].
      '</div></div></td>
      <td nowrap class="mytdcm">'.$row['start'].'</td>   
      <td nowrap class="mytdcm">'.$row['answer'].'</td>   
      <td nowrap class="mytdcm">'.$row['end'].'</td>   
      <td nowrap class="mytdcm">'.$row['duration'].'</td>   
      <td nowrap class="mytdcm">'.$row['billsec'].'</td>   
      <td nowrap class="mytdcm">'.$row['amaflags'].'</td>   
      <td nowrap class="mytdcml">'.$row['uniqueid'].'</td>   
      <td nowrap class="mytdcm">'.$row['userfield'].'</td>   
      <td nowrap class="mytdcm">'.$row['channel_ext'].'</td>   
      <td nowrap class="mytdcm">'.$row['dstchannel_ext'].'</td>   
      <td nowrap class="mytdcm">'.$row['service'].'</td>   
      <td nowrap class="mytdcm">'.$row['dstanswer'].'</td>   
      <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">'.
        $row['recordfiles'].
      '</div></div></td>     
      <td nowrap class="mytdcm">'.$row['session'].'</td>   
      <td nowrap class="mytdcm">'.$row['action_owner'].'</td>   
      <td nowrap class="mytdcm">'.$row['action_type'].'</td>   
      <td nowrap class="mytdcm">'.$row['src_trunk_name'].'</td>   
      <td nowrap class="mytdcm">'.$row['dst_trunk_name'].'</td>   
      <td nowrap class="mytdcm">'.$row['new_src'].'</td>   
      <td nowrap class="mytdcm">'.$row['reason'].'</td>   
      <td nowrap class="mytdcm">'.$row['sn'].'</td>   
  ';
  }
  $html.='
           
    </tr>';  
  }
  
  
  $html.='</tbody>
  </table>';
  
  
  
  $return = array('success' => true, 'message' => base64_encode('OK'), 'uniqueid'=>$uniqueid, 'html'=> $html);
  echo json_encode($return); die();  
}

echo '<pre>error';die();

