<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$my_page_title=gks_lang('Αυτόματη συμπλήρωση Aεροπορικής εταιρείας');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_airline','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$myaction=''; if (isset($_GET['myaction'])) $myaction=trim($_GET['myaction']);
$mypointype=0; if (isset($_GET['mypointype'])) $mypointype=intval($_GET['mypointype']);
//$cache_file='';if (isset($_GET['cache_file'])) $cache_file=trim($_GET['cache_file']);
$elem_id='';if (isset($_GET['elem_id'])) $elem_id=trim($_GET['elem_id']);
$airline='';if (isset($_GET['airline'])) $airline=trim($_GET['airline']);
$airline2='';if (isset($_GET['airline2'])) $airline2=trim($_GET['airline2']);
$airline3='';if (isset($_GET['airline3'])) $airline3=trim($_GET['airline3']);
$airline4='';if (isset($_GET['airline4'])) $airline4=trim($_GET['airline4']);
$flight_number='';if (isset($_GET['flight_number'])) $flight_number=trim($_GET['flight_number']);
$flight_number4='';if (isset($_GET['flight_number4'])) $flight_number4=trim($_GET['flight_number4']);


$val_date1_time='';if (isset($_GET['val_date1_time'])) $val_date1_time=trim($_GET['val_date1_time']);
$val_date2_time='';if (isset($_GET['val_date2_time'])) $val_date2_time=trim($_GET['val_date2_time']);
$val_from_id='';if (isset($_GET['val_from_id'])) $val_from_id=trim($_GET['val_from_id']);
$val_to_id='';if (isset($_GET['val_to_id'])) $val_to_id=trim($_GET['val_to_id']);
//$val_direction='';if (isset($_GET['val_direction'])) $val_direction=trim($_GET['val_direction']);

if ($mypointype==0) {
  if ($val_from_id>0) {
    $sql="SELECT poi_type_id FROM gks_poi WHERE poi_type_id in (2,3,4) and id_poi=".$val_from_id." limit 1";
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $mypointype=$row['poi_type_id'];
    }
  }
  if ($mypointype==0 and $val_to_id>0) {
    $sql="SELECT poi_type_id FROM gks_poi WHERE poi_type_id in (2,3,4) and id_poi=".$val_to_id." limit 1";
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $mypointype=$row['poi_type_id'];
      $temp=$val_from_id;
      $val_from_id=$val_to_id;
      $val_to_id=$val_from_id;
      
      
    }
  }
  
}

if ($mypointype!=2 and $mypointype!=3 and $mypointype!=4) $mypointype=0; //airpot, port, trai station

//echo '<pre>sssssss '.$mypointype.'|'.$val_from_id.'|'.$val_to_id;die();

if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim($_GET['term']);
$term=str_replace('*', '%', $term);
if (mb_strlen($term) <0 ) die();
if ($term=='') $term='%';

$term_array = array();
$temp=explode(' ',$term);
foreach ($temp as $value) {
  $value=trim($value);
  if ($value!='') {
    if (in_array($value, $term_array)==false) $term_array[] = $value;
  }
}

if ($myaction=='get_airline_train_cruise' and $mypointype==2) {
  //echo 'ggggddddd'.$mypointype.' '.$myaction;die();
  $out_recs=array(); $return_mode=0;
  
  //echo date('Y-m-d H:i:s',gks_popsicle_time_user($val_date1_time,1)); echo die();
  $split_cc=0;
  $day3letters=''; $mytime1='';$mytime2='';
  if ($val_date1_time == '__/__/____ __:__') $val_date1_time='';
  if ($val_date1_time!='') {
    $val_date1_time=trim_gks(stripslashes(urldecode($val_date1_time)));
    if ($val_date1_time!='') $val_date1_time = mystrtodb($val_date1_time);
    $val_date1_time=strtotime($val_date1_time);
    $day3letters=strtolower(showDate($val_date1_time,'D',1));
    $mytime1    =strtolower(showDate($val_date1_time,'H:i',1));
  }
  if ($val_date2_time == '__/__/____ __:__') $val_date2_time='';
  if ($val_date2_time!='') {
    $val_date2_time=trim_gks(stripslashes(urldecode($val_date2_time)));
    if ($val_date2_time!='') $val_date2_time = mystrtodb($val_date2_time);
    $val_date2_time=strtotime($val_date2_time);
    $day3letters_ret=strtolower(showDate($val_date2_time,'D',1));
    $mytime2        =strtolower(showDate($val_date2_time,'H:i',1));
  }
  //echo $val_date2_time; die();
  //print_r($_GET);die();
  
  if ($val_from_id>0 and $elem_id =='outward_from_airline') {
    $return_mode=1;
    $sql="SELECT gks_flights_routes.*, id_airline,airline_name, gks_poi_en_US.poi_descr_en_US
    FROM ((gks_flights_routes 
    LEFT JOIN gks_airline ON gks_flights_routes.airline_id = gks_airline.id_airline)
    LEFT JOIN gks_poi ON gks_flights_routes.airport_dep_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    
    WHERE gks_flights_routes.airport_arr_id=".$val_from_id." and airline_name<>''
    order by airline_name;";
    //echo $sql; die();
    //echo $val_date1_time;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime1!='') {
      foreach ($myarray as $row) {
        if ($mytime1==$row['arr_time']) {
          $dep_day3letters=strtolower(showDate($val_date1_time - $row['duration']*60,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date1_time - $row['duration']*60 ,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            if (isset($out_recs[$row['id_airline']])==false) $out_recs[$row['id_airline']]=$row;
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['airline_name']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date1_time - $row['duration']*60,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date1_time - $row['duration']*60 ,1)));
        if (strpos($row['days'],$dep_day3letters)!==false) {
          if (isset($out_recs[$row['id_airline']])==false) $out_recs[$row['id_airline']]=$row;
        }
      }
    }
    
    if (count($out_recs)>0 and array_key_last($out_recs) > 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  }
  
  if ($val_from_id>0 and $elem_id =='return_to_airline') {
    $return_mode=2;
    $sql="SELECT gks_flights_routes.*, id_airline,airline_name, gks_poi_en_US.poi_descr_en_US
    FROM ((gks_flights_routes 
    LEFT JOIN gks_airline ON gks_flights_routes.airline_id = gks_airline.id_airline)
    LEFT JOIN gks_poi ON gks_flights_routes.airport_arr_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    
    WHERE gks_flights_routes.airport_dep_id=".$val_from_id." and airline_name<>''
    order by airline_name;";
    //echo $sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime2!='') {
      foreach ($myarray as $row) {
        if ($mytime2==$row['dep_time']) {
          $dep_day3letters=strtolower(showDate($val_date2_time,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date2_time,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            if (isset($out_recs[$row['id_airline']])==false) $out_recs[$row['id_airline']]=$row;
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['airline_name']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date2_time,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date2_time,1)));
        if (strpos($row['days'],$dep_day3letters)!==false) {
          if (isset($out_recs[$row['id_airline']])==false) $out_recs[$row['id_airline']]=$row;
        }
      }
    }
    
    if (count($out_recs)>0 and array_key_last($out_recs) > 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  } 
  
  
  if ($val_from_id>0 and $elem_id =='outward_to_departure_airline') {
    $return_mode=3;
    $sql="SELECT gks_flights_routes.*, id_airline,airline_name, gks_poi_en_US.poi_descr_en_US
    FROM ((gks_flights_routes 
    LEFT JOIN gks_airline ON gks_flights_routes.airline_id = gks_airline.id_airline)
    LEFT JOIN gks_poi ON gks_flights_routes.airport_arr_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    WHERE gks_flights_routes.airport_dep_id=".$val_from_id." and airline_name<>''
    order by airline_name;";
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime1!='') {
      foreach ($myarray as $row) {
        if ($mytime1==$row['dep_time']) {
          $dep_day3letters=strtolower(showDate($val_date1_time,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date1_time,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            if (isset($out_recs[$row['id_airline']])==false) $out_recs[$row['id_airline']]=$row;
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['airline_name']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date1_time,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date1_time,1)));
        if (strpos($row['days'],$dep_day3letters)!==false) {
          if (isset($out_recs[$row['id_airline']])==false) $out_recs[$row['id_airline']]=$row;
        }
      }
    }
    
    if (count($out_recs)>0 and array_key_last($out_recs) > 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  }  



  
  if ($val_from_id>0 and $elem_id =='return_from_airline') {
    $return_mode=4;
    $sql="SELECT gks_flights_routes.*, id_airline,airline_name, gks_poi_en_US.poi_descr_en_US
    FROM ((gks_flights_routes 
    LEFT JOIN gks_airline ON gks_flights_routes.airline_id = gks_airline.id_airline)
    LEFT JOIN gks_poi ON gks_flights_routes.airport_dep_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    
    WHERE gks_flights_routes.airport_arr_id=".$val_from_id." and airline_name<>''
    order by airline_name;";
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime1!='') {
      foreach ($myarray as $row) {
        if ($mytime2==$row['arr_time']) {
          $dep_day3letters=strtolower(showDate($val_date2_time - $row['duration']*60,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date2_time - $row['duration']*60 ,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            if (isset($out_recs[$row['id_airline']])==false) $out_recs[$row['id_airline']]=$row;
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['airline_name']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date2_time - $row['duration']*60,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date2_time - $row['duration']*60 ,1)));
        if (strpos($row['days'],$dep_day3letters)!==false) {
          if (isset($out_recs[$row['id_airline']])==false) $out_recs[$row['id_airline']]=$row;
        }
      }
    }
    
    if (count($out_recs)>0 and array_key_last($out_recs) > 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  } 
  
  $sql="SELECT id_airline, airline_name, airline_iata_code, airline_icao_code
  FROM gks_airline
  where airline_name<> '' and (";
  $mywhere='';
  foreach ($term_array as $value) {
    $mywhere.=" (
    airline_name like '%".$db_link->escape_string($value)."%' or
    airline_iata_code like '%".$db_link->escape_string($value)."%' or
    airline_icao_code like '%".$db_link->escape_string($value)."%'
    ) and ";
  }
  if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
  $sql.=$mywhere.")  ";  
  
  $sql.=" ORDER BY airline_name limit 30";
  
  //echo $sql;die();
  
  $dbres = $db_link->query($sql);
  if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
  if ($dbres->num_rows>=1 and count($out_recs)>0 and array_key_last($out_recs) > 0) {$split_cc--; $out_recs[$split_cc]='split';}
  while ($row = $dbres->fetch_assoc()) {
    if (isset($out_recs[$row['id_airline']])==false) $out_recs[$row['id_airline']]=$row;
  }
  
  if (count($out_recs)>0 and array_key_last($out_recs) < 0) unset($out_recs[array_key_last($out_recs)]);
   
  $out=array();  
  foreach ($out_recs as $row) {
    if ($row==='split') {
      $out[] = array('id' => 'split', 'value' => '', 'icon' => 'split');
    } else {
      $airline_name=$row['airline_name'];
      //if (!empty($row['airline_iata_code']))      $airline_name.=' ('.trim($row['airline_iata_code']).')';
      //else if (!empty($row['airline_icao_code'])) $airline_name.=' ('.trim($row['airline_icao_code']).')';
      $flight_number='';
      if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
      else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
      else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);

      
      $out[] = array('id' => $row['id_airline'], 'value' => $airline_name, 'icon' => '<i class="fas fa-plane"></i>',
      'flight_iata' => $flight_number,
      'orig_airport' => (empty($row['poi_descr_en_US']) ? '' : $row['poi_descr_en_US']),
      'rm' => $return_mode,
      );
    }
  }
  
  
  $return = array('success'=>true, 'message'=>'OK','list'=>$out);
  echo json_encode($return);
  die();
}


if ($myaction=='get_flight_number' and $mypointype==2) {
  //echo date('Y-m-d H:i:s',gks_popsicle_time_user($val_date1_time,1)); echo die();
  $out_recs=array(); $return_mode=0;
  $split_cc=0;
  $day3letters=''; $mytime1='';$mytime2='';
  if ($val_date1_time == '__/__/____ __:__') $val_date1_time='';
  if ($val_date1_time!='') {
    $val_date1_time=trim_gks(stripslashes(urldecode($val_date1_time)));
    if ($val_date1_time!='') $val_date1_time = mystrtodb($val_date1_time);
    $val_date1_time=strtotime($val_date1_time);
    $day3letters=strtolower(showDate($val_date1_time,'D',1));
    $mytime1    =strtolower(showDate($val_date1_time,'H:i',1));
  }
  if ($val_date2_time == '__/__/____ __:__') $val_date2_time='';
  if ($val_date2_time!='') {
    $val_date2_time=trim_gks(stripslashes(urldecode($val_date2_time)));
    if ($val_date2_time!='') $val_date2_time = mystrtodb($val_date2_time);
    $val_date2_time=strtotime($val_date2_time);
    $day3letters_ret=strtolower(showDate($val_date2_time,'D',1));
    $mytime2        =strtolower(showDate($val_date2_time,'H:i',1));
  } 
  
  $airline_id=0; //Aegean Airlines = 87
  if ($airline!='') {
    $sql="select id_airline from gks_airline where airline_name like '".$db_link->escape_string($airline)."' order by id_airline limit 1";
    //echo 'gggg '.$sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    if ($dbres->num_rows>=1) {
      $row = $dbres->fetch_assoc();
      $airline_id=$row['id_airline'];
    }
  }
  $airline_id2=0; //Aegean Airlines = 87
  if ($airline2!='') {
    $sql="select id_airline from gks_airline where airline_name like '".$db_link->escape_string($airline2)."' order by id_airline limit 1";
    //echo 'gggg '.$sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    if ($dbres->num_rows>=1) {
      $row = $dbres->fetch_assoc();
      $airline_id2=$row['id_airline'];
    }
  }
  $airline_id3=0; //Aegean Airlines = 87
  if ($airline3!='') {
    $sql="select id_airline from gks_airline where airline_name like '".$db_link->escape_string($airline3)."' order by id_airline limit 1";
    //echo 'gggg '.$sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    if ($dbres->num_rows>=1) {
      $row = $dbres->fetch_assoc();
      $airline_id3=$row['id_airline'];
    }
  }
  $airline_id4=0; //Aegean Airlines = 87
  if ($airline4!='') {
    $sql="select id_airline from gks_airline where airline_name like '".$db_link->escape_string($airline4)."' order by id_airline limit 1";
    //echo 'gggg '.$sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    if ($dbres->num_rows>=1) {
      $row = $dbres->fetch_assoc();
      $airline_id4=$row['id_airline'];
    }
  }

  
  if ($val_from_id>0  && $elem_id=='outward_from_flight_number') {
    $return_mode=1;
    $sql="SELECT gks_flights_routes.*, gks_poi_en_US.poi_descr_en_US
    FROM (gks_flights_routes 
    LEFT JOIN gks_poi ON gks_flights_routes.airport_dep_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    WHERE gks_flights_routes.airport_arr_id=".$val_from_id."
    ".($airline_id>0 ? ' and airline_id='.$airline_id : '')."
    and (flight_iata<>'' or flight_icao<>'' or flight_number<>'')
    order by flight_iata,flight_icao,flight_number";
    //echo $sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime1!='') {
      foreach ($myarray as $row) {
        if ($mytime1==$row['arr_time']) {
          $dep_day3letters=strtolower(showDate($val_date1_time - $row['duration']*60,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date1_time - $row['duration']*60 ,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            $flight_number='';
            if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
            else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
            else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
            if (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['flight_iata']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date1_time - $row['duration']*60,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date1_time - $row['duration']*60 ,1)));
        //echo $dep_day3letters.' '.$row['flight_iata'].'<br>';
        if (strpos($row['days'],$dep_day3letters)!==false) {
          $flight_number='';
          if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
          else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
          else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
          if  (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
        }
      }
    }
    
    if (count($out_recs)>0 and intval(array_key_last($out_recs)) >= 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  }
  
  if ($val_from_id>0  && $elem_id=='return_to_flight_number') {
    //echo '<pre>sssss '.$val_from_id.' '.$airline_id2.' '.$airline2;die();
    
    $return_mode=2;
    $sql="SELECT gks_flights_routes.*, gks_poi_en_US.poi_descr_en_US
    FROM (gks_flights_routes 
    LEFT JOIN gks_poi ON gks_flights_routes.airport_arr_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    
    WHERE gks_flights_routes.airport_dep_id=".$val_from_id."
    ".($airline_id2>0 ? ' and airline_id='.$airline_id2 : '')."
    and (flight_iata<>'' or flight_icao<>'' or flight_number<>'')
    order by flight_iata,flight_icao,flight_number";
    //echo '<pre>'.$sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime2!='') {
      foreach ($myarray as $row) {
        if ($mytime2==$row['dep_time']) {
          $dep_day3letters=strtolower(showDate($val_date2_time,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date2_time,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            $flight_number='';
            if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
            else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
            else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
            if (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['flight_iata']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date2_time,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date2_time,1)));
        //echo $dep_day3letters.' '.$row['flight_iata'].'<br>';
        if (strpos($row['days'],$dep_day3letters)!==false) {
          $flight_number='';
          if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
          else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
          else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
          if  (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
        }
      }
    }
    
    if (count($out_recs)>0 and intval(array_key_last($out_recs)) >= 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  }
  



  if ($val_from_id>0  && $elem_id=='outward_to_flight_number') {
    $return_mode=3;
    $sql="SELECT gks_flights_routes.*, gks_poi_en_US.poi_descr_en_US
    FROM (gks_flights_routes 
    LEFT JOIN gks_poi ON gks_flights_routes.airport_arr_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id

    WHERE gks_flights_routes.airport_dep_id=".$val_from_id."
    ".($airline_id3>0 ? ' and airline_id='.$airline_id3 : '')."
    and (flight_iata<>'' or flight_icao<>'' or flight_number<>'')
    order by flight_iata,flight_icao,flight_number";
    //echo $sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime1!='') {
      foreach ($myarray as $row) {
        if ($mytime1==$row['dep_time']) {
          $dep_day3letters=strtolower(showDate($val_date1_time,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date1_time,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            $flight_number='';
            if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
            else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
            else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
            if (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['flight_iata']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date1_time,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date1_time,1)));
        //echo $dep_day3letters.' '.$row['flight_iata'].'<br>';
        if (strpos($row['days'],$dep_day3letters)!==false) {
          $flight_number='';
          if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
          else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
          else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
          if  (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
        }
      }
    }
    
    if (count($out_recs)>0 and intval(array_key_last($out_recs)) >= 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  }

  if ($val_from_id>0  && $elem_id=='return_from_flight_number') {
    $return_mode=4;
    $sql="SELECT gks_flights_routes.*, gks_poi_en_US.poi_descr_en_US
    FROM (gks_flights_routes 
    LEFT JOIN gks_poi ON gks_flights_routes.airport_dep_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    
    WHERE gks_flights_routes.airport_arr_id=".$val_from_id."
    ".($airline_id4>0 ? ' and airline_id='.$airline_id4 : '')."
    and (flight_iata<>'' or flight_icao<>'' or flight_number<>'')
    order by flight_iata,flight_icao,flight_number";
    //echo $sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime1!='') {
      foreach ($myarray as $row) {
        if ($mytime2==$row['arr_time']) {
          $dep_day3letters=strtolower(showDate($val_date2_time - $row['duration']*60,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date2_time - $row['duration']*60 ,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            $flight_number='';
            if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
            else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
            else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
            if (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['flight_iata']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date2_time - $row['duration']*60,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date2_time - $row['duration']*60 ,1)));
        //echo $dep_day3letters.' '.$row['flight_iata'].'<br>';
        if (strpos($row['days'],$dep_day3letters)!==false) {
          $flight_number='';
          if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
          else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
          else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
          if  (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
        }
      }
    }
    
    if (count($out_recs)>0 and intval(array_key_last($out_recs)) >= 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  }    
  //echo count($out_recs).' ---- '; print_r($out_recs);die();

  $sql="SELECT flight_number,flight_iata, flight_icao,gks_poi_en_US.poi_descr_en_US
  FROM (gks_flights_routes
  LEFT JOIN gks_poi ON gks_flights_routes.airport_dep_id = gks_poi.id_poi)
  LEFT JOIN (
    SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
  ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
  
  where (flight_iata<>'' or flight_icao<>'' or flight_number<>'') and ";
  if ($airline_id>0) $sql.="airline_id=".$airline_id." and ";
  $sql.=" (";
  
  $mywhere='';
  foreach ($term_array as $value) {
    $mywhere.=" (
    flight_iata like '%".$db_link->escape_string($value)."%' or
    flight_icao like '%".$db_link->escape_string($value)."%' or
    flight_number like '%".$db_link->escape_string($value)."%'
    ) and ";
  }
  if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
  $sql.=$mywhere.")  ";  
  $sql.=" group by flight_iata, flight_icao, flight_number, gks_poi_en_US.poi_descr_en_US
  ORDER BY flight_iata,flight_icao,flight_number limit 50";

  $dbres = $db_link->query($sql);
  if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
  if ($dbres->num_rows>=1 and count($out_recs)>0 and intval(array_key_last($out_recs)) >= 0) {$split_cc--; $out_recs[$split_cc]='split';}
  while ($row = $dbres->fetch_assoc()) {
    $flight_number='';
    if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
    else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
    else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
    if (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
  }
  
  if ($airline_id>0) { // se ola ta ypoloipa, asxeta apo airline
    $sql="SELECT flight_number,flight_iata, flight_icao,gks_poi_en_US.poi_descr_en_US
    FROM (gks_flights_routes
    LEFT JOIN gks_poi ON gks_flights_routes.airport_dep_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    
    where (flight_iata<>'' or flight_icao<>'' or flight_number<>'') and ";
    $sql.=" (";
    
    $mywhere='';
    foreach ($term_array as $value) {
      $mywhere.=" (
      flight_iata like '%".$db_link->escape_string($value)."%' or
      flight_icao like '%".$db_link->escape_string($value)."%' or
      flight_number like '%".$db_link->escape_string($value)."%'
      ) and ";
    }
    if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
    $sql.=$mywhere.")  ";  
    $sql.=" group by flight_iata, flight_icao, flight_number, gks_poi_en_US.poi_descr_en_US
    ORDER BY flight_iata,flight_icao,flight_number limit 50";
  
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    if ($dbres->num_rows>=1 and count($out_recs)>0 and intval(array_key_last($out_recs)) >= 0) {$split_cc--; $out_recs[$split_cc]='split';}
    while ($row = $dbres->fetch_assoc()) {
      $flight_number='';
      if (!empty($row['flight_iata']))        $flight_number=trim($row['flight_iata']);
      else if (!empty($row['flight_icao']))   $flight_number=trim($row['flight_icao']);
      else if (!empty($row['flight_number'])) $flight_number=trim($row['flight_number']);
      if (isset($out_recs[$flight_number])==false) $out_recs[$flight_number]=$row;
    }
  }
  
  
  if (count($out_recs)>0 and intval(array_key_last($out_recs)) < 0) unset($out_recs[array_key_last($out_recs)]);
  $out=array();  
  foreach ($out_recs as $flight_number => $row) {
    if ($row==='split') {
      $out[] = array('id' => 'split', 'value' => '', 'icon' => 'split');
    } else {
      $out[] = array('id' => $flight_number, 'value' => $flight_number, 'icon' => '<i class="fas fa-hashtag"></i>', 
        'orig_airport' => trim($row['poi_descr_en_US']),
        'rm' => $return_mode,
        );
    }
  }
  //print_r($exist_number);die();
  $return = array('success'=>true, 'message'=>'OK','list'=>$out);
  echo json_encode($return);
  die();
}


if ($myaction=='get_originating_airport' and $mypointype==2) {
  $out_recs=array();
  $split_cc=0;
  $airline_id=0; //Aegean Airlines = 87
  $day3letters=''; $mytime1='';$mytime2='';
  if ($val_date1_time == '__/__/____ __:__') $val_date1_time='';
  if ($val_date1_time!='') {
    $val_date1_time=trim_gks(stripslashes(urldecode($val_date1_time)));
    if ($val_date1_time!='') $val_date1_time = mystrtodb($val_date1_time);
    $val_date1_time=strtotime($val_date1_time);
    $day3letters=strtolower(showDate($val_date1_time,'D',1));
    $mytime1    =strtolower(showDate($val_date1_time,'H:i',1));
  }
  if ($val_date2_time == '__/__/____ __:__') $val_date2_time='';
  if ($val_date2_time!='') {
    $val_date2_time=trim_gks(stripslashes(urldecode($val_date2_time)));
    if ($val_date2_time!='') $val_date2_time = mystrtodb($val_date2_time);
    $val_date2_time=strtotime($val_date2_time);
    $day3letters_ret=strtolower(showDate($val_date2_time,'D',1));
    $mytime2        =strtolower(showDate($val_date2_time,'H:i',1));
  }
  

  $airline_id=0; //Aegean Airlines = 87
  if ($airline!='') {
    $sql="select id_airline from gks_airline where airline_name like '".$db_link->escape_string($airline)."' order by id_airline limit 1";
    //echo 'gggg '.$sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    if ($dbres->num_rows>=1) {
      $row = $dbres->fetch_assoc();
      $airline_id=$row['id_airline'];
    }
  }
  $airline_id4=0; //Aegean Airlines = 87
  if ($airline4!='') {
    $sql="select id_airline from gks_airline where airline_name like '".$db_link->escape_string($airline4)."' order by id_airline limit 1";
    //echo 'gggg '.$sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    if ($dbres->num_rows>=1) {
      $row = $dbres->fetch_assoc();
      $airline_id4=$row['id_airline'];
    }
  }
  
  if ($val_from_id>0 and $elem_id=='outward_from_originating_airport') {
    $sql="SELECT gks_flights_routes.*,airport_dep_id as id_poi, gks_poi_en_US.poi_descr_en_US
    FROM (gks_flights_routes 
    LEFT JOIN gks_poi ON gks_flights_routes.airport_dep_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    
    WHERE gks_flights_routes.airport_arr_id=".$val_from_id."
    ".($airline_id>0 ? ' and airline_id='.$airline_id : '')."
    and (flight_iata<>'' or flight_icao<>'' or flight_number<>'')
    order by flight_iata,flight_icao,flight_number";
    //echo $sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime1!='') {
      foreach ($myarray as $row) {
        if ($mytime1==$row['arr_time']) {
          $dep_day3letters=strtolower(showDate($val_date1_time - $row['duration']*60,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date1_time - $row['duration']*60 ,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            if ($row['flight_iata']==$flight_number or $row['flight_icao']==$flight_number or $row['flight_number']==$flight_number) {
              if (isset($out_recs[$row['poi_descr_en_US']])==false) $out_recs[$row['poi_descr_en_US']]=$row;
            }
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['poi_descr_en_US']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date1_time - $row['duration']*60,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date1_time - $row['duration']*60 ,1)));
        //echo $dep_day3letters.' '.$row['flight_iata'].'<br>';
        if (strpos($row['days'],$dep_day3letters)!==false) {
          //if ($row['flight_iata']==$flight_number or $row['flight_icao']==$flight_number or $row['flight_number']==$flight_number) {
            if  (isset($out_recs[$row['poi_descr_en_US']])==false) $out_recs[$row['poi_descr_en_US']]=$row;
          //}
        }
      }
    }
    
    if (count($out_recs)>0 and intval(array_key_last($out_recs)) >= 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  }

  if ($val_from_id>0 and $elem_id=='return_from_originating_airport') {
    $sql="SELECT gks_flights_routes.*,airport_dep_id as id_poi, gks_poi_en_US.poi_descr_en_US
    FROM (gks_flights_routes 
    LEFT JOIN gks_poi ON gks_flights_routes.airport_dep_id = gks_poi.id_poi)
    LEFT JOIN (
      SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
    ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
    
    WHERE gks_flights_routes.airport_arr_id=".$val_from_id."
    ".($airline_id4>0 ? ' and airline_id='.$airline_id4 : '')."
    and (flight_iata<>'' or flight_icao<>'' or flight_number<>'')
    order by flight_iata,flight_icao,flight_number";
    //echo $sql;die();
    $dbres = $db_link->query($sql);
    if (!$dbres) {debug_mail(false,'sql error', $sql);  $return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
    $myarray=array();
    while ($row = $dbres->fetch_assoc()) {
      $myarray[]=$row;
    }
    
    if ($mytime2!='') {
      foreach ($myarray as $row) {
        if ($mytime2==$row['arr_time']) {
          $dep_day3letters=strtolower(showDate($val_date2_time - $row['duration']*60,'D',1));
                         //strtolower(date('D',gks_popsicle_time_user($val_date2_time - $row['duration']*60 ,1)));
          if (strpos($row['days'],$dep_day3letters)!==false) {
            if ($row['flight_iata']==$flight_number4 or $row['flight_icao']==$flight_number4 or $row['flight_number']==$flight_number4) {
              if (isset($out_recs[$row['poi_descr_en_US']])==false) $out_recs[$row['poi_descr_en_US']]=$row;
            }
          }
        }
      }
    }
    if (count($out_recs)>0) {$split_cc--; $out_recs[$split_cc]='split';}
    
    
    
    $term_upper=strtoupper($term);
    foreach ($myarray as $row) {
      if ($term_upper=='%' or strpos(strtoupper($row['poi_descr_en_US']),$term_upper)!==false) {
        $dep_day3letters=strtolower(showDate($val_date2_time - $row['duration']*60,'D',1));
                       //strtolower(date('D',gks_popsicle_time_user($val_date2_time - $row['duration']*60 ,1)));
        //echo $dep_day3letters.' '.$row['flight_iata'].'<br>';
        if (strpos($row['days'],$dep_day3letters)!==false) {
          //if ($row['flight_iata']==$flight_number4 or $row['flight_icao']==$flight_number4 or $row['flight_number']==$flight_number4) {
            if  (isset($out_recs[$row['poi_descr_en_US']])==false) $out_recs[$row['poi_descr_en_US']]=$row;
          //}
        }
      }
    }
    
    if (count($out_recs)>0 and intval(array_key_last($out_recs)) >= 0) {$split_cc--; $out_recs[$split_cc]='split';}
    
  }  

  $sql="SELECT gks_poi.id_poi, gks_poi.poi_descr, gks_poi_en_US.poi_descr_en_US
  FROM gks_poi
  LEFT JOIN (
    SELECT poi_id, poi_descr as poi_descr_en_US FROM gks_poi_lang WHERE lang_code='en-US'
  ) AS gks_poi_en_US ON gks_poi.id_poi = gks_poi_en_US.poi_id
  
  WHERE gks_poi.poi_type_id=2 and (";
  $mywhere='';
  foreach ($term_array as $value) {
    $mywhere.=" (
    gks_poi_en_US.poi_descr_en_US like '%".$db_link->escape_string($value)."%' or
    gks_poi.poi_descr like '%".$db_link->escape_string($value)."%'
    ) and ";
  }
  if (strlen($mywhere)>5) $mywhere=substr($mywhere, 0, strlen($mywhere)-5);
  $sql.=$mywhere.")  ";  
  $sql.=" ORDER BY gks_poi_en_US.poi_descr_en_US limit 50";
  
  $dbres = $db_link->query($sql);
  if (!$dbres) {debug_mail(false,'sql error', $sql);$return = array('success' => false, 'message' => base64_encode('error sql'));echo json_encode($return); die();}  
  if ($dbres->num_rows>=1 and count($out_recs)>0 and intval(array_key_last($out_recs)) >= 0) {$split_cc--; $out_recs[$split_cc]='split';}
  while ($row = $dbres->fetch_assoc()) {
    if (isset($out_recs[$row['poi_descr_en_US']])==false) $out_recs[$row['poi_descr_en_US']]=$row;
  }
  
  if (count($out_recs)>0 and array_key_last($out_recs) < 0) unset($out_recs[array_key_last($out_recs)]);
  $out=array();
  foreach ($out_recs as $row) {  
    if ($row==='split') {
      $out[] = array('id' => 'split', 'value' => '', 'icon' => 'split');
    } else {
      $poi_descr_en_US='';
      if (!empty($row['poi_descr_en_US']))        $poi_descr_en_US=trim($row['poi_descr_en_US']);
      if ($poi_descr_en_US!='') {
        $out[] = array('id' => $row['id_poi'], 'value' => $poi_descr_en_US, 'icon' => '<i class="fas fa-plane-departure"></i>');
      }
    }
  }
  
  
  $return = array('success'=>true, 'message'=>'OK','list'=>$out);
  echo json_encode($return);
  die();
}

$return = array('success'=>true, 'message'=>'OK','list'=>[]);
echo json_encode($return);
die();
echo 'gggg'.$mypointype.' '.$myaction;die();


die();
