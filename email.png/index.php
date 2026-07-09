<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

$id=trim($_GET["id"]);
$gkIP=$_SERVER['REMOTE_ADDR'];

if (strlen($id) <> 32) die();

set_time_limit(10);
ini_set('max_execution_time', 10);
putenv("ENV=PRODUCTION");

define('SECURE', 1);
//include_once('functions.php');
require_once('../../wp-config.php');

$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headers .= 'From: debug@myphotos.gr' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";

$db_link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

//mail('kostas@myphotos.gr' , 'stat error' , 'dffffffffff',$headers );

if ($db_link->connect_error) {
    mail('kostas@gks.gr' , 'email.png/index.php' , $db_link->connect_errno . '-'.$db_link->connect_error,$headers );
}
$db_link->set_charset('utf8'); 


$sql="update gks_email set views_count=views_count+1 , views_ips= CONCAT(IFNULL(views_ips,''), '".$db_link->escape_string($gkIP)."\n') where guid='".$db_link->escape_string($id)."' order by id desc limit 1";
$result = $db_link->query($sql);

$sql="update gks_email set date_view=now() where guid='".$db_link->escape_string($id)."' and date_view is null order by id desc limit 1";
$result = $db_link->query($sql);


$size = filesize('../img/null.png');
header("Content-Length: " . $size);
header("Content-Type: image/png");
echo file_get_contents('../img/null.png');

die();
