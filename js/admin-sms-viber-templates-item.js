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
      
      elem=$('#submit_button_ok');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
      
    }  
    
  });
      
    
  window.mysubmit=function() {
    
    datasend='';


    datasend+='&sms_viber_template_name='  + encodeURIComponent($.base64.encode($("#mypostform #sms_viber_template_name").val().trim()));
    datasend+='&sms_viber_template_text='  + encodeURIComponent($.base64.encode($("#mypostform #sms_viber_template_text").val().trim()));
    datasend+='&sms_viber_template_sortorder='  + encodeURIComponent(($("#mypostform #sms_viber_template_sortorder").val().trim()));
    datasend+='&sms_viber_template_disabled=' + (($('#sms_viber_template_disabled').is(':checked')) ? '0':'1');
    datasend+='&sms_enabled=' + (($('#sms_enabled').is(':checked')) ? '1':'0');
    datasend+='&viber_enabled=' + (($('#viber_enabled').is(':checked')) ? '1':'0');
    datasend+='&fobjects='  + encodeURIComponent($.base64.encode($("#mypostform #gks_fobjects").val().trim()));


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


    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-sms-viber-templates-item-exec.php?id=' + from_php_id,
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
            need_save=false;
            if (data.redirect=='') {
  					  window.location.reload();
  					} else {
  					  window.location.href = $.base64.decode(data.redirect);
  					}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }  
  


  $("#sms_viber_template_text").on('change keyup paste', function() {
    messagesms_change('sms_viber_template_text','sms_chars');
    gks_resize_textarea($(this));
  });       
  messagesms_change('sms_viber_template_text','sms_chars');
  gks_resize_textarea($('#sms_viber_template_text')); 
    

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












  // last of all 
  if (from_php_id==-1) {
    parameterline_add(true,0);
  } else {
    if (last_bb==0) parameterline_add(true,0);
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

