<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

ini_set('max_execution_time', 6000);
set_time_limit(6000);


//debug_mail(false,'admin-orders-item-link-action_start.php','');
//die();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  die();}


$my_page_title=gks_lang('AWS - αναζήτηση αρχείων παραγγελίας');
db_open();
stat_record();

$action=''; if (isset($_POST['action'])) $action=trim_gks($_POST['action']);
$folder=''; if (isset($_POST['folder'])) $folder=trim_gks(base64_decode($_POST['folder']));

putenv('AWS_CSM_ENABLED=false');
require 'aws/aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;



$sharedConfig =[
    'version'     => 'latest',
    'region'      => 'eu-west-1',
    'credentials' => [
        'key'    => $GKS_AWS_KEY,
        'secret' => $GKS_AWS_SECRET,
    ],
];

$s3Client = new S3Client($sharedConfig);


if ($action == 'getbutton') {
  
  $sql="select * from gks_orders_aws_download where order_id=".$id." and status not in ('finish','finish_old')";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}    
  
  if ($result->num_rows > 0) {  
    $html='<div id="aws_download_perc" class="progress">'.
          '<div id="aws_download_perc_bar" class="progress-bar progress-bar-striped" role="progressbar" style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">'.
          '</div></div>';
    $html.='<div id="aws_filename" style="text-align:center;">&nbsp;</div>';
    
    $return = array('success' => true, 'message' => base64_encode('OK'), 'html' =>base64_encode($html),'runscript' => 'timer');
    echo json_encode($return); die();
  }
  
  
  if ($folder=='') {
    debug_mail(false,'folder is not set',$folder);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').'<br>'.gks_lang('Δεν έχει ορισθεί ο φάκελος του AWS')));
    echo json_encode($return); die();}
    
  $parts=explode('/',$folder);
  if (count($parts)!=2) {
    debug_mail(false,'folder is not set',$folder);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').'<br>'.gks_lang('Ο φάκελος του AWS δεν είναι σωστός')));
    echo json_encode($return); die();}
  
  $folder_root=$parts[0];
  $folder_order=$parts[1];
  
      
  $objects = $s3Client->getIterator('ListObjects', array(
    'Bucket' => $GKS_AWS_BUCKET,
    'Prefix' => $folder.'/',
  ));
  
  $files_array=array();
  $total_size=0;
  foreach ($objects as $object) {
    $this_size=intval($object['Size']);
    if ($this_size>0) {
      $files_array[] = array(
        'name' => mb_basename($object['Key']),
        'size' => $this_size,
        'exist_local' => false,
      );
      $total_size+=$this_size;
    }
  }
  
  
  usort($files_array, "aws_items_sort");
  
  
  $html='';
  $runscript='';
  
  if (count($files_array)==0) {
    $html=gks_lang('Δεν βρέθηκαν αρχεία στο AWS');
  } else {
    $found_files=0;
    
    $save_dir = GKS_FileServerShare.'order/'.$id.'/';
    if (file_exists($save_dir)==false or file_exists($save_dir.$folder_order.'/') == false) {
      $html=gks_lang('Λήψη').' '.count($files_array).' αρχείων ('.number_format($total_size/1024/1024, 2, $GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB)';
      $runscript='button';
      $html='<button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="aws_download_files">'.
            $html.
            '</button>';
    } else {
      $exist_files = array_diff(scandir($save_dir.$folder_order.'/'), array('..', '.')); 
      
      foreach ($files_array as &$myfile) {
        $local_found=false;
        foreach ($exist_files as $localfile) {
          
          if (mb_strtolower($myfile['name']) == mb_strtolower($localfile)) {
            if (filesize($save_dir.$folder_order.'/'.$localfile) == $myfile['size']) {
              $myfile['exist_local']=true;
              break;
            }
          } else if ((endwith(strtolower($myfile['name']),'.zip') or endwith(strtolower($myfile['name']),'.rar')) and file_exists($save_dir.$folder_order.'/'.$myfile['name'].'.file')) {
              $myfile['exist_local']=true;
              break;            
          }
        }
      }
      unset($myfile);
      
      $exist_files=0;
      $total_files=0;
      $total_size=0;
      foreach ($files_array as $myfile) {
        if ($myfile['exist_local']==false) {
          $total_files++;
          $total_size+=$myfile['size'];          
        } else {
          $exist_files++;
        }
      }
      if ($total_files>0 and $exist_files==0) {
        $html=gks_lang('Λήψη').' '.$total_files.' αρχείων ('.number_format($total_size/1024/1024, 2, $GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB)';
        $runscript='button';
        $html='<button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="aws_download_files">'.
              $html.
              '</button>';
      } else if ($total_files>0 and $exist_files>0) {
        $html=gks_lang('Λήψη').' '.$total_files.' ΝΕΩΝ-ΕΠΙΠΛΕΟΝ αρχείων ('.number_format($total_size/1024/1024, 2, $GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB)';
        $runscript='button';
        $html='<button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="aws_download_files">'.
              $html.
              '</button>';
      } else {
        $html=gks_lang('Δεν βρέθηκαν νέα αρχεία');
        $runscript='';
      }
      //print '<pre>';
      //print_r($exist_files);
      //print_r($files_array);
      //die();
    }
  }

  $return = array('success' => true, 'message' => base64_encode('OK'), 'html' =>base64_encode($html),'runscript' => $runscript);
  echo json_encode($return); die();

} else if ($action == 'getfiles') {


  if ($folder=='') {
    debug_mail(false,'folder is not set',$folder);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').'<br>'.gks_lang('Δεν έχει ορισθεί ο φάκελος του AWS')));
    echo json_encode($return); die();}
    
  $parts=explode('/',$folder);
  if (count($parts)!=2) {
    debug_mail(false,'folder is not set',$folder);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').'<br>'.gks_lang('Ο φάκελος του AWS δεν είναι σωστός')));
    echo json_encode($return); die();}
  
  $folder_root=$parts[0];
  $folder_order=$parts[1];
  
  $save_dir = GKS_FileServerShare.'order/'.$id.'/';
  
  if (file_exists($save_dir) == false) {
    if (@mkdir($save_dir , 0755, true) == false ) {
      debug_mail(false,'can not create dir: ',$save_dir);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').' '.$save_dir));
      echo json_encode($return); die();
    }
  }
  
  $save_dir.=$folder_order.'/';
  if (file_exists($save_dir) == false) {
    if (@mkdir($save_dir , 0755, true) == false ) {
      debug_mail(false,'can not create dir: ',$save_dir);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').' '.$save_dir));
      echo json_encode($return); die();
    }
  }
  
  $exist_files = array_diff(scandir($save_dir), array('..', '.'));
  
    
  $objects = $s3Client->getIterator('ListObjects', array(
    'Bucket' => $GKS_AWS_BUCKET,
    'Prefix' => $folder.'/',
  ));
  
  $files_array=array();
  $total_size=0;
  foreach ($objects as $object) {
    $this_size=intval($object['Size']);
    if ($this_size>0) {
      $files_array[] = array(
        'name' => mb_basename($object['Key']),
        'size' => $this_size,
        'exist_local' => false,
      );
      $total_size+=$this_size;
    }
  }
  
   
  foreach ($files_array as &$myfile) {
    $local_found=false;
    foreach ($exist_files as $localfile) {
          if (mb_strtolower($myfile['name']) == mb_strtolower($localfile)) {
            if (filesize($save_dir.$myfile['name']) == $myfile['size']) {
              $myfile['exist_local']=true;
              break;
            }
          } else if ((endwith(strtolower($myfile['name']),'.zip') or endwith(strtolower($myfile['name']),'.rar')) and file_exists($save_dir.$folder_order.'/'.$myfile['name'].'.file')) {
              $myfile['exist_local']=true;
              break;            
          }
          
          
//      if (mb_strtolower($myfile['name']) == mb_strtolower($localfile)) {
//        if (filesize($save_dir.$myfile['name']) == $myfile['size']) {
//          $myfile['exist_local']=true;
//          break;
//        }
//      }                

    }
  }
  unset($myfile);
  
  $sql="update gks_orders_aws_download set status='finish_old' where status='finish' and order_id=".$id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}      
  
  $first_insert=0;
  foreach ($files_array as $myfile) {
    if ($myfile['exist_local']==false) {
      $sql="insert into gks_orders_aws_download (
        order_id,remote_path,local_path,file_size,pososto,status
      ) values (
        ".$id.",
        '".$db_link->escape_string($folder.'/'.$myfile['name'])."',
        '".$db_link->escape_string($save_dir.$myfile['name'])."',
        ".$myfile['size'].",
        0,'draft'
      )";
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}      
      
      if ($first_insert==0) $first_insert = $db_link->insert_id;
    }
  }
  
  
  $runscript='timer';
  $html='<div id="aws_download_perc" class="progress">'.
        '<div id="aws_download_perc_bar" class="progress-bar progress-bar-striped" role="progressbar" style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">'.
        '</div></div>';
  $html.='<div id="aws_filename" style="text-align:center;">&nbsp;</div>';

        
  
  //  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').' ddd '.$runscript));
  //  echo json_encode($return); die();
  
  
  $return = array('success' => true, 'message' => base64_encode('OK'), 'html' =>base64_encode($html),'runscript' => $runscript);
  echo json_encode($return); die();
  
  
    
} else if ($action == 'timer') {
  $percent=0;
  $stoptime=0;
  
  $sql="select * from gks_orders_aws_download where order_id=".$id." and status not in ('finish','finish_old') order by id_aws_download desc";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}    

  $total_size=0;
  $total_download_size=0;
  $hasfinish=true;
  $start_new_download=0;
  $has_working=false;
  $filename_working='';
  while ($row = $result->fetch_assoc()) {
    $total_size+=$row['file_size'];
    $total_download_size+=$row['dowloaded_file_size'];
    if ($row['status'] !='complete') {
      $hasfinish=false;
    }
    if ($start_new_download==0 and $row['status']=='draft') {
      $start_new_download=$row['id_aws_download'];
    }
    if ($row['status']=='working') {
      $has_working=true;
      $filename_working = basename($row['remote_path']) .' ('.number_format($row['file_size']/1024/1024, 2, ',', '.').'MB)';
    }
  }
  
  
  if ($total_size>0) {
    $percent=100*$total_download_size/$total_size;
  } else {
    $percent=100;
  }
  
  if ($has_working) $start_new_download=0;
  if ($hasfinish == false && $has_working == false && $start_new_download>0) {
    
    $sql="update gks_orders_aws_download set status='working' where id_aws_download=".$start_new_download;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    

    gks_curl_post_async(GKS_SITE_URL.'my/exec_order_aws_start.php',array('id' =>$start_new_download));
  

  }
  
  
  $sql="select * from gks_orders_aws_download where order_id=".$id." and status in ('complete','finish') order by id_aws_download";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}  
  
  
  $complete_td=array();
  $crop_len=strlen(GKS_FileServerShare.'order/'.$id.'/');
  while ($row = $result->fetch_assoc()) {
    //file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/aws/temp/'.$row['id_aws_download'].'.txt',print_r($row,true)."\r\n");
    if (endwith(strtolower($row['local_path']),'.zip') or endwith(strtolower($row['local_path']),'.rar')) {
      if (file_exists($row['local_path'].'.file')) {
        $html_tds = trim_gks($row['html_tds']);
        if ($html_tds!='') {
          $html_tds_array=json_decode($html_tds, true);
          foreach ($html_tds_array as $value) {
            $complete_td[] = array(
              'relpath' => $value['path'],
              'td' => $value['html'],
            );          
          } 
        }
      }
    
    } else if (file_exists($row['local_path'])) {
      $relative_path=substr($row['local_path'], $crop_len);
      $url_file='admin-get-file.php?fs=fileservers&file='.rawurlencode('order/'.$id.'/'.$relative_path);
      
      $img_thump='';
      //$out_name_html='<a href="'.$url_file.'"  target="_blank">'.$dir.'</a>';
      $out_name_html='<a href="'.$url_file.'"  target="_blank">'.mb_basename($relative_path).'</a>';
      $fileext = strtolower(pathinfo($relative_path, PATHINFO_EXTENSION));
      if (in_array('.'.$fileext,GKS_IMAGE_EXTENSION)) {
        
        $thump_file=$id.'/'.dirname($relative_path).'/thumbnail/' . mb_basename($relative_path);
  
        $img_thump=$thump_file;
        $url_thump='admin-get-file.php?fs=fileservers&file='.rawurlencode('order/'.$thump_file);
        $img_thump='<a class="filesobjectlist_lightgallery_gks_fileserver_item" href="'.$url_file.'" data-download-url="'.$url_file.'&download=1">'.
                    '<img src="'.$url_thump.'">'.
                   '</a>';
        $select_for_print='<img class="filesobjectlist_set_print_photo" data-value=0 data-path="'.'order/'.$id.'/'.$relative_path.'" src="img/0b.png">';
      } else {
        $select_for_print='';            
      }
      $public_file='<img class="filesobjectlist_set_public_file"data-path="'.'order/'.$id.'/'.$relative_path.'" src="img/0bbl.png" data-expire_date="" data-shortcode_url="" data-myopencount="0">';
            
      $scandir_rec_echo= 
      '<tr class="tddd" data-path="'.'order/'.$id.'/'.$relative_path.'">'.
        '<th class="mytdcm" scope="row" nowrap>'.
          '<i class="fas fa-trash-alt filesobjectlist_delete_upload_photo" data-path="'.'order/'.$id.'/'.$relative_path.'"></i>'.
        '</th>'.
        '<td class="mytdcml fol_td_name">'.$out_name_html.'</td>'.
        '<td class="mytdcm tdimg_descr"></td>'.
        '<td class="mytdcm tdimg">'.$img_thump.'</td>'.
        '<td class="mytdcm fol_td_date">'.secondsago(filemtime($row['local_path'])).'</td>'.
        '<td class="mytdcmr" nowrap>'. number_format(($row['file_size']/1024/1024),2,',','.').' MB</td>'.
        '<td class="mytdcm" nowrap>'.
         '<a href="'.$url_file.'&download=1">'.
           '<i class="fas fa-download fol_td_download"></i>'.
         '</a>'.
        '</td>'.
        '<td class="mytdcm fol_selprint" nowrap>'.$select_for_print.'</td>'.
        '<td class="mytdcm fol_selpublic" nowrap>'.$public_file.'</td>'.
      '</tr>';  
      
      $complete_td[] = array(
        'relpath' => 'order/'.$id.'/'.$relative_path,
        'td' => $scandir_rec_echo,
      );

    }         
  }
  
  
  
  
  $return = array(
    'success' => true, 
    'message' => base64_encode('OK'), 
    'percent' =>$percent,
    'filename'=> $filename_working,
    'hasfinish' => $hasfinish,
    'total_size' => $total_size,
    'total_download_size' => $total_download_size,
    'start_new_download' => $start_new_download,
    'has_working' => $has_working,
    'complete_td' => $complete_td,

  
  );
  echo json_encode($return); die();  
}


$return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα εντολής')));
echo json_encode($return); die();


function aws_items_sort($a, $b) {
  $c = new Collator('el_GR');
  return $c->compare($a['name'], $b['name']);
  
}
