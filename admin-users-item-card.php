<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Οικονομική Καρτέλα Επαφής');
$nav_active_array=array('manage','manage_users');


db_open();
stat_record();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','view',$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

if ($id>0) {
  
  $user_companys=gks_get_companys_list();
  
  $filters = array();
  
  $filters[] = array(
    'name' => 'fstate',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => '102,103,109,1105,1106,1108,1110,1109,1113',
    'multiselect' => true,
    'field'  => "doc_state = '%V%'",
    'vals' => array(
      array('value' => 1111, 'text' => getOrderStateDescr('005prodraft'),           'sql' => "doc_state='005prodraft'"),
      array('value' => 111, 'text' => getAccInvStateDescr('010draft'),              'sql' => "doc_state='010draft'"),
      array('value' => 1101, 'text' => getOrderStateDescr('020pending'),            'sql' => "doc_state='020pending'"),
      array('value' => 1114, 'text' => getOrderStateDescr('025offer'),              'sql' => "doc_state='025offer'"),
      array('value' => 1102, 'text' => getOrderStateDescr('030forcancellation'),    'sql' => "doc_state='030forcancellation'"),
      array('value' => 100, 'text' => getAccInvStateDescr('040cancelled'),          'sql' => "doc_state='040cancelled'"),
      array('value' => 101, 'text' => getAccInvStateDescr('050proinvoice'),         'sql' => "doc_state='050proinvoice'"),
      array('value' => 1104, 'text' => getOrderStateDescr('050rejected'),           'sql' => "doc_state='050rejected'"),
      array('value' => 1112, 'text' => getOrderStateDescr('055wait_payment'),       'sql' => "doc_state='055wait_payment'"),
      array('value' => 1113, 'text' => getHotelReservationStatusDescr('070wait_payment') .gks_lang(' (Κρατήσεις)'),       'sql' => "doc_state='070wait_payment'"),
      array('value' => 1105, 'text' => getOrderStateDescr('060registered'),         'sql' => "doc_state='060registered'"),
      array('value' => 1106, 'text' => getOrderStateDescr('070inproduction'),       'sql' => "doc_state='070inproduction'"),
      array('value' => 1107, 'text' => getOrderStateDescr('080failed'),             'sql' => "doc_state='080failed'"),
      array('value' => 102, 'text' => getAccInvStateDescr('080listing'),            'sql' => "doc_state='080listing' or doc_state='080confirm'"),
      array('value' => 103, 'text' => getAccInvStateDescr('090ekdosi'),             'sql' => "doc_state='090ekdosi'"),
      array('value' => 1108, 'text' => getOrderStateDescr('090indelivery'),         'sql' => "doc_state='090indelivery'"),
      array('value' => 1110, 'text' => getOrderStateDescr('095execute'),            'sql' => "doc_state='095execute'"),
      array('value' => 109, 'text' => getAccInvStateDescr('100payment'),            'sql' => "(doc_state='100payment' or doc_state='110payment')"),
      array('value' => 1109, 'text' => getOrderStateDescr('100completed'),          'sql' => "doc_state='100completed'"),
      
       
      
    ),
  );
  
  
  
  $filters[] = array(
    'name' => 'fdoc_date',
    'class' => 'filterselectbox ui-state-default ui-corner-all',
    'style' => '',
    'title' => gks_lang('Ημερομηνία'),
    'has_custom_date' => true,
    'field' => 'doc_date', 
    'has_custom_default' => 1,
    //		'mywherepos'=>1,
    'vals' => gks_filter_date_vals(['field'=>'doc_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
  );
  
  if (count($user_companys)>1) {
    $vals=array();
    foreach ($user_companys as $key=>$value) {
      $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "company_id=".$value['id_company']." and company_sub_id=".$value['id_company_sub']);
    } 
    $filters[] = array(
      'name' => 'fcompany_id',
      'class' => 'filterselectbox',
      'style' => '',
      'title' => gks_lang('Εταιρεία'),
      'has_custom_default' => -1,
      'multiselect' => true,
      'field'  => "1=1",
      'vals' => $vals,
    );  
  }
  
  $filters[] = array(
    'name' => 'fjournal',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ημερολόγιο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "doc_journal_id = %V%",
    'vals' => array(
        //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.user_id=0"),
        //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.user_id<>0"),
    ),
    'sql' => "SELECT id_acc_journal AS id, acc_journal_descr AS descr
    FROM gks_acc_journal 
    ORDER BY sortorder,acc_journal_descr;",    
  );
  
  
  $filters[] = array(
    'name' => 'fseira',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Σειρά'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "doc_seira_id = %V%",
    'vals' => array(
        //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.user_id=0"),
        //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.user_id<>0"),
    ),
    'sql' => "SELECT id_acc_seira AS id, seira_descr AS descr
  FROM gks_acc_seires 
  ORDER BY sortorder,seira_descr;",    
  );
  
  
  $sortable = array(
    array('name' => 'soid', 'field' => 'doc_id'),
    array('name' => 'sostate', 'field' => 'doc_state'),
    array('name' => 'sood', 'field' => 'doc_date'),
    array('name' => 'socompany', 'field' => 'company_title, company_sub_title'),
    array('name' => 'sojournal', 'field' => 'acc_journal_descr'),
    array('name' => 'soseira', 'field' => 'seira_code'),
    array('name' => 'sonumber', 'field' => 'doc_number'),
    array('name' => 'soprice', 'field' => 'gks_price_net'),
    array('name' => 'sonetfpa', 'field' => 'gks_price_netfpa'),
    array('name' => 'sowithheld', 'field' => 'totalWithheldAmount'),
    array('name' => 'sobalance', 'field' => 'affect_balance_calc'),
    array('name' => 'sopa', 'field' => 'payment_acquirer_name'),
    
  );
  
  $has_sort=false;
  foreach ($_GET as $key1 => $value1) {
    foreach ($sortable as $value2) {
      if ($key1==$value2['name']) {
        $has_sort=true;
        break;
      }
    }
    if ($has_sort) break;
  }
  if ($has_sort==false) $_GET['sood']='desc';
  //echo '<pre>';print_r($_GET);die();
  
  $search_fields = array(
  'company_title',
  'company_sub_title',
  'acc_journal_descr',
  'seira_code',
  'payment_acquirer_name',
  );
  
  
  
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
  
  
  
  
  $sql = "SELECT SQL_CALC_FOUND_ROWS alltables.* 
  from (
    SELECT
    'acc_inv' as obj_type, 
    gks_acc_inv.id_acc_inv as doc_id,
    gks_acc_inv.inv_state as doc_state,
    gks_acc_inv.inv_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.seira_code,
    gks_acc_inv.inv_acc_number_int as doc_number,
    gks_acc_inv.gks_price_net,
    gks_acc_inv.gks_price_netfpa,
    gks_acc_inv.totalWithheldAmount,
    CASE
      WHEN (inv_state='080listing' or inv_state='090ekdosi' or inv_state='100payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    gks_payment_acquirers.payment_acquirer_name,
    gks_acc_inv.company_id,
    gks_acc_inv.company_sub_id,
    gks_acc_inv.inv_acc_journal_id as doc_journal_id,
    gks_acc_inv.inv_acc_seira_id as doc_seira_id
    FROM ((((((gks_acc_inv
    LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
    where gks_acc_inv.user_id=".$id."
    
    union
    
    SELECT
    'acc_pay' as obj_type, 
    gks_acc_pay.id_acc_pay as doc_id,
    gks_acc_pay.pay_state as doc_state,
    gks_acc_pay.pay_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.seira_code,
    gks_acc_pay.pay_acc_number_int as doc_number,
    gks_price_total as gks_price_net,
    gks_price_total as gks_price_netfpa,
    0 as totalWithheldAmount,
    CASE
      WHEN (pay_state='080listing' or pay_state='090ekdosi' or pay_state='100payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    '' as payment_acquirer_name,
    gks_acc_pay.company_id,
    gks_acc_pay.company_sub_id,
    gks_acc_pay.pay_acc_journal_id as doc_journal_id,
    gks_acc_pay.pay_acc_seira_id as doc_seira_id
    FROM (((((gks_acc_pay
    LEFT JOIN gks_company on gks_acc_pay.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_pay.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
    LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira
    where gks_acc_pay.user_id=".$id."
  
    union
    
    SELECT
    'order' as obj_type, 
    gks_orders.id_order as doc_id,
    gks_orders.order_state as doc_state,
    gks_orders.order_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.seira_code,
    gks_orders.order_number_int as doc_number,
    gks_orders.gks_price_net,
    gks_orders.gks_price_netfpa,
    0 as totalWithheldAmount,
  
    CASE
      WHEN (order_state='070wait_payment' or order_state='080confirm' or 
           order_state='100completed' or order_state='110payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,  
    gks_payment_acquirers.payment_acquirer_name,
    gks_orders.company_id,
    gks_orders.company_sub_id,
    gks_orders.order_journal_id as doc_journal_id,
    gks_orders.order_seira_id as doc_seira_id
    FROM ((((((gks_orders
    LEFT JOIN gks_company on gks_orders.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_orders.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_journal ON gks_orders.order_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
    LEFT JOIN gks_acc_seires ON gks_orders.order_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
    where gks_orders.user_id=".$id."
    
    
    union
    
    SELECT
    'reservation' as obj_type, 
    gks_hotel_reservation.id_hotel_reservation as doc_id,
    gks_hotel_reservation.reservation_status as doc_state,
    gks_hotel_reservation.reservation_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.seira_code,
    gks_hotel_reservation.reservation_number_int as doc_number,
    gks_hotel_reservation.gks_price_net,
    gks_hotel_reservation.gks_price_netfpa,
    0 as totalWithheldAmount,
  
    CASE
      WHEN (reservation_status='060registered' or reservation_status='070inproduction' or 
           reservation_status='090indelivery' or reservation_status='095execute' or reservation_status='100completed' or reservation_status='110payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,  
    gks_payment_acquirers.payment_acquirer_name,
    gks_hotel.company_id,
    gks_hotel.company_sub_id,
    gks_hotel_reservation.reservation_journal_id as doc_journal_id,
    gks_hotel_reservation.reservation_seira_id as doc_seira_id
    FROM (((((((gks_hotel_reservation
    LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)
    LEFT JOIN gks_company on gks_hotel.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_hotel.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_journal ON gks_hotel_reservation.reservation_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
    LEFT JOIN gks_acc_seires ON gks_hotel_reservation.reservation_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_payment_acquirers ON gks_hotel_reservation.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
    where gks_hotel_reservation.user_id=".$id."    
    
  ) as alltables 
  
  
  where 1=1 ".$where . $search_where;
  
  
  if (empty($sorted['sql'])) {
  	$sql .= " ORDER BY doc_date desc, doc_id desc";
  } else {
  	$sql .= " ORDER BY " . $sorted['sql'];
  }
  $sql .= " LIMIT ". $showFrom .", " . $rows_per_page;
  
  
  //echo '<pre>';echo $where;die();   
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  
  $sql_numrows = "SELECT FOUND_ROWS() AS `found_rows`;";
  $res_numrows = $db_link->query($sql_numrows);
  $row_numrows = $res_numrows->fetch_assoc();
  $total_records = $row_numrows['found_rows'];
  
  $pages = ceil($total_records / $rows_per_page) - 1;
  
  $paging = array('records' => '', 'total' => '', 'pages' => '');
  $url = $_SERVER['SCRIPT_NAME'].'?id='.$id;
  $params='';
  if (isset($filter['url']) && $filter['url']!='') $params.='&'.$filter['url'];
  if (isset($sorted['url']) && $sorted['url']!='') $params.='&'.$sorted['url'];
  if (isset($_GET['search_string']) && $_GET['search_string']!='') $params.='&search_string='.urlencode($_GET['search_string']);
  
  
  
  
  pagination($pages, $page, $total_records, $url, $paging, false, $params);
      
  $sortable_url='?id='.$id;
  if (isset($filter['url']) && $filter['url']!='') $sortable_url.='&'.$filter['url'];
  if (isset($page) && $page>0) $sortable_url.='&page='.$page;
  if (isset($_GET['search_string']) && $_GET['search_string']!='') $sortable_url.='&search_string='.urlencode($_GET['search_string']);
  
  
  
  $sortfields = explode("=", $sorted['url']);
  if (count($sortfields) < 2) {
      $sortfields[0] = '';
      $sortfields[1] = '';
  }
}


include_once('_my_header_admin.php');
?>
<style>
.splittd {
  border-left: 2px solid gray !important;
}

@media print {
  .gks_card_expand_icon {display: none;} 
  .gks_card_expand {border-width: 0px;}

}


</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>






<?php if ($id>0) {
  $sql_one="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  ".GKS_WP_TABLE_PREFIX."users.gks_balance, ".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image, 
  gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy,
  myfirst_name,mylast_name
  FROM ((".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name')) 
  )  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
  )  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id 
  
  where ".GKS_WP_TABLE_PREFIX."users.ID=".$id;
  $result_one = $db_link->query($sql_one);        
  if (!$result_one) debug_mail(false,'error sql',$sql_one);
  if (!$result_one) die('sql error');
  $gks_nickname='';
  $curr_balance=false;
  $eponimia='';
  $title='';
  $afm='';
  $doy='';
  $myfirst_name='';
  $mylast_name='';
  $photo_html='';
  if ($result_one->num_rows==1) {
    $row_one = $result_one->fetch_assoc();
    $gks_nickname=trim_gks($row_one['gks_nickname']);
    $curr_balance= floatval($row_one['gks_balance']);
    $eponimia=trim_gks($row_one['eponimia']);
    $title=trim_gks($row_one['title']);
    $afm=trim_gks($row_one['afm']);
    $doy=trim_gks($row_one['doy']);
    $myfirst_name=trim_gks($row_one['myfirst_name']);
    $mylast_name=trim_gks($row_one['mylast_name']);
    
    $myimgurl=trim_gks($row_one['gks_wsl_current_user_image'].'');
    if ($myimgurl != '') {
      $mydir = dirname($myimgurl);
      if (endwith($mydir,'/thumbnail')) {
        $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
      } else {
        $photo_url=$myimgurl;
      }
      $photo_html='<a class="lightgalleryitem_user gks_photo_link" tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$gks_nickname.'"><img style="max-height: 64px;" src="'.$myimgurl.'"></a>';
    }
  }
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header hide_on_print" style="text-align:center">
          <?php echo gks_lang('Επαφή');?>
        </div>
        <div class="card-body">
          <div class="row">
          
            <div class="col-sm">
              
              <?php if ($title=='' and $eponimia=='' and ($myfirst_name!='' or $mylast_name!='')) { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('Ονοματεπώνυμο');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $mylast_name.' '.$myfirst_name;?></div>
              </div>
              
              
              <?php } ?>
              <?php if ($title!='') { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('Τίτλος');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $title;?></div>
              </div>
              <?php } ?>
              <?php if ($eponimia!='') { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('Επωνυμία');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $eponimia;?></div>
              </div>
              <?php } ?>
              <?php if ($afm!='') { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $afm;?></div>
              </div>
              <?php } ?>
              <?php if ($doy!='') { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('ΔΟΥ');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $doy;?></div>
              </div>
              <?php } ?>
              
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('Τρέχον υπόλοιπο');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php 
                  if (is_float($curr_balance)) {
                    echo '<span style="font-weight: bold;">'.myCurrencyFormat($curr_balance).'</span>';
    
                  }
                 ?></div>
              </div>
              
            </div>

            <div class="col-sm hide_on_print">
              
              <div class="form-group row">
                <div class="col-sm-6 text-sm-right"><?php echo gks_lang('Υποκοριστικό');?>:</div>
                <div class="col-sm-6 text-sm-left" style="font-weight: bold;"><a href="admin-users-item.php?id=<?php echo $id;?>"><?php echo $gks_nickname;?></a></div>
              </div>
              <?php if ($photo_html!='') {?>
              <div class="form-group row">
                <div class="col-sm-6 text-sm-right"><span style="line-height: 64px;"><?php echo gks_lang('Φωτό');?>:</span></div>
                <div class="col-sm-6 text-sm-left" style="font-weight: bold;"><?php echo $photo_html;?></div>
              </div>
              <?php } ?>
    
            </div>

          </div>
            
                      
        </div>
      </div>
    </div>
  </div>
</div>
<?php } else {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="alert alert-warning" role="alert" style="text-align:center">
        <?php echo gks_lang('Επιλέξτε μια επαφή');?>:
      </div>
      
      <div class="text-center">
        <input id="user" type="text" class="form-control form-control-sm myneedsave"  
        value="" 
        style="max-width:300px;display:inline;" 
        placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" />
        
      </div>
    </div>
  </div>
</div>

<?php } ?>

<?php if ($id>0) {?>
<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <input type="hidden" name="id" value="<?php echo $id;?>"/>
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<div class="hide_on_print"><?php mytablepages($paging, $total_records); ?></div>
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?id=<?php echo $id;?>">#</a></th>
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostate', gks_lang('Κατάσταση')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sood', gks_lang('Ημερομηνία')); ?></th>
        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Χρέωση');?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Υπόλοιπο');?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Πίστωση');?></th>        
        
                
        <th nowrap class="table-dark" scope="col" style="text-align: left !important;" width="15%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: left !important;" width="25%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sojournal', gks_lang('Ημερολόγιο')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soseira', gks_lang('Σειρά')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonumber', gks_lang('Αριθμός')); ?></th>        
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice', gks_lang('Ποσό')); ?></th>        
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonetfpa', gks_lang('Μικτό')); ?></th>        
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sowithheld', '<span class="tooltipster" title="'.gks_lang('Φόροι Παρακρατούμενοι').'">'.gks_lang('Παρακρ.').'</span>'); ?></th>        
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: left !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopa', gks_lang('Τρόπος<br>Πληρωμής')); ?></th>  
    </tr>
</thead>
<tbody>
  
    <?php
    $curr_balance=0;
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm hide_on_print">
      
      <?php if ($row['obj_type']=='acc_inv') { ?>
        <a href="admin-acc-inv-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='acc_pay') { ?>
        <a href="admin-acc-pay-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='order') { ?>
        <a href="admin-orders-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='reservation') { ?>
        <a href="admin-hotel-reservation-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } ?>
      <?php echo $row['doc_id'];?>
    </td>
    
    <td nowrap class="mytdcm hide_on_print">
      <?php if ($row['obj_type']=='acc_inv') { ?>
        <span class="acc_inv_state_<?php echo $row['doc_state'];?>"><?php echo getAccInvStateDescr($row['doc_state']);?></span>
      <?php } else if ($row['obj_type']=='acc_pay') { ?>
        <span class="acc_pay_state_<?php echo $row['doc_state'];?>"><?php echo getAccPayStateDescr($row['doc_state']);?></span>
      <?php } else if ($row['obj_type']=='order') { ?>
        <span class="order_state_<?php echo $row['doc_state'];?>"><?php echo getOrderStateDescr($row['doc_state']);?></span>
      <?php } else if ($row['obj_type']=='reservation') { ?>
        <span class="reservation_status_<?php echo $row['doc_state'];?>"><?php echo getHotelReservationStatusDescr($row['doc_state']);?></span>
      <?php } ?>
    </td>  
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['doc_date']), 'd/m/Y H:i', 1);?><br> </td>  
    <?php
    
    $params=array(
      'id' => $id,
      'until_date' => $row['doc_date'], //eos KAI aftin tin imera-ora
    );
    $curr_balance=gks_balance_calc($params);
    
    $xreosi=0;
    $pistosi=0;
    if ($row['affect_balance_calc']>0) {
      $xreosi=$row['affect_balance_calc'];
    } else if ($row['affect_balance_calc']<0) {
      $pistosi=-$row['affect_balance_calc'];
    }
    ?>
    <td nowrap class="mytdcm"><?php if ($xreosi!=0) echo myCurrencyFormat($xreosi);?></td>  
    <td nowrap class="mytdcm" style="font-weight:bold;"><?php if ($curr_balance!=0) echo myCurrencyFormat($curr_balance);?></td>  
    <td nowrap class="mytdcm"><?php if ($pistosi!=0) echo myCurrencyFormat($pistosi);?></td>  
      
      
       
    <td        class="mytdcml"><?php echo $row['company_title']; if (isset($row['company_sub_title'])) echo '<br>'.$row['company_sub_title'];?></td> 
    <td        class="mytdcml"><?php echo $row['acc_journal_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['seira_code'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['doc_number']<>0) echo $row['doc_number'];?></td>
    <td nowrap class="mytdcm hide_on_print" ><?php 
      if ($row['gks_price_net']!=0) echo myCurrencyFormat($row['gks_price_net']);
    ?></td>
    <td nowrap class="mytdcm hide_on_print" ><?php 
      if ($row['gks_price_netfpa']!=0) echo myCurrencyFormat($row['gks_price_netfpa']);
    ?></td>
    <td nowrap class="mytdcm hide_on_print" ><?php 
      if ($row['totalWithheldAmount']!=0) echo myCurrencyFormat($row['totalWithheldAmount']);
    ?></td>

    <td        class="mytdcml hide_on_print"><?php echo $row['payment_acquirer_name'];?></a></td>

  </tr>
<?php    
    }
?>

</tbody>
</table>
<div class="hide_on_print"><?php mytablepages($paging, $total_records); ?></div>

<?php } ?>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>



  $('#fdoc_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdoc_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'fdoc_date') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'fdoc_date') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        $('#filter-form').submit();
      }
  });

  $('#user').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-user.php',
        dataType: "json",
        cache: false,
        data: mydata,
        error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},
        success: function( data ) {
          if (data.success == true) {
            response( data.list);
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      });
    },
    create: function(event, ui){
      //$(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    minLength: 3,
    autoFocus: true,
    delay: 300, //default
    select: function( event, ui ) {
      window.location.href='?id=' + ui.item.id;
    },

  });
});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');



