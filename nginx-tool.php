<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

/*
chmod -R 770 /var/www/php/test.easyfilesselection.com/cache_nginx
chown www-data:www-data -R /var/www/php/test.easyfilesselection.com/cache_nginx
*/

function endsWith($haystack, $needle) {
  $length = strlen($needle);
  if ($length == 0) return true;
  return (substr($haystack, -$length) === $needle);
}

$mypath=getcwd();
$mypath=dirname(dirname(dirname(__FILE__))).'/cache_nginx';
//echo $mypath; die();

if (file_exists($mypath)==false) die();

$files=array_diff(scandir($mypath), array('..', '.'));

foreach ($files as $value) {
 @unlink($mypath.'/'.$value);
} 

print '<pre>';
echo $mypath."\n";
print_r($files);die();


//$dirs1 = array_diff(scandir($mypath), array('..', '.'));
//foreach ($dirs1 as $value) {
//  $dirs2=array_diff(scandir($mypath.'/'.$value), array('..', '.'));
//  
//} 
//print '<pre>';
//echo $mypath."\n";
//print_r($dirs1);

