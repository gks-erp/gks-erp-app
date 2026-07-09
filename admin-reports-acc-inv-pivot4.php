<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Αναφορά - Pivot Table - Παραστατικά');
$nav_active_array=array('accounting','accounting_inv_pivot4');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_inv_pivot4','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');


$gks_custom_prepare = gks_custom_table_item_prepare('gks_acc_inv',['from'=>'pivot']);
$user_companys=gks_get_companys_list();
include_once('admin-acc-inv_filters.php');
//$sql .= " LIMIT ". $showFrom .", " . $rows_per_page;

//echo $sql;
//die();


$_gks_session['temp']['wherepivot4']=$sql;
gks_erp_cookie_save();

//print '<pre>';print_r($_gks_session);die();
	
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
  <button class="btn btn-primary btn-sm" id="export_pivot_to_excel"><?php echo gks_lang('Export σε αρχείο Excel');?></button>

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

  $('#fprint_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fprint_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#faade_send_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#faade_send_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#fdispatch_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdispatch_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'fdate_add' || sname=='fprint_date' || sname=='faade_send_date' || sname=='fdispatch_date' || gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'fdate_add' || sname=='fprint_date' || sname=='faade_send_date' || sname=='fdispatch_date' || gks_custom_filters_date_elems.includes(sname)) {
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
  mydata.cols=[gks_lang('Μήνας')];
  mydata.rows=[gks_lang('Έτος')];
  mydata.vals=[gks_lang('Αξία','part2')];
  mydata.aggregatorName=gks_lang('Άθροισμα');
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
  var renderers = $.extend($.pivotUtilities.renderers,$.pivotUtilities.c3_renderers);
  
  var onRefresh_count=0;
  
  $.ajax({
		url: '/my/admin-reports-acc-inv-pivot4-json.php',
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
      myalert('error:'+gks_lang('Δεν βρέθηκαν δεδομένα'));
      return;  
    }
    myhtml=elem[0].outerHTML;
    //console.log(myhtml);
    
    datasend='';
    datasend+='&source=' + encodeURIComponent($.base64.encode('orders'));
    datasend+='&mytable=' + encodeURIComponent($.base64.encode(myhtml));
    
    $('body').addClass("myloading");
    $.ajax({
			url: '/my/admin-reports-acc-inv-pivot4-export-excel.php',
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


