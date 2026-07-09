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


  
  function mysubmit() {
    datasend='';

    
    if ($("#mypostform #gks_lang").val().trim()=='') {
      $("#gks_lang").focus();  
      myalert('error:'+gks_lang('Ορίστε την Γλώσσα της φόρμας εκτύπωσης'));
      return;      
    }
    
     
    

    datasend+='&template_html_descr='  + encodeURIComponent($.base64.encode($("#mypostform #template_html_descr").val().trim()));
    datasend+='&template_html_type='  + $("#mypostform #template_html_type").val();
    datasend+='&gks_lang='  + encodeURIComponent($.base64.encode($("#mypostform #gks_lang").val().trim()));
    
    if ($('#edit_mode_raw').prop('checked')) datasend+='&edit_mode=' + encodeURIComponent($.base64.encode('raw'));
    else datasend+='&edit_mode=' + encodeURIComponent($.base64.encode('html'));
    
    datasend+='&orders_online_url='  + encodeURIComponent($.base64.encode($("#mypostform #orders_online_url").val().trim()));
    datasend+='&orders_online_sms_sender='  + encodeURIComponent($.base64.encode($("#mypostform #orders_online_sms_sender").val().trim()));
    
    datasend+='&is_disable=' + (($('#is_disable').is(':checked')) ? '0':'1');
    
    

    if (gks_curr_tinymce_running==false) {
      datasend+='&html_part_1='  + encodeURIComponent($.base64.encode($('#html_part_1').val()));
      datasend+='&html_part_3='  + encodeURIComponent($.base64.encode($('#html_part_3').val()));
      datasend+='&html_part_5='  + encodeURIComponent($.base64.encode($('#html_part_5').val()));
      datasend+='&html_part_6='  + encodeURIComponent($.base64.encode($('#html_part_6').val()));
      datasend+='&html_part_7='  + encodeURIComponent($.base64.encode($('#html_part_7').val()));
      datasend+='&html_part_4='  + encodeURIComponent($.base64.encode($('#html_part_4').val()));
      datasend+='&html_part_2='  + encodeURIComponent($.base64.encode($('#html_part_2').val()));
      datasend+='&html_part_8='  + encodeURIComponent($.base64.encode($('#html_part_8').val()));
      datasend+='&html_part_9='  + encodeURIComponent($.base64.encode($('#html_part_9').val()));
    } else {
      datasend+='&html_part_1='  + encodeURIComponent($.base64.encode(tinyMCE.get('html_part_1').getContent()));
      datasend+='&html_part_3='  + encodeURIComponent($.base64.encode(tinyMCE.get('html_part_3').getContent()));
      datasend+='&html_part_5='  + encodeURIComponent($.base64.encode(tinyMCE.get('html_part_5').getContent()));
      datasend+='&html_part_6='  + encodeURIComponent($.base64.encode(tinyMCE.get('html_part_6').getContent()));
      datasend+='&html_part_7='  + encodeURIComponent($.base64.encode(tinyMCE.get('html_part_7').getContent()));
      datasend+='&html_part_4='  + encodeURIComponent($.base64.encode(tinyMCE.get('html_part_4').getContent()));
      datasend+='&html_part_2='  + encodeURIComponent($.base64.encode(tinyMCE.get('html_part_2').getContent()));
      datasend+='&html_part_8='  + encodeURIComponent($.base64.encode(tinyMCE.get('html_part_8').getContent()));
      datasend+='&html_part_9='  + encodeURIComponent($.base64.encode(tinyMCE.get('html_part_9').getContent()));
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
			url: '/my/admin-template_html-item-exec.php?id=' + from_php_id,
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
          '<i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="'+gks_lang('Προβολή Πρότυπου HTML')+'"></i>' +
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
          url: "admin-autocomplete-template_html.php",
          dataType: "json",
          data: {
            term: request.term,
            lang_id: $('.local_set_lang[data-id=' + $(this)[0].element.attr('data-id') + ']').val(),
            
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
        $('.local_set_form_link[data-id=' + data_id + ']').show().attr('href','admin-template_html-item.php?id=' + ui.item.id);
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
  


  
  
  $('#submit_button_export').click(function() {
    mydatajson={};
    mydatajson.app='gks ERP';
    mydatajson.exporturl=window.location.href;
    d = new Date();
    d = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+ d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
    mydatajson.exportdate= d;
    mydatajson.filetype='gks_template_html';
    mydatajson.form={};
    mydatajson.form.id=from_php_id;
    mydatajson.form.template_html_descr=$('#template_html_descr').val();
    mydatajson.form.is_disable=(($('#is_disable').is(':checked')) ? 0:1);
    mydatajson.form.sortorder=parseInt($("#mypostform #sortorder").val().trim());
    mydatajson.form.gks_lang=$("#mypostform #gks_lang").val();
    mydatajson.form.template_html_type=parseInt($('#template_html_type').val());
    mydatajson.form.orders_online_url=$('#orders_online_url').val();
    mydatajson.form.orders_online_sms_sender=$('#orders_online_sms_sender').val();

    if ($('#edit_mode_raw').prop('checked')) mydatajson.form.edit_mode='raw'; else mydatajson.form.edit_mode='html';
        
    if (gks_curr_tinymce_running==false) {
      mydatajson.form.html_part_1=$.base64.encode($('#html_part_1').val());
      mydatajson.form.html_part_3=$.base64.encode($('#html_part_3').val());
      mydatajson.form.html_part_5=$.base64.encode($('#html_part_5').val());
      mydatajson.form.html_part_6=$.base64.encode($('#html_part_6').val());
      mydatajson.form.html_part_7=$.base64.encode($('#html_part_7').val());
      mydatajson.form.html_part_4=$.base64.encode($('#html_part_4').val());
      mydatajson.form.html_part_2=$.base64.encode($('#html_part_2').val());
      mydatajson.form.html_part_8=$.base64.encode($('#html_part_8').val());
      mydatajson.form.html_part_9=$.base64.encode($('#html_part_9').val());
    } else {
      mydatajson.form.html_part_1=$.base64.encode(tinyMCE.get('html_part_1').getContent());
      mydatajson.form.html_part_3=$.base64.encode(tinyMCE.get('html_part_3').getContent());
      mydatajson.form.html_part_5=$.base64.encode(tinyMCE.get('html_part_5').getContent());
      mydatajson.form.html_part_6=$.base64.encode(tinyMCE.get('html_part_6').getContent());
      mydatajson.form.html_part_7=$.base64.encode(tinyMCE.get('html_part_7').getContent());      
      mydatajson.form.html_part_4=$.base64.encode(tinyMCE.get('html_part_4').getContent());
      mydatajson.form.html_part_2=$.base64.encode(tinyMCE.get('html_part_2').getContent());
      mydatajson.form.html_part_8=$.base64.encode(tinyMCE.get('html_part_8').getContent());
      mydatajson.form.html_part_9=$.base64.encode(tinyMCE.get('html_part_9').getContent());
    }
    
    mydatajson.form.custom_css=$.base64.encode(custom_css_editor.getValue());
    mydatajson.form.custom_javascript=$.base64.encode(custom_javascript_editor.getValue());
    

    
    //console.log(mydatajson);return;
    
    const blob = new Blob([JSON.stringify(mydatajson, null, 2)], {type:'application/json'});
    const url = URL.createObjectURL(blob); 
    const a=document.createElement('a'); 
    a.href=url; 
    a.download='gks_ERP_template_html_' + from_php_id + '_' + $('#template_html_descr').val() + '_' + d.replaceAll(':','_').replaceAll(' ','_').replaceAll('-','_') + '.json'; 
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
            typeof(mydatajson.filetype)=='string' && mydatajson.filetype=='gks_template_html' &&
            typeof(mydatajson.form)=='object') {
          const myf=mydatajson.form;
          if (typeof(myf.template_html_descr)=='string') $('#mypostform #template_html_descr').val(myf.template_html_descr);
          if (typeof(myf.is_disable)=='number') {
            if (($('#mypostform #is_disable').is(':checked')?0:1)!=myf.is_disable) $('#mypostform #is_disable').click();
          }
          if (typeof(myf.sortorder)=='number') $("#mypostform #sortorder").val(myf.sortorder);
          if (typeof(myf.gks_lang)=='string' && $('#mypostform #gks_lang option[value="'+ myf.gks_lang +'"]').length==1) $("#mypostform #gks_lang").val(myf.gks_lang);

          if (typeof(myf.template_html_type)=='number') $('#mypostform #template_html_type').val(myf.template_html_type);
          if (typeof(myf.orders_online_url)=='string') $('#mypostform #orders_online_url').val(myf.orders_online_url);
          if (typeof(myf.orders_online_sms_sender)=='string') $('#mypostform #orders_online_sms_sender').val(myf.orders_online_sms_sender);

          template_html_type_change();
          
          $('#edit_mode_raw').prop('checked',true).click();
          if (typeof(myf.html_part_1)=='string') $('#html_part_1').val($.base64.decode(myf.html_part_1));
          if (typeof(myf.html_part_3)=='string') $('#html_part_3').val($.base64.decode(myf.html_part_3));
          if (typeof(myf.html_part_5)=='string') $('#html_part_5').val($.base64.decode(myf.html_part_5));
          if (typeof(myf.html_part_6)=='string') $('#html_part_6').val($.base64.decode(myf.html_part_6));
          if (typeof(myf.html_part_7)=='string') $('#html_part_7').val($.base64.decode(myf.html_part_7));
          if (typeof(myf.html_part_4)=='string') $('#html_part_4').val($.base64.decode(myf.html_part_4));
          if (typeof(myf.html_part_2)=='string') $('#html_part_2').val($.base64.decode(myf.html_part_2));
          if (typeof(myf.html_part_8)=='string') $('#html_part_8').val($.base64.decode(myf.html_part_8));
          if (typeof(myf.html_part_9)=='string') $('#html_part_9').val($.base64.decode(myf.html_part_9));
            
          if (typeof(myf.edit_mode)!='string') myf.edit_mode='raw';
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
  
  
  function template_html_type_change() {
    var typeid=parseInt($('#template_html_type').val())  ;
    if (isNaN(typeid)) typeid=0;
    if (typeid==0) {
      $('.container_html_part').hide();
      return;
    }
    from_php_js_types.forEach(function(typeitem) {
      if (typeid==typeitem.id) {
        console.log(typeitem);
        for(ii=0; ii<typeitem.t.length; ii++) {
          iii=ii+1;
          ddd=$('.container_html_part[data-index="'+iii+ '"]');
          if (typeitem.t[ii]=='') {
            ddd.hide();
          } else {
            ddd.show();
            ddd.find('.card-header').html(typeitem.t[ii]);
          }
          
        }
      }
    });
  }
  $('#template_html_type').change(template_html_type_change);
  template_html_type_change();
  
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
  tinyMCE.get('html_part_1').destroy();
  tinyMCE.get('html_part_3').destroy();
  tinyMCE.get('html_part_5').destroy();
  tinyMCE.get('html_part_6').destroy();
  tinyMCE.get('html_part_7').destroy();
  tinyMCE.get('html_part_4').destroy();
  tinyMCE.get('html_part_2').destroy();
  tinyMCE.get('html_part_8').destroy();
  tinyMCE.get('html_part_9').destroy();
}

if (!(from_php_edit_mode=='raw')) {
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
