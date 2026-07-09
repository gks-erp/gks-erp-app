/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


jQuery(document).ready(function($) {
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  }); 
  
  $('input[name=datecheck]').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,})); 
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });  



  var dialog_print_zoom_slider_handle = $('#dialog_print_zoom_slider_handle');
  $('#dialog_print_zoom_slider').slider({
    min: 10,
    max: 200,
    value: 100,
    create: function() {
      dialog_print_zoom_slider_handle.text( $( this ).slider('value') + '%');
    },
    slide: function( event, ui ) {
      dialog_print_zoom_slider_handle.text( ui.value + '%' );
    }
  });
    
  
  var dialog_print;
  dialog_print = $('#dialog_print').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_print_preview",
        html: '<i class="fa fa-print"></i> ' + gks_lang('Προεπισκόπηση'),
        //icon: "ui-icon-print",  
        click: function() {
          dialog_print_button(true);
        }
      },
      {
        id: "dialog_print_ok",
        html: '<i class="fa fa-print"></i> ' + gks_lang('Εκτύπωση'),
        //icon: "ui-icon-print",
        click: function() {
          dialog_print_button(false);
        }
      },
      {
        id: "dialog_print_cancel",
        html: '<i class="fa fa-window-close"></i> ' + gks_lang('Κλείσιμο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
    create: function( event, ui ) {
      $('#dialog_print_preview').before(
        '<span class="ui-button ui-corner-all-1 ui-widget" style="margin-right: 20px;">' +
        '<input type="checkbox" id="dialog_print_set_def" style="cursor:pointer;vertical-align: top;"> ' +
        '<label for="dialog_print_set_def" style="cursor:pointer;margin: 0px;margin-right: 20px;">' + gks_lang('Αποθήκευση ως προεπιλογές') + '</label>' +
        '</span>');
      
    },
    
  });
  
  function dialog_print_button(ispreview) {
    
    from_php_print_def_file_type='pdf';
    if ($('#dialog_print_file_type_pdf').prop('checked')) from_php_print_def_file_type='pdf';
    else if ($('#dialog_print_file_type_html').prop('checked')) from_php_print_def_file_type='html';
    else if ($('#dialog_print_file_type_jpg').prop('checked')) from_php_print_def_file_type='jpg';
    
    if ($('#dialog_print_grayscale_on').prop('checked')) from_php_print_def_grayscale=true;
    else if ($('#dialog_print_grayscale_off').prop('checked')) from_php_print_def_grayscale=false;
    
    if ($('#dialog_print_landscape_on').prop('checked')) from_php_print_def_landscape=true;
    else if ($('#dialog_print_landscape_off').prop('checked')) from_php_print_def_landscape=false;
    
    from_php_print_def_zoom=parseInt($('#dialog_print_zoom_slider').slider('value'));
    if (isNaN(from_php_print_def_zoom)) from_php_print_def_zoom=100;
    
    datasend='';
    datasend+='&file_type=' + from_php_print_def_file_type;
    datasend+='&grayscale=' + (from_php_print_def_grayscale ? '1' : '0');
    datasend+='&landscape=' + (from_php_print_def_landscape ? '1' : '0');
    datasend+='&zoom=' + from_php_print_def_zoom;
    datasend+='&preview=' + (ispreview ? '1' : '0');
    datasend+='&set_def=' + ($('#dialog_print_set_def').is(':checked') ? '1' : '0');
    
    if ($('#gks_paroxos_send_pdf').length>0) {
      datasend+='&gks_paroxos_send_pdf=' + ($('#gks_paroxos_send_pdf').is(':checked') ? '1' : '0');  
    }
    if ($('#gks_print_send_gks_erp_app').length>0) {
      datasend+='&gks_print_send_gks_erp_app=' + ($('#gks_print_send_gks_erp_app').is(':checked') ? '1' : '0');  
    }
    
    form_id=0;
    elem_sel=$('.gks_print_thump_div_selected');
    if (elem_sel.length<=0) {
      myalert('error:' + gks_lang('Επιλέξτε την φόρμα εκτύπωσης'));
      return;
    }
    form_id=parseInt(elem_sel.attr('data-form_id'));
    if (isNaN(form_id)) form_id=0;
    datasend+='&form_id=' + form_id;
    
    
    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    $.ajax({
			url: 'admin-products-pdf.php' + window.location.search,
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
					  if (data.preview_url!='') {
					   
              var preview_win = window.open(data.preview_url, '_blank');
              //console.log('preview_win');
              //console.log(preview_win);
              if (preview_win==null) {
                myalert('ok:'+gks_lang('Η προεπισκόπηση έχει δημιουργηθεί, αλλά δεν ήταν δυνατόν να ανοίξει σε άλλη καρτέλα')+'<br>' +
                gks_lang('Μπορείτε να το ανοίξετε από τον παρακάτω σύνδεσμο')+':<br>' +
                '<a href="' + data.preview_url + '" class="gks_link" class="gks_">'+gks_lang('σύνδεσμος')+'</a>');
              } else {
                preview_win.focus();
              }
        
					    
					  } else {
    					if (data.save_but_message!='') {
    					  if ($.base64.decode(data.message)=='ok') {
    					    myalert('ok:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
    					    
    					  } else {
    					    myalert('error:' + $.base64.decode(data.save_but_message), $.base64.decode(data.redirect),true);
    					  }
    					} else {
      					if (data.redirect=='') {
      					  window.location.reload();
      					} else {
      					  window.location.href = $.base64.decode(data.redirect);
      					}
      				}
      			}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
  }
  
  
  $('#submit_button_print').click(function() {

    $('#dialog_print_preview').prop('checked', false);
    if (from_php_print_def_file_type=='pdf') $('#dialog_print_file_type_pdf').prop('checked', true);
    else if (from_php_print_def_file_type=='html') $('#dialog_print_file_type_html').prop('checked', true);
    else if (from_php_print_def_file_type=='jpg') $('#dialog_print_file_type_jpg').prop('checked', true);
    if (from_php_print_def_grayscale) $('#dialog_print_grayscale_on').prop('checked', true);
    else $('#dialog_print_grayscale_off').prop('checked', true);
    if (from_php_print_def_landscape) $('#dialog_print_landscape_on').prop('checked', true);
    else $('#dialog_print_landscape_off').prop('checked', true);
    $('#dialog_print_zoom_slider').slider('option', 'value', from_php_print_def_zoom);
    dialog_print_zoom_slider_handle.text($('#dialog_print_zoom_slider').slider('value') + '%');
    
    if (from_php_print_def_file_type=='pdf') {
      $('#dialog_print_zoom_slider').slider('enable');
    } else if (from_php_print_def_file_type=='html') {
      $('#dialog_print_zoom_slider').slider('disable');
    } else if (from_php_print_def_file_type=='jpg') {
      $('#dialog_print_zoom_slider').slider('enable');
    }
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 850) dwidth=850;
	  if (dheight> 650) dheight=650;
	  dialog_print.dialog('option', 'width', dwidth);
	  dialog_print.dialog('option', 'height', dheight);
	  $('#dialog_print').parent().css({position:'fixed'});    
    dialog_print.dialog('open'); 
    
    $('.gks_print_thump_div_selected').removeClass('gks_print_thump_div_selected');
    
    sel_company_id_sub_id=$('#company_id_sub_id').val();
    sel_inv_acc_journal_id=parseInt($('#inv_acc_journal_id').val()); if (isNaN(sel_inv_acc_journal_id)) sel_inv_acc_journal_id=0;
    sel_inv_acc_seira_id=parseInt($('#inv_acc_seira_id').val()); if (isNaN(sel_inv_acc_seira_id)) sel_inv_acc_seira_id=0;
    
    var temp=$('#dr_user_lang').val();
    if (typeof(temp)=='undefined' || temp==null || temp=='') temp='el-GR';
    var temp_cc=0;
    var temp_hh=0;
    if (temp!='') {
      $('.gks_print_thump_div').each(function() {
        if ($(this).attr('data-lang')==temp) {
          data_form_id=parseInt($(this).attr('data-form_id')); if (isNaN(data_form_id)) data_form_id=0;
          will_show=true;
          for (i=0;i<from_php_perm_print_forms.length;i++) {
            if (from_php_perm_print_forms[i].id == data_form_id) {
              if (typeof(from_php_perm_print_forms[i].perm_company_ids) != 'undefined') {
                if (from_php_perm_print_forms[i].perm_company_ids.includes(sel_company_id_sub_id)==false) {
                  will_show=false;
                  break;
                }
              }
              if (typeof(from_php_perm_print_forms[i].perm_acc_journal_ids) != 'undefined') {
                if (from_php_perm_print_forms[i].perm_acc_journal_ids.includes(sel_inv_acc_journal_id)==false) {
                  will_show=false;
                  break;
                }
              }
              if (typeof(from_php_perm_print_forms[i].perm_acc_seires_ids) != 'undefined') {
                if (from_php_perm_print_forms[i].perm_acc_seires_ids.includes(sel_inv_acc_seira_id)==false) {
                  will_show=false;
                  break;
                }
              }
            }
          }
          
          if (will_show) {
            $(this).show();
            temp_cc++;
          } else {
            $(this).hide();
          }
        } else {
          $(this).hide();
          temp_hh++;
        }
      });
//      if (temp_cc==0) {
//        $('.gks_print_thump_div').show();
//      }
            
      if (from_php_print_def_forms[temp]!==undefined && $('.gks_print_thump_div[data-form_id=' + from_php_print_def_forms[temp] + ']').length>0) {
        if ($('.gks_print_thump_div[data-form_id=' + from_php_print_def_forms[temp] + ']').css('display')!='none') {
          $('.gks_print_thump_div[data-form_id=' + from_php_print_def_forms[temp] + ']').addClass('gks_print_thump_div_selected');
        }
      }

      if ($('.gks_print_thump_div_selected').length==0) {
        temp_cc=0;
        $('.gks_print_thump_div').each(function() {
          if ($(this).css('display')!='none') temp_cc++;
        });
        if (temp_cc==1) {
          $('.gks_print_thump_div').each(function() {
            if ($(this).css('display')!='none') {
              $(this).addClass('gks_print_thump_div_selected');
              return; 
            }
          });          
        }
      }
      
      temp_cc=0;
      $('.gks_print_thump_div').each(function() {
        if ($(this).css('display')=='none') temp_cc++;
      });
      
      if (temp_cc==0) {
        $('#gks_print_thump_more_div').hide();
      } else {
        $('#gks_print_thump_more_div').show();
      }
      
    }
    
    fff=$('.gks_print_thump_div_selected');
    if (fff.length>0) {
      //document.getElementById('gggggg').scrollIntoView({block: "nearest",inline : 'nearest'});
      fff[0].scrollIntoView({block: "nearest",inline : 'nearest'});
    }
    
  });    
  
  $('#gks_print_thump_more_div').click(function() {
    $('.gks_print_thump_div').show();
    $('#gks_print_thump_more_div').hide();
  });
  

  
  $('input[name=dialog_print_file_type]').click( function() {
    val=$(this).val();
    //console.log(val);
    if (val==1) {
      $('#dialog_print_zoom_slider').slider('enable');
    } else if (val==2) {
      $('#dialog_print_zoom_slider').slider('disable');
    } else if (val==3) {
      $('#dialog_print_zoom_slider').slider('enable');
    }
  });
  
  
  $('.gks_print_thump_div').click(function() {
    form_id=parseInt($(this).attr('data-form_id'));
    if (isNaN(form_id)) form_id=0;
    if (form_id<=0) return;
    $('.gks_print_thump_div').each(function() {
      $(this).removeClass('gks_print_thump_div_selected');  
    });
    $(this).addClass('gks_print_thump_div_selected');
    
    val=$(this).attr('data-file_type');
    if (val=='pdf') $('#dialog_print_file_type_pdf').click();
    else if (val=='html') $('#dialog_print_file_type_html').click();
    else if (val=='jpg') $('#dialog_print_file_type_jpg').click();
   
    val=$(this).attr('data-landscape');
    if (val=='0') $('#dialog_print_landscape_off').click();
    else if (val=='1') $('#dialog_print_landscape_on').click();
    
    val=$(this).attr('data-grayscale');
    if (val=='0') $('#dialog_print_grayscale_off').click();
    else if (val=='1') $('#dialog_print_grayscale_on').click();
    
    val=$(this).attr('data-zoom');
    $('#dialog_print_zoom_slider').slider('option', 'value', val);
    dialog_print_zoom_slider_handle.text($('#dialog_print_zoom_slider').slider('value') + '%');
    
    
    
    
  });
  
});
