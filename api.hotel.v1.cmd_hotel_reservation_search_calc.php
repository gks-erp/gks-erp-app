<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_hotel_cmd_hotel_reservation_search_calc($id_hotel,$row_hotel,$input_data) {
  global $db_link;
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
  //print '<pre>|'.$gks_erp_cookie_id.'|';
  $hotel_title=$row_hotel['hotel_title'];
  $gkIP = (isset($_SERVER['REMOTE_ADDR'])) ? long2ip(ip2long($_SERVER['REMOTE_ADDR'])) : '';
  gks_erp_cookie_start($gks_erp_cookie_id);
  if (isset($input_data['shortcode_attributes']['lang']) and trim_gks($input_data['shortcode_attributes']['lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['shortcode_attributes']['lang']);
  }
  if (isset($input_data['post']['ui_lang']) and trim_gks($input_data['post']['ui_lang'])!='') {
    $_gks_session['gks']['ui_lang']=gks_erp_supperted_lang($input_data['post']['ui_lang']);
  }
  $gks_user_settings['lang']['backend']=$_gks_session['gks']['ui_lang'];
  gks_load_lang();
  
  $defs = get_def_check($id_hotel);
  $hotel_params=gks_hotel_get_params($id_hotel);
  
  
  $_POST=$input_data['post'];


  
    


$check_in=''; if (isset($_POST['check_in'])) $check_in=trim_gks($_POST['check_in']);
$check_out=''; if (isset($_POST['check_out'])) $check_out=trim_gks($_POST['check_out']);
if ($check_in=='') {
  debug_mail(false,'check_in',$check_in);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει ορισθεί η ημερομηνία άφιξης').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}
if ($check_out=='') {
  debug_mail(false,'check_out',$check_out);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει ορισθεί η ημερομηνία αναχώρησης').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}


//$days_round=hotel_round_days($id_hotel, $gks_check_in, $gks_check_out);

$room_type_id=0; if (isset($_POST['room_type_id'])) $room_type_id=intval($_POST['room_type_id']);
if ($room_type_id=='') {
  debug_mail(false,'room_type_id',$room_type_id);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα').': '.gks_lang('Δεν έχει ορισθεί ο τύπος δωματίου').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
  return $return;}

$num_adults=0;  if (isset($_POST['num_adults'])) $num_adults=intval($_POST['num_adults']); 
$num_childs=0;  if (isset($_POST['num_adults'])) $num_childs=intval($_POST['num_childs']); 

$childs_ages_list = array();
if (isset($_POST['childs_ages_list_str'])) {
  $childs_ages_list_s = trim_gks(base64_decode($_POST['childs_ages_list_str']));
  if ($childs_ages_list_s != '') {
    $childs_ages_list_s=trim_gks(base64_decode($_POST['childs_ages_list_str']));
    $childs_ages_list = json_decode($childs_ages_list_s, true);
  }
}
//print '<pre>';print_r($_POST);die();

$rnum_child_kounies=0;  if (isset($_POST['rnum_child_kounies'])) $rnum_child_kounies=intval($_POST['rnum_child_kounies']); 
$rnum_extra_beds=0;  if (isset($_POST['rnum_extra_beds'])) $rnum_extra_beds=intval($_POST['rnum_extra_beds']); 


$get_availability_rooms_imput=array(
  'id_hotel' => $id_hotel,
  'date_from' => $check_in,
  'date_to' => $check_out,
  'alldata' => false,
  'id_hotel_room' => 0,
  'id_hotel_room_type' => $room_type_id,
  'not_id_hotel_reservation' => 0,
  'not_id_hotel_folio' => 0,
  'not_id_hotel_room' => [],
  'rnum_adults' => $num_adults,
  'rnum_childs' => count($childs_ages_list),
  'rchilds_ages_list' => $childs_ages_list, 
  'rnum_child_kounies' => $rnum_child_kounies,
  'rnum_extra_beds' => $rnum_extra_beds,
  'come_from' => 'online',
);
$rooms_array = get_availability_rooms($get_availability_rooms_imput);

if ($rooms_array['error_msg']!='') {
  $return = array('success' => false, 'message' => base64_encode($rooms_array['error_msg']));
  return $return;
}
if (isset($rooms_array['rooms'])==false or count($rooms_array['rooms'])==0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το δωμάτιο')));
  return $return;  
}

//
//print '<pre>';print_r($rooms_array);die();

$price=0;
foreach ($rooms_array['rooms'] as $value) {
  //print '<pre>';print_r($value);die();
  $price = $value['room_ajia_table']['ajia_total_out'];
  break;
} 
$return = array('success' => true, 'message' => base64_encode('OK'),
  'price' => $price,
  'price_html' =>myCurrencyFormat($price),

);

return $return;
  
print '<pre>';print_r($rooms_array);die();





















$postype=''; if (isset($_POST['postype'])) $postype=trim_gks($_POST['postype']);

$mycmd='';if (isset($_POST['mycmd'])) $mycmd=trim_gks($_POST['mycmd']);
$myfile='';if (isset($_POST['myfile'])) $myfile=trim_gks($_POST['myfile']);

$cmd_is_for_coupon=false;
if ($mycmd=='couponadd' or $mycmd=='coupondelete') {
  $cmd_is_for_coupon=true;
  $mycoupon=$myfile;
}
 



//echo '<pre>'.$check_in.'|'.$check_out;print_r($_POST);print_r($days_round);die();

//$num_days = $days_round['num_days'];



//$sxolio=''; //if (isset($_POST['sxolio'])) $sxolio=trim_gks(base64_decode($_POST['sxolio']));
//$fiscal_position_id=0; //if (isset($_POST['fiscal_position_id'])) $fiscal_position_id=intval($_POST['fiscal_position_id']);  
//$pricelist_id=0; //if (isset($_POST['pricelist_id'])) $pricelist_id=intval($_POST['pricelist_id']);  
//$def_ekptosi=0;  //if (isset($_POST['def_ekptosi']))  $def_ekptosi=floatval($_POST['def_ekptosi']);
//$user_id=0; if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
//$dr_user_first_name=''; if (isset($_POST['dr_user_first_name'])) $dr_user_first_name=trim_gks(base64_decode($_POST['dr_user_first_name']));
//$dr_user_last_name=''; if (isset($_POST['dr_user_last_name'])) $dr_user_last_name=trim_gks(base64_decode($_POST['dr_user_last_name']));
//$dr_user_email=''; if (isset($_POST['dr_user_email'])) $dr_user_email=trim_gks(base64_decode($_POST['dr_user_email']));
//$dr_user_mobile=''; if (isset($_POST['dr_user_mobile'])) $dr_user_mobile=trim_gks(base64_decode($_POST['dr_user_mobile']));
//$dr_user_lang=''; if (isset($_POST['dr_user_lang'])) $dr_user_lang=trim_gks(base64_decode($_POST['dr_user_lang']));
//$dr_user_ma_odos=''; if (isset($_POST['dr_user_ma_odos'])) $dr_user_ma_odos=trim_gks(base64_decode($_POST['dr_user_ma_odos']));
//$dr_user_ma_perioxi=''; if (isset($_POST['dr_user_ma_perioxi'])) $dr_user_ma_perioxi=trim_gks(base64_decode($_POST['dr_user_ma_perioxi']));
//$dr_user_ma_poli=''; if (isset($_POST['dr_user_ma_poli'])) $dr_user_ma_poli=trim_gks(base64_decode($_POST['dr_user_ma_poli']));
//$dr_user_ma_tk=''; if (isset($_POST['dr_user_ma_tk'])) $dr_user_ma_tk=trim_gks(base64_decode($_POST['dr_user_ma_tk']));
//$dr_user_ma_country_id=0; if (isset($_POST['dr_user_ma_country_id'])) $dr_user_ma_country_id=intval($_POST['dr_user_ma_country_id']);
//$dr_user_ma_nomos_id=0; if (isset($_POST['dr_user_ma_nomos_id'])) $dr_user_ma_nomos_id=intval($_POST['dr_user_ma_nomos_id']);
//$form_parastatiko=0; if (isset($_POST['form_parastatiko'])) $form_parastatiko=intval($_POST['form_parastatiko']);
//$tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);
//if ($form_parastatiko == 0) {
//  $dr_user_eponimia=''; 
//  $dr_user_title=''; 
//  $dr_user_afm=''; 
//  $dr_user_doy=''; 
//  $dr_user_epaggelma='';
//} else {
//  $dr_user_eponimia=''; if (isset($_POST['dr_user_eponimia'])) $dr_user_eponimia=trim_gks(base64_decode($_POST['dr_user_eponimia']));
//  $dr_user_title=''; if (isset($_POST['dr_user_title'])) $dr_user_title=trim_gks(base64_decode($_POST['dr_user_title']));
//  $dr_user_afm=''; if (isset($_POST['dr_user_afm'])) $dr_user_afm=trim_gks(base64_decode($_POST['dr_user_afm']));
//  $dr_user_doy=''; if (isset($_POST['dr_user_doy'])) $dr_user_doy=trim_gks(base64_decode($_POST['dr_user_doy']));
//  $dr_user_epaggelma=''; if (isset($_POST['dr_user_epaggelma'])) $dr_user_epaggelma=trim_gks(base64_decode($_POST['dr_user_epaggelma']));
//}
//
//
//$tropos_pliromis=0; if (isset($_POST['tropos_pliromis'])) $tropos_pliromis=intval($_POST['tropos_pliromis']);
//$kostos_pliromis=0;  if (isset($_POST['kostos_pliromis']))  $kostos_pliromis=floatval($_POST['kostos_pliromis']);
//$kostos_pliromis_mode='';  if (isset($_POST['kostos_pliromis_mode']))  $kostos_pliromis_mode= trim_gks($_POST['kostos_pliromis_mode']);
//
//
//$coupons = array();
//if (isset($_POST['coupons_str'])) {
//  $coupons_s = trim_gks(base64_decode($_POST['coupons_str']));
//  if ($coupons_s != '') {
//    $coupons_s=trim_gks(base64_decode($_POST['coupons_str']));
//    $coupons = json_decode($coupons_s, true);
//  }
//}


//print '<pre>';
//print_r($_POST['coupons_str']);
//print_r($coupons);
//die();

$childs_ages_list = array();
if (isset($_POST['childs_ages_list_str'])) {
  $childs_ages_list_s = trim_gks(base64_decode($_POST['childs_ages_list_str']));
  if ($childs_ages_list_s != '') {
    $childs_ages_list_s=trim_gks(base64_decode($_POST['childs_ages_list_str']));
    $childs_ages_list = json_decode($childs_ages_list_s, true);
  }
}
//print '<pre>'; print_r($childs_ages_list);die();



$objects=array();
$objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 
'copies' => 1, //$days_round['num_days'], 
'files' => array(), 'warnings'=>array());

$basket_products_temp=array();
$basket_products_temp[1]=array(
  'is_hotel_room_type' => true,
  'product_id'=>array(
    'id_product'=>10072, //$room_product[$value['hotel_room_id']]['product_id'], 
    'product_monada_id' => 100,
    'product_fpa_base_id' => 1002, //$room_product[$value['hotel_room_id']]['product_fpa_base_id'], 
    'product_sheets'=>0, 
    'product_set' => '',
  ), 
  'objects'=>$objects,
  'user_ekptosi' => 0,
  'user_final_net' => 0 ,
  'user_final_total' => 0 ,
  'user_change_ekptosi_or_final_net' => 'gks_ekptosi',
  'user_field_change' =>  'gks_ekptosi', //gks_ekptosi  or gks_price or gks_quantity
  
  
  'id_hotel'=> $id_hotel,
  'user_check_in'=> $days_round['check_in_round'],
  'user_check_out'=> $days_round['check_out_round'],
  'user_room_id' => 0,
  'user_rnum_adults' => $num_adults,
  'user_rnum_childs' => $num_childs,
  'user_rchilds_ages_list' => json_encode($childs_ages_list),
  'user_rnum_child_kounies' => 0, //$value['rnum_child_kounies'],
  'user_rnum_extra_beds' => 0, //$value['rnum_extra_beds'],


  'other_taxes' => array(
    'withheldPercentCategory' => 0,// intval($value['product_withheldPercentCategory']),  
    'withheldAmount' => 0,//floatval($value['product_withheldAmount']),  
    'otherTaxesPercentCategory' => 0,//intval($value['product_otherTaxesPercentCategory']),  
    'otherTaxesAmount' => 0,//floatval($value['product_otherTaxesAmount']),  
    'stampDutyPercentCategory' => 0,//intval($value['product_stampDutyPercentCategory']),  
    'stampDutyAmount' => 0,//floatval($value['product_stampDutyAmount']), 
    'feesPercentCategory' => 0,//intval($value['product_feesPercentCategory']),  
    'feesAmount' => 0,//floatval($value['product_feesAmount']),  
    'deductionsAmount' => 0,//floatval($value['product_deductionsAmount']),  
  ),
  
);  
  
unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);

$mybasketarray['from']='reservation';
$mybasketarray['id_object'] = 0;  
$mybasketarray['products'] = $basket_products_temp;
$myproducts = gks_basket_recalc($mybasketarray, array(), array());


$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);
$mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);

$pliroteo = $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'] + $mybasketarray['kostos_pliromis'];  
  
$return = array('success' => false, 'message' => base64_encode('edo eisai'));
//return $return;



$roolist = array();
if (isset($_POST['roolist'])) {
  $roolist_s = trim_gks($_POST['roolist']);
  if ($roolist_s != '') {
    $roolist_s=trim_gks(stripslashes(urldecode($_POST['roolist'])));
    $roolist = json_decode($roolist_s, true);
  }
}

$coupons_array=array();
foreach ($roolist as &$myroom) {
  $myroom['aa'] = intval($myroom['aa']);
  $myroom['add'] = intval($myroom['add']);
  $myroom['edit'] = intval($myroom['edit']);
  $myroom['delete'] = intval($myroom['delete']);
  $myroom['recid'] = intval($myroom['recid']);
  $myroom['hotel_room_id'] = intval($myroom['hotel_room_id']);
  $myroom['room_descr'] = trim_gks($myroom['room_descr']);
  $myroom['rnum_adults'] = intval($myroom['rnum_adults']);
  $myroom['rnum_childs'] = intval($myroom['rnum_childs']);
  $myroom['rchilds_ages_list'] = json_encode($myroom['rchilds_ages_list']);
  $myroom['ajia_total'] = floatval($myroom['ajia_total']);
  $myroom['gks_ekptosi_pososto'] = floatval($myroom['gks_ekptosi_pososto']);
  $myroom['rsxolio'] = trim_gks($myroom['rsxolio']);
  $myroom['ruser_id'] = intval($myroom['ruser_id']);
  if ($myroom['ruser_id']<=-1) {
    $myroom['ruser_lang'] = '';
    $myroom['ruser_first_name'] = '';
    $myroom['ruser_last_name'] = '';
    $myroom['ruser_email'] = '';
    $myroom['ruser_mobile'] = '';
    $myroom['ruser_ma_odos'] = '';
    $myroom['ruser_ma_orofos'] = '';
    $myroom['ruser_ma_perioxi'] = '';
    $myroom['ruser_ma_poli'] = '';
    $myroom['ruser_ma_tk'] = '';
    $myroom['ruser_ma_country_id'] = 0;
    $myroom['ruser_ma_nomos_id'] = 0;
    $myroom['ruser_fiscal_position_id'] = 0;
    $myroom['ruser_pricelist_id'] = 0;
  } else {
    $myroom['ruser_lang'] = trim_gks($myroom['ruser_lang']);
    $myroom['ruser_first_name'] = trim_gks($myroom['ruser_first_name']);
    $myroom['ruser_last_name'] = trim_gks($myroom['ruser_last_name']);
    $myroom['ruser_email'] = trim_gks($myroom['ruser_email']);
    $myroom['ruser_mobile'] = trim_gks($myroom['ruser_mobile']);
    $myroom['ruser_ma_odos'] = trim_gks($myroom['ruser_ma_odos']);
    $myroom['ruser_ma_orofos'] = trim_gks($myroom['ruser_ma_orofos']);
    $myroom['ruser_ma_perioxi'] = trim_gks($myroom['ruser_ma_perioxi']);
    $myroom['ruser_ma_poli'] = trim_gks($myroom['ruser_ma_poli']);
    $myroom['ruser_ma_tk'] = trim_gks($myroom['ruser_ma_tk']);
    $myroom['ruser_ma_country_id'] = intval($myroom['ruser_ma_country_id']);
    $myroom['ruser_ma_nomos_id'] = intval($myroom['ruser_ma_nomos_id']);
    $myroom['ruser_fiscal_position_id'] = intval($myroom['ruser_fiscal_position_id']);
    $myroom['ruser_pricelist_id'] = intval($myroom['ruser_pricelist_id']);
  }
  
  
  if ($postype == '' and $myroom['delete']==0) {
    $myroom['pdata']['product_id'] =                           intval($myroom['pdata']['product_id']);
    $myroom['pdata']['product_fpa_base_id'] =                  intval($myroom['pdata']['product_fpa_base_id']);                   
    $myroom['pdata']['product_fpa_id'] =                       intval($myroom['pdata']['product_fpa_id']);                   
    $myroom['pdata']['product_fpa_pososto'] =                  floatval($myroom['pdata']['product_fpa_pososto']);                  
    $myroom['pdata']['product_fpa_id_json'] =                  json_decode(base64_decode($myroom['pdata']['product_fpa_id_json']), true);               
    $myroom['pdata']['product_price_include_vat'] =            intval($myroom['pdata']['product_price_include_vat']);            
    $myroom['pdata']['product_price_start_peritem_db'] =       floatval($myroom['pdata']['product_price_start_peritem_db']);       
    $myroom['pdata']['product_price_start_peritem_net'] =      floatval($myroom['pdata']['product_price_start_peritem_net']);      
    $myroom['pdata']['product_price_start_peritem_fpa'] =      floatval($myroom['pdata']['product_price_start_peritem_fpa']);      
    $myroom['pdata']['product_price_start_peritem_total'] =    floatval($myroom['pdata']['product_price_start_peritem_total']);    
    $myroom['pdata']['product_price_start_all_net'] =          floatval($myroom['pdata']['product_price_start_all_net']);          
    $myroom['pdata']['product_price_start_all_fpa'] =          floatval($myroom['pdata']['product_price_start_all_fpa']);          
    $myroom['pdata']['product_price_start_all_total'] =        floatval($myroom['pdata']['product_price_start_all_total']);    
    $myroom['pdata']['product_price_final_peritem_db'] =       floatval($myroom['pdata']['product_price_final_peritem_db']);   
    $myroom['pdata']['product_price_final_peritem_net'] =      floatval($myroom['pdata']['product_price_final_peritem_net']);  
    $myroom['pdata']['product_price_final_peritem_fpa'] =      floatval($myroom['pdata']['product_price_final_peritem_fpa']);  
    $myroom['pdata']['product_price_final_peritem_total'] =    floatval($myroom['pdata']['product_price_final_peritem_total']);
    $myroom['pdata']['product_price_final_all_net'] =          floatval($myroom['pdata']['product_price_final_all_net']);      
    $myroom['pdata']['product_price_final_all_fpa'] =          floatval($myroom['pdata']['product_price_final_all_fpa']);      
    $myroom['pdata']['product_price_final_all_total'] =        floatval($myroom['pdata']['product_price_final_all_total']);    
    $myroom['pdata']['product_price_ekptosi_net'] =            floatval($myroom['pdata']['product_price_ekptosi_net']);        
    $myroom['pdata']['product_price_ekptosi_pososto'] =        floatval($myroom['pdata']['product_price_ekptosi_pososto']);    
    $myroom['pdata']['product_pricelist_item_id'] =            intval($myroom['pdata']['product_pricelist_item_id']);        
    $myroom['pdata']['product_pricelist_item_percent'] =       floatval($myroom['pdata']['product_pricelist_item_percent']);   
    $myroom['pdata']['product_price_coupon_use'] =             trim_gks($myroom['pdata']['product_price_coupon_use']);         
    $myroom['pdata']['product_price_coupon_use_disabled'] =    intval($myroom['pdata']['product_price_coupon_use_disabled']);
    
    $myroom['pdata']['ajia_table_math'] =                      trim_gks($myroom['pdata']['ajia_table_math']);
    $myroom['pdata']['ajia_table_html'] =                      trim_gks($myroom['pdata']['ajia_table_html']);
    $myroom['pdata']['ajia_table_array']=                      base64_decode($myroom['pdata']['ajia_table_array']); 
        
    if ($myroom['pdata']['product_price_coupon_use']!='' and in_array($myroom['pdata']['product_price_coupon_use'],$coupons_array)==false) {
      $coupons_array[]= $myroom['pdata']['product_price_coupon_use'];
    } 
    

        
  }
  
 
}
unset($myroom);

$coupons_str='';
if (count($coupons_array)>=1) {
  $coupons_str='|' . implode('|',$coupons_array).'|';
}


$roomcheck1=array();
foreach ($roolist as $myroom) {
  if ($myroom['delete']==0) {
    if ($myroom['hotel_room_id'] > 0) {
      $roomcheck1[]=$myroom['hotel_room_id'];
    }
  }
}

//echo $postype;
//die();

  
  
unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);

$mybasketarray['from']='reservation';

$mybasketarray['id_object'] = 0; //$id;
$mybasketarray['user']['user_id']=0; //$my_wp_user_id;
$mybasketarray['user']['first_name']=''; //$dr_user_first_name;
$mybasketarray['user']['last_name']=''; //$dr_user_last_name;
$mybasketarray['user']['email']=''; //$dr_user_email;
$mybasketarray['user']['mobile']=''; //$dr_user_mobile;
$mybasketarray['user']['lang']=''; //$dr_user_lang;
$mybasketarray['user']['ma_odos']=''; //$dr_user_ma_odos;
$mybasketarray['user']['ma_orofos']=''; //$dr_user_ma_orofos;
$mybasketarray['user']['ma_perioxi']=''; //$dr_user_ma_perioxi;
$mybasketarray['user']['ma_poli']=''; //$dr_user_ma_poli;
$mybasketarray['user']['ma_tk']=''; //$dr_user_ma_tk;
$mybasketarray['user']['ma_country_id']=91; //$dr_user_ma_country_id;
$mybasketarray['user']['ma_nomos_id']=0; //$dr_user_ma_nomos_id;
$mybasketarray['user']['eponimia']=''; //$dr_user_eponimia;
$mybasketarray['user']['title']=''; //$dr_user_title;
$mybasketarray['user']['afm']=''; //$dr_user_afm;
$mybasketarray['user']['doy']=''; //$dr_user_doy;
$mybasketarray['user']['epaggelma']=''; //$dr_user_epaggelma;
$mybasketarray['address_extra']=-1;



//$mybasketarray['user']['ma_country_id']=91;
$mybasketarray['fiscal_position']=1; // intval($fiscal_position_id);
if ($mybasketarray['fiscal_position']<1) $mybasketarray['fiscal_position']=1;

$mybasketarray['pricelist_id']=1; //intval($pricelist_id);
if ($mybasketarray['pricelist_id']<1) $mybasketarray['pricelist_id']=1;
$mybasketarray['coupons']=array(); //$coupons;

//$mybasketarray['coupons']['meion20']='Meion 20%';
//$mybasketarray['coupons']['meion21']='Meion 21%';
//$mybasketarray['coupons']['meion22']='Meion 22%';
//$mybasketarray['coupons']['meion23']='Meion 23%';
//$mybasketarray['coupons']['meion24']='Meion 24%';
//$mybasketarray['coupons']['meion25']='Meion 25%';

$mybasketarray['parastatiko']=0; //intval($form_parastatiko);  

$mybasketarray['tropos_apostolis'] = 0;
$mybasketarray['tropos_pliromis'] = 0; //$tropos_pliromis;


if ($postype=='calc') {
  
  if ($cmd_is_for_coupon) { //cmd is for coupon
    
  
    $pricelist_id= $mybasketarray['pricelist_id'];
    if ($pricelist_id <= 0) $pricelist_id = 1;  
  
    //$return = array('success' => false, 'message' => base64_encode($pricelist_id));
    //return $return;  
    
    switch ($mycmd) {
      case 'couponadd':
        $sql="SELECT gks_eshop_pricelist_items.pricelist_item_coupon, gks_eshop_pricelist_items.pricelist_item_descr, 
        gks_eshop_pricelist_items.pricelist_item_date_from, gks_eshop_pricelist_items.pricelist_item_date_to
        FROM gks_eshop_pricelist_items
        WHERE gks_eshop_pricelist_items.pricelist_item_coupon='".$db_link->escape_string($mycoupon)."' 
        AND gks_eshop_pricelist_items.pricelist_id=".$pricelist_id." 
        AND gks_eshop_pricelist_items.pricelist_item_disable=0;";
        //echo '|'.$pricelist_id;
        
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
          return $return; 
        }
        if ($result->num_rows == 0) {
          debug_mail(false,'coupon not found:'.$mycoupon );
          $msg_temp=gks_lang('Δεν βρέθηκε το κουπόνι <b>[1]</b>').'<br>'.
          gks_lang('Βεβαιωθείτε ότι το έχετε γράψει σωστά').'<br>'.
          gks_lang('Βεβαιωθείτε ότι έχετε επιλέξει το σωστό τιμοκατάλογο').' (1)';
          $msg_temp=str_replace('[1]', $mycoupon, $msg_temp);
          $return = array('success' => false, 'message' => base64_encode($msg_temp));
          return $return;
        }  
  
  
        $pricelist_item_coupon='';
        $pricelist_item_descr='';
        while ($row = $result->fetch_assoc()) {  
          $pricelist_item_coupon=$row['pricelist_item_coupon'];
          $pricelist_item_descr=$row['pricelist_item_descr'];
          
          if (isset($row['pricelist_item_date_to'])) {
            if ( ! (time() >= strtotime($row['pricelist_item_date_from']) and time() <= strtotime($row['pricelist_item_date_to']))) {
              if (time() < strtotime($row['pricelist_item_date_from'])) { //den exei energopoiuei akoma
                debug_mail(false,'coupon not start:'.$mycoupon );
                $msg_temp=gks_lang('Δεν βρέθηκε το κουπόνι <b>[1]</b>').'<br>'.
                gks_lang('Βεβαιωθείτε ότι το έχετε γράψει σωστά').'<br>'.
                gks_lang('Βεβαιωθείτε ότι έχετε επιλέξει το σωστό τιμοκατάλογο').' (2)';
                $msg_temp=str_replace('[1]', $mycoupon, $msg_temp);
                $return = array('success' => false, 'message' => base64_encode($msg_temp));
                return $return;          
              }
              debug_mail(false,'coupon expire',$mycoupon.':'. showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i:s', 1));
              $msg_temp=gks_lang('Το κουπόνι <b>[1]</b> έχει λήξει στις<br>[2]');
              $msg_temp=str_replace('[1]', $pricelist_item_coupon, $msg_temp);
              $msg_temp=str_replace('[2]', showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i:s', 1), $msg_temp);
              $return = array('success' => false, 'message' =>  base64_encode($msg_temp));
              return $return;          
            }
          }
        }
        
        if (isset($mybasketarray['coupons'][$pricelist_item_coupon])) {
          debug_mail(false,'coupon has already added:'. $mycoupon);
          $msg_temp=gks_lang('Το κουπόνι <b>[1]</b> το έχετε καταχωρήσει ήδη');
          $msg_temp=str_replace('[1]', $pricelist_item_coupon, $msg_temp);
          
          $return = array('success' => false, 'message' => base64_encode($msg_temp));
          return $return;           
        } else {
          $mybasketarray['coupons'][$pricelist_item_coupon]=$pricelist_item_descr;
          $out[] =array('id' => '#input_coupon','type'=>'val', 'data' => base64_encode(''));
        }
        
        break;
      case 'coupondelete':
        //$return = array('success' => false, 'message' => base64_encode($mycoupon));
        //return $return;
      
        if (isset($mybasketarray['coupons'][$mycoupon])) {
          unset($mybasketarray['coupons'][$mycoupon]);// = array();
        } else {
          debug_mail(false,'try to remove coupon:'. $mycoupon);
        }
        
        break;
      default:
        debug_mail(false,'error on cmd for coupon: '.$mycmd);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
        return $return;
    }
    
    $coupons_html='';
    foreach ($mybasketarray['coupons'] as $key => $coupon) {
       $coupons_html.='<span class="tooltipster_basket" title="'.$coupon.'" style="text-align:left;border: 1px solid gray;border-radius: 4px;padding:8px;margin-right: 6px;">
       <span class="coupons">'.$key.' 
       <i class="coupon_delete gks_fas gks_fa-trash-alt gks_reservation_delete_icon" data-coupon="'.$key.'" style="cursor:pointer;"></i>
       </span></span> ';
    } 
    if ($coupons_html!='') {
      $coupons_html=gks_lang('Τα κουπόνια').': '.$coupons_html;
    }
    $out[] =array('id' => '#coupons_html','type'=>'html', 'data' => base64_encode($coupons_html));  
    
  }  
  
  
  
  $fields_change=''; //trim_gks(base64_decode($_POST['fields_change']));
  $fields_change = array(); //json_decode($fields_change, true);
  $fields_change_curr_name=''; //trim_gks($_POST['fields_change_curr_name']);
  $fields_change_curr_aa=-1; //intval($_POST['fields_change_curr_aa']);
  
//  print '|'.$fields_change_curr_name;
//  print '|'.$fields_change_curr_aa;
//  print '|';
//  print_r($fields_change);
//  die();

  $array_rooms_ids=array();
  $room_product=array();
  foreach ($roolist as $myroom) {
    if ($myroom['delete']==0) {
      $array_rooms_ids[]=$myroom['hotel_room_id'];
    }
  }
  if (count($array_rooms_ids)>0) {
  $sql="SELECT gks_hotel_room_type.id_hotel_room_type, gks_hotel_room_type.product_id, gks_hotel_room_type.room_type_descr, 
    gks_hotel_room_type.room_type_visitors, gks_hotel_room_type.room_type_visitors_childs, gks_hotel_room_type.room_type_visitors_max, 
    gks_hotel_room.id_hotel_room
    FROM gks_hotel_room LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type
    WHERE gks_hotel_room_type.id_hotel_room_type Is Not Null
    AND gks_hotel_room_type.product_id>0
    AND gks_hotel_room.id_hotel_room In (".implode(',',$array_rooms_ids).")";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      return $return; }  
    while ($row = $result->fetch_assoc()) {
      $room_product[$row['id_hotel_room']] = $row;
    }   
    
    
    
  }
  
//  print '<pre>';
//  print $_POST['fields_change'];
//  print_r($fields_change);
//  die();
  

  $basket_products_temp =array();
  foreach ($roolist as $value) {
    $aa=$value['aa']; //+1;
    if (isset($room_product[$value['hotel_room_id']]) and $value['delete']==0) {
      
      $user_field_change='';
      if ($aa == $fields_change_curr_aa) $user_field_change=$fields_change_curr_name;
      $user_change_ekptosi_or_final_net='';
      if (isset($fields_change[$aa])) $user_change_ekptosi_or_final_net=$fields_change[$aa];

      //print '['.$user_field_change;
      //print "|";
      //print $user_change_ekptosi_or_final_net;
      //print ']';
      //die();
      //$user_change_ekptosi_or_final_net='gks_price_total';
      //$user_field_change='gks_price_final'; //gks_ekptosi  or gks_price or gks_quantity
      
      $user_ekptosi = floatval($value['gks_ekptosi_pososto']); //floatval($value['product_price_ekptosi_pososto']);  
      $objects=array();
      $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $days_round['num_days'], 'files' => array(), 'warnings'=>array());
      $basket_products_temp[$aa]=array(
        'is_hotel_room_type' => true,
        'product_id'=>array('id_product'=>$room_product[$value['hotel_room_id']]['product_id'], 'product_sheets'=>0, 'product_set' => '','product_monada_id' => 100), 
        'objects'=>$objects,
        'user_ekptosi' => $user_ekptosi,
        'user_final_net' => floatval($value['ajia_total']) ,
        'user_final_total' => floatval($value['ajia_total']) ,
        'user_change_ekptosi_or_final_net' => $user_change_ekptosi_or_final_net,
        'user_field_change' => $user_field_change,
        'user_check_in'=> $days_round['check_in_round'],
        'user_check_out'=> $days_round['check_out_round'],
        'user_room_id' => $value['hotel_room_id'],
        'user_rnum_adults' => $value['rnum_adults'],
        'user_rnum_childs' => $value['rnum_childs'],
        'user_rchilds_ages_list' => $value['rchilds_ages_list'],
      );
      
//      print '<pre>';
//      var_dump($basket_products_temp[$aa]['user_rchilds_ages_list']);
//      print_r($basket_products_temp[$aa]);
//      die();
    }
  }
  
 
  
  $mybasketarray['products'] = $basket_products_temp;
  $myproducts = gks_basket_recalc($mybasketarray, $fields_change, array());
  

  $mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);
  $mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);

  

  

  
  //if ($kostos_pliromis_mode=='manual')  $mybasketarray['kostos_pliromis']= $kostos_pliromis;
  
  $pliroteo = $mybasketarray['products_total'] + $mybasketarray['kostos_pliromis'];//$mybasketarray['kostos_apostolis'] + 
  
  
  $eidi=array();
  
  gks_CheckAFM_Live($mybasketarray);
  
  $coupons_html='';
  foreach ($mybasketarray['coupons'] as $key => $coupon) {
     $coupons_html.='<span class="tooltipster coupons_span" title="'.$coupon.'">
     <span class="coupons text-sm">'.$key.' 
     <i class="coupon_delete fas fa-trash-alt gks_reservation_delete_icon" data-coupon="'.$key.'" style=""></i>
     </span></span> ';
  } 
  if ($coupons_html!='') {
    $coupons_html=gks_lang('Κουπόνια').': '.$coupons_html;
  }
  
  //file_put_contents(GKS_SITE_PATH.'tmp/res.txt', print_r($mybasketarray, true)."\n".print_r($roolist,true)."\n".print_r($days_round,true));
//  file_put_contents(GKS_SITE_PATH.'tmp/bas.txt', print_r($_gks_session['gks']['basket'], true));
  $eidi=array();
  foreach ($mybasketarray['products'] as $aa => $value) {
    $product_price_ekptosi_net=round($value['product_id']['product_price_start_all_net']-$value['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    $product_price_ekptosi_total=round($value['product_id']['product_price_start_all_total']-$value['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    $product_price_ekptosi_pososto=0;
    if ($value['product_id']['product_price_start_all_net']!=0) $product_price_ekptosi_pososto=round($product_price_ekptosi_total*100/$value['product_id']['product_price_start_all_total'],2);
    if (isset($value['user_change_ekptosi_or_final_net']) and $value['user_change_ekptosi_or_final_net']=='gks_ekptosi' and isset($value['user_ekptosi'])) {
      $product_price_ekptosi_pososto=$value['user_ekptosi'];
    }
    
    $ekptosi_poso_html='';
    if ($product_price_ekptosi_total!=0) $ekptosi_poso_html=myCurrencyFormat($product_price_ekptosi_total,false,true);
    

    
    $eidi[] = array(
      'aa' => $aa,
      
      'product_id' => $value['product_id']['id_product'],
      'product_fpa_base_id' => $value['product_id']['product_fpa_base_id'],
      'product_fpa_id' => $value['product_id']['product_fpa_id_array']['id_fpa_to'],
      'product_fpa_pososto' => $value['product_id']['product_fpa_id_array']['fpa_pososto'],
      'product_fpa_id_json' => json_encode($value['product_id']['product_fpa_id_array']),
      'product_price_include_vat' => $value['product_id']['product_price_include_vat'],
      'product_price_start_peritem_db' => round($value['product_id']['product_price_start_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_peritem_net' => round($value['product_id']['product_price_start_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_peritem_fpa' => round($value['product_id']['product_price_start_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_peritem_total' => round($value['product_id']['product_price_start_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_all_net' => round($value['product_id']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_all_fpa' => round($value['product_id']['product_price_start_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_start_all_total' => round($value['product_id']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_peritem_db' => round($value['product_id']['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_peritem_net' => round($value['product_id']['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_peritem_fpa' => round($value['product_id']['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_peritem_total' => round($value['product_id']['product_price_final_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_all_net' => round($value['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_all_fpa' => round($value['product_id']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_final_all_total' => round($value['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL),
      'product_price_ekptosi_net' => $product_price_ekptosi_net,
      'product_price_ekptosi_pososto' => $product_price_ekptosi_pososto,
      'product_pricelist_item_id' => $value['product_id']['product_pricelist_item_id'],
      'product_pricelist_item_percent' => round($value['product_id']['product_pricelist_item_percent'],2),
      'product_price_coupon_use' => $value['product_id']['product_price_coupon_use'],
      'product_price_coupon_use_disabled' => $value['product_id']['product_price_coupon_use_disabled'],
      
      'fpa_descr_print' => $value['product_id']['product_fpa_id_array']['fpa_descr_print'],
      'ekptosi_poso_html' => $ekptosi_poso_html,

      'room_ajia_table' => $value['product_id']['room_ajia_table'],
      
    );
  }   
  
  $return = array('success' => true, 'message' => base64_encode('OK'),
    //'eidi_array' => $eidi_array,
    'eidi' => $eidi,
  
    'products_posotita' => (myNumberFormat($mybasketarray['products_posotita'],0)),
    'products_posotita_val'    => $mybasketarray['products_posotita'],
//    'products_ogos' => ($products_ogos),
//    'products_varos' => ($products_varos),
    'products_netvalue' => (myCurrencyFormat($mybasketarray['products_netvalue'],true,true)),
    'products_fpa'      => (myCurrencyFormat($mybasketarray['products_fpa'],true,true)),
    'products_total' => (myCurrencyFormat($mybasketarray['products_total'],true,true)),
    'kostos_apostolis'  => (myCurrencyFormat($mybasketarray['kostos_apostolis'],true,true)),
    'kostos_apostolis_val' => number_format($mybasketarray['kostos_apostolis'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', ''),
    'kostos_pliromis'   => (myCurrencyFormat($mybasketarray['kostos_pliromis'],true,true)),
    'kostos_pliromis_val' => number_format($mybasketarray['kostos_pliromis'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', ''),
    'pliroteo'    => (myCurrencyFormat($pliroteo ,true,true)),
    'pliroteo_val'    => $pliroteo,
  
    
    'tropoi_apostolis_all' => $mybasketarray['tropoi_apostolis_all'],
    'tropoi_pliromis_all' => $mybasketarray['tropoi_pliromis_all'],
    'products_need_apostoli' => $mybasketarray['products_need_apostoli'],
    'products_need_pliromi' => $mybasketarray['products_need_pliromi'],
    //'products_total' => $mybasketarray['products_total'],
    'check_vies' => $mybasketarray['check_vies'],
    //'views_run_img' => $mybasketarray['check_vies']['views_run_img'],
    'coupons_html' => $coupons_html,
    'coupons_array' => $mybasketarray['coupons'],
    'fields_change' => $fields_change,
  );
  return $return;  
  
  
  
  
}


if ($id > 0) {
  $sql ="SELECT * FROM gks_hotel_reservation where id_hotel_reservation = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    return $return; }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    return $return;  }
  $row = $result->fetch_assoc();
}


if ($reservation_date=='') {
  debug_mail(false,'reservation_date',$reservation_date);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Κράτησης')));
  return $return;}

if ($check_in=='') {
  debug_mail(false,'check_in',$check_in);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Άφιξης')));
  return $return;}

if ($check_out=='') {
  debug_mail(false,'check_out',$check_out);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Αναχώρησης')));
  return $return;}
   
if (strtotime($check_in) > strtotime($check_out)) {
  debug_mail(false,'check_in check_out');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η ημερομηνία άφιξης δεν μπορεί να είναι μεγαλύτερη από την ημερομηνία αναχώρησης')));
  return $return;}

if ($dr_user_email != '' and !filter_var($dr_user_email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,'email is not ok : '.$dr_user_email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  return $return;}


if ($dr_user_ma_country_id == 91 and $dr_user_mobile != '' and (strlen($dr_user_mobile) != 10 or substr($dr_user_mobile,0,2) != '69') ) {
  debug_mail(false,'mobile is not ok : '.$dr_user_mobile);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To κινητό δεν είναι σωστό για την επιλεγμένη χώρα')));
  return $return;} 

//if (count($childs_ages_list) != $num_childs) {
//  debug_mail(false,'emptyl',          'childs_ages_list');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τις ηλικές των παιδιών')));
//  return $return; }


if ($fiscal_position_id<=0) {
  debug_mail(false,'emptyl',          'user_fiscal_position_id can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η φορολογική θέση δεν μπορεί να είναι κενή')));
  return $return; }

if ($pricelist_id<=0) {
  debug_mail(false,'emptyl',                'user_pricelist_id can not be empty');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ο τιμοκατάλογος δεν μπορεί να είναι κενός')));
  return $return; }




//if (count($roomcheck1)<=0)  {
//  debug_mail(false,'hotel_room_id','<pre>'.print_r($roolist,true).'</pre>');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Προσθέστε τουλάχιστον ένα δωμάτιο')));
//  return $return;}  

$roomcheck2=array();
if (count($roomcheck1)>0) {
  $sql="SELECT gks_hotel_room.id_hotel_room, gks_hotel_room.room_descr, 
  gks_hotel_room.room_status, gks_hotel_room_type.room_type_status, 
  gks_hotel_room_type.room_type_visitors, gks_hotel_room_type.room_type_visitors_childs, gks_hotel_room_type.room_type_visitors_max,
  gks_hotel_room_type.room_type_price,
  gks_hotel_room.hotel_room_type_id
  FROM gks_hotel_room 
  LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type
  WHERE gks_hotel_room.id_hotel_room In (".implode(',', $roomcheck1).")";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    return $return; }  
  while ($row = $result->fetch_assoc()) {
    $roomcheck2[$row['id_hotel_room']] = $row;
  }  
}
//$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($roomcheck1,true).'</pre>'));
//return $return;


if ($reservation_status=='080confirm' or $reservation_status=='100completed') {
  $get_availability_rooms_imput=array(
    'date_from' => $days_round['check_in_round'],
    'date_to' => $days_round['check_out_round'],
    'alldata' => false,
    'id_hotel_room' => 0,
    'id_hotel_room_type' => 0,
    'not_id_hotel_reservation' => $id,
    'not_id_hotel_folio' => 0,
    'not_id_hotel_room' => array(),
    'rchilds_ages_list' => array(),
    'rnum_child_kounies' =>0,
    'rnum_extra_beds' =>0,
    'come_from' => 'online',
  );
  $rooms_array = get_availability_rooms($get_availability_rooms_imput);
}

//check rooms
$roomcheck3=array();
$sum_num_adults=0;
$sum_num_childs=0;
$sum_ajia_total=0;
$sum_rooms_plithos=0;

foreach ($roolist as $myroom) {
  if ($myroom['delete']==0) {
    if ($myroom['hotel_room_id']<=0) {
      debug_mail(false,'hotel_room_id','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το δωμάτιο σε όλες τις εγγραφές')));
      return $return;}
    if (isset($roomcheck2[$myroom['hotel_room_id']]) == false) {
      $tmpmsg=gks_lang('Το δωμάτιο <b>[1]</b> δεν βρέθηκε');
      $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);
      $tmpmsg.='<br>'.gks_lang('Ανανεώστε την σελίδα');
      
      debug_mail(false,'hotel_room_id not found','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
      return $return;}
      
    if ($roomcheck2[$myroom['hotel_room_id']]['room_status'] != 'available' or $roomcheck2[$myroom['hotel_room_id']]['room_type_status'] != 'available') {
      $tmpmsg=gks_lang('Το δωμάτιο <b>[1]</b> δεν είναι διαθέσιμο');
      $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);      
      debug_mail(false,'hotel_room_id not available','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
      return $return;}
    
    if (in_array($myroom['hotel_room_id'],$roomcheck3)) {
      $tmpmsg=gks_lang('Το δωμάτιο <b>[1]</b> υπάρχει πάνω από μία φορά στην λίστα');
      $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);      
      debug_mail(false,'hotel_room_id more one','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
      return $return;}
    
    $roomcheck3[] = $myroom['hotel_room_id'];
//    if ($myroom['rnum_adults']<=0) {
//      $tmpmsg=gks_lang('Στο δωμάτιο <b>[1]</b> ορίστε το πλήθος των ενήλικων επισκεπτών');
//      $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);
//      debug_mail(false,'rnum_adults','<pre>'.print_r($myroom,true).'</pre>');
//      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
//      return $return;}      

    if ($myroom['rnum_adults']<=0 and $myroom['rnum_childs']<=0) {
      $tmpmsg=gks_lang('Στο δωμάτιο <b>[1]</b> ορίστε το πλήθος των επισκεπτών');
      $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);      
      debug_mail(false,'rnum_adults and rnum_childs','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
      return $return;}      
    
    
    if ($myroom['rnum_adults']+$myroom['rnum_childs'] > $roomcheck2[$myroom['hotel_room_id']]['room_type_visitors_max']) {
      $tmpmsg=gks_lang('Στο δωμάτιο <b>[1]</b> ορίσατε παραπάνω επισκέπτες από την δυνατότητα του δωματίου: [2]');
      $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);  
      $tmpmsg=str_replace('[2]',($myroom['rnum_adults']+$myroom['rnum_childs']).'/'.$roomcheck2[$myroom['hotel_room_id']]['room_type_visitors'],$tmpmsg);  
      debug_mail(false,'rnum_adults rnum_childs','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
      return $return;}      
    
    if ($myroom['ajia_total'] <0) {
      $tmpmsg=gks_lang('Στο δωμάτιο <b>[1]</b> ορίστε την αξία');
      $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);      
      debug_mail(false,'ajia_total','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
      return $return;}
    
    if ($myroom['ruser_email'] != '' and !filter_var($myroom['ruser_email'], FILTER_VALIDATE_EMAIL)) {
      $tmpmsg=gks_lang('Στο δωμάτιο <b>[1]</b> το email <b>[2]</b> δεν είναι σωστό');
      $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);      
      $tmpmsg=str_replace('[1]',$myroom['ruser_email'],$tmpmsg);      
      debug_mail(false,'ruser_email','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
      return $return;}

    if ($myroom['ruser_ma_country_id'] == 91 and $myroom['ruser_mobile'] != '' and (strlen($myroom['ruser_mobile']) != 10 or substr($myroom['ruser_mobile'],0,2) != '69') ) {
      $tmpmsg=gks_lang('Στο δωμάτιο <b>[1]</b> το κινητό <b>[2]</b> δεν είναι σωστό');
      $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);      
      $tmpmsg=str_replace('[1]',$myroom['ruser_mobile'],$tmpmsg);      
      debug_mail(false,'ruser_mobile','<pre>'.print_r($myroom,true).'</pre>');
      $return = array('success' => false, 'message' => base64_encode($tmpmsg));
      return $return;}
    
    $sum_num_adults+=$myroom['rnum_adults'];
    $sum_num_childs+=$myroom['rnum_childs'];
    $sum_ajia_total+=$myroom['ajia_total'];
    $sum_rooms_plithos++;
    
    if ($reservation_status=='080confirm' or $reservation_status=='100completed') {
      if (!(isset($rooms_array['rooms'][$myroom['hotel_room_id']]['is_avl_state_folio']) and $rooms_array['rooms'][$myroom['hotel_room_id']]['is_avl_state_folio'] == true)) {
        $tmpmsg=gks_lang('Το δωμάτιο <b>[1]</b> δεν είναι διαθέσιμο για αυτές τις ημερομηνίες');
        $tmpmsg=str_replace('[1]',$myroom['room_descr'],$tmpmsg);        
        debug_mail(false,'room is not aval','<pre>'.print_r($myroom,true).'</pre><pre>'.print_r($rooms_array,true).'</pre>');
        $return = array('success' => false, 'message' => base64_encode($tmpmsg));
        return $return;}
    }
      
  }
}

if ($num_adults==0 and $num_childs==0) {
  $num_adults = $sum_num_adults;
  $num_childs = $sum_num_childs;
} else {
  if ($num_adults!=$sum_num_adults) {
        debug_mail(false,'num_adults != sum_num_adults','<pre>'.print_r($roolist,true).'</pre>');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν συμφωνεί το πλήθος των ενηλίκων της κράτησης με το άθροισμα των ενηλίκων από τα δωμάτια')));
        return $return;}
  
  
  if ($num_childs!=$sum_num_childs) {
        debug_mail(false,'num_childs != sum_num_childs','<pre>'.print_r($roolist,true).'</pre>');
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν συμφωνεί το πλήθος των παιδιών της κράτησης με το άθροισμα των παιδιών από τα δωμάτια')));
        return $return;}
}
//$return = array('success' => false, 'message' => base64_encode('φφφφ'));
//return $return;





//write

if ($id <= 0) {
  $reservation_guid=guid_for_reservation();

  $sql="insert into gks_hotel_reservation (
  reservation_guid,reservation_status,
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip
  ) values (
  '".$db_link->escape_string($reservation_guid)."','010draft',
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
 
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    return $return; }
   
  $id = $db_link->insert_id;    
    
}



$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray,-1);
$mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray,-1);

$kostos_pliromis_json='';
if (isset($mybasketarray['tropoi_pliromis_all'][$tropos_pliromis])) {
  $kostos_pliromis_json=json_encode($mybasketarray['tropoi_pliromis_all'][$tropos_pliromis]);
  
}
//$return = array('success' => false, 'message' => base64_encode($id.'<br>'.$check_in.'<br>'.$check_out.'<br>'.$reservation_guid));
//return $return; 


//ajia_total=".number_format($sum_ajia_total, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
$gks_price_original_net=0;
$gks_price_net=0;
$gks_price_fpa=0;
$gks_price_total=0;

foreach ($roolist as $myroom) {
  if ($myroom['delete'] == 0) {
    $gks_price_original_net+=$myroom['pdata']['product_price_start_all_net'];
    $gks_price_net+=$myroom['pdata']['product_price_final_all_net'];
    $gks_price_fpa+=$myroom['pdata']['product_price_final_all_fpa'];
    $gks_price_total+=$myroom['pdata']['product_price_final_all_total'];
    
    
  }
}


$sql="update gks_hotel_reservation set ";
if ($reservation_status!= '') {
  $sql.="reservation_status='".$db_link->escape_string($reservation_status)."', ";
}
$sql.="
reservation_date=".($reservation_date == '' ? 'null' : "'".$db_link->escape_string($reservation_date)."'") .", 
check_in=".($check_in == '' ? 'null' : "'".$db_link->escape_string($check_in)."'") .", 
check_out=".($check_out == '' ? 'null' : "'".$db_link->escape_string($check_out)."'") .", 
num_days=".$num_days.",
num_adults=".$num_adults.",
num_childs=".$num_childs.",
childs_ages_list='".$db_link->escape_string(json_encode($childs_ages_list))."',
sxolio='".$db_link->escape_string($sxolio)."',
rooms_plithos=".$sum_rooms_plithos.",


user_id=".$user_id.",
user_lang='".$db_link->escape_string($dr_user_lang)."',
user_first_name='".$db_link->escape_string($dr_user_first_name)."',
user_last_name='".$db_link->escape_string($dr_user_last_name)."',
user_email='".$db_link->escape_string($dr_user_email)."',
user_mobile='".$db_link->escape_string($dr_user_mobile)."',
ma_odos='".$db_link->escape_string($dr_user_ma_odos)."',
ma_orofos='".$db_link->escape_string($dr_user_ma_orofos)."',
ma_perioxi='".$db_link->escape_string($dr_user_ma_perioxi)."',
ma_poli='".$db_link->escape_string($dr_user_ma_poli)."',
ma_tk='".$db_link->escape_string($dr_user_ma_tk)."',
ma_country_id=".$dr_user_ma_country_id.",
ma_nomos_id=".$dr_user_ma_nomos_id.",
fiscal_position_id=".$fiscal_position_id.",
pricelist_id=".$pricelist_id.",
def_ekptosi=".number_format($def_ekptosi, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
coupons='".$db_link->escape_string($coupons_str)."',
parastatiko=".$form_parastatiko.",
eponimia='".$db_link->escape_string($dr_user_eponimia)."',
title='".$db_link->escape_string($dr_user_title)."',
afm='".$db_link->escape_string($dr_user_afm)."',
doy='".$db_link->escape_string($dr_user_doy)."',
epaggelma='".$db_link->escape_string($dr_user_epaggelma)."',

products_need_pliromi=".($gks_price_total==0 ? '0':'1').",
products_need_pliromi=".($gks_price_total==0 ? '0':'1').",

kostos_apostolis=0,
tropos_apostolis=0,
tropos_apostolis_json=null,

kostos_pliromis=".number_format($kostos_pliromis, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
tropos_pliromis=".$tropos_pliromis.",
kostos_pliromis_json='".$db_link->escape_string($kostos_pliromis_json)."',

gks_price_original_net=".number_format($gks_price_original_net, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
gks_price_net=".number_format($gks_price_net, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
gks_price_fpa=".number_format($gks_price_fpa, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
gks_price_total=".number_format($gks_price_total, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",



user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hotel_reservation = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  return $return; }
  
//if ($postype == '') {
//  print '<pre>';
//  print_r($roolist);
//  die();
//}

$sql="UPDATE gks_hotel_reservation_room 
LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product 
SET gks_hotel_reservation_room.product_descr = gks_eshop_products.product_descr
WHERE gks_eshop_products.product_descr is not null
AND gks_eshop_products.id_product Is Not Null
and gks_hotel_reservation_room.hotel_reservation_id=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  return $return; }

$sql="UPDATE gks_hotel_reservation_room 
LEFT JOIN gks_eshop_pricelist_items ON gks_hotel_reservation_room.product_pricelist_item_id = gks_eshop_pricelist_items.id_pricelist_item 
SET gks_hotel_reservation_room.product_pricelist_item_descr = gks_eshop_pricelist_items.pricelist_item_descr
WHERE gks_eshop_pricelist_items.pricelist_item_descr Is Not Null 
AND gks_eshop_pricelist_items.id_pricelist_item Is Not Null
AND gks_hotel_reservation_room.hotel_reservation_id=".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  return $return; }




$roolist_day=array();
foreach ($roolist as &$myroom) {
  //if ($myroom['add']==1 or $myroom['edit']==1 or $myroom['delete']==1) {
    if ($myroom['delete'] == 1) {
      if ($myroom['recid'] >0) {
        $sql="delete from gks_hotel_reservation_room where id_hotel_reservation_room=".$myroom['recid']." and hotel_reservation_id=".$id." limit 1";
        $result = $db_link->query($sql); 
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          return $return; }          
      }
    } else if ($myroom['add'] == 1) {
      $sql="insert into gks_hotel_reservation_room (
      user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
      hotel_reservation_id,hotel_room_id,rnum_adults,rnum_childs,rchilds_ages_list,
      ruser_id,ruser_lang,ruser_first_name,ruser_last_name,ruser_email,ruser_mobile,
      ruser_ma_odos,ruser_ma_orofos,ruser_ma_perioxi,ruser_ma_poli,ruser_ma_tk,ruser_ma_country_id,ruser_ma_nomos_id,rsxolio,
      ruser_fiscal_position_id,ruser_pricelist_id,
      product_id,
      product_fpa_base_id,
      product_fpa_id,
      product_fpa_pososto,
      product_fpa_id_json,
      product_price_include_vat,
      product_price_start_peritem_db,
      product_price_start_peritem_net,
      product_price_start_peritem_fpa,
      product_price_start_peritem_total,
      product_price_start_all_net,
      product_price_start_all_fpa,
      product_price_start_all_total,
      product_price_final_peritem_db,
      product_price_final_peritem_net,
      product_price_final_peritem_fpa,
      product_price_final_peritem_total,
      product_price_final_all_net,
      product_price_final_all_fpa,
      product_price_final_all_total,
      product_price_ekptosi_net,
      product_price_ekptosi_pososto,
      product_pricelist_item_id,
      product_pricelist_item_percent,
      product_price_coupon_use,
      product_price_coupon_use_disabled,
      
      product_quantity,
      
      room_ajia_table_math,
      room_ajia_table_html,
      room_ajia_table_array
      
      
      ) values (
      ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
      ".$id.",".$myroom['hotel_room_id'].",".$myroom['rnum_adults'].",".$myroom['rnum_childs'].",
      '".$db_link->escape_string($myroom['rchilds_ages_list'])."',
      ".$myroom['ruser_id'].",
      '".$db_link->escape_string($myroom['ruser_lang'])."',
      '".$db_link->escape_string($myroom['ruser_first_name'])."',
      '".$db_link->escape_string($myroom['ruser_last_name'])."',
      '".$db_link->escape_string($myroom['ruser_email'])."',
      '".$db_link->escape_string($myroom['ruser_mobile'])."',
      '".$db_link->escape_string($myroom['ruser_ma_odos'])."',
      '".$db_link->escape_string($myroom['ruser_ma_orofos'])."',
      '".$db_link->escape_string($myroom['ruser_ma_perioxi'])."',
      '".$db_link->escape_string($myroom['ruser_ma_poli'])."',
      '".$db_link->escape_string($myroom['ruser_ma_tk'])."',
      ".$myroom['ruser_ma_country_id'].",
      ".$myroom['ruser_ma_nomos_id'].",
      '".$db_link->escape_string($myroom['rsxolio'])."',
      ".$myroom['ruser_fiscal_position_id'].",
      ".$myroom['ruser_pricelist_id'].",
      
      ".$myroom['pdata']['product_id'].",
      ".$myroom['pdata']['product_fpa_base_id'].",
      ".$myroom['pdata']['product_fpa_id'].",
      ".number_format($myroom['pdata']['product_id'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      '".$db_link->escape_string(json_encode($myroom['pdata']['product_fpa_id_json']))."',
      ".$myroom['pdata']['product_price_include_vat'].",
      ".number_format($myroom['pdata']['product_price_start_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_ekptosi_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".number_format($myroom['pdata']['product_price_ekptosi_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      ".$myroom['pdata']['product_pricelist_item_id'].",
      ".number_format($myroom['pdata']['product_pricelist_item_percent'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      '".$db_link->escape_string($myroom['pdata']['product_price_coupon_use'])."',
      ".$myroom['pdata']['product_price_coupon_use_disabled'].",
      
      ".$num_days.",
      '".$db_link->escape_string($myroom['pdata']['ajia_table_math'])."',
      '".$db_link->escape_string($myroom['pdata']['ajia_table_html'])."',
      '".$db_link->escape_string($myroom['pdata']['ajia_table_array'])."'
      
      
      )";
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        return $return; }          
      
      $myroom['recid'] = $db_link->insert_id;    
      
    } else if ($myroom['recid']>0) { //($myroom['edit'] == 1) {
      $sql="update gks_hotel_reservation_room set 
      user_id_edit=".$my_wp_user_id.",
      mydate_edit=now(),
      myip='".$db_link->escape_string($gkIP)."',
      hotel_room_id=".$myroom['hotel_room_id'].",
      rnum_adults=".$myroom['rnum_adults'].",
      rnum_childs=".$myroom['rnum_childs'].",
      rchilds_ages_list='".$db_link->escape_string($myroom['rchilds_ages_list'])."',
      
      ruser_id=".$myroom['ruser_id'].",
      ruser_lang='".$db_link->escape_string($myroom['ruser_lang'])."',
      ruser_first_name='".$db_link->escape_string($myroom['ruser_first_name'])."',
      ruser_last_name='".$db_link->escape_string($myroom['ruser_last_name'])."',
      ruser_email='".$db_link->escape_string($myroom['ruser_email'])."',
      ruser_mobile='".$db_link->escape_string($myroom['ruser_mobile'])."',
      ruser_ma_odos='".$db_link->escape_string($myroom['ruser_ma_odos'])."',
      ruser_ma_orofos='".$db_link->escape_string($myroom['ruser_ma_orofos'])."',
      ruser_ma_perioxi='".$db_link->escape_string($myroom['ruser_ma_perioxi'])."',
      ruser_ma_poli='".$db_link->escape_string($myroom['ruser_ma_poli'])."',
      ruser_ma_tk='".$db_link->escape_string($myroom['ruser_ma_tk'])."',
      ruser_ma_country_id=".$myroom['ruser_ma_country_id'].",
      ruser_ma_nomos_id=".$myroom['ruser_ma_nomos_id'].",
      rsxolio='".$db_link->escape_string($myroom['rsxolio'])."',
      ruser_fiscal_position_id=".$myroom['ruser_fiscal_position_id'].",
      ruser_pricelist_id=".$myroom['ruser_pricelist_id'].",
      
      
      product_id=".$myroom['pdata']['product_id'].",
      product_fpa_base_id=".$myroom['pdata']['product_fpa_base_id'].",
      product_fpa_id=".$myroom['pdata']['product_fpa_id'].",
      product_fpa_pososto=".number_format($myroom['pdata']['product_id'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_fpa_id_json='".$db_link->escape_string(json_encode($myroom['pdata']['product_fpa_id_json']))."',
      product_price_include_vat=".$myroom['pdata']['product_price_include_vat'].",
      product_price_start_peritem_db=".number_format($myroom['pdata']['product_price_start_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_peritem_net=".number_format($myroom['pdata']['product_price_start_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_peritem_fpa=".number_format($myroom['pdata']['product_price_start_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_peritem_total=".number_format($myroom['pdata']['product_price_start_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_all_net=".number_format($myroom['pdata']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_all_fpa=".number_format($myroom['pdata']['product_price_start_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_start_all_total=".number_format($myroom['pdata']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_peritem_db=".number_format($myroom['pdata']['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_peritem_net=".number_format($myroom['pdata']['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_peritem_fpa=".number_format($myroom['pdata']['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_peritem_total=".number_format($myroom['pdata']['product_price_final_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_all_net=".number_format($myroom['pdata']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_all_fpa=".number_format($myroom['pdata']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_final_all_total=".number_format($myroom['pdata']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_ekptosi_net=".number_format($myroom['pdata']['product_price_ekptosi_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_ekptosi_pososto=".number_format($myroom['pdata']['product_price_ekptosi_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_pricelist_item_id=".$myroom['pdata']['product_pricelist_item_id'].",
      product_pricelist_item_percent=".number_format($myroom['pdata']['product_pricelist_item_percent'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
      product_price_coupon_use='".$db_link->escape_string($myroom['pdata']['product_price_coupon_use'])."',
      product_price_coupon_use_disabled=".$myroom['pdata']['product_price_coupon_use_disabled'].",
      
      product_quantity=".$num_days.",

      room_ajia_table_math='".$db_link->escape_string($myroom['pdata']['ajia_table_math'])."',
      room_ajia_table_html='".$db_link->escape_string($myroom['pdata']['ajia_table_html'])."',
      room_ajia_table_array='".$db_link->escape_string($myroom['pdata']['ajia_table_array'])."'
      
      
      where id_hotel_reservation_room=".$myroom['recid']." and hotel_reservation_id=".$id." limit 1";
      //echo 'ddddddd';
      //die();
      $result = $db_link->query($sql); 
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        return $return; }         
      
    
    }
  //}
  $hotel_type_room_id=0;
  if (isset($roomcheck2[$myroom['hotel_room_id']])) {
    $hotel_type_room_id=$roomcheck2[$myroom['hotel_room_id']]['hotel_room_type_id'];
  }
  
  $roolist_day[]=array('delete'=>0, 'hotel_room_id'=> $myroom['hotel_room_id'], 'recid'=> $myroom['recid'], 'hotel_type_room_id'=>$hotel_type_room_id);
}
unset($myroom);


gks_hotel_reservation_room_day_recs($id,$roolist_day,$reservation_status,$days_round['check_in_round_time'],$days_round['check_out_round_time']);


$reloadurl='';
if ($isnewrecord)  {
  $reloadurl='?id='.$id;
}
$return = array('success' => true, 'message' => base64_encode('OK'),'reloadurl' => $reloadurl);



return $return;
  
}
