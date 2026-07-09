<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


if (isset($gks_lang_array) == false) $gks_lang_array = array();

function gks_lang_map_WPML_to_gks($lang) {
  if ($lang=='el') return 'el-GR';
  else if ($lang=='en') return 'en-US'; 
  else return 'en-US'; 
}
function gks_lang_map_gks_to_WPML($lang) {
  if ($lang=='el-GR') return 'el';
  else if ($lang=='en-US') return 'en';  
  else return 'en';
}
function gks_erp_supperted_lang($lang) {
  return $lang;
//  if ($lang=='el-GR') return 'el-GR';
//  if ($lang=='en-US') return 'en-US';
//  return 'en-US';
  
}
function gks_wpml_lang_convert_from_site_to_db($a) {
  $a=trim_gks($a);
  if ($a=='') return '';
  if ($a=='bg') return 'bg-BG';
  if ($a=='de') return 'de-DE';
  if ($a=='en') return 'en-US';
  if ($a=='fr') return 'fr-FR';
  if ($a=='it') return 'it-IT';
  if ($a=='sq') return 'sq-AL';
  if ($a=='sr') return 'sr-RS';
  if ($a=='tr') return 'tr-TR';
  
  return '';
}


function gks_datetimepicker_locale($lang) {
  if ($lang=='el-GR') return 'el';
  else if ($lang=='en-US') return 'en';  
  else if ($lang=='fr-FR') return 'fr';  
  else if ($lang=='de-DE') return 'de';  
  else if ($lang=='it-IT') return 'it';  
  else if ($lang=='sr-RS') return 'sr-YU';  
  else if ($lang=='bg-BG') return 'bg';  
  else if ($lang=='sq-AL') return 'sq';  
  else if ($lang=='tr-TR') return 'tr';  

  else return 'en';  
}

//https://www.tiny.cloud/docs/tinymce/latest/ui-localization/#using-the-premium-language-packs
//https://www.tiny.cloud/get-tiny/language-packages/
function gks_tinymce_locale($lang) {
  if ($lang=='el-GR') return 'el';
  else if ($lang=='en-US') return 'en';  
  else if ($lang=='fr-FR') return 'fr';  
  else if ($lang=='de-DE') return 'de';  
  else if ($lang=='it-IT') return 'it';  
  else if ($lang=='sr-RS') return 'sr-YU';  
  else if ($lang=='bg-BG') return 'bg';  
  else if ($lang=='sq-AL') return 'sq';  
  else if ($lang=='tr-TR') return 'tr';  

  else return 'en';  
}

function gks_fullcalendar_locale($lang) {
  if ($lang=='el-GR') return 'el';
  else if ($lang=='en-US') return 'en';  
  else if ($lang=='fr-FR') return 'fr';  
  else if ($lang=='de-DE') return 'de';  
  else if ($lang=='it-IT') return 'it';  
  else if ($lang=='sr-RS') return 'sr-YU';  
  else if ($lang=='bg-BG') return 'bg';  
  else if ($lang=='sq-AL') return 'sq';  
  else if ($lang=='tr-TR') return 'tr';  

  else return 'en';  
}


function gks_pivottable_locale($lang) {
  if ($lang=='el-GR') return 'el';
  else if ($lang=='en-US') return 'en';  
  else if ($lang=='fr-FR') return 'fr';  
  else if ($lang=='de-DE') return 'de';  
  else if ($lang=='it-IT') return 'it';  
  else if ($lang=='sr-RS') return 'sr-YU';  
  else if ($lang=='bg-BG') return 'bg';  
  else if ($lang=='sq-AL') return 'sq';  
  else if ($lang=='tr-TR') return 'tr';  

  else return 'en';  
}

function gks_jquery_multiselect_local() {
  global $gks_user_settings;
  global $GKS_LANG_DEFAULT;
  $load_lang=$GKS_LANG_DEFAULT;
  if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);


  if ($load_lang=='el-GR') return 'el';
  else if ($load_lang=='en-US') return '';  
  else if ($load_lang=='sr-RS') return 'sr-YU';  
  else if ($load_lang=='de-DE') return 'de';  
  else if ($load_lang=='fr-FR') return 'fr';  
  else if ($load_lang=='it-IT') return 'it';  
  else if ($load_lang=='tr-TR') return 'tr';  
  else if ($load_lang=='es-ES') return 'es';  
  else if ($load_lang=='cs-CZ') return 'cs';  
  else if ($load_lang=='hu-HU') return 'hu';  
  else if ($load_lang=='ja-JP') return 'ja';  
  else if ($load_lang=='pl-PL') return 'pl';  
  else if ($load_lang=='ru-RU') return 'ru';  
  else if ($load_lang=='zh-CN') return 'zh-cn';  
  else if ($load_lang=='zh-TW') return 'zh-tw';  
  

  else return '';  
   
}


function getWeekDayName($i,$load_lang='') {
  global $gks_user_settings;
  global $GKS_LANG_DEFAULT;
  if ($load_lang=='') {
    $load_lang=$GKS_LANG_DEFAULT;
    if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
  }
  //0 (for Sunday) through 6 (for Saturday)
  if ($i>=7) $i=$i-7;
  if ($i>=7) $i=$i-7;
  if ($i>=7) $i=$i-7;

  //echo '<pre>';echo $load_lang;die();
  
  $out='';
  //if ($load_lang=='el-GR') {
    switch ($i) {   
      case 0: $out = gks_lang('Κυριακή','part3');break;  
      case 1: $out = gks_lang('Δευτέρα','part3');break;  
      case 2: $out = gks_lang('Τρίτη','part3');break;  
      case 3: $out = gks_lang('Τετάρτη','part3');break;  
      case 4: $out = gks_lang('Πέμπτη','part3');break;  
      case 5: $out = gks_lang('Παρασκευή','part3');break;  
      case 6: $out = gks_lang('Σάββατο','part3');break;  
    }
//  } else {
//    switch ($i) {
//      case 0: $out = 'Sunday';break;  
//      case 1: $out = 'Monday';break;  
//      case 2: $out = 'Tuesday';break;  
//      case 3: $out = 'Wednesday';break;  
//      case 4: $out = 'Thursday';break;  
//      case 5: $out = 'Friday';break;  
//      case 6: $out = 'Saturday';break;  
//    }
//  }
  return $out;
}
function getMonthName($i,$load_lang='') {
  
  global $gks_user_settings;
  global $GKS_LANG_DEFAULT;
  if ($load_lang=='') {
    $load_lang=$GKS_LANG_DEFAULT;
    if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
  }
  
  //0 (for Sunday) through 6 (for Saturday)
  if ($i>12) $i=$i-12;
  if ($i>12) $i=$i-12;
  if ($i>12) $i=$i-12;
  
  $out='';
  //if ($load_lang=='el-GR') {
    switch ($i) {
      case 1:  $out = gks_lang('Ιανουάριος','part3');break;  
      case 2:  $out = gks_lang('Φεβρουάριος','part3');break;  
      case 3:  $out = gks_lang('Μάρτιος','part3');break;  
      case 4:  $out = gks_lang('Απρίλιος','part3');break;  
      case 5:  $out = gks_lang('Μάιος','part3');break;  
      case 6:  $out = gks_lang('Ιούνιος','part3');break;  
      case 7:  $out = gks_lang('Ιούλιος','part3');break;  
      case 8:  $out = gks_lang('Αύγουστος','part3');break;  
      case 9:  $out = gks_lang('Σεπτέμβριος','part3');break;  
      case 10: $out = gks_lang('Οκτώβριος','part3');break;  
      case 11: $out = gks_lang('Νοέμβριος','part3');break;  
      case 12: $out = gks_lang('Δεκέμβριος','part3');break;  
    }
//  } else {
//    switch ($i) {   
//      case 1: $out = 'January';break;  
//      case 2: $out = 'February';break;  
//      case 3: $out = 'March';break;  
//      case 4: $out = 'April';break;  
//      case 5: $out = 'May';break;  
//      case 6: $out = 'June';break;  
//      case 7: $out = 'July';break;  
//      case 8: $out = 'August';break;  
//      case 9: $out = 'September';break;  
//      case 10: $out = 'October';break;  
//      case 11: $out = 'November';break;  
//      case 12: $out = 'December';break;  
//    }
//  }
  return $out;
}



function gks_load_lang($a='') {
  if ($a=='') $a='generic.php';

  global $gks_lang_array;  
  global $_gks_session;
  global $gks_user_settings;
  
  //return as $gks_load_lang_filename = gks_load_lang
  //print '<pre>';print_r($_gks_session);//die();
  
  //$load_lang='el-GR';
  //if (isset($_gks_session['gks']['ui_lang'])) $load_lang = gks_erp_supperted_lang($_gks_session['gks']['ui_lang']);
  //Echo '<pre>111111111111 '.$a.'|';print_r($_gks_session); die();
  
  $load_lang='el-GR';
  if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
  
  //echo '<pre>qqqqqqqqqqqqqqqqqqqqqq '.$a.'|'.$load_lang."\r\n</pre>";//print_r($gks_user_settings); die();
  
  if ($load_lang=='el-GR') return;
  
  $file_name = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/languages/'.$load_lang.'/'.$a;
  //echo $file_name;die();

  if (file_exists($file_name)) {
    //echo $file_name;
    include $file_name;
    //return;
  } else {
    file_put_contents(GKS_SITE_PATH.'logs/language_'.$load_lang.'_file_not_found.log',date('Y-m-d H:i:s').' '.$file_name."\n",FILE_APPEND);
    $file_name = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/languages/en-US/'.$a;
    
    if (file_exists($file_name)) {
      include $file_name;
      //return;
    } else {
      file_put_contents(GKS_SITE_PATH.'logs/language_en-US_file_not_found.log',date('Y-m-d H:i:s').' '.$file_name."\n",FILE_APPEND);
      
    }
  }
  
  if ($a=='generic.php') gks_load_lang('part2.php');
  if ($a=='part2.php') gks_load_lang('part3.php');
  if ($a=='part3.php') gks_load_lang('part4.php');
  
}


function gks_lang($key,$file='generic',$item='') {
  global $gks_lang_array;
  global $_gks_session;
  global $gks_user_settings;
  
  //if ($_gks_session['gks']['ui_lang']=='el-GR') return $key;
  //if (isset($gks_user_settings['lang']['backend']) and $gks_user_settings['lang']['backend']=='el-GR') return $key;
  //if (isset($gks_user_settings['lang'])==false) return $key;
  if (isset($gks_user_settings['lang']['backend'])==false) return $key;
  if ($gks_user_settings['lang']['backend']=='el-GR') return $key;

  if ($file=='part4') {
    if (isset($gks_lang_array[$file][$item][$key])) return $gks_lang_array[$file][$item][$key];
  } else {
    if (isset($gks_lang_array[$file][$key])) return $gks_lang_array[$file][$key];
  }
  //file_put_contents(GKS_SITE_PATH.'logs/language_'.$_gks_session['gks']['ui_lang'].'_'.$file.'.log',date('Y-m-d H:i:s').' '.$key."\n",FILE_APPEND);
  file_put_contents(GKS_SITE_PATH.'logs/language_'.$gks_user_settings['lang']['backend'].'_'.$file.'.log',$key."\n",FILE_APPEND);
  return $key;
  
}
function gks_lang_big_texts($file) {
  global $gks_cache_version;
  global $gks_user_settings;
  $big_text_js='languages/el-GR/big_texts_'.$file.'.js?v='.$gks_cache_version;
  if (isset($gks_user_settings['lang']['backend']) and $gks_user_settings['lang']['backend']!='el-GR') {
    $lang_file=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/languages/'.$gks_user_settings['lang']['backend'].'/big_texts_'.$file.'.js';
    if (file_exists($lang_file)) {
      $big_text_js='languages/'.$gks_user_settings['lang']['backend'].'/big_texts_'.$file.'.js?v='.$gks_cache_version;
    }
  }
  return '<script src="'.$big_text_js.'"></script>'."\r\n";
}


function gks_set_lang_url() {
  global $sitepress;
  global $_gks_session;
  
  if (function_exists('icl_object_id') == false ) { //is WPML ?
    return '';
//    $languages = apply_filters( 'wpml_active_languages', NULL, '' );
//    return '<pre>'.print_r($languages,true).'</pre>';
//    return 'WPML is active';  
  }
  $def_lang=$sitepress->get_default_language();
  $curr_lang=gks_lang_map_gks_to_WPML($_gks_session['gks']['ui_lang']);
  //echo 'gggg '.print_r($_GET,true) .' '.$def_lang.' '.$curr_lang.' ';
  if ($def_lang == $curr_lang) return '';
  $out='/?lang='.$curr_lang;
  return $out;
}


/*
function gks_translate_init($lang) {
  //$lang='en-US';
  if ($lang=='el-GR') {
    setlocale(LC_ALL, 'el_GR.UTF-8');
  } else if ($lang=='en-US') {
    setlocale(LC_ALL, 'en_US.UTF-8');
//  } else {
//    setlocale(LC_ALL, $lang.'.UTF-8');
    
  }

  //echo $lang;die();
  
  setlocale(LC_NUMERIC, 'en_US.UTF-8');
  setlocale(LC_MONETARY,'en_US.UTF-8');
  //$locale_info = localeconv();
  //print'<pre>'.$lang; print_r($locale_info);die();

//  en-US print_r($locale_info);
//  Array
//  (
//      [decimal_point] => .
//      [thousands_sep] => ,
//      [int_curr_symbol] => USD 
//      [currency_symbol] => $
//      [mon_decimal_point] => .
//      [mon_thousands_sep] => ,
//      [positive_sign] => 
//      [negative_sign] => -
//      [int_frac_digits] => 2
//      [frac_digits] => 2
//      [p_cs_precedes] => 1
//      [p_sep_by_space] => 0
//      [n_cs_precedes] => 1
//      [n_sep_by_space] => 0
//      [p_sign_posn] => 1
//      [n_sign_posn] => 1
//      [grouping] => Array
//          (
//              [0] => 3
//              [1] => 3
//          )
//  
//      [mon_grouping] => Array
//          (
//              [0] => 3
//              [1] => 3
//          )
//  
//  )
  
//  el-GR print_r($locale_info);
//  Array
//  (
//      [decimal_point] => ,
//      [thousands_sep] => .
//      [int_curr_symbol] => EUR 
//      [currency_symbol] => €
//      [mon_decimal_point] => ,
//      [mon_thousands_sep] => .
//      [positive_sign] => 
//      [negative_sign] => -
//      [int_frac_digits] => 2
//      [frac_digits] => 2
//      [p_cs_precedes] => 0
//      [p_sep_by_space] => 0
//      [n_cs_precedes] => 1
//      [n_sep_by_space] => 0
//      [p_sign_posn] => 1
//      [n_sign_posn] => 1
//      [grouping] => Array
//          (
//          )
//  
//      [mon_grouping] => Array
//          (
//              [0] => 3
//          )
//  
//  )
  
    
}


function gks_translate_add_file($file) {
  if (endwith($file,'.php')) $file=substr($file, 0, strlen($file)-4);
  bindtextdomain($file, 'i18n');
  //echo $file;die();
}


function gks_tra_text($file,$text) {
  return dgettext($file, $text);
}


<?php echo gks_tra_text($gks_curr_tra_file,'   ');?>
gks_tra_text($gks_curr_tra_file,'  ');

*/

