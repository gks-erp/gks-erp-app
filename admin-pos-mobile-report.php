<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$app_mobile_debug=false;
//if ($gkIP=='94.69.253.51') $app_mobile_debug=true;
//if ($gkIP=='192.168.1.109') $app_mobile_debug=true;
//echo $gkIP;die();

if ($app_mobile_debug) {
  $id_erp_app_mobile=10001;
  $id_report=1;
  //https://test.easyfilesselection.com//my/admin-pos-mobile-report.php

  //$id_erp_app_mobile=10001;
  //$id_report=2;
  //https://erp.myphotos.gr/my/admin-pos-mobile-report.php

  
} else {
if (isset($_GET['id_report']) and isset($_GET['from']) and isset($_GET['ltype']) and (isset($_GET['token']) or isset($_GET['uname'])) and isset($_GET['mydate']) and isset($_GET['send1']) and isset($_GET['send2'])) {
  //print '<pre>';print_r($_GET);die();
  
  if (intval($_GET['id_report'])>0 and $_GET['from']=='gks_erp_app_mobile' and (strlen($_GET['token'])>=9 or strlen($_GET['uname'])>=4)  and strlen($_GET['mydate'])==19 and strlen($_GET['send1'])>=32 and strlen($_GET['send2'])>=32) {
    db_open();
    
    if ($_GET['ltype']!='token' and $_GET['ltype']!='user') die('dddddddddddddd');
    
    $id_report=intval($_GET['id_report']);
    if ($_GET['ltype']=='token') {
      $erp_app_mobile_token=trim($_GET['token']);
      $sql="SELECT * from gks_erp_app_mobile 
      where erp_app_mobile_disabled=0 and erp_app_mobile_token='".$db_link->escape_string($erp_app_mobile_token)."'";
    } else if ($_GET['ltype']=='user') {
      $sql="select * from ".GKS_WP_TABLE_PREFIX."users
      where user_login='".$db_link->escape_string($_GET['uname'])."'";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows==0) {debug_mail(false,'user pos not found (2)',$sql);die('user pos not found (2)');}
      $row = $result->fetch_assoc();
      $erp_app_mobile_user_id=intval($row['ID']);
      if ($erp_app_mobile_user_id<=0) {debug_mail(false,'user pos not found (3)',$sql);die('user pos not found (3)');}
      //echo 'fffff1';die();

      $sql="SELECT * from gks_erp_app_mobile 
      where erp_app_mobile_disabled=0 and erp_app_mobile_user_id=".$erp_app_mobile_user_id;
      
    }
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==1) {
      $row_app=$result->fetch_assoc(); 
      $erp_app_mobile_secret=$row_app['erp_app_mobile_secret'];
      $erp_app_mobile_user_token=$row_app['erp_app_mobile_user_token'];
      $id_erp_app_mobile=intval($row_app['id_erp_app_mobile']);
      
      $send1=trim($_GET['send1']);
      $send2=trim($_GET['send2']);
      $mydate=trim($_GET['mydate']);
      if ($_GET['ltype']=='token') {
        $calc2= md5($send1 . $send1 . $id_report . $mydate . $erp_app_mobile_secret . $send1 . $erp_app_mobile_token .  GKS_ERP_HASHMD5KEY01 . $send1);
      } else if ($_GET['ltype']=='user') {
        $calc2= md5($send1 . $send1 . $id_report . $mydate . $_GET['uname'] . $send1 . $erp_app_mobile_user_token .  GKS_ERP_HASHMD5KEY01 . $send1);
      } else {
        $calc2='ssssssssssssssssss';
      }  
      $diafora=strtotime($mydate) - _time_user(time(),1);
      if (abs($diafora)>60) {
        echo gks_lang('Σφάλμα').' 12343';die();
      }
        
      //echo '<pre>'.$diafora."\n".$mydate."\n".$calc2."\n".$send2;die();
      
      
      if ($calc2==$send2) {
        //OK !!!
        
        
      } else {
        echo gks_lang('Σφάλμα').' 12345';die();
      }
    } else {
      echo gks_lang('Σφάλμα').' 12346';die();
    }
  } else {
    echo gks_lang('Σφάλμα').' 12348';die();
  }
} else {
  echo gks_lang('Σφάλμα').' 12349';die();
}
}

if ($id_report < 1 or $id_report > 5) $id_report=1;

//$today_vardia_this = date('Y-m-d',_time_user(time(), 1));
//$wd=$today_vardia_this;
//if (GKS_ERP_START_VARDIA!=0 and $today_vardia!='') $wd=$today_vardia;
//$set_vardia=GKS_ERP_START_VARDIA!=0;

$mydaydif=0;
if ($id_report==1) $mydaydif=0;
if ($id_report==2) $mydaydif=0;
if ($id_report==3) $mydaydif=-1;
if ($id_report==4) $mydaydif=-2;

 

$mytimenow=time() + $mydaydif*24*60*60; // + 0*24*60*60;
$time_vardia=_time_user($mytimenow, 1);
$time_vardia-= GKS_ERP_START_VARDIA*60*60;
$today_vardia = date('Y-m-d',$time_vardia);
$today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
$today_vardia = _time_user($today_vardia, -1);
$today_vardia_time = $today_vardia;
$today_vardia = date('Y-m-d H:i:s', $today_vardia);

//echo $id_report.'|'.$mydaydif.'|'.$today_vardia;die();
$date_where='';
if ($id_report>=2) {
  $date_where="
  and gks_acc_inv.inv_date >='".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'
  and gks_acc_inv.inv_date<  '".date('Y-m-d H:i:s', $today_vardia_time + (1 * 24*60*60))."'";

}
//echo '<pre>'.$date_where; die();

db_open();
$inv_acc_seira_code='';
$sql="select inv_acc_seira_code 
from gks_acc_inv 
where gks_acc_inv.erp_app_mobile_id=".$id_erp_app_mobile."
and inv_acc_seira_code<>''
order by gks_acc_inv.id_acc_inv desc
limit 1";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
}
if ($result->num_rows>0) {
  $row = $result->fetch_assoc();
  $inv_acc_seira_code=$row['inv_acc_seira_code'];
}


$sql="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.inv_date, gks_acc_inv.inv_acc_seira_code, 
gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_state, 
gks_acc_inv.gks_price_fpa, gks_acc_inv.gks_price_netfpa, gks_acc_inv.gks_price_total, gks_acc_inv.gks_price_net,
gks_acc_inv.aade_send_date,gks_acc_inv.aade_invoicemark, gks_acc_inv.aade_qrurl, 
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

WHERE ";
if ($inv_acc_seira_code!='') {
  $sql.=" gks_acc_inv.inv_acc_seira_code='".$db_link->escape_string($inv_acc_seira_code)."' ";
} else {
  $sql.=" gks_acc_inv.erp_app_mobile_id=".$id_erp_app_mobile." ";
}
$sql.=$date_where."
ORDER BY gks_acc_inv.id_acc_inv DESC ";

if ($id_report==1) {
  $sql.=" limit 10";
}



$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
}

$my_page_title=gks_lang('gks ERP App Mobile Report ID').': '.$id_report;
stat_record();
$gks_header_footer_layout='empty';

include_once('_my_header_admin.php');
  
if ($result->num_rows==0) {
  echo '<div class="alert alert-danger" role="alert" style="margin-bottom: 0px;text-align: center;">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</div>';
  include_once('_my_footer_admin.php');
  die();
}

?>
<div class="alert alert-success" role="alert" style="margin-bottom: 0px;text-align: center;">
  <?php echo str_replace('[1]',$result->num_rows,gks_lang('Βρέθηκαν [1] καταχωρήσεις'));?>
</div>

<table class="table table-sm table-responsive table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" style="width:100%">
<thead>
  <tr >	
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?">#</a></th>
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Σειρά');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Αρι');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Αξία');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Τρόπος');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('QR');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Εκτ');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Ημερομηνία');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κατάσταση');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Αναφορά');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Καθ');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ΦΠΑ');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('POS');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('APP');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Πελάτης');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Χρήστης');?></th> 
    <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th> 

  </tr>	
</thead>
<tbody>

<?php


$row_array=[];
$ids_list=[];
while ($row = $result->fetch_assoc()) {   
  //echo '<pre>'; print_r($row);die();
     
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

  $sum_gks_price_total+=floatval($row['gks_price_total']);
  $sum_gks_price_net+=floatval($row['gks_price_net']);
  $sum_gks_price_fpa+=floatval($row['gks_price_fpa']);

  if (isset($per_payment_acquirer_name[$row['payment_acquirer_name']])==false)  {
    $per_payment_acquirer_name[$row['payment_acquirer_name']]=0;
  }
  $per_payment_acquirer_name[$row['payment_acquirer_name']]+=floatval($row['gks_price_total']);
  

  
?>

  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i);?></th>
    
    
    <td nowrap class="mytdcm"><?php echo $row['inv_acc_seira_code'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['inv_acc_number_int']<>0) echo $row['inv_acc_number_int'];?></td>
    <td nowrap class="mytdcm" ><?php 
      if ($row['gks_price_total']!=0) echo '<b>'.myCurrencyFormat($row['gks_price_total']).'</b>';
    ?></td>
    <td nowrap class="mytdcm" ><?php 
      echo $row['payment_acquirer_name'];
    ?></td>
    
    <td nowrap class="mytdcm" 
      <?php 
      //if (trim_gks($row['aade_qrurl'])=='') echo 'style="background-color:red;"';
      ?>
      ><?php 
      //if (isset($row['aade_send_date'])) echo showDate(strtotime($row['aade_send_date']), 'd/m/Y\<\b\r\>H:i:s', 1);
      if (trim_gks($row['aade_qrurl'])!='') echo '<a href="'.$row['aade_qrurl'].'">QR</a>';
      
      
    ?></td>   
    <td nowrap class="mytdcm"
      
      <?php
      $found_file='';
      if (trim_gks($row['print_file_name'])!='') {
        $relative_path='acc/inv/'.$row['id_acc_inv'].'/print/'.$row['print_file_name'];
        $local_file=GKS_FileServerShare.$relative_path;
        if (file_exists($local_file)) {
          //print_file_url
          $url_file='admin-get-file.php?fs=fileservers&file=acc%2Finv%2F'.$row['id_acc_inv'].'%2Fprint%2F'.urlencode($row['print_file_name']);
          $url_file.='&download=1';
          
          //echo '<a href="'.$url_file.'" target="_blank" id="last_print_file">'.$row['print_file_name'].'</a> ';
          $icon_color='blue';
          if (isset($files_shortcode[$relative_path])) {
            $url_file='/s/'.$shortcode_prefix.$files_shortcode[$relative_path];
            $icon_color='darkblue';
          }
          $found_file='<a href="'.$url_file.'" target="_blank"><i class="fas fa-download" style="color:'.$icon_color.';"></i></a>';
         
        }
      }
      //if ($found_file=='') echo 'style="background-color:red;"';             
    ?>><?php 
      echo $found_file;
      //echo $url_file;
    ?></td>
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['inv_date']), 'd/m/Y\<\b\r\>H:i:s', 1);?></td>   
    
    
    <td nowrap class="mytdcm"><span class="acc_inv_state_<?php echo $row['inv_state'];?>"><?php echo getAccInvStateDescr($row['inv_state']);?></span></td>
    <td nowrap class="mytdcm" ><?php 
      if (!empty($row['merchant_ref_trns'])) echo nl2br_gks($row['merchant_ref_trns']);
    ?></td>
    <td nowrap class="mytdcm" ><?php 
      if ($row['gks_price_net']!=0) echo '<b>'.myCurrencyFormat($row['gks_price_net']).'</b>';
    ?></td>
    <td nowrap class="mytdcm" ><?php 
      if ($row['gks_price_netfpa']!=0) echo myCurrencyFormat($row['gks_price_fpa']);
    ?></td>
    <td nowrap class="mytdcm"><?php echo $row['pos_name'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['erp_app_mobile_name'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['gks_nickname'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['gks_nickname_edit'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['id_acc_inv'];?></td>
    


  </tr>
<?php      
}
?>

</tbody>
<tfoot>
  <tr class="table-warning">
    <td class="bottomsums mytdcml" colspan="3" nowrap=""><?php echo gks_lang('Σύνολα');?></td>
    <td class="bottomsums mytdcm" nowrap="" align="right"><b><?php echo myCurrencyFormat($sum_gks_price_total);?></b></td>  
    <td class="bottomsums mytdcm" nowrap="" align="right" colspan="6"></td>  
    <td class="bottomsums mytdcm" nowrap="" align="right"><?php echo myCurrencyFormat($sum_gks_price_net);?></td>  
    <td class="bottomsums mytdcm" nowrap="" align="right"><?php echo myCurrencyFormat($sum_gks_price_fpa);?></td>  
    <td class="bottomsums mytdcm" nowrap="" align="right" colspan="6"></td>  
  </tr>
  <tr>
    <td class="bottomsums mytdcml" nowrap="" align="right" colspan="17">
<?php    
foreach ($per_payment_acquirer_name as $key => $value) {
  echo $key.': '.myCurrencyFormat($value).'<br>';
} 
?>    
</td> 
  </tr>
</tfoot>

</table>

<?php
include_once('_my_footer_admin.php');

die();
