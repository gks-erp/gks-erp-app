<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

//echo date('Y-m-d H:i:s',1610888400); echo '<br>';echo date('Y-m-d H:i:s',1610899200);die();

// http://test.easyfilesselection.com/my/dav/cal/admin/default/
// http://192.168.1.245/calendars/admin/default/

//echo md5('kostas1:SabreDAV:kostas2');die();

/*

CalendarServer example

This server features CalDAV support

*/
//print '<pre>';print_r($_SERVER);die();
//require_once '../../../wp-config.php';
//echo DB_NAME; die();
//var_dump([1,2]);
//$a=array();
//$a[]=1;
//$a[]=2;
//var_dump($a);
//die();
//$a=(string)false;
//var_dump($a);
//die();


require_once('../_current/_dav_db.php');
$gks_webdav_folders_local=[];
if (defined('GKS_WEBDAV_FOLDERS') and is_array(GKS_WEBDAV_FOLDERS) and count(GKS_WEBDAV_FOLDERS)>=2) {
  if (isset(GKS_WEBDAV_FOLDERS['tmp'])) {
    $gks_webdav_folders_local=GKS_WEBDAV_FOLDERS;
  }
}
//echo '<pre>';print_r($gks_webdav_folders_local);die();

// settings
date_default_timezone_set('UTC');

// If you want to run the SabreDAV server in a custom location (using mod_rewrite for instance)
// You can override the baseUri here.
$baseUri = '/my/dav/';

/* Database */
//$pdo = new PDO('sqlite:data/db.sqlite');
//$pdo = new PDO('mysql:dbname=sabredav_gks_gr;host=127.0.0.1', 'sabredav_gks_gr', 'sabredav_gks_gr236712ffsdf89715');
$pdo = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASSWORD);
$pdo->exec("set names utf8");

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Mapping PHP errors to exceptions
//function exception_error_handler($errno, $errstr, $errfile, $errline) {
//    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
//}
//set_error_handler("exception_error_handler");

// Files we need
require_once '../vendor/autoload.php';

// Backends


//fileserver.php material files
//https://cyberduck.io/download/
//https://www.raidrive.com
//  \\test.easyfilesselection.com@SSL\my\dav\test 
//Choose a custom network location
//HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\WebClient\Parameters
//FileSizeLimitInBytes = 4294967295 -> 50 MB
//BasicAuthLevel DWORD = 2
//services.msc WebClient restart
//https://github.com/sabre-io/dav/blob/master/examples/fileserver.php





$authBackend = new gks_dav\Auth\Backend\gks_PDO($pdo);
$authBackend->setRealm('gkssystem');


$calendarBackend = new gks_dav\CalDAV\Backend\gks_PDO($pdo);
$carddavBackend = new gks_dav\CardDAV\Backend\gks_PDO($pdo);
$principalBackend = new gks_dav\DAVACL\PrincipalBackend\gks_PDO($pdo);

// Directory structure
$tree = [
    new Sabre\CalDAV\Principal\Collection($principalBackend),
    new Sabre\CalDAV\CalendarRoot($principalBackend, $calendarBackend),
    new Sabre\CardDAV\AddressBookRoot($principalBackend, $carddavBackend),
    //fileserver.php
    //new Sabre\DAV\FS\Directory($publicDir),
    //new Sabre\DAV\FS\Directory($publicDir2),
];
foreach ($gks_webdav_folders_local as $key => $value) {
  if ($key!='tmp') {
    $tree[]=new Sabre\DAV\FS\Directory($value);
  }
}


$server = new Sabre\DAV\Server($tree);

if (isset($baseUri)) {
    $server->setBaseUri($baseUri);
}

//fileserver.php
if (count($gks_webdav_folders_local)>=2) {
  // Support for LOCK and UNLOCK
  $lockBackend = new Sabre\DAV\Locks\Backend\File($gks_webdav_folders_local['tmp'].'/locksdb');
  $lockPlugin = new Sabre\DAV\Locks\Plugin($lockBackend);
  $server->addPlugin($lockPlugin);
}


/* Server Plugins */
$authPlugin = new Sabre\DAV\Auth\Plugin($authBackend);
$server->addPlugin($authPlugin);

$aclPlugin = new Sabre\DAVACL\Plugin();
//fileserver.php
//$aclPlugin->allowUnauthenticatedAccess = false; // default
//$server->on('beforeMethod:PROPFIND', function($request, $response) {
//    $path = $request->getPath();
//    if ($path === '' || $path === '/') {
//        // Άφησε να περάσει χωρίς auth check
//        return true; 
//    }
//});

$server->addPlugin($aclPlugin);

/* CalDAV support */
$caldavPlugin = new Sabre\CalDAV\Plugin();
$server->addPlugin($caldavPlugin);

/* Calendar subscription support */
$server->addPlugin(
    new Sabre\CalDAV\Subscriptions\Plugin()
);

/* Calendar scheduling support */
$server->addPlugin(
    new Sabre\CalDAV\Schedule\Plugin()
);

/* WebDAV-Sync plugin */
$server->addPlugin(new Sabre\DAV\Sync\Plugin());

/* CalDAV Sharing support */
$server->addPlugin(new Sabre\DAV\Sharing\Plugin());
$server->addPlugin(new Sabre\CalDAV\SharingPlugin());


$server->addPlugin(new Sabre\CardDAV\Plugin());


// Support for html frontend
$browser = new Sabre\DAV\Browser\Plugin();
$server->addPlugin($browser);


//fileserver.php
if (count($gks_webdav_folders_local)>=2) {
  // Automatically guess (some) contenttypes, based on extension
  $server->addPlugin(new Sabre\DAV\Browser\GuessContentType());
}

//fileserver.php
if (count($gks_webdav_folders_local)>=2) {
  // Temporary file filter
  $tempFF = new \Sabre\DAV\TemporaryFileFilterPlugin($gks_webdav_folders_local['tmp']);
  $server->addPlugin($tempFF);
}

// And off we go!
$server->start();
