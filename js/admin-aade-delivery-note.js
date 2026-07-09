/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

var need_save=false;
var mychange = 'change keyup paste';
var id_aade_delivery_note=0;
jQuery(document).ready(function($) {
    function docReady(fn) {
	  if (document.readyState === "complete"
	      || document.readyState === "interactive") {
	      setTimeout(fn, 1);
	  } else {
	      document.addEventListener("DOMContentLoaded", fn);
	  }
  }
  
  var lastResult=''; 	var timer_clear; var mydict=[];
  $('#scan_start').click(function() {
    $('#scan_start').prop('disabled',true);
    $('#qr-reader').show();
    docReady(function () {
      var resultContainer = document.getElementById('qr-reader-results');
      function onScanSuccess(decodedText, decodedResult) {
        
  	    if (decodedText !== lastResult) {
  	    	lastResult = decodedText;
  	    	if (mydict[decodedText] === undefined) {
  				  isnum = /^\d+$/.test(decodedText);
  				  if (isnum) {
  				    $('#input_mark').val(decodedText);
  				  }
  				  vvv=$('#input_mark').val();
  				  isnum = /^\d+$/.test(vvv);
  				  if (isnum) {
  				    $('#mark_get_qrUrl').prop('disabled', false);
  				  } else {
  				    $('#mark_get_qrUrl').prop('disabled', true);
  				  }
  				  
  				  if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
  				    $('#input_qrUrl').val(decodedText);
  				  }
  				  vvv=$('#input_qrUrl').val();
  				  if (vvv.startsWith('http://') || vvv.startsWith('https://')) {
  				    $('#qrUrl_get_mark').prop('disabled', false);
  				    $('#qrUrl_open_newtab').prop('disabled', false);
  				  } else {
  				    $('#qrUrl_get_mark').prop('disabled', true);
  				    $('#qrUrl_open_newtab').prop('disabled', true);
  				  }
  				  setTimeout(function() {
  				    $('#html5-qrcode-button-camera-stop').click();
  
  				  }, 200);
  				  $('html, body').animate({
              scrollTop: ($('#input_mark').offset().top -50)
            }, 600);
            qrUrl_get_mark_click('scan');
  	    	}
  	    	timer_clear = setTimeout(timer_clear_run, 3000);
        }
      }
      var html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
      html5QrcodeScanner.render(onScanSuccess);
    });
  });
  
  function timer_clear_run() {
		//console.log('clear ' + lastResult);  	
		lastResult='';
  }

  
  $('#input_mark').on(mychange,function() {
    vvv=$('#input_mark').val().trim();
    isnum = /^\d+$/.test(vvv);
    if (isnum) {
      $('#mark_get_qrUrl').prop('disabled', false);
    } else {
      $('#mark_get_qrUrl').prop('disabled', true);
    }
  });
  
  $('#input_qrUrl').on(mychange,function() {
    vvv=$('#input_qrUrl').val().trim();
    if (vvv.startsWith('http://') || vvv.startsWith('https://')) {
      $('#qrUrl_get_mark').prop('disabled', false);
      $('#qrUrl_open_newtab').prop('disabled', false);
    } else {
      $('#qrUrl_get_mark').prop('disabled', true);
      $('#qrUrl_open_newtab').prop('disabled', true);
    }
  });
  
  $('#qrUrl_open_newtab').click(function() {
    vvv=$('#input_qrUrl').val().trim();
    if (vvv.startsWith('http://') || vvv.startsWith('https://')) {
      window.open(vvv, '_blank');  
    }
  });  


  $('#mark_get_qrUrl').click(function() {
    //console.log('mark_get_qrUrl');  
    $('#mark_get_qrUrl_results').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    mark=$('#input_mark').val().trim();
    datasend='cmd=' + encodeURIComponent($.base64.encode('get_qrUrl'));
    datasend+='&mark=' + encodeURIComponent($.base64.encode(mark));
    datasend+='&cid=' + encodeURIComponent($.base64.encode($('#company_id_sub_id').val()));
    
    $.ajax({
			url: '/my/admin-aade-delivery-note-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#mark_get_qrUrl_results').html('');
				myalert('error:' + jqXHR.responseText);
			},
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					$('#mark_get_qrUrl_results').html('');
				} else {
				  refresh_histoty(data.id_aade_delivery_note);
					if (data.success == true) {
  					//myalert('ok:'+$.base64.decode(data.message));
  					if (data.qrUrl!='') $('#input_qrUrl').val(data.qrUrl);
  					html=gks_lang('QRCode URL')+': ' + data.qrUrl + '<br>' +
  					     gks_lang('ΑΦΜ εκδότη')+': ' + data.vat_issuer + '<br>' +
  					     gks_lang('ΑΦΜ πελάτη')+': ' + data.vat_customer;  					
  					$('#params_status_issuerVatNumber').val(data.vat_issuer);
  					$('#mark_get_qrUrl_results').html(html);  
  	        if (run_search_after_get_some) {
  	          run_search_after_get_some=false;
  					  $('#run_cmd_search').click();
  					}
  					
					} else {
					  $('#mark_get_qrUrl_results').html('');
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});    
  });  
    
  function qrUrl_get_mark_click(myfrom) {
    $('#qrUrl_get_mark_results').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    myqrUrl=$('#input_qrUrl').val().trim();
    datasend='cmd=' + encodeURIComponent($.base64.encode('get_mark'));
    datasend+='&qrUrl=' + encodeURIComponent($.base64.encode(myqrUrl));
    
    $.ajax({
			url: '/my/admin-aade-delivery-note-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#qrUrl_get_mark_results').html('');
				myalert('error:' + jqXHR.responseText);
			},
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					$('#qrUrl_get_mark_results').html('');
				} else {
				  refresh_histoty(data.id_aade_delivery_note);
					if (data.success == true) {
  					//myalert('ok:'+$.base64.decode(data.message));
  					if (data.mark!='') $('#input_mark').val(data.mark);
  					html=gks_lang('ΜΑΡΚ')+': ' + data.mark + '<br>' +
  					     gks_lang('ΑΦΜ εκδότη')+': ' + data.vat_issuer + '<br>' +
  					     gks_lang('ΑΦΜ πελάτη')+': ' + data.vat_customer;
  					$('#params_status_issuerVatNumber').val(data.vat_issuer);
  					$('#qrUrl_get_mark_results').html(html);  
  	        if (run_search_after_get_some) {
  	          run_search_after_get_some=false;
  					  $('#run_cmd_search').click();
  					}
  					
  					
					} else {
					  $('#qrUrl_get_mark_results').html('');
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});    
  }
  $('#qrUrl_get_mark').click(function() {
    qrUrl_get_mark_click('');
  });
  
  function refresh_histoty(gggg) {
    if (typeof(gggg)=== 'undefined') return;
    gggg=parseInt(gggg);
    if (isNaN(gggg)) gggg=0;
    if (gggg<=0) return;
    id_aade_delivery_note=gggg;
    setTimeout(function() {
      //$('#history_card').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
      datasend = 'cmd=' + encodeURIComponent($.base64.encode('history'));
      datasend+='&id=' + id_aade_delivery_note;
      $.ajax({
  			url: '/my/admin-aade-delivery-note-cmd.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  				$('#history_card').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + jqXHR.responseText + '</div>');
  			},
  			success: function(data) {
  				if (!data) {
					  $('#history_card').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
   				} else {
  					if (data.success == true) {
  					  $('#history_card').html($.base64.decode(data.html));
  					} else {
  					  $('#history_card').html('<div class="alert alert-danger" role="alert">' + $.base64.decode(data.message) + '</div>');
  					}
  				}
  			}
  		});      
    },1000);
  }
  
  $('#run_cmd_search').click(function() {
    mymark=$('#input_mark').val().trim();
    myqrUrl=$('#input_qrUrl').val().trim();
    if (mymark=='' && myqrUrl=='') {
      myalert('info:'+gks_lang('Πληκτρολογήστε ή σαρώστε πρώτα το ΜΑΡΚ ή το QRCode URL στο σχετικό πεδίο'));  
      return;
    }
    $('#records_html').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');

    datasend='cmd=' + encodeURIComponent($.base64.encode('get_records'));
    datasend+='&mark=' + encodeURIComponent($.base64.encode(mymark));
    datasend+='&qrUrl=' + encodeURIComponent($.base64.encode(myqrUrl));
    
    $.ajax({
			url: '/my/admin-aade-delivery-note-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#records_html').html('');
				myalert('error:' + jqXHR.responseText);
			},
			success: function(data) {
				if (!data) {
				  $('#records_html').html('');
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  refresh_histoty(data.id_aade_delivery_note);
					if (data.success == true) {
  					$('#records_html').html($.base64.decode(data.records));  
					} else {
					  $('#records_html').html('');
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});      
  });
  
    
  function gks_deltio_cmd_change() { 
    vvv=$('#gks_deltio_cmd').val();
    $('.gks_deltio_cmd_info').hide();
    $('#gks_deltio_cmd_info_' + vvv).show();
    
    $('.gks_deltio_cmd_params_status').hide();
    $('.gks_deltio_cmd_params_register').hide();
    $('.gks_deltio_cmd_params_confirm').hide();
    $('.gks_deltio_cmd_params_reject').hide();
    $('.gks_deltio_cmd_params_' + vvv).show();
    
    needs_mark=false;
    needs_qrUrl=false;
    switch (vvv) {
      case 'status':
        needs_mark=true;
        break;
      case 'register':
        needs_qrUrl=true;
        break;
      case 'confirm':
        needs_qrUrl=true;
        break;
      case 'reject':
        needs_mark=true;
        needs_qrUrl=true;
        
        break;
      default:
      
    }
    if (vvv=='reject') {
      $('#needs_mark').addClass('gks_f_optional');
      $('#needs_qrUrl').addClass('gks_f_optional');
      
    } else {
      $('#needs_mark').removeClass('gks_f_optional');
      $('#needs_qrUrl').removeClass('gks_f_optional');
      
    }
    if (needs_mark) {
      $('#needs_mark').show(); 
      $('#input_mark').prop('disabled', false);
    } else {
      $('#needs_mark').hide();
      $('#input_mark').prop('disabled', true);
    }
    if (needs_qrUrl) {
      $('#needs_qrUrl').show(); 
      $('#input_qrUrl').prop('disabled', false);
    } else {
      $('#needs_qrUrl').hide();
      $('#input_qrUrl').prop('disabled', true);
    }
    
  }
  $('#gks_deltio_cmd').change(gks_deltio_cmd_change);

  $('#run_cmd').click(gks_deltio_run_cmd);
  
  function gks_deltio_run_cmd() {
    cid=$('#company_id_sub_id').val();
    if (cid=='' || cid=='0|0') {
      myalert('info:'+gks_lang('Επιλέξτε ποια εταιρεία αφορά η συγκεκριμένη ενέργεια'));  
      return;
    }
    mycmd=$('#gks_deltio_cmd').val();
    mymark=$('#input_mark').val().trim();
    myqrUrl=$('#input_qrUrl').val().trim();
    if (mycmd=='status' && mymark=='') {
      myalert('info:'+gks_lang('Πληκτρολογήστε πρώτα το ΜΑΡΚ στο σχετικό πεδίο'));  
      return;
    } else if (['reject'].includes(mycmd)  && myqrUrl=='' && mymark=='') {
      myalert('info:'+gks_lang('Πληκτρολογήστε ή σαρώστε το QRCode URL στο σχετικό πεδίο ή πληκτρολογήστε το ΜΑΡΚ'));  
      return;
    } else if (['register','confirm'].includes(mycmd)  && myqrUrl=='') {
      myalert('info:'+gks_lang('Πληκτρολογήστε ή σαρώστε πρώτα το QRCode URL στο σχετικό πεδίο'));  
      return;
    }
    
    
    $('#records_html').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    $('#result_html').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    $('#result_raw_data_send').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    $('#result_raw_data_response').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
    
    datasend='cmd=' + encodeURIComponent($.base64.encode(mycmd));
    datasend+='&cid=' + encodeURIComponent($.base64.encode(cid));
    datasend+='&mark=' + encodeURIComponent($.base64.encode(mymark));
    datasend+='&qrUrl=' + encodeURIComponent($.base64.encode(myqrUrl));
    
    if (mycmd=='status') {
      datasend+='&issuerVatNumber=' + encodeURIComponent($.base64.encode($('#params_status_issuerVatNumber').val().trim()));
    } else if (mycmd=='register') {
      datasend+='&vehicleNumber=' + encodeURIComponent($.base64.encode($('#params_register_vehicleNumber').val().trim()));
      datasend+='&transportType=' + encodeURIComponent($.base64.encode($('#params_register_transportType').val().trim()));
      //datasend+='&timeStamp=' + encodeURIComponent($.base64.encode($('#params_register_timeStamp').val().trim()));
      datasend+='&carrierVatNumber=' + encodeURIComponent($.base64.encode($('#params_register_carrierVatNumber').val().trim()));
      datasend+='&pNumber=' + encodeURIComponent($.base64.encode($('#params_register_pNumber').val().trim()));
      datasend+='&longitude=' + encodeURIComponent($.base64.encode($('#params_register_longitude').val().trim()));
      datasend+='&latitude=' + encodeURIComponent($.base64.encode($('#params_register_latitude').val().trim()));
    } else if (mycmd=='confirm') {
      datasend+='&outcome=' + encodeURIComponent($.base64.encode($('#params_confirm_outcome').val().trim()));
      datasend+='&deliveredWithoutRecipient=' + (($('#params_confirm_deliveredWithoutRecipient').is(':checked')) ? '0':'1');

      if ($('#params_confirm_outcome').val()=='PARTIAL') {
        var dpitems=[];
        $('.deliveredPackaging_item').each(function() {
          item={};
          item.packagingType=$(this).find('.dpitem_packagingType').val();
          item.quantity=$(this).find('.dpitem_quantity').val();
          item.othertitle=$(this).find('.dpitem_othertitle').val();
          dpitems.push(item); 
        });
        dpitems_str = encodeURIComponent($.base64.encode(JSON.stringify(dpitems)));
        datasend+='&dpitems_str=' + dpitems_str; 
      }
            
    } else if (mycmd=='reject') {
      datasend+='&rejectionReason=' + encodeURIComponent($.base64.encode($('#params_reject_rejectionReason').val().trim()));


    }
    
    $('body').addClass('myloading');
    $.ajax({
			url: '/my/admin-aade-delivery-note-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('body').removeClass('myloading');
			  $('#records_html').html('');
				$('#result_html').html('');  
				$('#result_raw_data_send').html('');  
				$('#result_raw_data_response').html('');  
				myalert('error:' + jqXHR.responseText);
			},
			success: function(data) {
				$('body').removeClass('myloading');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  			  $('#records_html').html('');
					$('#result_html').html('');  
					$('#result_raw_data_send').html('');  
					$('#result_raw_data_response').html('');  
				} else {
				  refresh_histoty(data.id_aade_delivery_note);
					if (data.success == true) {
  					$('#records_html').html($.base64.decode(data.records));
  					$('#result_html').html($.base64.decode(data.html));
  					$('#result_raw_data_send').html('Raw Data Send:<br><pre>' + escapeHtml($.base64.decode(data.raw_data_send)) + '</pre>');
  					$('#result_raw_data_response').html('Raw Data Response:<br><pre>' + escapeHtml($.base64.decode(data.raw_data_response)) + '</pre>');
					  need_save=false;
					  gks_dn_reset_fields();
					} else {
						myalert('error:' + $.base64.decode(data.message));
						$('#records_html').html($.base64.decode(data.records));
						$('#result_html').html('');
						if (data.raw_data_send=='') {
						  $('#result_raw_data_send').html('');  
						} else {
						  $('#result_raw_data_send').html('Raw Data Send:<br><pre>' + escapeHtml($.base64.decode(data.raw_data_send)) + '</pre>');
						}
						if (data.raw_data_response=='') {
						  $('#result_raw_data_response').html('');  
						} else {
						  $('#result_raw_data_response').html('Raw Data Response:<br><pre>' + escapeHtml($.base64.decode(data.raw_data_response)) + '</pre>');
						}
					}
				}
			}
			
		});
		
  }
  
  function gks_dn_reset_fields() {
    //$('#params_status_issuerVatNumber').val('');
    $('#params_register_vehicleNumber').val('');
    $('#params_register_transportType').val('1');
    $('#params_register_carrierVatNumber').val('');
    $('#params_register_pNumber').val('');
    $('#params_register_longitude').val('');
    $('#params_register_latitude').val('');
    $('#params_confirm_outcome').val('FULL');
    if ($('#params_confirm_deliveredWithoutRecipient').is(':checked')) $('#params_confirm_deliveredWithoutRecipient').click();
    $('.deliveredPackaging_item').remove();
    deliveredPackaging_add();    
    $('#params_reject_rejectionReason').val('');
  }
  
  function escapeHtml(unsafe) {
    return unsafe
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  };

  $("#lightgallery_imgs").lightGallery({
  	selector: '.lightgallery_img',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });

  var elems_switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  elems_switchery1_this.forEach(function(html) {
    var switchery1_this = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
    
  $('#params_status_issuerVatNumber, #params_register_carrierVatNumber').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        return_vat_only:1,
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
    minLength: 3,
    autoFocus: true,
    delay: 300, //default
    select: function( event, ui ) {
      need_save=true;
      
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        
      }
    },
    create: function () {
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $('<li>')
          .append('<a class="gks_autocomplete_id">' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + item.descr + '</span>')
          .appendTo(ul);
      };
    },    
  });
  
//  $('#params_register_timeStamp').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
//    function(ct,$i){
//      need_save=true;
//    }
//  }));

  function params_confirm_outcome_change() {
    vvv=$('#params_confirm_outcome').val();
    if (vvv=='PARTIAL') {
      $('#params_confirm_deliveredPackaging_div').show();
    } else {
      $('#params_confirm_deliveredPackaging_div').hide();
    }
  }
  $('#params_confirm_outcome').change(params_confirm_outcome_change);
  

  function deliveredPackaging_add(add_after_dpitem=0) {
    var max_dpitem=0;
    $('.deliveredPackaging_item').each(function() {
      vvv=parseInt($(this).attr('data-dpitem'));
      if (isNaN(vvv)) vvv=0;
      if (vvv>max_dpitem) max_dpitem=vvv;
    });
    max_dpitem++;
    
    html_item=
    '<div data-dpitem="' + max_dpitem + '" class="deliveredPackaging_item form-group row">' +
      '<div class="col-4">' +
        '<select data-dpitem="' + max_dpitem + '" class="dpitem_packagingType form-control form-control-sm">';
        for (i=0;i<from_php_packagingTypes.length;i++) {
          html_item+='<option value="' + from_php_packagingTypes[i].id + '">' + from_php_packagingTypes[i].descr + '</option>'; 
        }
    html_item+=
        '</select>' +
      '</div>' +
      '<div class="col-3">' +
        '<input data-dpitem="' + max_dpitem + '" class="dpitem_quantity form-control form-control-sm" type="number" value="" placeholder="'+gks_lang('π.χ.')+' 1" autocomplete="off" min="1">' +
      '</div>' +
      '<div class="col-3">' +
        '<input data-dpitem="' + max_dpitem + '" class="dpitem_othertitle form-control form-control-sm" type="text" value="" placeholder="'+gks_lang('π.χ.')+' '+gks_lang('τσουβάλια')+'" autocomplete="off" style="display:none;">' +
      '</div>' +
      '<div class="col-2">' +
        '<div class="text-center">' +
          '<div style="width:50%;float:left;">' +
            '<i class="fas fa-trash-alt gks_delete_deliveredPackaging" data-dpitem="' + max_dpitem + '"></i>' +
          '</div>' +
          '<div style="width:50%;float:left;">' +
            '<i class="fas fa-plus-circle gks_add_deliveredPackaging" data-dpitem="' + max_dpitem + '"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
    
    
    if (add_after_dpitem==0) {
      $('#params_confirm_deliveredPackaging').append(html_item);  
    } else {
      $('.deliveredPackaging_item[data-dpitem=' + add_after_dpitem + ']').after(html_item);
    }
    
    $('.gks_add_deliveredPackaging[data-dpitem=' + max_dpitem + ']').click(gks_add_deliveredPackaging_click);
    $('.gks_delete_deliveredPackaging[data-dpitem=' + max_dpitem + ']').click(gks_delete_deliveredPackaging_click);
    $('.dpitem_packagingType[data-dpitem=' + max_dpitem + ']').change(dpitem_packagingType_change);
  }
  deliveredPackaging_add();

  
  function gks_add_deliveredPackaging_click() {
    vvv=parseInt($(this).attr('data-dpitem'));
    if (isNaN(vvv)) vvv=0; if (vvv<=0) return;
    deliveredPackaging_add(vvv);
  }
  function gks_delete_deliveredPackaging_click() {
    vvv=parseInt($(this).attr('data-dpitem'));
    if (isNaN(vvv)) vvv=0;if (vvv<=0) return;
    $('.deliveredPackaging_item[data-dpitem=' + vvv + ']').remove();
    if ($('.deliveredPackaging_item').length==0) {
      deliveredPackaging_add();
    }
  }
  function dpitem_packagingType_change() {
    vvv=parseInt($(this).attr('data-dpitem'));
    if (isNaN(vvv)) vvv=0;if (vvv<=0) return;
    kkk=parseInt($(this).val());
    if (isNaN(kkk)) kkk=0; if (kkk<=0) return; 
    if (kkk==6) {
      $('.dpitem_othertitle[data-dpitem=' + vvv + ']').show();   
    } else {
      $('.dpitem_othertitle[data-dpitem=' + vvv + ']').hide();
    }
  }
  
  params_confirm_outcome_change();
  gks_deltio_cmd_change();
  
  var run_search_after_get_some=false;
  
  if ($('#input_mark').val().trim()!='') $('#mark_get_qrUrl').prop('disabled', false);
    
  if ($('#input_qrUrl').val().trim()!='') {
    $('#qrUrl_get_mark').prop('disabled', false);
    $('#qrUrl_open_newtab').prop('disabled', false);
    if ($('#input_mark').val().trim()=='') {
      run_search_after_get_some=true;
      $('#qrUrl_get_mark').click();
    } else {
      $('#run_cmd_search').click();
    }
  } else if ($('#input_mark').val().trim()!='') {
    $('#mark_get_qrUrl').prop('disabled', false);
    if ($('#input_qrUrl').val().trim()=='') {
      run_search_after_get_some=true;
      $('#mark_get_qrUrl').click();
    } else {
      $('#run_cmd_search').click();
    }    
  }  
  
  
  //scans
  function gks_render_item(myaa,decodedText,formatName,app_id,recid) {
  	mycmdhtml='';
  	if (decodedText.startsWith('https://mydatapi.aade.gr/myDATA/TimologioQR/QRInfo') || 
  	           decodedText.startsWith('https://mydataapidev.aade.gr/TimologioQR/QRInfo')) {
  		mycmdhtml='<i class="fas fa-truck open_admin_aade_delivery_note" title="'+gks_lang('ΑΑΔΕ Ψηφιακό δελτίο αποστολής')+'"></i>';
  		mycmdhtml+=' <a href="' + decodedText + '" target="_blank"><i class="fas fas fa-external-link-alt external_link" title="'+gks_lang('Άνοιγμα συνδέσμου σε άλλη καρτέλα')+'"></i></a>';
      
  	} else if (decodedText.startsWith('https://') || 
  	           decodedText.startsWith('http://')) {
  		mycmdhtml='<a href="' + decodedText + '" target="_blank"><i class="fas fas fa-external-link-alt external_link" title="'+gks_lang('Άνοιγμα συνδέσμου σε άλλη καρτέλα')+'"></i></a>';
  	} else {
  		
  	} 
  	mycmdhtml+='<i class="fas fa-trash-alt delete_scan" title="'+gks_lang('Διαγραφή')+'"></i>';
  	
  	html='<div class="row gks_itemrow" data-aa="' + myaa + '" data-recid="' + recid + '">' +
  				'<div class="col-sm-6 decodedText">' +
  					decodedText +
  				'</div>' +
  				'<div class="col-sm-3 d-flex align-items-center justify-content-center">' +
  					'<div style="text-align:center">' + app_id + '<br>' + formatName + '</div>' + 
  				'</div>' +
  				'<div class="col-sm-3 d-flex align-items-center justify-content-end">' +
  					mycmdhtml +
  				'</div>' +
  				'<div class="col-sm-12 import_result" style="display:none;">' +
  				'</div>' +
  			'</div>';
  	
  	$('#gks_item_list').prepend(html);
  	$('.gks_itemrow[data-aa=' + myaa + '] .open_admin_aade_delivery_note').click(gks_open_admin_aade_delivery_note_click);
  	
  	$('.gks_itemrow[data-aa=' + myaa + '] .delete_scan').click(gks_delete_scan_click);
  
  	mydict[decodedText]=myaa;
	
  }

  function gks_open_admin_aade_delivery_note_click(event) {
  	aa=$(this).parent().parent().attr('data-aa'); if (isNaN(aa)) aa=0;
  	if (aa<=0) return;
  	myurl=$('.gks_itemrow[data-aa=' + aa + '] .decodedText').text();
  	if (myurl=='') return;
  	
    $('#input_mark').val('');
    $('#input_qrUrl').val(myurl);
    $('#qrUrl_get_mark').prop('disabled', false);
    $('#qrUrl_open_newtab').prop('disabled', false);
    
    qrUrl_get_mark_click('scan');
    
  }
  
  function gks_delete_scan_click() {
  	aa=$(this).parent().parent().attr('data-aa'); if (isNaN(aa)) aa=0;
  	if (aa<=0) return;
	
	  //$('.gks_itemrow[data-aa=' + aa + ']').remove();
	  gks_qrcode_cmd(aa,'delete','','');
  }
  function gks_qrcode_cmd(aa,cmd,url,format) {
	
		elem=$('.gks_itemrow[data-aa=' + aa + ']');
		aa=elem.attr('data-aa'); if (isNaN(aa)) aa=0; //if (aa<=0) return;
		recid=elem.attr('data-recid'); if (isNaN(recid)) recid=0; 
	 
	  //console.log(aa,recid);
  	datasend='';
  	datasend+='&cmd=' + encodeURIComponent($.base64.encode(cmd));
  	datasend+='&url=' + encodeURIComponent($.base64.encode(url));
  	datasend+='&format=' + encodeURIComponent($.base64.encode(format));
  	datasend+='&aa=' + encodeURIComponent(aa+'');
  	datasend+='&recid=' + encodeURIComponent(recid+'');
  	datasend+='&last_recid=' + encodeURIComponent(last_recid+'');
 
	
    $.ajax({
			url: '/my/admin-qrcode-cmd.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_cmd: cmd,
			gks_url: url,
			gks_aa: aa,
			gks_recid: recid,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
						if (this.gks_cmd=='delete') {
							$('.gks_itemrow[data-aa=' + this.gks_aa + ']').remove();
						} else if (this.gks_cmd=='add') {
							$('.gks_itemrow[data-aa=' + this.gks_aa + ']').attr('data-recid',data.ret_recid);
						} else if (this.gks_cmd=='new') {
							for(i=0; i<data.recs.length;i++) {
								if (last_recid<data.recs[i].recid) last_recid= data.recs[i].recid;
								myaa++;
								gks_render_item(myaa,data.recs[i].url,data.recs[i].format,data.recs[i].app_id,data.recs[i].recid);
								
							}
						}
						
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		
    });
	
  }
    
  $('.gks_itemrow .open_admin_aade_delivery_note').click(gks_open_admin_aade_delivery_note_click);
  $('.gks_itemrow .delete_scan').click(gks_delete_scan_click);
  
  function gks_qrcode_get_new_recs() {
	  //console.log('gks_qrcode_get_new_recs');
	  gks_qrcode_cmd(0,'new','','');
  }
  setInterval(gks_qrcode_get_new_recs, 3000);
  

  //generic
  $('.myneedsave').on('input change keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };
  need_save=false;

    
});
