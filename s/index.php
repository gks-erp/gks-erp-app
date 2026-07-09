<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

// https://test.easyfilesselection.com/s/s1
// e5daf154697c4bd8c27d0564d0e17f52

define('SECURE', 1);
include_once('../functions.php');

//print '<pre>';print_r($_SERVER);die();


//$shorturl='http';
//if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']=='on') $shorturl='https';
//$shorturl.='://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$shorturl=''; if (isset($_SERVER['REQUEST_URI'])) $shorturl=trim($_SERVER['REQUEST_URI']);
if (startwith($shorturl,'/s/')) $shorturl=trim(substr($shorturl, 3));
//echo '|'.$shorturl.'|';die();

$my_page_title='short url - exec';
db_open();
stat_record();

//https://test.easyfilesselection.com/s/s1?fbclid=11111111111
$parts=explode('?',$shorturl);
$shorturl=$parts[0];
$rest_url=''; if (count($parts)==2) $rest_url=trim($parts[1]);

//echo '<pre>'.$shorturl."\n".$rest_url; die();


if ($shorturl=='') {header('Location: /'); die();}

$sql="select * from gks_urlshort where longurl<>'' and shorturl='".$db_link->escape_string($shorturl)."'";
//echo $sql;die();
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'sql error',$sql);
  header('Location: /'); die(); }

$s_pageurl = trim(urldecode($_SERVER['SCRIPT_NAME']));
$s_pageurl=$_SERVER['REQUEST_URI'];
$s_query_string = trim(urldecode($_SERVER['QUERY_STRING']));
$s_host = trim($_SERVER["HTTP_HOST"]);
$s_userAgent = '';
if (isset($_SERVER['HTTP_USER_AGENT'])) {
  $s_userAgent = $_SERVER['HTTP_USER_AGENT'];
  //$s_userAgent = mb_substr($user_agent,0,255);
}
$s_referer = '';
if (isset($_SERVER['HTTP_REFERER'])) {
  $s_referer = trim(rawurldecode($_SERVER['HTTP_REFERER']));
}

$urlshort_hit_guid=guid_for_urlshort_hit();

  
if ($result->num_rows > 0) {

  
  
  $row = $result->fetch_assoc();
  $id_urlshort=$row['id_urlshort'];
  $longurl=$row['longurl'];
  
  //$_SESSION['gks']['urlshort']=array(
  //  'id_urlshort'=>$id_urlshort,
  //  'longurl'=>$longurl,
  //  'shorturl'=>$shorturl,
  //  'crm_channel_id'=>$row['crm_channel_id'],
  //  'crm_channel_contact_id'=>$row['crm_channel_contact_id'],
  //  'crm_channel_campain_id'=>$row['crm_channel_campain_id'],
  //  'crm_channel_text'=>trim($row['crm_channel_text']),
  //  'assigned_id'=>$row['assigned_id'],
  //  'id_urlshort_hit'=>0,
  //);
  
    
  $sql="insert into gks_urlshort_hit (
  mydate_add,user_id_add,myip,
  urlshort_hit_guid,
  urlshort_id,crm_channel_id,crm_channel_contact_id,crm_channel_campain_id,
  crm_channel_code,crm_channel_text,assigned_id,
  sessionid,pageurl,query_string,host,userAgent,referer
  ) values (
  now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  '".$db_link->escape_string($urlshort_hit_guid)."',
  ".$id_urlshort.",
  ".$row['crm_channel_id'].",
  ".$row['crm_channel_contact_id'].",
  ".$row['crm_channel_campain_id'].",
  '".$db_link->escape_string(trim($row['crm_channel_code']))."',
  '".$db_link->escape_string(trim($row['crm_channel_text']))."',
  ".$row['assigned_id'].",
  '".$db_link->escape_string(trim(session_id()))."',
  '".$db_link->escape_string($s_pageurl)."',
  '".$db_link->escape_string($s_query_string)."',
  '".$db_link->escape_string($s_host)."',
  '".$db_link->escape_string($s_userAgent)."',
  '".$db_link->escape_string($s_referer)."'
  )";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'sql error',$sql);
    //header('Location: /'); die(); 
  //} else {
  //  $_SESSION['gks']['urlshort']['id_urlshort_hit'] = $db_link->insert_id;
  }


  //echo '<pre>'; echo $longurl."\n\n"; print_r($_SESSION['gks']['urlshort']); die();
  header('Location: '.$longurl.($rest_url=='' ? '' : '?'.$rest_url).'#gkssourlgid='.$urlshort_hit_guid); 
  die();
}

if (strlen($shorturl)<8) { //3 chars to obj sin 5 to elaxisto
  debug_mail(false,'shorturl not found: '.$_SERVER['REQUEST_URI'],$_SERVER['REQUEST_URI']);
  header('Location: /'); die();}
  
$first3chars=strtolower(substr($shorturl, 0,3));

$object_name='';

if ($first3chars=='s01') $object_name='gks_assets_service_reasons';
else if ($first3chars=='s02') $object_name='gks_assets_type';
else if ($first3chars=='s03') $object_name='gks_crm_channel_sale';
else if ($first3chars=='s04') $object_name='gks_crm_leads_status';
else if ($first3chars=='s05') $object_name='gks_crm_tasks_status';
else if ($first3chars=='s06') $object_name='gks_lang';
else if ($first3chars=='s07') $object_name='gks_custom_table';


if ($object_name=='') {
  $sql="SELECT * FROM gks_custom_table where shortcode_prefix='".$db_link->escape_string($first3chars)."'";
  //echo '<pre>'.$sql;die();
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'sql error',$sql);
    header('Location: /'); die();}
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();  
    $object_name=$row['custom_table_name'];
  }
}


if ($object_name=='') {
  debug_mail(false,'shorturl not found: '.$_SERVER['REQUEST_URI'],$_SERVER['REQUEST_URI']);
  header('Location: /'); die(); }

$object_map=gks_FilesObjectList_map($object_name);
/*Array
(
    [table] => gks_acc_inv_photo
    [tid] => id_acc_inv_photo
    [pid] => acc_inv_id
    [path] => acc/inv/
    [shortcode_prefix] => 565
)*/
$object_path=$object_map['path'];
$object_table=$object_map['table'];
$object_tid=$object_map['tid'];
$object_pid=$object_map['pid'];
$shortcode_prefix=$object_map['shortcode_prefix'];

$restchars=substr($shorturl, 3);
$sql="select * from ".$object_table." where public_shortcode like '".$db_link->escape_string($restchars)."'";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'sql error',$sql);
  header('Location: /'); die();}
if ($result->num_rows == 0) {
  debug_mail(false,'shorturl not found: '.$_SERVER['REQUEST_URI'],$_SERVER['REQUEST_URI']);
  header('Location: /'); die();}

$row = $result->fetch_assoc(); 
if (empty($row['photo_url']) or empty($row['public_expire_date'])) {
  debug_mail(false,'shorturl not found: '.$_SERVER['REQUEST_URI'],$_SERVER['REQUEST_URI']);
  header('Location: /'); die();}

$myfiledescr=trim_gks($row['descr']);
$myfilepath=$row['photo_url'];
$public_expire_date=strtotime($row['public_expire_date']);
if ($public_expire_date<time()) {
  debug_mail(false,'shorturl not found: '.$_SERVER['REQUEST_URI'],$_SERVER['REQUEST_URI']);
  header('Location: /'); die();}


$myfilepath = GKS_FileServerShare.$myfilepath;
if (file_exists($myfilepath) == false) {
  debug_mail(false,'shorturl file not found: '.$_SERVER['REQUEST_URI'],$_SERVER['REQUEST_URI']);
  header('Location: /'); die();}

$sql="update ".$object_table." set public_myopencount=public_myopencount+1
where public_shortcode like '".$db_link->escape_string($restchars)."'";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'sql error',$sql);
  header('Location: /'); die();}


$finfo = new finfo();
$fileinfo = $finfo->file($myfilepath, FILEINFO_MIME);

//$info = getimagesize($myfilepath);
//var_dump($fileinfo);
//die();  



$filename=mb_basename($myfilepath);
if ($myfiledescr!='') {
  $fileext = strtolower(pathinfo($myfilepath, PATHINFO_EXTENSION));
  $filename=$myfiledescr.'.'.$fileext;
  //https://test.easyfilesselection.com/s/565pzked?download=1
}
//echo '<pre>'.$filename;die();

//$urlshort_hit_guid=''; //guid_for_urlshort_hit();
$id_urlshort=-1;
$crm_channel_id=0;
$crm_channel_contact_id=0;
$crm_channel_campain_id=0;
$crm_channel_code='';
$crm_channel_text='';
$assigned_id=0;

$sql="insert into gks_urlshort_hit (
mydate_add,user_id_add,myip,
urlshort_hit_guid,
urlshort_id,crm_channel_id,crm_channel_contact_id,crm_channel_campain_id,
crm_channel_code,crm_channel_text,assigned_id,
sessionid,pageurl,query_string,host,userAgent,referer
) values (
now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
'".$db_link->escape_string($urlshort_hit_guid)."',
".$id_urlshort.",
".$crm_channel_id.",
".$crm_channel_contact_id.",
".$crm_channel_campain_id.",
'".$db_link->escape_string(trim($crm_channel_code))."',
'".$db_link->escape_string(trim($crm_channel_text))."',
".$assigned_id.",
'".$db_link->escape_string(trim(session_id()))."',
'".$db_link->escape_string($s_pageurl)."',
'".$db_link->escape_string($s_query_string)."',
'".$db_link->escape_string($s_host)."',
'".$db_link->escape_string($s_userAgent)."',
'".$db_link->escape_string($s_referer)."'
)";
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'sql error',$sql);
  //header('Location: /'); die(); 
//} else {
//  $_SESSION['gks']['urlshort']['id_urlshort_hit'] = $db_link->insert_id;
}


header('Content-Type: '.$fileinfo);
if (isset($_GET['download']) or isset($_GET['d'])) {
  header('Content-Disposition: attachment; filename="'.$filename.'"');
} else {
  header('Content-Disposition: inline; filename="'.$filename.'"'); 
}

$offset = 60 * 60 * 24; //24 ores
//if (getenv('ENV') == 'DEVELOPMENT') $offset = 60;
  
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
header("Cache-Control: max-age=$offset, must-revalidate"); 
header("Pragma: private");

header("gks_read_file: run");
readfile($myfilepath);
 
/*  
echo '<pre>'.$myfilepath;die();
echo '<pre>'.$restchars.'|';print_r($object_map);die();



echo '<pre>'.$shorturl."\n".$rest_url; die();

die();




echo '<pre>';
print time();
print "\n";
print $shorturl;
print "\n";
print $longurl;
print "\n";
print_r($_SERVER);
die();


*/
