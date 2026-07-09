<?php

function gks_hotel_reservation_booking_save($read_file) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  
  if (isset($my_wp_user_id)==false or $my_wp_user_id<=0) $my_wp_user_id=2;
  if (isset($gkIP)==false or $gkIP=='') $gkIP='127.0.0.1';
  
  $return = array('success' => false, 'message' => 'generic error reservation_booking_save', 'dev_errors' => array());
  
  
  $rsv_data=gks_hotel_reservation_parse_booking_email($read_file);
  
  
  if ($rsv_data['success']==false) {
    $return = array('success' => false, 'message' => base64_encode('Σφάλμα κατά την εισαγωγή της κράτησης'), 'dev_errors' => $rsv_data['errors']);
    return $return;
  }
  $data=$rsv_data['data'];
  echo 'remove me'."\n";
  $data['reservation_status']='010draft';
  
  
  $sql="select * from gks_hotel where hotel_disable=0 and hotel_id_booking like '".$db_link->escape_string($data['hotel_id'])."'";
  $result = $db_link->query($sql);        
  if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows!=1) {
    $return['message']='Δεν βρέθηκε το ξενοδοχείο με κωδικό booking <b>'.$data['hotel_id'].'</b>';
    debug_mail(false,$return['message'],$sql);return $return;}
  
  $row = $result->fetch_assoc();
  $id_hotel=$row['id_hotel'];
  $hotel_id=$id_hotel;
  $hotel_title=trim_gks($row['hotel_title']);
  $company_id=$row['company_id'];
  $company_sub_id=$row['company_sub_id'];

  $hotel_params=gks_hotel_get_params($id_hotel);

  //find all hotel floors
  $sql="SELECT id_hotel_floor FROM gks_hotel_floor WHERE hotel_id=".$id_hotel." ORDER BY sort_order";
  $result = $db_link->query($sql);        
  if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
  $floors=array();
  while ($row = $result->fetch_assoc()) $floors[]=$row['id_hotel_floor'];

  foreach ($data['rooms'] as &$myroom) {
    $myroom['id_hotel_room_type']=0;
    $sql="select id_hotel_room_type from gks_hotel_room_type 
    where hotel_id=".$id_hotel."
    and (room_type_descr like '".$db_link->escape_string($myroom['name'])."' 
    or id_hotel_room_type in (
      select hotel_room_type_id from gks_hotel_room_type_lang 
      where room_type_descr like '".$db_link->escape_string($myroom['name'])."'
      and lang_code='en-US'
    ))";
    
    $result = $db_link->query($sql);
    if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $myroom['id_hotel_room_type']=$row['id_hotel_room_type'];
    }
    
    if ($myroom['id_hotel_room_type']==0) {
      $sql="SELECT gks_hotel_room_type_channel_name.hotel_room_type_id
      FROM gks_hotel_room_type_channel_name 
      LEFT JOIN gks_hotel_room_type ON gks_hotel_room_type_channel_name.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type
      WHERE gks_hotel_room_type.hotel_id=".$id_hotel."
      AND gks_hotel_room_type_channel_name.channel='booking'
      AND gks_hotel_room_type_channel_name.room_type_descr_channel_name like '".$db_link->escape_string($myroom['name'])."'
      ORDER BY gks_hotel_room_type.room_type_sortorder;";
      $result = $db_link->query($sql);
      if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
      if ($result->num_rows==0) {
        $return['message']='Δεν βρέθηκε o τύπος δωματίου<br><b>'.$myroom['name'].'</b><br>στο ξενοδοχείο<br><b>'.$hotel_title.'</b>';
        debug_mail(false,$return['message'],$sql);return $return;
      } else {
        $row = $result->fetch_assoc();
        $myroom['id_hotel_room_type']=$row['hotel_room_type_id'];
      }
    }

  }
  unset($myroom);
  //print_r($data);die();

  
  $id=-1;
  $sql="select * from gks_hotel_reservation where crm_channel_id=21 and crm_channel_code like '".$db_link->escape_string($data['reservation_id'])."'";
  $result = $db_link->query($sql);        
  if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $id=$row['id_hotel_reservation'];
  }
  $set_rooms_id_template=array(); foreach ($data['rooms'] as $myroom) $set_rooms_id_template[$myroom['id_hotel_room_type']]=0;
  
  //alldata-> true gia na vro ta kathara domatia, xoris draft kratiseis
  $get_availability_rooms_imput=array(
    'id_hotel' => $id_hotel,
    'date_from' => $data['check_in'],
    'date_to' => $data['check_out'],
    'alldata' => true,
    'id_hotel_room' => 0,
    'id_hotel_room_type' => 0,
    'not_id_hotel_reservation' => $id,
    'not_id_hotel_folio' => 0,
    'not_id_hotel_room' => array(),
    'rnum_adults' => 0,
    'rnum_childs' => 0,
    'rchilds_ages_list' => array(),
    'rnum_child_kounies' => 0,
    'rnum_extra_beds' => 0,
  );
  $rooms_array = get_availability_rooms($get_availability_rooms_imput);
  
  
  
  //kathara domatia, xoris draft kratiseis, ston idio orofo
  foreach ($floors as $myfloor) {
    $set_rooms_id=$set_rooms_id_template;
    foreach ($set_rooms_id as $type_id => &$value) {
      foreach ($rooms_array['rooms'] as $room) {
        if ($room['is_avl_state_reservation']==true and 
            $room['is_avl_state_folio']==true and
            $room['hotel_room_type_id']==$type_id and 
            $myfloor == $room['hotel_floor_id']) {
          if (in_array($room['id_hotel_room'],$set_rooms_id)==false) {
            $value=$room['id_hotel_room'];
            break;
          }
        }
      }
    }
    unset($value);
    $isokcc=0; foreach ($set_rooms_id as $value) if ($value) $isokcc++;
    if ($isokcc==count($set_rooms_id)) break;
  }

  $isokcc=0; foreach ($set_rooms_id as $value) if ($value) $isokcc++;
  if ($isokcc!=count($set_rooms_id)) {
    //kathara domatia, xoris draft kratiseis, asxeta apo orofo
    $set_rooms_id=$set_rooms_id_template;
    foreach ($set_rooms_id as $type_id => &$value) {
      foreach ($rooms_array['rooms'] as $room) {
        if ($room['is_avl_state_reservation']==true and 
            $room['is_avl_state_folio']==true and
            $room['hotel_room_type_id']==$type_id) {
          if (in_array($room['id_hotel_room'],$set_rooms_id)==false) {
            $value=$room['id_hotel_room'];
            break;
          }
        }
      }
    }
    unset($value);
  }
  
  //alldata-> false gia na vro ta diathesiam domatia (asxeta ean exoyn draft)
  $get_availability_rooms_imput=array(
    'id_hotel' => $id_hotel,
    'date_from' => $data['check_in'],
    'date_to' => $data['check_out'],
    'alldata' => false,
    'id_hotel_room' => 0,
    'id_hotel_room_type' => 0,
    'not_id_hotel_reservation' => $id,
    'not_id_hotel_folio' => 0,
    'not_id_hotel_room' => array(),
    'rnum_adults' => 0,
    'rnum_childs' => 0,
    'rchilds_ages_list' => array(),
    'rnum_child_kounies' => 0,
    'rnum_extra_beds' => 0,
  );
  $rooms_array = get_availability_rooms($get_availability_rooms_imput);
  
  //ston idio orofo
  foreach ($floors as $myfloor) {
    $set_rooms_id=$set_rooms_id_template;
    foreach ($set_rooms_id as $type_id => &$value) {
      foreach ($rooms_array['rooms'] as $room) {
        if ($room['is_avl_state_reservation']==true and 
            $room['is_avl_state_folio']==true and
            $room['hotel_room_type_id']==$type_id and 
            $myfloor == $room['hotel_floor_id']) {
          if (in_array($room['id_hotel_room'],$set_rooms_id)==false) {
            $value=$room['id_hotel_room'];
            break;
          }
        }
      }
    }
    unset($value);
    $isokcc=0; foreach ($set_rooms_id as $value) if ($value) $isokcc++;
    if ($isokcc==count($set_rooms_id)) break;
  }  
  
  $isokcc=0; foreach ($set_rooms_id as $value) if ($value) $isokcc++;
  if ($isokcc!=count($set_rooms_id)) {
    //kathara domatia, xoris draft kratiseis, asxeta apo orofo
    $set_rooms_id=$set_rooms_id_template;
    foreach ($set_rooms_id as $type_id => &$value) {
      foreach ($rooms_array['rooms'] as $room) {
        if ($room['is_avl_state_reservation']==true and 
            $room['is_avl_state_folio']==true and
            $room['hotel_room_type_id']==$type_id) {
          if (in_array($room['id_hotel_room'],$set_rooms_id)==false) {
            $value=$room['id_hotel_room'];
            break;
          }
        }
      }
    }
    unset($value);
  }
  
  $isokcc=0; foreach ($set_rooms_id as $value) if ($value) $isokcc++;
  if ($isokcc!=count($set_rooms_id)) {
    $return['message']='Δεν βρέθηκαν διαθέσιμα δωμάτια';
    debug_mail(false,$return['message'],$sql);return $return;
  }
  
  
  //print 'floors '; print_r($floors); 
  //print 'set_rooms_id_template '; print_r($set_rooms_id_template);
  //print 'set_rooms_id '; print_r($set_rooms_id);
  //print 'rooms_array '; print_r($rooms_array); echo time();die();

  
  $sel_rooms=array();
  foreach ($set_rooms_id as $type_id => $value) {
    $sel_rooms[]=array(
      'hotel_room_type_id' => $type_id,
      'hotel_room_id' => $value,
    );
  } 
  //print 'sel_rooms '; print_r($sel_rooms);
  //print 'data_rooms '; print_r($data['rooms']);
  //print 'rooms_array '; print_r($rooms_array);
  
  $fields_change_curr_aa=0;
  $fields_change_curr_name='gks_price_final';

  $roolist=array();
  $fields_change=array();
  $aa=-1;
  foreach ($data['rooms'] as $myroom) {
    $aa++;
    $fields_change[$aa]='gks_price_final';
    
    $user_first_name='';
    $user_last_name='';
    if ($myroom['fullname']!='') {
      $fullname=explode(' ',$myroom['fullname'],2);
      $user_first_name=$fullname[0];
      if (count($fullname)>=2) $user_last_name=$fullname[1];
    }
  
    $roolist[]=array(
      'aa' => $aa,
      'add' => 1,
      'edit' => 0,
      'delete' => 0,
      'recid' => -1,
      'hotel_room_id' => $sel_rooms[$aa]['hotel_room_id'],
      'room_descr' => $rooms_array['rooms'][$sel_rooms[$aa]['hotel_room_id']]['room_descr'],
      'room_type_descr' => $rooms_array['rooms'][$sel_rooms[$aa]['hotel_room_id']]['room_type_descr'],
      'visitors' => $rooms_array['rooms'][$sel_rooms[$aa]['hotel_room_id']]['room_type_visitors'],
      'visitors_childs' => $rooms_array['rooms'][$sel_rooms[$aa]['hotel_room_id']]['room_type_visitors_childs'],
      'visitors_max' => $rooms_array['rooms'][$sel_rooms[$aa]['hotel_room_id']]['room_type_visitors_max'],
      'room_type_child_kounies' => $rooms_array['rooms'][$sel_rooms[$aa]['hotel_room_id']]['room_type_child_kounies'],
      'room_type_extra_beds' => $rooms_array['rooms'][$sel_rooms[$aa]['hotel_room_id']]['room_type_extra_beds'],
      'rnum_adults' => $myroom['visitors']['rnum_adults'],
      'rnum_childs' => $myroom['visitors']['rnum_childs'],
      'rchilds_ages_list' => '[]', //'[{"index":1,"age":4},{"index":2,"age":16}]',
      'rnum_child_kounies' => 0,
      'rnum_extra_beds' => 0,
      'ruser_id' => -1,
      'gks_nickname' => '',
      'ruser_lang' => '',
      'ruser_first_name' => $user_first_name,
      'ruser_last_name' => $user_last_name,
      'ruser_email' => '',
      'ruser_mobile' => '',
      'ruser_ma_odos' => '',
      'ruser_ma_orofos' => '',
      'ruser_ma_perioxi' => '',
      'ruser_ma_poli' => '',
      'ruser_ma_tk' => '',
      'ruser_ma_country_id' => 0,
      'ruser_ma_nomos_id' => 0,
      'rsxolio' => '',
      'ruser_fiscal_position_id' => 0,
      'ruser_pricelist_id' => 0,
      'ajia_total' => $myroom['price'],
      'gks_ekptosi_pososto' => 0,
      'pdata' => array(),
    );    
  }

  $check_in=$data['check_in'] .' '.$hotel_params['hotel_default_checkin'];
  $check_out=date('Y-m-d',strtotime($data['check_out']) + 24*60*60).' '.$hotel_params['hotel_default_checkout'];
  
  $days_round=hotel_round_days($id_hotel, $check_in, $check_out);
//  print '<pre>';
//  print $check_in."\n";
//  print $check_out."\n";
//  print_r($days_round);
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
    gks_hotel_room.id_hotel_room,
    gks_eshop_products.product_fpa_base_id
    FROM (gks_hotel_room 
    LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type)
    LEFT JOIN gks_eshop_products ON gks_hotel_room_type.product_id = gks_eshop_products.id_product
    WHERE gks_hotel_room_type.id_hotel_room_type Is Not Null
    AND gks_hotel_room_type.product_id>0
    AND gks_hotel_room.id_hotel_room In (".implode(',',$array_rooms_ids).")";
    
    //echo '<pre>';print $sql; die();
    
    $result = $db_link->query($sql);        
    if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
    while ($row = $result->fetch_assoc()) {
      $room_product[$row['id_hotel_room']] = $row;
    }   
    //echo '<pre>';print_r($room_product); die();
  }

  $dr_user_first_name='';
  $dr_user_last_name='';
  if ($data['fullname']!='') {
    $fullname=explode(' ',$data['fullname'],2);
    $dr_user_first_name=$fullname[0];
    if (count($fullname)>=2) $dr_user_last_name=$fullname[1];
  }
  
  unset($mybasketarray);
  gks_mybasketarray_create($mybasketarray);
  
  $mybasketarray['from']='reservation';
  $mybasketarray['id_object'] = $id;
  $mybasketarray['company_id']=intval($company_id);
  $mybasketarray['company_sub_id']=intval($company_sub_id);
  $mybasketarray['user']['user_id']=0;
  $mybasketarray['user']['first_name']=$dr_user_first_name;
  $mybasketarray['user']['last_name']=$dr_user_last_name;
  $mybasketarray['user']['email']='';
  $mybasketarray['user']['mobile']='';
  $mybasketarray['user']['lang']=$data['user_lang'];
  $mybasketarray['user']['ma_odos']=$data['address'];
  $mybasketarray['user']['ma_orofos']='';
  $mybasketarray['user']['ma_perioxi']='';
  $mybasketarray['user']['ma_poli']='';
  $mybasketarray['user']['ma_tk']='';
  $mybasketarray['user']['ma_country_id']=$data['ma_country_id'];
  $mybasketarray['user']['ma_nomos_id']=0;
  $mybasketarray['user']['eponimia']='';
  $mybasketarray['user']['title']='';
  $mybasketarray['user']['afm']='';
  $mybasketarray['user']['doy']='';
  $mybasketarray['user']['epaggelma']='';
  $mybasketarray['address_extra']=-1;
  
  
  
  //$mybasketarray['user']['ma_country_id']=91;
  $mybasketarray['fiscal_position']=($data['ma_country_id']==91 ? '1' : ( $data['country_ee']!='' ? '2' : '3'));
  if ($mybasketarray['fiscal_position']<1) $mybasketarray['fiscal_position']=1;
  
  $mybasketarray['pricelist_id']=1;
  if ($mybasketarray['pricelist_id']<1) $mybasketarray['pricelist_id']=1;
  $mybasketarray['coupons']=array();
  
  //$mybasketarray['coupons']['μειον20']='Μείον 20%';
  //$mybasketarray['coupons']['μειον21']='Μείον 21%';
  //$mybasketarray['coupons']['μειον22']='Μείον 22%';
  //$mybasketarray['coupons']['μειον23']='Μείον 23%';
  //$mybasketarray['coupons']['μειον24']='Μείον 24%';
  //$mybasketarray['coupons']['μειον25']='Μείον 25%';
  
  $mybasketarray['parastatiko']=1;  
  
  $tropos_apostolis=1;$kostos_apostolis=0;
  $tropos_pliromis=10; //epi pistosi
  $mybasketarray['tropos_apostolis'] = $tropos_apostolis;
  $mybasketarray['tropos_pliromis'] = $tropos_pliromis;  

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
      
      $value['product_withheldPercentCategory']=0;
      $value['product_withheldAmount']=0;
      $value['product_otherTaxesPercentCategory']=0;  
      $value['product_otherTaxesAmount']=0; 
      $value['product_stampDutyPercentCategory']=0;  
      $value['product_stampDutyAmount']=0;
      $value['product_feesPercentCategory']=0;  
      $value['product_feesAmount']=0;  
      $value['product_deductionsAmount']=0;  
      
      $sql="select * from gks_eshop_products where id_product=".$room_product[$value['hotel_room_id']]['product_id'];  
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        echo json_encode($return); die(); 
      }
      if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $value['product_withheldPercentCategory']=$row['product_withheldPercentCategory'];
        $value['product_otherTaxesPercentCategory']=$row['product_otherTaxesPercentCategory'];
        $value['product_stampDutyPercentCategory']=$row['product_stampDutyPercentCategory'];
        $value['product_feesPercentCategory']=$row['product_feesPercentCategory'];
      }        
      
      //print '<pre>'; print_r($days_round);die();
      
      $objects=array();
      $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $days_round['num_days'], 'files' => array(), 'warnings'=>array());
      //print '<pre>';print_r($room_product[$value['hotel_room_id']]);die();
      
      //print '<pre>';print_r($value);die();
      
      $basket_products_temp[$aa]=array(
        'is_hotel_room_type' => true,
        'product_id'=>array(
          'id_product'=>$room_product[$value['hotel_room_id']]['product_id'], 
          'product_monada_id' => 100,
          'product_fpa_base_id' => $room_product[$value['hotel_room_id']]['product_fpa_base_id'], 
          'product_sheets'=>0, 
          'product_set' => '',
        ), 
        'objects'=>$objects,
        'user_ekptosi' => $user_ekptosi,
        'user_final_net' => floatval($value['ajia_total']) ,
        'user_final_total' => floatval($value['ajia_total']) ,
        'user_change_ekptosi_or_final_net' => $user_change_ekptosi_or_final_net,
        'user_field_change' => $user_field_change,
        
        
        'id_hotel'=> $hotel_id,
        'hotel_room_id'=> $value['hotel_room_id'],
        'user_check_in'=> $days_round['check_in_round'],
        'user_check_out'=> $days_round['check_out_round'],
        'user_room_id' => $value['hotel_room_id'],
        'user_rnum_adults' => $value['rnum_adults'],
        'user_rnum_childs' => $value['rnum_childs'],
        'user_rchilds_ages_list' => $value['rchilds_ages_list'],
        'user_rnum_child_kounies' => $value['rnum_child_kounies'],
        'user_rnum_extra_beds' => $value['rnum_extra_beds'],


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

  

  

  $kostos_pliromis_mode='';
  if ($kostos_pliromis_mode=='manual')  $mybasketarray['kostos_pliromis']= $kostos_pliromis;
  
  
  $pliroteo = $mybasketarray['products_total'] + $mybasketarray['kostos_apostolis'] + $mybasketarray['kostos_pliromis'];

  foreach ($roolist as &$value) {
    foreach ($mybasketarray['products'] as $product) {
      if ($value['hotel_room_id']==$product['hotel_room_id']) {


            
        
        $product_price_ekptosi_net=round($product['product_id']['product_price_start_all_net']-$product['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $product_price_ekptosi_total=round($product['product_id']['product_price_start_all_total']-$product['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $product_price_ekptosi_pososto=0;
        if ($product['product_id']['product_price_start_all_net']!=0 and $product['product_id']['product_price_include_vat']==0) {
          $product_price_ekptosi_pososto=round($product_price_ekptosi_net*100/$product['product_id']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
          $product['product_id']['product_price_ekptosi_net']=$product['product_id']['product_price_start_all_net']-$product['product_id']['product_price_start_all_net'];
        } else if ($product['product_id']['product_price_start_all_total']!=0 and $product['product_id']['product_price_include_vat']!=0) {
          $product_price_ekptosi_pososto=round($product_price_ekptosi_total*100/$product['product_id']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
          $product['product_id']['product_price_ekptosi_net']=$product_price_ekptosi_total-$product['product_id']['product_price_start_all_total'];
        }
        
        $product['product_id']['product_price_ekptosi_pososto']=$product_price_ekptosi_pososto;
        
        //$product['product_id']['room_ajia_table']['roomaf_array']=json_decode(base64_decode($product['product_id']['room_ajia_table']['roomaf_array']),true);
        
        $value['pdata']=$product;
            
        break;
  
      }
    }
  }
  unset($value);
  

  //echo  $pliroteo;     
  //print_r($roolist);
  //print_r($room_product);
  //print_r($mybasketarray['products']);
  //echo time();die();
  
  $is_new_rec=false;
  if ($id <= 0) {
    $is_new_rec=true;

    $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira
    FROM gks_acc_journal 
    LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id
    WHERE gks_acc_journal.acc_eidos_parastatikou_id=1200
    AND gks_acc_journal.company_id=".$company_id."
    AND gks_acc_journal.company_sub_id=".$company_sub_id."
    AND gks_acc_journal.is_disable=0
    AND gks_acc_seires.is_disable=0
    ORDER BY gks_acc_journal.sortorder, gks_acc_seires.sortorder;";
    $result = $db_link->query($sql);        
    if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $id_acc_journal=$row['id_acc_journal'];
      $id_acc_seira=$row['id_acc_seira'];
    } else {
      $return['message']='Δεν βρέθηκε ημερολόγιο ή/και σειρά';debug_mail(false,$return['message'],$sql);return $return;
    }
    
    echo 'id_acc_journal:'.$id_acc_journal."\n";
    echo 'id_acc_seira:'.$id_acc_seira."\n";
    
    $reservation_guid=guid_for_reservation();
    $bank_deposit_9digit=gks_get_bank_deposit_9digit();
    $sql="insert into gks_hotel_reservation (
    reservation_guid,reservation_status,bank_deposit_9digit,
    user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
    crm_channel_id,crm_channel_code,
    reservation_journal_id,reservation_seira_id
    ) values (
    '".$db_link->escape_string($reservation_guid)."','010draft','".$db_link->escape_string($bank_deposit_9digit)."',
    ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
    21,'".$db_link->escape_string($data['reservation_id'])."',
    ".$id_acc_journal.",
    ".$id_acc_seira."
    )";
   
    $result = $db_link->query($sql);        
    if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
     
    $id = $db_link->insert_id;  
    
    $sxolio_log="Προσθήκη από booking (parse)"; 
    $sql="insert into gks_hotel_reservation_log (hotel_reservation_id, add_date,user_id,sxolio) values (
    ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
    $result = $db_link->query($sql);        
    if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
  }
  
  

  
  
  //num_child_kounies=0,
  //num_extra_beds=0,
  $sql="update gks_hotel_reservation set
  hotel_id=".$id_hotel.",
  reservation_date='".date('Y-m-d H:i:s',_time_user(strtotime($data['mydate_add']),-1))."',
  reservation_status='".$db_link->escape_string($data['reservation_status'])."',
  check_in='".$check_in."',
  check_out='".$check_out."',
  num_days=".$data['num_days'].",
  num_adults=".$data['visitors']['rnum_adults'].",
  num_childs=".$data['visitors']['rnum_childs'].",
  childs_ages_list='".$db_link->escape_string(json_encode($data['visitors']['rchilds_ages_list']))."',
  rooms_plithos=".$data['rooms_plithos'].",
  gks_price_total=".$data['gks_price_total'].",
  user_first_name='".$db_link->escape_string($dr_user_first_name)."',
  user_last_name='".$db_link->escape_string($dr_user_last_name)."',
  ma_country_id=".$data['ma_country_id'].",
  ma_odos='".$data['address']."',
  user_lang='".$data['user_lang']."',
  user_notes='".$db_link->escape_string($data['user_notes'])."',
  fiscal_position_id=".($data['ma_country_id']==91 ? '1' : ( $data['country_ee']!='' ? '2' : '3')).",
  pricelist_id=1,
  kostos_apostolis=0,
  kostos_pliromis=0,
  tropos_apostolis=1,
  tropos_pliromis=10,
  
  
  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."'
  where id_hotel_reservation=".$id;
  $result = $db_link->query($sql);
  if (!$result) {$return['message']='sql error';debug_mail(false,$return['message'],$sql);return $return;}
  
  
   
  echo 'id_hotel_reservation:'.$id."\n";
  print_r($roolist);
  
  $roolist_day=array();
  foreach ($roolist as &$myroom) {
    //echo '<pre>';var_dump($myroom);die();
//    //if ($myroom['add']==1 or $myroom['edit']==1 or $myroom['delete']==1) {
//      if ($myroom['delete'] == 1) {
//        if ($myroom['recid'] >0) {
//          $sql="delete from gks_hotel_reservation_room where id_hotel_reservation_room=".$myroom['recid']." and hotel_reservation_id=".$id." limit 1";
//          $result = $db_link->query($sql); 
//          if (!$result) {
//            debug_mail(false,'error sql',$sql);
//            $return = array('success' => false, 'message' => base64_encode('sql error'));
//            echo json_encode($return); die(); }          
//        }
//      } else if ($myroom['add'] == 1) {
        $sql="insert into gks_hotel_reservation_room (
        user_id_add,user_id_edit,mydate_add,mydate_edit,myip,
        hotel_reservation_id,hotel_room_id,rnum_adults,rnum_childs,rchilds_ages_list,
        rnum_child_kounies,rnum_extra_beds,
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
        room_ajia_table_array,
        
        product_withheldPercentCategory,
        product_withheldAmount,
        product_otherTaxesPercentCategory,
        product_otherTaxesAmount,
        product_stampDutyPercentCategory,
        product_stampDutyAmount,
        product_feesPercentCategory,
        product_feesAmount
  
        
        
        ) values (
        ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
        ".$id.",".$myroom['hotel_room_id'].",".$myroom['rnum_adults'].",".$myroom['rnum_childs'].",
        '".$db_link->escape_string($myroom['rchilds_ages_list'])."',
        ".$myroom['rnum_child_kounies'].",".$myroom['rnum_extra_beds'].",
        
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
        
        ".$myroom['pdata']['product_id']['id_product'].",
        ".$myroom['pdata']['product_id']['product_fpa_base_id'].",
        ".$myroom['pdata']['product_id']['product_fpa_id'].",
        ".number_format($myroom['pdata']['product_id']['product_fpa_id_array']['fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        '".$db_link->escape_string(json_encode($myroom['pdata']['product_id']['product_fpa_id_array']))."',
        ".$myroom['pdata']['product_id']['product_price_include_vat'].",
        ".number_format($myroom['pdata']['product_id']['product_price_start_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_start_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_start_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_start_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_start_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_final_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_ekptosi_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".number_format($myroom['pdata']['product_id']['product_price_ekptosi_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        ".$myroom['pdata']['product_id']['product_pricelist_item_id'].",
        ".number_format($myroom['pdata']['product_id']['product_pricelist_item_percent'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
        '".$db_link->escape_string($myroom['pdata']['product_id']['product_price_coupon_use'])."',
        ".intval($myroom['pdata']['product_id']['product_price_coupon_use_disabled']).",
        
        ".$data['num_days'].",
        '".$db_link->escape_string($myroom['pdata']['product_id']['room_ajia_table']['msg_price'])."',
        '".$db_link->escape_string($myroom['pdata']['product_id']['room_ajia_table']['roomaf_html'])."',
        '".$db_link->escape_string(base64_decode($myroom['pdata']['product_id']['room_ajia_table']['roomaf_array']))."',
        
        
        ".intval($myroom['pdata']['other_taxes']['withheldPercentCategory']).",
        ".floatval($myroom['pdata']['other_taxes']['withheldAmount']).",
        ".intval($myroom['pdata']['other_taxes']['otherTaxesPercentCategory']).",
        ".floatval($myroom['pdata']['other_taxes']['otherTaxesAmount']).",
        ".intval($myroom['pdata']['other_taxes']['stampDutyPercentCategory']).",
        ".floatval($myroom['pdata']['other_taxes']['stampDutyAmount']).",
        ".intval($myroom['pdata']['other_taxes']['feesPercentCategory']).",
        ".floatval($myroom['pdata']['other_taxes']['feesAmount'])."
  
  
        
        
        )";
        //echo $sql;die();
        $result = $db_link->query($sql); 
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }          
        
        $myroom['recid'] = $db_link->insert_id;    
        
//      } else if ($myroom['recid']>0) { //($myroom['edit'] == 1) {
//        
//        //print '<pre>';print_r($myroom['pdata']); die();
//        
//        $sql="update gks_hotel_reservation_room set 
//        user_id_edit=".$my_wp_user_id.",
//        mydate_edit=now(),
//        myip='".$db_link->escape_string($gkIP)."',
//        hotel_room_id=".$myroom['hotel_room_id'].",
//        rnum_adults=".$myroom['rnum_adults'].",
//        rnum_childs=".$myroom['rnum_childs'].",
//        rchilds_ages_list='".$db_link->escape_string($myroom['rchilds_ages_list'])."',
//        rnum_child_kounies=".$myroom['rnum_child_kounies'].",
//        rnum_extra_beds=".$myroom['rnum_extra_beds'].",
//        
//        ruser_id=".$myroom['ruser_id'].",
//        ruser_lang='".$db_link->escape_string($myroom['ruser_lang'])."',
//        ruser_first_name='".$db_link->escape_string($myroom['ruser_first_name'])."',
//        ruser_last_name='".$db_link->escape_string($myroom['ruser_last_name'])."',
//        ruser_email='".$db_link->escape_string($myroom['ruser_email'])."',
//        ruser_mobile='".$db_link->escape_string($myroom['ruser_mobile'])."',
//        ruser_ma_odos='".$db_link->escape_string($myroom['ruser_ma_odos'])."',
//        ruser_ma_perioxi='".$db_link->escape_string($myroom['ruser_ma_perioxi'])."',
//        ruser_ma_poli='".$db_link->escape_string($myroom['ruser_ma_poli'])."',
//        ruser_ma_tk='".$db_link->escape_string($myroom['ruser_ma_tk'])."',
//        ruser_ma_country_id=".$myroom['ruser_ma_country_id'].",
//        ruser_ma_nomos_id=".$myroom['ruser_ma_nomos_id'].",
//        rsxolio='".$db_link->escape_string($myroom['rsxolio'])."',
//        ruser_fiscal_position_id=".$myroom['ruser_fiscal_position_id'].",
//        ruser_pricelist_id=".$myroom['ruser_pricelist_id'].",
//        
//        
//        product_id=".$myroom['pdata']['product_id'].",
//        product_fpa_base_id=".$myroom['pdata']['product_fpa_base_id'].",
//        product_fpa_id=".$myroom['pdata']['product_fpa_id'].",
//        product_fpa_pososto=".number_format($myroom['pdata']['product_fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//  
//        product_fpa_id_json='".$db_link->escape_string(json_encode($myroom['pdata']['product_fpa_id_json']))."',
//        product_price_include_vat=".$myroom['pdata']['product_price_include_vat'].",
//        product_price_start_peritem_db=".number_format($myroom['pdata']['product_price_start_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_start_peritem_net=".number_format($myroom['pdata']['product_price_start_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_start_peritem_fpa=".number_format($myroom['pdata']['product_price_start_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_start_peritem_total=".number_format($myroom['pdata']['product_price_start_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_start_all_net=".number_format($myroom['pdata']['product_price_start_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_start_all_fpa=".number_format($myroom['pdata']['product_price_start_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_start_all_total=".number_format($myroom['pdata']['product_price_start_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_final_peritem_db=".number_format($myroom['pdata']['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_final_peritem_net=".number_format($myroom['pdata']['product_price_final_peritem_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_final_peritem_fpa=".number_format($myroom['pdata']['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_final_peritem_total=".number_format($myroom['pdata']['product_price_final_peritem_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_final_all_net=".number_format($myroom['pdata']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_final_all_fpa=".number_format($myroom['pdata']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_final_all_total=".number_format($myroom['pdata']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_ekptosi_net=".number_format($myroom['pdata']['product_price_ekptosi_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_ekptosi_pososto=".number_format($myroom['pdata']['product_price_ekptosi_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_pricelist_item_id=".$myroom['pdata']['product_pricelist_item_id'].",
//        product_pricelist_item_percent=".number_format($myroom['pdata']['product_pricelist_item_percent'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').",
//        product_price_coupon_use='".$db_link->escape_string($myroom['pdata']['product_price_coupon_use'])."',
//        product_price_coupon_use_disabled=".$myroom['pdata']['product_price_coupon_use_disabled'].",
//        
//        product_quantity=".$num_days.",
//  
//        room_ajia_table_math='".$db_link->escape_string($myroom['pdata']['ajia_table_math'])."',
//        room_ajia_table_html='".$db_link->escape_string($myroom['pdata']['ajia_table_html'])."',
//        room_ajia_table_array='".$db_link->escape_string($myroom['pdata']['ajia_table_array'])."',
//        
//        product_withheldPercentCategory=".intval($myroom['pdata']['other_taxes']['withheldPercentCategory']).",
//        product_withheldAmount=".floatval($myroom['pdata']['other_taxes']['withheldAmount']).",
//        product_otherTaxesPercentCategory=".intval($myroom['pdata']['other_taxes']['otherTaxesPercentCategory']).",
//        product_otherTaxesAmount=".floatval($myroom['pdata']['other_taxes']['otherTaxesAmount']).",
//        product_stampDutyPercentCategory=".intval($myroom['pdata']['other_taxes']['stampDutyPercentCategory']).",
//        product_stampDutyAmount=".floatval($myroom['pdata']['other_taxes']['stampDutyAmount']).",
//        product_feesPercentCategory=".intval($myroom['pdata']['other_taxes']['feesPercentCategory']).",
//        product_feesAmount=".floatval($myroom['pdata']['other_taxes']['feesAmount'])."
//  
//        
//        where id_hotel_reservation_room=".$myroom['recid']." and hotel_reservation_id=".$id." limit 1";
//        //echo 'ddddddd';
//        //die();
//        $result = $db_link->query($sql); 
//        if (!$result) {
//          debug_mail(false,'error sql',$sql);
//          $return = array('success' => false, 'message' => base64_encode('sql error'));
//          echo json_encode($return); die(); }         
//        
//      
//      }
    //}
    $hotel_type_room_id=0;
    if (isset($roomcheck2[$myroom['hotel_room_id']])) {
      $hotel_type_room_id=$roomcheck2[$myroom['hotel_room_id']]['hotel_room_type_id'];
    }
    
    $roolist_day[]=array('delete'=>0, 'hotel_room_id'=> $myroom['hotel_room_id'], 'recid'=> $myroom['recid'], 'hotel_type_room_id'=>$hotel_type_room_id);
  }
  unset($myroom);
  
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
    echo json_encode($return); die(); }
  
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
    echo json_encode($return); die(); }
  
  
  $gks_price_original_net=0;
  $gks_price_net=0;
  $gks_price_fpa=0;
  $gks_price_netfpa=0;
  $gks_price_total=0;
  
  $totalWithheldAmount=0;
  $totalOtherTaxesAmount=0;
  $totalStampDutyamount=0;
  $totalFeesAmount=0;
  
  foreach ($roolist as $myroom) {
    if ($myroom['delete'] == 0) {
      $gks_price_original_net+=$myroom['pdata']['product_id']['product_price_start_all_net'];
      $gks_price_net+=$myroom['pdata']['product_id']['product_price_final_all_net'];
      $gks_price_fpa+=$myroom['pdata']['product_id']['product_price_final_all_fpa'];
      $gks_price_netfpa+=$myroom['pdata']['product_id']['product_price_final_all_net']+$myroom['pdata']['product_id']['product_price_final_all_fpa'];
      $gks_price_total+=$myroom['pdata']['product_id']['product_price_final_all_total'];
  
  
  
      $totalWithheldAmount+=$myroom['pdata']['other_taxes']['withheldAmount'];
      $totalOtherTaxesAmount+=$myroom['pdata']['other_taxes']['otherTaxesAmount'];
      $totalStampDutyamount+=$myroom['pdata']['other_taxes']['stampDutyAmount'];
      $totalFeesAmount+=$myroom['pdata']['other_taxes']['feesAmount'];
  
  
      
    }
  }
  
  $totalDeductionsAmount=0;
  
  $gks_price_total=
     $gks_price_net 
      + $gks_price_fpa
      - $totalWithheldAmount
      + $totalOtherTaxesAmount
      + $totalStampDutyamount
      + $totalFeesAmount
      - $totalDeductionsAmount;
      
  
  $sql="update gks_hotel_reservation set 
  
  products_posotita=".($data['num_days']*count($roolist)).",
  
  
  products_need_pliromi=".($gks_price_total==0 ? '0':'1').",
  
  
  gks_price_original_net=".number_format($gks_price_original_net, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
  gks_price_net=".number_format($gks_price_net, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
  gks_price_fpa=".number_format($gks_price_fpa, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
  gks_price_netfpa=".number_format($gks_price_netfpa, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').", 
  gks_price_total=".number_format($gks_price_total, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').",
  
  totalWithheldAmount=".number_format($totalWithheldAmount, 10, '.', '').", 
  totalOtherTaxesAmount=".number_format($totalOtherTaxesAmount, 10, '.', '').", 
  totalStampDutyamount=".number_format($totalStampDutyamount, 10, '.', '').", 
  totalFeesAmount=".number_format($totalFeesAmount, 10, '.', '').", 
  

  user_id_edit=".$my_wp_user_id.",
  mydate_edit=now(),
  myip='".$db_link->escape_string($gkIP)."'
  where id_hotel_reservation = ".$id." limit 1";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  //print '<pre>|'.$id.'|'.$reservation_status."\r\n|".$reservation_status_old."\r\n"; print_r($roolist_day);print_r($days_round);die();
    
  gks_hotel_reservation_room_day_recs($id,$roolist_day,
    $data['reservation_status'],
    $days_round['check_in_round_time'],$days_round['check_out_round_time']
  );  
  print_r($rsv_data);
  
  $return['success']=true;
  $return['message']=base64_encode('OK');
  
  return $return;
}

function gks_hotel_reservation_parse_booking_email($read_file) {
  global $db_link;
  
  $rsv_data=array();
  $rsv_data['success']=false;
  $rsv_data['message']='get reservation generic error';
  $rsv_data['errors']=array();
  $rsv_data['data']=array(
    'hotel_id' => '',
    'reservation_status' => '',
    'check_in' => '',
    'check_in_orig' => '',
    'check_out' => '',
    'check_out_orig' => '',
    'num_days' => 0,
    'num_days_orig' => '',
    'visitors' => array(
      'rnum_adults'=>0,
      'rnum_childs'=>0,
      'rchilds_ages_list'=>array(),
    ),
    'visitors_orig' => '',
    'rooms_plithos' => 0,
    'rooms_plithos_orig' => '',
    'gks_price_total' => 0,
    'gks_price_total_orig' => '',
    'fullname' => '',
    'ma_country_id' => 0,
    'ma_country_id_orig' => '',
    'country_ee' => '',
    'address' => '',
    'user_lang' => '',
    'user_lang_orig' => '',
    'channel' => '',
    'IATA_TIDS' => '',
    'reservation_id' => '',
    'price_for_promithia' => 0,
    'price_for_promithia_orig' => '',
    'mydate_add' => '',
    'mydate_add_orig' => '',
    'promithia' => 0,
    'promithia_orig' => '',
    'user_notes' => '',
    'payment_poso' => 0,
    'payment_poso_orig' => '',
    'rooms'=> array(),
  );
              
  if (file_exists($read_file)==false) {
    $rsv_data['message']='file not found: '.mb_basename($read_file); 
    $rsv_data['errors'][]=$rsv_data['message'];
    debug_mail(false,$rsv_data['message'],$read_file); return $rsv_data;}
  
    
  $html=file_get_contents($read_file);
  
  $doc = new DOMDocument();
  $doc->preserveWhiteSpace = false;
  
  libxml_use_internal_errors(true);
  $doc->loadHTML('<?xml encoding="UTF-8">'.$html);
  //echo $doc->saveHTML();
  $xpath = new DOMXpath($doc);
  
  
  //$elements = $xpath->query("*/div[@id='main-container']/div[@class='page-body']/");
  //$elements = $xpath->query("*/div[@id='main-container']/main/div/div/main/div/div/div/div/div/div[@class='res-reservation-overview bui-panel']");
  //$elements = $xpath->query("//div[@class='res-reservation-overview bui-panel']");
  $elements = $xpath->query("//div[contains(@class, 'res-reservation-overview')]/div[@class='bui-grid']");
  
  
  
  if ($elements===false or $elements->length==0) {
    $rsv_data['message']='parse error (1)'; 
    $rsv_data['errors'][]='xpath not found: "//div[contains(@class, \'res-reservation-overview\')]/div[@class=\'bui-grid\']"';
    debug_mail(false,$rsv_data['message'],$read_file); return $rsv_data;}
  
  
  $payment_poso = $xpath->query("//div[@class='res-vcc-wrapper']/div[@role='status']/div[@class='bui-alert__description']/p[@class='bui-alert__text']/span");
  if ($payment_poso!==false and $payment_poso->length >= 1) {
    $rsv_data['data']['payment_poso_orig']=gks_hotel_reservation_parse_text($payment_poso[0]->textContent);
    $temp=$rsv_data['data']['payment_poso_orig'];
    if (substr($temp, 0, 60)=='You have successfully charged the total amount on this card:') {
      $temp=str_replace('You have successfully charged the total amount on this card:', '', $temp);
      $temp=trim_gks(str_replace('€','',$temp));
      $rsv_data['data']['payment_poso']=gks_hotel_reservation_parse_float($temp,$rsv_data['errors']);
      if ($rsv_data['data']['payment_poso']>0) $rsv_data['data']['reservation_status']='080confirm';
    }
    
    if (mb_substr($temp, 0, 22)=='Έχετε χρεώσει επιτυχώς' and strpos($temp,'συνολικά σε αυτήν την κάρτα')!==false) {
      $temp=str_replace('Έχετε χρεώσει επιτυχώς', '', $temp);
      $temp=str_replace('συνολικά σε αυτήν την κάρτα:', '', $temp);
      $temp=str_replace(':', '', $temp);
      $temp=trim_gks(str_replace('€','',$temp));
      $rsv_data['data']['payment_poso']=gks_hotel_reservation_parse_float($temp,$rsv_data['errors']);
      if ($rsv_data['data']['payment_poso']>0) $rsv_data['data']['reservation_status']='080confirm';
    }
  }
  
  $logo_link = $xpath->query("//section[@class='ext-header__logo-container']/a");
  if ($logo_link!==false and $logo_link->length >= 1) {

    $attr=trim_gks($logo_link[0]->getAttribute('href'));
    $myurl=parse_url($attr);
    if (isset($myurl['host']) and $myurl['host']=='admin.booking.com' and isset($myurl['query']) and $myurl['query']!='') {
      $output=false;
      parse_str($myurl['query'], $output);
      if (is_array($output)) {
        if (isset($output['hotel_id'])) {
          $temp=intval($output['hotel_id']);
          if ($temp>0) {
            if (strpos($html,'&hotel_id='.$temp.'&')!==false) {
              $rsv_data['data']['hotel_id']=trim_gks($temp.'');
            }
          }
        }
      }
    }
    //var_dump($myurl);
    //die();
  }
  
  //echo $elements->length."\n";
  $bui_grid=$elements[0];
  //var_dump($bui_grid);
  $columns = $bui_grid->childNodes;
  //var_dump($columns);
  
  $node_column1=false;
  $node_column2=false;
  $cc=0;
  foreach ($columns as $column) {
    //echo $column->nodeValue. "\n";
    $textContent=trim_gks($column->textContent);
    if ($textContent!='') {
      $cc++;
      //echo $cc."\n";
      if ($cc==1) $node_column1=$column;
      if ($cc==2) $node_column2=$column;
    }
  }
  if ($node_column1!==false) {
    $node_myps = $node_column1->getElementsByTagName('p');
    //var_dump($node_myps);
    $cc=0;
    $prev_text='';
    foreach ($node_myps as $myp) {
      $cc++;
      $curr_text=trim_gks($myp->textContent);
      
      //echo $curr_text."\n";
      if ($prev_text=='check-in') {$rsv_data['data']['check_in_orig']=$curr_text; $rsv_data['data']['check_in']=gks_hotel_reservation_parse_date($curr_text,$rsv_data['errors']);}
      else if ($prev_text=='check-out') {$rsv_data['data']['check_out_orig']=$curr_text; $rsv_data['data']['check_out']=gks_hotel_reservation_parse_date($curr_text,$rsv_data['errors']);}
      else if ($prev_text=='διάρκειαδιαμονής' or $prev_text=='lengthofstay') 
        $rsv_data['data']['num_days_orig']=gks_hotel_reservation_parse_text($curr_text);
      else if ($prev_text=='σύνολοεπισκεπτών' or $prev_text=='totalguests') 
        {$rsv_data['data']['visitors_orig']=gks_hotel_reservation_parse_text($curr_text);$rsv_data['data']['visitors']=gks_hotel_reservation_parse_visitors($curr_text,$rsv_data['errors']);}
      else if ($prev_text=='συνολικόςαριθμόςμονάδων' or $prev_text=='totalunits') 
        $rsv_data['data']['rooms_plithos_orig']=gks_hotel_reservation_parse_text($curr_text);
      else if ($prev_text=='συνολικήτιμή' or $prev_text=='totalprice') 
        {$rsv_data['data']['gks_price_total_orig']=$curr_text; $rsv_data['data']['gks_price_total']=gks_hotel_reservation_parse_float($curr_text,$rsv_data['errors']);}
      
      $prev_text=$curr_text;
      $prev_text=mb_strtolower($prev_text);
      $prev_text=str_replace(' ','',$prev_text);
      $prev_text=str_replace(':','',$prev_text);
      //echo $prev_text."\n";
    }

  }
  
  if ($node_column2!==false) {
    //var_dump($node_column2);
    $node_address = $node_column2->getElementsByTagName('address');
    
    if ($node_address->length==1) {
      $node_address=$node_address[0];
      //var_dump($node_address);
      $node_spans=$node_address->getElementsByTagName('span');
      foreach ($node_spans as $myspan) {
        $attr=trim_gks($myspan->getAttribute('data-test-id'));
        if ($attr=='reservation-overview-name') {
          $rsv_data['data']['fullname']=gks_hotel_reservation_parse_text($myspan->textContent);
        }
        $class=trim_gks($myspan->getAttribute('class'));
        if ($class=='bui-flag__text') {
          $rsv_data['data']['ma_country_id_orig']=gks_hotel_reservation_parse_text($myspan->textContent);
        }
        //var_dump($attr);
      } 
      //var_dump($node_spans);
      
      $elements = $xpath->query("span",$node_address);
      if ($elements!==false and $elements->length==1) {
        $rsv_data['data']['address']=gks_hotel_reservation_parse_text($elements[0]->textContent);
      }
      //var_dump($elements[0]->textContent);
      
    }
    
    $elements = $xpath->query("./div/div/div",$node_column2);
    foreach ($elements as $mydiv) {

      $node_myps = $mydiv->getElementsByTagName('p');
      //var_dump($node_myps);
      $cc=0;
      $prev_text='';
      foreach ($node_myps as $myp) {
        $cc++;
        $curr_text=trim_gks($myp->textContent);
        
        //echo $curr_text."\n";
        if ($prev_text=='γλώσσαπροτίμησης' or $prev_text=='preferredlanguage') 
          $rsv_data['data']['user_lang_orig']=gks_hotel_reservation_parse_text($curr_text);
        else if ($prev_text=='κανάλι' or $prev_text=='channel') 
          $rsv_data['data']['channel']=gks_hotel_reservation_parse_text($curr_text);
        else if ($prev_text=='κωδικόςiata/tids' or $prev_text=='iata/tidscode') 
          $rsv_data['data']['IATA_TIDS']=gks_hotel_reservation_parse_text($curr_text);
        else if ($prev_text=='αριθμόςκράτησης' or $prev_text=='bookingnumber') 
          $rsv_data['data']['reservation_id']=gks_hotel_reservation_parse_text($curr_text);
        else if ($prev_text=='ποσόστοοποίουπολογίζεταιπρομήθεια' or $prev_text=='commissionableamount') 
          {$rsv_data['data']['price_for_promithia_orig']=$curr_text; $rsv_data['data']['price_for_promithia']=gks_hotel_reservation_parse_float($curr_text,$rsv_data['errors']);}
        else if ($prev_text=='ελήφθη' or $prev_text=='received') 
          {$rsv_data['data']['mydate_add_orig']=$curr_text; $rsv_data['data']['mydate_add']=gks_hotel_reservation_parse_date($curr_text,$rsv_data['errors']);}
        else if ($prev_text=='προμήθεια' or $prev_text=='commission') 
          {$rsv_data['data']['promithia_orig']=$curr_text; $rsv_data['data']['promithia']=gks_hotel_reservation_parse_float($curr_text,$rsv_data['errors']);}
        else if ($prev_text=='σημειώσεις(εσωτερικήχρήσημόνο)' or $prev_text=='notepad(internalonly)') 
          $rsv_data['data']['sxolio']=gks_hotel_reservation_parse_text($curr_text);
        else if ($prev_text=='σημαντικήπληροφορίαγιααυτότονεπισκέπτη' or $prev_text=='importantinformationaboutthisguest') 
          $rsv_data['data']['user_notes']=gks_hotel_reservation_parse_text($curr_text);
        
        
        $prev_text=$curr_text;
        $prev_text=mb_strtolower($prev_text);
        $prev_text=str_replace(' ','',$prev_text);
        $prev_text=str_replace(':','',$prev_text);
        //echo $prev_text."\n";
      }
            
    }
    //var_dump($elements);
    //var_dump($elements[0]->textContent);
  }
  

  
  $element_rooms = $xpath->query("//div[contains(@class, 'res-room-block__wrapper')]");
  if ($elements!==false and $elements->length>=1) {
    foreach ($element_rooms as $elem_room) {
      $data_room=array(
        'r_reservation_status' => '',
        'number' => '',
        'name' => '',
        'name_orig' => '', 
        'destructive'  => '',
        'outline' => '',
        'price_with_efd' => 0,
        'price_with_efd_orig' => '',
        'price' => 0,
        'price_orig' => '',
        'check_in' => '',
        'check_in_orig' => '',
        'check_out' => '',
        'check_out_orig' => '',
        'fullname' => '',
        'visitors' => array(
          'rnum_adults'=>0,
          'rnum_childs'=>0,
          'rchilds_ages_list'=>array(),
        ),
        'visitors_orig' => '',
        'visitors_max' => array(
          'adults'=>0,
          'childs'=>0,
          'max' => 0,
        ),
        'visitors_max_orig' => '',
        'photo' => '',
      );
      
      $node_mydivs = $elem_room->getElementsByTagName('div');
      foreach ($node_mydivs as $mydiv) {
        $attr=trim_gks($mydiv->getAttribute('class'));
        if (strpos($attr, 'res-room-title__name')!==false) {
          $data_room['name_orig']=gks_hotel_reservation_parse_text($mydiv->textContent);
          //var_dump($mydiv);//die();
          $node_myspans = $mydiv->getElementsByTagName('span');
          
          foreach ($node_myspans as $myspan) {
            $attr=trim_gks($myspan->getAttribute('class'));
            if (strpos($attr, 'res-room-title__number')!==false) {
              $data_room['number']=gks_hotel_reservation_parse_text($myspan->textContent);
            }
            
            
            if (strpos($attr, 'bui-badge--destructive')!==false) {
              $data_room['destructive']=gks_hotel_reservation_parse_text($myspan->textContent);
              
            }
            if (strpos($attr, 'bui-badge--outline')!==false) {
              $data_room['outline']=gks_hotel_reservation_parse_text($myspan->textContent);
            }
            
          }
          
        }

        if (strpos($attr, 'bui-price-display__value')!==false) {
          $data_room['price_with_efd_orig']=$mydiv->textContent;
          $data_room['price_with_efd']=gks_hotel_reservation_parse_float($mydiv->textContent,$rsv_data['errors']);
        }
      }
      
      $node_myps = $elem_room->getElementsByTagName('p');
      foreach ($node_myps as $myp) {
        $attr=trim_gks($myp->getAttribute('class'));
        //echo $myp->textContent."\n";//die();
        if (strpos($attr, 'bui-accordion__subtitle')!==false) {
          $node_myspans = $myp->getElementsByTagName('span');
          $cc=0;
          foreach ($node_myspans as $myp) {
            $attr=trim_gks($myp->getAttribute('class'));
            if (strpos($attr, 'res-room-subtitle__item')!==false) {
              $cc++;
              if ($cc==1) {$data_room['check_in_orig']=$myp->textContent; $data_room['check_in']=gks_hotel_reservation_parse_date($myp->textContent,$rsv_data['errors']); }
              if ($cc==2) {$data_room['check_out_orig']=$myp->textContent; $data_room['check_out']=gks_hotel_reservation_parse_date($myp->textContent,$rsv_data['errors']); }
            }
          }
          //var_dump($myp);
        }
      }
      
      $node_mydivs = $elem_room->getElementsByTagName('div');
      foreach ($node_mydivs as $mydiv) {
        $attr=trim_gks($mydiv->getAttribute('class'));
        if (strpos($attr, 'bui-accordion__content')!==false) {
          //echo $mydiv->textContent;//die();
          
          $node_column1=false;
          $node_column2=false;          
          $node_mydivs2 = $mydiv->getElementsByTagName('div');
          $cc=0;
          foreach ($node_mydivs2 as $mydiv2) {
            $attr=trim_gks($mydiv2->getAttribute('class'));
            if (strpos($attr, 'bui-grid__column-full')!==false) {
              $cc++;
              if ($cc==1) $node_column1=$mydiv2;
              if ($cc==2) $node_column2=$mydiv2;
            }
          }
          
          if ($node_column1!==false) {
            //var_dump($node_column1);
            $node_myps = $node_column1->getElementsByTagName('p');
            //var_dump($node_myps);
            $cc=0;
            $prev_text='';
            foreach ($node_myps as $myp) {
              $cc++;
              $curr_text=trim_gks($myp->textContent);
              //echo $curr_text."\n";
              if ($prev_text=='όνομαεπισκέπτη' or $prev_text=='guestname') 
                $data_room['fullname']=gks_hotel_reservation_parse_text($curr_text);
              else if ($prev_text=='αριθμόςεπισκεπτών' or $prev_text=='bookedoccupancy') 
                {$data_room['visitors_orig']=gks_hotel_reservation_parse_text($curr_text); $data_room['visitors']=gks_hotel_reservation_parse_visitors($curr_text,$rsv_data['errors']);}
              else if ($prev_text=='μέγιστοςαριθμόςατόμων' or $prev_text=='maxoccupancy') 
                {$data_room['visitors_max_orig']=gks_hotel_reservation_parse_text($curr_text);$data_room['visitors_max']=gks_hotel_reservation_parse_visitors_max($curr_text,$rsv_data['errors']);}
              else if ($prev_text=='φωτογραφίαδωματίου' or $prev_text=='roomphoto') {
                $myimg=$myp->getElementsByTagName('img');
                foreach ($myimg as $myimg) {
                  $data_room['photo']=trim_gks($myimg->getAttribute('src'));
                }
              }
              $prev_text=$curr_text;
              $prev_text=mb_strtolower($prev_text);
              $prev_text=str_replace(' ','',$prev_text);
              $prev_text=str_replace(':','',$prev_text);
              //echo $prev_text."\n";
            }
          }
          
          if ($node_column2!==false) {
            //var_dump($node_column2);
            $node_mytrs = $node_column2->getElementsByTagName('tr');
            foreach ($node_mytrs as $mytr) {
              $attr=trim_gks($mytr->getAttribute('class'));
              if (strpos($attr, 'res-room-row-subtotal')!==false) {
                $node_mytds = $mytr->getElementsByTagName('td');
                foreach ($node_mytds as $mytd) {
                  //var_dump($mytd);die();
                  $attr=trim_gks($mytd->getAttribute('class'));
                  //echo $attr;
                  if (strpos($attr, 'bui-table__cell--align-end')!==false) {
                    $data_room['price_orig']=$mytd->textContent; 
                    $data_room['price']=gks_hotel_reservation_parse_float($mytd->textContent,$rsv_data['errors']);//die();
                  }
                  
                }
                //echo $mytr->textContent;//die();
                
              }
              
            }
            

          }          
          
        }
        
      }
      
      $data_room['name']=gks_hotel_reservation_parse_text($data_room['name_orig']);
      
      if ($data_room['number']!= '' and strlen($data_room['name']) > strlen($data_room['number'])) {
        $data_room['name']=substr($data_room['name'], strlen($data_room['number']));
        $data_room['name']=gks_hotel_reservation_parse_text($data_room['name']);
      }
      if ($data_room['outline']!='') $data_room['name']=trim_gks(str_replace($data_room['outline'], '', $data_room['name']));
      if ($data_room['destructive']!='') {
        $data_room['name']=trim_gks(str_replace($data_room['destructive'], '', $data_room['name']));
        if ($data_room['destructive']=='Ακυρώθηκε από τον επισκέπτη' or 
           $data_room['destructive']=='Μη εμφάνιση επισκέπτη/-των' or 
           $data_room['destructive']=='Cancelled by guest') {
          $data_room['r_reservation_status'] = '040cancelled';
        }
      }
      
      
      
      //die();
      $rsv_data['data']['rooms'][]=$data_room;
    } 
    //var_dump($elements);
  }

  //num_days_orig
  if ($rsv_data['data']['num_days_orig']!='') {
    $temp=$rsv_data['data']['num_days_orig'];
    $temp=str_replace('βράδια','',$temp);
    $temp=str_replace('βράδυ','',$temp);
    $temp=str_replace('overnights','',$temp);
    $temp=str_replace('overnight','',$temp);
    $temp=str_replace('nights','',$temp);
    $temp=str_replace('night','',$temp);
    $temp=trim_gks($temp);
    $rsv_data['data']['num_days']=intval($temp);
  }
  //if ($rsv_data['data']['num_days_orig']!='' and $rsv_data['data']['num_days']==0) $rsv_data['errors'][]='error parsing num_days: '.$rsv_data['data']['num_days_orig'].' to: '.$rsv_data['data']['num_days'];    
  if ($rsv_data['data']['num_days']<1) $rsv_data['errors'][]='error parsing num_days: '.$rsv_data['data']['num_days_orig'].' to: '.$rsv_data['data']['num_days'];    

  

  //ma_country_id_orig 
  if ($rsv_data['data']['ma_country_id_orig']!='') {
    $temp=$rsv_data['data']['ma_country_id_orig'];
    $sql="select id_country,country_ee from gks_country 
    where country_name like '".$db_link->escape_string($temp)."' 
    or id_country in (
      select country_id from gks_country_lang 
      where country_name like '".$db_link->escape_string($temp)."'
      and lang_code='en-US'
    )";
    
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $rsv_data['errors'][]='error sql';
    } else {
      if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
        $rsv_data['data']['ma_country_id']=intval($row['id_country']);
        $rsv_data['data']['country_ee']=trim_gks($row['country_ee']);
      }
    }
    
    if ($rsv_data['data']['ma_country_id']==0) {
      if ($temp== 'Ηνωμένες Πολιτείες') $rsv_data['data']['ma_country_id']=234;
    }
    
    
    if ($rsv_data['data']['ma_country_id']==0) {
      if ($temp!='') $rsv_data['errors'][]='error parsing ma_country_id_orig: '.$temp;
    }
  }
  
  
  
  //user_lang_orig
  if ($rsv_data['data']['user_lang_orig']!='') {
    $temp=$rsv_data['data']['user_lang_orig'];
    $sql="select id_lang 
    from (gks_lang 
    LEFT JOIN (
      SELECT lang_idd, lang_name as lang_name_en_US FROM gks_lang_lang WHERE lang_code='en-US'
    ) AS gks_lang_en_US ON gks_lang.idd_lang = gks_lang_en_US.lang_idd)
    
    where lang_name like '".$db_link->escape_string($temp)."' or lang_name_en_US like '".$db_link->escape_string($temp)."'";  
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $rsv_data['errors'][]='error sql';
    } else {
      if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
        $rsv_data['data']['user_lang']=$row['id_lang'];
      }
    }
    if ($rsv_data['data']['user_lang']=='') {
      if ($temp== 'Αγγλικά (Η.Π.Α.)') $rsv_data['data']['user_lang']='en-US';
      if ($temp== 'Αγγλικά (Η.Β.)') $rsv_data['data']['user_lang']='en-US';
      if ($temp== 'Ρουμανικά') $rsv_data['data']['user_lang']='en-US';

      if ($temp== 'English (US)') $rsv_data['data']['user_lang']='en-US';
      if ($temp== 'English (UK)') $rsv_data['data']['user_lang']='en-US';
      if ($temp== 'Romanian') $rsv_data['data']['user_lang']='en-US';
      
      
    }
    
    if ($rsv_data['data']['user_lang']=='') {
      if ($temp!='') $rsv_data['errors'][]='error parsing user_lang_orig: '.$temp;
    }
  }
  
  if (count($rsv_data['data']['rooms'])==1 and $rsv_data['data']['rooms'][0]['visitors_orig']=='') {
    $rsv_data['data']['rooms'][0]['visitors']=$rsv_data['data']['visitors'];
  }
  
  $num_adults=0;
  $num_childs=0;
  $gks_price_total=0;
  $price_with_efd=0;
  $rs040cancelled=0;
  foreach ($rsv_data['data']['rooms'] as &$myroom) {
    if ($myroom['visitors']['rnum_adults']==0 and $myroom['visitors']['rnum_childs']==0 and count($myroom['visitors']['rchilds_ages_list'])==0 and
        $myroom['visitors_max']['adults']>0) {
      $myroom['visitors']['rnum_adults'] = $myroom['visitors_max']['adults'];
    }
    
    
    if ($myroom['r_reservation_status'] == '040cancelled') {
      $rs040cancelled++;
    } else {
      $gks_price_total+=$myroom['price'];
      $price_with_efd+=$myroom['price_with_efd'];
    } 
    $num_adults+=$myroom['visitors']['rnum_adults'];
    $num_childs+=$myroom['visitors']['rnum_childs'];
  }
  unset($myroom);
  
  if ($rs040cancelled==count($rsv_data['data']['rooms'])) {
    $rsv_data['data']['reservation_status']='040cancelled';
  }
  if ($rsv_data['data']['reservation_status']=='') {
    $rsv_data['data']['reservation_status']='070wait_payment'; 
  }
    
  if ($rsv_data['data']['hotel_id']=='') {
    $rsv_data['errors'][]='error parsing hotel_id';
  }
  if ($rsv_data['data']['reservation_id']=='') {
    $rsv_data['errors'][]='error parsing reservation_id';
  }

  //rooms_plithos_orig
  $rsv_data['data']['rooms_plithos']=intval($rsv_data['data']['rooms_plithos_orig']);
  if ($rsv_data['data']['reservation_status']!='040cancelled' and $rsv_data['data']['rooms_plithos']<1) {
    $rsv_data['errors'][]='error parsing rooms_plithos: '.$rsv_data['data']['rooms_plithos_orig'].' to: '.$rsv_data['data']['rooms_plithos'];    
  }
  
  
  if ($rsv_data['data']['reservation_status']!='040cancelled' and $num_adults!=$rsv_data['data']['visitors']['rnum_adults']) {
    $rsv_data['errors'][]='error num_adults<>data_visitors_rnum_adults: '.$num_adults.'|'.$rsv_data['data']['visitors']['rnum_adults'];
  }
  if ($rsv_data['data']['reservation_status']!='040cancelled' and $num_childs!=$rsv_data['data']['visitors']['rnum_childs']) {
    $rsv_data['errors'][]='error num_childs<>data_visitors_rnum_childs: '.$num_childs.'|'.$rsv_data['data']['visitors']['rnum_childs'];
  }
  if ($rsv_data['data']['reservation_status']!='040cancelled' and $gks_price_total!=$rsv_data['data']['price_for_promithia']) {
    $rsv_data['errors'][]='error gks_price_total<>data_price_for_promithia: '.$gks_price_total.'|'.$rsv_data['data']['price_for_promithia'];
  }
  if ($rsv_data['data']['reservation_status']!='040cancelled' and $price_with_efd!=$rsv_data['data']['gks_price_total']) {
    $rsv_data['errors'][]='error price_with_efd<>data_gks_price_total: '.$price_with_efd.'|'.$rsv_data['data']['gks_price_total'];
  }
  
  if ($rsv_data['data']['reservation_status']!='040cancelled' and $rsv_data['data']['gks_price_total']<=0) {
    $rsv_data['errors'][]='error gks_price_total<=0:'.$rsv_data['data']['gks_price_total'];
  }
  if ($rsv_data['data']['check_in']=='' or  $rsv_data['data']['check_out']=='') {
    $rsv_data['errors'][]='error check_in is empty';
  }
  if ($rsv_data['data']['check_out']=='') {
    $rsv_data['errors'][]='error check_out is empty';
  }
  if ($rsv_data['data']['check_in']!='' and $rsv_data['data']['check_out']!='') {
    $diastima=strtotime($rsv_data['data']['check_out']) - strtotime($rsv_data['data']['check_in']);
    $diafora = ($rsv_data['data']['num_days']*24*60*60)-$diastima;
    if ($diafora!=0) {
      $rsv_data['errors'][]='error diafora is not zero: '.$rsv_data['data']['check_in'].'|'.$rsv_data['data']['check_out'].'|'.$rsv_data['data']['num_days'].'|'.$diafora;
    }
  }
  
  
  if ($rsv_data['data']['reservation_status']!='040cancelled') {
    if ($rsv_data['data']['rooms_plithos']==0 or count($rsv_data['data']['rooms'])==0) {
      $rsv_data['errors'][]='error not found: '.$rsv_data['data']['rooms_plithos'].'|'.count($rsv_data['data']['rooms']);
    } else if ($rsv_data['data']['rooms_plithos']<>count($rsv_data['data']['rooms'])) {
      $rsv_data['errors'][]='error rooms_plithos<>count(data_rooms): '.$rsv_data['data']['rooms_plithos'].'<>'.count($rsv_data['data']['rooms']);
    }
  }
  

  if (count($rsv_data['errors'])==0) {
    $rsv_data['success']=true;
    $rsv_data['message']='ok';
  } else {
    $rsv_data['message']='Βρέθηκαν '.count($rsv_data['errors']). ' λάθη κατά την διαδικασία';
  }
  
  return $rsv_data;
  
}

function gks_hotel_reservation_parse_text($a) {
  $a=str_replace("\r\n", ' ', $a);
  $a=str_replace("\n", ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  
  $a=trim_gks($a);
  return $a;
}

function gks_hotel_reservation_parse_float($a,&$erros) {
  //echo $a;
  $a_orig=$a;
  
  $a=gks_hotel_reservation_parse_text($a);
  if ($a=='€ 0') return 0;
  if ($a=='0') return 0;
  
  $a=str_replace('€', ' ', $a);
  $a=str_replace('$', ' ', $a);
  $a=trim_gks($a);
  
  //$a='1.620,56'; echo $a;
  //$a='1,620.56'; echo $a;
  //$a='1.620'; echo $a;
  //$a='1,620'; echo $a;
  //$a='6,20'; echo $a;
  //$a='6.20'; echo $a;
  $pos_comma=strpos($a, ',');
  $pos_teleia=strpos($a, '.');
  
  
  if ($pos_comma===false and $pos_teleia===false) {
    $a=floatval($a);
  } else if ($pos_comma!==false and $pos_teleia!==false) {
    if ($pos_comma < $pos_teleia) { //to comma prota meta teleia p.x. 1,620.50
      //echo $a;die();
      $a=str_replace(',', '', $a);
      $a=floatval($a);
    } else { //1.620,50
      //echo 'hhhh';
      $a=str_replace('.', '', $a);
      $a=str_replace(',', '.', $a);
      $a=floatval($a);
    }
  } else {
    if ($pos_comma===false)  { //exei teleia
      //echo '|'.$pos_comma.'|'.strlen($a).'|';
      if ($pos_teleia == (strlen($a) - 2 -1)) { //einai ipodiastoli
        //echo 'kkkkkkkkkk';
        $a=floatval($a);
      } else {
        //echo 'ppppp';
        $a=str_replace('.', '', $a);
        $a=floatval($a);
      }
    } else { //exei comma
      //echo '|'.$pos_comma.'|'.strlen($a).'|';
      if ($pos_comma == (strlen($a) - 2 -1)) { //einai ipodiastoli
        //echo 'ggggggggggg';
        $a=str_replace(',', '.', $a);
        $a=floatval($a);
      } else {
        //echo 'eeeeeeeeeee';
        $a=str_replace(',', '', $a);
        $a=floatval($a);
      }
    }
    
  }
  //var_dump($a);
  $a=floatval($a);
  //var_dump($a);die();
  
    
  
  if ($a_orig!='' and $a==0) $erros[]='error parsing float: '.$a_orig.' to: '.$a;
  
  return $a;
}

function gks_hotel_reservation_parse_date($a,&$erros) {
  
  //$a='Κυρ 30 Μάη 2021';
  //echo $a."\n";
  $a_orig=$a;
  $a=gks_hotel_reservation_parse_text($a);
  
  $a=mb_strtolower($a);
  $a=cleartonous_php($a);

  $a=str_replace('κυριακη', '', $a);
  $a=str_replace('δευτερα', '', $a);
  $a=str_replace('τριτη', '', $a);
  $a=str_replace('τεταρτη', '', $a);
  $a=str_replace('πεμπτη', '', $a);
  $a=str_replace('παρασκευη', '', $a);
  $a=str_replace('σαββατο', '', $a);
  
  $a=str_replace('sunday', '', $a);
  $a=str_replace('monday', '', $a);
  $a=str_replace('tuesday', '', $a);
  $a=str_replace('wednesday', '', $a);
  $a=str_replace('thursday', '', $a);
  $a=str_replace('friday', '', $a);
  $a=str_replace('saturday', '', $a);
  
  $a=str_replace('ιανουαριος',  ' m1 ', $a);
  $a=str_replace('φεβρουαριος', ' m2 ', $a);  
  $a=str_replace('μαρτιος',     ' m3 ', $a);
  $a=str_replace('απριλιος',    ' m4 ', $a); 
  $a=str_replace('μαιος',       ' m5 ', $a);
  $a=str_replace('ιουνιος',     ' m6 ', $a);  
  $a=str_replace('ιουλιος',     ' m7 ', $a);  
  $a=str_replace('αυγουστος',   ' m8 ', $a);
  $a=str_replace('σεπτεμβριος', ' m9 ', $a);  
  $a=str_replace('οκτωβριος',   ' m10 ', $a);
  $a=str_replace('νοεμβριος',   ' m11 ', $a); 
  $a=str_replace('δεκεμβριος',  ' m12 ', $a); 

  $a=str_replace('ιαν',  ' m1 ', $a);
  $a=str_replace('φεβ',  ' m2 ', $a);  
  $a=str_replace('μαρ',  ' m3 ', $a);
  $a=str_replace('απρ',  ' m4 ', $a); 
  $a=str_replace('μαι',  ' m5 ', $a);
  $a=str_replace('μαη',  ' m5 ', $a);
  $a=str_replace('ιουν', ' m6 ', $a);  
  $a=str_replace('ιουλ', ' m7 ', $a);  
  $a=str_replace('αυγ',  ' m8 ', $a);
  $a=str_replace('σεπ',  ' m9 ', $a);  
  $a=str_replace('οκτ',  ' m10 ', $a);
  $a=str_replace('νοε',  ' m11 ', $a); 
  $a=str_replace('δεκ',  ' m12 ', $a); 

  $a=str_replace('january',   ' m1 ', $a);
  $a=str_replace('february',  ' m2 ', $a);  
  $a=str_replace('march',     ' m3 ', $a);
  $a=str_replace('april',     ' m4 ', $a); 
  $a=str_replace('may',       ' m5 ', $a);
  $a=str_replace('june',      ' m6 ', $a);  
  $a=str_replace('july',      ' m7 ', $a);  
  $a=str_replace('august',    ' m8 ', $a);
  $a=str_replace('september', ' m9 ', $a);  
  $a=str_replace('october',   ' m10 ', $a);
  $a=str_replace('november',  ' m11 ', $a); 
  $a=str_replace('december',  ' m12 ', $a); 

  $a=str_replace('jan',  ' m1 ', $a);
  $a=str_replace('feb',  ' m2 ', $a);  
  $a=str_replace('mar',  ' m3 ', $a);
  $a=str_replace('apr',  ' m4 ', $a); 
  $a=str_replace('may',  ' m5 ', $a);
  $a=str_replace('jun',  ' m6 ', $a);  
  $a=str_replace('jul',  ' m7 ', $a);  
  $a=str_replace('aug',  ' m8 ', $a);
  $a=str_replace('sep',  ' m9 ', $a);  
  $a=str_replace('oct',  ' m10 ', $a);
  $a=str_replace('nov',  ' m11 ', $a); 
  $a=str_replace('dec',  ' m12 ', $a); 


  
  $a=str_replace('κυρ', '', $a);
  $a=str_replace('δευ', '', $a);
  $a=str_replace('τρι', '', $a);
  $a=str_replace('τετ', '', $a);
  $a=str_replace('πεμ', '', $a);
  $a=str_replace('παρ', '', $a);
  $a=str_replace('σαβ', '', $a);
  
  $a=str_replace('sun', '', $a);
  $a=str_replace('mon', '', $a);
  $a=str_replace('tue', '', $a);
  $a=str_replace('wed', '', $a);
  $a=str_replace('thu', '', $a);
  $a=str_replace('fri', '', $a);
  $a=str_replace('sat', '', $a);
  
  $a=trim_gks($a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  
  //echo $a."\n";
  $parts=explode(' ',$a);
  if (count($parts)!=3) {
    $a='';
  } else {
    $d=0;
    $m=0;
    $y=0;
    foreach ($parts as $index => $value) {
      $value=trim_gks($value);
      if ($value!='') {
        if (substr($value, 0,1)=='m') {
          $m=intval(substr($value,1));
        } else {
          if ($index==2) $y=intval($value); else $d=intval($value);
        }
      }
    } 
    if ($d<1 or $d>31 or $m<1 or $m>12 or $y < 2020) {
      $a='';  
    } else {
      $a=$y.'-'.($m < 9 ? '0' : '').$m.'-'.($d < 9 ? '0' : '').$d;
    }
  }
  
  if ($a_orig!='' and $a=='') $erros[]='error parsing date: '.$a_orig.' to: '.$a;

  //echo $a."\n"; die();
  return $a;
}

function gks_hotel_reservation_parse_visitors($a,&$erros) {
  $ret=array(
    'rnum_adults'=>0,
    'rnum_childs'=>0,
    'rchilds_ages_list'=>array(),
  );
  
  $a_orig=$a;
  
  if ($a_orig!='' and $a_orig==trim_gks(intval($a_orig))) {
    $ret['rnum_adults']=intval($a_orig);
    return $ret;
  }
  
  //echo $a_orig."\n";
  $a=gks_hotel_reservation_parse_text($a);
  
  $pos1=strpos($a,'(');
  
  $agestext='';
  if ($pos1!==false) {
    $pos2=strpos($a,')',$pos1);
    $agestext=substr($a, $pos1+1,$pos2-$pos1-1);
    $a=trim_gks(str_replace('('.$agestext.')','',$a));
    $agestext=str_replace('και',',', $agestext);
    $agestext=str_replace('and',',', $agestext);
    $parts=explode(',',$agestext);
    foreach ($parts as $value) {
      $value=trim_gks($value);
      $value=intval($value);
      if ($value>0) {
        $ret['rchilds_ages_list'][]=$value;
      }
    } 
    
    //echo 'pos1:'.$pos1.' pos2:'.$pos2.' agestext:'.$agestext.' a:'.$a."\n"; 
  }
  $parts1=explode(',',$a);
  //print_r($parts1);
  foreach ($parts1 as $i=>$part1) {
    $parts2=explode(' ',trim_gks($part1));
    if (count($parts2)==2) {
      if ($i==0) $ret['rnum_adults']=intval($parts2[0]);
      if ($i==1) $ret['rnum_childs']=intval($parts2[0]);
    }
  }
  
  if (($ret['rnum_adults']==0 and $ret['rnum_childs']==0) or 
      ($ret['rnum_childs']!=count($ret['rchilds_ages_list']))
      ) {
    $temp=str_replace("\n",' ',print_r($ret,true));
    $temp=str_replace('  ',' ',$temp);
    $temp=str_replace('  ',' ',$temp);
    $temp=str_replace('  ',' ',$temp);
    $temp=str_replace('  ',' ',$temp);
    $temp=str_replace('  ',' ',$temp);
    
    $erros[]='error parsing visitors: '.$a_orig.' to: '.$temp;
  }
  
  
  //if ($a_orig!='' and ($ret['rnum_adults']==0 or $ret['rnum_childs']==0)) $erros[]='error parsing visitors: '.$a_orig.' to: '.$a;
  
  return $ret;
}

function gks_hotel_reservation_parse_visitors_max($a,&$erros) {
  //2 ενήλικες, 1 παιδί (μέχρι 2 επισκέπτες)
  
  $ret=array(
    'adults'=>0,
    'childs'=>0,
    'max'=>0,
  );
  
  $a_orig=$a;
  //echo $a_orig."\n";
  $a=gks_hotel_reservation_parse_text($a);
  
  $pos1=strpos($a,'(');
  
  $agestext='';
  if ($pos1!==false) {
    $pos2=strpos($a,')',$pos1);
    $agestext=substr($a, $pos1+1,$pos2-$pos1-1);
    $a=trim_gks(str_replace('('.$agestext.')','',$a));
    $agestext=str_replace('μέχρι',',', $agestext);
    $agestext=str_replace('επισκέπτες',',', $agestext);
    $agestext=str_replace('max',',', $agestext);
    $agestext=str_replace('guests',',', $agestext);
    $parts=explode(',',$agestext);
    foreach ($parts as $value) {
      $value=trim_gks($value);
      $value=intval($value);
      if ($value>0) {
        $ret['max']=$value;
      }
    } 
    
    //echo 'pos1:'.$pos1.' pos2:'.$pos2.' agestext:'.$agestext.' a:'.$a."\n"; 
  }
  $parts1=explode(',',$a);
  //print_r($parts1);
  foreach ($parts1 as $i=>$part1) {
    $parts2=explode(' ',trim_gks($part1));
    if (count($parts2)==2) {
      if ($i==0) $ret['adults']=intval($parts2[0]);
      if ($i==1) $ret['childs']=intval($parts2[0]);
    }
  }
  
  if (($ret['adults']==0 and $ret['childs']==0) and $ret['max']==0) {
    $temp=str_replace("\n",' ',print_r($ret,true));
    $temp=str_replace('  ',' ',$temp);
    $temp=str_replace('  ',' ',$temp);
    $temp=str_replace('  ',' ',$temp);
    $temp=str_replace('  ',' ',$temp);
    $temp=str_replace('  ',' ',$temp);
    
    $erros[]='error parsing visitors_max: '.$a_orig.' to: '.$temp;
  }
  
  
  //if ($a_orig!='' and ($ret['rnum_adults']==0 or $ret['rnum_childs']==0)) $erros[]='error parsing visitors: '.$a_orig.' to: '.$a;
  
  return $ret;
}
