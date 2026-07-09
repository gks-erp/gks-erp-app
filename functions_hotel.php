<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
function GKS_HOTEL_CHILD_AGE_PRICE_SORT($a, $b) {
  if ($b['from'] > $a['from']) return -1;
  if ($b['from'] < $a['from']) return 1;
  return 0;
}
function GKS_HOTEL_EXTRA_BEDS_SORT($a, $b) {
  if ($b['from'] > $a['from']) return -1;
  if ($b['from'] < $a['from']) return 1;
  return 0;
}
function roomaf_html_chlds_array_sort($a, $b) {
  if ($b['age'] > $a['age']) return -1;
  if ($b['age'] < $a['age']) return 1;
  return 0;
}

function get_def_check($id_hotel) {
  
  
  //echo '<pre>get_def_check '.$id_hotel;die();

  $hotel_params=gks_hotel_get_params($id_hotel);
  
  //echo '<pre>get_def_check '.$id_hotel;die();
  
  $ret=array(
    'inh' => 14, //in hour
    'inm' => 0,  //in minute
    'outh' => 12,//out hour 
    'outm' => 0, //out minute
    'max_reservation_date_time' => 0,
  );
  $var=explode(':', $hotel_params['hotel_default_checkin']);
  if (count($var)== 2) {
    $ret['inh']=intval($var[0]);
    $ret['inm']=intval($var[1]);
  }
  $var=explode(':', $hotel_params['hotel_default_checkout']);
  if (count($var)== 2) {
    $ret['outh']=intval($var[0]);
    $ret['outm']=intval($var[1]);
  }
    
  
  if ($hotel_params['hotel_date_close']!='') {
    $ret['max_reservation_date_time'] = strtotime($hotel_params['hotel_date_close']);
  }
  
  if ($hotel_params['hotel_reservation_days_future'] > 0) {
    $gks_hotel_date_close_time2=strtotime(date('Y-m-d')) + $hotel_params['hotel_reservation_days_future']*24*60*60;
    if ($ret['max_reservation_date_time'] == 0) {
      $ret['max_reservation_date_time'] = $gks_hotel_date_close_time2;   
    } else {
      if ($gks_hotel_date_close_time2 < $ret['max_reservation_date_time']) {
        $ret['max_reservation_date_time'] = $gks_hotel_date_close_time2;
      }
    }
  }
  if ($ret['max_reservation_date_time']>0 and $ret['max_reservation_date_time'] < strtotime(date('Y-m-d'))) {
    $ret['max_reservation_date_time']=0;
  }
  
  return $ret;
}
function getHotelRoomTypeStatusDescr($mystate) {
  switch ($mystate) {
    case 'disable': return gks_lang('Ανενεργό','part4','hotelroomtypestatusdescr'); break; 
    case 'available': return gks_lang('Διαθέσιμο','part4','hotelroomtypestatusdescr'); break; 
    case 'renovation': return gks_lang('Ανακαίνιση','part4','hotelroomtypestatusdescr'); break; 
    default: return $mystate; break; 
  } 
}
function getHotelCustomTypeDescr($mystate) {
  switch ($mystate) {
    case 'default': return gks_lang('Προεπιλογή ξενοδοχείου','part4','hotelcustomtypedescr'); break; 
    case 'roomtype': return gks_lang('Προεπιλογή τύπου δωματίου','part4','hotelcustomtypedescr'); break; 
    case 'custom': return gks_lang('Ορισμός','part4','hotelcustomtypedescr'); break; 
    default: return $mystate; break; 
  }
}
function getHotelReservationStatusDescr($mystate) {
  global $gks_user_settings;
  $load_lang='el-GR';
  if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);

  if ($load_lang=='en-US') {
    switch ($mystate) {
      case '005prodraft': return 'At Cart'; break; 
      case '010draft': return 'Draft'; break; 
      case '040cancelled': return 'Cancelled'; break; 
      case '050rejected': return 'Rejected'; break; 
      case '070wait_payment': return 'Wait Payment'; break; 
      case '080confirm': return 'Confirm'; break; 
      case '100completed': return 'Completed'; break;
      case '110payment': return 'Payment'; break;
      default: return $mystate; break; 
    }
  } else {
    switch ($mystate) {
      case '005prodraft': return gks_lang('Σε καλάθι','part4','hotelreservationstatusdescr'); break; 
      case '010draft': return gks_lang('Πρόχειρη','part4','hotelreservationstatusdescr'); break; 
      case '040cancelled': return gks_lang('Ακυρωμένη','part4','hotelreservationstatusdescr'); break; 
      case '050rejected': return gks_lang('Απορρίφθηκε','hotelreservationstatusdescr'); break; 
      case '070wait_payment': return gks_lang('Αναμονή Πληρωμής','part4','hotelreservationstatusdescr'); break; 
      case '080confirm': return gks_lang('Επιβεβαιωμένη','part4','hotelreservationstatusdescr'); break; 
      case '100completed': return gks_lang('Ολοκληρωμένη','part4','hotelreservationstatusdescr'); break;
      case '110payment': return gks_lang('Εξοφλημένη','part4','hotelreservationstatusdescr'); break;
       
      default: return $mystate; break; 
    }
  }
}
function getHotelReservationStatusDescr_en_US($mystate) {
  switch ($mystate) {
    case '005prodraft': return 'At Cart'; break; 
    case '010draft': return 'Draft'; break; 
    case '040cancelled': return 'Cancelled'; break; 
    case '050rejected': return 'Rejected'; break; 
    case '070wait_payment': return 'Wait payment'; break; 
    case '080confirm': return 'Confirm'; break; 
    case '100completed': return 'Completed'; break;
    case '110payment': return 'Payment'; break;
     
    default: return $mystate; break; 
  }
}

function getHotelFolioStatusDescr($mystate) {
  switch ($mystate) {
    case '01draft': return gks_lang('Πρόχειρη','part4','hotelfoliostatusdescr'); break; 
    case '10cancel': return gks_lang('Ακυρωμένη','part4','hotelfoliostatusdescr'); break; 
    case '20open': return gks_lang('Ανοιχτή','part4','hotelfoliostatusdescr'); break; 
    case '30complete': return gks_lang('Ολοκληρωμένη','part4','hotelfoliostatusdescr'); break; 
    default: return $mystate; break; 
  }
}
function getHotelAvailabilityDescr($mystate) {
  switch ($mystate) {
    case 1: return gks_lang('Ανοιχτό','part4','hotelavailabilitydescr'); break; 
    case 0: return gks_lang('Κλειστό','part4','hotelavailabilitydescr'); break; 
    default: return $mystate; break; 
  }
}
function calc_availability_day($hotel_room_type_id, $hotel_room_id,$availability_from, $availability_to) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_HOTEL_DAYS_FUTURE;
  $max_date_time=time() + $GKS_HOTEL_DAYS_FUTURE*24*60*60;
  
  if ($availability_to ==  '' )$availability_to = date('Y-m-d', $max_date_time);
  
  $availability_from_time=strtotime($availability_from);
  $availability_to_time=strtotime($availability_to);
  
  
  //debug_mail(false,'calc_availability_day',$hotel_room_type_id.'--'. $hotel_room_id.'--'.$availability_from.'--'.$availability_to);
  
  
  if ($hotel_room_id>0) {
    $hotel_id=0;
    $sql="select hotel_id from gks_hotel_room where id_hotel_room=".$hotel_room_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $hotel_id=$row['hotel_id'];
    }    
    
    
    $sql="select * from gks_hotel_availability 
    where hotel_room_id=".$hotel_room_id." and ";
    if ($availability_to == '') {
      $sql.="(availability_to>='".$availability_from."' or availability_to is null)";
    } else {
      $sql.="(
        (availability_to >='".$availability_from."' and availability_to <= '".$availability_to."') or
        (availability_from >='".$availability_from."' and availability_from <= '".$availability_to."') or 
        (availability_from <='".$availability_from."' and availability_to >= '".$availability_to."') or 
        (availability_from <='".$availability_to."' and gks_hotel_availability.availability_to is null)
      )";
    }
    $sql.=" order by id_hotel_availability desc";

    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $row_edit=array();
    while ($row = $result->fetch_assoc()) {
      $row['availability_from_time']=strtotime($row['availability_from']);
      if (isset($row['availability_to'])) {
        $row['availability_to_time']=strtotime($row['availability_to']);
      } else {
        $row['availability_to_time']=$max_date_time;
      }
      $row_edit[]= $row;
      
    }    
    
    for ($it=$availability_from_time; $it<=$availability_to_time; $it+=24*60*60) {
      $v=-2; //not found
      $id_hotel_availability=0;
      foreach ($row_edit as $row) {
        $is_day=false;
        $w=date('w',$it);
        switch ($w) {   
          case 0: $is_day = $row['avail_weekday_ky']!=0; break;  //Kiriaki
          case 1: $is_day = $row['avail_weekday_de']!=0; break;  //Deytera
          case 2: $is_day = $row['avail_weekday_tr']!=0; break;  //Triti
          case 3: $is_day = $row['avail_weekday_te']!=0; break;  //Tetarti
          case 4: $is_day = $row['avail_weekday_pe']!=0; break;  //Pempti
          case 5: $is_day = $row['avail_weekday_pa']!=0; break;  //Paraskevi
          case 6: $is_day = $row['avail_weekday_sa']!=0; break;  //Sabbato
        }
        if ($is_day) {
          if ($it >= $row['availability_from_time'] and $it <= $row['availability_to_time']) {
            $v=0;
            if ($row['availability_status']!=0) $v = 1;
            $id_hotel_availability=$row['id_hotel_availability'];
            break;
          }
        }
      }
      //$return = array('success' => false, 'message' => base64_encode(date('Y-m-d',$it).' '.$v));
      //echo json_encode($return); die();
            
      if ($v == -2) {
        $sql="delete from gks_hotel_availability_day where hotel_room_id=".$hotel_room_id." and availability_day='".date('Y-m-d',$it)."'";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
         
      } else {
        
        $sql="select id_hotel_availability_day from gks_hotel_availability_day where hotel_room_type_id=0 and hotel_room_id=".$hotel_room_id." and availability_day='".date('Y-m-d',$it)."'";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        if ($result->num_rows>=1) {
          $row = $result->fetch_assoc();
          $id_hotel_availability_day=$row ['id_hotel_availability_day'];
          $sql="update gks_hotel_availability_day set 
          hotel_id=".$hotel_id.",
          availability_status=".$v.",
          hotel_availability_id=".$id_hotel_availability.",
          user_id_edit=".$my_wp_user_id.",
          mydate_edit=now(),
          myip='".$db_link->escape_string($gkIP)."'
          where id_hotel_availability_day=".$id_hotel_availability_day;
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }
          
          
        } else {
        
          $sql="insert INTO gks_hotel_availability_day (
          hotel_id,hotel_room_type_id, hotel_room_id, availability_day, availability_status,
          hotel_availability_id,
          user_id_edit,mydate_edit,myip) 
          VALUES (
          ".$hotel_id.",0,".$hotel_room_id.",'".date('Y-m-d',$it)."',".$v.",
          ".$id_hotel_availability.",
          ".$my_wp_user_id.",now(),'".$db_link->escape_string($gkIP)."')";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }
        }
        

        
      } 
    }
  }
  
  
  $mydata_type_room=array();
  if ($hotel_room_type_id>0) {
    $hotel_id=0;
    $sql="select hotel_id from gks_hotel_room_type where id_hotel_room_type=".$hotel_room_type_id;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $hotel_id=$row['hotel_id'];
    }    
    
    $sql="select * from gks_hotel_availability 
    where hotel_room_type_id=".$hotel_room_type_id." and ";
    if ($availability_to == '') {
      $sql.="(availability_to>='".$availability_from."' or availability_to is null)";
    } else {
      $sql.="(
        (availability_to >='".$availability_from."' and availability_to <= '".$availability_to."') or
        (availability_from >='".$availability_from."' and availability_from <= '".$availability_to."') or 
        (availability_from <='".$availability_from."' and availability_to >= '".$availability_to."') or 
        (availability_from <='".$availability_to."' and gks_hotel_availability.availability_to is null)
      )";
    }
    $sql.=" order by id_hotel_availability desc";
    
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }
    
    $row_edit=array();
    while ($row = $result->fetch_assoc()) {
      $row['availability_from_time']=strtotime($row['availability_from']);
      if (isset($row['availability_to'])) {
        $row['availability_to_time']=strtotime($row['availability_to']);
      } else {
        $row['availability_to_time']=$max_date_time;
      }
      $row_edit[]= $row;
    }    
    
    for ($it=$availability_from_time; $it<=$availability_to_time; $it+=24*60*60) {
      $v=-2; //not found
      foreach ($row_edit as $row) {
        $is_day=false;
        $w=date('w',$it);
        switch ($w) {   
          case 0: $is_day = $row['avail_weekday_ky']!=0; break;  //Kiriaki   
          case 1: $is_day = $row['avail_weekday_de']!=0; break;  //Deytera   
          case 2: $is_day = $row['avail_weekday_tr']!=0; break;  //Triti     
          case 3: $is_day = $row['avail_weekday_te']!=0; break;  //Tetarti   
          case 4: $is_day = $row['avail_weekday_pe']!=0; break;  //Pempti    
          case 5: $is_day = $row['avail_weekday_pa']!=0; break;  //Paraskevi 
          case 6: $is_day = $row['avail_weekday_sa']!=0; break;  //Sabbato   
        }
        if ($is_day) {
          if ($it >= $row['availability_from_time'] and $it <= $row['availability_to_time']) {
            $v=0;
            if ($row['availability_status']!=0) $v = 1;
            $id_hotel_availability=$row['id_hotel_availability'];
            break;
          }          
        }
      }

          
      if ($v == -2) {
        $sql="delete from gks_hotel_availability_day where hotel_room_type_id=".$hotel_room_type_id." and availability_day='".date('Y-m-d',$it)."'";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
         
      } else {
        $sql="select id_hotel_availability_day from gks_hotel_availability_day where hotel_room_type_id=".$hotel_room_type_id." and hotel_room_id=0 and availability_day='".date('Y-m-d',$it)."'";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        if ($result->num_rows>=1) {
          $row = $result->fetch_assoc();
          $id_hotel_availability_day=$row ['id_hotel_availability_day'];
          $sql="update gks_hotel_availability_day set 
          hotel_id=".$hotel_id.",
          availability_status=".$v.",
          hotel_availability_id=".$id_hotel_availability.",
          user_id_edit=".$my_wp_user_id.",
          mydate_edit=now(),
          myip='".$db_link->escape_string($gkIP)."'
          where id_hotel_availability_day=".$id_hotel_availability_day;
          $result = $db_link->query($sql);
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }
          
          
        } else {
        
          $sql="insert INTO gks_hotel_availability_day (
          hotel_id,hotel_room_type_id, hotel_room_id, availability_day, availability_status,
          hotel_availability_id,
          user_id_edit,mydate_edit,myip) 
          VALUES (
          ".$hotel_id.",".$hotel_room_type_id.",0,'".date('Y-m-d',$it)."',".$v.",
          ".$id_hotel_availability.",
          ".$my_wp_user_id.",now(),'".$db_link->escape_string($gkIP)."')";
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }
        }
      }
    }
  }  
  

  
  
  
  //file_put_contents('/var/www/php/my-rooms.gks.gr/tmp/row_edit.txt', $out."\n".print_r($mydata_room, true)."\n".print_r($mydata_type_room, true));
  
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.$mhycase."\n1:".$sql1."\n2:".$sql2."\n3:".$sql3));
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.count($row_edit)));
  //echo json_encode($return); die();
  
}

function calc_price_day($hotel_room_type_id, $price_from, $price_to) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_HOTEL_DAYS_FUTURE;
  $max_date_time=time() + $GKS_HOTEL_DAYS_FUTURE*24*60*60;
  
  if ($hotel_room_type_id<=0) return;
  
  if ($price_to ==  '' )$price_to = date('Y-m-d', $max_date_time);
  
  $price_from_time=strtotime($price_from);
  $price_to_time=strtotime($price_to);
  
  
  //debug_mail(false,'calc_price_day',$hotel_room_type_id.'--'.$price_from.'--'.$price_to);
  
  

  
  
  $mydata_type_room=array();
  
  $hotel_id=0;
  $sql="select hotel_id from gks_hotel_room_type where id_hotel_room_type=".$hotel_room_type_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows>=1) {
    $row = $result->fetch_assoc();
    $hotel_id=$row['hotel_id'];
  }  
      
  $sql="select * from gks_hotel_price 
  where hotel_room_type_id=".$hotel_room_type_id." and ";
  if ($price_to == '') {
    $sql.="(price_to>='".$price_from."' or price_to is null)";
  } else {
    $sql.="(
      (price_to >='".$price_from."' and price_to <= '".$price_to."') or
      (price_from >='".$price_from."' and price_from <= '".$price_to."') or 
      (price_from <='".$price_from."' and price_to >= '".$price_to."') or 
      (price_from <='".$price_to."' and gks_hotel_price.price_to is null)
    )";
  }
  $sql.=" order by id_hotel_price desc";
  
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  
  $row_edit=array();
  while ($row = $result->fetch_assoc()) {
    $row['price_from_time']=strtotime($row['price_from']);
    if (isset($row['price_to'])) {
      $row['price_to_time']=strtotime($row['price_to']);
    } else {
      $row['price_to_time']=$max_date_time;
    }
    $row_edit[]= $row;
  }    
  
  for ($it=$price_from_time; $it<=$price_to_time; $it+=24*60*60) {
    $v=-2; //not found
    $price=0;
    foreach ($row_edit as $row) {
      $is_day=false;
      $w=date('w',$it);
      switch ($w) {   
        case 0: $is_day = $row['price_weekday_ky']!=0; break;  //Kiriaki   
        case 1: $is_day = $row['price_weekday_de']!=0; break;  //Deytera   
        case 2: $is_day = $row['price_weekday_tr']!=0; break;  //Triti     
        case 3: $is_day = $row['price_weekday_te']!=0; break;  //Tetarti   
        case 4: $is_day = $row['price_weekday_pe']!=0; break;  //Pempti    
        case 5: $is_day = $row['price_weekday_pa']!=0; break;  //Paraskevi 
        case 6: $is_day = $row['price_weekday_sa']!=0; break;  //Sabbato   
      }
      if ($is_day) {
        if ($it >= $row['price_from_time'] and $it <= $row['price_to_time']) {
          $v = 1;
          $price=$row['price'];
          $id_hotel_price=$row['id_hotel_price'];
          break;
        }          
      }
    }

        
    if ($v == -2) {
      $sql="delete from gks_hotel_price_day where hotel_room_type_id=".$hotel_room_type_id." and price_day='".date('Y-m-d',$it)."'";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
       
    } else {
      $sql="select id_hotel_price_day from gks_hotel_price_day where hotel_room_type_id=".$hotel_room_type_id." and price_day='".date('Y-m-d',$it)."'";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $id_hotel_price_day=$row ['id_hotel_price_day'];
        $sql="update gks_hotel_price_day set 
        hotel_id=".$hotel_id.",
        price=".number_format($price, 8, '.', '').",
        hotel_price_id=".$id_hotel_price.",
        user_id_edit=".$my_wp_user_id.",
        mydate_edit=now(),
        myip='".$db_link->escape_string($gkIP)."'
        where id_hotel_price_day=".$id_hotel_price_day;
        $result = $db_link->query($sql);
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        
        
      } else {
      
        $sql="insert INTO gks_hotel_price_day (
        hotel_id,hotel_room_type_id, price_day, price,
        hotel_price_id,
        user_id_edit,mydate_edit,myip) 
        VALUES (
        ".$hotel_id.",".$hotel_room_type_id.",'".date('Y-m-d',$it)."',".number_format($price, 8, '.', '').",
        ".$id_hotel_price.",
        ".$my_wp_user_id.",now(),'".$db_link->escape_string($gkIP)."')";
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
      }
    }
  }
  
  

  
  
  
  //file_put_contents('/var/www/php/my-rooms.gks.gr/tmp/row_edit.txt', $out."\n".print_r($mydata_room, true)."\n".print_r($mydata_type_room, true));
  
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.$mhycase."\n1:".$sql1."\n2:".$sql2."\n3:".$sql3));
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.count($row_edit)));
  //echo json_encode($return); die();
  
}

function gks_hotel_get_params($id_hotel) {
  global $db_link;
  $hotel_params=array('success' => false,'message' => base64_encode('generic error hotel get data'));
  
  $sql="select gks_hotel.*,
  hotel_template_eidos_descr_en_US,hotel_template_efd_descr_en_US,hotel_template_woo_descr_en_US
  from gks_hotel
  LEFT JOIN (
    SELECT hotel_id, 
    hotel_template_eidos_descr as hotel_template_eidos_descr_en_US,
    hotel_template_efd_descr as hotel_template_efd_descr_en_US,
    hotel_template_woo_descr as hotel_template_woo_descr_en_US
    FROM gks_hotel_lang WHERE lang_code='en-US'
  ) AS gks_hotel_en_US ON gks_hotel.id_hotel = gks_hotel_en_US.hotel_id  
  
  where id_hotel=".$id_hotel;
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$outdata['message'] = 'error sql';return $hotel_params;}
  if ($result->num_rows == 0) {
    debug_mail(false,'error sql',$sql);$outdata['message'] = gks_lang('Δεν βρέθηκε το ξενοδοχείο');return $hotel_params;
  }
  $row_hotel = $result->fetch_assoc();
  $hotel_params['hotel_default_availability']=(intval($row_hotel['hotel_default_availability'])==0 ? false : true);
  $hotel_params['hotel_date_open']=trim_gks($row_hotel['hotel_date_open']);
  $hotel_params['hotel_date_close']=trim_gks($row_hotel['hotel_date_close']);
  $hotel_params['hotel_default_price']=floatval($row_hotel['hotel_default_price']);
  $hotel_params['hotel_default_checkin']=trim_gks($row_hotel['hotel_default_checkin']);
  $hotel_params['hotel_default_checkout']=trim_gks($row_hotel['hotel_default_checkout']);
  $hotel_params['hotel_reservation_days_future']=intval($row_hotel['hotel_reservation_days_future']);
  $hotel_params['hotel_reservation_min_days_online']=intval($row_hotel['hotel_reservation_min_days_online']);
  $hotel_params['hotel_reservation_max_days_online']=intval($row_hotel['hotel_reservation_max_days_online']);
  $hotel_params['hotel_reservation_can_select_room']=(intval($row_hotel['hotel_reservation_can_select_room'])==0 ? false : true);
  $hotel_params['hotel_efd_product_id']=intval($row_hotel['hotel_efd_product_id']);
  $hotel_params['hotel_child_accept']=(intval($row_hotel['hotel_child_accept'])==0 ? false : true);
  $hotel_params['hotel_child_accept_above_age']=intval($row_hotel['hotel_child_accept_above_age']);
  $hotel_params['hotel_child_age_price'] = json_decode(trim_gks($row_hotel['hotel_child_age_price']), true);
  $hotel_params['hotel_child_kounies'] = json_decode(trim_gks($row_hotel['hotel_child_kounies']), true);
  $hotel_params['hotel_extra_beds'] = json_decode(trim_gks($row_hotel['hotel_extra_beds']), true);
  $hotel_params['hotel_template_eidos_descr'] = trim_gks($row_hotel['hotel_template_eidos_descr']);
  $hotel_params['hotel_template_eidos_descr_en_US'] = trim_gks($row_hotel['hotel_template_eidos_descr_en_US']);
  $hotel_params['hotel_template_efd_descr'] = trim_gks($row_hotel['hotel_template_efd_descr']);
  $hotel_params['hotel_template_efd_descr_en_US'] = trim_gks($row_hotel['hotel_template_efd_descr_en_US']);
  $hotel_params['hotel_template_woo_descr'] = trim_gks($row_hotel['hotel_template_woo_descr']);
  $hotel_params['hotel_template_woo_descr_en_US'] = trim_gks($row_hotel['hotel_template_woo_descr_en_US']);
  
  $hotel_params['hotel_use_checkout_system'] = trim_gks($row_hotel['hotel_use_checkout_system']);

  
  


  
  
  //usort($hotel_params['hotel_child_age_price'], "GKS_HOTEL_CHILD_AGE_PRICE_SORT");
  //usort($hotel_params['hotel_extra_beds']['beds'], "GKS_HOTEL_EXTRA_BEDS_SORT");


  $hotel_params['hotel_child_accept_max_age']=17;
  if ($hotel_params['hotel_child_accept']) {
    $temp=0;
    if (isset($hotel_params['hotel_child_age_price'])) {
      foreach ($hotel_params['hotel_child_age_price'] as $value) {
        if ($temp < $value['to']) {
          $temp=$value['to'];
        }
      }
    }
    $hotel_params['hotel_child_accept_max_age']=$temp;
  }  
  
  //$hotel_params['hotel_child_free_as_adults']=true;
  $hotel_params['hotel_child_free_as_adults']=false;
  
  
  
  return $hotel_params;
}


function get_availability_rooms($get_availability_rooms_imput) {
  //$get_availability_rooms_imput
  $id_hotel = $get_availability_rooms_imput['id_hotel'];
  $date_from = $get_availability_rooms_imput['date_from'];
  $date_to = $get_availability_rooms_imput['date_to'];
  $alldata = $get_availability_rooms_imput['alldata'];
  $id_hotel_room = $get_availability_rooms_imput['id_hotel_room'];
  $id_hotel_room_type = $get_availability_rooms_imput['id_hotel_room_type'];
  $not_id_hotel_reservation = $get_availability_rooms_imput['not_id_hotel_reservation'];
  $not_id_hotel_folio = $get_availability_rooms_imput['not_id_hotel_folio'];
  $not_id_hotel_room = $get_availability_rooms_imput['not_id_hotel_room'];
  $rnum_adults = $get_availability_rooms_imput['rnum_adults'];
  $rnum_childs = $get_availability_rooms_imput['rnum_childs'];
  $rchilds_ages_list = $get_availability_rooms_imput['rchilds_ages_list'];
  $rnum_child_kounies = $get_availability_rooms_imput['rnum_child_kounies'];
  $rnum_extra_beds = $get_availability_rooms_imput['rnum_extra_beds'];
  $come_from='backend';if (isset($get_availability_rooms_imput['come_from'])) $come_from=$get_availability_rooms_imput['come_from'];
  
//  print '<pre>';
//  print_r($get_availability_rooms_imput);
//  die();
  
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $dev_page_starttime;

  

  
  
  $outdata = array(
    'error_msg' => '',
    'days' => 0,
    'date_from' => $date_from,
    'date_to' => $date_from,
    'child_prices' => array(),
    'alldata' => $alldata,
    'avl_rooms_state_settings' => 0,
    'avl_rooms_state_reservation' => 0,
    'avl_rooms_state_folio' => 0,
    'rooms' => array(),
    'rooms_types' => array(),
  );
  
  $defs = get_def_check($id_hotel);
  $hotel_params=gks_hotel_get_params($id_hotel);

  //print '<pre>';print_r($hotel_params);die();
  
  

  
  
  if ($id_hotel_room>0) {
    $sql="select hotel_room_type_id from gks_hotel_room where hotel_room_type_id>0 and id_hotel_room=".$id_hotel_room;  
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$outdata['error_msg'] = gks_lang('sql error');return $outdata;}
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $id_hotel_room_type = $row['hotel_room_type_id'];
    }
  }
  
    
  if ($date_from == '' or $date_to == '') {
    $outdata['error_msg'] = gks_lang('Εισάγετε κάποιες ημερομηνίες');
    debug_mail(false,'empty dates','<pre>'.print_r($outdata,true));
    return $outdata;} 
  
  $v1=explode('-',$date_from);
  $date_from='';
  $date_from_time=0;
  if (count($v1)!=3) {
    $outdata['error_msg'] = gks_lang('Η ημερομηνία άφιξης δεν έχει την σωστή μορφή');
    debug_mail(false,'date_from wrong format','<pre>'.print_r($outdata,true));
    return $outdata;}
    
  $date_from_time=strtotime($v1[0].'-'.$v1[1].'-'.$v1[2]);
  $date_from=date('Y-m-d',$date_from_time);
  $outdata['date_from'] = $date_from;
  
  $v1=explode('-',$date_to);
  $date_to='';
  $date_to_time=0;
  if (count($v1)!=3) {
    $outdata['error_msg'] = gks_lang('Η ημερομηνία αναχώρησης δεν έχει την σωστή μορφή');
    debug_mail(false,'date_to wrong format','<pre>'.print_r($outdata,true));
    return $outdata;}
    
  $date_to_time=strtotime($v1[0].'-'.$v1[1].'-'.$v1[2]);
  $date_to=date('Y-m-d',$date_to_time);
  $outdata['date_to'] = $date_to;
  
  if ($date_from_time > $date_to_time or $date_from_time==0 or $date_to_time==0) {
    $outdata['error_msg'] = gks_lang('Λανθασμένο εύρος ημερομηνιών');
    debug_mail(false,'error date range','<pre>'.print_r($outdata,true));
    return $outdata;} 
  
  
  if ($alldata == false and $hotel_params['hotel_default_availability']==false) {
    $outdata['error_msg'] = gks_lang('Το ξενοδοχείο είναι κλειστό');
    //debug_mail(false,gks_lang('Το ξενοδοχείο είναι κλειστό'),'<pre>'.print_r($outdata,true));
    return $outdata;}
  

  $gks_hotel_date_open_time=0;
  $gks_hotel_date_close_time=0;
  if ($hotel_params['hotel_date_open']!='')  $gks_hotel_date_open_time  = strtotime($hotel_params['hotel_date_open']);
  if ($hotel_params['hotel_date_close']!='') $gks_hotel_date_close_time = strtotime($hotel_params['hotel_date_close']);

  //$outdata['error_msg'] = gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια').$come_from;return $outdata;
  
  if ($come_from=='online' and $defs['max_reservation_date_time']>0 and $date_from_time>$defs['max_reservation_date_time']) {
    //$outdata['error_msg'] = gks_lang('Το ξενοδοχείο είναι κλειστό').'1 |'.date('Y-m-d H:i:s', $defs['max_reservation_date_time']).'|'.date('Y-m-d H:i:s', $date_from_time).'|';  
    $outdata['error_msg'] = gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια');
    return $outdata;
  }

  

//  print '<pre>'; print_r($outdata); die();
    

  $price_def = myCurrencyFormat($hotel_params['hotel_default_price']);
  
  
  $days=array();
  for ($i=$date_from_time; $i<=$date_to_time; $i+=24*60*60) {
    $dateval=date('Y-m-d', $i);
    $days[$dateval] = array( 
      'froma'=> gks_lang('Διαθέσιμο').', ', 
      'val1' => 1,
      'val2' => 1,
      'fromp'=> gks_lang('Προεπιλεγμένη τιμή ξενοδοχείου'), 
      'price' => $hotel_params['hotel_default_price'], 
      'price_format'=> $price_def,
      'price_room' => $hotel_params['hotel_default_price'], 
      'price_room_format'=> $price_def,
      'price_child' => 0, 
      'price_child_format'=> '',
      'descra' => '',
      'descrp' => '',
    ); 
    if ($hotel_params['hotel_default_availability']==false) {$days[$dateval]['val1']=0;$days[$dateval]['froma']=gks_lang('Το ξενοδοχείο είναι κλειστό').', ';}
  
    if ($days[$dateval]['val1'] == 1) {
      if ($gks_hotel_date_open_time > 0  and $i < $gks_hotel_date_open_time)  {$days[$dateval]['val1'] = 0; $days[$dateval]['froma']=gks_lang('Ημερομηνίες λειτουργίας του ξενοδοχείου').', ';}
      if ($gks_hotel_date_close_time > 0 and $i > $gks_hotel_date_close_time) {$days[$dateval]['val1'] = 0; $days[$dateval]['froma']=gks_lang('Ημερομηνίες λειτουργίας του ξενοδοχείου').', ';}
    }
  }
  $outdata['days'] = count($days);
  
  if ($come_from=='online') {
    if ($hotel_params['hotel_reservation_min_days_online']>0) {
      if ($outdata['days'] < $hotel_params['hotel_reservation_min_days_online']) {
        $outdata['error_msg'] = str_replace('%s1',$hotel_params['hotel_reservation_min_days_online'] ,gks_lang('Η κράτηση θα πρέπει να έχει διάρκεια τουλάχιστον %s1 διανυκτερεύσεις'));
        return $outdata;
      }
    }
    if ($hotel_params['hotel_reservation_max_days_online']>0) {
      if ($outdata['days'] > $hotel_params['hotel_reservation_max_days_online']) {
        //$outdata['error_msg'] = str_replace('%s1',$hotel_params['hotel_reservation_max_days_online'].' '.$outdata['days'].' '.print_r($days, true) ,gks_lang('Η κράτηση θα πρέπει να έχει μέγιστη διάρκεια %s1 διανυκτερεύσεις'));
        $outdata['error_msg'] = str_replace('%s1',$hotel_params['hotel_reservation_max_days_online'] ,gks_lang('Η κράτηση θα πρέπει να έχει μέγιστη διάρκεια %s1 διανυκτερεύσεις'));
        return $outdata;
      }
    }
    
  }
  
  
  
  
  $id_hotel_room_array = array();
  $id_hotel_room_type_array=array();
  
  
  $sql="SELECT gks_hotel_room.id_hotel_room, gks_hotel_room.room_descr, 
  gks_hotel_room.hotel_floor_id, gks_hotel_floor.floor_descr,
  gks_hotel_room.hotel_room_type_id, gks_hotel_room_type.room_type_descr, gks_hotel_room_type_en_US.room_type_descr_en_US, 
  gks_hotel_room_type.room_type_visitors, gks_hotel_room_type.room_type_visitors_childs, gks_hotel_room_type.room_type_visitors_max,
  gks_hotel_room_type.room_type_child_kounies,gks_hotel_room_type.room_type_extra_beds,
  gks_hotel_room_type.room_type_price,
  gks_hotel_room.room_status,gks_hotel_room_type.room_type_status,
  gks_hotel_room_type.room_type_photo
  FROM ((gks_hotel_room 
  LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type) 
  LEFT JOIN gks_hotel_floor ON gks_hotel_room.hotel_floor_id = gks_hotel_floor.id_hotel_floor)
  LEFT JOIN (
    SELECT hotel_room_type_id, room_type_descr as room_type_descr_en_US FROM gks_hotel_room_type_lang WHERE lang_code='en-US'
  ) AS gks_hotel_room_type_en_US ON gks_hotel_room_type.id_hotel_room_type = gks_hotel_room_type_en_US.hotel_room_type_id  
  
  where gks_hotel_room.hotel_id=".$id_hotel;
  if ($alldata == false) {
    $sql.=" and gks_hotel_room.room_status ='available' AND gks_hotel_room_type.room_type_status='available'";
  } else {
    //$sql.=" 1=1";
  }
  if ($id_hotel_room>0) {
    $sql.=" and gks_hotel_room.id_hotel_room =".$id_hotel_room;
  }
  if ($id_hotel_room_type>0) {
    $sql.=" and gks_hotel_room.hotel_room_type_id=".$id_hotel_room_type;
  }
  if (count($not_id_hotel_room)>0) {
    $sql.=" and gks_hotel_room.id_hotel_room not in (".implode(',',$not_id_hotel_room).")";
  }
  
  
  $sql.=" ORDER BY gks_hotel_room.room_sortorder, gks_hotel_floor.sort_order, gks_hotel_floor.floor_descr, gks_hotel_room.room_descr;";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$outdata['error_msg'] = gks_lang('sql error');return $outdata;}
  $rooms = array();
  while ($row = $result->fetch_assoc()) { 
    $row['avl_days_settings']=0;
    $row['is_avl_state_settings']=false;
    $row['avl_days_reservation']=0;
    $row['is_avl_state_reservation']=false;
    $row['avl_days_folio']=0;
    $row['is_avl_state_folio']=false;
    $row['room_total_price']=0;
    $row['days'] = $days;
    $rooms[$row['id_hotel_room']]= $row;
    foreach ($rooms[$row['id_hotel_room']]['days'] as &$myday) {
      if ($row['room_type_price']>0) {
        $myday['fromp'] = gks_lang('Προεπιλεγμένη τιμή από τον τύπου δωματίου');
        $myday['price_room']=$row['room_type_price'];
        $myday['price_room_format']=myCurrencyFormat($row['room_type_price']);
        
        $myday['price'] = $myday['price_room'];
        $myday['price_format']=myCurrencyFormat($myday['price']);
        
      }
    } 
    unset($myday);
    $id_hotel_room_array[] = $row['id_hotel_room'];
    if (in_array($row['hotel_room_type_id'], $id_hotel_room_type_array) == false) {
      $id_hotel_room_type_array[] = $row['hotel_room_type_id'];
    }
  }
  if ($alldata == false and count($rooms)==0) {
    $outdata['error_msg'] = gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια');
    //debug_mail(false,gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια').' (1)','<pre>'.print_r($outdata,true));
    return $outdata;
  }
  $outdata['rooms'] = $rooms;
      


  //availability room type
  if (count($id_hotel_room_type_array)) {
    $sql="SELECT gks_hotel_availability_day.hotel_room_type_id,
    gks_hotel_availability_day.availability_day, gks_hotel_availability_day.availability_status, gks_hotel_availability.availability_descr
    FROM gks_hotel_availability_day LEFT JOIN gks_hotel_availability ON gks_hotel_availability_day.hotel_availability_id = gks_hotel_availability.id_hotel_availability
    where gks_hotel_availability_day.hotel_room_type_id in (".implode(',',$id_hotel_room_type_array).")
    and gks_hotel_availability_day.availability_day>='". $db_link->escape_string($date_from)."'
    and gks_hotel_availability_day.availability_day<='". $db_link->escape_string($date_to)."'";
    if ($id_hotel_room>0) {
      $sql.=" and gks_hotel_availability_day.hotel_room_id =".$id_hotel_room;
    }
    if (count($not_id_hotel_room)>0) {
      $sql.=" and gks_hotel_availability_day.hotel_room_id not in (".implode(',',$not_id_hotel_room).")";
    }
    $sql.=" order by gks_hotel_availability_day.availability_day";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$outdata['error_msg'] = gks_lang('sql error');return $outdata;}
    
    while ($row = $result->fetch_assoc()) {
      $dateval=strtotime($row['availability_day']);
      $dateval=date('Y-m-d', $dateval);
      foreach ($outdata['rooms']as &$myroom) {
        if ($row['hotel_room_type_id'] == $myroom['hotel_room_type_id']) {
          foreach ($myroom['days'] as $daykey => &$myday) {
            if ($daykey == $dateval) {
              if ($myday['val1'] == 1) {
                $myday['froma'] = gks_lang('Διαθεσιμότητα από τον τύπο δωματίου').', ';
                $myday['val1'] = $row['availability_status'];
                if (isset($row['availability_descr']) and trim_gks($row['availability_descr'])!='') {
                  $myday['descra'].=trim_gks($row['availability_descr']).', ';
                }              
              }
            }
          }
          unset($myday); 
        }
      } 
      unset($myroom);
    }
  }
  
  //price room type
  if (count($id_hotel_room_type_array)) { 
    $sql="SELECT gks_hotel_price_day.hotel_room_type_id,
    gks_hotel_price_day.price_day, gks_hotel_price_day.price, gks_hotel_price.price_descr
    FROM gks_hotel_price_day LEFT JOIN gks_hotel_price ON gks_hotel_price_day.hotel_price_id = gks_hotel_price.id_hotel_price
    where gks_hotel_price_day.hotel_room_type_id in (".implode(',',$id_hotel_room_type_array).") 
    and gks_hotel_price_day.price_day>='". $db_link->escape_string($date_from)."'
    and gks_hotel_price_day.price_day<='". $db_link->escape_string($date_to)."'";
    if ($id_hotel_room_type>0) {
      $sql.=" and gks_hotel_price_day.hotel_room_type_id =".$id_hotel_room_type;
    }
    
    $sql.=" order by gks_hotel_price_day.price_day";
    //echo '<pre>'.$sql;die();
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$outdata['error_msg'] = gks_lang('sql error');return $outdata;}
    while ($row = $result->fetch_assoc()) { 
      $dateval=strtotime($row['price_day']);
      $dateval=date('Y-m-d', $dateval);
      
      foreach ($outdata['rooms']as &$myroom) {
        if ($row['hotel_room_type_id'] == $myroom['hotel_room_type_id']) {
          foreach ($myroom['days'] as $daykey => &$myday) {
            if ($daykey == $dateval) {
              
              $myday['fromp'] = gks_lang('Τιμή από τον τύπου δωματίου');
              $myday['price_room'] = $row['price'];
              $myday['price_room_format'] = myCurrencyFormat($row['price']);
              
              $myday['price'] = $myday['price_room'];
              $myday['price_format']=myCurrencyFormat($myday['price']);
                            
              if (isset($row['price_descr']) and trim_gks($row['price_descr'])!='') {
                $myday['descrp'].=trim_gks($row['price_descr']).', ';
              }
            }
          }
          unset($myday); 
        }
      } 
      unset($myroom);      
    }    
  }
  
  
  //availability room
  if (count($id_hotel_room_array)>0) {
    $sql="SELECT gks_hotel_availability_day.hotel_room_id,
    gks_hotel_availability_day.availability_day, gks_hotel_availability_day.availability_status, gks_hotel_availability.availability_descr
    FROM gks_hotel_availability_day 
    LEFT JOIN gks_hotel_availability ON gks_hotel_availability_day.hotel_availability_id = gks_hotel_availability.id_hotel_availability
    where gks_hotel_availability_day.hotel_room_id in (".implode(',',$id_hotel_room_array).")
    and gks_hotel_availability_day.availability_day>='". $db_link->escape_string($date_from)."'
    and gks_hotel_availability_day.availability_day<='". $db_link->escape_string($date_to)."'";
    if ($id_hotel_room>0) {
      $sql.=" and gks_hotel_availability_day.hotel_room_id =".$id_hotel_room;
    }   
    if (count($not_id_hotel_room)>0) {
      $sql.=" and gks_hotel_availability_day.hotel_room_id not in (".implode(',',$not_id_hotel_room).")";
    }     
    $sql.=" order by gks_hotel_availability_day.availability_day";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$outdata['error_msg'] = gks_lang('sql error');return $outdata;}
    
    while ($row = $result->fetch_assoc()) { 
      $dateval=strtotime($row['availability_day']);
      $dateval=date('Y-m-d', $dateval);

      if (isset($outdata['rooms'][$row['hotel_room_id']])) {
        if (isset($outdata['rooms'][$row['hotel_room_id']]['days'][$dateval])) {
          if ($outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['val1'] == 1) {
            $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['froma'] = gks_lang('Διαθεσιμότητα από το δωμάτιο').', ';
            $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['val1'] = intval($row['availability_status']);
            if (isset($row['availability_descr']) and trim_gks($row['availability_descr'])!='') {
              $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['descra'].=trim_gks($row['availability_descr']).', ';
            }              
          }

        }
      }     
       

    }
  }
  
  foreach ($outdata['rooms']as &$myroom) {
    $avl_days_settings=0;
    foreach ($myroom['days'] as $daykey => &$myday) {
      if ($myday['val1'] == 1) {
        $avl_days_settings++;
      }
    }
    unset($myday); 
    $myroom['avl_days_settings'] = $avl_days_settings;
    if ($avl_days_settings == $outdata['days']) {
      $myroom['is_avl_state_settings']=true;
      $outdata['avl_rooms_state_settings']++;
    }
  }      
  unset($myroom);  
  
  if ($alldata == false and $outdata['avl_rooms_state_settings']==0) {
    $outdata['error_msg'] = gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια');
    //debug_mail(false,gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια').' (2)','<pre>'.print_r($outdata,true));
    return $outdata;
  }

 
  if ($alldata == false) { //clean rooms
    $id_hotel_room_array=array();
    foreach ($outdata['rooms']as &$myroom) {
      if ($myroom['is_avl_state_settings'] == true) {
        if (in_array($myroom['id_hotel_room'], $id_hotel_room_array) == false) {
          $id_hotel_room_array[] = $myroom['id_hotel_room'];
        }
      }
    }
    unset($myroom);
  }

  
  // check reservation
  if (count($id_hotel_room_array)>0) {
    $sql="SELECT gks_hotel_reservation_room_day.id_hotel_reservation_room_day,
    gks_hotel_reservation_room_day.hotel_reservation_id,
    gks_hotel_reservation_room_day.dreservation_status,
    gks_hotel_reservation_room_day.hotel_reservation_room_type_id,
    gks_hotel_reservation_room_day.hotel_reservation_room_id,
    gks_hotel_reservation_room_day.hotel_room_id,
    gks_hotel_reservation_room_day.reservation_room_day,
    gks_hotel_reservation_room_day.priceperday
    FROM gks_hotel_reservation_room_day
    WHERE gks_hotel_reservation_room_day.hotel_room_id In (".implode(',',$id_hotel_room_array).") 
    AND gks_hotel_reservation_room_day.reservation_room_day>='". $db_link->escape_string($date_from)."'
    AND gks_hotel_reservation_room_day.reservation_room_day<='". $db_link->escape_string($date_to)."'";
    if ($alldata == false) {
      $sql.=" and gks_hotel_reservation_room_day.dreservation_status in ('070wait_payment','080confirm','100completed','110payment')";
    } else {
      $sql.=" and gks_hotel_reservation_room_day.dreservation_status <>'040cancelled'";
    }
    if ($id_hotel_room>0) {
      $sql.=" and gks_hotel_reservation_room_day.hotel_room_id = ".$id_hotel_room;
    }
    if (count($not_id_hotel_room)>0) {
      $sql.=" and gks_hotel_reservation_room_day.hotel_room_id not in (".implode(',',$not_id_hotel_room).")";
    }    
    if ($not_id_hotel_reservation>0) {
      $sql.=" and gks_hotel_reservation_room_day.hotel_reservation_id <> ".$not_id_hotel_reservation;
    }    
    $sql.=" ORDER BY gks_hotel_reservation_room_day.reservation_room_day,hotel_reservation_id,id_hotel_reservation_room_day;";
    //echo $sql;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$outdata['error_msg'] = 'sql error';return $outdata;}
    
    while ($row = $result->fetch_assoc()) { 
      $dateval=strtotime($row['reservation_room_day']);
      $dateval=date('Y-m-d', $dateval);
      
      //echo $dateval;
      
      if (isset($outdata['rooms'][$row['hotel_room_id']])) {
        if (isset($outdata['rooms'][$row['hotel_room_id']]['days'][$dateval])) {
          
          $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['val2'] = 0;
          $row['priceperday_format'] =  myCurrencyFormat($row['priceperday']);
          if (isset($outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['reservation']) == false) {
            $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['reservation'] = array();
          }
          $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['reservation'][] = $row;
          $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['froma'].= gks_lang('Κρατημένο από κράτηση').', ';
          $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['descra'].= 'hotel_reservation_id: '.$row['hotel_reservation_id'].', ';
          
        
        }
      }
    }  
  }

  foreach ($outdata['rooms']as &$myroom) {
    $avl_days_reservation=0;
    foreach ($myroom['days'] as $daykey => &$myday) {
      if ($myday['val1'] == 1 and $myday['val2'] == 1) {
        $avl_days_reservation++;
      }
    }
    unset($myday); 
    $myroom['avl_days_reservation'] = $avl_days_reservation;
    if ($avl_days_reservation == $outdata['days']) {
      if ($myroom['is_avl_state_settings']) {
        $myroom['is_avl_state_reservation']=true;
        $outdata['avl_rooms_state_reservation']++;
      }
    }
  }      
  unset($myroom);
  
  if ($alldata == false and $outdata['avl_rooms_state_reservation']==0) {
    $outdata['error_msg'] = gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια');
    //debug_mail(false,gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια').' (3)','<pre>'.print_r($outdata,true));
    return $outdata;    
  }  

  
  //check folio
  if (count($id_hotel_room_array)>0) {
    $sql="SELECT gks_hotel_folio_room_day.id_hotel_folio_room_day, gks_hotel_folio_room_day.hotel_folio_id, 
    gks_hotel_folio_room_day.hotel_folio_room_id, gks_hotel_folio_room_day.hotel_room_id, gks_hotel_folio_room_day.dfolio_status, 
    gks_hotel_folio_room_day.folio_room_day, gks_hotel_folio_room_day.priceperday
    FROM gks_hotel_folio_room_day
    WHERE gks_hotel_folio_room_day.hotel_room_id In (".implode(',',$id_hotel_room_array).")
    AND gks_hotel_folio_room_day.folio_room_day>='". $db_link->escape_string($date_from)."'
    AND gks_hotel_folio_room_day.folio_room_day<='". $db_link->escape_string($date_to)."'";
    if ($alldata == false) {
      $sql.=" and gks_hotel_folio_room_day.dfolio_status in ('20open','30complete')";
    } else {
      $sql.=" and gks_hotel_folio_room_day.dfolio_status <> '10cancel'";
    }
    if ($id_hotel_room>0) {
      $sql.=" and gks_hotel_folio_room_day.hotel_room_id = ".$id_hotel_room;
    }
    if (count($not_id_hotel_room)>0) {
      $sql.=" and gks_hotel_folio_room_day.hotel_room_id not in (".implode(',',$not_id_hotel_room).")";
    }     
    if ($not_id_hotel_folio > 0)  {
      $sql.=" and gks_hotel_folio_room_day.hotel_folio_id <> ".$not_id_hotel_folio;
    }
    $sql.=" ORDER BY gks_hotel_folio_room_day.priceperday,hotel_folio_id,id_hotel_folio_room_day;";
    //echo $sql;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$outdata['error_msg'] = gks_lang('sql error');return $outdata;}
    
    while ($row = $result->fetch_assoc()) { 
      $dateval=strtotime($row['folio_room_day']);
      $dateval=date('Y-m-d', $dateval);
      
      //echo $dateval;
      
      if (isset($outdata['rooms'][$row['hotel_room_id']])) {
        if (isset($outdata['rooms'][$row['hotel_room_id']]['days'][$dateval])) {
          $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['val2'] = 0;
          $row['priceperday_format'] =  myCurrencyFormat($row['priceperday']);
          if (isset($outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['folio'])== false) {
            $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['folio'] = array();  
          }
          $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['folio'][] = $row;
          $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['froma'].= gks_lang('Κρατημένο από φάκελο').', ';
          $outdata['rooms'][$row['hotel_room_id']]['days'][$dateval]['descra'].= 'hotel_folio_id: '.$row['hotel_folio_id'].', ';
        }
      }
    }
  }
  
  foreach ($outdata['rooms']as &$myroom) {
    $avl_days_folio=0;
    foreach ($myroom['days'] as $daykey => &$myday) {
      if ($myday['val1'] == 1 and $myday['val2'] == 1) {
        $avl_days_folio++;
      }
    }
    unset($myday); 
    $myroom['avl_days_folio'] = $avl_days_folio;
    if ($avl_days_folio == $outdata['days']) {
      if ($myroom['is_avl_state_reservation']) {
        $myroom['is_avl_state_folio']=true;
        $outdata['avl_rooms_state_folio']++;
      }
    }
  }      
  unset($myroom);  
  
  if ($alldata == false and $outdata['avl_rooms_state_folio']==0) {
    $outdata['error_msg'] = gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια');
    //debug_mail(false,gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια').' (4)','<pre>'.print_r($outdata,true));
    return $outdata;    
  }  
  
  
  
  // get simple room type
  if ($alldata == false) {
    $id_hotel_room_type_array=array();
    foreach ($outdata['rooms']as &$myroom) {
      if ($myroom['is_avl_state_settings'] == true and $myroom['is_avl_state_reservation'] == true and $myroom['is_avl_state_folio'] == true) {
        if (in_array($myroom['hotel_room_type_id'], $id_hotel_room_type_array) == false) {
          $id_hotel_room_type_array[] = $myroom['hotel_room_type_id'];
        }  
      }
    }
    unset($myroom);
  }


  foreach ($outdata['rooms']as &$myroom) {
    foreach ($myroom['days'] as $daykey => &$myday) {
      $myroom['room_total_price']+=$myday['price'];
    }
    unset($myday);
  }
  unset($myroom);  
  
  if (count($id_hotel_room_type_array)>0) {
    $sql="SELECT gks_hotel_room_type.id_hotel_room_type, 
    gks_hotel_room_type.room_type_descr,  gks_hotel_room_type_en_US.room_type_descr_en_US,
    gks_hotel_room_type.room_type_photo, 
    gks_hotel_room_type.hotel_room_type_fix_id, gks_hotel_room_type_fix.room_type_fix_descr,gks_hotel_room_type_fix.room_type_fix_descr_en, 
    gks_hotel_room_type.room_type_price, gks_hotel_room_type.room_type_status, 
    gks_hotel_room_type.room_type_embado, 
    gks_hotel_room_type.room_type_visitors, gks_hotel_room_type.room_type_visitors_childs, gks_hotel_room_type.room_type_visitors_max,
    gks_hotel_room_type.room_type_child_kounies,gks_hotel_room_type.room_type_extra_beds,
    gks_hotel_room_type.room_type_bedrooms, gks_hotel_room_type.room_type_living_rooms, gks_hotel_room_type.room_type_bathrooms
    FROM (gks_hotel_room_type 
    LEFT JOIN (
      SELECT hotel_room_type_id, room_type_descr as room_type_descr_en_US FROM gks_hotel_room_type_lang WHERE lang_code='en-US'
    ) AS gks_hotel_room_type_en_US ON gks_hotel_room_type.id_hotel_room_type = gks_hotel_room_type_en_US.hotel_room_type_id)
    LEFT JOIN gks_hotel_room_type_fix ON gks_hotel_room_type.hotel_room_type_fix_id = gks_hotel_room_type_fix.id_hotel_room_type_fix
    where id_hotel_room_type in (".implode(',',$id_hotel_room_type_array).")";
    if ($id_hotel_room_type>0) {
      $sql.=" and gks_hotel_room_type.id_hotel_room_type =".$id_hotel_room_type;
    }
    $sql.=" ORDER BY gks_hotel_room_type.room_type_sortorder, gks_hotel_room_type.room_type_descr;";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$outdata['error_msg'] = gks_lang('sql error');return $outdata;} 
    while ($row = $result->fetch_assoc()) { 
      $row['type_room_total_price']=0;
      $row['free_rooms']=array();
      $outdata['rooms_types'][$row['id_hotel_room_type']] = $row;
    }
    
    foreach ($outdata['rooms_types'] as &$room_type) {
      foreach ($outdata['rooms']as $myroom) {
        if ($myroom['hotel_room_type_id'] == $room_type['id_hotel_room_type']) {
          if ($myroom['is_avl_state_folio'] == true) {
            $room_type['free_rooms'][]=$myroom['id_hotel_room'];
            if ($room_type['type_room_total_price']<=0) { //den exei idi set apo allo domatio
              $room_type['type_room_total_price'] = $myroom['room_total_price'];
            }
          }
        }
      }      
    } 
    unset($room_type);
  }

  
  //find max track count
  foreach ($outdata['rooms']as &$myroom) {
    $max_room_tracks=0;
    foreach ($myroom['days'] as $daykey => &$myday) {
      $max_tracks=0;
      if (isset($myday['reservation'])) $max_tracks+=count($myday['reservation']);
      if (isset($myday['folio'])) $max_tracks+=count($myday['folio']);
      if ($max_tracks>0) {
        $myday['dtracks']=$max_tracks;
        if ($max_tracks > $max_room_tracks) $max_room_tracks = $max_tracks;
      }
    }
    unset($myday);
    $myroom['rtracks'] = $max_room_tracks;
  }
  unset($myroom);


  //assing tracks to reservation and folio
  foreach ($outdata['rooms']as &$myroom) {
    if ($myroom['rtracks']>0) {
      foreach ($myroom['days'] as $daykey => &$myday) {
        $dateval=$daykey;        
        $dateval_prev=date('Y-m-d', strtotime($daykey) - 24*60*60);        
        $myday['dateval']=$dateval;
        $myday['dateval_prev']=$dateval_prev;              
        
        if (isset($myroom['days'][$dateval_prev]) == false or (isset($myroom['days'][$dateval_prev]) and isset($myroom['days'][$dateval_prev]['reservation']) == false and isset($myroom['days'][$dateval_prev]['folio']) == false)) { //iparxei alla den exei tipota gia auta
          //anathesi me tin seira
          $track=0;
          if (isset($myday['reservation'])) {
            foreach ($myday['reservation'] as &$reservation) {
              $track++;
              $reservation['track'] = $track;
            }
            unset($reservation);
          }
          if (isset($myday['folio'])) {
            foreach ($myday['folio'] as &$folio) {
              $track++;
              $folio['track'] = $track;
            }
            unset($folio);
          }
        } else {
          $track_found=array();
          //print '<pre>';
          //print $daykey;
          //print_r($myday['reservation']);
          //die();
          if (isset($myday['reservation'])) {
            foreach ($myday['reservation'] as &$reservation) {
              if (isset($myroom['days'][$dateval_prev]['reservation'])) {
                foreach ($myroom['days'][$dateval_prev]['reservation'] as &$reservation_prev) {
                  if ($reservation_prev['hotel_reservation_id']==$reservation['hotel_reservation_id']) {
                    if (isset($reservation_prev['track'])) {
                      $reservation['track'] = $reservation_prev['track'];
                      $track_found[] = $reservation['track'];
                    } else {
                      //$reservation['track']=1111;
                    }
                    break;
                  }
                }
                unset($reservation_prev);  
              }
            }
            unset($reservation);          
          }
          if (isset($myday['folio'])) {
            foreach ($myday['folio'] as &$folio) {
              if (isset($myroom['days'][$dateval_prev]['folio'])) {
                foreach ($myroom['days'][$dateval_prev]['folio'] as &$folio_prev) {
                  if ($folio_prev['hotel_folio_id']==$folio['hotel_folio_id']) {
                    if (isset($folio_prev['track'])) {
                      $folio['track'] = $folio_prev['track'];
                      $track_found[] = $folio['track'];
                    } else {
                      //$folio['track']=22222222;
                    }
                    break;
                  }
                }
                unset($folio_prev);  
              }
            }
            unset($folio);          
          }
          
          //rest not assign to parents
          if (isset($myroom['rtracks'])) {
            if (isset($myday['reservation'])) {
              foreach ($myday['reservation'] as &$reservation) {
                if (isset($reservation['track']) == false) {
                  for ($ctrack = 1; $ctrack <= $myroom['rtracks']; $ctrack++) {
                    if (in_array($ctrack,$track_found) == false) {
                      $reservation['track'] = $ctrack;
                      $track_found[] = $ctrack;
                      break;
                    }
                  }                  
                }
              }
              unset($reservation);
            }
            if (isset($myday['folio'])) {
              foreach ($myday['folio'] as &$folio) {
                if (isset($folio['track']) == false) {
                  for ($ctrack = 1; $ctrack <= $myroom['rtracks']; $ctrack++) {
                    if (in_array($ctrack,$track_found) == false) {
                      $folio['track'] = $ctrack;
                      $track_found[] = $ctrack;
                      break;
                    }
                  }                  
                }
              }
              unset($folio);
            }            
          }
        }
      }
      unset($myday);  
    }
  }
  unset($myroom);
  
  
  $rooms_types_sort=$outdata['rooms_types'];
  $outdata['rooms_types']=array();
  usort($rooms_types_sort, 'room_types_sort_price_name');
  foreach ($rooms_types_sort as $value) {
    $outdata['rooms_types'][$value['id_hotel_room_type']] = $value;
  } 
  
  
  if ($outdata['avl_rooms_state_folio']==0) {
    $outdata['error_msg'] = gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια');
    //debug_mail(false,gks_lang('Δεν βρέθηκαν διαθέσιμα δωμάτια').' (5)','<pre>'.print_r($outdata,true));
  }





  foreach ($outdata['rooms']as &$myroom) {

    $rchilds_ages_list_crop=array();
    //$rchilds_ages_list_crop = $rchilds_ages_list;
    //print '<pre>';
    //print_r($myroom);
    //die();
  
//    print '<pre>';
//    print $rnum_adults.'|';
//    print $rnum_childs.'|';
//    print_r($rchilds_ages_list);
//    die();

    if ($hotel_params['hotel_child_free_as_adults']) {
      $free_childs_as_adults = $myroom['room_type_visitors'] - $rnum_adults;
  //    print '<pre>';
  //    print $free_childs_as_adults.'|';
  //    print count($rchilds_ages_list).'|';
  //    die();
      if ($free_childs_as_adults < 0) $free_childs_as_adults=0;
      if ($free_childs_as_adults > count($rchilds_ages_list)) $free_childs_as_adults=count($rchilds_ages_list);
      
      $free_childs_as_adults_remain=$free_childs_as_adults;
    }
    
//    $cc=0;
//    foreach($rchilds_ages_list as $k => $age_list) {
//      $cc++;  
//      if ($cc > $myroom['room_type_visitors_childs']) break;
//      $rchilds_ages_list_crop[$k] = $age_list;
//    }
    $cc=0;
    foreach($rchilds_ages_list as $k => $age_list) {
      $cc++;  
      if ($cc > ($rnum_adults + $myroom['room_type_visitors_max'])) break;
      $rchilds_ages_list_crop[$k] = $age_list;
    }
    
    usort($rchilds_ages_list_crop, "roomaf_html_chlds_array_sort");
    
    //print '<pre>';print_r($rchilds_ages_list_crop);die();
        
    $myroom['rchilds_ages_list'] = $rchilds_ages_list;
    $myroom['rchilds_ages_list_crop'] = $rchilds_ages_list_crop;    
    
    $child_prices = array(
      'free_childs' => array(
        'childs' => 0,
        'ages' => array(),
      ),
      'night' => array(
        'ages' => array(),
      ),
      'stay' => array(
        'ages' => array(),
      ),
      'as_adults' => array (
        'childs' => 0,
        'ages' => array(),
      ),
    );
  
    //print '<pre>';print_r($rchilds_ages_list_crop); die();
    
    foreach($rchilds_ages_list_crop as $age_list) {
      if ($age_list['age']>=0) {
        if ($hotel_params['hotel_child_free_as_adults'] and $free_childs_as_adults_remain > 0) {
          $free_childs_as_adults_remain--;
          $child_prices['as_adults']['childs']++;
          $child_prices['as_adults']['ages'][]= $age_list['age'];          
        } else {
        
          $found=false;
          foreach ($hotel_params['hotel_child_age_price'] as $age_set) {
            if ($age_list['age'] >=$age_set['from'] and $age_list['age'] <=$age_set['to']) {
              $found=true;
              if ($age_set['price']==0) {
                $child_prices['free_childs']['childs']++;
                $child_prices['free_childs']['ages'][]= $age_list['age'];
              } else {
                if (isset($child_prices[$age_set['type']])) { //only night ans stay
                  if (isset($child_prices[$age_set['type']]['ages'][$age_list['age']]) == false) {
                    $child_prices[$age_set['type']]['ages'][$age_list['age']] = array(
                      'childs' => 0,
                      'price' => $age_set['price'],
                    );
                  }
                  $child_prices[$age_set['type']]['ages'][$age_list['age']]['childs']++;
                }
              }
              break;
            }
          }
          if ($found == false) { //age not found, is adult
            $child_prices['as_adults']['childs']++;
            $child_prices['as_adults']['ages'][]= $age_list['age'];
          }
        }
      }
    }
    $myroom['child_prices'] = $child_prices;

    $child_kounies_prices = array(
      'free_childs' => array(
        'childs' => 0,
        'ages' => array(),
      ),
      'night' => array(
        'ages' => array(),
      ),
      'stay' => array(
        'ages' => array(),
      ),

    );
    //print '<pre>';print_r($hotel_params); die();    
    //print '<pre>';print_r($myroom); die();   
    
    
    $rnum_child_kounies_found=0;
    if ($hotel_params['hotel_child_kounies']['enable'] and $rnum_child_kounies>0) {
      foreach($rchilds_ages_list_crop as $age_list) {
        if ($age_list['age']>=0) {
          if ($hotel_params['hotel_child_kounies']['price']==0 or $age_list['age'] < $hotel_params['hotel_child_kounies']['from']) {
            $child_kounies_prices['free_childs']['childs']++;
            $child_kounies_prices['free_childs']['ages'][]= $age_list['age'];
            $rnum_child_kounies_found++;
          } else if ($age_list['age'] >=$hotel_params['hotel_child_kounies']['from'] and $age_list['age'] <=$hotel_params['hotel_child_kounies']['to']) {
            //print '<pre>cccc '; print $age_list['age'];print_r($hotel_params['hotel_child_kounies']);die();
            if (isset($child_kounies_prices[$hotel_params['hotel_child_kounies']['type']])) { //only night ans stay
              if (isset($child_kounies_prices[$hotel_params['hotel_child_kounies']['type']]['ages'][$age_list['age']]) == false) {
                $child_kounies_prices[$hotel_params['hotel_child_kounies']['type']]['ages'][$age_list['age']] = array(
                  'childs' => 0,
                  'price' => $hotel_params['hotel_child_kounies']['price'],
                );
              }
              $child_kounies_prices[$hotel_params['hotel_child_kounies']['type']]['ages'][$age_list['age']]['childs']++;
              $rnum_child_kounies_found++;
            }
          }
        }
        if ($rnum_child_kounies_found >= $rnum_child_kounies) {
          break;
        }
      }
    }  
    $myroom['child_kounies_prices'] = $child_kounies_prices;
    $myroom['rnum_child_kounies_found'] = $rnum_child_kounies_found;
    
    
    //print '<pre>bbbb ';print_r($myroom['child_kounies_prices']); die();    

    $extra_beds_prices = array(
      'free_childs' => array(
        'childs' => 0,
        'ages' => array(),
      ),
      'night' => array(
        'ages' => array(),
      ),
      'stay' => array(
        'ages' => array(),
      ),
      'as_adults' => array (
        'childs' => 0,
        'ages' => array(),
      ),
    );
    
    //print '<pre>bbbb ';print_r($hotel_params); die(); 
    
    

    $rnum_extra_beds_found=0;
    if ($hotel_params['hotel_extra_beds']['enabled'] and $rnum_extra_beds>0) {
      
      $keys=array_keys($rchilds_ages_list_crop);
      for ($reverse_key=count($keys)-1;$reverse_key>=0; $reverse_key--) { 
      //foreach($rchilds_ages_list_crop as $age_list) {
        $age_list=$rchilds_ages_list_crop[$reverse_key];
        if ($age_list['age']>=0) {
          $found=false;
          
          foreach ($hotel_params['hotel_extra_beds']['beds'] as $age_set) {
            //$age_set=$hotel_params['hotel_extra_beds']['beds'][$reverse_key];
            if ($age_list['age'] >=$age_set['from'] and $age_list['age'] <=$age_set['to']) {
              $found=true;
              if ($age_set['price']==0) {
                $extra_beds_prices['free_childs']['childs']++;
                $extra_beds_prices['free_childs']['ages'][]= $age_list['age'];
                $rnum_extra_beds_found++;
              } else {
                if (isset($extra_beds_prices[$age_set['type']])) { //only night ans stay
                  if (isset($extra_beds_prices[$age_set['type']]['ages'][$age_list['age']]) == false) {
                    $extra_beds_prices[$age_set['type']]['ages'][$age_list['age']] = array(
                      'childs' => 0,
                      'price' => $age_set['price'],
                    );
                    
                  }
                  $extra_beds_prices[$age_set['type']]['ages'][$age_list['age']]['childs']++;
                  $rnum_extra_beds_found++;
                }
              }
              break;
            }
          }
//          if ($found == false) { //age not found, is adult
//            $extra_beds_prices['as_adults']['childs']++;
//            $extra_beds_prices['as_adults']['ages'][]= $age_list['age'];
//          }
        }
        
        if ($rnum_extra_beds_found >= $rnum_extra_beds) {
          break;
        }
      }
    }
    
    
    if ($rnum_extra_beds_found < $rnum_extra_beds) {
      $diafora=$rnum_extra_beds-$rnum_extra_beds_found;
      
      $age_list=array();$age_list['age']=18;
      foreach ($hotel_params['hotel_extra_beds']['beds'] as $age_set) {
        if ($age_set['to']==18) {
          for ($dd=1;$dd<=$diafora;$dd++) {
            if ($age_set['price']==0) {
              $extra_beds_prices['free_childs']['childs']++;
              $extra_beds_prices['free_childs']['ages'][]= $age_list['age'];
              $rnum_extra_beds_found++;
            } else {
              if (isset($extra_beds_prices[$age_set['type']])) { //only night ans stay
                if (isset($extra_beds_prices[$age_set['type']]['ages'][$age_list['age']]) == false) {
                  $extra_beds_prices[$age_set['type']]['ages'][$age_list['age']] = array(
                    'childs' => 0,
                    'price' => $age_set['price'],
                  );
                }
                $extra_beds_prices[$age_set['type']]['ages'][$age_list['age']]['childs']++;
                $rnum_extra_beds_found++;
              }
            }
          }
          break;  
        }   
        
      }
      
    }
    
        
    $myroom['extra_beds_prices'] = $extra_beds_prices;
    $myroom['rnum_extra_beds'] = $rnum_extra_beds;
    $myroom['rnum_extra_beds_found'] = $rnum_extra_beds_found;

    
    //print '<pre>bbbb |';print $myroom['rnum_extra_beds'].'|'.$myroom['rnum_extra_beds_found'].'|'; print_r($myroom['extra_beds_prices']);print_r($myroom); die();  
    
    
    //child
    $per_stay_child=0;
    foreach ($myroom['child_prices']['stay']['ages'] as $value) {
      $per_stay_child+= $value['childs'] * $value['price'];
    }
    $per_night_child=0;
    foreach ($myroom['child_prices']['night']['ages'] as $value) {
      $per_night_child+= $value['childs'] * $value['price'];
    }
    $room_child_extra_night=$per_night_child;
    if ($outdata['days'] > 0) $room_child_extra_night+=$per_stay_child/$outdata['days'];
    
    //kounies
    $per_stay_kounies=0;
    foreach ($myroom['child_kounies_prices']['stay']['ages'] as $value) {
      $per_stay_kounies+= $value['childs'] * $value['price'];
    }
    $per_night_kounies=0;
    foreach ($myroom['child_kounies_prices']['night']['ages'] as $value) {
      $per_night_kounies+= $value['childs'] * $value['price'];
    }
    $room_kounies_extra_night=$per_night_kounies;
    if ($outdata['days'] > 0) $room_kounies_extra_night+=$per_stay_kounies/$outdata['days'];
    
    //extra_beds
    $per_stay_extra_beds=0;
    foreach ($myroom['extra_beds_prices']['stay']['ages'] as $value) {
      $per_stay_extra_beds+= $value['childs'] * $value['price'];
    }
    $per_night_extra_beds=0;
    foreach ($myroom['extra_beds_prices']['night']['ages'] as $value) {
      $per_night_extra_beds+= $value['childs'] * $value['price'];
    }
    $room_extra_beds_extra_night=$per_night_extra_beds;
    if ($outdata['days'] > 0) $room_extra_beds_extra_night+=$per_stay_extra_beds/$outdata['days'];
    
    
    
    
    foreach ($myroom['days'] as &$myday) {
      $myday['price_child']=$room_child_extra_night;
      $myday['price_child_format']=myCurrencyFormat($room_child_extra_night);
      
      $myday['price_kounies']=$room_kounies_extra_night;
      $myday['price_kounies_format']=myCurrencyFormat($room_kounies_extra_night);
      
      $myday['price_extra_beds']=$room_extra_beds_extra_night;
      $myday['price_extra_beds_format']=myCurrencyFormat($room_extra_beds_extra_night);
      
      
      
      $myday['price'] = $myday['price_room'] + $myday['price_child'] + $myday['price_kounies'] + $myday['price_extra_beds'];
      $myday['price_format']=myCurrencyFormat($myday['price']);
    } 
    unset($myday);
    
    $has_childs=false;
    $roomaf_html_chlds_array = array();
    if ($myroom['child_prices']['free_childs']['childs']!=0) {
      $has_childs=true;
      foreach ($myroom['child_prices']['free_childs']['ages'] as $item) {
        $roomaf_html_chlds_array[] = array('age' => $item, 'txt' => $item.' '.gks_lang('ετών').': '.gks_lang('Δωρεάν'));
      } 
    }
    if (count($myroom['child_prices']['night']['ages'])!=0) {
      $has_childs=true;
      foreach ($myroom['child_prices']['night']['ages'] as $age => $item) {
        for ($ci=1;$ci<=$item['childs'];$ci++) {
          $roomaf_html_chlds_array[] = array('age' => $age, 'txt' => $age.' '.gks_lang('ετών').': '.myCurrencyFormat($item['price']).' / '.gks_lang('Βράδυ'));
        }
      }
    }
    if (count($myroom['child_prices']['stay']['ages'])!=0) {
      $has_childs=true;
      foreach ($myroom['child_prices']['stay']['ages'] as $age => $item) {
        for ($ci=1;$ci<=$item['childs'];$ci++) {
          $roomaf_html_chlds_array[] = array('age' => $age, 'txt' => $age.' '.gks_lang('ετών').': '.myCurrencyFormat($item['price']).' / '.gks_lang('Κράτηση'));
        }
      }
    }
              
    if ($myroom['child_prices']['as_adults']['childs']!=0) {
      $has_childs=true;
      foreach ($myroom['child_prices']['as_adults']['ages'] as $item) {
        $roomaf_html_chlds_array[] = array('age' => $item, 'txt' => $item.' '.gks_lang('ετών').': '.gks_lang('Ως ενήλικας'));
      } 
    }
    
    usort($roomaf_html_chlds_array, "roomaf_html_chlds_array_sort");
    $roomaf_html_chlds='';
    foreach ($roomaf_html_chlds_array as $value) $roomaf_html_chlds.=$value['txt'].'<br>';
    if (strlen($roomaf_html_chlds)>0) $roomaf_html_chlds=substr($roomaf_html_chlds, 0, strlen($roomaf_html_chlds)-4);
    
    $has_kounies=false;
    $roomaf_html_kounies_array = array();
    if ($myroom['child_kounies_prices']['free_childs']['childs']!=0) {
      $has_kounies=true;
      foreach ($myroom['child_kounies_prices']['free_childs']['ages'] as $item) {
        $roomaf_html_kounies_array[] = array('age' => $item, 'txt' => $item.' '.gks_lang('ετών').': '.gks_lang('Δωρεάν'));
      } 
    }
    if (count($myroom['child_kounies_prices']['night']['ages'])!=0) {
      $has_kounies=true;
      foreach ($myroom['child_kounies_prices']['night']['ages'] as $age => $item) {
        for ($ci=1;$ci<=$item['childs'];$ci++) {
          $roomaf_html_kounies_array[] = array('age' => $age, 'txt' => $age.' '.gks_lang('ετών').': '.myCurrencyFormat($item['price']).' / '.gks_lang('Βράδυ'));
        }
      }
    }
    if (count($myroom['child_kounies_prices']['stay']['ages'])!=0) {
      $has_kounies=true;
      foreach ($myroom['child_kounies_prices']['stay']['ages'] as $age => $item) {
        for ($ci=1;$ci<=$item['childs'];$ci++) {
          $roomaf_html_kounies_array[] = array('age' => $age, 'txt' => $age.' '.gks_lang('ετών').': '.myCurrencyFormat($item['price']).' / '.gks_lang('Κράτηση'));
        }
      }
    }
              
//    if ($myroom['child_kounies_prices']['as_adults']['childs']!=0) {
//      $has_kounies=true;
//      foreach ($myroom['child_kounies_prices']['as_adults']['ages'] as $item) {
//        $roomaf_html_kounies_array[] = array('age' => $item, 'txt' => $item.' '.gks_lang('ετών').': '.gks_lang('Ως ενήλικας'));
//      } 
//    }
    
    usort($roomaf_html_kounies_array, "roomaf_html_chlds_array_sort");
    $roomaf_html_kounies='';
    foreach ($roomaf_html_kounies_array as $value) $roomaf_html_kounies.=$value['txt'].'<br>';
    if (strlen($roomaf_html_kounies)>0) $roomaf_html_kounies=substr($roomaf_html_kounies, 0, strlen($roomaf_html_kounies)-4);


    $has_extra_beds=false;
    $roomaf_html_extra_beds_array = array();
    if ($myroom['extra_beds_prices']['free_childs']['childs']!=0) {
      $has_extra_beds=true;
      foreach ($myroom['extra_beds_prices']['free_childs']['ages'] as $item) {
        $roomaf_html_extra_beds_array[] = array('age' => $item, 'txt' => $item.' '.gks_lang('ετών').': '.gks_lang('Δωρεάν'));
      } 
    }
    if (count($myroom['extra_beds_prices']['night']['ages'])!=0) {
      $has_extra_beds=true;
      foreach ($myroom['extra_beds_prices']['night']['ages'] as $age => $item) {
        for ($ci=1;$ci<=$item['childs'];$ci++) {
          $roomaf_html_extra_beds_array[] = array('age' => $age, 'txt' => $age.' '.gks_lang('ετών').': '.myCurrencyFormat($item['price']).' / '.gks_lang('Βράδυ'));
        }
      }
    }
    if (count($myroom['extra_beds_prices']['stay']['ages'])!=0) {
      $has_extra_beds=true;
      foreach ($myroom['extra_beds_prices']['stay']['ages'] as $age => $item) {
        for ($ci=1;$ci<=$item['childs'];$ci++) {
          $roomaf_html_extra_beds_array[] = array('age' => $age, 'txt' => $age.' '.gks_lang('ετών').': '.myCurrencyFormat($item['price']).' / '.gks_lang('Κράτηση'));
        }
      }
    }
              
    if ($myroom['extra_beds_prices']['as_adults']['childs']!=0) {
      $has_extra_beds=true;
      foreach ($myroom['extra_beds_prices']['as_adults']['ages'] as $item) {
        $roomaf_html_extra_beds_array[] = array('age' => $item, 'txt' => $item.' '.gks_lang('ετών').': '.gks_lang('Ως ενήλικας'));
      } 
    }
    
    usort($roomaf_html_extra_beds_array, "roomaf_html_chlds_array_sort");
    $roomaf_html_extra_beds='';
    foreach ($roomaf_html_extra_beds_array as $value) $roomaf_html_extra_beds.=$value['txt'].'<br>';
    if (strlen($roomaf_html_extra_beds)>0) $roomaf_html_extra_beds=substr($roomaf_html_extra_beds, 0, strlen($roomaf_html_extra_beds)-4);

     
    
    $msg_aval_not_out='';
    $price_array = array();
    $ajia_total_out_room = 0;
    $ajia_total_out_child = 0;
    $ajia_total_out_kounies = 0;
    $ajia_total_out_extra_beds = 0;
    $ajia_total_out = 0;
    $roomaf_array=array();
    $roomaf_html_trs='';
    $roomaf_index=0;

    //print '<pre>';print_r($myroom['days']);die();
    foreach ($myroom['days'] as $myday => $day) {
      $roomaf_array[]=$day;
      
      if ($day['val1']==0) {
        $msg_aval_not_out.=date('d/m', strtotime($myday)).'<br>';
      }
      $price_as_key=myCurrencyFormat($day['price']);
      
      if (isset($price_array[$price_as_key]) == false) {
        //print '<pre>';print_r($day);die();
        $price_array[$price_as_key] = array();
      }
      $price_array[$price_as_key][] = $myday;
      $ajia_total_out_room+=$day['price_room'];
      $ajia_total_out_child+=$day['price_child'];
      $ajia_total_out_kounies+=$day['price_kounies'];
      $ajia_total_out_extra_beds+=$day['price_extra_beds'];
      
      $ajia_total_out+=$day['price'];
  
      $roomaf_index++;
      $roomaf_html_trs.='<tr>'.
        '<th scope="row" nowrap style="text-align:center;">'.$roomaf_index.'</th>'
        .'<td nowrap align="right">'.myDateFormatw(strtotime($myday)).'</td>'
        .'<td nowrap align="right">'.myCurrencyFormat($day['price_room']).'</td>';
      if ($has_childs) $roomaf_html_trs.=
         '<td nowrap align="right">'.myCurrencyFormat($day['price_child']).'</td>';
      if ($has_kounies) $roomaf_html_trs.=
         '<td nowrap align="right">'.myCurrencyFormat($day['price_kounies']).'</td>';
      if ($has_extra_beds) $roomaf_html_trs.=
         '<td nowrap align="right">'.myCurrencyFormat($day['price_extra_beds']).'</td>';
      
      if ($has_childs or $has_kounies or $has_extra_beds) $roomaf_html_trs.=  
        '<td nowrap align="right">'.myCurrencyFormat($day['price']).'</td>';
        
      $roomaf_html_trs.='</tr>';
    }  
  
    $msg_price_out='';
    foreach ($price_array as $price => $item) {
      $msg_price_out.=count($item).' x '.$price.'<br>';
    } 
    
    $roomaf_html='';
    if ($roomaf_html_trs!='') {
      $roomaf_html='<table class="table table-sm table-responsive1 table-striped table-bordered" style="font-size: 0.8rem;width:100px;" border="0" cellspacing="0" cellpadding="5" align="center">
    <thead>
      <tr>	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="40%">'.gks_lang('Ημερομηνία').'</th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="20%">'.gks_lang('Δωμάτιο').'</th>';
      if ($has_childs) $roomaf_html.=         
        '<th class="table-dark" scope="col" style="text-align: center !important;" width="20%">'.gks_lang('Παιδιά').'</th>';
      if ($has_kounies) $roomaf_html.=         
        '<th class="table-dark" scope="col" style="text-align: center !important;" width="20%"><span title="'.gks_lang('Βρεφικό Κρεβάτι').'">'.gks_lang('Βρ.Κρ').'</span></th>';
      if ($has_extra_beds) $roomaf_html.=         
        '<th class="table-dark" scope="col" style="text-align: center !important;" width="20%"><span title="'.gks_lang('Επιπλέον Κρεβάτι').'">'.gks_lang('Επ.Κρ').'</span></th>';
        
      if ($has_childs or $has_kounies or $has_extra_beds) $roomaf_html.=
        '<th class="table-dark" scope="col" style="text-align: center !important;" width="20%">'.gks_lang('Σύνολο').'</th>';
                
      $roomaf_html.='</tr>
    </thead>
    <tbody>'.
      $roomaf_html_trs.
      '<tfoot>'.
      '<tr>'.
      '<th class="table-primary" scope="row" nowrap colspan="2" style="text-align:left !important;">'.gks_lang('Σύνολο').'</th>'.
      '<td class="table-primary" align="right" style="font-weight: bold;">'.myCurrencyFormat($ajia_total_out_room).'</td>';
      if ($has_childs) $roomaf_html.=
        '<td class="table-primary" align="right" style="font-weight: bold;">'.myCurrencyFormat($ajia_total_out_child).'</td>';
      if ($has_kounies) $roomaf_html.=
        '<td class="table-primary" align="right" style="font-weight: bold;">'.myCurrencyFormat($ajia_total_out_kounies).'</td>';
      if ($has_extra_beds) $roomaf_html.=
        '<td class="table-primary" align="right" style="font-weight: bold;">'.myCurrencyFormat($ajia_total_out_extra_beds).'</td>';
      
      if ($has_childs or $has_kounies or $has_extra_beds) $roomaf_html.=
        '<td class="table-primary" align="right" style="font-weight: bold;">'.myCurrencyFormat($ajia_total_out).'</td>';
      
      $roomaf_html.='</tr>'.
      '</tbody></table>';
    }
    
    if ($roomaf_html_chlds!='') {
      $roomaf_html.='<p><b>'.gks_lang('Παιδιά').'</b><br>'.$roomaf_html_chlds.'</p>';
    }
    if ($roomaf_html_kounies!='') {
      $roomaf_html.='<p><b>'.gks_lang('Βρεφικό Κρεβάτι').'</b><br>'.$roomaf_html_kounies.'</p>';
    }
    if ($roomaf_html_extra_beds!='') {
      $roomaf_html.='<p><b>'.gks_lang('Επιπλέον Κρεβάτι').'</b><br>'.$roomaf_html_extra_beds.'</p>';
    }
    
    
    
    $myroom['room_ajia_table']=array(
      'ajia_total_out_room' => $ajia_total_out_room,
      'ajia_total_out_child' => $ajia_total_out_child,
      'ajia_total_out' => $ajia_total_out,
      'msg_price'=> $msg_price_out,
      'roomaf_html' => $roomaf_html,
      'roomaf_array' => base64_encode(json_encode($roomaf_array)),
    );  
  }
  unset($myroom);
  
  //$outdata['roomaf_html_chlds']=$roomaf_html_chlds;
  
  //file_put_contents(GKS_SITE_PATH.'tmp/outdata_'.time().'_'.rand(1000,9999).'.txt' , print_r($outdata,true));

  return $outdata;
}

function room_types_sort_price_name ($a, $b) {
  
  
  if ($b['type_room_total_price'] > $a['type_room_total_price']) return -1;
  if ($b['type_room_total_price'] < $a['type_room_total_price']) return 1;
  
  if ($a['room_type_descr'] == '' and $b['room_type_descr'] != '') return 1;
  if ($a['room_type_descr'] != '' and $b['room_type_descr'] == '') return -1;

  $collator = new Collator('el_GR');
  return $collator -> compare ($a['room_type_descr'],$b['room_type_descr']);
}

function guid_for_reservation($not_in = array()) {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    $guid = strtolower($guid);
    
    if (!(is_array($not_in) and count($not_in) and in_array($guid, $not_in))) {
      
      $sql = "SELECT reservation_guid from gks_hotel_reservation where reservation_guid='".$db_link->escape_string($guid)."'";
      $result = $db_link->query($sql);
      
      if ($result->num_rows == 0) {
        return $guid; 
      }
    }
  }
}

function hotel_round_days($id_hotel, $check_in, $check_out) {
  $ret=array();
  $check_in_time  = strtotime($check_in);
  $check_out_time = strtotime($check_out) - 24*60*60; //i imera apoxorisis den xreonete
  //echo '<pre>fff '.$id_hotel;die();
  $defs = get_def_check($id_hotel);
  if (intval(date('H',$check_in_time)) < $defs['inh']) {
    $check_in_round = date('Y-m-d',  $check_in_time - 24*60*60);
  } else {
    $check_in_round = date('Y-m-d',  $check_in_time);
  }
  if (intval(date('H',$check_out_time)) > $defs['outh']) {
    $check_out_round = date('Y-m-d',  $check_out_time + 24*60*60);
  } else {
    $check_out_round = date('Y-m-d',  $check_out_time);
  }
  $check_in_round_time =strtotime($check_in_round. ' 00:00:00');
  $check_out_round_time=strtotime($check_out_round.' 00:00:00');
  $num_days=($check_out_round_time - $check_in_round_time)/(24*60*60) + 1;  
  
  $ret['check_in_round'] = $check_in_round;
  $ret['check_out_round'] = $check_out_round;
  $ret['check_in_round_time'] = $check_in_round_time;
  $ret['check_out_round_time'] = $check_out_round_time;
  $ret['num_days'] = $num_days;
  
  return $ret;
}

function hotel_basket_rsrv_calc($id_hotel,&$myreservations, &$elems, &$total_sum, &$total_visitors, &$total_dianiktereuseis, &$total_domatia, $auto_room_asign = false) {
  global $db_link;
  
  $elems=array();
  $total_sum=0;
  $total_visitors=0;
  $total_dianiktereuseis=0;
  $total_domatia=0;
  
  foreach ($myreservations as $rsrv_aa => &$reservation ) {
    foreach ($reservation['selrooms'] as &$selroom) {
      
      foreach($selroom['rooms_items'] as $room_aa => &$room_item) {
        //$room_aa=$ii+1;
        //$selroom['rooms_items'][$room_aa]['user_sel_rooms']=$user_room;
        
        $def_vals= array(
            'is_delete' => 0,
            'is_same' => 1,
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'mobile' => '',
            'lang' => '',
            'ma_odos' => '',
            'ma_arithmos' => '',
            'ma_orofos' => '',
            'ma_perioxi' => '',
            'ma_poli' => '',
            'ma_tk' => '',
            'ma_country_id' => 0,
            'ma_nomos_id' => 0,
            'room_item_id' => 0,
            'rnum_adults' => -1,
            'rnum_childs' => -1,
            'rchilds_ages_list'=> array(),
            'rnum_child_kounies' => 0,
            'rnum_extra_beds' => 0,
            
           );
        
        $room_item=array_merge($def_vals,$room_item);
        //if (isset($selroom['rooms_items'][$room_aa]['user_sel_rooms'])
        
        $room_item['rchilds_ages_list']=$room_item['childs_and_ages']; //array();
//        foreach ($room_item['childs_and_ages'] as $value) {
//          $room_item['rchilds_ages_list'][]=$value['age'];
//        }
        //print '<pre>';print_r($room_item);die();
        
        $get_availability_rooms_imput=array(
          'id_hotel' => $id_hotel,
          'date_from' => $reservation['check_in'],
          'date_to' => $reservation['check_out'],
          'alldata' => false,
          'id_hotel_room' => 0,
          'id_hotel_room_type' => $selroom['roomtype']['id'],
          'not_id_hotel_reservation' => 0,
          'not_id_hotel_folio' => 0,
          'not_id_hotel_room' => [],
          'rnum_adults' => $room_item['rnum_adults'],
          'rnum_childs' => $room_item['rnum_childs'],
          'rchilds_ages_list' => $room_item['childs_and_ages'], 
          'rnum_child_kounies' => $room_item['rnum_child_kounies'],
          'rnum_extra_beds' => $room_item['rnum_extra_beds'],
        );
        
        $rooms_array = get_availability_rooms($get_availability_rooms_imput);
        //print '<pre>';print_r($rooms_array);die();
        $room_item['room_price']=-1;
        if ($rooms_array['error_msg']=='') {
          foreach ($rooms_array['rooms'] as $first_room) {
            //print '<pre>';print_r($first_room);die();
            $room_item['room_price']=$first_room['room_ajia_table']['ajia_total_out'];
            
            break; // mono apo to proto, ta alla einai idia
          } 
          
        }
        
        
      }
      unset ($room_item);
      
      

      
    }
    unset($selroom);
  }
  unset($reservation);

  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/myreservations0.txt',print_r($myreservations,true));
  

  foreach ($myreservations as $rsrv_aa => $reservation) {
    $myreservations[$rsrv_aa]['total_price']=0;
    $myreservations[$rsrv_aa]['total_visitors']=0;
    $myreservations[$rsrv_aa]['total_dianiktereuseis']=0;
    $myreservations[$rsrv_aa]['total_domatia']=0;
    foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
      $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['num'] = count($selroom['rooms_items']);
      $myreservations[$rsrv_aa]['total_dianiktereuseis']+= count($selroom['rooms_items']) * $reservation['num_days'];
      $myreservations[$rsrv_aa]['total_domatia']+= count($selroom['rooms_items']);
      
      foreach($selroom['rooms_items'] as $room_iteem) {
        $myreservations[$rsrv_aa]['total_price']+= $room_iteem['room_price'];
        $myreservations[$rsrv_aa]['total_visitors']+= $room_iteem['rnum_adults'] +  $room_iteem['rnum_childs'];
      }
    }
    $total_sum+=$myreservations[$rsrv_aa]['total_price'];
    $total_visitors+=$myreservations[$rsrv_aa]['total_visitors'];
    $total_dianiktereuseis+=$myreservations[$rsrv_aa]['total_dianiktereuseis'];
    $total_domatia+=$myreservations[$rsrv_aa]['total_domatia'];
  
    $elems[] = array('gks_rsrv_total_price_'.$rsrv_aa , myCurrencyFormat($myreservations[$rsrv_aa]['total_price'],true, true));
    $elems[] = array('gks_rsrv_total_visitors_'.$rsrv_aa , $myreservations[$rsrv_aa]['total_visitors']);
    $text_warn='';
    if ($myreservations[$rsrv_aa]['total_domatia']  <  $myreservations[$rsrv_aa]['rooms']) $text_warn.='<p style="margin:0px;"><i class="gks_fas gks_fa-exclamation-triangle" style="font-size:150%;color:orange;"></i> '.gks_lang('Προσοχή: Έχετε επιλέξει λιγότερα δωμάτια από αυτά που θέλετε').'</p>';
    if ($myreservations[$rsrv_aa]['total_visitors'] < ($myreservations[$rsrv_aa]['adults'] + $myreservations[$rsrv_aa]['childs'])) $text_warn.='<p style="margin:0px;"><i class="gks_fas gks_fa-exclamation-triangle" style="font-size:150%;color:orange;"></i> '.gks_lang('Προσοχή: Τα δωμάτια που έχετε επιλέξει εξυπηρετούν λιγότερους επισκέπτες από αυτούς που θέλετε').'</p>';
    if ($text_warn != '') {
      $elems[] = array('gks_rsrv_warning_'.$rsrv_aa , '<div style="margin:0px;padding:10px 10px 10px 10px;border:1px solid #dadd00;background-color: #feffbe;">'.$text_warn.'</div>');
    }  
  }
  
  foreach ($myreservations as $rsrv_aa1 => $reservation1) {
    $myreservations[$rsrv_aa1]['other_rsrv_time_overlap'] = array();
    $this_check_in1  = strtotime($reservation1['check_in']);
    $this_check_out1 = strtotime($reservation1['check_out']);
    foreach ($myreservations as $rsrv_aa2 => $reservation2) {
      if ($rsrv_aa1 != $rsrv_aa2) {
        $this_check_in2  = strtotime($reservation2['check_in']);
        $this_check_out2 = strtotime($reservation2['check_out']);
        
             if ($this_check_in2  <= $this_check_in1 and $this_check_out2 >= $this_check_out1) $myreservations[$rsrv_aa1]['other_rsrv_time_overlap'][] = $rsrv_aa2;
        else if ($this_check_in2  >= $this_check_in1 and $this_check_in2  <= $this_check_out1) $myreservations[$rsrv_aa1]['other_rsrv_time_overlap'][] = $rsrv_aa2;
        else if ($this_check_out2 >= $this_check_in1 and $this_check_out2 <= $this_check_out1) $myreservations[$rsrv_aa1]['other_rsrv_time_overlap'][] = $rsrv_aa2;
        
      }
    }
  }
  
  
  
  
  //if ($GKS_HOTEL_RESERVATION_CAN_SELECT_ROOM != 0) {
  if ($auto_room_asign) {
//    $all_rooms=array();
//    $sql="SELECT gks_hotel_room.id_hotel_room, gks_hotel_room.room_descr, gks_hotel_room.hotel_floor_id, gks_hotel_floor.floor_descr
//    FROM gks_hotel_room 
//    LEFT JOIN gks_hotel_floor ON gks_hotel_room.hotel_floor_id = gks_hotel_floor.id_hotel_floor
//    ORDER BY gks_hotel_floor.floor_descr, gks_hotel_room.room_descr;";
//    $result = $db_link->query($sql);  
//    if (!$result) {
//      debug_mail(false,'error sql',$sql);
//      $return = array('success' => false, 'message' => base64_encode('sql error'));
//      echo json_encode($return); die(); }
//    
//    $row_edit=array();
//    while ($row = $result->fetch_assoc()) {
//      $all_rooms[$row['id_hotel_room']] = $row;
//    }
    
    
    foreach ($myreservations as $rsrv_aa => $reservation) {
      $exist_or_anav_rooms=array();
      
      foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
        foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
          if ($myroom['room_item_id']>0) {
            if (in_array($myroom['room_item_id'], $exist_or_anav_rooms) == false) {
              $exist_or_anav_rooms[] = $myroom['room_item_id'];
            }
          }
        }
      }
      
      foreach ($myreservations as $rsrv_aa2 => $reservation2) {
        if ($rsrv_aa != $rsrv_aa2) {
          if (in_array($rsrv_aa, $reservation2['other_rsrv_time_overlap'])) {
            
            foreach ($reservation2['selrooms'] as $roomtype_aa => $selroom) {
              foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
                if ($myroom['room_item_id']>0) {
                  if (in_array($myroom['room_item_id'], $exist_or_anav_rooms) == false) {
                    $exist_or_anav_rooms[] = $myroom['room_item_id'];
                  }
                }
              }
            }            
             
          }
          
        }
      }
      $myreservations[$rsrv_aa]['exist_or_anav_rooms'] = $exist_or_anav_rooms;
      
      
      foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
        foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
          if ($myroom['room_item_id']==0) {
            $found_id=0;
            foreach ($selroom['roomtype']['free_rooms'] as $free_room) {
              if (in_array($free_room, $exist_or_anav_rooms) == false) {
                $found_id = $free_room;
                break;
              }
            }
            if ($found_id>0) {
              $exist_or_anav_rooms[] = $found_id;
              $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['room_item_id'] = $found_id;
            }
          }
        }
      }      
      
    }
 
  }
  
  
//  // rnum_adults and rnum_childs
//  //return;
//  foreach ($myreservations as $rsrv_aa => $reservation) {
//    $adults=$reservation['adults'];
//    $childs=$reservation['childs'];
//  
//    $exist_adults=0;
//    $exist_childs=0;
//      
//    foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
//      foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
//        if ($myroom['rnum_adults']>0) $exist_adults+=$myroom['rnum_adults'];
//        if ($myroom['rnum_childs']>0) $exist_childs+=$myroom['rnum_childs'];
//        
//        $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_adults_run'] = 0;
//        $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_childs_run'] = 0;
//        
//      }
//    }
//    $rest_adults=$adults-$exist_adults;
//    $rest_childs=$childs-$exist_childs;
//    
//    if ($rest_adults > 0 or $rest_childs > 0) {
//      $perasmata_max=($rest_adults + $rest_childs) * $reservation['rooms'] *3 ;
//      
//      $perasmata_cur=0;
//      do {
//        
//        foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
//          foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
//            if ($rest_adults>0 and $myroom['rnum_adults'] == -1) {
//              $room_max_visitors = $selroom['roomtype']['visitors_max'];
//              $exist_visitors=$myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_adults_run'] + $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_childs_run'];
//              if ($exist_visitors < $room_max_visitors) {
//                $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_adults_run']++;
//                $rest_adults--;
//              }
//            }
//            if ($rest_childs>0 and $myroom['rnum_childs'] == -1) {
//              $room_max_visitors = $selroom['roomtype']['visitors_max'];
//              $exist_visitors=$myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_adults_run'] + $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_childs_run'];
//              if ($exist_visitors < $room_max_visitors) {
//                $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_childs_run']++;
//                $rest_childs--;
//              }
//            }
//          }
//        }     
//        //break; 
//        if ($rest_adults<=0 and $rest_childs<=0) break;
//        
//        $perasmata_cur++;
//        if ($perasmata_cur>$perasmata_max) break;
//      } while (true);
//    }
//  }
//  foreach ($myreservations as $rsrv_aa => $reservation) {
//    foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
//      foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
//        if ($myroom['rnum_adults'] == -1 and $myroom['rnum_adults_run']>0) {
//          $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_adults'] = $myroom['rnum_adults_run'];
//        }
//        unset($myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_adults_run']);
//        if ($myroom['rnum_childs'] == -1 and $myroom['rnum_childs_run']>0) {
//          $myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_childs'] = $myroom['rnum_childs_run'];
//        }
//        unset($myreservations[$rsrv_aa]['selrooms'][$roomtype_aa]['rooms_items'][$room_aa]['rnum_childs_run']);
//      }
//    }
//  }
//  
//  //unset($reservation);
  
//  print '<pre>';
//  print $adults."\n";
//  print $childs."\n";
//  print $exist_adults."\n";
//  print $exist_childs."\n";
//  print $rest_adults."\n";
//  print $rest_childs."\n";
//  print_r($myreservations);
//  
//  die();  
  //file_put_contents('/var/www/php/my-rooms.gks.gr/tmp/reservation_basket_array.txt', print_r($myreservations,true));
}

function gks_hotel_reservation_room_day_recs($id, $roolist, $reservation_status,$fromdate, $enddate) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  
  //echo '<pre>';print '['.$id.']'; print_r($roolist);print '['.$reservation_status.']'; print '['.$fromdate.']'; print '['.$enddate.']'; die();
  
  
  $sql="select id_hotel_reservation_room,hotel_room_id,product_price_ekptosi_pososto,room_ajia_table_array 
  from gks_hotel_reservation_room
  where hotel_reservation_id=".$id;
  
  $sql="SELECT gks_hotel_reservation_room.id_hotel_reservation_room, gks_hotel_reservation_room.hotel_room_id, 
  gks_hotel_reservation_room.product_price_final_all_total, gks_hotel_reservation_room.product_price_ekptosi_pososto, 
  gks_hotel_reservation.num_days, gks_hotel_reservation_room.room_ajia_table_array, 
  gks_hotel.hotel_plan_price_avg
  FROM (gks_hotel_reservation_room 
  LEFT JOIN gks_hotel_reservation ON gks_hotel_reservation_room.hotel_reservation_id = gks_hotel_reservation.id_hotel_reservation) 
  LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel
  WHERE gks_hotel_reservation_room.hotel_reservation_id=".$id;



  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $room_ajia_table_array=array();
  while ($row = $result->fetch_assoc()) {
    $room_ajia_table_array[$row['hotel_room_id']]=array (
      'id_hotel_reservation_room'=>$row['id_hotel_reservation_room'],
      'hotel_room_id'=>$row['hotel_room_id'],
      'product_price_final_all_total'=>$row['product_price_final_all_total'],
      'product_price_ekptosi_pososto'=>$row['product_price_ekptosi_pososto'],
      'num_days'=>$row['num_days'],
      'hotel_plan_price_avg'=>intval($row['hotel_plan_price_avg']),
      'room_ajia_table_array' => json_decode($row['room_ajia_table_array'],true),
    );
  }
  //print '<pre>';print_r($room_ajia_table_array);die();
  

  $sql="SELECT id_hotel_reservation_room_day, reservation_room_day, hotel_room_id
  FROM gks_hotel_reservation_room_day
  WHERE hotel_reservation_id=".$id."
  order by hotel_room_id,reservation_room_day";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $exist_data=array();
  while ($row = $result->fetch_assoc()) {
    
    if (isset($exist_data[$row['hotel_room_id']]) == false) {
      $exist_data[$row['hotel_room_id']] = array(); 
    }
    $dateval=date('Y-m-d', strtotime($row['reservation_room_day']));
    $exist_data[$row['hotel_room_id']][$dateval]=array('d_recid' => $row['id_hotel_reservation_room_day'],'hascheck' => false);
  } 
//  print '<pre>';
//  print_r($exist_data);
//  die();
  
  
  
  foreach ($roolist as $myroom) {
    
    if ($myroom['delete'] == 0) {
      for ($i=$fromdate; $i<=$enddate; $i+=24*60*60) {
        $dateval=date('Y-m-d', $i);
        
        
        
        $priceperday=0;
        
        if (isset($room_ajia_table_array[$myroom['hotel_room_id']]['hotel_plan_price_avg'])) {
        
          if ($room_ajia_table_array[$myroom['hotel_room_id']]['hotel_plan_price_avg']==1) {
            //mesi timi ana imera apo tin kratisi
            if (isset($room_ajia_table_array[$myroom['hotel_room_id']]['room_ajia_table_array'])) {
              if ($room_ajia_table_array[$myroom['hotel_room_id']]['num_days']>0) {
                $priceperday= $room_ajia_table_array[$myroom['hotel_room_id']]['product_price_final_all_total'] / $room_ajia_table_array[$myroom['hotel_room_id']]['num_days'];
              }
            }
          } else {
            //analogiki timi me vasi to pososto ekptosis
            if (isset($room_ajia_table_array[$myroom['hotel_room_id']]['room_ajia_table_array'])) {
              
              $product_price_ekptosi_pososto=$room_ajia_table_array[$myroom['hotel_room_id']]['product_price_ekptosi_pososto'];
              foreach ($room_ajia_table_array[$myroom['hotel_room_id']]['room_ajia_table_array'] as $temp_value) {
                if (isset($temp_value['dateval'])) {
                  if ($temp_value['dateval']==$dateval) {
                    $priceperday=$temp_value['price'];
                    if ($product_price_ekptosi_pososto!=0) {
                      $priceperday-=$temp_value['price']*$product_price_ekptosi_pososto/100;
                      $priceperday=round($priceperday,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    }
                    break; 
                  }
                } else if (isset($temp_value['price'])) {
                    $priceperday=$temp_value['price'];
                    if ($product_price_ekptosi_pososto!=0) {
                      $priceperday-=$temp_value['price']*$product_price_ekptosi_pososto/100;
                      $priceperday=round($priceperday,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    }              
                }
              }   
            }
          }
        }
      
        if (isset($exist_data[$myroom['hotel_room_id']][$dateval])) {
          
          $sql="update gks_hotel_reservation_room_day set
          user_id_edit=".$my_wp_user_id.",
          mydate_edit=now(),
          myip='".$db_link->escape_string($gkIP)."',
          hotel_reservation_room_type_id=".$myroom['hotel_type_room_id'].",
          hotel_reservation_room_id=".$myroom['recid'].",
          dreservation_status='".$db_link->escape_string($reservation_status)."', 
          priceperday=".$priceperday."
          where hotel_reservation_id=".$id." and reservation_room_day='".$dateval."' and hotel_room_id=".$myroom['hotel_room_id'];
          
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }   
            
          $exist_data[$myroom['hotel_room_id']][$dateval]['hascheck'] = true;  
        } else {
          
  //        print '<pre>';
  //        print $myroom['hotel_room_id'];
  //        print "\r\n";
  //        print $dateval;
  //        print "\r\n";
  //        print_r($exist_data);
  //        die();
  
          
          $sql="insert into gks_hotel_reservation_room_day (
          user_id_add, user_id_edit, mydate_add, mydate_edit, myip,
          hotel_reservation_id, hotel_reservation_room_type_id, hotel_reservation_room_id, hotel_room_id, dreservation_status, 
          reservation_room_day, priceperday
          ) values (
          ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."',
          ".$id.",
          ".$myroom['hotel_type_room_id'].",
          ".$myroom['recid'].",
          ".$myroom['hotel_room_id'].",
          '".$db_link->escape_string($reservation_status)."',
          '".$dateval."',
          ".$priceperday.")";
          $result = $db_link->query($sql);        
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }    
            
          $d_recid = $db_link->insert_id;    
          $exist_data[$myroom['hotel_room_id']][$dateval] = array('d_recid' => $d_recid,'hascheck' => true);   
          
        }
      }
    }      
  }
  
  
  
  $delete_ids=array();
  foreach ($exist_data as $roomid => $rec) {
    foreach ($rec as $dateval => $rec) {
      if ($rec['hascheck'] == false) {
        $delete_ids[] = $rec['d_recid'];
      }
    }
  } 
  if (count($delete_ids) > 0) {
    $sql="delete from gks_hotel_reservation_room_day where id_hotel_reservation_room_day in (".implode(',', $delete_ids).") and hotel_reservation_id=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }     
    
  }
}

function gks_get_hotels_list($user_id=0) {
  global $my_wp_user_id;
  global $db_link;
  
  if ($user_id==0) $user_id=$my_wp_user_id;
  
  $perm_id_hotel_ids=gks_permission_user_condition($user_id,'gks_hotel','01');
  
  $sql="SELECT gks_hotel.*, gks_eshop_products.product_otherTaxesPercentCategory, gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_poso_fn
  FROM (gks_hotel 
  LEFT JOIN gks_eshop_products ON gks_hotel.hotel_efd_product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_eshop_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron
  where gks_hotel.hotel_disable=0 ";
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel.id_hotel in (".implode(',',$perm_id_hotel_ids).")";
  $sql.=" ORDER BY gks_hotel.hotel_sortorder";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  $ret=array();
  while ($row = $result->fetch_assoc()) {
    $id=$row['id_hotel'];
    $row['hotel_title']=trim_gks($row['hotel_title']);
    
    $aade_katigoria_loipon_foron_poso_fn=trim_gks($row['aade_katigoria_loipon_foron_poso_fn']);
    $efd=0;
    if ($aade_katigoria_loipon_foron_poso_fn!='') {
      $temp=array();
      $temp['product_quantity']=1;
      $efd=call_user_func($aade_katigoria_loipon_foron_poso_fn,$temp);
    }
    
    $ret[$row['id_hotel']]=array(
      'id' => $row['id_hotel'],
      'descr' => $row['hotel_title'],
      'company_id' => $row['company_id'],
      'company_sub_id' => $row['company_sub_id'],
      'aade_katigoria_loipon_foron_poso_fn' => $aade_katigoria_loipon_foron_poso_fn,
      'efd' => $efd,
    ); 
  }
  return $ret;
}



function gks_hotel_reservation_create_acc_pay($old_id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  
  if ($old_id<=0) {
    debug_mail(false,'id is zero',$old_id);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αποθηκεύστε πρώτα την παραγγελία')));
    echo json_encode($return); die(); }   
  

  
  $sql="SELECT gks_hotel_reservation.*, 
  gks_hotel.company_id, gks_hotel.company_sub_id,
  gks_company.company_title, gks_company_subs.company_sub_title, 
  gks_payment_acquirers.payment_acquirer_name, gks_payment_acquirers.show_acc_pay
  FROM (((gks_hotel_reservation 
  LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel) 
  LEFT JOIN gks_company ON gks_hotel.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_hotel.company_sub_id = gks_company_subs.id_company_sub) 
  LEFT JOIN gks_payment_acquirers ON gks_hotel_reservation.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
  WHERE gks_company.id_company>0
  and gks_hotel_reservation.id_hotel_reservation=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'gks_hotel_reservation_create_acc_pay',       gks_lang('Δεν βρέθηκε η κράτηση').'<br>'.gks_lang('Ανανεώστε την σελίδα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η κράτηση').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}
  
  $old_row = $result->fetch_assoc();

  $reservation_status=$old_row['reservation_status'];
  if ($reservation_status!='070wait_payment' and $reservation_status!='080confirm' and $reservation_status!='100completed' and $reservation_status!='110payment') {
    $message=gks_lang('Η κατάσταση της κράτησης είναι').':<br>'.
    '<span class="reservation_status_'.$reservation_status.'">'.getHotelReservationStatusDescr($reservation_status).'</span><br>'.
    gks_lang('ενώ θα πρέπει να είναι').':<br>'.
    '<span class="reservation_status_070wait_payment">'.getHotelReservationStatusDescr('070wait_payment').'</span> '.gks_lang('ή').'<br>'.
    '<span class="reservation_status_080confirm">'.getHotelReservationStatusDescr('080confirm').'</span> '.gks_lang('ή').'<br>'.
    '<span class="reservation_status_100completed">'.getHotelReservationStatusDescr('100completed').'</span> '.gks_lang('ή').'<br>'.
    '<span class="reservation_status_110payment">'.getHotelReservationStatusDescr('110payment').'</span><br>'.
    gks_lang('για να δημιουργηθεί η σχετική πληρωμή');
    
    debug_mail(false,'gks_hotel_reservation_create_acc_pay',       $message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  
  
  //echo '<pre>';echo $old_id;die();
    
  $company_id=$old_row['company_id'];
  $company_title=trim_gks($old_row['company_title']);
  $company_sub_id=$old_row['company_sub_id'];
  $company_sub_title=trim_gks($old_row['company_sub_title']);
  if ($company_sub_title=='') $company_sub_title=gks_lang('Κεντρικό');
  $fiscal_position_id=$old_row['fiscal_position_id'];
  $parastatiko=$old_row['parastatiko'];
  $tropos_pliromis=intval($old_row['tropos_pliromis']);
  $payment_acquirer_name=trim_gks($old_row['payment_acquirer_name']);
  $gks_price_total=$old_row['gks_price_total'];
  $affect_balance_poso=$old_row['affect_balance_poso'];
  $show_acc_pay=$old_row['show_acc_pay'];
  
  if ($show_acc_pay==0) {
    $tropos_pliromis=9;
    $payment_acquirer_name=gks_lang('Μετρητά');
  }
  
  //echo '<pre>';print $old_id;die();
  
  $sql_eidi="SELECT gks_eshop_products.product_base_type, Count(gks_eshop_products.id_product) AS cc
  FROM gks_hotel_reservation_room LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product
  WHERE (((gks_hotel_reservation_room.hotel_reservation_id)=".$old_id."))
  GROUP BY gks_eshop_products.product_base_type;";
  $result_eidi = $db_link->query($sql_eidi);  
  if (!$result_eidi) {
    debug_mail(false,'error sql',$sql_eidi);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $pbasetypes=array();
  $pbasetypes[0]=array('type'=>0, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_pay_acc_journal_id'=>0,'new_pay_acc_seira_id'=>0,'new_pay_acc_seira_code'=>'','error'=>''); //emporevma kai proion pane mazi
  //$pbasetypes[2]=array('type'=>2, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_pay_acc_journal_id'=>0,'new_pay_acc_seira_id'=>0,'new_pay_acc_seira_code'=>'','error'=>''); //ypiresia
  $total_eidi=0;
  while ($row_eidi= $result_eidi->fetch_assoc()) { 
    $total_eidi+=$row_eidi['cc'];
    $pbasetypes[0]['cc']+=$row_eidi['cc'];
  }  
  if ($total_eidi==0) {
    debug_mail(false,'total_eidi is zero',$total_eidi);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν είδη στην παραγγελία')));
    echo json_encode($return); die(); } 
    
  //print '<pre>';print_r($pbasetypes);die();
  
  //print '<pre>';print $parastatiko.'|'.$fiscal_position_id;die();
  
  

  $pbasetypes[0]['id_acc_eidos_parastatikou']=802; //Eispraxeis apo pelates
  
  
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
        $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira, gks_acc_seires.seira_code,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda
        FROM (gks_acc_journal 
        LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id) 
        LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
        WHERE gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou=".$pb['id_acc_eidos_parastatikou']."
        and gks_acc_journal.id_acc_journal>0
        and gks_acc_journal.is_disable=0
        and gks_acc_seires.id_acc_seira>0
        and gks_acc_seires.is_xeirografi=0
        and gks_acc_seires.is_disable=0
        AND gks_acc_journal.company_id=".$company_id." 
        AND gks_acc_journal.company_sub_id=".$company_sub_id;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        if ($result->num_rows==0) {
          $sql="SELECT eidos_parastatikou_descr FROM gks_acc_eidi_parastatikon WHERE id_acc_eidos_parastatikou=".$db_link->escape_string($pb['id_acc_eidos_parastatikou']);
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }  
          if ($result->num_rows>0) {
            $row=$result->fetch_assoc();
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>'.gks_lang('με τύπο παραστατικού').':<br><b>'.$row['eidos_parastatikou_descr'].'</b>';
          } else {
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>'.gks_lang('με ID τύπου παραστατικού').':<br><b>'.$pb['id_acc_eidos_parastatikou'].'</b>';
          }
        } else {
          $row=$result->fetch_assoc();
          $pbasetypes[$i]['new_pay_acc_journal_id']=$row['id_acc_journal'];
          $pbasetypes[$i]['new_pay_acc_seira_id']=$row['id_acc_seira'];
          $pbasetypes[$i]['new_pay_acc_seira_code']=$row['seira_code'];
          $pbasetypes[$i]['has_esoda']=$row['eidos_parastatikou_has_esoda'];
          $pbasetypes[$i]['has_eksoda']=$row['eidos_parastatikou_has_eksoda'];
          
          
        }
      } else {
        $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε ποιο ημερολόγιο θα πρέπει να χρησιμοποιηθεί για αυτήν την λειτουργία');
      }
    }
  }
  
  $errors='';
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['error']!='') {
      $errors.=$pb['error'].'<br><br>';
    }
  } 
  if ($errors!='') {
    $errors=substr($errors, 0, strlen($errors)-8);
    debug_mail(false,'errors',                                     $errors);
    $return = array('success' => false, 'message' => base64_encode($errors));
    echo json_encode($return); die();}
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id; //.'|'.$fiscal_position_id_new; die();
  

  
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
            
        $new_pay_guid=guid_for_acc_pay();
        //echo $new_ooo_guid."\n"; die();
        
        $pay_poso=array();
        $pay_poso[]=array(
          'i'=>$old_id,
          'f'=>'hotel_reservation',
          'v'=>$affect_balance_poso,
        );
        $pay_poso_str=serialize($pay_poso);
        
        $sql="INSERT INTO gks_acc_pay (pay_guid, pay_date, mydate_add, mydate_edit, 
        user_id_add, user_id_edit, myip, 
        pay_acc_journal_id, pay_acc_seira_id, 
        pay_acc_seira_code, pay_state, 
        
        company_id, company_sub_id, user_id,user_notes,note_logistirio,
        affect_balance,affect_balance_all_poso,affect_balance_all_poso_type,affect_balance_poso,
        gks_price_total,pay_poso_str
        )
        SELECT '".$new_pay_guid."' as pay_guid, now() as pay_date, now() as mydate_add, now() as mydate_edit,
        ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
        ".$pbasetypes[$i]['new_pay_acc_journal_id']." as pay_acc_journal_id, ".$pbasetypes[$i]['new_pay_acc_seira_id']." as pay_acc_seira_id, 
        '".$db_link->escape_string($pbasetypes[$i]['new_pay_acc_seira_code'])."' as pay_acc_seira_code, '010draft' as pay_state, 
        
        ".$company_id." as company_id,".$company_sub_id." as company_sub_id, user_id,user_notes,note_logistirio,
        1 as affect_balance,1 as affect_balance_all_poso,'price_total' as affect_balance_all_poso_type,affect_balance_poso,
        ".number_format($affect_balance_poso,10, '.','').",
        '".$db_link->escape_string($pay_poso_str)."'
        FROM gks_hotel_reservation
        WHERE id_hotel_reservation=".$old_id;
        
         
        
        //echo '<pre>';echo $sql;die();
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        $new_id = $db_link->insert_id;  
        //echo '<pre>'.$new_id."\n";die();
        
        

        $sql="insert into gks_acc_pay_method (
          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
          acc_pay_id,paymethod_aa,paymethod_id,
          paymethod_total,paymethod_descr,paymethod_comments
        ) values (
          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
          ".$new_id.",
          1,
          ".$tropos_pliromis.",
          ".number_format($affect_balance_poso,10, '.','').",
          '".$db_link->escape_string($payment_acquirer_name)."',
          ''
        )";
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        $new_product_id = $db_link->insert_id;
        
                    //echo print_r($map_products,true)."\n";die();
        
        
        
        
        
        
        $sxolio=gks_lang('Προσθήκη από backend, δημιουργία από κράτηση με ID').' #<a href="admin-hotel-reservation-item.php?id='.$old_id.'">'.$old_id.'</a>'; 
        $sql="insert into gks_acc_pay_log (acc_pay_id, add_date,user_id,sxolio) values (
        ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
     
        
        $pbasetypes[$i]['new_id']=$new_id;
        
        $sql="insert into gks_object_rel (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        object_name1,object_id1,object_name2,object_id2
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        'gks_hotel_reservation',".$old_id.",'gks_acc_pay',".$new_id."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        
      }
    } 
  }
  
  $ret=array();
  foreach ($pbasetypes as $i => $pb) {
    if (isset($pb['new_id']) and $pb['new_id']>0) $ret[]=$pb['new_id'];
  } 
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  return $ret;
  
}

function gks_hotel_reservation_create_acc_inv($old_id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_LANG_DATA_ENABLED;
  
  if ($old_id<=0) {
    debug_mail(false,'id is zero',$old_id);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Αποθηκεύστε πρώτα την παραγγελία')));
    echo json_encode($return); die(); }   
  

  
  $sql="SELECT gks_hotel_reservation.*, 
  gks_hotel.company_id, gks_hotel.company_sub_id,
  gks_company.company_title, gks_company_subs.company_sub_title, 
  gks_payment_acquirers.payment_acquirer_name, gks_payment_acquirers.show_acc_pay
  FROM (((gks_hotel_reservation 
  LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel) 
  LEFT JOIN gks_company ON gks_hotel.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_hotel.company_sub_id = gks_company_subs.id_company_sub) 
  LEFT JOIN gks_payment_acquirers ON gks_hotel_reservation.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
  WHERE gks_company.id_company>0
  and gks_hotel_reservation.id_hotel_reservation=".$old_id;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  if ($result->num_rows==0) {
    debug_mail(false,'gks_hotel_reservation_create_acc_inv',       gks_lang('Δεν βρέθηκε η κράτηση').'<br>'.gks_lang('Ανανεώστε την σελίδα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η κράτηση').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();}
  
  
  
  $old_row = $result->fetch_assoc();
  $id_hotel=$old_row['hotel_id'];
  $check_in=$old_row['check_in'];
  $check_out=$old_row['check_out'];
  $num_days=$old_row['num_days'];
  $user_lang=$old_row['user_lang'];
  
  $hotel_params=gks_hotel_get_params($id_hotel);
  
  //echo '<pre>'.$hotel_params['hotel_efd_product_id'];die();
  
  $reservation_status=$old_row['reservation_status'];
  if ($reservation_status!='070wait_payment' and $reservation_status!='080confirm' and $reservation_status!='100completed' and $reservation_status!='110payment') {
     $message=gks_lang('Η κατάσταση της κράτησης είναι').':<br>'.
    '<span class="reservation_status_'.$reservation_status.'">'.getHotelReservationStatusDescr($reservation_status).'</span><br>'.
    gks_lang('ενώ θα πρέπει να είναι').':<br>'.
    '<span class="reservation_status_070wait_payment">'.getHotelReservationStatusDescr('070wait_payment').'</span> '.gks_lang('ή').'<br>'.
    '<span class="reservation_status_080confirm">'.getHotelReservationStatusDescr('080confirm').'</span> '.gks_lang('ή').'<br>'.
    '<span class="reservation_status_100completed">'.getHotelReservationStatusDescr('100completed').'</span> '.gks_lang('ή').'<br>'.
    '<span class="reservation_status_110payment">'.getHotelReservationStatusDescr('110payment').'</span><br>'.
    gks_lang('για να δημιουργηθoύν τα σχετικά παραστατικά');
    
    debug_mail(false,'gks_hotel_reservation_create_acc_inv',                  $message);
    $return = array('success' => false, 'message' => base64_encode($message));
    echo json_encode($return); die();}
  
  
  //echo '<pre>';echo $old_id;die();
    
  $company_id=$old_row['company_id'];
  $company_title=trim_gks($old_row['company_title']);
  $company_sub_id=$old_row['company_sub_id'];
  $company_sub_title=trim_gks($old_row['company_sub_title']);
  if ($company_sub_title=='') $company_sub_title=gks_lang('Κεντρικό');
  $fiscal_position_id=$old_row['fiscal_position_id'];
  $parastatiko=$old_row['parastatiko'];
  
  $sql_eidi="SELECT gks_eshop_products.product_base_type, Count(gks_eshop_products.id_product) AS cc
  FROM gks_hotel_reservation_room LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product
  WHERE (((gks_hotel_reservation_room.hotel_reservation_id)=".$old_id."))
  GROUP BY gks_eshop_products.product_base_type;";
  $result_eidi = $db_link->query($sql_eidi);  
  if (!$result_eidi) {
    debug_mail(false,'error sql',$sql_eidi);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $pbasetypes=array();
  $pbasetypes[0]=array('type'=>0, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_inv_acc_journal_id'=>0,'new_inv_acc_seira_id'=>0,'new_inv_acc_seira_code'=>'','error'=>''); //emporevma kai proion pane mazi
  $pbasetypes[2]=array('type'=>2, 'cc'=>0, 'id_acc_eidos_parastatikou' => 0,'new_inv_acc_journal_id'=>0,'new_inv_acc_seira_id'=>0,'new_inv_acc_seira_code'=>'','error'=>''); //ypiresia
  $pbasetypes[100]=array('type'=>100, 'cc'=>0, 'id_acc_eidos_parastatikou' => 82,'new_inv_acc_journal_id'=>0,'new_inv_acc_seira_id'=>0,'new_inv_acc_seira_code'=>'','error'=>''); //Eidiko Stoicheio - Apodeixis Eispraxis Forou Diamonis
  
  $total_eidi=0;
  while ($row_eidi= $result_eidi->fetch_assoc()) { 
    $total_eidi+=$row_eidi['cc'];
    if ($row_eidi['product_base_type']==0 or $row_eidi['product_base_type']==1) $pbasetypes[0]['cc']+=$row_eidi['cc'];
    if ($row_eidi['product_base_type']==2) $pbasetypes[2]['cc']+=$row_eidi['cc'];
    $pbasetypes[100]['cc']+=$row_eidi['cc'];
  }  
  if ($total_eidi==0) {
    debug_mail(false,'total_eidi is zero',$total_eidi);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν είδη στην παραγγελία')));
    echo json_encode($return); die(); } 
    
  //print '<pre>';print_r($pbasetypes);die();
  
  //print '<pre>';print $parastatiko.'|'.$fiscal_position_id;die();
  
  $fiscal_position_id_new=$fiscal_position_id;

  
  if ($parastatiko==0) { //apodiji
    if ($pbasetypes[0]['cc']>0) {
      $pbasetypes[0]['id_acc_eidos_parastatikou']=111; //ALP
    }
    if ($pbasetypes[2]['cc']>0) {
      $pbasetypes[2]['id_acc_eidos_parastatikou']=112; //APY  
    }
    switch ($fiscal_position_id) {
      case 1:	break; // Lianikis Esoterikou
      case 2:	break; // Lianikis Endokoinotikes
      case 3:	break; // Lianikis Trites Chores
      case 11:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou
      case 12:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou (syndedemenes ontotites)
      case 21:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou Meiomeno
      case 22:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou Meiomeno (syndedemenes ontotites)
      case 31:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou Apallagis
      case 32:	$fiscal_position_id_new=1; break; //	Chondrikis Esoterikou Apallagis (syndedemenes ontotites)
      case 41:	$fiscal_position_id_new=2; break; //	Chondrikis Endokoinotikes
      case 42:	$fiscal_position_id_new=2; break; //	Chondrikis Endokoinotikes (syndedemenes ontotites)
      case 51:	$fiscal_position_id_new=3; break; //	Chondrikis Trites Chores
      case 52:	$fiscal_position_id_new=3; break; //	Chondrikis Trites Chores (syndedemenes ontotites)
    }
  
  } else { //timologio
    
    if ($pbasetypes[0]['cc']>0) {
      switch ($fiscal_position_id) {
        case 1:	 $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //Lianikis Esoterikou
        case 2:	 $pbasetypes[0]['id_acc_eidos_parastatikou']=12; break; //Lianikis Endokoinotikes
        case 3:	 $pbasetypes[0]['id_acc_eidos_parastatikou']=13; break; //Lianikis Trites Chores
        case 11: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou
        case 12: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou (syndedemenes ontotites)
        case 21: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou Meiomeno
        case 22: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou Meiomeno (syndedemenes ontotites)
        case 31: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou Apallagis
        case 32: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Esoterikou Apallagis (syndedemenes ontotites)
        case 41: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Endokoinotikes
        case 42: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Endokoinotikes (syndedemenes ontotites)
        case 51: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Trites Chores
        case 52: $pbasetypes[0]['id_acc_eidos_parastatikou']=11; break; //	Chondrikis Trites Chores (syndedemenes ontotites)
      }    
    }
    if ($pbasetypes[2]['cc']>0) {
      switch ($fiscal_position_id) {
        case 1:	 $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //Lianikis Esoterikou
        case 2:	 $pbasetypes[2]['id_acc_eidos_parastatikou']=22; break; //Lianikis Endokoinotikes
        case 3:	 $pbasetypes[2]['id_acc_eidos_parastatikou']=23; break; //Lianikis Trites Chores
        case 11: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou
        case 12: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou (syndedemenes ontotites)
        case 21: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou Meiomeno
        case 22: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou Meiomeno (syndedemenes ontotites)
        case 31: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou Apallagis
        case 32: $pbasetypes[2]['id_acc_eidos_parastatikou']=21; break; //	Chondrikis Esoterikou Apallagis (syndedemenes ontotites)
        case 41: $pbasetypes[2]['id_acc_eidos_parastatikou']=22; break; //	Chondrikis Endokoinotikes
        case 42: $pbasetypes[2]['id_acc_eidos_parastatikou']=22; break; //	Chondrikis Endokoinotikes (syndedemenes ontotites)
        case 51: $pbasetypes[2]['id_acc_eidos_parastatikou']=23; break; //	Chondrikis Trites Chores
        case 52: $pbasetypes[2]['id_acc_eidos_parastatikou']=23; break; //	Chondrikis Trites Chores (syndedemenes ontotites)
      }    

    }    

    switch ($fiscal_position_id) {
      case 1:	$fiscal_position_id_new=11; break; //Lianikis Esoterikou
      case 2:	$fiscal_position_id_new=11; break; //Lianikis Endokoinotikes
      case 3:	$fiscal_position_id_new=51; break; //Lianikis Trites Chores
      case 11:	break; //	Chondrikis Esoterikou
      case 12:	break; //	Chondrikis Esoterikou (syndedemenes ontotites)
      case 21:	break; //	Chondrikis Esoterikou Meiomeno
      case 22:	break; //	Chondrikis Esoterikou Meiomeno (syndedemenes ontotites)
      case 31:	break; //	Chondrikis Esoterikou Apallagis
      case 32:	break; //	Chondrikis Esoterikou Apallagis (syndedemenes ontotites)
      case 41:	break; //	Chondrikis Endokoinotikes
      case 42:	break; //	Chondrikis Endokoinotikes (syndedemenes ontotites)
      case 51:	break; //	Chondrikis Trites Chores
      case 52:	break; //	Chondrikis Trites Chores (syndedemenes ontotites)
    }    
  }
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
        $sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_seires.id_acc_seira, gks_acc_seires.seira_code,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda,
        gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda
        FROM (gks_acc_journal 
        LEFT JOIN gks_acc_seires ON gks_acc_journal.id_acc_journal = gks_acc_seires.acc_journal_id) 
        LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
        WHERE gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou=".$pb['id_acc_eidos_parastatikou']."
        and gks_acc_journal.id_acc_journal>0
        and gks_acc_journal.is_disable=0
        and gks_acc_seires.id_acc_seira>0
        and gks_acc_seires.is_xeirografi=0
        and gks_acc_seires.is_disable=0
        AND gks_acc_journal.company_id=".$company_id." 
        AND gks_acc_journal.company_sub_id=".$company_sub_id;
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        if ($result->num_rows==0) {
          $sql="SELECT eidos_parastatikou_descr FROM gks_acc_eidi_parastatikon WHERE id_acc_eidos_parastatikou=".$db_link->escape_string($pb['id_acc_eidos_parastatikou']);
          $result = $db_link->query($sql);  
          if (!$result) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            echo json_encode($return); die(); }  
          if ($result->num_rows>0) {
            $row=$result->fetch_assoc();
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>με τύπο παραστατικού:<br><b>'.$row['eidos_parastatikou_descr'].'</b>';
          } else {
            $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε σχετικό ημερολόγιο ή/και σειρά για την εταιρεία').'<br><b>'.$company_title.'/'.$company_sub_title.'</b><br>με ID τύπου παραστατικού:<br><b>'.$pb['id_acc_eidos_parastatikou'].'</b>';
          }
        } else {
          $row=$result->fetch_assoc();
          $pbasetypes[$i]['new_inv_acc_journal_id']=$row['id_acc_journal'];
          $pbasetypes[$i]['new_inv_acc_seira_id']=$row['id_acc_seira'];
          $pbasetypes[$i]['new_inv_acc_seira_code']=$row['seira_code'];
          $pbasetypes[$i]['has_esoda']=$row['eidos_parastatikou_has_esoda'];
          $pbasetypes[$i]['has_eksoda']=$row['eidos_parastatikou_has_eksoda'];
          
          
        }
      } else {
        $pbasetypes[$i]['error']=gks_lang('Δεν βρέθηκε ποιο παραστατικό θα πρέπει να χρησιμοποιηθεί για αυτήν την λειτουργία');
      }
    }
  }
  
  $errors='';
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['error']!='') {
      $errors.=$pb['error'].'<br><br>';
    }
  } 
  if ($errors!='') {
    $errors=substr($errors, 0, strlen($errors)-8);
    debug_mail(false,'errors',                                     $errors);
    $return = array('success' => false, 'message' => base64_encode($errors));
    echo json_encode($return); die();}
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();
  
  //echo $new_inv_acc_journal_id.'|'.$new_inv_acc_seira_id.'|'.$new_inv_acc_seira_code."\n"; die();
  //foreach ($pbasetypes as $i => $pb) {
  
  $add_efd=true;
  
  foreach ($pbasetypes as $i => $pb) {
    if ($pb['cc']>0) {
      if ($pb['id_acc_eidos_parastatikou']!=0) {
            
        $new_inv_guid=guid_for_acc_inv();
        //echo $new_inv_guid."\n"; die();
        
        $sql="INSERT INTO gks_acc_inv (inv_guid, inv_date, mydate_add, mydate_edit, 
        user_id_add, user_id_edit, myip, 
        inv_acc_journal_id, inv_acc_seira_id, 
        inv_acc_seira_code, inv_state, 
        
        company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos,ma_arithmos,
        ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
        address_extra, destination_data_name, destination_data_phone, destination_data_odos, destination_data_arithmos, destination_data_orofos, destination_data_perioxi, destination_data_poli, destination_data_tk,
        destination_data_country_id, destination_data_nomos_id, destination_data_apostoli_number, user_notes, 
        fiscal_position_id, pricelist_id, is_other, other_first_name,
        other_last_name, other_email, other_mobile, other_lang, other_ma_odos,  other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
        other_ma_nomos_id, 
        products_need_apostoli, products_need_pliromi, kostos_apostolis, tropos_apostolis, tropos_apostolis_json,
        kostos_pliromis, tropos_pliromis, kostos_pliromis_json, delivery_id_8, coupons, def_ekptosi,
        note_logistirio,
        delivery_number,vehicle_number,dispatch_date,
        warehouses_id_from,warehouses_id_to,
        affect_balance,affect_balance_all_poso,affect_balance_all_poso_type,affect_balance_poso
        )
        SELECT '".$new_inv_guid."' as inv_guid, now() as inv_date, now() as mydate_add, now() as mydate_edit,
        ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit, '".$db_link->escape_string($gkIP)."' as myip, 
        ".$pbasetypes[$i]['new_inv_acc_journal_id']." as inv_acc_journal_id, ".$pbasetypes[$i]['new_inv_acc_seira_id']." as inv_acc_seira_id, 
        '".$db_link->escape_string($pbasetypes[$i]['new_inv_acc_seira_code'])."' as inv_acc_seira_code, '010draft' as inv_state, 
        
        company_id, company_sub_id, user_id, user_email, user_first_name, user_last_name, user_mobile, user_lang, eponimia, title, afm, doy, epaggelma, ma_odos, ma_arithmos,
        ma_orofos, ma_perioxi, ma_poli, ma_tk, ma_country_id, ma_nomos_id,
        -1 as address_extra, '' as destination_data_name, '' as destination_data_phone, '' as destination_data_odos, '' as destination_data_arithmos, '' as destination_data_orofos, '' as destination_data_perioxi, '' as destination_data_poli, '' as destination_data_tk,
        0 as destination_data_country_id, 0 as destination_data_nomos_id, '' as destination_data_apostoli_number, user_notes, 
        fiscal_position_id, pricelist_id, is_other, other_first_name,
        other_last_name, other_email, other_mobile, other_lang, other_ma_odos, other_ma_arithmos, other_ma_orofos, other_ma_perioxi, other_ma_poli, other_ma_tk, other_ma_country_id,
        other_ma_nomos_id, 
        products_need_apostoli, products_need_pliromi, kostos_apostolis, 1 as tropos_apostolis, '' as tropos_apostolis_json,
        kostos_pliromis, tropos_pliromis, kostos_pliromis_json, delivery_id_8, coupons, def_ekptosi,
        note_logistirio,
        delivery_number,'' as vehicle_number,null as dispatch_date,
        0 as warehouses_id_from,0 as warehouses_id_to,
        ".($i==100 ? '1' : '0')." as affect_balance,1 as affect_balance_all_poso,'price_total' as affect_balance_all_poso_type,affect_balance_poso
        
        FROM gks_hotel_reservation
        LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel
        WHERE id_hotel_reservation=".$old_id;
        
        //echo '<pre>';echo $sql; die();
        /*
        idiotites,
        products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
        gks_price_original_net, gks_price_net, gks_price_fpa, gks_price_netfpa, gks_price_total, 
        totalWithheldAmount, totalOtherTaxesAmount, totalStampDutyamount, totalFeesAmount, totalDeductionsAmount,
        
        
        idiotites,
        products_posotita, products_varos, products_ogos, products_ogos_max_x, products_ogos_max_y, products_ogos_max_z,
        gks_price_original_net, gks_price_net, gks_price_fpa, gks_price_netfpa, gks_price_total, 
        totalWithheldAmount, totalOtherTaxesAmount, totalStampDutyamount, totalFeesAmount, 0 as totalDeductionsAmount,
        
        */
        
        //echo '<pre>';echo $sql;die();
        
        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        $new_id = $db_link->insert_id;  
        //echo $new_id."\n";die();
        
        
//        $sql="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.warehouses_id_from, gks_acc_inv.warehouses_id_to, 
//        gks_acc_journal.acc_eidos_parastatikou_whi_id, gks_acc_eidi_parastatikon.eidos_parastatikou_type_id,
//        gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros,
//        gks_acc_inv.company_id,gks_acc_inv.company_sub_id
//        FROM (gks_acc_inv 
//        LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
//        LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
//        where gks_acc_inv.id_acc_inv=".$new_id;
//        
//        
//        $result = $db_link->query($sql);  
//        if (!$result) {
//          debug_mail(false,'error sql',$sql);
//          $return = array('success' => false, 'message' => base64_encode('sql error'));
//          echo json_encode($return); die(); }  
//        $added_row = $result->fetch_assoc();
//        $change_warehouses=false;
//        if (($added_row['warehouses_id_from']==0 or $added_row['warehouses_id_to']==0) and $added_row['acc_eidos_parastatikou_whi_id']>0) {
//          
//          $whi_eidos_parastatikou_type_id_org=intval($added_row['eidos_parastatikou_type_id']);
//          $whi_eidos_parastatikou_stock_pros_org=intval($added_row['eidos_parastatikou_stock_pros']);
//          $warehouses_id_from=$added_row['warehouses_id_from'];
//          $warehouses_id_to=  $added_row['warehouses_id_to'];
//          if ($whi_eidos_parastatikou_type_id_org==24) { //apografi
//            $warehouses_id_from=0;  
//        //    $aade_skopos_diakinisis_id=0;
//        //    $pricelist_id=0;
//        //    $fiscal_position_id=0;
//        //    $tropos_apostolis=1; //Den apaiteitai apostoli
//            
//          } else if ($whi_eidos_parastatikou_type_id_org==23) { //endodiakinisi
//            
//          } else {
//            if ($whi_eidos_parastatikou_stock_pros_org==1) { //erxete, auksanei to ypoloipo stock
//              if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
//                $warehouses_id_from=1; //Eikoniki Apothiki Pelaton
//                $warehouses_id_from_is_virtual=true;
//              } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheftes
//                $warehouses_id_from=2; //Eikoniki Apothiki Promithefton
//                $warehouses_id_from_is_virtual=true;
//              }
//            } else if ($whi_eidos_parastatikou_stock_pros_org==-1) { //feuvei, meionete to ypoloipo stock
//              if ($whi_eidos_parastatikou_type_id_org==21) { //pelates
//                $warehouses_id_to=1; //Eikoniki Apothiki Pelaton
//                $warehouses_id_to_is_virtual=true;
//              } else if ($whi_eidos_parastatikou_type_id_org==22) { //promitheftes
//                $warehouses_id_to=2; //Eikoniki Apothiki Promithefton
//                $warehouses_id_to_is_virtual=true;
//              }
//            }
//          }
//          
//          //echo '<pre>'.$sql."\n".$warehouses_id_from."\n".$warehouses_id_to;die();
//          
//          if ($warehouses_id_from==0 or $warehouses_id_to==0) {
//            $sql="select id_warehouse from gks_warehouses where is_virtual=0 and warehouse_disable=0
//            and company_id=".$added_row['company_id']."
//            and company_sub_id=".$added_row['company_sub_id']."
//            order by warehouse_sortorder limit 1";
//            $result = $db_link->query($sql);  
//            if (!$result) {
//              debug_mail(false,'error sql',$sql);
//              $return = array('success' => false, 'message' => base64_encode('sql error'));
//              echo json_encode($return); die(); }  
//            if ($result->num_rows==1) {
//              $row = $result->fetch_assoc();
//              if ($warehouses_id_from==0) $warehouses_id_from=$row['id_warehouse'];
//              if ($warehouses_id_to==0)   $warehouses_id_to=$row['id_warehouse'];
//              $change_warehouses=true;
//              
//              $sql="update gks_acc_inv set 
//              warehouses_id_from=".$warehouses_id_from.",
//              warehouses_id_to=".$warehouses_id_to."
//              where gks_acc_inv.id_acc_inv=".$new_id;
//              $result = $db_link->query($sql);  
//              if (!$result) {
//                debug_mail(false,'error sql',$sql);
//                $return = array('success' => false, 'message' => base64_encode('sql error'));
//                echo json_encode($return); die(); }  
//              
//            }
//          }
//            
//          //echo '<pre>'.$sql."\n".$warehouses_id_from."\n".$warehouses_id_to;die();
//          
//          
//          //$return = array('success' => false, 'message' => base64_encode('|'.$change_warehouses.'|'.$warehouses_id_from.'|'.$warehouses_id_to.'|'));
//          //echo json_encode($return); die();
//          
//        }
        
        
        
        $sql="SELECT gks_hotel_reservation_room.id_hotel_reservation_room, gks_eshop_products.product_base_type, gks_eshop_products.id_product, 
        gks_eshop_products.product_parent_id, gks_eshop_products.product_class, gks_hotel_reservation_room.hotel_reservation_id
        FROM gks_hotel_reservation_room 
        LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product
        WHERE gks_hotel_reservation_room.hotel_reservation_id=".$old_id."
        order by gks_hotel_reservation_room.id_hotel_reservation_room";

        $result = $db_link->query($sql);  
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        $old_product_ids=array();
        while ($row = $result->fetch_assoc()) {  
          $old_product_ids[]=array(
            'id' => $row['id_hotel_reservation_room'], 
            'type' => $row['product_base_type'],
            'id_product' => $row['id_product'],
            'product_parent_id' => $row['product_parent_id'],
            'product_class' => $row['product_class'],
          );
        }
        
        $map_products=array();
        $product_aa=0;
        foreach ($old_product_ids as $vid) {
          if ((($vid['type']==0 or $vid['type']==1) and ($i==0 or $i==1)) or 
               ($vid['type']==2 and $i==2)  or
               ($i==100)  ) {
            
            $product_aa++;
            
            $sql="SELECT gks_hotel_reservation_room.id_hotel_reservation_room, gks_hotel_reservation_room.product_quantity, 
            gks_hotel_reservation_room.rnum_adults, gks_hotel_reservation_room.rnum_childs, 
            gks_hotel_reservation_room.rnum_child_kounies, gks_hotel_reservation_room.rnum_extra_beds, 
            gks_hotel_room.room_descr, gks_hotel_room_en_US.room_descr_en_US, 
            gks_hotel_room_type.room_type_descr,gks_hotel_room_type_en_US.room_type_descr_en_US
            FROM (((gks_hotel_reservation_room 
            LEFT JOIN gks_hotel_room ON gks_hotel_reservation_room.hotel_room_id = gks_hotel_room.id_hotel_room) 
            LEFT JOIN (
              SELECT hotel_room_id, room_descr as room_descr_en_US FROM gks_hotel_room_lang WHERE lang_code='en-US'
            ) AS gks_hotel_room_en_US ON gks_hotel_room.id_hotel_room = gks_hotel_room_en_US.hotel_room_id)
            LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type)
            LEFT JOIN (
              SELECT hotel_room_type_id, room_type_descr as room_type_descr_en_US FROM gks_hotel_room_type_lang WHERE lang_code='en-US'
            ) AS gks_hotel_room_type_en_US ON gks_hotel_room_type.id_hotel_room_type = gks_hotel_room_type_en_US.hotel_room_type_id
            
            where id_hotel_reservation_room=".$vid['id'];
            $result = $db_link->query($sql);  
            if (!$result) {debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }
            if ($result->num_rows==0) {debug_mail(false,                     gks_lang('Δεν βρέθηκε το είδος για την Είσπραξη Φόρου Διαμονής του ξενοδοχείου'),$sql);
              $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το είδος για την Είσπραξη Φόρου Διαμονής του ξενοδοχείου')));
              echo json_encode($return); die(); }
              
            $row = $result->fetch_assoc();
            $product_quantity=$row['product_quantity'];
//            $rnum_adults=intval($row['rnum_adults']);
//            $rnum_childs=intval($row['rnum_childs']);
//            $rnum_child_kounies=intval($row['rnum_child_kounies']);
//            $rnum_extra_beds=intval($row['rnum_extra_beds']);
//            $room_descr=trim_gks($row['room_descr']);
//            $room_descr_en_US=trim_gks($row['room_descr_en_US']);
//            $room_type_descr=trim_gks($row['room_type_descr']);
//            $room_type_descr_en_US=trim_gks($row['room_type_descr_en_US']);


            $in_array=array();
            $in_array['room_name']=array('value' => gks_print_isset_s($row['room_descr']),'type' => 's');
            $in_array['room_name_en']=array('value' => gks_print_isset_s($row['room_descr_en_US']),'type' => 's');
            $in_array['room_type']=array('value' => gks_print_isset_s($row['room_type_descr']),'type' => 's');
            $in_array['room_type_en']=array('value' => gks_print_isset_s($row['room_type_descr_en_US']),'type' => 's');

            $in_array['check_in']=    array('value' => myDateTimeFormat(strtotime($check_in)),'type' => 's');
            $in_array['check_in_d']=  array('value' => myDateFormat(strtotime($check_in)),'type' => 's');
            $in_array['check_in_dw']= array('value' => myDateFormatw(strtotime($check_in)),'type' => 's');
            $in_array['check_in_dt']= array('value' => myDateTimeFormat(strtotime($check_in)),'type' => 's');
            $in_array['check_in_dtw']=array('value' => myDateTimeFormatw(strtotime($check_in)),'type' => 's');
            
            $in_array['check_out']=    array('value' => myDateTimeFormat(strtotime($check_out)),'type' => 's');
            $in_array['check_out_d']=  array('value' => myDateFormat(strtotime($check_out)),'type' => 's');
            $in_array['check_out_dw']= array('value' => myDateFormatw(strtotime($check_out)),'type' => 's');
            $in_array['check_out_dt']= array('value' => myDateTimeFormat(strtotime($check_out)),'type' => 's');
            $in_array['check_out_dtw']=array('value' => myDateTimeFormatw(strtotime($check_out)),'type' => 's');
            
            $in_array['days']=array('value' => $num_days,'type' => 'n');
            $in_array['adults']=array('value' => gks_print_isset_n($row['rnum_adults']),'type' => 'n');
            $in_array['childs']=array('value' => gks_print_isset_n($row['rnum_childs']),'type' => 'n');
            $in_array['visitors']=array('value' => ($row['rnum_adults'] + $row['rnum_childs']),'type' => 'n');
            $in_array['child_kounies']=array('value' => gks_print_isset_n($row['rnum_child_kounies']),'type' => 'n');
            $in_array['extra_beds']=array('value' => gks_print_isset_n($row['rnum_extra_beds']),'type' => 'n');
            
            //print '<pre>';print_r($hotel_params);die();
            //print '<pre>uuuuu '.$user_lang.' ';print_r($row);die();
            //print '<pre>';print_r($GKS_LANG_DATA_ARRAY);die();
            
            
            if ($i==100) {
              $text_room_template=$hotel_params['hotel_template_efd_descr'];
            } else {
              $text_room_template=$hotel_params['hotel_template_eidos_descr'];
            }
            if ($user_lang!='el-GR') {
              $found_lang=false;
              foreach ($GKS_LANG_DATA_ENABLED as $val_lang) {
                if ($val_lang==$user_lang) {
                  $sql_lang="select * from gks_hotel_lang
                  where hotel_id=".$id_hotel."
                  and lang_code='".$db_link->escape_string($val_lang)."'";
                  $result_lang = $db_link->query($sql_lang);  
                  if (!$result_lang) {debug_mail(false,'error sql',$sql_lang);
                    $return = array('success' => false, 'message' => base64_encode('sql error'));
                    echo json_encode($return); die(); }
                  if ($result_lang->num_rows==1) {
                    $row_lang = $result_lang->fetch_assoc();
                    if ($i==100) {
                      $temp=trim_gks($row_lang['hotel_template_efd_descr']);
                      if ($temp!='') {
                        $text_room_template=$temp;
                        $found_lang=true;
                      }
                    } else {
                      $temp=trim_gks($row_lang['hotel_template_eidos_descr']);
                      if ($temp!='') {
                        $text_room_template=$temp;
                        $found_lang=true;
                      }
                    }
                  }
                }
                
              } 
              foreach ($GKS_LANG_DATA_ENABLED as $val_lang) {
                if ($val_lang=='en-US' and $found_lang==false) {
                  $sql_lang="select * from gks_hotel_lang
                  where hotel_id=".$id_hotel."
                  and lang_code='en-US'";
                  $result_lang = $db_link->query($sql_lang);  
                  if (!$result_lang) {debug_mail(false,'error sql',$sql_lang);
                    $return = array('success' => false, 'message' => base64_encode('sql error'));
                    echo json_encode($return); die(); }
                  if ($result_lang->num_rows==1) {
                    $row_lang = $result_lang->fetch_assoc();
                    if ($i==100) {
                      $temp=trim_gks($row_lang['hotel_template_efd_descr']);
                      if ($temp!='') {
                        $text_room_template=$temp;
                      }
                    } else {
                      $temp=trim_gks($row_lang['hotel_template_eidos_descr']);
                      if ($temp!='') {
                        $text_room_template=$temp;
                      }
                    }
                  }                  
                }
              } 
              //print '<pre>';print_r($GKS_LANG_DATA_ENABLED);die();
            }
            
            
            //print '<pre>'.$text_room_template;die();

            
            $mc=gks_print_form_mc($text_room_template);
            $tr_m= array('html' => $text_room_template, 'mc' => $mc, 'tr_hide'=> false);
            $text_room=gks_print_form_replace_field($tr_m,$in_array);

            
            $text_room=str_replace('[[data]]', '', $text_room);
            $text_room=str_replace('{hide}',   '', $text_room);
           
            $text_room=str_replace("\r\n\r\n","\r\n", $text_room);
            $text_room=str_replace("\r\n\r\n","\r\n", $text_room);
            $text_room=str_replace("\r\n\r\n","\r\n", $text_room);
            $text_room=str_replace("\r\n\r\n","\r\n", $text_room);
            if (endwith($text_room,"\r\n")) $text_room=substr($text_room,0, strlen($text_room)-2);
            
            
            //print '<pre>';print $html_out;die();
            //s -> string
            //h -> html
            //n -> number int
            //nl -> number myNumberFormatNo0Local
            //c -> Currency
              //cs -> Currency + symbol
                
            
//      
//            $text_room=$text_room_template;
//            $text_room=str_replace('{room_name}', $room_descr, $text_room);
//            $text_room=str_replace('{room_type}', $room_type_descr, $text_room);
//            $text_room=str_replace('{check_in}', myDateTimeFormatw(strtotime($check_in)), $text_room);
//            $text_room=str_replace('{check_out}', myDateTimeFormatw(strtotime($check_out)), $text_room);
//            $text_room=str_replace('{days}', $num_days, $text_room);
//            $text_room=str_replace('{adults}', $rnum_adults, $text_room);
//            $text_room=str_replace('{childs}', $rnum_childs, $text_room);
//            $text_room=str_replace('{visitors}', ($rnum_adults + $rnum_childs), $text_room);
//            $text_room=str_replace('{child_kounies}', $rnum_child_kounies, $text_room);
//            $text_room=str_replace('{extra_beds}', $rnum_extra_beds, $text_room);
            
            
            
            
            
            
            
            if ($i==100) {
              if ($add_efd) {
                //Eispraxis Forou Diamonis
                $product_id = $hotel_params['hotel_efd_product_id'];
                
                $sql="select product_parent_id,product_class,product_descr,product_fpa_base_id,product_otherTaxesPercentCategory 
                from gks_eshop_products where id_product=".$product_id;
                $result = $db_link->query($sql);  
                if (!$result) {debug_mail(false,'error sql',$sql);
                  $return = array('success' => false, 'message' => base64_encode('sql error'));
                  echo json_encode($return); die(); }
                if ($result->num_rows==0) {debug_mail(false,                     gks_lang('Δεν βρέθηκε το είδος για την Είσπραξη Φόρου Διαμονής του ξενοδοχείου'),$sql);
                  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το είδος για την Είσπραξη Φόρου Διαμονής του ξενοδοχείου')));
                  echo json_encode($return); die(); }
                $row = $result->fetch_assoc();
                $product_descr=trim_gks($row['product_descr']);
                $product_fpa_base_id=intval($row['product_fpa_base_id']);
                $product_otherTaxesPercentCategory=intval($row['product_otherTaxesPercentCategory']);
                
                $vid['id_product']=$product_id;
                $vid['product_parent_id']=intval($row['product_parent_id']);
                $vid['product_class']=trim_gks($row['product_class']);
              
                              
                $sql="select * from gks_aade_katigoria_loipon_foron 
                where id_aade_katigoria_loipon_foron in (6,7,8,9,10)
                and id_aade_katigoria_loipon_foron=".$product_otherTaxesPercentCategory;
                $result = $db_link->query($sql);  
                if (!$result) {debug_mail(false,'error sql',$sql);
                  $return = array('success' => false, 'message' => base64_encode('sql error'));
                  echo json_encode($return); die(); }
                if ($result->num_rows==0) {debug_mail(false,                     gks_lang('Δεν βρέθηκαν οι <b>Λοιποί Φόροι</b> για το είδος').' <b>'.$product_descr.'</b>',$sql);
                  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκαν οι <b>Λοιποί Φόροι</b> για το είδος').' <b>'.$product_descr.'</b>'));
                  echo json_encode($return); die(); }
                $row = $result->fetch_assoc();
                
                
                $aade_katigoria_loipon_foron_poso_fn=trim_gks($row['aade_katigoria_loipon_foron_poso_fn']);
                
                

                
                $value_product=array();
                $value_product['product_quantity']=$product_quantity;
                $product_otherTaxesAmount = call_user_func_array($aade_katigoria_loipon_foron_poso_fn,array($value_product));
                
                
//                product_is_digital,product_is_simple_download,product_need_apostoli,
//                product_fpa_id,product_fpa_ejeresi_id,product_fpa_pososto,product_fpa_id_json,
//                product_price_check_fpa,product_price_include_vat,
//                product_price_start_peritem_db,product_price_start_peritem_net,product_price_start_peritem_fpa,product_price_start_peritem_total,
//                product_price_start_all_net,product_price_start_all_fpa,product_price_start_all_total,
//                product_price_final_peritem_db,product_price_final_peritem_net,product_price_final_peritem_fpa,product_price_final_peritem_total,
//                product_price_final_all_net,product_price_final_all_fpa,product_price_final_all_total,
//                product_price_ekptosi_net,product_price_ekptosi_pososto,              
  
                $sql="insert into gks_acc_inv_products (
                mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
                acc_inv_id,product_aa,product_id,product_descr,
                product_monada_id_org,product_monada_id,monada_convert_json,monada_convert_epi,monada_convert_epi_rev,
                product_fpa_base_id,
                product_normal,product_type,
                product_quantity,
                product_otherTaxesPercentCategory,product_otherTaxesAmount
                ) values (
                now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
                ".$new_id.",".$product_aa.",".$product_id.",'".$db_link->escape_string($text_room)."',
                100,100,'',1,1,
                ".$product_fpa_base_id.",
                1,'normal',
                ".$product_quantity.",
                ".$product_otherTaxesPercentCategory.",
                ".number_format($product_otherTaxesAmount,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."
                )";
                $result = $db_link->query($sql);  
                if (!$result) {debug_mail(false,'error sql',$sql);
                  $return = array('success' => false, 'message' => base64_encode('sql error'));
                  echo json_encode($return); die(); }
                  
                $new_product_id = $db_link->insert_id;
                      
              }            
              
            } else {
              // aplo domatio
              
              $sql="INSERT INTO gks_acc_inv_products ( 
              mydate_add, mydate_edit, user_id_add, user_id_edit, myip, 
              acc_inv_id, 
              product_aa, product_set, product_id, product_descr, product_monada_id_org, product_monada_id, monada_convert_json, monada_convert_epi, monada_convert_epi_rev, product_is_digital,
              product_is_simple_download, product_need_apostoli, product_fpa_base_id, product_fpa_id, product_fpa_ejeresi_id, product_fpa_pososto, product_fpa_id_json,
              product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
              product_ogos_y, product_ogos_z, product_category_ids, product_sheets, product_quantity, product_price_check_fpa,product_price_include_vat, product_price_start_peritem_db,
              product_price_start_peritem_net, product_price_start_peritem_fpa, product_price_start_peritem_total, product_price_start_all_net, product_price_start_all_fpa,
              product_price_start_all_total, product_price_final_peritem_db, product_price_final_peritem_net, product_price_final_peritem_fpa,
              product_price_final_peritem_total, product_price_final_all_net, product_price_final_all_fpa, product_price_final_all_total, product_price_ekptosi_net,
              product_price_ekptosi_pososto, product_pricelist_item_id, product_pricelist_item_descr, product_pricelist_item_percent, product_price_coupon_use,
              product_price_coupon_use_disabled, product_comments, product_withheldPercentCategory, product_withheldAmount, product_stampDutyPercentCategory,
              product_stampDutyAmount, product_feesPercentCategory, product_feesAmount, product_otherTaxesPercentCategory, product_otherTaxesAmount,
              product_deductionsAmount, aade_lineComments,
              p_warehouses_id_from,p_warehouses_id_to
              )
              
              
              SELECT now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,
              '".$db_link->escape_string($gkIP)."' as myip,
              ".$new_id." as acc_inv_id,
              ".$product_aa." as product_aa, '' as product_set, product_id, '".$db_link->escape_string($text_room)."', product_monada_id as product_monada_id_org,
              product_monada_id, '' as monada_convert_json,
              1 as monada_convert_epi, 1 as monada_convert_epi_rev,
              product_is_digital,
              product_is_simple_download, product_need_apostoli,
              gks_eshop_products.product_fpa_base_id, product_fpa_id, product_fpa_ejeresi_id, product_fpa_pososto, product_fpa_id_json,
              product_normal, product_type, product_need_multi_files, product_need_multi_files_min, product_need_multi_files_max, product_varos, product_ogos_x,
              product_ogos_y, product_ogos_z, '' as product_category_ids, 0 as product_sheets, product_quantity, 1 as product_price_check_fpa,gks_hotel_reservation_room.product_price_include_vat, product_price_start_peritem_db,
              product_price_start_peritem_net, product_price_start_peritem_fpa, product_price_start_peritem_total, product_price_start_all_net, product_price_start_all_fpa,
              product_price_start_all_total, product_price_final_peritem_db, product_price_final_peritem_net, product_price_final_peritem_fpa,
              product_price_final_peritem_total, product_price_final_all_net, product_price_final_all_fpa, product_price_final_all_total, product_price_ekptosi_net,
              product_price_ekptosi_pososto, product_pricelist_item_id, product_pricelist_item_descr, product_pricelist_item_percent, product_price_coupon_use,
              product_price_coupon_use_disabled, product_comments,
              gks_hotel_reservation_room.product_withheldPercentCategory, gks_hotel_reservation_room.product_withheldAmount,
              gks_hotel_reservation_room.product_stampDutyPercentCategory,
              gks_hotel_reservation_room.product_stampDutyAmount, gks_hotel_reservation_room.product_feesPercentCategory, gks_hotel_reservation_room.product_feesAmount,
              gks_hotel_reservation_room.product_otherTaxesPercentCategory, gks_hotel_reservation_room.product_otherTaxesAmount,
              0 as product_deductionsAmount, '' as aade_lineComments,
              0 as p_warehouses_id_from, 0 as
              p_warehouses_id_to
              FROM gks_hotel_reservation_room
              LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product
              where id_hotel_reservation_room=".$vid['id'];
            
            
            
            
            //echo '<pre>';echo $sql;die();
            
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql);
                $return = array('success' => false, 'message' => base64_encode('sql error'));
                echo json_encode($return); die(); }  
              
              $new_product_id = $db_link->insert_id;  
            }
            
            $map_products[]=array(
              'old' => $vid['id'], 
              'new' => $new_product_id,
              'type' => $vid['type'],
              'id_product' => $vid['id_product'],
              'product_parent_id' => $vid['product_parent_id'],
              'product_class' => $vid['product_class'],
            );
          }
        }
        
//        if ($change_warehouses) {
//          $sql="update gks_acc_inv_products set 
//          p_warehouses_id_from=".$warehouses_id_from.",
//          p_warehouses_id_to=".$warehouses_id_to."
//          where gks_acc_inv_products.acc_inv_id=".$new_id;
//          $result = $db_link->query($sql);  
//          if (!$result) {
//            debug_mail(false,'error sql',$sql);
//            $return = array('success' => false, 'message' => base64_encode('sql error'));
//            echo json_encode($return); die(); }
//        }
        
        //echo '<pre>'.print_r($map_products,true)."\n";die();
        
        $sql="UPDATE gks_acc_inv LEFT JOIN (
          
          SELECT 
          ".$new_id." as nv_id_acc_inv,
          Sum(product_quantity) AS nv_products_posotita,
          Sum(product_varos*product_quantity) AS nv_products_varos,
          Sum(product_ogos_x*product_ogos_y*product_ogos_z*product_quantity) AS nv_products_ogos,
          Max(product_ogos_x) AS nv_products_ogos_max_x,
          Max(product_ogos_y) AS nv_products_ogos_max_y,
          Max(product_ogos_z*product_quantity) AS nv_products_ogos_max_z,
          Sum(product_price_start_all_net) as nv_gks_price_original_net,
          sum(product_price_final_all_net) as nv_gks_price_net,
          sum(product_price_final_all_fpa) as nv_gks_price_fpa,
          sum(product_price_final_all_net+product_price_final_all_fpa) as nv_gks_price_netfpa,
          
          sum(product_withheldAmount) as nv_totalWithheldAmount,
          sum(product_otherTaxesAmount) as nv_totalOtherTaxesAmount,
          sum(product_stampDutyAmount) as nv_totalStampDutyamount,
          sum(product_feesAmount) as nv_totalFeesAmount,
          sum(product_deductionsAmount) as nv_totalDeductionsAmount
          FROM gks_acc_inv_products
          WHERE acc_inv_id=".$new_id."        
        ) AS sum_vals ON gks_acc_inv.id_acc_inv = sum_vals.nv_id_acc_inv 
        SET 
        products_posotita = nv_products_posotita,
        products_varos=nv_products_varos,
        products_ogos=nv_products_ogos,
        products_ogos_max_x=nv_products_ogos_max_x,
        products_ogos_max_y=nv_products_ogos_max_y,
        products_ogos_max_z=nv_products_ogos_max_z,
        gks_price_original_net=nv_gks_price_original_net,
        gks_price_net=nv_gks_price_net,
        gks_price_fpa=nv_gks_price_fpa,
        gks_price_netfpa=nv_gks_price_netfpa,
        totalWithheldAmount=nv_totalWithheldAmount,
        totalOtherTaxesAmount=nv_totalOtherTaxesAmount,
        totalStampDutyamount=nv_totalStampDutyamount,
        totalFeesAmount=nv_totalFeesAmount,
        totalDeductionsAmount=nv_totalDeductionsAmount
        WHERE id_acc_inv=".$new_id;
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        
        $myarray_new=array();
        $myarray_line_new=array();        
        $idiotites_new=get_acc_inv_details_txt($new_id, $myarray_new, $myarray_line_new); 


        $sql="UPDATE gks_acc_inv SET 
        idiotites='".$db_link->escape_string(json_encode($myarray_new))."',
        gks_price_total = gks_price_net 
                        + gks_price_fpa
                        - totalWithheldAmount
                        + totalOtherTaxesAmount
                        + totalStampDutyamount
                        + totalFeesAmount
                        - totalDeductionsAmount
        WHERE id_acc_inv=".$new_id;
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }
        


        
        
        foreach ($map_products as $map_product) {
          if ($pb['has_esoda']!=0) {
            $sql="SELECT product_price_final_all_net
            FROM gks_acc_inv_products
            WHERE id_acc_inv_product=".$map_product['new'];
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }
            $row = $result->fetch_assoc();
            $product_price_final_all_net=floatval($row['product_price_final_all_net']);
            

            $xarakt_product_id=$map_product['id_product'];
            if ($map_product['product_class']=='variable_item') {
              $xarakt_product_id=$map_product['product_parent_id'];
            }
            $sql="SELECT aade_typos_xarakt_esodon_id AS typos_id, 
            aade_katigoria_xarakt_esodon_id AS cat_id, 
            acc_inv_product_income_pososto AS pososto,
            acc_eidos_parastatikou_id
            FROM gks_eshop_products_income
            WHERE product_id=".$xarakt_product_id."
            and acc_eidos_parastatikou_id in (0,".$pbasetypes[$i]['id_acc_eidos_parastatikou'].")
            ORDER BY id_product_income;";
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }

            $row_array=array();
            $has_spec_this=false;
            while ($row = $result->fetch_assoc()) {
              $row_array[]=$row;
              if ($row['acc_eidos_parastatikou_id']>0) $has_spec_this=true;
            }

            $final_all_net=0;
            $out_xarakt_esoda=array();
            $poso_sum=0;
            //while ($row = $result->fetch_assoc()) {
            foreach ($row_array as $row) {
              if ($has_spec_this == false or $row['acc_eidos_parastatikou_id']>0) {
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
            }
            $diafora=$final_all_net-$poso_sum;
            if ($diafora!=0 and count($out_xarakt_esoda)>0) $out_xarakt_esoda[count($out_xarakt_esoda)-1]['poso']+=round($diafora,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            
            foreach ($out_xarakt_esoda as $val) {
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
                $return = array('success' => false, 'message' => base64_encode('sql error'));
                echo json_encode($return); die(); }  
            }
            //print '<pre>';print_r($map_products);print_r($out_xarakt_esoda);print $final_all_net.'|'.$diafora;die();          
          }
          
          if ($pb['has_eksoda']!=0) {
            $sql="SELECT product_price_final_all_net
            FROM gks_acc_inv_products
            WHERE id_acc_inv_product=".$map_product['new'];
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }
            $row = $result->fetch_assoc();
            $product_price_final_all_net=floatval($row['product_price_final_all_net']);
            
            $xarakt_product_id=$map_product['id_product'];
            if ($map_product['product_class']=='variable_item') {
              $xarakt_product_id=$map_product['product_parent_id'];
            }
            $sql="SELECT aade_typos_xarakt_eksodon_id AS typos_id, 
            aade_katigoria_xarakt_eksodon_id AS cat_id, 
            acc_inv_product_expenses_pososto AS pososto,
            acc_eidos_parastatikou_id
            FROM gks_eshop_products_expenses
            WHERE product_id=".$xarakt_product_id."
            and acc_eidos_parastatikou_id in (0,".$pbasetypes[$i]['id_acc_eidos_parastatikou'].")
            ORDER BY id_product_expenses;";
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }

            $row_array=array();
            $has_spec_this=false;
            while ($row = $result->fetch_assoc()) {
              $row_array[]=$row;
              if ($row['acc_eidos_parastatikou_id']>0) $has_spec_this=true;
            }

            $final_all_net=0;
            $out_xarakt_eksoda=array();
            $poso_sum=0;
            //while ($row = $result->fetch_assoc()) {
            foreach ($row_array as $row) {
              if ($has_spec_this == false or $row['acc_eidos_parastatikou_id']>0) {
                $final_all_net=$product_price_final_all_net;
                if (empty($row['typos_id']) == false or empty($row['cat_id'])==false) {
                  $poso=round(floatval($row['pososto'])/100 * $final_all_net,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                  $poso_sum+=$poso;
                  $out_xarakt_eksoda[]=array(
                    'typos_id'=> intval($row['typos_id']),
                    'cat_id'=> intval($row['cat_id']),
                    'pososto'=> floatval($row['pososto']),
                    'poso' => $poso,
                  );
                }
              }
            }
            $diafora=$final_all_net-$poso_sum;
            if ($diafora!=0 and count($out_xarakt_eksoda)>0) $out_xarakt_eksoda[count($out_xarakt_eksoda)-1]['poso']+=round($diafora,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            
            foreach ($out_xarakt_eksoda as $val) {
              $sql="insert into gks_acc_inv_products_expenses (
              acc_inv_product_id,aade_typos_xarakt_eksodon_id,aade_katigoria_xarakt_eksodon_id,acc_inv_product_expenses_ammount
              ) values (
              ".$map_product['new'].",
              ".$val['typos_id'].",
              ".$val['cat_id'].",
              ".number_format($val['poso'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','')."
              )";
              $result = $db_link->query($sql);  
              if (!$result) {
                debug_mail(false,'error sql',$sql);
                $return = array('success' => false, 'message' => base64_encode('sql error'));
                echo json_encode($return); die(); }  
            }
            //print '<pre>';print_r($map_products);print_r($out_xarakt_eksoda);print $final_all_net.'|'.$diafora;die();          


           
          }
        }
        
        $sxolio=gks_lang('Προσθήκη από backend, δημιουργία από κράτηση με ID').' #<a href="admin-hotel-reservation-item.php?id='.$old_id.'">'.$old_id.'</a>'; 
        $sql="insert into gks_acc_inv_log (acc_inv_id, add_date,user_id,sxolio) values (
        ".$new_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
     
        
        $pbasetypes[$i]['new_id']=$new_id;
        
        $sql="insert into gks_object_rel (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        object_name1,object_id1,object_name2,object_id2
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        'gks_hotel_reservation',".$old_id.",'gks_acc_inv',".$new_id."
        )";
        $result = $db_link->query($sql);        
        if (!$result) {
          debug_mail(false,'error sql',$sql);
          $return = array('success' => false, 'message' => base64_encode('sql error'));
          echo json_encode($return); die(); }  
        
        
      }
    } 
  }
  
  
  
  //dimotikos_foros sxetiko parastatiko
  if (isset($pbasetypes[100]) and isset($pbasetypes[100]['new_id']) and $pbasetypes[100]['new_id']>0) { //dimotikos foros
    if (isset($pbasetypes[2]) and isset($pbasetypes[2]['new_id']) and $pbasetypes[2]['new_id']>0) { //apy i timologio paroxis
      $sql="update gks_acc_inv set dimotikos_foros_for_acc_inv_id=".$pbasetypes[2]['new_id']."
      where id_acc_inv=".$pbasetypes[100]['new_id'];
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }     
      
      $sql="insert into gks_acc_inv_correlated_invoices (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      acc_inv_id,coi_acc_inv_id,coi_aa
      ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$pbasetypes[100]['new_id'].",".$pbasetypes[2]['new_id'].",1
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }     
      
    }
  }
  
  
  //sindesi tou enos parastatikou me to allo
  foreach ($pbasetypes as $i1 => $pb1) {
    if (isset($pb1['new_id']) and $pb1['new_id']>0) {
      foreach ($pbasetypes as $i2 => $pb2) {
        if (isset($pb2['new_id']) and $pb2['new_id']>0) {
          if ($i1!=$i2) {

            $sql="insert into gks_object_rel (
            mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
            object_name1,object_id1,object_name2,object_id2
            ) values (
            now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
            'gks_acc_inv',".$pb1['new_id'].",'gks_acc_inv',".$pb2['new_id']."
            )";
            $result = $db_link->query($sql);        
            if (!$result) {
              debug_mail(false,'error sql',$sql);
              $return = array('success' => false, 'message' => base64_encode('sql error'));
              echo json_encode($return); die(); }  
            
            
          }
        }
      }
      break; 
    }
  } 
  
  
  
  $ret=array();
  foreach ($pbasetypes as $i => $pb) {
    if (isset($pb['new_id']) and $pb['new_id']>0) $ret[]=$pb['new_id'];
  } 
  
  //print '<pre>';print_r($pbasetypes);print $fiscal_position_id.'|'.$fiscal_position_id_new; die();

  return $ret;
  
}

include_once 'functions_hotel2.php';

