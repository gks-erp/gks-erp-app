<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Pivot Table - Παραγγελίες Prolab');
$nav_active_array=array('sales','sales_orders_pivot');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders_pivot','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$filters = array();


$filters[] = array(
  'name' => 'fdate_add',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία'),
  'has_custom_date' => true,
  'field' => 'gks_orders.order_date', 
  'has_custom_default' => 16,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_orders.order_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),

);



$filters[] = array(
  'name' => 'fstate',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατάσταση'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.order_state = '%V%'",
  'vals' => array(
    array('value' => 111, 'text' => getOrderStateDescr('005prodraft'),      'sql' => "gks_orders.order_state='005prodraft'"),
    array('value' => 100, 'text' => getOrderStateDescr('010draft'),      'sql' => "gks_orders.order_state='010draft'"),
    array('value' => 101, 'text' => getOrderStateDescr('020pending'),      'sql' => "gks_orders.order_state='020pending'"),
    array('value' => 114, 'text' => getOrderStateDescr('025offer'),      'sql' => "gks_orders.order_state='025offer'"),
    array('value' => 102, 'text' => getOrderStateDescr('030forcancellation'),      'sql' => "gks_orders.order_state='030forcancellation'"),
    array('value' => 103, 'text' => getOrderStateDescr('040cancelled'),      'sql' => "gks_orders.order_state='040cancelled'"),
    array('value' => 104, 'text' => getOrderStateDescr('050rejected'),      'sql' => "gks_orders.order_state='050rejected'"),
    array('value' => 112, 'text' => getOrderStateDescr('055wait_payment'),      'sql' => "gks_orders.order_state='055wait_payment'"),
    array('value' => 105, 'text' => getOrderStateDescr('060registered'),      'sql' => "gks_orders.order_state='060registered'"),
    array('value' => 106, 'text' => getOrderStateDescr('070inproduction'),      'sql' => "gks_orders.order_state='070inproduction'"),
    array('value' => 107, 'text' => getOrderStateDescr('080failed'),      'sql' => "gks_orders.order_state='080failed'"),
    array('value' => 108, 'text' => getOrderStateDescr('090indelivery'),      'sql' => "gks_orders.order_state='090indelivery'"),
    array('value' => 110, 'text' => getOrderStateDescr('095execute'),      'sql' => "gks_orders.order_state='095execute'"),
    array('value' => 109, 'text' => getOrderStateDescr('100completed'),      'sql' => "gks_orders.order_state='100completed'"),
    array('value' => 113, 'text' => getOrderStateDescr('110payment'),      'sql' => "gks_orders.order_state='110payment'"),
  
  ),
);
$filters[] = array(
  'name' => 'fcustomer',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Πελάτης'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.user_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.user_id=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.user_id<>0"),
  ),
  'sql' => "SELECT gks_orders.user_id AS id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname AS descr
FROM gks_orders LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE (((".GKS_WP_TABLE_PREFIX."users.ID) Is Not Null))
GROUP BY gks_orders.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",    
);


$filters[] = array(
  'name' => 'fbtid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Κατηγορία'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.base_type_id = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.base_type_id=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.base_type_id<>0"),
  ),
  'sql' => "SELECT gks_orders.base_type_id as id, gks_cmsb_base_type.base_type_descr as descr
FROM gks_orders LEFT JOIN gks_cmsb_base_type ON gks_orders.base_type_id = gks_cmsb_base_type.id_base_type
WHERE (((gks_cmsb_base_type.id_base_type) Is Not Null))
GROUP BY gks_orders.base_type_id, gks_cmsb_base_type.base_type_descr
ORDER BY gks_cmsb_base_type.base_type_descr;",    
);


$filters[] = array(
  'name' => 'fdwid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τρόπος Αποστολής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.tropos_apostolis = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.tropos_apostolis=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.tropos_apostolis<>0"),
  ),
  'sql' => "SELECT gks_delivery_methods.id_delivery_method AS id, gks_delivery_methods.delivery_method_name AS descr
  FROM gks_orders LEFT JOIN gks_delivery_methods ON gks_orders.tropos_apostolis = gks_delivery_methods.id_delivery_method
  WHERE gks_orders.tropos_apostolis>0 AND gks_delivery_methods.id_delivery_method>0
  GROUP BY gks_delivery_methods.id_delivery_method, gks_delivery_methods.delivery_method_name, gks_delivery_methods.mysortorder",    
);

$filters[] = array(
  'name' => 'fpwid',
  'class' => 'filterselectbox',
  'style' => '',
  'title' => gks_lang('Τρόπος Πληρωμής'),
  'has_custom_default' => -1,
  'multiselect' => true,
  'field'  => "gks_orders.tropos_pliromis = %V%",
  'vals' => array(
      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.tropos_pliromis=0"),
      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.tropos_pliromis<>0"),
  ),
  'sql' => "SELECT gks_payment_acquirers.id_payment_acquirer as id, gks_payment_acquirers.payment_acquirer_name AS descr
  FROM gks_orders LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
  where gks_orders.tropos_pliromis>0 and gks_payment_acquirers.id_payment_acquirer > 0
  GROUP BY gks_payment_acquirers.id_payment_acquirer, gks_payment_acquirers.payment_acquirer_name, gks_payment_acquirers.mysortorder
  ORDER BY gks_payment_acquirers.mysortorder",    
);

$filters[] = array(
  'name' => 'fddate',
  'class' => 'filterselectbox ui-state-default ui-corner-all',
  'style' => '',
  'title' => gks_lang('Ημερομηνία Παράδοσης'),
  'has_custom_date' => true,
  'field' => 'gks_orders.ddate', 
  'has_custom_default' => 1,
  //		'mywherepos'=>1,
  'vals' => gks_filter_date_vals(['field'=>'gks_orders.ddate','future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia]),
);

if  ($GKS_ORDERS_OCCASION) {
$filters[] = array(
    'name' => 'focc',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Περίσταση'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "gks_orders_occasion.occasion_id = %V%",
    'vals' => array(
        array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_occasion_types.id_occasion_type Is Null"),
        array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_occasion_types.id_occasion_type<>0"),
    ),
    'sql' => "SELECT gks_orders_occasion.occasion_id as id, gks_occasion_types.occasion_type_descr as descr
FROM (gks_orders LEFT JOIN gks_orders_occasion ON gks_orders.order_occasion_id = gks_orders_occasion.id_order_occasion) 
LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type
WHERE (((gks_occasion_types.id_occasion_type) Is Not Null))
GROUP BY gks_orders_occasion.occasion_id, gks_occasion_types.occasion_type_descr
ORDER BY gks_occasion_types.occasion_type_descr",    
);
}




//$filters[] = array(
//  'name' => 'feidos',
//  'class' => 'filterselectbox',
//  'style' => '',
//  'title' => gks_lang('Είδος'),
//  'has_custom_default' => -1,
//  'multiselect' => true,
//  'field'  => "gks_orders.product_id = %V%",
//  'vals' => array(
//      array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_orders.product_id=0"),
//      array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_orders.product_id<>0"),
//  ),
//  'sql' => "SELECT gks_orders.product_id as id, gks_eshop_products.product_descr as descr
//FROM gks_orders LEFT JOIN gks_eshop_products ON gks_orders.product_id = gks_eshop_products.id_product
//WHERE (((gks_eshop_products.id_product) Is Not Null))
//GROUP BY gks_orders.product_id, gks_eshop_products.product_descr
//ORDER BY gks_eshop_products.product_descr;",    
//);








$sortable = array(
  array('name' => 'soid', 'field' => 'gks_orders.id_order'),
  array('name' => 'sood', 'field' => 'gks_orders.order_date'),
  array('name' => 'soexec', 'field' => 'gks_orders.mdate_execute'),
  array('name' => 'sopay', 'field' => 'gks_orders.mdate_payment'),
  array('name' => 'sostate', 'field' => 'gks_orders.order_state'),
  array('name' => 'souser', 'field' => 'gks_orders.user_email, '.GKS_WP_TABLE_PREFIX.'users.gks_nickname'),
  array('name' => 'soposotita', 'field' => 'gks_orders.products_posotita'),
  array('name' => 'soprice', 'field' => 'sortprice'),
  array('name' => 'sopa', 'field' => 'gks_payment_acquirers.payment_acquirer_name'),
  array('name' => 'sode', 'field' => 'gks_delivery_methods.delivery_method_name'),
  
  array('name' => 'soddate', 'field' => 'gks_orders.ddate'),
  array('name' => 'soocc', 'field' => 'gks_occasion_types.occasion_type_descr'),
  array('name' => 'souedit', 'field' => GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname'),
  array('name' => 'sotime', 'field' => 'gks_orders.production_sum_time'),
  
);
$sortable[] = array('name' => 'sopcat', 'field' => 'gks_cmsb_base_type.base_type_descr');

if ($GKS_ORDERS_PRODUCTION) {
  $sortable[] = array('name' => 'sopososto', 'field' => 'gks_orders.production_pososto, gks_orders.production_ergasies_total');
}  
            

$search_fields = array(
'gks_orders.order_state',
GKS_WP_TABLE_PREFIX.'users.gks_nickname',
GKS_WP_TABLE_PREFIX.'users.user_email',
GKS_WP_TABLE_PREFIX.'users.display_name',
GKS_WP_TABLE_PREFIX.'users.gks_mobile',
GKS_WP_TABLE_PREFIX.'users.gks_fullname',
'gks_users.eponimia',
'gks_users.title',
'gks_users.afm',
'gks_users.epaggelma',
'gks_users.ma_odos',
'gks_users.ma_perioxi',
'gks_users.ma_poli',
'gks_users.ma_tk',
'gks_users.phone_home',
GKS_WP_TABLE_PREFIX.'users_add.gks_nickname',
GKS_WP_TABLE_PREFIX.'users_edit.gks_nickname',
'gks_payment_acquirers.payment_acquirer_name',
'gks_orders.note_production',
'gks_orders.note_logistirio',



'gks_orders_occasion.title',
'gks_occasion_types.occasion_type_descr',
'gks_cmsb_base_type.base_type_descr',
'gks_orders.notes',
'gks_orders.subnotes',
'gks_orders.connect_txt_id',




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


$_gks_session['temp']['wherepivot1']=$where . $search_where;
gks_erp_cookie_save();
//echo $sql;
//die();
	
$paging = array('records' => '', 'total' => '', 'pages' => '');
$url = $_SERVER['SCRIPT_NAME'].'?';
$params='';
if (isset($filter['url']) && $filter['url']!='') $params.='&'.$filter['url'];
if (isset($sorted['url']) && $sorted['url']!='') $params.='&'.$sorted['url'];
if (isset($_GET['search_string']) && $_GET['search_string']!='') $params.='&search_string='.urlencode($_GET['search_string']);

$sortfields = explode("=", $sorted['url']);
if (count($sortfields) < 2) {
    $sortfields[0] = '';
    $sortfields[1] = '';
}

include_once('_my_header_admin.php');
?>


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>





<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>

<p>&nbsp;</p>
<?php gks_erp_app_purchase_ads_fix_970x90('pivot_a');?>
<table class="generic-table111" border="0" width="96%" cellspacing="0" cellpadding="0"  align=center>
  <tr>
    <td>
      <div id="output" style="width:100%;font-size: 0.8rem;position: relative;"><div style="text-align:center"><img src="img/wait.gif"/></div></div>
    </td>
  </tr>
</table>

<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
  <button class="btn btn-primary btn-sm" id="export_pivot_to_excel">Export σε αρχείο Excel</button>

    </div> 
  </div> 
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('pivot_b');?>

<link rel="stylesheet" type="text/css" href="/my/js/libs/c3/0.4.11/c3.min.css">
<script type="text/javascript" src="/my/js/libs/d3/3.5.5/d3.min.js"></script>
<script type="text/javascript" src="/my/js/libs/c3/0.4.11/c3.min.js"></script>
<script type="text/javascript" src="/my/js/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

<link rel="stylesheet" type="text/css" href="/my/pivottable/pivot.css"/>
<script type="text/javascript" src="/my/pivottable/pivot.js"></script>
<script type="text/javascript" src="/my/pivottable/export_renderers.js"></script>
<script type="text/javascript" src="/my/pivottable/c3_renderers.js"></script>
<script type="text/javascript" src="/my/pivottable/d3_renderers.js"></script>
<script type="text/javascript" src="/my/pivottable/pivot.el.js"></script>
<script type="text/javascript" src="/my/js/papaparse.min.js"></script>  





<?php 
//echo '<pre>';
//echo $where . $search_where;  
//echo '</pre>';
?>

  
  

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var hashchange='';


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  
  $('#fdate_add-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdate_add-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#fddate-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fddate-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_add' || sname=='fddate') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (sname == 'fdate_add' || sname=='fddate') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  });
  

  var load_action = $('#filter-form').attr('action');
  $('#filter-form').attr('action', load_action + window.location.hash);
  
  var mydata={};
  mydata.cols=['Μήνας'];
  mydata.rows=['Έτος'];
  mydata.vals=['Αξία'];
  mydata.aggregatorName='Άθροισμα';
  mydata.rendererName= 'Heatmap';
  mydata.rowOrder= 'key_a_to_z'; //"value_z_to_a", 
  mydata.colOrder= 'key_a_to_z'; //"value_z_to_a",
  
  
  if (window.location.hash!='') {
    currsearch=window.location.hash.replace('#', '');
    currsearch=decodeURI(currsearch);
    term=currsearch.substring(4);
    try {
      var mydata = JSON.parse(term);
      //console.log(mydata);
    } catch(err) {
      //console.log('error ' + err);  
    }       
  }
  
  
  var derivers = $.pivotUtilities.derivers;
  var renderers = $.extend($.pivotUtilities.renderers,$.pivotUtilities.c3_renderers,$.pivotUtilities.export_renderers);
  
  var onRefresh_count=0;

  $.ajax({
		url: '/my/admin-reports-orders-pivot-json.php',
		type: 'POST',
		cache: false,
		dataType: 'json',
		data: '',
		error : function(jqXHR ,textStatus,  errorThrown) {
			myalert('error:' + jqXHR.responseText);
		},
		success: function(data) {
			if (!data) {
				myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
			} else {
				if (data.success == true) {
          run_pivot(data.url);
				} else {
					myalert('error:' + $.base64.decode(data.message));
				}
			}
		}
	});
	
	function run_pivot(get_url) {
    Papa.parse(get_url, {
      download: true,
      skipEmptyLines: true,
      complete: function(parsed){
        //console.log(parsed.data);
        $("#output").pivotUI(parsed.data, {
          renderers: renderers,
          cols: mydata.cols, 
          rows: mydata.rows,
          vals: mydata.vals,
          aggregatorName: mydata.aggregatorName, 
          rendererName: mydata.rendererName, 
          rowOrder: mydata.rowOrder, 
          colOrder: mydata.colOrder,
          rendererOptions: {
            c3: { 
              data: {
                colors: {
                  Liberal: '#dc3912', 
                  Conservative: '#3366cc', 
                  NDP: '#ff9900',
                  Green:'#109618', 
                  'Bloc Quebecois': '#990099'
                }
              }
            }
          },
          onRefresh: function(config) {
            onRefresh_count++;
            if (onRefresh_count>1) {
              var config_copy = JSON.parse(JSON.stringify(config));
              //delete some values which are functions
              delete config_copy["aggregators"];
              delete config_copy["renderers"];
              //delete some bulky default values
              delete config_copy["rendererOptions"];
              delete config_copy["localeStrings"];
              delete config_copy["exclusions"];
              delete config_copy["inclusions"];
              delete config_copy["inclusionsInfo"];
              delete config_copy["derivedAttributes"];
              delete config_copy["hiddenAttributes"];
              delete config_copy["menuLimit"];
              delete config_copy["unusedAttrsVertical"];
              delete config_copy["autoSortUnusedAttrs"];
              delete config_copy["sorters"];
              //console.log(config_copy);
              
              myconfig = JSON.stringify(config_copy, undefined, 2);   
              myhash = 'adv=' + encodeURI(myconfig);
              document.location.hash =myhash
              hashchange=document.location.hash;
  
              $('#filter-form').attr('action', load_action + '#' + myhash);
            } 
                   
          },
        },
        false, 
        from_php_gks_pivottable_locale,
        );
      }
    });   
  }
  
  $('#export_pivot_to_excel').click(function() {
    elem=$('.pvtTable');
    if (elem.length==0) {
      myalert('error:Δεν βρέθηκαν δεδομένα');
      return;  
    }
    myhtml=elem[0].outerHTML;
    //console.log(myhtml);
    
    datasend='';
    datasend+='&source=' + encodeURIComponent($.base64.encode('orders-connect'));
    datasend+='&mytable=' + encodeURIComponent($.base64.encode(myhtml));
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-reports-orders-pivot-export-excel.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
            var downloadfile_win = window.open(data.downloadfile, '_blank');
            //console.log('data.downloadfile');
            //console.log(data.downloadfile);
            if (downloadfile_win==null) {
              myalert('ok:'+gks_lang('Το αρχείο έχει δημιουργηθεί, αλλά δεν ήταν δυνατόν να ανοίξει σε άλλη καρτέλα')+'<br>' +
              gks_lang('Μπορείτε να το ανοίξετε από τον παρακάτω σύνδεσμο')+':<br>' +
              '<a href="' + data.downloadfile + '" class="gks_link" target="_blank">'+gks_lang('σύνδεσμος')+'</a>');
            } else {
              downloadfile_win.focus();
            }
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});    
  });
  
});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


