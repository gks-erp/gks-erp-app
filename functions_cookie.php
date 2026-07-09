<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

// $_SESSION
// session_id()
// session_start()
// $_gks_session

// global $_gks_session;
//$_gks_session=array();

//$gks_erp_curr_user_cookie_data=false;
//$gks_erp_curr_user_cookie_id=false;
//
//function gks_erp_cookie_get_user_data() {
//  global $db_link;
//  global $gks_erp_curr_user_cookie_data;
//  
//  if ($gks_erp_curr_user_cookie_data!==false) return;
//  $gks_erp_curr_user_cookie_data=array();
//  
//  $gks_erp_curr_user_cookie_id='';
//  if (isset($_COOKIE['gks_erp_cookie_id'])) $gks_erp_curr_user_cookie_id=trim_gks($_COOKIE['gks_erp_cookie_id']);
//  
//  
//}

//function gks_session_set() {
//  if (!isset($_SESSION['gks'])) $_SESSION['gks']=array();
//  if (!isset($_gks_session['gks']['rows_per_page']))	$_gks_session['gks']['rows_per_page']=50;
//  if (!isset($_SESSION['gks']['stat']))	$_SESSION['gks']['stat']=array();
//  if (!isset($_SESSION['gks']['stat']['admin_stats_bots_enable']))	$_SESSION['gks']['stat']['admin_stats_bots_enable']=true;
//  if (!isset($_SESSION['gks']['this_site']))	$_SESSION['gks']['this_site']=array();
//  if (!isset($_SESSION['gks']['ui_lang'])) $_SESSION['gks']['ui_lang']='el-GR';
//  if (!isset($_SESSION['gks']['recordback'])) $_SESSION['gks']['recordback']='';  
//  
//  gks_mybasketarray_create($_SESSION['gks']['basket']);
//}


function gks_erp_cookie_start($cookie_id='') {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $_gks_session;
  global $_gks_id_session;

  if (isset($_SERVER['SCRIPT_NAME']) and substr($_SERVER['SCRIPT_NAME'],0,8)=='/my/cron') return;
  
  
  if ($cookie_id=='' and isset($_COOKIE['gks_erp_cookie_id'])) $cookie_id=trim_gks($_COOKIE['gks_erp_cookie_id']);
  
  
  if ($db_link===null) db_open();
  
  $sql="select data from gks_erp_cookie where gks_erp_cookie_id ='".$db_link->escape_string($cookie_id)."'";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);echo '<pre>sql error'; die();}    
  $is_new=true;
  if ($result->num_rows>0) {
    $is_new=false;
    $row = $result->fetch_assoc();
    $_gks_session=unserialize($row['data']);
  }
  $_gks_session['gks']['last_action'] = time();
  
  //echo 'ggggggggddd|'.print_r($_gks_session,true).'|'; die();

  gks_erp_cookie_defaults();
    
  //print '<pre>';print_r($_gks_session);
  
  if ($cookie_id!='') {
    $_gks_id_session=$cookie_id;
    
    if ($is_new) {
      $sql="insert into gks_erp_cookie (
        gks_erp_cookie_id,
        user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
        data    
      ) values (
      '".$db_link->escape_string($cookie_id)."',
      ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
      '".$db_link->escape_string(serialize($_gks_session))."'
      )";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);echo '<pre>sql error'; die();}    
    } else {
      $sql="update gks_erp_cookie set
      user_id_edit=".$my_wp_user_id.",
      mydate_edit=now(),
      myip='".$db_link->escape_string($gkIP)."',
      data='".$db_link->escape_string(serialize($_gks_session))."'
      where gks_erp_cookie_id ='".$db_link->escape_string($cookie_id)."'";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);echo '<pre>sql error'; die();}    
    }
  }
  
}
function gks_erp_cookie_defaults() {
  global $_gks_session;
  
  if (!isset($_gks_session['gks'])) $_gks_session['gks']=array();
  if (!isset($_gks_session['gks']['rows_per_page']))	$_gks_session['gks']['rows_per_page']=50;
  if (!isset($_gks_session['gks']['stat']))	$_gks_session['gks']['stat']=array();
  if (!isset($_gks_session['gks']['stat']['admin_stats_bots_enable']))	$_gks_session['gks']['stat']['admin_stats_bots_enable']=true;
  if (!isset($_gks_session['gks']['this_site']))	$_gks_session['gks']['this_site']=array();
  if (!isset($_gks_session['gks']['ui_lang'])) $_gks_session['gks']['ui_lang']='el-GR';
  if (!isset($_gks_session['gks']['recordback'])) $_gks_session['gks']['recordback']='';  

  
  //echo '<pre>';print_r($_SERVER);die();
  if (isset($_SERVER['REQUEST_URI']) and substr($_SERVER['REQUEST_URI'], 0, 10)=='/my/admin-') {
    $_gks_session['gks']['ui_lang']='el-GR';
    //$_gks_session['gks']['ui_lang']='en-US';
  }
  
  
  
  gks_mybasketarray_create($_gks_session['gks']['basket']);

  
}

function gks_erp_cookie_save($cookie_id='') {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $_gks_session;
  global $_gks_id_session;
  
  if (is_array($_gks_session)==false) $_gks_session=array();
  
  if ($cookie_id=='' and isset($_COOKIE['gks_erp_cookie_id'])) $cookie_id=trim_gks($_COOKIE['gks_erp_cookie_id']);
  if ($cookie_id=='') return;

  $_gks_id_session=$cookie_id;
  //echo $_gks_id_session;die();
  
  $sql="update gks_erp_cookie set
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."',
  data='".$db_link->escape_string(serialize($_gks_session))."'
  where gks_erp_cookie_id ='".$db_link->escape_string($cookie_id)."'";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);echo '<pre>sql error'; die();}    
  
}

