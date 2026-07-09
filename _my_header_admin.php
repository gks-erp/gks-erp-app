<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

if (GKS_DEBUG==false or $_SERVER['SCRIPT_NAME']!='/my/admin-basket-debug.php') {
  if ($my_wp_user_id<=0) { header('Location: /'); die(); }
}



//echo gks_lang('Απενεργοποίηση bots'); die();
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

<script src="/my/js/_gks_custom.js?v=<?php echo $gks_cache_version;?>" type='text/javascript'></script>


<style>
<?php
if (isset($leads_status_styles)==false) gks_get_leads_status($leads_status,$leads_status_styles); 
echo $leads_status_styles;

if (isset($tasks_status_styles)==false) gks_get_tasks_status($tasks_status,$tasks_status_styles); 
echo $tasks_status_styles;

?> 
</style>
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
<style>
<?php if ($gks_user_settings['menu']['hover']=='1') { ?>

/*
.dropdown:hover .dropdown-menu{
    display: block;
}
.dropdown-menu{
    margin-top: 0;
}
*/
@media all and (min-width: 992px) {
	.navbar1 .nav-item .dropdown-menu {display: none;}
	.navbar1 .nav-item:hover .nav-link {   }
	
	.navbar .nav-item .dropdown-menu {margin-top:0;}
	.navbar > .navbar-collapse > .navbar-nav > .nav-item:hover > ul.dropdown-menu{ display:block;}
	.navbar1 > .navbar-collapse > .navbar-nav > .nav-item > .dropdown-menu .dropdown-submenu:hover > ul.dropdown-menu {display: block;}
}
<?php } ?>  
  
body1 {
  zoom: 0.7; 
  -moz-transform: scale(0.7); 
  -moz-transform-origin: 0 0;
}
</style>


<?php
$gks_custom_css_global_file=$gks_user_cache_version_prefix.'custom_css_global.css';
if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/'.$gks_custom_css_global_file)==false) {
  $custom_css_global_val='';
  $sql="select myvalue from gks_settings where mykey='custom_css_global'";
  $result_select = $db_link->query($sql);
  if (!$result_select) {debug_mail(false,'error sql',$sql);die('error sql');} 
  if ($result_select->num_rows==1) {
    $row_select = $result_select->fetch_assoc();
    $custom_css_global_val=trim_gks($row_select['myvalue']);
  }
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/'.$gks_custom_css_global_file,$custom_css_global_val);
}


$gks_custom_css_user_file=$gks_user_cache_version_prefix.'custom_css_user.css';
if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/'.$gks_custom_css_user_file)==false) {
  $custom_css_user_val='';
  $sql="select myvalue from gks_settings_users where user_id=".$my_wp_user_id." and myobject='css' and mysubobject='user'";
  $result_select = $db_link->query($sql);
  if (!$result_select) {debug_mail(false,'error sql',$sql);die('error sql');} 
  if ($result_select->num_rows==1) {
    $row_select = $result_select->fetch_assoc();
    $custom_css_user_val=trim_gks($row_select['myvalue']);
  }
  file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/'.$gks_custom_css_user_file,$custom_css_user_val);
}

if (isset($gks_custom_css_global_file) and $gks_custom_css_global_file!='') {
  echo '<link rel="stylesheet" href="/my/cache/'.$gks_custom_css_global_file.'">';
}
if (isset($gks_custom_css_user_file) and $gks_custom_css_user_file!='') {
  echo '<link rel="stylesheet" href="/my/cache/'.$gks_custom_css_user_file.'">';
} 
  
   
?>
  
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
<body class="<?php echo $gks_body_class;?>">
<!--
<img src="/my/_current/_img_site/icon.png?v=<?php echo $gks_cache_version;?>" style="display:none;"/>
-->
<?php if (isset($gks_header_footer_layout)==false or $gks_header_footer_layout=='full') { ?>
<div id="dialog_favorites_add" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <form>
    <div class="form-group">
      <label for="dialog_favorites_add_title"><?php echo gks_lang('Όνομα σελίδας');?>:</label>
      <input type="text" class="form-control form-control-sm" id="dialog_favorites_add_title" name="dialog_favorites_add_title" placeholder="<?php echo gks_lang('Πληκτρολογήστε ένα όνομα');?>">
    </div>
    <div class="form-group">
      <label for="dialog_favorites_add_url"><?php echo gks_lang('Σύνδεσμος');?>:</label>
      <input type="text" class="form-control form-control-sm" id="dialog_favorites_add_url" name="dialog_favorites_add_url" placeholder="<?php echo gks_lang('Πληκτρολογήστε το URL');?>">
    </div>
  </form>
</div>
<?php } 



$perm_ret=gks_permission_get_user($my_wp_user_id);
//print '<pre>';print_r($gks_permission_get_user);die();

if (isset($gks_header_footer_layout)==false or $gks_header_footer_layout=='full') { ?>
<div style="clear: both;"></div>  
<gks_main_container class="<?php if ($gks_user_settings['menu']['pos']=='left') echo 'gks_menu_pos_left'; else echo 'gks_menu_pos_top';?>">
<gks_nav_parent class="<?php 
  if ($gks_user_settings['menu']['pos']=='left') {
    echo 'gks_menu_pos_left'; 
    if ($gks_user_settings['menu']['narrow']=='1') echo' gks_menu_narrow'; else echo' gks_menu_nonarrow';
  } else {
    echo 'gks_menu_pos_top';
  }?>">
<nav id="gks_nav_session_header" class="navbar navbar-dark bg-primary navbar-expand-lg <?php
  if ($gks_user_settings['menu']['sticky-top']=='1') echo 'sticky-top';
  ?> fixed-top1" style="background-color11: #e3f2fd;margin-bottom:0px">
  <a class="navbar-brand" href="/my">
    <img src="/my/_current/_img_site/logo2.png?v=<?php echo $gks_cache_version.'.'.$gks_menu_version;?>" alt="logo" class="gks_logo_300">
    <img src="/my/_current/_img_site/logo50.png?v=<?php echo $gks_cache_version.'.'.$gks_menu_version;?>" alt="logo" class="gks_logo_64">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="form-inline my-2 my-lg-0" id="gks_narrow_icon_div">
    <i class="fas fa-arrows-alt-h" id="gks_narrow_icon" ></i>
  </div>
  
  <div class="form-inline my-2 my-lg-0" id="gks_header_search1_div">
    <input id="gks_header_search1" class="gks_header_search_elem form-control mr-sm-2" type="search" placeholder="<?php echo gks_lang('Αναζήτηση');?>" aria-label="<?php echo gks_lang('Αναζήτηση');?>" autocomplete="one-time-code">
  </div>



  <div class="collapse navbar-collapse" id="navbarCollapse">
    
  <?php 


  
    
  $gks_menu_html_file=$gks_user_cache_version_prefix.'html_menu_user.html';
  
  //echo $gks_menu_html_file;
  
  if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/'.$gks_menu_html_file)==false) {
    
    $this_root_menu_html='<ul class="navbar-nav mr-auto">';


    
    
    if ($my_wp_user_id>=1 and isset($db_link)) {
      $this_root_menu_favorites=
			'<li class="nav-item dropdown ks_main_menu_favorites">'.
        '<a id="nav_favorites" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
          '<i class="fas fa-heart gks_menu_icon_text gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Αγαπημένα').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
          '<span class="gks_menu_text_icon gks_menu_narrow_text">'.gks_lang('Αγαπημένα').'</span>'.
        '</a>'.
				'<ul class="dropdown-menu" aria-labelledby="nav_favorites">';

        $sql_favorites="select * from gks_users_favorites where descr<>'' and url<>'' and user_id=".$my_wp_user_id." order by fav_sortorder,descr";
        $result_favorites = $db_link->query($sql_favorites);      
        if (!$result_favorites) {
          debug_mail(false,'error sql',$sql_favorites);
          die();
        }
        while ($row_favorites = $result_favorites->fetch_assoc()) {
          $url_target='';
          $tturl=strtolower($row_favorites['url']);
          if ((substr($tturl, 0, strlen(GKS_SITE_URL)) != GKS_SITE_URL and substr($tturl, 0, 1) != '/') or ( substr($tturl, 0, 5) == 'http:' or substr($tturl, 0, 5) == 'https:' )) {
            $url_target=' target="_blank"';
          }
          $this_root_menu_favorites.='<li><a class="dropdown-item" href="'.$row_favorites['url'].'" '.$url_target.'>'.$row_favorites['descr'].'</a></li>';
        }
			  $this_root_menu_favorites.= 
					'<li class="gks_menu_li_icon_add111"><a class="dropdown-item" href="#"><i id="favorites_add" class="fas fa-plus-circle" style = "color:#28a745;font-size: 120%;position:relative;top:0px;cursor:pointer;" title="'.gks_lang('Προσθήκη της τρέχουσας σελίδας στα αγαπημένα').'"></i></a></li>'.
					'<li><a class="dropdown-item gks_main_menu_favorites_add" href="/my/admin-favorites.php">'.gks_lang('Διαχείριση Αγαπημένων').'</a></li>'.
				'</ul>'.
			'</li>';
	
	    $this_root_menu_html.=$this_root_menu_favorites;
    }
    
    

    
    $this_root_menu_home='<li class="nav-item dropdown gks_main_menu_index">'.
      '<a class="nav-link" href="/my">'.
        '<i class="fas fa-home gks_menu_icon_text gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Αρχική').'" style="font-size:120%;position:relative;top:0px;"></i>'.
        '<span class="gks_menu_text_icon gks_menu_narrow_text">'.gks_lang('Αρχική').'</span>'.
      '</a>'.
    '</li>';
    $this_root_menu_html.=$this_root_menu_home;
    

    $this_menu='';
    $this_menu.='<li class="">'.
        '<a class="dropdown-item"  href="/">'.gks_lang('Αρχική Ιστότοπου').'</a>'.
      '</li>';
    if (ur_ad()) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item"  href="/wp-admin/">'.gks_lang('Πίνακας Ελέγχου WordPress').'</a>'.
      '</li>';
    }
    if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
    
    $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__filesexplore','view',0);
    if ($perm_ret['success']) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_filesexplore"  href="/my/admin-files-explore.php">'.gks_lang('Εξερεύνηση αρχείων').'</a>'.
      '</li>';
    } 
           
    $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__downloads','view',0);
    if ($perm_ret['success']) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_downloads"  href="/my/admin-downloads.php">'.gks_lang('Λήψεις - Σύνδεσμοι').'</a>'.
      '</li>';
    }
    $this_root_menu_pages='';
    if ($this_menu!='') {
      if (endwith($this_menu,'<li class="dropdown-divider"></li>')) $this_menu=substr($this_menu, 0,strlen($this_menu)-34);
      $this_root_menu_pages='<li class="nav-item dropdown gks_main_menu_pages">
        <a id="nav_pages" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
          '<i class="fas fa-file-image gks_menu_icon_text gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Σελίδες').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
          '<span class="gks_menu_text_icon gks_menu_narrow_text">'.gks_lang('Σελίδες').'</span>'.
        '</a>'.
        '<ul class="dropdown-menu" aria-labelledby="nav_pages">'.
          $this_menu.'
        </ul>
      </li>';
    }
    //echo $this_root_menu_pages; 
    $this_root_menu_html.=$this_root_menu_pages;
            
    
    $this_menu='';
    $sql_dikamouobj="SELECT gks_custom_table.id_custom_table, gks_custom_table.custom_table_descr, gks_custom_table.obj_url, gks_custom_table.custom_sortorder
    FROM (gks_custom_table 
    LEFT JOIN gks_permission_object ON gks_custom_table.custom_table_name = gks_permission_object.table_name) 
    LEFT JOIN gks_permission_user ON gks_permission_object.id_permission_object = gks_permission_user.permission_object_id
    WHERE gks_custom_table.custom_table_disabled=0
    AND gks_permission_user.user_id=".$my_wp_user_id."
    AND gks_permission_user.perm_view=1 
    AND gks_custom_table.id_custom_table>=10000
    GROUP BY gks_custom_table.custom_table_descr, gks_custom_table.id_custom_table, gks_custom_table.obj_url, gks_custom_table.custom_sortorder
    ORDER BY gks_custom_table.custom_sortorder;";
    $result_dikamouobj = $db_link->query($sql_dikamouobj);      
    if (!$result_dikamouobj) {
      debug_mail(false,'error sql',$sql_dikamouobj);
      die();
    }
    while ($row_dikamouobj = $result_dikamouobj->fetch_assoc()) {
      $this_menu.='<li><a class="dropdown-item gks_main_menu_dikamouobj_table_'.$row_dikamouobj['id_custom_table'].'" href="'.$row_dikamouobj['obj_url'].'">'.$row_dikamouobj['custom_table_descr'].'</a></li>';
    }    
    

    
    $this_root_menu_dikamouobj='';
    if ($this_menu!='') {
      //if (endwith($this_menu,'<li class="dropdown-divider"></li>')) $this_menu=substr($this_menu, 0,strlen($this_menu)-34);
      $this_root_menu_dikamouobj='<li class="nav-item dropdown gks_main_menu_dikamouobj">
        <a id="nav_dikamouobj" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
          '<i class="fas fa-dice-d6 gks_menu_icon_text gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Δικά μου αντικείμενα').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
          '<span class="gks_menu_text_icon gks_menu_narrow_text">'.gks_lang('Δικά μου αντικείμενα').'</span>'.
        '</a>'.
        '<ul class="dropdown-menu" aria-labelledby="nav_dikamouobj">'.
          $this_menu.'
        </ul>
      </li>';
    }
    
   

    $this_root_menu_html.=$this_root_menu_dikamouobj;    
    
    
    $this_menu='';
    if (gks_permission_user_can_action_php($my_wp_user_id,'wp_users','add',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_new_user" href="/my/admin-users-item.php?id=-1">'.gks_lang('Νέα Επαφή').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'wp_users','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_users" href="/my/admin-users.php">'.gks_lang('Επαφές').'</a>'.
      '</li>';
    }
    
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_users_groups','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_users_groups" href="/my/admin-usersgroups.php">'.gks_lang('Ομάδες Επαφών').'</a>'.
      '</li>';
    }
    if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
    
    $this_menu_sub='';
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_products','add',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_new_product" href="/my/admin-products-item.php?id=-1">'.gks_lang('Νέο Είδος').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_products','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_product" href="/my/admin-products.php">'.gks_lang('Είδη').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_products','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_product_prices" href="/my/admin-products-prices.php">'.gks_lang('Τιμές Ειδών').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_products_categories','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_product_category" href="/my/admin-product-categories.php">'.gks_lang('Κατηγορίες').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_products_brands','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_product_brands" href="/my/admin-product-brands.php">'.gks_lang('Μάρκες').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_product_idiotites','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_product_idiotites" href="/my/admin-product-idiotites.php">'.gks_lang('Ιδιότητες').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_product_idiotites_terms','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_product_idiotites_terms" href="/my/admin-product-idiotites-term.php">'.gks_lang('Όροι Ιδιοτήτων').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_barcodes','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_product_barcodes" href="/my/admin-barcodes.php">'.gks_lang('Barcodes Ειδών').'</a>'.
      '</li>';
    }    
    
    if ($this_menu_sub!='') {
      $this_menu.='<li class="dropdown-submenu">'.
          '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle gks_main_menu_manage_menu_product" aria-haspopup="true" aria-expanded="false">'.gks_lang('Είδη').'</a>'.
          '<ul class="dropdown-menu">'.
            $this_menu_sub.
          '</ul>'.
        '</li>';
    }
    
    
    if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
    
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_pricelist" href="/my/admin-pricelists.php">'.gks_lang('Τιμοκατάλογοι').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_pricelist_items','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_pricelist_items" href="/my/admin-pricelists-items.php">'.gks_lang('Στοιχεία Τιμοκαταλόγου-Κουπόνια').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_delivery_methods','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_d" href="/my/admin-delivery-methods.php">'.gks_lang('Τρόποι Αποστολής').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_payment_acquirers','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_p" href="/my/admin-payment-acquirers.php">'.gks_lang('Τρόποι Πληρωμής').'</a>'.
      '</li>';
    }
    
    

    

    if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
    
    
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_lang','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_lang" href="/my/admin-lang.php">'.gks_lang('Γλώσσες').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_country','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_country" href="/my/admin-country.php">'.gks_lang('Χώρες').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_country','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_nomoi" href="/my/admin-nomoi.php">'.gks_lang('Νομοί').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_monades_metrisis','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_monades" href="/my/admin-monades.php">'.gks_lang('Μονάδες Μέτρησης').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_banks','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_banks" href="/my/admin-banks.php">'.gks_lang('Τράπεζες').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_bank_accounts','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_bank_accounts" href="/my/admin-bank_accounts.php">'.gks_lang('Τραπεζικοί λογαριασμοί').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_sociallinks_type','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_sociallinks_type" href="/my/admin-sociallinks-type.php">'.gks_lang('Τύποι Συνδέσμων Κοινωνικών Δικτύων').'</a>'.
      '</li>';
    } 
        
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_aade','view',0)) {
      
      $this_menu_sub='';
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_aade_fiscal_position" href="/my/admin-eshop-fiscal-position.php">'.gks_lang('Φορολογική Θέση').'</a>'.
      '</li>';  
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_aade_eidi_parastatikon" href="/my/admin-aade-eidi-parastatikon.php">'.gks_lang('Είδη Παραστατικών').'</a>'.
      '</li>';  
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_aade_skopos_diakinisis" href="/my/admin-aade-skopos-diakinisis.php">'.gks_lang('Σκοπός Διακίνησης').'</a>'.
      '</li>';  
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_aade_katigoria_fpa_ejeresi" href="/my/admin-aade-katigoria-fpa-ejeresi.php">'.gks_lang('Αιτία Εξαίρεσης ΦΠΑ').'</a>'.
      '</li>';  
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_aade_katigoria_parakratoumemenon_foron" href="/my/admin-aade-katigoria-parakratoumemenon-foron.php">'.gks_lang('Παρακρατούμενοι Φόροι').'</a>'.
      '</li>';  
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_aade_katigoria_loipon_foron" href="/my/admin-aade-katigoria-loipon-foron.php">'.gks_lang('Λοιποί Φόροι').'</a>'.
      '</li>';      
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_aade_katigoria_xartosimou" href="/my/admin-aade-katigoria-xartosimou.php">'.gks_lang('Ψηφιακό Τέλος συναλλαγής').'</a>'.
      '</li>';  
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_aade_katigoria_telon" href="/my/admin-aade-katigoria-telon.php">'.gks_lang('Τέλη').'</a>'.
      '</li>';      
      
           
      $this_menu.='<li class="dropdown-submenu">'.
        '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle gks_main_menu_manage_aade" aria-haspopup="true" aria-expanded="false">'.gks_lang('ΑΑΔΕ').'</a>'.
        '<ul class="dropdown-menu">'.
        $this_menu_sub.
        '</ul>'.            
      '</li>';

            
     
      
    }    
    

        
    
    if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
    
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_company','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_company" href="/my/admin-company.php">'.gks_lang('Εταιρείες').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_company_subs','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_company_subs" href="/my/admin-company-sub.php">'.gks_lang('Υποκαταστήματα').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_journal','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_accounting_journal" href="/my/admin-acc_journal.php">'.gks_lang('Ημερολόγια').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_seires','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_accounting_seires" href="/my/admin-acc_seires.php">'.gks_lang('Σειρές').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_warehouses','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_warehouse" href="/my/admin-warehouses.php">'.gks_lang('Αποθήκες').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_print_forms','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_print_forms" href="/my/admin-print_forms.php">'.gks_lang('Φόρμες Εκτύπωσης').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_template_html','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_template_html" href="/my/admin-template_html.php">'.gks_lang('Πρότυπα HTML').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_eshops','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_eshop" href="/my/admin-eshop.php">'.gks_lang('eshops').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_erp_app','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_erp_app" href="/my/admin-erp-app.php">'.gks_lang('gks ERP App Desktop').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_erp_app_mobile','view',0)) {
      $this_menu.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_erp_app_mobile" href="/my/admin-erp-app-mobile.php">'.gks_lang('gks ERP App Mobile').'</a>'.
      '</li>';
    }    
    
    
    if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
    


    $this_menu_sub='';
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_settings_users','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_user_settings" href="/my/admin-user-settings.php">'.gks_lang('Οι Ρυθμίσεις μου').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_settings','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_system_settings" href="/my/admin-system-settings.php">'.gks_lang('Ρυθμίσεις Εφαρμογής').'</a>'.
      '</li>';
    }
    if (ur_ad()) { 
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_system_health" href="/my/admin-system-health.php">'.gks_lang('Υγεία ιστότοπου').'</a>'.
      '</li>';
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_crons','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_system_crons" href="/my/admin-crons.php">'.gks_lang('Χρονοπρογραμματισμός εργασιών').'</a>'.
      '</li>';        
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_app_info','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_system_async_queue" href="/my/admin-async-queue.php">'.gks_lang('Ασύγχρονη Ουρά Εντολών').'</a>'.
      '</li>';        
    }

    
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_custom_table','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_custom" href="/my/admin-custom.php">'.gks_lang('Προσαρμογή').'</a>'.
      '</li>';
    }
    
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks_app_info','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_system_info" href="/my/admin-system-info.php">'.gks_lang('Πληροφορίες Εφαρμογής').'</a>'.
      '</li>';        
    }
    if (gks_permission_user_can_action_php($my_wp_user_id,'gks__sql','view',0)) {
      $this_menu_sub.='<li class="">'.
        '<a class="dropdown-item gks_main_menu_manage_sql" href="/my/admin-sql.php">'.gks_lang('MySQL Query Browser').'</a>'.
      '</li>';        
    }
    

    if ($this_menu_sub!='') {
      $this_menu.='<li class="dropdown-submenu dropup">'.
          '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle gks_main_menu_manage_settings" aria-haspopup="true" aria-expanded="false">'.gks_lang('Ρυθμίσεις').'</a>'.
          '<ul class="dropdown-menu">'.
            $this_menu_sub.
          '</ul>'.
        '</li>';
    }
    

    
    $this_root_menu_manage='';
    if ($this_menu!='') {
      if (endwith($this_menu,'<li class="dropdown-divider"></li>')) $this_menu=substr($this_menu, 0,strlen($this_menu)-34);
      $this_root_menu_manage='<li class="nav-item dropdown gks_main_menu_manage">
        <a id="nav_manage" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
          '<i class="fas fa-database gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Διαχείριση').'" style="font-size: 120%;position:relative;top:0px;" ></i>'.
          '<span class="gks_menu_narrow_text">'.gks_lang('Διαχείριση').'</span>'.
        '</a>
        <ul class="dropdown-menu" aria-labelledby="nav_manage">'.
          $this_menu.'
        </ul>
      </li>';
    }
  
    if (gks_permission_user_can_action_php($my_wp_user_id,'wp_users','add',0)) {
      $this_root_menu_manage.=
      '<li class="nav-item gks_main_menu_manage_new_user gks_header_add_new gks_menu_li_icon_add">'.
        '<a class="nav-link" href="/my/admin-users-item.php?id=-1"><i class="fas fa-plus-circle"></i></a>'.
      '</li>';
    }
          
    //echo $this_root_menu_manage; 
    $this_root_menu_html.=$this_root_menu_manage;
    
   
    if ($GKS_HOTEL_BACKEND) {
      
      $this_menu='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks__hotel_plan','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_plan" href="/my/admin-hotel-plan.php">'.gks_lang('Πλάνο').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_reservation','add',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_new_reservation" href="/my/admin-hotel-reservation-item.php?id=-1">'.gks_lang('Νέα Κράτηση').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_reservation','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_reservation" href="/my/admin-hotel-reservation.php">'.gks_lang('Κρατήσεις').'</a>'.
        '</li>';
      }
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';

      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_availability','view',0) or gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_price','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_availability_prices" href="/my/admin-hotel-availability-prices-calendar.php">'.gks_lang('Διαθεσιμότητα και Τιμές').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_availability','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_availability" href="/my/admin-hotel-availability.php">'.gks_lang('Διαθεσιμότητα').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_price','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_price" href="/my/admin-hotel-price.php">'.gks_lang('Τιμές').'</a>'.
        '</li>';
      }
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';

      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_room_type','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_room_types" href="/my/admin-hotel-room-type.php">'.gks_lang('Τύποι Δωματίων').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_room','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_rooms" href="/my/admin-hotel-room.php">'.gks_lang('Δωμάτια').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_floor','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_floors" href="/my/admin-hotel-floor.php">'.gks_lang('Όροφοι').'</a>'.
        '</li>';
      }
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_hotel_manage" href="/my/admin-hotel.php">'.gks_lang('Ξενοδοχεία').'</a>'.
        '</li>';
      }

      $this_root_menu_hotel='';
      if ($this_menu!='') {
        if (endwith($this_menu,'<li class="dropdown-divider"></li>')) $this_menu=substr($this_menu, 0,strlen($this_menu)-34);
        $this_root_menu_hotel='<li class="nav-item dropdown gks_main_menu_hotel">
          <a id="nav_sales" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
            '<i class="fas fa-hotel gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Ξενοδοχείο').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
            '<span class="gks_menu_narrow_text">'.gks_lang('Ξενοδοχείο').'</span>'.
          '</a>
          <ul class="dropdown-menu" aria-labelledby="nav_sales">'.
            $this_menu.'
          </ul>
        </li>';
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_reservation','add',0)) {
        $this_root_menu_hotel.=
        '<li class="nav-item gks_main_menu_hotel_new_reservation gks_header_add_new gks_menu_li_icon_add">'.
          '<a class="nav-link" href="/my/admin-hotel-reservation-item.php?id=-1"><i class="fas fa-plus-circle"></i></a>'.
        '</li>';
      }
      $this_root_menu_html.=$this_root_menu_hotel;              
    }
    
    if (GKS_TRANSFER) {
      $this_menu='';
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer_reservation','add',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_new_reservation" href="/my/admin-transfer-reservation-item.php?id=-1">'.gks_lang('Νέα Κράτηση').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer_reservation','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_reservation" href="/my/admin-transfer-reservation.php">'.gks_lang('Κρατήσεις').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer_manage','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_manager" href="/my/admin-transfer-manage.php">'.gks_lang('Διαχείριση').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer_driver','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_driver" href="/my/admin-transfer-driver.php">'.gks_lang('Σελίδα Οδηγού').'</a>'.
        '</li>';
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer_pricelist','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_pricelist" href="/my/admin-transfer-pricelist.php">'.gks_lang('Τιμοκατάλογος').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_poi_diadromes','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_poi_diadromes" href="/my/admin-poi-diadromes.php">'.gks_lang('Διαδρομές').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_poi','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_poi" href="/my/admin-poi.php">'.gks_lang('Σημεία Ενδιαφέροντος').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_map','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_map" href="/my/admin-map.php">'.gks_lang('Χάρτης Σημείων').'</a>'.
        '</li>';
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer_area','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_area" href="/my/admin-transfer-area.php">'.gks_lang('Περιοχές').'</a>'.
        '</li>';
      }

      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';

      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_airline','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_airline" href="/my/admin-airline.php">'.gks_lang('Aεροπορικές εταιρείες').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_flights_routes','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_flights_routes" href="/my/admin-flights-routes.php">'.gks_lang('Πλάνο Πτήσεων').'</a>'.
        '</li>';
      }
      
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';

      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_poi_type','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_poi_type" href="/my/admin-poi-type.php">'.gks_lang('Τύποι Σημείων Ενδιαφέροντος').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer_oxima_type','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_oxima_type" href="/my/admin-transfer-oxima-type.php">'.gks_lang('Τύποι Οχημάτων').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer_oxima_type','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_oxima_type_perkm" href="/my/admin-transfer-oxima-type_perkm.php">'.gks_lang('Τύποι Οχημάτων - Τιμές ανά Km').'</a>'.
        '</li>';
      }

      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_transfer_manage" href="/my/admin-transfer.php">'.gks_lang('Κανάλια Transfer').'</a>'.
        '</li>';
      }
      
      

      $this_root_menu_transfer='';
      if ($this_menu!='') {
        if (endwith($this_menu,'<li class="dropdown-divider"></li>')) $this_menu=substr($this_menu, 0,strlen($this_menu)-34);
        $this_root_menu_transfer='<li class="nav-item dropdown gks_main_menu_transfer">
          <a id="nav_transfer" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
            '<i class="fas fa-taxi gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Transfer').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
            '<span class="gks_menu_narrow_text">'.gks_lang('Transfer').'</span>'.
          '</a>
          <ul class="dropdown-menu" aria-labelledby="nav_transfer">'.
            $this_menu.'
          </ul>
        </li>';
      }

      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_transfer_reservation','add',0)) {
        $this_root_menu_transfer.=
        '<li class="nav-item gks_main_menu_transfer_new_reservation gks_header_add_new gks_menu_li_icon_add">'.
          '<a class="nav-link" href="/my/admin-transfer-reservation-item.php?id=-1"><i class="fas fa-plus-circle"></i></a>'.
        '</li>';
      }
      $this_root_menu_html.=$this_root_menu_transfer; 
    }
    
    if ($GKS_CRM_ENABLE) {
      
      
      $this_menu='';
      if ($GKS_CRM_LEADS_ENABLE) {
        if (gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_leads','add',0)) {
          $this_menu.='<li class="">'.
            '<a class="dropdown-item gks_main_menu_crm_new_lead" href="/my/admin-crm-lead-item.php?id=-1">'.gks_lang('Νέα Ευκαιρία').'</a>'.
          '</li>';
        }
        if (gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_leads','view',0)) {
          $this_menu.='<li class="">'.
            '<a class="dropdown-item gks_main_menu_crm_leads" href="/my/admin-crm-lead.php">'.gks_lang('Ευκαιρίες').'</a>'.
          '</li>';
        }
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_activity','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_activity" href="/my/admin-crm-activity.php">'.gks_lang('Δραστηριότητα').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_calendar','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_calendar" href="/my/admin-crm-calendar.php">'.gks_lang('Ημερολόγιο','part2').'</a>'.
        '</li>';
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_map','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_map" href="/my/admin-map.php">'.gks_lang('Χάρτης').'</a>'.
        '</li>';
      }
            
      
      if ($GKS_CRM_TASKS_ENABLE and gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_tasks','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_tasks" href="/my/admin-crm-task.php">'.gks_lang('Εργασίες').'</a>'.
        '</li>';
      }
      if ($GKS_CRM_MACHINE_ENABLE and gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_machine','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_machine" href="/my/admin-crm-machine.php">'.gks_lang('Συσκευές').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_ads_campain','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_ads_campain" href="/my/admin-ads-campain.php">'.gks_lang('Καμπάνιες').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_urlshort','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_urlshort" href="/my/admin-urlshort.php">'.gks_lang('Μικρό URL').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_urlshort_hit','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_urlshort_hit" href="/my/admin-urlshort-hit.php">'.gks_lang('Καταγραφές Μικρό URL').'</a>'.
        '</li>';
      }
      



      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
      
      
      
      
          
      
      $this_menu_sub='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_sms','add',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_mass_messages" href="/my/admin-mass-messages.php">'.gks_lang('Μαζική Αποστολή SMS-Viber-email').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_sms','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_smschat" href="/my/admin-sms-chat.php">'.gks_lang('Συζήτηση SMS').'</a>'.
        '</li>';
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_sms','add',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_smssend" href="/my/admin-sms-send.php">'.gks_lang('Αποστολή SMS').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_sms','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_smslog" href="/my/admin-log-sms.php">'.gks_lang('Καταγραφές SMS').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_sms_viber_template','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_sms_viber_templates" href="/my/admin-sms-viber-templates.php">'.gks_lang('Πρότυπα SMS-Viber').'</a>'.
        '</li>';
      }
      
      if ($this_menu_sub!='') {
        if (endwith($this_menu_sub,'<li class="dropdown-divider"></li>')) $this_menu_sub=substr($this_menu_sub, 0,strlen($this_menu_sub)-34);
        $this_menu.='<li class="dropdown-submenu">'.
          '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle gks_main_menu_manage_sms" aria-haspopup="true" aria-expanded="false">'.gks_lang('SMS').'</a>'.
          '<ul class="dropdown-menu">'.
          $this_menu_sub.
          '</ul>'.            
        '</li>';
      }
      
      
      $this_menu_sub='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_viber_msgs','add',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_vibersend" href="/my/admin-viber-send.php">'.gks_lang('Αποστολή Viber').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_viber_msgs','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_viberlog" href="/my/admin-viber-log.php">'.gks_lang('Καταγραφές Viber').'</a>'.
        '</li>';
      }      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_sms_viber_template','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_sms_viber_templates" href="/my/admin-sms-viber-templates.php">'.gks_lang('Πρότυπα SMS-Viber').'</a>'.
        '</li>';
      }      
      if ($this_menu_sub!='') {
        if (endwith($this_menu_sub,'<li class="dropdown-divider"></li>')) $this_menu_sub=substr($this_menu_sub, 0,strlen($this_menu_sub)-34);
        $this_menu.='<li class="dropdown-submenu">'.
          '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle gks_main_menu_manage_viber" aria-haspopup="true" aria-expanded="false">'.gks_lang('Viber').'</a>'.
          '<ul class="dropdown-menu">'.
          $this_menu_sub.
          '</ul>'.            
        '</li>';
      }      
      
      $this_menu_sub='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_email','add',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_emailsend" href="/my/admin-email-send.php">'.gks_lang('Αποστολή email').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_email','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_emaillog" href="/my/admin-log-emails.php">'.gks_lang('Καταγραφές emails').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_email_template','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_email_templates" href="/my/admin-email-templates.php">'.gks_lang('Πρότυπα emails').'</a>'.
        '</li>';
      }
      
      if ($this_menu_sub!='') {
        if (endwith($this_menu_sub,'<li class="dropdown-divider"></li>')) $this_menu_sub=substr($this_menu_sub, 0,strlen($this_menu_sub)-34);
        $this_menu.='<li class="dropdown-submenu">'.
          '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle gks_main_menu_manage_email" aria-haspopup="true" aria-expanded="false">'.gks_lang('emails').'</a>'.
          '<ul class="dropdown-menu">'.
          $this_menu_sub.
          '</ul>'.            
        '</li>';
      }
      
      
      
      
      $this_menu_sub='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_voip_calls','add',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_phone" href="/my/admin-phone.php">'.gks_lang('Τηλέφωνο').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_voip_calls','add',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_manage_phoneslog" href="/my/admin-log-phones.php">'.gks_lang('Καταγραφές Τηλεφώνων').'</a>'.
        '</li>';
      }
      if ($this_menu_sub!='') {
        if (endwith($this_menu_sub,'<li class="dropdown-divider"></li>')) $this_menu_sub=substr($this_menu_sub, 0,strlen($this_menu_sub)-34);
        $this_menu.='<li class="dropdown-submenu">'.
          '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle gks_main_menu_manage_phones" aria-haspopup="true" aria-expanded="false">'.gks_lang('Τηλέφωνο').'</a>'.
          '<ul class="dropdown-menu">'.
          $this_menu_sub.
          '</ul>'.            
        '</li>';
      }
      
      
      
      
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';


      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_leads_pivot10','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_leads_pivot10" href="/my/admin-reports-crm-leads-pivot10.php">'.gks_lang('Pivot Table - Ευκαιρίες').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_tasks_pivot1','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_tasks_pivot1" href="/my/admin-reports-crm-tasks-pivot1.php">'.gks_lang('Pivot Table - Εργασίες').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_machine_pivot11','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_machine_pivot11" href="/my/admin-reports-crm-machine-pivot11.php">'.gks_lang('Pivot Table - Συσκευές').'</a>'.
        '</li>';
      }      
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
   
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_channel_sale','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_channel_sale" href="/my/admin-crm-channel-sale.php">'.gks_lang('Κανάλια πωλήσεων').'</a>'.
        '</li>';
      }
      if ($GKS_CRM_LEADS_ENABLE and gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_leads_status','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_leads_status" href="/my/admin-crm-leads-status.php">'.gks_lang('Κατάσταση Ευκαιριών').'</a>'.
        '</li>';
      }
      if ($GKS_CRM_TASKS_ENABLE and gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_tasks_status','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_crm_tasks_status" href="/my/admin-crm-tasks-status.php">'.gks_lang('Κατάσταση Εργασιών').'</a>'.
        '</li>';
      }
    
      $this_root_menu_crm='';
      if ($this_menu!='') {
        if (endwith($this_menu,'<li class="dropdown-divider"></li>')) $this_menu=substr($this_menu, 0,strlen($this_menu)-34);
        $this_root_menu_crm='<li class="nav-item dropdown gks_main_menu_crm">
          <a id="nav_sales" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
            '<i class="fas fa-user-tie gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('CRM').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
            '<span class="gks_menu_narrow_text">'.gks_lang('CRM').'</span>'.
          '</a>
          <ul class="dropdown-menu" aria-labelledby="nav_sales">'.
            $this_menu.'
          </ul>
        </li>';
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_leads','add',0)) {
        $this_root_menu_crm.=
        '<li class="nav-item gks_main_menu_crm_new_lead gks_header_add_new gks_menu_li_icon_add">'.
          '<a class="nav-link" href="/my/admin-crm-lead-item.php?id=-1"><i class="fas fa-plus-circle"></i></a>'.
        '</li>';
        //echo $this_root_menu_crm;
               
      }
      $this_root_menu_html.=$this_root_menu_crm;
      
    }
    if ($GKS_WARE_HOUSE_ENABLE) {
      
      $this_menu='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov','add',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_warehouse_mov_new" href="/my/admin-whi-mov-item.php?id=-1">'.gks_lang('Νέο Δελτίο Αποστολής').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_warehouse_mov" href="/my/admin-whi-mov.php">'.gks_lang('Δελτία Αποστολής').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_aade_delivery_note','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_warehouse_aade_delivery_note" href="/my/admin-aade-delivery-note.php">'.gks_lang('ΑΑΔΕ Ψηφιακό δελτίο αποστολής').'</a>'.
        '</li>';
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov_balance','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_warehouse_balance" href="/my/admin-whi-mov-balance.php">'.gks_lang('Υπόλοιπα Ειδών').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov_balance_history','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_warehouse_history_product" href="/my/admin-whi-mov-product-history.php">'.gks_lang('Ιστορικό είδους σε αποθήκη').'</a>'.
        '</li>';
      }
      
      if ($GKS_PRODUCT_LOTS_SERIALS and gks_permission_user_can_action_php($my_wp_user_id,'gks_eshop_product_lots','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_product_lots" href="/my/admin-products-lots.php">'.gks_lang('Πατρίδες - Serial Numbers').'</a>'.
        '</li>';
      }
      if ($GKS_PRODUCT_LOTS_SERIALS and gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov_balance_lots_serials','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_warehouse_balance_lots_serials" href="/my/admin-whi-mov-balance-lots-serials.php">'.gks_lang('Υπόλοιπα Παρτίδων-Serial Numbers').'</a>'.
        '</li>';
      }
      if ($GKS_PRODUCT_LOTS_SERIALS and gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov_balance_lots_serials_history','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_warehouse_history_lots_serials" href="/my/admin-whi-mov-balance-lots-serials-history.php">'.gks_lang('Ιστορικό Παρτίδων-Serial Numbers').'</a>'.
        '</li>';
      }

      
      
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov_pivot8','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_warehouse_whi_mov_pivot8" href="/my/admin-reports-whi-mov-pivot8.php">'.gks_lang('Pivot Table - Δελτία').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov_pivot9','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_warehouse_whi_mov_pivot9" href="/my/admin-reports-whi-mov-pivot9.php">'.gks_lang('Pivot Table - Δελτία με Είδη').'</a>'.
        '</li>';
      }
            
            
            
            
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov','add',0)) {
        $sql_templates="select * from gks_users_templates where object_name in ('gks_whi_mov') and user_id=".$my_wp_user_id." order by object_name,object_name";      
        $result_templates = $db_link->query($sql_templates);      
        if (!$result_templates) {
          debug_mail(false,'error sql',$sql_templates);
          die();
        }
        $templates_html='';
        while ($row_templates = $result_templates->fetch_assoc()) {
          $temp='/my/admin-whi-mov-item.php?id=-1&template_id='.$row_templates['template_id'];
          $templates_html.='<li class="">'.
            '<a class="dropdown-item" href="'.$temp.'">'.$row_templates['template_name'].'</a>'.
          '</li>';
        }
        if ($templates_html!='') {
          $templates_html=
          '<li class="dropdown-submenu">'.
            '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle" aria-haspopup="true" aria-expanded="false">'.gks_lang('Πρότυπα').'</a>'.
            '<ul class="dropdown-menu">'.
              $templates_html.
            '</ul>'.
          '</li>';
          
          $this_menu.=$templates_html;
        }
            
      }
            
      $this_root_menu_warehouse='';
      if ($this_menu!='')  {
        $this_root_menu_warehouse='<li class="nav-item dropdown gks_main_menu_warehouse">'.
          '<a id="nav_warehouse" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
            '<i class="fas fa-warehouse gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Αποθήκη').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
            '<span class="gks_menu_narrow_text">'.gks_lang('Αποθήκη').'</span>'.
          '</a>'.
          '<ul class="dropdown-menu" aria-labelledby="nav_warehouse">'.
          $this_menu.
          '</ul>'.
        '</li>';
        
        
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_whi_mov','add',0)) {
        $this_root_menu_warehouse.=
          '<li class="nav-item gks_main_menu_warehouse_mov_new gks_header_add_new gks_menu_li_icon_add">'.
            '<a class="nav-link" href="/my/admin-whi-mov-item.php?id=-1"><i class="fas fa-plus-circle"></i></a>'.
          '</li>';
      }
      //echo $this_root_menu_warehouse;
      $this_root_menu_html.=$this_root_menu_warehouse;
    }
    
    
    if ($GKS_ORDERS_ENABLE) {
      $this_menu='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','add',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_sales_new_order" href="/my/admin-orders-item.php?id=-1">'.gks_lang('Νέα Παραγγελία').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_sales_orders" href="/my/admin-orders.php">'.gks_lang('Παραγγελίες').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_orders_pivot2','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_sales_orders_pivot2" href="/my/admin-reports-orders-pivot2.php">'.gks_lang('Pivot Table - Παραγγελίες').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_orders_pivot3','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_sales_orders_pivot3" href="/my/admin-reports-orders-pivot3.php">'.gks_lang('Pivot Table - Παραγγελίες με Είδη').'</a>'.
        '</li>';
      }
      
      gks_plugins_functions_run('_my_header_admin_menu_orders_pivot',array(
        'menu'=>&$this_menu,
      ));
          
          
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','add',0)) {
        $sql_templates="select * from gks_users_templates where object_name in ('gks_orders') and user_id=".$my_wp_user_id." order by object_name,object_name";      
        $result_templates = $db_link->query($sql_templates);      
        if (!$result_templates) {
          debug_mail(false,'error sql',$sql_templates);
          die();
        }
        $templates_html='';
        while ($row_templates = $result_templates->fetch_assoc()) {
          $temp='/my/admin-orders-item.php?id=-1&template_id='.$row_templates['template_id'];
          $templates_html.='<li class="">'.
            '<a class="dropdown-item" href="'.$temp.'">'.$row_templates['template_name'].'</a>'.
          '</li>';
        }
        if ($templates_html!='') {
          $templates_html=
          '<li class="dropdown-submenu">'.
            '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle" aria-haspopup="true" aria-expanded="false">'.gks_lang('Πρότυπα').'</a>'.
            '<ul class="dropdown-menu">'.
              $templates_html.
            '</ul>'.
          '</li>';
          
          $this_menu.=$templates_html;
        }
      }
            

      
      $this_root_menu_sales='';
      if ($this_menu!='') {
        $this_root_menu_sales='<li class="nav-item dropdown gks_main_menu_sales">'.
          '<a id="nav_sales" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
            '<i class="fas fa-coins gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Πωλήσεις').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
            '<span class="gks_menu_narrow_text">'.gks_lang('Πωλήσεις').'</span>'.
          '</a>'.
          '<ul class="dropdown-menu" aria-labelledby="nav_sales">'.
            $this_menu.
          '</ul>'.
        '</li>';
        
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_orders','add',0)) {
        $this_root_menu_sales.=
          '<li class="nav-item gks_main_menu_sales_new_order gks_header_add_new gks_menu_li_icon_add">'.
            '<a class="nav-link" href="/my/admin-orders-item.php?id=-1"><i class="fas fa-plus-circle"></i></a>'.
          '</li>';
      }
      //echo $this_root_menu_sales;
      $this_root_menu_html.=$this_root_menu_sales;
     
    
    }
    
    
    if ($GKS_ORDERS_PRODUCTION) {
      
      $this_menu='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_production_line','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_production_line" href="/my/admin-production-line.php">'.gks_lang('Γραμμές Παραγωγής').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta_select','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_production_posto_select" href="/my/admin-production-posto-select.php">'.gks_lang('Επιλογή Πόστου').'</a>'.
        '</li>';
      }
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';

      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_production_posta','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_production_posta" href="/my/admin-production-posta.php">'.gks_lang('Πόστα').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_production_ergasies','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_production_ergasies" href="/my/admin-production-ergasies.php">'.gks_lang('Εργασίες').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_production_bom','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_production_bom" href="/my/admin-production-bom.php">'.gks_lang('Συνταγές').'</a>'.
        '</li>';
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_production_ergasies_map','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_production_ergasies_map" href="/my/admin-production-ergasies-map.php">'.gks_lang('Χάρτης Εργασιών').'</a>'.
        '</li>';
      }
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_production_review','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_production_review" href="/my/admin-production-review.php">'.gks_lang('Επισκόπηση').'</a>'.
        '</li>';
      }
      
      $this_root_menu_production='';
      if ($this_menu!='') {
        if (endwith($this_menu,'<li class="dropdown-divider"></li>')) $this_menu=substr($this_menu, 0,strlen($this_menu)-34);
        $this_root_menu_production='<li class="nav-item dropdown gks_main_menu_production">'.
          '<a id="nav_production" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
          '<i class="fas fa-industry gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Παραγωγή').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
            '<span class="gks_menu_narrow_text">'.gks_lang('Παραγωγή').'</span>'.
          '</a>'.
          '<ul class="dropdown-menu" aria-labelledby="nav_production">'.
            $this_menu.
          '</ul>'.
        '</li>';
      }
      //echo $this_root_menu_production;
      $this_root_menu_html.=$this_root_menu_production;
    }
    
    

    if ($GKS_ACC_ENABLE) {
      $this_menu='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','add',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_inv_new" href="/my/admin-acc-inv-item.php?id=-1">'.gks_lang('Νέο Παραστατικό').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_inv" href="/my/admin-acc-inv.php">'.gks_lang('Παραστατικά').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv_pivot4','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_inv_pivot4" href="/my/admin-reports-acc-inv-pivot4.php">'.gks_lang('Pivot Table - Παραστατικά').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv_pivot5','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_inv_pivot5" href="/my/admin-reports-acc-inv-pivot5.php">'.gks_lang('Pivot Table - Παραστατικά με Είδη').'</a>'.
        '</li>';
      }
            
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_aade_docs','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_aade_docs" href="/my/admin-acc-aade-docs.php">'.gks_lang('Έγγραφα ΑΑΔΕ μέσω myData').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','add',-1)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_qrcode" href="/my/admin-qrcode.php">'.gks_lang('Σάρωση QR Code').'</a>'.
        '</li>';
      }
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay','add',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_pay_new" href="/my/admin-acc-pay-item.php?id=-1">'.gks_lang('Νέα Είσπραξη/Πληρωμή').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_pay" href="/my/admin-acc-pay.php">'.gks_lang('Πληρωμές/Εισπράξεις').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay_pivot6','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_pay_pivot6" href="/my/admin-reports-acc-pay-pivot6.php">'.gks_lang('Pivot Table - Πληρωμές').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay_pivot7','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_pay_pivot7" href="/my/admin-reports-acc-pay-pivot7.php">'.gks_lang('Pivot Table - Πληρωμές με Μέθοδο').'</a>'.
        '</li>';
      }
      
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';

      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_pos','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_pos" href="/my/admin-pos.php">'.gks_lang('Εντατική Λιανική').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_pos_run','add',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_pos_run" href="/my/admin-pos-run-select.php">'.gks_lang('Επιλογή σημείου Εντατικής Λιανικής').'</a>'.
        '</li>';
      }

      



      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
      
      
      $this_menu_sub='';
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_paroxos_signature','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_paroxos_signature" href="/my/admin-paroxos-signature.php">'.gks_lang('Ψηφιακές υπογραφές από πάροχο').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_eftpos_transaction','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_eftpos_eftpos" href="/my/admin-eftpos-transaction.php">'.gks_lang('Συναλλαγές EFT/POS').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_viva_transaction','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_eftpos_viva" href="/my/admin-viva-transaction.php">'.gks_lang('Συναλλαγές Viva').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_megeftpos_transaction','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_eftpos_megeftpos" href="/my/admin-megeftpos-transaction.php">'.gks_lang('Συναλλαγές Meg EFT/POS Driver').'</a>'.
        '</li>';
      }      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_mellon_transaction','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_eftpos_mellon" href="/my/admin-mellon-transaction.php">'.gks_lang('Συναλλαγές Mellon').'</a>'.
        '</li>';
      }      
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_cardlink_transaction','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_eftpos_cardlink" href="/my/admin-cardlink-transaction.php">'.gks_lang('Συναλλαγές Cardlink').'</a>'.
        '</li>';
      }      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_epay_transaction','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_eftpos_epay" href="/my/admin-epay-transaction.php">'.gks_lang('Συναλλαγές ePay').'</a>'.
        '</li>';
      }      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_worldline_transaction','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_eftpos_worldline" href="/my/admin-worldline-transaction.php">'.gks_lang('Συναλλαγές Worldline').'</a>'.
        '</li>';
      }      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_nexi_transaction','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_eftpos_nexi" href="/my/admin-nexi-transaction.php">'.gks_lang('Συναλλαγές NEXI').'</a>'.
        '</li>';
      }      
      if ($this_menu_sub!='') {
        $this_menu.='<li class="dropdown-submenu">'.
            '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle gks_main_menu_accounting_eftpos " aria-haspopup="true" aria-expanded="false">'.gks_lang('EFT/POS').'</a>'.
            '<ul class="dropdown-menu">'.
              $this_menu_sub.
            '</ul>'.
          '</li>';
      }
      
      $this_menu_sub='';
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks__paroxos_overview_ilyda','view',0)) {
        $this_menu_sub.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_paroxos_overview_ilyda" href="/my/admin-paroxos-overview-ilyda.php">'.gks_lang('ΙΛΥΔΑ').'</a>'.
        '</li>';
      }
      if ($this_menu_sub!='') {
        $this_menu.='<li class="dropdown-submenu">'.
            '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle gks_main_menu_accounting_paroxos_overview " aria-haspopup="true" aria-expanded="false">'.gks_lang('Επισκόπηση Παρόχου').'</a>'.
            '<ul class="dropdown-menu">'.
              $this_menu_sub.
            '</ul>'.
          '</li>';
      }      
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay_map','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_map" href="/my/admin-acc-map.php">'.gks_lang('Χάρτης Λογιστικής').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_gsis_check','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_gsis_check" href="/my/admin-gsis-check.php">'.gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_vies_check','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_accounting_vies_check" href="/my/admin-vies-check.php">'.gks_lang('VIES ΕΕ Επαλήθευση αριθ. ΦΠΑ').'</a>'.
        '</li>';
      }


     
      

      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','add',0) or 
          gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_pay','add',0)) {
        $sql_templates="select * from gks_users_templates where object_name in ('gks_acc_inv','gks_acc_pay') and user_id=".$my_wp_user_id." order by object_name,object_name";      
        $result_templates = $db_link->query($sql_templates);      
        if (!$result_templates) {
          debug_mail(false,'error sql',$sql_templates);
          die();
        }
        $templates_html='';
        while ($row_templates = $result_templates->fetch_assoc()) {
          $temp='';
          if ($row_templates['object_name']=='gks_acc_inv') {
            $temp='/my/admin-acc-inv-item.php?id=-1&template_id='.$row_templates['template_id'];
          } else if ($row_templates['object_name']=='gks_acc_pay') {
            $temp='/my/admin-acc-pay-item.php?id=-1&template_id='.$row_templates['template_id'];
          }
          $templates_html.='<li class="">'.
            '<a class="dropdown-item" href="'.$temp.'">'.$row_templates['template_name'].'</a>'.
          '</li>';
        }
        if ($templates_html!='') {
          $templates_html=
          '<li class="dropdown-submenu">'.
            '<a href="#" data-toggle="dropdown" class="dropdown-item dropdown-toggle" aria-haspopup="true" aria-expanded="false">'.gks_lang('Πρότυπα').'</a>'.
            '<ul class="dropdown-menu">'.
              $templates_html.
            '</ul>'.
          '</li>';
          
          $this_menu.=$templates_html;
        }
            
      }

      
      $this_root_menu_accounting='';
      if ($this_menu!='') {
        if (endwith($this_menu,'<li class="dropdown-divider"></li>')) $this_menu=substr($this_menu, 0,strlen($this_menu)-34);
        $this_root_menu_accounting='<li class="nav-item dropdown gks_main_menu_accounting">'.
          '<a id="nav_accounting" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
            '<i class="fas fa-file-invoice gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Λογιστική').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
            '<span class="gks_menu_narrow_text">'.gks_lang('Λογιστική').'</span>'.
          '</a>'.
          '<ul class="dropdown-menu" aria-labelledby="nav_accounting">'.
            $this_menu.
          '</ul>'.
        '</li>';
      }
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','add',0)) {
        $this_root_menu_accounting.=
        '<li class="nav-item gks_main_menu_accounting_inv_new gks_header_add_new gks_menu_li_icon_add">'.
          '<a class="nav-link" href="/my/admin-acc-inv-item.php?id=-1"><i class="fas fa-plus-circle"></i></a>'.
        '</li>';
      }
      //echo $this_root_menu_accounting;
      $this_root_menu_html.=$this_root_menu_accounting;
    } 
    

    if ($GKS_ASSETS_ENABLE) {
      
      
      $this_menu='';
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_assets','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_assets_assets" href="/my/admin-assets.php">'.gks_lang('Πάγια').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_moves','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_assets_moves" href="/my/admin-assets-moves.php">'.gks_lang('Κινήσεις Παγίων').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_service','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_assets_service" href="/my/admin-assets-service.php">'.gks_lang('Service').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_whi_mov','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_assets_whi_mov" href="/my/admin-assets-whi-mov.php">'.gks_lang('Απογραφές Παγίων').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_whi_mov','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_assets_whi_mov_not" href="/my/admin-assets-whi-mov-not.php">'.gks_lang('Πάγια που δεν έχουν απογραφεί').'</a>'.
        '</li>';
      }
      
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
   
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_type','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_assets_type" href="/my/admin-assets-type.php">'.gks_lang('Τύπος Παγίου').'</a>'.
        '</li>';
      }
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_assets_service_reasons','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_assets_service_reasons" href="/my/admin-assets-service-reasons.php">'.gks_lang('Αιτίες Service Παγίου').'</a>'.
        '</li>';
      }
      if ($this_menu!='' and endwith($this_menu,'<li class="dropdown-divider"></li>')==false) $this_menu.='<li class="dropdown-divider"></li>';
      
      if (gks_permission_user_can_action_php($my_wp_user_id,'gks_assets','view',0)) {
        $this_menu.='<li class="">'.
          '<a class="dropdown-item gks_main_menu_assets_oximata_sintirisi_report" href="/my/admin-assets-oximata-sintirisi-report.php">'.gks_lang('Αναφορά Συντήρησης Οχημάτων').'</a>'.
        '</li>';
      }
      
    
      $this_root_menu_assets='';
      if ($this_menu!='') {
        if (endwith($this_menu,'<li class="dropdown-divider"></li>')) $this_menu=substr($this_menu, 0,strlen($this_menu)-34);
        $this_root_menu_assets='<li class="nav-item dropdown gks_main_menu_assets">
          <a id="nav_sales" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
            '<i class="fas fa-boxes gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Πάγια').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
            '<span class="gks_menu_narrow_text">'.gks_lang('Πάγια').'</span>'.
          '</a>
          <ul class="dropdown-menu" aria-labelledby="nav_assets">'.
            $this_menu.'
          </ul>
        </li>';
      }
      
            
      $this_root_menu_html.=$this_root_menu_assets;
      
    }    
      
    if (GKS_LICENCE_EFS and ur_ad()) {
      $this_root_menu_licence_efs=
      '<li class="nav-item dropdown gks_main_menu_license">'.
        '<a id="nav_license" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.gks_lang('Άδειες').'</a>'.
        '<ul class="dropdown-menu" aria-labelledby="nav_license">'.
          '<li class="">'.
            '<a class="dropdown-item gks_main_menu_license_efs_license" href="/my/admin-efs-license.php">'.gks_lang('Άδειες Χρήσης').'</a>'.
          '</li>'.
          '<li class="">'.
            '<a class="dropdown-item gks_main_menu_license_efs_license_log" href="/my/admin-efs-license-log.php">'.gks_lang('Pings').'</a>'.
          '</li>'.
        '</ul>'.
      '</li>';
      $this_root_menu_html.=$this_root_menu_licence_efs;
    }        
    
    
    if (ur_ad()) { 
      $this_root_menu_stat=
      '<li class="nav-item dropdown gks_main_menu_stat">'.
        '<a id="nav_stat" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
          '<i class="fas fa-dot-circle gks_menu_narrow_icon tooltipstermenu" title="'.gks_lang('Καταγραφές').'" style = "font-size: 120%;position:relative;top:0px;"></i>'.
          '<span class="gks_menu_narrow_text">'.gks_lang('Καταγραφές').'</span>'.
        '</a>'.
        '<ul class="dropdown-menu" aria-labelledby="nav_stat">'.
          '<li class="">'.
            '<a class="dropdown-item gks_main_menu_stat_index" href="/my/admin-stat-index.php">'.gks_lang('Αρχική Στατιστικών').'</a>'.
          '</li>'.
          '<li class="">'.
            '<a class="dropdown-item gks_main_menu_stat_online"  href="/my/admin-stat-online.php">'.gks_lang('Online').'</a>'.
          '</li>'.
          '<li class="">'.
            '<a class="dropdown-item gks_main_menu_stat_lasthits"  href="/my/admin-stat-last300hits.php">'.gks_lang('Τελευταία hits').'</a>'.
          '</li>'.
          '<li class="" id="header_admin_menu_statbots">--</li>'.
          '<li class="">'.
            '<a class="dropdown-item"  href="/my/cron_ips.php">'.gks_lang('IPs-DNS-Χώρα').'</a>'.
          '</li>';
          
       
          gks_plugins_functions_run('_my_header_admin_menu_katagrafes',array(
            'menu'=>&$this_root_menu_stat,
          ));
          
        $this_root_menu_stat.=  
        '</ul>'.
      '</li>';
      
      $this_root_menu_html.=$this_root_menu_stat;
    }
    
    
    $this_root_menu_html.='</ul>';
    
    
    $this_root_menu_right='';
    $this_root_menu_right.=
    '<div class="form-inline my-2 my-lg-0" id="gks_header_search2_div">'.
      '<input id="gks_header_search2" class="gks_header_search_elem form-control mr-sm-2" type="search" placeholder="'.gks_lang('Αναζήτηση').'" aria-label="'.gks_lang('Αναζήτηση').'" autocomplete="one-time-code">'.
    '</div>';
  
    $this_root_menu_right.=
    
    '<ul class="navbar-nav ml-auto1 navbar-right">'. 
      '<li class="nav-item dropdown gks_main_menu_user">';
      
      if ($my_wp_user_id >0) {
  

        $user_photo_value="";
        $myimgurl = get_user_meta($my_wp_user_id, 'wsl_current_user_image', true);
        //echo $myimgurl;
        if ($myimgurl.'' == '') {
          $myimgurl='/my/img/avatar.png?v='.$gks_cache_version.'.'.$gks_menu_version;
        } else {
          $user_photo_value = $myimgurl;
        }
              
        $this_root_menu_right.=
        '<a id="nav_user" class="nav-link dropdown-toggle1" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'.
          '<img src="'.$myimgurl.'" border="0" style="max-width:32px;max-height:32px;position: relative;top:0px; border-radius: 50%;margin-right: 5px;" id="header_user_photo_img">';
          
          //echo wp_get_current_user()->display_name; 
          //<span class="caret"></span>
        $this_root_menu_right.=
          '<sup><span class="gks_notification_count gks_notification_count_style1 badge badge-pill badge-warning" style="display:none"></span></sup>'.
        '</a>'.
        '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="nav_user">'.
          '<li class="">'.
            '<span class="dropdown-item gks_main_menu_new_notification" id="new_notification">'.gks_lang('Νέα Ειδοποίηση').'</span>'.
          '</li>'.
          '<li class="">'.
            '<a class="dropdown-item gks_main_menu_notification"  href="/my/admin-notification.php">'.gks_lang('Ειδοποιήσεις').' <sup><span class="gks_notification_count gks_notification_count_style2 badge badge-pill badge-warning" style="display:none"></span></sup></a>'.
          '</li>';
                    
          

          $this_root_menu_right.=
          '<li class="">'.
            '<a class="dropdown-item gks_main_menu_profile"  href="/my/profile.php">'.gks_lang('Το Προφίλ μου').'</a>'.
          '</li>';
          if (gks_permission_user_can_action_php($my_wp_user_id,'gks_settings_users','view',0)) {
            $this_root_menu_right.=
            '<li class="">'.
              '<a class="dropdown-item gks_main_menu_manage_user_settings" href="/my/admin-user-settings.php">'.gks_lang('Οι Ρυθμίσεις μου').'</a>'.
            '</li>';
          }          
          if (ur_ad()) {
          $this_root_menu_right.=
          '<li class="">'.
            '<a class="dropdown-item gks_main_menu_my_permission"  href="/my/admin-users-item-permission.php?id='.$my_wp_user_id.'">'.gks_lang('Τα Δικαιώματά μου').'</a>'.
          '</li>';
          }
          $this_root_menu_right.=
          '<li class="">'.
            '<a class="dropdown-item gks_main_menu_2fa"  href="/my/admin-2fa.php" title="'.gks_lang('Έλεγχος ταυτότητας δύο παραγόντων').'">'.gks_lang('2FA').'</a>'.
          '</li>';
          $this_root_menu_right.=
          '<li class="">'.
            '<a class="dropdown-item"  href="'.wp_logout_url().'">'.gks_lang('Αποσύνδεση').'</a>'.
          '</li>';
          
          
      } else {
        $this_root_menu_right.=
        '<li class="nav-item">'.
          '<a class="nav-link" href="/wp-login.php?redirect_to='.urlencode(GKS_SITE_URL.'/my').'">'.gks_lang('Σύνδεση').'</a>'.
        '</li>';
      }
        
    $this_root_menu_right.='</ul>';
    
    $this_root_menu_html.=$this_root_menu_right;
    
    file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/'.$gks_menu_html_file,$this_root_menu_html);
  }
  
  echo '<div w3_include_my_header_admin_menu="/my/cache/'.$gks_menu_html_file.'?v='.$gks_cache_version.'"></div>';

      
 ?>                 
         



  
  
    
    
   
  </div>
</nav>
</gks_nav_parent>


<div id="gks_header_search_results" style="display:none;">
  <div id="gks_header_search_results_hourglass_div">
    <img id="gks_header_search_results_hourglass" src="img/progress_bar2.gif"/>
  </div>
  <div id="gks_header_search_results_html"></div>
</div>

<gks_main_content class="<?php if ($gks_user_settings['menu']['pos']=='left') echo 'gks_menu_pos_left'; else echo 'gks_menu_pos_top';?>">
  


<script src='/my/js/header_admin0.js?v=<?php echo $gks_cache_version;?>'></script>
<?php }
gks_erp_app_purchase_ads_header_ad();

