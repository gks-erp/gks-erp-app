<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_api_hotel_cmd_hotel_reservation_search($id_hotel,$row_hotel,$input_data) {
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

  $myreservations = $_gks_session['gks']['basket']['hotel']['reservation']['basket']['reservations'];

  
  //echo '<pre>'.print_r($input_data, true);die();
  
  $db_lang='';$db_lang2='';
  if ($_gks_session['gks']['ui_lang']=='en-US') {$db_lang='_en_US';$db_lang2='_en';}
        
  $_POST=$input_data['post'];

//echo '<pre>'.$id_hotel;die();

if ($_POST['gks_check_in'] == '__/__/____ __:__') $_POST['gks_check_in']='';
$gks_check_in=trim_gks(stripslashes(urldecode($_POST['gks_check_in'])));
if ($gks_check_in!='') {
  $gks_check_in = gks_myFormatDate($gks_check_in);}
if ($gks_check_in == '' or $gks_check_in<=0) {
  debug_mail(false,'gks_check_in',$gks_check_in);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την ημερομηνία άφιξης')));
  return $return;}
if ($gks_check_in < time() - 24*60*60) {
  debug_mail(false,'gks_check_in',$gks_check_in);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η ημερομηνία άφιξης είναι στο παρελθόν')));
  return $return;}  
$gks_check_in=date('Y-m-d', $gks_check_in).' '.$hotel_params['hotel_default_checkin'].':00';

if ($_POST['gks_check_out'] == '__/__/____ __:__') $_POST['gks_check_out']='';
$gks_check_out=trim_gks(stripslashes(urldecode($_POST['gks_check_out'])));
if ($gks_check_out!='') {
  $gks_check_out = gks_myFormatDate($gks_check_out);}
if ($gks_check_out == '' or $gks_check_out<=0) {
  debug_mail(false,'gks_check_out',$gks_check_out);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την ημερομηνία αναχώρησης')));
  return $return;}
if ($gks_check_out < time() - 24*60*60) {
  debug_mail(false,'gks_check_out',$gks_check_out);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η ημερομηνία αναχώρησης είναι στο παρελθόν')));
  return $return;}  
$gks_check_out=date('Y-m-d', $gks_check_out).' '.$hotel_params['hotel_default_checkout'].':00';

$gks_adults_count=0;if (isset($_POST['gks_adults_count'])) $gks_adults_count=intval($_POST['gks_adults_count']);
if ($gks_adults_count <=0) {
  debug_mail(false,'gks_adults_count',$gks_adults_count);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το πλήθος των ενηλίκων')));
  return $return;}
  
$gks_childs_count=0;if (isset($_POST['gks_childs_count'])) $gks_childs_count=intval($_POST['gks_childs_count']);
if ($gks_childs_count < 0) {
  debug_mail(false,'gks_childs_count',$gks_childs_count);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το πλήθος των παιδιών')));
  return $return;}
  
$gks_rooms_count=0;if (isset($_POST['gks_rooms_count'])) $gks_rooms_count=intval($_POST['gks_rooms_count']);
if ($gks_rooms_count <=0) {
  debug_mail(false,'gks_rooms_count',$gks_rooms_count);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το πλήθος των δωματίων')));
  return $return;}

$childs_and_ages = array();
if (isset($_POST['childs_and_ages'])) {
  $childs_and_ages_s = trim_gks($_POST['childs_and_ages']);
  if ($childs_and_ages_s != '') {
    $childs_and_ages_s=trim_gks(stripslashes(urldecode($_POST['childs_and_ages'])));
    $childs_and_ages = json_decode($childs_and_ages_s, true);
  }
}

$child_age_error='';

foreach ($childs_and_ages as $index => $value) {
  $i=$index+1;
  if ($value['age'] < 0) {
    $child_age_error.=
    str_replace('[1]', ($index + 1), 
    gks_lang('Επιλέξτε την ηλικία του [1]ου παιδιού')
    ).'<br>';
  } 
} 
if ($child_age_error!='') {
  debug_mail(false,'child_age_error',$child_age_error);
  $return = array('success' => false, 'message' => base64_encode($child_age_error));
  return $return;}
  



//$rchilds_ages_list = Array
//(
//    [0] => Array
//        (
//            [index] => 1
//            [age] => 7
//        )
//
//    [1] => Array
//        (
//            [index] => 2
//            [age] => 12
//        )
//
//    [2] => Array
//        (
//            [index] => 1
//            [age] => 7
//        )
//
//)

$rchilds_ages_list=array();
$index2=0;
foreach ($childs_and_ages as $index => $value) {
  if ($value['age'] >= 0) {
    $index2++;
    $rchilds_ages_list[]= array('index' => $index2, 'age' => $value['age']);
  } 
} 
//print '<pre>';
//print_r($rchilds_ages_list);
//die();


$child_age_price_ap_array=array();
for($ia=0; $ia<=$hotel_params['hotel_child_accept_max_age']; $ia++) {
  if ($ia < $hotel_params['hotel_child_accept_above_age']) {
    $child_age_price_ap_array[$ia]='';
  } else {
    $foundprice=gks_lang('ως ενήλικας');
    foreach ($hotel_params['hotel_child_age_price'] as $valia) {
      if ($ia >= $valia['from'] and $ia <= $valia['to']) {
        if ($valia['price']==0) $foundprice=gks_lang('Δωρεάν');
        else {
          $foundprice=myCurrencyFormat($valia['price']);
          if ($valia['type']=='night') $foundprice.= ' / '.gks_lang('Βράδυ');
          else if ($valia['type']=='stay') $foundprice.= ' / '.gks_lang('Κράτηση');
        }
        break;
      }
    } 
    $child_age_price_ap_array[$ia] = $ia.' '.gks_lang('ετών'); // ('.$foundprice.')';
  }
}

$days_round=hotel_round_days($id_hotel, $gks_check_in, $gks_check_out);
//print '<pre>';
//print_r($days_round);
//die();



$this_check_in1  = strtotime($days_round['check_in_round']);
$this_check_out1 = strtotime($days_round['check_out_round']);
$not_id_hotel_room=array();
foreach ($myreservations as $rsrv_aa => $reservation) {
  $this_check_in2  = strtotime($reservation['check_in']);
  $this_check_out2 = strtotime($reservation['check_out']);
  
  $is_overlap=false;
  
       if ($this_check_in2  <= $this_check_in1 and $this_check_out2 >= $this_check_out1) $is_overlap=true;
  else if ($this_check_in2  >= $this_check_in1 and $this_check_in2  <= $this_check_out1) $is_overlap=true;
  else if ($this_check_out2 >= $this_check_in1 and $this_check_out2 <= $this_check_out1) $is_overlap=true;
  
  if ($is_overlap) {
    foreach ($reservation['selrooms'] as $roomtype_aa => $selroom) {
      foreach ($selroom['rooms_items'] as $room_aa => $myroom) {
        if ($myroom['room_item_id']>0) {
                
          if (in_array($myroom['room_item_id'], $not_id_hotel_room)== false) {
            $not_id_hotel_room[] = $myroom['room_item_id'];
          }
        }
      }
    }
  }
}
//print '<pre>';
//print $days_round['check_in_round']."\r\n";
//print $days_round['check_out_round']."\r\n";
//print_r($not_id_hotel_room);
//die();


// 'rchilds_ages_list' =>  $rchilds_ages_list 
// den xreiazetao edo na steilo ta paidia. 
// Tha ginei janaipologismos sto kathena ksexorista otan ginei anathesi timon sto kauena
//echo '<pre>'.$id_hotel;die();

$get_availability_rooms_imput=array(
  'id_hotel' => $id_hotel,
  'date_from' => $days_round['check_in_round'],
  'date_to' => $days_round['check_out_round'],
  'alldata' => false,
  'id_hotel_room' => 0,
  'id_hotel_room_type' => 0,
  'not_id_hotel_reservation' => 0,
  'not_id_hotel_folio' => 0,
  'not_id_hotel_room' => $not_id_hotel_room,
  'rnum_adults' => 0,
  'rnum_childs' => 0,
  'rchilds_ages_list' => array(), 
  'rnum_child_kounies' =>0,
  'rnum_extra_beds' =>0,
  'come_from' => 'online',
);
$rooms_array = get_availability_rooms($get_availability_rooms_imput);




$html='';

//$html.='<div><pre>'.print_r($days_round,true).'</pre></div>';

$html.='<div>';
  $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ημερομηνία άφιξης').': <b>'.
          getWeekDayName(date('w', $days_round['check_in_round_time'])).' '.
          date('j', $days_round['check_in_round_time']).' '.
          getMonthName(date('n', $days_round['check_in_round_time'])).' '.
          date('Y', $days_round['check_in_round_time']).'</b>'.'</div>';
  $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ώρα άφιξης').': '.gks_lang('μετά τις').' <b>'.$hotel_params['hotel_default_checkin'].'</b></div>';
  $html.='<div class="gks_dfn"></div>';
  $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ημερομηνία αναχώρησης').': <b>'.
          getWeekDayName(date('w', $days_round['check_out_round_time'] + 24*60*60)).' '.
          date('j', $days_round['check_out_round_time'] + 24*60*60).' '.
          getMonthName(date('n', $days_round['check_out_round_time'] + 24*60*60)).' '.
          date('Y', $days_round['check_out_round_time'] + 24*60*60).'</b>'.'</div>';
  $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ώρα αναχώρησης').': '.gks_lang('έως τις').' <b>'.$hotel_params['hotel_default_checkout'].'</b></div>';
  $html.='<div class="gks_dfn"></div>';
  $html.='<div class="gks_rsrv_hfd">'.gks_lang('Διανυκτερεύσεις').': <b>'.$days_round['num_days'].'</b>'.'</div>';
  $html.='<div class="gks_rsrv_hfd">'.gks_lang('Ενήλικες').': <b>'.$gks_adults_count.'</b>'.'</div>';
  $html.='<div class="gks_rsrv_hfd">'.gks_lang('Παιδιά').': <b>'.$gks_childs_count.'</b>'.'</div>';
  $html.='<div class="gks_rsrv_hfd">'.gks_lang('Δωμάτια').': <b>'.$gks_rooms_count.'</b>'.'</div>';
  $html.='<div class="gks_dfn"></div>';
$html.='</div>
<div class="gks_dfn"></div>';        

//$total_roumtypes=
if ($rooms_array['error_msg'] != '') {
  
  $html.='
<div style="box-sizing:content-box;font-size:100%;line-height:22.5px;font-weight1:bold;text-size-adjust:100%;">
	<div class="" style="background-color:rgb(255, 250, 144);border-radius: 6px;border: 1px solid rgb(218, 213, 94);padding: 24px;color:black">
		<p style="text-align:center;">
		<i class="gks_fa gks_fa-exclamation-circle" style = "color: #cb0000;font-size: 200%;"></i>
		<br>
		'.$rooms_array['error_msg'].'
		</p>
	</div>
</div>';


  $return = array(
    'success' => true, 
    'message' => base64_encode('OK'),
    'html' => base64_encode($html), 
    //'html' => base64_encode($html.'<pre>'.print_r($defs,true).print_r($hotel_params,true).'</pre>'), //max_reservation_date_time
    'gks_roomsarray' => array(),
    'gks_adults_count' => $gks_adults_count,
    'gks_childs_count' => $gks_childs_count,
    'gks_rooms_count' => $gks_rooms_count,
    'hasfreerooms' => false,
    'gks_rooms_selection' => array(),
  );
  return $return;  
  
} 
$id_hotel_room_type_array=array();
foreach ($rooms_array['rooms_types'] as $rt) {
  $id_hotel_room_type_array[] = $rt['id_hotel_room_type'];
}
if (count($id_hotel_room_type_array) > 0) {
  $sql="SELECT gks_hotel_room_type_subroom.id_hotel_room_type_subroom,
  gks_hotel_room_type_subroom.hotel_room_type_id, 
  gks_hotel_room_type_subroom.subroom_type, gks_hotel_room_type_subroom.subroom_descr, 
  gks_hotel_room_type_subroom_en_US.subroom_descr_en_US,
  gks_hotel_room_type_subroom.subroom_visitors, gks_hotel_room_type_subroom.subroom_private_bath
  FROM gks_hotel_room_type_subroom
  LEFT JOIN (
    SELECT hotel_room_type_subroom_id, subroom_descr as subroom_descr_en_US FROM gks_hotel_room_type_subroom_lang WHERE lang_code='en-US'
  ) AS gks_hotel_room_type_subroom_en_US ON gks_hotel_room_type_subroom.id_hotel_room_type_subroom = gks_hotel_room_type_subroom_en_US.hotel_room_type_subroom_id  
  
  WHERE gks_hotel_room_type_subroom.hotel_room_type_id In (". implode(',',$id_hotel_room_type_array).")
  ORDER BY gks_hotel_room_type_subroom.hotel_room_type_id, gks_hotel_room_type_subroom.subroom_type, gks_hotel_room_type_subroom.subroom_descr;";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    return $return;}
  $id_hotel_room_type_subroom_array = array();
  while ($row = $result->fetch_assoc()) {
    $id_hotel_room_type_subroom_array[] = $row['id_hotel_room_type_subroom'];
    $row['beds']=array();
    $rooms_array['rooms_types'][$row['hotel_room_type_id']][$row['subroom_type']][$row['id_hotel_room_type_subroom']] = $row;
  }

  if (count($id_hotel_room_type_subroom_array) > 0) { 
    $sql="SELECT gks_hotel_room_type_subroom_bed.id_hotel_room_type_subroom_bed, 
    gks_hotel_room_type_subroom_bed.hotel_room_type_subroom_id, gks_hotel_room_type_subroom.hotel_room_type_id, gks_hotel_room_type_subroom.subroom_type, 
    gks_hotel_room_type_subroom_bed.hotel_bed_type_fix_id, 
    gks_hotel_bed_type_fix.bed_type_fix_descr, gks_hotel_bed_type_fix.bed_type_fix_descr_en, 
    gks_hotel_bed_type_fix.bed_type_fix_descr_extra, gks_hotel_bed_type_fix.bed_type_fix_descr_en_extra as bed_type_fix_descr_extra_en, 
    gks_hotel_room_type_subroom_bed.subroom_bed_plithos
    FROM (gks_hotel_room_type_subroom_bed 
    LEFT JOIN gks_hotel_bed_type_fix ON gks_hotel_room_type_subroom_bed.hotel_bed_type_fix_id = gks_hotel_bed_type_fix.id_hotel_bed_type_fix) 
    LEFT JOIN gks_hotel_room_type_subroom ON gks_hotel_room_type_subroom_bed.hotel_room_type_subroom_id = gks_hotel_room_type_subroom.id_hotel_room_type_subroom
    WHERE gks_hotel_room_type_subroom_bed.hotel_room_type_subroom_id In (".implode(',',$id_hotel_room_type_subroom_array).")
    and gks_hotel_bed_type_fix.bed_type_fix_disabled=0
    ORDER BY gks_hotel_bed_type_fix.bed_type_fix_visitors DESC , gks_hotel_bed_type_fix.id_hotel_bed_type_fix";
    $result = $db_link->query($sql); 
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      return $return;}
      
    while ($row = $result->fetch_assoc()) {
      $rooms_array['rooms_types'][$row['hotel_room_type_id']][$row['subroom_type']][$row['hotel_room_type_subroom_id']]['beds'][$row['id_hotel_room_type_subroom_bed']] = $row;
    }    
  }


  
  $sql="SELECT gks_hotel_room_type_amenity.id_hotel_room_type_amenity, gks_hotel_room_type_amenity.hotel_room_type_id,gks_hotel_room_type_amenity.hotel_room_amenity_type_fix_id, 
  gks_hotel_room_amenity_type_fix.room_amenity_type_fix_descr, gks_hotel_room_amenity_type_fix.room_amenity_type_fix_descr_en, 
  gks_hotel_room_amenity_type_fix.room_amenity_type_fix_memo, gks_hotel_room_amenity_type_fix.room_amenity_type_fix_memo_en, 
  gks_hotel_room_amenity_type_fix.hotel_room_amenity_group_type_fix_id, 
  gks_hotel_room_amenity_group_type_fix.room_amenity_group_type_fix_descr,gks_hotel_room_amenity_group_type_fix.room_amenity_group_type_fix_descr_en
  FROM (gks_hotel_room_type_amenity 
  LEFT JOIN gks_hotel_room_amenity_type_fix ON gks_hotel_room_type_amenity.hotel_room_amenity_type_fix_id = gks_hotel_room_amenity_type_fix.id_hotel_room_amenity_type_fix) 
  LEFT JOIN gks_hotel_room_amenity_group_type_fix ON gks_hotel_room_amenity_type_fix.hotel_room_amenity_group_type_fix_id = gks_hotel_room_amenity_group_type_fix.id_hotel_room_amenity_group_type_fix
  WHERE gks_hotel_room_type_amenity.hotel_room_type_id In (".implode(',',$id_hotel_room_type_array).") 
  AND gks_hotel_room_amenity_type_fix.room_amenity_type_fix_disabled=0
  ORDER BY gks_hotel_room_amenity_group_type_fix.room_amenity_group_type_fix_sortorder, gks_hotel_room_amenity_type_fix.room_amenity_type_fix_descr;";
  $result = $db_link->query($sql); 
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    return $return;}
  
  while ($row = $result->fetch_assoc()) {
    $rooms_array['rooms_types'][$row['hotel_room_type_id']]['amenity'][] = $row;
  }

}
//file_put_contents('/var/www/php/my-rooms.gks.gr/tmp/rooms_array.txt', print_r($rooms_array,true));


//print_r($id_hotel_room_type_array);
//die();

$html.='[[warning_msg]]';

$html.='<div class="gks_rsrv_har">'.gks_lang('Διαθέσιμα δωμάτια').'</div>';

$room_types = $rooms_array['rooms_types'];


$gks_roomsarray=array();
foreach ($room_types as $rt) {
  $gks_roomsarray[] = array(
    'id' => intval($rt['id_hotel_room_type']),
    'visitors_adults' => intval($rt['room_type_visitors']),
    'visitors_childs' => intval($rt['room_type_visitors_childs']),
    'visitors_max'    => intval($rt['room_type_visitors_max']),
    'price' => floatval($rt['type_room_total_price']),
    'free_rooms' => $rt['free_rooms'],
  );
}

$rooms_selection=array();
//$rooms_selection[20]=array(1);
//$rooms_selection[17]=array(2);


//$warning_msg='';

//$warning_msg = '<pre>'.print_r($gks_roomsarray,true).'</pre>';

$total_vistors=$gks_adults_count + $gks_childs_count;
if ($gks_rooms_count == 1 and $gks_adults_count == 1 and $gks_childs_count==0) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 1 and $myrec['visitors_childs'] == 0 and $myrec['visitors_max']==1) {
      $rooms_selection[$myrec['id']] = 1; break;
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=2; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_childs'] == 0) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        } else if ($myrec['visitors_adults'] == $i) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
} else if ($gks_rooms_count == 1 and $gks_adults_count == 2 and $gks_childs_count == 0) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 2 and $myrec['visitors_childs'] == 0 and $myrec['visitors_max']==2) {
      $rooms_selection[$myrec['id']] = 1; break;
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=2; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_childs'] == 0) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        } else if ($myrec['visitors_adults'] == $i) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
} else if ($gks_rooms_count == 1 and $gks_adults_count == 1 and $gks_childs_count == 1) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 1 and $myrec['visitors_childs'] == 1 and $myrec['visitors_max']==2) {
      $rooms_selection[$myrec['id']] = 1; break;
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=1; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_childs'] == 1 and $myrec['visitors_max']>=2) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        } else if ($myrec['visitors_adults'] == $i and $myrec['visitors_childs'] >= 1 and $myrec['visitors_max']>=2) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
  
} else if ($gks_rooms_count == 1 and $gks_adults_count == 3 and $gks_childs_count == 0) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 3 and $myrec['visitors_childs'] == 0 and $myrec['visitors_max']==3) {
      $rooms_selection[$myrec['id']] =1; break;
    }
  } 
  if (count($rooms_selection)==0) {
    for ($i=3; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
} else if ($gks_rooms_count == 1 and $gks_adults_count == 2 and $gks_childs_count == 1) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 2 and $myrec['visitors_childs'] == 1 and $myrec['visitors_max']==3) {
      $rooms_selection[$myrec['id']] =1; break;
    }
  } 
  if (count($rooms_selection)==0) {
    for ($i=2; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']==3) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=2; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']>=3) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
  
} else if ($gks_rooms_count == 1 and $gks_adults_count == 1 and $gks_childs_count == 2) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 1 and $myrec['visitors_childs'] == 2 and $myrec['visitors_max']==3) {
      $rooms_selection[$myrec['id']] =1; break;
    }
  } 
  if (count($rooms_selection)==0) {
    for ($i=1; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']==3) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=1; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']>=3) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
  
} else if ($gks_rooms_count == 1 and $gks_adults_count == 4 and $gks_childs_count == 0) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 4 and $myrec['visitors_childs'] == 0 and $myrec['visitors_max']==4 ) {
      $rooms_selection[$myrec['id']] = 1; break;
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=4; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
} else if ($gks_rooms_count == 1 and $gks_adults_count == 3 and $gks_childs_count == 1) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 3 and $myrec['visitors_childs'] == 1 and $myrec['visitors_max']==4 ) {
      $rooms_selection[$myrec['id']] = 1; break;
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=3; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']==4) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=3; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']>=4) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
} else if ($gks_rooms_count == 1 and $gks_adults_count == 2 and $gks_childs_count == 2) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 2 and $myrec['visitors_childs'] == 2 and $myrec['visitors_max']==4) {
      $rooms_selection[$myrec['id']] = 1; break;
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=2; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']==4) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=2; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']>=4) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
} else if ($gks_rooms_count == 1 and $gks_adults_count == 1 and $gks_childs_count == 3) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 1 and $myrec['visitors_childs'] == 3 and $myrec['visitors_max']==4) {
      $rooms_selection[$myrec['id']] = 1; break;
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=1; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']==4) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=1; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']>=4) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
   
} else if ($gks_rooms_count == 1 and $total_vistors >= 5) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == $gks_adults_count and $myrec['visitors_childs'] == $gks_childs_count and $myrec['visitors_max']==$total_vistors) {
      $rooms_selection[$myrec['id']] = 1; break;
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=$gks_adults_count; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max'] ==$total_vistors) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }
  if (count($rooms_selection)==0) {
    for ($i=$gks_adults_count; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i and $myrec['visitors_max'] >=$total_vistors) {
          $rooms_selection[$myrec['id']] = 1; break 2;
        }
      }
    }
  }



  
} else if ($gks_rooms_count == 2 and $gks_adults_count == 2 and $gks_childs_count == 0) {
  $found_room=0;
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['visitors_adults'] == 1 and $myrec['visitors_childs']==0 and count($myrec['free_rooms']) >= 2) {
      $rooms_selection[$myrec['id']] = 2; 
      $found_room=2; break;
    }
  }
  if ($found_room < $gks_rooms_count) {
    foreach ($gks_roomsarray as $myrec) {
      if ($myrec['visitors_adults']==1 and $myrec['visitors_childs']==0) {
        $rooms_selection[$myrec['id']] = 1; 
        $found_room++; 
        if ($found_room==$gks_rooms_count) break;
      }
    }
  }
  if ($found_room < $gks_rooms_count) {
    $diafora = $gks_rooms_count - $found_room;
    foreach ($gks_roomsarray as $myrec) {
      if ($myrec['visitors_adults'] == 2 and $myrec['visitors_childs']==0 and count($myrec['free_rooms']) >= $diafora) {
        $rooms_selection[$myrec['id']] = $diafora;
        $found_room+=$diafora; 
        if ($found_room==$gks_rooms_count) break;
      }
    }
  }
  if ($found_room < $gks_rooms_count) {
    foreach ($gks_roomsarray as $myrec) {
      if ($myrec['visitors_adults'] == 2) {
        $rooms_selection[$myrec['id']] = 1; 
        $found_room++; 
        if ($found_room==$gks_rooms_count) break;
      }
    }
  }
    
  if ($found_room < $gks_rooms_count) {
    for ($i=3; $i <= ($total_vistors + 10); $i++) {
      foreach ($gks_roomsarray as $myrec) {
        if ($myrec['visitors_adults'] == $i) {
          $diafora = $gks_rooms_count - $found_room;
          if (count($myrec['free_rooms']) < $diafora) $diafora= count($myrec['free_rooms']);
          $rooms_selection[$myrec['id']] = $diafora; 
          $found_room+=$diafora; 
          if ($found_room==$gks_rooms_count) break 2;
        }
      }
    }
  }

// apo edo sinexise  
} else { //if ($gks_rooms_count > 2) {
  //domatia
  $arr=array();
  for ($i=1; $i <= $gks_rooms_count; $i++) {
    $arr[$i]=array(
//      'index' => $i,
      'adults'=>0,
      'childs'=>0,
      'total'=>0,
      'room'=>0,
    );
  }
  //atoma ana domatio
  $cc=1;
  for ($i=1; $i <= $gks_adults_count; $i++) {
    $arr[$cc]['adults']++;
    $arr[$cc]['total']++;
    $cc++;
    if ($cc > $gks_rooms_count) $cc=1;
  }  
  $cc=1;
  for ($i=1; $i <= $gks_childs_count; $i++) {
    $arr[$cc]['childs']++;
    $arr[$cc]['total']++;
    $cc++;
    if ($cc > $gks_rooms_count) $cc=1;
  } 
  
  
  $arr_orig=$arr;
  //$warning_msg='<pre>'.print_r($arr,true);


  $domatia = array();
  foreach ($arr as &$val) {
    foreach ($gks_roomsarray as $myrec) {
      if ($myrec['visitors_adults'] == $val['adults'] and $myrec['visitors_childs'] == $val['childs'] and $myrec['visitors_max'] == $arr[$cc]['total']) {
        foreach ($myrec['free_rooms'] as $free_room) {
          if (in_array($free_room, $domatia) == false) {
            $domatia[] = $free_room;
            $val['room']=$free_room;
            break 2;
          }
        }
      }
    }
  }
  unset($val);
  
  
  
  if (count($domatia) < $gks_rooms_count) {
    foreach ($arr as &$val) {
      if ($val['room']==0) {
        foreach ($gks_roomsarray as $myrec) {
          if ($myrec['visitors_adults'] == $val['adults'] and $myrec['visitors_childs'] >= $val['childs'] and $myrec['visitors_max'] >= $arr[$cc]['total']) {
            foreach ($myrec['free_rooms'] as $free_room) {
              if (in_array($free_room, $domatia) == false) {
                $domatia[] = $free_room;
                $val['room']=$free_room;
                if (count($domatia) >= $gks_rooms_count) break 3;
                break 2;
              }
            }
          }
        }
      }
    }
    unset($val);
  }


  if (count($domatia) < $gks_rooms_count) {
    foreach ($arr as &$val) {
      if ($val['room']==0) {
        foreach ($gks_roomsarray as $myrec) {
          for ($i=1;$i <= $val['childs']; $i++) {
            if ($myrec['visitors_adults']==($val['adults']+$i) and $myrec['visitors_childs']==($val['childs']-$i) and $myrec['visitors_max']>=$arr[$cc]['total']) {
              foreach ($myrec['free_rooms'] as $free_room) {
                if (in_array($free_room, $domatia) == false) {
                  $domatia[] = $free_room;
                  $val['room']=$free_room;
                  if (count($domatia) >= $gks_rooms_count) break 4;
                  break 3;
                }
              }
            }
          }
        }
      }
    }
    unset($val);
  }
  
  if (count($domatia) < $gks_rooms_count) {
    foreach ($arr as &$val) {
      if ($val['room']==0) {
        foreach ($gks_roomsarray as $myrec) {
          if ($myrec['visitors_adults']==$val['total'] and $myrec['visitors_childs']==0) {
            foreach ($myrec['free_rooms'] as $free_room) {
              if (in_array($free_room, $domatia) == false) {
                $domatia[] = $free_room;
                $val['room']=$free_room;
                if (count($domatia) >= $gks_rooms_count) break 3;
                break 2;
              }
            }
          }
        }
      }
    }
    unset($val);
  }

  if (count($domatia) < $gks_rooms_count) {
    foreach ($arr as &$val) {
      if ($val['room']==0) {
        foreach ($gks_roomsarray as $myrec) {
          for ($i=$val['adults']; $i <= $val['adults'] + 10; $i++) {
            if ($myrec['visitors_adults'] == $i and $myrec['visitors_max']>=$arr[$cc]['total']) {
              foreach ($myrec['free_rooms'] as $free_room) {
                if (in_array($free_room, $domatia) == false) {
                  $domatia[] = $free_room;
                  $val['room']=$free_room;
                  if (count($domatia) >= $gks_rooms_count) break 4;
                  break 3;
                }
              } 
            }
          }
        }
      }
    }
    unset($val);
  }
  
  //echo '<pre>'; print_r($domatia); print "\n"; print_r($arr); die();


  
  //find total visitors
  $sum_visitors=0;
  foreach ($gks_roomsarray as $myrec) {
    foreach ($myrec['free_rooms'] as $free_room) {
      if (in_array($free_room, $domatia)) {
        $sum_visitors+=$myrec['visitors_max'];
      }
    }
  }
  //$warning_msg.=$sum_visitors;
  
//  //den exei kalifthei to plithos ton visitors, opote apo tin arxi
//  if ($sum_visitors <= $total_vistors) {
//    $sum_visitors=0;
//    $domatia=array();
//    foreach ($gks_roomsarray as $myrec) {
//      foreach ($myrec['free_rooms'] as $free_room) {
//        $domatia[] = $free_room;
//        $sum_visitors+=$myrec['visitors'];
//        if ($sum_visitors >= $total_vistors) {
//          break 2;
//        }
//      }
//    }    
//  }
  
  
  
  
//  if (count($domatia) < $gks_rooms_count) {
//    echo 'hhhhhhhhh'; 
//  }
  
  
  
  //find room type ids
  foreach ($domatia as $domatio) {
    foreach ($gks_roomsarray as $myrec) {
      foreach ($myrec['free_rooms'] as $free_room) {
        if ($free_room == $domatio) {
          if (isset($rooms_selection[$myrec['id']]) == false) {
            $rooms_selection[$myrec['id']]=0;
          }
          $rooms_selection[$myrec['id']]++;
        }
      }
    }
  }
  
  
  
  
  
  
  //print '<pre>';
  //print_r($gks_roomsarray);
  //print_r($arr);
  //print_r($domatia);
  //die();
  //$warning_msg.=print_r($arr,true).print_r($domatia,true).'</pre>';
  
}

//print '<pre>';
//print_r($rooms_selection);
//die();

$sum_rooms=0;
$sum_visitors_adults=0;
$sum_visitors_childs=0;
$sum_visitors_max=0;
$katanomi=array();
foreach ($rooms_selection as $room_type_id => $room_cc) {
  foreach ($gks_roomsarray as $myrec) {
    if ($myrec['id'] == $room_type_id) {
      $sum_rooms += $room_cc;
      $sum_visitors_adults += $room_cc * $myrec['visitors_adults'];
      $sum_visitors_childs += $room_cc * $myrec['visitors_childs'];
      $sum_visitors_max += $room_cc * $myrec['visitors_max'];
    
      $katanomi[$room_type_id]=array();
      for($i=1;$i<=$room_cc;$i++) {
        $katanomi[$room_type_id][] = array (
          
          'visitors_adults' => $myrec['visitors_adults'],
          'visitors_childs' => $myrec['visitors_childs'],
          'visitors_max' => $myrec['visitors_max'],
          'assign_adults' =>0,
          'assign_childs' =>0,
          'rchilds_ages_list' => array(),
        );
      }
    }
  }
}

$assign_adults=0;
for ($i=1; $i<=$gks_adults_count;$i++) {
  if ($assign_adults >= $gks_adults_count) break;
  foreach ($katanomi as &$room_type) {
    foreach ($room_type as &$room) {
      if ($room['visitors_adults'] > $room['assign_adults']) {
        $room['assign_adults']++;
        $assign_adults++;
        if ($assign_adults >= $gks_adults_count) break 3;
      }
    }
    unset($room);
  }
  unset($room_type);
}

$assign_childs=0;
for ($i=1; $i<=$gks_childs_count;$i++) {
  if ($assign_childs >= $gks_childs_count) break;
  foreach ($katanomi as &$room_type) {
    foreach ($room_type as &$room) {
      if ($room['visitors_childs'] > $room['assign_childs'] and ($room['assign_adults'] + $room['assign_childs']) < $room['visitors_max']) {
        $room['assign_childs']++;
        $assign_childs++;
        if ($assign_childs >= $gks_childs_count) break 3;
      }
    }
    unset($room);
  } 
  unset($room_type);
}
//ta ipoloipa paidia poy den mpikan sta kanonika
for ($i=1; $i<=$gks_childs_count;$i++) {
  if ($assign_childs >= $gks_childs_count) break;
  foreach ($katanomi as &$room_type) {
    foreach ($room_type as &$room) {
      if (($room['assign_adults'] + $room['assign_childs']) < $room['visitors_max']) {
        $room['assign_childs']++;
        $assign_childs++;
        if ($assign_childs >= $gks_childs_count) break 3;
      }
    }
    unset($room);
  } 
  unset($room_type);
}

//create rchilds_ages_list per room
foreach ($rchilds_ages_list as $child) {
  foreach ($katanomi as $id_room_type => &$room_type) {
    foreach ($room_type as &$room) {
      if (count($room['rchilds_ages_list']) < $room['assign_childs']) {
        $index=count($room['rchilds_ages_list']) + 1;
        //$room['rchilds_ages_list'][] = array('index' => $index, 'age' => $child['age']);
        $room['rchilds_ages_list'][] = array('index' => $child['index'], 'age' => $child['age']);
        break 2; 
      }
    }
    unset($room);
  }
  unset($room_type);
}


//print '<pre>';print_r($katanomi);die();

foreach ($katanomi as $id_room_type => &$room_type) {
  foreach ($room_type as &$room) {
    $get_availability_rooms_imput=array(
      'id_hotel' => $id_hotel,
      'date_from' => $days_round['check_in_round'],
      'date_to' => $days_round['check_out_round'],
      'alldata' => false,
      'id_hotel_room' => 0,
      'id_hotel_room_type' => $id_room_type,
      'not_id_hotel_reservation' => 0,
      'not_id_hotel_folio' => 0,
      'not_id_hotel_room' => array(),
      'rnum_adults' => $room['assign_adults'],
      'rnum_childs' => $room['assign_childs'],
      'rchilds_ages_list' => $room['rchilds_ages_list'],
      'rnum_child_kounies' =>0,
      'rnum_extra_beds' =>0,
      'come_from' => 'online',
    );  
    $roomaf = get_availability_rooms($get_availability_rooms_imput);
    //print '<pre>fff'.$roomaf['error_msg']; die();    
    if ($roomaf['error_msg'] == '') {
      if (isset($roomaf['rooms']) and count($roomaf['rooms']) >=1) {
        foreach ($roomaf['rooms'] as $myfirstroom) {
          if ($myfirstroom['hotel_room_type_id'] == $id_room_type) { // elegxos asfaleias
            $room['room_type_data'] = $myfirstroom; 
            //echo '<pre>'.$room['room_type_data']['room_ajia_table']['ajia_total_out']; die();
            
            break; //mono to proto me noiazei
            
          }
        } 
      }
    }
    //print '<pre>'.$id_room_type.' ';print_r($roomaf); die();    
  }
  unset($room);
} 
unset($room_type);

//print '<pre>';print_r($katanomi);die();




//print '<pre>';//print_r($rooms_selection);//die();
//print '<pre>';print_r($room_types);die();

$html.='<div style="">';
$rt_row=0;
$more_less_aa=0;
for($step_loop=1; $step_loop<=2; $step_loop++) {
  foreach ($room_types as $rt) {
    $add_this=false;
    if ($step_loop==1) {
      if (isset($rooms_selection[$rt['id_hotel_room_type']]) and $rooms_selection[$rt['id_hotel_room_type']]>=1) $add_this=true;
    } else {
      if (!(isset($rooms_selection[$rt['id_hotel_room_type']]) and $rooms_selection[$rt['id_hotel_room_type']]>=1)) $add_this=true;
    }
    if ($add_this) {
      
      //echo '<pre>'.print_r($rt,true).'</pre>';die();
      
      
      $rt_row++;
      $rt_html='';
      $rt_html.='<div class="gks_rsrv_rth">';
        $rt_html.='<div class="gks_rsrv_rthd" data-id="'.$rt['id_hotel_room_type'].'">'.$rt['room_type_descr'.$db_lang].'</div>';
        $rt_html.='<div class="gks_rsrv_rtc1 lightgallery_room_type" style="width:30%;">';
      
          
          $myimgurl = trim_gks($rt['room_type_photo']); 
          if ($myimgurl == '') {
            $myimgurl=GKS_SITE_URL."my/img/product.png";
            $myimgurl_thumb=$myimgurl;
          } else {
            $myimgurl_thumb = GKS_SITE_URL.$myimgurl;
            $myimgurl=GKS_SITE_URL.str_replace('/thumbnail/','/', $myimgurl);
          }
          $myimgurl=str_replace('//my/','/my/',$myimgurl);
          $myimgurl_thumb=str_replace('//my/','/my/',$myimgurl_thumb);
          
          $rt_html.='<a href="'.$myimgurl.'" class="lightgallery_photo">';
          $rt_html.='<img src="'.$myimgurl_thumb.'" class="gks_rsrv_img_main">';
          $rt_html.='</a>';
          $rt_html.='<div>'; 
          
                                 
          $sql_photos="select * from gks_hotel_room_type_photo where hotel_room_type_id=".$rt['id_hotel_room_type']." order by id_hotel_room_type_photo";
          $result_photos = $db_link->query($sql_photos);        
          if (!$result_photos) {
            debug_mail(false,'error sql',$sql);
            $return = array('success' => false, 'message' => base64_encode('sql error'));
            return $return;}            
          
          while ($row_photos = $result_photos->fetch_assoc()) {
            
            $photo_url =  GKS_SITE_URL.$row_photos['photo_url'];
            $photo_url_thumb = GKS_SITE_URL.dirname($row_photos['photo_url']).'/thumbnail/'.mb_basename($row_photos['photo_url']);
            
            $photo_url=str_replace('//my/','/my/',$photo_url);
            $photo_url_thumb=str_replace('//my/','/my/',$photo_url_thumb);
            
            if ($photo_url!=$myimgurl) {
              $rt_html.='<a href="'.$photo_url.'" class="lightgallery_photo">';
              $rt_html.='<img class="gks_rsrv_img" src="'.$photo_url_thumb.'" data-src="'.$photo_url_thumb.'">';
              $rt_html.='</a>';
            }
          }
          
                              

    //        $rt_html.='<img class="gks_rsrv_img" src="/wp-content/uploads/2019/03/342.jpg">';
    //        $rt_html.='<img class="gks_rsrv_img" src="/wp-content/uploads/2019/01/samsung_2.jpg">';
    //        $rt_html.='<img class="gks_rsrv_img" src="/wp-content/uploads/2019/01/tablet_3-324x324.jpg">';
    //        $rt_html.='<img class="gks_rsrv_img" src="/wp-content/uploads/2019/03/816.jpg">';
    //        $rt_html.='<img class="gks_rsrv_img" src="/wp-content/uploads/2019/01/samsung_2.jpg">';
    //        $rt_html.='<img class="gks_rsrv_img" src="/wp-content/uploads/2019/01/tablet_3-324x324.jpg">';
    //        $rt_html.='<img class="gks_rsrv_img" src="/wp-content/uploads/2019/01/tablet_3-324x324.jpg">';
    //        $rt_html.='<img class="gks_rsrv_img" src="/wp-content/uploads/2019/03/816.jpg">';
    //        $rt_html.='<img class="gks_rsrv_img" src="/wp-content/uploads/2019/01/tablet_3-324x324.jpg">';
          $rt_html.='</div>';
          $rt_html.='<div class="gks_dfn"></div>';
        $rt_html.='</div>';
        $rt_html.='<div class="gks_rsrv_rtc2" style="width:30%;">';
          $rt_html.='<div>'.gks_lang('Τύπος').': '.$rt['room_type_fix_descr'.$db_lang2].'</div>';
          
          
          
          if ($rt['room_type_visitors_max']>0) $rt_html.='<div>'.gks_lang('Επισκέπτες').': '.number_format($rt['room_type_visitors_max'], 0, '.', ',') .'</div>';
          if ($rt['room_type_bathrooms']>0) $rt_html.='<div>'.gks_lang('Μπάνια').': '.$rt['room_type_bathrooms'].'</div>';
          if ($rt['room_type_embado']>0) $rt_html.='<div>'.gks_lang('Εμβαδό').': '.number_format($rt['room_type_embado'], 0, '.', ',') .'m<sup>2</sup></div>';
          
          if (isset($rt['bedroom'])) {
            foreach ($rt['bedroom'] as $bedroom) {
              $rt_html.='<div><i class="gks_fas gks_fa-dot-circle"></i> '.$bedroom['subroom_descr'.$db_lang].'</div>';
              
              
              $tmps='';
              for($i=1;$i<=$bedroom['subroom_visitors'];$i++) {
                $tmps.='<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i>';
              }
              if ($tmps!='') $rt_html.='<div style="padding-left: 16px;">'.$tmps.'</div>';
              
              if ($bedroom['subroom_private_bath']==1) {
                $rt_html.='<div style="padding-left: 16px;">'.gks_lang('Έχει ιδιωτικό μπάνιο').'</div>';
              }
              $tmps='';
              foreach ($bedroom['beds'] as $bed) {
                $tmps.='';
                $tmps.='<div style="padding-left: 16px;">';
    
                if ($bed['subroom_bed_plithos']>1) $tmps.=$bed['subroom_bed_plithos'].' x ';
                $tmps.=$bed['bed_type_fix_descr'.$db_lang2];
                if (!empty($bed['bed_type_fix_descr_extra'.$db_lang2])) $tmps.=' <i class="gks_fas gks_fa-info-circle gks_jqtp" title="'.$bed['bed_type_fix_descr_extra'.$db_lang2].'"></i>';
                $tmps.='</div>';
              } 
              if ($tmps!='') $rt_html.=$tmps;
              
            } 
          }
          if (isset($rt['livingroom'])) {
            foreach ($rt['livingroom'] as $livingroom) {
              $rt_html.='<div><i class="gks_fas gks_fa-dot-circle"></i> '.$livingroom['subroom_descr'].'</div>';
              
              
              $tmps='';
              for($i=1;$i<=$livingroom['subroom_visitors'];$i++) {
                $tmps.='<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i>';
              }
              if ($tmps!='') $rt_html.='<div style="padding-left: 16px;">'.$tmps.'</div>';
              
              if ($livingroom['subroom_private_bath']==1) {
                $rt_html.='<div style="padding-left: 16px;">'.gks_lang('Έχει ιδιωτικό μπάνιο').'</div>';
              }
              $tmps='';
              foreach ($livingroom['beds'] as $bed) {
                $tmps.='';
                $tmps.='<div style="padding-left: 16px;">';
    
                if ($bed['subroom_bed_plithos']>1) $tmps.=$bed['subroom_bed_plithos'].' x ';
                $tmps.=$bed['bed_type_fix_descr'];
                if (!empty($bed['bed_type_fix_descr_extra'])) $tmps.=' <i class="gks_fas gks_fa-info-circle gks_jqtp" title="'.$bed['bed_type_fix_descr_extra'].'"></i>';
                $tmps.='</div>';
              } 
              if ($tmps!='') $rt_html.=$tmps;
              
            } 
          }      
          
          if (isset($rt['amenity'])) {
            $tmps1='';
            $tmps2='';
            
            foreach ($rt['amenity'] as $amenity) {
              
              $thistmp=$amenity['room_amenity_type_fix_descr'.$db_lang2];
              if (!empty($amenity['room_amenity_type_fix_memo'.$db_lang2])) $thistmp.=' <i class="gks_fas gks_fa-info-circle gks_jqtp" title="'.$amenity['room_amenity_type_fix_memo'.$db_lang2].'"></i>'; 
              $thistmp.=', ';
              
              if ($amenity['hotel_room_amenity_group_type_fix_id']==1) {
                $tmps1.=$thistmp;
              } else {
                $tmps2.=$thistmp;
              }
            } 
            if ($tmps1!='' or $tmps2!='') {
              if ($tmps1=='' and $tmps2!='') {$tmps1=$tmps2;$tmps2='';}
              
              if ($tmps1!='') $tmps1=substr($tmps1, 0, strlen($tmps1)-2);
              if ($tmps2!='') $tmps2=substr($tmps2, 0, strlen($tmps2)-2);
              
              $rt_html.='<div>'.gks_lang('Παροχές δωματίου').':</div>';
              $rt_html.='<div class="gks_amenity1">'.$tmps1 .'</div>';
              if ($tmps2!='') {
                $more_less_aa++;
                $rt_html.='<div class="gks_amenity2">';
                $rt_html.='<div class="gks_amenity2m" data-id="'.$more_less_aa.'"><span class="gks_amenity2ml" data-id="'.$more_less_aa.'"><i class="gks_fas gks_fa-eye"></i> '.gks_lang('Περισσότερα').'</span></div>';
                $rt_html.='<div class="gks_amenity2t" data-id="'.$more_less_aa.'">'.$tmps2 .'</div>';
                $rt_html.='<div class="gks_amenity2l" data-id="'.$more_less_aa.'"><span class="gks_amenity2ll" data-id="'.$more_less_aa.'"><i class="gks_fas gks_fa-eye-slash"></i>'.gks_lang('Λιγότερα').'</span></div>';
                $rt_html.='</div>';
              }
              
            }
            
            
          }
          //$tmps='';
          //for($i=1;$i<=$rt['room_type_bathrooms'];$i++) {
          //  $tmps.='<i class="gks_fa gks_fa-toilet gks_rsrv_adulticon"></i>';
          //}
          //if ($tmps!='') $rt_html.='<div class="gks_jqtp" title="'.$rt['room_type_bathrooms'].'">Bathrooms: '.$tmps.'</div>'; 
    
          
          
        $rt_html.='</div>';
        $rt_html.='<div class="gks_rsrv_rtc3" style="width:40%;">';
    
          $rt_html.='<table cellpadding="0" cellpadding="0" border="0" style="width:100%;border-collapse: collapse;">';
            $rt_html.='<tr class="gks_rsrv_table_tr_head">';
              $rt_html.='<td class="gks_rsrv_th1">'.gks_lang('Επισκέπτες').'</td>';
              //$rt_html.='<td class="gks_rsrv_th2">'.gks_lang('Τιμή').'</td>';
              $rt_html.='<td class="gks_rsrv_th3">'.gks_lang('Δωμάτια').'</td>';
            $rt_html.='</tr>'; 
            $rt_html.='<tr class="gks_rsrv_table_tr_details">';
              $rt_html.='<td class="gks_rsrv_td1">';
              $tmps='';
              for($i=1;$i<=$rt['room_type_visitors'];$i++) {
                $tmps.='<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i>';
              }
              for($i=1;$i<=($rt['room_type_visitors_max']-$rt['room_type_visitors']);$i++) {
                $tmps.='<i class="gks_fa gks_fa-child gks_rsrv_adulticon" style="font-size:70%"></i>';
              }
              $rt_html.=$tmps.'</td>';
              //$rt_html.='<td class="gks_rsrv_td2">'.myCurrencyFormat($rt['type_room_total_price'],true, true).'</td>';
              $rt_html.='<td class="gks_rsrv_td3">';
              $rt_html.='<select class="gks_rsrv_select gks_input_select" '.
              'id="gks_rsrv_select_id_'.$rt['id_hotel_room_type'].'" '.
              'data-id="'.$rt['id_hotel_room_type'].'" '.
              'data-adults="'.$rt['room_type_visitors'].'" '.
              'data-childs="'.$rt['room_type_visitors_childs'].'" '.
              'data-max="'.$rt['room_type_visitors_max'].'" '.
              'data-child_kounies="'.$rt['room_type_child_kounies'].'" '.
              'data-extra_beds="'.$rt['room_type_extra_beds'].'" '.
              'style="width:100%;max-width:320px;">'.
              '<option value="0">0</option>';
              $tmps='';
              for($i=1;$i<=count($rt['free_rooms']);$i++) {
                $tmps.='<option value="'.$i.'">'.$i.'</option>';
              }
              
              $rt_html.=$tmps.'</select></td>';
            $rt_html.='</tr>'; 
    //        $rt_html.='<tr>';
    //          $rt_html.='<td class="gks_rsrv_td1" style="width:33%">'.gks_lang('Άτομα').'</td>';
    //          $rt_html.='<td class="gks_rsrv_td2" style="width:33%">'.gks_lang('Τιμή').'</td>';
    //          $rt_html.='<td class="gks_rsrv_td3" style="width:34%">'.gks_lang('Δωμάτια').'</td>';
    //        $rt_html.='</tr>'; 
          $rt_html.='</table>';
            
          $has_selected_rooms=false;
          if (isset($rooms_selection[$rt['id_hotel_room_type']]) and $rooms_selection[$rt['id_hotel_room_type']]>0) $has_selected_rooms=true;
          
          $rt_html.='<div id="rooms_details_'.$rt['id_hotel_room_type'].'" style="'.($has_selected_rooms == false ? 'display:none;':'').'">';
          $rt_html.='<div class="rooms_details_title">'.gks_lang('Δωμάτια').'</div>';
          $rt_html.='<table cellpadding="0" cellpadding="0" border="0" style="width:100%;border-collapse: collapse;" id="rooms_details_table_'.$rt['id_hotel_room_type'].'"><tbody>';
          if ($has_selected_rooms) {
            for($ri=1; $ri<=$rooms_selection[$rt['id_hotel_room_type']]; $ri++) {
              $rt_html.='<tr class="">';
              $rt_html.='<td class="rooms_details_table_td">';
              $rt_html.='<span class="rooms_details_aa">#'.$ri.'</span> ';
              //echo '<pre>'.print_r($rt,true).'</pre>';
              
              $rt_html.='<select '.
                  'class="gks_input_select gks_input_rnum_adults" '.
                  'data_room_aa="'.$ri.'" '.
                  'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
                  'data_room_max_visitors="'.$rt['room_type_visitors_max'].'" '.
                  'style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
              
              
              $max_selectors=$rt['room_type_visitors'];
              if ($max_selectors > $gks_adults_count) $max_selectors=$gks_adults_count;
              
              for($i=1;$i<=$max_selectors;$i++) {
                $rt_html.='<option value="'.$i.'" ';
                if (isset($katanomi[$rt['id_hotel_room_type']]) and 
                    count($katanomi[$rt['id_hotel_room_type']]) >= $ri and
                    $katanomi[$rt['id_hotel_room_type']][$ri - 1]['assign_adults'] == $i) {
                  $rt_html.='selected';
                }
                $rt_html.='>'.$i.'</option>';
              }
              $rt_html.='</select>x<i class="gks_fa gks_fa-male gks_rsrv_adulticon"></i> ';
              if (count($childs_and_ages)>0) {
                $rt_html.='<select '.
                    'class="gks_input_select gks_input_rnum_childs" '.
                    'data_room_aa="'.$ri.'" '.
                    'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
                    'data_room_max_visitors="'.$rt['room_type_visitors_max'].'" '.
                    'style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
                
                
                $max_selectors=$rt['room_type_visitors_max'];
                if ($max_selectors > count($rchilds_ages_list)) $max_selectors=count($rchilds_ages_list);

                for($i=1;$i<=$max_selectors;$i++) {
                  $rt_html.='<option value="'.$i.'"';
                  if (isset($katanomi[$rt['id_hotel_room_type']]) and 
                      count($katanomi[$rt['id_hotel_room_type']]) >= $ri and
                      $katanomi[$rt['id_hotel_room_type']][$ri - 1]['assign_childs'] == $i) {
                    $rt_html.='selected';
                  }                
                  $rt_html.='>'.$i.'</option>';
                }
                $rt_html.='</select>x<i class="gks_fa gks_fa-child gks_rsrv_childicon" style="font-size:70%;"></i> ';  
              
              
                if (isset($katanomi[$rt['id_hotel_room_type']]) and 
                    count($katanomi[$rt['id_hotel_room_type']]) >= $ri and
                    $katanomi[$rt['id_hotel_room_type']][$ri - 1]['assign_childs'] > 0) {
                  $rt_html.='<div class="div_child_selectors" '.
                  'data_room_aa="'.$ri.'" '.
                  'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
                  '>'.
                  gks_lang('Παιδιά').': <span '.
                  'class="child_selectors" '.
                  'data_room_aa="'.$ri.'" '.
                  'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
                  '></span>';    
                  for($i=1;$i<=$katanomi[$rt['id_hotel_room_type']][$ri - 1]['assign_childs'];$i++) {
                      
                    $rt_html.='<select '.
                        'class="gks_input_select gks_input_child_age" '.
                        'data_room_aa="'.$ri.'" '.
                        'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
                        'data_room_i="'.$i.'" '.
                        'style="width:unset !important;padding: 4px 0px !important;"><option value=0></option>';
                    
                    $child_index_selection=0;
                    if (isset($katanomi[$rt['id_hotel_room_type']]) and 
                      count($katanomi[$rt['id_hotel_room_type']]) >= $ri and
                      isset($katanomi[$rt['id_hotel_room_type']][$ri - 1]['rchilds_ages_list'][$i - 1])) {
                      $child_index_selection=$katanomi[$rt['id_hotel_room_type']][$ri - 1]['rchilds_ages_list'][$i - 1]['index'];
                    }
                    
                    foreach ($rchilds_ages_list as $childage) {
                       $rt_html.='<option value='.$childage['index'].
                       ($child_index_selection == $childage['index'] ? ' selected ': '').
                       '>'.$childage['age'].' '.gks_lang('ετών').'</option>';
                    }
                    
                    //foreach($child_age_price_ap_array as $age => $myage) {
                    //  if ($myage!='') $rt_html.='<option value='.$age.'>'.$myage.'</option>';
                    //}
                    $rt_html.='</select>';  
                    
                    //$rt_html.= '<pre>'.print_r($child_age_price_ap_array,true).'</pre>';  
                  }
                  $rt_html.='</div>'; 
                  
                  //$rt_html.= '<pre>'.print_r($child_age_price_ap_array,true).'</pre>';
                }
              
                $ajia_total_out_child=0;
                if (isset($katanomi[$rt['id_hotel_room_type']]) and 
                    count($katanomi[$rt['id_hotel_room_type']]) >= $ri and
                    isset($katanomi[$rt['id_hotel_room_type']][$ri - 1]['room_type_data']['room_ajia_table']['ajia_total_out_child']) and 
                    $katanomi[$rt['id_hotel_room_type']][$ri - 1]['room_type_data']['room_ajia_table']['ajia_total_out_child'] >0) {
                  $ajia_total_out_child=$katanomi[$rt['id_hotel_room_type']][$ri - 1]['room_type_data']['room_ajia_table']['ajia_total_out_child'];
                
                }

                
                if ($rt['room_type_child_kounies']>0 and $hotel_params['hotel_child_kounies']['enable']) {
                  $childs_under_6=0;
                  foreach ($childs_and_ages as $value) {
                    if ($value['age']<=$hotel_params['hotel_child_kounies']['to']) {
                      $childs_under_6++;
                    }
                  }
                  if ($childs_under_6>0) {
                    $rt_html.='<div class="div_gks_input_rnum_child_kounies" '.
                    'data_room_aa="'.$ri.'" '.
                    'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
                    '>'.
                    gks_lang('Βρεφικά κρεβάτια').': '.
                    '<select '.
                    'class="gks_input_select gks_input_rnum_child_kounies" '.
                    'data_room_aa="'.$ri.'" '.
                    'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
                    'style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
                    $max_selectors=$rt['room_type_child_kounies'];
                    if ($max_selectors > $childs_under_6) $max_selectors=$childs_under_6;
                    for($i=1;$i<=$max_selectors;$i++) {
                      $rt_html.='<option value="'.$i.'">'.$i.'</option>';
                    }
                    $rt_html.='</select></div>';
                    //$rt_html.=print_r($childs_and_ages,true);
                  }
                }
              }
              

              if ($rt['room_type_extra_beds']>0 and $hotel_params['hotel_extra_beds']['enabled']) {
                $max_support_age=0;
                foreach ($hotel_params['hotel_extra_beds']['beds'] as $value) {
                  if ($value['to']>$max_support_age) $max_support_age=$value['to'];
                } 
                $visitors_age_is_supported=0;
                foreach ($childs_and_ages as $value) {
                  if ($value['age']<=$max_support_age) {
                    $visitors_age_is_supported++;
                  }
                }
                if ($max_support_age==18) { //ipostirizei kai adults
                  $visitors_age_is_supported+=$gks_adults_count;
                }
                
                if ($visitors_age_is_supported>0) {
                  $rt_html.='<div class="div_gks_input_rnum_extra_beds" '.
                  'data_room_aa="'.$ri.'" '.
                  'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
                  '>'.
                  gks_lang('Επιπλέον κρεβάτια').': '.
                  '<select '.
                  'class="gks_input_select gks_input_rnum_extra_beds" '.
                  'data_room_aa="'.$ri.'" '.
                  'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
                  'style="width:unset !important;padding: 4px 0px !important;"><option value="0"></option>';
                  $max_selectors=$rt['room_type_extra_beds'];
                  if ($max_selectors > $visitors_age_is_supported) $max_selectors=$visitors_age_is_supported;
                  for($i=1;$i<=$max_selectors;$i++) {
                    $rt_html.='<option value="'.$i.'">'.$i.'</option>';
                  }
                  $rt_html.='</select></div>';
                  //$rt_html.=print_r($childs_and_ages,true);
                }
              }              
              
              $room_type_total_price=$katanomi[$rt['id_hotel_room_type']][$ri - 1]['room_type_data']['room_ajia_table']['ajia_total_out'];
              $rt_html.='<div class="div_room_type_total_price" '.
              'data_room_aa="'.$ri.'" '.
              'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
              '>'.
              gks_lang('Τιμή').': <span class="room_type_total_price" '.
              'data_room_aa="'.$ri.'" '.
              'data_room_type_id="'.$rt['id_hotel_room_type'].'" '.
              'data-val="'.myNumberFormatNo0($room_type_total_price).'">'.
              myCurrencyFormat($room_type_total_price,true, true).
              '</span></div>';
              
              //$rt_html.='<pre>'.print_r($rt,true).'</pre>';
              
              //if (isset($katanomi[$rt['id_hotel_room_type']])) $rt_html.= '<pre>'.print_r($katanomi[$rt['id_hotel_room_type']],true).'</pre>';
                      
              //room_type_visitors
              //room_type_visitors_childs
              //room_type_visitors_max
              //$myroom['rnum_adults']
              //$myroom['rnum_childs']
              
              $rt_html.='</td>'; 
              $rt_html.='</tr>'; 
            }
          }
          $rt_html.='</tbody></table>';
          $rt_html.='</div>'; 
        
            
          
            
          
                      
          
          
        $rt_html.='</div>';
      
      
      $rt_html.='</div>';
      
      $rt_html.='<div class="gks_dfn"></div>';
      if ($rt_row < count($room_types)) {
        $rt_html.='<div class="gks_rsrv_crow"></div>';
      }
      
      //$rt_row
      
      $html.=$rt_html;
    }
  } 
}
$html.='</div>';

//elegxos apotelesmatos


//$warning_msg='<pre>'.$sum_rooms.' '.$sum_visitors.' '.print_r($rooms_selection, true).'</pre>';
//echo $warning_msg;
//die();

//$total_vistors=$gks_adults_count + $gks_childs_count;
//echo '<pre>'; echo $gks_adults_count; die();






$warning_msg='';
if ($sum_rooms == 0) {
  $warning_msg=gks_lang('Επιλέξτε τα δωμάτια που θέλετε');
} else if ($sum_rooms == $gks_rooms_count and $sum_visitors_adults == $gks_adults_count and $sum_visitors_childs==$gks_childs_count) {
  //ola kala. Einai akrivos
} else if ($sum_rooms == $gks_rooms_count and $sum_visitors_adults >= $gks_adults_count and $sum_visitors_max == $total_vistors) {
  //kapoia paidia tha einai san megaloi
} else {
  if ($sum_rooms > $gks_rooms_count and $sum_visitors_max == $total_vistors) {
    $warning_msg=gks_lang('Προσοχή: Έχουν επιλεγεί περισσότερα δωμάτια');
  } else if ($sum_rooms < $gks_rooms_count and $sum_visitors_max == $total_vistors) {
    $warning_msg=gks_lang('Προσοχή: Έχουν επιλεγεί λιγότερα δωμάτια');
  } else if ($sum_rooms == $gks_rooms_count and $sum_visitors_max > $total_vistors) {
    $warning_msg=gks_lang('Προσοχή: Τα επιλεγμένα δωμάτια εξυπηρετούν περισσότερους επισκέπτες');
  } else if ($sum_rooms == $gks_rooms_count and $sum_visitors_max < $total_vistors) {
    $warning_msg=gks_lang('Προσοχή: Τα επιλεγμένα δωμάτια εξυπηρετούν λιγότερους επισκέπτες');
  } else if ($sum_rooms > $gks_rooms_count and $sum_visitors_max > $total_vistors) {
    $warning_msg=gks_lang('Προσοχή: Έχουν επιλεγεί περισσότερα δωμάτια τα οποία εξυπηρετούν περισσότερους επισκέπτες');
  } else if ($sum_rooms < $gks_rooms_count and $sum_visitors_max < $total_vistors) {
    $warning_msg=gks_lang('Προσοχή: Έχουν επιλεγεί λιγότερα δωμάτια τα οποία εξυπηρετούν λιγότερους επισκέπτες');
  } else if ($sum_rooms < $gks_rooms_count and $sum_visitors_max > $total_vistors) {
    $warning_msg=gks_lang('Προσοχή: Έχουν επιλεγεί λιγότερα δωμάτια τα οποία εξυπηρετούν περισσότερους επισκέπτες');
  } else if ($sum_rooms > $gks_rooms_count and $sum_visitors_max < $total_vistors) {
    $warning_msg=gks_lang('Προσοχή: Έχουν επιλεγεί περισσότερα δωμάτια τα οποία εξυπηρετούν λιγότερους επισκέπτες');
  } else {
    $warning_msg=gks_lang('Προσοχή: Έχουν επιλεγεί κάποια δωμάτια τα οποία δεν εξυπηρετούν το σύνολο των επισκεπτών');
  }
  
  
}



if ($warning_msg!='') {
  
  $warning_msg='
<div id="warning_msg" style="box-sizing:content-box;font-size:100%;line-height:22.5px;font-weight1:bold;text-size-adjust:100%;">
	<div class="" style="background-color:rgb(255, 250, 144);border-radius: 6px;border: 1px solid rgb(218, 213, 94);padding: 24px;color:black">
		<p style="text-align:center;">
		<i class="gks_fa gks_fa-exclamation-circle" style = "color: #cb0000;font-size: 200%;"></i>
		<br>
		'.$warning_msg.'
		</p>
	</div>
</div>';

  $html=str_replace('[[warning_msg]]',$warning_msg,$html);
} else {
  $html=str_replace('[[warning_msg]]','',$html);
}
//echo '<pre>';print_r($days_round);die();

$return = array(
  'success' => true, 
  'message' => base64_encode('OK'),
  'html' => base64_encode($html),
  'gks_roomsarray' => $gks_roomsarray,
  'gks_adults_count' => $gks_adults_count,
  'gks_childs_count' => $gks_childs_count,
  'gks_rooms_count' => $gks_rooms_count,
  'hasfreerooms' => true,
  'gks_check_in_round' => $days_round['check_in_round'],
  'gks_check_out_round' => $days_round['check_out_round'],
  'gks_num_days' => $days_round['num_days'],
  'gks_rooms_selection' => $rooms_selection,
);
return $return;
  
}

