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
                

        $('#div_pelati_sxolio').hide('fade', 'slow');
        $('#text_pelati_sxolio').html('');
                        
        $('#div_order_sxolio').hide('fade', 'slow');
        $('#text_order_sxolio').html('');   
           
        $('#dr_myfirst_name').html('');
        $('#dr_mylast_name').html('');
        $('#dr_user_email').html('');
        $('#dr_user_mobile').html(''); 
        $('#dr_user_lang').val('el-GR');
        $('#dr_user_ma_odos').html('');
        $('#dr_user_ma_arithmos').html('');
        $('#dr_user_ma_orofos').html('');
        $('#dr_user_ma_perioxi').html('');
        $('#dr_user_ma_poli').html('');
        $('#dr_user_ma_tk').html('');
        $('#dr_user_ma_country_id').html('');
        $('#dr_user_ma_nomos_id').html('');
                      
        $('#dr_user_eponimia').html('');
        $('#dr_user_title').html('');
        $('#dr_user_afm').html('');
        $('#dr_user_doy').html('');
        $('#dr_user_epaggelma').html('');
        

        
        $('#fiscal_position_id').val(1);
        $('#pricelist_id').val(1);
             
        gks_myscroll(); 
                     
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

  
  
  function mysubmit(inv_state = '') {
    if (from_php_perm_ret_edit==false) return;
    
    datasend='';
    datasend+='&pos_name='  + encodeURIComponent($.base64.encode($("#pos_name").val().trim()));
    datasend+='&pos_descr='  + encodeURIComponent($.base64.encode($("#pos_descr").val().trim()));
    datasend+='&pos_user_can_change_prices='  + ($('#pos_user_can_change_prices').is(':checked') ? '1' : '0');
    datasend+='&pos_max_ammount='  + encodeURIComponent($("#pos_max_ammount").val().trim());
    datasend+='&pos_aade_mydata_live=' + ($('#pos_aade_mydata_live').is(':checked') ? '1' : '0');
    datasend+='&pos_multi_copies=' + encodeURIComponent($("#pos_multi_copies").val().trim());
    datasend+='&pos_installments=' + encodeURIComponent($("#pos_installments").val().trim());
    datasend+='&pos_tip=' + ($('#pos_tip').is(':checked') ? '1' : '0');
    datasend+='&pos_can_search_products=' + ($('#pos_can_search_products').is(':checked') ? '1' : '0');
    datasend+='&pos_indexeddb=' + ($('#pos_indexeddb').is(':checked') ? '1' : '0');
    datasend+='&pos_auto_click_start_at_paywith=' + ($('#pos_auto_click_start_at_paywith').is(':checked') ? '1' : '0');
    
    datasend+='&pos_disable=' + ($('#pos_disable').is(':checked') ? '0' : '1');
    
    
    datasend+='&app_mobile_userlogin_id=' + encodeURIComponent($('#app_mobile_userlogin_id').attr('data-user_id'));
    
    if ($('#file_type_pdf').prop('checked'))       datasend+='&file_type=' + encodeURIComponent($.base64.encode('pdf'));
    else if ($('#file_type_html').prop('checked')) datasend+='&file_type=' + encodeURIComponent($.base64.encode('html'));
    else if ($('#file_type_jpg').prop('checked'))  datasend+='&file_type=' + encodeURIComponent($.base64.encode('jpg'));
    else datasend+='&file_type=';    
    datasend+='&is_landscape='  + ($('#is_landscape_on').prop('checked') ? '1' : '0');
    datasend+='&grayscale='  + ($('#grayscale_on').prop('checked') ? '1' : '0');
    zoom_slider=parseInt($('#zoom_slider').slider('value'));
    if (isNaN(zoom_slider)) zoom_slider=100;
    datasend+='&zoom='  + encodeURIComponent(zoom_slider);
    datasend+='&pos_print_form_id='  + encodeURIComponent($("#pos_print_form_id").val().trim());
    datasend+='&pos_thermal_form_id='  + encodeURIComponent($("#pos_thermal_form_id").val().trim());
    datasend+='&pos_print_x_form_id='  + encodeURIComponent($("#pos_print_x_form_id").val().trim());
    if ($("#company_id_sub_id").length > 0) datasend+='&company_id_sub_id=' + encodeURIComponent($.base64.encode($("#company_id_sub_id").val().trim()));
    if ($("#pos_journal_id").length > 0) datasend+='&pos_journal_id=' + encodeURIComponent($("#pos_journal_id").val().trim());
    if ($("#pos_seira_id").length > 0) datasend+='&pos_seira_id=' + encodeURIComponent($("#pos_seira_id").val().trim());
    datasend+='&def_aade_skopos_diakinisis_id=' +      encodeURIComponent($('#def_aade_skopos_diakinisis_id').val().trim());
    datasend+='&def_fiscal_position_id=' +      encodeURIComponent($('#def_fiscal_position_id').val().trim());
    datasend+='&def_pricelist_id=' +      encodeURIComponent($('#def_pricelist_id').val().trim());
    datasend+='&def_assigned_id='  + $("#mypostform #def_assigned_id").attr('data-id');
    datasend+='&user_id=' + encodeURIComponent($("#user_id").val().trim());
    datasend+='&def_user_lang='  + encodeURIComponent($.base64.encode($("#def_user_lang").val().trim()));
    datasend+='&pos_warehouses_id_from=' + $("#mypostform #pos_warehouses_id_from").attr('data-id');
    datasend+='&pos_warehouses_id_to=' + $("#mypostform #pos_warehouses_id_to").attr('data-id');

    d=$('input[name=radio_delivery_way]:checked');
    if (d.css('display')=='none') {
      myalert('error:'+gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο αποστολής'));
      return;
    }
    d=d.val();
    if (d === undefined || d === null) d=0;
    if (d<=0) {
      myalert('error:'+gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο αποστολής'));
      return;
    }
    
    p=$('input[name=radio_payment_way]:checked');
    if (p.css('display')=='none') {
      myalert('error:'+gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο πληρωμής'));
      return;
    }
    p=p.val();
    if (p === undefined || p === null) p=0;
    if (p<=0) {
      myalert('error:'+gks_lang('Παρακαλώ επιλέξτε κάποιον τρόπο πληρωμής'));
      return;
    }
    
    delivery_id_8=0;
    if (d == 8) {
      if ($('#delivery_id_8').val() == 0) {
        myalert('error:'+gks_lang('Παρακαλώ επιλέξτε το κατάστημα που θέλετε να παραλάβετε τα προϊόντα σας'));
        return;  
      }
      delivery_id_8=$('#delivery_id_8').val();
    }
    
    var def_tropos_pliromis_array=[];
    $('input[name=radio_payment_way_check]:checked').each(function() {
      item={};
      item.id=$(this).val();
      item.asset_id=parseInt($('.div_payment_one_terminal[data-one_pway=' + item.id + '] .div_payment_one_terminal_terminal').attr('data-asset_id'));
      if (isNaN(item.asset_id)) item.asset_id=0;
      def_tropos_pliromis_array.push(item);
      
      
    });
    //console.log(def_tropos_pliromis_array);return;
    
    datasend+='&def_tropos_pliromis_array='  + encodeURIComponent($.base64.encode(JSON.stringify(def_tropos_pliromis_array)));


    
    datasend+='&tropos_apostolis=' + d;    
    datasend+='&tropos_pliromis=' + p;    
    datasend+='&delivery_id_8=' + delivery_id_8;    
    
    datasend+='&def_affect_balance=' + ($('#def_affect_balance').is(':checked') ? '1' : '0');
    datasend+='&def_affect_balance_all_poso=' + ($('#def_affect_balance_all_poso').is(':checked') ? '1' : '0');
    baltype=$('input[name=def_affect_balance_all_poso_type]:checked');
    if (baltype.css('display')=='none') baltype='';
    else {baltype=baltype.val(); if (baltype === undefined || baltype === null) d='';}
    datasend+='&def_affect_balance_all_poso_type=' + encodeURIComponent(baltype);
     
    if (from_php_GKS_CRM_ENABLE) {
      datasend+='&def_crm_channel_id='  + $("#mypostform #def_crm_channel_id").val();
      datasend+='&def_crm_channel_contact_id='  + $("#mypostform #def_crm_channel_contact_id").attr('data-id');
      datasend+='&def_crm_channel_campain_id='  + $("#mypostform #def_crm_channel_campain_id").attr('data-id');
      datasend+='&def_crm_channel_url='  + encodeURIComponent($.base64.encode($("#mypostform #def_crm_channel_url").val().trim()));
      datasend+='&def_crm_channel_code='  + encodeURIComponent($.base64.encode($("#mypostform #def_crm_channel_code").val().trim()));
      datasend+='&def_crm_channel_text='  + encodeURIComponent($.base64.encode($("#mypostform #def_crm_channel_text").val().trim()));
    }
    
    datasend+='&pos_print_enable=' + ($('#pos_print_enable').is(':checked') ? '1' : '0');
    datasend+='&pos_paroxos_send_pdf=' + ($('#pos_paroxos_send_pdf').is(':checked') ? '1' : '0');
    
    
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
    

    datasend+='&mypropertiesheight=' + encodeURIComponent(window.scrollY);
    datasend+=gks_custom_datasend();

    var product_ids='';
    $('#def_products_ids_table .product_tr').each(function() {
      pid=$(this).attr('data-id');
      product_ids+=pid +',';
    });
    


    datasend+='&def_products_ids='  + encodeURIComponent($.base64.encode(product_ids));
    datasend+='&def_products_text='  + encodeURIComponent($.base64.encode($("#def_products_text").val().trim()));
    
    if ($("#pos_sms_erp_app_mobile_id_code").val()==null) $("#pos_sms_erp_app_mobile_id_code").val('');
    datasend+='&pos_sms_erp_app_mobile_id_code=' + encodeURIComponent($.base64.encode($("#pos_sms_erp_app_mobile_id_code").val().trim()));
    datasend+='&pos_sms_template_text='  + encodeURIComponent($.base64.encode($("#pos_sms_template_text").val().trim()));
    
    //console.log(datasend);
    //return;

    //console.log(eidi_array);
    //console.log(eidi_array);
    //console.log(datasend);

    //console.log(datasend);
    //return;

    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-pos-item-exec.php?id=' + from_php_id,
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
  

  $('#myproperties').show();
  //console.log($('#divmyproperties').css('height'));
  //console.log($('#divmyproperties').height());
  
  //$('#divmyproperties').css('height','');
  //console.log($('#divmyproperties').height());

  


    
  

  

  
    

  

  $('input[name=radio_delivery_way]').click( function() {
    if (from_php_perm_ret_edit==false) return;


    
    need_save=true;
    mytype=$(this).attr('data-type');
    mytype_o=$(this).attr('data-type-o');

    $('input[name=radio_payment_way]').each(function( index ) {
      myto=$(this).attr('data-type-o');
      if (myto.indexOf('[' + mytype + ']') !== -1) {
        $(this).prop('disabled', false);
        $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');
      } else {
        $(this).prop('disabled', true);
        $(this).parent().children('.delivery_payment_label').addClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').addClass('delivery_payment_disabled');
        if ($(this).prop('checked')) {
          $(this).prop('checked',false);
          $('#payment_acquirer_sxolio').html('');
          $('#button_html').html(gks_lang('Πληρωμή τώρα'));
        }
      }
    });
    
    $('input[name=radio_delivery_way]').each(function( index ) {
      $(this).prop('disabled', false);
      $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
      $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');      
    });

    d=$('input[name=radio_delivery_way]:checked').val();
    if (d === undefined || d === null) d=0;
    p=$('input[name=radio_payment_way]:checked').val();
    if (p === undefined || p === null) p=0;
    //basket_edit(false,true,false,'delivery_payment', d, p, '', 0);    
    
    $('#delivery_method_sxolio').html('');
    myhtml= $.base64.decode($(this).attr('data-sxolio'));
    if (myhtml!='') $('#delivery_method_sxolio').html(gks_lang('Σχόλιο τρόπου αποστολής')+': <i>' + myhtml + '</i>');

    if (d == 8) {
      $('#span_delivery_id_8').show();
      if ($('#delivery_id_8').val() == '0') {
        if ($('#delivery_id_8 option').length==2) {
          $('#delivery_id_8').val($($('#delivery_id_8 option')[1]).attr('value'));
        }
      }
    } else {
      $('#span_delivery_id_8').hide();
    }

    if (mytype=='delivery' || mytype=='pelatis' || mytype=='post') {
      $('#div_delivery_number').show();
      $('#div_vehicle_number').show();
      $('#div_dispatch_date').show();
    } else {
      $('#div_delivery_number').hide();
      $('#div_vehicle_number').hide();
      $('#div_dispatch_date').hide();
    }
    gks_myscroll();
  });
  
  
  
  
  $('input[name=radio_payment_way]').click(function() {
    if (from_php_perm_ret_edit==false) return;


    need_save=true;
    mytype=$(this).attr('data-type');
    mytype_o=$(this).attr('data-type-o');
    

    $('input[name=radio_delivery_way]').each(function( index ) {
      myto=$(this).attr('data-type-o');
      if (myto.indexOf('[' + mytype + ']') !== -1) {
        $(this).prop('disabled', false);
        $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');
      } else {
        $(this).prop('disabled', true);
        $(this).parent().children('.delivery_payment_label').addClass('delivery_payment_disabled');
        $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').addClass('delivery_payment_disabled');
        if ($(this).prop('checked')) {
          $(this).prop('checked',false);
          $('#delivery_method_sxolio').html('');
        }
      }
    });

    $('input[name=radio_payment_way]').each(function( index ) {
      $(this).prop('disabled', false);
      $(this).parent().children('.delivery_payment_label').removeClass('delivery_payment_disabled');
      $(this).parent().children('.delivery_payment_label').children('.delivery_payment_price').removeClass('delivery_payment_disabled');      
    });    
    
    
    d=$('input[name=radio_delivery_way]:checked').val();
    if (d === undefined || d === null) d=0;
    p=$('input[name=radio_payment_way]:checked').val();
    if (p === undefined || p === null) p=0;
    //basket_edit(false,true,false,'delivery_payment', d, p, '', 0);
    
    if (d!=8) $('#span_delivery_id_8').hide();
    
    
    
    $('#payment_acquirer_sxolio').html('');
    myhtml= $.base64.decode($(this).attr('data-sxolio'));
    if (myhtml!='') $('#payment_acquirer_sxolio').html(gks_lang('Σχόλιο τρόπου πληρωμής')+': <i>' + myhtml + '</i>');
    
    myhtml= $.base64.decode($(this).attr('data-button-html'));
    if (myhtml=='') myhtml=gks_lang('Πληρωμή τώρα');
    $('#button_html').html(myhtml);
    
    if (p==2) { 
      $('#div_bank_deposit_9digit').show();
    } else {
      $('#div_bank_deposit_9digit').hide();
    }
        
    
    gks_myscroll();
  });



 
  
  

  function gks_admin_get_user_data(user_id, dialog_gsis_result=false) {
    
      
    datasend='cmd=get&id=' + user_id + '&acc_inv_id=' + from_php_id;
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

//              exit_text=$('#note_production').val();
//              if (exit_text!='') exit_text+="\r\n";
//              exit_text+=mytext;
//              $('#note_production').val(exit_text);
              //$('#note_production').focus();
            }
            
            $('#dr_myfirst_name').html(data.first_name);
            $('#dr_mylast_name').html(data.last_name);
            $('#dr_user_email').html(data.email_link);
            html_phone=(data.def_phone_link!='' ? data.def_phone_link : (data.phone_home_link!='' ? data.phone_home_link : data.mobile_link));
            html_phone=html_phone.replace('<a ','<a class="'+from_php_gks_voip_params.class_span+'" ');
            html_phone+=from_php_gks_voip_params.html_after_span;
            $('#dr_user_mobile').html(html_phone);
            $('#dr_user_mobile .gks_voip_originate_after_span').click(gks_voip_originate_click);
            
            $('#dr_user_lang').val(data.lang);
            $('#pricelist_id').val(data.pricelist_id);
           




                                    
              //console.log('gks_dialog_gsis_result false');
              $('#dr_user_ma_odos').html(data.ma_odos);
              $('#dr_user_ma_arithmos').html(data.ma_arithmos);
              $('#dr_user_ma_orofos').html(data.ma_orofos);
              $('#dr_user_ma_perioxi').html(data.ma_perioxi);
              $('#dr_user_ma_poli').html(data.ma_poli);
              $('#dr_user_ma_tk').html(data.ma_tk);
              $('#dr_user_ma_country_id').html(data.country_name);
              $('#dr_user_ma_nomos_id').html(data.nomos_descr);

              
              
              
              $('#dr_user_eponimia').html(data.eponimia);
              $('#dr_user_title').html(data.title);
              $('#dr_user_afm').html(data.afm);
              $('#dr_user_doy').html(data.doy);
              $('#dr_user_epaggelma').html(data.epaggelma);
              

              
              
              $('#fiscal_position_id').val(data.fiscal_position_id);
   
                         
            
            
            //$('#div_pelati_acc_type_descr').html(data.acc_type_descr);
            
            $('#balance_user_before').html(data.balance_user_before.mymoney()).attr('data-val',data.balance_user_before);
 
            
            gks_myscroll();
             
            
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
    inv_acc_journal_id_fill('pos_journal_id','pos_seira_id',company_id,company_sub_id,0);
    
    gks_myscroll();
     
    
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
  
  



  window.inv_acc_journal_id_change = function inv_acc_journal_id_change() {
    v=$('#pos_journal_id').val();
    acc_journal_id=parseInt(v); if (isNaN(acc_journal_id)) acc_journal_id=0; 
    inv_acc_seira_id_fill('pos_seira_id',acc_journal_id,0);
    
    from_php_acc_eidos_parastatikou_id=parseInt($('#pos_journal_id option:selected').attr('data-eidi_id'));
    from_php_eidos_parastatikou_type_id=parseInt($('#pos_journal_id option:selected').attr('data-type_id'));
    from_php_eidos_parastatikou_need_prev=parseInt($('#pos_journal_id option:selected').attr('data-need_prev'));
    from_php_eidos_parastatikou_has_fpa=parseInt($('#pos_journal_id option:selected').attr('data-fpa'));
    from_php_eidos_parastatikou_has_othertaxes=($('#pos_journal_id option:selected').attr('data-othertaxes'));
    from_php_eidos_parastatikou_has_esoda=parseInt($('#pos_journal_id option:selected').attr('data-esoda'));
    from_php_eidos_parastatikou_has_eksoda=parseInt($('#pos_journal_id option:selected').attr('data-eksoda'));
    from_php_eidos_parastatikou_need_afm=parseInt($('#pos_journal_id option:selected').attr('data-need_afm'));
    from_php_eidos_parastatikou_balance_pros=parseInt($('#pos_journal_id option:selected').attr('data-balance_pros'));
    from_php_whi_eidos_parastatikou_stock_pros=parseInt($('#pos_journal_id option:selected').attr('data-whi_stock_pros'));
    from_php_whi_eidos_parastatikou_type_id=parseInt($('#pos_journal_id option:selected').attr('data-whi_type_id'));
    
    if (isNaN(from_php_acc_eidos_parastatikou_id)) from_php_acc_eidos_parastatikou_id=0;
    if (isNaN(from_php_eidos_parastatikou_need_prev)) from_php_eidos_parastatikou_need_prev=0;
    if (isNaN(from_php_eidos_parastatikou_has_fpa)) from_php_eidos_parastatikou_has_fpa=0;
    if (from_php_eidos_parastatikou_has_othertaxes === undefined || from_php_eidos_parastatikou_has_othertaxes === null) from_php_eidos_parastatikou_has_othertaxes='';
    if (isNaN(from_php_eidos_parastatikou_has_esoda)) from_php_eidos_parastatikou_has_esoda=0;
    if (isNaN(from_php_eidos_parastatikou_has_eksoda)) from_php_eidos_parastatikou_has_eksoda=0;
    if (isNaN(from_php_eidos_parastatikou_need_afm)) from_php_eidos_parastatikou_need_afm=0;
    if (isNaN(from_php_eidos_parastatikou_balance_pros)) from_php_eidos_parastatikou_balance_pros=0;
    if (isNaN(from_php_whi_eidos_parastatikou_stock_pros)) from_php_whi_eidos_parastatikou_stock_pros=0;
    if (isNaN(from_php_whi_eidos_parastatikou_type_id)) from_php_whi_eidos_parastatikou_type_id=0;
    
    antisimvalomenos_label=gks_lang('αντισυμβαλλόμενος');
    for(i=0; i < eidi_parastatikon_types.length; i++) {
      if (eidi_parastatikon_types[i].id== from_php_eidos_parastatikou_type_id) {
        antisimvalomenos_label=eidi_parastatikon_types[i].label;
        break; 
      }
    }
    $('#antisimvalomenos_label').html(antisimvalomenos_label);


    
   
      
    if (from_php_eidos_parastatikou_need_afm == 0) {
      $('#div_form_idio_afm').hide();
      $('#div_show_user').show();
      $('#div_parastatiko_timologio').hide();  
    } else if (from_php_eidos_parastatikou_need_afm == 1) {
      $('#div_form_idio_afm').hide();
      $('#div_show_user').show();
      $('#div_parastatiko_timologio').show();
    } else if (from_php_eidos_parastatikou_need_afm == -1) {
      $('#div_form_idio_afm').show();
      data_afm=$('#company_id_sub_id option:selected').attr('data-afm');
      //console.log('data_afm',data_afm);
      dr_user_afm=$('#dr_user_afm').val();
      //console.log('dr_user_afm',dr_user_afm);
      if (data_afm == dr_user_afm) {
        $('#form_idio_afm_nai').click();
      } else {
        $('#form_idio_afm_oxi').click();
      }
      $('#div_parastatiko_timologio').show();
    }


    if (from_php_whi_eidos_parastatikou_type_id==0) {
      $('#div_warehouses').hide();
    } else {
      $('#div_warehouses').show();
          
      if (from_php_whi_eidos_parastatikou_type_id == 24) { //apografi
        $('.pos_warehouses_id_from_elem').hide();
        $('.pos_warehouses_id_to_elem').show().html(gks_lang('Αφορά')+':');
        //$('#div_show_user').hide();
        //$('#div_aade_skopos_diakinisis_id').hide();
        //$('#div_fiscal_position_id').hide();
        //$('#div_pricelist_id').hide();
        //$('#div_apografi_label').show();
        $('.gks_quantity').addClass('gks_quantity_apografi');
        
      } else if (from_php_whi_eidos_parastatikou_type_id == 23) { //endodiakinisi
        $('.pos_warehouses_id_from_elem').show();
        $('.pos_warehouses_id_to_elem').show().html(gks_lang('Προς')+':');
        //$('#div_show_user').hide();
        //$('#div_aade_skopos_diakinisis_id').show();
        //$('#div_fiscal_position_id').show();
        //$('#div_pricelist_id').show();
        //$('#div_apografi_label').hide();
        $('.gks_quantity').removeClass('gks_quantity_apografi');
      } else {
        //$('#div_aade_skopos_diakinisis_id').show();
        //$('#div_fiscal_position_id').show();
        //$('#div_pricelist_id').show();
        //$('#div_apografi_label').hide();
        $('.gks_quantity').removeClass('gks_quantity_apografi');
        if (from_php_whi_eidos_parastatikou_stock_pros == 1) {//erxete, auksanei to ypoloipo stock
          $('.pos_warehouses_id_from_elem').hide();
          $('.pos_warehouses_id_to_elem').show().html(gks_lang('Προς')+':');
          //$('#div_show_user').show();
        } else if (from_php_whi_eidos_parastatikou_stock_pros == -1) { //feuvei, meionete to ypoloipo stock
          $('.pos_warehouses_id_from_elem').show();
          $('.pos_warehouses_id_to_elem').hide();
          //$('#div_show_user').show();
        }
      }
      
      set_def_warehouses();
    }

    gks_myscroll();
        
  }
  $('#pos_journal_id').change(inv_acc_journal_id_change);
 

  function set_def_warehouses() {
    if (from_php_whi_eidos_parastatikou_type_id>0 && from_php_whi_eidos_parastatikou_type_id != 23 && from_php_whi_eidos_parastatikou_type_id != 24) { //not endodiakinisi,apografi
      var old_warehouses_id_from=parseInt($('#pos_warehouses_id_from').attr('data-id'));
      var old_warehouses_id_to=  parseInt($('#pos_warehouses_id_to').attr('data-id'));
      if (isNaN(old_warehouses_id_from)) old_warehouses_id_from=0;
      if (isNaN(old_warehouses_id_to)) old_warehouses_id_to=0;
       
      mydata={};
      mydata.term='***';
      company_id=0;
      company_sub_id=0;
      v=$('#company_id_sub_id').val();
      if (v === undefined || v === null) v='';
      parts=v.split('|');
      if (parts.length==2) {
        company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
        company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
      }
      if (company_id>0) {
        mydata.company_id=company_id;
        if (company_sub_id>=0) mydata.company_sub_id=company_sub_id;
      }
      $.getJSON('admin-autocomplete-warehouse.php',mydata,function(data, textStatus, jqXHR) {
        if (textStatus=='success') {
          myfound=false;
          for (i=0;i<data.list.length;i++) if (data.list[i].id==old_warehouses_id_from) {myfound=true;break;}
          if (myfound==false) {
            if (data.list.length>=1) 
              $('#pos_warehouses_id_from').attr('data-id',data.list[0].id).val(data.list[0].value);
            else 
              $('#pos_warehouses_id_from').attr('data-id',0).val('');
          }
          myfound=false;
          for (i=0;i<data.list.length;i++) if (data.list[i].id==old_warehouses_id_to) {myfound=true;break;}
          if (myfound==false) {
            if (data.list.length>=1) 
              $('#pos_warehouses_id_to').attr('data-id',data.list[0].id).val(data.list[0].value);
            else 
              $('#pos_warehouses_id_to').attr('data-id',0).val('');
          }
        } 
      });
    }    
  }  


  if (from_php_id==-1 && from_php_template_id==0 && from_php_whi_eidos_parastatikou_type_id!=0) {
    set_def_warehouses();
  }
    
  window.inv_acc_seira_id_change = function inv_acc_seira_id_change() {
    acc_seira_id=parseInt($('#pos_seira_id').val()); if (isNaN(acc_seira_id)) acc_seira_id=0; 
//    is_xeirografi=parseInt($('#pos_seira_id option:selected').attr('data-is_xeirografi')); if (isNaN(is_xeirografi)) is_xeirografi=0; 
//    if (is_xeirografi!=0) {
//      $('#inv_acc_number_int').prop('disabled' , false);
//      $('#submit_button_080listing').show();
//      $('#submit_button_090ekdosi').hide();
//    } else {
//      $('#inv_acc_number_int').prop('disabled' , true);
//      $('#submit_button_080listing').hide();
//      $('#submit_button_090ekdosi').show();
//    }
  }
  $('#pos_seira_id').change(inv_acc_seira_id_change);

  
 


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
    
  });  
  
  
  //gks_myscroll();


  
 
  
  



  $('#def_affect_balance').change(function() {
    if ($('#def_affect_balance').is(':checked')) {
      $('#div_def_affect_balance_all_poso').show();
      if ($('#def_affect_balance_all_poso').is(':checked')) {
        $('#small_def_affect_balance_all_poso').show();
      } else {
        $('#small_def_affect_balance_all_poso').hide();
      }
    } else {
      $('#div_def_affect_balance_all_poso').hide();
    }
  });
  $('#def_affect_balance_all_poso').change(function() {
    if ($('#def_affect_balance_all_poso').is(':checked')) {
      
      $('#small_def_affect_balance_all_poso').show();
    } else {
      
      $('#small_def_affect_balance_all_poso').hide();
    }
    
  });
  


  

  
  
 
  
  $('#def_assigned_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml: 1,
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
      $('#def_assigned_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#def_assigned_id').val('').attr('data-id','0');
        }
    }
  });
  
  $('#def_crm_channel_id').change(function() {
    has_contact = parseInt($('#def_crm_channel_id option:selected').attr('data-contact'));
    has_campain = parseInt($('#def_crm_channel_id option:selected').attr('data-campain'));
    has_url = parseInt($('#def_crm_channel_id option:selected').attr('data-url'));
    has_code = parseInt($('#def_crm_channel_id option:selected').attr('data-code'));
    has_text = parseInt($('#def_crm_channel_id option:selected').attr('data-text'));
    //console.log(has_text,has_contact,contact_filter,has_campain,has_url);
    if (has_contact==0) $('#def_crm_channel_contact_id_div').slideUp(); else $('#def_crm_channel_contact_id_div').slideDown();
    if (has_campain==0) $('#def_crm_channel_campain_id_div').slideUp(); else $('#def_crm_channel_campain_id_div').slideDown();
    if (has_url==0) $('#def_crm_channel_url_div').slideUp(); else $('#def_crm_channel_url_div').slideDown();
    if (has_code==0) $('#def_crm_channel_code_div').slideUp(); else $('#def_crm_channel_code_div').slideDown();
    if (has_text==0) $('#def_crm_channel_text_div').slideUp(); else $('#def_crm_channel_text_div').slideDown();
    
  });
  


  $('#def_crm_channel_contact_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      contact_filter = $.base64.decode($('#def_crm_channel_id option:selected').attr('data-contact_filter'));
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
      $('#def_crm_channel_contact_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#def_crm_channel_contact_id').val('').attr('data-id','0');
      }
    }
  });
  
  $('#def_crm_channel_campain_id').autocomplete({
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
      $('#def_crm_channel_campain_id').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#def_crm_channel_campain_id').val('').attr('data-id','0');
      }
    }
  });

  function pos_descr_change() {gks_resize_textarea($(this));}
  $('#pos_descr').on('change keyup paste', pos_descr_change);
  if ($('#pos_descr').length>0) gks_resize_textarea($('#pos_descr'));

  function def_crm_channel_text_change() {gks_resize_textarea($(this));}
  $('#def_crm_channel_text').on('change keyup paste', def_crm_channel_text_change);
  if ($('#def_crm_channel_text').length>0) gks_resize_textarea($('#def_crm_channel_text'));

  function pos_sms_template_text_change() {gks_resize_textarea($(this));}
  $('#pos_sms_template_text').on('change keyup paste', pos_sms_template_text_change);
  if ($('#pos_sms_template_text').length>0) gks_resize_textarea($('#pos_sms_template_text'));



  $('#pos_warehouses_id_from').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      if (from_php_whi_eidos_parastatikou_type_id>0 && from_php_whi_eidos_parastatikou_type_id != 23 && from_php_whi_eidos_parastatikou_type_id != 24) { //not endodiakinisi,apografi

        company_id=0;
        company_sub_id=0;
        v=$('#company_id_sub_id').val();
        if (v === undefined || v === null) v='';
        parts=v.split('|');
        if (parts.length==2) {
          company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
          company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
        }
        //console.log(company_id,company_sub_id);
        if (company_id>0) {
          mydata.company_id=company_id;
          if (company_sub_id>=0) mydata.company_sub_id=company_sub_id;
        }
      }
      $.ajax({
        url: 'admin-autocomplete-warehouse.php',
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
      $('#pos_warehouses_id_from').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#pos_warehouses_id_from').val('').attr('data-id','0');
      }
    },
    response: function (event, ui) {
      //console.log(event);  
      //console.log(ui);  
    },
    
  });
  $('#pos_warehouses_id_to').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      if (from_php_whi_eidos_parastatikou_type_id>0 && from_php_whi_eidos_parastatikou_type_id != 23 && from_php_whi_eidos_parastatikou_type_id != 24) { //not endodiakinisi,apografi

        company_id=0;
        company_sub_id=0;
        v=$('#company_id_sub_id').val();
        if (v === undefined || v === null) v='';
        parts=v.split('|');
        if (parts.length==2) {
          company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
          company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
        }
        //console.log(company_id,company_sub_id);
        if (company_id>0) {
          mydata.company_id=company_id;
          if (company_sub_id>=0) mydata.company_sub_id=company_sub_id;
        }
      }
      $.ajax({
        url: 'admin-autocomplete-warehouse.php',
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
      $('#pos_warehouses_id_to').attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#pos_warehouses_id_to').val('').attr('data-id','0');
      }
    }
  });      
  
  

  
  

  var zoom_slider_handle = $('#zoom_slider_handle');
  $('#zoom_slider').slider({
    min: 10,
    max: 200,
    value: from_php_pos_print_zoom,
    create: function() {
      zoom_slider_handle.text( $( this ).slider('value') + '%');
    },
    slide: function( event, ui ) {
      zoom_slider_handle.text( ui.value + '%' );
      need_save=true;
    }
  });
        


  function def_products_text_change() {gks_resize_textarea($(this));}
  $('#def_products_text').on('change keyup paste', def_products_text_change);
  if ($('#def_products_text').length>0) gks_resize_textarea($('#def_products_text'));


  function product_tr_aa() {
    var aa=0;
    $('#def_products_ids_table .product_aa').each(function() {
      aa++;
      $(this).html(aa);  
    });
  }

  function product_tr_delete_click() {
    pid=$(this).attr('data-id');
    $('#def_products_ids_table .product_tr[data-id=' + pid + ']').remove();
    product_tr_aa();
  }
  $('.product_tr_delete').click(product_tr_delete_click);
  
  
  $('#def_products_ids_search').autocomplete({
   source: function(request, response) {
      mydata={
        term: request.term,
        mode: 'photo',
      };
      $.ajax({
        url: 'admin-autocomplete-product.php',
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
      id_product=ui.item.id;
      if ($('#def_products_ids_table .product_tr[data-id=' + id_product + ']').length > 0) {
        myalert('error:'+gks_lang('Αυτό το είδος υπάρχει ήδη στην λίστα'));
      } else {
      
        //console.log(ui.item);
        html='<tr class="product_tr" data-id="' + id_product + '">' + 
          '<th scope="row" nowrap="" class="mytdcm product_aa">' +
          '</th><td nowrap="" class="mytdcm">' +
            '<img src="img/delete.png" border="0" width="16" class="product_tr_delete" data-id="' + id_product + '">' +
          '</td>' +
          '<td class="mytdcm">' + ui.item.photo + '</td>' +
          '<td class="mytdcml" nowrap="">' + ui.item.value + '</td>' +
          '<td class="mytdcml"><a href="admin-products-item.php?id=' + id_product + '">' + ui.item.descr + '</a></td>' + 
        '</tr>';
        
        $('#def_products_ids_table').append(html);
        $('#def_products_ids_table .product_tr_delete[data-id=' + id_product + ']').click(product_tr_delete_click);
        product_tr_aa();
        
      }
      
      setTimeout(function() {
        $('#def_products_ids_search').val('');
      }, 300);
      
      
      
    },
    change: function (event, ui) {
                 
    },
    create: function () {
      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
        return $('<li>')
          .append('<a class="gks_autocomplete_id">' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + item.descr + '</span>')
          .appendTo(ul);
      };
    },
    
  });  

  $('#pos_print_enable').change(function() {
    if ($('#pos_print_enable').is(':checked')) {
      $('#div_pos_print_enable').show();
    } else {
      $('#div_pos_print_enable').hide();
    }
  });

  $('input[name=radio_payment_way]').change(function() {
    val=parseInt($(this).attr('value'));if (isNaN(val)) val=0;
    if (val<=0) return;
    ccc_elem=$('input[name=radio_payment_way_check][value=' + val + ']');
    //console.log(val,ccc_elem);
    if (ccc_elem.prop('checked')==false) {
      ccc_elem.prop('checked',true);
      $('.div_payment_one_terminal[data-one_pway=' + val + ']').show(); 
    }
  });
  
  
  $('input[name=radio_payment_way_check]').change(function() {
    val=parseInt($(this).attr('value'));if (isNaN(val)) val=0;
    if (val<=0) return;
    if ($(this).is(':checked')) {
      $('.div_payment_one_terminal[data-one_pway=' + val + ']').show(); 
    } else {
      $('.div_payment_one_terminal[data-one_pway=' + val + ']').hide();
    }
      
  });
  


  $('#app_mobile_userlogin_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml:1,
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
      $("#app_mobile_userlogin_id").attr('data-user_id',ui.item.id);
      $('#autocomplete_app_mobile_userlogin_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim()).show();
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#app_mobile_userlogin_id").val('').attr('data-user_id','0');
        $('#autocomplete_app_mobile_userlogin_id').hide(); 
      }
    }
  });  




  ///////////////////////////////////////////////////////// pre end
  
  
  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
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
