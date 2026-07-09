<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id<>-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Υπολογισμός δελτίου αποστολής').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}




$mydata_str = trim_gks(base64_decode($_POST['mydata_str']));
//$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 

$mydata = json_decode($mydata_str, true);
if ($mydata === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['mydata_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}


if (isset($mydata['gks_lock']) and $mydata['gks_lock']==true) {
  
  $sql=select_gks_whi_mov()." where gks_whi_mov.id_whi_mov = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row = $result->fetch_assoc();
  
  //$row['tropos_apostolis']=$mydata['tropos_apostolis'];
  //$row['tropos_pliromis'] =$mydata['tropos_pliromis'];
  
  $products_posotita=$row['products_posotita'];
  $products_varos=$row['products_varos'];
  $products_ogos=$row['products_ogos'];;
  $products_ogos_max_x=$row['products_ogos_max_x'];
  $products_ogos_max_y=$row['products_ogos_max_y'];
  $products_ogos_max_z=$row['products_ogos_max_z'];
  $products_need_apostoli=$row['products_need_apostoli']==0 ? false : true;
  
  
  unset($mybasketarray);
  gks_mybasketarray_create($mybasketarray);
  $mybasketarray['from']='whi_mov';
  $mybasketarray['id_object'] = $id;
  $mybasketarray['company_id']= $row['company_id'];
  $mybasketarray['company_sub_id']= $row['company_sub_id'];
  $mybasketarray['mov_whi_journal_id']=intval($row['mov_whi_journal_id']);
  $mybasketarray['mov_whi_seira_id']=intval($row['mov_whi_seira_id']);
  $mybasketarray['mov_state']=trim_gks($row['mov_state']);
  $mybasketarray['mov_date']=trim_gks($row['mov_date']);
  
  $mybasketarray['user']['user_id']=$row['user_id'];
  $mybasketarray['user']['afm']=$row['afm'];
  $mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
  $mybasketarray['parastatiko']= $row['eidos_parastatikou_need_afm'];
  
  
  
  $mybasketarray['products_varos']= $products_varos;
  $mybasketarray['products_ogos']= $products_ogos;
  $mybasketarray['products_ogos_max_x']= $products_ogos_max_x;
  $mybasketarray['products_ogos_max_y']= $products_ogos_max_y;
  $mybasketarray['products_ogos_max_z']= $products_ogos_max_z;
  $mybasketarray['products_need_apostoli']=$products_need_apostoli;
  $mybasketarray['products_need_pliromi']=false;
  $mybasketarray['destination_data']['name'] = trim_gks($row['destination_data_name']);
  $mybasketarray['destination_data']['phone'] = trim_gks($row['destination_data_phone']);
  $mybasketarray['destination_data']['odos'] = trim_gks($row['destination_data_odos']);
  $mybasketarray['destination_data']['arithmos'] = trim_gks($row['destination_data_arithmos']);
  $mybasketarray['destination_data']['orofos'] = trim_gks($row['destination_data_orofos']);
  $mybasketarray['destination_data']['perioxi'] = trim_gks($row['destination_data_perioxi']);
  $mybasketarray['destination_data']['poli'] = trim_gks($row['destination_data_poli']);
  $mybasketarray['destination_data']['tk'] = trim_gks($row['destination_data_tk']);
  $mybasketarray['destination_data']['country_id'] = intval($row['destination_data_country_id']);
  $mybasketarray['destination_data']['nomos_id'] = intval($row['destination_data_nomos_id']);
  $mybasketarray['products_total'] = 0;
  
  $mybasketarray['tropos_apostolis'] = intval($mydata['tropos_apostolis']);
  $mybasketarray['tropos_pliromis']=0;

  
  $mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);


  $kostos_apostolis_mode=''; if (isset($mydata['kostos_apostolis_mode'])) $kostos_apostolis_mode=trim_gks($mydata['kostos_apostolis_mode']);
  
  if ($kostos_apostolis_mode=='manual') $mybasketarray['kostos_apostolis']=$mydata['kostos_apostolis'];
    




  $cache_file='whi_mov_'.$id.'_'.date('Y-m-d_H-i-s').'_'.rand(10000,99999).'.json';
  file_put_contents(GKS_CACHE.$cache_file,json_encode($mybasketarray));
  //if (GKS_DEBUG) file_put_contents(GKS_CACHE.'basket.txt',print_r($mybasketarray,true));

  $return = array('success' => true, 'message' => base64_encode('OK'),
    //'eidi_array' => $eidi_array,
    'kostos_apostolis'  => (myCurrencyFormat($mybasketarray['kostos_apostolis'],true,true)),
    'kostos_apostolis_val' => number_format($mybasketarray['kostos_apostolis'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', ''),
    
    'tropoi_apostolis_all' => $mybasketarray['tropoi_apostolis_all'],
    
    'cache_file' =>$cache_file,
  );
  echo json_encode($return); die();


  
}


//print '<pre>';print_r($mydata);die();

$eidi_array=$mydata['eidi_array'];


$mycmd='';if (isset($mydata['mycmd'])) $mycmd=trim_gks($mydata['mycmd']);
$myfile='';if (isset($mydata['myfile'])) $myfile=trim_gks($mydata['myfile']);



unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);

$mybasketarray['from']='whi_mov';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']=intval($mydata['company_id']);
$mybasketarray['company_sub_id']=intval($mydata['company_sub_id']);
$mybasketarray['mov_whi_journal_id']=intval($mydata['mov_whi_journal_id']);
$mybasketarray['mov_whi_seira_id']=intval($mydata['mov_whi_seira_id']);
$mybasketarray['mov_state']=trim_gks($mydata['mov_state']);
$mybasketarray['mov_date']='';
if ($mydata['mov_date'] == '__/__/____ __:__') $mydata['mov_date']='';
$mydata['mov_date']=trim_gks(stripslashes(urldecode($mydata['mov_date'])));
if ($mydata['mov_date']!='') {
  $mybasketarray['mov_date'] = mystrtodb($mydata['mov_date']);
}
//echo '<pre>'; echo $mybasketarray['mov_date']; die();

$mybasketarray['user']['user_id']=$mydata['user_id'];
$mybasketarray['user']['first_name']=$mydata['first_name'];
$mybasketarray['user']['last_name']=$mydata['last_name'];
$mybasketarray['user']['email']=$mydata['email'];
$mybasketarray['user']['mobile']=$mydata['mobile'];
$mybasketarray['user']['lang']=$mydata['lang'];
$mybasketarray['user']['ma_odos']=$mydata['ma_odos'];
$mybasketarray['user']['ma_arithmos']=$mydata['ma_arithmos'];
$mybasketarray['user']['ma_orofos']=$mydata['ma_orofos'];
$mybasketarray['user']['ma_perioxi']=$mydata['ma_perioxi'];
$mybasketarray['user']['ma_poli']=$mydata['ma_poli'];
$mybasketarray['user']['ma_tk']=$mydata['ma_tk'];
$mybasketarray['user']['ma_country_id']=$mydata['ma_country_id'];
$mybasketarray['user']['ma_nomos_id']=$mydata['ma_nomos_id'];
$mybasketarray['user']['eponimia']=$mydata['eponimia'];
$mybasketarray['user']['title']=$mydata['title'];
$mybasketarray['user']['afm']=$mydata['afm'];
$mybasketarray['user']['doy']=$mydata['doy'];
$mybasketarray['user']['epaggelma']=$mydata['epaggelma'];
$mybasketarray['address_extra']=$mydata['address_extra'];
$mybasketarray['destination_data']['name'] = trim_gks($mydata['dd_name']);
$mybasketarray['destination_data']['phone'] = trim_gks($mydata['dd_phone']);
$mybasketarray['destination_data']['odos'] = trim_gks($mydata['dd_odos']);
$mybasketarray['destination_data']['arithmos'] = trim_gks($mydata['dd_arithmos']);
$mybasketarray['destination_data']['orofos'] = trim_gks($mydata['dd_orofos']);
$mybasketarray['destination_data']['perioxi'] = trim_gks($mydata['dd_perioxi']);
$mybasketarray['destination_data']['poli'] =  trim_gks($mydata['dd_poli']);
$mybasketarray['destination_data']['tk'] = trim_gks($mydata['dd_tk']);
$mybasketarray['destination_data']['country_id'] = intval($mydata['dd_country_id']);
$mybasketarray['destination_data']['nomos_id'] = intval($mydata['dd_nomos_id']);
if ($mybasketarray['destination_data']['country_id']==0) $mybasketarray['destination_data']['country_id']=91;



//$mybasketarray['user']['ma_country_id']=91;
$mybasketarray['fiscal_position']=intval($mydata['fiscal_position_id']);
if ($mybasketarray['fiscal_position']<1) $mybasketarray['fiscal_position']=1;

$mybasketarray['pricelist_id']=intval($mydata['pricelist_id']);
if ($mybasketarray['pricelist_id']<1) $mybasketarray['pricelist_id']=1;
$mybasketarray['coupons']=array();

$mybasketarray['parastatiko']=intval($mydata['need_afm']);


if ($id>0) {
  
  
  $sql=select_gks_whi_mov()." where gks_whi_mov.id_whi_mov = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row = $result->fetch_assoc();
  
  $credit_memo_for_whi_mov_id=$row['credit_memo_for_whi_mov_id'];
  if ($credit_memo_for_whi_mov_id!=0) {
    $mybasketarray['company_id']= $row['company_id'];
    $mybasketarray['company_sub_id']= $row['company_sub_id'];
    
    $mybasketarray['user']['user_id']=$row['user_id'];
    $mybasketarray['user']['afm']=$row['afm'];
    $mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
    
    
    
    $sql=select_gks_whi_mov()." where gks_whi_mov.id_whi_mov = ".$credit_memo_for_whi_mov_id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result->num_rows!=1) {
      debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();}
    $row = $result->fetch_assoc();
    $mybasketarray['mov_whi_journal_id']=intval($row['mov_whi_journal_id']);
    $mybasketarray['mov_whi_seira_id']=intval($row['mov_whi_seira_id']);
    $mybasketarray['parastatiko']= $row['eidos_parastatikou_need_afm'];
    
  }
}











  
$mybasketarray['products_need_apostoli'] = intval($mydata['gks_products_need_apostoli'])!=0;
$mybasketarray['products_varos']= intval($mydata['gks_products_varos']);
$mybasketarray['products_ogos']= intval($mydata['gks_products_ogos']);
$mybasketarray['products_ogos_max_x']= intval($mydata['gks_products_ogos_x']);
$mybasketarray['products_ogos_max_y']= intval($mydata['gks_products_ogos_y']);
$mybasketarray['products_ogos_max_z']= intval($mydata['gks_products_ogos_z']);
$mybasketarray['products_need_pliromi']=false;




//echo '<pre>';
//print $mybasketarray['destination_data']['country_id'];
//die();

$mybasketarray['tropos_apostolis'] = intval($mydata['tropos_apostolis']);
$mybasketarray['tropos_pliromis'] = 0;
$mybasketarray['products_total'] = 0;



$basket_products_temp =array();
foreach ($eidi_array as &$value) {
  $user_field_change='';



  $hotel_check_out_round  = showDate(time(),'Y-d-m',1);
  $hotel_check_in_round = showDate(time()-$value['product_quantity']*24*60*60,'Y-d-m',1);
  if ($mybasketarray['mov_date']!='') {
    $hotel_check_out_round  = date('Y-d-m',strtotime($mybasketarray['mov_date']));
    $hotel_check_in_round = date('Y-d-m',strtotime($mybasketarray['mov_date'])-$value['product_quantity']*24*60*60);
  }

 
  //print '<pre>';print_r($value);die();
  
  $objects=array();
  $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $value['product_quantity'],  'files' => array(), 'warnings'=>array());
  $basket_products_temp[$value['aa']]=array(
    'product_id'=>array(
      'id_product'=>$value['product_id'], 
      'product_monada_id' => $value['product_monada_id'], 
      'product_fpa_base_id' => 0, 
      'product_sheets'=>0, 
      'product_set' => '',
     ), 
    'objects'=>$objects,
    'user_ekptosi' => 0,
    'user_final_net' => 0,
    'user_change_ekptosi_or_final_net' => '',
    'user_field_change' => $user_field_change,
    
    'other_taxes' => array(
      'withheldPercentCategory' => 0,  
      'withheldAmount' => 0,  
      'otherTaxesPercentCategory' => 0,  
      'otherTaxesAmount' => 0,  
      'stampDutyPercentCategory' => 0,  
      'stampDutyAmount' => 0, 
      'feesPercentCategory' => 0,  
      'feesAmount' => 0,  
      'deductionsAmount' => 0,  
    ),

    
  );
}
unset($value);
//print '<pre>';
//print_r($basket_products_temp);
//die();  


$mybasketarray['products'] = $basket_products_temp;
$myproducts = gks_basket_recalc($mybasketarray, array(), array());


//$tropos_apostolis=intval($mydata['tropos_apostolis']);
//$tropos_pliromis=intval($mydata['tropos_pliromis']);


$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);


$kostos_apostolis_mode=''; if (isset($mydata['kostos_apostolis_mode'])) $kostos_apostolis_mode=trim_gks($mydata['kostos_apostolis_mode']);


if ($kostos_apostolis_mode=='manual') $mybasketarray['kostos_apostolis']=$mydata['kostos_apostolis'];





$products_ogos='';
if ($mybasketarray['products_ogos_max_x']>0 or $mybasketarray['products_ogos_max_y']>0 or $mybasketarray['products_ogos_max_z']>0) {
  $products_ogos = number_format($mybasketarray['products_ogos_max_x'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                   number_format($mybasketarray['products_ogos_max_y'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                   number_format($mybasketarray['products_ogos_max_z'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND);
  
}
$products_varos='';
if ($mybasketarray['products_varos']>0) $products_varos=number_format($mybasketarray['products_varos'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).' gr';

$eidi=array();
foreach ($mybasketarray['products'] as $aa => $value) {
  
  



  
  
  $eidi[] = array(
    'aa' => $aa,
    'varos' => intval($value['product_id']['product_varos']),
    'ogos_x' => intval($value['product_id']['product_ogos_x']),
    'ogos_y' => intval($value['product_id']['product_ogos_y']),
    'ogos_z' => intval($value['product_id']['product_ogos_z']),
    'need_apostoli' => intval($value['product_id']['product_need_apostoli']),
  );
} 


gks_CheckAFM_Live($mybasketarray);




$cache_file='whi_mov_'.$id.'_'.date('Y-m-d_H-i-s').'_'.rand(10000,99999).'.json';
file_put_contents(GKS_CACHE.$cache_file,json_encode($mybasketarray));
if (GKS_DEBUG) {
  //file_put_contents(GKS_CACHE.'basket.txt',print_r($mybasketarray,true));
  //file_put_contents(GKS_CACHE.$cache_file.'.txt',print_r($mybasketarray,true));
  
}

//print '<pre>';print_r($eidi);die();

$return = array('success' => true, 'message' => base64_encode('OK'),
  //'eidi_array' => $eidi_array,
  'eidi' => $eidi,

  'products_posotita' => myNumberFormatNo0Local($mybasketarray['products_posotita']),
  'products_posotita_val'    => $mybasketarray['products_posotita'],
  'products_ogos' => ($products_ogos),
  'products_ogos_val_x' => $mybasketarray['products_ogos_max_x'],
  'products_ogos_val_y' => $mybasketarray['products_ogos_max_y'],
  'products_ogos_val_z' => $mybasketarray['products_ogos_max_z'],
  'products_varos' => ($products_varos),
  'products_varos_val' => $mybasketarray['products_varos'],
  'kostos_apostolis'  => (myCurrencyFormat($mybasketarray['kostos_apostolis'],true,true)),
  'kostos_apostolis_val' => number_format($mybasketarray['kostos_apostolis'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', ''),

  
  'tropoi_apostolis_all' => $mybasketarray['tropoi_apostolis_all'],
  'products_need_apostoli' => $mybasketarray['products_need_apostoli'],
  'check_vies' => $mybasketarray['check_vies'],
  //'views_run_img' => $mybasketarray['check_vies']['views_run_img'],
  'cache_file' =>$cache_file,
);
//echo '<pre>';

//$ggg=floatval(1.93);
//var_dump($ggg);
//echo json_encode($ggg);
//die();
//$eidi=array();
//$eidi[]=array('aa'=>1,'fffffffffff' => floatval(1.93));
//echo json_encode($eidi);die();
//echo serialize($eidi);
//die();

//var_dump($eidi);



//echo json_encode($eidi[0]);

//die();
echo json_encode($return); die();

