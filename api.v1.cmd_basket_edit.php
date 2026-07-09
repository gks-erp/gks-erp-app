<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_cmd_basket_edit($id_hotel,$row_hotel,$input_data) {
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
  
  //return '<pre>'.print_r($_gks_session,true).'</pre>';
  
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

  
  $my_wp_user_id=0;

  $_POST=$input_data['post'];
  
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.$id_hotel.
  //'|'.$hotel_params['hotel_reservation_can_select_room'].'|'.
  //print_r($hotel_params,true).'</pre>'));
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($_gks_session,true).'</pre>'));
  //return $return;
  
  
unset($_gks_session['gks']['confirm']);
unset($_gks_session['gks']['alphabank']);
gks_erp_cookie_save($gks_erp_cookie_id);

//echo 'hhhhhh';
//$return = array('success' => false, 'message' => base64_encode('dfg ds fgdfgdfg'),'out' => '');
//echo json_encode($return); die(); 

$showloading=intval($_POST['showloading']);
$showerrors=intval($_POST['showerrors']);
$gonext=intval($_POST['gonext']);
  

$mycmd ='';
$myindex = 0;
$myproduct_id = 0;
$myobject = 0;
$myfile = '';
$myvalue = 0;


if (!isset($_POST['cmd'])) {
  debug_mail(false,'basket-edit.php error on cmd');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').' (1)<br>'.gks_lang('Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
  echo json_encode($return); die();
}
$mycmd=trim_gks(stripslashes(urldecode($_POST['cmd'])));

if (!isset($_POST['myindex'])) {
  debug_mail(false,'basket-edit.php error on myindex');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα στο Index του προϊόντος').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
  echo json_encode($return); die();
}
$myindex=intval($_POST['myindex']);

if (!isset($_POST['product_id'])) {
  debug_mail(false,'basket-edit.php error on product_id');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα στο ID του προϊόντος<br>Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
  echo json_encode($return); die();
}
$myproduct_id=intval($_POST['product_id']);

if (!isset($_POST['object'])) {
  debug_mail(false,'basket-edit.php error on object');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα στο αντικείμενο του προϊόντος<br>Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
  echo json_encode($return); die();
}
$myobject=intval($_POST['object']);

if (isset($_POST['file'])) $myfile=trim_gks(stripslashes(urldecode($_POST['file'])));
if (isset($_POST['value'])) $myvalue=intval($_POST['value']);

//$my_page_title=gks_lang('Επεξεργασία καλαθιού'); 
//db_open();
//stat_record();




$rsrv_count=-1;
$out=array();
$also_delete='';





$cmd_is_for_object=false;
$cmd_is_for_file=false;
$cmd_is_for_coupon=false;
$cmd_is_for_delivery_payment=false;
$cmd_is_for_rooms=false;
if ($mycmd=='rowdelete' or $mycmd=='rowposotita') {
  $cmd_is_for_object=true;
} else if ($mycmd=='filedelete' or $mycmd=='fileposotita') {
  $cmd_is_for_file=true;
} else if ($mycmd=='couponadd' or $mycmd=='coupondelete') {
  $cmd_is_for_coupon=true;
  $mycoupon=$myfile;
} else if ($mycmd=='delivery_payment') {
  $cmd_is_for_delivery_payment=true;
  $mydelivery_id=$myproduct_id;
  $mypayment_id=$myobject;
} else if ($mycmd=='rooms') {
  $cmd_is_for_rooms=true;
  
} else {
  debug_mail(false,'basket-edit.php error on cmd for object: '.$mycmd);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').' (2)<br>'.gks_lang('Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
  echo json_encode($return); die();  
}



//rooms start
$datasend_rooms = ''; if (isset($_POST['datasend_rooms']) and $_POST['datasend_rooms'] != '') $datasend_rooms = base64_decode($_POST['datasend_rooms']);
//print '<pre>';
//print_r($datasend_rooms);
//die();

if ($datasend_rooms != '') {
  $data = json_decode($datasend_rooms,true);

  //print '<pre>';
  //print_r($data);
  //print '</pre>';

  $myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];
  
  
  foreach ($data as $rsrv_aa => $reservation) {
    if (isset($reservation['selrooms'])) {
      foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
        foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
          if (isset($myreservations) and 
              isset($myreservations[$rsrv_aa]['selrooms']) and 
              isset($myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]) and 
              isset($myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items']) and 
              isset($myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa])) {
                
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['is_delete'] = $myroom['is_delete'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['is_same'] = $myroom['is_same'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['first_name'] = $myroom['first_name'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['last_name'] = $myroom['last_name'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['email'] = $myroom['email'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['mobile'] = $myroom['mobile'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['lang'] = $myroom['lang'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['ma_odos'] = $myroom['ma_odos'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['ma_orofos'] = $myroom['ma_orofos'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['ma_perioxi'] = $myroom['ma_perioxi'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['ma_poli'] = $myroom['ma_poli'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['ma_tk'] = $myroom['ma_tk'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['ma_country_id'] = $myroom['ma_country_id'];
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['ma_nomos_id'] = $myroom['ma_nomos_id'];
            
            if ($hotel_params['hotel_reservation_can_select_room']) {
              $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['room_item_id'] = intval($myroom['room_item_id']);
              $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_adults'] = intval($myroom['rnum_adults']);
              $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_childs'] = intval($myroom['rnum_childs']);
            }
            
            //$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($myroom, true)));
            //echo json_encode($return); die();
    
          }
        }
        unset($myroom);
      }
      unset($selroom);
    }
  }
  unset($reservation);
  
  $also_delete_product_id=0;
  
  foreach ($myreservations as $rsrv_aa => $reservation) {
    if (isset($reservation['selrooms'])) {
      foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
        foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
          if ($myroom['is_delete'] == 1) {
            unset($myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]);
            $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['num']--;
          }
        }
        if (count($myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items']) == 0) {
          
          $also_delete_product_id=$myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['roomtype']['product_id'];
          unset($myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]);
        }
        
      }
      if (count($myreservations[$rsrv_aa]['selrooms']) == 0) {
        unset($myreservations[$rsrv_aa]);
      }   
    } else {
      unset($myreservations[$rsrv_aa]);
    }
  }
  
  if ($also_delete_product_id > 0) {
    $other_product_id_exist=false;
    foreach ($myreservations as $rsrv_aa => $reservation) {
      foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
        if ($myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['roomtype']['product_id'] == $also_delete_product_id) {
          $other_product_id_exist=true;
          break 2;
        }
      }
    }
    if ($other_product_id_exist == false) {
      $also_delete=$also_delete_product_id.'_0';
    }
  }
  $rerv_error='';
  $gks_total_visitors_adults=0;
  $gks_total_visitors_childs=0;  
  foreach ($myreservations as $rsrv_aa => $reservation) {
    $reservation_rnum_adults=0;
    $reservation_rnum_childs=0;    
    foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
      foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
        
        if ($myroom['rnum_adults']>0) {
          $reservation_rnum_adults+=$myroom['rnum_adults'];
          $gks_total_visitors_adults+=$myroom['rnum_adults'];
          
        }
        if ($myroom['rnum_childs']>0) {
          $reservation_rnum_childs+=$myroom['rnum_childs'];
          $gks_total_visitors_childs+=$myroom['rnum_childs'];
        } 
      }
    }
    
    $rsrv_aa_plus1=$rsrv_aa + 1;
    if ($reservation_rnum_adults != $reservation['adults']) {
      $msg_temp=gks_lang('Στην <b>Κράτηση [1]</b> ορίσατε <b>[2]</b> ενήλικες στα δωμάτια, ενώ αρχικά είχατε επιλέξει <b>[3]</b><br>');
      $msg_temp=str_replace('[1]', $rsrv_aa_plus1, $msg_temp);
      $msg_temp=str_replace('[2]', $reservation_rnum_adults, $msg_temp);
      $msg_temp=str_replace('[3]', $reservation['adults'], $msg_temp);
      $rerv_error.=$msg_temp;
    }
    if ($reservation_rnum_childs != $reservation['childs']) {
      $msg_temp=gks_lang('Στην <b>Κράτηση [1]</b> ορίσατε <b>[2]</b> παιδιά στα δωμάτια, ενώ αρχικά είχατε επιλέξει <b>[3]</b><br>');
      $msg_temp=str_replace('[1]', $rsrv_aa_plus1, $msg_temp);
      $msg_temp=str_replace('[2]', $reservation_rnum_childs, $msg_temp);
      $msg_temp=str_replace('[3]', $reservation['childs'], $msg_temp);
      
      $rerv_error.=$msg_temp;
    }
  }

  
  
  $elems=array(); $total_sum=0; $total_visitors=0; $total_dianiktereuseis=0; $total_domatia=0;
  hotel_basket_rsrv_calc($id_hotel,$myreservations, $elems, $total_sum, $total_visitors, $total_dianiktereuseis, $total_domatia, false);
  $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'] = $myreservations;




//  $elems[] = array('gks_total_reservations_span', count($myreservations));
//  $elems[] = array('gks_total_domatia_span', $total_domatia);
//  $elems[] = array('gks_total_dianiktereuseis_span', $total_dianiktereuseis);
//  $elems[] = array('gks_total_visitors_span', $total_visitors);
//  $elems[] = array('gks_total_price_span', myCurrencyFormat($total_sum,true, true));

  //'rsrv_count' => count($myreservations),
  $rsrv_count = count($myreservations);

  $out[]=array('id' => '#gks_total_reservations_span', 'type' => 'html', 'data' => base64_encode(count($myreservations)));
  $out[]=array('id' => '#gks_total_domatia_span', 'type' => 'html', 'data' => base64_encode($total_domatia));
  $out[]=array('id' => '#gks_total_dianiktereuseis_span', 'type' => 'html', 'data' => base64_encode($total_dianiktereuseis));
  $out[]=array('id' => '#gks_total_visitors_span', 'type' => 'html', 'data' => base64_encode($total_visitors));
  $out[]=array('id' => '#gks_total_price_span', 'type' => 'html', 'data' => base64_encode(myCurrencyFormat($total_sum,true, true)));
}
//roms end



if ($cmd_is_for_object) { //cmd is for object

  foreach ($_gks_session['gks']['basket']['products'] as $index => $product) {
    foreach ($product['objects'] as $keyo => $object) {
      
      $row_id=$index.'_'.$product['product_id']['id_product'].'_'.$keyo;
      
      if ($index == $myindex && $product['product_id']['id_product'] == $myproduct_id and $keyo == $myobject) {
          
        switch ($mycmd) {
        case 'rowdelete':
          unset($_gks_session['gks']['basket']['products'][$index]['objects'][$keyo]);
          if (count($_gks_session['gks']['basket']['products'][$index]['objects'])==0) {
            unset($_gks_session['gks']['basket']['products'][$index]);
          }        
          break;
        case 'rowposotita':
          $_gks_session['gks']['basket']['products'][$index]['objects'][$keyo]['copies'] = $myvalue;
          //if ($object['type'] == 'multi') { 
          //  $out[] =array('id' => '#input_'.$row_id,'type'=>'val', 'data' => base64_encode($myvalue));
          //} else {
          //  $out[] =array('id' => '#rowposotita_'.$row_id,'type'=>'html', 'data' => base64_encode($myvalue));
          //}
          break;  
        default:
          debug_mail(false,'basket-edit.php error on cmd for object: '.$mycmd);
          $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').' (3)<br>'.gks_lang('Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
          echo json_encode($return); die();
        }      
      
        break 2;
        
      }
      
    }
  }  
  
  
}
if ($cmd_is_for_file) { //cmd is for file


  foreach ($_gks_session['gks']['basket']['products'] as $index => $product) {
    foreach ($product['objects'] as $keyo => $object) {
      
      
      foreach ($object['files'] as $keyf => $file) {
        $file_id=$product['product_id']['id_product'].'_'.$keyo.'_'.$file['id'];
        
        if ($index == $myindex and $product['product_id']['id_product'] == $myproduct_id and $keyo == $myobject and $file['id'] == $myfile) {
        
          
          switch ($mycmd) {
          case 'filedelete':
          
            unset($_gks_session['gks']['basket']['products'][$index]['objects'][$keyo]['files'][$keyf]);
            //if ($object['type'] == 'multi' == false) {
              if (count($_gks_session['gks']['basket']['products'][$index]['objects'][$keyo]['files'])==0) {
                $also_delete=$index.'_'.$product['product_id']['id_product']. '_' . $keyo;
                unset($_gks_session['gks']['basket']['products'][$index]['objects'][$keyo]);
                if (count($_gks_session['gks']['basket']['products'][$index]['objects'])==0) {
                  unset($_gks_session['gks']['basket']['products'][$index]);
                }
              }
            //}
            break;
          case 'fileposotita':
            if ($product['product_id']['product_is_digital']) {//digital
              $_gks_session['gks']['basket']['products'][$index]['objects'][$keyo]['files'][$keyf]['copies'] = 1;
              if ($file['copies'] + $myvalue >1) {
                $msg_temp=gks_lang('Το είδος <b>[1]</b> είναι ψηφιακό<br>Η ποσότητα θα πρέπει να είναι 1');
                $msg_temp=str_replace('[1]', $product['product_id']['product_descr'], $msg_temp);
                $errors_response=$msg_temp;
                $out[] =array('id' => '#input_file_'.$myindex.'_'.$file_id,'type'=>'val', 'data' => base64_encode('1'));
                $return = array('success' => false, 'message' => base64_encode($errors_response),'out' => $out,);
                echo json_encode($return); die();                 
              }
            } 
          
            if ($product['product_id']['product_need_multi_files']!=0) {
              $mycc=0;
              $oldvalue=0;
              foreach ($object['files'] as $filetemp) {
                if ($filetemp['id'] == $myfile) {
                  $oldvalue = $filetemp['copies'];
                  $mycc+=$myvalue;
                } else {
                  $mycc+=$filetemp['copies'];  
                }
              }
              
              if ($mycc > $product['product_id']['product_need_multi_files_max']) {
                $msg_temp=gks_lang('Το είδος <b>[1]</b> μπορεί να δεχθεί έως <b>[2]</b> φωτογραφίες.<br>Αυτή η προσθήκη δεν μπορεί να γίνει διότι το αντικείμενο <b>[3]</b> θα είχε συνολικά <b>[4]</b> φωτογραφίες.<br>');
                $msg_temp=str_replace('[1]', $product['product_id']['product_descr'], $msg_temp);
                $msg_temp=str_replace('[2]', $product['product_id']['product_need_multi_files_max'], $msg_temp);
                $msg_temp=str_replace('[3]', $object['descr'], $msg_temp);
                $msg_temp=str_replace('[4]', $mycc, $msg_temp);
                $errors_response=$msg_temp;
                
                $out[] =array('id' => '#input_file_'.$myindex.'_'.$file_id,'type'=>'val', 'data' => base64_encode($oldvalue));

                
                $return = array('success' => false, 'message' => base64_encode($errors_response),'out' => $out,);
                echo json_encode($return); die();                
              }
            }
            $_gks_session['gks']['basket']['products'][$index]['objects'][$keyo]['files'][$keyf]['copies'] = $myvalue;
            $out[] =array('id' => '#input_file_'.$myindex.'_'.$file_id,'type'=>'val', 'data' => base64_encode($myvalue));
            break;  
          default:
            debug_mail(false,'basket-edit.php error on cmd for object: '.$mycmd);
            $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').' (4)<br>'.gks_lang('Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
            echo json_encode($return); die();
          }      
        
          break 2;        
        }
        
      }
    }
  }  
  
//$return = array('success' => false, 'message' => base64_encode($mycmd.'-'.$cmd_is_for_object.'-'.$myproduct_id.'-'.$myobject.'-'.$myfile.'-'.$myvalue));
//echo json_encode($return); die();  
  
}  

if ($cmd_is_for_coupon) { //cmd is for coupon
  
  //get pricelist
  if ($my_wp_user_id < 1) {
    //not login
    $pricelist_id = 1;
  } else {
    $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.pricelist_id, gks_eshop_pricelist.pricelist_disable
    FROM ".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist
    WHERE gks_eshop_pricelist.pricelist_disable=0 and ".GKS_WP_TABLE_PREFIX."users.ID=".$my_wp_user_id;
    $res = $db_link->query($sql);
    if (!$res) {
      debug_mail(false,'basket-edit.php error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
      echo json_encode($return); die();
    }    
    $row = $res->fetch_assoc();
    $pricelist_id = $row['pricelist_id'];
  }
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
        debug_mail(false,'basket-edit.php error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
        echo json_encode($return); die(); 
      }
      if ($result->num_rows == 0) {
        debug_mail(false,'coupon not found:'.$mycoupon );
        $msg_temp=gks_lang('Δεν βρέθηκε το κουπόνι <b>[1]</b>').'<br>'.
        gks_lang('Βεβαιωθείτε ότι το έχετε γράψει σωστά');
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
              debug_mail(false,'coupon not start:'.$mycoupon );
              $msg_temp=gks_lang('Δεν βρέθηκε το κουπόνι <b>[1]</b>').'<br>'.gks_lang('Βεβαιωθείτε ότι το έχετε γράψει σωστά');
              $msg_temp=str_replace('[1]', $mycoupon, $msg_temp);
              $return = array('success' => false, 'message' => base64_encode($msg_temp));
              echo json_encode($return); die();          
            }
            debug_mail(false,'coupon expire',$mycoupon.':'. showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i:s', 1));
            $msg_temp=gks_lang('Το κουπόνι <b>[1]</b> έχει λήξει στις<br>[2]');
            $msg_temp=str_replace('[1]', $pricelist_item_coupon, $msg_temp);
            $msg_temp=str_replace('[2]', showDate(strtotime($row['pricelist_item_date_to']), 'd/m/Y H:i:s', 1), $msg_temp);
            $return = array('success' => false, 'message' =>  base64_encode($msg_temp));
            echo json_encode($return); die();          
          }
        }
      }
      
      if (isset($_gks_session['gks']['basket']['coupons'][$pricelist_item_coupon])) {
        debug_mail(false,'coupon already exist:'. $mycoupon);
        $msg_temp=gks_lang('Το κουπόνι <b>[1]</b> το έχετε καταχωρήσει ήδη');
        $msg_temp=str_replace('[1]', $pricelist_item_coupon, $msg_temp);
        
        $return = array('success' => false, 'message' => base64_encode($msg_temp));
        echo json_encode($return); die();           
      } else {
        $_gks_session['gks']['basket']['coupons'][$pricelist_item_coupon]=$pricelist_item_descr;
        $out[] =array('id' => '#input_coupon','type'=>'val', 'data' => base64_encode(''));
      }
      
      break;
    case 'coupondelete':
      //$return = array('success' => false, 'message' => base64_encode($mycoupon));
      //echo json_encode($return); die();
    
      if (isset($_gks_session['gks']['basket']['coupons'][$mycoupon])) {
        unset($_gks_session['gks']['basket']['coupons'][$mycoupon]);// = array();
      } else {
        debug_mail(false,'basket-edit.php try to remove coupon:'. $mycoupon);
      }
      
      break;
    default:
      debug_mail(false,'basket-edit.php error on cmd for coupon: '.$mycmd);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα Εντολής').' (5)<br>'.gks_lang('Ανανεώστε την σελίδα ή δοκιμάστε ξανά αργότερα')));
      echo json_encode($return); die();
  }
  
  $coupons_html='';
  foreach ($_gks_session['gks']['basket']['coupons'] as $key => $coupon) {
     $coupons_html.='<span class="tooltipster_basket" title="'.$coupon.'" style="text-align:left;border: 1px solid gray;border-radius: 4px;padding:8px;margin-right: 6px;">
     <span class="coupons">'.$key.' 
     <i class="coupon_delete gks_fas gks_fa-trash-alt gks_basket_delete_icon" data-coupon="'.$key.'" style="cursor:pointer;"></i>
     </span></span> ';
  } 
  if ($coupons_html!='') {
    $coupons_html=gks_lang('Τα κουπόνια σας').': '.$coupons_html;
  }
  $out[] =array('id' => '#coupons_html','type'=>'html', 'data' => base64_encode($coupons_html));  
  
}


if ($cmd_is_for_delivery_payment) { //cmd is for delivery_payment

  
  switch ($mycmd) {
    case 'delivery_payment':
      $_gks_session['gks']['basket']['tropos_apostolis'] = $mydelivery_id;
      $_gks_session['gks']['basket']['tropos_pliromis'] = $mypayment_id;
      
      //$_gks_session['gks']['basket']['kostos_apostolis'] = 110;
      //$_gks_session['gks']['basket']['kostos_pliromis']  = 110;
      //echo 'ffffffff';
      
  //$mydelivery_id=$myproduct_id;
  //$mypayment_id=$myobject;
        
      break;
    default:
    
      break;
  }
    

  //$return = array('success' => false, 'message' => base64_encode('error on cmd for delivery-payment<br>'.gks_lang('Ανανεώστε την σελίδα').$mydelivery_id.'-'.$mypayment_id));
  //echo json_encode($return); die();
        
}



$myproducts = gks_basket_recalc($_gks_session['gks']['basket'], array(), array());

$_gks_session['gks']['basket']['kostos_apostolis'] = gks_calculate_kostos_apostolis($_gks_session['gks']['basket'],-1);
$_gks_session['gks']['basket']['kostos_pliromis']  = gks_calculate_kostos_pliromis ($_gks_session['gks']['basket'],-1);
  
//$return = array('success' => false, 'message' => base64_encode($_gks_session['gks']['basket']['tropos_pliromis'].'--'.$_gks_session['gks']['basket']['kostos_pliromis']));
//echo json_encode($return); die();


foreach ($_gks_session['gks']['basket']['products'] as $index => &$product) {
  foreach ($product['objects'] as $keyo => &$object) {

    $row_id=$index.'_'.$product['product_id']['id_product'].'_'.$keyo;
    
    if ($object['type'] == 'normal') {
      $mycopies=$object['copies'];
    } else if ($object['type'] == 'multi') {
      $mycopies=$object['copies'];
      
      $subcount=0;
      foreach ($object['files'] as $file) {
        $subcount+=$file['copies'];  
      }
      $id='#subcount_'.$row_id;
      $out[]=array('id' => $id, 'type' => 'html', 'data' => base64_encode($subcount));
      
    } else if ($object['type'] == 'simple') {
      $mycopies=0;
      
      foreach ($object['files'] as $file) {
        $mycopies+=$file['copies'];  
      }
    }   
    
    
    if (isset($product['is_hotel_room_type'])) {
      $out[] =array('id' => '#rowposotita_'.$row_id,'type'=>'html', 'data' => base64_encode($mycopies));
    } else if ($object['type'] == 'normal' or $object['type'] == 'multi') { 
      $out[] =array('id' => '#input_'.$row_id,'type'=>'val', 'data' => base64_encode($mycopies));
    } else if ($object['type'] == 'simple') {
      $out[] =array('id' => '#rowposotita_'.$row_id,'type'=>'html', 'data' => base64_encode($mycopies));
      
    }    
   
    $html=''; $id='#td_price_id_product_'.$row_id;
    if (isset($product['product_id']['product_price_coupon_use']) and $product['product_id']['product_price_coupon_use']!='') {
      $coupons_html=' <span class="tooltipster_basket" title="'.$product['product_id']['product_pricelist_item_descr'].'" style="text-align:left">
      <span class="coupons">'.$product['product_id']['product_price_coupon_use'].'</span></span> ';
      $html.=$coupons_html;
    }    
        
    if (abs($product['product_id']['product_pricelist_item_percent']) >= 0.01) {
      $html.='<span style="font-weight: normal;text-decoration: line-through;color:#ff0000;padding-left: 10px;">'.myCurrencyFormat($product['product_id']['product_price_start_peritem_total'],true,true).'</span>';
    }
    $html.='<span style="font-weight: bold;color111:#000000;padding-left: 10px;">'.myCurrencyFormat($product['product_id']['product_price_final_peritem_total'],true,true).'</span>';
    $out[]=array('id' => $id, 'type' => 'html', 'data' => base64_encode($html));
    
    $htmlrowpricesum=myCurrencyFormat($product['product_id']['product_price_final_all_total'],true,true);
    $out[] =array('id' => '#rowpricesum_'.$row_id, 'type'=>'html', 'data' => base64_encode($htmlrowpricesum));
    
  }

  
}


//if ($_gks_session['gks']['basket']['tropos_apostolis']>0) {
//    $id='#price_delivery_way_'.$_gks_session['gks']['basket']['tropos_apostolis'];
//    $html=myCurrencyFormat($_gks_session['gks']['basket']['kostos_apostolis'],true,true);
//    $out[]=array('id' => $id, 'type' => 'html', 'data' => base64_encode($html));
//}

//foreach ($_gks_session['gks']['basket']['tropoi_pliromis_all'] as $item) {
//    $id='#price_payment_way_'.$item['id_payment_acquirer'];
//    $html=myCurrencyFormat($item['pa_calc_kostos'],true,true);
//    $out[]=array('id' => $id, 'type' => 'html', 'data' => base64_encode($html));
//} 
//if ($_gks_session['gks']['basket']['tropos_pliromis']>0) {
//    $id='#price_payment_way_'.$_gks_session['gks']['basket']['tropos_pliromis'];
//    $html=myCurrencyFormat($_gks_session['gks']['basket']['kostos_pliromis'],true,true);
//    $out[]=array('id' => $id, 'type' => 'html', 'data' => base64_encode($html));
//}

$allwarnings = gks_basket_warnings($_gks_session['gks']['basket']);



$pliroteo=$_gks_session['gks']['basket']['products_total'] + $_gks_session['gks']['basket']['kostos_apostolis'] + $_gks_session['gks']['basket']['kostos_pliromis'];
$ids_hide=array();
$ids_show=array();

if ($_gks_session['gks']['basket']['products_fpa']==0) $ids_hide[]='#tr_basket_products_fpa'; else $ids_show[]= '#tr_basket_products_fpa';
if ($_gks_session['gks']['basket']['kostos_apostolis']==0) $ids_hide[]='#tr_basket_kostos_apostolis'; else $ids_show[]= '#tr_basket_kostos_apostolis';
if ($_gks_session['gks']['basket']['kostos_pliromis']==0) $ids_hide[]='#tr_basket_kostos_pliromis'; else $ids_show[]= '#tr_basket_kostos_pliromis';
if ($_gks_session['gks']['basket']['products_netvalue']==$pliroteo) $ids_hide[]='#tr_basket_products_netvalue'; else $ids_show[]= '#tr_basket_products_netvalue';

if (0==$_gks_session['gks']['basket']['products_varos'] && 0==$_gks_session['gks']['basket']['products_ogos']) $ids_hide[]='#table_products_varos_ogos'; else $ids_show[]= '#table_products_varos_ogos';

//if ($_gks_session['gks']['basket']['products_need_apostoli'] && $_gks_session['gks']['basket']['tropos_apostolis']>0) {
//  if (isset($_gks_session['gks']['basket']['tropoi_apostolis_all']) and isset($_gks_session['gks']['basket']['tropoi_apostolis_all'][ $_gks_session['gks']['basket']['tropos_apostolis'] ])) {
//    if ($_gks_session['gks']['basket']['tropoi_apostolis_all'][ $_gks_session['gks']['basket']['tropos_apostolis'] ]['delivery_method_type']=='store') {
//      $ids_hide[]= '#gks_rsrv_rc_send_from';
//    }
//  }
//}

if ($_gks_session['gks']['basket']['products_need_apostoli']==false) {
  $ids_hide[]= '#gks_rsrv_rc_send_from'; 
} else {
  if ($_gks_session['gks']['basket']['tropos_apostolis']>0 and 
   isset($_gks_session['gks']['basket']['tropoi_apostolis_all']) and 
   isset($_gks_session['gks']['basket']['tropoi_apostolis_all'][ $_gks_session['gks']['basket']['tropos_apostolis'] ]) and
   $_gks_session['gks']['basket']['tropoi_apostolis_all'][ $_gks_session['gks']['basket']['tropos_apostolis'] ]['delivery_method_type']=='store') {
    $ids_hide[]= '#gks_rsrv_rc_send_from'; 
  } else {
    $ids_show[]= '#gks_rsrv_rc_send_from'; 
  }
} 


//$ids_hide[]= '#gks_rsrv_rc_send_from';

gks_erp_cookie_save($gks_erp_cookie_id);


$return = array('success' => true, 'message' => base64_encode('ok'),
  'products_posotita' => base64_encode(myNumberFormat($_gks_session['gks']['basket']['products_posotita'],0)),
  'products_posotita_val'    => $_gks_session['gks']['basket']['products_posotita'],
  'products_netvalue' => base64_encode(myCurrencyFormat($_gks_session['gks']['basket']['products_netvalue'],true,true)),
  'products_fpa'      => base64_encode(myCurrencyFormat($_gks_session['gks']['basket']['products_fpa'],true,true)),
  'kostos_apostolis'  => base64_encode(myCurrencyFormat($_gks_session['gks']['basket']['kostos_apostolis'],true,true)),
  'kostos_pliromis'   => base64_encode(myCurrencyFormat($_gks_session['gks']['basket']['kostos_pliromis'],true,true)),
  'products_total'    => base64_encode(myCurrencyFormat($pliroteo ,true,true)),
  'products_total_val'    => $pliroteo,
  'out' => $out,
  'also_delete' => $also_delete,
  'allwarnings' => base64_encode(json_encode($allwarnings)),
  'rsrv_count' => $rsrv_count,
  'ids_hide' => $ids_hide,
  'ids_show' => $ids_show,
  'tropoi_apostolis_all' => $_gks_session['gks']['basket']['tropoi_apostolis_all'],
  'tropoi_pliromis_all' => $_gks_session['gks']['basket']['tropoi_pliromis_all'],
  
);

//echo json_encode($return); die();

//$return = array('success' => true, 'message' => base64_encode('OK'));


return $return;
  
}

