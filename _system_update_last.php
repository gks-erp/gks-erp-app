<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
//_system_update.php



if (file_exists(GKS_SITE_PATH.'gks_erp_maxmind/')==false) {
  if (@mkdir(GKS_SITE_PATH.'gks_erp_maxmind/' , 0755, true) == false ) {
    echo 'can not create dir: '.GKS_SITE_PATH.'gks_erp_maxmind/';die();
  }
}

if (file_exists(GKS_SITE_PATH.'gks_erp_qr_code/')==false) {
  if (@mkdir(GKS_SITE_PATH.'gks_erp_qr_code/' , 0755, true) == false ) {
    echo 'can not create dir: '.GKS_SITE_PATH.'gks_erp_qr_code/';die();
  }
}
if (file_exists(GKS_SITE_PATH.'gks_erp_qr_code/cache/')==false) {
  if (@mkdir(GKS_SITE_PATH.'gks_erp_qr_code/cache/' , 0755, true) == false ) {
    echo 'can not create dir: '.GKS_SITE_PATH.'gks_erp_qr_code/cache/';die();
  }
}

if (file_exists(GKS_SITE_PATH.'gks_erp_qr_code/log/')==false) {
  if (@mkdir(GKS_SITE_PATH.'gks_erp_qr_code/log/' , 0755, true) == false ) {
    echo 'can not create dir: '.GKS_SITE_PATH.'gks_erp_qr_code/log/';die();
  }
}

if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/qr_codes/')==false) {
  if (@mkdir(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/qr_codes/' , 0755, true) == false ) {
    echo 'can not create dir: '.GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/qr_codes/';die();
  }
}

if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/qrconfig.php')==false) {
  echo 'file not exist: '.GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/qrconfig.php';die();
}


$sql="select id_custom_table from gks_custom_table where shortcode_prefix is null or shortcode_prefix=''";
$result = gks_run_sql($sql);
$list=array();
while ($row = $result->fetch_assoc()) $list[]=$row['id_custom_table'];
foreach ($list as $row) {
  $shortcode_prefix=gks_shortcode_prefix_for_custom_table();
  gks_run_sql("update gks_custom_table 
  set shortcode_prefix='".$db_link->escape_string($shortcode_prefix)."'
  where id_custom_table=".$row);
}



$list=gks_FilesObjectList_obj_list();
foreach ($list as $objitem) {
  $object_map=gks_FilesObjectList_map($objitem);
  $object_path=$object_map['path'];
  $object_table=$object_map['table'];
  $object_tid=$object_map['tid'];
  $object_pid=$object_map['pid'];
  
  //print $object_table.'<br>';
  
  $sql="select public_expire_date from ".$object_table." limit 1";
  $result = $db_link->query($sql);
  if (!$result) { //must return error
    gks_run_sql("ALTER TABLE `".$object_table."`
    add column `public_expire_date` datetime DEFAULT NULL,
    add column `public_shortcode` varchar(64) DEFAULT NULL,
    add column `public_myopencount` int NOT NULL DEFAULT 0,
    add column `descr` varchar(190) DEFAULT NULL");
  }
}


$sql="select custom_table_name from gks_custom_table where id_custom_table>=10000";
$result = gks_run_sql($sql);
$gks_custom_table_list=array();
while ($row = $result->fetch_assoc()) $gks_custom_table_list[]=$row['custom_table_name'];
//echo '<pre>';print_r($gks_custom_table_list);die();
foreach ($gks_custom_table_list as $objitem) {
  $object_map=gks_FilesObjectList_map($objitem);
  $object_path=$object_map['path'];
  $object_table=$object_map['table'];
  $object_tid=$object_map['tid'];
  $object_pid=$object_map['pid'];
  
  //echo '<pre>';print_r($object_map);die();
  
  $sql="select public_expire_date from ".$object_table." limit 1";
  $result = $db_link->query($sql);
  if (!$result) { //must return error
    gks_run_sql("ALTER TABLE `".$object_table."`
    add column `public_expire_date` datetime DEFAULT NULL,
    add column `public_shortcode` varchar(64) DEFAULT NULL,
    add column `public_myopencount` int NOT NULL DEFAULT 0,
    add column `descr` varchar(190) DEFAULT NULL");
  }
}



$theme_file='../wp-content/themes/gks_erp_theme/functions.php';
if (file_exists($theme_file)==false) {
  echo '<p style="background-color:red;color:white;">theme gks_erp_theme not exist</p>';die();}

$mu_file='../wp-content/mu-plugins/gks_core_plugin_mu.php';
if (file_exists($mu_file)==false) {
  echo '<p style="background-color:red;color:white;">mu-plugins/gks_core_plugin_mu.php not exist</p>';die();}

$read_file=file_get_contents($mu_file);
if (strpos($read_file, 'gks_erp_theme') === false) {
  echo '<p style="background-color:red;color:white;">update gks_core_plugin_mu.php for the gks_erp_theme</p>';die();}

if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/vendor/symfony/deprecation-contracts/function.php')) {
  echo '<div style="background-color: red;color:white">update vendor</div>';die();}

if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/vendor/phpmailer/phpmailer/src/PHPMailer.kostas.php')==false) {
  echo '<div style="background-color: red;color:white">patch /my/vendor/phpmailer/phpmailer/src/PHPMailer.kostas.php</div>';die();} 
  
$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/vendor/phpmailer/phpmailer/src/PHPMailer.php');
if (strpos($read_file, '//kostas') === false) {
  echo '<p style="background-color:red;color:white;">update PHPMailer.php for the //kostas in /vendor/phpmailer/phpmailer/src/</p>';die();}

    
    
    
// ***************************** mono sto last step ***************************** mono sto last step *****************************    
    //mono sto last step

gks_run_sql("update ".GKS_WP_TABLE_PREFIX."users set gks_menu_version=".time()." where gks_menu_version>0");

gks_run_sql("ALTER TABLE `".GKS_WP_TABLE_PREFIX."users` MODIFY COLUMN `user_registered` DATETIME DEFAULT NULL;");

gks_run_sql("ALTER TABLE `".GKS_WP_TABLE_PREFIX."users` MODIFY COLUMN `user_nicename` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL;");


gks_run_sql("update ".GKS_WP_TABLE_PREFIX."users set user_registered=odbc where user_registered is null");
gks_run_sql("ALTER TABLE `".GKS_WP_TABLE_PREFIX."users` MODIFY COLUMN `user_registered` DATETIME DEFAULT NULL;");


gks_run_sql("ALTER TABLE `".GKS_WP_TABLE_PREFIX."users` MODIFY COLUMN `user_url` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;");


gks_run_sql("UPDATE ".GKS_WP_TABLE_PREFIX."users set mydate_add=now() where mydate_add is null");
gks_run_sql("UPDATE ".GKS_WP_TABLE_PREFIX."users set mydate_edit=now() where mydate_edit is null");



gks_run_sql("UPDATE ".GKS_WP_TABLE_PREFIX."users LEFT JOIN (
  SELECT user_id, meta_value
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE ".GKS_WP_TABLE_PREFIX."usermeta.meta_key='".GKS_WP_TABLE_PREFIX."capabilities'
  and meta_value<>''
) AS mysubq ON ".GKS_WP_TABLE_PREFIX."users.ID = mysubq.user_id 
SET ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities = meta_value
WHERE mysubq.user_id Is Not Null");


gks_run_sql("UPDATE ".GKS_WP_TABLE_PREFIX."users LEFT JOIN (
  SELECT user_id, meta_value
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE ".GKS_WP_TABLE_PREFIX."usermeta.meta_key='wsl_current_user_image'
  and meta_value<>''
) AS mysubq ON ".GKS_WP_TABLE_PREFIX."users.ID = mysubq.user_id 
SET ".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image = meta_value
WHERE mysubq.user_id Is Not Null;");

gks_run_sql("UPDATE ".GKS_WP_TABLE_PREFIX."users LEFT JOIN (
  SELECT user_id, meta_value
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE ".GKS_WP_TABLE_PREFIX."usermeta.meta_key='nickname'
  and meta_value<>''
) AS mysubq ON ".GKS_WP_TABLE_PREFIX."users.ID = mysubq.user_id 
SET ".GKS_WP_TABLE_PREFIX."users.gks_nickname = meta_value
WHERE mysubq.user_id Is Not Null;");

gks_run_sql("UPDATE ".GKS_WP_TABLE_PREFIX."users LEFT JOIN (
  SELECT user_id, meta_value
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE ".GKS_WP_TABLE_PREFIX."usermeta.meta_key='mobile'
  and meta_value<>''
) AS mysubq ON ".GKS_WP_TABLE_PREFIX."users.ID = mysubq.user_id 
SET ".GKS_WP_TABLE_PREFIX."users.gks_mobile = meta_value
WHERE mysubq.user_id Is Not Null;");

gks_run_sql("update ".GKS_WP_TABLE_PREFIX."users
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."users.ID, tblfn.first_name, tblln.last_name, tblbn.display_name,tblnn.nickname,
  TRIM(CONCAT_WS(' ',tblfn.first_name,tblln.last_name)) as text1,
  TRIM(CONCAT_WS(' ',tblbn.display_name)) as text2,
  if (TRIM(CONCAT_WS(' ',tblfn.first_name,tblln.last_name))!='',
     TRIM(CONCAT_WS(' ',tblfn.first_name,tblln.last_name)),
     if (TRIM(CONCAT_WS(' ',tblbn.display_name))!='',
          TRIM(CONCAT_WS(' ',tblbn.display_name)),
          TRIM(CONCAT_WS(' ',tblnn.nickname)))
  ) as forupdate
  FROM (((".GKS_WP_TABLE_PREFIX."users 
  LEFT JOIN (
    SELECT user_id, meta_value AS first_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE meta_value<>'' AND meta_key='first_name'
  ) AS tblfn ON ".GKS_WP_TABLE_PREFIX."users.ID = tblfn.user_id) 
  LEFT JOIN (
    SELECT user_id, meta_value AS last_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE meta_value<>'' AND meta_key='last_name'
  ) AS tblln ON ".GKS_WP_TABLE_PREFIX."users.ID = tblln.user_id) 
  LEFT JOIN (
    SELECT user_id, meta_value AS display_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE meta_value<>'' AND meta_key='display_name'
  ) AS tblbn ON ".GKS_WP_TABLE_PREFIX."users.ID = tblbn.user_id)
  LEFT JOIN (
    SELECT user_id, meta_value AS nickname
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE meta_value<>'' AND meta_key='nickname'
  ) AS tblnn ON ".GKS_WP_TABLE_PREFIX."users.ID = tblnn.user_id
  
  where tblfn.first_name<>'' or tblln.last_name<>'' or tblbn.display_name<>'' or tblnn.nickname<>''
) tblupdate ON ".GKS_WP_TABLE_PREFIX."users.ID = tblupdate.ID 
SET ".GKS_WP_TABLE_PREFIX."users.gks_fullname = forupdate
WHERE tblupdate.ID Is Not Null;");

gks_run_sql("INSERT INTO gks_users (
  user_id, mydate_add, mydate_edit, user_id_add, user_id_edit, myip
)
SELECT ".GKS_WP_TABLE_PREFIX."users.ID, Now() AS mydate_add, Now() AS mydate_edit, 2 AS user_id_add, 2 AS user_id_edit, '127.0.0.1' as myip
FROM ".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id
WHERE (((gks_users.user_id) Is Null));");






//echo time(); die();
if (1==2) {

  $sql="select myvalue from gks_settings where mykey='gks_calc_profilepososto_run_for_all' and myvalue<>''";
  $result=gks_run_sql($sql);
  $gks_calc_profilepososto_run_for_all=0;
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $gks_calc_profilepososto_run_for_all=strtotime($row['myvalue']);
  }
  //echo $gks_calc_profilepososto_run_for_all;die();

  if ($gks_calc_profilepososto_run_for_all==0 or time() > ($gks_calc_profilepososto_run_for_all + 172800)) { // 2 days => 2*24*60*60 => 172800
    //echo 'run calc_profilepososto for all ...<br>'."\n";
    
    $sql="select ID from ".GKS_WP_TABLE_PREFIX."users order by ID";
    $result=gks_run_sql($sql);
    $myids=[];
    while ($row = $result->fetch_assoc()) {    
      $myids[]=$row['ID'];
    }
    foreach ($myids as $id) {
      calc_profilepososto($id,false);
    } 
    $sql="replace into gks_settings (mykey,myvalue) values ('gks_calc_profilepososto_run_for_all','".$db_link->escape_string(date('Y-m-d H:i:s'))."')";
    $result=gks_run_sql($sql);
  }
}




gks_setall_bank_deposit_9digit();
$GKS_PRODUCT_LOTS_SERIALS=true;


$ret=gks_custom_table_db_all_create();
if ($ret['success']==false) {print_r($ret);die();}
//echo '<pre>';print_r($ret);die();

gks_license_get_status();

$latest_json=[];
$sql="select myvalue from gks_settings where mykey='GKS_ERP_APP_LAST_latest.json'";
$result = gks_run_sql($sql);
if ($result->num_rows==1) {
  $row=$result->fetch_assoc();
  $latest_json=$row['myvalue'];
  $latest_json=json_decode($latest_json,true);
}

$temp=array(
  'time'=>time(),
  'latest_json'=>$latest_json,
);
$sql="replace into gks_settings (mykey,myvalue) values ('GKS_ERP_APP_LAST_UDPATE_DATA','".$db_link->escape_string(json_encode($temp,JSON_PRETTY_PRINT))."')";
gks_run_sql($sql);


?>
<div style="background-color: green;color: white;font-size: 150%;padding: 10px;">Successful upgrade / Επιτυχής αναβάθμιση</div>
<div style="font-weight: bold;font-size: 150%;padding: 10px;"><a href="/my">Go to home page / Μετάβαση στην αρχική σελίδα</a></div>
<div style="font-weight: bold;font-size: 150%;padding: 10px;"><a href="/my/admin-update.php">Go to upgrade page / Μετάβαση σελίδα της αναβάθμισης</a></div>
