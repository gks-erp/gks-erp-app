<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_woo_order_update_local_from_woo($eshop,$data,$woo_settings,$force) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_ORDER_DEFAULT_DELIVERY;
  global $GKS_ORDER_DEFAULT_PAYMENT;

  //print '<pre>';print_r($data);die();
  //return array('success' => false, 'message' => base64_encode('<pre>data start '.print_r($data, true)));
  $check_final_price_from_woo=floatval($data['total']);
  $check_final_price_from_gks=0;
  
  //file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/ggg.txt', print_r($data,true));
  
  //$force==0 and 
  if ((isset($eshop['import_yes']) ==false or trim_gks($eshop['import_yes'])=='' or $eshop['import_yes']==0)) {
    return array('success' => true, 'message' => base64_encode(gks_lang('Δεν είναι ενεργοποιημένο το: <b>Να γίνεται αυτόματη εισαγωγή στο WooCommerce</b>')));}
  
  
  $remote_order_status=trim_gks($data['status']);
  //$force==0 and 
  if (in_array($remote_order_status,$eshop['update_state_woo'])==false) {
    return array('success' => true, 'message' => base64_encode(gks_lang('Η κατάσταση της παραγγελίας στο WooCommerce δεν είναι στην λίστα με τις επιτρεπτές στο <b>Κατάσταση στο WooCommerce</b>')));}
    
  
  if (isset($my_wp_user_id)==false) $my_wp_user_id=2;
  if (isset($gkIP)==false) $gkIP='127.0.0.1';
  
  
  $eshop_id=$eshop['id_eshop'];
  //print '<pre>';print_r($eshop);
  $woo_order_id=intval($data['id']);
  if ($woo_order_id<=0) {
    debug_mail(false,'woo_order_id is not set',print_r($data,true));
    return array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί ο αριθμός παραγγελίας')));} 
  
  
  

  
  if ($eshop['import_as']=='transfer') { 
    $table_into='gks_transfer_reservation';
    $table_id  ='id_transfer_reservation';
    $table_idr ='transfer_reservation_id';
  } else if ($eshop['import_as']=='reservation') { 
    $table_into='gks_hotel_reservation';
    $table_id  ='id_hotel_reservation';
    $table_idr ='hotel_reservation_id';
  } else if ($eshop['import_as']=='order') {
    $table_into='gks_orders';
    $table_id  ='id_order';
    $table_idr ='order_id';
  } else if ($eshop['import_as']=='acc_inv') {
    $table_into='gks_acc_inv';
    $table_id  ='id_acc_inv';
    $table_idr ='acc_inv_id';
  } else {
    return array('success' => false, 'message' => base64_encode('erro on import_as'));
  }

  $eponimia='';
  if (trim_gks($eshop['order_meta_eponimia'])!='' and isset($data['meta_data'])) {
    foreach ($data['meta_data'] as $meta_data) {
      if ($meta_data['key']==$eshop['order_meta_eponimia']) {
        $eponimia=trim_gks($meta_data['value']);
      }
    }   
  }
  
  $afm='';
  if (trim_gks($eshop['order_meta_afm'])!='' and isset($data['meta_data'])) {
    foreach ($data['meta_data'] as $meta_data) {
      if ($meta_data['key']==$eshop['order_meta_afm']) {
        $afm=trim_gks($meta_data['value']);
      }
    }   
  }
  

  $parastatiko=0;
  if (startwith($eshop['order_meta_parastatiko'],'def:')) {
    $parastatiko=intval(substr($eshop['order_meta_parastatiko'], 4)); //def:1
    if ($parastatiko!=1) $parastatiko=0;
  } else if ($eshop['order_meta_parastatiko']=='if_afm') { //if_afm
    if ($afm!='') $parastatiko=1;
  } else if ($eshop['order_meta_parastatiko']=='if_eponimia') { //if_eponimia
    if ($eponimia!='') $parastatiko=1;
  } else if (trim_gks($eshop['order_meta_parastatiko'])!='' and isset($data['meta_data'])) { //meta field name
    foreach ($data['meta_data'] as $meta_data) {
      if ($meta_data['key']==$eshop['order_meta_parastatiko']) {
        $temp=trim_gks($meta_data['value']);
        if ($temp=='1' or $temp=='yes') $parastatiko=1; else $parastatiko=0;
        break;
      }
    }
  }
  //return array('success' => false, 'message' => base64_encode('<pre>|'.$parastatiko.'|'.$eponimia.'|'.$title.'|'.$afm.'|'.$doy.'|'.$epaggelma.'|'));  
    

  //AND gks_acc_journal.acc_eidos_parastatikou_id=".($table_into=='gks_orders' ? '204' :'11')."
  
  $eshop_acc_journal_id=intval($eshop['acc_journal_id']);
  $eshop_acc_seira_id=intval($eshop['acc_seira_id']);
  $eshop_warehouses_id_from=intval($eshop['warehouses_id_from']);
  if ($eshop['import_as']=='acc_inv' and $parastatiko==1) {
    if (intval($eshop['acc_journal_id_tim'])>0 and intval($eshop['acc_seira_id_tim'])>0) {
      $eshop_acc_journal_id=intval($eshop['acc_journal_id_tim']);
      $eshop_acc_seira_id=intval($eshop['acc_seira_id_tim']);
      $eshop_warehouses_id_from=intval($eshop['warehouses_id_from']);
    }
  }
  
  $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira,gks_acc_seires.seira_code,
  gks_acc_journal.acc_eidos_parastatikou_id,
  gks_acc_journal.acc_eidos_parastatikou_whi_id
  FROM gks_acc_journal 
  LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id
  WHERE gks_acc_journal.company_id=".$eshop['company_id']."
  AND gks_acc_journal.company_sub_id=".$eshop['company_sub_id']."
  AND gks_acc_journal.is_disable=0
  AND gks_acc_seires.is_disable=0
  and id_acc_journal=".$eshop_acc_journal_id.".
  and id_acc_seira=".$eshop_acc_seira_id.".
  ORDER BY gks_acc_journal.sortorder, gks_acc_seires.sortorder;";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => base64_encode('sql error'));}  
  if ($result->num_rows==0) {
    debug_mail(false,'id_acc_journal or-and id_acc_seira not found',$sql);
    return array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε ημερολόγιο και σειρά')));}  
  $row = $result->fetch_assoc(); 
  $id_acc_journal=$row['id_acc_journal'];
  $id_acc_seira=$row['id_acc_seira'];
  $seira_code=trim_gks($row['seira_code']);
  $acc_eidos_parastatikou_id=intval($row['acc_eidos_parastatikou_id']);
  $acc_eidos_parastatikou_whi_id=intval($row['acc_eidos_parastatikou_whi_id']);
  
  $warehouses_id_from=0;
  $warehouses_id_to=1; //Eikoniki Apothiki Pelaton

  
  if ($acc_eidos_parastatikou_whi_id!=0) {
    $sql="SELECT id_warehouse FROM gks_warehouses 
    WHERE company_id=".$eshop['company_id']." 
    AND company_sub_id=".$eshop['company_sub_id']."
    and warehouse_disable=0
    and id_warehouse=".$eshop_warehouses_id_from."
    ORDER BY warehouse_sortorder";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $warehouses_id_from=$row['id_warehouse'];
    }
  }
  
  
  

  //return array('success' => false, 'message' => base64_encode('<pre>'.print_r($value, true)));
  $kataxoreiseis=array();
  
  if ($table_into=='gks_transfer_reservation') {
    
    $id_transfer=0;
    foreach ($data['gks_items'] as $item_index => $value) {
      //return array('success' => false, 'message' => base64_encode('<pre>'.print_r($value, true)));


      if (isset($value['gks_popsicle_transfer_data'])) {
        if (isset($value['gks_popsicle_transfer_data']['data']['guid'])) {
          $guid=trim_gks($value['gks_popsicle_transfer_data']['data']['guid']);
          $id_transfer=intval($value['gks_popsicle_transfer_data']['data']['id_transfer']);
          $rsrv_aa=$item_index + 1; 
          $temp_session_id=trim_gks($value['gks_popsicle_transfer_data']['data']['temp_session_id']);;
          
          //if (isset($kataxoreiseis[$guid])==false) {
            $items_need=$value['gks_popsicle_transfer_data']['protasi']['items_need'];
            if (isset($value['gks_popsicle_transfer_data']['transfer_params'])) {
              $transfer_params = $value['gks_popsicle_transfer_data']['transfer_params'];
            } else {
              $transfer_params = gks_transfer_get_params($id_transfer);
            }
          
            $tr_price=floatval($value['total']) + floatval($value['total_tax']);
            
            //if ($items_need==1 and $value['gks_popsicle_transfer_data']['data']['val_date2_time']==0) {
              //einai i pio apli periprosi
            //} else {
              $tr_rest=$tr_price;

              $rsrv_sms_text_message_price=0;
              $rsrv_sms_text_message_enable=intval($value['gks_popsicle_transfer_data']['contact']['sms']);
              if ($rsrv_sms_text_message_enable!=1) $rsrv_sms_text_message_enable=0;
              if ($rsrv_sms_text_message_enable==1) {
                $rsrv_sms_text_message_price=floatval($transfer_params['transfer_sms_text_message_price']);
                $tr_rest-=$rsrv_sms_text_message_price;
              }
             
              $rsrv_cancellation_protection_price=0;
              $rsrv_cancellation_protection_enable=intval($value['gks_popsicle_transfer_data']['confirm']['cancellation_protection']);
              if ($rsrv_cancellation_protection_enable!=1) $rsrv_cancellation_protection_enable=0;
              if ($rsrv_cancellation_protection_enable==1) {
                $rsrv_cancellation_protection_price=floatval($transfer_params['transfer_cancellation_protection_price']);
                $tr_rest-=$rsrv_cancellation_protection_price;
              }
              
              $extras=$value['gks_popsicle_transfer_data']['extras'];
              $tr_price_extras_outward=0;
              if ($extras['kareklakia_plitos']!=0 or $extras['kareklakia_plitos_return']!=0) $tr_price_extras_outward+=floatval($extras['kareklakia_price']) * ($extras['kareklakia_plitos']/($extras['kareklakia_plitos'] + $extras['kareklakia_plitos_return']));
              if ($extras['booster_plitos']!=0 or $extras['booster_plitos_return']!=0) $tr_price_extras_outward+=floatval($extras['booster_price']) * ($extras['booster_plitos']/($extras['booster_plitos'] + $extras['booster_plitos_return']));
              if ($extras['golfbag_plitos']!=0 or $extras['golfbag_plitos_return']!=0) $tr_price_extras_outward+=floatval($extras['golfbag_price']) * ($extras['golfbag_plitos']/($extras['golfbag_plitos'] + $extras['golfbag_plitos_return']));
              if ($extras['skis_plitos']!=0 or $extras['skis_plitos_return']!=0) $tr_price_extras_outward+=floatval($extras['skis_price']) * ($extras['skis_plitos']/($extras['skis_plitos'] + $extras['skis_plitos_return']));
              if ($extras['amajidia_plitos']!=0 or $extras['amajidia_plitos_return']!=0) $tr_price_extras_outward+=floatval($extras['amajidia_price']) * ($extras['amajidia_plitos']/($extras['amajidia_plitos'] + $extras['amajidia_plitos_return']));
              if ($extras['5minstop_plitos']!=0 or $extras['5minstop_plitos_return']!=0) $tr_price_extras_outward+=floatval($extras['5minstop_price']) * ($extras['5minstop_plitos']/($extras['5minstop_plitos'] + $extras['5minstop_plitos_return']));
              
              $tr_price_extras_return=0;
              if ($extras['kareklakia_plitos']!=0 or $extras['kareklakia_plitos_return']!=0) $tr_price_extras_return+=floatval($extras['kareklakia_price']) * ($extras['kareklakia_plitos_return']/($extras['kareklakia_plitos'] + $extras['kareklakia_plitos_return']));
              if ($extras['booster_plitos']!=0 or $extras['booster_plitos_return']!=0) $tr_price_extras_return+=floatval($extras['booster_price']) * ($extras['booster_plitos_return']/($extras['booster_plitos'] + $extras['booster_plitos_return']));
              if ($extras['golfbag_plitos']!=0 or $extras['golfbag_plitos_return']!=0) $tr_price_extras_return+=floatval($extras['golfbag_price']) * ($extras['golfbag_plitos_return']/($extras['golfbag_plitos'] + $extras['golfbag_plitos_return']));
              if ($extras['skis_plitos']!=0 or $extras['skis_plitos_return']!=0) $tr_price_extras_return+=floatval($extras['skis_price']) * ($extras['skis_plitos_return']/($extras['skis_plitos'] + $extras['skis_plitos_return']));
              if ($extras['amajidia_plitos']!=0 or $extras['amajidia_plitos_return']!=0) $tr_price_extras_return+=floatval($extras['amajidia_price']) * ($extras['amajidia_plitos_return']/($extras['amajidia_plitos'] + $extras['amajidia_plitos_return']));
              if ($extras['5minstop_plitos']!=0 or $extras['5minstop_plitos_return']!=0) $tr_price_extras_return+=floatval($extras['5minstop_price']) * ($extras['5minstop_plitos_return']/($extras['5minstop_plitos'] + $extras['5minstop_plitos_return']));
                            
              $tr_rest-=$tr_price_extras_outward;
              $tr_rest-=$tr_price_extras_return;
              
              $tr_price_outward=0;
              $tr_price_return=0;
              
              if ($value['gks_popsicle_transfer_data']['protasi']['group_type']=='group_one') {
                if ($value['gks_popsicle_transfer_data']['protasi']['pricelists']['proto']['price_oxima_per_item']>0) {
                  $tr_price_outward=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['proto']['price_oxima_per_item'];
                } else if ($value['gks_popsicle_transfer_data']['protasi']['pricelists']['proto']['transfer_pricelist_price_per_transfer_offer']>0) {
                  $tr_price_outward=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['proto']['transfer_pricelist_price_per_transfer_offer'];
                } else {
                  $tr_price_outward=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['proto']['transfer_pricelist_price_per_transfer'];
                }
                if ($value['gks_popsicle_transfer_data']['data']['val_date2_time']>0) { //mono  outward
                  if ($value['gks_popsicle_transfer_data']['protasi']['pricelists']['deytero']['price_oxima_per_item'] > 0) {
                    $tr_price_return=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['deytero']['price_oxima_per_item'];
                  } else if ($value['gks_popsicle_transfer_data']['protasi']['pricelists']['deytero']['transfer_pricelist_price_per_transfer_offer'] > 0) {
                    $tr_price_return=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['deytero']['transfer_pricelist_price_per_transfer_offer'];
                  } else {
                    $tr_price_return=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['deytero']['transfer_pricelist_price_per_transfer'];
                  }
                }
              } else {

                if ($value['gks_popsicle_transfer_data']['protasi']['pricelists']['proto']['transfer_pricelist_price_per_person_offer']>0) {
                  $tr_price_outward=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['proto']['transfer_pricelist_price_per_person_offer'];
                } else {
                  $tr_price_outward=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['proto']['transfer_pricelist_price_per_person'];
                }
                if ($value['gks_popsicle_transfer_data']['data']['val_date2_time']>0) { //mono  outward
                  if ($value['gks_popsicle_transfer_data']['protasi']['pricelists']['deytero']['transfer_pricelist_price_per_person_offer'] > 0) {
                    $tr_price_return=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['deytero']['transfer_pricelist_price_per_person_offer'];
                  } else {
                    $tr_price_return=$value['gks_popsicle_transfer_data']['protasi']['pricelists']['deytero']['transfer_pricelist_price_per_person'];
                  }
                }
                
                $epivates =  $value['gks_popsicle_transfer_data']['data']['val_adults'] +
                            $value['gks_popsicle_transfer_data']['data']['val_children'] +
                            $value['gks_popsicle_transfer_data']['data']['val_infants'];
                
                $tr_price_outward=$tr_price_outward*$epivates;
                $tr_price_return=$tr_price_return*$epivates;
                
                $items_need = 1;
                
                
                //return array('success' => false, 'message' => base64_encode('<pre>epivates '.$epivates));
                //return array('success' => false, 'message' => base64_encode('<pre>'.print_r($value['gks_popsicle_transfer_data']['protasi'], true)));
                
              }
              
              //$tr_price_outward+=$tr_price_extras_outward;;
              
              
              //if ($value['gks_popsicle_transfer_data']['data']['val_date2_time']>=0) { //mono  outward
                //$tr_price_return+=$tr_price_extras_return;
              //}
              
              
              $tr_oxima_outward=array();
              $tr_oxima_return=array();
              for($ff=1; $ff<=$items_need; $ff++) {
                $tr_oxima_outward[$ff]['adults']=0;
                $tr_oxima_outward[$ff]['children']=0;
                $tr_oxima_outward[$ff]['infants']=0;

                $tr_oxima_outward[$ff]['kareklakia']=0;
                $tr_oxima_outward[$ff]['booster']=0;
                $tr_oxima_outward[$ff]['golfbag']=0;
                $tr_oxima_outward[$ff]['skis']=0;
                $tr_oxima_outward[$ff]['amajidia']=0;
                $tr_oxima_outward[$ff]['5minstop']=0;
                $tr_oxima_outward[$ff]['extras_price']=0;
                $tr_oxima_outward[$ff]['transfer_price']=$tr_price_outward;
                $tr_oxima_outward[$ff]['price_total']=0;
                
                $tr_oxima_return[$ff]['adults']=0;
                $tr_oxima_return[$ff]['children']=0;
                $tr_oxima_return[$ff]['infants']=0;

                $tr_oxima_return[$ff]['kareklakia']=0;
                $tr_oxima_return[$ff]['booster']=0;
                $tr_oxima_return[$ff]['golfbag']=0;
                $tr_oxima_return[$ff]['skis']=0;
                $tr_oxima_return[$ff]['amajidia']=0;
                $tr_oxima_return[$ff]['5minstop']=0;
                $tr_oxima_return[$ff]['extras_price']=0;
                $tr_oxima_return[$ff]['transfer_price']=$tr_price_return;
                $tr_oxima_return[$ff]['price_total']=0;
              }
              
              $data_vals=$value['gks_popsicle_transfer_data']['data'];
 
              $cc_set=0; do {
                if ($cc_set >= $data_vals['val_adults']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_outward[$ff]['adults']++;$cc_set++;if ($cc_set >= $data_vals['val_adults']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $data_vals['val_children']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_outward[$ff]['children']++;$cc_set++;if ($cc_set >= $data_vals['val_children']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $data_vals['val_infants']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_outward[$ff]['infants']++;$cc_set++;if ($cc_set >= $data_vals['val_infants']) break;}
              } while (true);

              $cc_set=0; do {
                if ($cc_set >= $data_vals['val_adults']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_return[$ff]['adults']++;$cc_set++;if ($cc_set >= $data_vals['val_adults']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $data_vals['val_children']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_return[$ff]['children']++;$cc_set++;if ($cc_set >= $data_vals['val_children']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $data_vals['val_infants']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_return[$ff]['infants']++;$cc_set++;if ($cc_set >= $data_vals['val_infants']) break;}
              } while (true);


              
              $cc_set=0; do {
                if ($cc_set >= $extras['kareklakia_plitos']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_outward[$ff]['kareklakia']++;$cc_set++;if ($cc_set >= $extras['kareklakia_plitos']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['booster_plitos']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_outward[$ff]['booster']++;$cc_set++;if ($cc_set >= $extras['booster_plitos']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['golfbag_plitos']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_outward[$ff]['golfbag']++;$cc_set++;if ($cc_set >= $extras['golfbag_plitos']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['skis_plitos']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_outward[$ff]['skis']++;$cc_set++;if ($cc_set >= $extras['skis_plitos']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['amajidia_plitos']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_outward[$ff]['amajidia']++;$cc_set++;if ($cc_set >= $extras['amajidia_plitos']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['5minstop_plitos']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_outward[$ff]['5minstop']++;$cc_set++;if ($cc_set >= $extras['5minstop_plitos']) break;}
              } while (true);
              
              $cc_set=0; do {
                if ($cc_set >= $extras['kareklakia_plitos_return']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_return[$ff]['kareklakia']++;$cc_set++;if ($cc_set >= $extras['kareklakia_plitos_return']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['booster_plitos_return']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_return[$ff]['booster']++;$cc_set++;if ($cc_set >= $extras['booster_plitos_return']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['golfbag_plitos_return']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_return[$ff]['golfbag']++;$cc_set++;if ($cc_set >= $extras['golfbag_plitos_return']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['skis_plitos_return']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_return[$ff]['skis']++;$cc_set++;if ($cc_set >= $extras['skis_plitos_return']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['amajidia_plitos_return']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_return[$ff]['amajidia']++;$cc_set++;if ($cc_set >= $extras['amajidia_plitos_return']) break;}
              } while (true);
              $cc_set=0; do {
                if ($cc_set >= $extras['5minstop_plitos_return']) break;
                for ($ff = 1; $ff <= $items_need; $ff++) {$tr_oxima_return[$ff]['5minstop']++;$cc_set++;if ($cc_set >= $extras['5minstop_plitos_return']) break;}
              } while (true);
              
              $oxima_type=$value['gks_popsicle_transfer_data']['protasi']['oxima_type'];
              for ($ff = 1; $ff <= $items_need; $ff++) {
                 $tr_oxima_outward[$ff]['extras_price']+=$tr_oxima_outward[$ff]['kareklakia']*$oxima_type['transfer_oxima_type_price_kareklakia'];
                 $tr_oxima_outward[$ff]['extras_price']+=$tr_oxima_outward[$ff]['booster']*$oxima_type['transfer_oxima_type_price_booster'];
                 $tr_oxima_outward[$ff]['extras_price']+=$tr_oxima_outward[$ff]['golfbag']*$oxima_type['transfer_oxima_type_price_golfbag'];
                 $tr_oxima_outward[$ff]['extras_price']+=$tr_oxima_outward[$ff]['skis']*$oxima_type['transfer_oxima_type_price_skis'];
                 $tr_oxima_outward[$ff]['extras_price']+=$tr_oxima_outward[$ff]['amajidia']*$oxima_type['transfer_oxima_type_price_amajidia'];
                 $tr_oxima_outward[$ff]['extras_price']+=$tr_oxima_outward[$ff]['5minstop']*$oxima_type['transfer_oxima_type_price_5minstop'];

                 $tr_oxima_return[$ff]['extras_price']+=$tr_oxima_return[$ff]['kareklakia']*$oxima_type['transfer_oxima_type_price_kareklakia'];
                 $tr_oxima_return[$ff]['extras_price']+=$tr_oxima_return[$ff]['booster']*$oxima_type['transfer_oxima_type_price_booster'];
                 $tr_oxima_return[$ff]['extras_price']+=$tr_oxima_return[$ff]['golfbag']*$oxima_type['transfer_oxima_type_price_golfbag'];
                 $tr_oxima_return[$ff]['extras_price']+=$tr_oxima_return[$ff]['skis']*$oxima_type['transfer_oxima_type_price_skis'];
                 $tr_oxima_return[$ff]['extras_price']+=$tr_oxima_return[$ff]['amajidia']*$oxima_type['transfer_oxima_type_price_amajidia'];
                 $tr_oxima_return[$ff]['extras_price']+=$tr_oxima_return[$ff]['5minstop']*$oxima_type['transfer_oxima_type_price_5minstop'];
              }
              

              
              for ($ff = 1; $ff <= $items_need; $ff++) {
                $tr_oxima_outward[$ff]['price_total']=$tr_oxima_outward[$ff]['transfer_price'] + $tr_oxima_outward[$ff]['extras_price'];
                $tr_oxima_return[$ff]['price_total'] =$tr_oxima_return[$ff]['transfer_price']  + $tr_oxima_return[$ff]['extras_price'];
              }
              
              
//              return array('success' => false, 'message' => base64_encode('<pre>'.$tr_price.'|'.$tr_rest.'|'.
//              "\n".
//              $items_need.'|'.
//              "\n".
//              $rsrv_sms_text_message_price.'|'.$rsrv_cancellation_protection_price.'|'.
//              "\n".
//              $tr_price_extras_outward.'|'.$tr_price_extras_return.'|'.
//              "\n".
//              $tr_price_outward.'|'.$tr_price_return.'|'.
//              "\n".
//              //$tr_price_outward_rest.'|'.$tr_price_return_rest.'|'.
//              //"\n".
//              print_r($tr_oxima_outward,true)."\n".
//              print_r($tr_oxima_return,true)."\n".
//              '<br>'
//              ));
              
            //}
            

          
            $kataxoreiseis[$guid]=array(
              'transfer_outward_return' => 'outward',
              'temp_session_id' => $temp_session_id,
              'guid' => $guid,
              'aa' => $rsrv_aa,
              'id_transfer' => $id_transfer,
              'transfer_params' => $transfer_params,
              'oxima' => $value['gks_popsicle_transfer_data'],
              'items_tr' => $tr_oxima_outward,
              //'group_type' => $value['gks_popsicle_transfer_data']['protasi']['group_type'],
              
            );
            
            if ($value['gks_popsicle_transfer_data']['data']['val_date2_time']>0) { // exei kai return
              
              $kataxoreiseis[$guid.'.return']=array(
                'transfer_outward_return' => 'return',
                'temp_session_id' => $temp_session_id,
                'guid' => $guid.'.return',
                'aa' => $rsrv_aa,
                'id_transfer' => $id_transfer,
                'transfer_params' => $transfer_params,
                'oxima' => $value['gks_popsicle_transfer_data'],
                'items_tr' => $tr_oxima_return,
              );              
            }
            //return array('success' => false, 'message' => base64_encode('<pre>'.print_r($value, true)));


                        
          //}
        }
      }
    }
    
    //return array('success' => false, 'message' => base64_encode('<pre>'.print_r($kataxoreiseis, true)));
  
  } else if ($table_into=='gks_hotel_reservation') {
    
    $hotel_id=0;
    foreach ($data['gks_items'] as $item_index => $value) {
      if (isset($value['gks_hotel_reservation_room_data'])) {
        if (isset($value['gks_hotel_reservation_room_data']['guid'])) {
          $guid=trim_gks($value['gks_hotel_reservation_room_data']['guid']);
          $hotel_id=intval($value['gks_hotel_reservation_room_data']['id_hotel']);
          $rsrv_aa=intval($value['gks_hotel_reservation_room_data']['rsrv_aa']);
          $temp_session_id=trim_gks($value['gks_hotel_reservation_room_data']['temp_session_id']);;
          
          if (isset($kataxoreiseis[$guid])==false) {
            $kataxoreiseis[$guid]=array(
              'temp_session_id' => $temp_session_id,
              'guid' => $guid,
              'aa' => $rsrv_aa,
              'hotel_id' => $hotel_id,
              'rooms' => array(),
              'price' => 0,
              'num_adults' => 0,
              'num_childs' => 0,
              'childs_ages_list' => array(),
              'num_child_kounies' => 0,
              'num_extra_beds' => 0,
              'hotel_params' => gks_hotel_get_params($hotel_id),
              'first_room' => $value['gks_hotel_reservation_room_data'],
            );
          }
          
          foreach($value['gks_hotel_reservation_room_data']['room']['childs_and_ages'] as $child) {
            $kataxoreiseis[$guid]['childs_ages_list'][]=$child['age'];
          }
          
          $kataxoreiseis[$guid]['price']+=$value['gks_hotel_reservation_room_data']['room']['room_price'];
          $kataxoreiseis[$guid]['num_adults']+=$value['gks_hotel_reservation_room_data']['room']['rnum_adults'];
          $kataxoreiseis[$guid]['num_childs']+=$value['gks_hotel_reservation_room_data']['room']['rnum_childs'];
          $kataxoreiseis[$guid]['num_child_kounies']+=$value['gks_hotel_reservation_room_data']['room']['rnum_child_kounies'];
          $kataxoreiseis[$guid]['num_extra_beds']+=$value['gks_hotel_reservation_room_data']['room']['rnum_extra_beds'];
          $kataxoreiseis[$guid]['rooms'][]=array(
            'room_price'=> $value['gks_hotel_reservation_room_data']['room']['room_price'],
            'room_item_id' => $value['gks_hotel_reservation_room_data']['room']['room_item_id'],
            'childs_and_ages_fix' => $value['gks_hotel_reservation_room_data']['room']['childs_and_ages'],
            'item_index'=> $item_index,
          );
        }
      }
    }
    
    
    foreach ($kataxoreiseis as &$kataxorisi) {
      $childs_ages_list=$kataxorisi['childs_ages_list'];
      
      sort($childs_ages_list);
      $kataxorisi['childs_ages_list']=$childs_ages_list;
      
      $index_pos=$childs_ages_list;
      
      foreach ($kataxorisi['rooms'] as &$room_item) {
        
        foreach($room_item['childs_and_ages_fix'] as &$child_pos) {
          $find_pos=-1;
          foreach ($index_pos as $index => &$i_pos) {
            if ($i_pos==$child_pos['age']) {
              $i_pos=-1;
              $child_pos['index']=$index+1;
              break;
            } 
          }
          unset($i_pos);
        }
        unset($child_pos);
        
      }
      unset($room_item);
    }
    unset($kataxorisi);
    
    foreach ($kataxoreiseis as $kataxorisi) {
      foreach ($kataxorisi['rooms'] as $kat_room) {
        $data['gks_items'][$kat_room['item_index']]['gks_hotel_reservation_room_data']['room']['rchilds_ages_list_old']=$data['gks_items'][$kat_room['item_index']]['gks_hotel_reservation_room_data']['room']['rchilds_ages_list'];
        
        $data['gks_items'][$kat_room['item_index']]['gks_hotel_reservation_room_data']['room']['rchilds_ages_list']=$kat_room['childs_and_ages_fix'];
        $data['gks_items'][$kat_room['item_index']]['gks_hotel_reservation_room_data']['room']['childs_and_ages']  =$kat_room['childs_and_ages_fix'];
      }
    }    
    
    //$db_link->query("update gks_async_queue set status='pending' where guid='412f69f698dfd4dcbc036b214ad20e6a'"); 
    //print '<pre>'; 
    //print_r($kataxoreiseis); 
    //print_r($data['gks_items']); 
    //die();
    
  } else if ($table_into=='gks_orders') {
    $kataxoreiseis['guid']=array(
      'guid' => '',
      'aa' => 1,
      'temp_session_id' => '',
    );
    
  } else if ($table_into=='gks_acc_inv') {
    $kataxoreiseis['guid']=array(
      'guid' => '',
      'aa' => 1,
      'temp_session_id' => '',
    );
  }
  
  
  

  //return array('success' => false, 'message' => base64_encode('d asd ad asd asql error'));
  
  $woo_back_meta_data=[];
  
  $transfer_auto_mydata_email=array();
  $send_to_other_system_ids=array();
  
  $kat_id_ids=array();$kat_id=0;
  foreach ($kataxoreiseis as $kataxorisi) {
    
    //print '<pre>';print_r($kataxorisi);die();
    
    $kat_id++;
    
    $sql="select * from ".$table_into." where woo_eshop_id=".$eshop_id." and woo_order_id=".$woo_order_id; //echo $sql;
    if ($table_into=='gks_hotel_reservation' or $table_into=='gks_transfer_reservation') {
      $sql.=" and woo_guid='".$db_link->escape_string($kataxorisi['guid'])."'";
    }
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}  
    if ($result->num_rows==0) {
      $order_is_new=true;
      
      if ($table_into=='gks_transfer_reservation') $order_guid = guid_for_transfer_reservation();
      else if ($table_into=='gks_hotel_reservation') $order_guid = guid_for_reservation();
      else if ($table_into=='gks_orders') $order_guid = guid_for_order();
      else if ($table_into=='gks_acc_inv') $order_guid = guid_for_acc_inv();
      
      $bank_deposit_9digit=gks_get_bank_deposit_9digit();
      $sql="insert into ".$table_into." (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        woo_eshop_id,woo_order_id,";
        if ($table_into=='gks_hotel_reservation') $sql.="reservation_guid,woo_guid,hotel_booking_number"; 
        else if ($table_into=='gks_transfer_reservation') $sql.="transfer_reservation_guid,woo_guid,transfer_booking_number"; 
        else if ($table_into=='gks_orders') $sql.="order_guid,order_ref_number"; 
        else if ($table_into=='gks_acc_inv') $sql.="inv_guid,acc_inv_ref_number"; 
        $sql.=",bank_deposit_9digit
      ) values (
        '".$db_link->escape_string($data['gks_date_created'])."','".$db_link->escape_string($data['gks_date_modified'])."',
        ".intval($data['customer_id']).",".intval($data['customer_id']).",
        '".$db_link->escape_string($data['customer_ip_address'])."',
        ".$eshop_id.",".$woo_order_id.",
        '".$db_link->escape_string($order_guid)."',";
        
        if ($table_into=='gks_hotel_reservation') {
          $sql.="'".$db_link->escape_string($kataxorisi['guid'])."',";
          $hotel_booking_number=$eshop['woo_start_booking_number'].$woo_order_id.'-'.$kat_id;
          $sql.="'".$db_link->escape_string($hotel_booking_number)."',";
        }
        if ($table_into=='gks_transfer_reservation') {
          $sql.="'".$db_link->escape_string($kataxorisi['guid'])."',";
          $transfer_booking_number=$eshop['woo_start_booking_number'].'-'.$woo_order_id.'-'.$kat_id;
          $sql.="'".$db_link->escape_string($transfer_booking_number)."',";
        }
        if ($table_into=='gks_orders') {
          $order_ref_number='#'.$woo_order_id;
          $sql.="'".$db_link->escape_string($order_ref_number)."',";
        }
        if ($table_into=='gks_acc_inv') {
          $acc_inv_ref_number='#'.$woo_order_id;
          $sql.="'".$db_link->escape_string($acc_inv_ref_number)."',";
        }
        
        
        $sql.="'".$db_link->escape_string($bank_deposit_9digit)."'
      )";
      
      //return array('success' => false, 'message' => base64_encode($sql.' '.print_r($eshop,true)));
      
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => base64_encode('sql error'));}  
      
      $id_order=$db_link->insert_id;
      //return array('success' => false, 'message' => base64_encode($id_order));
    } else {
      
      //$force==0 and 
      if ($eshop['will_update']==0) {
        return array('success' => true, 'message' => base64_encode(gks_lang('Δεν είναι ενεργοποιημένη η <b>Ενημέρωση</b> για <b>Να ενημερώνεται η παραγγελία/παρασταστικό στο gks ERP κάθε φορά που αλλάζει η παραγγελία στο WooCommerce</b>')));}
      
      
      $order_is_new=false;
      $row = $result->fetch_assoc();
      $id_order=$row[$table_id];
      
      
      //print '<pre>|'.$force.'|'; print_r($eshop);die();
      //$force==0 and 
      if ($eshop['update_if_gks_change']==0 and $row['update_from_gks']!=0) {
        return array('success' => true, 'message' => base64_encode(gks_lang('Δεν είναι ενεργοποιημένη η <b>Ενημέρωση μετά από το gks ER</b> για <b>Να ενημερώνεται η παραγγελία/παρασταστικό κάθε φορά που αλλάζει η παραγγελία στο WooCommerce<b>')));}
      
      //$force==0 and 
      if ($eshop['import_as']=='transfer') {
        $transfer_reservation_status=trim_gks($row['transfer_reservation_status']);
        if (in_array($transfer_reservation_status,$eshop['update_state_gks_transfer'])==false) {
          return array('success' => true, 'message' => base64_encode(gks_lang('Η κατάσταση του transfer στο gks ERP δεν είναι στην λίστα με τις επιτρεπτές στο <b>Κατάσταση στο gks ERP</b>')));}

      //$force==0 and         
      } else if ($eshop['import_as']=='reservation') {
        $reservation_status=trim_gks($row['reservation_status']);
        if (in_array($reservation_status,$eshop['update_state_gks_reservation'])==false) {
          return array('success' => true, 'message' => base64_encode(gks_lang('Η κατάσταση της κράτησης στο gks ERP δεν είναι στην λίστα με τις επιτρεπτές στο <b>Κατάσταση στο gks ERP</b>')));}
      
      //$force==0 and 
      } else if ($eshop['import_as']=='order') {
        $order_state=trim_gks($row['order_state']);
        if (in_array($order_state,$eshop['update_state_gks_order'])==false) {
          return array('success' => true, 'message' => base64_encode(gks_lang('Η κατάσταση της παραγγελίας στο gks ERP δεν είναι στην λίστα με τις επιτρεπτές στο <b>Κατάσταση στο gks ERP</b>')));}
        
      //$force==0 and 
      } else if ($eshop['import_as']=='acc_inv') {
        $inv_state=trim_gks($row['inv_state']);
        if (in_array($inv_state,$eshop['update_state_gks_acc_inv'])==false) {
          return array('success' => true, 'message' => base64_encode(gks_lang('Η κατάσταση του παραστατικού στο gks ERP δεν είναι στην λίστα με τις επιτρεπτές στο <b>Κατάσταση στο gks ERP</b>')));}
        $aade_invoicemark=trim_gks($row['aade_invoicemark']);
        if ($aade_invoicemark!='') {
          return array('success' => true, 'message' => base64_encode(gks_lang('Το πραστατικό έχει αποσταλεί στην ΑΑΔΕ οπότε δεν μπορεί να αλλάξει')));}
        
      }
      
      
      
      if ($table_into=='gks_transfer_reservation') 
        $sql=select_gks_transfer_reservation($id_order)." where id_transfer_reservation=".$id_order." limit 1"; 
      else if ($table_into=='gks_hotel_reservation') 
        $sql=select_gks_hotel_reservation($id_order)." where id_hotel_reservation=".$id_order." limit 1"; 
      else if ($table_into=='gks_orders') 
        $sql=select_gks_orders($id_order)." where id_order=".$id_order." limit 1"; 
      else if ($table_into=='gks_acc_inv')
        $sql=select_gks_acc_inv()." where id_acc_inv = ".$id_order." limit 1"; 
      
      
      
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => base64_encode('sql error'));}
      if ($result->num_rows==1) {
        $row_old = $result->fetch_assoc();
        $products_old=[];
        
        $gks_custom_prepare=gks_custom_table_item_prepare($table_into,['from'=>'item']);
        $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
  
        if ($table_into=='gks_transfer_reservation') {
          $sql=select_gks_transfer_reservation_oximata();
          $sql.=" where transfer_reservation_id=".$id_order."
          order by oximata_aa,id_transfer_reservation_oximata";            
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => base64_encode('sql error'));}
          while ($row = $result->fetch_assoc()) {
            $products_old[]=$row;
          }
          $extra_address_old=array();


                    
        } else if ($table_into=='gks_hotel_reservation') {
          $sql="SELECT gks_hotel_reservation_room.*, gks_monades_metrisis.monada_descr, gks_eshop_fpa_base.fpa_base_descr
          FROM ((gks_hotel_reservation_room 
          LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product) 
          LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada) 
          LEFT JOIN gks_eshop_fpa_base ON gks_hotel_reservation_room.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base
          WHERE gks_hotel_reservation_room.hotel_reservation_id=".$id_order."
          ORDER BY gks_hotel_reservation_room.id_hotel_reservation_room;";
          
          
          $sql=select_gks_hotel_reservation_room();
          $sql.=" where hotel_reservation_id=".$id_order."
          order by id_hotel_reservation_room";            
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => base64_encode('sql error'));}
          while ($row = $result->fetch_assoc()) {
            $products_old[]=$row;
          }
          $extra_address_old=array();
        } else {
          
          $sql="SELECT ".$table_into."_products.*, gks_monades_metrisis.monada_descr, gks_eshop_fpa_base.fpa_base_descr
          FROM (".$table_into."_products 
          LEFT JOIN gks_monades_metrisis ON ".$table_into."_products.product_monada_id = gks_monades_metrisis.id_monada)
          LEFT JOIN gks_eshop_fpa_base ON ".$table_into."_products.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base
          WHERE ".$table_into."_products.".$table_idr."=".$id_order."
          ORDER BY ".$table_into."_products.product_aa;";
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => base64_encode('sql error'));}
          while ($row = $result->fetch_assoc()) {
            $products_old[]=$row;
          }
        
          $extra_address_old=array();
          if ($row_old['address_extra']>0) {
            $sql="SELECT gks_users_extra_address.*, gks_nomoi.nomos_descr, gks_country.country_name
            FROM (gks_users_extra_address 
            LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
            LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
            WHERE gks_users_extra_address.id_users_extra_address=".$row_old['address_extra'];
            $result_select = $db_link->query($sql);        
            if (!$result_select) {
              debug_mail(false,'error sql',$sql);
              die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
            }
            if ($result_select->num_rows==1) {
              $extra_address_old = $result_select->fetch_assoc();
            }
          }
        }
                
      }
          
    }
  
  
  
    //return array('success' => false, 'message' => base64_encode('<pre>products_old '.print_r($products_old, true)));
    
    $user_lang='el-GR'; 
    if (startwith($eshop['order_meta_user_lang'],'def:')) $user_lang=substr($eshop['order_meta_user_lang'], 4); //def:en-US
    else if (trim_gks($eshop['order_meta_user_lang'])!='' and isset($data['meta_data'])) { //meta field name
      foreach ($data['meta_data'] as $meta_data) {
        if ($meta_data['key']==$eshop['order_meta_user_lang']) {
          $temp=trim_gks($meta_data['value']);
          
          if (strlen($temp)>0) {
            if ($temp=='el') {
              $user_lang='el-GR';
            } else if ($temp=='en') {
              $user_lang='en-US';
            } else {
              $sql="select id_lang from gks_lang where id_lang like '".$db_link->escape_string($temp)."'";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              if ($result->num_rows>0) {
                $row = $result->fetch_assoc();
                $user_lang=$row['id_lang'];
              }
            }
          }
          break;
        }
      }   
    }
    
    //return array('success' => false, 'message' => base64_encode('<pre>user_lang '.$user_lang));
    
    $tropos_apostolis=0; 
    if (isset($data['gks_shipping_methods']) and 
        is_array($data['gks_shipping_methods']) and 
        isset($data['gks_shipping_methods'][0]) and
        isset($data['gks_shipping_methods'][0]['method_title']) and
        trim_gks($data['gks_shipping_methods'][0]['method_title'])!='') {
          
      $temp=trim_gks($data['gks_shipping_methods'][0]['method_title']);
      foreach ($eshop['woo_delivery_to_gks'] as $value) {
        if ($temp==$value['wt']) {
          if (intval($value['g'])>0) {
            $tropos_apostolis=$value['g'];
            break;
          }
        }
      }
      if ($tropos_apostolis==0) {
        $temp=trim_gks($data['gks_shipping_methods'][0]['method_id']);
        foreach ($eshop['woo_delivery_to_gks'] as $value) {
          if ($temp==$value['w']) {
            if (intval($value['g'])>0) {
              $tropos_apostolis=$value['g'];
              break;
            }
          }
        }
      }
    }
    if ($tropos_apostolis==0) $tropos_apostolis=$GKS_ORDER_DEFAULT_DELIVERY;
    if ($table_into=='gks_transfer_reservation') $tropos_apostolis=1;
    if ($table_into=='gks_hotel_reservation') $tropos_apostolis=1;
    
    $tropos_pliromis=0;
    if (isset($data['payment_method_title']) and trim_gks($data['payment_method_title']!='')) {
      $temp=trim_gks($data['payment_method_title']);
      foreach ($eshop['woo_payment_to_gks'] as $value) {
        if ($temp==$value['wt']) {
          if (intval($value['g'])>0) {
            $tropos_pliromis=$value['g'];
            break;
          }
        }
      }
    }
    //echo $tropos_pliromis;die();
    if ($tropos_pliromis==0) {
      if (isset($data['payment_method']) and trim_gks($data['payment_method']!='')) {
        $temp=trim_gks($data['payment_method']);
        foreach ($eshop['woo_payment_to_gks'] as $value) {
          if ($temp==$value['w']) {
            if (intval($value['g'])>0) {
              $tropos_pliromis=$value['g'];
              break;
            }
          }
        }
      }
    }
    if ($tropos_pliromis==0) $tropos_pliromis=$GKS_ORDER_DEFAULT_PAYMENT;
  
    /*
    $eponimia='';
    if (trim_gks($eshop['order_meta_eponimia'])!='' and isset($data['meta_data'])) {
      foreach ($data['meta_data'] as $meta_data) {
        if ($meta_data['key']==$eshop['order_meta_eponimia']) {
          $eponimia=trim_gks($meta_data['value']);
        }
      }   
    }
    */
    $title=isset($data['billing']['company']) ? trim_gks($data['billing']['company']) : '';
    if (trim_gks($eshop['order_meta_title'])!='' and isset($data['meta_data'])) {
      foreach ($data['meta_data'] as $meta_data) {
        if ($meta_data['key']==$eshop['order_meta_title']) {
          $title=trim_gks($meta_data['value']);
        }
      }   
    } 
    /*
    $afm='';
    if (trim_gks($eshop['order_meta_afm'])!='' and isset($data['meta_data'])) {
      foreach ($data['meta_data'] as $meta_data) {
        if ($meta_data['key']==$eshop['order_meta_afm']) {
          $afm=trim_gks($meta_data['value']);
        }
      }   
    } 
    */
    $doy='';
    if (trim_gks($eshop['order_meta_doy'])!='' and isset($data['meta_data'])) {
      foreach ($data['meta_data'] as $meta_data) {
        if ($meta_data['key']==$eshop['order_meta_doy']) {
          $doy=trim_gks($meta_data['value']);
        }
      }   
    } 
    $epaggelma='';
    if (trim_gks($eshop['order_meta_epaggelma'])!='' and isset($data['meta_data'])) {
      foreach ($data['meta_data'] as $meta_data) {
        if ($meta_data['key']==$eshop['order_meta_epaggelma']) {
          $epaggelma=trim_gks($meta_data['value']);
        }
      }   
    } 
    
    /*
    $parastatiko=0;
    if (startwith($eshop['order_meta_parastatiko'],'def:')) {
      $parastatiko=intval(substr($eshop['order_meta_parastatiko'], 4)); //def:1
      if ($parastatiko!=1) $parastatiko=0;
    } else if ($eshop['order_meta_parastatiko']=='if_afm') { //if_afm
      if ($afm!='') $parastatiko=1;
    } else if ($eshop['order_meta_parastatiko']=='if_eponimia') { //if_eponimia
      if ($eponimia!='') $parastatiko=1;
    } else if (trim_gks($eshop['order_meta_parastatiko'])!='' and isset($data['meta_data'])) { //meta field name
      foreach ($data['meta_data'] as $meta_data) {
        if ($meta_data['key']==$eshop['order_meta_parastatiko']) {
          $temp=trim_gks($meta_data['value']);
          if ($temp=='1' or $temp=='yes') $parastatiko=1; else $parastatiko=0;
          break;
        }
      }
    }
    */
    //return array('success' => false, 'message' => base64_encode('<pre>|'.$parastatiko.'|'.$eponimia.'|'.$title.'|'.$afm.'|'.$doy.'|'.$epaggelma.'|'));  
    
    $billing_mobile='';
    $billing_phone='';
    if (isset($data['billing']['phone']) and trim_gks($data['billing']['phone'])!='' and startwith($data['billing']['phone'],'69'))        $billing_mobile=trim_gks($data['billing']['phone']);
    if (isset($data['billing']['phone']) and trim_gks($data['billing']['phone'])!='' and startwith($data['billing']['phone'],'69')==false) $billing_phone= trim_gks($data['billing']['phone']);
    
    $shipping_mobile='';
    $shipping_phone='';
    if (isset($data['shipping']['phone']) and trim_gks($data['shipping']['phone'])!='' and startwith($data['shipping']['phone'],'69'))        $shipping_mobile=trim_gks($data['shipping']['phone']);
    if (isset($data['shipping']['phone']) and trim_gks($data['shipping']['phone'])!='' and startwith($data['shipping']['phone'],'69')==false) $shipping_phone= trim_gks($data['shipping']['phone']);
    
    $billing_email=''; if (isset($data['billing']['email'])) $billing_email=trim_gks($data['billing']['email']);
    
    //echo '|'.$billing_mobile.'|'.$billing_phone.'|'."\n";
    //echo '|'.$shipping_mobile.'|'.$shipping_phone.'|'."\n";
    //echo '|'.$billing_email.'|'."\n";
    
    $user_id=0;
    $order_find_user_from=trim_gks($eshop['order_find_user_from']);
    if ($order_find_user_from!='') { //afm,mobile,email,phone,user
      //echo $order_find_user_from;die();
      $parts=explode(',',$order_find_user_from);
      foreach ($parts as $method) {
        switch ($method) {
          case 'afm':
            if ($afm!='') {
              $sql="SELECT gks_users.user_id
              FROM gks_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
              WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
              and gks_users.afm like '".$db_link->escape_string($afm)."' limit 1";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              if ($result->num_rows>0) {
                $row = $result->fetch_assoc();
                $user_id=$row['user_id'];
              }
            }
            break;
          case 'mobile':
            if ($billing_mobile!='') {
              $sql="SELECT gks_users_communication.user_id
              FROM gks_users_communication 
              LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_communication.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
              WHERE gks_users_communication.comm_type='phone' 
              AND gks_users_communication.comm_value='".$db_link->escape_string($billing_mobile)."'
              AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
              ORDER BY gks_users_communication.comm_primary DESC limit 1";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              if ($result->num_rows>0) {
                $row = $result->fetch_assoc();
                $user_id=$row['user_id'];
              }
            }
            if ($user_id>0) break;
            if ($shipping_mobile!='') {
              $sql="SELECT gks_users_communication.user_id
              FROM gks_users_communication 
              LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_communication.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
              WHERE gks_users_communication.comm_type='phone' 
              AND gks_users_communication.comm_value='".$db_link->escape_string($shipping_mobile)."'
              AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
              ORDER BY gks_users_communication.comm_primary DESC limit 1";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              if ($result->num_rows>0) {
                $row = $result->fetch_assoc();
                $user_id=$row['user_id'];
              }
            }
            break;
          case 'phone':
            if ($billing_phone!='') {
              $sql="SELECT gks_users_communication.user_id
              FROM gks_users_communication 
              LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_communication.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
              WHERE gks_users_communication.comm_type='phone' 
              AND gks_users_communication.comm_value='".$db_link->escape_string($billing_phone)."'
              AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
              ORDER BY gks_users_communication.comm_primary DESC limit 1";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              if ($result->num_rows>0) {
                $row = $result->fetch_assoc();
                $user_id=$row['user_id'];
              }
            }
            if ($user_id>0) break;
            if ($shipping_phone!='') {
              $sql="SELECT gks_users_communication.user_id
              FROM gks_users_communication 
              LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_communication.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
              WHERE gks_users_communication.comm_type='phone' 
              AND gks_users_communication.comm_value='".$db_link->escape_string($shipping_phone)."'
              AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
              ORDER BY gks_users_communication.comm_primary DESC limit 1";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              if ($result->num_rows>0) {
                $row = $result->fetch_assoc();
                $user_id=$row['user_id'];
              }
            }
            break;
          
          case 'email':
            if ($billing_email!='') {
              $sql="SELECT gks_users_communication.user_id
              FROM gks_users_communication 
              LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_communication.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
              WHERE gks_users_communication.comm_type='email' 
              AND gks_users_communication.comm_value='".$db_link->escape_string($billing_email)."'
              AND ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
              ORDER BY gks_users_communication.comm_primary DESC limit 1";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
                return array('success' => false, 'message' => base64_encode('sql error'));}  
              if ($result->num_rows>0) {
                $row = $result->fetch_assoc();
                $user_id=$row['user_id'];
              }
            }
            break;
          case 'user': 
            if (intval($data['customer_id'])>0) {
              $user_id=intval($data['customer_id']);
            }
            break;
          default:
            
        }
        if ($user_id>0) break;
      }
    }
    if ($user_id<=0 and intval($data['customer_id'])>0) {
      //$user_id=intval($data['customer_id']);
    }
    
    //echo '|'.$user_id.'|';
  
    //return array('success' => false, 'message' => base64_encode('<pre>user_id '.$user_id));
    
    
    if ($table_into=='gks_transfer_reservation') $order_state=gks_woo_convert_state_to_transfer($data['status']);
    else if ($table_into=='gks_hotel_reservation') $order_state=gks_woo_convert_state_to_reservation($data['status']);
    else if ($table_into=='gks_orders') $order_state=gks_woo_convert_state_to_order($data['status']);
    else if ($table_into=='gks_acc_inv') $order_state=gks_woo_convert_state_to_acc_inv($data['status']);
  
    //return array('success' => false, 'message' => base64_encode('<pre>order_state '.$order_state));
    
    $sqlUpdate='';
    
    
    
    if ($table_into=='gks_transfer_reservation') {
      $sqlUpdate.="transfer_reservation_date='".$db_link->escape_string($data['gks_date_created'])."',";
      $sqlUpdate.="transfer_reservation_journal_id=".$id_acc_journal.",";
      $sqlUpdate.="transfer_reservation_seira_id=".$id_acc_seira.",";
      $sqlUpdate.="transfer_reservation_seira_code='".$db_link->escape_string($seira_code)."',";
      $sqlUpdate.="transfer_reservation_status='".$db_link->escape_string($order_state)."',";
      //kostas
      
      $transfer_id=0;
      foreach ($data['gks_items'] as $value) {
        if (isset($value['gks_popsicle_transfer_data']['data'])) {
          if (isset($value['gks_popsicle_transfer_data']['data']['id_transfer'])) {
            $transfer_id=intval($value['gks_popsicle_transfer_data']['data']['id_transfer']);
            break;
          }
        }
      } 
      $sqlUpdate.="transfer_id=".$transfer_id.",";
      
      
      
      if ($kataxorisi['transfer_outward_return']=='outward') {
        $transfer_start=_time_user($kataxorisi['oxima']['data']['val_date1_time'],1);
        
        if (in_array($kataxorisi['oxima']['data']['val_to_poi_type_id'],[2,3,4]) and
          $kataxorisi['oxima']['data']['pick_up_time_real_1']>0) {
          //$transfer_start=_time_user($kataxorisi['oxima']['data']['pick_up_time_real_1'],1);  
        }

        if (in_array($kataxorisi['oxima']['data']['val_to_poi_type_id'],[2,3,4]) and
          isset($kataxorisi['oxima']['contact']['outward_from_pick_up_time']) and 
          $kataxorisi['oxima']['contact']['outward_from_pick_up_time']>0) {
          $transfer_start=_time_user($kataxorisi['oxima']['contact']['outward_from_pick_up_time'],1);  
          //date('Y-m-d H:i:s',_time_user($return_from_pick_up_time,1))
        }
                    
        //print '<pre>';print_r($kataxorisi);die();
        
      } else {
        $transfer_start=_time_user($kataxorisi['oxima']['data']['val_date2_time'],1);
        //echo date('Y-m-d H:i:s',$transfer_start);echo "\r";
        //echo date('Y-m-d H:i:s',$kataxorisi['oxima']['contact']['return_from_pick_up_time']);echo "\r";
        //echo date('Y-m-d H:i:s',_time_user($kataxorisi['oxima']['contact']['return_from_pick_up_time'],1));die();
        //print '<pre>';print_r($kataxorisi);die();
        
        if (in_array($kataxorisi['oxima']['data']['val_from_poi_type_id'],[2,3,4]) and
          isset($kataxorisi['oxima']['contact']['return_from_pick_up_time']) and 
          $kataxorisi['oxima']['contact']['return_from_pick_up_time']>0) {
          $transfer_start=_time_user($kataxorisi['oxima']['contact']['return_from_pick_up_time'],1);  
          //date('Y-m-d H:i:s',_time_user($return_from_pick_up_time,1))
        }
                
      }
      
      
            
      
      
      $direction=$kataxorisi['oxima']['data']['val_direction'];
      if ($direction!='tole') $direction='tori';
      //file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/ssss.txt',print_r($kataxorisi,true));
      
      if ($direction=='tori') {
        if ($kataxorisi['transfer_outward_return']=='outward') { //outward
          $poi_id_from=intval($kataxorisi['oxima']['data']['val_from_id']);
          $poi_from_place_id='';
          if (isset($kataxorisi['oxima']['data']['val_from_place_id'])) 
            $poi_from_place_id=$kataxorisi['oxima']['data']['val_from_place_id'];
            
          $poi_from_place_formatted_address='';
          if (isset($kataxorisi['oxima']['data']['val_from_place_formatted_address'])) 
            $poi_from_place_formatted_address=$kataxorisi['oxima']['data']['val_from_place_formatted_address'];
            
          $poi_from_place_lat=0;
          if (isset($kataxorisi['oxima']['data']['val_from_place_lat'])) 
            $poi_from_place_lat=floatval($kataxorisi['oxima']['data']['val_from_place_lat']);
          else if (isset($kataxorisi['oxima']['data']['val_from_poi_map_latitude'])) 
            $poi_from_place_lat=floatval($kataxorisi['oxima']['data']['val_from_poi_map_latitude']);
          
          $poi_from_place_lng=0;
          if (isset($kataxorisi['oxima']['data']['val_from_place_lng'])) 
            $poi_from_place_lng=floatval($kataxorisi['oxima']['data']['val_from_place_lng']);
          else if (isset($kataxorisi['oxima']['data']['val_from_poi_map_longitude'])) 
            $poi_from_place_lng=floatval($kataxorisi['oxima']['data']['val_from_poi_map_longitude']);
          
          
          $poi_id_to  =intval($kataxorisi['oxima']['data']['val_to_id']);
          $poi_to_place_id=$kataxorisi['oxima']['data']['val_to_place_id'];
          $poi_to_place_formatted_address=$kataxorisi['oxima']['data']['val_to_place_formatted_address'];
          $poi_to_place_lat=floatval($kataxorisi['oxima']['data']['val_to_place_lat']);
          $poi_to_place_lng=floatval($kataxorisi['oxima']['data']['val_to_place_lng']);
        } else {
          $poi_id_to  =intval($kataxorisi['oxima']['data']['val_from_id']);
          $poi_to_place_id=$kataxorisi['oxima']['data']['val_from_place_id'];
          $poi_to_place_formatted_address=$kataxorisi['oxima']['data']['val_from_place_formatted_address'];
          $poi_to_place_lat=floatval($kataxorisi['oxima']['data']['val_from_poi_map_latitude']);
          $poi_to_place_lng=floatval($kataxorisi['oxima']['data']['val_from_poi_map_longitude']);
          
          $poi_id_from=intval($kataxorisi['oxima']['data']['val_to_id']);
          $poi_from_place_id=$kataxorisi['oxima']['data']['val_to_place_id'];
          $poi_from_place_formatted_address=$kataxorisi['oxima']['data']['val_to_place_formatted_address'];
          $poi_from_place_lat=floatval($kataxorisi['oxima']['data']['val_to_place_lat']);
          $poi_from_place_lng=floatval($kataxorisi['oxima']['data']['val_to_place_lng']);
        }
      } else {
        if ($kataxorisi['transfer_outward_return']=='outward') { //outward
          $poi_id_to=intval($kataxorisi['oxima']['data']['val_from_id']);
          $poi_to_place_id=$kataxorisi['oxima']['data']['val_from_place_id'];
          $poi_to_place_formatted_address=$kataxorisi['oxima']['data']['val_from_place_formatted_address'];
          $poi_to_place_lat=floatval($kataxorisi['oxima']['data']['val_from_poi_map_latitude']);
          $poi_to_place_lng=floatval($kataxorisi['oxima']['data']['val_from_poi_map_longitude']);
          
          $poi_id_from  =intval($kataxorisi['oxima']['data']['val_to_id']);
          $poi_from_place_id=$kataxorisi['oxima']['data']['val_to_place_id'];
          $poi_from_place_formatted_address=$kataxorisi['oxima']['data']['val_to_place_formatted_address'];
          $poi_from_place_lat=floatval($kataxorisi['oxima']['data']['val_to_place_lat']);
          $poi_from_place_lng=floatval($kataxorisi['oxima']['data']['val_to_place_lng']);
        } else {
          $poi_id_from  =intval($kataxorisi['oxima']['data']['val_from_id']);
          $poi_from_place_id=$kataxorisi['oxima']['data']['val_from_place_id'];
          $poi_from_place_formatted_address=$kataxorisi['oxima']['data']['val_from_place_formatted_address'];
          $poi_from_place_lat=floatval($kataxorisi['oxima']['data']['val_from_poi_map_latitude']);
          $poi_from_place_lng=floatval($kataxorisi['oxima']['data']['val_from_poi_map_longitude']);
          
          $poi_id_to=intval($kataxorisi['oxima']['data']['val_to_id']);
          $poi_to_place_id=$kataxorisi['oxima']['data']['val_to_place_id'];
          $poi_to_place_formatted_address=$kataxorisi['oxima']['data']['val_to_place_formatted_address'];
          $poi_to_place_lat=floatval($kataxorisi['oxima']['data']['val_to_place_lat']);
          $poi_to_place_lng=floatval($kataxorisi['oxima']['data']['val_to_place_lng']);          
        }
        
        
      }
      
      $sqlUpdate.="poi_id_from=".$poi_id_from.",";
      $sqlUpdate.="poi_from_place_id='".$db_link->escape_string(trim_gks($poi_from_place_id))."',";
      $sqlUpdate.="poi_from_place_formatted_address='".$db_link->escape_string(trim_gks($poi_from_place_formatted_address))."',";
      $sqlUpdate.="poi_from_place_lat=".$poi_from_place_lat.",";
      $sqlUpdate.="poi_from_place_lng=".$poi_from_place_lng.",";
      
      
      
      $sqlUpdate.="poi_id_to=".$poi_id_to.",";
      $sqlUpdate.="poi_to_place_id='".$db_link->escape_string(trim_gks($poi_to_place_id))."',";
      $sqlUpdate.="poi_to_place_formatted_address='".$db_link->escape_string(trim_gks($poi_to_place_formatted_address))."',";
      $sqlUpdate.="poi_to_place_lat=".$poi_to_place_lat.",";
      $sqlUpdate.="poi_to_place_lng=".$poi_to_place_lng.",";

      
      $poi_diadromes_id=0;

      $apostasi_se_metra=0;
      if (isset($kataxorisi['oxima']['protasi'] ['pricelists']['proto']['transfer_pricelist_apostasi_se_metra'])) 
        $apostasi_se_metra=intval($kataxorisi['oxima']['protasi']['pricelists']['proto']['transfer_pricelist_apostasi_se_metra']);
      if ($apostasi_se_metra<=0 and isset($kataxorisi['oxima']['data']['val_distance']))
        $apostasi_se_metra=intval($kataxorisi['oxima']['data']['val_distance']);
      
      
      $diarkeia_se_lepta=0;
      if (isset($kataxorisi['oxima']['protasi'] ['pricelists']['proto']['transfer_pricelist_diarkeia_se_lepta'])) 
        $diarkeia_se_lepta=intval($kataxorisi['oxima']['protasi']['pricelists']['proto']['transfer_pricelist_diarkeia_se_lepta']);
      if ($diarkeia_se_lepta<=0 and isset($kataxorisi['oxima']['data']['val_duration']))
        $diarkeia_se_lepta=intval($kataxorisi['oxima']['data']['val_duration']);
        

      //gks_poi_diadromes
      if ($poi_id_from>111 and $poi_id_to>111) {
        $sql_poi_diadromi="select * from gks_poi_diadromes where poi_id_from=".$poi_id_from." and poi_id_to=".$poi_id_to;
        $result_poi_diadromi = $db_link->query($sql_poi_diadromi);  
        if (!$result_poi_diadromi) {
          debug_mail(false,'error sql',$sql_poi_diadromi);
          return array('success' => false, 'message' => base64_encode('sql error'));}
        if ($result_poi_diadromi->num_rows>=1) {
          $row_poi_diadromi = $result_poi_diadromi->fetch_assoc();
          $poi_diadromes_id=$row_poi_diadromi['id_poi_diadromes'];
          if ($diarkeia_se_lepta==0) $apostasi_se_metra=$row_poi_diadromi['poi_diadromes_apostasi_se_metra'];
          if ($diarkeia_se_lepta==0) $diarkeia_se_lepta=$row_poi_diadromi['poi_diadromes_diarkeia_se_lepta'];
        } else {
          $sql_poi_diadromi="insert into gks_poi_diadromes (
          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
          poi_id_from,poi_id_to,poi_diadromes_disable,
          poi_diadromes_apostasi_se_metra,poi_diadromes_diarkeia_se_lepta,
          poi_diadromes_directions
          ) values (
          now(),now(),2,2,'".$db_link->escape_string($gkIP)."',
          ".$poi_id_from.",".$poi_id_to.",0,
          ".$apostasi_se_metra.",".$diarkeia_se_lepta.",
          ''
          )";
          $result_poi_diadromi = $db_link->query($sql_poi_diadromi);  
          if (!$result_poi_diadromi) {
            debug_mail(false,'error sql',$sql_poi_diadromi);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }  
          
          $poi_diadromes_id = $db_link->insert_id; 
        }
      }
      
      
      $transfer_end=$transfer_start + ($diarkeia_se_lepta ==0 ? 60 : $diarkeia_se_lepta) * 60;
      $sqlUpdate.="transfer_start='".date('Y-m-d H:i:s',$transfer_start)."',";
      $sqlUpdate.="transfer_end='".date('Y-m-d H:i:s',$transfer_end)."',";
      $sqlUpdate.="duration_secs=".($transfer_end-$transfer_start).",";
      $sqlUpdate.="num_adults=".$kataxorisi['oxima']['data']['val_adults'].",";
      $sqlUpdate.="num_childs=".$kataxorisi['oxima']['data']['val_children'].",";
      $sqlUpdate.="num_babys=".$kataxorisi['oxima']['data']['val_infants'].",";
      
      $sqlUpdate.="poi_diadromes_id=".$poi_diadromes_id.",";
      $sqlUpdate.="apostasi_se_metra=".$apostasi_se_metra.",";
      $sqlUpdate.="diarkeia_se_lepta=".$diarkeia_se_lepta.",";
      if ($kataxorisi['transfer_outward_return']=='outward') { //outward
        
      }
      
      $sqlUpdate.="direction='".$direction."',";
      $sqlUpdate.="diarkeia_se_lepta=".$diarkeia_se_lepta.",";
      
      
      $rsrv_sms_text_message_price=0;
      $rsrv_sms_text_message_enable=intval($kataxorisi['oxima']['contact']['sms']);
      if ($rsrv_sms_text_message_enable!=1) $rsrv_sms_text_message_enable=0;
      if ($rsrv_sms_text_message_enable==1) 
        $rsrv_sms_text_message_price=floatval($kataxorisi['transfer_params']['transfer_sms_text_message_price']);
      
      $sqlUpdate.="rsrv_sms_text_message_enable=".$rsrv_sms_text_message_enable.",";
      if ($kataxorisi['transfer_outward_return']=='outward') { //outward
        $sqlUpdate.="rsrv_sms_text_message_price=".$rsrv_sms_text_message_price.",";
      } else {
        $sqlUpdate.="rsrv_sms_text_message_price=0,";
      }
      $rsrv_cancellation_protection_price=0;
      $rsrv_cancellation_protection_enable=intval($kataxorisi['oxima']['confirm']['cancellation_protection']);
      if ($rsrv_cancellation_protection_enable!=1) $rsrv_cancellation_protection_enable=0;
      if ($rsrv_cancellation_protection_enable==1) 
        $rsrv_cancellation_protection_price=floatval($kataxorisi['transfer_params']['transfer_cancellation_protection_price']);
      
      $sqlUpdate.="rsrv_cancellation_protection_enable=".$rsrv_cancellation_protection_enable.",";
      if ($kataxorisi['transfer_outward_return']=='outward') { //outward
        $sqlUpdate.="rsrv_cancellation_protection_price=".$rsrv_cancellation_protection_price.",";
      } else {
        $sqlUpdate.="rsrv_cancellation_protection_price=0,";
      }
      
      $sqlUpdate.="outward_from_pick_up_point='".$db_link->escape_string($kataxorisi['oxima']['contact']['outward_from_pick_up_point'])."',";
      
      $outward_from_pick_up_time=intval($kataxorisi['oxima']['contact']['outward_from_pick_up_time']);
      if ($outward_from_pick_up_time>0) $outward_from_pick_up_time="'".date('Y-m-d H:i:s',_time_user($outward_from_pick_up_time,1))."'"; else $outward_from_pick_up_time='null';
      $sqlUpdate.="outward_from_pick_up_time=".$outward_from_pick_up_time.",";
      
      $outward_from_pick_up_time_max=intval($kataxorisi['oxima']['contact']['outward_from_pick_up_time_max']);
      if ($outward_from_pick_up_time_max>0) $outward_from_pick_up_time_max="'".date('Y-m-d H:i:s',_time_user($outward_from_pick_up_time_max,1))."'"; else $outward_from_pick_up_time_max='null';
      $sqlUpdate.="outward_from_pick_up_time_max=".$outward_from_pick_up_time_max.",";
      
      $sqlUpdate.="outward_from_airline='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['outward_from_airline']))."',";
      $sqlUpdate.="outward_from_flight_number='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['outward_from_flight_number']))."',";
      $sqlUpdate.="outward_from_originating_airport='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['outward_from_originating_airport']))."',";

      $outward_from_flight_arrival_time=intval($kataxorisi['oxima']['contact']['outward_from_flight_arrival_time']);
      if ($outward_from_flight_arrival_time>0) $outward_from_flight_arrival_time="'".date('Y-m-d H:i:s',_time_user($outward_from_flight_arrival_time,1))."'"; else $outward_from_flight_arrival_time='null';
      $sqlUpdate.="outward_from_flight_arrival_time=".$outward_from_flight_arrival_time.",";
      
      $sqlUpdate.="outward_to_drop_off_point='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['outward_to_drop_off_point']))."',";
      $sqlUpdate.="outward_to_departure_airline='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['outward_to_departure_airline']))."',";
      $sqlUpdate.="outward_to_flight_number='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['outward_to_flight_number']))."',";
      
      $outward_to_flight_departure_time=intval($kataxorisi['oxima']['contact']['outward_to_flight_departure_time']);
      if ($outward_to_flight_departure_time>0) $outward_to_flight_departure_time="'".date('Y-m-d H:i:s',_time_user($outward_to_flight_departure_time,1))."'"; else $outward_to_flight_departure_time='null';
      $sqlUpdate.="outward_to_flight_departure_time=".$outward_to_flight_departure_time.",";
      
      $return_from_address_different=intval($kataxorisi['oxima']['contact']['return_from_address_different']);
      if ($return_from_address_different==0) {
        $return_from_pick_up_point='';
      } else {
        $return_from_address_different=1;
        $return_from_pick_up_point=trim_gks($kataxorisi['oxima']['contact']['return_from_pick_up_point']);
      }
      $sqlUpdate.="return_from_address_different=".$return_from_address_different.",";
      $sqlUpdate.="return_from_pick_up_point='".$db_link->escape_string(trim_gks($return_from_pick_up_point))."',";
      
      $return_from_pick_up_time=intval($kataxorisi['oxima']['contact']['return_from_pick_up_time']);
      if ($return_from_pick_up_time>0) $return_from_pick_up_time="'".date('Y-m-d H:i:s',_time_user($return_from_pick_up_time,1))."'"; else $return_from_pick_up_time='null';
      $sqlUpdate.="return_from_pick_up_time=".$return_from_pick_up_time.",";

      $return_from_pick_up_time_max=intval($kataxorisi['oxima']['contact']['return_from_pick_up_time_max']);
      if ($return_from_pick_up_time_max>0) $return_from_pick_up_time_max="'".date('Y-m-d H:i:s',_time_user($return_from_pick_up_time_max,1))."'"; else $return_from_pick_up_time_max='null';
      $sqlUpdate.="return_from_pick_up_time_max=".$return_from_pick_up_time_max.",";

      $sqlUpdate.="return_from_airline='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['return_from_airline']))."',";
      $sqlUpdate.="return_from_flight_number='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['return_from_flight_number']))."',";
      $sqlUpdate.="return_from_originating_airport='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['return_from_originating_airport']))."',";

      $return_from_flight_arrival_time=intval($kataxorisi['oxima']['contact']['return_from_flight_arrival_time']);
      if ($return_from_flight_arrival_time>0) $return_from_flight_arrival_time="'".date('Y-m-d H:i:s',_time_user($return_from_flight_arrival_time,1))."'"; else $return_from_flight_arrival_time='null';
      $sqlUpdate.="return_from_flight_arrival_time=".$return_from_flight_arrival_time.",";
      
      $sqlUpdate.="return_to_airline='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['return_to_airline']))."',";
      $sqlUpdate.="return_to_flight_number='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['return_to_flight_number']))."',";

      $return_to_flight_departure_time=intval($kataxorisi['oxima']['contact']['return_to_flight_departure_time']);
      if ($return_to_flight_departure_time>0) $return_to_flight_departure_time="'".date('Y-m-d H:i:s',_time_user($return_to_flight_departure_time,1))."'"; else $return_to_flight_departure_time='null';
      $sqlUpdate.="return_to_flight_departure_time=".$return_to_flight_departure_time.",";

      $return_to_address_different=intval($kataxorisi['oxima']['contact']['return_to_address_different']);
      if ($return_to_address_different==0) {
        $return_to_drop_off_point='';
      } else {
        $return_to_address_different=1;
        $return_to_drop_off_point=trim_gks($kataxorisi['oxima']['contact']['return_to_drop_off_point']);
      }
      $sqlUpdate.="return_to_address_different=".$return_to_address_different.",";
      $sqlUpdate.="return_to_drop_off_point='".$db_link->escape_string(trim_gks($return_to_drop_off_point))."',";

      //echo '<pre>dddddddd '.$sqlUpdate;die();
      //return array('success' => false, 'message' => base64_encode('<pre>sqlUpdate '.print_r($kataxorisi,true)));
      
    } else if ($table_into=='gks_hotel_reservation') {
      $sqlUpdate.="reservation_date='".$db_link->escape_string($data['gks_date_created'])."',";
      $sqlUpdate.="reservation_journal_id=".$id_acc_journal.",";
      $sqlUpdate.="reservation_seira_id=".$id_acc_seira.",";
      $sqlUpdate.="reservation_seira_code='".$db_link->escape_string($seira_code)."',";
      $sqlUpdate.="reservation_status='".$db_link->escape_string($order_state)."',";
      //kostas
      
      $hotel_id=0;
      foreach ($data['gks_items'] as $value) {
        if (isset($value['gks_hotel_reservation_room_data'])) {
          if (isset($value['gks_hotel_reservation_room_data']['id_hotel'])) {
            $hotel_id=intval($value['gks_hotel_reservation_room_data']['id_hotel']);
            break;
          }
        }
      } 
      $sqlUpdate.="hotel_id=".$hotel_id.",";
      
      $check_in=$kataxorisi['first_room']['check_in'].' '.$kataxorisi['hotel_params']['hotel_default_checkin'].':00';
      $check_in=strtotime($check_in);
      
      $check_out=$kataxorisi['first_room']['check_out'].' '.$kataxorisi['hotel_params']['hotel_default_checkout'].':00';
      $check_out=strtotime($check_out) + 24*60*60;
            
      $sqlUpdate.="check_in='".date('Y-m-d H:i:s',$check_in)."',";
      $sqlUpdate.="check_out='".date('Y-m-d H:i:s',$check_out)."',";
      $sqlUpdate.="num_days=".$kataxorisi['first_room']['num_days'].",";
      $sqlUpdate.="num_adults=".$kataxorisi['num_adults'].",";
      $sqlUpdate.="num_childs=".$kataxorisi['num_childs'].",";
      $sqlUpdate.="childs_ages_list='".$db_link->escape_string(json_encode($kataxorisi['childs_ages_list']))."',";
      $sqlUpdate.="num_child_kounies=".$kataxorisi['num_child_kounies'].",";
      $sqlUpdate.="num_extra_beds=".$kataxorisi['num_extra_beds'].",";
      $sqlUpdate.="rooms_plithos=".count($kataxorisi['rooms']).",";
      
       
    } else if ($table_into=='gks_orders') {
      $sqlUpdate.="order_date='".$db_link->escape_string($data['gks_date_created'])."',";
      $sqlUpdate.="order_journal_id=".$id_acc_journal.",";
      $sqlUpdate.="order_seira_id=".$id_acc_seira.",";
      $sqlUpdate.="order_seira_code='".$db_link->escape_string($seira_code)."',";
      $sqlUpdate.="order_state='".$db_link->escape_string($order_state)."',";
    
      $sqlUpdate.="company_id=".$eshop['company_id'].",";
      $sqlUpdate.="company_sub_id=".$eshop['company_sub_id'].",";
      
    } else if ($table_into=='gks_acc_inv') {
      $sqlUpdate.="inv_date='".$db_link->escape_string($data['gks_date_created'])."',";
      $sqlUpdate.="inv_acc_journal_id=".$id_acc_journal.",";
      $sqlUpdate.="inv_acc_seira_id=".$id_acc_seira.",";
      $sqlUpdate.="inv_acc_seira_code='".$db_link->escape_string($seira_code)."',";
      $sqlUpdate.="inv_state='".$db_link->escape_string($order_state)."',";
  
      $sqlUpdate.="company_id=".$eshop['company_id'].",";
      $sqlUpdate.="company_sub_id=".$eshop['company_sub_id'].",";
      
    }
    $sqlUpdate.="user_id=".$user_id.",";
    
    
    
    if ($table_into=='gks_transfer_reservation') {
      $sqlUpdate.="user_email='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['email']))."',";
      $sqlUpdate.="user_first_name='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['name']))."',";
      $sqlUpdate.="user_last_name='".$db_link->escape_string(trim_gks($kataxorisi['oxima']['contact']['surname']))."',";
      
      $phone_code='';
      $country = trim_gks($kataxorisi['oxima']['contact']['country']);
      if ($country!='') {
        $sql_country="select id_country,phone_code from gks_country where country_initials like '".$db_link->escape_string($country)."'";
        $result_country = $db_link->query($sql_country);  
        if (!$result_country) {
          debug_mail(false,'error sql',$sql_country);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        if ($result_country->num_rows>0) {
          $row_country = $result_country->fetch_assoc();
          $phone_code=trim_gks($row_country['phone_code']);      
        }
      }
      $mobile=trim_gks($kataxorisi['oxima']['contact']['mobile']);
      if ($phone_code!='') $mobile=trim_gks('('.$phone_code.') '.$mobile);
      $sqlUpdate.="user_mobile='".$db_link->escape_string($mobile)."',";
      
      
    } else {
      $sqlUpdate.="user_email='".(isset($data['billing']['email']) ? $db_link->escape_string(trim_gks($data['billing']['email'])) : '')."',";
      $sqlUpdate.="user_first_name='".(isset($data['billing']['first_name']) ? $db_link->escape_string(trim_gks($data['billing']['first_name'])) : '')."',";
      $sqlUpdate.="user_last_name='".(isset($data['billing']['last_name']) ? $db_link->escape_string(trim_gks($data['billing']['last_name'])) : '')."',";
      $sqlUpdate.="user_mobile='".(isset($data['billing']['phone']) ? $db_link->escape_string(trim_gks($data['billing']['phone'])) : '')."',";
    }
    $sqlUpdate.="user_lang='".$db_link->escape_string($user_lang)."',";
    
    if ($table_into=='gks_transfer_reservation') {
      $sqlUpdate.="parastatiko=".$parastatiko.",";
    } else if ($table_into=='gks_hotel_reservation') {
      $sqlUpdate.="parastatiko=".$parastatiko.",";
    } else if ($table_into=='gks_orders') {
      $sqlUpdate.="parastatiko=".$parastatiko.",";
    } else if ($table_into=='gks_acc_inv') {
      
    }
    
    
    $sqlUpdate.="eponimia='".$db_link->escape_string($eponimia)."',";
    $sqlUpdate.="title='".$db_link->escape_string($title)."',";
    $sqlUpdate.="afm='".$db_link->escape_string($afm)."',";
    $sqlUpdate.="doy='".$db_link->escape_string($doy)."',";
    $sqlUpdate.="epaggelma='".$db_link->escape_string($epaggelma)."',";
    
    
    $customer_note=''; if (isset($data['customer_note'])) $customer_note=trim_gks($data['customer_note']);
    
    if ($table_into=='gks_transfer_reservation') {
      $sqlUpdate.="user_notes='".$db_link->escape_string($customer_note)."',";
    } else if ($table_into=='gks_hotel_reservation') {
      $sqlUpdate.="user_notes='".$db_link->escape_string($customer_note)."',";
    } else if ($table_into=='gks_orders') {
      $sqlUpdate.="note_doc='".$db_link->escape_string($customer_note)."',";
    } else if ($table_into=='gks_acc_inv') {
      $sqlUpdate.="note_doc='".$db_link->escape_string($customer_note)."',";
      $sqlUpdate.="aade_skopos_diakinisis_id=1,";
    }
    
    
    //return array('success' => false, 'message' => base64_encode('<pre>sqlUpdate '.$sqlUpdate));
    
    //to state einai i periferia
    $ma_odos1=(isset($data['billing']['address_1']) ? trim_gks($data['billing']['address_1']) : '');
    $ma_odos2=(isset($data['billing']['address_2']) ? trim_gks($data['billing']['address_2']) : '');
    $ma_odos='';
    if ($ma_odos1!='' and $ma_odos2!='') $ma_odos=$ma_odos1.', '.$ma_odos2;
    else if ($ma_odos1!='' and $ma_odos2=='') $ma_odos=$ma_odos1;
    else if ($ma_odos1=='' and $ma_odos2!='') $ma_odos=$ma_odos2;
    $sqlUpdate.="ma_odos='".$db_link->escape_string($ma_odos)."',";
    $sqlUpdate.="ma_poli='".(isset($data['billing']['city']) ? $db_link->escape_string(trim_gks($data['billing']['city'])) : '')."',";
    $sqlUpdate.="ma_tk='".(isset($data['billing']['postcode']) ? $db_link->escape_string(trim_gks($data['billing']['postcode'])) : '')."',";
  
    $ma_country_id=0;
    if (isset($data['billing']['country']) and $data['billing']['country']!='') {
      $sql="SELECT id_country FROM gks_country WHERE country_initials='".$db_link->escape_string($data['billing']['country'])."'";
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => base64_encode('sql error'));}
      if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
        $ma_country_id=intval($row['id_country']);
        $sqlUpdate.="ma_country_id=".$ma_country_id.",";  
      }
    }
    $ma_nomos_id=0;
    if (isset($data['billing']['state']) and substr($data['billing']['state'],0,2)=='GR') {
      $woo_nomos=Intval(substr($data['billing']['state'],2));
      if ($woo_nomos>0 and $ma_country_id==91 and $woo_nomos>=1 and $woo_nomos<=59) {
        $ma_nomos_id=$woo_nomos;
        $sqlUpdate.="ma_nomos_id=".$ma_nomos_id.",";  
      }
    }    
    
    $is_alli_adress=false;
    if ($is_alli_adress==false and isset($data['billing']['first_name']) and isset($data['shipping']['first_name']) and
        trim_gks($data['billing']['first_name']) != trim_gks($data['shipping']['first_name'])) {$is_alli_adress=true;}
    if ($is_alli_adress==false and isset($data['billing']['last_name']) and isset($data['shipping']['last_name']) and
        trim_gks($data['billing']['last_name']) != trim_gks($data['shipping']['last_name'])) {$is_alli_adress=true;}
    if ($is_alli_adress==false and isset($data['billing']['company']) and isset($data['shipping']['company']) and
        trim_gks($data['billing']['company']) != trim_gks($data['shipping']['company'])) {$is_alli_adress=true;}
    if ($is_alli_adress==false and isset($data['billing']['address_1']) and isset($data['shipping']['address_1']) and
        trim_gks($data['billing']['address_1']) != trim_gks($data['shipping']['address_1'])) {$is_alli_adress=true;}
    if ($is_alli_adress==false and isset($data['billing']['address_2']) and isset($data['shipping']['address_2']) and
        trim_gks($data['billing']['address_2']) != trim_gks($data['shipping']['address_2'])) {$is_alli_adress=true;}
    if ($is_alli_adress==false and isset($data['billing']['city']) and isset($data['shipping']['city']) and
        trim_gks($data['billing']['city']) != trim_gks($data['shipping']['city'])) {$is_alli_adress=true;}
    if ($is_alli_adress==false and isset($data['billing']['state']) and isset($data['shipping']['state']) and
        trim_gks($data['billing']['state']) != trim_gks($data['shipping']['state'])) {$is_alli_adress=true;}
    if ($is_alli_adress==false and isset($data['billing']['postcode']) and isset($data['shipping']['postcode']) and
        trim_gks($data['billing']['postcode']) != trim_gks($data['shipping']['postcode'])) {$is_alli_adress=true;}
    if ($is_alli_adress==false and isset($data['billing']['country']) and isset($data['shipping']['country']) and
        trim_gks($data['billing']['country']) != trim_gks($data['shipping']['country'])) {$is_alli_adress=true;}
    if ($is_alli_adress==false and isset($data['billing']['state']) and isset($data['shipping']['state']) and
        trim_gks($data['billing']['state']) != trim_gks($data['shipping']['state'])) {$is_alli_adress=true;}
    
    $destination_data_name='';
    $destination_data_phone='';
    $destination_data_odos='';
    $destination_data_orofos='';
    $destination_data_perioxi='';
    $destination_data_poli='';
    $destination_data_tk='';
    $destination_data_country_id=0;
    $destination_data_nomos_id=0;
    
    //echo 'is_alli_adress:'.$is_alli_adress."\n";
    if ($table_into=='gks_transfer_reservation') {
      $id_users_extra_address=-1;
    } else if ($table_into=='gks_hotel_reservation') {
      $id_users_extra_address=-1;
    } else {
      
      if ($is_alli_adress==false) {
        $id_users_extra_address=-1;
        
        $sqlUpdate.="address_extra=-1,";  
      } else {
        $id_users_extra_address=0;
        
        $shipping_first_name=''; if (isset($data['shipping']['first_name'])) $shipping_first_name=trim_gks($data['shipping']['first_name']);
        $shipping_last_name='';  if (isset($data['shipping']['last_name']))  $shipping_last_name= trim_gks($data['shipping']['last_name']);
        
        $ea_name='';
        if ($shipping_first_name!='' and $shipping_last_name!='') $ea_name=$shipping_first_name.', '.$shipping_last_name;
        else if ($shipping_first_name!='' and $shipping_last_name=='') $ea_name=$shipping_first_name;
        else if ($shipping_first_name=='' and $shipping_last_name!='') $ea_name=$shipping_last_name;
        
        $ea_phone='';
        if (isset($data['shipping']['phone'])) $ea_phone=trim_gks($data['shipping']['phone']);
        
        $ea_odos='';
        if ((isset($data['shipping']['address_1']) and trim_gks($data['shipping']['address_1'])!='') or 
            (isset($data['shipping']['address_2']) and trim_gks($data['shipping']['address_2'])!='')) {
          $ea_odos1=(isset($data['shipping']['address_1']) ? trim_gks($data['shipping']['address_1']) : '');
          $ea_odos2=(isset($data['shipping']['address_2']) ? trim_gks($data['shipping']['address_2']) : '');
          if ($ea_odos1!='' and $ea_odos2!='') $ea_odos=$ea_odos1.', '.$ea_odos2;
          else if ($ea_odos1!='' and $ea_odos2=='') $ea_odos=$ea_odos1;
          else if ($ea_odos1=='' and $ea_odos2!='') $ea_odos=$ea_odos2;
        }
        
        $ea_poli='';
        if (isset($data['shipping']['city'])) $ea_poli=trim_gks($data['shipping']['city']);
        
        $ea_tk='';
        if (isset($data['shipping']['postcode'])) $ea_tk=trim_gks($data['shipping']['postcode']);
    
        $ea_country_id=0;
        if (isset($data['shipping']['country']) and $data['shipping']['country']!='') {
          $sql="SELECT id_country FROM gks_country WHERE country_initials='".$db_link->escape_string($data['shipping']['country'])."'";
          $result = $db_link->query($sql);
          if (!$result) {debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => base64_encode('sql error'));}
          if ($result->num_rows>0) {
            $row = $result->fetch_assoc();
            $ea_country_id=intval($row['id_country']);
          }
        }
        $ea_nomos_id=0;
        if (isset($data['shipping']['state']) and substr($data['shipping']['state'],0,2)=='GR') {
          $woo_nomos=Intval(substr($data['shipping']['state'],2));
          if ($woo_nomos>0 and $ea_country_id==91) {
            $ea_nomos_id=$woo_nomos;
          }
        }

        
        $sql="select id_users_extra_address from gks_users_extra_address
        where user_id=".$user_id."
        and ea_odos like '".$db_link->escape_string($ea_odos)."'
        and ea_poli like '".$db_link->escape_string($ea_poli)."'
        and ea_tk   like '".$db_link->escape_string($ea_tk)."'
        and ea_nomos_id=".$ea_nomos_id."
        and ea_country_id=".$ea_country_id;
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));}
        if ($result->num_rows>0) {
          $row = $result->fetch_assoc();
          $id_users_extra_address=$row['id_users_extra_address'];
        } else {
          $sql="insert into gks_users_extra_address (
          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,is_woo_delivery,
          ea_name,ea_phone,ea_odos,ea_poli,ea_tk,ea_nomos_id,ea_country_id,".$table_idr."
          ) values (
          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$user_id.",1,
          '".$db_link->escape_string($ea_name)."',
          '".$db_link->escape_string($ea_phone)."',
          '".$db_link->escape_string($ea_odos)."',
          '".$db_link->escape_string($ea_poli)."',
          '".$db_link->escape_string($ea_tk)."',
          ".$ea_nomos_id.",
          ".$ea_country_id.",
          ".$id_order."
          )";
          $result = $db_link->query($sql);
          if (!$result) {debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => base64_encode('sql error'));}
          
          $id_users_extra_address=$db_link->insert_id;
          //echo $sql;
          
        }
        
        
        
        
        //echo '|'.$id_users_extra_address.'|'.$ea_name.'|'.$ea_phone.'|'.$ea_odos.'|'.$ea_poli.'|'.$ea_tk.'|'.$ea_country_id.'|'."\n";
    
        
        $sqlUpdate.="address_extra=".$id_users_extra_address.",";  
        
        
        if ($parastatiko >= 0) {
          $destination_data_name=$ea_name;
          $destination_data_phone=$ea_phone;
          $destination_data_odos=$ea_odos;
          $destination_data_orofos='';
          $destination_data_perioxi='';
          $destination_data_poli=$ea_poli;
          $destination_data_tk=$ea_tk;
          $destination_data_country_id=$ea_country_id;
          $destination_data_nomos_id=$ea_nomos_id; 
        }
      }
      $sqlUpdate.="destination_data_name='".$db_link->escape_string($destination_data_name)."',";
      $sqlUpdate.="destination_data_phone='".$db_link->escape_string($destination_data_phone)."',";
      $sqlUpdate.="destination_data_odos='".$db_link->escape_string($destination_data_odos)."',";
      $sqlUpdate.="destination_data_orofos='".$db_link->escape_string($destination_data_orofos)."',";
      $sqlUpdate.="destination_data_perioxi='".$db_link->escape_string($destination_data_perioxi)."',";
      $sqlUpdate.="destination_data_poli='".$db_link->escape_string($destination_data_poli)."',";
      $sqlUpdate.="destination_data_tk='".$db_link->escape_string($destination_data_tk)."',";
      $sqlUpdate.="destination_data_country_id=".$destination_data_country_id.",";
      $sqlUpdate.="destination_data_nomos_id=".$destination_data_nomos_id.",";
      
    }
    
    //echo '<pre>';print_r($kataxorisi['oxima']['data']);die();
    
    
    $fiscal_position_id=1;
    if (isset($kataxorisi['oxima']['data']['val_fiscal_position_id'])) {
      if ($kataxorisi['oxima']['data']['val_fiscal_position_id']==4) {
        $fiscal_position_id=4;
      }
    }
    
    $pricelist_id=1;
    $sqlUpdate.="fiscal_position_id=".$fiscal_position_id.",";  
    $sqlUpdate.="pricelist_id=".$pricelist_id.",";  
    $sqlUpdate.="tropos_apostolis=".$tropos_apostolis.",";  
    $sqlUpdate.="tropos_pliromis=".$tropos_pliromis.",";  
    
    //return array('success' => false, 'message' => base64_encode('<pre>sqlUpdate '.$sqlUpdate));
    
    
    $prices_include_tax=false;
    if (isset($data['prices_include_tax']) and intval($data['prices_include_tax'])==1) $prices_include_tax=true; 
    
    $kostos_apostolis=0;
    if ($eshop['acc_inv_product_shipping']<=0) {
      if (isset($data['gks_shipping']) and is_array($data['gks_shipping'])) {
        foreach ($data['gks_shipping'] as $myshipping) {
          if (isset($myshipping['total'])) $kostos_apostolis+=floatval($myshipping['total']);
          if (isset($myshipping['total_tax'])) $kostos_apostolis+=floatval($myshipping['total_tax']);
        }
      } else {
        if (isset($data['shipping_total'])) $kostos_apostolis+=floatval($data['shipping_total']);
        if (isset($data['shipping_tax'])) $kostos_apostolis+=floatval($data['shipping_tax']);
      }
    } else {
      //will add item to basket
      if (isset($data['gks_shipping']) and is_array($data['gks_shipping'])) {
        foreach ($data['gks_shipping'] as $myshipping) {
          $total=0;    if (isset($myshipping['total']))     $total    =floatval($myshipping['total']);
          $total_tax=0;if (isset($myshipping['total_tax'])) $total_tax=floatval($myshipping['total_tax']);
          
          $tax_class_hname='';
          $product_fpa_ejeresi_id=0;
          if ($total_tax<=0) {
            $tax_class_hname=$eshop['tax_class_xorisfpa'];
            $sql="SELECT product_fpa_ejeresi_id FROM gks_eshop_products WHERE id_product=".$eshop['acc_inv_product_shipping'];
            $result = $db_link->query($sql);
            if (!$result) {debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => base64_encode('sql error'));}
            if ($result->num_rows>0) {
              $row = $result->fetch_assoc();
              $product_fpa_ejeresi_id=intval($row['product_fpa_ejeresi_id']);
            }
          } else {
            
            
            $corsc_id=0;//company or sub company id
            $corsc_f='';
            $corsc_fs='';
            
            //if ($table_into=='gks_transfer_reservation') {
            //} else if ($table_into=='gks_hotel_reservation') {
            //} else if ($table_into=='gks_orders' or $table_into=='gks_acc_inv') {
              $corsc_id=$eshop['company_id'];
              if (isset($eshop['company_sub_id']) and $eshop['company_sub_id']>0) {
                $corsc_id=$eshop['company_sub_id'];
                $corsc_f='_sub';$corsc_fs='_subs';
              }
            //}
    
            $sql="SELECT fpa_base_id
            FROM gks_company".$corsc_fs."_basefpa 
            LEFT JOIN gks_eshop_fpa ON gks_company".$corsc_fs."_basefpa.fpa_id = gks_eshop_fpa.id_fpa
            WHERE company".$corsc_f."_id=".$corsc_id."
            and gks_company".$corsc_fs."_basefpa.fpa_id>0
            ORDER BY Abs(fpa_pososto*".number_format($total,8,'.','')."-".number_format($total_tax,8,'.','').")";
            //echo '<pre>aaaaaaaa ';echo $sql;echo '</pre>'; //die();
            $result = $db_link->query($sql);
            if (!$result) {debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => base64_encode('sql error'));}
            $product_fpa_ejeresi_id=0;
            if ($result->num_rows>0) {
              $row = $result->fetch_assoc();
              $fpa_base_id=$row['fpa_base_id'];
              if ($fpa_base_id==1001) //Kanonikos
                $tax_class_hname=$eshop['tax_class_basikos'];
              else if ($fpa_base_id==1002) //Meiomenos
                $tax_class_hname=$eshop['tax_class_meiomenos'];
              else if ($fpa_base_id==1003) //Ypermeiomenos
                $tax_class_hname=$eshop['tax_class_ypermeiomenos'];
              else if ($fpa_base_id==1005) //Yper-ypermeiomenos
                $tax_class_hname=$eshop['tax_class_yperypermeiomenos'];
              else if ($fpa_base_id==1004) //No FPA ---
                $tax_class_hname=$eshop['tax_class_xorisfpa'];
            }
          }
          
  
          
          $data['gks_items'][]=array(
            'is_shipping_or_fee'=> true,
            'product_fpa_ejeresi_id'=>$product_fpa_ejeresi_id,
            'id'=>0,
            'product_id' => $eshop['acc_inv_product_shipping'],
            'variation_id' => 0,
            'quantity' => 1,
            'product_name' => $myshipping['name'],
            'sku' => '',
            'total_org'=> $total,
            'total' => $total,
            'total_tax' => $total_tax,
            'tax_class_hname' => $tax_class_hname,
          );
          //print_r($data['gks_items']);die();
        }
      }
    }
    $sqlUpdate.="kostos_apostolis=".number_format($kostos_apostolis,2,'.','').",";  
    
    
    $kostos_pliromis=0;
    if ($eshop['acc_inv_product_fees']<=0) {
      if (isset($data['gks_fees']) and is_array($data['gks_fees'])) {
        foreach ($data['gks_fees'] as $myfee) {
          if (isset($myfee['total'])) $kostos_pliromis+=floatval($myfee['total']);
          if (isset($myfee['total_tax'])) $kostos_pliromis+=floatval($myfee['total_tax']);
        }
      }
    } else {
      //will add item to basket
      if (isset($data['gks_fees']) and is_array($data['gks_fees'])) {
        foreach ($data['gks_fees'] as $myfee) {
          $total=0;    if (isset($myfee['total']))     $total    =floatval($myfee['total']);
          $total_tax=0;if (isset($myfee['total_tax'])) $total_tax=floatval($myfee['total_tax']);
          
          $tax_class_hname='';
          $product_fpa_ejeresi_id=0;
          if ($total_tax<=0) {
            $tax_class_hname=$eshop['tax_class_xorisfpa'];
            $sql="SELECT product_fpa_ejeresi_id FROM gks_eshop_products WHERE id_product=".$eshop['acc_inv_product_fees'];
            $result = $db_link->query($sql);
            if (!$result) {debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => base64_encode('sql error'));}
            if ($result->num_rows>0) {
              $row = $result->fetch_assoc();
              $product_fpa_ejeresi_id=intval($row['product_fpa_ejeresi_id']);
            }
          } else {
            
            
            $corsc_id=0;//company or sub company id
            $corsc_f='';
            $corsc_fs='';
            
            //if ($table_into=='gks_transfer_reservation') {
            //} else if ($table_into=='gks_hotel_reservation') {
            //} else if ($table_into=='gks_orders' or $table_into=='gks_acc_inv') {
              $corsc_id=$eshop['company_id'];
              if (isset($eshop['company_sub_id']) and $eshop['company_sub_id']>0) {
                $corsc_id=$eshop['company_sub_id'];
                $corsc_f='_sub';$corsc_fs='_subs';
              }
            //}
    
            $sql="SELECT fpa_base_id
            FROM gks_company".$corsc_fs."_basefpa 
            LEFT JOIN gks_eshop_fpa ON gks_company".$corsc_fs."_basefpa.fpa_id = gks_eshop_fpa.id_fpa
            WHERE company".$corsc_f."_id=".$corsc_id."
            ORDER BY Abs(fpa_pososto*".number_format($total,8,'.','')."-".number_format($total_tax,8,'.','').")";
            //echo '<pre>';echo $sql;    die();
            
            $result = $db_link->query($sql);
            if (!$result) {debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => base64_encode('sql error'));}
            $product_fpa_ejeresi_id=0;
            if ($result->num_rows>0) {
              $row = $result->fetch_assoc();
              $fpa_base_id=$row['fpa_base_id'];
              if ($fpa_base_id==1001) //Kanonikos
                $tax_class_hname=$eshop['tax_class_basikos'];
              else if ($fpa_base_id==1002) //Meiomenos
                $tax_class_hname=$eshop['tax_class_meiomenos'];
              else if ($fpa_base_id==1003) //Ypermeiomenos
                $tax_class_hname=$eshop['tax_class_ypermeiomenos'];
              else if ($fpa_base_id==1005) //Yper-ypermeiomenos
                $tax_class_hname=$eshop['tax_class_yperypermeiomenos'];                
              else if ($fpa_base_id==1004) //No FPA
                $tax_class_hname=$eshop['tax_class_xorisfpa'];
            }
          }
          
  
          
          $data['gks_items'][]=array(
            'is_shipping_or_fee'=> true,
            'product_fpa_ejeresi_id'=>$product_fpa_ejeresi_id,
            'id'=>0,
            'product_id' => $eshop['acc_inv_product_fees'],
            'variation_id' => 0,
            'quantity' => 1,
            'product_name' => $myfee['name'],
            'sku' => '',
            'total_org'=> $total,
            'total' => $total,
            'total_tax' => $total_tax,
            'tax_class_hname' => $tax_class_hname,
          );
          //print_r($data['gks_items']);die();
        }
      }
      
      
    }
    $sqlUpdate.="kostos_pliromis=".number_format($kostos_pliromis,2,'.','').","; 
  
    //return array('success' => false, 'message' => base64_encode('<pre>sqlUpdate '.$sqlUpdate));
    
    //print_r($data['gks_items']);
    
  //  $gks_price_fpa=0; if (isset($data['gks_cart_tax'])) $gks_price_fpa=floatval($data['gks_cart_tax']);
  //  $gks_price_net=0; if (isset($data['gks_subtotal'])) $gks_price_net=floatval($data['gks_subtotal']);
  //  
  //  $gks_price_total=$gks_price_netfpa;
    
    $gks_price_net=0;
    $gks_price_fpa=0;
    $gks_price_netfpa=0;
    $gks_price_total=0;
    
    if ($table_into=='gks_transfer_reservation') {
      foreach ($data['gks_items'] as $item) {
        if ($item['gks_popsicle_transfer_data']['data']['guid']==$kataxorisi['guid']) {
          if (isset($item['total'])) $gks_price_net+=floatval($item['total']);
          if (isset($item['total_tax'])) $gks_price_fpa+=floatval($item['total_tax']);
        }
      }
      $gks_price_netfpa=$gks_price_net+$gks_price_fpa;
      $gks_price_total=$gks_price_netfpa;
    } else if ($table_into=='gks_hotel_reservation') {
      foreach ($data['gks_items'] as &$item) {
        if ($item['gks_hotel_reservation_room_data']['guid']==$kataxorisi['guid']) {
          //if (isset($item['total'])) $gks_price_net+=floatval($item['total']);
          if (isset($item['total_tax'])) $gks_price_fpa+=floatval($item['total_tax']);
          if (isset($item['total_with_tax_round'])) $gks_price_total+=floatval($item['total_with_tax_round']);
          if (isset($item['total_tax_round']) and isset($item['total_with_tax_round'])) {
            $item['total']=$item['total_with_tax_round']-$item['total_tax_round'];
          }
        }
      }
      unset($item);
      
      $gks_price_net=$gks_price_total-$gks_price_fpa;
      $gks_price_netfpa=$gks_price_net+$gks_price_fpa;


    } else {
      foreach ($data['gks_items'] as $item) {
        if (isset($item['total'])) $gks_price_net+=floatval($item['total']);
        if (isset($item['total_tax'])) $gks_price_fpa+=floatval($item['total_tax']);
      }
      $gks_price_netfpa=$gks_price_net+$gks_price_fpa;
      $gks_price_total=$gks_price_netfpa;
    }
    
    
    //$sqlUpdate.="gks_price_original_net=".number_format($gks_price_net,2,'.','').",";  
    $sqlUpdate.="gks_price_net=".number_format($gks_price_net,2,'.','').",";  
    $sqlUpdate.="gks_price_fpa=".number_format($gks_price_fpa,2,'.','').",";  
    $sqlUpdate.="gks_price_netfpa=".number_format($gks_price_netfpa,2,'.','').",";  
    $sqlUpdate.="gks_price_total=".number_format($gks_price_total,2,'.','').",";  
    
    //echo '|'.$gks_price_net.'|'.$gks_price_fpa.'|'.$gks_price_netfpa.'|'.$gks_price_total.'|'."\n";
    
    if ($table_into!='gks_hotel_reservation' and $table_into!='gks_transfer_reservation') {
      $sqlUpdate.="warehouses_id_from=".$warehouses_id_from.",";  
      $sqlUpdate.="warehouses_id_to=".$warehouses_id_to.",";  
    }
  
  
    if ($table_into=='gks_transfer_reservation') {
      if ($kataxorisi['transfer_outward_return']=='outward') { //outward
        
        $send_to_other_system_ids[]=$id_order;
        if ($order_state==='080confirm') {
          $transfer_auto_mydata_email[]=$id_order;
        }
        
      } else {
        //return array('success' => false, 'message' => base64_encode('<pre>kataxorisi '.$kat_id."\n".print_r($kat_id_ids,true)."\n".print_r($kataxorisi,true)));
        $sqlUpdate.="is_return_transfer_for_id=".$kat_id_ids[$kat_id-1].","; 
      }
      
    }
    
    $sqlUpdate=substr($sqlUpdate, 0, strlen($sqlUpdate)-1);
    
    //return array('success' => false, 'message' => base64_encode('<pre>sqlUpdate '.$sqlUpdate));
    $sql="update ".$table_into." set ".$sqlUpdate." where ".$table_id."=".$id_order;

    //echo '<pre>ssssssssssss '.$sql; die();


    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => base64_encode('sql error'));}  
    
    $kat_id_ids[$kat_id]=$id_order;
    
  
//    $db_link->query("update gks_async_queue set status='pending' where guid='5b7e25817d4ab7b9798e3a65f06c618d'"); 
//    echo '<pre>';
//    echo $id_acc_journal."\n";
//    echo $id_acc_seira."\n";
//    echo $seira_code."\n";
//    echo $acc_eidos_parastatikou_whi_id."\n";
//    echo $warehouses_id_from."\n";
//    echo $warehouses_id_to."\n";
//    echo $user_lang."\n";
//    echo $user_id."\n";
//    print_r($eshop);
//    print_r($data);die();
    
      
    //return array('success' => false, 'message' => base64_encode('<pre>sqlUpdate '.$sqlUpdate));
    
    unset($mybasketarray);
    gks_mybasketarray_create($mybasketarray);
    
    if ($table_into=='gks_transfer_reservation') {
      $mybasketarray['from']='transfer_reservation';
    } else if ($table_into=='gks_hotel_reservation') {
      $mybasketarray['from']='reservation';
    } else if ($table_into=='gks_orders') {
      $mybasketarray['from']='order';
    } else if ($table_into=='gks_acc_inv') {
      $mybasketarray['from']='acc_inv';
    }    
    $mybasketarray['id_object'] = $id_order;
    $mybasketarray['company_id']=intval($eshop['company_id']);
    $mybasketarray['company_sub_id']=intval($eshop['company_sub_id']);
    
    if ($table_into=='gks_orders' or $table_into=='gks_acc_inv') {
      $mybasketarray[($table_into == 'gks_orders' ? 'order_journal_id' : 'inv_acc_journal_id')]=intval($id_acc_journal);
      $mybasketarray[($table_into == 'gks_orders' ? 'order_seira_id'   : 'inv_acc_seira_id')]=intval($id_acc_seira);
      $mybasketarray[($table_into == 'gks_orders' ? 'order_state'      : 'inv_state')]=trim_gks($order_state);
      $mybasketarray[($table_into == 'gks_orders' ? 'order_date'       : 'inv_date')] = $data['gks_date_created'];
    }
    
    
    $mybasketarray['user']['user_id']=$user_id;
    $mybasketarray['user']['first_name']=(isset($data['billing']['first_name']) ? $db_link->escape_string(trim_gks($data['billing']['first_name'])) : '');
    $mybasketarray['user']['last_name']=(isset($data['billing']['last_name']) ? $db_link->escape_string(trim_gks($data['billing']['last_name'])) : '');
    $mybasketarray['user']['email']=(isset($data['billing']['email']) ? $db_link->escape_string(trim_gks($data['billing']['email'])) : '');
    $mybasketarray['user']['mobile']=(isset($data['billing']['phone']) ? $db_link->escape_string(trim_gks($data['billing']['phone'])) : '');
    $mybasketarray['user']['lang']=$user_lang;
    
    $mybasketarray['user']['ma_odos']=$ma_odos;
    $mybasketarray['user']['ma_arithmos']='';
    $mybasketarray['user']['ma_orofos']='';
    $mybasketarray['user']['ma_perioxi']='';
    $mybasketarray['user']['ma_poli']=(isset($data['billing']['city']) ? $db_link->escape_string(trim_gks($data['billing']['city'])) : '');
    $mybasketarray['user']['ma_tk']=(isset($data['billing']['postcode']) ? $db_link->escape_string(trim_gks($data['billing']['postcode'])) : '');
    $mybasketarray['user']['ma_country_id']=$ma_country_id;
    $mybasketarray['user']['ma_nomos_id']=$ma_nomos_id;
    $mybasketarray['user']['eponimia']=$eponimia;
    $mybasketarray['user']['title']=$title;
    $mybasketarray['user']['afm']=$afm;
    $mybasketarray['user']['doy']=$doy;
    $mybasketarray['user']['epaggelma']=$epaggelma;
    $mybasketarray['address_extra']=$id_users_extra_address;
    
    
    $mybasketarray['destination_data']['name'] = $destination_data_name;
    $mybasketarray['destination_data']['phone'] = $destination_data_phone;
    $mybasketarray['destination_data']['odos'] = $destination_data_odos;
    $mybasketarray['destination_data']['arithmos'] = '';
    $mybasketarray['destination_data']['orofos'] = $destination_data_orofos;
    $mybasketarray['destination_data']['perioxi'] = $destination_data_perioxi;
    $mybasketarray['destination_data']['poli'] =  $destination_data_poli;
    $mybasketarray['destination_data']['tk'] = $destination_data_tk;
    $mybasketarray['destination_data']['country_id'] = $destination_data_country_id;
    $mybasketarray['destination_data']['nomos_id'] = $destination_data_nomos_id;
    if ($mybasketarray['destination_data']['country_id']==0) $mybasketarray['destination_data']['country_id']=91;
    
    
    //$mybasketarray['user']['ma_country_id']=91;
    $mybasketarray['fiscal_position']=$fiscal_position_id;
    if ($mybasketarray['fiscal_position']<1) $mybasketarray['fiscal_position']=1;
    
    $mybasketarray['pricelist_id']=$pricelist_id;
    if ($mybasketarray['pricelist_id']<1) $mybasketarray['pricelist_id']=1;
    $mybasketarray['coupons']=array();
    //if (isset($mydata['coupons_array'])) {
    //  $mybasketarray['coupons']=$mydata['coupons_array'];
    //}
    
    //$mybasketarray['parastatiko']=($table_into == 'gks_acc_inv' ? 0 : $parastatiko);
    $mybasketarray['parastatiko']=$parastatiko;
   
  
    //if ($cmd_is_for_coupon) { //cmd is for coupon
      
    //}
  
    $mybasketarray['products_need_apostoli'] = false; //intval($mydata['gks_products_need_apostoli'])!=0;
    $mybasketarray['products_varos']= 0; //intval($mydata['gks_products_varos']);
    $mybasketarray['products_ogos']= 0; //intval($mydata['gks_products_ogos']);
    $mybasketarray['products_ogos_max_x']= 0; //intval($mydata['gks_products_ogos_x']);
    $mybasketarray['products_ogos_max_y']= 0; //intval($mydata['gks_products_ogos_y']);
    $mybasketarray['products_ogos_max_z']= 0; //intval($mydata['gks_products_ogos_z']);
    $mybasketarray['products_need_pliromi']=false;
    //if (floatval($mydata['gks_total_price_total'])>0) $mybasketarray['products_need_pliromi']=true;;
    
    $mybasketarray['tropos_apostolis'] = $tropos_apostolis; //intval($mydata['tropos_apostolis']);
    $mybasketarray['tropos_pliromis'] = $tropos_pliromis; //intval($mydata['tropos_pliromis']);
  
    $product_aa=0;
    $fields_change=array();
    $eidi_array=array();
    $is_shipping_or_fee_count=0;
    
    //print '<pre>'; print_r($data['gks_items']);//die();
    $roolist_day=array();

    
    $coupons_all_in_order=[];
    
    if (isset($data['gks_coupons']) and is_array($data['gks_coupons'])) {
      foreach ($data['gks_coupons'] as $coupon_item) {
        $coupon_code=trim_gks($coupon_item['code']);
        if ($coupon_code!='') {
          if (in_array($coupon_code,$coupons_all_in_order)==false) {
            $coupons_all_in_order[]=$coupon_code;
            $mybasketarray['coupons'][$coupon_code]=$coupon_code;
          }
        }
      }
    
      $items_with_coupon_found=0;
      foreach ($data['gks_items'] as &$item) {
        $coupon_amount=0; if (isset($item['coupon_amount'])) $coupon_amount=floatval($item['coupon_amount']);
        $coupon_amount_tax=0; if (isset($item['coupon_amount_tax'])) $coupon_amount_tax=floatval($item['coupon_amount_tax']);
        $coupon_amount_total=$coupon_amount+$coupon_amount_tax;
        if ($coupon_amount_total>0) {
          $item['coupons_all_in_order']=$coupons_all_in_order;
          $items_with_coupon_found++;
        }
      }
      unset($item);
      
      if ($items_with_coupon_found==1 and 
          count($coupons_all_in_order)==1) {
        foreach ($data['gks_items'] as &$item) {
          if (isset($item['coupons_all_in_order'])) {
            $item['coupons_all_in_order_onlyone']=true;
            break;
          }
        }
        unset($item);
        
      }
    }
    
    //print '<pre>sssssssssss ';print_r($data['gks_items']);die();
        
    
    foreach ($data['gks_items'] as $item) {
      
      
      //return array('success' => false, 'message' => base64_encode('<pre>kataxorisi item  '.print_r($kataxorisi,true)));
      //return array('success' => false, 'message' => base64_encode('<pre>gks_items item  '.print_r($item,true)));
      
      $check_this=true;
      if ($table_into=='gks_transfer_reservation') {
        if ($kataxorisi['transfer_outward_return']=='outward') { //outward
          if ($item['gks_popsicle_transfer_data']['data']['guid']!=$kataxorisi['guid']) {
            $check_this=false;
          }          
        } else { //return
          if ($item['gks_popsicle_transfer_data']['data']['guid'].'.return'!=$kataxorisi['guid']) {
            $check_this=false;
          } else {
            //return array('success' => false, 'message' => base64_encode('<pre>kataxorisi item  '.print_r($kataxorisi,true)));
          }        
        }
        

      } else if ($table_into=='gks_hotel_reservation') {
        if ($item['gks_hotel_reservation_room_data']['guid']!=$kataxorisi['guid']) {
          $check_this=false;
        }
      }
      
      if ($check_this) {
        
        
        
        $product_descr_variable='';  
        
        if (isset($item['is_shipping_or_fee'])) {
          $is_shipping_or_fee_count++;
          $product_id=$item['product_id'];
          $woo_item_id=-$is_shipping_or_fee_count;
        } else {
          
          $remote_product_id=intval($item['variation_id']); 
          if ($remote_product_id==0) $remote_product_id=intval($item['product_id']);
          if ($table_into=='gks_transfer_reservation') {
            $sql="SELECT product_id FROM gks_transfer_oxima_type where id_transfer_oxima_type=".$item['gks_popsicle_transfer_data']['protasi']['oxima_type']['id_transfer_oxima_type'];
          } else if ($table_into=='gks_hotel_reservation') {
            $sql="SELECT product_id FROM gks_hotel_room_type where id_hotel_room_type=".$item['gks_hotel_reservation_room_data']['room_type']['room_type_id'];
          } else {
            $sql="SELECT gks_woo_product.product_id,gks_eshop_products.product_class, gks_eshop_products.product_descr_variable
            FROM gks_woo_product 
            LEFT JOIN gks_eshop_products ON gks_woo_product.product_id = gks_eshop_products.id_product
            where gks_eshop_products.id_product is not null
            and eshop_id=".$eshop_id." 
            and remote_product_id=".$remote_product_id;
          }
          //return array('success' => false, 'message' => base64_encode('<pre>gks_items item  '.$sql));
          $result = $db_link->query($sql);
          //print $sql;
          if (!$result) {debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => base64_encode('sql error'));}
          if ($result->num_rows>0) {
            $row = $result->fetch_assoc();
            $product_id=$row['product_id'];
            if (isset($row['product_class']) and trim_gks($row['product_class'])=='variable_item') {
              $product_descr_variable=trim_gks($row['product_descr_variable']);
            }
          } else {
            $product_id=2;
          }
          $woo_item_id=intval($item['id']);
        }
        
  //      print '<pre>';
  //      print 'remote_product_id:'.$remote_product_id."\n";
  //      print 'product_id:'.$product_id."\n";
  //      print 'woo_item_id:'.$woo_item_id."\n";
  //      print_r($item);die();
        
        
        //check it kostas
        $product_fpa_base_id=1001;
        $item['tax_class_hname']=trim_gks($item['tax_class_hname']);
        if ($item['tax_class_hname']!='') {
          if ($eshop['tax_class_xorisfpa']!='' and $eshop['tax_class_xorisfpa']==$item['tax_class_hname']) {
            $product_fpa_base_id=1004;
          } else if ($eshop['tax_class_yperypermeiomenos']!='' and $eshop['tax_class_yperypermeiomenos']==$item['tax_class_hname']) {
            $product_fpa_base_id=1005;
          } else if ($eshop['tax_class_ypermeiomenos']!='' and $eshop['tax_class_ypermeiomenos']==$item['tax_class_hname']) {
            $product_fpa_base_id=1003;
          } else if ($eshop['tax_class_meiomenos']!='' and $eshop['tax_class_meiomenos']==$item['tax_class_hname']) {
            $product_fpa_base_id=1002;
          } else if ($eshop['tax_class_basikos']!='' and $eshop['tax_class_basikos']==$item['tax_class_hname']) {
            $product_fpa_base_id=1001;
          }
        }
        
        $product_quantity=floatval($item['quantity']);
        if ($table_into=='gks_transfer_reservation') $product_quantity=1; //$item['gks_popsicle_transfer_data']['protasi']['items_need'];
        if ($table_into=='gks_hotel_reservation') $product_quantity=$kataxorisi['first_room']['num_days'];
        
        
        $product_monada_id=1;if ($table_into=='gks_hotel_reservation') $product_monada_id=100;
        $product_price_check_fpa=($prices_include_tax ? 1 : 0);
        $product_price_start_all_net=floatval($item['total_org']);;
        $product_price_final_all_net=floatval($item['total']);
        $product_price_final_all_fpa=floatval($item['total_tax']);
        
        $product_price_ekptosi_pososto=0;
        if ($product_price_start_all_net!=0) $product_price_ekptosi_pososto=100*($product_price_start_all_net-$product_price_final_all_net)/$product_price_start_all_net;
    
        $product_comments='';
        if (isset($item['gks_details'])) {
          $temptxt= trim($item['gks_details']);
          if (substr($temptxt,0,5)=='<div>') {
            $temptxt=str_replace('</div><div>',"\r\n",$temptxt);
            $temptxt=str_replace('<div>','',$temptxt);
            $temptxt=str_replace('</div>','',$temptxt);
          }
          $temptxt=str_replace('<br>','',$temptxt);
          
          $product_comments.=$temptxt;
        }
        if (isset($item['gks_details_gr'])) {
          $temptxt= trim($item['gks_details_gr']);
          if (substr($temptxt,0,5)=='<div ') {
            $temptxt=str_replace('<span class="gks_wc_gr_iliakos_install_cart_label">','',$temptxt);
            $temptxt=str_replace('<span class="gks_wc_gr_iliakos_install_cart_value">','',$temptxt);
            $temptxt=str_replace('</span>','',$temptxt);
            
            $temptxt=str_replace('<div class="gks_wc_gr_iliakos_install_cart">','',$temptxt);
            $temptxt=str_replace('</div>','',$temptxt);
          }
          $temptxt=str_replace('<br>',"\r\n",$temptxt);
          
          $product_comments.=$temptxt;
        }
        
        
        
         
        $product_name=trim_gks($item['product_name']);
        if ($product_descr_variable!='' and endwith($product_name,' - '.$product_descr_variable)) $product_descr_variable='';
        
        $product_aa++;
        $fields_change[$product_aa]='gks_price';
        $hh_item=array(
          'aa' => $product_aa,
          'id_order_product' => 0, //$item['id_order_product'],
          'product_id' => $product_id,
          'product_fpa_base_id' => $product_fpa_base_id,
          'product_fpa_id' => 0,
          'product_fpa_pososto' => 0,
          'product_sheets' => 0,
          'product_quantity' => $product_quantity,
          'product_monada_id' => $product_monada_id,
          'product_price_check_fpa' => $product_price_check_fpa, 
          'product_price_start_all_net' => $product_price_start_all_net,
          'product_price_ekptosi_pososto' => $product_price_ekptosi_pososto,
          'product_price_final_all_net' => $product_price_final_all_net,
          'product_price_final_all_fpa' => $product_price_final_all_fpa,
          'product_descr' => $item['product_name'],
          'product_comments' => $product_comments,
          'product_set' => '',
          'woo_item_id' => $woo_item_id,
          'woo_remote_product_id' => $remote_product_id,
          'woo_product_name' => $item['product_name'].($product_descr_variable=='' ? '' : ' '.$product_descr_variable),
          
        );
        
        
        //if (isset($item['coupons_all_in_order'])) {
        //  $hh_item['product_price_coupon_use']=$item['coupons_all_in_order'][0];
        //  $hh_item['product_price_coupon_use_disabled']=false; //$item['coupons_all_in_order'][0];
        //}
        
        if ($table_into=='gks_transfer_reservation') { // or $table_into=='gks_hotel_reservation') {
          $new_calc_price = $item['total'] + $item['total_tax'];
          $new_calc_price-=$rsrv_sms_text_message_price;
          $new_calc_price-=$rsrv_cancellation_protection_price;
          
          
          
          $hh_item['woo_product_price_check_fpa'] = $product_price_check_fpa;
          $hh_item['woo_total'] = $new_calc_price;
          $hh_item['woo_total_tax'] = 0;
          $hh_item['woo_obj'] = $item;
          $hh_item['items_tr'] = $kataxorisi['items_tr'][1];
          
          //return array('success' => false, 'message' => base64_encode('<pre>kataxorisi  '.print_r($kataxorisi,true)));    
          
          $eidi_array[]=$hh_item;
          
          
          $items_need=1;
          if ($value['gks_popsicle_transfer_data']['protasi']['group_type']=='group_one') 
            $items_need=$item['gks_popsicle_transfer_data']['protasi']['items_need'];
            
          for($oxima_items_need=2; $oxima_items_need<=$items_need; $oxima_items_need++) {
            $product_aa++;
            $hh_item['aa'] = $product_aa;
            $hh_item['items_tr'] = $kataxorisi['items_tr'][$oxima_items_need];
                       
            $eidi_array[]=$hh_item;
          }
          
             
        } else {
          $hh_item['woo_product_price_check_fpa'] = $product_price_check_fpa;
          $hh_item['woo_total'] = $item['total'];
          $hh_item['woo_total_tax'] = $item['total_tax'];
          $hh_item['woo_obj'] = $item;
          
          $eidi_array[]=$hh_item;
        }
        
        

        
        //return array('success' => false, 'message' => base64_encode('<pre>item  '.print_r($item,true)));
      }
    }
    
    
    //echo '<pre>sssssssssss ';print_r($eidi_array);die();
    
    
    
    //return array('success' => false, 'message' => base64_encode('<pre>eidi_array  '.print_r($eidi_array,true)));
    
    $basket_products_temp =array();
    foreach ($eidi_array as &$value) {
  //    $user_field_change='';
  //    if ($value['aa'] == $fields_change_curr_aa) $user_field_change=$fields_change_curr_name;
  //    $user_change_ekptosi_or_final_net='';
  //    if (isset($fields_change[$value['aa']])) $user_change_ekptosi_or_final_net=$fields_change[$value['aa']];
      
      if ($table_into=='gks_transfer_reservation' or $table_into=='gks_hotel_reservation') {
        $user_field_change='gks_price_final'; //gks_ekptosi  or gks_price or gks_quantity
        $user_change_ekptosi_or_final_net='gks_price_total';
      } else {
        $user_field_change='gks_price';
        $user_change_ekptosi_or_final_net='gks_price';
      }


      
      

      
      $user_ekptosi = 0; //floatval($value['product_price_ekptosi_pososto']);  
    
      $value['product_withheldPercentCategory']=0;
      $value['product_withheldAmount']=0;
      $value['product_otherTaxesPercentCategory']=0;  
      $value['product_otherTaxesAmount']=0; 
      $value['product_stampDutyPercentCategory']=0;  
      $value['product_stampDutyAmount']=0;
      $value['product_feesPercentCategory']=0;  
      $value['product_feesAmount']=0;  
      $value['product_deductionsAmount']=0;  
      
      
      $sql="select * from gks_eshop_products where id_product=".$value['product_id'];  
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));}
      if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $value['product_withheldPercentCategory']=$row['product_withheldPercentCategory'];
        $value['product_otherTaxesPercentCategory']=$row['product_otherTaxesPercentCategory'];
        $value['product_stampDutyPercentCategory']=$row['product_stampDutyPercentCategory'];
        $value['product_feesPercentCategory']=$row['product_feesPercentCategory'];
      }  
    
      $objects=array();
      $objects[]=array('key' =>0, 'type'=>'normal', 'descr'=>'normal', 'copies' => $value['product_quantity'],  'files' => array(), 'warnings'=>array());
      $basket_products_temp[$value['aa']]=array(
        'product_id'=>array(
          'id_product'=>$value['product_id'], 
          'product_monada_id' => $value['product_monada_id'], 
          'product_fpa_base_id' => $value['product_fpa_base_id'], 
          'product_sheets'=>$value['product_sheets'], 
          'product_set' => $value['product_set'],
         ), 
        'objects'=>$objects,
        'user_ekptosi' => $user_ekptosi,
        'user_final_net' => floatval($value['product_price_final_all_net']),
        'user_change_ekptosi_or_final_net' => $user_change_ekptosi_or_final_net,
        'user_field_change' => $user_field_change,
        
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
        'woo_item_id' => $value['woo_item_id'],
        'woo_remote_product_id' => $value['woo_remote_product_id'],
        'woo_product_name' => $value['woo_product_name'],
        'woo_product_price_check_fpa' => $value['woo_product_price_check_fpa'],
        'woo_total' => $value['woo_total'],
        'woo_total_tax' => $value['woo_total_tax'],
        'woo_obj' => $value['woo_obj'],
        'product_comments' => $value['product_comments'],
      );
      //print '<pre>sssssssssssss ';print_r($basket_products_temp[$value['aa']]);die();
      
      if ($table_into=='gks_transfer_reservation') {
        $basket_products_temp[$value['aa']]['user_final_net']=$value['items_tr']['price_total']; 
        $basket_products_temp[$value['aa']]['items_tr']  = $value['items_tr'];
        $basket_products_temp[$value['aa']]['woo_total'] = $value['items_tr']['price_total']; 
        $basket_products_temp[$value['aa']]['woo_total_tax'] = 0; 
        
        $basket_products_temp[$value['aa']]['user_group_type'] = $value['woo_obj']['gks_popsicle_transfer_data']['protasi']['group_type'];
        
      }
      

      if ($table_into=='gks_hotel_reservation') {
        $basket_products_temp[$value['aa']]['is_hotel_room_type']=true;
        $basket_products_temp[$value['aa']]['id_hotel'] = $hotel_id;

        $basket_products_temp[$value['aa']]['user_check_in']= $value['woo_obj']['gks_hotel_reservation_room_data']['check_in'];
        $basket_products_temp[$value['aa']]['user_check_out']= $value['woo_obj']['gks_hotel_reservation_room_data']['check_out'];
        $basket_products_temp[$value['aa']]['user_room_id'] = $value['woo_obj']['gks_hotel_reservation_room_data']['room']['room_item_id'];
        $basket_products_temp[$value['aa']]['user_rnum_adults'] = $value['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_adults'];
        $basket_products_temp[$value['aa']]['user_rnum_childs'] = $value['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_childs'];
        $basket_products_temp[$value['aa']]['user_rchilds_ages_list'] = json_encode($value['woo_obj']['gks_hotel_reservation_room_data']['room']['rchilds_ages_list']);
        $basket_products_temp[$value['aa']]['user_rnum_child_kounies'] = $value['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_child_kounies'];
        $basket_products_temp[$value['aa']]['user_rnum_extra_beds'] = $value['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_extra_beds'];
        
//        $basket_products_temp[$value['aa']]['user_ekptosi']=0; 
//        unset($basket_products_temp[$value['aa']]['user_final_net']);
//        $basket_products_temp[$value['aa']]['user_change_ekptosi_or_final_net'] = 'gks_price_final';
//        $basket_products_temp[$value['aa']]['user_field_change'] = 'gks_price_final';


//        $basket_products_temp[$value['aa']]['user_product_price_check_fpa']=true;
//        $fields_change[$value['aa']]='gks_price_final';
        
//        $ffff=$value['woo_obj']['gks_hotel_room_price'];
//        $tttt=round($value['woo_total_tax'],2);
//        $bbbb=$ffff-$tttt;
//        
//        $basket_products_temp[$value['aa']]['user_final_net']=$ffff;
//        $basket_products_temp[$value['aa']]['user_final_total']=$ffff;
//        $basket_products_temp[$value['aa']]['woo_total'] = $ffff;
//        $basket_products_temp[$value['aa']]['woo_total_tax'] = $tttt;
//        
//        $basket_products_temp[$value['aa']]['woo_obj']['total'] = $ffff;
//        $basket_products_temp[$value['aa']]['woo_obj']['total_tax'] = $tttt;
        
        //$basket_products_temp[$value['aa']]['user_final_net']=$value['woo_obj']['gks_hotel_room_price'];
        //print '<pre>';print_r($basket_products_temp[$value['aa']]);die();
        
      }
    }
    unset($value);
    
    
    
    
    
    //echo '<pre>sssssssssss ';print_r($basket_products_temp);die();
    
    //print '<pre>sssssssssssss ';print_r($basket_products_temp);die();  
    
    //return array('success' => false, 'message' => base64_encode('<pre>basket_products_temp  '.print_r($basket_products_temp,true)));
    
    $mybasketarray['products'] = $basket_products_temp;
    $myproducts = gks_basket_recalc($mybasketarray, $fields_change, array());  


    //return array('success' => false, 'message' => base64_encode('<pre>basket_products_temp after  '.print_r($basket_products_temp,true)));

  
    //print '<pre>';print_r($mybasketarray); //die();
    //print '<pre>';print_r($mybasketarray['products']);
    $products_posotita=0;
    $gks_price_original_net=0;
    
    $table_id_product=$table_id.'_product';
    if ($table_into=='gks_transfer_reservation')  $table_id_product='id_transfer_reservation_oximata';
    if ($table_into=='gks_hotel_reservation')     $table_id_product='id_hotel_reservation_room';
    
    $table_into_products=$table_into."_products";
    if ($table_into=='gks_transfer_reservation')  $table_into_products='gks_transfer_reservation_oximata';
    if ($table_into=='gks_hotel_reservation')     $table_into_products='gks_hotel_reservation_room';
    
    
    $not_delete_product_id_list=array();
    foreach ($mybasketarray['products'] as $aa => $product) {
      

      $sql="select ".$table_id_product." from ".$table_into_products." where ".$table_idr."=".$id_order." and woo_item_id=".$product['woo_item_id'];
      if ($table_into=='gks_transfer_reservation') {
        $sql.=" and woo_item_aa=".$aa;
      }
      
      //return array('success' => false, 'message' => base64_encode('<pre>product 2  '.print_r($product,true)));      
      
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => base64_encode('sql error'));}
      if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
        $id_order_product=$row[$table_id_product];
        $not_delete_product_id_list[]=$id_order_product;
      } else {
        $sql="insert into ".$table_into_products." (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,".$table_idr.",woo_item_id";
        if ($table_into=='gks_transfer_reservation') $sql.=",woo_item_aa";
        $sql.=") values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',".$id_order.",".$product['woo_item_id'];
        if ($table_into=='gks_transfer_reservation') $sql.=",".$aa;
        $sql.=")";
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));}
        $id_order_product=$db_link->insert_id;
        $not_delete_product_id_list[]=$id_order_product;
      }
      
      
      //echo $id_order_product."\n";
      
      $products_posotita+=$product['product_id']['product_quantity'];
      $gks_price_original_net+=$product['product_id']['product_price_start_all_net'];
      
      $sql='';

      if ($table_into=='gks_transfer_reservation') {
        
        
        if ($product['woo_obj']['gks_popsicle_transfer_data']['confirm']['book_for_other']==0) {
          $sql.="ruser_id=-1,";
          $sql.="ruser_lang='',";
          $sql.="ruser_first_name='',";
          $sql.="ruser_last_name='',";
          $sql.="ruser_email='',";
          $sql.="ruser_mobile='',";
          $sql.="ruser_ma_odos='',";
          $sql.="ruser_ma_orofos='',";
          $sql.="ruser_ma_perioxi='',";
          $sql.="ruser_ma_poli='',";
          $sql.="ruser_ma_tk='',";
          $sql.="ruser_ma_country_id=0,";
          $sql.="ruser_ma_nomos_id=0,";
          $sql.="ruser_fiscal_position_id=0,";
          $sql.="ruser_pricelist_id=0,";
        } else {
          //return array('success' => false, 'message' => base64_encode('<pre>'.print_r($product['woo_obj'],true)));
          $ruser_ma_country_id=0;$phone_code='';
          $other_country=trim_gks($product['woo_obj']['gks_popsicle_transfer_data']['confirm']['other_country']);
          //return array('success' => false, 'message' => base64_encode('<pre>'.$other_country));
          if ($other_country!='') {
            $sql_cc="select id_country,phone_code from gks_country where country_initials like '".$other_country."'";
            $result_cc = $db_link->query($sql_cc);
            if (!$result_cc) {debug_mail(false,'error sql',$sql_cc);return array('success' => false, 'message' => base64_encode('sql error'));}
            if ($result_cc->num_rows>0) {
              $row_cc = $result_cc->fetch_assoc(); 
              $ruser_ma_country_id=$row_cc['id_country'];
              $phone_code=trim_gks($row_cc['phone_code']);
            }
          }
          
          $other_mobile=trim_gks($product['woo_obj']['gks_popsicle_transfer_data']['confirm']['other_mobile']);
          if ($phone_code!='') $other_mobile=trim_gks('('.$phone_code.') '.$other_mobile);
          
          $sql.="ruser_id=0,";
          $sql.="ruser_lang='".$db_link->escape_string($user_lang)."',";
          $sql.="ruser_first_name='".$db_link->escape_string($product['woo_obj']['gks_popsicle_transfer_data']['confirm']['other_name'])."',";
          $sql.="ruser_last_name='".$db_link->escape_string($product['woo_obj']['gks_popsicle_transfer_data']['confirm']['other_surname'])."',";
          $sql.="ruser_email='".$db_link->escape_string($product['woo_obj']['gks_popsicle_transfer_data']['confirm']['other_email'])."',";
          $sql.="ruser_mobile='".$db_link->escape_string($other_mobile)."',";
          $sql.="ruser_ma_odos='',";
          $sql.="ruser_ma_orofos='',";
          $sql.="ruser_ma_perioxi='',";
          $sql.="ruser_ma_poli='',";
          $sql.="ruser_ma_tk='',";
          $sql.="ruser_ma_country_id=".$ruser_ma_country_id.",";
          $sql.="ruser_ma_nomos_id=0,";
          $sql.="ruser_fiscal_position_id=1,";
          $sql.="ruser_pricelist_id=1,";
          
          //return array('success' => false, 'message' => base64_encode('<pre>'.$sql));
          
        }
        
        //return array('success' => false, 'message' => base64_encode('<pre>'.print_r($product,true)));
        
        $is_return_oxima_for_id=0;
        if ($kataxorisi['transfer_outward_return']=='outward') { //outward
        
        } else {
          $sql_irtfi="select id_transfer_reservation_oximata from gks_transfer_reservation_oximata
          where transfer_reservation_id=".$kat_id_ids[$kat_id-1]."
          and transfer_oxima_type_id=".intval($product['woo_obj']['gks_popsicle_transfer_data']['protasi']['oxima_type']['id_transfer_oxima_type'])."
          and oximata_aa=".$aa;
          $result_irtfi = $db_link->query($sql_irtfi);
          if (!$result_irtfi) {debug_mail(false,'error sql',$sql_irtfi);return array('success' => false, 'message' => base64_encode('sql error'));}
          if ($result_irtfi->num_rows>0) {
            $row_irtfi = $result_irtfi->fetch_assoc(); 
            $is_return_oxima_for_id=$row_irtfi['id_transfer_reservation_oximata'];
          }
        }
      
        $sql.="is_return_oxima_for_id=".$is_return_oxima_for_id.",";   
        
          
        
        
        $sql.="transfer_oxima_type_id=".intval($product['woo_obj']['gks_popsicle_transfer_data']['protasi']['oxima_type']['id_transfer_oxima_type']).",";     
        $sql.="oximata_aa=".$aa.",";     
        $sql.="rnum_adults=".$product['items_tr']['adults'].",";
        $sql.="rnum_childs=".$product['items_tr']['children'].",";
        $sql.="rnum_babys=".$product['items_tr']['infants'].",";
        
        
        
        $sql.="group_type='".$db_link->escape_string($product['woo_obj']['gks_popsicle_transfer_data']['protasi']['group_type'])."',";
        $sql.="rsrv_oxima_num_booster=".$product['items_tr']['booster'].",";
        $sql.="rsrv_oxima_num_kareklakia=".$product['items_tr']['kareklakia'].",";
        $sql.="rsrv_oxima_num_amajidia=".$product['items_tr']['amajidia'].",";
        $sql.="rsrv_oxima_num_golfbag=".$product['items_tr']['golfbag'].",";
        $sql.="rsrv_oxima_num_skis=".$product['items_tr']['skis'].",";
        $sql.="rsrv_oxima_num_5minstop=".$product['items_tr']['5minstop'].",";
        $sql.="rsrv_oxima_5minstop_descr='".$db_link->escape_string($product['woo_obj']['gks_popsicle_transfer_data']['extras']['5minstop_address'])."',";
         
        
        //return array('success' => false, 'message' => base64_encode('<pre>'.$sql));
        //print '<pre>';print_r($product);
        //die();
        
//        $sql.="hotel_room_id=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['room_item_id'].",";     
//        $sql.="rchilds_ages_list='".$db_link->escape_string(json_encode($product['woo_obj']['gks_hotel_reservation_room_data']['room']['rchilds_ages_list']))."',";     
//        $sql.="rnum_child_kounies=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_child_kounies'].",";     
//        $sql.="rnum_extra_beds=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_extra_beds'].",";     
//
//
//        $sql.="room_ajia_table_math='".$db_link->escape_string($product['product_id']['room_ajia_table']['msg_price'])."',";     
//        $sql.="room_ajia_table_html='".$db_link->escape_string($product['product_id']['room_ajia_table']['roomaf_html'])."',";     
//        $sql.="room_ajia_table_array='".$db_link->escape_string(base64_decode($product['product_id']['room_ajia_table']['roomaf_array']))."',";     

        
//        $roolist_day[]=array(
//          'delete'=>0, 
//          'hotel_room_id'=> $product['woo_obj']['gks_hotel_reservation_room_data']['room']['room_item_id'], 
//          'recid'=> $id_order_product, 
//          'hotel_type_room_id'=>$product['woo_obj']['gks_hotel_reservation_room_data']['room_type']['room_type_id'],
//        );
              
      } else if ($table_into=='gks_hotel_reservation') {
        
        
        if ($product['woo_obj']['gks_hotel_reservation_room_data']['room']['is_same']!=0) {
          $sql.="ruser_id=-1,";
          $sql.="ruser_lang='',";
          $sql.="ruser_first_name='',";
          $sql.="ruser_last_name='',";
          $sql.="ruser_email='',";
          $sql.="ruser_mobile='',";
          $sql.="ruser_ma_odos='',";
          $sql.="ruser_ma_orofos='',";
          $sql.="ruser_ma_perioxi='',";
          $sql.="ruser_ma_poli='',";
          $sql.="ruser_ma_tk='',";
          $sql.="ruser_ma_country_id=0,";
          $sql.="ruser_ma_nomos_id=0,";
          $sql.="ruser_fiscal_position_id=0,";
          $sql.="ruser_pricelist_id=0,";
        } else {
          $sql.="ruser_id=0,";
          $sql.="ruser_lang='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['lang'])."',";
          $sql.="ruser_first_name='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['first_name'])."',";
          $sql.="ruser_last_name='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['last_name'])."',";
          $sql.="ruser_email='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['email'])."',";
          $sql.="ruser_mobile='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['mobile'])."',";
          $sql.="ruser_ma_odos='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['ma_odos'])."',";
          $sql.="ruser_ma_orofos='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['ma_orofos'])."',";
          $sql.="ruser_ma_perioxi='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['ma_perioxi'])."',";
          $sql.="ruser_ma_poli='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['ma_poli'])."',";
          $sql.="ruser_ma_tk='".$db_link->escape_string($product['woo_obj']['gks_hotel_reservation_room_data']['room']['ma_tk'])."',";
          $sql.="ruser_ma_country_id=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['ma_country_id'].",";
          $sql.="ruser_ma_nomos_id=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['ma_nomos_id'].",";
          $sql.="ruser_fiscal_position_id=1,";
          $sql.="ruser_pricelist_id=1,";
          
        }
        //print '<pre>';print_r($product);
        //die();
        
        $sql.="hotel_room_id=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['room_item_id'].",";     
        $sql.="rnum_adults=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_adults'].",";     
        $sql.="rnum_childs=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_childs'].",";     
        $sql.="rchilds_ages_list='".$db_link->escape_string(json_encode($product['woo_obj']['gks_hotel_reservation_room_data']['room']['rchilds_ages_list']))."',";     
        $sql.="rnum_child_kounies=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_child_kounies'].",";     
        $sql.="rnum_extra_beds=".$product['woo_obj']['gks_hotel_reservation_room_data']['room']['rnum_extra_beds'].",";     


        $sql.="room_ajia_table_math='".$db_link->escape_string($product['product_id']['room_ajia_table']['msg_price'])."',";     
        $sql.="room_ajia_table_html='".$db_link->escape_string($product['product_id']['room_ajia_table']['roomaf_html'])."',";     
        $sql.="room_ajia_table_array='".$db_link->escape_string(base64_decode($product['product_id']['room_ajia_table']['roomaf_array']))."',";     

        
        $roolist_day[]=array(
          'delete'=>0, 
          'hotel_room_id'=> $product['woo_obj']['gks_hotel_reservation_room_data']['room']['room_item_id'], 
          'recid'=> $id_order_product, 
          'hotel_type_room_id'=>$product['woo_obj']['gks_hotel_reservation_room_data']['room_type']['room_type_id'],
        );
   
      } else {
        $sql.="product_aa=".$aa.",";
        $sql.="product_monada_id_org=".$product['product_id']['product_monada_id_org'].",";
        $sql.="product_monada_id=".$product['product_id']['product_monada_id'].",";
        $sql.="product_is_digital=".$product['product_id']['product_is_digital'].",";
        $sql.="product_is_simple_download=".$product['product_id']['product_is_simple_download'].",";
        $sql.="product_need_apostoli=".$product['product_id']['product_need_apostoli'].",";
        $sql.="product_normal=".$product['product_id']['product_normal'].",";
        $sql.="product_type='".$db_link->escape_string($product['product_id']['product_type'])."',";
        $sql.="product_need_multi_files=".$product['product_id']['product_need_multi_files'].",";
        $sql.="product_need_multi_files_min=".$product['product_id']['product_need_multi_files_min'].",";
        $sql.="product_need_multi_files_max=".$product['product_id']['product_need_multi_files_max'].",";
        $sql.="product_varos=".$product['product_id']['product_varos'].",";
        $sql.="product_ogos_x=".$product['product_id']['product_ogos_x'].",";
        $sql.="product_ogos_y=".$product['product_id']['product_ogos_y'].",";
        $sql.="product_ogos_z=".$product['product_id']['product_ogos_z'].",";
    //  product_category_ids` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
        $sql.="product_sheets=".$product['product_id']['product_sheets'].",";
        //$sql.="product_price_check_fpa=".$product['woo_product_price_check_fpa'].",";
        $sql.="product_price_check_fpa=".($prices_include_tax ? 1 : 0).",";
        
      }
      
      //if ($aa==3) {print '<pre>ssssssssssss '; print_r($product); die();}
      
      $sql.="product_id=".$product['product_id']['id_product'].",";
      $sql.="product_descr='".$db_link->escape_string($product['woo_product_name'])."',";
      $sql.="product_fpa_base_id=".$product['product_id']['product_fpa_base_id'].",";
      $sql.="product_fpa_id=".$product['product_id']['product_fpa_id_array']['id_fpa_to'].",";
      $sql.="product_fpa_pososto=".$product['product_id']['product_fpa_id_array']['fpa_pososto'].",";
      $sql.="product_quantity=".$product['product_id']['product_quantity'].",";
  //  apografi_posotitaonhand` double DEFAULT NULL,
      $sql.="product_price_start_all_net=".$product['product_id']['product_price_start_all_net'].",";
  
  
  
      if (1==1) { //karfota ta noumera apo woo
        
        $woo_product_price_final_all_net=floatval($product['woo_total']);
        $woo_product_price_final_all_fpa=floatval($product['woo_total_tax']);
        $woo_product_price_final_all_total=$woo_product_price_final_all_net + $woo_product_price_final_all_fpa; 
    //echo 'gggggggggg';die();
        $sql.="product_price_final_all_net=".number_format($woo_product_price_final_all_net,8,'.','').",";
        $sql.="product_price_final_all_fpa=".number_format($woo_product_price_final_all_fpa,8,'.','').",";
        $sql.="product_price_final_all_total=".number_format($woo_product_price_final_all_total,8,'.','').",";
  
        if (isset($product['woo_obj']) and isset($product['woo_obj']['product_fpa_ejeresi_id'])) {
          $sql.="product_fpa_ejeresi_id=".$product['woo_obj']['product_fpa_ejeresi_id'].",";
        }
        
      } else {
        $sql.="product_price_final_all_net=".$product['product_id']['product_price_final_all_net'].",";
        $sql.="product_price_final_all_fpa=".$product['product_id']['product_price_final_all_fpa'].",";
        $sql.="product_price_final_all_total=".$product['product_id']['product_price_final_all_total'].",";
        
      }
      
      //echo '<pre>sssssssssssssssssssssd ';print_r($product);die();
      
      
  
      $product_price_ekptosi_net=round($product['product_id']['product_price_start_all_net']-$product['product_id']['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $product_price_ekptosi_netfpa=round($product['product_id']['product_price_start_all_net']+$product['product_id']['product_price_start_all_fpa']-$product['product_id']['product_price_final_all_net']-$product['product_id']['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $product_price_ekptosi_total=round($product['product_id']['product_price_start_all_total']-$product['product_id']['product_price_final_all_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $product_price_ekptosi_pososto=0;
      if ($product['product_id']['product_price_start_all_net']!=0 and $product['product_id']['product_price_include_vat']==0) {
        $product_price_ekptosi_pososto=round($product_price_ekptosi_net*100/$product['product_id']['product_price_start_all_net'],2);
      } else if ($product['product_id']['product_price_start_all_total']!=0 and $product['product_id']['product_price_include_vat']!=0) {
        $product_price_ekptosi_pososto=round($product_price_ekptosi_total*100/$product['product_id']['product_price_start_all_total'],2);
      }
      $sql.="product_price_ekptosi_pososto=".$product_price_ekptosi_pososto.",";
      
      //file_put_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/product'.rand(1111,9999).'.txt', print_r($product,true));
      
      $sql.="product_comments='".$db_link->escape_string($product['product_comments'])."',";
  //  production_product_pososto` double NOT NULL DEFAULT '0',
  //  product_sum_time` int(11) NOT NULL DEFAULT '0',
  
      $sql.="product_withheldPercentCategory=".$product['other_taxes']['withheldPercentCategory'].",";
      $sql.="product_withheldAmount=".$product['other_taxes']['withheldAmount'].",";
      $sql.="product_stampDutyPercentCategory=".$product['other_taxes']['stampDutyPercentCategory'].",";
      $sql.="product_stampDutyAmount=".$product['other_taxes']['stampDutyAmount'].",";
      $sql.="product_feesPercentCategory=".$product['other_taxes']['feesPercentCategory'].",";
      $sql.="product_feesAmount=".$product['other_taxes']['feesAmount'].",";
      $sql.="product_otherTaxesPercentCategory=".$product['other_taxes']['otherTaxesPercentCategory'].",";
      $sql.="product_otherTaxesAmount=".$product['other_taxes']['otherTaxesAmount'].",";
      
      if ($table_into!='gks_hotel_reservation' and $table_into!='gks_transfer_reservation') {
        $sql.="p_warehouses_id_from=".$warehouses_id_from .",";
        $sql.="p_warehouses_id_to=".  $warehouses_id_to.",";
        $sql.=($table_into=='gks_orders' ? 'p_order_state' : 'p_inv_state')."='".$order_state."',";
      }
      
  //  after_balance_warehouses_id_from` double NOT NULL DEFAULT '0',
  //  after_balance_warehouses_id_to` double NOT NULL DEFAULT '0',
      
          
      
      if (isset($product['product_id']['product_fpa_id_array'])) $sql.="product_fpa_id_json='".$db_link->escape_string(json_encode($product['product_id']['product_fpa_id_array']))."',";
      if (isset($product['product_id']['product_price_include_vat'])) $sql.="product_price_include_vat=".intval($product['product_id']['product_price_include_vat']).",";
      if (isset($product['product_id']['product_price_start_peritem_db'])) $sql.="product_price_start_peritem_db=".number_format($product['product_id']['product_price_start_peritem_db'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_peritem_net'])) $sql.="product_price_start_peritem_net=".number_format($product['product_id']['product_price_start_peritem_net'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_peritem_fpa'])) $sql.="product_price_start_peritem_fpa=".number_format($product['product_id']['product_price_start_peritem_fpa'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_peritem_total'])) $sql.="product_price_start_peritem_total=".number_format($product['product_id']['product_price_start_peritem_total'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_all_fpa'])) $sql.="product_price_start_all_fpa=".number_format($product['product_id']['product_price_start_all_fpa'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_all_total'])) $sql.="product_price_start_all_total=".number_format($product['product_id']['product_price_start_all_total'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_db'])) $sql.="product_price_final_peritem_db=".number_format($product['product_id']['product_price_final_peritem_db'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_net'])) $sql.="product_price_final_peritem_net=".number_format($product['product_id']['product_price_final_peritem_net'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_fpa'])) $sql.="product_price_final_peritem_fpa=".number_format($product['product_id']['product_price_final_peritem_fpa'],8,'.','').",";
      if (isset($product['product_id']['product_price_final_peritem_total'])) $sql.="product_price_final_peritem_total=".number_format($product['product_id']['product_price_final_peritem_total'],8,'.','').",";
      if (isset($product['product_id']['product_price_start_all_net']) and 
          isset($product['product_id']['product_price_final_all_net'])) {
        $product_price_ekptosi_net=$product['product_id']['product_price_start_all_net']-$product['product_id']['product_price_final_all_net'];
        $sql.="product_price_ekptosi_net=".number_format($product_price_ekptosi_net,8,'.','').",";
      }
      if (isset($product['product_id']['product_pricelist_item_id'])) $sql.="product_pricelist_item_id=".intval($product['product_id']['product_pricelist_item_id']).",";
      if (isset($product['product_id']['product_pricelist_item_descr'])) $sql.="product_pricelist_item_descr='".$db_link->escape_string($product['product_id']['product_pricelist_item_descr'])."',";
      if (isset($product['product_id']['product_pricelist_item_percent'])) $sql.="product_pricelist_item_percent=".number_format($product['product_id']['product_pricelist_item_percent'],8,'.','').",";
  
    
      if ($table_into!='gks_hotel_reservation' and $table_into!='gks_transfer_reservation') {
        if (isset($product['product_id']['monada_convert'])) $sql.="monada_convert_json='".$db_link->escape_string(json_encode($product['product_id']['monada_convert']))."',";
      
        if (isset($product['product_id']['monada_convert']) and 
            isset($product['product_id']['monada_convert']['ok']) and 
            isset($product['product_id']['monada_convert']['epi']) and 
            $product['product_id']['monada_convert']['ok'] and 
            $product['product_id']['monada_convert']['epi'] !=0 and
            $product['product_id']['monada_convert']['epi'] != 1) {
          $sql.="monada_convert_epi=".number_format($product['product_id']['monada_convert']['epi'],16,'.','').",";
          $sql.="monada_convert_epi_rev=".number_format($product['product_id']['monada_convert']['epi_rev'],16,'.','').",";
        } else {
          $sql.="monada_convert_epi=1,";
          $sql.="monada_convert_epi_rev=1,";
        }
      }
      
      //print '<pre>ggggggggggggg ';print_r($product);die();
      
      $product_price_coupon_use='';
      $product_price_coupon_use_disabled=0;
      if (isset($product['woo_obj']['coupons_all_in_order'])) {
        $product_price_coupon_use=trim_gks($product['woo_obj']['coupons_all_in_order'][0]);
        $product_price_coupon_use_disabled=1;
        if (isset($product['woo_obj']['coupons_all_in_order_onlyone'])) {
          $product_price_coupon_use_disabled=0;
        }
      }
      $sql.="product_price_coupon_use='".$db_link->escape_string($product_price_coupon_use)."',";
      $sql.="product_price_coupon_use_disabled=".$product_price_coupon_use_disabled.",";
      
      
  
      if ($sql!='') {
        $sql=substr($sql, 0, strlen($sql)-1);
        $sql="update ".$table_into_products." set ".$sql." where ".$table_idr."=".$id_order." and ".$table_id_product."=".$id_order_product." limit 1";
        //if ($aa==3) {echo '<pre>aaaaaaaaaa ';echo $sql; die();}
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));}   
        
        if ($table_into=='gks_hotel_reservation') {
          
          
          $sql="UPDATE gks_hotel_reservation_room 
          LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product 
          SET gks_hotel_reservation_room.product_descr = gks_eshop_products.product_descr
          WHERE gks_eshop_products.product_descr Is Not Null 
          AND gks_eshop_products.id_product Is Not Null
          AND gks_hotel_reservation_room.id_hotel_reservation_room=".$id_order_product;
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => base64_encode('sql error'));}   

        }
         
      }
  
  
    }
  
    
    if ($table_into=='gks_transfer_reservation') {
      $sql="delete from gks_transfer_reservation_oximata 
      where transfer_reservation_id=".$id_order;
      if (count($not_delete_product_id_list)>0) {
        $sql.=" and id_transfer_reservation_oximata not in (".implode(',',$not_delete_product_id_list).")";
      }
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => base64_encode('sql error'));}   
    }
    
    if ($table_into=='gks_orders' or $table_into=='gks_acc_inv') {
    
      //$table_into_products.' | '.$table_idr.'|'.$table_id_product;
      //gks_acc_inv_products       acc_inv_id     id_acc_inv_product
      $sql="SELECT ".$table_id_product." as idpid FROM ".$table_into_products." WHERE ".$table_idr."=".$id_order;
      if (count($not_delete_product_id_list)>0) {
        $sql.=" and ".$table_id_product." not in (".implode(',',$not_delete_product_id_list).")";
      }
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => base64_encode('sql error'));}   
      $products_for_delete=[];
      while ($row= $result->fetch_assoc()) { 
        $products_for_delete[]=$row['idpid'];
      } 
      if (count($products_for_delete)>0) {
        $sql="delete from ".$table_into_products." where ".$table_id_product." in (".implode(',',$products_for_delete).")";
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));}   

        if ($table_into=='gks_acc_inv') {
          $sql="delete from gks_acc_inv_products_income where acc_inv_product_id in (".implode(',',$products_for_delete).")";
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => base64_encode('sql error'));}   
          
          $sql="delete from gks_acc_inv_products_expenses where acc_inv_product_id in (".implode(',',$products_for_delete).")";
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            return array('success' => false, 'message' => base64_encode('sql error'));}   
          
        }
        
      }
      //echo '<pre>sdsssssssssss ';print_r($products_for_delete);die();
      
      //echo '<pre>sdsssssssssss '.$sql;die();
      //echo '<pre>sdsssssssssss ';print_r($not_delete_product_id_list);die();
    
    
    }
    
    //gks_price_total
    $sql='';
    $sql.="products_need_apostoli=".($mybasketarray['products_need_apostoli'] ? '1' : '0').",";
    $sql.="products_need_pliromi=".($mybasketarray['products_need_pliromi'] ? '1' : '0').",";
    if ($table_into=='gks_hotel_reservation') {
      $sql.="products_posotita=".($kataxorisi['first_room']['num_days']*count($kataxorisi['rooms'])).",";
    } else {
      $sql.="products_posotita=".number_format($products_posotita,8,'.','').",";
    }
    $sql.="gks_price_original_net=".number_format($gks_price_original_net,8,'.','').",";
    if (isset($mybasketarray['products_varos'])) $sql.="products_varos=".number_format($mybasketarray['products_varos'],8,'.','').",";
    if (isset($mybasketarray['products_ogos'])) $sql.="products_ogos=".number_format($mybasketarray['products_ogos'],8,'.','').",";
    if (isset($mybasketarray['products_ogos_max_x'])) $sql.="products_ogos_max_x=".number_format($mybasketarray['products_ogos_max_x'],8,'.','').",";
    if (isset($mybasketarray['products_ogos_max_y'])) $sql.="products_ogos_max_y=".number_format($mybasketarray['products_ogos_max_y'],8,'.','').",";
    if (isset($mybasketarray['products_ogos_max_z'])) $sql.="products_ogos_max_z=".number_format($mybasketarray['products_ogos_max_z'],8,'.','').",";
    if (isset($mybasketarray['tropoi_apostolis_all']) and 
        isset($mybasketarray['tropos_apostolis']) and
        isset($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]) ) {
          $sql.="tropos_apostolis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_apostolis_all'][$mybasketarray['tropos_apostolis']]))."',";
    }
    if (isset($mybasketarray['tropoi_pliromis_all']) and 
        isset($mybasketarray['tropos_pliromis']) and
        isset($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]) ) {
          $sql.="kostos_pliromis_json='".$db_link->escape_string(json_encode($mybasketarray['tropoi_pliromis_all'][$mybasketarray['tropos_pliromis']]))."',";
    }
    
    
    if ($table_into=='gks_hotel_reservation' or $table_into=='gks_transfer_reservation') {
      $sql.="affect_balance=1,";
      $sql.="affect_balance_all_poso=1,";
      $sql.="affect_balance_all_poso_type='pliroteo',";
      $sql.="affect_balance_poso=".number_format($gks_price_total,2,'.','').",";  
      $sql.="affect_balance_pros=1,";
    
    }
    

    
    $sql.="session_id='".$db_link->escape_string($kataxorisi['temp_session_id'])."',";
    
    $coupons_str='';
    if (count($coupons_all_in_order)>0) $coupons_str='|'.implode('|',$coupons_all_in_order).'|';
    $sql.="coupons='".$db_link->escape_string($coupons_str)."',";
    
    //echo $sql;
    if ($sql!='') {
      $sql=substr($sql, 0, strlen($sql)-1);
      $sql="update ".$table_into." set ".$sql." where ".$table_id."=".$id_order;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => base64_encode('sql error'));}
      
    }
  

  
    $myarray_new=array();
    $myarray_line_new=array();
    if ($table_into=='gks_orders') {
      $idiotites_new=get_order_details_txt($id_order, $myarray_new, $myarray_line_new); 
    } else if ($table_into=='gks_acc_inv') {
      $idiotites_new=get_acc_inv_details_txt($id_order, $myarray_new, $myarray_line_new); 
    }
    
    $sql="update ".$table_into." set idiotites='".$db_link->escape_string(json_encode($myarray_new))."' where ".$table_id." = ".$id_order." limit 1";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      return array('success' => false, 'message' => base64_encode('sql error'));} 
  
  
      
    //print '<pre>id_order:'.$id_order."\n"; 
    //echo time();die();
    
    
    unset($item);
    
    //print_r($data['gks_items']); 
    
    if ($table_into=='gks_acc_inv') {
      
      $acc_inv_product_id_ids=array();
      
      $map_products=array();
      $sql="SELECT gks_acc_inv_products.id_acc_inv_product, gks_eshop_products.id_product, gks_eshop_products.product_parent_id, gks_eshop_products.product_class,
      gks_eshop_products.product_base_type,gks_acc_inv_products.product_price_final_all_net
      FROM gks_acc_inv_products 
      LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product
      WHERE gks_eshop_products.id_product Is Not Null 
      AND gks_acc_inv_products.acc_inv_id=".$id_order."
      ORDER BY gks_acc_inv_products.product_aa;";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        return array('success' => false, 'message' => base64_encode('sql error'));} 
      while ($row= $result->fetch_assoc()) { 
      
        //$pbasetypes[0]=array('type'=>0, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_inv_acc_journal_id'=>0,'new_inv_acc_seira_id'=>0,'new_inv_acc_seira_code'=>'','error'=>''); //emporevma kai proion pane mazi
        //$pbasetypes[2]=array('type'=>2, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_inv_acc_journal_id'=>0,'new_inv_acc_seira_id'=>0,'new_inv_acc_seira_code'=>'','error'=>''); //ypiresia
          
        $map_products[]=array(
          'old' => 0, //$vid['id'], 
          'new' => $row['id_acc_inv_product'],
          'type' => $row['product_base_type'],
          'id_product' => $row['id_product'],
          'product_parent_id' => $row['product_parent_id'],
          'product_class' => $row['product_class'],
          'product_price_final_all_net' => $row['product_price_final_all_net'],
        );
        
        $acc_inv_product_id_ids[]=$row['id_acc_inv_product'];
      }
      
      $id_acc_inv_product_income_array=array();
      if (count($acc_inv_product_id_ids)>0) {
        $sql="select id_acc_inv_product_income from gks_acc_inv_products_income 
        where acc_inv_product_id in (".implode(',',$acc_inv_product_id_ids).")
        order by id_acc_inv_product_income";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));} 
        while ($row= $result->fetch_assoc()) { 
          $id_acc_inv_product_income_array[$row['id_acc_inv_product_income']]=true;
        }
        
        $sql="update gks_acc_inv_products_income set 
        acc_inv_product_id=0,aade_typos_xarakt_esodon_id=0,aade_katigoria_xarakt_esodon_id=0,acc_inv_product_income_ammount=0
        where acc_inv_product_id in (".implode(',',$acc_inv_product_id_ids).")";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));} 
        
      }
      //print_r($id_acc_inv_product_income_array);
      
  
      foreach ($map_products as $map_product) {
        //if ($pb['has_esoda']!=0) {
  
  
        $product_price_final_all_net=floatval($map_product['product_price_final_all_net']);
        
  
        $xarakt_product_id=$map_product['id_product'];
        if ($map_product['product_class']=='variable_item') {
          $xarakt_product_id=$map_product['product_parent_id'];
        }
        $sql="SELECT aade_typos_xarakt_esodon_id AS typos_id, 
        aade_katigoria_xarakt_esodon_id AS cat_id, 
        acc_inv_product_income_pososto AS pososto
        FROM gks_eshop_products_income
        WHERE product_id=".$xarakt_product_id."
        and (acc_eidos_parastatikou_id=0 or acc_eidos_parastatikou_id=".$acc_eidos_parastatikou_id.")
        ORDER BY id_product_income;";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));}
                      
        $final_all_net=0;
        $out_xarakt_esoda=array();
        $poso_sum=0;
        while ($row = $result->fetch_assoc()) {
          $final_all_net=$product_price_final_all_net; 
          if (empty($row['typos_id']) == false or empty($row['cat_id'])==false) {
            $poso=round(floatval($row['pososto'])/100 * $final_all_net,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            $poso_sum+=$poso;
            $out_xarakt_esoda[]=array(
              'typos_id'=> intval($row['typos_id']),
              'cat_id'=> intval($row['cat_id']),
              'pososto'=> floatval($row['pososto']),
              'poso' => $poso,
            );
          }
        }
        $diafora=$final_all_net-$poso_sum;
        if ($diafora!=0 and count($out_xarakt_esoda)>0) $out_xarakt_esoda[count($out_xarakt_esoda)-1]['poso']+=round($diafora,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        foreach ($out_xarakt_esoda as $val) {
          if (count($id_acc_inv_product_income_array)>0) {
            $id_acc_inv_product_income=array_key_first($id_acc_inv_product_income_array);
            unset($id_acc_inv_product_income_array[$id_acc_inv_product_income]);
            //print $id_acc_inv_product_income."\n";
            //print_r($id_acc_inv_product_income_array);
            
            $sql="update gks_acc_inv_products_income set 
            acc_inv_product_id=".$map_product['new'].",
            aade_typos_xarakt_esodon_id=".$val['typos_id'].",
            aade_katigoria_xarakt_esodon_id=".$val['cat_id'].",
            acc_inv_product_income_ammount=".number_format($val['poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."
            where id_acc_inv_product_income=".$id_acc_inv_product_income;
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
          } else {
            
            $sql="insert into gks_acc_inv_products_income (
            acc_inv_product_id,aade_typos_xarakt_esodon_id,aade_katigoria_xarakt_esodon_id,acc_inv_product_income_ammount
            ) values (
            ".$map_product['new'].",
            ".$val['typos_id'].",
            ".$val['cat_id'].",
            ".number_format($val['poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."
            )";
            $result = $db_link->query($sql);  
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              return array('success' => false, 'message' => base64_encode('sql error'));}  
          }
        }
        //print '<pre>';print_r($map_products);print_r($out_xarakt_esoda);print $final_all_net.'|'.$diafora;die();          
      
        
      }
      
      if (count($id_acc_inv_product_income_array)>0) {
        $sql="delete from gks_acc_inv_products_income where id_acc_inv_product_income in (".implode(',',array_keys($id_acc_inv_product_income_array)).")";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));}  
        
        
      }    
      //print '<pre>';print_r($map_products);
    }
    
    if ($table_into=='gks_transfer_reservation') {
    
      //echo 'gks_transfer_reservation_recalc_from_db '.$id_order.'|';
      gks_transfer_reservation_recalc_from_db($id_order, 'gks_price_final');
      //return array('success' => false, 'message' => base64_encode('<pre>gks_transfer_reservation_recalc_from_db after  '.$id_order));  
    }    
   
    if ($table_into=='gks_transfer_reservation') {
      $sql_pn="select gks_price_total from gks_transfer_reservation
      where id_transfer_reservation=".$id_order;
      $result_pn = $db_link->query($sql_pn);
      if (!$result_pn) {debug_mail(false,'error sql',$sql_pn);return array('success' => false, 'message' => base64_encode('sql error'));}
      if ($result_pn->num_rows>0) {
        $row_pn = $result_pn->fetch_assoc(); 
        $gks_price_net=$row_pn['gks_price_total'];
      }
    }
  
    $check_final_price_from_gks+=$gks_price_total;
    
    
    
    if ($user_id==0 and $table_into=='gks_transfer_reservation') {
      $ret_add_user=gks_transfer_reservation_set_user($id_order);
      debug_mail(false,'ret_add_user '.$id_order,print_r($ret_add_user,true));
      if ($user_id==0 and $ret_add_user['success']) $user_id=$ret_add_user['user_id'];
    }
        
    
    if ($order_is_new) {
   
      
   
      $url= $eshop['eshop_url'].'/wp-admin/post.php?post='.$woo_order_id.'&action=edit';
      $sxolio_log=gks_lang('Εισαγωγή από WooCommerce με ID').': <a href="'.$url.'" target="_blank">'.$woo_order_id.'</a>';
      //if ($table_into!='gks_hotel_reservation') {
        $sql="insert into ".$table_into."_links (
        ".$table_idr.",url,mydate,ip,user_id,download_status
        ) values (
        ".$id_order.",
        '".$db_link->escape_string($url)."',
        now(),
        '".$db_link->escape_string($gkIP)."',
        ".$my_wp_user_id.",
        2
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));} 
      //}
        
      if ($sxolio_log!='') {
        //$sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
        $sql="insert into ".$table_into."_log (".$table_idr.", add_date,user_id,sxolio) values (
        ".$id_order.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
        
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          return array('success' => false, 'message' => base64_encode('sql error'));} 
      }
      
      $customer_name=trim_gks(trim_gks($data['billing']['first_name']).' '.trim_gks($data['billing']['last_name']));
      if ($table_into=='gks_transfer_reservation') {
        $message=gks_lang('Νέα κράτηση με αριθμό').' <a href="/my/';
        $message.='admin-transfer-reservation-item.php';
      } else if ($table_into=='gks_hotel_reservation') {
        $message=gks_lang('Νέα κράτηση με αριθμό').' <a href="/my/';
        $message.='admin-hotel-reservation-item.php';
      } else if ($table_into=='gks_orders') {
        $message=gks_lang('Νέα παραγγελία με αριθμό').' <a href="/my/';
        $message.='admin-orders-item.php';
      } else if ($table_into=='gks_acc_inv') {
        $message=gks_lang('Νέα παραγγελία με αριθμό').' <a href="/my/';
        $message.='admin-acc-inv-item.php';
      }
      $message.='?id='.$id_order.'">#'.$id_order.'</a> '.gks_lang('από το WooCommerce').' '.
      '(Woo ID <a href="'.$url.'" target="_blank">#'.$woo_order_id.'</a>) '.gks_lang('του πελάτη').' ';
      if ($user_id>0) {
        $message.='<a href="/my/admin-users-item.php?id='.$user_id.'">'.$customer_name.'</a>';
      } else {
        $message.=$customer_name;
      }
      $message.=' '.gks_lang('αξίας').' <b>'. myCurrencyFormat($gks_price_total,false).'</b>';
      
      
      $sql="insert into gks_notification (
      message,for_user_id,`date_add`,for_date,has_ok,model,model_id
      )
      select
      '".$db_link->escape_string($message)."' as message,
      user_id as for_user_id,
      now() as `date_add`,
      now() as `for_date`,
      0 as has_ok,'";
      if ($table_into=='gks_transfer_reservation') {
        $sql.='transfer';
      } else if ($table_into=='gks_hotel_reservation') {
        $sql.='reservation';
      } else if ($table_into=='gks_orders') {
        $sql.='orders';
      } else if ($table_into=='gks_acc_inv') {
        $sql.='acc_inv';
      }
      $sql.="' as model,
      ".$id_order." as model_id
      from gks_notification_userperm where notification_type_id=";
      if ($table_into=='gks_transfer_reservation') {
        $sql.='1010';
      } else if ($table_into=='gks_hotel_reservation') {
        $sql.='1010';
      } else if ($table_into=='gks_orders') {
        $sql.='1020';
      } else if ($table_into=='gks_acc_inv') {
        $sql.='1030';
      }
      $sql.=" and from_admin=1 and from_user=1".gks_notification_userperm_internal_users();
      //from ".GKS_WP_TABLE_PREFIX."users where gks_wp_capabilities like '%ordermanager%' or gks_wp_capabilities like '%adminmy%';";
      $result_insert = $db_link->query($sql);
      if (!$result_insert) debug_mail(false,'notification error sql',$sql);    
      
      
      
      $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.user_email
      FROM gks_notification_userperm 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE ".GKS_WP_TABLE_PREFIX."users.user_email<>''
      AND gks_notification_userperm.notification_type_id=";
      if ($table_into=='gks_transfer_reservation') {
        $sql.='1010';
      } else if ($table_into=='gks_hotel_reservation') {
        $sql.='1010';
      } else if ($table_into=='gks_orders') {
        $sql.='1020';
      } else if ($table_into=='gks_acc_inv') {
        $sql.='1030';
      }
      $sql.=" AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_email=1".gks_notification_userperm_internal_users();
      //debug_mail(false,'sql',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
      } else {
        $mysubject='';$model_name='';
        if ($table_into=='gks_transfer_reservation') {
          $mysubject=gks_lang('Νέα κράτηση με αριθμό').' '.$id_order;
          $model_name='transfer';
        } else if ($table_into=='gks_hotel_reservation') {
          $mysubject=gks_lang('Νέα κράτηση με αριθμό').' '.$id_order;
          $model_name='hotel-reservation';
        } else if ($table_into=='gks_orders') {
          $mysubject=gks_lang('Νέα παραγγελία με αριθμό').' '.$id_order;
          $model_name='order';
        } else if ($table_into=='gks_acc_inv') {
          $mysubject=gks_lang('Νέα παραγγελία με αριθμό').' '.$id_order;
          $model_name='acc_inv';
        }
        $replaces=array();
        $replaces[] = array('[[message]]', $message);
        
        while ($row = $result->fetch_assoc()) {
          $params=array(
            'model'=>$model_name,
            'model_id'=>$id_order,
            'to'=>$row['user_email'],
            'subject'=>$mysubject,
            'template'=>3, //'empty.html',
            'replaces'=>$replaces,
          );
              
          $send_email_res = gks_mymail_template($params);
          
        }
      }
            
      

      if ($table_into=='gks_transfer_reservation') {
        $message=gks_lang('Νέα κράτηση με αριθμό').' '.$id_order.' '.GKS_SITE_URL.'my/admin-transfer-reservation-item.php?id='.$id_order;
      } else if ($table_into=='gks_hotel_reservation') {
        $message=gks_lang('Νέα κράτηση με αριθμό').' '.$id_order.' '.GKS_SITE_URL.'my/admin-hotel-reservation-item.php?id='.$id_order;
      } else if ($table_into=='gks_orders') {
        $message=gks_lang('Νέα παραγγελία με αριθμό').' '.$id_order.' '.GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order;
      } else if ($table_into=='gks_acc_inv') {
        $message=gks_lang('Νέα παραγγελία με αριθμό').' '.$id_order.' '.GKS_SITE_URL.'my/admin-acc-inv-item.php?id='.$id_order;
      }
      $message.=' '.gks_lang('από το WooCommerce με ID').' '.$woo_order_id.' '.$url.
      ' '.gks_lang('του πελάτη').' '.$customer_name;
      if ($user_id>0) {
        $message.=' '.GKS_SITE_URL.'my/admin-users-item.php?id='.$user_id;
      }
      $message.=' '.gks_lang('αξίας').' '. myCurrencyFormat($gks_price_total,false);
            
      $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
      FROM gks_notification_userperm 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
      AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
      AND gks_notification_userperm.notification_type_id=";
      if ($table_into=='gks_transfer_reservation') {
        $sql.='1010';
      } else if ($table_into=='gks_hotel_reservation') {
        $sql.='1010';
      } else if ($table_into=='gks_orders') {
        $sql.='1020';
      } else if ($table_into=='gks_acc_inv') {
        $sql.='1030';
      }
      $sql.=" AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
      //debug_mail(false,'sql',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
      } else { 
        $send_viber=array();
        while ($row = $result->fetch_assoc()) {
          $send_viber[]=$row['viber_id'];
        }
        foreach ($send_viber as $value) {
          gks_viber_send(substr($table_into, 4) ,$id_order ,$value,$message);
        } 
      }

      
  
      $customer_note=''; if (isset($data['customer_note'])) $customer_note=trim_gks($data['customer_note']);
      if ($customer_note!='') {
        $field_text=gks_lang('Μήνυμα από πελάτη κατά την προσθήκη της παραγγελίας').': '.$customer_note;
        
        $sql="insert into ";
        if ($table_into=='gks_transfer_reservation') $sql.='gks_transfer_reservation_messages';
        if ($table_into=='gks_hotel_reservation') $sql.='gks_hotel_reservation_messages';
        if ($table_into=='gks_orders') $sql.='gks_orders_messages';
        if ($table_into=='gks_acc_inv') $sql.='gks_acc_inv_messages';
        
        $sql.=" (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,";
        if ($table_into=='gks_transfer_reservation') $sql.='transfer_reservation_id,transfer_reservation_message';
        if ($table_into=='gks_hotel_reservation') $sql.='hotel_reservation_id,hotel_reservation_message';
        if ($table_into=='gks_orders') $sql.='order_id,order_message';
        if ($table_into=='gks_acc_inv') $sql.='acc_inv_id,acc_inv_message';
      
        $sql.=") values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        ".$user_id.",
        ".$id_order.",
        '".$db_link->escape_string($field_text)."'
        )";
        $result = $db_link->query($sql);
        if (!$result) debug_mail(false,'error sql',$sql);
      }
      
  
    } else {
      
      //return array('success' => false, 'message' => base64_encode('<pre>ffffffffffffff'));
      //echo 'ffffffffffffffffffffff';
      //print $id_order.' - '.$table_into;die();
      if ($table_into=='gks_transfer_reservation') {
        gks_transfer_reservation_sxolio_log($id_order,$row_old,$products_old,gks_lang('Ενημέρωση από WooCommerce').'<br>',$gks_custom_row_old);
      } else if ($table_into=='gks_hotel_reservation') {
        gks_hotel_reservation_sxolio_log($id_order,$row_old,$products_old,gks_lang('Ενημέρωση από WooCommerce').'<br>',$gks_custom_row_old);
      } else if ($table_into=='gks_orders') {
        gks_order_sxolio_log($id_order,$row_old,$products_old,$extra_address_old,gks_lang('Ενημέρωση από WooCommerce').'<br>',$gks_custom_row_old);
      } else if ($table_into=='gks_acc_inv') {
        gks_inv_sxolio_log($id_order,$row_old,$products_old,$extra_address_old,gks_lang('Ενημέρωση από WooCommerce').'<br>',[],$gks_custom_row_old);
      }
 
    }
    
    
   
    
    
    if ($table_into=='gks_hotel_reservation') {
      
   
      
      
      gks_hotel_reservation_room_day_recs($id_order,$roolist_day,
        $order_state,
        strtotime($kataxorisi['first_room']['check_in']),
        strtotime($kataxorisi['first_room']['check_out'])
      );
         

    }
    

    
    
    $remote_url='';
    if ($table_into=='gks_transfer_reservation') {
      $remote_url=GKS_SITE_URL.'my/admin-transfer-reservation-item.php?id='.$id_order;
    } else if ($table_into=='gks_hotel_reservation') {
      $remote_url=GKS_SITE_URL.'my/admin-hotel-reservation-item.php?id='.$id_order;
    } else if ($table_into=='gks_orders') {
      $remote_url=GKS_SITE_URL.'my/admin-orders-item.php?id='.$id_order;
    } else if ($table_into=='gks_acc_inv') {
      $remote_url=GKS_SITE_URL.'my/admin-acc-inv-item.php?id='.$id_order;
    }
        
    
    $woo_back_meta_data[] = array('obj' => $table_into, 'id' => $id_order, 'url' => $remote_url);


    gks_plugins_functions_run('woo_import_order_after',array(
      'eshop' => &$eshop,
      'data' => &$data,
      'kataxorisi' => &$kataxorisi,
      'woo_settings' => &$woo_settings,
      'force' => &$force,
      'id_order' => &$id_order
    ));
    
  }
  
  

  $data_send = array(
  	'cmd'=>'set_order_id',
  	'id_order'=>$woo_order_id, 
    'woosettings' => false,
    'data'=>$woo_back_meta_data,
  );
  //return array('success' => false, 'message' => base64_encode('<pre>'.print_r($data,true)));
  
  $ret=gks_woo_post($eshop, $data_send);
    


  $diafora_final_price=$check_final_price_from_gks - $check_final_price_from_woo;
  if (abs($diafora_final_price)>=0.005) {
    debug_mail(false,'woo import price diafora: '.$diafora_final_price,"\n".'woo id: '.$woo_order_id."\n".'check_final_price_from_woo: '.$check_final_price_from_woo."\n".'check_final_price_from_gks: '.$check_final_price_from_gks."\n".print_r($data,true));
    
  }
  
  
  
  
  if ($table_into=='gks_transfer_reservation' and count($send_to_other_system_ids)>0) {
    
    foreach ($send_to_other_system_ids as $ccc_val_id) {
      gks_plugins_functions_run('function_woo2_send_to_other_system',array(
        'id'=>&$ccc_val_id,
      ));
    }
  }
  
  
  if ($table_into=='gks_transfer_reservation' and GKS_TRANSFER_AUTO_MYDATA_EMAIL==true and count($transfer_auto_mydata_email)>0) {
    foreach ($transfer_auto_mydata_email as $item_send_id) {
      $ret_auto_mydata_email=gks_transfer_reservation_ekdosi_auto_inv_acc_print_email($item_send_id);
      debug_mail(false,'gks_transfer_reservation_ekdosi_auto_inv_acc_print_email '.$item_send_id,print_r($ret_auto_mydata_email,true));
      
      
    }
  }
  
  
  //$db_link->query("update gks_async_queue set status='pending' where guid='5b7e25817d4ab7b9798e3a65f06c618d'"); 
  //print '<pre>'.$sql;print_r($product);   die();

    
  $save_but_message='';
  
  return array('success' => true, 'message' => base64_encode('OK'),'save_but_message'=>base64_encode($save_but_message));
  
}


function gks_transfer_reservation_set_user($id) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;  
  
  $sql="select * from gks_transfer_reservation where id_transfer_reservation=".$id;
  $result_go = $db_link->query($sql);
  if (!$result_go) {
    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => 'sql error');}   

  if ($result_go->num_rows==0) return array('success' => false, 'message' => 'not found');
  $row_go = $result_go->fetch_assoc();
  
  $user_id_org=intval($row_go['user_id']);

  
  $user_id=intval($row_go['user_id']);
  $user_first_name=trim_gks($row_go['user_first_name']);
  $user_last_name=trim_gks($row_go['user_last_name']);
  $user_email=trim_gks($row_go['user_email']);
  $user_mobile=trim_gks($row_go['user_mobile']);
  
  $user_lang=trim_gks($row_go['user_lang']);
  $eponimia=trim_gks($row_go['eponimia']);
  $title=trim_gks($row_go['title']);
  $afm=trim_gks($row_go['afm']);
  $doy=trim_gks($row_go['doy']);
  $epaggelma=trim_gks($row_go['epaggelma']);
  $ma_odos=trim_gks($row_go['ma_odos']);
  $ma_orofos=trim_gks($row_go['ma_orofos']);
  $ma_perioxi=trim_gks($row_go['ma_perioxi']);
  $ma_poli=trim_gks($row_go['ma_poli']);
  $ma_tk=trim_gks($row_go['ma_tk']);
  $ma_country_id=intval($row_go['ma_country_id']);
  $ma_nomos_id=intval($row_go['ma_nomos_id']);
  
  
  $id_return=0;
  $sql="select id_transfer_reservation,user_id from gks_transfer_reservation where is_return_transfer_for_id=".$id;
  $result_return = $db_link->query($sql);
  if (!$result_return) {
    debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
    return array('success' => false, 'message' => 'sql error');}   
  if ($result_return->num_rows>0) {
    $row_return = $result_return->fetch_assoc();
    $id_return=intval($row_return['id_transfer_reservation']);
    $user_id_return=intval($row_return['user_id']);
    if ($user_id>0 and $user_id_return==0) {
      $sql="update gks_transfer_reservation set user_id=".$user_id." where id_transfer_reservation=".$id_return;
      $result_return = $db_link->query($sql);
      if (!$result_return) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => 'sql error');}   
      return array('success' => true, 'message' => 'OK', 'user_id'=> $user_id);
    }
  }
  
  
  if ($user_id>0) {
    return array('success' => true, 'message' => 'OK', 'user_id'=> $user_id);
  }
  
  if ($user_id==0 and strlen($user_mobile)>=8) {
    $sql="SELECT user_id
    FROM gks_users_communication
    WHERE comm_value like '".$db_link->escape_string($user_mobile)."' and comm_type='phone'
    ORDER BY user_id, id_user_communication";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => 'sql error');}   
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $user_id=intval($row['user_id']);
    }
  }
  
  if ($user_id==0 and strlen($user_email)>=8) {
    $sql="SELECT user_id
    FROM gks_users_communication
    WHERE comm_value like '".$db_link->escape_string($user_email)."' and comm_type='email'
    ORDER BY user_id, id_user_communication";
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => 'sql error');}   
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $user_id=intval($row['user_id']);
    }
  }
  
  if ($user_id==0) {

    $randid=rand(10000,99999);
    $user_login='newuser'.$randid;
    $user_pass_pure=rand(1000,9999). rand(1000,9999);  
    $user_pin= rand(10000,99999);
    $display_name=trim_gks($user_first_name.' '.$user_last_name);
    
    
    $sql="insert into ".GKS_WP_TABLE_PREFIX."users (
    mydate_add,user_id_add,myip,
    user_login,display_name,user_nicename,gks_nickname,user_registered,update_from_gks,user_url
    ) values (
    now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    '".$user_login."','".$user_login."','".$user_login."','".$user_login."',NOW(),1,''
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => 'sql error');} 

    $user_id = $db_link->insert_id;
    
    $sql="insert into ".GKS_WP_TABLE_PREFIX."usermeta (user_id,meta_key,meta_value) values (".$user_id.",'nickname','newuser".$randid."')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => 'sql error');} 
    

        
    
    $sql="update ".GKS_WP_TABLE_PREFIX."users set 
    user_pass='".$db_link->escape_string(wp_hash_password($user_pass_pure))."',
    user_pass_pure='".$db_link->escape_string($user_pass_pure)."',
    user_pin='".$db_link->escape_string($user_pin)."',
    user_login='".$db_link->escape_string($user_login)."',
    user_nicename='".$db_link->escape_string($display_name)."',
    user_email='".$db_link->escape_string($user_email)."',
    user_url='',
    viber_id='',
    display_name='".$db_link->escape_string($display_name)."',
    fiscal_position_id=1,
    pricelist_id=1,
    generic_ekprosi=0,
    user_activation_key='',
    user_status=0,
    gks_sex=0,
    gks_lang='".$db_link->escape_string($user_lang)."',
    
    update_from_gks=1,
    user_id_edit=".$my_wp_user_id.",
    mydate_edit=now(),
    myip='".$db_link->escape_string($gkIP)."'
    
    where ID=".$user_id." limit 1";    
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => 'sql error');} 



    $user_object = new WP_User($user_id);
    $user_object->add_role('subscriber');

    update_user_meta( $user_id, 'nickname', $display_name);
    update_user_meta( $user_id, 'first_name', $user_first_name);
    update_user_meta( $user_id, 'last_name', $user_last_name);
    update_user_meta( $user_id, 'display_name', $display_name);
    update_user_meta( $user_id, 'mobile', $user_mobile);


    $sql="insert into gks_users (user_id,mydate_add,user_id_add,myip) values (".$user_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => 'sql error');} 

    $sql="update gks_users set  
    eponimia = '".$db_link->escape_string($eponimia)."',
    title = '".$db_link->escape_string($title)."',
    afm = '".$db_link->escape_string($afm)."',
    doy = '".$db_link->escape_string($doy)."',
    epaggelma = '".$db_link->escape_string($epaggelma)."',
    ma_odos = '".$db_link->escape_string($ma_odos)."',
    ma_orofos = '".$db_link->escape_string($ma_orofos)."',
    ma_perioxi = '".$db_link->escape_string($ma_perioxi)."',
    ma_poli = '".$db_link->escape_string($ma_poli)."',
    ma_tk = '".$db_link->escape_string($ma_tk)."',
    ma_country_id = ".$ma_country_id.",
    ma_nomos_id = ".$ma_nomos_id.",
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where user_id=".$user_id." limit 1";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => 'sql error');} 

    if (strlen($user_email)>=8) {
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      user_id,comm_type,comm_value,comm_descr,comm_primary
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$user_id.",'email','".$db_link->escape_string($user_email)."','',1
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => 'sql error');} 
    }
    if (strlen($user_mobile)>=8) {
      $sql="insert into gks_users_communication (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      user_id,comm_type,comm_value,comm_descr,comm_primary
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$user_id.",'phone','".$db_link->escape_string($user_mobile)."','',1
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => 'sql error');} 
    }
    
    gks_user_update_comm_search($user_id);
    gks_user_update_dav($user_id,true);
        
    echo $user_id;
  }
  
  if ($user_id>0) {
    $sql="update gks_transfer_reservation set user_id=".$user_id." where id_transfer_reservation=".$id;
    $result_return = $db_link->query($sql);
    if (!$result_return) {
      debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
      return array('success' => false, 'message' => 'sql error');}   
    if ($id_return>0) {
      $sql="update gks_transfer_reservation set user_id=".$user_id." where id_transfer_reservation=".$id_return;
      $result_return = $db_link->query($sql);
      if (!$result_return) {
        debug_mail(false,'error sql',$sql.' '.$db_link->errno . '-'.$db_link->error);
        return array('success' => false, 'message' => 'sql error');}   
    }
    
    
  }
  
  
  return array('success' => true, 'message' => 'OK', 'user_id'=> $user_id); 
  
}


