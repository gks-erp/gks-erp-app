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

  $('#submit_button_ok_custom').click(function(event) {mysubmit(); return false;});


  function mysubmit() {
    var mysort=[]
    $('.connectedSortable').each(function() {
      myppp=$(this).parent().parent();
      card_name=myppp.find('input.gks_div_custom_field_card_name').val();
      card_sortorder=myppp.find('.gks_div_custom_field_card_name_sortorder').val();
      card_width=myppp.find('.gks_div_custom_field_card_name_width').val();
      if (typeof(card_width)=='undefined') card_width='12';
      
      myar = $(this).sortable('toArray', { attribute: 'data-aa'});
      
      mysort.push({card_name: card_name,
                  card_sortorder:card_sortorder, 
                  card_width:card_width,
                  myar : myar});
    });
    //console.log(mysort);return;
    
    
    var myfields=[];
    $('.gks_div_custom_field').each(function() {
      item={};
      
      item.aa=parseInt($(this).attr('data-aa'));       if (isNaN(item.aa)) item.aa=0;
      item.recid=parseInt($(this).attr('data-rec-id'));if (isNaN(item.recid)) item.recid=0;
      
      item.label=$(this).find('.gks_div_custom_field_label').val().trim();
      item.type_id=$(this).find('.gks_div_custom_field_type_id').val().trim();
      item.allow_null=($(this).find('.gks_div_custom_field_allow_null').is(':checked') ? 0 : 1);
      item.default_value=$(this).find('.gks_div_custom_field_default_value').val().trim();
      item.show_on_list=($(this).find('.gks_div_custom_field_show_on_list').is(':checked') ? 1 : 0);
      

      myoptions={};
      
      if (item.type_id==501) {//501 epilogi enos apo lista
        var t_items=[];
        $(this).find('.gks_div_custom_field_attr_options').find('tbody').find('tr').each(function() {
          td_value=$(this).find('.gks_div_custom_field_attr_options_td_value').find('input').val();
          td_text=$(this).find('.gks_div_custom_field_attr_options_td_text').find('input').val();
          t_items.push({value:td_value,text: td_text});
        });
        item.options=t_items;
        
      } else if (item.type_id==502) {//502 epilogi pollon apo lista
        var t_items=[];
        $(this).find('.gks_div_custom_field_attr_options').find('tbody').find('tr').each(function() {
          td_text=$(this).find('.gks_div_custom_field_attr_options_td_text').find('input').val();
          t_items.push(td_text);
        });
        item.options=t_items;
        
      }
      

      
      myfields.push(item);
    });
    //console.log(myfields);
    
    datasend='';
    datasend+='&custom_table_descr=' + encodeURIComponent($.base64.encode($('#custom_table_descr').val()));
    datasend+='&custom_table_disabled=' + (($('#custom_table_disabled').is(':checked')) ? '0':'1');
    datasend+='&num_columns=' + $('#num_columns').val();
    datasend+='&mysort_str=' + encodeURIComponent($.base64.encode(JSON.stringify(mysort)));
    datasend+='&myfields_str=' + encodeURIComponent($.base64.encode(JSON.stringify(myfields)));

    if (from_php_id==-1 || from_php_id>=10000) {
      datasend+='&erp_app_id_check=' + (($('#erp_app_id_check').is(':checked')) ? '1':'0');
      datasend+='&erp_app_filter_val_webpage_computer=' + (($('#erp_app_filter_val_webpage_computer').is(':checked')) ? '1':'0');
      datasend+='&erp_app_filter_val_webpage_tablet=' + (($('#erp_app_filter_val_webpage_tablet').is(':checked')) ? '1':'0');
      datasend+='&erp_app_filter_val_webpage_mobile=' + (($('#erp_app_filter_val_webpage_mobile').is(':checked')) ? '1':'0');
      datasend+='&erp_app_filter_val_app_with_thermal=' + (($('#erp_app_filter_val_app_with_thermal').is(':checked')) ? '1':'0');
      datasend+='&erp_app_filter_val_app_no_thermal=' + (($('#erp_app_filter_val_app_no_thermal').is(':checked')) ? '1':'0');
      datasend+='&erp_app_id=' + encodeURIComponent(($("#mypostform #erp_app_id").val().trim()));
      datasend+='&erp_app_dest=' + encodeURIComponent($.base64.encode($('input[name=erp_app_dest]:checked').val()));
      datasend+='&erp_app_dest_printer='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_dest_printer").val().trim()));
      datasend+='&erp_app_dest_printer_method='  + encodeURIComponent(($("#mypostform #erp_app_dest_printer_method").val().trim()));
      datasend+='&erp_app_dest_printer_lpr_ip='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_dest_printer_lpr_ip").val().trim()));
      datasend+='&erp_app_dest_printer_copies='  + encodeURIComponent(($("#mypostform #erp_app_dest_printer_copies").val().trim()));
      datasend+='&erp_app_dest_folder='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_dest_folder").val().trim()));
    }
    //console.log(datasend);



    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-custom-item-exec.php?id=' + from_php_id,
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
  

  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
    
  
  $('.connectedSortable' ).sortable({
      connectWith: '.connectedSortable',
      placeholder: 'ui-state-highlight',
      handle: '.gks_div_custom_field_handle',
      helper: 'clone',
  }).disableSelection();
  
    
  
  function gks_div_custom_field_expand_icon_click() {
    aa=parseInt($(this).parent().attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    elem=$('.gks_div_custom_field_properties[data-aa=' + aa + ']');
    if (elem.length!=1) return;
    if (elem.css('display')!='none') {
      elem.hide('blind', {}, 500);
      $(this).animateRotate(180,0,500);
    } else {
      elem.show('blind', {}, 500);
      $(this).animateRotate(0,180,500);
    }
  }
  $('.gks_div_custom_field_expand_icon').click(gks_div_custom_field_expand_icon_click);
  
  
  
  
  function gks_div_custom_field_add_icon_click() {
    after_elem=$(this).parent().parent();
    gks_div_custom_field_add_icon_click_run(null, after_elem);
  }
  
  
  function gks_div_custom_field_add_icon_click_run(inside_elem, after_elem) {
    var aa=0;
    
    $('.gks_div_custom_field').each(function() {
      aaa=parseInt($(this).attr('data-aa'));
      if (isNaN(aaa)) aaa=0;
      if (aaa>aa) aa=aaa;
    })
    aa++;
    
    var html=   
                '<div ' +
                'class="gks_div_custom_field" '+ 
                'data-aa="' + aa + '" '+
                'data-rec-id="0" '+
                '>' +
                  '<div class="gks_div_custom_field_handle" data-aa="' + aa + '"><i class="fas fa-arrows-alt-v"></i></div>' +
                  '<div class="gks_div_custom_field_text"   data-aa="' + aa + '">' +
                    '<input id="field_label_' + aa + '" data-aa="' + aa + '" type="text" class="gks_div_custom_field_label form-control form-control-sm myneedsave" value="">' +
                  '</div>' +
                  
                  '<div class="gks_div_custom_field_expand" data-aa="' + aa + '"><i class="fas fa-angle-double-down gks_div_custom_field_expand_icon" style="transform: rotate(0deg);"></i></div>' +
                  '<div class="gks_div_custom_field_remove" data-aa="' + aa + '"><i class="fas fa-trash-alt         gks_div_custom_field_remove_icon"></i></div>' +
                  '<div class="gks_div_custom_field_add"    data-aa="' + aa + '"><i class="fas fa-plus-circle       gks_div_custom_field_add_icon"   ></i></div>' +
                  '<div class="gks_div_custom_field_properties" data-aa="' + aa + '" style="display:none;">';

    html+=          '<div class="form-group row">' +
                      '<label class="col-md-4 col-form-label form-control-sm text-md-right">DB Field Name:</label>' +
                      '<label class="col-md-8 col-form-label form-control-sm text-md-right1">' +
                      
                      '</label>' +
                    '</div>';
                  
    html+=          '<div class="form-group row">' +
                      '<label for="field_type_id_' + aa + '" class="col-md-4 col-form-label form-control-sm text-md-right">'+gks_lang('Τύπος')+':</label>' +
                      '<div class="col-md-8">' +
                        '<select id="field_type_id_' + aa + '" data-aa="' + aa + '" class="gks_div_custom_field_type_id form-control form-control-sm myneedsave" >' +
                          '<option value="0"></option>';
                        gks_ftypes.forEach((myg) => {
                            //console.log(myg);
                            html+='<optgroup label="' + myg.gdescr + '">';
                            myg.ft.forEach((myt) => {
                              //console.log(myt);
                              html+='<option value="' + myt.id + '">' + myt.descr + '</option>';
                            });
                            html+='</optgroup>';
                        });
                          
    html+=              '</select>' +                  
                      '</div>' +
                    '</div>';                  

                    
                    
    html+=          '<div class="form-group row" style="' +
                      'display:none;' +
                      '">' +
                      '<label for="field_attr_options_' + aa + '" class="col-md-4 col-form-label form-control-sm text-md-right">'+gks_lang('Τιμές')+':</label>' +
                      '<div class="col-md-8">' +
                      
                        '<table data-aa="' + aa + '" class="gks_div_custom_field_attr_options table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="field_attr_options_' + aa + '">' +
                          '<thead>' +
                            '<tr>' +
                              '<th class="table-dark" scope="col" width="0%" nowrap="">#</th>' +
                              '<th class="table-dark gks_div_custom_field_attr_options_th_value" scope="col" style="width:50%;" nowrap="" ' +
                              'style="display:none;" ' +
                              '>'+gks_lang('Τιμή')+'</th>' +
                              '<th class="table-dark gks_div_custom_field_attr_options_th_text" scope="col" style="width:' +
                              '100' +
                              '%;" nowrap="">'+gks_lang('Περιγραφή')+'</th>' +
                              '<th class="table-dark " scope="col" width="0%" nowrap="">'+gks_lang('Ενέργεια')+'</th>' +
                            '</tr>' +
                          '</thead>' +
                          '<tbody>';
                          
 
                          
    html+=                  '<tr>' +
                              '<th scope="row" nowrap="" class="gks_div_custom_field_attr_options_td_ii">1</th>' +
                              '<td nowrap class="mytdcm gks_div_custom_field_attr_options_td_value" style="display:none;">' +
                                '<input type="text" class="form-control form-control-sm myneedsave" value="' +
                                
                                '"/>' +
                              '</td>' +
                              '<td nowrap class="mytdcm gks_div_custom_field_attr_options_td_text">' +
                                '<input type="text" class="form-control form-control-sm myneedsave" value="' +
                                  
                                '"/>' +
                              '</td>' +
                              '<td nowrap class="mytdcm">' +
                                '<i class="fas fa-trash-alt     gks_div_custom_field_attr_options_td_value_remove"></i>' +
                                '<i class="fas fa-plus-circle   gks_div_custom_field_attr_options_td_value_add"   ></i>' +
                              '</td>' +
                            '</tr>';
                        
                              

    html+=                '</tbody>' +   
                        '</table>' +                      
                      '</div>' +
                    '</div>';

    html+=          '<div class="form-group row">' +
                      '<label for="field_allow_null_' + aa + '" class="col-md-4 col-form-label form-control-sm text-md-right">'+gks_lang('Απαιτείται')+':</label>' +
                      '<div class="col-md-8">' +
                        '<input id="field_allow_null_' + aa + '" data-aa="' + aa + '" type="checkbox" class="gks_div_custom_field_allow_null switchery1_this" value="1">' +
                      '</div>' +
                    '</div>';

    html+=          '<div class="form-group row">' +
                      '<label for="field_default_value_' + aa + '" class="col-md-4 col-form-label form-control-sm text-md-right">'+gks_lang('Προεπιλεγμένη τιμή')+':</label>' +
                      '<div class="col-md-8">' +
                        '<input id="field_default_value_' + aa + '" data-aa="' + aa + '" type="text" class="gks_div_custom_field_default_value form-control form-control-sm myneedsave" value="">' +
                      '</div>' +
                    '</div>';



    html+=          '<div class="form-group row">' +
                      '<label for="field_show_on_list_' + aa + '" class="col-md-4 col-form-label form-control-sm text-md-right">'+gks_lang('Εμφάνιση στην προβολή λίστας')+':</label>' +
                      '<div class="col-md-8">' +
                        '<input checked id="field_show_on_list_' + aa + '" data-aa="' + aa + '" type="checkbox" class="gks_div_custom_field_show_on_list switchery1_this" value="1">' +
                      '</div>' +
                    '</div>';


                    
    html+=        '</div>' +
                   
                '</div>';
    
    if (after_elem!=null) {
      after_elem.after(html);
    } else {
      inside_elem.append(html);
    }
    //console.log(html);
    
    elem=$('.gks_div_custom_field[data-aa=' + aa + ']');
    elem.find('.gks_div_custom_field_add_icon').click(gks_div_custom_field_add_icon_click);
    elem.find('.gks_div_custom_field_remove_icon').click(gks_div_custom_field_remove_icon_click);
    elem.find('.gks_div_custom_field_expand_icon').click(gks_div_custom_field_expand_icon_click);
    elem.find('.gks_div_custom_field_type_id').change(gks_div_custom_field_type_id_change);
    elem.find('.switchery1_this').each(function() {
      var switchery3 = new Switchery($(this)[0],gks_switchery_defaults());
      html.onchange = function() {need_save=true;};      
    });
    
    elem.find('.gks_div_custom_field_attr_options_td_value_add').click(gks_div_custom_field_attr_options_td_value_add_click);
    elem.find('.gks_div_custom_field_attr_options_td_value_remove').click(gks_div_custom_field_attr_options_td_value_remove_click);

    need_save=true;  
  }
  
  $('.gks_div_custom_field_add_icon').click(gks_div_custom_field_add_icon_click);
  
  
  
  function gks_div_custom_field_remove_icon_click() {
    $(this).parent().parent().remove();
    need_save=true;
  }
  $('.gks_div_custom_field_remove_icon').click(gks_div_custom_field_remove_icon_click);
  
  function gks_div_custom_field_type_id_change() {
    aa=parseInt($(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    val=parseInt($(this).val());
    if (isNaN(val)) val=0;
    mytable=$('.gks_div_custom_field_attr_options[data-aa=' + aa + ']');
    if (val==501 || val==502) {
      
      if (val==501) {
        mytable.find('.gks_div_custom_field_attr_options_th_value').show();
        mytable.find('.gks_div_custom_field_attr_options_th_text').css({width:'50%'});
        mytable.find('.gks_div_custom_field_attr_options_td_value').show();
      } else {
        mytable.find('.gks_div_custom_field_attr_options_th_value').hide();
        mytable.find('.gks_div_custom_field_attr_options_th_text').css({width:'100%'});
        mytable.find('.gks_div_custom_field_attr_options_td_value').hide();
      }
      trlength = mytable.find('tbody tr').length;
      if (trlength==0) {
        gks_div_custom_field_attr_options_add_tr(mytable,null)
      }
      
      mytable.parent().parent().slideDown();
      
    } else {
      mytable.parent().parent().slideUp();
      
    }  
    need_save=true;
  }
  
  $('.gks_div_custom_field_type_id').change(gks_div_custom_field_type_id_change);
  
  
  function gks_div_custom_field_attr_options_td_value_add_click() {
    after_elem=$(this).parent().parent();
    table_elem=$(this).parent().parent().parent().parent();
    gks_div_custom_field_attr_options_add_tr(table_elem,after_elem);
    need_save=true;
  }
  
  $('.gks_div_custom_field_attr_options_td_value_add').click(gks_div_custom_field_attr_options_td_value_add_click);
  
  
  function gks_div_custom_field_attr_options_td_value_remove_click() {
    tbody=$(this).parent().parent().parent();
    trlength = tbody.find('tr').length;
    table_elem=$(this).parent().parent().parent().parent();
    $(this).parent().parent().remove();
    if (trlength<=1) {
      gks_div_custom_field_attr_options_add_tr(table_elem,null);
    } 
    gks_div_custom_field_attr_options_td_ii_fix(table_elem);
    need_save=true;
  }
  
  $('.gks_div_custom_field_attr_options_td_value_remove').click(gks_div_custom_field_attr_options_td_value_remove_click);
  
  function gks_div_custom_field_attr_options_add_tr(table_elem,after_elem=null) {
    properties_div=table_elem.parent().parent().parent();
    
    
    field_type_id=properties_div.find('.gks_div_custom_field_type_id').val();
    //console.log(field_type_id);
    
    html=
          '<tr class="tr_new_add">' +
              '<th scope="row" nowrap="" class="gks_div_custom_field_attr_options_td_ii">*</th>' +
              '<td nowrap class="mytdcm gks_div_custom_field_attr_options_td_value" style="' + (field_type_id==502 ? 'display:none' : '') + '">' +
                '<input type="text" class="form-control form-control-sm myneedsave" value="' +
                  
                '"/>' +
              '</td>' +
              '<td nowrap class="mytdcm gks_div_custom_field_attr_options_td_text">' +
                '<input type="text" class="form-control form-control-sm myneedsave" value="' +
                  
                '"/>' +
              '</td>' +
              '<td nowrap class="mytdcm">' +
                '<i class="fas fa-trash-alt     gks_div_custom_field_attr_options_td_value_remove"></i>' +
                '<i class="fas fa-plus-circle   gks_div_custom_field_attr_options_td_value_add"   ></i>' +
              '</td>' +
            '</tr>';    
    if (after_elem!=null) {
      after_elem.after(html);
    } else {
      table_elem.find('tbody').append(html);
    }
    
    gks_div_custom_field_attr_options_td_ii_fix(table_elem);
    
    
    
    $('.tr_new_add').find('.gks_div_custom_field_attr_options_td_value_add').click(gks_div_custom_field_attr_options_td_value_add_click);
    $('.tr_new_add').find('.gks_div_custom_field_attr_options_td_value_remove').click(gks_div_custom_field_attr_options_td_value_remove_click);
    $('.tr_new_add').removeClass('tr_new_add');
    need_save=true;
  }
  
  function gks_div_custom_field_attr_options_td_ii_fix(table_elem) {
    var ii=0;
    table_elem.find('.gks_div_custom_field_attr_options_td_ii').each(function() {
      ii++;
      $(this).html(ii);
    });    
  }
  
  function gks_div_card_add_field_click() {
    inside_elem=$(this).parent().parent().find('.connectedSortable');
    gks_div_custom_field_add_icon_click_run(inside_elem, null);
    need_save=true;
  }

  $('.gks_div_card_add_field').click(gks_div_card_add_field_click);
  
  
  $('.gks_div_card_add_card').click(function() {
    
    var cc=0;
    $('.gks_card_group_fields').each(function() {
      tt=parseInt($(this).attr('data-cc'));
      if (isNaN(tt)) tt=0;
      if (tt>cc) cc=tt;
    });
    cc++;
    var max_cc=0;
    $('.gks_div_custom_field_card_name_sortorder').each(function() {
      vvv=parseInt($(this).val());if (isNaN(vvv)) vvv=0;
      if (max_cc < vvv) max_cc=vvv;
    });
    max_cc+=10;
    
    html=
          '<div class="card gks_card_expand gks_card_group_fields" data-cc="' + cc + '">' +
            '<div class="card-header" style="text-align:center">' +
              '<input id="field_card_name_' + cc + '" data-cc="' + cc + '" type="text" class="gks_div_custom_field_card_name gks_stoppropagation form-control form-control-sm myneedsave" value="">' +
            '</div>' +
            '<div class="card-body gks_section_title" >' + 


              '<div class="gks_section_settings">'+
                '<div class="form-group row">'+
                   '<label for="field_card_name_sortorder' + cc + '" class="col-lg-2 col-form-label form-control-sm text-lg-right">'+gks_lang('Σειρά')+':</label>'+
                   '<div class="col-lg-4">'+
                     '<input id="field_card_name_sortorder' + cc + '" value="'+max_cc+'"  data-cc="' + cc + '" type="number" class="gks_div_custom_field_card_name_sortorder form-control form-control-sm myneedsave" min="1">'+
                   '</div>'+
                   

                   '<label for="field_card_name_width' + cc + '" class="col-lg-2 col-form-label form-control-sm text-lg-right">'+gks_lang('Πλάτος')+':</label>'+
                   '<div class="col-lg-4">'+
                     '<select id="field_card_name_width' + cc + '" data-cc="' + cc + '" type="number" class="gks_div_custom_field_card_name_width form-control form-control-sm myneedsave">';
      for (bsw=1;bsw<=12;bsw++) {
        html+='<option value="'+bsw+'"'+(bsw==12?'selected':'')+'>'+bsw+' / 12</option>';
      }                
      html+=           '</select>'+
                   '</div>'+
                '</div>'+
              '</div>'+


              '<div class="connectedSortable">' +
              
              '</div>' +
              '<div style="text-align:center;"><i class="fas fa-plus-circle gks_div_card_add_field"></i></div>' +
            '</div>' +
          '</div>';
    
    $('.gks_card_group_fields:last').after(html);
    
    $('.gks_card_group_fields[data-cc=' + cc + ']').find('.gks_div_card_add_field').click(gks_div_card_add_field_click);
    
    $('.gks_card_group_fields[data-cc=' + cc + ']').find('.connectedSortable' ).sortable({
        connectWith: '.connectedSortable',
        placeholder: 'ui-state-highlight',
        handle: '.gks_div_custom_field_handle',
        helper: 'clone',
    }).disableSelection();
    need_save=true;
  });
  
  
  
  
  
  $('#erp_app_id').change(function() {
    
    erp_app_id=parseInt($('#erp_app_id').val());
    if (isNaN(erp_app_id)) erp_app_id=0;
    $('#erp_app_dest_printer option').each(function() { 
      if ($(this).text() !='') {
        $(this).remove();
      }
    });
    
    if (erp_app_id>0) {
      local_printers=$('#erp_app_id option:selected').attr('data-local-printers').trim();
      //console.log(local_printers);
      if (local_printers!='') {
        local_printers=JSON.parse($.base64.decode(local_printers));
        //console.log(local_printers);
        for(i=0; i<local_printers.length;i++) {
          $('#erp_app_dest_printer').append('<option>' + local_printers[i] + '</option>');
        }
      }
    }
    
  });
  
  $('#erp_app_id_check').change(erp_app_dest_visible);
  $('input[name=erp_app_dest]').change(erp_app_dest_visible);
  $('#erp_app_dest_printer_method').change(erp_app_dest_visible);
  
  function erp_app_dest_visible() {
    need_save=true;
    if ($('#erp_app_id_check').is(':checked')) {
      $('.div_erp_app_id_check_only').slideDown();
      val=$('input[name=erp_app_dest]:checked').val();
      if (val=='printer') {
        $('.div_erp_app_id_check_printer').slideDown();
        $('.div_erp_app_id_check_folder').slideUp(); 
        erp_app_dest_printer_method = $('#erp_app_dest_printer_method').val();
        if (erp_app_dest_printer_method==2) { //2 lpr
          $('.div_erp_app_id_check_printer_id01').slideUp();
          $('.div_erp_app_id_check_printer_id2').slideDown();
          $('.div_erp_app_id_check_printer_id3').slideUp();
        } else if (erp_app_dest_printer_method==3) { //3 html
          $('.div_erp_app_id_check_printer_id01').slideUp();
          $('.div_erp_app_id_check_printer_id2').slideUp();
          $('.div_erp_app_id_check_printer_id3').slideDown();
          
          
        } else { //0 PDFium (pdf), 1 Adobe Acrobat Reader 
          $('.div_erp_app_id_check_printer_id01').slideDown();
          $('.div_erp_app_id_check_printer_id2').slideUp();
          $('.div_erp_app_id_check_printer_id3').slideUp();
          
        }
      } else if (val=='folder') {
        $('.div_erp_app_id_check_printer').slideUp();
        $('.div_erp_app_id_check_printer_id01').slideUp();
        $('.div_erp_app_id_check_printer_id2').slideUp();
        $('.div_erp_app_id_check_printer_id3').slideUp();         
        $('.div_erp_app_id_check_folder').slideDown(); 
      }
    } else {
      $('.div_erp_app_id_check').slideUp();
      $('.div_erp_app_id_check_only').slideUp();
    }
  }  
    
  
  
  //generic
  gks_page_loading=false;
  
  if (from_php_scrollto!='') {
    if ($('#' + from_php_scrollto).length>0) {
      $([document.documentElement, document.body]).animate({
          scrollTop: $('#' + from_php_scrollto).offset().top
      }, 500);
    }
    if (window.location.href.endsWith('&scrollto=' + from_php_scrollto)) {
      newurl=window.location.href;
      newurl=newurl.substring(0,newurl.length-('&scrollto=' + from_php_scrollto).length);
      
      window.history.pushState({}, window.document.title, newurl);
    }
  } else if (from_php_temp_mypropertiesheight!=0) {
    $("html").scrollTop(from_php_temp_mypropertiesheight);
  }



  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {

    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;
  
  
    
});  