<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

if (GKS_DEBUG==false or $_SERVER['SCRIPT_NAME']!='/my/admin-basket-debug.php') {
  if ($my_wp_user_id<=0) { header('Location: /'); die(); }
}


$gks_html_lang='en';
if (isset($gks_user_settings['lang']['backend'])) {
  $gks_html_lang=$gks_user_settings['lang']['backend'];
}
?><!doctype html>
<html lang="<?php echo $gks_html_lang; ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="/my/_current/_img_site/icon.png?v=<?php echo $gks_cache_version.'.'.$gks_menu_version;?>" type="image/x-icon">

<title><?php echo $my_page_title;?> | <?php echo $GKS_SITE_HUMAN_NAME; ?></title>
<?php
if (!empty($gks_user_settings['htmlcss']['font_family_link'])) {
  echo str_replace('[[[gks_cache_version]]]',$gks_cache_version,$gks_user_settings['htmlcss']['font_family_link'])."\r\n"; 
} else {
  echo str_replace('[[[gks_cache_version]]]',$gks_cache_version,'<link href="css/font-open-sans.css?v=[[[gks_cache_version]]]" rel="stylesheet">')."\r\n"; ;
}
//$gks_erp_app_font_family='-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"';
$gks_erp_app_font_family='"Open Sans"';
if (!empty($gks_user_settings['htmlcss']['font_family'])) {
  $gks_erp_app_font_family=$gks_user_settings['htmlcss']['font_family'];
}
$gks_erp_app_font_size='1rem'; /* 1rem = 14px */ 
if (!empty($gks_user_settings['htmlcss']['font_size'])) {
  $gks_erp_app_font_size=$gks_user_settings['htmlcss']['font_size'];
}
?>
<style>
:root {
  --gks_erp_app_font_family:<?php echo $gks_erp_app_font_family;?>;
  --gks_erp_app_font_size:<?php echo $gks_erp_app_font_size;?>;
}
</style>
<link href="css/jquery-ui.min.css" rel="stylesheet">
<link href="css/jquery-ui.structure.min.css" rel="stylesheet">
<link href="css/jquery-ui.theme.min.css" rel="stylesheet">
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/my.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link href="css/hotel.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link href="css/transfer.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link href="css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
<link href="css/jquery.periodpicker.min.css" rel="stylesheet" type="text/css"/>
<link href="css/jquery.timepicker.min.css" rel="stylesheet" type="text/css"/>
<link href="css/fontawesome-all.min.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link rel="stylesheet" href="css/jquery.multiselect.css?v2" type="text/css">
<link rel="stylesheet" href="css/jquery.multiselect.filter.css" type="text/css">
<link rel="stylesheet" href="css/switchery.min.css" type="text/css">
<link rel="stylesheet" href="css/spectrum.min.css" type="text/css">


<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script type="text/javascript">
// Change JQueryUI plugin names to fix name collision with Bootstrap.
$.widget.bridge('uitooltip', $.ui.tooltip);
$.widget.bridge('uibutton', $.ui.button);
</script>

<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
var bootstrapButton = $.fn.button.noConflict(); // return $.fn.button to previously assigned value
$.fn.bootstrapBtn = bootstrapButton;            // give $().bootstrapBtn the Bootstrap functionality
var bootstrapTooltip = $.fn.tooltip.noConflict(); // return $.fn.button to previously assigned value
$.fn.bootstrapTltip = bootstrapTooltip;            // give $().bootstrapBtn the Bootstrap functionality
</script>
<script>
const gks_datetimepicker_defaults={
  scrollMonth:false,
  scrollTime:false,
  scrollInput:false,
};
</script>
<script src="js/jquery.base64.js"></script>
<script src='js/jquery.datetimepicker.full.min.js' type='text/javascript'></script>
<script src='js/jquery.periodpicker.full.min.js' type='text/javascript'></script>
<script src='js/jquery.timepicker.min.js' type='text/javascript'></script>

<script type="text/javascript" src="js/jquery.multiselect.js?v2"></script>
<script type="text/javascript" src="js/jquery.multiselect.filter.js"></script>
<?php 
$temp=gks_jquery_multiselect_local();
if ($temp!='') {?>
<script type="text/javascript" src="js/i18n/jquery.multiselect.<?php echo $temp;?>.js"></script>
<script type="text/javascript" src="js/i18n/jquery.multiselect.filter.<?php echo $temp;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="js/switchery.min.js"></script>
<script type="text/javascript" src="js/spectrum.min.js"></script>

<script type="text/javascript" src="js/jquery.hashchange.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.touch-punch.min.js"></script>

<link href="/my/js/light/gallery/css/lightgallery.min.css" rel="stylesheet">
<script src="/my/js/light/gallery/lib/picturefill.min.js"></script>
<script src="/my/js/light/gallery/js/lightgallery-all.min.js"></script>
<script src="/my/js/light/gallery/lib/jquery.mousewheel.min.js"></script>


<link rel="stylesheet" type="text/css" href="/my/js/aehlke-tag-it/tagit.ui-gks.css?v=<?php echo $gks_cache_version;?>"/>
<link rel="stylesheet" type="text/css" href="/my/js/aehlke-tag-it/jquery.tagit.css?v=<?php echo $gks_cache_version;?>"/>
<script type='text/javascript' src='/my/js/aehlke-tag-it/tag-it-gks.js?v=<?php echo $gks_cache_version;?>'></script>

<!--<link rel="stylesheet" type="text/css" href="/my/css/tooltipster-noir.css"/>
<link rel="stylesheet" type="text/css" href="/my/css/tooltipster.css"/>-->
<link rel="stylesheet" type="text/css" href="/my/js/tooltipster-4.2.8/dist/css/tooltipster.bundle.min.css"/>
<link rel="stylesheet" type="text/css" href="/my/js/tooltipster-4.2.8/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-noir.min.css"/>
<!-- <script type='text/javascript' src="/my/js/tooltipster-3.0/js/jquery.tooltipster.js"></script>-->
<script type='text/javascript' src="/my/js/tooltipster-4.2.8/dist/js/tooltipster.bundle.min.js"></script>

<link rel="stylesheet" type="text/css" href="/my/js/contextmenu-1.0.5/contextmenu.min.css"/>
<script type='text/javascript' src="/my/js/contextmenu-1.0.5/contextmenu.min.js"></script>

<link rel="stylesheet" type="text/css" href="/my/js/select2-4.0.13/css/select2.min.css"/>
<script type='text/javascript' src="/my/js/select2-4.0.13/js/select2.full.min.js"></script>
<script type='text/javascript' src="/my/js/select2-4.0.13/js/i18n/el.js"></script>

<link rel="stylesheet" href="/my/js/RateYo/jquery.rateyo.min.css"/>  
<script type="text/javascript" src="/my/js/RateYo/jquery.rateyo.js"></script>

<?php
if ($gks_html_lang!='' and $gks_html_lang!='en' and $gks_html_lang!='el-GR') {
  $files_list=['generic','part2','part3','part4'];
  foreach ($files_list as $file) {
    $gks_lang_data_file=$gks_user_cache_version_prefix.'gks_lang_data_'.$gks_html_lang.'_'.$file.'.js';
    if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/'.$gks_lang_data_file)) {   
      echo '<script src="/my/cache/'.$gks_lang_data_file.'?v='.$gks_cache_version.'" type="text/javascript"></script>'."\r\n";
    }
  } 
}
?>
<script src='/my/js/_gks_tabs_registry.js?v=<?php echo $gks_cache_version;?>' type='text/javascript'></script>
<script src='/my/js/my.js?v=<?php echo $gks_cache_version;?>' type='text/javascript'></script>
<script src="/my/js/_favorites.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="/my/js/_notification.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
if (isset($header_add_extra)) echo $header_add_extra;

?>

<script>

var from_php_gks_main_menu_active=[];
<?php 
if (isset($nav_active_array) and is_array($nav_active_array)) {
foreach ($nav_active_array as $value) {
   echo 'from_php_gks_main_menu_active.push(\'gks_main_menu_'.$value.'\');'."\n";
} }
?>  
var from_php_gks_menu_pos='<?php echo $gks_user_settings['menu']['pos'];?>';
var from_php_gks_user_settings_menu_sticky_top='<?php echo $gks_user_settings['menu']['sticky-top'];?>';
var from_php_header_admin_menu_statbots ='<a class="dropdown-item" href="/my/admin-stat-bots.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI'])?>"><?php
							  if (isset($_gks_session['gks']['stat']['admin_stats_bots_enable']) and $_gks_session['gks']['stat']['admin_stats_bots_enable']==false) {
							    echo gks_lang('Ενεργοποίηση bots');
							  } else {
							    echo gks_lang('Απενεργοποίηση bots');
							  }
							  echo '</a>';?>';

var from_php_gks_cache_version = <?php echo $gks_cache_version;?>;						  							  
</script>

<script src='cache/<?php echo $gks_user_cache_version_prefix;?>gks_common.js' type='text/javascript'></script>
<?php gks_erp_app_purchase_ads_head();?>
  
</head>
<?php
//echo '<pre>';print_r($_SERVER);echo '</pre>';//die();
$gks_body_class='gks_page_class_';
if (isset($_SERVER['SCRIPT_NAME'])) {
  $gks_body_class=trim_gks($_SERVER['SCRIPT_NAME']);
  $gks_body_class=str_replace('.php','',$gks_body_class);
  $gks_body_class=str_replace('/my/','',$gks_body_class);
  $gks_body_class='gks_body_class_'.$gks_body_class;
}

if (isset($_GET['from']) and $_GET['from']=='gks_erp_app_mobile') $gks_body_class.=' gks_erp_app_mobile';

?>
<body class="<?php echo $gks_body_class;?>_empty">
<?php gks_erp_app_purchase_ads_header_ad();?>

