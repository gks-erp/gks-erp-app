<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_transfer_cmd_transfer_destinations($id_transfer,$row_transfer,$input_data) {
  global $db_link;
  global $gkIP;
  global $gks_cache_version;
  global $_gks_session;
  global $_gks_id_session;
  global $gks_user_settings;
  
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW;
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;

  $gks_erp_cookie_id='';
  if(isset($input_data['gks_erp_cookie_id'])) {
    $gks_erp_cookie_id = $input_data['gks_erp_cookie_id'];
  }
  $transfer_title=$row_transfer['transfer_title'];
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  gks_erp_cookie_start($gks_erp_cookie_id);
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  $db_lang='';$db_lang2='';if ($_gks_session['gks']['ui_lang']=='en-US') {$db_lang='_en_US';$db_lang2='_en';}
  
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  $return=array('success' => false, 'message' => base64_encode('generic gks_api_transfer_cmd_transfer_destinations error'),'data' => false, 'debug'=>'');
  $error_html=[];
  
  $data=array();
//  $sql="SELECT gks_transfer_pricelist.poi_id_from, gks_poi.poi_descr, Count(gks_transfer_pricelist.id_transfer_pricelist) AS cc
//  FROM gks_transfer_pricelist LEFT JOIN gks_poi ON gks_transfer_pricelist.poi_id_from = gks_poi.id_poi
//  WHERE gks_poi.poi_disable=0 and gks_transfer_pricelist.transfer_pricelist_disable=0
//  GROUP BY gks_transfer_pricelist.poi_id_from, gks_poi.poi_descr
//  ORDER BY gks_poi.poi_descr;";

  $sql="SELECT gks_poi.id_poi, gks_poi_en_US.poi_descr_en_US, gks_poi.poi_type_id
  Count(gks_transfer_pricelist.id_transfer_pricelist) AS cc
  FROM ((gks_transfer_pricelist 
  LEFT JOIN gks_poi ON gks_transfer_pricelist.poi_id_from = gks_poi.id_poi)
  LEFT JOIN (
    SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
  ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id)
  LEFT JOIN gks_transfer_oxima_type ON gks_transfer_pricelist.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type
  WHERE (((gks_poi.poi_disable)=0) 
  AND ((gks_transfer_pricelist.transfer_pricelist_disable)=0) 
  AND ((gks_transfer_oxima_type.transfer_oxima_type_disable)=0))
  GROUP BY gks_poi.id_poi, gks_poi_en_US.poi_descr_en_US, gks_poi.poi_type_id
  ORDER BY gks_poi_en_US.poi_descr_en_US;";


  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
    $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
  
  
  while ($row = $result->fetch_assoc()) {
    $row['id_poi']=intval($row['id_poi']);
    $row['poi_type_id']=intval($row['poi_type_id']);
    $row['poi_descr_en_US']=trim_gks($row['poi_descr_en_US']);
    $row['cc']=intval($row['cc']);
    $row['locations']=array();
    $data[$row['id_poi']]=$row;
  }
  
  foreach ($data as $myid => &$dest) {
    $sql="SELECT gks_transfer_pricelist.poi_id_to, gks_poi_en_US.poi_descr_en_US, 
    Count(gks_transfer_pricelist.id_transfer_pricelist) AS cc, 
    Min(transfer_pricelist_price_per_transfer) AS min_per_transfer, 
    Min(transfer_pricelist_price_per_transfer_offer) AS min_per_transfer_offer
    FROM ((gks_transfer_pricelist 
    LEFT JOIN gks_poi ON gks_transfer_pricelist.poi_id_to = gks_poi.id_poi) 
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id)
    LEFT JOIN gks_transfer_oxima_type ON gks_transfer_pricelist.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type
    WHERE gks_transfer_pricelist.poi_id_from=".$myid." 
    AND gks_transfer_pricelist.transfer_pricelist_price_per_transfer>0
    AND poi_disable=0
    AND transfer_oxima_type_disable=0
    AND transfer_oxima_type_roure_group_one=1
    AND transfer_pricelist_disable=0
    GROUP BY gks_transfer_pricelist.poi_id_to, gks_poi_en_US.poi_descr_en_US
    order by gks_poi_en_US.poi_descr_en_US";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
      $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}
    
    while ($row = $result->fetch_assoc()) {
      $row['poi_id_to']=intval($row['poi_id_to']);
      $row['cc']=intval($row['cc']);
      $row['min_per_transfer']=floatval($row['min_per_transfer']);
      $row['min_per_transfer_offer']=floatval($row['min_per_transfer_offer']);
      $row['subs']=array();
      $dest['locations'][]=$row;
    }
  }
  unset($dest);
   
  
  $sql="SELECT id_poi, poi_parent_id, poi_type_id, gks_poi_en_US.poi_descr_en_US
  FROM gks_poi
  LEFT JOIN (
    SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
  ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id  
  
  WHERE poi_parent_id>0 AND poi_descr_en_US<>'' AND poi_disable=0
  ORDER BY poi_descr_en_US";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,"gks_transfer_search_poi sql error", $sql."\n".$db_link->errno . '-'.$db_link->error);
    $return['message']=base64_encode(gks_lang('Εσωτερικό σφάλμα συστήματος (sql error)').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));return $return;}

  while ($row = $result->fetch_assoc()) {
    foreach ($data as $myid => &$dest) {
      foreach ($dest['locations'] as &$mylocation) {
        if ($mylocation['poi_id_to']==$row['poi_parent_id']) {
          
          $row['id_poi']=intval($row['id_poi']);
          $row['poi_parent_id']=intval($row['poi_parent_id']);
          $row['poi_type_id']=intval($row['poi_type_id']);
          $row['poi_descr_en_US']=trim_gks($row['poi_descr_en_US']);
          
          $mylocation['subs'][]=$row;
          break 2;
        }
      } 
      unset($mylocation);
    }
    unset($dest);
  }
  
  
  $return['data']=$data;
  $return['success']=true;
  return $return;
  
  
 
  
}


