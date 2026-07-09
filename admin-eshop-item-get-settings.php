<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);

if ($id<=0) {
  debug_mail(false,'error on id');
  $return = array('success' => false, 'message' => base64_encode('error on ID<br>'.gks_lang('Ανανεώστε την σελίδα')));
  echo json_encode($return); die();}


  
$my_page_title=gks_lang('Λήψη ρυθμίσεων WooCommerce');
db_open();
stat_record();


//$return = array('success' => false, 'message' => base64_encode('id:'.$id));
//echo json_encode($return); die();  

$ret = gks_woo_get_eshop($id);
if ($ret['success']==false) {
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die(); 
}
//$return = array('success' => false, 'message' => base64_encode(print_r($eshop,true)));
//echo json_encode($return); die(); 
$eshop=$ret['eshop'];


$data = array(
	'cmd'=>'get_woo_settings',
);
$ret = gks_woo_post($eshop, $data);
if ($ret['success']==false) {
  $return = array('success' => false, 'message' => $ret['message']);
  echo json_encode($return); die(); 
}
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($ret,true)));
//echo json_encode($return); die();  
//echo '<pre>';print_r($ret);die();

$response_array=$ret['response_array'];
if ($response_array['success']==false) {
  $return = array('success' => false, 'message' => $response_array['message']);
  echo json_encode($return); die();}

$woo_settings=$response_array['woo_settings'];

$wpml_enable=0;$wpml_icl_language_code='';$wpml_default_lang='';$wpml_default_lang_code='';$wpml_languages='';
$data=array();
$data['html']='';
if (isset($woo_settings['wpml_enable'])) {
  $wpml_enable=($woo_settings['wpml_enable'] ? 1 : 0);
  $data['html'].='WPML: <b>'.($woo_settings['wpml_enable'] ? gks_lang('Ναι') : gks_lang('Όχι')).'</b><br>'; 
  if ($wpml_enable and $woo_settings['wpml_ICL_LANGUAGE_CODE']) {
    $wpml_icl_language_code=$woo_settings['wpml_ICL_LANGUAGE_CODE'];
    $data['html'].='WPML ICL_LANGUAGE_CODE: <b>'.$woo_settings['wpml_ICL_LANGUAGE_CODE'].'</b><br>'; 
  }
  
  if (isset($woo_settings['wpml_languages'])) {
    $wpml_languages=serialize($woo_settings['wpml_languages']);
    $temp=[];
    foreach ($woo_settings['wpml_languages'] as $value) {
      $temp[]=$value['translated_name'] .' ('.$value['language_code'].')';
      if (isset($woo_settings['wpml_default_lang']) and $woo_settings['wpml_default_lang']==$value['language_code']) {
        $wpml_default_lang=$value['translated_name'];
        $wpml_default_lang_code=$woo_settings['wpml_default_lang'];
      }
    }
    if (count($temp)>0) {
      $data['html'].='WPML '.gks_lang('Γλώσσες').': <b>'.implode(', ',$temp).'</b><br>'; 
    } 
  }
  if ($wpml_default_lang!='') {
     $data['html'].='WPML '.gks_lang('Προεπιλεγμένη γλώσσα').': <b>'.$wpml_default_lang.'</b><br>'; 
    
  }
}
$data['html'].=gks_lang('Έκδοση WooCommerce').': <b>'.$woo_settings['version'].'</b><br>';
$data['html'].=gks_lang('Νόμισμα').': <b>'.$woo_settings['currency'].'</b><br>';
$data['html'].=gks_lang('Μονάδα Βάρους').': <b>'.$woo_settings['weight_unit'].'</b><br>';
$data['html'].=gks_lang('Μονάδα διαστάσεων').': <b>'.$woo_settings['dimension_unit'].'</b><br>';
$data['html'].=gks_lang('Ενεργοποίηση φόρων').': <b>'.($woo_settings['calc_taxes']=='yes' ? gks_lang('Ναι') : gks_lang('Όχι')).'</b><br>';
$data['html'].=gks_lang('Οι Τιμές Εισάγονται Με Φόρο').': <b>'.($woo_settings['prices_include_tax']=='yes' ? gks_lang('Ναι') : gks_lang('Όχι')).'</b><br>';
$data['html'].=gks_lang('Διαχείριση του αποθέματος').': <b>'.($woo_settings['manage_stock']=='yes' ? gks_lang('Ναι') : gks_lang('Όχι')).'</b><br>';

$data['woo_settings']=$woo_settings;


$wootaxes=array();
foreach ($woo_settings['wootaxes'] as $tax) {
  $wootaxes[]=$tax['name'];
}



$data['html'].=gks_lang('Κλάσεις ΦΠΑ').': <b>'.(count($wootaxes)==0 ? '' : implode(', ',$wootaxes)).'</b><br>';

$delivery=$woo_settings['delivery'];
$payments=$woo_settings['payments'];

$sql="update gks_eshops set 
wpml_enable =".$wpml_enable.",
wpml_icl_language_code='".$db_link->escape_string($wpml_icl_language_code)."',
wpml_default_lang='".$db_link->escape_string($wpml_default_lang)."',
wpml_default_lang_code='".$db_link->escape_string($wpml_default_lang_code)."',
wpml_languages='".$db_link->escape_string($wpml_languages)."',

woo_version='".$db_link->escape_string($woo_settings['version'])."',
woo_currency='".$db_link->escape_string($woo_settings['currency'])."',
woo_weight_unit='".$db_link->escape_string($woo_settings['weight_unit'])."',
woo_dimension_unit='".$db_link->escape_string($woo_settings['dimension_unit'])."',
woo_calc_taxes=".($woo_settings['calc_taxes']=='yes' ? 1 : 0).",
woo_prices_include_tax=".($woo_settings['prices_include_tax']=='yes' ? 1 : 0).",
woo_manage_stock=".($woo_settings['manage_stock']=='yes' ? 1 : 0).",
woo_taxes='".$db_link->escape_string(serialize($wootaxes))."',

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_eshop=".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 


  
$return = array('success' => true, 'message' => base64_encode('ok'),'data' => $data, 'wootaxes' => $wootaxes, 'delivery' => $delivery, 'payments' => $payments);
echo json_encode($return); die();  
