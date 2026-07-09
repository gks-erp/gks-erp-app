<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id<>-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Υπολογισμός Παραγγελίας').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','edit',$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$mydata_str = trim_gks(base64_decode($_POST['mydata_str']));
//$eidi_array_str=substr($eidi_array_str, 10); //gia test otan iparxei error 

$mydata = json_decode($mydata_str, true);
if ($mydata === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['mydata_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}


if (isset($mydata['gks_lock']) and $mydata['gks_lock']==true) {
  
  $sql=select_gks_acc_inv()." where gks_acc_inv.id_acc_inv = ".$id;
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
  $products_need_pliromi=$row['products_need_pliromi']==0 ? false : true;
  
  unset($mybasketarray);
  gks_mybasketarray_create($mybasketarray);
  $mybasketarray['from']='acc_inv';
  $mybasketarray['id_object'] = $id;
  $mybasketarray['company_id']= $row['company_id'];
  $mybasketarray['company_sub_id']= $row['company_sub_id'];
  $mybasketarray['inv_acc_journal_id']=intval($row['inv_acc_journal_id']);
  $mybasketarray['inv_acc_seira_id']=intval($row['inv_acc_seira_id']);
  $mybasketarray['inv_state']=trim_gks($row['inv_state']);
  $mybasketarray['inv_date']=trim_gks($row['inv_date']);
  
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
  $mybasketarray['products_need_pliromi']=$products_need_pliromi;
  $mybasketarray['destination_data']['name'] = trim_gks($row['destination_data_name']);
  $mybasketarray['destination_data']['phone'] = trim_gks($row['destination_data_phone']);
  $mybasketarray['destination_data']['odos'] = trim_gks($row['destination_data_odos']);
  $mybasketarray['destination_data']['arithmos'] = trim_gks($row['destination_data_arithmos']);
  $mybasketarray['destination_data']['orofos'] = trim_gks($row['destination_data_orofos']);
  $mybasketarray['destination_data']['orofos'] = trim_gks($row['destination_data_orofos']);
  $mybasketarray['destination_data']['perioxi'] = trim_gks($row['destination_data_perioxi']);
  $mybasketarray['destination_data']['poli'] = trim_gks($row['destination_data_poli']);
  $mybasketarray['destination_data']['tk'] = trim_gks($row['destination_data_tk']);
  $mybasketarray['destination_data']['country_id'] = intval($row['destination_data_country_id']);
  $mybasketarray['destination_data']['nomos_id'] = intval($row['destination_data_nomos_id']);
  $mybasketarray['products_total'] = $row['gks_price_total'];
  
  $mybasketarray['tropos_apostolis'] = intval($mydata['tropos_apostolis']);
  $mybasketarray['tropos_pliromis'] = intval($mydata['tropos_pliromis']);

  
  $mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);
  $mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);

  $kostos_apostolis_mode=''; if (isset($mydata['kostos_apostolis_mode'])) $kostos_apostolis_mode=trim_gks($mydata['kostos_apostolis_mode']);
  $kostos_pliromis_mode='';  if (isset($mydata['kostos_pliromis_mode']))  $kostos_pliromis_mode= trim_gks($mydata['kostos_pliromis_mode']);
  
  if ($kostos_apostolis_mode=='manual') $mybasketarray['kostos_apostolis']=$mydata['kostos_apostolis'];
  if ($kostos_pliromis_mode=='manual')  $mybasketarray['kostos_pliromis']= $mydata['kostos_pliromis'];
    


  $pliroteo = $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'] + $mybasketarray['kostos_pliromis'];


  $cache_file='acc_inv_'.$id.'_'.date('Y-m-d_H-i-s').'_'.rand(10000,99999).'.json';
  file_put_contents(GKS_CACHE.$cache_file,json_encode($mybasketarray));
  //if (GKS_DEBUG) file_put_contents(GKS_CACHE.'basket.txt',print_r($mybasketarray,true));

  $return = array('success' => true, 'message' => base64_encode('OK'),
    //'eidi_array' => $eidi_array,
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
    'cache_file' =>$cache_file,
  );
  echo json_encode($return); die();


  
}


//print '<pre>';print_r($mydata);die();

$eidi_array=$mydata['eidi_array'];


$mycmd='';if (isset($mydata['mycmd'])) $mycmd=trim_gks($mydata['mycmd']);
$myfile='';if (isset($mydata['myfile'])) $myfile=trim_gks($mydata['myfile']);

$cmd_is_for_coupon=false;
if ($mycmd=='couponadd' or $mycmd=='coupondelete') {
  $cmd_is_for_coupon=true;
  $mycoupon=$myfile;
}
//$return = array('success' => false, 'message' => base64_encode($mycmd.' '.$mycoupon));
//echo json_encode($return); die();


unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);

$mybasketarray['from']='acc_inv';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']=intval($mydata['company_id']);
$mybasketarray['company_sub_id']=intval($mydata['company_sub_id']);
$mybasketarray['inv_acc_journal_id']=intval($mydata['inv_acc_journal_id']);
$mybasketarray['inv_acc_seira_id']=intval($mydata['inv_acc_seira_id']);
$mybasketarray['inv_state']=trim_gks($mydata['inv_state']);
$mybasketarray['inv_date']='';
if ($mydata['inv_date'] == '__/__/____ __:__') $mydata['inv_date']='';
$mydata['inv_date']=trim_gks(stripslashes(urldecode($mydata['inv_date'])));
if ($mydata['inv_date']!='') {
  $mybasketarray['inv_date'] = mystrtodb($mydata['inv_date']);
}
//echo '<pre>'; echo $mybasketarray['inv_date']; die();

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
if (isset($mydata['coupons_array'])) {
  $mybasketarray['coupons']=$mydata['coupons_array'];
  
}


$mybasketarray['parastatiko']=intval($mydata['need_afm']);


if ($id>0) {
  
  
  $sql=select_gks_acc_inv()." where gks_acc_inv.id_acc_inv = ".$id;
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
  
  $credit_memo_for_acc_inv_id=$row['credit_memo_for_acc_inv_id'];
  if ($credit_memo_for_acc_inv_id!=0) {
    $mybasketarray['company_id']= $row['company_id'];
    $mybasketarray['company_sub_id']= $row['company_sub_id'];
    
    $mybasketarray['user']['user_id']=$row['user_id'];
    $mybasketarray['user']['afm']=$row['afm'];
    $mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
    
    
    
    $sql=select_gks_acc_inv()." where gks_acc_inv.id_acc_inv = ".$credit_memo_for_acc_inv_id;
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
    $mybasketarray['inv_acc_journal_id']=intval($row['inv_acc_journal_id']);
    $mybasketarray['inv_acc_seira_id']=intval($row['inv_acc_seira_id']);
    $mybasketarray['parastatiko']= $row['eidos_parastatikou_need_afm'];
    
  }
}


if ($cmd_is_for_coupon) { //cmd is for coupon
  

  $pricelist_id= $mybasketarray['pricelist_id'];
  if ($pricelist_id <= 0) $pricelist_id = 1;  

  //$return = array('success' => false, 'message' => base64_encode($pricelist_id));
  //echo json_encode($return); die();  
  
  switch ($mycmd) {
    case 'couponadd':
      $sql="SELECT gks_eshop_pricelist_items.pricelist_item_coupon, gks_eshop_pricelist_items.pricelist_item_descr, 
      gks_eshop_pricelist_items.pricelist_item_date_from, gks_eshop_pricelist_items.pricelist_item_date_to
      FROM gks_eshop_pricelist_items
      WHERE gks_eshop_pricelist_items.pricelist_item_coupon='".$db_link->escape_string($mycoupon)."' 
      AND gks_eshop_pricelist_items.pricelist_id=".$pricelist_id." 
      AND gks_eshop_pricelist_items.pricelist_item_disable=0;";

      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        echo json_encode($return); die(); 
      }
      if ($result->num_rows == 0) {
        debug_mail(false,'mycoupon not found:'.$mycoupon );
        $msg_temp=gks_lang('Δεν βρέθηκε το κουπόνι <b>[1]</b>').'<br>'.
        gks_lang('Βεβαιωθείτε ότι το έχετε γράψει σωστά').'<br>'.
        gks_lang('Βεβαιωθείτε ότι έχετε επιλέξει το σωστό τιμοκατάλογο').' (1)';
        $msg_temp=str_replace('[1]', $mycoupon, $msg_temp);
        $return = array('success' => false, 'message' => base64_encode($msg_temp));
        echo json_encode($return); die();
      }  


      $pricelist_item_coupon='';
      $pricelist_item_descr='';
      while ($row = $result->fetch_assoc()) {  
        $pricelist_item_coupon=$row['pricelist_item_coupon'];
        $pricelist_item_descr=$row['pricelist_item_descr'];
        
        if (isset($row['pricelist_item_date_to'])) {
          if ( ! (time() >= strtotime($row['pricelist_item_date_from']) and time() <= strtotime($row['pricelist_item_date_to']))) {
            if (time() < strtotime($row['pricelist_item_date_from'])) { //den exei energopoiuei akoma
              debug_mail(false,'mycoupon not start yet:'.$mycoupon );
              $msg_temp=gks_lang('Δεν βρέθηκε το κουπόνι <b>[1]</b>').'<br>'.
              gks_lang('Βεβαιωθείτε ότι το έχετε γράψει σωστά').'<br>'.
              gks_lang('Βεβαιωθείτε ότι έχετε επιλέξει το σωστό τιμοκατάλογο').' (2)';
              $msg_temp=str_replace('[1]', $mycoupon, $msg_temp);
              $return = array('success' => false, 'message' => base64_encode($msg_temp));
              echo json_encode($return); die();          
            }
            debug_mail(false,'mycoupon expire',$mycoupon.':'. showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i:s', 1));
            $msg_temp=gks_lang('Το κουπόνι <b>[1]</b> έχει λήξει στις<br>[2]');
            $msg_temp=str_replace('[1]', $pricelist_item_coupon, $msg_temp);
            $msg_temp=str_replace('[2]', showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i:s', 1), $msg_temp);
            $return = array('success' => false, 'message' =>  base64_encode($msg_temp));
            echo json_encode($return); die();          
          }
        }
      }
      
      if (isset($mybasketarray['coupons'][$pricelist_item_coupon])) {
        debug_mail(false,'coupon already added:'. $mycoupon);
        $msg_temp=gks_lang('Το κουπόνι <b>[1]</b> το έχετε καταχωρήσει ήδη');
        $msg_temp=str_replace('[1]', $pricelist_item_coupon, $msg_temp);
        
        $return = array('success' => false, 'message' => base64_encode($msg_temp));
        echo json_encode($return); die();           
      } else {
        $mybasketarray['coupons'][$pricelist_item_coupon]=$pricelist_item_descr;
        $out[] =array('id' => '#input_coupon','type'=>'val', 'data' => base64_encode(''));
      }
      
      break;
    case 'coupondelete':
      //$return = array('success' => false, 'message' => base64_encode($mycoupon));
      //echo json_encode($return); die();
    
      if (isset($mybasketarray['coupons'][$mycoupon])) {
        unset($mybasketarray['coupons'][$mycoupon]);// = array();
      } else {
        debug_mail(false,'try to remove coupon:'. $mycoupon);
      }
      
      break;
    default:
      debug_mail(false,'error on cmd for coupon: '.$mycmd);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').'<br>'.gks_lang('Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
      echo json_encode($return); die();
  }
  
  $coupons_html='';
  foreach ($mybasketarray['coupons'] as $key => $coupon) {
     $coupons_html.='<span class="tooltipster_basket" title="'.$coupon.'" style="text-align:left;border: 1px solid gray;border-radius: 4px;padding:8px;margin-right: 6px;">
     <span class="coupons">'.$key.' 
     <i class="coupon_delete gks_fas gks_fa-trash-alt gks_basket_delete_icon" data-coupon="'.$key.'" style="cursor:pointer;"></i>
     </span></span> ';
  } 
  if ($coupons_html!='') {
    $coupons_html=gks_lang('Τα κουπόνια').': '.$coupons_html;
  }
  $out[] =array('id' => '#coupons_html','type'=>'html', 'data' => base64_encode($coupons_html));  
  
}








  
$mybasketarray['products_need_apostoli'] = intval($mydata['gks_products_need_apostoli'])!=0;
$mybasketarray['products_varos']= intval($mydata['gks_products_varos']);
$mybasketarray['products_ogos']= intval($mydata['gks_products_ogos']);
$mybasketarray['products_ogos_max_x']= intval($mydata['gks_products_ogos_x']);
$mybasketarray['products_ogos_max_y']= intval($mydata['gks_products_ogos_y']);
$mybasketarray['products_ogos_max_z']= intval($mydata['gks_products_ogos_z']);
$mybasketarray['products_need_pliromi']=false;
if (floatval($mydata['gks_total_price_total'])>0) $mybasketarray['products_need_pliromi']=true;;



//echo '<pre>';
//print $mybasketarray['destination_data']['country_id'];
//die();

$mybasketarray['tropos_apostolis'] = intval($mydata['tropos_apostolis']);
$mybasketarray['tropos_pliromis'] = intval($mydata['tropos_pliromis']);
$mybasketarray['products_total'] = floatval($mydata['gks_total_price_total']);

$fields_change=$mydata['fields_change'];
$fields_change_curr_name=trim_gks($mydata['fields_change_curr_name']);
$fields_change_curr_aa=intval($mydata['fields_change_curr_aa']);



$basket_products_temp =array();
foreach ($eidi_array as &$value) {
  $user_field_change='';
  if ($value['aa'] == $fields_change_curr_aa) $user_field_change=$fields_change_curr_name;
  $user_change_ekptosi_or_final_net='';
  if (isset($fields_change[$value['aa']])) $user_change_ekptosi_or_final_net=$fields_change[$value['aa']];
  
  $user_ekptosi = floatval($value['product_price_ekptosi_pososto']);  
  
  


  $hotel_check_out_round  = showDate(time(),'Y-d-m',1);
  $hotel_check_in_round = showDate(time()-$value['product_quantity']*24*60*60,'Y-d-m',1);
  if ($mybasketarray['inv_date']!='') {
    $hotel_check_out_round  = date('Y-d-m',strtotime($mybasketarray['inv_date']));
    $hotel_check_in_round = date('Y-d-m',strtotime($mybasketarray['inv_date'])-$value['product_quantity']*24*60*60);
  }

 
  //print '<pre>';var_dump($value);die();
  
  $objects=array();
  $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $value['product_quantity'],  'files' => array(), 'warnings'=>array());
  
  if ($value['product_fpa_base_id']>0) $value['product_fpa_aade_id']=0;
  
  $item_temp=array(
    'product_id'=>array(
      'id_product'=>$value['product_id'], 
      'product_monada_id' => $value['product_monada_id'], 
      'product_fpa_base_id' => $value['product_fpa_base_id'], 
      'product_fpa_aade_id' => $value['product_fpa_aade_id'], 
      'product_sheets'=>0, 
      'product_set' => '',
     ), 
    'objects'=>$objects,
    'user_ekptosi' => $user_ekptosi,
    //'user_final_net' => $product_price_final_all_net,
    //'user_final_total' => $product_price_final_all_total,
    'user_change_ekptosi_or_final_net' => $user_change_ekptosi_or_final_net,
    'user_field_change' => $user_field_change,
    'from_aade_import_user_fpa' => $value['from_aade_import_user_fpa'],
    'from_aade_import_user_fpa_value' => $value['from_aade_import_user_fpa_value'],
    
    
    
    
//    'user_check_in'=> $hotel_check_in_round,
//    'user_check_out'=> $hotel_check_out_round,
//    'user_room_id' => 10062,
//    'user_rnum_adults' => 1,
//    'user_rnum_childs' => 0,
//    'user_rchilds_ages_list' => '',

    
    'other_taxes' => array(
      'withheldPercentCategory' => intval($value['product_withheldPercentCategory']),  
      'withheldAmount' => floatval($value['product_withheldAmount']),  
      'otherTaxesPercentCategory' => intval($value['product_otherTaxesPercentCategory']),  
      'otherTaxesAmount' => floatval($value['product_otherTaxesAmount']),  
      'stampDutyPercentCategory' => intval($value['product_stampDutyPercentCategory']),  
      'stampDutyAmount' => floatval($value['product_stampDutyAmount']), 
      'feesPercentCategory' => intval($value['product_feesPercentCategory']),  
      'feesAmount' => floatval($value['product_feesAmount']),  
      'deductionsSelection' => trim_gks($value['product_deductionsSelection']),  
      'deductionsAmount' => floatval($value['product_deductionsAmount']),  
    ),
    
  );

  //code gks_peritem_net gks_quantity gks_ekptosi gks_price
  
  //if (in_array($user_field_change,['code','gks_quantity'])==false and in_array($user_change_ekptosi_or_final_net,['gks_price']) and isset($value['product_price_check_fpa']) and intval($value['product_price_check_fpa'])==1) {
  if (in_array($user_field_change,['code'])==false and in_array($user_change_ekptosi_or_final_net,['gks_price']) and isset($value['product_price_check_fpa']) and intval($value['product_price_check_fpa'])==1) {
    $item_temp['user_final_total']=floatval($value['product_price_final_all_total']);
    $item_temp['user_change_ekptosi_or_final_net']='gks_price_final';
    $item_temp['user_field_change']='gks_price_final';
    $item_temp['user_product_price_check_fpa']=true;
    
    if ($value['product_id']== 2) {
      $item_temp['user_final_net']=floatval($value['product_price_final_all_net']);
    }
  } else {
    $item_temp['user_final_net']=floatval($value['product_price_final_all_net']);
  }
  //print '<pre>';print '|'.$user_field_change .'|'.$user_change_ekptosi_or_final_net.'|'.$item_temp['user_field_change'];die();

 
  $basket_products_temp[$value['aa']]=$item_temp;
  
}
unset($value);

//print '<pre>';print_r($basket_products_temp);die();  


$mybasketarray['products'] = $basket_products_temp;
$myproducts = gks_basket_recalc($mybasketarray, $fields_change, array());


//$tropos_apostolis=intval($mydata['tropos_apostolis']);
//$tropos_pliromis=intval($mydata['tropos_pliromis']);


$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, -1);
$mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);

$kostos_apostolis_mode=''; if (isset($mydata['kostos_apostolis_mode'])) $kostos_apostolis_mode=trim_gks($mydata['kostos_apostolis_mode']);
$kostos_pliromis_mode='';  if (isset($mydata['kostos_pliromis_mode']))  $kostos_pliromis_mode= trim_gks($mydata['kostos_pliromis_mode']);

if ($kostos_apostolis_mode=='manual') $mybasketarray['kostos_apostolis']=$mydata['kostos_apostolis'];
if ($kostos_pliromis_mode=='manual')  $mybasketarray['kostos_pliromis']= $mydata['kostos_pliromis'];


$pliroteo = $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'] + $mybasketarray['kostos_pliromis'];

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
  //echo '<pre>';print_r($value);die();
  if ($value['product_id']['id_product']== 2) {
    $product_price_ekptosi_pososto=0;
    $ekptosi_poso_netfpa_html='';
    $ekptosi_poso_html='';
  } else {
    $product_price_ekptosi_net=round($value['product_id']['product_price_start_all_net']-$value['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    $product_price_ekptosi_netfpa=round($value['product_id']['product_price_start_all_net']+$value['product_id']['product_price_start_all_fpa']-$value['product_id']['product_price_final_all_net']-$value['product_id']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    $product_price_ekptosi_total=round($value['product_id']['product_price_start_all_total']-$value['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    $product_price_ekptosi_pososto=0;
    if ($value['product_id']['product_price_start_all_net']!=0 and $value['product_id']['product_price_include_vat']==0) {
      $product_price_ekptosi_pososto=round($product_price_ekptosi_net*100/$value['product_id']['product_price_start_all_net'],$GKS_BASKET_CALC_EKPTOSI_DECIMAL);
    } else if ($value['product_id']['product_price_start_all_total']!=0 and $value['product_id']['product_price_include_vat']!=0) {
      $product_price_ekptosi_pososto=round($product_price_ekptosi_total*100/$value['product_id']['product_price_start_all_total'],$GKS_BASKET_CALC_EKPTOSI_DECIMAL);
    }
    $ekptosi_poso_html='';
    if ($product_price_ekptosi_net!=0) {
      $ekptosi_poso_html=myCurrencyFormat($product_price_ekptosi_net,false,true);
      if ($GKS_BASKET_ROUND_DIAFORA_001) {
        if (abs($product_price_ekptosi_net)<=(1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) {
          $ekptosi_poso_html=''; //to 0.01 na ginei keno
        }
      } 
    }
    $ekptosi_poso_netfpa_html='';
    if ($product_price_ekptosi_netfpa!=0) {
      $ekptosi_poso_netfpa_html=myCurrencyFormat($product_price_ekptosi_netfpa,false,true);
      if ($GKS_BASKET_ROUND_DIAFORA_001) {
        if (abs($product_price_ekptosi_netfpa)<=(1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) {
          $ekptosi_poso_netfpa_html=''; //to 0.01 na ginei keno
        }
      } 
    }
  
    if (isset($value['user_change_ekptosi_or_final_net']) and 
        $value['user_change_ekptosi_or_final_net']=='gks_ekptosi' and 
        isset($value['user_ekptosi'])) {
      $product_price_ekptosi_pososto=$value['user_ekptosi'];
    } else if (isset($value['user_ekptosi']) and
        $value['user_field_change']=='gks_quantity' and 
        $value['product_id']['product_quantity']==0) {
      //echo '<pre>sss ';print_r($value);die();
      $product_price_ekptosi_pososto=$value['user_ekptosi'];
    }
  }


  //print '<pre>';print $value['product_id']['product_price_final_all_fpa']; die();
  //print '<pre>';print_r($value);die();
  
  //delete me
  //$value['other_taxes']['deductionsAmount']=11;
  
  $eidi[] = array(
    'aa' => $aa,
    'id_product' => intval($value['product_id']['id_product']),
    //'raw'=>$value['product_id'],
    'product_price_final_peritem_net' => round($value['product_id']['product_price_final_peritem_net'],$GKS_BASKET_CALC_ITEM_DECIMAL),
    'product_price_final_peritem_fpa' => round($value['product_id']['product_price_final_peritem_fpa'],$GKS_BASKET_CALC_ITEM_DECIMAL),
    'product_price_final_all_net' => $value['product_id']['product_price_final_all_net'],
    'product_price_start_all_net' => $value['product_id']['product_price_start_all_net'],
    'product_price_final_all_fpa' => $value['product_id']['product_price_final_all_fpa'],
    'fpa_base_id' => $value['product_id']['product_fpa_base_id'],
    'fpa_aade_id' => $value['product_id']['product_fpa_aade_id'],
    'id_fpa' => intval($value['product_id']['product_fpa_id_array']['id_fpa_to']),
    'fpa_pososto' => floatval($value['product_id']['product_fpa_id_array']['fpa_pososto']),
    'fpa_descr_print' => $value['product_id']['product_fpa_id_array']['fpa_descr_print'],
    //'fpa_descr_print_fr' => $value['product_id']['product_fpa_id_array']['fpa_descr_print_fr'],

    
    'varos' => intval($value['product_id']['product_varos']),
    'ogos_x' => intval($value['product_id']['product_ogos_x']),
    'ogos_y' => intval($value['product_id']['product_ogos_y']),
    'ogos_z' => intval($value['product_id']['product_ogos_z']),
    'need_apostoli' => intval($value['product_id']['product_need_apostoli']),
    'ekptosi_poso_html' => $ekptosi_poso_html,
    'ekptosi_poso_netfpa_html' => $ekptosi_poso_netfpa_html,
    'product_price_ekptosi_pososto' => $product_price_ekptosi_pososto,
    'product_price_coupon_use' => $value['product_id']['product_price_coupon_use'],
    'product_price_coupon_use_disabled' => $value['product_id']['product_price_coupon_use_disabled'],
    
    
    'other_taxes' => $value['other_taxes'],
    
    'calc_pricelist_item_descr'=>$value['product_id']['calc_pricelist_item_descr'],
  );
  
  //print '<pre>';var_dump($eidi);die();
  
} 


gks_CheckAFM_Live($mybasketarray);




$coupons_html='';
foreach ($mybasketarray['coupons'] as $key => $coupon) {
   $coupons_html.='<span class="tooltipster coupons_span" title="'.$coupon.'">
   <span class="coupons text-sm">'.$key.' 
   <i class="coupon_delete fas fa-trash-alt gks_order_delete_icon" data-coupon="'.$key.'" style=""></i>
   </span></span> ';
} 
if ($coupons_html!='') {
  $coupons_html=gks_lang('Κουπόνια').': '.$coupons_html;
}

$cache_file='acc_inv_'.$id.'_'.date('Y-m-d_H-i-s').'_'.rand(10000,99999).'.json';
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
  'products_netvalue' => (myCurrencyFormat($mybasketarray['products_netvalue'],true,true)),
  'products_netvalue_fl' => number_format($mybasketarray['products_netvalue'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.',''),
  'products_fpa'      => ($mybasketarray['products_fpa']==0 ? '' : myCurrencyFormat($mybasketarray['products_fpa'],true,true)),
  'products_fpa_fl'      => number_format($mybasketarray['products_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.',''),
  'products_netfpa'   => ($mybasketarray['products_netfpa']==0 ? '' : myCurrencyFormat($mybasketarray['products_netfpa'],true,true)),
  'products_netfpa_fl'=> $mybasketarray['products_netfpa'],
  
  'totalWithheldAmount' => ($mybasketarray['totalWithheldAmount']==0 ? '' : myCurrencyFormat($mybasketarray['totalWithheldAmount'],true,true)),
  'totalWithheldAmount_fl' => number_format($mybasketarray['totalWithheldAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.',''),
  'totalOtherTaxesAmount' => ($mybasketarray['totalOtherTaxesAmount']==0 ? '' :myCurrencyFormat($mybasketarray['totalOtherTaxesAmount'],true,true)),
  'totalOtherTaxesAmount_fl' => number_format($mybasketarray['totalOtherTaxesAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.',''),
  'totalStampDutyamount' => ($mybasketarray['totalStampDutyamount']==0 ? '' : myCurrencyFormat($mybasketarray['totalStampDutyamount'],true,true)),
  'totalStampDutyamount_fl' => number_format($mybasketarray['totalStampDutyamount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.',''),
  'totalFeesAmount' => ($mybasketarray['totalFeesAmount']==0 ? '' : myCurrencyFormat($mybasketarray['totalFeesAmount'],true,true)),
  'totalFeesAmount_fl' => number_format($mybasketarray['totalFeesAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.',''),
  'totalDeductionsAmount' => ($mybasketarray['totalDeductionsAmount']==0 ? '' : myCurrencyFormat($mybasketarray['totalDeductionsAmount'],true,true)),
  'totalDeductionsAmount_fl' => number_format($mybasketarray['totalDeductionsAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.',''),
 
  'products_total' => (myCurrencyFormat($mybasketarray['products_total'],true,true)),
  'products_total_fl' => $mybasketarray['products_total'],
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
//var_dump($eidi[0]['product_price_final_all_fpa']);

//echo json_encode($eidi[0]['product_price_final_all_fpa']);
//echo json_encode($eidi[0]);

//die();
echo json_encode($return); die();

