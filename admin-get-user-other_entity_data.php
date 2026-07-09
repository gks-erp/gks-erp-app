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
$doc_table='';if (isset($_POST['doc_table'])) $doc_table=trim_gks($_POST['doc_table']);
$address_extra=-1;if (isset($_POST['address_extra'])) $address_extra=intval($_POST['address_extra']);


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
$extra_address=array();
$extra_address[]=array(
  'id'=>-1,
  'descr' => gks_lang('Κεντρικό'),
);

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
$extra_address_exist=false;
while ($row_select = $result_select->fetch_assoc()) {
  $extra_address[]=array(
    'id'=>intval($row_select['id_users_extra_address']),
    'descr' => trim_gks($row_select['ea_name']),
  );
  if ($address_extra==intval($row_select['id_users_extra_address'])) {
    $extra_address_exist=true;
  }
}
if ($address_extra>0 and $extra_address_exist==false) $address_extra=-1;

$data=array();
$ret=gks_other_entity_get_data($doc_table,-1,$id,$address_extra);

$data['html']=$ret['html'];
$data['extra_address']=$extra_address;

$return=array('success' => true, 'message' => base64_encode('OK'),'data'=>$data);

echo json_encode($return); die();

