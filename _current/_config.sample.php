<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

//define('ENV','DEVELOPMENT');
define('ENV','PRODUCTION');

date_default_timezone_set("GMT");














define('GKS_ERP_HASHMD5KEY01','[[HASHKEY01]]');
define('GKS_ERP_HASHMD5KEY02','[[HASHKEY02]]');
define('GKS_ERP_HASHMD5KEY04','[[HASHKEY04]]');
define('GKS_ERP_HASHMD5KEY05','[[HASHKEY05]]');
define('GKS_ERP_HASHMD5KEY09','[[HASHKEY09]]');
define('GKS_ERP_HASHMD5KEY10','[[HASHKEY10]]');
define('GKS_ERP_HASHMD5KEY13','[[HASHKEY13]]');
define('GKS_ERP_HASHMD5KEY15','[[HASHKEY15]]');

define('GKS_DEBUG',false);
define('GKS_ERP_START_VARDIA',0);
define('GKS_HOTEL_BACKEND',[[hotel_backend]]);
define('GKS_HOTEL_RESERVATIONS_ONLINE',[[hotel_frontend]]);

define('GKS_LICENCE_EFS',false);
define('GKS_TRANSFER',false);
define('GKS_TRANSFER_AUTO_MYDATA_EMAIL',false);
define('GKS_HOTEL_PREFIX','ERP1-');
define('GKS_ORDERS_PREFIX','ERP2-');
define('GKS_INV_ACC_PREFIX','ERP3-');
define('GKS_TRANSFER_PREFIX','ERP4-');


define('GKS_IMAGE_EXTENSION',['.jpg','.jpeg','.jpe','.jif','.jfif','.jfi','.png','.gif','.bmp','.webp']);

define('GKS_ZODOMUS_MODE_LIVE',false);

define('GKS_SITE_URL',        '[[siteurl]]');
define('GKS_SITE_PATH',       '[[sitepath]]');
define('GKS_SITE_HTTPDOCS',   '[[httpdocs]]');
define('GKS_FileServerShare', '[[FileServer]]');
define('GKS_DATA',            '[[data]]');
define('GKS_CACHE',           '[[cache]]');
define('GKS_MAXIMIND_COM_PATH','');
//define('GKS_PDF_GENERATOR','');
define('GKS_PDF_GENERATOR','https://tools.gks.gr/remote_pdf_generator/create.php');
define('GKS_EMAIL_DEBUG_FROM','');

define('GKS_EMAIL_DEBUG_TO_1','');
define('GKS_EMAIL_DEBUG_TO_2','');
define('GKS_EMAIL_DEBUG_TO_3','');
define('GKS_EMAIL_DEBUG_HOST','');
define('GKS_EMAIL_DEBUG_PORT',587);
define('GKS_EMAIL_DEBUG_SMTPAUTH',true);
define('GKS_EMAIL_DEBUG_USERNAME','');
define('GKS_EMAIL_DEBUG_PASSWORD','');

if (file_exists(GKS_CACHE)==false) @mkdir(GKS_CACHE , 0777, true);

define('GKS_AADE_MYDATA_URL_TEST','https://mydataapidev.aade.gr/');
define('GKS_AADE_MYDATA_URL_LIVE','https://mydatapi.aade.gr/myDATA/');


define('GKS_ILYDA_COM_MODE_TEST_API',    'https://test.vs.gr');
define('GKS_ILYDA_COM_MODE_LIVE_API',    'https://vs.gr');

define('GKS_TESAE_GR_MODE_TEST_API',    'https://e-invoicing-api-dev.pegcloud.io');
define('GKS_TESAE_GR_MODE_LIVE_API',    'https://e-invoicing-user-api.pegcloud.io');

define('GKS_PAROCHOS_GR_MODE_TEST_ACCOUNT','https://beta-account.parochos.gr');
define('GKS_PAROCHOS_GR_MODE_TEST_API',    'https://beta-srv.parochos.gr');

define('GKS_PAROCHOS_GR_MODE_LIVE_ACCOUNT','https://account.parochos.gr');
define('GKS_PAROCHOS_GR_MODE_LIVE_API',    'https://srv.parochos.gr');

define('GKS_VIVA_URL_WWW','https://www.vivapayments.com');


define('GKS_CARDLINK_uniqueIntegratorId','');

define('GKS_MELLONGROUP_COM_API','https://mreceipts.com');


define('GKS_EPAY_COM_API','https://webecr.epayworldwide.com:11007');


define('GKS_WORLDLINE_COM_API','https://api-pub.tapxphone.com');


define('GKS_WORLDLINE_COM_API_TOKEN','https://token-pub.tapxphone.com');


define('GKS_WORLDLINE_COM_API_BANK_ID' ,'');


define('GKS_WORLDLINE_YLIDA_Key_ID' ,'');


define('GKS_WORLDLINE_COM_API_PARTNER_ID' ,'');
define('GKS_WORLDLINE_COM_API_PARTNER_KEY','');


define('GKS_Meg_EFT_POS_Driver_licenseKey','');
define('GKS_Meg_EFT_POS_Driver_vatNumber', '');





define('GKS_ESHOP_BRANDS_TAXONOMY', array(
  //array('taxonomy' => 'pa_brand',  'name' => 'As attribute with name Brand'),
  //array('taxonomy' => 'pa_brand1', 'name' => 'As attribute with name Brand 1'),
));

define('GKS_PROXY', array(
  'SERVER'=>'0.0.0.0',
  'PORT' => 7000,
  'VPORT' => 7800,
  'VSPORT' => 8444,
  'TOKEN' => '12345678901234567890',
  'HTTP_PREFIX' => 'web_gks_erp_app_mobile_',
  'DOMAIN_BASE_NAME' => '.gkserpapp.example.com',
  'LWS' => array(
    'addr' => '0.0.0.0',
    'port' => 7401,
    'user' => 'admin',
    'pass' => '1234567890',
  ),
));










define('GKS_ROLES_HIERARCHY',array(
  'administrator' => 1,
  'adminmy' => 1,
  
  'author' => 10,
  'contributor' => 10,
  'editor' => 10,
  
  'wpseo_manager' => 100,
  'wpseo_editor' => 150,
  
  'logistis' => 200,
  'hrmanager' => 200,
  
  
  'timologisi' => 300,
  'texnikos' => 300,
  'omadarxis' => 300,
  'ordermanager' => 300,
  'tamias' => 300,
  'ipethinosperioxis' => 300,
  'xiristismixanimaton' => 300,
  'findphotos' => 300,
  'babys' => 300,

  'photographer' => 400,
  
  'employee' => 500,
  'salesman' => 500,
  'apothikarios' => 500,
  'driver' => 500,
  
  
  'customer' => 800,
  'promitheutis' => 800,
  'kalitexnis' => 800,
  
  'subscriber' => 1000,
    
));
