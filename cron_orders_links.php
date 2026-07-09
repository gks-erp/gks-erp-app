<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

 
ini_set('max_execution_time', 5);
set_time_limit(5);
putenv("ENV=PRODUCTION");
define('SECURE', 1);

require_once('_current/_config.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');



$db_link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($db_link->connect_error) {
  debug_mail(false,'DB error',$db_link->connect_errno . '-'.$db_link->connect_error);
  die();
}
$db_link->set_charset('utf8'); 

//debug_mail(false,'cron_orders_links.php','');

$sql="select * from gks_orders_links where download_status=1 and download_start>date_sub(now(), interval 1 hour)";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'sql error',$db_link->errno . '-'.$db_link->error); die();}
if ($result->num_rows > 0) die(); //kapoio allo trexei

$sql="select * from gks_orders_links
where download_status=0
and (
  url like 'https://mega.nz/%' or
  url like 'http://sendanywhe.re/%' or
  url like 'https://sendanywhe.re/%' or
  url like 'https://www.dropbox.com/%' or
  url like 'https://we.tl/%' or
  url like 'https://wetransfer.com/%' or
  url like 'https://%.wetransfer.com/%' or
  url like 'https://%.zip' or
  url like 'http://%.zip' or
  url like 'https://%.rar' or
  url like 'http://%.rar'
)
and mydate>'2020-06-15'
order by id_order_links desc
limit 1";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'sql error',$db_link->errno . '-'.$db_link->error); die();}
if ($result->num_rows == 0) die(); //den brethike kati

$row = $result->fetch_assoc();
$url=$row['url'];
$id_order_links=$row['id_order_links'];
$order_id=$row['order_id'];

$fileurl=GKS_SITE_URL.'my/admin-orders-item-link-action_start.php?id='.$id_order_links.'&cron=1';

debug_mail(false,'start auto orders_links','<pre>order id: '.$order_id."\r\n".'id_order_links: '.$id_order_links."\r\n".'url: '.$url."\r\n".'fileurl: '.$fileurl.'</pre>');

//echo 'end|'.$url.'|'.$id_order_links.'|'.$order_id;
//die();
  
$opts = array(
  'http'=>array(
    'timeout' => 30,  //Seconds  
    'method'=>"POST",
    'header'=>"Content-type: application/x-www-form-urlencoded\r\n" .
              "Accept-language: en\r\n" ,
  ),
  "ssl"=>array(
      "verify_peer"=>false,
      "verify_peer_name"=>false,
  ),
);

$context = stream_context_create($opts);


$file = @file_get_contents($fileurl, false, $context); 

echo 'end|'.$url.'|'.$id_order_links.'|'.$order_id;
//die();

