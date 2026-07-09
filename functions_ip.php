<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

if (GKS_MAXIMIND_COM_PATH!='') {
 require_once(GKS_MAXIMIND_COM_PATH.'geoip2.phar');
}
use GeoIp2\Database\Reader;

$gks_maxmind_database_reader=null;

function gks_get_country_from_ip($ip) {
  if (GKS_MAXIMIND_COM_PATH=='') return '--';
  global $gks_maxmind_database_reader;
  
  if ($ip=='') return '';
  
  if ($ip=='127.0.0.1') return '-';
  if (substr($ip,0,3)=='10.') return '--';
  if (substr($ip,0,7)=='172.16.') return '--';
  if (substr($ip,0,7)=='172.17.') return '--';
  if (substr($ip,0,7)=='172.18.') return '--';
  if (substr($ip,0,7)=='172.19.') return '--';
  if (substr($ip,0,7)=='172.20.') return '--';
  if (substr($ip,0,7)=='172.21.') return '--';
  if (substr($ip,0,7)=='172.22.') return '--';
  if (substr($ip,0,7)=='172.23.') return '--';
  if (substr($ip,0,7)=='172.24.') return '--';
  if (substr($ip,0,7)=='172.25.') return '--';
  if (substr($ip,0,7)=='172.26.') return '--';
  if (substr($ip,0,7)=='172.27.') return '--';
  if (substr($ip,0,7)=='172.28.') return '--';
  if (substr($ip,0,7)=='172.29.') return '--';
  if (substr($ip,0,7)=='172.30.') return '--';
  if (substr($ip,0,7)=='172.31.') return '--';
  if (substr($ip,0,8)=='192.168.') return '--';
  
  if ($gks_maxmind_database_reader==null) {
    $gks_maxmind_database_reader = new Reader(GKS_MAXIMIND_COM_PATH.'GeoLite2-City.mmdb');
  }
  
  try {
    $record = $gks_maxmind_database_reader->city($ip);
    // or for Country DB
    // $reader = new Reader('/path/to/GeoLite2-Country.mmdb');
    // $record = $reader->country($_SERVER['REMOTE_ADDR']);
    return trim_gks($record->country->isoCode);
    
//    print($record->country->isoCode . "\n");
//    print($record->country->name . "\n");
//    //print($record->country->names['en-US'] . "\n");
//    print($record->mostSpecificSubdivision->name . "\n");
//    print($record->mostSpecificSubdivision->isoCode . "\n");
//    print($record->city->name . "\n");
//    print($record->postal->code . "\n");
//    print($record->location->latitude . "\n");
//    print($record->location->longitude . "\n");
//  //} catch (\GeoIp2\Exception\InvalidArgumentException $e2) {
//  //  var_dump($e2);
//  //} catch (\GeoIp2\Exception\AddressNotFoundException $e1) {
//  //  //var_dump($e1);
//  //  echo 'The address is not in the database.';
  } catch(Exception $e) {
    //echo $e->getMessage();
    
    
  }
  return '';
}

