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
      if ($("#email_template_descr").val().trim()=='') {
        $("#email_template_descr").focus();  
        myalert('error:'+gks_lang('Ορίστε την Περιγραφή'));
        return;
      }      
    }
    
    if ($("#mypostform #gks_lang").val().trim()=='') {
      $("#gks_lang").focus();  
      myalert('error:'+gks_lang('Ορίστε την Γλώσσα'));
      return;      
    }
    
    
    if (preview) {
      fobject_sel=$('input[name=fobject_sel]:checked').val();
      if (fobject_sel === undefined || fobject_sel === null) fobject_sel='';
      if (fobject_sel=='') {
        if ($('input[name=fobject_sel]').length>0) {
          $('input[name=fobject_sel]')[0].focus();
        } else {
          document.getElementById('fobjects_col').scrollIntoView();
        }
        myalert('error:'+gks_lang('Επιλέξτε κάποιο αντικείμενο το οποίο θα χρησιμοποιηθεί για την προεπισκόπηση'));
        return;       
      }
      datasend+='&fobject_sel='  + encodeURIComponent($.base64.encode(fobject_sel));
      
      fobject_id=parseInt($('#fobject_id').val());
      if (isNaN(fobject_id)) fobject_id=0;
      datasend+='&fobject_id='  + fobject_id;
      
      
    } 
    
    
    datasend+='&email_template_descr='  + encodeURIComponent($.base64.encode($("#mypostform #email_template_descr").val().trim()));
    datasend+='&gks_lang='  + encodeURIComponent($.base64.encode($("#mypostform #gks_lang").val().trim()));
    
    if ($('#edit_mode_raw').prop('checked')) datasend+='&edit_mode=' + encodeURIComponent($.base64.encode('raw'));
    else datasend+='&edit_mode=' + encodeURIComponent($.base64.encode('html'));
    
    
    
    datasend+='&email_subject='  + encodeURIComponent($.base64.encode($("#mypostform #email_subject").val().trim()));
    
    datasend+='&is_disable=' + (($('#is_disable').is(':checked')) ? '0':'1');
    datasend+='&need_attachments=' + (($('#need_attachments').is(':checked')) ? '1':'0');
    

    datasend+='&fobjects='  + encodeURIComponent($.base64.encode($("#mypostform #gks_fobjects").val().trim()));


    if (gks_curr_tinymce_running==false) {
      datasend+='&email_body='  + encodeURIComponent($.base64.encode($('#email_body').val()));
      datasend+='&email_message='  + encodeURIComponent($.base64.encode($('#email_message').val()));
    } else {
      datasend+='&email_body='  + encodeURIComponent($.base64.encode(tinyMCE.get('email_body').getContent()));
      datasend+='&email_message='  + encodeURIComponent($.base64.encode(tinyMCE.get('email_message').getContent()));
    }

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
    
    
    var parameterline_array=[];
    $('.gks_fparam_label').each(function() {
      bb=parseInt($(this).attr('data-bb'));
      if (isNaN(bb)) bb=0;        
      if (bb>0) {
        
        fparam_label=$('.gks_fparam_label[data-bb=' + bb + ']').val();
        fparam_type=$('.gks_fparam_type[data-bb=' + bb + ']').val();
        fparam_px=$('.gks_fparam_px[data-bb=' + bb + ']').val();
        fparam_icon=$('.gks_fparam_icon[data-bb=' + bb + ']').val();
        fparam_value=$('.gks_fparam_value[data-bb=' + bb + ']').val();
        fparam_jquery_selector=$('.gks_fparam_jquery_selector[data-bb=' + bb + ']').val();

        
        item={};
        item.aa=bb;
        item.label=fparam_label;
        item.type=fparam_type;
        item.px=fparam_px;
        item.icon=fparam_icon;
        item.value=fparam_value;
        item.jquery_selector=fparam_jquery_selector;
        
        parameterline_array.push(item);
      }
    });

    parameterline_array_str = encodeURIComponent($.base64.encode(JSON.stringify(parameterline_array)));
    datasend+='&parameterline_array_str=' + parameterline_array_str;       
    //console.log(parameterline_array);
    
    var attach_paramline_array=[];
    $('.gks_attach_param_basefolder').each(function() {
      ap=parseInt($(this).attr('data-ap'));
      if (isNaN(ap)) ap=0;        
      if (ap>0) {
        
        aparam_basefolder=$('.gks_attach_param_basefolder[data-ap=' + ap + ']').val();
        aparam_relative_path=$('.gks_attach_param_relative_path[data-ap=' + ap + ']').val();
        aparam_name_for_email=$('.gks_attach_param_name_for_email[data-ap=' + ap + ']').val();
        aparam_def_check=$('.gks_attach_param_def_check[data-ap=' + ap + ']').is(':checked') ? 1:0; 
        
        item={};
        item.aa=ap;
        item.basefolder=aparam_basefolder;
        item.relative_path=aparam_relative_path;
        item.name_for_email=aparam_name_for_email;
        item.def_check=aparam_def_check;

        attach_paramline_array.push(item);
      }
    });

    attach_paramline_array_str = encodeURIComponent($.base64.encode(JSON.stringify(attach_paramline_array)));
    datasend+='&attach_paramline_array_str=' + attach_paramline_array_str;       
    //console.log(attach_paramline_array);
 
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-email-templates-item-exec.php?id=' + from_php_id,
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

  
 
  
 

  $('#gks_fobjects').tagit({
    allowSpaces: true, 
    singleFieldDelimiter: ']][[',
    removeConfirmation: true,
    availableTags: gks_fobjects_tags,
    showAutocompleteOnFocus : true,

    preprocessTag: function(val) {
      if (!val) { return ''; }
  		if ($.inArray(val, gks_fobjects_tags) >= 0) {
  		  return val;
  		  //$('#aaaaaaaaaaa').tagit('createTag', data.out[0].value);
  		} else {
  		  myalert('error:'+gks_lang('Επιλέξτε κάποιο από τα διαθέσιμα'));
  		  return '';
  		}
    },
    
  });
  
  

   




  $('input[name=edit_mode]').click(function() {
    d=$('input[name=edit_mode]:checked').val();
    if (d === undefined || d === null) d='';
    from_php_edit_mode=d;
    if (from_php_edit_mode=='raw') {
      if (gks_curr_tinymce_running) gks_curr_tinymce_destroy();
    } else {
      if (gks_curr_tinymce_running==false) gks_curr_tinymce_init();
    }
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
          url: "admin-autocomplete-email-template.php",
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
        $('.local_set_form_link[data-id=' + data_id + ']').show().attr('href','admin-email-templates-item.php?id=' + ui.item.id);
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
  
  
  
  //parameterline
  function next_enter_parameterline_field_fnc(bb,fieldfrom,faultback) {
    //console.log('next_enter_field_fnc',bb,fieldfrom,faultback);
    
    if (control_enter_active) return;
    for (i=0; i<from_php_enter_parameter_order.length; i++) {
      if (from_php_enter_parameter_order[i]==fieldfrom) {
        if (i < (from_php_enter_parameter_order.length - 1)) {
          if (from_php_enter_parameter_order[i+1]=='new_row') {
            elemnext=$('.gks_fparam_label[data-bb=' + (bb+1) + ']');
            if (elemnext.length>0) {
              elem=$('.' + from_php_enter_parameter_order[0] + '[data-bb=' + (bb+1) + ']');
              if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
              else elem.focus().select();
            } else {
              $('.gks_add_parameterline[data-bb=' + bb + ']').click();
            }
          } else {
            elem=$('.' + from_php_enter_parameter_order[i+1] + '[data-bb=' + bb + ']');
            if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
            else elem.focus().select();
          }
          return;
        }
      }
    }
    if (faultback=='new_row') {
      elemnext=$('.gks_fparam_label[data-bb=' + (bb+1) + ']');
      if (elemnext.length>0) {
        if (from_php_enter_parameter_order.length>0) {
          elemnextuo=$('.' + from_php_enter_parameter_order[0] + '[data-bb=' + (bb+1) + ']');
          if (elemnextuo.prop('nodeName')=='TEXTAREA') elemnextuo.focus();
          else elemnextuo.focus().select();
        } else {
//          elemnext_set=$('.gks_set[data-bb=' + (bb+1) + ']');
//          if (elemnext_set.length>0) {
//            if (elemnext_set.prop('nodeName')=='TEXTAREA') elemnext_set.focus();
//            else elemnext_set.focus().select();
//          } else {
            if (elemnext.prop('nodeName')=='TEXTAREA') elemnext.focus();
            else elemnext.focus().select();
//          }
        }
      } else {
        $('.gks_add_parameterline[data-bb=' + bb + ']').click();
      }
    } else if (faultback!='') {
      elem=$('.' + faultback + '[data-bb=' + bb + ']');
      if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
      else elem.focus().select();
    }
  }  
  function gks_fparam_label_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        bb=parseInt($(this).attr('data-bb'));
        if (isNaN(bb)) bb=0;
        if (bb<=0) return;
        next_enter_parameterline_field_fnc(bb,'gks_fparam_label','gks_fparam_type');
        return;
      }
    }
  }
  $('.gks_fparam_label').keyup(gks_fparam_label_keyup);    
  
  function gks_fparam_type_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        bb=parseInt($(this).attr('data-bb'));
        if (isNaN(bb)) bb=0;
        if (bb<=0) return;
        next_enter_parameterline_field_fnc(bb,'gks_fparam_type','gks_fparam_px');
        return;
      }
    }
  }
  $('.gks_fparam_type').keyup(gks_fparam_type_keyup);    
  
  function gks_fparam_px_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        bb=parseInt($(this).attr('data-bb'));
        if (isNaN(bb)) bb=0;
        if (bb<=0) return;
        next_enter_parameterline_field_fnc(bb,'gks_fparam_px','gks_fparam_icon');
        return;
      }
    }
  }
  $('.gks_fparam_px').keyup(gks_fparam_px_keyup);  
  
  function gks_fparam_icon_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        bb=parseInt($(this).attr('data-bb'));
        if (isNaN(bb)) bb=0;
        if (bb<=0) return;
        next_enter_parameterline_field_fnc(bb,'gks_fparam_icon','gks_fparam_value');
        return;
      }
    }
  }
  $('.gks_fparam_icon').keyup(gks_fparam_icon_keyup);  

  function gks_fparam_value_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        bb=parseInt($(this).attr('data-bb'));
        if (isNaN(bb)) bb=0;
        if (bb<=0) return;
        next_enter_parameterline_field_fnc(bb,'gks_fparam_value','gks_fparam_jquery_selector');
        return;
      }
    }
  }
  $('.gks_fparam_value').keyup(gks_fparam_value_keyup);  

  function gks_fparam_jquery_selector_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        bb=parseInt($(this).attr('data-bb'));
        if (isNaN(bb)) bb=0;
        if (bb<=0) return;
        next_enter_parameterline_field_fnc(bb,'gks_fparam_jquery_selector','new_row');
        return;
      }
    }
  }
  $('.gks_fparam_jquery_selector').keyup(gks_fparam_jquery_selector_keyup);  


  
  
  

  


  function gks_delete_parameterline_click() {
    need_save=true;
    bb=parseInt( $(this).attr('data-bb'));
    if (isNaN(bb)) bb=0;
    if (bb<=0) return;
    $('.gks_parameter_line[data-bb=' + bb +']').remove(); 
    
    if ($('.gks_parameter_line').length ==0) {
      parameterline_add(false,0);  
    }
   
    gks_myscroll();
  }
  $('.gks_delete_parameterline').click(gks_delete_parameterline_click);   


  function parameterline_add(fromloading,click_bb) {
    //console.log('click_bb',click_bb);
    need_save=true;
    last_bb++;
    
    row_html=
    
    '<div class="form-group row gks_parameter_line" data-bb="' + last_bb + '">' +
      '<div class="' + from_php_gkscols_parameter1 + '">' +
        '<input type="text" class="form-control form-control-sm gks_fparam_label" data-bb="' + last_bb + '" value=""/>' +
      '</div>' +
      
      '<div class="' + from_php_gkscols_parameter2 + '">' +
        '<select class="form-control form-control-sm myneedsave gks_select2 gks_fparam_type" data-bb="' + last_bb + '">' +
        '<option selected value="text">'+gks_lang('Κείμενο')+'</option>' +
        '<option value="textarea">'+gks_lang('Μεγάλο κείμενο')+'</option>' +
        '</select>'+
      '</div>' +
      '<div class="' + from_php_gkscols_parameter3 + '">' +
        '<input type="text" class="form-control form-control-sm gks_fparam_px" data-bb="' + last_bb + '" value=""/>' +
      '</div>' +
      '<div class="' + from_php_gkscols_parameter4 + '">' +
        '<input type="text" class="form-control form-control-sm gks_fparam_icon" data-bb="' + last_bb + '" value=""/>' +
      '</div>' +
      '<div class="' + from_php_gkscols_parameter5 + '">' +
        '<input type="text" class="form-control form-control-sm gks_fparam_value" data-bb="' + last_bb + '" value=""/>' +
      '</div>' +
      '<div class="' + from_php_gkscols_parameter6 + '">' +
        '<input type="text" class="form-control form-control-sm gks_fparam_jquery_selector" data-bb="' + last_bb + '" value=""/>' +
      '</div>' +
 
       
      '<div class="' + from_php_gkscols_parameter7 + '">' +
        '<div class="text-center gks_parameter_icons">' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-trash-alt gks_delete_parameterline" data-bb="' + last_bb + '"></i>' +
          '</div>' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-arrows-alt-v sortorder_parameterline_handle"></i>' +
          '</div>' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-plus-circle gks_add_parameterline"  data-bb="' + last_bb + '"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
      
    '</div>';
    
    
    if (click_bb<=0) {
      $('#gks_parameter_footer1').before(row_html);
    } else {
      $('.gks_parameter_line[data-bb=' + click_bb + ']').after(row_html);
    }
    
    $('.gks_add_parameterline').show();  
    $('.gks_delete_parameterline').show();  
    

    
    $('.gks_fparam_label[data-bb=' + last_bb + ']').keyup(gks_fparam_label_keyup);
    $('.gks_fparam_type[data-bb=' + last_bb + ']').keyup(gks_fparam_type_keyup);
    $('.gks_fparam_px[data-bb=' + last_bb + ']').keyup(gks_fparam_px_keyup);
    $('.gks_fparam_icon[data-bb=' + last_bb + ']').keyup(gks_fparam_icon_keyup);
    $('.gks_fparam_value[data-bb=' + last_bb + ']').keyup(gks_fparam_value_keyup);
    $('.gks_fparam_jquery_selector[data-bb=' + last_bb + ']').keyup(gks_fparam_jquery_selector_keyup);
    

    
    $('.gks_add_parameterline[data-bb=' + last_bb + ']').click(function() {gks_add_parameterline_click(false,$(this));});
    $('.gks_delete_parameterline[data-bb=' + last_bb + ']').click(gks_delete_parameterline_click); //.hide();
    

    if (fromloading==false) {
      if (from_php_enter_parameter_order.length>0) {
        $('.' + from_php_enter_parameter_order[0] + '[data-bb=' + last_bb + ']').focus().select();
      } else {
        $('.gks_fparam_label[data-bb=' + last_bb + ']').focus().select();
      }
    }
    
    if (click_bb>0) {
      var mylist=[];
      $('.gks_parameter_line').each(function() {
        mylist.push($(this).attr('data-bb'));
      });
      parameters_table_sortable_after(mylist);
    }
    
    gks_myscroll();
  }  
  
  

  function gks_add_parameterline_click(fromloading,elem) {
    bb=elem.attr('data-bb');
    parameterline_add(fromloading,bb);
  }
  
  $('.gks_add_parameterline').click(function() {gks_add_parameterline_click(false,$(this));});

  $('#parameters_table').sortable({
    items: '.gks_parameter_line',
    handle: '.sortorder_parameterline_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-bb'});
      parameters_table_sortable_after(mylist);
    }
  });
  
  function parameters_table_sortable_after(mylist) {
      //console.log(mylist);
      $('#parameters_table > .gks_parameter_line').each(function() {
        aa=$(this).attr('data-bb');
        $(this).attr('data-bb_temp',aa);
      });
      $('#parameters_table > .gks_parameter_line').each(function() {
        bb=$(this).attr('data-bb_temp');
        new_bb=-1;
        for(i=0;i<mylist.length;i++) {
          if (mylist[i]==bb) {
            new_bb=i;break;
          }
        }
        //console.log('new_aa',new_aa);
        if (new_bb>=0) {
          new_bb++
          $(this).attr('data-bb',new_bb);
          $(this).find('*[data-bb=' + bb + ']').attr('data-bb',new_bb);
        }
        
      })      
    
  }  

  
  


  //attach_paramline
  function next_enter_attach_paramline_field_fnc(ap,fieldfrom,faultback) {
    //console.log('next_enter_field_fnc',ap,fieldfrom,faultback);
    
    if (control_enter_active) return;
    for (i=0; i<from_php_enter_attach_param_order.length; i++) {
      if (from_php_enter_attach_param_order[i]==fieldfrom) {
        if (i < (from_php_enter_attach_param_order.length - 1)) {
          if (from_php_enter_attach_param_order[i+1]=='new_row') {
            elemnext=$('.gks_attach_param_basefolder[data-ap=' + (ap+1) + ']');
            if (elemnext.length>0) {
              elem=$('.' + from_php_enter_attach_param_order[0] + '[data-ap=' + (ap+1) + ']');
              if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
              else elem.focus().select();
            } else {
              $('.gks_add_attach_paramline[data-ap=' + ap + ']').click();
            }
          } else {
            elem=$('.' + from_php_enter_attach_param_order[i+1] + '[data-ap=' + ap + ']');
            if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
            else elem.focus().select();
          }
          return;
        }
      }
    }
    if (faultback=='new_row') {
      elemnext=$('.gks_attach_param_basefolder[data-ap=' + (ap+1) + ']');
      if (elemnext.length>0) {
        if (from_php_enter_attach_param_order.length>0) {
          elemnextuo=$('.' + from_php_enter_attach_param_order[0] + '[data-ap=' + (ap+1) + ']');
          if (elemnextuo.prop('nodeName')=='TEXTAREA') elemnextuo.focus();
          else elemnextuo.focus().select();
        } else {
//          elemnext_set=$('.gks_set[data-ap=' + (ap+1) + ']');
//          if (elemnext_set.length>0) {
//            if (elemnext_set.prop('nodeName')=='TEXTAREA') elemnext_set.focus();
//            else elemnext_set.focus().select();
//          } else {
            if (elemnext.prop('nodeName')=='TEXTAREA') elemnext.focus();
            else elemnext.focus().select();
//          }
        }
      } else {
        $('.gks_add_attach_paramline[data-ap=' + ap + ']').click();
      }
    } else if (faultback!='') {
      elem=$('.' + faultback + '[data-ap=' + ap + ']');
      if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
      else elem.focus().select();
    }
  }  
  function gks_attach_param_basefolder_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        ap=parseInt($(this).attr('data-ap'));
        if (isNaN(ap)) ap=0;
        if (ap<=0) return;
        next_enter_attach_paramline_field_fnc(ap,'gks_attach_param_basefolder','gks_attach_param_relative_path');
        return;
      }
    }
  }
  $('.gks_attach_param_basefolder').keyup(gks_attach_param_basefolder_keyup);    
  
  function gks_attach_param_relative_path_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        ap=parseInt($(this).attr('data-ap'));
        if (isNaN(ap)) ap=0;
        if (ap<=0) return;
        next_enter_attach_paramline_field_fnc(ap,'gks_attach_param_relative_path','gks_attach_param_name_for_email');
        return;
      }
    }
  }
  $('.gks_attach_param_relative_path').keyup(gks_attach_param_relative_path_keyup);    
  
  function gks_attach_param_name_for_email_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        ap=parseInt($(this).attr('data-ap'));
        if (isNaN(ap)) ap=0;
        if (ap<=0) return;
        next_enter_attach_paramline_field_fnc(ap,'gks_attach_param_name_for_email','new_row');
        return;
      }
    }
  }
  $('.gks_attach_param_name_for_email').keyup(gks_attach_param_name_for_email_keyup);  
  

  


  function gks_delete_attach_paramline_click() {
    need_save=true;
    ap=parseInt( $(this).attr('data-ap'));
    if (isNaN(ap)) ap=0;
    if (ap<=0) return;
    $('.gks_attach_param_line[data-ap=' + ap +']').remove(); 
    
    if ($('.gks_attach_param_line').length ==0) {
      attach_paramline_add(false,0);  
    }
   
    gks_myscroll();
  }
  $('.gks_delete_attach_paramline').click(gks_delete_attach_paramline_click);   


  function attach_paramline_add(fromloading,click_ap,def_basefolder='',def_relative_path='') {
    //console.log('click_ap',click_ap);
    need_save=true;
    last_ap++;
    
    row_html=
    
    '<div class="form-group row gks_attach_param_line" data-ap="' + last_ap + '">' +
      '<div class="' + from_php_gkscols_attach_param1 + '">' +
        '<select class="form-control form-control-sm myneedsave gks_select2 gks_attach_param_basefolder" data-ap="' + last_ap + '">' +
        '<option value="erplo" ' + (def_basefolder=='erplo' ? 'selected' : '') + '>'+gks_lang('ERP Λογότυπα')+'</option>' +
        '<option value="erpfi" ' + (def_basefolder=='erpfi' ? 'selected' : '') + '>'+gks_lang('ERP Αρχεία')+'</option>' +
        '<option value="erpul" ' + (def_basefolder=='erpul' ? 'selected' : '') + '>'+gks_lang('ERP Μεταφορτώσεις')+'</option>' +
        '<option value="erpdl" ' + (def_basefolder=='erpdl' ? 'selected' : '') + '>'+gks_lang('ERP Λήψεις')+'</option>' +
        '<option value="wodpr" ' + (def_basefolder=='wodpr' ? 'selected' : '') + '>'+gks_lang('Wordpress')+'</option>' +
        '</select>'+
      '</div>' +
      '<div class="' + from_php_gkscols_attach_param2 + ' gks_attach_param_relative_path_div">' +
        '<input type="text" class="form-control form-control-sm myneedsave gks_attach_param_relative_path" data-ap="' + last_ap + '" value="' + def_relative_path + '"/>' +
        '<div title="'+gks_lang('Εξερεύνηση αρχείων')+'" class="btn btn-primary btn-sm gks_attach_param_relative_path_btn" data-ap="' + last_ap + '">' +
          '<i class="fa-solid fa-file-lines"></i>' +
        '</div>' +
      '</div>' +
      '<div class="' + from_php_gkscols_attach_param3 + '">' +
        '<input type="text" class="form-control form-control-sm myneedsave gks_attach_param_name_for_email" data-ap="' + last_ap + '" value=""/>' +
      '</div>' +
      '<div class="' + from_php_gkscols_attach_param4 + ' text-center">' +
        '<input type="checkbox" class="gks_attach_param_def_check_new switchery1_this" data-ap="' + last_ap + '" value="1" checked/>' +
      '</div>' +

      '<div class="' + from_php_gkscols_attach_param5 + '">' +
        '<div class="text-center gks_attach_param_icons">' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-trash-alt gks_delete_attach_paramline" data-ap="' + last_ap + '"></i>' +
          '</div>' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-arrows-alt-v sortorder_attach_paramline_handle"></i>' +
          '</div>' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-plus-circle gks_add_attach_paramline"  data-ap="' + last_ap + '"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
      
    '</div>';
    
    
    if (click_ap<=0) {
      $('#gks_attach_param_footer1').before(row_html);
    } else {
      $('.gks_attach_param_line[data-ap=' + click_ap + ']').after(row_html);
    }
    
    $('.gks_add_attach_paramline').show();  
    $('.gks_delete_attach_paramline').show();  
    

    
    $('.gks_attach_param_basefolder[data-ap=' + last_ap + ']').keyup(gks_attach_param_basefolder_keyup);
    $('.gks_attach_param_relative_path[data-ap=' + last_ap + ']').keyup(gks_attach_param_relative_path_keyup);
    $('.gks_attach_param_name_for_email[data-ap=' + last_ap + ']').keyup(gks_attach_param_name_for_email_keyup);

    var elems_switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.gks_attach_param_def_check_new'));
    elems_switchery1_this.forEach(function(html) {
      var switchery1_this = new Switchery(html,gks_switchery_defaults());
      html.onchange = function() {need_save=true;};
    });
    $('.gks_attach_param_def_check_new').addClass('gks_attach_param_def_check').removeClass('gks_attach_param_def_check_new');
    
    $('.gks_attach_param_relative_path_btn[data-ap=' + last_ap + ']').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});
    $('.gks_attach_param_relative_path_btn[data-ap=' + last_ap + ']').click(gks_attach_param_relative_path_btn_click);
    
    
    $('.gks_add_attach_paramline[data-ap=' + last_ap + ']').click(function() {gks_add_attach_paramline_click(false,$(this));});
    $('.gks_delete_attach_paramline[data-ap=' + last_ap + ']').click(gks_delete_attach_paramline_click); //.hide();
    

    if (fromloading==false) {
      if (from_php_enter_attach_param_order.length>0) {
        $('.' + from_php_enter_attach_param_order[0] + '[data-ap=' + last_ap + ']').focus().select();
      } else {
        $('.gks_attach_param_basefolder[data-ap=' + last_ap + ']').focus().select();
      }
    }
    
    if (def_relative_path=='' && click_ap>0) {
      var mylist=[];
      $('.gks_attach_param_line').each(function() {
        mylist.push($(this).attr('data-ap'));
      });
      attach_params_table_sortable_after(mylist);
    }
    
    gks_myscroll();
  }  
  
  

  function gks_add_attach_paramline_click(fromloading,elem) {
    ap=elem.attr('data-ap');
    attach_paramline_add(fromloading,ap);
  }
  
  $('.gks_add_attach_paramline').click(function() {gks_add_attach_paramline_click(false,$(this));});

  $('#attach_params_table').sortable({
    items: '.gks_attach_param_line',
    handle: '.sortorder_attach_paramline_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-ap'});
      attach_params_table_sortable_after(mylist);
    }
  });
  
  function attach_params_table_sortable_after(mylist) {
      //console.log(mylist);
      $('#attach_params_table > .gks_attach_param_line').each(function() {
        aa=$(this).attr('data-ap');
        $(this).attr('data-ap_temp',aa);
      });
      $('#attach_params_table > .gks_attach_param_line').each(function() {
        ap=$(this).attr('data-ap_temp');
        new_ap=-1;
        for(i=0;i<mylist.length;i++) {
          if (mylist[i]==ap) {
            new_ap=i;break;
          }
        }
        //console.log('new_aa',new_aa);
        if (new_ap>=0) {
          new_ap++
          $(this).attr('data-ap',new_ap);
          $(this).find('*[data-ap=' + ap + ']').attr('data-ap',new_ap);
        }
        
      })      
    
  }  
  
  var selected_data_ap=0;
  function gks_attach_param_relative_path_btn_click() {
    data_ap=parseInt($(this).attr('data-ap'));if (isNaN(data_ap)) data_ap=0; 
    if (data_ap<=0) return;
    selected_data_ap=data_ap;
    
    def_basefolder=$('.gks_attach_param_basefolder[data-ap=' + data_ap + ']').val();
    def_folder=$('.gks_attach_param_relative_path[data-ap=' + data_ap + ']').val();
    lastSlash = def_folder.lastIndexOf('/');
    def_folder = '/' + def_folder.substr(0,lastSlash);
    gks_filesexplore_div_footer_show=true;
    if (gks_filesexplore_div_start_run==false) {
      gks_filesexplore_div_start(def_basefolder,def_folder);
    } else {
      $('#gks_filesexplore_div').show(); 
      gks_filesexplore_get_folder_data(def_basefolder,def_folder);
    }    
  }
  $('.gks_attach_param_relative_path_btn').click(gks_attach_param_relative_path_btn_click);

  window.gks_filesexplore_div_footer_btn_ok_click_callback=function() {
    //console.log('ssss');
    var selected_files=[];
    $('.gks_filesexplore_file_selected').each(function() {
      data_rel_path=$(this).attr('data-rel_path');
      if (typeof(data_rel_path)!=undefined && data_rel_path!='') {
        selected_files.push(data_rel_path);
      }
    });
    //console.log(selected_files);
    if (selected_files.length==0) {
      myalert('error:'+gks_lang('Επιλέξτε κάποιο αρχείο'));
      return;
    }
    def_basefolder=$('#gks_filesexplore_basefolder').val();
    $('.gks_attach_param_relative_path[data-ap=' + selected_data_ap +']').val(selected_files[0]);
    $('.gks_attach_param_basefolder[data-ap=' + selected_data_ap +']').val(def_basefolder);
    if (selected_files.length>=2) {
      for(ii=1; ii<selected_files.length; ii++) {
        if (ii==1) pos_insert=selected_data_ap;
        else pos_insert=last_ap+0;
        attach_paramline_add(false,pos_insert,def_basefolder,selected_files[ii]);
        //console.log(ii,selected_files[ii]);
      } 

      var mylist=[];
      $('.gks_attach_param_line').each(function() {
        mylist.push($(this).attr('data-ap'));
      });
      attach_params_table_sortable_after(mylist);
    }
    $('#gks_filesexplore_div').hide();
    need_save=true;
  }
    
  
  // last of all 
  if (from_php_id==-1) {
    parameterline_add(true,0);
    attach_paramline_add(true,0);
  } else {
    if (last_bb==0) parameterline_add(true,0);
    if (last_ap==0) attach_paramline_add(true,0);
  } 
  
  
  
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
  //email_body
  tinymce.init({
    language: from_php_gks_tinymce_locale,
    entity_encoding : 'raw',
    forced_root_block:false, 
    remove_trailing_brs: false,
    theme: 'silver', 
    browser_spellcheck: true,
    // to plugin fullpage einai gia full code
    plugins: 'fullpage autoresize print preview  searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount imagetools textpattern help code',
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
  
    selector: '#email_body', //'.gks_curr_tinymce',
    init_instance_callback: function(editor) {
      editor.on('Change', function(e) {
        need_save=true;
      });
      //editor.execCommand('mceAutoResize');
    },
    readonly : (from_php_perm_ret_edit ? 0 : 1),
  });

  //email_message
  tinymce.init({
    language: from_php_gks_tinymce_locale,
    entity_encoding : 'raw',
    forced_root_block:false, 
    remove_trailing_brs: false,
    theme: 'silver', 
    browser_spellcheck: true,
    // to plugin fullpage einai gia full code
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
  
    selector: '#email_message', //'.gks_curr_tinymce',
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
  tinyMCE.get('email_body').destroy();
  tinyMCE.get('email_message').destroy();
}

if (!(from_php_edit_mode=='raw')) {
  gks_curr_tinymce_init();
}
