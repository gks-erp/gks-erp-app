<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
function gks_order_recalc_from_db($id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  
  $return = array('success' => false, 'message' => 'gks_order_recalc_from_db generic error');
  if ($id<=0) {$return['message']='id is not set';
    debug_mail(false,$return['message'],$id);return $return;}
  
  $sql=select_gks_orders($id)." where gks_orders.id_order = ".$id;
  //echo '<pre>'.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {$return['message']='error sql';
    debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows!=1) {$return['message']='record not found sql';
    debug_mail(false,$return['message'],$sql);return $return;}

  $row_order=$result->fetch_assoc();
  
  unset($mybasketarray);
  gks_mybasketarray_create($mybasketarray);
  
  $mybasketarray['from']='order';
  $mybasketarray['id_object'] = $id;
  $mybasketarray['company_id']=intval($row_order['company_id']);
  $mybasketarray['company_sub_id']=intval($row_order['company_sub_id']);
  $mybasketarray['order_journal_id']=intval($row_order['order_journal_id']);
  $mybasketarray['order_seira_id']=intval($row_order['order_seira_id']);
  $mybasketarray['order_state']=trim_gks($row_order['order_state']);
  $mybasketarray['order_date'] = $row_order['order_date'];
  $mybasketarray['online_enable']=intval($row_order['online_enable'])==1;

  $mybasketarray['user']['user_id']=$row_order['user_id'];
  $mybasketarray['user']['first_name']=$row_order['user_first_name'];
  $mybasketarray['user']['last_name']=$row_order['user_last_name'];
  $mybasketarray['user']['email']=$row_order['user_email'];
  $mybasketarray['user']['mobile']=$row_order['user_mobile'];
  $mybasketarray['user']['lang']=$row_order['user_lang'];
  $mybasketarray['user']['ma_odos']=$row_order['ma_odos'];
  $mybasketarray['user']['ma_arithmos']=$row_order['ma_arithmos'];
  $mybasketarray['user']['ma_orofos']=$row_order['ma_orofos'];
  $mybasketarray['user']['ma_perioxi']=$row_order['ma_perioxi'];
  $mybasketarray['user']['ma_poli']=$row_order['ma_poli'];
  $mybasketarray['user']['ma_tk']=$row_order['ma_tk'];
  $mybasketarray['user']['ma_country_id']=$row_order['ma_country_id'];
  $mybasketarray['user']['ma_nomos_id']=$row_order['ma_nomos_id'];
  $mybasketarray['user']['eponimia']=$row_order['eponimia'];
  $mybasketarray['user']['title']=$row_order['title'];
  $mybasketarray['user']['afm']=$row_order['afm'];
  $mybasketarray['user']['doy']=$row_order['doy'];
  $mybasketarray['user']['epaggelma']=$row_order['epaggelma'];
  $mybasketarray['address_extra']=$row_order['address_extra'];
  $mybasketarray['destination_data']['name'] = trim_gks($row_order['destination_data_name']);
  $mybasketarray['destination_data']['phone'] = trim_gks($row_order['destination_data_phone']);
  $mybasketarray['destination_data']['odos'] = trim_gks($row_order['destination_data_odos']);
  $mybasketarray['destination_data']['arithmos'] = trim_gks($row_order['destination_data_arithmos']);
  $mybasketarray['destination_data']['orofos'] = trim_gks($row_order['destination_data_orofos']);
  $mybasketarray['destination_data']['perioxi'] = trim_gks($row_order['destination_data_perioxi']);
  $mybasketarray['destination_data']['poli'] =  trim_gks($row_order['destination_data_poli']);
  $mybasketarray['destination_data']['tk'] = trim_gks($row_order['destination_data_tk']);
  $mybasketarray['destination_data']['country_id'] = intval($row_order['destination_data_country_id']);
  $mybasketarray['destination_data']['nomos_id'] = intval($row_order['destination_data_nomos_id']);
  if ($mybasketarray['destination_data']['country_id']==0) $mybasketarray['destination_data']['country_id']=91;
  
  $mybasketarray['fiscal_position']=intval($row_order['fiscal_position_id']);
  if ($mybasketarray['fiscal_position']<1) $mybasketarray['fiscal_position']=1;
  
  $mybasketarray['pricelist_id']=intval($row_order['pricelist_id']);
  if ($mybasketarray['pricelist_id']<1) $mybasketarray['pricelist_id']=1;

  $mybasketarray['coupons']=array();
  $coupons = trim_gks($row_order['coupons']);
  $coupons_parts=explode('|',$coupons);
  foreach ($coupons_parts as $value) {
    $value=trim_gks($value);
    if ($value!='') {
      $mybasketarray['coupons'][$value]=$value;
      $sql_coupon="SELECT pricelist_item_descr
      FROM gks_eshop_pricelist_items
      WHERE pricelist_item_coupon='".$db_link->escape_string($value)."'
      AND pricelist_id=".$row_order['pricelist_id'];
      $result_coupon = $db_link->query($sql_coupon);        
      if (!$result_coupon) {$return['message']='error sql';
        debug_mail(false,$return['message'],$sql_coupon);return $return;}
      if ($result_coupon->num_rows==1) {
        $row_coupon = $result_coupon->fetch_assoc();
        $mybasketarray['coupons'][$value]=$row_coupon['pricelist_item_descr'];
      }
      
    }
  } 
  
  $mybasketarray['parastatiko']=intval($row_order['parastatiko']);

  $mybasketarray['products_need_apostoli'] = intval($row_order['products_need_apostoli'])!=0;
  $mybasketarray['products_varos']= intval($row_order['products_varos']);
  $mybasketarray['products_ogos']= intval($row_order['products_ogos']);
  $mybasketarray['products_ogos_max_x']= intval($row_order['products_ogos_max_x']);
  $mybasketarray['products_ogos_max_y']= intval($row_order['products_ogos_max_y']);
  $mybasketarray['products_ogos_max_z']= intval($row_order['products_ogos_max_z']);
  $mybasketarray['products_need_pliromi']=false;
  if (floatval($row_order['gks_price_total'])>0) $mybasketarray['products_need_pliromi']=true;;
  
  $mybasketarray['tropos_apostolis'] = intval($row_order['tropos_apostolis']);
  $mybasketarray['tropos_pliromis'] = intval($row_order['tropos_pliromis']);
  $mybasketarray['products_total'] = floatval($row_order['gks_price_total']);


  $fields_change=[];
  $basket_products_temp =array();
  
  $sql_eidi="SELECT *  
  FROM gks_orders_products
  WHERE order_id=".$id."
  order by product_aa";
  $result_eidi = $db_link->query($sql_eidi);        
  if (!$result_eidi) {$return['message']='error sql';
    debug_mail(false,$return['message'],$sql_eidi);return $return;}
  
  while ($row_eidos = $result_eidi->fetch_assoc()) {
    $value=[];
    $value['aa']=$row_eidos['product_aa'];
    //$value['id_order_product']=$row_eidos['id_order_product'];
    $value['product_id']=$row_eidos['product_id'];
    $value['product_fpa_base_id']=$row_eidos['product_fpa_base_id'];
    $value['product_fpa_aade_id']=$row_eidos['product_fpa_aade_id'];
    //product_fpa_id
    //product_fpa_pososto
    $value['product_sheets']=$row_eidos['product_sheets'];
    $value['product_quantity']=$row_eidos['product_quantity'];
    $value['product_monada_id']=$row_eidos['product_monada_id'];
    //product_price_check_fpa
    //product_price_start_all_net
    //product_price_ekptosi_pososto
    $value['product_price_final_all_net']=$row_eidos['product_price_final_all_net'];
    //product_price_final_all_fpa
    $value['product_price_final_all_total']=$row_eidos['product_price_final_all_total'];
    //product_descr
    //product_comments
    $value['product_set']=$row_eidos['product_set'];
    $value['product_is_optional']=$row_eidos['product_is_optional'];
    
    
    
    
    
    
    
    $value['product_withheldPercentCategory']=0;
    $value['product_withheldAmount']=0;
    $value['product_otherTaxesPercentCategory']=0;  
    $value['product_otherTaxesAmount']=0; 
    $value['product_stampDutyPercentCategory']=0;  
    $value['product_stampDutyAmount']=0;
    $value['product_feesPercentCategory']=0;  
    $value['product_feesAmount']=0;  
    $value['product_deductionsAmount']=0;  
     
    $sql_pp="select * from gks_eshop_products where id_product=".$value['product_id'];  
    $result_pp = $db_link->query($sql_pp);
    if (!$result_pp) {$return['message']='error sql';
      debug_mail(false,$return['message'],$sql_pp);return $return;}
    if ($result_pp->num_rows == 1) {
      $row_pp = $result_pp->fetch_assoc();
      $value['product_withheldPercentCategory']=$row_pp['product_withheldPercentCategory'];
      $value['product_otherTaxesPercentCategory']=$row_pp['product_otherTaxesPercentCategory'];
      $value['product_stampDutyPercentCategory']=$row_pp['product_stampDutyPercentCategory'];
      $value['product_feesPercentCategory']=$row_pp['product_feesPercentCategory'];
    }
    
    $objects=array();
    $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $value['product_quantity'],  'files' => array(), 'warnings'=>array());
    
    if ($value['product_fpa_base_id']>0) $value['product_fpa_aade_id']=0;
    
    $item_temp=array(
      'product_id'=>array(
        'id_product'=>$value['product_id'], 
        'product_monada_id' => $value['product_monada_id'], 
        'product_fpa_base_id' => $value['product_fpa_base_id'], 
        'product_fpa_aade_id' => $value['product_fpa_aade_id'],
        'product_sheets'=>$value['product_sheets'], 
        'product_set' => $value['product_set'],
        'product_is_optional'=> $value['product_is_optional'],
       ), 
      'objects'=>$objects,
      'user_ekptosi' => $row_order['def_ekptosi'],
      //'user_final_net' => $product_price_final_all_net,
      //'user_final_total' => $product_price_final_all_total,
      'user_change_ekptosi_or_final_net' => 'gks_price_final',
      'user_field_change' => 'gks_price_final',
      
      'other_taxes' => array(
        'withheldPercentCategory' => intval($value['product_withheldPercentCategory']),  
        'withheldAmount' => floatval($value['product_withheldAmount']),  
        'otherTaxesPercentCategory' => intval($value['product_otherTaxesPercentCategory']),  
        'otherTaxesAmount' => floatval($value['product_otherTaxesAmount']),  
        'stampDutyPercentCategory' => intval($value['product_stampDutyPercentCategory']),  
        'stampDutyAmount' => floatval($value['product_stampDutyAmount']), 
        'feesPercentCategory' => intval($value['product_feesPercentCategory']),  
        'feesAmount' => floatval($value['product_feesAmount']),  
        'deductionsAmount' => floatval($value['product_deductionsAmount']),  
      ),
      
    );
  
    //code gks_peritem_net gks_quantity gks_ekptosi gks_price
    
    //if (in_array($user_field_change,['code','gks_quantity'])==false and in_array($user_change_ekptosi_or_final_net,['gks_price']) and isset($value['product_price_check_fpa']) and intval($value['product_price_check_fpa'])==1) {
    //if (in_array($user_field_change,['code'])==false and in_array($user_change_ekptosi_or_final_net,['gks_price']) and isset($value['product_price_check_fpa']) and intval($value['product_price_check_fpa'])==1) {
      $item_temp['user_final_total']=floatval($value['product_price_final_all_total']);
      $item_temp['user_change_ekptosi_or_final_net']='gks_price_final';
      $item_temp['user_field_change']='gks_price_final';
      $item_temp['user_product_price_check_fpa']=true;
      
      if ($value['product_id']== 2) {
        $item_temp['user_final_net']=floatval($value['product_price_final_all_net']);
      }    
    //} else {
    //  $item_temp['user_final_net']=floatval($value['product_price_final_all_net']);
    //}
    //print '<pre>';print '|'.$user_field_change .'|'.$user_change_ekptosi_or_final_net.'|';die();
  
   
    $basket_products_temp[$value['aa']]=$item_temp;    
    
    $fields_change[$value['aa']]='gks_price_final'; // gks_price_final gks_price
  }

  //print '<pre>';print_r($basket_products_temp);die();
  
  $mybasketarray['products'] = $basket_products_temp;
  $myproducts = gks_basket_recalc($mybasketarray, $fields_change, array());

  $mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);
  $mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);
  
  $kostos_apostolis_mode=''; if (isset($mydata['kostos_apostolis_mode'])) $kostos_apostolis_mode=trim_gks($mydata['kostos_apostolis_mode']);
  $kostos_pliromis_mode='';  if (isset($mydata['kostos_pliromis_mode']))  $kostos_pliromis_mode= trim_gks($mydata['kostos_pliromis_mode']);

  if ($kostos_apostolis_mode=='manual') $mybasketarray['kostos_apostolis']=$mydata['kostos_apostolis'];
  if ($kostos_pliromis_mode=='manual')  $mybasketarray['kostos_pliromis']= $mydata['kostos_pliromis'];

  $pliroteo = $mybasketarray['products_total'] + 
              $mybasketarray['kostos_apostolis'] + 
              $mybasketarray['kostos_pliromis'];

  $products_ogos='';
  if ($mybasketarray['products_ogos_max_x']>0 or $mybasketarray['products_ogos_max_y']>0 or $mybasketarray['products_ogos_max_z']>0) {
    $products_ogos = number_format($mybasketarray['products_ogos_max_x'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                     number_format($mybasketarray['products_ogos_max_y'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).'x'.
                     number_format($mybasketarray['products_ogos_max_z'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND);
  }
  $products_varos='';
  if ($mybasketarray['products_varos']>0) $products_varos=number_format($mybasketarray['products_varos'], 0, '', $GKS_NUMBER_FORMAT_THOUSAND).' gr';

  
  print '<pre>';print_r($mybasketarray);die();
  
}
