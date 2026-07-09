<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
?>

  
  $('#search_string').keyup(function(event){ 
    if (event.which == 13) {
      if (jQuery('#search_string').val() != '<?php if (isset($_GET['search_string'])) echo $_GET['search_string']; ?>') {
        //jQuery('#filter-form').submit();
      }
    }
  });
 
<?php
if (isset($_GET['search_string'])) echo "$('#search_string').focus();$('#search_string')[0].setSelectionRange(0," . mb_strlen($_GET['search_string']) . ");"; 
?>

<?php 
	if (isset($filter['script']) and $filter['script']!='') {
	  echo $filter['script'];
	}
?> 

  $('.submit_button_back').click(function() {
<?php if ((isset($_gks_session['gks']['recordback']) and $_gks_session['gks']['recordback']!='') and
          (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']!='' and endwith($_SERVER['HTTP_REFERER'], '?id=-1')) ) { ?>
    window.location.href='<?php echo $_gks_session['gks']['recordback'];?>';
<?php } else if (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER']!='' and endwith($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI']) == false ) { ?>
    window.location.href='<?php echo $_SERVER['HTTP_REFERER'];?>';
<?php } else { ?>
    window.location.href='<?php
  if (endwith($_SERVER['SCRIPT_NAME'],'-item.php')) {
    echo substr($_SERVER['SCRIPT_NAME'], 0,strlen($_SERVER['SCRIPT_NAME'])-9) . '.php';
  } else {
    echo '/';  
  }
  ?>';
<?php } ?>
  });

  $('#submit_button_ok').click(function(event) {  
    mysubmit();
    return false;
  });

  var gks_custom_filters_date_elems=[];

<?php 
if (isset($gks_custom_prepare) and isset($gks_custom_prepare['sql_filters_date_elems'])) {
  foreach ($gks_custom_prepare['sql_filters_date_elems'] as $value) {
    echo '  gks_custom_filters_date_elems.push(\''.$value.'\');'."\n";
  }
  foreach ($gks_custom_prepare['sql_filters_date_elems'] as $value) {
    echo  '  $(\'#'.$value.'-from\').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:\'39/19/9999\',format:\'d/m/Y\', timepicker:false,dayOfWeekStart:1,}));'."\n";
    echo  '  $(\'#'.$value.'-to\').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:\'39/19/9999\',format:\'d/m/Y\', timepicker:false,dayOfWeekStart:1,}));'."\n";
  }
}

gks_plugins_functions_run('_dialogs_js_after',array(
  
));
