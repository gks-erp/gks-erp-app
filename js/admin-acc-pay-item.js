/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

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
  },
  readonly : (from_php_perm_ret_edit ? 0 : 1),
    
});

var need_save=false;
//var mychange = 'input keyup paste';
var mychange = 'change keyup paste';
//var mychange = 'propertychange input change keyup paste';

//var mychange = 'change';
var gks_page_loading=true;

jQuery(document).ready(function($) {

  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    if (from_php_perm_ret_edit==false) return;
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      event.preventDefault();
      event.stopPropagation();
      elem=$('#submit_button_ok_custom');
      if (elem.css('display')!='none') {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
    }  
  });
  
  
  for(i=0;i<eidi_parastatikon_types.length;i++) {
    eidi_parastatikon_types[i].descr=$.base64.decode(eidi_parastatikon_types[i].descr);
    eidi_parastatikon_types[i].label=$.base64.decode(eidi_parastatikon_types[i].label);
  }






  $('#pay_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      if (from_php_perm_ret_edit==false) return;
      need_save=true;
    }
  }));  
  $('#dispatch_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      if (from_php_perm_ret_edit==false) return;
      need_save=true;
    }
  }));  



  
  $('#user').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
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
      old_val=$("#user_id").val();
      $("#user_id").val(ui.item.id);
      $('#autocomplete_user_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_user_id').show();

      
      
      
      gks_admin_get_user_data(ui.item.id, false);
    
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#user").val('');
        $("#user_id").val('');

        $('#autocomplete_user_id').hide(); 
        $('#balance_user_before').html((0).mymoney()).attr('data-val','0');
        $('#balance_user_after').html((0).mymoney());
        balance_user_after_calc();        

        $('#div_pelati_sxolio').hide('fade', 'slow');
        $('#text_pelati_sxolio').html('');
                        
        $('#div_order_sxolio').hide('fade', 'slow');
        $('#text_order_sxolio').html('');   
           
        $('#dr_user_first_name').html('');
        $('#dr_user_last_name').html('');
        $('#dr_user_email_div2').html('');
        $('#dr_user_mobile').html(''); 
        $('#dr_user_lang').html('');
        $('#dr_user_ma_odos').html('');
        $('#dr_user_ma_arithmos').html('');
        $('#dr_user_ma_orofos').html('');
        $('#dr_user_ma_perioxi').html('');
        $('#dr_user_ma_poli').html('');
        $('#dr_user_ma_tk').html('');
        $('#dr_user_ma_country_id').html('').attr('data-id','0');
        $('#dr_user_ma_nomos_id').html('').attr('data-id','0');
        
        $('#dr_user_eponimia').html('');
        $('#dr_user_title').html('');
        $('#dr_user_afm').html('');
        $('#dr_user_doy').html('');
        $('#dr_user_epaggelma').html('');
        $('#dr_user_afm_ee_initial_static').html('');
        $('#dr_user_afm_views_run_static').css('visibility','hidden');


            

        payment_is_for_invs();

      
        gks_myscroll(); 
        calc_pliroteo();             
      }
    }
  });

  


  $('.thisdeleterowbtn').click(function(event) {  
    if (from_php_perm_ret_edit==false) return; 
    var delete_id=$(this).attr('data-id');
    var delete_model=$(this).attr('data-model');
    var delete_backurl = $(this).attr('data-backurl');
    if (delete_id == undefined || delete_model == undefined) {
      return false; 
    }
    if (delete_backurl == undefined) delete_backurl='';
    myconfirm(gks_lang('Σίγουρα θέλετε να διαγράψετε την εγγραφή;'),'deleterow',delete_model,delete_id,delete_backurl);
    return false;
  }); 


  
  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  $('#submit_button_050proinvoice').click(function(event) {mysubmit('050proinvoice'); return false;});
  $('#submit_button_080listing').click(function(event) {mysubmit('080listing'); return false;});
  $('#submit_button_090ekdosi').click(function(event) {mysubmit('090ekdosi'); return false;});

  
  $('#submit_button_010draft').click(function(event) {
    myconfirm(gks_lang('Σίγουρα θέλετε να επαναφέρετε την πληρωμή σε πρόχειρη κατάσταση;'),
    'gks_mysubmit_draft');
    return false;
  });
  window.gks_mysubmit_draft = function() {
    mysubmit('010draft');
  }
    
  $('#submit_button_040cancelled').click(function(event) {
    myconfirm(gks_lang('Σίγουρα θέλετε να ακυρώσετε την πληρωμή;'),
    'gks_mysubmit_cancel');
    return false;
  });
  window.gks_mysubmit_cancel = function() {
    mysubmit('040cancelled');
  }
  
  $('#submit_button_credit_memo').click(function(event) {
    myconfirm(gks_lang('Σίγουρα θέλετε να δημιουργήσετε επιστροφή πληρωμής για την τρέχον πληρωμή;')+'<br>'+gks_lang('Θα δημιουργηθεί μία νέα πληρωμή την οποία θα πρέπει να την εκδώσετε'),
    'gks_mysubmit_credit_memo');
    return false;
  });
  window.gks_mysubmit_credit_memo = function() {
    mysubmit('credit_memo');
  }
  
  function mysubmit(pay_state = '') {
    if (from_php_perm_ret_edit==false) return;
    
    datasend='';
    datasend+='&gks_lock=' + (from_php_gks_lock ? '1' : '0');
    datasend+='&gks_number_lock=' + (from_php_number_gks_lock ? '1' : '0');
    datasend+='&gks_user_lock=' + (from_php_user_gks_lock ? '1' : '0');
    datasend+='&pay_state=' + encodeURIComponent($.base64.encode(pay_state));

    if (pay_state=='aade_send') {
      aade_mydata_live=($('#aade_mydata_live').is(':checked') ? 1 : 0);
      datasend+='&aade_mydata_live=' + aade_mydata_live;
    } else if (pay_state=='paroxos_send') {
      aade_mydata_live=($('#paroxos_mydata_live').is(':checked') ? 1 : 0);
      datasend+='&paroxos_mydata_live=' + aade_mydata_live;
    }

     
    if (from_php_gks_lock == false) {
      
      if ($("#company_id_sub_id").length > 0) datasend+='&company_id_sub_id=' + encodeURIComponent($.base64.encode($("#company_id_sub_id").val().trim()));
      if ($("#pay_acc_journal_id").length > 0) datasend+='&pay_acc_journal_id=' + encodeURIComponent($("#pay_acc_journal_id").val().trim());
      if ($("#pay_acc_seira_id").length > 0) datasend+='&pay_acc_seira_id=' + encodeURIComponent($("#pay_acc_seira_id").val().trim());
      if ($("#pay_acc_number_int").length > 0) datasend+='&pay_acc_number_int=' + encodeURIComponent($("#pay_acc_number_int").val().trim());
      datasend+='&pay_date=' + encodeURIComponent($("#pay_date").val().trim());
      datasend+='&user_id=' + encodeURIComponent($("#user_id").val().trim());
  
  
    }
    
    //datasend+='&price=' + encodeURIComponent($("#price").val().trim());
    datasend+='&note_doc=' + encodeURIComponent($.base64.encode($("#note_doc").val().trim()));
    datasend+='&note_logistirio=' + encodeURIComponent($.base64.encode($("#note_logistirio").val().trim()));
    


    datasend+='&mypropertiesheight=' + encodeURIComponent(window.scrollY);
    //console.log($('#divmyproperties').height());
    
    
    if (from_php_gks_lock == false) {
      var multiple_connected_marks_array=[];
      var multiple_connected_marks_cc=0;
      var multiple_connected_marks_has_error=false;
      $('.gks_multiple_connected_marks_item').each(function() {
        multiple_connected_marks_cc++;
        mcmaa=parseInt($(this).attr('data-mcmaa'));
        if (isNaN(mcmaa)) mcmaa=0;
        if (mcmaa>0) {
          recid = $(this).attr('data-recid');
          mcm_mark=$(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').val().trim();
          if (mcm_mark.startsWith('acc_inv#') || mcm_mark.startsWith('acc_pay#') || mcm_mark.startsWith('whi_mov#')) mcm_mark='';
          if (mcm_mark=='') {
            mcm_mark=$(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').attr('data-mcm_mark').trim();
          }
          mcm_acc_inv_id=parseInt($(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').attr('data-mcm_acc_inv_id'));
          mcm_acc_pay_id=parseInt($(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').attr('data-mcm_acc_pay_id'));
          mcm_whi_mov_id=parseInt($(this).find('.mcmitem_mark[data-mcmaa=' + mcmaa + ']').attr('data-mcm_whi_mov_id'));
          if (isNaN(recid)) recid=0;
          if (isNaN(mcm_acc_inv_id)) mcm_acc_inv_id=0;
          if (isNaN(mcm_acc_pay_id)) mcm_acc_pay_id=0;
          if (isNaN(mcm_whi_mov_id)) mcm_whi_mov_id=0;
          if (mcm_mark!='' || mcm_acc_inv_id>0 || mcm_acc_pay_id>0 || mcm_whi_mov_id>0) {
            itemmcmaa={};
            itemmcmaa.recid=recid;
            itemmcmaa.mcmaa=mcmaa;
            itemmcmaa.mcm_mark=mcm_mark;
            itemmcmaa.mcm_acc_inv_id=mcm_acc_inv_id;
            itemmcmaa.mcm_acc_pay_id=mcm_acc_pay_id;
            itemmcmaa.mcm_whi_mov_id=mcm_whi_mov_id;
            multiple_connected_marks_array.push(itemmcmaa);
          }
        }
      });
      //console.log(multiple_connected_marks_array);return;
      multiple_connected_marks_array_str = encodeURIComponent($.base64.encode(JSON.stringify(multiple_connected_marks_array)));
      datasend+='&multiple_connected_marks_array_str=' + multiple_connected_marks_array_str;
    } 
        
    if (from_php_gks_lock == false) {

      var eidi_array=[];
      $('.gks_price').each(function() {
        aa=parseInt($(this).attr('data-aa'));
        if (isNaN(aa)) aa=0;
        if (aa>0) {
          id_acc_pay_method = $('.gks_eidos[data-aa=' + aa + ']').attr('data-recid');
          paymethod_id = parseInt($('.gks_code[data-aa=' + aa + ']').val());
          paymethod_total= parseFloat($(this).val());
          paymethod_descr = $('.gks_descr[data-aa=' + aa + ']').val().trim();
          paymethod_comments = $('.gks_comments[data-aa=' + aa + ']').val().trim();
          
          asset_id=-1;
          elem_asset_id=$('.div_payment_type_multi_item_pos_terminal[data-aa=' + aa + ']');
          if (elem_asset_id.length==1) {
            asset_id=parseInt(elem_asset_id.attr('data-asset_id'));
          }
          
          
          if (isNaN(paymethod_total)) paymethod_total=0;
          if (isNaN(paymethod_id)) paymethod_id=0;
          if (isNaN(asset_id)) asset_id=0;

          
          if (paymethod_id<=0) paymethod_id=2;
          addthis=true;
          if (paymethod_id==2 && paymethod_descr=='' && paymethod_total==0) addthis=false;
          if (addthis) {
            item={};
            item.aa=aa;
            item.id_acc_pay_method=id_acc_pay_method;
            item.paymethod_id=paymethod_id;
            item.paymethod_total=paymethod_total;
            item.paymethod_descr=paymethod_descr;
            item.paymethod_comments=paymethod_comments;
            item.asset_id=asset_id;
            
            

    

            
            eidi_array.push(item);
          }
          
        }
        
      });
      eidi_array_str = encodeURIComponent($.base64.encode(JSON.stringify(eidi_array)));
      
      datasend+='&eidi_array_str=' + eidi_array_str;
      datasend+='&fields_change=' + encodeURIComponent($.base64.encode(JSON.stringify(fields_change)));
   
      var pay_poso=[];
      $('.pay_poso_for_invs').each(function() {
        val=parseFloat($(this).val());
        if (isNaN(val)) val=0;
        
        myfrom=$(this).attr('data-myfrom');
        recid=$(this).attr('data-recid');
        item={};
        item.i=recid;
        item.f=myfrom;
        item.v=val;
        pay_poso.push(item);
        
      });
      //console.log(pay_poso);
      pay_poso_str = encodeURIComponent($.base64.encode(JSON.stringify(pay_poso)));
      datasend+='&pay_poso_str=' + pay_poso_str;
      
    } else {
      var eidi_array=[];
      $('.gks_price').each(function() {
        aa=parseInt($(this).attr('data-aa'));
        if (isNaN(aa)) aa=0;
        if (aa>0) {
          id_acc_pay_method = $('.gks_eidos[data-aa=' + aa + ']').attr('data-recid');
          
          asset_id=-1;
          elem_asset_id=$('.div_payment_type_multi_item_pos_terminal[data-aa=' + aa + ']');
          if (elem_asset_id.length==1) {
            asset_id=parseInt(elem_asset_id.attr('data-asset_id'));
          }
          item={};
          item.id_acc_pay_method=id_acc_pay_method;
          item.asset_id=asset_id;
          eidi_array.push(item);
        }
        
      });
      eidi_array_str = encodeURIComponent($.base64.encode(JSON.stringify(eidi_array)));
      
      datasend+='&eidi_array_asset_str=' + eidi_array_str;
    }

    
    datasend+='&affect_balance=' + ($('#affect_balance').is(':checked') ? '1' : '0');
    datasend+='&affect_balance_all_poso=' + ($('#affect_balance_all_poso').is(':checked') ? '1' : '0');
    baltype=$('input[name=affect_balance_all_poso_type]:checked');
    if (baltype.css('display')=='none') baltype='';
    else {baltype=baltype.val(); if (baltype === undefined || baltype === null) d='';}
    datasend+='&affect_balance_all_poso_type=' + encodeURIComponent(baltype);
    datasend+='&affect_balance_poso=' + $('#affect_balance_poso').val();

    datasend+='&assigned_id='  + $("#mypostform #assigned_id").attr('data-id');
    if (from_php_GKS_CRM_ENABLE) {
      datasend+='&crm_channel_id='  + $("#mypostform #crm_channel_id").val();
      datasend+='&crm_channel_contact_id='  + $("#mypostform #crm_channel_contact_id").attr('data-id');
      datasend+='&crm_channel_campain_id='  + $("#mypostform #crm_channel_campain_id").attr('data-id');
      datasend+='&crm_channel_url='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_url").val().trim()));
      datasend+='&crm_channel_code='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_code").val().trim()));
      datasend+='&crm_channel_text='  + encodeURIComponent($.base64.encode($("#mypostform #crm_channel_text").val().trim()));
    }
        
    datasend+=gks_custom_datasend();
    
    //console.log(eidi_array);
    //console.log(eidi_array);
    //console.log(datasend);

    //console.log(datasend);
    //return;

    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-acc-pay-item-exec.php?id=' + from_php_id,
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
  					//myalert('ok:' + 'OK');
  					need_save=false;
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
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }  
  

  $('#myproperties').show();
  //console.log($('#divmyproperties').css('height'));
  //console.log($('#divmyproperties').height());
  
  //$('#divmyproperties').css('height','');
  //console.log($('#divmyproperties').height());

  

  
  
  
  $('#add_links_url').click(function(event) {  
    if (from_php_id<=0) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την πληρωμή'));
      return;
    }
    
    links_url=$("#links_url").val().trim();
    if (links_url=='') {
      myalert('error:'+gks_lang('Πληκτρολογήστε πρώτα την διεύθυνση'));
      return;
    }
    
    datasend='';
    datasend+='id=' + from_php_id;    
    datasend+='&link='  + encodeURIComponent($.base64.encode(links_url));    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-acc-pay-item-add-link.php',
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
  					//myalert('ok:' + 'OK');
  					myhtml=$.base64.decode(data.html);
  					//console.log(myhtml);
  					$('#tr_new_links_url').before(myhtml);

            $('#links_url').val('');
            $('#tr_links_url_' + data.trid).find('.deleterow').click(deleterow_click); 
  					$('#tr_links_url_' + data.trid).find('.download_action_start').click(download_action_start_click);

            var links_aa=0;
            $('#links_table .links_aa').each(function () {
              links_aa++;
              $(this).html(links_aa);  
            }); 


  					
  					//window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
  });  
  
  function gks_delete_eidos_click() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    aa=parseInt( $(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    $('.gks_eidos[data-aa=' + aa +']').remove(); 
 
    if ($('.gks_eidos').length ==0) {
      eidoi_add(false,0);  
    }
    
    $('#gks_products_count').html($('.gks_price').length);
    calc_pliroteo('delete', aa); 
    gks_myscroll();
  }
  
  $('.gks_delete_eidos').click(gks_delete_eidos_click);

  
  

  

  
  function gks_code_change() {
    //console.log('gks_code_change');
    aa=parseInt( $(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    
    $('.gks_descr[data-aa=' + aa + ']').val($(this).find('option:selected').text());

  }
  $('.gks_code').change(gks_code_change);
  


  
  
  function eidoi_add(fromloading,click_aa) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    last_aa++;
    //console.log('fromloading' + fromloading);
    
    var last_pp=0;
    $('.div_payment_type_multi_item ').each(function() {
      vvv=parseInt($(this).attr('data-pp'));
      if (isNaN(vvv)) vvv=0;
      if (vvv>last_pp) last_pp=vvv;
    });
    last_pp++;
    
    row_html=
          '<div class="form-group row gks_eidos " data-recid="0" data-aa="' + last_aa + '" data-pp="' + last_pp + '">' +
            '<div class="' + from_php_gkscols1 + '">';
                
    row_html+=  
              '<select class="div_payment_type_multi_item_select gks_code form-control form-control-sm" data-aa="' + last_aa + '" data-pp="' + last_pp + '">';

              for(pi=0;pi<from_php_paymethods_array.length;pi++) {
                row_html+='<option value="' + from_php_paymethods_array[pi].id + '"' +
                ' data-aade_id="' + from_php_paymethods_array[pi].aade_tropos_pliromis_id + '"' +
                ' data-payment_acquirer_with_id="' + from_php_paymethods_array[pi].payment_acquirer_with_id + '"' +
                '>' + from_php_paymethods_array[pi].descr + '</option>';
              }
              
    row_html+=  
              '</select>' +
            '</div>' + 
            '<div class="' + from_php_gkscols2 + '">' +
              '<div class="text-left">' + 
                '<textarea class="gks_descr form-control form-control-sm" rows="1" data-aa="' + last_aa + '" placeholder="'+gks_lang('Περιγραφή')+'">' +
                from_php_paymethods_array[0].descr +
                '</textarea>' + 
              '</div>' + 
            '</div>' +
            '<div class="' + from_php_gkscols3 + '">' +
              '<textarea class="gks_comments form-control form-control-sm" rows="1" data-aa="' + last_aa + '" placeholder="'+gks_lang('Σχόλιο')+'"></textarea>' + 
            '</div>';
            

    row_html+=
            '<div class="' + from_php_gkscols5 + '">' +
              '<div data-pp="' + last_pp + '" data-rec_id="0" class="div_payment_type_multi_item">' + 
                '<div class="div_payment_type_multi_item_row2" style="display:none;">' +
                  '<button data-pp="' + last_pp + '"' +
                    'class="btn btn-sm btn-primary div_payment_type_multi_item_pos_start">'+gks_lang('Πληρωμή με')+':</button>' +
                  '<input data-aa="' + last_aa + '" data-pp="' + last_pp + '" data-pawid="0" ' + 
                  'class="div_payment_type_multi_item_pos_terminal form-control form-control-sm" ' +
                  'type="text" placeholder="'+gks_lang('Τερματικό')+'" ' +
                  'data-asset_id="0" ' +
                  'value=""'+
                  '>' +
                  '<div class="div_payment_type_multi_item_pos_rest"></div>' +                
                
                '</div>' +
              '</div>' +
            '</div>';
            


    row_html+=
            '<div class="' + from_php_gkscols7 + '">' +
              '<input type="number" class="form-control form-control-sm gks_price" data-aa="' + last_aa + '" value="" style="text-align:right;" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" placeholder="'+gks_lang('Ποσό')+'">' +
            '</div>';

            
    row_html+=
            '<div class="' + from_php_gkscols8 + '">' +
              '<div class="text-center gks_icons">' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-arrows-alt-v sortorder_handle"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-plus-circle gks_add_eidos"  data-aa="' + last_aa + '"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-trash-alt gks_delete_eidos" data-aa="' + last_aa + '"></i>' +
                '</div>' +
              '</div>' +
            '</div>' +           
          '</div>';         
    

    

    if (click_aa<=0) {
      $('#eidi_footer1').before(row_html);
    } else {
      $('.gks_eidos[data-aa=' + click_aa + ']').after(row_html);
    }
    
    $('.gks_add_eidos').show();  
    $('.gks_delete_eidos').show();              




    $('.gks_code[data-aa=' + last_aa + ']').change(gks_code_change);
    $('.div_payment_type_multi_item_select[data-aa=' + last_aa + ']').change(div_payment_type_multi_item_select_change);
    $('.gks_descr[data-aa=' + last_aa + ']').on(mychange, gks_descr_keyup);
    $('.gks_descr[data-aa=' + last_aa + ']').on(mychange, gks_descr_change);
    $('.gks_comments[data-aa=' + last_aa + ']').keyup(gks_comments_keyup);
    $('.gks_comments[data-aa=' + last_aa + ']').on(mychange, gks_comments_change);
    $('.gks_price[data-aa=' + last_aa + ']').on(mychange, gks_price_change);

    $('.gks_add_eidos[data-aa=' + last_aa + ']').click(function() {gks_add_eidos_click(false,$(this));});
    $('.gks_delete_eidos[data-aa=' + last_aa + ']').click(gks_delete_eidos_click);

    $('.div_payment_type_multi_item_pos_terminal[data-pp=' + last_pp + ']').each(function() {
      if (typeof gks_div_payment_type_multi_item_pos_terminal_autocomplete === "function") {
        gks_div_payment_type_multi_item_pos_terminal_autocomplete($(this));
      }
    });
    $('.div_payment_type_multi_item_pos_start[data-pp=' + last_pp + ']').click(function() {
      div_payment_type_multi_item_pos_start_click($(this),'sale',from_php_id,0);
    });
  
    if (fromloading==false) {
      if (from_php_enter_order.length>0) {
        $('.' + from_php_enter_order[0] + '[data-aa=' + last_aa + ']').focus().select();
      } else {
//        elemset= $('.gks_set[data-aa=' + last_aa + ']');
//        if (elemset.length>0) 
//          $('.gks_set[data-aa=' + last_aa + ']').focus().select();
//        else
          $('.gks_code[data-aa=' + last_aa + ']').focus();
      }
      
    }
    
    
    $('#gks_products_count').html($('.gks_price').length);

    if (click_aa>0) {
      var mylist=[];
      $('.gks_eidos').each(function() {
        mylist.push($(this).attr('data-aa'));
      });
      eidi_table_sortable_after(mylist);
    }
    
    gks_myscroll();
  }
  



  
  var calc_pliroteo_xhr;
  var calc_pliroteo_timer=null;
  window.calc_pliroteo = function(field_name='', field_aa=-1, mycmd='', myfile='') {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    //console.log('calc_pliroteo ' + field_name + ' ' + field_aa + ' ' + mycmd + ' ' + myfile);
     
    check_vies_valid_wait_timer_stop();
    
    if(calc_pliroteo_xhr && calc_pliroteo_xhr.readyState != 4){
      calc_pliroteo_xhr.abort();
    }
    if (calc_pliroteo_timer!=null) clearTimeout(calc_pliroteo_timer);
    calc_pliroteo_timer=setTimeout(calc_pliroteo_run,400,field_name, field_aa, mycmd, myfile);
  }
  function calc_pliroteo_run(field_name='', field_aa=-1, mycmd='', myfile='') {
    $('#calc_hourglass').show();
    
    
    
    if (from_php_gks_lock) {
      
      mydata={};
      mydata.gks_lock=true;
      mydata.mycmd=mycmd;
      mydata.myfile=myfile;

      
      
      mydata_str = encodeURIComponent($.base64.encode(JSON.stringify(mydata)));
      datasend='&mydata_str=' + mydata_str;

    } else {


      if (field_aa>=1 && (field_name=='gks_price')) {
        fields_change[field_aa]=field_name;
      }
      
      
      
      

      var gks_total_price_net=0;
      var gks_total_price_total=0;
      var gks_total_price_original_net=0;
      var gks_products_need_apostoli=0;
  
      
      company_id=0;
      company_sub_id=0;
      v=$('#company_id_sub_id').val();
      if (v === undefined || v === null) v='';
      parts=v.split('|');
      if (parts.length==2) {
        company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
        company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
      }
  
      
      mydata={};
  
      //mydata.gks_number_lock=from_php_number_gks_lock;
      //mydata.gks_user_lock=from_php_user_gks_lock;
      mydata.mycmd=mycmd;

      mydata.mycmd=mycmd;
      mydata.myfile=myfile;
      mydata.company_id=company_id;
      mydata.company_sub_id=company_sub_id;
      mydata.pay_acc_journal_id = $('#pay_acc_journal_id').val();
      mydata.pay_acc_seira_id = $('#pay_acc_seira_id').val();
      mydata.pay_state = from_php_pay_state;
      mydata.pay_date = $('#pay_date').val();
      mydata.user_id = $('#user_id').val();
      
      mydata.afm = $('#dr_user_afm').html();
      mydata.ma_country_id = $('#dr_user_ma_country_id').attr('data-id');

      
      
      
      
      mydata.balance_pros=from_php_eidos_parastatikou_balance_pros;
 
      
      mydata.gks_total_price_net=gks_total_price_net;
      mydata.gks_total_price_total=gks_total_price_total;
     
  
      //console.log(mydata);
      
  
      var eidi_array=[];
      $('.gks_price').each(function() {
        aa=parseInt($(this).attr('data-aa'));
        if (isNaN(aa)) aa=0;
        if (aa>0) {
          id_acc_pay_method = $('.gks_eidos[data-aa=' + aa + ']').attr('data-recid');
          paymethod_id = parseInt($('.gks_code[data-aa=' + aa + ']').val());
          paymethod_total= parseFloat($(this).val());
          paymethod_descr = $('.gks_descr[data-aa=' + aa + ']').val().trim();
          paymethod_comments = $('.gks_comments[data-aa=' + aa + ']').val().trim();
         
          
          
          if (isNaN(paymethod_total)) paymethod_total=0;
           
           
          if (isNaN(paymethod_id)) paymethod_id=0;
          

     

                  
          if (paymethod_id<=0) paymethod_id=2;
          addthis=true;
          if (paymethod_id==2 && paymethod_descr=='' && paymethod_total==0) addthis=false;
          if (addthis) {
            item={};
            item.aa=aa;
            item.id_acc_pay_method=id_acc_pay_method;
            item.paymethod_id=paymethod_id;
            item.paymethod_total=paymethod_total;
            item.paymethod_descr=paymethod_descr;
            item.paymethod_comments=paymethod_comments;
    


            
            eidi_array.push(item);
          }
          
        }
      });
      
      
      mydata.eidi_array=eidi_array;
      mydata.fields_change=fields_change;
      mydata.fields_change_curr_name=field_name;
      mydata.fields_change_curr_aa=field_aa;
      
      mydata_str = encodeURIComponent($.base64.encode(JSON.stringify(mydata)));
      datasend='&mydata_str=' + mydata_str;
      
    }
    
    //console.log('datasend ['+ field_name + '] [' + field_aa + ']');
    //console.log(eidi_array);
    //console.log('calc_pliroteo');
    
    calc_pliroteo_xhr = $.ajax({
			url: '/my/admin-acc-pay-item-calc-basket.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_field_name:field_name,
			gks_field_aa:field_aa,
			gks_mycmd:mycmd,
			gks_myfile:myfile,
			error : function(jqXHR ,textStatus,  errorThrown) {
				if (textStatus != 'abort') {
				  myalert('error:' + jqXHR.responseText);
				  $('#calc_hourglass').hide();
				}
			},				
			success: function(data) {
			  need_save=true;
			  $('#calc_hourglass').hide();
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					//myalert('ok:' + 'OK');
  					//console.log('response');
  					//console.log(data.eidi[0]);
  					
  					if (from_php_gks_lock==false) {
    					$('#gks_total_price_total').html(data.gks_price_total).attr('data-val',data.gks_price_total_val);
    					$('#bal_gks_total_price_total').html(data.gks_price_total).attr('data-val',data.gks_price_total_val);
    					
    					
    					timer_pist_orange_check();
    					pay_split();
    				}
    				
  				  if (data.check_vies.views_run_img!='') {
              $('#dr_user_afm_views_run_static').html(data.check_vies.views_run_img).css('visibility','visible');
              $('#dr_user_afm_views_run_static .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
              if (data.check_vies.valid==2) check_vies_valid_wait_timer_restart();
            } else {
              $('#dr_user_afm_views_run_static').css('visibility','hidden');
            }
            
            if (data.check_vies.run) {
              $('#dr_user_afm_views_run_static').show();
            } else {
              $('#dr_user_afm_views_run_static').hide();
            }
  				  

  					pay_poso_for_invs_change();
  				  balance_user_after_calc();
  				  

  					
  					gks_myscroll();
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
  }
  
  

  
    
  function next_enter_field_fnc(aa,fieldfrom,faultback) {
    //console.log('next_enter_field_fnc',aa,fieldfrom,faultback);
    
    if (control_enter_active) return;
    for (i=0; i<from_php_enter_order.length; i++) {
      if (from_php_enter_order[i]==fieldfrom) {
        if (i < (from_php_enter_order.length - 1)) {
          if (from_php_enter_order[i+1]=='new_row') {
            elemnext=$('.gks_code[data-aa=' + (aa+1) + ']');
            if (elemnext.length>0) {
              elem=$('.' + from_php_enter_order[0] + '[data-aa=' + (aa+1) + ']');
              if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
              else elem.focus().select();
            } else {
              $('.gks_add_eidos[data-aa=' + aa + ']').click();
            }
          } else {
            elem=$('.' + from_php_enter_order[i+1] + '[data-aa=' + aa + ']');
            if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
            else elem.focus().select();
          }
          return;
        }
      }
    }
    if (faultback=='new_row') {
      elemnext=$('.gks_code[data-aa=' + (aa+1) + ']');
      if (elemnext.length>0) {
        if (from_php_enter_order.length>0) {
          elemnextuo=$('.' + from_php_enter_order[0] + '[data-aa=' + (aa+1) + ']');
          if (elemnextuo.prop('nodeName')=='TEXTAREA') elemnextuo.focus();
          else elemnextuo.focus().select();
        } else {
//          elemnext_set=$('.gks_set[data-aa=' + (aa+1) + ']');
//          if (elemnext_set.length>0) {
//            if (elemnext_set.prop('nodeName')=='TEXTAREA') elemnext_set.focus();
//            else elemnext_set.focus().select();
//          } else {
            if (elemnext.prop('nodeName')=='TEXTAREA') elemnext.focus();
            else elemnext.focus().select();
//          }
        }
      } else {
        $('.gks_add_eidos[data-aa=' + aa + ']').click();
      }
    } else if (faultback!='') {
      elem=$('.' + faultback + '[data-aa=' + aa + ']');
      if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
      else elem.focus().select();
    }
  }
  

  
  function gks_descr_keyup (event) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        val=$(this).val();
        //console.log('val',val);
        if (val == '\n' || val.endsWith('\n\n')) {
          val=val.replace(/\n+$/, '');
          $(this).val(val);
          event.preventDefault();  
          aa=parseInt($(this).attr('data-aa'));
          if (isNaN(aa)) aa=0;
          if (aa<=0) return;
          next_enter_field_fnc(aa,'gks_descr','gks_comments');
          return;
        }
      }
    }
  }
  $('.gks_descr').keyup(gks_descr_keyup);
  
  function gks_descr_change() {gks_resize_textarea($(this));}
  $('.gks_descr').on(mychange, gks_descr_change);
  $('.gks_descr').each(function() {gks_resize_textarea($(this));});
  
  function gks_comments_keyup (event) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        val=$(this).val();
        //console.log('val',val);
        if (val == '\n' || val.endsWith('\n\n')) {
          val=val.replace(/\n+$/, '');
          $(this).val(val);
          event.preventDefault();  
          aa=parseInt($(this).attr('data-aa'));
          if (isNaN(aa)) aa=0;
          if (aa<=0) return;
          next_enter_field_fnc(aa,'gks_comments','gks_price');
          return;
        }
      }
    }
  }
  $('.gks_comments').keyup(gks_comments_keyup);
  
  function gks_comments_change() {gks_resize_textarea($(this));}
  $('.gks_comments').on(mychange, gks_comments_change);
  $('.gks_comments').each(function() {gks_resize_textarea($(this));});
  



  
   

  function gks_price_change(event) {
    //console.log(event);
    
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    event.preventDefault();  
    aa_start=parseInt($(this).attr('data-aa'));
    if (isNaN(aa_start)) aa_start=0;
    if (aa_start<=0) return;

    aa=aa_start;
    if (event != undefined && event.which != undefined && event.which == 13) {
      event.preventDefault();   
      next_enter_field_fnc(aa,'gks_price','new_row');
      return;
    }
    calc_pliroteo('gks_price',aa_start);
    gks_myscroll();
  }
  
  
  $('.gks_price').on(mychange, gks_price_change);
  


  
  function gks_add_eidos_click(fromloading,elem) {
    aa=elem.attr('data-aa');
    eidoi_add(fromloading,aa);
  }
    
  $('.gks_add_eidos').click(function() {gks_add_eidos_click(false,$(this));});

  $('#eidi_table').sortable({
    items: '.gks_eidos',
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-aa'});
      eidi_table_sortable_after(mylist);
    }
  });
  
  function eidi_table_sortable_after(mylist) {
    //console.log(mylist);
    $('#eidi_table > .gks_eidos').each(function() {
      aa=$(this).attr('data-aa');
      $(this).attr('data-aa_temp',aa);
    });
    $('#eidi_table > .gks_eidos').each(function() {
      aa=$(this).attr('data-aa_temp');
      new_aa=-1;
      for(i=0;i<mylist.length;i++) {
        if (mylist[i]==aa) {
          new_aa=i;break;
        }
      }
      //console.log('new_aa',new_aa);
      if (new_aa>=0) {
        new_aa++
        $(this).attr('data-aa',new_aa);
        $(this).find('*[data-aa=' + aa + ']').attr('data-aa',new_aa);
      }
      
    })      
  }  

  
  

  $('#user').keyup(function (event) {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    event.preventDefault();  
    if (event != undefined && event.which != undefined && event.which == 13) {
      //$('#order_occasion').focus().select();
      return;
    }
  });
   
    
  

  $('#copy_text_pelati_sxolio_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#text_pelati_sxolio').text();
    exit_text=$('#note_logistirio').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#note_logistirio').val(exit_text);
    $('#note_logistirio').focus();
  });  


  $('#copy_text_order_sxolio_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#text_order_sxolio').text();
    exit_text=$('#note_logistirio').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#note_logistirio').val(exit_text);
    $('#note_logistirio').focus();
  });


  $('#copy_text_notes_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#notes').val();
    exit_text=$('#note_logistirio').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#note_logistirio').val(exit_text);
    $('#note_logistirio').focus();
  });
  
 
  $('#copy_text_subnotes_to_logistirio').click(function() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    mytext=$('#subnotes').val();
    exit_text=$('#note_logistirio').val();
    if (exit_text!='') exit_text+="\r\n";
    exit_text+=mytext;
    $('#note_logistirio').val(exit_text);
    $('#note_logistirio').focus();
  });
    

  

  


  
  
  
  function download_action_start_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id==0) return;
    download_action_func(data_id,'start');
  }
  $('.download_action_start').click(download_action_start_click);
  
  function download_action_stop_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id==0) return;
    download_action_func(data_id,'stop');
  }
  $('.download_action_stop').click(download_action_stop_click);

  function download_action_reset_click() {
    data_id=parseInt($(this).attr('data-id'));
    if (isNaN(data_id)) data_id=0;
    if (data_id==0) return;
    download_action_func(data_id,'reset');
  }
  $('.download_action_reset').click(download_action_reset_click);

  $('.download_action_complete').click(download_action_reset_click);
  
  
  function download_action_func(data_id, myaction) {
    //console.log(data_id);
    //console.log(myaction);

    datasend='&id=' + data_id + '&action=' + myaction;
    
    $.ajax({
			url: '/my/admin-acc-pay-item-link-action.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
  					//console.log(data);
  					if (data.action=='start') {
  					  $('.download_file_td[data-id=' + data.id + ']').html('<i class="fas fa-stop-circle download_action_stop" data-id="' + data.id + '" style="font-size:200%;vertical-align:middle;color:red;cursor:pointer;"></i>'); 
  					  $('.download_action_stop[data-id=' + data.id + ']').click(download_action_stop_click);
  					  $('.download-perc[data-id=' + data.id + ']').show();
  					  
  					  download_timer_start();
  					  
  					} else if (data.action=='stop') {
  					  $('.download_file_td[data-id=' + data.id + ']').html('<i class="fas fa-undo download_action_reset" data-id="' + data.id + '" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>'); 
  					  $('.download_action_reset[data-id=' + data.id + ']').click(download_action_reset_click);
  					  $('.download-perc[data-id=' + data.id + ']').hide();
  					  $('.download-message[data-id=' + data.id + ']').html(gks_lang('Ακυρώθηκε από τον χρήστη')).show();
  					  download_timer_start();
  					} else if (data.action=='reset') {
  					  $('.download_file_td[data-id=' + data.id + ']').html('<i class="fas fa-file-download download_action_start" data-id="' + data.id + '" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>'); 
  					  $('.download_action_start[data-id=' + data.id + ']').click(download_action_start_click);
  					  $('.download-perc[data-id=' + data.id + ']').hide();
  					  $('.download-message[data-id=' + data.id + ']').hide();
  					  download_timer_start();
  					  
  					  
  					}
  					
  					
  					
  					//window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
  }
  
  var download_timer=null;
  
  
  function download_timer_run() {
    fff=new Date();
    //console.log(fff);
    $.ajax({
			url: '/my/admin-acc-pay-item-link-timer.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: '',
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
				download_timer = setTimeout(download_timer_run, 2000);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					download_timer = setTimeout(download_timer_run, 2000);
				} else {
				  
					if (data.success == true) {
  					//console.log(data);
            for(i=0;i<data.data.length;i++) {
              $('.download_size_until_now[data-id=' + data.data[i].id + ']').html(data.data[i].now);
              if (data.data[i].status==2) { //ok exei teleiosei
                elem_stop=$('.download_action_stop[data-id=' + data.data[i].id + ']');
                if (elem_stop.length == 1) {
                  $('.download-perc[data-id=' + data.data[i].id + ']').hide();
                  $('.download_file_td[data-id=' + data.data[i].id + ']').html('<i class="fas fa-check-circle download_action_complete" data-id="' + data.data[i].id + '" style="font-size:200%;vertical-align:middle;color:green;cursor:pointer;"></i>');
      					  $('.download-message[data-id=' + data.data[i].id + ']').hide();
      					  $('.download_action_complete[data-id=' + data.data[i].id + ']').click(download_action_reset_click);
                }
              } else if (data.data[i].status==1) { //status =1
                $('.download-perc-bar[data-id=' + data.data[i].id + ']').attr('aria-valuenow', data.data[i].per).css('width', data.data[i].per + '%');
              } else if (data.data[i].status==3) { 
                elem_stop=$('.download_action_stop[data-id=' + data.data[i].id + ']');
                if (elem_stop.length == 1) {
      					  $('.download_file_td[data-id=' + data.data[i].id + ']').html('<i class="fas fa-undo download_action_reset" data-id="' + data.data[i].id + '" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>'); 
      					  $('.download_action_reset[data-id=' + data.data[i].id + ']').click(download_action_reset_click);
      					  $('.download-perc[data-id=' + data.data[i].id + ']').hide();
      					  $('.download-message[data-id=' + data.data[i].id + ']').show();  
      					  $('.download-message[data-id=' + data.data[i].id + ']').html(data.data[i].msg);
      					}
              }
            }

  					
  					
  					//for(i=0;i<data.complete_td.length;i++) {
  					for(i=0;i<data.complete_td.length;i++) {
  					  if ($('.tddd[data-path="' + data.complete_td[i].relpath + '"]').length==0) {
  					    //console.log('|' + data.complete_td[i].relpath + '|');
  					    $('#filesobjectlist_table_imagelist_photo').show();
                //$('#filesobjectlist_table_imagelist_photo tr:last').after(data.complete_td[i].td);
                if ($('#filesobjectlist_table_imagelist_photo tbody tr').length==0) {
                  $('#filesobjectlist_table_imagelist_photo tbody').append(data.complete_td[i].td);
                } else {
                  $('#filesobjectlist_table_imagelist_photo tbody tr:last').after(data.complete_td[i].td);
                }                
                
                $('.filesobjectlist_delete_upload_photo[data-path="' + data.complete_td[i].relpath + '"]').click(filesobjectlist_delete_upload_photo_click);
                $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.complete_td[i].relpath + '"] td.tdimg_descr').click(filesobjectlist_edit_descr_click);
                $('.filesobjectlist_set_print_photo[data-path="' + data.complete_td[i].relpath + '"]').click(filesobjectlist_set_print_photo_click);
                $('#filesobjectlist_table_imagelist_photo tr.tddd[data-path="' + data.complete_td[i].relpath + '"] .filesobjectlist_set_public_file').click(filesobjectlist_set_public_file_click);
  
                $("#filesobjectlist_table_imagelist_photo").data('lightGallery').destroy(true);
                $("#filesobjectlist_table_imagelist_photo").lightGallery({
                	selector: '.filesobjectlist_lightgallery_gks_fileserver_item',
                	thumbnail:true,
                	hideBarsDelay:1000,
                });   					    
  					  }		  
  					}

  					if (data.data.length<=0) {
  					  download_timer_stop();
  					} else {
  					  //setTimeout(, 3000);
  					  download_timer = setTimeout(download_timer_run, 2000);
  					}  					  					
  					//console.log(data);
  					  
					} else {
					  download_timer = setTimeout(download_timer_run, 2000);
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
  }
  
  function download_timer_stop() {
    clearTimeout(download_timer);
    download_timer=null;
  }
  function download_timer_start() {
    if (download_timer!=null) return;
    //download_timer = setInterval(download_timer_run, 1000);
    download_timer = setTimeout(download_timer_run, 2000);
    
  }
  if (from_php_need_download_timer==1) {
    download_timer_start();
  }
  
  
  

  
  
  

  
 

 
  

  
  

  function gks_admin_get_user_data(user_id, dialog_gsis_result=false) {
    
    $('#dr_user_afm_views_run_static').css('visibility','hidden');
    
    datasend='cmd=get&id=' + user_id + '&acc_pay_id=' + from_php_id;
    $.ajax({
			url: 'admin-get-user-data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_dialog_gsis_result:dialog_gsis_result,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  need_save=true;
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  //console.log('gks_admin_get_user_data res');
					  //console.log(data);
					  //console.log(this.gks_dialog_gsis_result);
					  
            if (data.pelati_sxolio=='') {
              $('#div_pelati_sxolio').hide('fade', 'slow');
              $('#text_pelati_sxolio').html('');
            } else {
              $('#div_pelati_sxolio').show('fade', 'slow');
              $('#text_pelati_sxolio').html(data.pelati_sxolio);
            }
            if (data.order_sxolio=='') {
              $('#div_order_sxolio').hide('fade', 'slow');
              $('#text_order_sxolio').html('');
            } else {
              $('#div_order_sxolio').show('fade', 'slow');
              $('#text_order_sxolio').html(data.order_sxolio);
              
              mytext=$('#text_order_sxolio').text();

              //$('#note_production').focus();
            }
            
            $('#dr_user_first_name').html(data.first_name);
            $('#dr_user_last_name').html(data.last_name);
            $('#dr_user_email_div2').html(data.email_link);
            html_phone=(data.def_phone_link!='' ? data.def_phone_link : (data.phone_home_link!='' ? data.phone_home_link : data.mobile_link));
            html_phone=html_phone.replace('<a ','<a class="'+from_php_gks_voip_params.class_span+'" ');
            html_phone+=from_php_gks_voip_params.html_after_span;
            $('#dr_user_mobile').html(html_phone);
            $('#dr_user_mobile .gks_voip_originate_after_span').click(gks_voip_originate_click);

            $('#dr_user_lang').html(data.lang_name).attr('data-val',data.lang);
            
   

            //console.log('gks_dialog_gsis_result false');
            $('#dr_user_ma_odos').html(data.ma_odos);
            $('#dr_user_ma_arithmos').html(data.ma_arithmos);
            $('#dr_user_ma_orofos').html(data.ma_orofos);
            $('#dr_user_ma_perioxi').html(data.ma_perioxi);
            $('#dr_user_ma_poli').html(data.ma_poli);
            $('#dr_user_ma_tk').html(data.ma_tk);
            $('#dr_user_ma_country_id').html(data.country_name).attr('data-id',data.ma_country_id);
            $('#dr_user_ma_nomos_id').html(data.nomos_descr).attr('data-id',data.ma_nomos_id);
            $('#dr_user_afm_ee_initial_static').html(data.country_ee);
            if (data.country_ee=='') $('#dr_user_afm_ee_initial_static').hide(); else $('#dr_user_afm_ee_initial_static').show();
            
            
            
            $('#dr_user_eponimia').html(data.eponimia);
            $('#dr_user_title').html(data.title);
            $('#dr_user_afm').html(data.afm);
            $('#dr_user_doy').html(data.doy);
            $('#dr_user_epaggelma').html(data.epaggelma);
         
            
            $('#balance_user_before').html(data.balance_user_before.mymoney()).attr('data-val',data.balance_user_before);
            balance_user_after_calc();
            
            
            payment_is_for_invs();
            gks_myscroll();
            calc_pliroteo(); 
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});     
    
  }  
  

  
  
  

  


  

  
  

 
    

  
  

 
  
  window.company_id_sub_id_change = function company_id_sub_id_change() {
    company_id=0;
    company_sub_id=0;
    v=$('#company_id_sub_id').val();
    if (v === undefined || v === null) v='';
    parts=v.split('|');
    if (parts.length==2) {
      company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
      company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
    }
    pay_acc_journal_id_fill('pay_acc_journal_id','pay_acc_seira_id',company_id,company_sub_id,0);
    
    fields_change_set_pososto();
    gks_myscroll();
    calc_pliroteo(); 
    
  }
  $('#company_id_sub_id').change(company_id_sub_id_change);
  
  if (from_php_id == -1 && from_php_template_id==0) { 
    temp=$('#company_id_sub_id').val();
    if (temp!='' && temp!='0|0') {
      company_id_sub_id_change();
    } else if ($('#company_id_sub_id option').length==2) {
      $('#company_id_sub_id').val($($('#company_id_sub_id option')[1]).attr('value'));
      company_id_sub_id_change();
    }
  }
    
  



  window.pay_acc_journal_id_change = function pay_acc_journal_id_change() {
    v=$('#pay_acc_journal_id').val();
    acc_journal_id=parseInt(v); if (isNaN(acc_journal_id)) acc_journal_id=0; 
    pay_acc_seira_id_fill('pay_acc_seira_id',acc_journal_id,0);
    
    from_php_acc_eidos_parastatikou_id=parseInt($('#pay_acc_journal_id option:selected').attr('data-eidi_id'));
    from_php_eidos_parastatikou_type_id=parseInt($('#pay_acc_journal_id option:selected').attr('data-type_id'));
    from_php_eidos_parastatikou_need_prev=parseInt($('#pay_acc_journal_id option:selected').attr('data-need_prev'));
    from_php_eidos_parastatikou_balance_pros=parseInt($('#pay_acc_journal_id option:selected').attr('data-balance_pros'));
    from_php_acc_eidos_parastatikou_other_entity=parseInt($('#pay_acc_journal_id option:selected').attr('data-other_entity'));
    from_php_journal_has_correlated_invoices=parseInt($('#pay_acc_journal_id option:selected').attr('data-correlated_invoices'));
    from_php_journal_has_multiple_connected_marks=parseInt($('#pay_acc_journal_id option:selected').attr('data-multiple_connected_marks'));
    
    if (isNaN(from_php_acc_eidos_parastatikou_id)) from_php_acc_eidos_parastatikou_id=0;
    if (isNaN(from_php_eidos_parastatikou_need_prev)) from_php_eidos_parastatikou_need_prev=0;
    if (isNaN(from_php_eidos_parastatikou_balance_pros)) from_php_eidos_parastatikou_balance_pros=0;
    if (isNaN(from_php_acc_eidos_parastatikou_other_entity)) from_php_acc_eidos_parastatikou_other_entity=0;
    if (isNaN(from_php_journal_has_correlated_invoices)) from_php_journal_has_correlated_invoices=0;
    if (isNaN(from_php_journal_has_multiple_connected_marks)) from_php_journal_has_multiple_connected_marks=0;

    if (from_php_eidos_parastatikou_balance_pros==0) {
      $('#myypoloipoepafis_card').addClass('myypoloipoepafis_card_notactive');
    } else {
      $('#myypoloipoepafis_card').removeClass('myypoloipoepafis_card_notactive');
    }
    
    antisimvalomenos_label=gks_lang('αντισυμβαλλόμενος');
    for(i=0; i < eidi_parastatikon_types.length; i++) {
      if (eidi_parastatikon_types[i].id== from_php_eidos_parastatikou_type_id) {
        antisimvalomenos_label=eidi_parastatikon_types[i].label;
        break; 
      }
    }
    $('#antisimvalomenos_label').html(antisimvalomenos_label);

    if (from_php_acc_eidos_parastatikou_other_entity==0) {
      $('#div_other_entity').hide();
    } else {
      $('#div_other_entity').show();
    }
    if (from_php_journal_has_correlated_invoices==0) {
      $('#div_correlated_invoices').hide();
    } else {
      $('#div_correlated_invoices').show();
    }
    if (from_php_journal_has_multiple_connected_marks==0) {
      $('#div_multiple_connected_marks').hide();
    } else {
      $('#div_multiple_connected_marks').show();
    }
    
  
    
    payment_is_for_invs();
    


    gks_myscroll();
    calc_pliroteo();    
  }
  $('#pay_acc_journal_id').change(pay_acc_journal_id_change);
 

  
  
  window.pay_acc_seira_id_change = function pay_acc_seira_id_change() {
    acc_seira_id=parseInt($('#pay_acc_seira_id').val()); if (isNaN(acc_seira_id)) acc_seira_id=0; 
    is_xeirografi=parseInt($('#pay_acc_seira_id option:selected').attr('data-is_xeirografi')); if (isNaN(is_xeirografi)) is_xeirografi=0; 
    if (is_xeirografi!=0) {
      $('#pay_acc_number_int').prop('disabled' , false);
      $('#submit_button_080listing').show();
      $('#submit_button_090ekdosi').hide();
    } else {
      $('#pay_acc_number_int').prop('disabled' , true);
      $('#submit_button_080listing').hide();
      $('#submit_button_090ekdosi').show();
    }
  }
  $('#pay_acc_seira_id').change(pay_acc_seira_id_change);


  

  
  

  
  

  
  
 
  
  
 


  $.fn.animateRotate = function(start,angle, duration, easing, complete) {
    var args = $.speed(duration, easing, complete);
    var step = args.step;
    return this.each(function(i, e) {
      args.complete = $.proxy(args.complete, e);
      args.step = function(now) {
        $.style(e, 'transform', 'rotate(' + now + 'deg)');
        if (step) return step.apply(e, arguments);
      };
  
      $({deg: start}).animate({deg: angle}, args);
    });
  };
     

   

  
  $('input[name=form_idio_afm]').click(function() {
    if ($(this).val() ==0) {
      $('#div_show_user').hide();  
    } else {
      $('#div_show_user').show();
    }
    need_save=true;
    gks_myscroll();
    calc_pliroteo();
  });  
  
  
  //gks_myscroll();


  function fields_change_set_pososto() {

  }  
  

  
  
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
        html: '<i class="fa fa-print"></i> '+gks_lang('Προεπισκόπηση'),
        //icon: "ui-icon-print",  
        click: function() {
          dialog_print_button(true);
        }
      },
      {
        id: "dialog_print_ok",
        html: '<i class="fa fa-print"></i> '+gks_lang('Εκτύπωση'),
        //icon: "ui-icon-print",
        click: function() {
          dialog_print_button(false);
        }
      },
      {
        id: "dialog_print_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'),
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
        '<label for="dialog_print_set_def" style="cursor:pointer;margin: 0px;margin-right: 20px;">'+gks_lang('Αποθήκευση ως προεπιλογές')+'</label>' +
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
      myalert('error:'+gks_lang('Επιλέξτε την φόρμα εκτύπωσης'));
      return;
    }
    form_id=parseInt(elem_sel.attr('data-form_id'));
    if (isNaN(form_id)) form_id=0;
    datasend+='&form_id=' + form_id;
    
    
    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    $.ajax({
			url: 'admin-acc-pay-item-pdf.php?id=' + from_php_id,
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
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την πληρωμή'));
      return;
    }
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
    gks_paroxos_send_pdf_ed();
    
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
    sel_pay_acc_journal_id=parseInt($('#pay_acc_journal_id').val()); if (isNaN(sel_pay_acc_journal_id)) sel_pay_acc_journal_id=0;
    sel_pay_acc_seira_id=parseInt($('#pay_acc_seira_id').val()); if (isNaN(sel_pay_acc_seira_id)) sel_pay_acc_seira_id=0;


    var temp=$('#dr_user_lang').attr('data-val');
    if (temp=='') temp='el-GR';
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
                if (from_php_perm_print_forms[i].perm_acc_journal_ids.includes(sel_pay_acc_journal_id)==false) {
                  will_show=false;
                  break;
                }
              }
              if (typeof(from_php_perm_print_forms[i].perm_acc_seires_ids) != 'undefined') {
                if (from_php_perm_print_forms[i].perm_acc_seires_ids.includes(sel_pay_acc_seira_id)==false) {
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
    gks_paroxos_send_pdf_ed();
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
  
  $('#dr_user_doy').autocomplete({
    source: "doy-autocomplete.php",
    minLength: 1,
    autoFocus: true,
    select: function( event, ui ) {
      $("#dr_user_doy").val(ui.item.value);
    },
  });  
  
  var dialog_aade;
  dialog_aade = $('#dialog_aade').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_aade_go",
        html: '<i class="fa fa-cloud-upload-alt"></i> '+gks_lang('Αποστολή'),
        //icon: "ui-icon-print",  
        click: function() {
          mysubmit('aade_send'); 
        }
      },
      {
        id: "dialog_aade_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
  });
  
  $('#submit_button_aade_send').click(function(event) {
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Ενημερώστε πρώτα το παραστατικό'));
      return;
    }
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 600) dwidth=600;
	  if (dheight> 400) dheight=400;
	  dialog_aade.dialog('option', 'width', dwidth);
	  dialog_aade.dialog('option', 'height', dheight);
	  $('#dialog_aade').parent().css({position:'fixed'});      
    dialog_aade.dialog('open'); 

  });

  var dialog_paroxos;
  dialog_paroxos = $('#dialog_paroxos').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_paroxos_go",
        html: '<i class="fa fa-cloud-upload-alt"></i> '+gks_lang('Αποστολή'),
        //icon: "ui-icon-print",  
        click: function() {
          mysubmit('paroxos_send'); 
        }
      },
      {
        id: "dialog_paroxos_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
  });
  
  $('#submit_button_paroxos_send').click(function(event) {
    if (from_php_id<=0 || need_save) {
      myalert('error:'+gks_lang('Ενημερώστε πρώτα το παραστατικό'));
      return;
    }
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 600) dwidth=600;
	  if (dheight> 400) dheight=400;
	  dialog_paroxos.dialog('option', 'width', dwidth);
	  dialog_paroxos.dialog('option', 'height', dheight);
	  $('#dialog_paroxos').parent().css({position:'fixed'});      
    dialog_paroxos.dialog('open'); 

  });
  

  

  var timer_pist_orange=null;
  function timer_pist_orange_check() {
    if (from_php_is_credit_memo==false) return;
    //console.log('timer_pist_orange_check');
    tnet=parseFloat($('#gks_total_price_total').attr('data-val'));
    rnet=parseFloat($('#rest_gks_price_total_sum').attr('data-val'));
    if (isNaN(tnet)) tnet=0;
    if (isNaN(rnet)) rnet=0;
    
    if (tnet>rnet) {
      if (timer_pist_orange == null) {
        timer_pist_orange = setInterval(function () {
          //console.log('timer_pist_orange');
          if ($('#gks_total_price_total').hasClass('span_bg_orange')) {
            $('#gks_total_price_total').removeClass('span_bg_orange');
            $('#rest_gks_price_total_sum').removeClass('span_bg_orange');
          } else {
            $('#gks_total_price_total').addClass('span_bg_orange');
            $('#rest_gks_price_total_sum').addClass('span_bg_orange');
          }
        }, 1000);        
      } 
    } else {
      if (timer_pist_orange != null) {
        clearTimeout(timer_pist_orange);
        timer_pist_orange=null;
        $('#gks_total_price_total').removeClass('span_bg_orange');
        $('#rest_gks_price_total_sum').removeClass('span_bg_orange');
      }
      
    }
    

    
  }
  if (from_php_is_credit_memo) {
    timer_pist_orange_check();
  }

  $('#affect_balance').change(function() {
    if ($('#affect_balance').is(':checked')) {
      $('#div_affect_balance_all_poso').show();
      if ($('#affect_balance_all_poso').is(':checked')) {
        $('#div_affect_balance_poso').hide();
        $('#small_affect_balance_all_poso').show();
      } else {
        $('#div_affect_balance_poso').show();
        $('#small_affect_balance_all_poso').hide();
      }
    } else {
      $('#div_affect_balance_all_poso').hide();
      $('#div_affect_balance_poso').hide();
    }
    balance_user_after_calc();
  });
  $('#affect_balance_all_poso').change(function() {
    if ($('#affect_balance_all_poso').is(':checked')) {
      $('#div_affect_balance_poso').hide();
      $('#small_affect_balance_all_poso').show();
    } else {
      $('#div_affect_balance_poso').show();
      $('#small_affect_balance_all_poso').hide();
    }
    balance_user_after_calc();
  });
  $('input[name=affect_balance_all_poso_type]').change(balance_user_after_calc);

  $('#affect_balance_poso').on(mychange ,balance_user_after_calc);
    
  function balance_user_after_calc() {
    before=parseFloat($('#balance_user_before').attr('data-val'));
    if (isNaN(before)) before=0;
    after=before;
    prosimo=from_php_eidos_parastatikou_balance_pros;
    if (prosimo == -100) { //akirotiko
      prosimo=0;
    } else {
      if (prosimo!= 1 && prosimo!= -1) prosimo=0;
    }
        
    if (1==1 || from_php_pay_state=='080listing' || from_php_pay_state=='090ekdosi' || from_php_pay_state=='100payment') {
      if ($('#affect_balance').is(':checked')) {
        if ($('#affect_balance_all_poso').is(':checked')) {
          poso_type=$('input[name=affect_balance_all_poso_type]:checked').val();
          poso=0;
          switch (poso_type) {
            //case 'price_net': poso=parseFloat($('#bal_gks_total_price_net').attr('data-val')); break;
            case 'price_total': poso=parseFloat($('#bal_gks_total_price_total').attr('data-val')); break;
            //case 'pliroteo': poso=parseFloat($('#bal_gks_pliroteo').attr('data-val')); break;
          }
          if (isNaN(poso)) poso=0;
          after+=prosimo*poso;
        } else {
          poso=parseFloat($('#affect_balance_poso').val());
          if (isNaN(poso)) poso=0;
          after+=prosimo*poso;
        }
      }
    }
    $('#balance_user_after').html(after.mymoney());
  }
  if (!(from_php_pay_state=='080listing' || from_php_pay_state=='090ekdosi' || from_php_pay_state=='100payment')) {
    balance_user_after_calc();
  }
  
  
  function payment_is_for_invs() {
    user_id=parseInt($("#user_id").val());
    if (isNaN(user_id)) user_id=0;
    if (user_id<=0) {
      $('#payment_is_for_invs').html('<div style="text-align:center;" class="alert alert-warning">'+gks_lang('Επιλέξτε κάποια επαφή (πελάτη/προμηθευτή) και εδώ θα εμφανιστούν τα μη εξοφλημένα παραστατικά')+'</div>');
      return;
    } else {
      $('#payment_is_for_invs').html('<div style="text-align:center;"><img src="img/wait.gif"></div>');
      
      datasend='cmd=get&id=' + user_id + '&acc_pay_id=' + from_php_id + '&pay_acc_journal_id=' + $('#pay_acc_journal_id').val();
      //console.log(datasend);
      
      $.ajax({
  			url: 'admin-get-user-payment_is_for_invs.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  				//myalert('error:' + jqXHR.responseText);
  				$('#payment_is_for_invs').html('<div style="text-align:center;" class="alert alert-danger">'+gks_lang('Σφάλμα')+': ' + jqXHR.responseText + '</div>');
  			},
  			success: function(data) {
  				if (!data) {
  				  $('#payment_is_for_invs').html('<div style="text-align:center;" class="alert alert-danger">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
  				} else {
  					if (data.success == true) {
  					  //console.log('gks_admin_get_user_data res');
  					  //console.log(data);

  					  $('#payment_is_for_invs').html(data.html);
  					  $('.pay_poso_for_invs').on(mychange, pay_poso_for_invs_change);
              
              pay_poso_for_invs_change();
              gks_myscroll();
  
              
  					} else {
  				    $('#payment_is_for_invs').html('<div style="text-align:center;" class="alert alert-danger">'+gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message) + '</div>');
  					}
  				}
  			}
  		});      
    }
  }
  
  
  function pay_poso_for_invs_change() {
    need_save=true;
    var sum_pay_poso_for_invs=0;
    $('.pay_poso_for_invs').each(function() {
      val=parseFloat($(this).val());
      if (isNaN(val)) val=0;
      sum_pay_poso_for_invs+=val;
      
      mmin=$(this).attr('min'); 
      if (isNaN(mmin)) mmin=0; 
      mmax=$(this).attr('max'); 
      if (isNaN(mmax)) mmax=0; 
      
      if (mmax>0) {
        pososto_bar=(100*val/mmax).myround(0);
        color_bar='#dc3545'; if (val==mmax) color_bar='#47a447';       
        $(this).parent().find('.pay_poso_for_invs_bar').css('width',pososto_bar + '%').css('background-color',color_bar); 
      } else if (mmin<0) {
        pososto_bar=(100*val/mmin).myround(0);
        color_bar='#dc3545'; if (val==mmin) color_bar='#47a447';       
        $(this).parent().find('.pay_poso_for_invs_bar').css('width',pososto_bar + '%').css('background-color',color_bar); 
        
      }
      
      
    });
    $('#sum_pay_poso_for_invs').html(sum_pay_poso_for_invs.mymoney());
    $('#bal_gks_total_price_total2').html($('#bal_gks_total_price_total').html());
    
    tnet=parseFloat($('#gks_total_price_total').attr('data-val'));
    if (isNaN(tnet)) tnet=0;


    diafora=(tnet-sum_pay_poso_for_invs).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    temp=diafora.mymoney();
    mymin=1;
    if (from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL>0) mymin=(1/2)/Math.pow(10, from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL); // 0.005
    if (Math.abs(diafora) < mymin)
      temp='<span style="font-weight:bold;font-size:150%;color:#47a447;">' + temp + '</span>';
    else
      temp='<span style="font-weight:bold;font-size:150%;color:#dc3545;">' + temp + '</span>';
      
    $('#diafora_pay_poso_for_invs').html(temp);
  }
  
  $('.pay_poso_for_invs').on(mychange, pay_poso_for_invs_change);
  if (from_php_pay_state!='090ekdosi') pay_poso_for_invs_change();
  
  
  function pay_split() {
    
    var myrest=parseFloat($('#gks_total_price_total').attr('data-val'));
    if (isNaN(myrest)) myrest=0;
    $('.pay_poso_for_invs').each(function() {
      $(this).val('');  
    });
    //console.log(myrest);

    $('.pay_poso_for_invs').each(function() {
      mmin=$(this).attr('min'); 
      if (isNaN(mmin)) mmin=0; 
      if (mmin<0) {
        myrest=(myrest-mmin).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $(this).val(mmin);        
      }
    });    

    $('.pay_poso_for_invs').each(function() {
      mmax=$(this).attr('max'); 
      if (isNaN(mmax)) mmax=0; 
      if (mmax>0) {
        if (mmax <= myrest) toset=mmax;
        else {
          toset=myrest.myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        }
        myrest=(myrest-toset).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $(this).val(toset);
      }
      
    });    
    pay_poso_for_invs_change();
  }
  

  window.gks_fnc_links_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('#tr_links_url_' + myargs[0]).hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var links_aa=0;
      $('#links_table .links_aa').each(function () {
        links_aa++;
        $(this).html(links_aa);  
      });    
    });
  }
  
  
  $('#assigned_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml: 1
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
    delay: 300, //default
    select: function( event, ui ) {
      $('#assigned_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#assigned_id').val('').attr('data-id','0');
        }
    }
  });
  
  $('#crm_channel_id').change(function() {
    has_contact = parseInt($('#crm_channel_id option:selected').attr('data-contact'));
    has_campain = parseInt($('#crm_channel_id option:selected').attr('data-campain'));
    has_url = parseInt($('#crm_channel_id option:selected').attr('data-url'));
    has_code = parseInt($('#crm_channel_id option:selected').attr('data-code'));
    has_text = parseInt($('#crm_channel_id option:selected').attr('data-text'));
    //console.log(has_text,has_contact,contact_filter,has_campain,has_url);
    if (has_contact==0) $('#crm_channel_contact_id_div').slideUp(); else $('#crm_channel_contact_id_div').slideDown();
    if (has_campain==0) $('#crm_channel_campain_id_div').slideUp(); else $('#crm_channel_campain_id_div').slideDown();
    if (has_url==0) $('#crm_channel_url_div').slideUp(); else $('#crm_channel_url_div').slideDown();
    if (has_code==0) $('#crm_channel_code_div').slideUp(); else $('#crm_channel_code_div').slideDown();
    if (has_text==0) $('#crm_channel_text_div').slideUp(); else $('#crm_channel_text_div').slideDown();
    
  });
  


  $('#crm_channel_contact_id').autocomplete({
    //source: "admin-autocomplete-user.php?salesman=1",
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      contact_filter = $.base64.decode($('#crm_channel_id option:selected').attr('data-contact_filter'));
      if (contact_filter!='') {
        parts=contact_filter.split('&');
        for (i=0;i<parts.length;i++) {
          parts2=parts[i].split('=');
          if (parts2.length==2) {
            mydata[parts2[0]]=parts2[1];
          }
        }
      }
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
    delay: 300, //default
    select: function( event, ui ) {
      need_save=true;
      $('#crm_channel_contact_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#crm_channel_contact_id').val('').attr('data-id','0');
      }
    }
  });
  
  $('#crm_channel_campain_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-campain.php',
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
    delay: 300, //default
    select: function( event, ui ) {
      need_save=true;
      $('#crm_channel_campain_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#crm_channel_campain_id').val('').attr('data-id','0');
      }
    }
  });

  function crm_channel_text_change() {gks_resize_textarea($(this));}
  $('#crm_channel_text').on('change keyup paste', crm_channel_text_change);
  if ($('#crm_channel_text').length>0) gks_resize_textarea($('#crm_channel_text'));
  

  function note_doc_change() {gks_resize_textarea($(this));}
  $('#note_doc').on(mychange, note_doc_change);
  gks_resize_textarea($('#note_doc'));
  
  function note_logistirio_change() {gks_resize_textarea($(this));}
  $('#note_logistirio').on(mychange, note_logistirio_change);
  gks_resize_textarea($('#note_logistirio'));


  if (from_php_id==-1 && from_php_template_id>0) {
    //kostos_apostolis_mode='manual';
    //kostos_pliromis_mode='manual';
    calc_pliroteo();
  }
  
  function div_payment_type_multi_item_select_change() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    pp=parseInt($(this).attr('data-pp')); if (isNaN(pp)) pp=0;
    if (pp<=0) return;
    val=parseInt($(this).val());if (isNaN(val)) val=0;
    paadeid=parseInt($(this).find('option:selected').attr('data-aade_id')); if (isNaN(paadeid)) paadeid=0;
    ppawith=parseInt($(this).find('option:selected').attr('data-payment_acquirer_with_id')); if (isNaN(ppawith)) ppawith=0;
    

    //console.log(pp,val,aade_id); 
    pp_elem=$('.div_payment_type_multi_item[data-pp=' + pp + ']');
    if (pp_elem.length!=1) return;
    if (ppawith>=1) { //POS
      pp_elem.find('.div_payment_type_multi_item_row2').show();
      pp_elem.find('.div_payment_type_multi_item_pos_terminal').attr('data-pawid',ppawith); //.attr('data-asset_id','').val('');
      
    } else {
      pp_elem.find('.div_payment_type_multi_item_row2').hide();
      pp_elem.find('.div_payment_type_multi_item_pos_terminal').attr('data-pawid','0').attr('data-asset_id','').val('');
    }
  }
  $('.div_payment_type_multi_item_select').change(div_payment_type_multi_item_select_change);  
  
  
  function gks_paroxos_ajax(mycmd) {
   
    datasend='&mycmd='+mycmd;
    datasend+='&page=' + encodeURIComponent($.base64.encode(window.location.pathname));
    
    $('body').addClass("myloading"); 
    $.ajax({
			url: '/my/admin-acc-xxx-item-paroxos.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_mycmd:mycmd,
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
  					//myalert('ok:' + 'OK');
  					if (this.gks_mycmd=='paroxos_files_check') {
  					  $([document.documentElement, document.body]).animate({
                scrollTop: $("#gks_arxeia").offset().top
              }, 500);
            }
//  					need_save=false;
            if (typeof data.save_but_message == 'undefined') data.save_but_message='';
  					if (data.save_but_message!='') {
  					  if ($.base64.decode(data.message)=='OK') {
  					    myalert('ok:' + $.base64.decode(data.save_but_message), '' ,true);
  					  } else if ($.base64.decode(data.message)=='INFO') {
  					    myalert('info:' + $.base64.decode(data.save_but_message), '' ,true);
  					  } else if ($.base64.decode(data.message)=='WARNING') {
  					    myalert('warning:' + $.base64.decode(data.save_but_message), '' ,true);
  					  } else {
  					    myalert('error:' + $.base64.decode(data.save_but_message), '' ,true);
  					  }
  					} else {
    					myalert('ok:' + $.base64.decode(data.message), '', true);
    				}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});  
  
    
  }    
      
  $('#paxoros_check_processing').click(function() {
    gks_paroxos_ajax('paroxos_status_check');
  });
  $('#paxoros_check_files').click(function() {
    gks_paroxos_ajax('paroxos_files_check');
  });
  $('#paxoros_send_pdf').click(function() {
    gks_paroxos_ajax('paxoros_send_pdf');
  });  
  $('#paroxos_get_docstate').click(function() {
    gks_paroxos_ajax('paroxos_get_docstate');
  });  
  
  if ($('#gks_paroxos_send_pdf').length==1) {
    var gks_paroxos_send_pdf_sw = new Switchery(document.querySelector('#gks_paroxos_send_pdf'),gks_switchery_defaults());
  }
  function gks_paroxos_send_pdf_ed() {
    if ($('#gks_paroxos_send_pdf').length==0) return;
    if ($('#dialog_print_file_type_pdf').prop('checked')) {
      gks_paroxos_send_pdf_sw.enable();
    } else {
      if ($('#gks_paroxos_send_pdf').is(':checked')) $('#gks_paroxos_send_pdf').click();
      gks_paroxos_send_pdf_sw.disable();
    }
  }
  
  gks_paroxos_send_pdf_ed();  
  
  




  function gks_mcmitem_mark_autocomplete(myelem) {
    myelem.autocomplete({
      source: function(request, response) {
        mydata={
          term: request.term,
        };
        $.ajax({
          url: 'admin-autocomplete-xxx-xxx.php',
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
        
        //$(this).attr('data-id',ui.item.id);
        
        mcmaa=parseInt($(this).attr('data-mcmaa'));
        if (isNaN(mcmaa)) mcmaa=0;
        $(this).attr('data-mcm_mark',ui.item.mark);
        $(this).attr('data-mcm_acc_inv_id',ui.item.mcm_acc_inv_id);
        $(this).attr('data-mcm_acc_pay_id',ui.item.mcm_acc_pay_id);
        $(this).attr('data-mcm_whi_mov_id',ui.item.mcm_whi_mov_id);
        gks_mcmitem_get_data(mcmaa);
        
      },
      change: function (event, ui) {
        need_save=true;
        if(!ui.item){
          //$(this).val('');
          
          mcmaa=$(this).attr('data-mcmaa');
          $(this).attr('data-mcm_mark','');
          $(this).attr('data-mcm_acc_inv_id','0');
          $(this).attr('data-mcm_acc_pay_id','0');
          $(this).attr('data-mcm_whi_mov_id','0');
          $('.mcmitem_text[data-mcmaa=' + mcmaa + ']').html('');
          gks_mcmitem_get_data(mcmaa);
        }
      },
      create: function () {
        $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
          return $('<li>')
            .append('<a class="gks_autocomplete_id">' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + item.descr + '</span>')
            .appendTo(ul);
        };
      },
      open: function(event, ui) {
        var mymaxui_id=0;
        $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
          temp=$(this).outerWidth();
          if (temp>mymaxui_id) mymaxui_id=temp;
        });
        var mymaxui_text=0;
        $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text').each(function() {
          temp=$(this).outerWidth();
          if (temp>mymaxui_text) mymaxui_text=temp;
        });
        mymaxui_id+=4;
        $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
          $(this).css({'min-width':mymaxui_id + 'px','display' : 'inline-block'});
        }); 
        mymaxui_text+=mymaxui_id + 4;
        limit_width=$(window).width()- $(event.target).offset().left - 20;
        if (mymaxui_text>limit_width) mymaxui_text=limit_width;
        $(this).data('ui-autocomplete').menu.element.css('width',mymaxui_text+'px');
      },  
            
            
    });
  }
  
  $('.mcmitem_mark').each(function() {
    gks_mcmitem_mark_autocomplete($(this));
  });  
  
  function gks_mcmitem_get_data(mcmaa) {
    
    if (mcmaa==0) return;
    
    elem=$('.mcmitem_mark[data-mcmaa=' + mcmaa + ']');
    mcm_mark=elem.val().trim();
    if (mcm_mark.startsWith('acc_inv#') || mcm_mark.startsWith('acc_pay#') || mcm_mark.startsWith('whi_mov#')) mcm_mark='';
    if (mcm_mark=='') {
      mcm_mark=elem.attr('data-mcm_mark').trim();
    }
    mcm_acc_inv_id=parseInt(elem.attr('data-mcm_acc_inv_id'));
    mcm_acc_pay_id=parseInt(elem.attr('data-mcm_acc_pay_id'));
    mcm_whi_mov_id=parseInt(elem.attr('data-mcm_whi_mov_id'));
    
    if (isNaN(mcm_acc_inv_id)) mcm_acc_inv_id=0;
    if (isNaN(mcm_acc_pay_id)) mcm_acc_pay_id=0;
    if (isNaN(mcm_whi_mov_id)) mcm_whi_mov_id=0;
    
    $('.mcmitem_text[data-mcmaa=' + mcmaa + ']').html('');
    
    //console.log(mcm_mark,mcm_acc_inv_id,mcm_whi_mov_id);
    
    datasend='';
    datasend+='&mcm_mark=' + encodeURIComponent($.base64.encode(mcm_mark));
    datasend+='&mcm_acc_inv_id=' + encodeURIComponent(mcm_acc_inv_id);
    datasend+='&mcm_acc_pay_id=' + encodeURIComponent(mcm_acc_pay_id);
    datasend+='&mcm_whi_mov_id=' + encodeURIComponent(mcm_whi_mov_id);
    
    $.ajax({
			url: '/my/admin-get-xxx-xxx_data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_mcmaa:mcmaa,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  //$("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				//$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					$('.mcmitem_text[data-mcmaa=' + this.gks_mcmaa + ']').html('error');
				} else {
					if (data.success == true) {
  					$('.mcmitem_text[data-mcmaa=' + this.gks_mcmaa + ']').html(data.data.html);
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
						$('.mcmitem_text[data-mcmaa=' + this.gks_mcmaa + ']').html('error');
					}
				}
			}
			
		}); 
		    
  }
 


  $('#mcmitem_multiple_connected_marks_table').sortable({
    items: '.gks_multiple_connected_marks_item',
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-mcmaa'});
      //console.log(mylist);
      mcmitem_sortable_after(mylist);
    }
  });

  function mcmitem_sortable_after(mylist) {

    $('#mcmitem_multiple_connected_marks_table > .gks_multiple_connected_marks_item').each(function() {
      mcmaa=$(this).attr('data-mcmaa');
      $(this).attr('data-mcmaa_temp',mcmaa);
    });
    $('#mcmitem_multiple_connected_marks_table > .gks_multiple_connected_marks_item').each(function() {
      mcmaa=$(this).attr('data-mcmaa_temp');
      new_mcmaa=-1;
      for(i=0;i<mylist.length;i++) {
        if (mylist[i]==mcmaa) {
          new_mcmaa=i;break;
        }
      }
      //console.log('new_aa',new_aa);
      if (new_mcmaa>=0) {
        new_mcmaa++
        $(this).attr('data-mcmaa',new_mcmaa);
        $(this).find('*[data-mcmaa=' + mcmaa + ']').attr('data-mcmaa',new_mcmaa);
      }
      
    });      
    mcmitem_table_colors();
  } 

  function mcmitem_table_colors() {
    $('.gks_multiple_connected_marks_item').each(function(index) {
      mcmaa=$(this).attr('data-mcmaa');
      if (index % 2) {
        $(this).removeClass('gks_multiple_connected_marks_even').addClass('gks_multiple_connected_marks_odd'); 
      } else {
        $(this).removeClass('gks_multiple_connected_marks_odd').addClass('gks_multiple_connected_marks_even');  
      }
    });
  }
  mcmitem_table_colors();

  function gks_multiple_connected_marks_delete_click() {
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    delete_mcmaa=parseInt( $(this).attr('data-mcmaa'));
    if (isNaN(delete_mcmaa)) delete_mcmaa=0;
    if (delete_mcmaa<=0) return;
    $('.gks_multiple_connected_marks_item[data-mcmaa=' + delete_mcmaa +']').remove(); 
    
    if ($('.gks_multiple_connected_marks_item').length ==0) {
      multiple_connected_marks_add(false,0);  
    }

    mcmitem_table_colors();
    gks_myscroll();
  }
  
  $('.gks_multiple_connected_marks_delete').click(gks_multiple_connected_marks_delete_click);
  
  

  function multiple_connected_marks_add(fromloading,click_mcmaa) {
    
    if (from_php_perm_ret_edit==false) return;
    need_save=true;
    last_mcmaa++;
    console.log('fromloading' , fromloading,click_mcmaa);

    row_html=
          '<div class="form-group row gks_multiple_connected_marks_item align-items-center" data-mcmaa="' + last_mcmaa + '" data-recid="0">' +
            '<div class="col-12 col-sm-4  col-md-3 col-lg-2">' +
              '<input class="mcmitem_mark form-control form-control-sm" ' +
              'value="" ' +
              'placeholder="'+gks_lang('ΜΑΡΚ ή #αριθμό ή @ημερομηνία')+' ..." ' +
              'data-mcmaa="' + last_mcmaa + '" ' +
              'data-mcm_mark="" ' +
              'data-mcm_acc_inv_id="0" ' +
              'data-mcm_acc_pay_id="0" ' +
              'data-mcm_whi_mov_id="0" ' +
              '>' +
            '</div>' +
            '<div class="col-12 col-sm-6  col-md-7  col-lg-9">' +
              '<div class="mcmitem_text" data-mcmaa="' + last_mcmaa + '">' +
              '</div>' +
            '</div>' +
            '<div class="col-12 col-sm-2  col-md-2  col-lg-1">' +
              '<div class="text-center gks_icons">' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-trash-alt gks_multiple_connected_marks_delete" data-mcmaa="' + last_mcmaa + '"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-arrows-alt-v sortorder_handle"></i>' +
                '</div>' +
                '<div style="width:33%;float:left;">' +
                  '<i class="fas fa-plus-circle gks_multiple_connected_marks_add" data-mcmaa="' + last_mcmaa + '"></i>' +
                '</div>' +
              '</div>' +
            '</div>' +
          '</div>';
          
    
    
    if (click_mcmaa<=0) {
      $('#div_multiple_connected_marks_footer').before(row_html);
    } else {
      $('.gks_multiple_connected_marks_item[data-mcmaa=' + click_mcmaa + ']').after(row_html);
    }
    gks_mcmitem_mark_autocomplete($('.mcmitem_mark[data-mcmaa=' + last_mcmaa + ']'));
    
    
    $('.gks_multiple_connected_marks_add[data-mcmaa=' + last_mcmaa + ']').click(function() {gks_multiple_connected_marks_add_click(false,$(this));});
    $('.gks_multiple_connected_marks_delete[data-mcmaa=' + last_mcmaa + ']').click(gks_multiple_connected_marks_delete_click);
    if (fromloading==false) {
      $('.mcmitem_mark[data-mcmaa=' + last_mcmaa + ']').focus().select();
    }
    if (click_mcmaa>0) {
      var mylist=[];
      $('.gks_multiple_connected_marks_item').each(function() {
        mylist.push($(this).attr('data-mcmaa'));
      });
      mcmitem_sortable_after(mylist);
    }
        
    mcmitem_table_colors();
    gks_myscroll();
  }

  function gks_multiple_connected_marks_add_click(fromloading,elem) {
    mcmaa=elem.attr('data-mcmaa');
    multiple_connected_marks_add(fromloading,mcmaa);
  }
  $('.gks_multiple_connected_marks_add').click(function() {gks_multiple_connected_marks_add_click(false,$(this));});

  
  
  // last of all 
  if (from_php_id==-1 && from_php_template_id==0) { 
    eidoi_add(true,0);
    multiple_connected_marks_add(true,0);
    $('#user').focus().select(); //to customer for select amesa
  } else {
    if (last_aa==0) {
      if (from_php_gks_lock==false) eidoi_add(true,0);
    }
    if (last_mcmaa==0) {
      if (from_php_gks_lock==false) multiple_connected_marks_add(true,0);
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
  
  
  
  $('.myneedsave').on('input change keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (from_php_perm_ret_edit==false) return;
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;
  
  //console.log('load end');

	  
});
