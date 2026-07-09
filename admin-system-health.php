<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);

$asmy=true;
if (isset($_GET['asmy']) and $_GET['asmy']=='0') {
  $asmy=false;
}

if ($asmy==false) {
  $gks_wordpress_load_not_load_plugins=false;
}

include_once('functions.php');
gks_permission_user_must_login_page();
require_once('functions_ip.php');




//echo 'ffff'.$gks_wordpress_load_not_load_plugins;

//echo time();die();




$my_page_title=gks_lang('Υγεία ιστότοπου');
$nav_active_array=array('manage','manage_settings','manage_system_health');

db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php'); die(); }

include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>



<p style="text-align:center;">
  <a href="?asmy=1" style="<?php if ($asmy==true ) echo 'background-color: yellow;padding: 10px;border: 1px solid blue;';?>" >my</a>
  <a href="?asmy=0" style="<?php if ($asmy==false) echo 'background-color: yellow;padding: 10px;border: 1px solid blue;';?>">wp</a>
 
</p>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>

<style>
.myresults > tbody > tr > td:nth-child(3) {
  text-align:center;
}
.myresults > tbody > tr > td {
  vertical-align: middle;
  overflow-wrap: anywhere;
}
.fa-check-circle {
  font-size:150%;
  color:green;  
}
.fa-times-circle {
  font-size:150%;
  color:red;
}
table.myresults td pre {
  background-color: lightyellow;
  margin: 0;
  white-space: break-spaces;    
}

</style>

<table class="table table-striped table-bordered gkstable myresults" border="0" style="width:96%;max-width:800px;" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr>
      <th class="table-dark" scope="col" nowrap width="0%">#</th>
      <th class="table-dark" scope="col" style="text-align:left;"   nowrap width="30%"><?php echo gks_lang('Ιδιότητα');?></th>
      <th class="table-dark" scope="col" style="text-align:center;" nowrap width="10%"><?php echo gks_lang('Έλεγχος');?></th>
      <th class="table-dark" scope="col" style="text-align:left;"   nowrap width="30%"><?php echo gks_lang('Τιμή','part2');?></th>
      <th class="table-dark" scope="col" style="text-align:left;"   nowrap width="30%"><?php echo gks_lang('Ελάχιστο Προτεινόμενο');?></th>
    </tr>
  
  </thead>
  <tbody>
<?php 

function myicon($myr) {
  //$myr='hh';
  if ($myr) return '<i class="fas fa-check-circle"></i>';
  
  return '<i class="fas fa-times-circle"></i>';;
}

$i=0; 
$phpext=get_loaded_extensions();
//print '<pre>';print_r($phpext);die();

if ( ! function_exists('get_plugins')) {
  require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$mywpplugins=get_plugins();
$mywpplugins_active=get_option('active_plugins');

?>
    
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td>CACHE DB VER</td>
      <td></td>
      <td><?php echo $GKS_CACHE_DB_VER;?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td> cache version</td>
      <td></td>
      <td><?php echo $gks_cache_version;?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td>IDIOTITES CACHE VER</td>
      <td></td>
      <td><?php echo $GKS_IDIOTITES_CACHE_VER;?></td>
      <td></td>
    </tr>
    
    
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td>carddav synctoken</td>
      <td></td>
      <td><?php 
        $sql_event="select myvalue from gks_settings where mykey='carddav_synctoken'";
      	$result_event = $db_link->query($sql_event);  
      	if (!$result_event) {
      	  debug_mail(false,'error sql',$sql_event);
      	  $return = array('success' => false, 'message' => base64_encode('sql error'));
      	  echo json_encode($return); die(); }  
          
        $carddav_synctoken=0;
        if ($result_event->num_rows>=1) {
          $row_event = $result_event->fetch_assoc();
          $carddav_synctoken=intval($row_event['myvalue']);
        }
          
        echo $carddav_synctoken;?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td>calc profilepososto run for all</td>
      <td></td>
      <td><?php 
        $sql_event="select myvalue from gks_settings where mykey='gks_calc_profilepososto_run_for_all'";
      	$result_event = $db_link->query($sql_event);  
      	if (!$result_event) {
      	  debug_mail(false,'error sql',$sql_event);
      	  $return = array('success' => false, 'message' => base64_encode('sql error'));
      	  echo json_encode($return); die(); }  
          
        $gks_calc_profilepososto_run_for_all='-';
        if ($result_event->num_rows>=1) {
          $row_event = $result_event->fetch_assoc();
          $gks_calc_profilepososto_run_for_all=($row_event['myvalue']);
        }
        
        echo $gks_calc_profilepososto_run_for_all;?></td>
      <td></td>
    </tr>
    
    
    
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td>Client IP</td>
      <td></td>
      <td><?php echo $gkIP;?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++;echo $i;?></th>
      <td>maxmind</td>
      <td><?php 
        $test_GR=gks_get_country_from_ip('79.129.62.198');
        echo myicon($test_GR=='GR');?></td>
      <td><?php echo $gkIP.' -> '.gks_get_country_from_ip($gkIP);?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>MySQL</td>
      <td><?php 
        $db_link =  @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($db_link===false) {
          $rt=false;
          $msg=mysqli_connect_errno().'-'.mysqli_connect_error();
        } else {
          $rt=true;
          $result=$db_link->query("select version() as vv");
          $row=$result->fetch_assoc();
          $msg=$row['vv'];
          $parts=explode('-',$msg);
          $rt=version_compare($parts[0],'5.7.0','>=');
        }
        echo myicon($rt);?></td>
      <td><?php echo $msg;?></td>
      <td>5.7</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP Version</td>
      <td><?php echo myicon(version_compare(phpversion(),'7.4.0','>='));?></td>
      <td><?php echo phpversion();?></td>
      <td>7.4</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP apc</td>
      <td><?php echo myicon(in_array('apc',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP apcu</td>
      <td><?php echo myicon(in_array('apcu',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP bcmath</td>
      <td><?php echo myicon(in_array('bcmath',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP bz2</td>
      <td><?php echo myicon(in_array('bz2',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP calendar</td>
      <td><?php echo myicon(in_array('calendar',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP ctype</td>
      <td><?php echo myicon(in_array('ctype',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP curl</td>
      <td><?php echo myicon(in_array('curl',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP date</td>
      <td><?php echo myicon(in_array('date',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP dom</td>
      <td><?php echo myicon(in_array('dom',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP exif</td>
      <td><?php echo myicon(in_array('exif',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP fileinfo</td>
      <td><?php echo myicon(in_array('fileinfo',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP gd</td>
      <td><?php echo myicon(in_array('gd',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP gettext</td>
      <td><?php echo myicon(in_array('gettext',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP hash</td>
      <td><?php echo myicon(in_array('hash',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>    
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP iconv</td>
      <td><?php echo myicon(in_array('iconv',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>    
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP imagick</td>
      <td><?php echo myicon(in_array('imagick',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>    
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP intl</td>
      <td><?php echo myicon(in_array('intl',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP libxml</td>
      <td><?php echo myicon(in_array('libxml',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP mbstring</td>
      <td><?php echo myicon(in_array('mbstring',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP mcrypt</td>
      <td><?php echo myicon(in_array('mcrypt',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP memcached</td>
      <td><?php echo myicon(in_array('memcached',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP mysqli</td>
      <td><?php echo myicon(in_array('mysqli',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP pdo_mysql</td>
      <td><?php echo myicon(in_array('pdo_mysql',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP openssl</td>
      <td><?php echo myicon(in_array('openssl',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP pcre</td>
      <td><?php echo myicon(in_array('pcre',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP readline</td>
      <td><?php echo myicon(in_array('readline',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP redis</td>
      <td><?php echo myicon(in_array('redis',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP SimpleXML</td>
      <td><?php echo myicon(in_array('SimpleXML',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP soap</td>
      <td><?php echo myicon(in_array('soap',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP xml</td>
      <td><?php echo myicon(in_array('xml',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP xmlreader</td>
      <td><?php echo myicon(in_array('xmlreader',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP xmlrpc</td>
      <td><?php echo myicon(in_array('xmlrpc',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP xmlwriter</td>
      <td><?php echo myicon(in_array('xmlwriter',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP xsl</td>
      <td><?php echo myicon(in_array('xsl',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP Zend OPcache</td>
      <td><?php echo myicon(in_array('Zend OPcache',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP zip</td>
      <td><?php echo myicon(in_array('zip',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP zlib</td>
      <td><?php echo myicon(in_array('zlib',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP rar</td>
      <td><?php echo myicon(in_array('rar',$phpext));?></td>
      <td></td>
      <td></td>
    </tr>
    
    
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP display_errors</td>
      <td><?php echo myicon(ini_get('display_errors')=='on');?></td>
      <td><?php echo ini_get('display_errors'); ?></td>
      <td>on</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP log_errors</td>
      <td><?php echo myicon(ini_get('log_errors')=='1' or ini_get('log_errors')=='on');?></td>
      <td><?php echo ini_get('log_errors'); ?></td>
      <td>on</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP post_max_size</td>
      <td><?php echo myicon(gks_return_bytes(ini_get('post_max_size')) >= 16*1024*1024);?></td>
      <td><?php echo ini_get('post_max_size'); ?></td>
      <td>16Μ</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP upload_max_filesize</td>
      <td><?php echo myicon(gks_return_bytes(ini_get('upload_max_filesize')) >= 16*1024*1024);?></td>
      <td><?php echo ini_get('upload_max_filesize'); ?></td>
      <td>16Μ</td>
    </tr>
    
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP max_input_vars</td>
      <td><?php echo myicon(intval(ini_get('max_input_vars'))>=10000);?></td>
      <td><?php echo ini_get('max_input_vars'); ?></td>
      <td>10000</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP memory_limit</td>
      <td><?php echo myicon(gks_return_bytes(ini_get('memory_limit'))>=512*1024*1024);?></td>
      <td><?php echo ini_get('memory_limit'); ?></td>
      <td>512M</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP max_execution_time</td>
      <td><?php echo myicon(intval(ini_get('max_execution_time'))>=600);?></td>
      <td><?php echo ini_get('max_execution_time'); ?></td>
      <td>600</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP max_input_time</td>
      <td><?php echo myicon(intval(ini_get('max_input_time'))>=600);?></td>
      <td><?php echo ini_get('max_input_time'); ?></td>
      <td>600</td>
    </tr>
    
    
    
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>PHP open_basedir</td>
      <td></td>
      <td><?php echo ini_get('open_basedir'); ?></td>
      <td></td>
    </tr>
    
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>Web Server</td>
      <td><?php 
        $res_web_server=false;
        $sss=$_SERVER['SERVER_SOFTWARE'];
        $parts=explode('/',$sss);
        if (count($parts)==2 and $parts[0]=='nginx') {
          $res_web_server=myicon(version_compare($parts[1],'1.21.0','>='));
        }
        echo myicon($res_web_server);?></td>
      <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
      <td>nginx/1.21.0</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>Web Server User</td>
      <td><?php echo myicon(isset($_SERVER['USER']) and $_SERVER['USER']==='www-data');?></td>
      <td><?php echo (isset($_SERVER['USER']) ? $_SERVER['USER'] : 'not set'); ?></td>
      <td>www-data</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>WordPress Version</td>
      <td><?php echo myicon(version_compare($wp_version,'5.8.1','>='));?></td>
      <td><?php echo $wp_version; ?></td>
      <td>5.6</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>WordPress ABSPATH</td>
      <td></td>
      <td><?php echo ABSPATH; ?></td>
      <td></td>
    </tr>

    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>WordPress wp-config.php</td>
      <td><?php 
        $temp='';
        if (file_exists(ABSPATH.'wp-config.php')) {
          $data=file_get_contents(ABSPATH.'wp-config.php');
          if (strpos($data, 'stat_record_wp') === false) {
            echo myicon(false);
          } else {
            echo myicon(true);
          }
        }
        ?></td>
      <td></td>
      <td>stat_record_wp</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>WordPress wp-content/advanced-cache.php</td>
      <td><?php 
        $temp='';
        if (file_exists(ABSPATH.'wp-content/advanced-cache.php')) {
          $data=file_get_contents(ABSPATH.'wp-content/advanced-cache.php');
          if (strpos($data, 'if (isset($gks_wordpress_load_not_load_plugins) and  $gks_wordpress_load_not_load_plugins==true) return;') === false) {
            echo myicon(false);
            $temp='<div>Add this to advanced-cache.php</div><pre>if (isset($gks_wordpress_load_not_load_plugins) and  $gks_wordpress_load_not_load_plugins==true) return;</pre>';
          } else {
            echo myicon(true);
          }
        }
        ?></td>
      <td style="align-text:left;"><?php echo $temp?></td>
      <td>return</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>WordPress my/.user.ini</td>
      <td><?php 
        $temp='';
        if (file_exists(ABSPATH.'my/.user.ini')) {
          $data=file_get_contents(ABSPATH.'my/.user.ini');
          if (strpos($data, "auto_prepend_file = ''") === false) {
            echo myicon(false);
            $temp= '<pre">'.$data.'</pre>';
          } else {
            echo myicon(true);
            $temp= '<pre>'.$data.'</pre>';
          }
        } else {
          echo myicon(false);
          $temp= 'Not found .user.ini';
        }
        ?></td>
      <td style="align-text:left;"><?php echo $temp?></td>
      <td>auto_prepend_file = ''</td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>Wordfence PHP auto_prepend_file</td>
      <td><?php 
        $temp_value=trim_gks(ini_get('auto_prepend_file'));
        echo myicon(trim_gks($temp_value)=='');
        
        ?></td>
      <td><?php echo '<pre>'.ini_get('auto_prepend_file').'</pre>'; ?></td>
      <td><?php echo gks_lang('Κενό');?></td>
    </tr>
    
    
    
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>Members</td>
      <td><?php echo myicon(isset($mywpplugins['members/members.php']) and in_array('members/members.php',$mywpplugins_active));?></td>
      <td><?php if (isset($mywpplugins['members/members.php'])) echo $mywpplugins['members/members.php']['Version']; ?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>Yoast SEO</td>
      <td><?php echo myicon(isset($mywpplugins['wordpress-seo/wp-seo.php']) and in_array('wordpress-seo/wp-seo.php',$mywpplugins_active));?></td>
      <td><?php if (isset($mywpplugins['wordpress-seo/wp-seo.php'])) echo $mywpplugins['wordpress-seo/wp-seo.php']['Version']; ?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>Under Construction</td>
      <td><?php echo myicon(isset($mywpplugins['under-construction-page/under-construction.php']) and in_array('under-construction-page/under-construction.php',$mywpplugins_active));?></td>
      <td><?php if (isset($mywpplugins['under-construction-page/under-construction.php'])) echo $mywpplugins['under-construction-page/under-construction.php']['Version']; ?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>Wordfence Security</td>
      <td><?php echo myicon(isset($mywpplugins['wordfence/wordfence.php']) and in_array('wordfence/wordfence.php',$mywpplugins_active));?></td>
      <td><?php if (isset($mywpplugins['wordfence/wordfence.php'])) echo $mywpplugins['wordfence/wordfence.php']['Version']; ?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>W3 Total Cache</td>
      <td><?php echo myicon(isset($mywpplugins['w3-total-cache/w3-total-cache.php']) and in_array('w3-total-cache/w3-total-cache.php',$mywpplugins_active));?></td>
      <td><?php if (isset($mywpplugins['w3-total-cache/w3-total-cache.php'])) echo $mywpplugins['w3-total-cache/w3-total-cache.php']['Version']; ?></td>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>WP Mail SMTP</td>
      <td><?php echo myicon(isset($mywpplugins['wp-mail-smtp/wp_mail_smtp.php']) and in_array('wp-mail-smtp/wp_mail_smtp.php',$mywpplugins_active));?></td>
      <td><?php if (isset($mywpplugins['wp-mail-smtp/wp_mail_smtp.php'])) echo $mywpplugins['wp-mail-smtp/wp_mail_smtp.php']['Version']; ?></td>
      <td></td>
    </tr>
    

      
      
  </tbody>
</table>

<?php
//echo '<pre>'; print_r($mywpplugins_active);echo '</pre>';
//echo '<pre>'; print_r($mywpplugins);echo '</pre>';

?>
<p>&nbsp;</p>
<?php $i=0;?>

<table class="table table-striped table-bordered gkstable myresults" border="0" style="width:96%;max-width:800px;" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr>
      <th class="table-dark" scope="col" nowrap width="0%">#</th>
      <th class="table-dark" scope="col" style="text-align:left;"   nowrap width="50%"><?php echo gks_lang('Παράμετρος');?></th>
      <th class="table-dark" scope="col" style="text-align:left;"   nowrap width="50%"><?php echo gks_lang('Τιμή','part2');?></th>

    </tr>
  
  </thead>
  <tbody>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>Theme</td>
      <td style="text-align:left"><?php echo get_template();?></td>
    </tr>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td>StyleSheet</td>
      <td style="text-align:left"><?php echo get_option('stylesheet');?></td>
    </tr>



</table>

<p>&nbsp;</p>

<table class="table table-striped table-bordered gkstable myresults" border="0" style="width:96%;max-width:800px;" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr>
      <th class="table-dark" scope="col" nowrap width="0%">#</th>
      <th class="table-dark" scope="col" style="text-align:left;"   nowrap width="100%"><?php echo gks_lang('Ενεργά plugins');?></th>
    </tr>
  </thead>
  <tbody>
  <?php
  $i=0;
  $mylist=array_merge($mywpplugins_active,wp_get_mu_plugins());
  foreach ($mylist as $value) {
  //foreach (wp_get_active_and_valid_plugins() as $value) {
  
  ?>
    <tr>
      <th scope="row"><?php $i++; echo $i;?></th>
      <td><?php echo $value;?></td>
    </tr>
  <?php } ?>    
  </tbody>
</table>



<?php
//<pre>
//print_r($mywpplugins);
//print_r(get_option('active_plugins'));
//</pre>v
?>


<p>&nbsp;</p>

<style>
.myservervalues > tbody > tr > td{
  overflow-wrap: anywhere;
}  
</style>  
<table class="table table-striped table-bordered gkstable myservervalues" border="0" style="width:96%;max-width:800px;" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr>
      <th class="table-dark" scope="col" nowrap width="40%">SERVER Key</th>
      <th class="table-dark" scope="col" nowrap width="60%">SERVER Value</th>
    </tr>
  </thead>
  <tbody>
    
<?php
  foreach ($_SERVER as $key => $value) {
?>
    <tr>
      <th scope="row"><?php echo $key;?></th>
      <td><?php 
        if (is_string($value)) echo $value;
        else if (is_array($value)) print_r($value); 
        ?></td>  
    </tr>

<?php
  } 
?>    
 
  </tbody>
</table>
  
<?php

//print '<pre>';
//print_r(get_included_files());
//print '</pre>';

include_once('_my_footer_admin.php');


