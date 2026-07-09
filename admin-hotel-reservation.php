<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();





$my_page_title=gks_lang('Κρατήσεις');
$nav_active_array=array('hotel','hotel_reservation');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_reservation','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');


$perm_edit=gks_permission_user_can_action_php($my_wp_user_id, 'gks_hotel_reservation','edit',0);
$perm_delete=gks_permission_user_can_action_php($my_wp_user_id, 'gks_hotel_reservation','delete',0);


$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_reservation',['from'=>'list']);

$user_hotels=gks_get_hotels_list();

////$testroomid=5;
////$roomaf=get_availability_rooms('2019-02-20','2019-02-23', false,0,0,0,0);
////$outdata=array();
////$msg_aval_not_out='';
////
////$price_array = array();
////$ajia_total_out = 0;
////if (isset($roomaf['rooms'][$testroomid]))  {
////  echo time(); 
////  foreach ($roomaf['rooms'][$testroomid]['days'] as $myday => $day) {
////    if ($day['val1']==0) {
////      
////      $msg_aval_not_out.=date('d/m', strtotime($myday)).'<br>';
////    }
////    if (isset($price_array[$day['price']]) == false) {
////      $price_array[$day['price']] = array();
////    }
////    $price_array[$day['price']][] = $myday;
////    $ajia_total_out+=$day['price'];
////  }  
////}
////$msg_price_out='';
////foreach ($price_array as $price => $item) {
////  $msg_price_out.=count($item).'x'.myCurrencyFormat($price,false,false).'<br>';
////} 
////
//////echo '<br>';
//////echo date('d/m/Y H:i:s',1550793600);
//////echo '<br>';
//////echo date('d/m/Y H:i:s',1550808000);
//////die();
////
////print time();
////print '<pre>';
////print $ajia_total_out;
////print "\r\n";
////print $msg_price_out;
////print "\r\n";
////print $msg_aval_not_out;
////print "\r\n";
////print_r($price_array);
////print "\r\n";
////print_r($outdata);
////print_r($roomaf);
////
////die();



$today_vardia_this = date('Y-m-d',_time_user(time(), 1));

$filters = array();
if (count($user_hotels)>=1) {
  $vals=array();
  foreach ($user_hotels as $key=>$value) {
    $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "gks_hotel_reservation.hotel_id=".$value['id']);
  } 
  $filters[] = array(
    'name' => 'fhotel_id',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ξενοδοχείο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "1=1",
    'vals' => $vals,
  );  
}

$filters[] = array(
    'name' => 'fstatus',
    'class' => 'filterselectbox',
    'style' => 'filterselectbox1 ui-state-default ui-corner-all',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_reservation.reservation_status = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
        array('value' => -11, 'text' => getHotelReservationStatusDescr('005prodraft'),      'sql' => "gks_hotel_reservation.reservation_status='005prodraft'"),
        array('value' => -12, 'text' => getHotelReservationStatusDescr('010draft'),         'sql' => "gks_hotel_reservation.reservation_status='010draft'"),
        array('value' => -13, 'text' => getHotelReservationStatusDescr('040cancelled'),     'sql' => "gks_hotel_reservation.reservation_status='040cancelled'"),
        array('value' => -14, 'text' => getHotelReservationStatusDescr('050rejected'),      'sql' => "gks_hotel_reservation.reservation_status='050rejected'"),
        array('value' => -15, 'text' => getHotelReservationStatusDescr('070wait_payment'),  'sql' => "gks_hotel_reservation.reservation_status='070wait_payment'"),
        array('value' => -16, 'text' => getHotelReservationStatusDescr('080confirm'),       'sql' => "gks_hotel_reservation.reservation_status='080confirm'"),
        array('value' => -17, 'text' => getHotelReservationStatusDescr('100completed'),     'sql' => "gks_hotel_reservation.reservation_status='100completed'"),
        array('value' => -18, 'text' => getHotelReservationStatusDescr('110payment'),       'sql' => "gks_hotel_reservation.reservation_status='110payment'"),
    ),
    'sql' => "SELECT reservation_status as descr, reservation_status as id 
    FROM gks_hotel_reservation where reservation_status is not null and reservation_status not in ('005prodraft','010draft','040cancelled','050rejected','070wait_payment','080confirm','100completed','110payment') 
    GROUP BY reservation_status ORDER BY reservation_status",
);

$filters[] = array(
			'name' => 'fcheck',
			'class' => 'filterselectbox ui-state-default ui-corner-all',
			'style' => '',
		  'title' => gks_lang('Ημερομηνία'),
			'has_custom_date' => true,
			'field' => 'gks_hotel_reservation.check_in',
			'has_custom_default' => 1,
//		'mywherepos'=>1,
			'vals' => array(
  			        array('value' => 1, 'text' => gks_lang('Όλα'),     'sql' => "1=1"),
								array('value' => 25,
											'text' => vardia_name($today_vardia_this, 8),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_reservation.check_out   < DATE_ADD('{$today_vardia_this}', INTERVAL 9 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_reservation.check_in < DATE_ADD('{$today_vardia_this}', INTERVAL 9 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_reservation.check_out   > DATE_ADD('{$today_vardia_this}', INTERVAL 9 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 24,
											'text' => vardia_name($today_vardia_this, 7),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_reservation.check_out   < DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_reservation.check_in < DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_reservation.check_out   > DATE_ADD('{$today_vardia_this}', INTERVAL 8 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 23,
											'text' => vardia_name($today_vardia_this, 6),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_reservation.check_out   < DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_reservation.check_in < DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_reservation.check_out   > DATE_ADD('{$today_vardia_this}', INTERVAL 7 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 22,
											'text' => vardia_name($today_vardia_this, 5),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_reservation.check_out   < DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_reservation.check_in < DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_reservation.check_out   > DATE_ADD('{$today_vardia_this}', INTERVAL 6 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 21,
											'text' => vardia_name($today_vardia_this, 4),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_reservation.check_out   < DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_reservation.check_in < DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_reservation.check_out   > DATE_ADD('{$today_vardia_this}', INTERVAL 5 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 20,
											'text' => vardia_name($today_vardia_this, 3),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_reservation.check_out   < DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_reservation.check_in < DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_reservation.check_out   > DATE_ADD('{$today_vardia_this}', INTERVAL 4 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 19,
											'text' => vardia_name($today_vardia_this, 2),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_reservation.check_out   < DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_reservation.check_in < DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_reservation.check_out   > DATE_ADD('{$today_vardia_this}', INTERVAL 3 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 3,
											'text' => gks_lang('Αύριο'),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_reservation.check_out   < DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_reservation.check_in < DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_reservation.check_out   > DATE_ADD('{$today_vardia_this}', INTERVAL 2 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 5,
											'text' => gks_lang('Σήμερα'),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= '{$today_vardia_this}' and gks_hotel_reservation.check_out   < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)) or
											(gks_hotel_reservation.check_in >= '{$today_vardia_this}' and gks_hotel_reservation.check_in < DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)) or
											(gks_hotel_reservation.check_in <= '{$today_vardia_this}' and gks_hotel_reservation.check_out   > DATE_ADD('{$today_vardia_this}', INTERVAL 1 DAY)) or 
											(gks_hotel_reservation.check_in <= '{$today_vardia_this}' and gks_hotel_reservation.check_out is null))"),
								array('value' => 7,
											'text' => gks_lang('Χθες'),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_reservation.check_out   < DATE_SUB('{$today_vardia_this}', INTERVAL 0 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_reservation.check_in < DATE_SUB('{$today_vardia_this}', INTERVAL 0 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_reservation.check_out   > DATE_SUB('{$today_vardia_this}', INTERVAL 0 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 9,
											'text' => vardia_name($today_vardia_this, -2),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_reservation.check_out   < DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_reservation.check_in < DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_reservation.check_out   > DATE_SUB('{$today_vardia_this}', INTERVAL 1 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 10,
											'text' => vardia_name($today_vardia_this, -3),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_reservation.check_out   < DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_reservation.check_in < DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_reservation.check_out   > DATE_SUB('{$today_vardia_this}', INTERVAL 2 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 11,
											'text' => vardia_name($today_vardia_this, -4),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_reservation.check_out   < DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_reservation.check_in < DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_reservation.check_out   > DATE_SUB('{$today_vardia_this}', INTERVAL 3 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 12,
											'text' => vardia_name($today_vardia_this, -5),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_reservation.check_out   < DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_reservation.check_in < DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_reservation.check_out   > DATE_SUB('{$today_vardia_this}', INTERVAL 4 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 13,
											'text' => vardia_name($today_vardia_this, -6),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_reservation.check_out   < DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_reservation.check_in < DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_reservation.check_out   > DATE_SUB('{$today_vardia_this}', INTERVAL 5 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 14,
											'text' => vardia_name($today_vardia_this, -7),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_reservation.check_out   < DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_reservation.check_in < DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_reservation.check_out   > DATE_SUB('{$today_vardia_this}', INTERVAL 6 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY) and gks_hotel_reservation.check_out is null))"),
								array('value' => 15,
											'text' => vardia_name($today_vardia_this, -8),
											'sql' => "
										 ((gks_hotel_reservation.check_out   >= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_reservation.check_out   < DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY)) or
											(gks_hotel_reservation.check_in >= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_reservation.check_in < DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY)) or
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_reservation.check_out   > DATE_SUB('{$today_vardia_this}', INTERVAL 7 DAY)) or 
											(gks_hotel_reservation.check_in <= DATE_SUB('{$today_vardia_this}', INTERVAL 8 DAY) and gks_hotel_reservation.check_out is null))"),

							)
);



$filters[] = array(
  'name' => 'fcin',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία Από'),
  'has_custom_date' => true,
  'field' => 'gks_hotel_reservation.check_in',
  'has_custom_default' => 1,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_hotel_reservation.check_in','future'=>true,'today'=>$today_vardia_this, 'today_vardia'=>$today_vardia_this, 'set_vardia'=>false]),


);

$filters[] = array(
	'name' => 'fcout',
	'class' => 'filterselectbox ui-state-default ui-corner-all',
	'style' => '',
  'title' => gks_lang('Ημερομηνία Έως'),
	'has_custom_date' => true,
	'field' => 'gks_hotel_reservation.check_out',
	'has_custom_default' => 2,
//		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_hotel_reservation.check_out','future'=>true,'today'=>$today_vardia_this, 'today_vardia'=>$today_vardia_this, 'set_vardia'=>false]),
);


$filters[] = array(
    'name' => 'fnum_days',
    'class' => 'filterselectbox ui-state-default ui-corner-all',
    'style' => '',
    'title' => gks_lang('Διανυκτερεύσεις'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_reservation.num_days = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT num_days as id, num_days as descr FROM gks_hotel_reservation where num_days>0 GROUP BY num_days order by num_days",
);

$filters[] = array(
    'name' => 'flang',
    'class' => 'filterselectbox ui-state-default ui-corner-all',
    'style' => '',
    'title' => gks_lang('Γλώσσα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_reservation.user_lang = '%V%'",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_hotel_reservation.user_lang as id, gks_lang.lang_name as descr
    FROM gks_hotel_reservation LEFT JOIN gks_lang ON gks_hotel_reservation.user_lang = gks_lang.id_lang
    WHERE (((gks_lang.id_lang) Is Not Null))
    GROUP BY gks_hotel_reservation.user_lang, gks_lang.lang_name
    ORDER BY gks_lang.lang_name;",
);

$filters[] = array(
    'name' => 'fcountry',
    'class' => 'filterselectbox ui-state-default ui-corner-all',
    'style' => '',
    'title' => gks_lang('Χώρα'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_reservation.ma_country_id = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT gks_hotel_reservation.ma_country_id as id, gks_country.country_name as descr
    FROM gks_hotel_reservation LEFT JOIN gks_country ON gks_hotel_reservation.ma_country_id = gks_country.id_country
    WHERE (((gks_country.id_country) Is Not Null))
    GROUP BY gks_hotel_reservation.ma_country_id, gks_country.country_name
    ORDER BY gks_country.country_name",
);

$filters[] = array(
    'name' => 'fadults',
    'class' => 'filterselectbox ui-state-default ui-corner-all',
    'style' => '',
    'title' => gks_lang('Ενήλικες'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_reservation.num_adults = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT num_adults as id, num_adults as descr FROM gks_hotel_reservation where num_adults>0 GROUP BY num_adults order by num_adults",
);

$filters[] = array(
    'name' => 'fchilds',
    'class' => 'filterselectbox ui-state-default ui-corner-all',
    'style' => '',
    'title' => gks_lang('Παιδιά'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_reservation.num_childs = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT num_childs as id, num_childs as descr FROM gks_hotel_reservation where num_childs>0 GROUP BY num_childs order by num_childs",
);

$filters[] = array(
    'name' => 'froomsn',
    'class' => 'filterselectbox ui-state-default ui-corner-all',
    'style' => '',
    'title' => gks_lang('Πλήθος Δωματίων'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "tblroomsnum.rooms_num = %V%",
    'vals' => array(
//        array('value' => -10, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "select cc as id, cc as descr from (
      SELECT hotel_reservation_id, count(id_hotel_reservation_room) AS cc
      FROM gks_hotel_reservation_room
      GROUP BY hotel_reservation_id
    ) as tt group by cc order by cc",
);


$filters[] = array(
    'name' => 'fassigned',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ανάθεση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_hotel_reservation.assigned_id=%V%",
    'vals' => array(
        //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
    ),
    'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
    FROM (gks_hotel_reservation 
    LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.assigned_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
    
".(count($perm_id_hotel_ids)>0 ? " and gks_hotel_reservation.hotel_id in (".implode(',',$perm_id_hotel_ids).")" : "")."
".(count($perm_id_company_ids)>0 ? " and gks_hotel.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_hotel.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_hotel_reservation.reservation_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_hotel_reservation.reservation_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
    
    GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
);

if ($GKS_CRM_ENABLE) {
  $filters[] = array(
      'name' => 'fchannel',
      'class' => 'filterselectbox',
      'style' => '',
      'title' => gks_lang('Κανάλι Πωλήσεων'),
      'has_custom_default' => -1,
      'multiselect' => true,
      'field'  => "gks_hotel_reservation.crm_channel_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_crm_channel_sale.id_crm_channel_sale AS id, gks_crm_channel_sale.crm_channel_sale_descr AS descr
      FROM (gks_hotel_reservation 
      LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)
      LEFT JOIN gks_crm_channel_sale ON gks_hotel_reservation.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale
      WHERE (((gks_crm_channel_sale.id_crm_channel_sale) Is Not Null))
".(count($perm_id_hotel_ids)>0 ? " and gks_hotel_reservation.hotel_id in (".implode(',',$perm_id_hotel_ids).")" : "")."
".(count($perm_id_company_ids)>0 ? " and gks_hotel.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_hotel.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_hotel_reservation.reservation_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_hotel_reservation.reservation_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
      GROUP BY gks_crm_channel_sale.id_crm_channel_sale, gks_crm_channel_sale.crm_channel_sale_descr, gks_crm_channel_sale.crm_channel_sale_sortorder
      ORDER BY gks_crm_channel_sale.crm_channel_sale_sortorder",
  );
  $filters[] = array(
      'name' => 'fchcontact',
      'class' => 'filterselectbox',
      'style' => '',
      'title' => gks_lang('Επαφή Πωλήσεων'),
      'has_custom_default' => -1,
      'multiselect' => true,
      'field'  => "gks_hotel_reservation.crm_channel_contact_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
      FROM (gks_hotel_reservation 
      LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
".(count($perm_id_hotel_ids)>0 ? " and gks_hotel_reservation.hotel_id in (".implode(',',$perm_id_hotel_ids).")" : "")."
".(count($perm_id_company_ids)>0 ? " and gks_hotel.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_hotel.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_hotel_reservation.reservation_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_hotel_reservation.reservation_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
      GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
      ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
  );
  $filters[] = array(
      'name' => 'fcampain',
      'class' => 'filterselectbox',
      'style' => '',
      'title' => gks_lang('Καμπάνια'),
      'has_custom_default' => -1,
      'multiselect' => true,
      'field'  => "gks_hotel_reservation.crm_channel_campain_id=%V%",
      'vals' => array(
          //array('value' => -1, 'text' => gks_lang('Όλα'),          'sql' => "1=1"),
      ),
      'sql' => "SELECT gks_ads_campain.id_ads_campain as id, gks_ads_campain.ads_campain_name as descr
      FROM (gks_hotel_reservation 
      LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)
      LEFT JOIN gks_ads_campain ON gks_hotel_reservation.crm_channel_campain_id = gks_ads_campain.id_ads_campain
      WHERE (((gks_ads_campain.id_ads_campain) Is Not Null))
".(count($perm_id_hotel_ids)>0 ? " and gks_hotel_reservation.hotel_id in (".implode(',',$perm_id_hotel_ids).")" : "")."
".(count($perm_id_company_ids)>0 ? " and gks_hotel.company_id in (".implode(',',$perm_id_company_ids).")" : "")."
".(count($perm_id_company_sub_ids)>0 ? " and gks_hotel.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")" : "")."
".(count($perm_id_acc_journal_ids)>0 ? " and gks_hotel_reservation.reservation_journal_id in (".implode(',',$perm_id_acc_journal_ids).")" : "")."
".(count($perm_id_acc_seira_ids)>0 ? " and gks_hotel_reservation.reservation_seira_id in (".implode(',',$perm_id_acc_seira_ids).")" : "")."
      
      GROUP BY gks_ads_campain.id_ads_campain, gks_ads_campain.ads_campain_name
      ORDER BY gks_ads_campain.ads_campain_name",
  );
}

$filters=array_merge($filters,$gks_custom_prepare['sql_filters']);




$sortable = array(
		array('name' => 'soid', 'field' => 'gks_hotel_reservation.id_hotel_reservation'),
		array('name' => 'sostatus', 'field' => 'gks_hotel_reservation.reservation_status'),
		array('name' => 'soprice', 'field' => 'gks_hotel_reservation.gks_price_total'),
		array('name' => 'sodates', 'field' => 'gks_hotel_reservation.check_in, gks_hotel_reservation.check_out'),
		array('name' => 'sodays', 'field' => 'gks_hotel_reservation.num_days'),
		array('name' => 'socustomer', 'field' => GKS_WP_TABLE_PREFIX.'users.gks_nickname, gks_hotel_reservation.user_last_name, gks_hotel_reservation.user_first_name'),
		array('name' => 'solang', 'field' => 'gks_lang.lang_name'),
		array('name' => 'socountry', 'field' => 'gks_country.country_name'),
		array('name' => 'soadults', 'field' => 'gks_hotel_reservation.num_adults'),
		array('name' => 'sochilds', 'field' => 'gks_hotel_reservation.num_childs'),
		array('name' => 'sorn', 'field' => 'tblroomsnum.rooms_num'),

		
		array('name' => 'soassigned', 'field' => GKS_WP_TABLE_PREFIX.'users_assigned.gks_nickname'),
		array('name' => 'sochannel', 'field' => 'gks_crm_channel_sale.crm_channel_sale_descr'),
		array('name' => 'sochcontact', 'field' => GKS_WP_TABLE_PREFIX.'users_crm_channel_contact.gks_nickname'),
		array('name' => 'socampain', 'field' => 'gks_ads_campain.ads_campain_name'),
		array('name' => 'socrmcode', 'field' => 'gks_hotel_reservation.crm_channel_code'),


);
$sortable=array_merge($sortable,$gks_custom_prepare['sql_sortable']);

$search_fields = array(
'gks_hotel_reservation.reservation_guid',
'gks_hotel_reservation.reservation_status',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
'gks_hotel_reservation.user_last_name',
'gks_hotel_reservation.user_first_name',
'gks_hotel_reservation.user_mobile',
'gks_hotel_reservation.user_email',
'gks_lang.lang_name',
'gks_country.country_name',
'gks_hotel_reservation.sxolio',
'gks_hotel_reservation.crm_channel_code',
'gks_hotel_reservation.hotel_booking_number',
);
$search_fields=array_merge($search_fields,$gks_custom_prepare['sql_search_fields']);



$filter = array('html' => '', 'sql' => '', 'url' => '');
$search_string_value = (isset($_GET['search_string']) ? $_GET['search_string'] : '');
makeFilters($filters, $filter, $_GET,true,true,$search_string_value);




$search_where = make_search_where($search_string_value,$search_fields);
$search_where = !empty($search_where) ? ' AND '.$search_where : '';
//echo $search_where;
//die();

//$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
//$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';

$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
//$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';

$sorted = array('sql' => '', 'url' => '');

makeSortable($sortable, $sorted, $_GET);
											


$rows_per_page = $_gks_session['gks']['rows_per_page'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

$showFrom = $page * $rows_per_page;
$showTo = $showFrom + $rows_per_page;


$sql = "SELECT SQL_CALC_FOUND_ROWS gks_hotel_reservation.*, 
".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_country.country_name, gks_nomoi.nomos_descr, 
gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
gks_country.country_initials,gks_country.country_name,
gks_lang.lang_ico, gks_lang.lang_name,
tblroomsnum.rooms_num,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_hotel.hotel_color
".$gks_custom_prepare['sql_all_list_sele']."
FROM ".$gks_custom_prepare['sql_all_list_from']." (((((((((((((gks_hotel_reservation 
".$gks_custom_prepare['sql_all_list_left']."

LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_reservation.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_reservation.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_country ON gks_hotel_reservation.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_hotel_reservation.ma_nomos_id = gks_nomoi.id_nomos) 
LEFT JOIN gks_eshop_fiscal_position ON gks_hotel_reservation.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_hotel_reservation.pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_lang ON gks_hotel_reservation.user_lang = gks_lang.id_lang)
LEFT JOIN (
  SELECT hotel_reservation_id, Count(id_hotel_reservation_room) AS rooms_num FROM gks_hotel_reservation_room GROUP BY hotel_reservation_id
) as tblroomsnum ON gks_hotel_reservation.id_hotel_reservation = tblroomsnum.hotel_reservation_id)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_hotel_reservation.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_hotel_reservation.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_hotel_reservation.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_hotel_reservation.crm_channel_campain_id = gks_ads_campain.id_ads_campain

where 1=1 ".$where . $search_where;
if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_reservation.hotel_id in (".implode(',',$perm_id_hotel_ids).")";
if (count($perm_id_company_ids)>0) $sql.=" and gks_hotel.company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_hotel.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_hotel_reservation.reservation_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_hotel_reservation.reservation_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";



if (empty($sorted['sql'])) {
	$sql .= " ORDER BY id_hotel_reservation desc";
} else {
	$sql .= " ORDER BY " . $sorted['sql'];
}
$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo '<pre>'.$sql;die();
	
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');

$sql_numrows = "SELECT FOUND_ROWS() AS `found_rows`;";
$res_numrows = $db_link->query($sql_numrows);
$row_numrows = $res_numrows->fetch_assoc();
$total_records = $row_numrows['found_rows'];

$pages = ceil($total_records / $rows_per_page) - 1;

$paging = array('records' => '', 'total' => '', 'pages' => '');
$url = $_SERVER['SCRIPT_NAME'].'?';
$params='';
if (isset($filter['url']) && $filter['url']!='') $params.='&'.$filter['url'];
if (isset($sorted['url']) && $sorted['url']!='') $params.='&'.$sorted['url'];
if (isset($_GET['search_string']) && $_GET['search_string']!='') $params.='&search_string='.urlencode($_GET['search_string']);




pagination($pages, $page, $total_records, $url, $paging, false, $params);
    
$sortable_url='?';
if (isset($filter['url']) && $filter['url']!='') $sortable_url.='&'.$filter['url'];
if (isset($page) && $page>0) $sortable_url.='&page='.$page;
if (isset($_GET['search_string']) && $_GET['search_string']!='') $sortable_url.='&search_string='.urlencode($_GET['search_string']);

$sortfields = explode("=", $sorted['url']);
if (count($sortfields) < 2) {
    $sortfields[0] = '';
    $sortfields[1] = '';
}




$_gks_session['gks']['recordback'] = $_SERVER['REQUEST_URI'];
gks_erp_cookie_save();

$gks_customtableview_user_settings=gks_customtableview_get_user_settings();

include_once('_my_header_admin.php');
?>
<link href="css/_gks_customtableview.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style class="gks_customtableview_style" data-index="1" data-rs=".gkstable" data-rs-pa=".gkstable > thead > tr > th">
<?php echo gks_customtableview_render_css($gks_customtableview_user_settings,1);?>
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-6" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
    <div class="col-sm-6" style="text-align:center">
      <a class="btn btn-primary gks_add_new_record" href="admin-hotel-reservation-item.php?id=-1"><?php echo gks_lang('Προσθήκη νέας κράτησης');?></a>
      <?php echo gks_customtableview_php_generate($gks_customtableview_user_settings);?>
    </div>
  </div>
</div>





<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<?php mytablepages($paging, $total_records); ?>
<table class="table table-sm table-responsive1 table-striped table-bordered table-hover1 gkstable <?php
  echo $gks_customtableview_user_settings['class'][1];
  ?>" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr >	
    <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' ><a href="?">#</a></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostatus', gks_lang('Κατάσταση')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice', gks_lang('Τιμή')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodates', gks_lang('Ημερομηνίες')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sodays', '<span class="tooltipster" class="tooltipster" title="'.gks_lang('Διανυκτερεύσεις').'">'.gks_lang('Διαν').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="20%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socustomer', gks_lang('Πελάτης')); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php 
      echo makeSortLink($sortable, $sortable_url, $_GET, 'solang', gks_lang('Γλώσσα')).'<br>'; 
      echo makeSortLink($sortable, $sortable_url, $_GET, 'socountry', gks_lang('Χώρα')); 
    ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php 
      echo makeSortLink($sortable, $sortable_url, $_GET, 'soadults', '<span class="tooltipster" title="'.gks_lang('Ενήλικες').'">'.gks_lang('Ενη').'</span>').'<br>'; 
      echo makeSortLink($sortable, $sortable_url, $_GET, 'sochilds', '<span class="tooltipster" title="'.gks_lang('Παιδιά').'">'.gks_lang('Παι').'</span>'); 
    ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sorn', '<span class="tooltipster" title="'.gks_lang('Πλήθος Δωματίων').'">'.gks_lang('ΠΔ').'</span>'); ?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="25%" ><?php echo gks_lang('Δωμάτια');?></th>
    <th class="table-dark" scope="col" style="text-align: center !important;" width="25%" ><?php echo gks_lang('Σχόλιο');?></th>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soassigned', gks_lang('Ανάθεση')); ?></th>        
<?php if ($GKS_CRM_ENABLE) {?>
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sochannel', gks_lang('Κανάλι')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sochcontact', '<span class="tooltipster" title="'.gks_lang('Επαφή Πωλήσεων').'">'.gks_lang('Επαφή Π').'</span>'); ?></th>                
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socampain', gks_lang('Καμπάνια')); ?></th>        
    <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socrmcode', '<span class="tooltipster" title="'.gks_lang('Κωδικός CRM').'">'.gks_lang('Κωδικός').'</span>'); ?></th>        
<?php } ?>
    
<?php 
echo gks_custom_table_list_header($gks_custom_prepare);
?>    
             
  </tr>
</thead>
<tbody>
  
    <?php
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_hotel_reservation'];?>">
    <th scope="row" nowrap class="mytdcm"><?php echo ($i + $showFrom);?></th>

    <td nowrap class="mytdcm p-0">
      <table class="tableids3col">
        <tr>
          <td><a href="admin-hotel-reservation-item.php?id=<?php echo $row['id_hotel_reservation'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
          <td><?php echo $row['id_hotel_reservation'];?></td>
          <?php if ($perm_delete) {?>
          <td><i class="fas fa-trash-alt deleterow perm_delete" data-id="<?php echo $row['id_hotel_reservation'];?>" data-model="gks_hotel_reservation"></i></td>
          <?php } ?>
        </tr>      
      </table>
      

      <?php if (!empty($row['hotel_booking_number'])) {?>
      <table cellpadding=0 cellspacing=0 class="ref_number_table">
        <tr class="ref_number_table_tr">
          <td class="ref_number_table_td">
            <span class="ref_number_table_td_span" style="background-color:<?php echo $row['hotel_color'];?>;"><?php echo $row['hotel_booking_number'];?></span>
          </td>
        </tr>  
      </table>
      <?php } ?>
    </td>          
    <td nowrap class="mytdcm"><span class="reservation_status_<?php echo $row['reservation_status'];?>"><?php echo getHotelReservationStatusDescr($row['reservation_status']);?></span></td>
    <td nowrap class="mytdcm"><?php if ($row['gks_price_total']<>0) echo myCurrencyFormat($row['gks_price_total']);?></td>
    <td nowrap class="mytdcm"><?php if (isset($row['check_in'])) echo myDateTimeFormatw(strtotime($row['check_in'])) ;?><br><?php if (isset($row['check_out'])) echo myDateTimeFormatw(strtotime($row['check_out'])) ;?></td>
    <td nowrap class="mytdcm"><?php echo $row['num_days'];?></td>
    
    
    <td nowrap class="mytdcm"><?php 
      $out='';
      if ($row['user_id']>0) {
        if (!empty($row['gks_nickname'])) $out.='<a href="admin-users-item.php?id="'.$row['user_id'].'>'.$row['gks_nickname'].'</a><br>';
      } else {
        if (!empty($row['user_last_name']) or !empty($row['user_first_name'])) $out.=$row['user_last_name'].' '.$row['user_first_name'].'<br>';
      }
      if (!empty($row['user_mobile'])) $out.= '<a href="tel:'.$row['user_mobile'].'">'.$row['user_mobile'].'</a><br>';
      if (!empty($row['user_email'])) $out.= '<a href="mailto:'.$row['user_email'].'">'.$row['user_email'].'</a><br>';
      if (strlen($out)>4) $out=substr($out, 0,strlen($out)-4);
      echo $out;
      ?>
    </td>
    <td nowrap class="mytdcm"><?php 
      if (isset($row['lang_name'])) echo '<img src="/my/img/flags/flags_iso/32/'.strtolower($row['lang_ico']).'.png" title="'.$row['lang_name'].'">';
      if (isset($row['country_name'])) echo '<br><img src="/my/img/flags/flags_iso/32/'.strtolower($row['country_initials']).'.png" title="'.$row['country_name'].'">';
      ?></td>
    <td nowrap class="mytdcm"><?php 
      $roomline='';
      if ($row['num_adults']>0)
        $roomline.='<i class="fa fa-male" style="color:#aaaaaa;"></i>'.$row['num_adults'];
      if ($row['num_childs']>0)  
        $roomline.=($roomline=='' ? '' : '<br>'). '<i class="fa fa-child" style="color:#aaaaaa;font-size:70%;"></i>'.$row['num_childs'];
      echo $roomline;  
        
    ?></td>
    <td nowrap class="mytdcm"><?php echo $row['rooms_num'];?></td>
    
    <td nowrap class="mytdcml"><?php
      $sql_rooms="SELECT gks_hotel_reservation_room.*, gks_hotel_room.room_descr, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
      gks_lang.lang_name, gks_lang.lang_ico, 
      gks_country.country_initials, gks_country.country_name
      FROM (((gks_hotel_reservation_room 
      LEFT JOIN gks_hotel_room ON gks_hotel_reservation_room.hotel_room_id = gks_hotel_room.id_hotel_room) 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation_room.ruser_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
      LEFT JOIN gks_lang ON gks_hotel_reservation_room.ruser_lang = gks_lang.id_lang) 
      LEFT JOIN gks_country ON gks_hotel_reservation_room.ruser_ma_country_id = gks_country.id_country
      where hotel_reservation_id=".$row['id_hotel_reservation']."
      ORDER BY gks_hotel_room.room_descr;";
      $result_rooms = $db_link->query($sql_rooms);        
      if (!$result_rooms) debug_mail(false,'error sql',$sql_rooms);
      if (!$result_rooms) die('sql error');
      $list=array();
      while ($row_room = $result_rooms->fetch_assoc()) {
        $myroom=$row_room['room_descr'];
        $roomline=($row_room['rnum_adults']==0 ? '' : '<i class="fa fa-male" style="color:#aaaaaa;"></i>'.$row_room['rnum_adults']).
                  ($row_room['rnum_childs']==0 ? '' :' <i class="fa fa-child" style="color:#aaaaaa;font-size:70%;"></i>'.$row_room['rnum_childs']);
        if ($roomline != '')  $myroom.=' '.$roomline;
        $myroom.='<br>';
        
        if ($row_room['ruser_id']<=-1) {
          //$myroom.= gks_lang('Ίδιος πελάτης').'<br>';
        } else if ($row_room['ruser_id']>0 and $row['user_id'] != $row_room['ruser_id']) {
          $myroom.= '<a href="admin-users-item.php?id="'.$row_room['ruser_id'].'>'.$row_room['gks_nickname'].'</a><br>';
          if ($row['user_mobile'] != $row_room['ruser_mobile'] and !empty($row_room['ruser_mobile'])) $myroom.= '<a href="tel:'.$row_room['ruser_mobile'].'">'.$row_room['ruser_mobile'].'</a><br>';
          if ($row['user_email'] != $row_room['ruser_email'] and !empty($row_room['ruser_email'])) $myroom.= '<a href="mailto:'.$row_room['ruser_email'].'">'.$row_room['ruser_email'].'</a><br>';
        } else if (($row_room['ruser_last_name']!='' or $row_room['ruser_first_name']!='') and ($row['user_last_name']!= $row_room['ruser_last_name'] or $row['user_last_name'] != $row_room['ruser_last_name'])) {
          $myroom.= $row_room['ruser_last_name'].' '.$row_room['ruser_first_name'].'<br>';
          if ($row['user_mobile'] != $row_room['ruser_mobile'] and !empty($row_room['ruser_mobile'])) $myroom.= '<a href="tel:'.$row_room['ruser_mobile'].'">'.$row_room['ruser_mobile'].'</a><br>';
          if ($row['user_email'] != $row_room['ruser_email'] and !empty($row_room['ruser_email'])) $myroom.= '<a href="mailto:'.$row_room['ruser_email'].'">'.$row_room['ruser_email'].'</a><br>';
        }
        
        $roomline='';
        if ($row['lang_name'] != $row_room['lang_name']) {
          if (isset($row_room['lang_name'])) $roomline.= '<img src="/my/img/flags/flags_iso/16/'.strtolower($row_room['lang_ico']).'.png" title="'.$row_room['lang_name'].'">';
        }
        if ($row['country_name'] != $row_room['country_name']) {
          if (isset($row_room['country_name'])) $roomline.= '<img src="/my/img/flags/flags_iso/16/'.strtolower($row_room['country_initials']).'.png" title="'.$row_room['country_name'].'">';
        }
        if($roomline!= '') $myroom.=$roomline.'<br>';
        
        if (endwith($myroom,'<br>')) $myroom=substr($myroom, 0, strlen($myroom)-4);
        $list[] = $myroom;
      }
      if (count($list)==1) {
        echo $list[0];
      } else {
        echo '<ol style="padding-inline-start:18px;margin-bottom:0px;">';
        foreach ($list as $myroom) {
          echo '<li>'.$myroom.'</li>';
        } 
        echo '</ol>';        
        
      }
      

      
    ?></td>  
    
    <td class="mytdcml"><?php echo nl2br_gks(htmlspecialchars_gks($row['sxolio'],ENT_QUOTES));?></td>
    
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['assigned_id'];?>"><?php echo $row['gks_nickname_assigned'];?></a></td>
<?php if ($GKS_CRM_ENABLE) {?>    
    <td class="mytdcml"><a href="admin-crm-channel-sale-item.php?id=<?php echo $row['crm_channel_id'];?>"><?php echo $row['crm_channel_sale_descr'];?></a></td>
    <td class="mytdcml"><a href="admin-users-item.php?id=<?php echo $row['crm_channel_contact_id'];?>"><?php echo $row['crm_channel_contact_gks_nickname'];?></a></td>
    <td class="mytdcml"><a href="admin-ads-campain-item.php?id=<?php echo $row['crm_channel_campain_id'];?>"><?php echo $row['ads_campain_name'];?></a></td>
    <td class="mytdcm"><?php echo $row['crm_channel_code'];?></a></td>
<?php } ?>
    
<?php
  echo gks_custom_table_list_rows($gks_custom_prepare,$row);
?> 
    
    
  </tr>
<?php    
    }
?>

</tbody>
</table>
<?php mytablepages($paging, $total_records); ?>

 



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_reservation','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_reservation','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_reservation','delete',0);?>;

var gks_customtableview_data=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_customtableview_user_settings['data']))?>'));
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  
  $('#fcin-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fcin-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('#fcout-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fcout-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fcin' || sname == 'fcout' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fcin' || sname == 'fcout' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });  
  


});
</script>

<script src="js/_gks_customtableview.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
//db_close();
include_once('_my_footer_admin.php');


