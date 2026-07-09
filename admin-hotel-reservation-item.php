<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_reservation',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$perm_gks_hotel_reservation_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_reservation','view',0);
$perm_gks_hotel_reservation_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_reservation','edit',0);
$perm_gks_hotel_reservation_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_reservation','add',0);
$perm_gks_hotel_reservation_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_hotel_reservation','delete',0);

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');
 
$gks_voip_params=gks_voip_user_params();   

$gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_reservation',['from'=>'item']);





$user_hotels=gks_get_hotels_list();
if (count($user_hotels)==0) {
  debug_mail(false,'hotel not found','');
  echo 'hotel not found';
  die(); 
}
//print '<pre>';print_r($user_hotels);die();

//print date('Y-m-d H:i:s',1648771200); echo '|'; print date('Y-m-d H:i:s',1648771200);die();
//2022-04-01 00:00:00|2022-04-01 00:00:00

$show_customer_more=0;
if (isset($_gks_session['gks']['basket']['hotel']['reservation']['show_customer_more'])) $show_customer_more = intval($_gks_session['gks']['basket']['hotel']['reservation']['show_customer_more']);




$template_id=0; if (isset($_GET['template_id'])) $template_id=intval($_GET['template_id']);

if ($id <= 0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}



if ($id > 0) {
  
  $sql=select_gks_hotel_reservation();
  $sql.=" where id_hotel_reservation = ".$id;
  //echo '<pre>'.$sql;die();
  if (count($perm_id_hotel_ids)>0) $sql.=" and gks_hotel_reservation.hotel_id in (".implode(',',$perm_id_hotel_ids).")";
  if (count($perm_id_company_ids)>0) $sql.=" and gks_hotel.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_hotel.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_hotel_reservation.reservation_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_hotel_reservation.reservation_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  

  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $user_id=$row['user_id'];
  $user_mobile=$row['user_mobile'];
  $user_email=$row['user_email'];
  $user_title='';
  if ($user_id>0) { 
    $user_title = trim_gks($row['gks_nickname']);
  } else {
    $user_title = $row['user_last_name'].' '.$row['user_first_name'].' '.$row['user_mobile'].' '.$row['user_email'];
    $user_title=trim_gks($user_title);
    $user_title=str_replace('  ', ' ', $user_title);
    $user_title=str_replace('  ', ' ', $user_title);
    $user_title=trim_gks($user_title);
  }

  $efd=0;
  foreach ($user_hotels as $row_select) {
    if ($row_select['id']==$row['hotel_id']) {
      $efd=$row_select['efd'];
      break;
    }
  }
  $efd=$efd*$row['num_days']*$row['rooms_plithos'];
  
} else {

  $mytime = time();
  //$mytime = strtotime('2019-01-05 04:30:00');
  
  $row = array();
  $row['id_hotel_reservation'] = -1;
  $row['hotel_id']=0;
  $row['company_id']=0;
  $row['company_sub_id']=0;
  if (count($user_hotels)>=1) foreach ($user_hotels as $value) {
    $row['hotel_id']=$value['id']; 
    $row['company_id']=$value['company_id'];
    $row['company_sub_id']=$value['company_sub_id'];
    //print '<pre>';print_r($value);die();
    break;
  }
  $hotel_params=gks_hotel_get_params($row['hotel_id']);
  
  
  $row['reservation_guid']='';
  $row['reservation_status'] = '010draft';
  $row['reservation_date']=date('Y-m-d H:i:s');
  
  $row['check_in'] = date('Y-m-d', _time_user($mytime,1) - GKS_ERP_START_VARDIA*60*60).' '.$hotel_params['hotel_default_checkin'];
  $row['check_out'] = date('Y-m-d', _time_user($mytime,1) - GKS_ERP_START_VARDIA*60*60 + 24*60*60).' '.$hotel_params['hotel_default_checkout'];
  $row['num_days']=1;
  $row['num_adults']=2;
  $row['num_childs']=0;
  $row['num_child_kounies']=0;
  $row['num_extra_beds']=0;
  $user_id=0;
  $user_email='';
  $user_mobile='';
  $user_title='';
  




  $row['user_id']=0;
  $row['gks_nickname']='';
  $row['user_first_name']='';
  $row['user_last_name']='';
  $row['user_email']='';
  $row['user_mobile']='';
  $row['user_lang']='el-GR';
  $row['parastatiko']=0;
  $row['eponimia']='';
  $row['title']='';
  $row['afm']='';
  $row['doy']='';
  $row['epaggelma']='';
  $row['ma_odos']='';
  $row['ma_arithmos']='';
  $row['ma_orofos']='';
  $row['ma_perioxi']='';
  $row['ma_poli']='';
  $row['ma_tk']='';
  $row['ma_country_id']=91;
  $row['ma_nomos_id']=26;
  $row['fiscal_position_id']=1;
  $row['pricelist_id']=1;
  $row['pelati_sxolio']='';
  $row['order_sxolio']='';
  $row['user_notes']='';
  $row['sxolio']='';
  $row['note_logistirio']='';

  $row['fiscal_position_id']=1;
  $row['pricelist_id']=1;
  
  
  $row['pelati_sxolio']='';
  $row['order_sxolio']='';
  
  $row['delivery_id_8']=1;
  $row['delivery_number']='';
    
  $row['coupons']=''; 
  $row['def_ekptosi']=0;   
  
  $row['products_posotita']=0;
  $row['products_varos']=0;
  $row['products_ogos']=0;
  $row['products_ogos_max_x']=0;
  $row['products_ogos_max_y']=0;
  $row['products_ogos_max_z']=0;
  $row['products_need_apostoli']=0;
  $row['products_need_pliromi']=0;

  $row['tropos_apostolis']=1;
  $row['tropos_pliromis']=1;
  $row['kostos_apostolis']=0;
  $row['kostos_pliromis']=0;
  
  $row['gks_price_net']=0;
  $row['gks_price_fpa']=0;
  $row['gks_price_netfpa']=0;
  
  $row['gks_price_total']=0;
  $row['kostos_apostolis']=0;
  $row['kostos_pliromis']=0;
  $row['childs_ages_list']='';
  
  $row['totalWithheldAmount']=0;
  $row['totalOtherTaxesAmount']=0;
  $row['totalStampDutyamount']=0;
  $row['totalFeesAmount']=0;

  $row['print_date']='';
  $row['print_file_name']='';
  $row['print_file_url']='';
  $row['print_user_id']='';
  $row['print_reservation_status']='';


  $row['affect_balance']=1;
  $row['affect_balance_all_poso']=1;
  $row['affect_balance_all_poso_type']='pliroteo';
  $row['affect_balance_poso']=0;
   
  $row['assigned_id']=0;
  $row['gks_nickname_assigned']='';
  $row['crm_channel_id']=0;
  $row['crm_channel_sale_descr']='';
  $row['crm_channel_contact_id']=0;
  $row['crm_channel_contact_gks_nickname']='';
  $row['crm_channel_campain_id']=0;
  $row['ads_campain_name']='';
  $row['crm_channel_url']='';
  $row['crm_channel_code']='';  
  $row['crm_channel_text']='';  

  $row['reservation_journal_id']=0;
  $row['reservation_seira_id']=0;
  $row['reservation_seira_code']='';
  $row['reservation_number_int']=0;
  $row['reservation_number_str']='';
  $row['is_xeirografi']=0;

  $row['bank_deposit_9digit']='';
  
  $row['hotel_color']='';
  $row['hotel_booking_number']='';
  
  $efd=0;
}

$reservation_seira_id=$row['reservation_seira_id'];

$hotel_params=gks_hotel_get_params($row['hotel_id']);

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

$gks_lock=false;
$gks_number_lock=false;
$gks_user_lock=false;

if (in_array($row['reservation_status'], array(
      //'070wait_payment','080confirm',
      '100completed','110payment'))) {
  $gks_lock=true;
} else {
  if ($row['reservation_number_int'] > 0 and $row['is_xeirografi']==0 and 
    in_array($row['reservation_status'],array(
      '005prodraft','010draft',
      '070wait_payment','080confirm',
      ))) { 
    $gks_number_lock=true;
  }
}

$row_reserv=$row;

$pelatisislink=false;
if ($user_id>0) $pelatisislink=true;

//echo $row['check_in'];
//echo '<br>';
//die();
//echo _time_user($mytime,1).'|';
//echo date('d/m/Y H:i:s', _time_user($mytime,1));
//die();

stat_record();
if ($id > 0) $my_page_title=gks_lang('Κράτηση').': #'.$row['id_hotel_reservation'];
else $my_page_title=gks_lang('Νέα Κράτηση');


if ($id==-1) {
  $nav_active_array=array('hotel','hotel_new_reservation');  
} else {
  $nav_active_array=array('hotel','hotel_reservation');
}

//echo $row['hotel_id'];die();

$defs = get_def_check($row['hotel_id']);
$reservation_status=$row['reservation_status'];
$products_posotita=$row['products_posotita'];
$products_varos=$row['products_varos'];
$products_ogos=$row['products_ogos'];;
$products_ogos_max_x=$row['products_ogos_max_x'];
$products_ogos_max_y=$row['products_ogos_max_y'];
$products_ogos_max_z=$row['products_ogos_max_z'];
$products_need_apostoli=$row['products_need_apostoli']==0 ? false : true;
$products_need_pliromi=$row['products_need_pliromi']==0 ? false : true;

$pliroteo=$row['gks_price_total'] + $row['kostos_apostolis'] + $row['kostos_pliromis'];

//print $pliroteo;
//die();

unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='reservation';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']=intval($row['company_id']);
$mybasketarray['company_sub_id']=intval($row['company_sub_id']);

$mybasketarray['user']['afm']=$row['afm'];
$mybasketarray['user']['ma_country_id']=$row['ma_country_id'];
$mybasketarray['parastatiko']=$row['parastatiko'];

$mybasketarray['products_varos']= $products_varos;
$mybasketarray['products_ogos']= $products_ogos;
$mybasketarray['products_ogos_max_x']= $products_ogos_max_x;
$mybasketarray['products_ogos_max_y']= $products_ogos_max_y;
$mybasketarray['products_ogos_max_z']= $products_ogos_max_z;
$mybasketarray['products_need_apostoli']=$products_need_apostoli;
$mybasketarray['products_need_pliromi']=$products_need_pliromi;
$mybasketarray['tropos_apostolis'] = $row['tropos_apostolis'];
$mybasketarray['tropos_pliromis'] = $row['tropos_pliromis'];
$mybasketarray['products_total'] = $row['gks_price_total'];

$mybasketarray['kostos_apostolis'] = gks_calculate_kostos_apostolis($mybasketarray,-1);
$mybasketarray['kostos_pliromis']  = gks_calculate_kostos_pliromis ($mybasketarray,-1);

//print '<pre>';
//print_r($mybasketarray);
//print_r($mybasketarray['tropoi_pliromis_all']);
//die();


if ($row['tropos_apostolis']>0 and isset($mybasketarray['tropoi_apostolis_all'][$row['tropos_apostolis']])) $mybasketarray['tropoi_apostolis_all'][$row['tropos_apostolis']]['dm_calc_kostos']= $row['kostos_apostolis'];
if ($row['tropos_pliromis']>0 and isset($mybasketarray['tropoi_pliromis_all'][$row['tropos_pliromis']])) $mybasketarray['tropoi_pliromis_all'][$row['tropos_pliromis']]['pa_calc_kostos']= $row['kostos_pliromis'];



$mybasketarray['coupons']=array();
$coupons = trim_gks($row['coupons']);
$coupons_parts=explode('|',$coupons);
foreach ($coupons_parts as $value) {
  $value=trim_gks($value);
  if ($value!='') {
    $mybasketarray['coupons'][$value]=$value;
    $sql_coupon="SELECT pricelist_item_descr
    FROM gks_eshop_pricelist_items
    WHERE pricelist_item_coupon='".$db_link->escape_string($value)."'
    AND pricelist_id=".$row['pricelist_id'];
    $result_coupon = $db_link->query($sql_coupon);        
    if (!$result_coupon) {
      debug_mail(false,'error sql',$sql_coupon);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result_coupon->num_rows==1) {
      $row_coupon = $result_coupon->fetch_assoc();
      $mybasketarray['coupons'][$value]=$row_coupon['pricelist_item_descr'];
    }
    
  }
} 

gks_CheckAFM_Live($mybasketarray);
$check_vies=$mybasketarray['check_vies'];


include_once('_my_header_admin.php');

//print '<pre>'; print_r($mybasketarray['check_vies']); print '</pre>';

?>



<link href="css/admin-hotel-reservation-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<style type="text/css" media="print1">
    @page { 
        size1: landscape1;
    }
</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <?php if ($id > 0) {?>
      <h3>
        <?php echo gks_lang('Κράτηση');?>: <span class="gks_object_badge_secondary">#<?php echo $row['id_hotel_reservation'];?></span>
        <?php echo gks_lang('Αναφορά');?>: <span class="hotel_booking_number_head" style="background-color:<?php echo $row['hotel_color'];?>;"><?php echo $row['hotel_booking_number'];?></span>
      </h3>
    <?php } else { ?>
      <h3>
        <?php echo gks_lang('Κράτηση');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span>
      </h3>
    <?php }?>
    </div>
  </div>
</div>

<div id="mypostform">
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-4">


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>  
          
          <div class="form-group row">
            <label for="hotel_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ξενοδοχείο');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['hotel_title'];
                echo '</div>';
              } else {?>
              <select id="hotel_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <!--
                <option value="0" data-company_id_sub_id="0|0"></option>
                -->
                <?php
                foreach ($user_hotels as $row_select) {
                  echo '<option value="'.$row_select['id'].'" '.
                  'data-company_id_sub_id="'.$row_select['company_id'].'|'.$row_select['company_sub_id'].'" '.
                  'data-efd="'.myNumberFormatNo0($row_select['efd']).'" ';
                  if ($row_select['id']==$row['hotel_id']) echo ' selected ';
                  echo '>'.$row_select['descr'].'</option>';
                }?>
              </select>    
              <?php } ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="reservation_journal_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερολόγιο');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="reservation_journal_id" type="hidden" value="'.$row['reservation_journal_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['acc_journal_descr'];
                echo '</div>';
              } else {?>
              <select id="reservation_journal_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_journal, acc_journal_descr, acc_eidos_parastatikou_id, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
                gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
                whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
                whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id
                FROM (gks_acc_journal 
                LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
                LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
                WHERE gks_acc_eidi_parastatikon.eidos_parastatikou_type_id in (1100) and gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou not in (702,703,704)
                and is_disable=0 and company_id=".$row['company_id']." AND company_sub_id=".$row['company_sub_id'];
                if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_journal.id_acc_journal in (".implode(',',$perm_id_acc_journal_ids).")";
                $sql.=" ORDER BY gks_acc_journal.sortorder,gks_acc_journal.acc_journal_descr;";
                
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_journal'].'" '.
                  'data-eidi_id="'.$row_select['acc_eidos_parastatikou_id'].'" '.
                  'data-type_id="'.$row_select['eidos_parastatikou_type_id'].'" '.
                  'data-need_prev="'.$row_select['eidos_parastatikou_need_prev'].'" '.
                  'data-fpa="'.$row_select['eidos_parastatikou_has_fpa'].'" '.
                  'data-need_afm="'.$row_select['eidos_parastatikou_need_afm'].'" '.
                  'data-balance_pros="'.$row_select['eidos_parastatikou_balance_pros'].'" '.
                  'data-whi_stock_pros="'.intval($row_select['whi_eidos_parastatikou_stock_pros']).'" '. // intval kanei to null se 0
                  'data-whi_type_id="'.intval($row_select['whi_eidos_parastatikou_type_id']).'" ';       // intval kanei to null se 0
                  
                  if ($row['reservation_journal_id'] == $row_select['id_acc_journal']) echo ' selected ';
                  echo '>'.$row_select['acc_journal_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="reservation_seira_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<input id="reservation_seira_id" type="hidden" value="'.$row['reservation_seira_id'].'">';
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['seira_code'].' - '.$row['seira_descr'];
                echo '</div>';
              } else {?>
              <select id="reservation_seira_id" class="form-control form-control-sm myneedsave" <?php if ($gks_number_lock) echo 'disabled';?>>
                <option value="0"></option>
                <?php
                $sql="SELECT id_acc_seira, seira_code,seira_descr,is_xeirografi 
                FROM gks_acc_seires 
                WHERE is_disable=0 and acc_journal_id=".$row['reservation_journal_id'];
                if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_seires.id_acc_seira in (".implode(',',$perm_id_acc_seira_ids).")";
                $sql.=" ORDER BY sortorder,seira_code;";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_acc_seira'].'" '.
                  'data-is_xeirografi="'.$row_select['is_xeirografi'].'" ';
                  if ($row['reservation_seira_id'] == $row_select['id_acc_seira']) echo ' selected ';
                  echo '>'.$row_select['seira_code'].' - '.$row_select['seira_descr'].'</option>';
                }
                ?>                
              </select>    
              <?php } ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="reservation_number_int" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['reservation_number_int'];
                echo '</div>';
              } else {?>
              <input id="reservation_number_int" class="form-control form-control-sm myneedsave" type="number" 
              value="<?php if ($row['reservation_number_int']>0) echo $row['reservation_number_int'];?>" style="max-width:100px;" 
              placeholder="" min="0" step="1"
              <?php if ($gks_number_lock or $row['is_xeirografi']==0) echo 'disabled';?>>
              <?php } ?>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="reservation_date" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία Κράτησης');?>:</label>
            <div class="col-sm-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo showDate(strtotime($row['reservation_date']), 'd/m/Y H:i', 1);
                echo '</div>';
              } else {?>
              <input id="reservation_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['reservation_date'])) echo  showDate(strtotime($row['reservation_date']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
              <?php } ?>
            </div>
          </div>           
       
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-sm-8">
              <span class="reservation_status_<?php echo $row['reservation_status'];?>"><?php echo getHotelReservationStatusDescr($row['reservation_status']);?></span>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κωδικός κράτησης');?>:</label>
            <div class="col-sm-8">
              <span class="hotel_booking_number" style="background-color:<?php echo $row['hotel_color'];?>;"><?php echo $row['hotel_booking_number'];?></span>
            </div>
          </div>
                              
          <div class="form-group row">
            <label for="check_in" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Άφιξη');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo date('d/m/Y H:i', strtotime($row['check_in']));
                echo '</div>';
              } else {?>
              <input id="check_in" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['check_in'])) echo date('d/m/Y H:i', strtotime($row['check_in']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <?php } ?>
            </div>
          </div>         
          <div class="form-group row">
            <label for="check_out" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αναχώρηση');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo date('d/m/Y H:i', strtotime($row['check_out']));
                echo '</div>';
              } else {?>
              <input id="check_out" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['check_out'])) echo date('d/m/Y H:i', strtotime($row['check_out']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="num_days" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Διανυκτερεύσεις');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['num_days'];
                echo '</div>';
              } else {?>
              <input id="num_days" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['num_days']>0) echo $row['num_days'];?>" min="1" max="1000" autocomplete="<?php echo $autocomplete_gks_disable;?>" disabled>
              <?php } ?>
            </div>
          </div>
          
          
          <div class="form-group row">
            <label for="num_adults" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενήλικες');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if ($row['num_adults']>0) echo $row['num_adults'];
                echo '</div>';
              } else {?>
              <input id="num_adults" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['num_adults']>0) echo $row['num_adults'];?>" min="0" max="1000" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <?php } ?>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="num_childs" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παιδιά');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if ($row['num_childs']>0) echo $row['num_childs'];
                echo '</div>';
              } else {?>
              <input id="num_childs" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['num_childs']>0) echo $row['num_childs'];?>" min="0" max="1000" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <?php } ?>
            </div>
          </div>
          
          <div id="childs_ages_list_main_div">
            <?php
            $childs_ages_list=array();
            if ($row['childs_ages_list']=='') $row['childs_ages_list']='[]';
            $childs_ages_list=json_decode(trim_gks($row['childs_ages_list']), true); 
            
            //echo '<pre>';print_r($childs_ages_list);die();
            
            $child_age_price_ap_array=array();
            for($ia=0; $ia<=$hotel_params['hotel_child_accept_max_age']; $ia++) {
              if ($ia < $hotel_params['hotel_child_accept_above_age']) {
                $child_age_price_ap_array[$ia]='';
              } else {
                $foundprice=gks_lang('ως ενήλικας');
                foreach ($hotel_params['hotel_child_age_price'] as $valia) {
                  if ($ia >= $valia['from'] and $ia <= $valia['to']) {
                    if ($valia['price']==0) $foundprice=gks_lang('Δωρεάν');
                    else {
                      $foundprice=myCurrencyFormat($valia['price']);
                      if ($valia['type']=='night') $foundprice.= ' / '.gks_lang('Βράδυ');
                      else if ($valia['type']=='stay') $foundprice.= ' / '.gks_lang('Κράτηση');
                    }
                    break;
                  }
                } 
                $child_age_price_ap_array[$ia] = $ia.' '.gks_lang('ετών').' ('.$foundprice.')';
              }
            }
            for ($ic=1;$ic<=$row['num_childs'];$ic++) { 
              
            ?>
              
          <div class="form-group row childs_ages_list_div" data-aa="<?php echo $ic;?>">
            <label for="childs_ages_list_<?php echo $ic;?>" class="childs_ages_list_label col-md-4 col-form-label form-control-sm text-md-right"><?php
              $tmpmsg=gks_lang('Ηλικία [n] παιδιού');
              $tmpmsg=str_replace('[n]',gks_n_ho($ic),$tmpmsg);              
              echo $tmpmsg;
              ?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                for($ia=0; $ia<=$hotel_params['hotel_child_accept_max_age']; $ia++) {
                  if ($child_age_price_ap_array[$ia]!='') {
                    if (isset($childs_ages_list[$ic-1]) and $ia==$childs_ages_list[$ic-1]) {
                      echo $child_age_price_ap_array[$ia]; 
                      break;
                    }
                  }
                }
                echo '</div>';
              } else {?>
              <select id="childs_ages_list_<?php echo $ic;?>" class="childs_ages_list_select form-control form-control-sm">
                <option value="-1"></option>
                <?php
                for($ia=0; $ia<=$hotel_params['hotel_child_accept_max_age']; $ia++) {
                  if ($child_age_price_ap_array[$ia]!='') {
                    echo '<option value="'.$ia.'" ';
                    if (isset($childs_ages_list[$ic-1]) and $ia==$childs_ages_list[$ic-1]) echo ' selected ';
                    echo '>'.$child_age_price_ap_array[$ia].'</option>';
                  }
                }?>
              </select>
              <?php } ?>
            </div>
          </div>          
          <?php    
            }
            ?>
          </div>
          
                  
  

          <div class="form-group row">
            <label for="fiscal_position_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φορολογική Θέση');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo  $row['fiscal_position_descr'];
                echo '</div>';
              } else {?>              
              <select id="fiscal_position_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_fiscal_position=gks_lang_data_obj_prepare('gks_eshop_fiscal_position','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_fiscal_position, array('fiscal_position_descr'));
                $sql="select id_fiscal_position,".gks_lang_sql_field('fiscal_position_descr',$lang_prepare_gks_eshop_fiscal_position)." 
                FROM ".$lang_prepare_gks_eshop_fiscal_position['sql']['from1']." gks_eshop_fiscal_position 
                ".$lang_prepare_gks_eshop_fiscal_position['sql']['from2']."
                where fiscal_position_disable=0 
                order by fiscal_position_sortorder,fiscal_position_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_fiscal_position'].'" ';
                  if ($row_select['id_fiscal_position']==$row['fiscal_position_id']) echo ' selected ';
                  echo '>'.$row_select['fiscal_position_descr'].'</option>';
                }?>
              </select>    
              <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="pricelist_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμοκατάλογος');?>:</label>
            <div class="col-md-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo  $row['pricelist_descr'];
                echo '</div>';
              } else {?>              
              <select id="pricelist_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_pricelist=gks_lang_data_obj_prepare('gks_eshop_pricelist','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_pricelist, array('pricelist_descr'));
                $sql="select id_pricelist,".gks_lang_sql_field('pricelist_descr',$lang_prepare_gks_eshop_pricelist)." 
                FROM ".$lang_prepare_gks_eshop_pricelist['sql']['from1']." gks_eshop_pricelist 
                ".$lang_prepare_gks_eshop_pricelist['sql']['from2']."
                where pricelist_disable=0 
                order by sortorder,pricelist_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_pricelist'].'" ';
                  if ($row_select['id_pricelist']==$row['pricelist_id']) echo ' selected ';
                  echo '>'.$row_select['pricelist_descr'].'</option>';
                }?>
              </select>    
              <?php } ?>
            </div>
          </div>  
          <div class="form-group row">
            <label for="def_ekptosi" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προεπιλεγμένη έκπτωση');?>:</label>
            <div class="col-sm-8">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo myNumberFormatNo0Local($row['def_ekptosi']).'%';
                echo '</div>';
              } else {?>
              <input id="def_ekptosi" type="number" class="form-control form-control-sm myneedsave" value="<?php echo number_format($row['def_ekptosi'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="max-width:100px;display: inline-block;" placeholder="" min="0" step="<?php echo $GKS_INPUT_STEP_POSOSTO;?>"> %
              <button class="btn btn-sm btn-primary" id="def_ekptosi_set"><?php echo gks_lang('Εφαρμογή');?></button>
              <?php } ?>              
            </div>
          </div> 

          <div class="form-group row">
            <label for="assigned_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ανάθεση σε');?>:</label>
            <div class="col-sm-8">
              <input id="assigned_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname_assigned']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['assigned_id'];?>">
            </div>
          </div>
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κουπόνια');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('koup');?>>
          <?php if ($gks_lock==false) {?>
          <div style="text-align: center;">
            <input type="text" class="form-control form-control-sm myneedsave" value="" style="max-width:180px;text-align:left;display: inline;vertical-align: middle;" id="input_coupon" />
            <span style="" id="coupon_use" class="btn btn-sm btn-primary"><?php echo gks_lang('Προσθήκη Κουπονιού');?></span>
          </div>
          <?php } ?>
          <div id="coupons_html" class="form-control-sm">   
<?php
            $coupons_html='';
            foreach ($mybasketarray['coupons'] as $key => $coupon) {
               $coupons_html.='<span class="tooltipster coupons_span" title="'.$coupon.'">
               <span class="coupons text-sm">'.$key.
               ($gks_lock ? '' : ' <i class="coupon_delete fas fa-trash-alt gks_reservation_delete_icon" data-coupon="'.$key.'" style=""></i>').
               '</span></span> ';
            } 
            if ($coupons_html!='') {
              $coupons_html=gks_lang('Κουπόνια').': '.$coupons_html;
              echo $coupons_html;
            }
?>            
          </div>         
        </div>
      
         
      </div>
      
      
    </div>
    <div class="col-sm-8">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Επαφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('user');?>>


          <div class="form-group row">
            <label for="user" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
            <div class="col-sm-4">
            <?php if ($gks_lock) {
              echo '<div class="gks_flock form-control-sm">';
                if ($row['user_id']>0)
                  echo '<a href="admin-users-item.php?id='.$row['user_id'].'" class="email_contact_name">'.$row['gks_nickname'].'</a>';
                else
                   echo '<span class="email_contact_name">'.$row['gks_nickname'].'</span>';
                echo '<input type="hidden" id="user_id" value="'.$row['user_id'].'">';
              echo '</div>';                
            } else {?>
                  <input id="user" type="text" class="form-control form-control-sm myneedsave email_contact_name" 
                  value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
                  style="width:calc(98% - 22px);display:inline;" 
                  placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                  <input id="user_id" type="hidden" value="<?php echo $row['user_id'];?>" class="myneedsave">
                  <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['user_id'];?>" style="<?php if ($row['user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
                  <i id="user_save" class="fas fa-save" style="<?php if ($row['user_id']>0) echo 'display:none';?>;color: #35dc35;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Δημιουργία επαφής');?>"></i>
            <?php } ?>
            </div>
            <?php if ($gks_lock==false) {?>
            <label for="" class="col-sm-2 col-form-label form-control-sm text-sm-right"><a class="tooltipster" title="<?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?>" href="https://www.aade.gr/epiheiriseis/forologikes-ypiresies/mitroo/anazitisi-basikon-stoiheion-mitrooy-epiheiriseon" target="_blank">aade.gr</a>:</label>
            <div class="col-sm-4">
              <button style="" id="btn_gsis_get" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση με το ΑΦΜ');?></button>
            </div>
            <?php } ?>
          </div>


          <div class="form-group row" style="margin-bottom: 0px;">
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_pelati_sxolio" style="<?php echo (trim_gks($row['pelati_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_pelati_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['pelati_sxolio']);?></div>
              </div>
                            
            </div>
            
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_order_sxolio" style="<?php echo (trim_gks($row['order_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['order_sxolio']);?></div>
              </div>               
            </div>
          </div>









          <div class="form-group row">
            <label for="dr_user_first_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['user_first_name'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_first_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['user_first_name']);?>">
              <?php } ?>
            </div>
            <label for="dr_user_last_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επώνυμο');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['user_last_name'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_last_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['user_last_name']);?>">
              <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="dr_user_email" class="col-sm-2 col-form-label form-control-sm text-sm-right">email:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if (isset($row['user_email'])) echo '<a href="mailto:'.$row['user_email'].'">'.$row['user_email'].'</a>';
                echo '</div>'; 
                echo '<input id="dr_user_email" type="hidden" value="'.htmlspecialchars_gks($row['user_email']).'">';               
              } else {?>
              <input id="dr_user_email" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['user_email']);?>">
              <?php } ?>
            </div>
            <label for="dr_user_mobile" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κινητό');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  if (isset($row['user_mobile'])) echo '<a href="tel:'.$row['user_mobile'].'" class="'.$gks_voip_params['class_span'].'">'.$row['user_mobile'].'</a>';
                  echo $gks_voip_params['html_after_span'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_mobile" type="text" class="form-control form-control-sm myneedsave <?php echo $gks_voip_params['class_input'];?>" value="<?php echo htmlspecialchars_gks($row['user_mobile']);?>">
              <?php echo $gks_voip_params['html_after_input'];?>
              <?php } ?>
            </div>
          </div>
              
          <div class="form-group row">
            <label for="dr_user_lang" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['lang_name'];
                echo '</div>';  
                echo '<input type="hidden" id="dr_user_lang" value="'.$row['user_lang'].'">';              
              } else {?>
              <select id="dr_user_lang" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php
                $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
                $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
                FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
                ".$lang_prepare_gks_lang['sql']['from2']."
                ORDER BY lang_sortorder,lang_name";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_lang'].'" ';
                  if ($row['user_lang'] == $row_select['id_lang']) echo ' selected ';
                  echo '>'.$row_select['lang_name'].'</option>';
                }
                ?>
              </select>                  
              <?php } ?>
            </div>
          </div>
          

          <div class="row">  
            <div class="col-md-12">
              <div class="text-sm-center" style="font-weight: bold;"><?php echo gks_lang('Τύπος Παραστατικού');?></div>
            </div>
          </div>   
          <div class="row">  
            <div class="col-md-12 form-control-sm text-sm-center">
              <span style="white-space: nowrap;"><input type="radio" name="form_parastatiko" value="0" id="form_parastatiko_apodiji"   <?php echo ($row['parastatiko'] == 0 ? ' checked ' : '');?> <?php if ($gks_lock) echo 'disabled';?>> <label class="gks_label" for="form_parastatiko_apodiji" style="display:inline;padding-right:18px" ><?php echo gks_lang('Απόδειξη');?></label></span> 
              <span style="white-space: nowrap;"><input type="radio" name="form_parastatiko" value="1" id="form_parastatiko_timologio" <?php echo ($row['parastatiko'] == 1 ? ' checked ' : '');?> <?php if ($gks_lock) echo 'disabled';?>> <label class="gks_label" for="form_parastatiko_timologio" style="display:inline"><?php echo gks_lang('Τιμολόγιο');?></label></span>
            </div>
          </div>   
          <div id="div_parastatiko_timologio" <?php echo ($row['parastatiko'] == 0 ? ' style="display: none;" ' : '');?>>
            <div class="form-group row">
              <label for="dr_user_eponimia" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επωνυμία');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['eponimia'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_eponimia" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['eponimia']);?>" >
              <?php } ?>
              </div>
              <label for="dr_user_title" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τίτλος');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['title'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['title']);?>" >
              <?php } ?>
              </div>
            </div>
            <?php
            $ee_initials='';
            $sql="select id_country,country_ee,country_name,country_initials 
            FROM gks_country where id_country=".$row['ma_country_id'];
            $result_select = $db_link->query($sql);        
            if (!$result_select) {
              debug_mail(false,'error sql',$sql);
              die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
            }
            if ($result_select->num_rows==1) {
              $row_select = $result_select->fetch_assoc();
              $ee_initials=trim_gks($row_select['country_ee']);
            }
           
            ?>
            
            
            <div class="form-group row">
              <label for="dr_user_afm" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    if ($ee_initials!='') echo $ee_initials.' ';
                    echo $row['afm'];
                  	echo '<input type="hidden" id="dr_user_afm" value="'.$row['afm'].'">';
                    echo '<span 
                    id="dr_user_afm_views_run" style="'.($check_vies['run'] ? 'display:inline-block;' : 'display:none;').'padding-left: 10px;position:relative;top:-3px;">'.$check_vies['views_run_img'].'</span>';

                  echo '</div>';                
                } else {?>
                <span id="dr_user_afm_ee_initials" style="<?php echo ($ee_initials!='' ? '' : 'display:none;');?>"><?php echo $ee_initials;?></span><input 
                  style="display: inline-block;max-width:100%;text-align:left;vertical-align: middle;<?php echo ($ee_initials=='' ? 'width:100%;' : 'width:calc(100% - 75px);');?>"
                  id="dr_user_afm" type="text" class="form-control form-control-sm myneedsave <?php echo ($ee_initials=='' ? '':'dr_user_afm_views');?>" <?php if ($gks_user_lock) echo 'disabled';?> value="<?php echo htmlspecialchars_gks($row['afm']);?>" ><span 
                  id="dr_user_afm_views_run" style="<?php echo ($check_vies['run'] ? '' : 'display:none;');?>"><?php echo $check_vies['views_run_img'];?></span>
                <?php
                //$out.='<span id="dr_user_afm_ee_initials" style="'.($ee_initials!='' ? '' : 'display:none;').';">'.$ee_initials.'</span>';
                //$out.='<span id="dr_user_afm_views_run" style="'.($check_vies['run'] ? '' : 'display:none;').'">'.$views_run_img.'</span>';
                ?>
                <?php } ?>
              </div>
              <label for="dr_user_doy" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
              <div class="col-sm-4">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['doy'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_doy" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['doy']);?>" >
                <?php } ?>
              </div>
            </div>


            <div class="form-group row">
              <label for="dr_user_epaggelma" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
              <div class="col-sm-10">
                <?php if ($gks_lock) {
                  echo '<div class="gks_flock form-control-sm">';
                    echo $row['epaggelma'];
                  echo '</div>';                
                } else {?>
                <input id="dr_user_epaggelma" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['epaggelma']);?>" >
                <?php } ?>
              </div>

            </div>
          </div>
                      
          <div class="row">  
            <div class="col-md-12">
              <div class="text-sm-center" style="font-weight: bold;"><?php echo gks_lang('Διεύθυνση Τιμολόγησης');?></div>
            </div>
          </div>  
          <div class="form-group row">
            <label for="dr_user_ma_odos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_odos'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_odos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_odos']);?>" >
              <small class="form-text text-muted auto_googlemaps" id="dr_user_ma_odos_auto_googlemaps"></small>
              <?php } ?>
            </div>
            <label for="dr_user_ma_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_arithmos'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_arithmos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_arithmos']);?>" >
              <?php } ?>
            </div>            
            
          </div>
          <div class="form-group row">
            <label for="dr_user_ma_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_orofos'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_orofos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_orofos']);?>" >
              <?php } ?>
            </div>
            <label for="dr_user_ma_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_perioxi'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_perioxi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_perioxi']);?>" >
              <?php } ?>
            </div>            
          </div>

          <div class="form-group row">
            <label for="dr_user_ma_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_poli'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_poli" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_poli']);?>" >
              <?php } ?>
            </div>
            <label for="dr_user_ma_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo $row['ma_tk'];
                echo '</div>';                
              } else {?>
              <input id="dr_user_ma_tk" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ma_tk']);?>" >
              <?php } ?>
            </div>
          </div>

          <div class="form-group row">
            <label for="dr_user_ma_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');
                  echo '<input type="hidden" id="dr_user_ma_country_id_h" value="'.$row['ma_country_id'].'">';
                echo '</div>';                
              } else {?>
              <select data-dbval="<?php echo $row['ma_country_id'];?>" id="dr_user_ma_country_id" class="form-control form-control-sm myneedsave">
              </select> 
              <?php } ?>
            </div>
            <label for="dr_user_ma_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-sm-4">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm">';
                  echo gks_lang_data_trans($row['nomos_descr'],$row['ma_nomos_id'],'gks_nomoi','nomos_descr');
                echo '</div>';
              } else {?>
              <select id="dr_user_ma_nomos_id" class="form-control form-control-sm myneedsave">
                <option value="0"><?php echo gks_lang('Νομός');?>...</option>
                <?php
                $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomos['sql']['from2']."
                where country_id=".$row['ma_country_id']." ORDER BY nomos_descr";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_nomos'].'" ';
                  if ($row['ma_nomos_id'] == $row_select['id_nomos']) echo ' selected ';
                  echo '>'.$row_select['nomos_descr'].'</option>';
                }
                ?>
              </select> 
              <?php } ?>
            </div>
          </div>   
          
        </div>
      </div>
    </div>
  </div>
</div>



<?php
$sql_rooms=select_gks_hotel_reservation_room();
$sql_rooms.=" where hotel_reservation_id=".$id."
order by id_hotel_reservation_room";

$result_rooms = $db_link->query($sql_rooms);        
if (!$result_rooms) debug_mail(false,'error sql',$sql_rooms);
if (!$result_rooms) die('sql error');
 

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Δωμάτια');?>
          <?php if ($gks_lock==false) {?>
          <button type="button" class="btn btn-sm btn-primary" id="addroom2"><?php echo gks_lang('Προσθήκη');?></button>
          <?php } ?>
        </div>
        <div class="card-body" <?php echo gks_card_body('rooms');?>> 
                
        
<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" style="width:100%" cellspacing="0" cellpadding="5" align="center" id="tableroomlist">
<thead>
    <tr>	
        <th class="table-dark d-print-none" scope="col" style="text-align: center !important;" width='0%'  >#</th>
        <th class="table-dark d-print-none" scope="col" style="text-align: center !important;" width="0%"   colspan=2>ID</th> 
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  ><?php echo gks_lang('Δωμάτιο');?></th>  
        <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"  ><?php echo gks_lang('Τύπος');?></th>  
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo gks_lang('Έκπ%');?></th>             
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><span title="<?php echo gks_lang('Τιμή ανά Διανυκτέρευση');?>" class="tooltipster"><?php echo gks_lang('Τιμή/Διανυκτ');?></span></th>             
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" colspan="2" ><?php echo gks_lang('Τιμή');?></th>             
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap ><span title="<?php echo gks_lang('Ενήλικες');?>" class="tooltipster"><?php echo gks_lang('Ενη');?></span> / <span title="<?php echo gks_lang('Παιδιά');?>" class="tooltipster"><?php echo gks_lang('Παι');?></span></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="30%" ><?php echo gks_lang('Πελάτης');?></th>        
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  nowrap><?php echo gks_lang('Γλώσσα / Χώρα');?></th>   
        <th class="table-dark" scope="col" style="text-align: center !important;" width="30%" ><?php echo gks_lang('Σχόλιο');?></th>
      
              
    </tr>
</thead>
<tbody>
  
    <?php

    
    $fields_change=array();
    $sum_rooms_total=0;
    $sum_rooms_rnum_adults=0;
    $sum_rooms_rnum_childs=0;
    $sum_rooms_rnum_child_kounies=0;
    $sum_rooms_rnum_extra_beds=0;
    
    $json_rooms_list= array();
    $iroom = -1;

    $eidi_sum_quantity=0;
    $eidi_sum_price_net=0;
    $gks_get_list_reservation_rooms_gr='';
    $gks_get_list_reservation_rooms_gr.=gks_lang('Ημερομηνία Άφιξης').': '.date('d/m/Y H:i', strtotime($row['check_in']))."\n";
    $gks_get_list_reservation_rooms_gr.=gks_lang('Ημερομηνία Αναχώρησης').': '.date('d/m/Y H:i', strtotime($row['check_out']))."\n";
    $gks_get_list_reservation_rooms_gr.=gks_lang('Διανυκτερεύσεις').': '.$row['num_days']."\n";
    $gks_get_list_reservation_rooms_gr.="\n";
    
    $gks_get_list_reservation_rooms_en='';
    $gks_get_list_reservation_rooms_en.='Arrival Date'.': '.date('d/m/Y H:i', strtotime($row['check_in']))."\n";
    $gks_get_list_reservation_rooms_en.='Departure Date'.': '.date('d/m/Y H:i', strtotime($row['check_out']))."\n";
    $gks_get_list_reservation_rooms_en.='Nights/Overnights'.': '.$row['num_days']."\n";
    $gks_get_list_reservation_rooms_en.="\n";
    
    
    while ($row_room = $result_rooms->fetch_assoc()) {
    	$iroom++;
    	
    	
    	//echo '<pre>';print_r(json_decode(trim_gks($row_room['rchilds_ages_list']),true));die();
    	
      $eidi_sum_quantity+=$row_room['product_quantity'];
      $eidi_sum_price_net+=$row_room['product_price_final_all_net'];

    	
      $sum_rooms_total+=$row_room['product_price_final_all_total'];
      $sum_rooms_rnum_adults+=$row_room['rnum_adults'];
      $sum_rooms_rnum_childs+=$row_room['rnum_childs'];
      $sum_rooms_rnum_child_kounies+=$row_room['rnum_child_kounies'];
      $sum_rooms_rnum_extra_beds+=$row_room['rnum_extra_beds'];



      $gks_get_list_reservation_rooms_gr.=gks_lang('Τύπος Δωματίου').': '.$row_room['room_type_descr']."\n";
      $gks_get_list_reservation_rooms_gr.=gks_lang('Δωμάτιο').': '.$row_room['room_descr']."\n";
      $gks_get_list_reservation_rooms_gr.=gks_lang('Ενήλικες').': '.$row_room['rnum_adults']."\n";

      $gks_get_list_reservation_rooms_en.='Room Type'.': '.$row_room['room_type_descr_en_US']."\n";
      $gks_get_list_reservation_rooms_en.='Room'.': '.$row_room['room_descr_en_US']."\n";
      $gks_get_list_reservation_rooms_en.='Adults'.': '.$row_room['rnum_adults']."\n";


      if ($row_room['rnum_childs']!=0) $gks_get_list_reservation_rooms_gr.=gks_lang('Παιδιά').': '.$row_room['rnum_childs']."\n";
      if ($row_room['rnum_childs']!=0) $gks_get_list_reservation_rooms_en.=('Children').': '.$row_room['rnum_childs']."\n";
      
      if ($row_room['rnum_childs']!=0) $gks_get_list_reservation_rooms_gr.=gks_lang('Επισκέπτες').': '.($row_room['rnum_adults']+$row_room['rnum_childs'])."\n";
      if ($row_room['rnum_childs']!=0) $gks_get_list_reservation_rooms_en.=('Visitors').': '.($row_room['rnum_adults']+$row_room['rnum_childs'])."\n";

      if ($row_room['rnum_child_kounies']!=0) $gks_get_list_reservation_rooms_gr.=gks_lang('Βρεφικά κρεβάτια').': '.$row_room['rnum_child_kounies']."\n";
      if ($row_room['rnum_child_kounies']!=0) $gks_get_list_reservation_rooms_en.=('Baby cots').': '.$row_room['rnum_child_kounies']."\n";

      if ($row_room['rnum_extra_beds']!=0) $gks_get_list_reservation_rooms_gr.=gks_lang('Επιπλέον κρεβάτια').': '.$row_room['rnum_extra_beds']."\n";
      if ($row_room['rnum_extra_beds']!=0) $gks_get_list_reservation_rooms_en.=('Extra beds').': '.$row_room['rnum_extra_beds']."\n";
      
      $gks_get_list_reservation_rooms_gr.="\n";
      $gks_get_list_reservation_rooms_en.="\n";
      
      $row_room['lang_name']=gks_lang_data_trans($row_room['ruser_lang'],$row_room['idd_lang'],'gks_lang','lang_name');
      $row_room['country_name']=gks_lang_data_trans($row_room['country_name'],$row_room['ruser_ma_country_id'],'gks_country','country_name');
      
      $json_rooms_list[] = array(
        'aa' => $iroom,
        'add' => 0,
        'edit' => 0,
        'delete' => 0,
        'recid' => intval($row_room['id_hotel_reservation_room']),
        'hotel_room_id' => intval($row_room['hotel_room_id']),
        'room_descr' => $row_room['room_descr'],
        'room_type_descr' => $row_room['room_type_descr'],
        'visitors' => intval($row_room['room_type_visitors']),
        'visitors_childs' => intval($row_room['room_type_visitors_childs']),
        'visitors_max' => intval($row_room['room_type_visitors_max']),
        'room_type_child_kounies' => intval($row_room['room_type_child_kounies']),
        'room_type_extra_beds' => intval($row_room['room_type_extra_beds']),
        
        
        
        
        'rnum_adults' => intval($row_room['rnum_adults']),
        'rnum_childs' => intval($row_room['rnum_childs']),
        'rchilds_ages_list' => (trim_gks($row_room['rchilds_ages_list'])=='' ? array() : json_decode(trim_gks($row_room['rchilds_ages_list']),true)),
        'rnum_child_kounies' => intval($row_room['rnum_child_kounies']),
        //'rchild_kounies_ages_list' => (trim_gks($row_room['rchild_kounies_ages_list'])=='' ? array() : json_decode(trim_gks($row_room['rchild_kounies_ages_list'],true))),
        'rnum_extra_beds' => intval($row_room['rnum_extra_beds']),
        //'rextra_beds_ages_list' => (trim_gks($row_room['rextra_beds_ages_list'])=='' ? array() : json_decode(trim_gks($row_room['rextra_beds_ages_list'],true))),
        
        'ruser_id' => intval($row_room['ruser_id']),
        'gks_nickname' => $row_room['gks_nickname'],
        'ruser_lang' => $row_room['ruser_lang'],
        'ruser_first_name' => $row_room['ruser_first_name'],
        'ruser_last_name' => $row_room['ruser_last_name'],
        'ruser_email' => $row_room['ruser_email'],
        'ruser_mobile' => $row_room['ruser_mobile'],
        'ruser_ma_odos' => $row_room['ruser_ma_odos'],
        'ruser_ma_arithmos' => $row_room['ruser_ma_arithmos'],
        'ruser_ma_orofos' => $row_room['ruser_ma_orofos'],
        'ruser_ma_perioxi' => $row_room['ruser_ma_perioxi'],
        'ruser_ma_poli' => $row_room['ruser_ma_poli'],
        'ruser_ma_tk' => $row_room['ruser_ma_tk'],
        'ruser_ma_country_id' => intval($row_room['ruser_ma_country_id']),
        'ruser_ma_nomos_id' => intval($row_room['ruser_ma_nomos_id']),
        'rsxolio' => $row_room['rsxolio'],
        'ruser_fiscal_position_id' => intval($row_room['ruser_fiscal_position_id']),
        'ruser_pricelist_id' => intval($row_room['ruser_pricelist_id']),
        
        'ajia_total' => floatval($row_room['product_price_final_all_total']),
        'gks_ekptosi_pososto' => floatval($row_room['product_price_ekptosi_pososto']),
      );

      //print '<pre>';print_r($json_rooms_list);print '</pre>';

      $ekptosi_poso_html='';
      $ekptosi_poso = $row_room['product_price_start_all_total']-$row_room['product_price_final_all_total'];
      if (abs($ekptosi_poso) >= 0.01) $ekptosi_poso_html= myCurrencyFormat($ekptosi_poso,false);
      
      $fields_change[$iroom]='';
      if (abs($ekptosi_poso) >= 0.01) {
        if ($row_room['product_price_coupon_use']=='') {
          $fields_change[$iroom]='gks_price_final'; //gks_ekptosi
        } else {
          if ($row_room['product_price_coupon_use_disabled']!=0) {
            $fields_change[$iroom]='gks_price_final';  //gks_ekptosi
          } else {
            $fields_change[$iroom]='';
          }
        }
      } else {
        if ($row_room['product_price_coupon_use']=='') {
          
        } else {
          $fields_change[$iroom]='gks_price_final';
        }
      } 
      //$fields_change[$iroom] = 'gks_price_final';      
    	
      $other_taxes=array();
      $other_taxes['withheldPercentCategory']=intval($row_room['product_withheldPercentCategory']);
      $other_taxes['withheldAmount']=floatval($row_room['product_withheldAmount']);
      $other_taxes['otherTaxesPercentCategory']=intval($row_room['product_otherTaxesPercentCategory']);
      $other_taxes['otherTaxesAmount']=floatval($row_room['product_otherTaxesAmount']);
      $other_taxes['stampDutyPercentCategory']=intval($row_room['product_stampDutyPercentCategory']);
      $other_taxes['stampDutyAmount']=floatval($row_room['product_stampDutyAmount']);
      $other_taxes['feesPercentCategory']=intval($row_room['product_feesPercentCategory']);
      $other_taxes['feesAmount']=floatval($row_room['product_feesAmount']);

      $other_taxes_tooltip='';
      if ($row_room['product_withheldPercentCategory']!=0) {
        $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Φόροι Παρακρατούμενοι').'</th><td nowrap style="text-align:left;">'.$row_room['aade_katigoria_parakratoumemenon_foron_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($row_room['product_withheldAmount']).'</td></tr>';
      }
      if ($row_room['product_otherTaxesPercentCategory']!=0) {
        $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Λοιποί Φόροι').'</th><td nowrap style="text-align:left;">'.$row_room['aade_katigoria_loipon_foron_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($row_room['product_otherTaxesAmount']).'</td></tr>';
      }
      if ($row_room['product_stampDutyPercentCategory']!=0) {
        $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Ψηφιακό Τέλος συναλλαγής').'</th><td nowrap style="text-align:left;">'.$row_room['aade_katigoria_xartosimou_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($row_room['product_stampDutyAmount']).'</td></tr>';
      }
      if ($row_room['product_feesPercentCategory']!=0) {
        $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Τέλη').'</th><td nowrap style="text-align:left;">'.$row_room['aade_katigoria_telon_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($row_room['product_feesAmount']).'</td></tr>';
      }
      
      if ($other_taxes_tooltip!='') {
        $other_taxes_tooltip=
        '<table class="table table-sm table-responsive1 table-striped table-bordered" style="font-size:0.8rem;width:100px;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">
        <tbody>'.
        $other_taxes_tooltip.
        '</tbody></table>';
      }
    
      $gks_room_for_delete='';
      if ($row['hotel_id']!=$row_room['hotel_id_from_room']) $gks_room_for_delete='gks_room_for_delete';
?>
  <tr class="<?php echo ($iroom % 2 == 0) ? 'even' : 'odd'; ?>" data-aa="<?php echo $iroom;?>">
    <th class="itemtd1  mytdcm p-0 d-print-none" scope="row" nowrap><?php echo ($iroom + 1);?></th>
    <td class="itemtd2  mytdcm p-0 d-print-none" nowrap><i class="editiconroom enterrow fas fa-pen" data-aa="<?php echo $iroom;?>" title="<?php echo gks_lang('Προβολή');?>" style="cursor:pointer"></i></td>
    <td class="itemtd3  mytdcm p-0 d-print-none" nowrap>
      <?php echo $row_room['id_hotel_reservation_room'];?>
      <?php if ($gks_lock==false) {?>
      <br><i class="fas fa-trash-alt deleteitem" data-aa="<?php echo $iroom;?>"></i>
      <?php }?>
    </td>
    <td class="itemtd5  mytdcml <?php echo $gks_room_for_delete;?>" data-hotel_id="<?php echo $row_room['hotel_id_from_room'];?>"><?php echo $row_room['room_descr'];?></td>
    <td class="itemtd11 mytdcml <?php echo $gks_room_for_delete;?>"><?php echo $row_room['room_type_descr'];?></td>
    <td class="itemtd12 form-group " nowrap align="center">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm gks_ekptosi_pososto_lock">';
                  if ($row_room['product_price_ekptosi_pososto']!=0) echo myNumberFormatNo0Local($row_room['product_price_ekptosi_pososto']);
                echo '</div>';
              } else {?>     
              <input type="number" class="form-control form-control-sm gks_ekptosi_pososto" data-aa="<?php echo $iroom;?>" 
              value="<?php 
              $valnotzero=myNumberFormatNo0($row_room['product_price_ekptosi_pososto']);
              echo $valnotzero;?>" 
              data-prev-value="<?php echo $valnotzero;?>" 
              style="text-align:right;min-width:100px;" min=0 step="<?php echo $GKS_INPUT_STEP_POSOSTO;?>"
              ><?php }
              $product_price_coupon_use=$row_room['product_price_coupon_use'];
              
              ?>  
              <div class="gks_coupon" data-aa="<?php echo $iroom;?>"><div 
                class="gks_coupon_item <?php if ($row_room['product_price_coupon_use_disabled']!=0) echo 'gks_coupon_item_disabled'.($gks_lock ? '_lock' :'');?>" data-aa="<?php echo $iroom;?>" style="<?php echo ($product_price_coupon_use=='' ? 'display:none;' : '');?>"   ><?php echo $product_price_coupon_use;?></div></div>              

      
    </td>    
    <td class="itemtd14" nowrap align="center">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm gks_price_lock">';
                  if ($row['num_days']>0) {
                    echo myCurrencyFormat($row_room['product_price_final_all_total']/$row['num_days'],false);
                  }
                echo '</div>';
                ?>
                <?php
              } else {?>     
              <input type="number" class="form-control form-control-sm gks_price_per_item" data-aa="<?php echo $iroom;?>" 
              value="<?php 
              if ($row['num_days']>0) {
                echo number_format($row_room['product_price_final_all_total']/$row['num_days'],2,'.','');
              }
              ?>" style="text-align:right;min-width:100px;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
              <?php } ?>
    
    </td>    
    <td class="itemtd6" nowrap align="center">
              <?php if ($gks_lock) {
                echo '<div class="gks_flock form-control-sm gks_price_lock">';
                  echo myCurrencyFormat($row_room['product_price_final_all_total'],false);
                echo '</div>';
                ?>
                <span style="display:hidden;"
                  class="gks_price_final" data-aa="<?php echo $iroom;?>" 
                  data-ajia_table_math="<?php echo base64_encode($row_room['room_ajia_table_math']);?>"
                  data-ajia_table_html="<?php echo base64_encode($row_room['room_ajia_table_html']);?>"
                  data-other_taxes_tooltip="<?php echo base64_encode($other_taxes_tooltip);?>"
                </span>
                <?php
              } else {?>     

              <input type="number" class="form-control form-control-sm gks_price_final" data-aa="<?php echo $iroom;?>" 
              value="<?php echo number_format($row_room['product_price_final_all_total'],2,'.','');?>" 
              style="text-align:right;min-width:100px;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
              
              data-product_id="<?php echo                                   $row_room['product_id'];?>"                      
              data-product_fpa_base_id="<?php echo                          $row_room['product_fpa_base_id'];?>"             
              data-product_fpa_id="<?php echo                               $row_room['product_fpa_id'];?>"             
              data-product_fpa_pososto="<?php echo                          $row_room['product_fpa_pososto'];?>"               
              data-product_fpa_id_json="<?php echo                          base64_encode(trim_gks($row_room['product_fpa_id_json']));?>"               
              data-product_price_include_vat="<?php echo                    $row_room['product_price_include_vat'];?>"         
              data-product_price_start_peritem_db="<?php echo               $row_room['product_price_start_peritem_db'];?>"    
              data-product_price_start_peritem_net="<?php echo              $row_room['product_price_start_peritem_net'];?>"   
              data-product_price_start_peritem_fpa="<?php echo              $row_room['product_price_start_peritem_fpa'];?>"   
              data-product_price_start_peritem_total="<?php echo            $row_room['product_price_start_peritem_total'];?>" 
              data-product_price_start_all_net="<?php echo                  $row_room['product_price_start_all_net'];?>"      
              data-product_price_start_all_fpa="<?php echo                  $row_room['product_price_start_all_fpa'];?>"       
              data-product_price_start_all_total="<?php echo                $row_room['product_price_start_all_total'];?>"     
              data-product_price_final_peritem_db="<?php echo               $row_room['product_price_final_peritem_db'];?>"    
              data-product_price_final_peritem_net="<?php echo              $row_room['product_price_final_peritem_net'];?>"   
              data-product_price_final_peritem_fpa="<?php echo              $row_room['product_price_final_peritem_fpa'];?>"   
              data-product_price_final_peritem_total="<?php echo            $row_room['product_price_final_peritem_total'];?>" 
              data-product_price_final_all_net="<?php echo                  $row_room['product_price_final_all_net'];?>"       
              data-product_price_final_all_fpa="<?php echo                  $row_room['product_price_final_all_fpa'];?>"       

              data-product_price_final_all_total="<?php echo                $row_room['product_price_final_all_total'];?>"     
              data-product_price_ekptosi_net="<?php echo                    $row_room['product_price_ekptosi_net'];?>"         
              data-product_price_ekptosi_pososto="<?php echo                $row_room['product_price_ekptosi_pososto'];?>"     
              data-product_pricelist_item_id="<?php echo                    $row_room['product_pricelist_item_id'];?>"         
              data-product_pricelist_item_percent="<?php echo               $row_room['product_pricelist_item_percent'];?>"    
              data-product_price_coupon_use="<?php echo                     $row_room['product_price_coupon_use'];?>"          
              data-product_price_coupon_use_disabled="<?php echo            $row_room['product_price_coupon_use_disabled'];?>"              
              
              data-fpa_descr_print="<?php echo $row_room['fpa_descr_print'];?>"
              
              data-ajia_table_math="<?php echo base64_encode(trim_gks($row_room['room_ajia_table_math']));?>"
              data-ajia_table_html="<?php echo base64_encode(trim_gks($row_room['room_ajia_table_html']));?>"
              data-ajia_table_array="<?php echo base64_encode(trim_gks($row_room['room_ajia_table_array']));?>"
              data-other_taxes="<?php echo base64_encode(json_encode($other_taxes));?>"
              data-other_taxes_tooltip="<?php echo base64_encode(trim_gks($other_taxes_tooltip));?>"
              >
              <?php } ?>

      <?php
//      print '<pre>';
//      print_r(json_decode($row_room['room_ajia_table_array'] ,true));
//      print '</pre>';
      ?>

      <div class="gks_ekptosi" data-aa="<?php echo $iroom;?>"><div 
        class="gks_ekptosi_poso" data-aa="<?php echo $iroom;?>" style="<?php echo ($ekptosi_poso_html=='' ? 'display:none;' : '');?>"   ><?php echo $ekptosi_poso_html;?></div></div>
      
    </td>    
    <td class="itemtd13 mytdcm" nowrap>
      <i class="fa fa-info-circle room_info_price" data-aa="<?php echo $iroom;?>"></i>
    </td>    
    <td class="itemtd7 mytdcm" nowrap><?php 
      $roomline='';
      if ($row_room['rnum_adults']>0)
        $roomline.='<i class="fa fa-male tooltipster" style="color:#aaaaaa;" title="'.gks_lang('Ενήλικες').'"></i>'.$row_room['rnum_adults'];
      if ($row_room['rnum_childs']>0)  
        $roomline.=($roomline=='' ? '' : ' '). '<i class="fa fa-child tooltipster" style="color:#aaaaaa;font-size:80%;" title="'.gks_lang('Παιδιά').'"></i>'.$row_room['rnum_childs'];
      if ($roomline!='') $roomline.='<br>';
      if ($row_room['rnum_child_kounies']>0) 
        $roomline.='<i class="fa fa-box tooltipster" style="color:#aaaaaa;font-size:90%;" title="'.gks_lang('Βρεφικά κρεβάτια').'"></i>'.$row_room['rnum_child_kounies'];
      if ($row_room['rnum_extra_beds']>0)
        $roomline.=($roomline=='' ? '' : ' '). '<i class="fa fa-bed tooltipster" style="color:#aaaaaa;" title="'.gks_lang('Επιπλέον κρεβάτια').'"></i>'.$row_room['rnum_extra_beds'];
      echo $roomline;
      
    ?></td>    
    <td class="itemtd8 mytdcm" nowrap><?php 
      if ($row_room['ruser_id'] == -1) {
        echo gks_lang('Ίδιος πελάτης');
      } else if ($row_room['ruser_id']>0) {
        $out ='';
        if (!empty($row_room['gks_nickname'])) $out.='<a href="admin-users-item.php?id='.$row_room['ruser_id'].'">'.$row_room['gks_nickname'].'</a>, ';
        if (!empty($row_room['ruser_email'])) $out.='<a href="mailto:'.$row_room['ruser_email'].'">'.$row_room['ruser_email'].'</a>, ';
        if (!empty($row_room['ruser_mobile'])) {
          $out.='<span><a href="tel:'.$row_room['ruser_mobile'].'" class="'.$gks_voip_params['class_span'].'">'.$row_room['ruser_mobile'].'</a>';
          $out.=$gks_voip_params['html_after_span'];
          $out.='</span>, ';
        }
        $out=substr($out, 0, strlen($out)-2);
        echo $out;
      } else {
        $out ='';
        if (!empty($row_room['ruser_last_name']) or !empty($row_room['ruser_first_name'])) $out.=$row_room['ruser_last_name'].' '.$row_room['ruser_first_name'].', ';
        if (!empty($row_room['ruser_email'])) $out.='<a href="mailto:'.$row_room['ruser_email'].'">'.$row_room['ruser_email'].'</a>, ';
        if (!empty($row_room['ruser_mobile'])) {
          $out.='<span><a href="tel:'.$row_room['ruser_mobile'].'" class="'.$gks_voip_params['class_span'].'">'.$row_room['ruser_mobile'].'</a>';
          $out.=$gks_voip_params['html_after_span'];
          $out.='</span>, ';
        }
        $out=substr($out, 0, strlen($out)-2);
        echo $out;
      }
      ?>
    </td>
    <td class="itemtd9 mytdcm" nowrap><?php 
      if (isset($row_room['lang_name']) and $row['user_lang']!= $row_room['ruser_lang'] ) echo '<img src="/my/img/flags/flags_iso/32/'.strtolower($row_room['lang_ico']).'.png" title="'.$row_room['lang_name'].'">';
      if (isset($row_room['country_name']) and $row['ma_country_id']!= $row_room['ruser_ma_country_id']) echo ' <img src="/my/img/flags/flags_iso/32/'.strtolower($row_room['country_initials']).'.png" title="'.$row_room['country_name'].'">';
      ?></td>     
    <td class="itemtd10" align="left"><?php echo nl2br_gks(htmlspecialchars_gks($row_room['rsxolio'],ENT_QUOTES));?></td>
    
    
    
  </tr>
<?php    
    }
?>
</tbody>
<tfoot>
  <tr>
    <th class="table-primary mytdcm p-0 d-print-none" nowrap colspan="4" scope="row">
      <?php if ($gks_lock==false) {?>
      <i id="addroom" class="fas fa-plus-circle" style="cursor: pointer;font-size: 150%;color:green;"></i>
      <?php } ?>
    </th>
    <td class="table-primary mytdcml" nowrap align="left" colspan="2"><b><?php echo gks_lang('Σύνολα');?></b></td>
    <td class="table-primary mytdcm" nowrap ></td>    
    <td class="table-primary mytdcm" nowrap id="sum_ajia_total" colspan="2"><?php if ($sum_rooms_total<>0) echo myCurrencyFormat($sum_rooms_total);?></td>    
    <td class="table-primary mytdcm" nowrap id="sum_visitors"><?php 
      $roomline='';
      if ($sum_rooms_rnum_adults>0)
        $roomline.='<i class="fa fa-male tooltipster" style="color:#aaaaaa;" title="'.gks_lang('Ενήλικες').'"></i>'.$sum_rooms_rnum_adults;
      if ($sum_rooms_rnum_childs>0)  
        $roomline.=($roomline=='' ? '' : ' '). '<i class="fa fa-child tooltipster" style="color:#aaaaaa;font-size:80%;" title="'.gks_lang('Παιδιά').'"></i>'.$sum_rooms_rnum_childs;
      if ($roomline!='') $roomline.='<br>';
      if ($sum_rooms_rnum_child_kounies>0) 
        $roomline.='<i class="fa fa-box tooltipster" style="color:#aaaaaa;font-size:90%;" title="'.gks_lang('Βρεφικά κρεβάτια').'"></i>'.$sum_rooms_rnum_child_kounies;
      if ($sum_rooms_rnum_extra_beds>0)
        $roomline.=($roomline=='' ? '' : ' '). '<i class="fa fa-bed tooltipster" style="color:#aaaaaa;" title="'.gks_lang('Επιπλέον κρεβάτια').'"></i>'.$sum_rooms_rnum_extra_beds;
      echo $roomline;

      
    ?></td>
    <td class="table-primary" colspan="3"></td>
       
  </tr>
</tfoot>
</table>


        </div>
      </div>
    </div>
  </div>
</div>
        
        
<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Πληρωμή');?>
        </div>


        <div class="card-body" <?php echo gks_card_body('pay');?>> 


          <div class="row">

            <div class="col-lg-12 col-xl-12">
              <div style="font-size1:16pt;"><?php echo gks_lang('Τρόποι πληρωμής');?>:</div>
              <?php


                foreach ($mybasketarray['tropoi_pliromis_all'] as $row_pliromi) {
              ?>
              <div style="white-space: nowrap1;<?php echo ($row_pliromi['myisok'] ? '' : 'display:none;');?>">
                <input class="myneedsave" type="radio" name="radio_payment_way" value="<?php echo $row_pliromi['id_payment_acquirer'];?>" id="radio_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" 
                data-type="<?php echo $row_pliromi['payment_acquirer_type'];?>" data-type-o="<?php echo $row_pliromi['payment_acquirer_type_dm'];?>" 
                data-sxolio="<?php echo base64_encode($row_pliromi['payment_acquirer_sxolio']);?>"
                data-button-html="<?php echo base64_encode($row_pliromi['payment_acquirer_button_html']);?>"
                
                <?php if ($row['tropos_pliromis'] == $row_pliromi['id_payment_acquirer']) echo ' checked ';?>
                > 
                <label for="radio_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" style="cursor: pointer;" class="tooltipster delivery_payment_label" title="<?php echo $row_pliromi['payment_acquirer_tooltip'];?>"><?php echo $row_pliromi['payment_acquirer_name'];?>
                  <?php if ($row_pliromi['payment_acquirer_fees_enabled']!=0 and $row_pliromi['payment_acquirer_type']!='none') {?>
                    <span class="delivery_payment_price" id="price_payment_way_<?php echo $row_pliromi['id_payment_acquirer'];?>" ><?php echo myCurrencyFormat($row_pliromi['pa_calc_kostos'],true,true);?></span>
                  <?php } ?>
                </label>
              </div>
              <?php } ?>                              
                
                
                                
              <div id="payment_acquirer_sxolio" class="form-text text-muted" style="font-size:80%"></div>
              <div class="" style="display:none"><span id="button_html"><?php echo gks_lang('Πληρωμή τώρα');?></span></div>
            </div>
            
          </div>
          
                
          <div class="form-group row" id="div_bank_deposit_9digit" style="<?php if ($row['tropos_pliromis']!=2) echo 'display:none;';?>">
            <label for="bank_deposit_9digit" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αιτιολογία κατάθεσης');?>:</label>
            <div class="col-md-8">
              <div class="gks_flock form-control-sm" id="bank_deposit_9digit"><?php echo gks_format_bank_deposit_9digit($row['bank_deposit_9digit'])?></div>
            </div>
          </div>
          
                    
        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σημειώσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('notes');?>> 

          <div class="form-group row">
            <label for="user_notes" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο από πελάτη');?>:</label>
            <div class="col-md-8">
              <textarea id="user_notes" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_hotel_reservation_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['user_notes']);?></textarea>
            </div>
          </div> 
          <div class="form-group row">
            <label for="sxolio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο κράτησης');?>:</label>
            <div class="col-md-8">
              <textarea id="sxolio" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_hotel_reservation_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['sxolio']);?></textarea>
            </div>
          </div> 


          <div class="form-group row">
            <label for="note_logistirio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερική σημείωση για λογιστήριο');?>:</label>
            <div class="col-md-8">
              <textarea id="note_logistirio" type="text" class="form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;" <?php if (!$perm_gks_hotel_reservation_edit) echo 'readonly';?>><?php echo htmlspecialchars_gks($row['note_logistirio']);?></textarea>
            </div>
          </div> 
          
          
          
        </div>
      </div>


<?php if ($GKS_CRM_ENABLE) { ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          CRM
        </div>
        <div class="card-body" <?php echo gks_card_body('crm_channel');?>> 
          <div class="form-group row">
            <label for="crm_channel_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κανάλι πωλήσεων');?>:</label>
            <div class="col-md-8">
              <select id="crm_channel_id" class="form-control form-control-sm myneedsave" >
                <option value="0" data-contact="0" data-contact_filter="" data-campain="0" data-url="0" data-code="0" data-text="0"></option>
                <?php
                $sql_channel_sale="SELECT *
                FROM gks_crm_channel_sale
                WHERE crm_channel_sale_disabled=0
                ORDER BY crm_channel_sale_sortorder";
                $result_channel_sale = $db_link->query($sql_channel_sale);        
                if (!$result_channel_sale) {
                  debug_mail(false,'error sql',$sql_channel_sale);
                  die('sql error');
                }
                $row_channel_sale_selected=array(
                  'crm_channel_has_contact'=>0,
                  'crm_channel_has_contact_filter'=>'',
                  'crm_channel_has_campain'=>0,
                  'crm_channel_has_url'=>0,
                  'crm_channel_has_code'=>0,
                  'crm_channel_has_text'=>0,
                );
                
                while ($row_channel_sale = $result_channel_sale->fetch_assoc()) {
                  echo '<option value="'.$row_channel_sale['id_crm_channel_sale'].'" '.
                  'data-contact="'.intval($row_channel_sale['crm_channel_has_contact']).'" '.
                  'data-contact_filter="'.base64_encode(trim_gks($row_channel_sale['crm_channel_has_contact_filter'])).'" '.
                  'data-campain="'.intval($row_channel_sale['crm_channel_has_campain']).'" '.
                  'data-url="'.intval($row_channel_sale['crm_channel_has_url']).'" '.
                  'data-code="'.intval($row_channel_sale['crm_channel_has_code']).'" '.
                  'data-text="'.intval($row_channel_sale['crm_channel_has_text']).'" ';
                  if ($row_channel_sale['id_crm_channel_sale']==$row['crm_channel_id']) {
                    echo ' selected ';
                    $row_channel_sale_selected=$row_channel_sale;
                  }
                  echo '>'.$row_channel_sale['crm_channel_sale_descr'].'</option>';
                }?>
              </select>
            </div>
          </div>



          <div class="form-group row" id="crm_channel_contact_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_contact']==0) echo 'display:none;';?>">
            <label for="crm_channel_contact_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επαφή Πωλήσεων');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_contact_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['crm_channel_contact_gks_nickname']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['crm_channel_contact_id'];?>">
            </div>
          </div>


          <div class="form-group row" id="crm_channel_campain_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_campain']==0) echo 'display:none;';?>">
            <label for="crm_channel_campain_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Καμπάνια');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_campain_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['ads_campain_name']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['crm_channel_campain_id'];?>">
            </div>
          </div>

          <div class="form-group row" id="crm_channel_url_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_url']==0) echo 'display:none;';?>">
            <label for="crm_channel_url" class="col-md-4 col-form-label form-control-sm text-md-right">URL:</label>
            <div class="col-md-8">
              <input id="crm_channel_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_channel_url']);?>">
            </div>
          </div>
          <div class="form-group row" id="crm_channel_code_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_code']==0) echo 'display:none;';?>">
            <label for="crm_channel_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_channel_code']);?>">
            </div>
          </div>
          
          <div class="form-group row" id="crm_channel_text_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_text']==0) echo 'display:none;';?>">
            <label for="crm_channel_text" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="crm_channel_text" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;"><?php echo htmlspecialchars_gks($row['crm_channel_text']);?></textarea>
            </div>
          </div>   


        </div>
      </div>

<?php } ?>


    
   
    
    </div>
    
  
  
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σύνολα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('total');?>> 
            
          
          <div class="form-group row total_row" id="tr_gks_total_price_net">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Υποσύνολο');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="gks_total_price_net"    data-val="<?php echo number_format($row['gks_price_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_net']);?></div>
            </div>
          </div>
          <div class="form-group row total_row" id="tr_gks_total_price_fpa" style="<?php if ($row['gks_price_fpa']==0) echo 'display:none;'?>">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('ΦΠΑ');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="gks_total_price_fpa"    data-val="<?php echo number_format($row['gks_price_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_fpa']);?></div>
            </div>
          </div>
          <div class="form-group row total_row" id="tr_gks_total_price_netfpa" style="<?php if ($row['gks_price_netfpa']==0) echo 'display:none;'?>">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Μικτό σύνολο');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="gks_total_price_netfpa" data-val="<?php echo number_format($row['gks_price_netfpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_netfpa']);?></div>
            </div>
          </div>
     

         
          <div class="form-group row total_row" id="tr_totalWithheldAmount" style="<?php if ($row['totalWithheldAmount']==0) echo 'display:none;'?>">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Φόροι Παρακρατούμενοι');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="totalWithheldAmount" data-val="<?php echo number_format($row['totalWithheldAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalWithheldAmount']);?></div>
            </div>
          </div>             
          <div class="form-group row total_row" id="tr_totalOtherTaxesAmount" style="<?php if ($row['totalOtherTaxesAmount']==0) echo 'display:none;'?>">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Λοιποί Φόροι');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="totalOtherTaxesAmount" data-val="<?php echo number_format($row['totalOtherTaxesAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalOtherTaxesAmount']);?></div>
            </div>
          </div>             
          <div class="form-group row total_row" id="tr_totalStampDutyamount" style="<?php if ($row['totalStampDutyamount']==0) echo 'display:none;'?>">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Ψηφιακό Τέλος συναλλαγής');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="totalStampDutyamount" data-val="<?php echo number_format($row['totalStampDutyamount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalStampDutyamount']);?></div>
            </div>
          </div>              
          <div class="form-group row total_row" id="tr_totalFeesAmount" style="<?php if ($row['totalFeesAmount']==0) echo 'display:none;'?>">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Τέλη');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="totalFeesAmount" data-val="<?php echo number_format($row['totalFeesAmount'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['totalFeesAmount']);?></div>
            </div>
          </div>             
         
         
         
         
          <div class="form-group row total_row" id="tr_gks_total_price_total">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Σύνολο');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="gks_total_price_total"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($row['gks_price_total']);?></div>
            </div>
          </div>              
          <div class="form-group row total_row" id="tr_kostos_pliromis">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Κόστος πληρωμής');?>:
            </div>
            <div class="col-6">
              <input type="number" id="kostos_pliromis" class="form-control form-control-sm" value="<?php echo number_format($row['kostos_pliromis'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="text-align:left;max-width:100px;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
            </div>
          </div>
          <div class="form-group row total_row" id="tr_pliroteo">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Πληρωτέο');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="gks_pliroteo"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($pliroteo);?></div>
            </div>
          </div>
          <div class="form-group row total_row" id="tr_efd">
            <div class="col-6 table-dark1 gks_eidos_label text-right">
              <?php echo gks_lang('Φόρος Διαμονής');?>:
            </div>
            <div class="col-6">
              <div class="text-left" id="gks_efd"  style="padding-right: 6px;font-size: 0.8rem;font-weight: bold;padding-top: 8px;"><?php echo myCurrencyFormat($efd);?></div>
            </div>
          </div>
          
          
                    
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υπόλοιπο Επαφής');?>         
        </div>

        <div class="card-body" <?php echo gks_card_body('aff_bal');?>>  
          <div class="form-group row">
            <label for="balance_user_before" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προηγούμενο υπόλοιπο');?>:</label>
            <div class="col-md-8">
              <div class="form-control-sm" id="balance_user_before" 
                <?php
                $balance_user_before=gks_balance_calc(['id' => $row['user_id'], 'except_id_hotel_reservation' => $id]);
                echo ' data-val="'.number_format($balance_user_before,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','').'">'.myCurrencyFormat($balance_user_before);
                ?>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="balance_user_after" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νέο υπόλοιπο');?>:</label>
            <div class="col-md-8">
              <div class="form-control-sm" id="balance_user_after" >
                <?php
                $balance_user_after=gks_balance_calc(['id' => $row['user_id']]);
                echo myCurrencyFormat($balance_user_after);
                ?>
              </div>
            </div>
          </div>
          
          <div class="form-group row" id="div_affect_balance">
            <label for="affect_balance" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επηρεάζει το υπόλοιπο της επαφής');?>:</label>
            <div class="col-md-8">
              <input id="affect_balance" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['affect_balance']!=0) echo ' checked ';?> <?php if (!$perm_gks_hotel_reservation_edit) echo 'disabled';?>>
              <?php if (!($reservation_status=='070wait_payment' or $reservation_status=='080confirm' or $reservation_status=='100completed' or $reservation_status=='110payment')) {?>
              <small class="form-text text-muted"><?php echo gks_lang('Θα εφαρμοστεί η ρύθμιση όταν η κατάσταση της κράτησης θα είναι μία από τις παρακάτω');?>:<br>
                <span style="line-height: 1.8;">
                <span class="reservation_status_070wait_payment"><?php echo getHotelReservationStatusDescr('070wait_payment');?></span>
                <span class="reservation_status_080confirm"><?php echo getHotelReservationStatusDescr('080confirm');?></span>
                <span class="reservation_status_100completed"><?php echo getHotelReservationStatusDescr('100completed');?></span>
                <span class="reservation_status_110payment"><?php echo getHotelReservationStatusDescr('110payment');?></span>
                </span>
              </small>
              <?php } ?>
            </div>
          </div> 

          
          
          <div class="form-group row" id="div_affect_balance_all_poso" style="<?php if ($row['affect_balance']==0) echo 'display:none;';?>">
            <label for="affect_balance_all_poso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ολόκληρο το ποσό');?>:</label>
            <div class="col-md-8">
              <input id="affect_balance_all_poso" type="checkbox" class="form-control form-control-sm switchery1_sel myneedsave" value="1" <?php if ($row['affect_balance_all_poso']!=0) echo ' checked ';?> <?php if (!$perm_gks_hotel_reservation_edit) echo 'disabled';?>>
              <small class="form-text text-muted" id="small_affect_balance_all_poso" style="<?php if (!($row['affect_balance']==0 or $row['affect_balance_all_poso']!=0)) echo 'display:none;';?>">
                <input type="radio" name="affect_balance_all_poso_type" value="price_net" id="affect_balance_all_poso_type_price_net" <?php
                if ($row['affect_balance_all_poso_type']=='price_net') echo ' checked';?>>
                  <label for="affect_balance_all_poso_type_price_net"    style="margin-bottom: 0px;"><?php echo gks_lang('Υποσύνολο');?> (<span
                    id="bal_gks_total_price_net" data-val="<?php echo number_format($eidi_sum_price_net,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                    ?>"><?php echo myCurrencyFormat($eidi_sum_price_net);?></span>)</label><br>
                <input type="radio" name="affect_balance_all_poso_type" value="price_netfpa" id="affect_balance_all_poso_type_price_netfpa" <?php
                if ($row['affect_balance_all_poso_type']=='price_netfpa') echo ' checked';?>>
                  <label for="affect_balance_all_poso_type_price_netfpa" style="margin-bottom: 0px;"><?php echo gks_lang('Μικτό σύνολο');?> (<span
                    id="bal_gks_total_price_netfpa" data-val="<?php echo number_format($row['gks_price_netfpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                    ?>"><?php echo myCurrencyFormat($row['gks_price_netfpa']);?></span>)</label><br>
                <input type="radio" name="affect_balance_all_poso_type" value="price_total" id="affect_balance_all_poso_type_price_total" <?php
                if ($row['affect_balance_all_poso_type']=='price_total') echo ' checked';?>>
                  <label for="affect_balance_all_poso_type_price_total" style="margin-bottom: 0px;"><?php echo gks_lang('Σύνολο');?> (<span
                    id="bal_gks_total_price_total" data-val="<?php echo number_format($row['gks_price_total'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                    ?>"><?php echo myCurrencyFormat($row['gks_price_total']);?></span>)</label><br>
                <input type="radio" name="affect_balance_all_poso_type" value="pliroteo" id="affect_balance_all_poso_type_pliroteo" <?php
                if ($row['affect_balance_all_poso_type']=='pliroteo') echo ' checked';?>>
                  <label for="affect_balance_all_poso_type_pliroteo" style="margin-bottom: 0px;"><?php echo gks_lang('Πληρωτέο');?> (<span
                    id="bal_gks_pliroteo" data-val="<?php echo number_format($pliroteo,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');
                    ?>"><?php echo myCurrencyFormat($pliroteo);?></span>)</label>

              </small>
            </div>
          </div> 
          <div class="form-group row" id="div_affect_balance_poso"  style="<?php if ($row['affect_balance']==0 or $row['affect_balance_all_poso']!=0) echo 'display:none;';?>">
            <label for="affect_balance_poso" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ποσό');?>:</label>
            <div class="col-md-8">
              
              <input id="affect_balance_poso" type="number" class="form-control form-control-sm myneedsave" 
              value="<?php 
              $valnotzero='';
              if ($row['affect_balance_poso']!=0) {
                $valnotzero=myNumberFormatNo0($row['affect_balance_poso']);
                echo $valnotzero;
              };?>" 
              style="text-align:right;max-width: 100px;" min=0 step="<?php echo $GKS_INPUT_STEP_AJIA;?>"
              placeholder="<?php echo gks_lang('Ποσό');?>"
              <?php if (!$perm_gks_hotel_reservation_edit) echo 'disabled';?> >            
            </div>
          </div> 
          
                    
        </div>
      </div>      
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>       
    </div>
  
    
  </div>
</div>
</div> 


  
          

<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      
<?php
$GKS_RESERVATION_STATUS_BUTTONS=array(
  '005prodraft' =>        array(                        'cmdprint','010draft',),
  '010draft' =>           array('cmdupdate','cmddelete','cmdprint',           '040cancelled','050rejected','070wait_payment','080confirm',),
  '040cancelled' =>       array('cmdupdate','cmddelete','cmdprint','010draft',),
  '050rejected' =>        array('cmdupdate','cmddelete','cmdprint','010draft',),
  '070wait_payment' =>    array('cmdupdate','cmddelete','cmdprint','010draft','080confirm',),
  '080confirm' =>         array('cmdupdate','cmddelete','cmdprint','010draft','100completed'),
  '100completed' =>       array(                        'cmdprint','010draft',),
);

if (isset($GKS_RESERVATION_STATUS_BUTTONS[$reservation_status])) {

    if (in_array('cmdupdate',$GKS_RESERVATION_STATUS_BUTTONS[$reservation_status])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="submit_button_ok_custom">'.gks_lang('Αποθήκευση').'</button> ';
    if (in_array('cmddelete',$GKS_RESERVATION_STATUS_BUTTONS[$reservation_status]) and $id>0) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn btn-danger deleterowbtn" data-id="'.$id.'" data-model="gks_hotel_reservation" data-backurl="admin-hotel-reservation.php">'.gks_lang('Διαγραφή').'</button> ';
    if (in_array('010draft',$GKS_RESERVATION_STATUS_BUTTONS[$reservation_status])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_reservation_status_010draft" id="submit_button_010draft">'.gks_lang('Επαναφορά σε Προσχέδιο').'</button> ';
    if (in_array('040cancelled',$GKS_RESERVATION_STATUS_BUTTONS[$reservation_status])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_reservation_status_040cancelled" id="submit_button_040cancelled">'.getHotelReservationStatusDescr('040cancelled').'</button> ';
    if (in_array('050rejected',$GKS_RESERVATION_STATUS_BUTTONS[$reservation_status])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_reservation_status_050rejected" id="submit_button_050rejected">'.getHotelReservationStatusDescr('050rejected').'</button> ';
    if (in_array('070wait_payment',$GKS_RESERVATION_STATUS_BUTTONS[$reservation_status])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_reservation_status_070wait_payment" id="submit_button_070wait_payment">'.getHotelReservationStatusDescr('070wait_payment').'</button> ';
    if (in_array('080confirm',$GKS_RESERVATION_STATUS_BUTTONS[$reservation_status])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_reservation_status_080confirm" id="submit_button_080confirm">'.getHotelReservationStatusDescr('080confirm').'</button> ';
    if (in_array('100completed',$GKS_RESERVATION_STATUS_BUTTONS[$reservation_status])) 
      echo '<button type="button" style="margin-bottom:10px;" class="btn button_reservation_status_100completed" id="submit_button_100completed">'.getHotelReservationStatusDescr('100completed').'</button> ';
    
    
  
  
  if (in_array('cmdprint',$GKS_RESERVATION_STATUS_BUTTONS[$reservation_status])) 
    echo '<button type="button" style="margin-bottom:10px;" class="btn btn-dark" id="submit_button_print">'.gks_lang('Εκτύπωση').' <i class="fas fa-print" style="color: #35dc35;font-size: 120%;"></i></button> ';
  
}
?>      
      
      <div style="display:inline-block;width:38px;height:38px;vertical-align:top;">
        <div style="border:1px solid gray;padding: 7px 0px 5px 0px;;border-radius:4px;background-color:#343a40;display:none;" id="calc_hourglass">
          <i class="fas fa-hourglass-half" style="color:coral;font-size:120%;"></i>
        </div> 
      </div>      
    </div> 
  </div> 
</div> 
      
<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">

      <?php 
      echo getObjectRels('gks_hotel_reservation',$id);
      echo getActivityObjectTable('gks_hotel_reservation',$id);
      ?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Μηνύματα');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="message_item_add"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('message');?>>
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
            <thead>
              <tr>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;">#</th>
                <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
                <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>                
                <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Μήνυμα');?></th>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;"><i class="fas fa-envelope" style="color: #35dc35;font-size: 120%;"></i></th>
              </tr>
            </thead>  
            <tbody id="item_messages_body"> 
              
            <?php
            $sql_msg="SELECT gks_hotel_reservation_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_hotel_reservation_messages LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_hotel_reservation_messages.hotel_reservation_id=".$id."
            ORDER BY gks_hotel_reservation_messages.mydate_add DESC, gks_hotel_reservation_messages.id_hotel_reservation_message DESC;";
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg['id_hotel_reservation_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php  
                if (!empty($row_msg['woo_author'])) echo $row_msg['woo_author'];
                else echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_msg['hotel_reservation_message']);
                ?></div></div></td>    
              <td class="mytdcm"><?php 
                if ($row_msg['email_id']!=0) {
                  echo '<i class="fas fa-envelope gks_email_view" data-id="'.$row_msg['email_id'].'"></i>';
                }
                if ($row_msg['sms_id']!=0) {
                  echo '<i class="fas fa-sms gks_sms_view" data-id="'.$row_msg['sms_id'].'"></i>';
                }                
                ?></td>
            </tr>
            <?php } ?>                      
            </tbody>   
          </table>                
        </div>
      </div>
            
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σύνδεσμοι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('links');?>><?php

          
          
          $query = "SELECT gks_hotel_reservation_links.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_hotel_reservation_links LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation_links.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_hotel_reservation_links.hotel_reservation_id in (".$id.")
          ORDER BY gks_hotel_reservation_links.mydate, gks_hotel_reservation_links.id_hotel_reservation_links;";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="links_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Χρήστης');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Προσθήκη');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Σύνδεσμος');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Μέγεθος');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          $need_download_timer=0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr id="tr_links_url_<?php echo $row_list['id_hotel_reservation_links'];?>">
              <th scope="row" nowrap align="right" class="links_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_list['id_hotel_reservation_links'];?>" data-deleteafter="gks_fnc_links_delete_after|<?php echo $row_list['id_hotel_reservation_links'];?>" data-model="gks_hotel_reservation_links" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>

              </td>
              <td nowrap><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.$row_list['gks_nickname'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate']), 'd/m/Y H:i', 1);?></td>   
              <td       style="word-break: break-all;">
                <div><?php 
                $temp=trim_gks($row_list['url']);
                if ($temp!='' and startwith($temp,'http')) {
                  $temp='<a href="'.$temp.'" target="_blank">'.(strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp).'</a>';
                  echo $temp;
                } else {
                  echo (strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp);
                }
                ?></div>
                <div class="progress download-perc" data-id="<?php echo $row_list['id_hotel_reservation_links'];?>" 
                  style="<?php echo ($row_list['download_status']==1 ? '' : 'display:none;');?>">
                  <div class="download-perc-bar progress-bar progress-bar-striped" 
                    data-id="<?php echo $row_list['id_hotel_reservation_links'];?>" role="progressbar" 
                    style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>    
                <div class="download-message" 
                  data-id="<?php echo $row_list['id_hotel_reservation_links'];?>" 
                  style="<?php echo ($row_list['download_status']==3 ? '' : 'display:none;');?>"
                  ><?php echo $row_list['download_message'];?></div>
                
              </td>
              <td nowrap class="download_size_until_now" data-id="<?php echo $row_list['id_hotel_reservation_links'];?>" style="text-align:right;vertical-align:middle;"><?php if ($row_list['download_size_until_now']>0) echo number_format($row_list['download_size_until_now']/1024/1024,2,$GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB';?></td>  
              <td nowrap class="download_file_td" data-id="<?php echo $row_list['id_hotel_reservation_links'];?>" style="text-align:center;vertical-align: middle;"><?php
              
              
              // 0 notdownload
              // 1 downloding
              // 2 complete
              // 3 abort
              
              if ($row_list['download_status']==0) { //notdownload
                echo '<i class="fas fa-file-download download_action_start" data-id="'.$row_list['id_hotel_reservation_links'].'" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==1) { //downloding
                $need_download_timer=1;
                echo '<i class="fas fa-stop-circle download_action_stop" data-id="'.$row_list['id_hotel_reservation_links'].'" style="font-size:200%;vertical-align:middle;color:red;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==2) { //complete
                echo '<i class="fas fa-check-circle download_action_complete" data-id="'.$row_list['id_hotel_reservation_links'].'" style="font-size:200%;vertical-align:middle;color:green;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==3) { //abort
                echo '<i class="fas fa-undo download_action_reset" data-id="'.$row_list['id_hotel_reservation_links'].'" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>';
              } 
                
              ?></td>  
            </tr>
          <?php } ?>


            <tr class="" id="tr_new_links_url">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="links_url"    id="links_url"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('π.χ.');?> https://we.tl/...">
              </td>  
            </tr>
            <tr class="" id="tr_new_links_url_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_links_url"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>                       
        </div>
      </div>
              



			<?php
			$obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_hotel_reservation','id'=>$id));
      echo $obj_fileslist['html'];
      ?>


        
      
            
    </div>
    <div class="col-xl-6">
        

      <?php 
      
      if (trim_gks($row['print_date'])!='' or 
          trim_gks($row['print_file_name']) != '' or 
          trim_gks($row['print_file_url']) != '' or 
          $row['print_user_id']>0 or 
          trim_gks($row['print_reservation_status']) != '') {?>
      
      <div class="card gks_card_expand" id="gks_print">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εκτύπωση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('print');?>>       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['print_date'])) echo showDate(strtotime($row['print_date']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εκτύπωση από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['print_user_id'].'">'.$row['gks_nickname_print'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση όταν έγινε η εκτύπωση');?>:</label>
            <div class="col-sm-8"><span class="acc_inv_state_<?php echo $row['print_reservation_status'];?>"><?php echo getHotelReservationStatusDescr($row['print_reservation_status']);?></span></div>
          </div>

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αρχείο');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (trim_gks($row['print_file_name'])!='') {
                $local_file=GKS_FileServerShare.'hotel/reservation/'.$id.'/print/'.$row['print_file_name'];
                if (file_exists($local_file)) {
                  //print_file_url
                  $url_file='admin-get-file.php?fs=fileservers&file=hotel%2Freservation%2F'.$id.'%2Fprint%2F'.urlencode($row['print_file_name']);
                  echo '<a href="'.$url_file.'" target="_blank" id="last_print_file">'.$row['print_file_name'].'</a> ';
                  echo '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
                }
              }
              ?></span></div>
          </div>

        </div>      
      </div>              
      <?php } ?>
              
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιστορικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('hist');?>>
        <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th class="table-dark" scope="col" width="0%" nowrap>#</th>
              <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
              <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>
              <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Τι');?></th>
            </tr>
          </thead>  
          <tbody> 
            
          <?php
          $sql_log="SELECT gks_hotel_reservation_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_hotel_reservation_log LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_hotel_reservation_log.hotel_reservation_id=".$id."
          ORDER BY gks_hotel_reservation_log.id_hotel_reservation_log DESC;";
          $result_log = $db_link->query($sql_log);        
          if (!$result_log) debug_mail(false,'error sql',$sql_log);
          if (!$result_log) die('sql error');
          
          $j = 0;
          while ($row_log = $result_log->fetch_assoc()) {
            $j++; ?>
        
          <tr>
            <th scope="row" align="center"><?php echo $j;?></th>
            <td align="left"><?php echo showDate(strtotime($row_log['add_date']), 'd/m/Y H:i:s', 1);?></td>  
            <td align="left"><?php echo $row_log['gks_nickname'];?></td>  
            <td align="left"><?php echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_log['sxolio']);?></td>    
          </tr>
          <?php } ?>                      
          </tbody>   
          </table>
        </div>                                   
      </div>
              
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body"  <?php echo gks_card_body('kat');?>>  


          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['id_hotel_reservation'];?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right">GUID:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['reservation_guid'];?></span></div>
          </div>          
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['gks_nickname_add'])) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['gks_nickname_edit'])) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['myip'])) echo '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>';?></span></div>
          </div>


        </div>      
      </div>      
    
             
        
           
    </div>
  </div>      
</div>
    

<div id="dialog_room" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <h3 align="center" style="padding-top:0px;"><?php echo gks_lang('Δωμάτιο');?></h3>
  <div class="container-fluid" id="dialog_room_area">
    <div class="row">
      <div class="col-md-6">
        <div class="container-fluid gksdataarea" style="width:96%;background-color: white;">
          
          <div class="form-group row">
            <label for="dialog_room_room_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Δωμάτιο');?>:</label>
            <div class="col-md-8">
              <input type="text"   value="" name="dialog_room_room_descr"    id="dialog_room_room_descr"   class="form-control" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <input type="hidden" value=""   name="dialog_room_room_id" id="dialog_room_room_id">
              <span id="dialog_room_room_id_result"></span> 
            </div>
          </div>           
          <div class="form-group row">
            <label for="dialog_room_room_type_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-md-8">
              <input id="dialog_room_room_type_descr" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" disabled>
            </div>
          </div>
          <div class="form-group row">
            <label  class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επισκέπτες');?>:</label>
            <div class="col-md-8" id="dialog_room_room_type_visitors_html">
              
            </div>
          </div>
          
          <div class="form-group row">
            <label for="dialog_room_num_adults" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενήλικες');?>:</label>
            <div class="col-md-8">
              <input id="dialog_room_num_adults" type="number" class="form-control form-control-sm" value="" min="0" max="1000" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="dialog_room_num_childs" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Παιδιά');?>:</label>
            <div class="col-md-8">
              <input id="dialog_room_num_childs" type="number" class="form-control form-control-sm" value="" min="0" max="1000" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div id="dialog_room_rchilds_ages_list_main_div">
            
          </div>
          <div class="form-group row" id="div_dialog_room_num_child_kounies">
            <label for="dialog_room_num_child_kounies" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Βρεφικά κρεβάτια');?>:</label>
            <div class="col-md-8">
              <select id="dialog_room_num_child_kounies" class="form-control form-control-sm" style="width:unset;">
              <?php for ($cc=0;$cc<=9;$cc++) echo '<option value="'.$cc.'">'.$cc.'</option>';?>
              </select>
            </div>
          </div>
          <div class="form-group row" id="div_dialog_room_num_extra_beds">
            <label for="dialog_room_num_extra_beds" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επιπλέον κρεβάτια');?>:</label>
            <div class="col-md-8">
              <select id="dialog_room_num_extra_beds" class="form-control form-control-sm" style="width:unset;">
              <?php for ($cc=0;$cc<=9;$cc++) echo '<option value="'.$cc.'">'.$cc.'</option>';?>
              </select>
            </div>
          </div>
          
          
          <div class="form-group row align-items-center">
            <label for="dialog_room_ekptosi_pososto" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έκπτωση');?>:</label>
            <div class="col-md-4 align-items-center">
            </div>
            <div class="col-md-4">
              <input id="dialog_room_ekptosi_pososto" type="number" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="text-align:right;width:calc(100% - 30px);display: inline;" min="0" step="<?php echo $GKS_INPUT_STEP_POSOSTO;?>"> %
            </div>
          </div>
          
          <div class="form-group row align-items-center">
            <label for="dialog_room_price_per_item" class="col-md-4 col-form-label form-control-sm text-md-right"><span title="<?php echo gks_lang('Τιμή ανά Διανυκτέρευση');?>" class="tooltipster"><?php echo gks_lang('Τιμή/Διανυκτ');?></span>:</label>
            <div class="col-md-4 align-items-center">
            </div>
            <div class="col-md-4">
              <input id="dialog_room_price_per_item" type="number" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="text-align:right;width:calc(100% - 30px);display: inline;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>"> &euro;
            </div>
          </div>
          

          <div class="form-group row align-items-center1">
            <label for="dialog_room_ajia_total" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αξία για όλες τις ημέρες');?>:</label>
            <div class="col-md-4 align-items-center">
              <div id="dialog_room_ajia_math" style="text-align:center"></div>
              <div id="dialog_room_ajia_table" style="text-align:center;color:blue;text-decoration: underline;cursor:pointer"><?php echo gks_lang('Ανάλυση ανά μέρα');?></div>
            </div>
            
            <div class="col-md-4">
              <div>
                <input id="dialog_room_ajia_total" type="number" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="text-align:right;width:calc(100% - 30px);display: inline;" min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>"> &euro;
              </div>
              <div class="gks_ekptosi" data-aa="-1"><div 
                class="gks_ekptosi_poso" data-aa="-1" style="display:none;" id="dialog_room_gks_ekptosi_poso"></div></div>
            </div>
          </div> 
          


  
          <div class="form-group row" style="margin-bottom:10px;">
            <label for="dialog_room_sxolio" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="dialog_room_sxolio" type="text" class="form-control form-control-sm" style="height:78px;"></textarea>
            </div>
          </div>                  
           
        </div>  
      </div> 
      <div class="col-md-6">
        <div class="container-fluid gksdataarea" style="width:96%;background-color: white;">
          
          <div class="form-group row">
            <label for="dialog_room_gks_nickname" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πελάτης');?>:</label>
            <div class="col-md-8"  style="text-align1:center">
              <input class="form-check-input111" type="radio" name="selecttype" id="selecttype0" value="0">
              <label class="form-check-label" for="selecttype0"><?php echo gks_lang('Ίδιος πελάτης');?></label>
              <br>
              <input class="form-check-input111" type="radio" name="selecttype" id="selecttype1" value="1">
              <label class="form-check-label" for="selecttype1"><?php echo gks_lang('Άλλος πελάτης');?></label>              
            </div>
          </div>
                    
          <div class="form-group row selecttypediv">
            <label for="dialog_room_gks_nickname" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Υπάρχον Πελάτης');?>:</label>
            <div class="col-md-8">
              <input type="text"   value="" name="dialog_room_gks_nickname"    id="dialog_room_gks_nickname"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <input type="hidden" value=""   name="dialog_room_user_id" id="dialog_room_user_id" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
 
          <div class="form-group row selecttypediv">
            <label for="dialog_room_user_first_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-md-8">
              <input id="dialog_room_user_first_name" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row selecttypediv">
            <label for="dialog_room_user_last_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επίθετο');?>:</label>
            <div class="col-md-8">
              <input id="dialog_room_user_last_name" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row selecttypediv">
            <label for="dialog_room_user_email" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ηλ. διεύθυνση');?>:</label>
            <div class="col-md-8">
              <input id="dialog_room_user_email" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row selecttypediv">
            <label for="dialog_room_user_mobile" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="dialog_room_user_mobile" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          
                
          <div class="selecttypediv">

            <div class="form-group row" id="dialog_room_div_customer_more_show">
              <div class="col-md-8 offset-sm-4">
                <span id="dialog_room_customer_more_show" style="color:#007bff;text-decoration: underline;cursor:pointer;"><?php echo gks_lang('Περισσότερα');?>..</span>
              </div>
            </div> 
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_lang" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γλώσσα');?>:</label>
              <div class="col-md-8">
                <select id="dialog_room_user_lang" class="dialog_room_pelatistype form-control form-control-sm">
                  <option value=""></option>
                  <?php
                  $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
                  gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
                  $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
                  FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
                  ".$lang_prepare_gks_lang['sql']['from2']."
                  ORDER BY lang_sortorder,lang_name";                  
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<option value="'.$row_select['id_lang'].'" ';
                    echo '>'.$row_select['lang_name'].'</option>';
                  }?>
                </select>    
              </div>
            </div>  
                      
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_ma_odos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οδός');?>:</label>
              <div class="col-md-8">
                <input id="dialog_room_user_ma_odos" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                <small class="form-text text-muted auto_googlemaps" id="dialog_room_user_ma_odos_auto_googlemaps"></small>
              </div>
            </div>
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_ma_arithmos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
              <div class="col-md-8">
                <input id="dialog_room_user_ma_arithmos" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>
            
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_ma_orofos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
              <div class="col-md-8">
                <input id="dialog_room_user_ma_orofos" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>            
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_ma_perioxi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιοχή');?>:</label>
              <div class="col-md-8">
                <input id="dialog_room_user_ma_perioxi" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_ma_poli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πόλη');?>:</label>
              <div class="col-md-8">
                <input id="dialog_room_user_ma_poli" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_ma_tk" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΤΚ');?>:</label>
              <div class="col-md-8">
                <input id="dialog_room_user_ma_tk" type="text" class="dialog_room_pelatistype form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              </div>
            </div>
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_ma_nomos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νομός');?>:</label>
              <div class="col-md-8">
                <select id="dialog_room_user_ma_nomos_id" class="dialog_room_pelatistype form-control form-control-sm">
                  <option value="0"></option>
                </select>    
              </div>
            </div>            
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_ma_country_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χώρα');?>:</label>
              <div class="col-md-8">
                <select id="dialog_room_user_ma_country_id" class="dialog_room_pelatistype form-control form-control-sm">
                </select>    
              </div>
            </div> 
            
            



            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_fiscal_position_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φορολογική Θέση');?>:</label>
              <div class="col-md-8">
                <select id="dialog_room_user_fiscal_position_id" class="dialog_room_pelatistype form-control form-control-sm">
                  <option value="0"></option>
                  <?php
                  $lang_prepare_gks_eshop_fiscal_position=gks_lang_data_obj_prepare('gks_eshop_fiscal_position','default');
                  gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_fiscal_position, array('fiscal_position_descr'));
                  $sql="select id_fiscal_position,".gks_lang_sql_field('fiscal_position_descr',$lang_prepare_gks_eshop_fiscal_position)." 
                  FROM ".$lang_prepare_gks_eshop_fiscal_position['sql']['from1']." gks_eshop_fiscal_position 
                  ".$lang_prepare_gks_eshop_fiscal_position['sql']['from2']."
                  where fiscal_position_disable=0 
                  order by fiscal_position_sortorder,fiscal_position_descr";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<option value="'.$row_select['id_fiscal_position'].'" ';
                    echo '>'.$row_select['fiscal_position_descr'].'</option>';
                  }?>
                </select>    
              </div>
            </div> 
            <div class="form-group row dialog_room_divs_customer_more">
              <label for="dialog_room_user_pricelist_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμοκατάλογος');?>:</label>
              <div class="col-md-8">
                <select id="dialog_room_user_pricelist_id" class="dialog_room_pelatistype form-control form-control-sm">
                  <option value="0"></option>
                  <?php
                  $lang_prepare_gks_eshop_pricelist=gks_lang_data_obj_prepare('gks_eshop_pricelist','default');
                  gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_pricelist, array('pricelist_descr'));
                  $sql="select id_pricelist,".gks_lang_sql_field('pricelist_descr',$lang_prepare_gks_eshop_pricelist)." 
                  FROM ".$lang_prepare_gks_eshop_pricelist['sql']['from1']." gks_eshop_pricelist 
                  ".$lang_prepare_gks_eshop_pricelist['sql']['from2']."
                  where pricelist_disable=0 
                  order by sortorder,pricelist_descr";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die('sql error');
                  }
                  while ($row_select = $result_select->fetch_assoc()) {
                    echo '<option value="'.$row_select['id_pricelist'].'" ';
                    echo '>'.$row_select['pricelist_descr'].'</option>';
                  }?>
                </select>    
              </div>
            </div> 


            
            

                   
            <div class="form-group row" id="dialog_room_div_customer_more_hide">
              <div class="col-md-8 offset-sm-4">
                <span id="dialog_room_customer_more_hide" style="color:#007bff;text-decoration: underline;cursor:pointer;"><?php echo gks_lang('Λιγότερα');?>..</span>
              </div>
            </div>           
          </div>
        </div> 
      </div> 

    </div>  
  </div>  
  
</div>



<div id="dialog_gsis" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?></div>
    </div>
    
    <div class="form-group row">  
      <label for="dialog_gsis_afm" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
      <div class="col-sm-4">
         <input id="dialog_gsis_afm" type="text" class="form-control form-control-sm" value="" >
      </div>
      <div class="col-sm-4">
         <button style="" id="dialog_gsis_run" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση');?></button>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_gsis_html">
        
      </div>
    </div>
    
  </div>
</div>

<div id="dialog_user_save" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Προσθήκη ή επιλογή επαφής');?></div>
    </div>
    <div class="form-group row">  
      <div style="font-size: 100%;text-align:center;width: 100%;">
        <?php echo gks_lang('Βρέθηκαν οι παρακάτω επαφές στο σύστημα');?>
        <?php echo gks_lang('Η αναζήτηση έγινε με βάση το σχετικό πεδίο που αναφέρεται στην στήλη <b>Αναζήτηση</b>.');?>
        <?php echo gks_lang('Μήπως η επαφή που θέλετε να προσθέσετε είναι μία από τις παρακάτω;');?>
        <?php echo gks_lang('Εάν <b>ναι</b>, τότε επιλέξτε την.');?>
        <?php echo gks_lang('Εάν <b>όχι</b>, τότε μπορείτε να προσθέσετε την νέα επαφή επιλέγοντας την επιλογή <b>Προσθήκη νέας επαφής</b>');?>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" style="text-align: center !important;">
        <input type="radio" name="dialog_user_save_radio" id="dialog_user_save_radio_new" value="-1">  <label class="gks_label" for="dialog_user_save_radio_new"><?php echo gks_lang('Προσθήκη νέας επαφής');?>:</label>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_user_save_html">
        
      </div>
    </div>
  
  </div>
</div>


<?php include_once 'admin-obj-send-message.php'; ?>

<div id="dialog_print" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group1 row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Ρυθμίσεις Εκτύπωσης');?></div>
    </div>
        
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Τύπος');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_pdf"  value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_pdf" style="display:inline;padding-right:18px;cursor: pointer;">pdf
          <i class="fas fa-file-pdf tooltipster" title="pdf" style="color:#fa0f00;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_html" value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_html" style="display:inline;padding-right:18px;cursor: pointer;">html
          <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_jpg" value="3" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_jpg" style="display:inline;padding-right:18px;cursor: pointer;">jpg
          <img src="img/jpg21.png" class="tooltipster" title="jpg" style="height:15px;vertical-align: top;"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Προσανατολισμός');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Κατακόρυφος');?>
          <i class="fas fa-portrait tooltipster" title="Portrait" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Οριζόντιος');?>
          <i class="fas fa-image tooltipster" title="Landscape" style="color:#4e4e4e;font-size:120%"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Χρώμα ή Γκρι');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Με χρώμα');?>
          <img src="img/palette-color.png" border="0" width="15" style="vertical-align: top;">
          </label>
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Γκρι');?>
          <img src="img/palette-gray.png" border="0" width="15" style="vertical-align: top;">
          </label>
      </div>
    </div>    

    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Μεγέθυνση');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left">
        <div id="dialog_print_zoom_slider" style="padding-top: 18px;width: calc(100% - 50px);    margin-left: 25px;">
          <div id="dialog_print_zoom_slider_handle" class="ui-slider-handle"></div>
        </div>
      </div>
    </div>

    
    <div class="row" >
      <div class="gks_print_thump_container">
<?php
  $user_def_form_id=0;
  if (isset($gks_user_settings['print']['form_id_reservation'])) $user_def_form_id=intval($gks_user_settings['print']['form_id_reservation']);
  
  $sql_print_forms="SELECT gks_print_forms.*, gks_lang.lang_name
  FROM ((gks_print_objects 
  LEFT JOIN gks_print_objects_forms ON gks_print_objects.id_print_object = gks_print_objects_forms.print_object_id) 
  LEFT JOIN gks_print_forms ON gks_print_objects_forms.print_form_id = gks_print_forms.id_print_form)
  LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  WHERE gks_print_forms.id_print_form is not null and gks_print_forms.is_disable=0 AND gks_print_objects.object_name='gks_hotel_reservation'
  ".(count($perm_id_print_forms)>0 ? " and gks_print_forms.id_print_form in (".implode(',',$perm_id_print_forms).")" : '')."
  order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";

  $perm_print_forms=array();
  
  $result_print_forms = $db_link->query($sql_print_forms);        
  if (!$result_print_forms) {debug_mail(false,'error sql',$sql_print_forms);die('sql error');}
  while ($row_print_forms = $result_print_forms->fetch_assoc()) {
    //print $row_print_forms['id_print_form'].' '.$row_print_forms['file_thump_url'].'<br>';
    
    $print_form_descr=trim_gks($row_print_forms['print_form_descr']);
    $print_lang_name=trim_gks($row_print_forms['lang_name']);
    $file_thump_url=trim_gks($row_print_forms['file_thump_url']);
    if ($file_thump_url=='') $file_thump_url='img/print_form_empty.png';
    
    $perm_company_ids=trim_gks($row_print_forms['perm_company_ids']);
    $perm_acc_journal_ids=trim_gks($row_print_forms['perm_acc_journal_ids']);
    $perm_acc_seires_ids=trim_gks($row_print_forms['perm_acc_seires_ids']);

    $temp=array('id'=>intval($row_print_forms['id_print_form']));
    if ($perm_company_ids!='') $temp['perm_company_ids']=unserialize($perm_company_ids);
    if ($perm_acc_journal_ids!='') $temp['perm_acc_journal_ids']=unserialize($perm_acc_journal_ids);
    if ($perm_acc_seires_ids!='') $temp['perm_acc_seires_ids']=unserialize($perm_acc_seires_ids);
    $perm_print_forms[]=$temp;
    
    $div_form='<div class="gks_print_thump_div '.
      ($user_def_form_id==$row_print_forms['id_print_form'] ? 'gks_print_thump_div_selected' : '').
      '" data-form_id="'.$row_print_forms['id_print_form'].'" '.
      'data-lang="'.$row_print_forms['gks_lang'].'" '.
      'data-file_type="'.$row_print_forms['file_type'].'" '.
      'data-landscape="'.$row_print_forms['is_landscape'].'" '.
      'data-grayscale="'.$row_print_forms['grayscale'].'" '.
      'data-zoom="'.intval($row_print_forms['zoom']*100).'" '.
      '>';
      $div_form.='<div class="gks_print_thump_title">'.$print_form_descr.'</div>';
      $div_form.='<div class="gks_print_thump_lang">'.$print_lang_name.'</div>';
      $div_form.='<img src="'.$file_thump_url.'" class="gks_print_thump_img" border="0"/>';
      
    
    $div_form.='</div>';
    echo $div_form;
  }
  
  $div_form='<div id="gks_print_thump_more_div">';
    $div_form.='<div id="gks_print_thump_more_text"><i class="fas fa-plus-circle" style="font-size:200%;color:#35dc35;"></i><br>'.gks_lang('Εμφάνιση όλων').'</div>';
  $div_form.='</div>';
  echo $div_form;
  

?>      
      </div>
    </div>
<?php
  $erp_app_id=0;
  if ($reservation_seira_id>0) {
    $sql_send_erp_app="SELECT gks_acc_seires.id_acc_seira, gks_acc_seires.erp_app_id, gks_acc_seires.erp_app_dest,  
    gks_acc_seires.erp_app_dest_printer, 
    gks_acc_seires.erp_app_dest_printer_method,
    gks_acc_seires.erp_app_dest_printer_lpr_ip,
    gks_acc_seires.erp_app_dest_printer_copies, 
    gks_acc_seires.erp_app_dest_folder, 
    gks_erp_app.id_erp_app, gks_erp_app.erp_app_name, gks_erp_app.erp_app_last_ping
    FROM gks_acc_seires 
    LEFT JOIN gks_erp_app ON gks_acc_seires.erp_app_id = gks_erp_app.id_erp_app
    where gks_acc_seires.id_acc_seira=".$reservation_seira_id;
    
    $result_send_erp_app = $db_link->query($sql_send_erp_app);        
    if (!$result_send_erp_app) {debug_mail(false,'error sql',$sql_send_erp_app);die('sql error');}
    if ($result_send_erp_app->num_rows==1) {
      $row_send_erp_app = $result_send_erp_app->fetch_assoc();
      $erp_app_id=$row_send_erp_app['erp_app_id'];
      

      $send_erp_app_tooltip='';
      $send_erp_app_tooltip.='gks ERP App Desktop: '.trim_gks($row_send_erp_app['erp_app_name']).'<br>';
      if ($row_send_erp_app['erp_app_dest']=='printer') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Εκτυπωτής').'<br>';
        $send_erp_app_tooltip.=gks_lang('Μέθοδος').': '.erp_app_dest_printer_method_descr($row_send_erp_app['erp_app_dest_printer_method']).'<br>';
        if (in_array($row_send_erp_app['erp_app_dest_printer_method'],[0,1])) $send_erp_app_tooltip.=gks_lang('Εκτυπωτής').': '.trim_gks($row_send_erp_app['erp_app_dest_printer']).'<br>';
        if (in_array($row_send_erp_app['erp_app_dest_printer_method'],[2]))   $send_erp_app_tooltip.=gks_lang('IP εκτυπωτή').': '.trim_gks($row_send_erp_app['erp_app_dest_printer_lpr_ip']).'<br>';
        $send_erp_app_tooltip.=gks_lang('Αντίτυπα').': '.trim_gks($row_send_erp_app['erp_app_dest_printer_copies']);
        
      } else if ($row_send_erp_app['erp_app_dest']=='folder') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Φάκελος').'<br>';
        $send_erp_app_tooltip.=gks_lang('Φάκελος').': '.trim_gks($row_send_erp_app['erp_app_dest_folder']);
      }     
      $send_erp_app_checkbox_disable=true;
      if (isset($row_send_erp_app['erp_app_last_ping'])) {
        if (strtotime($row_send_erp_app['erp_app_last_ping']) > time()-15*60) {
          $send_erp_app_tooltip.= '<br>'.gks_lang('Τελευταία σύνδεση εφαρμογής').':<br><span class=gks_erp_app_alive>'.secondsago(strtotime($row_send_erp_app['erp_app_last_ping'])).'</span>';
          $send_erp_app_checkbox_disable=false;
        } else {
          $send_erp_app_tooltip.= '<br>'.gks_lang('Τελευταία σύνδεση εφαρμογής').':<br><span class=gks_erp_app_not_alive>'.secondsago(strtotime($row_send_erp_app['erp_app_last_ping'])).'</span>';
        }
      }
      
    }
  }
  if ($erp_app_id>0) {
?>    
    <div class="row">  
      <div class="col-sm-12 form-control-sm text-sm-left">
        <input id="gks_print_send_gks_erp_app" type="checkbox" class="form-control form-control-sm switchery1_sel" value="1" <?php if ($send_erp_app_checkbox_disable) echo 'disabled'; else echo 'checked';?>>
        <label for="gks_print_send_gks_erp_app" style="margin: 0px;position: relative;top: 2px;font-size: 0.8rem;"> <?php echo gks_lang('Αποστολή στην εφαρμογή gks ERP App Desktop');?></label>
        <i class="fas fa-info-circle tooltipster" title="<?php echo $send_erp_app_tooltip;?>" style="font-size: 150%;position: relative;top: 4px;"></i>
      </div>
    </div>    
<?php } ?>    

  </div>  
</div>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_hotel_reservation';
var from_php_activity_model='gks_hotel_reservation';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');


var from_php_id=<?php echo $id;?>;
var from_php_template_id=<?php echo $template_id;?>;
var from_php_gks_lock=<?php echo ($gks_lock ? 'true' : 'false');?>;
var from_php_number_gks_lock=<?php echo ($gks_number_lock ? 'true' : 'false');?>;
var from_php_user_gks_lock=<?php echo ($gks_user_lock ? 'true' : 'false');?>;


var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;

var from_php_need_download_timer='<?php echo $need_download_timer;?>';

    
var from_php_payment_way_default=<?php echo $GKS_ORDER_DEFAULT_PAYMENT_HOTEL;?>;


var kostos_apostolis_mode='<?php if ($id>0) echo "manual";?>';
var kostos_pliromis_mode='<?php if ($id>0) echo "manual";?>';

 
var coupons_array = JSON.parse('<?php echo json_encode($mybasketarray['coupons']);?>');
//console.log(child_age_price_array);



<?php
$child_age_price_array_out=array();
$hotel_child_accept_max_age_out=array();
$child_age_price_ap_array_out=array();
foreach ($user_hotels as $hotel_item) {
  $hotel_params_temp=gks_hotel_get_params($hotel_item['id']);


  $hotel_child_accept_max_age_out[$hotel_item['id']] = $hotel_params_temp['hotel_child_accept_max_age'];
  $child_age_price_array_out[$hotel_item['id']] = $hotel_params_temp['hotel_child_age_price'];
  
  $child_age_price_ap_array=array();
  for($ia=0; $ia<=$hotel_params_temp['hotel_child_accept_max_age']; $ia++) {
    if ($ia < $hotel_params_temp['hotel_child_accept_above_age']) {
      $child_age_price_ap_array[$ia]='';
    } else {
      $foundprice=gks_lang('ως ενήλικας');
      foreach ($hotel_params_temp['hotel_child_age_price'] as $valia) {
        if ($ia >= $valia['from'] and $ia <= $valia['to']) {
          if ($valia['price']==0) $foundprice=gks_lang('Δωρεάν');
          else {
            $foundprice=myCurrencyFormat($valia['price']);
            if ($valia['type']=='night') $foundprice.= ' / '.gks_lang('Βράδυ');
            else if ($valia['type']=='stay') $foundprice.= ' / '.gks_lang('Κράτηση');
          }
          break;
        }
      } 
      $child_age_price_ap_array[$ia] = $ia.' '.gks_lang('ετών').' ('.$foundprice.')';
    }
  }
  $child_age_price_ap_array_out[$hotel_item['id']]=$child_age_price_ap_array;         
}
?>
var child_age_price_array = JSON.parse('<?php echo json_encode($child_age_price_array_out);?>');
var from_php_GKS_HOTEL_CHILD_ACCEPT_MAX_AGE = JSON.parse('<?php echo json_encode($hotel_child_accept_max_age_out);?>');
var child_age_price_ap_array = JSON.parse('<?php echo json_encode($child_age_price_ap_array_out);?>');
//console.log(child_age_price_ap_array);

var fields_change=[];
<?php 
foreach ($fields_change as $field_aa => $field_name) {

  echo "fields_change[".$field_aa."]='".$field_name."';";
} ?>
//console.log(fields_change);

var from_php_defs_inh=<?php echo $defs['inh'];?>;
var from_php_defs_outh=<?php echo $defs['outh'];?>;


var dialog_room;  
var json_rooms_list=[];

<?php 
foreach ($json_rooms_list as $value) {
  echo 'json_rooms_list.push('.json_encode($value).');'."\r\n";
}?>
//console.log(json_rooms_list);




var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_reservation','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_reservation','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hotel_reservation','delete',$id);?>;

var from_php_reservation_status='<?php echo $reservation_status;?>';
var from_php_eidos_parastatikou_balance_pros=1;


var from_php_print_def_file_type='<?php echo (isset($gks_user_settings['print']['file_type']) ? $gks_user_settings['print']['file_type'] : 'pdf');?>';
var from_php_print_def_grayscale=<?php echo (isset($gks_user_settings['print']['grayscale']) ? $gks_user_settings['print']['grayscale'] : 'false');?>;
var from_php_print_def_landscape=<?php echo (isset($gks_user_settings['print']['landscape']) ? $gks_user_settings['print']['landscape'] : 'false');?>;
var from_php_print_def_zoom=<?php echo (isset($gks_user_settings['print']['zoom']) ? $gks_user_settings['print']['zoom'] : '100');?>;;
var from_php_print_def_form_id=<?php echo (isset($gks_user_settings['print']['form_id_reservation']) ? $gks_user_settings['print']['form_id_reservation'] : '0');?>;;
var from_php_print_def_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_user_settings['print']['forms_reservation']));?>'));



var from_php_dialog_item_message_email_from_array=[];
<?php 
$temp=array();
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>



var from_php_get_list_reservation_rooms_gr=$.base64.decode('<?php echo base64_encode($gks_get_list_reservation_rooms_gr);?>');
var from_php_get_list_reservation_rooms_en=$.base64.decode('<?php echo base64_encode($gks_get_list_reservation_rooms_en);?>');


var from_php_perm_print_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($perm_print_forms));?>'));

var from_php_check_vies_valid_wait=<?php echo ($mybasketarray['check_vies']['valid']==2 ? 'true' : 'false');?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>



});
</script>

<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>gks_country.js"></script>
<script src="cache/<?php echo $gks_user_cache_version_prefix;?>gks_langs.js"></script>
<script src="js/admin-hotel-reservation-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

echo gks_from_googlemaps_scripts();

include_once('_my_footer_admin.php');


