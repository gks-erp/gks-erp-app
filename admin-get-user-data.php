<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();




$my_page_title=gks_lang('Λήψη δεδομένων επαφής');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','autocomplete',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$id=0; if (isset($_POST['id'])) $id=intval($_POST['id']);




$sql_user="SELECT ".GKS_WP_TABLE_PREFIX."users.*, gks_users.pelati_sxolio, gks_users.order_sxolio,  
gks_users.ma_odos, gks_users.ma_branch, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_nomos_id, gks_nomoi.nomos_descr, gks_users.ma_country_id, gks_country.country_name,gks_country.country_ee,
gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
table_last_name.mylast_name, table_first_name.myfirst_name, table_mobile.mymoobile,
gks_users.phone_home,".GKS_WP_TABLE_PREFIX."users.user_url,
gks_users.ma_latitude,gks_users.ma_longitude,
gks_users.genisi_date,gks_lang.lang_name,
gks_users.gemi_number,gks_users.is_b2g,gks_users.b2g_aaht_code,gks_users.b2g_aaht_name,
gks_users.b2g_aaht_foreas,gks_users.b2g_aaht_typos_forea,gks_users.b2g_aaht_kodikos_ekatharisis


FROM ((((((".GKS_WP_TABLE_PREFIX."users
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
)  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
)  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mymoobile
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='mobile'))
)  AS table_mobile ON ".GKS_WP_TABLE_PREFIX."users.ID = table_mobile.user_id) 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang

WHERE ".GKS_WP_TABLE_PREFIX."users.ID=".$id." limit 1";
$result_user = $db_link->query($sql_user);        
if (!$result_user) {
  debug_mail(false,'error sql',$sql_user);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
}
if ($result_user->num_rows!=1) {
  debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
  echo json_encode($return); die();  
}

$row_user = $result_user->fetch_assoc();
$row_user['country_name']=gks_lang_data_trans($row_user['country_name'],$row_user['ma_country_id'],'gks_country','country_name');
$row_user['nomos_descr']=gks_lang_data_trans($row_user['nomos_descr'],$row_user['ma_nomos_id'],'gks_nomoi','nomos_descr');

$pelati_sxolio=nl2br_gks($row_user['pelati_sxolio']);
$order_sxolio=nl2br_gks($row_user['order_sxolio']);



$addressL1=trim($row_user['ma_odos'].' '.$row_user['ma_arithmos']).', ';

if ($row_user['ma_orofos']!='') $addressL1.=$row_user['ma_orofos'].', ';
if ($row_user['ma_perioxi']!='') $addressL1.=$row_user['ma_perioxi'].', ';
if (endwith($addressL1,',')) $addressL1=substr($addressL1, 0, strlen($addressL1)-1);
$addressL2=trim_gks((empty($row_user['ma_poli']) ? '' : $row_user['ma_poli'].', ').
                (empty($row_user['nomos_descr']) ? '' : $row_user['nomos_descr'].', ').
                $row_user['ma_tk']);
if (endwith($addressL2,',')) $addressL1=substr($addressL2, 0, strlen($addressL2)-1);
$addressL3=$row_user['country_name'];

$address='';
if ($addressL1!='') $address.=$addressL1.'<br>';
if ($addressL2!='') $address.=$addressL2.'<br>';
$address.=$addressL3;
if (endwith($address,'<br>')) $address=substr($address, 0, strlen($address)-4);

//echo $gks_user_settings['lang']['backend'];die();
$extra_address=array();
if (isset($_POST['lead_id']) or isset($_POST['task_id']) or isset($_POST['machine_id'])) {
  $extra_address[]=array('id' => -1, 'descr'=> gks_lang('Βασική διεύθυνση'));
} else {
  $extra_address[]=array('id' => -1, 'descr'=> gks_lang('Αποστολή στην ίδια διεύθυνση'));
}

$sql="SELECT gks_users_extra_address.*, country_name,nomos_descr
FROM (gks_users_extra_address 
LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
WHERE (((gks_users_extra_address.user_id)=".$id."))
ORDER BY gks_users_extra_address.id_users_extra_address";
$result_select = $db_link->query($sql);        
if (!$result_select) {
  debug_mail(false,'error sql',$sql);
  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
}
$ea_ids=array();         
while ($row_select = $result_select->fetch_assoc()) {
  $row_select['country_name']=gks_lang_data_trans($row_select['country_name'],$row_select['ea_country_id'],'gks_country','country_name');
  $row_select['nomos_descr']=gks_lang_data_trans($row_select['nomos_descr'],$row_select['ea_nomos_id'],'gks_nomoi','nomos_descr');

  $address_name=$row_select['ea_name'].', '.trim_gks($row_select['ea_odos'].' '.$row_select['ea_arithmos']).', '.$row_select['ea_orofos'].', '.$row_select['ea_perioxi'].', '.$row_select['ea_poli'].', '.$row_select['ea_tk'].', '.$row_select['country_name'].', '.$row_select['nomos_descr'];

  $address_name=str_replace(', , ', ', ', $address_name);
  $address_name=str_replace(', , ', ', ', $address_name);
  $address_name=str_replace(', , ', ', ', $address_name);
  $address_name=str_replace(', , ', ', ', $address_name);
  
  if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
  if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
  if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);

  if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
  if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
  if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
  $address_name=trim_gks($address_name);
  
  $extra_address[]=array('id' => intval($row_select['id_users_extra_address']), 'descr'=> $address_name);
  $ea_ids[]=intval($row_select['id_users_extra_address']);
}

if (isset($_POST['machine_id'])) {

} else {
  $extra_address[]=array('id' => 0, 'descr'=> '-- '.gks_lang('Δημιουργία νέας διεύθυνσης').' --');
}

$parastatiko=0;
$address_extra=-1;

if (isset($_POST['page']) and $_POST['page']=='reservation') {
  $sql="select parastatiko from gks_hotel_reservation where user_id=".$id;
  if (isset($_POST['reservation_id']) and intval($_POST['reservation_id'])>0) {
    $sql.=" and id_hotel_reservation<".intval($_POST['reservation_id']);
  }
  $sql.=" order by id_hotel_reservation desc limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $parastatiko=intval($row['parastatiko']);
  }
} else {
  $sql="select parastatiko,address_extra from gks_orders where user_id=".$id;
  if (isset($_POST['order_id']) and intval($_POST['order_id'])>0) {
    $sql.=" and id_order<".intval($_POST['order_id']);
  }
  $sql.=" order by id_order desc limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  }
  if ($result->num_rows==1) {
    $row = $result->fetch_assoc();
    $parastatiko=intval($row['parastatiko']);
    $temp=intval($row['address_extra']);
    if (in_array($temp,$ea_ids)) $address_extra=$temp;
    
  }
}


if (isset($_POST['order_id']) and intval($_POST['order_id'])>0) {
  $balance_user_before=gks_balance_calc(['id' => $id, 'except_id_order' => intval($_POST['order_id'])]);
} else if (isset($_POST['acc_inv_id']) and intval($_POST['acc_inv_id'])>0) {
  $balance_user_before=gks_balance_calc(['id' => $id, 'except_id_acc_inv' => intval($_POST['acc_inv_id'])]);
} else {
  $balance_user_before=$row_user['gks_balance'];
}

$def_phone='';
$sql_def_phone="SELECT comm_value
FROM gks_users_communication
WHERE user_id=".$id." AND comm_primary=1 AND comm_type='phone' and comm_value<>'' order by id_user_communication desc";
$result_def_phone = $db_link->query($sql_def_phone);        
if (!$result_def_phone) {
  debug_mail(false,'error sql',$sql_def_phone);
  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
}
if ($result_def_phone->num_rows>=1) {
  $row_def_phone = $result_def_phone->fetch_assoc();
  $def_phone=trim_gks($row_def_phone['comm_value']);
}

$user_comms_email='';
$user_comms_phone='';
$user_comms_url='';
$user_comms=gks_get_user_communications($id);
if (isset($user_comms['email'])) {
  $temp=array();
  foreach ($user_comms['email'] as $value) $temp[]=$value['html'];
  $user_comms_email= implode('<br>', $temp);
}
if (isset($user_comms['phone'])) {
  $temp=array();
  foreach ($user_comms['phone'] as $value) $temp[]=$value['html'];
  $user_comms_phone= implode('<br>', $temp);
}
if (isset($user_comms['url'])) {
  $temp=array();
  foreach ($user_comms['url'] as $value) $temp[]=$value['html'];
  $user_comms_url= implode('<br>', $temp);
}

$return = array('success' => true, 'message' => base64_encode('OK'), 
'id' => intval($row_user['ID']),
'gks_nickname' => $row_user['gks_nickname'],
'first_name' => $row_user['myfirst_name'],
'last_name' => $row_user['mylast_name'],
'email' => $row_user['user_email'],
'email_link' => (trim_gks($row_user['user_email'])=='' ? '' : '<a href="mailto:'.trim_gks($row_user['user_email']).'">'.trim_gks($row_user['user_email']).'</a><input id="dr_user_email" type="hidden" value="'.trim_gks($row_user['user_email']).'">'),
'mobile' => $row_user['mymoobile'],
'mobile_link' => (trim_gks($row_user['mymoobile'])=='' ? '' : '<a href="tel:'.trim_gks($row_user['mymoobile']).'">'.trim_gks($row_user['mymoobile']).'</a>'),
'phone_home' => $row_user['phone_home'],
'phone_home_link' => (trim_gks($row_user['phone_home'])=='' ? '' : '<a href="tel:'.trim_gks($row_user['phone_home']).'">'.trim_gks($row_user['phone_home']).'</a>'),
'def_phone' => $def_phone,
'def_phone_link' => (trim_gks($def_phone)=='' ? '' : '<a href="tel:'.trim_gks($def_phone).'">'.trim_gks($def_phone).'</a>'),
'user_url' => $row_user['user_url'],
'user_comms_email' => $user_comms_email,
'user_comms_phone' => $user_comms_phone,
'user_comms_url' => $user_comms_url,
'lang' => $row_user['gks_lang'],
'lang_name' => trim_gks($row_user['lang_name']),
'pelati_sxolio' => $pelati_sxolio ,
'order_sxolio' => $order_sxolio, 
'address' => $address , 
'ma_branch' => trim_gks($row_user['ma_branch']),
'ma_odos' => trim_gks($row_user['ma_odos']),
'ma_arithmos' => trim_gks($row_user['ma_arithmos']),
'ma_orofos' => trim_gks($row_user['ma_orofos']),
'ma_perioxi' => trim_gks($row_user['ma_perioxi']),
'ma_poli' => trim_gks($row_user['ma_poli']),
'ma_tk' => trim_gks($row_user['ma_tk']),
'ma_country_id' => intval($row_user['ma_country_id']),
'country_name' => trim_gks($row_user['country_name']),
'country_ee' => trim_gks($row_user['country_ee']),
'ma_nomos_id' => intval($row_user['ma_nomos_id']),
'nomos_descr' => trim_gks($row_user['nomos_descr']),
'eponimia' => trim_gks($row_user['eponimia']),
'title' => trim_gks($row_user['title']),
'afm' => trim_gks($row_user['afm']),
'doy' => trim_gks($row_user['doy']),
'epaggelma' => trim_gks($row_user['epaggelma']),
'extra_address' =>$extra_address,
'parastatiko' =>$parastatiko,
'address_extra' =>$address_extra,
'pricelist_id' => $row_user['pricelist_id'],
'fiscal_position_id' => $row_user['fiscal_position_id'],
'generic_ekprosi' => $row_user['generic_ekprosi'],
'ma_latitude' => $row_user['ma_latitude'],
'ma_longitude' => $row_user['ma_longitude'],
'genisi_date' => ((isset($row_user['genisi_date']) and $row_user['genisi_date'] !='') ? date('d/m/Y', strtotime($row_user['genisi_date'])) : ''),

'balance_user_before' => floatval($balance_user_before),

'gemi_number'=>trim_gks($row_user['gemi_number']),
'is_b2g'=>(intval($row_user['is_b2g'])==1),
'b2g_aaht_code'=>trim_gks($row_user['b2g_aaht_code']),
'b2g_aaht_name'=>trim_gks($row_user['b2g_aaht_name']),
'b2g_aaht_foreas'=>trim_gks($row_user['b2g_aaht_foreas']),
'b2g_aaht_typos_forea'=>trim_gks($row_user['b2g_aaht_typos_forea']),
'b2g_aaht_kodikos_ekatharisis'=>trim_gks($row_user['b2g_aaht_kodikos_ekatharisis']),

);
echo json_encode($return); die();
