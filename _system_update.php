<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

//_system_update.php

ini_set('max_execution_time', 6000);
set_time_limit(6000);


putenv("ENV=PRODUCTION");

define('SECURE', 1);
require_once('_current/_config.php');

if (defined('GKS_SITE_HTTPDOCS') === false) {
  echo '_current/_config.php file not contains GKS_SITE_HTTPDOCS<br>';die();}

$file_gks_core_config1=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/gks_core_config.php';
$file_gks_core_config2=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/plugins/gks_core/_current/_config.php';
if (file_exists($file_gks_core_config1)==true and file_exists($file_gks_core_config2)==false) {
  @rename($file_gks_core_config1,$file_gks_core_config2);
}
//echo $file_gks_core_config1.'|'.$file_gks_core_config2;die();

function gks_update_movenext() {
  global $gks_version_exist;
  global $db_link;
  global $error_count;

  //die('kostas');
  
  $sql="update gks_settings set myvalue='".($gks_version_exist + 1) ."' where mykey='GKS_CACHE_DB_VER'";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error);}

  echo '<p>End Updating step</p>';
  echo '<p>Go to next step ...</p>';
  if ($error_count==0) {
    echo '
    <script>
      window.setTimeout(function() {
        window.location.href="_system_update.php?version='.($gks_version_exist + 1).'";
      }, 2000);
    </script>
    
    ';  
  }
  die();
}

function gks_run_sql($sql) {
  global $db_link;
  global $error_count;
  $result = $db_link->query($sql);
  if (!$result) {
    echo '<p>Error: <pre>'.htmlspecialchars($sql).'</pre><br>'.
    'Error number: '.$db_link->errno . '<br>'.
    'Error descri: '.$db_link->error.
    '</p>';
    $error_count++;
    die();
  }
  return $result;
}

require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-config.php');
$db_link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($db_link->connect_error) {
    echo $db_link->connect_errno . '-'.$db_link->connect_error; die();
}
$db_link->set_charset('utf8');
$db_link->query("SET time_zone = '+00:00';");

$sql="select idd_lang from gks_lang limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  
  gks_run_sql("ALTER TABLE `gks_lang` 
  ADD COLUMN `idd_lang` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `id_lang`,
  ADD COLUMN `lang_sortorder` INTEGER UNSIGNED NOT NULL DEFAULT 1000,
  ADD INDEX `lang_sortorder`(`lang_sortorder`)
  ");


 
  gks_run_sql("update gks_lang set idd_lang=1,lang_sortorder=1,lang_on_backend=1 where id_lang='el-GR'");
  gks_run_sql("update gks_lang set idd_lang=2,lang_sortorder=2 where id_lang='en-US';");
  gks_run_sql("update gks_lang set idd_lang=3,lang_sortorder=3 where id_lang='fr-FR';");
  gks_run_sql("update gks_lang set idd_lang=4,lang_sortorder=4 where id_lang='de-DE';");
  gks_run_sql("update gks_lang set idd_lang=5,lang_sortorder=5 where id_lang='it-IT';");
  gks_run_sql("update gks_lang set idd_lang=6,lang_sortorder=6,id_lang='sr-RS' where id_lang='sr-RS' or id_lang='sr_RS';");
  gks_run_sql("update gks_lang set idd_lang=7,lang_sortorder=7 where id_lang='bg-BG';");
  gks_run_sql("update gks_lang set idd_lang=8,lang_sortorder=8 where id_lang='sq-AL';");
  gks_run_sql("update gks_lang set idd_lang=10,lang_sortorder=10 where id_lang='tr-TR';");


  gks_run_sql("ALTER TABLE `gks_lang`
  MODIFY COLUMN `idd_lang` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY  USING BTREE(`idd_lang`),
  ADD UNIQUE INDEX `idunique`(`id_lang`);");
  
  gks_run_sql("ALTER TABLE `gks_lang` AUTO_INCREMENT = 10001;");
  
  gks_run_sql("ALTER TABLE `gks_lang` 
  MODIFY COLUMN `id_lang` VARCHAR(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  MODIFY COLUMN `lang_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  DROP INDEX `lang_name`,
  ADD INDEX `lang_name` USING BTREE(`lang_name`(250)),
  MODIFY COLUMN `lang_ico` VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  add column `mydate_add` datetime DEFAULT NULL,
  add column `mydate_edit` datetime DEFAULT NULL,
  add column `user_id_add` int(11) NOT NULL DEFAULT 0,
  add column `user_id_edit` int(11) NOT NULL DEFAULT 0,
  add column `myip` varchar(48) DEFAULT NULL,
  ADD INDEX `mydate_edit` (`mydate_edit`),
  ADD INDEX `user_id_edit` (`user_id_edit`)
  ");

  gks_run_sql("update gks_lang set mydate_add=now(),mydate_edit=now(),user_id_add=2,user_id_edit=2,myip='127.0.0.1';");

  
  gks_run_sql("insert into gks_settings (
  mykey,myvalue
  ) values (
  'GKS_LANG_DATA_ENABLED','a:1:{i:0;s:5:\"el-GR\";}'
  )");
   
}

$db_link->close();
$db_link = null;
  
//echo 'dddd';die();
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');


//$my_wp_user_id=2;
gks_permission_user_must_login_page(true);

db_open();


//debug_mail(false,'_system_update run','');//die();

$del_files = array_diff(scandir(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/'), array('..', '.'));
foreach ($del_files as $del_file) {
  $temp=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/'.$del_file;
  //echo 'delete file: '.$temp.'<br>';
  unlink($temp);  
}


$gks_version_run=0; if (isset($_GET['version'])) $gks_version_run=intval($_GET['version']);

$sql="select myvalue from gks_settings where mykey='GKS_CACHE_DB_VER'";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die();}

$row = $result->fetch_assoc();
$gks_version_exist=intval($row['myvalue']);

//echo '<pre>';
echo '<!DOCTYPE html>
<html>
<head>
    <title>gks Update</title>
</head>
<body style="background-color:lightgray;font-family:Courier;font-size: 14px;">
';
echo '<h1>System Update</h1>';
echo '<p>Current Version: '.$gks_version_exist.'</p>';
echo '<p>Start Updating ...</p>';
$error_count=0;

$sql="select option_value from ".GKS_WP_TABLE_PREFIX."options where option_name='".GKS_WP_TABLE_PREFIX."user_roles'";
$result = gks_run_sql($sql);
if ($result->num_rows!=1) die('error: user roles, members');
$row = $result->fetch_assoc();
$option_value=$row['option_value'];
$myroles=unserialize($option_value);
//unset($myroles['promitheutis']);unset($myroles['ordermanager']);
if (isset($myroles['adminmy'])==false) $myroles['adminmy']=array('name' => 'Διαχειριστής my','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['customer'])==false) $myroles['customer']=array('name' => 'Πελάτης','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['promitheutis'])==false) $myroles['promitheutis']=array('name' => 'Προμηθευτής','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['employee'])==false) $myroles['employee']=array('name' => 'Υπάλληλος','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['logistis'])==false) $myroles['logistis']=array('name' => 'Λογιστής','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['ordermanager'])==false) $myroles['ordermanager']=array('name' => 'Διαχειριστής Παραγγελιών','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['hrmanager'])==false) $myroles['hrmanager']=array('name' => 'HR Manager','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['apothikarios'])==false) $myroles['apothikarios']=array('name' => 'Αποθηκάριος','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['driver'])==false) $myroles['driver']=array('name' => 'Οδηγός','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['tamias'])==false) $myroles['tamias']=array('name' => 'Ταμίας','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['texnikos'])==false) $myroles['texnikos']=array('name' => 'Τεχνικός','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['timologisi'])==false) $myroles['timologisi']=array('name' => 'Τιμολόγηση','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['salesman'])==false) $myroles['salesman']=array('name' => 'Πωλητής','capabilities' => array('read' => 1, 'level_0' => 1));
if (isset($myroles['externalpartner'])==false) $myroles['externalpartner']=array('name' => 'Εξωτερικός Συνεργάτης','capabilities' => array('read' => 1, 'level_0' => 1));

//print '<pre>';print_r($myroles);die('kostas');
$myroles=serialize($myroles);
$sql="update ".GKS_WP_TABLE_PREFIX."options set option_value='".$db_link->escape_string($myroles)."' where option_name='".GKS_WP_TABLE_PREFIX."user_roles'";
gks_run_sql($sql);


switch ($gks_version_exist) {
  case 1: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 2: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 3: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 4: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 5: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 6: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 7: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 8: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 9: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 10: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 11: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 12: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 13: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 14: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 15: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 16: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 17: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 18: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 19: require_once('_system_update'.$gks_version_exist.'.php'); gks_update_movenext(); break;  
  case 20: require_once('_system_update'.$gks_version_exist.'.php'); 
  
    require_once('_system_update_last.php');  
    


    
    //echo time(); 
    die();

    
    gks_update_movenext();
    break;  

    
    
  default: 
    echo '<p style="color:red">nginx html cache html expires max;</p>';
    echo '<p style="color:red">try_files $uri $uri/ /my/s/index.php?$args;</p>';
    echo '<p>Nothing to do more</p>';

}

echo '<p>End</p>
</body>
</html>';
//echo $version_run.' | '.$version_exist.' | '.time();
die();



