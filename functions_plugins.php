<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

/*
woo_import_coupon_after
woo_import_order_after

*/

$gks_plugins_curr_plugin='';

function gks_plugins_load() {
  global $GKS_PLUGINS_ENABLED;
  global $gks_plugins_data;
  global $gks_plugins_curr_plugin;
  //echo '<pre>'; print_r($GKS_PLUGINS_ENABLED);die();  
  
  if (count($GKS_PLUGINS_ENABLED)==0) return;

  $gks_plugins_data=array();
  $gks_plugins_data['plugins']=array();
  $gks_plugins_data['functions']=array();
  
  foreach ($GKS_PLUGINS_ENABLED as $value) {
    $path=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/plugins/'.$value.'/functions.php';
    if (file_exists($path)) {
      $gks_plugins_curr_plugin=$value;
      include_once($path);
      
      $gks_plugins_data['plugins'][$value]=array(
        'name' => $value,
        'path' => $path,
        'info' => $gks_plugin_info,
      );
    }
  }
  
  foreach ($gks_plugins_data['functions'] as $location_key => $location) {
    usort($gks_plugins_data['functions'][$location_key], "gks_plugins_functions_sort");  
  } 
  //echo '<pre>'; print_r($gks_plugins_data);die();  
}

function gks_plugins_functions_load($location,$function_name,$num_params=0,$order=10) {
  global $gks_plugins_data;
  global $gks_plugins_curr_plugin; 

  if ($location=='') return;
  if ($function_name=='') return;
  
  if (isset($gks_plugins_data['functions'][$location])==false) {
     $gks_plugins_data['functions'][$location]=array();
  }
  $gks_plugins_data['functions'][$location][]=array(
    'plugin' => $gks_plugins_curr_plugin,
    'name' => $function_name,
    'num_params' => $num_params,
    'sortorder' => $order,
    
  );
  
}


function gks_plugins_functions_sort($a, $b) {
  if ($b['sortorder'] > $a['sortorder']) return -1;
  if ($b['sortorder'] < $a['sortorder']) return 1;
  
  $collator = new Collator('el_GR');
  return $collator -> compare ($a['name'],$b['name']);
}

function gks_plugins_functions_run($location) {
  global $gks_plugins_data;
  if ($location=='') return;
  if (isset($gks_plugins_data['functions'][$location])==false)  return;
  if (count($gks_plugins_data['functions'][$location])==0) return;
  
  foreach ($gks_plugins_data['functions'][$location] as $myfnc) {
     if (function_exists($myfnc['name'])) {
      $arg_list = func_get_args();
      unset($arg_list[0]);

      $refs = array();
      foreach($arg_list as $key => &$arg_item)   $refs[] =&$arg_item;
          
      
      //echo '<pre>';var_dump($refs);die();
      //call_user_func_array($myfnc['name'],$arg_list);
      
      call_user_func_array($myfnc['name'],$refs);
      
      //echo '<pre>';var_dump($arg_list);die();
      
      //echo $myfnc['name'];die();
    }
  }
}
