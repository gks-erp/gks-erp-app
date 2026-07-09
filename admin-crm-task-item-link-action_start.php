<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

ini_set('max_execution_time', 60000);
set_time_limit(60000);


//debug_mail(false,'admin-crm-task-item-link-action_start.php','');
//die();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  debug_mail(false,'admin-crm-task-item-link-action_start.php the id is not set','');
  die();}


$my_page_title=gks_lang('Έναρξη λήψης αρχείου εργασίας').' '.$id;
db_open();
stat_record();





$sql="select * from gks_crm_tasks_links where id_crm_tasks_links=".$id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
  
if ($result->num_rows <= 0) {  
  debug_mail(false,'admin-crm-task-item-link-action_start.php record not found',$sql);
  die();}

$row=$result->fetch_assoc();
$crm_task_id=$row['crm_task_id'];
$url=encodeURI($row['url']);
$url_db=$row['url'];


//if (startwith(strtolower($url),'https://drive.google.com/')) {
//  $url.='&export=download';
//}
//echo $url;
//die();

if ($row['download_status']!=0) {
  if ($row['download_status']!=2) { // einai idi OK
    $sql="update gks_crm_tasks_links set download_end=now(), 
    download_message='".$db_link->escape_string(gks_lang('Η κατάσταση του συνδέσμου δεν είναι η σωστή'))." 
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();} 
  } 
  debug_mail(false,'admin-crm-task-item-link-action_start.php download_status='.$row['download_status'].' not 0', $sql);
  die();}

if ($url=='' or filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
  $sql="update gks_crm_tasks_links set download_end=now(), 
  download_message='".$db_link->escape_string(gks_lang('Το url δεν είναι σωστό'))."' 
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
  debug_mail(false,'admin-crm-task-item-link-action_start.php url is valid',$url);
  die();} 


$sql="update gks_crm_tasks_links set 
download_status=1, 
download_start=now(),
download_end=null, 
download_message='".$db_link->escape_string(gks_lang('Έναρξη λήψης').' ...')."',
download_pososto=0,
download_size_until_now=0,
download_size_total=0
where id_crm_tasks_links=".$id;

$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);die();}  



if (file_exists(GKS_FileServerShare) == false) {
  $sql="update gks_crm_tasks_links set html_tds=null,download_end=now(), 
  download_message='".$db_link->escape_string(gks_lang('Δεν βρέθηκε ο φάκελος για την αποθήκευση του αρχείου'))."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
  debug_mail(false,'admin-crm-task-item-link-action_start.php url is valid',$url);
  die();} 


$upload_dir = GKS_FileServerShare.'crm/task/'.$crm_task_id.'/';

if (file_exists($upload_dir) == false) {
  if (@mkdir($upload_dir , 0777, true) == false ) {
    $sql="update gks_crm_tasks_links set html_tds=null,download_end=now(), 
    download_message='". $db_link->escape_string(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').' ../@crm/task/'.$crm_task_id)."' 
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'admin-crm-task-item-link-action_start.php can not create dir: ',$upload_dir);
    die(); 
  }
}


if (startwith(strtolower($url),'https://mega.nz/') and strlen($url)>=17) {
  

  $upload_dir2=$upload_dir.'Mega'.showDate(time(), 'Y-m-d_H.i.s',1);
  if (file_exists($upload_dir2)) $upload_dir2.='_'.rand(1000,9999);
  $upload_dir2.='/';
  if (@mkdir($upload_dir2 , 0777, true) == false ) {
    $sql="update gks_crm_tasks_links set html_tds=null,download_end=now(), 
    download_message='". $db_link->escape_string(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').' '.$upload_dir2)."' 
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'admin-crm-task-item-link-action_start.php can not create dir: ',$upload_dir2);
    die(); 
  }

  $relative_path=substr($upload_dir2, strlen($upload_dir));
  $sql="update gks_crm_tasks_links set 
  relative_path='".$db_link->escape_string($relative_path)."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
  db_close();

  
  $myexec=$upload_dir2.'run.sh';
  $mybash='#!/bin/bash'."\n".
  'cd '.$upload_dir2."\n".
  'mega-get '.str_replace('!', '\!', $url).' '.$upload_dir2."\n".
  'chmod -R 0777 '.$upload_dir2."\n".
  'rm '.$myexec;
  
  
  
  file_put_contents($myexec, $mybash);
  chmod($myexec, 0777);
  
  //debug_mail(false,'exec0',$mybash);
  //echo time().$mybash;
  
  $ret=exec($myexec.' 2>&1', $output, $return_var);
  
  //var_dump($output);
  //var_dump($return_var);
  
  //debug_mail(false,'exec1',print_r($output,true));
  //debug_mail(false,'exec2',print_r($return_var,true));
  
  if (file_exists($myexec)) unlink($myexec);
  //exec ("find ".$upload_dir2." -type d -exec chmod 0777 {} +");
//  $ret=exec('chmod -R 0777 '.$upload_dir2.' 2>&1',$output2, $return_var2);
//  
//  debug_mail(false,'exec3',print_r($output2,true));
//  debug_mail(false,'exec4',print_r($return_var2,true));
  
  $download_message='';
  
  $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_dir2));
  $myfilesize=0;
  $zip_files=array();
  $rar_files=array();
  foreach ($rii as $file) {
    if ($file->isDir()){ 
      continue;
    }
    $myfilesize+=filesize($file->getPathname());
    if (endwith(strtolower($file->getPathname()),'.zip')) $zip_files[]= $file->getPathname();
    if (endwith(strtolower($file->getPathname()),'.rar')) $rar_files[]= $file->getPathname();
  }
  if ($myfilesize==0)     $download_message=gks_lang('Δεν βρέθηκαν αρχεία').'<br>'.gks_lang('Πιθανόν να έχει λήξει ο σύνδεσμος');

  db_open();    
    
  if ($download_message!='') {

    $sql="update gks_crm_tasks_links set 
    download_status=3, 
    download_end=now(), 
    download_message='".$db_link->escape_string($download_message)."',
    relative_path='',
    download_size_total=0,
    download_size_until_now=0,
    html_tds=null
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'error download file',$sql);
    die();
      
  }
  
  
  
  foreach ($zip_files as &$value) {
    $value=substr($value, strlen($upload_dir2));
  } 
  unset($value);
  foreach ($rar_files as &$value) {
    $value=substr($value, strlen($upload_dir2));
  } 
  unset($value);
  //debug_mail(false,'zip files',print_r($zip_files,true));
  
  foreach ($zip_files as $value) {
    $file_path=$upload_dir2.$value;
    $zip = new ZipArchive;
    $res = $zip->open($file_path);
    if ($res === TRUE) {
      //echo ' 22222222';
      $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
      //$foldername=substr($foldername,0, strlen($foldername));
      
      $extract_path=dirname($file_path).'/'.$foldername;
      if (file_exists($extract_path)) {
        do {
          $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
          if (file_exists($extract_path)==false) break;
        } while (true);
      }
      
      for( $zipindexfile = 0 ; $zipindexfile < $zip->numFiles ; $zipindexfile++ ) {
          $zip_filename=$zip->getNameIndex($zipindexfile);
          if ( $zip_filename != '/' && $zip_filename != '__MACOSX/_' && $zip_filename != '__MACOSX' && strpos($zip_filename, '__MACOSX') === false) {
              //print $zip->getNameIndex( $zipindexfile ) . '<br>';
              $zip->extractTo( $extract_path, array($zip->getNameIndex($zipindexfile)) );
          }
      }
      //$zip->extractTo($extract_path);
      $zip->close();
      //unlink($file_path);
    } else {
      debug_mail(false,'can not open zip file',$file_path);
    }
  }

  foreach ($rar_files as $value) {
    $file_path=$upload_dir2.$value;
    $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
    //$foldername=substr($foldername,0, strlen($foldername));
    
    $extract_path=dirname($file_path).'/'.$foldername;
    if (file_exists($extract_path)) {
      do {
        $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
        if (file_exists($extract_path)==false) break;
      } while (true);
    }

        
    $archive = RarArchive::open($file_path);
    $entries = $archive->getEntries();
    foreach ($entries as $entry) {
        $entry->extract($extract_path);
    }
    $archive->close(); 
    
  }
    
  //$gks_fileserver_item_relative_path=array();
  $gks_fileserver_item_scandir_rec_echo='';
  $gks_fileserver_item_show_print=array();
  $gks_fileserver_item_relative_path=array();
  $gks_fileserver_item_relative_path_keys=array();
  gks_fileserver_item_scandir_rec($upload_dir2,1);
  $myjson=json_encode($gks_fileserver_item_relative_path_keys);  
  
  
  $sql="update gks_crm_tasks_links set 
  download_status=2, 
  download_end=now(), 
  download_message='OK',
  relative_path='".$db_link->escape_string($relative_path)."',
  download_size_total=".$myfilesize.",
  download_size_until_now=".$myfilesize.",
  download_pososto=100,
  html_tds='".$db_link->escape_string($myjson)."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
  
  $myexec=$upload_dir2.'gks_chmod.sh';
  $mybash='#!/bin/bash'."\n".
  'chmod -R 0777 '.$upload_dir2;
  file_put_contents($myexec, $mybash);
  chmod($myexec, 0777);  
  $ret=exec($myexec.' 2>&1', $output, $return_var);
  if (file_exists($myexec)) unlink($myexec);

} else if ($GKS_SEND_ANYWHERE_API_KEY!='' and (startwith(strtolower($url),'http://sendanywhe.re/') or startwith(strtolower($url),'https://sendanywhe.re/')) and strlen($url)==29) {
  
  $send_anywhere_id=substr($url, 21);
  debug_mail(false,'send_anywhere_id: ',$send_anywhere_id);

  $upload_dir2=$upload_dir.'SendAnywhere'.showDate(time(), 'Y-m-d_H.i.s',1);
  if (file_exists($upload_dir2)) $upload_dir2.='_'.rand(1000,9999);
  $upload_dir2.='/';
  if (@mkdir($upload_dir2 , 0777, true) == false ) {
    $sql="update gks_crm_tasks_links set html_tds=null,download_end=now(), 
    download_message='". $db_link->escape_string(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').' '.$upload_dir2)."' 
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'admin-crm-task-item-link-action_start.php can not create dir: ',$upload_dir2);
    die(); 
  }
  $upload_dir2_org=$upload_dir2;
  
  $relative_path=substr($upload_dir2, strlen($upload_dir));
  $sql="update gks_crm_tasks_links set 
  relative_path='".$db_link->escape_string($relative_path)."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
  
  $url_1='https://send-anywhere.com/web/v1/device?api_key='.$GKS_SEND_ANYWHERE_API_KEY.'&profile_name=gks';
  $ch = curl_init($url_1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  $result = curl_exec($ch);
  curl_close($ch);
  // get cookie
  // multi-cookie variant contributed by @Combuster in comments
  preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
  $cookies = array();
  foreach($matches[1] as $item) {
      parse_str($item, $cookie);
      $cookies = array_merge($cookies, $cookie);
  }
  debug_mail(false,'send_anywhere cookies: ',print_r($cookies,true));
 
  $cookie_string="";
  foreach( $cookies as $key => $value ) {
    $cookie_string .= "$key=$value;";
  }
  debug_mail(false,'send_anywhere cookies string: ',$cookie_string);


  $url_2='https://send-anywhere.com/web/v1/key/'.$send_anywhere_id;

  $ch = curl_init($url_2);
  curl_setopt($ch,CURLOPT_COOKIE, $cookie_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_HEADER, 1);
  $result = curl_exec($ch);
  curl_close($ch);  
 
  //array(7) { ["key"]=> string(8) "YN7C3SEN" ["device_id"]=> string(13) "7958495711157" ["mode"]=> string(6) "upload" ["expires_time"]=> int(1590917873) ["weblink"]=> string(140) "https://file-159-89-0-105.send-anywhere.com/api/webfile/YN7C3SEN?device_key=3e75e3a5817e6d8f579b15418f41ca1025b4dc57a6d5e821e7660a97ff3f275e" ["file_size"]=> int(233701699) ["file_count"]=> int(17) }
  $result=json_decode($result, true);
  debug_mail(false,'send_anywhere result: ',print_r($result,true));
  if (is_array($result) == false or isset($result['weblink'])==false or isset($result['file_size']) == false) {
    
    $download_message=gks_lang('Δεν βρέθηκε ο άμεσος σύνδεσμος λήψης').'<br>'.gks_lang('Πιθανόν να έχει λήξει ο σύνδεσμος');
    
    $sql="update gks_crm_tasks_links set 
    download_status=3, 
    download_end=now(), 
    download_message='".$db_link->escape_string($download_message)."',
    relative_path='',
    download_size_total=0,
    download_size_until_now=0,
    html_tds=null
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'error download file',$sql);
    die();
    
  }
  $file_size=intval($result['file_size']);
  
  
  $headerBuff = $upload_dir2.'_'.$id.'_header_'.rand(10000,99999).'.txt';
  $headerBuff_obj = fopen($headerBuff, 'w+');
  $targetFile = $upload_dir2.'_'.$id.'_download_'.rand(10000,99999).'.tmp';
  $targetFile_obj = fopen( $targetFile, 'w' );
  $Disposition_filename='';  
  
  $ch = curl_init( $result['weblink'] );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt( $ch, CURLOPT_WRITEHEADER, $headerBuff_obj);
  //curl_setopt( $ch, CURLOPT_HEADERFUNCTION, 'curlHeaderCallback');
  curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
  curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback' );
  curl_setopt( $ch, CURLOPT_FILE, $targetFile_obj );
  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
  //curl_exec( $ch );
  $curl_result='';
  $download_message='';
  if(curl_exec($ch) === false) {
    $curl_result= gks_lang('Σφάλμα').': ' . curl_error($ch);
  } else {
    rewind($headerBuff_obj);
    $headers = stream_get_contents($headerBuff_obj);
    //print_r($headers);
    //if(preg_match('/Content-Disposition: .*filename=([^ ]+)/', $headers, $matches)) {
    if ( preg_match('~filename=(?|"([^"]*)"|\'([^\']*)\'|([^;]*))~', $headers, $matches)) {
      //print_r($matches);
      if (isset($matches[1])) {
        $Disposition_filename=$matches[1];
        if (strpos($Disposition_filename, "\r\n") !== false) {
          $pp=explode("\r\n",$Disposition_filename);
          $Disposition_filename=$pp[0];
        }
      }
    }
    
    $curl_result= '';
  }
  curl_close($ch);
  fclose( $targetFile_obj );
  fclose( $headerBuff_obj );

 
  if ($download_message=='') $download_message=$curl_result;

  if (file_exists($headerBuff)) {
    unlink($headerBuff);
  }
  if ($download_message=='' and file_exists($targetFile) == false) 
    $download_message=gks_lang('Δεν έγινε λήψη του αρχείου');




  if ($download_message!='') {
    if (file_exists($targetFile)) {
      unlink($targetFile);
    }
    $sql="update gks_crm_tasks_links set 
    download_status=3, 
    download_end=now(), 
    download_message='".$db_link->escape_string($download_message)."',
    relative_path='',
    download_size_total=0,
    download_size_until_now=0,
    html_tds=null
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'error download file',$sql);
    die();
      
  }
  
  
  
  $file_name=$send_anywhere_id.'tmp';
  if ($Disposition_filename!='') $file_name=$Disposition_filename;
  
  $file_parts = pathinfo($file_name);
  $relative_path=$file_name;
  $targetFile_final=$upload_dir2.$file_name;
  if (file_exists($targetFile_final)) {
    do {
      $relative_path=$file_parts['filename'].'_'.rand(10000,99999).'.'.$file_parts['extension'];
      $targetFile_final=$upload_dir2.$relative_path;
      if (file_exists($targetFile_final)==false) break;
    } while (true);
  }
  
  //copy ($targetFile,$targetFile_final.'ggg');
  rename($targetFile,$targetFile_final);
  
  $myfilesize=filesize($targetFile_final);

  $upload_dir2='';
  
  if (endwith(strtolower($targetFile_final),'.zip')) {
    $file_path=$targetFile_final;
    $zip = new ZipArchive;
    $res = $zip->open($file_path);
    if ($res === TRUE) {
      //echo ' 22222222';
      $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
      //$foldername=substr($foldername,0, strlen($foldername));
      
      $extract_path=dirname($file_path).'/'.$foldername;
      if (file_exists($extract_path)) {
        do {
          $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
          if (file_exists($extract_path)==false) break;
        } while (true);
      }
      
      for( $zipindexfile = 0 ; $zipindexfile < $zip->numFiles ; $zipindexfile++ ) {
          $zip_filename=$zip->getNameIndex($zipindexfile);
          if ( $zip_filename != '/' && $zip_filename != '__MACOSX/_' && $zip_filename != '__MACOSX' && strpos($zip_filename, '__MACOSX') === false) {
              //print $zip->getNameIndex( $zipindexfile ) . '<br>';
              $zip->extractTo( $extract_path, array($zip->getNameIndex($zipindexfile)) );
          }
      }
      //$zip->extractTo($extract_path);
      $zip->close();
      //unlink($file_path);
      $upload_dir2=$extract_path.'/';
    } else {
      debug_mail(false,'can not open zip file',$file_path);
    }
  }
  if (endwith(strtolower($targetFile_final),'.rar')) {
    
    $file_path=$targetFile_final;
    $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
    //$foldername=substr($foldername,0, strlen($foldername));
    
    $extract_path=dirname($file_path).'/'.$foldername;
    if (file_exists($extract_path)) {
      do {
        $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
        if (file_exists($extract_path)==false) break;
      } while (true);
    }

        
    $archive = RarArchive::open($file_path);
    $entries = $archive->getEntries();
    foreach ($entries as $entry) {
        $entry->extract($extract_path);
    }
    $archive->close();   
    $upload_dir2=$extract_path.'/';
  }
  
  //debug_mail(false,'extract_path',$extract_path);
  
  $gks_fileserver_item_scandir_rec_echo='';
  $gks_fileserver_item_show_print=array();
  $gks_fileserver_item_relative_path=array();
  $gks_fileserver_item_relative_path_keys=array();
  gks_fileserver_item_scandir_rec(($upload_dir2=='' ? dirname($targetFile_final).'/' : $upload_dir2) ,1, ($upload_dir2=='' ? $targetFile_final : ''));
  $myjson=json_encode($gks_fileserver_item_relative_path_keys); 



  //relative_path='".$db_link->escape_string($relative_path)."',
  $sql="update gks_crm_tasks_links set 
  download_status=2, 
  download_end=now(), 
  download_message='OK',
  download_size_total=".$myfilesize.",
  download_size_until_now=".$myfilesize.",
  download_pososto=100,
  html_tds='".$db_link->escape_string($myjson)."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
 
 
  $myexec=$upload_dir2_org.'gks_chmod.sh';
  $mybash='#!/bin/bash'."\n".
  'chmod -R 0777 '.$upload_dir2_org;
  file_put_contents($myexec, $mybash);
  chmod($myexec, 0777);  
  $ret=exec($myexec.' 2>&1', $output, $return_var);
  if (file_exists($myexec)) unlink($myexec);
  
    
} else if (startwith(strtolower($url),'https://www.dropbox.com/') and endwith(strtolower($url),'?dl=0') and strlen($url)>=25) {
  $url=substr($url, 0, strlen($url) -1).'1';

  $upload_dir2=$upload_dir.'DropBox'.showDate(time(), 'Y-m-d_H.i.s',1);
  if (file_exists($upload_dir2)) $upload_dir2.='_'.rand(1000,9999);
  $upload_dir2.='/';
  if (@mkdir($upload_dir2 , 0777, true) == false ) {
    $sql="update gks_crm_tasks_links set html_tds=null,download_end=now(), 
    download_message='". $db_link->escape_string(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').' '.$upload_dir2)."' 
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'admin-crm-task-item-link-action_start.php can not create dir: ',$upload_dir2);
    die(); 
  }

  $relative_path=substr($upload_dir2, strlen($upload_dir));
  $sql="update gks_crm_tasks_links set 
  relative_path='".$db_link->escape_string($relative_path)."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
  db_close();
  
  $mybash='#!/bin/bash'."\n".
  'cd '.$upload_dir2."\n".
  'wget --content-disposition '.$url;
  
  $myexec=$upload_dir2.'run.sh';
  file_put_contents($myexec, $mybash);
  chmod($myexec, 0777);

  $ret=exec($myexec.' 2>&1', $output, $return_var);
  
  //debug_mail(false,'exec1',print_r($output,true));
  //debug_mail(false,'exec2',print_r($return_var,true));
  
  unlink($myexec);

  

  $download_message='';
  
  $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_dir2));
  $myfilesize=0;
  $zip_files=array();
  $rar_files=array();
  foreach ($rii as $file) {
    if ($file->isDir()){ 
      continue;
    }
    $myfilesize+=filesize($file->getPathname());
    if (endwith(strtolower($file->getPathname()),'.zip')) $zip_files[]= $file->getPathname();
    if (endwith(strtolower($file->getPathname()),'.rar')) $rar_files[]= $file->getPathname();
  }
  if ($myfilesize==0) 
    $download_message=gks_lang('Δεν βρέθηκαν αρχεία').'<br>'.gks_lang('Πιθανόν να έχει λήξει ο σύνδεσμος');

  db_open();
    
  if ($download_message!='') {

    $sql="update gks_crm_tasks_links set 
    download_status=3, 
    download_end=now(), 
    download_message='".$db_link->escape_string($download_message)."',
    relative_path='',
    download_size_total=0,
    download_size_until_now=0,
    html_tds=null
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'error download file',$sql);
    die();
      
  }
  
  
  
  foreach ($zip_files as &$value) {
    $value=substr($value, strlen($upload_dir2));
  } 
  unset($value);
  foreach ($rar_files as &$value) {
    $value=substr($value, strlen($upload_dir2));
  } 
  unset($value);
  //debug_mail(false,'zip files',print_r($zip_files,true));
  
  foreach ($zip_files as $value) {
    $file_path=$upload_dir2.$value;
    $zip = new ZipArchive;
    $res = $zip->open($file_path);
    if ($res === TRUE) {
      //echo ' 22222222';
      $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
      //$foldername=substr($foldername,0, strlen($foldername));
      
      $extract_path=dirname($file_path).'/'.$foldername;
      if (file_exists($extract_path)) {
        do {
          $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
          if (file_exists($extract_path)==false) break;
        } while (true);
      }
      
      for( $zipindexfile = 0 ; $zipindexfile < $zip->numFiles ; $zipindexfile++ ) {
          $zip_filename=$zip->getNameIndex($zipindexfile);
          if ( $zip_filename != '/' && $zip_filename != '__MACOSX/_' && $zip_filename != '__MACOSX' && strpos($zip_filename, '__MACOSX') === false) {
              //print $zip->getNameIndex( $zipindexfile ) . '<br>';
              $zip->extractTo( $extract_path, array($zip->getNameIndex($zipindexfile)) );
          }
      }
      //$zip->extractTo($extract_path);
      $zip->close();
      //unlink($file_path);
    } else {
      debug_mail(false,'can not open zip file',$file_path);
    }
  }

  foreach ($rar_files as $value) {
    $file_path=$upload_dir2.$value;
    $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
    //$foldername=substr($foldername,0, strlen($foldername));
    
    $extract_path=dirname($file_path).'/'.$foldername;
    if (file_exists($extract_path)) {
      do {
        $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
        if (file_exists($extract_path)==false) break;
      } while (true);
    }

        
    $archive = RarArchive::open($file_path);
    $entries = $archive->getEntries();
    foreach ($entries as $entry) {
        $entry->extract($extract_path);
    }
    $archive->close(); 
    
  }
    
  //$gks_fileserver_item_relative_path=array();
  $gks_fileserver_item_scandir_rec_echo='';
  $gks_fileserver_item_show_print=array();
  $gks_fileserver_item_relative_path=array();
  $gks_fileserver_item_relative_path_keys=array();
  gks_fileserver_item_scandir_rec($upload_dir2,1);
  $myjson=json_encode($gks_fileserver_item_relative_path_keys);  
  
  
  $sql="update gks_crm_tasks_links set 
  download_status=2, 
  download_end=now(), 
  download_message='OK',
  relative_path='".$db_link->escape_string($relative_path)."',
  download_size_total=".$myfilesize.",
  download_size_until_now=".$myfilesize.",
  download_pososto=100,
  html_tds='".$db_link->escape_string($myjson)."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
  
  $myexec=$upload_dir2.'gks_chmod.sh';
  $mybash='#!/bin/bash'."\n".
  'chmod -R 0777 '.$upload_dir2;
  file_put_contents($myexec, $mybash);
  chmod($myexec, 0777);  
  $ret=exec($myexec.' 2>&1', $output, $return_var);
  if (file_exists($myexec)) unlink($myexec);
  
} else if ((startwith(strtolower($url),'https://we.tl/') and strlen($url)>=15) or 
           (startwith(strtolower($url),'https://wetransfer.com/') and strlen($url)>=24) or 
           (startwith(strtolower($url),'https://') and strpos(strtolower($url), '.wetransfer.com/') !== false)) {  
  
  $upload_dir2=$upload_dir.'WeTransfer_'.showDate(time(), 'Y-m-d_H.i.s',1);
  
  if (file_exists($upload_dir2)) $upload_dir2.='_'.rand(1000,9999);
  $upload_dir2.='/';
  if (@mkdir($upload_dir2 , 0777, true) == false ) {
    $sql="update gks_crm_tasks_links set html_tds=null,download_end=now(), 
    download_message='". $db_link->escape_string(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').' '.$upload_dir2)."' 
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'admin-crm-task-item-link-action_start.php can not create dir: ',$upload_dir2);
    die(); 
  }
  //https://github.com/iamleot/transferwee
  if (copy (GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/utils/transferwee.py', $upload_dir2.'transferwee.py') == false) {
    $sql="update gks_crm_tasks_links set html_tds=null,download_end=now(), 
    download_message='". $db_link->escape_string(gks_lang('Δεν μπορεί να αντιγραφεί το αρχείο').' /my/utils/transferwee.py '.gks_lang('στον φάκελο').' '.$upload_dir2)."' 
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'admin-crm-task-item-link-action_start.php can not copy file transferwee.py in dir: ',$upload_dir2);
    die();    
  }
  $relative_path=substr($upload_dir2, strlen($upload_dir));
  $sql="update gks_crm_tasks_links set 
  relative_path='".$db_link->escape_string($relative_path)."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
  db_close();


  
  $mybash='#!/bin/bash'."\n".
  'cd '.$upload_dir2."\n".
//  'ls > gg1.txt'."\n".
//  'if test ".$1" = ".off" ; then'."\n".
//  '    printf \'\033%%@\''."\n".
//  'else'."\n".
//  '    printf \'\033%%G\''."\n".
//  'fi'."\n".
//  'ls > δδδ.txt'."\n".
  'python3.6 transferwee.py download '.$url;
  
  $myexec=$upload_dir2.'run.sh';
  file_put_contents($myexec, $mybash);
  chmod($myexec, 0777);
  chmod($upload_dir2.'transferwee.py', 0777);
  
  
  //echo $myexec; 
  $ret=exec($myexec.' 2>&1', $output, $return_var);
  
  //debug_mail(false,'exec1',print_r($output,true));
  //debug_mail(false,'exec2',print_r($return_var,true));
  
  unlink($myexec);
  unlink($upload_dir2.'transferwee.py');
  
  
  
  $download_message='';
  
  $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_dir2));
  $myfilesize=0;
  $zip_files=array();
  $rar_files=array();
  foreach ($rii as $file) {
    if ($file->isDir()){ 
      continue;
    }
    $myfilesize+=filesize($file->getPathname());
    if (endwith(strtolower($file->getPathname()),'.zip')) $zip_files[]= $file->getPathname();
    if (endwith(strtolower($file->getPathname()),'.rar')) $rar_files[]= $file->getPathname();
  }
  if ($myfilesize==0) 
    $download_message=gks_lang('Δεν βρέθηκαν αρχεία').'<br>'.gks_lang('Πιθανόν να έχει λήξει ο σύνδεσμος');
  
  
  db_open(); 
  if ($download_message!='') {

    $sql="update gks_crm_tasks_links set 
    download_status=3, 
    download_end=now(), 
    download_message='".$db_link->escape_string($download_message)."',
    relative_path='',
    download_size_total=0,
    download_size_until_now=0,
    html_tds=null
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'error download file',$sql);
    die();
      
  }
  
  
  
  foreach ($zip_files as &$value) {
    $value=substr($value, strlen($upload_dir2));
  } 
  unset($value);
  foreach ($rar_files as &$value) {
    $value=substr($value, strlen($upload_dir2));
  } 
  unset($value);
  //debug_mail(false,'zip files',print_r($zip_files,true));
  
  foreach ($zip_files as $value) {
    $file_path=$upload_dir2.$value;
    $zip = new ZipArchive;
    $res = $zip->open($file_path);
    if ($res === TRUE) {
      //echo ' 22222222';
      $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
      //$foldername=substr($foldername,0, strlen($foldername));
      
      $extract_path=dirname($file_path).'/'.$foldername;
      if (file_exists($extract_path)) {
        do {
          $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
          if (file_exists($extract_path)==false) break;
        } while (true);
      }
      
      for( $zipindexfile = 0 ; $zipindexfile < $zip->numFiles ; $zipindexfile++ ) {
          $zip_filename=$zip->getNameIndex($zipindexfile);
          if ( $zip_filename != '/' && $zip_filename != '__MACOSX/_' && $zip_filename != '__MACOSX' && strpos($zip_filename, '__MACOSX') === false) {
              //print $zip->getNameIndex( $zipindexfile ) . '<br>';
              $zip->extractTo( $extract_path, array($zip->getNameIndex($zipindexfile)) );
          }
      }
      //$zip->extractTo($extract_path);
      $zip->close();
      //unlink($file_path);
    } else {
      debug_mail(false,'can not open zip file',$file_path);
    }
  }

  foreach ($rar_files as $value) {
    $file_path=$upload_dir2.$value;
    $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
    //$foldername=substr($foldername,0, strlen($foldername));
    
    $extract_path=dirname($file_path).'/'.$foldername;
    if (file_exists($extract_path)) {
      do {
        $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
        if (file_exists($extract_path)==false) break;
      } while (true);
    }

        
    $archive = RarArchive::open($file_path);
    $entries = $archive->getEntries();
    foreach ($entries as $entry) {
        $entry->extract($extract_path);
    }
    $archive->close(); 
    
  }
    
  //$gks_fileserver_item_relative_path=array();
  $gks_fileserver_item_scandir_rec_echo='';
  $gks_fileserver_item_show_print=array();
  $gks_fileserver_item_relative_path=array();
  $gks_fileserver_item_relative_path_keys=array();
  gks_fileserver_item_scandir_rec($upload_dir2,1);
  $myjson=json_encode($gks_fileserver_item_relative_path_keys);  
  
  
  $sql="update gks_crm_tasks_links set 
  download_status=2, 
  download_end=now(), 
  download_message='OK',
  relative_path='".$db_link->escape_string($relative_path)."',
  download_size_total=".$myfilesize.",
  download_size_until_now=".$myfilesize.",
  download_pososto=100,
  html_tds='".$db_link->escape_string($myjson)."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  
        
//  echo time();
//  echo $myexec;
//  echo $ret;
//  echo $upload_dir2;
//  die(); 
  
  $myexec=$upload_dir2.'gks_chmod.sh';
  $mybash='#!/bin/bash'."\n".
  'chmod -R 0777 '.$upload_dir2;
  file_put_contents($myexec, $mybash);
  chmod($myexec, 0777);  
  $ret=exec($myexec.' 2>&1', $output, $return_var);
  if (file_exists($myexec)) unlink($myexec);
    
} else { //simple download
  
 
  
  //die();
  
//  echo '<pre>';
//  echo $upload_dir;
//  echo "\n";
//  echo $url;
//  echo "\n";
//  echo $url_db;
//  die();
  
  
  
  $headerBuff = $upload_dir.'_'.$id.'_header_'.rand(10000,99999).'.txt';
  $headerBuff_obj = fopen($headerBuff, 'w+');
  $targetFile = $upload_dir.'_'.$id.'_download_'.rand(10000,99999).'.tmp';
  $targetFile_obj = fopen( $targetFile, 'w' );
  $Disposition_filename='';
  
  $ch = curl_init( $url );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt( $ch, CURLOPT_WRITEHEADER, $headerBuff_obj);
  //curl_setopt( $ch, CURLOPT_HEADERFUNCTION, 'curlHeaderCallback');
  curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
  curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback' );
  curl_setopt( $ch, CURLOPT_FILE, $targetFile_obj );
  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
  //curl_exec( $ch );
  $curl_result='';
  $download_message='';
  if(curl_exec($ch) === false) {
    $curl_result= gks_lang('Σφάλμα').': ' . curl_error($ch);
  } else {
    rewind($headerBuff_obj);
    $headers = stream_get_contents($headerBuff_obj);
    //print_r($headers);
    //if(preg_match('/Content-Disposition: .*filename=([^ ]+)/', $headers, $matches)) {
    if ( preg_match('~filename=(?|"([^"]*)"|\'([^\']*)\'|([^;]*))~', $headers, $matches)) {
      //print_r($matches);
      if (isset($matches[1])) {
        $Disposition_filename=$matches[1];
        if (strpos($Disposition_filename, "\r\n") !== false) {
          $pp=explode("\r\n",$Disposition_filename);
          $Disposition_filename=$pp[0];
        }
      }
    }
    
    $curl_result= '';
  }
  curl_close($ch);
  fclose( $targetFile_obj );
  fclose( $headerBuff_obj );

  if ($download_message=='') $download_message=$curl_result;

  if (file_exists($headerBuff)) {
    unlink($headerBuff);
  }
  if ($download_message=='' and file_exists($targetFile) == false) 
    $download_message=gks_lang('Δεν έγινε λήψη του αρχείου');




  if ($download_message!='') {
    if (file_exists($targetFile)) {
      unlink($targetFile);
    }
    $sql="update gks_crm_tasks_links set 
    download_status=3, 
    download_end=now(), 
    download_message='".$db_link->escape_string($download_message)."',
    relative_path='',
    download_size_total=0,
    download_size_until_now=0,
    html_tds=null
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();}  
    debug_mail(false,'error download file',$sql);
    die();
      
  }
  
  
  
  $file_name=mb_basename($url_db);
  if ($Disposition_filename!='') $file_name=$Disposition_filename;
  
  //die($file_name);
  
  $file_parts = pathinfo($file_name);
  $relative_path=$file_name;
  $targetFile_final=$upload_dir.$file_name;
  if (file_exists($targetFile_final)) {
    do {
      $relative_path=$file_parts['filename'].'_'.rand(10000,99999).'.'.$file_parts['extension'];
      $targetFile_final=$upload_dir.$relative_path;
      if (file_exists($targetFile_final)==false) break;
    } while (true);
  }
  
  //copy ($targetFile,$targetFile_final.'ggg');
  rename($targetFile,$targetFile_final);
  
  $myfilesize=filesize($targetFile_final);

  $upload_dir2='';
  
  if (endwith(strtolower($targetFile_final),'.zip')) {
    $file_path=$targetFile_final;
    $zip = new ZipArchive;
    $res = $zip->open($file_path);
    if ($res === TRUE) {
      //echo ' 22222222';
      $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
      //$foldername=substr($foldername,0, strlen($foldername));
      
      $extract_path=dirname($file_path).'/'.$foldername;
      if (file_exists($extract_path)) {
        do {
          $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
          if (file_exists($extract_path)==false) break;
        } while (true);
      }
      
      for( $zipindexfile = 0 ; $zipindexfile < $zip->numFiles ; $zipindexfile++ ) {
          $zip_filename=$zip->getNameIndex($zipindexfile);
          if ( $zip_filename != '/' && $zip_filename != '__MACOSX/_' && $zip_filename != '__MACOSX' && strpos($zip_filename, '__MACOSX') === false) {
              //print $zip->getNameIndex( $zipindexfile ) . '<br>';
              $zip->extractTo( $extract_path, array($zip->getNameIndex($zipindexfile)) );
          }
      }
      //$zip->extractTo($extract_path);
      $zip->close();
      //unlink($file_path);
      $upload_dir2=$extract_path.'/';
    } else {
      debug_mail(false,'can not open zip file',$file_path);
    }
  }
  if (endwith(strtolower($targetFile_final),'.rar')) {
    
    $file_path=$targetFile_final;
    $foldername=str_replace(' ','',mb_basename($file_path)).'.file';
    //$foldername=substr($foldername,0, strlen($foldername));
    
    $extract_path=dirname($file_path).'/'.$foldername;
    if (file_exists($extract_path)) {
      do {
        $extract_path=dirname($file_path).'/'.$foldername.'_'.rand(10000,99999);
        if (file_exists($extract_path)==false) break;
      } while (true);
    }

        
    $archive = RarArchive::open($file_path);
    $entries = $archive->getEntries();
    foreach ($entries as $entry) {
        $entry->extract($extract_path);
    }
    $archive->close();   
    $upload_dir2=$extract_path.'/';
  }
  
  //debug_mail(false,'extract_path',$extract_path);
  
  $myjson='';

  $gks_fileserver_item_scandir_rec_echo='';
  $gks_fileserver_item_show_print=array();
  $gks_fileserver_item_relative_path=array();
  $gks_fileserver_item_relative_path_keys=array();
  gks_fileserver_item_scandir_rec(($upload_dir2=='' ? dirname($targetFile_final).'/' : $upload_dir2) ,1, ($upload_dir2=='' ? $targetFile_final : ''));
  $myjson=json_encode($gks_fileserver_item_relative_path_keys);  



  $sql="update gks_crm_tasks_links set 
  download_status=2, 
  download_end=now(), 
  download_message='OK',
  relative_path='".$db_link->escape_string($relative_path)."',
  download_size_total=".$myfilesize.",
  download_size_until_now=".$myfilesize.",
  download_pososto=100,
  html_tds='".$db_link->escape_string($myjson)."'
  where id_crm_tasks_links=".$id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die();}  

  if ($upload_dir2 != '') {
    $myexec=$upload_dir2.'gks_chmod.sh';
    $mybash='#!/bin/bash'."\n".
    'chmod -R 0777 '.$upload_dir2;
    file_put_contents($myexec, $mybash);
    chmod($myexec, 0777);  
    $ret=exec($myexec.' 2>&1', $output, $return_var);
    if (file_exists($myexec)) unlink($myexec);
  }
  
}



function progressCallback($resource, $download_size, $downloaded_size, $upload_size, $uploaded_size ) {
  global $db_link;
  global $id;
  global $download_message;
  
  static $previousProgress = 0;
  
  if ( $download_size == 0 ) {
      //$progress = 0;
      $progress = round( $downloaded_size * 100 / 100000000 ,1);
  } else {
      $progress = round( $downloaded_size * 100 / $download_size ,1);
  }
  if ( $progress > $previousProgress) {
    $previousProgress = $progress;
    
    $sql="update gks_crm_tasks_links set 
    download_pososto=".number_format($progress,2,'.','').",
    download_size_until_now=".$downloaded_size.",
    download_size_total=".$download_size.",
    download_end=now()
    where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();} 
    
    $sql="select download_status from gks_crm_tasks_links where id_crm_tasks_links=".$id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die();} 
    if ($result->num_rows==0) {
      $download_message=gks_lang('Εξαφανίστηκε η εγγραφή');
      return -1;
    }
    $row=$result->fetch_assoc();
    if ($row['download_status']!=1) {
      $download_message=gks_lang('Ακυρώθηκε από τον χρήστη !');
      return -1;
    } 
    
  }

}
