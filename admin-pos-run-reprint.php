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


$mydaydif=0;
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

$my_page_title=gks_lang('Σημείο Εντατικής Λιανικής - Επανακτύπωση').' : '.$id_pos;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_pos_run','view',$id_pos);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


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
ORDER BY gks_acc_inv.id_acc_inv DESC ";

//$sql.=" limit 100";



$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
}

if ($result->num_rows==0) {
  $html='<div class="alert alert-danger" role="alert" style="margin-bottom: 0px;text-align: center;">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</div>';
  $return = array('success' => true, 'message' => base64_encode('OK'), 'html'=>base64_encode($html));
  echo json_encode($return); die();}

$html='<div class="alert alert-success" role="alert" style="margin-bottom: 0px;text-align: center;">'.
str_replace('[1]',$result->num_rows,gks_lang('Βρέθηκαν [1] καταχωρήσεις')).
'</div>
<table class="table table-sm table-responsive'.($device_type=='desktop' ? '111' : '').' table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" style="width:100%">
<thead>
  <tr >	
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap=nowrap width="0%">#</th>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Αρι').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Αξία').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Τρόπος').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('QR').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Εκτ').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Ημερομηνία').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Κατάσταση').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Αναφορά').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Καθ').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('ΦΠΑ').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Σειρά').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('POS').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('APP').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Πελάτης').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('Χρήστης').'</th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%">'.gks_lang('ID').'</th> 
    <th nowrap class="table-dark mytooltipsterfixcard" scope="col" style="text-align: center !important;" width="0%" title="'.gks_lang('Fix Card').'">F</th> 
  </tr>	
</thead>
<tbody>';

$row_array=[];
$ids_list=[];
while ($row = $result->fetch_assoc()) {      
  $row_array[]=$row;
  $ids_list[]=$row['id_acc_inv'];
}
$files_shortcode=[];
$shortcode_prefix='';
if (count($ids_list)>0) {
  
  $sql_files="SELECT shortcode_prefix FROM gks_custom_table 
  where custom_table_name='gks_acc_inv'
  and shortcode_prefix<>''";
  $result_files = $db_link->query($sql_files);        
  if (!$result_files) {debug_mail(false,'error sql',$sql_files);die('sql error');}
  if ($result_files->num_rows==1) {
    $row_files = $result_files->fetch_assoc();
    $shortcode_prefix=trim_gks($row_files['shortcode_prefix']);
    if ($shortcode_prefix!='') {
      $sql_files="select photo_url, public_shortcode 
      from gks_acc_inv_photo
      where acc_inv_id in (".implode(',',$ids_list).")
      and public_expire_date>now()
      and photo_url<>''
      and public_shortcode<>''";
      $result_files = $db_link->query($sql_files);        
      if (!$result_files) {debug_mail(false,'error sql',$sql_files);die('sql error');}
      while ($row_files = $result_files->fetch_assoc()) { 
        $files_shortcode[$row_files['photo_url']]=$row_files['public_shortcode'];
      }
    }
  }
}


$i = 0;
$sum_gks_price_total=0;
$sum_gks_price_net=0;
$sum_gks_price_fpa=0;
$per_payment_acquirer_name=[];
foreach ($row_array as $row) {

  $i++;
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
  

  $html.='<tr class="'.(($i % 2 == 0) ? 'even' : 'odd').'">
    <th scope="row" nowrap class="mytdcm aa">'.$i.'</th>
    <td nowrap class="mytdcm">';
    if ($row['inv_acc_number_int']<>0) $html.= $row['inv_acc_number_int'];
    $html.='</td>';
    
    $html.='<td nowrap class="mytdcm" >';
      if ($row['gks_price_total']!=0) $html.='<b>'.myCurrencyFormat($row['gks_price_total']).'</b>';
    $html.='</td>';
    $html.='<td nowrap class="mytdcm" >'.$payment_acquirer_name.'</td>';
    
    $html.='<td nowrap class="mytdcm" ';
      if (trim_gks($row['aade_qrurl'])=='') {
        $html.='style="background-color:red;"';
        $show_fix_card=true; 
      }
      $html.='>';
      //if (isset($row['aade_send_date'])) $html.=showDate(strtotime($row['aade_send_date']), 'd/m/Y\<\b\r\>H:i:s', 1);
      if (trim_gks($row['aade_qrurl'])!='') $html.='<a href="'.$row['aade_qrurl'].'">QR</a>';
      
      
    $html.='</td>';
       
    $html.='<td nowrap class="mytdcm" ';
      

      $found_file='';
      if (trim_gks($row['print_file_name'])!='') {
        $relative_path='acc/inv/'.$row['id_acc_inv'].'/print/'.$row['print_file_name'];
        $local_file=GKS_FileServerShare.$relative_path;
        if (file_exists($local_file)) {
          //print_file_url
          $url_file='admin-get-file.php?fs=fileservers&file=acc%2Finv%2F'.$row['id_acc_inv'].'%2Fprint%2F'.urlencode($row['print_file_name']);
          if ($device_type!='desktop') $url_file.='&download=1';
          
          //$html.= '<a href="'.$url_file.'" target="_blank" id="last_print_file">'.$row['print_file_name'].'</a> ';
          $icon_color='blue';
          if (isset($files_shortcode[$relative_path]) and $device_type!='desktop') {
            $url_file='/s/'.$shortcode_prefix.$files_shortcode[$relative_path];
            $icon_color='darkblue';
          }
          $found_file='<a href="'.$url_file.'" target="_blank"><i class="fas fa-download" style="color:'.$icon_color.';"></i></a>';
         
        }
      }
      if ($found_file=='') $html.= 'style="background-color:red;"';             
    $html.='>'.$found_file.'</td>';

    if ($device_type=='desktop') {
      //$html.='<td nowrap class="mytdcm">'.showDate(strtotime($row['inv_date']), 'd/m/Y H:i:s', 1).'</td>';
      $html.='<td nowrap class="mytdcm mytooltipsterdate" title="'.showDate(strtotime($row['inv_date']), 'd/m/Y H:i:s', 1).'">'.secondsago(strtotime($row['inv_date'])).'</td>';
    } else {
      $html.='<td nowrap class="mytdcm">'.showDate(strtotime($row['inv_date']), 'd/m/Y\<\b\r\>H:i:s', 1).'</td>';  
    }
    
    
    
    
    
    $html.='<td nowrap class="mytdcm"><span class="acc_inv_state_'.$row['inv_state'].'">'. getAccInvStateDescr($row['inv_state']).'</span></td>';
    $html.='<td nowrap class="mytdcm" >';
      if (!empty($row['merchant_ref_trns'])) $html.= nl2br_gks($row['merchant_ref_trns']);
    $html.='</td>';
    $html.='<td nowrap class="mytdcm" >';
      if ($row['gks_price_net']!=0) $html.= myCurrencyFormat($row['gks_price_net']);
    $html.='</td>';
    $html.='<td nowrap class="mytdcm" >'; 
      if ($row['gks_price_fpa']!=0) $html.= myCurrencyFormat($row['gks_price_fpa']);
    $html.='</td>';
    $html.='<td nowrap class="mytdcm">'.$row['inv_acc_seira_code'].'</td>';
    $html.='<td nowrap class="mytdcm">'.$row['pos_name'].'</td>';
    $html.='<td nowrap class="mytdcm">'.$row['erp_app_mobile_name'].'</td>';
    $html.='<td nowrap class="mytdcm">'.$row['gks_nickname'].'</td>';
    $html.='<td nowrap class="mytdcm">'.$row['gks_nickname_edit'].'</td>';
    $html.='<td nowrap class="mytdcm"><a target="_blank" href="admin-acc-inv-item.php?id='.$row['id_acc_inv'].'">'.$row['id_acc_inv'].'</a></td>';
    $html.='<td nowrap class="mytdcm">';
    if ($show_fix_card) $html.='<a target="_blank" href="admin-acc-inv-item-fix.php?id='.$row['id_acc_inv'].'">F</a>';
    $html.='</td>';
     
  $html.='</tr>';
  
}

$html.='</tbody>
<tfoot>
  <tr class="table-warning">
    <td class="bottomsums mytdcml" colspan="2" nowrap="">'.gks_lang('Σύνολα').'</td>
    <td class="bottomsums mytdcm" nowrap="" align="right"><b>'.myCurrencyFormat($sum_gks_price_total).'</b></td>  
    <td class="bottomsums mytdcm" nowrap="" align="right" colspan="6"></td>  
    <td class="bottomsums mytdcm" nowrap="" align="right">'.myCurrencyFormat($sum_gks_price_net).'</td>  
    <td class="bottomsums mytdcm" nowrap="" align="right">'.myCurrencyFormat($sum_gks_price_fpa).'</td>  
    <td class="bottomsums mytdcm" nowrap="" align="right" colspan="7"></td>  
  </tr>
  <tr>
    <td class="bottomsums mytdcml" nowrap="" align="right" colspan="18">';
    
foreach ($per_payment_acquirer_name as $key => $value) {
  $html.=$key.': '.myCurrencyFormat($value).'<br>';
} 
     
    
$html.='</td> 
  </tr>
</tfoot>';

$html.='</table>';


$return = array('success' => true, 'message' => base64_encode('OK'), 'html'=>base64_encode($html));
echo json_encode($return); die();
