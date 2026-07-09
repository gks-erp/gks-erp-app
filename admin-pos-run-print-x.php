<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$id_pos=0;
if (isset($_POST['id_pos'])) $id_pos=intval($_POST['id_pos']);
if ($id_pos<=0) {
  debug_mail(false,'the id_pos is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το POS ID')));
  echo json_encode($return); die();}

$device_type=''; if (isset($_POST['device_type'])) $device_type=trim_gks(base64_decode($_POST['device_type']));




$my_page_title=gks_lang('Σημείο Εντατικής Λιανικής - Εκτύπωση Χ').' : '.$id_pos;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_pos_run','view',$id_pos);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


$mydaydif=0;if (isset($_POST['mydaydif'])) $mydaydif=intval($_POST['mydaydif']);
if ($mydaydif>0) $mydaydif=0;

$sql_p="SELECT perm_int_cond02
FROM gks_permission_user
WHERE user_id=".$my_wp_user_id." AND permission_object_id=684";
$result_p = $db_link->query($sql_p);        
if (!$result_p) {
  debug_mail(false,'error sql',$sql_p);die('sql error');
}
if ($result_p->num_rows==0) {
  $mydaydif=0;
} else {
  $row_p = $result_p->fetch_assoc();
  $perm_int_cond02=intval($row_p['perm_int_cond02']);  
  if (abs($mydaydif) > $perm_int_cond02) $mydaydif=-$perm_int_cond02;
}  

//echo '<pre>'.$mydaydif;die();


$mytimenow=time() + $mydaydif*24*60*60; // + 0*24*60*60;
$time_vardia=_time_user($mytimenow, 1);
$time_vardia-= GKS_ERP_START_VARDIA*60*60;
$today_vardia = date('Y-m-d',$time_vardia);
$today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
$today_vardia = _time_user($today_vardia, -1);
$today_vardia_time = $today_vardia;
$today_vardia = date('Y-m-d H:i:s', $today_vardia);

$date_where="
and gks_acc_inv.inv_date >='".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'
and gks_acc_inv.inv_date<  '".date('Y-m-d H:i:s', $today_vardia_time + (1 * 24*60*60))."'";


$html=gks_lang('ΕΚΤΥΠΩΣΗ Χ - ΑΡΧΗ').'<br>'; 
$html.=gks_lang('Εκτύπωση').': '.showDate(time(),'d/m/Y H:i:s',1).'<br>';
if ($mydaydif==0) {
  $html.=gks_lang('Από').': '.showDate($today_vardia_time,'d/m/Y H:i:s',1).'<br>';
  $html.=gks_lang('Έως').': '.showDate(time(),'d/m/Y H:i:s',1).'<br>';
} else {
  $html.=gks_lang('Από').': '.showDate($today_vardia_time,'d/m/Y H:i:s',1).'<br>';
  $html.=gks_lang('Έως').': '.showDate($today_vardia_time + (1 * 24*60*60),'d/m/Y H:i:s',1).'<br>';
}

$sql="SELECT gks_pos.*, 
gks_company.company_afm, gks_company.company_doy, gks_company.company_epaggelma,

gks_company.company_title, gks_company.company_eponimia, 
gks_company.company_odos,
gks_company.company_arithmos,
gks_company.company_orofos,
gks_company.company_perioxi,
gks_company.company_poli,
gks_company.company_tk,
gks_company.company_phone,
gks_company.company_email,
gks_company.company_url,
gks_country.country_name,
gks_country.country_initials,
gks_country.country_initials3,
gks_country.country_ee,
gks_nomoi.nomos_descr,

gks_company_subs.company_sub_title, gks_company_subs.company_sub_eponimia, 
gks_company_subs.company_sub_odos,
gks_company_subs.company_sub_arithmos,
gks_company_subs.company_sub_orofos,
gks_company_subs.company_sub_perioxi,
gks_company_subs.company_sub_poli,
gks_company_subs.company_sub_tk,
gks_company_subs.company_sub_phone,
gks_company_subs.company_sub_email,
gks_company_subs.company_sub_url,
gks_country_sub.country_name as country_name_sub,
gks_country_sub.country_initials as country_initials_sub,
gks_country_sub.country_initials3 as country_initials3_sub,
gks_country_sub.country_ee as country_ee_sub,
gks_nomoi_sub.nomos_descr as nomos_descr_sub,

gks_acc_journal.acc_journal_code, gks_acc_journal.acc_journal_descr, 
gks_acc_seires.seira_code, gks_acc_seires.seira_descr
FROM (((((((gks_pos 
LEFT JOIN gks_company ON gks_pos.pos_company_id = gks_company.id_company) 
LEFT JOIN gks_country ON gks_company.company_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_company.company_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_company_subs ON gks_pos.pos_company_sub_id = gks_company_subs.id_company_sub) 
LEFT JOIN gks_country as gks_country_sub ON gks_company_subs.company_sub_country_id = gks_country_sub.id_country)
LEFT JOIN gks_nomoi as gks_nomoi_sub ON gks_company_subs.company_sub_nomos_id = gks_nomoi_sub.id_nomos)
LEFT JOIN gks_acc_journal ON gks_pos.pos_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_seires ON gks_pos.pos_seira_id = gks_acc_seires.id_acc_seira
WHERE gks_pos.id_pos=".$id_pos;
//echo $sql;die();
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
}
if ($result->num_rows==0) {
  $html='<div class="alert alert-danger" role="alert" style="margin-bottom: 0px;text-align: center;">'.gks_lang('Δεν βρέθηκε το σημείο εντατικής λιανικής').'</div>';
  $return = array('success' => true, 'message' => base64_encode('OK'), 'html'=>base64_encode($html));
  echo json_encode($return); die();}
$row_pos = $result->fetch_assoc();

if (!empty($row_pos['company_title'])) $html.=gks_lang('Τίτλος').': '.$row_pos['company_title'].'<br>';
if (!empty($row_pos['company_eponimia'])) $html.=gks_lang('Επωνυμία').': '.$row_pos['company_eponimia'].'<br>';
if (!empty($row_pos['company_sub_title'])) $html.=gks_lang('Υποκατάστημα').': '.$row_pos['company_sub_title'].'<br>';
if (!empty($row_pos['company_afm'])) $html.=gks_lang('ΑΦΜ').': '.$row_pos['company_afm'].'<br>';
if (!empty($row_pos['company_doy'])) $html.=gks_lang('ΔΟΥ').': '.$row_pos['company_doy'].'<br>';
if (!empty($row_pos['acc_journal_descr'])) $html.=gks_lang('Ημερολόγιο').': '.$row_pos['acc_journal_descr'].' ('.$row_pos['acc_journal_code'].')<br>';
if (!empty($row_pos['seira_descr'])) $html.=gks_lang('Σειρά').': '.$row_pos['seira_descr'].' ('.$row_pos['seira_code'].')<br>';
if (!empty($row_pos['pos_name'])) $html.=gks_lang('Σημείο').': '.$row_pos['pos_name'].'<br>';


$sql="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.inv_date, gks_acc_inv.inv_acc_seira_code, 
gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_state, 
gks_acc_inv.gks_price_fpa, gks_acc_inv.gks_price_netfpa, gks_acc_inv.gks_price_total, gks_acc_inv.gks_price_net,
gks_acc_inv.aade_send_date,gks_acc_inv.aade_invoicemark, gks_acc_inv.aade_qrurl, 
gks_acc_inv.tropos_pliromis_via,
gks_pos.pos_name, gks_erp_app_mobile.erp_app_mobile_name,
gks_acc_inv.print_file_name,
".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
".GKS_WP_TABLE_PREFIX."users.gks_nickname,
gks_payment_acquirers.payment_acquirer_name,
gks_acc_inv.merchant_ref_trns
FROM ((((gks_acc_inv 
LEFT JOIN gks_pos ON gks_acc_inv.pos_id = gks_pos.id_pos) 
LEFT JOIN gks_erp_app_mobile ON gks_acc_inv.erp_app_mobile_id = gks_erp_app_mobile.id_erp_app_mobile)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_inv.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer

WHERE gks_acc_inv.pos_id=".$id_pos.
$date_where."
and inv_state in ('090ekdosi','100payment')
ORDER BY gks_acc_inv.id_acc_inv";

//$sql.=" limit 100";



$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
}



$row_array=[];
while ($row = $result->fetch_assoc()) {      
  $row_array[]=$row;
}


$count_invs = 0;
$sum_gks_price_total=0;
$sum_gks_price_net=0;
$sum_gks_price_fpa=0;
$per_payment_acquirer_name=[];
$int_min=0;
$int_max=0;
foreach ($row_array as $row) {
  $count_invs++;
  $show_fix_card=false;
  
  $sum_gks_price_total+=floatval($row['gks_price_total']);
  $sum_gks_price_net+=floatval($row['gks_price_net']);
  $sum_gks_price_fpa+=floatval($row['gks_price_fpa']);

  
  $payment_acquirer_name=$row['payment_acquirer_name'];
  if (trim_gks($row['tropos_pliromis_via'])!='') {
    $payment_acquirer_name=$row['tropos_pliromis_via'].' via '.$payment_acquirer_name;
  }
  if (isset($per_payment_acquirer_name[$payment_acquirer_name])==false)  {
    $per_payment_acquirer_name[$payment_acquirer_name]=0;
  }
  $per_payment_acquirer_name[$payment_acquirer_name]+=floatval($row['gks_price_total']);
  
  //$row['inv_acc_number_int']
  
  if ($count_invs==1) {
    $int_min=$row['inv_acc_number_int'];
    $int_max=$row['inv_acc_number_int'];    
  } else {
    if ($row['inv_acc_number_int'] < $int_min) $int_min=$row['inv_acc_number_int'];
    if ($row['inv_acc_number_int'] > $int_max) $int_max=$row['inv_acc_number_int'];
  }
}

$html.=gks_lang('Πλήθος αποδείξεων').': '.$count_invs.'<br>';
$html.=gks_lang('Αρίθμηση').': ['.$int_min.' - '.$int_max.']<br>';
$html.=gks_lang('Καθαρή αξία').': '.myCurrencyFormat($sum_gks_price_net).'<br>';
$html.=gks_lang('ΦΠΑ').': '.myCurrencyFormat($sum_gks_price_fpa).'<br>';
$html.=gks_lang('Σύνολο').': '.myCurrencyFormat($sum_gks_price_total).'<br>';
$html.=gks_lang('Τρόποι πληρωμής').':<br>';
foreach ($per_payment_acquirer_name as $key => $value) {
  $html.=$key.': '.myCurrencyFormat($value).'<br>'; //'&nbsp;'.
} 

$html.=gks_lang('ΕΚΤΥΠΩΣΗ Χ - ΤΕΛΟΣ').'<br>'; 


$pos_print_x_form_id=intval($row_pos['pos_print_x_form_id']);
if ($pos_print_x_form_id>0) {
  $sql="select * from gks_print_forms where id_print_form=".$pos_print_x_form_id;
  //echo $sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);die('sql error');
  }
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    $template='';
    $temp=trim_gks($row['page_header']); if (endwith($temp,"\r\n")==false) $temp.="\r\n"; $template.=$temp;
    $temp=trim_gks($row['form_header']); if (endwith($temp,"\r\n")==false) $temp.="\r\n"; $template.=$temp;
    $temp=trim_gks($row['details_header']); if (endwith($temp,"\r\n")==false) $temp.="\r\n"; $template.=$temp;
    $temp=trim_gks($row['details_body']); if (endwith($temp,"\r\n")==false) $temp.="\r\n"; $template.=$temp;
    $temp=trim_gks($row['details_footer']); if (endwith($temp,"\r\n")==false) $temp.="\r\n"; $template.=$temp;
    $temp=trim_gks($row['form_footer']); if (endwith($temp,"\r\n")==false) $temp.="\r\n"; $template.=$temp;
    $temp=trim_gks($row['page_footer']); if (endwith($temp,"\r\n")==false) $temp.="\r\n"; $template.=$temp;
    
    if (endwith($template,"\r\n")) $template=substr($template,0, strlen($template)-2);
    
    $template=str_replace("\r\n","\n",$template);
    for ($dddd=0;$dddd<=30;$dddd++) {
      $template=str_replace("\n\n","\n",$template);
    }    
    $template=str_replace('[br]',"\n",$template);
    
    $template=str_replace("\n",'<br>',$template);
    $html=$template;
    
    $html=str_replace('{now}', showDate(time(),'d/m/Y H:i:s',1), $html);
    if ($mydaydif==0) {
      $html=str_replace('{fromdate}', showDate($today_vardia_time,'d/m/Y H:i:s',1), $html);
      $html=str_replace('{todate}', showDate(time(),'d/m/Y H:i:s',1), $html);
    } else {
      $html=str_replace('{fromdate}', showDate($today_vardia_time,'d/m/Y H:i:s',1), $html);
      $html=str_replace('{todate}', showDate($today_vardia_time + (1 * 24*60*60),'d/m/Y H:i:s',1), $html);
    }
    
    if ($row_pos['pos_company_sub_id']==0) {
      $html=str_replace('{company_title}', $row_pos['company_title'], $html);
      $html=str_replace('{company_eponimia}', $row_pos['company_eponimia'], $html);
      $html=str_replace('{company_epaggelma}', $row_pos['company_epaggelma'], $html);
      $html=str_replace('{company_odos}', $row_pos['company_odos'], $html);
      $html=str_replace('{company_arithmos}', $row_pos['company_arithmos'], $html);
      $html=str_replace('{company_tk}', $row_pos['company_tk'], $html);
      $html=str_replace('{company_orofos}', $row_pos['company_orofos'], $html);
      $html=str_replace('{company_perioxi}', $row_pos['company_perioxi'], $html);
      $html=str_replace('{company_poli}', $row_pos['company_poli'], $html);
      $html=str_replace('{company_nomos_descr}', $row_pos['nomos_descr'], $html);
      $html=str_replace('{company_country_name}', $row_pos['country_name'], $html);
      $html=str_replace('{company_country_ee}', $row_pos['country_ee'], $html);
      $html=str_replace('{company_phone}', $row_pos['company_phone'], $html);
      $html=str_replace('{company_email}', $row_pos['company_email'], $html);
      $html=str_replace('{company_url}', $row_pos['company_url'], $html);


    } else {
      $html=str_replace('{company_title}', $row_pos['company_sub_title'], $html);
      $html=str_replace('{company_eponimia}', $row_pos['company_sub_eponimia'], $html);
      $html=str_replace('{company_epaggelma}', $row_pos['company_epaggelma'], $html);
      $html=str_replace('{company_odos}', $row_pos['company_sub_odos'], $html);
      $html=str_replace('{company_arithmos}', $row_pos['company_sub_arithmos'], $html);
      $html=str_replace('{company_tk}', $row_pos['company_sub_tk'], $html);
      $html=str_replace('{company_orofos}', $row_pos['company_sub_orofos'], $html);
      $html=str_replace('{company_perioxi}', $row_pos['company_sub_perioxi'], $html);
      $html=str_replace('{company_poli}', $row_pos['company_sub_poli'], $html);
      $html=str_replace('{company_nomos_descr}', $row_pos['nomos_descr_sub'], $html);
      $html=str_replace('{company_country_name}', $row_pos['country_name_sub'], $html);
      $html=str_replace('{company_country_ee}', $row_pos['country_ee_sub'], $html);
      $html=str_replace('{company_phone}', $row_pos['company_sub_phone'], $html);
      $html=str_replace('{company_email}', $row_pos['company_sub_email'], $html);
      $html=str_replace('{company_url}', $row_pos['company_sub_url'], $html);

    
    }
    //$html=str_replace('{company_country_ee}', $row_pos['company_eponimia'], $html);
    $html=str_replace('{company_afm}', $row_pos['company_afm'], $html);
    $html=str_replace('{company_doy}', $row_pos['company_doy'], $html);
    
    $html=str_replace('{doc_title}', $row_pos['acc_journal_descr'], $html);
    $html=str_replace('{doc_seira}', $row_pos['seira_descr'], $html);
    $html=str_replace('{doc_pos_name}', $row_pos['pos_name'], $html);
    $html=str_replace('{doc_items_count}', $count_invs, $html);
    $html=str_replace('{doc_items_number_range}', $int_min.' - '.$int_max, $html);
    
    $html=str_replace('{doc_items_sum_priceall}', myCurrencyFormat($sum_gks_price_net), $html);
    $html=str_replace('{doc_items_sum_fpa_amount}', myCurrencyFormat($sum_gks_price_fpa), $html);
    $html=str_replace('{doc_items_sum_priceall_total}', myCurrencyFormat($sum_gks_price_total), $html);
    $temp=[];
    foreach ($per_payment_acquirer_name as $key => $value) {
      $temp[]=$key.': '.myCurrencyFormat($value); //'&nbsp;'.
    }
    
    $html=str_replace('{doc_tropos_pliromis}', implode('<br>',$temp), $html);

    
    
    
    
  }
  
}


$fileName=showDate(time(),'Y_m_d_H_i_s',1).'_'.$id_pos.'_'.rand(1000,9999).rand(1000,9999).rand(1000,9999).'.txt';

$txtAbsoluteFilePath = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$fileName;
$txtdata="\xEF\xBB\xBF"."\n".$html;
$txtdata=str_replace('<br>', "\n", $txtdata);
$txtdata=str_replace('<b>', '[b]', $txtdata);
$txtdata=str_replace('</b>', '', $txtdata);
$txtdata=str_replace('&euro;', '€', $txtdata);
$txtdata.="\n\n\n";
@file_put_contents($txtAbsoluteFilePath,$txtdata);

$url_txt='';
$url_qrcode='';
if (file_exists($txtAbsoluteFilePath)) {
  $url_txt=GKS_SITE_URL.'my/temp/'.$fileName;

  include_once('vendor_inc/phpqrcode/qrlib.php');
	$fileName = 'qr_code_temp_'.md5($url_txt).'.png';
  $pngAbsoluteFilePath = GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$fileName;
  $urlRelativeFilePath = GKS_SITE_URL.'my/temp/'.$fileName;
  // generating
  if (!file_exists($pngAbsoluteFilePath)) {
    QRcode::png($url_txt, $pngAbsoluteFilePath, QR_ECLEVEL_L, 10, 0);
  } 
  if (file_exists($pngAbsoluteFilePath)) {
    $url_qrcode=$urlRelativeFilePath;
  }
}

$return = array('success' => true, 'message' => base64_encode('OK'), 'html'=>base64_encode($html),'url_txt'=>base64_encode($url_txt),'url_qrcode'=>base64_encode($url_qrcode));
echo json_encode($return); die();
