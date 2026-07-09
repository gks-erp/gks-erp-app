/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


jQuery(document).ready(function($) {


  $('.mybtn_change_order_state').click(function() {    mystate($(this), from_php_to_order_state);  });

  function mystate(elem,newstate) {
    data_id=parseInt(elem.attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id<=0) return;
    
    //console.log(data_id);
    //console.log(newstate);
    
    datasend='';
    datasend+='&newstate='  + encodeURIComponent($.base64.encode(newstate));
    datasend+='&oldstate=' + encodeURIComponent($.base64.encode(from_php_from_order_state));
    for(plugin_index=0; plugin_index < gks_plugins_js_admin_production_posto_change_order_state_mysubmit_datasend.length;plugin_index++) {
      datasend+=eval(gks_plugins_js_admin_production_posto_change_order_state_mysubmit_datasend[plugin_index]+'()');
    }
    
    window.clearTimeout(timer_refresh);
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-posto-change-order-state-exec.php?id=' + data_id,
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
					  window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;    
    
    
  }
  


  var timer_refresh  = setInterval(myTimer, 100);
  var time_start = performance.now();
  var time_end = 2*60*1000; //2 lepta
  function myTimer() {
    var time_now = performance.now();
    diafora = (time_now - time_start);
    //console.log(diafora);
    
    pososto = diafora/time_end;
    pososto=(100-pososto*100);
    
    if (pososto<0) {
      pososto=0;
      window.clearTimeout(timer_refresh);
      window.location.reload();
      //window.location.href = 'admin-production-posto-run.php?id=' + from_php_posto_id;
    }
    //console.log(pososto);
    $('#psososto_refresh').css('width',pososto.formatMoney(2,'.','') + '%');
  }


  $('.order_priority').rateYo({
    fullStar: true,
    numStars: 5,
    maxValue:5,
    starWidth: '14px',
    spacing: '0px',
    readOnly: true,
  });
  

  $('#gks_filter_id').focus();
  $('#gks_filter_id').on('change keyup paste',function() {
    time_start = performance.now();
    var temp_order_id=parseInt($(this).val());
    if (isNaN(temp_order_id)) temp_order_id=0;
    
    $('.order_table_tr').each(function() {
      if (temp_order_id<=0) {
        $(this).show();
      } else {
        data_order_id=$(this).attr('data-order-id');
        if (data_order_id.includes(temp_order_id + '')) 
          $(this).show();
        else
          $(this).hide();
      }
    });
  });
  
  
  $('#checkboxall').click(function(event) {    
    var myval= $(this).is(":checked");

    $('.myccheckbox').each(function( index ) {
      $(this).prop("checked", myval);
    });    
  });
    
  var mymassaction='';
  var mymasschecks=[];    
  $('#checkboxall_run').click(function() {

    mymasschecks = [];
    $('.myccheckbox').each(function( index ) {
      if ($(this).is(":checked")) 
        mymasschecks.push($(this).attr("value"));
    });    
    if (mymasschecks.length==0) {
      myalert('error:'+gks_lang('Επιλέξτε τουλάχιστον μία εγγραφή'));
      return;   
    }
    mymassaction='complete';
    myconfirm(gks_lang('Σίγουρα θέλετε να ορίσετε την κατάσταση σε <span class="order_state_100completed">Ολοκληρωμένη</span> στις επιλεγμένες εγγραφές;'),'gks_mymass_run','','','','','','','','');
    
  });
  
  window.gks_mymass_run=function() {
    //console.log('gks_mymass_run');  
    //console.log(mymasschecks);  

    datasend='';
    datasend+='&newstate='  + encodeURIComponent($.base64.encode(from_php_to_order_state));
    datasend+='&oldstate=' + encodeURIComponent($.base64.encode(from_php_from_order_state));
    datasend+='&mymassids=' + encodeURIComponent($.base64.encode(mymasschecks.join()));

    for(plugin_index=0; plugin_index < gks_plugins_js_admin_production_posto_change_order_state_mysubmit_datasend_mass.length;plugin_index++) {
      datasend+=eval(gks_plugins_js_admin_production_posto_change_order_state_mysubmit_datasend_mass[plugin_index]+'()');
    }
    //console.log(datasend);
  
    
    $('body').addClass("myloading");
    window.clearTimeout(timer_refresh);
    $.ajax({
			url: '/my/admin-production-posto-change-order-state-exec.php',
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
					  window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
		    
  }
    
});

