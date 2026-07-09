<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Πλάνο');
$nav_active_array=array('hotel','hotel_plan');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__hotel_plan','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$user_hotels=gks_get_hotels_list();



$ismobile=0;
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
  $ismobile=1;
}


$id_hotel=0;if (isset($_GET['hotel_id'])) $id_hotel=intval($_GET['hotel_id']); 
if ($id_hotel<=0) {
  if (count($user_hotels)>=1) foreach ($user_hotels as $value) {$id_hotel=$value['id']; break;}  
}

//echo $id_hotel;die();

$mydaydif=0;
if (isset($_GET['day'])) $mydaydif=intval($_GET['day']);

$plusdays=60;
if ($ismobile) $plusdays=3;
if (isset($_GET['plusdays'])) $plusdays=intval($_GET['plusdays']);


$mytimenow=time() + $mydaydif*24*60*60; // + 0*24*60*60;
//$mytimenow=strtotime('2019-02-22 03:30:00');

$time_vardia=_time_user($mytimenow, 1);
$time_vardia-= GKS_ERP_START_VARDIA*60*60;
$today_vardia = date('Y-m-d',$time_vardia);
$today_vardia = strtotime($today_vardia);
//$today_vardia = _time_user($today_vardia, -1);
//$today_vardia = $today_vardia + GKS_ERP_START_VARDIA*60*60;
$today_vardia_time = $today_vardia;
$today_vardia = date('Y-m-d H:i:s', $today_vardia);


$today_day = date('w', $today_vardia_time); //0 (for Sunday) through 6 (for Saturday)
$thisdatehuman = getWeekDayName($today_day).' '. showDate($today_vardia_time, 'd/m/Y', 1);

$date_fr_time = $today_vardia_time;
$date_fr = date('Y-m-d', $date_fr_time);
$date_fr_day = date('w', $date_fr_time); //0 (for Sunday) through 6 (for Saturday)
$date_fr_human = getWeekDayName($date_fr_day).' '. showDate($date_fr_time, 'd/m/Y', 1);

$date_to_time = $date_fr_time + ($plusdays-1)*24*60*60;
$date_to = date('Y-m-d', $date_to_time);
$date_to_day = date('w', $date_to_time); //0 (for Sunday) through 6 (for Saturday)
$date_to_human = getWeekDayName($date_to_day).' '. showDate($date_to_time, 'd/m/Y', 1);

$date_fr1 = date('Y-m-d', $date_fr_time-24*60*60);
$date_to1 = date('Y-m-d', $date_to_time+24*60*60);

//echo date('Y-m-d H:i:s', $date_to_time);
//die();

$today_real_time = strtotime(date('Y-m-d', _time_user(time(), 1))); // - GKS_ERP_START_VARDIA*60*60


    


$lang_data_sqlfl=gks_lang_data_obj_prepare('gks_hotel_room_type','default');
if ($lang_data_sqlfl['success']==false) die($lang_data_sqlfl['message']);
gks_lang_data_obj_sql_prepare($lang_data_sqlfl, array('room_type_descr'));



$plandata=array();
$sql="SELECT gks_hotel_room_type.*,
".gks_lang_sql_field('room_type_descr',$lang_data_sqlfl)."
FROM ".$lang_data_sqlfl['sql']['from1']." gks_hotel_room_type 
".$lang_data_sqlfl['sql']['from2']."
where gks_hotel_room_type.hotel_id=".$id_hotel."
".(count($perm_id_hotel_ids)>0 ? ' and gks_hotel_room_type.hotel_id in ('.implode(',',$perm_id_hotel_ids).')' : '')."
ORDER BY room_type_sortorder,".gks_lang_sql_field('room_type_descr',$lang_data_sqlfl,'',true);
//echo '<pre>'.$sql;die();


$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
while ($row = $result->fetch_assoc()) {
  $row['rooms']=array();
  $plandata[$row['id_hotel_room_type']]=$row; 
}
$lang_data_sqlfl_a=gks_lang_data_obj_prepare('gks_hotel_room','default');
if ($lang_data_sqlfl_a['success']==false) die($lang_data_sqlfl_a['message']);
gks_lang_data_obj_sql_prepare($lang_data_sqlfl_a, array('room_descr'));

$lang_data_sqlfl_b=gks_lang_data_obj_prepare('gks_hotel_floor','default');
if ($lang_data_sqlfl_b['success']==false) die($lang_data_sqlfl_b['message']);
gks_lang_data_obj_sql_prepare($lang_data_sqlfl_b, array('floor_descr'));



$sql="SELECT gks_hotel_room.*, 
".gks_lang_sql_field('room_descr',$lang_data_sqlfl_a).",
".gks_lang_sql_field('floor_descr',$lang_data_sqlfl_b)."
FROM (".$lang_data_sqlfl_a['sql']['from1']." ".$lang_data_sqlfl_b['sql']['from1']." gks_hotel_room 
LEFT JOIN gks_hotel_floor ON gks_hotel_room.hotel_floor_id = gks_hotel_floor.id_hotel_floor)
".$lang_data_sqlfl_a['sql']['from2']."
".$lang_data_sqlfl_b['sql']['from2']."

where gks_hotel_room.hotel_id=".$id_hotel."
".(count($perm_id_hotel_ids)>0 ? ' and gks_hotel_room.hotel_id in ('.implode(',',$perm_id_hotel_ids).')' : '')."
ORDER BY gks_hotel_room.room_sortorder,".gks_lang_sql_field('room_descr',$lang_data_sqlfl_a,'', true);
$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
while ($row = $result->fetch_assoc()) {
  if (isset($plandata[$row['hotel_room_type_id']])) {
    $plandata[$row['hotel_room_type_id']]['rooms'][]=$row; 
  }
}
$get_availability_rooms_imput=array(
  'id_hotel' => $id_hotel,
  'date_from' => $date_fr1,
  'date_to' => $date_to1,
  'alldata' => true,
  'id_hotel_room' => 0,
  'id_hotel_room_type' => 0,
  'not_id_hotel_reservation' => 0,
  'not_id_hotel_folio' => 0,
  'not_id_hotel_room' => array(),
  'rnum_adults' => 0,
  'rnum_childs' => 0,
  'rchilds_ages_list' => array(),
  'rnum_child_kounies' =>0,
  'rnum_extra_beds' =>0,
  
);
$rooms_array = get_availability_rooms($get_availability_rooms_imput);


$id_hotel_reservation_ids=array();
foreach ($rooms_array['rooms'] as $myroom) {
  foreach ($myroom['days'] as $myday) {
    if (isset($myday['reservation'])) {
      foreach ($myday['reservation'] as $reservation) {
        if (in_array($reservation['hotel_reservation_id'],$id_hotel_reservation_ids)==false) {
          $id_hotel_reservation_ids[]=$reservation['hotel_reservation_id'];
        }
      }
    }
  }
}
//print '<pre>';print_r($id_hotel_reservation_ids);print '</pre>';die();

$reservation_data=array();
if (count($id_hotel_reservation_ids)>0) {
  $sql="SELECT gks_hotel_reservation.id_hotel_reservation, gks_hotel_reservation.reservation_status, 
  gks_hotel_reservation.check_in, gks_hotel_reservation.check_out, gks_hotel_reservation.num_days, 
  gks_hotel_reservation.num_adults, gks_hotel_reservation.num_childs, gks_hotel_reservation.num_child_kounies, gks_hotel_reservation.num_extra_beds, 
  gks_hotel_reservation.rooms_plithos, 
  gks_hotel_reservation.user_first_name, gks_hotel_reservation.user_last_name, gks_hotel_reservation.user_email, gks_hotel_reservation.user_mobile, 
  gks_lang.lang_name, gks_country.country_name, gks_hotel_reservation.user_notes,gks_hotel_reservation.sxolio,
  gks_hotel_reservation.gks_price_total,
  ".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
  gks_crm_channel_sale.crm_channel_sale_descr, 
  ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
  gks_ads_campain.ads_campain_name,
  crm_channel_url,crm_channel_text,crm_channel_code,
  gks_hotel_reservation.ma_country_id,
  gks_hotel_reservation.ma_nomos_id,
  gks_hotel_reservation.user_lang,gks_lang.idd_lang
  FROM (((((gks_hotel_reservation 
  LEFT JOIN gks_lang ON gks_hotel_reservation.user_lang = gks_lang.id_lang) 
  LEFT JOIN gks_country ON gks_hotel_reservation.ma_country_id = gks_country.id_country)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_hotel_reservation.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
  LEFT JOIN gks_crm_channel_sale ON gks_hotel_reservation.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_hotel_reservation.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
  LEFT JOIN gks_ads_campain ON gks_hotel_reservation.crm_channel_campain_id = gks_ads_campain.id_ads_campain
  
  where gks_hotel_reservation.id_hotel_reservation in (".implode(',',$id_hotel_reservation_ids).")";
  //echo '<pre>'.$sql;die();
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
  while ($row = $result->fetch_assoc()) {
    
    $row['check_in']=myDateTimeFormatw(strtotime($row['check_in']));
    $row['check_out']=myDateTimeFormatw(strtotime($row['check_out']));
    
    $row['num_days']=intval($row['num_days']);
    $row['num_adults']=intval($row['num_adults']);
    $row['num_childs']=intval($row['num_childs']);
    $row['num_child_kounies']=intval($row['num_child_kounies']);
    $row['num_extra_beds']=intval($row['num_extra_beds']);
    $row['rooms_plithos']=intval($row['rooms_plithos']);
    $row['user_first_name']=trim_gks($row['user_first_name']);
    $row['user_last_name']=trim_gks($row['user_last_name']);
    $row['user_email']=trim_gks($row['user_email']);
    $row['user_mobile']=trim_gks($row['user_mobile']);
    $row['lang_name']=gks_lang_data_trans($row['lang_name'],$row['idd_lang'],'gks_lang','lang_name');
    $row['country_name']=gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');;
    $row['user_notes']=trim_gks(nl2br_gks($row['user_notes']));
    $row['sxolio']=nl2br_gks($row['sxolio']);
    $row['gks_price_total']=floatval($row['gks_price_total']);
    $row['gks_nickname_assigned']=trim_gks($row['gks_nickname_assigned']);
    $row['crm_channel_sale_descr']=trim_gks($row['crm_channel_sale_descr']);
    $row['crm_channel_contact_gks_nickname']=trim_gks($row['crm_channel_contact_gks_nickname']);
    $row['ads_campain_name']=trim_gks($row['ads_campain_name']);
    $row['crm_channel_url']=trim_gks($row['crm_channel_url']);
    $row['crm_channel_code']=trim_gks($row['crm_channel_code']);
    $row['crm_channel_text']=nl2br_gks($row['crm_channel_text']);
    
    
    $row['rstatusspan']='<div style="margin:4px 0px;"><span class=reservation_status_'.$row['reservation_status'].'>'.getHotelReservationStatusDescr($row['reservation_status']).'</span></div>';
    
    $reservation_data[$row['id_hotel_reservation']]=$row; 
  }
}

//print '<pre>';print_r($reservation_data);print '</pre>';die();
//print '<pre>';print_r($id_hotel_reservation_ids);print '</pre>';die();
//print '<pre>';print_r($rooms_array);print '</pre>';



include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>
<div class="container-fluid" style="margin-top: 20px;">
  <div class="row align-items-center">
    <div class="col-md-4" style="text-align:left">
        <button style=font-size:12pt;font-weight:bold;" type="button" class="btn btn-primary" id="dayminus" onclick="window.location.href='?hotel_id=<?php echo $id_hotel;?>&day=<?php
          echo ($mydaydif-$plusdays);?>&plusdays=<?php echo $plusdays;?>'">&lt; <?php 
          echo getWeekDayName(date('w', $today_vardia_time+ (-$plusdays * 24*60*60))).' '. showDate($today_vardia_time + (-$plusdays * 24*60*60), 'd/m/Y', 1) ?></button> 
    </div>
    <div class="col-md-4" style="text-align:center">
      
      <div class="row align-items-center" style="margin-bottom:4px;">
        <label for="hotel_id" class="col-md-6 col-form-label form-control-sm text-md-right text-center" style="font-size1: 120%;  font-weight1: bold;"><?php echo gks_lang('Ξενοδοχείο');?>:</label>
        <div class="col-md-6 text-md-left text-center">
          <select id="hotel_id" class="form-control form-control-sm">
            <option value="0"></option>
            <?php
            foreach ($user_hotels as $row_select) {
              echo '<option value="'.$row_select['id'].'" ';
              if ($row_select['id']==$id_hotel) echo ' selected ';
              echo '>'.$row_select['descr'].'</option>';
            }?>
          </select>    
        </div>
      </div>
      <div class="row align-items-center">
        <label for="mydatejump" class="col-md-6 col-form-label form-control-sm text-md-right text-center" style="font-size1: 120%;  font-weight1: bold;"><?php echo gks_lang('Ημερομηνία');?>:</label>
        <div class="col-md-6 text-md-left text-center">
          <input type="text" style="width:100px;display: inline-block;" id="mydatejump" name="mydatejump" class="form-control form-control-sm" value="<?php echo showDate($today_vardia_time, 'd/m/Y', 1); ?>"> 
          + 
          <select name="splusdays" id="splusdays" class="form-control form-control-sm" style="width:unset;display: inline-block;">
            <?php for ($ppd=1; $ppd<=365; $ppd++) {
              echo '<option value="'.$ppd.'" '.($plusdays == $ppd ? 'selected' : '').'>'.$ppd.'</option>';
            } ?>
          </select>
          <?php echo gks_lang('ημέρες');?>
        </div>
      </div>
    </div>
    <div class="col-md-4" style="text-align:right">
      <button style=font-size:12pt;font-weight:bold;" type="button" class="btn btn-primary" id="dayplus" onclick="window.location.href='?hotel_id=<?php echo $id_hotel;?>&day=<?php 
        echo ($mydaydif+$plusdays);?>&plusdays=<?php echo $plusdays;?>'"><?php 
        echo getWeekDayName(date('w', $today_vardia_time+ ($plusdays * 24*60*60))).' '. showDate($today_vardia_time + ($plusdays * 24*60*60), 'd/m/Y', 1) ?> &gt;</button>      
    </div>
  </div>
  
  


  <form method="get" action="?" id="sform">
  <input type="hidden" name="hotel_id" id="sshotel_id" value="<? echo $id_hotel;?>"/>
  <input type="hidden" name="day" id="sform_day" value="<? echo $mydaydif;?>"/>
  <input type="hidden" name="plusdays" id="plusdays" value="<? echo $plusdays;?>"/>
  <table align="center" width="96%" border="0" cellspacing=0 cellpadding=0>
    <tr>
      <td align="center" style="font-size:12pt;font-weight:bold">
        <?php echo gks_lang('Από');?>: <?php echo $date_fr_human;?> 
        <?php echo gks_lang('Έως');?>: <?php echo $date_to_human;?>
      </td>
    </tr>
  </table>
  </form>
</div>
<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<div class="container-fluid">

<table class="table table-sm table-responsive1 table-striped table-bordered" border="0" style="width:100%" cellspacing="0" cellpadding="5" align="center" id="plantable">
  <thead>
    <tr>	
      <th class="table-dark plan_th" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Τύπος / Δωμάτιο');?></th>
      <?php 
        $td_width=number_format(100/$plusdays,2,'.','');
        for ($cday=$date_fr_time; $cday<=$date_to_time; $cday+=24*60*60) {
        $dtext_day = date('w', $cday); //0 (for Sunday) through 6 (for Saturday)
        $dtext_day = mb_substr(getWeekDayName($dtext_day),0,2).' '. showDate($cday, 'd/m', 1); 
        echo '<th class="table-dark plan_th '.(intval(date('d', $cday))==1 ? ' plan_month1_cell' : '').'" scope="col" style="text-align: center !important;" width="'.$td_width.'%">'.$dtext_day.'</th>';
      }?>      
    </tr>
  </thead>
  <tbody>
<?php
  $i=0;
  
  foreach ($plandata as $myroom_type) {
    $i++;
    
    echo '<tr data-id-roomtype="'.$myroom_type['id_hotel_room_type'].'">';
      echo '<th '.($ismobile ? '' : 'nowrap').' class="plan_th" scope="row"><i class="fa fa-angle-down plan-room-type"></i> '.$myroom_type['room_type_descr'].'</th>';
      for ($cday=$date_fr_time; $cday<=$date_to_time; $cday+=24*60*60) {
        $dateval=date('Y-m-d', $cday);
        
        $myclass='';
        if ($myroom_type['room_type_status']!='available') {
          $myclass='plan_'.$myroom_type['room_type_status'];
        }
        $dwn= intval(date('w', $cday));
        if ($dwn == 0 or $dwn==6) $myclass.=' plan_sk_cell';
        if (intval(date('d', $cday))==1) $myclass.=' plan_month1_cell';
        if ($cday == $today_real_time) $myclass.=' plan_today_cell';
        else if ($cday < $today_real_time) $myclass.=' plan_past_cell';
        
//        echo $today_real_time;
//        echo '<br>';
//        echo $cday;
//        die();
                  

        
        echo '<td class="'.$myclass.'">';
        //echo $myclass;
        echo '</td>';
        
      }
    echo '</tr>'; 

    
    foreach ($myroom_type['rooms'] as $myroom) {
      $rtracks=0;
      if (isset($rooms_array['rooms'][$myroom['id_hotel_room']]['rtracks'])) $rtracks=$rooms_array['rooms'][$myroom['id_hotel_room']]['rtracks'];
      if ($rtracks==0) $rtracks=1;

      for ($ctrack = 1; $ctrack <= $rtracks; $ctrack++) {
        echo '<tr data-roomtype-id="'.$myroom['hotel_room_type_id'].'" data-id-room="'.$myroom['id_hotel_room'].'">';
        
        if ($ctrack == 1) {
          echo '<td class="plan_th" '.($ismobile ? '' : 'nowrap').' scope="row" style="'.($ismobile ? '' : 'text-indent:30px;').'" rowspan="'.$rtracks.'">'.$myroom['room_descr'].'</td>';
        }
        
        for ($cday=$date_fr_time; $cday<=$date_to_time; $cday+=24*60*60) {
          $dateval=date('Y-m-d', $cday);        
          $dateval_prev=date('Y-m-d', $cday - 24*60*60);        
          $dateval_next=date('Y-m-d', $cday + 24*60*60);        
          //echo '<br>';
          
          //print_r($rooms_array['rooms'][$myroom['id_hotel_room']]);
          
          $myclass='';
          if ($myroom_type['room_type_status']!='available') {
            $myclass='plan_'.$myroom_type['room_type_status'];
          } else if ($myroom['room_status']!='available') {
            $myclass='plan_'.$myroom['room_status'];
          } else {
            
          }
          $dwn= intval(date('w', $cday));
          if ($dwn == 0 or $dwn==6) $myclass.=' plan_sk_cell';
          if (intval(date('d', $cday))==1) $myclass.=' plan_month1_cell';
          if ($cday == $today_real_time) $myclass.=' plan_today_cell';
          else if ($cday < $today_real_time) $myclass.=' plan_past_cell';
          
          
          $hotel_reservation_id=0;
          $hotel_reservation_id_prev=0;
          $hotel_reservation_id_next=0;
          
          $hotel_reservation_status_id='';
          $hotel_reservation_status_id_prev='';
          $hotel_reservation_status_id_next='';
          
          $hotel_folio_id=0;
          $hotel_folio_id_prev=0;
          $hotel_folio_id_next=0;
          
          $priceperday=0;
          
          if (isset($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval]['folio'])) {
            foreach ($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval]['folio'] as $folio) {
              if ($folio['track'] == $ctrack) {
                $hotel_folio_id = $folio['hotel_folio_id'];
                $priceperday=$folio['priceperday'];
              }
            } 
          }
          if (isset($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval_prev]['folio'])) {
            foreach ($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval_prev]['folio'] as $folio) {
              if ($folio['track'] == $ctrack) {
                $hotel_folio_id_prev = $folio['hotel_folio_id'];
              }
            }
          }
          if (isset($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval_next]['folio'])) {
            foreach ($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval_next]['folio'] as $folio) {
              if ($folio['track'] == $ctrack) {
                $hotel_folio_id_next = $folio['hotel_folio_id'];
              }
            }
          }  
                
          if (isset($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval]['reservation'])) {
            foreach ($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval]['reservation'] as $reservation) {
              if ($reservation['track'] == $ctrack) {
                $hotel_reservation_id = $reservation['hotel_reservation_id'];
                $hotel_reservation_status_id=$reservation['dreservation_status']; 
                if ($priceperday==0) $priceperday=$reservation['priceperday'];
                //print '<pre>';print_r($reservation);die();
              }
            }
          }
          if (isset($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval_prev]['reservation'])) {
            foreach ($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval_prev]['reservation'] as $reservation) {
              if ($reservation['track'] == $ctrack) {
                $hotel_reservation_id_prev = $reservation['hotel_reservation_id'];
                $hotel_reservation_status_id_prev = $reservation['dreservation_status'];
              }
            }
          }
          if (isset($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval_next]['reservation'])) {
            foreach ($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval_next]['reservation'] as $reservation) {
              if ($reservation['track'] == $ctrack) {
                $hotel_reservation_id_next = $reservation['hotel_reservation_id'];
                $hotel_reservation_status_id_next = $reservation['dreservation_status'];
              }
            }
          }
            
          if ($hotel_folio_id == 0 and $hotel_reservation_id == 0) {
            if ($priceperday==0) $priceperday=$rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval]['price'];
          }
  
            
            
          if ($hotel_reservation_status_id=='') $hotel_reservation_status_id='010draft';
          if ($hotel_reservation_status_id_prev=='') $hotel_reservation_status_id_prev='010draft';
          if ($hotel_reservation_status_id_next=='') $hotel_reservation_status_id_next='010draft';
       
          $has_rf=false;
          $mytdtitle='';

          $my_data_resrv_folio='';
          
          if ($hotel_reservation_id > 0 or $hotel_folio_id > 0) {
            if ($hotel_reservation_id_prev > 0 or $hotel_folio_id_prev > 0) {
              if ($hotel_folio_id == $hotel_folio_id_prev and $hotel_folio_id>0) {
                $myclass.=' plan_f_full';
                $mytdtitle=gks_lang('Φάκελος').': '.$hotel_folio_id;
                $my_data_resrv_folio.='f'.$hotel_folio_id.'|';
              } else if ($hotel_reservation_id == $hotel_reservation_id_prev and $hotel_reservation_id > 0) {
                $myclass.=' plan_r_full_'.$hotel_reservation_status_id;
                $mytdtitle=gks_lang('Κράτηση').': <a href=admin-hotel-reservation-item.php?id='.$hotel_reservation_id.'>#'.$hotel_reservation_id.'</a><br>'.
                '<span class=reservation_status_'.$hotel_reservation_status_id.'>'.getHotelReservationStatusDescr($hotel_reservation_status_id).'</span>';
                $my_data_resrv_folio.='r'.$hotel_reservation_id.'|';
              } else {
                if ($hotel_folio_id > 0 and $hotel_folio_id_prev > 0) {
                  $myclass.=' plan_f_both';
                  $mytdtitle=gks_lang('Φάκελος').': '.$hotel_folio_id_prev.'<br>'.'Φάκελος: '.$hotel_folio_id;
                  $my_data_folio.='f'.$hotel_folio_id_prev.'|';
                  $my_data_folio.='f'.$hotel_folio_id.'|';
                } else if ($hotel_reservation_id > 0 and $hotel_reservation_id_prev > 0) {
                  $myclass.=' plan_r_both_'.$hotel_reservation_status_id_prev.'_'.$hotel_reservation_status_id;
                  //$mytdtitle=gks_lang('Κράτηση').': '.$hotel_reservation_id_prev.'<br>'.'Κράτηση: '.$hotel_reservation_id;
                  
                  $mytdtitle=gks_lang('Κράτηση').': <a href=admin-hotel-reservation-item.php?id='.$hotel_reservation_id_prev.'>#'.$hotel_reservation_id_prev.'</a><br>'.
                  '<span class=reservation_status_'.$hotel_reservation_status_id_prev.'>'.getHotelReservationStatusDescr($hotel_reservation_status_id_prev).'</span>';

                  $mytdtitle.='<br><br>'.gks_lang('Κράτηση').': <a href=admin-hotel-reservation-item.php?id='.$hotel_reservation_id.'>#'.$hotel_reservation_id.'</a><br>'.
                  '<span class=reservation_status_'.$hotel_reservation_status_id.'>'.getHotelReservationStatusDescr($hotel_reservation_status_id).'</span>';
  
                  $my_data_resrv_folio.='r'.$hotel_reservation_id_prev.'|';
                  $my_data_resrv_folio.='r'.$hotel_reservation_id.'|';
                  
                } else if ($hotel_folio_id_prev > 0 and $hotel_reservation_id > 0) {
                  $myclass.=' plan_fr_both';
                  $mytdtitle=gks_lang('Φάκελος').': '.$hotel_folio_id_prev.'<br>'.'Κράτηση: '.$hotel_reservation_id;
                  $my_data_resrv_folio.='f'.$hotel_folio_id_prev.'|';
                  $my_data_resrv_folio.='r'.$hotel_reservation_id.'|';
                  
                } else if ($hotel_reservation_id_prev > 0 and $hotel_folio_id > 0) {
                  $myclass.=' plan_rf_both';
                  $mytdtitle=gks_lang('Κράτηση').': '.$hotel_reservation_id_prev.'<br>'.'Φάκελος: '.$hotel_folio_id;
                  $my_data_resrv_folio.='r'.$hotel_reservation_id_prev.'|';
                  $my_data_resrv_folio.='f'.$hotel_folio_id.'|';
                  
                  
                }
                
                
              }
              $has_rf=true;
            } else {
              if ($hotel_folio_id > 0) {
                $myclass.=' plan_f_right';
                $mytdtitle=gks_lang('Φάκελος').': '.$hotel_folio_id;
                $my_data_resrv_folio.='f'.$hotel_folio_id.'|';
              } else {
                $myclass.=' plan_r_right_'.$hotel_reservation_status_id;
                $mytdtitle=gks_lang('Κράτηση').': <a href=admin-hotel-reservation-item.php?id='.$hotel_reservation_id.'>#'.$hotel_reservation_id.'</a><br>'.
                '<span class=reservation_status_'.$hotel_reservation_status_id.'>'.getHotelReservationStatusDescr($hotel_reservation_status_id).'</span>';
                $my_data_resrv_folio.='r'.$hotel_reservation_id.'|';
              }
              $has_rf=true;
            }
            
          } else {
            if ($hotel_reservation_id_prev > 0 or $hotel_folio_id_prev > 0) {
              if ($hotel_folio_id_prev > 0) {
                $myclass.=' plan_f_left';
                $mytdtitle=gks_lang('Φάκελος').': '.$hotel_folio_id_prev;
                $my_data_resrv_folio.='f'.$hotel_folio_id_prev.'|';
              } else {
                $myclass.=' plan_r_left_'.$hotel_reservation_status_id_prev;
                $mytdtitle=gks_lang('Κράτηση').': <a href=admin-hotel-reservation-item.php?id='.$hotel_reservation_id_prev.'>#'.$hotel_reservation_id_prev.'</a><br>'.
                '<span class=reservation_status_'.$hotel_reservation_status_id_prev.'>'.getHotelReservationStatusDescr($hotel_reservation_status_id_prev).'</span>';
                $my_data_resrv_folio.='r'.$hotel_reservation_id_prev.'|';
              }
              $has_rf=true;
            }
          }
          
          $val1 = 1;
          if (isset($rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval]['val1'])) $val1 = $rooms_array['rooms'][$myroom['id_hotel_room']]['days'][$dateval]['val1'];
          if ($val1==0) $myclass.=' plan_disable';

          //$myclass=' plan_test';
                   
          
          if ($my_data_resrv_folio != '') $myclass = 'mytdtt '.trim_gks($myclass);
          //echo '<td class="mytd '.$myclass.'" '.($mytdtitle!='' ? ' title="'.$mytdtitle.'"' : '').'>';
          //echo '<td class="mytd '.$myclass.'" '.($mytdtitle!='' ? ' title="'.$mytdtitle.'"' : '').' >';
          echo '<td class="mytd '.$myclass.'" '.($my_data_resrv_folio!='' ? ' data-resrv_folio="'.$my_data_resrv_folio.'"' : '').' >';
          //echo $myclass.' '.$val;
  //        echo $hotel_reservation_id_prev.'|'.$hotel_reservation_id.'|'.$hotel_reservation_id_next;
  //        echo '<br>';
  //        echo $hotel_folio_id_prev.'|'.$hotel_folio_id.'|'.$hotel_folio_id_next;
  //        echo '<br>';
  
          if ($priceperday !=0) {
            echo myNumberFormatNo0Local(round($priceperday,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL));
          } else {
            echo '&nbsp;'; 
          }
          echo '</td>';
  
        }
        
        echo '</tr>';     
        
      }
    
    
    }
  } 


?>
  </tbody>    
</table>
</div>
<?php
//print '<pre>';
//print_r($rooms_array);
//print_r($plandata);
//print '</pre>';


//echo $today_vardia;
//echo '<br>';
//echo $today_vardia_time;
//echo '<br>';
//echo $today_day;
//echo '<br>';
//echo $date_fr;
//echo '<br>';
//echo $date_to;



?>



<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_mydaydif = <?php echo $mydaydif;?>;
var from_php_mynow_Y = <?php echo  date('Y',_time_user(time(),1)-GKS_ERP_START_VARDIA*60*60)?>;
var from_php_mynow_m = <?php echo (date('m',_time_user(time(),1)-GKS_ERP_START_VARDIA*60*60) - 1)?>;
var from_php_mynow_d = <?php echo  date('d',_time_user(time(),1)-GKS_ERP_START_VARDIA*60*60)?>;

var from_php_reservation_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($reservation_data));?>'));







  
</script>
  
<script src="js/admin-hotel-plan.js?v=<?php echo $gks_cache_version;?>"></script>


<?php 
include_once('_my_footer_admin.php');  