<?php
include_once('_current/_config.php');

function trim_gks($a) {
  if (is_null($a)) return '';
  return trim($a); 
}

$id=0;
if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
if (isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
$input_data = json_decode($HTTP_RAW_POST_DATA, true);
if (isset($input_data['id'])) $id=intval($input_data['id']);
if ($id<=0) die();

$fileurl=GKS_SITE_URL.'my/admin-acc-inv-item-link-action_start.php?cache='.time().rand(1000,9999).rand(1000,9999).rand(1000,9999).'&id='.$id;

//echo $fileurl;
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


