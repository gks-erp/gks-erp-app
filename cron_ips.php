<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

set_time_limit(10);
ini_set('max_execution_time', 10);
putenv("ENV=PRODUCTION");

define('SECURE', 1);
require_once('functions.php');
//require_once('_current/_config.php');
//require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-config.php');

require_once('functions_ip.php');

$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

// Additional headers
//$headers .= 'To: '. $to . "\r\n";
$headers .= 'From: kostas@gks.gr' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";

$db_link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

//mail('kostas@gks.gr' , 'stat error' , 'dffffffffff',$headers );

if ($db_link->connect_error) {
    mail('kostas@gks.gr' , 'stat error cron ips' , $db_link->connect_errno . '-'.$db_link->connect_error,$headers );
}
$db_link->set_charset('utf8'); 

ini_set('max_execution_time', 10);
set_time_limit(10);

//debug_mail(false,'cron_ips.php','');

function myBots() {
	global $db_link;
	global $headers;
	
	$query="update gks_stat_ips set isbot =-1 where isbot = 0 and dns_name like '%search.msn.com'";
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  

	$query="update gks_stat_ips set isbot =-1 where isbot = 0 and dns_name like '%.googlebot.com';";
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  

	$query="update gks_stat_ips set isbot =-1 where isbot = 0 and dns_name like 'fulltextrobot-%.seznam.cz';";
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  

	$query="update gks_stat_ips set isbot =-1 where isbot = 0 and dns_name like 'ahrefsbot-%.ahrefs.com';";
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  

	$query="update gks_stat_ips set isbot =-1 where isbot = 0 and dns_name like 'bot.%.wasalive.com';";
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  

	$query="update gks_stat_ips set isbot =-1 where isbot = 0 and dns_name like 'baiduspider-%.crawl.baidu.com';";
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  

	$query="update gks_stat_ips set isbot =-1 where isbot = 0 and dns_name like 'crawl%.archive.org';";
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  

	$query="update gks_stat_ips set isbot =-1 where isbot = 0 and dns_name like '%.crawler.sistrix.net';";
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  
	
}
	

function myIps() {
  global $db_link;
  global $headers;
  
	$table_online="gks_stat_online";
	$table_stat="gks_stat_stat";
	
	$query="INSERT INTO gks_stat_ips ( ip )
	SELECT table1.visitor
	FROM (SELECT ".$table_online.".visitor
	FROM ".$table_online." LEFT JOIN gks_stat_ips ON ".$table_online.".visitor = gks_stat_ips.ip
	WHERE (((gks_stat_ips.ip) Is Null))
	and ".$table_online.".visitor <>''
	GROUP BY ".$table_online.".visitor)  AS table1;";
	 
  //echo $query;
  //die();

	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  

	
	// stats only
//	$query="INSERT INTO gks_stat_ips ( ip )
//			SELECT table1.ip
//			FROM (
//				SELECT ".$table_stat.".ip
//				FROM ".$table_stat." LEFT JOIN gks_stat_ips ON ".$table_stat.".ip = gks_stat_ips.ip
//				WHERE (((gks_stat_ips.ip) Is Null)) and ".$table_stat.".ip <>''
//				and ".$table_stat.".ip is not null
//				GROUP BY ".$table_stat.".ip				
//			)  AS table1;";

	$query="SELECT table1.ip
			FROM (
			  SELECT ip from ".$table_stat." 
			  where ip  <>''
			  group by ip
			)  AS table1
			LEFT JOIN gks_stat_ips ON table1.ip = gks_stat_ips.ip
			WHERE (((gks_stat_ips.ip) Is Null));";
				
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  

	
	$myips=array();
	
	while ($line = $result->fetch_assoc()) {  
		if (isset($line['ip']) and $line['ip']!='') {
			$myips[] = $line['ip'];
		}
	}
	foreach ($myips as $value) {
    $query= "insert into gks_stat_ips (ip) values ('".$value."')";
  	$result2 = $db_link->query($query);
  	if (!$result2) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  
		
	}
	#print_r($myips);
	#die();
	
	$query="SELECT gks_stat_ips.ip, gks_stat_ips.dns_name
	FROM gks_stat_ips
	WHERE (((gks_stat_ips.dns_name) Is Null)) order by ip desc;";
	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  
	
	
	while ($line = $result->fetch_assoc()) {  
		$ip=$line["ip"];
		//echo $ip."|";
		//flush();
		$dns_name=gethostbyaddr($ip)."";
		if ($ip=='localhost') $dns_name='localhost';
		//echo $dns_name."\n";
		
		if ($dns_name!="") {
			$query="update gks_stat_ips set dns_name='".$db_link->escape_string($dns_name)."' where ip='".$db_link->escape_string($ip)."' limit 1;";

    	$result2 = $db_link->query($query);
    	if (!$result2) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  
			
		}
	}
	
	return true;
}








function countryForIP(){
  global $db_link;
  global $headers;
  
	$query="SELECT gks_stat_ips.ip
			FROM gks_stat_ips
			WHERE country_initials is null and gks_stat_ips.ip <>''";

	$result = $db_link->query($query);
	if (!$result) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  


	
	while ($line = $result->fetch_assoc()) {  
		//$iip = ip2long($line['ip']);
		$iip = trim_gks($line['ip']);
		$country_initials=gks_get_country_from_ip($line['ip']);
		
		//echo '|'.$line['ip'].'|'.$iip.'|'.$country_initials.'|<br>';//die();
		if ($country_initials!='') {
			$query="update gks_stat_ips set country_initials = '".$db_link->escape_string($country_initials)."' where ip='".$db_link->escape_string($line['ip'])."' limit 1;";
    	$result3 = $db_link->query($query);
    	if (!$result3) mail('kostas@gks.gr' , 'stat error cron ips' , 'stat cron ips error sql: '.$query,$headers );  
		}
	}
}

function mygks_delete_files_from_temp_folder() {
  
  $mydir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/';



  $myfiles=array();

  $di = new RecursiveDirectoryIterator($mydir);
  $mydirlen=strlen($mydir);
  foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
    if (endwith($filename,'/.') == false and endwith($filename,'/..') == false) {
      $myfiles[] =$filename;
    }
  }
  $time_limit = time() - 24*60*60;
  
  foreach($myfiles as $delfile) {
    $diafora=filemtime($delfile) - $time_limit;
    if ($diafora<0) {
      //echo $diafora.' | '.$delfile.'<br>';
      unlink($delfile);  
        
    }
  }
  
  
  $di = new RecursiveDirectoryIterator($mydir);
  $myfolders=array();
  foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
    if (endwith($filename,'/.') and $filename!=$mydir.'.') { //mono fakelous kai ektos apo ton temp (root)
      $filename=substr($filename, 0,strlen($filename)-1);
      $myfolders[]=$filename;
    }
  }
  
  rsort($myfolders);
  $sort_folders=array();
  foreach ($myfolders as $fitem) {
    $sort_folders[$fitem]=0;
  } 
  
//  print '<pre>';
  foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
    if (endwith($filename,'/.') == false and endwith($filename,'/..') == false) {
      $base_filename=mb_basename($filename);
      $dir_filename=substr($filename, 0, strlen($filename) - strlen($base_filename));
      if (isset($sort_folders[$dir_filename])) $sort_folders[$dir_filename]++;
    }
  }
  
  foreach ($sort_folders as $folder_path => $value) {
    if ($value==0) {
      @rmdir($folder_path);
    }
  }
  //print '<pre>';print_r($sort_folders);
  //die();
    
}

function gks_stat_queue_insert() {
  global $db_link;
  global $headers;
  
	$sql="SELECT * FROM gks_stat_queue order by id limit 10000";

	$result = $db_link->query($sql);
	if (!$result) mail('kostas@gks.gr' , 'gks_stat_queue_insert' , 'stat cron ips error sql: '.$sql,$headers );  
  
  $myvalues=array();
  while ($row = $result->fetch_assoc()) {
    $myvalues[]=$row ;
  }
  $sqls_ins=array();
  $del_id=array();
  foreach ($myvalues as $value) {
    
    $sqls_ins[]=$value['myvalues'];
    $del_id[]=$value['id'];
    if (count($sqls_ins)>=100) {
      $sql="insert into gks_stat_stat (pagetitle,userid,sessionid,username,ip,timevisit,pageurl,query_string,host, userAgent,referer) values ".implode(',',$sqls_ins);
      //echo $sql.'<br>';die();
      $result = $db_link->query($sql);
      
      $sql="delete from gks_stat_queue where id in (".implode(',',$del_id).")";
      //echo $sql.'<br>';die();
      $result = $db_link->query($sql);
      
      $sqls_ins=array();
      $del_id=array();
    }
  }
  
  if (count($sqls_ins)>=1) {
    $sql="insert into gks_stat_stat (pagetitle,userid,sessionid,username,ip,timevisit,pageurl,query_string,host, userAgent,referer) values ".implode(',',$sqls_ins);
    $result = $db_link->query($sql);
    
    $sql="delete from gks_stat_queue where id in (".implode(',',$del_id).")";
    $result = $db_link->query($sql);
  }
  
  
  if (count($myvalues)>0) {
    $sql="lock tables gks_stat_queue WRITE;";
    $result = $db_link->query($sql);
  
    $sql="select count(*) as cc from gks_stat_queue";
    $result = $db_link->query($sql);
    $row = $result->fetch_assoc();
    if ($row['cc']==0) {
      $sql="truncate table gks_stat_queue";  
      $result = $db_link->query($sql);
    }
    $sql="unlock tables;";  
    $result = $db_link->query($sql);
  }

  $sql="truncate table gks_stat_online;";
  $result = $db_link->query($sql);
  
  $sql="insert into gks_stat_online (
  `timevisit`,`session`,`username`,`user_id`,`lasturl`,`query_string`,`host`,`visitor`,`pagetitle`,`userAgent`
  )
  select timevisit,sessionid,username,userid,pageurl,query_string,host,ip,pagetitle,userAgent
  from gks_stat_stat
  where id in (
    SELECT Max(id) AS mid
    FROM gks_stat_stat
    WHERE timevisit>date_sub(now(), interval 30 minute)
    GROUP BY ip, sessionid
  )";
  $result = $db_link->query($sql);
  
}

if (isset($_GET['stat'])) {
  gks_stat_queue_insert();
  if (isset($_GET['redirect'])) {
    header('Location: '.rawurldecode($_GET['redirect']));
  }
  die();  
}

mygks_delete_files_from_temp_folder();
gks_stat_queue_insert();
myIps();
countryForIP();
myBots();
//echo 'fffffffff';