/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

jQuery(document).ready(function($) {


  var control_enter_active=false;
  
  $(document).on('keypress', function(event) {
    //var tag = e.target.tagName.toLowerCase();
    
    
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      //console.log(event.ctrlKey);
      //console.log(event.which);
      event.preventDefault();
      event.stopPropagation();
      
      elem=$('#submit_button_ok_custom');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
      
    }  
    
  });
  
  $('#submit_button_ok_custom').click(function(event) {mysubmit(false); return false;});


  
  function mysubmit(preview) {
    datasend='';

    if (preview==false) {
      if ($("#print_form_descr").val().trim()=='') {
        $("#print_form_descr").focus();  
        myalert('error:'+gks_lang('Ορίστε την Περιγραφή της φόρμας εκτύπωσης'));
        return;
      }      
    }
    
    if ($("#mypostform #gks_lang").val().trim()=='') {
      $("#gks_lang").focus();  
      myalert('error:'+gks_lang('Ορίστε την Γλώσσα της φόρμας εκτύπωσης'));
      return;      
    }
    
    var mysels=$('#gks_fobjects').tagit("assignedTags");
    if (mysels.length==0) {
      
      //document.getElementById('field_gks_fobjects').scrollIntoView();
      $('#field_gks_fobjects').find('input').focus();
      myalert('error:'+gks_lang('Ορίστε σε ποια αντικείμενα μπορεί να εφαρμοστεί αυτή η εκτύπωση'));
      return;       
    }
        
    if (preview) {
      fobject_sel=parseInt($('input[name=fobject_sel]:checked').val());
      if (isNaN(fobject_sel)) fobject_sel=0; 
      if (fobject_sel<=0) {
        if ($('input[name=fobject_sel]').length>0) {
          $('input[name=fobject_sel]')[0].focus();
        } else {
          document.getElementById('fobjects_col').scrollIntoView();
        }
        myalert('error:'+gks_lang('Επιλέξτε κάποιο αντικείμενο το οποίο θα χρησιμοποιηθεί για την προεπισκόπηση'));
        return;       
      }
      datasend+='&fobject_sel='+fobject_sel;
      
      fobject_id=parseInt($('#fobject_id').val());
      if (isNaN(fobject_id)) fobject_id=0;
      datasend+='&fobject_id='  + fobject_id;
      datasend+='&createthump=' + (($('#createthump').is(':checked')) ? '1':'0');
    } 
    
    
    datasend+='&preview='  + (preview ? '1' : '0');
    datasend+='&print_form_descr='  + encodeURIComponent($.base64.encode($("#mypostform #print_form_descr").val().trim()));
    datasend+='&gks_lang='  + encodeURIComponent($.base64.encode($("#mypostform #gks_lang").val().trim()));
    
    if ($('#edit_mode_raw').prop('checked')) datasend+='&edit_mode=' + encodeURIComponent($.base64.encode('raw'));
    else datasend+='&edit_mode=' + encodeURIComponent($.base64.encode('html'));
    
    if ($('#file_type_pdf').prop('checked'))       datasend+='&file_type=' + encodeURIComponent($.base64.encode('pdf'));
    else if ($('#file_type_html').prop('checked')) datasend+='&file_type=' + encodeURIComponent($.base64.encode('html'));
    else if ($('#file_type_jpg').prop('checked'))  datasend+='&file_type=' + encodeURIComponent($.base64.encode('jpg'));
    else if ($('#file_type_raw').prop('checked'))  datasend+='&file_type=' + encodeURIComponent($.base64.encode('raw'));
    else datasend+='&file_type=';
    
    
    
    datasend+='&is_landscape='  + ($('#is_landscape_on').prop('checked') ? '1' : '0');
    datasend+='&grayscale='  + ($('#grayscale_on').prop('checked') ? '1' : '0');
    zoom_slider=parseInt($('#zoom_slider').slider('value'));
    if (isNaN(zoom_slider)) zoom_slider=100;
    datasend+='&zoom='  + encodeURIComponent(zoom_slider);
    datasend+='&logo_url='  + encodeURIComponent($.base64.encode($("#mypostform #logo_url").val().trim()));
    datasend+='&page_background_url='  + encodeURIComponent($.base64.encode($("#mypostform #page_background_url").val().trim()));
    datasend+='&page_background_opacity='  + encodeURIComponent($("#mypostform #page_background_opacity").val().trim());
    
    datasend+='&is_disable=' + (($('#is_disable').is(':checked')) ? '0':'1');
    
    datasend+='&size_name='  + encodeURIComponent($.base64.encode($("#mypostform #size_name").val().trim()));
    datasend+='&width_cm='  + encodeURIComponent($("#mypostform #width_cm").val().trim());
    datasend+='&height_cm='  + encodeURIComponent($("#mypostform #height_cm").val().trim());
    datasend+='&margin_cm_left='  + encodeURIComponent($("#mypostform #margin_cm_left").val().trim());
    datasend+='&margin_cm_right='  + encodeURIComponent($("#mypostform #margin_cm_right").val().trim());
    datasend+='&margin_cm_top='  + encodeURIComponent($("#mypostform #margin_cm_top").val().trim());
    datasend+='&margin_cm_bottom='  + encodeURIComponent($("#mypostform #margin_cm_bottom").val().trim());
    datasend+='&dpi='  + encodeURIComponent($("#mypostform #dpi").val().trim());

    datasend+='&fobjects='  + encodeURIComponent($.base64.encode($("#mypostform #gks_fobjects").val().trim()));
    datasend+='&perm_company_ids='  + encodeURIComponent($.base64.encode($("#mypostform #perm_company_ids").val().trim()));
    datasend+='&perm_acc_journal_ids='  + encodeURIComponent($.base64.encode($("#mypostform #perm_acc_journal_ids").val().trim()));
    datasend+='&perm_acc_seires_ids='  + encodeURIComponent($.base64.encode($("#mypostform #perm_acc_seires_ids").val().trim()));
    

    if (gks_curr_tinymce_running==false) {
      datasend+='&page_header='  + encodeURIComponent($.base64.encode($('#page_header').val()));
      datasend+='&form_header='  + encodeURIComponent($.base64.encode($('#form_header').val()));
      datasend+='&details_header='  + encodeURIComponent($.base64.encode($('#details_header').val()));
      datasend+='&details_body='  + encodeURIComponent($.base64.encode($('#details_body').val()));
      datasend+='&details_footer='  + encodeURIComponent($.base64.encode($('#details_footer').val()));
      datasend+='&form_footer='  + encodeURIComponent($.base64.encode($('#form_footer').val()));
      datasend+='&page_footer='  + encodeURIComponent($.base64.encode($('#page_footer').val()));
      datasend+='&fpa_analysis='  + encodeURIComponent($.base64.encode($('#fpa_analysis').val()));
      datasend+='&foroi_analysis='  + encodeURIComponent($.base64.encode($('#foroi_analysis').val()));
      datasend+='&lots_and_serials_analysis='  + encodeURIComponent($.base64.encode($('#lots_and_serials_analysis').val()));
      datasend+='&eidoi_optional='  + encodeURIComponent($.base64.encode($('#eidoi_optional').val()));
    } else {
      datasend+='&page_header='  + encodeURIComponent($.base64.encode(tinyMCE.get('page_header').getContent()));
      datasend+='&form_header='  + encodeURIComponent($.base64.encode(tinyMCE.get('form_header').getContent()));
      datasend+='&details_header='  + encodeURIComponent($.base64.encode(tinyMCE.get('details_header').getContent()));
      datasend+='&details_body='  + encodeURIComponent($.base64.encode(tinyMCE.get('details_body').getContent()));
      datasend+='&details_footer='  + encodeURIComponent($.base64.encode(tinyMCE.get('details_footer').getContent()));
      datasend+='&form_footer='  + encodeURIComponent($.base64.encode(tinyMCE.get('form_footer').getContent()));
      datasend+='&page_footer='  + encodeURIComponent($.base64.encode(tinyMCE.get('page_footer').getContent()));
      datasend+='&fpa_analysis='  + encodeURIComponent($.base64.encode(tinyMCE.get('fpa_analysis').getContent()));
      datasend+='&foroi_analysis='  + encodeURIComponent($.base64.encode(tinyMCE.get('foroi_analysis').getContent()));
      datasend+='&lots_and_serials_analysis='  + encodeURIComponent($.base64.encode(tinyMCE.get('lots_and_serials_analysis').getContent()));
      datasend+='&eidoi_optional='  + encodeURIComponent($.base64.encode(tinyMCE.get('eidoi_optional').getContent()));
    }

    datasend+='&custom_css=' + encodeURIComponent($.base64.encode(custom_css_editor.getValue()));
    datasend+='&custom_javascript=' + encodeURIComponent($.base64.encode(custom_javascript_editor.getValue()));

    datasend+='&sortorder='  + encodeURIComponent(($("#mypostform #sortorder").val().trim()));
    
    var loc_langs=[];
    $('.local_set_lang').each(function() {
      loc_lang=$(this).val().trim();
      if (loc_lang!='') {
        data_id=parseInt($(this).attr('data-id'));
        if (isNaN(data_id)) data_id=0;
        loc_form_id=parseInt($('.local_set_form[data-id=' + data_id + ']').attr('data-form_id'));
        if (isNaN(loc_form_id)) loc_form_id=0;
        if (loc_form_id>0) {
          loc_langs.push({lang: loc_lang,form_id:  loc_form_id});
        }
      }
      
    });
    //console.log(loc_langs);    
    datasend+='&loc_langs=' + encodeURIComponent($.base64.encode(JSON.stringify(loc_langs)));
    //console.log(datasend);    
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-print_forms-item-exec.php?id=' + from_php_id,
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
					    if (createthump.isChecked()) $('#createthump').click();
					    
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
              
              if (data.file_thump_url!='') {
                $('#img_file_thump_url').attr('src',data.file_thump_url);
                
              }
              	    
					  } else {
              need_save=false;
              if (data.redirect=='') {
    					  window.location.reload();
    					} else {
    					  window.location.href = $.base64.decode(data.redirect);
    					}
    				}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }

  var zoom_slider_handle = $('#zoom_slider_handle');
  $('#zoom_slider').slider({
    min: 10,
    max: 200,
    value: from_php_zoom*100,
    create: function() {
      zoom_slider_handle.text( $( this ).slider('value') + '%');
    },
    slide: function( event, ui ) {
      zoom_slider_handle.text( ui.value + '%' );
      need_save=true;
    }
  });
  
  $('#size_name').change(function() {
    val=$(this).val();
    if (val=='') {
      $('#width_cm').prop('disabled', false);
      $('#height_cm').prop('disabled', false);
    } else {
      $('#width_cm').prop('disabled', true);
      $('#height_cm').prop('disabled', true);
      data_width=parseFloat($('#size_name option:selected').attr('data-width'));
      data_height=parseFloat($('#size_name option:selected').attr('data-height'));
      if (isNaN(data_width)) data_width=10;
      if (isNaN(data_height)) data_height=10;
      $('#width_cm').val(data_width);
      $('#height_cm').val(data_height);
    }  
  });
  


  $('#gks_fobjects').tagit({
    allowSpaces: true, 
    singleFieldDelimiter: ']][[',
    removeConfirmation: true,
    availableTags: gks_fobjects_tags,
    showAutocompleteOnFocus : true,
    afterTagAdded:function()   {fobjects_col_update();need_save=true;},
    afterTagRemoved:function() {fobjects_col_update();need_save=true;},
    preprocessTag: function(val) {
      if (!val) { return ''; }
  		if ($.inArray(val, gks_fobjects_tags) >= 0) {
  		  return val;
  		  //$('#Owners').tagit('createTag', data.out[0].value);
  		} else {
  		  myalert('error:'+gks_lang('Επιλέξτε κάποιο από τα διαθέσιμα'));
  		  return '';
  		}
    },
    
  });
  
  
  function fobjects_col_update() {
    if (gks_page_loading) return;
    var mysels=$('#gks_fobjects').tagit("assignedTags");
    console.log(mysels);
//    $('.div_fobject').each(function() {
//      data_val=$(this).attr('data-val');
//      if (mysels.includes(data_val)==false) $(this).remove();
//    });
    $('.div_fobject').remove();
    
    //var mysels_c=$('input[name=fobject_sel]:checked').length;
    fovc=0;
    for (i=0; i < fobjects_max_ids.length;i++) {
      if (mysels.includes(fobjects_max_ids[i].descr)) {
        fovc++;
        htmladd='<div class="div_fobject" '+
          'data-id="'+fobjects_max_ids[i].id+'" '+
          'data-ctid="'+fobjects_max_ids[i].ctid+'" >'+
          '<input type="radio" name="fobject_sel" '+
          'value="'+fobjects_max_ids[i].id+'" '+
          'id="id_print_object_'+fobjects_max_ids[i].id+'" '+
          (fovc==1 ? 'checked' : '')+'> '+
          '<label for="id_print_object_'+fobjects_max_ids[i].id+'">'+fobjects_max_ids[i].descr+'</label></div>';
        $('#fobjects_col').append(htmladd);
        $('#id_print_object_'+fobjects_max_ids[i].id).click(fobject_sel_click);
        if (fovc==1) $('#fobject_id').val(fobjects_max_ids[i].maxid);
      }
      
    }
  }
  
 
  for(i=0;i<fobjects_max_ids.length;i++) {
    fobjects_max_ids[i].descr=$.base64.decode(fobjects_max_ids[i].descr);
  }
  //console.log(fobjects_max_ids);

  function fobject_sel_click() {
    val=parseInt($(this).val()); if (isNaN(val)) val=0;
    //console.log(val);
    for (i=0; i<fobjects_max_ids.length;i++) {
      if (fobjects_max_ids[i].id==val) {
        $('#fobject_id').val(fobjects_max_ids[i].maxid);
        break;
      }
    }
  }
  
  $('input[name=fobject_sel]').click(fobject_sel_click);

  var createthump = new Switchery(document.querySelector('#createthump'),gks_switchery_defaults());
  
  $('input[name=edit_mode]').click(function() {
    d=$('input[name=edit_mode]:checked').val();
    if (d === undefined || d === null) d='';
    from_php_edit_mode=d;
    if (from_php_edit_mode=='raw' || from_php_file_type=='raw') {
      if (gks_curr_tinymce_running) gks_curr_tinymce_destroy();
    } else {
      if (gks_curr_tinymce_running==false) gks_curr_tinymce_init();
    }
  });
  
  
  $('input[name=file_type]').click(function() {
    d=$('input[name=file_type]:checked').val();
    if (d === undefined || d === null) d='';
    from_php_file_type=d;
    if (from_php_edit_mode=='raw' || from_php_file_type=='raw') {
      if (gks_curr_tinymce_running) gks_curr_tinymce_destroy();
    } else {
      if (gks_curr_tinymce_running==false) gks_curr_tinymce_init();
    }
    
    if (from_php_file_type=='pdf') {
      createthump.enable();
    } else {
      createthump.disable();
    }
    
    if (from_php_file_type=='raw') {
      $('input[name=edit_mode]').prop('disabled', true);
      $('#edit_mode_div').addClass('edit_mode_div_disabled');
    } else {
      $('input[name=edit_mode]').prop('disabled', false);
      $('#edit_mode_div').removeClass('edit_mode_div_disabled');
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
        text: gks_lang('Προεπισκόπηση'),
        icon: "ui-icon-print",  
        click: function() {
          mysubmit(true);
        }
      },
      {
        id: "dialog_print_cancel",
        text: gks_lang('Κλείσιμο'),
        icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
  });
  
  $('#submit_button_preview').click(function(event)   {
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 600) dwidth=600;
	  if (dheight> 400) dheight=400;
	  dialog_print.dialog('option', 'width', dwidth);
	  dialog_print.dialog('option', 'height', dheight);
	  $('#dialog_print').parent().css({position:'fixed'});      
    dialog_print.dialog('open');
    
  });
  
  $('#gks_lang').change(function() {
    mytext=$(this).find('option:selected').text();
    //console.log(mytext);
    $('#local_set_lang_current').html(mytext);  
    local_set_lang_fix();
  });
  function local_set_aa() {
    var temp=1;
    $('.local_set_aa').each(function() {
      temp++;
      $(this).html(temp);
    });  
  }  
  
  function local_set_index_remove_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id<=0) return;
    $('.local_set_tr[data-id=' + data_id + ']').remove();
    local_set_lang_fix();
    local_set_aa();
  }
  $('.local_set_index_remove').click(local_set_index_remove_click);

  function local_set_lang_change() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id<=0) return;
    $('.local_set_form[data-id=' + data_id + ']').val('');
    $('.local_set_form_link[data-id=' + data_id + ']').hide().attr('href','#');
    local_set_lang_fix();
  }
  $('.local_set_lang').change(local_set_lang_change);
  

  
  $('#add_local_set').click(function() {
    var local_set_index=1;
    $('.local_set_tr').each(function() {
      data_id=parseInt($(this).attr('data-id'));
      if (isNaN(data_id)) data_id=0;
      if (data_id>local_set_index) local_set_index=data_id;
    });    
    local_set_index++;
    
    myhtml='';
    myhtml+='<tr class="local_set_tr" data-id="' + local_set_index + '">' +
      '<th scope="row" nowrap class="mytdcm local_set_aa">' + local_set_index + '</td>'+
      '<td nowrap class="mytdcm"><i class="fas fa-trash-alt local_set_index_remove" data-id="' + local_set_index + '"></i></td>'+
      '<td nowrap class="mytdcm">'+
        '<select class="form-control form-control-sm myneedsave local_set_lang" style="width:100%;" data-id="' + local_set_index + '">'+
//          '<option value=""></option>' +
        '</select>' +
      '</td>' +
      '<td nowrap class="mytdcm">' +
        '<input type="text" class="form-control form-control-sm myneedsave local_set_form" value="" ' +
        'data-id="' + local_set_index + '" ' +
        'style="width:calc(100% - 22px);display:inline;" ' +
        'placeholder="'+gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες')+'" ' +
        'data-form_id="0"> ' +
        '<a class="local_set_form_link" data-id="' + local_set_index + '" href="" tabindex="-1" style="display:none;">' +
          '<i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="'+gks_lang('Προβολή Φόρμας')+'"></i>' +
        '</a>' +
      '</td>' +
    '</tr>';
    
    $('#tr_new_button').before(myhtml);
    $('#gks_lang > option').each(function() {
      mytext=$(this).text();
      myval=$(this).attr('value');
      
      $('.local_set_lang[data-id=' + local_set_index + ']').append(new Option(mytext, myval));
    });    
    
    $('.local_set_index_remove[data-id=' + local_set_index + ']').click(local_set_index_remove_click);
    $('.local_set_lang[data-id=' + local_set_index + ']').change(local_set_lang_change);
    local_set_form_autocomplete($('.local_set_form[data-id=' + local_set_index + ']'));
    
    local_set_lang_fix();
    local_set_aa();
  })
  

  function local_set_form_autocomplete(myelem) {
    myelem.autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: "admin-autocomplete-print_form.php",
          dataType: "json",
          data: {
            term: request.term,
            lang_id: $('.local_set_lang[data-id=' + $(this)[0].element.attr('data-id') + ']').val(),
            gks_fobjects: $('#gks_fobjects').val(),
            notid:from_php_id,
            anddisabled: 1,
          },
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
      delay: 300, //default
      autoFocus: true,
      select: function( event, ui ) {
        need_save=true;
        $(this).attr('data-form_id',ui.item.id);
        data_id=parseInt($(this).attr('data-id'));
        if (isNaN(data_id)) data_id=0;
        $('.local_set_form_link[data-id=' + data_id + ']').show().attr('href','admin-print_forms-item.php?id=' + ui.item.id);
      },
      change: function (event, ui) {
        need_save=true;
        if(!ui.item){
          $(this).val('').attr('data-form_id','0');
          data_id=parseInt($(this).attr('data-id'));
          if (isNaN(data_id)) data_id=0;
          $('.local_set_form_link[data-id=' + data_id + ']').hide().attr('href','#');
        }
      }
    });
    
  }
  $('.local_set_form').each(function() {
    local_set_form_autocomplete($(this));
  });  
  
  function local_set_lang_fix() {
    var main_lang=$('#gks_lang').val().trim();
    var other_langs=[];
    $('.local_set_lang').each(function() {
      data_id=parseInt($(this).attr('data-id'));
      if (isNaN(data_id)) data_id=0;
      this_val=$(this).val().trim();
      if (main_lang!='' && this_val == main_lang) {
        $(this).val('');
        $('.local_set_form[data-id=' + data_id + ']').val('').attr('data-form_id','0');
        $('.local_set_form_link[data-id=' + data_id + ']').hide().attr('href','#');
      }
      $(this).find('option').each(function() {
        if (main_lang!='' && $(this).attr('value')==main_lang) {
          $(this).prop('disabled', true);
        } else {
          $(this).prop('disabled', false);
        }
      });
      if (this_val!='') other_langs.push(this_val);
    });
    
    //console.log(other_langs);
    
    $('.local_set_lang').each(function() {
      var this_val=$(this).val().trim();
      $(this).find('option').each(function() {
        opt_val=$(this).attr('value');
        if (this_val!= opt_val && other_langs.includes(opt_val)) {
          $(this).prop('disabled', true);
        }
      });
    });
    
  }
  local_set_lang_fix();
  
  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });  
  


  $('#perm_company_ids').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: perm_company_ids_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});
  $('#perm_acc_journal_ids').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: perm_acc_journal_ids_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});
  $('#perm_acc_seires_ids').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: perm_acc_seires_ids_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});
  
  
  
  $('#submit_button_export').click(function() {
    mydatajson={};
    mydatajson.app='gks ERP';
    mydatajson.exporturl=window.location.href;
    d = new Date();
    d = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+ d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
    mydatajson.exportdate= d;
    mydatajson.filetype='gks_print_forms';
    mydatajson.form={};
    mydatajson.form.id=from_php_id;
    mydatajson.form.print_form_descr=$('#print_form_descr').val();
    mydatajson.form.is_disable=(($('#is_disable').is(':checked')) ? 0:1);
    mydatajson.form.sortorder=parseInt($("#mypostform #sortorder").val().trim());
    mydatajson.form.gks_lang=$("#mypostform #gks_lang").val();
    if ($('#file_type_pdf').prop('checked'))       mydatajson.form.file_type='pdf';
    else if ($('#file_type_html').prop('checked')) mydatajson.form.file_type='html';
    else if ($('#file_type_jpg').prop('checked'))  mydatajson.form.file_type='jpg';
    else if ($('#file_type_raw').prop('checked'))  mydatajson.form.file_type='raw';
    else mydatajson.form.file_type='';

    mydatajson.form.grayscale=($('#grayscale_on').prop('checked') ? 1 : 0);
    zoom_slider=parseInt($('#zoom_slider').slider('value'));
    if (isNaN(zoom_slider)) zoom_slider=100;
    mydatajson.form.zoom=zoom_slider;
    mydatajson.form.dpi=parseFloat($("#mypostform #dpi").val().trim());
    mydatajson.form.size_name=$("#mypostform #size_name").val().trim();
    mydatajson.form.width_cm=parseFloat($("#mypostform #width_cm").val().trim());
    mydatajson.form.height_cm=parseFloat($("#mypostform #height_cm").val().trim());
    mydatajson.form.is_landscape=($('#is_landscape_on').prop('checked') ? 1 : 0);
    mydatajson.form.margin_cm_left=parseFloat($("#mypostform #margin_cm_left").val().trim());
    mydatajson.form.margin_cm_right=parseFloat($("#mypostform #margin_cm_right").val().trim());
    mydatajson.form.margin_cm_top=parseFloat($("#mypostform #margin_cm_top").val().trim());
    mydatajson.form.margin_cm_bottom=parseFloat($("#mypostform #margin_cm_bottom").val().trim());
    mydatajson.form.logo_url=$("#mypostform #logo_url").val().trim();
    mydatajson.form.fobjects=$("#mypostform #gks_fobjects").val().trim();
    mydatajson.form.page_background_url=$("#mypostform #page_background_url").val().trim()
    mydatajson.form.page_background_opacity=parseFloat($("#mypostform #page_background_opacity").val().trim());

    mydatajson.form.perm_company_ids=$("#mypostform #perm_company_ids").val().trim();
    mydatajson.form.perm_acc_journal_ids=$("#mypostform #perm_acc_journal_ids").val().trim();
    mydatajson.form.perm_acc_seires_ids=$("#mypostform #perm_acc_seires_ids").val().trim();
    if ($('#edit_mode_raw').prop('checked')) mydatajson.form.edit_mode='raw'; else mydatajson.form.edit_mode='html';
        
    if (gks_curr_tinymce_running==false) {
      mydatajson.form.page_header=$.base64.encode($('#page_header').val());
      mydatajson.form.form_header=$.base64.encode($('#form_header').val());
      mydatajson.form.details_header=$.base64.encode($('#details_header').val());
      mydatajson.form.details_body=$.base64.encode($('#details_body').val());
      mydatajson.form.details_footer=$.base64.encode($('#details_footer').val());
      mydatajson.form.form_footer=$.base64.encode($('#form_footer').val());
      mydatajson.form.page_footer=$.base64.encode($('#page_footer').val());
      mydatajson.form.fpa_analysis=$.base64.encode($('#fpa_analysis').val());
      mydatajson.form.foroi_analysis=$.base64.encode($('#foroi_analysis').val());
      mydatajson.form.lots_and_serials_analysis=$.base64.encode($('#lots_and_serials_analysis').val());
      mydatajson.form.eidoi_optional=$.base64.encode($('#eidoi_optional').val());
    } else {
      mydatajson.form.page_header=$.base64.encode(tinyMCE.get('page_header').getContent());
      mydatajson.form.form_header=$.base64.encode(tinyMCE.get('form_header').getContent());
      mydatajson.form.details_header=$.base64.encode(tinyMCE.get('details_header').getContent());
      mydatajson.form.details_body=$.base64.encode(tinyMCE.get('details_body').getContent());
      mydatajson.form.details_footer=$.base64.encode(tinyMCE.get('details_footer').getContent());      
      mydatajson.form.form_footer=$.base64.encode(tinyMCE.get('form_footer').getContent());
      mydatajson.form.page_footer=$.base64.encode(tinyMCE.get('page_footer').getContent());
      mydatajson.form.fpa_analysis=$.base64.encode(tinyMCE.get('fpa_analysis').getContent());
      mydatajson.form.foroi_analysis=$.base64.encode(tinyMCE.get('foroi_analysis').getContent());
      mydatajson.form.lots_and_serials_analysis=$.base64.encode(tinyMCE.get('lots_and_serials_analysis').getContent());
      mydatajson.form.eidoi_optional=$.base64.encode(tinyMCE.get('eidoi_optional').getContent());
    }
    
    mydatajson.form.custom_css=$.base64.encode(custom_css_editor.getValue());
    mydatajson.form.custom_javascript=$.base64.encode(custom_javascript_editor.getValue());
    

    
    //console.log(mydatajson);return;
    
    const blob = new Blob([JSON.stringify(mydatajson, null, 2)], {type:'application/json'});
    const url = URL.createObjectURL(blob); 
    const a=document.createElement('a'); 
    a.href=url; 
    a.download='gks_ERP_form_' + from_php_id + '_' + $('#print_form_descr').val() + '_' + d.replaceAll(':','_').replaceAll(' ','_').replaceAll('-','_') + '.json'; 
    a.click(); 
    URL.revokeObjectURL(url);
  });
  
  $('#submit_button_import').click(function() {
    $('#submit_button_import_file').val('').click();
  });
  $('#submit_button_import_file').change(function(ev) {
    //console.log(ev);
    const f = ev.target.files && ev.target.files[0]; 
    if(!f) return;
    const reader = new FileReader(); 
    reader.onload = ()=> {
      try{ 
        const mydatajson = JSON.parse(reader.result);
        if (typeof(mydatajson)=='object' &&
            typeof(mydatajson.app)=='string' && mydatajson.app=='gks ERP' &&
            typeof(mydatajson.filetype)=='string' && mydatajson.filetype=='gks_print_forms' &&
            typeof(mydatajson.form)=='object') {
          const myf=mydatajson.form;
          if (typeof(myf.print_form_descr)=='string') $('#mypostform #print_form_descr').val(myf.print_form_descr);
          if (typeof(myf.is_disable)=='number') {
            if (($('#mypostform #is_disable').is(':checked')?0:1)!=myf.is_disable) $('#mypostform #is_disable').click();
          }
          if (typeof(myf.sortorder)=='number') $("#mypostform #sortorder").val(myf.sortorder);
          if (typeof(myf.gks_lang)=='string' && $('#mypostform #gks_lang option[value="'+ myf.gks_lang +'"]').length==1) $("#mypostform #gks_lang").val(myf.gks_lang);
          if (typeof(myf.file_type)=='string') $('#file_type_'+myf.file_type).click();
          if (typeof(myf.grayscale)=='number') if (myf.grayscale==0) $('#grayscale_off').click(); else $('#grayscale_on').click();
          if (typeof(myf.zoom)=='number') {
            $('#zoom_slider').slider('value',myf.zoom);
            zoom_slider_handle.text(myf.zoom + '%');  
          }
          if (typeof(myf.dpi)=='number') $('#mypostform #dpi').val(myf.dpi);
          if (typeof(myf.size_name)=='string' && $('#mypostform #size_name option[value="'+ myf.size_name +'"]').length==1) {
            $('#mypostform #size_name').val(myf.size_name).change();
          }
          if ($('#mypostform #size_name').val()=='') {
            if (typeof(myf.width_cm)=='number') $('#mypostform #width_cm').val(myf.width_cm);
            if (typeof(myf.height_cm)=='number') $('#mypostform #height_cm').val(myf.height_cm);
          }
          if (typeof(myf.is_landscape)=='number') if (myf.is_landscape==0) $('#is_landscape_off').click(); else $('#is_landscape_on').click();
          if (typeof(myf.margin_cm_left)=='number') $('#mypostform #margin_cm_left').val(myf.margin_cm_left);
          if (typeof(myf.margin_cm_right)=='number') $('#mypostform #margin_cm_right').val(myf.margin_cm_right);
          if (typeof(myf.margin_cm_top)=='number') $('#mypostform #margin_cm_top').val(myf.margin_cm_top);
          if (typeof(myf.margin_cm_bottom)=='number') $('#mypostform #margin_cm_bottom').val(myf.margin_cm_bottom);
          if (typeof(myf.logo_url)=='string') $('#mypostform #logo_url').val(myf.logo_url);
          if (typeof(myf.fobjects)=='string') {
            $('#mypostform #gks_fobjects').tagit('removeAll');
            vvv=myf.fobjects.split(']][[');
            for(ivv=0;ivv<vvv.length;ivv++) {
              $('#mypostform #gks_fobjects').tagit('createTag',vvv[ivv]);
            }
          }
          if (typeof(myf.page_background_url)=='string') $('#mypostform #page_background_url').val(myf.page_background_url);
          if (typeof(myf.page_background_opacity)=='number') $('#mypostform #page_background_opacity').val(myf.page_background_opacity);
          if (typeof(myf.perm_company_ids)=='string') {
            $('#mypostform #perm_company_ids').tagit('removeAll');
            vvv=myf.perm_company_ids.split(']][[');
            for(ivv=0;ivv<vvv.length;ivv++) {
              $('#mypostform #perm_company_ids').tagit('createTag',vvv[ivv]);
            }
          }
          if (typeof(myf.perm_acc_journal_ids)=='string') {
            $('#mypostform #perm_acc_journal_ids').tagit('removeAll');
            vvv=myf.perm_acc_journal_ids.split(']][[');
            for(ivv=0;ivv<vvv.length;ivv++) {
              $('#mypostform #perm_acc_journal_ids').tagit('createTag',vvv[ivv]);
            }
          }
          if (typeof(myf.perm_acc_seires_ids)=='string') {
            $('#mypostform #perm_acc_seires_ids').tagit('removeAll');
            vvv=myf.perm_acc_seires_ids.split(']][[');
            for(ivv=0;ivv<vvv.length;ivv++) {
              $('#mypostform #perm_acc_seires_ids').tagit('createTag',vvv[ivv]);
            }
          }

          $('#edit_mode_raw').prop('checked',true).click();
          if (typeof(myf.page_header)=='string') $('#page_header').val($.base64.decode(myf.page_header));
          if (typeof(myf.form_header)=='string') $('#form_header').val($.base64.decode(myf.form_header));
          if (typeof(myf.details_header)=='string') $('#details_header').val($.base64.decode(myf.details_header));
          if (typeof(myf.details_body)=='string') $('#details_body').val($.base64.decode(myf.details_body));
          if (typeof(myf.details_footer)=='string') $('#details_footer').val($.base64.decode(myf.details_footer));
          if (typeof(myf.form_footer)=='string') $('#form_footer').val($.base64.decode(myf.form_footer));
          if (typeof(myf.page_footer)=='string') $('#page_footer').val($.base64.decode(myf.page_footer));
          if (typeof(myf.fpa_analysis)=='string') $('#fpa_analysis').val($.base64.decode(myf.fpa_analysis));
          if (typeof(myf.foroi_analysis)=='string') $('#foroi_analysis').val($.base64.decode(myf.foroi_analysis));
          if (typeof(myf.lots_and_serials_analysis)=='string') $('#lots_and_serials_analysis').val($.base64.decode(myf.lots_and_serials_analysis));
          if (typeof(myf.eidoi_optional)=='string') $('#eidoi_optional').val($.base64.decode(myf.eidoi_optional));
            
          if (typeof(myf.edit_mode)!='string') myf.edit_mode='raw';
          if (myf.file_type=='raw') myf.edit_mode='raw';
          $('#edit_mode_' + myf.edit_mode).prop('checked',true).click();
          
          if (typeof(myf.custom_css)=='string')         custom_css_editor.setValue($.base64.decode(myf.custom_css));
          if (typeof(myf.custom_javascript)=='string')  custom_javascript_editor.setValue($.base64.decode(myf.custom_javascript));
          
          
          need_save=true;
          
          myalert('ok:'+gks_lang('Η εισαγωγή ήταν επιτυχής'));
        } else {
          myalert('error:'+gks_lang('Το αρχείο δεν έχει τα αναμενόμενα δεδομένα'));
        }
        
      } catch(e){
        myalert('error:'+gks_lang('Σφάλμα ανάγνωσης αρχείου'));
      }
    }; 
    reader.readAsText(f);    
        
  });

  var custom_css_editor = CodeMirror.fromTextArea(document.getElementById("custom_css"), {
    lineNumbers: true,
    extraKeys: {
      "Ctrl-Space": "autocomplete",
      Tab: function(cm) {
        var spaces = Array(cm.getOption("indentUnit") + 1).join(" ");
        cm.replaceSelection(spaces);
      },
      "F11": function(cm) {
        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
      },
      "Esc": function(cm) {
        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
      }      
    },
    mode: {name: "css",globalVars: true }, //
    lineNumbers: true,
    spellcheck: true,
    autocorrect: true,
    autocapitalize: true,
    indentUnit:2,
    tabSize: 2,
    indentWithTabs:false,
    smartIndent:true,
    autoCloseBrackets: true,
    styleActiveLine: true,
    lineWrapping: true,
    foldGutter: true,
    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
    //viewportMargin: 20, //Infinity
  });
  
  var custom_javascript_editor = CodeMirror.fromTextArea(document.getElementById("custom_javascript"), {
    lineNumbers: true,
    extraKeys: {
      "Ctrl-Space": "autocomplete",
      Tab: function(cm) {
        var spaces = Array(cm.getOption("indentUnit") + 1).join(" ");
        cm.replaceSelection(spaces);
      },
      "F11": function(cm) {
        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
      },
      "Esc": function(cm) {
        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
      }      
    },
    mode: {name: "javascript",globalVars: true }, //
    lineNumbers: true,
    spellcheck: false,
    autocorrect: false,
    autocapitalize: false,
    indentUnit:2,
    tabSize: 2,
    indentWithTabs:false,
    smartIndent:true,
    autoCloseBrackets: true,
    styleActiveLine: true,
    lineWrapping: true,
    foldGutter: true,
    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
    matchBrackets:true,
    //viewportMargin: 20, //Infinity
  });  
  
  
  //generic
  gks_page_loading=false;
  



  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;     
  
});


var gks_curr_tinymce_running=false;
   
function gks_curr_tinymce_init() { 
  gks_curr_tinymce_running=true;
  //console.log('gks_curr_tinymce_init');
  tinymce.init({
    language: from_php_gks_tinymce_locale,
    entity_encoding : 'raw',
    forced_root_block:false, 
    remove_trailing_brs: false,
    theme: 'silver', 
    browser_spellcheck: true,
    plugins: 'autoresize print preview  searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount imagetools textpattern help code',
    toolbar: 'undo redo formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
    menubar:true,
    statusbar: true,
    contextmenu: '', //gia na gine disable to default
    templates: [],
    content_css: [],
    content_style: '.mce-content-body {font-size:12px;font-family:"Open Sans",sans-serif;}',
    relative_urls : true,
    convert_urls: true,
    document_base_url : (window.location.origin + '/'),
    min_height: 200,
  
    selector: '.gks_curr_tinymce',
    init_instance_callback: function(editor) {
      editor.on('Change', function(e) {
        need_save=true;
      });
      //editor.execCommand('mceAutoResize');
    },
    readonly : (from_php_perm_ret_edit ? 0 : 1),
  });
}
function gks_curr_tinymce_destroy() {  
  gks_curr_tinymce_running=false;
  //console.log('gks_curr_tinymce_destroy');
  tinyMCE.get('page_header').destroy();
  tinyMCE.get('form_header').destroy();
  tinyMCE.get('details_header').destroy();
  tinyMCE.get('details_body').destroy();
  tinyMCE.get('details_footer').destroy();
  tinyMCE.get('form_footer').destroy();
  tinyMCE.get('page_footer').destroy();
  tinyMCE.get('fpa_analysis').destroy();
  tinyMCE.get('foroi_analysis').destroy();
  tinyMCE.get('lots_and_serials_analysis').destroy();
  tinyMCE.get('eidoi_optional').destroy();
}

if (!(from_php_edit_mode=='raw' || from_php_file_type=='raw')) {
  gks_curr_tinymce_init();
}


tinymce.init({
  language: from_php_gks_tinymce_locale,
  entity_encoding : 'raw',
  forced_root_block:false, 
  remove_trailing_brs: false,
  theme: 'silver', 
  browser_spellcheck: true,
  plugins: 'autoresize print preview  searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount imagetools textpattern help code',
  toolbar: 'undo redo formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
  menubar:true,
  statusbar: true,
  contextmenu: '', //gia na gine disable to default
  templates: [],
  content_css: [],
  content_style: '.mce-content-body {font-size:12px;font-family:"Open Sans",sans-serif;}',
  relative_urls : true,
  convert_urls: true,
  document_base_url : (window.location.origin + '/'),
  min_height: 200,

  selector: '.gks_tinymce',
  init_instance_callback: function(editor) {
    editor.on('Change', function(e) {
      need_save=true;
    });
    //editor.execCommand('mceAutoResize');
  },
  readonly : (from_php_perm_ret_edit ? 0 : 1),
});
