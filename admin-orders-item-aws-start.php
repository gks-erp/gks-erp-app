<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

putenv('AWS_CSM_ENABLED=false');
require 'aws/aws-autoloader.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;


ini_set('max_execution_time', 6000);
set_time_limit(6000);


//debug_mail(false,'admin-orders-item-link-action_start.php','');
//die();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0) {
  debug_mail(false,'the id is not set','');
  die();}


$my_page_title=gks_lang('Έναρξη λήψης αρχείου παραγγελίας AWS').' id '.$id;
db_open();
stat_record();


$sql="select * from gks_orders_aws_download where id_aws_download=".$id." and status in ('draft', 'working') order by id_aws_download desc";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
if ($result->num_rows == 0) {  debug_mail(false,'aws not found',$sql);die('aws not found');}
$row=$result->fetch_assoc();
$order_id = $row['order_id'];

$sql="update gks_orders_aws_download set status='working' where id_aws_download=".$id;
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}


if ((endwith(strtolower($row['local_path']),'.zip') or endwith(strtolower($row['local_path']),'.rar')) and file_exists($row['local_path'].'.file')) {
  echo 'file_exists2 '.$row['local_path'];
  die();
} else if (file_exists($row['local_path']) and filesize($row['local_path']) == $row['file_size'] and endwith(strtolower($row['local_path']),'.zip')==false and endwith(strtolower($row['local_path']),'.rar')==false) { //alreay exist
  //exist file from other procudure
  echo 'file_exists1 '.$row['local_path'];
  die(); 

  
} else {

  

  
  $sharedConfig =[
      'version'     => 'latest',
      'region'      => 'eu-west-1',
      'credentials' => [
          'key'    => $GKS_AWS_KEY,
          'secret' => $GKS_AWS_SECRET,
      ],
  ];
  
  $s3Client = new S3Client($sharedConfig);
  
  
 $id_aws_download=$id;
 
  //https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#progress
  
  // Apply the http option to a specific command using the "@http"
  // command parameter
  $data = $s3Client->getObject([
      'Bucket' => $GKS_AWS_BUCKET,
      'Key'    => $row['remote_path'],
      '@http' => [
          'progress' => function ($expectedDl, $dl, $expectedUl, $ul) {
            global $id_aws_download;  
            global $db_link;
              
            $sql="update gks_orders_aws_download set dowloaded_file_size=".$dl." where id_aws_download=".$id_aws_download;
            $result = $db_link->query($sql);
            if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}

              
//              printf(
//                  "%s of %s downloaded, %s of %s uploaded.\n",
//                  $expectedDl,
//                  $dl,
//                  $expectedUl,
//                  $ul
//              );
          }
      ]
  ]);  
  $data=$data['Body'];
  //$s3Client->registerStreamWrapper();
  
  //echo $row['local_path'];
  //die();
  
  //$ctx = stream_context_create();
  //stream_context_set_params($ctx, array("notification" => "stream_notification_callback"));

  
  //$key2=$row['remote_path'];
  //$data = file_get_contents("s3://{$bucket}/{$key2}"); //, false, $ctx
  
  $save_dir=dirname($row['local_path']);
  if (file_exists($save_dir)==false) {
    if (@mkdir($save_dir , 0755, true) == false ) {
      debug_mail(false,'can not create dir: ',$save_dir);
      die(gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος').' '.$save_dir);
    }   
  }
  file_put_contents($row['local_path'],$data);
  
  if (file_exists($row['local_path']) == false) {
    debug_mail(false,'error download',$row['local_path']);die('download error');
  } else {
    //admin-orders-item-aws-start.php?id=3
    //echo $row['local_path'];
    //die();
    $file_path=$row['local_path'];
    if (endwith(strtolower($file_path),'.zip')) {
      echo 'ggggggggggg';
      
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
          if ( $zip->getNameIndex( $zipindexfile ) != '/' && $zip->getNameIndex( $zipindexfile ) != '__MACOSX/_' && $zip->getNameIndex( $zipindexfile ) != '__MACOSX') {
              print $zip->getNameIndex( $zipindexfile ) . '<br>';
              $zip->extractTo( $extract_path, array($zip->getNameIndex($zipindexfile)) );
          }
        }
        //$zip->extractTo($extract_path);
        $zip->close();
        //unlink($file_path);
        
        //$gks_fileserver_item_relative_path=array();
        $gks_fileserver_item_scandir_rec_echo='';
        $gks_fileserver_item_show_print=array();
        $gks_fileserver_item_relative_path=array();
        $gks_fileserver_item_relative_path_keys=array();
        gks_fileserver_item_scandir_rec($extract_path.'/',1);
        $myjson=json_encode($gks_fileserver_item_relative_path_keys);
        
        $sql="update gks_orders_aws_download set html_tds='".$db_link->escape_string($myjson)."' where id_aws_download=".$id;
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}

      } else {
        debug_mail(false,'can not open zip file',$file_path);
          
      }
    } else if (endwith(strtolower($file_path),'.rar')) {
      
      
      $has_parts=false;
      $part_num=0;
      $rar_parts=explode('.', mb_basename(strtolower($file_path)));
      
      if (count($rar_parts)>=3) {
        $part_tem=$rar_parts[count($rar_parts)-2];
        if (strlen($part_tem)>=4 &&  startwith($part_tem,'part')) {
          $has_parts=true;
          //print_r($rar_parts);   
          $part_num=intval(substr($part_tem, 4));
          //echo $part_num;
        }
      }

      if ($has_parts == false or ($has_parts == true and $part_num==1)) {
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
        //unlink($file_path);
        
        //$gks_fileserver_item_relative_path=array();
        $gks_fileserver_item_scandir_rec_echo='';
        $gks_fileserver_item_show_print=array();
        $gks_fileserver_item_relative_path=array();
        $gks_fileserver_item_relative_path_keys=array();
        gks_fileserver_item_scandir_rec($extract_path.'/',1);
        $myjson=json_encode($gks_fileserver_item_relative_path_keys);
        
        $sql="update gks_orders_aws_download set html_tds='".$db_link->escape_string($myjson)."' where id_aws_download=".$id;
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}

      }
    }
    
    
  }
  
}
$sql="select * from gks_orders_aws_download where status='draft' and order_id=".$order_id." and id_aws_download<>".$id." order by id_aws_download desc";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
if ($result->num_rows > 0) {
  $row_new=$result->fetch_assoc();
  $id_new = $row_new['id_aws_download'];
  
  $sql="update gks_orders_aws_download set status='working' where id_aws_download=".$id_new;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}

  gks_curl_post_async(GKS_SITE_URL.'my/exec_order_aws_start.php',array('id' =>$id_new));


    
} 


$mysize=0;
if (file_exists($row['local_path'])) {
  $mysize=filesize($row['local_path']);
} else {
  if (endwith(strtolower($row['local_path']),'.zip') || endwith(strtolower($row['local_path']),'.rar')) {
    $mysize=$row['file_size'];
  }
}

$sql="update gks_orders_aws_download set status='complete',dowloaded_file_size=".$mysize.", pososto=100 where id_aws_download=".$id;
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}


$sql="select * from gks_orders_aws_download where order_id=".$order_id." and status not in ('complete', 'finish', 'finish_old')";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
if ($result->num_rows == 0) {
  $sql="update gks_orders_aws_download set status='finish' where order_id=".$order_id." and status='complete'";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
  
}
  

die();


function stream_notification_callback($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max) {
  global $db_link;
  global $id_aws_download;
  
    switch($notification_code) {
        case STREAM_NOTIFY_RESOLVE:
        case STREAM_NOTIFY_AUTH_REQUIRED:
        case STREAM_NOTIFY_COMPLETED:
        case STREAM_NOTIFY_FAILURE:
        case STREAM_NOTIFY_AUTH_RESULT:
            //var_dump($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max);
            /* Ignore */
            break;

        case STREAM_NOTIFY_REDIRECTED:
            //echo "Being redirected to: ", $message;
            break;

        case STREAM_NOTIFY_CONNECT:
        
            //echo "Connected...";
            break;

        case STREAM_NOTIFY_FILE_SIZE_IS:
            //$sql="update gks_orders_aws_download set file_size=".$bytes_max." where id_aws_download=".$id_aws_download;
            //$result = $db_link->query($sql);
            //if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}

            //echo "Got the filesize: ", $bytes_max;
            break;

        case STREAM_NOTIFY_MIME_TYPE_IS:
            //echo "Found the mime-type: ", $message;
            break;

        case STREAM_NOTIFY_PROGRESS:
            $sql="update gks_orders_aws_download set dowloaded_file_size=".$bytes_transferred." where id_aws_download=".$id_aws_download;
            $result = $db_link->query($sql);
            if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
            //echo "Made some progress, downloaded ", $bytes_transferred, " so far";
            break;
    }
    //echo "\n";
}
