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
  echo json_encode($return); die();
}
$my_page_title=gks_lang('Αποθήκευση Ευκαιρίας');
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_leads',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



gks_get_leads_status($leads_status,$leads_status_styles);




$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  $sql_row ="SELECT gks_crm_leads.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname,gks_users.pelati_sxolio,gks_users.order_sxolio,
  gks_crm_leads_status.lead_status_descr, gks_crm_leads_status.lead_status_color, gks_crm_leads_status.lead_status_sortorder,
  gks_company.company_title, gks_company_subs.company_sub_title,
  gks_country.country_name, gks_nomoi.nomos_descr,gks_lang.lang_name,
  ".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
  gks_crm_channel_sale.crm_channel_sale_descr, 
  ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
  gks_ads_campain.ads_campain_name
  FROM (((((((((((((gks_crm_leads 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_leads.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_leads.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_leads.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
  LEFT JOIN gks_crm_leads_status ON gks_crm_leads.lead_status_id = gks_crm_leads_status.id_crm_lead_status)
  LEFT JOIN gks_company ON gks_crm_leads.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_crm_leads.company_sub_id = gks_company_subs.id_company_sub) 
  LEFT JOIN gks_country ON gks_crm_leads.country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_crm_leads.nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_lang ON gks_crm_leads.user_lang = gks_lang.id_lang)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_crm_leads.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
  LEFT JOIN gks_crm_channel_sale ON gks_crm_leads.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_crm_leads.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
  LEFT JOIN gks_ads_campain ON gks_crm_leads.crm_channel_campain_id = gks_ads_campain.id_ads_campain
  where id_crm_lead = ".$id." limit 1";
    
  $result = $db_link->query($sql_row);        
  if (!$result) {
    debug_mail(false,'error sql',$sql_row);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
  $row_old = $result->fetch_assoc();

  $gks_custom_prepare=gks_custom_table_item_prepare('gks_crm_leads',['from'=>'item']);
  $gks_custom_row_old=gks_custom_table_item_view($gks_custom_prepare,$row_old); 

}


if ($_POST['lead_date'] == '__/__/____ __:__') $_POST['lead_date']='';
$lead_date=trim_gks(stripslashes(urldecode($_POST['lead_date'])));
if ($lead_date!='') {
  $lead_date = mystrtodb($lead_date);
}

$lead_status_id=1;if (isset($_POST['lead_status_id'])) $lead_status_id=intval($_POST['lead_status_id']);
$subject=''; if (isset($_POST['subject'])) $subject=trim_gks(base64_decode($_POST['subject']));
$message=''; if (isset($_POST['message'])) $message=trim_gks(base64_decode($_POST['message']));
$esoda=0; if (isset($_POST['esoda'])) $esoda=floatval(str_replace(',','.', $_POST['esoda']));
$lead_color=''; if (isset($_POST['lead_color'])) $lead_color=trim_gks(base64_decode($_POST['lead_color']));
$internal_note=''; if (isset($_POST['internal_note'])) $internal_note=trim_gks(base64_decode($_POST['internal_note']));
$user_id=0;if (isset($_POST['user_id'])) $user_id=intval($_POST['user_id']);
$first_name=''; if (isset($_POST['first_name'])) $first_name=trim_gks(base64_decode($_POST['first_name']));
$last_name=''; if (isset($_POST['last_name'])) $last_name=trim_gks(base64_decode($_POST['last_name']));
$email=''; if (isset($_POST['email'])) $email=trim_gks(base64_decode($_POST['email']));
$mobile=''; if (isset($_POST['mobile'])) $mobile=trim_gks(base64_decode($_POST['mobile']));
$phone=''; if (isset($_POST['phone'])) $phone=trim_gks(base64_decode($_POST['phone']));
$web=''; if (isset($_POST['web'])) $web=trim_gks(base64_decode($_POST['web']));
$user_lang=''; if (isset($_POST['user_lang'])) $user_lang=trim_gks(base64_decode($_POST['user_lang']));
if ($_POST['birthday'] == '__/__/____') $_POST['birthday']='';
$birthday=trim_gks(stripslashes(urldecode($_POST['birthday'])));
if ($birthday!='') {
  $birthday = mystrtodb_s($birthday.' 00:00:00');
}
$form_select_apostoli=-1; if (isset($_POST['form_select_apostoli'])) $form_select_apostoli=intval($_POST['form_select_apostoli']);
$form_ea_name=''; if (isset($_POST['form_ea_name'])) $form_ea_name=trim_gks(base64_decode($_POST['form_ea_name']));
$form_ea_phone=''; if (isset($_POST['form_ea_phone'])) $form_ea_phone=trim_gks(base64_decode($_POST['form_ea_phone']));

$odos=''; if (isset($_POST['odos'])) $odos=trim_gks(base64_decode($_POST['odos']));
$arithmos=''; if (isset($_POST['arithmos'])) $arithmos=trim_gks(base64_decode($_POST['arithmos']));
$orofos=''; if (isset($_POST['orofos'])) $orofos=trim_gks(base64_decode($_POST['orofos']));
$perioxi=''; if (isset($_POST['perioxi'])) $perioxi=trim_gks(base64_decode($_POST['perioxi']));
$poli=''; if (isset($_POST['poli'])) $poli=trim_gks(base64_decode($_POST['poli']));
$tk=''; if (isset($_POST['tk'])) $tk=trim_gks(base64_decode($_POST['tk']));
$country_id=0; if (isset($_POST['country_id'])) $country_id=intval($_POST['country_id']);
$nomos_id=0; if (isset($_POST['nomos_id'])) $nomos_id=intval($_POST['nomos_id']);
$map_latitude=0; if (isset($_POST['map_latitude'])) $map_latitude=floatval(str_replace(',','.', $_POST['map_latitude']));
$map_longitude=0; if (isset($_POST['map_longitude'])) $map_longitude=floatval(str_replace(',','.', $_POST['map_longitude']));
$company_id=0;if (isset($_POST['company_id'])) $company_id=intval($_POST['company_id']);
$company_sub_id=0;if (isset($_POST['company_sub_id'])) $company_sub_id=intval($_POST['company_sub_id']);

$eponimia=''; if (isset($_POST['eponimia'])) $eponimia=trim_gks(base64_decode($_POST['eponimia']));
$title=''; if (isset($_POST['title'])) $title=trim_gks(base64_decode($_POST['title']));
$afm=''; if (isset($_POST['afm'])) $afm=trim_gks(base64_decode($_POST['afm']));
$doy=''; if (isset($_POST['doy'])) $doy=trim_gks(base64_decode($_POST['doy']));
$epaggelma=''; if (isset($_POST['epaggelma'])) $epaggelma=trim_gks(base64_decode($_POST['epaggelma']));

$fiscal_position_id=0; if (isset($_POST['fiscal_position_id'])) $fiscal_position_id=intval($_POST['fiscal_position_id']);
$pricelist_id=0; if (isset($_POST['pricelist_id'])) $pricelist_id=intval($_POST['pricelist_id']);

$assigned_id=0; if (isset($_POST['assigned_id'])) $assigned_id=intval($_POST['assigned_id']);
$crm_channel_id=0; if (isset($_POST['crm_channel_id'])) $crm_channel_id=intval($_POST['crm_channel_id']);
$crm_channel_contact_id=0; if (isset($_POST['crm_channel_contact_id'])) $crm_channel_contact_id=intval($_POST['crm_channel_contact_id']);
$crm_channel_campain_id=0; if (isset($_POST['crm_channel_campain_id'])) $crm_channel_campain_id=intval($_POST['crm_channel_campain_id']);
$crm_channel_url=''; if (isset($_POST['crm_channel_url'])) $crm_channel_url=trim_gks(base64_decode($_POST['crm_channel_url']));
$crm_channel_code=''; if (isset($_POST['crm_channel_code'])) $crm_channel_code=trim_gks(base64_decode($_POST['crm_channel_code']));
$crm_channel_text=''; if (isset($_POST['crm_channel_text'])) $crm_channel_text=trim_gks(base64_decode($_POST['crm_channel_text']));

//$return = array('success' => false, 'message' => base64_encode('<pre>ggg'));
//echo json_encode($return); die();  
    
if ($crm_channel_id<=0) {
  $crm_channel_contact_id=0;
  $crm_channel_campain_id=0;
  $crm_channel_url='';
  $crm_channel_code='';
  $crm_channel_text='';
} else {
  $sql_channel="select * from gks_crm_channel_sale where id_crm_channel_sale=".$crm_channel_id;
  $result_channel = $db_link->query($sql_channel);        
  if (!$result_channel) {
    debug_mail(false,'error sql',$sql_channel);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result_channel->num_rows!=1) {
    debug_mail(false,'error sql','crm_channel_sale not found',$sql_channel);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το κανάλι πωλήσεων').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά')));
    echo json_encode($return); die();}
  $row_channel = $result_channel->fetch_assoc();
  if ($row_channel['crm_channel_has_contact']==0)  $crm_channel_contact_id=0;
  if ($row_channel['crm_channel_has_campain']==0)  $crm_channel_campain_id=0;
  if ($row_channel['crm_channel_has_url']==0)  $crm_channel_url='';
  if ($row_channel['crm_channel_has_code']==0)  $crm_channel_code='';
  if ($row_channel['crm_channel_has_text']==0)  $crm_channel_text='';
}



if ($subject=='') {debug_mail(false,'empty subject','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε το Θέμα')));
  echo json_encode($return); die();}



if ($email != '' and !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  debug_mail(false,'email is not ok: '.$email);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('To email δεν είναι σωστό')));
  echo json_encode($return); die();}

//if ($phone != '' and (strlen($phone) != 10 or substr($phone,0,1) != '2') ) {
//  debug_mail(false,'phone is not ok: '.$phone);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('To Σταθερό Τηλέφωνο δεν είναι σωστό')));
//  echo json_encode($return); die();}  

  
//if ($country_id==0) {debug_mail(false,'country_id s not ok','');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε μία χώρα')));
//  echo json_encode($return); die();}

//if ($nomos_id==0) {debug_mail(false,'nomos is not ok','');
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε έναν νομό')));
//  echo json_encode($return); die();}



$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_crm_leads');





$redirect='';
if ($id==-1) {
  $sql="insert into gks_crm_leads (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-crm-lead-item.php?id='.$id); 
  
  $sxolio=gks_lang('Προσθήκη από backend'); 
  $sql="insert into gks_crm_leads_log (crm_lead_id, add_date,user_id,sxolio) values (
  ".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }  
}


if ($form_select_apostoli==-1) { //apostoli stin idia adress
   
//  $sql="select user_id from gks_users where user_id=".$user_id;
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode('sql error'));
//    echo json_encode($return); die(); }
//  if ($result->num_rows==0) {
//    $sql="insert into gks_users (user_id,mydate_add,user_id_add,myip) values (".$user_id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
//    $result = $db_link->query($sql);    
//  }
//  
//  $sql="update gks_users set 
//  ma_odos='".$db_link->escape_string($odos)."',
//  ma_perioxi='".$db_link->escape_string($perioxi)."',
//  ma_poli='".$db_link->escape_string($poli)."',
//  ma_tk='".$db_link->escape_string($tk)."',
//  ma_country_id=".$db_link->escape_string($country_id).",
//  ma_nomos_id=".$db_link->escape_string($nomos_id).",
//  ma_latitude=".number_format($map_latitude,16,'.','').",
//  ma_longitude=".number_format($map_longitude,16,'.','').",
//  mydate_edit=now(),
//  user_id_edit=".$my_wp_user_id.",
//  myip='".$db_link->escape_string($gkIP)."'
//  where user_id=".$user_id. " limit 1";
//  $run = $db_link->query($sql);
//  if (!$run) {
//    debug_mail(false,'error sql',$sql);
//    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
//    echo json_encode($return); die(); }
  
} else if ($form_select_apostoli == 0) { // new address
  $sql="insert into gks_users_extra_address (
  mydate_add,user_id_add,myip,
  user_id,ea_name,ea_phone,ea_odos,ea_arithmos,ea_orofos,ea_perioxi,ea_poli,ea_tk,ea_country_id,ea_nomos_id,ea_latitude,ea_longitude
  ) values (
  now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
  ".$user_id.",
  '".$db_link->escape_string($form_ea_name)."',
  '".$db_link->escape_string($form_ea_phone)."',
  '".$db_link->escape_string($odos)."',
  '".$db_link->escape_string($arithmos)."',
  '".$db_link->escape_string($orofos)."',
  '".$db_link->escape_string($perioxi)."',
  '".$db_link->escape_string($poli)."',
  '".$db_link->escape_string($tk)."',
  ".$country_id.",
  ".$nomos_id.",
  ".number_format($map_latitude,16,'.','').",
  ".number_format($map_longitude,16,'.','').")";
  $run = $db_link->query($sql);
  if (!$run) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }
  
  $form_select_apostoli = $db_link->insert_id;  
  
} else if ($form_select_apostoli>0) { //update iparxousa address
  
  $sql="update gks_users_extra_address set 
  ea_name='".$db_link->escape_string($form_ea_name)."',
  ea_phone='".$db_link->escape_string($form_ea_phone)."',
  ea_odos='".$db_link->escape_string($odos)."',
  ea_arithmos='".$db_link->escape_string($arithmos)."',
  ea_orofos='".$db_link->escape_string($orofos)."',
  ea_perioxi='".$db_link->escape_string($perioxi)."',
  ea_poli='".$db_link->escape_string($poli)."',
  ea_tk='".$db_link->escape_string($tk)."',
  ea_country_id=".$db_link->escape_string($country_id).",
  ea_nomos_id=".$db_link->escape_string($nomos_id).",
  ea_latitude=".number_format($map_latitude,16,'.','').",
  ea_longitude=".number_format($map_longitude,16,'.','').",
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  where user_id=".$user_id."
  and id_users_extra_address=".$form_select_apostoli;
  $run = $db_link->query($sql);
  if (!$run) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die(); }  
} 
//echo '<pre>'.$form_select_apostoli;die();


$sql="update gks_crm_leads set 
lead_date=".($lead_date == '' ? 'null' : "'".$db_link->escape_string($lead_date)."'") .", 
lead_status_id=".$lead_status_id.",
subject='".$db_link->escape_string($subject)."',
message='".$db_link->escape_string($message)."',
esoda=".number_format($esoda, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, '.', '').", 
lead_color=". ($lead_color =='' ? 'null' : "'".$db_link->escape_string($lead_color)."'").",
internal_note='".$db_link->escape_string($internal_note)."',
user_id=".$user_id.",
first_name='".$db_link->escape_string($first_name)."',
last_name='".$db_link->escape_string($last_name)."',
email=". ($email =='' ? 'null' : "'".$db_link->escape_string($email)."'").",
mobile=". ($mobile =='' ? 'null' : "'".$db_link->escape_string($mobile)."'").",
phone=". ($phone =='' ? 'null' : "'".$db_link->escape_string($phone)."'").",
web=". ($web =='' ? 'null' : "'".$db_link->escape_string($web)."'").",
user_lang=". ($user_lang =='' ? 'null' : "'".$db_link->escape_string($user_lang)."'").",
birthday=".($birthday == '' ? 'null' : "'".$db_link->escape_string($birthday)."'") .", 
address_extra=".$form_select_apostoli.",
odos=". ($odos =='' ? 'null' : "'".$db_link->escape_string($odos)."'").",
arithmos=". ($arithmos =='' ? 'null' : "'".$db_link->escape_string($arithmos)."'").",
orofos=". ($orofos =='' ? 'null' : "'".$db_link->escape_string($orofos)."'").",
perioxi=". ($perioxi =='' ? 'null' : "'".$db_link->escape_string($perioxi)."'").",
poli=". ($poli =='' ? 'null' : "'".$db_link->escape_string($poli)."'").",
tk=". ($tk =='' ? 'null' : "'".$db_link->escape_string($tk)."'").",
country_id=".$country_id.",
nomos_id=".$nomos_id.",
map_latitude='".number_format($map_latitude,16,'.','')."',
map_longitude='".number_format($map_longitude,16,'.','')."',
company_id=".$company_id.",
company_sub_id=".$company_sub_id.",

eponimia=". ($eponimia =='' ? 'null' : "'".$db_link->escape_string($eponimia)."'").",
title=". ($title =='' ? 'null' : "'".$db_link->escape_string($title)."'").",
afm=". ($afm =='' ? 'null' : "'".$db_link->escape_string($afm)."'").",
doy=". ($doy =='' ? 'null' : "'".$db_link->escape_string($doy)."'").",
epaggelma=". ($epaggelma =='' ? 'null' : "'".$db_link->escape_string($epaggelma)."'").",
fiscal_position_id=".$fiscal_position_id.",
pricelist_id=".$pricelist_id.",

assigned_id=".$assigned_id.",
crm_channel_id=".$crm_channel_id.",
crm_channel_contact_id=".$crm_channel_contact_id.",
crm_channel_campain_id=".$crm_channel_campain_id.",
crm_channel_url=". ($crm_channel_url =='' ? 'null' : "'".$db_link->escape_string($crm_channel_url)."'").",
crm_channel_code=". ($crm_channel_code =='' ? 'null' : "'".$db_link->escape_string($crm_channel_code)."'").",
crm_channel_text=". ($crm_channel_text =='' ? 'null' : "'".$db_link->escape_string($crm_channel_text)."'").",

mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_crm_lead = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }




$gks_custom_save_run=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);

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
  
  if (trim_gks($row_old['lead_date']) != trim_gks($row_new['lead_date'])) 
    $sxolio_log.=gks_lang('Ημερομηνία').': <b>'.(isset($row_old['lead_date']) ? showDate(strtotime($row_old['lead_date']), 'd/m/Y H:i', 1) : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['lead_date']) ? showDate(strtotime($row_new['lead_date']), 'd/m/Y H:i', 1) : '').'</b>'.'<br>';

  
  if ($row_old['lead_status_id'].'' != $row_new['lead_status_id'].'') 
    $sxolio_log.=gks_lang('Κατάσταση').': '.
    '<span class="lead_status_'.$row_old['lead_status_id'].'">'.(isset($leads_status[$row_old['lead_status_id']]) ? $leads_status[$row_old['lead_status_id']]['lead_status_descr'] : '').'</span>'.
    ' [[-r]] '.
    '<span class="lead_status_'.$row_new['lead_status_id'].'">'.(isset($leads_status[$row_new['lead_status_id']]) ? $leads_status[$row_new['lead_status_id']]['lead_status_descr'] : '').'</span>'.
    '<br>';

  if (trim_gks($row_old['subject']) != trim_gks($row_new['subject'])) 
    $sxolio_log.=gks_lang('Θέμα').': <b>'.(isset($row_old['subject']) ? $row_old['subject'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['subject']) ? $row_new['subject'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['message']) != trim_gks($row_new['message'])) 
    $sxolio_log.=gks_lang('Μήνυμα').':<br><b>'.(isset($row_old['message']) ? nl2br_gks($row_old['message']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['message']) ? nl2br_gks($row_new['message']) : '').'</b>'.'<br>';

  $esoda_old=myCurrencyFormat($row_old['esoda']);
  $esoda_new=myCurrencyFormat($row_new['esoda']);
  if ($esoda_old != $esoda_new) 
    $sxolio_log.=gks_lang('Αναμενόμενα έσοδα').': <b>'.$esoda_old.'</b> [[-r]] '.
    '<b>'.$esoda_new.'</b>'.'<br>';

  if (trim_gks($row_old['lead_color']) != trim_gks($row_new['lead_color'])) 
    $sxolio_log.=gks_lang('Χρώμα').': <b>'.(isset($row_old['lead_color']) ? '<span style="background-color:'.$row_old['lead_color'].';display: inline-block;width:20px;height:20px;vertical-align: bottom;"> </span>' : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['lead_color']) ? '<span style="background-color:'.$row_new['lead_color'].';display: inline-block;width:20px;height:20px;vertical-align: bottom;"> </span>' : '').'</b>'.'<br>';
  

  if (trim_gks($row_old['internal_note']) != trim_gks($row_new['internal_note'])) 
    $sxolio_log.=gks_lang('Εσωτερική Σημείωση').':<br><b>'.(isset($row_old['internal_note']) ? nl2br_gks($row_old['internal_note']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['internal_note']) ? nl2br_gks($row_new['internal_note']) : '').'</b>'.'<br>';

  if (intval($row_old['assigned_id']) != intval($row_new['assigned_id']))
    $sxolio_log.=gks_lang('Ανάθεση σε').': <b>'.trim_gks($row_old['gks_nickname_assigned']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['gks_nickname_assigned']).'</b>'.'<br>';


  if (intval($row_old['crm_channel_id']) != intval($row_new['crm_channel_id']))
    $sxolio_log.=gks_lang('Κανάλι πωλήσεων').': <b>'.trim_gks($row_old['crm_channel_sale_descr']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['crm_channel_sale_descr']).'</b>'.'<br>';

  if (intval($row_old['crm_channel_contact_id']) != intval($row_new['crm_channel_contact_id']))
    $sxolio_log.=gks_lang('Επαφή Πωλήσεων').': <b>'.trim_gks($row_old['crm_channel_contact_gks_nickname']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['crm_channel_contact_gks_nickname']).'</b>'.'<br>';

  if (intval($row_old['crm_channel_campain_id']) != intval($row_new['crm_channel_campain_id']))
    $sxolio_log.=gks_lang('Καμπάνια').': <b>'.trim_gks($row_old['ads_campain_name']).'</b> [[-r]] '.
    '<b>'.trim_gks($row_new['ads_campain_name']).'</b>'.'<br>';

  if (trim_gks($row_old['crm_channel_url']) != trim_gks($row_new['crm_channel_url'])) 
    $sxolio_log.=gks_lang('URL').':<br><b>'.(isset($row_old['crm_channel_url']) ? nl2br_gks($row_old['crm_channel_url']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['crm_channel_url']) ? nl2br_gks($row_new['crm_channel_url']) : '').'</b>'.'<br>';

  if (trim_gks($row_old['crm_channel_code']) != trim_gks($row_new['crm_channel_code'])) 
    $sxolio_log.=gks_lang('Κωδικός CRM').':<br><b>'.(isset($row_old['crm_channel_code']) ? nl2br_gks($row_old['crm_channel_code']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['crm_channel_code']) ? nl2br_gks($row_new['crm_channel_code']) : '').'</b>'.'<br>';

  if (trim_gks($row_old['crm_channel_text']) != trim_gks($row_new['crm_channel_text'])) 
    $sxolio_log.=gks_lang('Σχόλιο').':<br><b>'.(isset($row_old['crm_channel_text']) ? nl2br_gks($row_old['crm_channel_text']) : '').'</b><br>[[-r]]<br>'.
    '<b>'.(isset($row_new['crm_channel_text']) ? nl2br_gks($row_new['crm_channel_text']) : '').'</b>'.'<br>';



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



  if (trim_gks($row_old['gks_nickname']) != trim_gks($row_new['gks_nickname'])) 
    $sxolio_log.=gks_lang('Πελάτης').': <b>'.(isset($row_old['gks_nickname']) ? $row_old['gks_nickname'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['gks_nickname']) ? $row_new['gks_nickname'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['first_name']) != trim_gks($row_new['first_name'])) 
    $sxolio_log.=gks_lang('Όνομα').': <b>'.(isset($row_old['first_name']) ? $row_old['first_name'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['first_name']) ? $row_new['first_name'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['last_name']) != trim_gks($row_new['last_name'])) 
    $sxolio_log.=gks_lang('Επώνυμο').': <b>'.(isset($row_old['last_name']) ? $row_old['last_name'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['last_name']) ? $row_new['last_name'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['email']) != trim_gks($row_new['email'])) 
    $sxolio_log.=gks_lang('Ηλ. διεύθυνση').': <b>'.(isset($row_old['email']) ? $row_old['email'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['email']) ? $row_new['email'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['mobile']) != trim_gks($row_new['mobile'])) 
    $sxolio_log.=gks_lang('Κινητό').': <b>'.(isset($row_old['mobile']) ? $row_old['mobile'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['mobile']) ? $row_new['mobile'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['phone']) != trim_gks($row_new['phone'])) 
    $sxolio_log.=gks_lang('Σταθερό Τηλέφωνο').': <b>'.(isset($row_old['phone']) ? $row_old['phone'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['phone']) ? $row_new['phone'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['web']) != trim_gks($row_new['web'])) 
    $sxolio_log.=gks_lang('Ιστότοπος').': <b>'.(isset($row_old['web']) ? $row_old['web'] : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['web']) ? $row_new['web'] : '').'</b>'.'<br>';

  if (trim_gks($row_old['lang_name']) != trim_gks($row_new['lang_name'])) 
    $sxolio_log.=gks_lang('Γλώσσα').': <b>'.$row_old['lang_name'].'</b> [[-r]] <b>'.$row_new['lang_name'].'</b>'.'<br>';



  if (trim_gks($row_old['birthday']) != trim_gks($row_new['birthday'])) 
    $sxolio_log.=gks_lang('Ημερ. Γέννησης').': <b>'.(isset($row_old['birthday']) ? date('d/m/Y',strtotime($row_old['birthday'])) : '').'</b> [[-r]] '.
    '<b>'.(isset($row_new['birthday']) ? date('d/m/Y',strtotime($row_new['birthday'])) : '').'</b>'.'<br>';

  if (trim_gks($row_old['odos']) != trim_gks($row_new['odos'])) 
    $sxolio_log.=gks_lang('Οδός').': <b>'.$row_old['odos'].'</b> [[-r]] <b>'.$row_new['odos'].'</b>'.'<br>';

  if (trim_gks($row_old['arithmos']) != trim_gks($row_new['arithmos'])) 
    $sxolio_log.=gks_lang('Αριθμός').': <b>'.$row_old['arithmos'].'</b> [[-r]] <b>'.$row_new['arithmos'].'</b>'.'<br>';

  if (trim_gks($row_old['orofos']) != trim_gks($row_new['orofos'])) 
    $sxolio_log.=gks_lang('Όροφος').': <b>'.$row_old['orofos'].'</b> [[-r]] <b>'.$row_new['orofos'].'</b>'.'<br>';

  if (trim_gks($row_old['perioxi']) != trim_gks($row_new['perioxi'])) 
    $sxolio_log.=gks_lang('Περιοχή').': <b>'.$row_old['perioxi'].'</b> [[-r]] <b>'.$row_new['perioxi'].'</b>'.'<br>';

  if (trim_gks($row_old['poli']) != trim_gks($row_new['poli'])) 
    $sxolio_log.=gks_lang('Πόλη').': <b>'.$row_old['poli'].'</b> [[-r]] <b>'.$row_new['poli'].'</b>'.'<br>';

  if (trim_gks($row_old['tk']) != trim_gks($row_new['tk'])) 
    $sxolio_log.=gks_lang('TK').': <b>'.$row_old['tk'].'</b> [[-r]] <b>'.$row_new['tk'].'</b>'.'<br>';

  if (trim_gks($row_old['country_name']) != trim_gks($row_new['country_name'])) 
    $sxolio_log.=gks_lang('Χώρα').': <b>'.$row_old['country_name'].'</b> [[-r]] <b>'.$row_new['country_name'].'</b>'.'<br>';

  if (trim_gks($row_old['nomos_descr']) != trim_gks($row_new['nomos_descr'])) 
    $sxolio_log.=gks_lang('Νομός').': <b>'.$row_old['nomos_descr'].'</b> [[-r]] <b>'.$row_new['nomos_descr'].'</b>'.'<br>';


  $map_latitude_old=floatval($row_old['map_latitude']);
  $map_latitude_new=floatval($row_new['map_latitude']);
  if ($map_latitude_old != $map_latitude_new) 
    $sxolio_log.=gks_lang('Γεωγραφικό Πλάτος').': <b>'.myNumberFormatNo0Local($map_latitude_old).'</b> [[-r]] '.
    '<b>'.myNumberFormatNo0Local($map_latitude_new).'</b>'.'<br>';

  $map_longitude_old=floatval($row_old['map_longitude']);
  $map_longitude_new=floatval($row_new['map_longitude']);
  if ($map_longitude_old != $map_longitude_new) 
    $sxolio_log.=gks_lang('Γεωγραφικό Μήκος').': <b>'.myNumberFormatNo0Local($map_longitude_old).'</b> [[-r]] '.
    '<b>'.myNumberFormatNo0Local($map_longitude_new).'</b>'.'<br>';



  if (trim_gks($row_old['eponimia']) != trim_gks($row_new['eponimia'])) 
    $sxolio_log.=gks_lang('Επωνυμία').': <b>'.$row_old['eponimia'].'</b> [[-r]] <b>'.$row_new['eponimia'].'</b>'.'<br>';

  if (trim_gks($row_old['title']) != trim_gks($row_new['title'])) 
    $sxolio_log.=gks_lang('Τίτλος').': <b>'.$row_old['title'].'</b> [[-r]] <b>'.$row_new['title'].'</b>'.'<br>';

  if (trim_gks($row_old['afm']) != trim_gks($row_new['afm'])) 
    $sxolio_log.=gks_lang('ΑΦΜ').': <b>'.$row_old['afm'].'</b> [[-r]] <b>'.$row_new['afm'].'</b>'.'<br>';

  if (trim_gks($row_old['doy']) != trim_gks($row_new['doy'])) 
    $sxolio_log.=gks_lang('ΔΟΥ').': <b>'.$row_old['doy'].'</b> [[-r]] <b>'.$row_new['doy'].'</b>'.'<br>';

  if (trim_gks($row_old['epaggelma']) != trim_gks($row_new['epaggelma'])) 
    $sxolio_log.=gks_lang('Επάγγελμα').': <b>'.$row_old['epaggelma'].'</b> [[-r]] <b>'.$row_new['epaggelma'].'</b>'.'<br>';




  $gks_custom_prepare=gks_custom_table_item_prepare('gks_crm_leads',['from'=>'item']);
  $gks_custom_row_new=gks_custom_table_item_view($gks_custom_prepare,$row_new); 
  $custom_sxolio_log=gks_custom_sxolio_log($gks_custom_row_old,$gks_custom_row_new);
  $sxolio_log.=$custom_sxolio_log;


  if ($sxolio_log == '') $sxolio_log=gks_lang('Ενημέρωση').'<br>';
  //print '<pre>';
  //print_r($products_old);
  //die();  
  
  if ($sxolio_log!='') {
    $sxolio_log = substr($sxolio_log, 0, strlen($sxolio_log) -4);
    $sql="insert into gks_crm_leads_log (crm_lead_id, add_date,user_id,sxolio) values (
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


gks_update_user_from_some_move(array('user_id'=>$user_id,'table'=>'gks_crm_leads','id_table'=>$id));



$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();







