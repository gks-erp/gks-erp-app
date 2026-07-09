<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$cmd=''; if (isset($_POST['cmd'])) $cmd=trim_gks($_POST['cmd']);
if ($cmd=='') {
  debug_mail(false,'cmd is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εντολή')));
  echo json_encode($return); die();}

if (in_array($cmd,['check_version','downloadfiles','unzip','dbupdate'])==false) {
  debug_mail(false,'cmd is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η εντολή σωστά').' '.$cmd));
  echo json_encode($return); die();}
  


$my_page_title=gks_lang('Εντολή Αναβάθμισης').': '.$cmd;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_app_info','edit',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

$return = array('success' => false, 'message' => base64_encode('generic error'));

if (defined('GKS_SITE_HTTPDOCS')==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν έχει ορισθεί η σταθερά GKS_SITE_HTTPDOCS');
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  
  
$save_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_update';
if (file_exists($save_path)==false) @mkdir($save_path,0755);
if (file_exists($save_path)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').'<br>'.$save_path;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

if ($cmd=='check_version') {
  $url="https://tools.gks.gr/gks_erp/latest.json?time=".time().rand(1000,9999).rand(1000,9999);
  
  $data_raw=file_get_contents($url);
  if ($data_raw=='') {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η λήψη πληροφοριών αναβάθμισης από').'<br>'.$url;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
    
  @file_put_contents($save_path.'/latest.json',$data_raw);  

  $data=json_decode($data_raw,true);
  if (is_array($data)==false or 
     isset($data['name'])==false or 
     $data['name']!='gks ERP' or 
     isset($data['filesize'])==false or 
     $data['filesize']<=10000 or 
     isset($data['url'])==false or 
     strlen($data['url'])<10) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η λήψη δεδομένων από').'<br>'.$url;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  
    
  $sql="replace into gks_settings (mykey,myvalue) values ('GKS_ERP_APP_LAST_latest.json','".$db_link->escape_string($data_raw)."')";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
  
  $curr_version=$GKS_CACHE_DB_VER.'.'.$gks_cache_version;
  $live_version=$data['DB Version'].'.'.$data['Cache Version'];
  $need_update=true;
  if ($GKS_CACHE_DB_VER==$data['DB Version'] and $gks_cache_version==$data['Cache Version']) {
    $need_update=false;  
  } else if ($GKS_CACHE_DB_VER==$data['DB Version'] and $gks_cache_version > $data['Cache Version']) {
    $need_update=false;  
  } else if ($GKS_CACHE_DB_VER > $data['DB Version']) {
    $need_update=false; 
  }
  
  if ($need_update==false) {
    $return['message']='<div class="alert alert-success" role="alert">'.gks_lang('Έχετε ήδη την νεότερη έκδοση').'</div>';
    $return['newversion']=false;
  } else {
    $total_bytes=0;
    foreach ($data as $key => $value) {
      if (substr($key,0,8)=='filesize') {
        $total_bytes+=$value;
      }
    } 
    $message=gks_lang('Έχετε την έκδοση [1]<br>Κάντε αναβάθμιση στην έκδοση <b>[2]</b><br>Ημερομηνία δημοσίευσης: [3]<br>Μέγεθος αρχείων: [4] MB');
    $message=str_replace('[1]',$curr_version,$message);
    $message=str_replace('[2]',$live_version,$message);
    $message=str_replace('[3]',showDate(strtotime($data['Date']),'d/m/Y H:i',1),$message);
    $message=str_replace('[4]',number_format($total_bytes/1024/1024, 2, ',', '.'),$message);
    $return['message']='<div class="alert alert-warning " role="alert">'.$message.'</div>';
    
    $return['newversion']=true;
  }
  $return['success']=true;
  $return['message']=base64_encode($return['message']);
  echo json_encode($return); die();
}
if ($cmd=='downloadfiles') {
  
  $url="https://tools.gks.gr/gks_erp/latest.json?time=".time().rand(1000,9999).rand(1000,9999);
  $data_raw=file_get_contents($url);
  if ($data_raw!='') @file_put_contents($save_path.'/latest.json',$data_raw);  

  if (file_exists($save_path.'/latest.json')==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν βρέθηκε το αρχείο').'<br>'.$save_path.'/latest.json';
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

  $data=json_decode(file_get_contents($save_path.'/latest.json'),true);
    
  if (abs(time()-filemtime($save_path.'/latest.json'))>10*60) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο latest.json είναι παλιό').'<br>'.gks_lang('Κάντε την διαδικασία από την αρχή');
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

  set_time_limit(600);
  
  /////////////
  $save_file=$save_path.'/gks_erp.zip';
  if (file_exists($save_file)) @unlink($save_file);
  if (file_exists($save_file)) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η διαγραφή του αρχείου').':<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $url=$data['url'];
  $fp = fopen ($save_file, 'w+');
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 600);
  curl_setopt($ch, CURLOPT_FILE, $fp); 
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_exec($ch);
  curl_close($ch);
  fclose($fp);
  if (filesize($save_file)!=$data['filesize']) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο που λήφθηκε είναι κατεστραμμένο').'<br>'.gks_lang('Δοκιμάστε ξανά').'<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

  /////////////
  $save_file=$save_path.'/gks_erp_img_site.zip';
  if (file_exists($save_file)) @unlink($save_file);
  if (file_exists($save_file)) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η διαγραφή του αρχείου').':<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $url=$data['url_img_site'];
  $fp = fopen ($save_file, 'w+');
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 600);
  curl_setopt($ch, CURLOPT_FILE, $fp); 
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_exec($ch); 
  curl_close($ch);
  fclose($fp);
  if (filesize($save_file)!=$data['filesize_img_site']) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο που λήφθηκε είναι κατεστραμμένο').'<br>'.gks_lang('Δοκιμάστε ξανά').'<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

  /////////////
  $save_file=$save_path.'/gks_erp_theme.zip';
  if (file_exists($save_file)) @unlink($save_file);
  if (file_exists($save_file)) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η διαγραφή του αρχείου').':<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $url=$data['url_theme'];
  $fp = fopen ($save_file, 'w+');
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 600);
  curl_setopt($ch, CURLOPT_FILE, $fp); 
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_exec($ch); 
  curl_close($ch);
  fclose($fp);
  if (filesize($save_file)!=$data['filesize_theme']) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο που λήφθηκε είναι κατεστραμμένο').'<br>'.gks_lang('Δοκιμάστε ξανά').'<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

  /////////////
  $save_file=$save_path.'/gks_core.zip';
  if (file_exists($save_file)) @unlink($save_file);
  if (file_exists($save_file)) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η διαγραφή του αρχείου').':<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $url=$data['url_gks_core'];
  $fp = fopen ($save_file, 'w+');
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 600);
  curl_setopt($ch, CURLOPT_FILE, $fp); 
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_exec($ch); 
  curl_close($ch);
  fclose($fp);
  if (filesize($save_file)!=$data['filesize_gks_core']) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο που λήφθηκε είναι κατεστραμμένο').'<br>'.gks_lang('Δοκιμάστε ξανά').'<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  
  /////////////
  $save_file=$save_path.'/gks_erp_mu_plugins.zip';
  if (file_exists($save_file)) @unlink($save_file);
  if (file_exists($save_file)) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η διαγραφή του αρχείου').':<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $url=$data['url_mu_plugins'];
  $fp = fopen ($save_file, 'w+');
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 600);
  curl_setopt($ch, CURLOPT_FILE, $fp); 
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_exec($ch); 
  curl_close($ch);
  fclose($fp);
  if (filesize($save_file)!=$data['filesize_mu_plugins']) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο που λήφθηκε είναι κατεστραμμένο').'<br>'.gks_lang('Δοκιμάστε ξανά').'<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  
  /////////////
  $save_file=$save_path.'/gks_erp_maxmind.zip';
  if (file_exists($save_file)) @unlink($save_file);
  if (file_exists($save_file)) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η διαγραφή του αρχείου').':<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $url=$data['url_maxmind'];
  $fp = fopen ($save_file, 'w+');
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 600);
  curl_setopt($ch, CURLOPT_FILE, $fp); 
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_exec($ch); 
  curl_close($ch);
  fclose($fp);
  if (filesize($save_file)!=$data['filesize_maxmind']) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο που λήφθηκε είναι κατεστραμμένο').'<br>'.gks_lang('Δοκιμάστε ξανά').'<br>'.$save_file;
    debug_mail(false,$return['message'],$data_raw);
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
    
  $return['message']='<div class="alert alert-success" role="alert">'.gks_lang('Η λήψη των αρχείων έγινε επιτυχώς').'</div>';
  $return['success']=true;
  $return['message']=base64_encode($return['message']);
  echo json_encode($return); die();
}


if ($cmd=='unzip') {
  $save_file_json=$save_path.'/latest.json';
  
  $save_file_erp=$save_path.'/gks_erp.zip';
  if (file_exists($save_file_erp)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο δεν βρέθηκε').':<br>'.$save_file_erp;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $save_file_img_site=$save_path.'/gks_erp_img_site.zip';
  if (file_exists($save_file_img_site)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο δεν βρέθηκε').':<br>'.$save_file_img_site;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $save_file_theme=$save_path.'/gks_erp_theme.zip';
  if (file_exists($save_file_theme)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο δεν βρέθηκε').':<br>'.$save_file_theme;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $save_file_gks_core=$save_path.'/gks_core.zip';
  if (file_exists($save_file_gks_core)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο δεν βρέθηκε').':<br>'.$save_file_gks_core;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $save_file_mu_plugins=$save_path.'/gks_erp_mu_plugins.zip';
  if (file_exists($save_file_mu_plugins)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο δεν βρέθηκε').':<br>'.$save_file_mu_plugins;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $save_file_maxmind=$save_path.'/gks_erp_maxmind.zip';
  if (file_exists($save_file_maxmind)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Το αρχείο δεν βρέθηκε').':<br>'.$save_file_maxmind;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}




  $path_theme=get_theme_root();
  if (file_exists($path_theme)==false) @mkdir($path_theme,0755);
  if (file_exists($path_theme)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η δημιουργία του φακέλου').':<br>'.$path_theme;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  if (is_writeable($path_theme)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Ο φάκελος δεν είναι εγγράψιμος').':<br>'.$path_theme;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

    
  $path_plugin=WP_PLUGIN_DIR;
  if (file_exists($path_plugin)==false) @mkdir($path_plugin,0755);
  if (file_exists($path_plugin)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η δημιουργία του φακέλου').':<br>'.$path_plugin;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  if (is_writeable($path_plugin)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Ο φάκελος δεν είναι εγγράψιμος').':<br>'.$path_plugin;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}


  $path_mu_plugin=WPMU_PLUGIN_DIR;
  if (file_exists($path_mu_plugin)==false) @mkdir($path_mu_plugin,0755);
  if (file_exists($path_mu_plugin)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η δημιουργία του φακέλου').':<br>'.$path_mu_plugin;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  if (is_writeable($path_mu_plugin)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Ο φάκελος δεν είναι εγγράψιμος').':<br>'.$path_mu_plugin;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

  
  //if (1==2) {//
  ////////
  $root_dir=$_SERVER['DOCUMENT_ROOT'];
  $pre_root_dir=dirname($root_dir);
  $erp_path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my';
  if (file_exists($erp_path)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν βρέθηκε ο φάκελος').':<br>'.$erp_path;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  if (is_writable($erp_path)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Ο φάκελος δεν είναι εγγράψιμος').':<br>'.$erp_path;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}


  $zip = new ZipArchive;
  $res = $zip->open($save_file_erp);
  if ($res === TRUE) {
    $rese=$zip->extractTo($erp_path);
    for ($i = 0; $i < $zip->numFiles; $i++) {
      $stat = $zip->statIndex($i);
      $filename = $erp_path.'/'.$stat['name'];
      if (file_exists($filename)) @touch($filename, $stat['mtime']);
    }
    $zip->close();
  } 
  if ($res !== TRUE or $rese!== TRUE) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η αποσυμπίεση του αρχείου').':<br>'.$save_file_erp.' σε '.$erp_path;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

  if (file_exists($erp_path.'/gks_cache_version.php')==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Η αποσυμπίεση δεν ολοκληρώθηκε με επιτυχία').'<br>File: '.$save_file_erp.' to '.$erp_path;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

  @unlink($erp_path.'/_current/mysql.sql');

  ////////
  $path_theme2=$path_theme.'/gks_erp_theme';
  if (file_exists($path_theme2)) gks_erp_installer_delete_dir($path_theme2);
  if (file_exists($path_theme2)){
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η διαγραφή του φακέλου').':<br>'.$path_theme2;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  $zip = new ZipArchive;
  $res = $zip->open($save_file_theme);
  if ($res === TRUE) {
    $rese=$zip->extractTo($path_theme);
    for ($i = 0; $i < $zip->numFiles; $i++) {
      $stat = $zip->statIndex($i);
      $filename = $path_theme.'/'.$stat['name'];
      if (file_exists($filename)) @touch($filename, $stat['mtime']);
    }    
    $zip->close();
  } 
  if ($res !== TRUE or $rese!== TRUE) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η αποσυμπίεση του αρχείου').':<br>'.$save_file_theme.' σε '.$path_theme;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}


  ////////
  $path_plugin2=$path_plugin.'/gks_core';
  //if (file_exists($path_plugin2)) gks_erp_installer_delete_dir($path_plugin2);
  //if (file_exists($path_plugin2)){
  //  $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η διαγραφή του φακέλου').':<br>'.$path_plugin2;
  //  debug_mail(false,$return['message'],'');
  //  $return['message']=base64_encode($return['message']);echo json_encode($return); die();}

  $zip = new ZipArchive;
  $res = $zip->open($save_file_gks_core);
  if ($res === TRUE) {
    $rese=$zip->extractTo($path_plugin);
    for ($i = 0; $i < $zip->numFiles; $i++) {
      $stat = $zip->statIndex($i);
      $filename = $path_plugin.'/'.$stat['name'];
      if (file_exists($filename)) @touch($filename, $stat['mtime']);
    }     
    $zip->close();
  } 
  if ($res !== TRUE or $rese!== TRUE) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η αποσυμπίεση του αρχείου').':<br>'.$save_file_gks_core.' σε '.$path_plugin;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  
//$return = array('success' => false, 'message' => base64_encode('aaaaaaaaaaa'));echo json_encode($return); die();
      

  ////////
  $zip = new ZipArchive;
  $res = $zip->open($save_file_mu_plugins);
  if ($res === TRUE) {
    $rese=$zip->extractTo($path_mu_plugin);
    for ($i = 0; $i < $zip->numFiles; $i++) {
      $stat = $zip->statIndex($i);
      $filename = $path_mu_plugin.'/'.$stat['name'];
      if (file_exists($filename)) @touch($filename, $stat['mtime']);
    }     
    $zip->close();
  } 
  if ($res !== TRUE or $rese!== TRUE) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η αποσυμπίεση του αρχείου').':<br>'.$save_file_mu_plugins.' σε '.$path_mu_plugin;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  
  
  ////////
  $root_dir=$_SERVER['DOCUMENT_ROOT'];
  $maxmind_dir=dirname($root_dir).'/gks_erp_maxmind/';    
  if (file_exists($maxmind_dir)==false) @mkdir($maxmind_dir,0755);  
  if (file_exists($maxmind_dir)==false) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η δημιουργία του φακέλου').':<br>'.$maxmind_dir;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  
  $zip = new ZipArchive;
  $res = $zip->open($save_file_maxmind);
  if ($res === TRUE) {
    $rese=$zip->extractTo($maxmind_dir);
    for ($i = 0; $i < $zip->numFiles; $i++) {
      $stat = $zip->statIndex($i);
      $filename = $maxmind_dir.'/'.$stat['name'];
      if (file_exists($filename)) @touch($filename, $stat['mtime']);
    }      
    $zip->close();
  } 
  if ($res !== TRUE or $rese!== TRUE) {
    $return['message']=gks_lang('Σφάλμα').' '.gks_lang('Δεν είναι δυνατή η αποσυμπίεση του αρχείου').':<br>'.$save_file_maxmind.' σε '.$maxmind_dir;
    debug_mail(false,$return['message'],'');
    $return['message']=base64_encode($return['message']);echo json_encode($return); die();}
  
  //die('kkkkkkkkkkk');
  $data=json_decode(file_get_contents($save_path.'/latest.json'),true);
  if (isset($data['delete_files']) and is_array($data['delete_files'])) {
    foreach ($data['delete_files'] as $value) {
      $value=str_replace('[GKS_SITE_PATH]',GKS_SITE_PATH,$value);
      $value=str_replace('[GKS_SITE_HTTPDOCS]',GKS_SITE_HTTPDOCS,$value);
      $value=str_replace('[GKS_FileServerShare]',GKS_FileServerShare,$value);
      $value=str_replace('[GKS_DATA]',GKS_DATA,$value);
      $value=str_replace('[GKS_CACHE]',GKS_CACHE,$value);
      if (is_file($value)) {
        @unlink($value);
      } else if (is_dir($value)) {
        @rmdir($value);
      }
    } 
  }
    
  ////delete files
  @unlink($save_file_json);
  @unlink($save_file_erp);
  @unlink($save_file_img_site);
  @unlink($save_file_theme);
  @unlink($save_file_gks_core);
  @unlink($save_file_mu_plugins);
  @unlink($save_file_maxmind);
  


  $return['message']='<div class="alert alert-success" role="alert">'.gks_lang('Η αποσυμπίεση και η αντιγραφή των αρχείων ήταν επιτυχής').'</div>';
  $return['success']=true;
  $return['message']=base64_encode($return['message']);
  echo json_encode($return); die();
  
  
}


$return = array('success' => false, 'message' => base64_encode(gks_lang('Λάθος εντολή')));echo json_encode($return); die();


function gks_erp_installer_delete_dir(string $dir): void {
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
    rmdir($dir);
}