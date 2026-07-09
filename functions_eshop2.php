<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_basket_recalc(&$mybasketarray, $fields_change, $br_params=array()) {
  global $dev_page_starttime;
  global $db_link;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  

  //echo '<pre>';print_r($br_params);print_r($fields_change);print_r($mybasketarray); die();
  
  

  $generic_ekprosi=-1;
  if ($mybasketarray['from']!='reservation' and $mybasketarray['from']!='transfer_reservation' and $mybasketarray['user']['user_id'] >0) {
    $sql="select generic_ekprosi from ".GKS_WP_TABLE_PREFIX."users where ID=".$mybasketarray['user']['user_id'];
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    if ($result->num_rows == 1) {    
      $row = $result->fetch_assoc();
      $generic_ekprosi = $row['generic_ekprosi'];
    }
  }

  $check_vies_run=false;
  $check_vies_valid=false;
  $check_vies_error='';
  $check_vies_function='';
  
  
  

  

  //$mybasketarray['fiscal_position']=10;
  $afm=trim_gks($mybasketarray['user']['afm']);
  if ($mybasketarray['from']=='basket') {
    if ($mybasketarray['user']['ma_country_id'] == 91) { //greece
      if ($mybasketarray['parastatiko']==0) {//apodiji 
        $mybasketarray['fiscal_position']=1; //Lianikis Esoterikou
      } else { //timologio
        if ($afm=='') {
          $mybasketarray['fiscal_position']=1; //Lianikis Esoterikou
        } else {
          $res_vies = CheckAFM_GSIS($afm);
          $check_vies_function='CheckAFM_GSIS';
          //echo '<pre>';
          //print_r($res_vies);
          //die();
          $check_vies_run=true;
          $check_vies_error=$res_vies['error'];
          if ($res_vies['valid']) {
            $check_vies_valid=true;
            $mybasketarray['fiscal_position']=11; //Chondrikis Esoterikou
          } else {
            $mybasketarray['fiscal_position']=1; //Lianikis Esoterikou
          }
        }        
      }
    } else if ($mybasketarray['user']['ma_country_id'] <= 0) {
      $mybasketarray['fiscal_position']=1;
    } else if ($mybasketarray['user']['ma_country_id'] > 0) { //alli xora
      $country_ee='';
      $sql="select country_ee from gks_country where country_ee<>'' and id_country=".$mybasketarray['user']['ma_country_id'];
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'sql error',$sql);
      } else {
        if ($result->num_rows == 1) {
          $row = $result->fetch_assoc();
          $country_ee=$row['country_ee'];
        }
      }
      
      if ($country_ee=='') { //trites xores
        if ($mybasketarray['parastatiko']==0) {//apodiji 
          $mybasketarray['fiscal_position']=3; //Lianikis Trites Chores
        } else { //timologio
          $mybasketarray['fiscal_position']=51; //Chondrikis Trites Chores
        }
      } else { //ee
        if ($mybasketarray['parastatiko']==0) {//apodiji 
          $mybasketarray['fiscal_position']=2; //Lianikis Endokoinotikes
        } else { //timologio
          if ($afm=='') {
            $mybasketarray['fiscal_position']=2; //Lianikis Endokoinotikes
          } else {
            
            $res_vies = CheckAFM_VIES($country_ee,$afm);
            $check_vies_function='CheckAFM_VIES';
            //echo '<pre>';
            //print_r($res_vies);
            //die();
            $check_vies_run=true;
            $check_vies_error=$res_vies['error'];
            if ($res_vies['valid']) {
              $check_vies_valid=true;
              $mybasketarray['fiscal_position']=41; //Chondrikis Endokoinotikes
            } else {
              $mybasketarray['fiscal_position']=2; //Lianikis Endokoinotikes
            }
            
          }
        }
      }
    }
  }
  
  //print '<pre>bb '.$mybasketarray['fiscal_position']; die();
  
  $mybasketarray['check_vies']=array();
  $mybasketarray['check_vies']['run'] = $check_vies_run;
  $mybasketarray['check_vies']['valid'] = $check_vies_valid;
  $mybasketarray['check_vies']['error'] = $check_vies_error;
  $mybasketarray['check_vies']['function'] = $check_vies_function;
  

  $mybasketarray['eidos_parastatikou_has_fpa']=1;
  $mybasketarray['eidos_parastatikou_type_id']=0;
  $mybasketarray['acc_eidos_parastatikou_id']=0;
  if ($mybasketarray['from']=='acc_inv' and isset($mybasketarray['inv_acc_journal_id']) and $mybasketarray['inv_acc_journal_id']>0) {
    $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,eidos_parastatikou_type_id,acc_eidos_parastatikou_id
    FROM gks_acc_journal 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE gks_acc_journal.id_acc_journal=".$mybasketarray['inv_acc_journal_id']." AND gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou Is Not Null";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    if ($result->num_rows >=1) {
      $row = $result->fetch_assoc();
      $mybasketarray['eidos_parastatikou_has_fpa']=intval($row['eidos_parastatikou_has_fpa']);
      $mybasketarray['eidos_parastatikou_type_id']=intval($row['eidos_parastatikou_type_id']);
      $mybasketarray['acc_eidos_parastatikou_id']=intval($row['acc_eidos_parastatikou_id']);
      //echo '<pre>'; echo $mybasketarray['eidos_parastatikou_has_fpa'];die();
    }
  }
  if ($mybasketarray['from']=='whi_mov' and isset($mybasketarray['mov_whi_journal_id']) and $mybasketarray['mov_whi_journal_id']>0) {
    $sql="SELECT gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,eidos_parastatikou_type_id,acc_eidos_parastatikou_id
    FROM gks_acc_journal 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
    WHERE gks_acc_journal.id_acc_journal=".$mybasketarray['mov_whi_journal_id']." AND gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou Is Not Null";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }  
    if ($result->num_rows >=1) {
      $row = $result->fetch_assoc();
      $mybasketarray['eidos_parastatikou_has_fpa']=intval($row['eidos_parastatikou_has_fpa']);
      $mybasketarray['eidos_parastatikou_type_id']=intval($row['eidos_parastatikou_type_id']);
      $mybasketarray['acc_eidos_parastatikou_id']=intval($row['acc_eidos_parastatikou_id']);
      //echo '<pre>'; echo $mybasketarray['eidos_parastatikou_has_fpa'];die();
    }
  }
    
  
  
	$myreservations = $mybasketarray['hotel']['reservation']['basket']['reservations'];
  
  //print '<pre>';
  //print_r($myreservations);
  //print '</pre>';
  //die();  
  
//  $myrt=array();
//  $rindex=-1;
//  foreach ($myreservations as $reservation) {
//    foreach ($reservation['selrooms'] as $selroom) {
//      foreach ($selroom['rooms_items'] as $roomitem) {
//        $rindex++;
//        $rtid=$selroom['roomtype']['id'];
//        $pid=$selroom['roomtype']['product_id'];
//        //if (isset($myrt[$pid])==false) {
//        $myrt[$rindex]=array('rtid' => $rtid,'pid' => $pid,'rooms'=>0, 'nights'=>0,'price' => 0);
//        //}
//        $myrt[$rindex]['rooms']=count($selroom['rooms_items']);
//        $myrt[$rindex]['nights']=$reservation['num_days']; // * $selroom['num'];
//        $myrt[$rindex]['price']=$selroom['roomtype']['price'];// * $selroom['num'];
//      }
//    }
//  }


//  print '<pre>';
//  print_r($myrt);
//  print '</pre>';
//  die();  
  //$dev_page_starttime1=microtime(true); echo ($dev_page_starttime1-$dev_page_starttime).'<br>';

  //debug_mail(false,'1111',print_r($myrt,true));
  //print '<pre>ddddd';die();
  //print_r($mybasketarray['products']);
  
  //remove rooms 
  if ($mybasketarray['from']=='basket') {
    $keys_products = array_keys($mybasketarray['products']);
    foreach ($keys_products as $product_key) {
      if (isset($mybasketarray['products'][$product_key]['is_hotel_room_type']) and $mybasketarray['products'][$product_key]['is_hotel_room_type'] == true) {
        unset($mybasketarray['products'][$product_key]);
      }
    } 
  }

  
  $fix_prices=array();
//  foreach ($myrt as $myrt_val) {
//    //$product_posotita[$myrt_val['pid']] = $myrt_val['nights'];
//    $fix_prices[$myrt_val['index']]=$myrt_val['price']/$myrt_val['nights'];
//  }
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/myreservations1.txt',print_r($myreservations,true));
  
  
  foreach ($myreservations as $reservation) {
    //print '<pre>ssssss';print_r($myreservations);die();
    foreach ($reservation['selrooms'] as $selroom) {
      foreach ($selroom['rooms_items'] as $roomitem) {

        $empty_index=1;
        do {
          if (isset($mybasketarray['products'][$empty_index])==false) break;
          $empty_index++;
        } while(true);
        //$empty_index;
        $sql="SELECT gks_hotel_room_type.id_hotel_room_type, gks_hotel_room_type.product_id, gks_hotel_room_type.room_type_descr, 
        gks_hotel_room_type.room_type_visitors, gks_hotel_room_type.room_type_visitors_childs, gks_hotel_room_type.room_type_visitors_max, 
        gks_hotel_room.id_hotel_room, gks_eshop_products.product_fpa_base_id,
        gks_hotel_room.hotel_id,
        product_withheldPercentCategory,
        product_otherTaxesPercentCategory,
        product_stampDutyPercentCategory,
        product_feesPercentCategory
        
        FROM (gks_hotel_room 
        LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
        LEFT JOIN gks_eshop_products ON gks_hotel_room_type.product_id = gks_eshop_products.id_product
        WHERE gks_hotel_room_type.id_hotel_room_type Is Not Null
        AND gks_hotel_room_type.product_id>0
        AND gks_hotel_room.id_hotel_room =".$roomitem['room_item_id'];
        //print '<pre>ssss ';print $sql; die();
        
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        if ($result->num_rows == 1) {
    
        
          $row = $result->fetch_assoc();
          $hotel_id=$row['hotel_id']; 
          
          $value['product_withheldPercentCategory']=0;
          $value['product_withheldAmount']=0;
          $value['product_otherTaxesPercentCategory']=0;  
          $value['product_otherTaxesAmount']=0; 
          $value['product_stampDutyPercentCategory']=0;  
          $value['product_stampDutyAmount']=0;
          $value['product_feesPercentCategory']=0;  
          $value['product_feesAmount']=0;  
          $value['product_deductionsSelection']='';  
          $value['product_deductionsAmount']=0;  
  
          $value['product_withheldPercentCategory']=$row['product_withheldPercentCategory'];
          $value['product_otherTaxesPercentCategory']=$row['product_otherTaxesPercentCategory'];
          $value['product_stampDutyPercentCategory']=$row['product_stampDutyPercentCategory'];
          $value['product_feesPercentCategory']=$row['product_feesPercentCategory'];


          
          $objects=array();
          $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $reservation['num_days'], 'files' => array(), 'warnings'=>array());
        
          //print '<pre>wwwwwww';print_r($roomitem);die();
        
          $mybasketarray['products'][$empty_index]=array(
            'is_hotel_room_type' => true,
            'product_id'=>array(
              'id_product'=>$row['product_id'], 
              'product_monada_id' => 100,
              'product_fpa_base_id' => $row['product_fpa_base_id'],
              'product_fpa_aade_id' => 0,
              'product_sheets'=>0, 
              'product_set' => '',
              'is_optional'=>0,
             ), 
            'objects'=>$objects,
            'user_ekptosi' => 0, //($generic_ekprosi > 0 ? $generic_ekprosi :0),
            'user_final_net' => 0, //floatval($selroom['roomtype']['price']) ,
            'user_final_total' => floatval($roomitem['room_price']) ,
            'user_change_ekptosi_or_final_net' => 'gks_price_final', //gks_price_final gks_ekptosi 
            'user_field_change' => 'gks_price_final',// 'gks_price_final'; //gks_ekptosi  or gks_price or gks_quantity
            
            
            'id_hotel'=> $hotel_id,
            'user_check_in'=> $reservation['check_in'],
            'user_check_out'=> $reservation['check_out'],
            'user_room_id' => $roomitem['room_item_id'],
            'user_rnum_adults' => $roomitem['rnum_adults'],
            'user_rnum_childs' => $roomitem['rnum_childs'],
            'user_rchilds_ages_list' => json_encode($roomitem['childs_and_ages']), //$value['rchilds_ages_list'],
            'user_rnum_child_kounies' => 0,
            'user_rnum_extra_beds' => 0,
            
            
            'other_taxes' => array(
              'withheldPercentCategory' => intval($value['product_withheldPercentCategory']),  
              'withheldAmount' => floatval($value['product_withheldAmount']),  
              'otherTaxesPercentCategory' => intval($value['product_otherTaxesPercentCategory']),  
              'otherTaxesAmount' => floatval($value['product_otherTaxesAmount']),  
              'stampDutyPercentCategory' => intval($value['product_stampDutyPercentCategory']),  
              'stampDutyAmount' => floatval($value['product_stampDutyAmount']), 
              'feesPercentCategory' => intval($value['product_feesPercentCategory']),  
              'feesAmount' => floatval($value['product_feesAmount']),  
              'deductionsSelection' => '',
              'deductionsAmount' => floatval($value['product_deductionsAmount']),  
            ),

          );
          

        }
                
      }
    }
  }
        
  //print '<pre>';print_r($mybasketarray['products']); die();
  
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/myreservations2.txt',print_r($myreservations,true));
  
  //file_put_contents(GKS_SITE_PATH.'tmp/basket.'.time().rand(1000,9999).'.txt',print_r($mybasketarray,true));
      
  $product_posotita=array();
  //$index=0;
  //$product_posotita[0]=1;
  
  
  foreach ($mybasketarray['products'] as $keyp => &$item) {
    if (isset($item['product_id']['product_fpa_base_id']) and
        isset($item['product_id']['product_fpa_aade_id'])==false) {
      $item['product_id']['product_fpa_aade_id']=0;
    }
  }
  unset($item);
  
  foreach ($mybasketarray['products'] as $keyp => $item) {
    $mycc=0;
    foreach ($item['objects'] as $keyo => $object) {
      if ($object['type']=='normal') {
        $mycc+=$object['copies'];
      } else if ($object['type']=='multi') {
        $mycc+=$object['copies'];
        $subcount=0;
        foreach ($object['files'] as $file) {
          $subcount+=$file['copies'];  
        }   
        $mybasketarray['products'][$keyp]['objects'][$keyo]['subcount']=$subcount;
      } else if ($object['type']=='simple') {
        foreach ($object['files'] as $file) {
          $mycc+=$file['copies'];  
        }
      }
    }
    //$index++;
    $product_is_optional=0;
    if (isset($item['product_id']['product_is_optional'])) {
      $product_is_optional=$item['product_id']['product_is_optional'];
    }
    $product_posotita[$keyp]=array(
      'id_product' => $item['product_id']['id_product'], 
      'posotita' => $mycc, 
      'monada_id' => $item['product_id']['product_monada_id'], 
      'fpa_base_id' => $item['product_id']['product_fpa_base_id'], 
      'fpa_aade_id' => $item['product_id']['product_fpa_aade_id'], 
      'sheets' => $item['product_id']['product_sheets'], 
      'set' => $item['product_id']['product_set'],
      'is_optional' => $product_is_optional,
      'is_transfer_oxima_type' => (isset($item['product_id']['is_transfer_oxima_type']) ? $item['product_id']['is_transfer_oxima_type'] : false),
      'is_hotel_room_type' => ($mybasketarray['from']=='reservation' ? true : false),
      
    );
    //$product_posotita[$item['product_id']['id_product']] = $mycc;
  }
  





  
    

  //print '<pre>sss'; print_r($product_posotita); print '</pre>';
    
  
  //$dev_page_starttime1=microtime(true); echo ($dev_page_starttime1-$dev_page_starttime).'<br>';
  
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/basket1.txt',print_r($mybasketarray,true));
  
  //echo '<pre>';print_r($mybasketarray);die();
  $gp_params=array();// Get Products params
  $gp_params=$br_params;
  
  //echo '<pre>rrrrrrrrrrrtttttttt';print_r($br_params);die();
  
  $myproducts = gks_products_get($mybasketarray,$product_posotita,'',$mybasketarray['coupons'],$fix_prices,$fields_change,$gp_params);  
  //echo '<pre>';print_r($mybasketarray);die();
  //echo '<pre>22222222';print_r($myproducts);die();


  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/basket2.txt',print_r($myproducts,true));

  //echo '<pre>';print_r($mybasketarray);die();


  //$dev_page_starttime1=microtime(true); echo ($dev_page_starttime1-$dev_page_starttime).'<br>';
  
  //print '<pre>';print_r($myproducts);die(); 


  
  
  $products_posotita=0;
  $products_varos=0;
  $products_ogos=0;
  $products_ogos_max_x=0;
  $products_ogos_max_y=0;
  $products_ogos_max_z=0;
  $products_original_netvalue=0;
  $products_netvalue=0;
  $products_fpa=0;
  $products_netfpa=0;
  $products_total=0;
  $products_need_apostoli = false;
  $totalWithheldAmount=0;
  $totalOtherTaxesAmount=0;
  $totalStampDutyamount=0;
  $totalFeesAmount=0;
  $totalDeductionsAmount=0;
  
  //print '<pre>333333333';print_r($myproducts);die();
  //print '<pre>333333333';print_r($mybasketarray['products']);die();
  
  $online_enable=false; if (isset($mybasketarray['online_enable'])) $online_enable=$mybasketarray['online_enable'];
  //echo '<pre>'.$online_enable.'|';die();
  
  foreach ($mybasketarray['products'] as $index => &$item) {
    $item['product_id'] = $myproducts[$index];
    
    $pifs=true;//product_is_for_sum
    if ($online_enable) {
      //echo '<pre>';print_r($item);die();
      $is_optional=0;
      if (isset($item['product_id']['input']['is_optional'])) {
        $is_optional=intval($item['product_id']['input']['is_optional']);
      } 
      //0-> metraei sto sinolo - to default
      //1-> mporei na to prosuesei o pelatis
      //2-> to prosthese o pelatis
      if ($is_optional==1) {
        $pifs=false;
      }
      //echo '<pre>'.$is_optional.'|'.$pifs;die();
    }
    
    
    $count_free = array();
    $curr_product_posotita=0;
    foreach ($item['objects'] as $object) {

      if ($item['product_id']['id_product'] == 1) {  //Digital file
        
        foreach ($object['files'] as $file) {
          $check_file_id =  $file['id'];
          
          foreach ($mybasketarray['products'] as $id_product_check => $item_check) {
            
            if ($item_check['product_id']['id_product'] != 1 and $item_check['product_id']['product_category_id'] == 3) { //Printings
              if ($item_check['product_id']['product_price_start_peritem_net'] >= $item['product_id']['product_price_start_peritem_net']) {
                foreach ($item_check['objects'] as $object_check) {
                  foreach ($object_check['files'] as $file_check) {
                    if ($file_check['id'] == $check_file_id) {
                      if (in_array($check_file_id, $count_free ) == false ) 
                      $count_free[] = $check_file_id;
                    }
                  }
                }
              }
            }
          }
        }
      }
       
      $epi_posostita=1;
      if (isset($item['product_id']['monada_convert']['ok']) and 
          $item['product_id']['monada_convert']['ok'] and 
          $item['product_id']['monada_convert']['epi']!=0 and 
          $item['product_id']['monada_convert']['epi']!=1) {
        $epi_posostita=$item['product_id']['monada_convert']['epi'];
//        print '<pre>';
//        print $epi_posostita;
//        die();
      }
      
      if ($object['type']=='simple') {
        $copies_temp=0;
        foreach ($object['files'] as $file) {
          $copies_temp+=$file['copies'];  
        }
        $curr_product_posotita+=$copies_temp;
        if ($pifs) $products_posotita+=$copies_temp;
        
        if ($item['product_id']['product_is_digital']==0) {
          
          if ($item['product_id']['product_need_apostoli']) {
            if ($pifs) $products_varos+= $copies_temp * $item['product_id']['product_varos'] / $epi_posostita;
            if ($pifs) $products_ogos+=($copies_temp * ($item['product_id']['product_ogos_x'] * $item['product_id']['product_ogos_y'] * $item['product_id']['product_ogos_z'])) / $epi_posostita;
            
            if ($pifs) if ($item['product_id']['product_ogos_x'] > $products_ogos_max_x) $products_ogos_max_x=$item['product_id']['product_ogos_x'];
            if ($pifs) if ($item['product_id']['product_ogos_y'] > $products_ogos_max_y) $products_ogos_max_y=$item['product_id']['product_ogos_y'];
            if ($pifs) $products_ogos_max_z+=$copies_temp * $item['product_id']['product_ogos_z'] / $epi_posostita;
            
          }
        }
      } else if ($object['type']=='multi' or $object['type']=='normal') {
        if ($pifs) $products_posotita+=$object['copies'];
        $curr_product_posotita+=$object['copies'];
        if ($item['product_id']['product_is_digital']==0) {
          if ($item['product_id']['product_need_apostoli']) {
            if ($pifs) $products_varos+= $object['copies'] * $item['product_id']['product_varos'] / $epi_posostita;
            if ($pifs) $products_ogos+=($object['copies'] * ($item['product_id']['product_ogos_x'] * $item['product_id']['product_ogos_y'] * $item['product_id']['product_ogos_z'])) / $epi_posostita;

            if ($pifs) if ($item['product_id']['product_ogos_x'] > $products_ogos_max_x) $products_ogos_max_x=$item['product_id']['product_ogos_x'];
            if ($pifs) if ($item['product_id']['product_ogos_y'] > $products_ogos_max_y) $products_ogos_max_y=$item['product_id']['product_ogos_y'];
            if ($pifs) $products_ogos_max_z+=$object['copies'] * $item['product_id']['product_ogos_z'] / $epi_posostita;
          }
        }
      }
    }
    
    $item['product_id']['count_free'] = count($count_free);
    if ($item['product_id']['count_free'] > 0) {
      $logos = ($item['product_id']['product_quantity'] - $item['product_id']['count_free']) / $item['product_id']['product_quantity'];
      $item['product_id']['product_price_final_all_net']=round($item['product_id']['product_price_final_all_net'] * $logos,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $item['product_id']['product_price_final_all_fpa']=round($item['product_id']['product_price_final_all_fpa'] * $logos,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $item['product_id']['product_price_final_all_total']=round($item['product_id']['product_price_final_all_total'] * $logos,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    }
    
    
    //echo '<pre>';print_r($item);die();
    
    
    
    
    
    
    if ($pifs) $products_original_netvalue+=$item['product_id']['product_price_start_all_net'];
    if ($pifs) $products_netvalue+=$item['product_id']['product_price_final_all_net'];
    if ($pifs) $products_fpa+=$item['product_id']['product_price_final_all_fpa'];
    if ($pifs) $products_netfpa+=$item['product_id']['product_price_final_all_net'] + $item['product_id']['product_price_final_all_fpa'];
    if ($pifs) $products_total+=$item['product_id']['product_price_final_all_total'];
    
    if ($pifs) $totalWithheldAmount+=$item['other_taxes']['withheldAmount'];
    if ($pifs) $totalOtherTaxesAmount+=$item['other_taxes']['otherTaxesAmount'];
    if ($pifs) $totalStampDutyamount+=$item['other_taxes']['stampDutyAmount'];
    if ($pifs) $totalFeesAmount+=$item['other_taxes']['feesAmount'];
    if ($pifs) $totalDeductionsAmount+=$item['other_taxes']['deductionsAmount'];
    
    if ($pifs) if ($item['product_id']['product_need_apostoli'] and $curr_product_posotita>0) {
      $products_need_apostoli = true;
    }
    
  }
  unset($item);

  //print '<pre>';
  //print_r($mybasketarray['products']);
  //print '</pre>';
  //die(); 


    
  if (gks_get_destination_data($mybasketarray) == false) {
    debug_mail(false,gks_lang('Σφάλμα υπολογισμού διεύθυνσης αποστολής'),print_r($mybasketarray['destination_data'],true));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα υπολογισμού διεύθυνσης αποστολής').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die(); }
    
    
//  echo '<pre>111111 ';
//  print_r($mybasketarray['destination_data']);
//  die();  
  
  
  //$dev_page_starttime1=microtime(true); echo ($dev_page_starttime1-$dev_page_starttime).'<br>';
  
    
  $mybasketarray['products_posotita'] = $products_posotita;
  $mybasketarray['products_varos'] = $products_varos;              //se grammaria
  $mybasketarray['products_ogos'] = $products_ogos;                //se cm^3   //  1 liters = 1000 cubic centimeters  
  $mybasketarray['products_ogos_max_x'] = $products_ogos_max_x;                //se cm 
  $mybasketarray['products_ogos_max_y'] = $products_ogos_max_y;                //se cm 
  $mybasketarray['products_ogos_max_z'] = $products_ogos_max_z;                //se cm 

  
  $mybasketarray['products_original_netvalue'] = round($products_original_netvalue,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  $mybasketarray['products_netvalue'] = round($products_netvalue,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  $mybasketarray['products_fpa'] = round($products_fpa,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  $mybasketarray['products_netfpa'] = round($products_netfpa,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
  
  $mybasketarray['totalWithheldAmount'] = $totalWithheldAmount;
  $mybasketarray['totalOtherTaxesAmount'] = $totalOtherTaxesAmount;
  $mybasketarray['totalStampDutyamount'] = $totalStampDutyamount;
  $mybasketarray['totalFeesAmount'] = $totalFeesAmount;
  $mybasketarray['totalDeductionsAmount'] = $totalDeductionsAmount;


  
  

  $mybasketarray['products_total'] = 
    $mybasketarray['products_netvalue'] 
    + $mybasketarray['products_fpa']
    - $mybasketarray['totalWithheldAmount']
    + $mybasketarray['totalOtherTaxesAmount']
    + $mybasketarray['totalStampDutyamount']
    + $mybasketarray['totalFeesAmount']
    - $mybasketarray['totalDeductionsAmount'];
  
  
  $mybasketarray['products_need_apostoli'] = $products_need_apostoli;
  
  if ($mybasketarray['products_need_apostoli'] == false) $mybasketarray['tropos_apostolis'] = 1;
  if ($mybasketarray['products_need_apostoli'] == true && $mybasketarray['tropos_apostolis'] == 1 ) $mybasketarray['tropos_apostolis'] = 0;
  
  $mybasketarray['products_need_pliromi'] = $mybasketarray['products_total'] >= 0.01;
  
  if ($mybasketarray['eidos_parastatikou_type_id']==24) {//apografi
    $products_need_apostoli=false;
    $mybasketarray['products_need_apostoli']=false;
    $mybasketarray['products_need_pliromi']=false;
    
  }

//  echo '<pre>111111 ';
//  print_r($mybasketarray['destination_data']);
//  die();
  
      
  $mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray, $mybasketarray['tropos_apostolis']);
  $mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, -1);
  $mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray, $mybasketarray['tropos_pliromis']);

      
  //$return = array('success' => false, 'message' => base64_encode($mybasketarray['tropos_pliromis'].'--'.$mybasketarray['kostos_pliromis']));
  //echo json_encode($return); die();
  
  //$products_total+=1;
  //if (abs($products_total - $mybasketarray['products_total']) > 0.01) {
  //  debug_mail(false,'basket_recalc diafora: '.$products_total.'-'.$mybasketarray['products_total'].'='.($products_total - $mybasketarray['products_total']));
  //}
  
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/basket3.txt',print_r($mybasketarray,true));
  
  
  return $myproducts;
}
