<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

//	umy.gr top level domain free
ini_set('memory_limit', '-1');

if (SECURE != 1) { die('ACCESS_DENIED'); }
$dev_page_starttime=microtime(true);
 
require_once('_current/_config.php');


$autocomplete_gks_disable='off'; 
//old is: autocomplete_disable_gks
//na allaxthei kai sto js/_gks_inc.js

$my_time_for_version = time();
$my_ver_after_update    = '.1';


//echo ENV; die();

switch (ENV) {
	case 'DEVELOPMENT':
		//error_reporting(E_ALL | E_STRICT);
		error_reporting(E_ALL);
	  define('__VERSION__', date('Y',$my_time_for_version).'.'.date('m',$my_time_for_version).'.'.date('d',$my_time_for_version).$my_ver_after_update .$my_time_for_version) ;  
		
		header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // Date in the past
		header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" ); // always modified
		header ( "Cache-Control: no-cache, must-revalidate, no-store, max-age=0" ); // HTTP/1.1
	  header ( "Cache-Control: post-check=0, pre-check=0", false );
		header ( "Pragma: no-cache" );
		ini_set('display_errors', 'on');
		ini_set('display_startup_errors', 'on');
		ini_set('log_errors',1);
		break;

	case 'PRODUCTION':
		error_reporting(E_ALL | E_STRICT);
		define('__VERSION__', date('Y',$my_time_for_version).'.'.date('m',$my_time_for_version).'.'.date('d',$my_time_for_version).$my_ver_after_update);  
		header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // Date in the past
		header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" ); // always modified
		header ( "Cache-Control: no-cache, must-revalidate, no-store, max-age=0" ); // HTTP/1.1
	  header ( "Cache-Control: post-check=0, pre-check=0", false );
		header ( "Pragma: no-cache" );
		ini_set('display_errors', 'off');
		ini_set('display_startup_errors', 'off');
		ini_set('log_errors',1);
	default:
		break;
}






require_once('gks_cache_version.php');


if (isset($gks_wordpress_load_not_load_plugins)==false) $gks_wordpress_load_not_load_plugins=true;




//echo time();

//if (GKS_SITE_URL=='https://vetshop.gr/') {
//  require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-content/plugins/woocommerce-status-actions/includes/functions.php');
//}

   
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-config.php');

//die('ddddddd');



if (isset($table_prefix)) {
  define('GKS_WP_TABLE_PREFIX',$table_prefix);
} else {
  define('GKS_WP_TABLE_PREFIX','wp_');
}
//echo GKS_WP_TABLE_PREFIX;die();

//     '".GKS_WP_TABLE_PREFIX."
//     ".GKS_WP_TABLE_PREFIX."users.gks_nickname'),
//     '".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname'),

//     vv2wp_capabilities
//     vv2wp_options
//     vv2wp_user_roles


//echo '[f '.number_format( memory_get_usage()/1024/1024,1,',','.').' f]';


      
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-includes/class-wpdb.php'); //wp-db.php
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/wp-includes/pluggable.php');



include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_lang_ui.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_lang_data.php');


include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_cache.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_wp.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_wp2.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_wp3.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_wp4.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_sms.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_email.php');

include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eshop.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eshop2.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eshop3.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_dp.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_orders.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_orders2.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_xxx_xxx.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_acc_inv.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_acc_pay.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_whi.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_hotel.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_cm.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_production.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_crm_task.php');


include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_aade.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_aade_2.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_aade_3.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_aade_4.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_aade_ap_lianikis.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_print.php');

include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_woo.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_woo2.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_woo3.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_woo4.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_custom.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_permission.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_export_excel.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_gks_erp_app.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_gks_erp_app_mobile.php');


include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_cookie.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_booking_com.php');

include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_calendar.php');
if (defined('GKS_TRANSFER') and GKS_TRANSFER and file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_transfer.php')) {
  include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_transfer.php');
}
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_viva.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_mellon.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_epay.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_worldline.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_nexi.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_filesobjectlist.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_sociallinks.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_paroxos.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_eftpos.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_customtableview.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_ads.php');
include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_voip.php');


include_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions_plugins.php');

require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/vendor/autoload.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/vendor/autoload.php');




ini_set('max_execution_time',600); // 600 = 10 minutes

$gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
if ($gkIP=='') $gkIP='127.0.0.1';

$my_wp_user_id=get_current_user_id();
$my_wp_user_info=wp_get_current_user(); //get_currentuserinfo()
//echo '<pre>';var_dump(wp_get_current_user());die();

$my_thumbnail_width = 300;
$my_thumbnail_height = 200;
$my_preview_width = 1500;
$my_preview_height = 1000;
//$my_dp = 2;


$gks_user_settings=array();
$GKS_USERS_ACCESS_ROLES=array(
  'logistis',
  'hrmanager',
  'ordermanager',
  //'omadarxis',
  'tamias',
  'texnikos',
  'timologisi',
  'salesman',
  'apothikarios',
  'driver',
  'employee',
);

$GKS_SITE_HUMAN_NAME=GKS_SITE_URL; //'_GKS_SITE_HUMAN_NAME_';
$GKS_OFFICIAL_SITE_URL=GKS_SITE_URL; //https://www.gks.gr
$GKS_SITE_NAME=GKS_SITE_URL; // gks on web

$GKS_SITE_EMAIL=''; //'info@'.$_SERVER['HTTP_HOST'];
$GKS_EMAIL_BCC1='';
$GKS_EMAIL_BCC2='';
$GKS_EMAIL_BCC3='';
$GKS_EMAIL_HOST=''; //'www.gks.gr';
$GKS_EMAIL_PORT=587;
$GKS_EMAIL_SMTPAUTH=true;
$GKS_EMAIL_USERNAME=''; //'info@'.$_SERVER['HTTP_HOST'];
$GKS_EMAIL_PASSWORD='';


$GKS_PRODUCT_DESCR_SMALL=true;
$GKS_PRODUCT_DESCR_BIG=true;

$GKS_ORDERS_ENABLE=true;
$GKS_ORDERS_OCCASION=true;
$GKS_ORDERS_PRODUCTION=true;
$GKS_CRM_ENABLE=true;
$GKS_CRM_LEADS_ENABLE=true;
$GKS_CRM_TASKS_ENABLE=true;
$GKS_CRM_MACHINE_ENABLE=true;


$GKS_ACC_ENABLE=true;

$GKS_WARE_HOUSE_ENABLE=true;
$GKS_PRODUCT_LOTS_SERIALS=false;


$GKS_ORDERS_AWS=false;
$GKS_ORDERS_SETS=false;
$GKS_ORDERS_SETS_VALS='';
$GKS_ORDERS_SHEETS=false;
$GKS_ORDERS_COL_ITEMPRICE=false;
$GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA=false;
$GKS_ORDERS_COL_FPA=true;




$GKS_ACC_INV_COL_ITEMPRICE=true;
$GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA=false;
$GKS_ACC_INV_COL_FPA=true;
$GKS_ACC_INV_EXTRA_OPEN=false;

$GKS_ASSETS_ENABLE=true;

$GKS_BASKET_CALC_ITEM_DECIMAL=4;
$GKS_BASKET_CALC_EKPTOSI_DECIMAL=4;

$GKS_NUMBER_FORMAT_DECIMAL=',';
$GKS_NUMBER_FORMAT_THOUSAND='.';
$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL=2;
$GKS_NUMBER_FORMAT_CURRENCY_SYMBOL='&euro;';
$GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW='after';
$GKS_NUMBER_FORMAT_DATE='d/m/Y';
$GKS_NUMBER_FORMAT_TIME='H:i:s';
$GKS_HOTEL_DAYS_FUTURE=10; //365

//$GKS_HOTEL_CHILD_AGE_PRICE=array();
//$GKS_HOTEL_CHILD_AGE_PRICE[] = array('from' => 0, 'to' => 2, 'price' => 0,  'type' => 'night');
//$GKS_HOTEL_CHILD_AGE_PRICE[] = array('from' => 3, 'to' => 6, 'price' => 10, 'type' => 'night');
//$GKS_HOTEL_CHILD_AGE_PRICE[] = array('from' => 7, 'to' => 17, 'price' => 20, 'type' => 'stay');
//$GKS_HOTEL_CHILD_KOUNIES=array('enable' => false, 'from' => 0, 'to' => 0, 'price' => 0, 'type' => 'night');
//$GKS_HOTEL_EXTRA_BEDS=array(
//  'enabled' => false,
//  'beds' => array(),
//);
//$GKS_HOTEL_EXTRA_BEDS['beds'][] = array('from' => 0, 'to' => 2, 'price' => 0,  'type' => 'night');
//$GKS_HOTEL_EXTRA_BEDS['beds'][] = array('from' => 3, 'to' => 6, 'price' => 10, 'type' => 'night');;
//$GKS_HOTEL_EXTRA_BEDS['beds'][] = array('from' => 7, 'to' => 17, 'price' => 20, 'type' => 'stay');


$GKS_PAYPAL_REAL_USERNAME='';
$GKS_PAYPAL_REAL_PASSWORD='';
$GKS_PAYPAL_REAL_SIGNATURE='';
$GKS_PAYPAL_REAL_CLIENT_ID='';
$GKS_PAYPAL_REAL_SECRET='';
$GKS_PAYPAL_SANDBOX=true;
$GKS_PAYPAL_SAND_USERNAME='';
$GKS_PAYPAL_SAND_PASSWORD='';
$GKS_PAYPAL_SAND_SIGNATURE='';
$GKS_PAYPAL_SAND_CLIENT_ID='';
$GKS_PAYPAL_SAND_SECRET='';

$GKS_ALPHABANK_REAL_MID='';
$GKS_ALPHABANK_REAL_KEY='';
$GKS_ALPHABANK_REAL_URL='';
$GKS_ALPHABANK_SAND_MID='';
$GKS_ALPHABANK_SAND_KEY='';
$GKS_ALPHABANK_SAND_URL='';

$GKS_PIRAEUSBANK_SAND_AcquirerID='';
$GKS_PIRAEUSBANK_SAND_MerchantID='';
$GKS_PIRAEUSBANK_SAND_PosID='';
$GKS_PIRAEUSBANK_SAND_UserName='';
$GKS_PIRAEUSBANK_SAND_Password='';
$GKS_PIRAEUSBANK_SANDBOX=true;
$GKS_PIRAEUSBANK_REAL_AcquirerID='';
$GKS_PIRAEUSBANK_REAL_MerchantID='';
$GKS_PIRAEUSBANK_REAL_PosID='';
$GKS_PIRAEUSBANK_REAL_UserName='';
$GKS_PIRAEUSBANK_REAL_Password='';

$GKS_AWS_BUCKET='';
$GKS_AWS_KEY='';
$GKS_AWS_SECRET='';
$GKS_AWS_FOLDER='';

$GKS_SEND_ANYWHERE_API_KEY='';
$GKS_ORDER_DEFAULT_DELIVERY=0;
$GKS_ORDER_DEFAULT_PAYMENT=0;
$GKS_ORDER_DEFAULT_PAYMENT_HOTEL=0;
$GKS_ORDER_DEFAULT_PAYMENT_TRANSFER=0;
$GKS_GOOGLE_MAPS_API_KEY='';
$GKS_GOOGLE_MAPS_API_KEY_SERVER='';


$GKS_CACHE_DB_VER=0;
$GKS_IDIOTITES_CACHE_VER=0;

$GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK=false;
$GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK=false;
$GKS_BASKET_ROUND_DIAFORA_001=true;
$GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI=true;
$GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI=false;
$GKS_INPUT_STEP_AJIA='0.01';
$GKS_INPUT_STEP_POSOTITA='1';
$GKS_INPUT_STEP_POSOSTO='0.01';
$GKS_AADE_MYDATA_SANDBOX_AFM='';
$GKS_AADE_MYDATA_SANDBOX_BRANCE=-1;
$GKS_AADE_MYDATA_SANDBOX_USER_ID='';
$GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY='';

$GKS_HOTEL_BACKEND=false;
$GKS_HOTEL_RESERVATIONS_ONLINE=false;


$GKS_SMS_SENDER='';
$GKS_SMS_TOKEN='';

$GKS_VIBER_URI='';
$GKS_VIBER_TOKEN='';


$GKS_LANG_DEFAULT=''; //en-US';
$GKS_LANG_DEFAULT_DB='en_US';
$GKS_LANG_DATA_ENABLED=[];

$GKS_PLUGINS_ENABLED=[];
$gks_plugins_data=array();

$GKS_ERP_CRON_LAST_RUN=0;
$GKS_ERP_APP_DEF_TIMEZONE='Europe/Athens';
$GKS_ERP_APP_PURCHASE_DATA=''; 
$GKS_ERP_APP_PURCHASE_CODE=[]; 


$my_is_global_admin=is_global_admin();

$time_vardia=_time_user(time(), 1);
$time_vardia-=GKS_ERP_START_VARDIA*60*60;
$today_vardia=date('Y-m-d',$time_vardia);
$today_vardia=strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
$today_vardia=_time_user($today_vardia, -1);
$today_vardia=date('Y-m-d H:i:s', $today_vardia);
$today=user_server_curdate();



//echo '<pre>';
//var_dump($_COOKIE);

//var_dump($my_wp_user_info);

//echo '<pre>';echo time();die();


//if(!session_id()) session_start();
//gks_session_set

$_gks_session=false;
$_gks_id_session=''; if (isset($_COOKIE['gks_erp_cookie_id'])) $_gks_id_session==trim_gks($_COOKIE['gks_erp_cookie_id']);
gks_erp_cookie_start();

$_gks_session['gks']['last_action'] = time();

//print_r($_SESSION);
//die();


function trim_gks($a) {
  if (is_null($a)) return '';
  return trim($a); 
}
function nl2br_gks($a) {
  if (is_null($a)) return '';
  return nl2br($a); 
}

function htmlspecialchars_gks($a) {
  if (is_null($a)) return '';
  return htmlspecialchars($a); 
}

