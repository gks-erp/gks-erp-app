<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



//echo '<pre>';
//echo '<img src="'.gks_qr_code_generate('https://phpqrcode.sourceforge.net/examples/index.php?example=008').'">';
//die();



//$mail = new PHPMailer(true);
//debug_mail(false,'stat index','ggggggggg');

//echo time();
//die();



$my_page_title=gks_lang('Αρχική Σελίδα');
$nav_active_array=array('index');

db_open();
stat_record();





gks_get_leads_status($leads_status,$leads_status_styles);
gks_get_tasks_status($tasks_status,$tasks_status_styles);

$perm_ret=gks_permission_get_user($my_wp_user_id);

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');


//print '<pre>'; print_r($perm_ret);die();

if ($perm_ret['data']['user_is_admin']==false and count($perm_ret['data']['objects'])==0) {
  //den einai admin, den exei prosvasi pouthena
  header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();
}
//print '<pre>'; print_r($perm_ret);die();

if ($perm_ret['success']==false) {
  //den xreiazete, den tha erthei edo pote. Diklida asfaleias
  header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();
}
//print '<pre>'; print_r($perm_ret);die();

//$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'index','view',0);
//if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


//echo 'index';
//die();

//$userrole='';
//if (isset($my_wp_user_info->roles)) {
//  if (in_array('employee',$my_wp_user_info->roles))  $userrole='employee';
//  if (in_array('ordermanager',$my_wp_user_info->roles))  $userrole='ordermanager';
//  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
//  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
//}
//if ($userrole=='') {header('Location: /my/admin-deny.php'); die(); }

//echo time();die();

$show_money=false;
if (ur_ad()) $show_money=true;


$show_chart1=false;
if (ur_ad()) $show_chart1=true;



$days_back=13;
$date_from=showDate(time()-$days_back*86400, 'd/m/Y', 1);
$date_to  =showDate(time(),   'd/m/Y', 1);


$date_from_time=gks_myFormatDate($date_from);
$date_from=date('d/m/Y', $date_from_time);
$date_to_time=gks_myFormatDate($date_to);
$date_to=date('d/m/Y', $date_to_time);
$p1 = date('Y-m-d H:i:s', _time_user($date_from_time,-1));
$p2 = date('Y-m-d H:i:s', _time_user($date_to_time+86400,-1));

function aak($mya, $myk) {
  $myr='';
  foreach ($mya as $val) {
    $myr.='"'. $val[$myk] .'",';
  }  
  if (strlen($myr)>0) $myr=substr($myr, 0, strlen($myr)-1);
  return $myr;
}
function aaks($mya, $myk) {
  $myr=0;
  foreach ($mya as $val) {
    $myr+=$val[$myk];
  }  
  return $myr;
}



//$date_from_time=gks_myFormatDate($date_from);
//$date_from=date('d/m/Y', $date_from_time);
//print '<pre>';
$morders_poliseis_enable=false;
if (ur_ad() && $GKS_ORDERS_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders','view',0);
  if ($perm_ret['success']) $morders_poliseis_enable=true;
}
$morders_agores_enable=$morders_poliseis_enable;

$ergasies_enable=false;
if (ur_ad() && $GKS_ORDERS_PRODUCTION) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_ergasies','view',0);
  if ($perm_ret['success']) $ergasies_enable=true;
}
$posta_enable=false;
if (ur_ad() && $GKS_ORDERS_PRODUCTION) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_posta','view',0);
  if ($perm_ret['success']) $posta_enable=true;
}



if ($morders_poliseis_enable) {
  $morders_poliseis=array();
  for($cdate=$date_from_time; $cdate<=$date_to_time; $cdate+=(24*60*60)) {
    $cdate_s=date('Y-m-d', $cdate);
    //print $cdate.' '.date('Y-m-d H:i:s', $cdate)."\r\n";
    $morders_poliseis[$cdate_s]=array(
      'label' => mb_substr(getWeekDayName(date('w',$cdate)),0,2).' '.date('d/m/Y', $cdate),
      '005prodraft' => 0,
      '010draft' => 0,
      '020pending' => 0,
      '025offer' => 0,
      '030forcancellation' => 0,
      '040cancelled' => 0,
      '050rejected' => 0,
      '055wait_payment'=>0,
      '060registered' => 0,
      '070inproduction' => 0,
      '080failed' => 0,
      '090indelivery' => 0,
      '095execute' => 0,
      '100completed' => 0,
      '110payment' => 0,
      'ajia' => 0,
    );
  }
  //die();

  $sql_field_ajia='gks_orders.gks_price_net';
  gks_plugins_functions_run('index_field_ajia',array(
    'sql_field_ajia' => &$sql_field_ajia,
  ));
  
  
  $sql="select (DATE(CONVERT_TZ(order_date, 'GMT','Europe/Athens'))) as mydate,
  order_state,
  count(id_order) as cc,
  sum(".$sql_field_ajia.") as ajia
  from gks_orders
  WHERE order_date >= '".$p1."' and order_date < '".$p2."'
  group by mydate,order_state
  order by mydate,order_state";
  
  $sql="select order_date,order_state,
  ".$sql_field_ajia." as ajia
  from (gks_orders
  LEFT JOIN gks_acc_journal ON gks_orders.order_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou  
  WHERE (gks_acc_eidi_parastatikon.eidos_parastatikou_type_id=31 or gks_acc_eidi_parastatikon.eidos_parastatikou_type_id is null)
  and order_date >= '".$p1."' and order_date < '".$p2."'
  ";
  if (count($perm_id_company_ids)>0) $sql.=" and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  
  
  //echo $sql; die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'stat index error sql',$sql);die('sql error');}
  while ($row = $result->fetch_assoc()) {  
    $order_date = showDate(strtotime($row['order_date']), 'Y-m-d', 1);
    if (isset($morders_poliseis[$order_date])) {
      if (isset($morders_poliseis[$order_date][$row['order_state']])) {
        $morders_poliseis[$order_date][$row['order_state']]++;
        $morders_poliseis[$order_date]['ajia']+=$row['ajia'];
      }
    }
  }
  
  $morders_poliseis_sums=array(
    '005prodraft' => 0,
    '010draft' => 0,
    '020pending' => 0,
    '025offer' => 0,
    '030forcancellation' => 0,
    '040cancelled' => 0,
    '050rejected' => 0,
    '055wait_payment'=>0,
    '060registered' => 0,
    '070inproduction' => 0,
    '080failed' => 0,
    '090indelivery' => 0,
    '095execute' => 0,
    '100completed' => 0,
    '110payment' => 0,
    'ajia' => 0,
  );
  
  foreach ($morders_poliseis as $value) {
    $morders_poliseis_sums['005prodraft']+=$value['005prodraft'];
    $morders_poliseis_sums['010draft']+=$value['010draft'];
    $morders_poliseis_sums['020pending']+=$value['020pending'];
    $morders_poliseis_sums['025offer']+=$value['025offer'];
    $morders_poliseis_sums['030forcancellation']+=$value['030forcancellation'];
    $morders_poliseis_sums['040cancelled']+=$value['040cancelled'];
    $morders_poliseis_sums['050rejected']+=$value['050rejected'];
    $morders_poliseis_sums['055wait_payment']+=$value['055wait_payment'];
    $morders_poliseis_sums['060registered']+=$value['060registered'];
    $morders_poliseis_sums['070inproduction']+=$value['070inproduction'];
    $morders_poliseis_sums['080failed']+=$value['080failed'];
    $morders_poliseis_sums['090indelivery']+=$value['090indelivery'];
    $morders_poliseis_sums['095execute']+=$value['095execute'];
    $morders_poliseis_sums['100completed']+=$value['100completed'];
    $morders_poliseis_sums['110payment']+=$value['110payment'];
    $morders_poliseis_sums['ajia']+=$value['ajia'];
  }

}
if ($morders_agores_enable) {
  $morders_agores=array();
  for($cdate=$date_from_time; $cdate<=$date_to_time; $cdate+=(24*60*60)) {
    $cdate_s=date('Y-m-d', $cdate);
    //print $cdate.' '.date('Y-m-d H:i:s', $cdate)."\r\n";
    $morders_agores[$cdate_s]=array(
      'label' => mb_substr(getWeekDayName(date('w',$cdate)),0,2).' '.date('d/m/Y', $cdate),
      '005prodraft' => 0,
      '010draft' => 0,
      '020pending' => 0,
      '025offer' => 0,
      '030forcancellation' => 0,
      '040cancelled' => 0,
      '050rejected' => 0,
      '055wait_payment'=>0,
      '060registered' => 0,
      '070inproduction' => 0,
      '080failed' => 0,
      '090indelivery' => 0,
      '095execute' => 0,
      '100completed' => 0,
      '110payment' => 0,
      'ajia' => 0,
    );
  }
  //die();
  
  $sql="select (DATE(CONVERT_TZ(order_date, 'GMT','Europe/Athens'))) as mydate,
  order_state,
  count(id_order) as cc,
  sum(".$sql_field_ajia.") as ajia
  from gks_orders
  WHERE order_date >= '".$p1."' and order_date < '".$p2."'
  group by mydate,order_state
  order by mydate,order_state";
  
  $sql="select order_date,order_state,
  ".$sql_field_ajia." as ajia
  from (gks_orders
  LEFT JOIN gks_acc_journal ON gks_orders.order_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou  
  WHERE (gks_acc_eidi_parastatikon.eidos_parastatikou_type_id=32)
  and order_date >= '".$p1."' and order_date < '".$p2."'
  ";
  if (count($perm_id_company_ids)>0) $sql.=" and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")";
  if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
  if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
  if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
  
  
  //echo $sql; die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'stat index error sql',$sql);die('sql error');}
  while ($row = $result->fetch_assoc()) {  
    $order_date = showDate(strtotime($row['order_date']), 'Y-m-d', 1);
    if (isset($morders_agores[$order_date])) {
      if (isset($morders_agores[$order_date][$row['order_state']])) {
        $morders_agores[$order_date][$row['order_state']]++;
        $morders_agores[$order_date]['ajia']+=$row['ajia'];
      }
    }
  }
  
  $morders_agores_sums=array(
    '005prodraft' => 0,
    '010draft' => 0,
    '020pending' => 0,
    '025offer' => 0,
    '030forcancellation' => 0,
    '040cancelled' => 0,
    '050rejected' => 0,
    '055wait_payment'=>0,
    '060registered' => 0,
    '070inproduction' => 0,
    '080failed' => 0,
    '090indelivery' => 0,
    '095execute' => 0,
    '100completed' => 0,
    '110payment' => 0,
    'ajia' => 0,
  );
  
  foreach ($morders_agores as $value) {
    $morders_agores_sums['005prodraft']+=$value['005prodraft'];
    $morders_agores_sums['010draft']+=$value['010draft'];
    $morders_agores_sums['020pending']+=$value['020pending'];
    $morders_agores_sums['025offer']+=$value['025offer'];
    $morders_agores_sums['030forcancellation']+=$value['030forcancellation'];
    $morders_agores_sums['040cancelled']+=$value['040cancelled'];
    $morders_agores_sums['050rejected']+=$value['050rejected'];
    $morders_agores_sums['055wait_payment']+=$value['055wait_payment'];
    $morders_agores_sums['060registered']+=$value['060registered'];
    $morders_agores_sums['070inproduction']+=$value['070inproduction'];
    $morders_agores_sums['080failed']+=$value['080failed'];
    $morders_agores_sums['090indelivery']+=$value['090indelivery'];
    $morders_agores_sums['095execute']+=$value['095execute'];
    $morders_agores_sums['100completed']+=$value['100completed'];
    $morders_agores_sums['110payment']+=$value['110payment'];
    $morders_agores_sums['ajia']+=$value['ajia'];
  }

}

if ($ergasies_enable) {

  $sql="SELECT id_production_ergasia, production_ergasia_descr
  FROM gks_production_ergasies
  ORDER BY production_ergasia_sortorder, production_ergasia_descr;";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'stat index error sql',$sql);die('sql error');}
  $ergasies=array();
  while ($row = $result->fetch_assoc()) {  
    $ergasies[$row['id_production_ergasia']]=array(
      'descr' => $row['production_ergasia_descr'],     
      '010draft' => 0,
      '030pending' => 0,
      '040ready' => 0,
      '050processing' => 0,
      '060pause' => 0,
    );
  }
  $ergasies_sum=array(
    '010draft' => 0,
    '030pending' => 0,
    '040ready' => 0,
    '050processing' => 0,
    '060pause' => 0,
  );
  $sql="SELECT ergasia_id, pl_state, Count(id_production_line) AS cc
  FROM gks_production_line
  WHERE pl_state In ('010draft','030pending','040ready','050processing','060pause')
  GROUP BY ergasia_id, pl_state";
  //echo $sql; die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'stat index error sql',$sql);die('sql error');}
  while ($row = $result->fetch_assoc()) {  
    if (isset($ergasies[$row['ergasia_id']])) {
      if (isset($ergasies[$row['ergasia_id']][$row['pl_state']])) {
        $ergasies[$row['ergasia_id']][$row['pl_state']]+=$row['cc'];
        $ergasies_sum[$row['pl_state']]+=$row['cc'];
      }
    }
  }
  $ergasies_clean=array();
  foreach ($ergasies as $value) {
    if ($value['010draft']!=0 or $value['030pending']!=0 or $value['040ready']!=0 or $value['050processing']!=0 or $value['060pause']!=0) {
      $ergasies_clean[]=$value;
    }
  }

}

if ($posta_enable) {
  $sql="SELECT id_production_posto, production_posto_descr
  FROM gks_production_posta
  ORDER BY production_posto_sortorder, production_posto_descr;";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'stat index error sql',$sql);die('sql error');}
  $posta=array();
  while ($row = $result->fetch_assoc()) {  
    $posta[$row['id_production_posto']]=array(
      'descr' => $row['production_posto_descr'],     
      '010draft' => 0,
      '030pending' => 0,
      '040ready' => 0,
      '050processing' => 0,
      '060pause' => 0,
    );
  }
  $posta_sum=array(
    '010draft' => 0,
    '030pending' => 0,
    '040ready' => 0,
    '050processing' => 0,
    '060pause' => 0,
  );
  $sql="SELECT gks_production_posta_ergasies.production_posto_id, gks_production_line.pl_state, count(gks_production_line.id_production_line) AS cc
  FROM gks_production_posta_ergasies 
  LEFT JOIN gks_production_line ON gks_production_posta_ergasies.production_ergasia_id = gks_production_line.ergasia_id
  WHERE pl_state In ('010draft','030pending','040ready','050processing','060pause')
  GROUP BY gks_production_posta_ergasies.production_posto_id, gks_production_line.pl_state;";
  //echo $sql; die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'stat index error sql',$sql);die('sql error');}
  while ($row = $result->fetch_assoc()) {  
    if (isset($posta[$row['production_posto_id']])) {
      if (isset($posta[$row['production_posto_id']][$row['pl_state']])) {
        $posta[$row['production_posto_id']][$row['pl_state']]+=$row['cc'];
        $posta_sum[$row['pl_state']]+=$row['cc'];
      }
    }
  }
  $posta_clean=array();
  foreach ($posta as $value) {
    if ($value['010draft']!=0 or $value['030pending']!=0 or $value['040ready']!=0 or $value['050processing']!=0 or $value['060pause']!=0) {
      $posta_clean[]=$value;
    }
  }

}



include_once('_my_header_admin.php');

//print '<pre>';
//print $p1;
//print "\r\n";
//print $p2;
//
//print "\r\n";
//print_r($morders_poliseis);
//print_r($morders_poliseis_sums);
//print '</pre>';

//echo gks_lang('Σύνδεση','part2');

?>



<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>

<div class="container-fluid" style="margin-top:36px;">
  <div class="row align-items-center1">

<?php
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','view',0);
  if ($perm_ret['success']) {?>  
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 10 τελευταίες επαφές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10users');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>         
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Υποκοριστικό');?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select ID as id,gks_nickname as descr FROM ".GKS_WP_TABLE_PREFIX."users ORDER BY ID DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-users-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>
<?php } ?>
    
<?php
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_eshop_products','view',0);
  if ($perm_ret['success']) {?>  
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τα 10 τελευταία είδη');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10products');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>         
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Περιγραφή')?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_product as id,product_descr as descr FROM gks_eshop_products where product_class<>'variable_item' ORDER BY id_product DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-products-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>
<?php } ?>

    


<?php
if ($GKS_CRM_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_activity','view',0);
  if ($perm_ret['success']) {?>
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Δραστηριότητα για σήμερα')?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10activity1');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID')?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κατάσταση')?></th>         
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Θέμα')?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_crm_activity as id,activity_subject as descr,activity_status 
          FROM gks_crm_activity 
          where activity_status='050new'
          and gks_crm_activity.activity_user_id=".$my_wp_user_id."
          and gks_crm_activity.activity_duedate >= '{$today}' and gks_crm_activity.activity_duedate < DATE_ADD('{$today}', INTERVAL 1 DAY)
          ORDER BY gks_crm_activity.activity_duedate DESC , gks_crm_activity.id_crm_activity DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-crm-activity.php?fstatus=1&fcity=-2&ftype=-1&fduedate=6&fobject=-1&search_string="><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              <td class="mytdcm"><span class="activity_status_<?php echo $row['activity_status'];?>"><?php echo getActivityStatusDescr($row['activity_status']);?></span></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>
    
<?php
if ($GKS_CRM_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_activity','view',0);
  if ($perm_ret['success']) {?>
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 10 τελευταίες δραστηριότητές μου');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10activity2');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κατάσταση')?></th>         
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Θέμα')?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_crm_activity as id,activity_subject as descr,activity_status 
          FROM gks_crm_activity 
          where gks_crm_activity.activity_user_id=".$my_wp_user_id."
          ORDER BY gks_crm_activity.id_crm_activity DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-crm-activity.php?&fstatus=-1&fcity=-2&fduedate=1&fobject=-1&soid=desc"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              <td class="mytdcm"><span class="activity_status_<?php echo $row['activity_status'];?>"><?php echo getActivityStatusDescr($row['activity_status']);?></span></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>

<?php
if ($GKS_CRM_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_calendar','view',0);
  if ($perm_ret['success']) {?>
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ημερολόγιο');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10calendar');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>         
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Θέμα')?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $range_start=$today;
          $range_end=user_server_curdate_plus(1);
          $sql="select id_calendar as id,calendar_title as descr
          FROM gks_calendar 
          where calendar_user_id=".$my_wp_user_id."
          and (
          	(gks_calendar.calendar_start >='".$range_start."' and gks_calendar.calendar_start <'".$range_end."') or 
          	(gks_calendar.calendar_end >'".$range_start."'   and gks_calendar.calendar_end <='".$range_end."') or
          	(gks_calendar.calendar_start <='".$range_start."' and gks_calendar.calendar_end >='".$range_end."')
          )
          ORDER BY id_calendar DESC limit 10";
          //echo '<pre>'.$sql;die();
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-crm-calendar.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
               <td class="mytdcml"><?php echo $row['descr'];?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>

<?php
if ($GKS_CRM_ENABLE and $GKS_CRM_LEADS_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_leads','view',0);
  if ($perm_ret['success']) {?>  
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 10 τελευταίες ευκαιρίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10crmleads');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κατάσταση')?></th>         
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Θέμα')?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Α.Έσοδα')?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_crm_lead as id,subject as descr,lead_status_id,esoda FROM gks_crm_leads ORDER BY id_crm_lead DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-crm-lead-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              
              <td class="mytdcm"><span class="lead_status_<?php echo $row['lead_status_id'];?>"><?php if (isset($leads_status[$row['lead_status_id']])) echo $leads_status[$row['lead_status_id']]['lead_status_descr'];?></span></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
              <td class="mytdcmr"><?php if ($row['esoda']!=0) echo myCurrencyFormat($row['esoda']);?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>

<?php
if ($GKS_CRM_ENABLE and $GKS_CRM_TASKS_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_tasks','view',0);
  if ($perm_ret['success']) {?>  
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 10 τελευταίες εργασίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10crmtasks');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κατάσταση')?></th>         
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Θέμα')?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Α.Έσοδα')?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_crm_task as id,subject as descr,task_status_id,esoda FROM gks_crm_tasks ORDER BY id_crm_task DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-crm-task-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              
              <td class="mytdcm"><span class="task_status_<?php echo $row['task_status_id'];?>"><?php if (isset($tasks_status[$row['task_status_id']])) echo $tasks_status[$row['task_status_id']]['task_status_descr'];?></span></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
              <td class="mytdcmr"><?php if ($row['esoda']!=0) echo myCurrencyFormat($row['esoda']);?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>

<?php
if ($GKS_CRM_ENABLE and $GKS_CRM_MACHINE_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_machine','view',0);
  if ($perm_ret['success']) {?>  
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 10 τελευταίες συσκευές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10crmmachine');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>         
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Όνομα');?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_crm_machine as id,crm_machine_name as descr FROM gks_crm_machine ORDER BY id_crm_machine DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-crm-machine-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>


<?php
if ($GKS_WARE_HOUSE_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov','view',0);
  if ($perm_ret['success']) {?>  
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τα 10 τελευταία δελτία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10whi_mov');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κατάσταση')?></th>          
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Όνομα');?></th> 
              <th class="table-dark" scope="col" style="text-align: right  !important;" width="0%"><?php echo gks_lang('Ποσότητα');?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_whi_mov as id, gks_nickname as descr, mov_state, products_posotita
          FROM gks_whi_mov 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.user_id = ".GKS_WP_TABLE_PREFIX."users.ID 
          where 1=1 ";
          if (count($perm_id_company_ids)>0) $sql.=" and gks_whi_mov.company_id in (".implode(',',$perm_id_company_ids).")";
          if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_whi_mov.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
          if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_whi_mov.mov_whi_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
          if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_whi_mov.mov_whi_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
          $sql.=" ORDER BY id_whi_mov DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-whi-mov-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              <td class="mytdcm"><span class="whi_mov_state_<?php echo $row['mov_state'];?>"><?php echo getWhiMovStateDescr($row['mov_state']);?></span></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
              <td class="mytdcmr"><?php if ($row['products_posotita']!=0) echo myNumberFormatNo0Local($row['products_posotita']);?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>


<?php
if ($GKS_ORDERS_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders','view',0);
  if ($perm_ret['success']) {?>  
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 10 τελευταίες παραγγελίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10orders');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κατάσταση')?></th>          
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Όνομα');?></th> 
              <th class="table-dark" scope="col" style="text-align: right  !important;" width="0%"><?php echo gks_lang('Αξία');?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_order as id, gks_nickname as descr, order_state, gks_price_net
          FROM gks_orders 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID 
          where 1=1 ";
          if (count($perm_id_company_ids)>0) $sql.=" and gks_orders.company_id in (".implode(',',$perm_id_company_ids).")";
          if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_orders.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
          if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_orders.order_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
          if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_orders.order_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
          $sql.=" ORDER BY id_order DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-orders-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              <td class="mytdcm"><span class="order_state_<?php echo $row['order_state'];?>"><?php echo getOrderStateDescr($row['order_state']);?></span></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
              <td class="mytdcmr"><?php if ($row['gks_price_net']!=0) echo myCurrencyFormat($row['gks_price_net']);?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>




<?php
if ($GKS_ACC_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv','view',0);
  if ($perm_ret['success']) {?>  
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τα 10 τελευταία παραστατικά');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10acc_inv');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κατάσταση')?></th>          
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Όνομα');?></th> 
              <th class="table-dark" scope="col" style="text-align: right  !important;" width="0%"><?php echo gks_lang('Αξία');?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_acc_inv as id, gks_nickname as descr, inv_state, gks_price_net
          FROM gks_acc_inv 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID 
          where 1=1 ";
          if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_inv.company_id in (".implode(',',$perm_id_company_ids).")";
          if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_inv.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
          if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_inv.inv_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
          if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_inv.inv_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
          $sql.=" ORDER BY id_acc_inv DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-acc-inv-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              <td class="mytdcm"><span class="acc_inv_state_<?php echo $row['inv_state'];?>"><?php echo getAccInvStateDescr($row['inv_state']);?></span></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
              <td class="mytdcmr"><?php if ($row['gks_price_net']!=0) echo myCurrencyFormat($row['gks_price_net']);?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>


<?php
if ($GKS_ACC_ENABLE) {
  $perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_pay','view',0);
  if ($perm_ret['success']) {?>  
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Οι 10 τελευταίες πληρωμές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('last10acc_pay');?>>   
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ID');?></th>         
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Κατάσταση')?></th>          
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Όνομα');?></th> 
              <th class="table-dark" scope="col" style="text-align: right  !important;" width="0%"><?php echo gks_lang('Αξία');?></th> 
            </tr>
          </thead>
          <tbody>               
          <?php
          $sql="select id_acc_pay as id, gks_nickname as descr, pay_state, gks_price_total
          FROM gks_acc_pay
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay.user_id = ".GKS_WP_TABLE_PREFIX."users.ID 
          where 1=1 ";
          if (count($perm_id_company_ids)>0) $sql.=" and gks_acc_pay.company_id in (".implode(',',$perm_id_company_ids).")";
          if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_acc_pay.company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
          if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_acc_pay.pay_acc_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
          if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_acc_pay.pay_acc_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";
          $sql.=" ORDER BY id_acc_pay DESC limit 10";
          $result = $db_link->query($sql);        
          if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
          while ($row = $result->fetch_assoc()) {?>
            <tr>
              <td class="mytdcm" nowrap><?php echo $row['id'];?></td>
              <td class="mytdcm"><a href="admin-acc-pay-item.php?id=<?php echo $row['id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή')?>"></i></a></td>
              <td class="mytdcm"><span class="acc_pay_state_<?php echo $row['pay_state'];?>"><?php echo getAccPayStateDescr($row['pay_state']);?></span></td>
              <td class="mytdcml"><?php echo $row['descr'];?></td>
              <td class="mytdcmr"><?php if ($row['gks_price_total']!=0) echo myCurrencyFormat($row['gks_price_total']);?></td>
            </tr>
          <?php }?>
          </tbody> 
          </table>
        </div>
      </div>
    </div>

<?php }} ?>

    
  </div>
</div>


<?php
if ($morders_poliseis_enable) {?>
<div class="container-fluid" style="margin-top:36px;">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <div class="chart-container" style="position: relative; width:96%;height:500px;text-align: center;margin: auto;border: 1px solid #b7b7b7;">
          <canvas id="canvas_morders_poliseis"></canvas>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php
if ($morders_agores_enable) {?>
<div class="container-fluid" style="margin-top:36px;">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <div class="chart-container" style="position: relative; width:96%;height:500px;text-align: center;margin: auto;border: 1px solid #b7b7b7;">
          <canvas id="canvas_morders_agores"></canvas>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if ($ergasies_enable) { ?>
<div class="container-fluid" style="margin-top:36px;">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <div class="chart-container" style="position: relative; width:96%;height:500px;text-align: center;margin: auto;border: 1px solid #b7b7b7;">
          <canvas id="canvas_ergasies"></canvas>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<?php if ($posta_enable) { ?>

<div class="container-fluid" style="margin-top:36px;">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <div class="chart-container" style="position: relative; width:96%;height:500px;text-align: center;margin: auto;border: 1px solid #b7b7b7;">
          <canvas id="canvas_posta"></canvas>
      </div>
    </div>
  </div>
</div>
<?php } ?>





<script  src="js/chartjs-2.9.2/dist/Chart.bundle.min.js"></script>
<link rel="stylesheet" href="js/chartjs-2.9.2/dist/Chart.min.css" type="text/css">



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#date_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#date_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));


<?php if ($morders_poliseis_enable) { ?>    
  var ctx = document.getElementById('canvas_morders_poliseis');
  var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels: [<?php echo aak($morders_poliseis,'label');?>],
          datasets: [
<?php if ($show_money ) {?>          
            {
              type: 'line',
              label: '<?php echo gks_lang('Αξία');?>',
              data: [<?php echo aak($morders_poliseis,'ajia');?>],
              fill: false,
              backgroundColor: '#0032c3',
              borderColor: '#002aa1',
              borderWidth: 1,
              yAxisID: 'y-ajia',
            },
<?php }            
      if ($morders_poliseis_sums['005prodraft']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('005prodraft');?>',
              data: [<?php echo aak($morders_poliseis,'005prodraft');?>],
              backgroundColor: '#777777',
              borderColor: '#777777',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_poliseis_sums['010draft']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('010draft');?>',
              data: [<?php echo aak($morders_poliseis,'010draft');?>],
              backgroundColor: '#aaaaaa',
              borderColor: '#787878',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_poliseis_sums['020pending']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('020pending');?>',
              data: [<?php echo aak($morders_poliseis,'020pending');?>],
              backgroundColor: '#5bc0de',
              borderColor: '#4896ad',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_poliseis_sums['025offer']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('025offer');?>',
              data: [<?php echo aak($morders_poliseis,'025offer');?>],
              backgroundColor: '#5bc0de',
              borderColor: '#4896ad',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }
      if ($morders_poliseis_sums['030forcancellation']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('030forcancellation');?>',
              data: [<?php echo aak($morders_poliseis,'030forcancellation');?>],
              backgroundColor: '#ed9c28',
              borderColor: '#b3761e',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_poliseis_sums['040cancelled']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('040cancelled');?>',
              data: [<?php echo aak($morders_poliseis,'040cancelled');?>],
              backgroundColor: '#ff0000',
              borderColor: '#c30000',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_poliseis_sums['050rejected']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('050rejected');?>',
              data: [<?php echo aak($morders_poliseis,'050rejected');?>],
              backgroundColor: '#d2322d',
              borderColor: '#962420',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }  
      if ($morders_poliseis_sums['055wait_payment']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('055wait_payment');?>',
              data: [<?php echo aak($morders_poliseis,'055wait_payment');?>],
              backgroundColor: '#518df1',
              borderColor: '#518df1',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }          
      if ($morders_poliseis_sums['060registered']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('060registered');?>',
              data: [<?php echo aak($morders_poliseis,'060registered');?>],
              backgroundColor: '#337AB7',
              borderColor: '#245580',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_poliseis_sums['070inproduction']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('070inproduction');?>',
              data: [<?php echo aak($morders_poliseis,'070inproduction');?>],
              backgroundColor: '#8261a7',
              borderColor: '#584272',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_poliseis_sums['080failed']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('080failed');?>',
              data: [<?php echo aak($morders_poliseis,'080failed');?>],
              backgroundColor: '#ff3a00',
              borderColor: '#c92e00',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_poliseis_sums['090indelivery']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('090indelivery');?>',
              data: [<?php echo aak($morders_poliseis,'090indelivery');?>],
              backgroundColor: '#71e399',
              borderColor: '#54aa72',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }  
      if ($morders_poliseis_sums['095execute']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('095execute');?>',
              data: [<?php echo aak($morders_poliseis,'095execute');?>],
              backgroundColor: '#71e399',
              borderColor: '#71e399',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }          
      if ($morders_poliseis_sums['100completed']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('100completed');?>',
              data: [<?php echo aak($morders_poliseis,'100completed');?>],
              backgroundColor: '#47a447',
              borderColor: '#2e6b2e',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php } 
      if ($morders_poliseis_sums['110payment']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('110payment');?>',
              data: [<?php echo aak($morders_poliseis,'110payment');?>],
              backgroundColor: '#47a447',
              borderColor: '#2e6b2e',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php } 
?>          
            
          ]
      },
      options: {
        responsive: true,
        maintainAspectRatio:false,
        title: {
				  display: true,
					text: '<?php echo gks_lang('Παραγγελίες Πωλήσεων');?>'
				},
        tooltips: {
          //mode: 'index', //label
          //position: 'nearest',
          //intersect: false,
        },				
        hover: {
          //mode: 'index', //dataset
        },				
        scales: {
          xAxes: [{
            //stacked: true,
            ticks: {
              beginAtZero: true
            }
          }],
          yAxes: [
            {
              //stacked: true,
              ticks: {
                beginAtZero: true
              },
              position: 'left',
              id: 'y-posotita',
              scaleLabel: {
								display: true,
								labelString: '<?php echo gks_lang('Πλήθος Παραγγελιών');?>'
							},              
            },

<?php if ($show_money) {?>            
            {
              stacked: true,
              ticks: {
                beginAtZero: true
              },
              position: 'right',
              id: 'y-ajia',
              gridLines : {
                //display: true,
					      //drawBorder: true,
					      //drawOnChartArea: false,
					      color: 'rgba(0,42,161,0.3)',
					      borderWidth: 1,
      				},
              scaleLabel: {
								display: true,
								labelString: '<?php echo gks_lang('Αξία Παραγγελιών');?>'
							},       				
            },
<?php } ?>            
          ],
        }
      }
  });

<?php } ?> 


<?php if ($morders_agores_enable) { ?>    
  var ctx = document.getElementById('canvas_morders_agores');
  var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels: [<?php echo aak($morders_agores,'label');?>],
          datasets: [
<?php if ($show_money ) {?>          
            {
              type: 'line',
              label: '<?php echo gks_lang('Αξία');?>',
              data: [<?php echo aak($morders_agores,'ajia');?>],
              fill: false,
              backgroundColor: '#0032c3',
              borderColor: '#002aa1',
              borderWidth: 1,
              yAxisID: 'y-ajia',
            },
<?php }            
      if ($morders_agores_sums['005prodraft']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('005prodraft');?>',
              data: [<?php echo aak($morders_agores,'005prodraft');?>],
              backgroundColor: '#777777',
              borderColor: '#777777',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_agores_sums['010draft']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('010draft');?>',
              data: [<?php echo aak($morders_agores,'010draft');?>],
              backgroundColor: '#aaaaaa',
              borderColor: '#787878',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_agores_sums['020pending']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('020pending');?>',
              data: [<?php echo aak($morders_agores,'020pending');?>],
              backgroundColor: '#5bc0de',
              borderColor: '#4896ad',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_agores_sums['025offer']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('025offer');?>',
              data: [<?php echo aak($morders_agores,'025offer');?>],
              backgroundColor: '#5bc0de',
              borderColor: '#4896ad',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }
      if ($morders_agores_sums['030forcancellation']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('030forcancellation');?>',
              data: [<?php echo aak($morders_agores,'030forcancellation');?>],
              backgroundColor: '#ed9c28',
              borderColor: '#b3761e',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_agores_sums['040cancelled']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('040cancelled');?>',
              data: [<?php echo aak($morders_agores,'040cancelled');?>],
              backgroundColor: '#ff0000',
              borderColor: '#c30000',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_agores_sums['050rejected']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('050rejected');?>',
              data: [<?php echo aak($morders_agores,'050rejected');?>],
              backgroundColor: '#d2322d',
              borderColor: '#962420',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }  
      if ($morders_agores_sums['055wait_payment']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('055wait_payment');?>',
              data: [<?php echo aak($morders_agores,'055wait_payment');?>],
              backgroundColor: '#518df1',
              borderColor: '#518df1',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }          
      if ($morders_agores_sums['060registered']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('060registered');?>',
              data: [<?php echo aak($morders_agores,'060registered');?>],
              backgroundColor: '#337AB7',
              borderColor: '#245580',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_agores_sums['070inproduction']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('070inproduction');?>',
              data: [<?php echo aak($morders_agores,'070inproduction');?>],
              backgroundColor: '#8261a7',
              borderColor: '#584272',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_agores_sums['080failed']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('080failed');?>',
              data: [<?php echo aak($morders_agores,'080failed');?>],
              backgroundColor: '#ff3a00',
              borderColor: '#c92e00',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }            
      if ($morders_agores_sums['090indelivery']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('090indelivery');?>',
              data: [<?php echo aak($morders_agores,'090indelivery');?>],
              backgroundColor: '#71e399',
              borderColor: '#54aa72',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }  
      if ($morders_agores_sums['095execute']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('095execute');?>',
              data: [<?php echo aak($morders_agores,'095execute');?>],
              backgroundColor: '#71e399',
              borderColor: '#71e399',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php }          
      if ($morders_agores_sums['100completed']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('100completed');?>',
              data: [<?php echo aak($morders_agores,'100completed');?>],
              backgroundColor: '#47a447',
              borderColor: '#2e6b2e',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php } 
      if ($morders_agores_sums['110payment']>0) {?>          
            {
              label: '<?php echo getOrderStateDescr('110payment');?>',
              data: [<?php echo aak($morders_agores,'110payment');?>],
              backgroundColor: '#47a447',
              borderColor: '#2e6b2e',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
<?php } 
?>          
            
          ]
      },
      options: {
        responsive: true,
        maintainAspectRatio:false,
        title: {
				  display: true,
					text: '<?php echo gks_lang('Παραγγελίες Αγορών');?>'
				},
        tooltips: {
          //mode: 'index', //label
          //position: 'nearest',
          //intersect: false,
        },				
        hover: {
          //mode: 'index', //dataset
        },				
        scales: {
          xAxes: [{
            //stacked: true,
            ticks: {
              beginAtZero: true
            }
          }],
          yAxes: [
            {
              //stacked: true,
              ticks: {
                beginAtZero: true
              },
              position: 'left',
              id: 'y-posotita',
              scaleLabel: {
								display: true,
								labelString: '<?php echo gks_lang('Πλήθος Παραγγελιών');?>'
							},              
            },

<?php if ($show_money) {?>            
            {
              stacked: true,
              ticks: {
                beginAtZero: true
              },
              position: 'right',
              id: 'y-ajia',
              gridLines : {
                //display: true,
					      //drawBorder: true,
					      //drawOnChartArea: false,
					      color: 'rgba(0,42,161,0.3)',
					      borderWidth: 1,
      				},
              scaleLabel: {
								display: true,
								labelString: '<?php echo gks_lang('Αξία Παραγγελιών');?>'
							},       				
            },
<?php } ?>            
          ],
        }
      }
  });

<?php } ?> 

<?php if ($ergasies_enable) { ?>  
  var ctx_ergasies = document.getElementById('canvas_ergasies');
  var myChart_ergasies = new Chart(ctx_ergasies, {
      type: 'bar',
      data: {
          labels: [<?php echo aak($ergasies_clean,'descr');?>],
          datasets: [
<?php if ($ergasies_sum['010draft']>0) {?>          
            {
              label: '<?php echo getProductionLineStateDescr('010draft');?>',
              data: [<?php echo aak($ergasies_clean,'010draft');?>],
              backgroundColor: '#aaaaaa',
              borderColor: '#787878',
              borderWidth: 1,
            },
<?php }
      if ($ergasies_sum['030pending']>0) {?>               
            {
              label: '<?php echo getProductionLineStateDescr('030pending');?>',
              data: [<?php echo aak($ergasies_clean,'030pending');?>],
              backgroundColor: '#5bc0de',
              borderColor: '#4896ad',
              borderWidth: 1,
            },
<?php }
      if ($ergasies_sum['040ready']>0) {?>               
            {
              label: '<?php echo getProductionLineStateDescr('040ready');?>',
              data: [<?php echo aak($ergasies_clean,'040ready');?>],
              backgroundColor: '#337AB7',
              borderColor: '#245580',
              borderWidth: 1,
            },
<?php }
      if ($ergasies_sum['050processing']>0) {?>               
            {
              label: '<?php echo getProductionLineStateDescr('050processing');?>',
              data: [<?php echo aak($ergasies_clean,'050processing');?>],
              backgroundColor: '#ffff00',
              borderColor: '#000000',
              borderWidth: 1,
            },
<?php }
      if ($ergasies_sum['060pause']>0) {?>               
            {
              label: '<?php echo getProductionLineStateDescr('060pause');?>',
              data: [<?php echo aak($ergasies_clean,'060pause');?>],
              backgroundColor: '#ed9c28',
              borderColor: '#b3761e',
              borderWidth: 1,
            },
<?php } ?>               
          ]
      },
      options: {
        responsive: true,
        maintainAspectRatio:false,
        title: {
				  display: true,
					text: '<?php echo gks_lang('Εργασίες');?>'
				},
        tooltips: {
          //mode: 'index', //label
          //position: 'nearest',
          //intersect: false,
        },				
        hover: {
          //mode: 'index', //dataset
        },				
        scales: {
          xAxes: [{
            stacked: true,
            ticks: {
              beginAtZero: true
            }
          }],
          yAxes: [
            {
              stacked: true,
              ticks: {
                beginAtZero: true
              },
              position: 'left',
              id: 'y-posotita',
            },
          ],
        }
      }
  }); 
  
<?php } ?>
<?php if ($posta_enable) { ?>
  
  var ctx_posta = document.getElementById('canvas_posta');
  var myChart_posta = new Chart(ctx_posta, {
      type: 'bar',
      data: {
          labels: [<?php echo aak($posta_clean,'descr');?>],
          datasets: [
<?php if ($posta_sum['010draft']>0) {?>          
            {
              label: '<?php echo getProductionLineStateDescr('010draft');?>',
              data: [<?php echo aak($posta_clean,'010draft');?>],
              backgroundColor: '#aaaaaa',
              borderColor: '#787878',
              borderWidth: 1,
            },
<?php }
      if ($posta_sum['030pending']>0) {?>               
            {
              label: '<?php echo getProductionLineStateDescr('030pending');?>',
              data: [<?php echo aak($posta_clean,'030pending');?>],
              backgroundColor: '#5bc0de',
              borderColor: '#4896ad',
              borderWidth: 1,
            },
<?php }
      if ($posta_sum['040ready']>0) {?>               
            {
              label: '<?php echo getProductionLineStateDescr('040ready');?>',
              data: [<?php echo aak($posta_clean,'040ready');?>],
              backgroundColor: '#337AB7',
              borderColor: '#245580',
              borderWidth: 1,
            },
<?php }
      if ($posta_sum['050processing']>0) {?>               
            {
              label: '<?php echo getProductionLineStateDescr('050processing');?>',
              data: [<?php echo aak($posta_clean,'050processing');?>],
              backgroundColor: '#ffff00',
              borderColor: '#000000',
              borderWidth: 1,
            },
<?php }
      if ($posta_sum['060pause']>0) {?>               
            {
              label: '<?php echo getProductionLineStateDescr('060pause');?>',
              data: [<?php echo aak($posta_clean,'060pause');?>],
              backgroundColor: '#ed9c28',
              borderColor: '#b3761e',
              borderWidth: 1,
            },
<?php } ?>               
          ]
      },
      options: {
        responsive: true,
        maintainAspectRatio:false,
        title: {
				  display: true,
					text: '<?php echo gks_lang('Πόστα');?>'
				},
        tooltips: {
          //mode: 'index', //label
          //position: 'nearest',
          //intersect: false,
        },				
        hover: {
          //mode: 'index', //dataset
        },				
        scales: {
          xAxes: [{
            stacked: true,
            ticks: {
              beginAtZero: true
            }
          }],
          yAxes: [
            {
              stacked: true,
              ticks: {
                beginAtZero: true
              },
              position: 'left',
              id: 'y-posotita',
            },
          ],
        }
      }
  });
  


<?php } ?>
  
});
</script>



<?php 
include_once('_my_footer_admin.php');  