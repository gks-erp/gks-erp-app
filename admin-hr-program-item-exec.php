<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Προγράμματος Υπαλλήλων').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hr_program',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}

gks_get_hr_program_status($hr_program_status,$hr_program_status_styles);
gks_get_hr_program_vardia($hr_program_vardia,$hr_program_vardia_styles);


$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  

  
  $sql_row ="SELECT gks_hr_program.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname,".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile as user_mobile,
  table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name,
  gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
  gks_users.order_sxolio,gks_users.pelati_sxolio,
  gks_lang.lang_name, ".GKS_WP_TABLE_PREFIX."users.gks_lang as user_lang,
  gks_users.ma_odos,gks_users.ma_arithmos,gks_users.ma_orofos,gks_users.ma_perioxi,gks_users.ma_poli,gks_users.ma_tk,
  gks_users.ma_country_id,gks_country.country_name,
  gks_users.ma_nomos_id,gks_nomoi.nomos_descr,
  gks_company.company_title, gks_company_subs.company_sub_title,
  ".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned,
  gks_production_posta.production_posto_descr
  FROM ((((((((((((gks_hr_program
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_hr_program.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_hr_program.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_company ON gks_hr_program.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_hr_program.company_sub_id = gks_company_subs.id_company_sub) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hr_program.hr_program_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
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
  LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_hr_program.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID)
  LEFT JOIN gks_production_posta ON gks_hr_program.hr_program_posto_id = gks_production_posta.id_production_posto

  where gks_hr_program.id_hr_program = ".$id;
  $result = $db_link->query($sql_row);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_row);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row_old = $result->fetch_assoc();

  $gks_custom_prepare=gks_custom_table_item_prepare('gks_hr_program',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 
  
  
}
$company_id=0;if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$company_sub_id=0;if (isset($_POST['company_sub_id'])) $company_sub_id=intval($_POST['company_sub_id']);

if ($_POST['hr_program_date'] == '__/__/____ __:__') $_POST['hr_program_date']='';
$hr_program_date=trim_gks(stripslashes(urldecode($_POST['hr_program_date'])));
if ($hr_program_date!='') {
  $hr_program_date = mystrtodb($hr_program_date);
}
if ($hr_program_date=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Ημερομηνία Καταχώρησης')));
  echo json_encode($return); die();}  

$hr_program_status_id=1;if (isset($_POST['hr_program_status_id'])) $hr_program_status_id=intval($_POST['hr_program_status_id']);
if ($hr_program_status_id<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Κατάσταση')));
  echo json_encode($return); die();}  


$hr_program_vardia_id=0;if (isset($_POST['hr_program_vardia_id'])) $hr_program_vardia_id=intval($_POST['hr_program_vardia_id']);
if ($hr_program_vardia_id<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την Βάρδια')));
  echo json_encode($return); die();}  

$sql_ergas="select * from gks_hr_program_vardia where id_hr_program_vardia=".$hr_program_vardia_id;
$result_ergas = $db_link->query($sql_ergas);  
if (!$result_ergas) {
  debug_mail(false,'error sql',$sql_ergas);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  

$hr_program_vardia_is_ergasia=0;
if ($result_ergas->num_rows>=1) {
  $row_ergas = $result_ergas->fetch_assoc();
  $hr_program_vardia_is_ergasia=intval($row_ergas['hr_program_vardia_is_ergasia']);
}
$hr_program_is_ergasia=$hr_program_vardia_is_ergasia;

$hr_program_user_id=0; if (isset($_POST['hr_program_user_id'])) $hr_program_user_id=intval($_POST['hr_program_user_id']);
if ($hr_program_user_id<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Υπάλληλος')));
  echo json_encode($return); die();}  

$hr_program_posto_id=0; if (isset($_POST['hr_program_posto_id'])) $hr_program_posto_id=intval($_POST['hr_program_posto_id']);
if ($hr_program_vardia_is_ergasia==0) $hr_program_posto_id=0;
if ($hr_program_vardia_is_ergasia==1 and $hr_program_posto_id<=0) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Πόστο')));
  echo json_encode($return); die();
}



if ($_POST['hr_program_date_from'] == '__/__/____ __:__') $_POST['hr_program_date_from']='';
$hr_program_date_from=trim_gks(stripslashes(urldecode($_POST['hr_program_date_from'])));
if ($hr_program_date_from!='') {
  $hr_program_date_from = mystrtodb($hr_program_date_from);
}
if ($hr_program_date_from=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την ημερομηνία <b>Από</b>')));
  echo json_encode($return); die();}  


if ($_POST['hr_program_date_to'] == '__/__/____ __:__') $_POST['hr_program_date_to']='';
$hr_program_date_to=trim_gks(stripslashes(urldecode($_POST['hr_program_date_to'])));
if ($hr_program_date_to!='') {
  $hr_program_date_to = mystrtodb($hr_program_date_to);
}
if ($hr_program_date_to=='') {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την ημερομηνία <b>Έως</b>')));
  echo json_encode($return); die();}  

if (strtotime($hr_program_date_to)<strtotime($hr_program_date_from)) {
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί η ημερομηνία <b>Έως</b> να είναι μικρότερη από την <b>Από</b> στον <b>Προγραμματισμός</b>')));
  echo json_encode($return); die();} 

$ccf=$hr_program_date_from;
$cct=$hr_program_date_to;

$sql="select * from gks_hr_program
where id_hr_program<>".$id."
and hr_program_user_id=".$hr_program_user_id."
and (
  (hr_program_date_from <'".$ccf."' and hr_program_date_to>'".$ccf."') or 
  (hr_program_date_from <'".$cct."' and hr_program_date_to>'".$cct."') or 
  (hr_program_date_from >'".$ccf."' and hr_program_date_to<'".$cct."') or 
  (hr_program_date_from <'".$ccf."' and hr_program_date_to>'".$cct."')
)";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Υπάρχει ήδη σχετική καταχώρηση για αυτόν τον υπάλληλο σε παράμοιο χρονικό διάστημα:<br><a href="admin-hr-program-item.php?id=[1]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$row['id_hr_program'],$message);  
  debug_mail(false,'exist',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}





$hr_program_name=''; if (isset($_POST['hr_program_name'])) $hr_program_name=trim_gks(base64_decode($_POST['hr_program_name']));
//if ($hr_program_name=='') {debug_mail(false,'emptyl',            gks_lang('Η Περιγραφή δεν μπορεί να είναι κενό'));
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η Περιγραφή δεν μπορεί να είναι κενό')));
//  echo json_encode($return); die(); }

$hr_program_descr=''; if (isset($_POST['hr_program_descr'])) $hr_program_descr=trim_gks(base64_decode($_POST['hr_program_descr']));
$hr_program_color=''; if (isset($_POST['hr_program_color'])) $hr_program_color=trim_gks(base64_decode($_POST['hr_program_color']));
$internal_note=''; if (isset($_POST['internal_note'])) $internal_note=trim_gks(base64_decode($_POST['internal_note']));
$assigned_id=0; if (isset($_POST['assigned_id'])) $assigned_id=intval($_POST['assigned_id']);


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_hr_program');

$sql_custom_data="select * from gks_customt_gks_hr_program where hr_program_id=".$id;
$result_custom_data = $db_link->query($sql_custom_data);  
if (!$result_custom_data) {
  debug_mail(false,'error sql',$sql_custom_data);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  
  
$row_custom_old=[];
if ($result_custom_data->num_rows>=1) {
  $row_custom_old = $result_custom_data->fetch_assoc();
}

//echo '<pre>';print_r($gks_custom_save_prepare);die();

$redirect='';
if ($id==-1) {
  $sql="insert into gks_hr_program (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-hr-program-item.php?id='.$id); 
  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_hr_program_log (hr_program_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
    
}

$sql="update gks_hr_program set 
company_id=".$company_id.",
company_sub_id=".$company_sub_id.",
hr_program_date=".($hr_program_date == '' ? 'null' : "'".$db_link->escape_string($hr_program_date)."'") .", 
hr_program_is_ergasia=".$hr_program_is_ergasia.",
hr_program_status_id=".$hr_program_status_id.",
hr_program_vardia_id=".$hr_program_vardia_id.",
hr_program_date_from=".($hr_program_date_from == '' ? 'null' : "'".$db_link->escape_string($hr_program_date_from)."'") .", 
hr_program_date_to=".($hr_program_date_to == '' ? 'null' : "'".$db_link->escape_string($hr_program_date_to)."'") .", 
hr_program_color=". ($hr_program_color =='' ? 'null' : "'".$db_link->escape_string($hr_program_color)."'").",
hr_program_name='".$db_link->escape_string($hr_program_name)."',
hr_program_descr='".$db_link->escape_string($hr_program_descr)."',
hr_program_user_id=".$hr_program_user_id.",
hr_program_posto_id=".$hr_program_posto_id.",
internal_note='".$db_link->escape_string($internal_note)."',
assigned_id=".$assigned_id.",

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_hr_program = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


if ($is_new_rec == false) {

  $result = $db_link->query($sql_row);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_row);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();}
  $row_new = $result->fetch_assoc();
  
 





  $sxolio_log='';

  if (trim_gks($row_old['hr_program_date']) != trim_gks($row_new['hr_program_date'])) 
    $sxolio_log.=gks_lang('Καταχώρηση').': <b>'.(isset($row_old['hr_program_date']) ? showDate(strtotime($row_old['hr_program_date']), 'd/m/Y H:i', 1) : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['hr_program_date']) ? showDate(strtotime($row_new['hr_program_date']), 'd/m/Y H:i', 1) : '').'</b>'.'<br>';
  if ($row_old['hr_program_status_id'].'' != $row_new['hr_program_status_id'].'') 
    $sxolio_log.=gks_lang('Κατάσταση').': '.
    '<span class="hr_program_status_'.$row_old['hr_program_status_id'].'">'.(isset($hr_program_status[$row_old['hr_program_status_id']]) ? $hr_program_status[$row_old['hr_program_status_id']]['hr_program_status_descr'] : '').'</span>'.
    ' [[-r]] '.
    '<span class="hr_program_status_'.$row_new['hr_program_status_id'].'">'.(isset($hr_program_status[$row_new['hr_program_status_id']]) ? $hr_program_status[$row_new['hr_program_status_id']]['hr_program_status_descr'] : '').'</span>'.
    '<br>';
    
  if (trim_gks($row_old['hr_program_user_id']) != trim_gks($row_new['hr_program_user_id'])) 
    $sxolio_log.=gks_lang('Υπάλληλος').': <b><a href="admin-users-item.php?id='.$row_old['hr_program_user_id'].'">'.trim_gks($row_old['gks_nickname']).'</a></b> [[-r]] '.
    '<b><a href="admin-users-item.php?id='.$row_new['hr_program_user_id'].'">'.trim_gks($row_new['gks_nickname']).'</a></b>'.'<br>';
  if (trim_gks($row_old['hr_program_posto_id']) != trim_gks($row_new['hr_program_posto_id'])) 
    $sxolio_log.=gks_lang('Πόστο').': <b><a href="admin-production-posta-item.php?id='.$row_old['hr_program_posto_id'].'">'.trim_gks($row_old['production_posto_descr']).'</a></b> [[-r]] '.
    '<b><a href="admin-production-posta-item.php?id='.$row_new['hr_program_posto_id'].'">'.trim_gks($row_new['production_posto_descr']).'</a></b>'.'<br>';
  if ($row_old['hr_program_vardia_id'].'' != $row_new['hr_program_vardia_id'].'') 
    $sxolio_log.=gks_lang('Κατάσταση').': '.
    '<span class="hr_program_vardia_'.$row_old['hr_program_vardia_id'].'">'.(isset($hr_program_vardia[$row_old['hr_program_vardia_id']]) ? $hr_program_vardia[$row_old['hr_program_vardia_id']]['hr_program_vardia_descr'] : '').'</span>'.
    ' [[-r]] '.
    '<span class="hr_program_vardia_'.$row_new['hr_program_vardia_id'].'">'.(isset($hr_program_vardia[$row_new['hr_program_vardia_id']]) ? $hr_program_vardia[$row_new['hr_program_vardia_id']]['hr_program_vardia_descr'] : '').'</span>'.
    '<br>';

  if (trim_gks($row_old['hr_program_date_from']) != trim_gks($row_new['hr_program_date_from'])) 
    $sxolio_log.=gks_lang('Ημερομηνία').' '.gks_lang('Από').': <b>'.(isset($row_old['hr_program_date_from']) ? showDate(strtotime($row_old['hr_program_date_from']), 'd/m/Y H:i', 1) : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['hr_program_date_from']) ? showDate(strtotime($row_new['hr_program_date_from']), 'd/m/Y H:i', 1) : '').'</b>'.'<br>';

  if (trim_gks($row_old['hr_program_date_to']) != trim_gks($row_new['hr_program_date_to'])) 
    $sxolio_log.=gks_lang('Ημερομηνία').' '.gks_lang('Έως').': <b>'.(isset($row_old['hr_program_date_to']) ? showDate(strtotime($row_old['hr_program_date_to']), 'd/m/Y H:i', 1) : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['hr_program_date_to']) ? showDate(strtotime($row_new['hr_program_date_to']), 'd/m/Y H:i', 1) : '').'</b>'.'<br>';
  if (trim_gks($row_old['hr_program_name']) != trim_gks($row_new['hr_program_name'])) 
    $sxolio_log.=gks_lang('Περιγραφή').': <b>'.(isset($row_old['hr_program_name']) ? $row_old['hr_program_name'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['hr_program_name']) ? $row_new['hr_program_name'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['hr_program_descr']) != trim_gks($row_new['hr_program_descr'])) 
    $sxolio_log.=gks_lang('Σχόλιο').':<br>'.(isset($row_old['hr_program_descr']) ? ($row_old['hr_program_descr']) : '').'<br>[[-r]]<br>'.
    ''.(isset($row_new['hr_program_descr']) ? ($row_new['hr_program_descr']) : '').''.'<br>';


  if (trim_gks($row_old['hr_program_color']) != trim_gks($row_new['hr_program_color'])) 
    $sxolio_log.=gks_lang('Χρώμα').': <b>'.(isset($row_old['hr_program_color']) ? '<span style="background-color:'.$row_old['hr_program_color'].';display: inline-block;width:20px;height:20px;vertical-align: bottom;"> </span>' : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['hr_program_color']) ? '<span style="background-color:'.$row_new['hr_program_color'].';display: inline-block;width:20px;height:20px;vertical-align: bottom;"> </span>' : '').'</b>'.'<br>';

  if (trim_gks($row_old['internal_note']) != trim_gks($row_new['internal_note'])) 
    $sxolio_log.=gks_lang('Εσωτερική Σημείωση').':<br><b>'.(isset($row_old['internal_note']) ? nl2br_gks($row_old['internal_note']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['internal_note']) ? nl2br_gks($row_new['internal_note']) : '').'</b>'.'<br>';

  if (intval($row_old['assigned_id']) != intval($row_new['assigned_id']))
    $sxolio_log.=gks_lang('Ανάθεση σε').': <b>'.trim_gks($row_old['gks_nickname_assigned']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['gks_nickname_assigned']).'</b>'.'<br>';

  if ((isset($row_old['company_id']) and isset($row_new['company_id']) == false) or 
      (isset($row_old['company_id']) == false and isset($row_new['company_id'])) or 
      $row_old['company_id'] != $row_new['company_id']) 
    $sxolio_log.=gks_lang('Εταιρεία').': <b>'.(isset($row_old['company_title']) ? $row_old['company_title'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['company_title']) ? $row_new['company_title'] : '').'</b>'.'<br>';

  if ((isset($row_old['company_sub_id']) and isset($row_new['company_sub_id']) == false) or 
      (isset($row_old['company_sub_id']) == false and isset($row_new['company_sub_id'])) or 
      $row_old['company_sub_id'] != $row_new['company_sub_id']) {
    if ($row_old['company_sub_id']==0) $row_old['company_sub_title']=gks_lang('Κεντρικό');
    if ($row_new['company_sub_id']==0) $row_new['company_sub_title']=gks_lang('Κεντρικό');
    $sxolio_log.=gks_lang('Υποκατάστημα').': <b>'.(isset($row_old['company_sub_title']) ? $row_old['company_sub_title'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['company_sub_title']) ? $row_new['company_sub_title'] : '').'</b>'.'<br>';
  }
  
  $gks_custom_prepare=gks_custom_table_item_prepare('gks_hr_program',['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;
  


  if ($sxolio_log == '') $sxolio_log=gks_lang('Ενημέρωση').'<br>';
 
  
  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into gks_hr_program_log (hr_program_id, add_date,user_id,sxolio) values (
    ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio_log)."')";
    
    //$return = array('success' => false, 'message' => base64_encode($sql));
    //echo json_encode($return); die();  
     
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }  
  }  
  

}

gks_plugins_functions_run('admin_hr_program_item_exec_after',array(
  'id'=>&$id,
  'hr_program_user_id'=>&$hr_program_user_id,
  'is_new_rec'=>&$is_new_rec,
  'row_old'=>&$row_old,
  'row_custom_old'=>&$row_custom_old,
));

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

