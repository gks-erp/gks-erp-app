<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

//debug_mail(false,'places','');


if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$term=str_replace('*', '%', $term);
//$term=str_replace('%', '', $term);
$term=str_replace("'", '', $term);

if (mb_strlen($term) < 1 ) die();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση Δ.Ο.Υ.');
db_open();
stat_record();

$sql="SELECT id_doy, doy_title
FROM gks_doy 
WHERE doy_title<> '' 
and doy_disable = 0 
and doy_title like '%".$db_link->escape_string($term)."%' 
order by doy_title
limit 1000";
//echo $sql;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'warning on mymail error sql',$sql);
  die();
}
$out=array();
while ($row = $result->fetch_assoc()) {
  $out[] = array('id' => $row['id_doy'], 'value' => $row['doy_title']);
}

echo json_encode($out);



